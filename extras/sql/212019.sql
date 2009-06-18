# 06/17/2009
ALTER TABLE osdial_ivr ADD reserve_agents INT(3) default '2';

UPDATE system_settings SET version='2.1.2.019';
