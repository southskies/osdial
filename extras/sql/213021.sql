# 06/22/2009

ALTER TABLE system_settings ADD admin_template VARCHAR(50) default 'default';
ALTER TABLE system_settings ADD agent_template VARCHAR(50) default 'default';

UPDATE system_settings SET version='2.1.3.021';
