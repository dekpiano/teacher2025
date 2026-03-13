-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Mar 13, 2026 at 02:36 AM
-- Server version: 10.6.25-MariaDB-ubu2204
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skjacth_personnel`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_teacher_evaluation_config`
--

CREATE TABLE `tb_teacher_evaluation_config` (
  `conf_id` int(11) NOT NULL,
  `conf_year` varchar(4) NOT NULL,
  `conf_round` int(1) NOT NULL,
  `conf_status` tinyint(1) DEFAULT 0,
  `conf_start_date` date DEFAULT NULL,
  `conf_end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `tb_teacher_evaluation_config`
--

INSERT INTO `tb_teacher_evaluation_config` (`conf_id`, `conf_year`, `conf_round`, `conf_status`, `conf_start_date`, `conf_end_date`) VALUES
(2, '2568', 1, 1, '2026-03-11', '2026-03-14'),
(3, '2569', 1, 1, '2026-03-10', '2026-03-15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_teacher_evaluation_config`
--
ALTER TABLE `tb_teacher_evaluation_config`
  ADD PRIMARY KEY (`conf_id`),
  ADD UNIQUE KEY `conf_year` (`conf_year`,`conf_round`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_teacher_evaluation_config`
--
ALTER TABLE `tb_teacher_evaluation_config`
  MODIFY `conf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
