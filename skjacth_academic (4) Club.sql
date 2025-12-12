-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 10, 2025 at 11:08 AM
-- Server version: 10.6.22-MariaDB-cll-lve
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skjacth_academic`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_clubs`
--

CREATE TABLE `tb_clubs` (
  `club_id` int(11) NOT NULL COMMENT 'รหัสชุมนุม ',
  `club_name` varchar(100) NOT NULL COMMENT 'ชื่อชุมนุม',
  `club_description` text DEFAULT NULL COMMENT 'รายละเอียดชุมนุม',
  `club_faculty_advisor` varchar(100) DEFAULT NULL COMMENT 'อาจารย์ที่ปรึกษา',
  `club_established_date` date DEFAULT NULL COMMENT 'วันที่สร้างชุมนุม',
  `club_year` varchar(4) NOT NULL COMMENT 'ปีการศึกษา',
  `club_trem` varchar(1) NOT NULL COMMENT 'เทอมที่',
  `club_max_participants` int(11) NOT NULL COMMENT 'จำนวนผู้เข้าร่วมสูงสุด',
  `club_status` enum('open','close') DEFAULT 'open' COMMENT 'เปิดปิดชุมนุม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `tb_clubs`
--

INSERT INTO `tb_clubs` (`club_id`, `club_name`, `club_description`, `club_faculty_advisor`, `club_established_date`, `club_year`, `club_trem`, `club_max_participants`, `club_status`) VALUES
(6, 'skj', 'dgdfg', 'pers_006|pers_007|pers_008', '2024-11-18', '2567', '1', 10, 'open'),
(7, 'คอมพิวเตอร์', 'ertert', 'pers_004|pers_036', '2024-11-18', '2567', '2', 20, 'open'),
(9, 'คอมพิวเตอร์', 'เล่นเกมส์', 'pers_021', '2024-11-21', '2567', '1', 10, 'open'),
(10, 'คอมพิวเตอร์22', 'aasd', 'pers_006|pers_008', '2024-11-21', '2567', '1', 23, 'open'),
(11, 'ภาษาไทย', 'ทำงาน', 'pers_004', '2024-11-22', '2567', '1', 10, 'open');

-- --------------------------------------------------------

--
-- Table structure for table `tb_club_activities`
--

CREATE TABLE `tb_club_activities` (
  `act_id` int(11) NOT NULL COMMENT 'รหัสกิจกรรม',
  `act_name` varchar(100) NOT NULL COMMENT 'ชื่อกิจกรรม',
  `act_date` date NOT NULL COMMENT 'วันที่จัดกิจกรรม',
  `act_location` varchar(100) DEFAULT NULL COMMENT 'สถานที่จัดกิจกรรม',
  `act_description` text DEFAULT NULL COMMENT 'รายละเอียดกิจกรรม',
  `act_club_id` int(11) DEFAULT NULL COMMENT 'รหัสชุมนุม',
  `act_start_time` time DEFAULT NULL COMMENT 'เวลาเริ่มกิจกรรม',
  `act_end_time` time DEFAULT NULL COMMENT 'เวลาสิ้นสุดกิจกรรม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_club_members`
--

CREATE TABLE `tb_club_members` (
  `member_id` int(11) NOT NULL COMMENT 'รหัสสมาชิก',
  `member_student_id` int(11) DEFAULT NULL COMMENT 'รหัสนักเรียน',
  `member_club_id` int(11) DEFAULT NULL COMMENT 'รหัสชุมนุม',
  `member_join_date` date DEFAULT NULL COMMENT 'วันที่เข้าชุมนุม',
  `member_role` enum('Member','Leader') DEFAULT 'Member' COMMENT 'บทบาทในชุมนุม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `tb_club_members`
--

INSERT INTO `tb_club_members` (`member_id`, `member_student_id`, `member_club_id`, `member_join_date`, `member_role`) VALUES
(6, 2576, 9, '2024-11-21', 'Member'),
(7, 2622, 9, '2024-11-21', 'Member'),
(8, 2631, 7, '2024-11-21', 'Member'),
(9, 2660, 9, '2024-11-21', 'Member'),
(11, 1909, 10, '2024-11-21', 'Member'),
(12, 2093, 6, '2024-11-21', 'Member'),
(13, 2870, 6, '2024-11-21', 'Member'),
(14, 2894, 6, '2024-11-21', 'Member'),
(15, 3706, 9, '2024-11-21', 'Member'),
(16, 3691, 9, '2024-11-21', 'Member'),
(17, 3996, 11, '2024-11-22', 'Member'),
(18, 3998, 11, '2024-11-22', 'Member'),
(19, 4013, 11, '2024-11-22', 'Member'),
(20, 3489, 11, '2024-11-22', 'Member'),
(21, 2775, 11, '2024-11-22', 'Member'),
(22, 3691, 6, '2024-11-23', 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `tb_club_onoff`
--

CREATE TABLE `tb_club_onoff` (
  `c_onoff_id` int(11) NOT NULL,
  `c_onoff_year` varchar(9) NOT NULL,
  `c_onoff_term` int(1) NOT NULL,
  `c_onoff_regisstart` datetime NOT NULL,
  `c_onoff_regisend` datetime NOT NULL,
  `c_onoff_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `tb_club_onoff`
--

INSERT INTO `tb_club_onoff` (`c_onoff_id`, `c_onoff_year`, `c_onoff_regisstart`, `c_onoff_regisend`, `c_onoff_created_at`) VALUES
(1, '2567/2', '2024-11-23 15:00:00', '2024-12-28 15:00:00', '2024-11-23 04:43:22');

-- --------------------------------------------------------

--
-- Table structure for table `tb_club_recoed_activity`
--

CREATE TABLE `tb_club_recoed_activity` (
  `tcra_id` int(11) NOT NULL COMMENT 'รหัสผู้เข้าร่วม',
  `tcra_club_id` int(11) DEFAULT NULL COMMENT 'รหัสชุมนุม',
  `tcra_teac_id` varchar(10) NOT NULL COMMENT 'ครูประเมิน',
  `trca_schedule_id` int(5) NOT NULL,
  `tcra_ma` text NOT NULL COMMENT 'มา',
  `tcra_khad` text NOT NULL COMMENT 'ขาด',
  `tcra_rapwy` text NOT NULL COMMENT 'ลาป่วย',
  `tcra_rakic` text NOT NULL COMMENT 'ลากิจ',
  `tcra_kickrrm` text NOT NULL COMMENT 'กิจกรรม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_club_settings_schedule`
--

CREATE TABLE `tb_club_settings_schedule` (
  `tcs_schedule_id` int(11) NOT NULL COMMENT 'รหัสการกำหนด',
  `tcs_academic_year` varchar(9) NOT NULL COMMENT 'ปีการศึกษา ',
  `tcs_academic_trem` int(1) NOT NULL DEFAULT 1,
  `tcs_start_date` date NOT NULL COMMENT 'วันที่เริ่มต้นเรียน',
  `tcs_week_number` int(11) NOT NULL COMMENT 'สัปดาห์ที่',
  `tcs_week_status` enum('เปิด','ปิด') NOT NULL COMMENT 'สถานะเปิดปิด',
  `tcs_created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `tcs_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไข'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `tb_club_settings_schedule`
--

INSERT INTO `tb_club_settings_schedule` (`tcs_schedule_id`, `tcs_academic_year`, `tcs_start_date`, `tcs_week_number`, `tcs_week_status`, `tcs_created_at`, `tcs_updated_at`) VALUES
(1, '2567/2', '2024-11-24', 1, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:43:17'),
(2, '2567/2', '2024-11-25', 2, 'ปิด', '2024-11-24 06:18:06', '2024-11-24 06:44:09'),
(3, '2567/2', '2024-11-28', 3, 'ปิด', '2024-11-24 06:18:06', '2024-11-24 06:44:14'),
(4, '2567/2', '0000-00-00', 4, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(5, '2567/2', '0000-00-00', 5, 'ปิด', '2024-11-24 06:18:06', '2024-11-24 06:43:27'),
(6, '2567/2', '0000-00-00', 6, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(7, '2567/2', '0000-00-00', 7, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(8, '2567/2', '0000-00-00', 8, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(9, '2567/2', '0000-00-00', 9, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(10, '2567/2', '0000-00-00', 10, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(11, '2567/2', '0000-00-00', 11, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(12, '2567/2', '0000-00-00', 12, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(13, '2567/2', '0000-00-00', 13, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(14, '2567/2', '0000-00-00', 14, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(15, '2567/2', '0000-00-00', 15, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(16, '2567/2', '0000-00-00', 16, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(17, '2567/2', '0000-00-00', 17, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(18, '2567/2', '0000-00-00', 18, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(19, '2567/2', '0000-00-00', 19, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(20, '2567/2', '0000-00-00', 20, 'เปิด', '2024-11-24 06:18:06', '2024-11-24 06:18:06'),
(21, '2567/1', '0000-00-00', 1, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(22, '2567/1', '0000-00-00', 2, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(23, '2567/1', '0000-00-00', 3, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(24, '2567/1', '0000-00-00', 4, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(25, '2567/1', '0000-00-00', 5, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(26, '2567/1', '0000-00-00', 6, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(27, '2567/1', '0000-00-00', 7, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(28, '2567/1', '0000-00-00', 8, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(29, '2567/1', '0000-00-00', 9, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(30, '2567/1', '0000-00-00', 10, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(31, '2567/1', '0000-00-00', 11, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(32, '2567/1', '0000-00-00', 12, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(33, '2567/1', '0000-00-00', 13, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(34, '2567/1', '0000-00-00', 14, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(35, '2567/1', '0000-00-00', 15, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(36, '2567/1', '0000-00-00', 16, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(37, '2567/1', '0000-00-00', 17, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(38, '2567/1', '0000-00-00', 18, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(39, '2567/1', '0000-00-00', 19, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18'),
(40, '2567/1', '0000-00-00', 20, 'เปิด', '2024-11-24 11:02:18', '2024-11-24 11:02:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_clubs`
--
ALTER TABLE `tb_clubs`
  ADD PRIMARY KEY (`club_id`);

--
-- Indexes for table `tb_club_activities`
--
ALTER TABLE `tb_club_activities`
  ADD PRIMARY KEY (`act_id`),
  ADD KEY `act_club_id` (`act_club_id`);

--
-- Indexes for table `tb_club_members`
--
ALTER TABLE `tb_club_members`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `tb_club_onoff`
--
ALTER TABLE `tb_club_onoff`
  ADD PRIMARY KEY (`c_onoff_id`);

--
-- Indexes for table `tb_club_recoed_activity`
--
ALTER TABLE `tb_club_recoed_activity`
  ADD PRIMARY KEY (`tcra_id`);

--
-- Indexes for table `tb_club_settings_schedule`
--
ALTER TABLE `tb_club_settings_schedule`
  ADD PRIMARY KEY (`tcs_schedule_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_clubs`
--
ALTER TABLE `tb_clubs`
  MODIFY `club_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสชุมนุม ', AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_club_activities`
--
ALTER TABLE `tb_club_activities`
  MODIFY `act_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสกิจกรรม';

--
-- AUTO_INCREMENT for table `tb_club_members`
--
ALTER TABLE `tb_club_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสสมาชิก', AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tb_club_onoff`
--
ALTER TABLE `tb_club_onoff`
  MODIFY `c_onoff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_club_recoed_activity`
--
ALTER TABLE `tb_club_recoed_activity`
  MODIFY `tcra_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสผู้เข้าร่วม';

--
-- AUTO_INCREMENT for table `tb_club_settings_schedule`
--
ALTER TABLE `tb_club_settings_schedule`
  MODIFY `tcs_schedule_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการกำหนด', AUTO_INCREMENT=41;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
