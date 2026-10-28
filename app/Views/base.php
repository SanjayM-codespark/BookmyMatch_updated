<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url(); ?>/public/assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url(); ?>/public/assets/icons/fontawesome-free-6.0.0-web/css/all.css">
    <link rel="stylesheet" href="<?= base_url(); ?>/public/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap" >
    <style>
        @font-face {
            font-family: 'my-font';
            src: url(<?= base_url(); ?>/public/assets/font/Source_Sans_Pro/SourceSansPro-Regular.ttf);
        }

        .app-red {
            color: #E71A2D;
        }
    </style>
    <?php echo $this->renderSection('pageCss'); ?>
    <script>
        var base_url = "<?= base_url(); ?>/";
        var current_url = "<?= current_url() ?>";
    </script>
</head>

<body>
    <?php
    if (!empty($header)) {
        echo view('layouts/header');
    }
    ?>

    <?php echo $this->renderSection('main_content'); ?>
    <!-- Button trigger modal -->
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body p-1">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h6 class="modal-title text-center mt-3">POPULAR CITIES IN INDIA
                    </h6>
                    <?php
                    if (isset($famous_region) && !empty($famous_region)) {
                        $rows = ceil(count($famous_region) / 3);
                        $all_items = array_chunk($famous_region, $rows);
                        echo '<div class="row mx-0">';
                        foreach ($all_items as $div_item) {
                            echo '<div class="col-md-4">';
                            foreach ($div_item as $data) {
                                echo '<div class="py-1"><a href="javascript:void(0);" data-city-id="' . $data->city_id . '" class="select-city text-body">' . $data->city_name . '</a></div>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                    ?>
                    <p class="text-center">
                    <ul class="list-inline text-center">
                        <li class="list-inline-item text-center"><a href="javascript:void(0)" data-country-id="1"
                                class="text-info select-all-ind">Rest of India</a></li>
                        <li class="list-inline-item  text-center"><a href="javascript:void(0)"
                                class="text-info select-global">Global tournaments</a></li>
                    </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (!empty($footer)) {
        echo view('layouts/footer');
    }
    ?>

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->

    <script src="<?= base_url(); ?>/public/assets/js/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url(); ?>/public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url(); ?>/public/assets/icons/fontawesome-free-6.0.0-web/js/fontawesome.min.js"></script>
    <!-- Slick -->
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.withOptions').slick({
                slidesToShow: 6,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 1500,
                arrows: false,
                dots: false,
                pauseOnHover: false,
                responsive: [{
                        breakpoint: 1140,
                        settings: {
                            slidesToShow: 4
                        }
                    }, {
                        breakpoint: 520,
                        settings: {
                            slidesToShow: 1
                        }
                    },
                    {
                        breakpoint: 820,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 900,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 1000,
                        settings: {
                            slidesToShow: 4
                        }
                    }
                ]
            });
        });
    </script>
    <script>
        $(".dropdown dt a").on('click', function() {
            $(".dropdown dd ul").slideToggle('fast');
        });

        $(".dropdown dd ul li a").on('click', function() {
            $(".dropdown dd ul").hide();
        });

        function getSelectedValue(id) {
            return $("#" + id).find("dt a span.value").html();
        }

        $(document).bind('click', function(e) {
            var $clicked = $(e.target);
            if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
        });

        $('.mutliSelect input[type="checkbox"]').on('click', function() {
            var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val(),
                title = $(this).val() + ",";

            if ($(this).is(':checked')) {
                var html = '<span title="' + title + '">' + title + '</span>';
                $('.multiSel').append(html);
                $(".hida").hide();
            } else {
                $('span[title="' + title + '"]').remove();
                var ret = $(".hida");
                $('.dropdown dt a').append(ret);
            }
        });


        /*******************************header section ***********************/
        $(document).on("change", "#sports", function() {
            var sport = $(this).val();
            var tstat = $("#tstatus option:selected").val();

            $.ajax({
                type: "POST",
                data: {
                    'sports': sport,
                    'tstatus': tstat
                },
                url: "<?php echo base_url(); ?>/Ajax/search_tournment",
                success: function(html) {
                    window.location.href = html;
                }
            });
        });

        $(document).on("change", "#tstatus", function() {
            var tstat = $(this).val();
            var sport = $("#sports option:selected").val();

            $.ajax({
                type: "POST",
                data: {
                    'sports': sport,
                    'tstatus': tstat
                },
                url: "<?php echo base_url(); ?>/Ajax/search_tournment",
                success: function(html) {
                    window.location.href = html;
                }
            });
        });

        $(document).on('click', '.select-city', function() {
            var cid = $(this).attr('data-city-id');
            var cname = $(this).text();
            $('#rInput').val(cname);
            $('.region-name').text(cname);
            $.ajax({
                type: "POST",
                data: {
                    cityid: cid,
                    cityname: cname
                },
                url: "<?php echo base_url(); ?>/Ajax/set_city",
                success: function(html) {
                    window.location.href = current_url;
                }
            });

        });
        $(document).on('click', '.select-all-ind', function() {
            var cid = $(this).attr('data-country-id');
            $.ajax({
                type: "POST",
                data: {
                    'countryid': cid
                },
                url: "<?php echo base_url(); ?>/Ajax/all_ind",
                success: function(html) {
                    window.location.href = current_url;
                }
            });

        });

        $(document).on('click', '.select-global', function() {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>/Ajax/set_global",
                success: function(html) {
                    window.location.href = current_url;
                }
            });

        });



        $(document).on('keyup', '#search-input', function(event) {
            var stext = $(this).val();
            if (stext == '') {
                $('.search-list').removeClass('show');
            } else {
                $.ajax({
                    type: "POST",
                    data: {
                        'q': stext
                    },
                    url: "<?php echo base_url(); ?>/Ajax/search_list",
                    success: function(html) {
                        $('.search-list').addClass('show');
                        $('.search-list').html(html);
                    }
                });
            }
        });

        $(document).on("click", ".search-droplist", function() {
            var tstat = $("#tstatus option:selected").val();
            var sport = $("#sports option:selected").val();
            var search = $(this).text();
            $.ajax({
                type: "POST",
                data: {
                    'sports': sport,
                    'tstatus': tstat,
                    'q': search
                },
                url: "<?php echo base_url(); ?>/Ajax/search_tournment",
                success: function(html) {
                    window.location.href = html;
                }
            });
        });

        $(document).on('click', '#search', function(event) {
            var tstat = $("#tstatus option:selected").val();
            var sport = $("#sports option:selected").val();
            var search = $('#search-input').val().trim();

            if (search != '') {
                $.ajax({
                    type: "POST",
                    data: {
                        'sports': sport,
                        'tstatus': tstat,
                        'q': search
                    },
                    url: "<?php echo base_url(); ?>/Ajax/search_tournment",
                    success: function(html) {
                        window.location.href = html;
                    }
                });
            }

        });
        $('#search-input').keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                $('#search').click();
            }
        });

        $(document).on('click', '#search2', function(event) {
            var tstat = $("#tstatus option:selected").val();
            var sport = $("#sports option:selected").val();
            var search = $('#search-input2').val().trim();

            if (search != '') {
                $.ajax({
                    type: "POST",
                    data: {
                        'sports': sport,
                        'tstatus': tstat,
                        'q': search
                    },
                    url: "<?php echo base_url(); ?>/Ajax/search_tournment",
                    success: function(html) {
                        window.location.href = html;
                    }
                });
            }

        });
        $('#search-input2').keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                $('#search2').click();
            }
        });
    </script>
    <?php echo $this->renderSection('pageScript'); ?>
</body>

</html>