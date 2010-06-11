# 06/11/2010

ALTER TABLE osdial_campaigns MODIFY xfer_cid_mode ENUM('CAMPAIGN','PHONE','LEAD','LEAD_CUSTOM2','LEAD_CUSTOM1') default 'CAMPAIGN';##|##
 ##    Add LEAD_CUSTOM1 option for XFER/3rd-party calls.;

UPDATE system_settings SET version='2.2.1.060',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.060 and clearing last_update_check flag.;
