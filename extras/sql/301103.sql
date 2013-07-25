# 07/24/2013

ALTER TABLE system_settings ADD COLUMN default_ext_context varchar(20) DEFAULT 'osdialEXT';##|##
 ## Default ext_context for system.;

UPDATE system_settings SET version='3.0.1.103',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.103 and clearing last_update_check flag.;
