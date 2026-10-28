<?php

namespace App\API\r1;

use CodeIgniter\RESTful\ResourceController;
use App\Traits\ReqresTrait;
use App\Models\CommonModel;
use App\Models\UmpireModel;

class Umpire extends ResourceController
{
    use ReqresTrait;
    protected $common;
    protected $umpire;
    protected $db;
    public function __construct()
    {
        helper(['form', 'url', 'custom', 'number']);
        $this->common = new CommonModel();
        $this->umpire = new UmpireModel();
        $this->db = \Config\Database::connect();
    }

    public  function match_list()
    {
        $response = [];
        $rules = [
            'limit' => ['label' => 'Page Limit', 'rules' => 'required|numeric'],
            'page' => ['label' => 'page', 'rules' => 'required|numeric'],
            'user_id' => ['label' => 'user id', 'rules' => 'required|numeric']
        ];
        if (! $this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);

            $per_page = xss_clean($input['limit']);
            $page = xss_clean($input['page']);

            $user_id = !empty($input['user_id']) ? $input['user_id'] : NULL;

            $offset = ($page * $per_page) - $per_page;
            if ($page == 1) {
                $offset = 0;
            }
            $result = $this->umpire->get_match_list(1, $per_page, $offset, $user_id);

            if (!empty($result)) {

                $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }
        return $this->getResponse($response);
    }

    public  function match_list_completed()
    {
        $response = [];
        $rules = [
            'limit' => ['label' => 'Page Limit', 'rules' => 'required|numeric'],
            'page' => ['label' => 'page', 'rules' => 'required|numeric'],
            'user_id' => ['label' => 'user id', 'rules' => 'required|numeric']
        ];
        if (! $this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);

            $per_page = xss_clean($input['limit']);
            $page = xss_clean($input['page']);

            $user_id = !empty($input['user_id']) ? $input['user_id'] : NULL;

            $offset = ($page * $per_page) - $per_page;
            if ($page == 1) {
                $offset = 0;
            }
            $result = $this->umpire->get_match_list(0, $per_page, $offset, $user_id);

            if (!empty($result)) {

                $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }
        return $this->getResponse($response);
    }

    public  function umpire_notification()
    {
        $response = [];
        $rules = [
            'user_id' => ['label' => 'user_id', 'rules' => 'required|numeric']
        ];
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);

            $user_id = !empty($input['user_id']) ? $input['user_id'] : NULL;

            $query = $this->umpire->get_umpire_notification($user_id);

            if (!empty($query)) {
                $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $query->getResult(),
                    'count' => count($query->getResult()),
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }
        return $this->getResponse($response);
    }

    public  function match_details()
    {
        $response = [];
        $rules = [
            'user_id' => ['label' => 'user id', 'rules' => 'required|numeric'],
            'match_id' => ['label' => 'match id', 'rules' => 'required|numeric'],
        ];
        if (! $this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);

            $user_id = !empty($input['user_id']) ? $input['user_id'] : NULL;
            $match_id = !empty($input['match_id']) ? $input['match_id'] : NULL;


            $result = $this->umpire->get_match_details($match_id, $user_id);

            if (!empty($result)) {

                $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }
        return $this->getResponse($response);
    }

    public  function team_list()
    {
        $response = [];
        $rules = [
            'team1_id' => ['label' => 'team1_id', 'rules' => 'required|numeric'],
            'team2_id' => ['label' => 'team2_id', 'rules' => 'required|numeric'],
        ];
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);
            $team1_id = $input['team1_id'];
            $team2_id = $input['team2_id'];

            $result1 = $this->umpire->get_team_list($team1_id);
            $result2 = $this->umpire->get_team_list($team2_id);

            if (!empty($result1) && !empty($result2)) {
                $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => [
                        $result1[0]->team_id . '%%' . $result1[0]->team_name => $result1,
                        $result2[0]->team_id . '%%' . $result2[0]->team_name => $result2
                    ],
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No Team data found.'
                ];
            }
        }
        return $this->getResponse($response);
    }

    // -------------------------------------------------------------
    // Umpiring cricket related functions
    //-------------------------------------------------------------
    // --------------------------Cricket API Start-----------------------------
    public function openCricketMatch()
    {
        $rules = [
            'match_id'      => ['label' => 'Match ID', 'rules' => 'required|numeric'],
            'toss_win'      => ['label' => 'Toss Win', 'rules' => 'required'],
            'batting_team'  => ['label' => 'Batting Team', 'rules' => 'required|numeric'],
            'bowling_team'  => ['label' => 'Bowling Team', 'rules' => 'required|numeric|differs[batting_team]'],
            'overs'         => ['label' => 'Overs', 'rules' => 'required|numeric|greater_than[0]|less_than_equal_to[50]'],
        ];

        if (!$this->validate($rules)) {
            return $this->getResponse([
                'status'    => false,
                'message'   => 'Validation error',
                'errors'    => $this->validator->getErrors()
            ]);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        // Sanitize input
        $match_id       = $input['match_id'];
        $score_exists   = $this->db->table('scores_cricket')->where('match_id', $match_id)->get()->getResult();
            
        if (!$score_exists) {
            // Combine into one object
            $matchDetails = (object) [
                'toss_win'      => $input['toss_win'] . '%%' . $input['winner_select'],
                'batting_team'  => $input['batting_team'],
                'bowling_team'  => $input['bowling_team'],
                'overs'         => $input['overs'],
                'bowling_overs' => 1
            ];

            // Convert the object to a JSON string
            $matchDetailsJson = json_encode($matchDetails);

            // Fetch match details
            $this->common->update_data('matches', ['match_cricket' => $matchDetailsJson], ['match_id' => $match_id]);

            $match = $this->common->select('matches', '*', ['match_id' => $match_id]);
            $match->match_cricket = json_decode($match->match_cricket, true);

            $teams = [
                'batting_team' => 1, // Batting players
                'bowling_team' => 0  // Bowling players
            ];

            $insertedPlayers = [];

            foreach ($teams as $teamKey => $isBatting) {
                $query = $this->db->table('participants')
                    ->where('participant_team_id', $input[$teamKey])
                    ->where('participant_active', 1)
                    ->get();

                $participants = $query->getResult();

                foreach ($participants as $participant) {
                    $existing = $this->db->table('players')
                        ->where('player_participant_id', $participant->participant_id)
                        ->where('player_match_id', $match_id)
                        ->get()->getResult();

                    if (empty($existing)) {
                        $playerData = [
                            'player_participant_id' => $participant->participant_id,
                            'player_match_id'       => $match_id,
                            'team_id'               => $participant->participant_team_id,
                            'name'                  => $participant->participant_name,
                            'player_quality'        => '{"captain": "0", "batsman": "0", "bowler": "0", "allrownder": "0", "other":"0"}',
                            'batting'               => $isBatting,
                            'bowling'               => !$isBatting,
                            'batsman'               => 0,
                            'wicket'                => 0,
                        ];

                        $this->db->table('players')->insert($playerData);
                    }
                }

                $insertedPlayers[$teamKey] = $participants;
            }
            $match_existing = $this->db->query("SELECT * FROM players WHERE batsman = 1 AND player_match_id = $match_id")->getRow();
            if ($match_existing) {
                $data = $this->umpire->getCricketmMatchData($match_id);
            } else {
                $data = null;
            }
            return $this->getResponse([
                'status'    => true,
                'message'   => 'Toss completed successfully.',
                'data'      => $data,
            ]);
        } else {
            return $this->getResponse([
                'status'    => true,
                'message'   => 'Match Already Started Data retrived successfully.',
                'data'      => $this->umpire->getCricketmMatchData($match_id),
            ]);
        }
    }

    public function getBatsmenByTeam()
    {
        $rules = [
            'match_id'  => ['label' => 'Match ID', 'rules' => 'required|numeric'],
            'play_mode' => ['label' => 'play_mode', 'rules' => 'required'],
        ];

        if (!$this->validate($rules)) {
            return $this->getResponse([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $input  = $this->request->getPost();

        // Sanitize input
        $match_id   = $input['match_id'];
        $play_mode  = $input['play_mode'];

        // Fetch the current batsmen for the given match
        if ($play_mode != "all") {
            $query = $this->db->table('players as p')
                ->select('t.team_name, p.*')
                ->join('teams t', 't.team_id = p.team_id', 'left')
                ->where('p.player_match_id', $match_id)
                ->where('p.batting', $play_mode == "batting" ? 1 : 0) // Only select players currently batting or bowling
                ->get();
            $batsmen = $query->getResult();
        } else {
            $teams = [
                'batting_team' => 1, // Batting players
                'bowling_team' => 0  // Bowling players
            ];

            $insertedPlayers = [];
            foreach ($teams as $teamKey => $isBatting) {
                $query = $this->db->table('players as p')
                    ->select('t.team_name, p.*')
                    ->join('teams t', 't.team_id = p.team_id', 'left')
                    ->where('p.player_match_id', $match_id)
                    ->where('p.batting', $isBatting) // Only select players currently batting or bowling
                    ->get();

                $participants = $query->getResult();

                $insertedPlayers[$teamKey] = $participants;
            }

            if (empty($insertedPlayers)) {
                return $this->getResponse([
                    'status'  => false,
                    'message' => "No players found for this match.",
                    'data'    => []
                ]);
            }

            return $this->getResponse([
                'status'  => true,
                'message' => "Players data retrieved successfully.",
                'data'    => $insertedPlayers
            ]);
        }

        $retrive_text = $play_mode == "batting" ? "Batting team list" : "Bowling Team List";
        $selection_text = $play_mode == "batting" ? "Please select a batsman." : "Please select a bowler.";
        if (empty($batsmen)) {
            return $this->getResponse([
                'status'  => false,
                'message' => "No " . $retrive_text . " found for this match.",
                'data'    => []
            ]);
        }

        return $this->getResponse([
            'status'  => true,
            'message' => $selection_text,
            'data'    => $batsmen
        ]);
    }

    public function selectBatsmanOrBowler()
    {
        $input  = $this->request->getPost();

        $rules = [
            'match_id'  => ['label' => 'Match ID', 'rules' => 'required|numeric'],
            'select_for' => ['label' => 'select_for', 'rules' => 'required'],
        ];

        $match_id  = $input['match_id'];
        $select_for = $input['select_for'];

        if ($select_for === "batting") {
            $query = $this->db->table('players')
                ->select('COUNT(CASE WHEN batsman = 1 THEN 0 END) AS batsman_count')
                ->where('player_match_id', $match_id)
                ->where('batting', 1)
                ->get();

            $batsmen = $query->getRow();

            // If no batsmen exist, apply validation for array items separately
            if ($batsmen->batsman_count == 0) {
                $rules = [
                    'selected_players' => [
                        'label' => 'Selected Players',
                        'rules' => 'required|is_array|min_array_length[2]',
                        'errors' => [
                            'required' => 'The {field} field is required.',
                            'is_array' => 'The {field} must be an array.',
                            'min_array_length' => 'The {field} must have 2 batsman.',
                        ],
                    ],
                ];
            }
        } else {
            // Ensure 'selected_players' is required in all cases
            $rules = [
                'selected_players' => [
                    'label' => 'Selected Players',
                    'rules' => 'required|is_array',
                    'errors' => [
                        'required' => 'The {field} field is required.',
                        'is_array' => 'The {field} must be an array.',
                    ],
                ],
            ];
        }

        if (!$this->validate($rules)) {
            return $this->getResponse([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $this->validator->getErrors()
            ]);
        }
        $selected_players = $input['selected_players'];
        if ($select_for === "batting") {
            foreach ($selected_players as $key => $batsman) {
                $is_batting = $key == 0 ? 1 : 0;
                $this->common->update_data('players', ["batsman" => 1, "is_batting" => $is_batting], ['player_match_id' => $match_id, 'player_participant_id' => $batsman]);
            }
        } else {
            $this->common->update_data('players', ['bowler' => 0], ["player_match_id" => $match_id]);
            $this->common->update_data('players', ['bowler' => 1], ['player_match_id' => $match_id, 'player_participant_id' => $selected_players[0]]);
        }
        $retrive_text = $select_for == "batting" ? "Batsman selected successfully." : "Bowler selected successfully.";

        $data = $this->umpire->getCricketmMatchData($match_id);

        return $this->getResponse([
            'status'  => true,
            'message' => $retrive_text,
            'data'    => $data,
        ]);
    }

    public function updateCricketScore()
    {
        $request        = $this->request->getPost();
        $match_id       = $request['match_id'];
        $batsman_one    = isset($request['batsman_one']) ? $request['batsman_one'] : 0;
        $batsman_two    = isset($request['batsman_two']) ? $request['batsman_two'] : 0;
        $runout_batsman = isset($request['runout_batsman']) ? $request['runout_batsman'] : 0;
        $bowler         = isset($request['bowler']) ? $request['bowler'] : 0;
        $runs_scored    = isset($request['runs_scored']) ? $request['runs_scored'] : 0;
        $extra_type     = isset($request['extra_type']) ? $request['extra_type'] : 0;
        $wicket_type    = isset($request['wicket_type']) ? $request['wicket_type'] : 0;
        $input_type     = $request['input_type'];
        $match_data     = $this->common->select('matches', '*', ["match_id" => $match_id]);
        $match_cricket  = json_decode($match_data->match_cricket);

        $team_wickets = $this->umpire->teamWickets($match_id, $match_cricket->batting_team);
        if ($team_wickets->total_wickets == $team_wickets->total_players - 1) {
            return $this->respond([
                "status"  => true,
                "message" => "All Players are Out. Finish or Change side.",
                "data"    => $this->umpire->getCricketmMatchData($match_id),
            ]);
        }
        if ($input_type == "undo") {
            $delete = $this->db->query("DELETE FROM scores_cricket
                WHERE match_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1
            ", [$match_id]);

            $previous_score_data = $this->umpire->getCurrentOverData($match_id);

            if (!empty($previous_score_data)) {
                $match_cricket->bowling_overs = $previous_score_data->overs;
                $undo_over = $this->common->update_data('matches', ["match_cricket" => json_encode($match_cricket)], ["match_id" => $match_id]);

                $revert_batsman_one = $this->common->select('players', '*', ["player_match_id" => $match_id, "player_participant_id" => $previous_score_data->batsman_one]);
                $revert_batsman_two = $this->common->select('players', '*', ["player_match_id" => $match_id, "player_participant_id" => $previous_score_data->batsman_two]);
                $revert_bowler = $this->common->select('players', '*', ["player_match_id" => $match_id, "player_participant_id" => $previous_score_data->bowler]);
                $this->common->update_data('players', ['batsman' => 0, 'is_batting' => 0, 'bowler' => 0], ["player_match_id" => $match_id]);

                // Update batsman one
                $undo_batsman_one = $this->common->update_data('players', [
                    'batsman' => ($previous_score_data->batsman_one == $revert_batsman_one->player_participant_id && $previous_score_data->wickets != 1) ? 1 : 0,
                    'is_batting' => $previous_score_data->wickets != 1 ? 1 : 0,
                    'wicket' => $previous_score_data->wickets
                ], ["player_match_id" => $match_id, "player_participant_id" => $previous_score_data->batsman_one]);

                // Update batsman two (fixing wrong player reference)
                $undo_batsman_two = $this->common->update_data('players', [
                    'batsman' => ($previous_score_data->batsman_two == $revert_batsman_two->player_participant_id) ? 1 : 0,
                    'is_batting' => 0
                ], ["player_match_id" => $match_id, "player_participant_id" => $previous_score_data->batsman_two]);

                // Update bowler
                $undo_bowler = $this->common->update_data('players', [
                    'bowler' => ($previous_score_data->bowler == $revert_bowler->player_participant_id) ? 1 : 0
                ], ["player_match_id" => $match_id, "player_participant_id" => $previous_score_data->bowler]);
            }

            return $this->respond([
                "status"    => true,
                "message"   => "Last entry undone successfully!",
                "data"      => $this->umpire->getCricketmMatchData($match_id)
            ]);
        } else {
            $this->common->update_data('players', ['is_batting' => 0], ["player_match_id" => $match_id]);
            $this->common->update_data('players', ['is_batting' => 1], ["player_match_id" => $match_id, "player_participant_id" => $batsman_one]);
            $batsman = [
                'match_id'              => $match_id,
                'batting_team_id'       => $match_cricket->batting_team,
                'bowling_team_id'       => $match_cricket->bowling_team,
                'batsman_one'           => $batsman_one,
                'batsman_two'           => $batsman_two,
                'bowler'                => $bowler,
                'runs_scored'           => 0,
                'balls_faced'           => 0,
                'fours'                 => 0,
                'sixes'                 => 0,
                'wides'                 => 0,
                'no_balls'              => 0,
                'byes'                  => 0,
                'leg_byes'              => 0,
                'penalty_runs'          => 0,
                'total_extras'          => 0,
                'wickets'               => 0,
                'hit_wickets'           => 0,
                'catches'               => 0,
                'stumpings'             => 0,
                'lbws'                  => 0,
                'runouts'               => 0,
                'non_striker_runouts'   => 0
            ];

            // Fetch match details
            $match = $this->common->select('matches', '*', ['match_id' => $match_id]);
            $batsman['overs'] = json_decode($match->match_cricket)->bowling_overs ?? 0;

            // Count boundaries (only if not an extra)
            if (in_array($extra_type, [0, 2, 3, 4])) {
                if ($runs_scored == 4) $batsman['fours'] = 1;
                if ($runs_scored == 6) $batsman['sixes'] = 1;
            }

            // Runs & Balls Count
            $batsman['runs_scored'] = $runs_scored;

            if (in_array($extra_type, [0, 3, 4, 5])) { // Only count balls for valid deliveries
                $batsman['balls_faced'] = 1;
            }

            // Handle Wickets
            if ($extra_type != 0) {
                switch ($extra_type) {
                    case 1:
                        $batsman['wides'] = 1;
                        $batsman['total_extras'] = 1;
                        $batsman['runs_scored'] = 0;
                        break;
                    case 2:
                        $batsman['no_balls'] = 1;
                        $batsman['total_extras'] = 1;
                        break;
                    case 3:
                        $batsman['byes'] = 1;
                        break;
                    case 4:
                        $batsman['leg_byes'] = 1;
                        break;
                    case 5:
                        $batsman['penalty_runs'] = 1;
                        $batsman['total_extras'] = 5;
                        break;
                }
            }

            // Handle Wickets
            if ($wicket_type != 0) {
                $batsman['wickets'] = 1;
                switch ($wicket_type) {
                    case 1:
                        $update_player_wicket = $this->common->update_data('players', ['wicket' => 1, 'batsman' => 0, 'is_batting' => 0], ["player_match_id" => $match_id, "player_participant_id" => $batsman_one]);
                        $batsman['hit_wickets'] = 1;
                        $batsman['runs_scored'] = 0;
                        break;
                    case 2:
                        $update_player_wicket = $this->common->update_data('players', ['wicket' => 1, 'batsman' => 0, 'is_batting' => 0], ["player_match_id" => $match_id, "player_participant_id" => $batsman_one]);
                        $batsman['catches'] = 1;
                        break;
                    case 3:
                        $update_player_wicket = $this->common->update_data('players', ['wicket' => 1, 'batsman' => 0, 'is_batting' => 0], ["player_match_id" => $match_id, "player_participant_id" => $batsman_one]);
                        $batsman['stumpings'] = 1;
                        $batsman['runs_scored'] = 0;
                        break;
                    case 4:
                        $update_player_wicket = $this->common->update_data('players', ['wicket' => 1, 'batsman' => 0, 'is_batting' => 0], ["player_match_id" => $match_id, "player_participant_id" => $batsman_one]);
                        $batsman['lbws'] = 1;
                        $batsman['runs_scored'] = 0;
                        break;
                    case 5:
                        if($runout_batsman == 0) {
                            return $this->respond([
                                "status"  => false,
                                "message" => "Please select player for runout.",
                            ]);
                        }
                        $update_player_wicket = $this->common->update_data('players', ['wicket' => 1, 'batsman' => 0, 'is_batting' => 0], ["player_match_id" => $match_id, "player_participant_id" => $runout_batsman]);
                        $batsman['runouts'] = 1;
                        $batsman['runs_scored'] = $runs_scored;
                        break;
                    case 6:
                        if($runout_batsman == 0) {
                            return $this->respond([
                                "status"  => false,
                                "message" => "Please select player for runout.",
                            ]);
                        }
                        $update_player_wicket = $this->common->update_data('players', ['wicket' => 1, 'batsman' => 0, 'is_batting' => 0], ["player_match_id" => $match_id, "player_participant_id" => $runout_batsman]);
                        $batsman['non_striker_runouts'] = 1;
                        $batsman['runs_scored'] = $runs_scored;
                        break;
                }
            }

            // get current over
            $over_data = $this->umpire->getCurrentOverData($match_id);
            $over_bowled_balls = $over_data->balls ?? 0;
            if ($over_bowled_balls == 5) {
                $batsman['overs_bowled'] = in_array($extra_type, [0, 3, 4, 5]) ? 6 : $over_bowled_balls;
                $maiden_check = $this->umpire->checkThisMaiden($match_id, $match_cricket->bowling_overs);
                $batsman['maidens'] = in_array($extra_type, [0, 3, 4, 5]) && $maiden_check ? 1 : 0;
            } elseif ($over_bowled_balls == 6) {
                $batsman['overs_bowled'] = in_array($extra_type, [0, 3, 4, 5]) ? 1 : $over_bowled_balls;
            } else {
                $batsman['overs_bowled'] = in_array($extra_type, [0, 3, 4, 5]) ? $over_bowled_balls + 1 : $over_bowled_balls;
            }

            $insert_score = $this->common->insert_data('scores_cricket', $batsman);

            $updated_over_data = $this->umpire->getCurrentOverData($match_id);
            $team_wickets_add = $this->umpire->teamWickets($match_id, $match_cricket->batting_team);
            if ($team_wickets_add->total_wickets == $team_wickets_add->total_players - 1) {
                return $this->respond([
                    "status"  => true,
                    "message" => "All Players are Out. Finish or Change side.",
                    "data"    => $this->umpire->getCricketmMatchData($match_id),
                ]);
            } elseif ($wicket_type != 0) {
                return $this->respond([
                    "status"  => true,
                    "message" => "Please select the next batsman.",
                ]);
            } elseif ($updated_over_data->overs_bowled == 6) {
                return $this->respond([
                    "status"  => true,
                    "message" => "Please select the next bowler.",
                ]);
            } else {
                return $this->respond([
                    "status"  => true,
                    "message" => "Score updated successfully!",
                    "data"    => $this->umpire->getCricketmMatchData($match_id),
                ]);
            }
        }
    }

    public function overChange()
    {
        $request = $this->request->getPost();
        $match_id = $request['match_id'];

        // Fetch match data
        $match_data = $this->common->select('matches', '*', ["match_id" => $match_id]);
        $match_cricket = json_decode($match_data->match_cricket);

        // Get updated over data
        $updated_over_data = $this->umpire->getCurrentOverData($match_id);

        // Check if overs are finished
        if ($updated_over_data->balls == 6 && $match_cricket->overs == $match_cricket->bowling_overs) {
            return $this->respond([
                "status"  => false,
                "message" => "Overs Finished.",
                "data"    => $this->umpire->getCricketmMatchData($match_id),
            ]);
        }

        // Check if over should be incremented
        if ($updated_over_data->balls == 6 && $match_cricket->overs != $match_cricket->bowling_overs) {
            $match_cricket->bowling_overs = (int) $match_cricket->bowling_overs + 1;
            $update_over = $this->common->update_data('matches', ["match_cricket" => json_encode($match_cricket)], ["match_id" => $match_id]);

            if ($update_over) {
                return $this->respond([
                    "status"  => true,
                    "message" => "Over changed successfully!",
                    "data"    => $this->umpire->getCricketmMatchData($match_id),
                ]);
            }
        }

        // If overs are not yet completed
        return $this->respond([
            "status"  => false,
            "message" => "Over not completed.",
            "data"    => $this->umpire->getCricketmMatchData($match_id),
        ]);
    }

    public function cricketOverallScore()
    {
        $request = $this->request->getPost();
        $match_id = $request['match_id'];

        $teamQuery = $this->db->query(
            "SELECT 
                t.team_id,
                t.team_name,
                m.match_cricket,
                COALESCE(batting.runs, 0) AS runs,
                COALESCE(batting.wickets, 0) AS wickets,
                COALESCE(batting.balls_faced, 0) AS balls_faced
            FROM teams t
            LEFT JOIN matches m 
                ON t.team_id = m.match_team1_id 
                OR t.team_id = m.match_team2_id
            LEFT JOIN (
                -- Batting stats
                SELECT 
                    batting_team_id, 
                    SUM(runs_scored) + SUM(total_extras) AS runs, 
                    SUM(wickets) AS wickets, 
                    SUM(balls_faced) AS balls_faced 
                FROM scores_cricket 
                WHERE match_id = ? 
                GROUP BY batting_team_id
            ) batting ON t.team_id = batting.batting_team_id
            LEFT JOIN (
                -- Bowling stats (to get total overs bowled)
                SELECT 
                    bowling_team_id, 
                    MAX(overs_bowled) AS total_overs 
                FROM scores_cricket 
                WHERE match_id = ? 
                GROUP BY bowling_team_id
            ) bowling ON t.team_id = bowling.bowling_team_id
            WHERE m.match_id = ?",
            [$match_id, $match_id, $match_id]
        );

        $teams      = $teamQuery->getResultArray();
        $teamData   = [];
        foreach ($teams as $team) {
            $score_data     = $this->umpire->getOverallScoreData($match_id, $team['team_id']);
            $bowling_data   = $this->umpire->getOverallBowlingData($match_id, $team['team_id']);
            $extra_data     = $this->umpire->getOverallExtraData($match_id, $team['team_id']);
            $fall_of_wicket = $this->umpire->getOverallFallOfWicket($match_id, $team['team_id']);

            $current_over = $this->umpire->formatOvers($team['balls_faced']);
            $current_over_arr[] = $current_over;
            $teamData[] = [
                "team_id"           => $team['team_id'],
                "team_name"         => $team['team_name'],
                "runs"              => $team['runs'],
                "wickets"           => $team['wickets'],
                "overs"             => $current_over,
                "score_data"        => $score_data,
                "bowling_data"      => $bowling_data,
                "extra_data"        => $extra_data,
                "fall_of_wicket"    => $fall_of_wicket,
            ];
        }
        return $this->respond([
            "status"  => true,
            "message" => "Overall Scores Retrived successfully.",
            "data"    => $teamData,
        ]);
    }

    public function cricketSideChange()
    {
        $request        = $this->request->getPost();
        $match_id       = $request['match_id'];
        $batting_team_id = $request['batting_team_id'];
        $match_data     = $this->common->select('matches', 'match_cricket', ["match_id" => $match_id]);
        $current_over   = $this->umpire->getCurrentOverData($match_id);
        $match_data     = json_decode($match_data->match_cricket);

        if (($current_over->overs + 1) == $match_data->bowling_overs && $current_over->overs_bowled == 6) {
            $innings_complete = $this->umpire->inningsComplete($match_id);
            if ($innings_complete) {
                return $this->respond([
                    "status"  => false,
                    "message" => "Innings completed.",
                ]);
            } else {
                $update_batting = $this->common->update_data('players', ['batting' => 0, 'is_batting' => 0, "bowling" => 1], ["player_match_id" => $match_id]);
                $update_bowling = $this->common->update_data('players', ['batting' => 1, 'is_batting' => 0, "bowling" => 0], ["player_match_id" => $match_id, "team_id" => $batting_team_id]);
                $batting_team = $match_data->batting_team;
                $bowling_team = $match_data->bowling_team;
                $match_data->batting_team = $bowling_team;
                $match_data->bowling_team = $batting_team;
                $match_data_change = $this->common->update_data('matches', ["match_cricket" => json_encode($match_data)], ["match_id" => $match_id]);

                if ($update_batting && $update_bowling && $match_data_change) {
                    return $this->respond([
                        "status"  => true,
                        "message" => "Side Changed successfully.",
                        "data"    => $this->umpire->getCricketmMatchData($match_id),
                    ]);
                }
            }
        } else {
            return $this->respond([
                "status"  => false,
                "message" => "Batting overs not completed.",
                "data"    => $this->umpire->getCricketmMatchData($match_id),
            ]);
        }
    }

    public function completeCricketmMatch()
    {
        $request        = $this->request->getPost();
        $match_id       = $request['match_id'];
        $match_data     = $this->common->select('matches', 'match_cricket', ["match_id" => $match_id]);
        $current_over   = $this->umpire->getCurrentOverData($match_id);
        $match_data     = json_decode($match_data->match_cricket);
        if (($current_over->overs + 1) == $match_data->bowling_overs && $current_over->balls == 6) {
            $match_completed = $this->common->update_data('matches', ['match_status' => 0], ['match_id' => $match_id]);
            if ($match_completed) {
                return $this->respond([
                    "status"  => true,
                    "message" => "Cricket Match Completed.",
                ]);
            } else {
                return $this->respond([
                    "status"  => false,
                    "message" => "Failed to complete the match.",
                ]);
            }
        } else {
            return $this->respond([
                "status"  => false,
                "message" => "Innings not completed.",
            ]);
        }
    }
    // --------------------------Cricket API End-----------------------------

    // --------------------------Post API Start-----------------------------
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
            $poststatus     = 1;
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
                'post_images_json'  => json_encode($images),
                'post_videos_json'  => json_encode($videos),
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

    // --------------------------Badmonton API Start-----------------------------
    public function startUmpiring()
    {
        $response = [];
        $rules = [
            'user_id' => ['label' => 'user id', 'rules' => 'required|numeric'],
            'match_id' => ['label' => 'match id', 'rules' => 'required|numeric'],
            'selected_type' => ['label' => 'selected_type', 'rules' => 'required|numeric'],
        ];
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);
            $user_id = xss_clean($input['user_id']);
            $match_id = xss_clean($input['match_id']);
            $selected_type = xss_clean($input['selected_type']);
            $val = $this->common->select('matches', 'match_total_sets, match_set_complete', array('match_id' => $match_id), false);
            $set_number = $val->match_set_complete ? $val->match_set_complete + 1 : 1;
            $set_number = $set_number == 4 ? 3 : $set_number;
            if ($selected_type) {
                $scoreboard = $this->umpire->get_badminton_score($match_id, 3, $val->match_total_sets); // stating selected points

                // Check the length of the scoreboard
                if (is_null($scoreboard) || count($scoreboard) < 2) {
                    $message = count($scoreboard) == 1 ? 'One player is missing.' : 'No players found.';
                    $response = [
                        'status' => false,
                        'message' => $message,
                    ];
                } else {
                    $setupdate = $this->common->update_data('matches', ['match_total_sets' => $selected_type], array('match_id' => $match_id));
                    $result = $this->umpire->get_match_details($match_id, $user_id);
                    $result->set_points = $set_number;
                    $response = [
                        'status' => true,
                        'message' => 'Match Started',
                        'data' => [
                            'match_details' => $result,
                            'scoreboard' => $scoreboard,
                            'player1_points' => $scoreboard[0]['Set_' . $set_number],
                            'player2_points' => $scoreboard[1]['Set_' . $set_number]
                        ]
                    ];
                }
            }
        }

        return $this->getResponse($response);
    }

    public function getBadmintonScoring()
    {
        $response = [];
        $rules = [
            'user_id' => ['label' => 'user_id', 'rules' => 'required|numeric'],
            'match_id' => ['label' => 'match id', 'rules' => 'required|numeric'],
        ];
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);
            $user_id = xss_clean($input['user_id']);
            $match_id = xss_clean($input['match_id']);
            $val = $this->common->select('matches', 'match_total_sets, match_set_complete', array('match_id' => $match_id), false);
            $set_number = $val->match_set_complete ? $val->match_set_complete + 1 : 1;
            $totalpoints = $val->match_total_sets;

            $result = $this->umpire->get_match_details($match_id, $user_id);
            $result->set_points = $set_number;

            $scoreboard = $this->umpire->get_badminton_score($match_id, 3, $totalpoints);
            $scoreboard['match_set_complete'] = $val->match_set_complete ? $val->match_set_complete : 0;
            $scoreboard['selected_total_points'] = $val->match_total_sets;
            $response = [
                'status' => true,
                'message' => 'Scores retrived successfully.',
                'data' => [
                    'match_details' => $result,
                    'scoreboard' => $scoreboard,
                   // 'player1_winings' => $scoreboard[0]['Total_Points'],
                   // 'player2_winings' => $scoreboard[1]['Total_Points']
                ]
            ];
        }

        return $this->getResponse($response);
    }

    public  function add_badminton_score()
    {
        $response = [];
        $rules = [
            'user_id' => ['label' => 'user_id', 'rules' => 'required|numeric'],
            'match_id' => ['label' => 'match_id', 'rules' => 'required|numeric'],
            'player_id' => ['label' => 'player_id', 'rules' => 'required|numeric'],
            'team_id' => ['label' => 'team_id', 'rules' => 'required|numeric'],
            'points' => ['label' => 'points', 'rules' => 'required|numeric'],
            'mode' => ['label' => 'mode', 'rules' => 'required'],
            'set_number_edit' => ['rules' => 'required']

        ];
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);
            $user_id = xss_clean($input['user_id']);
            $match_id = xss_clean($input['match_id']);
            $player_id = xss_clean($input['player_id']);
            $team_id = xss_clean($input['team_id']);
            $points = xss_clean($input['points']);
            $set_number_edit = xss_clean($input['set_number_edit']);
            $mode = xss_clean($input['mode']);
            $set_mode = xss_clean($input['set_mode']);
            $sets = $this->common->select('matches', 'match_total_sets, match_set_complete', array('match_id' => $match_id), false);
            $set_winner =  $this->common->set_winner($match_id,$team_id,$player_id,$set_number_edit);
            $set_number = 1;
            $match_set_complete = $sets->match_set_complete ??0; 
            if ($match_set_complete < 3) {
                $set_number = $sets->match_set_complete + 1;
            } else {
                $set_number = 3;
            }
            $data = array(
                'match_id' => $match_id,
                'player_id' => $player_id,
                'team_id' => $team_id,
                'points' => $points,
                'set_number' => $set_number
            );

            $added = $mode == 'add' ? 'add' : 'undo';
            $completed = 'Try Again.';

            $match_details = $this->umpire->get_match_details($match_id, $user_id);

            if ($sets->match_set_complete == 3 || $sets->match_set_complete > 3) {
                $completed = 'Sets Completed. Please Select Winner. And End the Match.';
                $result = false;
            } else {
                if ($mode == 'add') {
                    $result = $this->common->insert_data('scores_badminton', $data);

                } else {
                    $id = $this->common->select('scores_badminton', 'score_id', array('match_id' => $match_id, 'set_number' => $set_number, 'team_id' => $team_id, 'player_id' => $player_id), false, 'score_id desc');
                    if ($id) {
                        $score_id = $id->score_id;
                        $result = $this->common->delete_data('scores_badminton', array('score_id' => $score_id));
                    } else {
                        $result = false;
                    }
                }
            }

            $scoresboard = $this->umpire->get_badminton_score($match_id, 3, $sets->match_total_sets);
            $scoresboard['match_set_complete'] = $sets->match_set_complete ? $sets->match_set_complete : 0;
            $scoresboard['selected_total_points'] = $sets->match_total_sets;
            if($set_winner == 15 && $set_mode == 15){
                $winner = 1;
            }elseif($set_winner == 21 && $set_mode == 21){
                $winner = 1;
            }else{
                $winner = 0;
            }
           
            if ($result) {
                
                $response = [
                    'status' => true,
                    'message' => 'Score ' . $added . 'ing successfull!',
                    'data' => [
                        'match_details' => $match_details,
                        'scoreboard' => $scoresboard,
                        'player1_points' => $scoresboard[0]['Set_' . $set_number],
                        'player2_points' => $scoresboard[1]['Set_' . $set_number],
                        'winner_details' => $winner,
                    ]
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Score ' . $added . 'ing fail. ' . $completed,
                    'data' => [
                        'match_details' => $match_details,
                        'scoreboard' => $scoresboard,
                        'player1_points' => $scoresboard[0]['Set_' . $set_number],
                        'player2_points' => $scoresboard[1]['Set_' . $set_number],
                        'winner_details' => $winner,
                    ]
                ];
            }
        }
        return $this->getResponse($response);
    }

    public  function selectSetWinner()
    {
        $response = [];
        $rules = [
            'match_id' => ['label' => 'match id', 'rules' => 'required|numeric'],
            'team_id' => ['label' => 'team or individual id', 'rules' => 'required|numeric'],
            'set_number' => ['label' => 'set_number', 'rules' => 'required|numeric'],
            'desc'  => ['label' => 'description', 'rules' => 'required|min_length[5]'],
        ];
        if (! $this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);
            $match_id = xss_clean($input['match_id']);
            $set_number = xss_clean($input['set_number']);
            $team_id = xss_clean($input['team_id']);
            $desc = xss_clean($input['desc']);
            $sets = $this->common->select('matches', 'match_total_sets, match_set_complete', array('match_id' => $match_id), false);

            $set_winnings = $this->common->select('set_winnings', '*', array('sets_match_id' => $match_id, 'sets_set_number' => $set_number), false);
            if (!empty($set_winnings)) {
                $updatedata['sets_team_id'] = $team_id;
                $updatedata['sets_winning_desc'] = $desc;
                $set_number_update = $sets->match_set_complete > $set_number ? $sets->match_set_complete : $set_number;
                $setupdate = $this->common->update_data('matches', ['match_set_complete' => $set_number_update], array('match_id' => $match_id));
                $setupdate = $this->common->update_data('matches', ['match_set_complete' => $set_number_update], array('match_id' => $match_id));
                $result = $this->common->update_data('set_winnings', $updatedata, array('sets_id' => $set_winnings->sets_id));
                $response = [
                    'status' => true,
                    'message' => 'Set Winner updated.'
                ];
            } else {
                $updatedata['sets_match_id'] = $match_id;
                $updatedata['sets_set_number'] = $set_number;
                $updatedata['sets_team_id'] = $team_id;
                $updatedata['sets_winning_desc'] = $desc;
                $setupdate = $this->common->update_data('matches', ['match_set_complete' => $set_number], array('match_id' => $match_id));
                $result = $this->common->insert_data('set_winnings', $updatedata);
                $response = [
                    'status' => true,
                    'message' => 'Set Winner added.'
                ];
            }
        }

        return $this->getResponse($response);
    }

    public function selectWinner()
    {
        $response = [];
        $rules = [
            'match_id' => ['label' => 'match id', 'rules' => 'required|numeric'],
            'team_id' => ['label' => 'team or individual id', 'rules' => 'required|numeric'],
            'desc'  => ['label' => 'description', 'rules' => 'required|min_length[5]'],
        ];
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);
            $match_id = xss_clean($input['match_id']);
            $team_id = xss_clean($input['team_id']);
            $desc = xss_clean($input['desc']);

            $match = $this->common->select('matches', '*', array('match_id' => $match_id));
            if (!empty($match)) {
                $updatedata['match_winning_desc'] = $desc;
                if ($match->match_type == 'individual') {
                    $updatedata['match_winning_individual_id'] = $team_id;
                } else {
                    $updatedata['match_winning_team_id'] = $team_id;
                }
                $result = $this->common->update_data('matches', $updatedata, array('match_id' => $match_id));
                $response = [
                    'status' => true,
                    'message' => 'Winner select updated.'
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Match not exist'
                ];
            }
        }

        return $this->getResponse($response);
    }

    public function complete_match()
    {
        $response = [];
        $rules = [
            'match_id' => ['label' => 'match_id', 'rules' => 'required|numeric'],
        ];
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);
            $match_id = xss_clean($input['match_id']);

            $match = $this->common->select('matches', '*', array('match_id' => $match_id));
            if (!empty($match)) {
                $updatedata['match_status'] = 0;
                $result = $this->common->update_data('matches', $updatedata, array('match_id' => $match_id));
                $response = [
                    'status' => true,
                    'message' => 'Match Completed.'
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Match not exist'
                ];
            }
        }

        return $this->getResponse($response);
    }

    public function editBadmintonMatch()
    {
        $response = [];
        $rules = [
            'set_number' => ['rules' => 'required|numeric'],
            'player_1' => ['rules' => 'required|numeric'],
            'player_2' => ['rules' => 'required|numeric'],
        ];
        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        } else {
            $input = $this->getRequestInput($this->request);
            $set_number = xss_clean($input['set_number']);
            $player1 = xss_clean($input['player_1']);
            $player2 = xss_clean($input['player_2']);
            $match_id = xss_clean($input['match_id']);
            $team_id = xss_clean($input['team_id']);
            $player_id1 = xss_clean($input['player_id1']);
            $player_id2 = xss_clean($input['player_id2']);

            $data1 = array(
                'match_id' => $match_id,
                'player_id' => $player_id1,
                'team_id' => $team_id,
                'points' => $player1,
                'set_number' => $set_number
            );
            $data2 = array(
                'match_id' => $match_id,
                'player_id' => $player_id2,
                'team_id' => $team_id,
                'points' => $player2,
                'set_number' => $set_number
            );
            $data3 = array(
                'match_id' => $match_id,
                'player_id' => $player_id1,
                'team_id' => $team_id,
                'points' => 0,
                'set_number' => $set_number
            );
            $data4 = array(
                'match_id' => $match_id,
                'player_id' => $player_id2,
                'team_id' => $team_id,
                'points' => 0,
                'set_number' => $set_number
            );

           $result1 =  $this->common->change_data('scores_badminton', $data3);
           $result2 = $this->common->change_data('scores_badminton', $data4);
           $this->common->insert_data('scores_badminton', $data1);
           $this->common->insert_data('scores_badminton', $data2);
           
            $response = [
                'status' => true,
                'message' => 'Points Updated'
            ];
      
        }

        return $this->getResponse($response);
    }
    // --------------------------Badmonton API END-----------------------------
}
