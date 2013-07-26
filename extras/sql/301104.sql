# 07/26/2013

ALTER TABLE system_settings ADD COLUMN admin_session_expiration int(11) unsigned DEFAULT '1800';##|##
 ## The expiration time for admin sessions.;

ALTER TABLE system_settings ADD COLUMN admin_session_lockout int(11) unsigned DEFAULT '1800';##|##
 ## The time after expiration that the user history is removed from admin sessions.;

UPDATE system_settings SET version='3.0.1.104',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.104 and clearing last_update_check flag.;
