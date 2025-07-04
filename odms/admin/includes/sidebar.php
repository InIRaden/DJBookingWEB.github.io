<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['odmsaid'] == 0)) {
    header('location:logout.php');
} else {
?>    <nav id="sidebar" style="background-color: #212121; box-shadow: 0 0 15px rgba(0,0,0,0.4); border-bottom-right-radius: 20px; border-top-right-radius: 20px;">
        <!-- Sidebar Scroll Container -->
        <div id="sidebar-scroll">
            <!-- Sidebar Content -->
            <div class="sidebar-content">
                <!-- Side Header -->                <div class="content-header content-header-fullrow px-15" style="background-color: #212121; border-bottom: 1px solid #242424;">
                    <!-- Mini Mode -->
                    <div class="content-header-section sidebar-mini-visible-b">
                        <!-- Logo -->
                        <span class="content-header-item font-w700 font-size-xl float-left animated fadeIn">
                            <span class="text-dual-primary-dark">S</span><span class="text-primary">P</span>
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
                
                                <span class="font-size-xl text-white">SPINUP</span>
                            </a>
                        </div>
                        <!-- END Logo -->
                    </div>
                    <!-- END Normal Mode -->
                </div>
                <!-- END Side Header -->                <!-- Side User -->                <div class="content-side content-side-full content-side-user px-10 align-parent" style="background-color: #212121; border-bottom: 1px solid #242424;">
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
                <div class="content-side content-side-full" style="background-color: #212121">                    <ul class="nav-main" style="color: #e0e0e0;">
                        <li class="open">
                            <a href="dashboard.php" style="color: #e0e0e0; background-color: #212121;"><i class="si si-cup" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Dashboard</span></a>
                        </li>

                        <li class="nav-main-heading" style="color: #4f9fff; margin-top: 10px;"><span class="sidebar-mini-visible">UI</span><span class="sidebar-mini-hidden">Menu</span></li>
                        <li>
                            <a class="nav-submenu" data-toggle="nav-submenu" style="color: #e0e0e0;"><i class="si si-volume-2" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">DJ Services</span></a>
                            <ul style="background-color: #212121; border-left: 3px solid #4f9fff; margin: 0; padding: 0;">
                                <li>
                                    <a href="add-services.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Add Services</a>
                                </li>
                                <li>
                                    <a href="manage-services.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Manage Services</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-energy" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Type of Events</span></a>
                            <ul style="background-color: #212121; border-left: 3px solid #4f9fff; margin: 0; padding: 0;">
                                <li>
                                    <a href="add-event-type.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Add Event Types</a>
                                </li>
                                <li>
                                    <a href="manage-event-type.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Manage Event Types</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-note" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Booking</span></a>
                            <ul style="background-color: #212121; border-left: 3px solid #4f9fff; margin: 0; padding: 0;">
                                <li>
                                    <a href="new-booking.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">New Booking</a>
                                </li>
                                <li>
                                    <a href="approved-booking.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Approved Booking</a>
                                </li>
                                <li>
                                    <a href="cancelled-booking.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Cancelled Booking</a>
                                </li>
                                <li>
                                    <a href="all-booking.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">All Booking</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-users" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Contact Us Queries</span></a>
                            <ul style="background-color: #212121; border-left: 3px solid #4f9fff; margin: 0; padding: 0;">
                                <li>
                                    <a href="unread-queries.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Unread Queries</a>
                                </li>
                                <li>
                                    <a href="read-queries.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Read Queries</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="between-dates-report.php" style="color: #e0e0e0;"><i class="si si-doc" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">B/w Dates Report</span></a>
                        </li>
                        <li>
                            <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-magnifier" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Search</span></a>
                            <ul style="background-color: #212121; border-left: 3px solid #4f9fff; margin: 0; padding: 0;">
                                <li>
                                    <a href="user-search.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">User Search</a>
                                </li>
                                <li>
                                    <a href="booking-search.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Booking Search</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="nav-submenu" data-toggle="nav-submenu" href="#" style="color: #e0e0e0;"><i class="si si-docs" style="color: #4f9fff;"></i><span class="sidebar-mini-hide">Pages</span></a>
                            <ul style="background-color: #212121; border-left: 3px solid #4f9fff; margin: 0; padding: 0;">
                                <li>
                                    <a href="aboutus.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">About Us</a>
                                </li>
                                <li>
                                    <a href="contactus.php" style="color: #e0e0e0; padding-left: 40px; margin: 0;">Contact Us</a>
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
    <style>
        .nav-main li a:hover,
        .nav-main li.open > a {
            background-color: #2c2c2c !important;
            color: #ffffff !important;
        }
        .nav-main ul a:hover {
            background-color: #2c2c2c !important;
            color: #ffffff !important;
            padding-left: 20px;
            transition: all 0.3s ease;
        }
        .nav-main ul {
            transition: all 0.3s ease;
        }
        .nav-main > li:hover > a {
            border-left-color: #4f9fff !important;
        }
    </style>
<?php } ?>