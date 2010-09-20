# 09/19/2010

ALTER TABLE osdial_email_templates ADD et_send_action enum('ONDEMAND','ALL','ALLFORCE') DEFAULT 'ONDEMAND';##|##
 ##    Adds option to send email on-demand, to all, or force to all.;

UPDATE system_settings SET version='2.2.1.076',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.076 and clearing last_update_check flag.;
