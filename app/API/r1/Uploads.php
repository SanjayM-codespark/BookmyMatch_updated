<?php
namespace App\API\r1;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ReqresTrait;
use App\Models\CommonModel;
use App\Libraries\NotificationLibrary;

class Uploads extends ResourceController
{
    use ReqresTrait;
    protected $common;
    protected $notification;
	public function __construct()
	{
		helper(['form','url','custom']);
		$this->common = new CommonModel();
		$this->notification = new NotificationLibrary();
	}

	public function profile_image()
	{
		$response = [];
			
		$rules = [
			'user_id' => ['label' => 'User Id', 'rules' =>'required|numeric'],
			'image' => ['label' => 'Image', 'rules' =>'uploaded[image]|ext_in[image,jpg,jpeg,png,gif]|max_size[image,4096]']
		];
		if(! $this->validate($rules)){
			$response = [
            	'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
		}else{
			$user_id = $this->request->getPost('user_id');
			$folderpath = FCPATH.'public/uploads/images/profile/'.$user_id;
			if(create_folder($folderpath)){
	        	$foldername = $folderpath;
	        }else{
	        	$foldername = FCPATH.'public/uploads/images/profile/'.$user_id;
	        }
	        $images = array();
	        $imageFile = $this->request->getFile('image');
	        $filename = $imageFile->getRandomName();

            if($imageFile->move($foldername, $filename)){
            	$result = $this->common->update_data("users", array('user_profile_img' => $filename), array('user_id' => $user_id));
            	if($result){
            		$response['status'] = true;
	            	$response['message'] = 'Image uploaded successfully';
		            $response['filename'] = $filename;
            	}else{
            		$response['status'] = false;
	            	$response['message'] = 'Something went wrong. Try again';
            	}
            	
            }else{
            	$response['status'] = false;
	            $response['message'] = 'Image is not uploaded. Try again';
            }
            
		}
		
		return $this->getResponse($response);
	}

}