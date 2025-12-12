-- 1. Create the new table for plan types
CREATE TABLE `tb_send_plan_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  `type_description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='ตารางเก็บประเภทของเอกสารในระบบส่งแผนการสอน';

-- 2. Populate the new table with existing plan types
INSERT INTO `tb_send_plan_type` (`type_name`) VALUES
('บันทึกตรวจใช้แผน'),
('แบบตรวจแผนการจัดการเรียนรู้'),
('โครงการสอน'),
('แผนการสอนหน้าเดียว'),
('แผนการสอนเต็ม'),
('บันทึกหลังสอน');

-- 3. Add the new foreign key column to the existing plan submission table
ALTER TABLE `tb_send_plan` ADD `seplan_typeplan_id` INT NULL DEFAULT NULL AFTER `seplan_typeplan`;

