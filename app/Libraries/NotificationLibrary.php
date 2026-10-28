<?php namespace App\Libraries;

class NotificationLibrary
{
	public function __construct()
	{

	}

	public function send($device_token,$title,$message)
	{
		$device_token = $this->request->getVar("device_token");
		$info_data = array(
			"title" => $title,
			"body" => $message
		);
		return $this->sendNotification($device_token, $info_data);
	}

	public function sendNotification($device_token, $message)
    {
        $SERVER_API_KEY = '<YOUR-SERVER-API-KEY>';
  
        // payload data, it will vary according to requirement
        $data = [
            "to" => $device_token, // for single device id
            "data" => $message
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
      
        curl_close($ch);
      
        return $response;
    }
}