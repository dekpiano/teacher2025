-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 10:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `tb_admin_rloes`
--

CREATE TABLE `tb_admin_rloes` (
  `admin_rloes_id` int(11) NOT NULL COMMENT 'รหัส',
  `admin_rloes_userid` varchar(20) NOT NULL COMMENT 'ชื่อ',
  `admin_rloes_nanetype` text NOT NULL COMMENT 'เป็นใครในะรบบ\r\n',
  `admin_rloes_status` varchar(20) NOT NULL COMMENT 'สถานะในระบบ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tb_admin_rloes`
--

INSERT INTO `tb_admin_rloes` (`admin_rloes_id`, `admin_rloes_userid`, `admin_rloes_nanetype`, `admin_rloes_status`) VALUES
(1, 'pers_060', 'ผู้บริหาร', 'manager'),
(2, 'pers_058', 'รองวิชาการ', 'manager'),
(3, 'pers_127', 'หัวหน้าวิชาการ', 'manager'),
(4, 'pers_049', 'งานทะเบียน|งานวัดและประเมินผล', 'admin'),
(5, 'pers_015', 'งานทะเบียน|งานวัดและประเมินผล', 'admin'),
(6, 'pers_051', 'งานทะเบียน|งานวัดและประเมินผล|งานหลักสูตร', 'admin'),
(7, 'pers_050', 'งานทะเบียน|งานวัดและประเมินผล', 'admin'),
(9, 'pers_021', 'งานทะเบียน|งานหลักสูตร|งานวัดและประเมินผล|งานกิจกรรมพัฒนาผู้เรียน', 'admin'),
(10, 'pers_073', 'งานทะเบียน-วัดผล|งานหลักสูตร|งานกิจกรรมพัฒนาผู้เรียน', 'admin'),
(12, 'pers_130', 'งานทะเบียน|งานวัดและประเมินผล', 'admin'),
(14, 'pers_140', 'งานทะเบียน|งานวัดและประเมินผล', 'admin'),
(15, 'pers_139', 'งานทะเบียน-วัดผล|งานหลักสูตร', 'admin'),
(16, 'pers_086', '', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin_rloes`
--
ALTER TABLE `tb_admin_rloes`
  ADD PRIMARY KEY (`admin_rloes_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_admin_rloes`
--
ALTER TABLE `tb_admin_rloes`
  MODIFY `admin_rloes_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัส', AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
