-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Jun 2025 pada 09.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `odmsdb`
--

DELIMITER $$
--
-- Prosedur
-- Stored Procedure untuk membuat booking dan payment sekaligus
CREATE PROCEDURE sp_create_booking_and_payment (
    IN p_bookingid VARCHAR(20), 
    IN p_serviceid INT,
    -- ... parameter lainnya ...
    OUT p_success BOOLEAN, 
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_booking_exists INT DEFAULT 0;
    
    -- Conditional untuk cek booking exists
    IF v_booking_exists > 0 THEN
        SET p_success = FALSE;
        SET p_message = 'Booking ID already exists';
    ELSE
        -- Transaction untuk insert booking dan payment
        START TRANSACTION;
        -- ... kode insert ...
        IF ROW_COUNT() > 0 THEN
            COMMIT;
        ELSE
            ROLLBACK;
        END IF;
    END IF;
END

-- Stored Procedure untuk membuat cicilan pembayaran
CREATE PROCEDURE sp_create_installments (
    IN p_bookingid INT,
    IN p_amount DECIMAL(10,2),
    IN p_bank VARCHAR(50),
    IN p_count INT
)
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE cicilan DECIMAL(10,2);
    SET cicilan = p_amount / p_count;

    -- Loop untuk membuat record cicilan
    WHILE i <= p_count DO
        INSERT INTO tblpayment (...) VALUES (...);
        SET i = i + 1;
    END WHILE;
END

--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_booking_and_payment` (IN `p_bookingid` VARCHAR(20), IN `p_serviceid` INT, IN `p_name` VARCHAR(200), IN `p_mobilenumber` VARCHAR(20), IN `p_email` VARCHAR(200), IN `p_eventdate` VARCHAR(200), IN `p_eventstartingtime` VARCHAR(200), IN `p_eventendingtime` VARCHAR(200), IN `p_venueaddress` MEDIUMTEXT, IN `p_eventtype` VARCHAR(200), IN `p_additionalinformation` MEDIUMTEXT, IN `p_paymentmethod` ENUM('cash','transfer','installment'), IN `p_amount` DECIMAL(10,2), IN `p_transferbank` VARCHAR(50), OUT `p_success` BOOLEAN, OUT `p_message` VARCHAR(255))   BEGIN
    DECLARE v_booking_exists INT DEFAULT 0;
    
    
    SET p_success = FALSE;
    SET p_message = 'An error occurred during processing';
    
    
    SELECT COUNT(*) INTO v_booking_exists FROM tblbooking WHERE BookingID = p_bookingid;
    
    IF v_booking_exists > 0 THEN
        SET p_success = FALSE;
        SET p_message = 'Booking ID already exists';
    ELSE
        
        START TRANSACTION;
        
        
        INSERT INTO tblbooking(BookingID, ServiceID, Name, MobileNumber, Email, EventDate, 
                              EventStartingtime, EventEndingtime, VenueAddress, EventType, 
                              AdditionalInformation, BookingDate, Status)
        VALUES(p_bookingid, p_serviceid, p_name, p_mobilenumber, p_email, p_eventdate,
              p_eventstartingtime, p_eventendingtime, p_venueaddress, p_eventtype,
              p_additionalinformation, NOW(), 'Pending');
        
        
        INSERT INTO tblpayment(BookingID, PaymentMethod, Amount, TransferBank, PaymentStatus, PaymentDate)
        VALUES(p_bookingid, p_paymentmethod, p_amount, p_transferbank, 'Pending', NOW());
        
        
        IF ROW_COUNT() > 0 THEN
            COMMIT;
            SET p_success = TRUE;
            SET p_message = 'Booking and payment created successfully';
        ELSE
            ROLLBACK;
            SET p_success = FALSE;
            SET p_message = 'Failed to create payment record';
        END IF;
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_installments` (IN `p_bookingid` INT, IN `p_amount` DECIMAL(10,2), IN `p_bank` VARCHAR(50), IN `p_count` INT)   BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE cicilan DECIMAL(10,2);
    SET cicilan = p_amount / p_count;

    WHILE i <= p_count DO
        INSERT INTO tblpayment (
            BookingID,
            PaymentMethod,
            Amount,
            TransferBank,
            VirtualAccountNumber,
            PaymentStatus,
            PaymentDate,
            InstallmentCount
        ) VALUES (
            p_bookingid,
            'Installment',
            cicilan,
            p_bank,
            FLOOR(RAND() * 9999999999999999),
            'Pending',
            NOW(),
            i
        );
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` varchar(120) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(120) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'bagas', 'admin', 5689730939, 'bagasadhinugraha6@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-09-01 11:48:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblbooking`
--

CREATE TABLE `tblbooking` (
  `ID` int(10) NOT NULL,
  `BookingID` int(10) DEFAULT NULL,
  `ServiceID` int(10) DEFAULT NULL,
  `Name` varchar(200) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `EventDate` varchar(200) DEFAULT NULL,
  `EventStartingtime` varchar(200) DEFAULT NULL,
  `EventEndingtime` varchar(200) DEFAULT NULL,
  `VenueAddress` mediumtext DEFAULT NULL,
  `EventType` varchar(200) DEFAULT NULL,
  `AdditionalInformation` mediumtext DEFAULT NULL,
  `BookingDate` timestamp NULL DEFAULT current_timestamp(),
  `Remark` varchar(200) DEFAULT NULL,
  `Status` varchar(200) DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tblbooking`
--

INSERT INTO `tblbooking` (`ID`, `BookingID`, `ServiceID`, `Name`, `MobileNumber`, `Email`, `EventDate`, `EventStartingtime`, `EventEndingtime`, `VenueAddress`, `EventType`, `AdditionalInformation`, `BookingDate`, `Remark`, `Status`, `UpdationDate`) VALUES
(91, 517809124, 1, 'Raden Mahesa', 811111111, 'radenmahesa8@upi.edu', '2025-06-14', '12 p.m', '2 p.m', 'Komplek permata Biru', 'Birthday Party', '-', '2025-06-14 13:35:30', NULL, 'Pending', NULL),
(92, 267847921, 1, 'Esaman', 811111111, 'iniradenmahesa8@gmail.com', '2025-06-26', '5 p.m', '3 p.m', 'asd', 'Social', 'asd', '2025-06-14 23:35:45', 'berhasil', 'Approved', '2025-06-14 23:36:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbleventtype`
--

CREATE TABLE `tbleventtype` (
  `ID` int(10) NOT NULL,
  `EventType` varchar(200) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbleventtype`
--

INSERT INTO `tbleventtype` (`ID`, `EventType`, `CreationDate`) VALUES
(1, 'Anniversary', '2024-09-10 07:01:39'),
(2, 'Birthday Party', '2024-09-10 07:01:39'),
(3, 'Charity', '2024-09-10 07:01:39'),
(4, 'Cocktail', '2024-09-10 07:01:39'),
(5, 'College', '2024-09-10 07:01:39'),
(6, 'Community', '2024-09-10 07:01:39'),
(7, 'Concert', '2024-09-10 07:01:39'),
(8, 'Engagement', '2024-09-10 07:01:39'),
(9, 'Get Together', '2024-09-10 07:01:39'),
(10, 'Government', '2024-09-10 07:01:39'),
(11, 'Night Club', '2024-09-10 07:01:39'),
(13, 'Post Wedding', '2024-09-10 07:01:39'),
(14, 'Pre Engagement', '2024-09-10 07:01:39'),
(15, 'Religious', '2024-09-10 07:01:39'),
(16, 'Sangeet', '2024-09-10 07:01:39'),
(17, 'Social', '2024-09-10 07:01:39'),
(18, 'Wedding', '2024-09-10 07:01:39'),
(22, 'Nikahan', '2025-06-12 04:12:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblpage`
--

CREATE TABLE `tblpage` (
  `ID` int(10) NOT NULL,
  `PageType` varchar(100) DEFAULT NULL,
  `PageTitle` mediumtext DEFAULT NULL,
  `PageDescription` mediumtext DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tblpage`
--

INSERT INTO `tblpage` (`ID`, `PageType`, `PageTitle`, `PageDescription`, `Email`, `MobileNumber`, `UpdationDate`) VALUES
(1, 'aboutus', 'About Us', '<b>Online DJ Management System</b><div><b>ODMS&nbsp;is one of the Internet\'s largest and trusted Online DJ Booking Service. ODMS has done several placements locally &amp; globally for top artists.</b></div><div><b><br></b></div><div><b>&nbsp;Test data for testing.</b></div>', NULL, NULL, '2024-09-11 15:36:25'),
(2, 'contactus', 'Contact Us', 'D-204, Hole Town South West,Delhi-110096,India', 'info@gmail.com', 1234567890, '2024-09-11 15:36:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblpayment`
--

CREATE TABLE `tblpayment` (
  `ID` int(50) NOT NULL,
  `BookingID` int(50) NOT NULL,
  `PaymentMethod` enum('cash','transfer','Installment','') DEFAULT NULL,
  `PaymentStatus` enum('Pending','Paid','Failed','') NOT NULL,
  `VirtualAccountNumber` varchar(40) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `TransferBank` varchar(255) NOT NULL,
  `CompletedDate` timestamp NULL DEFAULT NULL,
  `InstallmentCount` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tblpayment`
--

INSERT INTO `tblpayment` (`ID`, `BookingID`, `PaymentMethod`, `PaymentStatus`, `VirtualAccountNumber`, `Amount`, `PaymentDate`, `TransferBank`, `CompletedDate`, `InstallmentCount`) VALUES
(43, 517809124, 'transfer', 'Pending', '', 800.00, '2025-06-14 13:35:30', 'BCA', NULL, NULL),
(44, 267847921, 'transfer', 'Paid', '4493540846206687', 800.00, '2025-06-14 23:35:45', 'BCA', '2025-06-14 18:35:45', 0);

--
-- Trigger `tblpayment`
--
DELIMITER $$
CREATE TRIGGER `trg_payment_paid_update_booking` AFTER UPDATE ON `tblpayment` FOR EACH ROW BEGIN
    IF NEW.PaymentStatus = 'Paid' AND OLD.PaymentStatus != 'Paid' THEN
        UPDATE tblbooking
        SET Status = 'Confirmed'
        WHERE BookingID = NEW.BookingID;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tblservice`
--

CREATE TABLE `tblservice` (
  `ID` int(10) NOT NULL,
  `ServiceName` varchar(200) DEFAULT NULL,
  `SerDes` varchar(250) NOT NULL,
  `ServicePrice` varchar(200) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tblservice`
--

INSERT INTO `tblservice` (`ID`, `ServiceName`, `SerDes`, `ServicePrice`, `CreationDate`) VALUES
(1, 'Wedding DJ', '(we install the DJ equipment before your ceremony or after your wedding breakfast)', '800', '2024-09-20 07:17:43'),
(2, 'Party DJ', '(we install the DJ equipment 1 hour before your selected event start time)', '700', '2024-09-20 07:17:43'),
(3, 'Ceremony Music', 'Our ceremony music service is a popular add on to our wedding DJ stay all day hire.', '650', '2024-09-20 07:17:43'),
(4, 'Photo Booth Hire', '(early equipment setup included)', '500', '2024-09-20 07:17:43'),
(5, 'Karaoke Add-on', 'Karaoke is a great alternative to a disco. It’s perfect for staff parties and children’s parties.', '450', '2024-09-20 07:17:43'),
(6, 'Uplighters', 'Uplighters are bright lighting fixtures which are installed on the floor and shine a vibrant wash of colour over the walls of your venue', '200', '2024-09-20 07:17:43'),
(10, 'Nikahan', 'cocok untuk arul', '900', '2025-06-11 04:23:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbluser`
--

CREATE TABLE `tbluser` (
  `ID` int(10) NOT NULL,
  `Name` varchar(200) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Message` mediumtext DEFAULT NULL,
  `MsgDate` timestamp NULL DEFAULT current_timestamp(),
  `IsRead` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbluser`
--

INSERT INTO `tbluser` (`ID`, `Name`, `MobileNumber`, `Email`, `Message`, `MsgDate`, `IsRead`) VALUES
(2, 'John Doe', 1231231230, 'hhdoe12@gmail.com', 'NA', '2024-12-11 15:06:01', 1),
(3, 'Bagas 79 Nugraha', 5885725027, 'bagasadhinugraha6@gmail.com', 'ddddd', '2025-06-11 09:53:41', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbluser_login`
--

CREATE TABLE `tbluser_login` (
  `ID` int(10) NOT NULL,
  `UserName` varchar(120) NOT NULL,
  `NameUser` varchar(120) NOT NULL,
  `MobileNumber` bigint(13) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Password` varchar(120) NOT NULL,
  `AdminRegdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbluser_login`
--

INSERT INTO `tbluser_login` (`ID`, `UserName`, `NameUser`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'eca', 'Ecaaaa', 83170887883, 'radenmahesa8@upi.edu', '9bd8c49d6759135b7bac1f3fc03d3fdc', '2025-05-23 16:21:11'),
(2, 'bagas', 'Bagas Adhi', 812345678, 'bagas@gmail.com', '5ffd9bb73b00bce4feeb77e2d12722da', '2025-05-23 12:18:12'),
(3, 'fia', 'Via Lokasi', 87777666555, 'fia@gmail.com', '585f722f0471e7b82b290dcb5c00e5ef', '2025-05-23 12:29:47');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks untuk tabel `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ServiceID` (`ServiceID`),
  ADD KEY `EventType` (`EventType`);

--
-- Indeks untuk tabel `tbleventtype`
--
ALTER TABLE `tbleventtype`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `EventType` (`EventType`);

--
-- Indeks untuk tabel `tblpage`
--
ALTER TABLE `tblpage`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks untuk tabel `tblpayment`
--
ALTER TABLE `tblpayment`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks untuk tabel `tblservice`
--
ALTER TABLE `tblservice`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indeks untuk tabel `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks untuk tabel `tbluser_login`
--
ALTER TABLE `tbluser_login`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tblbooking`
--
ALTER TABLE `tblbooking`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT untuk tabel `tbleventtype`
--
ALTER TABLE `tbleventtype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `tblpage`
--
ALTER TABLE `tblpage`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tblpayment`
--
ALTER TABLE `tblpayment`
  MODIFY `ID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `tblservice`
--
ALTER TABLE `tblservice`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tbluser_login`
--
ALTER TABLE `tbluser_login`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
