

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



CREATE TABLE `car_1491` (
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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO car_1491 VALUES('1','7:00am to 7:45am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('2','7:45am to 8:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('3','8:30am to 9:15am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('4','9:15am to 10:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('5','10:00am to 10:45am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('6','10:45am to 11:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('7','11:30am to 12:15pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('8','12:15pm to 1:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('9','1:00pm to 1:45pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('10','1:45pm to 2:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('11','2:30pm to 3:15pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('12','3:15pm to 4:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('13','4:00pm to 4:45pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('14','4:45pm to 5:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('15','5:30pm to 6:15pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('16','6:15pm to 7:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('17','7:00pm to 7:45pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_1491 VALUES('18','7:45pm to 8:30pm','','','','','0000-00-00','0000-00-00','empty');


CREATE TABLE `car_3950` (
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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO car_3950 VALUES('1','7:00am to 7:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('2','7:30am to 8:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('3','8:00am to 8:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('4','8:30am to 9:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('5','9:00am to 9:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('6','9:30am to 10:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('7','10:00am to 10:30am','raju rastogi','5462388123','Four Wheeler Swift/ 5-7Km ','mahendra rajput','2025-04-02','2025-04-18','active');
INSERT INTO car_3950 VALUES('8','10:30am to 11:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('9','11:00am to 11:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('10','11:30am to 12:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('11','12:00pm to 12:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('12','12:30pm to 1:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('13','1:00pm to 1:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('14','1:30pm to 2:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('15','2:00pm to 2:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('16','2:30pm to 3:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('17','3:00pm to 3:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('18','3:30pm to 4:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('19','4:00pm to 4:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('20','4:30pm to 5:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('21','5:00pm to 5:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('22','5:30pm to 6:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('23','6:00pm to 6:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('24','6:30pm to 7:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('25','7:00pm to 7:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_3950 VALUES('26','7:30pm to 8:00pm','','','','','0000-00-00','0000-00-00','empty');


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



CREATE TABLE `car_7688` (
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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO car_7688 VALUES('1','7:00am to 7:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('2','7:30am to 8:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('3','8:00am to 8:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('4','8:30am to 9:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('5','9:00am to 9:30am','kaushal parmar','6353149071','Four Wheeler Creta/ 5-7Km ','kavit patel','2025-04-01','2025-04-17','active');
INSERT INTO car_7688 VALUES('6','9:30am to 10:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('7','10:00am to 10:30am','chirag bhai','9994443332','Four Wheeler Creta/ 9-10Km ','rajesh sinde','2025-04-02','2025-04-24','active');
INSERT INTO car_7688 VALUES('8','10:30am to 11:00am','japan parekh','0006665553','Four Wheeler Creta/ 9-10Km ','rajesh sinde','2025-04-02','2025-04-24','active');
INSERT INTO car_7688 VALUES('9','11:00am to 11:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('10','11:30am to 12:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('11','12:00pm to 12:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('12','12:30pm to 1:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('13','1:00pm to 1:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('14','1:30pm to 2:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('15','2:00pm to 2:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('16','2:30pm to 3:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('17','3:00pm to 3:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('18','3:30pm to 4:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('19','4:00pm to 4:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('20','4:30pm to 5:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('21','5:00pm to 5:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('22','5:30pm to 6:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('23','6:00pm to 6:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('24','6:30pm to 7:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('25','7:00pm to 7:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_7688 VALUES('26','7:30pm to 8:00pm','','','','','0000-00-00','0000-00-00','empty');


CREATE TABLE `car_8554` (
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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO car_8554 VALUES('1','7:00am to 7:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('2','7:30am to 8:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('3','8:00am to 8:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('4','8:30am to 9:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('5','9:00am to 9:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('6','9:30am to 10:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('7','10:00am to 10:30am','chirag santoki','2225554441','Four Wheeler Verna/ 9-10Km ','mahendra rajput','2025-04-02','2025-04-24','active');
INSERT INTO car_8554 VALUES('8','10:30am to 11:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('9','11:00am to 11:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('10','11:30am to 12:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('11','12:00pm to 12:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('12','12:30pm to 1:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('13','1:00pm to 1:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('14','1:30pm to 2:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('15','2:00pm to 2:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('16','2:30pm to 3:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('17','3:00pm to 3:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('18','3:30pm to 4:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('19','4:00pm to 4:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('20','4:30pm to 5:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('21','5:00pm to 5:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('22','5:30pm to 6:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('23','6:00pm to 6:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('24','6:30pm to 7:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('25','7:00pm to 7:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8554 VALUES('26','7:30pm to 8:00pm','','','','','0000-00-00','0000-00-00','empty');


CREATE TABLE `car_8855` (
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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO car_8855 VALUES('1','7:00am to 7:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('2','7:30am to 8:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('3','8:00am to 8:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('4','8:30am to 9:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('5','9:00am to 9:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('6','9:30am to 10:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('7','10:00am to 10:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('8','10:30am to 11:00am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('9','11:00am to 11:30am','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('10','11:30am to 12:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('11','12:00pm to 12:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('12','12:30pm to 1:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('13','1:00pm to 1:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('14','1:30pm to 2:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('15','2:00pm to 2:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('16','2:30pm to 3:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('17','3:00pm to 3:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('18','3:30pm to 4:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('19','4:00pm to 4:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('20','4:30pm to 5:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('21','5:00pm to 5:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('22','5:30pm to 6:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('23','6:00pm to 6:30pm','Bharat bhai Parmar','9662012186','Four Wheeler WagonR/ 5-7Km ','rajesh sinde','2025-04-01','2025-04-17','active');
INSERT INTO car_8855 VALUES('24','6:30pm to 7:00pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('25','7:00pm to 7:30pm','','','','','0000-00-00','0000-00-00','empty');
INSERT INTO car_8855 VALUES('26','7:30pm to 8:00pm','','','','','0000-00-00','0000-00-00','empty');


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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO categories VALUES('1',NULL,'Salary','income','1');
INSERT INTO categories VALUES('2',NULL,'Freelance','income','1');
INSERT INTO categories VALUES('3',NULL,'Investments','income','1');
INSERT INTO categories VALUES('4',NULL,'Food','expense','1');
INSERT INTO categories VALUES('5',NULL,'Transport','expense','1');
INSERT INTO categories VALUES('6',NULL,'Utilities','expense','1');
INSERT INTO categories VALUES('7',NULL,'Other','expense','1');


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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO cust_details VALUES('1','2SZOWE','kaushal parmar','kaushalparmar9898@gmail.com','6353149071','Maninagar East','5000','2500','2500','cash','15','9:00am to 9:30am','Four Wheeler Creta/ 5-7Km','Not Applied','kaushal parmar','6353149071','2025-03-31','08:03:24','2025-04-17','2025-04-01','Ayush');
INSERT INTO cust_details VALUES('2','6YP2W7','Bharat bhai Parmar','kaushalparmar9898@gmail.com','9662012186','2,navdurga society hatkeshwar ahmedabad-08','2500','1500','1000','cash','15','6:00pm to 6:30pm','Four Wheeler WagonR/ 5-7Km','Not Applied','rajesh sinde','6745193732','2025-04-01','11:50:41','2025-04-17','2025-04-01','kaushal parmar');
INSERT INTO cust_details VALUES('3','2KDPVM','raju rastogi','rajurastogi777@gmail.com','5462388123','786, lakshmi narayan society, bal bhavan, maninagar, ahmedabad ,  ','3500','2000','1500','cash','15','10:00am to 10:30am','Four Wheeler Swift/ 5-7Km','Not Applied','mahendra rajput','9834587356','2025-04-02','07:34:31','2025-04-18','2025-04-02','kaushal parmar');
INSERT INTO cust_details VALUES('4','LPEFNX','vishal doshi','vishald123@gmail.com','8887776665','gls university,opp law garden','5000','5000','0','cash','20','9:00am to 9:30am','Four Wheeler Creta/ Km','Applied','rajesh sinde','6745193732','2025-04-02','07:39:07','0025-05-17','0025-04-25','kaushal parmar');
INSERT INTO cust_details VALUES('5','XZRY7D','chirag bhai','santokihappy90@gmail;.com','9994443332','morbi','5000','5000','0','cash','20','10:00am to 10:30am','Four Wheeler Creta/ 9-10Km','Not Applied','rajesh sinde','6745193732','2025-04-02','11:21:40','2025-04-24','2025-04-02','kaushal parmar');
INSERT INTO cust_details VALUES('6','2V605W','chirag santoki','happysantoki90@gmail.com','2225554441','morbi','4000','4000','0','cash','20','10:00am to 10:30am','Four Wheeler Verna/ 9-10Km','Not Applied','mahendra rajput','9834587356','2025-04-02','11:22:57','2025-04-24','2025-04-02','kaushal parmar');
INSERT INTO cust_details VALUES('7','ZG3SXV','japan parekh','japansocialhandles@gmail.com','0006665553','ghodasar','5000','5000','0','cash','20','10:30am to 11:00am','Four Wheeler Creta/ 9-10Km','Not Applied','rajesh sinde','6745193732','2025-04-02','11:29:02','2025-04-24','2025-04-02','kaushal parmar');


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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO customer_attendance VALUES('1','1','2SZOWE','XOTQPR','kaushal parmar','2025-03-31','2025-04-01 02:19:29','2025-04-01 02:19:29','2025-04-01 02:19:29','Four Wheeler Creta/ 5-7Km','kaushal parmar','','2025-04-01 02:19:29','2025-04-01 02:19:29');


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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



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

