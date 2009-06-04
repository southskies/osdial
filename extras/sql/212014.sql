# 06/04/2009
ALTER TABLE osdial_lead_recycle CHANGE COLUMN attempt_delay attempt_delay int(11) unsigned default '1800';
ALTER TABLE osdial_lead_recycle CHANGE COLUMN attempt_maximum attempt_maximum int(5) unsigned default '32';

UPDATE system_settings SET version='2.1.2.014';
