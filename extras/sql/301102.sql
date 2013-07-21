# 07/20/2013

ALTER TABLE system_settings ADD COLUMN use_old_admin_auth enum('0','1') DEFAULT '0';##|##
 ## A setting that can enable session based authentication.;

UPDATE system_settings SET version='3.0.1.102',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.102 and clearing last_update_check flag.;
