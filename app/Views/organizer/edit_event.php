<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
        <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-6">
                    <h4 class="text-black mt-3">Add Your Event</h4>
                </div>
            </div>
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
                            
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-9">
                                    <h5 class="p-3 mb-0 dash-head1">Add Events</h5>
                                    <ul class="list-group">
                                        <li class=" list-group-item">
                                            <div id="">
                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label for="event_name" class="col-form-label ">Events</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <select class="form-control custom-select inputgroups" id="event_name" name="event_name[]">
                                                            <option value="">Select Events</option>
                                                            <?php
                                                            if (!empty($event_list)) {
                                                                foreach ($event_list as $mfr) { ?>
                                                                    <option value="<?= $mfr->event_id; ?>" <?php echo (set_value('event_name[]') == $mfr->event_id) ? " selected=' selected'" : "" ?>><?= ucfirst($mfr->event_name); ?></option>
                                                            <?php   }
                                                            } ?>
                                                        </select>
                                                        <div class="text-danger">
                                                            <?= session('errors.event_name[]') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label for="event_type" class="col-form-label ">Event type</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <select class="form-control custom-select inputgroups event_type_select" name="event_type[]">
                                                            <option value="">Select Type</option>
                                                            <option value="individual" <?php echo (set_value('event_type[]') == 'individual') ? " selected=' selected'" : "" ?>>Individual</option>
                                                            <option value="doubles" <?php echo (set_value('event_type[]') == 'doubles') ? " selected=' selected'" : "" ?>>Doubles</option>
                                                            <option value="team" <?php echo (set_value('event_type[]') == 'team') ? " selected=' selected'" : "" ?>>Team</option>
                                                        </select>
                                                        <div class="text-danger">
                                                            <?= session('errors.event_type[]') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3 team-settings" style="display:none;">
                                                    <div class="col-md-2">
                                                        <label class="col-form-label">Max Players Per Team</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="number" class="form-control inputgroups" name="max_players[]" min="1" value="1" placeholder="Max players per team">
                                                    </div>
                                                </div>
                                                <div class="row mb-3 team-settings" style="display:none;">
                                                    <div class="col-md-2">
                                                        <label class="col-form-label">Max Teams</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="number" class="form-control inputgroups" name="max_teams[]" min="0" value="0" placeholder="0 = unlimited">
                                                        <small class="text-muted">0 = unlimited</small>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label for="type" class="col-form-label">Type</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <select id="type" class="form-control inputgroups" name="type[]">
                                                            <option value="" disabled selected>Choose type</option>
                                                            <option value="knockout">Knock out</option>
                                                            <option value="knockout_round" >Knock out round</option>
                                                            <option value="round_robin_knockout">Round Robin Knockout</option>
                                                        </select>
                                                        <div class="text-danger">
                                                            <?= session('errors.type[]') ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label for="fee" class="col-form-label ">Fee</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" id="fee" class="form-control inputgroups" aria-describedby="passwordHelpInline" name="fee[]" value="<?= set_value('fee[]') ?>" placeholder="Specify the Fees for your Event">
                                                        <div class="text-danger">
                                                            <?= session('errors.fee[]') ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="date-form">

                                                    <div class="row mb-3">
                                                        <div class="col-md-2">
                                                            <label for="event_start" class="col-form-label ">Event Start Date</label>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input type="datetime-local" id="event_start" name="event_start[]" value="<?= set_value('event_start[]') ?>" onchange="updateEndDate(this)" class="form-control inputgroups start-date" placeholder="Start Date">
                                                            <div class="text-danger">
                                                                <?= session('errors.event_start[]') ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-2">
                                                            <label for="event_end" class="col-form-label ">Event End Date</label>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input type="datetime-local" id="event_end" name="event_end[]" value="<?= set_value('event_end[]') ?>" class="form-control inputgroups end-date" placeholder="End Date">
                                                            <div class="text-danger">
                                                                <?= session('errors.event_end[]') ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label for="lastbook_date" class="col-form-label ">Participants Last booking date</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="datetime-local" id="lastbook_date" name="lastbook_date[]" value="<?= set_value('lastbook_date[]') ?>" class="form-control inputgroups " placeholder="End Date">
                                                        <div class="text-danger">
                                                            <?= session('errors.lastbook_date[]') ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label class="col-form-label">Players Reporting Time</label>
                                                        <small class="d-block text-muted" style="font-size:.75rem;">Round 1 pairings are created on this date</small>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="datetime-local" name="matchmaking_date[]" class="form-control inputgroups">
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <label for="inlineCheckbox1" class="col-form-label ">IDs </label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="tnba_id[]" value="1">
                                                            <label class="form-check-label" for="inlineCheckbox1">TNBA-ID</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox2" name="bai_id[]" value="1">
                                                            <label class="form-check-label" for="inlineCheckbox2">BAI-ID</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" name="other_id[]" value="1">
                                                            <label class="form-check-label" for="inlineCheckbox3">Other-ID</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox4" name="dob[]" value="1">
                                                            <label class="form-check-label" for="inlineCheckbox4">DOB</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="add_del_btn_outer">
                                                <button id="addRow" type="button" class="btn btn-success btn-circle addbtn formicons"><i class="fa fa-plus"></i> </button>
                                                <button type="button" id="removeRow" class="btn  deletebtn"> <i class="fa-solid fa-trash-can" style="color:#e71a2d;font-size: 20px;"></i>
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-6 text-left">
                                    <button type="submit" class="btn org-btn2 p-0" style="width: 320px;height: 36px;left: 720px;top: 751px;background: #E71A2D;border-radius: 4px;color: #FFFFFF;padding: 12px;">Update</button>
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

        $(document).on("click", ".addbtn", function() {
            var cloned = $(this).parents('.list-group-item').clone();
            cloned.find('.event_type_select').val('');
            cloned.find('.team-settings').hide();
            cloned.appendTo($(this).parents('.list-group'));
        });
        $(document).on("click", ".deletebtn", function() {
            var count = $('.list-group').children('.list-group-item').length;
            if (count > 1) {
                $(this).parents('.list-group-item').remove();
            }
        });
        $(document).on('change', '.event_type_select', function() {
            var row = $(this).closest('.list-group-item');
            if ($(this).val() == 'team') {
                row.find('.team-settings').show();
            } else {
                row.find('.team-settings').hide();
            }
        });

</script>

<?= $this->endSection(); ?>