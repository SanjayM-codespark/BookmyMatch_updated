<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RunMatchmaking extends BaseCommand
{
    protected $group       = 'BookMyMatch';
    protected $name        = 'matchmaking:run';
    protected $description = 'Auto-pairs players/teams for all events whose matchmaking date has arrived';

    public function run(array $params)
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $events = $db->query("
            SELECT le.listing_event_id, le.listing_event_title, le.listing_event_type,
                   le.listing_event_listing_id, le.listing_event_start, le.listing_event_matchmaking_date,
                   COALESCE(l.listing_sports_id, 0) AS listing_sports_id
            FROM listing_events le
            LEFT JOIN listings l ON l.listing_id = le.listing_event_listing_id
            WHERE le.listing_event_active = 1
              AND le.listing_event_matchmaking_done = 0
              AND le.listing_event_matchmaking_date IS NOT NULL
              AND le.listing_event_matchmaking_date <= ?
        ", [$now])->getResult();

        if (empty($events)) {
            CLI::write('[' . $now . '] No events ready for matchmaking.', 'yellow');
            return;
        }

        foreach ($events as $event) {
            CLI::write('Processing: event #' . $event->listing_event_id . ' (type: ' . $event->listing_event_type . ')', 'cyan');

            $type = $event->listing_event_type; // team | doubles | individual

            if ($type === 'individual') {
                $this->_processIndividual($db, $event, $now);
            } else {
                // team + doubles both use the teams table
                $this->_processTeam($db, $event, $now);
            }

            $db->table('listing_events')
                ->where('listing_event_id', $event->listing_event_id)
                ->update(['listing_event_matchmaking_done' => 1]);
        }

        CLI::write('Matchmaking complete.', 'green');
    }

    // -----------------------------------------------------------------------
    // TEAM / DOUBLES
    // -----------------------------------------------------------------------
    private function _processTeam($db, $event, $now)
    {
        $teams = $db->table('teams')
            ->where('team_listing_event_id', $event->listing_event_id)
            ->whereIn('team_status', ['open', 'full'])
            ->orderBy('team_created_at', 'ASC')
            ->get()->getResultArray();

        $total = count($teams);

        if ($total < 2) {
            CLI::write('  Skipped — only ' . $total . ' team(s) registered.', 'red');
            return;
        }

        $round_id   = $this->_createRound($db, $event, 'Round 1', $now);
        $match_date = $event->listing_event_start ?? $now;

        $bye_team = null;
        if ($total % 2 !== 0) {
            $bye_team = array_pop($teams);
        }

        $count = 0;
        foreach (array_chunk($teams, 2) as $pair) {
            $t1 = $pair[0];
            $t2 = $pair[1];

            $db->table('matches')->insert([
                'match_type'           => $event->listing_event_type,
                'match_round_id'       => $round_id,
                'match_tournament'     => $event->listing_event_listing_id,
                'match_event'          => $event->listing_event_id,
                'match_sport_id'       => (int)($event->listing_sports_id ?? 0),
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

            $this->_notifyTeam($db, $t1, $t2, $match_date);
            CLI::write('  Match: ' . $t1['team_name'] . ' vs ' . $t2['team_name'], 'green');
            $count++;
        }

        if ($bye_team) {
            CLI::write('  Bye: ' . $bye_team['team_name'] . ' advances directly to Round 2', 'yellow');
        }

        CLI::write('  Done — ' . $count . ' match(es) created.', 'green');
    }

    // -----------------------------------------------------------------------
    // INDIVIDUAL / SINGLES
    // -----------------------------------------------------------------------
    private function _processIndividual($db, $event, $now)
    {
        $participants = $db->table('participants')
            ->where('participant_event_id', $event->listing_event_id)
            ->where('participant_list_id', $event->listing_event_listing_id)
            ->orderBy('participant_id', 'ASC')
            ->get()->getResultArray();

        $total = count($participants);

        if ($total < 2) {
            CLI::write('  Skipped — only ' . $total . ' participant(s) registered.', 'red');
            return;
        }

        $round_id   = $this->_createRound($db, $event, 'Round 1', $now);
        $match_date = $event->listing_event_start ?? $now;

        $bye = null;
        if ($total % 2 !== 0) {
            $bye = array_pop($participants);
        }

        $count = 0;
        foreach (array_chunk($participants, 2) as $pair) {
            $p1 = $pair[0];
            $p2 = $pair[1];

            $db->table('matches')->insert([
                'match_type'           => 'individual',
                'match_round_id'       => $round_id,
                'match_tournament'     => $event->listing_event_listing_id,
                'match_event'          => $event->listing_event_id,
                'match_sport_id'       => (int)($event->listing_sports_id ?? 0),
                'match_team1_id'       => 0,
                'match_team2_id'       => 0,
                'match_individual1_id' => $p1['participant_id'],
                'match_individual2_id' => $p2['participant_id'],
                'match_date'           => $match_date,
                'match_status'         => 1,
                'match_created_at'     => $now,
                'match_updated_at'     => $now,
            ]);

            $this->_notifyIndividual($db, $p1, $p2, $match_date);
            CLI::write('  Match: ' . $p1['participant_name'] . ' vs ' . $p2['participant_name'], 'green');
            $count++;
        }

        if ($bye) {
            CLI::write('  Bye: ' . $bye['participant_name'] . ' advances directly to Round 2', 'yellow');
        }

        CLI::write('  Done — ' . $count . ' match(es) created.', 'green');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------
    private function _createRound($db, $event, $round_name, $now): int
    {
        $db->table('tbl_rounds')->insert([
            'round_name'       => $round_name,
            'round_group'      => 0,
            'round_tournament' => $event->listing_event_listing_id,
            'round_event'      => $event->listing_event_id,
            'round_status'     => 1,
            'round_created_at' => $now,
            'round_updated_at' => $now,
        ]);
        return $db->insertID();
    }

    private function _notifyTeam($db, array $team1, array $team2, string $match_date): void
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

    private function _notifyIndividual($db, array $p1, array $p2, string $match_date): void
    {
        $date_str = date('d M Y h:i A', strtotime($match_date));

        foreach ([$p1, $p2] as $me) {
            $opp      = ($me['participant_id'] === $p1['participant_id']) ? $p2 : $p1;
            $msg_desc = '"' . $me['participant_name'] . '" has been matched against "' . $opp['participant_name'] . '" on ' . $date_str . '. Get ready!';

            $db->table('messages')->insert([
                'msg_title'      => 'Match Scheduled!',
                'msg_desc'       => $msg_desc,
                'msg_image'      => null,
                'msg_active'     => 1,
                'msg_created_at' => date('Y-m-d H:i:s'),
                'msg_updated_at' => date('Y-m-d H:i:s'),
            ]);
            $msg_id = $db->insertID();

            // notify the user linked to this participant (via booking)
            $booking = $db->table('booking')
                ->where('book_event_id', $me['participant_event_id'])
                ->where('book_tournament_id', $me['participant_list_id'])
                ->get()->getRow();

            if ($booking && $booking->book_user_id) {
                $db->table('notifications')->insert([
                    'notify_msg_id' => $msg_id,
                    'notify_for'    => $booking->book_user_id,
                    'notify_from'   => 0,
                    'notify_count'  => 0,
                    'notify_active' => 1,
                    'notify_time'   => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
