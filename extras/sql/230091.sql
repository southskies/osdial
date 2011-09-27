# 09/25/2011

CREATE TABLE osdial_tts (
  id INT(11) NOT NULL auto_increment,
  description VARCHAR(255) NOT NULL,
  extension VARCHAR(20) NOT NULL,
  phrase mediumtext,
  voice VARCHAR(100) NOT NULL,
  created TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;##|##
  ## Table for tts phrases.;

UPDATE system_settings SET version='2.3.0.091',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.3.0.091 and clearing last_update_check flag.;
