<?php

namespace App\API\r1;

use CodeIgniter\RESTful\ResourceController;
use App\Traits\ReqresTrait;
use App\Models\CommonModel;

class Team extends ResourceController
{
    use ReqresTrait;

    protected $common;
    protected $db;

    public function __construct()
    {
        helper(['form', 'url', 'custom']);
        $this->common = new CommonModel();
        $this->db     = \Config\Database::connect();
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE: check active membership
    // ─────────────────────────────────────────────────────────────
    private function _hasActiveMembership($user_id)
    {
        return $this->db->table('memberships')
            ->where('membership_user_id', $user_id)
            ->where('membership_status', 'active')
            ->countAllResults() > 0;
    }

    // The tournament's Last Booking Date overrides the event's own value,
    // so editing the tournament re-opens/closes registration for its events.
    private function _effectiveLastbook($event)
    {
        if (empty($event)) {
            return $event;
        }

        $listing = $this->db->table('listings')
            ->select('listing_lastbook_date')
            ->where('listing_id', $event->listing_event_listing_id)
            ->get()->getRow();

        if (!empty($listing) && !empty($listing->listing_lastbook_date)) {
            $event->listing_event_lastbook_date = $listing->listing_lastbook_date;
        }

        return $event;
    }

    // ─────────────────────────────────────────────────────────────
    // GET api/event_teams/{listing_event_id}
    // ─────────────────────────────────────────────────────────────
    public function event_teams($listing_event_id)
    {
        $event = $this->db->table('listing_events')
            ->where('listing_event_id', $listing_event_id)
            ->where('listing_event_active', 1)
            ->get()->getRow();

        if (!$event) {
            return $this->getResponse(['status' => false, 'message' => 'Event not found']);
        }

        $event = $this->_effectiveLastbook($event);

        $teams = $this->db->table('teams')
            ->where('team_listing_event_id', $listing_event_id)
            ->orderBy('team_created_at', 'ASC')
            ->get()->getResult();

        foreach ($teams as $team) {
            $members = $this->db->query("
                SELECT tm.*, u.user_profile_img
                FROM team_members tm
                LEFT JOIN users u ON u.user_id = tm.tm_user_id
                WHERE tm.tm_team_id = ? AND tm.tm_status = 'accepted'
            ", [$team->team_id])->getResult();
            $team->members      = $members;
            $team->member_count = count($members);
            $team->slots_left   = max(0, $team->team_max_players - count($members));
            $team->invite_link  = base_url('api/team_by_token/' . $team->team_join_token);
        }

        return $this->getResponse([
            'status'      => true,
            'message'     => 'Success',
            'event_type'  => $event->listing_event_type,
            'max_players' => $event->listing_event_max_players,
            'max_teams'   => $event->listing_event_max_teams,
            'deadline'    => $event->listing_event_lastbook_date,
            'data'        => $teams ?? [],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST api/create_team
    // Body: user_id, listing_event_id, team_name
    // Creator must have an active membership
    // ─────────────────────────────────────────────────────────────
    public function create_team()
    {
        $rules = [
            'user_id'          => ['label' => 'User ID',          'rules' => 'required|numeric'],
            'listing_event_id' => ['label' => 'Listing Event ID', 'rules' => 'required|numeric'],
            'team_name'        => ['label' => 'Team Name',        'rules' => 'required|min_length[2]'],
        ];

        if (!$this->validate($rules)) {
            return $this->getResponse(['status' => false, 'message' => 'Validation error', 'data' => $this->validator->getErrors()]);
        }

        $input            = $this->getRequestInput($this->request);
        $user_id          = (int) $input['user_id'];
        $listing_event_id = (int) $input['listing_event_id'];
        $team_name        = xss_clean($input['team_name']);

        // Must be a registered user with an active membership
        $user = $this->db->table('users')->where('user_id', $user_id)->get()->getRow();
        if (!$user) {
            return $this->getResponse(['status' => false, 'message' => 'User account not found']);
        }

        if (!$this->_hasActiveMembership($user_id)) {
            return $this->getResponse(['status' => false, 'message' => 'An active membership is required to create a team']);
        }

        $event = $this->db->table('listing_events')
            ->where('listing_event_id', $listing_event_id)
            ->where('listing_event_active', 1)
            ->get()->getRow();

        if (!$event) {
            return $this->getResponse(['status' => false, 'message' => 'Event not found']);
        }

        $event = $this->_effectiveLastbook($event);

        if ($event->listing_event_type !== 'team') {
            return $this->getResponse(['status' => false, 'message' => 'This event is not a team event']);
        }

        if (!empty($event->listing_event_lastbook_date) && strtotime($event->listing_event_lastbook_date) < time()) {
            return $this->getResponse(['status' => false, 'message' => 'Team registration deadline has passed']);
        }

        if (!empty($event->listing_event_max_teams)) {
            $existing = $this->db->table('teams')
                ->where('team_listing_event_id', $listing_event_id)
                ->countAllResults();
            if ($existing >= $event->listing_event_max_teams) {
                return $this->getResponse(['status' => false, 'message' => 'Maximum number of teams reached for this event']);
            }
        }

        $duplicate = $this->db->table('teams')
            ->where('team_listing_event_id', $listing_event_id)
            ->where('team_name', $team_name)
            ->get()->getRow();
        if ($duplicate) {
            return $this->getResponse(['status' => false, 'message' => 'A team with this name already exists in this event']);
        }

        // User already in a team for this event?
        $alreadyIn = $this->db->query("
            SELECT tm.tm_id FROM team_members tm
            JOIN teams t ON t.team_id = tm.tm_team_id
            WHERE tm.tm_user_id = ? AND t.team_listing_event_id = ? AND tm.tm_status = 'accepted'
        ", [$user_id, $listing_event_id])->getRow();
        if ($alreadyIn) {
            return $this->getResponse(['status' => false, 'message' => 'You are already in a team for this event']);
        }

        $token = bin2hex(random_bytes(16));
        $date  = date('Y-m-d H:i:s');

        $team_id = $this->common->insert_data('teams', [
            'team_name'             => $team_name,
            'team_leader_id'        => $user_id,
            'team_listing_event_id' => $listing_event_id,
            'team_tournament'       => $event->listing_event_listing_id,
            'team_event'            => $listing_event_id,
            'team_join_token'       => $token,
            'team_max_players'      => $event->listing_event_max_players,
            'team_status'           => 'open',
            'team_created_at'       => $date,
        ]);

        if (!$team_id) {
            return $this->getResponse(['status' => false, 'message' => 'Failed to create team']);
        }

        $this->db->table('team_members')->insert([
            'tm_team_id'    => $team_id,
            'tm_user_id'    => $user_id,
            'tm_name'       => $user->user_full_name ?? $user->user_mobile ?? ('User ' . $user_id),
            'tm_mobile'     => $user->user_mobile,
            'tm_email'      => $user->user_email,
            'tm_status'     => 'accepted',
            'tm_added_by'   => 'leader',
            'tm_created_at' => $date,
        ]);


        return $this->getResponse([
            'status'  => true,
            'message' => 'Team created successfully',
            'data'    => [
                'team_id'     => $team_id,
                'team_name'   => $team_name,
                'invite_link' => base_url('api/team_by_token/' . $token),
                'token'       => $token,
            ]
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET api/team_by_token/{token}
    // ─────────────────────────────────────────────────────────────
    public function team_by_token($token)
    {
        $team = $this->db->table('teams')->where('team_join_token', $token)->get()->getRow();

        if (!$team) {
            return $this->getResponse(['status' => false, 'message' => 'Invalid or expired invite link']);
        }

        $members = $this->db->query("
            SELECT tm.*, u.user_profile_img
            FROM team_members tm
            LEFT JOIN users u ON u.user_id = tm.tm_user_id
            WHERE tm.tm_team_id = ? AND tm.tm_status = 'accepted'
        ", [$team->team_id])->getResult();

        $event = $this->db->table('listing_events')
            ->where('listing_event_id', $team->team_listing_event_id)
            ->get()->getRow();

        $event = $this->_effectiveLastbook($event);

        return $this->getResponse([
            'status'  => true,
            'message' => 'Success',
            'data'    => [
                'team_id'      => $team->team_id,
                'team_name'    => $team->team_name,
                'team_status'  => $team->team_status,
                'max_players'  => $team->team_max_players,
                'member_count' => count($members),
                'slots_left'   => max(0, $team->team_max_players - count($members)),
                'members'      => $members,
                'event'        => $event,
                'deadline'     => $event ? $event->listing_event_lastbook_date : null,
            ]
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET api/team_details/{team_id}
    // ─────────────────────────────────────────────────────────────
    public function team_details($team_id)
    {
        $team = $this->db->table('teams')->where('team_id', $team_id)->get()->getRow();

        if (!$team) {
            return $this->getResponse(['status' => false, 'message' => 'Team not found']);
        }

        $members = $this->db->query("
            SELECT tm.*, u.user_profile_img
            FROM team_members tm
            LEFT JOIN users u ON u.user_id = tm.tm_user_id
            WHERE tm.tm_team_id = ?
            ORDER BY tm.tm_created_at ASC
        ", [$team_id])->getResult();

        $event = $this->db->table('listing_events')
            ->where('listing_event_id', $team->team_listing_event_id)
            ->get()->getRow();

        $accepted       = array_filter((array)$members, fn($m) => $m->tm_status === 'accepted');
        $team->members      = $members;
        $team->member_count = count($accepted);
        $team->slots_left   = max(0, $team->team_max_players - count($accepted));
        $team->invite_link  = base_url('api/team_by_token/' . $team->team_join_token);
        $team->event        = $event;

        return $this->getResponse(['status' => true, 'message' => 'Success', 'data' => $team]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST api/join_team
    // Join via invite token — registered account + active membership required
    // Body: user_id, token
    // ─────────────────────────────────────────────────────────────
    public function join_team()
    {
        $rules = [
            'user_id' => ['label' => 'User ID', 'rules' => 'required|numeric'],
            'token'   => ['label' => 'Token',   'rules' => 'required|min_length[10]'],
        ];

        if (!$this->validate($rules)) {
            return $this->getResponse(['status' => false, 'message' => 'Validation error', 'data' => $this->validator->getErrors()]);
        }

        $input   = $this->getRequestInput($this->request);
        $user_id = (int) $input['user_id'];
        $token   = xss_clean($input['token']);

        $user = $this->db->table('users')->where('user_id', $user_id)->get()->getRow();
        if (!$user) {
            return $this->getResponse(['status' => false, 'message' => 'User account not found']);
        }

        if (!$this->_hasActiveMembership($user_id)) {
            return $this->getResponse(['status' => false, 'message' => 'An active membership is required to join a team']);
        }

        $team = $this->db->table('teams')->where('team_join_token', $token)->get()->getRow();
        if (!$team) {
            return $this->getResponse(['status' => false, 'message' => 'Invalid invite link']);
        }

        if ($team->team_status === 'full' || $team->team_status === 'locked') {
            return $this->getResponse(['status' => false, 'message' => 'This team is already full']);
        }

        $event = $this->db->table('listing_events')
            ->where('listing_event_id', $team->team_listing_event_id)
            ->get()->getRow();

        $event = $this->_effectiveLastbook($event);

        if ($event && !empty($event->listing_event_lastbook_date) && strtotime($event->listing_event_lastbook_date) < time()) {
            return $this->getResponse(['status' => false, 'message' => 'Registration deadline has passed']);
        }

        // Already in this team?
        $exists = $this->db->table('team_members')
            ->where('tm_team_id', $team->team_id)
            ->where('tm_user_id', $user_id)
            ->get()->getRow();
        if ($exists) {
            return $this->getResponse(['status' => false, 'message' => 'You are already in this team']);
        }

        // Already in another team for this event?
        $alreadyIn = $this->db->query("
            SELECT tm.tm_id FROM team_members tm
            JOIN teams t ON t.team_id = tm.tm_team_id
            WHERE tm.tm_user_id = ? AND t.team_listing_event_id = ? AND tm.tm_status = 'accepted'
        ", [$user_id, $team->team_listing_event_id])->getRow();
        if ($alreadyIn) {
            return $this->getResponse(['status' => false, 'message' => 'You are already in another team for this event']);
        }

        $member_count = $this->db->table('team_members')
            ->where('tm_team_id', $team->team_id)
            ->where('tm_status', 'accepted')
            ->countAllResults();

        $max_players = (!empty($event->listing_event_max_players)) ? (int)$event->listing_event_max_players : (int)$team->team_max_players;
        if ($max_players > 0 && $member_count >= $max_players) {
            return $this->getResponse(['status' => false, 'message' => 'Team is full']);
        }

        $this->db->table('team_members')->insert([
            'tm_team_id'    => $team->team_id,
            'tm_user_id'    => $user_id,
            'tm_name'       => $user->user_full_name ?? $user->user_mobile ?? ('User ' . $user_id),
            'tm_mobile'     => $user->user_mobile,
            'tm_email'      => $user->user_email,
            'tm_status'     => 'accepted',
            'tm_added_by'   => 'self',
            'tm_created_at' => date('Y-m-d H:i:s'),
        ]);

        // Mark team full if capacity reached
        if ($max_players > 0 && ($member_count + 1) >= $max_players) {
            $this->db->table('teams')->where('team_id', $team->team_id)->update(['team_status' => 'full']);
        }

        return $this->getResponse([
            'status'  => true,
            'message' => 'Successfully joined the team',
            'data'    => ['team_id' => $team->team_id, 'team_name' => $team->team_name]
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST api/add_team_member
    // Leader adds a registered user to the team directly
    // Body: leader_id, team_id, member_user_id
    // ─────────────────────────────────────────────────────────────
    public function add_team_member()
    {
        $rules = [
            'leader_id'      => ['label' => 'Leader ID',        'rules' => 'required|numeric'],
            'team_id'        => ['label' => 'Team ID',          'rules' => 'required|numeric'],
            'member_user_id' => ['label' => 'Member User ID',   'rules' => 'required|numeric'],
        ];

        if (!$this->validate($rules)) {
            return $this->getResponse(['status' => false, 'message' => 'Validation error', 'data' => $this->validator->getErrors()]);
        }

        $input          = $this->getRequestInput($this->request);
        $leader_id      = (int) $input['leader_id'];
        $team_id        = (int) $input['team_id'];
        $member_user_id = (int) $input['member_user_id'];

        $team = $this->db->table('teams')->where('team_id', $team_id)->get()->getRow();
        if (!$team) {
            return $this->getResponse(['status' => false, 'message' => 'Team not found']);
        }

        if ((int) $team->team_leader_id !== $leader_id) {
            return $this->getResponse(['status' => false, 'message' => 'Only the team leader can add members']);
        }

        if ($team->team_status === 'full' || $team->team_status === 'locked') {
            return $this->getResponse(['status' => false, 'message' => 'Team is already full or locked']);
        }

        // Verify the member has an account
        $member = $this->db->table('users')->where('user_id', $member_user_id)->get()->getRow();
        if (!$member) {
            return $this->getResponse(['status' => false, 'message' => 'User not found. Only registered account holders can be added']);
        }

        if (!$this->_hasActiveMembership($member_user_id)) {
            return $this->getResponse(['status' => false, 'message' => 'That user does not have an active membership']);
        }

        // Already in this team?
        $exists = $this->db->table('team_members')
            ->where('tm_team_id', $team_id)
            ->where('tm_user_id', $member_user_id)
            ->get()->getRow();
        if ($exists) {
            return $this->getResponse(['status' => false, 'message' => 'This user is already in the team']);
        }

        // Already in another team for this event?
        $alreadyIn = $this->db->query("
            SELECT tm.tm_id FROM team_members tm
            JOIN teams t ON t.team_id = tm.tm_team_id
            WHERE tm.tm_user_id = ? AND t.team_listing_event_id = ? AND tm.tm_status = 'accepted'
        ", [$member_user_id, $team->team_listing_event_id])->getRow();
        if ($alreadyIn) {
            return $this->getResponse(['status' => false, 'message' => 'This user is already in another team for this event']);
        }

        $event_for_team = $this->db->table('listing_events')
            ->where('listing_event_id', $team->team_listing_event_id)
            ->get()->getRow();

        $member_count = $this->db->table('team_members')
            ->where('tm_team_id', $team_id)
            ->where('tm_status', 'accepted')
            ->countAllResults();

        $max_players = (!empty($event_for_team->listing_event_max_players)) ? (int)$event_for_team->listing_event_max_players : (int)$team->team_max_players;
        if ($max_players > 0 && $member_count >= $max_players) {
            return $this->getResponse(['status' => false, 'message' => 'Team is already full']);
        }

        $this->db->table('team_members')->insert([
            'tm_team_id'    => $team_id,
            'tm_user_id'    => $member_user_id,
            'tm_name'       => $member->user_full_name ?? $member->user_mobile ?? ('User ' . $member_user_id),
            'tm_mobile'     => $member->user_mobile,
            'tm_email'      => $member->user_email,
            'tm_status'     => 'accepted',
            'tm_added_by'   => 'leader',
            'tm_created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($max_players > 0 && ($member_count + 1) >= $max_players) {
            $this->db->table('teams')->where('team_id', $team_id)->update(['team_status' => 'full']);
        }

        return $this->getResponse(['status' => true, 'message' => 'Member added successfully']);
    }

    // ─────────────────────────────────────────────────────────────
    // POST api/request_join_team
    // Registered user with membership sends a join request (pending leader approval)
    // Body: user_id, team_id
    // ─────────────────────────────────────────────────────────────
    public function request_join_team()
    {
        $rules = [
            'user_id' => ['label' => 'User ID', 'rules' => 'required|numeric'],
            'team_id' => ['label' => 'Team ID', 'rules' => 'required|numeric'],
        ];

        if (!$this->validate($rules)) {
            return $this->getResponse(['status' => false, 'message' => 'Validation error', 'data' => $this->validator->getErrors()]);
        }

        $input   = $this->getRequestInput($this->request);
        $user_id = (int) $input['user_id'];
        $team_id = (int) $input['team_id'];

        $user = $this->db->table('users')->where('user_id', $user_id)->get()->getRow();
        if (!$user) {
            return $this->getResponse(['status' => false, 'message' => 'User account not found']);
        }

        if (!$this->_hasActiveMembership($user_id)) {
            return $this->getResponse(['status' => false, 'message' => 'An active membership is required to join a team']);
        }

        $team = $this->db->table('teams')->where('team_id', $team_id)->get()->getRow();
        if (!$team) {
            return $this->getResponse(['status' => false, 'message' => 'Team not found']);
        }

        if ($team->team_status === 'full' || $team->team_status === 'locked') {
            return $this->getResponse(['status' => false, 'message' => 'Team is full or locked']);
        }

        $existing = $this->db->table('team_members')
            ->where('tm_team_id', $team_id)
            ->where('tm_user_id', $user_id)
            ->get()->getRow();
        if ($existing) {
            return $this->getResponse(['status' => false, 'message' => 'You already have a request or are already in this team']);
        }

        $this->db->table('team_members')->insert([
            'tm_team_id'    => $team_id,
            'tm_user_id'    => $user_id,
            'tm_name'       => $user->user_full_name ?? $user->user_mobile ?? ('User ' . $user_id),
            'tm_mobile'     => $user->user_mobile,
            'tm_email'      => $user->user_email,
            'tm_status'     => 'pending',
            'tm_added_by'   => 'request',
            'tm_created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->getResponse(['status' => true, 'message' => 'Join request sent. Waiting for leader approval']);
    }

    // ─────────────────────────────────────────────────────────────
    // POST api/approve_team_member
    // Leader approves or rejects a pending request
    // Body: leader_id, team_id, tm_id, action (accept|reject)
    // ─────────────────────────────────────────────────────────────
    public function approve_team_member()
    {
        $rules = [
            'leader_id' => ['label' => 'Leader ID', 'rules' => 'required|numeric'],
            'team_id'   => ['label' => 'Team ID',   'rules' => 'required|numeric'],
            'tm_id'     => ['label' => 'Member ID', 'rules' => 'required|numeric'],
            'action'    => ['label' => 'Action',    'rules' => 'required|in_list[accept,reject]'],
        ];

        if (!$this->validate($rules)) {
            return $this->getResponse(['status' => false, 'message' => 'Validation error', 'data' => $this->validator->getErrors()]);
        }

        $input     = $this->getRequestInput($this->request);
        $leader_id = (int) $input['leader_id'];
        $team_id   = (int) $input['team_id'];
        $tm_id     = (int) $input['tm_id'];
        $action    = $input['action'];

        $team = $this->db->table('teams')->where('team_id', $team_id)->get()->getRow();

        if (!$team || (int) $team->team_leader_id !== $leader_id) {
            return $this->getResponse(['status' => false, 'message' => 'Only the team leader can approve requests']);
        }

        if ($action === 'accept') {
            $event_for_approve = $this->db->table('listing_events')
                ->where('listing_event_id', $team->team_listing_event_id)
                ->get()->getRow();

            $member_count = $this->db->table('team_members')
                ->where('tm_team_id', $team_id)
                ->where('tm_status', 'accepted')
                ->countAllResults();

            $max_players = (!empty($event_for_approve->listing_event_max_players)) ? (int)$event_for_approve->listing_event_max_players : (int)$team->team_max_players;
            if ($max_players > 0 && $member_count >= $max_players) {
                return $this->getResponse(['status' => false, 'message' => 'Team is already full']);
            }

            $this->db->table('team_members')
                ->where('tm_id', $tm_id)
                ->where('tm_team_id', $team_id)
                ->update(['tm_status' => 'accepted']);

            if ($max_players > 0 && ($member_count + 1) >= $max_players) {
                $this->db->table('teams')->where('team_id', $team_id)->update(['team_status' => 'full']);
            }

            return $this->getResponse(['status' => true, 'message' => 'Member accepted']);
        }

        $this->db->table('team_members')
            ->where('tm_id', $tm_id)
            ->where('tm_team_id', $team_id)
            ->update(['tm_status' => 'rejected']);

        return $this->getResponse(['status' => true, 'message' => 'Member request rejected']);
    }

    // ─────────────────────────────────────────────────────────────
    // GET api/pending_requests/{team_id}/{leader_id}
    // ─────────────────────────────────────────────────────────────
    public function pending_requests($team_id, $leader_id)
    {
        $team = $this->db->table('teams')->where('team_id', $team_id)->get()->getRow();

        if (!$team || (int) $team->team_leader_id !== (int) $leader_id) {
            return $this->getResponse(['status' => false, 'message' => 'Not authorized']);
        }

        $requests = $this->db->query("
            SELECT tm.*, u.user_profile_img
            FROM team_members tm
            LEFT JOIN users u ON u.user_id = tm.tm_user_id
            WHERE tm.tm_team_id = ? AND tm.tm_status = 'pending'
        ", [$team_id])->getResult();

        return $this->getResponse(['status' => true, 'message' => 'Success', 'data' => $requests ?? []]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET api/search_users?query=name_or_mobile&exclude_team_id=:num
    // Search registered account holders to invite to a team
    // Only returns users who have an active membership
    // ─────────────────────────────────────────────────────────────
    public function search_users()
    {
        $query         = trim($this->request->getGet('query') ?? '');
        $exclude_team  = (int) ($this->request->getGet('exclude_team_id') ?? 0);

        if (strlen($query) < 2) {
            return $this->getResponse(['status' => false, 'message' => 'Enter at least 2 characters to search']);
        }

        $builder = $this->db->table('users u')
            ->select('u.user_id, u.user_full_name, u.user_mobile, u.user_email, u.user_profile_img')
            ->join('memberships m', 'm.membership_user_id = u.user_id AND m.membership_status = \'active\'', 'inner')
            ->where('u.user_active', 1)
            ->groupStart()
                ->like('u.user_full_name', $query)
                ->orLike('u.user_mobile', $query)
            ->groupEnd()
            ->limit(20);

        // Exclude users already in the given team
        if ($exclude_team > 0) {
            $already = $this->db->table('team_members')
                ->select('tm_user_id')
                ->where('tm_team_id', $exclude_team)
                ->whereIn('tm_status', ['accepted', 'pending'])
                ->get()->getResultArray();
            $ids = array_column($already, 'tm_user_id');
            if (!empty($ids)) {
                $builder->whereNotIn('u.user_id', $ids);
            }
        }

        $users = $builder->get()->getResult();

        return $this->getResponse([
            'status'  => true,
            'message' => 'Success',
            'data'    => $users ?? [],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST api/run_matchmaking
    // Organizer triggers this on/after listing_event_matchmaking_date
    // Body: listing_event_id, organizer_id
    // Creates Round 1 pairings for all registered teams + notifies members
    // ─────────────────────────────────────────────────────────────
    public function run_matchmaking()
    {
        $rules = [
            'listing_event_id' => ['label' => 'Event ID',      'rules' => 'required|numeric'],
            'organizer_id'     => ['label' => 'Organizer ID',  'rules' => 'required|numeric'],
        ];

        if (!$this->validate($rules)) {
            return $this->getResponse(['status' => false, 'message' => 'Validation error', 'data' => $this->validator->getErrors()]);
        }

        $input            = $this->getRequestInput($this->request);
        $listing_event_id = (int) $input['listing_event_id'];
        $organizer_id     = (int) $input['organizer_id'];

        $event = $this->db->table('listing_events')
            ->where('listing_event_id', $listing_event_id)
            ->get()->getRow();

        if (!$event) {
            return $this->getResponse(['status' => false, 'message' => 'Event not found']);
        }

        if (!empty($event->listing_event_matchmaking_done) && $event->listing_event_matchmaking_done == 1) {
            return $this->getResponse(['status' => false, 'message' => 'Matchmaking has already been run for this event']);
        }

        // Enforce matchmaking date — must be reached before running
        if (!empty($event->listing_event_matchmaking_date) && strtotime($event->listing_event_matchmaking_date) > time()) {
            return $this->getResponse([
                'status'  => false,
                'message' => 'Matchmaking date not reached yet. Scheduled for: ' . date('d M Y h:i A', strtotime($event->listing_event_matchmaking_date)),
            ]);
        }

        // Get all registered teams for this event ordered by registration time
        $teams = $this->db->table('teams')
            ->where('team_listing_event_id', $listing_event_id)
            ->whereIn('team_status', ['open', 'full'])
            ->orderBy('team_created_at', 'ASC')
            ->get()->getResultArray();

        $total = count($teams);

        if ($total < 2) {
            return $this->getResponse(['status' => false, 'message' => 'Need at least 2 registered teams to run matchmaking']);
        }

        // Create Round 1 record
        $this->db->table('tbl_rounds')->insert([
            'round_name'       => 'Round 1',
            'round_group'      => 0,
            'round_tournament' => $event->listing_event_listing_id,
            'round_event'      => $listing_event_id,
            'round_status'     => 1,
            'round_created_at' => date('Y-m-d H:i:s'),
            'round_updated_at' => date('Y-m-d H:i:s'),
        ]);
        $round_id = $this->db->insertID();

        $matches_created = [];
        $bye_team        = null;
        $match_date      = $event->listing_event_start ?? date('Y-m-d H:i:s');

        // Pair sequentially: [0] vs [1], [2] vs [3], ...
        // If odd count, last team gets a bye (no match in Round 1)
        if ($total % 2 !== 0) {
            $bye_team = array_pop($teams); // remove last team — gets bye
        }

        foreach (array_chunk($teams, 2) as $pair) {
            $t1 = $pair[0];
            $t2 = $pair[1];

            $this->db->table('matches')->insert([
                'match_type'           => 'team',
                'match_round_id'       => $round_id,
                'match_tournament'     => $event->listing_event_listing_id,
                'match_event'          => $listing_event_id,
                'match_team1_id'       => $t1['team_id'],
                'match_team2_id'       => $t2['team_id'],
                'match_individual1_id' => $t1['team_id'],
                'match_individual2_id' => $t2['team_id'],
                'match_date'           => $match_date,
                'match_status'         => 1,
                'match_created_at'     => date('Y-m-d H:i:s'),
                'match_updated_at'     => date('Y-m-d H:i:s'),
            ]);
            $match_id = $this->db->insertID();

            // Lock both teams
            $this->db->table('teams')
                ->whereIn('team_id', [$t1['team_id'], $t2['team_id']])
                ->update(['team_status' => 'locked']);

            $matches_created[] = [
                'match_id' => $match_id,
                'team1'    => $t1['team_name'],
                'team2'    => $t2['team_name'],
            ];

            // Notify all members of both teams
            $this->_notifyTeamMatch($t1, $t2, $match_date);
        }

        // Mark matchmaking done
        $this->db->table('listing_events')
            ->where('listing_event_id', $listing_event_id)
            ->update(['listing_event_matchmaking_done' => 1]);

        $response = [
            'status'          => true,
            'message'         => 'Matchmaking complete. ' . count($matches_created) . ' match(es) created.',
            'total_teams'     => $total,
            'matches_created' => count($matches_created),
            'bracket'         => $matches_created,
        ];

        if ($bye_team) {
            $response['bye_team'] = $bye_team['team_name'];
            $response['message'] .= ' Team "' . $bye_team['team_name'] . '" gets a bye to Round 2.';
        }

        return $this->getResponse($response);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE — send match notification to all members of both teams
    // ─────────────────────────────────────────────────────────────
    private function _notifyTeamMatch($team1, $team2, $match_date)
    {
        $date_str = date('d M Y h:i A', strtotime($match_date));

        foreach ([$team1, $team2] as $my_team) {
            $opp = ($my_team['team_id'] === $team1['team_id']) ? $team2 : $team1;

            $msg_title = 'Match Scheduled!';
            $msg_desc  = 'Your team "' . $my_team['team_name'] . '" has been matched against "' . $opp['team_name'] . '" on ' . $date_str . '. Get ready!';

            $this->db->table('messages')->insert([
                'msg_title'      => $msg_title,
                'msg_desc'       => $msg_desc,
                'msg_image'      => null,
                'msg_active'     => 1,
                'msg_created_at' => date('Y-m-d H:i:s'),
                'msg_updated_at' => date('Y-m-d H:i:s'),
            ]);
            $msg_id = $this->db->insertID();

            // Get all accepted members of this team
            $members = $this->db->table('team_members')
                ->where('tm_team_id', $my_team['team_id'])
                ->where('tm_status', 'accepted')
                ->where('tm_user_id IS NOT NULL', null, false)
                ->get()->getResult();

            foreach ($members as $member) {
                $this->db->table('notifications')->insert([
                    'notify_msg_id' => $msg_id,
                    'notify_for'    => $member->tm_user_id,
                    'notify_from'   => 0,
                    'notify_count'  => 0,
                    'notify_active' => 1,
                    'notify_time'   => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
