# 06/06/2009
ALTER TABLE phones CHANGE COLUMN protocol protocol ENUM('SIP','Zap','IAX2','EXTERNAL','DAHDI') default 'SIP';


UPDATE system_settings SET version='2.1.2.015';
