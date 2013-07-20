# 07/20/2013

ALTER TABLE system_settings MODIFY osdial_agent_disable enum('NOT_ACTIVE','LIVE_AGENT','EXTERNAL','ALL') DEFAULT 'LIVE_AGENT';##|##
 ## ;

UPDATE system_settings SET osdial_agent_disable='LIVE_AGENT';##|##
 ## ;

UPDATE system_settings SET version='3.0.1.101',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.101 and clearing last_update_check flag.;
