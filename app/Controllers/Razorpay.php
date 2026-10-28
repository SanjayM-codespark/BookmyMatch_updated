<?php 
namespace App\Controllers;
use App\Models\CommonModel;
class Razorpay extends BaseController {
	protected $common;
	public function __construct() {
		helper(['form','url','custom']);
		$this->common = new CommonModel();
	}
	public function index() {
		if(!session()->has('logged_in')){
			return redirect()->to(base_url());	
		}
		$data = [];
		$data['title']              = 'Checkout payment';  
		$data['callback_url']       = base_url().'/razorpay/callback';
		$data['surl']               = base_url().'/razorpay/success';
		$data['furl']               = base_url().'/razorpay/failed';
		$data['amount']			    = session()->get('paymtamout');
		$data['currency_code']      = 'INR';
		echo view("payment/razor_checkout", $data);
	}
	// initialized cURL Request
	private function curl_handler($payment_id, $amount)  {
		$url            = 'https://api.razorpay.com/v1/payments/'.$payment_id.'/capture';
		$key_id         = "rzp_test_gFGEDl83OLF33h";
		$key_secret     = "9Bub0KaCmQMZr4wcsJX4vUFw";
		$fields_string  = "amount=$amount";
		//cURL Request
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERPWD, $key_id.':'.$key_secret);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		return $ch;
	}   
	// callback method
	public function callback() {   
		if (!empty($this->request->getPost('razorpay_payment_id')) && !empty($this->request->getPost('merchant_order_id'))) {
			$razorpay_payment_id    = $this->request->getPost('razorpay_payment_id');
			$merchant_order_id      = $this->request->getPost('merchant_order_id');
			session()->set('razorpay_payment_id', $this->request->getPost('razorpay_payment_id'));
			session()->set('merchant_order_id', $this->request->getPost('merchant_order_id'));
			$currency_code = 'INR';
			$amount = $this->request->getPost('merchant_total');
			$success = false;
			$error = '';
			try {                
				$ch = $this->curl_handler($razorpay_payment_id, $amount);
				//execute post
				$result = curl_exec($ch);
				
				$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($result === false) {
					$success = false;
					$error = 'Curl error: '.curl_error($ch);
				} else {
					$response_array = json_decode($result, true);
					//Check success response
					if ($http_status === 200 and isset($response_array['error']) === false) {
						$success = true;
					} else {
						$success = false;
						if (!empty($response_array['error']['code'])) {
							$error = $response_array['error']['code'].':'.$response_array['error']['description'];
						} else {
							$error = 'RAZORPAY_ERROR:Invalid Response <br/>'.$result;
						}
					}
				}
				//close curl connection
				curl_close($ch);
			} catch (Exception $e) {
				$success = false;
				$error = 'Request to Razorpay Failed';
			}
			if ($success === true) {
				if(!empty(session()->get('ci_subscription_keys'))) {
				session()->unset('ci_subscription_keys');
				}
				
					return redirect()->to($this->request->getPost('merchant_surl_id'));
				
			} else {
				return redirect()->to($this->request->getPost('merchant_furl_id'));
			}
		} else {
			echo 'An error occured. Contact site administrator, please!';
		}
	}
	
	public function success() {
		if(!session()->has('logged_in')){
			return redirect()->to(base_url());	
		}
		
		$data['title'] = 'Razorpay Success';
		
		$payment_id = session()->get('paymtid');
		$payment_order_id = session()->get('payorderid');
		$txn_id = session()->get('razorpay_payment_id');
		    $data = [
		    	'payment_txn_id' => $txn_id,
				'payment_active' => 1,
				'payment_status' => 'success'
			];
			$condition = [
				'payment_id' => $payment_id,
				'payment_order_id' => $payment_order_id
			];
			
	 	$result = $this->common->update_data('payment_details',$data, $condition);
	 	$data['razorpay_payment_id'] = session()->get('razorpay_payment_id');
		$data['merchant_order_id'] = session()->get('merchant_order_id');
		
		$data['header'] = TRUE;
        $data['footer'] = TRUE;

		echo view('payment/razor_success', $data);
	}  

	public function failed() {
		if(!session()->has('logged_in')){
			return redirect()->to(base_url());	
		}
		
		$data['body_class'] = 'skin-default card-no-border';
		$data['title'] = 'Razorpay Failed';  
		
		$payment_id = session()->get('paymtid');
		$payment_order_id = session()->get('payorderid');
		$txn_id = $_GET['payment_id'];
		    $data = [
		    	'payment_txn_id' => $txn_id,
				'payment_status' => 'failed'
			];
			$condition = [
				'payment_id' => $payment_id,
				'payment_order_id' => $payment_order_id
			];
			
	 	$result = $this->common->update_data('payment_details',$data, $condition);
	 	$data['razorpay_payment_id'] = $_GET['payment_id'];
		//$data['merchant_order_id'] = session()->get('merchant_order_id');
		echo view('payment/razor_failed', $data);
	}
}