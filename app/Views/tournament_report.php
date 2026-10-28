<?= $this->extend('organizer_base'); ?>

<?= $this->section('main_content'); ?>
<section>
    <div class="container-fluid">
        <h3 class="p-3 mb-0">Tournament Report</h3>
        <div class="card">
            <div class="card-body">
                <?php if (!empty($report)) { ?>
                    <table class="table table-bordered">
                        <thead align="center">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Sport</th>
                                <th>Team</th>
                                <th>Round</th>
                                <th>Win Matches</th>
                                <th>Loss Matches</th>
                                <th>Description</th> <!-- Added Discretion Column -->
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php   
                            // print_r($report); die(); 
                            foreach ($report as $item): ?>
                                <tr>
                                    <td><?= esc($item->listing_id); ?></td>
                                    <td><?= esc($item->listing_title); ?></td>
                                    <td><?= esc($item->sports_name); ?></td>
                                    
                                    <td>
                                    <?= !empty($item->team1_name) ? esc($item->team1_name) : 'Unknown Team'; ?> 
                                    vs 
                                    <?= !empty($item->team2_name) ? esc($item->team2_name) : 'Unknown Team'; ?>
                                    </td>
                                    <td><?= esc($item->round_name); ?></td>

                                    <td>
                                        <?php
                                        // Check if there are any win matches for team1 or team2
                                        if($item->win_matches) {
                                            if ($item->win_matches == $item->team1_id) {
                                                echo esc($item->team1_name); // If win_matches > 0, team1 is the winner
                                            } elseif ($item->win_matches == $item->team2_id) {
                                                echo esc($item->team2_name); // If win_matches > 0 for team2, team2 is the winner
                                            }
                                        } else {
                                            echo 'N/A'; // No win found
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        // Check if there are any win matches for team1 or team2
                                        if($item->win_matches) {
                                            if ($item->win_matches != $item->team1_id) {
                                                echo esc($item->team1_name); // If win_matches > 0, team1 is the winner
                                            } elseif ($item->win_matches != $item->team2_id) {
                                                echo esc($item->team2_name); // If win_matches > 0 for team2, team2 is the winner
                                            }
                                        } else {
                                            echo 'N/A'; // No win found
                                        }
                                        ?>
                                    </td>


                                    
                                    <!-- description Column -->
                                     <td><?= esc($item->description);?></td>
                                    


                                    <!-- Status Column -->
                                    <td>
                                            <?php
                                            if (isset($item->match_status)) {


                                                if ($item->match_status == 0) {
                                                    echo '<span class="badge bg-success">Completed</span>';
                                                } elseif ($item->match_status == 1) {
                                                    echo '<span class="badge bg-warning"> Pending  </span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">Unknown Status</span>';
                                                }
                                            } else {
                                                echo '<span class="badge bg-secondary">Not Available</span>';
                                            }
                                            ?>
                                        </td>
                                    <!-- matches_status == 0 is Completed
                                     matches_status == 1 is Pending... -->




                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-warning">No data found.</div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
