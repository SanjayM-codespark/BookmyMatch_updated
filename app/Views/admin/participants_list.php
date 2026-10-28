<?= $layout = session()->get('role_id') == 1 ? 'admin_base' : 'organizer_base'; ?>
<?= $this->extend($layout); ?>

<?= $this->section('pageCss'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
/* Payment status badges */
.pay-badge {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 5px 18px; border-radius: 20px; font-size: 12px; font-weight: 600;
    cursor: pointer; border: none; font-family: 'Montserrat', sans-serif;
    min-width: 90px;
}
.pay-badge-notpaid { background: #0BC5EA; color: #fff; }
.pay-badge-notpaid:hover { background: #0AA9C8; }
.pay-badge-paid    { background: #38A169; color: #fff; }
.pay-badge-paid:hover { background: #2F8A57; }

/* DataTables overrides */
#participantsTable_wrapper .dataTables_length label,
#participantsTable_wrapper .dataTables_filter label { font-size: 13px; font-weight: 500; color: #374151; }
#participantsTable_wrapper .dataTables_length select,
#participantsTable_wrapper .dataTables_filter input {
    border: 1px solid #E2E8F0; border-radius: 8px; padding: 5px 10px;
    font-size: 13px; font-family: 'Montserrat', sans-serif;
}
#participantsTable_wrapper .dataTables_filter input:focus { outline: none; border-color: #E53E3E; box-shadow: 0 0 0 3px rgba(229,62,62,.1); }
#participantsTable_wrapper .dataTables_info { font-size: 13px; color: #6B7280; }
#participantsTable_wrapper .dataTables_paginate .paginate_button { border-radius: 6px !important; font-size: 13px; }
#participantsTable_wrapper .dataTables_paginate .paginate_button.current,
#participantsTable_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #E53E3E !important; border-color: #E53E3E !important; color: #fff !important;
}
#participantsTable_wrapper .dataTables_paginate .paginate_button:hover {
    background: #FEE2E2 !important; border-color: #FECACA !important; color: #E53E3E !important;
}
</style>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<?php if (session()->has('success_message')): ?>
    <div class="alert alert-success alert-dismissible mb-3">
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        <?= session('success_message') ?>
    </div>
<?php endif ?>

<div class="page-card">
    <div class="page-card-header">
        <span class="page-card-title">
            <?= $is_team_event ? 'Teams &amp; Members' : 'List of Participants' ?>
        </span>
        <?php if (!empty($event->event_name)): ?>
            <span style="font-size:13px;color:#6B7280;"><?= esc($event->event_name) ?></span>
        <?php endif ?>
    </div>

    <?php if ($is_team_event): ?>
    <!-- ── TEAM EVENT VIEW ── -->
    <?php if (!empty($teams)): ?>
        <?php foreach ($teams as $team): ?>
        <div style="border:1px solid #E5E7EB;border-radius:10px;margin-bottom:18px;overflow:hidden;">
            <div style="background:#1E3A5F;color:#fff;padding:10px 18px;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-weight:700;font-size:14px;">
                    <i class="fa-solid fa-users" style="margin-right:6px;"></i>
                    <?= esc($team->team_name) ?>
                </span>
                <span style="font-size:12px;opacity:.85;">
                    <?= count($team->members) ?> / <?= $event->listing_event_max_players ?? '?' ?> members &nbsp;|&nbsp;
                    <span style="background:<?= $team->team_status === 'full' ? '#22C55E' : '#F59E0B' ?>;color:#fff;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">
                        <?= ucfirst($team->team_status) ?>
                    </span>
                </span>
            </div>
            <div class="org-table-wrap" style="margin:0;">
                <table class="org-table" style="width:100%;margin:0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($team->members)): ?>
                            <?php foreach ($team->members as $i => $m): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <?= esc($m->tm_name) ?>
                                    <?php if ((int)$m->tm_user_id === (int)$team->team_leader_id): ?>
                                        <span style="background:#EFF6FF;color:#2563EB;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;margin-left:5px;">Leader</span>
                                    <?php endif ?>
                                </td>
                                <td><?= esc($m->tm_email ?? '—') ?></td>
                                <td><?= esc($m->tm_mobile ?? '—') ?></td>
                                <td><?= ucfirst($m->tm_added_by ?? 'member') ?></td>
                            </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;color:#9CA3AF;padding:14px;">No accepted members yet</td></tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-users" style="font-size:40px;color:#E2E8F0;display:block;margin-bottom:12px;"></i>
            <p style="color:#94A3B8;font-size:14px;">No teams have registered for this event yet.</p>
        </div>
    <?php endif ?>

    <?php else: ?>
    <!-- ── INDIVIDUAL EVENT VIEW ── -->
    <div class="org-table-wrap">
        <table class="org-table" id="participantsTable" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Event</th>
                    <th>Fee</th>
                    <th>Mode of Payment</th>
                    <th>Payment Status</th>
                    <?php if (session()->get('role_id') != 1): ?>
                        <th>Action</th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($participants)): ?>
                    <?php foreach ($participants as $row): ?>
                        <tr>
                            <td><?= $row->participant_id ?></td>
                            <td><?= esc($row->participant_name) ?></td>
                            <td><?= esc($row->participant_email) ?></td>
                            <td><?= esc($row->participant_mobile) ?></td>
                            <td><?= esc($event->event_name ?? '') ?></td>
                            <td><?= esc($event->listing_event_fee ?? '') ?></td>
                            <td>
                                <?php if (!empty($row->mode)): ?>
                                    <span class="badge-status badge-upi"><?= esc($row->mode) ?></span>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($row->participant_pay_status === '0' || is_null($row->participant_pay_status)): ?>
                                    <button class="pay-badge pay-badge-notpaid"
                                            onclick="showPaymentModal(<?= $row->participant_id ?>)">
                                        Not Paid
                                    </button>
                                <?php else: ?>
                                    <button class="pay-badge pay-badge-paid"
                                            onclick="togglePayStatus(<?= $row->participant_id ?>, 0)">
                                        Paid
                                    </button>
                                <?php endif ?>
                            </td>
                            <?php if (session()->get('role_id') != 1): ?>
                                <td>
                                    <a href="<?= site_url('organizer/create_match') ?>"
                                       class="btn-tbl btn-tbl-action">
                                        <i class="fa-solid fa-calendar-check"></i> Schedule Match
                                    </a>
                                </td>
                            <?php endif ?>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>
    </div>
    <?php endif ?>
</div>

<!-- Payment Mode Modal (Bootstrap 4) -->
<div class="modal fade" id="paymentModeModal" tabindex="-1" role="dialog" aria-labelledby="paymentModeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <form id="paymentModeForm">
                <div class="modal-header" style="border-bottom:1px solid #F1F5F9;padding:20px 24px 16px;">
                    <h5 class="modal-title" id="paymentModeLabel"
                        style="font-weight:700;font-size:16px;color:#0F172A;">
                        Select Payment Mode
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <input type="hidden" id="participantIdInput" name="participant_id">
                    <div class="d-flex gap-3" style="gap:12px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:14px;font-weight:500;
                               cursor:pointer;padding:12px 20px;border:1px solid #E2E8F0;border-radius:8px;flex:1;">
                            <input type="radio" name="mode" value="UPI" required style="position:static;margin:0;flex-shrink:0;">
                            UPI
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;font-size:14px;font-weight:500;
                               cursor:pointer;padding:12px 20px;border:1px solid #E2E8F0;border-radius:8px;flex:1;">
                            <input type="radio" name="mode" value="Cash" required style="position:static;margin:0;flex-shrink:0;">
                            Cash
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #F1F5F9;padding:16px 24px;">
                    <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-submit-main">
                        <i class="fa-solid fa-check"></i> Mark as Paid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function () {
    if ($('#participantsTable').length) $('#participantsTable').DataTable({
        paging: true,
        ordering: true,
        info: true,
        order: [[0, 'desc']],
        language: {
            search: '',
            searchPlaceholder: 'Search...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: '',
            paginate: { previous: 'Previous', next: 'Next' },
            emptyTable: '<div class="empty-state"><i class="fa-solid fa-users" style="font-size:40px;color:#E2E8F0;display:block;margin-bottom:12px;"></i><p style="color:#94A3B8;font-size:14px;">No participants found for this event.</p></div>'
        }
    });
});

function showPaymentModal(participantId) {
    $('#participantIdInput').val(participantId);
    $('input[name="mode"]').prop('checked', false);
    $('#paymentModeModal').modal('show');
}

$('#paymentModeForm').on('submit', function (e) {
    e.preventDefault();
    const selectedMode = $('input[name="mode"]:checked').val();
    if (!selectedMode) { alert('Please select a payment mode.'); return; }
    $.ajax({
        url: "<?= base_url() ?>/Ajax/change_participant_paystatus",
        type: 'POST',
        data: {
            participant_id: $('#participantIdInput').val(),
            participant_pay_status: 1,
            mode: selectedMode
        },
        success: function (response) {
            $('#paymentModeModal').modal('hide');
            location.reload();
        },
        error: function () { alert('Error updating payment status.'); }
    });
});

function togglePayStatus(participantId, newStatus) {
    var label = newStatus === 1 ? 'mark as Paid' : 'mark as Not Paid';
    if (confirm('Are you sure you want to ' + label + '?')) {
        $.ajax({
            url: "<?= base_url() ?>/Ajax/change_participant_paystatus",
            type: 'POST',
            data: { participant_id: participantId, participant_pay_status: newStatus },
            success: function () { location.reload(); },
            error: function () { alert('Failed to update payment status.'); }
        });
    }
}
</script>
<?= $this->endSection(); ?>
