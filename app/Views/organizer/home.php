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

<?php if (!empty($tournaments) && empty(session()->get('user_active'))) : ?>
    <div style="display:flex;align-items:center;gap:10px;background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:14px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        Your account is not verified. Your tournament events are not published yet.
    </div>
<?php endif ?>

<div class="page-card">
    <!-- Filters -->
    <form method="get" action="<?= current_url() ?>">
        <div class="filter-bar">
            <select class="form-control custom-select" name="status" style="min-width:160px;">
                <option value="">Select Status</option>
                <option value="upcoming" <?= (!empty($_GET['status']) && $_GET['status'] == 'upcoming') ? 'selected' : '' ?>>Live &amp; Upcoming</option>
                <option value="completed" <?= (!empty($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : '' ?>>Recently Completed</option>
            </select>
            <select class="form-control custom-select" name="sports" style="min-width:160px;">
                <option value="">Select Sports</option>
                <?php if (!empty($sports)) foreach ($sports as $vt) : ?>
                    <option value="<?= $vt->sports_id ?>" <?= (!empty($_GET['sports']) && $_GET['sports'] == $vt->sports_id) ? 'selected' : '' ?>>
                        <?= esc(ucfirst($vt->sports_name)) ?>
                    </option>
                <?php endforeach ?>
            </select>
            <input type="text" name="query" class="form-control" placeholder="Search.."
                   value="<?= esc($_GET['query'] ?? '') ?>" style="min-width:220px;">
            <button class="btn-filter" type="submit">
                <i class="fa-solid fa-magnifying-glass" style="margin-right:5px;"></i>Filter
            </button>
        </div>
    </form>

    <?php if (!empty($tournaments)) : ?>
    <div class="org-table-wrap">
        <table class="org-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Sports</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>City</th>
                    <th>Address</th>
                    <th>Events</th>
                    <th>Report</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tournaments as $row) :
                    $now     = time();
                    $startTs = strtotime($row->listing_start_date);
                    $endTs   = strtotime($row->listing_end_date);
                    if ($row->listing_cancel == 1) {
                        $statusLabel = 'Cancelled'; $statusClass = 'badge-cancelled';
                    } elseif ($startTs < $now && $endTs > $now) {
                        $statusLabel = 'Live';      $statusClass = 'badge-live';
                    } elseif ($startTs < $now) {
                        $statusLabel = 'Completed'; $statusClass = 'badge-completed';
                    } else {
                        $statusLabel = 'Upcoming';  $statusClass = 'badge-upcoming';
                    }
                ?>
                <tr>
                    <td><?= $row->listing_id ?></td>
                    <td style="min-width:140px;"><?= esc($row->listing_title) ?></td>
                    <td><?= esc($row->sports_name) ?></td>
                    <td style="white-space:nowrap;"><?= date('d-m-Y h:i a', $startTs) ?></td>
                    <td style="white-space:nowrap;"><?= date('d-m-Y h:i a', $endTs) ?></td>
                    <td><?= esc($row->city_name) ?></td>
                    <td style="min-width:200px;max-width:260px;"><?= esc($row->listing_address) ?></td>
                    <td>
                        <a class="btn-tbl btn-tbl-info" href="<?= base_url('view_events/' . $row->listing_id) ?>">
                            <i class="fa-regular fa-calendar-days"></i> View
                        </a>
                    </td>
                    <td>
                        <a class="btn-tbl btn-tbl-info" href="<?= base_url('reports/' . $row->listing_id) ?>">
                            <i class="fa-solid fa-chart-bar"></i> View
                        </a>
                    </td>
                    <td><span class="badge-status <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                    <td style="white-space:nowrap;">
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            <a class="btn-tbl btn-tbl-edit" href="<?= base_url('edit_tournament/' . $row->listing_id) ?>">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <?php if ($row->listing_cancel == 0 && $statusLabel !== 'Completed') : ?>
                            <a class="btn-tbl btn-tbl-cancel" href="javascript:void(0)"
                               onclick="cancelTournament(<?= $row->listing_id ?>)">
                                <i class="fa-solid fa-ban"></i> Cancel
                            </a>
                            <?php endif ?>
                            <a class="btn-tbl btn-tbl-delete" href="javascript:void(0)"
                               onclick="deleteTournament('<?= $row->listing_id ?>')">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;"><?= $pager ?></div>
    <?php else : ?>
    <div class="empty-state">
        <i class="fa-solid fa-trophy"></i>
        <p>No tournaments found. <a href="<?= base_url('create_tournament') ?>" style="color:#2563EB;">Create one →</a></p>
    </div>
    <?php endif ?>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #E5E7EB;">
                <h6 class="modal-title" style="font-weight:600;font-family:'Montserrat',sans-serif;">Cancel Tournament</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <label style="font-size:13px;font-weight:500;color:#374151;">Remarks <span class="text-danger">*</span></label>
                <input type="text" class="form-control mt-1" id="cancel-remarks" placeholder="Enter reason...">
                <input type="hidden" id="cancel-tourney-id">
            </div>
            <div class="modal-footer" style="border-top:1px solid #E5E7EB;">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm" style="background:#2563EB;color:#fff;border-radius:6px;padding:6px 18px;"
                        onclick="submitCancelTournament()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script>
function deleteTournament(id) {
    if (!confirm('Are you sure you want to delete this tournament?')) return;
    $.post('<?= base_url('Ajax/delete_tournament') ?>', {event_id: id}, function(r) {
        var o = $.parseJSON(r);
        alert(o.message);
        if (o.success === 'success') location.reload();
    }).fail(function(){ alert('Something went wrong.'); });
}
function cancelTournament(id) {
    $('#cancel-tourney-id').val(id);
    $('#cancel-remarks').val('');
    $('#cancelModal').modal('show');
}
function submitCancelTournament() {
    var remarks = $('#cancel-remarks').val().trim();
    var id      = $('#cancel-tourney-id').val();
    if (!remarks) { alert('Remarks are required.'); return; }
    $.post('<?= base_url('Ajax/cancel_tournament') ?>', {id: id, remarks: remarks}, function(r) {
        var o = $.parseJSON(r);
        $('#cancelModal').modal('hide');
        alert(o.message);
        if (o.success === 'success') location.reload();
    }).fail(function(){ alert('Something went wrong.'); });
}
</script>
<style>
.pagination .page-link { border-radius:6px; color:#2563EB; border:1px solid #E5E7EB; font-size:13px; }
.pagination .page-item.active .page-link { background:#2563EB; border-color:#2563EB; color:#fff; }
</style>
<?= $this->endSection(); ?>
