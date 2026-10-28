<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                <h4 class="p-3 mb-0 dash-head1">Users List</h4>
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
                <?php if (session()->has('success_message')) : ?>
                    <div class="alert alert-success">
                        <?= session('success_message') ?>
                        <?php session()->remove('success_message') ?>
                    </div>
                <?php endif ?>
                <div class="card-body">
                    <h5 class="card-title">Organizer List</h5>
                </div>
                <?php
                if (!empty($user)) {
                ?>
                    <div class="table-responsive px-1" style="position: relative;top: -25px;">
                        <table id="myTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>


                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                foreach ($user as $data) {

                                ?>
                                    <tr>
                                        <td><?= $data->user_id; ?></td>
                                 <td>
    <?php if (!empty($data->user_profile_img)): ?>
        <a href="<?= base_url('public/uploads/images/profile/' . $data->user_id . '/' . $data->user_profile_img) ?>" target="_blank">
            <img src="<?= base_url('public/uploads/images/profile/' . $data->user_id . '/' . $data->user_profile_img) ?>" style="max-width: 80px; height: 80px;">
        </a>
    <?php endif; ?>
</td>

                                        <td><?= $data->user_name; ?></td>

                                        <td><?= $data->user_email; ?></td>
                                        <td><?= $data->user_mobile; ?></td>
                                        <td><?= $data->user_address; ?></td>

                                        <td>
                                            <?php if ($data->user_active === '0' || is_null($data->user_active)) : ?>
                                                <button class="btn btn-info btn-sm dash-btn2" onclick="togglePayStatus(<?= $data->user_id ?>, 1)">Inactive</button>
                                            <?php else : ?>
                                                <button class="btn btn-success btn-sm dash-btn2" onclick="togglePayStatus(<?= $data->user_id ?>, 0)">Active</button>
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="javascript:void(0)" onclick="delete_post('<?= !empty($data->user_id) ? $data->user_id : ''; ?>')" class="btn btn-sm btn-danger">Delete</a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>
                <?php } else { ?>
                    <div class="jumbotron text-center">
                        <h5>No data found</h5>
                    </div>
                <?php } ?>
            </div>
            <!-- Card --->
        </div>
        <!-- Column -->
    </div>
</section>



<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script src="<?= base_url() ?>/public/assets/node_modules/datatables/datatables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            paging: true,
            order: [
                [0, 'desc']
            ],
            info: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search"
            }
        });
    });

    function togglePayStatus(postId, newStatus) {
        let statusText = newStatus === 1 ? "Active" : "mark as Inactive";
        if (confirm("Are you sure you want to " + statusText + "?")) {
            $.ajax({
                url:"<?=base_url();?>/Ajax/active_user",
                type: "POST",
                data: {
                    post_id: postId,
                    post_status: newStatus
                },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert("Failed to update post status.");
                }
            });
        }
    }

    function delete_post(id) {
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this Post?") == true) {
            $.ajax({
                url: "<?= base_url(); ?>/Ajax/delete_user",
                method: "POST",
                data: {
                    'post_id': id
                },
                success: function(response) {
                    location.reload();
                },
                error: function(response) {
                    alert('Something went wrong. try again');
                }
            });
        }
    }
</script>
<style>
    .pagination li a {
        color: #007bff !important;
        background-color: white !important;
    }

    .page-item.active .page-link {
        color: #fff !important;
        background-color: #007bff !important;
    }
</style>
<?= $this->endSection(); ?>