-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 04, 2017 at 02:46 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Pharma`
--

-- --------------------------------------------------------

--
-- Table structure for table `additional_sale`
--

CREATE TABLE IF NOT EXISTS `additional_sale` (
  `_id` int(11) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `doc_id` varchar(100) NOT NULL,
  `chem_id` varchar(100) NOT NULL,
  `mr_id` varchar(100) NOT NULL,
  `sale_unit` double DEFAULT NULL,
  `sale_value` double NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `additional_sale`
--

INSERT INTO `additional_sale` (`_id`, `product_id`, `doc_id`, `chem_id`, `mr_id`, `sale_unit`, `sale_value`, `date`) VALUES
(2, '5LI2S54DJKL', 'C6RWESGQMJ', 'WSBRBSZPRA', 'MR1_INDORE', 5, 150, '01/19/2017 17:15:28');

-- --------------------------------------------------------

--
-- Table structure for table `Chemist_profile`
--

CREATE TABLE IF NOT EXISTS `Chemist_profile` (
  `chem_id` varchar(100) NOT NULL,
  `name` text NOT NULL,
  `contact_person` text,
  `phone` text NOT NULL,
  `email` text,
  `profile_pic` text,
  `DOB` text,
  `anniversary` text,
  `MR_core` tinyint(1) NOT NULL,
  `geotag` text,
  `station_name` text,
  `doctor_relation` text,
  `probable_stocklist` text,
  `shipping_address` text,
  `active_MR` varchar(100) DEFAULT NULL,
  `inactive_date` text,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Chemist_profile`
--

INSERT INTO `Chemist_profile` (`chem_id`, `name`, `contact_person`, `phone`, `email`, `profile_pic`, `DOB`, `anniversary`, `MR_core`, `geotag`, `station_name`, `doctor_relation`, `probable_stocklist`, `shipping_address`, `active_MR`, `inactive_date`, `active`) VALUES
('WSBRBSZPRA', 'Ramu Pharmas', 'Ramu', '7872705997', 'sahilvs000@gmail.com', '/Pharma/uploads/Chemist/download.png', '1996-10-30', '2001-1-30', 1, '{"lat":22.322362,"lon":87.305886}', 'MADARA', 'owned', 'ASUS', 'delhi-16', 'MR1_INDORE', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `chemist_visit`
--

CREATE TABLE IF NOT EXISTS `chemist_visit` (
  `_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL DEFAULT '0',
  `sale_achieved_unit` int(11) NOT NULL DEFAULT '0',
  `sale_achieved_value` double NOT NULL DEFAULT '0',
  `date` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chemist_visit`
--

INSERT INTO `chemist_visit` (`_id`, `p_id`, `sale_achieved_unit`, `sale_achieved_value`, `date`) VALUES
(3, 1, 10, 100, '2/17/2017 14:25:59'),
(4, 1, 15, 150, '2/28/2017 12:15:50');

-- --------------------------------------------------------

--
-- Table structure for table `chem_gifts`
--

CREATE TABLE IF NOT EXISTS `chem_gifts` (
  `_id` int(11) NOT NULL,
  `mr_id` varchar(100) NOT NULL,
  `chem_id` varchar(100) NOT NULL,
  `gift_id` varchar(100) NOT NULL,
  `quantity` decimal(10,0) NOT NULL DEFAULT '1',
  `date` text NOT NULL,
  `value` decimal(10,0) NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Doctor_Chemist`
--

CREATE TABLE IF NOT EXISTS `Doctor_Chemist` (
  `sno` int(11) NOT NULL,
  `doc_id` varchar(100) NOT NULL,
  `ass_chem_id_1` varchar(100) DEFAULT NULL,
  `ass_chem_id_2` varchar(100) DEFAULT NULL,
  `ass_chem_id_3` varchar(100) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Doctor_Chemist`
--

INSERT INTO `Doctor_Chemist` (`sno`, `doc_id`, `ass_chem_id_1`, `ass_chem_id_2`, `ass_chem_id_3`) VALUES
(1, 'C6RWESGQMJ', 'WSBRBSZPRA', NULL, NULL),
(2, 'LIQLDLCAU2', 'WSBRBSZPRA', NULL, NULL),
(6, 'EBHJ5NSS8B', 'WSBRBSZPRA', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_gifts`
--

CREATE TABLE IF NOT EXISTS `doctor_gifts` (
  `_id` int(11) NOT NULL,
  `mr_id` varchar(100) NOT NULL,
  `doc_id` varchar(100) NOT NULL,
  `gift_id` varchar(100) NOT NULL,
  `quantity` decimal(10,0) NOT NULL DEFAULT '1',
  `date` text NOT NULL,
  `value` decimal(10,0) NOT NULL DEFAULT '0',
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Doctor_profile`
--

CREATE TABLE IF NOT EXISTS `Doctor_profile` (
  `doc_id` varchar(100) NOT NULL,
  `set_no` int(11) NOT NULL,
  `station_name` varchar(100) DEFAULT '',
  `name` text NOT NULL,
  `email` varchar(100) DEFAULT '',
  `sex` varchar(100) DEFAULT '',
  `phone` varchar(100) NOT NULL DEFAULT '',
  `office_phone` varchar(100) DEFAULT '',
  `assistant_phone` varchar(100) DEFAULT '',
  `geotag` text,
  `DOB` varchar(100) DEFAULT '',
  `anniversary` varchar(100) DEFAULT '',
  `qualification` varchar(100) DEFAULT '',
  `specialization` varchar(100) DEFAULT '',
  `pat_freq` int(11) DEFAULT '0',
  `monthly_business` int(11) DEFAULT '0',
  `visit_freq` int(11) DEFAULT '0',
  `visit_day` text NOT NULL,
  `class` varchar(100) DEFAULT '',
  `profile_pic` varchar(500) DEFAULT '',
  `pad_image` varchar(500) DEFAULT '',
  `clinic_image` varchar(500) DEFAULT '',
  `MR_core` tinyint(1) NOT NULL DEFAULT '0',
  `AM_core` tinyint(1) NOT NULL DEFAULT '0',
  `RM_core` tinyint(1) NOT NULL DEFAULT '0',
  `inactive_date` varchar(100) DEFAULT '',
  `meeting_time` text NOT NULL,
  `active_MR` varchar(100) DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Doctor_profile`
--

INSERT INTO `Doctor_profile` (`doc_id`, `set_no`, `station_name`, `name`, `email`, `sex`, `phone`, `office_phone`, `assistant_phone`, `geotag`, `DOB`, `anniversary`, `qualification`, `specialization`, `pat_freq`, `monthly_business`, `visit_freq`, `visit_day`, `class`, `profile_pic`, `pad_image`, `clinic_image`, `MR_core`, `AM_core`, `RM_core`, `inactive_date`, `meeting_time`, `active_MR`, `active`) VALUES
('C6RWESGQMJ', 2, 'Burari', 'Anukul Jha', 'anukuljha@gmail.com', 'male', '9560313547', '7251741324', '9564484522', '{"loc_1":{"lat":22.428362,"lon":87.006886,"address":"Gali no-15, Sant Nagar,Burari,Delhi-16"},"loc_2":{"lat":22.423462,"lon":87.055886,"address":"Gali no-22, Sant Nagar,Burari,Delhi-16"}}', '1996-11-30', '1996-11-20', 'MBBS', 'Neurologist', 40, 200000, 6, 'Saturday', 'B', '/Pharma/uploads/Doctor/Screenshot from 2015-12-09 11:56:1212.png', '/Pharma/uploads/Doctor/Screenshot from 2016-01-01 04:05:5112.png', '', 0, 1, 1, 'Kuch toh h', '13:00:00', 'MR1_INDORE', 1),
('EBHJ5NSS8B', 3, 'INDORE', 'Dr. Rahul Yadav', 'sahilvs000@gmail.com', 'male', '8826879295', '', '', '{"loc1":{"address":"sdda","lat":85.54,"lon":57.54}}', '30/11/1996', '25/02/1995', 'MBBS', 'Heart Surgeon', 50, 65000, 20, 'Monday', 'B', '', '', '', 1, 0, 1, '', '10:00:00', 'MR2_INDORE', 1),
('LIQLDLCAU2', 1, 'Sant Nagar', 'Sahil Chaddha', 'sahilvs000@gmail.com', 'male', '7872705997', '8575242545', '7534314419', '{"loc_1":{"lat":22.422362,"lon":87.005886,"address":"Gali no-15, Sant Nagar,Burari,Delhi-84"},"loc_2":{"lat":22.423362,"lon":87.025886,"address":"Gali no-22, Sant Nagar,Burari,Delhi-84"}}', '1996-11-25', '1996-11-20', 'PhD', 'Dentist', 60, 125000, 5, 'Monday', 'A', '/Pharma/uploads/Doctor/facebook1.png', '/Pharma/uploads/Doctor/Screenshot from 2016-01-01 04:07:183.png', '', 1, 0, 1, 'Kuch toh h', '16:00:00', 'MR1_INDORE', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_visit`
--

CREATE TABLE IF NOT EXISTS `doctor_visit` (
  `_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `sample_unit_given` int(11) NOT NULL DEFAULT '0',
  `sample_given_value` double DEFAULT NULL,
  `date` text NOT NULL,
  `type` varchar(100) NOT NULL DEFAULT 'scheduled'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor_visit`
--

INSERT INTO `doctor_visit` (`_id`, `p_id`, `sample_unit_given`, `sample_given_value`, `date`, `type`) VALUES
(1, 3, 2, 50, '01/15/2017 10:15:45', 'scheduled'),
(2, 4, 2, 100, '01/15/2017 10:15:30', 'scheduled'),
(3, 3, 3, 150, '01/15/2017 12:00:15', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `Employee_profile`
--

CREATE TABLE IF NOT EXISTS `Employee_profile` (
  `user_id` varchar(100) NOT NULL,
  `HQ` text NOT NULL,
  `role` text NOT NULL,
  `person_id` varchar(100) DEFAULT NULL,
  `head_role` text NOT NULL,
  `head_id` varchar(100) NOT NULL,
  `inactive_date` text,
  `create_date` text,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Employee_profile`
--

INSERT INTO `Employee_profile` (`user_id`, `HQ`, `role`, `person_id`, `head_role`, `head_id`, `inactive_date`, `create_date`, `active`) VALUES
('ADMIN1', 'Gwalior', 'Admin', 'FG455AD84A', 'DUMMY', 'DUMMY', NULL, NULL, 1),
('ADMIN2', 'Ranchi', 'Admin', 'YDXJFZXBZ9', 'DUMMY', 'DUMMY', '01/21/2017 15:00:34', NULL, 1),
('ADMIN3', 'RANCHI', 'Admin', NULL, 'DUMMY', 'DUMMY', NULL, NULL, 0),
('AM1_GWALIOR', 'GWALIOR', 'AM', NULL, 'RM', 'RM2_INDORE', NULL, NULL, 0),
('DUMMY', 'DUMMY', 'DUMMY', NULL, 'DUMMY', 'DUMMY', '01/30/2017 16:00:00', '01/30/2017 16:00:00', 0),
('MR1_INDORE', 'Ranchi', 'MR', 'KD5IKOU8LM', 'TM', 'TM2', NULL, NULL, 1),
('MR2_INDORE', 'INDORE', 'MR', NULL, 'TM', 'TM2', NULL, '01/30/2017 16:11:00', 0),
('MSD1', 'GWALIOR', 'MSD', '845AD54HAJ', 'DUMMY', 'DUMMY', NULL, NULL, 1),
('MSD2', 'RANCHI', 'MSD', 'ADSDH784SD', 'DUMMY', 'DUMMY', NULL, NULL, 1),
('RM1_INDORE', 'INDORE', 'RM', 'DFS845SDF5', 'ZM', 'ZM1_INDORE', NULL, NULL, 1),
('RM2_INDORE', 'INDORE', 'RM', NULL, 'ZM', 'ZM1_INDORE', NULL, NULL, 0),
('TM2', 'INDORE', 'TM', NULL, 'AM', 'AM1_GWALIOR', '01/23/2017 15:42:21', '01/23/2017 15:42:21', 0),
('ZM1_INDORE', 'INDORE', 'ZM', 'ASFJ545ASD', 'MSD', 'MSD1', NULL, NULL, 1),
('ZM2_INDORE', 'INDORE', 'ZM', 'MKM78SAD5A', 'MSD', 'MSD2', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Gifts`
--

CREATE TABLE IF NOT EXISTS `Gifts` (
  `gift_id` varchar(100) NOT NULL,
  `gift_name` text NOT NULL,
  `description` text,
  `in_practice` tinyint(1) NOT NULL DEFAULT '1',
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Gifts`
--

INSERT INTO `Gifts` (`gift_id`, `gift_name`, `description`, `in_practice`, `price`) VALUES
('45SAOS5D1D', 'wallet', 'paise rkhio isme', 1, 200),
('AS5FMF2DAS', 'watch', 'hand watch', 1, 200),
('ER5PHXGR7W', 'Iphone', 'kidney bech de', 1, 4000);

-- --------------------------------------------------------

--
-- Table structure for table `Head_History`
--

CREATE TABLE IF NOT EXISTS `Head_History` (
  `sno` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `head_id` varchar(100) DEFAULT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Head_History`
--

INSERT INTO `Head_History` (`sno`, `user_id`, `head_id`, `date`) VALUES
(5, 'ADMIN1', NULL, '01/16/17 01:06:26'),
(6, 'ADMIN2', NULL, '01/16/17 01:52:31'),
(7, 'MSD1', NULL, '01/17/2016 9:08:15'),
(8, 'MSD2', NULL, '01/17/2016 9:09:15'),
(9, 'RM1_INDORE', 'ZM1_INDORE', '01/17/2016 9:10:15'),
(10, 'ZM1_INDORE', 'MSD1', '01/17/2016 9:06:15'),
(11, 'ZM2_INDORE', NULL, '01/17/2017 9:42:15'),
(12, 'ADMIN3', NULL, '01/20/2017 06:41:29'),
(13, 'AM1_GWALIOR', NULL, '01/21/2017 15:10:56'),
(14, 'AM1_GWALIOR', 'RM1_INDORE', '01/21/2017 15:25:40'),
(15, 'AM1_GWALIOR', 'RM2_INDORE', '01/21/2017 15:33:01'),
(16, 'RM2_INDORE', 'ZM1_INDORE', '01/21/2017 18:40:48'),
(17, 'TM2', NULL, '01/23/2017 15:42:21'),
(29, 'TM2', NULL, '01/25/2017 03:13:54'),
(30, 'MR1_INDORE', 'TM2', '01/29/2017 20:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `MR_History`
--

CREATE TABLE IF NOT EXISTS `MR_History` (
  `sno` int(11) NOT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `child_id` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `MR_History`
--

INSERT INTO `MR_History` (`sno`, `user_id`, `child_id`, `role`, `date`) VALUES
(1, 'MR1_INDORE', 'WSBRBSZPRA', 'Chemist', '12/01/2016 10:35 pm'),
(2, 'MR1_INDORE', 'C6RWESGQMJ', 'Doctor', '12/02/2016 1:35 pm'),
(3, 'MR1_INDORE', 'LIQLDLCAU2', 'Doctor', '11/29/2016 12:01 am'),
(4, 'MR1_INDORE', 'EBHJ5NSS8B', 'Doctor', '01/23/2017 12:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `Person_History`
--

CREATE TABLE IF NOT EXISTS `Person_History` (
  `sno` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `person_id` varchar(100) DEFAULT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Person_History`
--

INSERT INTO `Person_History` (`sno`, `user_id`, `person_id`, `date`) VALUES
(6, 'ADMIN1', 'FG455AD84A', '01/16/17 01:06:26'),
(7, 'ADMIN2', 'YDXJFZXBZ9', '01/16/17 01:52:31'),
(8, 'ADMIN2', 'ASFJ545ASD', '01/16/17 02:10:00'),
(9, 'ADMIN2', 'YDXJFZXBZ9', '01/16/17 02:12:59'),
(10, 'ADMIN2', NULL, '01/16/17 02:13:24'),
(11, 'ADMIN2', 'YDXJFZXBZ9', '01/16/17 02:13:51'),
(12, 'ADMIN2', 'ASFJ545ASD', '01/17/17 12:42:44'),
(13, 'ADMIN2', NULL, '01/17/17 12:46:14'),
(18, 'MSD1', '845AD54HAJ', '01/17/2016 9:08:15'),
(19, 'MSD2', 'ADSDH784SD', '01/17/2016 9:09:15'),
(20, 'RM1_INDORE', 'DFS845SDF5', '01/17/2016 9:10:15'),
(21, 'ZM1_INDORE', 'ASFJ545ASD', '01/17/2016 9:07:15'),
(22, 'ZM2_INDORE', 'MKM78SAD5A', '01/17/2017 9:40:35'),
(23, 'ADMIN3', NULL, '01/20/2017 06:41:29'),
(24, 'AM1_GWALIOR', NULL, '01/21/2017 15:10:56'),
(25, 'RM2_INDORE', NULL, '01/21/2017 18:41:36'),
(26, 'MR1_INDORE', 'KD5IKOU8LM', '01/21/2017 18:42:57'),
(27, 'TM2', NULL, '01/23/2017 15:42:21'),
(39, 'TM2', NULL, '01/25/2017 03:13:54'),
(40, 'MR1_INDORE', NULL, '01/29/2017 20:16:51'),
(41, 'MR1_INDORE', 'KD5IKOU8LM', '01/29/2017 20:17:11');

-- --------------------------------------------------------

--
-- Table structure for table `Person_profiles`
--

CREATE TABLE IF NOT EXISTS `Person_profiles` (
  `person_id` varchar(100) NOT NULL,
  `employe_id` text,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `password` text NOT NULL,
  `DOB` text NOT NULL,
  `profile_pic` text,
  `sex` text NOT NULL,
  `last_credential_update_time` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Person_profiles`
--

INSERT INTO `Person_profiles` (`person_id`, `employe_id`, `name`, `email`, `phone`, `password`, `DOB`, `profile_pic`, `sex`, `last_credential_update_time`) VALUES
('845AD54HAJ', NULL, 'Anukul', 'anukul@gmail.com', '9015222678', '4ff2023fe39ab7b5b654b76ccd42b2e2', '25-5-1996', NULL, 'male', 1483793262.3689),
('ADSDH784SD', NULL, 'Sahil', 'sahilvs000@gmail.com', '8826879295', 'd96a64dabe0f5ad5ca8ddeabc79cdeec', '30-11-1996', NULL, 'male', 1483793346.5409),
('ASFJ545ASD', NULL, 'Sumeet Khirwal', 'sumeet@gmail.com', '7872705997', '4b50bbf9267a8e6d66f9c8b71380a5f6', '20-11-1996', NULL, 'male', 1484594174.5584),
('DFS845SDF5', NULL, 'Ayush', 'ayush@gmail.com', '9560313547', 'b63cf5ceab3de75edc6545af63cf88e1', '28-12-1996', NULL, 'male', 1482519525.3634),
('FG455AD84A', NULL, 'Sajal', 'sajal@gmail.com', '8574852255', '9c3647274234b151ab7d611f6e104446', '15-11-1994', NULL, 'male', 1482751231.7269),
('KD5IKOU8LM', NULL, 'Jaydip', 'jaydip@gmail.com', '8857541253', '96bf0fb174f5b4224114fd085ecd66ef', '25/11/1995', NULL, 'male', 1485701231.4238),
('MKM78SAD5A', NULL, 'Deepak Jha', 'deepak@ecell-iitkgp.org ', '7872705997', 'ddf0fe3a20992d4b7026bce7f69a6ad0', '4-1-1995', NULL, 'male', 1482519939.1979),
('YDXJFZXBZ9', NULL, 'Faizal', 'faizal@gmail.com', '9846547121', '9ca05608edcd69e1302000a6de4a13d5', '20-11-1996', NULL, 'male', 1484991054.2091);

-- --------------------------------------------------------

--
-- Table structure for table `Price_History`
--

CREATE TABLE IF NOT EXISTS `Price_History` (
  `sno` int(11) NOT NULL,
  `commo_id` varchar(100) NOT NULL,
  `role` text NOT NULL,
  `price` float NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Price_History`
--

INSERT INTO `Price_History` (`sno`, `commo_id`, `role`, `price`, `date`) VALUES
(1, '45SAOS5D1D', 'Gift', 200, '01/14/2016 8:41:00 pm'),
(2, 'AS5FMF2DAS', 'Gift', 200, '01/14/2016 5:00 pm'),
(3, '5SD2S5SDJKL', 'Product', 20, '02/14/2016 9:25:59 am'),
(4, 'KOD2S54DJKL', 'Product', 60, '02/14/2016 9:25:59 am'),
(5, 'NHD2S54DJKL', 'Product', 180, '05/07/2015 8:12:00 pm '),
(6, 'OHD2S54DJKL', 'Product', 80, '05/11/2015 8:12:00 pm ');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `product_id` varchar(100) NOT NULL,
  `product_group` varchar(100) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `pack` text,
  `price` double NOT NULL,
  `PTS` double DEFAULT NULL,
  `scheme` double DEFAULT NULL,
  `add_date` text NOT NULL,
  `remarks` text,
  `in_practice` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_group`, `product_name`, `pack`, `price`, `PTS`, `scheme`, `add_date`, `remarks`, `in_practice`) VALUES
('2TI2S54DJKL', 'AMOX Group', 'AMOX-500 Cap', '1*10', 39, 27.17, 10, '2016-10-28 21:08:09', '', 1),
('5LI2S54DJKL', 'Acenova Group', 'Acenova-P Tab', '1*10', 29, 19.89, 10, '2016-10-28 21:04:43', '', 1),
('5SD2S54DJKL', 'Acenova Group', 'Acenova-FH Tab', '1*10', 60, 41.82, 10, '2016-10-28 21:04:43', '', 1),
('5SD2S5SDJKL', 'Acenova Group', 'Acenova Tab', '1*10', 20, 13.71, 10, '2016-10-28 21:01:13', '', 1),
('5SMLS54DJKL', 'AMOX Group', 'AMOX-250 Cap', '1*10', 49.9, 34.22, 0, '2016-10-28 21:08:09', '', 1),
('D9D2S54DJKL', 'Acenova Group', 'Acenova-MR Tab', '1*10', 50, 34.85, 10, '2016-10-28 21:04:43', '', 1),
('KOD2S54DJKL', 'AMOX Group', 'AMOX-P Tab', '1*10', 72, 50.87, 10, '2016-10-28 21:08:09', 'Latest', 1),
('NHD2S54DJKL', 'AMOX Group', 'AMOX-250 DT Tab', '1*10', 31.9, 23.02, 10, '2016-10-28 21:08:09', '', 1),
('OHD2S54DJKL', 'Acenova Group', 'Acenova-SR Tab', '1*10', 44, 30.65, 10, '2016-10-28 21:04:43', '', 1),
('SLZCHE9NH5', 'AMILY', 'syrup124', '150ml', 30, 12.2, 5, '', 'Latest', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Set_data`
--

CREATE TABLE IF NOT EXISTS `Set_data` (
  `sno` int(11) NOT NULL,
  `mr_id` varchar(100) NOT NULL,
  `set_no` int(11) NOT NULL,
  `station_name` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Set_data`
--

INSERT INTO `Set_data` (`sno`, `mr_id`, `set_no`, `station_name`) VALUES
(1, 'MR1_INDORE', 1, 'INDORE'),
(2, 'MR1_INDORE', 2, 'ALLAHABAD'),
(3, 'MR1_INDORE', 3, 'JAMSEDHPUR');

-- --------------------------------------------------------

--
-- Table structure for table `Sync_Table`
--

CREATE TABLE IF NOT EXISTS `Sync_Table` (
  `_id` int(11) NOT NULL,
  `row_id` text NOT NULL,
  `table_name` text NOT NULL,
  `changed_by` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `change_type` text NOT NULL,
  `editor_role` text NOT NULL,
  `notify_scope` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `WCP_main`
--

CREATE TABLE IF NOT EXISTS `WCP_main` (
  `WCP_id` int(11) NOT NULL,
  `WCP_wrap_id` int(11) NOT NULL,
  `type` text,
  `DOT` text,
  `doc_id` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `WCP_main`
--

INSERT INTO `WCP_main` (`WCP_id`, `WCP_wrap_id`, `type`, `DOT`, `doc_id`) VALUES
(1, 1, 'Core', 'MR', 'LIQLDLCAU2'),
(2, 1, 'Core', 'MR', 'EBHJ5NSS8B'),
(9, 17, 'S Core', 'MR', 'C6RWESGQMJ'),
(10, 17, 'Core', 'RM', 'LIQLDLCAU2'),
(11, 18, 'S Core', 'RM', 'LIQLDLCAU2');

-- --------------------------------------------------------

--
-- Table structure for table `WCP_products`
--

CREATE TABLE IF NOT EXISTS `WCP_products` (
  `p_id` int(11) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `WCP_main_id` int(11) NOT NULL,
  `sample_plan_unit` int(11) NOT NULL DEFAULT '0',
  `sample_plan_value` double NOT NULL,
  `sale_plan_unit` int(11) NOT NULL DEFAULT '0',
  `sale_plan_value` double NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `WCP_products`
--

INSERT INTO `WCP_products` (`p_id`, `product_id`, `WCP_main_id`, `sample_plan_unit`, `sample_plan_value`, `sale_plan_unit`, `sale_plan_value`) VALUES
(1, '2TI2S54DJKL', 1, 5, 100, 40, 2000),
(2, '5LI2S54DJKL', 1, 3, 75, 20, 1000),
(3, 'D9D2S54DJKL', 2, 5, 160, 35, 1600),
(4, 'SLZCHE9NH5', 2, 3, 80, 15, 750),
(25, '2TI2S54DJKL', 9, 3, 117, 20, 543.4),
(26, 'SLZCHE9NH5', 9, 2, 60, 15, 183),
(27, 'NHD2S54DJKL', 10, 5, 159.5, 30, 690.6),
(28, 'OHD2S54DJKL', 10, 4, 176, 25, 766.25),
(29, 'OHD2S54DJKL', 11, 5, 220, 40, 1226);

-- --------------------------------------------------------

--
-- Table structure for table `WCP_wrap`
--

CREATE TABLE IF NOT EXISTS `WCP_wrap` (
  `wcp_wrap_id` int(11) NOT NULL,
  `month` text NOT NULL,
  `year` text NOT NULL,
  `create_time` text,
  `submit_status` tinyint(1) NOT NULL DEFAULT '0',
  `approval_status` tinyint(1) NOT NULL DEFAULT '0',
  `is_excepted` tinyint(1) NOT NULL DEFAULT '0',
  `approved_by` varchar(100) DEFAULT NULL,
  `mr_id` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `WCP_wrap`
--

INSERT INTO `WCP_wrap` (`wcp_wrap_id`, `month`, `year`, `create_time`, `submit_status`, `approval_status`, `is_excepted`, `approved_by`, `mr_id`) VALUES
(1, 'January', '2017', NULL, 0, 0, 0, NULL, 'MR1_INDORE'),
(17, 'February', '2017', NULL, 1, 0, 0, NULL, 'MR1_INDORE'),
(18, 'March', '2017', NULL, 0, 0, 0, NULL, 'MR1_INDORE');

-- ----------------------------------------------------------
--
-- Table structure for table `Tour_Plan`
--

CREATE TABLE IF NOT EXISTS `Tour_Plan` (
  `user_id` varchar(100),
  `tour_month` varchar(3),
  `tour_year` INT(4) NOT NULL,
  `tour_plan` text,
  `approval_status` text,
  `edit_access` SET('0','1') NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `Tour_Plan` 
  ADD PRIMARY KEY (`user_id`,`tour_month`,`tour_year`);



--
-- Indexes for dumped tables
--

--
-- Indexes for table `additional_sale`
--
ALTER TABLE `additional_sale`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `product_id_constraint` (`product_id`),
  ADD KEY `doctor_id_constraint` (`doc_id`),
  ADD KEY `chem_id_constraint` (`chem_id`),
  ADD KEY `mr_id_constraint` (`mr_id`);

--
-- Indexes for table `Chemist_profile`
--
ALTER TABLE `Chemist_profile`
  ADD PRIMARY KEY (`chem_id`) USING BTREE,
  ADD KEY `activemr_id_constraint` (`active_MR`);

--
-- Indexes for table `chemist_visit`
--
ALTER TABLE `chemist_visit`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `fk_p_id` (`p_id`);

--
-- Indexes for table `chem_gifts`
--
ALTER TABLE `chem_gifts`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `mr_id` (`mr_id`),
  ADD KEY `chem_id` (`chem_id`),
  ADD KEY `gift_id` (`gift_id`);

--
-- Indexes for table `Doctor_Chemist`
--
ALTER TABLE `Doctor_Chemist`
  ADD PRIMARY KEY (`sno`),
  ADD UNIQUE KEY `doc_id` (`doc_id`),
  ADD KEY `ass_chem_id_1` (`ass_chem_id_1`),
  ADD KEY `ass_chem_id_2` (`ass_chem_id_2`),
  ADD KEY `ass_chem_id_3` (`ass_chem_id_3`);

--
-- Indexes for table `doctor_gifts`
--
ALTER TABLE `doctor_gifts`
  ADD PRIMARY KEY (`_id`),
  ADD KEY `mr_id` (`mr_id`),
  ADD KEY `doc_id` (`doc_id`),
  ADD KEY `gift_id` (`gift_id`);

--
-- Indexes for table `Doctor_profile`
--
ALTER TABLE `Doctor_profile`
  ADD PRIMARY KEY (`doc_id`) USING BTREE,
  ADD KEY `active_MR` (`active_MR`);

--
-- Indexes for table `doctor_visit`
--
ALTER TABLE `doctor_visit`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `_id` (`_id`),
  ADD KEY `p_id` (`p_id`);

--
-- Indexes for table `Employee_profile`
--
ALTER TABLE `Employee_profile`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `person_id` (`person_id`),
  ADD KEY `head_id` (`head_id`);

--
-- Indexes for table `Gifts`
--
ALTER TABLE `Gifts`
  ADD PRIMARY KEY (`gift_id`) USING BTREE;

--
-- Indexes for table `Head_History`
--
ALTER TABLE `Head_History`
  ADD PRIMARY KEY (`sno`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `head_id` (`head_id`);

--
-- Indexes for table `MR_History`
--
ALTER TABLE `MR_History`
  ADD PRIMARY KEY (`sno`),
  ADD UNIQUE KEY `child_id` (`child_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Person_History`
--
ALTER TABLE `Person_History`
  ADD PRIMARY KEY (`sno`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `Person_profiles`
--
ALTER TABLE `Person_profiles`
  ADD PRIMARY KEY (`person_id`) USING BTREE,
  ADD UNIQUE KEY `person_id_2` (`person_id`);

--
-- Indexes for table `Price_History`
--
ALTER TABLE `Price_History`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`) USING BTREE;

--
-- Indexes for table `Set_data`
--
ALTER TABLE `Set_data`
  ADD PRIMARY KEY (`sno`),
  ADD UNIQUE KEY `set_no_2` (`set_no`),
  ADD KEY `set_no` (`set_no`),
  ADD KEY `mr_id` (`mr_id`);

--
-- Indexes for table `Sync_Table`
--
ALTER TABLE `Sync_Table`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `WCP_main`
--
ALTER TABLE `WCP_main`
  ADD PRIMARY KEY (`WCP_id`),
  ADD KEY `WCP_wrap_id` (`WCP_wrap_id`),
  ADD KEY `doc_id` (`doc_id`) USING BTREE;

--
-- Indexes for table `WCP_products`
--
ALTER TABLE `WCP_products`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `p_id` (`p_id`),
  ADD UNIQUE KEY `NOSameProductUniqueIndex` (`WCP_main_id`,`product_id`) USING BTREE,
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `WCP_wrap`
--
ALTER TABLE `WCP_wrap`
  ADD PRIMARY KEY (`wcp_wrap_id`),
  ADD UNIQUE KEY `no_multiple_WCP` (`month`(100),`year`(100),`mr_id`),
  ADD KEY `mr_id` (`mr_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `additional_sale`
--
ALTER TABLE `additional_sale`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `chemist_visit`
--
ALTER TABLE `chemist_visit`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `chem_gifts`
--
ALTER TABLE `chem_gifts`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Doctor_Chemist`
--
ALTER TABLE `Doctor_Chemist`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `doctor_visit`
--
ALTER TABLE `doctor_visit`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Head_History`
--
ALTER TABLE `Head_History`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `MR_History`
--
ALTER TABLE `MR_History`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `Person_History`
--
ALTER TABLE `Person_History`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `Price_History`
--
ALTER TABLE `Price_History`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `Set_data`
--
ALTER TABLE `Set_data`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Sync_Table`
--
ALTER TABLE `Sync_Table`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `WCP_main`
--
ALTER TABLE `WCP_main`
  MODIFY `WCP_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `WCP_products`
--
ALTER TABLE `WCP_products`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `WCP_wrap`
--
ALTER TABLE `WCP_wrap`
  MODIFY `wcp_wrap_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `additional_sale`
--
ALTER TABLE `additional_sale`
  ADD CONSTRAINT `chem_id_constraint` FOREIGN KEY (`chem_id`) REFERENCES `Chemist_profile` (`chem_id`),
  ADD CONSTRAINT `doctor_id_constraint` FOREIGN KEY (`doc_id`) REFERENCES `Doctor_profile` (`doc_id`),
  ADD CONSTRAINT `mr_id_constraint` FOREIGN KEY (`mr_id`) REFERENCES `Employee_profile` (`user_id`),
  ADD CONSTRAINT `product_id_constraint` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `Chemist_profile`
--
ALTER TABLE `Chemist_profile`
  ADD CONSTRAINT `activemr_id_constraint` FOREIGN KEY (`active_MR`) REFERENCES `Employee_profile` (`user_id`);

--
-- Constraints for table `chemist_visit`
--
ALTER TABLE `chemist_visit`
  ADD CONSTRAINT `fk_p_id` FOREIGN KEY (`p_id`) REFERENCES `WCP_products` (`p_id`);

--
-- Constraints for table `chem_gifts`
--
ALTER TABLE `chem_gifts`
  ADD CONSTRAINT `chem_gifts_ibfk_1` FOREIGN KEY (`mr_id`) REFERENCES `Employee_profile` (`user_id`),
  ADD CONSTRAINT `chem_gifts_ibfk_2` FOREIGN KEY (`chem_id`) REFERENCES `Chemist_profile` (`chem_id`),
  ADD CONSTRAINT `chem_gifts_ibfk_3` FOREIGN KEY (`gift_id`) REFERENCES `Gifts` (`gift_id`);

--
-- Constraints for table `Doctor_Chemist`
--
ALTER TABLE `Doctor_Chemist`
  ADD CONSTRAINT `Doctor_Chemist_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `Doctor_profile` (`doc_id`),
  ADD CONSTRAINT `Doctor_Chemist_ibfk_2` FOREIGN KEY (`ass_chem_id_1`) REFERENCES `Chemist_profile` (`chem_id`),
  ADD CONSTRAINT `Doctor_Chemist_ibfk_3` FOREIGN KEY (`ass_chem_id_2`) REFERENCES `Chemist_profile` (`chem_id`),
  ADD CONSTRAINT `Doctor_Chemist_ibfk_4` FOREIGN KEY (`ass_chem_id_3`) REFERENCES `Chemist_profile` (`chem_id`);

--
-- Constraints for table `doctor_gifts`
--
ALTER TABLE `doctor_gifts`
  ADD CONSTRAINT `doctor_gifts_ibfk_1` FOREIGN KEY (`mr_id`) REFERENCES `Employee_profile` (`user_id`),
  ADD CONSTRAINT `doctor_gifts_ibfk_2` FOREIGN KEY (`doc_id`) REFERENCES `Doctor_profile` (`doc_id`),
  ADD CONSTRAINT `doctor_gifts_ibfk_3` FOREIGN KEY (`gift_id`) REFERENCES `Gifts` (`gift_id`);

--
-- Constraints for table `Doctor_profile`
--
ALTER TABLE `Doctor_profile`
  ADD CONSTRAINT `Doctor_profile_ibfk_1` FOREIGN KEY (`active_MR`) REFERENCES `Employee_profile` (`user_id`);

--
-- Constraints for table `doctor_visit`
--
ALTER TABLE `doctor_visit`
  ADD CONSTRAINT `doctor_visit_ibfk_1` FOREIGN KEY (`p_id`) REFERENCES `WCP_products` (`p_id`);

--
-- Constraints for table `Employee_profile`
--
ALTER TABLE `Employee_profile`
  ADD CONSTRAINT `Employee_profile_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `Person_profiles` (`person_id`),
  ADD CONSTRAINT `Employee_profile_ibfk_2` FOREIGN KEY (`head_id`) REFERENCES `Employee_profile` (`user_id`);

--
-- Constraints for table `Head_History`
--
ALTER TABLE `Head_History`
  ADD CONSTRAINT `Head_History_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Employee_profile` (`user_id`),
  ADD CONSTRAINT `Head_History_ibfk_2` FOREIGN KEY (`head_id`) REFERENCES `Employee_profile` (`user_id`);

--
-- Constraints for table `MR_History`
--
ALTER TABLE `MR_History`
  ADD CONSTRAINT `MR_History_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Employee_profile` (`user_id`);

--
-- Constraints for table `Person_History`
--
ALTER TABLE `Person_History`
  ADD CONSTRAINT `Person_History_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Employee_profile` (`user_id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `Person_History_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `Person_profiles` (`person_id`);

--
-- Constraints for table `Set_data`
--
ALTER TABLE `Set_data`
  ADD CONSTRAINT `Set_data_ibfk_1` FOREIGN KEY (`mr_id`) REFERENCES `Employee_profile` (`user_id`);

--
-- Constraints for table `WCP_main`
--
ALTER TABLE `WCP_main`
  ADD CONSTRAINT `WCP_main_ibfk_2` FOREIGN KEY (`doc_id`) REFERENCES `Doctor_profile` (`doc_id`),
  ADD CONSTRAINT `WCP_main_ibfk_3` FOREIGN KEY (`WCP_wrap_id`) REFERENCES `WCP_wrap` (`wcp_wrap_id`);

--
-- Constraints for table `WCP_products`
--
ALTER TABLE `WCP_products`
  ADD CONSTRAINT `WCP_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `WCP_products_ibfk_2` FOREIGN KEY (`WCP_main_id`) REFERENCES `WCP_main` (`WCP_id`);

--
-- Constraints for table `WCP_wrap`
--
ALTER TABLE `WCP_wrap`
  ADD CONSTRAINT `WCP_wrap_ibfk_1` FOREIGN KEY (`mr_id`) REFERENCES `Employee_profile` (`user_id`),
  ADD CONSTRAINT `WCP_wrap_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `Employee_profile` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
