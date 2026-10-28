<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CommonModel;
use App\Libraries\EmailLibrary;

class Ajax extends Controller
{

	protected $common;

	public function __construct()
	{
		helper(['custom']);
		$this->common = new CommonModel();
	}

	public function delete_event()
	{
		$data = [];
		$event_id = $this->request->getPost('event_id');
		if (!empty($event_id)) {
			try {

				$this->common->delete_data('events', array('event_id' => $event_id));
				$data['success'] = 'success';
				$data['message'] = 'Event deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Event id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function delete_sports()
	{
		$data = [];
		$sports_id = $this->request->getPost('sports_id');
		if (!empty($sports_id)) {
			try {

				$this->common->delete_data('sports', array('sports_id' => $sports_id));
				$data['success'] = 'success';
				$data['message'] = 'Sport deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Sport id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function delete_country()
	{
		$data = [];
		$country_id = $this->request->getPost('country_id');
		if (!empty($country_id)) {
			try {

				$this->common->delete_data('countries', array('country_id' => $country_id));
				$data['success'] = 'success';
				$data['message'] = 'Country deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Country id is required';
		}

		echo json_encode($data);
		exit;
	}
	
	public function delete_state()
	{
		$data = [];
		$state_id = $this->request->getPost('state_id');
		if (!empty($state_id)) {
			try {

				$this->common->delete_data('state', array('state_id' => $state_id));
				$data['success'] = 'success';
				$data['message'] = 'State deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'State id is required';
		}

		echo json_encode($data);
		exit;
	}
	
	public function delete_city()
	{
		$data = [];
		$city_id = $this->request->getPost('city_id');
		if (!empty($city_id)) {
			try {

				$this->common->delete_data('cities', array('city_id' => $city_id));
				$data['success'] = 'success';
				$data['message'] = 'City deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'City id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function delete_district()
	{
		$data = [];
		$district_id = $this->request->getPost('district_id');
		if (!empty($district_id)) {
			try {

				$this->common->delete_data('districts', array('district_id' => $district_id));
				$data['success'] = 'success';
				$data['message'] = 'District deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'District id is required';
		}

		echo json_encode($data);
		exit;
	}
	
	public function delete_tournament()
	{
		$data = [];
		$event_id = $this->request->getPost('event_id');
		$user_id = session()->get('user_id');
		if (!empty($event_id)) {
			try {

				$this->common->delete_data('listings ', array('listing_user_id' => $user_id, 'listing_id' => $event_id));
				$this->common->delete_data('listing_events ', array('listing_event_listing_id' => $event_id));

				$data['success'] = 'success';
				$data['message'] = 'Event deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Event id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function cancel_tournament()
	{
		$data = [];
		$id = $this->request->getPost('id');
		$remarks = session()->get('remarks');
		if (!empty($id)) {
			try {

				$insertdata = ['listing_cancel' => 1, 'listing_cancel_remarks' => $remarks];
				$this->common->update_data('listings ', $insertdata, array('listing_id' => $id));

				$data['success'] = 'success';
				$data['message'] = 'Tournament cancelled successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Tournament id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function delete_tourney_event()
	{
		$data = [];
		$event_id = $this->request->getPost('event_id');
		$user_id = session()->get('user_id');
		if (!empty($event_id)) {
			try {

				$this->common->delete_data('listing_events ', array('listing_event_id' => $event_id));

				$data['success'] = 'success';
				$data['message'] = 'Event deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Event id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function get_states()
	{
		$country = $this->request->getPost('country');
		$condition = array('state_country_id' => $country, 'state_active' => 1);
		$states = $this->common->select('state', '*', $condition, TRUE);
		$content = '<option value="">--Select State--</option>';
		if (isset($states) && !empty($states)) {
			foreach ($states as $data) {
				$content .= '<option value="' . $data->state_id . '">' . $data->state_name . '</option>';
			}
		}
		echo $content;
	}

	public function get_cities()
	{
		$state = $this->request->getPost('state');
		$country = $this->request->getPost('country');
		if ($state) {
			$condition = array('city_state_id' => $state, 'city_active' => 1);
		} else {
			$condition = array('city_country_id' => $country, 'city_active' => 1);
		}
		$cities = $this->common->select('cities', '*', $condition, TRUE);
		$content = '<option value="">--Select City--</option>';
		if (isset($cities) && !empty($cities)) {
			foreach ($cities as $data) {
				$content .= '<option value="' . $data->city_id . '">' . $data->city_name . '</option>';
			}
		}
		echo $content;
	}
	
	// FIXED: This method should get districts based on STATE ID
	public function get_districts()
	{
		// Get the state ID from POST (should be 'state' not 'district')
		$state_id = $this->request->getPost('state');
		
		// Log for debugging
		log_message('info', 'get_districts called with state_id: ' . $state_id);
		
		if ($state_id) {
			$condition = array('district_state_id' => $state_id, 'district_active' => 1);
			$districts = $this->common->select('districts', '*', $condition, TRUE);
			$content = '<option value="">--Select District--</option>';
			if (!empty($districts)) {
				foreach ($districts as $data) {
					$content .= '<option value="' . $data->district_id . '">' . $data->district_name . '</option>';
				}
			}
		} else {
			$content = '<option value="">--Select District--</option>';
		}
		
		echo $content;
	}
	
	// FIXED: This method should get city based on DISTRICT ID
	public function get_city()
	{
		// Get the district ID from POST
		$district_id = $this->request->getPost('district_id');
		
		// Log for debugging
		log_message('info', 'get_city called with district_id: ' . $district_id);
		
		if ($district_id) {
			$condition = array('city_district_id' => $district_id, 'city_active' => 1);
			$cities = $this->common->select('cities', '*', $condition, TRUE);
			
			if (!empty($cities)) {
				// Return city name as text for the input field
				echo $cities[0]->city_name;
			} else {
				echo '';
			}
		} else {
			echo '';
		}
	}

	public function uploadImage()
	{
		// is already logged in.

		$response = [];

		$rules = [
			'images' => 'uploaded[images]|ext_in[images,jpg,jpeg,png,gif]|max_size[images,12288]',
		];
		if (! $this->validate($rules)) {
			$response = [
				'status' => false,
				'message' => 'Validation error',
				'data' => $this->validator->getErrors()
			];
		} else {
			$user_id = session()->get('user_id');
			$folderpath = FCPATH . 'public/uploads/listings/' . $user_id;
			if (create_folder($folderpath)) {
				$foldername = $folderpath;
			} else {
				$foldername = FCPATH . 'public/uploads/listings/' . $user_id;
			}
			$images = array();
			$imageFile = $this->request->getFile('images');
			$filename = $imageFile->getRandomName();
			$imageFile->move($foldername, $filename);
			$file_name = $this->resize($foldername, $filename);
			array_push($images, $file_name);
			unlink($foldername . '/' . $filename);

			$response['status'] = true;
			$response['filename'] = $file_name;
		}

		echo json_encode($response);
	}

	public function resize($foldername, $filename)
	{
		$image = \Config\Services::image();
		//$filewidth=array(800, 621);
		$filewidth = array(621);
		foreach ($filewidth as $fwidth) {
			//if($fwidth==800){$fn="lg_";$fheight=500;}
			if ($fwidth == 621) {
				$fn = "md_" . $filename;
				$fheight = 465;
			}

			$image->withFile($foldername . '/' . $filename)
				//->fit($fwidth, $fheight)
				->resize($fwidth, $fheight, true, 'width')
				->save($foldername . '/' . $fn);
			return $fn;
		}
	}

	public function search_tournment()
	{
		//print_r($_POST);die();
		$sports = $this->request->getPost('sports');
		$tstatus = $this->request->getPost('tstatus');
		$q = $this->request->getPost('q');
		if ($tstatus == 'upcoming') {
			$tstatus = '';
		}
		$url = base_url() . '/?';
		if (!empty($sports) && empty($tstatus)) {
			$url .= 'sports=' . $sports;
			if (!empty($q)) {
				$url .= '&q=' . urlencode($q);
			}
		}
		if (empty($sports) && !empty($tstatus)) {
			$url .= 'status=' . $tstatus;
			if (!empty($q)) {
				$url .= '&q=' . urlencode($q);
			}
		}
		if (!empty($sports) && !empty($tstatus)) {
			$url .= 'sports=' . $sports . '&status=' . $tstatus;
			if (!empty($q)) {
				$url .= '&q=' . urlencode($q);
			}
		}
		if (empty($sports) && empty($tstatus)) {
			if (!empty($q)) {
				$url .= 'q=' . urlencode($q);
			}
		}

		echo $url;
		die();
	}

	public function set_city()
	{
		$city_id = $this->request->getPost('cityid');
		$city_name = $this->request->getPost('cityname');
		session()->set('region_name', $city_name);
		session()->set('city_id', $city_id);
		echo 'success';
	}

	public function all_ind()
	{
		$country_id = $this->request->getPost('countryid');
		session()->remove('city_id');
		session()->set('region_name', 'India');
		session()->set('country_id', $country_id);
		echo 'success';
	}

	public function set_global()
	{
		session()->remove('country_id');
		session()->remove('city_id');
		session()->set('region_name', 'Global');
		echo 'success';
	}

	public function search_list()
	{
		$q = $this->request->getPost('q');
		$search = array('listing_title' => $q);
		$listings = $this->common->select('listings', '*', false, TRUE, false, $search);
		$content = '';
		if (isset($listings) && !empty($listings)) {
			foreach ($listings as $data) {
				$content .= '<a class="dropdown-item search-droplist" href="javascript:void(0)">' . $data->listing_title . '</a>';
			}
		}
		echo $content;
	}

	public function delete_organizer()
	{
		$data = [];
		$user_id = $this->request->getPost('user_id');
		if (!empty($user_id)) {
			try {

				$this->common->delete_data('users', array('user_id' => $user_id));
				$data['success'] = 'success';
				$data['message'] = 'Organizer deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Sport id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function active_account()
	{
		$data = [];
		$user_id = $this->request->getPost('user_id');
		if (!empty($user_id)) {
			try {
				$this->common->update_data('users', array('user_active' => 1, 'user_remarks' => NULL), array('user_id' => $user_id));
				$data['success'] = 'success';
				$data['message'] = 'Organizer verified successfully!';
				session()->set('success_message', 'Organizer verified successfully!');
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'User id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function admin_delete_tournament()
	{
		$data = [];
		$event_id = $this->request->getPost('event_id');
		$user_id = session()->get('user_id');
		if (!empty($event_id)) {
			try {

				$this->common->delete_data('listings ', array('listing_id' => $event_id));
				$this->common->delete_data('listing_events ', array('listing_event_listing_id' => $event_id));

				$data['success'] = 'success';
				$data['message'] = 'Event deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Event id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function refer_back()
	{

		$org_id = $_POST['org_id'];
		$sub = $_POST['subject'];
		$msg = $_POST['message'];
		$users = $this->common->select('users', '*', array('user_id' => $org_id));
		//print_array($users);echo $users->user_email;die();
		$user = [
			'email' => $users->user_email,
			'subject' => $sub,
			'message' => $msg

		];
		$email = new EmailLibrary();
		$sent = $email->send($user);
		if ($sent) {
			$this->common->update_data('users', array('user_remarks' => 'Onhold'), array('user_id' => $org_id));
			$data['success'] = 'success';
			$data['message'] = 'Message sent successfully!';
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Message is not sent';
		}
		echo json_encode($data);
		exit;
	}

	public function delete_group()
	{
		$data = [];
		$grp_id = $this->request->getPost('grp_id');
		if (!empty($grp_id)) {
			try {

				$this->common->delete_data('tbl_groups', array('grp_id' => $grp_id));
				$data['success'] = 'success';
				$data['message'] = 'Group deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Group id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function delete_round()
	{
		$data = [];
		$round_id = $this->request->getPost('round_id');
		if (!empty($round_id)) {
			try {

				$this->common->delete_data('tbl_rounds', array('round_id' => $round_id));
				$data['success'] = 'success';
				$data['message'] = 'Round deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Round id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function delete_team()
	{
		$data = [];
		$team_id = $this->request->getPost('team_id');
		if (!empty($team_id)) {
			try {

				$this->common->delete_data('teams', array('team_id' => $team_id));
				$data['success'] = 'success';
				$data['message'] = 'Team deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Team id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function delete_match()
	{
		$data = [];
		$match_id = $this->request->getPost('match_id');
		if (!empty($match_id)) {
			try {

				$this->common->delete_data('matches', array('match_id' => $match_id));
				$data['success'] = 'success';
				$data['message'] = 'Match deleted successfully!';
			} catch (Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Match id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function delete_umpire()
	{
		if (session()->get('role_id') != 3) {
			return redirect()->to(base_url());
		}

		$data = [];
		$user_id = $this->request->getPost('user_id');
		if (!empty($user_id)) {
			try {

				$this->common->delete_data('users', array('user_id' => $user_id));
				$data['success'] = 'success';
				$data['message'] = 'Umpire deleted successfully!';
			} catch (Exception $e) {

				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {

			$data['error'] = 'fail';
			$data['message'] = 'Umpire id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function change_participant_paystatus()
	{
		$participant_id = $this->request->getPost('participant_id');
		$participant_pay_status = $this->request->getPost('participant_pay_status');
		$mode = $this->request->getPost('mode')?? NULL;

		$updated = $this->common->update_participant_status($participant_id, $participant_pay_status,$mode);

		if ($updated) {
			return $this->response->setJSON(['success' => true]);
		} else {
			return $this->response->setJSON(['success' => false]);
		}
	}
	
	public function post_status()
	{
		$post_id = $this->request->getPost('post_id');
		$post_status = $this->request->getPost('post_status');
		$updated = $this->common->update_post_status($post_id, $post_status);

		if ($updated) {
			return $this->response->setJSON(['success' => true]);
		} else {
			return $this->response->setJSON(['success' => false]);
		}
	}
	
	public function delete_post()
	{
		$post_id = $this->request->getPost('post_id');
		$updated = $this->common->delete_data('posts', array('post_id' => $post_id));

		if ($updated) {
			return $this->response->setJSON(['success' => true]);
		} else {
			return $this->response->setJSON(['success' => false]);
		}
	}
	
	public function bulk_action_posts()
	{
		$action = $this->request->getPost('action');
		$ids = $this->request->getPost('ids');

		if (empty($ids) || !in_array($action, ['delete', 'activate', 'deactivate'])) {
			return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
		}

		foreach ($ids as $id) {
			if ($action === 'delete') {
				$this->common->delete_data('posts', ['post_id' => $id]);
			} elseif ($action === 'activate') {
				$this->common->update_post_status($id, 1); // 1 = Active
			} elseif ($action === 'deactivate') {
				$this->common->update_post_status($id, 0); // 0 = Inactive
			}
		}

		return $this->response->setJSON(['success' => true]);
	}
	
	public function active_user()
	{
		
		$post_id = $this->request->getPost('post_id');
		$post_status = $this->request->getPost('post_status');
		$updated = $this->common->update_data('users', array('user_active' => $post_status), array('user_id' => $post_id));

		if ($updated) {
			return $this->response->setJSON(['success' => true]);
		} else {
			return $this->response->setJSON(['success' => false]);
		}
	}
	
	public function delete_user()
	{
		$post_id = $this->request->getPost('post_id');
		$updated = $this->common->delete_data('users', array('user_id' => $post_id));

		if ($updated) {
			return $this->response->setJSON(['success' => true]);
		} else {
			return $this->response->setJSON(['success' => false]);
		}
	}

	public function delete_package()
	{
		$package_id = $this->request->getPost('package_id');
		if (empty($package_id)) {
			echo json_encode(['error' => 'fail', 'message' => 'Package id is required']);
			exit;
		}
		$this->common->delete_data('packages', ['package_id' => $package_id]);
		echo json_encode(['success' => 'success', 'message' => 'Package deleted successfully!']);
		exit;
	}

	public function toggle_package()
	{
		$package_id = $this->request->getPost('package_id');
		if (empty($package_id)) {
			echo json_encode(['error' => 'fail', 'message' => 'Package id is required']);
			exit;
		}
		$pkg = $this->common->select('packages', '*', ['package_id' => $package_id]);
		if (empty($pkg)) {
			echo json_encode(['error' => 'fail', 'message' => 'Package not found']);
			exit;
		}
		$newStatus = $pkg->package_active ? 0 : 1;
		$this->common->update_data('packages', ['package_active' => $newStatus], ['package_id' => $package_id]);
		$msg = $newStatus ? 'Package enabled.' : 'Package disabled.';
		echo json_encode(['success' => 'success', 'message' => $msg]);
		exit;
	}

	public function cancel_membership()
	{
		$membership_id = $this->request->getPost('membership_id');
		if (empty($membership_id)) {
			echo json_encode(['error' => 'fail', 'message' => 'Membership id is required']);
			exit;
		}
		$db = \Config\Database::connect();
		$db->table('memberships')
			->where('membership_id', $membership_id)
			->update(['membership_status' => 'cancelled', 'membership_updated_at' => date('Y-m-d H:i:s')]);
		echo json_encode(['success' => 'success', 'message' => 'Membership cancelled.']);
		exit;
	}
}