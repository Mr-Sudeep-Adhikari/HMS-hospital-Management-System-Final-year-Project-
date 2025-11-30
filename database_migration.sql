-- =============================================================================
-- DATABASE MIGRATION SCRIPT FOR HMS
-- =============================================================================
-- Purpose: Add missing PRIMARY KEYs and indexes for better data integrity
-- Run this script ONCE on your database

USE myhmsdb;

-- =============================================================================
-- 1. ADD PRIMARY KEY TO doctb TABLE
-- =============================================================================
-- Check if id column already exists
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'myhmsdb' 
  AND TABLE_NAME = 'doctb' 
  AND COLUMN_NAME = 'id';

-- Add id column if it doesn't exist
SET @query = IF(@col_exists = 0,
    'ALTER TABLE `doctb` ADD COLUMN `id` INT AUTO_INCREMENT PRIMARY KEY FIRST',
    'SELECT "Column id already exists in doctb" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================================================
-- 2. ADD PRIMARY KEY TO prestb TABLE
-- =============================================================================
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'myhmsdb' 
  AND TABLE_NAME = 'prestb' 
  AND COLUMN_NAME = 'id';

SET @query = IF(@col_exists = 0,
    'ALTER TABLE `prestb` ADD COLUMN `id` INT AUTO_INCREMENT PRIMARY KEY FIRST',
    'SELECT "Column id already exists in prestb" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================================================
-- 3. ADD UNIQUE INDEXES FOR BETTER PERFORMANCE
-- =============================================================================

-- Add unique index on doctb.username (if not exists)
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'myhmsdb'
  AND TABLE_NAME = 'doctb'
  AND INDEX_NAME = 'idx_username';

SET @query = IF(@index_exists = 0,
    'ALTER TABLE `doctb` ADD UNIQUE INDEX `idx_username` (`username`)',
    'SELECT "Index idx_username already exists on doctb" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add unique index on patreg.email (if not exists)
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'myhmsdb'
  AND TABLE_NAME = 'patreg'
  AND INDEX_NAME = 'idx_email';

SET @query = IF(@index_exists = 0,
    'ALTER TABLE `patreg` ADD UNIQUE INDEX `idx_email` (`email`)',
    'SELECT "Index idx_email already exists on patreg" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add unique index on admintb.username (if not exists)
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'myhmsdb'
  AND TABLE_NAME = 'admintb'
  AND INDEX_NAME = 'idx_username';

SET @query = IF(@index_exists = 0,
    'ALTER TABLE `admintb` ADD UNIQUE INDEX `idx_username` (`username`)',
    'SELECT "Index idx_username already exists on admintb" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================================================
-- 4. ADD INDEXES ON FOREIGN KEY COLUMNS FOR PERFORMANCE
-- =============================================================================

-- Index on appointmenttb.doctor
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'myhmsdb'
  AND TABLE_NAME = 'appointmenttb'
  AND INDEX_NAME = 'idx_doctor';

SET @query = IF(@index_exists = 0,
    'ALTER TABLE `appointmenttb` ADD INDEX `idx_doctor` (`doctor`)',
    'SELECT "Index idx_doctor already exists on appointmenttb" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index on appointmenttb.pid
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'myhmsdb'
  AND TABLE_NAME = 'appointmenttb'
  AND INDEX_NAME = 'idx_pid';

SET @query = IF(@index_exists = 0,
    'ALTER TABLE `appointmenttb` ADD INDEX `idx_pid` (`pid`)',
    'SELECT "Index idx_pid already exists on appointmenttb" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================================================
-- VERIFICATION QUERIES
-- =============================================================================
SELECT 'Migration completed successfully!' AS status;

-- Show table structures
SHOW CREATE TABLE doctb;
SHOW CREATE TABLE prestb;
SHOW CREATE TABLE patreg;
SHOW CREATE TABLE admintb;
SHOW CREATE TABLE appointmenttb;
