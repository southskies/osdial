# 03/25/2009
UPDATE system_settings SET version='2.1.0.002';

ALTER TABLE osdial_list CHANGE COLUMN security_phrase custom1 VARCHAR(255);
ALTER TABLE osdial_list ADD COLUMN custom2 VARCHAR(255);
