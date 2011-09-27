# 07/04/2010

ALTER TABLE osdial_campaigns ADD use_cid_areacode_map ENUM('Y','N') default 'N';##|##
 ##    Campaign option to set the CID based on the dialed areacode.;

CREATE TABLE osdial_campaign_cid_areacodes (
  campaign_id VARCHAR(20) NOT NULL,
  areacode VARCHAR(4) NOT NULL,
  cid_number VARCHAR(10) NOT NULL,
  cid_name VARCHAR(40) NOT NULL,
  PRIMARY KEY (campaign_id,areacode)
) ENGINE=InnoDB;##|##
  ## Table to hold AreaCode to CID mappings.;


UPDATE system_settings SET version='2.2.1.064',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.064 and clearing last_update_check flag.;
