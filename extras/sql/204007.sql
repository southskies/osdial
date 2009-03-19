#index osdial_agent_log - 2008-10-22 23:12
create index time_user ON osdial_agent_log (event_time,user);

UPDATE system_settings SET version='2.0.4.007';
