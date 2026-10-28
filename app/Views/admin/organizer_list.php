<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                    <h4 class="p-3 mb-0 dash-head1">Organizer List</h4>
            </div>
            <div class="col-md-6 col-6 d-flex  justify-content-end m-auto ">
                <a href="<?=base_url();?>" class="mx-5 dash-link1">Home</a>
            </div>
        </div>
    </div>
</section>
<section class="p-4">

<div class="row">
    <!-- Column -->
    <div class="col-lg-12 col-md-12">
        <div class="card">
        <?php if (session()->has('success_message')) : ?>
            <div class="alert alert-success">
                <?= session('success_message') ?>
                <?php session()->remove('success_message') ?>
            </div>
        <?php endif ?>
        <div class="card-body">
            <h5 class="card-title">Organizer List</h5>
        </div>
        <?php 
        if(!empty($organizers)){
        ?>
        <div class="table-responsive px-1" style="position: relative;top: -25px;">
            <table id="myTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                         <th>Sl No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Organization</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Action</th>
                        
                        <!--<th>Events</th>
                        <th>Status</th>-->
                    </tr>
                </thead>
               
                <tbody>
                     <?php  $i = 1;
                foreach ($organizers as $data) {

                ?>
                    <tr>
                         <td><?= $i++; ?></td> 
                        <td><?=$data->user_name;?></td>
                        <td><?=$data->user_email;?></td>
                        <td><?=$data->user_mobile;?></td>
                        <td><?=$data->user_organization_name;?></td>
                        <td><?=$data->user_address;?></td>
                        <td>
                        <?php
                        if($data->user_active == 1){
                            echo '<div class="text-success">Verified</div>';
                        }else{
                            if($data->user_remarks == 'Onhold'){
                                echo '<div class="text-warning">Onhold</div>';
                            }else{
                                echo '<div class="text-info">Not Verified</div>';
                            }
                            
                        }
                        ?>
                        </td>
                        <td>
                        <?php
                        if($data->user_active == 0){ ?>
                            <button class="btn btn-success btn-sm mb-1" onclick="active_account('<?= !empty($data->user_id)?$data->user_id:'';?>')">Active</button>
                            <button class="btn btn-info btn-sm mb-1" onclick="refer_back('<?= !empty($data->user_id)?$data->user_id:'';?>')">Refer back</button>
                        <?php } ?>  
                            <button class="btn btn-danger btn-sm" onclick="delete_account('<?= !empty($data->user_id)?$data->user_id:'';?>')">Delete</button></td>
                    </tr>
                    <?php }?>
                </tbody>
            
            </table>
        </div>
    <?php }else{ ?>
        <div class="jumbotron text-center">
          <h5>No data found</h5>
        </div>
<?php } ?>
    </div>
        <!-- Card --->
    </div>
    <!-- Column -->
</div>
</section>


<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Refer back</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="alert alert-danger rb-ealert" style="display: none;"></div>
        <form id="refer-back">
          <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" class="form-control" placeholder="" name="subject" id="subject">
          </div>
          <div class="form-group">
            <label for="message">Message:</label>
            <textarea class="form-control" name="message" id="message" rows="5"></textarea>
          </div>
          <input type="hidden" class="org_id" name="org_id" value="">
          <button type="button" class="btn btn-primary refer-back-sub">Submit</button>
        </form>
      </div>

    </div>
  </div>
</div>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script src="<?=base_url()?>/public/assets/node_modules/datatables/datatables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
     $(document).ready(function () {
        $('#myTable').DataTable({
            paging: true,
            info: true,
            order: [],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search"
            }
        });
    });
    function delete_account(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this Organizer?") == true) {
             $.ajax({
                url:"<?=base_url();?>/Ajax/delete_organizer",
                method:"POST",
                data: {'user_id':id},
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

    function active_account(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want active this Organizer?") == true) {
             $.ajax({
                url:"<?=base_url();?>/Ajax/active_account",
                method:"POST",
                data: {'user_id':id},
                success:function(response)
                {
                    var obje = jQuery.parseJSON(response);
                        if(obje.success == 'success'){  
                          window.location.href = '<?=base_url();?>/organizer_list/verified';
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

    function refer_back(id){
        
        $('#refer-back .org_id').val(id);
        $("#myModal").modal("show");  
    }

    $(document).on("click", ".refer-back-sub", function(){ 
        $('.rb-ealert').hide();
        var sub = $('#subject').val();
        var msg = $('#message').val();
        if(sub == ''){
            $('.rb-ealert').html('Subject is required').show();
            return false;
        }
        if(msg == ''){
            $('.rb-ealert').html('Message is required').show();
            return false;
        }
        var currenturl = window.location.href;
        var formdata = $('#refer-back').serialize();
       $.ajax({
            url:"<?=base_url();?>/Ajax/refer_back",
            method:"POST",
            dataType:'json',
            data:formdata,
            success:function(response)
            {
                if(response.success == 'success'){ 
                  $("#myModal").modal('hide');
                  alert(response.message);
                  window.location.href = currenturl;
                }else{
                   alert('Something went wrong. try again');
                }

            },
            error: function (response) {
                alert('Something went wrong. try again'); 
            }
        });
     });
</script>
<style>
    .pagination li a {
        color: #007bff !important;
        background-color: white !important;
    }

    .page-item.active .page-link {
        color: #fff !important;
        background-color: #007bff !important;
    }
</style>
<?=$this->endSection();?>