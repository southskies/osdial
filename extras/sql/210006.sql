# 04/30/2009
UPDATE system_settings SET version='2.1.0.006';

ALTER TABLE osdial_campaigns ADD campaign_call_time VARCHAR(10) DEFAULT '',
