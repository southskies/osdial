# 08/01/2013

ALTER TABLE osdial_user_groups ADD COLUMN agent_message text;##|##
 ## Agent Message;

UPDATE system_settings SET version='3.0.1.108',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.108 and clearing last_update_check flag.;
