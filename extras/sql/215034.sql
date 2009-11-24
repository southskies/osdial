# 09/18/2009

ALTER TABLE osdial_campaign_hotkeys ADD xfer_exten VARCHAR(20) default '';##|##
 ##Adds ability to xfer on a hotkey;


UPDATE system_settings SET version='2.1.5.034',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.1.5.034 and clearing last_update_check flag.;
