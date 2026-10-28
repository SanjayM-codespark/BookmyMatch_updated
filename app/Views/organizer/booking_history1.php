<?=$this->extend('organizer_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        </div>
    </div>
</section>


 <?php if (session()->has('success_message')) : ?>
    <div class="alert alert-success">
        <?= session('success_message') ?>
        <?php session()->remove('success_message') ?>
    </div>
<?php endif ?>

    <div class="card  mb-5" style="overflow-x:auto; margin: 23px">
        <?php 
        if(!empty($tournaments)){
        if(!empty(session()->get('user_active'))){ ?>
        <?php }else{ ?>
            <div class="alert alert-danger">
                Your account is not verified. So your tournament events are not published yet.
            </div>
        <?php } ?>
        <div class="p-1">
           
            <form method="get" action="<?=current_url();?>">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control custom-select" name="status" id="tstatus">
                            <option value="">Select Status</option>
                            <option value="upcoming" <?=!empty($_GET['status'])?($_GET['status'] == 'upcoming')?'selected="selected"':'':'';?>>Live & Upcoming</option>
                            <option value="completed" <?=!empty($_GET['status'])?($_GET['status'] == 'completed')?'selected="selected"':'':'';?>>Recently Completed</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control custom-select" name="sports" id="tstatus">
                            <option value="">Select Sports</option>
                            <?php
                            if(!empty($sports)){
                                foreach ($sports as $vt) { ?>
                                   <option value="<?=$vt->sports_id;?>" <?=!empty($_GET['sports'])?($_GET['sports'] == $vt->sports_id)?'selected="selected"':'':'';?>><?=ucfirst($vt->sports_name);?></option>
                            <?php    }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" name="query" class="form-control" placeholder="Search.." value="<?=!empty($_GET['query'])?$_GET['query']:'';?>">
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
        <div class="table-responsive-xl org-table p-1" style="overflow-x:auto;">
        <table class="table table-bordered table-striped w-auto mw-100">
            <thead style="background: #fff">
                <tr class="text-center">
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
            <tbody style="background: #fff">
                 <?php
                foreach ($tournaments as $data) {
                ?>
                <tr>
                    <td class="text-left"><?=$data->listing_id;?></td>
                    <td class="text-left"  width="150px;"><?=$data->listing_title;?></td>
                    <td class="text-left"><?=$data->sports_name;?></td>
                    <td class="text-left"  width="130px;"><?=date('d-m-Y h:i a',strtotime($data->listing_start_date));?></td>
                    <td class="text-left"  width="130px;"><?=date('d-m-Y h:i a',strtotime($data->listing_end_date));?></td>
                    <td class="text-left"><?=$data->city_name;?></td>
                    <td class="text-left"  width="250px;"><?=$data->listing_address;?></td>
                    <td class="text-left">
                        <a class="btn btn-info btn-sm dash-btn2" href="<?=base_url();?>/view_events/<?=$data->listing_id;?>">View</a>
                    </td>
              
                </tr>
                <?php }?>
            </tbody>
        </table>
        <?= $pager ?>

        </div>
        <?php }else{ ?>
                <div class="jumbotron text-center">
                  <h5>No data found</h5>
                </div>
        <?php } ?>
    </div>


<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Cancel Tournament</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
         <form action="/action_page.php">
          <div class="form-group">
            <label for="email">Remarks:</label>
            <input type="text" class="form-control" placeholder="Remarks" id="remarks" name="remarks">
          </div>
          <input type="hidden" name="tourney_id" id="tourney_id" value="">
          <button type="button" onclick="cancel_tournament_submit()" class="btn btn-primary">Submit</button>
        </form> 
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
 <script src="<?=base_url()?>/public/assets/node_modules/datatables/datatables.min.js"></script>
    <script>   
    $('#example23').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excel'
        ]
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
  
    function delete_tournament(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this Event?") == true) {
             $.ajax({
                url:"<?=base_url();?>/Ajax/delete_tournament",
                method:"POST",
                data: {'event_id':id},
                success:function(response)
                {
                    var obje = jQuery.parseJSON(response);
                        if(obje.success == 'success'){     
                          alert(obje['message']);
                          window.location.href = currenturl;
                        }else if(obje['failed'] == 'error'){
                           alert(obje['message']);
                        }
                },
                error: function (response) {
                    alert('Something went wrong. try again'); 
                }
             });
        }
    }

    function cancel_tournament(id){
        if (confirm("Are you sure want cancel this tournament?") == true) {
            $('#tourney_id').val(id);
            $('#myModal').modal('show');
        }
    }

    function cancel_tournament_submit(){
        var currenturl = window.location.href;
        var id = $('#tourney_id').val();
        var remarks = $('#remarks').val();
        if(remarks == ''){
            alert('Remark is required');
            return false;
        }
        $.ajax({
            url:"<?=base_url();?>/Ajax/cancel_tournament",
            method:"POST",
            data: {'id':id,'remarks':remarks},
            success:function(response)
            {

                var obje = jQuery.parseJSON(response);
                if(obje.success == 'success'){ 
                    $('#myModal').modal('hide');  
                    $('#remarks').val('');  
                    alert(obje['message']);
                    window.location.href = currenturl;
                }else if(obje['failed'] == 'error'){
                    alert(obje['message']);
                }
            },
            error: function (response) {
                alert('Something went wrong. try again'); 
            }
        });
    }
</script>
<style>
    .custom-pager {
        min-width: 10px;
        padding: 5px 10px;
        font-size: 16px;
        text-align: center;
    }
</style>
<?=$this->endSection();?>