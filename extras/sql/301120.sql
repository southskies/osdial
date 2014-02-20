# 02/06/2014

CREATE TABLE osdial_events (
  id BIGINT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  event_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  server_ip VARCHAR(15),
  uniqueid VARCHAR(20),
  callerid VARCHAR(20),
  `user` VARCHAR(20),
  campaign_id VARCHAR(20),
  group_id VARCHAR(20),
  lead_id INT(9) unsigned,
  event VARCHAR(50) NOT NULL,
  data1 VARCHAR(255) NOT NULL,
  data2 VARCHAR(255) NOT NULL,
  data3 VARCHAR(255) NOT NULL,
  data4 VARCHAR(255) NOT NULL,
  data5 VARCHAR(255) NOT NULL,
  data6 VARCHAR(255) NOT NULL,
  KEY `event_time` (`event_time`),
  KEY `svruniq` (`server_ip`,`uniqueid`),
  KEY `callerid` (`callerid`),
  KEY `user` (`user`),
  KEY `campaign_id` (`campaign_id`),
  KEY `group_id` (`group_id`),
  KEY `lead_id` (`lead_id`)
) Engine=InnoDB;##|##
  ## System events table;

UPDATE osdial_extensions_data SET ext_appdata=REPLACE(ext_appdata,' ^{MIXMONITOR_FILENAME} ',' \'^{MIXMONITOR_FILENAME}\' ') WHERE ext_app='MixMonitor';##|##
 ##Quote filename for MixMonitor move.;

UPDATE system_settings SET version='3.0.1.120',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.120 and clearing last_update_check flag.;
