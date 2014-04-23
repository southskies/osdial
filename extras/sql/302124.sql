# 04/23/2014

ALTER TABLE osdial_list_fields MODIFY `value` TEXT DEFAULT NULL;##|##
 ## Changed AFF field values to allow more data.;

UPDATE system_settings SET version='3.0.2.124',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.2.124 and clearing last_update_check flag.;
