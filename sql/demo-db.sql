-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 31, 2025 at 01:56 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u511129607_pmds`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_inquiries`
--

CREATE TABLE `booking_inquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `package_name` varchar(100) DEFAULT NULL,
  `package_price` varchar(20) DEFAULT NULL,
  `package_features` text DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `vehicle_name` varchar(50) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `time_slot` varchar(50) DEFAULT NULL,
  `booking_inquiry_date` date DEFAULT NULL,
  `distance` varchar(50) DEFAULT NULL,
  `session_duration` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `budget_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `period` enum('monthly','yearly') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `notification_threshold` int(11) DEFAULT 80,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_4060`
--

CREATE TABLE `car_4060` (
  `id` int(11) NOT NULL,
  `timeslots` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(56) NOT NULL DEFAULT 'empty'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_6975`
--

CREATE TABLE `car_6975` (
  `id` int(11) NOT NULL,
  `timeslots` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(56) NOT NULL DEFAULT 'empty'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_7218`
--

CREATE TABLE `car_7218` (
  `id` int(11) NOT NULL,
  `timeslots` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(56) NOT NULL DEFAULT 'empty'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_two`
--

CREATE TABLE `car_two` (
  `id` int(11) NOT NULL,
  `timeslots` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(56) NOT NULL DEFAULT 'empty'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_attendance`
--

CREATE TABLE `customer_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `cust_id` int(11) NOT NULL,
  `cust_uid` varchar(256) NOT NULL,
  `emp_uid` varchar(256) NOT NULL,
  `cust_name` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `attendance_time` timestamp NOT NULL,
  `time_in` timestamp NOT NULL,
  `time_out` timestamp NULL DEFAULT NULL,
  `vehicle_name` varchar(30) NOT NULL,
  `trainer_name` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_documents`
--

CREATE TABLE `customer_documents` (
  `id` int(11) NOT NULL,
  `cust_uid` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cust_details`
--

CREATE TABLE `cust_details` (
  `id` int(11) NOT NULL,
  `cust_uid` varchar(10) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `totalamount` varchar(256) NOT NULL,
  `paidamount` varchar(256) NOT NULL,
  `dueamount` varchar(256) NOT NULL,
  `payment_method` enum('cash','bank') NOT NULL DEFAULT 'bank',
  `days` varchar(256) NOT NULL,
  `timeslot` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `newlicence` varchar(256) NOT NULL,
  `trainername` varchar(256) NOT NULL,
  `trainerphone` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `endedAT` date NOT NULL,
  `startedAT` date NOT NULL,
  `formfiller` varchar(256) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `emp_uid` varchar(256) NOT NULL,
  `emp_att_qr` longblob NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `aadhar` varchar(12) DEFAULT NULL,
  `dob` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `role` enum('admin','staff','trainer') NOT NULL,
  `joining_date` date NOT NULL,
  `rejoin_date` date DEFAULT NULL,
  `leaving_date` date DEFAULT NULL,
  `address` text NOT NULL,
  `photo` longblob DEFAULT NULL,
  `photo_type` varchar(50) DEFAULT NULL,
  `aadhar_image` longblob DEFAULT NULL,
  `aadhar_image_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_ex_employee` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pre_book_queue`
--

CREATE TABLE `pre_book_queue` (
  `id` int(11) NOT NULL,
  `priority` varchar(256) NOT NULL,
  `timeslot` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(256) NOT NULL DEFAULT 'empty'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','bank') NOT NULL DEFAULT 'cash',
  `transaction_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('income','expense') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_db`
--

CREATE TABLE `users_db` (
  `id` int(11) NOT NULL,
  `emp_uid` varchar(256) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permissions` enum('user','admin','staff','trainer') NOT NULL DEFAULT 'user',
  `time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_db`
--

INSERT INTO `users_db` (`id`, `emp_uid`, `name`, `username`, `password`, `permissions`, `time`) VALUES
(15, NULL, 'Ayush', 'ayushx309', '149fa3342abb7068f1196b192a8c1aef', 'admin', '2024-12-04 14:41:06'),
(17, 'DR2Y57', 'vinod bhai Chauhan ', 'vinod', 'ab49b208848abe14418090d95df0d590', 'trainer', '2024-12-05 09:22:56'),
(16, '3JAV19', 'Hemal patel', 'hemal', '494495f02ae076611eff45c99801355e', 'admin', '2024-12-05 08:58:12'),
(20, 'RWS1BK', 'sandipkumar', 'sandipkumar ', 'a1d2e644cf5204646a9e4be041fc53a3', 'trainer', '2025-03-01 09:49:29'),
(23, '', 'bobby', 'bobby', '81dc9bdb52d04dc20036dbd8313ed055', 'trainer', '2025-03-06 16:15:06');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `category` varchar(256) NOT NULL,
  `vehicle_name` varchar(256) NOT NULL,
  `data_base_table` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `VisitLogs`
--

CREATE TABLE `VisitLogs` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `user_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visit_tracking`
--

CREATE TABLE `visit_tracking` (
  `id` int(11) NOT NULL,
  `visit_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`visit_data`)),
  `timestamp` datetime DEFAULT NULL,
  `ip_address` text DEFAULT NULL,
  `page_url` varchar(2048) DEFAULT NULL,
  `user_agent` varchar(1024) DEFAULT NULL,
  `referrer` varchar(2048) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal` varchar(20) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `asn` varchar(100) DEFAULT NULL,
  `isp` varchar(255) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_inquiries`
--
ALTER TABLE `booking_inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`budget_id`);

--
-- Indexes for table `car_4060`
--
ALTER TABLE `car_4060`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `car_6975`
--
ALTER TABLE `car_6975`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `car_7218`
--
ALTER TABLE `car_7218`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `car_two`
--
ALTER TABLE `car_two`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customer_attendance`
--
ALTER TABLE `customer_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_date` (`cust_id`,`date`);

--
-- Indexes for table `customer_documents`
--
ALTER TABLE `customer_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cust_uid` (`cust_uid`);

--
-- Indexes for table `cust_details`
--
ALTER TABLE `cust_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `emp_uid` (`emp_uid`),
  ADD UNIQUE KEY `aadhar` (`aadhar`);

--
-- Indexes for table `pre_book_queue`
--
ALTER TABLE `pre_book_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users_db`
--
ALTER TABLE `users_db`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `VisitLogs`
--
ALTER TABLE `VisitLogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visit_tracking`
--
ALTER TABLE `visit_tracking`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_inquiries`
--
ALTER TABLE `booking_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_4060`
--
ALTER TABLE `car_4060`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_6975`
--
ALTER TABLE `car_6975`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_7218`
--
ALTER TABLE `car_7218`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_two`
--
ALTER TABLE `car_two`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_attendance`
--
ALTER TABLE `customer_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_documents`
--
ALTER TABLE `customer_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cust_details`
--
ALTER TABLE `cust_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pre_book_queue`
--
ALTER TABLE `pre_book_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_db`
--
ALTER TABLE `users_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `VisitLogs`
--
ALTER TABLE `VisitLogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visit_tracking`
--
ALTER TABLE `visit_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
