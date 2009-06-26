# 06/26/2009

ALTER TABLE osdial_ivr ADD allow_inbound ENUM('Y','N') default 'Y';
GRANT ALL on osdial.* TO 'osdial'@'127.0.0.1' IDENTIFIED BY 'osdial1234';
GRANT ALL on osdial.* TO 'osdial'@'localhost' IDENTIFIED BY 'osdial1234';
GRANT ALL on osdial.* TO 'osdial'@'%' IDENTIFIED BY 'osdial1234';

UPDATE system_settings SET version='2.1.3.024';
