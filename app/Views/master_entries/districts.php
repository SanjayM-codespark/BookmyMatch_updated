<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                    <h4 class="p-3 mb-0 dash-head1">Districts</h4>
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
                        <h5 class="card-title">Edit District</h5>
                   <?php }else{ ?>
                        <h5 class="card-title">Add District</h5>
                  <?php  }
                    ?>
                     
                     <form id="add-cleaners" action="<?=current_url();?>" method="post" style="max-width: 400px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Country</label>
                           <select class="form-control custom-select" id="country" name="country" onchange="get_states(this.value)">
                                <option value="">--Select Country--</option>
                                <?php
                                if(!empty($countries)){
                                    foreach ($countries as $mfr) { ?>
                                       <option value="<?=$mfr->country_id;?>" <?php echo !empty($city->district_country_id)?($city->district_country_id == $mfr->country_id)?'selected':'':'';?>><?=ucfirst($mfr->country_name);?></option>
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
                           <select class="form-control custom-select" id="state" name="state">
                                <option value="">--Select State--</option>
                                <?php
                                if(!empty($states)){
                                    foreach ($states as $mfr) { ?>
                                       <option value="<?=$mfr->state_id;?>" <?php echo !empty($city->district_state_id)?($city->district_state_id == $mfr->state_id)?'selected':'':'';?>><?=ucfirst($mfr->state_name);?></option>
                                <?php    }
                                }
                                ?>
                            </select>
                            <div class="text-danger">
                                <?= session('errors.state') ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">District</label>
                            <input type="text" id="district" name="district" value="<?=!empty($city->district_name)?$city->district_name:set_value('name')?>" class="form-control" placeholder="District name">
                            <div class="text-danger">
                                <?= session('errors.district') ?>
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
                if(!empty($districts)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Districts</th>
                                <th>States</th>
                                <th>Countries</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=$offset+1;
                            foreach ($districts as $data) { ?>
                            <tr>
                                <td><?=$i;?></td>
                                <td><?=!empty($data->district_name)?$data->district_name:'';?></td>
                                <td><?php
                                $state_id = $data->district_state_id;
                                $state = $commonModel->select('state','*',array('state_id'=>$state_id));
                                echo !empty($state->state_name)?$state->state_name:'';
                                ?></td>
                                <td><?php
                                $country_id = $data->district_country_id;
                                $country = $commonModel->select('countries','*',array('country_id'=>$country_id));
                                echo !empty($country->country_name)?$country->country_name:'';
                                ?></td>
                                
                                <td><a href="<?=base_url()?>/admin/edit_districts/<?= !empty($data->district_id)?$data->district_id:'';?>"  class="btn btn-sm btn-info">Edit</a>&nbsp;<a href="javascript:void(0)" onclick="delete_district('<?= !empty($data->district_id)?$data->district_id:'';?>')" class="btn btn-sm btn-danger">Delete</a></td>
                            </tr>
                        <?php $i++;} ?>
                          
                            
                        </tbody>
                    </table>
                    <div class="mt-3">
                                        <?= $pager ?>
                                    </div>
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
<style>
    .pagination li a, 
    .pagination li span {
        padding: 0.5rem 0.5rem; 
        font-size: 1rem;
    }
</style>
<script>
    $(function() {
        $('#myTable').DataTable({
          "pageLength": 100,
          "bLengthChange": false,
          "bInfo": false,
        });
    });

    function delete_district(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this District?") == true) {
             $.ajax({
                url:"<?=base_url();?>/Ajax/delete_district",
                method:"POST",
                data: {'district_id':id},
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

    function get_states(id){
        $.ajax({
            url:"<?=base_url();?>/Ajax/get_states",
            method:"POST",
            data: {'country':id},
            success:function(response)
            {

                $('#state').html(response);

            },
            error: function (response) {
                alert('Something went wrong. try again'); 
            }
        });
    }
</script>
<?=$this->endSection();?>