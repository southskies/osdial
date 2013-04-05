# 04/05/2013

ALTER TABLE system_settings ADD COLUMN last_general_extension varchar(20) DEFAULT '85110000';##|##
 ## Adding a general extension for non-file based media, template, and phone association.;

ALTER TABLE system_settings ADD COLUMN default_phone_code varchar(10) DEFAULT '1';##|##
 ## Adding a definable default phone country code for lead import, incoming, and outbound calls.;


UPDATE system_settings SET version='2.9.9.095',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.9.9.095 and clearing last_update_check flag.;
