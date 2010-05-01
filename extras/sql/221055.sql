# 04/30/2010

ALTER TABLE osdial_hopper MODIFY status enum('READY','QUEUE','INCALL','DONE','HOLD','API') DEFAULT 'READY';##|##
 ##    Added status for API insertion.;

ALTER TABLE osdial_campaigns MODIFY web_form_address VARCHAR(2000) DEFAULT '/osdial/agent/webform_redirect.php';##|##
 ##    Set max length on campaign web-form1 to 2000 characters.;

ALTER TABLE osdial_campaigns MODIFY web_form_address2 VARCHAR(2000) DEFAULT '/osdial/agent/webform_redirect.php';##|##
 ##    Set max length on campaign web-form2 to 2000 characters.;

ALTER TABLE osdial_lists MODIFY web_form_address VARCHAR(2000) DEFAULT '/osdial/agent/webform_redirect.php';##|##
 ##    Set max length on lists web-form1 to 2000 characters.;

ALTER TABLE osdial_lists MODIFY web_form_address2 VARCHAR(2000) DEFAULT '/osdial/agent/webform_redirect.php';##|##
 ##    Set max length on lists web-form2 to 2000 characters.;

ALTER TABLE osdial_inbound_groups MODIFY web_form_address VARCHAR(2000) DEFAULT '/osdial/agent/webform_redirect.php';##|##
 ##    Set max length on in-groups web-form1 to 2000 characters.;

ALTER TABLE osdial_inbound_groups MODIFY web_form_address2 VARCHAR(2000) DEFAULT '/osdial/agent/webform_redirect.php';##|##
 ##    Set max length on in-groups web-form2 to 2000 characters.;

UPDATE system_settings SET version='2.2.1.055',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.055 and clearing last_update_check flag.;
