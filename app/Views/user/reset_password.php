<?=$this->extend('layouts/admin_base');?>
<?=$this->section('main_content');?>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <section id="wrapper">
        <div class="login-register">
            <div class="login-box card">
                <div class="card-body">
                    <form class="" id="loginform" action="<?php echo current_url();?>" method="post">
                        <h3 class="text-center m-b-20">Recover Password</h3>
                         <?php if (session()->has('success_message')) : ?>
                            <div class="alert alert-success">
                                <?= session('success_message') ?>
                                <?php session()->remove('success_message') ?>
                            </div>
                        <?php endif ?>

                        <?php if (session()->has('error')) : ?>
                            <div class="alert alert-danger">
                                <?= session('error') ?>
                            </div>
                        <?php endif ?>

                         <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">OTP</label>
                                    <input type="text" id="otp" name="otp" value="<?=set_value('otp')?>" class="form-control" placeholder="Enter OTP">
                                    <div class="text-danger">
                                        <?= session('errors.otp') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">New Password</label>
                                    <input type="text" id="password" name="password" value="<?=set_value('password')?>" class="form-control" placeholder="Enter New Password">
                                    <div class="text-danger">
                                        <?= session('errors.password') ?>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                       
                        <div class="form-group text-center">
                            <div class="col-xs-12 p-b-20">
                                <button class="btn btn-block btn-lg btn-info btn-rounded" type="submit">Reset Password</button>
                            </div>
                        </div>
                       
                    </form>
                    
                </div>
            </div>
        </div>
    </section>
    
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
   
<?=$this->endSection();?>