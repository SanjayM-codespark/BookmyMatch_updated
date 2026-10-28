<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>

<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
        <section>
        <div class="container-fluid">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card p-4" style="overflow-x:auto;background: #fff;">
                        <?php if (session()->has('success_message')) : ?>
                            <div class="alert alert-success">
                                <?= session('success_message') ?>
                                <?php session()->remove('success_message') ?>
                            </div>
                        <?php endif ?>
                        <?php if (session()->has('error')) : ?>
                            <div class="alert alert-danger">
                                <?= session('error') ?>
                                <?php session()->remove('error') ?>
                            </div>
                        <?php endif ?>

                        <form action="<?= current_url(); ?>" method="post">
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="inputPassword6" class="col-form-label ">Tournament</label>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control custom-select" id="tournament" name="tournament" required>
                                        <option value="">Select Tournament</option>
                                        <?php
                                        if (!empty($tournaments)) {
                                            foreach ($tournaments as $mfr) { ?>
                                                <option value="<?= $mfr->listing_id; ?>" <?php echo (!empty($matchdata) && $matchdata[0]->match_tournament == $mfr->listing_id) ? "selected" : "" ?>><?= ucfirst($mfr->listing_title); ?></option>
                                        <?php   }
                                        } ?>
                                    </select>
                                    <div class="text-danger">
                                        <?= session('errors.tournament') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="inputPassword6" class="col-form-label ">Event</label>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control custom-select" id="event" name="event" required>
                                        <option value="">Select Event</option>
                                        <?php
                                        if (!empty($events)) {
                                            foreach ($events as $mfr) { ?>
                                                <option value="<?= $mfr->listing_event_id; ?>" <?php echo (!empty($matchdata) && $matchdata[0]->match_event == $mfr->listing_event_id) ? "selected" : "" ?>><?= ucfirst($mfr->event_name??''); ?></option>
                                        <?php   }
                                        } ?>
                                    </select>
                                    <div class="text-danger">
                                        <?= session('errors.event') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="inputPassword6" class="col-form-label ">Group Name</label>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control custom-select" id="group" name="group_name" required>
                                        <option value="">Select Group</option>
                                        <?php
                                        if (!empty($tblgroup)) {
                                            foreach ($tblgroup as $mfr) { ?>
                                                <option value="<?= $mfr->grp_id; ?>" <?php echo (!empty($matchdata) && $matchdata[0]->match_group_id == $mfr->grp_id) ? "selected" : "" ?>><?= ucfirst($mfr->grp_name); ?></option>
                                        <?php   }
                                        } ?>
                                    </select>
                                    <div class="text-danger">
                                        <?= session('errors.event') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="inputPassword6" class="col-form-label ">Round Name</label>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control custom-select" id="round_name" name="round_name" required>
                                        <option value="">Select Round</option>
                                        <?php
                                        if (!empty($tblrounds)) {
                                            foreach ($tblrounds as $mfr) { ?>
                                                <option value="<?= $mfr->round_id; ?>" <?php echo (!empty($matchdata) && $matchdata[0]->match_round_id == $mfr->round_id) ? "selected" : "" ?>><?= ucfirst($mfr->round_name); ?></option>
                                        <?php   }
                                        } ?>
                                    </select>
                                    <div class="text-danger">
                                        <?= session('errors.event') ?>
                                    </div>
                                </div>
                            </div>

                            <div id="matchForm" style="max-width:700px;">
                                <?php if (!empty($matchdata[0]->match_round_id)) {
                                    $matchCount = 0;
                                    $team_event = $commonModel->select('teams', '*', array('team_event' => $matchdata[0]->match_event), true);
                                    foreach ($matchdata as $row) {
                                        $matchCount++; ?>
                                        <input type="hidden" name="match_id[]" value="<?= !empty($row->match_id) ? $row->match_id : '' ?>">
                                        <div class="row mb-3">
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Player <?= $matchCount * 2 - 1 ?></label>
                                                    <select class="form-control custom-select team1" name="team1[]" required>
                                                        <option value="">Select Player</option>
                                                        <?php
                                                        if (isset($team_event) && !empty($team_event)) {
                                                            foreach ($team_event as $data) { ?>
                                                                <option value="<?= $data->team_id; ?>" <?php echo ($row->match_team1_id == $data->team_id || $row->match_individual1_id == $data->team_id) ? "selected" : "" ?>><?= ucfirst($data->team_name); ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                    <div class="text-danger">
                                                        <?= session('errors.team1') ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-7">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Match Date</label>
                                                    <div class='input-group date' id='datetimepicker'>
                                                        <input type="datetime-local" name="match_date[]" class="form-control datetime-local" value="<?= $row->match_date; ?>" required />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                    <div class="text-danger">
                                                        <?= session('errors.match_date') ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Player <?= $matchCount * 2; ?></label>
                                                    <select class="form-control custom-select team2" name="team2[]" required>
                                                        <option value="">Select Player</option>
                                                        <?php
                                                        if (isset($team_event) && !empty($team_event)) {
                                                            foreach ($team_event as $data) { ?>
                                                                <option value="<?= $data->team_id; ?>" <?php echo (($row->match_team2_id == $data->team_id || $row->match_individual2_id == $data->team_id)) ? "selected" : "" ?>><?= ucfirst($data->team_name); ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                    <div class="text-danger">
                                                        <?= session('errors.team2') ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-7">
                                                <div class="mb-3">
                                                    <label class="col-form-label">Match Location</label>
                                                    <input type="text" value="<?= $row->match_location; ?>" class="form-control" name="match_location[]" placeholder="Match Location" required>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <select class="form-control custom-select" placeholder="Umpire" name="umpire[]">
                                                    <option value="">Select Umpire</option>
                                                    <?php
                                                    if (isset($umpires) && !empty($umpires)) {
                                                        foreach ($umpires as $data) { ?>
                                                            <option value="<?= $data->user_id; ?>" <?php echo ($row->match_umpire == $data->user_id) ? "selected" : "" ?>><?= ucfirst($data->user_full_name); ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                                <div class="text-danger">
                                                    <?= session('errors.umpire') ?>
                                                </div>
                                            </div>
                                            <?php if ($matchCount > 1) { ?>
                                                <div class="col-md-7 text-right">
                                                    <button type="button" class="btn btn-danger removeMatch" data-marchId="<?= !empty($row->match_id) ? $row->match_id : '' ?>">Delete</button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php
                                    }
                                } else { ?>
                                    <div class="row mb-3">
                                        <!-- Team 1 -->
                                        <div class="col-md-5">
                                            <div class="mb-3">
                                                <label for="inputPassword6" class="col-form-label">Team 1</label>
                                                <select class="form-control custom-select tem1" name="team1[]" required>
                                                    <option value="">Select Player</option>
                                                    <!-- Options can be added dynamically if needed -->
                                                </select>
                                                <div class="text-danger">
                                                    <?= session('errors.team1') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Match Date -->
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <label for="inputPassword6" class="col-form-label">Match Date</label>
                                                <div class='input-group date' id='datetimepicker'>
                                                    <input type="datetime-local" name="match_date[]" class="form-control datetime-local" value="" required />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                                <div class="text-danger">
                                                    <?= session('errors.match_date') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Team 2 -->
                                        <div class="col-md-5">
                                            <div class="mb-3">
                                                <label for="inputPassword6" class="col-form-label">Team 2</label>
                                                <select class="form-control custom-select tem2" name="team2[]" required>
                                                    <option value="">Select Player</option>
                                                    <!-- Options can be added dynamically if needed -->
                                                </select>
                                                <div class="text-danger">
                                                    <?= session('errors.team2') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Match Location -->
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <label for="inputPassword6" class="col-form-label">Match Location</label>
                                                <input type="text" class="form-control" name="match_location[]" placeholder="Match Location" required>
                                                <div class="text-danger">
                                                    <?= session('errors.match_location[]') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <select class="form-control custom-select" placeholder="Umpire" name="umpire[]">
                                                <option value="">Select Umpire</option>
                                                <?php
                                                if (isset($umpires) && !empty($umpires)) {
                                                    foreach ($umpires as $data) { ?>
                                                        <option value="<?= $data->user_id; ?>"><?= ucfirst($data->user_full_name); ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                            <div class="text-danger">
                                                <?= session('errors.umpire') ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="add">Add Match</button>

                                    <button type="button" class="btn btn-primary" id="addMatch">Add Teams</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script>
    $(document).on('change', '#tournament', function() {
        var cslug = $(this).val();
        get_events(cslug);
    });

    function get_events(cslug) {
        $.ajax({
            type: "POST",
            data: {
                'tourney_id': cslug
            },
            url: "<?php echo base_url(); ?>/Organizer/get_ajax_org_events",
            success: function(html) {
                $('#event').html(html);
                var event_id = "<?= !empty($matchdata[0]->match_event) ? $matchdata[0]->match_event : ''; ?>";
                if (event_id) {
                    $('#event').val(event_id);
                }
            }
        });
    }

    $(document).on('change', '#event', function() {
        var cslug = $(this).val();
        get_evt_group(cslug);
    });

    function get_evt_group(cslug) {
        $.ajax({
            type: "POST",
            data: {
                'event_id': cslug
            },
            dataType: 'json',
            url: "<?php echo base_url(); ?>/Organizer/get_ajax_evt_group",
            success: function(res) {
                $('#group').html(res.rounds);
                var match_group_id = "<?= !empty($matchdata[0]->match_group_id) ? $matchdata[0]->match_group_id : ''; ?>";
                if (match_group_id) {
                    $('#group').val(match_group_id);
                }
            }
        });
    }

    $(document).on('change', '#group', function() {
        var cslug = $('#event').val();
        var cslug1 = $('#tournament').val();
        get_org_round(cslug, cslug1);
    });

    function get_org_round(cslug, cslug1) {
        $.ajax({
            type: "POST",
            data: {
                'event_id': cslug,
                'tournament_id': cslug1
            },
            dataType: 'json',
            url: "<?php echo base_url(); ?>/Organizer/get_ajax_org_round",
            success: function(res) {
                $('#round_name').html(res.rounds);
                $('.tem1').html(res.teams);
                $('.tem2').html(res.teams);
                var match_round_id = "<?= !empty($matchdata[0]->match_round_id) ? $matchdata[0]->match_round_id : ''; ?>";
                if (match_round_id) {
                    $('#round_name').val(match_round_id);
                }
            }
        });
    }
</script>

<?php
if (!empty($matchdata[0]->match_tournament)) {
?>
    <script>
        var cslug = "<?= $matchdata[0]->match_tournament; ?>";
        get_events(cslug);
    </script>
<?php
}
?>

<?php
if (!empty($matchdata[0]->match_event)) {
?>
    <script>
        var cslug2 = "<?= $matchdata[0]->match_event; ?>";
        get_evt_group(cslug2);

        var cslug3 = "<?= $matchdata[0]->match_event; ?>";
        var cslug4 = "<?= $matchdata[0]->match_tournament; ?>";
        get_org_round(cslug3, cslug4);
    </script>
<?php
}
?>


<script>
    let matchCount = 1; // Start with the initial match count

    document.getElementById('addMatch').addEventListener('click', function() {
        var tournament = $('#tournament').val();
        var cslug = $('#event').val();
        var cslug1 = $('#group').val();
        if (tournament == '') {
            alert('Select tournament');
            return false;
        }
        if (cslug == '') {
            alert('Select Event');
            return false;
        }

        matchCount++; // Increment the match count

        $.ajax({
            url: '<?php echo base_url(); ?>/Organizer/get_teams', // Replace with your actual API endpoint
            method: 'POST',
            data: {
                'event_id': cslug,
                'group_id': cslug1
            },
            success: function(res) {
                // Debugging: Log the received data
                console.log(res);

                // Parse the response if it's not already an array
                if (typeof res === 'string') {
                    res = JSON.parse(res);
                }

                // Ensure data is an array
                if (!Array.isArray(res)) {
                    console.error('Expected an array but received:', res);
                    return;
                }

                var teamOptions = '';
                res.forEach(function(team) {
                    teamOptions += `<option value="${team.team_id}">${team.team_name}</option>`;
                });

                var row = document.createElement('div');
                row.className = 'row mb-3';

                var col1 = document.createElement('div');
                col1.className = 'col-md-5';
                col1.innerHTML = `
                    <div class="mb-3">
                        <label class="col-form-label">Player ${matchCount * 2 - 1}</label>
                        <select class="form-control custom-select team1" name="team1[]" required>
                            <option value="">Select Player</option>
                            ${teamOptions}
                        </select>
                        <div class="text-danger">
                            <?= session('errors.team1') ?>
                        </div>
                    </div>
                `;

                var col2 = document.createElement('div');
                col2.className = 'col-md-5';
                col2.innerHTML = `
                    <div class="mb-3">
                        <label class="col-form-label">Player ${matchCount * 2}</label>
                        <select class="form-control custom-select team2" name="team2[]" required>
                            <option value="">Select Player</option>
                            ${teamOptions}
                        </select>
                        <div class="text-danger">
                            <?= session('errors.team2') ?>
                        </div>
                    </div>
                `;

                var col3 = document.createElement('div');
                col3.className = 'col-md-7';
                col3.innerHTML = `
                    <div class="mb-3">
                        <label class="col-form-label">Match Date</label>
                        <div class='input-group date' id='datetimepicker'>
                            <input type="datetime-local" name="match_date[]" class="form-control datetime-local" value="" required/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <div class="text-danger">
                            <?= session('errors.match_date') ?>
                        </div>
                    </div>
                `;

                var col4 = document.createElement('div');
                col4.className = 'col-md-7';
                col4.innerHTML = `
                    <div class="mb-3">
                        <label class="col-form-label">Match Location</label>
                        <input type="text" class="form-control" name="match_location[]" placeholder="Match Location" required>
                    </div>
                `;

                var col5 = document.createElement('div');
                col5.className = 'col-md-5';
                col5.innerHTML = `
                    <select class="form-control custom-select" placeholder="Umpire" name="umpire[]">
                        <option value="">Select Umpire</option>
                        <?php
                        if (isset($umpires) && !empty($umpires)) {
                            foreach ($umpires as $data) { ?> 
                                <option value="<?= $data->user_id; ?>" <?php echo (!empty($row->match_umpire) && $row->match_umpire == $data->user_id) ? " selected=' selected'" : "" ?>><?= ucfirst($data->user_full_name); ?></option> 
                        <?php }
                        } ?>
                    </select>
                    <div class="text-danger">
                        <?= session('errors.umpire') ?>
                    </div>
                `;

                var col6 = document.createElement('div');
                col6.className = 'col-md-7 text-right';
                col6.innerHTML = `
                    <button type="button" class="btn btn-danger removeMatch">Delete</button>
                `;

                row.appendChild(col1);
                row.appendChild(col3);
                row.appendChild(col2);
                row.appendChild(col4);
                row.appendChild(col5);
                row.appendChild(col6);

                // Insert the new row before the Add Match button
                var addMatchButton = document.getElementById('addMatch').parentNode;
                document.getElementById('matchForm').insertBefore(row, addMatchButton);

                // Add event listener to the new delete button
                row.querySelector('.removeMatch').addEventListener('click', function() {
                    row.remove();
                    updateLabels();
                });

                // Update labels after adding new row
                updateLabels();
            },
            error: function(err) {
                console.error('Error fetching teams:', err);
            }
        });
    });

    // Add event listener to existing delete buttons
    document.querySelectorAll('.removeMatch').forEach(function(button) {
        button.addEventListener('click', function() {
            const row = button.closest('.row.mb-3');
            const matchId = $(this).data('marchid');
            if (matchId) {
                if (confirm("Are you sure want delete this match?") == true) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>/Organizer/delete_match',
                        method: 'POST',
                        data: {
                            match_id: matchId
                        },
                        success: function(response) {
                            response = typeof response === 'string' ? JSON.parse(response) : response;
                            if (response.status === 'success') {
                                row.remove();
                                updateLabels();
                                alert('Match deleted successfully');
                            } else {
                                alert('Failed to delete the match. Please try again.');
                            }
                        },
                        error: function(err) {
                            console.error('Error deleting match:', err);
                            alert('An error occurred. Please try again later.');
                        }
                    });
                }
            } else {
                row.remove();
                updateLabels();
            }
        });
    });



    function updateLabels() {
        const rows = document.querySelectorAll('#matchForm .row.mb-3');
        let counter = 1;

        rows.forEach(row => {
            const team1Label = row.querySelector('.col-md-5:nth-of-type(1) .col-form-label');
            const team2Label = row.querySelector('.col-md-5:nth-of-type(3) .col-form-label');

            if (team1Label && team2Label) {
                team1Label.innerText = `Player ${counter * 2 - 1}`;
                team2Label.innerText = `Player ${counter * 2}`;
                counter++;
            }
        });

        matchCount = counter - 1; // Update the match count to reflect the current number of matches
    }
</script>

<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            if (validateForm()) {
                //this.submit();

                $.ajax({
                    type: "POST",
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        $('.text-danger').html(''); // Clear previous errors
                        if (response.status === 'error') {

                            $.each(response.errors, function(field, message) {
                                let fieldSelector = `[name="${field}"]`;
                                $(fieldSelector).next('.text-danger').html(message);
                            });
                            let messages = '';
                            // Handle extra errors
                            if (response.extra_errors) {
                                $.each(response.extra_errors, function(index, extraError) {
                                    messages += '<div>' + extraError + '</div>';
                                });
                            }

                            // Display the accumulated messages in the #res-error element
                            $('#res-error').html(messages);

                        } else if (response.status === 'success') {
                            window.location.href = "<?= base_url('organizer/match_list') ?>";
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred. Please try again later.');
                    }
                });
            } else {
                alert('Please fill all the required fields.');
            }
        });
    });

    function validateForm() {
        let isValid = true;
        $('.text-danger').html(''); // Clear previous errors

        // Validate each select and input field
        $('select[required], input[required]').each(function() {
            if (!$(this).val()) {
                $(this).next('.text-danger').html('This field is required');
                isValid = false;
            }
        });

        return isValid;
    }
</script>

<?= $this->endSection(); ?>