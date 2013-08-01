# 08/01/2013

ALTER TABLE osdial_inbound_groups ADD COLUMN placement_interval int(11) NOT NULL DEFAULT '0';##|##
 ## Placement interval;

ALTER TABLE osdial_inbound_groups ADD COLUMN placement_max_repeat int(11) NOT NULL DEFAULT '0';##|##
 ## Placement repeat count;

ALTER TABLE osdial_inbound_groups ADD COLUMN queuetime_interval int(11) NOT NULL DEFAULT '0';##|##
 ## Estimated Queue Time interval;

ALTER TABLE osdial_inbound_groups ADD COLUMN queuetime_max_repeat int(11) NOT NULL DEFAULT '0';##|##
 ## Estimated Queue Time repeat count;

ALTER TABLE osdial_inbound_groups ADD COLUMN background_music_filename varchar(50) NOT NULL DEFAULT 'conf';##|##
 ## Filename for background music for use with new ExternalIVR based ingroup;

ALTER TABLE osdial_inbound_groups MODIFY after_hours_action enum('HANGUP','MESSAGE','EXTENSION','VOICEMAIL','CALLBACK') NOT NULL DEFAULT 'HANGUP';##|##
 ## Added CALLBACK and made HANGUP the default;

ALTER TABLE osdial_inbound_groups MODIFY drop_message varchar(20) NOT NULL DEFAULT 'N';##|##
 ## Change drop_message to varchar;

UPDATE osdial_inbound_groups SET drop_message='EXTENSION' WHERE drop_message='Y';##|##
 ## Drop Message set EXTENSION from Y;

UPDATE osdial_inbound_groups SET drop_message='VOICEMAIL' WHERE drop_message='N' OR drop_message='';##|##
 ## Drop Message set VOICEMAIL from N;

ALTER TABLE osdial_inbound_groups CHANGE drop_message drop_action enum('HANGUP','MESSAGE','EXTENSION','VOICEMAIL','CALLBACK') NOT NULL DEFAULT 'HANGUP';##|##
 ## Rename drop_message to drop_action and change type.;

ALTER TABLE osdial_inbound_groups ADD COLUMN drop_message_filename varchar(50) NOT NULL DEFAULT '';##|##
 ## Filename for drop_action type of MESSAGE;

UPDATE system_settings SET version='3.0.1.106',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.106 and clearing last_update_check flag.;
