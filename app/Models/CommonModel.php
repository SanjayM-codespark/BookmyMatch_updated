<?php

namespace App\Models;

use CodeIgniter\Model;

class CommonModel extends Model
{
    public function insert_data($table, $data)
    {
        $builder = $this->db->table($table);
        if($builder->insert($data)){
            return $this->db->insertID();
        }
        return FALSE;
    }
     public function get_org_tournament($org_id=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*');
        
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }


    public function get_org_event($org_id=NULL,$tourney_id=NULL)
    {
        $builder = $this->db->table('listing_events as e');
        $builder->select('e.*,ev.event_name');
        $builder->join('listings as l','l.listing_id = e.listing_event_listing_id');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        if(!empty($tourney_id)){
            $builder->where('e.listing_event_listing_id', $tourney_id);
        }
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }
    
    public function batch_insert($table, $data) {
        $builder = $this->db->table($table);
        if($builder->insertBatch($data)){
            return TRUE;
        }
        return FALSE;
    }
     public function selected($table, $select, $condition = "", $rows = "", $order_by = "", $search= "", $result_array="") {
        $builder = $this->db->table('participants');
        $builder->select('*');
        if($condition){
            $builder->where($condition);
        }
        if($order_by){
            $builder->orderBy($order_by);
        }
        if (!empty($search)) {
        $builder->groupStart(); 
        $builder->like('participant_name', $search);
        $builder->orLike('participant_mobile', $search);
        $builder->groupEnd(); 
    }
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            if($rows){
                if(!empty($result_array)){
                    return $query->getResultArray();
                }else{
                    return $query->getResult();
                }
                
            }else{
                return $query->getRow();
            }
            
        }
        //echo $this->db->getLastQuery();die();
        return FALSE;
    }
public function update_data1($table, $data, $condition, $increment = false) {
    $builder = $this->db->table($table);
    $builder->where($condition);

    if ($increment) {
        foreach ($data as $key => $value) {
            $builder->set($key, "$key + $value", false); // raw increment
        }
    } else {
        $builder->set($data);
    }

    $builder->update();
    
    if ($this->db->affectedRows() > 0) {
        return TRUE;
    } 
    return FALSE;
}

    public function select($table, $select, $condition = "", $rows = "", $order_by = "", $search= "", $result_array="") {
        $builder = $this->db->table($table);
        $builder->select($select);
        if($condition){
            $builder->where($condition);
        }
        if($order_by){
            $builder->orderBy($order_by);
        }
        if(!empty($search)){
            $builder->like($search);
        }
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            if($rows){
                if(!empty($result_array)){
                    return $query->getResultArray();
                }else{
                    return $query->getResult();
                }
                
            }else{
                return $query->getRow();
            }
            
        }
        //echo $this->db->getLastQuery();die();
        return FALSE;
    }
     public function select1($table, $select, $condition = "") 
{
    $builder = $this->db->table($table);
    
    if ($condition) {
        $builder->where($condition);
    }

    return $builder->countAllResults(); 
}

    public function update_data($table, $data, $condition) {
        $builder = $this->db->table($table);
        $builder->where($condition);
        $builder->update($data);
        if ($this->db->affectedRows() > 0) {
            return TRUE;
        } 
        return FALSE;
    }
    public function change_data($table, $data)
{
    $builder = $this->db->table($table);
   $builder->where('match_id', $data['match_id'])
                ->where('player_id', $data['player_id'])
                ->where('set_number', $data['set_number']);    

    $result = $builder->update($data);

    return $result; 
}

    public function delete_data($table, $condition){
        $builder = $this->db->table($table);
        $builder->where($condition);
        if ($builder->delete()) {
            return TRUE;
        } else{
        return FALSE;
    } 
}
public function set_winner($match_id, $team_id, $player_id, $set)
{
    $builder = $this->db->table('scores_badminton')
        ->selectSum('points') // Use sum instead of count to get total points
        ->where('match_id', $match_id)
        ->where('team_id', $team_id) // Assuming the column is team_id, not team
        ->where('player_id', $player_id)
        ->where('set_number', $set)
        ->get(); // Run the query

    $result = $builder->getRow();

    return $result ? $result->points : 0; 
}


    //========================== Notifications ==============================
    public function notifications_count($search=NULL) {
        $columns = array('msg_title');
        $builder = $this->db->table("messages");
        $builder->select("*");
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
        return $builder->countAllResults();
    }
    
    public function get_notifications($limit=NULL,$offset=NULL,$search=NULL) {
        $columns = array('msg_title');
        $builder = $this->db->table("messages");
        $builder->select("*");
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
        $builder->orderBy('msg_id', 'DESC');
        $builder->limit($limit,$offset);
        $query = $builder->get();
        //echo $this->db->getLastQuery();die();
        if ($query->getNumRows() > 0) {
            return $query->getResult();
        }
        return false;
    }

    public function count_booking_history($user_id=NULL,$org_id=NULL)
    {
        $builder = $this->db->table('booking as b');
        $builder->select('b.*,l.*,e.*,u.*');
        $builder->join('listings as l','l.listing_id = b.book_tournament_id');
        $builder->join('listing_events as e','e.listing_event_id = b.book_event_id');
        $builder->join('users as u','u.user_id = b.book_user_id');
        if(!empty($user_id)){
            $builder->where('b.book_user_id', $user_id);
        }
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($search)){
            $builder->groupStart();
            $i=0;
            foreach ($columns as $column) {
                if($i==0){
                    $builder->like('LOWER(' .$column. ')', strtolower($search), 'both'); 
                }else{
                    $builder->orLike('LOWER(' .$column. ')', strtolower($search), 'both'); 
                }
                
                $i++;
            }
            $builder->groupEnd();
        }
        return $builder->countAllResults();
    }
    public function get_booking_history($per_page=NULL, $offset=NULL, $user_id=NULL,$org_id=NULL)
    {
        $builder = $this->db->table('booking as b');
        $builder->select('b.*,l.*,e.*,u.*');
        $builder->join('listings as l','l.listing_id = b.book_tournament_id');
        $builder->join('listing_events as e','e.listing_event_id = b.book_event_id');
        $builder->join('users as u','u.user_id = b.book_user_id');
        if(!empty($user_id)){
            $builder->where('b.book_user_id', $user_id);
        }
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($search)){
            $builder->groupStart();
            $i=0;
            foreach ($columns as $column) {
                if($i==0){
                    $builder->like('LOWER(' .$column. ')', strtolower($search), 'both'); 
                }else{
                    $builder->orLike('LOWER(' .$column. ')', strtolower($search), 'both'); 
                }
                
                $i++;
            }
            $builder->groupEnd();
        }
        $builder->limit($per_page,$offset); 
        $builder->groupBy('b.book_id');
        $builder->orderBy('b.book_id', 'DESC');
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }


    public function get_org_tournaments($org_id=NULL)
    {
        $builder = $this->db->table('listings as l');
        $builder->select('l.*');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }


    public function get_org_events($org_id=NULL,$tourney_id=NULL)
    {
        $builder = $this->db->table('listing_events as e');
        $builder->select('e.*,ev.event_name');
        $builder->join('listings as l','l.listing_id = e.listing_event_listing_id');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($tourney_id)){
            $builder->where('e.listing_event_listing_id', $tourney_id);
        }
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }

    public function get_org_group($org_id=NULL,$event_id=NULL)
    {
        $builder = $this->db->table('tbl_groups as g');
        $builder->select('g.*');
        $builder->join('listings as l','l.listing_id = g.grp_tournament');
        $builder->join('listing_events as e','e.listing_event_id = g.grp_event');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($event_id)){
            $builder->where('e.listing_event_id', $event_id);
        }
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }
    public function get_org_players($user_id)
    {
        $builder = $this->db->table('participants as g');
        $builder->select('g.*');
       
       
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }


    public function get_org_round($org_id=NULL,$event_id=NULL,$group_id=NULL)
    {
        $builder = $this->db->table('tbl_rounds as r');
        $builder->select('r.*');
        $builder->join('listings as l','l.listing_id = r.round_tournament');
        $builder->join('listing_events as e','e.listing_event_id = r.round_event');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($event_id)){
            $builder->where('r.round_event', $event_id);
        }
        if(!empty($group_id)){
            $builder->where('r.round_tournament', $group_id);
        }
        $query=$builder->get();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }

    public function get_org_evt_group($org_id=NULL,$event_id=NULL)
    {
        $builder = $this->db->table('tbl_groups as g');
        $builder->select('g.*');
        $builder->join('listings as l','l.listing_id = g.grp_tournament');
        $builder->join('listing_events as e','e.listing_event_id = g.grp_event');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($event_id)){
            $builder->where('g.grp_event', $event_id);
        }
        $query=$builder->get();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }


    public function count_group_list($org_id=NULL,$tournament=NULL,$event=NULL)
    {
        $builder = $this->db->table('tbl_groups as g');
        $builder->select('g.*,l.listing_title,ev.event_name');
        $builder->join('listings as l','l.listing_id = g.grp_tournament');
        $builder->join('listing_events as e','e.listing_event_id = g.grp_event');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($tournament)){
            $builder->where('l.listing_id', $tournament);
        }
        if(!empty($event)){
            $builder->where('e.listing_event_id', $event);
        }
        return $builder->countAllResults();
    }

    public function get_group_list($per_page=NULL, $offset=NULL, $org_id=NULL,$tournament=NULL,$event=NULL)
    {
        $builder = $this->db->table('tbl_groups as g');
        $builder->select('g.*,l.listing_title,ev.event_name');
        $builder->join('listings as l','l.listing_id = g.grp_tournament');
        $builder->join('listing_events as e','e.listing_event_id = g.grp_event');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($tournament)){
            $builder->where('l.listing_id', $tournament);
        }
        if(!empty($event)){
            $builder->where('e.listing_event_id', $event);
        }
        $builder->limit($per_page,$offset); 
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }


    public function count_round_list($org_id=NULL,$tblgroup=NULL,$tournament=NULL,$event=NULL)
    {
        $builder = $this->db->table('tbl_rounds as r');
        $builder->select('r.*,g.grp_name,l.listing_title,ev.event_name');
        $builder->join('tbl_groups as g','g.grp_id = r.round_group');
        $builder->join('listings as l','l.listing_id = r.round_tournament');
        $builder->join('listing_events as e','e.listing_event_id = r.round_event');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        if(!empty($tblgroup)){
            $builder->where('r.round_group', $tblgroup);
        }
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($tournament)){
            $builder->where('r.round_tournament', $tournament);
        }
        if(!empty($event)){
            $builder->where('r.round_event', $event);
        }
        return $builder->countAllResults();
    }

    public function get_round_list($per_page=NULL, $offset=NULL, $org_id=NULL,$tblgroup=NULL,$tournament=NULL,$event=NULL)
    {
        $builder = $this->db->table('tbl_rounds as r');
        $builder->select('r.*,g.grp_name,l.listing_title,ev.event_name');
        $builder->join('tbl_groups as g','g.grp_id = r.round_group');
        $builder->join('listings as l','l.listing_id = r.round_tournament');
        $builder->join('listing_events as e','e.listing_event_id = r.round_event');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        if(!empty($tblgroup)){
            $builder->where('r.round_group', $tblgroup);
        }
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($tournament)){
            $builder->where('r.round_tournament', $tournament);
        }
        if(!empty($event)){
            $builder->where('r.round_event', $event);
        }
        $builder->limit($per_page,$offset); 
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }


    public function count_team_list($org_id=NULL,$tblgroup=NULL,$tournament=NULL,$event=NULL)
    {
        $builder = $this->db->table('teams as t');
        $builder->select('t.*,g.grp_name,l.listing_title,ev.event_name');
        $builder->join('tbl_groups as g','g.grp_id = t.team_group');
        $builder->join('listings as l','l.listing_id = t.team_tournament');
        $builder->join('listing_events as e','e.listing_event_id = t.team_event');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        if(!empty($tblgroup)){
            $builder->where('t.team_group', $tblgroup);
        }
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($tournament)){
            $builder->where('t.team_tournament', $tournament);
        }
        if(!empty($event)){
            $builder->where('t.team_event', $event);
        }
        return $builder->countAllResults();
    }

    public function get_team_list($per_page=NULL, $offset=NULL, $org_id=NULL,$tblgroup=NULL,$tournament=NULL,$event=NULL)
    {
        $builder = $this->db->table('teams as t');
        $builder->select('t.*,g.grp_name,l.listing_title,ev.event_name');
        $builder->join('tbl_groups as g','g.grp_id = t.team_group or t.team_group = 0');
        $builder->join('listings as l','l.listing_id = t.team_tournament');
        $builder->join('listing_events as e','e.listing_event_id = t.team_event');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        if(!empty($tblgroup)){
            $builder->where('t.team_group', $tblgroup);
        }
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($tournament)){
            $builder->where('t.team_tournament', $tournament);
        }
        if(!empty($event)){
            $builder->where('t.team_event', $event);
        }
        $builder->groupBy('t.team_id');
                        $builder->orderBy('t.team_id','desc');
        $builder->limit($per_page,$offset); 
        $query=$builder->get();
        if($query->getNumRows()>0){
               $result = $query->getResult(); 
            return $result;
        }
        return false;
    }
  public function count_player_list($user_id)
{
      $builder = $this->db->table('listings');
    $builder->select('listings.listing_id, participants.*,teams.team_name');
    $builder->join('participants', 'participants.participant_list_id = listings.listing_id');
        $builder->join('teams', 'teams.team_id = participants.participant_team_id');
    $builder->where('listings.listing_user_id', $user_id);
    
    return $builder->countAllResults();
}

public function get_player_list($limit, $offset, $user_id)
{
    $builder = $this->db->table('listings');
    $builder->select('listings.listing_id, participants.*,teams.team_name');
    $builder->join('participants', 'participants.participant_list_id = listings.listing_id');
            $builder->join('teams', 'teams.team_id = participants.participant_team_id');

    $builder->where('listings.listing_user_id', $user_id);
    $builder->limit($limit, $offset);
    
    $query = $builder->get();

    if ($query->getNumRows() > 0) {
        return $query->getResult();
    }

    return false;
}

public function get_player_list1($id)
{
     $builder = $this->db->table('listings');
    $builder->select('listings.listing_id, participants.*,teams.team_name');
    $builder->join('participants', 'participants.participant_list_id = listings.listing_id');
            $builder->join('teams', 'teams.team_id = participants.participant_team_id');

    $builder->where('participants.participant_id', $id);


    $query = $builder->get();

    if ($query->getNumRows() > 0) {
        return $query->getResult();
    }

    return false;
}



    public function match_exist($condition=NULL, $team=NULL, $match_id=NULL) {
        $builder = $this->db->table('matches');
        $builder->select('*');
        if($condition){
            $builder->where($condition);
        }
        if(!empty($team)){
            $builder->groupStart();
            $builder->where('match_team1_id', $team);
            $builder->orWhere('match_team2_id', $team);
            $builder->groupEnd();
        }
        if(!empty($match_id)){
            $builder->where('match_id !=', $match_id);
        }
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return $query->getRow();
        }
        return FALSE;
    }


    public function count_match_list($org_id=NULL,$tblround=NULL,$tournament=NULL,$event=NULL)
    {
        $builder = $this->db->table('matches as m');
        $builder->select('m.*,r.round_name,l.listing_title,ev.event_name');
        $builder->join('tbl_rounds as r','r.round_id = m.match_round_id');
        $builder->join('listings as l','l.listing_id = m.match_tournament');
        $builder->join('listing_events as e','e.listing_event_id = m.match_event');
        $builder->join('events as ev','ev.event_id = e.listing_event_title','left');
        $builder->join('users as ump','ump.user_id = m.match_umpire','left');
        if(!empty($tblround)){
            $builder->where('t.match_round_id', $tblround);
        }
        if(!empty($org_id)){
            $builder->where('l.listing_user_id', $org_id);
        }
        if(!empty($tournament)){
            $builder->where('m.match_tournament', $tournament);
        }
        if(!empty($event)){
            $builder->where('m.match_event', $event);
        }
        return $builder->countAllResults();
    }

    public function get_match_list($per_page = NULL, $offset = NULL, $org_id = NULL, $tblround = NULL, $tournament = NULL, $event = NULL)
{
    $builder = $this->db->table('matches as m');

    $builder->select("
        m.*,
        r.round_name,
        l.listing_title,
        ev.event_name,
        CASE
            WHEN m.match_type = 'individual' THEN p1.participant_name
            ELSE t1.team_name
        END as team1,
        CASE
            WHEN m.match_type = 'individual' THEN p2.participant_name
            ELSE t2.team_name
        END as team2,
        ump.user_full_name as umpire_name,
        CASE
            WHEN m.match_type = 'individual' AND m.match_winning_individual_id > 0
                THEN wp.participant_name
            WHEN m.match_type != 'individual' AND m.match_winning_team_id > 0
                THEN wt.team_name
            ELSE NULL
        END as winner_name
    ");

    $builder->join('tbl_rounds as r', 'r.round_id = m.match_round_id');
    $builder->join('listings as l', 'l.listing_id = m.match_tournament');
    $builder->join('listing_events as e', 'e.listing_event_id = m.match_event');
    $builder->join('events as ev', 'ev.event_id = e.listing_event_title', 'left');

    $builder->join('teams as t1', 't1.team_id = m.match_team1_id', 'left');
    $builder->join('teams as t2', 't2.team_id = m.match_team2_id', 'left');
    $builder->join('teams as wt', 'wt.team_id = m.match_winning_team_id', 'left');

    $builder->join('participants as p1', 'p1.participant_id = m.match_individual1_id', 'left');
    $builder->join('participants as p2', 'p2.participant_id = m.match_individual2_id', 'left');
    $builder->join('participants as wp', 'wp.participant_id = m.match_winning_individual_id', 'left');

    $builder->join('users as ump', 'ump.user_id = m.match_umpire', 'left');

    if (!empty($tblround)) {
        $builder->where('m.match_round_id', $tblround); 
    }
    if (!empty($org_id)) {
        $builder->where('l.listing_user_id', $org_id);
    }
    if (!empty($tournament)) {
        $builder->where('m.match_tournament', $tournament);
    }
    if (!empty($event)) {
        $builder->where('m.match_event', $event);
    }

    $query = $builder->get();

    if ($query->getNumRows() > 0) {
        return $query->getResult();
    }
    return false;
}



    public function count_umpire_list($org_id=NULL)
    {
        $columns = ['u.name','u.mobile','u.user_city_id'];
        $builder = $this->db->table('users as u');
        $builder->select('u.*,c.*,co.*');
        $builder->join('cities as c','c.city_id = u.user_city_id','left');
        $builder->join('countries as co','co.country_id = u.user_country_id','left');
        if(!empty($org_id)){
            $builder->where('u.user_organizer_id', $org_id);
        }
        if(!empty($search)){
            $builder->groupStart();
            $i=0;
            foreach ($columns as $column) {
                if($i==0){
                    $builder->like('LOWER(' .$column. ')', strtolower($search), 'both'); 
                }else{
                    $builder->orLike('LOWER(' .$column. ')', strtolower($search), 'both'); 
                }
                
                $i++;
            }
            $builder->groupEnd();
        }
        return $builder->countAllResults();
    }
   public function get_umpire_list($per_page=NULL, $offset=NULL, $org_id=NULL,$search=NULL)
    {
        
        $columns = ['u.name','u.mobile','u.user_city_id'];
        $builder = $this->db->table('users as u');
        $builder->select('u.*,c.*,co.*');
        $builder->join('cities as c','c.city_id = u.user_city_id','left');
        $builder->join('countries as co','co.country_id = u.user_country_id','left');
       if($org_id == 2){
             $builder->where('u.user_role_id', 4);
        }else{
             $builder->where('u.user_organizer_id', $org_id);

        }
        if(!empty($search)){
             $builder->where('u.user_full_name', $search);
             $builder->orwhere('u.user_mobile', $search);
        }
        $builder->limit($per_page,$offset); 
        $builder->orderBy('u.user_id', 'ASC');
        $query=$builder->get();
        //echo $this->db->getLastQuery();die();
        if($query->getNumRows()>0){
               $results = $query->getResult(); 
               
            return $results;
        }
        return false;
    }
    
    public function get_umpire_list_data($per_page = NULL, $offset = NULL, $org_id = NULL, $search = NULL)
    {
        $builder = $this->db->table('users as u');
        $builder->select('u.*, c.*, co.*');
        $builder->join('cities as c', 'c.city_id = u.user_city_id', 'left');
        $builder->join('countries as co', 'co.country_id = u.user_country_id', 'left');
    
        $builder->where('u.user_role_id', 4);
    
        
        if (!empty($org_id)) {
            $builder->where('u.user_organizer_id', $org_id);
        }
    
        if (!empty($search)) {
            $builder->groupStart()
                     ->like('u.user_full_name', $search)
                     ->orLike('u.user_mobile', $search)
                     ->groupEnd();
        }
    
        $builder->limit($per_page, $offset);
        $builder->orderBy('u.user_id', 'ASC');
    
        $query = $builder->get();
    
        if ($query->getNumRows() > 0) {
            return $query->getResult();
        }
    
        return false;
    }
    
  public function get_update($table,$user_id){
        $builder = $this->db->table($table);
    $builder->set('participant_pay_status', 1);
        $builder->where('participant_id', $user_id);

    return $builder->update();
}
public function get_player($id){

    $builder = $this->db->table('participants as p');
    $builder->select('p.*, teams.team_name, pd.payment_active');
    $builder->join('teams', 'teams.team_id = p.participant_team_id', 'left');
    $builder->join('(SELECT * FROM payment_details GROUP BY payment_event_id) as pd', 'pd.payment_event_id = teams.team_event', 'left');
    $builder->where('participant_team_id', $id);
    $query = $builder->get();
    
    if($query->getNumRows()>0){
           $results = $query->getResult(); 
           
        return $results;
    }
    return false;
}
public function update_user($table, $id){

    $builder = $this->db->table($table);

    $builder->set('user_active', 0);
    $builder->set('user_role_id', 3);

    $builder->where('user_id', $id);

    if ($builder->update()) {
        return true; 
    }
 return false;
}
public function delete_user($id){

    $builder = $this->db->table('users');

    $builder->where('user_id', $id);

    if ($builder->delete()) {
        return true; 
    }
 return false;
}
public function get_tour_details($id){

    $builder = $this->db->table('listings');

    $builder->where('listing_id', $id);
    $query=$builder->get();

    if($query->getNumRows()>0){
        $results = $query->getResult(); 
        
     return $results;
 }
 return false;
}
public function get_event_details($id){

    $builder = $this->db->table('events');

    $builder->where('event_id', $id);
    $query=$builder->get();

    if($query->getNumRows()>0){
        $results = $query->getResult(); 
        
     return $results;
 }
 return false;
}
public function get_limit_data($table, $fields = '*', $condition = [], $limit = 10, $offset = 0)
{
    return $this->db->table($table)
                    ->select($fields)
                    ->where($condition)
                    ->limit($limit, $offset)
                    ->get()
                    ->getResult();
}

public function count_data($table, $condition = [])
{
    return $this->db->table($table)->where($condition)->countAllResults();
}
public function update_participant_status($participant_id, $status,$mode)
{
    $builder = $this->db->table('participants');
    $builder->set('participant_pay_status', $status);
    if ($status == 0) {
        $builder->set('mode', NULL);
    } elseif (!empty($mode)) {
        $builder->set('mode', $mode);
    }

    $builder->where('participant_id', $participant_id);
    return $builder->update();
}
public function update_post_status($post_id, $status)
{
    $builder = $this->db->table('posts');
    $builder->set('post_status', $status); 
    $builder->where('post_id', $post_id);
    return $builder->update();
}


}
