<?= $this->extend('base'); ?>
<?= $this->section('main_content'); ?>
<section style="min-height: 70vh;">
    <div class="container-fluid">
           

            <div class="container-fluid pt-5 logincolumn2 login-sec2">
                <div class="row">
                    <div class="col-md-12 text-center logoinmobile">
                        <a href="<?= base_url(); ?>"><img src="<?= base_url(); ?>/public/assets/img/logo.png" alt="" style="width: 35%;"></a>

                    </div>
                </div>
                <div class="signin-section1">
                    <h5 style="font-size: 32px; text-align: center; font-weight: 600; " class="mb-1"><span class="viewchange"></span><span style="color:#E71A2D;"> Book My
                            Match  </span> Admin Login</h5><br>                 


                    <form id="loginform" action="<?php echo current_url(); ?>" method="post">
                                     <input name="role" value="1" type="hidden">

                        <div class="row justify-content-center">

                            <div class="col-md-4  sign-sec3">
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

                                <?php if (session()->has('errors')) : ?>
                                    <ul class="alert alert-danger">
                                        <?php foreach (session('errors') as $error) : ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                <?php endif ?>
                                <div class="mb-3" style="position: relative;">

                                    <input name="mobile" type="text" value="<?php echo isset($_COOKIE['rnavaum']) ? $_COOKIE['rnavaum'] : set_value('mobile'); ?>" class="form-control inputgroups" id="exampleInputEmail1"
                                        aria-describedby="emailHelp" placeholder="Mobile">

                                </div>
                                <div class="mb-1" style="position: relative;">

                                    <input name="password" value="<?php echo isset($_COOKIE['rpanaivelam']) ? $_COOKIE['rpanaivelam'] : ''; ?>" type="password" class="form-control  inputgroups" id="exampleInputPassword1" placeholder="Password">

                                </div>
                                <!--<div id="recaptcha-container"></div>-->
                                <div class="mb-1 text-center">

                                    <a href="<?= base_url(); ?>/user/forgot_password" class="" style="color:#E71A2D;font-size: 18px;">Forgot Password?</a>
                                </div>
                                <div class="mb-1 text-center ">
                                    <button type="submit" class="btn btn-primary loginbutton">Login</button>
                                </div>

                            </div>
                        </div>

                    </form>
                    <div class="text-center">
                        <p class="viewchange sign-txt1">New to Book My Match? <a href="<?= base_url(); ?>/signup" style="color:#E71A2D;">Create Account</a> </p>
                    </div>
                </div>
            </div>
    </div>

</section>
<?= $this->endSection(); ?>
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
    window.onload = function() {
        render();
    };

    function render() {
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container');
        recaptchaVerifier.render();
    }
</script>
<style>
    .nav-link {
        color: white !important;
        font-size: 28px;
    }

    .nav-link.active {
        color: red !important;
        background-color: #212529 !important;
    }
</style>
<script>
    function changeTabColor(tab) {
        var participantTab = document.getElementById('participants-tab');
        var organizerTab = document.getElementById('organizer-tab');

        participantTab.classList.remove('active');
        organizerTab.classList.remove('active');

        if (tab === 'participants') {
            participantTab.classList.add('active');
        } else if (tab === 'organizer') {
            organizerTab.classList.add('active');
        }
    }
</script>

<?= $this->endSection(); ?>