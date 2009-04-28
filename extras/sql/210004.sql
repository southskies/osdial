# 04/28/2009
UPDATE system_settings SET version='2.1.0.004';

ALTER TABLE osdial_campaigns MODIFY web_form_address VARCHAR(255) default '/osdial/agent/webform_redirect.php';
ALTER TABLE osdial_campaigns MODIFY web_form_address2 VARCHAR(255) default '/osdial/agent/webform_redirect.php';
ALTER TABLE osdial_inbound_groups MODIFY web_form_address VARCHAR(255) default '/osdial/agent/webform_redirect.php';
ALTER TABLE osdial_inbound_groups MODIFY web_form_address2 VARCHAR(255) default '/osdial/agent/webform_redirect.php';
