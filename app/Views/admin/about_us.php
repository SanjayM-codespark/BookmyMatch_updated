<?=$this->extend('admin_base');?>
<?=$this->section('pageCss');?>
<link href="<?=base_url();?>/public/assets/plugins/alertify/css/alertify.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=base_url()?>/public/assets/plugins/minimal_rich_editor/richtext.min.css">
<?=$this->endSection();?>

<?=$this->section('main_content');?>

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
    <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="card-title">About Us</div>
                <form class="" action="<?=current_url();?>" method="post">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" value="<?=$about_us->about_us_title;?>" placeholder="" required="">
                        <div class="text-danger"><?= session('errors.title') ?></div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control todolist" name="desc" required=""><?=$about_us->about_us_desc;?></textarea>
                        <div class="text-danger"><?= session('errors.desc') ?></div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="about_us_id" value="<?php if(!empty($about_us)) { echo $about_us->about_us_id; }?>">
                        <button type="submit" name="submit" value="save" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="card-title">About Us</div>
                <?php if(!empty($about_us)): ?>
                    <h3><?=$about_us->about_us_title;?></h3>
                    <div><?=$about_us->about_us_desc;?></div>
                <?php endif ?>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>

<?=$this->endSection();?>

<?= $this->section('pageScript') ?>
<script src="<?=base_url();?>/public/assets/plugins/alertify/js/alertify.js"></script>
<script src="<?=base_url();?>/public/assets/pages/alertify-init.js"></script>
<script src="<?=base_url()?>/public/assets/plugins/minimal_rich_editor/jquery.richtext.js"></script>
<script>
    $(document).ready(function () {
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
    });
</script>
<?=$this->endSection();?>
