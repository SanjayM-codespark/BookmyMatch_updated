<?=$this->extend('base');?>
<?=$this->section('pageCss');?>
<?=$this->endSection();?>
<?=$this->section('main_content');?>
<section class="" style="background:#10141E;padding-top:30px;min-height: 70vh;color:#ffffff;">
<div class="container">
    <h2 class="text-center">Terms & Conditions</h2>
    <p>
        <div><?php echo !empty($terms_conditions->terms_conditions_desc)?$terms_conditions->terms_conditions_desc:'';?></div>
    </p>
</div>
</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<?=$this->endSection();?>