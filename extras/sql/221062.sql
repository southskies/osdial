# 06/18/2010

ALTER TABLE osdial_lists MODIFY web_form_address VARCHAR(2000) default '';##|##
 ##    Default web_form_address override should be blank.;

ALTER TABLE osdial_lists MODIFY web_form_address2 VARCHAR(2000) default '';##|##
 ##    Default web_form_address2 override should be blank.;

UPDATE system_settings SET version='2.2.1.062',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.062 and clearing last_update_check flag.;
