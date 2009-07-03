# 07/01/2009

ALTER TABLE phones MODIFY pass VARCHAR(20);

UPDATE system_settings SET version='2.1.3.026';
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);
