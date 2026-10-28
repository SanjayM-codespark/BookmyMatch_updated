<?= $this->extend('base'); ?>
<?= $this->section('pageCss'); ?>
<style type="text/css">
    .pagnav {
        margin: 0px auto;
    }

    .pagination li a {
        color: red;
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section style="background: #EAEFFD;">
    <div class="slider-sec1">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php if (!empty($home_banners)) {
                    foreach ($home_banners as $key => $value) { ?>
                        <div class="carousel-item <?= ($key == 0) ? 'active' : ''; ?>">
                            <img class="d-block w-100" src="<?= base_url(); ?>/public/uploads/images/<?= $value->home_banner_web_image; ?>" alt="First slide">
                        </div>
                <?php }
                } ?>
            </div>

            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
   
</section>
<section class="py-5" style="background: #10141e;">
    <div class="container">
         <?php if (!empty($messages)) { ?>
            <h4 style="color:white;">Notifications</h4>
                        <table id="myTable" class="table pay-table table-bordered table-striped"style="color:white;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message) { ?>
                        <tr>
                            <td><?= $message->msg_id; ?></td>
<td><?= date('d-m-Y', strtotime($message->msg_created_at)); ?></td>
                            <td><?= $message->msg_title; ?></td>
                            <td><?= $message->msg_desc; ?></td>
                           
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
        
        <div class="row text-white text-center mb-5">
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
                <h1 style="font-weight: 600; font-size: 24px;">
                    <?php
                    if (isset($_GET['status']) && ($_GET['status'] == 'completed')) {
                        echo 'Recently Completed';
                    } else {
                        echo 'Live and Upcoming';
                    }
                    ?>
                </h1>
                <h2 style="font-weight: 600; font-size: 20px;">Discover the Sports Events We Offer you</h2>
                <div class="d-flex justify-content-center">
                    <span>
                        <hr style="width: 80px;">
                    </span> <span><img src="<?= base_url(); ?>/public/assets/icons/Group_22.png" alt="" class="mx-2"></span><span>
                        <hr style="width: 80px;">
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tourn-drop-sec">
                    <select name="cars" class="tour-select" id="cars" onchange="location = this.value;">
                        <option value="<?= base_url(); ?>">Tournaments</option>
                        <option value="<?= base_url(); ?>organizers">Organization</option>
                    </select>
                </div>
            </div>
        </div>
        <?php if (!empty($tournaments)) { ?>
            <div class="row">
                <?php foreach ($tournaments as $data) {
                    $now = strtotime(date('Y-m-d H:i:s'));
                    $startdate = strtotime($data->listing_start_date);
                    $enddate = strtotime($data->listing_end_date);
                    if ($startdate < $now) {
                        $match_status = "Completed";
                    } else {
                        $match_status = "Upcoming";
                    }
                    if ($startdate < $now && $enddate > $now) {
                        $match_status = "Live";
                    } ?>
                    <div class="col-md-6 mb-2">
                        <?php if (in_array(session()->get('role_id'), [1, 2, 3,4])): ?>
                            <a href="<?= base_url(); ?>/view_tournament_details/<?= $data->listing_id; ?>" style="text-decoration: none;">
                            <?php else: ?>
                                <a href="#" style="text-decoration: none;" onclick="return false;">
                                <?php endif; ?>                            <div class="card turnamentcards p-4 m-auto">
                                <div class="row ipadrow">
                                    <div class="col-md-4 p-0">
                                        <?php if (!empty($data->listing_image)) { ?>
                                            <img src="<?= base_url() . "/public/uploads/listings/images/$data->listing_user_id/$data->listing_image" ?>" class="tuorcards" alt="listing_image">
                                        <?php } else { ?>
                                            <img src="<?= base_url() . '/public/assets/img/tournament_pic.png' ?>" class="tuorcards" alt="listing_image">
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-8 cardscolumns">
                                        <div class="list-section1">
                                            <div>
                                                <span class="app-txt1"><?= $data->sports_name; ?></span>
                                            </div>
                                            <h5 class="hotelname text-truncate"><?= $data->listing_title; ?></h5>
                                            <h3 class="mt-3">
                                                <i class="fa-solid fa-clock"></i>
                                                <span class="hoteltime"><?= date('H:i A', strtotime($data->listing_start_date)); ?> - <?= date('H:i A', strtotime($data->listing_end_date)); ?></span>
                                            </h3>
                                            <div class="mt-3">
                                                <i class="fa fa-calendar"></i>
                                                <span class="hoteltime"><?= date('F d', strtotime($data->listing_start_date)); ?> - <?= date('F d', strtotime($data->listing_end_date)); ?></span>
                                            </div>
                                            <div class="mt-3 d-flex">
                                                <i class="fas fa-map-marker-alt  mr-2"></i>
                                                <span class="hoteladd text-truncate "><?= $data->listing_address; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <?php $pager = service('pager');
            echo $pager->makeLinks($page, $perPage, $total, 'tourney_default_full'); ?>
        <?php } else { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="jumbotron text-center">
                        <h5>There is no matches at the movement..</h5>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<?= $this->endSection(); ?>