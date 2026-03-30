ALTER TABLE users
ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER must_change_password,
ADD COLUMN two_factor_secret VARCHAR(64) DEFAULT NULL AFTER two_factor_enabled;
