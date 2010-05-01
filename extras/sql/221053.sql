# 04/03/2010

LOCK TABLES server_updater WRITE;##|##
 ##    Lock Tables.;

ALTER TABLE server_updater DROP PRIMARY KEY;##|##
 ##    Drop primary key if its there.;

DELETE FROM server_updater;##|##
 ##    Clear server updater records.

ALTER TABLE server_updater ADD sql_time timestamp NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ##    Increase filename size in recording_log.;

ALTER IGNORE TABLE server_updater ADD PRIMARY KEY (server_ip);##|##
 ##    Add primary key.;

UNLOCK TABLES;##|##
 ##    Unlock Tables.;

UPDATE system_settings SET version='2.2.1.053',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.053 and clearing last_update_check flag.;
