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

#if ($config['settings']['use_non_latin'] < 1)	{
	### DIGITS ONLY ###
	$default_phone_code = OSDpreg_replace("/[^0-9]/","",$default_phone_code);
	$last_general_extension = OSDpreg_replace("/[^0-9]/","",$last_general_extension);
	$areacode = OSDpreg_replace("/[^0-9]/","",$areacode);
	$alt_phone = OSDpreg_replace("/[^0-9]/","",$alt_phone);
	$adaptive_dropped_percentage = OSDpreg_replace("/[^0-9]/","",$adaptive_dropped_percentage);
	$adaptive_latest_server_time = OSDpreg_replace("/[^0-9]/","",$adaptive_latest_server_time);
	$admin_api_access = OSDpreg_replace("/[^0-9]/","",$admin_api_access);
	$admin_hangup_enabled = OSDpreg_replace("/[^0-9]/","",$admin_hangup_enabled);
	$admin_hijack_enabled = OSDpreg_replace("/[^0-9]/","",$admin_hijack_enabled);
	$admin_monitor_enabled = OSDpreg_replace("/[^0-9]/","",$admin_monitor_enabled);
	$AFLogging_enabled = OSDpreg_replace("/[^0-9]/","",$AFLogging_enabled);
	$agent_api_access = OSDpreg_replace("/[^0-9]/","",$agent_api_access);
	$agent_choose_ingroups = OSDpreg_replace("/[^0-9]/","",$agent_choose_ingroups);
	$agentcall_manual = OSDpreg_replace("/[^0-9]/","",$agentcall_manual);
	$agentonly_callbacks = OSDpreg_replace("/[^0-9]/","",$agentonly_callbacks);
	$AGI_call_logging_enabled = OSDpreg_replace("/[^0-9]/","",$AGI_call_logging_enabled);
	$allcalls_delay = OSDpreg_replace("/[^0-9]/","",$allcalls_delay);
	$alter_agent_interface_options = OSDpreg_replace("/[^0-9]/","",$alter_agent_interface_options);
	$am_message_exten = OSDpreg_replace("/[^0-9]/","",$am_message_exten);
	$answer_transfer_agent = OSDpreg_replace("/[^0-9]/","",$answer_transfer_agent);
	$ast_admin_access = OSDpreg_replace("/[^0-9]/","",$ast_admin_access);
	$ast_delete_phones = OSDpreg_replace("/[^0-9]/","",$ast_delete_phones);
	$attempt_delay = OSDpreg_replace("/[^0-9]/","",$attempt_delay);
	$attempt_maximum = OSDpreg_replace("/[^0-9]/","",$attempt_maximum);
	$auto_dial_next_number = OSDpreg_replace("/[^0-9]/","",$auto_dial_next_number);
	$balance_trunks_offlimits = OSDpreg_replace("/[^0-9]/","",$balance_trunks_offlimits);
	$call_parking_enabled = OSDpreg_replace("/[^0-9]/","",$call_parking_enabled);
	$answers_per_hour_limit = OSDpreg_replace("/[^0-9]/","",$answers_per_hour_limit);
	$CallerID_popup_enabled = OSDpreg_replace("/[^0-9]/","",$CallerID_popup_enabled);
	$campaign_detail = OSDpreg_replace("/[^0-9]/","",$campaign_detail);
	$campaign_rec_exten = OSDpreg_replace("/[^0-9]/","",$campaign_rec_exten);
	$campaign_vdad_exten = OSDpreg_replace("/[^0-9]/","",$campaign_vdad_exten);
	$change_agent_campaign = OSDpreg_replace("/[^0-9]/","",$change_agent_campaign);
	$closer_default_blended = OSDpreg_replace("/[^0-9]/","",$closer_default_blended);
	$conf_exten = OSDpreg_replace("/[^0-9]/","",$conf_exten);
	$conf_on_extension = OSDpreg_replace("/[^0-9]/","",$conf_on_extension);
	$conferencing_enabled = OSDpreg_replace("/[^0-9]/","",$conferencing_enabled);
	$company_id = OSDpreg_replace("/[^0-9]/","",$company_id);
	$ct_default_start = OSDpreg_replace("/[^0-9]/","",$ct_default_start);
	$ct_default_stop = OSDpreg_replace("/[^0-9]/","",$ct_default_stop);
	$ct_friday_start = OSDpreg_replace("/[^0-9]/","",$ct_friday_start);
	$ct_friday_stop = OSDpreg_replace("/[^0-9]/","",$ct_friday_stop);
	$ct_monday_start = OSDpreg_replace("/[^0-9]/","",$ct_monday_start);
	$ct_monday_stop = OSDpreg_replace("/[^0-9]/","",$ct_monday_stop);
	$ct_saturday_start = OSDpreg_replace("/[^0-9]/","",$ct_saturday_start);
	$ct_saturday_stop = OSDpreg_replace("/[^0-9]/","",$ct_saturday_stop);
	$ct_sunday_start = OSDpreg_replace("/[^0-9]/","",$ct_sunday_start);
	$ct_sunday_stop = OSDpreg_replace("/[^0-9]/","",$ct_sunday_stop);
	$ct_thursday_start = OSDpreg_replace("/[^0-9]/","",$ct_thursday_start);
	$ct_thursday_stop = OSDpreg_replace("/[^0-9]/","",$ct_thursday_stop);
	$ct_tuesday_start = OSDpreg_replace("/[^0-9]/","",$ct_tuesday_start);
	$ct_tuesday_stop = OSDpreg_replace("/[^0-9]/","",$ct_tuesday_stop);
	$ct_wednesday_start = OSDpreg_replace("/[^0-9]/","",$ct_wednesday_start);
	$ct_wednesday_stop = OSDpreg_replace("/[^0-9]/","",$ct_wednesday_stop);
	$DBX_port = OSDpreg_replace("/[^0-9]/","",$DBX_port);
	$DBY_port = OSDpreg_replace("/[^0-9]/","",$DBY_port);
	$dedicated_trunks = OSDpreg_replace("/[^0-9]/","",$dedicated_trunks);
	$delete_call_times = OSDpreg_replace("/[^0-9]/","",$delete_call_times);
	$delete_campaigns = OSDpreg_replace("/[^0-9]/","",$delete_campaigns);
	$delete_dnc = OSDpreg_replace("/[^0-9]/","",$delete_dnc);
	$delete_filters = OSDpreg_replace("/[^0-9]/","",$delete_filters);
	$delete_ingroups = OSDpreg_replace("/[^0-9]/","",$delete_ingroups);
	$delete_lists = OSDpreg_replace("/[^0-9]/","",$delete_lists);
	$delete_remote_agents = OSDpreg_replace("/[^0-9]/","",$delete_remote_agents);
	$delete_scripts = OSDpreg_replace("/[^0-9]/","",$delete_scripts);
	$delete_user_groups = OSDpreg_replace("/[^0-9]/","",$delete_user_groups);
	$delete_users = OSDpreg_replace("/[^0-9]/","",$delete_users);
	$dial_timeout = OSDpreg_replace("/[^0-9]/","",$dial_timeout);
	$dialplan_number = OSDpreg_replace("/[^0-9]/","",$dialplan_number);
	$drop_call_seconds = OSDpreg_replace("/[^0-9]/","",$drop_call_seconds);
	$drop_exten = OSDpreg_replace("/[^0-9]/","",$drop_exten);
	$enable_fast_refresh = OSDpreg_replace("/[^0-9]/","",$enable_fast_refresh);
	$enable_persistant_mysql = OSDpreg_replace("/[^0-9]/","",$enable_persistant_mysql);
	$export_leads = OSDpreg_replace("/[^0-9]/","",$export_leads);
	$fast_refresh_rate = OSDpreg_replace("/[^0-9]/","",$fast_refresh_rate);
	$hopper_level = OSDpreg_replace("/[^0-9]/","",$hopper_level);
	$hotkey = OSDpreg_replace("/[^0-9]/","",$hotkey);
	$hotkeys_active = OSDpreg_replace("/[^0-9]/","",$hotkeys_active);
	$list_id = OSDpreg_replace("/[^0-9]/","",$list_id);
	$list_id_override = OSDpreg_replace("/[^0-9]/","",$list_id_override);
	$load_dnc = OSDpreg_replace("/[^0-9]/","",$load_dnc);
	$load_leads = OSDpreg_replace("/[^0-9]/","",$load_leads);
	$max_osdial_trunks = OSDpreg_replace("/[^0-9]/","",$max_osdial_trunks);
	$modify_call_times = OSDpreg_replace("/[^0-9]/","",$modify_call_times);
	$modify_users = OSDpreg_replace("/[^0-9]/","",$modify_users);
	$modify_campaigns = OSDpreg_replace("/[^0-9]/","",$modify_campaigns);
	$modify_lists = OSDpreg_replace("/[^0-9]/","",$modify_lists);
	$modify_scripts = OSDpreg_replace("/[^0-9]/","",$modify_scripts);
	$modify_filters = OSDpreg_replace("/[^0-9]/","",$modify_filters);
	$modify_ingroups = OSDpreg_replace("/[^0-9]/","",$modify_ingroups);
	$modify_usergroups = OSDpreg_replace("/[^0-9]/","",$modify_usergroups);
	$modify_remoteagents = OSDpreg_replace("/[^0-9]/","",$modify_remoteagents);
	$modify_servers = OSDpreg_replace("/[^0-9]/","",$modify_servers);
	$view_reports = OSDpreg_replace("/[^0-9]/","",$view_reports);
	$modify_leads = OSDpreg_replace("/[^0-9]/","",$modify_leads);
	$monitor_prefix = OSDpreg_replace("/[^0-9]/","",$monitor_prefix);
	$number_of_lines = OSDpreg_replace("/[^0-9]/","",$number_of_lines);
	$old_conf_exten = OSDpreg_replace("/[^0-9]/","",$old_conf_exten);
	$campaign_cid = OSDpreg_replace("/[^0-9]/","",$campaign_cid);
	$outbound_cid = OSDpreg_replace("/[^0-9]/","",$outbound_cid);
	$cid_number = OSDpreg_replace("/[^0-9]/","",$cid_number);
	$park_ext = OSDpreg_replace("/[^0-9]/","",$park_ext);
	$park_on_extension = OSDpreg_replace("/[^0-9]/","",$park_on_extension);
	$phone_number = OSDpreg_replace("/[^0-9]/","",$phone_number);
	$preview_force_dial_time = OSDpreg_replace("/[^0-9]/","",$preview_force_dial_time);
	$QUEUE_ACTION_enabled = OSDpreg_replace("/[^0-9]/","",$QUEUE_ACTION_enabled);
	$recording_exten = OSDpreg_replace("/[^0-9]/","",$recording_exten);
	$remote_agent_id = OSDpreg_replace("/[^0-9]/","",$remote_agent_id);
	$safe_harbor_exten = OSDpreg_replace("/[^0-9]/","",$safe_harbor_exten);
	$telnet_port = OSDpreg_replace("/[^0-9]/","",$telnet_port);
	$updater_check_enabled = OSDpreg_replace("/[^0-9]/","",$updater_check_enabled);
	$user_level = OSDpreg_replace("/[^0-9]/","",$user_level);
	$user_switching_enabled = OSDpreg_replace("/[^0-9]/","",$user_switching_enabled);
	$VDstop_rec_after_each_call = OSDpreg_replace("/[^0-9]/","",$VDstop_rec_after_each_call);
	$last_recording_extension = OSDpreg_replace("/[^0-9]/","",$last_recording_extension);
	$OSDIAL_park_on_extension = OSDpreg_replace("/[^0-9]/","",$OSDIAL_park_on_extension);
	$osdial_recording = OSDpreg_replace("/[^0-9]/","",$osdial_recording);
	$osdial_transfers = OSDpreg_replace("/[^0-9]/","",$osdial_transfers);
	$old_phone = OSDpreg_replace("/[^0-9]/","",$old_phone);
	$voicemail_button_enabled = OSDpreg_replace("/[^0-9]/","",$voicemail_button_enabled);
	$voicemail_dump_exten = OSDpreg_replace("/[^0-9]/","",$voicemail_dump_exten);
	$voicemail_ext = OSDpreg_replace("/[^0-9]/","",$voicemail_ext);
	$voicemail_exten = OSDpreg_replace("/[^0-9]/","",$voicemail_exten);
	$voicemail_id = OSDpreg_replace("/[^0-9]/","",$voicemail_id);
	$voicemail_password = OSDpreg_replace("/[^0-9]/","",$voicemail_password);
	$wrapup_seconds = OSDpreg_replace("/[^0-9]/","",$wrapup_seconds);
	$use_non_latin = OSDpreg_replace("/[^0-9]/","",$use_non_latin);
	$webroot_writable = OSDpreg_replace("/[^0-9]/","",$webroot_writable);
	$enable_queuemetrics_logging = OSDpreg_replace("/[^0-9]/","",$enable_queuemetrics_logging);
	$enable_sipsak_messages = OSDpreg_replace("/[^0-9]/","",$enable_sipsak_messages);
	$allow_sipsak_messages = OSDpreg_replace("/[^0-9]/","",$allow_sipsak_messages);
	$mix_container_item = OSDpreg_replace("/[^0-9]/","",$mix_container_item);
	$prompt_interval = OSDpreg_replace("/[^0-9]/","",$prompt_interval);
	$agent_alert_delay = OSDpreg_replace("/[^0-9]/","",$agent_alert_delay);
	$manual_dial_list_id = OSDpreg_replace("/[^0-9]/","",$manual_dial_list_id);
	$xfer_exten = OSDpreg_replace("/[^0-9]/","",$xfer_exten);
	$cpuinfo = OSDpreg_replace("/[^0-9]/","",$cpuinfo);
	$xfer_agent2agent = OSDpreg_replace("/[^0-9]/","",$xfer_agent2agent);
	$xfer_agent2agent_wait_seconds = OSDpreg_replace("/[^0-9]/","",$xfer_agent2agent_wait_seconds);

    $export_agent_pause_summary = OSDpreg_replace("/[^0-9]/","",$export_agent_pause_summary);
    $export_agent_performance_detail = OSDpreg_replace("/[^0-9]/","",$export_agent_performance_detail);
    $export_agent_timesheet = OSDpreg_replace("/[^0-9]/","",$export_agent_timesheet);
    $export_campaign_call_report = OSDpreg_replace("/[^0-9]/","",$export_campaign_call_report);
    $export_campaign_recent_outbound_sales = OSDpreg_replace("/[^0-9]/","",$export_campaign_recent_outbound_sales);
    $export_dnc = OSDpreg_replace("/[^0-9]/","",$export_dnc);
    $export_ingroup_call_report = OSDpreg_replace("/[^0-9]/","",$export_ingroup_call_report);
    $export_lead_performance_campaign = OSDpreg_replace("/[^0-9]/","",$export_lead_performance_campaign);
    $export_lead_performance_list = OSDpreg_replace("/[^0-9]/","",$export_lead_performance_list);
    $export_lead_search_advanced = OSDpreg_replace("/[^0-9]/","",$export_lead_search_advanced);
    $export_list_cost_entry = OSDpreg_replace("/[^0-9]/","",$export_list_cost_entry);
    $view_agent_pause_summary = OSDpreg_replace("/[^0-9]/","",$view_agent_pause_summary);
    $view_agent_performance_detail = OSDpreg_replace("/[^0-9]/","",$view_agent_performance_detail);
    $view_agent_realtime = OSDpreg_replace("/[^0-9]/","",$view_agent_realtime);
    $view_agent_realtime_iax_barge = OSDpreg_replace("/[^0-9]/","",$view_agent_realtime_iax_barge);
    $view_agent_realtime_iax_listen = OSDpreg_replace("/[^0-9]/","",$view_agent_realtime_iax_listen);
    $view_agent_realtime_sip_barge = OSDpreg_replace("/[^0-9]/","",$view_agent_realtime_sip_barge);
    $view_agent_realtime_sip_listen = OSDpreg_replace("/[^0-9]/","",$view_agent_realtime_sip_listen);
    $view_agent_realtime_summary = OSDpreg_replace("/[^0-9]/","",$view_agent_realtime_summary);
    $view_agent_stats = OSDpreg_replace("/[^0-9]/","",$view_agent_stats);
    $view_agent_status = OSDpreg_replace("/[^0-9]/","",$view_agent_status);
    $view_agent_timesheet = OSDpreg_replace("/[^0-9]/","",$view_agent_timesheet);
    $view_campaign_call_report = OSDpreg_replace("/[^0-9]/","",$view_campaign_call_report);
    $view_campaign_recent_outbound_sales = OSDpreg_replace("/[^0-9]/","",$view_campaign_recent_outbound_sales);
    $view_ingroup_call_report = OSDpreg_replace("/[^0-9]/","",$view_ingroup_call_report);
    $view_lead_performance_campaign = OSDpreg_replace("/[^0-9]/","",$view_lead_performance_campaign);
    $view_lead_performance_list = OSDpreg_replace("/[^0-9]/","",$view_lead_performance_list);
    $view_lead_search = OSDpreg_replace("/[^0-9]/","",$view_lead_search);
    $view_lead_search_advanced = OSDpreg_replace("/[^0-9]/","",$view_lead_search_advanced);
    $view_list_cost_entry = OSDpreg_replace("/[^0-9]/","",$view_list_cost_entry);
    $view_server_performance = OSDpreg_replace("/[^0-9]/","",$view_server_performance);
    $view_server_times = OSDpreg_replace("/[^0-9]/","",$view_server_times);
    $view_usergroup_hourly_stats = OSDpreg_replace("/[^0-9]/","",$view_usergroup_hourly_stats);
	
	### DIGITS and DASHES
	$group_rank = OSDpreg_replace("/[^-0-9]/","",$group_rank);
	$campaign_rank = OSDpreg_replace("/[^-0-9]/","",$campaign_rank);

    ### DIGITS, X's, *'s.
	$dnc_search_phone = OSDpreg_replace("/[^0-9Xx\*]/","",$dnc_search_phone);
	$dnc_add_phone = OSDpreg_replace("/[^0-9Xx\*]/","",$dnc_add_phone);
	$dnc_delete_phone = OSDpreg_replace("/[^0-9Xx\*]/","",$dnc_delete_phone);
	
	### Y or N ONLY ###
	$active = OSDpreg_replace("/[^NY]/","",$active);
	$allow_closers = OSDpreg_replace("/[^NY]/","",$allow_closers);
	$reset_hopper = OSDpreg_replace("/[^NY]/","",$reset_hopper);
	$alt_number_dialing = OSDpreg_replace("/[^NY]/","",$alt_number_dialing);
	$safe_harbor_message = OSDpreg_replace("/[^NY]/","",$safe_harbor_message);
	$selectable = OSDpreg_replace("/[^NY]/","",$selectable);
	$reset_list = OSDpreg_replace("/[^NY]/","",$reset_list);
	$fronter_display = OSDpreg_replace("/[^NY]/","",$fronter_display);
	$drop_message = OSDpreg_replace("/[^NY]/","",$drop_message);
	$use_internal_dnc = OSDpreg_replace("/[^NY]/","",$use_internal_dnc);
	$use_browser_timezone_offset = OSDpreg_replace("/[^NY]/","",$use_browser_timezone_offset);
	$omit_phone_code = OSDpreg_replace("/[^NY]/","",$omit_phone_code);
	$available_only_ratio_tally = OSDpreg_replace("/[^NY]/","",$available_only_ratio_tally);
	$sys_perf_log = OSDpreg_replace("/[^NY]/","",$sys_perf_log);
	$osdial_balance_active = OSDpreg_replace("/[^NY]/","",$osdial_balance_active);
	$manual_preview_default = OSDpreg_replace("/[^NY]/","",$manual_preview_default);
	$vd_server_logs = OSDpreg_replace("/[^NY]/","",$vd_server_logs);
	$agent_pause_codes_active = OSDpreg_replace("/[^NY]/","",$agent_pause_codes_active);
	$campaign_stats_refresh = OSDpreg_replace("/[^NY]/","",$campaign_stats_refresh);
	$disable_alter_custdata = OSDpreg_replace("/[^NY]/","",$disable_alter_custdata);
	$disable_manual_dial = OSDpreg_replace("/[^NY]/","",$disable_manual_dial);
	$no_hopper_leads_logins = OSDpreg_replace("/[^NY]/","",$no_hopper_leads_logins);
	$human_answered = OSDpreg_replace("/[^NY]/","",$human_answered);
	$hide_xfer_local_closer = OSDpreg_replace("/[^NY]/","",$hide_xfer_local_closer);
	$hide_xfer_dial_override = OSDpreg_replace("/[^NY]/","",$hide_xfer_dial_override);
	$hide_xfer_hangup_xfer = OSDpreg_replace("/[^NY]/","",$hide_xfer_hangup_xfer);
	$hide_xfer_leave_3way = OSDpreg_replace("/[^NY]/","",$hide_xfer_leave_3way);
	$hide_xfer_dial_with = OSDpreg_replace("/[^NY]/","",$hide_xfer_dial_with);
	$hide_xfer_hangup_both = OSDpreg_replace("/[^NY]/","",$hide_xfer_hangup_both);
	$hide_xfer_blind_xfer = OSDpreg_replace("/[^NY]/","",$hide_xfer_blind_xfer);
	$hide_xfer_park_dial = OSDpreg_replace("/[^NY]/","",$hide_xfer_park_dial);
	$hide_xfer_blind_vmail = OSDpreg_replace("/[^NY]/","",$hide_xfer_blind_vmail);
	$tovdad_display = OSDpreg_replace("/[^NY]/","",$tovdad_display);
	$campaign_allow_inbound = OSDpreg_replace("/[^NY]/","",$campaign_allow_inbound);
	$allow_tab_switch = OSDpreg_replace("/[^NY]/","",$allow_tab_switch);
	$web_form_extwindow = OSDpreg_replace("/[^NY]/","",$web_form_extwindow);
	$web_form2_extwindow = OSDpreg_replace("/[^NY]/","",$web_form2_extwindow);
	$use_custom2_callerid = OSDpreg_replace("/[^NY]/","",$use_custom2_callerid);
	$use_cid_areacode_map = OSDpreg_replace("/[^NY]/","",$use_cid_areacode_map);
	
	### ALPHA-NUMERIC ONLY ###
	#$user_start = OSDpreg_replace("/[^0-9a-zA-Z]/","",$user_start);
	#$script_id = OSDpreg_replace("/[^0-9a-zA-Z]/","",$script_id);
	#$script_override = OSDpreg_replace("/[^0-9a-zA-Z]/","",$script_override);
	#$script_button_id = OSDpreg_replace("/[^0-9a-zA-Z]/","",$script_button_id);
	$submit = OSDpreg_replace("/[^0-9a-zA-Z]/","",$submit);
	#$get_call_launch = OSDpreg_replace("/[^0-9a-zA-Z]/","",$get_call_launch);
	#$campaign_recording = OSDpreg_replace("/[^0-9a-zA-Z]/","",$campaign_recording);
	$ADD = OSDpreg_replace("/[^0-9a-zA-Z]/","",$ADD);
	$dial_prefix = OSDpreg_replace("/[^0-9a-zA-Z]/","",$dial_prefix);
	#$state_call_time_state = OSDpreg_replace("/[^0-9a-zA-Z]/","",$state_call_time_state);
	#$scheduled_callbacks = OSDpreg_replace("/[^0-9a-zA-Z]/","",$scheduled_callbacks);
	$concurrent_transfers = OSDpreg_replace("/[^0-9a-zA-Z]/","",$concurrent_transfers);
	#$billable = OSDpreg_replace("/[^0-9a-zA-Z]/","",$billable);
	#$pause_code = OSDpreg_replace("/[^0-9a-zA-Z]/","",$pause_code);
	#$osdial_recording_override = OSDpreg_replace("/[^0-9a-zA-Z]/","",$osdial_recording_override);
	$queuemetrics_log_id = OSDpreg_replace("/[^0-9a-zA-Z]/","",$queuemetrics_log_id);
	#$after_hours_action = OSDpreg_replace("/[^0-9a-zA-Z]/","",$after_hours_action);
	$after_hours_exten = OSDpreg_replace("/[^0-9a-zA-Z]/","",$after_hours_exten);
	$after_hours_voicemail = OSDpreg_replace("/[^0-9a-zA-Z]/","",$after_hours_voicemail);
	#$intra_server_protocol = OSDpreg_replace("/[^0-9a-zA-Z]/","",$intra_server_protocol);
	
	### DIGITS and Dots
	$server_ip = OSDpreg_replace("/[^\.0-9]/","",$server_ip);
	$auto_dial_level = OSDpreg_replace("/[^\.0-9]/","",$auto_dial_level);
	$adaptive_maximum_level = OSDpreg_replace("/[^\.0-9]/","",$adaptive_maximum_level);
	$phone_ip = OSDpreg_replace("/[^\.0-9]/","",$phone_ip);
	$old_server_ip = OSDpreg_replace("/[^\.0-9]/","",$old_server_ip);
	$computer_ip = OSDpreg_replace("/[^\.0-9]/","",$computer_ip);
	$cost = OSDpreg_replace("/[^\.0-9]/","",$cost);
	$queuemetrics_server_ip = OSDpreg_replace("/[^\.0-9]/","",$queuemetrics_server_ip);
	
	### ALPHA-NUMERIC and spaces and hash and star and comma
	$xferconf_a_dtmf = OSDpreg_replace("/[^ \,\*\#0-9a-zA-Z]/","",$xferconf_a_dtmf);
	$xferconf_b_dtmf = OSDpreg_replace("/[^ \,\*\#0-9a-zA-Z]/","",$xferconf_b_dtmf);
	
	### ALPHACAPS-NUMERIC
	$xferconf_a_number = OSDpreg_replace("/[^0-9A-Z]/","",$xferconf_a_number);
	$xferconf_b_number = OSDpreg_replace("/[^0-9A-Z]/","",$xferconf_b_number);
	
	### ALPHA-NUMERIC and underscore and dash
	#$agi_output = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$agi_output);
	#$ASTmgrSECRET = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrSECRET);
	#$ASTmgrUSERNAME = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrUSERNAME);
	#$ASTmgrUSERNAMElisten = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrUSERNAMElisten);
	#$ASTmgrUSERNAMEsend = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrUSERNAMEsend);
	#$ASTmgrUSERNAMEupdate = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$ASTmgrUSERNAMEupdate);
	#$call_time_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$call_time_id);
	#$et_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",OSDstrtoupper($et_id));
	#$campaign_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$campaign_id);
	$campaign_id = OSDpreg_replace("/[\s\'\"]/","",$campaign_id);
	$CoNfIrM = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$CoNfIrM);
	#$DBX_database = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$DBX_database);
	#$DBX_pass = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$DBX_pass);
	#$DBX_user = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$DBX_user);
	#$DBY_database = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$DBY_database);
	#$DBY_pass = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$DBY_pass);
	#$DBY_user = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$DBY_user);
	#$dial_method = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$dial_method);
	#$dial_status_a = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_a);
	#$dial_status_b = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_b);
	#$dial_status_c = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_c);
	#$dial_status_d = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_d);
	#$dial_status_e = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status_e);
	#$drop_trigger = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$drop_trigger);
	#$ext_context = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$ext_context);
	#$group_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$group_id);
	$group_id = OSDpreg_replace("/[\s\'\"]/","",$group_id);
	#$lead_filter_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$lead_filter_id);
	$lead_filter_id = OSDpreg_replace("/[\s\'\"]/","",$lead_filter_id);
	#$local_call_time = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$local_call_time);
	#$login = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$login);
	$login = OSDpreg_replace("/[\s\'\"]/","",$login);
	#$login_campaign = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$login_campaign);
	$login_campaign = OSDpreg_replace("/[\s\'\"]/","",$login_campaign);
	#$login_pass = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$login_pass);
	#$login_user = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$login_user);
	$login_user = OSDpreg_replace("/[\s\'\"]/","",$login_user);
	#$next_agent_call = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$next_agent_call);
	#$old_campaign_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$old_campaign_id);
	$old_campaign_id = OSDpreg_replace("/[\s\'\"]/","",$old_campaign_id);
	#$old_server_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$old_server_id);
	#$OLDuser_group = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$OLDuser_group);
	$OLDuser_group = OSDpreg_replace("/[\s\'\"]/","",$OLDuser_group);
	#$park_file_name = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$park_file_name);
	#$pass = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$pass);
	#$phone_login = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$phone_login);
	$phone_login = OSDpreg_replace("/[\s\'\"]/","",$phone_login);
	#$phone_pass = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$phone_pass);
	$phone_pass = OSDpreg_replace("/[\s\'\"]/","",$phone_pass);
	#$PHP_AUTH_PW = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$PHP_AUTH_PW);
	#$PHP_AUTH_USER = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$PHP_AUTH_USER);
	#$PHP_AUTH_USER = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$PHP_AUTH_USER);
	$protocol = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$protocol);
	#$server_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$server_id);
	#$stage = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$stage);
	#$state_rule = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$state_rule);
	#$status = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$status);
	$trunk_restriction = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$trunk_restriction);
	#$user = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$user);
	$user = OSDpreg_replace("/[\s\'\"]/","",$user);
	#$user_group = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$user_group);
	$user_group = OSDpreg_replace("/[\s\'\"]/","",$user_group);
	#$OSDIAL_park_on_filename = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$OSDIAL_park_on_filename);
	$auto_alt_dial = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$auto_alt_dial);
	#$dial_status = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$dial_status);
	$dial_status = OSDpreg_replace("/[\s\'\"]/","",$dial_status);
	$queuemetrics_eq_prepend = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$queuemetrics_eq_prepend);
	$osdial_agent_disable = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$osdial_agent_disable);
	$alter_custdata_override = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$alter_custdata_override);
	#$list_order_mix = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$list_order_mix);
	#$vcl_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$vcl_id);
	$mix_method = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$mix_method);
	$category = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$category);
	$vsc_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$vsc_id);
	$moh_context = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$moh_context);
	$agent_alert_exten = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$agent_alert_exten);
	#$source_campaign_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$source_campaign_id);
	$source_campaign_id = OSDpreg_replace("/[\s\'\"]/","",$source_campaign_id);
	#$source_user_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$source_user_id);
	$source_user_id = OSDpreg_replace("/[\s\'\"]/","",$source_user_id);
	#$source_group_id = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$source_group_id);
	$source_group_id = OSDpreg_replace("/[\s\'\"]/","",$source_group_id);
	#$default_xfer_group = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$default_xfer_group);
	$default_xfer_group = OSDpreg_replace("/[\s\'\"]/","",$default_xfer_group);
	$xfer_cid_mode = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$xfer_cid_mode);
	$carrier_name = OSDpreg_replace("/[^-\_0-9a-zA-Z]/","",$carrier_name);
	
	### ALPHA-NUMERIC and spaces
	$lead_order = OSDpreg_replace("/[^ 0-9a-zA-Z]/","",$lead_order);
	#$script_button_label = OSDpreg_replace("/[^ 0-9a-zA-Z]/","",$script_button_label);
	### ALPHA-NUMERIC and hash
	$group_color = OSDpreg_replace("/[^\#0-9a-zA-Z]/","",$group_color);
	
	### ALPHA-NUMERIC and spaces dots, commas, dashes, underscores
	$adaptive_dl_diff_target = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$adaptive_dl_diff_target);
	$adaptive_intensity = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$adaptive_intensity);
	$asterisk_version = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$asterisk_version);
	#$call_time_comments = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$call_time_comments);
	#$call_time_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$call_time_name);
	#$campaign_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$campaign_name);
	#$campaign_rec_filename = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$campaign_rec_filename);
	#$company = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$company);
	#$full_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$full_name);
	#$fullname = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$fullname);
	#$group_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$group_name);
	#$HKstatus = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$HKstatus);
	#$lead_filter_comments = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$lead_filter_comments);
	#$lead_filter_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$lead_filter_name);
	#$list_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$list_name);
	$local_gmt = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$local_gmt);
	#$phone_type = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$phone_type);
	$picture = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$picture);
	#$script_comments = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$script_comments);
	#$script_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$script_name);
	#$server_description = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$server_description);
	#$status = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$status);
	#$status_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$status_name);
	#$wrapup_message = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$wrapup_message);
	#$pause_code_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$pause_code_name);
	#$campaign_description = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$campaign_description);
	#$list_description = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$list_description);
	#$vcl_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$vcl_name);
	#$vsc_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$vsc_name);
	#$vsc_description = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$vsc_description);
	$campaign_cid_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$campaign_cid_name);
	$outbound_cid_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$outbound_cid_name);
	$cid_name = OSDpreg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$cid_name);
	
	### ALPHA-NUMERIC and underscore and dash and slash and at and dot
	$call_out_number_group = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$call_out_number_group);
	$client_browser = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$client_browser);
	$DBX_server = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$DBX_server);
	$DBY_server = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$DBY_server);
	$dtmf_send_extension = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$dtmf_send_extension);
	$extension = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$extension);
	$install_directory = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$install_directory);
	$old_extension = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$old_extension);
	$telnet_host = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$telnet_host);
	$queuemetrics_dbname = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$queuemetrics_dbname);
	$queuemetrics_login = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$queuemetrics_login);
	$queuemetrics_pass = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$queuemetrics_pass);
	$after_hours_message_filename = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$after_hours_message_filename);
	$welcome_message_filename = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$welcome_message_filename);
	$onhold_prompt_filename = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$onhold_prompt_filename);
	$voicemail_email = OSDpreg_replace("/[^-\.\:\/\@\_0-9a-zA-Z]/","",$voicemail_email);

    # Others
	$company_name = OSDpreg_replace("/[^ \.\!\,\&\@0-9a-zA-Z]/","",$company_name);
	
	### remove semi-colons ###
	$lead_filter_sql = OSDpreg_replace("/;/","",$lead_filter_sql);
	$list_mix_container = OSDpreg_replace("/;/","",$list_mix_container);
	
	### VARIABLES TO BE mysql_real_escape_string ###
	# $web_form_address
	# $queuemetrics_url
	# $admin_home_url
	
	### VARIABLES not filtered at all ###
	# $script_text
#}	# end of non_latin


##### END VARIABLE FILTERING FOR SECURITY #####
?>
