<!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- User Profile-->
                <div class="user-profile">
                    <div class="user-pro-body">
                        <div><img src="<?=base_url()?>/public/assets/images/users/150-1503945_transparent-user-png-default-user-image-png-png.png" alt="user-img" class="img-circle"></div>
                        <div class="dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=!empty(session()->get('username'))?ucfirst(session()->get('username')):'';?> <span class="caret"></span></a>
                            <div class="dropdown-menu animated flipInY">
                                <?php if(session()->get('role_id') == 1){ ?>
                                  <a href="<?php echo base_url().'/admin';?>" class="dropdown-item"><i class="ti-user"></i> Dashboard</a>
                                <?php } ?>
                                <?php if(session()->get('role_id') == 3){ ?>
                                  <a href="<?php echo base_url().'/organizer/dashboard';?>" class="dropdown-item"><i class="ti-user"></i> Dashboard</a>
                                <?php } ?>
                                <!-- text-->
                                <a href="<?=base_url()?>/profile" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                                <?php if(session()->get('role_id') == 2){ ?>
                                  <a href="<?php echo base_url().'/my_payments';?>" class="dropdown-item"><i class="ti-user"></i> My Payments</a>
                                <?php } ?>
                                <a href="<?=base_url()?>/change_password" class="dropdown-item"><i class="ti-settings"></i> Change Password</a>
                               
                                <div class="dropdown-divider"></div>
                                <!-- text-->
                                <a href="<?=base_url();?>/logout" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                                <!-- text-->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        
                        <?php
                        $uri = service('uri');
                        ?>
                       
                        <?php if(session()->get('role_id') == 1){ ?>
                        <li class="<?php echo ($uri->getSegment(2) == 'admin')?'active':'';?>"> 
                            <a class="waves-effect waves-dark <?php echo ($uri->getSegment(2) == 'home')?'active':'';?>" href="<?php echo base_url().'/admin';?>">Dashboard</a>
                        </li>
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="far fa-circle text-success"></i><span class="hide-menu">Master Entries</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="<?=base_url();?>/admin/events">Events</a></li>
                                <li><a href="<?=base_url();?>/admin/sports">Sports</a></li>
                                <li><a href="<?=base_url();?>/admin/country">Countries</a></li>
                                <li><a href="<?=base_url();?>/admin/cities">Cities</a></li>
                            </ul>
                        </li> 
                         <li><a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="far fa-circle text-success"></i><span class="hide-menu">Tournaments</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <!--<li><a href="<?=base_url();?>/admin/events">Events</a></li>-->
                                <li><a href="<?=base_url();?>/tournament_list/upcoming">Upcoming</a></li>
                                <li><a href="<?=base_url();?>/tournament_list/ongoing">Ongoing</a></li>
                                <li><a href="<?=base_url();?>/tournament_list/completed">Completed</a></li>
                            </ul>
                        </li> 
                        <li><a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="far fa-circle text-success"></i><span class="hide-menu">Organizer list</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <!--<li><a href="<?=base_url();?>/admin/events">Events</a></li>-->
                                <li><a href="<?=base_url();?>/organizer_list/new">New</a></li>
                                <li><a href="<?=base_url();?>/organizer_list/verified">Verified</a></li>
                            </ul>
                        </li> 
                        <?php } ?>
                         
                         <?php if(session()->get('role_id') == 3){ ?>
                        <li class="<?php echo ($uri->getSegment(2) == 'home')?'active':'';?>"> 
                            <a class="waves-effect waves-dark <?php echo ($uri->getSegment(2) == 'home')?'active':'';?>" href="<?php echo base_url().'/organizer';?>">Dashboard</a>
                        </li>
                        <!--<li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="far fa-circle text-success"></i><span class="hide-menu">Master Entries</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="<?=base_url();?>/organizer/events">Events</a></li>
                            </ul>
                        </li> -->
                        <?php } ?>
                         <!--<li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-layout-grid2"></i><span class="hide-menu">My Tournaments</span></a>
                        </li> 
                       
                        <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="far fa-circle text-success"></i><span class="hide-menu">Master Entries</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="<?=base_url();?>/admin/cities">City</a></li>
                                <li><a href="<?=base_url();?>/admin/areas">Area</a></li>
                            </ul>
                        </li> -->
                        
                        
                        
                        <li> <a class="waves-effect waves-dark" href="<?php echo base_url().'/logout';?>" aria-expanded="false"><i class="far fa-circle text-success"></i><span class="hide-menu">Log Out</span></a></li>
                        
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->