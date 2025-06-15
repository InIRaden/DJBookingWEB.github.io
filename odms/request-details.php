<?php
//error_reporting(0);
session_start(); // Tambahkan ini jika belum ada dan diperlukan untuk header
include('includes/dbconnection.php');

// Ambil bookingid dari GET request, pastikan aman
$booking_id_display = isset($_GET['bookingid']) ? htmlspecialchars($_GET['bookingid']) : 'Tidak Diketahui';
$eid = isset($_GET['bid']) ? $_GET['bid'] : null;

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <title>Online DJ Management System || Detail Permintaan</title>
  <!-- Menggunakan Tailwind CSS dari src/output.css seperti di about.php -->
  <link rel="stylesheet" href="../src/output.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: "Inter", sans-serif;
    }

    /* Anda bisa menambahkan custom CSS di sini jika diperlukan, atau memindahkannya ke file CSS terpisah */
    .table-modern {
      width: 100%;
      border-collapse: collapse;
    }

    .table-modern th,
    .table-modern td {
      border: 1px solid #4A5568;
      /* gray-700 */
      padding: 0.75rem;
      /* p-3 */
      text-align: left;
    }

    .table-modern th {
      background-color: #2D3748;
      /* gray-800 */
      color: white;
      font-weight: 600;
    }

    .table-modern tr:nth-child(even) {
      background-color: #1A202C;
      /* gray-900 */
    }

    .header-container {
      position: relative;
      width: 100%;
      height: 300px;
      overflow: hidden;
    }

    .header-image {
      width: 100%;
      height: 300px;
      object-fit: cover;
    }

    .header-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.7) 60%, rgba(0, 0, 0, 0.1) 100%);
    }

    .header-content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      width: 100%;
      max-width: 500px;
      padding: 0 20px;
      z-index: 2;
    }

    .header-title {
      font-size: 2.2rem;
      font-weight: 800;
      color: rgba(255, 255, 255, 0.92);
      margin-bottom: 1rem;
      text-shadow: 0 0 10px #fff, 0 0 18px #2563eb, 2px 2px 8px rgba(0, 0, 0, 0.3);
      letter-spacing: 1px;
    }

    .header-text {
      color: rgba(255, 255, 255, 0.8);
      font-size: 1.1rem;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }
  </style>
</head>

<body class="bg-black text-white">

  <header class="relative">
    <?php include_once('includes/header.php'); ?>
    <div class="header-container">
      <img src="images/abt.jpg" alt="DJ performing at event" class="w-full h-[300px] object-cover header-image" />
      <div class="header-overlay"></div>
      <div class="header-content">
        <h1 class="header-title">Detail Permintaan</h1>
        <p class="header-text">Lihat detail permintaan booking Anda di sini</p>
      </div>
    </div>
  </header>

  <!-- Konten Utama -->
  <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs mb-8">
      <a href="index.php" class="text-gray-400 hover:text-white">Home</a>
      <span class="text-gray-600">/</span>
      <span class="text-white">Detail Permintaan</span>
    </div>

    <h2 class="font-semibold text-white text-lg mb-6">Detail Permintaan #<?php echo $booking_id_display; ?></h2>

    <div class="bg-gray-800 shadow-md rounded-lg p-6 md:p-8">
      <?php
      if ($eid) {
        $sql = "SELECT tblbooking.BookingID,tblbooking.Name,tblbooking.MobileNumber,tblbooking.Email,tblbooking.EventDate,tblbooking.EventStartingtime,tblbooking.EventEndingtime,tblbooking.VenueAddress,tblbooking.EventType,tblbooking.AdditionalInformation,tblbooking.BookingDate,tblbooking.Remark,tblbooking.Status,tblbooking.UpdationDate,tblservice.ServiceName,tblservice.SerDes,tblservice.ServicePrice from tblbooking join tblservice on tblbooking.ServiceID=tblservice.ID  where tblbooking.ID=:eid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
          foreach ($results as $row) {               ?>
            <div class="overflow-x-auto">
              <table class="table-modern">
                <tbody>
                  <tr>
                    <th class="w-1/3 md:w-1/4">Nomor Booking</th>
                    <td class="w-2/3 md:w-3/4"><?php echo htmlentities($row->BookingID); ?></td>
                  </tr>
                  <tr>
                    <th>Nama Klien</th>
                    <td><?php echo htmlentities($row->Name); ?></td>
                  </tr>
                  <tr>
                    <th>Nomor HP</th>
                    <td><?php echo htmlentities($row->MobileNumber); ?></td>
                  </tr>
                  <tr>
                    <th>Email</th>
                    <td><?php echo htmlentities($row->Email); ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Acara</th>
                    <td><?php echo htmlentities($row->EventDate); ?></td>
                  </tr>
                  <tr>
                    <th>Waktu Mulai Acara</th>
                    <td><?php echo htmlentities($row->EventStartingtime); ?></td>
                  </tr>
                  <tr>
                    <th>Waktu Selesai Acara</th>
                    <td><?php echo htmlentities($row->EventEndingtime); ?></td>
                  </tr>
                  <tr>
                    <th>Alamat Tempat Acara</th>
                    <td><?php echo htmlentities($row->VenueAddress); ?></td>
                  </tr>
                  <tr>
                    <th>Tipe Acara</th>
                    <td><?php echo htmlentities($row->EventType); ?></td>
                  </tr>
                  <tr>
                    <th>Informasi Tambahan</th>
                    <td><?php echo htmlentities($row->AdditionalInformation); ?></td>
                  </tr>
                  <tr>
                    <th>Nama Layanan</th>
                    <td><?php echo htmlentities($row->ServiceName); ?></td>
                  </tr>
                  <tr>
                    <th>Deskripsi Layanan</th>
                    <td><?php echo htmlentities($row->SerDes); ?></td>
                  </tr>
                  <tr>
                    <th>Harga Layanan</th>
                    <td>$<?php echo htmlentities($row->ServicePrice); ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Pengajuan</th>
                    <td><?php echo htmlentities($row->BookingDate); ?></td>
                  </tr>
                  <tr class="bg-gray-700">
                    <th class="font-bold">Status Final Pesanan</th>
                    <td class="font-semibold">
                      <?php
                      $status = $row->Status;
                      if ($row->Status == "Approved") {
                        echo "<span class='text-green-400'>Booking Anda telah disetujui</span>";
                      } else if ($row->Status == "Cancelled") {
                        echo "<span class='text-red-400'>Booking Anda telah dibatalkan</span>";
                      } else {
                        echo "<span class='text-yellow-400'>Belum ada respons</span>";
                      }
                      ?>
                    </td>
                  </tr>
                  <tr class="bg-gray-700">
                    <th class="font-bold">Catatan Admin</th>
                    <td class="font-semibold">
                      <?php if ($row->Remark == "") { ?>
                        <?php echo "Belum diperbarui"; ?>
                      <?php } else { ?>
                        <?php echo htmlentities($row->Remark); // Menggunakan Remark sesuai database, bukan Status
                        ?>
                      <?php } ?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
      <?php
          }
        } else {
          echo "<p class='text-center text-gray-400'>Detail permintaan tidak ditemukan.</p>";
        }
      } else {
        echo "<p class='text-center text-gray-400'>ID Permintaan tidak valid.</p>";
      }
      ?>
    </div>

  </main>

  <?php include_once('includes/footer.php'); ?>

</body>

</html>