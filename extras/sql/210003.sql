# 03/25/2009
UPDATE system_settings SET version='2.1.0.003';

ALTER TABLE live_inbound MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE call_log MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE park_log MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE osdial_manager MODIFY uniqueid VARCHAR(20);
ALTER TABLE osdial_live_agents MODIFY uniqueid VARCHAR(20);
ALTER TABLE osdial_auto_calls MODIFY uniqueid VARCHAR(20);
ALTER TABLE osdial_log MODIFY uniqueid VARCHAR(20) NOT NULL;
ALTER TABLE live_inbound_log MODIFY uniqueid VARCHAR(20) NOT NULL;

ALTER TABLE system_settings ADD company_name VARCHAR(100) default 'Company Name Here';
ALTER TABLE osdial_log ADD term_reason ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE') default 'NONE';
ALTER TABLE osdial_closer_log ADD term_reason ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE') default 'NONE';
ALTER TABLE osdial_closer_log ADD uniqueid VARCHAR(20) NOT NULL default '';
ALTER TABLE osdial_campaigns ADD calls_per_hour_limit INT(8) default '0';

CREATE TABLE osdial_outbound_ivr (
	id INT(11) NOT NULL auto_increment,
	campaign_id VARCHAR(8),
	name VARCHAR(50),
	announcement VARCHAR(255) default 'hello-world',
	repeat_loops INT(4) default '3',
	wait_loops INT(4) default '5',
	wait_timeout INT(4) default '500',
	answered_status VARCHAR(6) default 'VPU',
	virtual_agents INT(3),
	status enum('ACTIVE','INACTIVE') default 'INACTIVE',
	primary key (id),
	unique (campaign_id)
);

CREATE TABLE osdial_outbound_ivr_options (
	id INT(11) NOT NULL auto_increment,
	outbound_ivr_id INT(11),
	parent_id INT(11) NOT NULL default '0',
	keypress CHAR(1),
	action VARCHAR(100),
	action_data TEXT,
	last_state VARCHAR(100),
	primary key (id),
	unique (outbound_ivr_id,parent_id,keypress)
);

INSERT INTO osdial_user_groups SET user_group='VIRTUAL',group_name='Virtual Agents',allowed_campaigns=' -ALL-CAMPAIGNS- - -';
INSERT INTO osdial_status_categories (vsc_id,vsc_name,tovdad_display) values('IVR','IVR','N');
INSERT INTO osdial_statuses values('VAXFER','IVR: Agent XFER','N','Y','IVR');
INSERT INTO osdial_statuses values('VEXFER','IVR: External XFER','N','Y','IVR');
INSERT INTO osdial_statuses values('VPU','IVR: PickUp','N','Y','IVR');
INSERT INTO osdial_statuses values('VNI','IVR: Not-Interested','N','Y','IVR');
INSERT INTO osdial_statuses values('VDNC','IVR: Do-Not-Call','N','Y','IVR');
INSERT INTO osdial_statuses values('VPLAY','IVR: Played Audio','N','Y','IVR');
