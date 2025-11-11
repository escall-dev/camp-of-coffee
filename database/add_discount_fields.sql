-- Add discount fields to sales table
ALTER TABLE `sales` 
ADD COLUMN `discount_type` VARCHAR(50) DEFAULT NULL AFTER `total_amount`,
ADD COLUMN `discount_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `discount_type`;
