<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<link href="<?= base_url() ?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section>
    <div class="container-fluid">
        <?php if (session()->has('errors')) : ?>
            <ul class="alert alert-danger">
                <?php foreach (session('errors') as $error) : ?>
                    <li><?= $error ?></li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
        <div class="row">
            <div class="col-md-6 col-6">
                <h4 class="text-black mt-3">List Of Players</h4>
            </div>
        </div>
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
                    <table class="dash-table table mb-0" id="participantsTable">
                        <thead style="background: #fff">
                            <tr class="text-center">
                                <th scope="col" class="tablehead">ID</th>
                                <th scope="col" class="tablehead">Player Name</th>
                                <th scope="col" class="tablehead">Team Name</th>
                                <th scope="col" class="tablehead">Payment Active</th>
                                <th scope="col" class="tablehead">Action</th>
                            </tr>
                        </thead>

                        <tbody style="background: #fff">
                            <?php if (!empty($userdata)) : ?>
                                <?php foreach ($userdata as $data) : ?>
                                    <tr>
                                        <td class="tablehead1"><?= $data->participant_id; ?></td>
                                        <td class="tablehead1"><?= $data->participant_name; ?></td>
                                        <td class="tablehead1"><?= $data->team_name; ?></td>
                                        <td class="tablehead1"><?= $data->payment_active == 1 ? 'Paid' : 'Not Paid'; ?></td>
                                        <td class="tablehead1">
                                            <a href="<?= site_url('organizer/create_match'); ?>" class="mb-2 btn btn-primary">Add Match</a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5">No data found</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#participantsTable').DataTable({
            paging: true,
            ordering: true,
            info: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search"
            },
        });

        $('#participantSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
<style>
    .pagination li a {
        color: #007bff !important;
    }
    .page-item.active .page-link {
        color: #fff !important;
    }
</style>
<?= $this->endSection(); ?>