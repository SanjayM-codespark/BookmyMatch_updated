<?php

namespace App\Models;

use CodeIgniter\Model;

class TournamentModel extends Model
{
    public function count_all_listings($city=NULL, $country=NULL, $sports_id=NULL, $completed=NULL, $search=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*,le.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('listing_events as le','le.listing_event_listing_id = l.listing_id','left');
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
    // public function get_tournament_home($per_page=NULL, $offset=NULL, $city=NULL, $country=NULL, $sports_id=NULL, $completed=NULL, $search=NULL)
    // {
    //     $builder = $this->db->table('listings as l');
    //     $builder->select('l.*,s.*,c.*,co.*,u.*,le.*');
    //     $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
    //     $builder->join('listing_events as le','le.listing_event_listing_id = l.listing_id','left');
    //     $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
    //     $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
    //     $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','inner');
    //     if(!empty($city)){
    //         $builder->where('l.listing_city_id', $city);
    //     }
    //     if(!empty($country)){
    //         $builder->where('l.listing_country_id', $country);
    //     }
    //     if(!empty($sports_id)){
    //         $builder->where('l.listing_sports_id', $sports_id);
    //     }
    //     if(!empty($completed) && ($completed == 'completed')){
    //         $builder->where('l.listing_end_date <=', date('Y-m-d H:i:s'));
    //     }else{ 
    //         $builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
    //     }
    //     if(!empty($search)){
    //         $builder->like('l.listing_title', $search);
    //     }
    //     $builder->where('l.listing_active', 1);
    //     $builder->limit($per_page,$offset); 
    //     $builder->groupBy('l.listing_id');
    //     $builder->orderBy('l.listing_start_date', 'ASC');
    //     $query=$builder->get();
    //     // echo $this->db->getLastQuery();die();
    //     if($query->getNumRows()>0){
    //           $results = $query->getResult(); 
    //           foreach($results as $result){
    //             $builder = $this->db->table('listing_events  as le');
    //             $builder->select('e.event_name');
    //             $builder->join('events as e','e.event_id = le.listing_event_title','inner');
    //             $builder->where('le.listing_event_listing_id', $result->listing_id);
    //             $query=$builder->get();
    //             if($query->getNumRows()>0){
    //                  $result->events = $query->getResult();
    //             }else{
    //                 $result->events = [];
    //             }
    //           }
    //         return $results;
    //     }
    //     return false;
    // }
    
    
    public function get_tournament_home($per_page=NULL, $offset=NULL, $city=NULL, $country=NULL, $sports_id=NULL, $completed=NULL, $search=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*,s.*,c.*,co.*,u.*,le.*');
        $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
        $builder->join('listing_events as le','le.listing_event_listing_id = l.listing_id','left');
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
    
        // Search across title, address/location, organizer name, and city
        if(!empty($search)){
            $builder->groupStart();
                $builder->like('l.listing_title', $search);
                $builder->orLike('l.listing_address', $search);   // tournament address/venue field
                $builder->orLike('u.user_name', $search);         // organizer name field
                $builder->orLike('c.city_name', $search);         // location by city name too
            $builder->groupEnd();
        }
    
        $builder->where('l.listing_active', 1);
        $builder->limit($per_page,$offset);
        $builder->groupBy('l.listing_id');
        $builder->orderBy('l.listing_start_date', 'ASC');
        $query=$builder->get();
    
        if($query->getNumRows()>0){
            $results = $query->getResult();
            foreach($results as $result){
                $builder = $this->db->table('listing_events as le');
                $builder->select('e.event_name');
                $builder->join('events as e','e.event_id = le.listing_event_title','inner');
                $builder->where('le.listing_event_listing_id', $result->listing_id);
                $query=$builder->get();
                $result->events = $query->getNumRows()>0 ? $query->getResult() : [];
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
    public function get_tournament($user_id = null, $list_id = null, $sports_id = NULL, $status = NULL, $search = NULL, $per_page = NULL, $offset = NULL)
{
    $columns = ['l.listing_title','c.city_name','s.sports_name'];
    $builder = $this->db->table('listings as l');
    $builder->select('l.*,s.*,c.*,co.*,u.*');
    $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
    $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
    $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
    $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');

    if (!empty($user_id)) {
        $builder->where('l.listing_user_id', $user_id);
    }
    if (!empty($list_id)) {
        $builder->where('l.listing_id', $list_id);
    }
    if (!empty($sports_id)) {
        $builder->where('l.listing_sports_id', $sports_id);
    }
    if (!empty($status)) {
        if ($status == 'completed') {
            $builder->where('l.listing_end_date <=', date('Y-m-d H:i:s'));
        } else {
            $builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
        }
    }
    if (!empty($search)) {
        $i = 0;
        foreach ($columns as $column) {
            if ($i == 0) {
                $builder->like('LOWER(' . $column . ')', strtolower($search), 'both');
            } else {
                $builder->orLike('LOWER(' . $column . ')', strtolower($search), 'both');
            }
            $i++;
        }
    }

    $builder->where('l.listing_active', 1);
    $builder->groupBy('l.listing_id');
    $builder->orderBy('l.listing_id', 'DESC');

    if (!empty($per_page) && !is_null($offset)) {
        $builder->limit($per_page, $offset);
    }

    $query = $builder->get();
    if ($query->getNumRows() > 0) {
        return !empty($list_id) ? $query->getRow() : $query->getResult();
    }

    return false;
}
public function get_tournament_count($user_id = null, $sports_id = NULL, $status = NULL, $search = NULL)
{
    $columns = ['l.listing_title','c.city_name','s.sports_name'];
    $builder = $this->db->table('listings as l');
    $builder->select('l.listing_id');
    $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
    $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
    $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
    $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');

    if (!empty($user_id)) {
        $builder->where('l.listing_user_id', $user_id);
    }
    if (!empty($sports_id)) {
        $builder->where('l.listing_sports_id', $sports_id);
    }
    if (!empty($status)) {
        if ($status == 'completed') {
            $builder->where('l.listing_end_date <=', date('Y-m-d H:i:s'));
        } else {
            $builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
        }
    }
    if (!empty($search)) {
        $i = 0;
        foreach ($columns as $column) {
            if ($i == 0) {
                $builder->like('LOWER(' . $column . ')', strtolower($search), 'both');
            } else {
                $builder->orLike('LOWER(' . $column . ')', strtolower($search), 'both');
            }
            $i++;
        }
    }

    $builder->where('l.listing_active', 1);
    $builder->groupBy('l.listing_id');
    return $builder->countAllResults();
}

   public function get_events($tourney_id = null, $search = null)
    {
        $builder = $this->db->table('listing_events  as le');
        $builder->select('le.*,e.*,l.*,s.*,c.*,co.*,u.*');
        $builder->join('listings as l', 'l.listing_id = le.listing_event_listing_id and l.listing_active=1', 'inner');
        $builder->join('events as e', 'e.event_id = le.listing_event_title and e.event_active=1', 'inner');
        $builder->join('sports as s', 's.sports_id = l.listing_sports_id and s.sports_active=1', 'left');
        $builder->join('cities as c', 'c.city_id = l.listing_city_id and c.city_active=1', 'left');
        $builder->join('countries as co', 'co.country_id = l.listing_country_id and co.country_active=1', 'left');
        $builder->join('users as u', 'u.user_id = l.listing_user_id and u.user_active=1', 'left');
        if (!empty($tourney_id)) {
            $builder->where('le.listing_event_listing_id', $tourney_id);
        }
        if (!empty($search)) {
            $builder->like('e.event_name', $search);
        }

        $builder->where('le.listing_event_active', 1);
        /*$builder->limit($per_page,$offset);*/
        $builder->groupBy('le.listing_event_id');
        $builder->orderBy('le.listing_event_id', 'DESC');
        $query = $builder->get();
        //echo $this->db->getLastQuery();die();
        if ($query->getNumRows() > 0) {
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


    public function count_admin_tournament_list($status=NULL,$search = NULL)
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
       if ($search) {
    $builder->groupStart()
        ->like('l.listing_title', $search)
        ->orLike('l.listing_id', $search)
        ->groupEnd();
}

        return $builder->countAllResults();
        
    }


    public function get_admin_tournament_list($per_page=NULL, $offset=NULL, $status=NULL,$search=NULL)
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
        if ($search) {
    $builder->groupStart()
        ->like('l.listing_title', $search)
        ->orLike('l.listing_id', $search)
        ->groupEnd();
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
public function get_tournament1($user_id = null, $list_id = null, $sports_id = NULL, $status = NULL, $search = NULL, $per_page = NULL, $offset = NULL)
{
    $columns = ['l.listing_title','c.city_name','s.sports_name'];
    $builder = $this->db->table('listings as l');
    $builder->select('l.*,s.*,c.*,co.*,u.*');
    $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
    $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
    $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
    $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');

    
    if (!empty($list_id)) {
        $builder->where('l.listing_id', $list_id);
    }
    if (!empty($sports_id)) {
        $builder->where('l.listing_sports_id', $sports_id);
    }
    if (!empty($status)) {
        if ($status == 'completed') {
            $builder->where('l.listing_end_date <=', date('Y-m-d H:i:s'));
        } else {
            $builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
        }
    }
    if (!empty($search)) {
        $i = 0;
        foreach ($columns as $column) {
            if ($i == 0) {
                $builder->like('LOWER(' . $column . ')', strtolower($search), 'both');
            } else {
                $builder->orLike('LOWER(' . $column . ')', strtolower($search), 'both');
            }
            $i++;
        }
    }

    $builder->where('l.listing_active', 1);
    $builder->groupBy('l.listing_id');
    $builder->orderBy('l.listing_id', 'DESC');

    if (!empty($per_page) && !is_null($offset)) {
        $builder->limit($per_page, $offset);
    }

    $query = $builder->get();
    if ($query->getNumRows() > 0) {
        return !empty($list_id) ? $query->getRow() : $query->getResult();
    }

    return false;
}
public function get_tournament_count1($user_id = null, $sports_id = NULL, $status = NULL, $search = NULL)
{
    $columns = ['l.listing_title','c.city_name','s.sports_name'];
    $builder = $this->db->table('listings as l');
    $builder->select('l.listing_id');
    $builder->join('sports as s','s.sports_id = l.listing_sports_id and s.sports_active=1','left');
    $builder->join('cities as c','c.city_id = l.listing_city_id and c.city_active=1','left');
    $builder->join('countries as co','co.country_id = l.listing_country_id and co.country_active=1','left');
    $builder->join('users as u','u.user_id = l.listing_user_id and u.user_active=1','left');

 
    if (!empty($sports_id)) {
        $builder->where('l.listing_sports_id', $sports_id);
    }
    if (!empty($status)) {
        if ($status == 'completed') {
            $builder->where('l.listing_end_date <=', date('Y-m-d H:i:s'));
        } else {
            $builder->where('l.listing_end_date >=', date('Y-m-d H:i:s'));
        }
    }
    if (!empty($search)) {
        $i = 0;
        foreach ($columns as $column) {
            if ($i == 0) {
                $builder->like('LOWER(' . $column . ')', strtolower($search), 'both');
            } else {
                $builder->orLike('LOWER(' . $column . ')', strtolower($search), 'both');
            }
            $i++;
        }
    }

    $builder->where('l.listing_active', 1);
    $builder->groupBy('l.listing_id');
    return $builder->countAllResults();
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
        $today = date('Y-m-d H:i:s');
    
        $builder = $this->db->table('matches as m');
        $builder->select('m.match_tournament, m.match_date, l.*, s.*, c.*, co.*, u.*')
                ->join('listings as l', 'l.listing_id = m.match_tournament', 'left')
                ->join('sports as s', 's.sports_id = l.listing_sports_id AND s.sports_active = 1', 'left')
                ->join('cities as c', 'c.city_id = l.listing_city_id AND c.city_active = 1', 'left')
                ->join('countries as co', 'co.country_id = l.listing_country_id AND co.country_active = 1', 'left')
                ->join('users as u', 'u.user_id = l.listing_user_id AND u.user_active = 1', 'left')
                ->where('l.listing_active', 1)
                                ->where('m.match_status', 1)
              ->where('m.match_date >=', $today)
                ->groupBy(['m.match_tournament', 'm.match_date', 'l.listing_id']);
        return $builder->countAllResults();
    }

    public function get_schedules($per_page=NULL,$offset=NULL) {
        $today = date('Y-m-d H:i:s');
    
        $builder = $this->db->table('matches as m');
        $builder->select('m.match_tournament, m.match_date,m.match_id, l.*, s.*, c.*, co.*, u.*')
                ->join('listings as l', 'l.listing_id = m.match_tournament', 'left')
                ->join('sports as s', 's.sports_id = l.listing_sports_id AND s.sports_active = 1', 'left')
                ->join('cities as c', 'c.city_id = l.listing_city_id AND c.city_active = 1', 'left')
                ->join('countries as co', 'co.country_id = l.listing_country_id AND co.country_active = 1', 'left')
                ->join('users as u', 'u.user_id = l.listing_user_id AND u.user_active = 1', 'left')
                ->where('l.listing_active', 1)
                                        ->where('m.match_date >=', $today)
                                ->where('m.match_status', 1)
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
    
          $builder = $this->db->table('events as e');
    $builder->select('m.match_tournament, m.match_date, le.*, e.*, s.*')
            ->join('listing_events as le', 'le.listing_event_title = e.event_id', 'left')
            ->join('matches as m', 'm.match_event = le.listing_event_id', 'left')
            ->join('listings as l', 'l.listing_id = le.listing_event_listing_id', 'left')
            ->join('sports as s', 's.sports_id = l.listing_sports_id AND s.sports_active = 1', 'left')
            ->where('le.listing_event_listing_id', $tourney_id);
        return $builder->countAllResults();
    }

public function get_scheduler_events($per_page = NULL, $offset = NULL, $tourney_id = NULL) {
    $today = date('Y-m-d');

    $builder = $this->db->table('listing_events as le');
    $builder->select('m.match_tournament, m.match_date, le.*, e.*, s.*')
        ->join('events as e', 'e.event_id = le.listing_event_title', 'left')
        ->join('listings as l', 'l.listing_id = le.listing_event_listing_id', 'left')
        ->join('sports as s', 's.sports_id = l.listing_sports_id AND s.sports_active = 1', 'left')
         ->join('matches as m', 'm.match_event = le.listing_event_id', 'left')
        ->where('le.listing_event_listing_id', $tourney_id)
        ->groupBy('le.listing_event_id')
        ->orderBy("
                CASE
                    WHEN DATE(m.match_date) = '$today' THEN 1
                    WHEN DATE(m.match_date) > '$today' THEN 2
                    ELSE 3
                END, m.match_date", 'ASC')       
         ->limit($per_page, $offset);
    $matches = $builder->get()->getResult();

    return $matches ?: false;
}


    public function get_scheduler_groups($event_id) {
        $builder = $this->db->table('tbl_groups as g');
        $builder->select('g.*')
                ->where('g.grp_event', $event_id)
                ->orderBy("g.grp_name", 'ASC');

        $groups = $builder->get()->getResult();

        if (empty($groups)) {
            return false;
        }

        $event = $this->db->table('listing_events le')
            ->select('le.*')
            ->where('le.listing_event_id', $event_id)
            ->get()->getRow();

        $type = strtolower($event->listing_event_type ?? 'individual');
        $max_players = (int)($event->listing_event_max_players ?? 0);
        if ($max_players <= 0) $max_players = ($type === 'team') ? 11 : 1;

        $participants = $this->db->table('participants')
            ->where('participant_event_id', $event_id)
            ->countAllResults();

        $teams = 0;
        if ($type === 'team') {
            $teams = $this->db->table('teams')
                ->groupStart()
                ->where('team_listing_event_id', $event_id)
                ->orWhere('(team_event = ' . (int)$event_id . ' AND team_tournament = ' . (int)$event->listing_event_listing_id . ')')
                ->groupEnd()
                ->countAllResults();
        }

        $unit_count = ($type === 'team')
            ? $teams
            : (($type === 'double' || $type === 'doubles') ? (int)ceil($participants / 2) : $participants);

        // All competing units belong to the first-created group (grp_id ASC); the rest stay empty.
        $first_id = (int)min(array_map(fn($g) => (int)$g->grp_id, $groups));

        foreach ($groups as $group) {
            $group->has_players  = ((int)$group->grp_id === $first_id) && $unit_count > 0;
            $group->player_count = $group->has_players ? $unit_count : 0;
        }

        return $groups;
    }


    // public function get_rounds_and_matches($event_id,$group_id) {
    //     $builder = $this->db->table('tbl_rounds');
    //     $builder->select('round_id, round_name, round_group, round_tournament, round_event, round_status');
    //     $builder->where('round_event',$event_id);
    //     $builder->where('round_group',$group_id);
    //     $rounds = $builder->get()->getResultArray();
        
        
    //     foreach ($rounds as &$round) {
    //         $builder2 = $this->db->table('matches as m');
    //         $builder2->select('m.*,t1.team_name as team1, 
    //             t2.team_name as team2,
                
    //             SUM(sb1.points) as team1_points_badminton, 
    //             SUM(sb2.points) as team2_points_badminton,
    //             SUM(sc1.runs_scored) as team1_points_cricket, 
    //             SUM(sc2.runs_scored) as team2_points_cricket,'
    //         );        
    //         $builder2->join('teams as t1', 't1.team_id = m.match_team1_id', 'left');
    //         $builder2->join('teams as t2', 't2.team_id = m.match_team2_id', 'left');
        
    //         $builder2->join('scores_badminton as sb1', 'sb1.match_id = m.match_id AND sb1.team_id = m.match_individual1_id', 'left');
    //         $builder2->join('scores_badminton as sb2', 'sb2.match_id = m.match_id AND sb2.team_id = m.match_individual2_id', 'left');  
    //       $builder2->join('scores_cricket as sc1', 'sc1.match_id = m.match_id AND sc1.batting_team_id = m.match_team1_id', 'left');
    //         $builder2->join('scores_cricket as sc2', 'sc2.match_id = m.match_id AND sc2.batting_team_id = m.match_team2_id', 'left');
    //         $builder2->where('m.match_round_id', $round['round_id']);
    //         $builder2->groupBy('m.match_id, sc1.batting_team_id');
        
    //         $round['matches'] = $builder2->get()->getResultArray();
    //     }
        
        
        
    //     return $rounds;
    // }
    
    public function get_rounds_and_matches($event_id, $group_id)
    {
        $builder = $this->db->table('tbl_rounds');
        $builder->select('round_id, round_name, round_group, round_tournament, round_event, round_status');
        $builder->where('round_event', $event_id);
        $builder->where('round_group', $group_id);
        $rounds = $builder->get()->getResultArray();
    
        foreach ($rounds as &$round) {
            $builder2 = $this->db->table('matches as m');
            $builder2->select("m.*,
                    COALESCE(t1.team_name, p1.participant_name) as team1,
                    COALESCE(t2.team_name, p2.participant_name) as team2,
                    SUM(sb1.points) as team1_points_badminton,
                    SUM(sb2.points) as team2_points_badminton,
                    SUM(sc1.runs_scored) as team1_points_cricket,
                    SUM(sc2.runs_scored) as team2_points_cricket"
                );
    
            // Team-based matches
            $builder2->join('teams as t1', 't1.team_id = m.match_team1_id', 'left');
            $builder2->join('teams as t2', 't2.team_id = m.match_team2_id', 'left');
    
            // Individual matches — adjust table/column names to match your schema
            $builder2->join('participants as p1', 'p1.participant_id = m.match_individual1_id', 'left');
            $builder2->join('participants as p2', 'p2.participant_id = m.match_individual2_id', 'left');
    
            $builder2->join('scores_badminton as sb1', 'sb1.match_id = m.match_id AND sb1.team_id = m.match_individual1_id', 'left');
            $builder2->join('scores_badminton as sb2', 'sb2.match_id = m.match_id AND sb2.team_id = m.match_individual2_id', 'left');
            $builder2->join('scores_cricket as sc1', 'sc1.match_id = m.match_id AND sc1.batting_team_id = m.match_team1_id', 'left');
            $builder2->join('scores_cricket as sc2', 'sc2.match_id = m.match_id AND sc2.batting_team_id = m.match_team2_id', 'left');
    
            $builder2->where('m.match_round_id', $round['round_id']);
            $builder2->groupBy('m.match_id, sc1.batting_team_id');
    
            $matches = $builder2->get()->getResultArray();

            // Resolve winner_name and loser_name from existing team1/team2 and IDs
            foreach ($matches as &$match) {
                $winning_individual_id = (int)($match['match_winning_individual_id'] ?? 0);
                $winning_team_id       = (int)($match['match_winning_team_id'] ?? 0);
                $individual1_id        = (int)($match['match_individual1_id'] ?? 0);
                $individual2_id        = (int)($match['match_individual2_id'] ?? 0);
                $team1_id              = (int)($match['match_team1_id'] ?? 0);
                $team2_id              = (int)($match['match_team2_id'] ?? 0);
                $team1_name            = $match['team1'] ?? '';
                $team2_name            = $match['team2'] ?? '';

                if ($winning_individual_id > 0) {
                    if ($winning_individual_id === $individual1_id) {
                        $match['winner_name'] = $team1_name;
                        $match['loser_name']  = $team2_name;
                    } elseif ($winning_individual_id === $individual2_id) {
                        $match['winner_name'] = $team2_name;
                        $match['loser_name']  = $team1_name;
                    } else {
                        $match['winner_name'] = '';
                        $match['loser_name']  = '';
                    }
                } elseif ($winning_team_id > 0) {
                    if ($winning_team_id === $team1_id) {
                        $match['winner_name'] = $team1_name;
                        $match['loser_name']  = $team2_name;
                    } elseif ($winning_team_id === $team2_id) {
                        $match['winner_name'] = $team2_name;
                        $match['loser_name']  = $team1_name;
                    } else {
                        $match['winner_name'] = '';
                        $match['loser_name']  = '';
                    }
                } else {
                    $match['winner_name'] = '';
                    $match['loser_name']  = '';
                }

                $match['is_completed'] = ($winning_individual_id > 0 || $winning_team_id > 0);
            }
            unset($match);

            $round['matches'] = $matches;
        }
    
        return $rounds;
    }

}
