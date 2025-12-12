CREATE TABLE `tb_send_research` (
  `seres_ID` int(5) NOT NULL AUTO_INCREMENT,
  `seres_research_name` varchar(255) NOT NULL COMMENT 'ชื่องานวิจัย',
  `seres_namesubject` varchar(255) NOT NULL COMMENT 'ชื่อรายวิชา',
  `seres_coursecode` varchar(10) NOT NULL COMMENT 'รหัสวิชา',
  `seres_gradelevel` varchar(2) NOT NULL COMMENT 'ระดับชั้น',
  `seres_sendcomment` text NOT NULL COMMENT 'รายละเอียดเพิ่มเติม',
  `seres_createdate` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่ส่ง',
  `seres_usersend` varchar(20) NOT NULL COMMENT 'ผู้ส่ง',
  `seres_learning` varchar(15) NOT NULL COMMENT 'กลุ่มสาระ',
  `seres_year` varchar(4) NOT NULL COMMENT 'ปีการศึกษา',
  `seres_term` varchar(1) NOT NULL COMMENT 'ภาคเรียน',
  `seres_file` text NOT NULL COMMENT 'ไฟล์งานวิจัย',
  `seres_status` varchar(30) NOT NULL DEFAULT 'ส่งแล้ว' COMMENT 'สถานะการส่ง',
  PRIMARY KEY (`seres_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;