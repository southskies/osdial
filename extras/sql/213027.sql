# 07/06/2009

ALTER TABLE osdial_campaigns ADD COLUMN submit_method enum('NORMAL','WEBFORM1','WEBFORM2') default 'NORMAL';

UPDATE system_settings SET version='2.1.3.027';
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);
