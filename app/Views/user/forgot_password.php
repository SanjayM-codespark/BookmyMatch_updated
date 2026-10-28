<?=$this->extend('base');?>
<?=$this->section('main_content');?>
<section style="min-height: 70vh;">
    <div class="container-fluid">
        <div class="row">
           <div class="col-md-6 text-center logincolums login-sec1" style="padding:0!important;">
            <div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item sign-back singin-back active" style="background-image: url(<?=base_url();?>/public/assets/img/login1.jpg);">
     
    </div>
     <div class="carousel-item  singin-back sign-back" style="background-image: url(<?=base_url();?>/public/assets/img/login2.jpg);">
     
    </div>
  </div>
 
</div>
               <!-- <a href="<?=base_url();?>"><img src="<?=base_url();?>/public/assets/img/logo.png" alt="" style="width: 50%;" ></a>-->
            </div>
            <div class="col-md-6 pt-5 logincolumn2 login-sec2">
                <div class="alert alert-danger" id="error" style="display: none;"></div>
                <div class="alert alert-success" id="successAuth" style="display: none;"></div>
				<div class="row">
                        <div class="col-md-12 text-center logoinmobile">
                            <a href="<?=base_url();?>"><img src="<?=base_url();?>/public/assets/img/logo.png" alt="" style="width: 35%;" ></a>

                        </div>
                    </div>
                <div class="forpass">
                <form>
                    
                    <div class="mb-3 text-center">
                        <h5 style="font-size: 32px; text-align: center; font-weight: 600; "  ><span class="viewchange">Forget</span> <span style="color:#E71A2D;">Password
                        </span></h5>

                        <p class="viewchange">Enter your Email Id and get
                            OTP Verification</p>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-7 ">
						<div class="forgot-sec1">
                            <div class="mb-3" style="position: relative;">

                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">@</span>
                                  </div>
                                  <input type="email" class="form-control inputgroups" id="number" name="email" 
                                        placeholder="Email Address">
                                </div>
                                
                            </div>
                             <div id="recaptcha-container" style="width:100%;"></div>
                            
                            <div class="mb-1 text-center ">
    
                                <a  style="color:white;font-size: 18px;padding: 10px;"  class="btn btn-primary loginbutton" onclick="sendOTP();">GET OTP</a>
                                

                            </div>
							</div>
                        </div>
                    </div>
                </form>
                </div>

                <div class="mb-5 mt-5 vepcode" style="display: none;">
                <div class="alert alert-success" id="successOtpAuth" style="display: none;"></div>
                    <div class="row justify-content-center">
                        <div class="col-md-7">
						<div class="forgot-sec1">
                        <div class="mb-3 text-center">
                            <h5 style="font-size: 32px; text-align: center; font-weight: 600; "  ><span class="viewchange"></span> <span style="color:#E71A2D;"> Reset Your Password
                            </span></h5>
                        </div>

                        <form>
                            <div class="form-group">
                                <input type="text" id="verification" class="form-control inputgroups" placeholder="OTP">
                            </div>
                            <div class="form-group">
                                <input type="password" id="password" name="password" class="form-control inputgroups" placeholder="New Password">
                            </div>
                            <div class="form-group">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control inputgroups" placeholder="Confirm New Password">
                            </div>
                            <input type="hidden" name="mob" id="mob" value="">
                            <button type="button" class="btn btn-info mt-3" onclick="verify()">Reset Password</button>
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

        function sendOTP() {

            var mobile = $("#number").val();
            var countrycode = "";
            $('#mob').val(mobile);
            var number = mobile;
            if(mobile == ''){
                $("#error").text('Email Id is required');
                $("#error").show();return;
            }
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(mobile)) {
                $("#error").text('Please enter a valid Email Id');
                $("#error").show();return;
            } 
            $.ajax({
                type: "POST",
                data: { 'email':mobile},
                url: base_url+"user/forgot_password_email",
                success: function( html ) {
                    if(html == 'success'){
                    //    firebase.auth().signInWithPhoneNumber(number, window.recaptchaVerifier).then(function (confirmationResult) {
                    //         window.confirmationResult = confirmationResult;
                    //         coderesult = confirmationResult;
                    //         console.log(coderesult);
                            var htm="An Email with a One Time Password (OTP) has been sent to your email address: "+number;
                            $("#successAuth").text(htm);
                            $("#successAuth").show();
                            $('.vepcode').show();
                            $('.forpass').hide();
                            $("#error").hide();
                        // }).catch(function (error) {
                        //     $("#error").text(error.message);
                        //     $("#error").show();
                        //     $('.vepcode').hide();
                        //     $('.forpass').show();
                        // });
                    } else {
                        $("#error").text('Invalid Email Id');
                        $("#error").show();return; 
                    }
                }
            });
        }

        function verify() { 
            var uri = "<?php echo base_url(); ?>/reset_password";
            var redi = "<?=base_url()?>/login";
            $("#error,#successAuth,#successOtpAuth").hide();
            var code = $("#verification").val();
            var password = $("#password").val();
            var confirm_password = $("#confirm_password").val();

            if(code == ''){
                $("#error").text('OTP is required');
                $("#error").show();return;
            }
            if(password == ''){
                $("#error").text('New Password is required');
                $("#error").show();return;
            }
            if(confirm_password == ''){
                $("#error").text('Confirm New Password is required');
                $("#error").show();return;
            }
            if (password.length < 6) {  
                $("#error").text('New Password must be at least 6 characters long');
                $("#error").show();return;
            } 
            var mob = $('#mob').val();
            if(password !='' && confirm_password !='' && code !=''){
                if(password == confirm_password){
                    $.ajax({
                        type: "POST",
                        data: { 'email':mob, 'otp':code, 'pass': password},
                        url: uri,
                        success: function( html ) {
                            if(html == 'success'){
                                window.location.href = redi;
                                $("#successOtpAuth").text("Password reset successfully");
                                $("#successOtpAuth").show();
                                $("#successAuth").show();
                            } else {
                                $("#error").text('OTP is invalid. Enter correct OTP');
                                $("#error").show();
                            }
                        }
                    }); 
                } else {
                    $("#error").text('New Password and Confirm New Password is not same').show();
                }
            } else {
                $("#error").text('Need all fields').show();
            }
        }
    </script>
<?=$this->endSection();?>