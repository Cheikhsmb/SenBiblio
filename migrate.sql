-- Migration script for Library Management System
-- Run this in MySQL to add phone and address columns to students table

USE `library_senegal`;

ALTER TABLE `students`
    ADD COLUMN `phone` VARCHAR(20) NULL AFTER `email`,
    ADD COLUMN `address` VARCHAR(255) NULL AFTER `phone`;

-- Optional: If you want to rename the database (requires manual step):
-- RENAME DATABASE `library_senegal` TO `library_management`;
-- Then update DB_NAME in config.php to 'library_management'