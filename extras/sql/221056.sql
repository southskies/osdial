# 05/01/2010

LOCK TABLES server_updater WRITE, server_performance WRITE, web_client_sessions WRITE, osdial_campaign_stats WRITE, osdial_campaign_server_stats WRITE, osdial_campaign_agent_stats WRITE;##|##
 ##    Lock tables.;

ALTER TABLE server_updater DROP PRIMARY KEY;##|##
 ##    Drop server_updater primary key.;
ALTER TABLE server_performance DROP PRIMARY KEY;##|##
 ##    Drop server_performance primary key.;
ALTER TABLE web_client_sessions DROP PRIMARY KEY;##|##
 ##    Drop web_client_sessions primary key.;
ALTER TABLE osdial_campaign_stats DROP PRIMARY KEY;##|##
 ##    Drop osdial_campaign_stats primary key.;
ALTER TABLE osdial_campaign_server_stats DROP PRIMARY KEY;##|##
 ##    Drop osdial_campaign_server_stats primary key.;
ALTER TABLE osdial_campaign_agent_stats DROP PRIMARY KEY;##|##
 ##    Drop osdial_campaign_agent_stats primary key.;

DELETE FROM server_updater;##|##
 ##    Clear records in server_updater.;
DELETE FROM server_performance;##|##
 ##    Clear records in server_performance.;
DELETE FROM web_client_sessions;##|##
 ##    Clear records in web_client_sessions.;
DELETE FROM osdial_campaign_stats;##|##
 ##    Clear records in osdial_campaign_stats.;
DELETE FROM osdial_campaign_server_stats;##|##
 ##    Clear records in osdial_campaign_server_stats.;
DELETE FROM osdial_campaign_agent_stats;##|##
 ##    Clear records in osdial_campaign_agent_stats.;

ALTER IGNORE TABLE server_updater ADD PRIMARY KEY (server_ip);##|##
 ##    Add server_updater primary key.;
ALTER IGNORE TABLE server_performance ADD PRIMARY KEY (server_ip,start_time);##|##
 ##    Add server_performance primary key.;
ALTER IGNORE TABLE web_client_sessions ADD PRIMARY KEY (session_name);##|##
 ##    Add web_client_sessions primary key.;
ALTER IGNORE TABLE osdial_campaign_stats ADD PRIMARY KEY (campaign_id);##|##
 ##    Add osdial_campaign_stats primary key.;
ALTER IGNORE TABLE osdial_campaign_server_stats ADD PRIMARY KEY (campaign_id,server_ip);##|##
 ##    Add osdial_campaign_server_stats primary key.;
ALTER IGNORE TABLE osdial_campaign_agent_stats ADD PRIMARY KEY (campaign_id,user);##|##
 ##    Add osdial_campaign_agent_stats primary key.;

UNLOCK TABLES;##|##
 ##    Add osdial_campaign_agent_stats primary key.;

UPDATE system_settings SET version='2.2.1.056',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.056 and clearing last_update_check flag.;
