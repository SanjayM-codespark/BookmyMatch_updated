<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
#bookingTable_wrapper .dataTables_length label,
#bookingTable_wrapper .dataTables_filter label { font-size:13px;font-weight:500;color:#374151; }
#bookingTable_wrapper .dataTables_length select,
#bookingTable_wrapper .dataTables_filter input {
    border:1px solid #E2E8F0;border-radius:8px;padding:5px 10px;
    font-size:13px;font-family:'Montserrat',sans-serif;
}
#bookingTable_wrapper .dataTables_filter input:focus { outline:none;border-color:#E53E3E;box-shadow:0 0 0 3px rgba(229,62,62,.1); }
#bookingTable_wrapper .dataTables_info { font-size:13px;color:#6B7280; }
#bookingTable_wrapper .dataTables_paginate .paginate_button { border-radius:6px !important;font-size:13px; }
#bookingTable_wrapper .dataTables_paginate .paginate_button.current,
#bookingTable_wrapper .dataTables_paginate .paginate_button.current:hover {
    background:#E53E3E !important;border-color:#E53E3E !important;color:#fff !important;
}
#bookingTable_wrapper .dataTables_paginate .paginate_button:hover {
    background:#FEE2E2 !important;border-color:#FECACA !important;color:#E53E3E !important;
}
.amt-badge {
    display:inline-block;padding:3px 12px;background:#F0FFF4;color:#276749;
    border:1px solid #9AE6B4;border-radius:20px;font-size:12px;font-weight:600;
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

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="get" action="<?= current_url() ?>" class="d-flex flex-wrap align-items-end" style="gap:12px;">
        <div>
            <label class="filter-label">Organizer</label>
            <select name="organizer" class="filter-select">
                <option value="">All Organizers</option>
                <?php if (!empty($organizers)): ?>
                    <?php foreach ($organizers as $org): ?>
                        <option value="<?= $org->user_id ?>"
                            <?= ($_GET['organizer'] ?? '') == $org->user_id ? 'selected' : '' ?>>
                            <?= esc($org->user_full_name ?? 'Organizer #' . $org->user_id) ?>
                        </option>
                    <?php endforeach ?>
                <?php endif ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn-tbl btn-tbl-action" style="height:38px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <?php if (!empty($_GET['organizer'])): ?>
                <a href="<?= current_url() ?>" class="btn-tbl btn-tbl-edit" style="height:38px;margin-left:6px;">
                    <i class="fa-solid fa-xmark"></i> Clear
                </a>
            <?php endif ?>
        </div>
    </form>
</div>

<div class="page-card">
    <div class="page-card-header">
        <span class="page-card-title">
            Payment Records
            <?php if (!empty($total)): ?>
                <span style="font-size:13px;font-weight:500;color:#6B7280;margin-left:8px;">(<?= number_format($total) ?> total)</span>
            <?php endif ?>
        </span>
    </div>

    <div class="org-table-wrap">
        <table class="org-table" id="bookingTable" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tournament</th>
                    <th>Event</th>
                    <th>Organizer</th>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $row): ?>
                        <?php
                            $listing_event = $commonModel->select('listing_events', '*', ['listing_event_id' => $row->payment_event_id ?? 0]);
                            $event_name = '';
                            if (!empty($listing_event)) {
                                $ev = $commonModel->select('events', '*', ['event_id' => $listing_event->listing_event_title]);
                                $event_name = $ev->event_name ?? '';
                            }
                        ?>
                        <tr>
                            <td><?= $row->payment_id ?></td>
                            <td><?= esc($row->listing_title ?? '—') ?></td>
                            <td><?= esc($event_name ?: '—') ?></td>
                            <td><?= esc($row->user_full_name ?? '—') ?></td>
                            <td>
                                <?php if (!empty($row->payment_txn_id)): ?>
                                    <span style="font-family:monospace;font-size:12px;color:#374151;">
                                        <?= esc($row->payment_txn_id) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:#94A3B8;">—</span>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if (!empty($row->payment_amount)): ?>
                                    <span class="amt-badge">&#8377; <?= number_format((float)$row->payment_amount, 2) ?></span>
                                <?php else: ?>
                                    <span style="color:#94A3B8;">—</span>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if (!empty($row->payment_date)): ?>
                                    <?= date('d M Y', strtotime($row->payment_date)) ?>
                                <?php else: ?>
                                    <span style="color:#94A3B8;">—</span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
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
$(document).ready(function () {
    $('#bookingTable').DataTable({
        paging: true,
        ordering: true,
        info: true,
        order: [[0, 'desc']],
        language: {
            search: '',
            searchPlaceholder: 'Search...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ records',
            infoEmpty: '',
            paginate: { previous: 'Previous', next: 'Next' },
            emptyTable: '<div class="empty-state"><i class="fa-solid fa-receipt" style="font-size:40px;color:#E2E8F0;display:block;margin-bottom:12px;"></i><p style="color:#94A3B8;font-size:14px;margin:0;">No payment records found.</p></div>'
        }
    });
});
</script>
<?= $this->endSection(); ?>
