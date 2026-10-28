<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<style>
    @media (max-width: 767px) {
        .org-filters .form-group {
            margin-bottom: 10px;
        }
        .org-filters .btn {
            width: 100%;
        }
        .team-header-actions {
            justify-content: flex-start !important;
            margin-top: 10px;
        }
        .team-header-actions .btn {
            width: 100%;
        }

        /* Card-style table on small screens */
        .org-table thead {
            display: none;
        }
        .org-table, .org-table tbody, .org-table tr, .org-table td {
            display: block;
            width: 100%;
        }
        .org-table tr {
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 8px;
        }
        .org-table td {
            text-align: right;
            padding: 8px 10px;
            border: none;
            border-bottom: 1px solid #f1f1f1;
        }
        .org-table td:last-child {
            border-bottom: none;
        }
        .org-table td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: #555;
        }
        .org-table td.no-data {
            text-align: center;
        }
        .org-table td.no-data::before {
            content: '';
        }
        /* DataTables controls stack on mobile */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: left !important;
            float: none !important;
            width: 100%;
            margin-bottom: 8px;
        }
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>

<section>
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 col-12">
                <h4 class="p-3 mb-0 dash-head1">Teams</h4>
            </div>
            <div class="col-md-6 col-12 d-flex justify-content-end team-header-actions">
                <a href="<?= site_url('organizer/create_team'); ?>" class="mb-2 btn btn-primary">Add Team</a>
            </div>
        </div>
    </div>
</section>

<?php if (session()->has('success_message')) : ?>
    <div class="alert alert-success mx-3">
        <?= session('success_message') ?>
        <?php session()->remove('success_message') ?>
    </div>
<?php endif ?>

<div class="card mb-5" style="margin: 23px;">
    <div class="p-3 org-filters">
        <div class="row">
            <div class="col-md-12">
                <label>Filters</label>
            </div>
        </div>
        <form method="get" action="<?= current_url(); ?>">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="form-group">
                        <select class="form-control" id="tournament" name="tournament">
                            <option value="">Select Tournament</option>
                            <?php
                            if (!empty($tournaments)) {
                                foreach ($tournaments as $mfr) { ?>
                                    <option value="<?= $mfr->listing_id; ?>" <?php echo (!empty($_GET['tournament']) && $_GET['tournament'] == $mfr->listing_id) ? " selected='selected'" : "" ?>><?= ucfirst($mfr->listing_title); ?></option>
                            <?php   }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="form-group">
                        <select class="form-control" id="event" name="event">
                            <option value="">Select Events</option>
                            <?php
                            if (!empty($events)) {
                                foreach ($events as $mfr) { ?>
                                    <option value="<?= $mfr->listing_event_id; ?>" <?php echo (!empty($_GET['event']) && $_GET['event'] == $mfr->listing_event_id) ? " selected='selected'" : "" ?>>
                                        <?= ucfirst($mfr->event_name ?? '') ?>
                                    </option>
                            <?php   }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="form-group">
                        <select class="form-control" id="group_name" name="group_name">
                            <option value="">Select Group</option>
                            <?php
                            if (!empty($tblgroups)) {
                                foreach ($tblgroups as $mfr) { ?>
                                    <option value="<?= $mfr->grp_id; ?>" <?php echo (!empty($_GET['group_name']) && $_GET['group_name'] == $mfr->grp_id) ? " selected='selected'" : "" ?>><?= ucfirst($mfr->grp_name); ?></option>
                            <?php   }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 col-12">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive-xl org-table-wrap p-1" style="overflow-x:auto;">
        <table id="myTable" class="table pay-table table-bordered table-striped org-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Team Name</th>
                    <th>Status</th>
                    <th>Members</th>
                    <th style="width:220px!important">Tournament Name</th>
                    <th>Event Name</th>
                    <th>Invite Link</th>
                    <th>Players</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($team_list)) {
                    foreach ($team_list as $data) { ?>
                        <tr>
                            <td data-label="ID">
                                <?php if (!empty($data->team_id)) {
                                    echo $data->team_id;
                                } ?>
                            </td>
                            <td data-label="Team Name"><?= !empty($data->team_name) ? $data->team_name : '' ?></td>
                            <td data-label="Status">
                                <?php
                                $status = !empty($data->team_status) ? $data->team_status : 'open';
                                $badge  = ['open' => 'success', 'full' => 'warning', 'locked' => 'danger'];
                                ?>
                                <span class="badge badge-<?= $badge[$status] ?? 'secondary' ?>"><?= ucfirst($status) ?></span>
                            </td>
                            <td data-label="Members">
                                <?php
                                $memberCount = !empty($data->member_count) ? $data->member_count : 0;
                                $maxPlayers  = !empty($data->team_max_players) ? $data->team_max_players : '?';
                                echo $memberCount . ' / ' . $maxPlayers;
                                ?>
                            </td>
                            <td data-label="Tournament Name"><?= !empty($data->listing_title) ? $data->listing_title : '' ?></td>
                            <td data-label="Event Name"><?= !empty($data->event_name) ? $data->event_name : '' ?></td>
                            <td data-label="Invite Link">
                                <?php if (!empty($data->team_join_token)) : ?>
                                    <button class="btn btn-sm btn-outline-secondary copy-link" data-token="<?= $data->team_join_token ?>">
                                        <i class="fa fa-copy"></i> Copy Link
                                    </button>
                                <?php else : ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Players">
                                <a href="<?= base_url(); ?>/organizer/player/<?= $data->team_id; ?>" class="btn btn-primary btn-sm">View</a>
                            </td>
                            <td data-label="Action">
                                <a onclick="delete_team('<?= !empty($data->team_id) ? $data->team_id : ''; ?>')"><i class="fa-solid fa-trash-can m-2" style="cursor:pointer;"></i></a>
                            </td>
                        </tr>
                <?php }
                } else { ?>
                    <tr>
                        <td colspan="9" class="no-data">No data found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script>
    $(document).on('click', '.copy-link', function() {
        var token = $(this).data('token');
        var link = '<?= base_url() ?>/team/join/' + token;
        navigator.clipboard.writeText(link).then(function() {
            alert('Invite link copied: ' + link);
        });
    });

    function delete_team(id) {
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this team?") == true) {
            $.ajax({
                url: "<?= base_url(); ?>/Ajax/delete_team",
                method: "POST",
                data: {
                    'team_id': id
                },
                success: function(response) {
                    var obje = jQuery.parseJSON(response);
                    if (obje.success == 'success') {
                        alert(obje['message']);
                        window.location.href = currenturl;
                    } else if (obje['failed'] == 'error') {
                        alert(obje['message']);
                    }
                },
                error: function(response) {
                    alert('Something went wrong. try again');
                }
            });
        }
    }

    $(document).on('change', '#tournament', function() {
        var cslug = $(this).val();
        $.ajax({
            type: "POST",
            data: {
                'tourney_id': cslug
            },
            url: "<?php echo base_url(); ?>/Organizer/get_ajax_org_events",
            success: function(html) {
                $('#event').html(html);
            }
        });
    });

    $(document).on('change', '#event', function() {
        var cslug = $(this).val();
        $.ajax({
            type: "POST",
            data: {
                'event_id': cslug
            },
            url: "<?php echo base_url(); ?>/Organizer/get_ajax_org_group",
            success: function(html) {
                $('#group_name').html(html);
            }
        });
    });
</script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true
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