# 2010/03/01


ALTER TABLE osdial_log MODIFY campaign_id VARCHAR(20), MODIFY server_ip VARCHAR(15);##|##
 ##    Field length or NULL correction for campaign_id and server_ip, this may take a while;

UPDATE system_settings SET version='2.2.1.047',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.047 and clearing last_update_check flag.;
