# 06/10/2010

ALTER TABLE osdial_script_button_log MODIFY script_id VARCHAR(40);##|##
 ##    Increase script_id to 40 chars in osdial_script_button_log.;

UPDATE system_settings SET version='2.2.1.059',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.059 and clearing last_update_check flag.;
