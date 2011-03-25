# 03/24/2011

ALTER TABLE osdial_inbound_groups ADD allow_multicall ENUM('Y','N') default 'N';##|##
 ## Add multicall capabilities.;

UPDATE osdial_inbound_groups SET allow_mutlicall='Y' WHERE group_id LIKE 'A2A%';##|##
 ## Turn multicall on by default for A2A.;

UPDATE system_settings SET version='2.2.9.084',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.084 and clearing last_update_check flag.;
