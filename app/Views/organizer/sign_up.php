<?=$this->extend('base');?>
<?=$this->section('pageCss');?>
<link rel="stylesheet" href="<?=base_url();?>/public/assets/node_modules/html5-editor/bootstrap-wysihtml5.css" />
<?=$this->endSection();?>
<?=$this->section('main_content');?>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <section id="wrapper">
        <div class="login-register1  mt-2">
            <div class="login-box card" style="max-width: 500px;margin:0px auto;">
                <div class="card-body">
                    <form class="form-horizontal form-material" id="signup-form" accept="<?=current_url();?>" method="post">
                        <h3 class="text-center m-b-20">Organizer Registration</h3>
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
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" name="organization_name" type="text" value="<?php echo set_value('organization_name'); ?>"  placeholder="Organization Name"> 
                                <div class="text-danger">
                                    <?= session('errors.organization_name') ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" name="name" type="text" value="<?php echo set_value('name'); ?>"  placeholder="Name"> 
                                <div class="text-danger">
                                    <?= session('errors.name') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input class="form-control" name="mobile" type="text" value="<?php echo set_value('mobile'); ?>"  placeholder="Mobile"> 
                                <div class="text-danger">
                                    <?= session('errors.mobile') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input class="form-control" name="email" type="text" value="<?php echo set_value('email'); ?>"  placeholder="Email"> 
                                <div class="text-danger">
                                    <?= session('errors.email') ?>
                                </div>
                            </div>
                        </div>
                      
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" name="password" type="password"  placeholder="Password"> 
                                <div class="text-danger">
                                    <?= session('errors.password') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" name="confirm_password" type="password"  placeholder="Confirm Password"> 
                                <div class="text-danger">
                                    <?= session('errors.confirm_password') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                           <select class="form-control custom-select" id="country" name="country" placeholder="Country">
                                <option value="">Country</option>
                                <?php
                                if(!empty($countries)){
                                    foreach ($countries as $vt) { ?>
                                       <option value="<?=$vt->country_id;?>" <?php echo (set_value('country')==$vt->country_id)?" selected=' selected'":""?>><?=ucfirst($vt->country_name);?></option>
                                <?php    }
                                }
                                ?>
                            </select>
                            <div class="text-danger">
                                <?= session('errors.country') ?>
                            </div>
                        </div>
                        <div class="form-group">
                           <select class="form-control custom-select" id="city" name="city" placeholder="City">
                                <option value="" class="text-muted">City</option>
                                <?php
                                if(!empty($cities)){
                                    foreach ($cities as $vt) { ?>
                                       <option value="<?=$vt->city_id;?>" <?php echo (set_value('city')==$vt->city_id)?" selected=' selected'":""?>><?=ucfirst($vt->city_name);?></option>
                                <?php    }
                                }
                                ?>
                            </select>
                            <div class="text-danger">
                                <?= session('errors.city') ?>
                            </div>
                        </div>
                         <div class="form-group">
                            <div class="col-xs-12">
                                <textarea class="form-control address" name="address"  placeholder="Address"><?php echo set_value('address'); ?></textarea>
                                <div class="text-danger">
                                    <?= session('errors.address') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-center">
                            <div class="col-xs-12 p-b-20">
                                <button class="btn btn-block btn-lg btn-info btn-rounded" type="submit">Submit</button>
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
<?= $this->Section('pageScript') ?>
<script src="<?=base_url();?>/public/assets/node_modules/html5-editor/wysihtml5-0.3.0.js"></script>
<script src="<?=base_url();?>/public/assets/node_modules/html5-editor/bootstrap-wysihtml5.js"></script>

<script>
    $(document).ready(function() {
        $('.address').wysihtml5();
        var content = $('.address');
        var contentPar = content.parent()
        contentPar.find('.wysihtml5-toolbar').remove();
    });
 $(document).on('change','#country',function(){
   var cslug = $(this).val();
   $.ajax({
        type: "POST",
        data: { 'country': cslug},
        url: "<?php echo base_url(); ?>/Ajax/get_cities",
        success: function( html ) {
            $('#city').html(html);
        }
    }); 
    
});

</script>
<?=$this->endSection();?>