-- This script fixes the collation error from the previous step.
-- It explicitly sets the collation for the join condition to prevent the "Illegal mix of collations" error.

UPDATE `tb_send_plan`
JOIN `tb_send_plan_type` ON `tb_send_plan`.`seplan_typeplan` COLLATE utf8_general_ci = `tb_send_plan_type`.`type_name`
SET `tb_send_plan`.`seplan_typeplan_id` = `tb_send_plan_type`.`type_id`
WHERE `tb_send_plan`.`seplan_typeplan_id` IS NULL;
