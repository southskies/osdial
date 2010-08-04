# 08/04/2010


ALTER TABLE osdial_inbound_groups ADD drop_trigger ENUM('CALL_SECONDS_TIMEOUT','NO_AGENTS_CONNECTED','NO_AGENTS_AVAILABLE') default 'CALL_SECONDS_TIMEOUT';##|##
 ##    Updates to osdial_inbound_groups to set a drop_trigger to wait for the timeout or check for agent availability.;

ALTER TABLE osdial_closer_log MODIFY term_reason ENUM('CALLER','AGENT','QUEUETIMEOUT','ABANDON','AFTERHOURS','NONE','NOAGENTS','NOAGENTSAVAILABLE') default 'NONE';##|##
 ##    Updates to osdial_closer_log for ingroups without agents or unavailable agents.;

UPDATE system_settings SET version='2.2.1.069',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.069 and clearing last_update_check flag.;
