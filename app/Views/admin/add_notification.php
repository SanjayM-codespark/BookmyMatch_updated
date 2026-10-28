<?= $this->extend('admin_base'); ?>

<?= $this->section('pageCss'); ?>
<link href="<?= base_url(); ?>/public/assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= base_url(); ?>/public/assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6">
                <h4 class="p-3 dash-head1 mb-0">Add Notification</h4>
            </div>
        </div>
    </div>
</section>

<section class="p-4">
    <div class="row">
        <div class="col-12">
            <?php if (session()->has('success_message')) : ?>
                <div class="alert alert-success"><?= session('success_message') ?></div>
                <?php session()->remove('success_message') ?>
            <?php endif ?>
            <?php if (session()->has('error_message')) : ?>
                <div class="alert alert-danger"><?= session('error_message') ?></div>
                <?php session()->remove('error_message') ?>
            <?php endif ?>
            <?php if (session()->has('errors')) : ?>
                <ul class="alert alert-danger">
                    <?php foreach (session('errors') as $error) : ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Notification</h4>
                </div>
                <div class="card-body">
                    <form action="<?= current_url(); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

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
                                    <select class="form-control custom-select" id="group" name="group_name">
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
                                    <select class="form-control custom-select" id="round_name" name="round_name">
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

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="title">Title</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="title" name="title" required>
                                <div class="text-danger"><?= session('errors.title') ?></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="desc">Description</label>
                            </div>
                            <div class="col-md-6">
                                <textarea class="form-control todolist" name="desc" rows="4" required></textarea>
                                <div class="text-danger"><?= session('errors.desc') ?></div>
                            </div>
                        </div>

                        <!-- Include Image -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="include_image">Include image</label>
                            </div>
                            <div class="col-md-6">
                                <input type="checkbox" name="include_image" id="include_image">
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="row mb-3" id="image_row" style="display: none;">
                            <div class="col-md-2">
                                <label for="image">Image</label>
                            </div>
                            <div class="col-md-6">
                                <input type="file" name="image" id="image">
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="row">
                            <div class="col-md-6 offset-md-2">
                                <button type="submit" name="submit" value="save" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </form>
                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col-12 -->
    </div> <!-- row -->
</section>

<?= $this->endSection(); ?>

<?= $this->section('pageScript') ?>
<script src="<?= base_url(); ?>/public/assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>/public/assets/plugins/sweetalert2/dist/sweetalert2.min.js"></script>

<script>
    $("#include_image").change(function () {
        if (this.checked) {
            $('#image_row').show('fast');
        } else {
            $('#image').val('');
            $('#image_row').hide('fast');
        }
    });
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

    $(document).on('change', '#event', function () {
    var tournamentId = $('#tournament').val();
    var eventId = $('#event').val();
    get_evt_group1(tournamentId, eventId);
});

function get_evt_group1(tournamentId, eventId) {
    $.ajax({
        type: "POST",
        data: {
            'event_id': eventId,
            'tournament_id': tournamentId
        },
        dataType: 'json',
        url: "<?= base_url(); ?>/Organizer/get_ajax_evt_group1",
        success: function (res) {
            $('#group').html(res.rounds);
            var match_group_id = "<?= !empty($matchdata[0]->match_group_id) ? $matchdata[0]->match_group_id : ''; ?>";
            if (match_group_id) {
                $('#group').val(match_group_id);
            }
        }
    });
}


    $(document).on('change', '#group', function() {
         var cslug1 = $('#tournament').val();
        var cslug = $('#event').val();
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
            url: "<?php echo base_url(); ?>/Organizer/get_ajax_org_round1",
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

<?= $this->endSection(); ?>