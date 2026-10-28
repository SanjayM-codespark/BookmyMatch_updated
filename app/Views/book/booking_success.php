<?= $this->extend('base'); ?>
<?= $this->section('main_content'); ?>
<section class="pricecardsection px-0" style="background:#10141E;">
    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card text-center p-4 " style="background: #202739;border-radius: 8px;">
                    <img src="<?= base_url() ?>/public/assets/img/success.png" alt="" class="m-auto " style="width: 40px;
                    height: 40px;">
                    <h6 style="font-weight: 400;
                    font-size: 24px;
                    line-height: 30px;
                    
                    color: #fff;" class="mt-3">Congratulations</h6>
                    <h6 style="font-weight: 400;
                    font-size: 24px;
                    line-height: 30px;
                    color: #fff;
                    ">Booking Success!<br />Your booking has been confirmed.</h6>

                    <div class="row justify-content-center mt-3">
                        <div class="col-md-4 text-center">
                            <a href="<?= base_url(); ?>" class="btn makepaymentbutton">GO HOME</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script>

</script>
<?= $this->endSection(); ?>