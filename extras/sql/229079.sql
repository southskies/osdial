# 11/06/2010

ALTER TABLE osdial_users ADD load_dnc ENUM('0','1') DEFAULT '0';##|##
 ##    Adds permission option to load DNC records from CSV file.;

ALTER TABLE osdial_users ADD export_dnc ENUM('0','1') DEFAULT '0';##|##
 ##    Adds permission option to export DNC records to CSV file.;

ALTER TABLE osdial_users ADD delete_dnc ENUM('0','1') DEFAULT '0';##|##
 ##    Adds permission option to delete DNC records.;

UPDATE osdial_users SET load_dnc='1',export_dnc='1',delete_dnc='1' WHERE (user='admin' OR user='6666') AND user_level='9';##|##
 ##    Adds load/export/delete permissions to admin user.;

ALTER TABLE osdial_dnc ADD creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;##|##
 ##    Adds a timestamp to the DNC record.;

UPDATE osdial_dnc SET creation_date='2010-01-01 00:00:00';##|##
 ##    Set the initial creation_date to 2010-01-01 00:00:00.;

UPDATE system_settings SET version='2.2.9.079',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.079 and clearing last_update_check flag.;
