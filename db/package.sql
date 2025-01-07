-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2025 at 03:10 AM
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
-- Database: `rapidprint`
--

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `packageID` int(11) NOT NULL,
  `branchID` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `package_detail` text NOT NULL,
  `price` decimal(10,2) NOT NULL COMMENT 'Price in MYR',
  `status` enum('Available','Unavailable') DEFAULT 'Available',
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`packageID`, `branchID`, `package_name`, `package_detail`, `price`, `status`, `qr_code`) VALUES
(4, 6, 'Basic Package', 'Black & White, One Sided', 0.50, 'Available', 'qrcodes/basic.png'),
(5, 6, 'Premium Package', 'Color, Two Sided', 1.50, 'Available', 'qrcodes/premium.png'),
(6, 6, 'Standard Package', 'Color, One Sided', 1.00, 'Available', 'qrcodes/standard.png'),
(7, 6, 'Normal Package', 'Black & White, One Sided', 0.20, 'Available', 'qrcodes/normal.png'),
(16, 15, 'Gold', 'All,  All', 2.00, 'Available', 'qrcodes/gold.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`packageID`),
  ADD KEY `branchID` (`branchID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `packageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
