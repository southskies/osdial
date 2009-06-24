# 06/23/2009

ALTER TABLE system_settings ADD last_update_check DATETIME;
ALTER TABLE system_settings ADD last_update_version VARCHAR(50);

UPDATE system_settings SET version='2.1.3.022';
