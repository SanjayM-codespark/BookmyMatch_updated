<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table = 'matches'; // Main table
    protected $primaryKey = 'match_id'; // Primary Key

    public function getTournamentReport($list_id)
    {
        $db = \Config\Database::connect();

        // Query to join tournaments, sports, teams, and matches
        $query = $db->table('matches')
            ->select('
                            listings.listing_id, 
                            listings.listing_title, 
                            sports.sports_name, 
                            t1.team_id as team1_id, 
                            t2.team_id as team2_id, 
                            t1.team_name as team1_name, 
                            t2.team_name as team2_name, 
                            tbl_rounds.round_name, 
                            COUNT(matches.match_id) as total_matches, 
                            match_winning_team_id as win_matches, 
                            matches.match_winning_desc as description,
                            matches.match_status as match_status
                            ')
            ->join('listings', 'listings.listing_id = matches.match_tournament', 'left')
            ->join('sports', 'listings.listing_sports_id = sports.sports_id', 'left')
            ->join('teams as t1', 't1.team_id = matches.match_team1_id', 'left')
            ->join('teams as t2', 't2.team_id = matches.match_team2_id', 'left')
            ->join('tbl_rounds', 'tbl_rounds.round_id = matches.match_round_id', 'left')
            ->where('matches.match_tournament', $list_id) // Filter by listing ID
            ->groupBy('matches.match_team1_id, matches.match_team2_id, tbl_rounds.round_name, matches.match_winning_desc,matches.match_status')
            ->get();
        $result = $query->getResult();

        return $query->getResult();
    }
}
