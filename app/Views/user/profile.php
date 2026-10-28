<?=$this->extend('base');?>
<?=$this->section('main_content');?>
<section class="pricecardsection px-0" style="background-color:#10141E;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card profile-head1 p-4 " style="border-radius: 15px;bordere position: relative;">
                   <div class="profile-head2">
				   <h5>My Profile</h5>
				   </div>
				   <br>
                    <?php if (session()->has('success_message')) : ?>
                        <div class="alert alert-success">
                            <?= session('success_message') ?>
                            <?php session()->remove('success_message') ?>
                        </div>
                    <?php endif ?>
                    <div class="row">
                        <div class="col-md-4 text-left profile-photo1" style="position: relative;">
						<div class="profile-left">
                           <?php
                           if(!empty($users->user_profile_img)){
                            $scr = base_url().'/public/uploads/images/profile/'.$users->user_id.'/'.$users->user_profile_img;
                           }else{
                            $scr = base_url().'/public/assets/img/profile1.png';
                           }
                           ?>
                            <img class="pro-img pro-img1" src="<?=$scr;?>" alt="">
                            <img class="update-pro-img" src="<?=base_url();?>/public/assets/icons/Group 37822.png" alt="" style="position: absolute;
                            bottom: 0;
                            left: 80px;">
							 <h4  class="profile-input-txt1 profile-input-txt"><i class="fa fa-user" aria-hidden="true"></i>
 <?=!empty($users->user_name)?$users->user_name:'';?></h4>
							  <h4  class="profile-input-txt1"><i class="fa fa-phone" aria-hidden="true"></i>
 <?=!empty($users->user_mobile)?$users->user_mobile:set_value('mobile');?></h4>
 <h4  class="profile-input-txt1"><i class="fa fa-envelope" aria-hidden="true"></i> 
 <?=!empty($users->user_email)?$users->user_email:set_value('email');?></h4>
							  
                            <input type="file" name="image" style="display: none;">
							</div>
                        </div>
                        <div class="col-md-8  profile-name1 m-auto">
						<div class="profile-right">
                            <form action="<?=current_url();?>" method="post">
                        <?php if(session()->get('role_id') == 3){ ?>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputPassword6" class="col-form-label profilelabel" >Organization Name</label>
                              </div>
                              <div class="col-md-12">
                                <input type="name" id="inputPassword6" name="organization_name" value="<?=!empty($users->user_organization_name)?$users->user_organization_name:set_value('organization_name');?>" class="form-control inputgroups" aria-describedby="passwordHelpInline" placeholder="Name">
                                <div class="text-danger">
                                    <?= session('errors.organization_name') ?>
                                </div>
                              </div>
                        </div>
                        <?php } ?>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputPassword6" class="col-form-label profilelabel" >User Name</label>
                              </div>
                              <div class="col-md-12">
                                <input type="name" id="inputPassword6" name="username" value="<?=!empty($users->user_name)?$users->user_name:set_value('username');?>" class="form-control inputgroups" aria-describedby="passwordHelpInline" placeholder="Name">
                                <div class="text-danger">
                                    <?= session('errors.username') ?>
                                </div>
                              </div>
                              
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputPassword6" class="col-form-label profilelabel">Email</label>
                              </div>
                              <div class="col-md-12">
                                <input type="email" id="inputPassword6" name="email" value="<?=!empty($users->user_email)?$users->user_email:set_value('email');?>"  class="form-control inputgroups" aria-describedby="passwordHelpInline" placeholder="Email">
                                <div class="text-danger">
                                    <?= session('errors.email') ?>
                                </div>
                              </div>
                              
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputPassword6" class="col-form-label profilelabel">Mobile No</label>
                              </div>
                              <div class="col-md-12">
                                <input type="number" id="inputPassword6" name="mobile" value="<?=!empty($users->user_mobile)?$users->user_mobile:set_value('mobile');?>" class="form-control inputgroups" aria-describedby="passwordHelpInline" placeholder="Mobile" >
                                <div class="text-danger">
                                    <?= session('errors.mobile') ?>
                                </div>
                              </div>
                              
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputPassword6" class="col-form-label profilelabel">Address</label>
                              </div>
                              <div class="col-md-12">
                                <input type="Address" id="inputPassword6" name="address" class="form-control inputgroups" aria-describedby="passwordHelpInline" placeholder="Address" value="<?=!empty($users->user_address)?$users->user_address:set_value('address');?>">
                                <div class="text-danger">
                                    <?= session('errors.address') ?>
                                </div>
                              </div>
                              
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="inputPassword6" class="col-form-label profilelabel">Upload Profile Image</label>
                              </div>
                              <div class="col-md-12">
                                <input type="file" id="" name="image" class="form-control inputgroups"  placeholder="Upload Profile Image" >
                                <div class="text-danger">
                                    <?= session('errors.image') ?>
                                </div>
                              </div>
                              
                        </div>
                       
                        <div class="row">
                            <div class="col-md-12">

                            </div>
                            <div class="col-md-12 text-left">
                                <button type="submit" class="btn" style="width: 160px;
                                height: 48px;
                                left: 720px;
                                top: 751px;
                                
                                background: #E71A2D;
                                border-radius: 4px;
                                font-weight: 600;
font-size: 16px;
line-height: 20px;

color: #FFFFFF;padding: 12px;
" >UPDATE PROFILE</button>
                            </div>
                        </div>
                        
                    </form>
					</div>
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
$(document).on("change", "input[name='image']", function(){ 
    var white = $(this);
    var file = this.files[0];
    var fileType = file["type"];
    var validImageTypes = ["image/gif", "image/jpg", "image/jpeg", "image/png"];
    if ($.inArray(fileType, validImageTypes) < 0) {
        alert('Invalid file type. Only jpg, jpeg, png files are allowed');
        return false;
    }
    var name = this.files[0].name;
    var form_data = new FormData();

    form_data.append("image", this.files[0]);
    $.ajax({
        dataType: 'json',
        url:"<?=base_url();?>/uploads/profile_image",
        method:"POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend:function(){
           
        },   
        success:function(data)
        {
            if(data.status){
             var src = "<?=base_url();?>/public/uploads/images/profile/<?=session()->get('user_id');?>/"+data.filename;
             $('.pro-img').attr('src',src);
            }else{
                alert(data.message);
            }
         
        }
    });
});
$(document).on("click", ".update-pro-img", function(){ 
    $("input[name='image']").click();
});

</script>
<?=$this->endSection();?>