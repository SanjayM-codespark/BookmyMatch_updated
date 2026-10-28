<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<!-- Alertify css -->
<link href="<?=base_url();?>/public/assets/plugins/alertify/css/alertify.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=base_url()?>/public/assets/plugins/minimal_rich_editor/richtext.min.css">
<link rel="stylesheet" href="<?=base_url()?>/public/assets/plugins/switchery/switchery.css">
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

                <div class="card-title">General Settings</div>
                <form class="" action="<?=current_url();?>" method="post">

                    <div class="form-group">

                        <label for="free_booking">Free Booking <small>[ Enable / Disable ] </small></label><br>
                        <input type="checkbox" id="free_booking_btn" class="js-switch" <?php if (!empty($free_booking) && $free_booking == '1') { echo 'checked'; } ?>>

                        <input type="hidden" id="free_booking" name="free_booking" value="<?= (isset($free_booking) && !empty($free_booking)) ? $free_booking : 0; ?>">
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
<script type="text/javascript" src="<?=base_url()?>/public/assets/plugins/switchery/switchery.js"></script>

<script type="text/javascript">
    var changeCheckbox = document.querySelector('#free_booking_btn');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if (changeCheckbox.checked)
            $('#free_booking').val(1);
        else
            $('#free_booking').val(0);
    };
</script>
<?=$this->endSection();?>