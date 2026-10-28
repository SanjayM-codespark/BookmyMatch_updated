<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ReportModel;
use App\Models\CommonModel;
use App\Models\TournamentModel;

class ReportController extends BaseController
{
    protected $reportModel;
    protected $common;
    protected $tournament;

    public function __construct()
    {
        $this->reportModel = new ReportModel(); // Load Report Model
        $this->common = new CommonModel();
        $this->tournament = new TournamentModel();
    }

    public function report($list_id)
    {
        if ((session()->get('role_id') != 1) && (session()->get('role_id') != 3)) {
            return redirect()->to(base_url());
        }

        $data['body_class'] = 'skin-default card-no-border';
        $user_id = session()->get('user_id');
        $data['users'] = $this->common->select('users', '*', array('user_id' => $user_id));

        $data['commonModel'] = $this->common;
        $data['tourneyModel'] = $this->tournament;

        $data['report'] = $this->reportModel->getTournamentReport($list_id);

        echo view('tournament_report', $data);
    }
}
