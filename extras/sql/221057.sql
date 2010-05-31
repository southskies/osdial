# 05/13/2010

ALTER TABLE osdial_ivr MODIFY campaign_id VARCHAR(20);##|##
 ##    Increase campaign_id to 20 chars in osdial_ivr.;

UPDATE system_settings SET version='2.2.1.057',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.057 and clearing last_update_check flag.;
