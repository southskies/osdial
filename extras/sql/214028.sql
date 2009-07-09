# 07/07/2009

ALTER TABLE osdial_xfer_log ADD uniqueid VARCHAR(20) NOT NULL default '';
CREATE INDEX uniqueid ON osdial_xfer_log (uniqueid);

ALTER TABLE osdial_agent_log ADD uniqueid VARCHAR(20) NOT NULL default '';
CREATE INDEX uniqueid ON osdial_agent_log (uniqueid);

ALTER TABLE recording_log ADD uniqueid VARCHAR(20) NOT NULL default '';
CREATE INDEX uniqueid ON recording_log (uniqueid);

ALTER TABLE osdial_live_agents MODIFY closer_campaigns TEXT default '';

UPDATE system_settings SET version='2.1.4.028';
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);
