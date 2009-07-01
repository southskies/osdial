# 06/30/2009

ALTER TABLE osdial_lists ADD scrub_dnc ENUM('Y','N') default 'N';
ALTER TABLE osdial_lists ADD scrub_last DATETIME;
ALTER TABLE osdial_lists ADD scrub_info VARCHAR(20) default '';

INSERT INTO configuration (name,data) values('External_DNC_Active','N');
INSERT INTO configuration (name,data) values('External_DNC_Address','');
INSERT INTO configuration (name,data) values('External_DNC_Database','');
INSERT INTO configuration (name,data) values('External_DNC_Username','');
INSERT INTO configuration (name,data) values('External_DNC_Password','');
INSERT INTO configuration (name,data) values('External_DNC_SQL','');

UPDATE system_settings SET version='2.1.3.025';
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);
