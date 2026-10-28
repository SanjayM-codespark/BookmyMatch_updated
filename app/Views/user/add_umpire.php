<?=$this->extend('base');?>
<?=$this->section('main_content');?>
<section style="min-height: 70vh;">
    <div class="container-fluid">
        <div class="row">

    <div class="col-md-6 text-center logincolums" style="padding:0 !important;">
	<div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item sign-back active" style="background-image: url(<?=base_url();?>/public/assets/img/login1.jpg);">
     
    </div>
     <div class="carousel-item sign-back" style="background-image: url(<?=base_url();?>/public/assets/img/login2.jpg);">
     
    </div>
  </div>
 
</div>
             <!--   <a href="<?=base_url();?>"><img src="<?=base_url();?>/public/assets/img/logo.png" alt="" style="width:50%; position: sticky; top: 29%; "  ></a>-->
            </div>
            <div class="col-md-6 pt-4 logincolumn2">
               
                    <div class="row">
                        <div class="col-md-12 text-center logoinmobile">
                            <a href="<?=base_url();?>"><img src="<?=base_url();?>/public/assets/img/logo.png" alt="" style="width:35%;" ></a>

                        </div>
                    </div>
					
					<div class="signup-section1">
                    <h5 style="font-size: 32px; text-align: center; font-weight: 600; " class="mb-2"><span class="viewchange"></span><span
                            style="color:#E71A2D;">Your Account</span></h5>

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

                    <ul class="nav nav-tabs justify-content-center mb-3  border-0" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation" style="width: 142px;
                        text-align: center;">
                            <a class="nav-link viewchange <?=!empty($user_reg)?'active':'';?>" id="Participents-tab"  href="<?=base_url();?>/signup"
                                role="tab" aria-controls="Participents" aria-selected="<?=!empty($user_reg)?'true':'false';?>">Participant</a>
                        </li>
                        <li class="nav-item" role="presentation" style="width: 142px;
                        text-align: center;">
                            <a class="nav-link <?=!empty($user_reg)?'':'active';?>" id="Orgnizer-tab"  href="<?=base_url();?>/organizer/sign_up"
                                role="tab" aria-controls="home" aria-selected="<?=!empty($user_reg)?'false':'true';?>">Organizer</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade <?=!empty($user_reg)?'show active':'';?>" id="Participents" role="tabpanel"
                            aria-labelledby="Participents-tab">
                            <form class="_sfrm" id="signup-form">
                            <div class="row justify-content-center">
                                <div class="col-md-7  sign-sec3">
                                    <div class="mb-3" style="position: relative;">

                                        <input type="text" class="form-control inputgroups" name="name" onkeydown="validateInput(event)" id="exampleInputEmail1"
                                            aria-describedby="emailHelp" placeholder="Full Name"
                                            >
                                        <small id="nameError" class="vError text-danger"></small>    
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="email" class="form-control inputgroups" name="email" id="exampleInputEmail1"
                                            aria-describedby="emailHelp" placeholder="Email"
                                            >
                                        <small id="emailError" class="vError text-danger"></small>
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="password" class="form-control inputgroups" id="exampleInputPassword1"
                                            placeholder="Password" name="password">
                                        <small id="passwordError" class="vError text-danger"></small>
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="password" class="form-control inputgroups" id="exampleInputPassword1"
                                            placeholder=" Confrim Password" name="confirm_password">
                                        <small id="confirm_passwordError" class="vError text-danger"></small>
                                    </div>
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                        <span class="input-group-text p-0 m-0" id="basic-addon1">
                                            <select class="form-control"  id="countrycode" style="height: 100%;" name="country_code">
                                            <?php if(!empty($countries)){
                                                foreach($countries as $row){
                                             ?>
                                            <option value="<?=$row->country_mobile_code;?>" <?=($row->country_mobile_code == "91")?'selected':'';?>>+<?=$row->country_mobile_code;?></option>
                                            <?php } } ?>
                                        </select></span>
                                      </div>
                                      <input type="text" class="form-control inputgroups" id="mobile" name="mobile" 
                                            placeholder="Mobile Number">
                                    </div>
                                    <small id="mobileError" class="vError text-danger"></small>

                                    <div id="recaptcha-container"></div>
                                    <small id="g-recaptcha-responseError" class="vError text-danger"></small>
                                    <div class="mb-1 text-center ">

                                        <a class="" style="color:#E71A2D;font-size: 18px;"><button type="button" 
                                                class="btn btn-primary loginbutton _fsbn">SIGNUP</button></a>
                                    </div>
                                    <div class="text-center" >
                                        <p class="viewchange sign-txt1">Already Have an Account? <a href="<?=base_url();?>/login"  style="color:#E71A2D;" >Login</a> </p>
                                       </div>
                                </div>
                            </div>
                            </form>
                            <form class="accver" style="display: none;">
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                    <h3>Account verification</h3>
                                    <p>A text with One time Password(OTP) has been sent to your mobile</p>
                                     <div class="alert alert-danger" id="error" style="display: none;"></div>
                                    <div class="alert alert-success" id="successOtpAuth" style="display: none;"></div>
                                    <div class="form-group">
                                        <input type="text" id="verification" class="form-control inputgroups" placeholder="OTP">
                                    </div>
                                   
                                    <input type="hidden" name="mob" id="mob" value="">
                                    <button type="button" class="btn btn-success mt-3" onclick="verify()">Verify Account</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade <?=!empty($user_reg)?'':'show active';?>" id="Orgnizer" role="tabpanel"
                            aria-labelledby="Orgnizer-tab">
                            <form  id="signup-form" accept="<?=current_url();?>" method="post">
                            <div class="row justify-content-center">
                                <div class="col-md-7  sign-sec3">
                                    <div class="mb-3" style="position: relative;">
                                        <input type="address" class="form-control inputgroups" name="organization_name" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Orgnization Name" value="<?php echo set_value('organization_name'); ?>">
                                         <div class="text-danger">
                                            <?= session('errors.organization_name') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="text" class="form-control inputgroups" id="exampleInputEmail1"
                                            aria-describedby="emailHelp" name="name" placeholder="Username" value="<?php echo set_value('name'); ?>">
                                        <div class="text-danger">
                                            <?= session('errors.name') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="email" class="form-control inputgroups" name="email" id="exampleInputEmail1"
                                            aria-describedby="emailHelp" placeholder="Email" value="<?php echo set_value('email'); ?>">
                                        <div class="text-danger">
                                            <?= session('errors.email') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="password" class="form-control inputgroups" name="password" id="exampleInputPassword1"
                                            placeholder="Password" >
                                        <div class="text-danger">
                                            <?= session('errors.password') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="password" class="form-control inputgroups" name="confirm_password" id="exampleInputPassword1"
                                            placeholder=" Confrim Password" >
                                        <div class="text-danger">
                                            <?= session('errors.confirm_password') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="text" class="form-control inputgroups" name="mobile" id="exampleInputPassword1"
                                            placeholder="Mobile Number" value="<?php echo set_value('mobile'); ?>">
                                        <div class="text-danger">
                                            <?= session('errors.mobile') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="position: relative;">

                                        <input type="text" class="form-control inputgroups " name="address" id="exampleInputPassword1"
                                            placeholder="Address" value="<?php echo set_value('address'); ?>">
                                        <div class="text-danger">
                                            <?= session('errors.address') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="position: relative;">
                                        <select class="form-control custom-select inputgroups" id="country" name="country"
                                            >
                                            <option value="">Country</option>
                                            <?php
                                            if(!empty($countries)){
                                                foreach ($countries as $vt) { ?>
                                                   <option value="<?=$vt->country_id;?>" <?php echo (set_value('country')==$vt->country_id)?" selected=' selected'":""?>><?=ucfirst($vt->country_name);?></option>
                                            <?php    }
                                            }
                                            ?>
                                        </select>
                                        <i class="fa-solid fa-angle-down mailicon"></i>
                                        <div class="text-danger">
                                            <?= session('errors.country') ?>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="position: relative;">
                                        <select class="form-control custom-select inputgroups" id="city" name="city"
                                            >
                                            <option value="" class="text-muted">City</option>
                                            <?php
                                            if(!empty($cities)){
                                                foreach ($cities as $vt) { ?>
                                                   <option value="<?=$vt->city_id;?>" <?php echo (set_value('city')==$vt->city_id)?" selected=' selected'":""?>><?=ucfirst($vt->city_name);?></option>
                                            <?php    }
                                            }
                                            ?>
                                        </select>
                                        <i class="fa-solid fa-angle-down mailicon"></i>
                                         <div class="text-danger">
                                            <?= session('errors.city') ?>
                                        </div>
                                    </div>
                                    

                                    <div class="mb-1 text-center ">

                                        <a href="/" class="" style="color:#E71A2D;font-size: 18px;"><button
                                                class="btn btn-primary loginbutton">SIGNUP</button></a>
                                    </div>
                                    <div class="text-center" >
                                        <p class="viewchange sign-txt1" style="" >Already Have an Account? <a href="<?=base_url();?>/login" style="color:#E71A2D;" >Login</a> </p>
                                       </div>
                                </div>
                            </div>
                            </form>
                        </div>
                        
                    </div>

            </div>
            </div>
           


        </div>
    </div>

</section>
<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
<script>
        var firebaseConfig = {
            apiKey: "AIzaSyA43e2DQcZRkpwmGtJEqWcSLUplS5B9bJw",
            authDomain: "bookmymatch-72d9e.firebaseapp.com",
            projectId: "bookmymatch-72d9e",
            storageBucket: "bookmymatch-72d9e.appspot.com",
            messagingSenderId: "895302275199",
            appId: "1:895302275199:web:d9d1406c035177203d6f4a",
            measurementId: "G-4EB7L9HV34"
        };
        firebase.initializeApp(firebaseConfig);
    </script>
    <script type="text/javascript">
        window.onload = function () {
            render();
        };

        function render() {
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container');
            recaptchaVerifier.render();
        }

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

$(document).ready(function(){
    $("._fsbn").click(function(){ 
        var mobile = $("#mobile").val();
        var countrycode = $("#countrycode").val();
        $('#mob').val(mobile);
        var number = "+"+countrycode+$("#mobile").val();
        var formdata = $('._sfrm').serialize();
        $.ajax({
            method: "POST",
            dataType : 'json',
            data: formdata,
            url: "<?=base_url();?>/sign_up",
            beforeSend: function() {
                var _loadtxt = "<i class='fa fa-refresh fa-spin'></i> Submitting...";
                $('._fsbn').html(_loadtxt);
            },
            success: function( response ) {
                $('#alert-error').addClass('d-none');
                $('.vError').text('');
                $('.form-control').removeClass('is-invalid');
                if(response.success){
                    if(response.redirectURL){
                        firebase.auth().signInWithPhoneNumber(number, window.recaptchaVerifier).then(function (confirmationResult) {
                            window.confirmationResult = confirmationResult;
                            coderesult = confirmationResult;
                            console.log(coderesult);
                            var htm="A text with a One Time Password (OTP) has been sent to your mobile number: "+number;
                            $("#successAuth").text(htm);
                            $("._sfrm").hide();
                            $(".accver").show();
                        }).catch(function (error) {
                            $("._sfrm").hide();
                            $(".accver").show();
                            $("#error").text(error.message);
                        });
                    }
                    $('#alert-success').html(response.message).removeClass('d-none');
                    
                }else if(response.validationError){
                    $.each(response.messages,function(k,v){
                        $('#'+k+'Error').text(v);
                    });
                }else if(response.error){
                    $('#alert-error').html(response.message).removeClass('d-none');
                }

            },
            error: function (xhr, httpStatusMessage, customErrorMessage) {
                if (xhr.status === 500) {
                  $('#alert-error').html(customErrorMessage).removeClass('d-none');
                }
                $('._fsbn').html('Submit');
            },
            complete: function() {
                $('._fsbn').html('Submit');
            }
        }); 
    });
});

function verify() { 
            var uri = "<?php echo base_url(); ?>/account_activation";
            var redi = "<?=base_url()?>/login";
            $("#error,#successAuth,#successOtpAuth").hide();
            var code = $("#verification").val();
            if(code == ''){
                $("#error").text('OTP is required');
                $("#error").show();return;
            }
          
            var mob = $('#mob').val();
            if( code !=''){
                    confirmationResult.confirm(code).then(function (result) {
                        var user = result.user;
                        console.log(user);
                        $.ajax({
                            type: "POST",
                            data: { 'mob':mob},
                            url: uri,
                            success: function( html ) {
                                if(html == 'success'){
                                    window.location.href = redi;
                                }
                            }
                        }); 
                    }).catch(function (error) {
                        //$("#error").text(error.message);
                        $("#error").text('OTP is invalid. Enter correct OTP');
                        $("#error").show();
                    });
            }else{
                $("#error").text('OTP is required').show();
            }
        }
</script>
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
<script>
function validateInput(event) {
    // Check if the pressed key is a number
    if (event.key >= '0' && event.key <= '9') {
        event.preventDefault(); // Prevent the input
    }
}
</script>
<?=$this->endSection();?>