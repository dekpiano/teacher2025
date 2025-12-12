-- Add parent_id column to tb_evalu_desirable_item to support hierarchical structure

ALTER TABLE `tb_evalu_desirable_item`
ADD `parent_id` INT NOT NULL DEFAULT 0 AFTER `item_id`;

-- You might want to add an index for faster lookups
ALTER TABLE `tb_evalu_desirable_item`
ADD INDEX `parent_id` (`parent_id`);

-- Also, update the allowed fields in the model if you haven't already.
-- The DesirableAssessmentModel allowedFields should include 'parent_id'.
