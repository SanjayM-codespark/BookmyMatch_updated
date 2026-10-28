<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MatchmakingFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Run silently — never block the actual request
        try {
            $this->_runPendingMatchmaking();
        } catch (\Throwable $e) {
            // Swallow all errors so a matchmaking bug never breaks the API
            log_message('error', 'MatchmakingFilter: ' . $e->getMessage());
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    private function _runPendingMatchmaking()
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $events = $db->query("
            SELECT listing_event_id, listing_event_title, listing_event_type,
                   listing_event_listing_id, listing_event_start
            FROM listing_events
            WHERE listing_event_active = 1
              AND listing_event_matchmaking_done = 0
              AND listing_event_matchmaking_date IS NOT NULL
              AND listing_event_matchmaking_date <= ?
            LIMIT 5
        ", [$now])->getResult();

        if (empty($events)) return;

        foreach ($events as $event) {
            // Immediately mark done to prevent duplicate runs from concurrent requests
            $updated = $db->table('listing_events')
                ->where('listing_event_id', $event->listing_event_id)
                ->where('listing_event_matchmaking_done', 0)
                ->update(['listing_event_matchmaking_done' => 1]);

            // If another request already claimed it, skip
            if ($db->affectedRows() === 0) continue;

            $teams = $db->table('teams')
                ->where('team_listing_event_id', $event->listing_event_id)
                ->whereIn('team_status', ['open', 'full'])
                ->orderBy('team_created_at', 'ASC')
                ->get()->getResultArray();

            if (count($teams) < 2) {
                log_message('info', 'MatchmakingFilter: Event ' . $event->listing_event_id . ' has less than 2 teams, skipped.');
                continue;
            }

            // Create Round 1
            $db->table('tbl_rounds')->insert([
                'round_name'       => 'Round 1',
                'round_group'      => 0,
                'round_tournament' => $event->listing_event_listing_id,
                'round_event'      => $event->listing_event_id,
                'round_status'     => 1,
                'round_created_at' => $now,
                'round_updated_at' => $now,
            ]);
            $round_id   = $db->insertID();
            $match_date = $event->listing_event_start ?? $now;

            $bye_team = null;
            if (count($teams) % 2 !== 0) {
                $bye_team = array_pop($teams);
            }

            foreach (array_chunk($teams, 2) as $pair) {
                $t1 = $pair[0];
                $t2 = $pair[1];

                $db->table('matches')->insert([
                    'match_type'           => $event->listing_event_type,
                    'match_round_id'       => $round_id,
                    'match_tournament'     => $event->listing_event_listing_id,
                    'match_event'          => $event->listing_event_id,
                    'match_team1_id'       => $t1['team_id'],
                    'match_team2_id'       => $t2['team_id'],
                    'match_individual1_id' => $t1['team_id'],
                    'match_individual2_id' => $t2['team_id'],
                    'match_date'           => $match_date,
                    'match_status'         => 1,
                    'match_created_at'     => $now,
                    'match_updated_at'     => $now,
                ]);

                $db->table('teams')
                    ->whereIn('team_id', [$t1['team_id'], $t2['team_id']])
                    ->update(['team_status' => 'locked']);

                $this->_notify($db, $t1, $t2, $match_date);
            }

            log_message('info', 'MatchmakingFilter: Matchmaking done for event ' . $event->listing_event_id . ' (' . $event->listing_event_title . ')');
        }
    }

    private function _notify($db, $team1, $team2, $match_date)
    {
        $date_str = date('d M Y h:i A', strtotime($match_date));

        foreach ([$team1, $team2] as $my_team) {
            $opp      = ($my_team['team_id'] === $team1['team_id']) ? $team2 : $team1;
            $msg_desc = 'Your team "' . $my_team['team_name'] . '" has been matched against "' . $opp['team_name'] . '" on ' . $date_str . '. Get ready!';

            $db->table('messages')->insert([
                'msg_title'      => 'Match Scheduled!',
                'msg_desc'       => $msg_desc,
                'msg_image'      => null,
                'msg_active'     => 1,
                'msg_created_at' => date('Y-m-d H:i:s'),
                'msg_updated_at' => date('Y-m-d H:i:s'),
            ]);
            $msg_id = $db->insertID();

            $members = $db->table('team_members')
                ->where('tm_team_id', $my_team['team_id'])
                ->where('tm_status', 'accepted')
                ->where('tm_user_id IS NOT NULL', null, false)
                ->get()->getResult();

            foreach ($members as $m) {
                $db->table('notifications')->insert([
                    'notify_msg_id' => $msg_id,
                    'notify_for'    => $m->tm_user_id,
                    'notify_from'   => 0,
                    'notify_count'  => 0,
                    'notify_active' => 1,
                    'notify_time'   => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
