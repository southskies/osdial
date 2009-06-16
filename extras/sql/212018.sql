# 06/16/2009
create index uniqeuid ON osdial_live_agents (uniqueid);
create index callerid ON osdial_live_agents (callerid);
create index unique_caller ON osdial_live_agents (uniqueid,callerid);
create index us ON osdial_live_agents (user);
create index conf ON osdial_live_agents (conf_exten);
create index ussp ON osdial_live_agents (user,server_ip);
create index uscs ON osdial_live_agents (user,campaign_id);
create index usall1 ON osdial_live_agents (user,server_ip,campaign_id);
create index usall2 ON osdial_live_agents (user,server_ip,conf_exten);
create index usall3 ON osdial_live_agents (user,server_ip,campaign_id,conf_exten);



UPDATE system_settings SET version='2.1.2.018';
