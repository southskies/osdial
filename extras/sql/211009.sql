# 05/15/2009
ALTER TABLE osdial_campaigns ADD preview_force_dial_time TINYINT UNSIGNED default '10';
ALTER TABLE osdial_campaigns ADD manual_preview_default ENUM('Y','N') default 'Y';
ALTER TABLE osdial_campaigns ADD web_form_extwindow ENUM('Y','N') default 'N';
ALTER TABLE osdial_campaigns ADD web_form2_extwindow ENUM('Y','N') default 'N';
ALTER TABLE osdial_campaigns MODIFY get_call_launch ENUM('NONE','SCRIPT','WEBFORM', 'WEBFORM2') default 'NONE';
ALTER TABLE osdial_inbound_groups ADD web_form_extwindow ENUM('Y','N') default 'Y';
ALTER TABLE osdial_inbound_groups ADD web_form2_extwindow ENUM('Y','N') default 'Y';
ALTER TABLE osdial_inbound_groups MODIFY get_call_launch ENUM('NONE','SCRIPT','WEBFORM', 'WEBFORM2') default 'NONE';

UPDATE system_settings SET version='2.1.1.009';
