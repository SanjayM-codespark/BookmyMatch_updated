<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<!-- Alertify css -->
<link href="<?=base_url();?>/public/assets/plugins/alertify/css/alertify.css" rel="stylesheet" type="text/css">
<link href="<?=base_url()?>/public/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
               <h5 class="p-3 mb-0 dash-head1">Banners</h5>
            </div>
            <div class="col-md-6 col-6 d-flex  justify-content-end m-auto ">
                <a href="<?=base_url();?>" class="mx-5 dash-link1">Home</a>
            </div>
        </div>
    </div>
</section>

<section class="p-4">
<div class="row">
    <div class="col-md-12">
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
                <div><a href="<?=site_url('admin/add_home_banner');?>" class="mb-2 btn btn-success">Add Home Banner</a></div>
                <div class="table-responsive">
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SN No.</th>
                                <th>Image</th>
                                <th>Website Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!empty($home_banner)){ 
                        $i=1;
                        foreach ($home_banner as $data) { ?>
                        <tr>
                            <td><?=$i;?></td>
                            <td>
                                <?php $home_banner_image = !empty($data->home_banner_image)?$data->home_banner_image:'';?>
                               <img src="<?=base_url();?>/public/uploads/images/<?=$home_banner_image;?>" width="250" height="120">
                            </td>
                            <td>
                                <?php $home_banner_web_image = !empty($data->home_banner_web_image)?$data->home_banner_web_image:'';?>
                               <img src="<?=base_url();?>/public/uploads/images/<?=$home_banner_web_image;?>" width="250" height="120">
                            </td>
                            
                           <td>
                            <a href="<?=base_url();?>/admin/edit_home_banner/<?= !empty($data->home_banner_id)?$data->home_banner_id:'';?>" class="btn btn-sm btn-info">Edit</a>&nbsp;<a href="javascript:void(0)" onclick="delete_home_banner('<?= !empty($data->home_banner_id)?$data->home_banner_id:'';?>')" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php $i++;} } else { ?>
                          <tr><td colspan="5" class="text-center">No Record Found</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>    
</div>
<!-- end row -->
</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script src="<?=base_url();?>/public/assets/plugins/alertify/js/alertify.js"></script>
<script>
    
    function delete_home_banner(id){
        var currenturl = window.location.href;
        alertify.confirm('Are you sure want delete this Banner?', function(){ 
            $.ajax({
                url:"<?=base_url();?>/admin/delete_home_banner",
                method:"POST",
                data: {'home_banner_id':id},
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
            
        });
    }
</script>
<?=$this->endSection();?>