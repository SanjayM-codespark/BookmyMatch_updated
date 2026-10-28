<?php


namespace App\Controllers;

use App\Models\UmpireModel;

class OrganizerController extends BaseController
{
    public function umpireMatchList()
    {
        // Load the UmpireModel
        $umpireModel = new UmpireModel();

        // Fetch all umpires
        $data['umpires'] = $umpireModel->findAll();

        // Load the view and pass the data
        return view('organizer/umpire_match_list', $data);
    }
}

?>