

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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO cust_details VALUES('1','CUST001','Jay Patel','jaypatel@example.com','9876543210','Ahmedabad, Gujarat','10000','4000','6000','','482c811da5d5b4bc6d497ffa98491e38','482c811da5d5b4bc6d497ffa98491e38','81dc9bdb52d04dc20036dbd8313ed055','Monday, Wednesday, Friday','10:00 AM - 11:00 AM','Swift','Yes','Jane Smith','9876543210','2025-05-01','10:00:00','2025-05-30','2025-05-01','Admin');
INSERT INTO cust_details VALUES('10','CUST001','John Doe','john.doe@example.com','1234567890','123 Main St, City, Country','1000.00','500.00','500.00','','b9f52a46053629c3ef01883685062068','10e3b35d5a2a01e0a2e5e2b1e2328ec8','81dc9bdb52d04dc20036dbd8313ed055','Monday, Wednesday','10:00 AM - 12:00 PM','Car','1234567890','Jane Smith','9876543210','2025-04-18','08:30:00','2025-04-18','2025-04-18','John Doe');
INSERT INTO cust_details VALUES('11','CUST002','Alice Johnson','alice.johnson@example.com','9876543210','456 Elm St, City, Country','1500.00','700.00','800.00','','e4c9fda617c8856c9a754644d7e38e6f','d4c74594d841139328b08699d53b53c0','5c7fa90f1b562aec21f2eab40f869902','Tuesday, Thursday','01:00 PM - 03:00 PM','Bike','9876543210','John Smith','1234567890','2025-04-19','09:30:00','2025-04-19','2025-04-19','Alice Johnson');
INSERT INTO cust_details VALUES('12','CUST003','Bob Brown','bob.brown@example.com','1122334455','789 Pine St, City, Country','2000.00','1200.00','800.00','cash','0e37ac4ed9b30c2ff0cf2316df5de87f','dbf740bba78dce12280d9e499520907d','78b391d74501c3f6e0fdf0f5e5d2c16c','Monday, Friday','04:00 PM - 06:00 PM','Truck','1122334455','Mary Johnson','6677889900','2025-04-20','10:00:00','2025-04-20','2025-04-20','Bob Brown');
INSERT INTO cust_details VALUES('13','CUST004','Charlie White','charlie.white@example.com','9988776655','321 Oak St, City, Country','1800.00','800.00','1000.00','','7fc69c54f6cb9f8df04705f995702d30','49e42e135f65d9d6f5bf86de1c67a2db','92dc1c62e7f9b4f8321b12d6ac5e5fc0','Tuesday, Saturday','02:00 PM - 04:00 PM','Bus','9988776655','Peter Clark','5566778899','2025-04-21','11:00:00','2025-04-21','2025-04-21','Charlie White');


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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO employees VALUES('6','EMP001','EMP001QR','Jane Smith','9876543210','jane.smith@example.com','111122223333','1990-01-01','female','trainer','2023-01-10',NULL,NULL,'123 Main St, City, Country',NULL,NULL,NULL,NULL,'2025-04-18 22:50:53','2025-04-18 22:50:53','0');
INSERT INTO employees VALUES('7','EMP002','EMP002QR','John Smith','1234567890','john.smith@example.com','444455556666','1985-05-15','male','trainer','2023-02-20',NULL,NULL,'456 Elm St, City, Country',NULL,NULL,NULL,NULL,'2025-04-18 22:50:53','2025-04-18 22:50:53','0');
INSERT INTO employees VALUES('8','EMP003','EMP003QR','Mary Johnson','6677889900','mary.johnson@example.com','777788889999','1992-03-22','female','trainer','2023-03-05',NULL,NULL,'789 Pine St, City, Country',NULL,NULL,NULL,NULL,'2025-04-18 22:50:53','2025-04-18 22:50:53','0');
INSERT INTO employees VALUES('9','EMP004','EMP004QR','Peter Clark','5566778899','peter.clark@example.com','999900001111','1988-08-08','male','trainer','2023-04-18',NULL,NULL,'321 Oak St, City, Country',NULL,NULL,NULL,NULL,'2025-04-18 22:50:53','2025-04-18 22:50:53','0');
INSERT INTO employees VALUES('10','CMR1LY','iVBORw0KGgoAAAANSUhEUgAAASwAAAEsAQMAAABDsxw2AAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAACI0lEQVRoge2avXHDMAyF4XOhUiNoFI1mj6ZROIJKFzohxB9pOSlsyOkemtDS5xQA7uGBCdF7cWOLepzWYbsw32f5vBPfafKXBVgWWyTJF66HabWE18MQX7QnwPJYTXVNvn4eNu3nQoMmP3hg57Ha7atgtbeDB/Y9jOZCdHXJkBANAXYaCw3hMrI1OfPjqCrA0hhbWEtvl+PBXwLLYj3mmvwrc5foO/0VwD7BtApi22qux0d/Hk+uauSAZbGF9K0JskzA6iu0HOKTV+MLsCRWB9/4JMhu5GQU6s/B1hBgWUxdxK5tHNJReRMTNq0mYGnMlVlburviLSyH/AZgeYzIbdtive3SIaoSBzMYwBLYYQLy1paOXZfoUG9gSUxCe1vKYd0eGmIFCqkBlsHUYOjKLL5CW1pbX2ciRV2AZTHZNcy2NTsxcVwL13LsriHAMphMQInm36wKbjA4tg9gOaxtHyxuzexEVGHu/g1YFoulwx82I8dPMxHYKUxiflLmtk03qQGWxew+TcVEjdxxiQ4NAZbEVDpUp12Q2xfL+HAjByyN+QTkuAKylS8WEzkUYEmshd782Boy+d9AF3N0wPKYplqrsAjRJqCLic1EApbF/C3FBaavIRJ+iBoBy2CtpSPncxnjHySOUgPsDObluPkdRb8WBvYNbOJ2b2muuK57cnhtcmCfYC/J7xpSu/231AD7DGMLa2mtgm95wh8v54H9F/Ze/AA4gG/gyRepAQAAAABJRU5ErkJggg==','jaydeep','9988776655','welet42273@konetas.com','','2025-04-16','male','trainer','2025-04-28',NULL,NULL,'asdasdasd',NULL,NULL,NULL,NULL,'2025-04-28 19:04:38','2025-04-28 19:04:38','0');


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

