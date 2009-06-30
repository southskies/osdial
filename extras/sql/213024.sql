# 06/26/2009

ALTER TABLE osdial_ivr ADD allow_inbound ENUM('Y','N') default 'Y';

UPDATE system_settings SET version='2.1.3.024';
