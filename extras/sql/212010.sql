# 05/18/2009
ALTER TABLE osdial_list MODIFY last_name VARCHAR(30) default '' NOT NULL;
ALTER TABLE osdial_list MODIFY first_name VARCHAR(30) default '' NOT NULL;

UPDATE system_settings SET version='2.1.2.010';
