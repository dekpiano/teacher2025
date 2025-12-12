-- This script updates the `tb_send_plan` table to populate the new `seplan_typeplan_id` column.
-- It matches the old text-based plan type (`seplan_typeplan`) with the corresponding ID
-- from the new `tb_send_plan_type` table.

UPDATE `tb_send_plan`
JOIN `tb_send_plan_type` ON `tb_send_plan`.`seplan_typeplan` = `tb_send_plan_type`.`type_name`
SET `tb_send_plan`.`seplan_typeplan_id` = `tb_send_plan_type`.`type_id`
WHERE `tb_send_plan`.`seplan_typeplan_id` IS NULL;
