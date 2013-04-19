# 04/18/2013

ALTER TABLE osdial_list ADD COLUMN organization varchar(255) DEFAULT '';##|##
 ## Add organization to lead.  *** This may take awhile, do not interrupt! ***;

ALTER TABLE osdial_list ADD COLUMN organization_name varchar(255) DEFAULT '';##|##
 ## Add organization_name to lead. *** This may take awhile, do not interrupt! ***;

UPDATE system_settings SET version='3.0.0.096',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.0.096 and clearing last_update_check flag.;
