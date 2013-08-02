# 08/02/2013

ALTER TABLE osdial_inbound_groups ADD COLUMN onhold_startdelay smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

ALTER TABLE osdial_inbound_groups ADD COLUMN callback_startdelay smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

ALTER TABLE osdial_inbound_groups MODIFY callback_interval smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

ALTER TABLE osdial_inbound_groups ADD COLUMN placement_startdelay smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

ALTER TABLE osdial_inbound_groups MODIFY placement_interval smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

ALTER TABLE osdial_inbound_groups MODIFY placement_max_repeat smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

ALTER TABLE osdial_inbound_groups ADD COLUMN queuetime_startdelay smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

ALTER TABLE osdial_inbound_groups MODIFY queuetime_interval smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

ALTER TABLE osdial_inbound_groups MODIFY queuetime_max_repeat smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Wait time before first prompt;

UPDATE system_settings SET version='3.0.1.110',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.110 and clearing last_update_check flag.;
