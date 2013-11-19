# 11/19/2013

ALTER TABLE osdial_inbound_groups ADD COLUMN `welcome_message_min_playtime` varchar(10) DEFAULT '0';##|##
 ## Add min playtime for welcome message.;

UPDATE osdial_inbound_groups SET `welcome_message_min_playtime`='0';##|##
 ## Set default on welcome_message_min_playtime to 0.;

UPDATE system_settings SET version='3.0.1.116',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.116 and clearing last_update_check flag.;
