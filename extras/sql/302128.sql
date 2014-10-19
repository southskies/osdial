# 10/19/2014

CREATE TABLE osdial_playlist (
  id INT(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30),
  description VARCHAR(255),
  autoadvance enum('Y','N') NOT NULL DEFAULT 'N'
) Engine=InnoDB;##|##
  ## Create Playlist table.;

CREATE TABLE osdial_playlist_items (
  id INT(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  playlist_id INT(11) unsigned NOT NULL,
  parent_id INT(11) unsigned NOT NULL,
  priority INT(5) unsigned NOT NULL DEFAULT '0',
  label VARCHAR(255),
  phrase TEXT,
  extension VARCHAR(255),
  KEY `playlist` (`playlist_id`),
  KEY `self` (`parent_id`)
) Engine=InnoDB;##|##
  ## Create Playlist items table.;

CREATE TABLE osdial_playlist_item_users (
  id INT(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  playlist_item_id INT(11) unsigned NOT NULL,
  user VARCHAR(20),
  extension VARCHAR(255),
  KEY `playlistitem` (`playlist_item_id`),
) Engine=InnoDB;##|##
  ## Create Playlist item users table.;

ALTER TABLE osdial_campaigns ADD COLUMN `playlist_id` INT(11) unsigned NOT NULL;##|##
 ##Add playlist_id to campaigns.;

ALTER TABLE osdial_inbound_groups ADD COLUMN `playlist_id` INT(11) unsigned NOT NULL;##|##
 ##Add playlist_id to inbound groups.;

UPDATE system_settings SET version='3.0.2.128',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.2.128 and clearing last_update_check flag.;
