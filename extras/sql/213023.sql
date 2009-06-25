# 06/25/2009

ALTER TABLE osdial_call_times ADD use_recycle_gap ENUM('Y','N') default 'N';

UPDATE system_settings SET version='2.1.3.023';
