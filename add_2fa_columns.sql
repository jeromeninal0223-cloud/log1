-- Add 2FA columns to vendors table
-- Execute this in your MySQL database

USE log1;

-- Check current table structure
DESCRIBE vendors;

-- Add 2FA columns (will fail gracefully if columns already exist)
ALTER TABLE vendors 
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE,
ADD COLUMN two_factor_secret VARCHAR(255) NULL,
ADD COLUMN two_factor_backup_codes TEXT NULL,
ADD COLUMN two_factor_confirmed_at TIMESTAMP NULL;

-- Verify columns were added
DESCRIBE vendors;
