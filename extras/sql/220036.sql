# 11/15/2009

ALTER TABLE osdial_campaigns ADD use_custom2_callerid ENUM('Y','N') default 'N';##|##
 ##Ability to use per-lead callerid from custom2 field in lead.;

UPDATE system_settings SET version='2.2.0.036',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.0.036 and Clearing last_update_check flag.;
