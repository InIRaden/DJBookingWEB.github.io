<?php
//error_reporting(0);
session_start(); // Tambahkan ini jika belum ada dan diperlukan untuk header
include('includes/dbconnection.php');

// Ambil bookingid dari GET request, pastikan aman
$booking_id_display = isset($_GET['bookingid']) ? htmlspecialchars($_GET['bookingid']) : 'Tidak Diketahui';
$eid = isset($_GET['bid']) ? $_GET['bid'] : null;

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <title>Online DJ Management System | Request Detail</title>
  <link rel="stylesheet" href="../src/output.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: "Inter", sans-serif;
    }

    .table-modern {
      width: 100%;
      border-collapse: collapse;
      background: #30343b;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10);
    }

    .table-modern th,
    .table-modern td {
      border: 1px solid #393939;
      padding: 1.1rem 1rem;
      text-align: left;
      font-size: 1rem;
    }

    .table-modern th {
      background-color: #30343b;
      color: #fff;
      font-weight: 700;
      letter-spacing: 0.02em;
    }

    .table-modern tr:nth-child(even) {
      background-color: #232323;
    }

    .table-modern tr:nth-child(odd) {
      background-color: #30343b;
    }

    .bg-card {
      background: #212121 !important;
      box-shadow: 0 6px 32px rgba(0, 0, 0, 0.18);
      border-radius: 18px;
    }

    .badge-status {
      display: inline-flex;
      align-items: center;
      padding: 0.5em 1.2em;
      border-radius: 9999px;
      font-weight: 600;
      font-size: 1rem;
      gap: 0.5em;
    }

    .badge-approved {
      background: rgba(34, 197, 94, 0.12);
      color: #22c55e;
    }

    .badge-cancelled {
      background: rgba(239, 68, 68, 0.12);
      color: #ef4444;
    }

    .badge-pending {
      background: rgba(250, 204, 21, 0.12);
      color: #facc15;
    }

    .icon-label {
      color: #b3b3b3;
      margin-right: 0.7em;
      font-size: 1.1em;
      vertical-align: middle;
    }

    .section-title {
      font-size: 2.1rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: 0.5rem;
      text-shadow: 0 0 10px #fff, 0 0 18px #2563eb, 2px 2px 8px rgba(0, 0, 0, 0.3);
      letter-spacing: 1px;
    }

    .section-desc {
      color: #cbd5e1;
      font-size: 1.1rem;
      margin-bottom: 2.5rem;
      text-align: center;
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 0.5em;
      font-size: 0.95em;
      color: #a3a3a3;
      margin-bottom: 1.5rem;
    }

    .breadcrumb a {
      color: #a3a3a3;
      text-decoration: none;
    }

    .breadcrumb a:hover {
      color: #fff;
    }
  </style>
</head>

<body class="bg-black text-white">

  <header class="relative">
    <?php include_once('includes/header.php'); ?>
    <div class="header-container" style="position:relative;width:100%;height:300px;overflow:hidden;">
      <img src="images/abt.jpg" alt="DJ performing at event" class="w-full h-[300px] object-cover header-image" />
      <div class="header-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(to top,rgba(0,0,0,0.95) 0%,rgba(0,0,0,0.7) 60%,rgba(0,0,0,0.1) 100%);"></div>
      <div class="header-content" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;width:100%;max-width:500px;padding:0 20px;z-index:2;">
        <h1 class="section-title">Request Detail</h1>
        <p class="section-desc">See all your booking request details here</p>
      </div>
    </div>
  </header>

  <!-- Konten Utama -->
  <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto">
    <div class="breadcrumb">
      <a href="index.php">Home</a>
      <span>/</span>
      <span>Request Detail</span>
    </div>
    <h2 class="font-semibold text-white text-lg mb-6">Request Detail #<?php echo $booking_id_display; ?></h2>

    <div class="bg-card shadow-md rounded-lg p-6 md:p-8">
      <?php
      if ($eid) {
        $sql = "SELECT tblbooking.BookingID,tblbooking.Name,tblbooking.MobileNumber,tblbooking.Email,tblbooking.EventDate,tblbooking.EventStartingtime,tblbooking.EventEndingtime,tblbooking.VenueAddress,tblbooking.EventType,tblbooking.AdditionalInformation,tblbooking.BookingDate,tblbooking.Remark,tblbooking.Status,tblbooking.UpdationDate,tblservice.ServiceName,tblservice.SerDes,tblservice.ServicePrice from tblbooking join tblservice on tblbooking.ServiceID=tblservice.ID  where tblbooking.ID=:eid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
          foreach ($results as $row) { ?>
            <div class="overflow-x-auto">
              <table class="table-modern">
                <tbody>
                  <tr>
                    <th><i class="fas fa-hashtag icon-label"></i>Booking Number</th>
                    <td><?php echo htmlentities($row->BookingID); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-user icon-label"></i>Client Name</th>
                    <td><?php echo htmlentities($row->Name); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-phone icon-label"></i>Mobile Number</th>
                    <td><?php echo htmlentities($row->MobileNumber); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-envelope icon-label"></i>Email</th>
                    <td><?php echo htmlentities($row->Email); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-calendar-alt icon-label"></i>Event Date</th>
                    <td><?php echo htmlentities($row->EventDate); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-clock icon-label"></i>Event Start Time</th>
                    <td><?php echo htmlentities($row->EventStartingtime); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-clock icon-label"></i>Event End Time</th>
                    <td><?php echo htmlentities($row->EventEndingtime); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-map-marker-alt icon-label"></i>Venue Address</th>
                    <td><?php echo htmlentities($row->VenueAddress); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-star icon-label"></i>Event Type</th>
                    <td><?php echo htmlentities($row->EventType); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-info-circle icon-label"></i>Additional Information</th>
                    <td><?php echo htmlentities($row->AdditionalInformation); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-music icon-label"></i>Service Name</th>
                    <td><?php echo htmlentities($row->ServiceName); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-align-left icon-label"></i>Service Description</th>
                    <td><?php echo htmlentities($row->SerDes); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-dollar-sign icon-label"></i>Service Price</th>
                    <td>$<?php echo htmlentities($row->ServicePrice); ?></td>
                  </tr>
                  <tr>
                    <th><i class="fas fa-calendar-check icon-label"></i>Booking Date</th>
                    <td><?php echo htmlentities($row->BookingDate); ?></td>
                  </tr>
                  <tr style="background:#232323;">
                    <th class="font-bold"><i class="fas fa-clipboard-check icon-label"></i>Booking Status</th>
                    <td class="font-semibold">
                      <?php
                      $status = $row->Status;
                      if ($status == "Approved") {
                        echo "<span class='badge-status badge-approved'><i class='fas fa-check-circle'></i>Approved</span>";
                      } else if ($status == "Cancelled") {
                        echo "<span class='badge-status badge-cancelled'><i class='fas fa-times-circle'></i>Cancelled</span>";
                      } else {
                        echo "<span class='badge-status badge-pending'><i class='fas fa-hourglass-half'></i>Pending</span>";
                      }
                      ?>
                    </td>
                  </tr>
                  <tr style="background:#232323;">
                    <th class="font-bold"><i class="fas fa-sticky-note icon-label"></i>Admin Note</th>
                    <td class="font-semibold">
                      <?php if ($row->Remark == "") { ?>
                        Not updated yet
                      <?php } else { ?>
                        <?php echo htmlentities($row->Remark); ?>
                      <?php } ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
      <?php
          }
        } else {
          echo "<p class='text-center text-gray-400'>Request details not found.</p>";
        }
      } else {
        echo "<p class='text-center text-gray-400'>Invalid request ID.</p>";
      }
      ?>
    </div>

  </main>

  <?php include_once('includes/footer.php'); ?>

</body>

</html>