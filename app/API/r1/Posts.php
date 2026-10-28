<?php

namespace app\API\r1;

use CodeIgniter\RESTful\ResourceController;
use App\Traits\ReqresTrait;
use App\Models\CommonModel;
use App\Models\PostModel;
use App\Models\UmpireModel;

class Posts extends ResourceController
{
	use ReqresTrait;
	protected $common;
	protected $umpire;
	protected $post;
	protected $db;
	public function __construct()
	{
		helper(['form', 'url', 'custom', 'number']);
		$this->common = new CommonModel();
		$this->umpire = new UmpireModel();
		$this->post = new PostModel();
		$this->db = \Config\Database::connect();
	}

	public function createPost()
	{
		$response = [];
		$rules = [
			'user_id'   => ['label' => 'User ID', 'rules' => 'required|numeric'],
			'type'      => ['label' => 'type', 'rules' => 'required'],
		];

		if (!$this->validate($rules)) {
			$response = [
				'status'    => false,
				'message'   => 'validation error',
				'data'      => $this->validator->getErrors()
			];
		} else {
			$input          = $this->getRequestInput($this->request);
			$user_id        = $input['user_id'];
			$poststatus     = 0;
			$type           = $input['type'];
			$title          = !empty($input['title']) ? xss_clean($input['title']) : NULL;
			$content        = !empty($input['content']) ? xss_clean($input['content']) : NULL;
			$files          = $this->request->getFiles();
			$upload_data    = [];

			helper('filesystem'); // Load the file helper
			if (isset($files['userfiles']) && !empty($files['userfiles'])) {
				foreach ($files['userfiles'] as $file) {
					if ($file->isValid() && !$file->hasMoved()) {
						$newName = $file->getRandomName();
						// Generate a unique name for the image
						$image_name = time() . '_post_img.jpg';
						$path = FCPATH . 'public/uploads/posts';
						// Ensure the directory exists
						if (!is_dir(dirname($path))) {
							mkdir(dirname($path), 0755, true);
						}
						$file->move($path, $newName);
						$upload_data[] = 'public/uploads/posts/' . $newName;
					} else {
						$response = [
							'status'    => false,
							'error'     => $file->getErrorString()
						];
						return $this->getResponse($response);
					}
				}
			}
            
			// Separate images and videos based on their extensions
			$images = [];
			$videos = [];

			foreach ($upload_data as $file) {
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
					$images[] = $file;
				} elseif (in_array($extension, ['mp4', 'avi', 'mov'])) {
					$videos[] = $file;
				}
			}

			$data = [
				'post_user_id'      => $user_id,
				'post_title'        => $title,
				'post_content'      => $content,
				'post_type'         => $type,
				'post_images_json'  => json_encode($images, JSON_UNESCAPED_SLASHES),
				'post_videos_json'  => json_encode($videos, JSON_UNESCAPED_SLASHES),
				'post_status'       => $poststatus,
			];

			$result = $this->common->insert_data('posts', $data);
			if (!empty($result)) {
				$response = [
					'status'    => true,
					'message'   => 'Post added successfully'
				];
			} else {
				$response = [
					'status'    => false,
					'error'     => 'Failed to save the post.'
				];
			}
		}

		return $this->getResponse($response);
	}

	public function editPost()
	{
		$response = [];
		$rules = [
			'post_id' => ['label' => 'Post ID', 'rules' => 'required|numeric'],
			'user_id' => ['label' => 'User ID', 'rules' => 'required|numeric'],
			'type' => ['label' => 'type', 'rules' => 'required'],
		];

		if (!$this->validate($rules)) {
			$response = [
				'status' => false,
				'message' => 'validation error',
				'data' => $this->validator->getErrors()
			];
		} else {
			$input 			= $this->getRequestInput($this->request);
			$date 			= date('Y-m-d H:i:s');
			$user_id 		= $input['user_id'];
			$poststatus 	= 1;
			$type 			= $input['type'];
			$post_id 		= xss_clean($input['post_id']);
			$title 			= !empty($input['title']) ? xss_clean($input['title']) : NULL;
			$content 		= !empty($input['content']) ? xss_clean($input['content']) : NULL;
			$files 			= $this->request->getFiles();
			$postdata 		= $this->common->select('posts', '*', array('post_id' => $post_id));
			$upload_data 	= [];
			helper('filesystem'); // Load the file helper
			if (isset($files['userfiles']) && !empty($files['userfiles'])) {
				foreach ($files['userfiles'] as $file) {
					if ($file->isValid() && !$file->hasMoved()) {
						$newName = $file->getRandomName();
						// Generate a unique name for the image
						$image_name = time() . '_post_img.jpg';
						$path = FCPATH . 'public/uploads/posts';
						// Ensure the directory exists
						if (!is_dir(dirname($path))) {
							mkdir(dirname($path), 0755, true);
						}

						$file->move($path, $newName);
						$upload_data[] = 'public/uploads/posts/' . $newName;
					} else {
						$response = [
							'status' => false,
							'error' => $file->getErrorString()
						];
						return $this->getResponse($response);
					}
				}
			}

			$data = [
				'post_user_id' 	=> $user_id,
				'post_title' 	=> $title,
				'post_content' 	=> $content,
				'post_type' 	=> $type,
				'post_status' 	=> $poststatus,
			];

			// Separate images and videos based on their extensions
			$images = [];
			$videos = [];

			foreach ($upload_data as $file) {
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
					$images[] = $file;
				} elseif (in_array($extension, ['mp4', 'avi', 'mov'])) {
					$videos[] = $file;
				}
			}

			if (!empty($images)) {
				$existing_images = !empty($postdata->post_images_json) ? json_decode($postdata->post_images_json, true) : [];
				$merged_images = array_merge($existing_images, $images);
				$data['post_images_json'] = json_encode($merged_images);
			}

			if (!empty($videos)) {
				$existing_videos = !empty($postdata->post_videos_json) ? json_decode($postdata->post_videos_json, true) : [];
				$merged_videos = array_merge($existing_videos, $videos);
				$data['post_videos_json'] = json_encode($merged_videos);
			}

			$data['post_updated_at'] = $date;
			$result = $this->common->update_data('posts', $data, array('post_id' => $post_id));
			if (!empty($result)) {
				$response = [
					'status' => true,
					'message' => 'Post updated successfully'
				];
			} else {
				$response = [
					'status' => false,
					'error' => 'Failed to save the post.'
				];
			}
		}

		return $this->getResponse($response);
	}

	public function addLike()
	{
		$response = [];
		$rules = [
			'user_id' => ['label' => 'User ID', 'rules' => 'required|numeric'],
			'post_id' => ['label' => 'Post Id', 'rules' => 'required|numeric'],
		];

		if (! $this->validate($rules)) {
			$response = [
				'status' => false,
				'message' => 'validation error',
				'data' => $this->validator->getErrors()
			];
		} else {
			$input = $this->getRequestInput($this->request);
			$user_id = $input['user_id'];
			$post_id = $input['post_id'];

			if (!$this->post->is_liked($user_id, $post_id)) {
				$this->post->add_like($user_id, $post_id);
				$response = [
					'status' => true,
					'message' => 'Liked'
				];
			} else {
				$this->post->add_like($user_id, $post_id);
				$response = [
					'status' => true,
					'message' => 'Like Removed'
				];
			}
		}
		
		return $this->getResponse($response);
	}
	public function getPost()
{
    $response = [];

	$posts = $this->post->post_details();
	if ($posts->getNumRows() > 0) {
		$response = [
			'status' => true,
			'data' => $posts->getResult()
		];
	} else {
		$response = [
			'status' => false,
			'message' => 'No datas found'
		];
	}
    return $this->getResponse($response);
}


	public function createComment()
	{
		$response = [];
		$rules = [
			'user_id' => ['rules' => 'required|numeric'],
			'post_id' => ['rules' => 'required|numeric'],
			'comment' =>['rules' => 'required']
		];

		if (! $this->validate($rules)) {
			$response = [
				'status' => false,
				'message' => 'validation error',
				'data' => $this->validator->getErrors()
			];
		} else {
			$input = $this->getRequestInput($this->request);
			$user_id = $input['user_id'];
			$post_id = $input['post_id'];
			$comment = $input['comment'];

			
				$this->post->add_comment($user_id, $post_id,$comment);
				$response = [
					'status' => true,
					'message' => 'Comment Added'
				];
			
		}
		
		return $this->getResponse($response);
	}
	public function editComment()
	{
		$response = [];
		$rules = [
			'user_id' => ['rules' => 'required|numeric'],
			'post_id' => ['rules' => 'required|numeric'],
			'comment' =>['rules' => 'required'],
			'id' => ['rules' => 'required'],
		];

		if (! $this->validate($rules)) {
			$response = [
				'status' => false,
				'message' => 'validation error',
				'data' => $this->validator->getErrors()
			];
		} else {
			$input = $this->getRequestInput($this->request);
			$user_id = $input['user_id'];
			$post_id = $input['post_id'];
			$id= $input['id'];
			$comment = $input['comment'];			
			$this->post->edit_comment($user_id, $post_id,$id,$comment);
				$response = [
					'status' => true,
					'message' => 'Comment Updated'
				];
			
		}
		
		return $this->getResponse($response);
	}


	public function deleteComment()
	{
		$response = [];
		$rules = [
			'user_id' => ['rules' => 'required|numeric'],
			'post_id' => ['rules' => 'required|numeric'],
			'comment_id' => ['rules' => 'required'],
		];

		if (! $this->validate($rules)) {
			$response = [
				'status' => false,
				'message' => 'validation error',
				'data' => $this->validator->getErrors()
			];
		} else {
			$input = $this->getRequestInput($this->request);
			$user_id = $input['user_id'];
			$post_id = $input['post_id'];
			$comment_id = $input['comment_id'];

			
				$this->post->delete_comment($user_id, $post_id,$comment_id);
				$response = [
					'status' => true,
					'message' => 'Comment Deleted'
				];
			
		}
		
		return $this->getResponse($response);
	}
	
	public function getComments()
	{
		$response = [];
		$rules = [
			'post_id' => ['rules' => 'required|numeric'],
		];

		if (! $this->validate($rules)) {
			$response = [
				'status' => false,
				'message' => 'validation error',
				'data' => $this->validator->getErrors()
			];
		} else {
			$input = $this->getRequestInput($this->request);
			$post_id = $input['post_id'];

			
			$comments = $this->post->get_comment($post_id);
        
			if ($comments->getNumRows() > 0) {
				$response = [
					'status' => true,
					'data' => $comments->getResult()
				];
			} else {
				$response = [
					'status' => false,
					'message' => 'No comments found'
				];
			}
			
		}		
		return $this->getResponse($response);
	}
}
