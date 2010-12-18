# 12/16/2010

ALTER TABLE osdial_ivr ADD allow_agent_extensions ENUM('Y','N') DEFAULT 'N';##|##
 ##    Adds server profile to servers table.;

UPDATE system_settings SET version='2.2.9.082',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.082 and clearing last_update_check flag.;
