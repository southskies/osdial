# 08/31/2010


ALTER TABLE osdial_conferences ADD leave_3way ENUM('0','1') default '0';##|##
 ##    Adds leave_3way flag to conferences.;

ALTER TABLE osdial_conferences ADD leave_3way_datetime DATETIME;##|##
 ##    Adds leave_3way_datetime to conferences.;

UPDATE system_settings SET version='2.2.1.072',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.072 and clearing last_update_check flag.;
