-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2026 at 07:36 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `q_sys_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_data`
--

CREATE TABLE `client_data` (
  `client_data_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` varchar(7) NOT NULL,
  `birthdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_data`
--

INSERT INTO `client_data` (`client_data_id`, `first_name`, `middle_name`, `last_name`, `gender`, `birthdate`) VALUES
(1, 'Ben', '', 'Abaigar', 'Male', '2026-05-05'),
(2, 'Edward', '', 'Alcabasa', 'Male', '2026-05-05'),
(3, 'Jhon', '', 'Pandong', 'Female', '2026-05-04'),
(4, 'Alejandro', '', 'Daguman', 'Female', '2026-05-04'),
(5, 'Juliana', '', 'Ventoso', 'Female', '2026-05-05'),
(6, 'Juliana Mae', '', 'Ventoso', 'Female', '2026-05-05'),
(7, 'Edwardo', '', 'Ventoso', 'Female', '2026-05-05');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_data`
--

CREATE TABLE `transaction_data` (
  `transaction_data_id` int(11) NOT NULL,
  `client_data_id` int(11) NOT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `transaction_class` int(11) NOT NULL,
  `transaction_sequence` varchar(3) NOT NULL,
  `priority_level` int(11) NOT NULL,
  `transaction_schedule` date NOT NULL,
  `transaction_status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_data`
--

INSERT INTO `transaction_data` (`transaction_data_id`, `client_data_id`, `transaction_type`, `transaction_class`, `transaction_sequence`, `priority_level`, `transaction_schedule`, `transaction_status`) VALUES
(1, 1, 'a', 1, '001', 1, '2026-05-04', 0),
(2, 2, 'a', 1, '001', 2, '2026-05-05', 0),
(3, 3, 'q', 1, '002', 1, '2026-05-04', 0),
(4, 4, 'q', 1, '001', 2, '2026-05-04', 1),
(5, 5, 'a', 1, '002', 2, '2026-05-05', 0),
(6, 6, 'a', 4, '001', 0, '2026-05-30', 0),
(7, 7, 'a', 4, '001', 1, '2026-05-05', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

CREATE TABLE `user_data` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_password` varchar(300) NOT NULL,
  `user_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client_data`
--
ALTER TABLE `client_data`
  ADD PRIMARY KEY (`client_data_id`);

--
-- Indexes for table `transaction_data`
--
ALTER TABLE `transaction_data`
  ADD PRIMARY KEY (`transaction_data_id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client_data`
--
ALTER TABLE `client_data`
  MODIFY `client_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaction_data`
--
ALTER TABLE `transaction_data`
  MODIFY `transaction_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
