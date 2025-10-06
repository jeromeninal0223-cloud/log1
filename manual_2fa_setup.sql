-- Manual 2FA column setup
-- Run this in your database management tool or MySQL command line
-- Database: logistics1_db on port 3307

USE logistics1_db;

-- Add 2FA columns if they don't exist
ALTER TABLE vendors 
ADD COLUMN IF NOT EXISTS two_factor_enabled BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS two_factor_secret VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS two_factor_backup_codes TEXT NULL,
ADD COLUMN IF NOT EXISTS two_factor_confirmed_at TIMESTAMP NULL;

-- Verify columns were added
DESCRIBE vendors;
