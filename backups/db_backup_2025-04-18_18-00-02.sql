

CREATE TABLE `booking_inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `budgets` (
  `budget_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `period` enum('monthly','yearly') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `notification_threshold` int(11) DEFAULT 80,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`budget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `car_4060` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timeslots` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(56) NOT NULL DEFAULT 'empty',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `car_6975` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timeslots` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(56) NOT NULL DEFAULT 'empty',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `car_7218` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timeslots` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(56) NOT NULL DEFAULT 'empty',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `car_two` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timeslots` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(56) NOT NULL DEFAULT 'empty',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `cust_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cust_uid` varchar(10) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `totalamount` varchar(256) NOT NULL,
  `paidamount` varchar(256) NOT NULL,
  `dueamount` varchar(256) NOT NULL,
  `payment_method` enum('cash','bank') NOT NULL DEFAULT 'bank',
  `app_md5_pass` varchar(32) DEFAULT NULL,
  `app_password_hash` varchar(255) DEFAULT NULL,
  `app_pin_hash` varchar(255) DEFAULT NULL,
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
  `formfiller` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO cust_details VALUES('4','admin123','admin','','14191419','','','','','bank','admin','admin','admin','','','','','','','0000-00-00','00:00:00','0000-00-00','0000-00-00','');
INSERT INTO cust_details VALUES('5','UID001','Test User','','111','','','','','bank','698d51a19d8a121ce581499d7b701668',NULL,NULL,'','','','','','','0000-00-00','00:00:00','0000-00-00','0000-00-00','');
INSERT INTO cust_details VALUES('1','12345','John Doe','john.doe@example.com','1234567890','1234 Elm Street','1000.00','500.00','500.00','','d41d8cd98f00b204e9800998ecf8427e','abc1234567890abcdef','1234abcd','Monday, Tuesday','10:00 AM - 12:00 PM','Sedan','ABC1234567','Jane Smith','9876543210','2025-04-18','10:30:00','2025-04-18','2025-04-18','Admin');
INSERT INTO cust_details VALUES('2','67890','Alice Williams','alice.williams@example.com','0987654321','5678 Oak Street','2000.00','1500.00','500.00','','098f6bcd4621d373cade4e832627b4f6','def1234567890abcdef','abcd1234','Wednesday, Friday','2:00 PM - 4:00 PM','SUV','XYZ9876543','Mark Johnson','1122334455','2025-04-18','14:00:00','2025-04-18','2025-04-18','Admin');
INSERT INTO cust_details VALUES('3','11223','Bob Brown','bob.brown@example.com','1122334455','7890 Pine Street','1200.00','1200.00','0.00','cash','a87ff679a2f3e71d9181a67b7542122c','7890abcdef1234567890','efgh5678','Monday, Friday','9:00 AM - 11:00 AM','Truck','MNO1357924','Lisa Green','2233445566','2025-04-18','09:15:00','2025-04-18','2025-04-18','Admin');


CREATE TABLE `customer_attendance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cust_id` int(11) NOT NULL,
  `cust_uid` varchar(256) NOT NULL,
  `emp_uid` varchar(256) NOT NULL,
  `cust_name` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `attendance_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `time_in` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_out` timestamp NULL DEFAULT NULL,
  `vehicle_name` varchar(30) NOT NULL,
  `trainer_name` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_customer_date` (`cust_id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `customer_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cust_uid` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cust_uid` (`cust_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `is_ex_employee` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `emp_uid` (`emp_uid`),
  UNIQUE KEY `aadhar` (`aadhar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `pre_book_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` varchar(256) NOT NULL,
  `timeslot` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `vehicle` varchar(256) NOT NULL,
  `trainer` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(256) NOT NULL DEFAULT 'empty',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','bank') NOT NULL DEFAULT 'cash',
  `transaction_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('income','expense') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`transaction_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `users_db` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_uid` varchar(256) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permissions` enum('user','admin','staff','trainer') NOT NULL DEFAULT 'user',
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users_db VALUES('15',NULL,'Ayush','ayushx309','66049c07d9e8546699fe0872fd32d8f6','admin','2024-12-04 14:41:06');
INSERT INTO users_db VALUES('17','DR2Y57','vinod bhai Chauhan ','vinod','ab49b208848abe14418090d95df0d590','trainer','2024-12-05 09:22:56');
INSERT INTO users_db VALUES('16','3JAV19','Hemal patel','hemal','494495f02ae076611eff45c99801355e','admin','2024-12-05 08:58:12');
INSERT INTO users_db VALUES('20','RWS1BK','sandipkumar','sandipkumar ','a1d2e644cf5204646a9e4be041fc53a3','trainer','2025-03-01 09:49:29');
INSERT INTO users_db VALUES('23','','bobby','abc','123','trainer','2025-03-06 16:15:06');
INSERT INTO users_db VALUES('24',NULL,'jaydeep','jaydeep','admin','admin','2025-04-16 17:44:09');


CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(256) NOT NULL,
  `vehicle_name` varchar(256) NOT NULL,
  `data_base_table` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `visit_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `visitlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `user_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

