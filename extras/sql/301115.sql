# 11/19/2013

ALTER TABLE osdial_inbound_groups ADD COLUMN `prompt_language` varchar(100) DEFAULT '';##|##
 ## Add prompt language selection field.;

ALTER TABLE servers ADD COLUMN `asterisk_languages` varchar(255) DEFAULT 'en';##|##
 ## Add asterisk languages field.;

UPDATE system_settings SET version='3.0.1.115',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.115 and clearing last_update_check flag.;
