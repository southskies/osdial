# Convert tables to InnoDB, much better performance.
ALTER TABLE call_log ENGINE=InnoDB;
ALTER TABLE conferences ENGINE=InnoDB;
ALTER TABLE inbound_numbers ENGINE=InnoDB;
ALTER TABLE live_channels ENGINE=InnoDB;
ALTER TABLE live_inbound ENGINE=InnoDB;
ALTER TABLE live_inbound_log ENGINE=InnoDB;
ALTER TABLE live_sip_channels ENGINE=InnoDB;
ALTER TABLE park_log ENGINE=InnoDB;
ALTER TABLE parked_channels ENGINE=InnoDB;
ALTER TABLE phone_favorites ENGINE=InnoDB;
ALTER TABLE phones ENGINE=InnoDB;
ALTER TABLE recording_log ENGINE=InnoDB;
ALTER TABLE server_performance ENGINE=InnoDB;
ALTER TABLE server_updater ENGINE=InnoDB;
ALTER TABLE servers ENGINE=InnoDB;
ALTER TABLE system_settings ENGINE=InnoDB;
ALTER TABLE osdial_agent_log ENGINE=InnoDB;
ALTER TABLE osdial_auto_calls ENGINE=InnoDB;
ALTER TABLE osdial_call_times ENGINE=InnoDB;
ALTER TABLE osdial_callbacks ENGINE=InnoDB;
ALTER TABLE osdial_campaign_agents ENGINE=InnoDB;
ALTER TABLE osdial_campaign_hotkeys ENGINE=InnoDB;
ALTER TABLE osdial_campaign_server_stats ENGINE=InnoDB;
ALTER TABLE osdial_campaign_stats ENGINE=InnoDB;
ALTER TABLE osdial_campaign_statuses ENGINE=InnoDB;
ALTER TABLE osdial_campaigns ENGINE=InnoDB;
ALTER TABLE osdial_campaigns_list_mix ENGINE=InnoDB;
ALTER TABLE osdial_closer_log ENGINE=InnoDB;
ALTER TABLE osdial_conferences ENGINE=InnoDB;
ALTER TABLE osdial_dnc ENGINE=InnoDB;
ALTER TABLE osdial_hopper ENGINE=InnoDB;
ALTER TABLE osdial_inbound_group_agents ENGINE=InnoDB;
ALTER TABLE osdial_inbound_groups ENGINE=InnoDB;
ALTER TABLE osdial_ivr ENGINE=InnoDB;
ALTER TABLE osdial_lead_filters ENGINE=InnoDB;
ALTER TABLE osdial_lead_recycle ENGINE=InnoDB;
ALTER TABLE osdial_list ENGINE=InnoDB;
ALTER TABLE osdial_list_pins ENGINE=InnoDB;
ALTER TABLE osdial_lists ENGINE=InnoDB;
ALTER TABLE osdial_live_agents ENGINE=InnoDB;
ALTER TABLE osdial_live_inbound_agents ENGINE=InnoDB;
ALTER TABLE osdial_log ENGINE=InnoDB;
ALTER TABLE osdial_manager ENGINE=InnoDB;
ALTER TABLE osdial_pause_codes ENGINE=InnoDB;
ALTER TABLE osdial_phone_codes ENGINE=InnoDB;
ALTER TABLE osdial_postal_codes ENGINE=InnoDB;
ALTER TABLE osdial_remote_agents ENGINE=InnoDB;
ALTER TABLE osdial_scripts ENGINE=InnoDB;
ALTER TABLE osdial_server_trunks ENGINE=InnoDB;
ALTER TABLE osdial_state_call_times ENGINE=InnoDB;
ALTER TABLE osdial_stations ENGINE=InnoDB;
ALTER TABLE osdial_status_categories ENGINE=InnoDB;
ALTER TABLE osdial_statuses ENGINE=InnoDB;
ALTER TABLE osdial_user_groups ENGINE=InnoDB;
ALTER TABLE osdial_user_log ENGINE=InnoDB;
ALTER TABLE osdial_users ENGINE=InnoDB;
ALTER TABLE osdial_xfer_log ENGINE=InnoDB;
ALTER TABLE web_client_sessions ENGINE=InnoDB;

#Create some custom indexes.
create index country_postal ON osdial_postal_codes (country_code,postal_code);
create index country_area ON osdial_phone_codes (country_code,areacode);
create index country_state ON osdial_phone_codes (country_code,state);
create index country_code ON osdial_phone_codes (country_code);
create index phone_list ON osdial_list (phone_number,list_id);

#Create a globalized config table.
CREATE TABLE configuration (
	id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	fk_id VARCHAR(20) NOT NULL default '',
	name VARCHAR(50),
	data VARCHAR(100),
	index (name),
	index (fk_id,name)
) ENGINE=INNODB;

INSERT INTO configuration (name,data) values('ArchiveHostname','');
INSERT INTO configuration (name,data) values('ArchiveTransferMethod','');
INSERT INTO configuration (name,data) values('ArchivePort','');
INSERT INTO configuration (name,data) values('ArchiveUsername','');
INSERT INTO configuration (name,data) values('ArchivePassword','');
INSERT INTO configuration (name,data) values('ArchivePath','');
INSERT INTO configuration (name,data) values('ArchiveWebPath','');
INSERT INTO configuration (name,data) values('ArchiveMixFormat','');

#Quality Control Server / Rules / Transfer Log
CREATE TABLE qc_servers (
	id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	name VARCHAR(50),
	description VARCHAR(255),
	transfer_method enum('FTP','SCP','SFTP') default 'FTP',
	host VARCHAR(100),
	username VARCHAR(50),
	password VARCHAR(50),
	home_path VARCHAR(255),
	location_template VARCHAR(255) DEFAULT '[campaign_id]/[date]',
	transfer_type enum('IMMEDIATE','BATCH','ARCHIVE') DEFAULT 'IMMEDIATE',
	archive enum('NONE','ZIP','TAR','TGZ','TBZ2') DEFAULT 'NONE',
	active enum('Y','N') DEFAULT 'Y',
	batch_time INT(2) UNSIGNED DEFAULT 0,
	batch_lastrun DATETIME,
	index (active)
) ENGINE=INNODB;

CREATE TABLE qc_server_rules (
	id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	qc_server_id INT(10) UNSIGNED NOT NULL,
	query VARCHAR(255),
	index (qc_server_id)
) ENGINE=INNODB;

CREATE TABLE qc_recordings (
	id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	recording_id INT(10) UNSIGNED NOT NULL,
	lead_id INT(10) UNSIGNED NOT NULL,
	filename VARCHAR(255),
	location VARCHAR(255),
	index (recording_id),
	index (lead_id),
	index (recording_id,lead_id),
	index (filename),
	index (location),
	unique (location,filename)
) ENGINE=INNODB;

CREATE TABLE qc_transfers (
	id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	qc_server_id INT(10) UNSIGNED NOT NULL,
	qc_recording_id INT(10) UNSIGNED NOT NULL,
	status enum('NOTFOUND','PENDING','SUCCESS','FAILURE') DEFAULT 'PENDING',
	last_attempt DATETIME,
	archive_filename VARCHAR(255),
	remote_location VARCHAR(255),
	index (qc_server_id),
	index (qc_recording_id),
	index (status),
	index (qc_server_id,status),
	index (qc_recording_id,status),
	unique (qc_server_id,qc_recording_id),
	index (qc_server_id,qc_recording_id,status)
) ENGINE=INNODB;


#index on call_log - 2008-10-15 18:40
create index start_time ON call_log (start_time);
create index end_time ON call_log (end_time);
create index time ON call_log (start_time,end_time);
create index list_phone ON osdial_list (list_id,phone_number);
create index list_status ON osdial_list (list_id,status);

#index osdial_agent_log - 2008-10-22 23:12
create index time_user ON osdial_agent_log (event_time,user);

#index osdial closer/xfer logs
create index date_user ON osdial_xfer_log (call_date,user);
create index date_closer ON osdial_xfer_log (call_date,closer);
create index phone_number ON osdial_xfer_log (phone_number);
create index phone_number ON osdial_closer_log (phone_number);
create index date_user ON osdial_closer_log (call_date,user);


# 02/11/2009
CREATE TABLE osdial_campaign_forms (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        campaigns VARCHAR(255) NOT NULL,
        name VARCHAR(15) NOT NULL,
	description VARCHAR(50) NOT NULL,
	description2 VARCHAR(50) NOT NULL,
        priority INT(10) UNSIGNED NOT NULL,
        deleted INT(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=INNODB;

CREATE TABLE osdial_campaign_fields (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        form_id INT(10) UNSIGNED NOT NULL,
        name VARCHAR(15) NOT NULL,
	description VARCHAR(50) NOT NULL,
	options VARCHAR(255) NOT NULL,
        length INT(2) NOT NULL DEFAULT '22',
        priority INT(10) UNSIGNED NOT NULL,
        deleted INT(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=INNODB;


CREATE TABLE osdial_list_fields (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        lead_id INT(10) UNSIGNED NOT NULL,
        field_id INT(10) UNSIGNED NOT NULL,
        value VARCHAR(255),
	index (lead_id),
	unique (lead_id, field_id)
) ENGINE=INNODB;

INSERT INTO osdial_campaign_forms values('1','ALL','CREDITCARD','Details as They Appear on CC','(if different)','1','0');
INSERT INTO osdial_campaign_fields values('1','1','NAME','Name on Card','','22','1','0');
INSERT INTO osdial_campaign_fields values('2','1','ADDRESS','Billing Address','','22','2','0');
INSERT INTO osdial_campaign_fields values('3','1','CITY','City','','22','3','0');
INSERT INTO osdial_campaign_fields values('4','1','STATE','State','','3','4','0');
INSERT INTO osdial_campaign_fields values('5','1','ZIP','ZIP','','10','5','0');
INSERT INTO osdial_campaign_fields values('6','1','TYPE','CC Type','VISA,Master Card,American Express','22','6','0');
INSERT INTO osdial_campaign_fields values('7','1','NUMBER','CC Number','','22','7','0');
INSERT INTO osdial_campaign_fields values('8','1','CVV','CVV Code','','5','8','0');

