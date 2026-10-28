<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CommonModel;
use App\Models\TournamentModel;
use App\Libraries\RestLibrary;
class Book extends Controller
{
	protected $rest;
	protected $common;
	protected $tournament;
	public function __construct()
	{
		helper(['form','url','custom']);
		session()->set('redirect_url', current_url(true));
		$this->rest = new RestLibrary();
		$this->common = new CommonModel();
		$this->tournament = new TournamentModel();
	}

	public function index($list_id,$event_id)
	{
		
		$data['header'] = TRUE;
        $data['footer'] = TRUE;
        $contact_us= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
		if(!empty($contact_us)){
			$data['contact_us'] = json_decode($contact_us->settings_value);
		}
        $data['famous_region'] = $this->common->select('cities','*',array('city_active'=>1),true);
		session()->remove('paymtamout');
		session()->remove('paymtid');
		session()->remove('payorderid');

		$data['body_class'] = 'skin-default card-no-border';
		$data['countries'] = $this->common->select('countries','*',array('country_active'=>1),true);
		$data['states'] = $this->common->select('state','*',array('state_active'=>1),true);
		$data['cities'] = $this->common->select('cities','*',array('city_active'=>1),true);
		$data['commonModel'] = $this->common;
    	$data['tourneyModel'] = $this->tournament;

    	$free_booking= $this->common->select('settings','settings_value',array('settings_variable'=>'free_booking'));
		if(!empty($free_booking)){
			$data['free_booking'] = $free_booking->settings_value;
		}
		echo view('book/booking',$data);
	}

	public function cal_amount()
	{
		$response = [];
		$counts = $_POST['counts'];
		$tourny_id = $_POST['tourny_id'];
		$event_id = $_POST['event_id'];
		try {
            $res = $this->common->select('listing_events','*',array('listing_event_id'=>$event_id));
			$amount = $res->listing_event_fee;
			$total = $amount*$counts;
			session()->set('paytotal',$total);
			$response = [
					'success' => true,
				    'amount' => $total
				];
		}catch (\Exception $e) {
		    $response = [
				'error' => true,
			    'message' => 'Something went Wrong. Try again'
			];
		}
		echo json_encode($response);die();
	}

	public function booking_proceed()
	{
		 
		$data = [];
		$user_id = session()->get('user_id')??1;

		$payment_order_id = "play_".rand(99,999).time();
		$totalamount = session()->get('paytotal');
		$date = date('Y-m-d H:i:s');

		$book_status = 'pending';
		$book_active = 0;
		$free_booking= $this->common->select('settings','settings_value',array('settings_variable'=>'free_booking'));
		if($free_booking->settings_value == 1){
			$book_status = 'success';
			$book_active = 1;
		}
		
		$teamName = trim($_POST['team_name']);
		$tournament_id = trim($_POST['list_id']);
		$event_id = trim($_POST['event_id']);

		$team_exist = $this->common->select('teams', 'team_id', array(
			'team_name' => $teamName,
			'team_tournament' => $tournament_id,
			'team_event' => $event_id
		), false);

		if(!$team_exist){
			$team_data = [
				'team_name' => $teamName,
				'team_tournament' => $tournament_id,
				'team_event' => $event_id,
				'team_created_at'=> $date
			];
			$team_id = $this->common->insert_data('teams', $team_data);
		} else {
			$team_id = $team_exist->team_id;
		}
		
		$book_details = [
			'book_user_id' => $user_id,
			'book_tournament_id' => $tournament_id,
			'book_event_id' => $event_id,
			'book_status' => $book_status,
			'book_active' => $book_active,
			'book_created_at' => $date,
			'book_updated_at' => $date
		];

		$book_id = $this->common->insert_data('booking',$book_details);
		if(!empty($book_id)){
		

			session()->set('paymtamout',$totalamount);
			session()->set('paymtid',$book_id);
			session()->set('payorderid',$payment_order_id);

			$count = count($_POST['name']);
			 $i=0;
			for ($i=0; $i < $count; $i++) { 
			 	$tnba = !empty($_POST['tnba'][$i])?xss_clean($_POST['tnba'][$i]):0;
			 	$bai = !empty($_POST['bai'][$i])?xss_clean($_POST['bai'][$i]):0;
			 	$other = !empty($_POST['other'][$i])?xss_clean($_POST['other'][$i]):0;
			 	$raa = [
			 		'participant_name' => xss_clean($_POST['name'][$i]),
			 		'participant_email' => xss_clean($_POST['email'][$i]),
			 		'participant_mobile' => xss_clean($_POST['mobile'][$i]),
			 		'participant_club' => !empty($_POST['club'][$i])?xss_clean($_POST['club'][$i]):NULL,
			 		'participant_dob' => !empty($_POST['dob'][$i])?xss_clean($_POST['dob'][$i]):NULL,
			 		'participant_list_id' => $tournament_id,
			 		'participant_event_id' => $event_id,
			 		       'participant_state' => !empty($_POST['state']) ? xss_clean($_POST['state']) : $_POST($input['state']),
                        'participant_city' => !empty($_POST['city']) ? xss_clean($_POST['city']) : xss_clean($_POST['city']),
					'participant_team_id' => $team_id,
			 		//'participant_payment_id' => $payment_id,
			 		'participant_book_id' => $book_id,
			 		'participant_tnba_id' => $tnba,
			 		'participant_bai_id' => $bai,
			 		'participant_other_id' => $other
			 	];
			 	$this->common->insert_data('participants', $raa);
			}
			$response = [
				'success' => true,
			    'redirectURL' => base_url('razorpay')
			];
		}else{
			$response = [
				'error' => true,
			    'message' => 'Something went Wrong. Try again'
			];
		}
			
$data['title'] = 'Booking Success';
		$data['header'] = TRUE;
        $data['footer'] = TRUE;

		echo view('book/booking_success', $data);	}

	public function razorpay_callback()
	{
		$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? null;
		$razorpay_order_id = $_POST['razorpay_order_id'] ?? null;

		if ($razorpay_payment_id) {
			// Update Booking and Payment Status to Success
			$this->common->update_data('booking', ['book_status' => 'success', 'book_active' => 1], ['book_id' => session()->get('paymtid')]);
			$this->common->update_data('payment_details', ['payment_status' => 'success'], ['payment_order_id' => $razorpay_order_id]);

			return redirect()->to(base_url('booking-success'));
		} else {
			// Payment Failed, Redirect to Booking Page
			$this->common->update_data('booking', ['book_status' => 'failed'], ['book_id' => session()->get('paymtid')]);
			return redirect()->to(base_url('booking-failed'))->with('error', 'Payment failed. Please try again.');
		}
	}

	public function booking_success() {
		if(!session()->has('logged_in')){
			return redirect()->to(base_url());	
		}
		
		$data['title'] = 'Booking Success';
		$data['header'] = TRUE;
        $data['footer'] = TRUE;

		echo view('book/booking_success', $data);
	}  
}