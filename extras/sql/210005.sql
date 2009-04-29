# 04/29/2009
UPDATE system_settings SET version='2.1.0.005';

INSERT INTO osdial_statuses values('CRF','Carrier Failure','N','Y','SYSTEM');
INSERT INTO osdial_statuses values('CRR','Carrier Rejected','N','Y','SYSTEM');
INSERT INTO osdial_statuses values('CRO','Carrier Out-of-Order','N','Y','SYSTEM');
INSERT INTO osdial_statuses values('CRC','Carrier Congestion','N','Y','SYSTEM');
