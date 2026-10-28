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
                    <h4 class="p-3 mb-0  dash-head1"><?=$page_name;?></h4>
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
    <div class="col-lg-12 col-md-12">
        
        <div class="card">
            <div class="card-body">
                <form class="" action="<?=current_url();?>" method="post">
                     <div class="form-group">
                        <label>Question</label>
                        <input type="text" class="form-control" name="question"  value="<?=!empty($faq)?$faq->faq_question:'';?>" placeholder="" required="">
                        <div class="text-danger"><?= session('errors.question') ?></div>
                    </div> 
                    <div class="form-group">
                        <label>Answer</label>
                        <textarea class="form-control todolist" name="answer" required=""><?=!empty($faq)?$faq->faq_answer:'';?></textarea>
                        <div class="text-danger"><?= session('errors.answer') ?></div>
                    </div>
                   
                    <div class="form-group">
                        <input type="hidden" name="faq_id" value="<?php if(!empty($faq)) { echo $faq->faq_id; }?>">
                        <button type="submit" name="submit" value="save" class="btn btn-success">Submit</button>
                    </div>   
                </form>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-12 col-md-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title  dash-head1">FAQs</div>
                <table class="table">
                  <tr>
                    <th>S.No</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Action</th>
                  </tr>
                
                <?php 
                if(!empty($faqs)){
                  $i=1;
                  foreach ($faqs as $data) {?>
                    <tr>
                      <td><?=$i;?></td>
                      <td><?=$data->faq_question;?></td>
                      <td><?=$data->faq_answer;?></td>
                      <td><a href="<?=base_url();?>/admin/edit_faq/<?= !empty($data->faq_id)?$data->faq_id:'';?>" class="btn btn-sm btn-info">Edit</a>
                       <?php
                       if(session()->get('role_id') == 1){
                       ?>
                        &nbsp;<a href="javascript:void(0)" onclick="delete_faq('<?= !empty($data->faq_id)?$data->faq_id:'';?>')" class="btn btn-sm btn-danger">Delete</a>
                        <?php } ?>
                    </td>
                    </tr>
                 
                <?php  $i++;} } ?>
                </table>
            </div>
        </div>
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
    function delete_faq(id){
        var currenturl = window.location.href;
        alertify.confirm('Are you sure want delete this FAQ?', function(){ 
            $.ajax({
                url:"<?=base_url();?>/admin/delete_faq",
                method:"POST",
                data: {'faq_id':id},
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