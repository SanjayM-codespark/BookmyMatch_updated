<?php
namespace App\API\r1;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ReqresTrait;
use App\Models\CommonModel;

class Account extends ResourceController
{
    use ReqresTrait;
    protected $common;
	public function __construct()
	{
		helper(['form','url','custom']);
		$this->common = new CommonModel();
	}
   
    /**
     * Create a new account
     */
    public function create_account()
    {
    	$response = [];
    	$rules = [
			'full_name'	=> ['label' => 'Full Name', 'rules' =>'required'],
			'email' => [
				'label' => 'Email', 
				'rules' =>'required|min_length[6]|max_length[50]|valid_email|is_unique[users.user_email]',
				'errors' => ['is_unique' => 'Email already exist']
		    ],
		    'country_code' => ['label' => 'Mobile country code', 'rules' =>'required'],
		    'mobile' => [
				'label' => 'Mobile', 
				'rules' =>'required|min_length[10]|is_unique[users.user_mobile]',
				'errors' => ['is_unique' => 'Mobile number already exist']
		    ],
			'password' => ['label' => 'Password', 'rules' =>'required|strong_password'],
			/*'city' => ['label' => 'City', 'rules' =>'required|numeric'],
			'area' => ['label' => 'Area', 'rules' =>'required|numeric'],
			'address' => ['label' => 'Address', 'rules' =>'required|min_length[10]'],*/
			'fcm_id' => ['label' => 'FCM ID', 'rules' =>'required|min_length[5]']
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

			$full_name = xss_clean($input['full_name']);
			$email = xss_clean($input['email']);
			$mobile = xss_clean($input['mobile']);
			$password = xss_clean($input['password']);
			/*$city = xss_clean($input['city']);
			$area = xss_clean($input['area']);*/
			$address = !empty($input['address'])?xss_clean($input['address']):'';
			$fcm_id = xss_clean($input['fcm_id']);
			$country_code = xss_clean($input['country_code']);
			$pass = password_hash($password, PASSWORD_DEFAULT);
			$otp = get_unique_code('numeric',6);

	        $data = array(
                'user_name' => $full_name,
		        'user_full_name' => $full_name,  
		        'user_email' => $email, 
		        'user_mobile_code' => $country_code,
                'user_mobile' => $mobile,  
                'fcm_id' => $fcm_id, 
                'user_password' => $pass,
                /*'user_city_id' => $city,
                'user_area_id' => $area,*/
                                'user_active' => 1,
                'user_address' => $address,
                				'login_count' => 0,
                'user_role_id' => 2,
                'user_reset_hash' => $otp,
				'user_created_at' => date('Y-m-d H:i:s')
            );

	        $result = $this->common->insert_data('users', $data);
	        if($result){
				// Prepare SMS message
				$subject = 'Account activation OTP';
				// Prepare message text for OTP
				$messagetext = "Your OTP for Book My Match login is $otp. Please do not share this OTP.";
				$template_id = "1407168862906996721";
				$messagetext = urlencode($messagetext);

				// Send OTP via API (SMS Gateway)
				$url = "http://sms.spiderindia.com/api/smsapi?key=76a4a331953994b26514dbee1a9b275c&route=2&sender=INSTNE&number=" . $mobile . "&templateid=" . $template_id . "&sms=" . $messagetext;

				// Send OTP using cURL
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

	        	$login_data = $this->common->select('users','*',array('user_id' => $result));
		        unset($login_data->user_password);     		
	            $response = [
	            	'status' => true,
	                'message' => 'User added successfully',
	                'data' => $login_data
	            ];
	        }else{
	        	$response = [
	            	'status' => false,
	                'message' => 'Registration fail. Try again'
	            ];
	        }
	    }

	    return $this->getResponse($response);

    }


    public function sign_in()
    {
    	$response = [];
    	$rules = [
			'mobile' => ['label' => 'Mobile', 'rules' =>'required|min_length[10]'],
			'password' => ['label' => 'Password', 'rules' =>'required|min_length[6]'],
			'fcm_id' => ['label' => 'FCM ID', 'rules' =>'required']
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

			$mobile = xss_clean($input['mobile']);
			$password = xss_clean($input['password']);
			$fcm_id = xss_clean($input['fcm_id']);

			$mobile_check = $this->common->select('users','user_id,user_mobile,user_email,user_password,user_status,user_active' ,  array('user_mobile' => $mobile));
		    $this->common->update_data1("users", array('login_count' => 1), array('user_mobile' => $mobile), true);

			if(empty($mobile_check)){

				$response = [
	            	'status' => false,
	                'message' => 'Account not found. Please check your credentials'
	            ];
				
			}else{

				if($mobile_check->user_status == 'banned'){

					$response = [
		            	'status' => false,
		            	'account_status'=>'banned',
		                'message' => 'Account is banned by admin'
		            ];
		            return $this->getResponse($response);

				}

				if(empty($mobile_check->user_active)){

					$response = [
		            	'status' => false,
		            	'account_status'=>'inactive',
		                'message' => 'Account is inactive. Kindly verify your mobile number'
		            ];

				}else{

					$user_id = $mobile_check->user_id;
					$pass = $mobile_check->user_password;

	                $pass_check = password_verify($password, $pass);

	                if(!empty($pass_check)){
	                	$update_date = date('Y-m-d H:i:s');
	                	$result = $this->common->update_data("users", array('fcm_id' => $fcm_id,'user_updated_at'=>$update_date), array('user_id' => $user_id));

	                	if(!empty($result)){

	                		$login_data = $this->common->select('users','*',array('user_id' => $user_id));
	                		unset($login_data->user_password);
							$login_data->full_profile_image = base_url() . '/public/uploads/images/profile/' . $login_data->user_id . '/' . $login_data->user_profile_img;
	                        //$fcm_id = $this->generateBeamsToken($user_id);
							$result = $this->common->update_data("users", array('fcm_id' => $fcm_id), array('user_id' => $user_id));
	                		$response = [
				            	'status' => true,
				                'message' => 'Successfully logged in',
				                'data' => $login_data
				            ];

	                	}else{
	                		$response = [
				            	'status' => false,
				                'message' => 'Something went wrong, try again'
				            ];
	                	}

	                }else{

	                	$response = [
			            	'status' => false,
			                'message' => 'Password is wrong'
			            ];

	                }
				}
				
			}
		}

		return $this->getResponse($response);

    }
 public function generateBeamsToken($userId)
    {
        $key = "7530CB90E01C4F2A5FEA1DC15CEFA82124009ED18EDF94236E3A70F496609CDA";
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(['user_id' => $userId, 'exp' => time() + 7200]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    }
    public function umpire_sign_in()
    {
    	$response = [];
    	$rules = [
			'mobile' => ['label' => 'Mobile', 'rules' =>'required|min_length[10]'],
			'password' => ['label' => 'Password', 'rules' =>'required|min_length[6]'],
			'fcm_id' => ['label' => 'FCM ID', 'rules' =>'required|min_length[5]']
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

			$mobile = xss_clean($input['mobile']);
			$password = xss_clean($input['password']);
			$fcm_id = xss_clean($input['fcm_id']);

			$mobile_check = $this->common->select('users', 'user_id,user_mobile,user_email,user_password,user_status,user_active', ['user_mobile' => $mobile, 'user_role_id' => 4]);

			if (!$mobile_check) {
				$mobile_check = $this->common->select('users', 'user_id,user_mobile,user_email,user_password,user_status,user_active', ['user_mobile' => $mobile, 'user_role_id' => 2]);
			}

			if(empty($mobile_check)){

				$response = [
	            	'status' => false,
	                'message' => 'Account not found. Please check your credentials'
	            ];
				
			}else{

				if($mobile_check->user_status == 'banned'){

					$response = [
		            	'status' => false,
		            	'account_status'=>'banned',
		                'message' => 'Account is banned by admin'
		            ];
		            return $this->getResponse($response);

				}

				if(empty($mobile_check->user_active)){

					$response = [
		            	'status' => false,
		            	'account_status'=>'inactive',
		                'message' => 'Account is inactive. Kindly verify your mobile number'
		            ];

				}else{

					$user_id = $mobile_check->user_id;
					$pass = $mobile_check->user_password;

	                $pass_check = password_verify($password, $pass);

	                if(!empty($pass_check)){
	                	$update_date = date('Y-m-d H:i:s');
	                	$result = $this->common->update_data("users", array('fcm_id' => $fcm_id,'user_updated_at'=>$update_date), array('user_id' => $user_id));

	                	if(!empty($result)){

	                		$login_data = $this->common->select('users','*',array('user_id' => $user_id));
	                		unset($login_data->user_password);

	                		$response = [
				            	'status' => true,
				                'message' => 'Successfully logged in',
				                'data' => $login_data
				            ];

	                	}else{
	                		$response = [
				            	'status' => false,
				                'message' => 'Something went wrong, try again'
				            ];
	                	}

	                }else{

	                	$response = [
			            	'status' => false,
			                'message' => 'Password is wrong'
			            ];

	                }
				}
				
			}
		}

		return $this->getResponse($response);

    }

	public function account_verification()
	{
		$response = [];
    	$rules = [
			'mobile' => ['label' => 'Mobile', 'rules' =>'required|min_length[10]']
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

			$mobile = xss_clean($input['mobile']);
			//$otp = $input['otp'];

			$mobile_check = $this->common->select('users','*',array('user_mobile' => $mobile));

			if(empty($mobile_check)){

				$response = [
	            	'status' => false,
	                'message' => 'Mobile number not exist'
	            ];
				
			}else{

				if($mobile_check->user_active == '1'){
					$response = [
		            	'status' => false,
		                'message' => 'Account already verified'
		            ];
				}else{
					$user_data = $this->common->select('users','*',array('user_mobile' => $mobile));
					if(!empty($user_data)){
						$update_date = date('Y-m-d H:i:s');
						$result = $this->common->update_data("users", array('user_active'=>1,'user_reset_hash' => NULL,'user_updated_at'=>$update_date), array('user_mobile' => $mobile));

	                	if(!empty($result)){
	                		$login_data = $this->common->select('users','*',array('user_mobile' => $mobile,'user_active'=>'1'));
	                		unset($login_data->user_password);
	                		$response = [
				            	'status' => true,
				                'message' => 'Account verified Successfully',
				                'data' => $login_data
				            ];
	                	}else{
	                		$response = [
				            	'status' => false,
				                'message' => 'Account not verified'
				            ];
	                	}
					}else{
						$response = [
			            	'status' => false,
			                'message' => 'Account not exist'
			            ];
					}
				}
								
			}
		}

		return $this->getResponse($response);
	}


	public function account_verification_old()
	{
		$response = [];
    	$rules = [
			'mobile' => ['label' => 'Mobile', 'rules' =>'required|min_length[10]'],
			'otp' => ['label' => 'OTP', 'rules' =>'required|min_length[6]|numeric']
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

			$mobile = xss_clean($input['mobile']);
			$otp = $input['otp'];

			$mobile_check = $this->common->select('users','*',array('user_mobile' => $mobile));

			if(empty($mobile_check)){

				$response = [
	            	'status' => false,
	                'message' => 'Mobile number not exist'
	            ];
				
			}else{

				if($mobile_check->user_active == '1'){
					$response = [
		            	'status' => false,
		                'message' => 'Account already verified'
		            ];
				}else{
					$user_data = $this->common->select('users','*',array('user_mobile' => $mobile,'user_reset_hash'=>$otp));
					if(!empty($user_data)){
						$update_date = date('Y-m-d H:i:s');
						$result = $this->common->update_data("users", array('user_active'=>1,'user_reset_hash' => NULL,'user_updated_at'=>$update_date), array('user_mobile' => $mobile,'user_reset_hash'=>$otp));

	                	if(!empty($result)){
	                		$login_data = $this->common->select('users','*',array('user_mobile' => $mobile,'user_active'=>'1'));
	                		unset($login_data->user_password);
	                		$response = [
				            	'status' => true,
				                'message' => 'Account verified Successfully',
				                'data' => $login_data
				            ];
	                	}else{
	                		$response = [
				            	'status' => false,
				                'message' => 'Something went wrong, try again'
				            ];
	                	}
					}else{
						$response = [
			            	'status' => false,
			                'message' => 'OTP is invalid'
			            ];
					}
				}
								
			}
		}

		return $this->getResponse($response);
	}

	public function resend_account_verification()
	{
		$response = [];
		$throttler = service('throttler');

		if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false)
		{
			$response = [
            	'status' => false,
                'message' => 'Too many requests. Please wait 10 seconds'
            ];
		}else{
			$rules = [
				'mobile' => ['label' => 'Mobile', 'rules' =>'required|min_length[10]']
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
			    $mobile = xss_clean($input['mobile']);

				$user = $this->common->select('users','*',array('user_mobile' => $mobile,'user_active' => 0));
				if (empty($user))
				{
					$response = [
		            	'status' => false,
		                'message' => 'Unable to locate a user'
		            ];
					
				}else{
					$fcm_id = $user->user_fcm_id;
					$otp = get_unique_code('numeric',6);
					$update_date = date('Y-m-d H:i:s');

					$result = $this->common->update_data("users", array('user_reset_hash' => $otp,'user_updated_at'=>$update_date), array('user_mobile' => $mobile));
                	if(!empty($result)){
                		try{
                			$sub = 'Account activation OTP';
                			$msg = 'Use the OTP '.$otp.' to verify your account and login';
                			//$this->send_notification($fcm_id,$sub,$msg);
                			$response = [
				            	'status' => true,
				                'message' => 'OTP sent successfully'
				            ];
						}catch(Exception $e) {
						    $response = [
				            	'status' => false,
				                'message' => 'Something went wrong, try again'
				            ];
						}
                	}else{
                		$response = [
			            	'status' => false,
			                'message' => 'Something went wrong, try again'
			            ];
                	}

				}
			}
		}

		
		return $this->getResponse($response);
	}

// public function forgot_password()
// 	{
// 		$response = [];
// 		$rules = [
// 			'mobile' => [
// 				'label' => 'Mobile',
// 				'rules' => 'required|numeric|min_length[10]|max_length[13]'
// 			]
// 		];

// 		if (! $this->validate($rules)) {
// 			$response = [
// 				'status' => false,
// 				'message' => 'Validation error',
// 				'data' => $this->validator->getErrors()
// 			];
// 		} else {
// 			$input = $this->getRequestInput($this->request);
// 			$mobile = xss_clean($input['mobile']);

// 			$user = $this->common->select('users', '*', ['user_mobile' => $mobile]);

// 			if (!empty($user)) {

// 				$otp = rand(100000, 999999);

// 				$update = ['user_reset_hash' => $otp];
// 				$this->common->update_data('users', $update, ['user_id' => $user->user_id]);
// 				$data['user'] = $user->user_name;
// 				$email = $user->user_email;
// 				$data['otp_code'] = $otp;
// 				$message = view('book/email', $data);

// 				$emailService = \Config\Services::email();
// 				$emailService->setTo($email);
// 				$emailService->setSubject('OTP Verification');
// 				$emailService->setMessage($message);
// 				if ($emailService->send()) {
// 					$response = [
// 						'status' => true,
// 						'message' => 'OTP sent to your email successfully',
// 						'otp' => $otp,
// 						'user_id' => $user->user_id,
// 					];
// 				} else {
// 					$response = [
// 						'status' => false,
// 						'message' => 'Failed to send OTP. Please try again later.'
// 					];
// 				}
// 			} else {
// 				$response = [
// 					'status' => false,
// 					'message' => 'Mobile number not registered'
// 				];
// 			}
// 		}

// 		return $this->getResponse($response);
// 	}
	
public function forgot_password()
	{
		$response = [];
		$rules = [
			'email' => [
				'label' => 'Email',
				'rules' => 'required'
			]
		];

		if (! $this->validate($rules)) {
			$response = [
				'status' => false,
				'message' => 'Validation error',
				'data' => $this->validator->getErrors()
			];
		} else {
			$input = $this->getRequestInput($this->request);
			$email = xss_clean($input['email']);

			$user = $this->common->select('users', '*', ['user_email' => $email]);

			if (!empty($user)) {

				$otp = rand(100000, 999999);

				$update = ['user_reset_hash' => $otp];
				$this->common->update_data('users', $update, ['user_id' => $user->user_id]);
				$data['user'] = $user->user_name;
				$email = $user->user_email;
				$data['otp_code'] = $otp;
				$message = view('book/email', $data);

				$emailService = \Config\Services::email();
				$emailService->setTo($email);
				$emailService->setSubject('OTP Verification');
				$emailService->setMessage($message);
				if ($emailService->send()) {
					$response = [
						'status' => true,
						'message' => 'OTP sent to your email successfully',
						'otp' => $otp,
						'user_id' => $user->user_id,
					];
				} else {
					$response = [
						'status' => false,
						'message' => 'Failed to send OTP. Please try again later.'
					];
				}
			} else {
				$response = [
					'status' => false,
					'message' => 'Mobile number not registered'
				];
			}
		}

		return $this->getResponse($response);
	}

	public function forgot_password_old()
	{
		$response = [];
    	$rules = [
			'mobile' => ['label' => 'Mobile', 'rules' =>'required|numeric|min_length[10]|max_length[13]']
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
			$mobile = xss_clean($input['mobile']);
			$user = $this->common->select('users','*', array('user_mobile' =>$mobile));

			if (!empty($user))
			{
				
				$fcm_id = $user->user_fcm_id;
				$otp = get_unique_code('numeric',6);
				$update_date = date('Y-m-d H:i:s');
				$result = $this->common->update_data("users", array('user_reset_hash' => $otp,'user_updated_at'=>$update_date), array('user_mobile' => $mobile));
				try{
        			$sub = 'Forgot Password OTP';
        			$msg = $otp.' is forgot password verification code';
        			//$this->notification->send($fcm_id,$sub,$msg);
        			$response = [
		            	'status' => true,
		                'message' => 'OTP sent successfully'
		            ];
				}catch (\Exception $e) {
				    $response = [
		            	'status' => false,
		                'message' => 'Something went wrong, try again'
		            ];
				}
			}else{
				$response = [
					'error' => true,
					'message' => 'Mobile number does not exist'
				];

			}
		}
		return $this->getResponse($response);
	}

	public function forgot_password_verification()
	{
		$response = [];
    	$rules = [
			'mobile' => ['label' => 'Mobile', 'rules' =>'required|numeric|min_length[10]|max_length[13]'],
			'otp' => ['label' => 'OTP', 'rules' =>'required|min_length[6]']
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

			$mobile = xss_clean($input['mobile']);
			$otp = xss_clean($input['otp']);

			$user_data = $this->common->select('users','*',array('user_mobile' => $mobile,'user_reset_hash'=>$otp));
			if(!empty($user_data)){
				$update_date = date('Y-m-d H:i:s');
				$result = $this->common->update_data("users", array('user_reset_hash' => NULL,'user_updated_at'=>$update_date), array('user_mobile' => $mobile,'user_reset_hash'=>$otp));

            	if(!empty($result)){
            		$login_data = $this->common->select('users','*',array('user_mobile' => $mobile,'user_active'=>'1'));
            		unset($login_data->password);
            		$response = [
		            	'status' => true,
		                'message' => 'OTP verified successfully',
		                'data' => $login_data
		            ];
            	}else{
            		$response = [
		            	'status' => false,
		                'message' => 'Something went wrong, try again'
		            ];
            	}
			}else{
				$response = [
	            	'status' => false,
	                'message' => 'OTP is invalid'
	            ];
			}
		}

		return $this->getResponse($response);
	}

	public function reset_password(){

		$response = [];
    	$rules = [
			'user_id' =>['label' => 'User ID', 'rules' =>'required|numeric'],
			'password' => ['label' => 'Password', 'rules' =>'required|min_length[6]|strong_password'],
			'confirm_password' => ['label' => 'Confirm Password', 'rules' =>'required|min_length[6]|matches[password]'],

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

		$user_id = $input['user_id'];
		$password = $input['password'];
		
		$user_data = $this->common->select('users','*',array('user_id' => $user_id));
		if(!empty($user_data)){
			$pass = password_hash($password, PASSWORD_DEFAULT);
			$update_date = date('Y-m-d H:i:s');
			$result = $this->common->update_data("users", array('user_password' => $pass,'user_updated_at'=>$update_date), array('user_id' => $user_id));

			if(!empty($result)){
				$login_data = $this->common->select('users','*',array('user_id' => $user_id,'user_active'=>'1'));
				unset($login_data->user_password);
				$response = [
					'status' => true,
					'message' => 'Password Changed successfully',
					'data' => $login_data
				];
			}else{
				$response = [
					'status' => false,
					'message' => 'Something went wrong, try again'
				];
			}
		}else{
			$response = [
				'status' => false,
				'message' => 'User Not Registered'
			];
		}
	}

	return $this->getResponse($response);
	}

	public function forgot_password_resend_otp(){

		$response = [];
		$throttler = service('throttler');

		if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false)
		{
			$response = [
				'status' => false,
				'message' => 'Too many requests. Please wait 10 seconds'
			];
		}else{
			$rules = [
				'mobile' => ['label' => 'Mobile', 'rules' =>'required|numeric|min_length[10]|max_length[13]']
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
				$mobile = xss_clean($input['mobile']);
				$user = $this->common->select('users','*',array('user_mobile' => $mobile,'user_active' => 1));
				if (empty($user))
				{
					$response = [
						'status' => false,
						'message' => 'Unable to locate a user'
					];
					
				}else{
					$fcm_id = $user->user_fcm_id;
					$otp = get_unique_code('numeric',6);
					$update_date = date('Y-m-d H:i:s');

					$result = $this->common->update_data("users", array('user_reset_hash' => $otp,'user_updated_at'=>$update_date), array('user_mobile' => $mobile));
					if(!empty($result)){
						try{
							$sub = 'Account activation OTP';
							$msg = 'Use the OTP '.$otp.' to verify your account and login';
							//$this->notification->send($fcm_id,$sub,$msg);
							$response = [
								'status' => true,
								'message' => 'OTP sent successfully'
							];
						}catch (\Exception $e) {
							$response = [
								'status' => false,
								'message' => 'Something went wrong, try again'
							];
						}
					}else{
						$response = [
							'status' => false,
							'message' => 'Something went wrong, try again'
						];
					}

				}
			}
		}
		
		return $this->getResponse($response);

	 }

// 	public function get_profile_data($user_id)
//   	{
   	   
//   		$response = [];
//         $result = $this->common->select('users','*',array('user_id' => $user_id));
//         if(!empty($result)){
//         	 unset($result->user_password); 
//             $response = [
//                     'status' => true,
//                     'data' => $result
//                 ];
//         }else{
//             $response = [
//                 'status' => false,
//                 'message' => 'No data found'
//             ];
//         }
                

//         return $this->getResponse($response);
//   	}

    public function get_profile_data($user_id)
    {
        $response = [];
        $result = $this->common->select('users', '*', array('user_id' => $user_id));
    
        if (!empty($result)) {
            unset($result->user_password);
    
            // Get the user's active membership
            $membership = $this->common->select('memberships', '*', array(
                'membership_user_id' => $user_id,
                'membership_status'  => 'active'
            ));
    
            if (!empty($membership)) {
                $membership = is_array($membership) ? $membership[0] : $membership;
    
                $package = $this->common->select('packages', '*', array(
                    'package_id' => $membership->membership_package_id
                ));
                $package = is_array($package) ? $package[0] : $package;
    
                $result->subscription = [
                    'membership_id'          => $membership->membership_id,
                    'membership_start_date'  => $membership->membership_start_date,
                    'membership_end_date'    => $membership->membership_end_date,
                    'membership_status'      => $membership->membership_status,
                    'package'                => $package
                ];
            } else {
                $result->subscription = null;
            }
    
            $response = [
                'status' => true,
                'data'   => $result
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'No data found'
            ];
        }
    
        return $this->getResponse($response);
    }

   	/**
     * Edit profile
     */
    public function profile_update()
    {
    	$response = [];
    	$rules = [
    		'user_id' =>['label' => 'User ID', 'rules' =>'required|numeric'],
			'full_name'	=> ['label' => 'Full name', 'rules' =>'required|string|min_length[3]'],
			'email' => [
				'label' => 'Email', 
				'rules' =>'required|min_length[6]|max_length[50]|valid_email',
		    ],
		    'mobile' => [
				'label' => 'Mobile', 
				'rules' =>'required|numeric|min_length[10]',
		    ],
			'address' => ['label' => 'Address', 'rules' =>'required|min_length[8]']
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

			$user_id = xss_clean($input['user_id']);
			$full_name = xss_clean($input['full_name']);
			$email = xss_clean($input['email']);
			$mobile = xss_clean($input['mobile']);
			$address = xss_clean($input['address']);

			$users = $this->common->select('users','*',array('user_email'=>$email,'user_id !=' => $user_id));
			if(!empty($users)){
				$response = [
	            	'status' => false,
	                'message' => 'Email already exist'
	            ];
	            return $this->getResponse($response);
			}
			$users = $this->common->select('users','*',array('user_mobile'=>$mobile,'user_id !=' => $user_id));
			if(!empty($users)){
				$response = [
	            	'status' => false,
	                'message' => 'Mobile already exist'
	            ];
	            return $this->getResponse($response);
			}

	        $data = array(
	            'user_name' => $full_name,
		        'user_full_name' => $full_name,  
		        'user_email' => $email, 
		        'user_mobile' => $mobile, 
                'user_address' => $address,
                'user_updated_at' => date('Y-m-d H:i:s')
            );
	        $condition = [
	        	'user_id' => $user_id,
	        ];
	        $result = $this->common->update_data('users', $data, $condition);
	        if($result){
	        	$user_data = $this->common->select('users','*',array('user_id' => $user_id));
		        unset($user_data->user_password); 
		             		
	            $response = [
	            	'status' => true,
	                'message' => 'User updated successfully',
	                'data' => $user_data
	            ];

	        }else{
	        	$response = [
	            	'status' => false,
	                'message' => 'Mobile number is not verified'
	            ];
	        }
	    }

	    return $this->getResponse($response);

    }

    public function change_password()
	{
		$rules = [
			'user_id' => ['label' => 'User Id', 'rules' =>'required|numeric'],
			'current_password'	=> ['label' => 'Current Password', 'rules' =>'required|min_length[6]|trim'],
		    'new_password' => ['label' => 'New Password', 'rules' =>'required|strong_password'],
		    'confirm_new_password' => ['label' => 'Confirm New Password', 'rules' =>'required|matches[new_password]'] 
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

			$user_id = xss_clean($input['user_id']);
			$current_password = $input['current_password'];
			$password = $input['new_password'];

	        $condition = array('user_id' => $user_id);

	        $user = $this->common->select('users','*', $condition);
	        if(!password_verify($current_password, $user->user_password)) {
	        	$response = [
	            	'status' => false,
	                'message' => 'Current password is wrong'
	            ];
	        }else{
		        $newpass = password_hash($password, PASSWORD_DEFAULT);
		        $update_data = array('user_password' => $newpass);
		        $condition = array('user_id' => $user_id);
		        try {
				  $this->common->update_data('users',$update_data,$condition);
				    $response = [
		            	'status' => true,
		                'message' => 'Password changed successfully!'
		            ];
				}catch (\Exception $e) {
					$response = [
		            	'status' => false,
		                'message' => 'Something went wrong. Please try again'
		            ];

				}
	        }

	        
		}
		return $this->getResponse($response);
	}

   	public function logout()
   	{
   		$response = [];
    	$rules = [
			'user_id'	=> ['label' => 'User ID', 'rules' =>'required|numeric']
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

			$user_id = $input['user_id'];
			$result = $this->common->update_data('users', array('user_fcm_id' => ''), array('user_id'=> $user_id));
	        if($result){
	        	$response = [
	                'status' => true,
	                'message' => 'Logout'
	            ];
	        }else{
	            $response = [
	                'status' => false,
	                'message' => 'Access denied !!'
	            ];
	        } 
		}
		return $this->getResponse($response);
   	}


	   public function delete_user()
   	{
		$input=$this->getRequestInput($this->request);

		$user_id = $input['user_id'];
   		$response = [];
        $result = $this->common->delete_user($user_id);

        if(!empty($result)){
            $response = [
                    'status' => true,
                    'data' => 'User Deleted Successfully'
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