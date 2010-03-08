# 02/28/2010

DROP TABLE osdial_stations;##|##
 ##    Drop unusued table.;

DROP TABLE osdial_verification_ivr;##|##
 ##    Drop unusued table.;

DROP TABLE phone_favorites;##|##
 ##    Drop unusued table.;

UPDATE system_settings SET version='2.2.1.048',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.048 and clearing last_update_check flag.;
