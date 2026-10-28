<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<?php if (session()->has('success_message')) : ?>
    <div style="display:flex;align-items:center;gap:10px;background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:14px;">
        <i class="fa-solid fa-circle-check"></i>
        <?= session('success_message') ?><?php session()->remove('success_message') ?>
    </div>
<?php endif ?>

<div class="page-card">
    <div class="page-card-header">
        <span class="page-card-title"><?= esc($page_name ?? 'Umpires') ?></span>
        <?php if (session()->get('role_id') == 3) : ?>
            <a href="<?= site_url('organizer/add_umpire') ?>"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#2563EB;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                <i class="fa-solid fa-plus"></i> Add Umpire
            </a>
        <?php endif ?>
    </div>

    <div class="org-table-wrap">
        <table class="org-table" id="umpireTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Photo</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <?php if (session()->get('role_id') == 3) : ?><th>Action</th><?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($umpire_list)) foreach ($umpire_list as $row) : ?>
                <tr>
                    <td><?= $row->user_id ?? '' ?></td>
                    <td><?= esc($row->user_full_name ?? '') ?></td>
                    <td>
                        <img src="<?= base_url('public/uploads/umpires/' . ($row->user_profile_img ?? 'default.png')) ?>"
                             style="width:44px;height:44px;border-radius:50%;object-fit:cover;background:#E5E7EB;">
                    </td>
                    <td><?= esc($row->user_mobile ?? '') ?></td>
                    <td><?= esc($row->user_email ?? '') ?></td>
                    <?php if (session()->get('role_id') == 3) : ?>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a class="btn-tbl btn-tbl-edit" href="<?= base_url('organizer/edit_umpire/' . $row->user_id) ?>">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <a class="btn-tbl btn-tbl-delete" href="javascript:void(0)"
                               onclick="deleteUmpire('<?= $row->user_id ?>')">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </a>
                        </div>
                    </td>
                    <?php endif ?>
                </tr>
                <?php endforeach ?>
                <?php if (empty($umpire_list)) : ?>
                <tr><td colspan="6" style="text-align:center;padding:32px;color:#9CA3AF;">No umpires found.</td></tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function(){
    $('#umpireTable').DataTable({ paging:true, ordering:true, info:true, searching:true,
        language:{ search:'', searchPlaceholder:'Search...' }
    });
});
function deleteUmpire(id) {
    if (!confirm('Are you sure you want to delete this umpire?')) return;
    $.post('<?= base_url('Ajax/delete_umpire') ?>', {user_id: id}, function(r) {
        var o = $.parseJSON(r);
        alert(o.message);
        if (o.success === 'success') location.reload();
    }).fail(function(){ alert('Something went wrong.'); });
}
</script>
<style>
.pagination .page-link { border-radius:6px; color:#2563EB; font-size:13px; }
.pagination .page-item.active .page-link { background:#2563EB; border-color:#2563EB; color:#fff; }
</style>
<?= $this->endSection(); ?>
