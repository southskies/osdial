# 02/15/2010

ALTER TABLE osdial_lists ADD web_form_address VARCHAR(255) default '';##|##
 ##    Add web_form_address to lists.;

ALTER TABLE osdial_lists ADD web_form_address2 VARCHAR(255) default '';##|##
 ##    Add web_form_address2 to lists.;

UPDATE servers SET telnet_host='127.0.0.1' WHERE telnet_host='localhost';##|##
 ##    AMI host: replace localhost with dotted notation in servers table.;

UPDATE system_settings SET version='2.2.1.039',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.039 and clearing last_update_check flag.;
