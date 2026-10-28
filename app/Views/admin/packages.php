<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                <h4 class="p-3 mb-0 dash-head1">Packages</h4>
            </div>
            <div class="col-md-6 col-6 d-flex justify-content-end m-auto">
                <a href="<?= base_url(); ?>" class="mx-5 dash-link1">Home</a>
            </div>
        </div>
    </div>
</section>

<section class="p-4">
    <div class="row">
        <div class="col-lg-12">
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
                        <!-- ── ADD / EDIT FORM ── -->
                        <div class="col-lg-5 col-md-5">
                            <h5 class="card-title"><?= !empty($edit) ? 'Edit Package' : 'Add Package' ?></h5>
                            <form action="<?= current_url(); ?>" method="post">

                                <div class="form-group mb-2">
                                    <label>Package Name <span class="text-danger">*</span></label>
                                    <input type="text" name="package_name" class="form-control"
                                        value="<?= !empty($package->package_name) ? $package->package_name : set_value('package_name') ?>"
                                        placeholder="e.g. Pro Plan">
                                    <div class="text-danger"><?= session('errors.package_name') ?></div>
                                </div>

                                <div class="form-group mb-2">
                                    <label>Description</label>
                                    <textarea name="package_description" class="form-control" rows="2"
                                        placeholder="Short description shown to users"><?= !empty($package->package_description) ? $package->package_description : set_value('package_description') ?></textarea>
                                </div>

                                <div class="form-group mb-2">
                                    <label>Duration Type <span class="text-danger">*</span></label>
                                    <select name="package_duration_type" class="form-control" id="duration_type">
                                        <?php
                                        $dur = !empty($package->package_duration_type) ? $package->package_duration_type : set_value('package_duration_type');
                                        foreach ([
                                            'monthly' => 'Monthly (30 days)',
                                            '6month'  => '6 Months (180 days)',
                                            'yearly'  => 'Yearly (365 days)'
                                        ] as $val => $label) { ?>
                                            <option value="<?= $val ?>" <?= $dur == $val ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group mb-2">
                                    <label>Price (₹) <span class="text-danger">*</span> — 0 = Free</label>
                                    <input type="number" name="package_price" class="form-control" min="0" step="0.01"
                                        value="<?= !empty($package->package_price) ? $package->package_price : set_value('package_price', 0) ?>"
                                        placeholder="0 for free plan">
                                    <div class="text-danger"><?= session('errors.package_price') ?></div>
                                </div>

                                <div class="form-group mb-2">
                                    <label>Max Matches — 0 = Unlimited</label>
                                    <input type="number" name="package_max_matches" class="form-control" min="0"
                                        value="<?= isset($package->package_max_matches) ? $package->package_max_matches : set_value('package_max_matches', 0) ?>"
                                        placeholder="0 = unlimited">
                                </div>

                                <div class="form-group mb-2">
                                    <label>Max Tournaments — 0 = Unlimited</label>
                                    <input type="number" name="package_max_tournaments" class="form-control" min="0"
                                        value="<?= isset($package->package_max_tournaments) ? $package->package_max_tournaments : set_value('package_max_tournaments', 0) ?>"
                                        placeholder="0 = unlimited">
                                </div>

                                <div class="form-group mb-2">
                                    <label>Max Teams — 0 = Unlimited</label>
                                    <input type="number" name="package_max_teams" class="form-control" min="0"
                                        value="<?= isset($package->package_max_teams) ? $package->package_max_teams : set_value('package_max_teams', 0) ?>"
                                        placeholder="0 = unlimited">
                                </div>

                                <div class="form-group mb-2">
                                    <label>Sort Order</label>
                                    <input type="number" name="package_sort_order" class="form-control" min="0"
                                        value="<?= isset($package->package_sort_order) ? $package->package_sort_order : set_value('package_sort_order', 0) ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="d-block">Features / Permissions</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="package_can_create_tournament" value="1"
                                            <?= !empty($package->package_can_create_tournament) ? 'checked' : '' ?>>
                                        <label class="form-check-label">Can Create Tournament</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="package_priority_booking" value="1"
                                            <?= !empty($package->package_priority_booking) ? 'checked' : '' ?>>
                                        <label class="form-check-label">Priority Booking</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="package_is_featured" value="1"
                                            <?= !empty($package->package_is_featured) ? 'checked' : '' ?>>
                                        <label class="form-check-label">Featured (highlighted)</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-info">
                                    <?= !empty($edit) ? 'Update Package' : 'Add Package' ?>
                                </button>
                                <?php if (!empty($edit)) : ?>
                                    <a href="<?= base_url('admin/packages') ?>" class="btn btn-secondary ml-2">Cancel</a>
                                <?php endif ?>
                            </form>
                        </div>

                        <!-- ── PACKAGES LIST ── -->
                        <div class="col-lg-7 col-md-7">
                            <h5 class="card-title">All Packages</h5>
                            <?php if (!empty($packages)) : ?>
                                <div class="table-responsive">
                                    <table id="packagesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Duration</th>
                                                <th>Price</th>
                                                <th>Matches</th>
                                                <th>Features</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($packages as $i => $p) : ?>
                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td>
                                                        <?= esc($p->package_name) ?>
                                                        <?php if ($p->package_is_featured) : ?>
                                                            <span class="badge badge-warning ml-1">Featured</span>
                                                        <?php endif ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info"><?= ucfirst($p->package_duration_type) ?></span>
                                                    </td>
                                                    <td>
                                                        <?= $p->package_price == 0 ? '<span class="badge badge-success">Free</span>' : '₹' . number_format($p->package_price, 2) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= $p->package_max_matches == 0 ? '<span title="Unlimited">∞</span>' : $p->package_max_matches ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($p->package_can_create_tournament) echo '<span class="badge badge-primary mr-1">Create Tourney</span>'; ?>
                                                        <?php if ($p->package_priority_booking) echo '<span class="badge badge-secondary mr-1">Priority</span>'; ?>
                                                        <?php if ($p->package_max_tournaments == 0) echo '<span class="badge badge-light mr-1">∞ Tourneys</span>'; ?>
                                                        <?php if ($p->package_max_teams == 0) echo '<span class="badge badge-light">∞ Teams</span>'; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($p->package_active) : ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else : ?>
                                                            <span class="badge badge-danger">Inactive</span>
                                                        <?php endif ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('admin/edit_package/' . $p->package_id) ?>"
                                                           class="btn btn-sm btn-info">Edit</a>
                                                        &nbsp;
                                                        <a href="javascript:void(0)"
                                                           onclick="togglePackage(<?= $p->package_id ?>, <?= $p->package_active ?>)"
                                                           class="btn btn-sm <?= $p->package_active ? 'btn-warning' : 'btn-success' ?>">
                                                           <?= $p->package_active ? 'Disable' : 'Enable' ?>
                                                        </a>
                                                        &nbsp;
                                                        <a href="javascript:void(0)"
                                                           onclick="deletePackage(<?= $p->package_id ?>)"
                                                           class="btn btn-sm btn-danger">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <p class="text-muted">No packages yet. Add one using the form.</p>
                            <?php endif ?>
                        </div>
                    </div>

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
    $('#packagesTable').DataTable({ paging: true, ordering: true, info: true });
});

function deletePackage(id) {
    if (!confirm('Delete this package? Users with this membership will not be affected.')) return;
    $.post('<?= base_url('Ajax/delete_package') ?>', { package_id: id }, function(res) {
        var r = jQuery.parseJSON(res);
        alert(r.message);
        if (r.success == 'success') location.reload();
    });
}

function togglePackage(id, currentStatus) {
    var action = currentStatus == 1 ? 'disable' : 'enable';
    if (!confirm(action.charAt(0).toUpperCase() + action.slice(1) + ' this package?')) return;
    $.post('<?= base_url('Ajax/toggle_package') ?>', { package_id: id }, function(res) {
        var r = jQuery.parseJSON(res);
        alert(r.message);
        location.reload();
    });
}
</script>
<style>
.pagination li a { color: #007bff !important; }
.page-item.active .page-link { color: #fff !important; }
</style>
<?= $this->endSection(); ?>
