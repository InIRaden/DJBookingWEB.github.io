<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['odmsaid']==0)) {
  header('location:logout.php');
} else {
?>
<nav id="sidebar" style="background-color: #1a1f2e; box-shadow: 0 0 15px rgba(0,0,0,0.4);">
    <!-- Sidebar Scroll Container -->
    <div id="sidebar-scroll">
        <!-- Sidebar Content -->
        <div class="sidebar-content">
            <!-- Side Header -->
            <div class="content-header content-header-fullrow px-15" style="background-color: #151a27; border-bottom: 1px solid #2d3446;">
                <!-- Mini Mode -->
                <div class="content-header-section sidebar-mini-visible-b">
                    <!-- Logo -->
                    <span class="content-header-item font-w700 font-size-xl float-left animated fadeIn">
                        <span class="text-dual-primary-dark">O</span><span class="text-primary">D</span>
                    </span>
                    <!-- END Logo -->
                </div>
                <!-- END Mini Mode -->

                <!-- Normal Mode -->
                <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                    <!-- Close Sidebar, Visible only on mobile screens -->
                    <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
                    <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout" data-action="sidebar_close">
                        <i class="fa fa-times text-danger"></i>
                    </button>
                    <!-- END Close Sidebar -->

                    <!-- Logo -->
                    <div class="content-header-item">
                        <a class="link-effect font-w700" href="dashboard.php">
                            <i class="si si-music-tone-alt text-primary"></i>
                            <span class="font-size-xl text-white">OD</span><span class="font-size-xl text-primary">MS</span>
                        </a>
                    </div>
                    <!-- END Logo -->
                </div>
                <!-- END Normal Mode -->
            </div>
            <!-- END Side Header -->

            <!-- Side User -->
            <div class="content-side content-side-full content-side-user px-10 align-parent" style="background-color: #151a27; border-bottom: 1px solid #2d3446;">
                <!-- Visible only in mini mode -->
                <div class="sidebar-mini-visible-b align-v animated fadeIn">
                    <img class="img-avatar img-avatar32" src="assets/img/avatars/avatar15.jpg" alt="">
                </div>
                <!-- END Visible only in mini mode -->

                <!-- Visible only in normal mode -->
                <div class="sidebar-mini-hidden-b text-center">
                    <a class="img-link" href="admin-profile.php">
                        <img class="img-avatar" src="assets/img/avatars/avatar15.jpg" alt="">
                    </a>
                    <ul class="list-inline mt-10">
                        <li class="list-inline-item">
                            <a class="link-effect text-white font-size-sm font-w600 text-uppercase" href="admin-profile.php">Admin</a>
                        </li>
                        <li class="list-inline-item">
                            <a class="link-effect text-white" data-toggle="layout" data-action="sidebar_style_inverse_toggle" href="admin-profile.php">
                                <i class="si si-drop"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a class="link-effect text-white" href="logout.php">
                                <i class="si si-logout"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- END Visible only in normal mode -->
            </div>
            <!-- END Side User -->

            <!-- Side Navigation -->
            <div class="content-side content-side-full">
                <ul class="nav-main" style="color: #e0e0e0;">
                    <li class="open">
                        <a href="dashboard.php" style="color: #e0e0e0; background-color: #151a27;"><i class="si si-cup" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Dashboard</span></a>
                    </li>
                  
                    <li class="nav-main-heading" style="color: #4f9fff; margin-top: 10px;"><span class="sidebar-mini-visible">UI</span><span class="sidebar-mini-hidden">Menu</span></li>
                    <li>
                        <a class="nav-submenu" data-toggle="nav-submenu" style="color: #e0e0e0;"><i class="si si-volume-2" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">DJ Services</span></a>
                        <ul style="background-color: #151a27; border-left: 3px solid #4f9fff;">
                            <li>
                                <a href="add-services.php" style="color: #e0e0e0; padding-left: 40px;">Add Services</a>
                            </li>
                            <li>
                                <a href="manage-services.php" style="color: #e0e0e0; padding-left: 40px;">Manage Services</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-energy" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Type of Events</span></a>
                        <ul style="background-color: #151a27; border-left: 3px solid #4f9fff;">
                            <li>
                                <a href="add-event-type.php" style="color: #e0e0e0; padding-left: 40px;">Add Event Types</a>
                            </li>
                            <li>
                                <a href="manage-event-type.php" style="color: #e0e0e0; padding-left: 40px;">Manage Event Types</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-note" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Booking</span></a>
                        <ul style="background-color: #151a27; border-left: 3px solid #4f9fff;">
                            <li>
                                <a href="new-booking.php" style="color: #e0e0e0; padding-left: 40px;">New Booking</a>
                            </li>
                            <li>
                                <a href="approved-booking.php" style="color: #e0e0e0; padding-left: 40px;">Approved Booking</a>
                            </li>
                            <li>
                                <a href="cancelled-booking.php" style="color: #e0e0e0; padding-left: 40px;">Cancelled Booking</a>
                            </li>
                            <li>
                                <a href="all-booking.php" style="color: #e0e0e0; padding-left: 40px;">All Booking</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-users" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Contact Us Queries</span></a>
                        <ul style="background-color: #151a27; border-left: 3px solid #4f9fff;">
                            <li>
                                <a href="unread-queries.php" style="color: #e0e0e0; padding-left: 40px;">Unread Queries</a>
                            </li>
                            <li>
                                <a href="read-queries.php" style="color: #e0e0e0; padding-left: 40px;">Read Queries</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="between-dates-report.php" style="color: #e0e0e0;"><i class="si si-doc" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">B/w Dates Report</span></a>
                    </li>
                    <li>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-magnifier" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Search</span></a>
                        <ul style="background-color: #151a27; border-left: 3px solid #4f9fff;">
                            <li>
                                <a href="user-search.php" style="color: #e0e0e0; padding-left: 40px;">User Search</a>
                            </li>
                            <li>
                                <a href="booking-search.php" style="color: #e0e0e0; padding-left: 40px;">Booking Search</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-docs" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Pages</span></a>
                        <ul style="background-color: #151a27; border-left: 3px solid #4f9fff;">
                            <li>
                                <a href="aboutus.php" style="color: #e0e0e0; padding-left: 40px;">About Us</a>
                            </li>
                            <li>
                                <a href="contactus.php" style="color: #e0e0e0; padding-left: 40px;">Contact Us</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- END Side Navigation -->
        </div>
        <!-- Sidebar Content -->
    </div>
    <!-- END Sidebar Scroll Container -->
</nav>
<?php } ?>