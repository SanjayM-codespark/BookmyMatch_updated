<section>
    <nav class="navbar menu-sec1 navbar-expand-lg">
        <div class="container" style="max-width: 1258px; position: relative;">
            <?php $scr1 = base_url() . '/public/assets/img/logo.png'; ?>
            <a class="navbar-brand" href="<?= base_url(); ?>"><img src="<?= $scr1; ?>" alt="" width="142"></a>
            <ul class="navbar-nav m-auto ipadview">
                <li class="nav-item active m-auto">
                    <div class="dropdown mx-3">
                        <a class="tour-head1 loca-txt1 text-light text-truncate" type="button" data-toggle="modal"
                            data-target="#exampleModal">
                            <i class="fa-solid fa-location-dot mr-1" style="color:#E71A2D"></i><?= !empty(session()->get('region_name')) ? session()->get('region_name') : 'Global'; ?>
                        </a>
                    </div>
                </li>
                <li class="nav-item active navauto">
                    <select class="form-control custom-select sizeadjust" id="sports" name="sports">
                        <option value="">All Sports</option>
                        <?php if (!empty($sports)) {
                            foreach ($sports as $vt) { ?>
                                <option value="<?= $vt->sports_id; ?>" <?= !empty($_GET['sports']) ? ($_GET['sports'] == $vt->sports_id) ? 'selected="selected"' : '' : ''; ?>><?= ucfirst($vt->sports_name); ?></option>
                        <?php }
                        } ?>
                    </select>
                </li>
                <li class="nav-item  navauto px-2">
                    <select class="form-control custom-select sizeadjust" id="tstatus">
                        <option value="upcoming">Live & Upcoming</option>
                        <option value="completed" <?= !empty($_GET['status']) ? ($_GET['status'] == 'completed') ? 'selected="selected"' : '' : ''; ?>>Recently Completed</option>
                    </select>
                </li>
                <li class="nav-item active navauto">
                    <form class="form-inline my-2 my-lg-0" style="position: relative;">
                        <input class="form-control mr-sm-2 sizeadjust" type="text" id="search-input2" value="<?= !empty($_GET['q']) ? $_GET['q'] : ''; ?>" placeholder="Search"
                            aria-label="Search" style="
                        height: 39px;">
                        <img id="search2" src="<?= base_url(); ?>/public/assets/icons/Group_37873.png" style="position: absolute;
                        right: 11px;
                        width: 20px;" />
                        <div id="search-list" class="dropdown-menu search-list" style="position: absolute;top: 37px;"></div>
                    </form>
                </li>
            </ul>
            <button class="navbar-toggler ml-auto searchbuttons" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fa-solid fa-magnifying-glass" style="color:#fff;"></i>
            </button>
            <button class="navbar-toggler hidetoggle" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fa-solid fa-sliders"></i>
            </button>
            <dl class="dropdown mytoggledrop">
                <dt>
                    <a href="#" class="text-white ">
                        <i class="fa-solid fa-sliders" style="font-size: 20px;"></i>
                    </a>
                </dt>

                <dd>
                    <div class="mutliSelect">
                        <ul style="position: absolute; left: -116px; z-index: 1;">
                            <?php if (!session()->has('logged_in')) { ?>
                                <li>
                                    <a href="<?= base_url(); ?>login">Login</a>
                                </li>
                                <li>
                                    <a href="<?= base_url(); ?>signup">Register</a>
                                </li>
                            <?php } else { ?>
                                <?php
                                $dashboard = false;
                                if (session()->get('role_id') == 1) {
                                    $dash = base_url() . 'admin';
                                    $dashboard = true;
                                } elseif (session()->get('role_id') == 3) {
                                    $dash = base_url() . 'organizer';
                                    $dashboard = true;
                                }

                                if (!empty($dashboard)) { ?>
                                    <li>
                                        <a href="<?= $dash; ?>">Dashboard</a>
                                    </li>
                                <?php } ?>

                                <li>
                                    <a href="<?= base_url() ?>profile">My Profile</a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url() . 'booking_history'; ?>">Booking History </a>
                                </li>
                                <li>
                                    <a href="<?= base_url() ?>change_password">Change Password</a>
                                </li>
                                <li>
                                    <a href="<?= base_url(); ?>logout">Logout</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </dd>
            </dl>
            <div class="collapse navbar-collapse" id="navbarSupportedContent1">
                <ul class="navbar-nav m-auto">
                    <li class="nav-item active m-auto ipaddnone">
                        <div class="dropdown mx-3">
                            <a class=" text-light loca-txt1" type="button" data-toggle="modal" data-target="#exampleModal">
                                <i class="fa-solid fa-location-dot mr-1" style="color:#E71A2D"></i>
                                <span class="text-truncate" style="width: 100px; overflow: hidden;">
                                    <?= !empty(session()->get('region_name')) ? session()->get('region_name') : 'Global'; ?>
                                </span>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item active navauto ipaddnone">
                        <select class="form-control custom-select sizeadjust" id="sports" name="sports">
                            <option value="">All Sports</option>
                            <?php if (!empty($sports)) {
                                foreach ($sports as $vt) { ?>
                                    <option value="<?= $vt->sports_id; ?>" <?= !empty($_GET['sports']) ? ($_GET['sports'] == $vt->sports_id) ? 'selected="selected"' : '' : ''; ?>><?= ucfirst($vt->sports_name); ?></option>
                            <?php }
                            } ?>
                        </select>
                    </li>
                    <li class="nav-item  navauto px-2 ipaddnone">
                        <select class="form-control custom-select sizeadjust" id="tstatus">
                            <option value="upcoming">Live & Upcoming</option>
                            <option value="completed" <?= !empty($_GET['status']) ? ($_GET['status'] == 'completed') ? 'selected="selected"' : '' : ''; ?>>Recently Completed</option>
                        </select>
                    </li>
                    <li class="nav-item active navauto ipaddnone">
                        <form class="form-inline my-2 my-lg-0" style="position: relative;">
                            <input class="form-control mr-sm-2 sizeadjust" type="text" id="search-input" value="<?= !empty($_GET['q']) ? $_GET['q'] : ''; ?>" placeholder="Search"
                                aria-label="Search" style="height: 39px;">
                            <img id="search" src="<?= base_url(); ?>/public/assets/icons/Group_37873.png" style="position: absolute; right: 11px; width: 20px;" />
                            <div id="search-list" class="dropdown-menu search-list" style="position: absolute;top: 37px;"></div>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="loginhide" style="position: absolute; right: 50px;">
                <form class="form-inline navauto my-lg-0">
                    <?php if (!session()->has('logged_in')) { ?>
                        <a href="<?= base_url(); ?>login" class="log-txt1" style="color: white; margin: 10px;">Login</a>
                        <a href="<?= base_url(); ?>signup" class="log-txt1" style="color: white; margin: 10px;">Register</a>
                    <?php } else { ?>
                        <a href="<?= base_url(); ?>/admin/notification" class="d-block icon d-flex justify-content-center align-items-center ">
                            <span class="ion-ios-notifications  notification-sec1">
                                <i class="fa fa-bell" aria-hidden="true"></i>
                            </span>
                        </a>

                        <dl class="dropdown">
                            <dt>
                                <?php
                                if (!empty($users->user_profile_img)) {
                                    $scr = base_url() . '/public/uploads/images/profile/' . $users->user_id . '/' . $users->user_profile_img;
                                } else {
                                    $scr = base_url() . '/public/assets/img/user.png';
                                }
                                ?>
                                <a href="javascript:void(0)">
                                    <img class="hida" src="<?= $scr; ?>" alt=""
                                        style="width: 35px; border-radius: 50%; height: 36px; object-fit: cover; margin-top: 20px;">
                                </a>
                            </dt>

                            <dd>
                                <div class="mutliSelect">
                                    <ul style="position: absolute; left: -95px; z-index: 99;">
                                        <?php if (!empty($dashboard)) { ?>
                                            <li>
                                                <a href="<?= $dash; ?>">Dashboard</a>
                                            </li>
                                        <?php } ?>
                                        <li>
                                            <a href="<?= base_url() ?>profile">My Profile</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url() . 'booking_history'; ?>">Booking History </a>
                                        </li>
                                        <li>
                                            <a href="<?= base_url() ?>change_password">Change Password</a>
                                        </li>
                                        <li>
                                            <a href="<?= base_url(); ?>logout">Logout</a>
                                        </li>
                                    </ul>
                                </div>
                            </dd>
                        </dl>
                    <?php } ?>
                </form>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="formhide">
                    <?php if (!session()->has('logged_in')) { ?>
                        <li><a href="<?= base_url(); ?>login" class="log-txt1" style="color: white;
                        margin: 10px;">Login</a></li>
                        <li><a href="<?= base_url(); ?>signup" class="log-txt1" style="color: white;
                            margin: 10px;">Register</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
</section>