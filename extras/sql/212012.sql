# 06/01/2009
ALTER TABLE osdial_ivr RENAME TO osdial_verification_ivr;
ALTER TABLE osdial_outbound_ivr RENAME TO osdial_ivr;
ALTER TABLE osdial_outbound_ivr_options RENAME TO osdial_ivr_options;
ALTER TABLE osdial_ivr_options CHANGE COLUMN outbound_ivr_id ivr_id INT(11);
ALTER TABLE osdial_closer_log ADD callerid VARCHAR(20) default '';

UPDATE system_settings SET version='2.1.2.012';
