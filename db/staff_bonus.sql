-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2025 at 03:03 AM
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
-- Table structure for table `staff_bonus`
--

CREATE TABLE `staff_bonus` (
  `Bonus_ID` int(11) NOT NULL,
  `Staff_ID` int(11) NOT NULL,
  `Date_Recorded` date NOT NULL,
  `Bonus_Amount` decimal(10,2) NOT NULL,
  `POINTS_ACCUMULATED` int(11) DEFAULT 0,
  `BONUS_EARNED` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_bonus`
--

INSERT INTO `staff_bonus` (`Bonus_ID`, `Staff_ID`, `Date_Recorded`, `Bonus_Amount`, `POINTS_ACCUMULATED`, `BONUS_EARNED`) VALUES
(1, 72, '2025-01-06', 10.00, 10, 50.00),
(2, 74, '2025-01-06', 20.00, 20, 30.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `staff_bonus`
--
ALTER TABLE `staff_bonus`
  ADD PRIMARY KEY (`Bonus_ID`),
  ADD KEY `Staff_ID` (`Staff_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
