# 2010/03/01

ALTER TABLE osdial_agent_log MODIFY campaign_id VARCHAR(20);##|##
 ##    Field length or NULL correction. This may take a while.;

UPDATE system_settings SET version='2.2.1.045',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.045 and clearing last_update_check flag.;