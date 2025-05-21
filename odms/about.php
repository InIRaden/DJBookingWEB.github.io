<?php
session_start();
error_reporting(0);

include('includes/dbconnection.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Online DJ Management System || About Us</title>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
    <!-- Custom Theme files -->
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" href="css/touchTouch.css" type="text/css" media="all" />
    <!-- Custom Theme files -->
    <script src="js/jquery.min.js"></script>
    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <!--webfont-->
    <link href='http://fonts.googleapis.com/css?family=Monoton' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />

    <!---//End-css-style-switecher----->
    <script type="text/javascript" src="js/jquery.fancybox.js"></script>
    <link rel="stylesheet" type="text/css" href="css/jquery.fancybox.css" media="screen" />
    <script type="text/javascript">
        $(document).ready(function() {
            /*
             *  Simple image gallery. Uses default settings
             */

            $('.fancybox').fancybox();

        });
    </script>

    <!-- Tambahan CSS untuk meningkatkan tampilan -->
    <style type="text/css">
        /* Styling untuk halaman About */
        .about.content {
            background: linear-gradient(to bottom, #1a1a1a, #333);
            padding-bottom: 40px;
        }

        .about-main {
            margin-top: 30px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .about-main:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .abt-pic img {
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .abt-pic img:hover {
            transform: scale(1.02);
        }

        .abt-pic-info p {
            font-size: 16px;
            line-height: 1.8;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            padding: 15px;
            border-left: 4px solid #ff5722;
        }

        h2 {
            color: #ff5722;
            font-size: 36px;
            text-transform: uppercase;
            margin-bottom: 25px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            position: relative;
            padding-bottom: 15px;
        }

        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background: #ff5722;
        }

        .latest {
            margin-top: 50px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .latest h3 {
            color: #ff5722;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .pic {
            transition: all 0.3s ease;
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }

        .pic:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
            background: rgba(0, 0, 0, 0.5);
        }

        .pic img {
            border-radius: 5px;
            transition: all 0.5s ease;
        }

        .pic:hover img {
            transform: scale(1.05);
        }

        .pic h4 {
            margin-top: 15px;
            font-size: 18px;
        }

        .pic h4 a {
            color: #ff5722;
            transition: all 0.3s ease;
        }

        .pic h4 a:hover {
            color: #ff8a65;
            text-decoration: none;
        }

        .pic p {
            color: #ccc;
            font-size: 14px;
            line-height: 1.6;
        }

        .breadcrumb {
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            padding: 12px 20px;
            margin-top: 20px;
        }

        .breadcrumb li a {
            color: #ff5722;
            transition: all 0.3s ease;
        }

        .breadcrumb li a:hover {
            color: #ff8a65;
            text-decoration: none;
        }

        .breadcrumb li.active {
            color: #fff;
        }

        /* Animasi untuk elemen */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .about-main,
        .latest {
            animation: fadeIn 1s ease-out;
        }
    </style>

</head>

<body>
    <!---->
    <?php include_once('includes/header.php'); ?>
    <div class="about content">
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="index.php">Home</a></li>
                <li class="active">About</li>
            </ol>
            <?php
            $sql = "SELECT * from tblpage where PageType='aboutus'";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            $cnt = 1;
            if ($query->rowCount() > 0) {
                foreach ($results as $row) {               ?>
                    <h2><?php echo htmlentities($row->PageTitle); ?></h2>
                    <div class="about-main">
                        <div class="col-md-6 abt-pic">
                            <img src="images/abt.jpg" class="img-responsive" alt="" />
                        </div>
                        <div class="col-md-6 abt-pic-info">

                            <p style="color:#fff"><?php echo $row->PageDescription; ?>.</p>

                        </div>
                <?php $cnt = $cnt + 1;
                }
            } ?>
                <div class="clearfix"></div>
                    </div>

                    <div class="latest">
                        <h3>LATEST PHOTOS</h3>
                        <div class="pic start">
                            <a class="fancybox" href="images/4.jpg" data-fancybox-group="gallery" title="Lorem ipsum dolor sit amet"><img src="images/4.jpg" class="img-style row6" alt=""><span> </span></a>
                            <h4><a href="event.html">Aenean rutrum</a></h4>
                            <p>Suspendisse posuere enim eu ante scelerisque, vehicula turpis.</p>
                        </div>
                        <div class="pic">
                            <a class="fancybox" href="images/5.jpg" data-fancybox-group="gallery" title="Lorem ipsum dolor sit amet"><img src="images/5.jpg" class="img-style row6" alt=""><span> </span></a>
                            <h4><a href="event.html">Aenean rutrum</a></h4>
                            <p>Suspendisse posuere enim eu ante scelerisque, vehicula turpis.</p>
                        </div>
                        <div class="pic">
                            <a class="fancybox" href="images/6.jpg" data-fancybox-group="gallery" title="Lorem ipsum dolor sit amet"><img src="images/6.jpg" class="img-style row6" alt=""><span> </span></a>
                            <h4><a href="event.html">Aenean rutrum</a></h4>
                            <p>Suspendisse posuere enim eu ante scelerisque, vehicula turpis.</p>
                        </div>
                        <div class="pic">
                            <a class="fancybox" href="images/7.jpg" data-fancybox-group="gallery" title="Lorem ipsum dolor sit amet"><img src="images/7.jpg" class="img-style row6" alt=""><span> </span></a>
                            <h4><a href="event.html">Aenean rutrum</a></h4>
                            <p>Suspendisse posuere enim eu ante scelerisque, vehicula turpis.</p>
                        </div>
                        <div class="pic">
                            <a class="fancybox" href="images/8.jpg" data-fancybox-group="gallery" title="Lorem ipsum dolor sit amet"><img src="images/8.jpg" class="img-style row6" alt=""><span> </span></a>
                            <h4><a href="event.html">Aenean rutrum</a></h4>
                            <p>Suspendisse posuere enim eu ante scelerisque, vehicula turpis.</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php include_once('includes/footer.php'); ?>
        </div>
    </div>
    <!---->

    <!---->
</body>

</html>