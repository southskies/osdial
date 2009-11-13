# 11/13/2009

UPDATE system_settings SET version='2.2.0.035';##|##
 ##Updating database to version 2.2.0.035;
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##Clearing last_update_check flag.;
