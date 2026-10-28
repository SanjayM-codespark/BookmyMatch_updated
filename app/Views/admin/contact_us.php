<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<!-- Alertify css -->
<link href="<?=base_url();?>/public/assets/plugins/alertify/css/alertify.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=base_url()?>/public/assets/plugins/minimal_rich_editor/richtext.min.css">
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6">
                    <h4 class="p-3  dash-head1 mb-0"><?=$page_name;?></h4>
            </div>
        </div>
    </div>
</section>
<section class="p-4">
<?php if (session()->has('success_message')) : ?>
    <div class="alert alert-success">
        <?= session('success_message') ?>
        <?php session()->remove('success_message') ?>
    </div>
<?php endif ?>
<?php if (session()->has('error_message')) : ?>
    <div class="alert alert-danger">
        <?= session('error_message') ?>
        <?php session()->remove('error_message') ?>
    </div>
<?php endif ?>
<?php if (session()->has('errors')) : ?>
    <ul class="alert alert-danger">
    <?php foreach (session('errors') as $error) : ?>
        <li><?= $error ?></li>
    <?php endforeach ?>
    </ul>
<?php endif ?>

<div class="row">
    <!-- Column -->
    <div class="col-lg-8 col-md-8">
        
        <div class="card">
            <div class="card-body">

                <div class="card-title">Contact Us</div>
                <form class="" action="<?=current_url();?>" method="post">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone"  value="<?=!empty($contact_us->phone)?$contact_us->phone:'';?>" placeholder="" required="">
                        <div class="text-danger"><?= session('errors.phone') ?></div>
                    </div> 
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email"  value="<?=!empty($contact_us->email)?$contact_us->email:'';?>" placeholder="" required="">
                        <div class="text-danger"><?= session('errors.email') ?></div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" required=""><?=!empty($contact_us->address)?$contact_us->address:'';?></textarea>
                        <div class="text-danger"><?= session('errors.address') ?></div>
                    </div>
                   
                    <div class="form-group">
                        <button type="submit" name="submit" value="save" class="btn btn-success">Submit</button>
                    </div>   
                </form>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-6 col-md-6">
        
    </div>
    <!-- Column -->
</div>
<!-- end row -->
</section>
<?=$this->endSection();?>
<?=$this->section('pageScript');?>

<!-- Alertify js -->
<script src="<?=base_url();?>/public/assets/plugins/alertify/js/alertify.js"></script>
<script src="<?=base_url();?>/public/assets/pages/alertify-init.js"></script>
<script src="<?=base_url()?>/public/assets/plugins/minimal_rich_editor/jquery.richtext.js"></script>
<script>
    $(document).ready( function () {
     $('.todolist').richText({
         height: 150,
      fontColor: false,
      imageUpload: false,
      fileUpload: false,
      videoEmbed: false,
      urls: false,
      table: false,
      removeStyles: false,
      code: false
     });
   } );
    function delete_brand(id){
        var currenturl = window.location.href;
        alertify.confirm('Are you sure want delete this Brand?', function(){ 
            $.ajax({
                url:"<?=base_url();?>/admin/delete_brand",
                method:"POST",
                data: {'brand_id':id},
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