# 02/28/2010

ALTER TABLE system_settings ADD enable_multicompany ENUM('0','1') default '0';##|##
 ##    Option to turn multi-company support on / off.;

ALTER TABLE system_settings ADD multicompany_admin VARCHAR(20) default 'admin';##|##
 ##    System option for user that will have global access when multi-company is enabled.;

CREATE TABLE osdial_companies (
  id TINYINT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  creation_date timestamp NOT NULL default CURRENT_TIMESTAMP,
  name varchar(100) NOT NULL default '',
  status ENUM('INACTIVE','ACTIVE','SUSPENDED','TERMINATED') NOT NULL default 'INACTIVE',
  campaign_ivr ENUM('0','1') NOT NULL default '0',
  campaign_listmix ENUM('0','1') NOT NULL default '1',
  lead_export ENUM('0','1') NOT NULL default '1',
  scripts ENUM('0','1') NOT NULL default '1',
  filters ENUM('0','1') NOT NULL default '0',
  ingroups ENUM('0','1') NOT NULL default '0',
  external_agents ENUM('0','1') NOT NULL default '0',
  system_call_times ENUM('0','1') NOT NULL default '0',
  system_phones ENUM('0','1') NOT NULL default '0',
  system_conferences ENUM('0','1') NOT NULL default '0',
  system_servers ENUM('0','1') NOT NULL default '0',
  system_campaign_trunks ENUM('0','1') NOT NULL default '0',
  system_statuses ENUM('0','1') NOT NULL default '0',
  system_status_categories ENUM('0','1') NOT NULL default '0',
  api_access ENUM('0','1') NOT NULL default '0',
  dnc_method ENUM('SYSTEM','COMPANY','BOTH','NONE') NOT NULL default 'SYSTEM',
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
 ## Initial mutli-company table.;

UPDATE system_settings SET version='2.2.1.044',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.044 and clearing last_update_check flag.;
