<?=$this->extend('base');?>
<?=$this->section('main_content');?>
<section class="pricecardsection px-0" style="background:#10141E;">
    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card text-center p-4 " style="background: #202739;border-radius: 8px;">
                    <img src="<?=base_url()?>/public/assets/img/success.png" alt="" class="m-auto " style="width: 40px;
                    height: 40px;" >
                    <h6 style="font-weight: 400;
                    font-size: 24px;
                    line-height: 30px;
                    
                    color: #fff;" class="mt-3" >Congratulations</h6>
                    <h6 style="font-weight: 400;
                    font-size: 24px;
                    line-height: 30px;
                    
                    color: #fff;
                    " >Your transaction is successful and your booking has been confirmed.</h6>

                    <p class="mb-0 text-light">Transaction ID: <?=$razorpay_payment_id;?></p>
                    <p class="text-light">Order ID:    <?=$merchant_order_id;?></p>
                    <div class="row justify-content-center mt-3">
                        <div class="col-md-4 text-center">
                            <a href="<?=base_url();?>" class="btn makepaymentbutton">GO HOME</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script>
 
</script>
<?=$this->endSection();?>