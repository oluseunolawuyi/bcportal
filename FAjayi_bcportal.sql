-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 03, 2020 at 10:19 AM
-- Server version: 5.6.46
-- PHP Version: 7.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `FAjayi_bcportal`
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

-- --------------------------------------------------------

--
-- Table structure for table `all_clients_reports`
--

CREATE TABLE `all_clients_reports` (
  `id` int(20) UNSIGNED NOT NULL,
  `client` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `direction` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `candidate_name` varchar(200) NOT NULL DEFAULT '',
  `verified_info` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `bc_education_types`
--

CREATE TABLE `bc_education_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `source` longtext NOT NULL,
  `comment` longtext NOT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `tat` date NOT NULL DEFAULT '0000-00-00',
  `date_sent_to_agent` date NOT NULL DEFAULT '0000-00-00',
  `date_received_from_agent` date NOT NULL DEFAULT '0000-00-00',
  `status` varchar(50) NOT NULL DEFAULT 'PENDING',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `clients_reports`
--

CREATE TABLE `clients_reports` (
  `id` int(20) UNSIGNED NOT NULL,
  `client` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `direction` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cover_letters`
--

CREATE TABLE `cover_letters` (
  `id` int(11) UNSIGNED NOT NULL,
  `request_id` int(20) UNSIGNED NOT NULL DEFAULT '0',
  `cover_letter_type` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `client` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `completion_date` date NOT NULL DEFAULT '0000-00-00',
  `attention` varchar(100) NOT NULL DEFAULT '',
  `reference_no` varchar(250) NOT NULL DEFAULT '',
  `client_designation` varchar(250) NOT NULL DEFAULT '',
  `client_department` varchar(250) NOT NULL DEFAULT '',
  `re` varchar(250) NOT NULL DEFAULT '',
  `invoice_attachment` varchar(1000) NOT NULL DEFAULT '',
  `signatory` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `names` varchar(100) NOT NULL DEFAULT '',
  `school` varchar(100) NOT NULL DEFAULT '',
  `year` varchar(100) NOT NULL DEFAULT '',
  `qualification` varchar(100) NOT NULL DEFAULT '',
  `grade` varchar(100) NOT NULL DEFAULT '',
  `course` varchar(100) NOT NULL DEFAULT '',
  `transaction_ref` varchar(100) NOT NULL DEFAULT '',
  `comment` longtext NOT NULL,
  `report_source` longtext NOT NULL,
  `confirmation_type` varchar(100) NOT NULL DEFAULT '',
  `provided_by` varchar(100) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `institution` varchar(100) NOT NULL DEFAULT '',
  `centre` varchar(100) NOT NULL DEFAULT '',
  `candidate_number` varchar(100) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT '',
  `award_date` varchar(50) NOT NULL DEFAULT '',
  `batch_category` longtext NOT NULL,
  `details_category` longtext NOT NULL,
  `list_category` longtext NOT NULL,
  `course_category` longtext NOT NULL,
  `generated_by` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `date_generated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cover_letter_types`
--

CREATE TABLE `cover_letter_types` (
  `id` int(11) UNSIGNED NOT NULL,
  `cover_letter_type` varchar(50) NOT NULL DEFAULT ''
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

-- --------------------------------------------------------

--
-- Table structure for table `recommendation_types`
--

CREATE TABLE `recommendation_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `school` tinyint(1) NOT NULL DEFAULT '0',
  `signature` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `md` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_title` varchar(50) NOT NULL DEFAULT '',
  `role_text` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `admin_analysis` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED DEFAULT '0',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `status_types`
--

CREATE TABLE `status_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uploadable_items`
--

CREATE TABLE `uploadable_items` (
  `id` int(20) UNSIGNED NOT NULL,
  `item` varchar(100) NOT NULL DEFAULT '',
  `user_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `all_clients_reports`
--
ALTER TABLE `all_clients_reports`
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
-- Indexes for table `cover_letters`
--
ALTER TABLE `cover_letters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cover_letter_types`
--
ALTER TABLE `cover_letter_types`
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
-- Indexes for table `uploadable_items`
--
ALTER TABLE `uploadable_items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_messages`
--
ALTER TABLE `admin_messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `all_clients_reports`
--
ALTER TABLE `all_clients_reports`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bc_education_types`
--
ALTER TABLE `bc_education_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bc_reports`
--
ALTER TABLE `bc_reports`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bc_reports_log`
--
ALTER TABLE `bc_reports_log`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bc_sub_reports`
--
ALTER TABLE `bc_sub_reports`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bc_verification_types`
--
ALTER TABLE `bc_verification_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients_reports`
--
ALTER TABLE `clients_reports`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cover_letters`
--
ALTER TABLE `cover_letters`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cover_letter_types`
--
ALTER TABLE `cover_letter_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cv_reports`
--
ALTER TABLE `cv_reports`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cv_reports_log`
--
ALTER TABLE `cv_reports_log`
  MODIFY `id` int(50) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recommendation_types`
--
ALTER TABLE `recommendation_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reg_users`
--
ALTER TABLE `reg_users`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_management`
--
ALTER TABLE `role_management`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `status_types`
--
ALTER TABLE `status_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uploadable_items`
--
ALTER TABLE `uploadable_items`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
