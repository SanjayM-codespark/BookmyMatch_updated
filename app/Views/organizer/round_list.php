<?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<style>
    @media (max-width: 767px) {
        .org-filters .form-group {
            margin-bottom: 10px;
        }
        .org-filters .btn {
            width: 100%;
        }
        .round-header-actions {
            justify-content: flex-start !important;
            margin-top: 10px;
        }
        .round-header-actions .btn {
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
    }
</style>
<?=$this->endSection();?>
<?=$this->section('main_content');?>

<section>
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 col-12">
                <h4 class="p-3 mb-0 dash-head1">Rounds</h4>
            </div>
            <div class="col-md-6 col-12 d-flex justify-content-end round-header-actions">
                <a href="<?=site_url('organizer/create_round');?>" class="mb-2 btn btn-primary">Add Round</a>
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
        <form method="get" action="<?=current_url();?>">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="form-group">
                        <select class="form-control" id="tournament" name="tournament">
                            <option value="">Select Tournament</option>
                            <?php
                            if (!empty($tournaments)) {
                                foreach ($tournaments as $mfr) { ?>
                                    <option value="<?=$mfr->listing_id;?>" <?php echo (!empty($_GET['tournament']) && $_GET['tournament'] == $mfr->listing_id) ? " selected='selected'" : ""; ?>><?=ucfirst($mfr->listing_title);?></option>
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
                                    <option value="<?=$mfr->listing_event_id;?>" <?php echo (!empty($_GET['event']) && $_GET['event'] == $mfr->listing_event_id) ? " selected='selected'" : ""; ?>><?=ucfirst($mfr->event_name ?? '');?></option>
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
                                    <option value="<?=$mfr->grp_id;?>" <?php echo (!empty($_GET['group_name']) && $_GET['group_name'] == $mfr->grp_id) ? " selected='selected'" : ""; ?>><?=ucfirst($mfr->grp_name);?></option>
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
                    <th>Round Name</th>
                    <th>Group Name</th>
                    <th>Tournament Name</th>
                    <th>Event Name</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($round_list)) {
                    foreach ($round_list as $data) { ?>
                        <tr>
                            <td data-label="ID">
                                <?php if (!empty($data->round_id)) {
                                    echo $data->round_id;
                                } ?>
                            </td>
                            <td data-label="Round Name">
                                <?php if (!empty($data->round_name)) {
                                    echo $data->round_name;
                                } ?>
                            </td>
                            <td data-label="Group Name">
                                <?php if (!empty($data->grp_name)) {
                                    echo $data->grp_name;
                                } ?>
                            </td>
                            <td data-label="Tournament Name">
                                <?php if (!empty($data->listing_title)) {
                                    echo $data->listing_title;
                                } ?>
                            </td>
                            <td data-label="Event Name">
                                <?php if (!empty($data->event_name)) {
                                    echo $data->event_name;
                                } ?>
                            </td>
                            <td data-label="Action">
                                <a href="<?=base_url();?>/organizer/edit_round/<?=$data->round_id;?>" class="mt-0"><i class="fa-solid fa-pen-to-square m-2"></i></a>
                                <a onclick="delete_round('<?= !empty($data->round_id) ? $data->round_id : ''; ?>')"><i class="fa-solid fa-trash-can m-2" style="cursor: pointer;"></i></a>
                            </td>
                        </tr>
                <?php }
                } else { ?>
                    <tr>
                        <td colspan="6" class="no-data">No data found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="p-2">
        <?php
        $pager = service('pager');
        echo $pager->makeLinks($page, $perPage, $total, 'tourney_default_full');
        ?>
    </div>
</div>

<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script>
    function delete_round(id) {
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this round?") == true) {
            $.ajax({
                url: "<?=base_url();?>/Ajax/delete_round",
                method: "POST",
                data: { 'round_id': id },
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
            data: { 'tourney_id': cslug },
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
            data: { 'event_id': cslug },
            url: "<?php echo base_url(); ?>/Organizer/get_ajax_org_group",
            success: function(html) {
                $('#group_name').html(html);
            }
        });
    });
</script>
<?=$this->endSection();?>