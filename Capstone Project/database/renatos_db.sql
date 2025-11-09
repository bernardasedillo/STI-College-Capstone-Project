-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2025 at 10:16 AM
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
-- Database: `renatos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `timestamp`, `user_id`, `action`, `details`) VALUES
(72, '2025-09-29 01:11:50', 1, 'Logout', 'Successful logout.'),
(73, '2025-09-29 01:12:01', 9, 'Login', 'Successful login.'),
(74, '2025-09-29 01:13:01', 0, 'Reservation Management', 'Processed checkout for reservation ID: 44. Total paid: 312000, Final Balance: 0, Billing Status: Paid'),
(75, '2025-09-29 01:13:17', 9, 'Logout', 'Successful logout.'),
(76, '2025-09-29 01:13:22', 1, 'Login', 'Successful login.'),
(77, '2025-09-29 01:48:31', 1, 'Login', 'Successful login.'),
(78, '2025-09-29 15:36:50', 0, 'Reservation Management', 'Re-scheduled reservation ID: 46 from 2025-10-04 to 2025-10-10'),
(79, '2025-09-29 15:37:04', 0, 'Confirm Reservation', 'Confirmed reservation ID 46'),
(80, '2025-09-29 15:37:19', 0, 'Reservation Management', 'Re-scheduled reservation ID: 46 from 2025-10-04 to 2025-10-08'),
(81, '2025-09-29 15:37:28', 0, 'Confirm Reservation', 'Confirmed reservation ID 46'),
(82, '2025-09-29 15:37:41', 0, 'Reservation Management', 'Re-scheduled reservation ID: 46 from 2025-10-04 to 2025-10-05'),
(83, '2025-09-29 15:37:52', 0, 'Confirm Reservation', 'Confirmed reservation ID 46'),
(84, '2025-09-29 15:38:10', 0, 'Reservation Management', 'Re-scheduled reservation ID: 46 from 2025-10-04 to 2025-10-17'),
(85, '2025-09-29 15:38:37', 0, 'Confirm Reservation', 'Confirmed reservation ID 46'),
(86, '2025-09-29 20:16:42', 0, 'Account Management', 'Archived account with admin_id: 15'),
(87, '2025-09-29 20:21:31', 1, 'Account Management', 'Restored account with admin_id: 15'),
(88, '2025-09-29 20:51:49', 0, 'Account Management', 'Archived account with admin_id: 15'),
(89, '2025-09-29 20:51:58', 1, 'Account Management', 'Restored account with admin_id: 15'),
(90, '2025-09-29 20:59:16', 1, 'Logout', 'Successful logout.'),
(91, '2025-09-29 20:59:51', 1, 'Login', 'Successful login.'),
(92, '2025-09-29 20:59:54', 1, 'Logout', 'Successful logout.'),
(93, '2025-09-29 21:00:03', 1, 'Login', 'Successful login.'),
(94, '2025-09-29 21:01:04', 1, 'Logout', 'Successful logout.'),
(95, '2025-09-29 21:01:11', 1, 'Login', 'Successful login.'),
(96, '2025-09-29 21:56:45', 1, 'Content Management', 'Added rest day: 2025-10-01 with reason: Close '),
(97, '2025-09-29 22:12:35', 1, 'Content Management', 'Added rest day: 2025-10-01 with reason: Closed'),
(98, '2025-09-29 22:18:57', 1, 'Content Management', 'Added rest day: 2025-10-01 with reason: Close'),
(99, '2025-09-29 22:58:27', 1, 'Content Management', 'Added rest day: 2025-10-01 with reason: Closed'),
(100, '2025-09-29 22:58:36', 1, 'Content Management', 'Added rest day: 2025-10-01 with reason: Closed'),
(101, '2025-09-30 15:08:46', 1, 'Logout', 'Successful logout.'),
(102, '2025-09-30 15:09:49', 1, 'Login', 'Successful login.'),
(103, '2025-09-30 15:37:48', 0, 'Inventory Management', 'Restored item: Demo (Type: Example)'),
(104, '2025-09-30 15:41:07', 1, 'Content Management', 'Added rest day: 2025-10-03 with reason: Close ');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `role` varchar(50) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `username`, `password`, `status`, `role`) VALUES
(1, 'Doms', 'bigboss', '12345', 'active', 'Super Admin'),
(9, 'Administrator', 'admin', 'admin', 'active', 'Administrator'),
(10, 'Staff', 'Staff', 'admin', 'active', 'Staff'),
(11, 'Manager', 'maneger', '12345', 'active', 'Event Manager'),
(15, 'Rohan', 'nardo', '1234', 'active', 'Event Manager');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `billing_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `down_payment` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','GCash','BDO') NOT NULL DEFAULT 'Cash',
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`billing_id`, `reservation_id`, `total_amount`, `down_payment`, `balance`, `payment_method`, `status`, `created_at`) VALUES
(17, 27, 32000.00, 32000.00, 0.00, 'Cash', 'Paid', '2025-09-24 04:28:20'),
(18, 28, 7000.00, 7000.00, 0.00, 'Cash', 'Paid', '2025-09-25 07:14:24'),
(34, 44, 312000.00, 312000.00, 0.00, 'Cash', 'Paid', '2025-09-28 13:03:53'),
(36, 46, 66000.00, 33000.00, 33000.00, 'Cash', 'Pending', '2025-09-28 13:46:35'),
(37, 47, 66000.00, 33000.00, 33000.00, 'Cash', 'Pending', '2025-09-28 13:53:12');

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE `prices` (
  `id` int(11) NOT NULL,
  `venue` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `day_type` varchar(50) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `duration_hours` varchar(100) DEFAULT NULL,
  `affiliate_catering` varchar(255) DEFAULT NULL,
  `affiliate_lights` varchar(255) DEFAULT NULL,
  `inclusions` varchar(255) DEFAULT NULL,
  `max_guest` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prices`
--

INSERT INTO `prices` (`id`, `venue`, `name`, `day_type`, `duration`, `price`, `notes`, `is_archived`, `duration_hours`, `affiliate_catering`, `affiliate_lights`, `inclusions`, `max_guest`) VALUES
(1, 'Room', 'Room 101', NULL, NULL, 2500.00, NULL, 0, '12 Hours Stay', NULL, NULL, NULL, NULL),
(2, 'Room', 'Room 101', NULL, NULL, 3500.00, NULL, 0, '22 Hours Stay', NULL, NULL, NULL, NULL),
(3, 'Resort', 'Daytime 8 Hours ', 'Weekdays', 'Feb - November', 9800.00, '', 0, '9:00am - 5:00pm', NULL, NULL, NULL, 25),
(4, 'Resort', 'Daytime 8 Hours ', 'Weekends', 'Feb - November', 11800.00, '', 0, '9:00am - 5:00pm', NULL, NULL, NULL, 25),
(5, 'Resort', 'Daytime 8 Hours ', 'Weekdays', 'Dec - Jan', 11000.00, '', 0, '9:00am - 5:00pm', NULL, NULL, NULL, 25),
(6, 'Resort', 'Daytime 8 Hours ', 'Weekends', 'Dec - Jan', 12500.00, '', 0, '9:00am - 5:00pm', NULL, NULL, NULL, 25),
(7, 'Resort', 'Overnight 12 Hours', 'Weekdays', 'Feb - November', 10800.00, '', 0, '7:00pm - 7:00am', NULL, NULL, NULL, 25),
(8, 'Resort', 'Overnight 12 Hours', 'Weekends', 'Feb - November', 12800.00, '', 0, '7:00pm - 7:00am', NULL, NULL, NULL, 25),
(9, 'Resort', 'Overnight 12 Hours', 'Weekdays', 'Dec - Jan', 13000.00, '', 0, '7:00pm - 7:00am', NULL, NULL, NULL, 25),
(10, 'Resort', 'Overnight 12 Hours', 'Weekends', 'Dec - Jan', 14000.00, '', 0, '7:00pm - 7:00am', NULL, NULL, NULL, 30),
(11, 'Resort', 'Staycation 22 Hours', 'Weekdays', 'Feb - November', 20500.00, '', 0, '9:00am - 7:00am', NULL, NULL, NULL, 30),
(12, 'Resort', 'Staycation 22 Hours', 'Weekends', 'Feb - November', 22500.00, '', 0, '9:00am - 7:00am', NULL, NULL, NULL, 30),
(13, 'Resort', 'Staycation 22 Hours', 'Weekdays', 'Dec - Jan', 23000.00, '', 0, '9:00am - 7:00am', NULL, NULL, NULL, 30),
(14, 'Resort', 'Staycation 22 Hours', 'Weekends', 'Dec - Jan', 24000.00, '', 0, '9:00am - 7:00am', NULL, NULL, NULL, 30),
(16, 'Mini Function Hall', 'Venue Only', 'Weekdays', NULL, 9500.00, '', 0, '', '', '', 'Veranda for smoking area, Infinity chairs and tables', NULL),
(17, 'Mini Function Hall', 'Venue Only', 'Weekends', NULL, 10000.00, '', 0, '', '', '', 'Veranda for smoking area, Infinity chairs and tables', NULL),
(18, 'Mini Function Hall', 'Exclusive 12K Package', 'Any_Day', NULL, 12000.00, '', 0, '', 'Renato\'s Catering Services', 'Renato\'s Lights and Sound', '10K (Consumable of food and drinks Menu Based + Service Charge), Veranda for smoking area, Infinity chairs and tables ', NULL),
(19, 'Mini Function Hall', 'Exclusive 25K Package', 'Any_Day', NULL, 25000.00, '', 0, '', 'Renato\'s Catering Services', 'Renato\'s Lights and Sound', 'Private Comfort room, Veranda for smoking area, Infinity chairs and tables', 30),
(20, 'Mini Function Hall', 'Exclusive 30K Package', 'Any_Day', NULL, 30000.00, '', 0, '', 'Renato\'s Catering Services', 'Renato\'s Lights and Sound', 'Private Comfort room, Veranda for smoking area, Infinity chairs and tables', 30),
(21, 'Mini Function Hall', 'Excess Rate', 'Any_Day', NULL, 1500.00, 'venue per hour excess', 0, '', '', '', '', NULL),
(22, 'Renatos Hall', '88K Intimate Package', 'Any_Day', NULL, 88000.00, '', 0, '', 'Renato\'s Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 12 Hours use of Renato\'s Suite Room, Basic Backdrop and Arch Set-up, One tier Cake', 60),
(23, 'Renatos Hall', 'Venue Only', 'Weekdays', NULL, 20000.00, '', 0, '', '', '', '', NULL),
(24, 'Renatos Hall', 'Venue Only', 'Weekends', NULL, 22000.00, '', 0, '', '', '', '', NULL),
(25, 'Renatos Hall', 'Venue + Resort', 'Weekdays', NULL, 40000.00, '', 0, '', '', '', '12 Hours use of Suite Room', NULL),
(26, 'Renatos Hall', 'Venue + Resort', 'Weekends', NULL, 44000.00, '', 0, '', '', '', '12 Hours use of Suite Room', NULL),
(27, 'Renatos Hall', 'Venue + Resort + Rooms', 'Weekdays', NULL, 40000.00, '', 0, '', '', '', '22 Hours use of Suite Room', NULL),
(28, 'Renatos Hall', 'Venue + Resort + Rooms', 'Weekends', NULL, 44000.00, '', 0, '', '', '', '22 Hours use of Suite Room', NULL),
(29, 'Renatos Hall', 'Excess Rate', 'Any_Day', NULL, 3000.00, 'Per hour excess', 0, '', '', '', '', NULL),
(30, 'Renatos Pavilion', 'Venue Only', 'Weekdays', NULL, 75000.00, '', 0, '', '', '', '', NULL),
(31, 'Renatos Pavilion', 'Venue Only', 'Weekends', NULL, 85000.00, '', 0, '', '', '', '', NULL),
(32, 'Renatos Pavilion', 'Venue + Resort ', 'Weekdays', NULL, 95000.00, '', 0, '', '', '', '12 Hours use of Renato\'s Pavilion Room', NULL),
(33, 'Renatos Pavilion', 'Venue + Resort ', 'Weekends', NULL, 105000.00, '', 0, '', '', '', '12 Hours use of Renato\'s Pavilion Room', NULL),
(34, 'Renatos Pavilion', 'Venue + Resort + Rooms ', 'Weekdays', NULL, 109000.00, '', 0, '', '', '', '22 Hours use of Renato\'s Pavilion Room, Free use of Garden', NULL),
(35, 'Renatos Pavilion', 'Venue + Resort + Rooms ', 'Weekends', NULL, 120000.00, '', 0, '', '', '', '22 Hours use of Renato\'s Pavilion Room,  Free use of Garden', NULL),
(36, 'Renatos Pavilion', 'Hizon\'s Catering Package', 'Weekdays', NULL, 180000.00, '', 0, '', 'Hizon\'s Event Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 22 Hours use of Renato\'s Pavilion Room', 100),
(37, 'Renatos Pavilion', 'Hizon\'s Catering Package', 'Weekends', NULL, 190000.00, '', 0, '', 'Hizon\'s Event Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 22 Hours use of Renato\'s Pavilion Room', 100),
(38, 'Renatos Pavilion', 'Densol\'s Catering Package', 'Weekdays', NULL, 178000.00, '', 0, '', 'Densol\'s Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 22 Hours use of Renato\'s Pavilion Room', 100),
(39, 'Renatos Pavilion', 'Densol\'s Catering Package', 'Weekends', NULL, 188000.00, '', 0, '', 'Densol\'s Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 22 Hours use of Renato\'s Pavilion Room', 100),
(40, 'Renatos Pavilion', 'Chef Maria\'s Catering Package', 'Weekdays', NULL, 312000.00, '', 0, '', 'Chef Maria\'s Event Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 22 Hours use of Renato\'s Pavilion Room, 2 Layer Customized design Cake, Classic Invitation', 100),
(41, 'Renatos Pavilion', 'Chef Maria\'s Catering Package', 'Weekends', NULL, 322000.00, '', 0, '', 'Chef Maria\'s Event Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 22 Hours use of Renato\'s Pavilion Room, 2 Layer Customized design Cake, Classic Invitation', 100),
(42, 'Renatos Pavilion', 'Madriaga\'s Catering Package', 'Weekdays', NULL, 180000.00, '', 0, '', 'Madriaga\'s Event Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 22 Hours use of Renato\'s Pavilion Room', 100),
(43, 'Renatos Pavilion', 'Madriaga\'s Catering Package', 'Weekends', NULL, 190000.00, '', 0, '', 'Madriaga\'s Event Catering Services', 'Renato\'s Lights and Sound', '22 Hours Resort Staycation, 22 Hours use of Renato\'s Pavilion Room', 100),
(44, 'Renatos Pavilion', 'Excess Rate', '', NULL, 5000.00, '', 0, '', '', '', '', NULL),
(52, 'Resort', 'testing', 'hahahahaha', 'what', 1222.00, '', 1, NULL, NULL, NULL, NULL, NULL),
(53, 'Resort', 'Staycation 22 Hours', 'Weekends', 'Dec - Jan', 24000.00, '', 0, '7:00pm - 5:00pm', NULL, NULL, NULL, 30),
(54, 'Resort', 'Staycation 22 Hours', 'Weekdays', 'Dec - Jan', 23000.00, '', 0, '7:00pm - 5:00pm', NULL, NULL, NULL, 30),
(55, 'Resort', 'Staycation 22 Hours', 'Weekdays', 'Feb - November', 20500.00, '', 0, '7:00pm - 5:00pm', NULL, NULL, NULL, 30),
(56, 'Resort', 'Staycation 22 Hours', 'Weekends', 'Feb - November', 22500.00, '', 0, '7:00pm - 5:00pm', NULL, NULL, NULL, 30),
(57, 'Resort', '66K Resort Package', 'Any_Day', '', 66000.00, 'package', 0, '9:00am - 7:00am', NULL, NULL, NULL, 60),
(58, 'Resort', 'testingssss', 'Any_Day', '', 12121212.00, '', 1, '', NULL, NULL, NULL, NULL),
(59, 'Resort', '66K Resort Package', 'Any_Day', '', 66000.00, 'package', 0, '7:00pm - 5:00pm', NULL, NULL, NULL, 60),
(61, 'Affiliates', 'Renato\'s Catering Services', NULL, NULL, 0.00, 'Catering option', 0, '', NULL, NULL, NULL, NULL),
(62, 'Affiliates', 'Sac B. Catering Services', NULL, NULL, 0.00, 'Catering option', 0, '', NULL, NULL, NULL, NULL),
(63, 'Affiliates', 'Densol\'s Catering Services', NULL, NULL, 0.00, 'Catering option', 0, '', NULL, NULL, NULL, NULL),
(64, 'Affiliates', 'Abraham\'s Event Catering Services', NULL, NULL, 0.00, 'Catering option', 0, '', NULL, NULL, NULL, NULL),
(65, 'Affiliates', 'Hizon\'s Event Catering Services', NULL, NULL, 0.00, 'Catering option', 0, '', NULL, NULL, NULL, NULL),
(66, 'Affiliates', 'Madriaga\'s Event Catering Services', NULL, NULL, 0.00, 'Catering option', 0, '', NULL, NULL, NULL, NULL),
(67, 'Affiliates', 'Chef Maria\'s Event Catering Services', NULL, NULL, 0.00, 'Catering option', 0, '', NULL, NULL, NULL, NULL),
(69, 'Affiliates', 'Renato\'s Lights and Sound', NULL, NULL, 0.00, 'Lights & Sound option', 0, '', NULL, NULL, NULL, NULL),
(70, 'Affiliates', 'Live Audio', NULL, NULL, 0.00, 'Lights & Sound option', 0, '', NULL, NULL, NULL, NULL),
(71, 'Affiliates', 'JGR', NULL, NULL, 0.00, 'Lights & Sound option', 0, '', NULL, NULL, NULL, NULL),
(72, 'Affiliates', 'Rave', NULL, NULL, 0.00, 'Lights & Sound option', 0, '', NULL, NULL, NULL, NULL),
(74, 'Affiliates', 'Renato\'s Cafe', NULL, NULL, 0.00, 'Mobile Bar option', 0, '', NULL, NULL, NULL, NULL),
(76, 'Affiliates', 'Donut Wall by Hazel', NULL, NULL, 0.00, 'Grazing Table option', 0, '', NULL, NULL, NULL, NULL),
(77, 'Affiliates', 'The Yolk Coffee and Snack Bar', NULL, NULL, 0.00, 'Grazing Table option', 0, '', NULL, NULL, NULL, NULL),
(78, 'Affiliates', 'Koun Takoyaki', NULL, NULL, 0.00, 'Grazing Table option', 0, '', NULL, NULL, NULL, NULL),
(80, 'Affiliates', 'Early Ingress Set-Up', NULL, NULL, 3000.00, 'Additional Venue Fee', 0, '', NULL, NULL, NULL, NULL),
(81, 'Affiliates', 'Use of Garden for Wedding Ceremony (2 Hours)', NULL, NULL, 5000.00, 'Additional Venue Fee', 0, '', NULL, NULL, NULL, NULL),
(82, 'Affiliates', 'sample', NULL, NULL, 0.00, 'catering option', 1, '', NULL, NULL, NULL, NULL),
(83, 'Renatos Hall', 'Guest Excess Rate', 'Any_Day', NULL, 600.00, '88K Package', 0, '', '', '', '', NULL),
(84, 'Renatos Pavilion', 'Guest Excess Rate', '', NULL, 750.00, 'Hizon\'s Catering Package', 0, '', '', '', '', NULL),
(85, 'Renatos Pavilion', 'Guest Excess Rate ', '', NULL, 720.00, 'Densol\'s Catering Package', 0, '', '', '', '', NULL),
(86, 'Renatos Pavilion', 'Guest Excess Rate', '', NULL, 1450.00, 'Chef Maria\'s Catering Package', 0, '', '', '', '', NULL),
(87, 'Renatos Pavilion', 'Guest Excess Rate', '', NULL, 740.00, 'Madriaga\'s Catering Package', 0, '', '', '', '', NULL),
(88, 'Room', 'Room 102', NULL, NULL, 2500.00, NULL, 0, '12 Hours Stay', NULL, NULL, NULL, NULL),
(89, 'Room', 'Room 102', NULL, NULL, 3500.00, NULL, 0, '22 Hours Stay', NULL, NULL, NULL, NULL),
(90, 'Room', 'Room 201', NULL, NULL, 2500.00, NULL, 0, '12 Hours Stay', NULL, NULL, NULL, NULL),
(91, 'Room', 'Room 201', NULL, NULL, 3500.00, NULL, 0, '22 Hours Stay', NULL, NULL, NULL, NULL),
(92, 'Room', 'Room 202', NULL, NULL, 2500.00, NULL, 0, '12 Hours Stay', NULL, NULL, NULL, NULL),
(93, 'Room', 'Room 202', NULL, NULL, 3500.00, NULL, 0, '22 Hours Stay', NULL, NULL, NULL, NULL),
(94, 'Room', 'Room 202', '', NULL, 3500.00, '', 1, '', NULL, NULL, NULL, NULL),
(95, 'Room', 'sample', '', NULL, 1.00, '', 1, '', NULL, NULL, NULL, NULL),
(96, 'Room', 'sample', '', NULL, 1.00, '', 1, '', NULL, NULL, NULL, NULL),
(97, 'Resort', 'Guest Excess Rate', '', '', 150.00, 'Over 25 or 30 guests', 0, '', NULL, NULL, NULL, NULL),
(98, 'Resort', 'Guest Excess Rate (66K Package)', '', '', 600.00, 'excess rate', 0, '', NULL, NULL, NULL, NULL),
(99, 'Mini Function Hall', 'Guest Excess Rate', '', NULL, 600.00, '25K Package', 0, '', '', '', '', NULL),
(100, 'Mini Function Hall', 'Guest Excess Rate', '', NULL, 700.00, '30K Package', 0, '', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `reservation_type` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `full_address` varchar(255) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkin_time` time DEFAULT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `guests` int(11) NOT NULL,
  `room_number` varchar(255) DEFAULT NULL,
  `resort_package` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `resort_room` varchar(255) DEFAULT NULL,
  `events_package` varchar(255) DEFAULT NULL,
  `events_venue` varchar(255) DEFAULT NULL,
  `event_type` varchar(255) DEFAULT NULL,
  `affiliate_caterer` varchar(255) DEFAULT NULL,
  `additional_fee` varchar(255) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `proof_of_payment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `original_checkin_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `reservation_type`, `full_name`, `email`, `phone`, `full_address`, `checkin_date`, `checkin_time`, `duration_hours`, `duration_minutes`, `guests`, `room_number`, `resort_package`, `duration`, `resort_room`, `events_package`, `events_venue`, `event_type`, `affiliate_caterer`, `additional_fee`, `special_requests`, `status`, `notes`, `total_amount`, `proof_of_payment`, `payment_method`, `proof_of_payment_path`, `created_at`, `original_checkin_date`) VALUES
(27, 'Event Package', 'Chichay', 'pdoms773@gmail.com', '09777989147', 'pasig city', '2025-09-30', NULL, NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'checked-out', NULL, 32000.00, NULL, NULL, NULL, '2025-09-24 04:28:20', NULL),
(28, 'Room', 'Rohan Victor', 'nadrdog@gmail.com', '09777989147', 'cainta rizal', '2025-09-29', NULL, NULL, NULL, 12, 'Room 102, Room 201', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'checked-out', NULL, 7000.00, NULL, NULL, NULL, '2025-09-25 07:14:24', NULL),
(44, 'Event Package', 'Domingo B. Cristo', 'pdoms773@gmail.com', '09777860332', 'Santa Lucia Pasig City', '2025-10-01', NULL, 4, 0, 50, NULL, NULL, NULL, NULL, '40', NULL, NULL, NULL, NULL, NULL, 'checked-out', NULL, 312000.00, 'uploads/proof_68d93239396275.50510139.jpg', 'GCash', NULL, '2025-09-28 13:03:53', NULL),
(46, 'Resort', 'francis credo', 'cis@gmail.com', '09254564548', 'cainta rizal', '2025-10-17', NULL, NULL, NULL, 60, NULL, '59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'confirmed', NULL, 66000.00, 'uploads/proof_68d93c3bc2d8c1.14775233.jpg', 'GCash', NULL, '2025-09-28 13:46:35', '2025-10-04'),
(47, 'Resort', 'amir sugui', 'amir@gmail.com', '09234234663', 'Santa Lucia Pasig City', '2025-10-03', NULL, NULL, NULL, 50, NULL, '57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, 66000.00, 'uploads/proof_68d93dc830c9b4.75692333.jpg', 'GCash', NULL, '2025-09-28 13:53:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rest_days`
--

CREATE TABLE `rest_days` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `reason` varchar(255) DEFAULT 'Closed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rest_days`
--

INSERT INTO `rest_days` (`id`, `date`, `reason`) VALUES
(9, '2025-10-03', 'Close ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`billing_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rest_days`
--
ALTER TABLE `rest_days`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `date` (`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `rest_days`
--
ALTER TABLE `rest_days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
