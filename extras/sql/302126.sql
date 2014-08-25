# 08/24/2014

ALTER TABLE osdial_lists ADD COLUMN `lead_transfer_id` VARCHAR(30) NOT NULL DEFAULT '';##|##
 ## Add lead transfer id to lists.;

CREATE TABLE osdial_lead_transfers (
  id VARCHAR(30) NOT NULL PRIMARY KEY,
  description VARCHAR(255),
  container TEXT,
  active enum('Y','N') DEFAULT 'N'
) Engine=InnoDB;##|##
  ## Table for lead transfers.;

UPDATE system_settings SET version='3.0.2.126',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.2.126 and clearing last_update_check flag.;
