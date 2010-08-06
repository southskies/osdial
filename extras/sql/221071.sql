# 08/06/2010


ALTER TABLE osdial_campaigns ADD disable_manual_dial ENUM('N','Y') default 'N';##|##
 ##    Adds option to disable manual dialing at the campaign level.;

UPDATE system_settings SET version='2.2.1.071',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.071 and clearing last_update_check flag.;
