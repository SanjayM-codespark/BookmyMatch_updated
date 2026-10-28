<?php
namespace App\API\r1;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ReqresTrait;
use App\Models\CommonModel;
use App\Models\TournamentModel;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
 
class Common extends ResourceController
{
    use ReqresTrait;
    protected $common;
	protected $tournament;
	
	protected $db;
    protected $razorpay;
 
    public function __construct()
    {
        helper(['form', 'url', 'custom', 'number']);
        $this->common     = new CommonModel();
        $this->tournament = new TournamentModel();
 
        $this->db       = \Config\Database::connect();
        $this->razorpay = new Api(getenv('RAZORPAY_KEY_ID'), getenv('RAZORPAY_KEY_SECRET'));
    }
 
    private function _hasActiveMembership(int $user_id): bool
    {
        return \Config\Database::connect()
            ->table('memberships')
            ->where('membership_user_id', $user_id)
            ->where('membership_status', 'active')
            ->countAllResults() > 0;
    }
    
    
    
    public function create_booking_order()
    {
        $input   = $this->getRequestInput($this->request);
        $user_id = $input['user_id'] ?? null;
        $book_id = $input['book_id'] ?? null;
 
        if (empty($user_id) || empty($book_id)) {
            return $this->getResponse(['status' => false, 'message' => 'user_id and book_id are required']);
        }
 
        $booking = $this->common->select(
            'booking',
            '*',
            array('book_id' => $book_id, 'book_user_id' => $user_id)
        );
 
        if (empty($booking)) {
            return $this->getResponse(['status' => false, 'message' => 'Booking not found']);
        }
 
        if ((int) $booking->book_active === 1) {
            return $this->getResponse(['status' => false, 'message' => 'This booking is already confirmed']);
        }
 
        $payment = $this->common->select(
            'payment_details',
            '*',
            array('payment_book_id' => $book_id, 'payment_user_id' => $user_id)
        );
 
        if (empty($payment)) {
            return $this->getResponse(['status' => false, 'message' => 'Payment record not found for this booking']);
        }
 
        $amount_in_paise = (int) round(((float) $payment->payment_amount) * 100);
 
        
        if ($amount_in_paise <= 0) {
            $this->common->update_data(
                'booking',
                array('book_status' => 'success', 'book_active' => 1, 'book_updated_at' => date('Y-m-d H:i:s')),
                array('book_id' => $book_id)
            );
 
            return $this->getResponse([
                'status'   => true,
                'message'  => 'Free booking activated',
                'is_free'  => true,
                'book_id'  => $book_id,
            ]);
        }
 
        try {
            $razorpay_order = $this->razorpay->order->create([
                'receipt'         => 'book_' . $book_id . '_' . time(),
                'amount'          => $amount_in_paise,
                'currency'        => 'INR',
                'payment_capture' => 1,
            ]);
        } catch (\Exception $e) {
            return $this->getResponse(['status' => false, 'message' => 'Unable to create payment order: ' . $e->getMessage()]);
        }
 
        $this->common->insert_data('payment_orders', array(
            'user_id'           => $user_id,
            'razorpay_order_id' => $razorpay_order['id'],
            'amount'            => $amount_in_paise,
            'currency'          => 'INR',
            'status'            => 'created',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ));
 
        return $this->getResponse([
            'status'  => true,
            'message' => 'Order created',
            'is_free' => false,
            'data'    => [
                'razorpay_order_id' => $razorpay_order['id'],
                'razorpay_key'      => getenv('RAZORPAY_KEY_ID'),
                'amount'            => $amount_in_paise,
                'currency'          => 'INR',
                'book_id'           => $book_id,
            ],
        ]);
    }
 
    
    public function verify_booking_order()
    {
        $input      = $this->getRequestInput($this->request);
        $user_id    = $input['user_id']    ?? null;
        $book_id    = $input['book_id']    ?? null;
        $order_id   = $input['razorpay_order_id']   ?? null;
        $payment_id = $input['razorpay_payment_id'] ?? null;
        $signature  = $input['razorpay_signature']  ?? null;
 
        if (empty($user_id) || empty($book_id) || empty($order_id) || empty($payment_id) || empty($signature)) {
            return $this->getResponse(['status' => false, 'message' => 'Missing required payment fields']);
        }
 
        $order_row = $this->common->select(
            'payment_orders',
            '*',
            array('razorpay_order_id' => $order_id, 'user_id' => $user_id)
        );
 
        if (empty($order_row)) {
            return $this->getResponse(['status' => false, 'message' => 'Order not found']);
        }
 
        try {
            $this->razorpay->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $order_id,
                'razorpay_payment_id' => $payment_id,
                'razorpay_signature'  => $signature,
            ]);
        } catch (SignatureVerificationError $e) {
            $this->common->update_data(
                'payment_orders',
                array('status' => 'failed', 'updated_at' => date('Y-m-d H:i:s')),
                array('razorpay_order_id' => $order_id)
            );
 
            return $this->getResponse(['status' => false, 'message' => 'Payment verification failed']);
        }
 
        $date = date('Y-m-d H:i:s');
 
        $this->common->update_data(
            'payment_orders',
            array('status' => 'paid', 'updated_at' => $date),
            array('razorpay_order_id' => $order_id)
        );
 
        $this->common->update_data(
            'payment_details',
            array(
                'payment_txn_id' => $payment_id,
                'payment_active' => 1,
                'payment_status' => 'success',
            ),
            array('payment_book_id' => $book_id, 'payment_user_id' => $user_id)
        );
 
        $this->common->update_data(
            'booking',
            array(
                'book_status'     => 'success',
                'book_active'     => 1,
                'book_updated_at' => $date,
            ),
            array('book_id' => $book_id)
        );
 
        $this->common->insert_data('transactions', array(
            'transaction_user_id'    => $user_id,
            'transaction_book_id'    => $book_id,
            'transaction_type'       => 'razorpay',
            'transaction_txn_id'     => $payment_id,
            'transaction_amount'     => $order_row->amount / 100,
            'transaction_status'     => 'success',
            'transaction_message'    => 'Razorpay payment verified',
            'transaction_created_at' => $date,
        ));
 
        return $this->getResponse([
            'status'  => true,
            'message' => 'Payment verified and booking activated',
            'book_id' => $book_id,
        ]);
    }
   
     /**
     * get location
     */
    
    public function get_countries($id=null)
    {
        $response = [];
        $condition = array('country_active' => 1);
        
        $result = $this->common->select('countries','*',$condition,true);
        
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }      
 
        return $this->getResponse($response);
 
    }
 
    public function get_states()
    {
        $response = [];
        $condition = array('state_active' => 1);
        if(!empty($_GET['country_id'])){
            $condition['state_country_id'] = $_GET['country_id'];
        }
        $result = $this->common->select('state','*',$condition,true);
        
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }      
 
        return $this->getResponse($response);
 
    }
    
    public function get_cities()
    {
        $response = [];
        $condition = array('city_active' => 1);
        if(!empty($_GET['country_id'])){
            $id = $_GET['country_id'];
            $condition = array('city_country_id'=>$id,'city_active' => 1);
        }
        if(!empty($_GET['state_id'])){
            $condition['city_state_id'] = $_GET['state_id'];
        }
        $result = $this->common->select('cities','*',$condition,true);
        
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
                
 
        return $this->getResponse($response);
 
    }
 
    
    public function get_city_by_id($id=null)
    {
        $response = [];
        $condition = array('city_active' => 1);
        $condition = array('city_id'=>$id,'city_active' => 1);
        $result = $this->common->select('cities','*',$condition);
       
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
                
 
        return $this->getResponse($response);
 
    }
 
     /**
     * get sports
     */
    public function get_sports($id=null)
    {
        $response = [];
        $condition = array('sports_active' => 1);
        if(!empty($id)){
            $condition = array('sports_id'=>$id,'sports_active' => 1);
            $result = $this->common->select('sports','*',$condition);
        }else{
            $result = $this->common->select('sports','*',$condition,true);
        }
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
                
 
        return $this->getResponse($response);
 
    }
    
    
    /**
     * get tournaments
     */
    public function get_tournaments()
    {
        $response = [];
        $rules = [
            'limit' => ['label' => 'Page Limit', 'rules' =>'required|numeric'],
            'page' => ['label' => 'Page No', 'rules' =>'required|numeric'],
        ];
        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        }else{
            $input=$this->getRequestInput($this->request);
 
            $per_page = xss_clean($input['limit']);
            $page = xss_clean($input['page']);
 
            $city_id = !empty($input['city_id'])?$input['city_id']:NULL;
            $country = !empty($input['country_id'])?$input['country_id']:NULL;
            $sports_id = !empty($input['sports_id'])?$input['sports_id']:NULL;
            $completed = !empty($input['status'])?$input['status']:NULL;
            $query = !empty($input['query'])?$input['query']:NULL;
 
 
            $item_count = $this->tournament->count_all_listings($city_id, $country, $sports_id, $completed, $query);
           
            $offset = ($page*$per_page)-$per_page;
            if($page == 1){
                $offset = 0;
            }
 
            $result = $this->tournament->get_tournament_home($per_page, $offset, $city_id, $country, $sports_id, $completed, $query);
 
            if(!empty($result)){
                foreach($result as $key => $b){  
                   unset($b->user_password);
                }
                $response = [
                        'status' => true,
                        'message' => 'Success',
                        'data' => $result
                    ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }      
        return $this->getResponse($response);
 
    }
    
    /**
     * get organizer list
     */
    public function get_organizers($id=null)
    {
        $response = [];
        $condition = array('user_role_id'=>3,'user_active' => 1);
        if(!empty($id)){
            $condition = array('user_id'=>$id,'user_role_id'=>3,'user_active' => 1);
            $result = $this->common->select('users','*',$condition);
        }else{
            $result = $this->common->select('users','*',$condition,true,'user_id DESC');
        }
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
                
 
        return $this->getResponse($response);
 
    }
    
    public function tournament_details($list_id)
    {
        
        $response = [];
        $result = $this->tournament->get_tournament(false,$list_id);
        if(!empty($result)){
            unset($result->user_password);
            $result->event_details = [];
            $event_details = $this->tournament->get_events($result->listing_id);
            if(!empty($event_details)){
                // The tournament's Last Booking Date is authoritative for its events
                if (!empty($result->listing_lastbook_date)) {
                    foreach ($event_details as $ev) {
                        $ev->listing_event_lastbook_date = $result->listing_lastbook_date;
                    }
                }
                $result->event_details = $event_details;
            } 
            // $result->similar_tournaments = $this->tournament->view_all_tournaments(10, 1, $result->listing_user_id);
            
            $similar_tournaments = $this->tournament->view_all_tournaments(10, 1, $result->listing_user_id); 
            $result->similar_tournaments = !empty($similar_tournaments) ? $similar_tournaments : [];
            
            $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
        return $this->getResponse($response);
    }
    
    
    
    public function check_team_availability()
    {
        
    
        $user_id = session()->get('user_id');
        $team_name = trim($this->request->getPost('team_name'));
        $event_id = (int) $this->request->getPost('event_id');
        $listing_id = (int) $this->request->getPost('listing_id');
        $team_id = (int) $this->request->getPost('team_id') ?? 0;
    
        if (empty($team_name)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Team name is required'
            ]);
        }
    
        if (empty($event_id) || empty($listing_id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Event ID and Listing ID are required'
            ]);
        }
    
        $db = \Config\Database::connect();
    
        $tournament = $db->table('listings')
            ->where('listing_id', $listing_id)
            ->where('listing_active', 1)
            ->get()
            ->getRow();
    
        if (!$tournament) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tournament not found or you do not have permission'
            ]);
        }
    
        $event = $db->table('listing_events le')
            ->select('le.*, l.listing_sports_id, l.listing_lastbook_date, s.sports_max_players, s.sports_name')
            ->join('listings l', 'l.listing_id = le.listing_event_listing_id')
            ->join('sports s', 's.sports_id = l.listing_sports_id', 'left')
            ->where('le.listing_event_id', $event_id)
            ->where('le.listing_event_listing_id', $listing_id)
            ->where('le.listing_event_active', 1)
            ->get()
            ->getRow();
    
        if (!$event) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Event not found'
            ]);
        }
    
        // if ($event->listing_event_type !== 'team') {
        //     return $this->response->setJSON([
        //         'status' => false,
        //         'message' => 'This event is not a team event. Only individual events are allowed.',
        //         'event_type' => $event->listing_event_type
        //     ]);
        // }
    
        // $team_query = $db->table('teams')
        //     ->where('team_name', $team_name)
        //     ->where('team_event', $event_id)
        //     ->where('team_tournament', $listing_id);
    
        // if ($team_id > 0) {
        //     $team_query->where('team_id !=', $team_id);
        // }
    
        // $existing_team = $team_query->get()->getRow();
    
        // if ($existing_team) {
        //     return $this->response->setJSON([
        //         'status' => false,
        //         'message' => 'Team name "' . $team_name . '" already exists in this event. Please choose a different name.',
        //         'existing_team_id' => $existing_team->team_id
        //     ]);
        // }
    
        $max_teams = (int) $event->listing_event_max_teams;
        if ($max_teams > 0) {
            $current_teams = $db->table('teams')
                ->where('team_event', $event_id)
                ->where('team_tournament', $listing_id)
                ->countAllResults();
    
            if ($team_id > 0) {
                $current_teams = $db->table('teams')
                    ->where('team_event', $event_id)
                    ->where('team_tournament', $listing_id)
                    ->where('team_id !=', $team_id)
                    ->countAllResults();
            }
    
            if ($current_teams >= $max_teams) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Maximum teams limit reached. Only ' . $max_teams . ' teams allowed for this event.',
                    'max_teams' => $max_teams,
                    'current_teams' => $current_teams
                ]);
            }
        }
    
        $max_players = (int) $event->listing_event_max_players;
        if ($max_players > 0) {
            // Get current team members count
            $current_members = $db->table('team_members')
                ->where('tm_team_id', $team_id)
                ->where('tm_status', 'accepted')
                ->countAllResults();
    
            $sport_max_players = (int) ($event->sports_max_players ?? 0);
            
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Team name available',
                'data' => [
                    'team_name' => $team_name,
                    'event_id' => $event_id,
                    'listing_id' => $listing_id,
                    'event_type' => $event->listing_event_type,
                    'max_teams_allowed' => $max_teams,
                    'max_players_per_team' => $max_players,
                    'sport_max_players' => $sport_max_players,
                    'current_team_members' => $current_members,
                    'available' => true
                ]
            ]);
        }
    
        if (strlen($team_name) < 2) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Team name must be at least 2 characters long'
            ]);
        }
    
        if (strlen($team_name) > 100) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Team name cannot exceed 100 characters'
            ]);
        }
    
        $current_date = date('Y-m-d H:i:s');
        if ($event->listing_event_start <= $current_date) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Cannot create teams as the event has already started.',
                'event_start_date' => $event->listing_event_start
            ]);
        }

        // Tournament's last booking date overrides the event's own value
        $lastbook = !empty($event->listing_lastbook_date) ? $event->listing_lastbook_date : $event->listing_event_lastbook_date;
        if (!empty($lastbook) && $lastbook < $current_date) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Registration for this event is closed. Last booking date was ' . date('d M Y', strtotime($lastbook)),
                'last_booking_date' => $lastbook
            ]);
        }
    
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Team name is available and meets all requirements',
            'data' => [
                'team_name' => $team_name,
                'event_id' => $event_id,
                'listing_id' => $listing_id,
                'event_type' => $event->listing_event_type,
                'max_teams_allowed' => $max_teams,
                'max_players_per_team' => (int) $event->listing_event_max_players,
                'event_start_date' => $event->listing_event_start,
                'last_booking_date' => $lastbook,
                'available' => true
            ]
        ]);
    }
    
    
    public function booking_proceed()
    {
        $response = [];
    
        $rules = [
            'user_id' => ['label' => 'user_id', 'rules' => 'required|numeric'],
            'listing_id' => ['label' => 'listing_id', 'rules' => 'required|numeric'],
            'event_id' => ['label' => 'event_id', 'rules' => 'required|numeric'],
            'name.*' => ['label' => 'name', 'rules' => 'required|min_length[2]'],
            'email.*' => ['label' => 'email', 'rules' => 'required|valid_email'],
            'mobile.*' => ['label' => 'mobile', 'rules' => 'required|numeric'],
            'payment_amount' => ['label' => 'payment_amount', 'rules' => 'required|numeric']
        ];
    
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
    
        } else {
            $input = $this->getRequestInput($this->request);
    
            $user_id = xss_clean($input['user_id']);
    
            if (!$this->_hasActiveMembership((int) $user_id)) {
                return $this->getResponse([
                    'status'  => false,
                    'message' => 'An active membership is required to book an event',
                ]);
            }
    
            $payment_book_id = "play_" . rand(99, 999) . time();
            $totalamount      = (float) xss_clean($input['payment_amount']);
            $tournament_id    = $input['listing_id'];
            $event_id         = $input['event_id'];
            $date             = date('Y-m-d H:i:s');
    
            // Verify submitted amount matches actual event fee × participant count
            $event = $this->common->select('listing_events', '*', ['listing_event_id' => $event_id]);
            if (empty($event)) {
                return $this->getResponse(['status' => false, 'message' => 'Event not found']);
            }
    
            $free_booking = $this->common->select('settings', 'settings_value', ['settings_variable' => 'free_booking']);
            $is_free = ($free_booking->settings_value == 1) || ((float) $event->listing_event_fee == 0);
    
            if (!$is_free) {
                $participant_count = count((array) ($input['name'] ?? []));
                $participant_count = max(1, $participant_count);
                $expected_amount   = (float) $event->listing_event_fee * $participant_count;
    
                if (abs($totalamount - $expected_amount) > 0.01) {
                    return $this->getResponse([
                        'status'  => false,
                        'message' => 'Payment amount does not match the event fee. Expected: ' . number_format($expected_amount, 2),
                    ]);
                }
            }
    
            // If this is a team event, make sure max team count hasn't been exceeded
            // before we start creating anything.
            $is_team_event = ($event->listing_event_type ?? null) === 'team';
    
            if ($is_team_event && !empty($event->listing_event_max_teams)) {
                $existingTeams = $this->db->table('teams')
                    ->where('team_listing_event_id', $event_id)
                    ->countAllResults();
                if ($existingTeams >= $event->listing_event_max_teams) {
                    return $this->getResponse(['status' => false, 'message' => 'Maximum number of teams reached for this event']);
                }
            }
    
            $book_status = 'pending';
            $book_active = 0;
            if ($is_free) {
                $book_status = 'success';
                $book_active = 1;
            }
    
            $book_details = [
                'book_user_id' => $user_id,
                'book_tournament_id' => xss_clean($input['listing_id']),
                'book_event_id' => xss_clean($event_id),
                'book_status' => $book_status,
                'book_active' => $book_active,
                'book_created_at' => $date,
                'book_updated_at' => $date
            ];
    
            $book_id = $this->common->insert_data('booking', $book_details);
    
            $payment_details = [
                'payment_user_id' => $user_id,
                'payment_tournament_id' => xss_clean($input['listing_id']),
                'payment_event_id' => xss_clean($input['event_id']),
                'payment_book_id' => $book_id,
                'payment_amount' => $totalamount,
                'payment_date' => $date,
                'payment_created_at' => $date
            ];
    
            $payment_id = $this->common->insert_data('payment_details', $payment_details);
    
            if (!empty($book_id)) {
                $count = count($input['name']);
                $i = 0;
                for ($i = 0; $i < $count; $i++) {
                    $teamName = !empty($_POST['club'][$i]) ? xss_clean($_POST['club'][$i]) : 0;
    
                    $team_id = null;
    
                    if ($is_team_event) {
                        $team_exist = $this->common->select('teams', '*', [
                            'team_name'       => $teamName,
                            'team_tournament' => $tournament_id,
                            'team_event'      => $event_id
                        ], false);
    
                        if ($team_exist) {
                            $team_id = $team_exist->team_id;
                        } else {
                            $team_token = bin2hex(random_bytes(16));
    
                            $team_data = [
                                'team_name'             => xss_clean($teamName),
                                'team_leader_id'        => $user_id,
                                'team_listing_event_id' => $event_id,
                                'team_tournament'       => xss_clean($tournament_id),
                                'team_event'            => xss_clean($event_id),
                                'team_join_token'       => $team_token,
                                'team_max_players'      => $event->listing_event_max_players ?? null,
                                'team_status'           => 'open',
                                'team_created_at'       => $date,
                            ];
                            $team_id = $this->common->insert_data('teams', $team_data);
                        }
    
                        $y = ['team_id' => $team_id];
                        $z = ['book_id' => $book_id];
                        $x = $this->common->update_data('booking', $y, $z);
    
                        $tm_user_id  = ($i === 0) ? $user_id : null;
                        $tm_added_by = 'leader';
                        
                        
    
                        // $member_exists = $this->db->table('team_members')
                        //     ->where('tm_team_id', $team_id)
                        //     ->where('tm_email', xss_clean($input['email'][$i]))
                        //     ->get()->getRow();
                            
                        $member_exists = $this->common->select('team_members', '*', [
                            'tm_team_id' => $team_id,
                            'tm_email'   => xss_clean($input['email'][$i])
                        ], false);
                            
    
                        if (!$member_exists) {
                            $this->common->insert_data('team_members', [
                                'tm_team_id'    => $team_id,
                                'tm_user_id'    => $tm_user_id,
                                'tm_name'       => xss_clean($input['name'][$i]),
                                'tm_mobile'     => xss_clean($input['mobile'][$i]),
                                'tm_email'      => xss_clean($input['email'][$i]),
                                'tm_status'     => 'accepted',
                                'tm_added_by'   => $tm_added_by,
                                'tm_created_at' => $date,
                            ]);
                        }
                    }
    
                    $tnba  = !empty($_POST['tnba_id'][$i]) ? xss_clean($_POST['tnba_id'][$i]) : 0;
                    $bai   = !empty($_POST['bai_id'][$i]) ? xss_clean($_POST['bai_id'][$i]) : 0;
                    $other = !empty($_POST['other_id'][$i]) ? xss_clean($_POST['other_id'][$i]) : 0;
    
                    $raa = [
                        'participant_name' => xss_clean($input['name'][$i]),
                        'participant_email' => xss_clean($input['email'][$i]),
                        'participant_mobile' => xss_clean($input['mobile'][$i]),
                        'participant_team_id' => $team_id,
                        'participant_club' => !empty($input['club'][$i]) ? xss_clean($input['club'][$i]) : xss_clean($input['club'][0]),
                        'participant_dob' => !empty($input['dob'][$i]) ? xss_clean($input['dob'][$i]) : NULL,
                        'participant_state' => !empty($input['state'][$i]) ? xss_clean($input['state'][$i]) : xss_clean($input['state'][0]),
                        'participant_city' => !empty($input['city'][$i]) ? xss_clean($input['city'][$i]) : xss_clean($input['city'][0]),
                        'participant_list_id' => xss_clean($input['listing_id']),
                        'participant_event_id' => xss_clean($input['event_id']),
                        // 'participant_payment_id' => $payment_id,
                        'participant_book_id' => $book_id,
                        'participant_tnba_id' => $tnba,
                        'participant_bai_id' => $bai,
                        'participant_other_id' => $other
                    ];
                    $this->common->insert_data('participants', $raa);
                }
    
                $response = [
                    'status' => true,
                    'message' => 'Event booked successfully',
                    'data' => ['book_id' => $book_id]
                ];
    
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Booking Failed'
                ];
            }
        }
    
        return $this->getResponse($response);
    }
    
    public function payment_transactions()
    {
        $response = [];
        $rules = [
            'user_id' => ['label' => 'User id', 'rules' =>'required|numeric'],
            'book_id' => ['label' => 'Booking id', 'rules' =>'required|numeric'],
            'type' => ['label' => 'Payment type', 'rules' =>'required|min_length[2]'],
            'txn_id' => ['label' => 'txn id', 'rules' =>'required|min_length[5]'],
            'amount' => ['label' => 'Amount', 'rules' =>'required|numeric'],
            'status' => ['label' => 'Status', 'rules' =>'required|min_length[5]'],
            'message' => ['label' => 'Message', 'rules' =>'required|min_length[5]'],
        ];
        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input      = $this->getRequestInput($this->request);
            $user_id    = $input['user_id'];
            $booking_id = $input['book_id'];
            $type       = $input['type'];
            $txn_id     = $input['txn_id'];
            $amount     = (float) $input['amount'];
            $status     = $input['status'];
            $message    = $input['message'];
            $date       = date('Y-m-d H:i:s');
    
            $is_free = (strtoupper($type) === 'FREE' && $amount == 0);
    
            if (! $is_free) {
                $stored = $this->common->select('payment_details', 'payment_amount', [
                    'payment_book_id'  => $booking_id,
                    'payment_user_id'  => $user_id,
                ]);
    
                if (empty($stored)) {
                    return $this->getResponse(['status' => false, 'message' => 'Booking payment record not found']);
                }
    
                if (abs($amount - (float) $stored->payment_amount) > 0.01) {
                    return $this->getResponse([
                        'status'  => false,
                        'message' => 'Transaction amount does not match the booking amount. Expected: ' . number_format((float) $stored->payment_amount, 2),
                    ]);
                }
            }
    
            $trans = array(
                'transaction_user_id' => $user_id,
                'transaction_book_id' => $booking_id,
                'transaction_type' => $type,
                'transaction_txn_id' => $txn_id,
                'transaction_amount' => $amount,
                'transaction_status' => $status,
                'transaction_message' => $message,
                'transaction_created_at' => $date
            );
    
            $result = $this->common->insert_data('transactions', $trans);
    
            if (!empty($result)) {
                if (strtolower($status) == 'success') {
                    $book_status = 'success';
                    $book_active = 1;
                    $insertdata = [
                        'book_status' => $book_status,
                        'book_active' => $book_active,
                    ];
                    $condition = [
                        'book_id' => $booking_id
                    ];
    
                    $result = $this->common->update_data('booking', $insertdata, $condition);
                }
                $response = [
                    'status' => true,
                    'message' => 'Transaction added successfully!',
                    'data' => array('booking_id' => $booking_id)
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Something went wrong.'
                ];
            }
        }
    
        return $this->getResponse($response);
    }
    
    
    public function update_payment_status() {
        $response = [];
        
        $rules = [
            'user_id' => ['label' => 'user id', 'rules' =>'required|numeric'],
            'book_id' => ['label' => 'user id', 'rules' =>'required|numeric'],
            'txn_id' => ['label' => 'txn id', 'rules' =>'required|min_length[3]'],
            'order_id' => ['label' => 'order id', 'rules' =>'required|min_length[3]'],
            'payment_method'=>['label' => 'payment method', 'rules' =>'required|min_length[2]'],
            'payment_date'=>['label' => 'payment date', 'rules' =>'required'],
            'payment_status'=> ['label' => 'payment method', 'rules' =>'required|min_length[2]'],
            'amount'=> ['label' => 'payment amount', 'rules' =>'required']
        ];
        
        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
            
        }else{
            
            $input            = $this->getRequestInput($this->request);
            $user_id          = $input['user_id'];
            $book_id          = $input['book_id'];
            $payment_order_id = $input['order_id'];
            $txn_id           = $input['txn_id'];
            $payment_method   = $input['payment_method'];
            $payment_date     = $input['payment_date'];
            $payment_status   = $input['payment_status'];
            $amount           = (float) $input['amount'];
 
            // Verify submitted amount matches the amount stored at booking time
            $stored = $this->common->select('payment_details', 'payment_amount', [
                'payment_book_id' => $book_id,
                'payment_user_id' => $user_id,
            ]);
            if (empty($stored)) {
                return $this->getResponse(['status' => false, 'message' => 'Booking payment record not found']);
            }
            if (abs($amount - (float) $stored->payment_amount) > 0.01) {
                return $this->getResponse([
                    'status'  => false,
                    'message' => 'Payment amount does not match the booking amount. Expected: ' . number_format((float) $stored->payment_amount, 2),
                ]);
            }
 
            $insertdata = [
                    'payment_txn_id' => $txn_id,
                    'payment_active' => 1,
                    'payment_status' => 'success'
                ];
                $condition = [
                    'payment_book_id' => $book_id,
                    'payment_user_id'=> $user_id
                ];
                
            $result = $this->common->update_data('payment_details',$insertdata, $condition);
            
            $response = [
                'status' => true,
                'message' => 'Booking success'
            ];
            
        }
        return $this->getResponse($response);
    }  
 
 
    
    public function my_payments($user_id)
    {
        $response = [];
        
        $result = $this->common->select('payment_details','*',array('payment_user_id'=>$user_id,'payment_active'=>1),true,'payment_id desc');
        if(!empty($result)){
            $post_details = [];
            foreach ($result as $data) {
                $x = $data;
                $listing_id = $data->payment_tournament_id;
                $listings = $this->common->select('listings','*',array('listing_id'=>$listing_id));
                if(!empty($listings)){
                    $x=(object) array_merge((array) $x, (array) $listings);
                }
                $event_id = $data->payment_event_id;
                $res = $this->common->select('listing_events','*',array('listing_event_id'=>$event_id));
                if(!empty($res)){
                    $events1 = $this->common->select('events','*',array('event_id'=>$res->listing_event_id));
                    if(!empty($events1)){ 
                        $x=(object) array_merge((array) $x, (array) $events1);
                    }
                }
                $pay_id = $data->payment_id;
                $res1 = $this->common->select('participants','*',array('participant_payment_id'=>$pay_id),true);
                if(!empty($res1)){
                    $x->participants = $res1;
                }
                $post_details[] = $x;
            }
            $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $post_details
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
                
 
        return $this->getResponse($response);
    }


    /**
     * get my events
     */
    public function my_events()
    {   

        $response = [];
        $rules = [
            'limit' => ['label' => 'Page Limit', 'rules' =>'required|numeric'],
            'page' => ['label' => 'Page No', 'rules' =>'required|numeric'],
            'user_id' => ['label' => 'User id', 'rules' =>'required|numeric'],
        ];
        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
            return $this->getResponse($response);
        }else{
            $input=$this->getRequestInput($this->request);
            $user_id = xss_clean($input['user_id']);
            $per_page = xss_clean($input['limit']);
            $page = xss_clean($input['page']);

            $status = !empty($input['status'])?xss_clean($input['status']):NULL;

            
            $item_count = $this->tournament->my_events_count($user_id, $status);
           
            $offset = ($page*$per_page)-$per_page;
            if($page == 1){
                $offset = 0;
            }

            $result = $this->tournament->get_my_events($per_page, $offset, $user_id, $status);

            if(!empty($result)){
                foreach($result as $key => $b){  
                   unset($b->user_password);
                }
                $response = [
                        'status' => true,
                        'message' => 'Success',
                        'data' => $result
                    ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }      
        return $this->getResponse($response);

    }

    /**
     * get event details
     */
    public function event_details($id)
    {   

        $response = [];
        $result = $this->tournament->event_details($id);

        if(!empty($result)){

            $response = [
                'status' => true,
                'message' => 'Success',
                'data' => $result
            ];
                
        }else{

            $response = [
                'status' => false,
                'message' => 'No data found'
            ];

        }     
        return $this->getResponse($response);

    }
    
    
    /*--------------------------System--------------------------------------*/
    public function attempt_contact_us()
    {
        $response = [];
       $rules = [
            'name'   => ['label' => 'Comment', 'rules' =>'required|min_length[2]'],
            'comment'   => ['label' => 'Comment', 'rules' =>'required|min_length[4]'],
            'mobile' => ['label' => 'Mobile', 'rules' =>'required|min_length[10]|numeric'],
            'email' => ['label' => 'Email', 'rules' =>'required|valid_email']
        ];
        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'validation error',
                'data' => $this->validator->getErrors()
            ];

        }else{

            $input=$this->getRequestInput($this->request);

            $name = !empty($input['name'])?xss_clean($input['name']):NULL;
            $email = !empty($input['email'])?xss_clean($input['email']):NULL;
            $mobile = !empty($input['mobile'])?xss_clean($input['mobile']):NULL;
            $comment = !empty($input['comment'])?xss_clean($input['comment']):NULL;

            $data = [
                'contact_us_name'=>$name,
                'contact_us_mobile' => $mobile,
                'contact_us_email' => $email,
                'contact_us_comment'=>$comment,
                'contact_us_created_at'=> date('Y-m-d H:i:s')
            ];
            $result = $this->common->insert_data('contact_us', $data);
           
            if(!empty($result)){
                $response = [
                    'status' => true,
                    'message' => 'Data submitted successfully'
                ];
                
            }else{
                $response = [
                    'status' => false,
                    'message' => 'Something went wrong. Try again.'
                ];
                
            }

        }
        return $this->getResponse($response);
    }

    /* About us details */

    public function about_us()
    {
        $response = [];
        $condition = array('about_us_active' => 1);
       
        $result = $this->common->select('about_us','*',$condition);
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
                

        return $this->getResponse($response);

    }

    /* App contact_us details */

    public function contact_us()
    {
        $response = [];
        $result= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'data' => json_decode($result->settings_value)
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
                

        return $this->getResponse($response);

    }

    public function faqs()
    {
        $response = [];
        $condition = array('faq_active' => 1);
        $result = $this->common->select('faqs','*',$condition,true);
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
        return $this->getResponse($response);

    }

    public function terms_conditions()
    {
        $response = [];
        $condition = array('terms_conditions_active' => 1);
        $result = $this->common->select('terms_conditions','*',$condition);
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
        return $this->getResponse($response);

    }

    public function privacy_policy()
    {
        $response = [];
        $condition = array('privacy_policy_active' => 1);
        $result = $this->common->select('privacy_policy','*',$condition);
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
        return $this->getResponse($response);

    }

    public function legal_policy()
    {
        $response = [];
        $condition = array('legal_policy_active' => 1);
        $result = $this->common->select('legal_policy','*',$condition,true);
        if(!empty($result)){
            $response = [
                    'status' => true,
                    'data' => $result
                ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
        return $this->getResponse($response);

    }
    
    /*---------------------Notifications ----------------------------------*/
    public function CountNotification(){
        
        $response = [];
        $user_id = $_GET['user_id'];
        $result = $this->common->select('notifications','COUNT(notify_for) as count',array('notify_for'=>$user_id,'notify_count' => 0));
        if(!empty($result)){
            $response = [
                'status' => true,
                'message' => 'success',
                'data' => $result
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }

        return $this->getResponse($response); 
    } 

    public  function notification(){
        $response = [];
        $user_id = !empty($_GET['user_id'])?$_GET['user_id']:null;
        if(!empty($user_id)){
        $count = $this->common->select('notifications','COUNT(notify_for) as count',array('notify_for'=>$user_id,'notify_count' => 0));
        $result = $this->common->select('notifications','*',array('notify_for'=>$user_id),true,'notify_time DESC');
        }else{
           $count = $this->common->select('notifications','COUNT(notify_for) as count',array('notify_count' => 0));
           $result = $this->common->select('notifications','*',array(),true,'notify_time DESC'); 
        }
        if(!empty($result)){
            
            $post_details = [];
            foreach ($result as $data) {
                
                $x = $data;
                /*$sender_details = $this->common->select('users','user_id,full_name,email,mobile,dob,user_city,pincode,user_type,user_role,created_at',array('user_id'=>$data->notify_from,'active'=>1));
                if(!empty($sender_details)){
                    unset($sender_details->password,$sender_details->updated_at,$sender_details->deleted_at);
                    $x->sender_details = $sender_details;
                }*/
                
                $postdetails = $this->common->select('messages','*',array('msg_id'=>$data->notify_msg_id,'msg_active'=>1));
                if(!empty($postdetails)){
                    
                    $x->messages = $postdetails;
                }
                
                $post_details[] = $x;
            }
            $response = [
                'status' => true,
                'message' => 'success',
                'data' => $post_details,
                'unseen_notify_count' => !empty($count->count)?$count->count:0
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }

        return $this->getResponse($response);
    } 
    public  function updateNotifications(){
        $response = [];
        $user_id = $_GET['user_id'];
        $result = $this->common->update_data('notifications',array('notify_count'=>1),array('notify_for'=>$user_id,'notify_count'=>0)); 
        if(!empty($result)){
            $response = [
                'status' => true,
                'message' => 'success'
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        } 
        return $this->getResponse($response);
    }

    public  function getSettings(){
        $response = [];
        $data = [];
        $free_booking= $this->common->select('settings','settings_value',array('settings_variable'=>'free_booking'));
        if(!empty($free_booking->settings_value)){
            $data['free_booking'] = 1;
        }else{
            $data['free_booking'] = 0;
        }
         $response = [
            'status' => true,
            'message' => 'success',
            'data'=> $data

        ];
        return $this->getResponse($response);
    }
    public function change_user(){
        $user_id = $_POST['user_id'];

        $result= $this->common->update_user('users', $user_id);
        if($result == 'true'){
            $response = [
                'status' => true,
                'message' => 'success'
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        } 
        return $this->getResponse($response);

    }
   
}