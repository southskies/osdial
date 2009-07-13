# 07/12/2009

ALTER TABLE osdial_campaign_stats ADD amd_onemin INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_stats ADD failed_onemin INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_stats ADD agents_paused INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_stats ADD agents_incall INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_stats ADD agents_waiting INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_stats ADD waiting_calls INT(9) UNSIGNED NOT NULL default '0';

ALTER TABLE osdial_campaign_server_stats ADD calls_onemin INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_server_stats ADD answers_onemin INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_server_stats ADD drops_onemin INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_server_stats ADD amd_onemin INT(9) UNSIGNED NOT NULL default '0';
ALTER TABLE osdial_campaign_server_stats ADD failed_onemin INT(9) UNSIGNED NOT NULL default '0';

CREATE UNIQUE INDEX camp_serv ON osdial_campaign_server_stats (campaign_id,server_ip);

ALTER TABLE call_log ADD isup_result INT(9) UNSIGNED NOT NULL default '0';

ALTER TABLE osdial_log ADD server_ip VARCHAR(20) NOT NULL default '';

UPDATE system_settings SET version='2.1.4.029';
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);
