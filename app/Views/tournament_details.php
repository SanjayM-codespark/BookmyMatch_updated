<?= $this->extend('base'); ?>
<?= $this->section('main_content'); ?>
<?php
if (!empty($tournaments->listing_image)) {
    $img = base_url() . "/public/uploads/listings/images/$tournaments->listing_user_id/$tournaments->listing_image";
} else {
    $img = base_url() . '/public/assets/img/tournament_pic.png';
}
?>
<section class="pricecardsection">
    <div class="container">
        <div class="row">
            <div class="col-md-6 tour-img-sec text-center">
                <img src="<?= $img; ?>" alt="" class="img-fluid" style="width:100%;">
            </div>
            <div class="col-md-6 text-white">
                <div class="list-section2">
                    <div>
                        <h3 class="app-txt1"><?= !empty($tournaments->sports_name) ? $tournaments->sports_name : ''; ?></h3>
                    </div>
                    <h2 class="desc-title1"><?= !empty($tournaments->listing_title) ? $tournaments->listing_title : ''; ?></h2>
                    <h4>
                        <span class="list-dis"><i class="fa-solid fa-clock"></i></span><span class="list-dis"><?= date('H:i A', strtotime($tournaments->listing_start_date)); ?> - <?= date('H:i A', strtotime($tournaments->listing_end_date)); ?></span>
                    </h4>
                    <h4>
                        <span class="list-dis"><i class="fa fa-calendar"></i></span><span class="list-dis"> <?= date('F d', strtotime($tournaments->listing_start_date)); ?> - <?= date('F d', strtotime($tournaments->listing_end_date)); ?></span>
                    </h4>
                    <h4>
                        <span class="list-dis"><i class="fas fa-map-marker-alt mr-2"></i></span><span class="list-dis"><?= $tournaments->listing_address; ?></span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="row d-block text-white text-center mobile-mar pt-3 pb-3 mt-5">
            <div class="col-md-12">
                <h1 style="font-weight: 600;font-size: 24px;">Event Categories</h1>
                <!-- <h2 style="font-weight: 600;font-size: 20px;">What you can do with PVR Cricket Academy</h2>
            <div class="d-flex justify-content-center">
                <span><hr style="width: 80px;"></span> <span><img src="<?= base_url(); ?>/public/assets/icons/Group_22.png" alt="" class="mx-2"></span><span ><hr style="width: 80px;"></span>
            </div>-->
            </div>
        </div>
        <div class="row booking-sec1 justify-content-center">
            <?php
            $results = $tourneyModel->get_events($tournaments->listing_id);
            if (!empty($results)) {
                foreach ($results as $row) {
            ?>
                    <div class="col-md-3 pricemenu">
                        <div class="card bookingcard text-left p-3">
                            <?php if (!empty($row->event_image)) { ?>
                                <img class="mb-3" src="<?= base_url() . '/public/uploads/images/events/' . $row->event_image ?>" alt="Event Image">
                            <?php } else { ?>
                                <img class="mb-3" src="<?= base_url() . '/public/assets/img/event1.png' ?>" alt="Event Image">
                            <?php } ?>
                            <h2 class="bookingh2"><?= $row->event_name; ?></h2>
                            <h4 class="bookingh4">Type: <?= $row->listing_match_type; ?></h4>
                            <h3 class="bookingh3"><img src="assets/icons/" alt=""><img src="<?= base_url(); ?>/public/assets/icons/rupee_icon.png" alt=""> <?= $row->listing_event_fee; ?></h3>
                            <?php
                            $now = strtotime(date('Y-m-d H:i:s'));
                            $startdate = !empty($row->listing_event_lastbook_date)
                                ? strtotime($row->listing_event_lastbook_date)
                                : null;
                            if ($startdate && $now <= $startdate) { ?>
                                <a href="<?= base_url(); ?>/book/<?= $tournaments->listing_id; ?>/<?= $row->listing_event_id; ?>">
                                    <h5 class="bookingh5">BOOK NOW</h5>
                                </a>
                            <?php } else { ?>
                                <h5 class="text-danger">BOOKING CLOSED</h5>
                            <?php } ?>
                        </div>
                    </div>
            <?php }
            } ?>
        </div>
    </div>
</section>

<section class="py-3 pt-0 rules-back1">
    <div class="container p-4 pt-0" style="padding-top: 0px !important;">
        <div class="row justify-content-center">
        </div>
        <div class="row justify-content-center pt-0 p-4" style="padding-top:0px !important;">
            <div class="col-md-6 bor-right1 p-0">
                <div class="desc-left">
                    <h1 class="rule-head1" style="">Tournament Description</h1>
                    <p><?= !empty($tournaments->listing_details) ? $tournaments->listing_details : ''; ?></p>
                </div>
            </div>
            <div class="col-md-6 p-0">
                <div class="rule-sec2 text-white">
                    <h1 class="rule-head1">Rules & Regulations</h1>
                    <?= !empty($tournaments->listing_rules) ? $tournaments->listing_rules : ''; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="org-details1">
    <div class="container-fluid p-0">
        <div class="row no-gutters" style="background:#202739;">
            <div class="col-md-5 pt-5 pb-5 p-3 m-auto">
                <h6 class="org-head1">Organizer Details</h6>
                <ul style="list-style: none;">
                    <li class="mt-3">
                        <div class="row">
                            <div class="col-md-2 col-2 p-0 m-auto text-center">
                                <i class="fas fa-map-marker-alt hotelicon mr-2" style="padding:14px;"></i>
                            </div>
                            <div class="col-md-10 col-10 p-0 m-auto">
                                <p class="addresstext"><?= $tournaments->listing_address; ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="mt-3">
                        <div class="row">
                            <div class="col-md-2 col-2 p-0 m-auto text-center">
                                <i class="fas fa-phone-alt hotelicon" style="margin-left: -10px;"></i>
                            </div>
                            <div class="col-md-10 col-10 p-0 m-auto">
                                <p class="addresstext"><?= $tournaments->user_mobile; ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="mt-3">
                        <div class="row">
                            <div class="col-md-2 col-2 p-0 m-auto text-center">
                                <i class="fa-solid fa-envelope hotelicon mr-2"></i>
                            </div>
                            <div class="col-md-10 col-10 p-0 m-auto">
                                <p class="addresstext"><?= $tournaments->user_email; ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="mt-3">
                        <div class="row">
                            <div class="col-md-12 mob-mar1">
                                <a href="<?= base_url(); ?>/view_all_tournaments/<?= $tournaments->listing_user_id; ?>"><button class="btn-btn-primary makepaymentbutton1">SIMILAR TURNAMENTS</button></a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-md-7">
                <iframe src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d31096.873468665643!2d80.20234987762576!3d13.028718523455955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sNo%2033%2C%20Lakshminarayan%20Street%2C%20Mamabalam%2C%20Chennai-600087!5e0!3m2!1sen!2sin!4v1646821508706!5m2!1sen!2sin" width="1010" height="430" style="border:0;
                width: 100%;
            " allowfullscreen="" loading="lazy"></iframe>
            </div>
            <div class="col-md-12">
            </div>
        </div>
        <div class="d-flex pt-3 justify-content-center">
            <span>
                <hr style="width: 80px;">
            </span> <span><img src="http://localhost/tournament/public/assets/icons/Group_22.png" alt="" class="mx-2"></span><span>
                <hr style="width: 80px;">
            </span>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script>
    (function() {
        const second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24;

        let offer = starttime,
            countDown = new Date(offer).getTime(),
            x = setInterval(function() {

                let now = new Date().getTime(),
                    distance = countDown - now;

                document.getElementById("days").innerText = Math.floor(distance / (day)),
                    document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
                    document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                    document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

                //do something later when date is reached
                if (distance < 0) {
                    let headline = document.getElementById("headline"),
                        countdown = document.getElementById("countdown"),
                        content = document.getElementById("content");

                    headline.innerText = "Ohhh no! Offer has ended";
                    countdown.style.display = "none";
                    content.style.display = "block";

                    clearInterval(x);
                }
                //seconds
            }, 0)
    }());
</script>
<?= $this->endSection(); ?>