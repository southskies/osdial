# 08/29/2013

ALTER TABLE system_settings MODIFY multicompany_admin VARCHAR(255) default 'admin';##|##
 ## Increase multicompany_admin size to 255.;

UPDATE system_settings SET version='3.0.1.112',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.112 and clearing last_update_check flag.;
