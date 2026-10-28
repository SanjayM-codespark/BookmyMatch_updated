<?= $this->extend('admin_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<?php  ?>
<main class="org-dash-sec content">
    <section style="background-color: #CAD6F8;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-6">
                    <h5 class="p-3 dash-head1 mb-0">Participants</h5>
                </div>
            </div>
        </div>
    </section>
    <section>


        <div class="container-fluid">

            <div class="row">
                <div class="col-md-6 col-6">
                    <h4 class="text-black mt-3">List Of Participants</h4>
                </div>
            </div>
            <form method="post" action="<?= base_url('participant_detail'); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" id="tournament" name="tournament">
                                <option value="">Select User Name</option>
                                <?php
                                if (!empty($userdata)) {
                                    foreach ($userdata as $data) { ?>
                                        <option value="<?= $data->participant_id ?? ''; ?>"><?= ucfirst($data->participant_name ?? ''); ?></option>
                                <?php   }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card" style="overflow-x:auto;">
                    <?php if (session()->has('success_message')) : ?>
                        <div class="alert alert-success">
                            <?= session('success_message') ?>
                            <?php session()->remove('success_message') ?>
                        </div>
                    <?php endif ?>


                    <div class="table-responsive">
                        <table class="dash-table table mb-0">
                            <thead style="background: #fff">
                                <tr class="text-center">
                                    <th scope="col" class="tablehead">ID</th>
                                    <th scope="col" class="tablehead">Name</th>
                                    <th scope="col" class="tablehead">Email</th>
                                    <th scope="col" class="tablehead">Mobile</th>
                                    <th scope="col" class="tablehead">DOB</th>
                                    <th scope="col" class="tablehead">Team ID</th>
                                    <th scope="col" class="tablehead">Club</th>
                                   
                                </tr>
                            </thead>
                            <tbody style="background: #fff">
                                <?php if (!empty($userdata)) { ?>
                                    <?php foreach ($userdata as $data) { ?>
                                        <tr class="text-center">
                                            <td class="tablehead1"><?= $data->participant_id; ?></td>
                                            <td class="tablehead1"><?= esc($data->participant_name); ?></td>
                                            <td class="tablehead1"><?= esc($data->participant_email); ?></td>
                                            <td class="tablehead1"><?= esc($data->participant_mobile); ?></td>
                                            <td class="tablehead1"><?= esc($data->participant_dob); ?></td>
                                            <td class="tablehead1"><?= esc($data->team_name); ?></td>
                                            <td class="tablehead1"><?= esc($data->participant_club); ?></td>
                                         
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <h5>No data found</h5>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <?php if (!empty($pager['total_pages']) && $pager['total_pages'] > 1): ?>
                            <nav>
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $pager['total_pages']; $i++): ?>
                                        <li class="page-item <?= ($i == $pager['current_page']) ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= $pager['base_url'] . '?page=' . $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        </div>
    </section>
</main>
<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>

<?= $this->endSection(); ?>