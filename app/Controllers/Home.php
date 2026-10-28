<?php namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\CommonModel;
use App\Models\TournamentModel;
use App\Libraries\RestLibrary;
class Home extends Controller
{
	protected $rest;
	protected $common;
	protected $tournament;
	public function __construct()
	{
		helper(['form','url','custom']);
        session()->set('redirect_url', current_url(true));
		$this->rest = new RestLibrary();
		$this->common = new CommonModel();
		$this->tournament = new TournamentModel();
	}
    public function admin()
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
		$data=[];
		echo view('user/admin',$data);
	}

    public function index()
    {
    	$data = [];
    	$data['header'] = TRUE;
        $data['footer'] = TRUE;
        $contact_us= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
        if(!empty($contact_us)){
            $data['contact_us'] = json_decode($contact_us->settings_value);
        }
    	$data['commonModel'] = $this->common;
        $data['famous_region'] = $this->common->select('cities','*',array('city_active'=>1),true);
        $user_id = session()->get('user_id');
        $data['users'] = $this->common->select('users','*',array('user_id'=>$user_id));

        $data['home_banners'] = $this->common->select('home_banner','*',array('home_banner_active'=>1),true);
        $data['sports'] = $this->common->select('sports','*',array('sports_active'=>1),true);
        $city_id = !empty(session()->get('city_id'))?session()->get('city_id'):NULL;
        $country = !empty(session()->get('country_id'))?session()->get('country_id'):NULL;

        $sports_id = !empty($this->request->getGET('sports'))?$this->request->getGET('sports'):NULL;
        $completed = !empty($this->request->getGET('status'))?$this->request->getGET('status'):NULL;
        //echo $completed;die();
        $query = !empty($this->request->getGET('q'))?$this->request->getGET('q'):NULL;
        
        //echo $completed_date;die();
        $item_count = $this->tournament->count_all_listings($city_id, $country, $sports_id, $completed, $query);
        //echo $item_count;exit;
        $data['total'] = $item_count;
        $per_page = 25;
        $page = $this->request->getGET('page');
        $offset = ($page*$per_page)-$per_page;
        if(empty($page)){
            $page = 1;
            $offset = 0;

        }
        $data['page'] = $page;
        $data['perPage'] = $per_page;
        $data['tournaments'] = $this->tournament->get_tournament_home($per_page, $offset, $city_id, $country, $sports_id, $completed, $query);
        echo view('home',$data);
    }

    public function tournament_details($list_id)
    {
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

    	$data['tournaments'] = $this->tournament->get_tournament(false, $list_id);
    	$data['commonModel'] = $this->common;
    	$data['tourneyModel'] = $this->tournament;
        echo view('tournament_details',$data);
    }

    public function view_all_tournaments($listing_user_id=null)
    {
        $data = [];
        
        $contact_us= $this->common->select('settings','settings_value',array('settings_variable'=>'contact_us'));
        if(!empty($contact_us)){
            $data['contact_us'] = json_decode($contact_us->settings_value);
        }
        $data['commonModel'] = $this->common;
        $data['famous_region'] = $this->common->select('cities','*',array('city_famous'=>1,'city_active'=>1),true);
        $user_id = session()->get('user_id');
        $data['users'] = $this->common->select('users','*',array('user_id'=>$user_id));

        $data['sports'] = $this->common->select('sports','*',array('sports_active'=>1),true);
        $city_id = !empty(session()->get('city_id'))?session()->get('city_id'):NULL;
        $country = !empty(session()->get('country_id'))?session()->get('country_id'):NULL;

        $sports_id = !empty($this->request->getGET('sports'))?$this->request->getGET('sports'):NULL;
        $completed = !empty($this->request->getGET('status'))?$this->request->getGET('status'):NULL;
        $query = !empty($this->request->getGET('q'))?$this->request->getGET('q'):NULL;
        $completed_date = '';
        if(!empty($completed)){
            $completed_date = date('Y-m-d H:i:s'); 
        }
        //echo $completed_date;die();
        $item_count = $this->tournament->count_view_all_tournaments($listing_user_id);
        //echo $item_count;exit;
        $data['total'] = $item_count;
        $per_page = 3;
        $page = $this->request->getGET('page');
        $offset = ($page*$per_page)-$per_page;
        if(empty($page)){
            $page = 1;
            $offset = 0;

        }
        $data['page'] = $page;
        $data['perPage'] = $per_page;
        $data['tournaments'] = $this->tournament->view_all_tournaments($per_page, $offset, $listing_user_id);
        $data['organizer_details'] = $this->common->select('users','*',array('user_id'=>$listing_user_id));
        //print_array($data['tournaments']);die();
        echo view('organizer/view_all_tournaments',$data);
    }

    /**
     * get organizers
     */
    public function get_organizers($id=null)
    {
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

        $condition = array('user_role_id'=>3);
        // $condition = array('user_role_id'=>3,'user_active' => 1);
        if(!empty($id)){
            $condition = array('user_id'=>$id,'user_role_id'=>3,'user_active' => 1);
            $data['organizers'] = $this->common->select('users','*',$condition);
        }else{
            $data['organizers'] = $this->common->select('users','*',$condition,true,'user_id DESC');
        }
        
        echo view('organizers',$data);

    }
    
    
    
    /**
     * privacy_policy
     */
    public function privacy_policy()
    {
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

        $condition = array('privacy_policy_active' => 1);
        $data['privacy_policy'] = $this->common->select('privacy_policy','*',$condition);
        
        echo view('common/privacy_policy',$data);

    }
    
    public function about_us()
    {
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

        $condition = array('about_us_active' => 1);
        $data['about_us'] = $this->common->select('about_us','*',$condition);
        
        echo view('common/about_us',$data);

    }
    public function delete_account()
    {
        echo view('common/delete_account');

    }
    
    public function faqs()
    {
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

        $condition = array('faq_active' => 1);
        $data['faq'] = $this->common->select('faqs','*',$condition,true);
        
        echo view('common/faq',$data);

    }
    
    public function terms_condition()
    {
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
        
        $condition = array('terms_conditions_active' => 1);
        $data['terms_conditions'] = $this->common->select('terms_conditions','*',$condition);
        
        echo view('common/terms_condition',$data);

    }

    
   
}
