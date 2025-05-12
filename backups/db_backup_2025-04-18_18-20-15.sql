

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

INSERT INTO cust_details VALUES('2','CUST002','Karan Malhotra','karan.malhotra@example.com','9123409876','13 JP Nagar, Bengaluru','18000','18000','0','cash','f03d081b0f79f6230d3d7d6ee0f47544','f03d081b0f79f6230d3d7d6ee0f47544','f0c1b6a04dd68e1e4c9860693e0d24a3','Tue-Thu','2:00PM-4PM','Hero Splendor','No','Aarav Sharma','9876543210','2024-04-16','02:00:00','2024-04-16','2024-04-16','FormFiller2');
INSERT INTO cust_details VALUES('3','CUST003','Simran Kaur','simran.kaur@example.com','9988741234','56 Sector 18, Chandigarh','12000','6000','6000','','dc51d60418e46e7762f5ebde7b81b2f0','dc51d60418e46e7762f5ebde7b81b2f0','f65b44b4ea7b4d2c9645ed70edc4e0e7','Mon-Fri','5:00PM-7PM','Bajaj Pulsar','Yes','Priya Verma','9123456789','2024-04-17','05:00:00','2024-04-17','2024-04-17','FormFiller3');
INSERT INTO cust_details VALUES('4','CUST004','Arjun Das','arjun.das@example.com','9012765432','88 Ballygunge, Kolkata','14000','10000','4000','','89dce6a446a69d6b9bdc01ac75251e4c','89dce6a446a69d6b9bdc01ac75251e4c','a761f39a4d5000c0c212bd27a0024f7e','Sat-Sun','8:00AM-10AM','TVS Jupiter','No','Rohan Iyer','9988776655','2024-04-18','08:00:00','2024-04-18','2024-04-18','FormFiller4');
INSERT INTO cust_details VALUES('1','CUST001','Riya Sen','riya.sen@example.com','9876501234','21 New Town, Kolkata','15000','10000','5000','','32250170a0dca92d53ec9624f336ca24','32250170a0dca92d53ec9624f336ca24','81dc9bdb52d04dc20036dbd8313ed055','Mon-Wed-Fri','9:00AM-11AM','Honda Activa','Yes','Sneha Patil','9012345678','2024-04-15','09:00:00','2024-04-15','2024-04-15','FormFiller1');
INSERT INTO cust_details VALUES('5','CUST005','Neha Nambiar','neha.nambiar@example.com','9765123456','99 Koregaon Park, Pune','16000','16000','0','','09b09e087d7d7c2a703bae0c6bdbeb50','09b09e087d7d7c2a703bae0c6bdbeb50','d404559f602eab6fd6020f511149baf3','Wed-Fri','1:00PM-3PM','Suzuki Access','Yes','Aditya Mehra','9765432109','2024-04-19','01:00:00','2024-04-19','2024-04-19','FormFiller5');


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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO employees VALUES('1','EMP001','QR001','Aarav Sharma','9876543210','aarav.sharma@example.com','123412341234','1995-08-15','male','','2021-06-01',NULL,NULL,'123 MG Road, Bengaluru','photo1.jpg','jpg','aadhaar1.jpg','jpg','2021-06-01 10:00:00','2024-04-10 12:00:00','0');
INSERT INTO employees VALUES('2','EMP002','QR002','Priya Verma','9123456789','priya.verma@example.com','234523452345','1992-03-25','female','','2020-01-15',NULL,NULL,'45 Park Street, Kolkata','photo2.png','png','aadhaar2.png','png','2020-01-15 09:30:00','2024-04-12 15:20:00','0');
INSERT INTO employees VALUES('3','EMP003','QR003','Rohan Iyer','9988776655','rohan.iyer@example.com','345634563456','1990-12-10','male','','2019-09-20',NULL,NULL,'88 Anna Salai, Chennai','photo3.jpg','jpg','aadhaar3.jpg','jpg','2019-09-20 11:00:00','2024-03-25 10:00:00','0');
INSERT INTO employees VALUES('4','EMP004','QR004','Sneha Patil','9012345678','sneha.patil@example.com','456745674567','1998-05-18','female','','2023-04-10',NULL,NULL,'22 FC Road, Pune','photo4.jpeg','jpeg','aadhaar4.jpeg','jpeg','2023-04-10 08:45:00','2024-04-01 09:00:00','0');
INSERT INTO employees VALUES('5','EMP005','QR005','Aditya Mehra','9765432109','aditya.mehra@example.com','567856785678','1988-11-05','male','','2018-02-01','2022-06-01','2022-05-30','67 Banjara Hills, Hyderabad','photo5.png','png','aadhaar5.png','png','2018-02-01 09:00:00','2022-05-30 18:00:00','1');


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

