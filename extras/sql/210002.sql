# 03/25/2009
UPDATE system_settings SET version='2.1.0.002';

ALTER TABLE osdial_list CHANGE COLUMN security_phrase custom1 VARCHAR(255);
ALTER TABLE osdial_list ADD COLUMN custom2 VARCHAR(255);
ALTER TABLE osdial_list ADD COLUMN external_key VARCHAR(100);

ALTER TABLE osdial_inbound_groups ADD COLUMN allow_tab_switch ENUM('Y','N') default 'Y';
ALTER TABLE osdial_campaigns ADD COLUMN allow_tab_switch ENUM('Y','N') default 'Y';
