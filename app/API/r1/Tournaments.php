<?php
namespace App\API\r1;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ReqresTrait;
use App\Models\CommonModel;
use App\Models\TournamentModel;

class Tournaments extends ResourceController
{
    use ReqresTrait;
    protected $common;
    protected $tournament;
	public function __construct()
	{
		helper(['form','url','custom','number']);
		$this->common = new CommonModel();
        $this->tournament = new TournamentModel();
	}

	public  function schedules()
	{
		$response = [];
        $rules = [
            'limit' => ['label' => 'Page Limit', 'rules' =>'required|numeric'],
            'page' => ['label' => 'page', 'rules' =>'required|numeric'],
        ];
        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        }else{
            $input=$this->getRequestInput($this->request);

            $per_page = xss_clean($input['limit']);
            $page = xss_clean($input['page']);

            $city_id = !empty($input['city_id'])?$input['city_id']:NULL;
            $country = !empty($input['country_id'])?$input['country_id']:NULL;
            $sports_id = !empty($input['sports_id'])?$input['sports_id']:NULL;
            $completed = !empty($input['status'])?$input['status']:NULL;
            $query = !empty($input['query'])?$input['query']:NULL;


            $item_count = $this->tournament->count_schedules();
           
            $offset = ($page*$per_page)-$per_page;
            if($page == 1){
                $offset = 0;
            }
            $result = $this->tournament->get_schedules($per_page, $offset);

            if(!empty($result)){
                
                $response = [
                        'status' => true,
                        'message' => 'Success',
                        'data' => $result
                    ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }      
        return $this->getResponse($response);
	}


    public  function getSchedulerEvents()
    {
        $response = [];
        $rules = [
            'limit' => ['label' => 'Page Limit', 'rules' =>'required|numeric'],
            'page' => ['label' => 'page', 'rules' =>'required|numeric'],
            'listing_id' => ['label' => 'listing_id', 'rules' =>'required|numeric'],
        ];

        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];

        }else{

            $input=$this->getRequestInput($this->request);

            $per_page = xss_clean($input['limit']);
            $page = xss_clean($input['page']);
            $tourney_id = xss_clean($input['listing_id']);

            $city_id = !empty($input['city_id'])?$input['city_id']:NULL;
            $country = !empty($input['country_id'])?$input['country_id']:NULL;
            $sports_id = !empty($input['sports_id'])?$input['sports_id']:NULL;
            $completed = !empty($input['status'])?$input['status']:NULL;
            $query = !empty($input['query'])?$input['query']:NULL;


            $item_count = $this->tournament->count_scheduler_events($tourney_id);
           
            $offset = ($page*$per_page)-$per_page;
            if($page == 1){
                $offset = 0;
            }
            $result = $this->tournament->get_scheduler_events($per_page, $offset,$tourney_id);

            if(!empty($result)){
                
                $response = [
                        'status' => true,
                        'message' => 'Success',
                        'data' => $result
                    ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }  
            
        return $this->getResponse($response);
    }


    public  function getSchedulerGroups()
    {
        $response = [];
        $rules = [
            'listing_event_id' => ['label' => 'listing_event_id', 'rules' =>'required|numeric'],
        ];
        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        }else{
            $input=$this->getRequestInput($this->request);

            $event_id = xss_clean($input['listing_event_id']);

            $city_id = !empty($input['city_id'])?$input['city_id']:NULL;
            $country = !empty($input['country_id'])?$input['country_id']:NULL;
            $sports_id = !empty($input['sports_id'])?$input['sports_id']:NULL;
            $completed = !empty($input['status'])?$input['status']:NULL;    
            $query = !empty($input['query'])?$input['query']:NULL;


            $result = $this->tournament->get_scheduler_groups($event_id);

            if(!empty($result)){
                
                $response = [
                        'status' => true,
                        'message' => 'Success',
                        'data' => $result
                    ];
            }else{
                $response = [
                    'status' => false,
                    'message' => 'No data found'
                ];
            }
        }      
        return $this->getResponse($response);
    }

    public  function getRoundsWithMatches()
    {
        $response = [];
        $rules = [
            'listing_event_id' => ['label' => 'listing_event_id', 'rules' =>'required|numeric'],
            'group_id' => ['label' => 'Group Id', 'rules' =>'required|numeric'],
        ];
        if (! $this->validate($rules))
        {
            $response = [
                'status' => false,
                'message' => 'Validation error',
                'data' => $this->validator->getErrors()
            ];
        }else{
            $input=$this->getRequestInput($this->request);
            $event_id = xss_clean($input['listing_event_id']);
            $group_id = xss_clean($input['group_id']);
            $result = $this->tournament->get_rounds_and_matches($event_id,$group_id);

            if(!empty($result)){

                $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $result,
                    'has_players' => true
                ];

            }else{

                $group = null;
                $groups = $this->tournament->get_scheduler_groups($event_id);
                if (is_array($groups)) {
                    foreach ($groups as $grp) {
                        if ((int)$grp->grp_id === (int)$group_id) {
                            $group = $grp;
                            break;
                        }
                    }
                }

                if ($group) {
                    $response = [
                        'status'       => true,
                        'message'      => 'No match scheduled',
                        'data'         => [],
                        'has_players'  => (bool)$group->has_players,
                        'player_count' => (int)$group->player_count
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'No data found'
                    ];
                }
            }
        }      
        return $this->getResponse($response);
    }

    // GET /api/r1/event_bracket?event_id=X
    public function event_bracket()
    {
        $event_id = (int) $this->request->getGet('event_id');
        if (!$event_id) {
            return $this->getResponse(['status' => false, 'message' => 'event_id is required']);
        }

        $db    = \Config\Database::connect();
        $event = $db->table('listing_events le')
            ->select('le.*, e.event_name')
            ->join('events e', 'e.event_id = le.listing_event_title', 'left')
            ->where('le.listing_event_id', $event_id)
            ->get()->getRow();

        if (!$event) {
            return $this->getResponse(['status' => false, 'message' => 'Event not found']);
        }

        $rounds = $db->table('tbl_rounds')
            ->where('round_event', $event_id)
            ->orderBy('round_id', 'ASC')
            ->get()->getResult();

        $is_individual = $event->listing_event_type === 'individual';

        foreach ($rounds as $round) {
            $matches = $db->table('matches as m')
                ->select('m.*,
                    t1.team_name as team1_name,
                    t2.team_name as team2_name,
                    wt.team_name as winner_name,
                    p1.participant_name as player1_name,
                    p2.participant_name as player2_name,
                    wp.participant_name as winner_player_name')
                ->join('teams t1', 't1.team_id = m.match_team1_id', 'left')
                ->join('teams t2', 't2.team_id = m.match_team2_id', 'left')
                ->join('teams wt', 'wt.team_id = m.match_winning_team_id', 'left')
                ->join('participants p1', 'p1.participant_id = m.match_individual1_id', 'left')
                ->join('participants p2', 'p2.participant_id = m.match_individual2_id', 'left')
                ->join('participants wp', 'wp.participant_id = m.match_winning_individual_id', 'left')
                ->where('m.match_round_id', $round->round_id)
                ->get()->getResult();

            $round->matches     = $matches;
            $round->match_count = count($matches);
            $round->is_complete = !empty($matches) && count(array_filter(
                $matches,
                fn($m) => $is_individual ? !empty($m->match_winning_individual_id) : !empty($m->match_winning_team_id)
            )) === count($matches);
        }

        return $this->getResponse([
            'status' => true,
            'message' => 'Success',
            'data' => ['event' => $event, 'rounds' => $rounds],
        ]);
    }

    // POST /api/r1/start_next_round  (organizer)
    // Body: event_id, round_id, match_date, bye_id (optional)
    public function startNextRound()
    {
        $input      = $this->getRequestInput($this->request);
        $event_id   = (int) ($input['event_id'] ?? 0);
        $round_id   = (int) ($input['round_id'] ?? 0);
        $match_date = $input['match_date'] ?? '';
        $bye_id     = (int) ($input['bye_id'] ?? 0);

        if (!$event_id || !$round_id || !$match_date) {
            return $this->getResponse(['status' => false, 'message' => 'event_id, round_id and match_date are required']);
        }

        $db    = \Config\Database::connect();
        $now   = date('Y-m-d H:i:s');
        $event = $db->table('listing_events le')
            ->select('le.*, s.sports_name')
            ->join('listings l', 'l.listing_id = le.listing_event_listing_id', 'left')
            ->join('sports s', 's.sports_id = l.listing_sports_id', 'left')
            ->where('le.listing_event_id', $event_id)
            ->get()->getRow();

        if (!$event) {
            return $this->getResponse(['status' => false, 'message' => 'Event not found']);
        }

        $matches = $db->table('matches')
            ->where('match_round_id', $round_id)
            ->where('match_event', $event_id)
            ->get()->getResult();

        if (empty($matches)) {
            return $this->getResponse(['status' => false, 'message' => 'No matches found in this round']);
        }

        $is_individual = $event->listing_event_type === 'individual';

        foreach ($matches as $m) {
            $has_winner = $is_individual ? !empty($m->match_winning_individual_id) : !empty($m->match_winning_team_id);
            if (!$has_winner) {
                return $this->getResponse(['status' => false, 'message' => 'All matches must have a winner before starting the next round']);
            }
        }

        $winners = array_map(
            fn($m) => (int) ($is_individual ? $m->match_winning_individual_id : $m->match_winning_team_id),
            $matches
        );

        if ($bye_id) $winners[] = $bye_id;

        $total = count($winners);
        if ($total < 2) {
            return $this->getResponse(['status' => false, 'message' => 'Tournament complete — only 1 winner remains']);
        }

        $existing_rounds = $db->table('tbl_rounds')->where('round_event', $event_id)->countAllResults();
        $round_name = $this->_getRoundName($total, (string) ($event->sports_name ?? ''), $existing_rounds);

        $db->table('tbl_rounds')->insert([
            'round_name'       => $round_name,
            'round_group'      => 0,
            'round_tournament' => $event->listing_event_listing_id,
            'round_event'      => $event_id,
            'round_status'     => 1,
            'round_created_at' => $now,
            'round_updated_at' => $now,
        ]);
        $new_round_id = $db->insertID();

        $bye_winner = null;
        if ($total % 2 !== 0) $bye_winner = array_pop($winners);

        $count = 0;
        foreach (array_chunk($winners, 2) as [$w1, $w2]) {
            $db->table('matches')->insert([
                'match_type'           => $is_individual ? 'individual' : $event->listing_event_type,
                'match_round_id'       => $new_round_id,
                'match_tournament'     => $event->listing_event_listing_id,
                'match_event'          => $event_id,
                'match_team1_id'       => $is_individual ? 0 : $w1,
                'match_team2_id'       => $is_individual ? 0 : $w2,
                'match_individual1_id' => $w1,
                'match_individual2_id' => $w2,
                'match_date'           => $match_date,
                'match_status'         => 1,
                'match_created_at'     => $now,
                'match_updated_at'     => $now,
            ]);
            $count++;
        }

        return $this->getResponse([
            'status'  => true,
            'message' => $round_name . ' created with ' . $count . ' match(es)',
            'data'    => [
                'round_id'   => $new_round_id,
                'round_name' => $round_name,
                'matches'    => $count,
                'bye_winner' => $bye_winner,
            ],
        ]);
    }

    private function _getRoundName(int $winner_count, string $sport = '', int $existing_rounds = 0): string
    {
        if ($winner_count <= 2) return 'Final';
        if ($winner_count <= 4) return 'Semi Finals';

        $sport = strtolower($sport);

        // Badminton: rounds -> semi final -> final (never "Quarter Finals")
        if (strpos($sport, 'badminton') !== false) {
            return 'Round ' . ($existing_rounds + 1);
        }

        // Cricket: group stage -> semi final -> final (never "Quarter Finals")
        if (strpos($sport, 'cricket') !== false) {
            return ($existing_rounds <= 0) ? 'Group Stage' : 'Round ' . ($existing_rounds + 1);
        }

        if ($winner_count <= 8) return 'Quarter Finals';
        return 'Round ' . $winner_count;
    }
}