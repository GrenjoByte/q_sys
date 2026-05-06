-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 02:58 AM
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
(7, 'Edwardo', '', 'Ventoso', 'Female', '2026-05-05'),
(8, 'Julie Ann', 'Hello', 'Pas', 'Male', '2026-05-04'),
(9, 'Jhon', 'Noel', 'Pandong', 'Male', '2002-07-12'),
(10, 'Jhon', 'Noel', 'Pandong', 'Male', '2002-07-12'),
(11, 'Jhon', 'Noel', 'Pandong', 'Male', '2002-07-12'),
(12, 'Wado', 'Noel', 'Pandong', 'Male', '2002-07-12'),
(13, 'Wih', 'Hello', 'Pamd', 'Male', '2026-05-05'),
(14, 'Alejandro', 'Jafaar', 'Jackson', 'Female', '2005-05-05'),
(15, 'Alejandro', 'Jafaar', 'Jackson', 'Female', '2005-05-05'),
(16, 'Jhon', 'Noel', 'Pandong', 'Male', '2026-05-05'),
(17, 'Julie Ann', '', 'Pas', 'Male', '2026-05-05'),
(18, 'Jhon', 'Noel', 'Pandong', 'Male', '2026-05-05'),
(24, 'Edward', '', 'Alcabasa', 'Male', '2026-05-06'),
(25, 'Nowel', '', 'Alcabasa', 'Male', '2026-05-06'),
(26, 'Hey', '', 'Dude', 'Male', '2026-05-06'),
(27, 'Hey', '', 'Dude', 'Male', '2026-05-06'),
(28, 'Julie Ann', '', 'Hey', 'Male', '2003-03-12'),
(29, 'Julie Ann', '', 'Hey', 'Male', '2026-05-05'),
(30, 'Jhonny', 'Noel', 'Pandong', 'Male', '2026-05-05'),
(31, 'edward', '', 'alcabasa', 'Male', '2026-06-05'),
(32, 'edward', '', 'alcabasa', 'Male', '2026-06-05'),
(33, 'edward', '', 'alcabasa', 'Male', '2026-05-06'),
(34, 'edward', '', 'alcabasa', 'Male', '2026-05-06'),
(35, 'edward', '', 'alcabasa', 'Male', '2026-05-06'),
(36, 'edward', '', 'alcabasa', 'Male', '2026-05-06'),
(37, 'edward', '', 'alcabasa', 'Male', '2026-05-06'),
(38, 'edward', '', 'alcabasa', 'Male', '2026-05-06'),
(39, 'edward', '', 'alcabasa', 'Male', '2026-05-06'),
(40, 'edward', '', 'alcabasa', 'Male', '2026-05-06'),
(41, 'edward', '', 'alcabasa', 'Male', '2026-05-06');

-- --------------------------------------------------------

--
-- Table structure for table `sse_state`
--

CREATE TABLE `sse_state` (
  `state_key` varchar(20) NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sse_state`
--

INSERT INTO `sse_state` (`state_key`, `updated_at`) VALUES
('queue', '2026-05-06 08:50:52');

-- --------------------------------------------------------

--
-- Table structure for table `table_data`
--

CREATE TABLE `table_data` (
  `table_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_value` varchar(20) NOT NULL,
  `table_status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_data`
--

INSERT INTO `table_data` (`table_id`, `user_id`, `table_value`, `table_status`) VALUES
(1, 0, '1', 2),
(2, 2, '2', 2),
(3, 3, '3', 2);

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
  `transaction_status` tinyint(4) NOT NULL,
  `table_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_data`
--

INSERT INTO `transaction_data` (`transaction_data_id`, `client_data_id`, `transaction_type`, `transaction_class`, `transaction_sequence`, `priority_level`, `transaction_schedule`, `transaction_status`, `table_id`) VALUES
(1, 1, 'a', 1, '001', 1, '2026-05-06', 2, 1),
(2, 2, 'a', 1, '001', 2, '2026-05-06', 0, NULL),
(3, 3, 'q', 1, '002', 1, '2026-05-06', 0, NULL),
(4, 4, 'q', 1, '001', 2, '2026-05-06', 0, NULL),
(5, 5, 'a', 1, '002', 2, '2026-05-06', 0, NULL),
(6, 6, 'a', 4, '001', 0, '2026-05-06', 0, NULL),
(7, 7, 'a', 4, '001', 1, '2026-05-06', 0, NULL),
(8, 8, 'q', 2, '001', 1, '2026-05-06', 0, NULL),
(9, 9, 'a', 1, '001', 1, '2026-05-06', 0, NULL),
(10, 10, 'a', 1, '002', 1, '2026-05-06', 0, NULL),
(11, 11, 'a', 1, '003', 1, '2026-05-06', 0, NULL),
(12, 12, 'a', 3, '001', 1, '2026-05-06', 0, NULL),
(13, 13, 'q', 1, '003', 1, '2026-05-06', 0, NULL),
(14, 14, 'a', 1, '001', 1, '2026-05-06', 0, NULL),
(15, 15, 'a', 1, '004', 1, '2026-05-06', 0, NULL),
(16, 16, 'a', 1, '003', 2, '2026-05-06', 0, NULL),
(17, 17, 'a', 1, '004', 2, '2026-05-06', 0, NULL),
(18, 18, 'q', 3, '002', 1, '2026-05-06', 0, NULL),
(19, 19, 'a', 1, '005', 1, '2026-05-06', 0, NULL),
(20, 20, 'a', 1, '005', 2, '2026-05-06', 0, NULL),
(21, 21, 'a', 1, '006', 2, '2026-05-06', 0, NULL),
(22, 22, 'a', 3, '001', 2, '2026-05-06', 0, NULL),
(23, 23, 'q', 2, '013', 1, '2026-05-06', 0, NULL),
(24, 24, 'q', 2, '011', 1, '2026-05-06', 0, NULL),
(25, 25, 'a', 2, '012', 1, '2026-05-06', 0, NULL),
(26, 26, 'a', 1, '007', 2, '2026-05-06', 0, NULL),
(27, 27, 'a', 4, '002', 1, '2026-05-06', 0, NULL),
(28, 28, 'q', 1, '008', 2, '2026-05-06', 0, NULL),
(29, 29, 'q', 4, '003', 1, '2026-05-06', 0, NULL),
(30, 30, 'a', 2, '001', 2, '2026-05-06', 0, NULL),
(31, 31, 'q', 1, '009', 2, '2026-05-06', 0, NULL),
(32, 32, 'q', 2, '002', 2, '2026-05-06', 0, NULL),
(33, 33, 'q', 4, '001', 2, '2026-05-06', 0, NULL),
(34, 34, 'q', 2, '014', 1, '2026-05-06', 0, NULL),
(35, 35, 'q', 1, '006', 1, '2026-05-06', 0, NULL),
(36, 36, 'q', 2, '015', 1, '2026-05-06', 0, NULL),
(37, 37, 'q', 2, '003', 2, '2026-05-06', 0, NULL),
(38, 38, 'q', 3, '002', 2, '2026-05-06', 0, NULL),
(39, 39, 'q', 1, '007', 1, '2026-05-06', 0, NULL),
(40, 40, 'q', 2, '016', 1, '2026-05-06', 0, NULL),
(41, 41, 'q', 1, '002', 1, '2026-05-06', 0, NULL);

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

--
-- Dumping data for table `user_data`
--

INSERT INTO `user_data` (`user_id`, `user_name`, `user_password`, `user_type`) VALUES
(0, 'grenjo8', '$2y$10$1zrxub83YgAZVPedpXJn0eUJRlUCMoLjkUV2wU5bJ8ZWqB.vXPwxW', 8),
(2, 'edward', '$2y$10$FJub4OA5Jf0asA5awRyJG.DXR962pTBomy1wb/ktaLQ9aGQ.vx85u', 2),
(3, 'Noel', '$2y$10$SoS.WWmtJumnNwrOs1lqieFI36CSYGpSP5hxuZCYpKC9miYQn0Cvu', 2);

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
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_id`, `first_name`, `middle_name`, `last_name`, `email`) VALUES
(0, 'Renzo', '', 'Advincula', 'r.advincula.psa@gmail.com'),
(2, 'Edward', '', 'Alcabasa', 'edward.alcabasa@ssu.edu.ph'),
(3, 'Jhon Noel', '', 'Pandong', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` int(11) NOT NULL,
  `priority_level` varchar(20) NOT NULL DEFAULT 'regular',
  `transaction_class` varchar(20) NOT NULL DEFAULT 'registration',
  `counter_window` varchar(20) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`user_id`, `priority_level`, `transaction_class`, `counter_window`, `table_id`) VALUES
(0, 'priority', 'registration', '1', 1),
(2, 'regular', 'update', '2', 2),
(3, 'priority', 'inquiry', '3', 3),
(4, 'priority', 'update', '1', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client_data`
--
ALTER TABLE `client_data`
  ADD PRIMARY KEY (`client_data_id`);

--
-- Indexes for table `sse_state`
--
ALTER TABLE `sse_state`
  ADD PRIMARY KEY (`state_key`);

--
-- Indexes for table `table_data`
--
ALTER TABLE `table_data`
  ADD PRIMARY KEY (`table_id`);

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
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client_data`
--
ALTER TABLE `client_data`
  MODIFY `client_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `table_data`
--
ALTER TABLE `table_data`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaction_data`
--
ALTER TABLE `transaction_data`
  MODIFY `transaction_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
