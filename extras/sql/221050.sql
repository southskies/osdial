# 03/08/2010

ALTER TABLE osdial_scripts MODIFY script_id VARCHAR(20);##|##
 ##    Option to turn the Lead Allocation menu on / off, default to on.;

ALTER TABLE osdial_lead_filters MODIFY lead_filter_id VARCHAR(20);##|##
 ##    Option to turn the External Agents menu on / off, defaults to off.;

ALTER TABLE osdial_companies DROP system_campaign_trunks, DROP system_status_categories;##|##
 ##    Remove unusued columns from companies.;

ALTER TABLE osdial_companies CHANGE campaign_ivr enable_campaign_ivr ENUM('0','1') DEFAULT '0';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE campaign_listmix enable_campaign_listmix ENUM('0','1') DEFAULT '1';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE lead_export export_leads ENUM('0','1') DEFAULT '1';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE scripts enable_scripts ENUM('0','1') DEFAULT '1';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE filters enable_filters ENUM('0','1') DEFAULT '0';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE ingroups enable_ingroups ENUM('0','1') DEFAULT '0';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE external_agents enable_external_agents ENUM('0','1') DEFAULT '0';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE system_call_times enable_system_calltimes ENUM('0','1') DEFAULT '0';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE system_phones enable_system_phones ENUM('0','1') DEFAULT '1';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE system_conferences enable_system_conferences ENUM('0','1') DEFAULT '0';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE system_servers enable_system_servers ENUM('0','1') DEFAULT '0';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_companies CHANGE system_statuses enable_system_statuses ENUM('0','1') DEFAULT '0';##|##
 ##    Rename some companies columns.;

ALTER TABLE osdial_campaigns MODIFY campaign_script VARCHAR(20);##|##
 ##    Adjust size of campaign campaign_script field.;

ALTER TABLE osdial_inbound_groups MODIFY ingroup_script VARCHAR(20);##|##
 ##    Adjust size of inbound groups ingroup_script field.;

ALTER TABLE osdial_campaigns MODIFY lead_filter_id VARCHAR(20);##|##
 ##    Adjust size of campaign lead_filter_id field.;

ALTER TABLE osdial_companies MODIFY dnc_method enum('SYSTEM','COMPANY','BOTH') default 'BOTH';##|##
 ##    Fix typo in dnc_method field.;

CREATE TABLE osdial_dnc_company (
  company_id TINYINT(3) UNSIGNED NOT NULL,
  phone_number varchar(12) NOT NULL,
  creation_date timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (company_id,phone_number)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
 ##    Add osdial_dnc_company table.;

UPDATE system_settings SET version='2.2.1.050',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.050 and clearing last_update_check flag.;
