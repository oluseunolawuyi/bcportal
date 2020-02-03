-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2018 at 07:14 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cvbc`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_messages`
--

CREATE TABLE `admin_messages` (
  `id` int(100) NOT NULL,
  `ticket_id` varchar(20) NOT NULL DEFAULT '',
  `sender_name` varchar(50) NOT NULL DEFAULT '',
  `sender_email` varchar(50) NOT NULL DEFAULT '',
  `sender_phone` varchar(50) NOT NULL DEFAULT '',
  `recipient_name` varchar(50) NOT NULL DEFAULT '',
  `recipient_email` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `message` longtext NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  `inbox` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `sent` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_messages`
--

INSERT INTO `admin_messages` (`id`, `ticket_id`, `sender_name`, `sender_email`, `sender_phone`, `recipient_name`, `recipient_email`, `subject`, `message`, `viewed`, `inbox`, `sent`, `date_time`) VALUES
(4, '20171006134739', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@earnensured.com).</p>{\r\n<p>Thank you.</p>', 1, 0, 1, '2017-10-06 13:47:39'),
(5, '20171006140255', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Account Deactivation Notice', '<p>Dear Ishola Wasiu Ayobami,</p><p>This is to notify you that your account has been deactivated due to some reasons. Therefore, you can not log in with your email(wasiuonline@gmail.com). Kindly contact the customer service for account activation.</p>', 1, 1, 0, '2017-10-06 14:02:55'),
(7, '20171006140306', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Account Activation Notice', '<p>Dear Ishola Wasiu Ayobami,</p><p>This is to notify you that your account has been activated. Your email(wasiuonline@gmail.com) has just been confirmed. You can always log in with your email (wasiuonline@gmail.com) and password.</p>', 1, 1, 0, '2017-10-06 14:03:06'),
(17, '20171006151045', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'hgzjhdfz', 'bhbshsd@sgs.ss', 'Successful Account Activation', '<p>Dear hgzjhdfz, this is to notify you that your account has been activated. Your email (bhbshsd@sgs.ss) has just been confirmed. You can always log in with your email (bhbshsd@sgs.ss) and password.</p>', 0, 1, 0, '2017-10-06 15:10:45'),
(19, '20171006175802', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Bonus Given on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a bonus of $5.00 has been added to your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 1, 1, 0, '2017-10-06 17:58:02'),
(20, '20171006175802', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'All Active Users', 'no-reply@reliancewisdom.com', 'Bonus Given to Active Users', '<p>Dear all active users,</p>\r\n<p>This is to notify you that a bonus of $5.00 has been added to your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-10-06 17:58:02'),
(21, '20171006180810', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Bonus Given on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a bonus of $2.00 has been added to your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 1, 1, 0, '2017-10-06 18:08:10'),
(22, '20171006180810', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'All Active Users', 'no-reply@reliancewisdom.com', 'Bonus Given to Active Users', '<p>Dear all active users,</p>\r\n<p>This is to notify you that a bonus of $2.00 has been added to your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-10-06 18:08:10'),
(31, '20171103124043', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Testing', '<p>Dear Ishola Wasiu Ayobami,</p><p>&lt;p&gt;This is a testing&lt;/p&gt;</p>', 0, 1, 0, '2017-11-03 12:40:43'),
(32, '20171103124502', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Testing 2', '<p>Dear Ishola Wasiu Ayobami,</p><p>&lt;p&gt;This is a second testing.&lt;/p&gt;</p>', 0, 1, 0, '2017-11-03 12:45:02'),
(33, '20171103124834', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'And this', '<p>Dear Ishola Wasiu Ayobami,</p><p>&lt;p&gt;This&amp;nbsp;This&amp;nbsp;&lt;strong&gt;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;Th&lt;/strong&gt;is&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;&lt;/p&gt;</p>', 0, 1, 0, '2017-11-03 12:48:34'),
(36, '20171104191127', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Bonus Given on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a bonus of $15.00 has been added to your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 1, 1, 0, '2017-11-04 19:11:27'),
(42, '20171105130733', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Bonus Given on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a bonus of $25.00 has been added to your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-11-05 13:07:33'),
(45, '20171105130843', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Penalty Charged on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a penalty of $9.00 has been charged to your e-wallet and deducted from your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-11-05 13:08:43'),
(48, '20171105132541', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Bonus Given on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a bonus of $5.00 has been added to your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-11-05 13:25:41'),
(51, '20171105132717', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Penalty Charged on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a penalty of $8.00 has been charged to your e-wallet and deducted from your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-11-05 13:27:17'),
(54, '20171105151937', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Penalty Charged on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a penalty of $3.00 has been charged to your e-wallet and deducted from your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-11-05 15:19:37'),
(57, '20171105152023', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Penalty Charged on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a penalty of $8.00 has been charged to your e-wallet and deducted from your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-11-05 15:20:23'),
(59, '20171209101102', 'Levi Project 1', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Bonus Given on Account', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that a bonus of $3.00 has been added to your outstanding balance on reliancewisdom.com.</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2017-12-09 10:11:02'),
(61, '20180112111023', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'Profile Update', '<p>Dear Administrator,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-01-12 11:10:23'),
(62, '20180112111023', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'Profile Update', '<p>Dear Administrator,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-01-12 11:10:23'),
(63, '20180112112110', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-01-12 11:21:10'),
(64, '20180112112110', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-01-12 11:21:10'),
(66, '20180112112130', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale1,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-01-12 11:21:30'),
(67, '20180112112139', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale1', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 1, 1, 0, '2018-01-12 11:21:39'),
(68, '20180112112139', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale1', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-01-12 11:21:39'),
(69, '20180112112151', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami1,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-01-12 11:21:51'),
(70, '20180112112151', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami1,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-01-12 11:21:51'),
(71, '20180112112158', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami1', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-01-12 11:21:58'),
(72, '20180112112158', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami1', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-01-12 11:21:58'),
(73, '20180220074950', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'Profile Update', '<p>Dear Administrator,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-02-20 07:49:50'),
(74, '20180220074950', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'Profile Update', '<p>Dear Administrator,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-02-20 07:49:50'),
(75, '20180221083700', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-02-21 08:37:00'),
(76, '20180221083700', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-02-21 08:37:00'),
(77, '20180221083717', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 1, 1, 0, '2018-02-21 08:37:17'),
(78, '20180221083717', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-02-21 08:37:17'),
(79, '20180221083832', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'Profile Update', '<p>Dear Administrator,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 1, 1, 0, '2018-02-21 08:38:32'),
(80, '20180221083832', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'Profile Update', '<p>Dear Administrator,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-02-21 08:38:32'),
(81, '20180226111658', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 11:16:58'),
(82, '20180226134939', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 13:49:39'),
(83, '20180226135035', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 13:50:35'),
(84, '20180226135101', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 13:51:01'),
(85, '20180226135137', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 13:51:37'),
(86, '20180226185420', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 18:54:20'),
(87, '20180226191041', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 19:10:41'),
(88, '20180226191324', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 19:13:24'),
(89, '20180226191350', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-26 19:13:50'),
(90, '20180227083246', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-02-27 08:32:46'),
(91, '20180227083246', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-02-27 08:32:46'),
(92, '20180227100809', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-27 10:08:09'),
(93, '20180227100942', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-02-27 10:09:42'),
(94, '20180227160012', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'New PDF Report Upload', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>We are pleased to inform you that a new PDF report has been uploaded for you.</p>\r\n<p>Kindly log in on reliancewisdom.com to view the report.</p>', 0, 1, 0, '2018-02-27 16:00:12'),
(95, '20180227160414', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'New PDF Report Upload', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>We are pleased to inform you that a new PDF report has been uploaded for you.</p>\r\n<p>Kindly log in on reliancewisdom.com to view the report.</p>', 0, 1, 0, '2018-02-27 16:04:14'),
(96, '20180305105934', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-03-05 10:59:34'),
(97, '20180305105934', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-03-05 10:59:34'),
(98, '20180305171059', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-03-05 17:10:59'),
(99, '20180305171059', 'CVBC', 'contact@reliancewisdom.com', '', 'Ishola Wasiu Ayobami', 'wasiuonline@gmail.com', 'Profile Update', '<p>Dear Ishola Wasiu Ayobami,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-03-05 17:10:59'),
(100, '20180305173046', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-03-05 17:30:46'),
(101, '20180305173046', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-03-05 17:30:46'),
(102, '20180305173107', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-03-05 17:31:07'),
(103, '20180305173107', 'CVBC', 'contact@reliancewisdom.com', '', 'Wasiu Olawale', 'wasionline@yahoo.com', 'Profile Update', '<p>Dear Wasiu Olawale,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user -  (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-03-05 17:31:07'),
(104, '20180306085636', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-06 08:56:36'),
(105, '20180306085837', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-06 08:58:37'),
(106, '20180310104343', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-10 10:43:43'),
(107, '20180310104402', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-10 10:44:02'),
(108, '20180310183639', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-10 18:36:39'),
(109, '20180310184216', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-10 18:42:16'),
(110, '20180310184804', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-10 18:48:04'),
(111, '20180311024325', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 02:43:25'),
(112, '20180311025249', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 02:52:49'),
(113, '20180311025655', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 02:56:55'),
(114, '20180311033910', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 03:39:10'),
(115, '20180311035623', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 03:56:23'),
(116, '20180311040229', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 04:02:29'),
(117, '20180311050523', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 05:05:23'),
(118, '20180311050543', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 05:05:43'),
(119, '20180311050733', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New BC Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 05:07:33'),
(120, '20180311081940', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 08:19:40'),
(121, '20180311121448', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 12:14:48'),
(122, '20180311121526', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 12:15:26'),
(123, '20180311133632', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-11 13:36:32'),
(124, '20180318112326', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New BC Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-18 11:23:26'),
(125, '20180318124348', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-18 12:43:48'),
(126, '20180318124541', 'CVBC', 'contact@reliancewisdom.com', '', 'Administrator', 'admin@riskcontrolnigeria.com', 'New CV Task(s)', '<p>Dear Administrator,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-18 12:45:41'),
(127, '20180318124643', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-18 12:46:43'),
(128, '20180318125550', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-18 12:55:50'),
(129, '20180318125921', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-18 12:59:21'),
(130, '20180321032839', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-21 03:28:39'),
(131, '20180321032935', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-21 03:29:35'),
(132, '20180321033533', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-21 03:35:33'),
(133, '20180321033606', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-21 03:36:06'),
(134, '20180321092423', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-21 09:24:23'),
(135, '20180322075235', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-22 07:52:35'),
(136, '20180322075445', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-22 07:54:45'),
(137, '20180322075507', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-22 07:55:07'),
(138, '20180322075609', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-22 07:56:09'),
(139, '20180322075735', 'CVBC', 'contact@reliancewisdom.com', '', 'Kunle Olayiwola', 'kunle@yahoo.com', 'New CV Task(s)', '<p>Dear Kunle Olayiwola,</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>', 0, 1, 0, '2018-03-22 07:57:35'),
(140, '20180323043202', 'CVBC', 'contact@reliancewisdom.com', '', 'Bola Bangbose', 'bola@gmail.com', 'Profile Update', '<p>Dear Bola Bangbose,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user - admin (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 1, 0, '2018-03-23 04:32:02'),
(141, '20180323043202', 'CVBC', 'contact@reliancewisdom.com', '', 'Bola Bangbose', 'bola@gmail.com', 'Profile Update', '<p>Dear Bola Bangbose,</p>\r\n<p>This is to notify you that your profile data has been modified by an admin user - admin (admin@riskcontrolnigeria.com).</p>\r\n<p>Thank you.</p>', 0, 0, 1, '2018-03-23 04:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(100) NOT NULL,
  `user_id` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `activity` longtext NOT NULL,
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `name`, `email`, `activity`, `date_time`) VALUES
(1, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Uploaded new bulk BC report.', '2018-03-11 05:05:23'),
(2, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Uploaded new bulk CV report.', '2018-03-11 05:05:43'),
(3, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with subject: Bolatito Oluwadara, in batch 1.', '2018-03-11 05:06:39'),
(4, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 3 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-11 05:06:39'),
(5, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-11 05:06:56'),
(6, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Added new 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-11 05:07:33'),
(7, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-11 08:13:36'),
(8, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-11 08:19:40'),
(9, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with subject: Bolatito Oluwadara, in batch 1.', '2018-03-11 12:13:30'),
(10, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 4 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-11 12:13:30'),
(11, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with subject: Olawale Ayinla, in batch 1.', '2018-03-11 12:14:38'),
(12, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 2 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Olawale Ayinla.', '2018-03-11 12:14:38'),
(13, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-11 12:14:48'),
(14, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-11 12:15:26'),
(15, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-11 12:15:56'),
(16, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ayinla Olalekan, in batch 2.', '2018-03-11 13:36:32'),
(17, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Logged in to own account.', '2018-03-11 17:05:22'),
(18, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Allowed the following privileges to a role (Accountant): Manage Admin Users, Add Admin Users, Edit Admin Users, Manage Clients, Add Clients, Edit Clients, Manage Agents.', '2018-03-12 04:22:10'),
(19, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Allowed the following privileges to a role (Accountant): Manage Admin Users, Add Admin Users, Edit Admin Users, Change Admin Picture, Assign Admin Role, Manage Clients, Add Clients, Edit Clients, Change Clients Picture, Bulk Client Upload, Manage Agents, Add Agents, Edit Agents, Change Agents Picture, Manage BC Verification Types, Manage BC Education Types, Manage Status Types, Manage Recommendation Types, Role Management, Manage BC Reports, Add BC Reports, Edit BC Reports, Delete BC Reports, Print BC Reports, Download BC Reports, Manage CV Reports, Add CV Reports, Edit CV Reports, Delete CV Reports, Download CV Reports, Manage Bulk BC Reports, Manage Bulk CV Reports, Manage Clients Reports, Manage Cover Letters, Manage General Messages.', '2018-03-12 04:22:31'),
(20, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Allowed the following privileges to a role (testing): Manage Admin Users, Add Admin Users, Edit Admin Users, Change Admin Picture, Assign Admin Role, Manage Clients, Add Clients, Edit Clients, Change Clients Picture, Bulk Client Upload, Manage Agents, Add Agents, Edit Agents, Change Agents Picture, Manage BC Verification Types, Manage BC Education Types, Manage Status Types, Manage Recommendation Types, Role Management, Manage BC Reports, Add BC Reports, Edit BC Reports, Delete BC Reports, Print BC Reports, Download BC Reports, Manage CV Reports, Add CV Reports, Edit CV Reports, Delete CV Reports, Download CV Reports, Manage Bulk BC Reports, Manage Bulk CV Reports, Manage Clients Reports, Manage Cover Letters, Manage General Messages.', '2018-03-12 04:22:53'),
(21, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Allowed the following privileges to a role (Testing): Manage Admin Users, Add Admin Users, Edit Admin Users, Change Admin Picture, Assign Admin Role, Manage Clients, Add Clients, Edit Clients, Change Clients Picture, Bulk Client Upload, Manage Agents, Add Agents, Edit Agents, Change Agents Picture, Manage BC Verification Types, Manage BC Education Types, Manage Status Types, Manage Recommendation Types, Role Management, Manage BC Reports, Add BC Reports, Edit BC Reports, Delete BC Reports, Print BC Reports, Download BC Reports, Manage CV Reports, Add CV Reports, Edit CV Reports, Delete CV Reports, Download CV Reports, Manage Bulk BC Reports, Manage Bulk CV Reports, Manage Clients Reports, Manage Cover Letters, Manage General Messages.', '2018-03-12 04:28:31'),
(22, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Deleted a role (0) from database.', '2018-03-12 04:30:47'),
(23, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Deleted 2 role from database: Testing, Accountant.', '2018-03-12 04:41:44'),
(24, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Allowed the following privileges to a role (MD): Manage Admin Users, Add Admin Users, Edit Admin Users, Change Admin Picture, Assign Admin Role, Manage Clients, Add Clients, Edit Clients, Change Clients Picture, Bulk Client Upload, Manage Agents, Add Agents, Edit Agents, Change Agents Picture, Manage BC Verification Types, Manage BC Education Types, Manage Status Types, Manage Recommendation Types, Role Management, Manage BC Reports, Add BC Reports, Edit BC Reports, Delete BC Reports, Print BC Reports, Download BC Reports, Manage CV Reports, Add CV Reports, Edit CV Reports, Delete CV Reports, Download CV Reports, Manage Bulk BC Reports, Manage Bulk CV Reports, Manage Clients Reports, Manage Cover Letters, Manage General Messages.', '2018-03-12 05:24:31'),
(25, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Assigned a role (MD) to  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 11:43:56'),
(26, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Unassigned a role from  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:06:27'),
(27, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Assigned a role (MD) to  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:11:24'),
(28, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Assigned a role (MD) to  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:12:40'),
(29, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Unassigned a role from  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:20:55'),
(30, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Assigned a role (MD) to  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:21:04'),
(31, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Unassigned a role from  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:21:26'),
(32, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Assigned a role (MD) to  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:21:38'),
(33, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Unassigned a role from  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:22:19'),
(34, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Unassigned a role from  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:22:27'),
(35, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Unassigned a role from  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:23:30'),
(36, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Assigned a role (MD) to  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:24:35'),
(37, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Unassigned a role from  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:45:27'),
(38, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Assigned a role (MD) to  Administrator (admin@riskcontrolnigeria.com).', '2018-03-12 12:45:38'),
(39, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Logged in to own account.', '2018-03-13 06:55:55'),
(40, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Added a new user with the email: kunle@yahoo.com.', '2018-03-13 07:23:09'),
(41, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Unassigned a role from  Kunle Olayiwola (kunle@yahoo.com).', '2018-03-13 07:36:48'),
(42, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Assigned a role (MD) to  Kunle Olayiwola (kunle@yahoo.com).', '2018-03-13 07:37:14'),
(43, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Logged in to own account.', '2018-03-13 14:42:08'),
(44, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Logged in to own account.', '2018-03-18 07:06:51'),
(45, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with subject: Bolatito Oluwadara, in batch 1.', '2018-03-18 10:17:14'),
(46, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 4 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-18 10:17:14'),
(47, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-18 11:03:12'),
(48, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-18 11:04:34'),
(49, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-18 11:05:29'),
(50, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-18 11:06:01'),
(51, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-18 11:15:18'),
(52, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-18 11:15:51'),
(53, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Added new 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Olawale Ayinla.', '2018-03-18 11:23:26'),
(54, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-18 12:43:48'),
(55, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-18 12:45:41'),
(56, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-18 12:46:43'),
(57, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-18 12:55:50'),
(58, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-18 12:59:21'),
(59, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with subject: Bolatito Oluwadara, in batch 1.', '2018-03-21 02:02:00'),
(60, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 4 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-21 02:02:00'),
(61, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated 1 BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-21 02:02:14'),
(62, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with subject: Bolatito Oluwadara, in batch 1.', '2018-03-21 02:02:22'),
(63, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-21 03:28:39'),
(64, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-21 03:29:35'),
(65, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-21 03:35:33'),
(66, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-21 03:36:06'),
(67, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Olawale Ayinla.', '2018-03-21 05:05:10'),
(68, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-21 08:49:03'),
(69, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Bolatito Oluwadara.', '2018-03-21 08:55:58'),
(70, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Olawale Ayinla.', '2018-03-21 09:09:45'),
(71, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-21 09:24:23'),
(72, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a BC verified information for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Olawale Ayinla.', '2018-03-21 10:05:09'),
(73, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Logged in to own account.', '2018-03-22 02:49:24'),
(74, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Ayinla Olalekan.', '2018-03-22 03:34:17'),
(75, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Ayinla Olalekan.', '2018-03-22 03:35:49'),
(76, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-22 07:52:35'),
(77, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-22 07:54:45'),
(78, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-22 07:55:07'),
(79, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-22 07:56:09'),
(80, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with names: Ibukun Oluwadarasimi, in batch 2.', '2018-03-22 07:57:35'),
(81, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated a CV report for Ishola Wasiu Ayobami (wasiuonline@gmail.com) with the subject: Ibukun Oluwadarasimi.', '2018-03-22 08:04:49'),
(82, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Updated the profile of user #4.', '2018-03-23 04:32:02'),
(83, '1', 'admin', 'admin@riskcontrolnigeria.com', 'Logged in to own account.', '2018-03-23 12:40:14');

-- --------------------------------------------------------

--
-- Table structure for table `bc_education_types`
--

CREATE TABLE `bc_education_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bc_education_types`
--

INSERT INTO `bc_education_types` (`id`, `type`) VALUES
(1, 'Primary'),
(2, 'Secondary'),
(3, 'Tertiary');

-- --------------------------------------------------------

--
-- Table structure for table `bc_reports`
--

CREATE TABLE `bc_reports` (
  `id` int(50) UNSIGNED NOT NULL,
  `client` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `batch` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `tat` date NOT NULL DEFAULT '0000-00-00',
  `status` varchar(50) NOT NULL DEFAULT 'PENDING',
  `recommendation` varchar(50) NOT NULL DEFAULT '',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bc_reports`
--

INSERT INTO `bc_reports` (`id`, `client`, `batch`, `subject`, `start_date`, `end_date`, `tat`, `status`, `recommendation`, `date_time`, `last_update`) VALUES
(1, 2, 1, 'Olawale Ayinla', '2018-01-02', '2018-03-25', '2018-01-22', 'PENDING', '', '2018-03-11 05:05:23', '2018-03-21 10:05:09'),
(2, 2, 1, 'Bolatito Oluwadara', '2018-01-01', '2018-05-31', '2018-02-10', 'PENDING', '', '2018-03-11 05:05:23', '2018-03-21 08:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `bc_reports_log`
--

CREATE TABLE `bc_reports_log` (
  `id` int(50) UNSIGNED NOT NULL,
  `reference_code` int(50) UNSIGNED NOT NULL DEFAULT '0',
  `client` varchar(100) NOT NULL DEFAULT '',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `verification_type` varchar(50) NOT NULL DEFAULT '',
  `batch` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(300) NOT NULL DEFAULT '',
  `updated_by` int(50) UNSIGNED NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bc_reports_log`
--

INSERT INTO `bc_reports_log` (`id`, `reference_code`, `client`, `subject`, `verification_type`, `batch`, `status`, `updated_by`, `date_time`) VALUES
(1, 2, '2', 'Bolatito Oluwadara', 'Age Verification', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),', 0, '2018-03-11 12:13:30'),
(2, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),', 0, '2018-03-11 12:13:30'),
(3, 2, '2', 'Bolatito Oluwadara', 'Neighbourhood', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),', 0, '2018-03-11 12:13:30'),
(4, 2, '2', 'Bolatito Oluwadara', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com),Sent to Agent on 01/03/2018,Received from Agent on 30/03/2018,COMPLETED,', 0, '2018-03-11 12:13:30'),
(5, 1, '2', 'Olawale Ayinla', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),', 0, '2018-03-11 12:14:38'),
(6, 1, '2', 'Olawale Ayinla', 'Employment Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),', 0, '2018-03-11 12:14:38'),
(7, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com),Sent to Agent on 01/03/2018,Received from Agent on 31/03/2018,COMPLETED,', 0, '2018-03-11 12:15:56'),
(8, 2, '2', 'Bolatito Oluwadara', 'Age Verification', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 01/01/2018,Sent to Agent on 18/03/2018,', 0, '2018-03-18 10:17:14'),
(9, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 05/01/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 18/03/2018,Sent to Agent on 01/03/2018,Received from Agent on 31/03/2018,COMPLETED,', 0, '2018-03-18 10:17:14'),
(10, 2, '2', 'Bolatito Oluwadara', 'Neighbourhood', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 05/02/2018,Sent to Agent on 18/03/2018,', 0, '2018-03-18 10:17:14'),
(11, 2, '2', 'Bolatito Oluwadara', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 01/03/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 18/03/2018,Sent to Agent on 01/03/2018,Received from Agent on 30/03/2018,COMPLETED,', 0, '2018-03-18 10:17:14'),
(12, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 05/01/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 18/03/2018,Sent to Agent on 01/03/2018,Received from Agent on 31/03/2018,COMPLETED,', 0, '2018-03-18 11:03:12'),
(13, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 18/03/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 18/03/2018,Sent to Agent on 01/03/2018,Received from Agent on 31/03/2018,COMPLETED,', 0, '2018-03-18 11:04:34'),
(14, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 05/01/2018,Sent to Agent on 18/03/2018,Received from Agent on 31/03/2018,COMPLETED,', 0, '2018-03-18 11:05:29'),
(15, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 05/01/2018,Sent to Agent on 18/03/2018,COMPLETED,', 0, '2018-03-18 11:06:01'),
(16, 2, '2', 'Bolatito Oluwadara', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 01/03/2018,COMPLETED,', 0, '2018-03-18 11:15:18'),
(17, 2, '2', 'Bolatito Oluwadara', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 01/03/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 18/03/2018,Sent to Agent on 18/03/2018,COMPLETED,', 0, '2018-03-18 11:15:51'),
(18, 1, '2', 'Olawale Ayinla', 'Reference checks', 1, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 20/03/2018,', 0, '2018-03-18 11:23:26'),
(19, 2, '2', 'Bolatito Oluwadara', 'Age Verification', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 01/01/2018,', 0, '2018-03-21 02:02:00'),
(20, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 05/01/2018,COMPLETED,', 0, '2018-03-21 02:02:00'),
(21, 2, '2', 'Bolatito Oluwadara', 'Neighbourhood', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 05/02/2018,', 0, '2018-03-21 02:02:00'),
(22, 2, '2', 'Bolatito Oluwadara', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 01/03/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 21/03/2018,Sent to Agent on 18/03/2018,COMPLETED,', 0, '2018-03-21 02:02:00'),
(23, 2, '2', 'Bolatito Oluwadara', 'NYSC Verification', 1, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 05/01/2018,COMPLETED,', 0, '2018-03-21 02:02:14'),
(24, 1, '2', 'Olawale Ayinla', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 02/01/2018,', 0, '2018-03-21 05:05:10'),
(25, 2, '2', 'Bolatito Oluwadara', 'Neighbourhood', 1, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 21/03/2018,', 0, '2018-03-21 08:49:03'),
(26, 2, '2', 'Bolatito Oluwadara', 'Age Verification', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 01/01/2018,', 1, '2018-03-21 08:55:58'),
(27, 1, '2', 'Olawale Ayinla', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 02/01/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 21/03/2018,Sent to Agent on 21/03/2018,', 1, '2018-03-21 09:09:45'),
(28, 1, '2', 'Olawale Ayinla', 'Identity Check', 1, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 02/01/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 21/03/2018,Sent to Agent on 21/03/2018,', 1, '2018-03-21 10:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `bc_sub_reports`
--

CREATE TABLE `bc_sub_reports` (
  `id` int(50) UNSIGNED NOT NULL,
  `bc_report_id` int(50) NOT NULL DEFAULT '0',
  `investigation_officer` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `assigned_agent` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `verification_type` varchar(50) NOT NULL DEFAULT '',
  `verification_order_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `education` varchar(50) NOT NULL DEFAULT '',
  `source` varchar(200) NOT NULL DEFAULT '',
  `comment` varchar(200) NOT NULL DEFAULT '',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `tat` date NOT NULL DEFAULT '0000-00-00',
  `date_sent_to_agent` date NOT NULL DEFAULT '0000-00-00',
  `date_received_from_agent` date NOT NULL DEFAULT '0000-00-00',
  `status` varchar(50) NOT NULL DEFAULT 'PENDING',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bc_sub_reports`
--

INSERT INTO `bc_sub_reports` (`id`, `bc_report_id`, `investigation_officer`, `assigned_agent`, `verification_type`, `verification_order_id`, `education`, `source`, `comment`, `start_date`, `end_date`, `tat`, `date_sent_to_agent`, `date_received_from_agent`, `status`, `date_time`, `last_update`) VALUES
(1, 1, 1, 3, 'Identity Check', 10, 'Tertiary', 'University of Lagos', 'Candidate&#039;s educational claim was verified as authentic from UNILAG database', '2018-01-02', '2018-02-01', '2018-01-22', '2018-03-21', '0000-00-00', 'PENDING', '2018-03-11 05:05:23', '2018-03-21 10:05:09'),
(2, 1, 1, 0, 'Employment Check', 2, 'Secondary', 'West African Examination Council', 'Candidate&#039;s educational claim was verified as authentic from WAEC database', '2018-01-02', '2018-03-25', '2018-03-21', '0000-00-00', '0000-00-00', 'PENDING', '2018-03-11 05:05:23', '2018-03-11 12:14:38'),
(3, 2, 1, 0, 'Age Verification', 7, 'Tertiary', 'University of Lagos', 'Candidate&#039;s educational claim was verified as authentic from UNILAG database', '2018-01-01', '2018-02-19', '2018-02-10', '0000-00-00', '0000-00-00', 'PENDING', '2018-03-11 05:05:23', '2018-03-21 08:55:58'),
(4, 2, 5, 0, 'NYSC Verification', 4, 'Primary', 'Canal Primary School', 'Candidate&#039;s educational claim was verified as authentic from Canal Primary School database', '2018-01-05', '2018-03-28', '2018-02-23', '0000-00-00', '0000-00-00', 'COMPLETED', '2018-03-11 05:05:23', '2018-03-21 02:02:14'),
(5, 2, 5, 0, 'Neighbourhood', 5, 'Secondary', 'West African Examination Council', 'Candidate&#039;s educational claim was verified as authentic from WAEC database', '2018-02-05', '2018-05-31', '2018-03-06', '0000-00-00', '0000-00-00', 'PENDING', '2018-03-11 05:05:23', '2018-03-21 08:49:03'),
(6, 2, 1, 3, 'Identity Check', 10, '', '', '', '2018-03-01', '2018-03-31', '2018-03-21', '2018-03-18', '0000-00-00', 'COMPLETED', '2018-03-11 05:07:33', '2018-03-21 02:02:00'),
(7, 1, 5, 0, 'Reference checks', 6, 'Primary', '', '', '2018-03-20', '0000-00-00', '2018-04-29', '0000-00-00', '0000-00-00', 'PENDING', '2018-03-18 11:23:26', '2018-03-18 11:23:26');

-- --------------------------------------------------------

--
-- Table structure for table `bc_verification_types`
--

CREATE TABLE `bc_verification_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `tat` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bc_verification_types`
--

INSERT INTO `bc_verification_types` (`id`, `type`, `order_id`, `tat`) VALUES
(1, 'Degree Verification', 1, 78),
(2, 'Employment Check', 2, 78),
(3, 'WAEC', 3, 40),
(4, 'NYSC Verification', 4, 49),
(5, 'Neighbourhood', 5, 29),
(6, 'Reference checks', 6, 40),
(7, 'Age Verification', 7, 40),
(8, 'Guarantor checks', 8, 40),
(9, 'Credit Checks', 9, 20),
(10, 'Identity Check', 10, 20),
(11, 'Professional Qualification Verification', 11, 40),
(12, 'Criminal Record Check', 12, 29);

-- --------------------------------------------------------

--
-- Table structure for table `clients_reports`
--

CREATE TABLE `clients_reports` (
  `id` int(20) UNSIGNED NOT NULL,
  `client` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cv_reports`
--

CREATE TABLE `cv_reports` (
  `id` int(50) UNSIGNED NOT NULL,
  `date_received` date NOT NULL DEFAULT '0000-00-00',
  `completion_date` date NOT NULL DEFAULT '0000-00-00',
  `tat` date NOT NULL DEFAULT '0000-00-00',
  `client` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `names` varchar(100) NOT NULL DEFAULT '',
  `institution` varchar(50) NOT NULL DEFAULT '',
  `course` varchar(50) NOT NULL DEFAULT '',
  `qualification` varchar(50) NOT NULL DEFAULT '',
  `grade` varchar(50) NOT NULL DEFAULT '',
  `session` varchar(20) NOT NULL DEFAULT '',
  `matric_number` varchar(50) NOT NULL DEFAULT '',
  `batch` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'PENDING',
  `verified_status` varchar(50) NOT NULL DEFAULT 'PENDING',
  `status_comment` varchar(200) NOT NULL DEFAULT '',
  `transaction_ref` varchar(50) NOT NULL DEFAULT '',
  `investigation_officer` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `assigned_agent` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `date_sent_out` date NOT NULL DEFAULT '0000-00-00',
  `date_received_from_school` date NOT NULL DEFAULT '0000-00-00',
  `school_letter_date` date NOT NULL DEFAULT '0000-00-00',
  `remark` varchar(250) NOT NULL DEFAULT '',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cv_reports`
--

INSERT INTO `cv_reports` (`id`, `date_received`, `completion_date`, `tat`, `client`, `names`, `institution`, `course`, `qualification`, `grade`, `session`, `matric_number`, `batch`, `status`, `verified_status`, `status_comment`, `transaction_ref`, `investigation_officer`, `assigned_agent`, `date_sent_out`, `date_received_from_school`, `school_letter_date`, `remark`, `date_time`, `last_update`) VALUES
(1, '2017-05-25', '2018-02-24', '2017-08-11', 2, 'Ayinla Olalekan', 'Lasgos State University', 'Marketing', 'B.Sc.', 'Ist Class', '2015/2016', 'SMBS/12/MKT/35/980', 2, 'COMPLETED', 'AUTHENTIC', '', '', 1, 3, '2018-03-13', '2018-03-23', '0000-00-00', '', '2018-03-11 05:05:43', '2018-03-22 03:35:49'),
(2, '2017-05-26', '0000-00-00', '0000-00-00', 2, 'Ibukun Oluwadarasimi', 'University of Ilorin', 'Insurance', 'B.Sc.', 'Second Class Upper', '2010/2011', '', 2, 'PENDING', 'AUTHENTIC', '', '6622162626', 1, 3, '2018-03-01', '2018-03-14', '0000-00-00', '', '2018-03-11 05:05:43', '2018-03-22 08:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `cv_reports_log`
--

CREATE TABLE `cv_reports_log` (
  `id` int(50) UNSIGNED NOT NULL,
  `reference_code` int(50) UNSIGNED NOT NULL DEFAULT '0',
  `client` varchar(100) NOT NULL DEFAULT '',
  `names` varchar(100) NOT NULL DEFAULT '',
  `institution` varchar(50) NOT NULL DEFAULT '',
  `batch` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `status` varchar(300) NOT NULL DEFAULT '',
  `updated_by` int(50) UNSIGNED NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cv_reports_log`
--

INSERT INTO `cv_reports_log` (`id`, `reference_code`, `client`, `names`, `institution`, `batch`, `status`, `updated_by`, `date_time`) VALUES
(1, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),COMPLETED,', 0, '2018-03-11 12:14:48'),
(2, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com),Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-11 12:15:26'),
(3, 1, '2', 'Ayinla Olalekan', 'Lasgos State University', 2, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com),Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com),Sent to Agent/School on 13/03/2018,Received from School on 23/03/2018,COMPLETED,', 0, '2018-03-11 13:36:32'),
(4, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 18/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-18 12:43:48'),
(5, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 18/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-18 12:45:41'),
(6, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 18/03/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 18/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-18 12:46:43'),
(7, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-18 12:55:50'),
(8, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-18 12:59:21'),
(9, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-21 03:28:39'),
(10, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-21 03:29:35'),
(11, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-21 03:35:33'),
(12, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 0, '2018-03-21 03:36:06'),
(13, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 1, '2018-03-21 09:24:23'),
(14, 1, '2', 'Ayinla Olalekan', 'Lasgos State University', 2, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 25/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 13/03/2018,Sent to Agent on 13/03/2018,Received from Agent on 23/03/2018,', 1, '2018-03-22 03:34:17'),
(15, 1, '2', 'Ayinla Olalekan', 'Lasgos State University', 2, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 25/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 13/03/2018,Sent to Agent on 13/03/2018,Received from Agent on 23/03/2018,COMPLETED,', 1, '2018-03-22 03:35:49'),
(16, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 1, '2018-03-22 07:52:35'),
(17, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 1, '2018-03-22 07:54:45'),
(18, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 31/03/2018,COMPLETED,', 1, '2018-03-22 07:55:07'),
(19, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,COMPLETED,', 1, '2018-03-22 07:56:09'),
(20, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Kunle Olayiwola (kunle@yahoo.com) on 26/05/2017,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from School on 14/03/2018,', 1, '2018-03-22 07:57:35'),
(21, 2, '2', 'Ibukun Oluwadarasimi', 'University of Ilorin', 2, 'Assigned to an Investigation Officer - Administrator (admin@riskcontrolnigeria.com) on 22/03/2018,Assigned to an Agent - Wasiu Olawale (wasionline@yahoo.com) on 01/03/2018,Sent to Agent/School on 01/03/2018,Received from Agent on 14/03/2018,', 1, '2018-03-22 08:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `recipient` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(300) NOT NULL DEFAULT '',
  `message` longtext NOT NULL,
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `recipient`, `subject`, `message`, `date_time`) VALUES
(1, '', 'Testing', '<p>Dear Ishola Wasiu Ayobami,</p><p>&lt;p&gt;This is a testing&lt;/p&gt;</p>', '2017-11-03 11:40:44'),
(2, '', 'Testing 2', '<p>Dear Ishola Wasiu Ayobami,</p><p>&lt;p&gt;This is a second testing.&lt;/p&gt;</p>', '2017-11-03 11:45:03'),
(3, '', 'And this', '<p>Dear All Active Depositors,</p><p>&lt;p&gt;This&amp;nbsp;This&amp;nbsp;&lt;strong&gt;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;Th&lt;/strong&gt;is&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;This&amp;nbsp;&lt;/p&gt;</p>', '2017-11-03 11:48:36');

-- --------------------------------------------------------

--
-- Table structure for table `recommendation_types`
--

CREATE TABLE `recommendation_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recommendation_types`
--

INSERT INTO `recommendation_types` (`id`, `type`) VALUES
(1, 'Proceed with caution'),
(2, 'No apparent risk'),
(3, 'Apparent risk');

-- --------------------------------------------------------

--
-- Table structure for table `reg_users`
--

CREATE TABLE `reg_users` (
  `id` int(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(250) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `designation` varchar(50) NOT NULL DEFAULT '',
  `telephone` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(50) NOT NULL DEFAULT '',
  `contact_person` varchar(50) NOT NULL DEFAULT '',
  `staff_id` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(200) NOT NULL DEFAULT '',
  `region` varchar(50) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `state_of_origin` varchar(50) NOT NULL DEFAULT '',
  `education` varchar(50) NOT NULL DEFAULT '',
  `confidentiality_agreement` varchar(10) NOT NULL DEFAULT 'No',
  `date_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `registered_by` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `logged_in` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `super_admin` tinyint(1) NOT NULL DEFAULT '0',
  `client` tinyint(1) NOT NULL DEFAULT '0',
  `agent` tinyint(1) NOT NULL DEFAULT '0',
  `school` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reg_users`
--

INSERT INTO `reg_users` (`id`, `name`, `username`, `password`, `email`, `designation`, `telephone`, `mobile`, `contact_person`, `staff_id`, `address`, `region`, `city`, `state`, `state_of_origin`, `education`, `confidentiality_agreement`, `date_registered`, `registered_by`, `logged_in`, `active`, `blocked`, `date_time`, `last_login`, `last_update`, `role_id`, `admin`, `super_admin`, `client`, `agent`, `school`) VALUES
(1, 'Administrator', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin@riskcontrolnigeria.com', 'Supervisor', '08088811560', '', '', '', '', '', '', '', '', '', '', '2013-10-01 19:37:15', 0, 1, 1, 0, '2018-03-23 12:40:14', '2018-03-13 07:56:41', '0000-00-00 00:00:00', 1, 1, 1, 0, 0, 0),
(2, 'Ishola Wasiu Ayobami', 'wasiu', '6e92d1437f787142118598e62c0a1c5e420fc7bc', 'wasiuonline@gmail.com', '', '', '', '', '', '7, Wosilat Aina Street', 'Orile Iganmu', 'Surulere', 'Lagos', '', '', '', '2017-09-30 13:08:34', 0, 0, 1, 0, '2018-03-05 10:47:18', '2018-03-05 10:59:03', '0000-00-00 00:00:00', 0, 0, 0, 1, 0, 0),
(3, 'Wasiu Olawale', 'olawale', '6e92d1437f787142118598e62c0a1c5e420fc7bc', 'wasionline@yahoo.com', '', '', '', '', '', '', '', '', '', '', '', 'No', '2017-10-22 16:41:27', 0, 0, 1, 0, '2018-02-28 18:40:11', '2018-03-01 07:42:19', '0000-00-00 00:00:00', 0, 0, 0, 0, 1, 0),
(4, 'Bola Bangbose', 'bola', '8cb2237d0679ca88db6464eac60da96345513964', 'bola@gmail.com', '', '8055668852', '8055668852', '', '', 'Plot 7, Wosilat Aina Street', 'Orile Iganmu', 'Surulere', 'Lagos', '', '', 'No', '2018-03-10 10:36:12', 1, 0, 1, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 1, 0, 0),
(5, 'Kunle Olayiwola', 'kunle', '4ff8fb1f17dfbecc1beed16425db88c5151820a1', 'kunle@yahoo.com', 'Manager', '08088811560', '', '', '', '', '', '', '', '', '', 'No', '2018-03-13 07:23:09', 1, 0, 1, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_title` varchar(50) NOT NULL DEFAULT '',
  `role_text` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_title`, `role_text`) VALUES
(1, 'manage_admin_users', 'Manage Admin Users'),
(2, 'add_admin_users', 'Add Admin Users'),
(3, 'edit_admin_users', 'Edit Admin Users'),
(4, 'change_admin_picture', 'Change Admin Picture'),
(5, 'assign_admin_role', 'Assign Admin Role'),
(6, 'manage_clients', 'Manage Clients'),
(7, 'add_clients', 'Add Clients'),
(8, 'edit_clients', 'Edit Clients'),
(9, 'change_clients_picture', 'Change Clients Picture'),
(10, 'bulk_client_upload', 'Bulk Client Upload'),
(11, 'manage_agents', 'Manage Agents'),
(12, 'add_agents', 'Add Agents'),
(13, 'edit_agents', 'Edit Agents'),
(14, 'change_agents_picture', 'Change Agents Picture'),
(15, 'manage_bc_verification_types', 'Manage BC Verification Types'),
(16, 'manage_bc_education_types', 'Manage BC Education Types'),
(17, 'manage_status_types', 'Manage Status Types'),
(18, 'manage_recommendation_types', 'Manage Recommendation Types'),
(19, 'role_management', 'Role Management'),
(20, 'manage_bc_reports', 'Manage BC Reports'),
(21, 'add_bc_reports', 'Add BC Reports'),
(22, 'edit_bc_reports', 'Edit BC Reports'),
(23, 'delete_bc_reports', 'Delete BC Reports'),
(24, 'print_bc_reports', 'Print BC Reports'),
(25, 'download_bc_reports', 'Download BC Reports'),
(26, 'manage_cv_reports', 'Manage CV Reports'),
(27, 'add_cv_reports', 'Add CV Reports'),
(28, 'edit_cv_reports', 'Edit CV Reports'),
(29, 'delete_cv_reports', 'Delete CV Reports'),
(30, 'download_cv_reports', 'Download CV Reports'),
(31, 'manage_bulk_bc_reports', 'Manage Bulk BC Reports'),
(32, 'manage_bulk_cv_reports', 'Manage Bulk CV Reports'),
(33, 'manage_clients_reports', 'Manage Clients Reports'),
(34, 'manage_cover_letters', 'Manage Cover Letters'),
(35, 'manage_general_messages', 'Manage General Messages');

-- --------------------------------------------------------

--
-- Table structure for table `role_management`
--

CREATE TABLE `role_management` (
  `id` int(10) UNSIGNED NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT '',
  `manage_admin_users` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_admin_users` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `edit_admin_users` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `change_admin_picture` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `assign_admin_role` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_clients` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_clients` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `edit_clients` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `change_clients_picture` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `bulk_client_upload` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_agents` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_agents` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `edit_agents` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `change_agents_picture` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_bc_verification_types` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_bc_education_types` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_status_types` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_recommendation_types` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `role_management` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_bc_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_bc_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `edit_bc_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `delete_bc_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `print_bc_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `download_bc_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_cv_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_cv_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `edit_cv_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `delete_cv_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `download_cv_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_bulk_bc_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_bulk_cv_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_clients_reports` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_cover_letters` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `manage_general_messages` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED DEFAULT '0',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role_management`
--

INSERT INTO `role_management` (`id`, `role`, `manage_admin_users`, `add_admin_users`, `edit_admin_users`, `change_admin_picture`, `assign_admin_role`, `manage_clients`, `add_clients`, `edit_clients`, `change_clients_picture`, `bulk_client_upload`, `manage_agents`, `add_agents`, `edit_agents`, `change_agents_picture`, `manage_bc_verification_types`, `manage_bc_education_types`, `manage_status_types`, `manage_recommendation_types`, `role_management`, `manage_bc_reports`, `add_bc_reports`, `edit_bc_reports`, `delete_bc_reports`, `print_bc_reports`, `download_bc_reports`, `manage_cv_reports`, `add_cv_reports`, `edit_cv_reports`, `delete_cv_reports`, `download_cv_reports`, `manage_bulk_bc_reports`, `manage_bulk_cv_reports`, `manage_clients_reports`, `manage_cover_letters`, `manage_general_messages`, `date_created`, `created_by`, `date_updated`, `updated_by`) VALUES
(1, 'MD', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2018-03-12 05:24:31', 1, '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `status_types`
--

CREATE TABLE `status_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status_types`
--

INSERT INTO `status_types` (`id`, `type`) VALUES
(1, 'COMPLETED'),
(2, 'PENDING'),
(3, 'AUTHENTIC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bc_education_types`
--
ALTER TABLE `bc_education_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bc_reports`
--
ALTER TABLE `bc_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bc_reports_log`
--
ALTER TABLE `bc_reports_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bc_sub_reports`
--
ALTER TABLE `bc_sub_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bc_verification_types`
--
ALTER TABLE `bc_verification_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients_reports`
--
ALTER TABLE `clients_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cv_reports`
--
ALTER TABLE `cv_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cv_reports_log`
--
ALTER TABLE `cv_reports_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recommendation_types`
--
ALTER TABLE `recommendation_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reg_users`
--
ALTER TABLE `reg_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_management`
--
ALTER TABLE `role_management`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status_types`
--
ALTER TABLE `status_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_messages`
--
ALTER TABLE `admin_messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;
--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;
--
-- AUTO_INCREMENT for table `bc_education_types`
--
ALTER TABLE `bc_education_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `bc_reports`
--
ALTER TABLE `bc_reports`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `bc_reports_log`
--
ALTER TABLE `bc_reports_log`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `bc_sub_reports`
--
ALTER TABLE `bc_sub_reports`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `bc_verification_types`
--
ALTER TABLE `bc_verification_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `clients_reports`
--
ALTER TABLE `clients_reports`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cv_reports`
--
ALTER TABLE `cv_reports`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `cv_reports_log`
--
ALTER TABLE `cv_reports_log`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `recommendation_types`
--
ALTER TABLE `recommendation_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `reg_users`
--
ALTER TABLE `reg_users`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT for table `role_management`
--
ALTER TABLE `role_management`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `status_types`
--
ALTER TABLE `status_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
