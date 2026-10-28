<?php

namespace App\Models;

use CodeIgniter\Model;

class TournamentModel extends Model
{
    public function count_all_listings($city=NULL, $country=NULL, $sports_id=NULL, $completed=NULL, $search=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','inner');
        if(!empty($city)){
            $builder->where('l.listing_city_id', $city);
        }
        if(!empty($country)){
            $builder->where('l.listing_country_id', $country);
        }
        if(!empty($sports_id)){
            $builder->where('l.listing_sports_id', $sports_id);
        }
        if(!empty($completed) && ($completed == 'completed')){
            $builder->where('l.listing_end_date <=', date('Y-m-d H:i:s'));
        }else{ 
            $builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
        }
        if(!empty($search)){
            $builder->like('l.listing_title', $search);
        }
        $builder->where('l.listing_active', 1);
        
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_start_date', 'ASC');
        return $builder->countAllResults();
    }
    public function get_tournament_home($per_page=NULL, $offset=NULL, $city=NULL, $country=NULL, $sports_id=NULL, $completed=NULL, $search=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','inner');
        if(!empty($city)){
            $builder->where('l.listing_city_id', $city);
        }
        if(!empty($country)){
            $builder->where('l.listing_country_id', $country);
        }
        if(!empty($sports_id)){
            $builder->where('l.listing_sports_id', $sports_id);
        }
        if(!empty($completed) && ($completed == 'completed')){
            $builder->where('l.listing_end_date <=', date('Y-m-d H:i:s'));
        }else{ 
            $builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
        }
        if(!empty($search)){
            $builder->like('l.listing_title', $search);
        }
        $builder->where('l.listing_active', 1);
        $builder->limit($per_page,$offset); 
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_start_date', 'ASC');
        $query=$builder->get();
        // echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $results = $query->getResult(); 
               foreach($results as $result){
                $builder = $this->db->table('listing_events  as le');
                $builder->select('e.event_name');
                $builder->join('events as e','e.event_id = le.listing_event_title','inner');
                $builder->where('le.listing_event_listing_id', $result->listing_id);
                $query=$builder->get();
                if($query->getNumRows()>0){
                     $result->events = $query->getResult();
                }else{
                    $result->events = [];
                }
               }
            return $results;
        }
        return false;
    }

    public function count_view_all_tournaments($user_id=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','inner');
        if(!empty($user_id)){
            $builder->where('l.listing_user_id', $user_id);
        }
        $builder->where('l.listing_active', 1);
        //$builder->limit($per_page,$offset); 
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_id', 'DESC');
        return $builder->countAllResults();
    }
    public function view_all_tournaments($per_page=NULL, $offset=NULL, $user_id=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','inner');
        
        if(!empty($user_id)){
            $builder->where('l.listing_user_id', $user_id);
        }

        $builder->where('l.listing_active', 1);
        $builder->limit($per_page,$offset); 
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_id', 'DESC');
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $results = $query->getResult(); 
               foreach($results as $result){
                    $builder = $this->db->table('listing_events  as le');
                    $builder->select('e.event_name');
                    $builder->join('events as e','e.event_id = le.listing_event_title','inner');
                    $builder->where('le.listing_event_listing_id', $result->listing_id);
                    $query=$builder->get();
                    if($query->getNumRows()>0){
                         $result->events = $query->getResult();
                    }else{
                        $result->events = [];
                    }
                }
            return $results;
        }
        return false;
    }
    public function get_tournament($user_id=null, $list_id=null, $sports_id=NULL, $status=NULL, $search=NULL)
    {
        $columns = array('l.listing_title','c.city_name','s.sports_name');
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
        if(!empty($user_id)){
            $builder->where('l.listing_user_id', $user_id);
        }
         if(!empty($list_id)){
            $builder->where('l.listing_id', $list_id);
        }
        if(!empty($sports_id)){
            $builder->where('l.listing_sports_id', $sports_id);
        }
        if(!empty($status)){
            if($status == 'completed'){
                $builder->where('l.listing_end_date <=', date('Y-m-d H:i:s'));
            }else{ 
                $builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
            }
        } 
        if(!empty($search)){
            $i=0;
            foreach ($columns as $column) {
                if($i==0){
                    $builder->like('LOWER(' .$column. ')', strtolower($search), 'both'); 
                }else{
                    $builder->orLike('LOWER(' .$column. ')', strtolower($search), 'both'); 
                }
                
                $i++;
            }
        }
        $builder->where('l.listing_active', 1);
        /*$builder->limit($per_page,$offset);*/ 
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_id', 'DESC');
        $query=$builder->get();
        if($query->getNumRows()>0){
            if(!empty($list_id)){
             $result = $query->getRow();
            }else{
               $result = $query->getResult(); 
            }
            return $result;
        }
        return false;
    }

    public function get_events($tourney_id=null,)
    {
        $builder = $this->db->table('listing_events  as le');
        $builder->select('le.*,e.*,l.*,s.*,c.*,co.*,u.*');
        $builder->join('listings as l','l.listing_id = le.listing_event_listing_id and l.listing_active=1','inner');
        $builder->join('events as e','e.event_id = le.listing_event_title and e.event_active=1','inner');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
        if(!empty($tourney_id)){
            $builder->where('le.listing_event_listing_id', $tourney_id);
        }
        $builder->where('le.listing_event_active', 1);
        /*$builder->limit($per_page,$offset);*/ 
        $builder->groupBy('le.listing_event_id');
        $builder->orderBy('le.listing_event_id', 'DESC');
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
             $result = $query->getResult();
            return $result;
        }
        return false;
    }
    
    
    public function get_tournament_list($per_page=NULL, $offset=NULL, $status=NULL)
    {
        $now = date('Y-m-d H:i:s');
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
       
        if($status == 'upcoming'){
            $builder->where('l.listing_start_date >', $now);
        }
        if($status == 'completed'){
            $builder->where('l.listing_end_date <', $now);
        }
        if($status == 'ongoing'){
            $builder->where('l.listing_start_date <', $now);
            $builder->where('l.listing_end_date >', $now);
        }
        //$builder->where('l.listing_end_date <=', $completed);
        //$builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
        
        $builder->where('l.listing_active', 1);
        //$builder->limit($per_page,$offset); 
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_id', 'DESC');
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }


    public function count_admin_tournament_list($status=NULL)
    {
        $now = date('Y-m-d H:i:s');
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
       
        if($status == 'upcoming'){
            $builder->where('l.listing_start_date >', $now);
        }
        if($status == 'completed'){
            $builder->where('l.listing_end_date <', $now);
        }
        if($status == 'ongoing'){
            $builder->where('l.listing_start_date <', $now);
            $builder->where('l.listing_end_date >', $now);
        }
        return $builder->countAllResults();
        
    }


    public function get_admin_tournament_list($per_page=NULL, $offset=NULL, $status=NULL)
    {
        $now = date('Y-m-d H:i:s');
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
       
        if($status == 'upcoming'){
            $builder->where('l.listing_start_date >', $now);
        }
        if($status == 'completed'){
            $builder->where('l.listing_end_date <', $now);
        }
        if($status == 'ongoing'){
            $builder->where('l.listing_start_date <', $now);
            $builder->where('l.listing_end_date >', $now);
        }
        //$builder->where('l.listing_end_date <=', $completed);
        //$builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
        
        $builder->limit($per_page,$offset); 
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_id', 'DESC');
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }

    public function search_tournament($per_page=NULL, $offset=NULL, $sports=NULL, $city_id=NULL, $tstatus=NULL, $search=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
        if(!empty($user_id)){
            $builder->where('l.listing_user_id', $user_id);
        }
         if(!empty($list_id)){
            $builder->where('l.listing_id', $list_id);
        }
        $builder->where('l.listing_active', 1);
        /*$builder->limit($per_page,$offset);*/ 
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_id', 'DESC');
        $query=$builder->get();
        if($query->getNumRows()>0){
            if(!empty($list_id)){
             $result = $query->getRow();
            }else{
               $result = $query->getResult(); 
            }
            return $result;
        }
        return false;
    }



    public function my_events_count($user_id=NULL,$status=NULL)
    {
        $now = date('Y-m-d H:i:s');

        $builder = $this->db->table('booking as b');
        $builder->select('b.*, le.*, e.*, l.*, s.*, c.city_id, c.city_name, c.city_country_id, co.country_id, co.country_name, u.user_id, u.user_name, u.user_email, u.user_mobile, u.user_organization_name, u.user_city_id, u.user_country_id, u.user_address,p.payment_id, p.payment_user_id, p.payment_active');
        $builder->join('payment_details as p','b.book_id = p.payment_book_id','left');
        $builder->join('listings as l','l.listing_id = b.book_tournament_id');
        $builder->join('listing_events as le','le.listing_event_id = b.book_event_id');
        $builder->join('events as e','e.event_id = le.listing_event_title and e.event_active=1','inner');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
        $builder->where('b.book_user_id', $user_id);
        if($status == 'upcoming'){
            $builder->where('le.listing_event_start >', $now);
        }
        if($status == 'completed'){
            $builder->where('le.listing_event_end <', $now);
        }
        if($status == 'ongoing'){
            $builder->where('le.listing_event_start <', $now);
            $builder->where('le.listing_event_end >', $now);
        }
        //$builder->where('p.payment_active', 1);
        return $builder->countAllResults();
    }

    public function get_my_events($per_page=NULL, $offset=NULL, $user_id=NULL,$status=NULL)
    {
        $now = date('Y-m-d H:i:s');

        $builder = $this->db->table('booking as b');
        $builder->select('b.*, le.*, e.*, l.*, s.*, c.city_id, c.city_name, c.city_country_id, co.country_id, co.country_name, u.user_id, u.user_name, u.user_email, u.user_mobile, u.user_organization_name, u.user_city_id, u.user_country_id, u.user_address,p.payment_id, p.payment_user_id, p.payment_active');
        $builder->join('payment_details as p','b.book_id = p.payment_book_id','left');
        $builder->join('listings as l','l.listing_id = b.book_tournament_id');
        $builder->join('listing_events as le','le.listing_event_id = b.book_event_id');
        $builder->join('events as e','e.event_id = le.listing_event_title and e.event_active=1','inner');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
        $builder->where('b.book_user_id', $user_id);

        if($status == 'upcoming'){
            $builder->where('le.listing_event_start >', $now);
        }
        if($status == 'completed'){
            $builder->where('le.listing_event_end <', $now);
        }
        if($status == 'ongoing'){
            $builder->where('le.listing_event_start <', $now);
            $builder->where('le.listing_event_end >', $now);
        }

        //$builder->where('p.payment_active', 1);
        $builder->groupBy('b.book_id');
        $builder->orderBy('b.book_id', 'DESC');
        $builder->limit($per_page,$offset);
        $query=$builder->get();
        if($query->getNumRows()>0){
             $result = $query->getResult();
            return $result;
        }
        return false;
    }


    public function event_details($id)
    {
        $builder = $this->db->table('listing_events  as le');
        $builder->select('le.*,e.*,l.*,s.*,c.*,co.*,u.*');
        $builder->join('listings as l','l.listing_id = le.listing_event_listing_id and l.listing_active=1','inner');
        $builder->join('events as e','e.event_id = le.listing_event_title and e.event_active=1','inner');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
        $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
        $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');
        
        $builder->where('le.listing_event_id', $id);
        $builder->groupBy('le.listing_event_id');
        $query=$builder->get();
        if($query->getNumRows()>0){
             $result = $query->getRow();
            return $result;
        }
        return false;
    }


    public function count_organizer_payments($id=NULL,$tournaments=NULL,$events=NULL)
    {
        $builder = $this->db->table('payment_details  as p');
        $builder->select('p.*,l.*,u.*');
        $builder->join('listings as l','l.listing_id = p.payment_tournament_id','left');
        $builder->join('users as u','u.user_id = l.listing_user_id','inner');
        
        $builder->where('l.listing_user_id', $id);

        if(!empty($tournaments)){
            $builder->where('p.payment_tournament_id', $tournaments);
        }
        if(!empty($events)){
            $builder->where('p.payment_event_id', $events);
        }
        
        return $builder->countAllResults();
    }

    public function organizer_payments($per_page=NULL,$offset=NULL,$id=NULL,$tournaments=NULL,$events=NULL)
    {
        $builder = $this->db->table('payment_details  as p');
        $builder->select('p.*,l.*,u.*');
        $builder->join('listings as l','l.listing_id = p.payment_tournament_id','left');
        $builder->join('users as u','u.user_id = l.listing_user_id','inner');
        
        $builder->where('l.listing_user_id', $id);
        if(!empty($tournaments)){
            $builder->where('p.payment_tournament_id', $tournaments);
        }
        if(!empty($events)){
            $builder->where('p.payment_event_id', $events);
        }
        
        $builder->limit($per_page,$offset); 
        $builder->groupBy('p.payment_id');
        $builder->orderBy('p.payment_id','DESC');
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
             $result = $query->getResult();
            return $result;
        }
        return false;
    }



    public function count_payments($org_id=NULL)
    {
        $builder = $this->db->table('payment_details  as p');
        $builder->select('p.*,l.*,u.*');
        $builder->join('listings as l','l.listing_id = p.payment_tournament_id','left');
        $builder->join('users as u','u.user_id = l.listing_user_id','inner');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        
        return $builder->countAllResults();
    }

    public function get_payments($per_page=NULL,$offset=NULL,$org_id=NULL)
    {
        $builder = $this->db->table('payment_details  as p');
        $builder->select('p.*,l.*,u.*');
        $builder->join('listings as l','l.listing_id = p.payment_tournament_id','left');
        $builder->join('users as u','u.user_id = l.listing_user_id','inner');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        $builder->limit($per_page,$offset); 
        $builder->groupBy('p.payment_id');
        $builder->orderBy('p.payment_id','DESC');
        $query=$builder->get();
        if($query->getNumRows()>0){
             $result = $query->getResult();
            return $result;
        }
        return false;
    }


    public function count_schedules() {
        $today = date('Y-m-d');
    
        $builder = $this->db->table('matches as m');
        $builder->select('m.match_tournament, m.match_date, l.*, s.*, c.*, co.*, u.*')
                ->join('listings as l', 'l.listing_id = m.match_tournament', 'left')
                ->join('sports as s', 's.sports_id = l.listing_sports_id AND s.sports_active = 1', 'left')
                ->join('cities as c', 'c.city_id = l.listing_city_id AND c.city_active = 1', 'left')
                ->join('countries as co', 'co.country_id = l.listing_country_id AND co.country_active = 1', 'left')
                ->join('users as u', 'u.user_id = l.listing_user_id AND u.user_active = 1', 'left')
                ->where('l.listing_active', 1)
                ->groupBy(['m.match_tournament', 'm.match_date', 'l.listing_id']);
        return $builder->countAllResults();
    }

    public function get_schedules($per_page=NULL,$offset=NULL) {
        $today = date('Y-m-d');
    
        $builder = $this->db->table('matches as m');
        $builder->select('m.match_tournament, m.match_date, l.*, s.*, c.*, co.*, u.*')
                ->join('listings as l', 'l.listing_id = m.match_tournament', 'left')
                ->join('sports as s', 's.sports_id = l.listing_sports_id AND s.sports_active = 1', 'left')
                ->join('cities as c', 'c.city_id = l.listing_city_id AND c.city_active = 1', 'left')
                ->join('countries as co', 'co.country_id = l.listing_country_id AND co.country_active = 1', 'left')
                ->join('users as u', 'u.user_id = l.listing_user_id AND u.user_active = 1', 'left')
                ->where('l.listing_active', 1)
                ->groupBy(['m.match_tournament', 'm.match_date', 'l.listing_id'])
                ->orderBy("CASE
                                WHEN DATE(m.match_date) = '$today' THEN 1
                                WHEN DATE(m.match_date) > '$today' THEN 2
                                ELSE 3
                            END, match_date", 'ASC')
                ->limit($per_page, $offset);
                
        $matches = $builder->get()->getResult();

        return $matches ?: false;
    }

    public function count_scheduler_events($tourney_id) {
        $today = date('Y-m-d');
    
        $builder = $this->db->table('matches as m');
        $builder->select('m.match_tournament, m.match_date, le.*,e.*,s.*')
                ->join('listing_events as le', 'le.listing_event_id = m.match_event')
                ->join('listings as l', 'l.listing_id = m.match_tournament', 'left')
                ->join('events as e', 'e.event_id = le.listing_event_title', 'left')
                ->join('sports as s', 's.sports_id = l.listing_sports_id AND s.sports_active = 1', 'left')
                ->where('m.match_tournament', $tourney_id)
                ->groupBy(['m.match_tournament', 'm.match_date', 'le.listing_event_id']);
        return $builder->countAllResults();
    }

    public function get_scheduler_events($per_page=NULL,$offset=NULL,$tourney_id=NULL) {
        $today = date('Y-m-d');
    
        $builder = $this->db->table('matches as m');
        $builder->select('m.match_tournament, m.match_date, le.*,e.*,s.*')
                ->join('listing_events as le', 'le.listing_event_id = m.match_event')
                ->join('listings as l', 'l.listing_id = m.match_tournament', 'left')
                ->join('events as e', 'e.event_id = le.listing_event_title', 'left')
                ->join('sports as s', 's.sports_id = l.listing_sports_id AND s.sports_active = 1', 'left')
                ->where('m.match_tournament', $tourney_id)
                ->groupBy(['m.match_tournament', 'm.match_date', 'le.listing_event_id'])
                ->orderBy("CASE
                                WHEN DATE(m.match_date) = '$today' THEN 1
                                WHEN DATE(m.match_date) > '$today' THEN 2
                                ELSE 3
                            END, match_date", 'ASC')
                ->limit($per_page, $offset);
                
        $matches = $builder->get()->getResult();

        return $matches ?: false;
    }

    public function get_scheduler_groups($event_id) {
        $today = date('Y-m-d');
    
        $builder = $this->db->table('tbl_groups as g');
        $builder->select('g.*')
                ->where('g.grp_event', $event_id)
                ->orderBy("g.grp_name", 'ASC');
                
        $matches = $builder->get()->getResult();

        return $matches ?: false;
    }


    public function get_rounds_and_matches($event_id,$group_id) {
        $builder = $this->db->table('tbl_rounds');
        $builder->select('round_id, round_name, round_group, round_tournament, round_event, round_status');
        $builder->where('round_event',$event_id);
        $builder->where('round_group',$group_id);
        $rounds = $builder->get()->getResultArray();
        
        // Loop through each round to get the matches
        foreach ($rounds as &$round) {
            $builder2 = $this->db->table('matches as m');
            $builder2->select('m.*,t1.team_name as team1, t2.team_name as team2');
            $builder2->join('teams as t1','t1.team_id=m.match_team1_id','left');
            $builder2->join('teams as t2','t2.team_id=m.match_team2_id','left');
            
            $builder2->where('match_round_id', $round['round_id']);
            $round['matches'] = $builder2->get()->getResultArray();
        }
        
        return $rounds;
    }

}
