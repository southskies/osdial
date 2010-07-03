# 07/02/2010

ALTER TABLE osdial_user_groups ADD view_agent_pause_summary ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_pause_summary;

ALTER TABLE osdial_user_groups ADD export_agent_pause_summary ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_agent_pause_summary;

ALTER TABLE osdial_user_groups ADD view_agent_performance_detail ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_performance_detail;

ALTER TABLE osdial_user_groups ADD export_agent_performance_detail ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_agent_performance_detail;

ALTER TABLE osdial_user_groups ADD view_agent_realtime ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_realtime;

ALTER TABLE osdial_user_groups ADD view_agent_realtime_iax_barge ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_realtime_iax_barge;

ALTER TABLE osdial_user_groups ADD view_agent_realtime_iax_listen ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_realtime_iax_listen;

ALTER TABLE osdial_user_groups ADD view_agent_realtime_sip_barge ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_realtime_sip_barge;

ALTER TABLE osdial_user_groups ADD view_agent_realtime_sip_listen ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_realtime_sip_listen;

ALTER TABLE osdial_user_groups ADD view_agent_realtime_summary ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_realtime_summary;

ALTER TABLE osdial_user_groups ADD view_agent_stats ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_stats;

ALTER TABLE osdial_user_groups ADD view_agent_status ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_status;

ALTER TABLE osdial_user_groups ADD view_agent_timesheet ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_agent_timesheet;

ALTER TABLE osdial_user_groups ADD export_agent_timesheet ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_agent_timesheet;



ALTER TABLE osdial_user_groups ADD view_campaign_call_report ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_campaign_call_report;

ALTER TABLE osdial_user_groups ADD export_campaign_call_report ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_campaign_call_report;

ALTER TABLE osdial_user_groups ADD view_campaign_recent_outbound_sales ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_campaign_recent_outbound_sales;

ALTER TABLE osdial_user_groups ADD export_campaign_recent_outbound_sales ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_campaign_recent_outbound_sales;



ALTER TABLE osdial_user_groups ADD view_ingroup_call_report ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_ingroup_call_report;

ALTER TABLE osdial_user_groups ADD export_ingroup_call_report ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_ingroup_call_report;



ALTER TABLE osdial_user_groups ADD view_lead_performance_campaign ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_lead_performance_campaign;

ALTER TABLE osdial_user_groups ADD export_lead_performance_campaign ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_lead_performance_campaign;

ALTER TABLE osdial_user_groups ADD view_lead_performance_list ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_lead_performance_list;

ALTER TABLE osdial_user_groups ADD export_lead_performance_list ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_lead_performance_list;

ALTER TABLE osdial_user_groups ADD view_lead_search ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_lead_search;

ALTER TABLE osdial_user_groups ADD view_lead_search_advanced ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_lead_search_advanced;

ALTER TABLE osdial_user_groups ADD export_lead_search_advanced ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_lead_search_advanced;



ALTER TABLE osdial_user_groups ADD view_list_cost_entry ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_list_cost_entry;

ALTER TABLE osdial_user_groups ADD export_list_cost_entry ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: export_list_cost_entry;



ALTER TABLE osdial_user_groups ADD view_server_performance ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_server_performance;

ALTER TABLE osdial_user_groups ADD view_server_times ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_server_times;



ALTER TABLE osdial_user_groups ADD view_usergroup_hourly_stats ENUM('0','1') default '1';##|##
 ##    Report permissions based on UserGroup: view_usergroup_hourly_stats;



UPDATE system_settings SET version='2.2.1.063',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.063 and clearing last_update_check flag.;
