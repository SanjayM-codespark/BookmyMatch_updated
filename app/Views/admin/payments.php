<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                    <h4 class="p-3 mb-0 dash-head1">Payments</h4>
            </div>
            <div class="col-md-6 col-6 d-flex  justify-content-end m-auto ">
                <a href="<?=base_url();?>" class="mx-5 dash-link1">Home</a>
            </div>
        </div>
    </div>
</section>
<section class="p-4">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card " style="overflow-x:auto;">

                <?php if (session()->has('success_message')) : ?>
                <div class="alert alert-success">
                    <?= session('success_message') ?>
                    <?php session()->remove('success_message') ?>
                </div>
            <?php endif ?>

            <?php 
            if(!empty($payments)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table pay-table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tournament Name</th>
                                <th>Event Name</th>
                                <!-- <th>Team</th> -->
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php
                            foreach ($payments as $data) {
                                ?>
                                <tr>
                                    <td><?=$data->payment_id;?></td>
                                    <td>
                                        <?php $listing_id = $data->payment_tournament_id;
                                        $res = $commonModel->select('listings','*',array('listing_id'=>$listing_id));
                                        if(!empty($res)){
                                           echo $res->listing_title;
                                       }
                                       ?>
                                       
                                   </td>
                                   <td>
                                    <?php $event_id = $data->payment_event_id;
                                    $res = $commonModel->select('listing_events','*',array('listing_event_id'=>$event_id));
                                    if(!empty($res)){
                                       $results = $commonModel->select('events','*',array('event_id'=>$res->listing_event_title));
                                       if(!empty($results)){
                                        echo $results->event_name;
                                        
                                    }
                                }
                                ?>
                            </td>
                            <!-- <td><?php 
                            $pay_id = $data->payment_id;
                            $res = $commonModel->select('participants','*',array('participant_payment_id'=>$pay_id),true);
                            if(!empty($res)){
                                foreach ($res as $row) {
                                    echo $row->participant_name.'<br/>';
                                }
                            }

                        ?></td> -->
                        <td><?=$data->payment_txn_id;?></td>
                        <td><?=$data->payment_amount;?></td>
                        <td><?=$data->payment_date;?></td>
                        
                    </tr>
                <?php }?>
            </tbody>
            
        </table>

    </div>
    <div>
        <?php
        $pager = service('pager');
        echo $pager->makeLinks($page,$perPage,$total,'tourney_default_full');
        ?>
    </div>
<?php }else{ ?>
    <div class="jumbotron text-center">
      <h5>No data found</h5>
  </div>
<?php } ?>

</div>
</div>
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
<script>
    $(function() {
         $('#myTable').DataTable({
          "pageLength": 100,
          "bLengthChange": false,
          "bInfo": false,
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
<?=$this->endSection();?>