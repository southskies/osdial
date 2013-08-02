# 08/01/2013

ALTER TABLE osdial_inbound_groups ADD COLUMN callback_interval int(11) NOT NULL DEFAULT '0';##|##
 ## Callback interval;

ALTER TABLE osdial_inbound_groups ADD COLUMN callback_interrupt_key char(1) NOT NULL DEFAULT '*';##|##
 ## Callback interrupt key;

UPDATE system_settings SET version='3.0.1.107',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.107 and clearing last_update_check flag.;
