<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="sc-label"><i class="fa-solid fa-eye" style="margin-right:6px;color:#E53E3E;"></i> Total Visits</div>
        <div class="sc-value"><?= number_format($total_visit ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="sc-label"><i class="fa-solid fa-chart-line" style="margin-right:6px;color:#7C3AED;"></i> Page Views</div>
        <div class="sc-value"><?= number_format($total_visit ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="sc-label"><i class="fa-solid fa-users" style="margin-right:6px;color:#0891B2;"></i> Unique Visitors</div>
        <div class="sc-value"><?= number_format($unique ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="sc-label"><i class="fa-solid fa-trophy" style="margin-right:6px;color:#D97706;"></i> Tournaments</div>
        <div class="sc-value"><?= is_array($tournaments) || is_object($tournaments) ? count((array)$tournaments) : 0 ?></div>
    </div>
    <div class="stat-card">
        <div class="sc-label"><i class="fa-solid fa-user-check" style="margin-right:6px;color:#059669;"></i> Verified Organizers</div>
        <div class="sc-value"><?= $data2 ?? 0 ?></div>
    </div>
    <div class="stat-card">
        <div class="sc-label"><i class="fa-solid fa-user-clock" style="margin-right:6px;color:#E53E3E;"></i> Pending Organizers</div>
        <div class="sc-value"><?= $data1 ?? 0 ?></div>
    </div>
</div>

<!-- Quick Links -->
<div class="page-card">
    <div class="page-card-header">
        <span class="page-card-title">Quick Actions</span>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:10px;">
        <a href="<?= base_url('organizer_list/new') ?>" class="btn-tbl btn-tbl-action">
            <i class="fa-solid fa-user-plus"></i> New Organizers <?php if(!empty($data1)): ?>(<?= $data1 ?>)<?php endif ?>
        </a>
        <a href="<?= base_url('tournament_list') ?>" class="btn-tbl btn-tbl-edit">
            <i class="fa-solid fa-trophy"></i> All Tournaments
        </a>
        <a href="<?= base_url('admin/booking_history') ?>" class="btn-tbl btn-tbl-view">
            <i class="fa-solid fa-calendar-check"></i> Booking History
        </a>
        <a href="<?= base_url('admin/sports') ?>" class="btn-tbl btn-tbl-edit">
            <i class="fa-solid fa-baseball-bat-ball"></i> Manage Sports
        </a>
        <a href="<?= base_url('admin/packages') ?>" class="btn-tbl btn-tbl-edit">
            <i class="fa-solid fa-box-open"></i> Packages
        </a>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('pageScript'); ?>
<?= $this->endSection(); ?>
