# 02/28/2010

set session old_alter_table=1;##|##
 ##    Use old_alter_table.;

ALTER IGNORE TABLE conferences ADD PRIMARY KEY (conf_exten,server_ip);##|##
 ##    Add primary key.;

ALTER TABLE osdial_callbacks MODIFY campaign_id VARCHAR(20);##|##
 ##    Field length or NULL correction.;

ALTER TABLE osdial_campaign_agents DROP INDEX campaign_id, DROP INDEX user;##|##
 ##    Bad index.;

ALTER TABLE osdial_campaign_agents MODIFY campaign_id VARCHAR(20) NOT NULL, MODIFY user VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER IGNORE TABLE osdial_campaign_agents ADD PRIMARY KEY (campaign_id,user);##|##
 ##    Didnt have a valid primary key.;

ALTER TABLE osdial_campaign_hotkeys DROP INDEX campaign_id;##|##
 ##    Bad index.;

ALTER TABLE osdial_campaign_hotkeys MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER IGNORE TABLE osdial_campaign_hotkeys ADD PRIMARY KEY (campaign_id,hotkey);##|##
 ##    Add primary key.;

ALTER TABLE osdial_campaign_server_stats DROP INDEX campaign_id, DROP INDEX server_ip, DROP INDEX camp_serv;##|##
 ##    Bad index.;

ALTER IGNORE TABLE osdial_campaign_server_stats ADD PRIMARY KEY (campaign_id,server_ip);##|##
 ##    Add primary key.;

ALTER TABLE osdial_campaign_statuses DROP INDEX campaign_id;##|##
 ##    Bad index.;

ALTER TABLE osdial_campaign_statuses MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER IGNORE TABLE osdial_campaign_statuses ADD PRIMARY KEY (campaign_id,status);##|##
 ##    Didnt have a valid primary key.;

ALTER TABLE osdial_campaigns MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER TABLE osdial_campaigns_list_mix MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER IGNORE TABLE osdial_conferences ADD PRIMARY KEY (conf_exten,server_ip);##|##
 ##    Add primary key.;

ALTER TABLE osdial_hopper MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER TABLE osdial_lead_recycle MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER TABLE osdial_lists MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER TABLE osdial_live_agents MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;


ALTER TABLE osdial_pause_codes DROP INDEX campaign_id;##|##
 ##    Bad index.;

ALTER TABLE osdial_pause_codes MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER IGNORE TABLE osdial_pause_codes ADD PRIMARY KEY (campaign_id,pause_code);##|##
 ##    Add primary key.;

ALTER TABLE osdial_remote_agents MODIFY campaign_id VARCHAR(20) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER TABLE phones DROP INDEX server_ip;##|##
 ##    Bad index.;

ALTER TABLE phones MODIFY login VARCHAR(20), MODIFY login_campaign VARCHAR(20), MODIFY extension VARCHAR(100) NOT NULL, MODIFY server_ip VARCHAR(15) NOT NULL;##|##
 ##    Field length or NULL correction.;

ALTER IGNORE TABLE phones ADD PRIMARY KEY (server_ip,extension);##|##
 ##    Add primary key.;

ALTER IGNORE TABLE server_updater ADD PRIMARY KEY (server_ip);##|##
 ##    Add primary key.;

ALTER IGNORE TABLE servers ADD PRIMARY KEY (server_ip);##|##
 ##    Add primary key.;

ALTER TABLE web_client_sessions DROP INDEX session_name;##|##
 ##    Bad index.;

ALTER IGNORE TABLE web_client_sessions ADD PRIMARY KEY (session_name);##|##
 ##    Add primary key.;

ALTER IGNORE TABLE parked_channels ADD PRIMARY KEY (server_ip,channel);##|##
 ##    Add primary key.;

UPDATE osdial_users SET manual_dial_new_limit='-1' WHERE manual_dial_new_limit='0';##|##
 ##    Fix so the default it unlimited manual dial / NEW / calls for manual dial agents..;

UPDATE system_settings SET version='2.2.1.043',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.043 and clearing last_update_check flag.;
