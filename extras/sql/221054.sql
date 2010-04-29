# 04/19/2010

ALTER TABLE osdial_companies ADD default_ext_context VARCHAR(20) NOT NULL default 'osdial';##|##
 ##    Add default phones ext context to MC.;

UPDATE system_settings SET version='2.2.1.054',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.054 and clearing last_update_check flag.;
