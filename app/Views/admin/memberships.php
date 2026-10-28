<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<style>
.stat-card { border-radius:8px; padding:16px 20px; color:#fff; }
.stat-card h3 { font-size:2rem; font-weight:700; margin:0; }
.stat-card small { font-size:.8rem; opacity:.85; }
.pkg-row td { vertical-align:middle; }
.badge-active    { background:#28a745; color:#fff; }
.badge-expired   { background:#ffc107; color:#222; }
.badge-cancelled { background:#dc3545; color:#fff; }
.badge-renewal   { background:#17a2b8; color:#fff; }
.badge-new       { background:#6c757d; color:#fff; }
.month-bar { height:10px; background:#E71A2D; border-radius:4px; display:inline-block; }
</style>
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>

<section>
    <div class="container-fluid">
        <div class="row" style="background:#CAD6F8;">
            <div class="col-md-6 m-auto">
                <h4 class="p-3 mb-0 dash-head1">Memberships & Earnings</h4>
            </div>
            <div class="col-md-6 d-flex justify-content-end m-auto">
                <a href="<?= base_url('admin/packages') ?>" class="btn btn-primary mx-3 my-2">Manage Packages</a>
                <a href="<?= base_url() ?>" class="mx-4 dash-link1 my-3">Home</a>
            </div>
        </div>
    </div>
</section>

<section class="p-3">
    <div class="container-fluid">

        <!-- ── STAT CARDS ── -->
        <div class="row mb-4">
            <div class="col-md-2 col-6 mb-2">
                <div class="stat-card" style="background:#17a2b8;">
                    <h3>₹<?= number_format($stats->total_revenue ?? 0, 0) ?></h3>
                    <small>Total Revenue</small>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <div class="stat-card" style="background:#28a745;">
                    <h3><?= $stats->active ?? 0 ?></h3>
                    <small>Active Members</small>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <div class="stat-card" style="background:#6c757d;">
                    <h3><?= $stats->total ?? 0 ?></h3>
                    <small>Total Purchases</small>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <div class="stat-card" style="background:#ffc107; color:#222;">
                    <h3><?= $stats->expired ?? 0 ?></h3>
                    <small>Expired</small>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <div class="stat-card" style="background:#dc3545;">
                    <h3><?= $stats->cancelled ?? 0 ?></h3>
                    <small>Cancelled</small>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <div class="stat-card" style="background:#E71A2D;">
                    <h3><?= $stats->paid_active ?? 0 ?></h3>
                    <small>Paid & Active</small>
                </div>
            </div>
        </div>

        <!-- ── RENEWAL VS NEW STAT ROW ── -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-2">
                <div class="stat-card" style="background:#17a2b8;">
                    <h3><?= $renewal_stats->renewals ?? 0 ?></h3>
                    <small>Renewals</small>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="stat-card" style="background:#6c757d;">
                    <h3><?= $renewal_stats->new_purchases ?? 0 ?></h3>
                    <small>New Purchases</small>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- ── REVENUE PER PACKAGE ── -->
            <div class="col-md-5 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold">Revenue by Package</h6>
                        <?php if (!empty($package_revenue)) : ?>
                            <?php
                            $maxRev = max(array_column((array) $package_revenue, 'revenue') ?: [1]);
                            ?>
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Package</th><th>Buyers</th><th>Active</th><th>Revenue</th></tr></thead>
                                <tbody>
                                <?php foreach ($package_revenue as $pr) : ?>
                                    <tr class="pkg-row">
                                        <td>
                                            <strong><?= esc($pr->package_name) ?></strong><br>
                                            <span class="badge badge-info" style="font-size:.7rem;"><?= ucfirst($pr->package_duration_type) ?></span>
                                            <?php $pct = $maxRev > 0 ? round(($pr->revenue / $maxRev) * 100) : 0; ?>
                                            <div class="mt-1"><span class="month-bar" style="width:<?= $pct ?>%;min-width:4px;"></span></div>
                                        </td>
                                        <td class="text-center"><?= $pr->total_buyers ?></td>
                                        <td class="text-center"><span class="text-success font-weight-bold"><?= $pr->active_count ?></span></td>
                                        <td class="text-right font-weight-bold">₹<?= number_format($pr->revenue, 0) ?></td>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p class="text-muted">No purchases yet.</p>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <!-- ── LAST 6 MONTHS ── -->
            <div class="col-md-7 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold">Monthly Earnings (Last 6 Months)</h6>
                        <?php if (!empty($monthly_revenue)) : ?>
                            <?php $maxMonth = max(array_column((array) $monthly_revenue, 'revenue') ?: [1]); ?>
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Month</th><th>Purchases</th><th>Revenue</th><th>Bar</th></tr></thead>
                                <tbody>
                                <?php foreach ($monthly_revenue as $mr) : ?>
                                    <tr>
                                        <td><strong><?= $mr->month_label ?></strong></td>
                                        <td class="text-center"><?= $mr->purchases ?></td>
                                        <td class="text-right font-weight-bold text-success">₹<?= number_format($mr->revenue, 0) ?></td>
                                        <td style="width:40%;">
                                            <?php $pct = $maxMonth > 0 ? round(($mr->revenue / $maxMonth) * 100) : 0; ?>
                                            <span class="month-bar" style="width:<?= max($pct, 4) ?>%;"></span>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p class="text-muted">No purchases in last 6 months.</p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── FILTERS ── -->
        <div class="card mb-3">
            <div class="card-body p-3">
                <form method="get" action="<?= current_url() ?>">
                    <div class="row align-items-end">
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <label class="small">Package</label>
                            <select name="package_id" class="form-control form-control-sm">
                                <option value="">All Packages</option>
                                <?php if (!empty($packages)) foreach ($packages as $p) : ?>
                                    <option value="<?= $p->package_id ?>"
                                        <?= (!empty($_GET['package_id']) && $_GET['package_id'] == $p->package_id) ? 'selected' : '' ?>>
                                        <?= esc($p->package_name) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <label class="small">Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All Statuses</option>
                                <option value="active"    <?= (!empty($_GET['status']) && $_GET['status'] == 'active')    ? 'selected' : '' ?>>Active</option>
                                <option value="expired"   <?= (!empty($_GET['status']) && $_GET['status'] == 'expired')   ? 'selected' : '' ?>>Expired</option>
                                <option value="cancelled" <?= (!empty($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <label class="small">Type</label>
                            <select name="type" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <option value="new"     <?= (!empty($_GET['type']) && $_GET['type'] == 'new')     ? 'selected' : '' ?>>New</option>
                                <option value="renewal" <?= (!empty($_GET['type']) && $_GET['type'] == 'renewal') ? 'selected' : '' ?>>Renewal</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <label class="small">From Date</label>
                            <input type="date" name="from" class="form-control form-control-sm"
                                value="<?= !empty($_GET['from']) ? $_GET['from'] : '' ?>">
                        </div>
                        <div class="col-md-2 col-sm-6 col-12 mb-2">
                            <label class="small">To Date</label>
                            <input type="date" name="to" class="form-control form-control-sm"
                                value="<?= !empty($_GET['to']) ? $_GET['to'] : '' ?>">
                        </div>
                        <div class="col-md-2 col-sm-12 col-12 mb-2">
                            <button type="submit" class="btn btn-primary btn-sm mr-1">Filter</button>
                            <a href="<?= base_url('admin/memberships') ?>" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── BUYERS TABLE ── -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                    <h6 class="font-weight-bold mb-0">All Buyers
                        <span class="text-muted" style="font-size:.85rem;">(<?= count($memberships ?? []) ?> records)</span>
                    </h6>
                    <div class="mt-2 mt-md-0">
                        <a href="<?= base_url('admin/memberships/export') . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '') ?>"
                           class="btn btn-sm btn-outline-success">Export Filtered</a>
                        <a href="<?= base_url('admin/memberships/export/all') ?>" class="btn btn-sm btn-success">Export All</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="membershipsTable" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Package</th>
                                <th>Duration</th>
                                <th>Type</th>
                                <th>Price Paid</th>
                                <th>Start</th>
                                <th>Expires</th>
                                <th>Payment Ref</th>
                                <th>Purchased On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($memberships ?? [] as $i => $m) : ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><strong><?= esc($m->user_full_name ?? '—') ?></strong></td>
                                        <td><?= esc($m->user_mobile ?? '—') ?></td>
                                        <td><small><?= esc($m->user_email ?? '—') ?></small></td>
                                        <td><strong><?= esc($m->package_name ?? '—') ?></strong></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php
                                                $duration = [
                                                    'monthly' => 'Monthly',
                                                    '6month'  => '6 Months',
                                                    'yearly'  => 'Yearly',
                                                ];
                                        
                                                echo $duration[$m->package_duration_type] ?? ucfirst($m->package_duration_type);
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= !empty($m->is_renewal) ? 'badge-renewal' : 'badge-new' ?>">
                                                <?= !empty($m->is_renewal) ? 'Renewal' : 'New' ?>
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            <?= $m->membership_price_paid == 0
                                                ? '<span class="text-muted">Free</span>'
                                                : '<span class="text-success">₹' . number_format($m->membership_price_paid, 2) . '</span>' ?>
                                        </td>
                                        <td><?= !empty($m->membership_start_date) ? date('d M Y', strtotime($m->membership_start_date)) : '—' ?></td>
                                        <td>
                                            <?php if (!empty($m->membership_end_date)) : ?>
                                                <?php if (strtotime($m->membership_end_date) < time()) : ?>
                                                    <span class="text-danger">
                                                        <?= date('d M Y', strtotime($m->membership_end_date)) ?>
                                                    </span>
                                                <?php else : ?>
                                                    <?= date('d M Y', strtotime($m->membership_end_date)) ?>
                                                    <small class="text-muted d-block">
                                                        <?= ceil((strtotime($m->membership_end_date) - time()) / 86400) ?> days left
                                                    </small>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?= esc($m->membership_payment_ref ?? '—') ?></small></td>
                                        <td><small><?= !empty($m->membership_created_at) ? date('d M Y H:i', strtotime($m->membership_created_at)) : '—' ?></small></td>
                                        <td>
                                            <?php
                                            $s = $m->membership_status ?? 'active';
                                            $cls = ['active' => 'badge-active', 'expired' => 'badge-expired', 'cancelled' => 'badge-cancelled'];
                                            ?>
                                            <span class="badge <?= $cls[$s] ?? '' ?>"><?= ucfirst($s) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($m->membership_status === 'active') : ?>
                                                <a href="javascript:void(0)"
                                                   onclick="cancelMembership(<?= $m->membership_id ?>)"
                                                   class="btn btn-sm btn-danger">Cancel</a>
                                            <?php else : ?>
                                                <span class="text-muted">—</span>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#membershipsTable').DataTable({
        paging: true, ordering: true, info: true, pageLength: 25,
        order: [[11, 'desc']],
        language: { emptyTable: 'No memberships found.' }
    });
});

function cancelMembership(id) {
    if (!confirm('Cancel this membership? The user will lose access when their current period ends.')) return;
    $.post('<?= base_url('Ajax/cancel_membership') ?>', { membership_id: id }, function(res) {
        var r = jQuery.parseJSON(res);
        alert(r.message);
        if (r.success == 'success') location.reload();
    });
}
</script>
<?= $this->endSection(); ?>