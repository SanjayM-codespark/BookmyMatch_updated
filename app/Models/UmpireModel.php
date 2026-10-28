<?php

namespace App\Models;

use CodeIgniter\Model;

class UmpireModel extends Model
{
    public function get_row($table, $where)
    {
        return $this->db->table($table)->where($where)->get()->getRow();
    }

    public function count_match_list($user_id)
    {
        $builder = $this->db->table('matches as m');
        $builder->select('m.*,r.round_name,l.*,ev.event_name,t1.team_name as team1, t2.team_name as team2, ump.user_full_name as umpire_name');
        $builder->join('tbl_rounds as r', 'r.round_id = m.match_round_id');
        $builder->join('listings as l', 'l.listing_id = m.match_tournament');
        $builder->join('listing_events as e', 'e.listing_event_id = m.match_event');
        $builder->join('events as ev', 'ev.event_id = e.listing_event_title', 'left');
        $builder->join('teams as t1', 't1.team_id = m.match_team1_id', 'left');
        $builder->join('teams as t2', 't2.team_id = m.match_team2_id', 'left');
        $builder->join('users as ump', 'ump.user_id = m.match_umpire', 'left');
        

        $builder->where('m.match_umpire', $user_id);

        return $builder->countAllResults();
    }

    public function get_match_list($status, $per_page, $offset, $user_id)
    {
        $builder = $this->db->table('matches as m');
        $builder->select('s.sports_name, l.listing_sports_id as sport_id, m.*,r.round_name,l.*,ev.event_name,t1.team_name as team1, t2.team_name as team2, ump.user_full_name as umpire_name');
        $builder->join('tbl_rounds as r', 'r.round_id = m.match_round_id');
        $builder->join('listings as l', 'l.listing_id = m.match_tournament');
        $builder->join('listing_events as e', 'e.listing_event_id = m.match_event');
        $builder->join('events as ev', 'ev.event_id = e.listing_event_title', 'left');
        $builder->join('sports as s', 's.sports_id = l.listing_sports_id', 'left');
        $builder->join('teams as t1', 't1.team_id = m.match_team1_id OR t1.team_id = m.match_individual1_id', 'left');
        $builder->join('teams as t2', 't2.team_id = m.match_team2_id OR t2.team_id = m.match_individual2_id', 'left');
        $builder->join('users as ump', 'ump.user_id = m.match_umpire', 'left');

        $builder->where('m.match_umpire', $user_id);
        $builder->where('m.match_status', $status);

        $builder->limit($per_page, $offset);
        $builder->groupBy('m.match_id');
        $builder->orderBy('m.match_date', 'ASC');
        $query = $builder->get();

        if ($query->getNumRows() > 0) {
            $results = $query->getResult();

            foreach ($results as $row) {
                $row->match_cricket = $this->_buildCricketData($row);
            }

            return $results;
        }
        return false;
    }

    private function _buildCricketData($row)
    {
        $team1 = $row->match_team1_id ?? $row->match_individual1_id ?? 0;
        $team2 = $row->match_team2_id ?? $row->match_individual2_id ?? 0;

        $default = [
            'toss_winner'  => 0,
            'bat_first'    => (int) $team1,
            'toss_choice'  => null,
            'innings'      => new \stdClass(),
            'batting_team' => 0,
            'bowling_team' => 0,
            'overs'        => 0,
        ];

        if (empty($row->match_cricket)) {
            return json_encode($default);
        }

        $decoded = json_decode($row->match_cricket, true);
        if (!is_array($decoded)) {
            return json_encode($default);
        }

        $innings = $decoded['innings'] ?? [];

        $teamIds = array_map('intval', array_keys($innings));

        $bat_first = (int) ($decoded['bat_first'] ?? ($teamIds[0] ?? $team1));

        $bat_second = 0;
        foreach ($teamIds as $tid) {
            if ($tid !== $bat_first) {
                $bat_second = $tid;
                break;
            }
        }

        if ($bat_second === 0) {
            $bat_second = ((int) $team1 === $bat_first) ? (int) $team2 : (int) $team1;
        }

        if (!empty($innings[(string) $bat_second]) && is_array($innings[(string) $bat_second])) {
            $batting_team = $bat_second;
            $bowling_team = $bat_first;
        } else {
            $batting_team = $bat_first;
            $bowling_team = $bat_second;
        }
        

        $overs = $innings[$batting_team]['overs'] ?? 0;

        $decoded['batting_team'] = $batting_team;
        $decoded['bowling_team'] = $bowling_team;
        // $decoded['overs']        = $overs;

        return json_encode($decoded);
    }

    public function get_match_details($match_id, $user_id)
    {
        $builder = $this->db->table('matches as m');
        $builder->select('m.*,r.round_name,l.*,ev.event_name,t1.team_name as team1, t2.team_name as team2, ump.user_full_name as umpire_name');
        $builder->join('tbl_rounds as r', 'r.round_id = m.match_round_id');
        $builder->join('listings as l', 'l.listing_id = m.match_tournament');
        $builder->join('listing_events as e', 'e.listing_event_id = m.match_event');
        $builder->join('events as ev', 'ev.event_id = e.listing_event_title', 'left');
        $builder->join('teams as t1', 't1.team_id = m.match_team1_id OR t1.team_id = m.match_individual1_id', 'left');
        $builder->join('teams as t2', 't2.team_id = m.match_team2_id OR t2.team_id = m.match_individual2_id', 'left');
        $builder->join('users as ump', 'ump.user_id = m.match_umpire', 'left');

        $builder->where('m.match_id', $match_id);
        $builder->where('m.match_umpire', $user_id);
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            $results = $query->getRow();

            return $results;
        }
        return false;
    }

    public function get_umpire_notification($user_id)
    {
        $builder = $this->db->table('matches m');
        $builder->select('m.*, l.listing_sports_id, s.sports_name, ABS(TIMESTAMPDIFF(second, m.match_date, NOW())) AS total_seconds');
        $builder->join('listings l', 'l.listing_id = m.match_tournament', 'left');
        $builder->join('sports s', 's.sports_id = l.listing_sports_id', 'left');
        $builder->where('m.match_umpire', $user_id);
        $builder->where('TIMESTAMPDIFF(SECOND, m.match_date, NOW()) >=', -86400 * 12); // 12 days
        $builder->where('TIMESTAMPDIFF(SECOND, m.match_date, NOW()) <', 0);
        $builder->orderBy('m.match_date', 'ASC');
        $query = $builder->get();
        return $query;
    }

    public function get_team_list($team_id)
    {
        $builder = $this->db->table('participants as p');
        $builder->select('t.team_id, t.team_name, p.*');
        $builder->join('teams t', 't.team_id = p.participant_team_id', 'left');
        $builder->where('p.participant_team_id', $team_id);
        $builder->orderBy('p.participant_name', 'ASC');
        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            $results = $query->getResult();

            return $results;
        }
        return false;
    }

    public function get_badminton_score($match_id, $set_number, $totalpoints)
    {
        $sets = '';
        $set_number = $set_number ?: 3;
        for ($i = 1; $i <= $set_number; $i++) {
            $comma =  $i == $set_number ? '' : ',';
            $sets .= "SUM(CASE WHEN sb.set_number = $i THEN sb.points ELSE 0 END) AS Set_$i $comma";
        }
          $match = $this->db->table('matches')
        ->select('*')
        ->where('match_id', $match_id)
        ->get()
        ->getRow();
        if($match->match_type == 'individual'){
            $query = "SELECT 
                    p.participant_name AS Player,
                    p.participant_id AS participant_id,
                    $sets
                FROM participants p 
                LEFT JOIN matches m 
                    ON m.match_individual1_id = p.participant_id 
                    OR m.match_individual2_id = p.participant_id 
                LEFT JOIN scores_badminton sb 
        ON p.participant_id = sb.player_id AND sb.match_id = '$match_id'
                WHERE 
                    m.match_id = '$match_id'
                GROUP BY 
                    p.participant_id
                ORDER BY 
                    p.participant_id";
        }else{
        $query = "SELECT 
                    p.participant_name AS Player,
                    p.participant_id AS participant_id,
                    $sets
                FROM participants p 
                LEFT JOIN matches m 
                    ON m.match_individual1_id = p.participant_team_id 
                    OR m.match_individual2_id = p.participant_team_id 
                LEFT JOIN scores_badminton sb 
        ON p.participant_id = sb.player_id AND sb.match_id = '$match_id'
                WHERE 
                    m.match_id = '$match_id'
                GROUP BY 
                    p.participant_id
                ORDER BY 
                    p.participant_id";
        }
        $scores = $this->db->query($query)->getResultArray();
        if (count($scores) == 2) {
            $player1_winning = 0;
            $player2_winning = 0;
            $set_number = 3;
            $maxpoints = ($totalpoints === 21) ? 30 : 20;
            for ($i = 1; $i <= $set_number; $i++) {
                $player1_score = $scores[0]['Set_' . $i];
                $player2_score = $scores[1]['Set_' . $i];
                if ($player1_score >= $totalpoints || $player2_score >= $totalpoints) {
                    if (abs($player1_score - $player2_score) >= 2) {
                        if ($player1_score > $player2_score) {
                            $player1_winning++;
                        } else {
                            $player2_winning++;
                        }
                    } elseif (max($player1_score, $player2_score) == $maxpoints) {
                        if ($player1_score > $player2_score) {
                            $player1_winning++;
                        } else {
                            $player2_winning++;
                        }
                    }
                }
            }

            $scores[0]['Total_Points'] = $player1_winning;
            $scores[1]['Total_Points'] = $player2_winning;
        }

        return $scores;
    }

    function formatOvers($balls)
    {
        if ($balls) {
            return number_format(floor($balls / 6) + ($balls % 6) / 10, 1);
        } else {
            return number_format(0, 1);
        }
    }

    /**
     * FIX (Bug 2 root cause): the previous version used
     *   SELECT *, SUM(...) AS balls FROM scores_cricket WHERE match_id = ? GROUP BY overs
     * which is not valid ONLY_FULL_GROUP_BY SQL — every ungrouped column
     * (batsman_one, batsman_two, bowler, wickets, overs_bowled, etc.) can be
     * pulled from ANY row within that over, not necessarily the most recent
     * ball. The undo logic in updateCricketScore() depends on this row
     * reflecting the true last delivery to correctly restore is_batting/wicket
     * flags. When MySQL picked a non-last row, undo could wrongly leave no
     * batsman marked is_batting = 1, which made getCricketmMatchData() return
     * "Please select batsmen first." even though nothing should have changed.
     *
     * Fix: fetch the true last delivery deterministically, then compute the
     * "balls in this over" count as a separate, correct aggregate.
     */
    public function getCurrentOverData($match_id)
    {
        $last_ball = $this->db->table('scores_cricket')
            ->where('match_id', $match_id)
            ->orderBy('created_at', 'DESC')
            ->orderBy('score_id', 'DESC') // tie-breaker if created_at collides
            ->get(1)
            ->getRow();

        if (!$last_ball) {
            return null;
        }

        // NOTE: selectSum()/selectMax() etc. auto-protect the identifier they're
        // given and cannot parse a CASE...END expression, which mangles it into
        // invalid SQL (splits off "END" as its own backticked identifier). Use
        // select() with the escape flag set to false so the raw expression is
        // passed through untouched.
        $balls_row = $this->db->table('scores_cricket')
            ->select("SUM(CASE WHEN balls_faced = 1 THEN 1 ELSE 0 END) AS balls", false)
            ->where('match_id', $match_id)
            ->where('overs', $last_ball->overs)
            ->get()
            ->getRow();

        $last_ball->balls = $balls_row->balls ?? 0;

        return $last_ball;
    }
    
    
    public function getCricketmMatchData($match_id)
    {
        
        $toss_winner_team = "";
        $toss_winner_select = "";

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
                SELECT 
                    batting_team_id, 
                    SUM(runs_scored) + SUM(total_extras) AS runs, 
                    SUM(wickets) AS wickets, 
                    SUM(balls_faced) AS balls_faced 
                FROM scores_cricket 
                WHERE match_id = ?
                GROUP BY batting_team_id
            ) batting ON t.team_id = batting.batting_team_id
            WHERE m.match_id = ?",
            [$match_id, $match_id]
        );

        $teams = $teamQuery->getResultArray();
        $teamData = [];
        $match_cricket = null;
        $batting_team_id = 0;
        $bowling_team_id = 0;
        $match_overs_balls = 0;

        foreach ($teams as $key => $team) {
            if (!$team['match_cricket']) continue;

            $match_cricket = json_decode($team['match_cricket']);
            $toss_win_team_id = explode('%%', $match_cricket->toss_win);
            if ($toss_win_team_id[0] == $team['team_id']) {
                $toss_winner_team = $team['team_name'];
                $toss_winner_select = $toss_win_team_id[1] ?? 'batting';
            }

            $batting_team_id = $match_cricket->batting_team ?? 0;
            $bowling_team_id = $match_cricket->bowling_team ?? 0;

            // remember balls faced for whichever team is currently batting
            if ($team['team_id'] == $batting_team_id) {
                $match_overs_balls = $team['balls_faced'];
            }

            $current_over = self::formatOvers($team['balls_faced']);
            $teamData[] = [
                "team_id"       => $team['team_id'],
                "team_name"     => $team['team_name'],
                "runs"          => $team['runs'],
                "wickets"       => $team['wickets'],
                "overs"         => $current_over,
                "is_batting"    => ($match_cricket->batting_team == $team['team_id']) ? 1 : 0
            ];
        }

        // Get current batsmen and bowler from players table
        $current_players = $this->db->query(
            "SELECT 
                player_participant_id,
                player_id,
                name,
                team_id,
                batsman,
                is_batting,
                bowler
            FROM players
            WHERE player_match_id = ?
            AND (batsman = 1 OR bowler = 1)",
            [$match_id]
        )->getResult();

        $batsman_one = null;
        $batsman_two = null;
        $bowler = null;

        foreach ($current_players as $player) {
            if ($player->batsman == 1 && $player->team_id == $batting_team_id) {
                if ($player->is_batting == 1) {
                    $batsman_one = $player;
                } else {
                    $batsman_two = $player;
                }
            }
            if ($player->bowler == 1 && $player->team_id == $bowling_team_id) {
                $bowler = $player;
            }
        }

        // If no batsmen found, return early
        if (!$batsman_one) {
            return ["message" => "Please select batsmen first."];
        }

        // Build batting data
        $battingData = [];

        // Batsman One Data
        $battingData[] = $this->getBatsmanStats($match_id, $batsman_one, 1);

        // Batsman Two Data (if exists)
        if ($batsman_two) {
            $battingData[] = $this->getBatsmanStats($match_id, $batsman_two, 0);
        } else {
            // If no batsman two, add empty placeholder
            $battingData[] = [
                "player_id"     => 0,
                "player_name"   => "Not Selected",
                "runs"          => 0,
                "balls"         => 0,
                "fours"         => 0,
                "sixes"         => 0,
                "is_batting"    => 0,
            ];
        }

        // Build bowling data
        $bowlingData = [];
        if ($bowler) {
            $bowlingData[] = $this->getBowlerStats($match_id, $bowler);
        } else {
            $bowlingData[] = [
                "player_id"     => 0,
                "player_name"   => "Not Selected",
                "overs"         => 0,
                "maidens"       => 0,
                "runs"          => 0,
                "wickets"       => 0,
            ];
        }

        // Get over data
        $current_over_number = $match_cricket->bowling_overs ?? 0;
        $overQuery = $this->db->query(
            "SELECT
                overs_bowled AS ball,
                runs_scored AS run,
                wides,
                no_balls,
                byes,
                leg_byes,
                total_extras,
                wickets,
                hit_wickets,
                catches,
                stumpings,
                lbws,
                runouts,
                non_striker_runouts
            FROM scores_cricket
            WHERE match_id = ?
            AND overs = ?",
            [$match_id, $current_over_number]
        );

        $overData = [
            "overs" => self::formatOvers($match_overs_balls),
            "over_balls" => []
        ];

        $ballData = $overQuery->getResult();
        $nb = 1;
        foreach ($ballData as $index => $ball) {
            $status = '';
            $runValue = $ball->run; // Store the runs scored on this ball
            
            if ($ball->wides == 1) {
                $status = 'w';
            } elseif ($ball->no_balls == 1) {
                $status = 'nb' . $nb;
                $nb++;
            } elseif ($ball->byes == 1) {
                $status = 'b';
            } elseif ($ball->leg_byes == 1) {
                $status = 'lb';
            } elseif ($ball->wickets == 1 || $ball->hit_wickets == 1 || 
                      $ball->catches == 1 || $ball->stumpings == 1 || 
                      $ball->lbws == 1 || $ball->runouts == 1 || 
                      $ball->non_striker_runouts == 1) {
                $status = 'out';
            }
            
            // Determine the final run value based on status and type of out
            if ($status == 'out') {
                // Check if it's a run out (runouts == 1)
                if ($ball->runouts == 1) {
                    // If runs were scored on the run out, show runs + 'W'
                    if ($runValue > 0) {
                        $finalRun = $runValue . '+W';
                    } else {
                        $finalRun = 'W';
                    }
                } else {
                    // For other types of outs (caught, bowled, LBW, stumped, etc.)
                    $finalRun = 'W';
                }
            } else {
                // Not an out - show the run value
                $finalRun = $runValue + $ball->total_extras;
            }
            
            $overData["over_balls"][] = [
                "ball"   => $ball->ball,
                "run"    => $finalRun,
                "status" => $status,
            ];
        }

        return [
            "top_team_data"     => $teamData,
            "toss_winner_text"  => "$toss_winner_team won the toss and selected $toss_winner_select first.",
            "batting_data"      => $battingData,
            "bowling_data"      => $bowlingData,
            "over_data"         => $overData
        ];
    }

    // public function getCricketmMatchData($match_id)
    // {
        
    //     $toss_winner_team = "";
    //     $toss_winner_select = "";

    //     $teamQuery = $this->db->query(
    //         "SELECT 
    //             t.team_id,
    //             t.team_name,
    //             m.match_cricket,
    //             COALESCE(batting.runs, 0) AS runs,
    //             COALESCE(batting.wickets, 0) AS wickets,
    //             COALESCE(batting.balls_faced, 0) AS balls_faced
    //         FROM teams t
    //         LEFT JOIN matches m 
    //             ON t.team_id = m.match_team1_id 
    //             OR t.team_id = m.match_team2_id
    //         LEFT JOIN (
    //             SELECT 
    //                 batting_team_id, 
    //                 SUM(runs_scored) + SUM(total_extras) AS runs, 
    //                 SUM(wickets) AS wickets, 
    //                 SUM(balls_faced) AS balls_faced 
    //             FROM scores_cricket 
    //             WHERE match_id = ?
    //             GROUP BY batting_team_id
    //         ) batting ON t.team_id = batting.batting_team_id
    //         WHERE m.match_id = ?",
    //         [$match_id, $match_id]
    //     );

    //     $teams = $teamQuery->getResultArray();
    //     $teamData = [];
    //     $match_cricket = null;
    //     $batting_team_id = 0;
    //     $bowling_team_id = 0;
    //     $match_overs_balls = 0;

    //     foreach ($teams as $key => $team) {
    //         if (!$team['match_cricket']) continue;

    //         $match_cricket = json_decode($team['match_cricket']);
    //         $toss_win_team_id = explode('%%', $match_cricket->toss_win);
    //         if ($toss_win_team_id[0] == $team['team_id']) {
    //             $toss_winner_team = $team['team_name'];
    //             $toss_winner_select = $toss_win_team_id[1] ?? 'batting';
    //         }

    //         $batting_team_id = $match_cricket->batting_team ?? 0;
    //         $bowling_team_id = $match_cricket->bowling_team ?? 0;

    //         // remember balls faced for whichever team is currently batting
    //         if ($team['team_id'] == $batting_team_id) {
    //             $match_overs_balls = $team['balls_faced'];
    //         }

    //         $current_over = self::formatOvers($team['balls_faced']);
    //         $teamData[] = [
    //             "team_id"       => $team['team_id'],
    //             "team_name"     => $team['team_name'],
    //             "runs"          => $team['runs'],
    //             "wickets"       => $team['wickets'],
    //             "overs"         => $current_over,
    //             "is_batting"    => ($match_cricket->batting_team == $team['team_id']) ? 1 : 0
    //         ];
    //     }

    //     // Get current batsmen and bowler from players table
    //     $current_players = $this->db->query(
    //         "SELECT 
    //             player_participant_id,
    //             player_id,
    //             name,
    //             team_id,
    //             batsman,
    //             is_batting,
    //             bowler
    //         FROM players
    //         WHERE player_match_id = ?
    //         AND (batsman = 1 OR bowler = 1)",
    //         [$match_id]
    //     )->getResult();

    //     $batsman_one = null;
    //     $batsman_two = null;
    //     $bowler = null;

    //     foreach ($current_players as $player) {
    //         if ($player->batsman == 1 && $player->team_id == $batting_team_id) {
    //             if ($player->is_batting == 1) {
    //                 $batsman_one = $player;
    //             } else {
    //                 $batsman_two = $player;
    //             }
    //         }
    //         if ($player->bowler == 1 && $player->team_id == $bowling_team_id) {
    //             $bowler = $player;
    //         }
    //     }

    //     // If no batsmen found, return early
    //     if (!$batsman_one) {
    //         return ["message" => "Please select batsmen first."];
    //     }

    //     // Build batting data
    //     $battingData = [];

    //     // Batsman One Data
    //     $battingData[] = $this->getBatsmanStats($match_id, $batsman_one, 1);

    //     // Batsman Two Data (if exists)
    //     if ($batsman_two) {
    //         $battingData[] = $this->getBatsmanStats($match_id, $batsman_two, 0);
    //     } else {
    //         // If no batsman two, add empty placeholder
    //         $battingData[] = [
    //             "player_id"     => 0,
    //             "player_name"   => "Not Selected",
    //             "runs"          => 0,
    //             "balls"         => 0,
    //             "fours"         => 0,
    //             "sixes"         => 0,
    //             "is_batting"    => 0,
    //         ];
    //     }

    //     // Build bowling data
    //     $bowlingData = [];
    //     if ($bowler) {
    //         $bowlingData[] = $this->getBowlerStats($match_id, $bowler);
    //     } else {
    //         $bowlingData[] = [
    //             "player_id"     => 0,
    //             "player_name"   => "Not Selected",
    //             "overs"         => 0,
    //             "maidens"       => 0,
    //             "runs"          => 0,
    //             "wickets"       => 0,
    //         ];
    //     }

    //     // Get over data
    //     $current_over_number = $match_cricket->bowling_overs ?? 0;
    //     $overQuery = $this->db->query(
    //         "SELECT
    //             overs_bowled AS ball,
    //             runs_scored AS run,
    //             wides,
    //             no_balls,
    //             byes,
    //             leg_byes,
    //             total_extras,
    //             wickets,
    //             hit_wickets,
    //             catches,
    //             stumpings,
    //             lbws,
    //             runouts,
    //             non_striker_runouts
    //         FROM scores_cricket
    //         WHERE match_id = ?
    //         AND overs = ?",
    //         [$match_id, $current_over_number]
    //     );

    //     $overData = [
    //         "overs" => self::formatOvers($match_overs_balls),
    //         "over_balls" => []
    //     ];

    //     $ballData = $overQuery->getResult();
    //     $nb = 1;
    //     foreach ($ballData as $index => $ball) {
    //         $status = '';
    //         if ($ball->wides == 1) {
    //             $status = 'w';
    //         } elseif ($ball->no_balls == 1) {
    //             $status = 'nb' . $nb;
    //             $nb++;
    //         } elseif ($ball->byes == 1) {
    //             $status = 'b';
    //         } elseif ($ball->leg_byes == 1) {
    //             $status = 'lb';
    //         } elseif ($ball->wickets == 1 || $ball->hit_wickets == 1 || 
    //                   $ball->catches == 1 || $ball->stumpings == 1 || 
    //                   $ball->lbws == 1 || $ball->runouts == 1 || 
    //                   $ball->non_striker_runouts == 1) {
    //             $status = 'out';
    //         }
    //         $overData["over_balls"][] = [
    //             "ball"   => $ball->ball,
    //             "run"    => $ball->run + $ball->total_extras,
    //             "status" => $status,
    //         ];
    //     }

    //     return [
    //         "top_team_data"     => $teamData,
    //         "toss_winner_text"  => "$toss_winner_team won the toss and selected $toss_winner_select first.",
    //         "batting_data"      => $battingData,
    //         "bowling_data"      => $bowlingData,
    //         "over_data"         => $overData
    //     ];
    // }


    /**
     * FIX (Bug 1 - doubled runs): the previous version LEFT JOINed `players`
     * on player_participant_id only, with no player_match_id filter. Since
     * `players` has one row per participant PER MATCH, a participant who has
     * appeared in more than one match in the tournament produced multiple
     * matching rows here, fanning out every scores_cricket row by that many
     * matches. SUM(s.runs_scored) then multiplied accordingly (2 matches
     * played previously = runs doubled), even though this join's columns
     * (from `p`) were never even selected. Removed the unused join entirely.
     */
    private function getBatsmanStats($match_id, $player, $is_batting)
    {
        $query = $this->db->query(
            "SELECT
                par.participant_id,
                par.participant_name as name,
                COALESCE(SUM(s.runs_scored), 0) AS runs,
                COALESCE(SUM(s.balls_faced), 0) AS balls,
                COALESCE(SUM(s.fours), 0) AS fours,
                COALESCE(SUM(s.sixes), 0) AS sixes
            FROM scores_cricket s 
            LEFT JOIN participants par ON par.participant_id = s.batsman_one
            WHERE s.match_id = ? AND s.batsman_one = ?
            GROUP BY par.participant_id",
            [$match_id, $player->player_participant_id]
        );

        $stats = $query->getRow();

        if ($stats) {
            return [
                "player_id"     => $stats->participant_id,
                "player_name"   => $stats->name,
                "runs"          => $stats->runs,
                "balls"         => $stats->balls,
                "fours"         => $stats->fours,
                "sixes"         => $stats->sixes,
                "is_batting"    => $is_batting,
            ];
        } else {
            return [
                "player_id"     => $player->player_participant_id,
                "player_name"   => $player->name,
                "runs"          => 0,
                "balls"         => 0,
                "fours"         => 0,
                "sixes"         => 0,
                "is_batting"    => $is_batting,
            ];
        }
    }

    /**
     * FIX (Bug 1 - same root cause as getBatsmanStats): removed the unused,
     * unscoped LEFT JOIN to `players` that was fanning out and doubling
     * (or worse) bowling figures (runs conceded, wickets, overs) for any
     * bowler who has appeared in more than one match.
     */
    private function getBowlerStats($match_id, $player)
    {
        $query = $this->db->query(
            "SELECT
                par.participant_id,
                par.participant_name AS name,
                COALESCE(SUM(s.balls_faced), 0) AS balls_faced,
                COALESCE(SUM(s.maidens), 0) AS maidens,
                COALESCE(SUM(s.runs_scored) + SUM(s.total_extras), 0) AS runs,
                COALESCE(SUM(s.wickets), 0) AS wickets 
            FROM scores_cricket s 
            LEFT JOIN participants par ON par.participant_id = s.bowler
            WHERE s.match_id = ? AND s.bowler = ?
            GROUP BY s.bowler",
            [$match_id, $player->player_participant_id]
        );

        $stats = $query->getRow();

        if ($stats) {
            return [
                "player_id"     => $stats->participant_id,
                "player_name"   => $stats->name,
                "overs"         => self::formatOvers($stats->balls_faced),
                "maidens"       => $stats->maidens,
                "runs"          => $stats->runs,
                "wickets"       => $stats->wickets,
            ];
        } else {
            return [
                "player_id"     => $player->player_participant_id,
                "player_name"   => $player->name,
                "overs"         => 0,
                "maidens"       => 0,
                "runs"          => 0,
                "wickets"       => 0,
            ];
        }
    }

    /**
     * Legacy/alternate builder kept for the legacy openCricketMatch1() flow.
     * Left unchanged from the original except that it is no longer used by
     * the "fixed" endpoints below — retained only for backward compatibility
     * with any caller still wired to openCricketMatch1().
     */
    public function getCricketmMatchData1($match_id)
    {
        // Fetch team data (total runs, wickets, overs)
        $toss_winner_team   = "";
        $toss_winner_select = "";
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
                SELECT 
                    batting_team_id, 
                    SUM(runs_scored) + SUM(total_extras) AS runs, 
                    SUM(wickets) AS wickets, 
                    SUM(balls_faced) AS balls_faced 
                FROM scores_cricket 
                WHERE match_id = $match_id
                GROUP BY batting_team_id
            ) batting ON t.team_id = batting.batting_team_id
            LEFT JOIN (
                SELECT 
                    bowling_team_id, 
                    MAX(overs_bowled) AS total_overs 
                FROM scores_cricket 
                WHERE match_id = ? 
                GROUP BY bowling_team_id
            ) bowling ON t.team_id = bowling.bowling_team_id
            WHERE m.match_id = ?",
            [$match_id, $match_id]
        );

        $teams          = $teamQuery->getResultArray();
        $teamData       = [];
        $match_cricket  = [];
        $current_batting_over = 0;
        foreach ($teams as $key => $team) {
            $match_cricket = json_decode($team['match_cricket']);
            $toss_win_team_id = explode('%%', $match_cricket->toss_win);
            $toss_winner_team .= $toss_win_team_id[0] == $team['team_id'] ? $team['team_name'] : "";
            $current_over = self::formatOvers($team['balls_faced']);
            $current_over_arr[] = $current_over;
            $teamData[] = [
                "team_id"       => $team['team_id'],
                "team_name"     => $team['team_name'],
                "runs"          => $team['runs'],
                "wickets"       => $team['wickets'],
                "overs"         => $current_over,
                "is_batting"    => ($match_cricket->batting_team == $team['team_id']) ? 1 : 0
            ];
        }

        $current_bowling = $this->db->query(
            "SELECT 
                MAX(CASE WHEN batsman = 1 AND is_batting = 1 THEN player_participant_id END) AS batsman_one,
                MAX(CASE WHEN batsman = 1 AND is_batting = 0 THEN player_participant_id END) AS batsman_two,
                MAX(CASE WHEN bowler  = 1 THEN player_participant_id END) AS bowler
            FROM players
            WHERE player_match_id = ?
            GROUP BY player_match_id",
            [$match_id]
        )->getRow();

        $current_batsmen = [$current_bowling->batsman_one, $current_bowling->batsman_two];
        if (count(array_filter($current_batsmen, null)) == 2) {
            $battingData = [];
            foreach ($current_batsmen as $index => $current_batsman) {
                $is_batting_id = $index == 0 ? $current_batsman : 0;
                $battingQuery = $this->db->query(
                    "SELECT
                        par.participant_id,
                        par.participant_name as name
                        , SUM(s.runs_scored) AS runs
                        , SUM(s.balls_faced) AS balls
                        , SUM(s.fours) AS fours
                        , SUM(s.sixes) AS sixes
                        , CASE WHEN s.batsman_one = $is_batting_id THEN 1 ELSE 0 END AS is_batting
                    FROM
                        scores_cricket s 
                    LEFT JOIN participants par ON par.participant_id = s.batsman_one
                    WHERE
                        s.match_id = ? AND s.batsman_one = ? GROUP BY participant_id",
                    [$match_id, $current_batsman]
                );
                $getbatsman = $battingQuery->getRow();
                if (!empty($getbatsman)) {
                    $battingData[] = [
                        "player_id"     => $getbatsman->participant_id,
                        "player_name"   => $getbatsman->name,
                        "runs"          => $getbatsman->runs,
                        "balls"         => $getbatsman->balls,
                        "fours"         => $getbatsman->fours,
                        "sixes"         => $getbatsman->sixes,
                        "is_batting"    => $getbatsman->is_batting,
                    ];
                } else {
                    $praticipants = $this->db->query("SELECT * FROM players WHERE player_participant_id = $current_batsman")->getRow();
                    $battingData[] = [
                        "player_id"     => $praticipants->player_participant_id ?? 0,
                        "player_name"   => $praticipants->name ?? 0,
                        "runs"          => 0,
                        "balls"         => 0,
                        "fours"         => 0,
                        "sixes"         => 0,
                        "is_batting"    => 0,
                    ];
                }
            }

            $bowlingQuery = $this->db->query(
                "SELECT
                    par.participant_id
                    , par.participant_name AS name
                    , SUM(s.balls_faced) AS overs
                    , s.maidens
                    , SUM(s.runs_scored) + SUM(s.total_extras) AS runs
                    , SUM(s.wickets) AS wickets 
                FROM
                    scores_cricket s 
                LEFT JOIN participants par ON par.participant_id = s.bowler
                WHERE s.match_id = ?
                AND s.bowler = ?
                GROUP BY s.bowler 
                ORDER BY s.overs_bowled DESC",
                [$match_id, $current_bowling->bowler]
            );
            $bowler = $bowlingQuery->getRowArray();
            $over = isset($bowler['overs']) ? self::formatOvers($bowler['overs']) : 0;

            if (!empty($bowler)) {
                $bowlingData[] = [
                    "player_id"     => $bowler['participant_id'],
                    "player_name"   => $bowler['name'],
                    "overs"         => isset($bowler['overs']) ? self::formatOvers($bowler['overs']) : 0,
                    "maidens"       => isset($bowler['maidens']) ? $bowler['maidens'] : 0,
                    "runs"          => isset($bowler['runs']) ? $bowler['runs'] : 0,
                    "wickets"       => isset($bowler['wickets']) ? $bowler['wickets'] : 0,
                ];
            } else {
                $current_bowler = $current_bowling->bowler ?? 0;
                $praticipants = $this->db->query("SELECT * FROM players WHERE player_participant_id = $current_bowler")->getRow();
                $bowlingData[] = [
                    "player_id"     => $praticipants->player_participant_id ?? 0,
                    "player_name"   => $praticipants->name ?? 0,
                    "overs"         => 0,
                    "maidens"       => 0,
                    "runs"          => 0,
                    "wickets"       => 0,
                ];
            }

            $overQuery = $this->db->query(
                "SELECT
                    overs_bowled AS ball
                    , runs_scored AS run
                    , wides
                    , no_balls
                    , byes
                    , leg_byes
                    , total_extras
                    , wickets
                    , hit_wickets
                    , catches
                    , stumpings
                    , lbws
                    , runouts
                    , non_striker_runouts
                FROM
                    scores_cricket
                WHERE
                    match_id = ?
                    AND overs = ? ",
                [$match_id, $match_cricket->bowling_overs ?? 0]
            );

            $overData = [
                "overs"  => $over,
                "over_balls" => []
            ];
            $ballData = $overQuery->getResult();
            $nb = 1;
            foreach ($ballData as $index => $ball) {
                $status = '';
                if ($ball->wides == 1) {
                    $status = 'w';
                } elseif ($ball->no_balls == 1) {
                    $status = 'nb' . $nb;
                    $nb++;
                } elseif ($ball->byes == 1) {
                    $status = 'b';
                } elseif ($ball->leg_byes == 1) {
                    $status = 'lb';
                } elseif ($ball->wickets == 1) {
                    $status = 'out';
                } elseif ($ball->hit_wickets == 1) {
                    $status = 'out';
                } elseif ($ball->catches == 1) {
                    $status = 'out';
                } elseif ($ball->stumpings == 1) {
                    $status = 'out';
                } elseif ($ball->lbws == 1) {
                    $status = 'out';
                } elseif ($ball->runouts == 1) {
                    $status = 'out';
                } elseif ($ball->non_striker_runouts == 1) {
                    $status = 'out';
                }
                $overData["over_balls"][] = [
                    "ball"   => $ball->ball,
                    "run"    => $ball->run + $ball->total_extras,
                    "status" => $status,
                ];
            }

            return [
                "top_team_data"     => $teamData,
                "toss_winner_text"  => "$toss_winner_team won the toss and selected $toss_winner_select first.",
                "batting_data"      => $battingData,
                "bowling_data"      => $bowlingData,
                "over_data"         => $overData
            ];
        } else {
            return ["message" => "Please select next batsman or bowler."];
        }
    }

    public function getOverallScoreData($match_id, $team_id)
    {
        $score_data = $this->db->query(
            "SELECT
                p.player_participant_id AS batsman_one,
                p.name,
                COALESCE(SUM(sc.runs_scored), 0) AS runs,
                COALESCE(COUNT(sc.balls_faced), 0) AS balls,
                COALESCE(SUM(CASE WHEN sc.runs_scored = 4 THEN 1 ELSE 0 END), 0) AS fours,
                COALESCE(SUM(CASE WHEN sc.runs_scored = 6 THEN 1 ELSE 0 END), 0) AS sixes
            FROM
                players AS p
            LEFT JOIN scores_cricket AS sc 
                ON sc.batsman_one = p.player_participant_id
                AND sc.match_id = ?
                AND sc.batting_team_id = ?
            WHERE
                p.player_match_id = ?
                AND p.team_id = ?
            GROUP BY
                p.player_participant_id, p.name",
            [$match_id, $team_id, $match_id, $team_id]
        )->getResult();

        return $score_data;
    }

    public function getOverallbowlingData1($match_id, $team_id)
    {
        $score_data = $this->db->query(
            "SELECT
                    sc.bowler
                    , p.name
                    , SUM(sc.balls_faced) AS overs
                    , SUM(sc.maidens) AS meidans
                    , SUM(sc.runs_scored) AS runs
                    , SUM(sc.wickets) AS wickets 
                    , SUM(sc.total_extras) AS total_extras 
                FROM
                    scores_cricket AS sc 
                    LEFT JOIN players AS p 
                        ON sc.bowler = p.player_participant_id 
                WHERE
                    sc.match_id = ? 
                    AND sc.bowling_team_id = ?
                GROUP BY
                    sc.bowler
                ",
            [$match_id, $team_id]
        )->getResult();

        return $score_data;
    }

    public function getOverallbowlingData($match_id, $team_id)
    {
        $score_data = $this->db->query(
            "SELECT
                p.player_participant_id AS bowler,
                p.name,
                FORMAT(COALESCE(SUM(sc.balls_faced)/6, 0), 1) AS overs,
                COALESCE(SUM(sc.maidens), 0) AS maidens,
                COALESCE(SUM(sc.runs_scored), 0) AS runs,
                COALESCE(SUM(sc.wickets), 0) AS wickets,
                COALESCE(SUM(sc.total_extras), 0) AS total_extras
            FROM
                players AS p
            LEFT JOIN scores_cricket AS sc 
                ON sc.bowler = p.player_participant_id 
                AND sc.match_id = ?
                AND sc.bowling_team_id = ?
            WHERE
                p.player_match_id = ?
                AND p.team_id = ?
            GROUP BY
                p.player_participant_id, p.name",
            [$match_id, $team_id, $match_id, $team_id]
        )->getResult();

        return $score_data;
    }

    public function getOverallExtraData($match_id, $team_id)
    {
        $extra_data = $this->db->query(
            "SELECT
                    SUM(sc.total_extras) AS extras
                    , SUM(sc.runs_scored + sc.total_extras) AS total_score
                    , SUM(sc.balls_faced) as balls_faced
                    , SUM(sc.wickets) AS wickets
                    , SUM(sc.maidens) AS maidens
                    , SUM(sc.wides) AS wides
                    , SUM(sc.no_balls) AS no_balls
                    , SUM(sc.byes) AS byes
                    , SUM(sc.leg_byes) AS leg_byes
                    , SUM(sc.penalty_runs) AS penalty_runs 
                FROM
                    scores_cricket AS sc 
                WHERE
                    sc.match_id = ? 
                    AND sc.bowling_team_id = ?
                ",
            [$match_id, $team_id]
        )->getResult();

        return $extra_data;
    }

    public function getOverallFallOfWicket($match_id, $team_id)
    {
        $fall_of_wicket = $this->db->query(
            "SELECT
                    p.name AS batsman_name, score.*
                FROM
                    ( 
                        SELECT
                            sc.batsman_one
                            , SUM(CASE WHEN sc.wickets = 1 THEN 1 ELSE 0 END) AS wickets
                            , SUM(sc.runs_scored) AS runs 
                        FROM
                            scores_cricket AS sc 
                        WHERE
                            sc.match_id = ? 
                            AND sc.batting_team_id = ? 
                        GROUP BY
                            sc.batsman_one 
                        ORDER BY
                            MIN(sc.created_at)
                    ) AS score 
                    LEFT JOIN players AS p ON p.player_participant_id = score.batsman_one
                WHERE
                    wickets = 1 
                GROUP BY
                    batsman_one",
            [$match_id, $team_id]
        )->getResult();

        return $fall_of_wicket;
    }

    public function teamWickets($match_id, $team_id)
    {
        $query = $this->db->table('players')
            ->select('SUM(CASE WHEN wicket = 1 THEN 1 ELSE 0 END) as total_wickets')
            ->select('COUNT(player_participant_id) as total_players')
            ->where('player_match_id', $match_id)
            ->where('team_id', $team_id)
            ->get()
            ->getRow();

        return $query;
    }

    /**
     * NOTE: as written, this only checks off-the-bat runs. Under the Laws of
     * Cricket, a maiden over is disqualified by wides and no-balls as well
     * (byes/leg-byes conventionally don't disqualify it). Left logic as-is
     * per original behaviour, but flagging: consider also checking
     * SUM(wides) = 0 AND SUM(no_balls) = 0 if you want strict rule accuracy.
     */
    public function checkThisMaiden($match_id, $over)
    {
        $query = $this->db->table('scores_cricket')
            ->select('SUM(runs_scored) AS runs, SUM(balls_faced) AS balls, SUM(wides) AS wides, SUM(no_balls) AS no_balls')
            ->where('match_id', $match_id)
            ->where('overs', $over)
            ->groupBy('overs')
            ->get()
            ->getRow();

        if (!$query) return false;

        if ($query->runs == 0 && $query->balls == 5 && $query->wides == 0 && $query->no_balls == 0) return true;

        return false;
    }

    public function inningsComplete($match_id)
    {
        $query = $this->db->query(
            "SELECT DISTINCT
                        batting_team_id 
                    FROM
                        scores_cricket 
                    WHERE
                        match_id = ?
                        ",
            [$match_id]
        )->getResult();

        return count($query) == 2;
    }
}