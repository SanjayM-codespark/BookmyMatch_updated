<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>



    <?php if (session()->has('success_message')) : ?>
        <div class="alert alert-success">
            <?= session('success_message') ?>
            <?php session()->remove('success_message') ?>
        </div>
    <?php endif ?>

    <div class="card  mb-5" style="overflow-x:auto;">
        <div class="p-1">
            <div class="row">
                <div class="col-md-12">
                    <label>Filters</label>
                </div>
            </div>
            <form method="get" action="<?= current_url(); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" id="tournament" name="tournament">
                                <option value="">Select Tournament</option>
                                <?php
                                if (!empty($tournaments)) {
                                    foreach ($tournaments as $mfr) { ?>
                                        <option value="<?= $mfr->listing_id; ?>" <?php echo (!empty($_GET['tournament']) && $_GET['tournament'] == $mfr->listing_id) ? " selected=' selected'" : "" ?>><?= ucfirst($mfr->listing_title); ?></option>
                                <?php   }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" id="event" name="event">
                                <option value="">Select Events</option>
                                <?php
                                if (!empty($events)) {
                                    foreach ($events as $mfr) { ?>
                                        <option value="<?= $mfr->listing_event_id; ?>" <?php echo (!empty($_GET['event']) && $_GET['event'] == $mfr->listing_event_id) ? " selected=' selected'" : "" ?>><?= ucfirst($mfr->event_name??''); ?></option>
                                <?php   }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" id="group_name" name="group_name">
                                <option value="">Select Group</option>
                                <?php
                                if (!empty($tblgroups)) {
                                    foreach ($tblgroups as $mfr) { ?>
                                        <option value="<?= $mfr->grp_id; ?>" <?php echo (!empty($_GET['group_name']) && $_GET['group_name'] == $mfr->grp_id) ? " selected=' selected'" : "" ?>><?= ucfirst($mfr->grp_name??''); ?></option>
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
        
        <div class="table-responsive-xl org-table p-1">
            <table id="myTable" class="table pay-table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Team 1</th>
                        <th>Team 2</th>
                        <th>Round Name</th>
                        <th>Tournament Name</th>
                        <th>Event Name</th>
                        <th>Match Location</th>
                        <th>Match Date</th>
                        <th>Umpire</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    if (!empty($match_list)) {
                        foreach ($match_list as $data) { ?>
                            <tr>
                                <td>
                                    <?php
                                    if (!empty($data->match_id)) {
                                        echo $data->match_id;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($data->team1)) {
                                        echo $data->team1;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($data->team2)) {
                                        echo $data->team2;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($data->round_name)) {
                                        echo $data->round_name;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($data->listing_title)) {
                                        echo $data->listing_title;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($data->event_name)) {
                                        echo $data->event_name;
                                    } ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($data->match_location)) {
                                        echo $data->match_location;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($data->match_date)) {
                                        echo date('d-m-Y h:iA', strtotime($data->match_date));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($data->umpire_name)) {
                                        echo $data->umpire_name;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= base_url(); ?>/organizer/edit_match/<?= $data->match_round_id; ?>" class="mt-0"><i class="fa-solid fa-pen-to-square m-2 "></i> </a> &nbsp;&nbsp;&nbsp;
                                    <a onclick="delete_match('<?= !empty($data->match_id) ? $data->match_id : ''; ?>')"><i class="fa-solid fa-trash-can m-2"
                                            style="cursor: pointer;"></i></a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <td colspan="5">No data found</td>
                    <?php } ?>
                </tbody>
            </table>
        </div>
       
    </div>
<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
     $(document).ready(function() {
    const table = $('#myTable').DataTable({
      paging: true,
      ordering: true,
      info: true,
      searching:true,
    });

  });
    function delete_match(id) {
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this match?") == true) {
            $.ajax({
                url: "<?= base_url(); ?>/Ajax/delete_match",
                method: "POST",
                data: {
                    'match_id': id
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
<style>
  .pagination li a {
        color: #007bff!important;
    }
    .page-item.active .page-link {
        color: #fff!important;
    }
</style>
<?= $this->endSection(); ?>