-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 05:12 PM
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
-- Table structure for table `user`
--


CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `picture` longblob DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','staff') DEFAULT NULL,
  `registrationDate` date NOT NULL,
  `verification_status` enum('incomplete','pending','approved','rejected') DEFAULT 'pending',
  `verification_proof` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `full_name`, `gender`, `picture`, `email`, `password`, `role`, `registrationDate`, `verification_status`, `verification_proof`) VALUES
(72, 'fami', 'Female', NULL, 'famishmn@gmail.com', '$2y$10$PGMnHLWYytSg9js/zZe/X.N6KvOAy8d5MgN8W47/8Eyy23hKR1EpK', 'student', '0000-00-00', 'incomplete', NULL),
(73, 'Alice Johnson', 'Female', NULL, 'alicejohnson@example.com', '$2y$10$MgmQg5y5xqjvvsy.Lmt6/OO2u5VXxomMMct6ASfjvi0SpwXxsp8i.', 'staff', '0000-00-00', '', NULL),
(74, 'Robert Martinez', 'Male', NULL, 'robertmartinez@example.com', '$2y$10$1ROHLPwbKZ2VbU4lK4Q03ef2XUUqYX1bZv7IhZgtHTHu5z2OzYa4u', 'staff', '0000-00-00', '', NULL),
(75, 'Sophia Lee', 'Female', NULL, 'sophialee@example.com', '$2y$10$NNVnbCo4JBhecfAJuMUpt.xTRAAiWUAyVDEyCjymT1FHPVujUw2fC', 'staff', '0000-00-00', '', NULL),
(76, 'John Doe', 'Male', NULL, 'johndoe@example.com', '$2y$10$ltpTfDh0rOOjKv.WZr//bO1zFizDWucdKTJ5CoiXxBDTQOEACMnnO', 'student', '0000-00-00', 'incomplete', NULL),
(77, 'Jane Smith', 'Female', NULL, 'janesmith@example.com', '$2y$10$OVcPCuDbQxUvMno5gr0JoeeXSC2IWBad531mwQ0acSynAsomUAk.u', 'student', '0000-00-00', 'incomplete', NULL),
(79, 'Emily Davis', 'Male', NULL, 'emilydavis@example.com', '$2y$10$gQLO5sB4A3zo.AWReZPmgu/ZKhS6b/GntjwMO.LKJMkr4oaobStUC', 'student', '0000-00-00', 'incomplete', NULL),
(80, 'David Wilson', 'Male', NULL, 'davidwilson@example.com', '$2y$10$HdZOAL945zivCaYI.QYasOoQjd0kpXpcyErTviGi9ehk8zPrmoWku', 'student', '0000-00-00', 'incomplete', NULL),
(81, 'baekhyun', 'Male', NULL, 'bbh@gmail.com', '$2y$10$er7W5aPR7afS.GwkaWop7.A3LsLDh0bACOyilMW98EpjVNH186PPi', 'staff', '0000-00-00', '', NULL),
(82, 'chanyeol', 'Male', NULL, 'pcy@gmail.com', '$2y$10$T0/i2b1aZbfSmz3X9CQhxOezk6VVKnXmruuGSBvEaCNUaP5L69/.m', 'student', '0000-00-00', 'incomplete', NULL);
--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `staff_bonus`
--
ALTER TABLE `staff_bonus`
  ADD PRIMARY KEY (`Bonus_ID`),
  ADD KEY `Staff_ID` (`Staff_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;
COMMIT;

--
-- Constraints for table `staff_bonus`
--
ALTER TABLE `staff_bonus`
  ADD CONSTRAINT `staff_bonus_ibfk_1` FOREIGN KEY (`Staff_ID`) REFERENCES `staff` (`Staff_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
