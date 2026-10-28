<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                <?php if (!empty($edit)) { ?>
                    <h4 class="p-3 mb-0 dash-head1">Edit Event</h4>
                <?php } else { ?>
                    <h4 class="p-3 mb-0 dash-head1">Add Event</h4>
                <?php } ?>
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
                            <?php if (!empty($edit)) { ?>
                                <h5 class="card-title">Edit Event</h5>
                            <?php } else { ?>
                                <h5 class="card-title">Add Event</h5>
                            <?php } ?>

                            <form id="add-cleaners" action="<?= current_url(); ?>" method="post" style="max-width: 400px;" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" value="<?= !empty($event->event_name) ? $event->event_name : set_value('name') ?>" class="form-control" placeholder="Event name">
                                            <div class="text-danger">
                                                <?= session('errors.name') ?>
                                            </div>
                                            <div class="form-group mt-2">
                                                <label class="control-label">Upload Image</label>
                                                <div class="mb-3">
                                                    <div class="img-div mb-1">
                                                        <?php if (!empty($event->event_image)) { ?>
                                                            <img src="<?= base_url() . '/public/uploads/images/events/' . $event->event_image ?>" width="200" height="100" alt="Event Image">
                                                        <?php } else { ?>
                                                            <img src="<?= base_url() . "/public/assets/img/event1.png" ?>" width="200" height="100" alt="Event Image">
                                                        <?php } ?>
                                                    </div>
                                                    <input type="file" name="event_image" class="file" accept="image/*">
                                                    <div class="text-danger">
                                                        <?= session('errors.event_image') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info"><?= !empty($edit) ? 'Update' : 'Add'; ?></button>
                                <!--<button type="reset" class="btn btn-danger">Clear</button>-->
                            </form>
                        </div>
                        <div class="col-lg-7 col-md-7">
                            <?php if (!empty($events)) { ?>
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Events</th>
                                                <th>Image</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = ($pagination['current_page'] - 1) * 10 + 1; ?>
                                            <?php foreach ($events as $data) { ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><?= !empty($data->event_name) ? $data->event_name : ''; ?></td>
                                                    <td><img src="<?= base_url() . '/public/uploads/images/events/' . $data->event_image ?>" width="80" height="80"></td>
                                                   
                                                 <?php
                                                    if ($data->event_user_id != 2) { ?>
                                                        <td><a href="<?= base_url() ?>/admin/edit_events/<?= !empty($data->event_id) ? $data->event_id : ''; ?>" class="btn btn-sm btn-info">Edit</a>&nbsp;<a href="javascript:void(0)" onclick="delete_event('<?= !empty($data->event_id) ? $data->event_id : ''; ?>')" class="btn btn-sm btn-danger">Delete</a></td>
                                                    <?php } ?>                                                </tr>
                                            <?php $i++;
                                            } ?>
                                        </tbody>
                                    </table>
                                    <?php if ($pagination['total_pages'] > 1): ?>
                                        <nav>
                                            <ul class="pagination justify-content-center">
                                                <!-- Previous page link -->
                                                <li class="page-item <?= ($pagination['current_page'] == 1) ? 'disabled' : '' ?>">
                                                    <a class="page-link" href="<?= base_url('admin/events') ?>?page=<?= $pagination['current_page'] - 1 ?>">Previous</a>
                                                </li>

                                                <?php
$total_pages = $pagination['total_pages'];
$current_page = $pagination['current_page'];

$range = 2; // how many pages before and after current page to show
$dot_shown = false;

for ($p = 1; $p <= $total_pages; $p++):
    if (
        $p == 1 || // first page
        $p == $total_pages || // last page
        ($p >= $current_page - $range && $p <= $current_page + $range) // around current page
    ):
?>
    <li class="page-item <?= ($p == $current_page) ? 'active' : '' ?>">
        <a class="page-link" href="<?= base_url('admin/events') ?>?page=<?= $p ?>"><?= $p ?></a>
    </li>
<?php
        $dot_shown = false;
    else:
        if (!$dot_shown):
?>
    <li class="page-item disabled"><span class="page-link">...</span></li>
<?php
            $dot_shown = true;
        endif;
    endif;
endfor;
?>


                                                <!-- Next page link -->
                                                <li class="page-item <?= ($pagination['current_page'] == $pagination['total_pages']) ? 'disabled' : '' ?>">
                                                    <a class="page-link" href="<?= base_url('admin/events') ?>?page=<?= $pagination['current_page'] + 1 ?>">Next</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>

                                </div>
                            <?php } ?>
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
<style>
.pagination .page-item.active .page-link {
    background-color: #007bff !important; /* Bootstrap blue */
    border-color: #007bff !important;
    color: white !important;
}

.pagination .page-link {
    color: #007bff!important; /* Blue text for normal links */
}

.pagination .page-link:hover {
    background-color: #0056b3; /* Darker blue on hover */
    color: white !important;
}
</style>

<script>
    $(function() {
        $('#myTable').DataTable({
            "paging": true,
            "pageLength": 10,
            "lengthChange": false,
            "info": false,
            "ordering": true,
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