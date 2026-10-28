<section class="footer-sec1 p-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <ul style="list-style: none;" >
                    <h5>Popular Links</h5>
                    <li class="mt-2" ><a href="<?=base_url();?>/organizers"> <i class="fa-solid fa-angle-right mr-2"></i> Organizers</a></li>
                    <li class="mt-2"><a href="<?=base_url();?>/privacy_policy"> <i class="fa-solid fa-angle-right mr-2"></i>Privacy Policy</a></li>
                    <li class="mt-2"><a href="<?=base_url();?>/about_us"><i class="fa-solid fa-angle-right mr-2"></i>About Us</a></li>
                    <li class="mt-2" ><a href="<?=base_url();?>/faqs"><i class="fa-solid fa-angle-right mr-2"></i>FAQ’s</a></li>
                    <li class="mt-2"> <a href="<?=base_url();?>/terms_condition"><i class="fa-solid fa-angle-right mr-2"></i>Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-md-6" style="margin: auto;
            text-align: center;">
			<img src="<?=base_url();?>/public/assets/img/footer-logo.png" class="footer-logo-img1" alt="">
              </div>

            <div class="col-md-3">
                <div class="row">
                    <ul class="foot-ctc" style="list-style: none;">
                        <h5>Contact Us</h5>
                        <li class="mt-3">
                            <div class="row">
                                <div class="col-md-2 col-2 p-0 m-auto text-center">
                                    <i class="fas fa-phone-alt hotelicon" style="background: #e71a2d;
                            color: white;
                            padding: 12px;
                            border-radius: 50%;"></i>
                                </div>
                                <div class="col-md-10 col-10 p-0 m-auto">
                                    <p class="addresstext"><?=!empty($contact_us->phone)?$contact_us->phone:'';?></p>
                                </div>
                            </div>
                        </li>
                        <li class="mt-3">
                            <div class="row">
                                <div class="col-md-2 col-2 p-0 m-auto text-center">
                                    <i class="fa-solid fa-envelope hotelicon mr-2"></i>
                                </div>
                                <div class="col-md-10 col-10 p-0 m-auto">
                                    <p class="addresstext"><?=!empty($contact_us->email)?$contact_us->email:'';?> </p>
                                </div>
                            </div>
                        </li>
                        
                        <li class="mt-3">
                            <div class="row">
                                <div class="col-md-2 col-2 p-0 m-auto text-center">
                                    <i class="fas fa-map-marker-alt hotelicon mr-2"></i>
                                </div>
                                <div class="col-md-10 col-10 p-0 m-auto">
                                    <p class="addresstext"><?=!empty($contact_us->address)?$contact_us->address:'';?></p>
                                </div>
                            </div>
                        </li>
                        
                        
                        
                    </ul>
                </div>
            </div>
			  <div class="col-md-12">
                <div class="row">
				<p class="copyright1"><span class="text-center text-sm-left d-block d-sm-inline-block">Copyright © 2026 <a href="#" class="text-muted" target="_blank">Book My Match</a>. All rights reserved. Powered by <a href="#" class="text-muted" target="_blank">Noombal Tech Pvt Ltd.</a></span></p>
				</div>
				</div>
        </div>
    </div>
</section>