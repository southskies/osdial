# 12/12/2010

ALTER TABLE phones ADD voicemail_password VARCHAR(50) DEFAULT '1234';##|##
 ##    Adds field for the voicemail password.;

ALTER TABLE phones ADD voicemail_email VARCHAR(255) DEFAULT '';##|##
 ##    Adds field for the voicemail email.;

UPDATE system_settings SET version='2.2.9.080',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.080 and clearing last_update_check flag.;
