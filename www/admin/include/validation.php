<?php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
# Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
#
#     This file is part of OSDial.
#
#     OSDial is free software: you can redistribute it and/or modify
#     it under the terms of the GNU Affero General Public License as
#     published by the Free Software Foundation, either version 3 of
#     the License, or (at your option) any later version.
#
#     OSDial is distributed in the hope that it will be useful,
#     but WITHOUT ANY WARRANTY; without even the implied warranty of
#     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#     GNU Affero General Public License for more details.
#
#     You should have received a copy of the GNU Affero General Public
#     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
#
# 090410-1731 - Added allow_tab_switch
# 090420-1846 - Added answers_per_hour_limit
# 090515-0135 - Added preview_force_dial_time
# 090515-0140 - Added manual_preview_default
# 090515-0538 - Added web_form_extwindow and web_form2_extwindow


######################################################################################################
######################################################################################################
#######   Form variable filtering for security and data integrity
######################################################################################################
######################################################################################################

if ($non_latin < 1)	{
	### DIGITS ONLY ###
	$areacode = preg_replace("/[^0-9]/","",$areacode);
	$alt_phone = preg_replace("/[^0-9]/","",$alt_phone);
	$adaptive_dropped_percentage = preg_replace("/[^0-9]/","",$adaptive_dropped_percentage);
	$adaptive_latest_server_time = preg_replace("/[^0-9]/","",$adaptive_latest_server_time);
	$admin_api_access = preg_replace("/[^0-9]/","",$admin_api_access);
	$admin_hangup_enabled = preg_replace("/[^0-9]/","",$admin_hangup_enabled);
	$admin_hijack_enabled = preg_replace("/[^0-9]/","",$admin_hijack_enabled);
	$admin_monitor_enabled = preg_replace("/[^0-9]/","",$admin_monitor_enabled);
	$AFLogging_enabled = preg_replace("/[^0-9]/","",$AFLogging_enabled);
	$agent_api_access = preg_replace("/[^0-9]/","",$agent_api_access);
	$agent_choose_ingroups = preg_replace("/[^0-9]/","",$agent_choose_ingroups);
	$agentcall_manual = preg_replace("/[^0-9]/","",$agentcall_manual);
	$agentonly_callbacks = preg_replace("/[^0-9]/","",$agentonly_callbacks);
	$AGI_call_logging_enabled = preg_replace("/[^0-9]/","",$AGI_call_logging_enabled);
	$allcalls_delay = preg_replace("/[^0-9]/","",$allcalls_delay);
	$alter_agent_interface_options = preg_replace("/[^0-9]/","",$alter_agent_interface_options);
	$am_message_exten = preg_replace("/[^0-9]/","",$am_message_exten);
	$answer_transfer_agent = preg_replace("/[^0-9]/","",$answer_transfer_agent);
	$ast_admin_access = preg_replace("/[^0-9]/","",$ast_admin_access);
	$ast_delete_phones = preg_replace("/[^0-9]/","",$ast_delete_phones);
	$attempt_delay = preg_replace("/[^0-9]/","",$attempt_delay);
	$attempt_maximum = preg_replace("/[^0-9]/","",$attempt_maximum);
	$auto_dial_next_number = preg_replace("/[^0-9]/","",$auto_dial_next_number);
	$balance_trunks_offlimits = preg_replace("/[^0-9]/","",$balance_trunks_offlimits);
	$call_parking_enabled = preg_replace("/[^0-9]/","",$call_parking_enabled);
	$answers_per_hour_limit = preg_replace("/[^0-9]/","",$answers_per_hour_limit);
	$CallerID_popup_enabled = preg_replace("/[^0-9]/","",$CallerID_popup_enabled);
	$campaign_detail = preg_replace("/[^0-9]/","",$campaign_detail);
	$campaign_rec_exten = preg_replace("/[^0-9]/","",$campaign_rec_exten);
	$campaign_vdad_exten = preg_replace("/[^0-9]/","",$campaign_vdad_exten);
	$change_agent_campaign = preg_replace("/[^0-9]/","",$change_agent_campaign);
	$closer_default_blended = preg_replace("/[^0-9]/","",$closer_default_blended);
	$conf_exten = preg_replace("/[^0-9]/","",$conf_exten);
	$conf_on_extension = preg_replace("/[^0-9]/","",$conf_on_extension);
	$conferencing_enabled = preg_replace("/[^0-9]/","",$conferencing_enabled);
	$company_id = preg_replace("/[^0-9]/","",$company_id);
	$ct_default_start = preg_replace("/[^0-9]/","",$ct_default_start);
	$ct_default_stop = preg_replace("/[^0-9]/","",$ct_default_stop);
	$ct_friday_start = preg_replace("/[^0-9]/","",$ct_friday_start);
	$ct_friday_stop = preg_replace("/[^0-9]/","",$ct_friday_stop);
	$ct_monday_start = preg_replace("/[^0-9]/","",$ct_monday_start);
	$ct_monday_stop = preg_replace("/[^0-9]/","",$ct_monday_stop);
	$ct_saturday_start = preg_replace("/[^0-9]/","",$ct_saturday_start);
	$ct_saturday_stop = preg_replace("/[^0-9]/","",$ct_saturday_stop);
	$ct_sunday_start = preg_replace("/[^0-9]/","",$ct_sunday_start);
	$ct_sunday_stop = preg_replace("/[^0-9]/","",$ct_sunday_stop);
	$ct_thursday_start = preg_replace("/[^0-9]/","",$ct_thursday_start);
	$ct_thursday_stop = preg_replace("/[^0-9]/","",$ct_thursday_stop);
	$ct_tuesday_start = preg_replace("/[^0-9]/","",$ct_tuesday_start);
	$ct_tuesday_stop = preg_replace("/[^0-9]/","",$ct_tuesday_stop);
	$ct_wednesday_start = preg_replace("/[^0-9]/","",$ct_wednesday_start);
	$ct_wednesday_stop = preg_replace("/[^0-9]/","",$ct_wednesday_stop);
	$DBX_port = preg_replace("/[^0-9]/","",$DBX_port);
	$DBY_port = preg_replace("/[^0-9]/","",$DBY_port);
	$dedicated_trunks = preg_replace("/[^0-9]/","",$dedicated_trunks);
	$delete_call_times = preg_replace("/[^0-9]/","",$delete_call_times);
	$delete_campaigns = preg_replace("/[^0-9]/","",$delete_campaigns);
	$delete_dnc = preg_replace("/[^0-9]/","",$delete_dnc);
	$delete_filters = preg_replace("/[^0-9]/","",$delete_filters);
	$delete_ingroups = preg_replace("/[^0-9]/","",$delete_ingroups);
	$delete_lists = preg_replace("/[^0-9]/","",$delete_lists);
	$delete_remote_agents = preg_replace("/[^0-9]/","",$delete_remote_agents);
	$delete_scripts = preg_replace("/[^0-9]/","",$delete_scripts);
	$delete_user_groups = preg_replace("/[^0-9]/","",$delete_user_groups);
	$delete_users = preg_replace("/[^0-9]/","",$delete_users);
	$dial_timeout = preg_replace("/[^0-9]/","",$dial_timeout);
	$dialplan_number = preg_replace("/[^0-9]/","",$dialplan_number);
	$drop_call_seconds = preg_replace("/[^0-9]/","",$drop_call_seconds);
	$drop_exten = preg_replace("/[^0-9]/","",$drop_exten);
	$enable_fast_refresh = preg_replace("/[^0-9]/","",$enable_fast_refresh);
	$enable_persistant_mysql = preg_replace("/[^0-9]/","",$enable_persistant_mysql);
	$export_leads = preg_replace("/[^0-9]/","",$export_leads);
	$fast_refresh_rate = preg_replace("/[^0-9]/","",$fast_refresh_rate);
	$hopper_level = preg_replace("/[^0-9]/","",$hopper_level);
	$hotkey = preg_replace("/[^0-9]/","",$hotkey);
	$hotkeys_active = preg_replace("/[^0-9]/","",$hotkeys_active);
	$list_id = preg_replace("/[^0-9]/","",$list_id);
	$list_id_override = preg_replace("/[^0-9]/","",$list_id_override);
	$load_dnc = preg_replace("/[^0-9]/","",$load_dnc);
	$load_leads = preg_replace("/[^0-9]/","",$load_leads);
	$max_osdial_trunks = preg_replace("/[^0-9]/","",$max_osdial_trunks);
	$modify_call_times = preg_replace("/[^0-9]/","",$modify_call_times);
	$modify_users = preg_replace("/[^0-9]/","",$modify_users);
	$modify_campaigns = preg_replace("/[^0-9]/","",$modify_campaigns);
	$modify_lists = preg_replace("/[^0-9]/","",$modify_lists);
	$modify_scripts = preg_replace("/[^0-9]/","",$modify_scripts);
	$modify_filters = preg_replace("/[^0-9]/","",$modify_filters);
	$modify_ingroups = preg_replace("/[^0-9]/","",$modify_ingroups);
	$modify_usergroups = preg_replace("/[^0-9]/","",$modify_usergroups);
	$modify_remoteagents = preg_replace("/[^0-9]/","",$modify_remoteagents);
	$modify_servers = preg_replace("/[^0-9]/","",$modify_servers);
	$view_reports = preg_replace("/[^0-9]/","",$view_reports);
	$modify_leads = preg_replace("/[^0-9]/","",$modify_leads);
	$monitor_prefix = preg_replace("/[^0-9]/","",$monitor_prefix);
	$number_of_lines = preg_replace("/[^0-9]/","",$number_of_lines);
	$old_conf_exten = preg_replace("/[^0-9]/","",$old_conf_exten);
	$campaign_cid = preg_replace("/[^0-9]/","",$campaign_cid);
	$outbound_cid = preg_replace("/[^0-9]/","",$outbound_cid);
	$cid_number = preg_replace("/[^0-9]/","",$cid_number);
	$park_ext = preg_replace("/[^0-9]/","",$park_ext);
	$park_on_extension = preg_replace("/[^0-9]/","",$park_on_extension);
	$phone_number = preg_replace("/[^0-9]/","",$phone_number);
	$preview_force_dial_time = preg_replace("/[^0-9]/","",$preview_force_dial_time);
	$QUEUE_ACTION_enabled = preg_replace("/[^0-9]/","",$QUEUE_ACTION_enabled);
	$recording_exten = preg_replace("/[^0-9]/","",$recording_exten);
	$remote_agent_id = preg_replace("/[^0-9]/","",$remote_agent_id);
	$safe_harbor_exten = preg_replace("/[^0-9]/","",$safe_harbor_exten);
	$telnet_port = preg_replace("/[^0-9]/","",$telnet_port);
	$updater_check_enabled = preg_replace("/[^0-9]/","",$updater_check_enabled);
	$user_level = preg_replace("/[^0-9]/","",$user_level);
	$user_switching_enabled = preg_replace("/[^0-9]/","",$user_switching_enabled);
	$VDstop_rec_after_each_call = preg_replace("/[^0-9]/","",$VDstop_rec_after_each_call);
	$OSDIAL_park_on_extension = preg_replace("/[^0-9]/","",$OSDIAL_park_on_extension);
	$osdial_recording = preg_replace("/[^0-9]/","",$osdial_recording);
	$osdial_transfers = preg_replace("/[^0-9]/","",$osdial_transfers);
	$old_phone = preg_replace("/[^0-9]/","",$old_phone);
	$voicemail_button_enabled = preg_replace("/[^0-9]/","",$voicemail_button_enabled);
	$voicemail_dump_exten = preg_replace("/[^0-9]/","",$voicemail_dump_exten);
	$voicemail_ext = preg_replace("/[^0-9]/","",$voicemail_ext);
	$voicemail_exten = preg_replace("/[^0-9]/","",$voicemail_exten);
	$voicemail_id = preg_replace("/[^0-9]/","",$voicemail_id);
	$voicemail_password = preg_replace("/[^0-9]/","",$voicemail_password);
	$wrapup_seconds = preg_replace("/[^0-9]/","",$wrapup_seconds);
	$use_non_latin = preg_replace("/[^0-9]/","",$use_non_latin);
	$webroot_writable = preg_replace("/[^0-9]/","",$webroot_writable);
	$enable_queuemetrics_logging = preg_replace("/[^0-9]/","",$enable_queuemetrics_logging);
	$enable_sipsak_messages = preg_replace("/[^0-9]/","",$enable_sipsak_messages);
	$allow_sipsak_messages = preg_replace("/[^0-9]/","",$allow_sipsak_messages);
	$mix_container_item = preg_replace("/[^0-9]/","",$mix_container_item);
	$prompt_interval = preg_replace("/[^0-9]/","",$prompt_interval);
	$agent_alert_delay = preg_replace("/[^0-9]/","",$agent_alert_delay);
	$manual_dial_list_id = preg_replace("/[^0-9]/","",$manual_dial_list_id);
	$xfer_exten = preg_replace("/[^0-9]/","",$xfer_exten);
	$cpuinfo = preg_replace("/[^0-9]/","",$cpuinfo);
	$xfer_agent2agent = preg_replace("/[^0-9]/","",$xfer_agent2agent);
	$xfer_agent2agent_wait_seconds = preg_replace("/[^0-9]/","",$xfer_agent2agent_wait_seconds);

    $export_agent_pause_summary = preg_replace("/[^0-9]/","",$export_agent_pause_summary);
    $export_agent_performance_detail = preg_replace("/[^0-9]/","",$export_agent_performance_detail);
    $export_agent_timesheet = preg_replace("/[^0-9]/","",$export_agent_timesheet);
    $export_campaign_call_report = preg_replace("/[^0-9]/","",$export_campaign_call_report);
    $export_campaign_recent_outbound_sales = preg_replace("/[^0-9]/","",$export_campaign_recent_outbound_sales);
    $export_dnc = preg_replace("/[^0-9]/","",$export_dnc);
    $export_ingroup_call_report = preg_replace("/[^0-9]/","",$export_ingroup_call_report);
    $export_lead_performance_campaign = preg_replace("/[^0-9]/","",$export_lead_performance_campaign);
    $export_lead_performance_list = preg_replace("/[^0-9]/","",$export_lead_performance_list);
    $export_lead_search_advanced = preg_replace("/[^0-9]/","",$export_lead_search_advanced);
    $export_list_cost_entry = preg_replace("/[^0-9]/","",$export_list_cost_entry);
    $view_agent_pause_summary = preg_replace("/[^0-9]/","",$view_agent_pause_summary);
    $view_agent_performance_detail = preg_replace("/[^0-9]/","",$view_agent_performance_detail);
    $view_agent_realtime = preg_replace("/[^0-9]/","",$view_agent_realtime);
    $view_agent_realtime_iax_barge = preg_replace("/[^0-9]/","",$view_agent_realtime_iax_barge);
    $view_agent_realtime_iax_listen = preg_replace("/[^0-9]/","",$view_agent_realtime_iax_listen);
    $view_agent_realtime_sip_barge = preg_replace("/[^0-9]/","",$view_agent_realtime_sip_barge);
    $view_agent_realtime_sip_listen = preg_replace("/[^0-9]/","",$view_agent_realtime_sip_listen);
    $view_agent_realtime_summary = preg_replace("/[^0-9]/","",$view_agent_realtime_summary);
    $view_agent_stats = preg_replace("/[^0-9]/","",$view_agent_stats);
    $view_agent_status = preg_replace("/[^0-9]/","",$view_agent_status);
    $view_agent_timesheet = preg_replace("/[^0-9]/","",$view_agent_timesheet);
    $view_campaign_call_report = preg_replace("/[^0-9]/","",$view_campaign_call_report);
    $view_campaign_recent_outbound_sales = preg_replace("/[^0-9]/","",$view_campaign_recent_outbound_sales);
    $view_ingroup_call_report = preg_replace("/[^0-9]/","",$view_ingroup_call_report);
    $view_lead_performance_campaign = preg_replace("/[^0-9]/","",$view_lead_performance_campaign);
    $view_lead_performance_list = preg_replace("/[^0-9]/","",$view_lead_performance_list);
    $view_lead_search = preg_replace("/[^0-9]/","",$view_lead_search);
    $view_lead_search_advanced = preg_replace("/[^0-9]/","",$view_lead_search_advanced);
    $view_list_cost_entry = preg_replace("/[^0-9]/","",$view_list_cost_entry);
    $view_server_performance = preg_replace("/[^0-9]/","",$view_server_performance);
    $view_server_times = preg_replace("/[^0-9]/","",$view_server_times);
    $view_usergroup_hourly_stats = preg_replace("/[^0-9]/","",$view_usergroup_hourly_stats);
	
	### DIGITS and DASHES
	$group_rank = preg_replace("/[^-0-9]/","",$group_rank);
	$campaign_rank = preg_replace("/[^-0-9]/","",$campaign_rank);

    ### DIGITS, X's, *'s.
	$dnc_search_phone = preg_replace("/[^0-9Xx\*]/","",$dnc_search_phone);
	$dnc_add_phone = preg_replace("/[^0-9Xx\*]/","",$dnc_add_phone);
	$dnc_delete_phone = preg_replace("/[^0-9Xx\*]/","",$dnc_delete_phone);
	
	### Y or N ONLY ###
	$active = preg_replace("/[^NY]/","",$active);
	$allow_closers = preg_replace("/[^NY]/","",$allow_closers);
	$reset_hopper = preg_replace("/[^NY]/","",$reset_hopper);
	$alt_number_dialing = preg_replace("/[^NY]/","",$alt_number_dialing);
	$safe_harbor_message = preg_replace("/[^NY]/","",$safe_harbor_message);
	$selectable = preg_replace("/[^NY]/","",$selectable);
	$reset_list = preg_replace("/[^NY]/","",$reset_list);
	$fronter_display = preg_replace("/[^NY]/","",$fronter_display);
	$drop_message = preg_replace("/[^NY]/","",$drop_message);
	$use_internal_dnc = preg_replace("/[^NY]/","",$use_internal_dnc);
	$omit_phone_code = preg_replace("/[^NY]/","",$omit_phone_code);
	$available_only_ratio_tally = preg_replace("/[^NY]/","",$available_only_ratio_tally);
	$sys_perf_log = preg_replace("/[^NY]/","",$sys_perf_log);
	$osdial_balance_active = preg_replace("/[^NY]/","",$osdial_balance_active);
	$manual_preview_default = preg_replace("/[^NY]/","",$manual_preview_default);
	$vd_server_logs = preg_replace("/[^NY]/","",$vd_server_logs);
	$agent_pause_codes_active = preg_replace("/[^NY]/","",$agent_pause_codes_active);
	$campaign_stats_refresh = preg_replace("/[^NY]/","",$campaign_stats_refresh);
	$disable_alter_custdata = preg_replace("/[^NY]/","",$disable_alter_custdata);
	$disable_manual_dial = preg_replace("/[^NY]/","",$disable_manual_dial);
	$no_hopper_leads_logins = preg_replace("/[^NY]/","",$no_hopper_leads_logins);
	$human_answered = preg_replace("/[^NY]/","",$human_answered);
	$hide_xfer_local_closer = preg_replace("/[^NY]/","",$hide_xfer_local_closer);
	$hide_xfer_dial_override = preg_replace("/[^NY]/","",$hide_xfer_dial_override);
	$hide_xfer_hangup_xfer = preg_replace("/[^NY]/","",$hide_xfer_hangup_xfer);
	$hide_xfer_leave_3way = preg_replace("/[^NY]/","",$hide_xfer_leave_3way);
	$hide_xfer_dial_with = preg_replace("/[^NY]/","",$hide_xfer_dial_with);
	$hide_xfer_hangup_both = preg_replace("/[^NY]/","",$hide_xfer_hangup_both);
	$hide_xfer_blind_xfer = preg_replace("/[^NY]/","",$hide_xfer_blind_xfer);
	$hide_xfer_park_dial = preg_replace("/[^NY]/","",$hide_xfer_park_dial);
	$hide_xfer_blind_vmail = preg_replace("/[^NY]/","",$hide_xfer_blind_vmail);
	$tovdad_display = preg_replace("/[^NY]/","",$tovdad_display);
	$campaign_allow_inbound = preg_replace("/[^NY]/","",$campaign_allow_inbound);
	$allow_tab_switch = preg_replace("/[^NY]/","",$allow_tab_switch);
	$web_form_extwindow = preg_replace("/[^NY]/","",$web_form_extwindow);
	$web_form2_extwindow = preg_replace("/[^NY]/","",$web_form2_extwindow);
	$use_custom2_callerid = preg_replace("/[^NY]/","",$use_custom2_callerid);
	$use_cid_areacode_map = preg_replace("/[^NY]/","",$use_cid_areacode_map);
	
	### ALPHA-NUMERIC ONLY ###
	$user_start = preg_replace("/[^0-9a-zA-Z]/","",$user_start);
	$script_id = preg_replace("/[^0-9a-zA-Z]/","",$script_id);
	$script_override = preg_replace("/[^0-9a-zA-Z]/","",$script_override);
	$script_button_id = preg_replace("/[^0-9a-zA-Z]/","",$script_button_id);
	$submit = preg_replace("/[^0-9a-zA-Z]/","",$submit);
	$get_call_launch = preg_replace("/[^0-9a-zA-Z]/","",$get_call_launch);
	$campaign_recording = preg_replace("/[^0-9a-zA-Z]/","",$campaign_recording);
	$ADD = preg_replace("/[^0-9a-zA-Z]/","",$ADD);
	$dial_prefix = preg_replace("/[^0-9a-zA-Z]/","",$dial_prefix);
	$state_call_time_state = preg_replace("/[^0-9a-zA-Z]/","",$state_call_time_state);
	$scheduled_callbacks = preg_replace("/[^0-9a-zA-Z]/","",$scheduled_callbacks);
	$concurrent_transfers = preg_replace("/[^0-9a-zA-Z]/","",$concurrent_transfers);
	$billable = preg_replace("/[^0-9a-zA-Z]/","",$billable);
	$pause_code = preg_replace("/[^0-9a-zA-Z]/","",$pause_code);
	$osdial_recording_override = preg_replace("/[^0-9a-zA-Z]/","",$osdial_recording_override);
	$queuemetrics_log_id = preg_replace("/[^0-9a-zA-Z]/","",$queuemetrics_log_id);
	$after_hours_action = preg_replace("/[^0-9a-zA-Z]/","",$after_hours_action);
	$after_hours_exten = preg_replace("/[^0-9a-zA-Z]/","",$after_hours_exten);
	$after_hours_voicemail = preg_replace("/[^0-9a-zA-Z]/","",$after_hours_voicemail);
	$intra_server_protocol = preg_replace("/[^0-9a-zA-Z]/","",$intra_server_protocol);
	
	### DIGITS and Dots
	$server_ip = preg_replace("/[^\.0-9]/","",$server_ip);
	$auto_dial_level = preg_replace("/[^\.0-9]/","",$auto_dial_level);
	$adaptive_maximum_level = preg_replace("/[^\.0-9]/","",$adaptive_maximum_level);
	$phone_ip = preg_replace("/[^\.0-9]/","",$phone_ip);
	$old_server_ip = preg_replace("/[^\.0-9]/","",$old_server_ip);
	$computer_ip = preg_replace("/[^\.0-9]/","",$computer_ip);
	$cost = preg_replace("/[^\.0-9]/","",$cost);
	$queuemetrics_server_ip = preg_replace("/[^\.0-9]/","",$queuemetrics_server_ip);
	
	### ALPHA-NUMERIC and spaces and hash and star and comma
	$xferconf_a_dtmf = preg_replace("/[^ \,\*\#0-9a-zA-Z]/","",$xferconf_a_dtmf);
	$xferconf_b_dtmf = preg_replace("/[^ \,\*\#0-9a-zA-Z]/","",$xferconf_b_dtmf);
	
	### ALPHACAPS-NUMERIC
	$xferconf_a_number = preg_replace("/[^0-9A-Z]/","",$xferconf_a_number);
	$xferconf_b_number = preg_replace("/[^0-9A-Z]/","",$xferconf_b_number);
	
	### ALPHA-NUMERIC and underscore and dash
	$agi_output = preg_replace("/[^-\_0-9a-zA-Z]/","",$agi_output);
	$ASTmgrSECRET = preg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrSECRET);
	$ASTmgrUSERNAME = preg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrUSERNAME);
	$ASTmgrUSERNAMElisten = preg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrUSERNAMElisten);
	$ASTmgrUSERNAMEsend = preg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrUSERNAMEsend);
	$ASTmgrUSERNAMEupdate = preg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrUSERNAMEupdate);
	$call_time_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$call_time_id);
	$et_id = preg_replace("/[^-\_0-9a-zA-Z]/","",strtoupper($et_id));
	$campaign_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$campaign_id);
	$CoNfIrM = preg_replace("/[^-\_0-9a-zA-Z]/","",$CoNfIrM);
	$DBX_database = preg_replace("/[^-\_0-9a-zA-Z]/","",$DBX_database);
	$DBX_pass = preg_replace("/[^-\_0-9a-zA-Z]/","",$DBX_pass);
	$DBX_user = preg_replace("/[^-\_0-9a-zA-Z]/","",$DBX_user);
	$DBY_database = preg_replace("/[^-\_0-9a-zA-Z]/","",$DBY_database);
	$DBY_pass = preg_replace("/[^-\_0-9a-zA-Z]/","",$DBY_pass);
	$DBY_user = preg_replace("/[^-\_0-9a-zA-Z]/","",$DBY_user);
	$dial_method = preg_replace("/[^-\_0-9a-zA-Z]/","",$dial_method);
	$dial_status_a = preg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_a);
	$dial_status_b = preg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_b);
	$dial_status_c = preg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_c);
	$dial_status_d = preg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_d);
	$dial_status_e = preg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_e);
	$drop_trigger = preg_replace("/[^-\_0-9a-zA-Z]/","",$drop_trigger);
	$ext_context = preg_replace("/[^-\_0-9a-zA-Z]/","",$ext_context);
	$group_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$group_id);
	$lead_filter_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$lead_filter_id);
	$local_call_time = preg_replace("/[^-\_0-9a-zA-Z]/","",$local_call_time);
	$login = preg_replace("/[^-\_0-9a-zA-Z]/","",$login);
	$login_campaign = preg_replace("/[^-\_0-9a-zA-Z]/","",$login_campaign);
	$login_pass = preg_replace("/[^-\_0-9a-zA-Z]/","",$login_pass);
	$login_user = preg_replace("/[^-\_0-9a-zA-Z]/","",$login_user);
	$next_agent_call = preg_replace("/[^-\_0-9a-zA-Z]/","",$next_agent_call);
	$old_campaign_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$old_campaign_id);
	$old_server_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$old_server_id);
	$OLDuser_group = preg_replace("/[^-\_0-9a-zA-Z]/","",$OLDuser_group);
	$park_file_name = preg_replace("/[^-\_0-9a-zA-Z]/","",$park_file_name);
	$pass = preg_replace("/[^-\_0-9a-zA-Z]/","",$pass);
	$phone_login = preg_replace("/[^-\_0-9a-zA-Z]/","",$phone_login);
	$phone_pass = preg_replace("/[^-\_0-9a-zA-Z]/","",$phone_pass);
	$PHP_AUTH_PW = preg_replace("/[^-\_0-9a-zA-Z]/","",$PHP_AUTH_PW);
	$PHP_AUTH_USER = preg_replace("/[^-\_0-9a-zA-Z]/","",$PHP_AUTH_USER);
	$protocol = preg_replace("/[^-\_0-9a-zA-Z]/","",$protocol);
	$server_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$server_id);
	$stage = preg_replace("/[^-\_0-9a-zA-Z]/","",$stage);
	$state_rule = preg_replace("/[^-\_0-9a-zA-Z]/","",$state_rule);
	$status = preg_replace("/[^-\_0-9a-zA-Z]/","",$status);
	$trunk_restriction = preg_replace("/[^-\_0-9a-zA-Z]/","",$trunk_restriction);
	$user = preg_replace("/[^-\_0-9a-zA-Z]/","",$user);
	$user_group = preg_replace("/[^-\_0-9a-zA-Z]/","",$user_group);
	$OSDIAL_park_on_filename = preg_replace("/[^-\_0-9a-zA-Z]/","",$OSDIAL_park_on_filename);
	$auto_alt_dial = preg_replace("/[^-\_0-9a-zA-Z]/","",$auto_alt_dial);
	$dial_status = preg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status);
	$queuemetrics_eq_prepend = preg_replace("/[^-\_0-9a-zA-Z]/","",$queuemetrics_eq_prepend);
	$osdial_agent_disable = preg_replace("/[^-\_0-9a-zA-Z]/","",$osdial_agent_disable);
	$alter_custdata_override = preg_replace("/[^-\_0-9a-zA-Z]/","",$alter_custdata_override);
	$list_order_mix = preg_replace("/[^-\_0-9a-zA-Z]/","",$list_order_mix);
	$vcl_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$vcl_id);
	$mix_method = preg_replace("/[^-\_0-9a-zA-Z]/","",$mix_method);
	$category = preg_replace("/[^-\_0-9a-zA-Z]/","",$category);
	$vsc_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$vsc_id);
	$moh_context = preg_replace("/[^-\_0-9a-zA-Z]/","",$moh_context);
	$agent_alert_exten = preg_replace("/[^-\_0-9a-zA-Z]/","",$agent_alert_exten);
	$source_campaign_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$source_campaign_id);
	$source_user_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$source_user_id);
	$source_group_id = preg_replace("/[^-\_0-9a-zA-Z]/","",$source_group_id);
	$default_xfer_group = preg_replace("/[^-\_0-9a-zA-Z]/","",$default_xfer_group);
	$xfer_cid_mode = preg_replace("/[^-\_0-9a-zA-Z]/","",$xfer_cid_mode);
	
	### ALPHA-NUMERIC and spaces
	$lead_order = preg_replace("/[^ 0-9a-zA-Z]/","",$lead_order);
	$script_button_label = preg_replace("/[^ 0-9a-zA-Z]/","",$script_button_label);
	### ALPHA-NUMERIC and hash
	$group_color = preg_replace("/[^\#0-9a-zA-Z]/","",$group_color);
	
	### ALPHA-NUMERIC and spaces dots, commas, dashes, underscores
	$adaptive_dl_diff_target = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$adaptive_dl_diff_target);
	$adaptive_intensity = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$adaptive_intensity);
	$asterisk_version = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$asterisk_version);
	$call_time_comments = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$call_time_comments);
	$call_time_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$call_time_name);
	$campaign_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$campaign_name);
	$campaign_rec_filename = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$campaign_rec_filename);
	$company = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$company);
	$full_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$full_name);
	$fullname = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$fullname);
	$group_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$group_name);
	$HKstatus = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$HKstatus);
	$lead_filter_comments = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$lead_filter_comments);
	$lead_filter_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$lead_filter_name);
	$list_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$list_name);
	$local_gmt = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$local_gmt);
	$phone_type = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$phone_type);
	$picture = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$picture);
	$script_comments = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$script_comments);
	$script_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$script_name);
	$server_description = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$server_description);
	$status = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$status);
	$status_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$status_name);
	$wrapup_message = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$wrapup_message);
	$pause_code_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$pause_code_name);
	$campaign_description = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$campaign_description);
	$list_description = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$list_description);
	$vcl_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$vcl_name);
	$vsc_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$vsc_name);
	$vsc_description = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$vsc_description);
	$campaign_cid_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$campaign_cid_name);
	$outbound_cid_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$outbound_cid_name);
	$cid_name = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$cid_name);
	
	### ALPHA-NUMERIC and underscore and dash and slash and at and dot
	$call_out_number_group = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$call_out_number_group);
	$client_browser = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$client_browser);
	$DBX_server = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$DBX_server);
	$DBY_server = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$DBY_server);
	$dtmf_send_extension = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$dtmf_send_extension);
	$extension = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$extension);
	$install_directory = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$install_directory);
	$old_extension = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$old_extension);
	$telnet_host = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$telnet_host);
	$queuemetrics_dbname = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$queuemetrics_dbname);
	$queuemetrics_login = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$queuemetrics_login);
	$queuemetrics_pass = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$queuemetrics_pass);
	$after_hours_message_filename = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$after_hours_message_filename);
	$welcome_message_filename = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$welcome_message_filename);
	$onhold_prompt_filename = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$onhold_prompt_filename);
	$voicemail_email = preg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$voicemail_email);

    # Others
	$company_name = preg_replace("/[^ \.\!\,\&\@0-9a-zA-Z]/","",$company_name);
	
	### remove semi-colons ###
	$lead_filter_sql = preg_replace("/;/","",$lead_filter_sql);
	$list_mix_container = preg_replace("/;/","",$list_mix_container);
	
	### VARIABLES TO BE mysql_real_escape_string ###
	# $web_form_address
	# $queuemetrics_url
	# $admin_home_url
	
	### VARIABLES not filtered at all ###
	# $script_text
}	# end of non_latin


##### END VARIABLE FILTERING FOR SECURITY #####
?>
