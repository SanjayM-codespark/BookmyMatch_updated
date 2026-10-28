<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
                    <h4 class="p-3 mb-0 dash-head1">Countries</h4>
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
                <div class="col-lg-5 col-md-5">
                    <?php
                    if(!empty($edit)){ ?>
                        <h5 class="card-title">Edit Country</h5>
                   <?php }else{ ?>
                        <h5 class="card-title">Add Country</h5>
                  <?php  }
                    ?>
                     
                     <form id="add-cleaners" action="<?=current_url();?>" method="post" style="max-width: 400px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" id="name" name="name" value="<?=!empty($country->country_name)?$country->country_name:set_value('name')?>" class="form-control" placeholder="Country name">
                            <div class="text-danger">
                                <?= session('errors.name') ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" id="code" name="code" value="<?=!empty($country->country_mobile_code)?$country->country_mobile_code:set_value('code')?>" class="form-control" placeholder="Country Code">
                            <div class="text-danger">
                                <?= session('errors.code') ?>
                            </div>
                        </div>
                    </div>
                    
                </div>
             
                  <button type="submit" class="btn btn-info"><?=!empty($edit)?'Update':'Add';?></button>
                  <!--<button type="reset" class="btn btn-danger">Clear</button>-->
            </form>
                </div>
                <div class="col-lg-7 col-md-7">
                <?php 
                if(!empty($countries)){
                ?>
                <div class="table-responsive">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Country</th>
                                <th>Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            foreach ($countries as $data) { ?>
                            <tr>
                                <td><?=$i;?></td>
                                <td><?=!empty($data->country_name)?$data->country_name:'';?></td>
                                <td><?=!empty($data->country_mobile_code)?$data->country_mobile_code:'';?></td>
                                <td><a href="<?=base_url()?>/admin/edit_country/<?= !empty($data->country_id)?$data->country_id:'';?>"  class="btn btn-sm btn-info">Edit</a>&nbsp;<a href="javascript:void(0)" onclick="delete_country('<?= !empty($data->country_id)?$data->country_id:'';?>')" class="btn btn-sm btn-danger">Delete</a></td>
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
   

    function delete_country(id){
        var currenturl = window.location.href;
        if (confirm("Are you sure want delete this Country?") == true) {
             $.ajax({
                url:"<?=base_url();?>/Ajax/delete_country",
                method:"POST",
                data: {'country_id':id},
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