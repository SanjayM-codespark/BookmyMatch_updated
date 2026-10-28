<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                <h4 class="p-3 mb-0 dash-head1"><?= !empty($event->event_id) ? 'Edit Event' : 'Add Event' ?></h4>
            </div>
            <div class="col-md-6 col-6 d-flex justify-content-end m-auto">
                <a href="<?= base_url(); ?>" class="mx-5 dash-link1">Home</a>
            </div>
        </div>
    </div>
</section>

<section class="p-4">
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">

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

                    <?php if (session()->has('errors') && is_string(session('errors'))) : ?>
                        <div class="alert alert-danger">
                            <?= session('errors') ?>
                            <?php session()->remove('errors') ?>
                        </div>
                    <?php endif ?>

                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <h5 class="card-title"><?= !empty($event->event_id) ? 'Edit Event' : 'Add Event' ?></h5>

                            <form id="add-events"
                                  action="<?= !empty($event->event_id) ? base_url('edit_events/' . $event->event_id) : base_url('events') ?>"
                                  method="post"
                                  style="max-width: 500px;"
                                  enctype="multipart/form-data">

                                <?= csrf_field() ?>

                                <div class="row">
                                    <div class="col-md-12">

                                        <!-- Event Name -->
                                        <div class="form-group">
                                            <label class="control-label">Event Name</label>
                                            <input type="text" id="name" name="name"
                                                   value="<?= !empty($event->event_name) ? esc($event->event_name) : set_value('name') ?>"
                                                   class="form-control" placeholder="Event name">
                                            <div class="text-danger">
                                                <?= session('errors.name') ?>
                                            </div>
                                        </div>

                                        <!-- Min Age -->
                                        <div class="form-group mt-2">
                                            <label class="control-label">Minimum Age</label>
                                            <input type="number" id="event_min_age" name="event_min_age" min="0"
                                                   value="<?= !empty($event->event_min_age) ? esc($event->event_min_age) : set_value('event_min_age') ?>"
                                                   class="form-control" placeholder="Minimum age">
                                            <div class="text-danger">
                                                <?= session('errors.event_min_age') ?>
                                            </div>
                                        </div>

                                        <!-- Max Age -->
                                        <div class="form-group mt-2">
                                            <label class="control-label">Maximum Age (Optional)</label>
                                            <input type="number" id="event_max_age" name="event_max_age" 
                                                   value="<?= !empty($event->event_max_age) ? esc($event->event_max_age) : set_value('event_max_age') ?>"
                                                   class="form-control" placeholder="Maximum age (optional)">
                                            <div class="text-danger">
                                                <?= session('errors.event_max_age') ?>
                                            </div>
                                        </div>

                                        <!-- Event Image -->
                                        <div class="form-group mt-2">
                                            <label class="control-label">Event Image</label>
                                            <div class="mb-3">
                                                <div class="img-div mb-1">
                                                    <?php if (!empty($event->event_image)) { ?>
                                                        <img id="eventImagePreview" src="<?= base_url() . '/public/uploads/images/events/' . $event->event_image ?>" width="200" height="100" alt="Event Image">
                                                    <?php } else { ?>
                                                        <img id="eventImagePreview" src="<?= base_url() . '/public/assets/img/event1.png' ?>" width="200" height="100" alt="Event Image">
                                                    <?php } ?>
                                                </div>
                                                <input type="file" id="event_image" name="event_image" class="form-control" accept="image/*" onchange="previewImage(event)">
                                                <div class="text-danger">
                                                    <?= session('errors.event_image') ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mt-2">
                                    <?= !empty($event->event_id) ? 'Update Event' : 'Add Event' ?>
                                </button>
                                <a href="<?= base_url('view_events') ?>" class="btn btn-secondary mt-2">Cancel</a>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->Section('pageScript') ?>
<script src="<?= base_url() ?>/public/assets/node_modules/datatables/datatables.min.js"></script>
<script>
    $(function() {
        $('#myTable').DataTable({
            "pageLength": 50,
            "bLengthChange": false,
            "bInfo": false,
        });
    });

    // Image preview function
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('eventImagePreview');
            preview.src = reader.result;
            preview.width = 200;
            preview.height = 100;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<?= $this->endSection(); ?>