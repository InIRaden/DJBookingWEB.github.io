<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['odmsaid']) == 0) {
  header('location:logout.php');
} else {
?>
  <!doctype html>
  <html lang="en" class="no-focus">

  <head>
    <title>Online DJ Management System - View Invoice</title>
    <link rel="stylesheet" href="assets/js/plugins/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
    <style>
      body {
        background: #ffffff;
        color: #333;
      }

      #page-container {
        background: transparent;
      }

      .content {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      }

      .content-heading {
        color: #1e3c72;
        font-weight: bold;
        margin-bottom: 20px;
      }

      .block {
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      }

      .block-header {
        background: #1e3c72;
        border-bottom: none;
        padding: 15px 20px;
      }

      .block-title {
        color: #ffffff;
        font-weight: bold;
      }

      .table {
        background: #ffffff;
        border-radius: 8px;
        margin-bottom: 0;
      }

      .table thead th {
        background: #f8f9fa;
        color: #1e3c72;
        font-weight: 600;
        border-bottom: 2px solid #e0e0e0;
      }

      .table td,
      .table th {
        vertical-align: middle;
        text-align: center;
      }

      .badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: bold;
        font-size: 0.9rem;
      }

      .btn-info {
        background: #ffffff;
        border: 2px solid #007BFF;
        color: #007BFF;
        border-radius: 8px;
        padding: 5px 15px;
        font-weight: bold;
        transition: all 0.3s ease;
        cursor: pointer;
      }

      .btn-info:hover {
        background: #007BFF;
        color: #ffffff;
        transform: translateY(-1px);
      }
    </style>
    <script language="javascript" type="text/javascript">
      function f2() {
        window.close();
      }

      function f3() {
        window.print();
      }
    </script>
  </head>

  <body>
    <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
      <?php include_once('includes/sidebar.php'); ?>
      <?php include_once('includes/header.php'); ?>
      <main id="main-container">
        <div class="content">
          <h2 class="content-heading">View Invoice</h2>
          <div class="block">
            <div class="block-header block-header-default">
              <h3 class="block-title">View Invoice</h3>
            </div>
            <div class="block-content block-content-full">
              <?php
              $invid = $_GET['invid'];
              $sql = "SELECT tblbooking.BookingID,tblbooking.Name,tblbooking.MobileNumber,tblbooking.Email,tblbooking.EventDate,tblbooking.EventStartingtime,tblbooking.EventEndingtime,tblbooking.VenueAddress,tblbooking.EventType,tblbooking.AdditionalInformation,tblbooking.BookingDate,tblbooking.Remark,tblbooking.Status,tblbooking.UpdationDate,tblservice.ServiceName,tblservice.SerDes,tblservice.ServicePrice from tblbooking join tblservice on tblbooking.ServiceID=tblservice.ID where tblbooking.ID=:invid";
              $query = $dbh->prepare($sql);
              $query->bindParam(':invid', $invid, PDO::PARAM_STR);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);
              $cnt = 1;
              $grandtotal = 0;
              if ($query->rowCount() > 0) {
                foreach ($results as $row) {
              ?>
                  <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                    <thead>
                      <tr>
                        <th colspan="4" style="color: #1e3c72; font-size: 20px;">Booking Number: <span class="badge badge-primary"><?php echo htmlentities($row->BookingID); ?></span></th>
                      </tr>
                      <tr>
                        <th>Name of Client</th>
                        <th>Mobile Number</th>
                        <th>Email</th>
                        <th>Event Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><?php echo htmlentities($row->Name); ?></td>
                        <td><?php echo htmlentities($row->MobileNumber); ?></td>
                        <td><?php echo htmlentities($row->Email); ?></td>
                        <td><span class="badge badge-primary"><?php echo htmlentities($row->EventDate); ?></span></td>
                      </tr>
                      <tr>
                        <th colspan="2">Service Name</th>
                        <th colspan="2">Service Price</th>
                      </tr>
                      <tr>
                        <td colspan="2"><?php echo htmlentities($row->ServiceName); ?></td>
                        <td colspan="2"><?php echo $total = htmlentities($row->ServicePrice); ?></td>
                      </tr>
                      <tr>
                        <th colspan="2" style="color: #1e3c72;">Grand Total</th>
                        <td colspan="2"><?php echo $grandtotal += $total; ?></td>
                      </tr>
                    </tbody>
                  </table>
              <?php
                  $cnt++;
                }
              }
              ?>
              <p style="text-align: center;">
                <input name="Submit2" type="submit" class="btn-info" value="Print" onclick="return f3();" />
              </p>
            </div>
          </div>
        </div>
      </main>
      <?php include_once('includes/footer.php'); ?>
    </div>
    <script src="assets/js/core/jquery.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/core/jquery.slimscroll.min.js"></script>
    <script src="assets/js/core/jquery.scrollLock.min.js"></script>
    <script src="assets/js/core/jquery.appear.min.js"></script>
    <script src="assets/js/core/jquery.countTo.min.js"></script>
    <script src="assets/js/core/js.cookie.min.js"></script>
    <script src="assets/js/codebase.js"></script>
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/pages/be_tables_datatables.js"></script>
  </body>

  </html>
<?php } ?>