--
-- Table structure for table `tb_evalu_desirable_item`
--

CREATE TABLE `tb_evalu_desirable_item` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` text DEFAULT NULL,
  `item_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for table `tb_evalu_desirable_item`
--
ALTER TABLE `tb_evalu_desirable_item`
  ADD PRIMARY KEY (`item_id`);

--
-- AUTO_INCREMENT for table `tb_evalu_desirable_item`
--
ALTER TABLE `tb_evalu_desirable_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Table structure for table `tb_evalu_desirable_detail`
--

CREATE TABLE `tb_evalu_desirable_detail` (
  `detail_id` bigint(20) NOT NULL,
  `student_id` varchar(17) NOT NULL,
  `item_id` int(11) NOT NULL,
  `score` tinyint(4) NOT NULL,
  `term` varchar(1) NOT NULL,
  `academic_year` varchar(4) NOT NULL,
  `evaluator_id` varchar(20) NOT NULL,
  `evaluated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for table `tb_evalu_desirable_detail`
--
ALTER TABLE `tb_evalu_desirable_detail`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `academic_year_term` (`academic_year`,`term`);

--
-- AUTO_INCREMENT for table `tb_evalu_desirable_detail`
--
ALTER TABLE `tb_evalu_desirable_detail`
  MODIFY `detail_id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;
