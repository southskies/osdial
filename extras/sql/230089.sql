# 08/18/2011

ALTER TABLE osdial_campaigns ADD COLUMN allow_md_hopperlist ENUM('Y','N') default 'N';##|##
 ##    Allow agent to access hopperlist when in MANUAL mode.;

UPDATE system_settings SET version='2.3.0.089',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.3.0.089 and clearing last_update_check flag.;
