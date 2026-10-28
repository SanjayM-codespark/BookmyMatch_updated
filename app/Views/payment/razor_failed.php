<?=$this->extend('base');?>
<?=$this->section('main_content');?>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
   <!-- <section id="wrapper">
        <div class="login-register mt-2">
            <div class="login-box card" style="max-width: 500px;margin:0px auto; background:#202739;border-radius: 8px;">
                <div class="card-body text-center">
                    <h4>Oops!</h4> 
                    <h4>Your transaction got Failed</h4>
                    <div><?=!empty($_GET['description'])?$_GET['description']:'';?></div>
                    <p class="mt-3">Transaction ID: <?=$razorpay_payment_id;?></p>
                    <a class="btn btn-info" href="<?=base_url();?>">Go Home</a>
                </div>
            </div>
        </div>
    </section>-->
	
	<section class="pricecardsection px-0" style="background:#10141E;">
    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card text-center p-4 " style="background: #202739;border-radius: 8px;">
                    <div class="card-body text-center">
                    <h4 class="text-light">Oops!</h4> 
                    <h4 class="text-light">Your transaction got Failed</h4>
                    <div class="text-light"><?=!empty($_GET['description'])?$_GET['description']:'';?></div>
                    <p class="mt-3 text-light">Transaction ID: <?=$razorpay_payment_id;?></p>
                    <a class="btn btn-info" href="<?=base_url();?>">Go Home</a>
                </div>
                </div>
            </div>
        </div>

    </div>
</section>
    
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
   
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>

<?=$this->endSection();?>