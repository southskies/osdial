# 10/20/2010

CREATE INDEX external_key ON osdial_list (external_key);##|##
 ##    Create index on external key.;

UPDATE system_settings SET version='2.2.9.078',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.078 and clearing last_update_check flag.;
