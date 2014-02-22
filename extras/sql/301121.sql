# 02/21/2014

CREATE TABLE server_keepalive_processes (
  id INT(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  server_ip VARCHAR(30),
  name VARCHAR(255),
  pid INT(11) NOT NULL DEFAULT '0',
  last_checkin timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `last` (`last_checkin`),
  KEY `server` (`server_ip`),
  KEY `name` (`name`)
) Engine=InnoDB;##|##
  ## Process tracking table.;

ALTER TABLE osdial_log ADD INDEX `campaign` (`campaign_id`), ADD INDEX `list` (`list_id`);##|##
 ##Add campaign and list indexes to osdial_log.;

UPDATE system_settings SET version='3.0.1.121',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.121 and clearing last_update_check flag.;
