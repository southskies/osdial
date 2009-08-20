# 07/13/2009

ALTER TABLE osdial_lists ADD cost FLOAT default '0.00';##|##
 ##Sets the default cost to be assigned to each loaded lead.;

ALTER TABLE osdial_list ADD cost FLOAT default '0.00';##|##
 ##Allow for a cost to be assigned to each lead.;

ALTER TABLE osdial_users ADD manual_dial_new_limit INT(9) default '0';##|##
 ##Adds a limit on the number of new leads an agent can take in a day.
 ##    Only applies to MANUAL dials.;

ALTER TABLE osdial_agent_log ADD lead_called_count INT(9) default '0';##|##
 ##Add lead_called_count to agent_log.;

ALTER TABLE osdial_agent_log ADD prev_status VARCHAR(6) default '';##|##
 ##Add prev_status to agent_log.;

CREATE TABLE osdial_campaign_agent_stats (
  campaign_id varchar(20) NOT NULL,
  user varchar(20) default '',
  update_time timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  manual_dial_new_today int(9) unsigned default '0',
  calls_today int(9) unsigned default '0',
  answers_today int(9) unsigned default '0',
  calls_hour int(9) unsigned default '0',
  answers_hour int(9) unsigned default '0',
  calls_halfhour int(9) unsigned default '0',
  answers_halfhour int(9) unsigned default '0',
  calls_fivemin int(9) unsigned default '0',
  answers_fivemin int(9) unsigned default '0',
  calls_onemin int(9) unsigned default '0',
  answers_onemin int(9) unsigned default '0',
  status_category_1 varchar(20) default NULL,
  status_category_count_1 int(9) unsigned default '0',
  status_category_2 varchar(20) default NULL,
  status_category_count_2 int(9) unsigned default '0',
  status_category_3 varchar(20) default NULL,
  status_category_count_3 int(9) unsigned default '0',
  status_category_4 varchar(20) default NULL,
  status_category_count_4 int(9) unsigned default '0',
  status_category_hour_count_1 int(9) unsigned default '0',
  status_category_hour_count_2 int(9) unsigned default '0',
  status_category_hour_count_3 int(9) unsigned default '0',
  status_category_hour_count_4 int(9) unsigned default '0',
  PRIMARY KEY  (campaign_id,user)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
  ## Adds a table for tracking agent stats per campaign.;

CREATE INDEX entry_date ON osdial_list (entry_date);##|##
  ## Adds entry_date to osdial_list as an index to sort on;

CREATE INDEX modify_date ON osdial_list (modify_date);##|##
  ## Adds modify_date to osdial_list as an index to sort on;

CREATE INDEX area_code ON osdial_list (phone_number(3));##|##
  ## Adds area_code to osdial_list as an index to sort on;

CREATE INDEX last_name ON osdial_list (last_name(3));##|##
  ## Adds last_name to osdial_list as an index to sort on;




######################################################
08-19-2009

CREATE TABLE osdial_postal_code_groups (
	country_code SMALLINT(5) UNSIGNED,
	postal_code VARCHAR(10),
	GMT_offset VARCHAR(5),
	PRIMARY KEY (country_code,postal_code)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 (
	SELECT country_code,postal_code,GMT_offset FROM osdial_postal_codes GROUP BY country_code,postal_code
);##|##
  ## Adding table osdial_postal_code_groups for faster gmt lookups

CREATE TABLE osdial_phone_code_groups (
	country_code SMALLINT(5) UNSIGNED,
	areacode VARCHAR(3),
	GMT_offset VARCHAR(5),
	PRIMARY KEY (country_code,areacode)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 (
	SELECT country_code,areacode,GMT_offset FROM osdial_phone_codes GROUP BY country_code,areacode
);##|##
  ## Adding table osdial_phone_code_groups for faster gmt lookups

CREATE TABLE osdial_report_groups (
	group_type VARCHAR(30),
	group_value VARCHAR(50),
	group_label VARCHAR(100),
  	PRIMARY KEY  (group_type,group_value)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
	(SELECT 'states' AS group_type,state AS group_value,state AS group_label FROM osdial_postal_codes WHERE country_code='1' GROUP BY state)
	UNION
	(SELECT 'lead_source_id' AS group_type,source_id AS group_value,source_id AS group_label FROM osdial_list WHERE source_id!='' GROUP BY source_id)
	UNION
	(SELECT 'lead_vendor_lead_code' AS group_type,vendor_lead_code AS group_value,vendor_lead_code AS group_label FROM osdial_list WHERE vendor_lead_code!='' GROUP BY vendor_lead_code);##|##
  ## Adding table osdial_report_groups for faster group selection


UPDATE system_settings SET version='2.1.5.031';##|##
 ##Updating database to version 2.1.5.031;
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##Clearing last_update_check flag.;
