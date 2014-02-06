# 01/29/2014

ALTER TABLE osdial_manager MODIFY action VARCHAR(50), MODIFY cmd_line_b VARCHAR(1024), MODIFY cmd_line_c VARCHAR(1024), MODIFY cmd_line_d VARCHAR(1024), MODIFY cmd_line_e VARCHAR(1024), MODIFY cmd_line_f VARCHAR(1024), MODIFY cmd_line_g VARCHAR(1024), MODIFY cmd_line_h VARCHAR(1024), MODIFY cmd_line_i VARCHAR(1024), MODIFY cmd_line_j VARCHAR(1024), MODIFY cmd_line_k VARCHAR(1024);##|##
 ## Increase size of cmd_line fields in order to hold all the data;

UPDATE osdial_extensions_data SET ext_appdata=REPLACE(ext_appdata,'${CALLERID(name)}','${FILENAME}') WHERE exten IN ('8309','8310','8311') AND ext_app='MixMonitor';##|##
 ## Do not use the CallerID to pass the filename.;


CREATE TABLE call_log2 LIKE call_log;##|##
 ## Create new call_log table.;

ALTER TABLE call_log2 DROP primary key, ADD KEY `uniquesvr` (`server_ip`,`uniqueid`), ADD COLUMN id BIGINT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST, ADD COLUMN carrier_id INT(11), ADD COLUMN did_id INT(11), ADD COLUMN answer_time DATETIME DEFAULT '0000-00-00 00:00:00', ADD COLUMN answer_epoch INT(10), ADD COLUMN cid_name VARCHAR(100), ADD COLUMN cid_number VARCHAR(80), ADD COLUMN dnid VARCHAR(80), ADD COLUMN language VARCHAR(20), DROP INDEX `time`, DROP INDEX `server_ip`, DROP INDEX `channel`, DROP INDEX `end_time`;##|##
 ## Alter new call_log table.;

INSERT IGNORE INTO call_log2 SELECT NULL,call_log.*,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL FROM call_log;##|##
 ## Copy data from old call_log to new call_log table.;

ALTER TABLE call_log RENAME TO old_call_log;##|##
 ## Rename old call_log to old_call_log.;

ALTER TABLE call_log2 RENAME TO call_log;##|##
 ## Rename new call_log to call_log.;


CREATE TABLE osdial_log2 LIKE osdial_log;##|##
 ## Create new osdial_log table.;

ALTER TABLE osdial_log2 DROP primary key, ADD KEY `scruniq` (`server_ip`,`uniqueid`), ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST, ADD COLUMN callerid VARCHAR(20) DEFAULT '', ADD INDEX `callerid` (`callerid`);##|##
 ## Alter new osdial_log table.;

INSERT IGNORE INTO osdial_log2 SELECT NULL,osdial_log.*,NULL FROM osdial_log;##|##
 ## Copy data from old osdial_log to new osdial_log table.;

ALTER TABLE osdial_log RENAME TO old_osdial_log;##|##
 ## Rename old osdial_log to old_osdial_log.;

ALTER TABLE osdial_log2 RENAME TO osdial_log;##|##
 ## Rename new osdial_log to osdial_log.;


CREATE TABLE osdial_closer_log2 LIKE osdial_closer_log;##|##
 ## Create new osdial_closer_log table.;

ALTER TABLE osdial_closer_log2 DROP INDEX `uniqueid`, ADD COLUMN server_ip VARCHAR(15), ADD INDEX `svruniq` (`server_ip`,`uniqueid`), ADD INDEX `callerid` (`callerid`);##|##
 ## Alter new osdial_closer_log table.;

INSERT IGNORE INTO osdial_closer_log2 SELECT osdial_closer_log.*,NULL FROM osdial_closer_log;##|##
 ## Copy data from old osdial_closer_log to new osdial_closer_log table.;

ALTER TABLE osdial_closer_log RENAME TO old_osdial_closer_log;##|##
 ## Rename old osdial_closer_log to old_osdial_closer_log.;

ALTER TABLE osdial_closer_log2 RENAME TO osdial_closer_log;##|##
 ## Rename new osdial_closer_log to osdial_closer_log.;


UPDATE system_settings SET version='3.0.1.119',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.119 and clearing last_update_check flag.;
