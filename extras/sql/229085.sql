# 04/15/2011

ALTER TABLE osdial_campaigns MODIFY amd_send_to_vmx ENUM('Y','N','CUSTOM1','CUSTOM2') default 'N';##|##
 ## Add options for using the custom1/custom2 fields for the filename to play to the answering machine.;

UPDATE system_settings SET version='2.2.9.085',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.085 and clearing last_update_check flag.;
