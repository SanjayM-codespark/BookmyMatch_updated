<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CommonModel;

class Uploads extends Controller
{
	protected $common;
	public function __construct()
	{
		helper(['form','url','custom']);
		$this->common = new CommonModel();
	}

	public function profile_image()
	{
		$response = [];
			
		$rules = [
			'image' => ['label' => 'Image', 'rules' =>'uploaded[image]|ext_in[image,jpg,jpeg,png,gif]|max_size[image,4096]']
		];
		if(! $this->validate($rules)){
			$response = [
            	'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
		}else{
			$user_id = session()->get('user_id');
			$image_type = $this->request->getPost('image_type');
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
            	$this->common->update_data("users", array('user_profile_img' => $filename), array('user_id' => $user_id));
            	$response['status'] = true;
            	$response['message'] = 'Image uploaded successfully';
	            $response['filename'] = $filename;
            }else{
            	$response['status'] = false;
	            $response['message'] = 'Image is not uploaded. Try again';
            }
		}
		
		echo json_encode($response);
	}

	public function upload_pdf()
	{
		
		$response = [];
			
		$rules = [
			'images' => 'uploaded[images]|ext_in[images,pdf]|max_size[images,12288]',
		];
		if(! $this->validate($rules)){
			$response = [
            	'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
		}else{
			$user_id = session()->get('user_id');
			$folderpath = FCPATH.'public/uploads/pdf';
			if(create_folder($folderpath)){
	        	$foldername = $folderpath;
	        }else{
	        	$foldername = FCPATH.'public/uploads/pdf';
	        }
	        $images = array();
	        $imageFile = $this->request->getFile('images');
	        $filename = $imageFile->getRandomName();
            $imageFile->move($foldername, $filename);
			$response['status'] = true;
	        $response['filename'] = $filename;
			
		}
		
		echo json_encode($response);
	   
	}

	public function upload_image()
	{
		
		$response = [];
			
		$rules = [
			'images' => 'uploaded[images]|ext_in[images,jpg,jpeg,png,gif]|max_size[images,12288]',
		];
		if(! $this->validate($rules)){
			$response = [
            	'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
		}else{
			$user_id = session()->get('user_id');
			$folderpath = FCPATH.'public/uploads/images';
			if(create_folder($folderpath)){
	        	$foldername = $folderpath;
	        }else{
	        	$foldername = FCPATH.'public/uploads/images';
	        }
	        $images = array();
	        $imageFile = $this->request->getFile('images');
	        $filename = $imageFile->getRandomName();
            $imageFile->move($foldername, $filename);
			$response['status'] = true;
	        $response['filename'] = $filename;
			
		}
		
		echo json_encode($response);
	   
	}


	public function upload_video()
	{
		
		$response = [];
			
		$rules = [
			'video_file' => ['label' => 'Video', 'rules' =>'uploaded[video_file]|mime_in[video_file,video/mpeg,video/avi,video/mp4]|max_size[video_file,100000]']
		];
		if(! $this->validate($rules)){
			$response = [
            	'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
		}else{
			$user_id = session()->get('user_id');
			$folderpath = FCPATH.'public/uploads/videos';
			if(create_folder($folderpath)){
	        	$foldername = $folderpath;
	        }else{
	        	$foldername = FCPATH.'public/uploads/videos';
	        }
	        $images = array();
	        $imageFile = $this->request->getFile('video_file');
	        $filename = $imageFile->getRandomName();
            $imageFile->move($foldername, $filename);
			$response['status'] = true;
	        $response['filename'] = $filename;
			
		}
		
		echo json_encode($response);
	   
	}

	

	
}