CREATE TABLE `tb_send_research_setup` (
  `seres_setup_ID` int(11) NOT NULL AUTO_INCREMENT,
  `seres_setup_startdate` datetime NOT NULL COMMENT 'วันที่เริ่มต้นการส่ง',
  `seres_setup_enddate` datetime NOT NULL COMMENT 'วันที่สิ้นสุดการส่ง',
  `seres_setup_year` varchar(4) NOT NULL COMMENT 'ปีการศึกษาที่เปิดรับ',
  `seres_setup_term` varchar(1) NOT NULL COMMENT 'ภาคเรียนที่เปิดรับ',
  `seres_setup_status` enum('on','off') NOT NULL DEFAULT 'off' COMMENT 'สถานะเปิด/ปิดการส่ง',
  `seres_setup_usersetup` varchar(20) DEFAULT NULL COMMENT 'ผู้ที่ตั้งค่าล่าสุด',
  PRIMARY KEY (`seres_setup_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;