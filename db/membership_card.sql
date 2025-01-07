-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2025 at 03:02 AM
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
-- Table structure for table `membership_card`
--

CREATE TABLE `membership_card` (
  `membership_ID` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `qr_code` varchar(255) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `create_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership_card`
--

INSERT INTO `membership_card` (`membership_ID`, `points`, `qr_code`, `CustomerID`, `balance`, `create_date`) VALUES
(28, 15, 'https://quickchart.io/qr?text=User+ID%3A+12%0APoints%3A+15%0ABalance%3A+20.00&size=200', 12, 20.00, '2025-01-07 09:59:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `membership_card`
--
ALTER TABLE `membership_card`
  ADD PRIMARY KEY (`membership_ID`),
  ADD KEY `fk_customerID` (`CustomerID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `membership_card`
--
ALTER TABLE `membership_card`
  MODIFY `membership_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
