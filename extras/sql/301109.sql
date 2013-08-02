# 08/02/2013

CREATE TABLE osdial_statuses_extended (
  parents varchar(255) NOT NULL,
  status varchar(10) NOT NULL,
  status_name varchar(30) NOT NULL,
  selectable enum('Y','N') DEFAULT NULL,
  PRIMARY KEY (`parents`,`status`)
) Engine=InnoDB;##|##
  ## Extended Status Table;

ALTER TABLE osdial_list ADD COLUMN status_extended varchar(255) NOT NULL DEFAULT '';##|##
 ## Agent Message;

CREATE TABLE osdial_campaign_statuses_extended (
  campaign_id varchar(20) NOT NULL,
  parents varchar(255) NOT NULL,
  status varchar(10) NOT NULL,
  status_name varchar(30) NOT NULL,
  selectable enum('Y','N') DEFAULT NULL,
  PRIMARY KEY (`campaign_id`,`parents`,`status`)
) Engine=InnoDB;##|##
  ## Extended Campaigns Status Table;

UPDATE system_settings SET version='3.0.1.109',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.109 and clearing last_update_check flag.;
