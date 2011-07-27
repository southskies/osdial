# 07/27/2011

ALTER TABLE osdial_campaign_cid_areacodes MODIFY cid_number VARCHAR(20);##|##
 ##    Allow up to 20 digits in the cid2areacode mappings.;

UPDATE system_settings SET version='2.3.0.088',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.3.0.088 and clearing last_update_check flag.;
