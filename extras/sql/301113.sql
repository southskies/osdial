# 10/04/2013

ALTER TABLE phones MODIFY voicemail_id VARCHAR(20);##|##
 ## Increase voicemail_id size to 20.;

UPDATE system_settings SET version='3.0.1.113',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.113 and clearing last_update_check flag.;
