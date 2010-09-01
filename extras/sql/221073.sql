# 09/01/2010


ALTER TABLE live_sip_channels MODIFY channel VARCHAR(255) NOT NULL default '';##|##
 ##    Increase channel size.;

ALTER TABLE live_sip_channels MODIFY channel_group VARCHAR(255) NOT NULL default '';##|##
 ##    Increase channel_group size.;

UPDATE system_settings SET version='2.2.1.073',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.073 and clearing last_update_check flag.;
