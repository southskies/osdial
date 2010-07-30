# 07/30/2010


ALTER TABLE osdial_campaigns CHANGE email_template_id email_templates text;##|##
 ##    Updates to osdial_campaigns for email_templates.;


UPDATE system_settings SET version='2.2.1.067',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.067 and clearing last_update_check flag.;
