<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>

<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section>
    <div class="container-fluid">
        <div class="row" style="background: #CAD6F8;">
            <div class="col-md-6 col-6 m-auto">
               <h5 class="p-3 mb-0 dash-head1"><?=$page_name;?></h5>
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
                <?php if (session()->has('error_message')) : ?>
                    <div class="alert alert-danger">
                        <?= session('error_message') ?>
                        <?php session()->remove('error_message') ?>
                    </div>
                <?php endif ?>
                <form class="" action="<?=current_url();?>" method="post" style="max-width: 500px;">
                    
                     <div class="form-group">
                        <label class="control-label">Upload Image</label>
                        <div class="img-div2 mb-1">
                            <?php
                            if(!empty($home_banner->home_banner_image)){ ?>
                               <img src='<?=base_url();?>/public/uploads/images/<?=$home_banner->home_banner_image;?>' width='250' height='150'>
                            <?php }else{ ?>
                                <img type="image" src="<?=base_url();?>/public/assets/images/image_preview.jpg" id="categoryimg" alt="" width="250" height="150">
                            <?php } ?>
                        </div>
                       <input type="file" name="image" class="image">
                        <input type="hidden" class="form-control photo2" name="top_image" value="<?=!empty($home_banner)?$home_banner->home_banner_image:'';?>"> 
                        <div class="text-danger">
                            <?= session('errors.top_image') ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label">Upload Website Image</label>
                        <div class="img-div3 mb-1">
                            <?php
                            if(!empty($home_banner->home_banner_web_image)){ ?>
                               <img src='<?=base_url();?>/public/uploads/images/<?=$home_banner->home_banner_web_image;?>' width='250' height='150'>
                            <?php }else{ ?>
                                <img type="image" src="<?=base_url();?>/public/assets/images/image_preview.jpg" id="categoryimg" alt="" width="250" height="150">
                            <?php } ?>
                        </div>
                       <input type="file" name="image1" class="image">
                        <input type="hidden" class="form-control photo3" name="top_image1" value="<?=!empty($home_banner)?$home_banner->home_banner_web_image:'';?>"> 
                        <div class="text-danger">
                            <?= session('errors.top_image1') ?>
                        </div>
                    </div>
              
                    <div class="form-group">
                        
                        <input type="hidden" name="home_banner_id" value="<?php if(!empty($home_banner)) { echo $home_banner->home_banner_id; }?>">
                        <button type="submit" name="submit" value="save" class="btn btn-success">Submit</button>
                    </div>   
                </form>
            </div>
        </div>
    </div>    
</div>
<!-- end row -->
</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script>
 $(document).on("change", "input[name='image']", function(){ 
    var white = $(this);
    var file = this.files[0];
    var fileType = file["type"];
    var validImageTypes = ["image/gif", "image/jpg", "image/jpeg", "image/png"];
    if ($.inArray(fileType, validImageTypes) < 0) {
        alert('Invalid file type. Only jpg, jpeg, png files are allowed');
        return false;
    }
    var name = this.files[0].name;
    var form_data = new FormData();

    form_data.append("images", this.files[0]);
    /*form_data.append("path", 'category');*/
    $.ajax({
        dataType: 'json',
        url:"<?=base_url();?>/uploads/upload_image",
        method:"POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend:function(){
           $('#uploaded_image').html("<label class='text-success'>Image Uploading...</label>");
        },   
        success:function(data)
        {
            if(data.status){
             var src = "<?=base_url();?>/public/uploads/images/"+data.filename;
             var img = "<img src='"+src+"' width='250' height='150'>";
             $('.img-div2').html(img);
             $('.photo2').val(data.filename);
            }else{
                alert(data.data.images);
            }
         
        }
    });
});

$(document).on("change", "input[name='image1']", function(){ 
    var white = $(this);
    var file = this.files[0];
    var fileType = file["type"];
    var validImageTypes = ["image/gif", "image/jpg", "image/jpeg", "image/png"];
    if ($.inArray(fileType, validImageTypes) < 0) {
        alert('Invalid file type. Only jpg, jpeg, png files are allowed');
        return false;
    }
    var name = this.files[0].name;
    var form_data = new FormData();

    form_data.append("images", this.files[0]);
    /*form_data.append("path", 'category');*/
    $.ajax({
        dataType: 'json',
        url:"<?=base_url();?>/uploads/upload_image",
        method:"POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend:function(){
           $('#uploaded_image').html("<label class='text-success'>Image Uploading...</label>");
        },   
        success:function(data)
        {
            if(data.status){
             var src = "<?=base_url();?>/public/uploads/images/"+data.filename;
             var img = "<img src='"+src+"' width='250' height='150'>";
             $('.img-div3').html(img);
             $('.photo3').val(data.filename);
            }else{
                alert(data.data.images);
            }
         
        }
    });
});

</script>
<?=$this->endSection();?>