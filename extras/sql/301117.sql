# 11/26/2013

ALTER TABLE osdial_campaigns MODIFY `campaign_cid` VARCHAR(20) DEFAULT '0000000000';##|##
 ## Change max CID length to 20.;

UPDATE system_settings SET version='3.0.1.117',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.117 and clearing last_update_check flag.;
