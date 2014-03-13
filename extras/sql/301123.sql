# 03/13/2014

ALTER TABLE live_sip_channels ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

ALTER TABLE live_channels ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

ALTER TABLE inbound_numbers ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

ALTER TABLE live_inbound ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

ALTER TABLE live_inbound_log ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

ALTER TABLE osdial_postal_codes ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

ALTER TABLE osdial_phone_codes ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

ALTER TABLE system_settings ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

ALTER TABLE osdial_live_inbound_agents ADD COLUMN `id` INT(5) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;##|##
 ##Add primary key.;

UPDATE system_settings SET version='3.0.1.123',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.123 and clearing last_update_check flag.;
