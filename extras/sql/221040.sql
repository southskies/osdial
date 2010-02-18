# 02/17/2010

ALTER TABLE osdial_campaigns ADD campaign_cid_name VARCHAR(40) default '';##|##
 ##    CID name for campaigns.;

ALTER TABLE phones ADD outbound_cid_name VARCHAR(40) default '';##|##
 ##    CID name for phones.;

ALTER TABLE osdial_campaigns ADD xfer_cid_mode ENUM('CAMPAIGN','PHONE','LEAD','LEAD_CUSTOM2') default 'CAMPAIGN';##|##
 ##    Option for selected CID for XFER/3rd-party calls.;

UPDATE system_settings SET version='2.2.1.040',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.040 and clearing last_update_check flag.;
