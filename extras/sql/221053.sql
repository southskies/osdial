# 04/03/2010

set session old_alter_table=1;##|##
 ##    Use old_alter_table.;

ALTER TABLE server_updater ADD sql_time timestamp NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ##    Increase filename size in recording_log.;

DELETE FROM server_updater;##|##
 ##    Clear server updater records.

ALTER IGNORE TABLE server_updater ADD PRIMARY KEY (server_ip);##|##
 ##    Add primary key.;

UPDATE system_settings SET version='2.2.1.053',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.053 and clearing last_update_check flag.;
