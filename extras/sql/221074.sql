# 09/11/2010

CREATE TABLE osdial_campaign_email_blacklist (
  campaign_id VARCHAR(20) NOT NULL,
  email VARCHAR(255) NOT NULL,
  PRIMARY KEY (campaign_id,email)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
  ## Table to hold Blacklisted Email Address per Campaign.;


UPDATE system_settings SET version='2.2.1.074',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.074 and clearing last_update_check flag.;
