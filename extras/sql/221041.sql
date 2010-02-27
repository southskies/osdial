# 02/27/2010

ALTER TABLE osdial_users ADD export_leads enum('0','1') DEFAULT '0';##|##
 ##    Add permission option for exporting leads.;

UPDATE osdial_users SET export_leads='1' WHERE user_level > '8';##|##
 ##    Go ahead and grant export_leads for users with a user_level > 8.;

ALTER TABLE osdial_users ADD admin_api_access enum('0','1') DEFAULT '0';##|##
 ##    Add permission option for API access for admin functions.;

ALTER TABLE osdial_users ADD agent_api_access enum('0','1') DEFAULT '0';##|##
 ##    Add permission option for API access for agent functions.;

UPDATE system_settings SET version='2.2.1.041',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.041 and clearing last_update_check flag.;
