# 07/22/2010


ALTER TABLE osdial_user_groups ADD allowed_scripts text default '';##|##
 ##    Updates to osdial_user_groups for allowed_scripts.;

ALTER TABLE osdial_user_groups ADD allowed_email_templates text default '';##|##
 ##    Updates to osdial_user_groups for allowed_email_templates.;

CREATE TABLE osdial_email_templates (
  et_id varchar(20) NOT NULL DEFAULT '',
  et_name varchar(50) DEFAULT '',
  et_comments varchar(255) DEFAULT '',
  et_host varchar(255) DEFAULT 'localhost',
  et_port varchar(5) DEFAULT '25',
  et_user varchar(255) DEFAULT '',
  et_pass varchar(255) DEFAULT '',
  et_from varchar(255) DEFAULT '',
  et_subject varchar(255) DEFAULT '',
  et_body_html text,
  et_body_text text,
  active enum('Y','N') DEFAULT NULL,
  PRIMARY KEY (et_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
 ##     Create email templates table.;


UPDATE system_settings SET version='2.2.1.066',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.066 and clearing last_update_check flag.;
