<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                    <h4 class="p-3 mb-0 dash-head1">Event</h4>
               
            </div>
            <div class="col-md-6 col-6 d-flex  justify-content-end m-auto ">
                <a href="<?= base_url(); ?>" class="mx-5 dash-link1">Home</a>
            </div>
        </div>
    </div>
</section>
<section class="p-4">
    <div class="row">
        <!-- Column -->
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
                    <div class="row">
                        <div class="col-lg-5 col-md-5">
                           
                                <h5 class="card-title">Event Name</h5>

                            <form id="add-cleaners" action="<?= current_url(); ?>" method="post" style="max-width: 400px;" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" value="<?= !empty($event->event_name) ? $event->event_name : set_value('name') ?>" class="form-control" placeholder="Event name" disabled>
                                            <div class="text-danger">
                                                <?= session('errors.name') ?>
                                            </div>
                                            <div class="form-group mt-2">
                                                <label class="control-label"> Event Image</label>
                                                <div class="mb-3">
                                                    <div class="img-div mb-1">
                                                        <?php if(!empty($event->event_image)) { ?>
                                                            <img src="<?= base_url() . '/public/uploads/images/events/' . $event->event_image ?>" width="200" height="100" alt="Event Image">
                                                        <?php } else { ?>
                                                            <img src="<?= base_url() . "/public/assets/img/event1.png" ?>" width="200" height="100" alt="Event Image">
                                                        <?php } ?>
                                                    </div>
                                                    <div class="text-danger">
                                                        <?= session('errors.event_image') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
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

    function delete_event(id) {
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this Event?") == true) {
            $.ajax({
                url: "<?= base_url(); ?>/Ajax/delete_event",
                method: "POST",
                data: {
                    'event_id': id
                },
                success: function(response) {
                    var obje = jQuery.parseJSON(response);
                    if (obje.success == 'success') {
                        alert(obje['message']);
                        window.location.href = currenturl;
                    } else if (obje['failed'] == 'error') {
                        alert(obje['message']);
                    }
                },
                error: function(response) {
                    alert('Something went wrong. try again');
                }
            });
        }
    }
</script>
<?= $this->endSection(); ?>