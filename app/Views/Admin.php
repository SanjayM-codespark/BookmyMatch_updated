<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CommonModel;
use App\Models\TournamentModel;
use App\Libraries\RestLibrary;
use App\Libraries\NotificationLibrary;
use DB;
class Admin extends Controller
{
	protected $rest;
	protected $common;
	protected $tournament;
	protected $notification;
	public function __construct()
	{
		helper(['form', 'url', 'custom']);
		$this->rest = new RestLibrary();
		$this->common = new CommonModel();
		$this->tournament = new TournamentModel();
		$this->notification = new NotificationLibrary();
	}

	public function index()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['tournaments'] = $this->tournament->get_tournament();
		//print_array($data['tournaments']);die();
		echo view('admin/home', $data);
	}

	/*---------------------------- banners -----------------------*/
	public function home_banners()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data['page_name'] = 'Home Banners';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['home_banner'] = $this->common->select('home_banner', '*', array('home_banner_active' => 1), true);
		$data['commonModel'] = $this->common;
		echo view('admin/home_banners', $data);
	}

	public function add_home_banner()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data['page_name'] = 'Add Home Banner';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));


		echo view('admin/add_home_banner', $data);
	}

	public function edit_home_banner($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data['page_name'] = 'Edit Home Banner';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));


		$data['home_banner'] = $this->common->select('home_banner', '*', array('home_banner_active' => 1, 'home_banner_id' => $id));
		echo view('admin/add_home_banner', $data);
	}

	public function attempt_home_banner()
	{

		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$rules = [
			'top_image' => ['label' => 'Upload Image', 'rules' => 'required'],
			'top_image1' => ['label' => 'Upload Website Image', 'rules' => 'required']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$home_banner_id = !empty($_POST['home_banner_id']) ? xss_clean($_POST['home_banner_id']) : NULL;

		$top_image = !empty($_POST['top_image']) ? xss_clean($_POST['top_image']) : NULL;
		$top_image1 = !empty($_POST['top_image1']) ? xss_clean($_POST['top_image1']) : NULL;
		$data = [
			'home_banner_image' => $top_image,
			'home_banner_web_image' => $top_image1
		];

		if (!empty($home_banner_id)) {
			$data['home_banner_updated_at'] = date('Y-m-d H:i:s');
			$result = $this->common->update_data('home_banner', $data, array('home_banner_id' => $home_banner_id));
			$msg = 'Home Banner updated successfully!';
		} else {
			$data['home_banner_created_at'] = date('Y-m-d H:i:s');
			$result = $this->common->insert_data('home_banner', $data);
			$msg = 'Home Banner added successfully!';
		}

		if ($result) {
			return redirect()->to(base_url('admin/home_banners'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function delete_home_banner()
	{
		$data = [];
		$home_banner_id = $this->request->getPost('home_banner_id');
		if (!empty($home_banner_id)) {
			try {
				$this->common->delete_data('home_banner', array('home_banner_id' => $home_banner_id));
				$data['success'] = 'success';
				$data['message'] = 'Home banner deleted successfully!';
			} catch (\Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Home banner is required';
		}

		echo json_encode($data);
		exit;
	}

	/*---------------------------- End banners -----------------------*/

	public function sports()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['sports'] = $this->common->select('sports', '*', array('sports_active' => 1), true);
		//print_array($data['sports']);die();
		echo view('master_entries/sports', $data);
	}

	public function edit_sports($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['sports'] = $this->common->select('sports', '*', array('sports_active' => 1), true);
		$data['sport'] = $this->common->select('sports', '*', array('sports_id' => $id));
		$data['edit'] = 'yes';
		//print_array($data['sports']);die();
		echo view('master_entries/sports', $data);
	}

	public function attempt_add_sports($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'name'	=>  ['label' => 'Sports Name', 'rules' => 'required|min_length[3]']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$mfr_name = trim($this->request->getPost('name'));
		$condition = array('sports_name' => $mfr_name);
		if (!empty($id)) {
			$condition = array('sports_name' => $mfr_name, 'sports_id !=' => $id);
		}
		$sports_exist = $this->common->select('sports', '*', $condition);
		if (!empty($sports_exist)) {
			return redirect()->back()->withInput()->with('error', 'Sports already exist');
		}
		$data = [
			'sports_name' => xss_clean($mfr_name)
		];

		if (!empty($this->request->getFile('image'))) {
			$folderpath = FCPATH . 'public/uploads/images/sports';
			$imageFile = $this->request->getFile('image');
			if ($imageFile->isValid() && ! $imageFile->hasMoved()) {
				$filename = $imageFile->getRandomName();
				$imageFile->move($folderpath, $filename);
				$data['sports_image'] = $filename;
			}
		}

		if (!empty($id)) {
			$result = $this->common->update_data('sports', $data, array('sports_id' => $id));
		} else {
			$result = $this->common->insert_data('sports', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'Sports updated successfully!';
			} else {
				$msg = 'Sports added successfully!';
			}
			return redirect()->to(base_url('admin/sports'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}

	public function country()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		//print_array($data['sports']);die();
		echo view('master_entries/country', $data);
	}

	public function edit_country($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['country'] = $this->common->select('countries', '*', array('country_id' => $id));
		$data['edit'] = 'yes';
		//print_array($data['sports']);die();
		echo view('master_entries/country', $data);
	}

	public function attempt_add_country($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'name'	=>  ['label' => 'Country Name', 'rules' => 'required|min_length[3]|is_unique[countries.country_name]', 'errors' => ['is_unique' => 'Country already exist']],
			'code'	=>  ['label' => 'Country Mobile Code', 'rules' => 'required']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		$country_name = trim($this->request->getPost('name'));
		$mobile_code = trim($this->request->getPost('code'));

		$data = [
			'country_name' => xss_clean($country_name),
			'country_mobile_code' => xss_clean($mobile_code)
		];

		if (!empty($id)) {
			$result = $this->common->update_data('countries', $data, array('country_id' => $id));
		} else {
			$result = $this->common->insert_data('countries', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'Country updated successfully!';
			} else {
				$msg = 'Country added successfully!';
			}
			return redirect()->to(base_url('admin/country'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}
	/*-------------------------------- states-------------------------------------*/

	public function states()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array(), true);
		$data['states'] = $this->common->select('state', '*', array(), true);
		$data['commonModel'] = $this->common;
		//print_array($data['sports']);die();
		echo view('master_entries/states', $data);
	}

	public function edit_states($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['commonModel'] = $this->common;

		$data['state'] = $this->common->select('state', '*', array('state_id' => $id));
		$data['edit'] = 'yes';
		//print_array($data['sports']);die();
		echo view('master_entries/states', $data);
	}

	public function attempt_add_states($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'country'	=>  ['label' => 'Country', 'rules' => 'required'],
			'state'	=>  ['label' => 'State name', 'rules' => 'required|min_length[2]']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$date = date('Y-m-d H:i:s');
		$city_name = trim($this->request->getPost('state'));
		$country = trim($this->request->getPost('country'));

		$data = [
			'state_name' => xss_clean($city_name),
			'state_country_id' => xss_clean($country)
		];

		$cond = array('state_country_id' => $country, 'state_name' => $city_name);
		if (!empty($id)) {
			$cond['state_id !='] = $id;
		}
		$city_exist = $this->common->select('state', '*', $cond);
		if (!empty($city_exist)) {
			return redirect()->back()->withInput()->with('error', 'State already exist');
		}

		if (!empty($id)) {
			$data['state_updated_at'] = $date;
			$result = $this->common->update_data('state', $data, array('state_id' => $id));
		} else {
			$data['state_created_at'] = $date;
			$result = $this->common->insert_data('state', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'State updated successfully!';
			} else {
				$msg = 'State added successfully!';
			}
			return redirect()->to(base_url('admin/states'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}
	/*--------------------------------- cities -----------------------------------*/

	public function cities()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['cities'] = $this->common->select('cities', '*', array('city_active' => 1), true);
		$data['commonModel'] = $this->common;
		//print_array($data['sports']);die();
		echo view('master_entries/cities', $data);
	}

	public function edit_cities($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['cities'] = $this->common->select('cities', '*', array('city_active' => 1), true);
		$data['commonModel'] = $this->common;

		$data['city'] = $this->common->select('cities', '*', array('city_id' => $id));
		$data['edit'] = 'yes';
		//print_array($data['sports']);die();
		echo view('master_entries/cities', $data);
	}

	public function attempt_add_cities($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'country'	=>  ['label' => 'Country', 'rules' => 'required'],
			'state'	=>  ['label' => 'Country', 'rules' => 'required'],
			'city'	=>  ['label' => 'City name', 'rules' => 'required|min_length[2]']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$state_name = trim($this->request->getPost('state'));
		$city_name = trim($this->request->getPost('city'));
		$country = trim($this->request->getPost('country'));

		$data = [
			'city_name' => xss_clean($city_name),
			'city_state_id' => xss_clean($state_name),
			'city_country_id' => xss_clean($country)
		];
		$cond = array('city_state_id' => $state_name, 'city_name' => $city_name);
		if (!empty($id)) {
			$cond['city_id !='] = $id;
		}
		$city_exist = $this->common->select('cities', '*', $cond);
		if (!empty($city_exist)) {
			return redirect()->back()->withInput()->with('error', 'City already exist');
		}

		if (!empty($id)) {
			$result = $this->common->update_data('cities', $data, array('city_id' => $id));
		} else {
			$result = $this->common->insert_data('cities', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'City updated successfully!';
			} else {
				$msg = 'City added successfully!';
			}
			return redirect()->to(base_url('admin/cities'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}
	public function attempt_add_districts($id = null)
	{
		//echo '<pre>';print_r($this->request);die();
		$rules = [
			'country'	=>  ['label' => 'Country', 'rules' => 'required'],
			'state'	=>  ['label' => 'Country', 'rules' => 'required'],
			'district'	=>  ['label' => 'District name', 'rules' => 'required|min_length[2]']
		];


		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$state_name = trim($this->request->getPost('state'));
		$district_name = trim($this->request->getPost('district'));
		$country = trim($this->request->getPost('country'));

		$data = [
			'district_name' => xss_clean($district_name),
			'district_state_id' => xss_clean($state_name),
			'district_country_id' => xss_clean($country)
		];
		$cond = array('district_state_id' => $state_name, 'district_name' => $district_name);
		if (!empty($id)) {
			$cond['district_id !='] = $id;
		}
		$district_exist = $this->common->select('districts', '*', $cond);
		if (!empty($district_exist)) {
			return redirect()->back()->withInput()->with('error', 'City already exist');
		}

		if (!empty($id)) {
			$result = $this->common->update_data('districts', $data, array('district_id' => $id));
		} else {
			$result = $this->common->insert_data('districts', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'District updated successfully!';
			} else {
				$msg = 'District added successfully!';
			}
			return redirect()->to(base_url('admin/districts'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}


	public function organizer_list($slug = NULL)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$condition = array('user_role_id' => 3);
		if (!empty($slug)) {
			if ($slug == 'new') {
				$condition = array('user_role_id' => 3, 'user_active' => 0);
			}
			if ($slug == 'verified') {
				$condition = array('user_role_id' => 3, 'user_active' => 1);
			}
		}
		$data['organizers'] = $this->common->select('users', '*', $condition, true, 'user_id DESC');
		//print_array($data['organizers']);die();
		echo view('admin/organizer_list', $data);
	}

	public function tournament_list($slug = NULL)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$item_count = $this->tournament->count_admin_tournament_list($slug);
		//echo $item_count;exit;
		$data['total'] = $item_count;
		$per_page = 5;
		$page = $this->request->getGET('page');
		$offset = ($page * $per_page) - $per_page;
		if (empty($page)) {
			$page = 1;
			$offset = 0;
		}
		$data['page'] = $page;
		$data['perPage'] = $per_page;
		$data['tournaments'] = $this->tournament->get_admin_tournament_list($per_page, $offset, $slug);
		//print_array($data['tournaments']);die();
		echo view('admin/tournament_list', $data);
	}


	public function inactive_tournament()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data = [];
		$user_id = $this->request->getPost('id');
		if (!empty($user_id)) {
			try {

				$this->common->update_data('listings', array('listing_active' => 0), array('listing_id' => $user_id));
				$data['success'] = 'success';
				$data['message'] = 'Tournament inactive successfully!';
			} catch (\Exception $e) {
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

	public function active_tournament()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$data = [];
		$user_id = $this->request->getPost('id');
		if (!empty($user_id)) {
			try {

				$this->common->update_data('listings', array('listing_active' => 1), array('listing_id' => $user_id));
				$data['success'] = 'success';
				$data['message'] = 'Tournament Activated successfully!';
			} catch (\Exception $e) {
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

	public function participants($list_id)
	{
		if ((session()->get('role_id') != 1) && (session()->get('role_id') != 3)) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$per_page = $offset = '';
		$data['commonModel'] = $this->common;
		$data['tourneyModel'] = $this->tournament;
		$data['participants'] = $this->common->select('participants', '*', array('participant_list_id' => $list_id), true);
		//print_array($data['participants']);die();
		echo view('admin/participants_list', $data);
	}

	public function events()
	{
		
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['events'] = $this->common->select('events', '*', array('event_active' => 1), true);

		echo view('master_entries/events', $data);
	}

	public function edit_events($id)
	{
		
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['events'] = $this->common->select('events', '*', array('event_active' => 1), true);
		$data['event'] = $this->common->select('events', '*', array('event_id' => $id));
		$data['edit'] = 'yes';

		echo view('master_entries/events', $data);
	}
	public function view_events($id)
	{
		
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['events'] = $this->common->select('events', '*', array('event_active' => 1), true);
		$data['event'] = $this->common->select('events', '*', array('event_id' => $id));
		$data['edit'] = 'yes';

		echo view('master_entries/view_events', $data);
	}

	public function attempt_add_events($id = null)
	{
		$mfr_name = trim($this->request->getPost('name'));
		$rules = [																					
			'name' => ['label' => 'Event Name', 'rules' => 'required|min_length[3]|is_unique[events.event_name,event_name,'.$mfr_name.']']
		];
		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$name = $this->common->select('events', '*', array('event_name' => $mfr_name));
		if ($name) {
			return redirect()->back()->withInput()->with('errors', 'event name already exist');
		}

		$user_id = session()->get('user_id');
		$data = [
			'event_name' => xss_clean($mfr_name),
			'event_user_id' => $user_id,
		];
		if (!empty($this->request->getFile('event_image'))) {
			$folderpath = FCPATH . '/public/uploads/images/events';
			$imageFile = $this->request->getFile('event_image');
			if ($imageFile->isValid() && ! $imageFile->hasMoved()) {
				$filename = $imageFile->getRandomName();
				$imageFile->move($folderpath, $filename);
				$data['event_image'] = $filename;
			}
		}

		if (!empty($id)) {
			$result = $this->common->update_data('events', $data, array('event_id' => $id));
		} else {
			$result = $this->common->insert_data('events', $data);
		}
		if ($result) {
			if (!empty($id)) {
				$msg = 'Event updated successfully!';
			} else {
				$msg = 'Event added successfully!';
			}
			return redirect()->to(base_url('admin/events'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. please try again.');
		}
	}


	public function payments()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['body_class'] = 'skin-default card-no-border';
		$data['commonModel'] = $this->common;

		$org_id = !empty($_GET['organizer']) ? $_GET['organizer'] : '';

		$item_count = $this->tournament->count_payments($org_id);

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
		$data['payments'] = $this->tournament->get_payments($per_page, $offset, $org_id);

		echo view('admin/payments', $data);
	}

	public function booking_history()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$data['body_class'] = 'skin-default card-no-border';
		$data['commonModel'] = $this->common;

		$org_id = !empty($_GET['organizer']) ? $_GET['organizer'] : '';

		$data['organizers'] = $this->common->select('users', '*', array('user_role_id' => 3), true);

		$item_count = $this->common->count_booking_history(false, $org_id);
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
		$data['bookings'] = $this->common->get_booking_history($per_page, $offset, false, $org_id);

		echo view('admin/booking_history', $data);
	}
	/*------------------------------ General seetings -------------------------*/
	public function general_settings()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'General Settings';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

		$free_booking = $this->common->select('settings', 'settings_value', array('settings_variable' => 'free_booking'));
		if (!empty($free_booking)) {
			$data['free_booking'] = $free_booking->settings_value;
		}


		echo view('admin/general_settings', $data);
	}



	public function attempt_general_settings()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$free_booking = !empty($_POST['free_booking']) ? 1 : 0;
		$data = array(
			'settings_value' => $free_booking
		);

		$this->common->update_data('settings', $data, array('settings_variable' => 'free_booking'));
		$msg = 'Setting Updated Successfully';

		return redirect()->to(base_url('admin/general_settings'))->with('success_message', $msg);
	}

	/*------------------------------ Notifications ----------------------------*/

	public function notifications()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['page_name'] = 'Notifications';
		$user_id = session()->get('user_id');
		$data['messages'] = $this->common->select('messages', '*', array('msg_active' => 1), true, 'msg_id DESC');

		echo view('admin/notifications', $data);
	}


	public function get_notifications()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url('admin'));
		}
		$search  = $from = $to = NULL;
		$offset = $_POST['start'];
		$limit = $_POST['length'];
		$draw = $_POST['draw'];
		if (!empty($_POST['search']['value'])) {
			$search = $_POST['search']['value'];
		}

		$bulkData = array();
		$item_count = $this->common->notifications_count($search);
		$customers = $this->common->get_notifications($limit, $offset, $search);

		$bulkData['draw'] = intval($draw);
		$bulkData['recordsTotal'] = $item_count;
		$bulkData['recordsFiltered'] = $item_count;
		$details = array();
		if (!empty($customers)) {
			$i = 1;
			foreach ($customers as $data) {
				$url = '';
				$img = !empty($data->msg_image) ? $data->msg_image : '';
				if (!empty($img)) {
					$url = base_url() . '/public/uploads/images/notifications/' . $img;
				}
				$row = array();
				$row['id'] = $data->msg_id;
				$row['title'] = !empty($data->msg_title) ? $data->msg_title : '';
				$row['desc'] = !empty($data->msg_desc) ? $data->msg_desc : '';
				$row['image'] = !empty($data->msg_desc) ? '<img src="' . $url . '" height="60"/>' : '';
				$del = !empty($data->msg_id) ? $data->msg_id : '';
				$row['action'] = '<a href="javascript:void(0)" onclick="delete_notification(' . $del . ')" class="btn btn-sm btn-danger">Delete</a>';

				$details[] = $row;
				$i++;
			}
		}
		$bulkData['data'] = $details;
		$bulkData['csrf_token'] = csrf_hash();

		echo json_encode($bulkData);
	}

	public function add_notification()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['page_name'] = 'Add Notification';
		$user_id = session()->get('user_id');
		echo view('admin/add_notification', $data);
	}

	public function attempt_notification()
	{

		$rules = [
			'title'	=> ['label' => 'Title', 'rules' => 'required|min_length[4]'],
			'desc' => ['label' => 'Description', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$user_id = session()->get('user_id');
		$role = !empty($_POST['role']) ? $_POST['role'] : NULL;
		$title = !empty($_POST['title']) ? $_POST['title'] : NULL;
		$desc = !empty($_POST['desc']) ? xss_clean($_POST['desc']) : NULL;
		$date = date('Y-m-d H:i:s');
		$data = [
			'msg_title' => $title,
			'msg_desc' => $desc,
			'msg_created_at' => $date
		];

		$result = $this->common->insert_data('messages', $data);
		if ($result) {
			if ($_FILES['image']['error'] !== 4) {
				$folderpath = FCPATH . 'public/uploads/images/notifications';
				if (create_folder($folderpath)) {
					$foldername = $folderpath;
				} else {
					$foldername = FCPATH . 'public/uploads/images/notifications';
				}

				$imageFile = $this->request->getFile('image');
				$filename = $imageFile->getRandomName();

				if ($imageFile->move($foldername, $filename)) {
					$this->common->update_data('messages', array('msg_image' => $filename), array('msg_id' => $result));
				}
			}
			$sub = $title;
			$msg = $desc;
			$attach = '';
			if (!empty($filename)) {
				$attach = base_url('public/uploads/images/notifications/' . $filename);
			}
			$condition = array('user_active' => 1);
			if (!empty($role)) {
				$condition['user_role_id'] = $role;
			} else {
				$condition['user_role_id'] = 2;
			}
			$users = $this->common->select('users', 'user_id,fcm_id', $condition, true);
			if (!empty($users)) {
				$fcmarray = [];
				foreach ($users as $data) {
					$data2 = [
						'notify_msg_id' => $result,
						'notify_for' => $data->user_id,
						'notify_from' => $user_id,
						'notify_time' => $date
					];
					$this->common->insert_data('notifications', $data2);

					if (!empty($data->fcm_id)) {
						$fcm_id = $data->fcm_id;
						array_push($fcmarray, $fcm_id);
					}
				}

				if (!empty($fcmarray)) {
					$this->notification->sendNotification($fcmarray, $sub, $msg, $attach);
				}
			}
			session()->set('success_message', 'Notification added successfully!');
			return redirect()->to(base_url('admin/notifications'));
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function delete_notification()
	{
		$data = [];
		$notify_id = $this->request->getPost('id');
		if (!empty($notify_id)) {
			try {
				$img = $this->common->select('messages', '*', array('msg_id' => $notify_id));
				if (!empty($img)) {
					if (!empty($img->msg_image)) {
						$image = $img->msg_image;
						$folderpath = FCPATH . 'uploads/images/notifications/' . $image;
						if (file_exists($folderpath)) {
							unlink($folderpath);
						}
					}
				}

				if ($this->common->delete_data('messages', array('msg_id' => $notify_id))) {
					$this->common->delete_data('notifications', array('notify_msg_id' => $notify_id));
				}
				$data['success'] = 'success';
				$data['message'] = 'Notification deleted successfully!';
			} catch (\Exception $e) {
				$data['error'] = 'fail';
				$data['message'] = $e->getMessage();
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'Notification is required';
		}

		echo json_encode($data);
		exit;
	}

	/*-------------------------- Contact us ------------------------------*/
	public function contact_us()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'Contact Us';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$contact_us = $this->common->select('settings', 'settings_value', array('settings_variable' => 'contact_us'));
		if (!empty($contact_us)) {
			$data['contact_us'] = json_decode($contact_us->settings_value);
		}


		echo view('admin/contact_us', $data);
	}



	public function attempt_contact_us()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'phone' => ['label' => 'phone', 'rules' => 'required|min_length[5]'],
			'email' => ['label' => 'email', 'rules' => 'required|valid_email'],
			'address' => ['label' => 'address', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$data = array(
			'settings_value' => json_encode($_POST)
		);
		//print_array($data);die();
		$res = $this->common->update_data('settings', $data, array('settings_variable' => 'contact_us'));
		$msg = 'Contact Updated Successfully';

		if (!empty($res)) {
			return redirect()->to(base_url('admin/contact_us'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again');
		}
	}

	public function about_us()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['page_name'] = 'About Us';
		$data['about_us'] = $this->common->select('about_us', '*', array('about_us_active' => 1));
		//$data['commonModel'] = $this->common;
		//print_array($data['tournaments']);die();
		echo view('admin/about_us', $data);
	}

	public function attempt_about_us()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'title'	=> ['label' => 'Title', 'rules' => 'required|min_length[4]'],
			'desc' => ['label' => 'Description', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$title = !empty($_POST['title']) ? $_POST['title'] : NULL;
		$desc = !empty($_POST['desc']) ? xss_clean($_POST['desc']) : NULL;

		$data = [
			'about_us_title' => $title,
			'about_us_desc' => $desc
		];
		$this->common->update_data('about_us', array('about_us_active' => 0), array('about_us_active' => 1));
		$data['about_us_created_at'] = date('Y-m-d H:i:s');
		$result = $this->common->insert_data('about_us', $data);
		$msg = 'About us added successfully!';

		if ($result) {
			return redirect()->to(base_url('admin/about_us'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function faqs()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$data['page_name'] = 'FAQ';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['faqs'] = $this->common->select('faqs', '*', array('faq_active' => 1), true);

		echo view('admin/faqs', $data);
	}

	public function edit_faq($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';

		$data['page_name'] = 'FAQ';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['faqs'] = $this->common->select('faqs', '*', array('faq_active' => 1), true);
		$data['faq'] = $this->common->select('faqs', '*', array('faq_id' => $id));

		echo view('admin/faqs', $data);
	}

	public function attempt_faq()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'question'	=> ['label' => 'Question', 'rules' => 'required|min_length[4]'],
			'answer' => ['label' => 'Answer', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$faq_id = !empty($_POST['faq_id']) ? $_POST['faq_id'] : NULL;

		$question = !empty($_POST['question']) ? xss_clean($_POST['question']) : NULL;
		$answer = !empty($_POST['answer']) ? xss_clean($_POST['answer']) : NULL;
		$data = [
			'faq_question' => $question,
			'faq_answer' => $answer
		];
		if (!empty($faq_id)) {
			$data['faq_updated_at'] = date('Y-m-d H:i:s');
			$result = $this->common->update_data('faqs', $data, array('faq_id' => $faq_id));
			$msg = 'Faq updated successfully!';
		} else {
			$data['faq_created_at'] = date('Y-m-d H:i:s');
			$result = $this->common->insert_data('faqs', $data);
			$msg = 'Faq added successfully!';
		}

		if ($result) {
			return redirect()->to(base_url('admin/faqs'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function delete_faq()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$data = [];
		$faq_id = $this->request->getPost('faq_id');
		if (!empty($faq_id)) {
			try {

				$this->common->delete_data('faqs', array('faq_id' => $faq_id));
				$data['success'] = 'success';
				$data['message'] = 'FAQ deleted successfully!';
			} catch (\Exception $e) {

				$data['error'] = 'fail';
				$data['message'] = 'Something went wrong. Try again.';
			}
		} else {
			$data['error'] = 'fail';
			$data['message'] = 'FAQ Id is required';
		}

		echo json_encode($data);
		exit;
	}

	public function terms_conditions()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';

		$data['page_name'] = 'Terms & Conditions';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['terms_conditions'] = $this->common->select('terms_conditions', '*', array('terms_conditions_active' => 1));

		echo view('admin/terms_conditions', $data);
	}

	public function attempt_terms_conditions()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'desc' => ['label' => 'Description', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$desc = !empty($_POST['desc']) ? xss_clean($_POST['desc']) : NULL;

		$data = [
			'terms_conditions_desc' => $desc
		];
		$this->common->update_data('terms_conditions', array('terms_conditions_active' => 0), array('terms_conditions_active' => 1));
		$data['terms_conditions_created_at'] = date('Y-m-d H:i:s');
		$result = $this->common->insert_data('terms_conditions', $data);
		$msg = 'Terms and conditions added successfully!';

		if ($result) {
			return redirect()->to(base_url('admin/terms_conditions'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}

	public function privacy_policy()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';

		$data['page_name'] = 'Privacy Policy';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['privacy_policy'] = $this->common->select('privacy_policy', '*', array('privacy_policy_active' => 1));

		echo view('admin/privacy_policy', $data);
	}

	public function attempt_privacy_policy()
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}

		$rules = [
			'desc' => ['label' => 'Description', 'rules' => 'required|min_length[4]']
		];

		if (! $this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		$desc = !empty($_POST['desc']) ? xss_clean($_POST['desc']) : NULL;

		$data = [
			'privacy_policy_desc' => $desc
		];
		$this->common->update_data('privacy_policy', array('privacy_policy_active' => 0), array('privacy_policy_active' => 1));
		$data['privacy_policy_created_at'] = date('Y-m-d H:i:s');
		$result = $this->common->insert_data('privacy_policy', $data);
		$msg = 'Privacy policy added successfully!';

		if ($result) {
			return redirect()->to(base_url('admin/privacy_policy'))->with('success_message', $msg);
		} else {
			return redirect()->back()->withInput()->with('error_message', 'Something went wrong. Try again');
		}
	}


	public function getEvents()
	{
		$results = [];
		$listing_id = $this->request->getPost('listing_id');
		if (!empty($listing_id)) {
			$results = $this->common->select('listing_events', '*', array('listing_event_listing_id' => $listing_id), true);

			if ($results) {
				foreach ($results as $row) {
					$row->event_name = '';
					$event = $this->common->select('events', '*', array('event_id' => $row->listing_event_title));
					if (!empty($event->event_name)) {
						$row->event_name = $event->event_name;
					}
				}
				echo json_encode(array('success' => 1, 'msg' => $results, 'csrf_token' => csrf_hash()));
			} else {
				echo json_encode(array('success' => 0, 'msg' => $results, 'csrf_token' => csrf_hash()));
			}
		} else {
			echo json_encode(array('success' => 0, 'msg' => $results, 'csrf_token' => csrf_hash()));
		}
	}
	public function refer_back(){
	if (session()->get('role_id') != 1) {
		return redirect()->to(base_url());
	}
	$subject = $this->request->getPost('subject');
	$message = $this->request->getPost('message');
	$user_id = session()->get('user_id');
	$data['users'] = $this->common->refer_back($user_id , $subject , $message);
	echo view('admin/privacy_policy', $data);
}
public function add_districts(){
	if (session()->get('role_id') != 1) {
		return redirect()->to(base_url());
	}
	$data['body_class'] = 'skin-default card-no-border';
	$user_id = session()->get('user_id');
	$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
	$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
	$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
	$data['districts'] = $this->common->select('districts', '*', array('district_active' => 1), true);
	$data['commonModel'] = $this->common;
	echo view('master_entries/districts',$data);
}

public function edit_district($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data['body_class'] = 'skin-default card-no-border';
		$user_id = session()->get('user_id');
		$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
		$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
		$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
		$data['districts'] = $this->common->select('districts', '*', array('district_active' => 1), true);
		$data['commonModel'] = $this->common;

		$data['city'] = $this->common->select('districts', '*', array('district_id' => $id));
		$data['edit'] = 'yes';
		//print_array($data['sports']);die();
		echo view('master_entries/districts', $data);
	}
	public function delete_district($id)
	{
		if (session()->get('role_id') != 1) {
			return redirect()->to(base_url());
		}
		$data = DB::table('districts')
		->where('district_id', $id)
		->delete();	
		$data['body_class'] = 'skin-default card-no-border';
	$user_id = session()->get('user_id');
	$data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));
	$data['countries'] = $this->common->select('countries', '*', array('country_active' => 1), true);
	$data['states'] = $this->common->select('state', '*', array('state_active' => 1), true);
	$data['districts'] = $this->common->select('districts', '*', array('district_active' => 1), true);
	$data['commonModel'] = $this->common;
	
		echo view('master_entries/districts', $data);
	}

}