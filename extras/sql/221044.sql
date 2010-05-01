# 2010/03/01

set session old_alter_table=1;##|##
 ##    Use old_alter_table.;

DELETE FROM server_performance;##|##
 ##    The Server Performane table does not have a primary key, but could actually have duplicates, so we must clear it;

ALTER IGNORE TABLE server_performance ADD PRIMARY KEY (server_ip,start_time);##|##
 ##    Add primary key.;

UPDATE system_settings SET version='2.2.1.044',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.044 and clearing last_update_check flag.;
