# 07/22/2010


ALTER TABLE osdial_user_groups ADD allowed_scripts text default ' -ALL-SCRIPTS- -';##|##
 ##    Updates to osdial_user_groups for allowed_scripts.;

UPDATE osdial_user_groups SET allowed_scripts=' -ALL-SCRIPTS- -';##|##
 ##      Add default values.;

ALTER TABLE osdial_user_groups ADD allowed_email_templates text default ' -ALL-EMAIL-TEMPLATES- -';##|##
 ##    Updates to osdial_user_groups for allowed_email_templates.;

UPDATE osdial_user_groups SET allowed_email_templates=' -ALL-EMAIL-TEMPLATES- -';##|##
 ##      Add default values.;

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

ALTER TABLE osdial_campaigns ADD email_template_id VARCHAR(20) default '';##|##
 ##    Updates to osdial_campaigns for email_templates.;


UPDATE system_settings SET version='2.2.1.066',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.066 and clearing last_update_check flag.;
