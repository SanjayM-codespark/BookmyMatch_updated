<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                    <h4 class="p-3 mb-0 dash-head1">States</h4>
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
                <div class="col-lg-4 col-md-4">
                    <?php
                    if(!empty($edit)){ ?>
                        <h5 class="card-title">Edit State</h5>
                   <?php }else{ ?>
                        <h5 class="card-title">Add State</h5>
                  <?php  }
                    ?>
                     
                     <form id="add-cleaners" action="<?=current_url();?>" method="post" style="max-width: 400px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Country</label>
                           <select class="form-control custom-select" id="country" name="country">
                                <option value="">--Select Country--</option>
                                <?php
                                if(!empty($countries)){
                                    foreach ($countries as $mfr) { ?>
                                       <option value="<?=$mfr->country_id;?>" <?php echo !empty($state->state_country_id)?($state->state_country_id == $mfr->country_id)?'selected':'':'';?>><?=ucfirst($mfr->country_name);?></option>
                                <?php    }
                                }
                                ?>
                            </select>
                            <div class="text-danger">
                                <?= session('errors.country') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">State</label>
                            <input type="text" id="city" name="state" value="<?=!empty($state->state_name)?$state->state_name:set_value('name')?>" class="form-control" placeholder="State name">
                            <div class="text-danger">
                                <?= session('errors.state') ?>
                            </div>
                        </div>
                    </div>
                    
                </div>
             
                  <button type="submit" class="btn btn-info"><?=!empty($edit)?'Update':'Add';?></button>
                  <!--<button type="reset" class="btn btn-danger">Clear</button>-->
            </form>
                </div>
                <div class="col-lg-8 col-md-8">
                <?php 
                if(!empty($states)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>States</th>
                                <th>Countries</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            foreach ($states as $data) {
                             ?>
                            <tr>
                                <td><?=$i;?></td>
                                <td><?=!empty($data->state_name)?$data->state_name:'';?></td>
                                <td><?php
                                $country_id = $data->state_country_id;
                                $country = $commonModel->select('countries','*',array('country_id'=>$country_id));
                                echo $country->country_name??'';
                                ?></td>
                                <td><a href="<?=base_url()?>/admin/edit_states/<?= !empty($data->state_id)?$data->state_id:'';?>"  class="btn btn-sm btn-info">Edit</a>&nbsp;<a href="javascript:void(0)" onclick="delete_city('<?= !empty($data->state_id)?$data->state_id:'';?>')" class="btn btn-sm btn-danger">Delete</a></td>
                            </tr>
                        <?php $i++;} ?>
                          
                            
                        </tbody>
                    </table>
                </div>

            <?php } ?>
            </div>
            </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script src="<?=base_url()?>/public/assets/node_modules/datatables/datatables.min.js"></script>
<script>
   

    function delete_city(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this State?") == true) {
             $.ajax({
                url:"<?=base_url();?>/Ajax/delete_state",
                method:"POST",
                data: {'state_id':id},
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
</script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function() {
    const table = $('#myTable').DataTable({
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
        color: #007bff!important;
    }
    .page-item.active .page-link {
        color: #fff!important;
    }
</style>
<?=$this->endSection();?>