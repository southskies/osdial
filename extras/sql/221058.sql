# 05/31/2010

CREATE TABLE osdial_script_button_log (
  id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  lead_id INT(9) UNSIGNED NOT NULL,
  script_id VARCHAR(10) NOT NULL,
  script_button_id VARCHAR(10) NOT NULL,
  user VARCHAR(20) NOT NULL,
  event_time timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  KEY lead_id (lead_id),
  KEY user_lead (user,lead_id),
  KEY result (script_id,script_button_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
  ## Adds logging to conditional scripting.;

UPDATE system_settings SET version='2.2.1.058',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.058 and clearing last_update_check flag.;
