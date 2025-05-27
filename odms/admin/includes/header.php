<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['odmsaid']) == 0) {
    header('location:logout.php');
} else {
?>
    <header id="page-header">
        <style>
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .dropdown-item:hover {
                transform: translateX(5px);
            }
        </style>
        <!-- Header Content -->
        <div class="content-header" style="background-color: #1a1f2e; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);">
            <!-- Left Section -->
            <div class="content-header-section">
                <!-- Toggle Sidebar -->
                <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
                <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout" data-action="sidebar_toggle">
                    <i class="fa fa-navicon" style="color: #e0e0e0;"></i>
                </button>
                <!-- Semua fitur lain dihapus dari sini -->
            </div>
            <!-- END Left Section -->

            <!-- Right Section -->
            <div class="content-header-section">
                <!-- User Dropdown -->
                <div class="btn-group" role="group">
                    <?php
                    $aid = $_SESSION['odmsaid'];
                    $sql = "SELECT AdminName from tbladmin where ID=:aid";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':aid', $aid, PDO::PARAM_STR);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    $cnt = 1;
                    if ($query->rowCount() > 0) {
                        foreach ($results as $row) {
                    ?>
                            <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #e0e0e0; transition: all 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'; this.style.backgroundColor='#252b3b';" onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='';">
                                <i class="fa fa-user d-sm-none"></i>
                                <span class="d-none d-sm-inline-block"><?php echo $row->AdminName; ?></span>
                                <i class="fa fa-angle-down ml-5"></i>
                            </button>
                    <?php $cnt = $cnt + 1;
                        }
                    } ?>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-dark min-width-200" aria-labelledby="page-header-user-dropdown" style="background-color: #252b3b; color: #e0e0e0; border: 1px solid #2d3446;">
                        <h5 class="h6 text-center py-10 mb-5 border-b" style="color: #4f9fff; border-color: #2d3446;">Admin</h5>
                        <a class="dropdown-item" href="admin-profile.php" style="color: #e0e0e0;">
                            <i class="si si-user mr-5" style="color: #4f9fff;"></i> Profile
                        </a>
                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="change-password.php" style="color: #e0e0e0;">
                            <span><i class="si si-key mr-5" style="color: #4f9fff;"></i> Password</span>
                        </a>
                        <div class="dropdown-divider" style="border-color: #2d3446;"></div>
                        <a class="dropdown-item" href="logout.php" style="color: #e0e0e0;">
                            <i class="si si-logout mr-5" style="color: #4f9fff;"></i> Sign Out
                        </a>
                    </div>
                </div>
                <!-- END User Dropdown -->
            </div>
            <!-- END Right Section -->
        </div>
        <!-- END Header Content -->

        <!-- Header Loader -->
        <div id="page-header-loader" class="overlay-header bg-primary">
            <div class="content-header content-header-fullrow text-center">
                <div class="content-header-item">
                    <i class="fa fa-sun-o fa-spin text-white"></i>
                </div>
            </div>
        </div>
        <!-- END Header Loader -->
    </header>
<?php } ?>
<header id="page-header">
    <!-- Header Content -->
    <div class="content-header">
        <!-- Left Section -->
        <div class="content-header-section">
            <!-- Toggle Sidebar -->
            <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
            <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-navicon"></i>
            </button>

            <!-- <div class="btn-group" role="group">
                <button type="button" class="btn btn-circle btn-dual-secondary" id="page-header-options-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-wrench"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="page-header-options-dropdown">
                    <h6 class="dropdown-header">Header</h6>
                    <button type="button" class="btn btn-sm btn-block btn-alt-secondary" data-toggle="layout" data-action="header_fixed_toggle">Fixed Mode</button>
                    <button type="button" class="btn btn-sm btn-block btn-alt-secondary d-none d-lg-block mb-10" data-toggle="layout" data-action="header_style_classic">Classic Style</button>
                    <div class="d-none d-xl-block">
                        <h6 class="dropdown-header">Main Content</h6>
                        <button type="button" class="btn btn-sm btn-block btn-alt-secondary mb-10" data-toggle="layout" data-action="content_layout_toggle">Toggle Layout</button>
                    </div>
                    <div class="dropdown-divider"></div>

                </div>
            </div>
            <!-- END Layout Options -->

            <!-- Color Themes (used just for demonstration) -->
            <!-- Themes functionality initialized in Codebase() -> uiHandleTheme() -->
            <!-- <div class="btn-group" role="group">
                <button type="button" class="btn btn-circle btn-dual-secondary" id="page-header-themes-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-paint-brush"></i>
                </button>
                <div class="dropdown-menu min-width-150" aria-labelledby="page-header-themes-dropdown">
                    <h6 class="dropdown-header text-center">Color Themes</h6>
                    <div class="row no-gutters text-center mb-5">
                        <div class="col-4 mb-5">
                            <a class="text-default" data-toggle="theme" data-theme="default" href="javascript:void(0)">
                                <i class="fa fa-2x fa-circle"></i>
                            </a>
                        </div>
                        <div class="col-4 mb-5">
                            <a class="text-elegance" data-toggle="theme" data-theme="assets/css/themes/elegance.min.css" href="javascript:void(0)">
                                <i class="fa fa-2x fa-circle"></i>
                            </a>
                        </div>
                        <div class="col-4 mb-5">
                            <a class="text-pulse" data-toggle="theme" data-theme="assets/css/themes/pulse.min.css" href="javascript:void(0)">
                                <i class="fa fa-2x fa-circle"></i>
                            </a>
                        </div>
                        <div class="col-4 mb-5">
                            <a class="text-flat" data-toggle="theme" data-theme="assets/css/themes/flat.min.css" href="javascript:void(0)">
                                <i class="fa fa-2x fa-circle"></i>
                            </a>
                        </div>
                        <div class="col-4 mb-5">
                            <a class="text-corporate" data-toggle="theme" data-theme="assets/css/themes/corporate.min.css" href="javascript:void(0)">
                                <i class="fa fa-2x fa-circle"></i>
                            </a>
                        </div>
                        <div class="col-4 mb-5">
                            <a class="text-earth" data-toggle="theme" data-theme="assets/css/themes/earth.min.css" href="javascript:void(0)">
                                <i class="fa fa-2x fa-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <button type="button" class="btn btn-sm btn-block btn-alt-secondary mb-10" data-toggle="layout" data-action="sidebar_style_inverse_toggle">Sidebar Style</button>
                    <div class="dropdown-divider"></div>

                </div>
            </div>  -->
            <!-- END Color Themes -->
        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div class="content-header-section">
            <!-- User Dropdown -->
            <div class="btn-group" role="group">
                <?php
                $aid = $_SESSION['odmsaid'];
                $sql = "SELECT AdminName from  tbladmin where ID=:aid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':aid', $aid, PDO::PARAM_STR);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                $cnt = 1;
                if ($query->rowCount() > 0) {
                    foreach ($results as $row) {               ?>
                        <button type="button" class="btn btn-rounded btn-dual-secondary" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $row->AdminName; ?><i class="fa fa-angle-down ml-5"></i>
                        </button>
                <?php $cnt = $cnt + 1;
                    }
                } ?>
                <div class="dropdown-menu dropdown-menu-right min-width-150" aria-labelledby="page-header-user-dropdown">
                    <a class="dropdown-item" href="admin-profile.php">
                        <i class="si si-user mr-5"></i> Profile
                    </a>

                    <div class="dropdown-divider"></div>

                    <!-- Toggle Side Overlay -->
                    <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->
                    <a class="dropdown-item" href="change-password.php" data-toggle="layout" data-action="side_overlay_toggle">
                        <i class="si si-wrench mr-5"></i> Change Password
                    </a>
                    <!-- END Side Overlay -->

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">
                        <i class="si si-logout mr-5"></i> Sign Out
                    </a>
                </div>
            </div>
            <!-- END User Dropdown -->

            <!-- Toggle Side Overlay -->
            <!-- Layout API, functionality initialized in Codebase() -> uiApiLayout() -->

        </div>
        <!-- END Right Section -->
    </div>
    <!-- END Header Content -->


    <!-- Header Loader -->
    <!-- Please check out the Activity page under Elements category to see examples of showing/hiding it -->
    <div id="page-header-loader" class="overlay-header bg-primary">
        <div class="content-header content-header-fullrow text-center">
            <div class="content-header-item">
                <i class="fa fa-sun-o fa-spin text-white"></i>
            </div>
        </div>
    </div>
    <!-- END Header Loader -->
</header>
<?php   ?>