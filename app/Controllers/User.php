<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CommonModel;
use App\Libraries\RestLibrary;
class User extends Controller
{
	protected $rest;
	protected $common;
	protected $booking;
	public function __construct()
	{
		helper(['form','url','custom']);
		$this->rest = new RestLibrary();
		$this->common = new CommonModel();
	}

	public function index()
	{
		
		if(session()->has('logged_in')){
			if(session()->get('role_id') == 1){
				return redirect()->to(base_url('admin'));
			}elseif (session()->get('role_id') == 2) {
				return redirect()->to(base_url('user'));
			}elseif (session()->get('role_id') == 3) {
				return redirect()->to(base_url('organizer'));
			}
			
		}
		$data=[];
		echo view('user/sign_in',$data);
	}

	public function attemptSignin()
	{
		$rules = [
			'mobile'   => ['label' => 'Mobile',   'rules' => 'required|min_length[5]'],
			'password' => ['label' => 'Password', 'rules' => 'required'],
		];
		
		if (! $this->validate($rules))
		{
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$mobile = trim($this->request->getPost('mobile'));
		$password = trim($this->request->getPost('password'));

		$role = trim($this->request->getPost('role'));


		$user = $this->common->select('users', '*', array('user_mobile' => $mobile,'user_role_id'=> $role));
        if (! $user)
        {
            return redirect()->back()->withInput()->with('error', 'Authentication Failed');
        }

        // if($user->user_role_id != 3){
        // 	if($user->user_active == 0){
        // 		return redirect()->back()->withInput()->with('error', 'Account is not active.');
        // 	}
        // }
        
        if ($user->user_active == 0)
        {
            return redirect()->back()->withInput()->with('error', 'Account is not active.');
        }


        // Now, try matching the passwords.
        if (! password_verify($password, $user->user_password))
        {
        	return redirect()->back()->withInput()->with('error', 'Invalid Password');
        }
        $remember = trim($this->request->getPost('remember') ?? '');
        if(!empty($remember)){
        	setcookie('rnavaum',$mobile,time()+3600*24*7);
        	setcookie('rpanaivelam',$password,time()+3600*24*7);
        }else{
        	setcookie('rnavaum',$mobile,10);
        	setcookie('rpanaivelam',$password,10);
        }
        $newdata = [
	    		'user_id' => $user->user_id,
			    'username' => $user->user_name,
			    'email' => $user->user_email,
			    'mobile' => $user->user_mobile,
			    'address' => $user->user_address,
			    'role_id' => $user->user_role_id,
			    'user_active' => $user->user_active,
			    'logged_in' => true
			];

			session()->set($newdata);
			
        if($user->user_role_id == 1){
			return redirect()->to('admin');
        }elseif($user->user_role_id == 2){
            return redirect()->to('organizer/dashboard');

        }elseif($user->user_role_id == 3){
        	return redirect()->to('organizer/dashboard');
        }else{
        	return redirect()->back()->withInput()->with('error', 'User not a admin');
        }

    	
	}

	public function signup()
	{
		if(session()->has('logged_in')){
			if(session()->get('role_id') == 1){
				return redirect()->to(base_url('admin'));
			}elseif (session()->get('role_id') == 2) {
				return redirect()->to(base_url());
			}elseif (session()->get('role_id') == 3) {
				return redirect()->to(base_url('organizer'));
			}
			
		}
		$data['user_reg'] = TRUE;
		$data['countries'] = $this->common->select('countries','*',array('country_active'=>1),true);
		echo view('user/sign_up',$data);
	}

	/**
     * Create a new account
     */
  public function attempt_sign_up()
{
    $response = [];

    // Validate basics first since some password rules rely on these fields
    $rules = [
        'name' => ['label' => 'Name', 'rules' => 'required|min_length[2]'],
        'email' => [
            'label' => 'Email', 
            'rules' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.user_email]',
            'errors' => ['is_unique' => 'Email already exist']
        ],
        'country_code' => ['label' => 'Mobile country code', 'rules' => 'required'],
        'mobile' => [
            'label' => 'Mobile', 
            'rules' => 'required|min_length[10]|is_unique[users.user_mobile]',
            'errors' => ['is_unique' => 'Mobile number already exist']
        ],
        'password' => ['label' => 'Password', 'rules' => 'required|strong_password'],
        'confirm_password' => ['label' => 'Confirm Password', 'rules' => 'required|matches[password]'],
        'g-recaptcha-response' => ['label' => 'reCaptcha', 'rules' => 'required']
    ];

    if (! $this->validate($rules)) {
        $response = [
            'validationError' => true,
            'messages' => $this->validator->getErrors()
        ];
    } else {
        $username = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $mobile = $this->request->getPost('mobile');
        $password = $this->request->getPost('password');
        $country_code = xss_clean($this->request->getPost('country_code'));
        $pass = password_hash($password, PASSWORD_DEFAULT);
        $otp = get_unique_code('numeric',6);

        // Base data array for participant
        $data = array(
            'user_name' => xss_clean($username),  
            'user_email' => xss_clean($email), 
            'user_mobile_code' => $country_code,
            'user_mobile' => xss_clean($mobile), 
            'user_password' => $pass,
            'user_role_id' => 2,
            'user_reset_hash' => $otp,
            'user_created_at' => date('Y-m-d H:i:s')
        );

        // Only add city and address if they exist (for organizer)
        $city = $this->request->getPost('city');
        $address = $this->request->getPost('address');
        
        if (!empty($city)) {
            $data['user_city_id'] = xss_clean($city);
        }
        
        if (!empty($address)) {
            $data['user_address'] = xss_clean($address);
        }

        $result = $this->common->insert_data('users', $data);
        
        if(!empty($result)) {
            // Prepare SMS message
            $subject = 'Account activation OTP';
            $message = "Your OTP for Reset password is $otp. Please do not share this OTP.";
            $templateId = "1407168862906996721";
    
            $url = "http://sms.spiderindia.com/api/smsapi?key=76a4a331953994b26514dbee1a9b275c&route=2&sender=INSTNE&number=" . $mobile . "&templateid=" . $templateId . "&sms=" . urlencode($message);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);
            
            $output = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            $response = [
                'success' => true,
                'message' => 'OTP sent successfully',
                'redirectURL' => base_url('login')
            ];
        } else {
            $response = [
                'error' => true,
                'message' => lang('Auth.unknownError')
            ];
        }
    }

    echo json_encode($response);
    die();
}

	
	/**
	 * Activate account.
	 *
	 * @return mixed
	 */
	public function account_activation()
	{
		$mobile = $this->request->getPost('mob');
		$otp = $this->request->getPost('code');
		$condition = array('user_mobile'=>$mobile);
		$user = $this->common->select('users','*', $condition);

		if (!empty($user)) {
			$data = [
				'user_active' => 1,
				'user_updated_at' => date('Y-m-d H:i:s'),
				'user_reset_hash' => null,
			];
			if($otp == $user->user_reset_hash) {
				$result = $this->common->update_data('users', $data, $condition);
				if($result){
					session()->set('success_message','Account activated successfully. Please login with new credentials.');
					echo 'success';
				} else {
					echo 'fail';
				}
			}
		} else {
			echo 'fail';
		}
	}

	public function doLogout()
	{
		$session =  session();
        $session->destroy(); // destroy session variables and values
        return redirect()->to(base_url());
	}

	public function admin_mobile()
	{
		$mobile = trim($this->request->getPost('mobile'));
		$condition = array('user_mobile'=>$mobile);
		$user = $this->common->select('users','*', $condition);
		$otp = get_unique_code('numeric',6);
	
			if (!empty($user)) {
				$data = [
					'user_reset_hash' => $otp,
					'user_updated_at' => date('Y-m-d H:i:s'),
					'user_reset_expires' => date('Y-m-d H:i:s', strtotime('+60 minutes')),
				];
				$result = $this->common->update_data('users', $data, $condition);

				$subject = 'Account activation OTP';
				$message = "Your OTP for Reset password is $otp. Please do not share this OTP.";
				// API details
				$templateId = "1407168862906996721";
		
				$url = "http://sms.spiderindia.com/api/smsapi?key=76a4a331953994b26514dbee1a9b275c&route=2&sender=INSTNE&number=" . $mobile . "&templateid=" . $templateId . "&sms=" . urlencode($message);

				$curl = curl_init();
				curl_setopt_array($curl, [
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_TIMEOUT => 10,
					CURLOPT_CUSTOMREQUEST => 'GET',
				]);
				
				$response = curl_exec($curl);
				curl_close($curl);
				$response = true;
			if($response) {
				echo 'success';die();
			} else {
				echo 'fail';die();
			}
		} else {
			echo 'fail';die();
		}
	}

	public function forgot_password_email()
	{
		$email = trim((string) $this->request->getPost('email'));
		if ($email === '') {
			echo 'fail'; die();
		}

		$user = $this->common->select('users', '*', array('user_email' => $email));
		if (empty($user)) {
			echo 'fail'; die();
		}

		$otp = get_unique_code('numeric', 6);
		$data = [
			'user_reset_hash'   => $otp,
			'user_updated_at'   => date('Y-m-d H:i:s'),
			'user_reset_expires' => date('Y-m-d H:i:s', strtotime('+60 minutes')),
		];
		$this->common->update_data('users', $data, array('user_id' => $user->user_id));

		$mailData = [
			'user'     => $user->user_name,
			'otp_code' => $otp,
		];
		$message = view('book/email', $mailData);

		$emailService = \Config\Services::email();
		$emailService->setTo($user->user_email);
		$emailService->setSubject('OTP Verification');
		$emailService->setMessage($message);

		if ($emailService->send()) {
			echo 'success'; die();
		}
		echo 'fail'; die();
	}

	public function forgot_password()
	{
		$data=[];
		$data['countries'] = $this->common->select('countries','*',array('country_active'=>1),true);
		
		echo view('user/forgot_password',$data);
	}

	/**
	 * Verifies the code with the email and saves the new password,
	 * if they all pass validation.
	 *
	 * @return mixed
	 */
	public function attempt_reset_password()
	{

		$email = $this->request->getPost('email') !== null ? $this->request->getPost('email') : $this->request->getPost('mob');
		$condition = array('user_email' => $email);
		$user = $this->common->select('users','*', $condition);

		if (empty($user))
		{
			return redirect()->back()->with('error', 'Unable to locate a user with that email.');
		}

		$current_time = date('Y-m-d H:i:s');
		$otp = $this->request->getPost('otp'); // Assuming the OTP is being sent via a POST request

		if (($user->user_reset_expires && $user->user_reset_expires < $current_time) || $user->user_reset_hash !== $otp) {
			return redirect()->back()->with('error', 'Invalid or expired OTP. Please try again.');
		}
		// Success! Save the new password, and cleanup the reset hash.
		$password = $this->request->getPost('pass');
		$pass = password_hash($password, PASSWORD_DEFAULT);

		$data = [
			'user_password' => $pass,
			'user_reset_hash' => null,
			'user_reset_expires' => null,
			'user_reset_at' => date('Y-m-d H:i:s'),
			'user_updated_at' => date('Y-m-d H:i:s')
		];
		
 		$result = $this->common->update_data('users',$data, $condition);
 		if($result){
 			session()->set('success_message','Password changed successfully. Please login with new password');
 			echo 'success';
 		}else{
 			echo 'fail';
 		}
		
	}

	public function profile()
	{
		if(! session()->has('logged_in')){
			return redirect()->to(base_url());
		}
		$id = session()->get('user_id');
		$data = [];
		$data['header'] = TRUE;
        $data['footer'] = TRUE;
        $contact_us= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
		if(!empty($contact_us)){
			$data['contact_us'] = json_decode($contact_us->settings_value);
		}
        $data['famous_region'] = $this->common->select('cities','*',array('city_active'=>1),true);
		$data['users'] = $this->common->select('users','*',array('user_id'=>$id));
		//print_array($data['users']);die();
		echo view('user/profile',$data);
	}

	public function attempt_profile()
	{
	    		session()->remove('errors');

		$rules = [
			'username'	=> ['label' => 'Name', 'rules' =>'required|min_length[3]'],
'mobile' => [
    'label' => 'Mobile',
    'rules' => 'required|regex_match[/^[6-9][0-9]{9}$/]',
],
		    'email' => ['label' => 'Email', 'rules' =>'required|valid_email'],
			'address' => ['label' => 'Address', 'rules' =>'required|min_length[10]']   
		];
		if(session()->get('role_id') == 3){
			$rules['organization_name'] = ['label' => 'Organization Name', 'rules' =>'required|min_length[3]'];
		}

		if (! $this->validate($rules))
		{
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
			$id = session()->get('user_id');
		    $username = $this->request->getVar('username');
			$mobile = $this->request->getVar('mobile');
			$email = $this->request->getVar('email');
			$password = $this->request->getVar('password');
			$address = addslashes($this->request->getVar('address'));

			$mobile_exists = $this->common->select('users','*',array('user_mobile'=>$mobile,'user_id !='=>$id));
			if(!empty($mobile_exists)){
				return redirect()->back()->withInput()->with('error', 'Mobile number already exists');
			}
			$email_exists = $this->common->select('users','*',array('user_email'=>$email,'user_id !='=>$id));
			if(!empty($email_exists)){
				return redirect()->back()->withInput()->with('error', 'Email already exists');
			}
			//$pass = password_hash($password, PASSWORD_DEFAULT);

	        $data = array(
		        'user_name' => xss_clean($username),  
		        'user_full_name' => xss_clean($username),  
                'user_mobile' => xss_clean($mobile), 
                'user_email' => xss_clean($email), 
                //'password' => $pass,
                'user_address' => xss_clean($address),
				'user_updated_at' => date('Y-m-d H:i:s')
            );
            if(session()->get('role_id') == 3){
				$data['user_organization_name'] = xss_clean($this->request->getVar('organization_name'));
			}

			if(!empty($this->request->getFile('image'))){
				$folderpath = FCPATH.'public/uploads/images/profile/'.$user_id;
				$imageFile=$this->request->getFile('image');
				if ($imageFile->isValid() && ! $imageFile->hasMoved()) {
					$filename = $imageFile->getRandomName();
	            	$imageFile->move($folderpath, $filename);
	            	$data['user_profile_img']=$filename;
				}
			}

		$result = $this->common->update_data('users', $data, array('user_id' => $id));
		$user = $this->common->select('users','*', array('user_id' => $id));
		$newdata = [
	    		'user_id' => $user->user_id,
			    'username' => $user->user_name,
			    'email' => $user->user_email,
			    'mobile' => $user->user_mobile,
			    'address' => $user->user_address,
			    'role_id' => $user->user_role_id,
			    'logged_in' => true
			];

			session()->set($newdata);
        if($result){
        	return redirect()->to(base_url('profile'))->with('success_message', 'Profile updated successfully!');
        }else{
        	return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
        }
	}

	public function change_password()
	{
		if(!session()->has('logged_in')){
			return redirect()->to(base_url());
		}
		
		$data = [];
		$data['header'] = TRUE;
        $data['footer'] = TRUE;
        $contact_us= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
		if(!empty($contact_us)){
			$data['contact_us'] = json_decode($contact_us->settings_value);
		}
        $data['famous_region'] = $this->common->select('cities','*',array('city_active'=>1),true);
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users','*',array('user_id'=>$user_id));
		//print_array($data['users']);die();
		echo view('user/change_password',$data);
	}

	public function attempt_change_password()
	{
		$rules = [
			'current_password'	=> ['label' => 'Current Password', 'rules' =>'required|min_length[6]|trim'],
		    'password' => ['label' => 'New Password', 'rules' =>'required|strong_password'],
		    'passconf' => ['label' => 'Confirm New Password', 'rules' =>'required|matches[password]'] 
		];
		

		if (! $this->validate($rules))
		{
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$id = session()->get('user_id');
        $condition = array('user_id' => $id);
        $user = $this->common->select('users','*', $condition);
        $oldpass = $this->request->getPost('current_password');
        if(!password_verify($oldpass, $user->user_password)) {
            return redirect()->back()->withInput()->with('error', 'Current password is wrong');
        }

        $password = $this->request->getPost('password');
        $newpass = password_hash($password, PASSWORD_DEFAULT);
        $update_data = array('user_password' => $newpass);
        $condition = array('user_id' => $id);
        
        try {

		  $this->common->update_data('users',$update_data,$condition);

		  return redirect()->back()->withInput()->with('success_message', 'Password changed successfully!');

		}catch(Exception $e) {

		  $data['error_message'] = "Something went wrong. Please try again";

		}
	}

	//==================For api====================

	public function create_account()
	{
		
		
		$get_city = $this->rest->get('get_city');
		if( $get_city->message == 'Success'){
			$data['city'] = $get_city->data;
		}
		$location = $this->rest->get('get_location_by_cityid/1');
		if( $location->message == 'Success'){
			$data['location'] = $location->data;
		}
		
		echo view('user/sign_up',$data);
	}

	public function attemptSignup()
	{
		$data = [
		 	'username' => $_POST['username'],
		 	'email' => $_POST['email'],
		 	'mobile' => $_POST['mobile'],
		 	'password' => $_POST['password'],
		 	'city' => $_POST['city'],
		 	'area' => $_POST['area'],
		 	'city' => $_POST['city'],
		 	'address' => $_POST['address'],
		 	'fcm_id' => 'abdd989899989dddgg'
		 ];
		
		 $result = $this->rest->post('signup',$data);
		 echo json_encode($result);

    	
	}

	public function my_payments()
	{
		if(! session()->has('logged_in')){
			return redirect()->to(base_url());
		}
		$id = session()->get('user_id');
		$data = [];
		$data['header'] = TRUE;
        $data['footer'] = TRUE;
        $contact_us= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
		if(!empty($contact_us)){
			$data['contact_us'] = json_decode($contact_us->settings_value);
		}
		$data['famous_region'] = $this->common->select('cities','*',array('city_active'=>1),true);
		$data['commonModel'] = $this->common;
		$data['users'] = $this->common->select('users','*',array('user_id'=>$id));
		$data['payments'] = $this->common->select('payment_details','*',array('payment_user_id'=>$id,'payment_active'=>1),true,'payment_id desc');
		//print_array($data['users']);die();
		echo view('user/my_payments',$data);
	}

	public function booking_history()
	{
		if(! session()->has('logged_in')){
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$data = [];
		$data['header'] = TRUE;
        $data['footer'] = TRUE;
        $contact_us= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
		if(!empty($contact_us)){
			$data['contact_us'] = json_decode($contact_us->settings_value);
		}
		$data['famous_region'] = $this->common->select('cities','*',array('city_active'=>1),true);
		$data['commonModel'] = $this->common;
		$data['users'] = $this->common->select('users','*',array('user_id'=>$user_id));
		//$data['payments'] = $this->common->select('payment_details','*',array('payment_user_id'=>$id,'payment_active'=>1),true,'payment_id desc');

		$item_count = $this->common->count_booking_history($user_id);
        //echo $item_count;exit;
        $data['total'] = $item_count;
        $per_page = 10;
        $page = $this->request->getGET('page');
        $offset = ($page*$per_page)-$per_page;
        if(empty($page)){
            $page = 1;
            $offset = 0;
        }
        $data['page'] = $page;
        $data['perPage'] = $per_page;
		$data['bookings'] = $this->common->get_booking_history($per_page, $offset, $user_id);
		//print_array($data['users']);die();
		echo view('user/booking_history',$data);
	}

}