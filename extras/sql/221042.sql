# 02/28/2010

CREATE INDEX lead_id ON osdial_agent_log (lead_id);##|##
 ##    Add index to agent_log to provide better grouping by lead.;

CREATE INDEX campaign_id ON osdial_agent_log (campaign_id);##|##
 ##    Add index to agent_log to provide better grouping by campaign.;

UPDATE system_settings SET version='2.2.1.042',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.042 and clearing last_update_check flag.;
