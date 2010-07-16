# 07/07/2010

ALTER TABLE osdial_carriers DROP COLUMN server_ip;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers DROP COLUMN inbound_context;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers DROP COLUMN inbound_config;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers DROP COLUMN outbound_context;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers DROP COLUMN outbound_config;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers DROP COLUMN failover_active;##|##
 ##    Updates to osdial_carriers.;


ALTER TABLE osdial_carriers ADD name VARCHAR(20) default '' AFTER id;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers CHANGE COLUMN description description VARCHAR(255) default '' AFTER name;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers MODIFY active ENUM('Y','N') default 'N';##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers ADD selectable ENUM('Y','N') default 'Y' AFTER active;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers MODIFY protocol ENUM('SIP','IAX2','DAHDI','Zap','EXTERNAL') default 'SIP';##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers ADD protocol_config TEXT AFTER protocol;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers CHANGE COLUMN registration_string registrations TEXT;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers CHANGE COLUMN dial_rules dialplan TEXT;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers CHANGE COLUMN failover_to failover_id INT(11) default '0' AFTER dialplan;##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers CHANGE COLUMN failover_on failover_condition ENUM('CHANUNAVAIL','CONGESTION','BOTH') default 'CHANUNAVAIL';##|##
 ##    Updates to osdial_carriers.;


ALTER TABLE osdial_carrier_dids MODIFY extension VARCHAR(100) default '9999';##|##
 ##    Updates to osdial_carrier_dids.;

ALTER TABLE osdial_carrier_dids ADD extension_context VARCHAR(20) default 'default' AFTER extension;##|##
 ##    Updates to osdial_carrier_dids.;

ALTER TABLE osdial_carrier_dids MODIFY voicemail VARCHAR(20) default '1234';##|##
 ##    Updates to osdial_carrier_dids.;

ALTER TABLE osdial_carrier_dids ADD phone VARCHAR(100) default '' AFTER did_action;##|##
 ##    Updates to osdial_carrier_dids.;

ALTER TABLE osdial_carrier_dids MODIFY did_action ENUM('INGROUP','PHONE','EXTENSION','VOICEMAIL') default 'EXTENSION';##|##
 ##    Updates to osdial_carriers.;


ALTER TABLE osdial_companies ADD enable_system_carriers ENUM('Y','N') default 'N';##|##
 ##    Add system carrier permission to companies.;


CREATE TABLE osdial_carrier_servers (
  carrier_id INT(11) NOT NULL,
  server_ip VARCHAR(15) NOT NULL,
  protocol_config TEXT NOT NULL,
  registrations TEXT NOT NULL,
  dialplan TEXT NOT NULL,
  PRIMARY KEY (carrier_id,server_ip)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
  ## Add tables for server specific carrier options.;


ALTER TABLE osdial_carriers ADD strip_msd ENUM('Y','N') default 'N';##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers ADD allow_international ENUM('Y','N') default 'N';##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers ADD default_callerid VARCHAR(20) default '0000000000';##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers ADD default_areacode VARCHAR(3) default '321';##|##
 ##    Updates to osdial_carriers.;

ALTER TABLE osdial_carriers ADD default_prefix VARCHAR(1) default '9';##|##
 ##    Updates to osdial_carriers.;


ALTER TABLE osdial_campaigns ADD carrier_id INT(11) default '0';##|##
 ##    Add carrier selection to campaigns.;

ALTER TABLE system_settings ADD default_carrier_id INT(11) default '0';##|##
 ##    Add carrier selection to system_settings.;


UPDATE system_settings SET version='2.2.1.065',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.065 and clearing last_update_check flag.;
