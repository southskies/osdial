# 05/19/2009
ALTER TABLE osdial_campaigns MODIFY dial_method ENUM('MANUAL','RATIO','ADAPT_HARD_LIMIT','ADAPT_TAPERED','ADAPT_AVERAGE','INBOUND_MAN') default 'MANUAL';

UPDATE system_settings SET version='2.1.2.011';
