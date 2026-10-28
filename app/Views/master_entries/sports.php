<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                <h4 class="p-3 mb-0 dash-head1">Sports</h4>
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
                            <?php
                            if (!empty($edit)) { ?>
                                <h5 class="card-title">Edit Sports</h5>
                            <?php } else { ?>
                                <h5 class="card-title">Add Sports</h5>
                            <?php  }
                            ?>

                            <form id="add-cleaners" action="<?= current_url(); ?>" method="post" style="max-width: 400px;" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" value="<?= !empty($sport->sports_name) ? $sport->sports_name : set_value('name') ?>" class="form-control" placeholder="Sport name">
                                            <div class="text-danger">
                                                <?= session('errors.name') ?>
                                            </div>
                                        </div>
                                        <div class="form-group mt-2">
                                            <label class="control-label">Max Players Per Team</label>
                                            <input type="number" id="max_players" name="max_players" min="1" value="<?= !empty($sport->sports_max_players) ? $sport->sports_max_players : set_value('max_players', 1) ?>" class="form-control" placeholder="e.g. 11 for Cricket, 5 for Basketball">
                                            <div class="text-danger">
                                                <?= session('errors.max_players') ?>
                                            </div>
                                        </div>
                                        <div class="form-group mt-2">
                                            <label class="control-label">Upload Image</label>
                                            <input type="file" id="image" name="image">
                                            <div class="text-danger">
                                                <?= session('errors.image') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info"><?= !empty($edit) ? 'Update' : 'Add'; ?></button>
                                <!--<button type="reset" class="btn btn-danger">Clear</button>-->
                            </form>
                        </div>
                        <div class="col-lg-7 col-md-7">
                            <?php
                            if (!empty($sports)) {
                            ?>
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Sports</th>
                                                <th>Max Players</th>
                                                <th>Image</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            foreach ($sports as $data) { ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><?= !empty($data->sports_name) ? $data->sports_name : ''; ?></td>
                                                    <td><?= !empty($data->sports_max_players) ? $data->sports_max_players : 1; ?></td>
                                                    <td><img src="<?= base_url(); ?>/public/uploads/images/sports/<?= !empty($data->sports_image) ? $data->sports_image : ''; ?>" width="80" height="80"></td>
                                                    <td><a href="<?= base_url() ?>/admin/edit_sports/<?= !empty($data->sports_id) ? $data->sports_id : ''; ?>" class="btn btn-sm btn-info">Edit</a>&nbsp;<a href="javascript:void(0)" onclick="delete_sport('<?= !empty($data->sports_id) ? $data->sports_id : ''; ?>')" class="btn btn-sm btn-danger">Delete</a></td>
                                                </tr>
                                            <?php $i++;
                                            } ?>
                                        </tbody>
                                    </table>
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
<script>
  
    function delete_sport(id) {
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this Sport?") == true) {
            $.ajax({
                url: "<?= base_url(); ?>/Ajax/delete_sports",
                method: "POST",
                data: {
                    'sports_id': id
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function() {
    const table = $('#myTable').DataTable({
      paging: true,
      ordering: true,
      info: true,
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search"
      },

    });

    $('#participantSearch').on('keyup', function() {
      table.search(this.value).draw();
    });
  });
</script>
<style>
  .pagination li a {
        color: #007bff!important;
    }
    .page-item.active .page-link {
        color: #fff!important;
    }
</style>
<?= $this->endSection(); ?>