-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 06:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
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
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'Administrator ', 'admin', 5689784589, 'admin@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-09-01 11:48:13');

-- --------------------------------------------------------

--
-- Table structure for table `tblbooking`
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
-- Dumping data for table `tblbooking`
--

INSERT INTO `tblbooking` (`ID`, `BookingID`, `ServiceID`, `Name`, `MobileNumber`, `Email`, `EventDate`, `EventStartingtime`, `EventEndingtime`, `VenueAddress`, `EventType`, `AdditionalInformation`, `BookingDate`, `Remark`, `Status`, `UpdationDate`) VALUES
(1, 916246474, 1, 'Anuj kumar', 1234563210, 'anujk.12@gmail.com', '2024-11-23', '5 p.m', '10 p.m', 'Test Address', 'Concert', 'NA', '2024-11-19 10:52:17', 'Booking approved', 'Approved', '2024-12-06 06:36:45'),
(2, 595445052, 1, 'Rahul', 4564564563, 'rahul@gmail.com', '2025-01-10', '5 p.m', '9 p.m', 'A 123 Xyz Apartment Mayur Vihar New Delhi', 'Wedding', 'NA', '2024-12-11 15:03:06', 'Booking request approved', 'Approved', '2024-12-11 15:03:56');

-- --------------------------------------------------------

--
-- Table structure for table `tbleventtype`
--

CREATE TABLE `tbleventtype` (
  `ID` int(10) NOT NULL,
  `EventType` varchar(200) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbleventtype`
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
(18, 'Wedding', '2024-09-10 07:01:39');

-- --------------------------------------------------------

--
-- Table structure for table `tblpage`
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
-- Dumping data for table `tblpage`
--

INSERT INTO `tblpage` (`ID`, `PageType`, `PageTitle`, `PageDescription`, `Email`, `MobileNumber`, `UpdationDate`) VALUES
(1, 'aboutus', 'About Us', '<b>Online DJ Management System</b><div><b>ODMS&nbsp;is one of the Internet\'s largest and trusted Online DJ Booking Service. ODMS has done several placements locally &amp; globally for top artists.</b></div><div><b><br></b></div><div><b>&nbsp;Test data for testing.</b></div>', NULL, NULL, '2024-09-11 15:36:25'),
(2, 'contactus', 'Contact Us', 'D-204, Hole Town South West,Delhi-110096,India', 'info@gmail.com', 1234567890, '2024-09-11 15:36:25');

-- --------------------------------------------------------

--
-- Table structure for table `tblservice`
--

CREATE TABLE `tblservice` (
  `ID` int(10) NOT NULL,
  `ServiceName` varchar(200) DEFAULT NULL,
  `SerDes` varchar(250) NOT NULL,
  `ServicePrice` varchar(200) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblservice`
--

INSERT INTO `tblservice` (`ID`, `ServiceName`, `SerDes`, `ServicePrice`, `CreationDate`) VALUES
(1, 'Wedding DJ', '(we install the DJ equipment before your ceremony or after your wedding breakfast)', '800', '2024-09-20 07:17:43'),
(2, 'Party DJ', '(we install the DJ equipment 1 hour before your selected event start time)', '700', '2024-09-20 07:17:43'),
(3, 'Ceremony Music', 'Our ceremony music service is a popular add on to our wedding DJ stay all day hire.', '650', '2024-09-20 07:17:43'),
(4, 'Photo Booth Hire', '(early equipment setup included)', '500', '2024-09-20 07:17:43'),
(5, 'Karaoke Add-on', 'Karaoke is a great alternative to a disco. It’s perfect for staff parties and children’s parties.', '450', '2024-09-20 07:17:43'),
(6, 'Uplighters', 'Uplighters are bright lighting fixtures which are installed on the floor and shine a vibrant wash of colour over the walls of your venue', '200', '2024-09-20 07:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
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
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`ID`, `Name`, `MobileNumber`, `Email`, `Message`, `MsgDate`, `IsRead`) VALUES
(2, 'John Doe', 1231231230, 'hhdoe12@gmail.com', 'NA', '2024-12-11 15:06:01', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ServiceID` (`ServiceID`),
  ADD KEY `EventType` (`EventType`);

--
-- Indexes for table `tbleventtype`
--
ALTER TABLE `tbleventtype`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `EventType` (`EventType`);

--
-- Indexes for table `tblpage`
--
ALTER TABLE `tblpage`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblservice`
--
ALTER TABLE `tblservice`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblbooking`
--
ALTER TABLE `tblbooking`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbleventtype`
--
ALTER TABLE `tbleventtype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tblpage`
--
ALTER TABLE `tblpage`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblservice`
--
ALTER TABLE `tblservice`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
