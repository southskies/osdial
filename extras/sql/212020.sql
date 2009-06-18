# 06/18/2009
create index se_st_ed ON osdial_manager (server_ip,status,entry_date);
create index se_ci_st ON osdial_manager (server_ip,callerid,status);
create index se_ci ON osdial_manager (server_ip,callerid);
create index se_un_ci ON osdial_manager (server_ip,uniqueid,callerid);

UPDATE system_settings SET version='2.1.2.020';
