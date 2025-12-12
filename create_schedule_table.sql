USE skjacth_academic;
CREATE TABLE `tb_club_settings_schedule` (
  `schedule_id` INT(11) NOT NULL AUTO_INCREMENT,
  `club_id` INT(11) NOT NULL,
  `schedule_date` DATE NOT NULL,
  `schedule_title` VARCHAR(255) NOT NULL,
  `academic_year` VARCHAR(10) NOT NULL,
  `term` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`schedule_id`),
  FOREIGN KEY (`club_id`) REFERENCES `tb_clubs`(`club_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;