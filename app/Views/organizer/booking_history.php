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
    <form method="get" action="<?= current_url() ?>">
        <div class="filter-bar">
            <select class="form-control custom-select" name="status" style="min-width:160px;">
                <option value="">Select Status</option>
                <option value="upcoming"  <?= (!empty($_GET['status']) && $_GET['status'] == 'upcoming')  ? 'selected' : '' ?>>Live &amp; Upcoming</option>
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
                    <th>Tournament</th>
                    <th>Sports</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>City</th>
                    <th>Address</th>
                    <th>Events</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tournaments as $row) : ?>
                <tr>
                    <td><?= $row->listing_id ?></td>
                    <td style="min-width:140px;"><?= esc($row->listing_title) ?></td>
                    <td><?= esc($row->sports_name) ?></td>
                    <td style="white-space:nowrap;"><?= date('d-m-Y h:i a', strtotime($row->listing_start_date)) ?></td>
                    <td style="white-space:nowrap;"><?= date('d-m-Y h:i a', strtotime($row->listing_end_date)) ?></td>
                    <td><?= esc($row->city_name) ?></td>
                    <td style="min-width:200px;"><?= esc($row->listing_address) ?></td>
                    <td>
                        <a class="btn-tbl btn-tbl-info" href="<?= base_url('view_events/' . $row->listing_id) ?>">
                            <i class="fa-regular fa-calendar-days"></i> View
                        </a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;"><?= $pager ?></div>
    <?php else : ?>
    <div class="empty-state">
        <i class="fa-regular fa-calendar-check"></i>
        <p>No booking records found.</p>
    </div>
    <?php endif ?>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<style>
.pagination .page-link { border-radius:6px; color:#2563EB; border:1px solid #E5E7EB; font-size:13px; }
.pagination .page-item.active .page-link { background:#2563EB; border-color:#2563EB; color:#fff; }
</style>
<?= $this->endSection(); ?>
