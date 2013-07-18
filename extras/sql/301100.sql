# 07/10/2013

ALTER TABLE system_settings ADD COLUMN default_acct_method enum('NONE','RANGE','TOTAL','AVAILABLE','TALK','TALK_ROUNDUP') DEFAULT 'NONE';##|##
 ## default_acct_method;

ALTER TABLE system_settings ADD COLUMN default_acct_cutoff smallint(3) DEFAULT '5';##|##
 ## default_acct_cutoff;

ALTER TABLE system_settings ADD COLUMN default_acct_expire_days smallint(3) DEFAULT '30';##|##
 ## default_acct_expire_days;

ALTER TABLE system_settings ADD COLUMN acct_email_warning_time smallint(3) DEFAULT '30';##|##
 ## acct_email_warning_time;

ALTER TABLE system_settings ADD COLUMN acct_email_warning_expire smallint(3) DEFAULT '3';##|##
 ## acct_email_warning_expire;

ALTER TABLE system_settings ADD COLUMN acct_email_warning_message text;##|##
 ## acct_email_warning_message;

ALTER TABLE system_settings ADD COLUMN acct_email_warning_from varchar(255) DEFAULT 'system@osdial.org';##|##
 ## acct_email_warning_from;

ALTER TABLE system_settings ADD COLUMN acct_email_warning_subject varchar(255) DEFAULT 'Out of Credit Warning';##|##
 ## acct_email_warning_subject;

UPDATE system_settings SET default_acct_method='NONE',default_acct_cutoff='5',default_acct_expire_days='30',acct_email_warning_time='30',acct_email_warning_expire='3',acct_email_warning_message='You are nearly out of credit.',acct_email_warning_from='system@osdial.org',acct_email_warning_subject='Out of Credit Warning';##|##
 ## Set defaults;

CREATE TABLE osdial_acct_trans_daily (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  company_id TINYINT(3) UNSIGNED NOT NULL,
  agent_log_id INT(9) UNSIGNED NOT NULL,
  trans_type varchar(20) NOT NULL,
  trans_sec INT(11) NOT NULL,
  updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `comp` (`company_id`)
) Engine=InnoDB;##|##
  ## ;

CREATE TABLE osdial_acct_trans (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  company_id TINYINT(3) UNSIGNED NOT NULL,
  agent_log_id INT(9) UNSIGNED NOT NULL,
  trans_type varchar(20) NOT NULL,
  trans_sec INT(11) NOT NULL,
  ref_id INT(11) UNSIGNED DEFAULT '0',
  reconciled datetime DEFAULT '0000-00-00 00:00:00',
  expire_date datetime default '0000-00-00 00:00:00',
  updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `comp` (`company_id`),
  KEY `compal` (`company_id`,`agent_log_id`),
  KEY `ref` (`ref_id`)
) Engine=InnoDB;##|##
  ## ;

CREATE TABLE osdial_acct_purchases (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  company_id TINYINT(3) UNSIGNED NOT NULL,
  trans_id INT(11) UNSIGNED NOT NULL,
  purchase_type varchar(255) DEFAULT 'MINUTES',
  purchase_quantity int(11) NOT NULL DEFAULT '1',
  purchase_val varchar(255) NOT NULL DEFAULT '',
  purchase_expire_date datetime default '0000-00-00 00:00:00',
  payment_method varchar(255) NOT NULL DEFAULT '',
  payment_type varchar(255) NOT NULL DEFAULT '',
  payment_amount DECIMAL(8,2) NOT NULL DEFAULT '0.00',
  payment_transid varchar(255) NOT NULL DEFAULT '',
  payment_date datetime default '0000-00-00 00:00:00',
  payment_details text NOT NULL DEFAULT '',
  updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `comp` (`company_id`)
) Engine=InnoDB;##|##
  ## ;

CREATE TABLE osdial_acct_packages (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  code VARCHAR(255) NOT NULL,
  ptype enum('MINUTES','DAYS','OTHER') DEFAULT 'MINUTES',
  name VARCHAR(255) NOT NULL,
  units int(11) NOT NULL DEFAULT '1',
  use_default_expire enum('Y','N') NOT NULL DEFAULT 'Y',
  other_action enum('','CREATE_NEW_COMPANY','DISABLE_COMPANY') NOT NULL DEFAULT '',
  other_newcomp_acct_method enum('','NONE','RANGE','TOTAL','AVAILABLE','TALK','TALK_ROUNDUP') DEFAULT '',
  other_newcomp_initial_units int(11) DEFAULT '0',
  base_cost DECIMAL(8,2) NOT NULL DEFAULT '0.00',
  recurring enum('Y','N') NOT NULL DEFAULT 'N',
  recurring_days tinyint(3) NOT NULL DEFAULT '0',
  active enum('Y','N') NOT NULL DEFAULT 'Y',
  updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `pcode` (`ptype`,`code`)
) Engine=InnoDB;##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (1,'NEWCOMPANY','OTHER','Create New Company',1,'Y','CREATE_NEW_COMPANY','',60,200.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (2,'ADD1MIN','MINUTES','Adds 1 Minute',1,'Y','','',0,1.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (3,'ADD5MIN','MINUTES','Adds 5 Minutes',5,'Y','','',0,2.50,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (4,'ADD15MIN','MINUTES','Adds 15 Minutes',15,'Y','','',0,4.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (5,'ADD30MIN','MINUTES','Adds 30 Minutes',30,'Y','','',0,6.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (6,'ADD60MIN','MINUTES','Adds 60 Minutes',60,'Y','','',0,10.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (7,'ADD1DAY','DAYS','Adds 1 Day',1,'Y','','',0,100.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (8,'ADD5DAY','DAYS','Adds 5 Days',5,'Y','','',0,450.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (9,'ADD15DAY','DAYS','Adds 15 Days',15,'Y','','',0,1250.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

INSERT INTO `osdial_acct_packages` VALUES (10,'ADD30DAY','DAYS','Adds 30 Days',30,'Y','','',0,2250.00,'',0,'Y',NOW(),NOW());##|##
  ## ;

ALTER TABLE osdial_companies ADD COLUMN acct_method enum('NONE','RANGE','TOTAL','AVAILABLE','TALK','TALK_ROUNDUP') DEFAULT 'NONE';##|##
 ## acct_method;

ALTER TABLE osdial_companies ADD COLUMN acct_startdate DATETIME DEFAULT '0000-00-00 00:00:00';##|##
 ## acct_startdate;

ALTER TABLE osdial_companies ADD COLUMN acct_enddate DATETIME DEFAULT '0000-00-00 00:00:00';##|##
 ## acct_enddate;

ALTER TABLE osdial_companies ADD COLUMN acct_cutoff smallint(3) DEFAULT '0';##|##
 ## acct_cutoff;

ALTER TABLE osdial_companies ADD COLUMN acct_expire_days smallint(3) DEFAULT '0';##|##
 ## acct_expire_days;

ALTER TABLE osdial_companies ADD COLUMN acct_remaining_time int(11) DEFAULT '0';##|##
 ## acct_remaining_time;

ALTER TABLE osdial_companies ADD COLUMN email varchar(255) DEFAULT '';##|##
 ## email;

ALTER TABLE osdial_companies ADD COLUMN acct_warning_sent DATETIME DEFAULT '0000-00-00 00:00:00';##|##
 ## acct_warning_sent;

ALTER TABLE osdial_companies ADD COLUMN contact_name varchar(255) DEFAULT '';##|##
 ## contact_name;

ALTER TABLE osdial_companies ADD COLUMN contact_phone_number varchar(255) DEFAULT '';##|##
 ## contact_phone_number;

ALTER TABLE osdial_companies ADD COLUMN contact_address varchar(255) DEFAULT '';##|##
 ## contact_address;

ALTER TABLE osdial_companies ADD COLUMN contact_address2 varchar(255) DEFAULT '';##|##
 ## contact_address2;

ALTER TABLE osdial_companies ADD COLUMN contact_city varchar(255) DEFAULT '';##|##
 ## contact_city;

ALTER TABLE osdial_companies ADD COLUMN contact_state varchar(255) DEFAULT '';##|##
 ## contact_state;

ALTER TABLE osdial_companies ADD COLUMN contact_postal_code varchar(255) DEFAULT '';##|##
 ## contact_postal_code;

ALTER TABLE osdial_companies ADD COLUMN contact_country varchar(255) DEFAULT '';##|##
 ## contact_country;


UPDATE system_settings SET version='3.0.1.100',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.100 and clearing last_update_check flag.;
