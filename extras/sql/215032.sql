# 09/14/2009

CREATE TABLE osdial_script_buttons (
  script_id varchar(10) NOT NULL,
  script_button_id varchar(10) NOT NULL,
  script_button_description varchar(100) default '',
  script_button_label varchar(50) default '',
  script_button_text TEXT default '',
  PRIMARY KEY  (script_id,script_button_id)
) ENGINE=InnoDB;##|##
  ## Adds ability to have buttons/conditional scripting;

ALTER TABLE osdial_list ADD COLUMN post_date DATETIME NOT NULL;##|##
 ##Add post_date field to leads;

INSERT INTO osdial_statuses VALUES ('PD','Post Date','Y','Y','CONTACT');##|##
 ##Add post-date PD status.;



UPDATE system_settings SET version='2.1.5.032',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.1.5.032 and clearing last_update_check flag.;
