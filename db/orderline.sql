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
-- Table structure for table `orderline`
--

CREATE TABLE `orderline` (
  `OrderLine_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Package_ID` int(11) NOT NULL,
  `File` varchar(255) DEFAULT NULL,
  `Colour` varchar(50) DEFAULT NULL,
  `Print_Quality` varchar(50) DEFAULT NULL,
  `Add_Service` varchar(100) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `Total_Cost` decimal(10,2) NOT NULL,
  `Page` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderline`
--

INSERT INTO `orderline` (`OrderLine_ID`, `Order_ID`, `Package_ID`, `File`, `Colour`, `Print_Quality`, `Add_Service`, `Quantity`, `Total_Cost`, `Page`) VALUES
(7, 13, 5, 'uploads/ASSIGNMENT 3 - Rubric_SEMI20242025.pdf', 'Colour', 'Low', 'Stapler', 1, 1.50, 12);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderline`
--
ALTER TABLE `orderline`
  ADD PRIMARY KEY (`OrderLine_ID`),
  ADD UNIQUE KEY `Order_ID` (`Order_ID`,`Package_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderline`
--
ALTER TABLE `orderline`
  MODIFY `OrderLine_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
