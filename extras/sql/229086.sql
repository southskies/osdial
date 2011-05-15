# 05/14/2011

ALTER TABLE osdial_user_groups ADD allowed_ingroups text;##|##
 ##    Updates to osdial_user_groups for allowed_ingroups.;

UPDATE osdial_user_groups SET allowed_ingroups=' -ALL-INGROUPS- -';##|##
 ##      Add default values.;

UPDATE system_settings SET version='2.2.9.086',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.086 and clearing last_update_check flag.;
