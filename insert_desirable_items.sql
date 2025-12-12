-- This script populates the `tb_evalu_desirable_item` table with the 8 desirable characteristics and their sub-items.
-- Sub-item names are placeholders as they are not available in the provided image.

-- To prevent errors on re-running, you might want to clear the table first.
-- DELETE FROM `tb_evalu_desirable_item`;
-- ALTER TABLE `tb_evalu_desirable_item` AUTO_INCREMENT = 1;

-- 1. Main Items
INSERT INTO `tb_evalu_desirable_item` (`item_id`, `parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(1, 0, 'รักชาติ ศาสน์ กษัตริย์', 1, 1),
(2, 0, 'ซื่อสัตย์สุจริต', 2, 1),
(3, 0, 'มีวินัย', 3, 1),
(4, 0, 'ใฝ่เรียนรู้', 4, 1),
(5, 0, 'อยู่อย่างพอเพียง', 5, 1),
(6, 0, 'มุ่งมั่นในการทำงาน', 6, 1),
(7, 0, 'รักความเป็นไทย', 7, 1),
(8, 0, 'มีจิตสาธารณะ', 8, 1);

-- 2. Sub-Items

-- Sub-items for 'รักชาติ ศาสน์ กษัตริย์' (parent_id = 1)
INSERT INTO `tb_evalu_desirable_item` (`parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(1, 'ตัวชี้วัดที่ 1.1', 1, 1),
(1, 'ตัวชี้วัดที่ 1.2', 2, 1),
(1, 'ตัวชี้วัดที่ 1.3', 3, 1),
(1, 'ตัวชี้วัดที่ 1.4', 4, 1);

-- Sub-items for 'ซื่อสัตย์สุจริต' (parent_id = 2)
INSERT INTO `tb_evalu_desirable_item` (`parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(2, 'ตัวชี้วัดที่ 2.1', 1, 1),
(2, 'ตัวชี้วัดที่ 2.2', 2, 1);

-- Sub-items for 'มีวินัย' (parent_id = 3)
INSERT INTO `tb_evalu_desirable_item` (`parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(3, 'ตัวชี้วัดที่ 3.1', 1, 1);

-- Sub-items for 'ใฝ่เรียนรู้' (parent_id = 4)
INSERT INTO `tb_evalu_desirable_item` (`parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(4, 'ตัวชี้วัดที่ 4.1', 1, 1),
(4, 'ตัวชี้วัดที่ 4.2', 2, 1);

-- Sub-items for 'อยู่อย่างพอเพียง' (parent_id = 5)
INSERT INTO `tb_evalu_desirable_item` (`parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(5, 'ตัวชี้วัดที่ 5.1', 1, 1),
(5, 'ตัวชี้วัดที่ 5.2', 2, 1);

-- Sub-items for 'มุ่งมั่นในการทำงาน' (parent_id = 6)
INSERT INTO `tb_evalu_desirable_item` (`parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(6, 'ตัวชี้วัดที่ 6.1', 1, 1),
(6, 'ตัวชี้วัดที่ 6.2', 2, 1);

-- Sub-items for 'รักความเป็นไทย' (parent_id = 7)
INSERT INTO `tb_evalu_desirable_item` (`parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(7, 'ตัวชี้วัดที่ 7.1', 1, 1),
(7, 'ตัวชี้วัดที่ 7.2', 2, 1),
(7, 'ตัวชี้วัดที่ 7.3', 3, 1);

-- Sub-items for 'มีจิตสาธารณะ' (parent_id = 8)
INSERT INTO `tb_evalu_desirable_item` (`parent_id`, `item_name`, `item_order`, `is_active`) VALUES
(8, 'ตัวชี้วัดที่ 8.1', 1, 1),
(8, 'ตัวชี้วัดที่ 8.2', 2, 1);

COMMIT;
