<?=$this->extend('base');?>
<?=$this->section('main_content');?>
<section class="pricecardsection px-0 " style="background-color: #10141E;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card profile-head1 text-center p-4 " style="background:#202739;border-radius: 0px;bordere position: relative;"> 
				<div class="profile-head2 text-center">
				   <h5 class="text-center">Change Your Password</h5>
				   </div>                  
				 <br>
                    <p style="font-weight: normal;
                    font-size: 16px;
                    line-height: 20px;
                    text-align: center;
                    
                    color: #fff;
                    "> Please Provide your current Password
                        and change New Password</p>
                    <form action="<?=current_url();?>" method="post">
                         <?php if (session()->has('success_message')) : ?>
                            <div class="alert alert-success">
                                <?= session('success_message') ?>
                                <?php session()->remove('success_message') ?>
                            </div>
                        <?php endif ?>
                        <?php if (session()->has('error')) : ?>
                            <div class="alert alert-danger">
                                <?= session('error') ?>
                                <?php session()->remove('error') ?>
                            </div>
                        <?php endif ?>
                        <div class="row p-3 justify-content-center">
                            <div class="col-md-10">
                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                        <div class="mb-3" style="position: relative;">

                                            <input type="password" class="form-control  inputgroups"
                                                id="exampleInputPassword1" name="current_password" value="<?=set_value('current_password');?>" placeholder=" Current Password ">
                                            <div class="text-danger">
                                                <?= session('errors.current_password') ?>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="mb-3" style="position: relative;">

                                            <input type="password" class="form-control  inputgroups"
                                                id="exampleInputPassword1" name="password" value="<?=set_value('password');?>" placeholder="New Password">
                                            <div class="text-danger">
                                                <?= session('errors.password') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="mb-3" style="position: relative;">

                                            <input type="password" class="form-control  inputgroups"
                                                id="exampleInputPassword1" name="passconf" value="<?=set_value('passconf');?>" placeholder="Confirm Password">
                                            <div class="text-danger">
                                                <?= session('errors.passconf') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="row justify-content-center">
                            <button type="submit" class="btn" style="width: 180px;
                            height: 48px;
                            background: #E71A2D;
                            border-radius: 4px;font-weight: 600;
font-size: 16px;
line-height: 20px;

color: #FFFFFF; padding: 12px;">CHANGE PASSWORD</button>
                        </div>
                    </form>
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