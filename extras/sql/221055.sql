# 04/30/2010

ALTER TABLE osdial_hopper MODIFY status `status` enum('READY','QUEUE','INCALL','DONE','HOLD','API') DEFAULT 'READY';##|##
 ##    Added status for API insertion.;

UPDATE system_settings SET version='2.2.1.055',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.055 and clearing last_update_check flag.;
