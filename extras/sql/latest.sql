GRANT ALL on osdial.* TO 'osdial'@'127.0.0.1' IDENTIFIED BY 'osdial1234';
GRANT ALL on osdial.* TO 'osdial'@'localhost' IDENTIFIED BY 'osdial1234';
GRANT ALL on osdial.* TO 'osdial'@'%' IDENTIFIED BY 'osdial1234';

CREATE TABLE `call_log` (
  `uniqueid` varchar(20) NOT NULL,
  `channel` varchar(100) NOT NULL DEFAULT '',
  `channel_group` varchar(30) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `server_ip` varchar(15) NOT NULL DEFAULT '',
  `extension` varchar(100) DEFAULT NULL,
  `number_dialed` varchar(15) DEFAULT NULL,
  `caller_code` varchar(20) NOT NULL DEFAULT '',
  `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_epoch` int(10) DEFAULT NULL,
  `end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_epoch` int(10) DEFAULT NULL,
  `length_in_sec` int(10) DEFAULT NULL,
  `length_in_min` double(8,2) DEFAULT NULL,
  `isup_result` int(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uniqueid`),
  KEY `caller_code` (`caller_code`),
  KEY `server_ip` (`server_ip`),
  KEY `channel` (`channel`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `time` (`start_time`,`end_time`)
);

CREATE TABLE `conferences` (
  `conf_exten` int(7) unsigned NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `extension` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`conf_exten`,`server_ip`)
);

CREATE TABLE `configuration` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_id` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `data` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_id` (`fk_id`,`name`),
  KEY `name` (`name`)
);

INSERT INTO `configuration` VALUES (1,'','ArchiveHostname','127.0.0.1');
INSERT INTO `configuration` VALUES (2,'','ArchiveTransferMethod','FTP');
INSERT INTO `configuration` VALUES (3,'','ArchivePort','21');
INSERT INTO `configuration` VALUES (4,'','ArchiveUsername','osdial');
INSERT INTO `configuration` VALUES (5,'','ArchivePassword','osdialftp1234');
INSERT INTO `configuration` VALUES (6,'','ArchivePath','recordings/processing/unmixed');
INSERT INTO `configuration` VALUES (7,'','ArchiveWebPath','http://127.0.0.1');
INSERT INTO `configuration` VALUES (8,'','ArchiveMixFormat','MP3');
INSERT INTO `configuration` VALUES (9,'','External_DNC_Active','N');
INSERT INTO `configuration` VALUES (10,'','External_DNC_Address','');
INSERT INTO `configuration` VALUES (11,'','External_DNC_Database','');
INSERT INTO `configuration` VALUES (12,'','External_DNC_Username','');
INSERT INTO `configuration` VALUES (13,'','External_DNC_Password','');
INSERT INTO `configuration` VALUES (14,'','External_DNC_SQL','');
INSERT INTO `configuration` VALUES (15,'','ArchiveReportPath','reports');

CREATE TABLE `inbound_numbers` (
  `extension` varchar(30) NOT NULL,
  `full_number` varchar(30) NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `inbound_name` varchar(30) DEFAULT NULL,
  `department` varchar(30) DEFAULT NULL
);

CREATE TABLE `live_channels` (
  `channel` varchar(100) NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `channel_group` varchar(30) DEFAULT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `channel_data` varchar(100) DEFAULT NULL
);

CREATE TABLE `live_inbound` (
  `uniqueid` varchar(20) NOT NULL,
  `channel` varchar(100) NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `caller_id` varchar(30) DEFAULT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `phone_ext` varchar(40) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `acknowledged` enum('Y','N') DEFAULT 'N',
  `inbound_number` varchar(20) DEFAULT NULL,
  `comment_a` varchar(50) DEFAULT NULL,
  `comment_b` varchar(50) DEFAULT NULL,
  `comment_c` varchar(50) DEFAULT NULL,
  `comment_d` varchar(50) DEFAULT NULL,
  `comment_e` varchar(50) DEFAULT NULL
);

CREATE TABLE `live_inbound_log` (
  `uniqueid` varchar(20) NOT NULL,
  `channel` varchar(100) NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `caller_id` varchar(30) DEFAULT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `phone_ext` varchar(40) DEFAULT '',
  `start_time` datetime DEFAULT '0000-00-00 00:00:00',
  `acknowledged` enum('Y','N') DEFAULT 'N',
  `inbound_number` varchar(20) DEFAULT NULL,
  `comment_a` varchar(50) DEFAULT NULL,
  `comment_b` varchar(50) DEFAULT NULL,
  `comment_c` varchar(50) DEFAULT NULL,
  `comment_d` varchar(50) DEFAULT NULL,
  `comment_e` varchar(50) DEFAULT NULL,
  KEY `uniqueid` (`uniqueid`),
  KEY `phone_ext` (`phone_ext`),
  KEY `start_time` (`start_time`)
);

CREATE TABLE `live_sip_channels` (
  `channel` varchar(255) NOT NULL DEFAULT '',
  `server_ip` varchar(15) NOT NULL,
  `channel_group` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(100) DEFAULT NULL,
  `channel_data` varchar(100) DEFAULT NULL
);

CREATE TABLE `osdial_agent_log` (
  `agent_log_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(20) DEFAULT NULL,
  `server_ip` varchar(15) NOT NULL,
  `event_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lead_id` int(9) unsigned NOT NULL DEFAULT '0',
  `campaign_id` varchar(20) NOT NULL DEFAULT '',
  `pause_epoch` int(10) unsigned DEFAULT NULL,
  `pause_sec` smallint(5) unsigned DEFAULT '0',
  `wait_epoch` int(10) unsigned DEFAULT NULL,
  `wait_sec` smallint(5) unsigned DEFAULT '0',
  `talk_epoch` int(10) unsigned DEFAULT NULL,
  `talk_sec` smallint(5) unsigned DEFAULT '0',
  `dispo_epoch` int(10) unsigned DEFAULT NULL,
  `dispo_sec` smallint(5) unsigned DEFAULT '0',
  `status` varchar(6) DEFAULT NULL,
  `user_group` varchar(20) DEFAULT NULL,
  `comments` varchar(100) DEFAULT NULL,
  `sub_status` varchar(6) DEFAULT NULL,
  `uniqueid` varchar(20) NOT NULL DEFAULT '',
  `lead_called_count` int(9) DEFAULT '0',
  `prev_status` varchar(6) DEFAULT '',
  PRIMARY KEY (`agent_log_id`),
  KEY `time_user` (`event_time`,`user`),
  KEY `uniqueid` (`uniqueid`),
  KEY `lead_id` (`lead_id`),
  KEY `campaign_id` (`campaign_id`)
);

CREATE TABLE `osdial_auto_calls` (
  `auto_call_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `server_ip` varchar(15) NOT NULL,
  `campaign_id` varchar(20) DEFAULT NULL,
  `status` enum('SENT','RINGING','LIVE','XFER','PAUSED','CLOSER','BUSY','DISCONNECT','CONGESTION','CPA') DEFAULT 'PAUSED',
  `lead_id` int(9) unsigned NOT NULL,
  `uniqueid` varchar(20) NOT NULL DEFAULT '',
  `callerid` varchar(20) NOT NULL DEFAULT '',
  `channel` varchar(100) DEFAULT NULL,
  `phone_code` varchar(10) DEFAULT NULL,
  `phone_number` varchar(12) DEFAULT NULL,
  `call_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `call_type` enum('IN','OUT','OUTBALANCE') DEFAULT 'OUT',
  `stage` varchar(20) DEFAULT 'START',
  `last_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `alt_dial` enum('NONE','MAIN','ALT','ADDR3','AFFAP1','AFFAP2','AFFAP3','AFFAP4','AFFAP5','AFFAP6','AFFAP7','AFFAP8','AFFAP9') DEFAULT 'NONE',
  PRIMARY KEY (`auto_call_id`),
  KEY `uniqueid` (`uniqueid`),
  KEY `callerid` (`callerid`),
  KEY `call_time` (`call_time`),
  KEY `last_update_time` (`last_update_time`)
);

CREATE TABLE `osdial_call_times` (
  `call_time_id` varchar(10) NOT NULL,
  `call_time_name` varchar(30) NOT NULL,
  `call_time_comments` varchar(255) DEFAULT '',
  `ct_default_start` smallint(4) unsigned NOT NULL DEFAULT '900',
  `ct_default_stop` smallint(4) unsigned NOT NULL DEFAULT '2100',
  `ct_sunday_start` smallint(4) unsigned DEFAULT '0',
  `ct_sunday_stop` smallint(4) unsigned DEFAULT '0',
  `ct_monday_start` smallint(4) unsigned DEFAULT '0',
  `ct_monday_stop` smallint(4) unsigned DEFAULT '0',
  `ct_tuesday_start` smallint(4) unsigned DEFAULT '0',
  `ct_tuesday_stop` smallint(4) unsigned DEFAULT '0',
  `ct_wednesday_start` smallint(4) unsigned DEFAULT '0',
  `ct_wednesday_stop` smallint(4) unsigned DEFAULT '0',
  `ct_thursday_start` smallint(4) unsigned DEFAULT '0',
  `ct_thursday_stop` smallint(4) unsigned DEFAULT '0',
  `ct_friday_start` smallint(4) unsigned DEFAULT '0',
  `ct_friday_stop` smallint(4) unsigned DEFAULT '0',
  `ct_saturday_start` smallint(4) unsigned DEFAULT '0',
  `ct_saturday_stop` smallint(4) unsigned DEFAULT '0',
  `ct_state_call_times` text,
  `use_recycle_gap` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`call_time_id`)
);

CREATE TABLE `osdial_callbacks` (
  `callback_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(9) unsigned NOT NULL DEFAULT '0',
  `list_id` bigint(14) unsigned DEFAULT NULL,
  `campaign_id` varchar(20) DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT '',
  `entry_time` datetime DEFAULT NULL,
  `callback_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user` varchar(20) DEFAULT NULL,
  `recipient` enum('USERONLY','ANYONE') DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `user_group` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`callback_id`),
  KEY `lead_id` (`lead_id`),
  KEY `status` (`status`),
  KEY `callback_time` (`callback_time`)
);

CREATE TABLE `osdial_campaign_agent_stats` (
  `campaign_id` varchar(20) NOT NULL,
  `user` varchar(20) NOT NULL DEFAULT '',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `manual_dial_new_today` int(9) unsigned DEFAULT '0',
  `calls_today` int(9) unsigned DEFAULT '0',
  `answers_today` int(9) unsigned DEFAULT '0',
  `calls_hour` int(9) unsigned DEFAULT '0',
  `answers_hour` int(9) unsigned DEFAULT '0',
  `calls_halfhour` int(9) unsigned DEFAULT '0',
  `answers_halfhour` int(9) unsigned DEFAULT '0',
  `calls_fivemin` int(9) unsigned DEFAULT '0',
  `answers_fivemin` int(9) unsigned DEFAULT '0',
  `calls_onemin` int(9) unsigned DEFAULT '0',
  `answers_onemin` int(9) unsigned DEFAULT '0',
  `status_category_1` varchar(20) DEFAULT NULL,
  `status_category_count_1` int(9) unsigned DEFAULT '0',
  `status_category_2` varchar(20) DEFAULT NULL,
  `status_category_count_2` int(9) unsigned DEFAULT '0',
  `status_category_3` varchar(20) DEFAULT NULL,
  `status_category_count_3` int(9) unsigned DEFAULT '0',
  `status_category_4` varchar(20) DEFAULT NULL,
  `status_category_count_4` int(9) unsigned DEFAULT '0',
  `status_category_hour_count_1` int(9) unsigned DEFAULT '0',
  `status_category_hour_count_2` int(9) unsigned DEFAULT '0',
  `status_category_hour_count_3` int(9) unsigned DEFAULT '0',
  `status_category_hour_count_4` int(9) unsigned DEFAULT '0',
  PRIMARY KEY (`campaign_id`,`user`)
);

CREATE TABLE `osdial_campaign_agents` (
  `user` varchar(20) NOT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `campaign_rank` tinyint(1) DEFAULT '0',
  `campaign_weight` tinyint(1) DEFAULT '0',
  `calls_today` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`campaign_id`,`user`)
);

CREATE TABLE `osdial_campaign_cid_areacodes` (
  `campaign_id` varchar(20) NOT NULL,
  `areacode` varchar(4) NOT NULL,
  `cid_number` varchar(20) DEFAULT NULL,
  `cid_name` varchar(40) NOT NULL,
  PRIMARY KEY (`campaign_id`,`areacode`)
);

CREATE TABLE `osdial_campaign_email_blacklist` (
  `campaign_id` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`campaign_id`,`email`)
);

CREATE TABLE `osdial_campaign_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned NOT NULL,
  `name` varchar(15) NOT NULL,
  `description` varchar(50) NOT NULL,
  `options` varchar(255) NOT NULL,
  `length` int(2) NOT NULL DEFAULT '22',
  `priority` int(10) unsigned NOT NULL,
  `deleted` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

INSERT INTO `osdial_campaign_fields` VALUES (1,1,'NAME','Name on Card','',22,1,0);
INSERT INTO `osdial_campaign_fields` VALUES (2,1,'ADDRESS','Billing Address','',22,2,0);
INSERT INTO `osdial_campaign_fields` VALUES (3,1,'CITY','City','',22,3,0);
INSERT INTO `osdial_campaign_fields` VALUES (4,1,'STATE','State','',3,4,0);
INSERT INTO `osdial_campaign_fields` VALUES (5,1,'ZIP','ZIP','',10,5,0);
INSERT INTO `osdial_campaign_fields` VALUES (6,1,'TYPE','CC Type','VISA,Master Card,American Express',22,6,0);
INSERT INTO `osdial_campaign_fields` VALUES (7,1,'NUMBER','CC Number','',22,7,0);
INSERT INTO `osdial_campaign_fields` VALUES (8,1,'CVV','CVV Code','',5,8,0);
INSERT INTO `osdial_campaign_fields` VALUES (9,2,'AFFAP1','Alt Phone 4','',15,1,0);
INSERT INTO `osdial_campaign_fields` VALUES (10,2,'AFFAP2','Alt Phone 5','',15,2,0);
INSERT INTO `osdial_campaign_fields` VALUES (11,2,'AFFAP3','Alt Phone 6','',15,3,0);
INSERT INTO `osdial_campaign_fields` VALUES (12,2,'AFFAP4','Alt Phone 7','',15,4,0);
INSERT INTO `osdial_campaign_fields` VALUES (13,2,'AFFAP5','Alt Phone 8','',15,5,0);
INSERT INTO `osdial_campaign_fields` VALUES (14,2,'AFFAP6','Alt Phone 9','',15,6,0);
INSERT INTO `osdial_campaign_fields` VALUES (15,2,'AFFAP7','Alt Phone 10','',15,7,0);

CREATE TABLE `osdial_campaign_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `campaigns` varchar(255) NOT NULL,
  `name` varchar(15) NOT NULL,
  `description` varchar(50) NOT NULL,
  `description2` varchar(50) NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `deleted` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

INSERT INTO `osdial_campaign_forms` VALUES (1,'ALL','CREDITCARD','Details as They Appear on CC','(if different)',1,0);
INSERT INTO `osdial_campaign_forms` VALUES (2,'ALL','AFFAP','Alternate Phone Numbers','',999,0);

CREATE TABLE `osdial_campaign_hotkeys` (
  `status` varchar(6) NOT NULL,
  `hotkey` varchar(1) NOT NULL,
  `status_name` varchar(30) DEFAULT NULL,
  `selectable` enum('Y','N') DEFAULT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `xfer_exten` varchar(20) DEFAULT '',
  PRIMARY KEY (`campaign_id`,`hotkey`)
);

CREATE TABLE `osdial_campaign_server_stats` (
  `campaign_id` varchar(20) NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `local_trunk_shortage` smallint(5) unsigned DEFAULT '0',
  `calls_onemin` int(9) unsigned NOT NULL DEFAULT '0',
  `answers_onemin` int(9) unsigned NOT NULL DEFAULT '0',
  `drops_onemin` int(9) unsigned NOT NULL DEFAULT '0',
  `amd_onemin` int(9) unsigned NOT NULL DEFAULT '0',
  `failed_onemin` int(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaign_id`,`server_ip`)
);

CREATE TABLE `osdial_campaign_stats` (
  `campaign_id` varchar(20) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dialable_leads` int(9) unsigned DEFAULT '0',
  `calls_today` int(9) unsigned DEFAULT '0',
  `answers_today` int(9) unsigned DEFAULT '0',
  `drops_today` int(9) unsigned DEFAULT '0',
  `drops_today_pct` varchar(6) DEFAULT '0',
  `drops_answers_today_pct` varchar(6) DEFAULT '0',
  `calls_hour` int(9) unsigned DEFAULT '0',
  `answers_hour` int(9) unsigned DEFAULT '0',
  `drops_hour` int(9) unsigned DEFAULT '0',
  `drops_hour_pct` varchar(6) DEFAULT '0',
  `calls_halfhour` int(9) unsigned DEFAULT '0',
  `answers_halfhour` int(9) unsigned DEFAULT '0',
  `drops_halfhour` int(9) unsigned DEFAULT '0',
  `drops_halfhour_pct` varchar(6) DEFAULT '0',
  `calls_fivemin` int(9) unsigned DEFAULT '0',
  `answers_fivemin` int(9) unsigned DEFAULT '0',
  `drops_fivemin` int(9) unsigned DEFAULT '0',
  `drops_fivemin_pct` varchar(6) DEFAULT '0',
  `calls_onemin` int(9) unsigned DEFAULT '0',
  `answers_onemin` int(9) unsigned DEFAULT '0',
  `drops_onemin` int(9) unsigned DEFAULT '0',
  `drops_onemin_pct` varchar(6) DEFAULT '0',
  `differential_onemin` varchar(20) DEFAULT '0',
  `agents_average_onemin` varchar(20) DEFAULT '0',
  `balance_trunk_fill` smallint(5) unsigned DEFAULT '0',
  `status_category_1` varchar(20) DEFAULT NULL,
  `status_category_count_1` int(9) unsigned DEFAULT '0',
  `status_category_2` varchar(20) DEFAULT NULL,
  `status_category_count_2` int(9) unsigned DEFAULT '0',
  `status_category_3` varchar(20) DEFAULT NULL,
  `status_category_count_3` int(9) unsigned DEFAULT '0',
  `status_category_4` varchar(20) DEFAULT NULL,
  `status_category_count_4` int(9) unsigned DEFAULT '0',
  `status_category_hour_count_1` int(9) unsigned DEFAULT '0',
  `status_category_hour_count_2` int(9) unsigned DEFAULT '0',
  `status_category_hour_count_3` int(9) unsigned DEFAULT '0',
  `status_category_hour_count_4` int(9) unsigned DEFAULT '0',
  `recycle_total` int(9) unsigned DEFAULT '0',
  `recycle_sched` int(9) unsigned DEFAULT '0',
  `amd_onemin` int(9) unsigned NOT NULL DEFAULT '0',
  `failed_onemin` int(9) unsigned NOT NULL DEFAULT '0',
  `agents_paused` int(9) unsigned NOT NULL DEFAULT '0',
  `agents_incall` int(9) unsigned NOT NULL DEFAULT '0',
  `agents_waiting` int(9) unsigned NOT NULL DEFAULT '0',
  `waiting_calls` int(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaign_id`)
);

CREATE TABLE `osdial_campaign_statuses` (
  `status` varchar(6) NOT NULL,
  `status_name` varchar(30) DEFAULT NULL,
  `selectable` enum('Y','N') DEFAULT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `human_answered` enum('Y','N') DEFAULT 'Y',
  `category` varchar(20) DEFAULT 'UNDEFINED',
  PRIMARY KEY (`campaign_id`,`status`)
);

CREATE TABLE `osdial_campaigns` (
  `campaign_id` varchar(20) NOT NULL,
  `campaign_name` varchar(40) DEFAULT NULL,
  `active` enum('Y','N') DEFAULT NULL,
  `dial_status_a` varchar(6) DEFAULT NULL,
  `dial_status_b` varchar(6) DEFAULT NULL,
  `dial_status_c` varchar(6) DEFAULT NULL,
  `dial_status_d` varchar(6) DEFAULT NULL,
  `dial_status_e` varchar(6) DEFAULT NULL,
  `lead_order` varchar(20) DEFAULT NULL,
  `park_ext` varchar(10) DEFAULT NULL,
  `park_file_name` varchar(10) DEFAULT NULL,
  `web_form_address` varchar(2000) DEFAULT '/osdial/agent/webform_redirect.php',
  `allow_closers` enum('Y','N') DEFAULT NULL,
  `hopper_level` int(8) unsigned DEFAULT '1',
  `auto_dial_level` varchar(6) DEFAULT '0',
  `next_agent_call` enum('random','oldest_call_start','oldest_call_finish','campaign_rank','overall_user_level','fewest_calls') DEFAULT 'oldest_call_finish',
  `local_call_time` varchar(10) DEFAULT '9am-9pm',
  `voicemail_ext` varchar(10) DEFAULT NULL,
  `dial_timeout` tinyint(3) unsigned DEFAULT '60',
  `dial_prefix` varchar(20) DEFAULT '9',
  `campaign_cid` varchar(10) DEFAULT '0000000000',
  `campaign_vdad_exten` varchar(20) DEFAULT '8365',
  `campaign_rec_exten` varchar(20) DEFAULT '8309',
  `campaign_recording` enum('NEVER','ONDEMAND','ALLCALLS','ALLFORCE') DEFAULT 'ONDEMAND',
  `campaign_rec_filename` varchar(50) DEFAULT 'FULLDATE_CUSTPHONE',
  `campaign_script` varchar(20) DEFAULT NULL,
  `get_call_launch` enum('NONE','SCRIPT','WEBFORM','WEBFORM2') DEFAULT 'NONE',
  `am_message_exten` varchar(20) DEFAULT NULL,
  `amd_send_to_vmx` enum('Y','N','CUSTOM1','CUSTOM2') DEFAULT 'N',
  `xferconf_a_dtmf` varchar(50) DEFAULT NULL,
  `xferconf_a_number` varchar(50) DEFAULT NULL,
  `xferconf_b_dtmf` varchar(50) DEFAULT NULL,
  `xferconf_b_number` varchar(50) DEFAULT NULL,
  `alt_number_dialing` enum('Y','N') DEFAULT 'N',
  `scheduled_callbacks` enum('Y','N') DEFAULT 'N',
  `lead_filter_id` varchar(20) DEFAULT NULL,
  `drop_call_seconds` tinyint(3) unsigned DEFAULT '5',
  `safe_harbor_message` enum('Y','N') DEFAULT 'N',
  `safe_harbor_exten` varchar(20) DEFAULT '8307',
  `display_dialable_count` enum('Y','N') DEFAULT 'Y',
  `wrapup_seconds` smallint(3) unsigned DEFAULT '0',
  `wrapup_message` varchar(255) DEFAULT 'Wrapup Call',
  `closer_campaigns` text,
  `use_internal_dnc` enum('Y','N') DEFAULT 'N',
  `allcalls_delay` smallint(3) unsigned DEFAULT '0',
  `omit_phone_code` enum('Y','N') DEFAULT 'N',
  `dial_method` enum('MANUAL','RATIO','ADAPT_HARD_LIMIT','ADAPT_TAPERED','ADAPT_AVERAGE') DEFAULT 'MANUAL',
  `available_only_ratio_tally` enum('Y','N') DEFAULT 'N',
  `adaptive_dropped_percentage` smallint(3) DEFAULT '3',
  `adaptive_maximum_level` varchar(6) DEFAULT '3.0',
  `adaptive_latest_server_time` varchar(4) DEFAULT '2100',
  `adaptive_intensity` varchar(6) DEFAULT '0',
  `adaptive_dl_diff_target` smallint(3) DEFAULT '0',
  `concurrent_transfers` enum('AUTO','1','2','3','4','5','6','7','8','9','10') DEFAULT 'AUTO',
  `auto_alt_dial` enum('NONE','ALT_ONLY','ADDR3_ONLY','ALT_AND_ADDR3','ALT_ADDR3_AND_AFFAP') DEFAULT 'NONE',
  `auto_alt_dial_statuses` varchar(255) DEFAULT ' B N NA DC -',
  `agent_pause_codes_active` enum('Y','N') DEFAULT 'N',
  `campaign_description` varchar(255) DEFAULT NULL,
  `campaign_changedate` datetime DEFAULT NULL,
  `campaign_stats_refresh` enum('Y','N') DEFAULT 'N',
  `campaign_logindate` datetime DEFAULT NULL,
  `dial_statuses` varchar(255) DEFAULT ' NEW -',
  `disable_alter_custdata` enum('Y','N') DEFAULT 'N',
  `no_hopper_leads_logins` enum('Y','N') DEFAULT 'N',
  `list_order_mix` varchar(20) DEFAULT 'DISABLED',
  `campaign_allow_inbound` enum('Y','N') DEFAULT 'N',
  `manual_dial_list_id` bigint(14) unsigned DEFAULT '999',
  `default_xfer_group` varchar(20) DEFAULT '---NONE---',
  `xfer_groups` text,
  `web_form_address2` varchar(2000) DEFAULT '/osdial/agent/webform_redirect.php',
  `allow_tab_switch` enum('Y','N') DEFAULT 'Y',
  `answers_per_hour_limit` int(8) DEFAULT '0',
  `campaign_call_time` varchar(10) DEFAULT '',
  `preview_force_dial_time` tinyint(3) unsigned DEFAULT '10',
  `manual_preview_default` enum('Y','N') DEFAULT 'Y',
  `web_form_extwindow` enum('Y','N') DEFAULT 'N',
  `web_form2_extwindow` enum('Y','N') DEFAULT 'N',
  `submit_method` enum('NORMAL','WEBFORM1','WEBFORM2') DEFAULT 'NORMAL',
  `use_custom2_callerid` enum('Y','N') DEFAULT 'N',
  `campaign_lastcall` datetime DEFAULT '2008-01-01 00:00:00',
  `campaign_cid_name` varchar(40) DEFAULT '',
  `xfer_cid_mode` enum('CAMPAIGN','PHONE','LEAD','LEAD_CUSTOM2','LEAD_CUSTOM1') DEFAULT 'CAMPAIGN',
  `use_cid_areacode_map` enum('Y','N') DEFAULT 'N',
  `carrier_id` int(11) DEFAULT '0',
  `email_templates` text,
  `disable_manual_dial` enum('N','Y') DEFAULT 'N',
  `hide_xfer_local_closer` enum('Y','N') DEFAULT 'N',
  `hide_xfer_dial_override` enum('Y','N') DEFAULT 'N',
  `hide_xfer_hangup_xfer` enum('Y','N') DEFAULT 'N',
  `hide_xfer_leave_3way` enum('Y','N') DEFAULT 'N',
  `hide_xfer_dial_with` enum('Y','N') DEFAULT 'N',
  `hide_xfer_hangup_both` enum('Y','N') DEFAULT 'N',
  `hide_xfer_blind_xfer` enum('Y','N') DEFAULT 'N',
  `hide_xfer_park_dial` enum('Y','N') DEFAULT 'N',
  `hide_xfer_blind_vmail` enum('Y','N') DEFAULT 'N',
  `allow_md_hopperlist` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`campaign_id`)
);

CREATE TABLE `osdial_campaigns_list_mix` (
  `vcl_id` varchar(20) NOT NULL,
  `vcl_name` varchar(50) DEFAULT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `list_mix_container` text,
  `mix_method` enum('EVEN_MIX','IN_ORDER','RANDOM') DEFAULT 'IN_ORDER',
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'INACTIVE',
  PRIMARY KEY (`vcl_id`),
  KEY `campaign_id` (`campaign_id`)
);

CREATE TABLE `osdial_carrier_dids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL DEFAULT '0',
  `did` char(50) NOT NULL DEFAULT '',
  `did_action` enum('INGROUP','PHONE','EXTENSION','VOICEMAIL') DEFAULT 'EXTENSION',
  `phone` varchar(100) DEFAULT '',
  `extension` varchar(100) DEFAULT '9999',
  `extension_context` varchar(20) DEFAULT 'default',
  `voicemail` varchar(20) DEFAULT '1234',
  `ingroup` varchar(20) NOT NULL DEFAULT '',
  `server_allocation` enum('LO','LB','SO') DEFAULT 'LO',
  `park_file` varchar(100) NOT NULL DEFAULT 'park',
  `lookup_method` enum('CID','CIDLOOKUP','CIDLOOKUPRL','CIDLOOKUPRC','CLOSER','ANI','ANILOOKUP','ANILOOKUPRL','3DIGITID','4DIGITID','5DIGITID','10DIGITID') DEFAULT 'CID',
  `initial_status` varchar(6) NOT NULL DEFAULT 'INBND',
  `default_list_id` varchar(15) NOT NULL DEFAULT '998',
  `default_phone_code` varchar(5) NOT NULL DEFAULT '1',
  `search_campaign` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `carrier_id` (`carrier_id`,`did`)
);

CREATE TABLE `osdial_carrier_servers` (
  `carrier_id` int(11) NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `protocol_config` text NOT NULL,
  `registrations` text NOT NULL,
  `dialplan` text NOT NULL,
  PRIMARY KEY (`carrier_id`,`server_ip`)
);

CREATE TABLE `osdial_carriers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `active` enum('Y','N') DEFAULT 'N',
  `selectable` enum('Y','N') DEFAULT 'Y',
  `protocol` enum('SIP','IAX2','DAHDI','Zap','EXTERNAL') DEFAULT 'SIP',
  `protocol_config` text,
  `registrations` text,
  `dialplan` text,
  `failover_id` int(11) DEFAULT '0',
  `failover_condition` enum('CHANUNAVAIL','CONGESTION','BOTH') DEFAULT 'CHANUNAVAIL',
  `strip_msd` enum('Y','N') DEFAULT 'N',
  `allow_international` enum('Y','N') DEFAULT 'N',
  `default_callerid` varchar(20) DEFAULT '0000000000',
  `default_areacode` varchar(3) DEFAULT '321',
  `default_prefix` varchar(1) DEFAULT '9',
  PRIMARY KEY (`id`)
);

CREATE TABLE `osdial_closer_log` (
  `closecallid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(9) unsigned NOT NULL,
  `list_id` bigint(14) unsigned DEFAULT NULL,
  `campaign_id` varchar(20) DEFAULT NULL,
  `call_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_epoch` int(10) unsigned DEFAULT NULL,
  `end_epoch` int(10) unsigned DEFAULT NULL,
  `length_in_sec` int(10) DEFAULT NULL,
  `status` varchar(6) DEFAULT NULL,
  `phone_code` varchar(10) DEFAULT NULL,
  `phone_number` varchar(12) NOT NULL DEFAULT '',
  `user` varchar(20) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `processed` enum('Y','N') DEFAULT NULL,
  `queue_seconds` decimal(7,2) DEFAULT '0.00',
  `user_group` varchar(20) DEFAULT NULL,
  `term_reason` enum('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE','NOAGENTS','NOAGENTSAVAILABLE') DEFAULT 'NONE',
  `uniqueid` varchar(20) NOT NULL DEFAULT '',
  `callerid` varchar(20) DEFAULT '',
  PRIMARY KEY (`closecallid`),
  KEY `lead_id` (`lead_id`),
  KEY `call_date` (`call_date`),
  KEY `phone_number` (`phone_number`),
  KEY `date_user` (`call_date`,`user`),
  KEY `uniqueid` (`uniqueid`)
);

CREATE TABLE `osdial_companies` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(100) NOT NULL DEFAULT '',
  `status` enum('INACTIVE','ACTIVE','SUSPENDED','TERMINATED') NOT NULL DEFAULT 'INACTIVE',
  `enable_campaign_ivr` enum('0','1') DEFAULT '0',
  `enable_campaign_listmix` enum('0','1') DEFAULT '1',
  `export_leads` enum('0','1') DEFAULT '1',
  `enable_scripts` enum('0','1') DEFAULT '1',
  `enable_filters` enum('0','1') DEFAULT '0',
  `enable_ingroups` enum('0','1') DEFAULT '0',
  `enable_external_agents` enum('0','1') DEFAULT '0',
  `enable_system_calltimes` enum('0','1') DEFAULT '0',
  `enable_system_phones` enum('0','1') DEFAULT '1',
  `enable_system_conferences` enum('0','1') DEFAULT '0',
  `enable_system_servers` enum('0','1') DEFAULT '0',
  `enable_system_statuses` enum('0','1') DEFAULT '0',
  `api_access` enum('0','1') NOT NULL DEFAULT '0',
  `dnc_method` enum('SYSTEM','COMPANY','BOTH') DEFAULT 'BOTH',
  `default_server_ip` varchar(15) DEFAULT '127.0.0.1',
  `default_local_gmt` varchar(6) DEFAULT '-5.00',
  `default_ext_context` varchar(20) NOT NULL DEFAULT 'osdial',
  `enable_system_carriers` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`id`)
);

CREATE TABLE `osdial_conferences` (
  `conf_exten` int(7) unsigned NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `leave_3way` enum('0','1') DEFAULT '0',
  `leave_3way_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`conf_exten`,`server_ip`)
);

CREATE TABLE `osdial_cpa_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `callerid` varchar(20) NOT NULL DEFAULT '',
  `uniqueid` varchar(20) NOT NULL DEFAULT '',
  `lead_id` int(9) unsigned NOT NULL DEFAULT '0',
  `server_ip` varchar(15) NOT NULL,
  `channel` varchar(100) NOT NULL,
  `status` enum('NEW','PROCESSED') DEFAULT 'NEW',
  `cpa_result` varchar(50) NOT NULL DEFAULT 'Voice',
  `cpa_detailed_result` varchar(255) NOT NULL DEFAULT '',
  `cpa_call_id` varchar(100) NOT NULL DEFAULT '',
  `cpa_reference_id` varchar(100) NOT NULL DEFAULT '',
  `cpa_campaign_name` varchar(255) NOT NULL DEFAULT '',
  `seconds` decimal(7,2) DEFAULT '0.00',
  `event_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `callerid` (`callerid`),
  KEY `uniqueid` (`uniqueid`),
  KEY `lead_id` (`lead_id`)
);

CREATE TABLE `osdial_dnc` (
  `phone_number` varchar(12) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`phone_number`)
);

CREATE TABLE `osdial_dnc_company` (
  `company_id` tinyint(3) unsigned NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`,`phone_number`)
);

CREATE TABLE `osdial_email_templates` (
  `et_id` varchar(20) NOT NULL DEFAULT '',
  `et_name` varchar(50) DEFAULT '',
  `et_comments` varchar(255) DEFAULT '',
  `et_host` varchar(255) DEFAULT 'localhost',
  `et_port` varchar(5) DEFAULT '25',
  `et_user` varchar(255) DEFAULT '',
  `et_pass` varchar(255) DEFAULT '',
  `et_from` varchar(255) DEFAULT '',
  `et_subject` varchar(255) DEFAULT '',
  `et_body_html` text,
  `et_body_text` text,
  `active` enum('Y','N') DEFAULT NULL,
  `et_send_action` enum('ONDEMAND','ALL','ALLFORCE') DEFAULT 'ONDEMAND',
  PRIMARY KEY (`et_id`)
);

CREATE TABLE `osdial_hopper` (
  `hopper_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(9) unsigned NOT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `status` enum('READY','QUEUE','INCALL','DONE','HOLD','API') DEFAULT 'READY',
  `user` varchar(20) DEFAULT NULL,
  `list_id` bigint(14) unsigned NOT NULL,
  `gmt_offset_now` decimal(4,2) DEFAULT '0.00',
  `state` varchar(2) DEFAULT '',
  `alt_dial` enum('NONE','ALT','ADDR3','AFFAP1','AFFAP2','AFFAP3','AFFAP4','AFFAP5','AFFAP6','AFFAP7','AFFAP8','AFFAP9') DEFAULT 'NONE',
  `priority` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`hopper_id`),
  KEY `lead_id` (`lead_id`)
);

CREATE TABLE `osdial_inbound_group_agents` (
  `user` varchar(20) NOT NULL DEFAULT '',
  `group_id` varchar(20) NOT NULL DEFAULT '',
  `group_rank` tinyint(1) DEFAULT '0',
  `group_weight` tinyint(1) DEFAULT '0',
  `calls_today` smallint(5) unsigned DEFAULT '0',
  KEY `group_id` (`group_id`),
  KEY `user` (`user`)
);

CREATE TABLE `osdial_inbound_groups` (
  `group_id` varchar(20) NOT NULL,
  `group_name` varchar(30) DEFAULT NULL,
  `group_color` varchar(7) DEFAULT NULL,
  `active` enum('Y','N') DEFAULT NULL,
  `web_form_address` varchar(2000) DEFAULT '/osdial/agent/webform_redirect.php',
  `voicemail_ext` varchar(10) DEFAULT NULL,
  `next_agent_call` enum('random','oldest_call_start','oldest_call_finish','overall_user_level','inbound_group_rank','campaign_rank','fewest_calls','fewest_calls_campaign') DEFAULT 'oldest_call_finish',
  `fronter_display` enum('Y','N') DEFAULT 'Y',
  `ingroup_script` varchar(20) DEFAULT NULL,
  `get_call_launch` enum('NONE','SCRIPT','WEBFORM','WEBFORM2') DEFAULT 'NONE',
  `xferconf_a_dtmf` varchar(50) DEFAULT NULL,
  `xferconf_a_number` varchar(50) DEFAULT NULL,
  `xferconf_b_dtmf` varchar(50) DEFAULT NULL,
  `xferconf_b_number` varchar(50) DEFAULT NULL,
  `drop_call_seconds` smallint(4) unsigned DEFAULT '360',
  `drop_message` enum('Y','N') DEFAULT 'N',
  `drop_exten` varchar(20) DEFAULT '8307',
  `call_time_id` varchar(20) DEFAULT '24hours',
  `after_hours_action` enum('HANGUP','MESSAGE','EXTENSION','VOICEMAIL') DEFAULT 'MESSAGE',
  `after_hours_message_filename` varchar(50) DEFAULT 'vm-goodbye',
  `after_hours_exten` varchar(20) DEFAULT '8300',
  `after_hours_voicemail` varchar(20) DEFAULT NULL,
  `welcome_message_filename` varchar(50) DEFAULT '---NONE---',
  `moh_context` varchar(50) DEFAULT 'default',
  `onhold_prompt_filename` varchar(50) DEFAULT 'generic_hold',
  `prompt_interval` smallint(5) unsigned DEFAULT '60',
  `agent_alert_exten` varchar(20) DEFAULT '8304',
  `agent_alert_delay` int(6) DEFAULT '1000',
  `default_xfer_group` varchar(20) DEFAULT '---NONE---',
  `web_form_address2` varchar(2000) DEFAULT '/osdial/agent/webform_redirect.php',
  `allow_tab_switch` enum('Y','N') DEFAULT 'Y',
  `web_form_extwindow` enum('Y','N') DEFAULT 'Y',
  `web_form2_extwindow` enum('Y','N') DEFAULT 'Y',
  `drop_trigger` enum('CALL_SECONDS_TIMEOUT','NO_AGENTS_CONNECTED','NO_AGENTS_AVAILABLE') DEFAULT 'CALL_SECONDS_TIMEOUT',
  `allow_multicall` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`group_id`)
);

CREATE TABLE `osdial_ivr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) DEFAULT NULL,
  `announcement` varchar(255) DEFAULT 'hello-world',
  `repeat_loops` int(4) DEFAULT '3',
  `wait_loops` int(4) DEFAULT '5',
  `wait_timeout` int(4) DEFAULT '500',
  `answered_status` varchar(6) DEFAULT 'VPU',
  `virtual_agents` int(3) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'INACTIVE',
  `timeout_action` varchar(1) DEFAULT '',
  `reserve_agents` int(3) DEFAULT '2',
  `allow_inbound` enum('Y','N') DEFAULT 'Y',
  `allow_agent_extensions` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_id` (`campaign_id`)
);

CREATE TABLE `osdial_ivr_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ivr_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `keypress` char(1) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `action_data` text,
  `last_state` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outbound_ivr_id` (`ivr_id`,`parent_id`,`keypress`)
);

CREATE TABLE `osdial_lead_filters` (
  `lead_filter_id` varchar(20) NOT NULL DEFAULT '',
  `lead_filter_name` varchar(30) NOT NULL,
  `lead_filter_comments` varchar(255) DEFAULT NULL,
  `lead_filter_sql` text,
  PRIMARY KEY (`lead_filter_id`)
);

CREATE TABLE `osdial_lead_recycle` (
  `recycle_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` varchar(20) NOT NULL,
  `status` varchar(6) NOT NULL,
  `attempt_delay` int(11) unsigned DEFAULT '1800',
  `attempt_maximum` int(5) unsigned DEFAULT '32',
  `active` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`recycle_id`),
  KEY `campaign_id` (`campaign_id`)
);

CREATE TABLE `osdial_list` (
  `lead_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `entry_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(6) NOT NULL DEFAULT '',
  `user` varchar(20) NOT NULL DEFAULT '',
  `vendor_lead_code` varchar(20) NOT NULL DEFAULT '',
  `source_id` varchar(6) NOT NULL DEFAULT '',
  `list_id` bigint(14) unsigned NOT NULL DEFAULT '0',
  `gmt_offset_now` decimal(4,2) NOT NULL DEFAULT '0.00',
  `called_since_last_reset` varchar(4) NOT NULL DEFAULT 'N',
  `phone_code` varchar(10) NOT NULL DEFAULT '',
  `phone_number` varchar(12) NOT NULL DEFAULT '',
  `title` varchar(4) DEFAULT NULL,
  `first_name` varchar(30) NOT NULL DEFAULT '',
  `middle_initial` varchar(1) DEFAULT NULL,
  `last_name` varchar(30) NOT NULL DEFAULT '',
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(2) NOT NULL DEFAULT '',
  `province` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) NOT NULL DEFAULT '',
  `country_code` varchar(3) NOT NULL DEFAULT '',
  `gender` enum('M','F','') NOT NULL DEFAULT '',
  `date_of_birth` date NOT NULL DEFAULT '0000-00-00',
  `alt_phone` varchar(12) NOT NULL DEFAULT '',
  `email` varchar(70) NOT NULL DEFAULT '',
  `custom1` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `called_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `custom2` varchar(255) DEFAULT NULL,
  `external_key` varchar(100) NOT NULL DEFAULT '',
  `last_local_call_time` datetime NOT NULL DEFAULT '2008-01-01 00:00:00',
  `cost` float NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL,
  `organization` varchar(255) DEFAULT '',
  `organization_title` varchar(255) DEFAULT '',
  PRIMARY KEY (`lead_id`),
  KEY `phone_number` (`phone_number`),
  KEY `list_id` (`list_id`),
  KEY `called_since_last_reset` (`called_since_last_reset`(1)),
  KEY `status` (`status`),
  KEY `gmt_offset_now` (`gmt_offset_now`),
  KEY `postal_code` (`postal_code`),
  KEY `list_phone` (`list_id`,`phone_number`),
  KEY `list_status` (`list_id`,`status`),
  KEY `last_local_call_time` (`last_local_call_time`),
  KEY `entry_date` (`entry_date`),
  KEY `modify_date` (`modify_date`),
  KEY `area_code` (`phone_number`(3)),
  KEY `last_name` (`last_name`(3)),
  KEY `external_key` (`external_key`)
);

CREATE TABLE `osdial_list_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(10) unsigned NOT NULL,
  `field_id` int(10) unsigned NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_id_2` (`lead_id`,`field_id`),
  KEY `lead_id` (`lead_id`)
);

CREATE TABLE `osdial_list_pins` (
  `pins_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `entry_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `phone_number` varchar(12) NOT NULL DEFAULT '',
  `lead_id` int(9) unsigned NOT NULL DEFAULT '0',
  `campaign_id` varchar(20) DEFAULT NULL,
  `product_code` varchar(20) DEFAULT NULL,
  `user` varchar(20) DEFAULT NULL,
  `digits` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`pins_id`),
  KEY `lead_id` (`lead_id`),
  KEY `phone_number` (`phone_number`),
  KEY `entry_time` (`entry_time`)
);

CREATE TABLE `osdial_lists` (
  `list_id` bigint(14) unsigned NOT NULL,
  `list_name` varchar(30) DEFAULT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `active` enum('Y','N') DEFAULT NULL,
  `list_description` varchar(255) DEFAULT NULL,
  `list_changedate` datetime DEFAULT NULL,
  `list_lastcalldate` datetime DEFAULT NULL,
  `scrub_dnc` enum('Y','N') DEFAULT 'N',
  `scrub_last` datetime DEFAULT NULL,
  `scrub_info` varchar(20) DEFAULT '',
  `cost` float DEFAULT '0',
  `web_form_address` varchar(2000) DEFAULT '',
  `web_form_address2` varchar(2000) DEFAULT '',
  `list_script` varchar(20) DEFAULT '',
  PRIMARY KEY (`list_id`),
  KEY `campaign` (`campaign_id`)
);

INSERT INTO `osdial_lists` VALUES (10,'PBX-IN','PBX-IN',NULL,'PBX/External Inbound',NULL,NULL,'N',NULL,'',0,'','','');
INSERT INTO `osdial_lists` VALUES (11,'PBX-OUT','PBX-OUT',NULL,'PBX/External Outbound',NULL,NULL,'N',NULL,'',0,'','','');

CREATE TABLE `osdial_live_agents` (
  `live_agent_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(20) NOT NULL DEFAULT '',
  `server_ip` varchar(15) NOT NULL,
  `conf_exten` varchar(20) DEFAULT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `status` enum('READY','QUEUE','INCALL','PAUSED','CLOSER') DEFAULT 'PAUSED',
  `lead_id` int(9) unsigned NOT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `uniqueid` varchar(20) NOT NULL DEFAULT '',
  `callerid` varchar(20) NOT NULL DEFAULT '',
  `channel` varchar(100) DEFAULT NULL,
  `random_id` int(8) unsigned NOT NULL DEFAULT '0',
  `last_call_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_call_finish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `closer_campaigns` text,
  `call_server_ip` varchar(15) DEFAULT NULL,
  `user_level` int(2) DEFAULT '0',
  `comments` varchar(20) DEFAULT NULL,
  `campaign_weight` tinyint(1) DEFAULT '0',
  `calls_today` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`live_agent_id`),
  KEY `random_id` (`random_id`),
  KEY `last_call_time` (`last_call_time`),
  KEY `last_update_time` (`last_update_time`),
  KEY `last_call_finish` (`last_call_finish`),
  KEY `uniqeuid` (`uniqueid`),
  KEY `callerid` (`callerid`),
  KEY `unique_caller` (`uniqueid`,`callerid`),
  KEY `us` (`user`),
  KEY `conf` (`conf_exten`),
  KEY `ussp` (`user`,`server_ip`),
  KEY `uscs` (`user`,`campaign_id`),
  KEY `usall1` (`user`,`server_ip`,`campaign_id`),
  KEY `usall2` (`user`,`server_ip`,`conf_exten`),
  KEY `usall3` (`user`,`server_ip`,`campaign_id`,`conf_exten`)
);

CREATE TABLE `osdial_live_inbound_agents` (
  `user` varchar(20) DEFAULT NULL,
  `group_id` varchar(20) NOT NULL DEFAULT '',
  `group_weight` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `calls_today` smallint(5) unsigned DEFAULT '0',
  `last_call_time` datetime DEFAULT NULL,
  `last_call_finish` datetime DEFAULT NULL,
  KEY `group_id` (`group_id`),
  KEY `group_weight` (`group_weight`)
);

CREATE TABLE `osdial_log` (
  `uniqueid` varchar(20) NOT NULL,
  `lead_id` int(9) unsigned NOT NULL,
  `list_id` bigint(14) unsigned DEFAULT NULL,
  `campaign_id` varchar(20) DEFAULT NULL,
  `call_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_epoch` int(10) unsigned DEFAULT NULL,
  `end_epoch` int(10) unsigned DEFAULT NULL,
  `length_in_sec` int(10) DEFAULT NULL,
  `status` varchar(6) DEFAULT NULL,
  `phone_code` varchar(10) DEFAULT NULL,
  `phone_number` varchar(12) DEFAULT NULL,
  `user` varchar(20) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `processed` enum('Y','N') DEFAULT NULL,
  `user_group` varchar(20) DEFAULT NULL,
  `term_reason` enum('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE') DEFAULT 'NONE',
  `server_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`uniqueid`),
  KEY `lead_id` (`lead_id`),
  KEY `call_date` (`call_date`)
);

CREATE TABLE `osdial_manager` (
  `man_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(20) NOT NULL DEFAULT '',
  `entry_date` datetime DEFAULT NULL,
  `status` enum('NEW','QUEUE','SENT','UPDATED','DEAD') DEFAULT NULL,
  `response` enum('Y','N') DEFAULT NULL,
  `server_ip` varchar(15) NOT NULL,
  `channel` varchar(100) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  `callerid` varchar(20) NOT NULL DEFAULT '',
  `cmd_line_b` varchar(100) DEFAULT NULL,
  `cmd_line_c` varchar(100) DEFAULT NULL,
  `cmd_line_d` varchar(100) DEFAULT NULL,
  `cmd_line_e` varchar(100) DEFAULT NULL,
  `cmd_line_f` varchar(100) DEFAULT NULL,
  `cmd_line_g` varchar(100) DEFAULT NULL,
  `cmd_line_h` varchar(100) DEFAULT NULL,
  `cmd_line_i` varchar(100) DEFAULT NULL,
  `cmd_line_j` varchar(100) DEFAULT NULL,
  `cmd_line_k` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`man_id`),
  KEY `callerid` (`callerid`),
  KEY `uniqueid` (`uniqueid`),
  KEY `serverstat` (`server_ip`,`status`),
  KEY `se_st_ed` (`server_ip`,`status`,`entry_date`),
  KEY `se_ci_st` (`server_ip`,`callerid`,`status`),
  KEY `se_ci` (`server_ip`,`callerid`),
  KEY `se_un_ci` (`server_ip`,`uniqueid`,`callerid`)
);

CREATE TABLE `osdial_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `mimetype` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `extension` varchar(20) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`)
);

CREATE TABLE `osdial_media_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `filedata` mediumblob,
  PRIMARY KEY (`id`,`filename`)
);

CREATE TABLE `osdial_pause_codes` (
  `pause_code` varchar(6) NOT NULL,
  `pause_code_name` varchar(30) DEFAULT NULL,
  `billable` enum('NO','YES','HALF') DEFAULT 'NO',
  `campaign_id` varchar(20) NOT NULL,
  PRIMARY KEY (`campaign_id`,`pause_code`)
);

CREATE TABLE `osdial_phone_code_groups` (
  `country_code` smallint(5) unsigned NOT NULL DEFAULT '0',
  `areacode` varchar(3) NOT NULL DEFAULT '',
  `GMT_offset` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`country_code`,`areacode`)
);

CREATE TABLE `osdial_phone_codes` (
  `country_code` smallint(5) unsigned NOT NULL DEFAULT '0',
  `country` char(3) DEFAULT NULL,
  `areacode` char(3) DEFAULT NULL,
  `state` varchar(4) DEFAULT NULL,
  `GMT_offset` varchar(5) DEFAULT NULL,
  `DST` enum('Y','N') DEFAULT NULL,
  `DST_range` varchar(8) DEFAULT NULL,
  `geographic_description` varchar(30) DEFAULT NULL,
  KEY `country_area` (`country_code`,`areacode`),
  KEY `country_state` (`country_code`,`state`),
  KEY `country_code` (`country_code`)
);

CREATE TABLE `osdial_postal_code_groups` (
  `country_code` smallint(5) unsigned NOT NULL DEFAULT '0',
  `postal_code` varchar(10) NOT NULL DEFAULT '',
  `GMT_offset` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`country_code`,`postal_code`)
);

CREATE TABLE `osdial_postal_codes` (
  `postal_code` varchar(10) NOT NULL,
  `state` varchar(4) DEFAULT NULL,
  `GMT_offset` varchar(5) DEFAULT NULL,
  `DST` enum('Y','N') DEFAULT NULL,
  `DST_range` varchar(8) DEFAULT NULL,
  `country` char(3) DEFAULT NULL,
  `country_code` smallint(5) unsigned NOT NULL DEFAULT '0',
  KEY `country_postal` (`country_code`,`postal_code`)
);

CREATE TABLE `osdial_remote_agents` (
  `remote_agent_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user_start` varchar(20) DEFAULT NULL,
  `number_of_lines` tinyint(3) unsigned DEFAULT '1',
  `server_ip` varchar(15) NOT NULL,
  `conf_exten` varchar(20) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'INACTIVE',
  `campaign_id` varchar(20) NOT NULL,
  `closer_campaigns` text,
  PRIMARY KEY (`remote_agent_id`)
);

CREATE TABLE `osdial_report_groups` (
  `group_type` varchar(30) NOT NULL DEFAULT '',
  `group_value` varchar(50) NOT NULL DEFAULT '',
  `group_label` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`group_type`,`group_value`)
);

CREATE TABLE `osdial_script_button_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(9) unsigned NOT NULL,
  `script_id` varchar(40) NOT NULL DEFAULT '',
  `script_button_id` varchar(10) NOT NULL,
  `user` varchar(20) NOT NULL,
  `event_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  KEY `user_lead` (`user`,`lead_id`),
  KEY `result` (`script_id`,`script_button_id`)
);

CREATE TABLE `osdial_script_buttons` (
  `script_id` varchar(10) NOT NULL,
  `script_button_id` varchar(10) NOT NULL,
  `script_button_description` varchar(100) DEFAULT '',
  `script_button_label` varchar(50) DEFAULT '',
  `script_button_text` text,
  PRIMARY KEY (`script_id`,`script_button_id`)
);

CREATE TABLE `osdial_scripts` (
  `script_id` varchar(20) NOT NULL DEFAULT '',
  `script_name` varchar(50) DEFAULT NULL,
  `script_comments` varchar(255) DEFAULT NULL,
  `script_text` text,
  `active` enum('Y','N') DEFAULT NULL,
  PRIMARY KEY (`script_id`)
);

CREATE TABLE `osdial_server_trunks` (
  `server_ip` varchar(15) NOT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `dedicated_trunks` smallint(5) unsigned DEFAULT '0',
  `trunk_restriction` enum('MAXIMUM_LIMIT','OVERFLOW_ALLOWED') DEFAULT 'OVERFLOW_ALLOWED',
  KEY `campaign_id` (`campaign_id`),
  KEY `server_ip` (`server_ip`)
);

CREATE TABLE `osdial_state_call_times` (
  `state_call_time_id` varchar(10) NOT NULL,
  `state_call_time_state` varchar(2) NOT NULL,
  `state_call_time_name` varchar(30) NOT NULL,
  `state_call_time_comments` varchar(255) DEFAULT '',
  `sct_default_start` smallint(4) unsigned NOT NULL DEFAULT '900',
  `sct_default_stop` smallint(4) unsigned NOT NULL DEFAULT '2100',
  `sct_sunday_start` smallint(4) unsigned DEFAULT '0',
  `sct_sunday_stop` smallint(4) unsigned DEFAULT '0',
  `sct_monday_start` smallint(4) unsigned DEFAULT '0',
  `sct_monday_stop` smallint(4) unsigned DEFAULT '0',
  `sct_tuesday_start` smallint(4) unsigned DEFAULT '0',
  `sct_tuesday_stop` smallint(4) unsigned DEFAULT '0',
  `sct_wednesday_start` smallint(4) unsigned DEFAULT '0',
  `sct_wednesday_stop` smallint(4) unsigned DEFAULT '0',
  `sct_thursday_start` smallint(4) unsigned DEFAULT '0',
  `sct_thursday_stop` smallint(4) unsigned DEFAULT '0',
  `sct_friday_start` smallint(4) unsigned DEFAULT '0',
  `sct_friday_stop` smallint(4) unsigned DEFAULT '0',
  `sct_saturday_start` smallint(4) unsigned DEFAULT '0',
  `sct_saturday_stop` smallint(4) unsigned DEFAULT '0',
  PRIMARY KEY (`state_call_time_id`)
);

CREATE TABLE `osdial_status_categories` (
  `vsc_id` varchar(20) NOT NULL,
  `vsc_name` varchar(50) DEFAULT NULL,
  `vsc_description` varchar(255) DEFAULT NULL,
  `tovdad_display` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`vsc_id`)
);

INSERT INTO `osdial_status_categories` VALUES ('CPA','Netborder - Call Progress Analysis',NULL,'N');
INSERT INTO `osdial_status_categories` VALUES ('IVR','IVR',NULL,'N');

CREATE TABLE `osdial_statuses` (
  `status` varchar(6) NOT NULL,
  `status_name` varchar(30) DEFAULT NULL,
  `selectable` enum('Y','N') DEFAULT NULL,
  `human_answered` enum('Y','N') DEFAULT 'N',
  `category` varchar(20) DEFAULT 'UNDEFINED',
  PRIMARY KEY (`status`)
);

INSERT INTO `osdial_statuses` VALUES ('CPRATB','CPA-Pre: All-Trunks-Busy','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRB','CPA-Pre: Busy','N','N','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRCR','CPA-Pre: Carrier Reject','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRLR','CPA-Pre: License Reject','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRNA','CPA-Pre: No-Answer','N','N','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRSIC','CPA-Pre: SIT-IC Changed','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRSIO','CPA-Pre: SIT-IO Invalid','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRSNC','CPA-Pre: SIT-NC Temp Busy','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRSRO','CPA-Pre: SIT-RO Temp Failure','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRSVC','CPA-Pre: SIT-VC Unassigned','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPRUNK','CPA-Pre: Unknown/Cancelled','N','N','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPSAA','CPA-Post: Answering Machine','N','N','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPSFAX','CPA-Post: Fax Machine','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPSHMN','CPA-Post: Human Voice','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPSHU','CPA-Post: Hangup','N','N','CPA');
INSERT INTO `osdial_statuses` VALUES ('CPSUNK','CPA-Post: Unknown/Error','N','Y','CPA');
INSERT INTO `osdial_statuses` VALUES ('CRC','Carrier Congestion','N','Y','SYSTEM');
INSERT INTO `osdial_statuses` VALUES ('CRF','Carrier Failure','N','Y','SYSTEM');
INSERT INTO `osdial_statuses` VALUES ('CRO','Carrier Out-of-Order','N','Y','SYSTEM');
INSERT INTO `osdial_statuses` VALUES ('CRR','Carrier Rejected','N','Y','SYSTEM');
INSERT INTO `osdial_statuses` VALUES ('DNCE','DNC From External DNC','N','N','SYSTEM');
INSERT INTO `osdial_statuses` VALUES ('DNCL','DNC From Another Lead/List','N','N','SYSTEM');
INSERT INTO `osdial_statuses` VALUES ('Fax','Fax Machine','Y','Y','CONTACT');
INSERT INTO `osdial_statuses` VALUES ('INBND','Inbound Call','N','Y','SYSTEM');
INSERT INTO `osdial_statuses` VALUES ('PD','Post Date','Y','Y','CONTACT');
INSERT INTO `osdial_statuses` VALUES ('VAXFER','IVR: Agent XFER','N','Y','IVR');
INSERT INTO `osdial_statuses` VALUES ('VDNC','IVR: Do-Not-Call','N','Y','IVR');
INSERT INTO `osdial_statuses` VALUES ('VEXFER','IVR: External XFER','N','Y','IVR');
INSERT INTO `osdial_statuses` VALUES ('VIXFER','IVR: InGroup XFER','N','Y','IVR');
INSERT INTO `osdial_statuses` VALUES ('VNI','IVR: Not-Interested','N','Y','IVR');
INSERT INTO `osdial_statuses` VALUES ('VPLAY','IVR: Played Audio','N','Y','IVR');
INSERT INTO `osdial_statuses` VALUES ('VPU','IVR: PickUp','N','Y','IVR');
INSERT INTO `osdial_statuses` VALUES ('VTO','IVR: Menu Time-out','N','Y','IVR');

CREATE TABLE `osdial_tts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `extension` varchar(20) NOT NULL,
  `phrase` mediumtext,
  `voice` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE `osdial_user_groups` (
  `user_group` varchar(20) NOT NULL,
  `group_name` varchar(40) NOT NULL,
  `allowed_campaigns` varchar(2048) DEFAULT ' -ALL-CAMPAIGNS- - -',
  `view_agent_pause_summary` enum('0','1') DEFAULT '1',
  `export_agent_pause_summary` enum('0','1') DEFAULT '1',
  `view_agent_performance_detail` enum('0','1') DEFAULT '1',
  `export_agent_performance_detail` enum('0','1') DEFAULT '1',
  `view_agent_realtime` enum('0','1') DEFAULT '1',
  `view_agent_realtime_iax_barge` enum('0','1') DEFAULT '1',
  `view_agent_realtime_iax_listen` enum('0','1') DEFAULT '1',
  `view_agent_realtime_sip_barge` enum('0','1') DEFAULT '1',
  `view_agent_realtime_sip_listen` enum('0','1') DEFAULT '1',
  `view_agent_realtime_summary` enum('0','1') DEFAULT '1',
  `view_agent_stats` enum('0','1') DEFAULT '1',
  `view_agent_status` enum('0','1') DEFAULT '1',
  `view_agent_timesheet` enum('0','1') DEFAULT '1',
  `export_agent_timesheet` enum('0','1') DEFAULT '1',
  `view_campaign_call_report` enum('0','1') DEFAULT '1',
  `export_campaign_call_report` enum('0','1') DEFAULT '1',
  `view_campaign_recent_outbound_sales` enum('0','1') DEFAULT '1',
  `export_campaign_recent_outbound_sales` enum('0','1') DEFAULT '1',
  `view_ingroup_call_report` enum('0','1') DEFAULT '1',
  `export_ingroup_call_report` enum('0','1') DEFAULT '1',
  `view_lead_performance_campaign` enum('0','1') DEFAULT '1',
  `export_lead_performance_campaign` enum('0','1') DEFAULT '1',
  `view_lead_performance_list` enum('0','1') DEFAULT '1',
  `export_lead_performance_list` enum('0','1') DEFAULT '1',
  `view_lead_search` enum('0','1') DEFAULT '1',
  `view_lead_search_advanced` enum('0','1') DEFAULT '1',
  `export_lead_search_advanced` enum('0','1') DEFAULT '1',
  `view_list_cost_entry` enum('0','1') DEFAULT '1',
  `export_list_cost_entry` enum('0','1') DEFAULT '1',
  `view_server_performance` enum('0','1') DEFAULT '1',
  `view_server_times` enum('0','1') DEFAULT '1',
  `view_usergroup_hourly_stats` enum('0','1') DEFAULT '1',
  `allowed_scripts` varchar(2048) DEFAULT ' -ALL-SCRIPTS- -',
  `allowed_email_templates` varchar(2048) DEFAULT ' -ALL-EMAIL-TEMPLATES- -',
  `allowed_ingroups` varchar(2048) DEFAULT ' -ALL-INGROUPS- -'
);

INSERT INTO `osdial_user_groups` VALUES ('VIRTUAL','Virtual Agents',' -ALL-CAMPAIGNS- - -','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1',' -ALL-SCRIPTS- -',' -ALL-EMAIL-TEMPLATES- -',' -ALL-INGROUPS- -');

CREATE TABLE `osdial_user_log` (
  `user_log_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(20) NOT NULL DEFAULT '',
  `event` varchar(50) DEFAULT NULL,
  `campaign_id` varchar(20) NOT NULL,
  `event_date` datetime DEFAULT NULL,
  `event_epoch` int(10) unsigned DEFAULT NULL,
  `user_group` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`user_log_id`),
  KEY `user` (`user`)
);

CREATE TABLE `osdial_users` (
  `user_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(20) NOT NULL DEFAULT '',
  `pass` varchar(20) DEFAULT NULL,
  `full_name` varchar(50) DEFAULT NULL,
  `user_level` int(2) DEFAULT NULL,
  `user_group` varchar(20) DEFAULT NULL,
  `phone_login` varchar(20) DEFAULT NULL,
  `phone_pass` varchar(20) DEFAULT NULL,
  `delete_users` enum('0','1') DEFAULT '0',
  `delete_user_groups` enum('0','1') DEFAULT '0',
  `delete_lists` enum('0','1') DEFAULT '0',
  `delete_campaigns` enum('0','1') DEFAULT '0',
  `delete_ingroups` enum('0','1') DEFAULT '0',
  `delete_remote_agents` enum('0','1') DEFAULT '0',
  `load_leads` enum('0','1') DEFAULT '0',
  `campaign_detail` enum('0','1') DEFAULT '0',
  `ast_admin_access` enum('0','1') DEFAULT '0',
  `ast_delete_phones` enum('0','1') DEFAULT '0',
  `delete_scripts` enum('0','1') DEFAULT '0',
  `modify_leads` enum('0','1') DEFAULT '0',
  `hotkeys_active` enum('0','1') DEFAULT '0',
  `change_agent_campaign` enum('0','1') DEFAULT '0',
  `agent_choose_ingroups` enum('0','1') DEFAULT '1',
  `closer_campaigns` text,
  `scheduled_callbacks` enum('0','1') DEFAULT '1',
  `agentonly_callbacks` enum('0','1') DEFAULT '0',
  `agentcall_manual` enum('0','1') DEFAULT '0',
  `osdial_recording` enum('0','1') DEFAULT '1',
  `osdial_transfers` enum('0','1') DEFAULT '1',
  `delete_filters` enum('0','1') DEFAULT '0',
  `alter_agent_interface_options` enum('0','1') DEFAULT '0',
  `closer_default_blended` enum('0','1') DEFAULT '0',
  `delete_call_times` enum('0','1') DEFAULT '0',
  `modify_call_times` enum('0','1') DEFAULT '0',
  `modify_users` enum('0','1') DEFAULT '0',
  `modify_campaigns` enum('0','1') DEFAULT '0',
  `modify_lists` enum('0','1') DEFAULT '0',
  `modify_scripts` enum('0','1') DEFAULT '0',
  `modify_filters` enum('0','1') DEFAULT '0',
  `modify_ingroups` enum('0','1') DEFAULT '0',
  `modify_usergroups` enum('0','1') DEFAULT '0',
  `modify_remoteagents` enum('0','1') DEFAULT '0',
  `modify_servers` enum('0','1') DEFAULT '0',
  `view_reports` enum('0','1') DEFAULT '0',
  `osdial_recording_override` enum('DISABLED','NEVER','ONDEMAND','ALLCALLS','ALLFORCE') DEFAULT 'DISABLED',
  `alter_custdata_override` enum('NOT_ACTIVE','ALLOW_ALTER') DEFAULT 'NOT_ACTIVE',
  `manual_dial_new_limit` int(9) DEFAULT '-1',
  `manual_dial_allow_skip` enum('0','1') DEFAULT '1',
  `export_leads` enum('0','1') DEFAULT '0',
  `admin_api_access` enum('0','1') DEFAULT '0',
  `agent_api_access` enum('0','1') DEFAULT '0',
  `xfer_agent2agent` enum('0','1') DEFAULT '0',
  `script_override` varchar(20) DEFAULT '',
  `load_dnc` enum('0','1') DEFAULT '0',
  `export_dnc` enum('0','1') DEFAULT '0',
  `delete_dnc` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `user` (`user`)
);

INSERT INTO `osdial_users` VALUES (1,'PBX-IN','do not delete',NULL,0,'ADMIN',NULL,NULL,'0','0','0','0','0','0','0','0','0','0','0','0','0','0','1',NULL,'1','0','0','1','1','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','DISABLED','NOT_ACTIVE',-1,'1','0','0','0','0','','0','0','0');
INSERT INTO `osdial_users` VALUES (2,'PBX-OUT','do not delete',NULL,0,'ADMIN',NULL,NULL,'0','0','0','0','0','0','0','0','0','0','0','0','0','0','1',NULL,'1','0','0','1','1','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','DISABLED','NOT_ACTIVE',-1,'1','0','0','0','0','','0','0','0');

CREATE TABLE `osdial_xfer_log` (
  `xfercallid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(9) unsigned NOT NULL,
  `list_id` bigint(14) unsigned DEFAULT NULL,
  `campaign_id` varchar(20) DEFAULT NULL,
  `call_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `phone_code` varchar(10) DEFAULT NULL,
  `phone_number` varchar(20) NOT NULL DEFAULT '',
  `user` varchar(20) DEFAULT NULL,
  `closer` varchar(20) DEFAULT NULL,
  `uniqueid` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`xfercallid`),
  KEY `lead_id` (`lead_id`),
  KEY `call_date` (`call_date`),
  KEY `date_user` (`call_date`,`user`),
  KEY `date_closer` (`call_date`,`closer`),
  KEY `phone_number` (`phone_number`),
  KEY `uniqueid` (`uniqueid`)
);

CREATE TABLE `park_log` (
  `uniqueid` varchar(20) NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `channel` varchar(100) DEFAULT NULL,
  `channel_group` varchar(30) DEFAULT NULL,
  `server_ip` varchar(15) DEFAULT NULL,
  `parked_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `grab_time` datetime DEFAULT NULL,
  `hangup_time` datetime DEFAULT NULL,
  `parked_sec` int(10) DEFAULT NULL,
  `talked_sec` int(10) DEFAULT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `user` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`uniqueid`),
  KEY `parked_time` (`parked_time`)
);

CREATE TABLE `parked_channels` (
  `channel` varchar(100) NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `channel_group` varchar(30) DEFAULT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `parked_by` varchar(100) DEFAULT NULL,
  `parked_time` datetime DEFAULT NULL,
  PRIMARY KEY (`server_ip`,`channel`)
);

CREATE TABLE `phones` (
  `extension` varchar(100) NOT NULL,
  `dialplan_number` varchar(20) DEFAULT NULL,
  `voicemail_id` varchar(10) DEFAULT NULL,
  `phone_ip` varchar(15) DEFAULT NULL,
  `computer_ip` varchar(15) DEFAULT NULL,
  `server_ip` varchar(15) NOT NULL,
  `login` varchar(20) DEFAULT NULL,
  `pass` varchar(20) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `active` enum('Y','N') DEFAULT NULL,
  `phone_type` varchar(50) DEFAULT NULL,
  `fullname` varchar(50) DEFAULT NULL,
  `company` varchar(10) DEFAULT NULL,
  `picture` varchar(19) DEFAULT NULL,
  `messages` int(4) DEFAULT NULL,
  `old_messages` int(4) DEFAULT NULL,
  `protocol` enum('SIP','Zap','IAX2','EXTERNAL','DAHDI') DEFAULT 'SIP',
  `local_gmt` varchar(6) DEFAULT '-5',
  `ASTmgrUSERNAME` varchar(20) DEFAULT 'cron',
  `ASTmgrSECRET` varchar(20) DEFAULT '1234',
  `login_user` varchar(20) DEFAULT NULL,
  `login_pass` varchar(20) DEFAULT NULL,
  `login_campaign` varchar(20) DEFAULT NULL,
  `park_on_extension` varchar(10) DEFAULT '8301',
  `conf_on_extension` varchar(10) DEFAULT '8302',
  `OSDIAL_park_on_extension` varchar(10) DEFAULT '8301',
  `OSDIAL_park_on_filename` varchar(10) DEFAULT 'park',
  `monitor_prefix` varchar(10) DEFAULT '8612',
  `recording_exten` varchar(10) DEFAULT '8309',
  `voicemail_exten` varchar(10) DEFAULT '8501',
  `voicemail_dump_exten` varchar(20) DEFAULT '85026666666666',
  `ext_context` varchar(20) DEFAULT 'osdial',
  `dtmf_send_extension` varchar(100) DEFAULT 'local/8500998@default',
  `call_out_number_group` varchar(100) DEFAULT 'Zap/g2/',
  `client_browser` varchar(100) DEFAULT '/usr/bin/mozilla',
  `install_directory` varchar(100) DEFAULT '/usr/local/perl_TK',
  `local_web_callerID_URL` varchar(255) DEFAULT 'http://localhost/agent/test_callerid_output.php',
  `OSDIAL_web_URL` varchar(255) DEFAULT 'http://localhost/agent/test_OSDIAL_output.php',
  `AGI_call_logging_enabled` enum('0','1') DEFAULT '1',
  `user_switching_enabled` enum('0','1') DEFAULT '1',
  `conferencing_enabled` enum('0','1') DEFAULT '1',
  `admin_hangup_enabled` enum('0','1') DEFAULT '0',
  `admin_hijack_enabled` enum('0','1') DEFAULT '0',
  `admin_monitor_enabled` enum('0','1') DEFAULT '1',
  `call_parking_enabled` enum('0','1') DEFAULT '1',
  `updater_check_enabled` enum('0','1') DEFAULT '1',
  `AFLogging_enabled` enum('0','1') DEFAULT '1',
  `QUEUE_ACTION_enabled` enum('0','1') DEFAULT '1',
  `CallerID_popup_enabled` enum('0','1') DEFAULT '1',
  `voicemail_button_enabled` enum('0','1') DEFAULT '1',
  `enable_fast_refresh` enum('0','1') DEFAULT '0',
  `fast_refresh_rate` int(5) DEFAULT '1000',
  `enable_persistant_mysql` enum('0','1') DEFAULT '0',
  `auto_dial_next_number` enum('0','1') DEFAULT '1',
  `VDstop_rec_after_each_call` enum('0','1') DEFAULT '1',
  `DBX_server` varchar(15) DEFAULT NULL,
  `DBX_database` varchar(15) DEFAULT 'asterisk',
  `DBX_user` varchar(15) DEFAULT 'cron',
  `DBX_pass` varchar(15) DEFAULT '1234',
  `DBX_port` int(6) DEFAULT '3306',
  `DBY_server` varchar(15) DEFAULT NULL,
  `DBY_database` varchar(15) DEFAULT 'asterisk',
  `DBY_user` varchar(15) DEFAULT 'cron',
  `DBY_pass` varchar(15) DEFAULT '1234',
  `DBY_port` int(6) DEFAULT '3306',
  `outbound_cid` varchar(20) DEFAULT NULL,
  `enable_sipsak_messages` enum('0','1') DEFAULT '0',
  `outbound_cid_name` varchar(40) DEFAULT '',
  `voicemail_password` varchar(50) DEFAULT '1234',
  `voicemail_email` varchar(255) DEFAULT '',
  PRIMARY KEY (`server_ip`,`extension`)
);

CREATE TABLE `qc_recordings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recording_id` int(10) unsigned NOT NULL,
  `lead_id` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_2` (`location`,`filename`),
  KEY `recording_id` (`recording_id`),
  KEY `lead_id` (`lead_id`),
  KEY `recording_id_2` (`recording_id`,`lead_id`),
  KEY `filename` (`filename`),
  KEY `location` (`location`)
);

CREATE TABLE `qc_server_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `qc_server_id` int(10) unsigned NOT NULL,
  `query` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qc_server_id` (`qc_server_id`)
);

CREATE TABLE `qc_servers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `transfer_method` enum('FTP','SCP','SFTP','FTPA') DEFAULT 'FTP',
  `host` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `home_path` varchar(255) DEFAULT NULL,
  `location_template` varchar(255) DEFAULT '[campaign_id]/[date]',
  `transfer_type` enum('IMMEDIATE','BATCH','ARCHIVE') DEFAULT 'IMMEDIATE',
  `archive` enum('NONE','ZIP','TAR','TGZ','TBZ2') DEFAULT 'NONE',
  `active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `batch_time` int(2) unsigned DEFAULT '0',
  `batch_lastrun` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
);

CREATE TABLE `qc_transfers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `qc_server_id` int(10) unsigned NOT NULL,
  `qc_recording_id` int(10) unsigned NOT NULL,
  `status` enum('NOTFOUND','PENDING','SUCCESS','FAILURE') NOT NULL DEFAULT 'PENDING',
  `last_attempt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archive_filename` varchar(255) NOT NULL DEFAULT '',
  `remote_location` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `qc_server_id_3` (`qc_server_id`,`qc_recording_id`),
  KEY `status` (`status`)
);

CREATE TABLE `recording_log` (
  `recording_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(100) DEFAULT NULL,
  `server_ip` varchar(15) DEFAULT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_epoch` int(10) DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `end_epoch` int(10) DEFAULT NULL,
  `length_in_sec` int(10) DEFAULT NULL,
  `length_in_min` double(8,2) DEFAULT NULL,
  `filename` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(255) DEFAULT NULL,
  `lead_id` int(9) unsigned NOT NULL DEFAULT '0',
  `user` varchar(20) NOT NULL DEFAULT '',
  `uniqueid` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`recording_id`),
  KEY `filename` (`filename`),
  KEY `lead_id` (`lead_id`),
  KEY `user` (`user`),
  KEY `uniqueid` (`uniqueid`),
  KEY `time_user` (`start_time`,`user`)
);

CREATE TABLE `server_performance` (
  `start_time` datetime NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `sysload` int(6) NOT NULL,
  `freeram` smallint(5) unsigned NOT NULL,
  `usedram` smallint(5) unsigned NOT NULL,
  `processes` smallint(4) unsigned NOT NULL,
  `channels_total` smallint(4) unsigned NOT NULL,
  `trunks_total` smallint(4) unsigned NOT NULL,
  `clients_total` smallint(4) unsigned NOT NULL,
  `clients_zap` smallint(4) unsigned NOT NULL,
  `clients_iax` smallint(4) unsigned NOT NULL,
  `clients_local` smallint(4) unsigned NOT NULL,
  `clients_sip` smallint(4) unsigned NOT NULL,
  `live_recordings` smallint(4) unsigned NOT NULL,
  `cpu_user_percent` smallint(3) unsigned NOT NULL DEFAULT '0',
  `cpu_system_percent` smallint(3) unsigned NOT NULL DEFAULT '0',
  `cpu_idle_percent` smallint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`server_ip`,`start_time`)
);

CREATE TABLE `server_stats` (
  `server_ip` varchar(15) NOT NULL,
  `server_timestamp` datetime NOT NULL,
  `host` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `load_one` varchar(6) NOT NULL,
  `load_five` varchar(6) NOT NULL,
  `load_ten` varchar(6) NOT NULL,
  `load_procs` varchar(10) NOT NULL,
  `cpu_count` varchar(2) NOT NULL,
  `cpu_pct` varchar(10) NOT NULL,
  `mem_total` varchar(20) NOT NULL,
  `mem_free` varchar(20) NOT NULL,
  `mem_pct` varchar(10) NOT NULL,
  `swap_used` varchar(20) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_ip`)
);

CREATE TABLE `server_updater` (
  `server_ip` varchar(15) NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `sql_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_ip`)
);

CREATE TABLE `servers` (
  `server_id` varchar(10) NOT NULL,
  `server_description` varchar(255) DEFAULT NULL,
  `server_ip` varchar(15) NOT NULL,
  `active` enum('Y','N') DEFAULT NULL,
  `asterisk_version` varchar(20) DEFAULT '1.2.24',
  `max_osdial_trunks` smallint(4) DEFAULT '96',
  `telnet_host` varchar(20) NOT NULL DEFAULT 'localhost',
  `telnet_port` int(5) NOT NULL DEFAULT '5038',
  `ASTmgrUSERNAME` varchar(20) NOT NULL DEFAULT 'cron',
  `ASTmgrSECRET` varchar(20) NOT NULL DEFAULT '1234',
  `ASTmgrUSERNAMEupdate` varchar(20) NOT NULL DEFAULT 'updatecron',
  `ASTmgrUSERNAMElisten` varchar(20) NOT NULL DEFAULT 'listencron',
  `ASTmgrUSERNAMEsend` varchar(20) NOT NULL DEFAULT 'sendcron',
  `local_gmt` varchar(6) DEFAULT '-5',
  `voicemail_dump_exten` varchar(20) NOT NULL DEFAULT '85026666666666',
  `answer_transfer_agent` varchar(20) NOT NULL DEFAULT '8365',
  `ext_context` varchar(20) NOT NULL DEFAULT 'default',
  `sys_perf_log` enum('Y','N') DEFAULT 'N',
  `vd_server_logs` enum('Y','N') DEFAULT 'Y',
  `agi_output` enum('NONE','STDERR','FILE','BOTH') DEFAULT 'FILE',
  `osdial_balance_active` enum('Y','N') DEFAULT 'N',
  `balance_trunks_offlimits` smallint(5) unsigned DEFAULT '0',
  `server_profile` enum('AIO','CONTROL','SQL','WEB','DIALER','ARCHIVE','OTHER') DEFAULT 'DIALER',
  PRIMARY KEY (`server_ip`)
);

CREATE TABLE `system_settings` (
  `version` varchar(50) DEFAULT NULL,
  `install_date` varchar(50) DEFAULT NULL,
  `use_non_latin` enum('0','1') DEFAULT '0',
  `webroot_writable` enum('0','1') DEFAULT '1',
  `enable_queuemetrics_logging` enum('0','1') DEFAULT '0',
  `queuemetrics_server_ip` varchar(15) DEFAULT NULL,
  `queuemetrics_dbname` varchar(50) DEFAULT NULL,
  `queuemetrics_login` varchar(50) DEFAULT NULL,
  `queuemetrics_pass` varchar(50) DEFAULT NULL,
  `queuemetrics_url` varchar(255) DEFAULT NULL,
  `queuemetrics_log_id` varchar(10) DEFAULT 'VIC',
  `queuemetrics_eq_prepend` varchar(255) DEFAULT 'NONE',
  `osdial_agent_disable` enum('NOT_ACTIVE','LIVE_AGENT','EXTERNAL','ALL') DEFAULT 'LIVE_AGENT',
  `allow_sipsak_messages` enum('0','1') DEFAULT '0',
  `admin_home_url` varchar(255) DEFAULT '/',
  `enable_agc_xfer_log` enum('0','1') DEFAULT '0',
  `company_name` varchar(100) DEFAULT 'Company Name Here',
  `admin_template` varchar(50) DEFAULT 'default',
  `agent_template` varchar(50) DEFAULT 'default',
  `last_update_check` datetime DEFAULT NULL,
  `last_update_version` varchar(50) DEFAULT NULL,
  `enable_lead_allocation` enum('0','1') DEFAULT '1',
  `enable_external_agents` enum('0','1') DEFAULT '0',
  `enable_filters` enum('0','1') DEFAULT '1',
  `enable_multicompany` enum('0','1') DEFAULT '0',
  `multicompany_admin` varchar(20) DEFAULT 'admin',
  `default_carrier_id` int(11) DEFAULT '0',
  `intra_server_protocol` enum('IAX2','SIP') DEFAULT 'SIP',
  `default_date_format` varchar(50) DEFAULT 'Y-m-d H:i:s',
  `use_browser_timezone_offset` enum('Y','N') DEFAULT 'Y',
  `last_recording_extension` varchar(20) DEFAULT '85100000',
  `last_general_extension` varchar(20) DEFAULT '85110000',
  `default_phone_code` varchar(10) DEFAULT '1'
);

INSERT INTO `system_settings` VALUES ('3.0.1.099',NOW(),'0','1','0','','','','','','VIC','NONE','NOT_ACTIVE','0','/','0','Company Name Here','default','default',NOW(),NULL,'1','0','1','0','admin',0,'SIP','Y-m-d H:i:s','Y','85100000','85110000','1');

CREATE TABLE `web_client_sessions` (
  `extension` varchar(100) NOT NULL,
  `server_ip` varchar(15) NOT NULL,
  `program` enum('agc','osdial','monitor','other') DEFAULT 'agc',
  `start_time` datetime NOT NULL,
  `session_name` varchar(40) NOT NULL,
  PRIMARY KEY (`session_name`)
);
