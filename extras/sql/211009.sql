# 05/15/2009
ALTER TABLE osdial_campaigns ADD manual_force_dial_time TINYINT UNSIGNED default '10';
ALTER TABLE osdial_campaigns ADD manual_preview_default ENUM('Y','N') default 'Y';

UPDATE system_settings SET version='2.1.1.009';
