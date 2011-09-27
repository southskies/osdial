# 12/20/2010

CREATE TABLE osdial_media (
  id INT(11) NOT NULL auto_increment,
  filename VARCHAR(100) NOT NULL,
  mimetype VARCHAR(50) NOT NULL,
  description VARCHAR(255) NOT NULL,
  extension VARCHAR(20) NOT NULL,
  created TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY (filename)
) ENGINE=InnoDB;##|##
  ## Table for media files.;

CREATE TABLE osdial_media_data (
  id INT(11) NOT NULL auto_increment,
  filename VARCHAR(255) NOT NULL,
  filedata MEDIUMBLOB,
  PRIMARY KEY (id,filename)
) ENGINE=InnoDB;##|##
  ## Table for media file data.;

UPDATE system_settings SET version='2.2.9.083',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.083 and clearing last_update_check flag.;
