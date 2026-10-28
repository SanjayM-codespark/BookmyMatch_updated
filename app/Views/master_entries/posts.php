<?= $this->extend('admin_base'); ?>

<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>
<main class="org-dash-sec content">
    <section style="background-color: #CAD6F8;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-6">
                    <h5 class="p-3 dash-head1 mb-0">Posts</h5>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container-fluid">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card" style="overflow-x:auto;">
                        <?php if (session()->has('success_message')) : ?>
                            <div class="alert alert-success">
                                <?= session('success_message') ?>
                                <?php session()->remove('success_message') ?>
                            </div>
                        <?php endif ?>
                        <div class="mb-3 text-end">
                            <button class="btn btn-success btn-sm" onclick="bulkAction('activate')">Bulk Activate</button>
                                                            <button class="btn btn-primary btn-sm me-2" onclick="bulkAction('deactivate')">Bulk Deactivate</button>
                            <button class="btn btn-danger btn-sm" onclick="bulkAction('delete')">Bulk Delete</button>
                        </div>

                        <div class="table-responsive">
                            <table class="dash-table table mb-0" id="userTable">
                                <thead style="background: #fff">
                                    <tr class="text-center">
                                        <th><input type="checkbox" id="select-all"></th> <!-- Master checkbox -->
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Sender</th>
                                        <th>Content</th>
                                        <th>Images</th>
                                        <th>Videos</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody style="background: #fff">
                                    <?php if (!empty($posts)) : ?>
                                        <?php foreach ($posts as $data) : ?>
                                            <tr>
                                                <td><input type="checkbox" class="select-item" value="<?= $data->post_id ?>"></td>
                                                <td><?= esc($data->post_id) ?></td>
                                                <td><?= date('d-m-Y', strtotime($data->post_created_at)) ?></td>
                                                <td><?= esc($data->user_name) ?></td>
                                                <td><?= esc($data->post_content) ?></td>
                                                <td>
                                                    <?php
                                                    $images = json_decode($data->post_images_json);
                                                    if (!empty($images)) {
                                                        foreach ($images as $img) {
                                                            $img_url = base_url($img);
                                                            echo '<a href="' . $img_url . '" target="_blank">';
                                                            echo '<img src="' . $img_url . '" alt="Post Image" style="height: 60px; width: 70px;">';
                                                            echo '</a>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $videos = json_decode($data->post_videos_json);
                                                    if (!empty($videos)) {
                                                        foreach ($videos as $video) {
                                                            $video_url = base_url($video);
                                                            echo '<a href="' . $video_url . '" target="_blank">';
                                                            echo '<video src="' . $video_url . '" style="height: 80px; width: 70px;" muted></video>';
                                                            echo '</a>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if ($data->post_status === '0' || is_null($data->post_status)) : ?>
                                                        <button class="btn btn-info btn-sm dash-btn2" onclick="togglePayStatus(<?= $data->post_id ?>, 1)">Inactive</button>
                                                    <?php else : ?>
                                                        <button class="btn btn-success btn-sm dash-btn2" onclick="togglePayStatus(<?= $data->post_id ?>, 0)">Active</button>
                                                    <?php endif; ?>
                                                </td>
                                                <td><a href="javascript:void(0)" onclick="delete_post('<?= !empty($data->post_id) ? $data->post_id : ''; ?>')" class="btn btn-sm btn-danger">Delete</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No data found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- card -->
                </div>
            </div>
        </div>
    </section>
</main>
<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#userTable').DataTable({
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
    // Select/Deselect all checkboxes
$('#select-all').on('click', function () {
    $('.select-item').prop('checked', this.checked);
});

// Bulk Action
function bulkAction(action) {
    var selectedIds = [];
    $('.select-item:checked').each(function () {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        alert("Please select at least one post.");
        return;
    }

    let confirmMsg = action === 'delete' ? "delete" : (action ==='activate'?"activate":"deactivate");
    if (!confirm("Are you sure you want to " + confirmMsg + " selected posts?")) {
        return;
    }

    $.ajax({
        url: "<?= base_url(); ?>/Ajax/bulk_action_posts",
        type: "POST",
        data: {
            action: action,
            ids: selectedIds
        },
        success: function (response) {
            location.reload();
        },
        error: function () {
            alert("Bulk action failed. Try again.");
        }
    });
}


    function togglePayStatus(postId, newStatus) {
        let statusText = newStatus === 1 ? "Activate" : "mark as Inactive";
        if (confirm("Are you sure you want to " + statusText + "?")) {
            $.ajax({
                url: "<?= base_url(); ?>/Ajax/post_status",
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
                url: "<?= base_url(); ?>/Ajax/delete_post",
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