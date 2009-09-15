# 09/14/2009

CREATE TABLE osdial_script_buttons (
  script_id varchar(10) NOT NULL,
  script_button_id varchar(10) NOT NULL,
  script_button_description varchar(100) default '',
  script_button_label varchar(50) default '',
  script_button_text TEXT default '',
  PRIMARY KEY  (script_id,script_button_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
  ## Adds ability to have buttons/conditional scripting;



UPDATE system_settings SET version='2.1.5.032';##|##
 ##Updating database to version 2.1.5.032;
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##Clearing last_update_check flag.;
