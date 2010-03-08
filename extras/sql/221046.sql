# 2010/03/01


ALTER TABLE osdial_user_log MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

UPDATE system_settings SET version='2.2.1.046',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.046 and clearing last_update_check flag.;
