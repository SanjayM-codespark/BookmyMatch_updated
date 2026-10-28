<?=$this->extend('base');?>
<?=$this->section('main_content');?>
<section class="py-5" style="background:#10141E;min-height: 70vh;">
        <div class="container">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="d-block text-white text-center mb-5">
                        <h1 style="font-weight: 600; font-size: 24px;">Tournament Organizers</h1>
                        <h2 style="font-weight: 600; font-size: 20px;    font-style: italic;">Discover the Organizers around you</h2>
                        <div class="d-flex justify-content-center">
                            <span>
                                <hr style="width: 80px;">
                            </span> <span>
                                <hr style="width: 80px;">
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tourn-drop-sec">
        			  <select name="cars" class="tour-select" id="cars" onchange="location = this.value;">
        				  <option value="<?=base_url();?>">Tournaments</option>
        				  <option value="<?=base_url();?>/organizers" selected>Organization</option>
        			  </select>
        			  </div>
                </div>
                
                
            </div>
            <div class="row">
                <?php 
                if(!empty($organizers)){
                    foreach ($organizers as $data) {
                ?>
                <div class="col-md-6">
                    <a href="" style="text-decoration: none;">
                        <div class="card mb-4 turnamentcards1 mar-bt">
                            <div class="row  ipadrow p-4">
                                <div class="col-md-4 p-0">
                                    <img src="<?=base_url();?>/public/assets/img/images_2.jpg" class="tuorcards" alt="">
                                </div>
                                <div class="col-md-8 cardscolumns">
								<div class="org-right-sec1 list-section1">
                                <!-- <div>
                                    <span class="live">live</span>
                                    <span class="game">Badminton</span>
                                </div> -->
                                    <h5 class="hotelname1 text-white text-truncate"><?=$data->user_organization_name;?></h5>
                                    <div class="mt-2 row">
                                        <div class="col-md-12">
                                             <span class="dis-tbl"><i class="fa-solid fa-phone mr-2"></i></span>  <span class="hoteltime1 dis-tbl"> <?=$data->user_mobile;?></span>
                                        </div>
                                       
                                    </div>

                                    <div class="mt-2 row">
                                        <div class="col-md-12">
                                             <span class="dis-tbl"><i class="fa-solid fa-envelope mr-2"></i> </span>  <span class="hoteltime1 dis-tbl"><?=$data->user_email;?></span>
                                        </div>
                                    </div>

                                    <div class="mt-2 row ">
                                        <div class="col-md-12">
                                           <span class="dis-tbl"> <i class="fas fa-map-marker-alt  mr-2"></i></span>  <span class="hoteltime1 dis-tbl"><?=$data->user_address;?></span>
                                        </div>
                                    </div>
                                    <!-- <div class="mt-2 mb-3" style="float: right;">
                                        <button class="btn-btn-primary makepaymentbutton2" style="border:none;"><i class="fa-solid fa-location-arrow mr-2"></i>Get Location</button>
                                    </div> -->

                                        <div class="d-flex align-items-center justify-content-between mt-2">
                                            <div>
                                                <?php if($data->user_active) { ?>
                                                    <span style="color: green;">&#9745;</span><span class="ml-3">Verified</span>
                                                <?php } else { ?>
                                                    <span>&#9745;</span><span class="ml-3">Not verified</span>
                                                <?php } ?>
                                            </div>
                                            <button class="btn-btn-primary makepaymentbutton2 mr-3" style="border:none;"><i class="fa-solid fa-location-arrow mr-2"></i>Get Location</button>
                                        </div>
									</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } }else{ ?>
                 <div class="col-md-12">
                    <div class="jumbotron text-center">
                      <h5>There is no data found..</h5>
                    </div>
                </div>
            <?php } ?>
                
            </div>
        </div>
    </section>

<?=$this->endSection();?>
<?= $this->Section('pageScript') ?>
<script>
 
</script>
<?=$this->endSection();?>