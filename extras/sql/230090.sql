# 09/20/2011

ALTER TABLE system_settings ADD COLUMN last_recording_extension VARCHAR(20) default '85100000';##|##
 ##    Moves the recording extension into the DB, so cluster systems can all be aware of the current extension.;

UPDATE system_settings SET version='2.3.0.090',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.3.0.090 and clearing last_update_check flag.;
