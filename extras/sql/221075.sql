# 09/12/2010

ALTER TABLE osdial_campaigns ADD hide_xfer_local_closer enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide local closer.;

ALTER TABLE osdial_campaigns ADD hide_xfer_dial_override enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide dial override.;

ALTER TABLE osdial_campaigns ADD hide_xfer_hangup_xfer enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide hangup xfer line.;

ALTER TABLE osdial_campaigns ADD hide_xfer_leave_3way enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide leave 3way call.;

ALTER TABLE osdial_campaigns ADD hide_xfer_dial_with enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide dial with customer.;

ALTER TABLE osdial_campaigns ADD hide_xfer_hangup_both enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide hangup both lines.;

ALTER TABLE osdial_campaigns ADD hide_xfer_blind_xfer enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide blind transfer.;

ALTER TABLE osdial_campaigns ADD hide_xfer_park_dial enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide park customer dial.;

ALTER TABLE osdial_campaigns ADD hide_xfer_blind_vmail enum('Y','N') DEFAULT 'N';##|##
 ##    Adds option to hide dial blind vmail.;

UPDATE system_settings SET version='2.2.1.075',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.075 and clearing last_update_check flag.;
