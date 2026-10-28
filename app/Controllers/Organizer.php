<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CommonModel;
use App\Models\TournamentModel;
use App\Libraries\RestLibrary;
use Razorpay\Api\Api;
use db;

class Organizer extends Controller
{
	protected $rest;
	protected $common;
	protected $tournament;
	public function __construct()
	{
		helper(['form', 'url', 'custom']);
		$this->rest = new RestLibrary();
		$this->common = new CommonModel();
		$this->tournament = new TournamentModel();
	}
	

    private function getActiveMembership($user_id)
    {
        $now = date('Y-m-d H:i:s');
 
        $membership = $this->common->select(
            'memberships',
            '*',
            "membership_user_id = $user_id
             AND membership_status = 'active'
             AND (membership_end_date IS NULL OR membership_end_date >= '$now')"
        );
 
        if (empty($membership)) {
            return null;
        }
 
        $package = $this->common->select(
            'packages',
            '*',
            array('package_id' => $membership->membership_package_id, 'package_active' => 1)
        );
 
        if (empty($package)) {
            return null;
        }
 
        return array('membership' => $membership, 'package' => $package);
    }
 
 
    public function packages()
    {
        if (session()->get('role_id') != 3) {
            return redirect()->to(base_url());
        }
 
        $user_id = session()->get('user_id');
 
        $data['page_title']      = 'Choose a Package';
        $data['page_subtitle']   = 'Purchase a package to start creating tournaments';
        $data['packages']        = $this->common->select('packages', '*', array('package_active' => 1), true);
        $data['active']          = $this->getActiveMembership($user_id);
 
        echo view('organizer/packages', $data);
    }
 
 
    
    public function razorpay_checkout($package_id = null)
    {
        if (session()->get('role_id') != 3) {
            return redirect()->to(base_url());
        }
 
        if (empty($package_id)) {
            return redirect()->to(base_url('organizer/packages'))
                ->with('error', 'No package selected.');
        }
 
        $user_id = session()->get('user_id');
 
        $package = $this->common->select(
            'packages', '*', array('package_id' => $package_id, 'package_active' => 1)
        );
 
        if (empty($package)) {
            return redirect()->to(base_url('organizer/packages'))
                ->with('error', 'Invalid or inactive package selected.');
        }
 
        // Prevent re-buying the exact same package while it's still active
        $active = $this->getActiveMembership($user_id);
        if (!empty($active) && $active['package']->package_id == $package->package_id) {
            return redirect()->to(base_url('organizer/packages'))
                ->with('error', 'This package is already your active plan.');
        }
 
        // Free packages skip Razorpay entirely and activate immediately
        if ((float) $package->package_price <= 0) {
            $this->activateMembership($user_id, $package, 'FREE-' . time());
            return redirect()->to(base_url('organizer/create_tournament'))
                ->with('success_message', 'Free package activated successfully!');
        }
 
        $key_id     = getenv('RAZORPAY_KEY_ID');
        $key_secret = getenv('RAZORPAY_KEY_SECRET');
 
        $api = new Api($key_id, $key_secret);
 
        // Amount must be in paise (smallest currency unit)
        $amount_paise = (int) round($package->package_price * 100);
 
        $razorpay_order = $api->order->create([
            'receipt'         => 'pkg_' . $package->package_id . '_user_' . $user_id . '_' . time(),
            'amount'          => $amount_paise,
            'currency'        => 'INR',
            'payment_capture' => 1, // auto-capture on success
        ]);
 
        $data['page_title']    = 'Complete Payment';
        $data['page_subtitle'] = 'Secure checkout powered by Razorpay';
        $data['package']       = $package;
        $data['razorpay_key']  = $key_id;
        $data['razorpay_order_id'] = $razorpay_order['id'];
        $data['amount_paise']  = $amount_paise;
        $data['user']          = $this->common->select('users', '*', array('user_id' => $user_id));
 
        echo view('organizer/razorpay_checkout', $data);
    }
 
    
    public function razorpay_verify()
    {
        if (session()->get('role_id') != 3) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }
 
        $user_id    = session()->get('user_id');
        $package_id = $this->request->getPost('package_id');
        $order_id   = $this->request->getPost('razorpay_order_id');
        $payment_id = $this->request->getPost('razorpay_payment_id');
        $signature  = $this->request->getPost('razorpay_signature');
 
        if (empty($order_id) || empty($payment_id) || empty($signature) || empty($package_id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Missing payment details.']);
        }
 
        $package = $this->common->select(
            'packages', '*', array('package_id' => $package_id, 'package_active' => 1)
        );
 
        if (empty($package)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid package.']);
        }
 
        $key_id     = getenv('RAZORPAY_KEY_ID');
        $key_secret = getenv('RAZORPAY_KEY_SECRET');
        $api        = new Api($key_id, $key_secret);
 
        $attributes = [
            'razorpay_order_id'   => $order_id,
            'razorpay_payment_id' => $payment_id,
            'razorpay_signature'  => $signature,
        ];
 
        try {
            
            $api->utility->verifyPaymentSignature($attributes);
        } catch (\Exception $e) {
            log_message('error', 'Razorpay signature verification failed: ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'message' => 'Payment verification failed.']);
        }
 
        $this->activateMembership($user_id, $package, $payment_id);
 
        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Payment verified successfully!',
            'redirect'  => base_url('organizer/create_tournament'),
        ]);
    }
 
    
    private function activateMembership($user_id, $package, $payment_ref)
    {
        $this->common->update_data(
            'memberships',
            array(
                'membership_status'     => 'expired',
                'membership_updated_at' => date('Y-m-d H:i:s'),
            ),
            array('membership_user_id' => $user_id, 'membership_status' => 'active')
        );
 
        $end_date = null; // lifetime => NULL
        if ($package->package_duration_type !== 'lifetime') {
            $days     = (int) $package->package_duration_days;
            $end_date = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        }
 
        $insert_data = array(
            'membership_user_id'     => $user_id,
            'membership_package_id'  => $package->package_id,
            'membership_start_date'  => date('Y-m-d H:i:s'),
            'membership_end_date'    => $end_date,
            'membership_status'      => 'active',
            'membership_payment_ref' => $payment_ref,
            'membership_price_paid'  => $package->package_price,
            'membership_created_at'  => date('Y-m-d H:i:s'),
        );
 
        return $this->common->insert_data('memberships', $insert_data);
    }
    
	
// 	private function getActiveMembership($user_id)
//     {
//         $now = date('Y-m-d H:i:s');
 
//         $membership = $this->common->select(
//             'memberships',
//             '*',
//             "membership_user_id = $user_id
//              AND membership_status = 'active'
//              AND (membership_end_date IS NULL OR membership_end_date >= '$now')"
//         );
 
//         if (empty($membership)) {
//             return null;
//         }
 
//         $package = $this->common->select(
//             'packages',
//             '*',
//             array('package_id' => $membership->membership_package_id, 'package_active' => 1)
//         );
 
//         if (empty($package)) {
//             return null;
//         }
 
//         return array('membership' => $membership, 'package' => $package);
//     }
 
    
//     public function packages()
//     {
//         if (session()->get('role_id') != 3) {
//             return redirect()->to(base_url());
//         }
 
//         $user_id = session()->get('user_id');
 
//         $data['page_title']      = 'Choose a Package';
//         $data['page_subtitle']   = 'Purchase a package to start creating tournaments';
//         $data['packages']        = $this->common->select('packages', '*', array('package_active' => 1), true);
//         $data['active']          = $this->getActiveMembership($user_id);
 
//         echo view('organizer/packages', $data);
//     }
 
    
//     public function buy_package($package_id = null)
//     {
//         if (session()->get('role_id') != 3) {
//             return redirect()->to(base_url());
//         }
 
//         if (empty($package_id)) {
//             return redirect()->to(base_url('organizer/packages'))
//                 ->with('error', 'No package selected.');
//         }
 
//         $user_id = session()->get('user_id');
 
//         $package = $this->common->select(
//             'packages', '*', array('package_id' => $package_id, 'package_active' => 1)
//         );
 
//         if (empty($package)) {
//             return redirect()->to(base_url('organizer/packages'))
//                 ->with('error', 'Invalid or inactive package selected.');
//         }
 
//         $active = $this->getActiveMembership($user_id);
//         if (!empty($active) && $active['package']->package_id == $package->package_id) {
//             return redirect()->to(base_url('organizer/packages'))
//                 ->with('error', 'This package is already your active plan.');
//         }
 
//         $this->common->update_data(
//             'memberships',
//             array(
//                 'membership_status'     => 'expired',
//                 'membership_updated_at' => date('Y-m-d H:i:s'),
//             ),
//             array('membership_user_id' => $user_id, 'membership_status' => 'active')
//         );
 
        
//         $end_date = null;
//         if ($package->package_duration_type !== 'lifetime') {
//             $days     = (int) $package->package_duration_days;
//             $end_date = date('Y-m-d H:i:s', strtotime("+{$days} days"));
//         }
 
//         $insert_data = array(
//             'membership_user_id'     => $user_id,
//             'membership_package_id'  => $package->package_id,
//             'membership_start_date'  => date('Y-m-d H:i:s'),
//             'membership_end_date'    => $end_date,
//             'membership_status'      => 'active',
//             'membership_payment_ref' => 'TEMP-MANUAL-' . time(), // <-- replace with Razorpay payment id later
//             'membership_price_paid'  => $package->package_price,
//             'membership_created_at'  => date('Y-m-d H:i:s'),
//         );
 
//         $inserted = $this->common->insert_data('memberships', $insert_data);
 
//         if (!$inserted) {
//             return redirect()->to(base_url('organizer/packages'))
//                 ->with('error', 'Something went wrong while activating your package. Please try again.');
//         }
 
//         return redirect()->to(base_url('organizer/create_tournament'))
//             ->with('success_message', 'Package activated successfully! You can now create your tournament.');
//     }






    /**
     * Organizer Dashboard with Statistics
     */
    public function dashboard()
    {
        if (session()->get('role_id') != 3) {
            return redirect()->to(base_url());
        }
    
        $user_id = session()->get('user_id');
        $data['users'] = $this->common->select('users', '*', ['user_id' => $user_id]);
        $data['page_title'] = 'Dashboard';
        $data['page_subtitle'] = 'Overview of your tournaments and activities';
    
        // Get current date for filtering
        $current_date = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        
        $db = \Config\Database::connect();
    
        // 1. Total Tournaments
        $data['total_tournaments'] = $db->table('listings')
            ->where('listing_user_id', $user_id)
            ->where('listing_active', 1)
            ->countAllResults();
    
        // 2. Active Tournaments (ongoing)
        $data['active_tournaments'] = $db->table('listings')
            ->where('listing_user_id', $user_id)
            ->where('listing_active', 1)
            ->where('listing_start_date <=', $current_date)
            ->where('listing_end_date >=', $current_date)
            ->countAllResults();
    
        // 3. Total Events
        $data['total_events'] = $db->table('listing_events le')
            ->join('listings l', 'l.listing_id = le.listing_event_listing_id')
            ->where('l.listing_user_id', $user_id)
            ->where('le.listing_event_active', 1)
            ->countAllResults();
    
        // 4. Total Participants/Bookings (Distinct Users)
        $data['total_participants'] = $db->table('booking b')
            ->join('listings l', 'l.listing_id = b.book_tournament_id')
            ->where('l.listing_user_id', $user_id)
            ->where('b.book_status', 'success')
            ->select('COUNT(DISTINCT b.book_user_id) as count')
            ->get()
            ->getRow()
            ->count ?? 0;
    
        // 5. Total Revenue
        $revenue_result = $db->table('booking b')
            ->join('listings l', 'l.listing_id = b.book_tournament_id')
            ->where('l.listing_user_id', $user_id)
            ->where('b.book_status', 'success')
            ->select('SUM(b.team_id) as total')
            ->get()
            ->getRow();
        $data['total_revenue'] = $revenue_result->total ?? 0;
    
        // 6. Today's Bookings/Registrations
        $data['today_bookings'] = $db->table('booking b')
            ->join('listings l', 'l.listing_id = b.book_tournament_id')
            ->where('l.listing_user_id', $user_id)
            ->where('b.book_status', 1)
            ->where('DATE(b.book_created_at)', $today)
            ->countAllResults();
    
        // Additional Statistics for Charts/Graphs
    
        // Recent Tournaments
        $data['recent_tournaments'] = $db->table('listings')
            ->where('listing_user_id', $user_id)
            ->where('listing_active', 1)
            ->orderBy('listing_id', 'DESC')
            ->limit(5)
            ->get()
            ->getResult();
    
        // Monthly Tournaments Count for Chart
        $data['monthly_stats'] = $this->getMonthlyTournamentStats($user_id);
    
        // Tournament Status Distribution
        $data['status_distribution'] = $this->getTournamentStatusDistribution($user_id);
    
        // Top Events by Participants
        $data['top_events'] = $this->getTopEvents($user_id);
    
        // Recent Bookings
        $data['recent_bookings'] = $db->table('booking b')
            ->select('b.*, u.user_name, u.user_full_name, l.listing_title')
            ->join('listings l', 'l.listing_id = b.book_tournament_id')
            ->join('users u', 'u.user_id = b.book_user_id')
            ->where('l.listing_user_id', $user_id)
            ->where('b.book_status', 1)
            ->orderBy('b.book_created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResult();
    
        echo view('organizer/dashboard', $data);
    }
    
    /**
     * Get Monthly Tournament Statistics for Charts
     */
    private function getMonthlyTournamentStats($user_id)
    {
        $db = \Config\Database::connect();
        $months = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[$month] = 0;
        }
    
        $query = $db->table('listings')
            ->select("DATE_FORMAT(listing_created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('listing_user_id', $user_id)
            ->where('listing_active', 1)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();
    
        foreach ($query->getResult() as $row) {
            if (isset($months[$row->month])) {
                $months[$row->month] = (int)$row->count;
            }
        }
    
        return $months;
    }
    
    /**
     * Get Tournament Status Distribution
     */
    private function getTournamentStatusDistribution($user_id)
    {
        $db = \Config\Database::connect();
        $current_date = date('Y-m-d H:i:s');
    
        $statuses = [
            'upcoming' => 0,
            'ongoing' => 0,
            'completed' => 0
        ];
    
        // Upcoming
        $statuses['upcoming'] = $db->table('listings')
            ->where('listing_user_id', $user_id)
            ->where('listing_active', 1)
            ->where('listing_start_date >', $current_date)
            ->countAllResults();
    
        // Ongoing
        $statuses['ongoing'] = $db->table('listings')
            ->where('listing_user_id', $user_id)
            ->where('listing_active', 1)
            ->where('listing_start_date <=', $current_date)
            ->where('listing_end_date >=', $current_date)
            ->countAllResults();
    
        // Completed
        $statuses['completed'] = $db->table('listings')
            ->where('listing_user_id', $user_id)
            ->where('listing_active', 1)
            ->where('listing_end_date <', $current_date)
            ->countAllResults();
    
        return $statuses;
    }
    
    /**
     * Get Top Events by Participants
     */
    private function getTopEvents($user_id)
    {
        $db = \Config\Database::connect();
        
        return $db->table('listing_events le')
            ->select('le.listing_event_id, le.listing_event_title, COUNT(b.book_id) as participant_count')
            ->join('listings l', 'l.listing_id = le.listing_event_listing_id')
            ->join('booking b', 'b.book_event_id = le.listing_event_id', 'left')
            ->where('l.listing_user_id', $user_id)
            ->where('le.listing_event_active', 1)
            ->where('b.book_status', 1)
            ->groupBy('le.listing_event_id')
            ->orderBy('participant_count', 'DESC')
            ->limit(5)
            ->get()
            ->getResult();
    }

	public function index()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}

		$user_id = session()->get('user_id');
		$data['users']         = $this->common->select('users', '*', ['user_id' => $user_id]);
		$data['page_title']    = 'Tournaments';
		$data['page_subtitle'] = 'Manage and track all your tournaments';

		$status    = $this->request->getGet('status') ?? NULL;
		$sports_id = $this->request->getGet('sports') ?? NULL;
		$search    = $this->request->getGet('query') ?? NULL;

		$perPage = 10;
		$page    = (int)($this->request->getGet('page') ?? 1);
		$offset  = ($page - 1) * $perPage;

		$total = $this->tournament->get_tournament_count($user_id, $sports_id, $status, $search);
		$data['tournaments'] = $this->tournament->get_tournament($user_id, false, $sports_id, $status, $search, $perPage, $offset);
		$data['sports']      = $this->common->select('sports', '*', ['sports_active' => 1], true);

		$pager = \Config\Services::pager();
		$data['pager'] = $pager->makeLinks($page, $perPage, $total, 'custom');

		echo view('organizer/home', $data);
	}
	public function index1()
	{

		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', ['user_id' => $user_id]);

		// Filters
		$status = $this->request->getGet('status') ?? NULL;
		$sports_id = $this->request->getGet('sports') ?? NULL;
		$search = $this->request->getGet('query') ?? NULL;

		// Pagination
		$perPage = 10;
		$page = (int)($this->request->getGet('page') ?? 1);
		$offset = ($page - 1) * $perPage;

		// Get total count for pagination
		$total = $this->tournament->get_tournament_count($user_id, $sports_id, $status, $search);

		// Get paginated data
		$data['tournaments'] = $this->tournament->get_tournament($user_id, false, $sports_id, $status, $search, $perPage, $offset);
		$data['sports'] = $this->common->select('sports', '*', ['sports_active' => 1], true);

		// Build pagination
		$pager = \Config\Services::pager();
		$data['pager'] = $pager->makeLinks($page, $perPage, $total, 'custom');

		echo view('organizer/booking_history1', $data);
	}

	public function booking_history()
	{
		$user_id = session()->get('user_id');
		$data['users']         = $this->common->select('users', '*', ['user_id' => $user_id]);
		$data['page_title']    = 'Bookings';
		$data['page_subtitle'] = 'View participant bookings and tournament registrations';

		// Filters
		$status = $this->request->getGet('status') ?? NULL;
		$sports_id = $this->request->getGet('sports') ?? NULL;
		$search = $this->request->getGet('query') ?? NULL;

		// Pagination
		$perPage = 10;
		$page = (int)($this->request->getGet('page') ?? 1);
		$offset = ($page - 1) * $perPage;

		// Get total count for pagination
		$total = $this->tournament->get_tournament_count($user_id, $sports_id, $status, $search);

		// Get paginated data
		$data['tournaments'] = $this->tournament->get_tournament($user_id, false, $sports_id, $status, $search, $perPage, $offset);
		$data['sports'] = $this->common->select('sports', '*', ['sports_active' => 1], true);

		// Build pagination
		$pager = \Config\Services::pager();
		$data['pager'] = $pager->makeLinks($page, $perPage, $total, 'custom');

		echo view('organizer/booking_history', $data);
	}


	public function sign_up()
	{
		if (session()->has('logged_in')) {
			if (session()->get('role_id') == 1) {
				return redirect()->to(base_url('admin'));
			} elseif (session()->get('role_id') == 2) {
				return redirect()->to(base_url());
			} elseif (session()->get('role_id') == 3) {
				return redirect()->to(base_url('organizer'));
			}
		}
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['cities'] = $this->common->select('cities', '*', array('city_active' => 1), true);
		echo view('user/sign_up', $data);
	}

	/**
	 * Create a new account
	 */
	public function attempt_sign_up()
	{
		$response = [];
		$rules = [
			'name'	=> ['label' => 'Name', 'rules' => 'required|min_length[2]'],
			'organization_name'	=> ['label' => 'Organization Name', 'rules' => 'required|min_length[2]'],
			'email' => [
				'label' => 'Email',
				'rules' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.user_email]',
				'errors' => ['is_unique' => 'Email already exist']
			],
			'mobile' => [
				'label' => 'Mobile',
				'rules' => 'required|min_length[10]|is_unique[users.user_mobile]',
				'errors' => ['is_unique' => 'Mobile number already exist']
			],
			'password' => ['label' => 'Password', 'rules' => 'required|strong_password'],
			'confirm_password' => ['label' => 'Confirm Password', 'rules' => 'required|matches[password]'],
			'city' => ['label' => 'City', 'rules' => 'required|numeric'],
			'country' => ['label' => 'Country', 'rules' => 'required|numeric'],
			'address' => ['label' => 'Address', 'rules' => 'required|min_length[10]']
		];
		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}


		$username = $this->request->getPost('name');
		$email = $this->request->getPost('email');
		$mobile = $this->request->getPost('mobile');
		$organization_name = $this->request->getPost('organization_name');
		$password = $this->request->getPost('password');
		$city = $this->request->getPost('city');
		$country = $this->request->getPost('country');
		$address = $this->request->getPost('address');

		$country_code = $this->common->select('countries', '*', array('country_id' => $country))->country_mobile_code;
		$pass = password_hash($password, PASSWORD_DEFAULT);
		$otp = get_unique_code('numeric', 6);

		$data = array(
			'user_name' => xss_clean($username),
			'user_email' => xss_clean($email),
			'user_mobile_code' => $country_code,
			'user_mobile' => xss_clean($mobile),
			'user_organization_name' => xss_clean($organization_name),
			'user_password' => $pass,
			'user_city_id' => xss_clean($city),
			'user_country_id' => xss_clean($country),
			'user_address' => xss_clean($address),
			'user_role_id' => 3,
			'user_reset_hash' => $otp,
			'user_created_at' => date('Y-m-d H:i:s')
		);
		//print_array($data);die();
		$result = $this->common->insert_data('users', $data);
		if ($result) {
			return redirect()->to(base_url('login'))->with('success_message', 'Account created successfully. Admin will verify and active your account soon');
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}



// 	public function create_tournament()
// 	{
// 		if (session()->get('role_id') != 3) {
// 			return redirect()->to(base_url());
// 		}
// 		$user_id = session()->get('user_id');
// 		$data['users']          = $this->common->select('users', '*', array('user_id' => $user_id));
// 		$data['page_title']     = 'Create Tournament';
// 		$data['page_subtitle']  = 'Fill in the details to create a new tournament';

// 		$data['sports']    = $this->common->select('sports', '*', array('sports_active' => 1), true);
// 		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
// 		$data['districts'] = $this->common->select('districts', '*', array('district_active' => 1), true);
// 		$data['cities']    = $this->common->select('cities', '*', array('city_active' => 1), true);
// 		$data['event_list'] = $this->common->select(
// 			'events',
// 			'*',
// 			"(event_user_id = $user_id OR event_user_id = 2)",
// 			true
// 		);
// 		$data['event_category_list'] = $this->common->select('events', '*', array('event_active' => 1, 'event_user_id' => $user_id), true);

// 		echo view('organizer/create_tournament', $data);
// 	}



    public function create_tournament()
    {
        if (session()->get('role_id') != 3) {
            return redirect()->to(base_url());
        }
        $user_id = session()->get('user_id');
        

 
        // 1) Must have an active, non-expired membership
        $active = $this->getActiveMembership($user_id);
 
        if (empty($active)) {
            return redirect()->to(base_url('organizer/packages'))
                ->with('error', 'Please purchase a package before creating a tournament.');
        }
         
 
        $package = $active['package'];
 
        // 2) Package must explicitly allow tournament creation
        if ((int) $package->package_can_create_tournament !== 1) {
            return redirect()->to(base_url('organizer/packages'))
                ->with('error', 'Your current package (' . esc($package->package_name) . ') does not include tournament creation. Please upgrade your package.');
        }
        
        
 
 
        // 3) Count tournaments already created by this organizer
        //    Your tournaments table is actually "listings" with creator column "listing_user_id"
        $existing_tournaments = $this->common->select(
            'listings', '*', array('listing_user_id' => $user_id), true
        );
        $tournament_count = is_array($existing_tournaments) ? count($existing_tournaments) : 0;
        // 4) Enforce package_max_tournaments (0 = unlimited)
        $max_tournaments = (int) $package->package_max_tournaments;
        //   var_dump("ihutytyrytuuuuiyi");
        // var_dump($max_tournaments);
        // var_dump($tournament_count);
        //  die();
        // if ($max_tournaments > 0 && $tournament_count >= $max_tournaments) {
        //     return redirect()->to(base_url('organizer/dashboard'))
        //         ->with('error', 'You have reached your package limit of '
        //             . $max_tournaments . ' tournament(s) for the "' . esc($package->package_name)
        //             . '" package. Please upgrade to create more.');
        // }
        
      
        
 
        $data['users']          = $this->common->select('users', '*', array('user_id' => $user_id));
        $data['page_title']     = 'Create Tournament';
        $data['page_subtitle']  = 'Fill in the details to create a new tournament';
 
        $data['sports']    = $this->common->select('sports', '*', array('sports_active' => 1), true);
        $data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
        $data['districts'] = $this->common->select('districts', '*', array('district_active' => 1), true);
        $data['cities']    = $this->common->select('cities', '*', array('city_active' => 1), true);
        $data['event_list'] = $this->common->select('events', '*', "(event_user_id = $user_id OR event_user_id = 2)", true);
        $data['event_category_list'] = $this->common->select('events', '*', array('event_active' => 1, 'event_user_id' => $user_id), true);
        
        
 
        // 5) Usage info for the view banner
        $data['package']               = $package;
        $data['tournament_used']       = $tournament_count;
        $data['max_tournaments']       = $max_tournaments; // 0 = unlimited
        $data['remaining_tournaments'] = $max_tournaments > 0 ? ($max_tournaments - $tournament_count) : null;
        echo view('organizer/create_tournament', $data);
    }

	public function attempt_add_event_category()
	{
		if ($this->request) {
			$rules = [
				'event_name' => [
					'label' => 'Event Category Name',
					'rules' => 'required|min_length[3]|is_unique[events.event_name]'
				]
			];

			if (!$this->validate($rules)) {
				return $this->response->setJSON([
					'status' => false,
					'errors' => $this->validator->getErrors()
				]);
			}

			$event_name = trim($this->request->getPost('event_name'));
			$user_id = session()->get('user_id');
			$edit_id = session()->get('edit_id');

			// Check if event already exists
			$listing_events = $this->common->select('events', '*', ['event_name' => $event_name]);

			if (!empty($listing_events)) {
				return $this->response->setJSON([
					'status' => false,
					'message' => 'Event already exists.'
				]);
			}
			$data = [
				'event_name' => xss_clean($event_name),
				'event_user_id' => $user_id
			];

			if ($edit_id != 0) {
				// Update existing category
				$this->common->update_data('events', $data, ['event_id' => $edit_id]);
				return $this->response->setJSON([
					'status' => true,
					'message' => 'Event updated successfully!',
					'edit_id' => $edit_id
				]);
			} else {
				$result = $this->common->insert_data('events', $data);
				if ($result) {
					return $this->response->setJSON([
						'status' => true,
						'message' => 'Event added successfully!',
						'edit_id' => $result,
					]);
				}
			}
		} else {
			return $this->response->setStatusCode(400)->setJSON([
				'status' => false,
				'message' => 'Invalid request.'
			]);
		}
	}

	public function edit_tournament($tourney_id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['page_title']    = 'Edit Tournament';
		$data['page_subtitle'] = 'Update tournament details';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['sports'] = $this->common->select('sports', '*', array('sports_active' => 1), true);
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['district'] = $this->common->select('districts', '*', array('district_active' => 1), true);
		$data['cities'] = $this->common->select('cities', '*', array('city_active' => 1), true);
		$data['listings'] = $this->common->select('listings', '*', array('listing_id' => $tourney_id));
		$data['event_list'] = $this->common->select('events', '*', array('event_user_id' => $user_id), true);

		echo view('organizer/edit_tournament', $data);
	}

// 	public function attempt_create_tournament($id = NULL)
// 	{
// 		if (session()->get('role_id') != 3) {
// 			return redirect()->to(base_url());
// 		}

// 		if (!$id) {
// 			$rules = [
// 				'title'          => ['rules' => 'required'],
// 				'sports'         => ['rules' => 'required'],
// 				'start_date'     => ['rules' => 'required'],
// 				'end_date'       => ['rules' => 'required'],
// 				'country'        => ['rules' => 'required'],
// 				'state'          => ['rules' => 'permit_empty'],
// 				'district'       => ['rules' => 'permit_empty'],
// 				'city'           => ['rules' => 'required'],
// 				'address'        => ['rules' => 'required'],
// 				'details'        => ['rules' => 'required'],
// 				'event_name.*'    => ['rules' => 'required'],
// 				'event_type.*'    => ['rules' => 'required'],
// 				'type.*'          => ['rules' => 'required'],
// 				'fee.*'           => ['rules' => 'required'],
// 				'event_start.*'   => ['rules' => 'required'],
// 				'event_end.*'     => ['rules' => 'required'],
// 				'lastbook_date.*' => ['rules' => 'required']
// 			];

// 			if (!$this->validate($rules)) {
// 				return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
// 			}
// 		}

// 		$user_id = session()->get('user_id');
		
		
// 		$active = $this->getActiveMembership($user_id);
//         if (empty($active) || (int) $active['package']->package_can_create_tournament !== 1) {
//             return redirect()->to(base_url('organizer/packages'))
//                 ->with('error', 'Your package does not allow tournament creation.');
//         }
 
//         $package = $active['package'];
//         $max_tournaments = (int) $package->package_max_tournaments;
 
//         if ($max_tournaments > 0) {
//             $existing_tournaments = $this->common->select(
//                 'listings', '*', array('listing_user_id' => $user_id), true
//             );
//             $tournament_count = is_array($existing_tournaments) ? count($existing_tournaments) : 0;
 
//             if ($tournament_count >= $max_tournaments) {
//                 return redirect()->to(base_url('organizer/create_tournament'))
//                     ->with('error', 'You have reached your package tournament limit.');
//             }
//         }

// 		$title      = $this->request->getPost('title');
// 		$sports     = $this->request->getPost('sports');
// 		$start_date = $this->request->getPost('start_date');
// 		$end_date   = $this->request->getPost('end_date');
// 		$country    = $this->request->getPost('country');
// 		$state      = $this->request->getPost('state');
// 		$district   = $this->request->getPost('district');
// 		$city       = $this->request->getPost('city');
// 		$address    = $this->request->getPost('address');
// 		$details    = $this->request->getPost('details');
// 		$rules      = !empty($_POST['rules']) ? $this->request->getPost('rules') : NULL;

// 		// FIX: Handle city data properly
// 		$city_id = null;

// 		// Check if city exists
// 		$cond = array('city_state_id' => $state, 'city_name' => $city);
// 		$city_exist = $this->common->select('cities', '*', $cond);

// 		if (empty($city_exist)) {
// 			// Insert new city
// 			$city_data = [
// 				'city_name' => xss_clean($city),
// 				'city_state_id' => xss_clean($state),
// 				'city_country_id' => xss_clean($country),
// 				'city_district_id' => xss_clean($district)
// 			];
// 			$city_id = $this->common->insert_data('cities', $city_data);
// 		} else {
// 			$city_id = $city_exist->city_id;
// 		}

// 		// FIX: Check if city_id exists before using
// 		if (empty($city_id)) {
// 			log_message('error', 'City ID is empty for city: ' . $city);
// 			return redirect()->back()->withInput()->with('error', 'Unable to create/find city. Please try again.');
// 		}

// 		// Prepare tournament data
// 		$data = [
// 			'listing_user_id'    => $user_id,
// 			'listing_title'      => $title,
// 			'listing_sports_id'  => $sports,
// 			'listing_start_date' => $start_date,
// 			'listing_end_date'   => $end_date,
// 			'listing_country_id' => $country,
// 			'listing_state_id'   => $state,
// 			'listing_district_id' => $district,
// 			'listing_city_id'    => $city_id,  // FIX: Use the retrieved/inserted city_id
// 			'listing_address'    => $address,
// 			'listing_details'    => $details,
// 			'listing_rules'      => $rules
// 		];

// 		// Handle banner image upload
// 		if (!empty($this->request->getFile('listing_image'))) {
// 			$folderpath = FCPATH . "public/uploads/listings/images/$user_id";
// 			if (!is_dir($folderpath)) {
// 				mkdir($folderpath, 0777, true);
// 			}
// 			$imageFile = $this->request->getFile('listing_image');
// 			if ($imageFile->isValid() && ! $imageFile->hasMoved()) {
// 				$filename = $imageFile->getRandomName();
// 				$imageFile->move($folderpath, $filename);
// 				$data['listing_image'] = $filename;
// 			}
// 		}

// 		// Handle rules file upload
// 		if (!empty($this->request->getFile('listing_rules_file'))) {
// 			$folderpath = FCPATH . "public/uploads/listings/rules/$user_id";
// 			if (!is_dir($folderpath)) {
// 				mkdir($folderpath, 0777, true);
// 			}
// 			$rulesFile = $this->request->getFile('listing_rules_file');
// 			if ($rulesFile->isValid() && ! $rulesFile->hasMoved()) {
// 				$filename = $rulesFile->getRandomName();
// 				$rulesFile->move($folderpath, $filename);
// 				$data['listing_rules_file'] = $filename;
// 			}
// 		}

// 		// Insert or update tournament
// 		if ($id) {
// 			$listing_id = $id;
// 			$data['listing_updated_at'] = date('Y-m-d H:i:s');
// 			$result = $this->common->update_data('listings', $data, array('listing_user_id' => $user_id, 'listing_id' => $id));
// 		} else {
// 			$data['listing_created_at'] = date('Y-m-d H:i:s');
// 			$result = $this->common->insert_data('listings', $data);
// 			$listing_id = $result;
// 		}

// 		if ($result) {
// 			if (!empty($id)) {
// 				$msg = 'Tournament updated successfully!';
// 			} else {
// 				// Process events
// 				$event_names     = $this->request->getPost('event_name');
// 				$types           = $this->request->getPost('type');
// 				$event_types     = $this->request->getPost('event_type');
// 				$fees            = $this->request->getPost('fee');
// 				$event_starts    = $this->request->getPost('event_start');
// 				$event_ends      = $this->request->getPost('event_end');
// 				$lastbook_dates     = $this->request->getPost('lastbook_date');
// 				$matchmaking_dates  = $this->request->getPost('matchmaking_date');
// 				$max_players_arr    = $this->request->getPost('max_players');
// 				$max_teams_arr      = $this->request->getPost('max_teams');

// 				$tnba_ids        = $this->request->getPost('tnba_id');
// 				$bai_ids         = $this->request->getPost('bai_id');
// 				$other_ids       = $this->request->getPost('other_id');
// 				$dobs            = $this->request->getPost('dob');

// 				if (!empty($event_names) && is_array($event_names)) {
// 					foreach ($event_names as $key => $value) {
// 						$tnbaid  = !empty($tnba_ids[$key]) ? $tnba_ids[$key] : 0;
// 						$baiid   = !empty($bai_ids[$key]) ? $bai_ids[$key] : 0;
// 						$otherid = !empty($other_ids[$key]) ? $other_ids[$key] : 0;
// 						$dob_val = !empty($dobs[$key]) ? $dobs[$key] : 0;
// 						$isTeam  = !empty($event_types[$key]) && $event_types[$key] === 'team';
// 						$mm_date = !empty($matchmaking_dates[$key]) ? xss_clean($matchmaking_dates[$key]) : null;

// 						$event_data = [
// 							'listing_event_listing_id'       => xss_clean($listing_id),
// 							'listing_event_title'            => xss_clean($event_names[$key]),
// 							'listing_event_type'             => xss_clean($event_types[$key]),
// 							'listing_match_type'             => xss_clean($types[$key]),
// 							'listing_event_fee'              => xss_clean($fees[$key]),
// 							'listing_event_start'            => xss_clean($event_starts[$key]),
// 							'listing_event_end'              => xss_clean($event_ends[$key]),
// 							'listing_event_lastbook_date'    => xss_clean($lastbook_dates[$key]),
// 							'listing_event_matchmaking_date' => $mm_date,
// 							'listing_event_tnba_id'          => xss_clean($tnbaid),
// 							'listing_event_bai_id'           => xss_clean($baiid),
// 							'listing_event_other_id'         => xss_clean($otherid),
// 							'listing_event_dob'              => xss_clean($dob_val),
// 							'listing_event_max_players'      => $isTeam ? (int)($max_players_arr[$key] ?? 1) : 1,
// 							'listing_event_max_teams'        => $isTeam ? (int)($max_teams_arr[$key] ?? 0) : 0,
// 						];

// 						$this->common->insert_data('listing_events', $event_data);
// 					}
// 				}

// 				$msg = 'Tournament created successfully!';
// 			}
// 			return redirect()->to(base_url('organizer'))->with('success_message', $msg);
// 		} else {
// 			return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
// 		}
// 	}


    public function attempt_create_tournament($id = NULL)
    {
        if (session()->get('role_id') != 3) {
            return redirect()->to(base_url());
        }
    
        if (!$id) {
            $rules = [
                'title'          => ['rules' => 'required'],
                'sports'         => ['rules' => 'required'],
                'start_date'     => ['rules' => 'required'],
                'end_date'       => ['rules' => 'required'],
                'lastbook_date'  => ['rules' => 'required'], // FIXED
                'country'        => ['rules' => 'required'],
                'state'          => ['rules' => 'permit_empty'],
                'district'       => ['rules' => 'permit_empty'],
                'city'           => ['rules' => 'required'],
                'address'        => ['rules' => 'required'],
                'details'        => ['rules' => 'required'],
                'event_name.*'   => ['rules' => 'required'],
                'event_type.*'   => ['rules' => 'required'],
                'type.*'         => ['rules' => 'required'],
                'fee.*'          => ['rules' => 'required'],
                'event_start.*'  => ['rules' => 'required'],
                'event_end.*'    => ['rules' => 'required']
            ];
    
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }
    
        $user_id = session()->get('user_id');
        
        $active = $this->getActiveMembership($user_id);
        if (empty($active) || (int) $active['package']->package_can_create_tournament !== 1) {
            return redirect()->to(base_url('organizer/packages'))
                ->with('error', 'Your package does not allow tournament creation.');
        }
    
        $package = $active['package'];
        $max_tournaments = (int) $package->package_max_tournaments;
    
        if ($max_tournaments > 0) {
            $existing_tournaments = $this->common->select(
                'listings', '*', array('listing_user_id' => $user_id), true
            );
            $tournament_count = is_array($existing_tournaments) ? count($existing_tournaments) : 0;
    
            if ($tournament_count >= $max_tournaments) {
                return redirect()->to(base_url('organizer/create_tournament'))
                    ->with('error', 'You have reached your package tournament limit.');
            }
        }
    
        $title      = $this->request->getPost('title');
        $sports     = $this->request->getPost('sports');
        $start_date = $this->request->getPost('start_date');
        $end_date   = $this->request->getPost('end_date');
        $lastbook_date = $this->request->getPost('lastbook_date'); // Get lastbook_date
        $country    = $this->request->getPost('country');
        $state      = $this->request->getPost('state');
        $district   = $this->request->getPost('district');
        $city       = $this->request->getPost('city');
        $address    = $this->request->getPost('address');
        $details    = $this->request->getPost('details');
        $rules      = !empty($_POST['rules']) ? $this->request->getPost('rules') : NULL;
    
        // Handle city
        $city_id = null;
        $cond = array('city_state_id' => $state, 'city_name' => $city);
        $city_exist = $this->common->select('cities', '*', $cond);
    
        if (empty($city_exist)) {
            $city_data = [
                'city_name' => xss_clean($city),
                'city_state_id' => xss_clean($state),
                'city_country_id' => xss_clean($country),
                'city_district_id' => xss_clean($district)
            ];
            $city_id = $this->common->insert_data('cities', $city_data);
        } else {
            $city_id = $city_exist->city_id;
        }
    
        if (empty($city_id)) {
            log_message('error', 'City ID is empty for city: ' . $city);
            return redirect()->back()->withInput()->with('error', 'Unable to create/find city. Please try again.');
        }
    
        // Prepare tournament data
        $data = [
            'listing_user_id'    => $user_id,
            'listing_title'      => $title,
            'listing_sports_id'  => $sports,
            'listing_start_date' => $start_date,
            'listing_end_date'   => $end_date,
            'listing_lastbook_date' => !empty($lastbook_date) ? date('Y-m-d H:i:s', strtotime($lastbook_date)) : NULL,
            'listing_country_id' => $country,
            'listing_state_id'   => $state,
            'listing_district_id' => $district,
            'listing_city_id'    => $city_id,
            'listing_address'    => $address,
            'listing_details'    => $details,
            'listing_rules'      => $rules
        ];
    
        // Handle image upload
        if (!empty($this->request->getFile('listing_image'))) {
            $folderpath = FCPATH . "public/uploads/listings/images/$user_id";
            if (!is_dir($folderpath)) {
                mkdir($folderpath, 0777, true);
            }
            $imageFile = $this->request->getFile('listing_image');
            if ($imageFile->isValid() && ! $imageFile->hasMoved()) {
                $filename = $imageFile->getRandomName();
                $imageFile->move($folderpath, $filename);
                $data['listing_image'] = $filename;
            }
        }
    
        // Handle rules file upload
        if (!empty($this->request->getFile('listing_rules_file'))) {
            $folderpath = FCPATH . "public/uploads/listings/rules/$user_id";
            if (!is_dir($folderpath)) {
                mkdir($folderpath, 0777, true);
            }
            $rulesFile = $this->request->getFile('listing_rules_file');
            if ($rulesFile->isValid() && ! $rulesFile->hasMoved()) {
                $filename = $rulesFile->getRandomName();
                $rulesFile->move($folderpath, $filename);
                $data['listing_rules_file'] = $filename;
            }
        }
    
        // Insert or update tournament
        if ($id) {
            $listing_id = $id;
            $data['listing_updated_at'] = date('Y-m-d H:i:s');
            $result = $this->common->update_data('listings', $data, array('listing_user_id' => $user_id, 'listing_id' => $id));
        } else {
            $data['listing_created_at'] = date('Y-m-d H:i:s');
            $result = $this->common->insert_data('listings', $data);
            $listing_id = $result;
        }
    
        if ($result) {
            if (!empty($id)) {
                $msg = 'Tournament updated successfully!';
            } else {
                // Process events
                $event_names     = $this->request->getPost('event_name');
                $types           = $this->request->getPost('type');
                $event_types     = $this->request->getPost('event_type');
                $fees            = $this->request->getPost('fee');
                $event_starts    = $this->request->getPost('event_start');
                $event_ends      = $this->request->getPost('event_end');
                $matchmaking_dates  = $this->request->getPost('matchmaking_date');
                $max_players_arr    = $this->request->getPost('max_players');
                $max_teams_arr      = $this->request->getPost('max_teams');
    
                $tnba_ids        = $this->request->getPost('tnba_id');
                $bai_ids         = $this->request->getPost('bai_id');
                $other_ids       = $this->request->getPost('other_id');
                $dobs            = $this->request->getPost('dob');
    
                if (!empty($event_names) && is_array($event_names)) {
                    foreach ($event_names as $key => $value) {
                        $tnbaid  = !empty($tnba_ids[$key]) ? $tnba_ids[$key] : 0;
                        $baiid   = !empty($bai_ids[$key]) ? $bai_ids[$key] : 0;
                        $otherid = !empty($other_ids[$key]) ? $other_ids[$key] : 0;
                        $dob_val = !empty($dobs[$key]) ? $dobs[$key] : 0;
                        $isTeam  = !empty($event_types[$key]) && $event_types[$key] === 'team';
                        $mm_date = !empty($matchmaking_dates[$key]) ? xss_clean($matchmaking_dates[$key]) : null;
    
                        $event_data = [
                            'listing_event_listing_id'       => xss_clean($listing_id),
                            'listing_event_title'            => xss_clean($event_names[$key]),
                            'listing_event_type'             => xss_clean($event_types[$key]),
                            'listing_match_type'             => xss_clean($types[$key]),
                            'listing_event_fee'              => xss_clean($fees[$key]),
                            'listing_event_start'            => xss_clean($event_starts[$key]),
                            'listing_event_end'              => xss_clean($event_ends[$key]),
                            // 'listing_event_lastbook_date' => xss_clean($lastbook_dates[$key]), // Remove this
                            'listing_event_matchmaking_date' => $mm_date,
                            'listing_event_tnba_id'          => xss_clean($tnbaid),
                            'listing_event_bai_id'           => xss_clean($baiid),
                            'listing_event_other_id'         => xss_clean($otherid),
                            'listing_event_dob'              => xss_clean($dob_val),
                            'listing_event_max_players'      => $isTeam ? (int)($max_players_arr[$key] ?? 1) : 1,
                            'listing_event_max_teams'        => $isTeam ? (int)($max_teams_arr[$key] ?? 0) : 0,
                        ];
    
                        $this->common->insert_data('listing_events', $event_data);
                    }
                }
    
                $msg = 'Tournament created successfully!';
            }

            // Keep every event's last booking date in sync with the tournament's
            // (the app/API gate registration on listing_event_lastbook_date)
            $this->common->update_data(
                'listing_events',
                array(
                    'listing_event_lastbook_date' => !empty($lastbook_date) ? date('Y-m-d H:i:s', strtotime($lastbook_date)) : NULL
                ),
                array('listing_event_listing_id' => $listing_id)
            );

            return redirect()->to(base_url('organizer'))->with('success_message', $msg);
        } else {
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }
	
	public function events($id = null)
    {
        $user_id = session()->get('user_id');
        $data['event'] = null;
        $data['page_title']    = 'Events';
        $data['page_subtitle'] = 'Add or edit tournament events';
        $data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
    
        if (!empty($id)) {
            $event = $this->common->select('events', '*', ['event_id' => $id, 'event_user_id' => $user_id]);
            if (!$event) {
                return redirect()->to(base_url('view_events'))->with('error', 'Event not found or unauthorized');
            }
            $data['event'] = $event[0] ?? $event; // adjust depending on what ->select() returns
        }
    
        return view('organizer/events', $data); // adjust view path to match your folder structure
    }
	
	public function attempt_add_events($id = null)
    {
        $user_id = session()->get('user_id');
    
        $mfr_name = trim($this->request->getPost('name'));
        $min_age  = $this->request->getPost('event_min_age');
        $max_age  = $this->request->getPost('event_max_age');
    
        $rules = [
            'event_min_age' => ['label' => 'Min Age', 'rules' => 'permit_empty|numeric|greater_than_equal_to[0]'],
            'event_max_age' => ['label' => 'Max Age', 'rules' => 'permit_empty|numeric|greater_than_equal_to[0]'],
        ];
    
        if (empty($id)) {
            $rules['name'] = ['rules' => 'required|min_length[3]|is_unique[events.event_name]'];
        }
    
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    
        if (!empty($min_age) && !empty($max_age) && (int) $min_age > (int) $max_age) {
            return redirect()->back()->withInput()->with('errors', ['event_min_age' => 'Min age cannot be greater than max age']);
        }
    
        // Ownership check on edit — make sure this event belongs to this organizer
        if (!empty($id)) {
            $existing = $this->common->select('events', '*', ['event_id' => $id, 'event_user_id' => $user_id]);
            if (!$existing) {
                return redirect()->to(base_url('view_events'))->with('error', 'You are not authorized to edit this event');
            }
        }
    
        if (empty($id)) {
            $name = $this->common->select('events', '*', ['event_name' => $mfr_name]);
            if ($name) {
                return redirect()->back()->withInput()->with('errors', 'event name already exist');
            }
        }
    
        $data = [
            'event_name'    => xss_clean($mfr_name),
            'event_user_id' => $user_id,
            'event_min_age' => !empty($min_age) ? (int) xss_clean($min_age) : null,
            'event_max_age' => !empty($max_age) ? (int) xss_clean($max_age) : null,
        ];
    
        if (!empty($this->request->getFile('event_image'))) {
            $folderpath = FCPATH . '/public/uploads/images/events';
            $imageFile = $this->request->getFile('event_image');
            if ($imageFile->isValid() && !$imageFile->hasMoved()) {
                $filename = $imageFile->getRandomName();
                $imageFile->move($folderpath, $filename);
                $data['event_image'] = $filename;
            }
        }
    
        if (!empty($id)) {
            $result = $this->common->update_data('events', $data, ['event_id' => $id, 'event_user_id' => $user_id]);
        } else {
            $result = $this->common->insert_data('events', $data);
        }
    
        if ($result) {
            $msg = !empty($id) ? 'Event updated successfully!' : 'Event added successfully!';
            return redirect()->to(base_url('view_events'))->with('success_message', $msg);
        }
    
        return redirect()->back()->withInput()->with('error', 'Something went wrong');
    }



	public function create_event($id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['page_title']    = 'Edit Event';
		$data['page_subtitle'] = 'Update event settings and details';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['event_list'] = $this->common->select('events', '*', array('event_active' => 1), true);
		$data['listing'] = $this->common->select('listings', '*', array('listing_id' => $id));
		echo view('organizer/create_event', $data);
	}

	public function attempt_create_event($id)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'title'        => ['label' => 'Event Title', 'rules' => 'required'],
			'event_type'   => ['label' => 'Event Type', 'rules' => 'required'],
			'type'         => ['label' => 'Match Type', 'rules' => 'required|min_length[4]'],
			'fee'          => ['label' => 'Fee', 'rules' => 'required|min_length[2]|numeric'],
			'event_start'  => ['label' => 'Event Start Date', 'rules' => 'required'],
			'event_end'    => ['label' => 'Event End Date', 'rules' => 'required'],
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$title            = trim($this->request->getPost('title'));
		$type             = $this->request->getPost('type');
		$event_type       = $this->request->getPost('event_type');
		$fee              = $this->request->getPost('fee');
		$event_start      = $this->request->getPost('event_start');
		$event_end        = $this->request->getPost('event_end');
		$matchmaking_date = $this->request->getPost('matchmaking_date');

		$listing_events = $this->common->select('listing_events', '*', array('listing_event_title' => $title, 'listing_event_listing_id' => $id));
		if (!empty($listing_events)) {
			return redirect()->back()->withInput()->with('error', 'Event Title already exist');
		}

		$tnbaid = !empty($this->request->getPost('tnba_id')) ? 1 : 0;
		$baiid = !empty($this->request->getPost('bai_id')) ? 1 : 0;
		$otherid = !empty($this->request->getPost('other_id')) ? 1 : 0;
		$otherid = !empty($this->request->getPost('other_id')) ? 1 : 0;
		$dob = !empty($this->request->getPost('dob')) ? 1 : 0;
		$data = [
			'listing_event_listing_id'      => xss_clean($id),
			'listing_event_title'           => xss_clean($title),
			'listing_event_type'            => xss_clean($event_type),
			'listing_match_type'            => xss_clean($type),
			'listing_event_fee'             => xss_clean($fee),
			'listing_event_start'           => xss_clean($event_start),
			'listing_event_end'             => xss_clean($event_end),
			'listing_event_matchmaking_date'=> !empty($matchmaking_date) ? xss_clean($matchmaking_date) : null,
			'listing_event_tnba_id'         => xss_clean($tnbaid),
			'listing_event_bai_id'          => xss_clean($baiid),
			'listing_event_other_id'        => xss_clean($otherid),
			'listing_event_dob'             => xss_clean($dob),
		];

		$result = $this->common->insert_data('listing_events', $data);
		if ($result) {
			return redirect()->to(base_url('view_events/' . $id))->with('success_message', 'Event created successfully!');
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}

	public function edit_tourney_event($id, $list_id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['event_list'] = $this->common->select('events', '*', array('event_active' => 1), true);
		$data['event'] = $this->common->select('listing_events', '*', array('listing_event_id' => $id, 'listing_event_active' => 1));
		$data['listing'] = $this->common->select('listings', '*', array('listing_id' => $list_id));
		echo view('organizer/edit_tourney_event', $data);
	}

	public function attempt_edit_tourney_event($id, $list_id)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'title '	=>  ['label' => 'Event Title', 'rules' => 'required'],
			'event_type' =>  ['label' => 'Event Type', 'rules' => 'required'],
			// 'min_member' =>  ['label' => 'Minimum Member', 'rules' => 'required'],
			'type' => ['label' => 'Type', 'rules' => 'required|min_length[4]'],
			'fee' =>  ['label' => 'Fee', 'rules' => 'required|min_length[2]|numeric'],
			'event_start' =>  ['label' => 'Event start date', 'rules' => 'required'],
			'event_end' =>  ['label' => 'Event end date', 'rules' => 'required']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$title = trim($this->request->getPost('title'));
		$listing_events = $this->common->select('listing_events', '*', array('listing_event_title' => $title, 'listing_event_id !=' => $id, 'listing_event_listing_id' => $list_id));
		if (!empty($listing_events)) {
			return redirect()->back()->withInput()->with('error', 'Event Title already exist');
		}
		$type = $this->request->getPost('type');
		$event_type = $this->request->getPost('event_type');
		// $min_member = $this->request->getPost('min_member');
		$fee = $this->request->getPost('fee');
		$duration = !empty($this->request->getPost('duration')) ? $this->request->getPost('duration') : '';
		$event_start = $this->request->getPost('event_start');
		$event_end = $this->request->getPost('event_end');
		$matchmaking_date = $this->request->getPost('matchmaking_date');
		$max_players      = $this->request->getPost('max_players');
		$max_teams        = $this->request->getPost('max_teams');

		$tnbaid = !empty($this->request->getPost('tnba_id')) ? 1 : 0;
		$baiid  = !empty($this->request->getPost('bai_id')) ? 1 : 0;
		$otherid = !empty($this->request->getPost('other_id')) ? 1 : 0;
		$dob    = !empty($this->request->getPost('dob')) ? 1 : 0;

		$isTeam = $event_type === 'team';
		$data = [
			'listing_event_title'            => xss_clean($title),
			'listing_event_type'             => xss_clean($event_type),
			'listing_match_type'             => xss_clean($type),
			'listing_event_fee'              => xss_clean($fee),
			'listing_event_duration'         => xss_clean($duration),
			'listing_event_start'            => xss_clean($event_start),
			'listing_event_end'              => xss_clean($event_end),
			'listing_event_matchmaking_date' => !empty($matchmaking_date) ? xss_clean($matchmaking_date) : null,
			'listing_event_tnba_id'          => xss_clean($tnbaid),
			'listing_event_bai_id'           => xss_clean($baiid),
			'listing_event_other_id'         => xss_clean($otherid),
			'listing_event_dob'              => xss_clean($dob),
			'listing_event_max_players'      => $isTeam ? (int)($max_players ?? 1) : 1,
			'listing_event_max_teams'        => $isTeam ? (int)($max_teams ?? 0) : 0,
			'listing_event_updated_at'       => date('Y-m-d H:i:s'),
		];

		$result = $this->common->update_data('listing_events', $data, array('listing_event_id' => $id));

		if ($result) {
			return redirect()->to(base_url('view_events/' . $list_id))->with('success_message', 'Event updated successfully!');
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}

	public function view_events($tourney_id = null)
	{
		$user_id = session()->get('user_id');
		$data['page_title']    = 'Events';
		$data['page_subtitle'] = 'Manage events for this tournament';
		$search = $this->request->getGET('search');

		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tourney_id'] = $tourney_id;
		$data['events'] = $this->tournament->get_events($tourney_id, $search);
		//print_array($data['events']);die();
		echo view('organizer/view_events', $data);
	}
	
	public function add_tourney_event($list_id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['event_list'] = $this->common->select('events', '*', array('event_user_id' => $user_id), true);

		echo view('organizer/edit_event', $data);
	}
	public function attempt_add_tourney_event($id = NULL)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}

		$rules = [
			'event_name.*'    => ['rules' => 'required'],
			'event_type.*'    => ['rules' => 'required'],
			'type.*'          => ['rules' => 'required'],
			'fee.*'           => ['rules' => 'required'],
			'event_start.*'   => ['rules' => 'required'],
			'event_end.*'     => ['rules' => 'required'],
			'lastbook_date.*' => ['rules' => 'required']
		];


		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}


		$user_id = session()->get('user_id');


		$title 			= $this->request->getPost('event_name[]');
		$type 			= $this->request->getPost('type[]');
		$event_type 	= $this->request->getPost('event_type[]');
		$fee 			= $this->request->getPost('fee[]');
		$event_start 	= $this->request->getPost('event_start[]');
		$event_end 		= $this->request->getPost('event_end[]');
		$lastbook_date 	= $this->request->getPost('lastbook_date[]');
		$max_players_arr = $this->request->getPost('max_players[]');
		$max_teams_arr   = $this->request->getPost('max_teams[]');

		$tnba_id 		= $this->request->getPost('tnba_id[]');
		$bai_id 		= $this->request->getPost('bai_id[]');
		$other_id 		= $this->request->getPost('other_id[]');
		$dob 			= $this->request->getPost('dob[]');

		foreach ($title as $key => $value) {
			$tnbaid = !empty($tnba_id[$key]) ? $tnba_id[$key] : 0;
			$baiid = !empty($bai_id[$key]) ? $bai_id[$key] : 0;
			$otherid = !empty($other_id[$key]) ? $other_id[$key] : 0;
			$dob = !empty($dob[$key]) ? $dob[$key] : 0;
			$isTeam = !empty($event_type[$key]) && $event_type[$key] === 'team';
			$data = [
				'listing_event_listing_id' 		=> xss_clean($id),
				'listing_event_title' 			=> xss_clean($title[$key]),
				'listing_event_type' 			=> xss_clean($event_type[$key]),
				'listing_match_type' 			=> xss_clean($type[$key]),
				'listing_event_fee' 			=> xss_clean($fee[$key]),
				'listing_event_start' 			=> xss_clean($event_start[$key]),
				'listing_event_end' 			=> xss_clean($event_end[$key]),
				'listing_event_lastbook_date' 	=> xss_clean($lastbook_date[$key]),
				'listing_event_tnba_id' 		=> xss_clean($tnbaid),
				'listing_event_bai_id' 			=> xss_clean($baiid),
				'listing_event_other_id'		=> xss_clean($otherid),
				'listing_event_dob' 			=> xss_clean($dob),
				'listing_event_max_players'		=> $isTeam ? (int)($max_players_arr[$key] ?? 1) : 1,
				'listing_event_max_teams'		=> $isTeam ? (int)($max_teams_arr[$key] ?? 0) : 0,
			];

			$result = $this->common->insert_data('listing_events', $data);
		}

		$msg = 'Event  added successfully!';

		return redirect()->to(base_url('organizer'))->with('success_message', $msg);
	}
	public function my_payments()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['commonModel'] = $this->common;
		$list_id = isset($_GET['tournaments']) ? $_GET['tournaments'] : "";
		$event_id = isset($_GET['events']) ? $_GET['events'] : "";
		$item_count = $this->tournament->count_organizer_payments($user_id, $list_id, $event_id);
		//echo $item_count;exit;
		$data['total'] = $item_count;
		$per_page = 25;
		$page = $this->request->getGET('page');
		$offset = ($page * $per_page) - $per_page;
		if (empty($page)) {
			$page = 1;
			$offset = 0;
		}
		$data['page'] = $page;
		$data['perPage'] = $per_page;
		$data['payments'] = $this->tournament->organizer_payments($per_page, $offset, $user_id, $list_id, $event_id);

		$data['tournaments'] = $this->common->select('listings', 'listing_id,listing_user_id,listing_title', array('listing_user_id' => $user_id), true);

		$events = $this->common->select('listing_events', '*', array(), true);
		if ($events) {
			foreach ($events as $row) {
				$row->event_name = '';
				$event = $this->common->select('events', '*', array('event_id' => $row->listing_event_title));
				if (!empty($event->event_name)) {
					$row->event_name = $event->event_name;
				}
			}
		}

		$data['events'] = $events;
		echo view('organizer/organizer_payments', $data);
	}

	public function group_list()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['page_title']    = 'Groups';
		$data['page_subtitle'] = 'Manage tournament groups';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['commonModel'] = $this->common;

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);

		$tournament = $this->request->getGET('tournament');
		$event = $this->request->getGET('event');

		$item_count = $this->common->count_group_list($user_id, $tournament, $event);
		//echo $item_count;exit;
		$data['total'] = $item_count;
		$per_page = 25;
		$page = $this->request->getGET('page');
		$offset = ($page * $per_page) - $per_page;
		if (empty($page)) {
			$page = 1;
			$offset = 0;
		}
		$data['page'] = $page;
		$data['perPage'] = $per_page;
		$data['group_list'] = $this->common->get_group_list($per_page, $offset, $user_id, $tournament, $event);

		echo view('organizer/group_list', $data);
	}




	public function create_group($id = NULL)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Create Group';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		echo view('organizer/create_group', $data);
	}

	public function edit_group($id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Edit Group';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);

		$data['groupdata'] = $this->common->select('tbl_groups', '*', array('grp_id' => $id));
		echo view('organizer/create_group', $data);
	}

	public function attempt_create_group()
	{
		$rules = [
			'tournament'	=>  ['label' => 'Tournament', 'rules' => 'required'],
			'event' =>  ['label' => 'Event', 'rules' => 'required'],
			'group_name' =>  ['label' => 'Group name', 'rules' => 'required']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$date = date('Y-m-d H:i:s');
		$grp_id = $this->request->getPost('grp_id');
		$tournament = $this->request->getPost('tournament');
		$event = $this->request->getPost('event');
		$group_name = $this->request->getPost('group_name');


		$condition = [
			'grp_tournament' => $tournament,
			'grp_event' => $event,
			'grp_name' => $group_name
		];

		if (!empty($grp_id)) {
			$condition['grp_id !='] = $grp_id;
		}

		$exist = $this->common->select('tbl_groups', '*', $condition);
		if (!empty($exist)) {
			return redirect()->back()->withInput()->with('error', 'Group already exist');
		}

		$data = [
			'grp_tournament' => xss_clean($tournament),
			'grp_event' => xss_clean($event),
			'grp_name' => xss_clean($group_name)
		];


		if (!empty($grp_id)) {
			$data['grp_updated_at'] = $date;
			$this->common->update_data('tbl_groups', $data, array('grp_id' => $grp_id));
			$result = $grp_id;
			$msg = 'Group updated successfully!';
		} else {
			$data['grp_created_at'] = $date;
			$result = $this->common->insert_data('tbl_groups', $data);
			$msg = 'Group added successfully!';
		}

		if ($result) {
			return redirect()->to(base_url('organizer/group_list'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}

	public function get_ajax_org_events()
	{

		$user_id = session()->get('user_id');
		$tourney_id = $this->request->getPost('tourney_id');
		//echo $tourney_id;die();
		$cities = $this->common->get_org_event($user_id, $tourney_id);
		//var_dump($cities);die();
		$content = '<option  value="">Select Event</option>';
		if (isset($cities) && !empty($cities)) {
			foreach ($cities as $data) {
				$content .= '<option value="' . $data->listing_event_id . '">' . $data->event_name . '</option>';
			}
		}
		echo $content;
	}


	public function round_list()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['page_title']    = 'Rounds';
		$data['page_subtitle'] = 'Manage tournament rounds';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['commonModel'] = $this->common;

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblgroups'] = $this->common->get_org_group($user_id);

		$tblgroup = $this->request->getGET('tblgroup');
		$tournament = $this->request->getGET('tournament');
		$event = $this->request->getGET('event');

		$item_count = $this->common->count_round_list($user_id, $tblgroup, $tournament, $event);
		//echo $item_count;exit;
		$data['total'] = $item_count;
		$per_page = 15;
		$page = $this->request->getGET('page');
		$offset = ($page * $per_page) - $per_page;
		if (empty($page)) {
			$page = 1;
			$offset = 0;
		}
		$data['page'] = $page;
		$data['perPage'] = $per_page;
		$data['round_list'] = $this->common->get_round_list($per_page, $offset, $user_id, $tblgroup, $tournament, $event);

		echo view('organizer/round_list', $data);
	}

	public function create_round($id = NULL)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Create Round';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblgroups'] = $this->common->get_org_group($user_id);
		echo view('organizer/create_round', $data);
	}

	public function edit_round($id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Edit Round';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblgroups'] = $this->common->get_org_group($user_id);

		$data['rounddata'] = $this->common->select('tbl_rounds', '*', array('round_id' => $id));
		echo view('organizer/create_round', $data);
	}

	public function attempt_create_round()
	{
		$rules = [
			'tournament'	=>  ['label' => 'Tournament', 'rules' => 'required'],
			'event' =>  ['label' => 'Event', 'rules' => 'required'],
			'group_name' =>  ['label' => 'Round name', 'rules' => 'required'],
			'round_name' =>  ['label' => 'Round name', 'rules' => 'required']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$date = date('Y-m-d H:i:s');
		$round_id = $this->request->getPost('round_id');
		$tournament = $this->request->getPost('tournament');
		$event = $this->request->getPost('event');
		$group_name = $this->request->getPost('group_name');
		$round_name = $this->request->getPost('round_name');

		$condition = [
			'round_name' => $round_name,
			'round_group' => $group_name,
			'round_tournament' => $tournament,
			'round_event' => $event
		];

		if (!empty($round_id)) {
			$condition['round_id !='] = $round_id;
		}

		$exist = $this->common->select('tbl_rounds', '*', $condition);
		if (!empty($exist)) {
			return redirect()->back()->withInput()->with('error', 'Round already exist');
		}

		$data = [
			'round_name' => xss_clean($round_name),
			'round_group' => xss_clean($group_name),
			'round_tournament' => xss_clean($tournament),
			'round_event' => xss_clean($event)
		];

		if (!empty($round_id)) {
			$data['round_updated_at'] = $date;
			$this->common->update_data('tbl_rounds', $data, array('round_id' => $round_id));
			$result = $round_id;
			$msg = 'Round updated successfully!';
		} else {
			$data['round_created_at'] = $date;
			$result = $this->common->insert_data('tbl_rounds', $data);
			$msg = 'Round added successfully!';
		}

		if ($result) {
			return redirect()->to(base_url('organizer/round_list'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}


	public function get_ajax_org_group()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$event_id = $this->request->getPost('event_id');
		$cities = $this->common->get_org_group($user_id, $event_id);

		$content = '<option value="">Select Group</option>';
		if (isset($cities) && !empty($cities)) {
			foreach ($cities as $data) {
				$content .= '<option value="' . $data->grp_id . '">' . $data->grp_name . '</option>';
			}
		}
		echo $content;
	}

	public function team_list()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['page_title']    = 'Teams';
		$data['page_subtitle'] = 'View and manage tournament teams';

		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Teams';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['commonModel'] = $this->common;

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblgroups'] = $this->common->get_org_group($user_id);

		$tblgroup = $this->request->getGet('tblgroup');
		$tournament = $this->request->getGet('tournament');
		$event = $this->request->getGet('event');

		$data['team_list'] = $this->common->get_team_list(null, null, $user_id, $tblgroup, $tournament, $event);

		echo view('organizer/team_list', $data);
	}



	public function create_team($id = NULL)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Create Team';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblgroups'] = $this->common->get_org_group($user_id);
		$data['players'] = $this->common->get_org_players($user_id);
		echo view('organizer/create_team', $data);
	}

	public function edit_team($id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Edit Team';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['players'] = $this->common->get_org_players($user_id);
		$data['tblgroups'] = $this->common->get_org_group($user_id);

		$data['teamdata'] = $this->common->select('teams', '*', array('team_id' => $id));
		echo view('organizer/create_team', $data);
	}

	public function attempt_create_team()
	{
		$rules = [
			'tournament'	=>  ['label' => 'Tournament', 'rules' => 'required'],
			'event' =>  ['label' => 'Event', 'rules' => 'required'],
			'group_name' =>  ['label' => 'Group name', 'rules' => 'required'],
			'team_name' =>  ['label' => 'Team name', 'rules' => 'required']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$date = date('Y-m-d H:i:s');
		$team_id = $this->request->getPost('team_id');
		$tournament = $this->request->getPost('tournament');
		$event = $this->request->getPost('event');
		$group_name = $this->request->getPost('group_name');
		$team_name = $this->request->getPost('team_name');


		$condition = [
			'team_name' => $team_name,
			'team_group' => $group_name,
			'team_tournament' => $tournament,
			'team_event' => $event
		];

		if (!empty($team_id)) {
			$condition['team_id !='] = $team_id;
		}

		$exist = $this->common->select('teams', '*', $condition);
		if (!empty($exist)) {
			return redirect()->back()->withInput()->with('error', 'Team already exist');
		}

		$data = [
			'team_name' => xss_clean($team_name),
			'team_group' => xss_clean($group_name),
			'team_tournament' => xss_clean($tournament),
			'team_event' => xss_clean($event)
		];
		$db = \Config\Database::connect();

		if (!empty($team_id)) {
			$data['team_updated_at'] = $date;
			$this->common->update_data('teams', $data, array('team_id' => $team_id));
			$result = $team_id;

			$player_ids = $this->request->getPost('player_name'); // This will be an array
			$team1 = $db->table('teams')
				->select('team_id')
				->where('team_name', $team_name)
				->get()
				->getRow();

			$team_id = $team1->team_id;
			foreach ($player_ids as $id) {
				$data1 = [
					'participant_team_id' => $team_id
				];
				$data2 = [
					'team_id' => $team_id
				];
				$this->common->update_data('participants', $data1, ['participant_id' => $id]);

				// Create a fresh condition for player lookup
				$playerCondition = ['player_participant_id' => $id];

				$exist1 = $this->common->select('players', 'players.player_id', $playerCondition);

				if (!empty($exist1)) {
					$this->common->update_data('players', $data2, ['player_id' => $exist1->player_id]);
				}
			}

			$msg = 'Team updated successfully!';
		} else {
			$data['team_created_at'] = $date;
			$result = $this->common->insert_data('teams', $data);

			$player_ids = $this->request->getPost('player_name'); // This will be an array
			$team1 = $db->table('teams')
				->select('team_id')
				->where('team_name', $team_name)
				->get()
				->getRow();

			$team_id = $team1->team_id;
			foreach ($player_ids as $id) {
				$data1 = [
					'participant_team_id' => $team_id
				];
				$data2 = [
					'team_id' => $team_id
				];
				$this->common->update_data('participants', $data1, ['participant_id' => $id]);

				// Create a fresh condition for player lookup
				$playerCondition = ['player_participant_id' => $id];

				$exist1 = $this->common->select('players', 'players.player_id', $playerCondition);

				if (!empty($exist1)) {
					$this->common->update_data('players', $data2, ['player_id' => $exist1->player_id]);
				}
			}


			$msg = 'Team added successfully!';
		}

		if ($result) {
			return redirect()->to(base_url('organizer/team_list'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}

	public function match_list()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['page_title']    = 'Matches';
		$data['page_subtitle'] = 'View all scheduled matches';
		$data['page_name'] = 'Matches';

		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', ['user_id' => $user_id]);
		$data['commonModel'] = $this->common;

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblrounds'] = $this->common->get_org_round($user_id);

		$tblround = $this->request->getGET('tblround');
		$tournament = $this->request->getGET('tournament');
		$event = $this->request->getGET('event');

		$data['match_list'] = $this->common->get_match_list(NULL, NULL, $user_id, $tblround, $tournament, $event);

		echo view('organizer/match_list', $data);
	}


	public function results()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$data['users']         = $this->common->select('users', '*', ['user_id' => $user_id]);
		$data['page_title']    = 'Results';
		$data['page_subtitle'] = 'Match results for all your tournaments';
		$data['tournaments']   = $this->common->get_org_tournaments($user_id);
		$data['events']        = $this->common->get_org_events($user_id);

		$tournament = $this->request->getGET('tournament');
		$event      = $this->request->getGET('event');

		$data['match_list'] = $this->common->get_match_list(NULL, NULL, $user_id, NULL, $tournament, $event);

		echo view('organizer/results', $data);
	}

	public function create_match($id = NULL)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Create Match';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblgroup'] = $this->common->get_org_evt_group($user_id);
		$data['tblrounds'] = $this->common->get_org_round($user_id);
		$data['umpires'] = $this->common->select('users', '*', array('user_organizer_id' => $user_id), true);

		echo view('organizer/create_match', $data);
	}
	public function schedule_match($tourney_id, $event_id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}

		$tournament_name = $this->common->get_tour_details($tourney_id);
		$event_name = $this->common->get_event_details($event_id);

		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Create Match';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblgroup'] = $this->common->get_org_evt_group($user_id);
		$data['tblrounds'] = $this->common->get_org_round($user_id);
		$data['umpires'] = $this->common->select('users', '*', array('user_organizer_id' => $user_id), true);
		$data['tournament_name'] = $tournament_name;
		$data['event_name'] = $event_name;

		echo view('organizer/schedule_match', $data);
	}

	public function edit_match($id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Edit Match';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['tournaments'] = $this->common->get_org_tournaments($user_id);
		$data['events'] = $this->common->get_org_events($user_id);
		$data['tblgroup'] = $this->common->get_org_evt_group($user_id);
		$data['tblrounds'] = $this->common->get_org_round($user_id);
		$data['umpires'] = $this->common->select('users', '*', array('user_organizer_id' => $user_id), true);

		$data['matchdata'] = $this->common->select('matches', '*', array('match_round_id' => $id), true);
		$data['commonModel'] = $this->common;
		echo view('organizer/create_match', $data);
	}

	public function attempt_create_match()
	{
		$rules = [
			'tournament'    =>  ['label' => 'Tournament', 'rules' => 'required'],
			'event'         =>  ['label' => 'Event', 'rules' => 'required'],
			'group_name'    =>  ['label' => 'Group', 'rules' => 'required'],
			'round_name'    =>  ['label' => 'Round', 'rules' => 'required'],
			'team1.*'       =>  ['label' => 'Team 1', 'rules' => 'required'],
			'team2.*'       =>  ['label' => 'Team 2', 'rules' => 'required'],
			'match_date.*'  =>  ['label' => 'Match date', 'rules' => 'required'],
			'match_location.*' =>  ['label' => 'Match location', 'rules' => 'required']
		];

		if (!$this->validate($rules)) {
			return $this->response->setJSON([
				'status' => 'error',
				'errors' => $this->validator->getErrors()
			]);
		}

		$team_pairs = [];
		$selected_teams = [];
		$errors = [];

		$team1 = $this->request->getPost('team1');
		$team2 = $this->request->getPost('team2');

		foreach ($team1 as $key => $val) {
			// Check if Team 1 and Team 2 are the same
			if ($team1[$key] == $team2[$key]) {
				$errors[] = "Team 1 and Team 2 cannot be the same for match " . ($key + 1);
			}

			// Check if the team pair already exists
			$pair = [$team1[$key], $team2[$key]];
			sort($pair); // Sort to avoid checking [team1, team2] and [team2, team1] as different pairs

			if (in_array($pair, $team_pairs)) {
				$errors[] = "The combination of Team 1 and Team 2 is already selected for match " . ($key + 1);
			}

			$team_pairs[] = $pair;

			// Check if a team is already selected for another match
			if (in_array($team1[$key], $selected_teams)) {
				$errors[] = "Team 1 (ID: {$team1[$key]}) is already selected for another match.";
			}

			if (in_array($team2[$key], $selected_teams)) {
				$errors[] = "Team 2 (ID: {$team2[$key]}) is already selected for another match.";
			}

			// Add teams to the selected teams list
			$selected_teams[] = $team1[$key];
			$selected_teams[] = $team2[$key];
		}

		// If there are errors, return them
		if (!empty($errors)) {
			return $this->response->setJSON([
				'status' => 'error',
				'extra_errors' => $errors
			]);
		}

		$date = date('Y-m-d H:i:s');
		$group_name = $this->request->getPost('group_name');
		$round_name = $this->request->getPost('round_name');
		$tournament = $this->request->getPost('tournament');
		$event = $this->request->getPost('event');
		$match_date = $this->request->getPost('match_date');
		$match_location = $this->request->getPost('match_location');
		$match_umpire = $this->request->getPost('umpire');

		$listing_event = $this->common->select('listing_events', '*', array('listing_event_id' => $event), false);

		if (!empty($listing_event)) {

			foreach ($team1 as $key => $val) {
				$matchdate = date('Y-m-d H:i:s', strtotime($match_location[$key]));
				$team1_id = isset($team1[$key]) ? $team1[$key] : '';
				$team2_id = isset($team2[$key]) ? $team2[$key] : '';


				$data = [
					'match_group_id' => xss_clean($group_name),
					'match_type' => $listing_event->listing_event_type,
					'match_round_id' => xss_clean($round_name),
					'match_tournament' => xss_clean($tournament),
					'match_event' => xss_clean($event),
					'match_date' => xss_clean($match_date[$key]),
					'match_location' => xss_clean($match_location[$key]),
					'match_umpire' => xss_clean($match_umpire[$key])
				];
				$data['match_team1_id'] = xss_clean($team1[$key]);
				$data['match_team2_id'] = xss_clean($team2[$key]);
				$data['match_individual1_id'] = xss_clean($team1[$key]);
				$data['match_individual2_id'] = xss_clean($team2[$key]);

				$match_id = !empty($_POST['match_id'][$key]) ? $_POST['match_id'][$key] : '';
				if (!empty($match_id)) {
					$data['match_updated_at'] = $date;
					$this->common->update_data('matches', $data, array('match_id' => $match_id));
					$result = $match_id;
					$msg = 'Match updated successfully!';
				} else {
					$data['match_created_at'] = $date;
					$result = $this->common->insert_data('matches', $data);
					$msg = 'Match added successfully!';
				}
			}

			if ($result) {
				session()->set('success_message', $msg);
				return $this->response->setJSON([
					'status' => 'success',
					'message' => $msg
				]);
			} else {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Something went wrong. Please try again.'
				]);
			}
		} else {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Event not exist'
			]);
		}
	}

	public function get_event_schedule_groups($event_id)
	{
		$db = \Config\Database::connect();
		$event = $db->table('listing_events le')
			->select('le.*, e.event_name, l.listing_title, l.listing_address, l.listing_sports_id, s.sports_name')
			->join('events e', 'e.event_id = le.listing_event_title', 'left')
			->join('listings l', 'l.listing_id = le.listing_event_listing_id', 'left')
			->join('sports s', 's.sports_id = l.listing_sports_id', 'left')
			->where('le.listing_event_id', $event_id)
			->get()->getRow();

		if (!$event) {
			return $this->response->setJSON(['status' => false, 'message' => 'Event not found']);
		}

		$type = strtolower($event->listing_event_type ?? 'individual');
		$max_players = (int)($event->listing_event_max_players ?? 0);
		if ($max_players <= 0) $max_players = ($type === 'team') ? 11 : 1;

		$participants = $db->table('participants')
			->where('participant_event_id', $event_id)
			->orderBy('participant_id', 'ASC')
			->get()->getResult();

		// Count units based on event type
		$units = [];
		if ($type === 'team') {
			$teams = $db->table('teams')
				->where('team_listing_event_id', $event_id)
				->orWhere('(team_event = ' . (int)$event_id . ' AND team_tournament = ' . (int)$event->listing_event_listing_id . ')')
				->get()->getResult();

			$team_map = [];
			foreach ($teams as $t) {
				$team_map[$t->team_id] = [
					'id'      => $t->team_id,
					'name'    => $t->team_name,
					'group'   => $t->team_group ?? 0,
					'players' => []
				];
			}

			foreach ($participants as $p) {
				$tid = $p->participant_team_id ?? 0;
				if (isset($team_map[$tid])) {
					if (count($team_map[$tid]['players']) < $max_players) {
						$team_map[$tid]['players'][] = $p;
					}
				} else {
					$found = false;
					foreach ($team_map as &$tm) {
						if (!empty($p->participant_club) && strtolower($tm['name']) === strtolower($p->participant_club)) {
							if (count($tm['players']) < $max_players) {
								$tm['players'][] = $p;
								$found = true;
								break;
							}
						}
					}
					unset($tm);
					if (!$found) {
						$new_id = $p->participant_id;
						$team_map['p_' . $new_id] = [
							'id'      => $new_id,
							'name'    => (!empty($p->participant_club) ? $p->participant_club : $p->participant_name . ' Team'),
							'group'   => 0,
							'players' => [$p]
						];
					}
				}
			}
			$units = array_values($team_map);
		} elseif ($type === 'double' || $type === 'doubles') {
			$chunks = array_chunk($participants, 2);
			foreach ($chunks as $chunk) {
				$names = array_map(fn($p) => $p->participant_name, $chunk);
				$units[] = [
					'id'      => $chunk[0]->participant_id,
					'name'    => implode(' & ', $names),
					'group'   => 0,
					'players' => $chunk
				];
			}
		} else {
			// Individual
			foreach ($participants as $p) {
				$units[] = [
					'id'      => $p->participant_id,
					'name'    => $p->participant_name,
					'group'   => 0,
					'players' => [$p]
				];
			}
		}

		// Groups available for this tournament + event only
		$listing_id = (int) $event->listing_event_listing_id;
		$db_groups  = $db->table('tbl_groups')
			->where('grp_event', (int) $event_id)
			->where('grp_tournament', $listing_id)
			->orderBy('grp_id', 'ASC')
			->get()->getResult();

		$has_group = !empty($db_groups);

		// Group type label from the event's match format
		$type_label = 'Knockout';
		$match_type = trim((string) ($event->listing_match_type ?? ''));
		if ($match_type !== '') {
			$type_label = ucwords(str_replace(['_', '-'], ' ', $match_type));
		}

		// Every team/player goes to the first created group, all other groups stay empty
		if ($has_group) {
			$first_group = (int) $db_groups[0]->grp_id;

			if ($type === 'team') {
				$db->table('teams')
					->groupStart()
						->where('team_listing_event_id', (int) $event_id)
						->orWhere('(team_event = ' . (int) $event_id . ' AND team_tournament = ' . $listing_id . ')')
					->groupEnd()
					->where('team_group !=', $first_group)
					->update(['team_group' => $first_group]);
			}

			foreach ($units as &$u) {
				$u['group'] = $first_group;
			}
			unset($u);
		}

		$groups_data = [];
		foreach ($db_groups as $g) {
			$is_first  = (int) $g->grp_id === (int) $db_groups[0]->grp_id;
			$count     = $is_first ? count(array_filter($units, fn($u) => ($u['group'] ?? 0) == $g->grp_id)) : 0;
			$rounds    = ($count > 0) ? max(1, (int) ceil(log($count, 2))) : 0;
			$groups_data[] = [
				'group_id'    => $g->grp_id,
				'group_name'  => $g->grp_name,
				'type'        => $type_label,
				'players'     => $count,
				'rounds'      => $rounds,
				'has_players' => $count > 0,
				'view_url'    => ($count > 0) ? base_url('organizer/event_matches/' . $event_id . '?group=' . $g->grp_id) : '',
			];
		}

		// Units still in contention (never lost) -> e.g. 8 before start, 4 after R1, 2, then 1
		$active_count = $this->_countActiveUnits($db, (int) $event_id, $units);
		$active_badge = $active_count . ' Active';

		$venue = !empty($event->listing_address) ? trim(explode("\n", $event->listing_address)[0]) : 'Indoor Stadium';
		if (strlen($venue) > 25 || empty($venue)) {
			$venue = 'Indoor Stadium';
		}

		return $this->response->setJSON([
			'status'           => true,
			'has_group'        => $has_group,
			'message'          => $has_group ? '' : 'No group available for this event. Please create a group first.',
			'tournament_title' => $event->listing_title ?: 'Tiruvallur District Badminton Championship',
			'event_name'       => $event->event_name ?: 'Under 15 Boys Singles',
			'sports_name'      => $event->sports_name ?: 'Badminton',
			'venue'            => $venue,
			'active_badge'     => $active_badge,
			'groups'           => $groups_data
		]);
	}

	public function event_matches($event_id)
	{
		if (session()->get('role_id') != 3 && session()->get('role_id') != 1) return redirect()->to(base_url());

		$user_id  = session()->get('user_id');
		$db       = \Config\Database::connect();
		$group_id = (int)$this->request->getGet('group');

		// The bracket always belongs to the event's first-created group (grp_id ASC)
		$first_group = $db->table('tbl_groups')
			->where('grp_event', $event_id)
			->orderBy('grp_id', 'ASC')
			->get()->getFirstRow();
		if ($first_group) {
			$group_id = (int)$first_group->grp_id;
		}

		$event = $db->table('listing_events le')
			->select('le.*, e.event_name, l.listing_title, l.listing_address, l.listing_sports_id, s.sports_name')
			->join('events e', 'e.event_id = le.listing_event_title', 'left')
			->join('listings l', 'l.listing_id = le.listing_event_listing_id', 'left')
			->join('sports s', 's.sports_id = l.listing_sports_id', 'left')
			->where('le.listing_event_id', $event_id)
			->get()->getRow();

		if (!$event) return redirect()->to(base_url('organizer'));

		// Automatically assign Round 1 initially if no rounds or matches exist
		$rounds = $db->table('tbl_rounds')
			->where('round_event', $event_id)
			->orderBy('round_id', 'ASC')
			->get()->getResult();

		$existing_matches_count = 0;
		if (!empty($rounds)) {
			$r_ids = array_map(fn($r) => $r->round_id, $rounds);
			$existing_matches_count = $db->table('matches')->whereIn('match_round_id', $r_ids)->countAllResults();
		}

		if (empty($rounds) || $existing_matches_count === 0) {
			$this->_auto_assign_bracket($event_id, $group_id);
			$rounds = $db->table('tbl_rounds')
				->where('round_event', $event_id)
				->orderBy('round_id', 'ASC')
				->get()->getResult();
		}

		// Players who registered after the draw was generated
		$missing_players = $this->_sync_bracket_players($event_id);
		$rounds          = $db->table('tbl_rounds')
			->where('round_event', $event_id)
			->orderBy('round_id', 'ASC')
			->get()->getResult();

		$is_individual = $event->listing_event_type !== 'team';

		// Units still in contention (never lost a match) for the "X Active" badge
		$active_type = strtolower($event->listing_event_type ?? 'individual');
		$active_max  = (int) ($event->listing_event_max_players ?? 0);
		if ($active_max <= 0) $active_max = ($active_type === 'team') ? 11 : 1;
		$active_parts = $db->table('participants')
			->where('participant_event_id', $event_id)
			->orderBy('participant_id', 'ASC')
			->get()->getResult();
		$active_count = $this->_countActiveUnits(
			$db,
			(int) $event_id,
			$this->_buildBracketUnits($db, $event, $active_parts, $active_type, $active_max)
		);

		$total_bracket_players = 0;
		$round_stats = [];

		foreach ($rounds as $round_idx => $round) {
			$matches = $db->table('matches as m')
				->select('m.*, t1.team_name as team1_name, t2.team_name as team2_name,
					p1.participant_name as player1_name, p2.participant_name as player2_name,
					wt.team_name as winner_team_name, wp.participant_name as winner_player_name')
				->join('teams t1', 't1.team_id = m.match_team1_id', 'left')
				->join('teams t2', 't2.team_id = m.match_team2_id', 'left')
				->join('participants p1', 'p1.participant_id = m.match_individual1_id', 'left')
				->join('participants p2', 'p2.participant_id = m.match_individual2_id', 'left')
				->join('teams wt', 'wt.team_id = m.match_winning_team_id', 'left')
				->join('participants wp', 'wp.participant_id = m.match_winning_individual_id', 'left')
				->where('m.match_round_id', $round->round_id)
				->orderBy('m.match_id', 'ASC')
				->get()->getResult();

			// Attach scores and initials
			foreach ($matches as $match) {
				// Badminton / sets scores
				$set_rows = $db->table('scores_badminton')
					->where('match_id', $match->match_id)
					->orderBy('set_number', 'ASC')
					->get()->getResult();

				$sets_by_team = [];
				foreach ($set_rows as $sr) {
					$sets_by_team[$sr->team_id][$sr->set_number] = $sr->points;
				}
				$match->sets_scores = $sets_by_team;

				// Generic / other scores
				$match->score_data = null;
				if (!empty($match->match_winning_desc)) {
					$decoded = json_decode($match->match_winning_desc, true);
					$match->score_data = is_array($decoded) ? $decoded : null;
				}

				// Player/Team names and avatars
				$p1_n = $is_individual ? ($match->player1_name ?? '') : ($match->team1_name ?? '');
				$p2_n = $is_individual ? ($match->player2_name ?? '') : ($match->team2_name ?? '');
				$match->p1_display_name = $p1_n ?: 'TBD';
				$match->p2_display_name = $p2_n ?: 'TBD';
				$match->p1_avatar = !empty($p1_n) ? strtoupper(mb_substr($p1_n, 0, 1)) : '?';
				$match->p2_avatar = !empty($p2_n) ? strtoupper(mb_substr($p2_n, 0, 1)) : '?';

				// Winner
				$winner_id = $is_individual ? $match->match_winning_individual_id : $match->match_winning_team_id;
				$match->winner_id = (int)$winner_id;
			}

			$round->matches = $matches;
			$completed = array_filter($matches, fn($m) => $is_individual
				? !empty($m->match_winning_individual_id)
				: !empty($m->match_winning_team_id));
			$round->is_complete = count($matches) > 0 && count($completed) === count($matches);
			$round->completed_count = count($completed);
			$round->total_count = count($matches);
			$round->is_final = strtolower($round->round_name) === 'final';

			if ($round_idx === 0) {
				// Round 1 determines total players in draw
				$total_bracket_players = count($matches) * 2;
			}
		}

		// Units currently seeded into Round 1 -> options for the player-swap dropdown
		$round1_units = [];
		if (!empty($rounds) && !empty($rounds[0]->matches)) {
			foreach ($rounds[0]->matches as $r1m) {
				$u1_id = $is_individual ? (int) $r1m->match_individual1_id : (int) $r1m->match_team1_id;
				$u2_id = $is_individual ? (int) $r1m->match_individual2_id : (int) $r1m->match_team2_id;
				if ($u1_id > 0) $round1_units[$u1_id] = $r1m->p1_display_name;
				if ($u2_id > 0) $round1_units[$u2_id] = $r1m->p2_display_name;
			}
			asort($round1_units, SORT_STRING | SORT_FLAG_CASE);
		}

		// Final winner - shown at the top with the winner icon
		$champion    = ['id' => 0, 'name' => ''];
		$final_round = null;
		foreach ($rounds as $rnd) {
			if (!empty($rnd->is_final)) $final_round = $rnd;
		}
		if (!$final_round && !empty($rounds)) {
			$final_round = $rounds[count($rounds) - 1];
		}
		if ($final_round && !empty($final_round->matches)) {
			foreach ($final_round->matches as $fm) {
				if ($fm->winner_id > 0) {
					$nm = $is_individual ? ($fm->winner_player_name ?? '') : ($fm->winner_team_name ?? '');
					if (empty($nm)) {
						$first_id = $is_individual ? (int) $fm->match_individual1_id : (int) $fm->match_team1_id;
						$nm = ((int) $fm->winner_id === $first_id) ? $fm->p1_display_name : $fm->p2_display_name;
					}
					$champion = ['id' => (int) $fm->winner_id, 'name' => $nm ?: 'Winner'];
					break;
				}
			}
		}

		$venue = !empty($event->listing_address) ? trim(explode("\n", $event->listing_address)[0]) : 'Indoor Stadium';
		if (strlen($venue) > 25 || empty($venue)) {
			$venue = 'Indoor Stadium';
		}

		$data['users']                 = $this->common->select('users', '*', ['user_id' => $user_id]);
		$data['event']                 = $event;
		$data['rounds']                = $rounds;
		$data['event_id']              = $event_id;
		$data['group_id']              = $group_id;
		$data['missing_players']       = $missing_players;
		$data['round1_units']          = $round1_units;
		$data['champion']              = $champion;
		$data['active_count']          = $active_count;
		$data['total_bracket_players'] = max($total_bracket_players, count($rounds) ? count($rounds[0]->matches) * 2 : 64);
		$data['venue']                 = $venue;
		$data['page_title']            = 'Match Schedule';
		$data['page_subtitle']         = esc($event->event_name ?? '') . ' — ' . esc($event->listing_title ?? '');

		echo view('organizer/event_matches', $data);
	}

	private function _auto_assign_bracket($event_id, $group_id = 0)
	{
		$db  = \Config\Database::connect();
		$now = date('Y-m-d H:i:s');

		// Rounds/matches always belong to the event's first-created group (grp_id ASC),
		// so a stale or default group id can never be written again.
		$first_group = $db->table('tbl_groups')
			->where('grp_event', $event_id)
			->orderBy('grp_id', 'ASC')
			->get()->getFirstRow();
		if ($first_group) {
			$group_id = (int)$first_group->grp_id;
		} elseif ((int)$group_id <= 0) {
			$group_id = 0;
		}

		$event = $db->table('listing_events le')
			->select('le.*, COALESCE(l.listing_sports_id, 0) AS listing_sports_id, s.sports_name')
			->join('listings l', 'l.listing_id = le.listing_event_listing_id', 'left')
			->join('sports s', 's.sports_id = l.listing_sports_id', 'left')
			->where('le.listing_event_id', $event_id)
			->get()->getRow();

		if (!$event) return false;

		$type = strtolower($event->listing_event_type ?? 'individual');
		$sport_id = (int)($event->listing_sports_id ?? 0);
		$max_players = (int)($event->listing_event_max_players ?? 0);
		if ($max_players <= 0) $max_players = ($type === 'team') ? 11 : 1;

		$participants = $db->table('participants')
			->where('participant_event_id', $event_id)
			->orderBy('participant_id', 'ASC')
			->get()->getResult();

		// Competing units
		$units = $this->_buildBracketUnits($db, $event, $participants, $type, $max_players);

		$N = count($units);
		if ($N < 2) {
			// Single or no participant placeholder
			$db->table('tbl_rounds')->insert([
				'round_name'       => 'Round 1',
				'round_group'      => $group_id,
				'round_tournament' => $event->listing_event_listing_id,
				'round_event'      => $event_id,
				'round_status'     => 1,
				'round_created_at' => $now,
				'round_updated_at' => $now,
			]);
			return true;
		}

		$R = max(1, (int) ceil(log($N, 2)));
		$bracket_size = pow(2, $R);

		// Clean old rounds/matches for this event if re-generating empty
		$db->table('tbl_rounds')->where('round_event', $event_id)->delete();
		$db->table('matches')->where('match_event', $event_id)->delete();

		// Create rounds in tbl_rounds
		$round_ids = [];
		for ($r = 1; $r <= $R; $r++) {
			$round_name = bmm_round_name($r, $R, (string) ($event->sports_name ?? ''));
			$db->table('tbl_rounds')->insert([
				'round_name'       => $round_name,
				'round_group'      => $group_id,
				'round_tournament' => $event->listing_event_listing_id,
				'round_event'      => $event_id,
				'round_status'     => 1,
				'round_created_at' => $now,
				'round_updated_at' => $now,
			]);
			$round_ids[$r] = $db->insertID();
		}

		$match_date = !empty($event->listing_event_start) ? $event->listing_event_start : date('Y-m-d 08:00:00');
		$match_type = ($type === 'team') ? 'team' : 'individual';

		// Create matches per round
		$round_match_ids = [];
		for ($r = 1; $r <= $R; $r++) {
			$num_matches = (int)($bracket_size / pow(2, $r));
			$round_match_ids[$r] = [];

			for ($m = 1; $m <= $num_matches; $m++) {
				$p1_id = 0;
				$p2_id = 0;

				if ($r === 1) {
					$u1 = $units[($m - 1) * 2] ?? null;
					$u2 = $units[($m - 1) * 2 + 1] ?? null;
					$p1_id = $u1 ? (int)$u1['id'] : 0;
					$p2_id = $u2 ? (int)$u2['id'] : 0;
				}

				$match_data = [
					'match_type'                  => $match_type,
					'match_round_id'              => $round_ids[$r],
					'match_tournament'            => $event->listing_event_listing_id,
					'match_event'                 => $event_id,
					'match_sport_id'              => $sport_id,
					'match_team1_id'              => ($type === 'team') ? $p1_id : 0,
					'match_team2_id'              => ($type === 'team') ? $p2_id : 0,
					'match_individual1_id'        => ($type === 'team') ? 0 : $p1_id,
					'match_individual2_id'        => ($type === 'team') ? 0 : $p2_id,
					'match_date'                  => $match_date,
					'match_status'                => 1,
					'match_winning_individual_id' => 0,
					'match_winning_team_id'       => 0,
					'loss'                        => 0,
					'match_created_at'            => $now,
					'match_updated_at'            => $now,
				];

				// If single bye in Round 1
				if ($r === 1 && $p1_id > 0 && $p2_id === 0) {
					if ($type === 'team') {
						$match_data['match_winning_team_id'] = $p1_id;
					} else {
						$match_data['match_winning_individual_id'] = $p1_id;
					}
				}

				$db->table('matches')->insert($match_data);
				$round_match_ids[$r][$m] = $db->insertID();
			}
		}

		// Advance any Round 1 byes into Round 2 placeholder matches
		if (isset($round_match_ids[2])) {
			$r1_matches = (int)($bracket_size / 2);
			for ($m = 1; $m <= $r1_matches; $m++) {
				$u1 = $units[($m - 1) * 2] ?? null;
				$u2 = $units[($m - 1) * 2 + 1] ?? null;
				if ($u1 && !$u2) {
					$next_m        = (int) ceil($m / 2);
					$slot          = ($m % 2 === 1) ? 1 : 2;
					$next_match_id = $round_match_ids[2][$next_m] ?? null;
					if ($next_match_id) {
						$upField = ($type === 'team')
							? ($slot === 1 ? 'match_team1_id' : 'match_team2_id')
							: ($slot === 1 ? 'match_individual1_id' : 'match_individual2_id');
						$db->table('matches')->where('match_id', $next_match_id)->update([$upField => (int)$u1['id']]);
					}
				}
			}
		}

		return true;
	}

	// Build the competing units (players / doubles pairs / teams) a draw is made of
	private function _buildBracketUnits($db, $event, array $participants, string $type, int $max_players): array
	{
		$units  = [];
		$ev_id  = (int) $event->listing_event_id;

		if ($type === 'team') {
			$teams = $db->table('teams')
				->where('team_listing_event_id', $ev_id)
				->orWhere('(team_event = ' . $ev_id . ' AND team_tournament = ' . (int) $event->listing_event_listing_id . ')')
				->get()->getResult();

			$team_map = [];
			foreach ($teams as $t) {
				$team_map[$t->team_id] = [
					'id'   => $t->team_id,
					'name' => $t->team_name,
					'p'    => []
				];
			}
			foreach ($participants as $p) {
				$tid = $p->participant_team_id ?? 0;
				if (isset($team_map[$tid])) {
					if (count($team_map[$tid]['p']) < $max_players) {
						$team_map[$tid]['p'][] = $p;
					}
				} else {
					$new_id = $p->participant_id;
					$team_map['p_' . $new_id] = [
						'id'   => $new_id,
						'name' => (!empty($p->participant_club) ? $p->participant_club : $p->participant_name . ' Team'),
						'p'    => [$p]
					];
				}
			}
			$units = array_values($team_map);
		} elseif ($type === 'double' || $type === 'doubles') {
			$chunks = array_chunk($participants, 2);
			foreach ($chunks as $chunk) {
				$units[] = [
					'id'   => $chunk[0]->participant_id,
					'name' => implode(' & ', array_map(fn($p) => $p->participant_name, $chunk)),
					'p'    => $chunk
				];
			}
		} else {
			// Individual
			foreach ($participants as $p) {
				$units[] = [
					'id'   => $p->participant_id,
					'name' => $p->participant_name,
					'p'    => [$p]
				];
			}
		}

		return $units;
	}

	/**
	 * Units still in contention = every unit that never lost a match for this event.
	 * Starts at the full field and drops as rounds are completed (8 -> 4 -> 2 -> 1).
	 */
	private function _countActiveUnits($db, int $event_id, array $units): int
	{
		$loser_ids = [];

		// Derive the loser of every decided match from the two slots + winner,
		// so the count stays correct even where `loss` was never written.
		$rows = $db->table('matches')
			->select('match_individual1_id, match_individual2_id, match_team1_id, match_team2_id, match_winning_individual_id, match_winning_team_id')
			->where('match_event', $event_id)
			->get()->getResult();

		foreach ($rows as $m) {
			$winner = (int) $m->match_winning_individual_id ?: (int) $m->match_winning_team_id;
			if ($winner <= 0) continue;

			$slot_ids = [
				(int) $m->match_individual1_id, (int) $m->match_individual2_id,
				(int) $m->match_team1_id,      (int) $m->match_team2_id,
			];
			foreach ($slot_ids as $sid) {
				if ($sid > 0 && $sid !== $winner) {
					$loser_ids[$sid] = true;
				}
			}
		}

		$active = 0;
		foreach ($units as $u) {
			if (!isset($loser_ids[(int) ($u['id'] ?? 0)])) {
				$active++;
			}
		}

		return $active;
	}

	/**
	 * Pick up players who registered after the draw was generated.
	 * Returns the names of players still missing from the bracket.
	 * - No results yet  -> rebuild the draw so every player is in it.
	 * - Results exist    -> leave the bracket untouched, caller shows a notice.
	 */
	private function _sync_bracket_players(int $event_id): array
	{
		$db = \Config\Database::connect();

		$rounds = $db->table('tbl_rounds')
			->where('round_event', $event_id)
			->orderBy('round_id', 'ASC')
			->get()->getResult();

		if (empty($rounds)) return [];

		$r_ids   = array_map(fn($r) => $r->round_id, $rounds);
		$matches = $db->table('matches')->whereIn('match_round_id', $r_ids)->get()->getResult();

		if (empty($matches)) return [];

		$has_results = false;
		foreach ($matches as $m) {
			$w  = (int) $m->match_winning_individual_id ?: (int) $m->match_winning_team_id;
			if ($w <= 0) continue;

			$p1 = (int) $m->match_individual1_id ?: (int) $m->match_team1_id;
			$p2 = (int) $m->match_individual2_id ?: (int) $m->match_team2_id;

			// Byes (auto-winner against an empty slot) are not real results
			$opponent = ($w === $p1) ? $p2 : $p1;
			if ($opponent > 0) {
				$has_results = true;
				break;
			}
		}

		// Unit ids already placed in Round 1
		$first_round_id = (int) $rounds[0]->round_id;
		$taken          = [];
		foreach ($matches as $m) {
			if ((int) $m->match_round_id !== $first_round_id) continue;
			$taken[] = (int) $m->match_individual1_id;
			$taken[] = (int) $m->match_individual2_id;
			$taken[] = (int) $m->match_team1_id;
			$taken[] = (int) $m->match_team2_id;
		}
		$taken = array_filter($taken);

		$event = $db->table('listing_events')
			->where('listing_event_id', $event_id)
			->get()->getRow();

		if (!$event) return [];

		$type        = strtolower($event->listing_event_type ?? 'individual');
		$max_players = (int) ($event->listing_event_max_players ?? 0);
		if ($max_players <= 0) $max_players = ($type === 'team') ? 11 : 1;

		$participants = $db->table('participants')
			->where('participant_event_id', $event_id)
			->orderBy('participant_id', 'ASC')
			->get()->getResult();

		$units = $this->_buildBracketUnits($db, $event, $participants, $type, $max_players);

		$missing_ids   = [];
		$missing_names = [];
		foreach ($units as $u) {
			if (!in_array((int) $u['id'], $taken, true)) {
				$missing_ids[]              = (int) $u['id'];
				$missing_names[(int) $u['id']] = $u['name'];
			}
		}

		if (empty($missing_ids)) return [];

		// Nothing has been decided yet -> rebuild so all players appear
		if (!$has_results) {
			$group_id = (int) $rounds[0]->round_group;
			$db->table('tbl_rounds')->where('round_event', $event_id)->delete();
			$db->table('matches')->where('match_event', $event_id)->delete();
			$this->_auto_assign_bracket($event_id, $group_id);
			return [];
		}

		// Results already exist -> drop the late players into empty Round 1 slots
		$left = $this->_fill_empty_bracket_slots($db, $rounds, $matches, $units, $missing_ids, $type);

		$names = [];
		foreach ($left as $id) {
			$names[] = $missing_names[$id] ?? ('#' . $id);
		}

		return $names;
	}

	/**
	 * Put players who registered late into Round 1 slots that are still empty.
	 * Decided matches are never touched; free byes are undone so the new
	 * player actually gets to play, then re-applied if a bye is unavoidable.
	 * Returns the ids that could not be placed.
	 */
	private function _fill_empty_bracket_slots($db, array $rounds, array $matches, array $units, array $queue_ids, string $type): array
	{
		if (empty($queue_ids) || empty($rounds)) return $queue_ids;

		$is_team = ($type === 'team');
		$f1      = $is_team ? 'match_team1_id' : 'match_individual1_id';
		$f2      = $is_team ? 'match_team2_id' : 'match_individual2_id';
		$fw      = $is_team ? 'match_winning_team_id' : 'match_winning_individual_id';

		$r1_id = (int) $rounds[0]->round_id;
		$r1    = array_values(array_filter($matches, fn($m) => (int) $m->match_round_id === $r1_id));
		usort($r1, fn($a, $b) => $a->match_id <=> $b->match_id);

		$r2 = [];
		if (isset($rounds[1])) {
			$r2 = array_values(array_filter($matches, fn($m) => (int) $m->match_round_id === (int) $rounds[1]->round_id));
			usort($r2, fn($a, $b) => $a->match_id <=> $b->match_id);
		}

		// 1) Free the slots of pure byes (auto-winner with an empty opponent)
		foreach ($r1 as $i => $m) {
			$p1 = (int) $m->{$f1};
			$p2 = (int) $m->{$f2};
			$w  = (int) $m->{$fw};
			if ($w <= 0) continue;
			if (($w === $p1 ? $p2 : $p1) > 0) continue;   // real result - leave alone

			$m->{$fw} = 0;
			$db->table('matches')->where('match_id', $m->match_id)->update([$fw => 0]);

			// remove the bye's advance into Round 2
			$pos = $i + 1;
			$next = (int) ceil($pos / 2);
			$slot = ($pos % 2 === 1) ? 1 : 2;
			if (isset($r2[$next - 1])) {
				$n  = $r2[$next - 1];
				$nf = ($slot === 1) ? $f1 : $f2;
				if ((int) $n->{$nf} === $w) {
					$n->{$nf} = 0;
					$db->table('matches')->where('match_id', $n->match_id)->update([$nf => 0]);
				}
			}
		}

		// 2) Fill the empty slots in draw order
		foreach ($r1 as $m) {
			if (empty($queue_ids)) break;
			if ((int) $m->{$fw} > 0) continue;

			$patch = [];
			if ((int) $m->{$f1} === 0 && !empty($queue_ids)) {
				$id = array_shift($queue_ids);
				$m->{$f1} = $id;
				$patch[$f1] = $id;
			}
			if ((int) $m->{$f2} === 0 && !empty($queue_ids)) {
				$id = array_shift($queue_ids);
				$m->{$f2} = $id;
				$patch[$f2] = $id;
			}
			if (!empty($patch)) {
				$db->table('matches')->where('match_id', $m->match_id)->update($patch);
			}
		}

		// 3) Any single-sided match left over is a bye - advance the player
		foreach ($r1 as $i => $m) {
			if ((int) $m->{$fw} > 0) continue;

			$p1 = (int) $m->{$f1};
			$p2 = (int) $m->{$f2};
			$winner = 0;
			if ($p1 > 0 && $p2 === 0)      $winner = $p1;
			elseif ($p2 > 0 && $p1 === 0)  $winner = $p2;

			if ($winner <= 0) continue;

			$m->{$fw} = $winner;
			$db->table('matches')->where('match_id', $m->match_id)->update([$fw => $winner]);

			$pos  = $i + 1;
			$next = (int) ceil($pos / 2);
			$slot = ($pos % 2 === 1) ? 1 : 2;
			if (isset($r2[$next - 1])) {
				$n  = $r2[$next - 1];
				$nf = ($slot === 1) ? $f1 : $f2;
				if ((int) $n->{$nf} === 0) {
					$n->{$nf} = $winner;
					$db->table('matches')->where('match_id', $n->match_id)->update([$nf => $winner]);
				}
			}
		}

		return $queue_ids;
	}

	public function save_bracket_winner()
	{
		if (session()->get('role_id') != 3 && session()->get('role_id') != 1) {
			return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
		}

		$match_id  = (int) $this->request->getPost('match_id');
		$winner_id = (int) $this->request->getPost('winner_id');
		$loser_id  = (int) $this->request->getPost('loser_id');

		if (!$match_id || !$winner_id) {
			return $this->response->setJSON(['status' => false, 'message' => 'match_id and winner_id required']);
		}

		$db  = \Config\Database::connect();
		$now = date('Y-m-d H:i:s');

		$match = $db->table('matches')->where('match_id', $match_id)->get()->getRow();
		if (!$match) {
			return $this->response->setJSON(['status' => false, 'message' => 'Match not found']);
		}

		$event = $db->table('listing_events')->where('listing_event_id', $match->match_event)->get()->getRow();
		$is_team = ($event && $event->listing_event_type === 'team');

		// 1. Update current match winner and loser
		$update = [
			'match_updated_at' => $now,
			'loss'             => $loser_id,
		];
		if ($is_team) {
			$update['match_winning_team_id'] = $winner_id;
		} else {
			$update['match_winning_individual_id'] = $winner_id;
		}
		$db->table('matches')->where('match_id', $match_id)->update($update);

		// 2. Advance winner to next round match in bracket
		$all_rounds = $db->table('tbl_rounds')
			->where('round_event', $match->match_event)
			->orderBy('round_id', 'ASC')
			->get()->getResult();

		$current_round_index = -1;
		foreach ($all_rounds as $idx => $r) {
			if ($r->round_id == $match->match_round_id) {
				$current_round_index = $idx;
				break;
			}
		}

		$next_round = null;
		if ($current_round_index !== -1 && isset($all_rounds[$current_round_index + 1])) {
			$next_round = $all_rounds[$current_round_index + 1];
		}

		$advanced_to = null;
		if ($next_round) {
			$current_matches = $db->table('matches')
				->where('match_round_id', $match->match_round_id)
				->orderBy('match_id', 'ASC')
				->get()->getResult();

			$match_index = 1;
			foreach ($current_matches as $idx => $cm) {
				if ($cm->match_id == $match_id) {
					$match_index = $idx + 1;
					break;
				}
			}

			$target_match_index = (int) ceil($match_index / 2);
			$slot               = ($match_index % 2 === 1) ? 1 : 2;

			$next_matches = $db->table('matches')
				->where('match_round_id', $next_round->round_id)
				->orderBy('match_id', 'ASC')
				->get()->getResult();

			$target_match = $next_matches[$target_match_index - 1] ?? null;
			if ($target_match) {
				$next_update = ['match_updated_at' => $now];
				if ($is_team) {
					if ($slot === 1) $next_update['match_team1_id'] = $winner_id;
					else             $next_update['match_team2_id'] = $winner_id;
				} else {
					if ($slot === 1) $next_update['match_individual1_id'] = $winner_id;
					else             $next_update['match_individual2_id'] = $winner_id;
				}
				$db->table('matches')->where('match_id', $target_match->match_id)->update($next_update);
				$advanced_to = [
					'match_id' => $target_match->match_id,
					'slot'     => $slot,
					'winner_id'=> $winner_id
				];
			}
		}

		// Winner details
		$winner_name = 'Winner';
		if ($is_team) {
			$tRow = $db->table('teams')->where('team_id', $winner_id)->get()->getRow();
			$winner_name = $tRow->team_name ?? 'Team';
		} else {
			$pRow = $db->table('participants')->where('participant_id', $winner_id)->get()->getRow();
			$winner_name = $pRow->participant_name ?? 'Player';
		}

		// Units still in contention after this result
		$active_type = strtolower($event->listing_event_type ?? 'individual');
		$active_max  = (int) ($event->listing_event_max_players ?? 0);
		if ($active_max <= 0) $active_max = ($active_type === 'team') ? 11 : 1;
		$active_parts = $db->table('participants')
			->where('participant_event_id', $match->match_event)
			->orderBy('participant_id', 'ASC')
			->get()->getResult();
		$active_count = $this->_countActiveUnits(
			$db,
			(int) $match->match_event,
			$this->_buildBracketUnits($db, $event, $active_parts, $active_type, $active_max)
		);

		return $this->response->setJSON([
			'status'       => true,
			'message'      => $winner_name . ' declared winner',
			'match_id'     => $match_id,
			'winner_id'    => $winner_id,
			'winner_name'  => $winner_name,
			'avatar'       => strtoupper(mb_substr($winner_name, 0, 1)),
			'advanced_to'  => $advanced_to,
			'is_final'     => ($next_round === null),
			'active_count' => $active_count
		]);
	}

	/**
	 * Round 1 only: place a different unit (player/team) into one match slot.
	 * If that unit already occupies another Round 1 slot, the two units are
	 * swapped so nobody ends up duplicated or missing from the bracket.
	 */
	public function swap_bracket_player()
	{
		if (session()->get('role_id') != 3 && session()->get('role_id') != 1) {
			return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
		}

		$match_id = (int) $this->request->getPost('match_id');
		$slot     = (int) $this->request->getPost('slot'); // 1 or 2
		$new_id   = (int) $this->request->getPost('unit_id');

		if (!$match_id || !in_array($slot, [1, 2], true)) {
			return $this->response->setJSON(['status' => false, 'message' => 'match_id and slot (1 or 2) are required']);
		}

		$db  = \Config\Database::connect();
		$now = date('Y-m-d H:i:s');

		$match = $db->table('matches')->where('match_id', $match_id)->get()->getRow();
		if (!$match) {
			return $this->response->setJSON(['status' => false, 'message' => 'Match not found']);
		}

		// Only the earliest round for this event can be edited this way
		$first_round = $db->table('tbl_rounds')
			->where('round_event', $match->match_event)
			->orderBy('round_id', 'ASC')
			->get()->getFirstRow();
		if (!$first_round || (int) $first_round->round_id !== (int) $match->match_round_id) {
			return $this->response->setJSON(['status' => false, 'message' => 'Players can only be changed in Round 1.']);
		}

		$event   = $db->table('listing_events')->where('listing_event_id', $match->match_event)->get()->getRow();
		$is_team = ($event && $event->listing_event_type === 'team');

		$p1_field     = $is_team ? 'match_team1_id' : 'match_individual1_id';
		$p2_field     = $is_team ? 'match_team2_id' : 'match_individual2_id';
		$winner_field = $is_team ? 'match_winning_team_id' : 'match_winning_individual_id';
		$slot_field   = ($slot === 1) ? $p1_field : $p2_field;
		$other_field  = ($slot === 1) ? $p2_field : $p1_field;

		if ((int) $match->$winner_field > 0) {
			return $this->response->setJSON(['status' => false, 'message' => 'Cannot change players after a result has been recorded for this match.']);
		}

		$old_id = (int) $match->$slot_field;
		if ($old_id === $new_id) {
			return $this->response->setJSON(['status' => true, 'message' => 'No change']);
		}

		// Find wherever the picked unit currently sits in Round 1, so it can be swapped out
		$partner       = null;
		$partner_field = null;
		if ($new_id > 0) {
			$found = $db->table('matches')
				->where('match_round_id', $match->match_round_id)
				->groupStart()
					->where($p1_field, $new_id)
					->orWhere($p2_field, $new_id)
				->groupEnd()
				->get()->getResult();
			$partner = $found[0] ?? null;

			if ($partner) {
				$partner_field = ((int) $partner->$p1_field === $new_id) ? $p1_field : $p2_field;

				if ((int) $partner->match_id !== $match_id && (int) $partner->$winner_field > 0) {
					return $this->response->setJSON(['status' => false, 'message' => 'Cannot swap with a match that already has a result.']);
				}
			}
		}

		$db->table('matches')->where('match_id', $match_id)->update([
			$slot_field        => $new_id,
			'match_updated_at' => $now,
		]);

		$swapped_to = null;
		if ($partner) {
			if ((int) $partner->match_id === $match_id) {
				// Picked the other slot's own occupant -> it takes this slot's old unit
				$db->table('matches')->where('match_id', $match_id)->update([$other_field => $old_id]);
				$swapped_to = ['match_id' => $match_id, 'slot' => ($other_field === $p1_field) ? 1 : 2];
			} else {
				$db->table('matches')->where('match_id', $partner->match_id)->update([
					$partner_field     => $old_id,
					'match_updated_at' => $now,
				]);
				$swapped_to = ['match_id' => (int) $partner->match_id, 'slot' => ($partner_field === $p1_field) ? 1 : 2];
			}
		}

		$name_table = $is_team ? 'teams' : 'participants';
		$name_col   = $is_team ? 'team_name' : 'participant_name';
		$id_col     = $is_team ? 'team_id' : 'participant_id';

		$names = [];
		foreach (array_filter([$new_id, $old_id]) as $uid) {
			$row         = $db->table($name_table)->where($id_col, $uid)->get()->getRow();
			$names[$uid] = $row->$name_col ?? '';
		}

		return $this->response->setJSON([
			'status'      => true,
			'message'     => 'Player updated',
			'match_id'    => $match_id,
			'slot'        => $slot,
			'new_id'      => $new_id,
			'new_name'    => $new_id ? ($names[$new_id] ?? 'TBD') : 'TBD',
			'old_id'      => $old_id,
			'old_name'    => $old_id ? ($names[$old_id] ?? 'TBD') : 'TBD',
			'swapped_to'  => $swapped_to,
		]);
	}

	public function update_match_result()
	{
		if (session()->get('role_id') != 3)
			return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);

		$match_id  = (int) $this->request->getPost('match_id');
		$winner_id = (int) $this->request->getPost('winner_id');
		$loser_id  = (int) $this->request->getPost('loser_id');
		$type      = $this->request->getPost('match_type'); // team or individual

		if (!$match_id || !$winner_id) {
			return $this->response->setJSON(['status' => false, 'message' => 'match_id and winner_id required']);
		}

		$db  = \Config\Database::connect();
		$now = date('Y-m-d H:i:s');

		if ($type === 'individual') {
			$update = [
				'match_winning_individual_id' => $winner_id,
				'loss'                        => $loser_id,
				'match_updated_at'            => $now,
			];
		} else {
			$update = [
				'match_winning_team_id' => $winner_id,
				'loss'                  => $loser_id,
				'match_updated_at'      => $now,
			];
		}

		$db->table('matches')->where('match_id', $match_id)->update($update);

		// Check if entire round is now complete
		$match      = $db->table('matches')->where('match_id', $match_id)->get()->getRow();
		$all        = $db->table('matches')->where('match_round_id', $match->match_round_id)->get()->getResult();
		$done       = array_filter($all, fn($m) => $type === 'individual'
			? !empty($m->match_winning_individual_id)
			: !empty($m->match_winning_team_id));
		$round_done = count($done) === count($all);

		// Check if this is the Final
		$round = $db->table('tbl_rounds')->where('round_id', $match->match_round_id)->get()->getRow();
		$is_final = $round && strtolower($round->round_name) === 'final';

		return $this->response->setJSON([
			'status'       => true,
			'message'      => 'Result updated',
			'round_done'   => $round_done,
			'is_final'     => $is_final,
			'round_id'     => $match->match_round_id,
		]);
	}

	public function update_match_score()
	{
		if (session()->get('role_id') != 3)
			return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);

		$match_id   = (int) $this->request->getPost('match_id');
		$sport_type = $this->request->getPost('sport_type'); // sets|cricket|goal|points|time|decision
		$now        = date('Y-m-d H:i:s');
		$db         = \Config\Database::connect();

		if ($sport_type === 'sets') {
			// Delete all existing set rows for this match, then re-insert
			$team1_id = (int) $this->request->getPost('team1_id');
			$team2_id = (int) $this->request->getPost('team2_id');
			$num_sets = (int) $this->request->getPost('num_sets');
			$sets_raw = $this->request->getPost('sets') ?? [];

			$db->table('scores_badminton')->where('match_id', $match_id)->delete();

			for ($s = 1; $s <= $num_sets; $s++) {
				$p1_raw = $sets_raw[$s]['p1'] ?? '';
				$p2_raw = $sets_raw[$s]['p2'] ?? '';
				if ($p1_raw === '' && $p2_raw === '') continue; // skip sets the user left completely blank
				$p1 = (int)$p1_raw;
				$p2 = (int)$p2_raw;
				$db->table('scores_badminton')->insertBatch([
					['match_id' => $match_id, 'team_id' => $team1_id, 'player_id' => 0, 'set_number' => $s, 'points' => $p1, 'created_at' => $now],
					['match_id' => $match_id, 'team_id' => $team2_id, 'player_id' => 0, 'set_number' => $s, 'points' => $p2, 'created_at' => $now],
				]);
			}

		} elseif ($sport_type === 'cricket') {
			// Store toss + innings summary in match_cricket JSON
			$toss_winner = (int) $this->request->getPost('toss_winner');
			$toss_choice = $this->request->getPost('toss_choice'); // bat|bowl
			$innings_raw = $this->request->getPost('innings') ?? [];

			$bat_first = ($toss_choice === 'bat') ? $toss_winner : 0;
			$summary   = ['toss_winner' => $toss_winner, 'bat_first' => $bat_first, 'toss_choice' => $toss_choice, 'innings' => []];

			foreach ($innings_raw as $bat_team_id => $data) {
				$summary['innings'][(int)$bat_team_id] = [
					'runs'    => (int)($data['runs']    ?? 0),
					'wickets' => (int)($data['wickets'] ?? 0),
					'overs'   => $data['overs'] ?? '0.0',
				];
			}

			$db->table('matches')->where('match_id', $match_id)->update([
				'match_cricket'    => json_encode($summary),
				'match_updated_at' => $now,
			]);

		} elseif ($sport_type === 'goal' || $sport_type === 'points') {
			$score1 = $this->request->getPost('score1');
			$score2 = $this->request->getPost('score2');
			$db->table('matches')->where('match_id', $match_id)->update([
				'match_winning_desc' => json_encode(['type' => $sport_type, 'team1' => $score1, 'team2' => $score2]),
				'match_updated_at'   => $now,
			]);

		} elseif ($sport_type === 'time') {
			$score1 = $this->request->getPost('score1');
			$score2 = $this->request->getPost('score2');
			$db->table('matches')->where('match_id', $match_id)->update([
				'match_winning_desc' => json_encode(['type' => 'time', 'team1' => $score1, 'team2' => $score2]),
				'match_updated_at'   => $now,
			]);

		} elseif ($sport_type === 'decision') {
			$notes = $this->request->getPost('notes');
			$db->table('matches')->where('match_id', $match_id)->update([
				'match_winning_desc' => json_encode(['type' => 'decision', 'notes' => $notes]),
				'match_updated_at'   => $now,
			]);

		} else {
			// Fallback generic
			$score1 = $this->request->getPost('score1');
			$score2 = $this->request->getPost('score2');
			$db->table('matches')->where('match_id', $match_id)->update([
				'match_winning_desc' => json_encode(['type' => 'generic', 'team1' => $score1, 'team2' => $score2]),
				'match_updated_at'   => $now,
			]);
		}

		return $this->response->setJSON(['status' => true, 'message' => 'Score updated']);
	}

	// POST: event_id, round_id, match_date
	public function start_next_round()
	{
		if (session()->get('role_id') != 3) {
			return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
		}

		$event_id   = (int) $this->request->getPost('event_id');
		$round_id   = (int) $this->request->getPost('round_id');
		$match_date = $this->request->getPost('match_date');

		if (!$event_id || !$round_id || !$match_date) {
			return $this->response->setJSON(['status' => false, 'message' => 'event_id, round_id and match_date are required']);
		}

		$db    = \Config\Database::connect();
		$now   = date('Y-m-d H:i:s');
		$event = $db->table('listing_events le')
			->select('le.*, COALESCE(l.listing_sports_id, 0) AS listing_sports_id, s.sports_name')
			->join('listings l', 'l.listing_id = le.listing_event_listing_id', 'left')
			->join('sports s', 's.sports_id = l.listing_sports_id', 'left')
			->where('le.listing_event_id', $event_id)
			->get()->getRow();

		if (!$event) {
			return $this->response->setJSON(['status' => false, 'message' => 'Event not found']);
		}
		$sport_id = (int)($event->listing_sports_id ?? 0);

		// Get all matches in this round
		$matches = $db->table('matches')
			->where('match_round_id', $round_id)
			->where('match_event', $event_id)
			->get()->getResult();

		if (empty($matches)) {
			return $this->response->setJSON(['status' => false, 'message' => 'No matches found in this round']);
		}

		// Check all matches have a result
		foreach ($matches as $m) {
			$has_winner = ($event->listing_event_type === 'individual')
				? !empty($m->match_winning_individual_id)
				: !empty($m->match_winning_team_id);
			if (!$has_winner) {
				return $this->response->setJSON(['status' => false, 'message' => 'All matches in the current round must have a winner before starting the next round']);
			}
		}

		// Collect winners and track who played in this round
		$winners       = [];
		$teams_in_round = [];
		foreach ($matches as $m) {
			if ($event->listing_event_type === 'individual') {
				$winners[]        = (int) $m->match_winning_individual_id;
				$teams_in_round[] = (int) $m->match_individual1_id;
				$teams_in_round[] = (int) $m->match_individual2_id;
			} else {
				$winners[]        = (int) $m->match_winning_team_id;
				$teams_in_round[] = (int) $m->match_team1_id;
				$teams_in_round[] = (int) $m->match_team2_id;
			}
		}

		// Auto-detect bye teams: still-active participants/teams that had no match this round
		// "Still active" = never appeared as a loser in any match for this event
		if ($event->listing_event_type === 'individual') {
			$active = $db->query("
				SELECT p.participant_id AS id FROM participants p
				INNER JOIN booking b ON b.book_event_id = ? AND b.book_tournament_id = ?
				WHERE p.participant_id = b.book_user_id
				  AND p.participant_id NOT IN (
				      SELECT COALESCE(loss,0) FROM matches WHERE match_event = ? AND loss > 0
				  )
			", [$event_id, $event->listing_event_listing_id, $event_id])->getResult();
			foreach ($active as $row) {
				$pid = (int)$row->id;
				if (!in_array($pid, $teams_in_round)) {
					$winners[] = $pid; // bye — auto-advances
				}
			}
		} else {
			$active = $db->query("
				SELECT team_id AS id FROM teams
				WHERE team_listing_event_id = ?
				  AND team_id NOT IN (
				      SELECT COALESCE(loss,0) FROM matches WHERE match_event = ? AND loss > 0
				  )
			", [$event_id, $event_id])->getResult();
			foreach ($active as $row) {
				$tid = (int)$row->id;
				if (!in_array($tid, $teams_in_round)) {
					$winners[] = $tid; // bye — auto-advances
				}
			}
		}

		$winners = array_values(array_unique($winners)); // deduplicate
		$total   = count($winners);

		if ($total < 2) {
			// Only 1 participant left — this was actually the Final
			return $this->response->setJSON(['status' => false, 'message' => 'Tournament complete — only 1 winner remains']);
		}

		// Determine next round name
		$existing_rounds = $db->table('tbl_rounds')->where('round_event', $event_id)->countAllResults();
		$round_name = $this->_getRoundName($total, (string) ($event->sports_name ?? ''), $existing_rounds);

		// Create next round
		$db->table('tbl_rounds')->insert([
			'round_name'       => $round_name,
			'round_group'      => 0,
			'round_tournament' => $event->listing_event_listing_id,
			'round_event'      => $event_id,
			'round_status'     => 1,
			'round_created_at' => $now,
			'round_updated_at' => $now,
		]);
		$new_round_id = $db->insertID();

		// Handle bye for odd number
		$bye_winner = null;
		if ($total % 2 !== 0) {
			$bye_winner = array_pop($winners);
		}

		$count = 0;
		foreach (array_chunk($winners, 2) as $pair) {
			$w1 = $pair[0];
			$w2 = $pair[1];

			if ($event->listing_event_type === 'individual') {
				$p1 = $db->table('participants')->where('participant_id', $w1)->get()->getRow();
				$p2 = $db->table('participants')->where('participant_id', $w2)->get()->getRow();

				$db->table('matches')->insert([
					'match_type'           => 'individual',
					'match_round_id'       => $new_round_id,
					'match_tournament'     => $event->listing_event_listing_id,
					'match_event'          => $event_id,
					'match_sport_id'       => $sport_id,
					'match_team1_id'       => 0,
					'match_team2_id'       => 0,
					'match_individual1_id' => $w1,
					'match_individual2_id' => $w2,
					'match_date'           => $match_date,
					'match_status'         => 1,
					'match_created_at'     => $now,
					'match_updated_at'     => $now,
				]);
				$this->_notifyIndividualNext($db, $p1, $p2, $match_date, $round_name);
			} else {
				$t1 = $db->table('teams')->where('team_id', $w1)->get()->getRow();
				$t2 = $db->table('teams')->where('team_id', $w2)->get()->getRow();

				$db->table('matches')->insert([
					'match_type'           => $event->listing_event_type,
					'match_round_id'       => $new_round_id,
					'match_tournament'     => $event->listing_event_listing_id,
					'match_event'          => $event_id,
					'match_sport_id'       => $sport_id,
					'match_team1_id'       => $w1,
					'match_team2_id'       => $w2,
					'match_individual1_id' => $w1,
					'match_individual2_id' => $w2,
					'match_date'           => $match_date,
					'match_status'         => 1,
					'match_created_at'     => $now,
					'match_updated_at'     => $now,
				]);

				// Lock teams for new round
				$db->table('teams')->whereIn('team_id', [$w1, $w2])->update(['team_status' => 'locked']);
				$this->_notifyTeamNext($db, $t1, $t2, $match_date, $round_name);
			}
			$count++;
		}

		return $this->response->setJSON([
			'status'     => true,
			'message'    => $round_name . ' created with ' . $count . ' match(es)',
			'round_id'   => $new_round_id,
			'round_name' => $round_name,
			'bye_winner' => $bye_winner,
		]);
	}

	private function _getRoundName(int $winner_count, string $sport = '', int $existing_rounds = 0): string
	{
		// winner_count = number of teams entering the new round
		if ($winner_count <= 2) return 'Final';
		if ($winner_count <= 4) return 'Semi Finals';

		$sport = strtolower($sport);

		// Badminton: rounds -> semi final -> final (never "Quarter Finals")
		if (strpos($sport, 'badminton') !== false) {
			return 'Round ' . ($existing_rounds + 1);
		}

		// Cricket: group stage -> semi final -> final (never "Quarter Finals")
		if (strpos($sport, 'cricket') !== false) {
			return ($existing_rounds <= 0) ? 'Group Stage' : 'Round ' . ($existing_rounds + 1);
		}

		if ($winner_count <= 8) return 'Quarter Finals';
		return 'Round ' . $winner_count;
	}

	private function _notifyTeamNext($db, $t1, $t2, string $match_date, string $round_name): void
	{
		$date_str = date('d M Y h:i A', strtotime($match_date));
		foreach ([$t1, $t2] as $my_team) {
			$opp = ($my_team->team_id === $t1->team_id) ? $t2 : $t1;
			$db->table('messages')->insert([
				'msg_title'      => $round_name . ' Match Scheduled!',
				'msg_desc'       => 'Your team "' . $my_team->team_name . '" will play against "' . $opp->team_name . '" in the ' . $round_name . ' on ' . $date_str,
				'msg_image'      => null,
				'msg_active'     => 1,
				'msg_created_at' => date('Y-m-d H:i:s'),
				'msg_updated_at' => date('Y-m-d H:i:s'),
			]);
			$msg_id = $db->insertID();
			$members = $db->table('team_members')
				->where('tm_team_id', $my_team->team_id)
				->where('tm_status', 'accepted')
				->where('tm_user_id IS NOT NULL', null, false)
				->get()->getResult();
			foreach ($members as $m) {
				$db->table('notifications')->insert([
					'notify_msg_id' => $msg_id,
					'notify_for'    => $m->tm_user_id,
					'notify_from'   => 0,
					'notify_count'  => 0,
					'notify_active' => 1,
					'notify_time'   => date('Y-m-d H:i:s'),
				]);
			}
		}
	}

	private function _notifyIndividualNext($db, $p1, $p2, string $match_date, string $round_name): void
	{
		$date_str = date('d M Y h:i A', strtotime($match_date));
		foreach ([$p1, $p2] as $me) {
			$opp = ($me->participant_id === $p1->participant_id) ? $p2 : $p1;
			$db->table('messages')->insert([
				'msg_title'      => $round_name . ' Match Scheduled!',
				'msg_desc'       => '"' . $me->participant_name . '" will play against "' . $opp->participant_name . '" in the ' . $round_name . ' on ' . $date_str,
				'msg_image'      => null,
				'msg_active'     => 1,
				'msg_created_at' => date('Y-m-d H:i:s'),
				'msg_updated_at' => date('Y-m-d H:i:s'),
			]);
			$msg_id = $db->insertID();
			$booking = $db->table('booking')
				->where('book_event_id', $me->participant_event_id)
				->where('book_tournament_id', $me->participant_list_id)
				->get()->getRow();
			if ($booking && $booking->book_user_id) {
				$db->table('notifications')->insert([
					'notify_msg_id' => $msg_id,
					'notify_for'    => $booking->book_user_id,
					'notify_from'   => 0,
					'notify_count'  => 0,
					'notify_active' => 1,
					'notify_time'   => date('Y-m-d H:i:s'),
				]);
			}
		}
	}

	public function delete_match()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$match_id = $this->request->getPost('match_id');

		if (!empty($match_id)) {
			$delete = $this->common->delete_data('matches', array('match_id' => $match_id));

			if ($delete) {
				echo json_encode(['status' => 'success']);
			} else {
				echo json_encode(['status' => 'error']);
			}
		} else {
			echo json_encode(['status' => 'error']);
		}
	}

	public function get_ajax_evt_group()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$event_id = $this->request->getPost('event_id');
		$cities = $this->common->get_org_evt_group($user_id, $event_id);

		$content = '<option value="">Select Group</option>';
		if (isset($cities) && !empty($cities)) {
			foreach ($cities as $data) {
				$content .= '<option value="' . $data->grp_id . '">' . $data->grp_name . '</option>';
			}
		}


		$team_event = $this->common->select('teams', '*', array('team_event' => $event_id), true);
		$teams = '<option value="">Select Player</option>';
		if (isset($team_event) && !empty($team_event)) {
			foreach ($team_event as $data) {
				$teams .= '<option value="' . $data->team_id . '">' . $data->team_name . '</option>';
			}
		}


		$data_cont = ['rounds' => $content, 'teams' => $teams];


		echo json_encode($data_cont);
	}



	public function get_ajax_org_round()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$event_id = $_POST['event_id'];
		$tournament_id =  $_POST['tournament_id'];
		$cities = $this->common->get_org_round($user_id, $event_id, $tournament_id);

		$content = '<option value="">Select Round</option>';
		if (isset($cities) && !empty($cities)) {
			foreach ($cities as $data) {
				$content .= '<option value="' . $data->round_id . '">' . $data->round_name . '</option>';
			}
		}
		$listing_event = $this->common->select('listing_events', '*', array('listing_event_id' => $event_id), false);
		if ($listing_event->listing_event_type == 'individual') {
			$team_event = $this->common->select('participants', '*', array('participant_event_id' => $event_id, 'participant_list_id' => $tournament_id), true);
			$teams = '<option value="">Select Player</option>';
			if (isset($team_event) && !empty($team_event)) {
				foreach ($team_event as $data) {
					$loss = $this->common->select('matches', '*', ['loss' => $data->participant_id], true);
					if (!empty($loss)) {
						$teams .= '<option value="' . $data->participant_id . '" disabled style="background-color:red;color:white">' . $data->participant_name . '</option>';
					} else {
						$teams .= '<option value="' . $data->participant_id . '">' . $data->participant_name . '</option>';
					}
				}
			}
		} else {

			$team_event = $this->common->select('teams', '*', array('team_event' => $event_id, 'team_tournament' => $tournament_id), true);
			$teams = '<option value="">Select Player</option>';
			if (isset($team_event) && !empty($team_event)) {
				foreach ($team_event as $data) {
					$loss = $this->common->select('matches', '*', ['loss' => $data->team_id], true);
					if (!empty($loss)) {
						$teams .= '<option value="' . $data->team_id . '" disabled style="background-color:red;color:white">' . $data->team_name . ' </option>';
					} else {
						$teams .= '<option value="' . $data->team_id . '">' . $data->team_name . '</option>';
					}
				}
			}
		}


		$data_cont = ['rounds' => $content, 'teams' => $teams];


		echo json_encode($data_cont);
	}



	public function get_teams()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$event_id = $this->request->getPost('event_id');
		$group_id = $this->request->getPost('group_id');
		$teams = $this->common->select('teams', '*', array('team_event' => $event_id, 'team_group' => $group_id), true, false, false, true);
		echo json_encode($teams);
	}



	public function get_ajax_org_team()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$match_id = $this->request->getPost('match_id');

		$match = $this->common->select('matches', '*', array('match_id' => $match_id));
		$event_id = $match->match_event;
		$match_team1_id = $match->match_team1_id;
		$match_team2_id = $match->match_team2_id;
		$team_event = $this->common->select('teams', '*', array('team_event' => $event_id), true);
		$team1 = '<option value="">Select Player</option>';
		if (isset($team_event) && !empty($team_event)) {
			foreach ($team_event as $data) {
				$selected = ($data->team_id == $match_team1_id) ? 'selected' : '';
				$team1 .= '<option value="' . $data->team_id . '" ' . $selected . '>' . $data->team_name . '</option>';
			}
		}

		$team_event = $this->common->select('teams', '*', array('team_event' => $event_id), true);
		$team2 = '<option value="">Select Player</option>';
		if (isset($team_event) && !empty($team_event)) {
			foreach ($team_event as $data) {
				$selected = ($data->team_id == $match_team2_id) ? 'selected' : '';
				$team2 .= '<option value="' . $data->team_id . '" ' . $selected . '>' . $data->team_name . '</option>';
			}
		}

		$data_cont = ['team1' => $team1, 'team2' => $team2];


		echo json_encode($data_cont);
	}

	/* -----------------------------------------------------------------------------------*/
	public function umpire_list()
	{
		$data['page_name']     = 'Manage Umpires';
		$data['page_title']    = 'Umpires';
		$data['page_subtitle'] = 'Add and manage umpires for your tournaments';
		$user_id = session()->get('user_id');
		$search = $this->request->getGET('search') ?? '';
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['commonModel'] = $this->common;

		$item_count = $this->common->count_umpire_list($user_id);

		$data['total'] = $item_count;
		$per_page = 25;
		$page = $this->request->getGET('page');
		$offset = ($page * $per_page) - $per_page;
		if (empty($page)) {
			$page = 1;
			$offset = 0;
		}
		$data['page'] = $page;
		$data['perPage'] = $per_page;
		$data['umpire_list'] = $this->common->get_umpire_list($per_page, $offset, $user_id, $search);

		echo view('organizer/umpire_list', $data);
	}

	public function add_umpire()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['page_name']     = 'Add Umpire';
		$data['page_title']    = 'Add Umpire';
		$data['page_subtitle'] = 'Register a new umpire';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['cities'] = $this->common->select('cities', '*', array('city_active' => 1), true);

		echo view('organizer/add_umpire', $data);
	}

	public function edit_umpire($umpire_id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Edit Umpire';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$umpire = $this->common->select('users', '*', array('user_id' => $umpire_id, 'user_role_id' => 4));
		if (empty($umpire)) {
			return redirect()->to(base_url('organizer'));
		}
		$data['umpire'] = $umpire;

		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['cities'] = $this->common->select('cities', '*', array('city_active' => 1), true);

		echo view('organizer/add_umpire', $data);
	}

	/**
	 * Create a new account
	 */
	public function attempt_add_umpire()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$organizer_id = session()->get('user_id');
		$response = [];
		$rules = [
			'name'    => ['label' => 'Name', 'rules' => 'required|min_length[2]'],
			'mobile'  => ['label' => 'Mobile', 'rules' => 'required|min_length[10]'],
			'image' => ['rules' => 'permit_empty|uploaded[image]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]'],
		];

		$id = trim($this->request->getPost('id'));
		if (empty($id)) {
			$rules['password'] = [
				'label' => 'Password',
				'rules' => 'required|min_length[8]|max_length[20]',
				'errors' => [
					'regex_match' => 'Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character (!@#$%^&*).'
				]
			];
			$rules['confirm_password'] = [
				'label' => 'Confirm Password',
				'rules' => 'required|matches[password]',
				'errors' => [
					'matches' => 'Confirm Password must match the Password.'
				]
			];
		}

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$id = trim($this->request->getPost('id'));
		$password = $this->request->getPost('password');
		$date = date('Y-m-d H:i:s');
		$name = $this->request->getPost('name');
		$mobile = trim($this->request->getPost('mobile'));

		if (!empty($id)) {
			$check_where = array(
				'user_mobile' => $mobile,
				'user_id!=' => $id
			);
		} else {
			$check_where = array(
				'user_mobile' => $mobile,
			);
		}

		$staff_check = $this->common->select('users', '*', $check_where);

		if (! empty($staff_check)) {
			return redirect()->back()->withInput()->with('error', 'Mobile Already Exist');
		}

		$pass = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : '';
		$otp = get_unique_code('numeric', 6);
		$username = get_unique_code('alnum', 8);

		$data = array(
			'user_name' => $username,
			'user_full_name' => xss_clean($name),
			'user_mobile' => xss_clean($mobile),
			'user_organizer_id' => xss_clean($organizer_id),
			'user_role_id' => 4,
			'user_reset_hash' => $otp,
			'user_active' => 1
		);
		$imageFile = $this->request->getFile('image');
		if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
			$newImageName = time() . '_' . $imageFile->getRandomName();
			$imageFile->move('public/uploads/umpires/', $newImageName);
			$data['user_profile_img'] = $newImageName;
		}

		if (!empty($password)) {
			$data['user_password'] = $pass;
		}
		if (!empty($id)) {
			$data['user_updated_at'] = $date;
			$result = $this->common->update_data('users', $data, array('user_id' => $id));
			$msg = 'Upmire updated successfully';
		} else {
			$data['user_created_at'] = $date;
			$result = $this->common->insert_data('users', $data);
			$msg = 'Upmire added successfully';
		}
		if ($result) {
			return redirect()->to(base_url('organizer/umpire_list'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}
	public function player($id)
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['userdata'] = $this->common->get_player($id);
		echo view('organizer/player', $data);
	}
	public function player_name()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}
		$id = $this->request->getPost('tournament');
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['userdata'] = $this->common->get_player($id);
		echo view('organizer/player', $data);
	}

	public function participants()
	{
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Teams';

		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$page = $this->request->getGet('page') ?? 1;
		$limit = 10;
		$offset = ($page - 1) * $limit;

		$total_items = $this->common->count_player_list($user_id);

		$data['userdata'] = $this->common->get_player_list($limit, $offset, $user_id);

		$data['pager'] = [
			'current_page' => $page,
			'total_items' => $total_items,
			'per_page' => $limit,
			'total_pages' => ceil($total_items / $limit),
			'base_url' => base_url('organizer/participants') // Change to your route
		];

		echo view('participants', $data);
	}

	public function activate($user_id)
	{
		$data['users'] = $this->common->get_update('participants', $user_id);
		return redirect()->back()->with('success_message', 'Pay status updated successfully');
	}

	public function participant_detail()
	{
		$tournament = $this->request->getPost('tournament');

		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Teams';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['userdata'] = $this->common->get_player_list1($tournament);

		echo view('participants', $data);
	}
	public function get_ajax_org_teams()
	{
		$tourney_id = $this->request->getPost('tourney_id');
		$event_id = $this->request->getPost('event_id');

		$team_event = $this->common->select('teams', '*', [
			'team_event' => $event_id,
			'team_tournament' => $tourney_id
		], true);

		$options = '<option value="">Select Team</option>';
		if (!empty($team_event)) {
			foreach ($team_event as $data) {
				$options .= '<option value="' . $data->team_id . '">' . ucfirst($data->team_name) . '</option>';
			}
		}

		echo $options;
		exit;
	}
}
