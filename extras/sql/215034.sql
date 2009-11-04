# 09/18/2009

ALTER TABLE osdial_campaign_hotkeys ADD xfer_exten VARCHAR(20) default '';##|##
 ##Adds ability to xfer on a hotkey;


UPDATE system_settings SET version='2.1.5.034';##|##
 ##Updating database to version 2.1.5.034;
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##Clearing last_update_check flag.;
