<?= $this->extend('base'); ?>
<?= $this->section('pageCss'); ?>
<!-- Custom CSS -->
<style>
    .add_del_btn_outer {
        /* position: absolute;
        right: 10px;
        bottom: 50%;
        margin-top: -12px; */
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('main_content'); ?>
<section class="booking-section1" style="background:#10141E;min-height: 70vh;">
    <div class="container">
        <form class="" id="_sfrm" action="<?= base_url(); ?>/booking_proceed" method="POST">
            <div class="team-details">
                <div class="card event book-card-sel selectioncard">
                    <div class="card-body book-card1">
                        <?php
                        $uri = service('uri');
                        $listing_id = $uri->getSegment(2);
                        $listing_event_id = $uri->getSegment(3);
                        $lsit = $commonModel->select('listings', '*', array('listing_id' => $listing_id));
                        $res = $commonModel->select('listing_events', '*', array('listing_event_id' => $listing_event_id));
                        $result = $commonModel->select('events', '*', array('event_id' => $res->listing_event_title));
                        ?>
                        <h2 style="font-weight: 600; font-size: 24px; line-height: 30px; text-align: center; color: #fff;">
                            <?= !empty($result->event_name) ? $result->event_name : ''; ?>
                        </h2>
                        <p style="font-weight: normal; font-size: 16px; line-height: 20px; text-align: center; color: #fff;">
                            Please fill the below details to particpate in our Event
                        </p>
                        <div style="color: #fff; margin-bottom: 15px;">
                            <span class="text-danger">*</span>Mandatory fields, provide valid data
                        </div>
                        <ul class="list-group">
                            <li class="booking-list1 list-group-item">
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Team Name<span class="text-danger">* <?= $res->listing_event_type ?></span></label>
                                                    <input class="form-control" name="team_name" type="text" value="" placeholder="" required>
                                                    <div class="text-danger errorname">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Country<span class="text-danger">*</span></label>
                                                    <select class="form-control" name="country" placeholder="" onchange="get_states(this.value)" required>
                                                        <option value="">--Select Country--</option>
                                                        <?php
                                                        if (!empty($countries)) {
                                                            foreach ($countries as $mfr) { ?>
                                                                <option value="<?= $mfr->country_id; ?>" <?php echo !empty($city->city_country_id) ? ($city->city_country_id == $mfr->country_id) ? 'selected' : '' : ''; ?>><?= ucfirst($mfr->country_name); ?></option>
                                                        <?php    }
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="text-danger errorcountry">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">State</label>
                                                    <select class="form-control" id="state" name="state" placeholder="" onchange="get_cities(this.value)" required>
                                                        <?php
                                                        if (!empty($states)) {
                                                            foreach ($states as $mfr) { ?>
                                                                <option value="<?= $mfr->state_id; ?>" <?php echo !empty($city->city_state_id) ? ($city->city_state_id == $mfr->state_id) ? 'selected' : '' : ''; ?>><?= ucfirst($mfr->state_name); ?></option>
                                                        <?php    }
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="text-danger errorstate">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">City<span class="text-danger">*</span></label>
                                                    <select class="form-control" id="city" name="city" placeholder="" required>
                                                    </select>
                                                    <div class="text-danger errorcity">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="booking-list1 booking-list2 list-group-item">
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Name<span class="text-danger">*</span></label>
                                                    <input class="form-control" name="name[]" type="text" value="" placeholder="" required>
                                                    <div class="text-danger errorname">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Email</label>
                                                    <input class="form-control" name="email[]" type="text" value="" placeholder="" required>
                                                    <div class="text-danger erroremail">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Mobile<span class="text-danger">*</span></label>
                                                    <input class="form-control" name="mobile[]" type="text" value="" placeholder="" required>
                                                    <div class="text-danger errormobile">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (!empty($lsit->listing_sports_id)) { 
                                                $sports_id = $lsit->listing_sports_id;
                                                if ($sports_id == 4 || $sports_id == 10) { ?>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Club / School Name</label>
                                                            <input class="form-control" name="club[]" type="text" value="" placeholder="">
                                                            <div class="text-danger errorclub">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if (($sports_id == 4) || !empty($res->listing_event_dob)) { ?>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">Date Of Birth</label>
                                                            <input class="form-control" name="dob[]" type="date" value="" placeholder="">
                                                            <div class="text-danger errordob">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if (!empty($res->listing_event_tnba_id) || !empty($res->listing_event_bai_id) || !empty($res->listing_event_other_id)) { ?>
                                                <?php if (!empty($res->listing_event_tnba_id)) { ?>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">TNBA-ID</label>
                                                            <input class="form-control" name="tnba[]" type="text" value="" placeholder="">
                                                            <div class="text-danger errorclub">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if (!empty($res->listing_event_bai_id)) { ?>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">BAI-ID</label>
                                                            <input class="form-control" name="bai[]" type="text" value="" placeholder="">
                                                            <div class="text-danger errorclub">

                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if (!empty($res->listing_event_other_id)) { ?>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="">OTHER-ID</label>
                                                            <input class="form-control" name="other[]" type="text" value="" placeholder="">
                                                            <div class="text-danger errorclub"></div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if($res->listing_event_type == 'doubles') { ?>
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Name<span class="text-danger">*</span></label>
                                                        <input class="form-control" name="name[]" type="text" value="" placeholder="" required>
                                                        <div class="text-danger errorname">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Email</label>
                                                        <input class="form-control" name="email[]" type="text" value="" placeholder="" required>
                                                        <div class="text-danger erroremail">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="">Mobile<span class="text-danger">*</span></label>
                                                        <input class="form-control" name="mobile[]" type="text" value="" placeholder="" required>
                                                        <div class="text-danger errormobile">
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if (!empty($lsit->listing_sports_id)) { 
                                                    $sports_id = $lsit->listing_sports_id;
                                                    if ($sports_id == 4 || $sports_id == 10) { ?>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Club / School Name</label>
                                                                <input class="form-control" name="club[]" type="text" value="" placeholder="">
                                                                <div class="text-danger errorclub">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (($sports_id == 4) || !empty($res->listing_event_dob)) { ?>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">Date Of Birth</label>
                                                                <input class="form-control" name="dob[]" type="date" value="" placeholder="">
                                                                <div class="text-danger errordob">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php if (!empty($res->listing_event_tnba_id) || !empty($res->listing_event_bai_id) || !empty($res->listing_event_other_id)) { ?>
                                                    <?php if (!empty($res->listing_event_tnba_id)) { ?>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">TNBA-ID</label>
                                                                <input class="form-control" name="tnba[]" type="text" value="" placeholder="">
                                                                <div class="text-danger errorclub">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($res->listing_event_bai_id)) { ?>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">BAI-ID</label>
                                                                <input class="form-control" name="bai[]" type="text" value="" placeholder="">
                                                                <div class="text-danger errorclub">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($res->listing_event_other_id)) { ?>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="">OTHER-ID</label>
                                                                <input class="form-control" name="other[]" type="text" value="" placeholder="">
                                                                <div class="text-danger errorclub"></div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if($res->listing_event_type == 'team') { ?>
                                    <div class="add_del_btn_outer">
                                        <button id="addRow" type="button" class="btn btn-success btn-circle addbtn formicons"><i class="fa fa-plus"></i> </button>
                                        <button type="button" id="removeRow" class="btn deletebtn"> <i class="fa-solid fa-trash-can" style="color:#e71a2d;font-size: 20px;"></i>
                                        </button>
                                    </div>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                </div> 
                <!--- End card-->
                
                <div class="row justify-content-center mt-3">
                    <div class="col-md-6">
                        <div class="card selectioncard book-card-sel mb-5">
                            <div class="card-body">
                                <div class="font-weight-bold text-light">Payment Summary</div>
                                <?php
                                $uri = service('uri');
                                $listing_event_id = $uri->getSegment(3);
                                $res = $commonModel->select('listing_events', '*', array('listing_event_id' => $listing_event_id));
                                $result = $commonModel->select('events', '*', array('event_id' => $res->listing_event_title));
                                session()->set('paytotal', $res->listing_event_fee);
                                ?>
                                <input type="hidden" class="amount" name="amount" value="<?= $res->listing_event_fee; ?>">
                                <div class="clearfix"></div>
                                <input type="hidden" name="list_id" value="<?= $uri->getSegment(2); ?>">
                                <input type="hidden" name="event_id" value="<?= $uri->getSegment(3); ?>">
                                <input type="hidden" class="text-light totalamount" name="totalamount" value="<?= !empty($res->listing_event_fee) ? $res->listing_event_fee : ''; ?>">
                                <div class="row mt-2 tf">
                                    <div class="col-md-6 col-6">
                                        <h6 class="text-light">Amount in Multiply</h6>
                                    </div>
                                    <div class="col-md-6 col-6 text-right">
                                        <h6 class="text-light">
                                            <div class="c">
                                                Rs.<?= !empty($res->listing_event_fee) ? $res->listing_event_fee : ''; ?> x 1
                                            </div>
                                        </h6>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6 col-6">
                                        <h6 class="font-weight-bold text-light">Total</h6>
                                    </div>
                                    <div class="col-md-6 col-6 text-right">
                                        <h6 class="font-weight-bold text-light">
                                            <!--<img src="<?= base_url(); ?>/public/assets/icons/Group 124.png" alt="">--> 
                                            Rs. <span class="total"><?= !empty($res->listing_event_fee) ? $res->listing_event_fee : ''; ?></span>
                                        </h6>
                                    </div>
                                </div>

                                <?php if($free_booking == 0) { ?>
                                    <h6 class="mt-4 text-light" style="font-weight: 600;font-size: 14px;line-height: 18px;">Choose Your Payment Method</h6>
                                    <div class="col-md-12 p-0 mb-3">
                                        <div class="form-check inputgroups pl-0" style="    padding: 11px; border-radius: 8px;">
                                            <img src="<?= base_url(); ?>/public/assets/img/upi.png" alt="" width="31">
                                            <label class="form-check-label text-light" for="flexRadioDefault1">
                                                Pay With Razorpay
                                            </label>
                                            <input class="form-check-input" type="radio" name="payment_method" value="razorpay"
                                                id="flexRadioDefault1" style="position: absolute;right: 10px;">
                                            <div class="text-danger errorpayment_method" style="position: relative;top: 8px;"></div>
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <div class="col-xs-12 p-b-20">
                                            <button class="btn makepaymentbutton _fsbn" type="button">MAKE PAYMENT</button>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="form-group text-center">
                                        <div class="col-xs-12 p-b-20">
                                            <button  type="submit">Complete Booking</button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div> 
                        <!--End  card--->
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- ============================================================== -->
<!-- Model -->
<!-- ============================================================== -->
<div class="modal fade" id="myModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-confirm border">
        <div class="modal-content">
            <div class="modal-header">
                <div class="icon-box">
                    <i class="material-icons"></i>
                </div>
                <h4 class="modal-title w-100">Awesome!</h4>
            </div>
            <div class="modal-body">
                <p class="text-center">Your booking has been confirmed.</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-success btn-block" href="<?= base_url('my_payments'); ?>">OK</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->Section('pageScript') ?>
<script>
    $(document).ready(function() {
        $("._fsbn").click(function() {
            $('.errorname,.erroremail,.errormobile,.errorschool,.errordob,.errorpayment_method').html('');
            if (!validate()) {
                return false;
            }
            var free_booking = "<?= !empty($free_booking) ? 1 : 0; ?>";
            if (free_booking == 0) {
                var isValid = $("input[name=payment_method]").is(":checked");
                if (!isValid) {
                    $('.errorpayment_method').html('Choose payment method');
                    return false;
                }
            }

            var formdata = $('#_sfrm').serialize();
            $.ajax({
                method: "POST",
                dataType: 'json',
                data: formdata,
                url: "<?= base_url(); ?>/booking_proceed",
                beforeSend: function() {
                    var _loadtxt = "<i class='fa fa-refresh fa-spin'></i> Submitting...";
                    $('._fsbn').html(_loadtxt);
                },
                success: function(response) {
                    $('#alert-error').addClass('d-none');
                    if (response.success) {
                        if (free_booking == 0) {
                            window.location.href = "<?= base_url('razorpay') ?>";
                        } else {
                            window.location.href = "<?= base_url('book/booking_success') ?>";
                        }
                    } else if (response.validationError) {
                        $.each(response.messages, function(k, v) {
                            $('#' + k + 'Error').text(v);
                        });
                    } else if (response.error) {
                        $('#alert-error').html(response.message).removeClass('d-none');
                    }
                },
                error: function(xhr, httpStatusMessage, customErrorMessage) {
                    if (xhr.status === 500) {
                        $('#alert-error').html(customErrorMessage).removeClass('d-none');
                    }
                    $('._fsbn').html('Proceed');
                },
                complete: function() {
                    $('._fsbn').html('Proceed');
                }
            });
        });

        function updateDeleteButtonState(listGroup) {
            var count = listGroup.children().length - 1; // Calculate count
            if (count === 1) {
                listGroup.find('.deletebtn').prop('disabled', true); // Disable if only one item remains
            } else {
                listGroup.find('.deletebtn').prop('disabled', false); // Enable if more than one item
            }
        }

        // Initial setup on page load
        $(document).ready(function() {
            $('.list-group').each(function() {
                updateDeleteButtonState($(this)); // Initialize for each list-group
            });
        });

        $(document).on("click", ".addbtn, .deletebtn", function() {
            var isAdd = $(this).hasClass("addbtn"); // Check if the clicked button is an "addbtn"
            var listGroup = $(this).parents('.list-group');
            var bookingList = $(this).parents('.booking-list2');
            
            if (isAdd) {
                bookingList.clone().appendTo(listGroup).find('input').val(''); // Clone the current item and append it
            } else {
                bookingList.remove(); // Remove the current item
            }

            // Update count after add or delete
            var count = listGroup.children().length - 1; // Adjust count after operation
            var event = "<?= $uri->getSegment(3); ?>";
            var tourny = "<?= $uri->getSegment(2); ?>";

            // AJAX request to update amounts
            $.ajax({
                method: "POST",
                dataType: 'json',
                data: {
                    'counts': count,
                    'tourny_id': tourny,
                    'event_id': event
                },
                url: "<?= base_url(); ?>/book/cal_amount",
                success: function(response) {
                    if (response.success) {
                        var amount = $('.amount').val();
                        var amountText = 'Rs.' + amount + "x" + count;
                        var totalAmount = amount * count;

                        $('.tf .c').html(amountText);
                        $('.tf .a').html("Rs." + totalAmount);
                        $('.total').html(totalAmount);
                        $('.totalamount').val(totalAmount);
                    } else {
                        window.location.href = window.location.href; // Reload page on error
                    }
                },
                error: function(xhr, httpStatusMessage, customErrorMessage) {
                    window.location.href = window.location.href; // Reload page on AJAX error
                }
            });

            // Disable delete button if only one item remains
            if (count === 1) {
                listGroup.find('.deletebtn').prop('disabled', true);
            } else {
                listGroup.find('.deletebtn').prop('disabled', false);
            }
        });
    });

    function validate() {
        var countryInput = document.getElementsByName('country')[0];
        countryInput.style.borderColor = "#ced4da"; // Reset border color
        if (countryInput.value == "") { // Assuming the empty value for 'Please select a country' is an empty string
            countryInput.style.borderColor = "red";
            countryInput.nextElementSibling.innerHTML = "Country is required"; // Assuming there's an element to display the error
            return false;
        } else {
            countryInput.nextElementSibling.innerHTML = ""; // Clear error message
        }

        var cityInput = document.getElementsByName('city')[0];
        cityInput.style.borderColor = "#ced4da"; // Reset border color
        if (cityInput.value == "") { // Assuming the empty value for 'Please select a country' is an empty string
            cityInput.style.borderColor = "red";
            cityInput.nextElementSibling.innerHTML = "City is required"; // Assuming there's an element to display the error
            return false;
        } else {
            cityInput.nextElementSibling.innerHTML = ""; // Clear error message
        }

        var nameInput = document.getElementsByName('name[]');
        for (i = 0; i < nameInput.length; i++) {
            nameInput[i].style.borderColor = "#ced4da";
            if (nameInput[i].value == "") {
                nameInput[i].style.borderColor = "red";
                nameInput[i].nextElementSibling.innerHTML = "Name is required";
                return false;
            }
        }

        var mobileInput = document.getElementsByName('mobile[]');
        for (i = 0; i < mobileInput.length; i++) {
            mobileInput[i].style.borderColor = "#ced4da";

            if (mobileInput[i].value == "") {
                mobileInput[i].style.borderColor = "red";
                mobileInput[i].nextElementSibling.innerHTML = "Mobile number is required";
                return false;
            }

            var phoneno = /^\d{10}$/;
            if (!mobileInput[i].value.match(phoneno)) {
                mobileInput[i].style.borderColor = "red";
                mobileInput[i].nextElementSibling.innerHTML = "Please put 10 digit mobile number";
                return false;
            }
        }

        var clubInput = document.getElementsByName('club[]');

        clubcount = clubInput.length;
        if (clubcount > 0) {
            for (i = 0; i < clubInput.length; i++) {
                clubInput[i].style.borderColor = "#ced4da";

                if (clubInput[i].value == "") {
                    clubInput[i].style.borderColor = "red";
                    clubInput[i].nextElementSibling.innerHTML = "Club/School is required";
                    return false;
                }
            }
        }

        var dobInput = document.getElementsByName('dob[]');

        dobcount = dobInput.length;
        if (dobcount > 0) {
            for (i = 0; i < dobInput.length; i++) {
                dobInput[i].style.borderColor = "#ced4da";

                if (dobInput[i].value == "") {
                    dobInput[i].style.borderColor = "red";
                    dobInput[i].nextElementSibling.innerHTML = "Date of birth is required";
                    return false;
                }
            }
        }

        return true;
    }

    function get_states(id) {
        $.ajax({
            url: "<?= base_url(); ?>/Ajax/get_states",
            method: "POST",
            data: {
                'country': id
            },
            success: function(response) {
                $('#state').html(response);
            },
            error: function(response) {
                alert('Something went wrong. try again');
            }
        });

        $.ajax({
            url: "<?= base_url(); ?>/Ajax/get_cities",
            method: "POST",
            data: {
                'country': id
            },
            success: function(response) {
                $('#city').html(response);
            },
            error: function(response) {
                alert('Something went wrong. try again');
            }
        });
    }
</script>

<?= $this->endSection(); ?>