
#index on call_log - 2008-10-15 18:40
create index start_time ON call_log (start_time);
create index end_time ON call_log (end_time);
create index time ON call_log (start_time,end_time);
create index list_phone ON osdial_list (list_id,phone_number);
create index list_status ON osdial_list (list_id,status);

UPDATE system_settings SET version='2.0.4-006';
