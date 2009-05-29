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
	$adaptive_dropped_percentage = ereg_replace("[^0-9]","",$adaptive_dropped_percentage);
	$adaptive_latest_server_time = ereg_replace("[^0-9]","",$adaptive_latest_server_time);
	$admin_hangup_enabled = ereg_replace("[^0-9]","",$admin_hangup_enabled);
	$admin_hijack_enabled = ereg_replace("[^0-9]","",$admin_hijack_enabled);
	$admin_monitor_enabled = ereg_replace("[^0-9]","",$admin_monitor_enabled);
	$AFLogging_enabled = ereg_replace("[^0-9]","",$AFLogging_enabled);
	$agent_choose_ingroups = ereg_replace("[^0-9]","",$agent_choose_ingroups);
	$agentcall_manual = ereg_replace("[^0-9]","",$agentcall_manual);
	$agentonly_callbacks = ereg_replace("[^0-9]","",$agentonly_callbacks);
	$AGI_call_logging_enabled = ereg_replace("[^0-9]","",$AGI_call_logging_enabled);
	$allcalls_delay = ereg_replace("[^0-9]","",$allcalls_delay);
	$alter_agent_interface_options = ereg_replace("[^0-9]","",$alter_agent_interface_options);
	$am_message_exten = ereg_replace("[^0-9]","",$am_message_exten);
	$answer_transfer_agent = ereg_replace("[^0-9]","",$answer_transfer_agent);
	$ast_admin_access = ereg_replace("[^0-9]","",$ast_admin_access);
	$ast_delete_phones = ereg_replace("[^0-9]","",$ast_delete_phones);
	$attempt_delay = ereg_replace("[^0-9]","",$attempt_delay);
	$attempt_maximum = ereg_replace("[^0-9]","",$attempt_maximum);
	$auto_dial_next_number = ereg_replace("[^0-9]","",$auto_dial_next_number);
	$balance_trunks_offlimits = ereg_replace("[^0-9]","",$balance_trunks_offlimits);
	$call_parking_enabled = ereg_replace("[^0-9]","",$call_parking_enabled);
	$answers_per_hour_limit = ereg_replace("[^0-9]","",$answers_per_hour_limit);
	$CallerID_popup_enabled = ereg_replace("[^0-9]","",$CallerID_popup_enabled);
	$campaign_detail = ereg_replace("[^0-9]","",$campaign_detail);
	$campaign_rec_exten = ereg_replace("[^0-9]","",$campaign_rec_exten);
	$campaign_vdad_exten = ereg_replace("[^0-9]","",$campaign_vdad_exten);
	$change_agent_campaign = ereg_replace("[^0-9]","",$change_agent_campaign);
	$closer_default_blended = ereg_replace("[^0-9]","",$closer_default_blended);
	$conf_exten = ereg_replace("[^0-9]","",$conf_exten);
	$conf_on_extension = ereg_replace("[^0-9]","",$conf_on_extension);
	$conferencing_enabled = ereg_replace("[^0-9]","",$conferencing_enabled);
	$ct_default_start = ereg_replace("[^0-9]","",$ct_default_start);
	$ct_default_stop = ereg_replace("[^0-9]","",$ct_default_stop);
	$ct_friday_start = ereg_replace("[^0-9]","",$ct_friday_start);
	$ct_friday_stop = ereg_replace("[^0-9]","",$ct_friday_stop);
	$ct_monday_start = ereg_replace("[^0-9]","",$ct_monday_start);
	$ct_monday_stop = ereg_replace("[^0-9]","",$ct_monday_stop);
	$ct_saturday_start = ereg_replace("[^0-9]","",$ct_saturday_start);
	$ct_saturday_stop = ereg_replace("[^0-9]","",$ct_saturday_stop);
	$ct_sunday_start = ereg_replace("[^0-9]","",$ct_sunday_start);
	$ct_sunday_stop = ereg_replace("[^0-9]","",$ct_sunday_stop);
	$ct_thursday_start = ereg_replace("[^0-9]","",$ct_thursday_start);
	$ct_thursday_stop = ereg_replace("[^0-9]","",$ct_thursday_stop);
	$ct_tuesday_start = ereg_replace("[^0-9]","",$ct_tuesday_start);
	$ct_tuesday_stop = ereg_replace("[^0-9]","",$ct_tuesday_stop);
	$ct_wednesday_start = ereg_replace("[^0-9]","",$ct_wednesday_start);
	$ct_wednesday_stop = ereg_replace("[^0-9]","",$ct_wednesday_stop);
	$DBX_port = ereg_replace("[^0-9]","",$DBX_port);
	$DBY_port = ereg_replace("[^0-9]","",$DBY_port);
	$dedicated_trunks = ereg_replace("[^0-9]","",$dedicated_trunks);
	$delete_call_times = ereg_replace("[^0-9]","",$delete_call_times);
	$delete_campaigns = ereg_replace("[^0-9]","",$delete_campaigns);
	$delete_filters = ereg_replace("[^0-9]","",$delete_filters);
	$delete_ingroups = ereg_replace("[^0-9]","",$delete_ingroups);
	$delete_lists = ereg_replace("[^0-9]","",$delete_lists);
	$delete_remote_agents = ereg_replace("[^0-9]","",$delete_remote_agents);
	$delete_scripts = ereg_replace("[^0-9]","",$delete_scripts);
	$delete_user_groups = ereg_replace("[^0-9]","",$delete_user_groups);
	$delete_users = ereg_replace("[^0-9]","",$delete_users);
	$dial_timeout = ereg_replace("[^0-9]","",$dial_timeout);
	$dialplan_number = ereg_replace("[^0-9]","",$dialplan_number);
	$drop_call_seconds = ereg_replace("[^0-9]","",$drop_call_seconds);
	$drop_exten = ereg_replace("[^0-9]","",$drop_exten);
	$enable_fast_refresh = ereg_replace("[^0-9]","",$enable_fast_refresh);
	$enable_persistant_mysql = ereg_replace("[^0-9]","",$enable_persistant_mysql);
	$fast_refresh_rate = ereg_replace("[^0-9]","",$fast_refresh_rate);
	$hopper_level = ereg_replace("[^0-9]","",$hopper_level);
	$hotkey = ereg_replace("[^0-9]","",$hotkey);
	$hotkeys_active = ereg_replace("[^0-9]","",$hotkeys_active);
	$list_id = ereg_replace("[^0-9]","",$list_id);
	$load_leads = ereg_replace("[^0-9]","",$load_leads);
	$max_osdial_trunks = ereg_replace("[^0-9]","",$max_osdial_trunks);
	$modify_call_times = ereg_replace("[^0-9]","",$modify_call_times);
	$modify_users = ereg_replace("[^0-9]","",$modify_users);
	$modify_campaigns = ereg_replace("[^0-9]","",$modify_campaigns);
	$modify_lists = ereg_replace("[^0-9]","",$modify_lists);
	$modify_scripts = ereg_replace("[^0-9]","",$modify_scripts);
	$modify_filters = ereg_replace("[^0-9]","",$modify_filters);
	$modify_ingroups = ereg_replace("[^0-9]","",$modify_ingroups);
	$modify_usergroups = ereg_replace("[^0-9]","",$modify_usergroups);
	$modify_remoteagents = ereg_replace("[^0-9]","",$modify_remoteagents);
	$modify_servers = ereg_replace("[^0-9]","",$modify_servers);
	$view_reports = ereg_replace("[^0-9]","",$view_reports);
	$modify_leads = ereg_replace("[^0-9]","",$modify_leads);
	$monitor_prefix = ereg_replace("[^0-9]","",$monitor_prefix);
	$number_of_lines = ereg_replace("[^0-9]","",$number_of_lines);
	$old_conf_exten = ereg_replace("[^0-9]","",$old_conf_exten);
	$outbound_cid = ereg_replace("[^0-9]","",$outbound_cid);
	$park_ext = ereg_replace("[^0-9]","",$park_ext);
	$park_on_extension = ereg_replace("[^0-9]","",$park_on_extension);
	$phone_number = ereg_replace("[^0-9]","",$phone_number);
	$preview_force_dial_time = ereg_replace("[^0-9]","",$preview_force_dial_time);
	$QUEUE_ACTION_enabled = ereg_replace("[^0-9]","",$QUEUE_ACTION_enabled);
	$recording_exten = ereg_replace("[^0-9]","",$recording_exten);
	$remote_agent_id = ereg_replace("[^0-9]","",$remote_agent_id);
	$safe_harbor_exten = ereg_replace("[^0-9]","",$safe_harbor_exten);
	$telnet_port = ereg_replace("[^0-9]","",$telnet_port);
	$updater_check_enabled = ereg_replace("[^0-9]","",$updater_check_enabled);
	$user_level = ereg_replace("[^0-9]","",$user_level);
	$user_switching_enabled = ereg_replace("[^0-9]","",$user_switching_enabled);
	$VDstop_rec_after_each_call = ereg_replace("[^0-9]","",$VDstop_rec_after_each_call);
	$OSDIAL_park_on_extension = ereg_replace("[^0-9]","",$OSDIAL_park_on_extension);
	$osdial_recording = ereg_replace("[^0-9]","",$osdial_recording);
	$osdial_transfers = ereg_replace("[^0-9]","",$osdial_transfers);
	$voicemail_button_enabled = ereg_replace("[^0-9]","",$voicemail_button_enabled);
	$voicemail_dump_exten = ereg_replace("[^0-9]","",$voicemail_dump_exten);
	$voicemail_ext = ereg_replace("[^0-9]","",$voicemail_ext);
	$voicemail_exten = ereg_replace("[^0-9]","",$voicemail_exten);
	$voicemail_id = ereg_replace("[^0-9]","",$voicemail_id);
	$wrapup_seconds = ereg_replace("[^0-9]","",$wrapup_seconds);
	$use_non_latin = ereg_replace("[^0-9]","",$use_non_latin);
	$webroot_writable = ereg_replace("[^0-9]","",$webroot_writable);
	$enable_queuemetrics_logging = ereg_replace("[^0-9]","",$enable_queuemetrics_logging);
	$enable_sipsak_messages = ereg_replace("[^0-9]","",$enable_sipsak_messages);
	$allow_sipsak_messages = ereg_replace("[^0-9]","",$allow_sipsak_messages);
	$mix_container_item = ereg_replace("[^0-9]","",$mix_container_item);
	$prompt_interval = ereg_replace("[^0-9]","",$prompt_interval);
	$agent_alert_delay = ereg_replace("[^0-9]","",$agent_alert_delay);
	$manual_dial_list_id = ereg_replace("[^0-9]","",$manual_dial_list_id);
	
	### DIGITS and DASHES
	$group_rank = ereg_replace("[^-0-9]","",$group_rank);
	$campaign_rank = ereg_replace("[^-0-9]","",$campaign_rank);
	
	### Y or N ONLY ###
	$active = ereg_replace("[^NY]","",$active);
	$allow_closers = ereg_replace("[^NY]","",$allow_closers);
	$reset_hopper = ereg_replace("[^NY]","",$reset_hopper);
	$amd_send_to_vmx = ereg_replace("[^NY]","",$amd_send_to_vmx);
	$alt_number_dialing = ereg_replace("[^NY]","",$alt_number_dialing);
	$safe_harbor_message = ereg_replace("[^NY]","",$safe_harbor_message);
	$selectable = ereg_replace("[^NY]","",$selectable);
	$reset_list = ereg_replace("[^NY]","",$reset_list);
	$fronter_display = ereg_replace("[^NY]","",$fronter_display);
	$drop_message = ereg_replace("[^NY]","",$drop_message);
	$use_internal_dnc = ereg_replace("[^NY]","",$use_internal_dnc);
	$omit_phone_code = ereg_replace("[^NY]","",$omit_phone_code);
	$available_only_ratio_tally = ereg_replace("[^NY]","",$available_only_ratio_tally);
	$sys_perf_log = ereg_replace("[^NY]","",$sys_perf_log);
	$osdial_balance_active = ereg_replace("[^NY]","",$osdial_balance_active);
	$manual_preview_default = ereg_replace("[^NY]","",$manual_preview_default);
	$vd_server_logs = ereg_replace("[^NY]","",$vd_server_logs);
	$agent_pause_codes_active = ereg_replace("[^NY]","",$agent_pause_codes_active);
	$campaign_stats_refresh = ereg_replace("[^NY]","",$campaign_stats_refresh);
	$disable_alter_custdata = ereg_replace("[^NY]","",$disable_alter_custdata);
	$no_hopper_leads_logins = ereg_replace("[^NY]","",$no_hopper_leads_logins);
	$human_answered = ereg_replace("[^NY]","",$human_answered);
	$tovdad_display = ereg_replace("[^NY]","",$tovdad_display);
	$campaign_allow_inbound = ereg_replace("[^NY]","",$campaign_allow_inbound);
	$allow_tab_switch = ereg_replace("[^NY]","",$allow_tab_switch);
	$web_form_extwindow = ereg_replace("[^NY]","",$web_form_extwindow);
	$web_form2_extwindow = ereg_replace("[^NY]","",$web_form2_extwindow);
	
	### ALPHA-NUMERIC ONLY ###
	$user_start = ereg_replace("[^0-9a-zA-Z]","",$user_start);
	$script_id = ereg_replace("[^0-9a-zA-Z]","",$script_id);
	$submit = ereg_replace("[^0-9a-zA-Z]","",$submit);
	$campaign_cid = ereg_replace("[^0-9a-zA-Z]","",$campaign_cid);
	$get_call_launch = ereg_replace("[^0-9a-zA-Z]","",$get_call_launch);
	$campaign_recording = ereg_replace("[^0-9a-zA-Z]","",$campaign_recording);
	$ADD = ereg_replace("[^0-9a-zA-Z]","",$ADD);
	$dial_prefix = ereg_replace("[^0-9a-zA-Z]","",$dial_prefix);
	$state_call_time_state = ereg_replace("[^0-9a-zA-Z]","",$state_call_time_state);
	$scheduled_callbacks = ereg_replace("[^0-9a-zA-Z]","",$scheduled_callbacks);
	$concurrent_transfers = ereg_replace("[^0-9a-zA-Z]","",$concurrent_transfers);
	$billable = ereg_replace("[^0-9a-zA-Z]","",$billable);
	$pause_code = ereg_replace("[^0-9a-zA-Z]","",$pause_code);
	$osdial_recording_override = ereg_replace("[^0-9a-zA-Z]","",$osdial_recording_override);
	$queuemetrics_log_id = ereg_replace("[^0-9a-zA-Z]","",$queuemetrics_log_id);
	$after_hours_action = ereg_replace("[^0-9a-zA-Z]","",$after_hours_action);
	$after_hours_exten = ereg_replace("[^0-9a-zA-Z]","",$after_hours_exten);
	$after_hours_voicemail = ereg_replace("[^0-9a-zA-Z]","",$after_hours_voicemail);
	
	### DIGITS and Dots
	$server_ip = ereg_replace("[^\.0-9]","",$server_ip);
	$auto_dial_level = ereg_replace("[^\.0-9]","",$auto_dial_level);
	$adaptive_maximum_level = ereg_replace("[^\.0-9]","",$adaptive_maximum_level);
	$phone_ip = ereg_replace("[^\.0-9]","",$phone_ip);
	$old_server_ip = ereg_replace("[^\.0-9]","",$old_server_ip);
	$computer_ip = ereg_replace("[^\.0-9]","",$computer_ip);
	$queuemetrics_server_ip = ereg_replace("[^\.0-9]","",$queuemetrics_server_ip);
	
	### ALPHA-NUMERIC and spaces and hash and star and comma
	$xferconf_a_dtmf = ereg_replace("[^ \,\*\#0-9a-zA-Z]","",$xferconf_a_dtmf);
	$xferconf_b_dtmf = ereg_replace("[^ \,\*\#0-9a-zA-Z]","",$xferconf_b_dtmf);
	
	### ALPHACAPS-NUMERIC
	$xferconf_a_number = ereg_replace("[^0-9A-Z]","",$xferconf_a_number);
	$xferconf_b_number = ereg_replace("[^0-9A-Z]","",$xferconf_b_number);
	
	### ALPHA-NUMERIC and underscore and dash
	$agi_output = ereg_replace("[^-\_0-9a-zA-Z]","",$agi_output);
	$ASTmgrSECRET = ereg_replace("[^-\_0-9a-zA-Z]","",$ASTmgrSECRET);
	$ASTmgrUSERNAME = ereg_replace("[^-\_0-9a-zA-Z]","",$ASTmgrUSERNAME);
	$ASTmgrUSERNAMElisten = ereg_replace("[^-\_0-9a-zA-Z]","",$ASTmgrUSERNAMElisten);
	$ASTmgrUSERNAMEsend = ereg_replace("[^-\_0-9a-zA-Z]","",$ASTmgrUSERNAMEsend);
	$ASTmgrUSERNAMEupdate = ereg_replace("[^-\_0-9a-zA-Z]","",$ASTmgrUSERNAMEupdate);
	$call_time_id = ereg_replace("[^-\_0-9a-zA-Z]","",$call_time_id);
	$campaign_id = ereg_replace("[^-\_0-9a-zA-Z]","",$campaign_id);
	$CoNfIrM = ereg_replace("[^-\_0-9a-zA-Z]","",$CoNfIrM);
	$DBX_database = ereg_replace("[^-\_0-9a-zA-Z]","",$DBX_database);
	$DBX_pass = ereg_replace("[^-\_0-9a-zA-Z]","",$DBX_pass);
	$DBX_user = ereg_replace("[^-\_0-9a-zA-Z]","",$DBX_user);
	$DBY_database = ereg_replace("[^-\_0-9a-zA-Z]","",$DBY_database);
	$DBY_pass = ereg_replace("[^-\_0-9a-zA-Z]","",$DBY_pass);
	$DBY_user = ereg_replace("[^-\_0-9a-zA-Z]","",$DBY_user);
	$dial_method = ereg_replace("[^-\_0-9a-zA-Z]","",$dial_method);
	$dial_status_a = ereg_replace("[^-\_0-9a-zA-Z]","",$dial_status_a);
	$dial_status_b = ereg_replace("[^-\_0-9a-zA-Z]","",$dial_status_b);
	$dial_status_c = ereg_replace("[^-\_0-9a-zA-Z]","",$dial_status_c);
	$dial_status_d = ereg_replace("[^-\_0-9a-zA-Z]","",$dial_status_d);
	$dial_status_e = ereg_replace("[^-\_0-9a-zA-Z]","",$dial_status_e);
	$ext_context = ereg_replace("[^-\_0-9a-zA-Z]","",$ext_context);
	$group_id = ereg_replace("[^-\_0-9a-zA-Z]","",$group_id);
	$lead_filter_id = ereg_replace("[^-\_0-9a-zA-Z]","",$lead_filter_id);
	$local_call_time = ereg_replace("[^-\_0-9a-zA-Z]","",$local_call_time);
	$login = ereg_replace("[^-\_0-9a-zA-Z]","",$login);
	$login_campaign = ereg_replace("[^-\_0-9a-zA-Z]","",$login_campaign);
	$login_pass = ereg_replace("[^-\_0-9a-zA-Z]","",$login_pass);
	$login_user = ereg_replace("[^-\_0-9a-zA-Z]","",$login_user);
	$next_agent_call = ereg_replace("[^-\_0-9a-zA-Z]","",$next_agent_call);
	$old_campaign_id = ereg_replace("[^-\_0-9a-zA-Z]","",$old_campaign_id);
	$old_server_id = ereg_replace("[^-\_0-9a-zA-Z]","",$old_server_id);
	$OLDuser_group = ereg_replace("[^-\_0-9a-zA-Z]","",$OLDuser_group);
	$park_file_name = ereg_replace("[^-\_0-9a-zA-Z]","",$park_file_name);
	$pass = ereg_replace("[^-\_0-9a-zA-Z]","",$pass);
	$phone_login = ereg_replace("[^-\_0-9a-zA-Z]","",$phone_login);
	$phone_pass = ereg_replace("[^-\_0-9a-zA-Z]","",$phone_pass);
	$PHP_AUTH_PW = ereg_replace("[^-\_0-9a-zA-Z]","",$PHP_AUTH_PW);
	$PHP_AUTH_USER = ereg_replace("[^-\_0-9a-zA-Z]","",$PHP_AUTH_USER);
	$protocol = ereg_replace("[^-\_0-9a-zA-Z]","",$protocol);
	$server_id = ereg_replace("[^-\_0-9a-zA-Z]","",$server_id);
	$stage = ereg_replace("[^-\_0-9a-zA-Z]","",$stage);
	$state_rule = ereg_replace("[^-\_0-9a-zA-Z]","",$state_rule);
	$status = ereg_replace("[^-\_0-9a-zA-Z]","",$status);
	$trunk_restriction = ereg_replace("[^-\_0-9a-zA-Z]","",$trunk_restriction);
	$user = ereg_replace("[^-\_0-9a-zA-Z]","",$user);
	$user_group = ereg_replace("[^-\_0-9a-zA-Z]","",$user_group);
	$OSDIAL_park_on_filename = ereg_replace("[^-\_0-9a-zA-Z]","",$OSDIAL_park_on_filename);
	$auto_alt_dial = ereg_replace("[^-\_0-9a-zA-Z]","",$auto_alt_dial);
	$dial_status = ereg_replace("[^-\_0-9a-zA-Z]","",$dial_status);
	$queuemetrics_eq_prepend = ereg_replace("[^-\_0-9a-zA-Z]","",$queuemetrics_eq_prepend);
	$osdial_agent_disable = ereg_replace("[^-\_0-9a-zA-Z]","",$osdial_agent_disable);
	$alter_custdata_override = ereg_replace("[^-\_0-9a-zA-Z]","",$alter_custdata_override);
	$list_order_mix = ereg_replace("[^-\_0-9a-zA-Z]","",$list_order_mix);
	$vcl_id = ereg_replace("[^-\_0-9a-zA-Z]","",$vcl_id);
	$mix_method = ereg_replace("[^-\_0-9a-zA-Z]","",$mix_method);
	$category = ereg_replace("[^-\_0-9a-zA-Z]","",$category);
	$vsc_id = ereg_replace("[^-\_0-9a-zA-Z]","",$vsc_id);
	$moh_context = ereg_replace("[^-\_0-9a-zA-Z]","",$moh_context);
	$agent_alert_exten = ereg_replace("[^-\_0-9a-zA-Z]","",$agent_alert_exten);
	$source_campaign_id = ereg_replace("[^-\_0-9a-zA-Z]","",$source_campaign_id);
	$source_user_id = ereg_replace("[^-\_0-9a-zA-Z]","",$source_user_id);
	$source_group_id = ereg_replace("[^-\_0-9a-zA-Z]","",$source_group_id);
	$default_xfer_group = ereg_replace("[^-\_0-9a-zA-Z]","",$default_xfer_group);
	
	### ALPHA-NUMERIC and spaces
	$lead_order = ereg_replace("[^ 0-9a-zA-Z]","",$lead_order);
	### ALPHA-NUMERIC and hash
	$group_color = ereg_replace("[^\#0-9a-zA-Z]","",$group_color);
	
	### ALPHA-NUMERIC and spaces dots, commas, dashes, underscores
	$adaptive_dl_diff_target = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$adaptive_dl_diff_target);
	$adaptive_intensity = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$adaptive_intensity);
	$asterisk_version = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$asterisk_version);
	$call_time_comments = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$call_time_comments);
	$call_time_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$call_time_name);
	$campaign_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$campaign_name);
	$campaign_rec_filename = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$campaign_rec_filename);
	$company = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$company);
	$full_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$full_name);
	$fullname = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$fullname);
	$group_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$group_name);
	$HKstatus = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$HKstatus);
	$lead_filter_comments = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$lead_filter_comments);
	$lead_filter_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$lead_filter_name);
	$list_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$list_name);
	$local_gmt = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$local_gmt);
	$phone_type = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$phone_type);
	$picture = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$picture);
	$script_comments = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$script_comments);
	$script_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$script_name);
	$server_description = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$server_description);
	$status = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$status);
	$status_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$status_name);
	$wrapup_message = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$wrapup_message);
	$pause_code_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$pause_code_name);
	$campaign_description = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$campaign_description);
	$list_description = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$list_description);
	$vcl_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$vcl_name);
	$vsc_name = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$vsc_name);
	$vsc_description = ereg_replace("[^ \.\,-\_0-9a-zA-Z]","",$vsc_description);
	
	### ALPHA-NUMERIC and underscore and dash and slash and at and dot
	$call_out_number_group = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$call_out_number_group);
	$client_browser = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$client_browser);
	$DBX_server = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$DBX_server);
	$DBY_server = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$DBY_server);
	$dtmf_send_extension = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$dtmf_send_extension);
	$extension = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$extension);
	$install_directory = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$install_directory);
	$old_extension = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$old_extension);
	$telnet_host = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$telnet_host);
	$queuemetrics_dbname = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$queuemetrics_dbname);
	$queuemetrics_login = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$queuemetrics_login);
	$queuemetrics_pass = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$queuemetrics_pass);
	$after_hours_message_filename = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$after_hours_message_filename);
	$welcome_message_filename = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$welcome_message_filename);
	$onhold_prompt_filename = ereg_replace("[^-\.\:\/\@\_0-9a-zA-Z]","",$onhold_prompt_filename);

    # Others
	$company_name = ereg_replace("[^ \.\!\,\&\@0-9a-zA-Z]","",$company_name);
	
	### remove semi-colons ###
	$lead_filter_sql = ereg_replace(";","",$lead_filter_sql);
	$list_mix_container = ereg_replace(";","",$list_mix_container);
	
	### VARIABLES TO BE mysql_real_escape_string ###
	# $web_form_address
	# $queuemetrics_url
	# $admin_home_url
	
	### VARIABLES not filtered at all ###
	# $script_text
}	# end of non_latin


##### END VARIABLE FILTERING FOR SECURITY #####
?>
