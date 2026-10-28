<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                    <h4 class="p-3 mb-0 dash-head1">Tournaments</h4>
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
        <div class="card  mb-5" style="overflow-x:auto;">
            <?php if (session()->has('success_message')) : ?>
                <div class="alert alert-success">
                    <?= session('success_message') ?>
                    <?php session()->remove('success_message') ?>
                </div>
            <?php endif ?>
        <div class="card-body">
                <h5 class="card-title text-capitalize"><?php $uri = service('uri'); echo $uri->getSegment(2);?> Tournaments</h5>
           
            <?php 
            if(!empty($tournaments)){
            ?>
            <div class="table-responsive-xl org-table2" style="overflow-x:auto;">
              
                <table id="participantsTable" class="table table-bordered table-striped w-auto mw-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Sports</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Events</th>
                         <th class="action-col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($tournaments as $data) {
                    ?>
                        <tr>
                            <td><?=$data->listing_id;?></td>
                            <td width="150px;">
                                <a href="<?=base_url();?>/view_tournament_details/<?=$data->listing_id;?>" class="link"> <?=$data->listing_title;?></a>
                            </td>
                            <td><?=$data->sports_name;?></td>
                            <td width="130px;"><?=date('d-m-Y h:i a',strtotime($data->listing_start_date));?>

                            </td>
                            <td width="130px;"><?=date('d-m-Y h:i a',strtotime($data->listing_end_date));?></td>
                            <td><?=$data->city_name;?></td>
                            <td width="250px;"><?=$data->listing_address;?></td>
                            <td>
                            <a class="btn btn-info btn-sm dash-btn2" href="<?=base_url();?>view_events/<?=$data->listing_id;?>">View</a>
                            </td>
                            <td> 
                                <?php
                                if($data->listing_active == 0){?> 
                                    <a class="btn btn-primary btn-sm mb-1" onclick="unban_tournament('<?=$data->listing_id;?>')">Active</a>&nbsp
                                <?php }else{ ?>
                                    <button class="btn btn-danger btn-sm mb-1" onclick="ban_tournament('<?=$data->listing_id;?>')">Inactive</button>&nbsp;
                                <?php }?>
                                <a class="btn btn-success btn-sm mb-1" href="<?=base_url();?>view_tournament_details/<?=$data->listing_id;?>">Details</a>
                                <button class="btn btn-danger btn-sm" onclick="delete_tournament('<?= !empty($data->listing_id)?$data->listing_id:'';?>')">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div>
              
            </div>
            <?php }else{?>
                <div class="jumbotron text-center">
                  <h5>No data found</h5>
                </div>
           <?php }?>
        </div>
        <!-- Card body --->
        </div>
        <!-- Card --->
    </div>
    <!-- Column -->
</div>
</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
 <script src="<?=base_url()?>/public/assets/node_modules/datatables/datatables.min.js"></script>
    
     <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script>   
  

   
    function delete_tournament(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this Event?") == true) {
             $.ajax({
                url:"<?=base_url();?>/Ajax/admin_delete_tournament",
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


function ban_tournament(id){
    var currenturl = window.location.href;
    if (confirm("Are you sure want Inactive this tournament?") == true) {
        $.ajax({
            url:"<?=base_url();?>/admin/inactive_tournament",
            method:"POST",
            data: {'id':id,'<?= csrf_token()?>':$('#csrftoken').val()},
            success:function(response)
            {
                var obje = jQuery.parseJSON(response);
                if(obje.success == 'success'){ 
                  alert(obje.message);
                  setTimeout(function() {
                     location.reload();
                  }, 1200);
                }else if(obje['failed'] == 'error'){
                   alert(obje.message);
                }
            },
            error: function (response) {
                alert('Something went wrong. try again'); 
            }
        });
        
    }
}

function unban_tournament(id){
    var currenturl = window.location.href;
    if (confirm("Are you sure want Activate this tournament?") == true) {
        $.ajax({
            url:"<?=base_url();?>/admin/active_tournament",
            method:"POST",
            data: {'id':id,'<?= csrf_token()?>':$('#csrftoken').val()},
            success:function(response)
            {
                var obje = jQuery.parseJSON(response);
                if(obje.success == 'success'){ 
                  alert(obje.message);
                  setTimeout(function() {
                     location.reload();
                  }, 1200);
                }else if(obje['failed'] == 'error'){
                   alert(obje.message);
                }
            },
            error: function (response) {
                alert('Something went wrong. try again'); 
            }
        });
        
    }
}

</script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#participantsTable').DataTable({
            paging: true,
            ordering: true,
            info: true,

        });

        $('#participantSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
<style>
.action-col {
        width: 250px !important;
        min-width: 250px;
        max-width: 250px;
        white-space: nowrap;
    }
  .pagination li a {
        color: #007bff!important;
    }
    .page-item.active .page-link {
              color: #fff!important;

    }
</style>
<?=$this->endSection();?>