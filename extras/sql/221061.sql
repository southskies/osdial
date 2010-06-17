# 06/16/2010

ALTER TABLE osdial_users ADD script_override VARCHAR(20) default '';##|##
 ##    Add override option for individual agents.;

UPDATE system_settings SET version='2.2.1.061',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.061 and clearing last_update_check flag.;
