# 06/02/2009
ALTER TABLE osdial_ivr ADD timeout_action VARCHAR(1) default '';

UPDATE system_settings SET version='2.1.2.013';
