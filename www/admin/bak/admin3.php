<?
# admin.php - VICIDIAL administration page
# 
# 
# Copyright (C) 2007  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#


require("dbconnect.php");


######################################################################################################
######################################################################################################
#######   static variable settings for display options
######################################################################################################
######################################################################################################

require("admin_config.inc");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];

######################################################################################################
######################################################################################################
#######   Form variable declaration
######################################################################################################
######################################################################################################


if (isset($_GET["active"])) {$active=$_GET["active"];}
 elseif (isset($_POST["active"])) {$active=$_POST["active"];}
if (isset($_GET["adastats"]))   {$adastats=$_GET["adastats"];}
 elseif (isset($_POST["adastats"])) {$adastats=$_POST["adastats"];}
if (isset($_GET["adaptive_dl_diff_target"])) {$adaptive_dl_diff_target=$_GET["adaptive_dl_diff_target"];}
 elseif (isset($_POST["adaptive_dl_diff_target"])) {$adaptive_dl_diff_target=$_POST["adaptive_dl_diff_target"];}
if (isset($_GET["adaptive_dropped_percentage"])) {$adaptive_dropped_percentage=$_GET["adaptive_dropped_percentage"];}
 elseif (isset($_POST["adaptive_dropped_percentage"])){$adaptive_dropped_percentage=$_POST["adaptive_dropped_percentage"];}
if (isset($_GET["adaptive_intensity"])) {$adaptive_intensity=$_GET["adaptive_intensity"];}
 elseif (isset($_POST["adaptive_intensity"])) {$adaptive_intensity=$_POST["adaptive_intensity"];}
if (isset($_GET["adaptive_latest_server_time"])) {$adaptive_latest_server_time=$_GET["adaptive_latest_server_time"];}
 elseif (isset($_POST["adaptive_latest_server_time"])){$adaptive_latest_server_time=$_POST["adaptive_latest_server_time"];}
if (isset($_GET["adaptive_maximum_level"])) {$adaptive_maximum_level=$_GET["adaptive_maximum_level"];}
 elseif (isset($_POST["adaptive_maximum_level"])) {$adaptive_maximum_level=$_POST["adaptive_maximum_level"];}
if (isset($_GET["ADD"]))   {$ADD=$_GET["ADD"];}
 elseif (isset($_POST["ADD"])) {$ADD=$_POST["ADD"];}
if (isset($_GET["admin_hangup_enabled"])) {$admin_hangup_enabled=$_GET["admin_hangup_enabled"];}
 elseif (isset($_POST["admin_hangup_enabled"])) {$admin_hangup_enabled=$_POST["admin_hangup_enabled"];}
if (isset($_GET["admin_hijack_enabled"])) {$admin_hijack_enabled=$_GET["admin_hijack_enabled"];}
 elseif (isset($_POST["admin_hijack_enabled"])) {$admin_hijack_enabled=$_POST["admin_hijack_enabled"];}
if (isset($_GET["admin_home_url"]))    {$admin_home_url=$_GET["admin_home_url"];}
 elseif (isset($_POST["admin_home_url"])) {$admin_home_url=$_POST["admin_home_url"];}
if (isset($_GET["admin_monitor_enabled"])) {$admin_monitor_enabled=$_GET["admin_monitor_enabled"];}
 elseif (isset($_POST["admin_monitor_enabled"])) {$admin_monitor_enabled=$_POST["admin_monitor_enabled"];}
if (isset($_GET["AFLogging_enabled"])) {$AFLogging_enabled=$_GET["AFLogging_enabled"];}
 elseif (isset($_POST["AFLogging_enabled"])) {$AFLogging_enabled=$_POST["AFLogging_enabled"];}
if (isset($_GET["after_hours_action"]))    {$after_hours_action=$_GET["after_hours_action"];}
 elseif (isset($_POST["after_hours_action"])) {$after_hours_action=$_POST["after_hours_action"];}
if (isset($_GET["after_hours_exten"]))    {$after_hours_exten=$_GET["after_hours_exten"];}
 elseif (isset($_POST["after_hours_exten"]))  {$after_hours_exten=$_POST["after_hours_exten"];}
if (isset($_GET["after_hours_message_filename"]))   {$after_hours_message_filename=$_GET["after_hours_message_filename"];}
 elseif (isset($_POST["after_hours_message_filename"])) {$after_hours_message_filename=$_POST["after_hours_message_filename"];}
if (isset($_GET["after_hours_voicemail"]))   {$after_hours_voicemail=$_GET["after_hours_voicemail"];}
 elseif (isset($_POST["after_hours_voicemail"])) {$after_hours_voicemail=$_POST["after_hours_voicemail"];}
if (isset($_GET["agent_alert_delay"]))    {$agent_alert_delay=$_GET["agent_alert_delay"];}
 elseif (isset($_POST["agent_alert_delay"]))  {$agent_alert_delay=$_POST["agent_alert_delay"];}
if (isset($_GET["agent_alert_exten"]))    {$agent_alert_exten=$_GET["agent_alert_exten"];}
 elseif (isset($_POST["agent_alert_exten"]))  {$agent_alert_exten=$_POST["agent_alert_exten"];}
if (isset($_GET["agent_choose_ingroups"])) {$agent_choose_ingroups=$_GET["agent_choose_ingroups"];}
 elseif (isset($_POST["agent_choose_ingroups"])) {$agent_choose_ingroups=$_POST["agent_choose_ingroups"];}
if (isset($_GET["agentcall_manual"])) {$agentcall_manual=$_GET["agentcall_manual"];}
 elseif (isset($_POST["agentcall_manual"])) {$agentcall_manual=$_POST["agentcall_manual"];}
if (isset($_GET["agentonly_callbacks"])) {$agentonly_callbacks=$_GET["agentonly_callbacks"];}
 elseif (isset($_POST["agentonly_callbacks"])) {$agentonly_callbacks=$_POST["agentonly_callbacks"];}
if (isset($_GET["agent_pause_codes_active"]))   {$agent_pause_codes_active=$_GET["agent_pause_codes_active"];}
 elseif (isset($_POST["agent_pause_codes_active"])) {$agent_pause_codes_active=$_POST["agent_pause_codes_active"];}
if (isset($_GET["AGI_call_logging_enabled"])) {$AGI_call_logging_enabled=$_GET["AGI_call_logging_enabled"];}
 elseif (isset($_POST["AGI_call_logging_enabled"])) {$AGI_call_logging_enabled=$_POST["AGI_call_logging_enabled"];}
if (isset($_GET["agi_output"])) {$agi_output=$_GET["agi_output"];}
 elseif (isset($_POST["agi_output"])) {$agi_output=$_POST["agi_output"];}
if (isset($_GET["allcalls_delay"])) {$allcalls_delay=$_GET["allcalls_delay"];}
 elseif (isset($_POST["allcalls_delay"])) {$allcalls_delay=$_POST["allcalls_delay"];}
if (isset($_GET["allow_closers"])) {$allow_closers=$_GET["allow_closers"];}
 elseif (isset($_POST["allow_closers"])) {$allow_closers=$_POST["allow_closers"];}
if (isset($_GET["allow_sipsak_messages"]))    {$allow_sipsak_messages=$_GET["allow_sipsak_messages"];}
 elseif (isset($_POST["allow_sipsak_messages"]))  {$allow_sipsak_messages=$_POST["allow_sipsak_messages"];}
if (isset($_GET["alter_custdata_override"]))   {$alter_custdata_override=$_GET["alter_custdata_override"];}
 elseif (isset($_POST["alter_custdata_override"])) {$alter_custdata_override=$_POST["alter_custdata_override"];}
if (isset($_GET["alt_number_dialing"])) {$alt_number_dialing=$_GET["alt_number_dialing"];}
 elseif (isset($_POST["alt_number_dialing"])) {$alt_number_dialing=$_POST["alt_number_dialing"];}
if (isset($_GET["alter_agent_interface_options"])) {$alter_agent_interface_options=$_GET["alter_agent_interface_options"];}
 elseif (isset($_POST["alter_agent_interface_options"])) {$alter_agent_interface_options=$_POST["alter_agent_interface_options"];}
if (isset($_GET["am_message_exten"])) {$am_message_exten=$_GET["am_message_exten"];}
 elseif (isset($_POST["am_message_exten"])) {$am_message_exten=$_POST["am_message_exten"];}
if (isset($_GET["amd_send_to_vmx"])) {$amd_send_to_vmx=$_GET["amd_send_to_vmx"];}
 elseif (isset($_POST["amd_send_to_vmx"])) {$amd_send_to_vmx=$_POST["amd_send_to_vmx"];}
if (isset($_GET["answer_transfer_agent"])) {$answer_transfer_agent=$_GET["answer_transfer_agent"];}
 elseif (isset($_POST["answer_transfer_agent"])) {$answer_transfer_agent=$_POST["answer_transfer_agent"];}
if (isset($_GET["archive_hostname"]))    {$archive_hostname=$_GET["archive_hostname"];}
 elseif (isset($_POST["archive_hostname"])) {$archive_hostname=$_POST["archive_hostname"];}
if (isset($_GET["archive_mix_format"]))    {$archive_mix_format=$_GET["archive_mix_format"];}
 elseif (isset($_POST["archive_mix_format"])) {$archive_mix_format=$_POST["archive_mix_format"];}
if (isset($_GET["archive_password"]))    {$archive_password=$_GET["archive_password"];}
 elseif (isset($_POST["archive_password"])) {$archive_password=$_POST["archive_password"];}
if (isset($_GET["archive_path"]))    {$archive_path=$_GET["archive_path"];}
 elseif (isset($_POST["archive_path"])) {$archive_path=$_POST["archive_path"];}
if (isset($_GET["archive_port"]))    {$archive_port=$_GET["archive_port"];}
 elseif (isset($_POST["archive_port"])) {$archive_port=$_POST["archive_port"];}
if (isset($_GET["archive_transfer_method"]))    {$archive_transfer_method=$_GET["archive_transfer_method"];}
 elseif (isset($_POST["archive_transfer_method"])) {$archive_transfer_method=$_POST["archive_transfer_method"];}
if (isset($_GET["archive_username"]))    {$archive_username=$_GET["archive_username"];}
 elseif (isset($_POST["archive_username"])) {$archive_username=$_POST["archive_username"];}
if (isset($_GET["archive_web_path"]))    {$archive_web_path=$_GET["archive_web_path"];}
 elseif (isset($_POST["archive_web_path"])) {$archive_web_path=$_POST["archive_web_path"];}
if (isset($_GET["ast_admin_access"])) {$ast_admin_access=$_GET["ast_admin_access"];}
 elseif (isset($_POST["ast_admin_access"])) {$ast_admin_access=$_POST["ast_admin_access"];}
if (isset($_GET["ast_delete_phones"])) {$ast_delete_phones=$_GET["ast_delete_phones"];}
 elseif (isset($_POST["ast_delete_phones"])) {$ast_delete_phones=$_POST["ast_delete_phones"];}
if (isset($_GET["asterisk_version"])) {$asterisk_version=$_GET["asterisk_version"];}
 elseif (isset($_POST["asterisk_version"])) {$asterisk_version=$_POST["asterisk_version"];}
if (isset($_GET["ASTmgrSECRET"])) {$ASTmgrSECRET=$_GET["ASTmgrSECRET"];}
 elseif (isset($_POST["ASTmgrSECRET"])) {$ASTmgrSECRET=$_POST["ASTmgrSECRET"];}
if (isset($_GET["ASTmgrUSERNAME"])) {$ASTmgrUSERNAME=$_GET["ASTmgrUSERNAME"];}
 elseif (isset($_POST["ASTmgrUSERNAME"])) {$ASTmgrUSERNAME=$_POST["ASTmgrUSERNAME"];}
if (isset($_GET["ASTmgrUSERNAMElisten"])) {$ASTmgrUSERNAMElisten=$_GET["ASTmgrUSERNAMElisten"];}
 elseif (isset($_POST["ASTmgrUSERNAMElisten"])) {$ASTmgrUSERNAMElisten=$_POST["ASTmgrUSERNAMElisten"];}
if (isset($_GET["ASTmgrUSERNAMEsend"])) {$ASTmgrUSERNAMEsend=$_GET["ASTmgrUSERNAMEsend"];}
 elseif (isset($_POST["ASTmgrUSERNAMEsend"])) {$ASTmgrUSERNAMEsend=$_POST["ASTmgrUSERNAMEsend"];}
if (isset($_GET["ASTmgrUSERNAMEupdate"])) {$ASTmgrUSERNAMEupdate=$_GET["ASTmgrUSERNAMEupdate"];}
 elseif (isset($_POST["ASTmgrUSERNAMEupdate"])) {$ASTmgrUSERNAMEupdate=$_POST["ASTmgrUSERNAMEupdate"];}
if (isset($_GET["attempt_delay"])) {$attempt_delay=$_GET["attempt_delay"];}
 elseif (isset($_POST["attempt_delay"])) {$attempt_delay=$_POST["attempt_delay"];}
if (isset($_GET["attempt_maximum"])) {$attempt_maximum=$_GET["attempt_maximum"];}
 elseif (isset($_POST["attempt_maximum"])) {$attempt_maximum=$_POST["attempt_maximum"];}
if (isset($_GET["auto_alt_dial"]))   {$auto_alt_dial=$_GET["auto_alt_dial"];}
 elseif (isset($_POST["auto_alt_dial"])) {$auto_alt_dial=$_POST["auto_alt_dial"];}
if (isset($_GET["auto_dial_level"])) {$auto_dial_level=$_GET["auto_dial_level"];}
 elseif (isset($_POST["auto_dial_level"])) {$auto_dial_level=$_POST["auto_dial_level"];}
if (isset($_GET["auto_dial_next_number"])) {$auto_dial_next_number=$_GET["auto_dial_next_number"];}
 elseif (isset($_POST["auto_dial_next_number"])) {$auto_dial_next_number=$_POST["auto_dial_next_number"];}
if (isset($_GET["available_only_ratio_tally"])) {$available_only_ratio_tally=$_GET["available_only_ratio_tally"];}
 elseif (isset($_POST["available_only_ratio_tally"])){$available_only_ratio_tally=$_POST["available_only_ratio_tally"];}
if (isset($_GET["balance_trunks_offlimits"])) {$balance_trunks_offlimits=$_GET["balance_trunks_offlimits"];}
 elseif (isset($_POST["balance_trunks_offlimits"])) {$balance_trunks_offlimits=$_POST["balance_trunks_offlimits"];}
if (isset($_GET["billable"]))     {$billable=$_GET["billable"];}
 elseif (isset($_POST["billable"]))   {$billable=$_POST["billable"];}
if (isset($_GET["CALLSdisplay"]))   {$CALLSdisplay=$_GET["CALLSdisplay"];}
 elseif (isset($_POST["CALLSdisplay"])) {$CALLSdisplay=$_POST["CALLSdisplay"];}
if (isset($_GET["call_out_number_group"])) {$call_out_number_group=$_GET["call_out_number_group"];}
 elseif (isset($_POST["call_out_number_group"])) {$call_out_number_group=$_POST["call_out_number_group"];}
if (isset($_GET["call_parking_enabled"])) {$call_parking_enabled=$_GET["call_parking_enabled"];}
 elseif (isset($_POST["call_parking_enabled"])) {$call_parking_enabled=$_POST["call_parking_enabled"];}
if (isset($_GET["call_time_comments"])) {$call_time_comments=$_GET["call_time_comments"];}
 elseif (isset($_POST["call_time_comments"])) {$call_time_comments=$_POST["call_time_comments"];}
if (isset($_GET["call_time_id"])) {$call_time_id=$_GET["call_time_id"];}
 elseif (isset($_POST["call_time_id"])) {$call_time_id=$_POST["call_time_id"];}
if (isset($_GET["call_time_name"])) {$call_time_name=$_GET["call_time_name"];}
 elseif (isset($_POST["call_time_name"])) {$call_time_name=$_POST["call_time_name"];}
if (isset($_GET["CallerID_popup_enabled"])) {$CallerID_popup_enabled=$_GET["CallerID_popup_enabled"];}
 elseif (isset($_POST["CallerID_popup_enabled"])) {$CallerID_popup_enabled=$_POST["CallerID_popup_enabled"];}
if (isset($_GET["campaign_allow_inbound"]))    {$campaign_allow_inbound=$_GET["campaign_allow_inbound"];}
 elseif (isset($_POST["campaign_allow_inbound"])) {$campaign_allow_inbound=$_POST["campaign_allow_inbound"];}
if (isset($_GET["campaign_cid"])) {$campaign_cid=$_GET["campaign_cid"];}
 elseif (isset($_POST["campaign_cid"])) {$campaign_cid=$_POST["campaign_cid"];}
if (isset($_GET["campaign_description"]))   {$campaign_description=$_GET["campaign_description"];}
 elseif (isset($_POST["campaign_description"])) {$campaign_description=$_POST["campaign_description"];}
if (isset($_GET["campaign_detail"])) {$campaign_detail=$_GET["campaign_detail"];}
 elseif (isset($_POST["campaign_detail"])) {$campaign_detail=$_POST["campaign_detail"];}
if (isset($_GET["campaign_id"])) {$campaign_id=$_GET["campaign_id"];}
 elseif (isset($_POST["campaign_id"])) {$campaign_id=$_POST["campaign_id"];}
if (isset($_GET["campaign_name"])) {$campaign_name=$_GET["campaign_name"];}
 elseif (isset($_POST["campaign_name"])) {$campaign_name=$_POST["campaign_name"];}
if (isset($_GET["campaign_rank"]))    {$campaign_rank=$_GET["campaign_rank"];}
 elseif (isset($_POST["campaign_rank"]))  {$campaign_rank=$_POST["campaign_rank"];}
if (isset($_GET["campaign_rec_exten"])) {$campaign_rec_exten=$_GET["campaign_rec_exten"];}
 elseif (isset($_POST["campaign_rec_exten"])) {$campaign_rec_exten=$_POST["campaign_rec_exten"];}
if (isset($_GET["campaign_rec_filename"])) {$campaign_rec_filename=$_GET["campaign_rec_filename"];}
 elseif (isset($_POST["campaign_rec_filename"])) {$campaign_rec_filename=$_POST["campaign_rec_filename"];}
if (isset($_GET["campaign_recording"])) {$campaign_recording=$_GET["campaign_recording"];}
 elseif (isset($_POST["campaign_recording"])) {$campaign_recording=$_POST["campaign_recording"];}
if (isset($_GET["campaigns"]))      {$campaigns=$_GET["campaigns"];}
 elseif (isset($_POST["campaigns"]))    {$campaigns=$_POST["campaigns"];}
if (isset($_GET["campaign_stats_refresh"]))   {$campaign_stats_refresh=$_GET["campaign_stats_refresh"];}
 elseif (isset($_POST["campaign_stats_refresh"])){$campaign_stats_refresh=$_POST["campaign_stats_refresh"];}
if (isset($_GET["campaign_vdad_exten"])) {$campaign_vdad_exten=$_GET["campaign_vdad_exten"];}
 elseif (isset($_POST["campaign_vdad_exten"])) {$campaign_vdad_exten=$_POST["campaign_vdad_exten"];}
if (isset($_GET["category"]))     {$category=$_GET["category"];}
 elseif (isset($_POST["category"]))   {$category=$_POST["category"];}
if (isset($_GET["change_agent_campaign"])) {$change_agent_campaign=$_GET["change_agent_campaign"];}
 elseif (isset($_POST["change_agent_campaign"])) {$change_agent_campaign=$_POST["change_agent_campaign"];}
if (isset($_GET["client_browser"])) {$client_browser=$_GET["client_browser"];}
 elseif (isset($_POST["client_browser"])) {$client_browser=$_POST["client_browser"];}
if (isset($_GET["closer_default_blended"])) {$closer_default_blended=$_GET["closer_default_blended"];}
 elseif (isset($_POST["closer_default_blended"])) {$closer_default_blended=$_POST["closer_default_blended"];}
if (isset($_GET["company"])) {$company=$_GET["company"];}
 elseif (isset($_POST["company"])) {$company=$_POST["company"];}
if (isset($_GET["computer_ip"])) {$computer_ip=$_GET["computer_ip"];}
 elseif (isset($_POST["computer_ip"])) {$computer_ip=$_POST["computer_ip"];}
if (isset($_GET["concurrent_transfers"]))   {$concurrent_transfers=$_GET["concurrent_transfers"];}
 elseif (isset($_POST["concurrent_transfers"])) {$concurrent_transfers=$_POST["concurrent_transfers"];}
if (isset($_GET["conf_exten"])) {$conf_exten=$_GET["conf_exten"];}
 elseif (isset($_POST["conf_exten"])) {$conf_exten=$_POST["conf_exten"];}
if (isset($_GET["conf_on_extension"])) {$conf_on_extension=$_GET["conf_on_extension"];}
 elseif (isset($_POST["conf_on_extension"])) {$conf_on_extension=$_POST["conf_on_extension"];}
if (isset($_GET["conferencing_enabled"])) {$conferencing_enabled=$_GET["conferencing_enabled"];}
 elseif (isset($_POST["conferencing_enabled"])) {$conferencing_enabled=$_POST["conferencing_enabled"];}
if (isset($_GET["CoNfIrM"])) {$CoNfIrM=$_GET["CoNfIrM"];}
 elseif (isset($_POST["CoNfIrM"])) {$CoNfIrM=$_POST["CoNfIrM"];}
if (isset($_GET["ct_default_start"])) {$ct_default_start=$_GET["ct_default_start"];}
 elseif (isset($_POST["ct_default_start"])) {$ct_default_start=$_POST["ct_default_start"];}
if (isset($_GET["ct_default_stop"])) {$ct_default_stop=$_GET["ct_default_stop"];}
 elseif (isset($_POST["ct_default_stop"])) {$ct_default_stop=$_POST["ct_default_stop"];}
if (isset($_GET["ct_friday_start"])) {$ct_friday_start=$_GET["ct_friday_start"];}
 elseif (isset($_POST["ct_friday_start"])) {$ct_friday_start=$_POST["ct_friday_start"];}
if (isset($_GET["ct_friday_stop"])) {$ct_friday_stop=$_GET["ct_friday_stop"];}
 elseif (isset($_POST["ct_friday_stop"])) {$ct_friday_stop=$_POST["ct_friday_stop"];}
if (isset($_GET["ct_monday_start"])) {$ct_monday_start=$_GET["ct_monday_start"];}
 elseif (isset($_POST["ct_monday_start"])) {$ct_monday_start=$_POST["ct_monday_start"];}
if (isset($_GET["ct_monday_stop"])) {$ct_monday_stop=$_GET["ct_monday_stop"];}
 elseif (isset($_POST["ct_monday_stop"])) {$ct_monday_stop=$_POST["ct_monday_stop"];}
if (isset($_GET["ct_saturday_start"])) {$ct_saturday_start=$_GET["ct_saturday_start"];}
 elseif (isset($_POST["ct_saturday_start"])) {$ct_saturday_start=$_POST["ct_saturday_start"];}
if (isset($_GET["ct_saturday_stop"])) {$ct_saturday_stop=$_GET["ct_saturday_stop"];}
 elseif (isset($_POST["ct_saturday_stop"])) {$ct_saturday_stop=$_POST["ct_saturday_stop"];}
if (isset($_GET["ct_sunday_start"])) {$ct_sunday_start=$_GET["ct_sunday_start"];}
 elseif (isset($_POST["ct_sunday_start"])) {$ct_sunday_start=$_POST["ct_sunday_start"];}
if (isset($_GET["ct_sunday_stop"])) {$ct_sunday_stop=$_GET["ct_sunday_stop"];}
 elseif (isset($_POST["ct_sunday_stop"])) {$ct_sunday_stop=$_POST["ct_sunday_stop"];}
if (isset($_GET["ct_thursday_start"])) {$ct_thursday_start=$_GET["ct_thursday_start"];}
 elseif (isset($_POST["ct_thursday_start"])) {$ct_thursday_start=$_POST["ct_thursday_start"];}
if (isset($_GET["ct_thursday_stop"])) {$ct_thursday_stop=$_GET["ct_thursday_stop"];}
 elseif (isset($_POST["ct_thursday_stop"])) {$ct_thursday_stop=$_POST["ct_thursday_stop"];}
if (isset($_GET["ct_tuesday_start"])) {$ct_tuesday_start=$_GET["ct_tuesday_start"];}
 elseif (isset($_POST["ct_tuesday_start"])) {$ct_tuesday_start=$_POST["ct_tuesday_start"];}
if (isset($_GET["ct_tuesday_stop"])) {$ct_tuesday_stop=$_GET["ct_tuesday_stop"];}
 elseif (isset($_POST["ct_tuesday_stop"])) {$ct_tuesday_stop=$_POST["ct_tuesday_stop"];}
if (isset($_GET["ct_wednesday_start"])) {$ct_wednesday_start=$_GET["ct_wednesday_start"];}
 elseif (isset($_POST["ct_wednesday_start"])) {$ct_wednesday_start=$_POST["ct_wednesday_start"];}
if (isset($_GET["ct_wednesday_stop"])) {$ct_wednesday_stop=$_GET["ct_wednesday_stop"];}
 elseif (isset($_POST["ct_wednesday_stop"])) {$ct_wednesday_stop=$_POST["ct_wednesday_stop"];}
if (isset($_GET["DB"]))    {$DB=$_GET["DB"];}
 elseif (isset($_POST["DB"])) {$DB=$_POST["DB"];}
if (isset($_GET["DBX_database"])) {$DBX_database=$_GET["DBX_database"];}
 elseif (isset($_POST["DBX_database"])) {$DBX_database=$_POST["DBX_database"];}
if (isset($_GET["DBX_pass"])) {$DBX_pass=$_GET["DBX_pass"];}
 elseif (isset($_POST["DBX_pass"])) {$DBX_pass=$_POST["DBX_pass"];}
if (isset($_GET["DBX_port"])) {$DBX_port=$_GET["DBX_port"];}
 elseif (isset($_POST["DBX_port"])) {$DBX_port=$_POST["DBX_port"];}
if (isset($_GET["DBX_server"])) {$DBX_server=$_GET["DBX_server"];}
 elseif (isset($_POST["DBX_server"])) {$DBX_server=$_POST["DBX_server"];}
if (isset($_GET["DBX_user"])) {$DBX_user=$_GET["DBX_user"];}
 elseif (isset($_POST["DBX_user"])) {$DBX_user=$_POST["DBX_user"];}
if (isset($_GET["DBY_database"])) {$DBY_database=$_GET["DBY_database"];}
 elseif (isset($_POST["DBY_database"])) {$DBY_database=$_POST["DBY_database"];}
if (isset($_GET["DBY_pass"])) {$DBY_pass=$_GET["DBY_pass"];}
 elseif (isset($_POST["DBY_pass"])) {$DBY_pass=$_POST["DBY_pass"];}
if (isset($_GET["DBY_port"])) {$DBY_port=$_GET["DBY_port"];}
 elseif (isset($_POST["DBY_port"])) {$DBY_port=$_POST["DBY_port"];}
if (isset($_GET["DBY_server"])) {$DBY_server=$_GET["DBY_server"];}
 elseif (isset($_POST["DBY_server"])) {$DBY_server=$_POST["DBY_server"];}
if (isset($_GET["DBY_user"])) {$DBY_user=$_GET["DBY_user"];}
 elseif (isset($_POST["DBY_user"])) {$DBY_user=$_POST["DBY_user"];}
if (isset($_GET["dedicated_trunks"])) {$dedicated_trunks=$_GET["dedicated_trunks"];}
 elseif (isset($_POST["dedicated_trunks"])) {$dedicated_trunks=$_POST["dedicated_trunks"];}
if (isset($_GET["default_xfer_group"]))    {$default_xfer_group=$_GET["default_xfer_group"];}
 elseif (isset($_POST["default_xfer_group"])) {$default_xfer_group=$_POST["default_xfer_group"];}
if (isset($_GET["delete_call_times"])) {$delete_call_times=$_GET["delete_call_times"];}
 elseif (isset($_POST["delete_call_times"])) {$delete_call_times=$_POST["delete_call_times"];}
if (isset($_GET["delete_campaigns"])) {$delete_campaigns=$_GET["delete_campaigns"];}
 elseif (isset($_POST["delete_campaigns"])) {$delete_campaigns=$_POST["delete_campaigns"];}
if (isset($_GET["delete_filters"])) {$delete_filters=$_GET["delete_filters"];}
 elseif (isset($_POST["delete_filters"])) {$delete_filters=$_POST["delete_filters"];}
if (isset($_GET["delete_ingroups"])) {$delete_ingroups=$_GET["delete_ingroups"];}
 elseif (isset($_POST["delete_ingroups"])) {$delete_ingroups=$_POST["delete_ingroups"];}
if (isset($_GET["delete_lists"])) {$delete_lists=$_GET["delete_lists"];}
 elseif (isset($_POST["delete_lists"])) {$delete_lists=$_POST["delete_lists"];}
if (isset($_GET["delete_remote_agents"])) {$delete_remote_agents=$_GET["delete_remote_agents"];}
 elseif (isset($_POST["delete_remote_agents"])) {$delete_remote_agents=$_POST["delete_remote_agents"];}
if (isset($_GET["delete_scripts"])) {$delete_scripts=$_GET["delete_scripts"];}
 elseif (isset($_POST["delete_scripts"])) {$delete_scripts=$_POST["delete_scripts"];}
if (isset($_GET["delete_user_groups"])) {$delete_user_groups=$_GET["delete_user_groups"];}
 elseif (isset($_POST["delete_user_groups"])) {$delete_user_groups=$_POST["delete_user_groups"];}
if (isset($_GET["delete_users"])) {$delete_users=$_GET["delete_users"];}
 elseif (isset($_POST["delete_users"])) {$delete_users=$_POST["delete_users"];}
if (isset($_GET["dial_level_override"]))   {$dial_level_override=$_GET["dial_level_override"];}
 elseif (isset($_POST["dial_level_override"])) {$dial_level_override=$_POST["dial_level_override"];}
if (isset($_GET["dial_method"])) {$dial_method=$_GET["dial_method"];}
 elseif (isset($_POST["dial_method"])) {$dial_method=$_POST["dial_method"];}
if (isset($_GET["dial_prefix"])) {$dial_prefix=$_GET["dial_prefix"];}
 elseif (isset($_POST["dial_prefix"])) {$dial_prefix=$_POST["dial_prefix"];}
if (isset($_GET["dial_status"]))    {$dial_status=$_GET["dial_status"];}
 elseif (isset($_POST["dial_status"]))  {$dial_status=$_POST["dial_status"];}
if (isset($_GET["dial_status_a"])) {$dial_status_a=$_GET["dial_status_a"];}
 elseif (isset($_POST["dial_status_a"])) {$dial_status_a=$_POST["dial_status_a"];}
if (isset($_GET["dial_status_b"])) {$dial_status_b=$_GET["dial_status_b"];}
 elseif (isset($_POST["dial_status_b"])) {$dial_status_b=$_POST["dial_status_b"];}
if (isset($_GET["dial_status_c"])) {$dial_status_c=$_GET["dial_status_c"];}
 elseif (isset($_POST["dial_status_c"])) {$dial_status_c=$_POST["dial_status_c"];}
if (isset($_GET["dial_status_d"])) {$dial_status_d=$_GET["dial_status_d"];}
 elseif (isset($_POST["dial_status_d"])) {$dial_status_d=$_POST["dial_status_d"];}
if (isset($_GET["dial_status_e"])) {$dial_status_e=$_GET["dial_status_e"];}
 elseif (isset($_POST["dial_status_e"])) {$dial_status_e=$_POST["dial_status_e"];}
if (isset($_GET["dial_timeout"])) {$dial_timeout=$_GET["dial_timeout"];}
 elseif (isset($_POST["dial_timeout"])) {$dial_timeout=$_POST["dial_timeout"];}
if (isset($_GET["dialplan_number"])) {$dialplan_number=$_GET["dialplan_number"];}
 elseif (isset($_POST["dialplan_number"])) {$dialplan_number=$_POST["dialplan_number"];}
if (isset($_GET["disable_alter_custdata"]))    {$disable_alter_custdata=$_GET["disable_alter_custdata"];}
 elseif (isset($_POST["disable_alter_custdata"])) {$disable_alter_custdata=$_POST["disable_alter_custdata"];}
if (isset($_GET["drop_call_seconds"])) {$drop_call_seconds=$_GET["drop_call_seconds"];}
 elseif (isset($_POST["drop_call_seconds"])) {$drop_call_seconds=$_POST["drop_call_seconds"];}
if (isset($_GET["drop_exten"])) {$drop_exten=$_GET["drop_exten"];}
 elseif (isset($_POST["drop_exten"])) {$drop_exten=$_POST["drop_exten"];}
if (isset($_GET["drop_message"])) {$drop_message=$_GET["drop_message"];}
 elseif (isset($_POST["drop_message"])) {$drop_message=$_POST["drop_message"];}
if (isset($_GET["dtmf_send_extension"])) {$dtmf_send_extension=$_GET["dtmf_send_extension"];}
 elseif (isset($_POST["dtmf_send_extension"])) {$dtmf_send_extension=$_POST["dtmf_send_extension"];}
if (isset($_GET["enable_agc_xfer_log"]))   {$enable_agc_xfer_log=$_GET["enable_agc_xfer_log"];}
 elseif (isset($_POST["enable_agc_xfer_log"])) {$enable_agc_xfer_log=$_POST["enable_agc_xfer_log"];}
if (isset($_GET["enable_fast_refresh"])) {$enable_fast_refresh=$_GET["enable_fast_refresh"];}
 elseif (isset($_POST["enable_fast_refresh"])) {$enable_fast_refresh=$_POST["enable_fast_refresh"];}
if (isset($_GET["enable_persistant_mysql"])) {$enable_persistant_mysql=$_GET["enable_persistant_mysql"];}
 elseif (isset($_POST["enable_persistant_mysql"])) {$enable_persistant_mysql=$_POST["enable_persistant_mysql"];}
if (isset($_GET["enable_sipsak_messages"]))    {$enable_sipsak_messages=$_GET["enable_sipsak_messages"];}
 elseif (isset($_POST["enable_sipsak_messages"])) {$enable_sipsak_messages=$_POST["enable_sipsak_messages"];}
if (isset($_GET["ext_context"])) {$ext_context=$_GET["ext_context"];}
 elseif (isset($_POST["ext_context"])) {$ext_context=$_POST["ext_context"];}
if (isset($_GET["extension"])) {$extension=$_GET["extension"];}
 elseif (isset($_POST["extension"])) {$extension=$_POST["extension"];}
if (isset($_GET["fast_refresh_rate"])) {$fast_refresh_rate=$_GET["fast_refresh_rate"];}
 elseif (isset($_POST["fast_refresh_rate"])) {$fast_refresh_rate=$_POST["fast_refresh_rate"];}
if (isset($_GET["force_logout"])) {$force_logout=$_GET["force_logout"];}
 elseif (isset($_POST["force_logout"])) {$force_logout=$_POST["force_logout"];}
if (isset($_GET["fronter_display"])) {$fronter_display=$_GET["fronter_display"];}
 elseif (isset($_POST["fronter_display"])) {$fronter_display=$_POST["fronter_display"];}
if (isset($_GET["full_name"])) {$full_name=$_GET["full_name"];}
 elseif (isset($_POST["full_name"])) {$full_name=$_POST["full_name"];}
if (isset($_GET["fullname"])) {$fullname=$_GET["fullname"];}
 elseif (isset($_POST["fullname"])) {$fullname=$_POST["fullname"];}
if (isset($_GET["get_call_launch"])) {$get_call_launch=$_GET["get_call_launch"];}
 elseif (isset($_POST["get_call_launch"])) {$get_call_launch=$_POST["get_call_launch"];}
if (isset($_GET["group_color"])) {$group_color=$_GET["group_color"];}
 elseif (isset($_POST["group_color"])) {$group_color=$_POST["group_color"];}
if (isset($_GET["group_id"])) {$group_id=$_GET["group_id"];}
 elseif (isset($_POST["group_id"])) {$group_id=$_POST["group_id"];}
if (isset($_GET["group_name"])) {$group_name=$_GET["group_name"];}
 elseif (isset($_POST["group_name"])) {$group_name=$_POST["group_name"];}
if (isset($_GET["group_rank"]))     {$group_rank=$_GET["group_rank"];}
 elseif (isset($_POST["group_rank"]))  {$group_rank=$_POST["group_rank"];}
if (isset($_GET["groups"])) {$groups=$_GET["groups"];}
 elseif (isset($_POST["groups"])) {$groups=$_POST["groups"];}
if (isset($_GET["HKstatus"])) {$HKstatus=$_GET["HKstatus"];}
 elseif (isset($_POST["HKstatus"])) {$HKstatus=$_POST["HKstatus"];}
if (isset($_GET["hopper_level"])) {$hopper_level=$_GET["hopper_level"];}
 elseif (isset($_POST["hopper_level"])) {$hopper_level=$_POST["hopper_level"];}
if (isset($_GET["hotkey"])) {$hotkey=$_GET["hotkey"];}
 elseif (isset($_POST["hotkey"])) {$hotkey=$_POST["hotkey"];}
if (isset($_GET["hotkeys_active"])) {$hotkeys_active=$_GET["hotkeys_active"];}
 elseif (isset($_POST["hotkeys_active"])) {$hotkeys_active=$_POST["hotkeys_active"];}
if (isset($_GET["human_answered"]))    {$human_answered=$_GET["human_answered"];}
 elseif (isset($_POST["human_answered"])) {$human_answered=$_POST["human_answered"];}
if (isset($_GET["IAXmonitorLINK"]))    {$IAXmonitorLINK=$_GET["IAXmonitorLINK"];}
 elseif (isset($_POST["IAXmonitorLINK"])) {$IAXmonitorLINK=$_POST["IAXmonitorLINK"];}
if (isset($_GET["install_directory"])) {$install_directory=$_GET["install_directory"];}
 elseif (isset($_POST["install_directory"])) {$install_directory=$_POST["install_directory"];}
if (isset($_GET["lead_filter_comments"])) {$lead_filter_comments=$_GET["lead_filter_comments"];}
 elseif (isset($_POST["lead_filter_comments"])) {$lead_filter_comments=$_POST["lead_filter_comments"];}
if (isset($_GET["lead_filter_id"])) {$lead_filter_id=$_GET["lead_filter_id"];}
 elseif (isset($_POST["lead_filter_id"])) {$lead_filter_id=$_POST["lead_filter_id"];}
if (isset($_GET["lead_filter_name"])) {$lead_filter_name=$_GET["lead_filter_name"];}
 elseif (isset($_POST["lead_filter_name"])) {$lead_filter_name=$_POST["lead_filter_name"];}
if (isset($_GET["lead_filter_sql"])) {$lead_filter_sql=$_GET["lead_filter_sql"];}
 elseif (isset($_POST["lead_filter_sql"])) {$lead_filter_sql=$_POST["lead_filter_sql"];}
if (isset($_GET["lead_id"]))			{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))	{$lead_id=$_POST["lead_id"];}
if (isset($_GET["lead_order"])) {$lead_order=$_GET["lead_order"];}
 elseif (isset($_POST["lead_order"])) {$lead_order=$_POST["lead_order"];}
if (isset($_GET["list_description"]))   {$list_description=$_GET["list_description"];}
 elseif (isset($_POST["list_description"])) {$list_description=$_POST["list_description"];}
if (isset($_GET["list_id"])) {$list_id=$_GET["list_id"];}
 elseif (isset($_POST["list_id"])) {$list_id=$_POST["list_id"];}
if (isset($_GET["list_mix_container"]))    {$list_mix_container=$_GET["list_mix_container"];}
 elseif (isset($_POST["list_mix_container"])) {$list_mix_container=$_POST["list_mix_container"];}
if (isset($_GET["list_name"])) {$list_name=$_GET["list_name"];}
 elseif (isset($_POST["list_name"])) {$list_name=$_POST["list_name"];}
if (isset($_GET["list_order_mix"]))    {$list_order_mix=$_GET["list_order_mix"];}
 elseif (isset($_POST["list_order_mix"])) {$list_order_mix=$_POST["list_order_mix"];}
if (isset($_GET["load_leads"])) {$load_leads=$_GET["load_leads"];}
 elseif (isset($_POST["load_leads"])) {$load_leads=$_POST["load_leads"];}
if (isset($_GET["local_call_time"])) {$local_call_time=$_GET["local_call_time"];}
 elseif (isset($_POST["local_call_time"])) {$local_call_time=$_POST["local_call_time"];}
if (isset($_GET["local_gmt"])) {$local_gmt=$_GET["local_gmt"];}
 elseif (isset($_POST["local_gmt"])) {$local_gmt=$_POST["local_gmt"];}
if (isset($_GET["local_web_callerID_URL"])) {$local_web_callerID_URL=$_GET["local_web_callerID_URL"];}
 elseif (isset($_POST["local_web_callerID_URL"])) {$local_web_callerID_URL=$_POST["local_web_callerID_URL"];}
if (isset($_GET["login"])) {$login=$_GET["login"];}
 elseif (isset($_POST["login"])) {$login=$_POST["login"];}
if (isset($_GET["login_campaign"])) {$login_campaign=$_GET["login_campaign"];}
 elseif (isset($_POST["login_campaign"])) {$login_campaign=$_POST["login_campaign"];}
if (isset($_GET["login_pass"])) {$login_pass=$_GET["login_pass"];}
 elseif (isset($_POST["login_pass"])) {$login_pass=$_POST["login_pass"];}
if (isset($_GET["login_user"])) {$login_user=$_GET["login_user"];}
 elseif (isset($_POST["login_user"])) {$login_user=$_POST["login_user"];}
if (isset($_GET["manual_dial_list_id"]))    {$manual_dial_list_id=$_GET["manual_dial_list_id"];}
 elseif (isset($_POST["manual_dial_list_id"]))  {$manual_dial_list_id=$_POST["manual_dial_list_id"];}
if (isset($_GET["max_vicidial_trunks"])) {$max_vicidial_trunks=$_GET["max_vicidial_trunks"];}
 elseif (isset($_POST["max_vicidial_trunks"])) {$max_vicidial_trunks=$_POST["max_vicidial_trunks"];}
if (isset($_GET["mix_container_item"]))    {$mix_container_item=$_GET["mix_container_item"];}
 elseif (isset($_POST["mix_container_item"])) {$mix_container_item=$_POST["mix_container_item"];}
if (isset($_GET["mix_method"]))     {$mix_method=$_GET["mix_method"];}
 elseif (isset($_POST["mix_method"]))  {$mix_method=$_POST["mix_method"];}
if (isset($_GET["modify_call_times"])) {$modify_call_times=$_GET["modify_call_times"];}
 elseif (isset($_POST["modify_call_times"])) {$modify_call_times=$_POST["modify_call_times"];}
if (isset($_GET["modify_campaigns"]))   {$modify_campaigns=$_GET["modify_campaigns"];}
 elseif (isset($_POST["modify_campaigns"])) {$modify_campaigns=$_POST["modify_campaigns"];}
if (isset($_GET["modify_filters"]))    {$modify_filters=$_GET["modify_filters"];}
 elseif (isset($_POST["modify_filters"])) {$modify_filters=$_POST["modify_filters"];}
if (isset($_GET["modify_ingroups"]))   {$modify_ingroups=$_GET["modify_ingroups"];}
 elseif (isset($_POST["modify_ingroups"])) {$modify_ingroups=$_POST["modify_ingroups"];}
if (isset($_GET["modify_leads"])) {$modify_leads=$_GET["modify_leads"];}
 elseif (isset($_POST["modify_leads"])) {$modify_leads=$_POST["modify_leads"];}
if (isset($_GET["modify_lists"]))    {$modify_lists=$_GET["modify_lists"];}
 elseif (isset($_POST["modify_lists"]))  {$modify_lists=$_POST["modify_lists"];}
if (isset($_GET["monitor_prefix"])) {$monitor_prefix=$_GET["monitor_prefix"];}
 elseif (isset($_POST["monitor_prefix"])) {$monitor_prefix=$_POST["monitor_prefix"];}
if (isset($_GET["modify_remoteagents"]))   {$modify_remoteagents=$_GET["modify_remoteagents"];}
 elseif (isset($_POST["modify_remoteagents"])) {$modify_remoteagents=$_POST["modify_remoteagents"];}
if (isset($_GET["modify_scripts"]))    {$modify_scripts=$_GET["modify_scripts"];}
 elseif (isset($_POST["modify_scripts"])) {$modify_scripts=$_POST["modify_scripts"];}
if (isset($_GET["modify_servers"]))    {$modify_servers=$_GET["modify_servers"];}
 elseif (isset($_POST["modify_servers"])) {$modify_servers=$_POST["modify_servers"];}
if (isset($_GET["modify_usergroups"]))   {$modify_usergroups=$_GET["modify_usergroups"];}
 elseif (isset($_POST["modify_usergroups"])) {$modify_usergroups=$_POST["modify_usergroups"];}
if (isset($_GET["modify_users"]))    {$modify_users=$_GET["modify_users"];}
 elseif (isset($_POST["modify_users"]))  {$modify_users=$_POST["modify_users"];}
if (isset($_GET["moh_context"]))     {$moh_context=$_GET["moh_context"];}
 elseif (isset($_POST["moh_context"]))   {$moh_context=$_POST["moh_context"];}
if (isset($_GET["next_agent_call"])) {$next_agent_call=$_GET["next_agent_call"];}
 elseif (isset($_POST["next_agent_call"])) {$next_agent_call=$_POST["next_agent_call"];}
if (isset($_GET["no_hopper_leads_logins"]))    {$no_hopper_leads_logins=$_GET["no_hopper_leads_logins"];}
 elseif (isset($_POST["no_hopper_leads_logins"])) {$no_hopper_leads_logins=$_POST["no_hopper_leads_logins"];}
if (isset($_GET["number_of_lines"])) {$number_of_lines=$_GET["number_of_lines"];}
 elseif (isset($_POST["number_of_lines"])) {$number_of_lines=$_POST["number_of_lines"];}
if (isset($_GET["old_campaign_id"])) {$old_campaign_id=$_GET["old_campaign_id"];}
 elseif (isset($_POST["old_campaign_id"])) {$old_campaign_id=$_POST["old_campaign_id"];}
if (isset($_GET["old_conf_exten"])) {$old_conf_exten=$_GET["old_conf_exten"];}
 elseif (isset($_POST["old_conf_exten"])) {$old_conf_exten=$_POST["old_conf_exten"];}
if (isset($_GET["old_extension"])) {$old_extension=$_GET["old_extension"];}
 elseif (isset($_POST["old_extension"])) {$old_extension=$_POST["old_extension"];}
if (isset($_GET["old_server_id"])) {$old_server_id=$_GET["old_server_id"];}
 elseif (isset($_POST["old_server_id"])) {$old_server_id=$_POST["old_server_id"];}
if (isset($_GET["old_server_ip"])) {$old_server_ip=$_GET["old_server_ip"];}
 elseif (isset($_POST["old_server_ip"])) {$old_server_ip=$_POST["old_server_ip"];}
if (isset($_GET["OLDuser_group"])) {$OLDuser_group=$_GET["OLDuser_group"];}
 elseif (isset($_POST["OLDuser_group"])) {$OLDuser_group=$_POST["OLDuser_group"];}
if (isset($_GET["omit_phone_code"])) {$omit_phone_code=$_GET["omit_phone_code"];}
 elseif (isset($_POST["omit_phone_code"])) {$omit_phone_code=$_POST["omit_phone_code"];}
if (isset($_GET["onhold_prompt_filename"]))    {$onhold_prompt_filename=$_GET["onhold_prompt_filename"];}
 elseif (isset($_POST["onhold_prompt_filename"])) {$onhold_prompt_filename=$_POST["onhold_prompt_filename"];}
if (isset($_GET["orderby"]))   {$orderby=$_GET["orderby"];}
 elseif (isset($_POST["orderby"])) {$orderby=$_POST["orderby"];}
if (isset($_GET["outbound_cid"])) {$outbound_cid=$_GET["outbound_cid"];}
 elseif (isset($_POST["outbound_cid"])) {$outbound_cid=$_POST["outbound_cid"];}
if (isset($_GET["park_ext"])) {$park_ext=$_GET["park_ext"];}
 elseif (isset($_POST["park_ext"])) {$park_ext=$_POST["park_ext"];}
if (isset($_GET["park_file_name"])) {$park_file_name=$_GET["park_file_name"];}
 elseif (isset($_POST["park_file_name"])) {$park_file_name=$_POST["park_file_name"];}
if (isset($_GET["park_on_extension"])) {$park_on_extension=$_GET["park_on_extension"];}
 elseif (isset($_POST["park_on_extension"])) {$park_on_extension=$_POST["park_on_extension"];}
if (isset($_GET["pass"])) {$pass=$_GET["pass"];}
 elseif (isset($_POST["pass"])) {$pass=$_POST["pass"];}
if (isset($_GET["pause_code"]))     {$pause_code=$_GET["pause_code"];}
 elseif (isset($_POST["pause_code"]))  {$pause_code=$_POST["pause_code"];}
if (isset($_GET["pause_code_name"]))   {$pause_code_name=$_GET["pause_code_name"];}
 elseif (isset($_POST["pause_code_name"])) {$pause_code_name=$_POST["pause_code_name"];}
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["phone_ip"])) {$phone_ip=$_GET["phone_ip"];}
 elseif (isset($_POST["phone_ip"])) {$phone_ip=$_POST["phone_ip"];}
if (isset($_GET["phone_login"])) {$phone_login=$_GET["phone_login"];}
 elseif (isset($_POST["phone_login"])) {$phone_login=$_POST["phone_login"];}
if (isset($_GET["phone_number"])) {$phone_number=$_GET["phone_number"];}
 elseif (isset($_POST["phone_number"])) {$phone_number=$_POST["phone_number"];}
if (isset($_GET["phone_pass"])) {$phone_pass=$_GET["phone_pass"];}
 elseif (isset($_POST["phone_pass"])) {$phone_pass=$_POST["phone_pass"];}
if (isset($_GET["phone_type"])) {$phone_type=$_GET["phone_type"];}
 elseif (isset($_POST["phone_type"])) {$phone_type=$_POST["phone_type"];}
if (isset($_GET["picture"])) {$picture=$_GET["picture"];}
 elseif (isset($_POST["picture"])) {$picture=$_POST["picture"];}
if (isset($_GET["prompt_interval"]))    {$prompt_interval=$_GET["prompt_interval"];}
 elseif (isset($_POST["prompt_interval"]))  {$prompt_interval=$_POST["prompt_interval"];}
if (isset($_GET["protocol"])) {$protocol=$_GET["protocol"];}
 elseif (isset($_POST["protocol"])) {$protocol=$_POST["protocol"];}
if (isset($_GET["qc_server_archive"]))   {$qc_server_archive=$_GET["qc_server_archive"];}
 elseif (isset($_POST["qc_server_archive"])) {$qc_server_archive=$_POST["qc_server_archive"];}
if (isset($_GET["qc_server_active"]))   {$qc_server_active=$_GET["qc_server_active"];}
 elseif (isset($_POST["qc_server_active"])) {$qc_server_active=$_POST["qc_server_active"];}
if (isset($_GET["qc_server_batch_time"]))   {$qc_server_batch_time=$_GET["qc_server_batch_time"];}
 elseif (isset($_POST["qc_server_batch_time"])) {$qc_server_batch_time=$_POST["qc_server_batch_time"];}
if (isset($_GET["qc_server_description"]))   {$qc_server_description=$_GET["qc_server_description"];}
 elseif (isset($_POST["qc_server_description"])) {$qc_server_description=$_POST["qc_server_description"];}
if (isset($_GET["qc_server_home_path"]))   {$qc_server_home_path=$_GET["qc_server_home_path"];}
 elseif (isset($_POST["qc_server_home_path"])) {$qc_server_home_path=$_POST["qc_server_home_path"];}
if (isset($_GET["qc_server_host"]))   {$qc_server_host=$_GET["qc_server_host"];}
 elseif (isset($_POST["qc_server_host"])) {$qc_server_host=$_POST["qc_server_host"];}
if (isset($_GET["qc_server_id"]))   {$qc_server_id=$_GET["qc_server_id"];}
 elseif (isset($_POST["qc_server_id"])) {$qc_server_id=$_POST["qc_server_id"];}
if (isset($_GET["qc_server_location_template"]))   {$qc_server_location_template=$_GET["qc_server_location_template"];}
 elseif (isset($_POST["qc_server_location_template"])) {$qc_server_location_template=$_POST["qc_server_location_template"];}
if (isset($_GET["qc_server_name"]))   {$qc_server_name=$_GET["qc_server_name"];}
 elseif (isset($_POST["qc_server_name"])) {$qc_server_name=$_POST["qc_server_name"];}
if (isset($_GET["qc_server_password"]))   {$qc_server_password=$_GET["qc_server_password"];}
 elseif (isset($_POST["qc_server_password"])) {$qc_server_password=$_POST["qc_server_password"];}
if (isset($_GET["qc_server_transfer_type"]))   {$qc_server_transfer_type=$_GET["qc_server_transfer_type"];}
 elseif (isset($_POST["qc_server_transfer_type"])) {$qc_server_transfer_type=$_POST["qc_server_transfer_type"];}
if (isset($_GET["qc_server_rule_id"]))   {$qc_server_rule_id=$_GET["qc_server_rule_id"];}
 elseif (isset($_POST["qc_server_rule_id"])) {$qc_server_rule_id=$_POST["qc_server_rule_id"];}
if (isset($_GET["qc_server_rule_query"]))   {$qc_server_rule_query=$_GET["qc_server_rule_query"];}
 elseif (isset($_POST["qc_server_rule_query"])) {$qc_server_rule_query=$_POST["qc_server_rule_query"];}
if (isset($_GET["qc_server_transfer_method"]))   {$qc_server_transfer_method=$_GET["qc_server_transfer_method"];}
 elseif (isset($_POST["qc_server_transfer_method"])) {$qc_server_transfer_method=$_POST["qc_server_transfer_method"];}
if (isset($_GET["qc_server_username"]))   {$qc_server_username=$_GET["qc_server_username"];}
 elseif (isset($_POST["qc_server_username"])) {$qc_server_username=$_POST["qc_server_username"];}
if (isset($_GET["QUEUE_ACTION_enabled"])) {$QUEUE_ACTION_enabled=$_GET["QUEUE_ACTION_enabled"];}
 elseif (isset($_POST["QUEUE_ACTION_enabled"])) {$QUEUE_ACTION_enabled=$_POST["QUEUE_ACTION_enabled"];}
if (isset($_GET["recording_exten"])) {$recording_exten=$_GET["recording_exten"];}
 elseif (isset($_POST["recording_exten"])) {$recording_exten=$_POST["recording_exten"];}
if (isset($_GET["remote_agent_id"])) {$remote_agent_id=$_GET["remote_agent_id"];}
 elseif (isset($_POST["remote_agent_id"])) {$remote_agent_id=$_POST["remote_agent_id"];}
if (isset($_GET["reset_hopper"])) {$reset_hopper=$_GET["reset_hopper"];}
 elseif (isset($_POST["reset_hopper"])) {$reset_hopper=$_POST["reset_hopper"];}
if (isset($_GET["reset_list"])) {$reset_list=$_GET["reset_list"];}
 elseif (isset($_POST["reset_list"])) {$reset_list=$_POST["reset_list"];}
if (isset($_GET["RR"]))     {$RR=$_GET["RR"];}
 elseif (isset($_POST["RR"]))  {$RR=$_POST["RR"];}
if (isset($_GET["safe_harbor_exten"])) {$safe_harbor_exten=$_GET["safe_harbor_exten"];}
 elseif (isset($_POST["safe_harbor_exten"])) {$safe_harbor_exten=$_POST["safe_harbor_exten"];}
if (isset($_GET["safe_harbor_message"])) {$safe_harbor_message=$_GET["safe_harbor_message"];}
 elseif (isset($_POST["safe_harbor_message"])) {$safe_harbor_message=$_POST["safe_harbor_message"];}
if (isset($_GET["scheduled_callbacks"])) {$scheduled_callbacks=$_GET["scheduled_callbacks"];}
 elseif (isset($_POST["scheduled_callbacks"])) {$scheduled_callbacks=$_POST["scheduled_callbacks"];}
if (isset($_GET["script_comments"])) {$script_comments=$_GET["script_comments"];}
 elseif (isset($_POST["script_comments"])) {$script_comments=$_POST["script_comments"];}
if (isset($_GET["script_id"])) {$script_id=$_GET["script_id"];}
 elseif (isset($_POST["script_id"])) {$script_id=$_POST["script_id"];}
if (isset($_GET["script_name"])) {$script_name=$_GET["script_name"];}
 elseif (isset($_POST["script_name"])) {$script_name=$_POST["script_name"];}
if (isset($_GET["script_text"])) {$script_text=$_GET["script_text"];}
 elseif (isset($_POST["script_text"])) {$script_text=$_POST["script_text"];}
if (isset($_GET["selectable"])) {$selectable=$_GET["selectable"];}
 elseif (isset($_POST["selectable"])) {$selectable=$_POST["selectable"];}
if (isset($_GET["SERVdisplay"]))   {$SERVdisplay=$_GET["SERVdisplay"];}
 elseif (isset($_POST["SERVdisplay"])) {$SERVdisplay=$_POST["SERVdisplay"];}
if (isset($_GET["server_description"])) {$server_description=$_GET["server_description"];}
 elseif (isset($_POST["server_description"])) {$server_description=$_POST["server_description"];}
if (isset($_GET["server_id"])) {$server_id=$_GET["server_id"];}
 elseif (isset($_POST["server_id"])) {$server_id=$_POST["server_id"];}
if (isset($_GET["server_ip"])) {$server_ip=$_GET["server_ip"];}
 elseif (isset($_POST["server_ip"])) {$server_ip=$_POST["server_ip"];}
if (isset($_GET["stage"])) {$stage=$_GET["stage"];}
 elseif (isset($_POST["stage"])) {$stage=$_POST["stage"];}
if (isset($_GET["SIPmonitorLINK"]))    {$SIPmonitorLINK=$_GET["SIPmonitorLINK"];}
 elseif (isset($_POST["SIPmonitorLINK"])) {$SIPmonitorLINK=$_POST["SIPmonitorLINK"];}
if (isset($_GET["source_campaign_id"]))    {$source_campaign_id=$_GET["source_campaign_id"];}
 elseif (isset($_POST["source_campaign_id"])) {$source_campaign_id=$_POST["source_campaign_id"];}
if (isset($_GET["source_group_id"]))   {$source_group_id=$_GET["source_group_id"];}
 elseif (isset($_POST["source_group_id"])) {$source_group_id=$_POST["source_group_id"];}
if (isset($_GET["source_user_id"]))    {$source_user_id=$_GET["source_user_id"];}
 elseif (isset($_POST["source_user_id"])) {$source_user_id=$_POST["source_user_id"];}
if (isset($_GET["state_call_time_state"])) {$state_call_time_state=$_GET["state_call_time_state"];}
 elseif (isset($_POST["state_call_time_state"])) {$state_call_time_state=$_POST["state_call_time_state"];}
if (isset($_GET["state_rule"])) {$state_rule=$_GET["state_rule"];}
 elseif (isset($_POST["state_rule"])) {$state_rule=$_POST["state_rule"];}
if (isset($_GET["status"])) {$status=$_GET["status"];}
 elseif (isset($_POST["status"])) {$status=$_POST["status"];}
if (isset($_GET["status_name"])) {$status_name=$_GET["status_name"];}
 elseif (isset($_POST["status_name"])) {$status_name=$_POST["status_name"];}
if (isset($_GET["SUB"]))   {$SUB=$_GET["SUB"];}
 elseif (isset($_POST["SUB"])) {$SUB=$_POST["SUB"];}
if (isset($_GET["submit"])) {$submit=$_GET["submit"];}
 elseif (isset($_POST["submit"])) {$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"])) {$SUBMIT=$_GET["SUBMIT"];}
 elseif (isset($_POST["SUBMIT"])) {$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["sys_perf_log"])) {$sys_perf_log=$_GET["sys_perf_log"];}
 elseif (isset($_POST["sys_perf_log"])) {$sys_perf_log=$_POST["sys_perf_log"];}
if (isset($_GET["telnet_host"])) {$telnet_host=$_GET["telnet_host"];}
 elseif (isset($_POST["telnet_host"])) {$telnet_host=$_POST["telnet_host"];}
if (isset($_GET["telnet_port"])) {$telnet_port=$_GET["telnet_port"];}
 elseif (isset($_POST["telnet_port"])) {$telnet_port=$_POST["telnet_port"];}
if (isset($_GET["tovdad_display"]))    {$tovdad_display=$_GET["tovdad_display"];}
 elseif (isset($_POST["tovdad_display"])) {$tovdad_display=$_POST["tovdad_display"];}
if (isset($_GET["trunk_restriction"])) {$trunk_restriction=$_GET["trunk_restriction"];}
 elseif (isset($_POST["trunk_restriction"])) {$trunk_restriction=$_POST["trunk_restriction"];}
if (isset($_GET["UGdisplay"]))   {$UGdisplay=$_GET["UGdisplay"];}
 elseif (isset($_POST["UGdisplay"])) {$UGdisplay=$_POST["UGdisplay"];}
if (isset($_GET["UidORname"]))   {$UidORname=$_GET["UidORname"];}
 elseif (isset($_POST["UidORname"])) {$UidORname=$_POST["UidORname"];}
if (isset($_GET["updater_check_enabled"])) {$updater_check_enabled=$_GET["updater_check_enabled"];}
 elseif (isset($_POST["updater_check_enabled"])) {$updater_check_enabled=$_POST["updater_check_enabled"];}
if (isset($_GET["use_internal_dnc"])) {$use_internal_dnc=$_GET["use_internal_dnc"];}
 elseif (isset($_POST["use_internal_dnc"])) {$use_internal_dnc=$_POST["use_internal_dnc"];}
if (isset($_GET["use_non_latin"]))    {$use_non_latin=$_GET["use_non_latin"];}
 elseif (isset($_POST["use_non_latin"]))  {$use_non_latin=$_POST["use_non_latin"];}
if (isset($_GET["user"])) {$user=$_GET["user"];}
 elseif (isset($_POST["user"])) {$user=$_POST["user"];}
if (isset($_GET["user_group"])) {$user_group=$_GET["user_group"];}
 elseif (isset($_POST["user_group"])) {$user_group=$_POST["user_group"];}
if (isset($_GET["user_level"])) {$user_level=$_GET["user_level"];}
 elseif (isset($_POST["user_level"])) {$user_level=$_POST["user_level"];}
if (isset($_GET["user_start"])) {$user_start=$_GET["user_start"];}
 elseif (isset($_POST["user_start"])) {$user_start=$_POST["user_start"];}
if (isset($_GET["user_switching_enabled"])) {$user_switching_enabled=$_GET["user_switching_enabled"];}
 elseif (isset($_POST["user_switching_enabled"])) {$user_switching_enabled=$_POST["user_switching_enabled"];}
if (isset($_GET["vcl_id"]))      {$vcl_id=$_GET["vcl_id"];}
 elseif (isset($_POST["vcl_id"]))   {$vcl_id=$_POST["vcl_id"];}
if (isset($_GET["vcl_name"]))     {$vcl_name=$_GET["vcl_name"];}
 elseif (isset($_POST["vcl_name"]))   {$vcl_name=$_POST["vcl_name"];}
if (isset($_GET["vd_server_logs"])) {$vd_server_logs=$_GET["vd_server_logs"];}
 elseif (isset($_POST["vd_server_logs"])) {$vd_server_logs=$_POST["vd_server_logs"];}
if (isset($_GET["VDstop_rec_after_each_call"])) {$VDstop_rec_after_each_call=$_GET["VDstop_rec_after_each_call"];}
 elseif (isset($_POST["VDstop_rec_after_each_call"])) {$VDstop_rec_after_each_call=$_POST["VDstop_rec_after_each_call"];}
if (isset($_GET["vendor_id"]))			{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))	{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["vicidial_agent_disable"]))    {$vicidial_agent_disable=$_GET["vicidial_agent_disable"];}
 elseif (isset($_POST["vicidial_agent_disable"])) {$vicidial_agent_disable=$_POST["vicidial_agent_disable"];}
if (isset($_GET["vicidial_balance_active"])) {$vicidial_balance_active=$_GET["vicidial_balance_active"];}
 elseif (isset($_POST["vicidial_balance_active"])) {$vicidial_balance_active=$_POST["vicidial_balance_active"];}
if (isset($_GET["VICIDIAL_park_on_extension"])) {$VICIDIAL_park_on_extension=$_GET["VICIDIAL_park_on_extension"];}
 elseif (isset($_POST["VICIDIAL_park_on_extension"])) {$VICIDIAL_park_on_extension=$_POST["VICIDIAL_park_on_extension"];}
if (isset($_GET["VICIDIAL_park_on_filename"])) {$VICIDIAL_park_on_filename=$_GET["VICIDIAL_park_on_filename"];}
 elseif (isset($_POST["VICIDIAL_park_on_filename"])) {$VICIDIAL_park_on_filename=$_POST["VICIDIAL_park_on_filename"];}
if (isset($_GET["vicidial_recording"])) {$vicidial_recording=$_GET["vicidial_recording"];}
 elseif (isset($_POST["vicidial_recording"])) {$vicidial_recording=$_POST["vicidial_recording"];}
if (isset($_GET["vicidial_recording_override"]))  {$vicidial_recording_override=$_GET["vicidial_recording_override"];}  
  elseif (isset($_POST["vicidial_recording_override"])) {$vicidial_recording_override=$_POST["vicidial_recording_override"];}
if (isset($_GET["vicidial_transfers"])) {$vicidial_transfers=$_GET["vicidial_transfers"];}
 elseif (isset($_POST["vicidial_transfers"])) {$vicidial_transfers=$_POST["vicidial_transfers"];}
if (isset($_GET["VICIDIAL_web_URL"])) {$VICIDIAL_web_URL=$_GET["VICIDIAL_web_URL"];}
 elseif (isset($_POST["VICIDIAL_web_URL"])) {$VICIDIAL_web_URL=$_POST["VICIDIAL_web_URL"];}
if (isset($_GET["view_reports"]))    {$view_reports=$_GET["view_reports"];}
 elseif (isset($_POST["view_reports"]))  {$view_reports=$_POST["view_reports"];}
if (isset($_GET["voicemail_button_enabled"])) {$voicemail_button_enabled=$_GET["voicemail_button_enabled"];}
 elseif (isset($_POST["voicemail_button_enabled"])) {$voicemail_button_enabled=$_POST["voicemail_button_enabled"];}
if (isset($_GET["voicemail_dump_exten"])) {$voicemail_dump_exten=$_GET["voicemail_dump_exten"];}
 elseif (isset($_POST["voicemail_dump_exten"])) {$voicemail_dump_exten=$_POST["voicemail_dump_exten"];}
if (isset($_GET["voicemail_ext"])) {$voicemail_ext=$_GET["voicemail_ext"];}
 elseif (isset($_POST["voicemail_ext"])) {$voicemail_ext=$_POST["voicemail_ext"];}
if (isset($_GET["voicemail_exten"])) {$voicemail_exten=$_GET["voicemail_exten"];}
 elseif (isset($_POST["voicemail_exten"])) {$voicemail_exten=$_POST["voicemail_exten"];}
if (isset($_GET["voicemail_id"])) {$voicemail_id=$_GET["voicemail_id"];}
 elseif (isset($_POST["voicemail_id"])) {$voicemail_id=$_POST["voicemail_id"];}
if (isset($_GET["vsc_id"]))      {$vsc_id=$_GET["vsc_id"];}
 elseif (isset($_POST["vsc_id"]))   {$vsc_id=$_POST["vsc_id"];}
if (isset($_GET["vsc_description"]))   {$vsc_description=$_GET["vsc_description"];}
 elseif (isset($_POST["vsc_description"])) {$vsc_description=$_POST["vsc_description"];}
if (isset($_GET["vsc_name"]))     {$vsc_name=$_GET["vsc_name"];}
 elseif (isset($_POST["vsc_name"]))   {$vsc_name=$_POST["vsc_name"];}
if (isset($_GET["web_form_address"])) {$web_form_address=$_GET["web_form_address"];}
 elseif (isset($_POST["web_form_address"])) {$web_form_address=$_POST["web_form_address"];}
if (isset($_GET["web_form_address2"])) {$web_form_address2=$_GET["web_form_address2"];}
 elseif (isset($_POST["web_form_address2"])) {$web_form_address2=$_POST["web_form_address2"];}
if (isset($_GET["webroot_writable"]))   {$webroot_writable=$_GET["webroot_writable"];}
 elseif (isset($_POST["webroot_writable"])) {$webroot_writable=$_POST["webroot_writable"];}
if (isset($_GET["welcome_message_filename"]))   {$welcome_message_filename=$_GET["welcome_message_filename"];}
 elseif (isset($_POST["welcome_message_filename"])) {$welcome_message_filename=$_POST["welcome_message_filename"];}
if (isset($_GET["wrapup_message"])) {$wrapup_message=$_GET["wrapup_message"];}
 elseif (isset($_POST["wrapup_message"])) {$wrapup_message=$_POST["wrapup_message"];}
if (isset($_GET["wrapup_seconds"])) {$wrapup_seconds=$_GET["wrapup_seconds"];}
 elseif (isset($_POST["wrapup_seconds"])) {$wrapup_seconds=$_POST["wrapup_seconds"];}
if (isset($_GET["xferconf_a_dtmf"])) {$xferconf_a_dtmf=$_GET["xferconf_a_dtmf"];}
 elseif (isset($_POST["xferconf_a_dtmf"])) {$xferconf_a_dtmf=$_POST["xferconf_a_dtmf"];}
if (isset($_GET["xferconf_a_number"])) {$xferconf_a_number=$_GET["xferconf_a_number"];}
 elseif (isset($_POST["xferconf_a_number"])) {$xferconf_a_number=$_POST["xferconf_a_number"];}
if (isset($_GET["xferconf_b_dtmf"])) {$xferconf_b_dtmf=$_GET["xferconf_b_dtmf"];}
 elseif (isset($_POST["xferconf_b_dtmf"])) {$xferconf_b_dtmf=$_POST["xferconf_b_dtmf"];}
if (isset($_GET["xferconf_b_number"])) {$xferconf_b_number=$_GET["xferconf_b_number"];}
 elseif (isset($_POST["xferconf_b_number"])) {$xferconf_b_number=$_POST["xferconf_b_number"];}
if (isset($_GET["XFERgroups"])) {$XFERgroups=$_GET["XFERgroups"];}
 elseif (isset($_POST["XFERgroups"])) {$XFERgroups=$_POST["XFERgroups"];}

//queumetrics
if (isset($_GET["enable_queuemetrics_logging"])) {$enable_queuemetrics_logging=$_GET["enable_queuemetrics_logging"];}
 elseif (isset($_POST["enable_queuemetrics_logging"])) {$enable_queuemetrics_logging=$_POST["enable_queuemetrics_logging"];}

if (isset($_GET["queuemetrics_eq_prepend"]))   {$queuemetrics_eq_prepend=$_GET["queuemetrics_eq_prepend"];}
 elseif (isset($_POST["queuemetrics_eq_prepend"])) {$queuemetrics_eq_prepend=$_POST["queuemetrics_eq_prepend"];}
if (isset($_GET["queuemetrics_dbname"]))   {$queuemetrics_dbname=$_GET["queuemetrics_dbname"];}
 elseif (isset($_POST["queuemetrics_dbname"])) {$queuemetrics_dbname=$_POST["queuemetrics_dbname"];}
if (isset($_GET["queuemetrics_log_id"]))   {$queuemetrics_log_id=$_GET["queuemetrics_log_id"];}
 elseif (isset($_POST["queuemetrics_log_id"])) {$queuemetrics_log_id=$_POST["queuemetrics_log_id"];}
if (isset($_GET["queuemetrics_login"]))    {$queuemetrics_login=$_GET["queuemetrics_login"];}
 elseif (isset($_POST["queuemetrics_login"])) {$queuemetrics_login=$_POST["queuemetrics_login"];}
if (isset($_GET["queuemetrics_pass"]))   {$queuemetrics_pass=$_GET["queuemetrics_pass"];}
 elseif (isset($_POST["queuemetrics_pass"])) {$queuemetrics_pass=$_POST["queuemetrics_pass"];}
if (isset($_GET["queuemetrics_server_ip"]))    {$queuemetrics_server_ip=$_GET["queuemetrics_server_ip"];}
 elseif (isset($_POST["queuemetrics_server_ip"])) {$queuemetrics_server_ip=$_POST["queuemetrics_server_ip"];}
if (isset($_GET["queuemetrics_url"]))   {$queuemetrics_url=$_GET["queuemetrics_url"];}
 elseif (isset($_POST["queuemetrics_url"])) {$queuemetrics_url=$_POST["queuemetrics_url"];}


if (isset($_GET["group"])) {$group=$_GET["group"];}
 elseif (isset($_POST["group"])) {$group=$_POST["group"];}




if (isset($script_id)) {$script_id= strtoupper($script_id);}
if (isset($lead_filter_id)) {$lead_filter_id = strtoupper($lead_filter_id);}

if (strlen($dial_status) > 0) {
	$ADD='28';
	$status = $dial_status;
}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct) {
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$i++;
}
##### END SETTINGS LOOKUP #####
###########################################

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
	$max_vicidial_trunks = ereg_replace("[^0-9]","",$max_vicidial_trunks);
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
	$QUEUE_ACTION_enabled = ereg_replace("[^0-9]","",$QUEUE_ACTION_enabled);
	$recording_exten = ereg_replace("[^0-9]","",$recording_exten);
	$remote_agent_id = ereg_replace("[^0-9]","",$remote_agent_id);
	$safe_harbor_exten = ereg_replace("[^0-9]","",$safe_harbor_exten);
	$telnet_port = ereg_replace("[^0-9]","",$telnet_port);
	$updater_check_enabled = ereg_replace("[^0-9]","",$updater_check_enabled);
	$user_level = ereg_replace("[^0-9]","",$user_level);
	$user_start = ereg_replace("[^0-9]","",$user_start);
	$user_switching_enabled = ereg_replace("[^0-9]","",$user_switching_enabled);
	$VDstop_rec_after_each_call = ereg_replace("[^0-9]","",$VDstop_rec_after_each_call);
	$VICIDIAL_park_on_extension = ereg_replace("[^0-9]","",$VICIDIAL_park_on_extension);
	$vicidial_recording = ereg_replace("[^0-9]","",$vicidial_recording);
	$vicidial_transfers = ereg_replace("[^0-9]","",$vicidial_transfers);
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
	$vicidial_balance_active = ereg_replace("[^NY]","",$vicidial_balance_active);
	$vd_server_logs = ereg_replace("[^NY]","",$vd_server_logs);
	$agent_pause_codes_active = ereg_replace("[^NY]","",$agent_pause_codes_active);
	$campaign_stats_refresh = ereg_replace("[^NY]","",$campaign_stats_refresh);
	$disable_alter_custdata = ereg_replace("[^NY]","",$disable_alter_custdata);
	$no_hopper_leads_logins = ereg_replace("[^NY]","",$no_hopper_leads_logins);
	$human_answered = ereg_replace("[^NY]","",$human_answered);
	$tovdad_display = ereg_replace("[^NY]","",$tovdad_display);
	$campaign_allow_inbound = ereg_replace("[^NY]","",$campaign_allow_inbound);
	
	### ALPHA-NUMERIC ONLY ###
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
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
	$vicidial_recording_override = ereg_replace("[^0-9a-zA-Z]","",$vicidial_recording_override);
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
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$protocol = ereg_replace("[^-\_0-9a-zA-Z]","",$protocol);
	$server_id = ereg_replace("[^-\_0-9a-zA-Z]","",$server_id);
	$stage = ereg_replace("[^-\_0-9a-zA-Z]","",$stage);
	$state_rule = ereg_replace("[^-\_0-9a-zA-Z]","",$state_rule);
	$status = ereg_replace("[^-\_0-9a-zA-Z]","",$status);
	$trunk_restriction = ereg_replace("[^-\_0-9a-zA-Z]","",$trunk_restriction);
	$user = ereg_replace("[^-\_0-9a-zA-Z]","",$user);
	$user_group = ereg_replace("[^-\_0-9a-zA-Z]","",$user_group);
	$VICIDIAL_park_on_filename = ereg_replace("[^-\_0-9a-zA-Z]","",$VICIDIAL_park_on_filename);
	$auto_alt_dial = ereg_replace("[^-\_0-9a-zA-Z]","",$auto_alt_dial);
	$dial_status = ereg_replace("[^-\_0-9a-zA-Z]","",$dial_status);
	$queuemetrics_eq_prepend = ereg_replace("[^-\_0-9a-zA-Z]","",$queuemetrics_eq_prepend);
	$vicidial_agent_disable = ereg_replace("[^-\_0-9a-zA-Z]","",$vicidial_agent_disable);
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


# AST GUI database administration
# admin.php
# 
# CHANGELOG:
# 50315-1110 - Added Custom Campaign Statuses
# 50317-1438 - Added Fronter Display var to inbound groups
# 50322-1355 - Added custom callerID per campaign
# 50517-1356 - Added user_groups sections and user_group to vicidial_users
# 50517-1440 - Added ability to logout (must click OK with empty user/pass)
# 50602-1622 - Added lead loader pages to load new files into vicidial_list
# 50620-1351 - Added custom vdad transfer AGI extension per campaign
# 50810-1414 - modified in groups to kick out spaces and dashes
# 50908-2136 - Added Custom Campaign HotKeys
# 50914-0950 - Fixed user search by user_group
# 50926-1358 - Modified to allow for language translation
# 50926-1615 - Added WeBRooTWritablE write controls
# 51020-1008 - Added editable web address and park ext - NEW dial campaigns
# 51020-1056 - Added fields and help for campaign recording control
# 51123-1335 - Altered code to function in php globals=off
# 51208-1038 - Added user_level changes, function controls and default user phones
# 51208-1556 - Added deletion of users/lists/campaigns/in groups/remote agents
# 51213-1706 - Added add/delete/modify vicidial scripts
# 51214-1737 - Added preview of vicidial script in popup window
# 51219-1225 - Added campaign and ingroups script selector and get_call_launch field
# 51222-1055 - Added am_message_exten to campaigns to allow for AM Message button
# 51222-1125 - Fixed new vicidial_campaigns default values not being assigned bug
# 51222-1156 - Added LOG OUT ALL AGENTS ON THIS CAMPAIGN button to campaign screen
# 60204-0659 - Fixed hopper reset bug
# 60207-1413 - Added AMD send to voicemail extension and xfer-conf dtmf presets
# 60213-1100 - Added several vicidial_users permissions fields
# 60215-1319 - Added On-hold CallBacks display and links
# 60227-1226 - Fixed vicidial_inbound_groups insert bug
# 60413-1308 - Fixed list display to have 1 row/status: count and time zone tables
#            - Added status name in selected dial statuses in campaign screen
# 60417-1416 - Added vicidial_lead_filters sections
#            - Changed the header links to color-coded sectional with sublinks below
#            - Added filter name and script name to campaign and in-group modify sections
#            - Added callback and alt dial options to campaigns section
#            - Added callback, alt dial and other options to users section
# 60419-1628 - Alter Callbacks display to include status and LIVE listings, reordered
# 60421-1441 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60425-2355 - Added agent options to vicidial_users, reformatted user page
# 60502-1627 - Added drop_call_seconds and safe_harbor_ fields to campaign screen
# 60503-1228 - Added drop_call_seconds and drop_ fields to inbound groups screen
# 60505-1117 - Added initial framework for new local_call_times tables and definitions
# 60506-1033 - More revisions to the local_call_time section
# 60508-1354 - Finished call_times and state_call_times sections
#            - Added modify/delete options for call_times
# 60509-1311 - Functionalize campaign dialable leads calculation
#            - Change state_call_times selection from call_times to only allow one per state
#            - Added dialable leads count popup to campaign screen if auto-calc is disabled
#            - Added test dialable leads count popup to filter screen 
# 60510-1050 - Added Wrapup seconds and Wrapup message to campaigns screen
# 60608-1401 - Added allowable inbound_groups checkboxes to CLOSER campaign detail screen
# 60609-1051 - Added add-to-dnc in LISTS section
# 60613-1415 - Added lead recycling options to campaign detail screen
# 60619-1523 - Added variable filtering to eliminate SQL injection attack threat
# 60622-1216 - Fixed HotKey addition form issues and variable filtering
# 60623-1159 - Fixed Scheduled Callbacks over-filtering bug and filter_sql bug
# 60808-1147 - Changed filtering for and added instructions for consutative transfers
# 60816-1552 - Added allcalls_delay start delay for recordings in vicidial.php
# 60817-2226 - Fixed bug that would not allow lead recycling of non-selectable statuses
# 60821-1543 - Added option to Omit Phone Code while dialing in vicidial
# 60821-1625 - Added ALLFORCE recording option for campaign_recording
# 60823-1154 - Added fields for adaptive dialing
# 60824-1326 - Added adaptive_latest_target_gmt for ADAPT_TAPERED dial method
# 60825-1205 - Added adaptive_intensity for ADAPT_ dial methods
# 60828-1019 - Changed adaptive_latest_target_gmt to adaptive_latest_server_time
# 60828-1115 - Added adaptive_dl_diff_target and changed intensity dropdown
# 60927-1246 - Added astguiclient/admin.php functions under SERVERS tab
# 61002-1402 - Added fields for vicidial balance trunk controls
# 61003-1123 - Added functions for vicidial_server_trunks records
# 61109-1022 - Added Emergency VDAC Jam Clear function to Campaign Detail screen
# 61110-1502 - Add ability to select NONE in dial statuses, new list_id must not be < 100
# 61122-1228 - Added user group campaign restrictions
# 61122-1535 - Changed script_text to unfiltered and added more variables to SCRIPTS
# 61129-1028 - Added headers to Users and Phones with clickable order-by titles
# 70108-1405 - Added ADAPT OVERRIDE to allow for forced dial_level changes in ADAPT dial methods
#            - Screen width definable at top of script, merged server_stats into this script
# 70109-1638 - Added ALTPH2 and ADDR3 hotkey options for alt number dialing with HotKeys
# 70109-1716 - Added concurrent_transfers option to vicidial_campaigns
# 70115-1152 - Aded (CLOSER|BLEND|INBND|_C$|_B$|_I$) options for CLOSER-type campaigns
# 70115-1532 - Added auto_alt_dial field to campaign screen for auto-dialing of alt numbers
# 70116-1200 - Added auto_alt_dial_status functionality to campaign screen
# 70117-1235 - Added header formatting variables at top of script
#            - Moved Call Times and Phones/Server functions to Admin section
# 70118-1706 - Added new user group displays and links
# 70123-1519 - Added user permission settings for all sections
# 70124-1346 - Fixed spelling errors and formatting consistency
# 70202-1120 - Added agent_pause_codes section to campaigns
# 70205-1204 - Added memo, last dialed, timestamp and stats-refresh fields to vicidial_campaigns/lists
# 70206-1323 - Added user setting for vicidial_recording_override
# 70212-1412 - Added system settings section
# 70214-1226 - Added QueueMetrics Log ID field to system settings section
# 70219-1102 - Changed campaign dial statuses to be one string allowing for high limit
# 70223-0957 - Added queuemetrics_eq_prepend for custom ENTERQUEUE prepending of a field
# 70302-1111 - Fixed small bug in dialable leads calculation
# 70314-1133 - Added insert selection on script forms
# 70319-1423 - Added Alter Customer Data and agent disable display functions
# 70319-1625 - Added option to allow agents to login to outbound campaigns with no leads in the hopper
# 70322-1455 - Added sipsak messages parameters
# 70402-1157 - Added HOME link and entry to system_settings table, added QM link on reports section
# 70516-1628 - Started reformatting campaigns to use submenus to break up options
# 70529-1653 - Added help for list mix
# 70530-1354 - Added human_answered field to statuses, added system status modification
# 70530-1714 - Added lists for all campaign subsections
# 70531-1631 - Development on List mix admin interface
# 70601-1629 - More development on List mix admin interface, formatting, and added some javascript
# 70602-1300 - More development on List mix admin interface, more javascript
# 70608-1459 - Added option to set LIVE Callbacks to INACTIVE after one month
# 70612-1451 - Added Callback INACTIVE link for after one week, sort by user/group/entrydate
# 70614-0231 - Added Status Categories, ability to Modify Statuses, moved system statuses to sub-section
# 70623-1008 - List Mix section now allows modification of list mix entries
# 70629-1721 - List Mix section adding and removing of list entries active
# 70706-1636 - List Mix section cleanup and more error-checking
# 70908-0941 - Added agc logile enable system_settings
# 71020-1934 - Added inbound groups options: on-hold music, messages, call_times
# 71022-1343 - Added inbound group ranks for users
# 71029-1710 - Added option for campaign to be inbound and/or blended with no restrictions on the campaign_id name
#            - Added 5th NEW and 6th NEW to the dial order options
# 71030-2010 - Added Manual Dial List ID field to campaigns table
# 71103-2207 - Added inbound_group_rank and fewest_calls to the inbound groups call order options
# 71113-1521 - Added campaign_rank to agent options
#            - Added ability to Copy a campaign's setting to a new campaign
# 71113-2225 - Added ability to copy user and in-group settings to new users and in-groups
# 71116-0942 - Added campaign_rank and fewest_calls as methods for agent call routing
# 71122-1135 - Added default transfer group for campaigns and inbound groups
# 71125-1751 - Added allowable transfer groups to campaign detail screen
# 80330-0814 - Added CBHOLD block on selecting, added user stats/status links
# 80424-0442 - Added non_latin system_settings lookup at top to override dbconnect setting
# 
# 90201-1001 - Branched to OSDial
#
# 90202-1108 - Moved AST_timeonVDADallSUMMARY, AST_timeonVDADall, admin_search_lead &
#			new_listloader_superL into admin.php
#			into admin.php. 
#			Complete upgrade of interface, matched with vicidial.php
#			Added admin_config.inc & styles.css.
#			Added realtime clock
#			Cleaned up indentations, font size and appearance in several sections
#			Changed logout to point back to welcome screen
#			Resized to a 1024 wide screen
#			Changed to new version (from 2.0.4-121)
#
# make sure you have added a user to the vicidial_users MySQL table with at least user_level 8 to access this page the first time

$admin_version = '2.1.0';
$build = '80424-0442/90102';

$STARTtime = date("U");
$SQLdate = date("Y-m-d H:i:s");
$MT[0]='';
$US='_';

$month_old = mktime(0, 0, 0, date("m")-1, date("d"),  date("Y"));
$past_month_date = date("Y-m-d H:i:s",$month_old);
$week_old = mktime(0, 0, 0, date("m"), date("d")-7,  date("Y"));
$past_week_date = date("Y-m-d H:i:s",$week_old);

if ($force_logout) {
	if( (strlen($PHP_AUTH_USER)>0) or (strlen($PHP_AUTH_PW)>0) ) {
		Header("WWW-Authenticate: Basic realm=\"OSDial-Administrator\"");
		Header("HTTP/1.0 401 Unauthorized");
	}
	#echo "<br><br><br><center>You have now logged out. Loading Login screen...</center>\n";
	echo "<script language=\"javascript\">\n";
	echo "window.location = '/';\n";
	echo "</script>\n";
	exit;
}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7;";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0)
	{$fp = fopen ("./project_auth_entries.txt", "a");}

$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or ($auth<1))
	{
    Header("WWW-Authenticate: Basic realm=\"OSDial-Administrator\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT * from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGfullname				=$row[3];
			$LOGuser_level				=$row[4];
			$LOGuser_group				=$row[5];
			$LOGdelete_users			=$row[8];
			$LOGdelete_user_groups		=$row[9];
			$LOGdelete_lists			=$row[10];
			$LOGdelete_campaigns		=$row[11];
			$LOGdelete_ingroups			=$row[12];
			$LOGdelete_remote_agents	=$row[13];
			$LOGload_leads				=$row[14];
			$LOGcampaign_detail			=$row[15];
			$LOGast_admin_access		=$row[16];
			$LOGast_delete_phones		=$row[17];
			$LOGdelete_scripts			=$row[18];
			$LOGdelete_filters			=$row[29];
			$LOGalter_agent_interface	=$row[30];
			$LOGdelete_call_times		=$row[32];
			$LOGmodify_call_times		=$row[33];
			$LOGmodify_users			=$row[34];
			$LOGmodify_campaigns		=$row[35];
			$LOGmodify_lists			=$row[36];
			$LOGmodify_scripts			=$row[37];
			$LOGmodify_filters			=$row[38];
			$LOGmodify_ingroups			=$row[39];
			$LOGmodify_usergroups		=$row[40];
			$LOGmodify_remoteagents		=$row[41];
			$LOGmodify_servers			=$row[42];
			$LOGview_reports			=$row[43];

			$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$LOGuser_group';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGallowed_campaigns		=$row[0];

		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "OSDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "OSDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}

######################################################################################################
######################################################################################################
#######   Header settings
######################################################################################################
######################################################################################################


header ("Content-type: text/html; charset=utf-8");
echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
if ($ADD==13) {
	if (!isset($RR)) { $RR=4; }
	if ($RR <1) { $RR=4; }
	echo "<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?ADD=13&RR=$RR&DB=$DB&adastats=$adastats\">\n";
}
if ($ADD==14) {
	if (!isset($RR)) { $RR=4; }
	if ($RR <1) { $RR=4; }
	echo "<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?ADD=14&RR=$RR&DB=$DB&group=$group&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">\n";
}
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\">\n";
echo "<title>OSDial Administrator: ";

if (!isset($ADD))   {$ADD=0;}

if ($ADD=="1")			{$hh='users';		echo "Add New User";}
if ($ADD=="1A")		{$hh='users';		echo "Copy User";}
if ($ADD==11)			{$hh='campaigns';	$sh='basic';	echo "Add New Campaign";}
if ($ADD==12)			{$hh='campaigns';	$sh='basic';	echo "Copy Campaign";}
if ($ADD==13)			{$hh='campaigns';	$sh='basic';	echo "Real Time Summary Campaign";}
if ($ADD==14)			{$hh='campaigns';	$sh='basic';	echo "Real Time Detail Campaign";}
if ($ADD==111)			{$hh='lists';		echo "Add New List";}
if ($ADD==112)			{$hh='lists';		$sh='basic';	echo "Search For A Lead";}
if ($ADD==121)			{$hh='lists';		echo "Add New DNC";}
if ($ADD==122)			{$hh='lists';		echo "Load New Leads";}
if ($ADD==1111)		{$hh='ingroups';	echo "Add New In-Group";}
if ($ADD==1211)		{$hh='ingroups';	echo "Copy In-Group";}
if ($ADD==11111)		{$hh='remoteagent';	echo "Add New Remote Agents";}
if ($ADD==111111)		{$hh='usergroups';	echo "Add New Users Group";}
if ($ADD==1111111)		{$hh='scripts';		echo "Add New Script";}
if ($ADD==11111111)		{$hh='filters';		echo "Add New Filter";}
if ($ADD==111111111)	{$hh='admin';	$sh='times';	echo "Add New Call Time";}
if ($ADD==1111111111)	{$hh='admin';	$sh='times';	echo "Add New State Call Time";}
if ($ADD==11111111111)	{$hh='admin';	$sh='phones';	echo "ADD NEW PHONE";}
if ($ADD==111111111111)	{$hh='admin';	$sh='server';	echo "ADD NEW SERVER";}
if ($ADD==1111111111111)	{$hh='admin';	$sh='conference';	echo "ADD NEW CONFERENCE";}
if ($ADD==11111111111111)	{$hh='admin';	$sh='conference';	echo "ADD NEW OSDial CONFERENCE";}
if ($ADD=="2")			{$hh='users';		echo "New User Addition";}
if ($ADD=="2A")		{$hh='users';		echo "New Copied User Addition";}
if ($ADD==20)			{$hh='campaigns';	$sh='basic';	echo "New Copied Campaign Addition";}
if ($ADD==21)			{$hh='campaigns';	$sh='basic';	echo "New Campaign Addition";}
if ($ADD==22)			{$hh='campaigns';	$sh='status';	echo "New Campaign Status Addition";}
if ($ADD==23)			{$hh='campaigns';	$sh='hotkey';	echo "New Campaign HotKey Addition";}
if ($ADD==25)			{$hh='campaigns';	$sh='recycle';	echo "New Campaign Lead Recycle Addition";}
if ($ADD==26)			{$hh='campaigns';	$sh='autoalt';	echo "New Auto Alt Dial Status";}
if ($ADD==27)			{$hh='campaigns';	$sh='pause';	echo "New Agent Pause Code";}
if ($ADD==28)			{$hh='campaigns';	$sh='dialstat';	echo "Campaign Dial Status Added";}
if ($ADD==29)			{$hh='campaigns';	$sh='listmix';	echo "Campaign List Mix Added";}
if ($ADD==211)			{$hh='lists';		echo "New List Addition";}
if ($ADD==2111)		{$hh='ingroups';	echo "New In-Group Addition";}
if ($ADD==2011)		{$hh='ingroups';	echo "New Copied In-Group Addition";}
if ($ADD==21111)		{$hh='remoteagent';	echo "New Remote Agents Addition";}
if ($ADD==211111)		{$hh='usergroups';	echo "New Users Group Addition";}
if ($ADD==2111111)		{$hh='scripts';		echo "New Script Addition";}
if ($ADD==21111111)		{$hh='filters';		echo "New Filter Addition";}
if ($ADD==211111111)	{$hh='admin';	$sh='times';	echo "New Call Time Addition";}
if ($ADD==2111111111)	{$hh='admin';	$sh='times';	echo "New State Call Time Addition";}
if ($ADD==21111111111)	{$hh='admin';	$sh='phones';	echo "ADDING NEW PHONE";}
if ($ADD==211111111111)	{$hh='admin';	$sh='server';	echo "ADDING NEW SERVER";}
if ($ADD==221111111111)	{$hh='admin';	$sh='server';	echo "ADDING NEW SERVER OSDial TRUNK RECORD";}
if ($ADD==2111111111111)	{$hh='admin';	$sh='conference';	echo "ADDING NEW CONFERENCE";}
if ($ADD==21111111111111)	{$hh='admin';	$sh='conference';	echo "ADDING NEW OSDial CONFERENCE";}
if ($ADD==221111111111111)	{$hh='admin';	$sh='status';	echo "ADDING OSDial SYSTEM STATUSES";}
if ($ADD==231111111111111)	{$hh='admin';	$sh='status';	echo "ADDING OSDial STATUS CATEGORY";}
if ($ADD==3)			{$hh='users';		echo "Modify User";}
if ($ADD==30)			{$hh='campaigns';	echo "Campaign Not Allowed";}
if ($ADD==31)			
	{
	$hh='campaigns';	$sh='detail';	echo "Modify Campaign - Detail - $campaign_id";
	if ($SUB==22)	{echo " - Statuses";}
	if ($SUB==23)	{echo " - HotKeys";}
	if ($SUB==25)	{echo " - Lead Recycle Entries";}
	if ($SUB==26)	{echo " - Auto Alt Dial Statuses";}
	if ($SUB==27)	{echo " - Agent Pause Codes";}
	if ($SUB==29)	{echo " - List Mixes";}
	}
if ($ADD==32)			{$hh='campaigns';	$sh='status';	echo "Campaign Statuses";}
if ($ADD==33)			{$hh='campaigns';	$sh='hotkey';	echo "Campaign HotKeys";}
if ($ADD==34)			{$hh='campaigns';	$sh='basic';	echo "Modify Campaign - Basic View";}
if ($ADD==35)			{$hh='campaigns';	$sh='recycle';	echo "Campaign Lead Recycle Entries";}
if ($ADD==36)			{$hh='campaigns';	$sh='autoalt';	echo "Campaign Auto Alt Dial Statuses";}
if ($ADD==37)			{$hh='campaigns';	$sh='pause';	echo "Campaign Agent Pause Codes";}
if ($ADD==38)			{$hh='campaigns';	$sh='dialstat';	echo "Campaign Dial Statuses";}
if ($ADD==39)			{$hh='campaigns';	$sh='listmix';	echo "Campaign List Mixes";}
if ($ADD==311)			{$hh='lists';		echo "Modify List";}
if ($ADD==3111)			{$hh='ingroups';	echo "Modify In-Group";}
if ($ADD==31111)		{$hh='remoteagent';	echo "Modify Remote Agents";}
if ($ADD==311111)		{$hh='usergroups';	echo "Modify Users Groups";}
if ($ADD==3111111)		{$hh='scripts';		echo "Modify Script";}
if ($ADD==31111111)		{$hh='filters';		echo "Modify Filter";}
if ($ADD==311111111)	{$hh='admin';	$sh='times';	echo "Modify Call Time";}
if ($ADD==321111111)	{$hh='admin';	$sh='times';	echo "Modify Call Time State Definitions List";}
if ($ADD==3111111111)	{$hh='admin';	$sh='times';	echo "Modify State Call Time";}
if ($ADD==31111111111)	{$hh='admin';	$sh='phones';	echo "MODIFY PHONE";}
if ($ADD==311111111111)	{$hh='admin';	$sh='server';	echo "MODIFY SERVER";}
if ($ADD==3111111111111)	{$hh='admin';	$sh='conference';	echo "MODIFY CONFERENCE";}
if ($ADD==31111111111111)	{$hh='admin';	$sh='conference';	echo "MODIFY VICIDIAL CONFERENCE";}
if ($ADD==311111111111111)	{$hh='admin';	$sh='settings';	echo "MODIFY OSDial SYSTEM SETTINGS";}
if ($ADD==321111111111111)	{$hh='admin';	$sh='status';	echo "MODIFY OSDial SYSTEM STATUSES";}
if ($ADD==331111111111111)	{$hh='admin';	$sh='status';	echo "MODIFY OSDial STATUS CATEGORY";}
if ($ADD==399111111111111)	{$hh='admin';	$sh='server';	echo "MODIFY ARCHIVE SERVER SETTINGS";}
if ($ADD==399211111111111)	{$hh='admin';	$sh='server';	echo "MODIFY QC SERVER SETTINGS";}
if ($ADD=="4A")			{$hh='users';		echo "Modify User - Admin";}
if ($ADD=="4B")			{$hh='users';		echo "Modify User - Admin";}
if ($ADD==4)			{$hh='users';		echo "Modify User";}
if ($ADD==41)			{$hh='campaigns';	$sh='detail';	echo "Modify Campaign";}
if ($ADD==42)			{$hh='campaigns';	$sh='status';	echo "Modify Campaign Status";}
if ($ADD==43)			{$hh='campaigns';	$sh='hotkey';	echo "Modify Campaign HotKey";}
if ($ADD==44)			{$hh='campaigns';	$sh='basic';	echo "Modify Campaign - Basic View";}
if ($ADD==45)			{$hh='campaigns';	$sh='recycle';	echo "Modify Campaign Lead Recycle";}
if ($ADD==47)			{$hh='campaigns';	$sh='pause';	echo "Modify Agent Pause Code";}
if ($ADD==49)			{$hh='campaigns';	$sh='listmix';	echo "Modify Campaign List Mix";}
if ($ADD==411)			{$hh='lists';		echo "Modify List";}
if ($ADD==4111)			{$hh='ingroups';	echo "Modify In-Group";}
if ($ADD==41111)		{$hh='remoteagent';	echo "Modify Remote Agents";}
if ($ADD==411111)		{$hh='usergroups';	echo "Modify Users Groups";}
if ($ADD==4111111)		{$hh='scripts';		echo "Modify Script";}
if ($ADD==41111111)		{$hh='filters';		echo "Modify Filter";}
if ($ADD==411111111)	{$hh='admin';	$sh='times';	echo "Modify Call Time";}
if ($ADD==4111111111)	{$hh='admin';	$sh='times';	echo "Modify State Call Time";}
if ($ADD==41111111111)	{$hh='admin';	$sh='phones';	echo "MODIFY PHONE";}
if ($ADD==411111111111)	{$hh='admin';	$sh='server';	echo "MODIFY SERVER";}
if ($ADD==421111111111)	{$hh='admin';	$sh='server';	echo "MODIFY SERVER OSDial TRUNK RECORD";}
if ($ADD==4111111111111)	{$hh='admin';	$sh='conference';	echo "MODIFY CONFERENCE";}
if ($ADD==41111111111111)	{$hh='admin';	$sh='conference';	echo "MODIFY OSDial CONFERENCE";}
if ($ADD==411111111111111)	{$hh='admin';	$sh='settings';	echo "MODIFY OSDial SYSTEM SETTINGS";}
if ($ADD==421111111111111)	{$hh='admin';	$sh='status';	echo "MODIFY OSDial SYSTEM STATUSES";}
if ($ADD==431111111111111)	{$hh='admin';	$sh='status';	echo "MODIFY OSDial STATUS CATEGORIES";}
if ($ADD==499111111111111)	{$hh='admin';	$sh='server';	echo "MODIFY ARCHIVE SERVER SETTINGS";}
if ($ADD==499211111111111)	{$hh='admin';	$sh='server';	echo "MODIFY QC SERVER SETTINGS";}
if ($ADD==5)			{$hh='users';		echo "Delete User";}
if ($ADD==51)			{$hh='campaigns';	$sh='detail';	echo "Delete Campaign";}
if ($ADD==52)			{$hh='campaigns';	$sh='detail';	echo "Logout Agents";}
if ($ADD==53)			{$hh='campaigns';	$sh='detail';	echo "Emergency VDAC Jam Clear";}
if ($ADD==511)			{$hh='lists';		echo "Delete List";}
if ($ADD==5111)			{$hh='ingroups';	echo "Delete In-Group";}
if ($ADD==51111)		{$hh='remoteagent';	echo "Delete Remote Agents";}
if ($ADD==511111)		{$hh='usergroups';	echo "Delete Users Group";}
if ($ADD==5111111)		{$hh='scripts';		echo "Delete Script";}
if ($ADD==51111111)		{$hh='filters';		echo "Delete Filter";}
if ($ADD==511111111)	{$hh='admin';	$sh='times';	echo "Delete Call Time";}
if ($ADD==5111111111)	{$hh='admin';	$sh='times';	echo "Delete State Call Time";}
if ($ADD==51111111111)	{$hh='admin';	$sh='phones';	echo "DELETE PHONE";}
if ($ADD==511111111111)	{$hh='admin';	$sh='server';	echo "DELETE SERVER";}
if ($ADD==5111111111111)	{$hh='admin';	$sh='conference';	echo "DELETE CONFERENCE";}
if ($ADD==51111111111111)	{$hh='admin';	$sh='conference';	echo "DELETE OSDial CONFERENCE";}
if ($ADD==6)			{$hh='users';		echo "Delete User";}
if ($ADD==61)			{$hh='campaigns';	$sh='detail';	echo "Delete Campaign";}
if ($ADD==62)			{$hh='campaigns';	$sh='detail';	echo "Logout Agents";}
if ($ADD==63)			{$hh='campaigns';	$sh='detail';	echo "Emergency VDAC Jam Clear";}
if ($ADD==65)			{$hh='campaigns';	$sh='recycle';	echo "Delete Lead Recycle";}
if ($ADD==66)			{$hh='campaigns';	$sh='autoalt';	echo "Delete Auto Alt Dial Status";}
if ($ADD==67)			{$hh='campaigns';	$sh='pause';	echo "Delete Agent Pause Code";}
if ($ADD==68)			{$hh='campaigns';	$sh='dialstat';	echo "Campaign Dial Status Removed";}
if ($ADD==69)			{$hh='campaigns';	$sh='listmix';	echo "Campaign List Mix Removed";}
if ($ADD==611)			{$hh='lists';		echo "Delete List";}
if ($ADD==6111)			{$hh='ingroups';	echo "Delete In-Group";}
if ($ADD==61111)		{$hh='remoteagent';	echo "Delete Remote Agents";}
if ($ADD==611111)		{$hh='usergroups';	echo "Delete Users Group";}
if ($ADD==6111111)		{$hh='scripts';		echo "Delete Script";}
if ($ADD==61111111)		{$hh='filters';		echo "Delete Filter";}
if ($ADD==611111111)	{$hh='admin';	$sh='times';	echo "Delete Call Time";}
if ($ADD==6111111111)	{$hh='admin';	$sh='times';	echo "Delete State Call Time";}
if ($ADD==61111111111)	{$hh='admin';	$sh='phones';	echo "DELETE PHONE";}
if ($ADD==611111111111)	{$hh='admin';	$sh='server';	echo "DELETE SERVER";}
if ($ADD==621111111111)	{$hh='admin';	$sh='server';	echo "DELETE SERVER OSDial TRUNK RECORD";}
if ($ADD==6111111111111)	{$hh='admin';	$sh='conference';	echo "DELETE CONFERENCE";}
if ($ADD==61111111111111)	{$hh='admin';	$sh='conference';	echo "DELETE OSDial CONFERENCE";}
if ($ADD==699211111111111)	{$hh='admin';	$sh='server';	echo "DELETE QC SERVER SETTINGS";}
if ($ADD==73)			{$hh='campaigns';	echo "Dialable Lead Count";}
if ($ADD==7111111)		{$hh='scripts';		echo "Preview Script";}
if ($ADD==0)			{$hh='users';		echo "Users List";}
if ($ADD==8)			{$hh='users';		echo "CallBacks Within Agent";}
if ($ADD==81)			{$hh='campaigns';	$sh='list';	echo "CallBacks Within Campaign";}
if ($ADD==811)			{$hh='lists';	echo "CallBacks Within List";}
if ($ADD==8111)			{$hh='usergroups';	echo "CallBacks Within User Group";}
if ($ADD==10)			{$hh='campaigns';	$sh='list';		echo "Campaigns";}
if ($ADD==100)			{$hh='lists';		echo "Lists";}
if ($ADD==1000)			{$hh='ingroups';	echo "In-Groups";}
if ($ADD==10000)		{$hh='remoteagent';	echo "Remote Agents";}
if ($ADD==100000)		{$hh='usergroups';	echo "User Groups";}
if ($ADD==1000000)		{$hh='scripts';		echo "Scripts";}
if ($ADD==10000000)		{$hh='filters';		echo "Filters";}
if ($ADD==100000000)	{$hh='admin';	$sh='times';	echo "Call Times";}
if ($ADD==1000000000)	{$hh='admin';	$sh='times';	echo "State Call Times";}
if ($ADD==10000000000)	{$hh='admin';	$sh='phones';	echo "PHONE LIST";}
if ($ADD==100000000000)	{$hh='admin';	$sh='server';	echo "SERVER LIST";}
if ($ADD==1000000000000)	{$hh='admin';	$sh='conference';	echo "CONFERENCE LIST";}
if ($ADD==10000000000000)	{$hh='admin';	$sh='conference';	echo "OSDial CONFERENCE LIST";}
if ($ADD==550)			{$hh='users';		echo "Search Form";}
if ($ADD==551)			{$hh='users';		echo "SEARCH PHONES";}
if ($ADD==660)			{$hh='users';		echo "Search Results";}
if ($ADD==661)			{$hh='users';		echo "SEARCH PHONES RESULTS";}
if ($ADD==99999)		{$hh='users';		echo "HELP";}
if ($ADD==999999)		{$hh='reports';		echo "REPORTS";}

if ( ($ADD>9) && ($ADD < 99998) )
	{
	##### get scripts listing for dynamic pulldown
	$stmt="SELECT script_id,script_name from vicidial_scripts order by script_id";
	$rslt=mysql_query($stmt, $link);
	$scripts_to_print = mysql_num_rows($rslt);
	$scripts_list="<option value=\"\">NONE</option>\n";

	$o=0;
	while ($scripts_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$scripts_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$scriptname_list["$rowx[0]"] = "$rowx[1]";
		$o++;
		}

	##### get filters listing for dynamic pulldown
	$stmt="SELECT lead_filter_id,lead_filter_name,lead_filter_sql from vicidial_lead_filters order by lead_filter_id";
	$rslt=mysql_query($stmt, $link);
	$filters_to_print = mysql_num_rows($rslt);
	$filters_list="<option value=\"\">NONE</option>\n";

	$o=0;
	while ($filters_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$filters_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$filtername_list["$rowx[0]"] = "$rowx[1]";
		$filtersql_list["$rowx[0]"] = "$rowx[2]";
		$o++;
		}

	##### get call_times listing for dynamic pulldown
	$stmt="SELECT call_time_id,call_time_name from vicidial_call_times order by call_time_id";
	$rslt=mysql_query($stmt, $link);
	$times_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($times_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$call_times_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$call_timename_list["$rowx[0]"] = "$rowx[1]";
		$o++;
		}
	}

if ( ( (strlen($ADD)>4) && ($ADD < 99998) ) or ($ADD==3) or (($ADD>20) and ($ADD<70)) or ($ADD=="4A")  or ($ADD=="4B") or (strlen($ADD)==12) )
	{
	##### get server listing for dynamic pulldown
	$stmt="SELECT server_ip,server_description from servers order by server_ip";
	$rslt=mysql_query($stmt, $link);
	$servers_to_print = mysql_num_rows($rslt);
	$servers_list='';

	$o=0;
	while ($servers_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$servers_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}

	##### BEGIN get campaigns listing for rankings #####

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);
	$campaigns_list='';
	$campaigns_value='';
	$RANKcampaigns_list="<tr><td>CAMPAIGN</td><td> &nbsp; &nbsp; RANK</td><td> &nbsp; &nbsp; CALLS</td></tr>\n";

	$o=0;
	while ($campaigns_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$campaign_id_values[$o] = $rowx[0];
		$campaign_name_values[$o] = $rowx[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o)
		{
		$stmt="SELECT campaign_rank,calls_today from vicidial_campaign_agents where user='$user' and campaign_id='$campaign_id_values[$o]'";
		$rslt=mysql_query($stmt, $link);
		$ranks_to_print = mysql_num_rows($rslt);
		if ($ranks_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$SELECT_campaign_rank = $row[0];
			$calls_today = $row[1];
			}
		else
			{$calls_today=0;   $SELECT_campaign_rank=0;}
		if ( ($ADD=="4A") or ($ADD=="4B") )
			{
			if (isset($_GET["RANK_$campaign_id_values[$o]"]))			{$campaign_rank=$_GET["RANK_$campaign_id_values[$o]"];}
				elseif (isset($_POST["RANK_$campaign_id_values[$o]"]))	{$campaign_rank=$_POST["RANK_$campaign_id_values[$o]"];}

			if ($ranks_to_print > 0)
				{
				$stmt="UPDATE vicidial_campaign_agents set campaign_rank='$campaign_rank', campaign_weight='$campaign_rank' where campaign_id='$campaign_id_values[$o]' and user='$user';";
				$rslt=mysql_query($stmt, $link);
				}
			else
				{
				$stmt="INSERT INTO vicidial_campaign_agents set campaign_rank='$campaign_rank', campaign_weight='$campaign_rank', campaign_id='$campaign_id_values[$o]', user='$user';";
				$rslt=mysql_query($stmt, $link);
				}

			$stmt="UPDATE vicidial_live_agents set campaign_weight='$campaign_rank' where campaign_id='$campaign_id_values[$o]' and user='$user';";
			$rslt=mysql_query($stmt, $link);
			}
		else {$campaign_rank = $SELECT_campaign_rank;}

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		# disable non user-group allowable campaign ranks
		$stmt="SELECT user_group from vicidial_users where user='$user';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$Ruser_group =	$row[0];

		$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$Ruser_group';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$allowed_campaigns =	$row[0];
		$allowed_campaigns = preg_replace("/ -$/","",$allowed_campaigns);
		$UGcampaigns = explode(" ", $allowed_campaigns);

		$p=0;   $RANK_camp_active=0;   $CR_disabled = '';
		if (eregi('-ALL-CAMPAIGNS-',$allowed_campaigns))
			{$RANK_camp_active++;}
		else
			{
			$UGcampaign_ct = count($UGcampaigns);
			while ($p < $UGcampaign_ct)
				{
				if ($campaign_id_values[$o] == $UGcampaigns[$p]) 
					{$RANK_camp_active++;}
				$p++;
				}
			}
		if ($RANK_camp_active < 1) {$CR_disabled = 'DISABLED';}

		$RANKcampaigns_list .= "<tr $bgcolor><td>";
		$campaigns_list .= "<a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id_values[$o]\">$campaign_id_values[$o]</a> - $campaign_name_values[$o] <BR>\n";
		$RANKcampaigns_list .= "<a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id_values[$o]\">$campaign_id_values[$o]</a> - $campaign_name_values[$o] </td>";
		$RANKcampaigns_list .= "<td> &nbsp; &nbsp; <select size=1 name=RANK_$campaign_id_values[$o] $CR_disabled>\n";
		$h="9";
		while ($h>=-9)
			{
			$RANKcampaigns_list .= "<option value=\"$h\"";
			if ($h==$campaign_rank)
				{$RANKcampaigns_list .= " SELECTED";}
			$RANKcampaigns_list .= ">$h</option>";
			$h--;
			}
		$RANKcampaigns_list .= "</select></td>\n";
		$RANKcampaigns_list .= "<td align=right> &nbsp; &nbsp; $calls_today</td></tr>\n";
		$o++;
		}
	##### END get campaigns listing for rankings #####


	##### BEGIN get inbound groups listing for checkboxes #####
	$xfer_groupsSQL='';
	if ( (($ADD>20) and ($ADD<70)) and ($ADD!=41) )
	{
	$stmt="SELECT closer_campaigns,xfer_groups from vicidial_campaigns where campaign_id='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$closer_campaigns =	$row[0];
		$closer_campaigns = preg_replace("/ -$/","",$closer_campaigns);
		$groups = explode(" ", $closer_campaigns);
	$xfer_groups =	$row[1];
		$xfer_groups = preg_replace("/ -$/","",$xfer_groups);
		$XFERgroups = explode(" ", $xfer_groups);
	$xfer_groupsSQL = preg_replace("/^ | -$/","",$xfer_groups);
	$xfer_groupsSQL = preg_replace("/ /","','",$xfer_groupsSQL);
	$xfer_groupsSQL = "WHERE group_id IN('$xfer_groupsSQL')";
	}
	if ($ADD==41)
	{
	$p=0;
	$XFERgroup_ct = count($XFERgroups);
	while ($p < $XFERgroup_ct)
		{
		$xfer_groups .= " $XFERgroups[$p]";
		$p++;
		}
	$xfer_groupsSQL = preg_replace("/^ | -$/","",$xfer_groups);
	$xfer_groupsSQL = preg_replace("/ /","','",$xfer_groupsSQL);
	$xfer_groupsSQL = "WHERE group_id IN('$xfer_groupsSQL')";
	}

	if ( (($ADD==31111) or ($ADD==31111)) and (count($groups)<1) )
	{
	$stmt="SELECT closer_campaigns from vicidial_remote_agents where remote_agent_id='$remote_agent_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$closer_campaigns =	$row[0];
	$closer_campaigns = preg_replace("/ -$/","",$closer_campaigns);
	$groups = explode(" ", $closer_campaigns);
	}

	if ($ADD==3)
	{
	$stmt="SELECT closer_campaigns from vicidial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$closer_campaigns =	$row[0];
	$closer_campaigns = preg_replace("/ -$/","",$closer_campaigns);
	$groups = explode(" ", $closer_campaigns);
	}

	$stmt="SELECT group_id,group_name from vicidial_inbound_groups order by group_id";
	$rslt=mysql_query($stmt, $link);
	$groups_to_print = mysql_num_rows($rslt);
	$groups_list='';
	$groups_value='';
	$XFERgroups_list='';
	$RANKgroups_list="<tr><td>INBOUND GROUP</td><td> &nbsp; &nbsp; RANK</td><td> &nbsp; &nbsp; CALLS</td></tr>\n";

	$o=0;
	while ($groups_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$group_id_values[$o] = $rowx[0];
		$group_name_values[$o] = $rowx[1];
		$o++;
		}

	$o=0;
	while ($groups_to_print > $o)
		{
		$stmt="SELECT group_rank,calls_today from vicidial_inbound_group_agents where user='$user' and group_id='$group_id_values[$o]'";
		$rslt=mysql_query($stmt, $link);
		$ranks_to_print = mysql_num_rows($rslt);
		if ($ranks_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$SELECT_group_rank = $row[0];
			$calls_today = $row[1];
			}
		else
			{$calls_today=0;   $SELECT_group_rank=0;}
		if ( ($ADD=="4A") or ($ADD=="4B") )
			{
			if (isset($_GET["RANK_$group_id_values[$o]"]))			{$group_rank=$_GET["RANK_$group_id_values[$o]"];}
				elseif (isset($_POST["RANK_$group_id_values[$o]"]))	{$group_rank=$_POST["RANK_$group_id_values[$o]"];}

			if ($ranks_to_print > 0)
				{
				$stmt="UPDATE vicidial_inbound_group_agents set group_rank='$group_rank', group_weight='$group_rank' where group_id='$group_id_values[$o]' and user='$user';";
				$rslt=mysql_query($stmt, $link);
				}
			else
				{
				$stmt="INSERT INTO vicidial_inbound_group_agents set group_rank='$group_rank', group_weight='$group_rank', group_id='$group_id_values[$o]', user='$user';";
				$rslt=mysql_query($stmt, $link);
				}

			$stmt="UPDATE vicidial_live_inbound_agents set group_weight='$group_rank' where group_id='$group_id_values[$o]' and user='$user';";
			$rslt=mysql_query($stmt, $link);
			}
		else {$group_rank = $SELECT_group_rank;}

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		$groups_list .= "<input type=\"checkbox\" name=\"groups[]\" value=\"$group_id_values[$o]\"";
		$XFERgroups_list .= "<input type=\"checkbox\" name=\"XFERgroups[]\" value=\"$group_id_values[$o]\"";
		$RANKgroups_list .= "<tr $bgcolor><td><input type=\"checkbox\" name=\"groups[]\" value=\"$group_id_values[$o]\"";
		$p=0;
		$group_ct = count($groups);
		while ($p < $group_ct)
			{
			if ($group_id_values[$o] == $groups[$p]) 
				{
				$groups_list .= " CHECKED";
				$RANKgroups_list .= " CHECKED";
				$groups_value .= " $group_id_values[$o]";
				}
			$p++;
			}
		$p=0;
		$XFERgroup_ct = count($XFERgroups);
		while ($p < $XFERgroup_ct)
			{
			if ($group_id_values[$o] == $XFERgroups[$p]) 
				{
				$XFERgroups_list .= " CHECKED";
				$XFERgroups_value .= " $group_id_values[$o]";
				}
			$p++;
			}
		$groups_list .= "> <a href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">$group_id_values[$o]</a> - $group_name_values[$o] <BR>\n";
		$XFERgroups_list .= "> <a href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">$group_id_values[$o]</a> - $group_name_values[$o] <BR>\n";
		$RANKgroups_list .= "> <a href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">$group_id_values[$o]</a> - $group_name_values[$o] </td>";
		$RANKgroups_list .= "<td> &nbsp; &nbsp; <select size=1 name=RANK_$group_id_values[$o]>\n";
		$h="9";
		while ($h>=-9)
			{
			$RANKgroups_list .= "<option value=\"$h\"";
			if ($h==$group_rank)
				{$RANKgroups_list .= " SELECTED";}
			$RANKgroups_list .= ">$h</option>";
			$h--;
			}
		$RANKgroups_list .= "</select></td>\n";
		$RANKgroups_list .= "<td align=right> &nbsp; &nbsp; $calls_today</td></tr>\n";
		$o++;
		}
	if (strlen($groups_value)>2) {$groups_value .= " -";}
	if (strlen($XFERgroups_value)>2) {$XFERgroups_value .= " -";}
	}
	##### END get inbound groups listing for checkboxes #####


	##### BEGIN get campaigns listing for checkboxes #####
	if ( ($ADD==211111) or ($ADD==311111) or ($ADD==411111) or ($ADD==511111) or ($ADD==611111) )
	{
		if ( ($ADD==211111) or ($ADD==311111) or ($ADD==511111) or ($ADD==611111) )
		{
		$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$user_group';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$allowed_campaigns =	$row[0];
		$allowed_campaigns = preg_replace("/ -$/","",$allowed_campaigns);
		$campaigns = explode(" ", $allowed_campaigns);
		}

	$campaigns_value='';
	$campaigns_list='<B><input type="checkbox" name="campaigns[]" value="-ALL-CAMPAIGNS-"';
		$p=0;
		while ($p<100)
			{
			if (eregi('ALL-CAMPAIGNS',$campaigns[$p])) 
				{
				$campaigns_list.=" CHECKED";
				$campaigns_value .= " -ALL-CAMPAIGNS- -";
				}
			$p++;
			}
	$campaigns_list.="> ALL-CAMPAIGNS - AGENTS CAN VIEW ANY CAMPAIGN</B><BR>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$campaign_id_value = $rowx[0];
		$campaign_name_value = $rowx[1];
		$campaigns_list .= "<input type=\"checkbox\" name=\"campaigns[]\" value=\"$campaign_id_value\"";
		$p=0;
		while ($p<100)
			{
			if ($campaign_id_value == $campaigns[$p]) 
				{
			#	echo "<!--  X $p|$campaign_id_value|$campaigns[$p]| -->";
				$campaigns_list .= " CHECKED";
				$campaigns_value .= " $campaign_id_value";
				}
		#	echo "<!--  O $p|$campaign_id_value|$campaigns[$p]| -->";
			$p++;
			}
		$campaigns_list .= "> $campaign_id_value - $campaign_name_value<BR>\n";
		$o++;
		}
	if (strlen($campaigns_value)>2) {$campaigns_value .= " -";}
	}
	##### END get campaigns listing for checkboxes #####


	if ( (strlen($ADD)==11) or (strlen($ADD)>12) )
	{
	##### get server listing for dynamic pulldown
	$stmt="SELECT server_ip,server_description from servers order by server_ip";
	$rsltx=mysql_query($stmt, $link);
	$servers_to_print = mysql_num_rows($rsltx);
	$servers_list='';

	$o=0;
	while ($servers_to_print > $o)
		{
		$rowx=mysql_fetch_row($rsltx);
		$servers_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
	}



$NWB = " &nbsp; <a href=\"javascript:openNewWindow('$PHP_SELF?ADD=99999";
$NWE = "')\"><IMG SRC=\"help.gif\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"HELP\" ALIGN=TOP></A>";


######################################################################################################
######################################################################################################
#######   9 series, HELP screen
######################################################################################################
######################################################################################################


######################
# ADD=99999 display the HELP SCREENS
######################

if ($ADD==99999)
{
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
echo "<CENTER>\n";
echo "<TABLE frame=box WIDTH=98% BGCOLOR=#AFCFD7 cellpadding=2 cellspacing=0>";
echo "<TR><TD ALIGN=center><font FACE=\"ARIAL,HELVETICA\" COLOR=1C4754 SIZE=4><B>OSDial Help</B></FONT></td>";
echo "</tr>";
echo "<tr><td><FONT FACE=\"ARIAL,HELVETICA\" COLOR=1C4754 SIZE=2><BR><BR>\n";

?>
<B><FONT SIZE=3>OSDial_Users Table</FONT></B><BR><BR>
<A NAME="osdial_users-user">
<BR>
<B>Agent ID -</B> This field is where you put the OSDial agents ID number, can be up to 8 digits in length, Must be at least 2 characters in length.

<BR>
<A NAME="osdial_users-pass">
<BR>
<B>Password -</B> This field is where you put the OSDial agents password. Must be at least 2 characters in length.

<BR>
<A NAME="osdial_users-full_name">
<BR>
<B>Full Name -</B> This field is where you put the OSDial agents full name. Must be at least 2 characters in length.

<BR>
<A NAME="osdial_users-user_level">
<BR>
<B>User Level -</B> This menu is where you select the OSDial agents user level. Must be a level of 1 to log into OSDial, Must be level greater than 2 to log in as a closer, Must be user level 8 or greater to get into admin web section.

<BR>
<A NAME="osdial_users-user_group">
<BR>
<B>User Group -</B> This menu is where you select the OSDial agents group that this user will belong to. This does not have any restrictions at this time, this is just to subdivide users and allow for future features based upon it.

<BR>
<A NAME="osdial_users-phone_login">
<BR>
<B>Phone Login -</B> Here is where you can set a default phone login value for when the user logs into agent.php. This value will populate the phone_login automatically when the user logs in with their user-pass-campaign in the agent.php login screen.

<BR>
<A NAME="osdial_users-phone_pass">
<BR>
<B>Phone Pass -</B> Here is where you can set a default phone pass value for when the user logs into agent.php. This value will populate the phone_pass automatically when the user logs in with their user-pass-campaign in the agent.php login screen.

<BR>
<A NAME="osdial_users-hotkeys_active">
<BR>
<B>Hot Keys Active -</B> This option if set to 1 allows the user to use the Hot Keys quick-dispositioning function in agent.php.

<BR>
<A NAME="osdial_users-agent_choose_ingroups">
<BR>
<B>Agent Choose Ingroups -</B> This option if set to 1 allows the user to choose the ingroups that they will receive calls from when they login to a CLOSER or INBOUND campaign. Otherwise the Manager will need to set this in their user detail screen of the admin page.

<BR>
<A NAME="osdial_users-scheduled_callbacks">
<BR>
<B>Scheduled Callbacks -</B> This option allows an agent to disposition a call as CALLBK and choose the data and time at which the lead will be re-activated.

<BR>
<A NAME="osdial_users-agentonly_callbacks">
<BR>
<B>Agent-Only Callbacks -</B> This option allows an agent to set a callback so that they are the only Agent that can call the customer back. This also allows the agent to see their callback listings and call them back any time they want to.

<BR>
<A NAME="osdial_users-agentcall_manual">
<BR>
<B>Agent Call Manual -</B> This option allows an agent to manually enter a new lead into the system and call them. This also allows the calling of any phone number from their agent screen and puts that call into their session. Use this option with caution.

<BR>
<A NAME="osdial_users-osdial_recording">
<BR>
<B>OSDial Recording -</B> This option can prevent an agent from doing any recordings after they log in to OSDial. This option must be on for OSDial to follow the campaign recording session.

<BR>
<A NAME="osdial_users-osdial_transfers">
<BR>
<B>OSDial Transfers -</B> This option can prevent an agent from opening the transfer - conference session of OSDial. If this is disabled, the agent cannot third party call or blind transfer any calls.

<BR>
<A NAME="osdial_users-closer_default_blended">
<BR>
<B>Closer Default Blended -</B> This option simply defaults the Blended checkbox on a CLOSER login screen.

<BR>
<A NAME="osdial_users-osdial_recording_override">
<BR>
<B>OSDial Recording Override -</B> This option will override whatever the option is in the campaign for recording. DISABLED will not override the campaign recording setting. NEVER will disable recording on the client. ONDEMAND is the default and allows the agent to start and stop recording as needed. ALLCALLS will start recording on the client whenever a call is sent to an agent. ALLFORCE will start recording on the client whenever a call is sent to an agent giving the agent no option to stop recording. For ALLCALLS and ALLFORCE there is an option to use the Recording Delay to cut down on very short recordings and recude system load.

<BR>
<A NAME="osdial_users-osdial_users-campaign_ranks">
<BR>
<B>Campaign Ranks -</B> In this section you can define the rank an agent will have for each campaign. These ranks can be used to allow for preferred call routing when Next Agent Call is set to campaign_rank.

<BR>
<A NAME="osdial_users-closer_campaigns">
<BR>
<B>Inbound Groups -</B> Here is where you select the inbound groups you want to receive calls from if you have selected the CLOSER campaign. You will also be able to set the rank, or skill level, in this section for each of the inbound groups as well as being able to see the number of calls received from each inbound group for this specific agent. Also in this section is the ability to give the agent a rank for each inbound group. These ranks can be used for preferred call routing when that option is selected in the in-group screen.

<BR>
<A NAME="osdial_users-alter_custdata_override">
<BR>
<B>Agent Alter Customer Data Override -</B> This option will override whatever the option is in the campaign for altering of customer data. NOT_ACTIVE will use whatever setting is present for the campaign. ALLOW_ALTER will always allow for the agent to alter the customer data, no matter what the campaign setting is. Default is NOT_ACTIVE.

<BR>
<A NAME="osdial_users-alter_agent_interface_options">
<BR>
<B>Alter Agent Interface Options -</B> This option if set to 1 allows the administrative user to modify the Agents interface options in admin.php.

<BR>
<A NAME="osdial_users-delete_users">
<BR>
<B>Delete Agents -</B> This option if set to 1 allows the user to delete other users of equal or lesser user level from the system.

<BR>
<A NAME="osdial_users-delete_user_groups">
<BR>
<B>Delete User Groups -</B> This option if set to 1 allows the user to delete user groups from the system.

<BR>
<A NAME="osdial_users-delete_lists">
<BR>
<B>Delete Lists -</B> This option if set to 1 allows the user to delete OSDial lists from the system.

<BR>
<A NAME="osdial_users-delete_campaigns">
<BR>
<B>Delete Campaigns -</B> This option if set to 1 allows the user to delete OSDial campaigns from the system.

<BR>
<A NAME="osdial_users-delete_ingroups">
<BR>
<B>Delete In-Groups -</B> This option if set to 1 allows the user to delete OSDial In-Groups from the system.

<BR>
<A NAME="osdial_users-delete_remote_agents">
<BR>
<B>Delete Remote Agents -</B> This option if set to 1 allows the user to delete OSDial remote agents from the system.

<BR>
<A NAME="osdial_users-load_leads">
<BR>
<B>Load Leads -</B> This option if set to 1 allows the user to load OSDial leads into the osdial_list table by way of the web based lead loader.

<BR>
<A NAME="osdial_users-campaign_detail">
<BR>
<B>Campaign Detail -</B> This option if set to 1 allows the user to view and modify the campaign detail screen elements.

<BR>
<A NAME="osdial_users-ast_admin_access">
<BR>
<B>AGC Admin Access -</B> This option if set to 1 allows the user to login to the astGUIclient admin pages.

<BR>
<A NAME="osdial_users-ast_delete_phones">
<BR>
<B>AGC Delete Phones -</B> This option if set to 1 allows the user to delete phone entries in the astGUIclient admin pages.

<BR>
<A NAME="osdial_users-delete_scripts">
<BR>
<B>Delete Scripts -</B> This option if set to 1 allows the user to delete Campaign scripts in the script modification screen.

<BR>
<A NAME="osdial_users-modify_leads">
<BR>
<B>Modify Leads -</B> This option if set to 1 allows the user to modify leads in the admin section lead search results page.

<BR>
<A NAME="osdial_users-change_agent_campaign">
<BR>
<B>Change Agent Campaign -</B> This option if set to 1 allows the user to alter the campaign that an agent is logged into while they are logged into it.

<BR>
<A NAME="osdial_users-delete_filters">
<BR>
<B>Delete Filters -</B> This option allows the user to be able to delete OSDial lead filters from the system.

<BR>
<A NAME="osdial_users-delete_call_times">
<BR>
<B>Delete Call Times -</B> This option allows the user to be able to delete OSDial call times records and OSDial state call times records from the system.

<BR>
<A NAME="osdial_users-modify_call_times">
<BR>
<B>Modify Call Times -</B> This option allows the user to view and modify the call times and state call times records. A user doesn't need this option enabled if they only need to change the call times option on the campaigns screen.

<BR>
<A NAME="osdial_users-modify_sections">
<BR>
<B>Modify Sections -</B> These options allow the user to view and modify each sections records. If set to 0, the user will be able to see the section list, but not the detail or modification screen of a record in that section.

<BR>
<A NAME="osdial_users-view_reports">
<BR>
<B>View Reports -</B> This option allows the user to view the OSDial reports.




<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CAMPAIGNS TABLE</FONT></B><BR><BR>
<A NAME="osdial_campaigns-campaign_id">
<BR>
<B>Campaign ID -</B> This is the short name of the campaign, it is not editable after initial submission, cannot contain spaces and must be between 2 and 8 characters in length.

<BR>
<A NAME="osdial_campaigns-campaign_name">
<BR>
<B>Campaign Name -</B> This is the description of the campaign, it must be between 6 and 40 characters in length.

<BR>
<A NAME="osdial_campaigns-campaign_description">
<BR>
<B>Campaign Description -</B> This is a memo field for the campaign, it is optional and can be a maximum of 255 characters in length.

<BR>
<A NAME="osdial_campaigns-campaign_changedate">
<BR>
<B>Campaign Change Date -</B> This is the last time that the settings for this campaign were modified in any way.

<BR>
<A NAME="osdial_campaigns-campaign_logindate">
<BR>
<B>Last Campaign Login Date -</B> This is the last time that an agent was logged into this campaign.

<BR>
<A NAME="osdial_campaigns-campaign_stats_refresh">
<BR>
<B>Campaign Stats Refresh -</B> This checkbox will allow you to force a OSDial stats refresh, even if the campaign is not active.

<BR>
<A NAME="osdial_campaigns-active">
<BR>
<B>Active -</B> This is where you set the campaign to Active or Inactive. If Inactive, noone can log into it.

<BR>
<A NAME="osdial_campaigns-park_ext">
<BR>
<B>Park Extension -</B> This is where you can customize the on-hold music for OSDial. Make sure the extension is in place in the extensions.conf and that it points to the filename below.

<BR>
<A NAME="osdial_campaigns-park_file_name">
<BR>
<B>Park File Name -</B> This is where you can customize the on-hold music for OSDial. Make sure the filename is 10 characters in length or less and that the file is in place in the /var/lib/asterisk/sounds directory.

<BR>
<A NAME="osdial_campaigns-web_form_address">
<BR>
<B>Web Form -</B> This is where you can set the custom web page that will be opened when the user clicks on one of the WEB FORM buttons.

<BR>
<A NAME="osdial_campaigns-allow_closers">
<BR>
<B>Allow Closers -</B> This is where you can set whether the users of this campaign will have the option to send the call to a closer.

<BR>
<A NAME="osdial_campaigns-default_xfer_group">
<BR>
<B>Default Transfer Group -</B> This field is the default In-Group that will be automatically selected when the agent goes to the transfer-conference frame in their agent interface.

<BR>
<A NAME="osdial_campaigns-xfer_groups">
<BR>
<B>Allowed Transfer Groups -</B> With these checkbox listings you can select the groups that agents in this campaign can transfer calls to. Allow Closers must be enabled for this option to show up.

<BR>
<A NAME="osdial_campaigns-campaign_allow_inbound">
<BR>
<B>Allow Inbound and Blended -</B> This is where you can set whether the users of this campaign will have the option to take inbound calls with this campaign. If you want to do blended inbound and outbound then this must be set to Y. If you only want to do outbound dialing on this campaign set this to N. Default is N.

<BR>
<A NAME="osdial_campaigns-dial_status">
<BR>
<B>Dial Status -</B> This is where you set the statuses that you are wanting to dial on within the lists that are active for the campaign below. To add another status to dial, select it from the drop-down list and click ADD. To remove one of the dial statuses, click on the REMOVE link next to the status you want to remove.

<BR>
<A NAME="osdial_campaigns-lead_order">
<BR>
<B>List Order -</B> This menu is where you select how the leads that match the statuses selected above will be put in the lead hopper:
 <BR> &nbsp; - DOWN: select the first leads loaded into the osdial_list table
 <BR> &nbsp; - UP: select the last leads loaded into the osdial_list table
 <BR> &nbsp; - UP PHONE: select the highest phone number and works its way down
 <BR> &nbsp; - DOWN PHONE: select the lowest phone number and works its way up
 <BR> &nbsp; - UP LAST NAME: starts with last names starting with Z and works its way down
 <BR> &nbsp; - DOWN LAST NAME: starts with last names starting with A and works its way up
 <BR> &nbsp; - UP COUNT: starts with most called leads and works its way down
 <BR> &nbsp; - DOWN COUNT: starts with least called leads and works its way up
 <BR> &nbsp; - DOWN COUNT 2nd NEW: starts with least called leads and works its way up inserting a NEW lead in every other lead - Must NOT have NEW selected in the dial statuses
 <BR> &nbsp; - DOWN COUNT 3nd NEW: starts with least called leads and works its way up inserting a NEW lead in every third lead - Must NOT have NEW selected in the dial statuses
 <BR> &nbsp; - DOWN COUNT 4th NEW: starts with least called leads and works its way up inserting a NEW lead in every forth lead - Must NOT have NEW selected in the dial statuses

<BR>
<A NAME="osdial_campaigns-hopper_level">
<BR>
<B>Hopper Level -</B> This is how many leads the VDhopper script tries to keep in the osdial_hopper table for this campaign. If running VDhopper script every minute, make this slightly greater than the number of leads you go through in a minute.

<BR>
<A NAME="osdial_campaigns-lead_filter_id">
<BR>
<B>Lead Filter -</B> This is a method of filtering your leads using a fragment of a SQL query. Use this feature with caution, it is easy to stop dialing accidentally with the slightest alteration to the SQL statement. Default is NONE.

<BR>
<A NAME="osdial_campaigns-force_reset_hopper">
<BR>
<B>Force Reset of Hopper -</B> This allows you to wipe out the hopper contents upon form submission. It should be filled again when the VDhopper script runs.

<BR>
<A NAME="osdial_campaigns-dial_method">
<BR>
<B>Dial Method -</B> This field is the way to define how dialing is to take place. If MANUAL then the auto_dial_level will be locked at 0 unless Dial Method is changed. If RATIO then the normal dialing a number of lines for Active agents. ADAPT_HARD_LIMIT will dial predictively up to the dropped percentage and then not allow aggressive dialing once the drop limit is reached until the percentage goes down again. ADAPT_TAPERED allows for running over the dropped percentage in the first half of the shift -as defined by call_time selected for campaign- and gets more strict as the shift goes on. ADAPT_AVERAGE tries to maintain an average or the dropped percentage not imposing hard limits as aggressively as the other two methods. You cannot change the Auto Dial Level if you are in any of the ADAPT dial methods. Only the Dialer can change the dial level when in predictive dialing mode.

<BR>
<A NAME="osdial_campaigns-auto_dial_level">
<BR>
<B>Auto Dial Level -</B> This is where you set how many lines OSDial should use per active agent. zero 0 means auto dialing is off and the agents will click to dial each number. Otherwise OSDial will keep dialing lines equal to active agents multiplied by the dial level to arrive at how many lines this campaign on each server should allow. The ADAPT OVERRIDE checkbox allows you to force a new dial level even though the dial method is in an ADAPT mode. This is useful if there is a dramatic shift in the quality of leads and you want to drastically change the dial_level manually.

<BR>
<A NAME="osdial_campaigns-available_only_ratio_tally">
<BR>
<B>Available Only Tally -</B> This field if set to Y will leave out INCALL and QUEUE status agents when calculating the number of calls to dial when not in MANUAL dial mode. Default is N.

<BR>
<A NAME="osdial_campaigns-adaptive_dropped_percentage">
<BR>
<B>Drop Percentage Limit -</B> This field is where you set the limit of the percentage of dropped calls you would like while using an adaptive-predictive dial method, not MANUAL or RATIO.

<BR>
<A NAME="osdial_campaigns-adaptive_maximum_level">
<BR>
<B>Maximum Adapt Dial Level -</B> This field is where you set the limit of the limit to the numbr of lines you would like dialed per agent while using an adaptive-predictive dial method, not MANUAL or RATIO. This number can be higher than the Auto Dial Level if your hardware will support it. Value must be a positive number greater than one and can have decimal places Default 3.0.

<BR>
<A NAME="osdial_campaigns-adaptive_latest_server_time">
<BR>
<B>Latest Server Time -</B> This field is only used by the ADAPT_TAPERED dial method. You should enter in the hour and minute that you will stop calling on this campaign, 2100 would mean that you will stop dialing this campaign at 9PM server time. This allows the Tapered algorithm to decide how aggressively to dial by how long you have until you will be finished calling.

<BR>
<A NAME="osdial_campaigns-adaptive_intensity">
<BR>
<B>Adapt Intensity Modifier -</B> This field is used to adjust the predictive intensity either higher or lower. The higher a positive number you select, the greater the dialer will increase the call pacing when it goes up and the slower the dialer will decrease the call pacing when it goes down. The lower the negative number you select here, the slower the dialer will increase the call pacing and the faster the dialer will lower the call pacing when it goes down. Default is 0. This field is not used by the MANUAL or RATIO dial methods.

<BR>
<A NAME="osdial_campaigns-adaptive_dl_diff_target">
<BR>
<B>Dial Level Difference Target -</B> This field is used to define whether you want to target having a specific number of agents waiting for calls or calls waiting for agents. For example if you would always like to have on average one agent free to take calls immediately you would set this to -1, if you would like to target always having one call on hold waiting for an agent you would set this to 1. Default is 0. This field is not used by the MANUAL or RATIO dial methods.

<BR>
<A NAME="osdial_campaigns-concurrent_transfers">
<BR>
<B>Concurrent Transfers -</B> This setting is used to define the number of calls that can be sent to agents at the same time. It is recommended that this setting is left at AUTO. This field is not used by the MANUAL dial method.

<BR>
<A NAME="osdial_campaigns-auto_alt_dial">
<BR>
<B>Auto Alt-Number Dialing -</B> This setting is used to automatically dial alternate number fields while dialing in the RATIO and ADAPT dial methods when there is no contact at the main phone number for a lead, the NA, B, DC and N statuses. This setting is not used by the MANUAL dial method.

<BR>
<A NAME="osdial_campaigns-next_agent_call">
<BR>
<B>Next Agent Call -</B> This determines which agent receives the next call that is available:
 <BR> &nbsp; - random: orders by the random update value in the osdial_live_agents table
 <BR> &nbsp; - oldest_call_start: orders by the last time an agent was sent a call. Results in agents receiving about the same number of calls overall.
 <BR> &nbsp; - oldest_call_finish: orders by the last time an agent finished a call. AKA agent waiting longest receives first call.
 <BR> &nbsp; - overall_user_level: orders by the user_level of the agent as defined in the osdial_users table a higher user_level will receive more calls.
 <BR> &nbsp; - campaign_rank: orders by the rank given to the agent for the campaign. Highest to Lowest.
 <BR> &nbsp; - fewest_calls: orders by the number of calls received by an agent for that specific inbound group. Least calls first.

<BR>
<A NAME="osdial_campaigns-local_call_time">
<BR>
<B>Local Call Time -</B> This is where you set during which hours you would like to dial, as determined by the local time in the are in which you are calling. This is controlled by area code and is adjusted for Daylight Savings time if applicable. General Guidelines in the USA for Business to Business is 9am to 5pm and Business to Consumer calls is 9am to 9pm.

<BR>
<A NAME="osdial_campaigns-dial_timeout">
<BR>
<B>Dial Timeout -</B> If defined, calls that would normally hang up after the timeout defined in extensions.conf would instead timeout at this amount of seconds if it is less than the extensions.conf timeout. This allows for quickly changing dial timeouts from server to server and limiting the effects to a single campaign. If you are having a lot of Answering Machine or Voicemail calls you may want to try changing this value to between 21-26 and see if results improve.

<BR>
<A NAME="osdial_campaigns-dial_prefix">
<BR>
<B>Dial Prefix -</B> This field allows for more easily changing a path of dialing to go out through a different method without doing a reload in Asterisk. Default is 9 based upon a 91NXXNXXXXXX in the dial plan - extensions.conf.

<BR>
<A NAME="osdial_campaigns-omit_phone_code">
<BR>
<B>Omit Phone Code -</B> This field allows you to leave out the phone_code field while dialing within OSDial. For instance if you are dialing in the UK from the UK you would have 44 in as your phone_code field for all leads, but you just want to dial 10 digits in your dial plan extensions.conf to place calls instead of 44 then 10 digits. Default is N.

<BR>
<A NAME="osdial_campaigns-campaign_cid">
<BR>
<B>Campaign CallerID -</B> This field allows for the sending of a custom callerid number on the outbound calls. This is the number that would show up on the callerid of the person you are calling. The default is UNKNOWN. If you are using T1 or E1s to dial out this option is only available if you are using PRIs - ISDN T1s or E1s - that have the custom callerid feature turned on, this will not work with Robbed-bit service -RBS- circuits. This will also work through most VOIP -SIP or IAX trunks- providers that allow dynamic outbound callerID. The custom callerID only applies to calls placed for the OSDial campaign directly, any 3rd party calls or transfers will not send the custom callerID. NOTE: Sometimes putting UNKNOWN or PRIVATE in the field will yield the sending of your default callerID number by your carrier with the calls. You may want to test this and put 0000000000 in the callerid field instead if you do not want to send you CallerID.

<BR>
<A NAME="osdial_campaigns-campaign_vdad_exten">
<BR>
<B>Campaign OSDial extension -</B> This field allows for a custom OSDial transfer extension. This allows you to use different VDADtransfer...agi scripts depending upon your campaign. The default transfer AGI - exten 8365 agi-VDADtransfer.agi - just immediately sends the calls on to agents as soon as they are picked up. An additional sample political survey AGI is also now included - 8366 agi-VDADtransferSURVEY.agi - that plays a message to the called person and allows them to make a choice by pressing buttons - effectively pre-screening the lead - . Please note that except for surveys, political calls and charities this form of calling is illegal in the United States.

<BR>
<A NAME="osdial_campaigns-campaign_rec_exten">
<BR>
<B>Campaign Rec extension -</B> This field allows for a custom recording extension to be used with OSDial. This allows you to use different extensions depending upon how long you want to allow a maximum recording and what type of codec you want to record in. The default exten is 8309 which if you follow the SCRATCH_INSTALL examples will record in the WAV format for upto one hour. Another option included in the examples is 8310 which will record in GSM format for upto one hour.

<BR>
<A NAME="osdial_campaigns-campaign_recording">
<BR>
<B>Campaign Recording -</B> This menu allows you to choose what level of recording is allowed on this campaign. NEVER will disable recording on the client. ONDEMAND is the default and allows the agent to start and stop recording as needed. ALLCALLS will start recording on the client whenever a call is sent to an agent. ALLFORCE will start recording on the client whenever a call is sent to an agent giving the agent no option to stop recording. For ALLCALLS and ALLFORCE there is an option to use the Recording Delay to cut down on very short recordings and recude system load.

<BR>
<A NAME="osdial_campaigns-campaign_rec_filename">
<BR>
<B>Campaign Rec Filename -</B> This field allows you to customize the name of the recording when Campaign recording is ONDEMAND or ALLCALLS. The allowed variables are CAMPAIGN CUSTPHONE FULLDATE TINYDATE EPOCH AGENT. The default is FULLDATE_AGENT and would look like this 20051020-103108_6666. Another example is CAMPAIGN_TINYDATE_CUSTPHONE which would look like this TESTCAMP_51020103108_3125551212. 50 char max.

<BR>
<A NAME="osdial_campaigns-allcalls_delay">
<BR>
<B>Recording Delay -</B> For ALLCALLS and ALLFORCE recording only. This setting will delay the starting of the recording on all calls for the number of seconds specified in this field. Default is 0.

<BR>
<A NAME="osdial_campaigns-campaign_script">
<BR>
<B>Campaign Script -</B> This menu allows you to choose the script that will appear on the agents screen for this campaign. Select NONE to show no script for this campaign.

<BR>
<A NAME="osdial_campaigns-get_call_launch">
<BR>
<B>Get Call Launch -</B> This menu allows you to choose whether you want to auto-launch the web-form page in a separate window, auto-switch to the SCRIPT tab or do nothing when a call is sent to the agent for this campaign. 

<BR>
<A NAME="osdial_campaigns-am_message_exten">
<BR>
<B>Answering Machine Message -</B> This field is for entering in an extension to blind transfer calls to when the agent gets an answering machine and clicks on the Answering Machine Message button in the transfer conference frame. You must set this exten up in the dial plan - extensions.conf - and make sure it plays an audio file then hangs up. 

<BR>
<A NAME="osdial_campaigns-amd_send_to_vmx">
<BR>
<B>AMD send to vm exten -</B> This menu allows you to define whether a message is left on an answering machine when it is detected. the call will be immediately forwarded to the Answering-Machine-Message extension if AMD is active and it is determined that the call is an answering machine.

<BR>
<A NAME="osdial_campaigns-xferconf_a_dtmf">
<BR>
<B>Xfer-Conf DTMF -</B> These four fields allow for you to have two sets of Transfer Conference and DTMF presets. When the call or campaign is loaded, the agent.php script will show two buttons on the transfer-conference frame and auto-populate the number-to-dial and the send-dtmf fields when pressed. If you want to allow Consultative Transfers, a fronter to a closer, you can place CXFER as one of the number-to-dial presets and the proper dial string will be sent to do a Local Consultative Transfer, then the agent can just LEAVE-3WAY-CALL and move on to their next call. If you want to allow Blind transfers of customers to a OSDial AGI script for logging or an IVR, then place AXFER in the number-to-dial field. You can also specify an custom extension after the AXFER or CXFER, for instance if you want to do Internal Consultative transfers instead of Local you would put CXFER90009 in the number-to-dial field.

<BR>
<A NAME="osdial_campaigns-alt_number_dialing">
<BR>
<B>Agent Alt Num Dialing -</B> This option allows an agent to manually dial the alternate phone number or address3 field after the main number has been called.

<BR>
<A NAME="osdial_campaigns-scheduled_callbacks">
<BR>
<B>Scheduled Callbacks -</B> This option allows an agent to disposition a call as CALLBK and choose the data and time at which the lead will be re-activated.

<BR>
<A NAME="osdial_campaigns-drop_call_seconds">
<BR>
<B>Drop Call Seconds -</B> The number of seconds from the time the customer line is picked up until the call is considered a DROP, only applies to outbound calls.

<BR>
<A NAME="osdial_campaigns-voicemail_ext">
<BR>
<B>Voicemail -</B> If defined, calls that would normally DROP would instead be directed to this voicemail box to hear and leave a message.

<BR>
<A NAME="osdial_campaigns-safe_harbor_message">
<BR>
<B>Safe Harbor Message -</B> If set to Y will play a message to customer after the Drop Call Seconds timeout is reached without being transferred to an agent. This setting will override sending to a voicemail box if this is set to Y.

<BR>
<A NAME="osdial_campaigns-safe_harbor_exten">
<BR>
<B>Safe Harbor Exten -</B> This is the dial plan extension that the desired Safe Harbor audio file is located at on your server.

<BR>
<A NAME="osdial_campaigns-wrapup_seconds">
<BR>
<B>Wrap Up Seconds -</B> The number of seconds to force an agent to wait before allowing them to receive or dial another call. The timer begins as soon as an agent hangs up on their customer - or in the case of alternate number dialing when the agent finishes the lead - Default is 0 seconds. If the timer runs out before the agent has dispositioned the call, the agent still will NOT move on to the next call until they select a disposition.

<BR>
<A NAME="osdial_campaigns-wrapup_message">
<BR>
<B>Wrap Up Message -</B> This is a campaign-specific message to be displayed on the wrap up screen if wrap up seconds is set.

<BR>
<A NAME="osdial_campaigns-use_internal_dnc">
<BR>
<B>Use Internal DNC List -</B> This defines whether this campaign is to filter leads against the Internal DNC list. If it is set to Y, the hopper will look for each phone number in the DNC list before placing it in the hopper. If it is in the DNC list then it will change that lead status to DNCL so it cannot be dialed. Default is N.

<BR>
<A NAME="osdial_campaigns-closer_campaigns">
<BR>
<B>Allowed Inbound Groups -</B> For CLOSER campaigns only. Here is where you select the inbound groups you want agents in this CLOSER campaign to be able to take calls from. It is important for BLENDED inbound-outbound campaigns only to select the inbound groups that are used for agents in this campaign. The calls coming into the inbound groups selected here will be counted as active calls for a blended campaign even if all agents in the campaign are not logged in to receive calls from all of those selected inbound groups.

<BR>
<A NAME="osdial_campaigns-agent_pause_codes_active">
<BR>
<B>Agent Pause Codes Active -</B> Allows agents to select a pause code when they click on the PAUSE button in agent.php. Pause codes are definable per campaign at the bottom of the campaign view detail screen and they are stored in the osdial_agent_log table. Default is N.

<BR>
<A NAME="osdial_campaigns-disable_alter_custdata">
<BR>
<B>Disable Alter Customer Data -</B> If set to Y, does not change any of the customer data record when an agent dispositions the call. Default is N.

<BR>
<A NAME="osdial_campaigns-no_hopper_leads_logins">
<BR>
<B>Allow No-Hopper-Leads Logins -</B> If set to Y, allows agents to login to the campaign even if there are no leads loaded into the hopper for that campaign. This function is not needed in CLOSER-type campaigns. Default is N.

<BR>
<A NAME="osdial_campaigns-list_order_mix">
<BR>
<B>List Order Mix -</B> Overrides the Lead Order and Dial Status fields. Will use the List and status parameters for the selected List Mix entry in the List Mix sub section instead. Default is DISABLED.

<BR>
<A NAME="osdial_campaigns-vcl_id">
<BR>
<B>List Mix ID -</B> ID of the list mix. Must be from 2-20 characters in length with no spaces or other special punctuation.

<BR>
<A NAME="osdial_campaigns-vcl_name">
<BR>
<B>List Mix Name -</B> Descriptive name of the list mix. Must be from 2-50 characters in length.

<BR>
<A NAME="osdial_campaigns-list_mix_container">
<BR>
<B>List Mix Detail -</B> The composition of the List Mix entry. Contains the List ID, mix order, percentages and statuses that make up this List Mix. The percentages always have to add up to 100, and the lists all have to be active and set to the campaign for the order mix entry to be Activated.

<BR>
<A NAME="osdial_campaigns-mix_method">
<BR>
<B>List Mix Method -</B> The method of mixing all of the parts of the List Mix Detail together. EVEN_MIX will mix leads from each part interleaved with the other parts, like this 1,2,3,1,2,3,1,2,3. IN_ORDER will put the leads in the order in which they are listed in the List Mix Detail screen 1,1,1,2,2,2,3,3,3. RANDOM will put them in RANDOM order 1,3,2,1,1,3,2,1,3. Default is IN_ORDER.

<BR>
<A NAME="osdial_campaigns-manual_dial_list_id">
<BR>
<B>Manual Dial List ID -</B> The default list_id to be used when an agent placces a manual call and a new lead record is created in osdial_list. Default is 999. This field can contain digits only.




<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_LISTS TABLE</FONT></B><BR><BR>
<A NAME="osdial_lists-list_id">
<BR>
<B>List ID -</B> This is the numerical name of the list, it is not editable after initial submission, must contain only numbers and must be between 2 and 8 characters in length. Must be a number greater than 100.

<BR>
<A NAME="osdial_lists-list_name">
<BR>
<B>List Name -</B> This is the description of the list, it must be between 2 and 20 characters in length.

<BR>
<A NAME="osdial_lists-list_description">
<BR>
<B>List Description -</B> This is the memo field for the list, it is optional.

<BR>
<A NAME="osdial_lists-list_changedate">
<BR>
<B>List Change Date -</B> This is the last time that the settings for this list were modified in any way.

<BR>
<A NAME="osdial_lists-list_lastcalldate">
<BR>
<B>List Last Call Date -</B> This is the last time that lead was dialed from this list.

<BR>
<A NAME="osdial_lists-campaign_id">
<BR>
<B>Campaign -</B> This is the campaign that this list belongs to. A list can only be dialed on a single campaign at one time.

<BR>
<A NAME="osdial_lists-active">
<BR>
<B>Active -</B> This defines whether the list is to be dialed on or not.

<BR>
<A NAME="osdial_lists-reset_list">
<BR>
<B>Reset Lead-Called-Status for this list -</B> This resets all leads in this list to N for "not called since last reset" and means that any lead can now be called if it is the right status as defined in the campaign screen.

<BR>
<A NAME="osdial_list-dnc">
<BR>
<B>OSDial DNC List -</B> This Do Not Call list contains every lead that has been set to a status of DNC in the system. Through the LISTS - ADD NUMBER TO DNC page you are able to manually add a number to this list so that it will not be called by campaigns that use the internal DNC list.



<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_INBOUND_GROUPS TABLE</FONT></B><BR><BR>
<A NAME="osdial_inbound_groups-group_id">
<BR>
<B>Group ID -</B> This is the short name of the inbound group, it is not editable after initial submission, must not contain any spaces and must be between 2 and 20 characters in length.

<BR>
<A NAME="osdial_inbound_groups-group_name">
<BR>
<B>Group Name -</B> This is the description of the group, it must be between 2 and 30 characters in length. Cannot include dashes, plusses or spaces .

<BR>
<A NAME="osdial_inbound_groups-group_color">
<BR>
<B>Group Color -</B> This is the color that displays in the OSDial client app when a call comes in on this group. It must be between 2 and 7 characters long. If this is a hex color definition you must remember to put a # at the beginning of the string or OSDial will not work properly.

<BR>
<A NAME="osdial_inbound_groups-active">
<BR>
<B>Active -</B> This determines whether this group show up in the selection box when a OSDial agent logs in.

<BR>
<A NAME="osdial_inbound_groups-web_form_address">
<BR>
<B>Web Form -</B> This is the custom address that clicking on one of the WEB FORM buttons in OSDial will take you to for calls that come in on this group.

<BR>
<A NAME="osdial_inbound_groups-next_agent_call">
<BR>
<B>Next Agent Call -</B> This determines which agent receives the next call that is available:
 <BR> &nbsp; - random: orders by the random update value in the osdial_live_agents table
 <BR> &nbsp; - oldest_call_start: orders by the last time an agent was sent a call. Results in agents receiving about the same number of calls overall.
 <BR> &nbsp; - oldest_call_finish: orders by the last time an agent finished a call. AKA agent waiting longest receives first call.
 <BR> &nbsp; - overall_user_level: orders by the user_level of the agent as defined in the osdial_users table a higher user_level will receive more calls.
 <BR> &nbsp; - inbound_group_rank: orders by the rank given to the agent for the specific inbound group. Highest to Lowest.
 <BR> &nbsp; - fewest_calls: orders by the number of calls received by an agent for that specific inbound group. Least calls first.
 <BR> &nbsp; - campaign_rank: orders by the rank given to the agent for the campaign. Highest to Lowest.
 <BR> &nbsp; - fewest_calls_campaign: orders by the number of calls received by an agent for the campaign. Least calls first.
<BR>
<A NAME="osdial_inbound_groups-fronter_display">
<BR>
<B>Fronter Display -</B> This field determines whether the inbound OSDial agent would have the fronter name - if there is one - displayed in the Status field when the call comes to the agent.

<BR>
<A NAME="osdial_inbound_groups-ingroup_script">
<BR>
<B>Campaign Script -</B> This menu allows you to choose the script that will appear on the agents screen for this campaign. Select NONE to show no script for this campaign.

<BR>
<A NAME="osdial_inbound_groups-get_call_launch">
<BR>
<B>Get Call Launch -</B> This menu allows you to choose whether you want to auto-launch the web-form page in a separate window, auto-switch to the SCRIPT tab or do nothing when a call is sent to the agent for this campaign. 

<BR>
<A NAME="osdial_inbound_groups-xferconf_a_dtmf">
<BR>
<B>Xfer-Conf DTMF -</B> These four fields allow for you to have two sets of Transfer Conference and DTMF presets. When the call or campaign is loaded, the agent.php script will show two buttons on the transfer-conference frame and auto-populate the number-to-dial and the send-dtmf fields when pressed. If you want to allow Consultative Transfers, a fronter to a closer, you can place CXFER as one of the number-to-dial presets and the proper dial string will be sent to do a Local Consultative Transfer, then the agent can just LEAVE-3WAY-CALL and move on to their next call. If you want to allow Blind transfers of customers to a OSDial AGI script for logging or an IVR, then place AXFER in the number-to-dial field. You can also specify an custom extension after the AXFER or CXFER, for instance if you want to do Internal Consultative transfers instead of Local you would put CXFER90009 in the number-to-dial field.

<BR>
<A NAME="osdial_inbound_groups-drop_call_seconds">
<BR>
<B>Drop Call Seconds -</B> The number of seconds from the time the customer line is picked up until the call is considered a DROP, only applies to outbound calls.

<BR>
<A NAME="osdial_inbound_groups-voicemail_ext">
<BR>
<B>Voicemail -</B> If defined, calls that would normally DROP would instead be directed to this voicemail box to hear and leave a message.

<BR>
<A NAME="osdial_inbound_groups-drop_message">
<BR>
<B>Drop Message -</B> If set to Y will play a message to customer after the Drop Call Seconds timeout is reached without being transferred to an agent. This setting will override sending to a voicemail box if this is set to Y.

<BR>
<A NAME="osdial_inbound_groups-drop_exten">
<BR>
<B>Drop Exten -</B> This is the dial plan extension that the desired Dropped call audio file is located at on your server.

<BR>
<A NAME="osdial_inbound_groups-call_time_id">
<BR>
<B>Call Time -</B> This is the call time scheme to use for this inbound group. Keep in mind that the time is based on the server time. Default is 24hours.

<BR>
<A NAME="osdial_inbound_groups-after_hours_action">
<BR>
<B>After Hours Action -</B> The action to perform if it is after hours as defined in the call time for this inbound group. HANGUP will immediately hangup the call, MESSASGE will play the file in the After Hours Message Filenam field, EXTENSION will send the call to the After Hours Extension in the dialplan and VOICEMAIL will send the call to the voicemail box listed in the After Hours Voicemail field. Default is MESSAGE.

<BR>
<A NAME="osdial_inbound_groups-after_hours_message_filename">
<BR>
<B>After Hours Message Filename -</B> The audio file located on the server to be played if the Action is set to MESSAGE. Default is vm-goodbye

<BR>
<A NAME="osdial_inbound_groups-after_hours_exten">
<BR>
<B>After Hours Extension -</B> The dialplan extension to send the call to if the Action is set to EXTENSION. Default is 8300.

<BR>
<A NAME="osdial_inbound_groups-after_hours_voicemail">
<BR>
<B>After Hours Voicemail -</B> The voicemail box to send the call to if the Action is set to VOICEMAIL.

<BR>
<A NAME="osdial_inbound_groups-welcome_message_filename">
<BR>
<B>Welcome Message Filename -</B> The audio file located on the server to be played when the call comes in. If set to ---NONE--- then no message will be played. Default is ---NONE---

<BR>
<A NAME="osdial_inbound_groups-moh_context">
<BR>
<B>Music On Hold Context -</B> The music on hold context to use when the customer is placed on hold. Default is default.

<BR>
<A NAME="osdial_inbound_groups-onhold_prompt_filename">
<BR>
<B>On Hold Prompt Filename -</B> The audio file located on the server to be played at a regular interval when the customer is on hold. Default is generic_hold. This audio file MUST be 9 seconds or less in length.

<BR>
<A NAME="osdial_inbound_groups-prompt_interval">
<BR>
<B>On Hold Prompt Interval -</B> The length of time in seconds to wait before playing the on hold prompt. Default is 60.

<BR>
<A NAME="osdial_inbound_groups-agent_alert_exten">
<BR>
<B>Agent Alert Extension -</B> The extension to send into the agent session to announce that a call is coming to the agent. This extension should have a Playback of an audio file. To not use this function set this to X. Default is X.

<BR>
<A NAME="osdial_inbound_groups-agent_alert_delay">
<BR>
<B>Agent Alert Delay -</B> The length of time in milliseconds to wait before sending the call to the agent after playing the on Agent Alert Extension. Default is 1000.

<BR>
<A NAME="osdial_inbound_groups-default_xfer_group">
<BR>
<B>Default Transfer Group -</B> This field is the default In-Group that will be automatically selected when the agent goes to the transfer-conference frame in their agent interface.



<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_REMOTE_AGENTS TABLE</FONT></B><BR><BR>
<A NAME="osdial_remote_agents-user_start">
<BR>
<B>Agent ID Start -</B> This is the starting Agent ID that is used when the remote agent entries are inserted into the system. If the Number of Lines is set higher than 1, this number is incremented by one until each line has an entry. Make sure you create a new OSDial agent account with a user level of 4 or great if you want them to be able to use the vdremote.php page for remote web access of this account.

<BR>
<A NAME="osdial_remote_agents-number_of_lines">
<BR>
<B>Number of Lines -</B> This defines how many remote agent entries the system creates, and determines how many lines it thinks it can safely send to the number below.

<BR>
<A NAME="osdial_remote_agents-server_ip">
<BR>
<B>Server IP -</B> A remote agent entry is only good for one specific server, here is where you select which server you want.

<BR>
<A NAME="osdial_remote_agents-conf_exten">
<BR>
<B>External Extension -</B> This is the number that you want the calls forwarded to. Make sure that it is a full dial plan number and that if you need a 9 at the beginning you put it in here. Test by dialing this number from a phone on the system.

<BR>
<A NAME="osdial_remote_agents-status">
<BR>
<B>Status -</B> Here is where you turn the remote agent on and off. As soon as the agent is Active the system assumes that it can send calls to it. It may take up to 30 seconds once you change the status to Inactive to stop receiving calls.

<BR>
<A NAME="osdial_remote_agents-campaign_id">
<BR>
<B>Campaign -</B> Here is where you select the campaign that these remote agents will be logged into. Inbound needs to use the CLOSER campaign and select the inbound campaigns below that you want to receive calls from.

<BR>
<A NAME="osdial_remote_agents-closer_campaigns">
<BR>
<B>Inbound Groups -</B> Here is where you select the inbound groups you want to receive calls from if you have selected the CLOSER campaign.


<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CAMPAIGN_LISTS</FONT></B><BR><BR>
<A NAME="osdial_campaign_lists">
<BR>
The lists within this campaign are listed here, whether they are active is denoted by the Y or N and you can go to the list screen by clicking on the list ID in the first column.


<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CAMPAIGN_STATUSES TABLE</FONT></B><BR><BR>
<A NAME="osdial_campaign_statuses">
<BR>
Through the use of custom campaign statuses, you can have statuses that only exist for a specific campaign. The Status must be 1-8 characters in length, the description must be 2-30 characters in length and Selectable defines whether it shows up in OSDial as a disposition. The human_answered field is used when calculating the drop percentage, or abandon rate. Setting human_answered to Y will use this status when counting the human-answered calls. The Category option allows you to group several statuses into a catogy that can be used for statistical analysis.



<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CAMPAIGN_HOTKEYS TABLE</FONT></B><BR><BR>
<A NAME="osdial_campaign_hotkeys">
<BR>
Through the use of custom campaign hot keys, agents that use the OSDial web-client can hang up and disposition calls just by pressing a single key on their keyboard.</B> There are two special HotKey options that you can use in conjunction with Alternate Phone number dialing, ALTPH2 - Alternate Phone Hot Dial and ADDR3-----Address3 Hot Dial allow an agent to use a hotkey to hang up their call, stay on the same lead, and dial another contact number from that lead. 





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_LEAD_RECYCLE TABLE</FONT></B><BR><BR>
<A NAME="osdial_lead_recycle">
<BR>
Through the use of lead recycling, you can call specific statuses of leads again at a specified interval without resetting the entire list. Lead recycling is campaign-specific and does not have to be a selected dialable status in your campaign. The attempt delay field is the number of seconds until the lead can be placed back in the hopper, this number must be at least 120 seconds. The attempt maximum field is the maximum number of times that a lead of this status can be attempted before the list needs to be reset, this number can be from 1 to 10. You can activate and deactivate a lead recycle entry with the provided links.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial AUTO ALT DIAL STATUSES</FONT></B><BR><BR>
<A NAME="osdial_auto_alt_dial_statuses">
<BR>
If the Auto Alt-Number Dialing field is set, then the leads that are dispositioned under these auto alt dial statuses will have their alt_phone and-or address3 fields dialed after any of these no-answer statuses are set.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial AGENT PAUSE CODES</FONT></B><BR><BR>
<A NAME="osdial_pause_codes">
<BR>
If the Agent Pause Codes Active field is set to active then the agents will be able to select from these pause codes when they click on the PAUSE button on their screens. This data is then stored in the OSDial agent log. The Pause code must contain only letters and numbers and be less than 7 characters long. The pause code name can be no longer than 30 characters.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_USER_GROUPS TABLE</FONT></B><BR><BR>
<A NAME="osdial_user_groups-user_group">
<BR>
<B>User Group -</B> This is the short name of a OSDial User group, try not to use any spaces or punctuation for this field. max 20 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_user_groups-group_name">
<BR>
<B>Group Name -</B> This is the description of the OSDial user group max of 40 characters.

<BR>
<A NAME="osdial_user_groups-allowed_campaigns">
<BR>
<B>Allowed Campaigns -</B> This is a selectable list of Campaigns to which members of this user group can log in to. The ALL-CAMPAIGNS option allows the users in this group to see and log in to any campaign on the system.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_SCRIPTS TABLE</FONT></B><BR><BR>
<A NAME="osdial_scripts-script_id">
<BR>
<B>Script ID -</B> This is the short name of a OSDial Script. This needs to be a unique identifier. Try not to use any spaces or punctuation for this field. max 10 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_scripts-script_name">
<B>Script Name -</B> This is the title of a OSDial Script. This is a short summary of the script. max 50 characters, minimum of 2 characters. There should be no spaces or punctuation of any kind in theis field.

<BR>
<A NAME="osdial_scripts-script_comments">
<B>Script Comments -</B> This is where you can place comments for a OSDial Script such as -changed to free upgrade on Sept 23-.  max 255 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_scripts-script_text">
<B>Script Text -</B> This is where you place the content of a OSDial Script. Minimum of 2 characters. You can have customer information be auto-populated in this script using "--A--field--B--" where field is one of the following fieldnames: vendor_lead_code, source_id, list_id, gmt_offset_now, called_since_last_reset, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, lead_id, campaign, phone_login, group, channel_group, SQLdate, epoch, uniqueid, customer_zap_channel, server_ip, SIPexten, session_id. For example, this sentence would print the persons name in it----<BR><BR>  Hello, can I speak with --A--first_name--B-- --A--last_name--B-- please? Well hello --A--title--B-- --A--last_name--B-- how are you today?<BR><BR> This would read----<BR><BR>Hello, can I speak with John Doe please? Well hello Mr. Doe how are you today?<BR><BR> You can also use an iframe to load a separate window within the SCRIPT tab, here is an example with prepopulated variables:

<DIV style="height:200px;width:400px;background:white;overflow:scroll;font-size:12px;font-family:sans-serif;" id=iframe_example>
&#60;iframe src="http://localhost/test_OSDial_output.php?lead_id=--A--lead_id--B--&#38;vendor_id=--A--vendor_lead_code--B--&#38;list_id=--A--list_id--B--&#38;gmt_offset_now=--A--gmt_offset_now--B--&#38;phone_code=--A--phone_code--B--&#38;phone_number=--A--phone_number--B--&#38;title=--A--title--B--&#38;first_name=--A--first_name--B--&#38;middle_initial=--A--middle_initial--B--&#38;last_name=--A--last_name--B--&#38;address1=--A--address1--B--&#38;address2=--A--address2--B--&#38;address3=--A--address3--B--&#38;city=--A--city--B--&#38;state=--A--state--B--&#38;province=--A--province--B--&#38;postal_code=--A--postal_code--B--&#38;country_code=--A--country_code--B--&#38;gender=--A--gender--B--&#38;date_of_birth=--A--date_of_birth--B--&#38;alt_phone=--A--alt_phone--B--&#38;email=--A--email--B--&#38;security_phrase=--A--security_phrase--B--&#38;comments=--A--comments--B--&#38;user=--A--user--B--&#38;campaign=--A--campaign--B--&#38;phone_login=--A--phone_login--B--&#38;fronter=--A--fronter--B--&#38;closer=--A--user--B--&#38;group=--A--group--B--&#38;channel_group=--A--group--B--&#38;SQLdate=--A--SQLdate--B--&#38;epoch=--A--epoch--B--&#38;uniqueid=--A--uniqueid--B--&#38;customer_zap_channel=--A--customer_zap_channel--B--&#38;server_ip=--A--server_ip--B--&#38;SIPexten=--A--SIPexten--B--&#38;session_id=--A--session_id--B--&#38;phone=--A--phone--B--" style="width:580;height:290;background-color:transparent;" scrolling="auto" frameborder="0" allowtransparency="true" id="popupFrame" name="popupFrame" width="460" height="290" STYLE="z-index:17"&#62;
&#60;/iframe&#62;
</DIV>

<BR>
<A NAME="osdial_scripts-active">
<BR>
<B>Active -</B> This determines whether this script can be selected to be used by a campaign.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_LEAD_FILTERS TABLE</FONT></B><BR><BR>
<A NAME="osdial_lead_filters-lead_filter_id">
<BR>
<B>Filter ID -</B> This is the short name of a OSDial Lead Filter. This needs to be a unique identifier. Do not use any spaces or punctuation for this field. max 10 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_lead_filters-lead_filter_name">
<B>Filter Name -</B> This is a more descriptive name of the Filter. This is a short summary of the filter. max 30 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_lead_filters-lead_filter_comments">
<B>Filter Comments -</B> This is where you can place comments for a OSDial Filter such as -calls all California leads-.  max 255 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_lead_filters-lead_filter_sql">
<B>Filter SQL -</B> This is where you place the SQL query fragment that you want to filter by. do not begin or end with an AND, that will be added by the hopper cron script automatically. an example SQL query that would work here is- called_count > 4 and called_count < 8 -.





<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_CALL TIMES TABLE</FONT></B><BR><BR>
<A NAME="osdial_call_times-call_time_id">
<BR>
<B>Call Time ID -</B> This is the short name of a OSDial Call Time Definition. This needs to be a unique identifier. Do not use any spaces or punctuation for this field. max 10 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_call_times-call_time_name">
<B>Call Time Name -</B> This is a more descriptive name of the Call Time Definition. This is a short summary of the Call Time definition. max 30 characters, minimum of 2 characters.

<BR>
<A NAME="osdial_call_times-call_time_comments">
<B>Call Time Comments -</B> This is where you can place comments for a OSDial Call Time Definition such as -10am to 4pm with extra call state restrictions-.  max 255 characters.

<BR>
<A NAME="osdial_call_times-ct_default_start">
<B>Default Start and Stop Times -</B> This is the default time that calling will be allowed to be started or stopped within this call time definition if the day-of-the-week start time is not defined. 0 is midnight. To prevent calling completely set this field to 2400 and set the Default Stop time to 2400. To allow calling 24 hours a day set the start time to 0 and the stop time to 2400.

<BR>
<A NAME="osdial_call_times-ct_sunday_start">
<B>Weekday Start and Stop Times -</B> These are the custom times per day that can be set for the call time definition. same rules apply as with the Default start and stop times.

<BR>
<A NAME="osdial_call_times-ct_state_call_times">
<B>State Call Time Definitions -</B> This is the list of State specific call time definitions that are followed in this Call Time Definition.

<BR>
<A NAME="osdial_call_times-state_call_time_state">
<B>State Call Time State -</B> This is the two letter code for the state that this calling time definition is for. For this to be in effect the local call time that is set in the campaign must have this state call time record in it as well as all of the leads having two letter state codes in them.




<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial LIST LOADER FUNCTIONALITY</FONT></B><BR><BR>
<A NAME="osdial_list_loader">
<BR>
The OSDial basic web-based lead loader is designed simply to take a lead file - up to 8MB in size - that is either tab or pipe delimited and load it into the osdial_list table. The lead loader allows for field choosing and TXT- Plain Text, CSV- Comma Separated Values and XLS- Excel file formats. The lead loader does not do data validation, but it does allow you to check for duplicates in itself, within the campaign or within the entire system. Also, make sure that you have created the list that these leads are to be under so that you can use them. Here is a list of the fields in their proper order for the lead files:
	<OL>
	<LI>Vendor Lead Code - shows up in the Vendor ID field of the GUI
	<LI>Source Code - internal use only for admins and DBAs
	<LI>List ID - the list number that these leads will show up under
	<LI>Phone Code - the prefix for the phone number - 1 for US, 01144 for UK, 01161 for AUS, etc
	<LI>Phone Number - must be at least 8 digits long
	<LI>Title - title of the customer - Mr. Ms. Mrs, etc...
	<LI>First Name
	<LI>Middle Initial
	<LI>Last Name
	<LI>Address Line 1
	<LI>Address Line 2
	<LI>Address Line 3
	<LI>City
	<LI>State - limited to 2 characters
	<LI>Province
	<LI>Postal Code
	<LI>Country
	<LI>Gender
	<LI>Date of Birth
	<LI>Alternate Phone Number
	<LI>Email Address
	<LI>Security Phrase
	<LI>Comments
	</OL>

<BR>NOTES: The Excel Lead loader functionality is enabled by a series of perl scripts and needs to have a properly configured /etc/astguiclient.conf file in place on the web server. Also, a couple perl modules must be loaded for it to work as well - OLE-Storage_Lite and Spreadsheet-ParseExcel. You can check for runtime errors in these by looking at your apache error_log file. Also, for duplication checks against gampaign lists, the list that has new leads going into it does need to be created in the system before you start to load the leads.




<BR><BR><BR><BR>






<B><FONT SIZE=3>PHONES TABLE</FONT></B><BR><BR>
<A NAME="phones-extension">
<BR>
<B>Phone extension -</B> This field is where you put the phones name as it appears to Asterisk not including the protocol or slash at the beginning. For Example: for the SIP phone SIP/test101 the Phone extension would be test101. Also, for IAX2 phones make sure you use the full phones name: IAX2/IAXphone1@IAXphone1 would be IAXphone1@IAXphone1. For Zap phones make sure you put the full channel: Zap/25-1 would be 25-1.  Another note, make sure you set the Protocol below correctly for your type of phone.

<BR>
<A NAME="phones-dialplan_number">
<BR>
<B>Dial Plan Number -</B> This field is for the number you dial to have the phone ring. This number is defined in the extensions.conf file of your Asterisk server

<BR>
<A NAME="phones-voicemail_id">
<BR>
<B>Voicemail Box -</B> This field is for the voicemail box that the messages go to for the user of this phone. We use this to check for voicemail messages and for the user to be able to use the VOICEMAIL button on astGUIclient app.

<BR>
<A NAME="phones-outbound_cid">
<BR>
<B>Outbound CallerID -</B> This field is where you would enter the callerID number that you would like to appear on outbound calls placed form the astguiclient web-client. This does not work on RBS, non-PRI, T1/E1s.

<BR>
<A NAME="phones-phone_ip">
<BR>
<B>Phone IP address -</B> This field is for the phone's IP address if it is a VOIP phone. This is an optional field

<BR>
<A NAME="phones-computer_ip">
<BR>
<B>Computer IP address -</B> This field is for the user's computer IP address. This is an optional field

<BR>
<A NAME="phones-server_ip">
<BR>
<B>Server IP -</B> This menu is where you select which server the phone is active on.

<BR>
<A NAME="phones-login">
<BR>
<B>Login -</B> The login used for the phone user to login to the client applications.

<BR>
<A NAME="phones-pass">
<BR>
<B>Password -</B>  The password used for the phone user to login to the client applications.

<BR>
<A NAME="phones-status">
<BR>
<B>Status -</B> The status of the phone in the system, ACTIVE and ADMIN allow for GUI clients to work. ADMIN allows access to this administrative web site. All other statuses do not allow GUI or Admin web access.

<BR>
<A NAME="phones-active">
<BR>
<B>Active Account -</B> Whether the phone is active to put it in the list in the GUI client.

<BR>
<A NAME="phones-phone_type">
<BR>
<B>Phone Type -</B> Purely for administrative notes.

<BR>
<A NAME="phones-fullname">
<BR>
<B>Full Name -</B> Used by the GUIclient in the list of active phones.

<BR>
<A NAME="phones-company">
<BR>
<B>Company -</B> Purely for administrative notes.

<BR>
<A NAME="phones-picture">
<BR>
<B>Picture -</B> Not yet Implemented.

<BR>
<A NAME="phones-messages">
<BR>
<B>New Messages -</B> Number of new voicemail messages for this phone on the Asterisk server.

<BR>
<A NAME="phones-old_messages">
<BR>
<B>Old Messages -</B> Number of old voicemail messages for this phone on the Asterisk server.

<BR>
<A NAME="phones-protocol">
<BR>
<B>Client Protocol -</B> The protocol that the phone uses to connect to the Asterisk server: SIP, IAX2, Zap . Also, there is EXTERNAL for remote dial numbers or speed dial numbers that you want to list as phones.

<BR>
<A NAME="phones-local_gmt">
<BR>
<B>Local GMT -</B> The difference from Greenwich Mean time, or ZULU time where the phone is located. DO NOT ADJUST FOR DAYLIGHT SAVINGS TIME. This is used by the OSDial campaign to accurately display the time and customer time.

<BR>
<A NAME="phones-ASTmgrUSERNAME">
<BR>
<B>Manager Login -</B> This is the login that the GUI clients for this phone will use to access the Database where the server data resides.

<BR>
<A NAME="phones-ASTmgrSECRET">
<BR>
<B>Manager Secret -</B> This is the password that the GUI clients for this phone will use to access the Database where the server data resides.

<BR>
<A NAME="phones-login_user">
<BR>
<B>OSDial Default Agent -</B> This is to place a default value in the OSDial agent field whenever this phone user opens the OSDial client app. Leave blank for no agent.

<BR>
<A NAME="phones-login_pass">
<BR>
<B>OSDial Default Pass -</B> This is to place a default value in the OSDial password field whenever this phone user opens the OSDial client app. Leave blank for no pass.

<BR>
<A NAME="phones-login_campaign">
<BR>
<B>OSDial Default Campaign -</B> This is to place a default value in the OSDial campaign field whenever this phone user opens the OSDial client app. Leave blank for no campaign.

<BR>
<A NAME="phones-park_on_extension">
<BR>
<B>Park Exten -</B> This is the default Parking extension for the client apps. Verify that a different one works before you change this.

<BR>
<A NAME="phones-conf_on_extension">
<BR>
<B>Conf Exten -</B> This is the default Conference park extension for the client apps. Verify that a different one works before you change this.

<BR>
<A NAME="phones-OSDial_park_on_extension">
<BR>
<B>OSDial Park Exten -</B> This is the default Parking extension for OSDial client app. Verify that a different one works before you change this.

<BR>
<A NAME="phones-OSDial_park_on_filename">
<BR>
<B>OSDial Park File -</B> This is the default OSDial park extension file name for the client apps. Verify that a different one works before you change this. limited to 10 characters.

<BR>
<A NAME="phones-monitor_prefix">
<BR>
<B>Monitor Prefix -</B> This is the dial plan prefix for monitoring of Zap channels automatically within the astGUIclient app. Only change according to the extensions.conf ZapBarge extensions records.

<BR>
<A NAME="phones-recording_exten">
<BR>
<B>Recording Exten -</B> This is the dial plan extension for the recording extension that is used to drop into meetme conferences to record them. It usually lasts upto one hour if not stopped. verify with extensions.conf file before changing.

<BR>
<A NAME="phones-voicemail_exten">
<BR>
<B>VMAIL Main Exten -</B> This is the dial plan extension going to check your voicemail. verify with extensions.conf file before changing.

<BR>
<A NAME="phones-voicemail_dump_exten">
<BR>
<B>VMAIL Dump Exten -</B> This is the dial plan prefix used to send calls directly to a user's voicemail from a live call in the astGUIclient app. verify with extensions.conf file before changing.

<BR>
<A NAME="phones-ext_context">
<BR>
<B>Exten Context -</B> This is the dial plan context that this phone primarily uses. It is assumed that all numbers dialed by the client apps are using this context so it is a good idea to make sure this is the most wide context possible. verify with extensions.conf file before changing.

<BR>
<A NAME="phones-dtmf_send_extension">
<BR>
<B>DTMF send Channel -</B> This is the channel string used to send DTMF sounds into meetme conferences from the client apps. Verify the exten and context with the extensions.conf file.

<BR>
<A NAME="phones-call_out_number_group">
<BR>
<B>Outbound Call Group -</B> This is the channel group that outbound calls from this phone are placed out of. There are a couple routines in the client apps that use this. For Zap channels you want to use something like Zap/g2 , for IAX2 trunks you would want to use the full IAX prefix like IAX2/VICItest1:secret@10.10.10.15:4569. Verify the trunks with the extensions.conf file, it is usually what you have defined as the TRUNK global variable at the top of the file.

<BR>
<A NAME="phones-client_browser">
<BR>
<B>Browser Location -</B> This is applicable to only UNIX/LINUX clients, the absolute path to Mozilla or Firefox browser on the machine. verify this by launching it manually.

<BR>
<A NAME="phones-install_directory">
<BR>
<B>Install Directory -</B> This is the place where the astGUIclient and OSDial scripts are located on your machine. For Win32 it should be something like C:\AST_VICI and for UNIX it should be something like /usr/local/perl_TK. verify this manually.

<BR>
<A NAME="phones-local_web_callerID_URL">
<BR>
<B>CallerID URL -</B> This is the web address of the page used to do custom callerID lookups. default testing address is: http://astguiclient.sf.net/test_callerid_output.php

<BR>
<A NAME="phones-OSDial_web_URL">
<BR>
<B>OSDial Default URL -</B> This is the web address of the page used to do custom OSDial Web Form queries. default testing address is: http://localhost/test_OSDial_output.php

<BR>
<A NAME="phones-AGI_call_logging_enabled">
<BR>
<B>Call Logging -</B> This is set to true if the call_log.agi file is in place in the extensions.conf file for all outbound and hang up 'h' extensions to log all calls. This should always be 1 because it is manditory for many astGUIclient and OSDial features to work properly.

<BR>
<A NAME="phones-user_switching_enabled">
<BR>
<B>Agent Switching -</B> Set to true to allow agent to switch to another agent account. NOTE: If agent switches they can initiate recording on the new agent's phone conversation

<BR>
<A NAME="phones-conferencing_enabled">
<BR>
<B>Conferencing -</B> Set to true to allow user to start conference calls with upto six external lines.

<BR>
<A NAME="phones-admin_hangup_enabled">
<BR>
<B>Admin Hang Up -</B> Set to true to allow user to be able to hang up any line at will through astGUIclient. Good idea only to enable this for Admin users.

<BR>
<A NAME="phones-admin_hijack_enabled">
<BR>
<B>Admin Hijack -</B> Set to true to allow user to be able to grab and redirect to their extension any line at will through astGUIclient. Good idea only to enable this for Admin users. But is very useful for Managers.

<BR>
<A NAME="phones-admin_monitor_enabled">
<BR>
<B>Admin Monitor -</B> Set to true to allow user to be able to grab and redirect to their extension any line at will through astGUIclient. Good idea only to enable this for Admin users. But is very useful for Managers and as a training tool.

<BR>
<A NAME="phones-call_parking_enabled">
<BR>
<B>Call Park -</B> Set to true to allow user to be able to park calls on astGUIclient hold to be picked up by any other astGUIclient user on the system. Calls stay on hold for upto a half hour then hang up. Usually enabled for all.

<BR>
<A NAME="phones-updater_check_enabled">
<BR>
<B>Updater Check -</B> Set to true to display a popup warning that the updater time has not changed in 20 seconds. Useful for Admin users.

<BR>
<A NAME="phones-AFLogging_enabled">
<BR>
<B>AF Logging -</B> Set to true to log many actions of astGUIclient usage to a text file on the user's computer.

<BR>
<A NAME="phones-QUEUE_ACTION_enabled">
<BR>
<B>Queue Enabled -</B> Set to true to have client apps use the Asterisk Central Queue system. Required for OSDial and recommended for all users.

<BR>
<A NAME="phones-CallerID_popup_enabled">
<BR>
<B>CallerID Popup -</B> Set to true to allow for numbers defined in the extensions.conf file to send CallerID popup screens to astGUIclient users.

<BR>
<A NAME="phones-voicemail_button_enabled">
<BR>
<B>VMail Button -</B> Set to true to display the VOICEMAIL button and the messages count display on astGUIclient.

<BR>
<A NAME="phones-enable_fast_refresh">
<BR>
<B>Fast Refresh -</B> Set to true to enable a new rate of refresh of call information for the astGUIclient. Default disabled rate is 1000 ms ,1 second. Can increase system load if you lower this number.

<BR>
<A NAME="phones-fast_refresh_rate">
<BR>
<B>Fast Refresh Rate -</B> in milliseconds. Only used if Fast Refresh is enabled. Default disabled rate is 1000 ms ,1 second. Can increase system load if you lower this number.

<BR>
<A NAME="phones-enable_persistant_mysql">
<BR>
<B>Persistant MySQL -</B> If enabled the astGUIclient connection will remain connected instead of connecting every second. Useful if you have a fast refresh rate set. It will increase the number of connections on your MySQL machine.

<BR>
<A NAME="phones-auto_dial_next_number">
<BR>
<B>Auto Dial Next Number -</B> If enabled the OSDial client will dial the next number on the list automatically upon disposition of a call unless they selected to "Stop Dialing" on the disposition screen.

<BR>
<A NAME="phones-VDstop_rec_after_each_call">
<BR>
<B>Stop Rec after each call -</B> If enabled the OSDial client will stop whatever recording is going on after each call has been dispositioned. Useful if you are doing a lot of recording or you are using a web form to trigger recording.

<BR>
<A NAME="phones-enable_sipsak_messages">
<BR>
<B>Enable SIPSAK Messages -</B> If enabled the server will send messages to the SIP phone to display on the phone LCD display when logged into OSDial. Feature only works with SIP phones and requires sipsak application to be installed on the web server. Default is 0.

<BR>
<A NAME="phones-DBX_server">
<BR>
<B>DBX Server -</B> The MySQL database server that this user should be connecting to.

<BR>
<A NAME="phones-DBX_database">
<BR>
<B>DBX Database -</B> The MySQL database that this user should be connecting to. Default is asterisk.

<BR>
<A NAME="phones-DBX_user">
<BR>
<B>DBX User -</B> The MySQL user login that this user should be using when connecting. Default is cron.

<BR>
<A NAME="phones-DBX_pass">
<BR>
<B>DBX Pass -</B> The MySQL user password that this user should be using when connecting. Default is 1234.

<BR>
<A NAME="phones-DBX_port">
<BR>
<B>DBX Port -</B> The MySQL TCP port that this user should be using when connecting. Default is 3306.

<BR>
<A NAME="phones-DBY_server">
<BR>
<B>DBY Server -</B> The MySQL database server that this user should be connecting to. Secondary server, not used currently.

<BR>
<A NAME="phones-DBY_database">
<BR>
<B>DBY Database -</B> The MySQL database that this user should be connecting to. Default is asterisk. Secondary server, not used currently.

<BR>
<A NAME="phones-DBY_user">
<BR>
<B>DBY User -</B> The MySQL user login that this user should be using when connecting. Default is cron. Secondary server, not used currently.

<BR>
<A NAME="phones-DBY_pass">
<BR>
<B>DBY Pass -</B> The MySQL user password that this user should be using when connecting. Default is 1234. Secondary server, not used currently.

<BR>
<A NAME="phones-DBY_port">
<BR>
<B>DBY Port -</B> The MySQL TCP port that this user should be using when connecting. Default is 3306. Secondary server, not used currently.


<BR><BR><BR><BR>

<B><FONT SIZE=3>SERVERS TABLE</FONT></B><BR><BR>
<A NAME="servers-server_id">
<BR>
<B>Server ID -</B> This field is where you put the Asterisk servers name, doesnt have to be an official domain sub, just a nickname to identify the server to Admin users.

<BR>
<A NAME="servers-server_description">
<BR>
<B>Server Description -</B> The field where you use a small phrase to describe the Asterisk server.

<BR>
<A NAME="servers-server_ip">
<BR>
<B>Server IP Address -</B> The field where you put the Network IP address of the Asterisk server.

<BR>
<A NAME="servers-active">
<BR>
<B>Active -</B> Set whether the Asterisk server is active or inactive.

<BR>
<A NAME="servers-asterisk_version">
<BR>
<B>Asterisk Version -</B> Set the version of Asterisk that you have installed on this server. Examples: '1.2', '1.0.8', '1.0.7', 'CVS_HEAD', 'REALLY OLD', etc... This is used because versions 1.0.8 and 1.0.9 have a different method of dealing with Local/ channels, a bug that has been fixed in CVS v1.0, and need to be treated differently when handling their Local/ channels. Also, current CVS_HEAD and the 1.2 release tree uses different manager and command output so it must be treated differently as well.

<BR>
<A NAME="servers-max_osdial_trunks">
<BR>
<B>Max OSDial Trunks -</B> This field will determine the maximum number of lines that the OSDial auto-dialer will attempt to call on this server. If you want to dedicate two full PRI T1s to dialing on a server then you would set this to 46. Default is 96.

<BR>
<A NAME="servers-telnet_host">
<BR>
<B>Telnet Host -</B> This is the address or name of the Asterisk server and is how the manager applications connect to it from where they are running. If they are running on the Asterisk server, then the default of 'localhost' is fine.

<BR>
<A NAME="servers-telnet_port">
<BR>
<B>Telnet Port -</B> This is the port of the Asterisk server Manager connection and is how the manager applications connect to it from where they are running. The default of '5038' is fine for a standard install.

<BR>
<A NAME="servers-ASTmgrUSERNAME">
<BR>
<B>Manager User -</B> The username or login used to connect genericly to the Asterisk server manager. Default is 'cron'

<BR>
<A NAME="servers-ASTmgrSECRET">
<BR>
<B>Manager Secret -</B> The secret or password used to connect genericly to the Asterisk server manager. Default is '1234'

<BR>
<A NAME="servers-ASTmgrUSERNAMEupdate">
<BR>
<B>Manager Update User -</B> The username or login used to connect to the Asterisk server manager optimized for the Update scripts. Default is 'updatecron' and assumes the same secret as the generic user.

<BR>
<A NAME="servers-ASTmgrUSERNAMElisten">
<BR>
<B>Manager Listen User -</B> The username or login used to connect to the Asterisk server manager optimized for scripts that only listen for output. Default is 'listencron' and assumes the same secret as the generic user.

<BR>
<A NAME="servers-ASTmgrUSERNAMEsend">
<BR>
<B>Manager Send User -</B> The username or login used to connect to the Asterisk server manager optimized for scripts that only send Actions to the manager. Default is 'sendcron' and assumes the same secret as the generic user.

<BR>
<A NAME="servers-local_gmt">
<BR>
<B>Server GMT offset -</B> The difference in hours from GMT time not adjusted for Daylight-Savings-Time of the server. Default is '-5'

<BR>
<A NAME="servers-voicemail_dump_exten">
<BR>
<B>VMail Dump Exten -</B> The extension prefix used on this server to send calls directly through agc to a specific voicemail box. Default is '85026666666666'

<BR>
<A NAME="servers-answer_transfer_agent">
<BR>
<B>OSDial AD extension -</B> The default extension if none is present in the campaign to send calls to for OSDial auto dialing. Default is '8365'

<BR>
<A NAME="servers-ext_context">
<BR>
<B>Default Context -</B> The default dial plan context used for scripts that operate for this server. Default is 'default'

<BR>
<A NAME="servers-sys_perf_log">
<BR>
<B>System Performance -</B> Setting this option to Y will enable logging of system performance stats for the server machine including system load, system processes and Asterisk channels in use. Default is N.

<BR>
<A NAME="servers-vd_server_logs">
<BR>
<B>Server Logs -</B> Setting this option to Y will enable logging of all OSDial related scripts to their text log files. Setting this to N will stop writing logs to files for these processes, also the screen logging of asterisk will be disabled if this is set to N when Asterisk is started. Default is Y.

<BR>
<A NAME="servers-agi_output">
<BR>
<B>AGI Output -</B> Setting this option to NONE will disable output from all OSDial related AGI scripts. Setting this to STDERR will send the AGI output to the Asterisk CLI. Setting this to FILE will send the output to a file in the logs directory. Setting this to BOTH will send output to both the Asterisk CLI and a log file. Default is FILE.

<BR>
<A NAME="servers-osdial_balance_active">
<BR>
<B>OSDial Balance Dialing -</B> Setting this field to Y will allow the server to place balance calls for campaigns in OSDial so that the defined dial level can be met even if there are no agents logged into that campaign on this server. Default is N.

<BR>
<A NAME="servers-balance_trunks_offlimits">
<BR>
<B>OSDial Balance Offlimits -</B> This setting defines the number of trunks to not allow OSDial balance dialing to use. For example if you have 40 max OSDial trunks and balance offlimits is set to 10 you will only be able to use 30 trunk lines for OSDial balance dialing. Default is 0.


<BR><BR><BR><BR>

<B><FONT SIZE=3>CONFERENCES TABLE</FONT></B><BR><BR>
<A NAME="conferences-conf_exten">
<BR>
<B>Conference Number -</B> This field is where you put the meetme conference dialpna number. It is also recommended that the meetme number in meetme.conf matches this number for each entry. This is for the conferences in astGUIclient and is used for leave-3way-call functionality in OSDial.

<BR>
<A NAME="conferences-server_ip">
<BR>
<B>Server IP -</B> The menu where you select the Asterisk server that this conference will be on.




<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_SERVER_TRUNKS TABLE</FONT></B><BR><BR>
<A NAME="osdial_server_trunks">
<BR>
OSDial Server Trunks allows you to restrict the outgoing lines that are used on this server for campaign dialing on a per-campaign basis. You have the option to reserve a specific number of lines to be used by only one campaign as well as allowing that campaign to run over its reserved lines into whatever lines remain open, as long at the total lines used by OSDial on this server is less than the Max OSDial Trunks setting. Not having any of these records will allow the campaign that dials the line first to have as many lines as it can get under the Max OSDial Trunks setting.





<BR><BR><BR><BR>

<B><FONT SIZE=3>SYSTEM_SETTINGS TABLE</FONT></B><BR><BR>
<A NAME="settings-use_non_latin">
<BR>
<B>Use Non-Latin -</B> This option allows you to default the web display script to use UTF8 characters and not do any latin-character-family regular expression filtering or display formatting. Default is 0.

<BR>
<A NAME="settings-webroot_writable">
<BR>
<B>Webroot Writable -</B> This setting allows you to define whether temp files and authentication files should be placed in the webroot on your web server. Default is 1.

<BR>
<A NAME="settings-enable_queuemetrics_logging">
<BR>
<B>Enable QueueMetrics Logging -</B> This setting allows you to define whether OSDial will insert log entries into the queue_log database table as Asterisk Queues activity does. QueueMetrics is a standalone, closed-source statistical analysis program. You must have QueueMetrics already installed and configured before enabling this feature. Default is 0.

<BR>
<A NAME="settings-queuemetrics_server_ip">
<BR>
<B>QueueMetrics Server IP -</B> This is the IP address of the database for your QueueMetrics installation.

<BR>
<A NAME="settings-queuemetrics_dbname">
<BR>
<B>QueueMetrics Database Name -</B> This is the database name for your QueueMetrics database.

<BR>
<A NAME="settings-queuemetrics_login">
<BR>
<B>QueueMetrics Database Login -</B> This is the user name used to log in to your QueueMetrics database.

<BR>
<A NAME="settings-queuemetrics_pass">
<BR>
<B>QueueMetrics Database Password -</B> This is the password used to log in to your QueueMetrics database.

<BR>
<A NAME="settings-queuemetrics_url">
<BR>
<B>QueueMetrics URL -</B> This is the URL or web site address used to get to your QueueMetrics installation.

<BR>
<A NAME="settings-queuemetrics_log_id">
<BR>
<B>QueueMetrics Log ID -</B> This is the server ID that all OSDial logs going into the QueueMetrics database will use as an identifier for each record.

<BR>
<A NAME="settings-queuemetrics_eq_prepend">
<BR>
<B>QueueMetrics EnterQueue Prepend -</B> This field is used to allow for prepending of one of the osdial_list data fields in front of the phone number of the customer for customized QueueMetrics reports. Default is NONE to not populate anything.

<BR>
<A NAME="settings-osdial_agent_disable">
<BR>
<B>OSDial Agent Disable Display -</B> This field is used to select when to show an agent when their session has been disabled by the system, a manager action or by an external measure. The NOT_ACTIVE setting will disable the message on the agents screen. The LIVE_AGENT setting will only display the disabled message when the agents osdial_auto_calls record has been removed, such as during a force logout or emergency logout. 

<BR>
<A NAME="settings-allow_sipsak_messages">
<BR>
<B>Allow SIPSAK Messages -</B> If set to 1, this will allow the phones table setting to work properly, the server will send messages to the SIP phone to display on the phone LCD display when logged into OSDial. This feature only works with SIP phones and requires sipsak application to be installed on the web server. Default is 0. 

<BR>
<A NAME="settings-admin_home_url">
<BR>
<B>Admin Home URL -</B> This is the URL or web site address that you will go to if you click on the HOME link at the top of the admin.php page.

<BR>
<A NAME="settings-enable_agc_xfer_log">
<BR>
<B>Enable Agent Transfer Logfile -</B> This option will log to a text logfile on the webserver every time a call is transferred to an agent. Default is 0, disabled.


<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_Statuses Table</FONT></B><BR><BR>
<A NAME="osdial_statuses">
<BR>
Through the use of system statuses, you can have statuses that exist for campaign and in-group. The Status must be 1-6 characters in length, the description must be 2-30 characters in length and Selectable defines whether it shows up in OSDial as an agent disposition. The human_answered field is used when calculating the drop percentage, or abandon rate. Setting human_answered to Y will use this status when counting the human-answered calls. The Category option allows you to group several statuses into a catogy that can be used for statistical analysis.</B>


<BR><BR><BR><BR>

<B><FONT SIZE=3>OSDial_Status_Categories Table</FONT></B><BR><BR>
<A NAME="osdial_status_categories">
<BR>
Through the use of system status categories, you can group together statuses to allow for statistical analysis on a group of statuses. The Category ID must be 2-20 characters in length with no spaces, the name must be 2-50 characters in length, the description is optional and Time On OSDial Display defines whether that status will be one of the upto 4 statuses that can be calculated and displayed on the Time On OSDial Real-Time report.</B>




<BR><BR><BR><BR><BR><BR><BR><BR>
<BR><BR><BR><BR><BR><BR><BR><BR>

</TD></TR></TABLE></BODY></HTML>
<?
exit;

#### END HELP SCREENS
}


######################################################################################################
######################################################################################################
#######   7 series, filter count preview and script preview
######################################################################################################
######################################################################################################




######################
# ADD=73 view dialable leads from a filter and a campaign
######################

if ($ADD==73)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "</title>\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT dial_statuses,local_call_time,lead_filter_id from vicidial_campaigns where campaign_id='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$dial_statuses =		$row[0];
	$local_call_time =		$row[1];
	if ($lead_filter_id=='')
		{
		$lead_filter_id =	$row[2];
		if ($lead_filter_id=='') 
			{
			$lead_filter_id='NONE';
			}
		}

	$stmt="SELECT list_id,active,list_name from vicidial_lists where campaign_id='$campaign_id'";
	$rslt=mysql_query($stmt, $link);
	$lists_to_print = mysql_num_rows($rslt);
	$camp_lists='';
	$o=0;
	while ($lists_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$o++;
	if (ereg("Y", $rowx[1])) {$camp_lists .= "'$rowx[0]',";}
	}
	$camp_lists = eregi_replace(".$","",$camp_lists);

	$filterSQL = $filtersql_list[$lead_filter_id];
	$filterSQL = eregi_replace("^and|and$|^or|or$","",$filterSQL);
	if (strlen($filterSQL)>4)
		{$fSQL = "and $filterSQL";}
	else
		{$fSQL = '';}


	echo "<BR><BR>\n";
	echo "<B>Show Dialable Leads Count</B> -<BR><BR>\n";
	echo "<B>CAMPAIGN:</B> $campaign_id<BR>\n";
	echo "<B>LISTS:</B> $camp_lists<BR>\n";
	echo "<B>STATUSES:</B> $dial_statuses<BR>\n";
	echo "<B>FILTER:</B> $lead_filter_id<BR>\n";
	echo "<B>CALL TIME:</B> $local_call_time<BR><BR>\n";

	### call function to calculate and print dialable leads
	dialable_leads($DB,$link,$local_call_time,$dial_statuses,$camp_lists,$fSQL);

	echo "<BR><BR>\n";
	echo "</BODY></HTML>\n";

	exit;
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=7111111 view sample script with test variables
######################

if ($ADD==7111111)
{
	##### TEST VARIABLES #####
	$vendor_lead_code = 'VENDOR:LEAD;CODE';
	$list_id = 'LISTID';
	$gmt_offset_now = 'GMTOFFSET';
	$phone_code = '1';
	$phone_number = '7275551212';
	$title = 'Mr.';
	$first_name = 'JOHN';
	$middle_initial = 'Q';
	$last_name = 'PUBLIC';
	$address1 = '1234 Main St.';
	$address2 = 'Apt. 3';
	$address3 = 'ADDRESS3';
	$city = 'CHICAGO';
	$state = 'IL';
	$province = 'PROVINCE';
	$postal_code = '33760';
	$country_code = 'USA';
	$gender = 'M';
	$date_of_birth = '1970-01-01';
	$alt_phone = '3125551212';
	$email = 'test@test.com';
	$security_phrase = 'SECUTIRY';
	$comments = 'COMMENTS FIELD';
	$RGfullname = 'JOE AGENT';
	$RGuser = '6666';
	$RGlead_id = '1234';
	$RGcampaign = 'TESTCAMP';
	$RGphone_login = 'gs102';
	$RGgroup = 'TESTCAMP';
	$RGchannel_group = 'TESTCAMP';
	$RGSQLdate = date("Y-m-d H:i:s");
	$RGepoch = date("U");
	$RGuniqueid = '1163095830.4136';
	$RGcustomer_zap_channel = 'Zap/1-1';
	$RGserver_ip = '10.10.10.15';
	$RGSIPexten = 'SIP/gs102';
	$RGsession_id = '8600051';

echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

$stmt="SELECT * from vicidial_scripts where script_id='$script_id';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$script_name =		$row[1];
$script_text =		$row[3];

if (eregi("iframe src",$script_text))
	{
	$vendor_lead_code = eregi_replace(' ','+',$vendor_lead_code);
	$list_id = eregi_replace(' ','+',$list_id);
	$gmt_offset_now = eregi_replace(' ','+',$gmt_offset_now);
	$phone_code = eregi_replace(' ','+',$phone_code);
	$phone_number = eregi_replace(' ','+',$phone_number);
	$title = eregi_replace(' ','+',$title);
	$first_name = eregi_replace(' ','+',$first_name);
	$middle_initial = eregi_replace(' ','+',$middle_initial);
	$last_name = eregi_replace(' ','+',$last_name);
	$address1 = eregi_replace(' ','+',$address1);
	$address2 = eregi_replace(' ','+',$address2);
	$address3 = eregi_replace(' ','+',$address2);
	$city = eregi_replace(' ','+',$city);
	$state = eregi_replace(' ','+',$state);
	$province = eregi_replace(' ','+',$province);
	$postal_code = eregi_replace(' ','+',$postal_code);
	$country_code = eregi_replace(' ','+',$country_code);
	$gender = eregi_replace(' ','+',$gender);
	$date_of_birth = eregi_replace(' ','+',$date_of_birth);
	$alt_phone = eregi_replace(' ','+',$alt_phone);
	$email = eregi_replace(' ','+',$email);
	$security_phrase = eregi_replace(' ','+',$security_phrase);
	$comments = eregi_replace(' ','+',$comments);
	$RGfullname = eregi_replace(' ','+',$RGfullname);
	$RGuser = eregi_replace(' ','+',$RGuser);
	$RGlead_id = eregi_replace(' ','+',$RGlead_id);
	$RGcampaign = eregi_replace(' ','+',$RGcampaign);
	$RGphone_login = eregi_replace(' ','+',$RGphone_login);
	$RGgroup = eregi_replace(' ','+',$RGgroup);
	$RGchannel_group = eregi_replace(' ','+',$RGchannel_group);
	$RGSQLdate = eregi_replace(' ','+',$RGSQLdate);
	$RGepoch = eregi_replace(' ','+',$RGepoch);
	$RGuniqueid = eregi_replace(' ','+',$RGuniqueid);
	$RGcustomer_zap_channel = eregi_replace(' ','+',$RGcustomer_zap_channel);
	$RGserver_ip = eregi_replace(' ','+',$RGserver_ip);
	$RGSIPexten = eregi_replace(' ','+',$RGSIPexten);
	$RGsession_id = eregi_replace(' ','+',$RGsession_id);
	}

$script_text = eregi_replace('--A--vendor_lead_code--B--',"$vendor_lead_code",$script_text);
$script_text = eregi_replace('--A--list_id--B--',"$list_id",$script_text);
$script_text = eregi_replace('--A--gmt_offset_now--B--',"$gmt_offset_now",$script_text);
$script_text = eregi_replace('--A--phone_code--B--',"$phone_code",$script_text);
$script_text = eregi_replace('--A--phone_number--B--',"$phone_number",$script_text);
$script_text = eregi_replace('--A--title--B--',"$title",$script_text);
$script_text = eregi_replace('--A--first_name--B--',"$first_name",$script_text);
$script_text = eregi_replace('--A--middle_initial--B--',"$middle_initial",$script_text);
$script_text = eregi_replace('--A--last_name--B--',"$last_name",$script_text);
$script_text = eregi_replace('--A--address1--B--',"$address1",$script_text);
$script_text = eregi_replace('--A--address2--B--',"$address2",$script_text);
$script_text = eregi_replace('--A--address3--B--',"$address3",$script_text);
$script_text = eregi_replace('--A--city--B--',"$city",$script_text);
$script_text = eregi_replace('--A--state--B--',"$state",$script_text);
$script_text = eregi_replace('--A--province--B--',"$province",$script_text);
$script_text = eregi_replace('--A--postal_code--B--',"$postal_code",$script_text);
$script_text = eregi_replace('--A--country_code--B--',"$country_code",$script_text);
$script_text = eregi_replace('--A--gender--B--',"$gender",$script_text);
$script_text = eregi_replace('--A--date_of_birth--B--',"$date_of_birth",$script_text);
$script_text = eregi_replace('--A--alt_phone--B--',"$alt_phone",$script_text);
$script_text = eregi_replace('--A--email--B--',"$email",$script_text);
$script_text = eregi_replace('--A--security_phrase--B--',"$security_phrase",$script_text);
$script_text = eregi_replace('--A--comments--B--',"$comments",$script_text);
$script_text = eregi_replace('--A--fullname--B--',"$RGfullname",$script_text);
$script_text = eregi_replace('--A--fronter--B--',"$RGuser",$script_text);
$script_text = eregi_replace('--A--user--B--',"$RGuser",$script_text);
$script_text = eregi_replace('--A--lead_id--B--',"$RGlead_id",$script_text);
$script_text = eregi_replace('--A--campaign--B--',"$RGcampaign",$script_text);
$script_text = eregi_replace('--A--phone_login--B--',"$RGphone_login",$script_text);
$script_text = eregi_replace('--A--group--B--',"$RGgroup",$script_text);
$script_text = eregi_replace('--A--channel_group--B--',"$RGchannel_group",$script_text);
$script_text = eregi_replace('--A--SQLdate--B--',"$RGSQLdate",$script_text);
$script_text = eregi_replace('--A--epoch--B--',"$RGepoch",$script_text);
$script_text = eregi_replace('--A--uniqueid--B--',"$RGuniqueid",$script_text);
$script_text = eregi_replace('--A--customer_zap_channel--B--',"$RGcustomer_zap_channel",$script_text);
$script_text = eregi_replace('--A--server_ip--B--',"$RGserver_ip",$script_text);
$script_text = eregi_replace('--A--SIPexten--B--',"$RGSIPexten",$script_text);
$script_text = eregi_replace('--A--session_id--B--',"$RGsession_id",$script_text);
$script_text = eregi_replace("\n","<BR>",$script_text);


echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "Preview Script: $script_id<BR>\n";
echo "<TABLE WIDTH=600><TR><TD>\n";
echo "<center><B>$script_name</B><BR></center>\n";
echo "$script_text\n";
echo "</TD></TR></TABLE></center>\n";

echo "</BODY></HTML>\n";

exit;
}




######################### HTML HEADER BEGIN #######################################
if ($hh=='users') 
	{$users_hh="bgcolor =\"$users_color\""; $users_fc="$users_font"; $users_bold="$header_selected_bold";}
	else {$users_hh=''; $users_fc='WHITE'; $users_bold="$header_nonselected_bold";}
if ($hh=='campaigns') 
	{$campaigns_hh="bgcolor=\"$campaigns_color\""; $campaigns_fc="$campaigns_font"; $campaigns_bold="$header_selected_bold";}
	else {$campaigns_hh=''; $campaigns_fc='WHITE'; $campaigns_bold="$header_nonselected_bold";}
if ($hh=='lists') 
	{$lists_hh="bgcolor=\"$lists_color\""; $lists_fc="$lists_font"; $lists_bold="$header_selected_bold";}
	else {$lists_hh=''; $lists_fc='WHITE'; $lists_bold="$header_nonselected_bold";}
if ($hh=='ingroups') 
	{$ingroups_hh="bgcolor=\"$ingroups_color\""; $ingroups_fc="$ingroups_font"; $ingroups_bold="$header_selected_bold";}
	else {$ingroups_hh=''; $ingroups_fc='WHITE'; $ingroups_bold="$header_nonselected_bold";}
if ($hh=='remoteagent') 
	{$remoteagent_hh="bgcolor=\"$remoteagent_color\""; $remoteagent_fc="$remoteagent_font"; $remoteagent_bold="$header_selected_bold";}
	else {$remoteagent_hh=''; $remoteagent_fc='WHITE'; $remoteagent_bold="$header_nonselected_bold";}
if ($hh=='usergroups') 
	{$usergroups_hh="bgcolor=\"$usergroups_color\""; $usergroups_fc="$usergroups_font"; $usergroups_bold="$header_selected_bold";}
	else {$usergroups_hh=''; $usergroups_fc='WHITE'; $usergroups_bold="$header_nonselected_bold";}
if ($hh=='scripts') 
	{$scripts_hh="bgcolor=\"$scripts_color\""; $scripts_fc="$scripts_font"; $scripts_bold="$header_selected_bold";}
	else {$scripts_hh=''; $scripts_fc='WHITE'; $scripts_bold="$header_nonselected_bold";}
if ($hh=='filters') 
	{$filters_hh="bgcolor=\"$filters_color\""; $filters_fc="$filters_font"; $filters_bold="$header_selected_bold";}
	else {$filters_hh=''; $filters_fc='WHITE'; $filters_bold="$header_nonselected_bold";}
if ($hh=='admin') 
	{$admin_hh="bgcolor=\"$admin_color\""; $admin_fc="$admin_font"; $admin_bold="$header_selected_bold";}
	else {$admin_hh=''; $admin_fc='WHITE'; $admin_bold="$header_nonselected_bold";}
if ($hh=='reports') 
	{$reports_hh="bgcolor=\"$reports_color\""; $reports_fc="$reports_font"; $reports_bold="$header_selected_bold";}
	else {$reports_hh=''; $reports_fc='WHITE'; $reports_bold="$header_nonselected_bold";}

echo "</title>\n";
echo "<script language=\"Javascript\">\n";





######################
# ADD=31 or 34 and SUB=29 for list mixes
######################
if ( ( ($ADD==34) or ($ADD==31) or ($ADD==49) ) and ($SUB==29) and ($LOGmodify_campaigns==1) and ( (eregi("$campaign_id",$LOGallowed_campaigns)) or (eregi("ALL-CAMPAIGNS",$LOGallowed_campaigns)) ) ) 
{

?>
// List Mix status add and remove
function mod_mix_status(stage,vcl_id,entry) 
	{
	var mod_status = document.getElementById("dial_status_" + entry + "_" + vcl_id);
	if (mod_status.value.length < 1)
		{
		alert("You must select a status first");
		}
	else
		{
		var old_statuses = document.getElementById("status_" + entry + "_" + vcl_id);
		var ROold_statuses = document.getElementById("ROstatus_" + entry + "_" + vcl_id);
		var MODstatus = new RegExp(" " + mod_status.value + " ","g");
		if (stage=="ADD")
			{
			if (old_statuses.value.match(MODstatus))
				{
				alert("The status " + mod_status.value + " is already present");
				}
			else
				{
				var new_statuses = " " + mod_status.value + "" + old_statuses.value;
				old_statuses.value = new_statuses;
				ROold_statuses.value = new_statuses;
				mod_status.value = "";
				}
			}
		if (stage=="REMOVE")
			{
			var MODstatus = new RegExp(" " + mod_status.value + " ","g");
			old_statuses.value = old_statuses.value.replace(MODstatus, " ");
			ROold_statuses.value = ROold_statuses.value.replace(MODstatus, " ");
			}
		}
	}

// List Mix percent difference calculation and warning message
function mod_mix_percent(vcl_id,entries) 
	{
	var i=0;
	var total_percent=0;
	var percent_diff='';
	while(i < entries)
		{
		var mod_percent_field = document.getElementById("percentage_" + i + "_" + vcl_id);
		temp_percent = mod_percent_field.value * 1;
		total_percent = (total_percent + temp_percent);
		i++;
		}

	var mod_diff_percent = document.getElementById("PCT_DIFF_" + vcl_id);
	percent_diff = (total_percent - 100);
	if (percent_diff > 0)
		{
		percent_diff = '+' + percent_diff;
		}
	var mix_list_submit = document.getElementById("submit_" + vcl_id);
	if ( (percent_diff > 0) || (percent_diff < 0) )
		{
		mix_list_submit.disabled = true;
		document.getElementById("ERROR_" + vcl_id).innerHTML = "<font color=red><B>The Difference % must be 0</B></font>";
		}
	else
		{
		mix_list_submit.disabled = false;
		document.getElementById("ERROR_" + vcl_id).innerHTML = "";
		}

	mod_diff_percent.value = percent_diff;
	}

function submit_mix(vcl_id,entries) 
	{
	var h=1;
	var j=1;
	var list_mix_container='';
	var mod_list_mix_container_field = document.getElementById("list_mix_container_" + vcl_id);
	while(h < 11)
		{
		var i=0;
		while(i < entries)
			{
			var mod_list_id_field = document.getElementById("list_id_" + i + "_" + vcl_id);
			var mod_priority_field = document.getElementById("priority_" + i + "_" + vcl_id);
			var mod_percent_field = document.getElementById("percentage_" + i + "_" + vcl_id);
			var mod_statuses_field = document.getElementById("status_" + i + "_" + vcl_id);
			if (mod_priority_field.value==h)
				{
				list_mix_container = list_mix_container + mod_list_id_field.value + "|" + j + "|" + mod_percent_field.value + "|" + mod_statuses_field.value + "|:";
				j++
				}
			i++;
			}
		h++
		}
	mod_list_mix_container_field.value = list_mix_container;
	var form_to_submit = document.getElementById("" + vcl_id);
	form_to_submit.submit();
	}
<?
}
?>

function openNewWindow(url) {
  window.open (url,"",'width=620,height=300,scrollbars=yes,menubar=yes,address=yes');
}
function scriptInsertField() {
	openField = '--A--';
	closeField = '--B--';
	var textBox = document.scriptForm.script_text;
	var scriptIndex = document.getElementById("selectedField").selectedIndex;
	var insValue =  document.getElementById('selectedField').options[scriptIndex].value;
  if (document.selection) {
	//IE
	textBox = document.scriptForm.script_text;
	insValue = document.scriptForm.selectedField.options[document.scriptForm.selectedField.selectedIndex].text;
	textBox.focus();
	sel = document.selection.createRange();
	sel.text = openField + insValue + closeField;
  } else if (textBox.selectionStart || textBox.selectionStart == 0) {
	//Mozilla
	var startPos = textBox.selectionStart;
	var endPos = textBox.selectionEnd;
	textBox.value = textBox.value.substring(0, startPos)
	+ openField + insValue + closeField
	+ textBox.value.substring(endPos, textBox.value.length);
  } else {
	textBox.value += openField + insValue + closeField;
  }
}



// Show the time 
var tick;
function stop() {
 	clearTimeout(tick);
}
function usnotime()  {
	var ut=new Date();
	var h,m,s;
	var time="      ";
	h=ut.getHours();
	m=ut.getMinutes();
	s=ut.getSeconds();
	if(s<=9) s="0"+s;
	if(m<=9) m="0"+m;
	if(h<=9) h="0"+h;
	time+=h+":"+m+":"+s;
	document.rclock.rtime.value=time;
	tick=setTimeout("usnotime()",1000);   
}


function openNewWindow(url) {
  window.open (url,"",'width=700,height=300,scrollbars=yes,menubar=yes,address=yes');
}
function ShowProgress(good, bad, total, dup, post) {
	parent.lead_count.document.open();
	parent.lead_count.document.write('<html><body><table border=0 width=200 cellpadding=10 cellspacing=0 align=center valign=top><tr bgcolor="#000000"><th colspan=2><font face="arial, helvetica" size=3 color=white>Current file status:</font></th></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Good:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+good+'</B></font></td></tr><tr bgcolor="#990000"><td align=right><font face="arial, helvetica" size=2 color=white><B>Bad:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+bad+'</B></font></td></tr><tr bgcolor="#000099"><td align=right><font face="arial, helvetica" size=2 color=white><B>Total:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+total+'</B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B> &nbsp; </B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B> &nbsp; </B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Duplicate:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+dup+'</B></font></td></tr><tr bgcolor="#009900"><td align=right><font face="arial, helvetica" size=2 color=white><B>Postal Match:</B></font></td><td align=left><font face="arial, helvetica" size=2 color=white><B>'+post+'</B></font></td></tr></table><body></html>');
	parent.lead_count.document.close();
}
function ParseFileName() {
	if (!document.forms[0].OK_to_process) {	
		var endstr=document.forms[0].leadfile.value.lastIndexOf('\\');
		if (endstr>-1) {
			endstr++;
			var filename=document.forms[0].leadfile.value.substring(endstr);
			document.forms[0].leadfile_name.value=filename;
		}
	}
}





// Display time
function clockinit ( )
{
  timeDisplay = document.createTextNode ( "" );
  document.getElementById("clock").appendChild ( timeDisplay );
}

function updateClock () {
  var currentTime = new Date ( );
  var currentHours = currentTime.getHours ( );
  var currentMinutes = currentTime.getMinutes ( );
  var currentSeconds = currentTime.getSeconds ( );
  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
  currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
  var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
  currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
  currentHours = ( currentHours == 0 ) ? 12 : currentHours;
  var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
  document.getElementById("clock").firstChild.nodeValue = currentTimeString;
}


</script>
</head>
<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onload="updateClock(); setInterval('updateClock()', 1000 )" onunload="stop()">
<?
echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-OSDial -->\n";

$stmt="SELECT admin_home_url from system_settings;";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$admin_home_url_LU = $row[0];

?>

<div class=container>
<TABLE WIDTH=900 Oldwidth<?=$page_width ?> BGCOLOR=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>
<tr><td colspan=10>

	<table align='center' border='0' cellspacing='0' cellpadding='0'>
		<tr>	<!-- First draw the top row  -->
			<td width='15'><img src='images/topleft.png' width='15' height='16' align='left'></td>
			<td class='across-top' align='center'></td>
			<td width='15'><img src='images/topright.png' width='15' height='16' align='right'></td>
		</tr>
		<tr valign='top'>
			<td align=left width=33%>
				<font face="arial,helvetica" color=white size=2>&nbsp;&nbsp;<B><a href="<? echo $admin_home_url_LU ?>"><font face="arial,helvetica" color=white size=1>HOME</a> | <a href="<? echo $PHP_SELF ?>?force_logout=1"><font face="arial,helvetica" color=yellow size=1>Logout</a>
			</td>
			<td class='user-company' align=center width=33%>
				<font color=#1C4754><? echo $user_company ?></font><br />
				<font color=#1C4754 size=2><b><br>OSDial Administrator<b><br><br><br></font>
			</td>
			<td align=right width=33%>
				<font face="arial,helvetica" color=white size=2><? echo date("l F j, Y") ?>&nbsp;&nbsp;<br>
				<div style="width: 10em; text-align: right; margin: 5px;">
					<span id="clock">&nbsp;&nbsp;</span>
				</div>
				<? //echo date("G:i:s A") ?>
			</td>
		</tr>
	</table>

</TR>
<!--tr xBGCOLOR=#015B91><td colspan=10></td></tr>
<tr xBGCOLOR=#015B91><td colspan=10></td></tr-->
<TR class='no-ul'>
	<!--TD height=25 ALIGN=CENTER <?=$users_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=0"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$users_fc ?> SIZE=<?=$header_font_size ?>> Agents </a></TD>
	<TD height=25 ALIGN=CENTER <?=$campaigns_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$campaigns_fc ?> SIZE=<?=$header_font_size ?>> Campaigns </a></TD>
	<TD height=25 ALIGN=CENTER <?=$lists_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=100"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$lists_fc ?> SIZE=<?=$header_font_size ?>> Lists </a></TD>
	<TD height=25 ALIGN=CENTER <?=$scripts_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=1000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$scripts_fc ?> SIZE=<?=$header_font_size ?>> Scripts </a></TD>
	<TD height=25 ALIGN=CENTER <?=$filters_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$filters_fc ?> SIZE=<?=$header_font_size ?>> Filters </a></TD>
	<TD height=25 ALIGN=CENTER <?=$ingroups_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=1000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$ingroups_fc ?> SIZE=<?=$header_font_size ?>> In-Groups </a></TD>
	<TD height=25 ALIGN=CENTER <?=$usergroups_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=100000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$usergroups_fc ?> SIZE=<?=$header_font_size ?>> User Groups </a></TD>
	<TD height=25 ALIGN=CENTER <?=$remoteagent_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$remoteagent_fc ?> SIZE=<?=$header_font_size ?>> Remote Agents </a></TD>
	<TD height=25 ALIGN=CENTER <?=$admin_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$admin_fc ?> SIZE=<?=$header_font_size ?>> Setup </a></TD>
	<TD height=25 ALIGN=CENTER <?=$reports_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=999999"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$reports_fc ?> SIZE=<?=$header_font_size ?>> Reports </a></TD-->
	
	<TD height=25 ALIGN=CENTER <?=$users_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=0"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Agents </a></TD>
	<TD height=25 ALIGN=CENTER <?=$campaigns_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Campaigns </a></TD>
	<TD height=25 ALIGN=CENTER <?=$lists_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=100"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Lists </a></TD>
	<TD height=25 ALIGN=CENTER <?=$scripts_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=1000000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Scripts </a></TD>
	<TD height=25 ALIGN=CENTER <?=$filters_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Filters </a></TD>
	<TD height=25 ALIGN=CENTER <?=$ingroups_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=1000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> In-Groups </a></TD>
	<TD height=25 ALIGN=CENTER <?=$usergroups_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=100000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> User Groups </a></TD>
	<TD height=25 ALIGN=CENTER <?=$remoteagent_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Off-Hook Agents </a></TD>
	<TD height=25 ALIGN=CENTER <?=$reports_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=999999"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Reports </a></TD>
	<TD height=25 ALIGN=CENTER <?=$admin_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Setup </a></TD>
</TR>

<? if (strlen($users_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$users_color ?>>
	<TD ALIGN=LEFT COLSPAN=10 height=20>
	<FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Agents </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Agent </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1A"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Copy Agent </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=550"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Search For An Agent </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="./user_stats.php?user=<?=$user ?>"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Agent Stats </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="./user_status.php?user=<?=$user ?>"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Agent Status </a> 
	</TD>
</TR>
<? } 
if (strlen($campaigns_hh) > 1) 
	{ 

#	if ($sh=='basic') {$basic_sh="bgcolor=\"$subcamp_color\""; $basic_fc="$subcamp_font";}
#		else {$basic_sh=''; $basic_fc='BLACK';}
#	if ($sh=='detail') {$detail_sh="bgcolor=\"$subcamp_color\""; $detail_fc="$subcamp_font";}
#		else {$detail_sh=''; $detail_fc='BLACK';}
#	if ($sh=='dialstat') {$dialstat_sh="bgcolor=\"$subcamp_color\""; $dialstat_fc="$subcamp_font";}
#		else {$dialstat_sh=''; $dialstat_fc='BLACK';}

	if ($sh=='basic') {$sh='list';}
	if ($sh=='detail') {$sh='list';}
	if ($sh=='dialstat') {$sh='list';}

	if ($sh=='list') {$list_sh="bgcolor=\"$subcamp_color\""; $list_fc="$subcamp_font";}
		else {$list_sh=''; $list_fc='BLACK';}
	if ($sh=='status') {$status_sh="bgcolor=\"$subcamp_color\""; $status_fc="$subcamp_font";}
		else {$status_sh=''; $status_fc='BLACK';}
	if ($sh=='hotkey') {$hotkey_sh="bgcolor=\"$subcamp_color\""; $hotkey_fc="$subcamp_font";}
		else {$hotkey_sh=''; $hotkey_fc='BLACK';}
	if ($sh=='recycle') {$recycle_sh="bgcolor=\"$subcamp_color\""; $recycle_fc="$subcamp_font";}
		else {$recycle_sh=''; $recycle_fc='BLACK';}
	if ($sh=='autoalt') {$autoalt_sh="bgcolor=\"$subcamp_color\""; $autoalt_fc="$subcamp_font";}
		else {$autoalt_sh=''; $autoalt_fc='BLACK';}
	if ($sh=='pause') {$pause_sh="bgcolor=\"$subcamp_color\""; $pause_fc="$subcamp_font";}
		else {$pause_sh=''; $pause_fc='BLACK';}
	if ($sh=='listmix') {$listmix_sh="bgcolor=\"$subcamp_color\""; $listmix_fc="$subcamp_font";}
		else {$listmix_sh=''; $listmix_fc='BLACK';}

	?>
<TR class='no-ul' BGCOLOR=<?=$campaigns_color ?>>
<TD height=20 ALIGN=CENTER <?=$list_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=10"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$list_fc ?> SIZE=<?=$subcamp_font_size ?>> Campaigns Main </a></TD>
<TD ALIGN=CENTER <?=$status_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=32"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$status_fc ?> SIZE=<?=$subcamp_font_size ?>> Statuses </a></TD>
<TD ALIGN=CENTER <?=$hotkey_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=33"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$hotkey_fc ?> SIZE=<?=$subcamp_font_size ?>> HotKeys </a></TD>
<TD ALIGN=CENTER <?=$recycle_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=35"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$recycle_fc ?> SIZE=<?=$subcamp_font_size ?>> Lead Recycle </a></TD>
<TD ALIGN=CENTER <?=$autoalt_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=36"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$autoalt_fc ?> SIZE=<?=$subcamp_font_size ?>> Auto-Alt Dial </a></TD>
<TD ALIGN=CENTER <?=$pause_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=37"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$pause_fc ?> SIZE=<?=$subcamp_font_size ?>> Pause Codes </a></TD>
<TD ALIGN=CENTER <?=$listmix_sh ?> COLSPAN=2><!--a href="<? echo $PHP_SELF ?>?ADD=39"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$listmix_fc ?> SIZE=<?=$subcamp_font_size ?>> List Mix </a --></TD>
</TR>
	<?
	if (strlen($list_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$subcamp_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> Show Campaigns </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> Add A New Campaign </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=12"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> Copy Campaign </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="?ADD=13"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> Real-Time Campaigns Summary </a></TD></TR>
		<? } 

	} 
if (strlen($lists_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$lists_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=100"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Lists </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New List </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=112"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Search For A Lead </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=121"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add Number To DNC </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=122"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Load New Leads </a></TD></TR>
<? } 
if (strlen($scripts_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$scripts_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Scripts </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Script </a></TD></TR>
<? } 
if (strlen($filters_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$filters_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Filters </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Filter </a></TD></TR>
<? } 
if (strlen($ingroups_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$ingroups_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show In-Groups </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New In-Group </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1211"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Copy In-Group </a></TD></TR>
<? } 
if (strlen($usergroups_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$usergroups_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=100000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show User Groups </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New User Group </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="group_hourly_stats.php"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Group Hourly Report </a></TD></TR>
<? } 
if (strlen($remoteagent_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$remoteagent_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Off-Hook Agents </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add New Off-Hook Agents </a></TD></TR>
<? } 

if (strlen($admin_hh) > 1) { 
	if ($sh=='times') {$times_sh="bgcolor=\"$times_color\""; $times_fc="$times_font";} # hard teal
		else {$times_sh=''; $times_fc='BLACK';}
	if ($sh=='phones') {$phones_sh="bgcolor=\"$server_color\""; $phones_fc="$phones_font";} # pink
		else {$phones_sh=''; $phones_fc='BLACK';}
	if ($sh=='server') {$server_sh="bgcolor=\"$server_color\""; $server_fc="$server_font";} # pink
		else {$server_sh=''; $server_fc='BLACK';}
	if ($sh=='conference') {$conference_sh="bgcolor=\"$server_color\""; $conference_fc="$server_font";} # pink
		else {$conference_sh=''; $conference_fc='BLACK';}
	if ($sh=='settings') {$settings_sh="bgcolor=\"$settings_color\""; $settings_fc="$settings_font";} # pink
		else {$settings_sh=''; $settings_fc='BLACK';}
	if ($sh=='status') {$status_sh="bgcolor=\"$status_color\""; $status_fc="$status_font";} # pink
		else {$status_sh=''; $status_fc='BLACK';}

	?>
<TR class='no-ul' BGCOLOR=<?=$admin_color ?>>
<TD height=20 ALIGN=center <?=$times_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=100000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$times_fc ?> SIZE=<?=$header_font_size ?>> Call Times </a></TD>
<TD ALIGN=center <?=$phones_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=10000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$phones_fc ?> SIZE=<?=$header_font_size ?>> Phones </a></TD>
<TD ALIGN=center <?=$conference_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=1000000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$conference_fc ?> SIZE=<?=$header_font_size ?>> Conferences </a></TD>
<TD ALIGN=center <?=$server_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=100000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$server_fc ?> SIZE=<?=$header_font_size ?>> Servers </a></TD>
<TD ALIGN=center <?=$settings_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=311111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$settings_fc ?> SIZE=<?=$header_font_size ?>> System Settings </a></TD>
<TD ALIGN=center <?=$status_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=321111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$status_fc ?> SIZE=<?=$header_font_size ?>> System Statuses </a></TD>
</TR>
	<?
	if (strlen($times_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$times_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=100000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Call Times </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Call Time </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show State Call Times </a> &nbsp; &nbsp; |  &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New State Call Time </a> &nbsp; </TD></TR>
		<? } 
	if (strlen($phones_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$phones_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Phones </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Phone </a></TD></TR>
		<? }
	if (strlen($conference_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$conference_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<a href="<? echo $PHP_SELF ?>?ADD=1000000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Conferences </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Conference </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10000000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show OSDial Conferences </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New OSDial Conference </a></TD></TR>
		<? }
	if (strlen($server_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$server_color ?>>
		<TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<a href="<? echo $PHP_SELF ?>?ADD=100000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Servers </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Server </a> | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=399111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>>Archive Server</a> | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=399211111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>>QC Servers</a>
		</TD></TR>
	<?}
	if (strlen($settings_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$settings_color ?>>
		<TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<a href="<? echo $PHP_SELF ?>?ADD=311111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> System Settings </a></TD>
	</TR>
	<?}
	if (strlen($status_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$status_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<a href="<? echo $PHP_SELF ?>?ADD=321111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> System Statuses </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=331111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Status Categories </a></TD></TR>
	<?}

### Do nothing if admin has no permissions
if($LOGast_admin_access < 1) 
	{
	$ADD='99999999999999999999';
	echo "</TABLE></center>\n";
	echo "<font color=red>You are not authorized to view this page. Please go back.</font>\n";
	}

} 
if (strlen($reports_hh) > 1) { 
	?>
<TR BGCOLOR=<?=$reports_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>><B> &nbsp; </B></TD></TR>
<? } ?>


<TR><TD ALIGN=LEFT COLSPAN=10 HEIGHT=1 BGCOLOR=#666666></TD></TR>
<TR><TD ALIGN=LEFT COLSPAN=10>
<? 
######################### HTML HEADER BEGIN #######################################





######################################################################################################
######################################################################################################
#######   1 series, ADD NEW forms for inserting new records into the database
######################################################################################################
######################################################################################################


######################
# ADD=1 display the ADD NEW USER FORM SCREEN
######################

if ($ADD=="1")
{
	if ($LOGmodify_users==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW AGENT<form action=$PHP_SELF method=POST></font><br><br>\n";
	echo "<input type=hidden name=ADD value=2>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Agent Number: </td><td align=left><input type=text name=user size=20 maxlength=10>$NWB#osdial_users-user$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10>$NWB#osdial_users-pass$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=20 maxlength=100>$NWB#osdial_users-full_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>User Level: </td><td align=left><select size=1 name=user_level>";
	$h=1;
	while ($h<=$LOGuser_level)
		{
		echo "<option>$h</option>";
		$h++;
		}
	echo "</select>$NWB#osdial_users-user_level$NWE</td></tr>\n";
	
	echo "<tr bgcolor=#C1D6DF><td align=right>User Group: </td><td align=left><select size=1 name=user_group>\n";

		$stmt="SELECT user_group,group_name from vicidial_user_groups order by user_group";
		$rslt=mysql_query($stmt, $link);
		$Ugroups_to_print = mysql_num_rows($rslt);
		$Ugroups_list='';

		$o=0;
		while ($Ugroups_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$Ugroups_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
		}
	echo "$Ugroups_list";
	echo "<option SELECTED>$user_group</option>\n";
	echo "</select>$NWB#osdial_users-user_group$NWE</td></tr>\n";
	
	echo "<tr bgcolor=#C1D6DF><td align=right>Phone Login: </td><td align=left><input type=text name=phone_login size=20 maxlength=20>$NWB#osdial_users-phone_login$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Phone Pass: </td><td align=left><input type=text name=phone_pass size=20 maxlength=20>$NWB#osdial_users-phone_pass$NWE</td></tr>\n";
	
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=1 display the COPY USER FORM SCREEN
######################

if ($ADD=="1A")
{
	if ($LOGmodify_users==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>COPY USER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2A>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Agent Number: </td><td align=left><input type=text name=user size=20 maxlength=10>$NWB#osdial_users-user$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10>$NWB#osdial_users-pass$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=20 maxlength=100>$NWB#osdial_users-full_name$NWE</td></tr>\n";

	if ($LOGuser_level==9) {$levelMAX=10;}
	else {$levelMAX=$LOGuser_level;}

	echo "<tr bgcolor=#C1D6DF><td align=right>Source Agent: </td><td align=left><select size=1 name=source_user_id>\n";

		$stmt="SELECT user,full_name from vicidial_users where user_level < $levelMAX order by full_name;";
		$rslt=mysql_query($stmt, $link);
		$Uusers_to_print = mysql_num_rows($rslt);
		$Uusers_list='';

		$o=0;
		while ($Uusers_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$Uusers_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
		}
	echo "$Uusers_list";
	echo "</select>$NWB#osdial_users-user$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=11 display the ADD NEW CAMPAIGN FORM SCREEN
######################

if ($ADD==11)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW CAMPAIGN</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign ID: </td><td align=left><input type=text name=campaign_id size=10 maxlength=8>$NWB#osdial_campaigns-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Name: </td><td align=left><input type=text name=campaign_name size=30 maxlength=30>$NWB#osdial_campaigns-campaign_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Description: </td><td align=left><input type=text name=campaign_description size=30 maxlength=255>$NWB#osdial_campaigns-campaign_description$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option></select>$NWB#osdial_campaigns-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Park Extension: </td><td align=left><input type=text name=park_ext size=10 maxlength=10>$NWB#osdial_campaigns-park_ext$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Park Filename: </td><td align=left><input type=text name=park_file_name size=10 maxlength=10>$NWB#osdial_campaigns-park_file_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 1: </td><td align=left><input type=text name=web_form_address size=50 maxlength=255>$NWB#osdial_campaigns-web_form_address$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 2: </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255>$NWB#osdial_campaigns-web_form_address$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Allow Closers: </td><td align=left><select size=1 name=allow_closers><option>Y</option><option>N</option></select>$NWB#osdial_campaigns-allow_closers$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Hopper Level: </td><td align=left><select size=1 name=hopper_level><option>1</option><option>5</option><option>10</option><option>20</option><option>50</option><option>100</option><option>200</option><option>500</option><option>1000</option><option>2000</option></select>$NWB#osdial_campaigns-hopper_level$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Auto Dial Level: </td><td align=left><select size=1 name=auto_dial_level><option selected>0</option><option>1</option><option>1.1</option><option>1.2</option><option>1.3</option><option>1.4</option><option>1.5</option><option>1.6</option><option>1.7</option><option>1.8</option><option>1.9</option><option>2.0</option><option>2.2</option><option>2.5</option><option>2.7</option><option>3.0</option><option>3.5</option><option>4.0</option></select>(0 = off)$NWB#osdial_campaigns-auto_dial_level$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Next Agent Call: </td><td align=left><select size=1 name=next_agent_call><option >random</option><option>oldest_call_start</option><option>oldest_call_finish</option><option>overall_user_level</option><option>campaign_rank</option><option>fewest_calls</option></select>$NWB#osdial_campaigns-next_agent_call$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Local Call Time: </td><td align=left><select size=1 name=local_call_time>";
	echo "$call_times_list";
	echo "</select>$NWB#osdial_campaigns-local_call_time$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Voicemail: </td><td align=left><input type=text name=voicemail_ext size=10 maxlength=10 value=\"$voicemail_ext\">$NWB#osdial_campaigns-voicemail_ext$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script: </td><td align=left><select size=1 name=script_id>\n";
	echo "$scripts_list";
	echo "</select>$NWB#osdial_campaigns-campaign_script$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Get Call Launch: </td><td align=left><select size=1 name=get_call_launch><option selected>NONE</option><option>SCRIPT</option><option>WEBFORM</option></select>$NWB#osdial_campaigns-get_call_launch$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=12 display the COPY CAMPAIGN FORM SCREEN
######################

if ($ADD==12)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>COPY A CAMPAIGN</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=20>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign ID: </td><td align=left><input type=text name=campaign_id size=10 maxlength=8>$NWB#osdial_campaigns-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Name: </td><td align=left><input type=text name=campaign_name size=30 maxlength=30>$NWB#osdial_campaigns-campaign_name$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Source Campaign: </td><td align=left><select size=1 name=source_campaign_id>\n";

		$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
		$rslt=mysql_query($stmt, $link);
		$campaigns_to_print = mysql_num_rows($rslt);
		$campaigns_list='';

		$o=0;
		while ($campaigns_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
		}
	echo "$campaigns_list";
	echo "</select>$NWB#osdial_campaigns-campaign_id$NWE</td></tr>\n";
	
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}




######################
# ADD=13 Show RealTimeSummary screen
######################
if ($ADD==13) {
 
	function getloadavg() {
		if (file_exists('Loadavg.txt')) {
			$loadavg = file_get_contents("Loadavg.txt");
		}
		return $loadavg;
	}

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
	if ($DB) { echo "|$stmt|\n"; }
	
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];
	
	if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
		Header("WWW-Authenticate: Basic realm=\"OSDial-Administrator\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
	}
	
	$NOW_TIME = date("Y-m-d H:i:s");
	$STARTtime = date("U");
	
	$stmt="select campaign_id from vicidial_campaigns where active='Y';";
	$rslt=mysql_query($stmt, $link);
	if (!isset($DB)) { $DB=0; }
	if ($DB) { echo "$stmt\n"; }
	
	$groups_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $groups_to_print)	{
		$row=mysql_fetch_row($rslt);
		$groups[$i] =$row[0];
		$i++;
	}
	
	if (!isset($RR))   {$RR=4;}
	if ($RR=0)  {$RR=4;}
	
	echo "<div class=no-ul>";
	echo "<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=ADD value=13>\n";
	echo "<input type=hidden name=adastats value=$adastats\n";
	echo "<input type=hidden name=group value=$group>\n";
	echo "<p class=centered><font color=navy>All Campaigns Summary &nbsp;&nbsp;&nbsp;&nbsp;Update: ";
	echo "<a href=\"$PHP_SELF?ADD=13&group=$group&RR=38400&DB=$DB&adastats=$adastats\">Daily</a> | ";
	echo "<a href=\"$PHP_SELF?ADD=13&group=$group&RR=3600&DB=$DB&adastats=$adastats\">Hourly</a> | ";
	echo "<a href=\"$PHP_SELF?ADD=13&group=$group&RR=30&DB=$DB&adastats=$adastats\">30sec</a> | ";
	echo "<a href=\"$PHP_SELF?ADD=13&group=$group&RR=4&DB=$DB&adastats=$adastats\">4sec</a>";
	echo "&nbsp;&nbsp;&nbsp;";
	if ($adastats<2) {
		echo "<a href=\"$PHP_SELF?ADD=13&group=$group&RR=$RR&DB=$DB&adastats=2\"><font size=1>VIEW MORE SETTINGS</font></a>";
	} else {
		echo "<a href=\"$PHP_SELF?ADD=13&group=$group&RR=$RR&DB=$DB&adastats=1\"><font size=1>VIEW LESS SETTINGS</font></a>";
	}
	//echo "&nbsp;&nbsp;&nbsp;<a href=\"./admin.php?ADD=10\">Campaigns</a>&nbsp;&nbsp;<a href=\"./admin.php?ADD=999999\">Reports</a>";
	echo "</p>\n\n";
	
	$k=0;
	while($k<$groups_to_print) {
		$NFB = '<b><font size=3 face="courier">';
		$NFE = '</font></b>';
		$F=''; $FG=''; $B=''; $BG='';
		
		
		$group = $groups[$k];
		echo "<font class=indents size=-1><b><a href=\"./admin.php?ADD=14&group=$group&RR=$RR&DB=$DB&adastats=$adastats\">$group</a></b> &nbsp; - &nbsp; ";
		echo "<a href=\"./admin.php?ADD=31&campaign_id=$group\">Modify</a> </font>\n";
		
		
		$stmt = "select count(*) from vicidial_campaigns where campaign_id='$group' and campaign_allow_inbound='Y';";
		$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$campaign_allow_inbound = $row[0];
		
		$stmt="select auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout,dial_statuses from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$DIALlev =	$row[0];
		$DIALstatusA =	$row[1];
		$DIALstatusB =	$row[2];
		$DIALstatusC =	$row[3];
		$DIALstatusD =	$row[4];
		$DIALstatusE =	$row[5];
		$DIALorder =	$row[6];
		$DIALfilter =	$row[7];
		$HOPlev =	$row[8];
		$DIALmethod =	$row[9];
		$maxDIALlev =	$row[10];
		$DROPmax =	$row[11];
		$targetDIFF =	$row[12];
		$ADAintense =	$row[13];
		$ADAavailonly =	$row[14];
		$TAPERtime =	$row[15];
		$CALLtime =	$row[16];
		$DIALtimeout =	$row[17];
		$DIALstatuses =	$row[18];
		$DIALstatuses = (preg_replace("/ -$|^ /","",$DIALstatuses));
		$DIALstatuses = (ereg_replace(' ',', ',$DIALstatuses));
		
		$stmt="select count(*) from vicidial_hopper where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$VDhop = $row[0];
		
		$stmt="select dialable_leads,calls_today,drops_today,drops_answers_today_pct,differential_onemin,agents_average_onemin,balance_trunk_fill,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4 from vicidial_campaign_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$DAleads =	$row[0];
		$callsTODAY =	$row[1];
		$dropsTODAY =	$row[2];
		$drpctTODAY =	$row[3];
		$diffONEMIN =	$row[4];
		$agentsONEMIN = $row[5];
		$balanceFILL =	$row[6];
		$answersTODAY = $row[7];
		$VSCcat1 =	$row[8];
		$VSCcat1tally = $row[9];
		$VSCcat2 =	$row[10];
		$VSCcat2tally = $row[11];
		$VSCcat3 =	$row[12];
		$VSCcat3tally = $row[13];
		$VSCcat4 =	$row[14];
		$VSCcat4tally = $row[15];
		
		if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) )	{
			$diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
			$diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
		} else {
			$diffpctONEMIN = '0.00';
		}
		
		$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$balanceSHORT = $row[0];
		
		echo "<table class=indents cellpadding=0 cellspacing=0><TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALlev&nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>TRUNK SHORT/FILL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $balanceSHORT / $balanceFILL &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>FILTER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALfilter &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>TIME:</B></TD><TD ALIGN=LEFT><font size=2 color=navy>&nbsp; $NOW_TIME </TD>";
		echo "";
		echo "</TR>";
		
		if ($adastats>1) {
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2>&nbsp; <B>MAX LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $maxDIALlev &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED MAX:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DROPmax% &nbsp; &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>TARGET DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $targetDIFF &nbsp; &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>INTENSITY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAintense &nbsp; &nbsp; </TD>";
			echo "</TR>";
		
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2><B>DIAL TIMEOUT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALtimeout &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>TAPER TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $TAPERtime &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>LOCAL TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $CALLtime &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>AVAIL ONLY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAavailonly &nbsp;</TD>";
			echo "</TR>";
			
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2><B>DL DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffONEMIN &nbsp; &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffpctONEMIN% &nbsp; &nbsp; </TD>";
			echo "</TR>";
		}
		
		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIALABLE LEADS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DAleads &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>AVG AGENTS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $agentsONEMIN &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL METHOD:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALmethod &nbsp; &nbsp; </TD>";
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>HOPPER LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $HOPlev &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED / ANSWERED:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY / $answersTODAY &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>STATUSES:</B></TD><TD ALIGN=LEFT colspan=3><font size=2>&nbsp; $DIALstatuses &nbsp; &nbsp; </TD>";
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>LEADS IN HOPPER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $VDhop &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; ";
		if ($drpctTODAY >= $DROPmax) {
			echo "<font color=red><B>$drpctTODAY%</B></font>";
		} else {
			echo "$drpctTODAY%";
		}
		echo " &nbsp; &nbsp;</TD>";
		
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>ORDER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALorder &nbsp; &nbsp; </TD>";
		echo "</tr><tr>";
		if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
			echo "<td align=right><font size=2 color=navy><B>$VSCcat1:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
			echo "<td align=right><font size=2 color=navy><B>$VSCcat2:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) { 
			echo "<td align=right><font size=2 color=navy><B>$VSCcat3:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
			echo "<td align=right><font size=2 color=navy><B>$VSCcat4:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4tally&nbsp;&nbsp;&nbsp; \n";
		}
		
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=LEFT COLSPAN=8>";
		
		### Header finish
		
		
		
		
		
		################################################################################
		### START calculating calls/agents
		################################################################################
		
		################################################################################
		###### OUTBOUND CALLS
		################################################################################
		if ($campaign_allow_inbound > 0) {
			$stmt="select closer_campaigns from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$closer_campaigns = preg_replace("/^ | -$/","",$row[0]);
			$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
			$closer_campaigns = "'$closer_campaigns'";
		
			$stmt="select status from vicidial_auto_calls where status NOT IN('XFER') and ( (call_type='IN' and campaign_id IN($closer_campaigns)) or (campaign_id='" . mysql_real_escape_string($group) . "' and call_type='OUT') );";
		} else {
			if ($group=='XXXX-ALL-ACTIVE-XXXX') { 
				$groupSQL = '';
			} else {
				$groupSQL = " and campaign_id='" . mysql_real_escape_string($group) . "'";
			}
		
			$stmt="select status from vicidial_auto_calls where status NOT IN('XFER') $groupSQL;";
		}
		$rslt=mysql_query($stmt, $link);
		
		if ($DB) {
			echo "$stmt\n";
		}
		
		$parked_to_print = mysql_num_rows($rslt);
		if ($parked_to_print > 0) {
			$i=0;
			$out_total=0;
			$out_ring=0;
			$out_live=0;
			while ($i < $parked_to_print) {
				$row=mysql_fetch_row($rslt);
		
				if (eregi("LIVE",$row[0])) {
					$out_live++;
				} else {
					if (eregi("CLOSER",$row[0])) {
						$nothing=1;
					} else {
						$out_ring++;
					}
				}
				$out_total++;
				$i++;
			}
	
			if ($out_live > 0)  {$F='<FONT class="r1">'; $FG='</FONT>';}
			if ($out_live > 4)  {$F='<FONT class="r2">'; $FG='</FONT>';}
			if ($out_live > 9)  {$F='<FONT class="r3">'; $FG='</FONT>';}
			if ($out_live > 14) {$F='<FONT class="r4">'; $FG='</FONT>';}
	
			if ($campaign_allow_inbound > 0) {
				echo "$NFB$out_total$NFE <font color=navy>current active calls</font>&nbsp; &nbsp; &nbsp; \n";
			} else {
				echo "$NFB$out_total$NFE <font color=navy>calls being placed</font> &nbsp; &nbsp; &nbsp; \n";
			}
			
			echo "$NFB$out_ring$NFE <font color=navy>calls ringing</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
			echo "$NFB$F &nbsp;$out_live $FG$NFE <font color=navy>calls waiting for agents</font> &nbsp; &nbsp; &nbsp; \n";
		} else {
			echo "<font color=red>&nbsp;NO LIVE CALLS WAITING</font>&nbsp;\n";
		}
		
		
		###################################################################################
		###### TIME ON SYSTEM
		###################################################################################
		
		$agent_incall=0;
		$agent_ready=0;
		$agent_paused=0;
		$agent_total=0;
		
		$stmt="select extension,user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id from vicidial_live_agents where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {
			echo "$stmt\n";
		}
		$talking_to_print = mysql_num_rows($rslt);
		if ($talking_to_print > 0) {
			$i=0;
			$agentcount=0;
			while ($i < $talking_to_print) {
				$row=mysql_fetch_row($rslt);
				if (eregi("READY|PAUSED",$row[3]))	{
					$row[5]=$row[6];
				}
				$Lstatus =			$row[3];
				$status =			sprintf("%-6s", $row[3]);
				if (!eregi("INCALL|QUEUE",$row[3])) {
					$call_time_S = ($STARTtime - $row[6]);
				} else {
					$call_time_S = ($STARTtime - $row[5]);
				}
		
				$call_time_M = ($call_time_S / 60);
				$call_time_M = round($call_time_M, 2);
				$call_time_M_int = intval("$call_time_M");
				$call_time_SEC = ($call_time_M - $call_time_M_int);
				$call_time_SEC = ($call_time_SEC * 60);
				$call_time_SEC = round($call_time_SEC, 0);
				if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
				$call_time_MS = "$call_time_M_int:$call_time_SEC";
				$call_time_MS =		sprintf("%7s", $call_time_MS);
				$G = '';		$EG = '';
				if (eregi("PAUSED",$row[3])) {
					if ($call_time_M_int >= 30) {
						$i++; continue;
					} else {
						$agent_paused++;  $agent_total++;
					}
				}
		
				if ( (eregi("INCALL",$status)) or (eregi("QUEUE",$status)) ) {$agent_incall++;  $agent_total++;}
				if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {$agent_ready++;  $agent_total++;}
				$agentcount++;
		
		
				$i++;
			}
		
			if ($agent_ready > 0) {$B='<FONT class="b1">'; $BG='</FONT>';}
			if ($agent_ready > 4) {$B='<FONT class="b2">'; $BG='</FONT>';}
			if ($agent_ready > 9) {$B='<FONT class="b3">'; $BG='</FONT>';}
			if ($agent_ready > 14) {$B='<FONT class="b4">'; $BG='</FONT>';}
	
			echo "\n<BR>\n";
	
			echo "$NFB$agent_total$NFE <font color=navy>agents logged in</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
			echo "$NFB$agent_incall$NFE <font color=navy>agents in calls</font> &nbsp; &nbsp; &nbsp; \n";
			echo "$NFB$B &nbsp;$agent_ready $BG$NFE <font color=navy>agents waiting</font> &nbsp; &nbsp; &nbsp; \n";
			echo "$NFB$agent_paused$NFE <font color=navy>paused agents</font> &nbsp; &nbsp; &nbsp; \n";
			
			$Aecho .= "<PRE><FONT face=Fixed,monospace SIZE=1>";
			echo "$Aecho";
		} else {
			echo "<font color=red>&bull;&nbsp;&nbsp;NO AGENTS ON CALLS</font><BR><br>\n";
			//$Aecho .= "<PRE><FONT face=Fixed,monospace SIZE=1>";
			echo "$Aecho"; 
		}
		
		################################################################################
		### END calculating calls/agents
		################################################################################
			
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
		
		echo "</FORM>\n\n";
		$k++;
	}
	
	echo "</div>";
	echo "&nbsp;";
 
	$load_ave = getloadavg();
	
	// Get server loads, txt file from other servers
	//$load_ave = get_server_load($load_ave);
		
	
	$Aecho="<pre><font face=Fixed,monospace SIZE=1>";
	if (file_exists('S1_load.txt')) {
		$s1_load = file('S1_load.txt');
		list( $line_num, $line ) = each( $s1_load );
		$load_ave_s1=$line;
		$Aecho .= "  <font color=navy>Apache   Load Average:</font> $load_ave<br>";
		$Aecho .= "  <font color=navy>MySQL    Load Average:</font> $load_ave_s1<br>";
	} elseif (!file_exists('D1_load.txt')&& !file_exists('D2_load.txt') && !file_exists('D3_load.txt') && !file_exists('D4_load.txt') && !file_exists('D5_load.txt') && !file_exists('D6_load.txt')) {
		$Aecho .= "  <font color=navy>Dialer Load Average:</font> $load_ave<br>";
	} else {
		$Aecho .= "  <font color=navy>SQL/Web  Load Average:</font> $load_ave<br>";
	}
	if (file_exists('D1_load.txt')) {
		$d1_load = file('D1_load.txt');
		list( $line_num, $line ) = each( $d1_load ) ;
		$load_ave_d1=$line;
		$Aecho .= "  <font color=navy>Dialer 1 Load Average:</font> $load_ave_d1";
	}
	if (file_exists('D2_load.txt')) {
		$d2_load = file('D2_load.txt');
		list( $line_num, $line ) = each( $d2_load );
		$load_ave_d2=$line;
		$Aecho .= "  <font color=navy>Dialer 2 Load Average:</font> $load_ave_d2";
	}
	if (file_exists('D3_load.txt')) {
		$d3_load = file('D3_load.txt');
		list( $line_num, $line ) = each( $d3_load );
		$load_ave_d3=$line;
		$Aecho .= "  <font color=navy>Dialer 3 Load Average:</font> $load_ave_d3";
	}
	if (file_exists('D4_load.txt')) {
		$d4_load = file('D4_load.txt');
		list( $line_num, $line ) = each( $d4_load );
		$load_ave_d4=$line;
		$Aecho .= "  <font color=navy>Dialer 4 Load Average:</font> $load_ave_d4";
	}
	if (file_exists('D5_load.txt')) {
		$d5_load = file('D5_load.txt');
		list( $line_num, $line ) = each( $d5_load );
		$load_ave_d5=$line;
		$Aecho .= "  <font color=navy>Dialer 5 Load Average:</font> $load_ave_d5";
	}
	if (file_exists('D6_load.txt')) {
		$d6_load = file('D6_load.txt');
		list( $line_num, $line ) = each( $d6_load );
		$load_ave_d6=$line;
		$Aecho .= "  <font color=navy>Dialer 6 Load Average:</font> $load_ave_d6";
	}
	//echo "<tr><td colspan=10>";
	echo "$Aecho";
	echo "</pre>";
	//echo "</td></tr><tr><td colspan=10>&nbsp;";
	echo "<TABLE WIDTH='<?=$page_width ?>' BGCOLOR=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>";
}


######################
# ADD=14 Show RealTime Detail screen
######################
if ($ADD==14) {

	function getloadavg() {
		$loadavg = file_get_contents("Loadavg.txt");
		return $loadavg;
	}
	
	if (!isset($RR))			{$gRRroup=4;}
	if (!isset($group))			{$group='';}
	if (!isset($usergroup))		{$usergroup='';}
	if (!isset($UGdisplay))		{$UGdisplay=0;}	# 0=no, 1=yes
	if (!isset($UidORname))		{$UidORname=0;}	# 0=id, 1=name
	if (!isset($orderby))		{$orderby='timeup';}
	if (!isset($SERVdisplay))	{$SERVdisplay=1;}	# 0=no, 1=yes
	if (!isset($CALLSdisplay))	{$CALLSdisplay=1;}	# 0=no, 1=yes

	
	while ( list( $line_num, $line ) = each( $fcontents ) ) {
			// Exit if the Verbosity line shows up - Obscured by only listing vm context 'default'
				//if ( substr($line,0,9) == "Verbosity") {
				//        break;
				//}
				// Ensuring only vm entries show up
				if ( substr($line,0,7) == "default" ) {
				echo "<tr><td><pre>" . $line . "</td></tr>";
				}
		}
	
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
	
	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
	if ($DB) {echo "|$stmt|\n";}
	if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
	
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];
	
	if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
		Header("WWW-Authenticate: Basic realm=\"OSDial-Administrator\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
	}

	$NOW_TIME = date("Y-m-d H:i:s");
	$NOW_DAY = date("Y-m-d");
	$NOW_HOUR = date("H:i:s");
	$STARTtime = date("U");
	$epochSIXhoursAGO = ($STARTtime - 21600);
	$timeSIXhoursAGO = date("Y-m-d H:i:s",$epochSIXhoursAGO);
	
	
	function HorizLine($Width) {
		for ($i = 1; $i <= $Width; $i++) {
			$HDLine.="&#x2550;";
		}
		return $HDLine;
	}
	function CenterLine($Width) {
		for ($i = 1; $i <= $Width; $i++) {
			$HDLine.="&#x2500;";
		}
		return $HDLine;
	}

	
	$stmt="select campaign_id from vicidial_campaigns where active='Y';";
	$rslt=mysql_query($stmt, $link);
	if (!isset($DB))   {$DB=0;}
	if ($DB) {echo "$stmt\n";}
	
	$groups_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $groups_to_print)	{
		$row=mysql_fetch_row($rslt);
		$groups[$i] =$row[0];
		$i++;
	}
	
	$stmt="select * from vicidial_user_groups;";
	$rslt=mysql_query($stmt, $link);
	if (!isset($DB))   {$DB=0;}
	if ($DB) {echo "$stmt\n";}
	$usergroups_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $usergroups_to_print) {
		$row=mysql_fetch_row($rslt);
		$usergroups[$i] =$row[0];
		$i++;
	}
	
	if (!isset($RR))   {$RR=4;}
	
	$NFB = '<b><font size=6 face="courier">';
	$NFE = '</font></b>';
	$F=''; $FG=''; $B=''; $BG='';
	
	?>
	
	<STYLE type="text/css">
	<!--
		.green {color: white; background-color: green}
		.red {color: white; background-color: red}
		.lightblue {color: black; background-color: #ADD8E6}
		.blue {color: white; background-color: blue}
		.midnightblue {color: white; background-color: #191970}
		.purple {color: white; background-color: purple}
		.violet {color: black; background-color: #EE82EE} 
		.thistle {color: black; background-color: #D8BFD8} 
		.olive {color: white; background-color: #808000}
		.yellow {color: black; background-color: yellow}
		.khaki {color: black; background-color: #F0E68C}
		.orange {color: black; background-color: orange}
	
		.r1 {color: black; background-color: #FFCCCC}
		.r2 {color: black; background-color: #FF9999}
		.r3 {color: black; background-color: #FF6666}
		.r4 {color: white; background-color: #FF0000}
		.b1 {color: black; background-color: #CCCCFF}
		.b2 {color: black; background-color: #9999FF}
		.b3 {color: black; background-color: #6666FF}
		.b4 {color: white; background-color: #0000FF}
	<?
	
	$stmt="select group_id,group_color from vicidial_inbound_groups;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$INgroups_to_print = mysql_num_rows($rslt);
	if ($INgroups_to_print > 0) {
		$g=0;
		while ($g < $INgroups_to_print) {
			$row=mysql_fetch_row($rslt);
			$group_id[$g] = $row[0];
			$group_color[$g] = $row[1];
			echo "   .$group_id[$g] {color: black; background-color: $group_color[$g]}\n";
			$g++;
		}
	}
	
	echo "\n-->\n
	</STYLE>\n";

	$stmt = "select count(*) from vicidial_campaigns where campaign_id='$group' and campaign_allow_inbound='Y';";
	$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$campaign_allow_inbound = $row[0];
		
	
	if (!isset($RR))   {$RR=4;}
	if ($RR=0)  {$RR=4;}
	
	//echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
	//echo "<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?RR=$RR&DB=$DB&group=$group&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">\n";
	
	echo "<div class=no-ul>";
	echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
	echo "<input type=hidden name=ADD value=14>\n";
	echo "<input type=hidden name=group value=$group>\n";
	echo "<p class=centered>";
	echo "<font color=navy>Campaign:</font> \n";
	echo "<INPUT TYPE=HIDDEN NAME=RR VALUE=\"$RR\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=adastats VALUE=\"$adastats\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=SIPmonitorLINK VALUE=\"$SIPmonitorLINK\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=IAXmonitorLINK VALUE=\"$IAXmonitorLINK\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=usergroup VALUE=\"$usergroup\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=UGdisplay VALUE=\"$UGdisplay\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=UidORname VALUE=\"$UidORname\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=orderby VALUE=\"$orderby\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=SERVdisplay VALUE=\"$SERVdisplay\">\n";
	echo "<INPUT TYPE=HIDDEN NAME=CALLSdisplay VALUE=\"$CALLSdisplay\">\n";
	echo "<SELECT SIZE=1 NAME=group>\n";
	echo "<option value=\"XXXX-ALL-ACTIVE-XXXX\">ALL ACTIVE</option>\n";
	$o=0;

	while ($groups_to_print > $o)	{
		if ($groups[$o] == $group) {
			echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";
		} else {
			echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";
		}
		$o++;
	}
	echo "</SELECT>\n";
	if ($UGdisplay > 0)	{
		echo "<SELECT SIZE=1 NAME=usergroup>\n";
		echo "<option value=\"\">ALL USER GROUPS</option>\n";
		$o=0;
		while ($usergroups_to_print > $o) {
			if ($usergroups[$o] == $usergroup) {
				echo "<option selected value=\"$usergroups[$o]\">$usergroups[$o]</option>\n";
			} else {
				echo "<option value=\"$usergroups[$o]\">$usergroups[$o]</option>\n";
			}
			$o++;
		}
		echo "</SELECT>\n";
	}
	echo "<INPUT type=submit NAME=SUBMIT VALUE=SUBMIT>";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=navy SIZE=2>&nbsp;&nbsp;&nbsp;&nbsp;Update: \n";
	echo "<a href=\"$PHP_SELF?ADD=$ADD&group=$group&RR=600&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">10min</a> | ";
	echo "<a href=\"$PHP_SELF?ADD=$ADD&group=$group&RR=30&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">30sec</a> | ";
	echo "<a href=\"$PHP_SELF?ADD=$ADD&group=$group&RR=4&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">4sec</a>";
	echo " &nbsp; &nbsp; &nbsp;<a href=\"./admin.php?ADD=31&campaign_id=$group\">Modify</a>&nbsp;\n";
	echo "<a href=\"$PHP_SELF?ADD=13&group=$group&RR=$RR&DB=$DB&adastats=$adastats\">Summary</a>&nbsp;\n";
	echo "</FONT>\n";
	echo "\n\n";

	
	if (!$group) {
		echo "<p class=indents>Please select a campaign!</p></FORM>\n"; 
	} else {
		$stmt="select auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout,dial_statuses from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$stmt="select avg(auto_dial_level),min(dial_status_a),min(dial_status_b),min(dial_status_c),min(dial_status_d),min(dial_status_e),min(lead_order),min(lead_filter_id),sum(hopper_level),min(dial_method),avg(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),min(available_only_ratio_tally),min(adaptive_latest_server_time),min(local_call_time),avg(dial_timeout),min(dial_statuses) from vicidial_campaigns;";
		}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$DIALlev =		$row[0];
		$DIALstatusA =	$row[1];
		$DIALstatusB =	$row[2];
		$DIALstatusC =	$row[3];
		$DIALstatusD =	$row[4];
		$DIALstatusE =	$row[5];
		$DIALorder =	$row[6];
		$DIALfilter =	$row[7];
		$HOPlev =		$row[8];
		$DIALmethod =	$row[9];
		$maxDIALlev =	$row[10];
		$DROPmax =		$row[11];
		$targetDIFF =	$row[12];
		$ADAintense =	$row[13];
		$ADAavailonly =	$row[14];
		$TAPERtime =	$row[15];
		$CALLtime =		$row[16];
		$DIALtimeout =	$row[17];
		$DIALstatuses =	$row[18];
		$DIALstatuses = (preg_replace("/ -$|^ /","",$DIALstatuses));
		$DIALstatuses = (ereg_replace(' ',', ',$DIALstatuses));
		
		$stmt="select count(*) from vicidial_hopper where campaign_id='" . mysql_real_escape_string($group) . "';";
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$stmt="select count(*) from vicidial_hopper;";
		}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$VDhop = $row[0];
		
		$stmt="select dialable_leads,calls_today,drops_today,drops_answers_today_pct,differential_onemin,agents_average_onemin,balance_trunk_fill,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4 from vicidial_campaign_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$stmt="select sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),min(status_category_1),sum(status_category_count_1),min(status_category_2),sum(status_category_count_2),min(status_category_3),sum(status_category_count_3),min(status_category_4),sum(status_category_count_4) from vicidial_campaign_stats;";
		}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$DAleads =		$row[0];
		$callsTODAY =	$row[1];
		$dropsTODAY =	$row[2];
		$drpctTODAY =	$row[3];
		$diffONEMIN =	$row[4];
		$agentsONEMIN = $row[5];
		$balanceFILL =	$row[6];
		$answersTODAY = $row[7];
		$VSCcat1 =		$row[8];
		$VSCcat1tally = $row[9];
		$VSCcat2 =		$row[10];
		$VSCcat2tally = $row[11];
		$VSCcat3 =		$row[12];
		$VSCcat3tally = $row[13];
		$VSCcat4 =		$row[14];
		$VSCcat4tally = $row[15];
		
		if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) ) {
			$diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
			$diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
		} else {
			$diffpctONEMIN = '0.00';
		}
		
		$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats;";
		}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$balanceSHORT = $row[0];

		echo "<BR><table cellpadding=0 cellspacing=0><TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALlev&nbsp;&nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>TRUNK SHORT/FILL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $balanceSHORT / $balanceFILL &nbsp;&nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>FILTER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALfilter &nbsp;&nbsp;</TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>TIME:</B></TD><TD ALIGN=LEFT><font size=2 color=navy>&nbsp; $NOW_TIME </TD>";
		echo "";
		echo "</TR>";
	
		if ($adastats>1) {
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2><B>MAX LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $maxDIALlev &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED MAX:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DROPmax% &nbsp; &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>TARGET DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $targetDIFF &nbsp; &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>INTENSITY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAintense &nbsp; &nbsp; </TD>";
			echo "</TR>";
		
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2><B>DIAL TIMEOUT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALtimeout &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>TAPER TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $TAPERtime &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>LOCAL TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $CALLtime &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>AVAIL ONLY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAavailonly &nbsp;</TD>";
			echo "</TR>";
			
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2><B>DL DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffONEMIN &nbsp; &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffpctONEMIN% &nbsp; &nbsp; </TD>";
			echo "</TR>";
		}

		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIALABLE LEADS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DAleads &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>AVG AGENTS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $agentsONEMIN &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL METHOD:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALmethod &nbsp; &nbsp; </TD>";
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>HOPPER LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $HOPlev &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED / ANSWERED:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY / $answersTODAY &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>STATUSES:</B></TD><TD ALIGN=LEFT colspan=3><font size=2>&nbsp; $DIALstatuses &nbsp; &nbsp; </TD>";
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>LEADS IN HOPPER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $VDhop &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; ";
		if ($drpctTODAY >= $DROPmax) {
			echo "<font color=red><B>$drpctTODAY%</B></font>";
		} else {
			echo "$drpctTODAY%";
		}
		echo " &nbsp; &nbsp;</TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>ORDER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALorder &nbsp; &nbsp; </TD>";
		echo "</tr><tr>";
		if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
			echo "<TD ALIGN=right><font size=2 color=navy><B>$VSCcat1:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1tally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
			echo "<TD ALIGN=right><font size=2 color=navy><B>$VSCcat2:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2tally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) {
			echo "<TD ALIGN=right><font size=2 color=navy><B>$VSCcat3:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3tally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
			echo "<TD ALIGN=right><font size=2 color=navy><B>$VSCcat4:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4tally&nbsp;&nbsp;</td>\n";
		}
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=LEFT COLSPAN=8>";

		if ($adastats<2) {
			echo "<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=2&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>VIEW MORE SETTINGS</font></a>";
		} else {
			echo "<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=1&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>VIEW LESS SETTINGS</font></a>";
		}
		if ($UGdisplay>0) {
			echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=0&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>HIDE USER GROUP</font></a>";
		} else {
			echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=1&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>VIEW USER GROUP</font></a>";
		}
		if ($UidORname>0) {
			echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=0&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>SHOW AGENT ID</font></a>";
		} else {
			echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=1&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>SHOW AGENT NAME</font></a>";
		}
		if ($SERVdisplay>0) {
			echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=0&CALLSdisplay=$CALLSdisplay\"><font size=1>HIDE SERVER INFO</font></a>";
		} else {
			echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=1&CALLSdisplay=$CALLSdisplay\"><font size=1>SHOW SERVER INFO</font></a>";
		}
		if ($CALLSdisplay>0) {
			echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=0\"><font size=1>HIDE WAITING CALLS DETAIL</font></a>";
		} else {
			echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=1\"><font size=1>SHOW WAITING CALLS DETAIL</font></a>";
		}
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
		
		echo "</FORM>\n\n";
	}

	###################################################################################
	###### INBOUND/OUTBOUND CALLS
	###################################################################################
	if ($campaign_allow_inbound > 0) {
		$stmt="select closer_campaigns from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$closer_campaigns = preg_replace("/^ | -$/","",$row[0]);
		$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
		$closer_campaigns = "'$closer_campaigns'";
	
		$stmtB="from vicidial_auto_calls where status NOT IN('XFER') and ( (call_type='IN' and campaign_id IN($closer_campaigns)) or (campaign_id='" . mysql_real_escape_string($group) . "' and call_type IN('OUT','OUTBALANCE')) ) order by campaign_id,call_time;";
	} else {
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$groupSQL = '';
		} else {
			$groupSQL = " and campaign_id='" . mysql_real_escape_string($group) . "'";
		}
	
		$stmtB="from vicidial_auto_calls where status NOT IN('XFER') $groupSQL order by campaign_id,call_time;";
	}
	if ($CALLSdisplay > 0) {
		$stmtA = "SELECT status,campaign_id,phone_number,server_ip,UNIX_TIMESTAMP(call_time),call_type";
	} else {
		$stmtA = "SELECT status";
	}
	
	
	$k=0;
	$stmt = "$stmtA $stmtB";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	
	$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0) {
		$i=0;
		$out_total=0;
		$out_ring=0;
		$out_live=0;
		while ($i < $parked_to_print)	{
			$row=mysql_fetch_row($rslt);
	
			if (eregi("LIVE",$row[0])) {
				$out_live++;
	
				if ($CALLSdisplay > 0) {
					$CDstatus[$k] =			$row[0];
					$CDcampaign_id[$k] =	$row[1];
					$CDphone_number[$k] =	$row[2];
					$CDserver_ip[$k] =		$row[3];
					$CDcall_time[$k] =		$row[4];
					$CDcall_type[$k] =		$row[5];
					$k++;
				}
			} else {
				if (eregi("CLOSER",$row[0])) {
					$nothing=1;
				} else {
					$out_ring++;
				}
			}
	
			$out_total++;
			$i++;
		}
	
		if ($out_live > 0) {$F='<FONT class="r1">'; $FG='</FONT>';}
		if ($out_live > 4) {$F='<FONT class="r2">'; $FG='</FONT>';}
		if ($out_live > 9) {$F='<FONT class="r3">'; $FG='</FONT>';}
		if ($out_live > 14) {$F='<FONT class="r4">'; $FG='</FONT>';}

		if ($campaign_allow_inbound > 0) {
			echo "$NFB$out_total$NFE <font color=blue>current active calls</font> &nbsp; &nbsp; &nbsp; \n";
		} else {
			echo "$NFB$out_total$NFE <font color=blue>calls being placed</font> &nbsp; &nbsp; &nbsp; \n";
		}
		
		echo "$NFB$out_ring$NFE <font color=blue>calls ringing</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$F &nbsp;$out_live $FG$NFE <font color=blue>calls waiting for agents</font> &nbsp; &nbsp; &nbsp; \n";
	} else {
		echo "&nbsp;<font color=red>NO LIVE CALLS WAITING</font>&nbsp;";
	}
	
	
	
	###################################################################################
	###### CALLS WAITING
	###################################################################################
	// Changed to draw solid lines
	$LNtopleft="&#x2554;";
	$LNleft="&#x2551;";
	$LNright="&#x2551;";
	$LNcenterleft="&#x255F;";
	$LNcenterbar="&#x2502;";
	$LNtopdown="&#x2564;";
	$LNtopright="&#x2557;";
	$LNbottomleft="&#x255A;";
	$LNbottomright="&#x255D;";
	$LNcentcross="&#x253C;";
	$LNcentright="&#x2562;";
	$LNbottomup="&#x2567;";
	// column length 8|14|14|17|9|12
	$Cecho = '';
	$Cecho .= "<font color=navy>&nbsp;&nbsp;OSDial: Calls Waiting                      $NOW_TIME\n";
	//$Cecho .= "+--------+--------------+--------------+-----------------+---------+------------+\n";
	$Cecho .=$LNtopleft.HorizLine(8).$LNtopdown.HorizLine(14).$LNtopdown.HorizLine(14).$LNtopdown.HorizLine(17).$LNtopdown.HorizLine(9).$LNtopdown.HorizLine(12).$LNtopright."<br>";
	$Cecho .="$LNleft STATUS $LNcenterbar CAMPAIGN     $LNcenterbar PHONE NUMBER $LNcenterbar SERVER_IP       $LNcenterbar DIALTIME$LNcenterbar CALL TYPE  $LNright\n";
	$Cecho .=$LNcenterleft.CenterLine(8).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."<br>";
	//$Cecho .= "| STATUS | CAMPAIGN     | PHONE NUMBER | SERVER_IP       | DIALTIME| CALL TYPE  |\n";
	//$Cecho .= "+--------+--------------+--------------+-----------------+---------+------------+\n";
	
	/*
	$Cecho .=$LNtopleft.HorizLine(8).$LNtopdown.HorizLine(14).HorizLine(14).$LNtopdown.HorizLine(17).$LNtopdown.HorizLine(9).$LNtopdown.HorizLine(12).$LNtopright."<br>";
	$Cecho .="$LNleft STATUS $LNcenterbar CAMPAIGN     $LNcenterbar PHONE NUMBER $LNcenterbar SERVER_IP       $LNcenterbar DIALTIME$LNcenterbar CALL TYPE  $LNright\n";
	$Cecho .=$LNbottomleft.HorizLine(8).$LNtopup.HorizLine(14).$LNtopup.HorizLine(14).$LNtopup.HorizLine(17).$LNtopup.HorizLine(9).$LNtopup.HorizLine(12).$LNbottomright;
	*/
	
	$p=0;
	while($p<$k) {
		$Cstatus =			sprintf("%-6s", $CDstatus[$p]);
		$Ccampaign_id =		sprintf("%-12s", $CDcampaign_id[$p]);
		$Cphone_number =	sprintf("%-12s", $CDphone_number[$p]);
		$Cserver_ip =		sprintf("%-15s", $CDserver_ip[$p]);
		$Ccall_type =		sprintf("%-10s", $CDcall_type[$p]);
	
		$Ccall_time_S = ($STARTtime - $CDcall_time[$p]);
		$Ccall_time_M = ($Ccall_time_S / 60);
		$Ccall_time_M = round($Ccall_time_M, 2);
		$Ccall_time_M_int = intval("$Ccall_time_M");
		$Ccall_time_SEC = ($Ccall_time_M - $Ccall_time_M_int);
		$Ccall_time_SEC = ($Ccall_time_SEC * 60);
		$Ccall_time_SEC = round($Ccall_time_SEC, 0);
		if ($Ccall_time_SEC < 10) {$Ccall_time_SEC = "0$Ccall_time_SEC";}
		$Ccall_time_MS = "$Ccall_time_M_int:$Ccall_time_SEC";
		$Ccall_time_MS =		sprintf("%7s", $Ccall_time_MS);
	
		$G = '';		$EG = '';
		if ($CDcall_type[$p] == 'IN')	{
			$G="<SPAN class=\"$CDcampaign_id[$p]\"><B>"; $EG='</B></SPAN>';
		}
		$Cecho .= "$LNleft $G$Cstatus$EG $LNcenterbar $G$Ccampaign_id$EG $LNcenterbar $G$Cphone_number$EG $LNcenterbar $G$Cserver_ip$EG $LNcenterbar $G$Ccall_time_MS$EG $LNcenterbar $G$Ccall_type$EG $LNright\n";
	
		$p++;
	}
	
	//$Cecho .= "+--------+--------------+--------------+-----------------+---------+------------+\n\n";
	$Cecho .=$LNbottomleft.HorizLine(8).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."<br></font>";
	
	if ($p<1) {$Cecho='';}
	
	###################################################################################
	###### TIME ON SYSTEM
	###################################################################################
	
	
	$agent_incall=0;
	$agent_ready=0;
	$agent_paused=0;
	$agent_total=0;
	$Aecho = '';
	$Aecho .= "<font color=navy size=1 face=fixed,monospace>&nbsp;&nbsp;OSDial: Agents Time On Calls Campaign: $group                      $NOW_TIME\n";
	
	// Changed to draw solid lines
	$LNtopleft="&#x2554;";
	$LNleft="&#x2551;";
	$LNright="&#x2551;";
	$LNcenterleft="&#x255F;";
	$LNcenterbar="&#x2502;";
	$LNtopdown="&#x2564;";
	$LNtopright="&#x2557;";
	$LNbottomleft="&#x255A;";
	$LNbottomright="&#x255D;";
	$LNcentcross="&#x253C;";
	$LNcentright="&#x2562;";
	$LNbottomup="&#x2567;";
	
	$HDbegin =	"&#x2554;"; // top left double line 
	$HTbegin =	"&#x2502;"; // |
	$HDstation =	HorizLine(12)."&#x2564;"; //12
	$HTstation =	"  STATION   &#x2502;";
	$HDuser =		HorizLine(20)."&#x2564;"; //20
	$HTuser =		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=userup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">USER</a>          &#x2502;";
	$HDusergroup =		HorizLine(14)."&#x2564;"; //14
	$HTusergroup =		" <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=groupup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">USER GROUP</a>   &#x2502;";
	$HDsessionid =		HorizLine(11)."&#x2564;"; //18
	$HTsessionid =		" SESSIONID       &#x2502;";
	$HDbarge =		HorizLine(7)."&#x2564;"; //7
	$HTbarge =		" BARGE &#x2502;";
	$HDstatus =		HorizLine(10)."&#x2564;"; //10
	$HTstatus =		"  STATUS  &#x2502;";
	$HDserver_ip =		HorizLine(17)."&#x2564;"; //17
	$HTserver_ip =		"    SERVER IP    &#x2502;";
	$HDcall_server_ip =	HorizLine(17)."&#x2564;"; //17
	$HTcall_server_ip =	" CALL SERVER IP  &#x2502;";
	$HDtime =			HorizLine(9)."&#x2564;"; //9
	$HTtime =			"&nbsp;&nbsp;<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=timeup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">MM:SS</a>  &#x2502;";
	$HDcampaign =		HorizLine(12)."&#x2557;"; //12
	$HTcampaign =		"&nbsp;&nbsp;<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=campaignup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">CAMPAIGN</a>  &#x2551;";
	
	if ($UGdisplay < 1)	{
		$HDusergroup =	'';
		$HTusergroup =	'';
	}
	if ( ($SIPmonitorLINK<1) && ($IAXmonitorLINK<1) ) {
		$HDsessionid =	HorizLine(11)."&#x2564";; //11
		$HTsessionid =	" SESSIONID &#x2502;";
	}
	if ( ($SIPmonitorLINK<2) && ($IAXmonitorLINK<2) ) {
		$HDbarge =		'';
		$HTbarge =		'';
	}
	if ($SERVdisplay < 1)	{
		$HDserver_ip =		'';
		$HTserver_ip =		'';
		$HDcall_server_ip =	'';
		$HTcall_server_ip =	'';
	}
		
	$Aline  = "$LNtopleft$HDstation$HDuser$HDusergroup$HDsessionid$HDbarge$HDstatus$HDserver_ip$HDcall_server_ip$HDtime$HDcampaign\n";
	$Bline  = "$LNleft$HTstation$HTuser$HTusergroup$HTsessionid$HTbarge$HTstatus$HTserver_ip$HTcall_server_ip$HTtime$HTcampaign\n";
	if ($UGdisplay < 1)	{
		$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$LNcentcross.CenterLine(11).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
		$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$LNbottomup.HorizLine(11).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";
		if ($SERVdisplay < 1) {
			$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$LNcentcross.CenterLine(11).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
			$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$LNbottomup.HorizLine(11).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";
		}
	} else {	
		$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(11).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
		$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(11).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";
		if ($SERVdisplay < 1) {
			$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(11).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
			$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(11).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";
		}
	}
	
	
	
	
	$Aecho .= "$Aline";
	$Aecho .= "$Bline";
	$Aecho .= "$Cline";
	
	if ($orderby=='timeup') {$orderSQL='status,last_call_time';}
	if ($orderby=='timedown') {$orderSQL='status desc,last_call_time desc';}
	if ($orderby=='campaignup') {$orderSQL='campaign_id,status,last_call_time';}
	if ($orderby=='campaigndown') {$orderSQL='campaign_id desc,status desc,last_call_time desc';}
	if ($orderby=='groupup') {$orderSQL='user_group,status,last_call_time';}
	if ($orderby=='groupdown') {$orderSQL='user_group desc,status desc,last_call_time desc';}
	if ($UidORname > 0) {
		if ($orderby=='userup') {$orderSQL='full_name,status,last_call_time';}
		if ($orderby=='userdown') {$orderSQL='full_name desc,status desc,last_call_time desc';}
	} else {
		if ($orderby=='userup') {$orderSQL='vicidial_live_agents.user';}
		if ($orderby=='userdown') {$orderSQL='vicidial_live_agents.user desc';}
	}
	
	if ($group=='XXXX-ALL-ACTIVE-XXXX') {
		$groupSQL = '';
	} else {
		$groupSQL = " and campaign_id='" . mysql_real_escape_string($group) . "'";
	}
	if (strlen($usergroup)<1) {
		$usergroupSQL = '';
	} else {
		$usergroupSQL = " and user_group='" . mysql_real_escape_string($usergroup) . "'";
	}
	
	$stmt="select extension,vicidial_live_agents.user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id,vicidial_users.user_group,vicidial_users.full_name,vicidial_live_agents.comments from vicidial_live_agents,vicidial_users where vicidial_live_agents.user=vicidial_users.user $groupSQL $usergroupSQL order by $orderSQL;";
	
	#$stmt="select extension,vicidial_live_agents.user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id,vicidial_users.user_group,vicidial_users.full_name from vicidial_live_agents,vicidial_users where vicidial_live_agents.user=vicidial_users.user and campaign_id='" . mysql_real_escape_string($group) . "' order by $orderSQL;";
	
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	
	$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0) {
		$i=0;
		$agentcount=0;
		while ($i < $talking_to_print) {
			$row=mysql_fetch_row($rslt);
			if (eregi("READY|PAUSED",$row[3])) {
				$row[5]=$row[6];
			}
			if ($non_latin < 1) {
				$extension = eregi_replace('Local/',"",$row[0]);
				$extension =		sprintf("%-10s", $extension);
				while(strlen($extension)>10) {
					$extension = substr("$extension", 0, -1);
				}
			} else {
				$extension = eregi_replace('Local/',"",$row[0]);
				$extension =		sprintf("%-40s", $extension);
				while(mb_strlen($extension, 'utf-8')>10) {
					$extension = mb_substr("$extension", 0, -1,'utf8');
				}
			}
			$Luser =			$row[1];
			$user =				sprintf("%-18s", $row[1]);
			$Lsessionid =		$row[2];
			$sessionid =		sprintf("%-9s", $row[2]);
			$Lstatus =			$row[3];
			$status =			sprintf("%-6s", $row[3]);
			$server_ip =		sprintf("%-15s", $row[4]);
			$call_server_ip =	sprintf("%-15s", $row[7]);
			$campaign_id =	sprintf("%-10s", $row[8]);
			$comments=		$row[11];
	
			if (eregi("INCALL",$Lstatus)) {
				if ( (eregi("AUTO",$comments)) or (strlen($comments)<1) ) {
					$CM='A';
				} else {
					if (eregi("INBOUND",$comments)) {
						$CM='I';
					} else {
						$CM='M';
					}
				} 
			} else {
				$CM=' ';
			}
	
			if ($UGdisplay > 0) {
				if ($non_latin < 1) {
					$user_group =		sprintf("%-12s", $row[9]);
					while(strlen($user_group)>12) {
						$user_group = substr("$user_group", 0, -1);
					}
				} else {
					$user_group =		sprintf("%-40s", $row[9]);
					while(mb_strlen($user_group, 'utf-8')>12) {
						$user_group = mb_substr("$user_group", 0, -1,'utf8');
					}
				}
			}
			if ($UidORname > 0) {
				if ($non_latin < 1) {
					$user =		sprintf("%-18s", $row[10]);
					while(strlen($user)>18) {
						$user = substr("$user", 0, -1);
					}
				} else {
					$user =		sprintf("%-40s", $row[10]);
					while(mb_strlen($user, 'utf-8')>18) {
						$user = mb_substr("$user", 0, -1,'utf8');
					}
				}
			}
			if (!eregi("INCALL|QUEUE",$row[3])) {
				$call_time_S = ($STARTtime - $row[6]);
			} else {
				$call_time_S = ($STARTtime - $row[5]);
			}
	
			$call_time_M = ($call_time_S / 60);
			$call_time_M = round($call_time_M, 2);
			$call_time_M_int = intval("$call_time_M");
			$call_time_SEC = ($call_time_M - $call_time_M_int);
			$call_time_SEC = ($call_time_SEC * 60);
			$call_time_SEC = round($call_time_SEC, 0);
			if ($call_time_SEC < 10) {
				$call_time_SEC = "0$call_time_SEC";
			}
			$call_time_MS = "$call_time_M_int:$call_time_SEC";
			$call_time_MS =		sprintf("%7s", $call_time_MS);
			$G = '';		$EG = '';
			if ($Lstatus=='INCALL') {
				if ($call_time_S >= 10) {$G='<SPAN class="thistle"><B>'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 1) {$G='<SPAN class="violet"><B>'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 5) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
		#		if ($call_time_M_int >= 10) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
		
			}
			if (eregi("PAUSED",$row[3])) {
				if ($call_time_M_int >= 360) {
					$i++; continue;
				} else {
					$agent_paused++;  $agent_total++;
					$G=''; $EG='';
					if ($call_time_S >= 10) {$G='<SPAN class="khaki"><B>'; $EG='</B></SPAN>';}
					if ($call_time_M_int >= 1) {$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';}
					if ($call_time_M_int >= 5) {$G='<SPAN class="olive"><B>'; $EG='</B></SPAN>';}
				}
			}
	#		if ( (strlen($row[7])> 4) and ($row[7] != "$row[4]") )
	#				{$G='<SPAN class="orange"><B>'; $EG='</B></SPAN>';}
	
			if ( (eregi("INCALL",$status)) or (eregi("QUEUE",$status)) ) {$agent_incall++;  $agent_total++;}
			if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {$agent_ready++;  $agent_total++;}
			if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {
				$G='<SPAN class="lightblue"><B>'; $EG='</B></SPAN>';
				if ($call_time_M_int >= 1) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 5) {$G='<SPAN class="midnightblue"><B>'; $EG='</B></SPAN>';}
			}
	
			$L='';
			$R='';
			if ($SIPmonitorLINK>0) {$L=" <a href=\"sip:6$Lsessionid@$server_ip\">LISTEN</a>";   $R='';}
			if ($IAXmonitorLINK>0) {$L=" <a href=\"iax:6$Lsessionid@$server_ip\">LISTEN</a>";   $R='';}
			if ($SIPmonitorLINK>1) {$R=" $LNcenterbar <a href=\"sip:$Lsessionid@$server_ip\">BARGE</a>";}
			if ($IAXmonitorLINK>1) {$R=" $LNcenterbar <a href=\"iax:$Lsessionid@$server_ip\">BARGE</a>";}
	
			//if ($UGdisplay > 0)	{$UGD = " $G$user_group$EG $LNcenterbar";}
			if ($UGdisplay > 0)	{
				$UGD = " $user_group $LNcenterbar";
			} else {
				$UGD = "";
			}
	
			//if ($SERVdisplay > 0)	{$SVD = "$G$server_ip$EG $LNcenterbar $G$call_server_ip$EG $LNcenterbar ";}
			if ($SERVdisplay > 0)	{
				$SVD = "$server_ip $LNcenterbar $call_server_ip $LNcenterbar ";
			} else {
				$SVD = "";
			}
	
			$agentcount++;
	
			//$Aecho .= "$LNleft $G$extension$EG $LNcenterbar <a href=\"./user_status.php?user=$Luser\" target=\"_blank\">$G$user$EG</a> $LNcenterbar$UGD $G$sessionid$EG$L$R $LNcenterbar $G$status$EG $CM $LNcenterbar $SVD$G$call_time_MS$EG $LNcenterbar $G$campaign_id$EG $LNright\n";
			
			$Aecho .= "$LNleft $G$extension $LNcenterbar <a href=\"./user_status.php?user=$Luser\" target=\"_blank\">$G$user$EG</a> $LNcenterbar$UGD $sessionid$L$R $LNcenterbar $status $CM $LNcenterbar $SVD$call_time_MS $LNcenterbar $campaign_id$EG $LNright\n";
	
			$i++;
		}

		$Aecho .= "$Dline";
		
		$Aecho .= "  $agentcount <font color=navy>agents logged in on all servers</font>\n";
		$Aecho .= "<PRE><FONT face=Fixed,monospace SIZE=1>";
		if (file_exists('S1_load.txt')) {
			$Aecho .= "  <font color=navy>Apache   Load Average:</font> $load_ave<br>";
			$Aecho .= "  <font color=navy>MySQL    Load Average:</font> $load_ave_s1<br>";
		} elseif (!file_exists('D1_load.txt')&& !file_exists('D2_load.txt') && !file_exists('D3_load.txt') && !file_exists('D4_load.txt') && !file_exists('D5_load.txt') && !file_exists('D6_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer Load Average:</font> $load_ave<br>";
		} else {
			$Aecho .= "  <font color=navy>SQL/Web  Load Average:</font> $load_ave<br>";
		}
		if (file_exists('D1_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 1 Load Average:</font> $load_ave_d1";
		}
		if (file_exists('D2_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 2 Load Average:</font> $load_ave_d2";
		}
		if (file_exists('D3_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 3 Load Average:</font> $load_ave_d3";
		}
		if (file_exists('D4_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 4 Load Average:</font> $load_ave_d4";
		}
		if (file_exists('D5_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 5 Load Average:</font> $load_ave_d5";
		}
		if (file_exists('D6_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 6 Load Average:</font> $load_ave_d6";
		}
		$Aecho .= "<br></font>";
		
	#	$Aecho .= "  <SPAN class=\"orange\"><B>          </SPAN> - Balanced call</B>\n";
	/*	$Aecho .= "  <font color=navy><SPAN class=\"lightblue\"><B>          </SPAN> - Agent waiting for call</B>\n";
		$Aecho .= "  <SPAN class=\"blue\"><B>          </SPAN> - Agent waiting for call > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"midnightblue\"><B>          </SPAN> - Agent waiting for call > 5 minutes</B>\n";
		$Aecho .= "  <SPAN class=\"thistle\"><B>          </SPAN> - Agent on call > 10 seconds</B>\n";
		$Aecho .= "  <SPAN class=\"violet\"><B>          </SPAN> - Agent on call > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"purple\"><B>          </SPAN> - Agent on call > 5 minutes</B>\n";
		$Aecho .= "  <SPAN class=\"khaki\"><B>          </SPAN> - Agent Paused > 10 seconds</B>\n";
		$Aecho .= "  <SPAN class=\"yellow\"><B>          </SPAN> - Agent Paused > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"olive\"><B>          </SPAN> - Agent Paused > 5 minutes</B></font>\n";
*/
		if ($agent_ready > 0) {$B='<FONT class="b1">'; $BG='</FONT>';}
		if ($agent_ready > 4) {$B='<FONT class="b2">'; $BG='</FONT>';}
		if ($agent_ready > 9) {$B='<FONT class="b3">'; $BG='</FONT>';}
		if ($agent_ready > 14) {$B='<FONT class="b4">'; $BG='</FONT>';}
	
	
		echo "\n<BR>\n";

		echo "$NFB$agent_total$NFE <font color=blue>agents logged in</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_incall$NFE <font color=blue>agents in calls</font> &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$B &nbsp;$agent_ready $BG$NFE <font color=blue>agents waiting</font> &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_paused$NFE <font color=blue>paused agents</font> &nbsp; &nbsp; &nbsp; \n";
		
		echo "<PRE><FONT SIZE=2>";
		echo "";
		echo "$Cecho";
		echo "$Aecho";
		
		echo "<table width=730><tr><td>";
		echo "  <font color=navy><SPAN class=\"khaki\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 10 seconds</B><br>";
		echo "  <SPAN class=\"yellow\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 1 minute</B><br>";
		echo "  <SPAN class=\"olive\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 5 minutes</B></font>";
		echo "</td><td>";
		echo "  <font color=navy><SPAN class=\"lightblue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call</B><br>";
		echo "  <SPAN class=\"blue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call > 1 minute</B><br>";
		echo "  <SPAN class=\"midnightblue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call > 5 minutes</B>";
		echo "</td><td>";
		echo "  <font color=navy><SPAN class=\"thistle\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 10 seconds</B><br>";
		echo "  <SPAN class=\"violet\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 1 minute</B><br>";
		echo "  <SPAN class=\"purple\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 5 minutes</B>";
		echo "</td></tr></table>";
	} else {
		echo "&nbsp;&nbsp;<font color=red>&bull;&nbsp;&nbsp;NO AGENTS ON CALLS</font> \n";
		$Aecho ="<br><br>";
		$Aecho .= "<PRE><FONT face=Fixed,monospace SIZE=1>";
		if (file_exists('S1_load.txt')) {
			$Aecho .= "  <font color=navy>Apache   Load Average:</font> $load_ave<br>";
			$Aecho .= "  <font color=navy>MySQL    Load Average:</font> $load_ave_s1<br>";
		} elseif (!file_exists('D1_load.txt')&& !file_exists('D2_load.txt') && !file_exists('D3_load.txt') && !file_exists('D4_load.txt') && !file_exists('D5_load.txt') && !file_exists('D6_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer Load Average:</font> $load_ave<br>";
		} else {
			$Aecho .= "  <font color=navy>SQL/Web  Load Average:</font> $load_ave<br>";
		}
		if (file_exists('D1_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 1 Load Average:</font> $load_ave_d1";
		}
		if (file_exists('D2_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 2 Load Average:</font> $load_ave_d2";
		}
		if (file_exists('D3_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 3 Load Average:</font> $load_ave_d3";
		}
		if (file_exists('D4_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 4 Load Average:</font> $load_ave_d4";
		}
		if (file_exists('D5_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 5 Load Average:</font> $load_ave_d5";
		}
		if (file_exists('D6_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 6 Load Average:</font> $load_ave_d6";
		}
		echo "$Aecho";
	}
	echo "</pre>";
	//echo "<tr><td colspan=10>";
	
	echo "</td>";
	echo "<TABLE WIDTH='<?=$page_width ?>' BGCOLOR=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>";
}



######################
# ADD=111 display the ADD NEW LIST FORM SCREEN
######################

if ($ADD==111)
{
	if ($LOGmodify_lists==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW LIST</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=211>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List ID: </td><td align=left><input type=text name=list_id size=8 maxlength=8> (digits only)$NWB#osdial_lists-list_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Description: </td><td align=left><input type=text name=list_description size=30 maxlength=255>$NWB#osdial_lists-list_description$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";

		$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
		$rslt=mysql_query($stmt, $link);
		$campaigns_to_print = mysql_num_rows($rslt);
		$campaigns_list='';

		$o=0;
		while ($campaigns_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
		}
	echo "$campaigns_list";
	echo "<option SELECTED>$campaign_id</option>\n";
	echo "</select>$NWB#osdial_lists-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_lists-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=112 admin_search_lead.php
######################

if ($ADD==112) {

	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<center><br><font color=navy size=+1>SEARCH FOR A LEAD</font>\n";
	
	### admin_search_lead.php
	### 
	### Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
	###
	### AST GUI database administration search for lead info
	### admin_modify_lead.php
	#
	# this is the administration lead information modifier screen, the administrator just needs to enter the leadID and then they can view and modify the information in the record for that lead
	#
	# changes:
	# 60620-1055 - Added variable filtering to eliminate SQL injection attack threat
	#            - Added required user/pass to gain access to this page
	#            - Changed results to multi-record
	#
	
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
	
	$STARTtime = date("U");
	$TODAY = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");
	
	
	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_leads='1';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];
	
	if ($WeBRooTWritablE > 0) {
		$fp = fopen ("./project_auth_entries.txt", "a");
	}
	
	$date = date("r");
	$ip = getenv("REMOTE_ADDR");
	$browser = getenv("HTTP_USER_AGENT");
	
	if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
		Header("WWW-Authenticate: Basic realm=\"OSDial-Administrator\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
	} else {
	
		if($auth>0) {
			$office_no=strtoupper($PHP_AUTH_USER);
			$password=strtoupper($PHP_AUTH_PW);
				$stmt="SELECT full_name,modify_leads from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$LOGfullname				=$row[0];
				$LOGmodify_leads			=$row[1];
	
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "OSDial|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
				fclose($fp);
			}
		} else {
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "OSDial|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
				fclose($fp);
			}
		}
	}
	
	
	if ( (!$vendor_id) and (!$phone)  and (!$lead_id) ) {
		echo "<style type=text/css> content {vertical-align:center}</style>";
		echo "\n<br><br><center>\n";
		echo "<TABLE width=$section_width cellspacing=0 bgcolor=#C1D6DF>\n";
		echo "<tr><td colspan=2>\n";
		echo "<form method=post name=search action=\"$PHP_SELF\">\n";
		echo "<input type=hidden name=ADD value=112>\n";
		echo "<input type=hidden name=DB value=\"$DB\">\n";
		echo "<br><center><font color=navy>Enter one of the following</font></center></td>";
		echo "</tr>";
		echo "<tr colspan=1>\n";
		echo "	<td align=right width=50%>Vendor ID (vendor lead code):&nbsp;</td>";
		echo "	<td width=50%><input type=text name=vendor_id size=10 maxlength=10></td>";
		echo "</tr>";
		echo "<tr> \n";
		echo "	<td align=right>Home Phone:&nbsp;</td>";
		echo "	<td align=left><input type=text name=phone size=10 maxlength=10></td>";
		echo "</tr>";
		echo "<tr> \n";
		echo "	<td align=right>Lead ID:&nbsp;</td>";
		echo "	<td align=left><input type=text name=lead_id size=10 maxlength=10></td>";
		echo "</tr>\n";
		echo "<tr>";
		echo "<th colspan=2><center><br><input type=submit name=submit value=SUBMIT></b></center><br></th>\n";
		echo "</form>\n";
		echo "</tr>";
		echo "</table>\n";
	
	/*
	echo "<tr bgcolor=#C1D6DF><td align=right>List ID: </td><td align=left><input type=text name=list_id size=8 maxlength=8> (digits only)$NWB#osdial_lists-list_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
	*/
	
	} else {
	
		if ($vendor_id) {
			$stmt="SELECT * from vicidial_list where vendor_lead_code='" . mysql_real_escape_string($vendor_id) . "' order by modify_date desc limit 1000";
		} else {
			if ($phone) {
				$stmt="SELECT * from vicidial_list where phone_number='" . mysql_real_escape_string($phone) . "' order by modify_date desc limit 1000";
			} else {
				if ($lead_id) {
					$stmt="SELECT * from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "' order by modify_date desc limit 1000";
				} else {
					print "ERROR: you must search for something! Go back and search for something";
					exit;
				}
			}
		}
		if ($DB) {
			echo "\n\n$stmt\n\n";
		}
		
		$rslt=mysql_query($stmt, $link);
		$results_to_print = mysql_num_rows($rslt);
		if ($results_to_print < 1) {
			//echo date("l F j, Y G:i:s A");
			echo "<br><br><br><center>\n";
			echo "<font size=3 color=navy>The item(s) you search for were not found.<br><br>\n";
			echo "You can click on \"Browser Back\" and double check the information you entered.</font>\n";
			echo "</center>\n";
		} else {
			echo "<p<font color=navy size=+1>Found:&nbsp;$results_to_print</font></b></p>";
			echo "<font size=1>";
			echo "<TABLE BGCOLOR=WHITE CELLPADDING=1 CELLSPACING=0>\n";
			echo "<TR BGCOLOR=#716A5B>\n";
			echo "<TD ALIGN=LEFT><FONT COLOR=WHITE size=2>#</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Lead ID</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Status</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Vendor ID</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Last Agent</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>List ID</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Phone</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Name</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>City</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Security</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Last Call</FONT></TD>\n";
			echo "</TR>\n";
			$o=0;
			while ($results_to_print > $o) {
				$row=mysql_fetch_row($rslt);
				$o++;
				if (eregi("1$|3$|5$|7$|9$", $o)) {
					$bgcolor='bgcolor="#CBDCE0"';
				} else {
					$bgcolor='bgcolor="#C1D6DB"';
				}
				echo "<TR $bgcolor>\n";
				echo "<TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$o</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1><a href=\"admin_modify_lead.php?lead_id=$row[0]\">$row[0]</a></FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[3]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[5]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[4]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[7]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[11]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[13] $row[15]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[19]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[28]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[2]</FONT></TD>\n";
				echo "</TR>\n";
			}
			echo "</TABLE>\n";
		}
	}
}





######################
# ADD=121 display the ADD NUMBER TO DNC FORM SCREEN and add a new number
######################

if ($ADD==121)
{
echo "<TABLE align=center><TR><TD align=center>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

if (strlen($phone_number) > 2) {
	$stmt="SELECT count(*) from vicidial_dnc where phone_number='$phone_number';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0) {
		echo "<br>DNC NOT ADDED - This phone number is already in the Do Not Call List: $phone_number<BR><BR>\n";
	} else {
		$stmt="INSERT INTO vicidial_dnc (phone_number) values('$phone_number');";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B>DNC ADDED: $phone_number</B><BR><BR>\n";

		### LOG INSERTION TO LOG FILE ###
		if ($WeBRooTWritablE > 0) {
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|ADD A NEW DNC NUMBER|$PHP_AUTH_USER|$ip|'$phone_number'|\n");
			fclose($fp);
		}
	}
}

echo "<br><font color=navy size=+1>ADD A NUMBER TO THE DNC LIST</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=121>\n";
//echo "<center>";
echo "<TABLE width=$section_width cellspacing=3>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Phone Number: </td><td align=left><input type=text name=phone_number size=14 maxlength=12> (digits only)$NWB#osdial_list-dnc$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
echo "</TABLE></center>\n";

}


######################
# ADD=122 new_listloader_superL.php
######################

if ($ADD==122)
{

# Copyright (C) 2008  Matt Florell,Joe Johnson <vicidial@gmail.com>    LICENSE: GPLv2
#
# AST GUI lead loader from formatted file
# 
# CHANGES
# 50602-1640 - First version created by Joe Johnson
# 51128-1108 - Removed PHP global vars requirement
# 60113-1603 - Fixed a few bugs in Excel import
# 60421-1624 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60616-1240 - added listID override
# 60616-1604 - added gmt lookup for each lead
# 60619-1651 - Added variable filtering to eliminate SQL injection attack threat
# 60822-1121 - fixed for nonwritable directories
# 60906-1100 - added filter of non-digits in alt_phone field
# 61110-1222 - added new USA-Canada DST scheme and Brazil DST scheme
# 61128-1149 - added postal code GMT lookup and duplicate check options
# 70417-1059 - Fixed default phone_code bug
# 70510-1518 - Added campaign and system duplicate check and phonecode override
# 80428-0417 - UTF8 changes
# 80514-1030 - removed filesize limit and raised number of errors to be displayed
#
# make sure vicidial_list exists and that your file follows the formatting correctly. This page does not dedupe or do any other lead filtering actions yet at this time.


### links used for testing
#$link=mysql_connect("10.10.10.15", "cron", "1234");
#mysql_select_db("asterisk");
#$WeBServeRRooT = '/home/www/htdocs';


echo "<TABLE align=center><TR><TD>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>LOAD NEW LEADS</font><form action=$PHP_SELF method=POST><br><br>\n";


$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
$leadfile=$_FILES["leadfile"];
	$LF_orig = $_FILES['leadfile']['name'];
	$LF_path = $_FILES['leadfile']['tmp_name'];
if (isset($_GET["submit_file"]))				{$submit_file=$_GET["submit_file"];}
	elseif (isset($_POST["submit_file"]))		{$submit_file=$_POST["submit_file"];}
if (isset($_GET["leadfile_name"]))				{$leadfile_name=$_GET["leadfile_name"];}
	elseif (isset($_POST["leadfile_name"]))		{$leadfile_name=$_POST["leadfile_name"];}
if (isset($_FILES["leadfile"]))				{$leadfile_name=$_FILES["leadfile"]['name'];}
if (isset($_GET["file_layout"]))				{$file_layout=$_GET["file_layout"];}
	elseif (isset($_POST["file_layout"]))		{$file_layout=$_POST["file_layout"];}
if (isset($_GET["OK_to_process"]))				{$OK_to_process=$_GET["OK_to_process"];}
	elseif (isset($_POST["OK_to_process"]))		{$OK_to_process=$_POST["OK_to_process"];}
if (isset($_GET["vendor_lead_code_field"]))				{$vendor_lead_code_field=$_GET["vendor_lead_code_field"];}
	elseif (isset($_POST["vendor_lead_code_field"]))		{$vendor_lead_code_field=$_POST["vendor_lead_code_field"];}
if (isset($_GET["source_id_field"]))				{$source_id_field=$_GET["source_id_field"];}
	elseif (isset($_POST["source_id_field"]))		{$source_id_field=$_POST["source_id_field"];}
if (isset($_GET["list_id_field"]))				{$list_id_field=$_GET["list_id_field"];}
	elseif (isset($_POST["list_id_field"]))		{$list_id_field=$_POST["list_id_field"];}
if (isset($_GET["phone_code_field"]))				{$phone_code_field=$_GET["phone_code_field"];}
	elseif (isset($_POST["phone_code_field"]))		{$phone_code_field=$_POST["phone_code_field"];}
if (isset($_GET["phone_number_field"]))				{$phone_number_field=$_GET["phone_number_field"];}
	elseif (isset($_POST["phone_number_field"]))		{$phone_number_field=$_POST["phone_number_field"];}
if (isset($_GET["title_field"]))				{$title_field=$_GET["title_field"];}
	elseif (isset($_POST["title_field"]))		{$title_field=$_POST["title_field"];}
if (isset($_GET["first_name_field"]))				{$first_name_field=$_GET["first_name_field"];}
	elseif (isset($_POST["first_name_field"]))		{$first_name_field=$_POST["first_name_field"];}
if (isset($_GET["middle_initial_field"]))				{$middle_initial_field=$_GET["middle_initial_field"];}
	elseif (isset($_POST["middle_initial_field"]))		{$middle_initial_field=$_POST["middle_initial_field"];}
if (isset($_GET["last_name_field"]))				{$last_name_field=$_GET["last_name_field"];}
	elseif (isset($_POST["last_name_field"]))		{$last_name_field=$_POST["last_name_field"];}
if (isset($_GET["address1_field"]))				{$address1_field=$_GET["address1_field"];}
	elseif (isset($_POST["address1_field"]))		{$address1_field=$_POST["address1_field"];}
if (isset($_GET["address2_field"]))				{$address2_field=$_GET["address2_field"];}
	elseif (isset($_POST["address2_field"]))		{$address2_field=$_POST["address2_field"];}
if (isset($_GET["address3_field"]))				{$address3_field=$_GET["address3_field"];}
	elseif (isset($_POST["address3_field"]))		{$address3_field=$_POST["address3_field"];}
if (isset($_GET["city_field"]))				{$city_field=$_GET["city_field"];}
	elseif (isset($_POST["city_field"]))		{$city_field=$_POST["city_field"];}
if (isset($_GET["state_field"]))				{$state_field=$_GET["state_field"];}
	elseif (isset($_POST["state_field"]))		{$state_field=$_POST["state_field"];}
if (isset($_GET["province_field"]))				{$province_field=$_GET["province_field"];}
	elseif (isset($_POST["province_field"]))		{$province_field=$_POST["province_field"];}
if (isset($_GET["postal_code_field"]))				{$postal_code_field=$_GET["postal_code_field"];}
	elseif (isset($_POST["postal_code_field"]))		{$postal_code_field=$_POST["postal_code_field"];}
if (isset($_GET["country_code_field"]))				{$country_code_field=$_GET["country_code_field"];}
	elseif (isset($_POST["country_code_field"]))		{$country_code_field=$_POST["country_code_field"];}
if (isset($_GET["gender_field"]))				{$gender_field=$_GET["gender_field"];}
	elseif (isset($_POST["gender_field"]))		{$gender_field=$_POST["gender_field"];}
if (isset($_GET["date_of_birth_field"]))				{$date_of_birth_field=$_GET["date_of_birth_field"];}
	elseif (isset($_POST["date_of_birth_field"]))		{$date_of_birth_field=$_POST["date_of_birth_field"];}
if (isset($_GET["alt_phone_field"]))				{$alt_phone_field=$_GET["alt_phone_field"];}
	elseif (isset($_POST["alt_phone_field"]))		{$alt_phone_field=$_POST["alt_phone_field"];}
if (isset($_GET["email_field"]))				{$email_field=$_GET["email_field"];}
	elseif (isset($_POST["email_field"]))		{$email_field=$_POST["email_field"];}
if (isset($_GET["security_phrase_field"]))				{$security_phrase_field=$_GET["security_phrase_field"];}
	elseif (isset($_POST["security_phrase_field"]))		{$security_phrase_field=$_POST["security_phrase_field"];}
if (isset($_GET["comments_field"]))				{$comments_field=$_GET["comments_field"];}
	elseif (isset($_POST["comments_field"]))		{$comments_field=$_POST["comments_field"];}
if (isset($_GET["list_id_override"]))				{$list_id_override=$_GET["list_id_override"];}
	elseif (isset($_POST["list_id_override"]))		{$list_id_override=$_POST["list_id_override"];}
	$list_id_override = (preg_replace("/\D/","",$list_id_override));
if (isset($_GET["lead_file"]))					{$lead_file=$_GET["lead_file"];}
	elseif (isset($_POST["lead_file"]))			{$lead_file=$_POST["lead_file"];}
if (isset($_GET["dupcheck"]))				{$dupcheck=$_GET["dupcheck"];}
	elseif (isset($_POST["dupcheck"]))		{$dupcheck=$_POST["dupcheck"];}
if (isset($_GET["postalgmt"]))				{$postalgmt=$_GET["postalgmt"];}
	elseif (isset($_POST["postalgmt"]))		{$postalgmt=$_POST["postalgmt"];}
if (isset($_GET["phone_code_override"]))			{$phone_code_override=$_GET["phone_code_override"];}
	elseif (isset($_POST["phone_code_override"]))	{$phone_code_override=$_POST["phone_code_override"];}
	$phone_code_override = (preg_replace("/\D/","",$phone_code_override));

# $country_field=$_GET["country_field"];					if (!$country_field) {$country_field=$_POST["country_field"];}


#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
{
$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
$list_id_override = ereg_replace("[^0-9]","",$list_id_override);
}

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_datetime = $STARTtime;

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7;";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0) {$fp = fopen ("./project_auth_entries.txt", "a");}
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"OSDial-LEAD-LOADER\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{
	header ("Content-type: text/html; charset=utf-8");
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0

	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT load_leads from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGload_leads				=$row[0];

		if ($LOGload_leads < 1)
			{
			echo "You do not have permissions to load leads\n";
			exit;
			}
		if ($WeBRooTWritablE > 0) 
			{
			fwrite ($fp, "LIST_LOAD|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0) 
			{
			fwrite ($fp, "LIST_LOAD|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}


$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443",$server_port)) {$HTTPprotocol = 'https://';}
  else {$HTTPprotocol = 'http://';}
$admDIR = "$HTTPprotocol$server_name$script_name";
$admDIR = eregi_replace('new_listloader_superL.php','',$admDIR);
//$admSCR = 'admin.php';          admin.php is already in admDIR
$NWB = " &nbsp; <a href=\"javascript:openNewWindow('$admDIR$admSCR?ADD=99999";
$NWE = "')\"><IMG SRC=\"help.gif\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"HELP\" ALIGN=TOP></A>";
$secX = date("U");
$hour = date("H");
$min = date("i");
$sec = date("s");
$mon = date("m");
$mday = date("d");
$year = date("Y");
$isdst = date("I");
$Shour = date("H");
$Smin = date("i");
$Ssec = date("s");
$Smon = date("m");
$Smday = date("d");
$Syear = date("Y");
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$inSD = $pulldate0;
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

	### Grab Server GMT value from the database
	$stmt="SELECT local_gmt FROM servers where server_ip = '$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$gmt_recs = mysql_num_rows($rslt);
	if ($gmt_recs > 0)
		{
		$row=mysql_fetch_row($rslt);
		$DBSERVER_GMT		=		"$row[0]";
		if (strlen($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
		if ($isdst) {$SERVER_GMT++;} 
		}
	else
		{
		$SERVER_GMT = date("O");
		$SERVER_GMT = eregi_replace("\+","",$SERVER_GMT);
		$SERVER_GMT = ($SERVER_GMT + 0);
		$SERVER_GMT = ($SERVER_GMT / 100);
		}

	$LOCAL_GMT_OFF = $SERVER_GMT;
	$LOCAL_GMT_OFF_STD = $SERVER_GMT;

#if ($DB) {print "SEED TIME  $secX      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n";}


echo "<!-- VERSION: $version     BUILD: $build -->\n";
echo "<!-- SEED TIME  $secX:   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF  DST: $isdst -->\n";

function macfontfix($fontsize) {
	$browser = getenv("HTTP_USER_AGENT");
	$pctype = explode("(", $browser);
	if (ereg("Mac",$pctype[1])) {
		/* Browser is a Mac.  If not Netscape 6, raise fonts */
		
		$blownbrowser = explode('/', $browser);
		$ver = explode(' ', $blownbrowser[1]);
		$ver = $ver[0];
		if ($ver >= 5.0) {
			return $fontsize; 
		} else { 
			return ($fontsize+2); 
		}
	} else {
		return $fontsize;	/* Browser is not a Mac - don't touch fonts */ 
	}
}

echo "<style type=\"text/css\">\n
<!--\n
.title {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(18)."pt}\n
.standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt}\n
.small_standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(8)."pt}\n
.tiny_standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(6)."pt}\n
.standard_bold {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold}\n
.standard_header {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(14)."pt; font-weight: bold}\n
.standard_bold_highlight {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold; color: white; BACKGROUND-COLOR: black}\n
.standard_bold_blue_highlight {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; BACKGROUND-COLOR: blue}\n
A.employee_standard {  font-family: garamond, sans-serif; font-size: ".macfontfix(10)."pt; font-style: normal; font-variant: normal; font-weight: bold; text-decoration: none}\n
.employee_standard {  font-family: garamond, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold}\n
.employee_title {  font-family: Garamond, sans-serif; font-size: ".macfontfix(14)."pt; font-weight: bold}\n
\\\\-->\n
</style>\n";

?>




<form action=<?=$PHP_SELF ?> method=post onSubmit="ParseFileName()" enctype="multipart/form-data">
<input type=hidden name='leadfile_name' value="<?=$leadfile_name ?>">
<? if ($file_layout!="custom") { ?>
<table align=center width="700" border=0 cellpadding=5 cellspacing=0 bgcolor=#C1D6DF>
  <tr>
	<td align=right width="35%"><B><font face="arial, helvetica" size=2>Load leads from this file:</font></B></td>
	<td align=left width="65%"><input type=file name="leadfile" value="<?=$leadfile ?>"> <? echo "$NWB#osdial_list_loader$NWE"; ?></td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>List ID Override: </font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><input type=text value="<?=$list_id_override ?>" name='list_id_override' size=10 maxlength=8> (numbers only or leave blank for values in the file)</td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Phone Code Override: </font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><input type=text value="<?=$phone_code_override ?>" name='phone_code_override' size=8 maxlength=6> (numbers only or leave blank for values in the file)</td>
  </tr>
  <tr>
	<td align=right><B><font face="arial, helvetica" size=2>File layout to use:</font></B></td>
	<td align=left><font face="arial, helvetica" size=2><input type=radio name="file_layout" value="standard" checked>Standard OSDial&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name="file_layout" value="custom">Custom layout</td>
  </tr>
    <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Lead Duplicate Check: </font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><select size=1 name=dupcheck>
	<option selected value="NONE">NO DUPLICATE CHECK</option>
	<option value="DUPLIST">CHECK FOR DUPLICATES BY PHONE IN LIST ID</option>
	<option value="DUPCAMP">CHECK FOR DUPLICATES BY PHONE IN ALL CAMPAIGN LISTS</option>
	<option value="DUPSYS">CHECK FOR DUPLICATES BY PHONE IN ENTIRE SYSTEM</option>
	</select></td>
  </tr>
  <tr>
	<td align=right width="25%"><font face="arial, helvetica" size=2>Lead Time Zone Lookup: </font></td>
	<td align=left width="75%"><font face="arial, helvetica" size=1><select size=1 name=postalgmt><option selected value="AREA">COUNTRY CODE AND AREA CODE ONLY</option><option value="POSTAL">POSTAL CODE FIRST</option></select></td>
  </tr>
<tr>
	<td align=center colspan=2><input type=submit value="SUBMIT" name='submit_file'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onClick="javascript:document.location='new_listloader_superL.php'" value="START OVER" name='reload_page'></td>
  </tr>
</table>
<? } ?>

<?

	if ($OK_to_process) {
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true;document.forms[0].list_id_override.disabled=true;document.forms[0].phone_code_override.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
		flush();
		$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';

		if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) {
			# copy($leadfile, "./vicidial_temp_file.txt");
			$file=fopen("$lead_file", "r");
			if ($WeBRooTWritablE > 0)
				{
				$stmt_file=fopen("listloader_stmts.txt", "w");
				}
			$buffer=fgets($file, 4096);
			$tab_count=substr_count($buffer, "\t");
			$pipe_count=substr_count($buffer, "|");

			if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
			$field_check=explode($delimiter, $buffer);

			if (count($field_check)>=5) {
				flush();
				$file=fopen("$lead_file", "r");
				print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Processing $delim_name-delimited file...\n";

			if (strlen($list_id_override)>0) 
				{
				print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
				}

			if (strlen($phone_code_override)>0) 
				{
				print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
				}

				while (!feof($file)) {
					$record++;
					$buffer=rtrim(fgets($file, 4096));
					$buffer=stripslashes($buffer);

					if (strlen($buffer)>0) {
						$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));

						$pulldate=date("Y-m-d H:i:s");
						$entry_date =			"$pulldate";
						$modify_date =			"";
						$status =				"NEW";
						$user ="";
						$vendor_lead_code =		$row[$vendor_lead_code_field];
						$source_code =			$row[$source_id_field];
						$source_id=$source_code;
						$list_id =				$row[$list_id_field];
						$gmt_offset =			'0';
						$called_since_last_reset='N';
						$phone_code =			eregi_replace("[^0-9]", "", $row[$phone_code_field]);
						$phone_number =			eregi_replace("[^0-9]", "", $row[$phone_number_field]);
							$USarea = 			substr($phone_number, 0, 3);
						$title =				$row[$title_field];
						$first_name =			$row[$first_name_field];
						$middle_initial =		$row[$middle_initial_field];
						$last_name =			$row[$last_name_field];
						$address1 =				$row[$address1_field];
						$address2 =				$row[$address2_field];
						$address3 =				$row[$address3_field];
						$city =$row[$city_field];
						$state =				$row[$state_field];
						$province =				$row[$province_field];
						$postal_code =			$row[$postal_code_field];
						$country_code =			$row[$country_code_field];
						$gender =				$row[$gender_field];
						$date_of_birth =		$row[$date_of_birth_field];
						$alt_phone =			eregi_replace("[^0-9]", "", $row[$alt_phone_field]);
						$email =				$row[$email_field];
						$security_phrase =		$row[$security_phrase_field];
						$comments =				trim($row[$comments_field]);

						if (strlen($list_id_override)>0) 
							{
						#	print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
							$list_id = $list_id_override;
							}
						if (strlen($phone_code_override)>0) 
							{
							$phone_code = $phone_code_override;
							}

						##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
						if (eregi("DUPCAMP",$dupcheck))
							{
								$dup_lead=0;
								$dup_lists='';
							$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
							$rslt=mysql_query($stmt, $link);
							$ci_recs = mysql_num_rows($rslt);
							if ($ci_recs > 0)
								{
								$row=mysql_fetch_row($rslt);
								$dup_camp =			$row[0];

								$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
								$rslt=mysql_query($stmt, $link);
								$li_recs = mysql_num_rows($rslt);
								if ($li_recs > 0)
									{
									$L=0;
									while ($li_recs > $L)
										{
										$row=mysql_fetch_row($rslt);
										$dup_lists .=	"'$row[0]',";
										$L++;
										}
									$dup_lists = eregi_replace(",$",'',$dup_lists);

									$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
									$rslt=mysql_query($stmt, $link);
									$pc_recs = mysql_num_rows($rslt);
									if ($pc_recs > 0)
										{
										$dup_lead=1;
										$row=mysql_fetch_row($rslt);
										$dup_lead_list =	$row[0];
										}
									if ($dup_lead < 1)
										{
										if (eregi("$phone_number$US$list_id",$phone_list))
											{$dup_lead++; $dup++;}
										}
									}
								}
							}

						##### Check for duplicate phone numbers in vicidial_list table entire database #####
						if (eregi("DUPSYS",$dupcheck))
							{
							$dup_lead=0;
							$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
							$rslt=mysql_query($stmt, $link);
							$pc_recs = mysql_num_rows($rslt);
							if ($pc_recs > 0)
								{
								$dup_lead=1;
								$row=mysql_fetch_row($rslt);
								$dup_lead_list =	$row[0];
								}
							if ($dup_lead < 1)
								{
								if (eregi("$phone_number$US$list_id",$phone_list))
									{$dup_lead++; $dup++;}
								}
							}

						##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
						if (eregi("DUPLIST",$dupcheck))
							{
							$dup_lead=0;
							$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
							$rslt=mysql_query($stmt, $link);
							$pc_recs = mysql_num_rows($rslt);
							if ($pc_recs > 0)
								{
								$row=mysql_fetch_row($rslt);
								$dup_lead =			$row[0];
								$dup_lead_list =	$list_id;
								}
							if ($dup_lead < 1)
								{
								if (eregi("$phone_number$US$list_id",$phone_list))
									{$dup_lead++; $dup++;}
								}
							}

						if ( (strlen($phone_number)>6) and ($dup_lead<1) )
							{
							if (strlen($phone_code)<1) {$phone_code = '1';}

							$US='_';
							$phone_list .= "$phone_number$US$list_id|";

							$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);

							if ($multi_insert_counter > 8) {
								### insert good deal into pending_transactions table ###
								$stmtZ = "INSERT INTO vicidial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0);";
								$rslt=mysql_query($stmtZ, $link);
								if ($WeBRooTWritablE > 0) 
									{fwrite($stmt_file, $stmtZ."\r\n");}
								$multistmt='';
								$multi_insert_counter=0;

							} else {
								$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0),";
								$multi_insert_counter++;
							}

							$good++;
						} else {
							if ($bad < 1000000) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead  $dup_lead_list</font><b>\n";}
							$bad++;
						}
						$total++;
						if ($total%100==0) {
							print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
							usleep(1000);
							flush();
						}
					}
				}
				if ($multi_insert_counter!=0) {
					$stmtZ = "INSERT INTO vicidial_list values".substr($multistmt, 0, -1).";";
					mysql_query($stmtZ, $link);
					if ($WeBRooTWritablE > 0) 
						{fwrite($stmt_file, $stmtZ."\r\n");}
				}

				print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";

			} else {
				print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: The file does not have the required number of fields to process it.</B></font></center>";
			}
		} else if (!eregi(".csv", $leadfile_name)) {
			# copy($leadfile, "./vicidial_temp_file.xls");
			$file=fopen("$lead_file", "r");

			print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Processing Excel file... \n";
			if (strlen($list_id_override)>0) 
			{
			print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>\n";
			}
			if (strlen($phone_code_override)>0) 
			{
			print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>\n";
			}
		# print "|$WeBServeRRooT/vicidial/listloader_super.pl $vendor_lead_code_field,$source_id_field,$list_id_field,$phone_code_field,$phone_number_field,$title_field,$first_name_field,$middle_initial_field,$last_name_field,$address1_field,$address2_field,$address3_field,$city_field,$state_field,$province_field,$postal_code_field,$country_code_field,$gender_field,$date_of_birth_field,$alt_phone_field,$email_field,$security_phrase_field,$comments_field, --forcelistid=$list_id_override --lead_file=$lead_file|";
			$dupcheckCLI=''; $postalgmtCLI='';
			if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
			if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
			if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
			if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
			passthru("$WeBServeRRooT/vicidial/listloader_super.pl $vendor_lead_code_field,$source_id_field,$list_id_field,$phone_code_field,$phone_number_field,$title_field,$first_name_field,$middle_initial_field,$last_name_field,$address1_field,$address2_field,$address3_field,$city_field,$state_field,$province_field,$postal_code_field,$country_code_field,$gender_field,$date_of_birth_field,$alt_phone_field,$email_field,$security_phrase_field,$comments_field, --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");
		} else {
			# copy($leadfile, "./vicidial_temp_file.csv");
			$file=fopen("$lead_file", "r");

			if ($WeBRooTWritablE > 0)
				{$stmt_file=fopen("$WeBServeRRooT/vicidial/listloader_stmts.txt", "w");}
			
			print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Processing CSV file... \n";
			if (strlen($list_id_override)>0) 
				{
				print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
				}

			if (strlen($phone_code_override)>0) 
				{
				print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
				}

			while($row=fgetcsv($file, 1000, ",")) {

				$pulldate=date("Y-m-d H:i:s");
				$entry_date =			"$pulldate";
				$modify_date =			"";
				$status =				"NEW";
				$user ="";
				$vendor_lead_code =		$row[$vendor_lead_code_field];
				$source_code =			$row[$source_id_field];
				$source_id=$source_code;
				$list_id =				$row[$list_id_field];
				$gmt_offset =			'0';
				$called_since_last_reset='N';
				$phone_code =			eregi_replace("[^0-9]", "", $row[$phone_code_field]);
				$phone_number =			eregi_replace("[^0-9]", "", $row[$phone_number_field]);
					$USarea = 			substr($phone_number, 0, 3);
				$title =				$row[$title_field];
				$first_name =			$row[$first_name_field];
				$middle_initial =		$row[$middle_initial_field];
				$last_name =			$row[$last_name_field];
				$address1 =				$row[$address1_field];
				$address2 =				$row[$address2_field];
				$address3 =				$row[$address3_field];
				$city =$row[$city_field];
				$state =				$row[$state_field];
				$province =				$row[$province_field];
				$postal_code =			$row[$postal_code_field];
				$country_code =			$row[$country_code_field];
				$gender =				$row[$gender_field];
				$date_of_birth =		$row[$date_of_birth_field];
				$alt_phone =			eregi_replace("[^0-9]", "", $row[$alt_phone_field]);
				$email =				$row[$email_field];
				$security_phrase =		$row[$security_phrase_field];
				$comments =				trim($row[$comments_field]);

					if (strlen($list_id_override)>0) 
						{
						$list_id = $list_id_override;
						}
					if (strlen($phone_code_override)>0) 
						{
						$phone_code = $phone_code_override;
						}

					##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
					if (eregi("DUPCAMP",$dupcheck))
						{
							$dup_lead=0;
							$dup_lists='';
						$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$ci_recs = mysql_num_rows($rslt);
						if ($ci_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_camp =			$row[0];

							$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
							$rslt=mysql_query($stmt, $link);
							$li_recs = mysql_num_rows($rslt);
							if ($li_recs > 0)
								{
								$L=0;
								while ($li_recs > $L)
									{
									$row=mysql_fetch_row($rslt);
									$dup_lists .=	"'$row[0]',";
									$L++;
									}
								$dup_lists = eregi_replace(",$",'',$dup_lists);

								$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPSYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
					if (eregi("DUPLIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					if ( (strlen($phone_number)>6) and ($dup_lead<1) )
						{
						if (strlen($phone_code)<1) {$phone_code = '1';}

						$US='_';
						$phone_list .= "$phone_number$US$list_id|";

						$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);


					if ($multi_insert_counter > 8) {
						### insert good deal into pending_transactions table ###
						$stmtZ = "INSERT INTO vicidial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0);";
						$rslt=mysql_query($stmtZ, $link);
						if ($WeBRooTWritablE > 0) 
							{fwrite($stmt_file, $stmtZ."\r\n");}
						$multistmt='';
						$multi_insert_counter=0;

					} else {
						$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0),";
						$multi_insert_counter++;
					}

					$good++;
				} else {
					if ($bad < 1000000) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead</font><b>\n";}
					$bad++;
				}
				$total++;
				if ($total%100==0) {
					print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
					usleep(1000);
					flush();
				}
			}
			if ($multi_insert_counter!=0) {
				$stmtZ = "INSERT INTO vicidial_list values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) 
					{fwrite($stmt_file, $stmtZ."\r\n");}
			}
			print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";
		}
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
	} 

if ($leadfile) {
		$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
		if ($file_layout=="standard") {

	print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
	flush();

	if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) {

		if ($WeBRooTWritablE > 0)
			{
			copy($LF_path, "$WeBServeRRooT/vicidial/vicidial_temp_file.txt");
			$lead_file = "./vicidial_temp_file.txt";
			}
		else
			{
			copy($LF_path, "/tmp/vicidial_temp_file.txt");
			$lead_file = "/tmp/vicidial_temp_file.txt";
			}
		$file=fopen("$lead_file", "r");
		if ($WeBRooTWritablE > 0)
			{$stmt_file=fopen("$WeBServeRRooT/vicidial/listloader_stmts.txt", "w");}

		$buffer=fgets($file, 4096);
		$tab_count=substr_count($buffer, "\t");
		$pipe_count=substr_count($buffer, "|");

		if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
		$field_check=explode($delimiter, $buffer);

		if (count($field_check)>=5) {
			flush();
			$file=fopen("$lead_file", "r");
			$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
			print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Processing $delim_name-delimited file... ($tab_count|$pipe_count)\n";
			if (strlen($list_id_override)>0) 
			{
			print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
			}
			if (strlen($phone_code_override)>0) 
			{
			print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>\n";
			}
		while (!feof($file)) {
				$record++;
				$buffer=rtrim(fgets($file, 4096));
				$buffer=stripslashes($buffer);

				if (strlen($buffer)>0) {
					$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));

					$pulldate=date("Y-m-d H:i:s");
					$entry_date =			"$pulldate";
					$modify_date =			"";
					$status =				"NEW";
					$user ="";
					$vendor_lead_code =		$row[0];
					$source_code =			$row[1];
					$source_id=$source_code;
					$list_id =				$row[2];
					$gmt_offset =			'0';
					$called_since_last_reset='N';
					$phone_code =			eregi_replace("[^0-9]", "", $row[3]);
					$phone_number =			eregi_replace("[^0-9]", "", $row[4]);
						$USarea = 			substr($phone_number, 0, 3);
					$title =				$row[5];
					$first_name =			$row[6];
					$middle_initial =		$row[7];
					$last_name =			$row[8];
					$address1 =				$row[9];
					$address2 =				$row[10];
					$address3 =				$row[11];
					$city =$row[12];
					$state =				$row[13];
					$province =				$row[14];
					$postal_code =			$row[15];
					$country_code =			$row[16];
					$gender =				$row[17];
					$date_of_birth =		$row[18];
					$alt_phone =			eregi_replace("[^0-9]", "", $row[19]);
					$email =				$row[20];
					$security_phrase =		$row[21];
					$comments =				trim($row[22]);

					if (strlen($list_id_override)>0) 
						{
						$list_id = $list_id_override;
						}
					if (strlen($phone_code_override)>0) 
						{
						$phone_code = $phone_code_override;
						}

					##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
					if (eregi("DUPCAMP",$dupcheck))
						{
							$dup_lead=0;
							$dup_lists='';
						$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$ci_recs = mysql_num_rows($rslt);
						if ($ci_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_camp =			$row[0];

							$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
							$rslt=mysql_query($stmt, $link);
							$li_recs = mysql_num_rows($rslt);
							if ($li_recs > 0)
								{
								$L=0;
								while ($li_recs > $L)
									{
									$row=mysql_fetch_row($rslt);
									$dup_lists .=	"'$row[0]',";
									$L++;
									}
								$dup_lists = eregi_replace(",$",'',$dup_lists);

								$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPSYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
					if (eregi("DUPLIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					if ( (strlen($phone_number)>6) and ($dup_lead<1) )
						{
						if (strlen($phone_code)<1) {$phone_code = '1';}

							$US='_';
							$phone_list .= "$phone_number$US$list_id|";

							$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);


						if ($multi_insert_counter > 8) {
							### insert good deal into pending_transactions table ###
							$stmtZ = "INSERT INTO vicidial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0);";
							$rslt=mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0) 
								{fwrite($stmt_file, $stmtZ."\r\n");}
							$multistmt='';
							$multi_insert_counter=0;

						} else {
							$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0),";
							$multi_insert_counter++;
						}

						$good++;
					} else {
						if ($bad < 1000000) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead</font><b>\n";}
						$bad++;
					}
					$total++;
					if ($total%100==0) {
						print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
						usleep(1000);
						flush();
					}
				}
			}
			if ($multi_insert_counter!=0) {
				$stmtZ = "INSERT INTO vicidial_list values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) 
					{fwrite($stmt_file, $stmtZ."\r\n");}
			}

			print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";

		} else {
			print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: The file does not have the required number of fields to process it.</B></font></center>";
		}
	} else if (!eregi(".csv", $leadfile_name)) 
		{
		if ($WeBRooTWritablE > 0)
			{
			copy($LF_path, "$WeBServeRRooT/vicidial/vicidial_temp_file.xls");
			$lead_file = "$WeBServeRRooT/vicidial/vicidial_temp_file.xls";
			}
		else
			{
			copy($LF_path, "/tmp/vicidial_temp_file.xls");
			$lead_file = "/tmp/vicidial_temp_file.xls";
			}
		$file=fopen("$lead_file", "r");

	#	echo "|$WeBServeRRooT/vicidial/listloader.pl --forcelistid=$list_id_override --lead-file=$lead_file|";
		$dupcheckCLI=''; $postalgmtCLI='';
		if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
		if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
		if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
		if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
		passthru("$WeBServeRRooT/vicidial/listloader.pl --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file  $postalgmtCLI $dupcheckCLI");
	
		}
		else 
		{
		if ($WeBRooTWritablE > 0)
			{
			copy($LF_path, "$WeBServeRRooT/vicidial/vicidial_temp_file.csv");
			$lead_file = "$WeBServeRRooT/vicidial/vicidial_temp_file.csv";
			}
		else
			{
			copy($LF_path, "/tmp/vicidial_temp_file.csv");
			$lead_file = "/tmp/vicidial_temp_file.csv";
			}
		$file=fopen("$lead_file", "r");
		if ($WeBRooTWritablE > 0)
			{$stmt_file=fopen("$WeBServeRRooT/vicidial/listloader_stmts.txt", "w");}
		
		print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Processing CSV file... \n";

		if (strlen($list_id_override)>0) 
			{
			print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
			}
		if (strlen($phone_code_override)>0) 
			{
			print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
			}

		while($row=fgetcsv($file, 1000, ",")) {
				$pulldate=date("Y-m-d H:i:s");
				$entry_date =			"$pulldate";
				$modify_date =			"";
				$status =				"NEW";
				$user ="";
				$vendor_lead_code =		$row[0];
				$source_code =			$row[1];
				$source_id=$source_code;
				$list_id =				$row[2];
				$gmt_offset =			'0';
				$called_since_last_reset='N';
				$phone_code =			eregi_replace("[^0-9]", "", $row[3]);
				$phone_number =			eregi_replace("[^0-9]", "", $row[4]);
					$USarea = 			substr($phone_number, 0, 3);
				$title =				$row[5];
				$first_name =			$row[6];
				$middle_initial =		$row[7];
				$last_name =			$row[8];
				$address1 =				$row[9];
				$address2 =				$row[10];
				$address3 =				$row[11];
				$city =$row[12];
				$state =				$row[13];
				$province =				$row[14];
				$postal_code =			$row[15];
				$country_code =			$row[16];
				$gender =				$row[17];
				$date_of_birth =		$row[18];
				$alt_phone =			eregi_replace("[^0-9]", "", $row[19]);
				$email =				$row[20];
				$security_phrase =		$row[21];
				$comments =				trim($row[22]);

					if (strlen($list_id_override)>0) 
						{
						$list_id = $list_id_override;
						}
					if (strlen($phone_code_override)>0) 
						{
						$phone_code = $phone_code_override;
						}

					##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
					if (eregi("DUPCAMP",$dupcheck))
						{
							$dup_lead=0;
							$dup_lists='';
						$stmt="select campaign_id from vicidial_lists where list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$ci_recs = mysql_num_rows($rslt);
						if ($ci_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_camp =			$row[0];

							$stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";
							$rslt=mysql_query($stmt, $link);
							$li_recs = mysql_num_rows($rslt);
							if ($li_recs > 0)
								{
								$L=0;
								while ($li_recs > $L)
									{
									$row=mysql_fetch_row($rslt);
									$dup_lists .=	"'$row[0]',";
									$L++;
									}
								$dup_lists = eregi_replace(",$",'',$dup_lists);

								$stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table entire database #####
					if (eregi("DUPSYS",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select list_id from vicidial_list where phone_number='$phone_number';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
					if (eregi("DUPLIST",$dupcheck))
						{
						$dup_lead=0;
						$stmt="select count(*) from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0)
							{
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
							}
						if ($dup_lead < 1)
							{
							if (eregi("$phone_number$US$list_id",$phone_list))
								{$dup_lead++; $dup++;}
							}
						}

					if ( (strlen($phone_number)>6) and ($dup_lead<1) )
						{
						if (strlen($phone_code)<1) {$phone_code = '1';}

						$US='_';
						$phone_list .= "$phone_number$US$list_id|";

						$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);


					if ($multi_insert_counter > 8) {
						### insert good deal into pending_transactions table ###
						$stmtZ = "INSERT INTO vicidial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0);";
						$rslt=mysql_query($stmtZ, $link);
						if ($WeBRooTWritablE > 0) 
							{fwrite($stmt_file, $stmtZ."\r\n");}
						$multistmt='';
						$multi_insert_counter=0;

					} else {
						$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0),";
						$multi_insert_counter++;
					}

					$good++;
				} else {
					if ($bad < 1000000) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead</font><b>\n";}
					$bad++;
				}
				$total++;
				if ($total%100==0) {
					print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
					usleep(1000);
					flush();
				}
			}
			if ($multi_insert_counter!=0) {
				$stmtZ = "INSERT INTO vicidial_list values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) 
					{fwrite($stmt_file, $stmtZ."\r\n");}
			}

			print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";

		}
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";

		} else {
			print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script><HR>";
			flush();
			print "<table border=0 cellpadding=3 cellspacing=0 width=700 align=center>\r\n";
			print "  <tr bgcolor='#330099'>\r\n";
			print "    <th align=right><font class='standard' color='white'>OSDial Column</font></th>\r\n";
			print "    <th><font class='standard' color='white'>File data</font></th>\r\n";
			print "  </tr>\r\n";

			$rslt=mysql_query("select vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments from vicidial_list limit 1", $link);
			

			if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) 
				{
				if ($WeBRooTWritablE > 0)
					{
					copy($LF_path, "$WeBServeRRooT/vicidial/vicidial_temp_file.txt");
					$lead_file = "$WeBServeRRooT/vicidial/vicidial_temp_file.txt";
					}
				else
					{
					copy($LF_path, "/tmp/vicidial_temp_file.txt");
					$lead_file = "/tmp/vicidial_temp_file.txt";
					}
				$file=fopen("$lead_file", "r");
				if ($WeBRooTWritablE > 0)
					{$stmt_file=fopen("$WeBServeRRooT/vicidial/listloader_stmts.txt", "w");}

				$buffer=fgets($file, 4096);
				$tab_count=substr_count($buffer, "\t");
				$pipe_count=substr_count($buffer, "|");

				if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
				$field_check=explode($delimiter, $buffer);
				flush();
				$file=fopen("$lead_file", "r");
				print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Processing $delim_name-delimited file...\n";

				if (strlen($list_id_override)>0) 
					{
					print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
					}
				if (strlen($phone_code_override)>0) 
					{
					print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
					}
				$buffer=rtrim(fgets($file, 4096));
				$buffer=stripslashes($buffer);
				$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));
				
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {

					if ( (mysql_field_name($rslt, $i)=="list_id" and $list_id_override!="") or (mysql_field_name($rslt, $i)=="phone_code" and $phone_code_override!="") ) {
						print "<!-- skipping " . mysql_field_name($rslt, $i) . " -->\n";
					} else {
						print "  <tr bgcolor=#D9E6FE>\r\n";
						print "    <td align=right><font class=standard>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </font></td>\r\n";
						print "    <td align=center><select name='".mysql_field_name($rslt, $i)."_field'>\r\n";
						print "     <option value='-1'>(none)</option>\r\n";
	
						for ($j=0; $j<count($row); $j++) {
							eregi_replace("\"", "", $row[$j]);
							print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
						}
	
						print "    </select></td>\r\n";
						print "  </tr>\r\n";
					}

				}
			} 
			else if (!eregi(".csv", $leadfile_name)) 
			{
				if ($WeBRooTWritablE > 0)
					{
					copy($LF_path, "$WeBServeRRooT/vicidial/vicidial_temp_file.xls");
					$lead_file = "$WeBServeRRooT/vicidial/vicidial_temp_file.xls";
					}
				else
					{
					copy($LF_path, "/tmp/vicidial_temp_file.xls");
					$lead_file = "/tmp/vicidial_temp_file.xls";
					}

			#	echo "|$WeBServeRRooT/vicidial/listloader_rowdisplay.pl --lead-file=$lead_file|";
				$dupcheckCLI=''; $postalgmtCLI='';
				if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
				if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
				if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
				if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
				passthru("$WeBServeRRooT/vicidial/listloader_rowdisplay.pl --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");
			} 
			else 
			{
				if ($WeBRooTWritablE > 0)
					{
					copy($LF_path, "$WeBServeRRooT/vicidial/vicidial_temp_file.csv");
					$lead_file = "$WeBServeRRooT/vicidial/vicidial_temp_file.csv";
					}
				else
					{
					copy($LF_path, "/tmp/vicidial_temp_file.csv");
					$lead_file = "/tmp/vicidial_temp_file.csv";
					}
				$file=fopen("$lead_file", "r");

				if ($WeBRooTWritablE > 0)
					{$stmt_file=fopen("$WeBServeRRooT/vicidial/listloader_stmts.txt", "w");}
				
				print "<center><font face='arial, helvetica' size=3 color='#009900'><B>Processing CSV file... \n";
				
				if (strlen($list_id_override)>0) 
					{
					print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
					}
				if (strlen($phone_code_override)>0) 
					{
					print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
					}

				$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
				$row=fgetcsv($file, 1000, ",");
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {
					if ( (mysql_field_name($rslt, $i)=="list_id" and $list_id_override!="") or (mysql_field_name($rslt, $i)=="phone_code" and $phone_code_override!="") ) {
						print "<!-- skipping " . mysql_field_name($rslt, $i) . " -->\n";
					} else {
						print "  <tr bgcolor=#D9E6FE>\r\n";
						print "    <td align=right><font class=standard>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </font></td>\r\n";
						print "    <td align=center><select name='".mysql_field_name($rslt, $i)."_field'>\r\n";
						print "     <option value='-1'>(none)</option>\r\n";

						for ($j=0; $j<count($row); $j++) {
							eregi_replace("\"", "", $row[$j]);
							print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
						}

						print "    </select></td>\r\n";
						print "  </tr>\r\n";
					}
				}
			}
			print "  <tr bgcolor='#330099'>\r\n";
			print "  <input type=hidden name=dupcheck value=\"$dupcheck\">\r\n";
			print "  <input type=hidden name=postalgmt value=\"$postalgmt\">\r\n";
			print "  <input type=hidden name=lead_file value=\"$lead_file\">\r\n";
			print "  <input type=hidden name=list_id_override value=\"$list_id_override\">\r\n";
			print "  <input type=hidden name=phone_code_override value=\"$phone_code_override\">\r\n";
			print "    <th colspan=2><input type=submit name='OK_to_process' value='OK TO PROCESS'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onClick=\"javascript:document.location='new_listloader_superL.php'\" value=\"START OVER\" name='reload_page'></th>\r\n";
			print "  </tr>\r\n";
			print "</table>\r\n";
	# }
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
	}
#} else if (filesize($leadfile)>8388608) {
#		print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: File exceeds the 8MB limit.</B></font></center>";
}
?>
</form>






<?





function lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code)
{
require("dbconnect.php");

$postalgmt_found=0;
if ( (eregi("POSTAL",$postalgmt)) && (strlen($postal_code)>4) )
	{
	if (preg_match('/^1$/', $phone_code))
		{
		$stmt="select * from vicidial_postal_codes where country_code='$phone_code' and postal_code LIKE \"$postal_code%\";";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[2];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[3];
			$dst_range =	$row[4];
			$PC_processed++;
			$postalgmt_found++;
			$post++;
			}
		}
	}
if ($postalgmt_found < 1)
	{
	$PC_processed=0;
	### UNITED STATES ###
	if ($phone_code =='1')
		{
		$stmt="select * from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### MEXICO ###
	if ($phone_code =='52')
		{
		$stmt="select * from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### AUSTRALIA ###
	if ($phone_code =='61')
		{
		$stmt="select * from vicidial_phone_codes where country_code='$phone_code' and state='$state';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### ALL OTHER COUNTRY CODES ###
	if (!$PC_processed)
		{
		$PC_processed++;
		$stmt="select * from vicidial_phone_codes where country_code='$phone_code';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	}

### Find out if DST to raise the gmt offset ###
$AC_GMT_diff = ($gmt_offset - $LOCAL_GMT_OFF_STD);
$AC_localtime = mktime(($Shour + $AC_GMT_diff), $Smin, $Ssec, $Smon, $Smday, $Syear);
	$hour = date("H",$AC_localtime);
	$min = date("i",$AC_localtime);
	$sec = date("s",$AC_localtime);
	$mon = date("m",$AC_localtime);
	$mday = date("d",$AC_localtime);
	$wday = date("w",$AC_localtime);
	$year = date("Y",$AC_localtime);
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

$AC_processed=0;
if ( (!$AC_processed) and ($dst_range == 'SSM-FSN') )
	{
	if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
	#**********************************************************************
	# SSM-FSN
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on Second Sunday March to First Sunday November at 2 am.
	#     INPUTS:
	#       mm              INTEGER       Month.
	#       dd              INTEGER       Day of the month.
	#       ns              INTEGER       Seconds into the day.
	#       dow             INTEGER       Day of week (0=Sunday, to 6=Saturday)
	#     OPTIONAL INPUT:
	#       timezone        INTEGER       hour difference UTC - local standard time
	#                                      (DEFAULT is blank)
	#                                     make calculations based on UTC time, 
	#                                     which means shift at 10:00 UTC in April
	#                                     and 9:00 UTC in October
	#     OUTPUT: 
	#                       INTEGER       1 = DST, 0 = not DST
	#
	# S  M  T  W  T  F  S
	# 1  2  3  4  5  6  7
	# 8  9 10 11 12 13 14
	#15 16 17 18 19 20 21
	#22 23 24 25 26 27 28
	#29 30 31
	# 
	# S  M  T  W  T  F  S
	#    1  2  3  4  5  6
	# 7  8  9 10 11 12 13
	#14 15 16 17 18 19 20
	#21 22 23 24 25 26 27
	#28 29 30 31
	# 
	#**********************************************************************

		$USACAN_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 11) {
		$USACAN_DST=0;   
		} elseif ($mm >= 4 and $mm <= 10) {
		$USACAN_DST=1;   
		} elseif ($mm == 3) {
		if ($dd > 13) {
			$USACAN_DST=1;   
		} elseif ($dd >= ($dow+8)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$USACAN_DST=0;   
			} else {
				$USACAN_DST=1;   
			}
			} else {
			if ($dow == 0 and $ns < 7200) {
				$USACAN_DST=0;   
			} else {
				$USACAN_DST=1;   
			}
			}
		} else {
			$USACAN_DST=0;   
		}
		} elseif ($mm == 11) {
		if ($dd > 7) {
			$USACAN_DST=0;   
		} elseif ($dd < ($dow+1)) {
			$USACAN_DST=1;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (7200+($timezone-1)*3600)) {
				$USACAN_DST=1;   
			} else {
				$USACAN_DST=0;   
			}
			} else { # local time calculations
			if ($ns < 7200) {
				$USACAN_DST=1;   
			} else {
				$USACAN_DST=0;   
			}
			}
		} else {
			$USACAN_DST=0;   
		}
		} # end of month checks
	if ($DBX) {print "     DST: $USACAN_DST\n";}
	if ($USACAN_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'FSA-LSO') )
	{
	if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
	#**********************************************************************
	# FSA-LSO
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Sunday in April and last Sunday in October at 2 am.
	#**********************************************************************
		
		$USA_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 4 || $mm > 10) {
		$USA_DST=0;
		} elseif ($mm >= 5 and $mm <= 9) {
		$USA_DST=1;
		} elseif ($mm == 4) {
		if ($dd > 7) {
			$USA_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$USA_DST=0;
			} else {
				$USA_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 7200) {
				$USA_DST=0;
			} else {
				$USA_DST=1;
			}
			}
		} else {
			$USA_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$USA_DST=1;
		} elseif ($dd < ($dow+25)) {
			$USA_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (7200+($timezone-1)*3600)) {
				$USA_DST=1;
			} else {
				$USA_DST=0;
			}
			} else { # local time calculations
			if ($ns < 7200) {
				$USA_DST=1;
			} else {
				$USA_DST=0;
			}
			}
		} else {
			$USA_DST=0;
		}
		} # end of month checks

	if ($DBX) {print "     DST: $USA_DST\n";}
	if ($USA_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'LSM-LSO') )
	{
	if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on last Sunday in March and last Sunday in October at 1 am.
	#**********************************************************************
		
		$GBR_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$GBR_DST=0;
		} elseif ($mm >= 4 and $mm <= 9) {
		$GBR_DST=1;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$GBR_DST=0;
		} elseif ($dd < ($dow+25)) {
			$GBR_DST=0;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$GBR_DST=0;
			} else {
				$GBR_DST=1;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$GBR_DST=0;
			} else {
				$GBR_DST=1;
			}
			}
		} else {
			$GBR_DST=1;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$GBR_DST=1;
		} elseif ($dd < ($dow+25)) {
			$GBR_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$GBR_DST=1;
			} else {
				$GBR_DST=0;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$GBR_DST=1;
			} else {
				$GBR_DST=0;
			}
			}
		} else {
			$GBR_DST=0;
		}
		} # end of month checks
		if ($DBX) {print "     DST: $GBR_DST\n";}
	if ($GBR_DST) {$gmt_offset++;}
	$AC_processed++;
	}
if ( (!$AC_processed) and ($dst_range == 'LSO-LSM') )
	{
	if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on last Sunday in October and last Sunday in March at 1 am.
	#**********************************************************************
		
		$AUS_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$AUS_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$AUS_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$AUS_DST=1;
		} elseif ($dd < ($dow+25)) {
			$AUS_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUS_DST=1;
			} else {
				$AUS_DST=0;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$AUS_DST=1;
			} else {
				$AUS_DST=0;
			}
			}
		} else {
			$AUS_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$AUS_DST=0;
		} elseif ($dd < ($dow+25)) {
			$AUS_DST=0;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUS_DST=0;
			} else {
				$AUS_DST=1;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$AUS_DST=0;
			} else {
				$AUS_DST=1;
			}
			}
		} else {
			$AUS_DST=1;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $AUS_DST\n";}
	if ($AUS_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'FSO-LSM') )
	{
	if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
	#**********************************************************************
	#   TASMANIA ONLY
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Sunday in October and last Sunday in March at 1 am.
	#**********************************************************************
		
		$AUST_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$AUST_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$AUST_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$AUST_DST=1;
		} elseif ($dd < ($dow+25)) {
			$AUST_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUST_DST=1;
			} else {
				$AUST_DST=0;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$AUST_DST=1;
			} else {
				$AUST_DST=0;
			}
			}
		} else {
			$AUST_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd > 7) {
			$AUST_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$AUST_DST=0;
			} else {
				$AUST_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 3600) {
				$AUST_DST=0;
			} else {
				$AUST_DST=1;
			}
			}
		} else {
			$AUST_DST=0;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $AUST_DST\n";}
	if ($AUST_DST) {$gmt_offset++;}
	$AC_processed++;
	}
if ( (!$AC_processed) and ($dst_range == 'FSO-TSM') )
	{
	if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Sunday in October and third Sunday in March at 1 am.
	#**********************************************************************
		
		$NZL_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$NZL_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$NZL_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 14) {
			$NZL_DST=1;
		} elseif ($dd < ($dow+14)) {
			$NZL_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$NZL_DST=1;
			} else {
				$NZL_DST=0;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$NZL_DST=1;
			} else {
				$NZL_DST=0;
			}
			}
		} else {
			$NZL_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd > 7) {
			$NZL_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$NZL_DST=0;
			} else {
				$NZL_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 3600) {
				$NZL_DST=0;
			} else {
				$NZL_DST=1;
			}
			}
		} else {
			$NZL_DST=0;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $NZL_DST\n";}
	if ($NZL_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'TSO-LSF') )
	{
	if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
	#**********************************************************************
	# TSO-LSF
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect. Brazil
	#     Based on Third Sunday October to Last Sunday February at 1 am.
	#**********************************************************************
		
		$BZL_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 2 || $mm > 10) {
		$BZL_DST=1;   
		} elseif ($mm >= 3 and $mm <= 9) {
		$BZL_DST=0;   
		} elseif ($mm == 2) {
		if ($dd < 22) {
			$BZL_DST=1;   
		} elseif ($dd < ($dow+22)) {
			$BZL_DST=1;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$BZL_DST=1;   
			} else {
				$BZL_DST=0;   
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$BZL_DST=1;   
			} else {
				$BZL_DST=0;   
			}
			}
		} else {
			$BZL_DST=0;   
		}
		} elseif ($mm == 10) {
		if ($dd < 22) {
			$BZL_DST=0;   
		} elseif ($dd < ($dow+22)) {
			$BZL_DST=0;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$BZL_DST=0;   
			} else {
				$BZL_DST=1;   
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$BZL_DST=0;   
			} else {
				$BZL_DST=1;   
			}
			}
		} else {
			$BZL_DST=1;   
		}
		} # end of month checks
	if ($DBX) {print "     DST: $BZL_DST\n";}
	if ($BZL_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if (!$AC_processed)
	{
	if ($DBX) {print "     No DST Method Found\n";}
	if ($DBX) {print "     DST: 0\n";}
	$AC_processed++;
	}

return $gmt_offset;
}



}

######################
# ADD=1111 display the ADD NEW INBOUND GROUP SCREEN
######################

if ($ADD==1111)
{
	if ($LOGmodify_ingroups==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW INBOUND GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group ID: </td><td align=left><input type=text name=group_id size=20 maxlength=20> (no spaces)$NWB#osdial_inbound_groups-group_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group Name: </td><td align=left><input type=text name=group_name size=30 maxlength=30>$NWB#osdial_inbound_groups-group_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group Color: </td><td align=left id=\"group_color_td\"><input type=text name=group_color size=7 maxlength=7>$NWB#osdial_inbound_groups-group_color$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option></select>$NWB#osdial_inbound_groups-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 1: </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"$web_form_address\">$NWB#osdial_inbound_groups-web_form_address$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 2: </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"$web_form_address2\">$NWB#osdial_inbound_groups-web_form_address$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Voicemail: </td><td align=left><input type=text name=voicemail_ext size=10 maxlength=10 value=\"$voicemail_ext\">$NWB#osdial_inbound_groups-voicemail_ext$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Next Agent Call: </td><td align=left><select size=1 name=next_agent_call><option >random</option><option>oldest_call_start</option><option>oldest_call_finish</option><option>overall_user_level</option><option>inbound_group_rank</option><option>campaign_rank</option><option>fewest_calls</option><option>fewest_calls_campaign</option></select>$NWB#osdial_inbound_groups-next_agent_call$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Fronter Display: </td><td align=left><select size=1 name=fronter_display><option SELECTED>Y</option><option>N</option></select>$NWB#osdial_inbound_groups-fronter_display$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script: </td><td align=left><select size=1 name=script_id>\n";
	echo "$scripts_list";
	echo "</select>$NWB#osdial_inbound_groups-ingroup_script$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Get Call Launch: </td><td align=left><select size=1 name=get_call_launch><option selected>NONE</option><option>SCRIPT</option><option>WEBFORM</option></select>$NWB#osdial_inbound_groups-get_call_launch$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=1211 display the COPY INBOUND GROUP SCREEN
######################

if ($ADD==1211)
{
	if ($LOGmodify_ingroups==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>COPY INBOUND GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2011>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group ID: </td><td align=left><input type=text name=group_id size=20 maxlength=20> (no spaces)$NWB#osdial_inbound_groups-group_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group Name: </td><td align=left><input type=text name=group_name size=30 maxlength=30>$NWB#osdial_inbound_groups-group_name$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Source Group ID: </td><td align=left><select size=1 name=source_group_id>\n";

		$stmt="SELECT group_id,group_name from vicidial_inbound_groups order by group_id";
		$rslt=mysql_query($stmt, $link);
		$groups_to_print = mysql_num_rows($rslt);
		$groups_list='';

		$o=0;
		while ($groups_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$groups_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
		}
	echo "$groups_list";
	echo "</select>$NWB#osdial_inbound_groups-group_id$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=11111 display the ADD NEW REMOTE AGENTS SCREEN
######################

if ($ADD==11111)
{
	if ($LOGmodify_remoteagents==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD NEW OFF-HOOK AGENTS</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Agent ID Start: </td><td align=left><input type=text name=user_start size=6 maxlength=6> (numbers only, incremented)$NWB#osdial_remote_agents-user_start$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Number of Lines: </td><td align=left><input type=text name=number_of_lines size=3 maxlength=3> (numbers only)$NWB#osdial_remote_agents-number_of_lines$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";
	echo "$servers_list";
	echo "</select>$NWB#osdial_remote_agents-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>External Extension: </td><td align=left><input type=text name=conf_exten size=20 maxlength=20> (dial plan number dialed to reach agents)$NWB#osdial_remote_agents-conf_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Status: </td><td align=left><select size=1 name=status><option>ACTIVE</option><option SELECTED>INACTIVE</option></select>$NWB#osdial_remote_agents-status$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
	echo "$campaigns_list";
	echo "</select>$NWB#osdial_remote_agents-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Inbound Groups: </td><td align=left>\n";
	echo "$groups_list";
	echo "$NWB#osdial_remote_agents-closer_campaigns$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "NOTE: It can take up to 30 seconds for changes submitted on this screen to go live\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=111111 display the ADD NEW AGENTS GROUP SCREEN
######################

if ($ADD==111111)
{
	if ($LOGmodify_usergroups==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD NEW AGENTS GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=211111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group: </td><td align=left><input type=text name=user_group size=15 maxlength=20> (no spaces or punctuation)$NWB#osdial_user_groups-user_group$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Description: </td><td align=left><input type=text name=group_name size=40 maxlength=40> (description of group)$NWB#osdial_user_groups-group_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=1111111 display the ADD NEW SCRIPT SCREEN
######################

if ($ADD==1111111)
{
	if ($LOGmodify_scripts==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD NEW SCRIPT</font><form name=scriptForm action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script ID: </td><td align=left><input type=text name=script_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_scripts-script_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script Name: </td><td align=left><input type=text name=script_name size=40 maxlength=50> (title of the script)$NWB#osdial_scripts-script_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script Comments: </td><td align=left><input type=text name=script_comments size=50 maxlength=255> $NWB#osdial_scripts-script_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option></select>$NWB#osdial_scripts-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script Text: </td><td align=left>";
	# BEGIN Insert Field
	echo "<select id=\"selectedField\" name=\"selectedField\">";
	echo "<option>vendor_lead_code</option>";
	echo "<option>source_id</option>";
	echo "<option>list_id</option>";
	echo "<option>gmt_offset_now</option>";
	echo "<option>called_since_last_reset</option>";
	echo "<option>phone_code</option>";
	echo "<option>phone_number</option>";
	echo "<option>title</option>";
	echo "<option>first_name</option>";
	echo "<option>middle_initial</option>";
	echo "<option>last_name</option>";
	echo "<option>address1</option>";
	echo "<option>address2</option>";
	echo "<option>address3</option>";
	echo "<option>city</option>";
	echo "<option>state</option>";
	echo "<option>province</option>";
	echo "<option>postal_code</option>";
	echo "<option>country_code</option>";
	echo "<option>gender</option>";
	echo "<option>date_of_birth</option>";
	echo "<option>alt_phone</option>";
	echo "<option>email</option>";
	echo "<option>security_phrase</option>";
	echo "<option>comments</option>";
	echo "<option>lead_id</option>";
	echo "<option>campaign</option>";
	echo "<option>phone_login</option>";
	echo "<option>group</option>";
	echo "<option>channel_group</option>";
	echo "<option>SQLdate</option>";
	echo "<option>epoch</option>";
	echo "<option>uniqueid</option>";
	echo "<option>customer_zap_channel</option>";
	echo "<option>server_ip</option>";
	echo "<option>SIPexten</option>";
	echo "<option>session_id</option>";
	echo "</select>";
	echo "<input type=\"button\" name=\"insertField\" value=\"Insert\" onClick=\"scriptInsertField();\"><BR>";
	# END Insert Field
	echo "<TEXTAREA NAME=script_text ROWS=20 COLS=50 value=\"\"></TEXTAREA> $NWB#osdial_scripts-script_text$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=11111111 display the ADD NEW FILTER SCREEN
######################

if ($ADD==11111111)
{
	if ($LOGmodify_filters==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD NEW FILTER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter ID: </td><td align=left><input type=text name=lead_filter_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_lead_filters-lead_filter_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter Name: </td><td align=left><input type=text name=lead_filter_name size=30 maxlength=30> (short description of the filter)$NWB#osdial_lead_filters-lead_filter_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter Comments: </td><td align=left><input type=text name=lead_filter_comments size=50 maxlength=255> $NWB#osdial_lead_filters-lead_filter_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter SQL: </td><td align=left><TEXTAREA NAME=lead_filter_sql ROWS=20 COLS=50 value=\"\"></TEXTAREA> $NWB#osdial_lead_filters-lead_filter_sql$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=111111111 display the ADD NEW CALL TIME SCREEN
######################

if ($ADD==111111111)
{
	if ($LOGmodify_call_times==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD NEW CALL TIME</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=211111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Call Time ID: </td><td align=left><input type=text name=call_time_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_call_times-call_time_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Call Time Name: </td><td align=left><input type=text name=call_time_name size=30 maxlength=30> (short description of the call time)$NWB#osdial_call_times-call_time_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Call Time Comments: </td><td align=left><input type=text name=call_time_comments size=50 maxlength=255> $NWB#osdial_call_times-call_time_comments$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2>Day and time options will appear once you have created the Call Time Definition</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=1111111111 display the ADD NEW STATE CALL TIME SCREEN
######################

if ($ADD==1111111111)
{
	if ($LOGmodify_call_times==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD NEW STATE CALL TIME</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>State Call Time ID: </td><td align=left><input type=text name=call_time_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_call_times-call_time_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>State Call Time State: </td><td align=left><input type=text name=state_call_time_state size=4 maxlength=2> (no spaces or punctuation)$NWB#osdial_call_times-state_call_time_state$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>State Call Time Name: </td><td align=left><input type=text name=call_time_name size=30 maxlength=30> (short description of the call time)$NWB#osdial_call_times-call_time_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>State Call Time Comments: </td><td align=left><input type=text name=call_time_comments size=50 maxlength=255> $NWB#osdial_call_times-call_time_comments$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2>Day and time options will appear once you have created the Call Time Definition</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=11111111111 display the ADD NEW PHONE SCREEN
######################

if ($ADD==11111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW PHONE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";

	echo "<center><TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Phone extension: </td><td align=left><input type=text name=extension size=20 maxlength=100 value=\"\">$NWB#phones-extension$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Dial Plan Number: </td><td align=left><input type=text name=dialplan_number size=15 maxlength=20 value=\"$row[1]\"> (digits only)$NWB#phones-dialplan_number$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Voicemail Box: </td><td align=left><input type=text name=voicemail_id size=10 maxlength=10 value=\"$row[2]\"> (digits only)$NWB#phones-voicemail_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Outbound CallerID: </td><td align=left><input type=text name=outbound_cid size=10 maxlength=20 value=\"$row[65]\"> (digits only)$NWB#phones-outbound_cid$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Phone IP address: </td><td align=left><input type=text name=phone_ip size=20 maxlength=15 value=\"$row[3]\"> (optional)$NWB#phones-phone_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Computer IP address: </td><td align=left><input type=text name=computer_ip size=20 maxlength=15 value=\"$row[4]\"> (optional)$NWB#phones-computer_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	echo "<option SELECTED>$row[5]</option>\n";
	echo "</select>$NWB#phones-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Login: </td><td align=left><input type=text name=login size=10 maxlength=10 value=\"$row[6]\">$NWB#phones-login$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=pass size=10 maxlength=10 value=\"$row[7]\">$NWB#phones-pass$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Status: </td><td align=left><select size=1 name=status><option>ACTIVE</option><option>SUSPENDED</option><option>CLOSED</option><option>PENDING</option><option>ADMIN</option><option selected>$row[8]</option></select>$NWB#phones-status$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active Account: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option selected>$row[9]</option></select>$NWB#phones-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Phone Type: </td><td align=left><input type=text name=phone_type size=20 maxlength=50 value=\"$row[10]\">$NWB#phones-phone_type$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Full Name: </td><td align=left><input type=text name=fullname size=20 maxlength=50 value=\"$row[11]\">$NWB#phones-fullname$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Company: </td><td align=left><input type=text name=company size=10 maxlength=10 value=\"$row[12]\">$NWB#phones-company$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Picture: </td><td align=left><input type=text name=picture size=20 maxlength=19 value=\"$row[13]\">$NWB#phones-picture$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Client Protocol: </td><td align=left><select size=1 name=protocol><option>SIP</option><option>Zap</option><option>IAX2</option><option>EXTERNAL</option><option selected>$row[16]</option></select>$NWB#phones-protocol$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$row[17]</option></select> (Do NOT Adjust for DST)$NWB#phones-local_gmt$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=111111111111 display the ADD NEW SERVER SCREEN
######################

if ($ADD==111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW SERVER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=211111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server ID: </td><td align=left><input type=text name=server_id size=10 maxlength=10>$NWB#servers-server_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server Description: </td><td align=left><input type=text name=server_description size=30 maxlength=255>$NWB#servers-server_description$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP Address: </td><td align=left><input type=text name=server_ip size=20 maxlength=15>$NWB#servers-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option></select>$NWB#servers-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Asterisk Version: </td><td align=left><input type=text name=asterisk_version size=20 maxlength=20>$NWB#servers-asterisk_version$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=1111111111111 display the ADD NEW CONFERENCE SCREEN
######################

if ($ADD==1111111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW CONFERENCE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2111111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Conference Number: </td><td align=left><input type=text name=conf_exten size=8 maxlength=7> (digits only)$NWB#conferences-conf_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	echo "<option SELECTED>$server_ip</option>\n";
	echo "</select>$NWB#conferences-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=11111111111111 display the ADD NEW OSDial CONFERENCE SCREEN
######################

if ($ADD==11111111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW OSDial CONFERENCE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Conference Number: </td><td align=left><input type=text name=conf_exten size=8 maxlength=7> (digits only)$NWB#conferences-conf_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	echo "<option SELECTED>$server_ip</option>\n";
	echo "</select>$NWB#conferences-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}



######################################################################################################
######################################################################################################
#######   2 series, validates form data and inserts the new record into the database
######################################################################################################
######################################################################################################


######################
# ADD=2 adds the new user to the system
######################

if ($ADD=="2")
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> USER NOT ADDED - there is already a user in the system with this user number</font>\n";}
	else
		{
		 if ( (strlen($user) < 2) or (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user) > 8) )
			{
			 echo "<br><font color=red> USER NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>user id must be between 2 and 8 characters long\n";
			 echo "<br>full name and password must be at least 2 characters long</font><br>\n";
			}
		 else
			{
			echo "<br><B>USER ADDED: $user</B>\n";

			$stmt="INSERT INTO vicidial_users (user,pass,full_name,user_level,user_group,phone_login,phone_pass) values('$user','$pass','$full_name','$user_level','$user_group','$phone_login','$phone_pass');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A USER          |$PHP_AUTH_USER|$ip|'$user','$pass','$full_name','$user_level','$user_group','$phone_login','$phone_pass'|\n");
				fclose($fp);
				}
			}
		}

$ADD=3;
}

######################
# ADD=2A adds the copied new user to the system
######################

if ($ADD=="2A")
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> USER NOT ADDED - there is already a user in the system with this user number</font>\n";}
	else
		{
		 if ( (strlen($user) < 2) or (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user) > 8) )
			{
			 echo "<br><font color=red> USER NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>user id must be between 2 and 8 characters long\n";
			 echo "<br>full name and password must be at least 2 characters long</font><br>\n";
			}
		 else
			{
			$stmt="INSERT INTO vicidial_users (user,pass,full_name,user_level,user_group,phone_login,phone_pass,delete_users,delete_user_groups,delete_lists,delete_campaigns,delete_ingroups,delete_remote_agents,load_leads,campaign_detail,ast_admin_access,ast_delete_phones,delete_scripts,modify_leads,hotkeys_active,change_agent_campaign,agent_choose_ingroups,closer_campaigns,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,delete_filters,alter_agent_interface_options,closer_default_blended,delete_call_times,modify_call_times,modify_users,modify_campaigns,modify_lists,modify_scripts,modify_filters,modify_ingroups,modify_usergroups,modify_remoteagents,modify_servers,view_reports,vicidial_recording_override,alter_custdata_override) SELECT \"$user\",\"$pass\",\"$full_name\",user_level,user_group,phone_login,phone_pass,delete_users,delete_user_groups,delete_lists,delete_campaigns,delete_ingroups,delete_remote_agents,load_leads,campaign_detail,ast_admin_access,ast_delete_phones,delete_scripts,modify_leads,hotkeys_active,change_agent_campaign,agent_choose_ingroups,closer_campaigns,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,delete_filters,alter_agent_interface_options,closer_default_blended,delete_call_times,modify_call_times,modify_users,modify_campaigns,modify_lists,modify_scripts,modify_filters,modify_ingroups,modify_usergroups,modify_remoteagents,modify_servers,view_reports,vicidial_recording_override,alter_custdata_override from vicidial_users where user=\"$source_user_id\";";
			$rslt=mysql_query($stmt, $link);

			$stmtA="INSERT INTO vicidial_inbound_group_agents (user,group_id,group_rank,group_weight,calls_today) SELECT \"$user\",group_id,group_rank,group_weight,\"0\" from vicidial_inbound_group_agents where user=\"$source_user_id\";";
			$rslt=mysql_query($stmtA, $link);

			$stmtA="INSERT INTO vicidial_campaign_agents (user,campaign_id,campaign_rank,campaign_weight,calls_today) SELECT \"$user\",campaign_id,campaign_rank,campaign_weight,\"0\" from vicidial_campaign_agents where user=\"$source_user_id\";";
			$rslt=mysql_query($stmtA, $link);

			echo "<br><B><font color=navy> USER COPIED: $user copied from $source_user_id</font></B>\n";
			echo "<br><br>\n";
			echo "<a href=\"$PHP_SELF?ADD=3&user=$user\">Click here to go to the user record</a>\n";
			echo "<br><br>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A COPIED USER   |$PHP_AUTH_USER|$ip|$user|$source_user_id|$stmt|\n");
				fclose($fp);
				}
			}
		}
exit;
}

######################
# ADD=21 adds the new campaign to the system
######################

if ($ADD==21)
{

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> CAMPAIGN NOT ADDED - there is already a campaign in the system with this ID</font>\n";}
	else
		{
		$stmt="SELECT count(*) from vicidial_inbound_groups where group_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{echo "<br><font color=red> CAMPAIGN NOT ADDED - there is already an inbound group in the system with this ID</font>\n";}
		else
			{
			 if ( (strlen($campaign_id) < 2) or (strlen($campaign_id) > 8) or (strlen($campaign_name) < 6)  or (strlen($campaign_name) > 40) )
				{
				 echo "<br><font color=red> CAMPAIGN NOT ADDED - Please go back and look at the data you entered\n";
				 echo "<br>campaign ID must be between 2 and 8 characters in length\n";
				 echo "<br>campaign name must be between 6 and 40 characters in length</font><br>\n";
				}
			 else
				{
				echo "<br><B><font color=navy> CAMPAIGN ADDED: $campaign_id</font></B>\n";

				$stmt="INSERT INTO vicidial_campaigns (campaign_id,campaign_name,campaign_description,active,dial_status_a,lead_order,park_ext,park_file_name,web_form_address,allow_closers,hopper_level,auto_dial_level,next_agent_call,local_call_time,voicemail_ext,campaign_script,get_call_launch,campaign_changedate,campaign_stats_refresh,list_order_mix,web_form_address2) values('$campaign_id','$campaign_name','$campaign_description','$active','NEW','DOWN','$park_ext','$park_file_name','" . mysql_real_escape_string($web_form_address) . "','$allow_closers','$hopper_level','$auto_dial_level','$next_agent_call','$local_call_time','$voicemail_ext','$script_id','$get_call_launch','$SQLdate','Y','DISABLED','" . mysql_real_escape_string($web_form_address2) . "');";
				$rslt=mysql_query($stmt, $link);

				$stmt="INSERT INTO vicidial_campaign_stats (campaign_id) values('$campaign_id');";
				$rslt=mysql_query($stmt, $link);

				echo "<!-- $stmt -->";
				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW CAMPAIGN  |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}

				}
			}
		}
$ADD=31;
}

######################
# ADD=20 adds copied new campaign to the system
######################

if ($ADD==20)
{

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> CAMPAIGN NOT ADDED - there is already a campaign in the system with this ID</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($campaign_id) > 8) or  (strlen($campaign_name) < 2) or (strlen($source_campaign_id) < 2) or (strlen($source_campaign_id) > 8) )
			{
			 echo "<br><font color=red> CAMPAIGN NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>campaign ID must be between 2 and 8 characters in length\n";
			 echo "<br>source campaign ID must be between 2 and 8 characters in length</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy> CAMPAIGN COPIED: $campaign_id copied from $source_campaign_id</font></B>\n";

			$stmt="INSERT INTO vicidial_campaigns (campaign_name,campaign_id,active,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,park_ext,park_file_name,web_form_address,allow_closers,hopper_level,auto_dial_level,next_agent_call,local_call_time,voicemail_ext,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,amd_send_to_vmx,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,lead_filter_id,drop_call_seconds,safe_harbor_message,safe_harbor_exten,display_dialable_count,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,dial_method,available_only_ratio_tally,adaptive_dropped_percentage,adaptive_maximum_level,adaptive_latest_server_time,adaptive_intensity,adaptive_dl_diff_target,concurrent_transfers,auto_alt_dial,auto_alt_dial_statuses,agent_pause_codes_active,campaign_description,campaign_changedate,campaign_stats_refresh,campaign_logindate,dial_statuses,disable_alter_custdata,no_hopper_leads_logins,list_order_mix,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,web_form_address2) SELECT \"$campaign_name\",\"$campaign_id\",\"N\",dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,park_ext,park_file_name,web_form_address,allow_closers,hopper_level,auto_dial_level,next_agent_call,local_call_time,voicemail_ext,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,amd_send_to_vmx,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,lead_filter_id,drop_call_seconds,safe_harbor_message,safe_harbor_exten,display_dialable_count,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,dial_method,available_only_ratio_tally,adaptive_dropped_percentage,adaptive_maximum_level,adaptive_latest_server_time,adaptive_intensity,adaptive_dl_diff_target,concurrent_transfers,auto_alt_dial,auto_alt_dial_statuses,agent_pause_codes_active,campaign_description,campaign_changedate,campaign_stats_refresh,campaign_logindate,dial_statuses,disable_alter_custdata,no_hopper_leads_logins,\"DISABLED\",campaign_allow_inbound,manual_dial_list_id,default_xfer_group,web_form_address2 from vicidial_campaigns where campaign_id='$source_campaign_id';";
			$rslt=mysql_query($stmt, $link);

			$stmtA="INSERT INTO vicidial_campaign_stats (campaign_id) values('$campaign_id');";
			$rslt=mysql_query($stmtA, $link);

			$stmtA="INSERT INTO vicidial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category) SELECT status,status_name,selectable,\"$campaign_id\",human_answered,category from vicidial_campaign_statuses where campaign_id='$source_campaign_id';";
			$rslt=mysql_query($stmtA, $link);

			$stmtA="INSERT INTO vicidial_campaign_hotkeys (status,hotkey,status_name,selectable,campaign_id) SELECT status,hotkey,status_name,selectable,\"$campaign_id\" from vicidial_campaign_hotkeys where campaign_id='$source_campaign_id';";
			$rslt=mysql_query($stmtA, $link);

			$stmtA="INSERT INTO vicidial_lead_recycle (status,attempt_delay,attempt_maximum,active,campaign_id) SELECT status,attempt_delay,attempt_maximum,active,\"$campaign_id\" from vicidial_lead_recycle where campaign_id='$source_campaign_id';";
			$rslt=mysql_query($stmtA, $link);

			$stmtA="INSERT INTO vicidial_pause_codes (pause_code,pause_code_name,billable,campaign_id) SELECT pause_code,pause_code_name,billable,\"$campaign_id\" from vicidial_pause_codes where campaign_id='$source_campaign_id';";
			$rslt=mysql_query($stmtA, $link);

			echo "<!-- $stmt -->";
			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|COPY TO NEW CAMPAIGN|$PHP_AUTH_USER|$ip|$campaign_id|$source_campaign_id|$stmt|$stmtA|\n");
				fclose($fp);
				}

			}
		}
$ADD=31;
}

######################
# ADD=22 adds the new campaign status to the system
######################

if ($ADD==22)
{

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaign_statuses where campaign_id='$campaign_id' and status='$status';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> CAMPAIGN STATUS NOT ADDED - there is already a campaign-status in the system with this name</font>\n";}
	else
		{
		$stmt="SELECT count(*) from vicidial_statuses where status='$status';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{echo "<br><font color=navy> CAMPAIGN STATUS NOT ADDED - there is already a global-status in the system with this name</font>\n";}
		else
			{
			 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) or (strlen($status_name) < 2) )
				{
				 echo "<br><font color=red> CAMPAIGN STATUS NOT ADDED - Please go back and look at the data you entered\n";
				 echo "<br>status must be between 1 and 8 characters in length\n";
				 echo "<br>status name must be between 2 and 30 characters in length</font><br>\n";
				}
			 else
				{
				echo "<br><B><font color=navy> CAMPAIGN STATUS ADDED: $campaign_id - $status</font></B>\n";

				$stmt="INSERT INTO vicidial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category) values('$status','$status_name','$selectable','$campaign_id','$human_answered','$category');";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW CAMPAIGN STATUS |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}
				}
			}
		}
$SUB=22;
$ADD=31;
}


######################
# ADD=23 adds the new campaign hotkey to the system
######################

if ($ADD==23)
{
	$HKstatus_data = explode('-----',$HKstatus);
	$status = $HKstatus_data[0];
	$status_name = $HKstatus_data[1];

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaign_hotkeys where campaign_id='$campaign_id' and hotkey='$hotkey' and hotkey='$hotkey';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> CAMPAIGN HOT KEY NOT ADDED - there is already a campaign-hotkey in the system with this hotkey</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) or (strlen($hotkey) < 1) )
			{
			 echo "<br><font color=red> CAMPAIGN HOT KEY NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>hotkey must be a single character between 1 and 9 \n";
			 echo "<br>status must be between 1 and 8 characters in length</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy> CAMPAIGN HOT KEY ADDED: $campaign_id - $status - $hotkey</font></B>\n";

			$stmt="INSERT INTO vicidial_campaign_hotkeys values('$status','$hotkey','$status_name','$selectable','$campaign_id');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW CAMPAIGN HOT KEY |$PHP_AUTH_USER|$ip|'$status','$hotkey','$status_name','$selectable','$campaign_id'|\n");
				fclose($fp);
				}
			}
		}
$SUB=23;
$ADD=31;
}


######################
# ADD=25 adds the new campaign lead recycle entry to the system
######################

if ($ADD==25)
{
	$status = eregi_replace("-----.*",'',$status);
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_lead_recycle where campaign_id='$campaign_id' and status='$status';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> CAMPAIGN LEAD RECYCLE NOT ADDED - there is already a lead-recycle for this campaign with this status</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) or ($attempt_delay < 120) or ($attempt_maximum < 1) or ($attempt_maximum > 10) )
			{
			 echo "<br><font color=red>CAMPAIGN LEAD RECYCLE NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length\n";
			 echo "<br>attempt delay must be at least 120 seconds\n";
			 echo "<br>maximum attempts must be from 1 to 10</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>CAMPAIGN LEAD RECYCLE ADDED: $campaign_id - $status - $attempt_delay</font></B>\n";

			$stmt="INSERT INTO vicidial_lead_recycle(campaign_id,status,attempt_delay,attempt_maximum,active) values('$campaign_id','$status','$attempt_delay','$attempt_maximum','$active');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW LEAD RECYCLE    |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$SUB=25;
$ADD=31;
}


######################
# ADD=26 adds the new auto alt dial status to the campaign
######################

if ($ADD==26)
{
	$status = eregi_replace("-----.*",'',$status);
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$campaign_id' and auto_alt_dial_statuses LIKE \"% $status %\";";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> AUTO ALT DIAL STATUS NOT ADDED - there is already an entry for this campaign with this status</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
			{
			 echo "<br><font color=red>AUTO ALT DIAL STATUS NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length</font>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>AUTO ALT DIAL STATUS ADDED: $campaign_id - $status</font></B>\n";

			$stmt="SELECT auto_alt_dial_statuses from vicidial_campaigns where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			if (strlen($row[0])<2) {$row[0] = ' -';}
			$auto_alt_dial_statuses = " $status$row[0]";
			$stmt="UPDATE vicidial_campaigns set auto_alt_dial_statuses='$auto_alt_dial_statuses' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A AUTO-ALT-DIAL STATUS|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$SUB=26;
$ADD=31;
}


######################
# ADD=27 adds the new campaign agent pause code entry to the system
######################

if ($ADD==27)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_pause_codes where campaign_id='$campaign_id' and pause_code='$pause_code';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>AGENT PAUSE CODE NOT ADDED - there is already an entry for this campaign with this pause code</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($pause_code) < 1) or (strlen($pause_code) > 6) or (strlen($pause_code_name) < 2) )
			{
			 echo "<br><font color=red>AGENT PAUSE CODE NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>pause code must be between 1 and 6 characters in length\n";
			 echo "<br>pause code name must be between 2 and 30 characters in length</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>AGENT PAUSE CODE ADDED: $campaign_id - $pause_code - $pause_code_name</font></B>\n";

			$stmt="INSERT INTO vicidial_pause_codes(campaign_id,pause_code,pause_code_name,billable) values('$campaign_id','$pause_code','$pause_code_name','$billable');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW AGENT PAUSE CODE|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$SUB=27;
$ADD=31;
}


######################
# ADD=28 adds new status to the campaign dial statuses
######################

if ($ADD==28)
{
	$status = eregi_replace("-----.*",'',$status);
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$campaign_id' and dial_statuses LIKE \"% $status %\";";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT ADDED - there is already an entry for this campaign with this status</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
			{
			 echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length</font>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>CAMPAIGN DIAL STATUS ADDED: $campaign_id - $status</font></B>\n";

			$stmt="SELECT dial_statuses from vicidial_campaigns where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			if (strlen($row[0])<2) {$row[0] = ' -';}
			$dial_statuses = " $status$row[0]";
			$stmt="UPDATE vicidial_campaigns set dial_statuses='$dial_statuses' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD CAMPAIGN DIAL STATUS  |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
#$SUB=28;
$ADD=31;
}


######################
# ADD=211 adds the new list to the system
######################

if ($ADD==211)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_lists where list_id='$list_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>LIST NOT ADDED - there is already a list in the system with this ID</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($list_name) < 2)  or ($list_id < 100) or (strlen($list_id) > 8) )
			{
			 echo "<br><font color=red>LIST NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>List ID must be between 2 and 8 characters in length\n";
			 echo "<br>List name must be at least 2 characters in length\n";
			 echo "<br>List ID must be greater than 100</font><br>\n";
			 }
		 else
			{
			echo "<br><B><font color=navy>LIST ADDED: $list_id</font></B>\n";

			$stmt="INSERT INTO vicidial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate) values('$list_id','$list_name','$campaign_id','$active','$list_description','$SQLdate');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW LIST      |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=311;
}



######################
# ADD=2111 adds the new inbound group to the system
######################

if ($ADD==2111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_inbound_groups where group_id='$group_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>GROUP NOT ADDED - there is already a group in the system with this ID</font>\n";}
	else
		{
		$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$group_id';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{echo "<br><font color=red>GROUP NOT ADDED - there is already a campaign in the system with this ID</font>\n";}
		else
			{
			 if ( (strlen($group_id) < 2) or (strlen($group_name) < 2)  or (strlen($group_color) < 2) or (strlen($group_id) > 20) or (eregi(' ',$group_id)) or (eregi("\-",$group_id)) or (eregi("\+",$group_id)) )
				{
				 echo "<br><font color=navy>GROUP NOT ADDED - Please go back and look at the data you entered\n";
				 echo "<br>Group ID must be between 2 and 20 characters in length and contain no ' -+'.\n";
				 echo "<br>Group name and group color must be at least 2 characters in length</font><br>\n";
				}
			 else
				{
				$stmt="INSERT INTO vicidial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,web_form_address2) values('$group_id','$group_name','$group_color','$active','" . mysql_real_escape_string($web_form_address) . "','$voicemail_ext','$next_agent_call','$fronter_display','$script_id','$get_call_launch','" . mysql_real_escape_string($web_form_address2) . "');";
				$rslt=mysql_query($stmt, $link);

				echo "<br><B><font color=navy>GROUP ADDED: $group_id</font></B>\n";

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW GROUP     |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}
				}
			}
		}
$ADD=3111;
}


######################
# ADD=2011 adds copied inbound group to the system
######################

if ($ADD==2011)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_inbound_groups where group_id='$group_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>GROUP NOT ADDED - there is already a group in the system with this ID</font>\n";}
	else
		{
		 if ( (strlen($group_id) < 2) or (strlen($group_name) < 2) or (strlen($group_id) > 20) or (eregi(' ',$group_id)) or (eregi("\-",$group_id)) or (eregi("\+",$group_id)) )
			{
			 echo "<br><font color=red>GROUP NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Group ID must be between 2 and 20 characters in length and contain no ' -+'.\n";
			 echo "<br>Group name and group color must be at least 2 characters in length</font><br>\n";
			}
		 else
			{
			$stmt="INSERT INTO vicidial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,drop_call_seconds,drop_message,drop_exten,call_time_id,after_hours_action,after_hours_message_filename,after_hours_exten,after_hours_voicemail,welcome_message_filename,moh_context,onhold_prompt_filename,prompt_interval,agent_alert_exten,agent_alert_delay,default_xfer_group,web_form_address2) SELECT \"$group_id\",\"$group_name\",group_color,\"N\",web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,drop_call_seconds,drop_message,drop_exten,call_time_id,after_hours_action,after_hours_message_filename,after_hours_exten,after_hours_voicemail,welcome_message_filename,moh_context,onhold_prompt_filename,prompt_interval,agent_alert_exten,agent_alert_delay,default_xfer_group,web_form_address2 from vicidial_inbound_groups where group_id=\"$source_group_id\";";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>GROUP ADDED: $group_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|COPIED TO NEW GROUP |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=3111;
}


######################
# ADD=21111 adds new remote agents to the system
######################

if ($ADD==21111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_remote_agents where server_ip='$server_ip' and user_start='$user_start';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>REMOTE AGENTS NOT ADDED - there is already a remote agents entry starting with this userID</font>\n";}
	else
		{
		 if ( (strlen($server_ip) < 2) or (strlen($user_start) < 2)  or (strlen($campaign_id) < 2) or (strlen($conf_exten) < 2) )
			{
			 echo "<br><font color=red>REMOTE AGENTS NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Agents ID start and external extension must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt="INSERT INTO vicidial_remote_agents values('','$user_start','$number_of_lines','$server_ip','$conf_exten','$status','$campaign_id','$groups_value');";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>REMOTE AGENTS ADDED: $user_start</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW REMOTE AGENTS ENTRY     |$PHP_AUTH_USER|$ip|'$user_start','$number_of_lines','$server_ip','$conf_exten','$status','$campaign_id','$groups_value'|\n");
				fclose($fp);
				}
			}
		}
$ADD=10000;
}

######################
# ADD=211111 adds new user group to the system
######################

if ($ADD==211111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_user_groups where user_group='$user_group';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>USER GROUP NOT ADDED - there is already a user group entry with this name</font>\n";}
	else
		{
		 if ( (strlen($user_group) < 2) or (strlen($group_name) < 2) )
			{
			 echo "<br><font color=red>USER GROUP NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Group name and description must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt="INSERT INTO vicidial_user_groups(user_group,group_name,allowed_campaigns) values('$user_group','$group_name','-ALL-CAMPAIGNS-');";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>USER GROUP ADDED: $user_group</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW USER GROUP ENTRY     |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=100000;
}

######################
# ADD=2111111 adds new script to the system
######################

if ($ADD==2111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_scripts where script_id='$script_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>SCRIPT NOT ADDED - there is already a script entry with this name</font>\n";}
	else
		{
		 if ( (strlen($script_id) < 2) or (strlen($script_name) < 2) or (strlen($script_text) < 2) )
			{
			 echo "<br><font color=red>SCRIPT NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Script name, description and text must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt="INSERT INTO vicidial_scripts values('$script_id','$script_name','$script_comments','$script_text','$active');";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>SCRIPT ADDED: $script_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW SCRIPT ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=1000000;
}


######################
# ADD=21111111 adds new filter to the system
######################

if ($ADD==21111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_lead_filters where lead_filter_id='$lead_filter_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>FILTER NOT ADDED - there is already a filter entry with this ID</font>\n";}
	else
		{
		 if ( (strlen($lead_filter_id) < 2) or (strlen($lead_filter_name) < 2) or (strlen($lead_filter_sql) < 2) )
			{
			 echo "<br><font color=red>FILTER NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Filter ID, name and SQL must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt="INSERT INTO vicidial_lead_filters SET lead_filter_id='$lead_filter_id',lead_filter_name='$lead_filter_name',lead_filter_comments='$lead_filter_comments',lead_filter_sql='$lead_filter_sql';";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>FILTER ADDED: $lead_filter_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW FILTER ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=10000000;
}


######################
# ADD=211111111 adds new call time definition to the system
######################

if ($ADD==211111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_call_times where call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>CALL TIME DEFINITION NOT ADDED - there is already a call time entry with this ID</font>\n";}
	else
		{
		 if ( (strlen($call_time_id) < 2) or (strlen($call_time_name) < 2) )
			{
			 echo "<br><font color=red>CALL TIME DEFINITION NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Call Time ID and name must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt="INSERT INTO vicidial_call_times SET call_time_id='$call_time_id',call_time_name='$call_time_name',call_time_comments='$call_time_comments';";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>CALL TIME ADDED: $call_time_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW CALL TIME ENTRY      |$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=311111111;
}


######################
# ADD=2111111111 adds new state call time definition to the system
######################

if ($ADD==2111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_state_call_times where state_call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>STATE CALL TIME DEFINITION NOT ADDED - there is already a call time entry with this ID</font>\n";}
	else
		{
		 if ( (strlen($call_time_id) < 2) or (strlen($call_time_name) < 2) or (strlen($state_call_time_state) < 2) )
			{
			 echo "<br><font color=red>STATE CALL TIME DEFINITION NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>State Call Time ID, name and state must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt="INSERT INTO vicidial_state_call_times SET state_call_time_id='$call_time_id',state_call_time_name='$call_time_name',state_call_time_comments='$call_time_comments',state_call_time_state='$state_call_time_state';";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>STATE CALL TIME ADDED: $call_time_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW STATE CALL TIME ENTRY|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=3111111111;
}


######################
# ADD=21111111111 adds new phone to the system
######################

if ($ADD==21111111111)
{
echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from phones where extension='$extension' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>PHONE NOT ADDED - there is already a Phone in the system with this extension/server</font>\n";}
	else
		{
		 if ( (strlen($extension) < 1) or (strlen($server_ip) < 7) or (strlen($dialplan_number) < 1) or (strlen($voicemail_id) < 1) or (strlen($login) < 1)  or (strlen($pass) < 1))
			{echo "<br><font color=red>PHONE NOT ADDED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=navy>PHONE ADDED</font>\n";

			$stmt="INSERT INTO phones (extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,protocol,local_gmt,outbound_cid) values('$extension','$dialplan_number','$voicemail_id','$phone_ip','$computer_ip','$server_ip','$login','$pass','$status','$active','$phone_type','$fullname','$company','$picture','$protocol','$local_gmt','$outbound_cid');";
			$rslt=mysql_query($stmt, $link);
			}
		}
$ADD=31111111111;
}


######################
# ADD=211111111111 adds new server to the system
######################

if ($ADD==211111111111)
{
echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from servers where server_id='$server_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>SERVER NOT ADDED - there is already a server in the system with this ID</font>\n";}
	else
		{
		 if ( (strlen($server_id) < 1) or (strlen($server_ip) < 7) )
			{echo "<br><font color=red>SERVER NOT ADDED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=navy>SERVER ADDED</font>\n";

			$stmt="INSERT INTO servers (server_id,server_description,server_ip,active,asterisk_version) values('$server_id','$server_description','$server_ip','$active','$asterisk_version');";
			$rslt=mysql_query($stmt, $link);
			}
		}
$ADD=311111111111;
}


######################
# ADD=221111111111 adds the new vicidial server trunk record to the system
######################

if ($ADD==221111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT max_vicidial_trunks from servers where server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	$MAXvicidial_trunks = $rowx[0];
	
	$stmt="SELECT sum(dedicated_trunks) from vicidial_server_trunks where server_ip='$server_ip' and campaign_id !='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	$SUMvicidial_trunks = ($rowx[0] + $dedicated_trunks);
	
	if ($SUMvicidial_trunks > $MAXvicidial_trunks)
		{
		echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT ADDED - the number of vicidial trunks is too high: $SUMvicidial_trunks / $MAXvicidial_trunks</font>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_server_trunks where campaign_id='$campaign_id' and server_ip='$server_ip';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT ADDED - there is already a server-trunk record for this campaign</font>\n";}
		else
			{
			 if ( (strlen($campaign_id) < 2) or (strlen($server_ip) < 7) or (strlen($dedicated_trunks) < 1) or (strlen($trunk_restriction) < 1) )
				{
				 echo "<br>OSDial SERVER TRUNK RECORD NOT ADDED - Please go back and look at the data you entered\n";
				 echo "<br>campaign must be between 3 and 8 characters in length\n";
				 echo "<br>server_ip delay must be at least 7 characters\n";
				 echo "<br>trunks must be a digit from 0 to 9999<br>\n";
				}
			 else
				{
				echo "<br><B><font color=navy>OSDial SERVER TRUNK RECORD ADDED: $campaign_id - $server_ip - $dedicated_trunks - $trunk_restriction</font></B>\n";

				$stmt="INSERT INTO vicidial_server_trunks(server_ip,campaign_id,dedicated_trunks,trunk_restriction) values('$server_ip','$campaign_id','$dedicated_trunks','$trunk_restriction');";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW OSDial TRUNK  |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}
				}
			}
		}
$ADD=311111111111;
}


######################
# ADD=2111111111111 adds new conference to the system
######################

if ($ADD==2111111111111)
{
echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from conferences where conf_exten='$conf_exten' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>CONFERENCE NOT ADDED - there is already a conference in the system with this ID and server</font>\n";}
	else
		{
		 if ( (strlen($conf_exten) < 1) or (strlen($server_ip) < 7) )
			{echo "<br><font color=red>CONFERENCE NOT ADDED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=navy>CONFERENCE ADDED</font>\n";

			$stmt="INSERT INTO conferences (conf_exten,server_ip) values('$conf_exten','$server_ip');";
			$rslt=mysql_query($stmt, $link);
			}
		}
$ADD=3111111111111;
}


######################
# ADD=21111111111111 adds new vicidial conference to the system
######################

if ($ADD==21111111111111)
{
echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_conferences where conf_exten='$conf_exten' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>OSDial CONFERENCE NOT ADDED - there is already an OSDial conference in the system with this ID and server</font>\n";}
	else
		{
		 if ( (strlen($conf_exten) < 1) or (strlen($server_ip) < 7) )
			{echo "<br><font color=red>OSDial CONFERENCE NOT ADDED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=navy>OSDial CONFERENCE ADDED</font>\n";

			$stmt="INSERT INTO vicidial_conferences (conf_exten,server_ip) values('$conf_exten','$server_ip');";
			$rslt=mysql_query($stmt, $link);
			}
		}
$ADD=31111111111111;
}


######################
# ADD=221111111111111 adds the new system status to the system
######################

if ($ADD==221111111111111)
{

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaign_statuses where status='$status';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>SYSTEM STATUS NOT ADDED - there is already a campaign-status in the system with this name: $row[0]</font>\n";}
	else
		{
		$stmt="SELECT count(*) from vicidial_statuses where status='$status';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{echo "<br><font color=red>SYSTEM STATUS NOT ADDED - there is already a global-status in the system with this name</font>\n";}
		else
			{
			 if ( (strlen($status) < 1) or (strlen($status_name) < 2) )
				{
				 echo "<br><font color=navy>SYSTEM STATUS NOT ADDED - Please go back and look at the data you entered\n";
				 echo "<br>status must be between 1 and 8 characters in length\n";
				 echo "<br>status name must be between 2 and 30 characters in length</font><br>\n";
				}
			 else
				{
				echo "<br><B><font color=navy>SYSTEM STATUS ADDED: $status_name - $status</font></B>\n";

				$stmt="INSERT INTO vicidial_statuses (status,status_name,selectable,human_answered,category) values('$status','$status_name','$selectable','$human_answered','$category');";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW SYSTEM STATUS   |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}
				}
			}
		}
$ADD=321111111111111;
}


######################
# ADD=231111111111111 adds the new status category to the system
######################

if ($ADD==231111111111111)
{

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_status_categories where vsc_id='$vsc_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>STATUS CATEGORY NOT ADDED - there is already a status category in the system with this ID: $row[0]</font>\n";}
	else
		{
		 if ( (strlen($vsc_id) < 2) or (strlen($vsc_id) > 20) or (strlen($vsc_name) < 2) )
			{
			 echo "<br><font color=red>STATUS CATEGORY NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>ID must be between 2 and 20 characters in length\n";
			 echo "<br>name name must be between 2 and 50 characters in length</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>STATUS CATEGORY ADDED: $vsc_id - $vsc_name</font></B>\n";

			$stmt="SELECT count(*) from vicidial_status_categories where tovdad_display='Y' and vsc_id NOT IN('$vsc_id');";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ( ($row[0] > 3) and (ereg('Y',$tovdad_display)) )
				{
				$tovdad_display = 'N';
				echo "<br><B><font color=red>ERROR: There are already 4 Status Categories set to Time On OSDial Display</font></B>\n";
				}

			$stmt="INSERT INTO vicidial_status_categories (vsc_id,vsc_name,vsc_description,tovdad_display) values('$vsc_id','$vsc_name','$vsc_description','$tovdad_display');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW STATUS CATEGORY |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=331111111111111;
}



######################################################################################################
######################################################################################################
#######   4 series, record modifications submitted and DB is modified, then on to 3 series forms below
######################################################################################################
######################################################################################################



######################
# ADD=4A submit user modifications to the system - ADMIN
######################

if ($ADD=="4A")
{
	if ($LOGmodify_users==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user_level) < 1) )
		{
		 echo "<br><font color=red>USER NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>USER MODIFIED - ADMIN: $user</font></B>\n";

		$stmt="UPDATE vicidial_users set pass='$pass',full_name='$full_name',user_level='$user_level',user_group='$user_group',phone_login='$phone_login',phone_pass='$phone_pass',delete_users='$delete_users',delete_user_groups='$delete_user_groups',delete_lists='$delete_lists',delete_campaigns='$delete_campaigns',delete_ingroups='$delete_ingroups',delete_remote_agents='$delete_remote_agents',load_leads='$load_leads',campaign_detail='$campaign_detail',ast_admin_access='$ast_admin_access',ast_delete_phones='$ast_delete_phones',delete_scripts='$delete_scripts',modify_leads='$modify_leads',hotkeys_active='$hotkeys_active',change_agent_campaign='$change_agent_campaign',agent_choose_ingroups='$agent_choose_ingroups',closer_campaigns='$groups_value',scheduled_callbacks='$scheduled_callbacks',agentonly_callbacks='$agentonly_callbacks',agentcall_manual='$agentcall_manual',vicidial_recording='$vicidial_recording',vicidial_transfers='$vicidial_transfers',delete_filters='$delete_filters',alter_agent_interface_options='$alter_agent_interface_options',closer_default_blended='$closer_default_blended',delete_call_times='$delete_call_times',modify_call_times='$modify_call_times',modify_users='$modify_users',modify_campaigns='$modify_campaigns',modify_lists='$modify_lists',modify_scripts='$modify_scripts',modify_filters='$modify_filters',modify_ingroups='$modify_ingroups',modify_usergroups='$modify_usergroups',modify_remoteagents='$modify_remoteagents',modify_servers='$modify_servers',view_reports='$view_reports',vicidial_recording_override='$vicidial_recording_override',alter_custdata_override='$alter_custdata_override' where user='$user';";
		$rslt=mysql_query($stmt, $link);



		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY USER INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo " <font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3;		# go to user modification below
}


######################
# ADD=4B submit user modifications to the system - ADMIN
######################

if ($ADD=="4B")
{
	if ($LOGmodify_users==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user_level) < 1) )
		{
		 echo "<br><font color=red>USER NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>USER MODIFIED - ADMIN: $user</font></B>\n";

		$stmt="UPDATE vicidial_users set pass='$pass',full_name='$full_name',user_level='$user_level',user_group='$user_group',phone_login='$phone_login',phone_pass='$phone_pass',hotkeys_active='$hotkeys_active',agent_choose_ingroups='$agent_choose_ingroups',closer_campaigns='$groups_value',scheduled_callbacks='$scheduled_callbacks',agentonly_callbacks='$agentonly_callbacks',agentcall_manual='$agentcall_manual',vicidial_recording='$vicidial_recording',vicidial_transfers='$vicidial_transfers',closer_default_blended='$closer_default_blended',vicidial_recording_override='$vicidial_recording_override',alter_custdata_override='$alter_custdata_override' where user='$user';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY USER INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3;		# go to user modification below
}



######################
# ADD=4 submit user modifications to the system
######################

if ($ADD==4)
{
	if ($LOGmodify_users==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user_level) < 1) )
		{
		 echo "<br><font color=red>USER NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>USER MODIFIED: $user</font></B>\n";

		$stmt="UPDATE vicidial_users set pass='$pass',full_name='$full_name',user_level='$user_level',user_group='$user_group',phone_login='$phone_login',phone_pass='$phone_pass' where user='$user';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY USER INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3;		# go to user modification below
}

######################
# ADD=41 submit campaign modifications to the system
######################

if ($ADD==41)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_name) < 6) or (strlen($active) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>the campaign name needs to be at least 6 characters in length\n";
		 echo "<br>|$campaign_name|$active|</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CAMPAIGN MODIFIED: $campaign_id</font></B>\n";

		if ($dial_method == 'MANUAL') 
			{
			$auto_dial_level='0';
			$adlSQL = "auto_dial_level='0',";
			}
		else
			{
			if ($dial_level_override > 0)
				{
				$adlSQL = "auto_dial_level='$auto_dial_level',";
				}
			else
				{
				if ($dial_method == 'RATIO')
					{
					if ($auto_dial_level < 1) {$auto_dial_level = "1.0";}
					$adlSQL = "auto_dial_level='$auto_dial_level',";
					}
				else
					{
					$adlSQL = "";
					if ($auto_dial_level < 1) 
						{
						$auto_dial_level = "1.0";
						$adlSQL = "auto_dial_level='$auto_dial_level',";
						}
					}
				}
			}
		if ( (!ereg("DISABLED",$list_order_mix)) and ($hopper_level < 100) )
			{$hopper_level='100';}

		$stmtA="UPDATE vicidial_campaigns set campaign_name='$campaign_name',active='$active',dial_status_a='$dial_status_a',dial_status_b='$dial_status_b',dial_status_c='$dial_status_c',dial_status_d='$dial_status_d',dial_status_e='$dial_status_e',lead_order='$lead_order',allow_closers='$allow_closers',hopper_level='$hopper_level', $adlSQL next_agent_call='$next_agent_call', local_call_time='$local_call_time', voicemail_ext='$voicemail_ext', dial_timeout='$dial_timeout', dial_prefix='$dial_prefix', campaign_cid='$campaign_cid', campaign_vdad_exten='$campaign_vdad_exten', web_form_address='" . mysql_real_escape_string($web_form_address) . "', park_ext='$park_ext', park_file_name='$park_file_name', campaign_rec_exten='$campaign_rec_exten', campaign_recording='$campaign_recording', campaign_rec_filename='$campaign_rec_filename', campaign_script='$script_id', get_call_launch='$get_call_launch', am_message_exten='$am_message_exten', amd_send_to_vmx='$amd_send_to_vmx', xferconf_a_dtmf='$xferconf_a_dtmf',xferconf_a_number='$xferconf_a_number', xferconf_b_dtmf='$xferconf_b_dtmf',xferconf_b_number='$xferconf_b_number',lead_filter_id='$lead_filter_id',alt_number_dialing='$alt_number_dialing',scheduled_callbacks='$scheduled_callbacks',safe_harbor_message='$safe_harbor_message',drop_call_seconds='$drop_call_seconds',safe_harbor_exten='$safe_harbor_exten',wrapup_seconds='$wrapup_seconds',wrapup_message='$wrapup_message',closer_campaigns='$groups_value',use_internal_dnc='$use_internal_dnc',allcalls_delay='$allcalls_delay',omit_phone_code='$omit_phone_code',dial_method='$dial_method',available_only_ratio_tally='$available_only_ratio_tally',adaptive_dropped_percentage='$adaptive_dropped_percentage',adaptive_maximum_level='$adaptive_maximum_level',adaptive_latest_server_time='$adaptive_latest_server_time',adaptive_intensity='$adaptive_intensity',adaptive_dl_diff_target='$adaptive_dl_diff_target',concurrent_transfers='$concurrent_transfers',auto_alt_dial='$auto_alt_dial',agent_pause_codes_active='$agent_pause_codes_active',campaign_description='$campaign_description',campaign_changedate='$SQLdate',campaign_stats_refresh='$campaign_stats_refresh',disable_alter_custdata='$disable_alter_custdata',no_hopper_leads_logins='$no_hopper_leads_logins',list_order_mix='$list_order_mix',campaign_allow_inbound='$campaign_allow_inbound',manual_dial_list_id='$manual_dial_list_id',default_xfer_group='$default_xfer_group',xfer_groups='$XFERgroups_value', web_form_address2='" . mysql_real_escape_string($web_form_address2) . "' where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmtA, $link);

		if ($reset_hopper == 'Y')
			{
			echo "<br><font color=navy>RESETTING CAMPAIGN LEAD HOPPER\n";
			echo "<br> - Wait 1 minute before dialing next number</font>\n";
			$stmt="DELETE from vicidial_hopper where campaign_id='$campaign_id' and status IN('READY','QUEUE','DONE');";
			$rslt=mysql_query($stmt, $link);

			### LOG RESET TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|CAMPAIGN HOPPERRESET|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY CAMPAIGN INFO|$PHP_AUTH_USER|$ip|$stmtA|$reset_hopper|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=31;	# go to campaign modification form below
}

######################
# ADD=42 modify/delete campaign status in the system
######################

if ($ADD==42)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN STATUS NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>the campaign id needs to be at least 2 characters in length\n";
		 echo "<br>the campaign status needs to be at least 1 characters in length</font><br>\n";
		}
	 else
		{
		if (ereg('delete',$stage))
			{
			echo "<br><B><font color=navy>CUSTOM CAMPAIGN STATUS DELETED: $campaign_id - $status</font></B>\n";

			$stmt="DELETE FROM vicidial_campaign_statuses where campaign_id='$campaign_id' and status='$status';";
			$rslt=mysql_query($stmt, $link);

			$stmtA="DELETE FROM vicidial_campaign_hotkeys where campaign_id='$campaign_id' and status='$status';";
			$rslt=mysql_query($stmtA, $link);


			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DELETE CAMPAIGN STATUS|$PHP_AUTH_USER|$ip|$stmt|$stmtA|\n");
				fclose($fp);
				}
			}
		if (ereg('modify',$stage))
			{
			echo "<br><B><font color=navy>CUSTOM CAMPAIGN STATUS MODIFIED: $campaign_id - $status</font></B>\n";

			$stmt="UPDATE vicidial_campaign_statuses SET status_name='$status_name',selectable='$selectable',human_answered='$human_answered',category='$category' where campaign_id='$campaign_id' and status='$status';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY CAMPAIGN STATUS|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=22;
$ADD=31;	# go to campaign modification form below
}

######################
# ADD=43 delete campaign hotkey in the system
######################

if ($ADD==43)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) or (strlen($hotkey) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN HOT KEY NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>the campaign id needs to be at least 2 characters in length\n";
		 echo "<br>the campaign status needs to be at least 1 characters in length\n";
		 echo "<br>the campaign hotkey needs to be at least 1 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CUSTOM CAMPAIGN HOT KEY DELETED: $campaign_id - $status - $hotkey</font></B>\n";

		$stmt="DELETE FROM vicidial_campaign_hotkeys where campaign_id='$campaign_id' and status='$status' and hotkey='$hotkey';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|DELETE CAMPAIGN STATUS|$PHP_AUTH_USER|$ip|DELETE FROM vicidial_campaign_hotkeys where campaign_id='$campaign_id' and status='$status' and hotkey='$hotkey'|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=23;
$ADD=31;	# go to campaign modification form below
}

######################
# ADD=44 submit campaign modifications to the system - Basic View
######################

if ($ADD==44)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_name) < 6) or (strlen($active) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>the campaign name needs to be at least 6 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CAMPAIGN MODIFIED: $campaign_id</font></B>\n";

		if ($dial_method == 'RATIO')
			{
			if ($auto_dial_level < 1) {$auto_dial_level = "1.0";}
			$adlSQL = "auto_dial_level='$auto_dial_level',";
			}
		else
			{
			if ($dial_method == 'MANUAL') 
				{
				$auto_dial_level='0';
				$adlSQL = "auto_dial_level='0',";
				}
			else
				{
				$adlSQL = "";
				if ($auto_dial_level < 1) 
					{
					$auto_dial_level = "1.0";
					$adlSQL = "auto_dial_level='$auto_dial_level',";
					}
				}
			}
		if ( (!ereg("DISABLED",$list_order_mix)) and ($hopper_level < 100) )
			{$hopper_level='100';}

		$stmtA="UPDATE vicidial_campaigns set campaign_name='$campaign_name',active='$active',dial_status_a='$dial_status_a',dial_status_b='$dial_status_b',dial_status_c='$dial_status_c',dial_status_d='$dial_status_d',dial_status_e='$dial_status_e',lead_order='$lead_order',hopper_level='$hopper_level', $adlSQL lead_filter_id='$lead_filter_id',dial_method='$dial_method',adaptive_intensity='$adaptive_intensity',campaign_changedate='$SQLdate',list_order_mix='$list_order_mix' where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmtA, $link);

		if ($reset_hopper == 'Y')
			{
			echo "<br>RESETTING CAMPAIGN LEAD HOPPER\n";
			echo "<br> - Wait 1 minute before dialing next number\n";
			$stmt="DELETE from vicidial_hopper where campaign_id='$campaign_id' and status IN('READY','QUEUE','DONE');;";
			$rslt=mysql_query($stmt, $link);

			### LOG HOPPER RESET TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|CAMPAIGN HOPPERRESET|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY CAMPAIGN INFO|$PHP_AUTH_USER|$ip|$stmtA|$reset_hopper|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=34;	# go to campaign modification form below
}

######################
# ADD=45 modify campaign lead recycle in the system
######################

if ($ADD==45)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) or ($attempt_delay < 120)  or ($attempt_maximum < 1) or ($attempt_maximum > 10) )
		{
		 echo "<br><font color=red>CAMPAIGN LEAD RECYCLE NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>status must be between 1 and 6 characters in length\n";
		 echo "<br>attempt delay must be at least 120 seconds\n";
		 echo "<br>maximum attempts must be from 1 to 10</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CAMPAIGN LEAD MODIFIED: $campaign_id - $status - $attempt_delay</font></B>\n";

		$stmt="UPDATE vicidial_lead_recycle SET attempt_delay='$attempt_delay',attempt_maximum='$attempt_maximum',active='$active' where campaign_id='$campaign_id' and status='$status';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY LEAD RECYCLE   |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=25;
$ADD=31;	# go to campaign modification form below
}

######################
# ADD=47 modify agent pause code in the system
######################

if ($ADD==47)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($pause_code) < 1) or (strlen($pause_code) > 6) or (strlen($pause_code_name) < 2) )
		{
		 echo "<br><font color=red>AGENT PAUSE CODE NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>pause_code must be between 1 and 6 characters in length\n";
		 echo "<br>pause_code name must be between 2 and 30 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>AGENT PAUSE CODE MODIFIED: $campaign_id - $pause_code - $pause_code_name</font></B>\n";

		$stmt="UPDATE vicidial_pause_codes SET pause_code_name='$pause_code_name',billable='$billable' where campaign_id='$campaign_id' and pause_code='$pause_code';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY AGENT PAUSECODE|$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=27;
$ADD=31;	# go to campaign modification form below
}


######################
# ADD=49 modify campaign list mix in the system
######################

if ($ADD==49)
{
	if ($LOGmodify_campaigns==1)
	{
	##### MODIFY a list mix container entry #####
		if ($stage=='MODIFY')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$Flist_mix_container = "list_mix_container_$vcl_id";
		$Fmix_method = "mix_method_$vcl_id";
		$Fstatus = "status_$vcl_id";
		$Fvcl_name = "vcl_name_$vcl_id";

		if (isset($_GET[$Flist_mix_container]))				{$list_mix_container=$_GET[$Flist_mix_container];}
			elseif (isset($_POST[$Flist_mix_container]))	{$list_mix_container=$_POST[$Flist_mix_container];}
		if (isset($_GET[$Fmix_method]))						{$mix_method=$_GET[$Fmix_method];}
			elseif (isset($_POST[$Fmix_method]))			{$mix_method=$_POST[$Fmix_method];}
		if (isset($_GET[$Fstatus]))							{$status=$_GET[$Fstatus];}
			elseif (isset($_POST[$Fstatus]))				{$status=$_POST[$Fstatus];}
		if (isset($_GET[$Fvcl_name]))						{$vcl_name=$_GET[$Fvcl_name];}
			elseif (isset($_POST[$Fvcl_name]))				{$vcl_name=$_POST[$Fvcl_name];}
		$list_mix_container = preg_replace("/:$/","",$list_mix_container);

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) or (strlen($list_mix_container) < 6) or (strlen($vcl_name) < 2) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>vcl_name name must be between 2 and 30 characters in length</font><br>\n";
			}
		 else
			{
			$stmt="UPDATE vicidial_campaigns_list_mix SET vcl_name='$vcl_name',mix_method='$mix_method',list_mix_container='$list_mix_container' where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

			echo "<br><B><font color=navy>LIST MIX MODIFIED: $campaign_id - $vcl_id - $vcl_name</font></B>\n";
			}
		}

	##### ADD a list mix container entry #####
		if ($stage=='ADD')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) or (strlen($list_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>list_id must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt="SELECT list_mix_container from vicidial_campaigns_list_mix where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$OLDlist_mix_container =	$row[0];
			$NEWlist_mix_container = "$OLDlist_mix_container:$list_id|10|0| -|";

			$stmt="UPDATE vicidial_campaigns_list_mix SET list_mix_container='$NEWlist_mix_container' where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

			echo "<br><B><font color=navy>LIST MIX MODIFIED: $campaign_id - $vcl_id - $list_id</font></B>\n";
			}
		}

	##### REMOVE a list mix container entry #####
		if ($stage=='REMOVE')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) or (strlen($list_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>list_id must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt="SELECT list_mix_container from vicidial_campaigns_list_mix where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$MIXentries = $MT;
			$MIXentries = explode(":", $row[0]);
			$Ms_to_print = (count($MIXentries) - 0);

			if ($Ms_to_print < 2)
				{
				echo "<br><B><font color=red>LIST MIX NOT MODIFIED: You cannot delete the last list_id entry from a list mix</font></B>\n";
				}
			else
				{
				$MIXdetailsPCT = explode('|', $MIXentries[$mix_container_item]);
				$MIXpercentPCT = $MIXdetailsPCT[2];

				$q=0;
				while ($Ms_to_print > $q) 
					{
					if ( ($mix_container_item > $q) or ($mix_container_item < $q) )
						{
						if ( ($q==0) and ($mix_container_item > 0) )
							{
							$MIXdetailsONE = explode('|', $MIXentries[$q]);
							$MIXpercentONE = ($MIXdetailsONE[2] + $MIXpercentPCT);
							$NEWlist_mix_container .= "$MIXdetailsONE[0]|$MIXdetailsONE[1]|$MIXpercentONE|$MIXdetailsONE[3]|:";
							}
						else
							{
							if ( ($q==1) and ($mix_container_item < 1) )
								{
								$MIXdetailsONE = explode('|', $MIXentries[$q]);
								$MIXpercentONE = ($MIXdetailsONE[2] + $MIXpercentPCT);
								$NEWlist_mix_container .= "$MIXdetailsONE[0]|$MIXdetailsONE[1]|$MIXpercentONE|$MIXdetailsONE[3]|:";
								}
							else
								{
								$NEWlist_mix_container .= "$MIXentries[$q]:";
								}
							}
						}
					$q++;
					}
				$NEWlist_mix_container = preg_replace("/.$/",'',$NEWlist_mix_container);

				$stmt="UPDATE vicidial_campaigns_list_mix SET list_mix_container='$NEWlist_mix_container' where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}

				echo "<br><B><font color=navy>LIST MIX MODIFIED: $campaign_id - $vcl_id - $list_id - $mix_container_item</font></B>\n";
				}
			}
		}

	##### ADD a NEW list mix #####
		if ($stage=='NEWMIX')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) or (strlen($vcl_name) < 2) )
			{
			 echo "<br><font color=red>LIST MIX NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>vcl_name must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt="SELECT count(*) from vicidial_campaigns_list_mix where vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ($row[0] > 0)
				{
				 echo "<br><font color=red>LIST MIX NOT ADDED - There is already a list mix with this ID in the system</font>\n";
				}
			else
				{
				$stmt="INSERT INTO vicidial_campaigns_list_mix SET list_mix_container='$list_id|1|100| $status -|',campaign_id='$campaign_id',vcl_id='$vcl_id',vcl_name='$vcl_name',mix_method='$mix_method',status='INACTIVE';";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}

				echo "<br><B><font color=navy>LIST MIX ADDED: $campaign_id - $vcl_id - $vcl_name</font></B>\n";
				}
			}
		}

	##### DELETE an existing list mix #####
		if ($stage=='DELMIX')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT DELETED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length</font>\n";
			}
		 else
			{
			$stmt="DELETE from vicidial_campaigns_list_mix where vcl_id='$vcl_id' and campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

			echo "<br><B><font color=navy>LIST MIX DELETED: $campaign_id - $vcl_id - $vcl_name</font></B>\n";
			}
		}

	##### Set list mix entry to active #####
		if ($stage=='SETACTIVE')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT ACTIVATED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length</font>\n";
			}
		 else
			{
			$stmt="UPDATE vicidial_campaigns_list_mix SET status='INACTIVE' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			$stmt="UPDATE vicidial_campaigns_list_mix SET status='ACTIVE' where vcl_id='$vcl_id' and campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

			echo "<br><B><font color=navy>LIST MIX ACTIVATED: $campaign_id - $vcl_id - $vcl_name</font></B>\n";
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=29;
$ADD=31;	# go to campaign modification form below
}


######################
# ADD=411 submit list modifications to the system
######################

if ($ADD==411)
{
	if ($LOGmodify_lists==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($list_name) < 2) or (strlen($campaign_id) < 2) )
		{
		 echo "<br><font color=red>LIST NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>list name must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>LIST MODIFIED: $list_id</font></B>\n";

		$stmt="UPDATE vicidial_lists set list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description',list_changedate='$SQLdate' where list_id='$list_id';";
		$rslt=mysql_query($stmt, $link);

		if ($reset_list == 'Y')
			{
			echo "<br><font color=navy>RESETTING LIST-CALLED-STATUS</font>\n";
			$stmt="UPDATE vicidial_list set called_since_last_reset='N' where list_id='$list_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG RESET TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|RESET LIST CALLED   |$PHP_AUTH_USER|$ip|list_name='$list_name'|\n");
				fclose($fp);
				}
			}
		if ($campaign_id != "$old_campaign_id")
			{
			echo "<br><font color=navy>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($old_campaign_id)</font>\n";
			$stmt="DELETE from vicidial_hopper where list_id='$list_id' and campaign_id='$old_campaign_id';";
			$rslt=mysql_query($stmt, $link);
			}

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY LIST INFO    |$PHP_AUTH_USER|$ip|list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description' where list_id='$list_id'|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311;	# go to list modification form below
}


######################
# ADD=4111 modify in-group info in the system
######################

if ($ADD==4111)
{
	if ($LOGmodify_ingroups==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($group_name) < 2) or (strlen($group_color) < 2) )
		{
		 echo "<br><font color=red>GROUP NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>group name and group color must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>GROUP MODIFIED: $group_id</font></B>\n";

		$stmt="UPDATE vicidial_inbound_groups set group_name='$group_name', group_color='$group_color', active='$active', web_form_address='" . mysql_real_escape_string($web_form_address) . "', voicemail_ext='$voicemail_ext', next_agent_call='$next_agent_call', fronter_display='$fronter_display', ingroup_script='$script_id', get_call_launch='$get_call_launch', xferconf_a_dtmf='$xferconf_a_dtmf',xferconf_a_number='$xferconf_a_number', xferconf_b_dtmf='$xferconf_b_dtmf',xferconf_b_number='$xferconf_b_number',drop_message='$drop_message',drop_call_seconds='$drop_call_seconds',drop_exten='$drop_exten',call_time_id='$call_time_id',after_hours_action='$after_hours_action',after_hours_message_filename='$after_hours_message_filename',after_hours_exten='$after_hours_exten',after_hours_voicemail='$after_hours_voicemail',welcome_message_filename='$welcome_message_filename',moh_context='$moh_context',onhold_prompt_filename='$onhold_prompt_filename',prompt_interval='$prompt_interval',agent_alert_exten='$agent_alert_exten',agent_alert_delay='$agent_alert_delay',default_xfer_group='$default_xfer_group', web_form_address2='" . mysql_real_escape_string($web_form_address2) . "' where group_id='$group_id';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY GROUP INFO   |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3111;	# go to in-group modification form below
}



######################
# ADD=41111 modify remote agents info in the system
######################

if ($ADD==41111)
{
	if ($LOGmodify_remoteagents==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($server_ip) < 2) or (strlen($user_start) < 2)  or (strlen($campaign_id) < 2) or (strlen($conf_exten) < 2) )
		{
		 echo "<br><font color=red>REMOTE AGENTS NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Agent ID Start and External Extension must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="UPDATE vicidial_remote_agents set user_start='$user_start', number_of_lines='$number_of_lines', server_ip='$server_ip', conf_exten='$conf_exten', status='$status', campaign_id='$campaign_id', closer_campaigns='$groups_value' where remote_agent_id='$remote_agent_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=navy>REMOTE AGENTS MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY REMOTE AGENTS ENTRY     |$PHP_AUTH_USER|$ip|set user_start='$user_start', number_of_lines='$number_of_lines', server_ip='$server_ip', conf_exten='$conf_exten', status='$status', campaign_id='$campaign_id', closer_campaigns='$groups_value' where remote_agent_id='$remote_agent_id'|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=31111;	# go to remote agents modification form below
}



######################
# ADD=411111 modify user group info in the system
######################

if ($ADD==411111)
{
	if ($LOGmodify_usergroups==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($user_group) < 2) or (strlen($group_name) < 2) )
		{
		 echo "<br><font color=red>USER GROUP NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Group name and description must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="UPDATE vicidial_user_groups set user_group='$user_group', group_name='$group_name',allowed_campaigns='$campaigns_value' where user_group='$OLDuser_group';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=navy>USER GROUP MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY USER GROUP ENTRY     |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111;	# go to user group modification form below
}

######################
# ADD=4111111 modify script in the system
######################

if ($ADD==4111111)
{
	if ($LOGmodify_scripts==1)
	{
	echo "<!-- $script_text -->\n";
	echo "<!--" . mysql_real_escape_string($script_text) . " -->\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($script_id) < 2) or (strlen($script_name) < 2) or (strlen($script_text) < 2) )
		{
		 echo "<br><font color=red>SCRIPT NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Script name, description and text must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="UPDATE vicidial_scripts set script_name='$script_name', script_comments='$script_comments', script_text='$script_text', active='$active' where script_id='$script_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=navy>SCRIPT MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY SCRIPT ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3111111;	# go to script modification form below
}


######################
# ADD=41111111 modify filter in the system
######################

if ($ADD==41111111)
{
	if ($LOGmodify_filters==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($lead_filter_id) < 2) or (strlen($lead_filter_name) < 2) or (strlen($lead_filter_sql) < 2) )
		{
		 echo "<br><font color=red>FILTER NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID, name and SQL must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="UPDATE vicidial_lead_filters set lead_filter_name='$lead_filter_name', lead_filter_comments='$lead_filter_comments', lead_filter_sql='$lead_filter_sql' where lead_filter_id='$lead_filter_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=navy>FILTER MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY FILTER ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=31111111;	# go to filter modification form below
}


######################
# ADD=411111111 modify call time in the system
######################

if ($ADD==411111111)
{
	if ($LOGmodify_call_times==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($call_time_id) < 2) or (strlen($call_time_name) < 2) )
		{
		 echo "<br><font color=red>CALL TIME NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID and name must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$ct_default_start = preg_replace('/\D/', '', $ct_default_start);
		$ct_default_stop = preg_replace('/\D/', '', $ct_default_stop);
		$ct_sunday_start = preg_replace('/\D/', '', $ct_sunday_start);
		$ct_sunday_stop = preg_replace('/\D/', '', $ct_sunday_stop);
		$ct_monday_start = preg_replace('/\D/', '', $ct_monday_start);
		$ct_monday_stop = preg_replace('/\D/', '', $ct_monday_stop);
		$ct_tuesday_start = preg_replace('/\D/', '', $ct_tuesday_start);
		$ct_tuesday_stop = preg_replace('/\D/', '', $ct_tuesday_stop);
		$ct_wednesday_start = preg_replace('/\D/', '', $ct_wednesday_start);
		$ct_wednesday_stop = preg_replace('/\D/', '', $ct_wednesday_stop);
		$ct_thursday_start = preg_replace('/\D/', '', $ct_thursday_start);
		$ct_thursday_stop = preg_replace('/\D/', '', $ct_thursday_stop);
		$ct_friday_start = preg_replace('/\D/', '', $ct_friday_start);
		$ct_friday_stop = preg_replace('/\D/', '', $ct_friday_stop);
		$ct_saturday_start = preg_replace('/\D/', '', $ct_saturday_start);
		$ct_saturday_stop = preg_replace('/\D/', '', $ct_saturday_stop);
		$stmt="UPDATE vicidial_call_times set call_time_name='$call_time_name', call_time_comments='$call_time_comments', ct_default_start='$ct_default_start', ct_default_stop='$ct_default_stop', ct_sunday_start='$ct_sunday_start', ct_sunday_stop='$ct_sunday_stop', ct_monday_start='$ct_monday_start', ct_monday_stop='$ct_monday_stop', ct_tuesday_start='$ct_tuesday_start', ct_tuesday_stop='$ct_tuesday_stop', ct_wednesday_start='$ct_wednesday_start', ct_wednesday_stop='$ct_wednesday_stop', ct_thursday_start='$ct_thursday_start', ct_thursday_stop='$ct_thursday_stop', ct_friday_start='$ct_friday_start', ct_friday_stop='$ct_friday_stop', ct_saturday_start='$ct_saturday_start', ct_saturday_stop='$ct_saturday_stop' where call_time_id='$call_time_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=navy>CALL TIME MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY CALL TIME ENTRY      |$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=navy>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111;	# go to call time modification form below
}


######################
# ADD=4111111111 modify state call time in the system
######################

if ($ADD==4111111111)
{
	if ($LOGmodify_call_times==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($call_time_id) < 2) or (strlen($call_time_name) < 2) or (strlen($state_call_time_state) < 2) )
		{
		 echo "<br><font color=red>STATE CALL TIME NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>State Call Time ID, name and state must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$ct_default_start = preg_replace('/\D/', '', $ct_default_start);
		$ct_default_stop = preg_replace('/\D/', '', $ct_default_stop);
		$ct_sunday_start = preg_replace('/\D/', '', $ct_sunday_start);
		$ct_sunday_stop = preg_replace('/\D/', '', $ct_sunday_stop);
		$ct_monday_start = preg_replace('/\D/', '', $ct_monday_start);
		$ct_monday_stop = preg_replace('/\D/', '', $ct_monday_stop);
		$ct_tuesday_start = preg_replace('/\D/', '', $ct_tuesday_start);
		$ct_tuesday_stop = preg_replace('/\D/', '', $ct_tuesday_stop);
		$ct_wednesday_start = preg_replace('/\D/', '', $ct_wednesday_start);
		$ct_wednesday_stop = preg_replace('/\D/', '', $ct_wednesday_stop);
		$ct_thursday_start = preg_replace('/\D/', '', $ct_thursday_start);
		$ct_thursday_stop = preg_replace('/\D/', '', $ct_thursday_stop);
		$ct_friday_start = preg_replace('/\D/', '', $ct_friday_start);
		$ct_friday_stop = preg_replace('/\D/', '', $ct_friday_stop);
		$ct_saturday_start = preg_replace('/\D/', '', $ct_saturday_start);
		$ct_saturday_stop = preg_replace('/\D/', '', $ct_saturday_stop);
		$stmt="UPDATE vicidial_state_call_times set state_call_time_name='$call_time_name', state_call_time_comments='$call_time_comments', sct_default_start='$ct_default_start', sct_default_stop='$ct_default_stop', sct_sunday_start='$ct_sunday_start', sct_sunday_stop='$ct_sunday_stop', sct_monday_start='$ct_monday_start', sct_monday_stop='$ct_monday_stop', sct_tuesday_start='$ct_tuesday_start', sct_tuesday_stop='$ct_tuesday_stop', sct_wednesday_start='$ct_wednesday_start', sct_wednesday_stop='$ct_wednesday_stop', sct_thursday_start='$ct_thursday_start', sct_thursday_stop='$ct_thursday_stop', sct_friday_start='$ct_friday_start', sct_friday_stop='$ct_friday_stop', sct_saturday_start='$ct_saturday_start', sct_saturday_stop='$ct_saturday_stop', state_call_time_state='$state_call_time_state'  where state_call_time_id='$call_time_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=navy>STATE CALL TIME MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY STATE CALL TIME ENTRY|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3111111111;	# go to state call time modification form below
}


######################
# ADD=41111111111 modify phone record in the system
######################

if ($ADD==41111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT count(*) from phones where extension='$extension' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ( ($row[0] > 0) && ( ($extension != $old_extension) or ($server_ip != $old_server_ip) ) )
		{echo "<br><font color=red>PHONE NOT MODIFIED - there is already a Phone in the system with this extension/server</font>\n";}
	else
		{
			 if ( (strlen($extension) < 1) or (strlen($server_ip) < 7) or (strlen($dialplan_number) < 1) or (strlen($voicemail_id) < 1) or (strlen($login) < 1)  or (strlen($pass) < 1))
			{echo "<br><font color=navy>PHONE NOT MODIFIED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=navy>PHONE MODIFIED: $extension</font>\n";

			$stmt="UPDATE phones set extension='$extension', dialplan_number='$dialplan_number', voicemail_id='$voicemail_id', phone_ip='$phone_ip', computer_ip='$computer_ip', server_ip='$server_ip', login='$login', pass='$pass', status='$status', active='$active', phone_type='$phone_type', fullname='$fullname', company='$company', picture='$picture', protocol='$protocol', local_gmt='$local_gmt', ASTmgrUSERNAME='$ASTmgrUSERNAME', ASTmgrSECRET='$ASTmgrSECRET', login_user='$login_user', login_pass='$login_pass', login_campaign='$login_campaign', park_on_extension='$park_on_extension', conf_on_extension='$conf_on_extension', VICIDIAL_park_on_extension='$VICIDIAL_park_on_extension', VICIDIAL_park_on_filename='$VICIDIAL_park_on_filename', monitor_prefix='$monitor_prefix', recording_exten='$recording_exten', voicemail_exten='$voicemail_exten', voicemail_dump_exten='$voicemail_dump_exten', ext_context='$ext_context', dtmf_send_extension='$dtmf_send_extension', call_out_number_group='$call_out_number_group', client_browser='$client_browser', install_directory='$install_directory', local_web_callerID_URL='" . mysql_real_escape_string($local_web_callerID_URL) . "', VICIDIAL_web_URL='" . mysql_real_escape_string($VICIDIAL_web_URL) . "', AGI_call_logging_enabled='$AGI_call_logging_enabled', user_switching_enabled='$user_switching_enabled', conferencing_enabled='$conferencing_enabled', admin_hangup_enabled='$admin_hangup_enabled', admin_hijack_enabled='$admin_hijack_enabled', admin_monitor_enabled='$admin_monitor_enabled', call_parking_enabled='$call_parking_enabled', updater_check_enabled='$updater_check_enabled', AFLogging_enabled='$AFLogging_enabled', QUEUE_ACTION_enabled='$QUEUE_ACTION_enabled', CallerID_popup_enabled='$CallerID_popup_enabled', voicemail_button_enabled='$voicemail_button_enabled', enable_fast_refresh='$enable_fast_refresh', fast_refresh_rate='$fast_refresh_rate', enable_persistant_mysql='$enable_persistant_mysql', auto_dial_next_number='$auto_dial_next_number', VDstop_rec_after_each_call='$VDstop_rec_after_each_call', DBX_server='$DBX_server', DBX_database='$DBX_database', DBX_user='$DBX_user', DBX_pass='$DBX_pass', DBX_port='$DBX_port', DBY_server='$DBY_server', DBY_database='$DBY_database', DBY_user='$DBY_user', DBY_pass='$DBY_pass', DBY_port='$DBY_port', outbound_cid='$outbound_cid', enable_sipsak_messages='$enable_sipsak_messages' where extension='$old_extension' and server_ip='$old_server_ip';";
			$rslt=mysql_query($stmt, $link);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=31111111111;	# go to phone modification form below
}


######################
# ADD=411111111111 modify server record in the system
######################

if ($ADD==411111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT count(*) from servers where server_id='$server_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ( ($row[0] > 0) && ($server_id != $old_server_id) )
		{echo "<br><font color=red>SERVER NOT MODIFIED - there is already a server in the system with this server_id</font>\n";}
	else
		{
		$stmt="SELECT count(*) from servers where server_ip='$server_ip';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ( ($row[0] > 0) && ($server_ip != $old_server_ip) )
			{echo "<br><font color=red>SERVER NOT MODIFIED - there is already a server in the system with this server_ip</font>\n";}
		else
			{
			 if ( (strlen($server_id) < 1) or (strlen($server_ip) < 7) )
				{echo "<br><font color=red>SERVER NOT MODIFIED - Please go back and look at the data you entered</font>\n";}
			 else
				{
				echo "<br><font color=navy>SERVER MODIFIED: $server_ip</font>\n";

				$stmt="UPDATE servers set server_id='$server_id',server_description='$server_description',server_ip='$server_ip',active='$active',asterisk_version='$asterisk_version', max_vicidial_trunks='$max_vicidial_trunks', telnet_host='$telnet_host', telnet_port='$telnet_port', ASTmgrUSERNAME='$ASTmgrUSERNAME', ASTmgrSECRET='$ASTmgrSECRET', ASTmgrUSERNAMEupdate='$ASTmgrUSERNAMEupdate', ASTmgrUSERNAMElisten='$ASTmgrUSERNAMElisten', ASTmgrUSERNAMEsend='$ASTmgrUSERNAMEsend', local_gmt='$local_gmt', voicemail_dump_exten='$voicemail_dump_exten', answer_transfer_agent='$answer_transfer_agent', ext_context='$ext_context', sys_perf_log='$sys_perf_log', vd_server_logs='$vd_server_logs', agi_output='$agi_output', vicidial_balance_active='$vicidial_balance_active', balance_trunks_offlimits='$balance_trunks_offlimits' where server_id='$old_server_id';";
				$rslt=mysql_query($stmt, $link);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111111;	# go to server modification form below
}


######################
# ADD=421111111111 modify vicidial server trunks record in the system
######################

if ($ADD==421111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT max_vicidial_trunks from servers where server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	$MAXvicidial_trunks = $rowx[0];
	
	$stmt="SELECT sum(dedicated_trunks) from vicidial_server_trunks where server_ip='$server_ip' and campaign_id !='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	$SUMvicidial_trunks = ($rowx[0] + $dedicated_trunks);
	
	if ($SUMvicidial_trunks > $MAXvicidial_trunks)
		{
		echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT ADDED - the number of OSDial trunks is too high: $SUMvicidial_trunks / $MAXvicidial_trunks</font>\n";
		}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($server_ip) < 7) or (strlen($dedicated_trunks) < 1) or (strlen($trunk_restriction) < 1) )
			{
			 echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>campaign must be between 3 and 8 characters in length\n";
			 echo "<br>server_ip delay must be at least 7 characters\n";
			 echo "<br>trunks must be a digit from 0 to 9999</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>OSDial SERVER TRUNK RECORD MODIFIED: $campaign_id - $server_ip - $dedicated_trunks - $trunk_restriction</font></B>\n";

			$stmt="UPDATE vicidial_server_trunks SET dedicated_trunks='$dedicated_trunks',trunk_restriction='$trunk_restriction' where campaign_id='$campaign_id' and server_ip='$server_ip';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY SERVER TRUNK   |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111111;	# go to server modification form below
}


######################
# ADD=4111111111111 modify conference record in the system
######################

if ($ADD==4111111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT count(*) from conferences where conf_exten='$conf_exten' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ( ($row[0] > 0) && ( ($conf_exten != $old_conf_exten) or ($server_ip != $old_server_ip) ) )
		{echo "<br><font color=red>CONFERENCE NOT MODIFIED - there is already a Conference in the system with this extension-server</font>\n";}
	else
		{
		 if ( (strlen($conf_exten) < 1) or (strlen($server_ip) < 7) )
			{echo "<br><font color=red>CONFERENCE NOT MODIFIED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=navy>CONFERENCE MODIFIED: $conf_exten</font>\n";

			$stmt="UPDATE conferences set conf_exten='$conf_exten',server_ip='$server_ip',extension='$extension' where conf_exten='$old_conf_exten';";
			$rslt=mysql_query($stmt, $link);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3111111111111;	# go to conference modification form below
}


######################
# ADD=41111111111111 modify vicidial conference record in the system
######################

if ($ADD==41111111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT count(*) from vicidial_conferences where conf_exten='$conf_exten' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ( ($row[0] > 0) && ( ($conf_exten != $old_conf_exten) or ($server_ip != $old_server_ip) ) )
		{echo "<br><font color=red>OSDial CONFERENCE NOT MODIFIED - there is already a Conference in the system with this extension-server</font>\n";}
	else
		{
		 if ( (strlen($conf_exten) < 1) or (strlen($server_ip) < 7) )
			{echo "<br><font color=red>OSDial CONFERENCE NOT MODIFIED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=navy>OSDial CONFERENCE MODIFIED: $conf_exten</font>\n";

			$stmt="UPDATE vicidial_conferences set conf_exten='$conf_exten',server_ip='$server_ip',extension='$extension' where conf_exten='$old_conf_exten';";
			$rslt=mysql_query($stmt, $link);

			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=31111111111111;	# go to vicidial conference modification form below
}



######################
# ADD=411111111111111 modify vicidial system settings
######################

if ($ADD==411111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<br><font color=navy>OSDial SYSTEM SETTINGS MODIFIED</font>\n";

	$stmt="UPDATE system_settings set use_non_latin='$use_non_latin',webroot_writable='$webroot_writable',enable_queuemetrics_logging='$enable_queuemetrics_logging',queuemetrics_server_ip='$queuemetrics_server_ip',queuemetrics_dbname='$queuemetrics_dbname',queuemetrics_login='$queuemetrics_login',queuemetrics_pass='$queuemetrics_pass',queuemetrics_url='$queuemetrics_url',queuemetrics_log_id='$queuemetrics_log_id',queuemetrics_eq_prepend='$queuemetrics_eq_prepend',vicidial_agent_disable='$vicidial_agent_disable',allow_sipsak_messages='$allow_sipsak_messages',admin_home_url='$admin_home_url',enable_agc_xfer_log='$enable_agc_xfer_log';";
	$rslt=mysql_query($stmt, $link);

	### LOG CHANGES TO LOG FILE ###
	if ($WeBRooTWritablE > 0)
		{
		$fp = fopen ("./admin_changes_log.txt", "a");
		fwrite ($fp, "$date|MODIFY SYSTEM SETTINGS|$PHP_AUTH_USER|$ip|$stmt|\n");
		fclose($fp);
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111111111;	# go to vicidial system settings form below
}

######################
# ADD=499111111111111 modify archive serversettings
######################

if ($ADD==499111111111111) {
	if ($LOGmodify_servers==1) {

		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=navy SIZE=2>";
		echo "<br>OSDial ARCHIVE SERVER MODIFIED\n";

		if ($archive_transfer_method == "FTP" and $archive_port == "") {
			$archive_port = "21";
		} elseif ($archive_transfer_method == "SFTP" and $archive_port == "") {
			$archive_port = "22";
		} elseif ($archive_transfer_method == "SCP" and $archive_port == "") {
			$archive_port = "22";
		}

		$stmt1 = "UPDATE configuration SET data='$archive_hostname' WHERE name='ArchiveHostname';";
		$stmt2 = "UPDATE configuration SET data='$archive_transfer_method' WHERE name='ArchiveTransferMethod';";
		$stmt3 = "UPDATE configuration SET data='$archive_port' WHERE name='ArchivePort';";
		$stmt4 = "UPDATE configuration SET data='$archive_username' WHERE name='ArchiveUsername';";
		$stmt5 = "UPDATE configuration SET data='$archive_password' WHERE name='ArchivePassword';";
		$stmt6 = "UPDATE configuration SET data='$archive_path' WHERE name='ArchivePath';";
		$stmt7 = "UPDATE configuration SET data='$archive_web_path' WHERE name='ArchiveWebPath';";
		$stmt8 = "UPDATE configuration SET data='$archive_mix_format' WHERE name='ArchiveMixFormat';";

		$rslt = mysql_query($stmt1, $link);
		$rslt = mysql_query($stmt2, $link);
		$rslt = mysql_query($stmt3, $link);
		$rslt = mysql_query($stmt4, $link);
		$rslt = mysql_query($stmt5, $link);
		$rslt = mysql_query($stmt6, $link);
		$rslt = mysql_query($stmt7, $link);
		$rslt = mysql_query($stmt8, $link);

	### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0) {
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt1|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt2|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt3|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt4|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt5|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt6|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt7|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt8|\n");
			fclose($fp);
		}
	} else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
		exit;
	}
	$ADD=399111111111111;	# go to vicidial system settings form below
}


######################
# ADD=499211111111111 modify qc serversettings
######################

if ($ADD==499211111111111) {
	if ($LOGmodify_servers==1) {

		if (($qc_server_transfer_type == "BATCH" or $qc_server_transfer_type == "ARCHIVE") and $qc_server_batch_time == "0") $qc_server_batch_time="23";
		if ($qc_server_transfer_type == "ARCHIVE" and $qc_server_archive == "NONE") $qc_server_archive="ZIP";
		if ($qc_server_transfer_type == "IMMEDIATE" or $qc_server_transfer_type == "BATCH") $qc_server_archive="NONE";
		if ($qc_server_transfer_type == "IMMEDIATE") $qc_server_batch_time="0";

		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=navy SIZE=2>";
		if ($SUB==1) {
			$qcact = "ADD";
			echo "<br>OSDial QC SERVER ADDED\n";

			$stmt = "INSERT INTO qc_servers (name,description,transfer_method,host,transfer_type,batch_time,username,password,home_path,location_template,archive,active) ";
			$stmt .= "VALUES ('$qc_server_name','$qc_server_description','$qc_server_transfer_method','$qc_server_host','$qc_server_transfer_type','$qc_server_batch_time',";
			$stmt .= "'$qc_server_username','$qc_server_password','$qc_server_home_path','$qc_server_location_template','$qc_server_archive','$qc_server_active');";

		} elseif ($SUB==2) {
			$qcact = "MODIFIED";
			echo "<br>OSDial QC SERVER MODIFIED\n";

			$stmt = "UPDATE qc_servers SET name='$qc_server_name',description='$qc_server_description',transfer_method='$qc_server_transfer_method',";
			$stmt .= "host='$qc_server_host',transfer_type='$qc_server_transfer_type',batch_time='$qc_server_batch_time',username='$qc_server_username',password='$qc_server_password',";
			$stmt .= "home_path='$qc_server_home_path',location_template='$qc_server_location_template',archive='$qc_server_archive',active='$qc_server_active' ";
			$stmt .= "WHERE id='$qc_server_id';";

		} elseif ($SUB==3) {
			$qcact = "ADD RULE";
			echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
			echo "<br>OSDial QC SERVER RULE MODIFIED\n";

			$stmt = "INSERT INTO qc_server_rules (qc_server_id,query) VALUES ('$qc_server_id','" . mysql_real_escape_string($qc_server_rule_query) . "');";
		} elseif ($SUB==4) {
			$qcact = "MODIFIED RULE";
			echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
			echo "<br>OSDial QC SERVER RULE MODIFIED\n";

			$stmt = "UPDATE qc_server_rules SET query='" . mysql_real_escape_string($qc_server_rule_query) ."' WHERE id='$qc_server_rule_id';";
			$qc_server_rule_query = "";
			$SUB=2;
		}
		$rslt = mysql_query($stmt, $link);
		if ($SUB==1) {
			$stmt = "SELECT id FROM qc_servers ORDER BY id DESC LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$qc_server_id=$row[0];
			$SUB++;
		}

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0) {
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|$qcact QC SERVER|$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
		}
	} else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
		exit;
	}
	$ADD=399211111111111;	# go to vicidial system settings form below
}


######################
# ADD=421111111111111 modify/delete system status in the system
######################

if ($ADD==421111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	if (ereg('delete',$stage))
		{
		if ( (strlen($status) < 1) or (preg_match("/^B$|^NA$|^DNC$|^NA$|^DROP$|^INCALL$|^QUEUE$|^NEW$/i",$status)) )
			{
			 echo "<br><font color=red>SYSTEM STATUS NOT DELETED - Please go back and look at the data you entered\n";
			 echo "<br>the system status cannot be a reserved status: B,NA,DNC,NA,DROP,INCALL,QUEUE,NEW\n";
			 echo "<br>the system status needs to be at least 1 characters in length</font><br>\n";
			}
		else
			{
			echo "<br><B><font color=navy>SYSTEM STATUS DELETED: $status</font></B>\n";

			$stmt="DELETE FROM vicidial_statuses where status='$status';";
			$rslt=mysql_query($stmt, $link);

			$stmtA="DELETE FROM vicidial_campaign_hotkeys where status='$status';";
			$rslt=mysql_query($stmtA, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DELETE SYSTEM STATUS  |$PHP_AUTH_USER|$ip|$stmt|$stmtA|\n");
				fclose($fp);
				}
			}
		}
	if (ereg('modify',$stage))
		{
		if ( (strlen($status) < 1) or (strlen($status_name) < 2) )
			{
			 echo "<br><font color=red>SYSTEM STATUS NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>the system status needs to be at least 1 characters in length\n";
			 echo "<br>the system status name needs to be at least 1 characters in length</font>\n";
			}
		else
			{
			echo "<br><B><font color=navy>SYSTEM STATUS MODIFIED: $status</font></B>\n";

			$stmt="UPDATE vicidial_statuses SET status_name='$status_name',selectable='$selectable',human_answered='$human_answered',category='$category' where status='$status';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY SYSTEM STATUS  |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=321111111111111;	# go to system settings modification form below
}


######################
# ADD=431111111111111 modify/delete status category in the system
######################

if ($ADD==431111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($vsc_id) < 2)  or (preg_match("/^UNDEFINED$/i",$vsc_id)) )
		{
		 echo "<br><font color=red>STATUS CATEGORY NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>the status category cannot be a reserved category: UNDEFINED\n";
		 echo "<br>the status category needs to be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		if (ereg('delete',$stage))
			{
			echo "<br><B><font color=navy>STATUS CATEGORY DELETED: $vsc_id</font></B>\n";

			$stmt="DELETE FROM vicidial_status_categories where vsc_id='$vsc_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DELETE STATUS CATEGORY|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		if (ereg('modify',$stage))
			{
			echo "<br><B><font color=navy>STATUS CATEGORY MODIFIED: $vsc_id</font></B>\n";

			$stmt="SELECT count(*) from vicidial_status_categories where tovdad_display='Y' and vsc_id NOT IN('$vsc_id');";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ( ($row[0] > 3) and (ereg('Y',$tovdad_display)) )
				{
				$tovdad_display = 'N';
				echo "<br><B><font color=red>ERROR: There are already 4 Status Categories set to Time On OSDial Display</font></B>\n";
				}

			$stmt="UPDATE vicidial_status_categories SET vsc_name='$vsc_name',vsc_description='$vsc_description',tovdad_display='$tovdad_display' where vsc_id='$vsc_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY STATUS CATEGORY|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=331111111111111;	# go to system settings modification form below
}




######################################################################################################
######################################################################################################
#######   5 series, delete records confirmation
######################################################################################################
######################################################################################################


######################
# ADD=5 confirmation before deletion of user
######################

if ($ADD==5)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($user) < 2) or ($LOGdelete_users < 1) )
		{
		 echo "<br><font color=red>USER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Agent be at least 2 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>USER DELETION CONFIRMATION: $user</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6&user=$user&CoNfIrM=YES\">Click here to delete user $user</a></font><br><br><br>\n";
		}

$ADD='3';		# go to user modification below
}

######################
# ADD=51 confirmation before deletion of campaign
######################

if ($ADD==51)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or ($LOGdelete_campaigns < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Campaign_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CAMPAIGN DELETION CONFIRMATION: $campaign_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61&campaign_id=$campaign_id&CoNfIrM=YES\">Click here to delete campaign $campaign_id</a></font><br><br><br>\n";
		}

$ADD='31';		# go to campaign modification below
}

######################
# ADD=52 confirmation before logging all agents out of campaign of campaign
######################

if ($ADD==52)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if (strlen($campaign_id) < 2)
		{
		 echo "<br><font color=red>AGENTS NOT LOGGED OUT OF CAMPAIGN - Please go back and look at the data you entered\n";
		 echo "<br>Campaign_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>AGENT LOGOUT CONFIRMATION: $campaign_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=62&campaign_id=$campaign_id&CoNfIrM=YES\">Click here to log all agents out of $campaign_id</a></font><br><br><br>\n";
		}

$ADD='31';		# go to campaign modification below
}

######################
# ADD=53 confirmation before Emergency VDAC Jam Clear - deletes oldest LIVE vicidial_auto_call record
######################

if ($ADD==53)
{
	if (eregi('IN',$stage))
		{$group_id=$campaign_id;}
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if (strlen($campaign_id) < 2)
		{
		 echo "<br><font color=red>VDAC NOT CLEARED FOR CAMPAIGN - Please go back and look at the data you entered\n";
		 echo "<br>Campaign_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>VDAC CLEAR CONFIRMATION: $campaign_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=63&campaign_id=$campaign_id&CoNfIrM=YES&&stage=$stage\">Click here to delete the oldest LIVE record in VDAC for $campaign_id</a></font><br><br><br>\n";
		}

# go to campaign modification below
if (eregi('IN',$stage))
	{$ADD='3111';}
else
	{$ADD='31';}	
}

######################
# ADD=511 confirmation before deletion of list
######################

if ($ADD==511)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($list_id) < 2) or ($LOGdelete_lists < 1) )
		{
		 echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>List_id be at least 2 characters in length</font>\n";
		}
	 else
		{
      if ($SUB==1) {
        echo "<br><B><font color=navy>LIST AND LEAD DELETION CONFIRMATION: $list_id</B>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=611&SUB=1&list_id=$list_id&CoNfIrM=YES\">Click here to delete list and all of its leads $list_id</a></font><br><br><br>\n";
      } else {
		    echo "<br><B><font color=navy>LIST DELETION CONFIRMATION: $list_id</B>\n";
		    echo "<br><br><a href=\"$PHP_SELF?ADD=611&list_id=$list_id&CoNfIrM=YES\">Click here to delete list $list_id</a></font><br><br><br>\n";
      }
		}

$ADD='311';		# go to campaign modification below
}

######################
# ADD=5111 confirmation before deletion of in-group
######################

if ($ADD==5111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($group_id) < 2) or ($LOGdelete_ingroups < 1) )
		{
		 echo "<br><font color=red>IN-GROUP NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Group_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>IN-GROUP DELETION CONFIRMATION: $group_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6111&group_id=$group_id&CoNfIrM=YES\">Click here to delete in-group $group_id</a></font><br><br><br>\n";
		}

$ADD='3111';		# go to in-group modification below
}

######################
# ADD=51111 confirmation before deletion of remote agent record
######################

if ($ADD==51111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($remote_agent_id) < 1) or ($LOGdelete_remote_agents < 1) )
		{
		 echo "<br><font color=red>REMOTE AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Remote_agent_id be at least 2 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>REMOTE AGENT DELETION CONFIRMATION: $remote_agent_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111&remote_agent_id=$remote_agent_id&CoNfIrM=YES\">Click here to delete remote agent $remote_agent_id</a></font><br><br><br>\n";
		}

$ADD='31111';		# go to remote agent modification below
}

######################
# ADD=511111 confirmation before deletion of user group record
######################

if ($ADD==511111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($user_group) < 2) or ($LOGdelete_user_groups < 1) )
		{
		 echo "<br><font color=red>USER GROUP NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>User_group be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>USER GROUP DELETION CONFIRMATION: $user_group</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=611111&user_group=$user_group&CoNfIrM=YES\">Click here to delete user group $user_group</a></font><br><br><br>\n";
		}

$ADD='311111';		# go to user group modification below
}

######################
# ADD=5111111 confirmation before deletion of script record
######################

if ($ADD==5111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($script_id) < 2) or ($LOGdelete_scripts < 1) )
		{
		 echo "<br><font color=red>SCRIPT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Script_id must be at least 2 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>SCRIPT DELETION CONFIRMATION: $script_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6111111&script_id=$script_id&CoNfIrM=YES\">Click here to delete script $script_id</a></font><br><br><br>\n";
		}

$ADD='3111111';		# go to script modification below
}

######################
# ADD=51111111 confirmation before deletion of filter record
######################

if ($ADD==51111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($lead_filter_id) < 2) or ($LOGdelete_filters < 1) )
		{
		 echo "<br><font color=red>FILTER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>FILTER DELETION CONFIRMATION: $lead_filter_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111111&lead_filter_id=$lead_filter_id&CoNfIrM=YES\">Click here to delete filter $lead_filter_id</a></font><br><br><br>\n";
		}

$ADD='31111111';		# go to filter modification below
}

######################
# ADD=511111111 confirmation before deletion of call time record
######################

if ($ADD==511111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($call_time_id) < 2) or ($LOGdelete_call_times < 1) )
		{
		 echo "<br><font color=red>CALL TIME NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID must be at least 2 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CALL TIME DELETION CONFIRMATION: $call_time_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=611111111&call_time_id=$call_time_id&CoNfIrM=YES\">Click here to delete call time $call_time_id</a></font><br><br><br>\n";
		}

$ADD='311111111';		# go to call time modification below
}

######################
# ADD=5111111111 confirmation before deletion of state call time record
######################

if ($ADD==5111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($call_time_id) < 2) or ($LOGdelete_call_times < 1) )
		{
		 echo "<br><font color=red>STATE CALL TIME NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>STATE CALL TIME DELETION CONFIRMATION: $call_time_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6111111111&call_time_id=$call_time_id&CoNfIrM=YES\">Click here to delete state call time $call_time_id</a></font><br><br><br>\n";
		}

$ADD='3111111111';		# go to state call time modification below
}


######################
# ADD=51111111111 confirmation before deletion of phone record
######################

if ($ADD==51111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($extension) < 2) or (strlen($server_ip) < 7) or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>PHONE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Extension be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>PHONE DELETION CONFIRMATION: $extension - $server_ip</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111111111&extension=$extension&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete phone $extension - $server_ip</a></font><br><br><br>\n";
		}
$ADD='31111111111';		# go to phone modification below
}


######################
# ADD=511111111111 confirmation before deletion of server record
######################

if ($ADD==511111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($server_id) < 2) or (strlen($server_ip) < 7) or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>SERVER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Server ID be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>SERVER DELETION CONFIRMATION: $server_id - $server_ip</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=611111111111&server_id=$server_id&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete phone $server_id - $server_ip</a></font><br><br><br>\n";
		}
$ADD='311111111111';		# go to server modification below
}


######################
# ADD=5111111111111 confirmation before deletion of conference record
######################

if ($ADD==5111111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($conf_exten) < 2) or (strlen($server_ip) < 7) or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>CONFERENCE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Conference must be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CONFERENCE DELETION CONFIRMATION: $conf_exten - $server_ip</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6111111111111&conf_exten=$conf_exten&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete phone $conf_exten - $server_ip</a></font><br><br><br>\n";
		}
$ADD='3111111111111';		# go to conference modification below
}


######################
# ADD=51111111111111 confirmation before deletion of vicidial conference record
######################

if ($ADD==51111111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($conf_exten) < 2) or (strlen($server_ip) < 7) or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>OSDial CONFERENCE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Conference must be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>OSDial CONFERENCE DELETION CONFIRMATION: $conf_exten - $server_ip</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111111111111&conf_exten=$conf_exten&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete phone $conf_exten - $server_ip</a></font><br><br><br>\n";
		}
$ADD='31111111111111';		# go to vicidial conference modification below
}



######################################################################################################
######################################################################################################
#######   6 series, delete records
######################################################################################################
######################################################################################################


######################
# ADD=6 delete user record
######################

if ($ADD==6)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( ( strlen($user) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_users < 1) )
		{
		 echo "<br><font color=red>AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Agent be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmtA="DELETE from vicidial_users where user='$user' limit 1;";
		$rslt=mysql_query($stmtA, $link);

		$stmt="DELETE from vicidial_campaign_agents where user='$user';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_inbound_group_agents where user='$user';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING AGENT!!!!|$PHP_AUTH_USER|$ip|$user|$stmtA|$stmt|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>AGENT DELETION COMPLETED: $user</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='0';		# go to user list
}

######################
# ADD=61 delete campaign record
######################

if ($ADD==61)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( ( strlen($campaign_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_campaigns < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Campaign_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_campaigns where campaign_id='$campaign_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_campaign_agents where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_live_agents where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_campaign_statuses where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_campaign_hotkeys where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_callbacks where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_campaign_stats where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_lead_recycle where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_campaign_server_stats where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_server_trunks where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_pause_codes where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_campaigns_list_mix where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><font color=navy>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($campaign_id)</font>\n";
		$stmt="DELETE from vicidial_hopper where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!DELETING CAMPAIGN!|$PHP_AUTH_USER|$ip|campaign_id='$campaign_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>CAMPAIGN DELETION COMPLETED: $campaign_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='10';		# go to campaigns list
}

######################
# ADD=62 Logout all agents from a campaign
######################

if ($ADD==62)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if (strlen($campaign_id) < 2)
		{
		 echo "<br><font color=red>AGENTS NOT LOGGED OUT OF CAMPAIGN - Please go back and look at the data you entered\n";
		 echo "<br>Campaign_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_live_agents where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!AGENT LOGOUT!!!!!!|$PHP_AUTH_USER|$ip|campaign_id='$campaign_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>AGENT LOGOUT COMPLETED: $campaign_id</font></B>\n";
		echo "<br><br>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD='31';		# go to campaign modification below
}


######################
# ADD=63 Emergency VDAC Jam Clear
######################

if ($ADD==63)
{
	if ($LOGmodify_campaigns==1)
	{
	if (eregi('IN',$stage))
		{$group_id=$campaign_id;}
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if (strlen($campaign_id) < 2)
		{
		 echo "<br><font color=red>VDAC NOT CLEARED FOR CAMPAIGN - Please go back and look at the data you entered\n";
		 echo "<br>Campaign_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_auto_calls where status='LIVE' and campaign_id='$campaign_id' order by call_time limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|EMERGENCY VDAC CLEAR|$PHP_AUTH_USER|$ip|campaign_id='$campaign_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>LAST VDAC RECORD CLEARED FOR CAMPAIGN: $campaign_id</font></B>\n";
		echo "<br><br>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
# go to campaign modification below
if (eregi('IN',$stage))
	{$ADD='3111';}
else
	{$ADD='31';}	
}


######################
# ADD=65 delete campaign lead recycle in the system
######################

if ($ADD==65)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN LEAD RECYCLE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>status must be between 1 and 6 characters in length\n";
		 echo "<br>attempt delay must be at least 120 seconds\n";
		 echo "<br>maximum attempts must be from 1 to 10</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CAMPAIGN LEAD RECYCLE DELETED: $campaign_id - $status - $attempt_delay</font></B>\n";

		$stmt="DELETE FROM vicidial_lead_recycle where campaign_id='$campaign_id' and status='$status';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|DELETE LEAD RECYCLE   |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=25;
$ADD=31;	# go to campaign modification form below
}


######################
# ADD=66 delete auto alt dial status from the campaign
######################

if ($ADD==66)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$campaign_id' and auto_alt_dial_statuses LIKE \"% $status %\";";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] < 1)
		{echo "<br><font color=red>AUTO ALT DIAL STATUS NOT DELETED - this auto alt dial status is not in this campaign</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
			{
			 echo "<br><font color=red>AUTO ALT DIAL STATUS NOT DELETED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>AUTO ALT DIAL STATUS DELETED: $campaign_id - $status</font></B>\n";

			$stmt="SELECT auto_alt_dial_statuses from vicidial_campaigns where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			$auto_alt_dial_statuses = eregi_replace(" $status "," ",$row[0]);
			$stmt="UPDATE vicidial_campaigns set auto_alt_dial_statuses='$auto_alt_dial_statuses' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DELETE AUTALTDIALSTTUS|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=26;
$ADD=31;	# go to campaign modification form below
}

######################
# ADD=67 delete agent pause code in the system
######################

if ($ADD==67)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($pause_code) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN PAUSE CODE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>pause code must be between 1 and 6 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CAMPAIGN PAUSE CODE DELETED: $campaign_id - $pause_code</font></B>\n";

		$stmt="DELETE FROM vicidial_pause_codes where campaign_id='$campaign_id' and pause_code='$pause_code';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|DELETE AGENT PAUSECODE|$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=27;
$ADD=31;	# go to campaign modification form below
}


######################
# ADD=68 remove campaign dial status
######################

if ($ADD==68)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$campaign_id' and dial_statuses LIKE \"% $status %\";";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] < 1)
		{echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT REMOVED - this dial status is not selected for this campaign</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
			{
			 echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT REMOVED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>CAMPAIGN DIAL STATUS REMOVED: $campaign_id - $status</font></B>\n";

			$stmt="SELECT dial_statuses from vicidial_campaigns where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			$dial_statuses = eregi_replace(" $status "," ",$row[0]);
			$stmt="UPDATE vicidial_campaigns set dial_statuses='$dial_statuses' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DIAL STATUS REMOVED   |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
#$SUB=28;
$ADD=31;	# go to campaign modification form below
}

######################
# ADD=611 delete list record and all leads within it
######################

if ($ADD==611)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( ( strlen($list_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_lists < 1) )
		{
		 echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>List_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_lists where list_id='$list_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		echo "<br><font color=navy>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($list_id)</font>\n";
		$stmt="DELETE from vicidial_hopper where list_id='$list_id';";
		$rslt=mysql_query($stmt, $link);

    if ($SUB==1) {
		  echo "<br><font color=navy>REMOVING LIST LEADS FROM OSDial_LIST TABLE</font>\n";
		  $stmt="DELETE from vicidial_list where list_id='$list_id';";
		  $rslt=mysql_query($stmt, $link);
    }

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING LIST!!!!|$PHP_AUTH_USER|$ip|list_id='$list_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>LIST DELETION COMPLETED: $list_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='100';		# go to lists list
}

######################
# ADD=6111 delete in-group record
######################

if ($ADD==6111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($group_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_ingroups < 1) )
		{
		 echo "<br><font color=red>IN-GROUP NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Group_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_inbound_groups where group_id='$group_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_inbound_group_agents where group_id='$group_id';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from vicidial_live_inbound_agents where group_id='$group_id';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING IN-GROUP!!|$PHP_AUTH_USER|$ip|group_id='$group_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>IN-GROUP DELETION COMPLETED: $group_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='1000';		# go to in-group list
}

######################
# ADD=61111 delete remote agent record
######################

if ($ADD==61111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($remote_agent_id) < 1) or ($CoNfIrM != 'YES') or ($LOGdelete_remote_agents < 1) )
		{
		 echo "<br><font color=red>REMOTE AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Remote_agent_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_remote_agents where remote_agent_id='$remote_agent_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING RMTAGENT!!|$PHP_AUTH_USER|$ip|remote_agent_id='$remote_agent_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>REMOTE AGENT DELETION COMPLETED: $remote_agent_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='10000';		# go to remote agents list
}

######################
# ADD=611111 delete user group record
######################

if ($ADD==611111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($user_group) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_user_groups < 1) )
		{
		 echo "<br><font color=red>USER GROUP NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>User_group be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_user_groups where user_group='$user_group' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING USRGROUP!!|$PHP_AUTH_USER|$ip|user_group='$user_group'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>USER GROUP DELETION COMPLETED: $user_group</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='100000';		# go to user group list
}

######################
# ADD=6111111 delete script record
######################

if ($ADD==6111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($script_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_scripts < 1) )
		{
		 echo "<br><font color=red>SCRIPT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Script_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_scripts where script_id='$script_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING SCRIPT!!!!|$PHP_AUTH_USER|$ip|script_id='$script_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>SCRIPT DELETION COMPLETED: $script_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='1000000';		# go to script list
}


######################
# ADD=61111111 delete filter record
######################

if ($ADD==61111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($lead_filter_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_filters < 1) )
		{
		 echo "<br><font color=red>FILTER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_lead_filters where lead_filter_id='$lead_filter_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING FILTER!!!!|$PHP_AUTH_USER|$ip|lead_filter_id='$lead_filter_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>FILTER DELETION COMPLETED: $lead_filter_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='10000000';		# go to filter list
}


######################
# ADD=611111111 delete call times record
######################

if ($ADD==611111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($call_time_id) < 2) or ($LOGdelete_call_times < 1) )
		{
		 echo "<br><font color=red>CALL TIME NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_call_times where call_time_id='$call_time_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING CALL TIME!|$PHP_AUTH_USER|$ip|call_time_id='$call_time_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>CALL TIME DELETION COMPLETED: $call_time_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='100000000';		# go to call times list
}


######################
# ADD=6111111111 delete state call times record
######################

if ($ADD==6111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($call_time_id) < 2) or ($LOGdelete_call_times < 1) )
		{
		 echo "<br><font color=red>STATE CALL TIME NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_state_call_times where state_call_time_id='$call_time_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		$stmt="SELECT call_time_id,ct_state_call_times from vicidial_call_times where ct_state_call_times LIKE \"%|$call_time_id|%\" order by call_time_id;";
		$rslt=mysql_query($stmt, $link);
		$sct_to_print = mysql_num_rows($rslt);
		$sct_list='';

		$o=0;
		while ($sct_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$sct_ids[$o] = "$rowx[0]";
			$sct_states[$o] = "$rowx[1]";
			$o++;
		}
		$o=0;

		while ($sct_to_print > $o) {
			$sct_states[$o] = eregi_replace("\|$call_time_id\|",'|',$sct_states[$o]);
			$stmt="UPDATE vicidial_call_times set ct_state_call_times='$sct_states[$o]' where call_time_id='$sct_ids[$o]';";
			$rslt=mysql_query($stmt, $link);
			echo "$stmt\n";
			echo "State Rule Removed: $sct_ids[$o]<BR>\n";
			$o++;
		}

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING CALL TIME!|$PHP_AUTH_USER|$ip|state_call_time_id='$call_time_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>STATE CALL TIME DELETION COMPLETED: $call_time_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='1000000000';		# go to call times list
}


######################
# ADD=61111111111 delete phone record
######################

if ($ADD==61111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($extension) < 2) or (strlen($server_ip) < 7) or ($CoNfIrM != 'YES') or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>PHONE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Extension be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from phones where extension='$extension' and server_ip='$server_ip' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING PHONE!!!|$PHP_AUTH_USER|$ip|extension='$extension'|server_ip='$server_ip'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>PHONE DELETION COMPLETED: $extension - $server_ip</font></B>\n";
		echo "<br><br>\n";
		}
$ADD='10000000000';		# go to phone list
}


######################
# ADD=611111111111 delete server record
######################

if ($ADD==611111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($server_id) < 2) or (strlen($server_ip) < 7) or ($CoNfIrM != 'YES') or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>SERVER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Server ID be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from servers where server_id='$server_id' and server_ip='$server_ip' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING SERVER!!|$PHP_AUTH_USER|$ip|server_id='$server_id'|server_ip='$server_ip'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>SERVER DELETION COMPLETED: $server_id - $server_ip</font></B>\n";
		echo "<br><br>\n";
		}
$ADD='100000000000';		# go to server list
}


######################
# ADD=621111111111 delete vicidial server trunk record in the system
######################

if ($ADD==621111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($server_ip) < 7) )
		{
		 echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>campaign must be between 3 and 8 characters in length\n";
		 echo "<br>server_ip delay must be at least 7 characters</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>OSDial SERVER TRUNK RECORD DELETED: $campaign_id - $server_ip</font></B>\n";

		$stmt="DELETE FROM vicidial_server_trunks where campaign_id='$campaign_id' and server_ip='$server_ip';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|DELETE SERVER TRUNK   |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111111;	# go to server modification form below
}


######################
# ADD=6111111111111 delete conference record
######################

if ($ADD==6111111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($conf_exten) < 2) or (strlen($server_ip) < 7) or ($CoNfIrM != 'YES') or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>CONFERENCE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Conference be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from conferences where conf_exten='$conf_exten' and server_ip='$server_ip' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING CONF!!!!|$PHP_AUTH_USER|$ip|conf_exten='$conf_exten'|server_ip='$server_ip'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>CONFERENCE DELETION COMPLETED: $conf_exten - $server_ip</font></B>\n";
		echo "<br><br>\n";
		}
$ADD='1000000000000';		# go to conference list
}


######################
# ADD=61111111111111 delete vicidial conference record
######################

if ($ADD==61111111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($conf_exten) < 2) or (strlen($server_ip) < 7) or ($CoNfIrM != 'YES') or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>OSDial CONFERENCE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Conference be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_conferences where conf_exten='$conf_exten' and server_ip='$server_ip' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING CONF!!!!|$PHP_AUTH_USER|$ip|conf_exten='$conf_exten'|server_ip='$server_ip'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>OSDial CONFERENCE DELETION COMPLETED: $conf_exten - $server_ip</font></B>\n";
		echo "<br><br>\n";
		}
$ADD='10000000000000';		# go to vicidial conference list
}

######################
# ADD=699211111111111 delete qc server and sql records.
######################

if ($ADD==699211111111111){
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	if ($SUB==2) {
		# Delete rule entries
		$stmt="DELETE from qc_server_rules where qc_server_id='$qc_server_id';";
		$rslt=mysql_query($stmt, $link);
		
		# Delete server entry
		$stmt="DELETE from qc_servers where id='$qc_server_id';";
		$rslt=mysql_query($stmt, $link);
		$nQSI='';
		$nSUB='';
	} elseif ($SUB==4) {
		# Delete rule entry
		$stmt="DELETE from qc_server_rules where id='$qc_server_rule_id';";
		$rslt=mysql_query($stmt, $link);
		$nQSI=$qc_server_id;
		$nSUB=2;
	}

	### LOG CHANGES TO LOG FILE ###
	if ($SUB > 0 and $WeBRooTWritablE > 0) {
		$fp = fopen ("./admin_changes_log.txt", "a");
		fwrite ($fp, "$date|!!!DELETING QC!!!!|$PHP_AUTH_USER|$ip|SUB=$SUB|qc_server_id='$qc_server_id'|qc_server_rule_id='$qc_server_rule_id'|\n");
		fclose($fp);
	}
	echo "<br><B>OSDial QC DELETION COMPLETED: $qc_server_id - $qc_server_rule_id</B>\n";
	echo "<br><br>\n";

	$SUB=$nSUB;
	$qc_server_id=$nQSI;
	$qc_server_rule_id='';
	$ADD='399211111111111';		# go to vicidial conference list
}




######################################################################################################
######################################################################################################
#######   3 series, record modification forms
######################################################################################################
######################################################################################################




######################
# ADD=3 modify user info in the system
######################

if ($ADD==3)
{
	if ($LOGmodify_users==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$user_level =			$row[4];
	$user_group =			$row[5];
	$phone_login =			$row[6];
	$phone_pass =			$row[7];
	$delete_users =			$row[8];
	$delete_user_groups =		$row[9];
	$delete_lists =			$row[10];
	$delete_campaigns =		$row[11];
	$delete_ingroups =		$row[12];
	$delete_remote_agents =		$row[13];
	$load_leads =			$row[14];
	$campaign_detail =		$row[15];
	$ast_admin_access =		$row[16];
	$ast_delete_phones =		$row[17];
	$delete_scripts =		$row[18];
	$modify_leads =			$row[19];
	$hotkeys_active =		$row[20];
	$change_agent_campaign =	$row[21];
	$agent_choose_ingroups =	$row[22];
	$scheduled_callbacks =		$row[24];
	$agentonly_callbacks =		$row[25];
	$agentcall_manual =		$row[26];
	$vicidial_recording =		$row[27];
	$vicidial_transfers =		$row[28];
	$delete_filters =		$row[29];
	$alter_agent_interface_options =$row[30];
	$closer_default_blended =	$row[31];
	$delete_call_times =		$row[32];
	$modify_call_times =		$row[33];
	$modify_users =			$row[34];
	$modify_campaigns =		$row[35];
	$modify_lists =			$row[36];
	$modify_scripts =		$row[37];
	$modify_filters =		$row[38];
	$modify_ingroups =		$row[39];
	$modify_usergroups =		$row[40];
	$modify_remoteagents =		$row[41];
	$modify_servers =		$row[42];
	$view_reports =			$row[43];
	$vicidial_recording_override =	$row[44];
	$alter_custdata_override = 	$row[45];

	if ( ($user_level >= $LOGuser_level) and ($LOGuser_level < 9) )
		{
		echo "<br><font color=red>You do not have permissions to modify this user: $row[1]</font>\n";
		}
	else
		{
		echo "<center><br><font color=navy size=+1>MODIFY AN AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
		if ($LOGuser_level > 8)
			{echo "<input type=hidden name=ADD value=4A>\n";}
		else
			{
			if ($LOGalter_agent_interface == "1")
				{echo "<input type=hidden name=ADD value=4B>\n";}
			else
				{echo "<input type=hidden name=ADD value=4>\n";}
			}
		echo "<input type=hidden name=user value=\"$row[1]\">\n";
		echo "<TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Agent Number: </td><td align=left><b>$row[1]</b>$NWB#osdial_users-user$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10 value=\"$row[2]\">$NWB#osdial_users-pass$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=30 maxlength=30 value=\"$row[3]\">$NWB#osdial_users-full_name$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>User Level: </td><td align=left><select size=1 name=user_level>";
		$h=1;
		while ($h<=$LOGuser_level)
			{
			echo "<option>$h</option>";
			$h++;
			}
		echo "<option SELECTED>$row[4]</option></select>$NWB#osdial_users-user_level$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right><A HREF=\"$PHP_SELF?ADD=311111&user_group=$user_group\">User Group</A>: </td><td align=left><select size=1 name=user_group>\n";

			$stmt="SELECT user_group,group_name from vicidial_user_groups order by user_group";
			$rslt=mysql_query($stmt, $link);
			$Ugroups_to_print = mysql_num_rows($rslt);
			$Ugroups_list='';

			$o=0;
			while ($Ugroups_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$Ugroups_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				$o++;
			}
		echo "$Ugroups_list";
		echo "<option SELECTED>$user_group</option>\n";
		echo "</select>$NWB#osdial_users-user_group$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Phone Login: </td><td align=left><input type=text name=phone_login size=20 maxlength=20 value=\"$phone_login\">$NWB#osdial_users-phone_login$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Phone Pass: </td><td align=left><input type=text name=phone_pass size=20 maxlength=20 value=\"$phone_pass\">$NWB#osdial_users-phone_pass$NWE</td></tr>\n";

		if ( ($LOGuser_level > 8) or ($LOGalter_agent_interface == "1") )
			{
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr bgcolor=#015B91><td colspan=2 align=center><font color=white><B>AGENT INTERFACE OPTIONS:</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Agent Choose Ingroups: </td><td align=left><select size=1 name=agent_choose_ingroups><option>0</option><option>1</option><option SELECTED>$agent_choose_ingroups</option></select>$NWB#osdial_users-agent_choose_ingroups$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Hot Keys Active: </td><td align=left><select size=1 name=hotkeys_active><option>0</option><option>1</option><option SELECTED>$hotkeys_active</option></select>$NWB#osdial_users-hotkeys_active$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Scheduled Callbacks: </td><td align=left><select size=1 name=scheduled_callbacks><option>0</option><option>1</option><option SELECTED>$scheduled_callbacks</option></select>$NWB#osdial_users-scheduled_callbacks$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Agent-Only Callbacks: </td><td align=left><select size=1 name=agentonly_callbacks><option>0</option><option>1</option><option SELECTED>$agentonly_callbacks</option></select>$NWB#osdial_users-agentonly_callbacks$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Agent Call Manual: </td><td align=left><select size=1 name=agentcall_manual><option>0</option><option>1</option><option SELECTED>$agentcall_manual</option></select>$NWB#osdial_users-agentcall_manual$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Vicidial Recording: </td><td align=left><select size=1 name=vicidial_recording><option>0</option><option>1</option><option SELECTED>$vicidial_recording</option></select>$NWB#osdial_users-osdial_recording$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Vicidial Transfers: </td><td align=left><select size=1 name=vicidial_transfers><option>0</option><option>1</option><option SELECTED>$vicidial_transfers</option></select>$NWB#osdial_users-osdial_transfers$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Closer Default Blended: </td><td align=left><select size=1 name=closer_default_blended><option>0</option><option>1</option><option SELECTED>$closer_default_blended</option></select>$NWB#osdial_users-closer_default_blended$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Recording Override: </td><td align=left><select size=1 name=vicidial_recording_override><option>DISABLED</option><option>NEVER</option><option>ONDEMAND</option><option>ALLCALLS</option><option>ALLFORCE</option><option SELECTED>$vicidial_recording_override</option></select>$NWB#osdial_users-osdial_recording_override$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Agent Alter Customer Data Override: </td><td align=left><select size=1 name=alter_custdata_override><option>NOT_ACTIVE</option><option>ALLOW_ALTER</option><option SELECTED>$alter_custdata_override</option></select>$NWB#osdial_users-alter_custdata_override$NWE</td></tr>\n";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr bgcolor=#C1D6DF><td align=center colspan=2>Campaign Ranks: $NWB#osdial_users-campaign_ranks$NWE<BR>\n";
			echo "<table border=0>\n";
			echo "$RANKcampaigns_list";
			echo "</table>\n";
			echo "</td></tr>\n";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr bgcolor=#C1D6DF><td align=center colspan=2>Inbound Groups: $NWB#osdial_users-closer_campaigns$NWE<BR>\n";
			echo "<table border=0>\n";
			echo "$RANKgroups_list";
			echo "</table>\n";
			echo "</td></tr>\n";
			}
		if ($LOGuser_level > 8)
			{
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr bgcolor=#015B91><td colspan=2 align=center><font color=white><B>ADMIN INTERFACE OPTIONS:</td></tr>\n";

#C1D6DB
#CBDCE0
			echo "<tr bgcolor=#CBDCE0><td align=right>View Reports: </td><td align=left><select size=1 name=view_reports><option>0</option><option>1</option><option SELECTED>$view_reports</option></select>$NWB#osdial_users-view_reports$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>Alter Agent Interface Options: </td><td align=left><select size=1 name=alter_agent_interface_options><option>0</option><option>1</option><option SELECTED>$alter_agent_interface_options</option></select>$NWB#osdial_users-alter_agent_interface_options$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Modify Agents: </td><td align=left><select size=1 name=modify_users><option>0</option><option>1</option><option SELECTED>$modify_users</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Change Agent Campaign: </td><td align=left><select size=1 name=change_agent_campaign><option>0</option><option>1</option><option SELECTED>$change_agent_campaign</option></select>$NWB#osdial_users-change_agent_campaign$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete Agents: </td><td align=left><select size=1 name=delete_users><option>0</option><option>1</option><option SELECTED>$delete_users</option></select>$NWB#osdial_users-delete_users$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>Modify User Groups: </td><td align=left><select size=1 name=modify_usergroups><option>0</option><option>1</option><option SELECTED>$modify_usergroups</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete User Groups: </td><td align=left><select size=1 name=delete_user_groups><option>0</option><option>1</option><option SELECTED>$delete_user_groups</option></select>$NWB#osdial_users-delete_user_groups$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>Modify Lists: </td><td align=left><select size=1 name=modify_lists><option>0</option><option>1</option><option SELECTED>$modify_lists</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete Lists: </td><td align=left><select size=1 name=delete_lists><option>0</option><option>1</option><option SELECTED>$delete_lists</option></select>$NWB#osdial_users-delete_lists$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Load Leads: </td><td align=left><select size=1 name=load_leads><option>0</option><option>1</option><option SELECTED>$load_leads</option></select>$NWB#osdial_users-load_leads$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Modify Leads: </td><td align=left><select size=1 name=modify_leads><option>0</option><option>1</option><option SELECTED>$modify_leads</option></select>$NWB#osdial_users-modify_leads$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>Modify Campaigns: </td><td align=left><select size=1 name=modify_campaigns><option>0</option><option>1</option><option SELECTED>$modify_campaigns</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Campaign Detail: </td><td align=left><select size=1 name=campaign_detail><option>0</option><option>1</option><option SELECTED>$campaign_detail</option></select>$NWB#osdial_users-campaign_detail$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete Campaigns: </td><td align=left><select size=1 name=delete_campaigns><option>0</option><option>1</option><option SELECTED>$delete_campaigns</option></select>$NWB#osdial_users-delete_campaigns$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>Modify In-Groups: </td><td align=left><select size=1 name=modify_ingroups><option>0</option><option>1</option><option SELECTED>$modify_ingroups</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete In-Groups: </td><td align=left><select size=1 name=delete_ingroups><option>0</option><option>1</option><option SELECTED>$delete_ingroups</option></select>$NWB#osdial_users-delete_ingroups$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>Modify Remote Agents: </td><td align=left><select size=1 name=modify_remoteagents><option>0</option><option>1</option><option SELECTED>$modify_remoteagents</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete Remote Agents: </td><td align=left><select size=1 name=delete_remote_agents><option>0</option><option>1</option><option SELECTED>$delete_remote_agents</option></select>$NWB#osdial_users-delete_remote_agents$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>Modify Scripts: </td><td align=left><select size=1 name=modify_scripts><option>0</option><option>1</option><option SELECTED>$modify_scripts</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete Scripts: </td><td align=left><select size=1 name=delete_scripts><option>0</option><option>1</option><option SELECTED>$delete_scripts</option></select>$NWB#osdial_users-delete_scripts$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>Modify Filters: </td><td align=left><select size=1 name=modify_filters><option>0</option><option>1</option><option SELECTED>$modify_filters</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete Filters: </td><td align=left><select size=1 name=delete_filters><option>0</option><option>1</option><option SELECTED>$delete_filters</option></select>$NWB#osdial_users-delete_filters$NWE</td></tr>\n";

			echo "<tr bgcolor=#CBDCE0><td align=right>AGC Admin Access: </td><td align=left><select size=1 name=ast_admin_access><option>0</option><option>1</option><option SELECTED>$ast_admin_access</option></select>$NWB#osdial_users-ast_admin_access$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>AGC Delete Phones: </td><td align=left><select size=1 name=ast_delete_phones><option>0</option><option>1</option><option SELECTED>$ast_delete_phones</option></select>$NWB#osdial_users-ast_delete_phones$NWE</td></tr>\n";
			echo "<tr bgcolor=#B9CBFB><td align=right>Modify Call Times: </td><td align=left><select size=1 name=modify_call_times><option>0</option><option>1</option><option SELECTED>$modify_call_times</option></select>$NWB#osdial_users-modify_call_times$NWE</td></tr>\n";
			echo "<tr bgcolor=#CBDCE0><td align=right>Delete Call Times: </td><td align=left><select size=1 name=delete_call_times><option>0</option><option>1</option><option SELECTED>$delete_call_times</option></select>$NWB#osdial_users-delete_call_times$NWE</td></tr>\n";
			echo "<tr bgcolor=#B9CBFB><td align=right>Modify Servers: </td><td align=left><select size=1 name=modify_servers><option>0</option><option>1</option><option SELECTED>$modify_servers</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			}
		echo "<tr bgcolor=#CBDCE0><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
		echo "</TABLE></center>\n";

		echo "<center><br><br><a href=\"./AST_agent_time_sheet.php?agent=$row[1]\">Click here for user time sheet</a>\n";
		echo "<br><br><a href=\"./user_status.php?user=$row[1]\">Click here for user status</a>\n";
		echo "<br><br><a href=\"./user_stats.php?user=$row[1]\">Click here for user stats</a>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=8&user=$row[1]\">Click here for user CallBack Holds</a></center>\n";
		if ($LOGdelete_users > 0)
			{
			echo "<br><br><a href=\"$PHP_SELF?ADD=5&user=$row[1]\">DELETE THIS USER</a>\n";
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=31 modify campaign info in the system - Detail view
######################

if ( ($LOGcampaign_detail < 1) and ($ADD==31) ) {$ADD=34;}	# send to Basic if not allowed

if ( ($ADD==31) and ( (!eregi("$campaign_id",$LOGallowed_campaigns)) and (!eregi("ALL-CAMPAIGNS",$LOGallowed_campaigns)) ) ) 
	{$ADD=30;}	# send to not allowed screen if not in vicidial_user_groups allowed_campaigns list

if ($ADD==31)
{
	if ($LOGmodify_campaigns==1)
	{
		if ($stage=='show_dialable')
		{
			$stmt="UPDATE vicidial_campaigns set display_dialable_count='Y' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
		}
		if ($stage=='hide_dialable')
		{
			$stmt="UPDATE vicidial_campaigns set display_dialable_count='N' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
		}

		$stmt="SELECT * from vicidial_campaigns where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		//$park_ext = $row[0];
		$campaign_name = $row[1];
		//$active = $row[2];
		$dial_status_a = $row[3];
		$dial_status_b = $row[4];
		$dial_status_c = $row[5];
		$dial_status_d = $row[6];
		$dial_status_e = $row[7];
		$lead_order = $row[8];
		//$park_ext = $row[9];
		//$park_file_name = $row[10];
		$web_form_address = $row[11];
		$allow_closers = $row[12];
		$hopper_level = $row[13];
		$auto_dial_level = $row[14];
		$next_agent_call = $row[15];
		$local_call_time = $row[16];
		$voicemail_ext = $row[17];
		$dial_timeout = $row[18];
		$dial_prefix = $row[19];
		$campaign_cid = $row[20];
		$campaign_vdad_exten = $row[21];
		$campaign_rec_exten = $row[22];
		$campaign_recording = $row[23];
		$campaign_rec_filename = $row[24];
		$script_id = $row[25];
		$get_call_launch = $row[26];
		$am_message_exten = $row[27];
		$amd_send_to_vmx = $row[28];
		$xferconf_a_dtmf = $row[29];
		$xferconf_a_number = $row[30];
		$xferconf_b_dtmf = $row[31];
		$xferconf_b_number = $row[32];
		$alt_number_dialing = $row[33];
		$scheduled_callbacks = $row[34];
		$lead_filter_id = $row[35];
		if ($lead_filter_id=='') {$lead_filter_id='NONE';}
		$drop_call_seconds = $row[36];
		$safe_harbor_message = $row[37];
		$safe_harbor_exten = $row[38];
		$display_dialable_count = $row[39];
		$wrapup_seconds = $row[40];
		$wrapup_message = $row[41];
	#	$closer_campaigns = $row[42];
		$use_internal_dnc = $row[43];
		$allcalls_delay = $row[44];
		$omit_phone_code = $row[45];
		$dial_method = $row[46];
		$available_only_ratio_tally = $row[47];
		$adaptive_dropped_percentage = $row[48];
		$adaptive_maximum_level = $row[49];
		$adaptive_latest_server_time = $row[50];
		$adaptive_intensity = $row[51];
		$adaptive_dl_diff_target = $row[52];
		$concurrent_transfers = $row[53];
		$auto_alt_dial = $row[54];
		$auto_alt_dial_statuses = $row[55];
		$agent_pause_codes_active = $row[56];
		$campaign_description = $row[57];
		$campaign_changedate = $row[58];
		$campaign_stats_refresh = $row[59];
		$campaign_logindate = $row[60];
		$dial_statuses = $row[61];
		$disable_alter_custdata = $row[62];
		$no_hopper_leads_logins = $row[63];
		$list_order_mix = $row[64];
		$campaign_allow_inbound = $row[65];
		$manual_dial_list_id = $row[66];
		$default_xfer_group = $row[67];
		//$xfer_groups = $row[68];
		$web_form_address2 = $row[69];

	if (ereg("DISABLED",$list_order_mix))
		{$DEFlistDISABLE = '';	$DEFstatusDISABLED=0;}
	else
		{$DEFlistDISABLE = 'disabled';	$DEFstatusDISABLED=1;}

	$stmt="SELECT count(*) from vicidial_campaigns_list_mix where campaign_id='$campaign_id' and status='ACTIVE'";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	if ($rowx[0] < 1)
		{
		$mixes_list="<option SELECTED value=\"DISABLED\">DISABLED</option>\n";
		$mixname_list["DISABLED"] = "DISABLED";
		}
	else
		{
		##### get list_mix listings for dynamic pulldown
		$stmt="SELECT vcl_id,vcl_name from vicidial_campaigns_list_mix where campaign_id='$campaign_id' and status='ACTIVE' limit 1";
		$rslt=mysql_query($stmt, $link);
		$mixes_to_print = mysql_num_rows($rslt);
		$mixes_list="<option value=\"DISABLED\">DISABLED</option>\n";

		$o=0;
		while ($mixes_to_print > $o)
			{
			$rowx=mysql_fetch_row($rslt);
			$mixes_list .= "<option value=\"ACTIVE\">ACTIVE ($rowx[0] - $rowx[1])</option>\n";
			$mixname_list["ACTIVE"] = "$rowx[0] - $rowx[1]";
			$o++;
			}
		}

	##### get status listings for dynamic pulldown
	$stmt="SELECT * from vicidial_statuses order by status";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);
	$statuses_list='';
	$dial_statuses_list='';

	$o=0;
	while ($statuses_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		if ($rowx[0] != 'CBHOLD') {$dial_statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
		$statname_list["$rowx[0]"] = "$rowx[1]";
		$LRstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>\n";
		if (eregi("Y",$rowx[2]))
			{$HKstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>\n";}
		$o++;
		}

	$stmt="SELECT * from vicidial_campaign_statuses where campaign_id='$campaign_id' order by status";
	$rslt=mysql_query($stmt, $link);
	$Cstatuses_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($Cstatuses_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		if ($rowx[0] != 'CBHOLD') {$dial_statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
		$statname_list["$rowx[0]"] = "$rowx[1]";
		$LRstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>\n";
		if (eregi("Y",$rowx[2]))
			{$HKstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>\n";}
		$o++;
		}

	$dial_statuses = preg_replace("/ -$/","",$dial_statuses);
	$Dstatuses = explode(" ", $dial_statuses);
	$Ds_to_print = (count($Dstatuses) -1);

	##### get in-groups listings for dynamic pulldown list menu
	$stmt="SELECT group_id,group_name from vicidial_inbound_groups $xfer_groupsSQL order by group_id";
	$rslt=mysql_query($stmt, $link);
	$Xgroups_to_print = mysql_num_rows($rslt);
	$Xgroups_menu='';
	$Xgroups_selected=0;
	$o=0;
	while ($Xgroups_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$Xgroups_menu .= "<option ";
		if ($default_xfer_group == "$rowx[0]") 
			{
			$Xgroups_menu .= "SELECTED ";
			$Xgroups_selected++;
			}
		$Xgroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
	if ($Xgroups_selected < 1) 
		{$Xgroups_menu .= "<option SELECTED value=\"---NONE---\">---NONE---</option>\n";}
	else 
		{$Xgroups_menu .= "<option value=\"---NONE---\">---NONE---</option>\n";}


	if ($SUB<1)		{$camp_detail_color=$subcamp_color;}
		else		{$camp_detail_color=$campaigns_color;}
	if ($SUB==22)	{$camp_statuses_color=$subcamp_color;}
		else		{$camp_statuses_color=$campaigns_color;}
	if ($SUB==23)	{$camp_hotkeys_color=$subcamp_color;}
		else		{$camp_hotkeys_color=$campaigns_color;}
	if ($SUB==25)	{$camp_recycle_color=$subcamp_color;}
		else		{$camp_recycle_color=$campaigns_color;}
	if ($SUB==26)	{$camp_autoalt_color=$subcamp_color;}
		else		{$camp_autoalt_color=$campaigns_color;}
	if ($SUB==27)	{$camp_pause_color=$subcamp_color;}
		else		{$camp_pause_color=$campaigns_color;}
	if ($SUB==29)	{$camp_listmix_color=$subcamp_color;}
		else		{$camp_listmix_color=$campaigns_color;}
	
	//echo "&nbsp;<font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\"> <B>$row[0]</B>: </font>";
	//echo "&nbsp;<font size=2 color=navy face=\"ARIAL,HELVETICA\"> <B>$row[0]</B>: </font>";
	
	echo "<TABLE WIDTH=$page_width CELLPADDING=2 CELLSPACING=0><TR class='no-ul' BGCOLOR=\"$campaigns_color\">\n";
	echo "<TD></TD>";
	echo "<TD align=center><a href=\"$PHP_SELF?ADD=34&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Basic </font></a></TD>";
	echo "<TD align=center BGCOLOR=\"$camp_detail_color\"> <a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Detail </font></a> </TD>";
	echo "<TD align=center BGCOLOR=\"$camp_statuses_color\"><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Statuses</font></a></TD>";
	echo "<TD align=center BGCOLOR=\"$camp_hotkeys_color\"><a href=\"$PHP_SELF?ADD=31&SUB=23&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">HotKeys</font></a></TD>";
	echo "<TD align=center BGCOLOR=\"$camp_recycle_color\"><a href=\"$PHP_SELF?ADD=31&SUB=25&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Lead Recycling</font></a></TD>";
	echo "<TD align=center BGCOLOR=\"$camp_autoalt_color\"><a href=\"$PHP_SELF?ADD=31&SUB=26&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Auto Alt Dial</font></a></TD>";
	echo "<TD align=center BGCOLOR=\"$camp_pause_color\"><a href=\"$PHP_SELF?ADD=31&SUB=27&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Pause Codes</font></a></TD>";
	echo "<TD align=center BGCOLOR=\"$camp_listmix_color\"><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">List Mix</font></a></TD>";
	echo "<TD align=center> <a href=\"./AST_timeonVDADall.php?RR=4&DB=0&group=$row[0]\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Real-Time</font></a></TD>\n";
	echo "</TR></TABLE>\n";

	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center>\n";

	if ($SUB < 1)
		{
		echo "<center><br><font color=navy size=+1>MODIFY CAMPAIGN</font><form action=$PHP_SELF method=POST></center>\n";
		echo "<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=ADD value=41>\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "<TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign ID: </td><td align=left><b>$row[0]</b>$NWB#osdial_campaigns-campaign_id$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Name: </td><td align=left><input type=text name=campaign_name size=40 maxlength=40 value=\"$campaign_name\">$NWB#osdial_campaigns-campaign_name$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Description: </td><td align=left><input type=text name=campaign_description size=40 maxlength=255 value=\"$campaign_description\">$NWB#osdial_campaigns-campaign_description$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Change Date: </td><td align=left>$campaign_changedate &nbsp; $NWB#osdial_campaigns-campaign_changedate$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Login Date: </td><td align=left>$campaign_logindate &nbsp; $NWB#osdial_campaigns-campaign_logindate$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$row[2]</option></select>$NWB#osdial_campaigns-active$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Park Extension: </td><td align=left><input type=text name=park_ext size=10 maxlength=10 value=\"$row[9]\"> - Filename: <input type=text name=park_file_name size=10 maxlength=10 value=\"$row[10]\">$NWB#osdial_campaigns-park_ext$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 1: </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"$web_form_address\">$NWB#osdial_campaigns-web_form_address$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 2: </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"$web_form_address2\">$NWB#osdial_campaigns-web_form_address$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Allow Closers: </td><td align=left><select size=1 name=allow_closers><option>Y</option><option>N</option><option SELECTED>$allow_closers</option></select>$NWB#osdial_campaigns-allow_closers$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Allow Inbound and Blended: </td><td align=left><select size=1 name=campaign_allow_inbound><option>Y</option><option>N</option><option SELECTED>$campaign_allow_inbound</option></select>$NWB#osdial_campaigns-campaign_allow_inbound$NWE</td></tr>\n";

		$o=0;
		while ($Ds_to_print > $o) 
			{
			$o++;
			$Dstatus = $Dstatuses[$o];

			echo "<tr bgcolor=#C1D6DF><td align=right>Dial Status $o: </td><td align=left> \n";

			if ($DEFstatusDISABLED > 0)
				{
				echo "<font color=grey><DEL><b>$Dstatus</b> - $statname_list[$Dstatus] &nbsp; &nbsp; &nbsp; &nbsp; <font size=2>\n";
				echo "REMOVE</DEL></td></tr>\n";
				}
			else
				{
				echo "<b>$Dstatus</b> - $statname_list[$Dstatus] &nbsp; &nbsp; &nbsp; &nbsp; <font size=2>\n";
				echo "<a href=\"$PHP_SELF?ADD=68&campaign_id=$campaign_id&status=$Dstatuses[$o]\">REMOVE</a></td></tr>\n";
				}
			}

		echo "<tr bgcolor=#C1D6DF><td align=right>Add A Dial Status: </td><td align=left><select size=1 name=dial_status $DEFlistDISABLE>\n";
		echo "<option value=\"\"> - NONE - </option>\n";

		echo "$dial_statuses_list";
		echo "</select> &nbsp; \n";
		echo "<input type=submit name=submit value=ADD> &nbsp; &nbsp; $NWB#osdial_campaigns-dial_status$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>List Order: </td><td align=left><select size=1 name=lead_order ><option>DOWN</option><option>UP</option><option>DOWN PHONE</option><option>UP PHONE</option><option>DOWN LAST NAME</option><option>UP LAST NAME</option><option>DOWN COUNT</option><option>UP COUNT</option><option>DOWN 2nd NEW</option><option>DOWN 3rd NEW</option><option>DOWN 4th NEW</option><option>DOWN 5th NEW</option><option>DOWN 6th NEW</option><option>UP 2nd NEW</option><option>UP 3rd NEW</option><option>UP 4th NEW</option><option>UP 5th NEW</option><option>UP 6th NEW</option><option>DOWN PHONE 2nd NEW</option><option>DOWN PHONE 3rd NEW</option><option>DOWN PHONE 4th NEW</option><option>DOWN PHONE 5th NEW</option><option>DOWN PHONE 6th NEW</option><option>UP PHONE 2nd NEW</option><option>UP PHONE 3rd NEW</option><option>UP PHONE 4th NEW</option><option>UP PHONE 5th NEW</option><option>UP PHONE 6th NEW</option><option>DOWN LAST NAME 2nd NEW</option><option>DOWN LAST NAME 3rd NEW</option><option>DOWN LAST NAME 4th NEW</option><option>DOWN LAST NAME 5th NEW</option><option>DOWN LAST NAME 6th NEW</option><option>UP LAST NAME 2nd NEW</option><option>UP LAST NAME 3rd NEW</option><option>UP LAST NAME 4th NEW</option><option>UP LAST NAME 5th NEW</option><option>UP LAST NAME 6th NEW</option><option>DOWN COUNT 2nd NEW</option><option>DOWN COUNT 3rd NEW</option><option>DOWN COUNT 4th NEW</option><option>DOWN COUNT 5th NEW</option><option>DOWN COUNT 6th NEW</option><option>UP COUNT 2nd NEW</option><option>UP COUNT 3rd NEW</option><option>UP COUNT 4th NEW</option><option>UP COUNT 5th NEW</option><option>UP COUNT 6th NEW</option><option SELECTED>$lead_order</option></select>$NWB#osdial_campaigns-lead_order$NWE</td></tr>\n";
		#echo "<tr bgcolor=#C1D6DF><td align=right>List Order: </td><td align=left><select size=1 name=lead_order ><option>DOWN</option><option>UP</option><option>UP PHONE</option><option>DOWN PHONE</option><option>UP LAST NAME</option><option>DOWN LAST NAME</option><option>UP COUNT</option><option>DOWN COUNT</option><option>DOWN COUNT 2nd NEW</option><option>DOWN COUNT 3rd NEW</option><option>DOWN COUNT 4th NEW</option><option>DOWN COUNT 5th NEW</option><option>DOWN COUNT 6th NEW</option><option SELECTED>$lead_order</option></select>$NWB#osdial_campaigns-lead_order$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaign_id&vcl_id=$list_order_mix\">List Mix</a>: </td><td align=left><select size=1 name=list_order_mix>\n";
		echo "$mixes_list";
		if (ereg("DISABLED",$list_order_mix))
			{echo "<option selected value=\"$list_order_mix\">$list_order_mix - $mixname_list[$list_order_mix]</option>\n";}
		else
			{echo "<option selected value=\"ACTIVE\">ACTIVE ($mixname_list[ACTIVE])</option>\n";}
		echo "</select>$NWB#osdial_campaigns-list_order_mix$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$lead_filter_id\">Lead Filter</a>: </td><td align=left><select size=1 name=lead_filter_id>\n";
		echo "$filters_list";
		echo "<option selected value=\"$lead_filter_id\">$lead_filter_id - $filtername_list[$lead_filter_id]</option>\n";
		echo "</select>$NWB#osdial_campaigns-lead_filter_id$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Hopper Level: </td><td align=left><select size=1 name=hopper_level><option>1</option><option>5</option><option>10</option><option>20</option><option>50</option><option>100</option><option>200</option><option>500</option><option>700</option><option>1000</option><option>2000</option><option SELECTED>$hopper_level</option></select>$NWB#osdial_campaigns-hopper_level$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Force Reset of Hopper: </td><td align=left><select size=1 name=reset_hopper><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_campaigns-force_reset_hopper$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Dial Method: </td><td align=left><select size=1 name=dial_method><option >MANUAL</option><option>RATIO</option><option>ADAPT_HARD_LIMIT</option><option>ADAPT_TAPERED</option><option>ADAPT_AVERAGE</option><option SELECTED>$dial_method</option></select>$NWB#osdial_campaigns-dial_method$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Auto Dial Level: </td><td align=left><select size=1 name=auto_dial_level><option >0</option><option>1</option><option>1.1</option><option>1.2</option><option>1.3</option><option>1.4</option><option>1.5</option><option>1.6</option><option>1.7</option><option>1.8</option><option>1.9</option><option>2.0</option><option>2.2</option><option>2.5</option><option>2.7</option><option>3.0</option><option>3.5</option><option>4.0</option><option SELECTED>$auto_dial_level</option></select>(0 = off)$NWB#osdial_campaigns-auto_dial_level$NWE &nbsp; &nbsp; &nbsp; <input type=checkbox name=dial_level_override value=\"1\">ADAPT OVERRIDE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Available Only Tally: </td><td align=left><select size=1 name=available_only_ratio_tally><option >Y</option><option>N</option><option SELECTED>$available_only_ratio_tally</option></select>$NWB#osdial_campaigns-available_only_ratio_tally$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Drop Percentage Limit: </td><td align=left><select size=1 name=adaptive_dropped_percentage>\n";
		$n=100;
		while ($n>=1)
			{
			echo "<option>$n</option>\n";
			$n--;
			}
		echo "<option SELECTED>$adaptive_dropped_percentage</option></select>% $NWB#osdial_campaigns-adaptive_dropped_percentage$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Maximum Adapt Dial Level: </td><td align=left><input type=text name=adaptive_maximum_level size=6 maxlength=6 value=\"$adaptive_maximum_level\"><i>number only</i> $NWB#osdial_campaigns-adaptive_maximum_level$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Latest Server Time: </td><td align=left><input type=text name=adaptive_latest_server_time size=6 maxlength=4 value=\"$adaptive_latest_server_time\"><i>4 digits only</i> $NWB#osdial_campaigns-adaptive_latest_server_time$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Adapt Intensity Modifier: </td><td align=left><select size=1 name=adaptive_intensity>\n";
		$n=40;
		while ($n>=-40)
			{
			$dtl = 'Balanced';
			if ($n<0) {$dtl = 'Less Intense';}
			if ($n>0) {$dtl = 'More Intense';}
			if ($n == $adaptive_intensity) 
				{echo "<option SELECTED value=\"$n\">$n - $dtl</option>\n";}
			else
				{echo "<option value=\"$n\">$n - $dtl</option>\n";}
			$n--;
			}
		echo "</select> $NWB#osdial_campaigns-adaptive_intensity$NWE</td></tr>\n";



		echo "<tr bgcolor=#9FD79F><td align=right>Dial Level Difference Target: </td><td align=left><select size=1 name=adaptive_dl_diff_target>\n";
		$n=40;
		while ($n>=-40)
			{
			$nabs = abs($n);
			$dtl = 'Balanced';
			if ($n<0) {$dtl = 'Agents Waiting for Calls';}
			if ($n>0) {$dtl = 'Calls Waiting for Agents';}
			if ($n == $adaptive_dl_diff_target) 
				{echo "<option SELECTED value=\"$n\">$n --- $nabs $dtl</option>\n";}
			else
				{echo "<option value=\"$n\">$n --- $nabs $dtl</option>\n";}
			$n--;
			}
		echo "</select> $NWB#osdial_campaigns-adaptive_dl_diff_target$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Concurrent Transfers: </td><td align=left><select size=1 name=concurrent_transfers><option >AUTO</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10<option SELECTED>$concurrent_transfers</option></select>$NWB#osdial_campaigns-concurrent_transfers$NWE</td></tr>\n";


		echo "<tr bgcolor=#C1D6DF><td align=right>Auto Alt-Number Dialing: </td><td align=left><select size=1 name=auto_alt_dial><option >NONE</option><option>ALT_ONLY</option><option>ADDR3_ONLY</option><option>ALT_AND_ADDR3<option SELECTED>$auto_alt_dial</option></select>$NWB#osdial_campaigns-auto_alt_dial$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Next Agent Call: </td><td align=left><select size=1 name=next_agent_call><option >random</option><option>oldest_call_start</option><option>oldest_call_finish</option><option>overall_user_level</option><option>campaign_rank</option><option>fewest_calls</option><option SELECTED>$next_agent_call</option></select>$NWB#osdial_campaigns-next_agent_call$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$local_call_time\">Local Call Time: </a></td><td align=left><select size=1 name=local_call_time>\n";
		echo "$call_times_list";
		echo "<option selected value=\"$local_call_time\">$local_call_time - $call_timename_list[$local_call_time]</option>\n";
		echo "</select>$NWB#osdial_campaigns-local_call_time$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Dial Timeout: </td><td align=left><input type=text name=dial_timeout size=3 maxlength=3 value=\"$dial_timeout\"> <i>in seconds</i>$NWB#osdial_campaigns-dial_timeout$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Dial Prefix: </td><td align=left><input type=text name=dial_prefix size=20 maxlength=20 value=\"$dial_prefix\"> <font size=1>use 9 for TRUNK1, use 8 for TRUNK2</font>$NWB#osdial_campaigns-dial_prefix$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Omit Phone Code: </td><td align=left><select size=1 name=omit_phone_code><option>Y</option><option>N</option><option SELECTED>$omit_phone_code</option></select>$NWB#osdial_campaigns-omit_phone_code$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign CallerID: </td><td align=left><input type=text name=campaign_cid size=20 maxlength=20 value=\"$campaign_cid\">$NWB#osdial_campaigns-campaign_cid$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign OSDial exten: </td><td align=left><input type=text name=campaign_vdad_exten size=10 maxlength=20 value=\"$campaign_vdad_exten\">$NWB#osdial_campaigns-campaign_vdad_exten$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Rec exten: </td><td align=left><input type=text name=campaign_rec_exten size=10 maxlength=10 value=\"$campaign_rec_exten\">$NWB#osdial_campaigns-campaign_rec_exten$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Recording: </td><td align=left><select size=1 name=campaign_recording><option>NEVER</option><option>ONDEMAND</option><option>ALLCALLS</option><option>ALLFORCE</option><option SELECTED>$campaign_recording</option></select>$NWB#osdial_campaigns-campaign_recording$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Rec Filename: </td><td align=left><input type=text name=campaign_rec_filename size=50 maxlength=50 value=\"$campaign_rec_filename\">$NWB#osdial_campaigns-campaign_rec_filename$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Recording Delay: </td><td align=left><input type=text name=allcalls_delay size=3 maxlength=3 value=\"$allcalls_delay\"> <i>in seconds</i>$NWB#osdial_campaigns-allcalls_delay$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=3111111&script_id=$script_id\">Script</a>: </td><td align=left><select size=1 name=script_id>\n";
		echo "$scripts_list";
		echo "<option selected value=\"$script_id\">$script_id - $scriptname_list[$script_id]</option>\n";
		echo "</select>$NWB#osdial_campaigns-campaign_script$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Get Call Launch: </td><td align=left><select size=1 name=get_call_launch><option selected>NONE</option><option>SCRIPT</option><option>WEBFORM</option><option selected>$get_call_launch</option></select>$NWB#osdial_campaigns-get_call_launch$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Answering Machine Message: </td><td align=left><input type=text name=am_message_exten size=10 maxlength=20 value=\"$am_message_exten\">$NWB#osdial_campaigns-am_message_exten$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>AMD Send to VM exten: </td><td align=left><select size=1 name=amd_send_to_vmx><option>Y</option><option>N</option><option SELECTED>$amd_send_to_vmx</option></select>$NWB#osdial_campaigns-amd_send_to_vmx$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Transfer-Conf DTMF 1: </td><td align=left><input type=text name=xferconf_a_dtmf size=20 maxlength=50 value=\"$xferconf_a_dtmf\">$NWB#osdial_campaigns-xferconf_a_dtmf$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Transfer-Conf Number 1: </td><td align=left><input type=text name=xferconf_a_number size=20 maxlength=50 value=\"$xferconf_a_number\">$NWB#osdial_campaigns-xferconf_a_dtmf$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Transfer-Conf DTMF 2: </td><td align=left><input type=text name=xferconf_b_dtmf size=20 maxlength=50 value=\"$xferconf_b_dtmf\">$NWB#osdial_campaigns-xferconf_a_dtmf$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Transfer-Conf Number 2: </td><td align=left><input type=text name=xferconf_b_number size=20 maxlength=50 value=\"$xferconf_b_number\">$NWB#osdial_campaigns-xferconf_a_dtmf$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Alt Number Dialing: </td><td align=left><select size=1 name=alt_number_dialing><option>Y</option><option>N</option><option SELECTED>$alt_number_dialing</option></select>$NWB#osdial_campaigns-alt_number_dialing$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Scheduled Callbacks: </td><td align=left><select size=1 name=scheduled_callbacks><option>Y</option><option>N</option><option SELECTED>$scheduled_callbacks</option></select>$NWB#osdial_campaigns-scheduled_callbacks$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Drop Call Seconds: </td><td align=left><input type=text name=drop_call_seconds size=5 maxlength=2 value=\"$drop_call_seconds\">$NWB#osdial_campaigns-drop_call_seconds$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Voicemail: </td><td align=left><input type=text name=voicemail_ext size=10 maxlength=10 value=\"$voicemail_ext\">$NWB#osdial_campaigns-voicemail_ext$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Use Safe Harbor Message: </td><td align=left><select size=1 name=safe_harbor_message><option>Y</option><option>N</option><option SELECTED>$safe_harbor_message</option></select>$NWB#osdial_campaigns-safe_harbor_message$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Safe Harbor Exten: </td><td align=left><input type=text name=safe_harbor_exten size=10 maxlength=20 value=\"$safe_harbor_exten\">$NWB#osdial_campaigns-safe_harbor_exten$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Wrap Up Seconds: </td><td align=left><input type=text name=wrapup_seconds size=5 maxlength=3 value=\"$wrapup_seconds\">$NWB#osdial_campaigns-wrapup_seconds$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Wrap Up Message: </td><td align=left><input type=text name=wrapup_message size=40 maxlength=255 value=\"$wrapup_message\">$NWB#osdial_campaigns-wrapup_message$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Use Internal DNC List: </td><td align=left><select size=1 name=use_internal_dnc><option>Y</option><option>N</option><option SELECTED>$use_internal_dnc</option></select>$NWB#osdial_campaigns-use_internal_dnc$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Agent Pause Codes Active: </td><td align=left><select size=1 name=agent_pause_codes_active><option>Y</option><option>N</option><option SELECTED>$agent_pause_codes_active</option></select>$NWB#osdial_campaigns-agent_pause_codes_active$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Stats Refresh: </td><td align=left><select size=1 name=campaign_stats_refresh><option>Y</option><option>N</option><option SELECTED>$campaign_stats_refresh</option></select>$NWB#osdial_campaigns-campaign_stats_refresh$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Disable Alter Customer Data: </td><td align=left><select size=1 name=disable_alter_custdata><option>Y</option><option>N</option><option SELECTED>$disable_alter_custdata</option></select>$NWB#osdial_campaigns-disable_alter_custdata$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Allow No-Hopper-Leads Logins: </td><td align=left><select size=1 name=no_hopper_leads_logins><option>Y</option><option>N</option><option SELECTED>$no_hopper_leads_logins</option></select>$NWB#osdial_campaigns-no_hopper_leads_logins$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Manual Dial List ID: </td><td align=left><input type=text name=manual_dial_list_id size=15 maxlength=12 value=\"$manual_dial_list_id\">$NWB#osdial_campaigns-manual_dial_list_id$NWE</td></tr>\n";

		if ($campaign_allow_inbound == 'Y')
			{
			echo "<tr bgcolor=#C1D6DF><td align=right>Allowed Inbound Groups: <BR>";
			echo " $NWB#osdial_campaigns-closer_campaigns$NWE</td><td align=left>\n";
			echo "$groups_list";
			echo "</td></tr>\n";
			}

		echo "<tr bgcolor=#C1D6DF><td align=right>Default Transfer Group: </td><td align=left><select size=1 name=default_xfer_group>";
		echo "$Xgroups_menu";
		echo "</select>$NWB#osdial_campaigns-default_xfer_group$NWE</td></tr>\n";

		if ($allow_closers == 'Y')
			{
			echo "<tr bgcolor=#C1D6DF><td align=right>Allowed Transfer Groups: <BR>";
			echo " $NWB#osdial_campaigns-xfer_groups$NWE</td><td align=left>\n";
			echo "$XFERgroups_list";
			echo "</td></tr>\n";
			}

		echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
		echo "</TABLE></center></FORM>\n";

	echo "<center>\n";
	echo "<br><br><b><font color=navy size=+1>LISTS WITHIN THIS CAMPAIGN &nbsp; $NWB#osdial_campaign_lists$NWE</font></b><br>\n";
	echo "<TABLE width=400 cellspacing=3>\n";
	echo "<tr><td><font color=navy>LIST ID</font></td><td><font color=navy>LIST NAME</font></td><td><font color=navy>ACTIVE</font></td></tr>\n";

		$active_lists = 0;
		$inactive_lists = 0;
		$stmt="SELECT list_id,active,list_name from vicidial_lists where campaign_id='$campaign_id'";
		$rslt=mysql_query($stmt, $link);
		$lists_to_print = mysql_num_rows($rslt);
		$camp_lists='';

		$o=0;
		while ($lists_to_print > $o) 
			{
				$rowx=mysql_fetch_row($rslt);
				$o++;
			if (ereg("Y", $rowx[1])) {$active_lists++;   $camp_lists .= "'$rowx[0]',";}
			if (ereg("N", $rowx[1])) {$inactive_lists++;}

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=311&list_id=$rowx[0]\">$rowx[0]</a></td><td><font size=1>$rowx[2]</td><td><font size=1>$rowx[1]</td></tr>\n";
			}
		echo "</table></center><br>\n";
		echo "<center><b>\n";

		$filterSQL = $filtersql_list[$lead_filter_id];
		$filterSQL = eregi_replace("^and|and$|^or|or$","",$filterSQL);
		if (strlen($filterSQL)>4)
			{$fSQL = "and $filterSQL";}
		else
			{$fSQL = '';}

			$camp_lists = eregi_replace(".$","",$camp_lists);
		echo "<br><br><font color=navy>This campaign has $active_lists active lists and $inactive_lists inactive lists</font><br><br>\n";

		if ($display_dialable_count == 'Y')
			{
			### call function to calculate and print dialable leads
			dialable_leads($DB,$link,$local_call_time,$dial_statuses,$camp_lists,$fSQL);
			echo " - <font size=1><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id&stage=hide_dialable\">HIDE</a></font><BR><BR>";
			}
		else
			{
			echo "<a href=\"$PHP_SELF?ADD=73&campaign_id=$campaign_id\" target=\"_blank\">Popup Dialable Leads Count</a>";
			echo " - <font size=1><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id&stage=show_dialable\">SHOW</a></font><BR><BR>";
			}





			$stmt="SELECT count(*) FROM vicidial_hopper where campaign_id='$campaign_id' and status IN('READY')";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$rowx=mysql_fetch_row($rslt);
			$hopper_leads = "$rowx[0]";

		echo "<font color=navy>This campaign has $hopper_leads leads in the dial hopper<br><br>\n";
		echo "<a href=\"./AST_VICIDIAL_hopperlist.php?group=$campaign_id\">Click here to see what leads are in the hopper right now</a><br><br>\n";
		echo "<a href=\"$PHP_SELF?ADD=81&campaign_id=$campaign_id\">Click here to see all CallBack Holds in this campaign</a><BR><BR>\n";
		echo "<a href=\"./AST_VDADstats.php?group=$campaign_id\">Click here to see a Time On Dialer report for this campaign</a></font><BR><BR>\n";
		echo "</b></center>\n";
		}


	##### CAMPAIGN CUSTOM STATUSES #####
	if ($SUB==22)
		{

	##### get status category listings for dynamic pulldown
	$stmt="SELECT vsc_id,vsc_name from vicidial_status_categories order by vsc_id desc";
	$rslt=mysql_query($stmt, $link);
	$cats_to_print = mysql_num_rows($rslt);
	$cats_list="";

	$o=0;
	while ($cats_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$cats_list .= "<option value=\"$rowx[0]\">$rowx[0] - " . substr($rowx[1],0,20) . "</option>\n";
		$catsname_list["$rowx[0]"] = substr($rowx[1],0,20);
		$o++;
		}


		echo "<center>\n";
		echo "<br><b><font color=navy size=+1>CUSTOM STATUSES WITHIN THIS CAMPAIGN &nbsp; $NWB#osdial_campaign_statuses$NWE</font></b><br><br>\n";
		echo "<TABLE width=500 cellspacing=3>\n";
		echo "<tr><td><font color=navy>STATUS</font></td>";
		echo "<td><font color=navy>DESCRIPTION</font></td>";
		echo "<td><font color=navy>SELECTABLE</font></td>";
		echo "<td><font color=navy>HUMAN ANSWER</font></td>";
		echo "<td><font color=navy>DELETE</font></td></tr>\n";

		$stmt="SELECT * from vicidial_campaign_statuses where campaign_id='$campaign_id'";
		$rslt=mysql_query($stmt, $link);
		$statuses_to_print = mysql_num_rows($rslt);
		$o=0;
		while ($statuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$AScategory = $rowx[5];
			$o++;

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><form action=$PHP_SELF method=POST>\n";
			echo "<input type=hidden name=ADD value=42>\n";
			echo "<input type=hidden name=stage value=modify>\n";
			echo "<input type=hidden name=status value=\"$rowx[0]\">\n";
			echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
			echo "<font size=2><B>$rowx[0]</B></td>\n";
			echo "<td><input type=text name=status_name size=20 maxlength=30 value=\"$rowx[1]\"></td>\n";
			echo "<td><select size=1 name=selectable><option>Y</option><option>N</option><option selected>$rowx[2]</option></select></td>\n";
			echo "<td><select size=1 name=human_answered><option>Y</option><option>N</option><option selected>$rowx[4]</option></select></td>\n";
			echo "<td>\n";
			echo "<select size=1 name=category>\n";
			echo "$cats_list";
			echo "<option selected value=\"$AScategory\">$AScategory - $catsname_list[$AScategory]</option>\n";
			echo "</select>\n";
			echo "</td>\n";
			echo "<td align=center nowrap><font size=1><input type=submit name=submit value=MODIFY> &nbsp; &nbsp; &nbsp; &nbsp; \n";
			echo " &nbsp; \n";
			echo "<a href=\"$PHP_SELF?ADD=42&campaign_id=$campaign_id&status=$rowx[0]&stage=delete\">DELETE</a>\n";
			echo "</form></td></tr>\n";
			}

		echo "</table>\n";

		echo "<br><br><br><font color=navy>ADD NEW CUSTOM CAMPAIGN STATUS</font><BR><form action=$PHP_SELF method=POST><br>\n";
		echo "<input type=hidden name=ADD value=22>\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "Status: <input type=text name=status size=10 maxlength=8> &nbsp; \n";
		echo "Description: <input type=text name=status_name size=20 maxlength=30> &nbsp; \n";
		echo "Selectable: <select size=1 name=selectable><option>Y</option><option>N</option></select> &nbsp; \n";
		echo "Human Answer: <select size=1 name=human_answered><option>Y</option><option>N</option></select> &nbsp; \n";
		echo "Category: \n";
		echo "<select size=1 name=category>\n";
		echo "$cats_list";
		echo "<option selected value=\"$AScategory\">$AScategory - $catsname_list[$AScategory]</option>\n";
		echo "</select> &nbsp; <BR>\n";
		echo "<input type=submit name=submit value=ADD><BR>\n";

		echo "</FORM><br>\n";
		}

	##### CAMPAIGN HOTKEYS #####
	if ($SUB==23)
		{
		echo "<center><br><b><font color=navy size=+1>CUSTOM HOT KEYS WITHIN THIS CAMPAIGN &nbsp; $NWB#osdial_campaign_hotkeys$NWE</font></b><br><br>\n";
		echo "<TABLE width=400 cellspacing=3 align=center>\n";
		echo "<tr><td>HOT KEY</td><td>STATUS</td><td>DESCRIPTION</td><td>DELETE</td></tr>\n";

			$stmt="SELECT * from vicidial_campaign_hotkeys where campaign_id='$campaign_id' order by hotkey";
			$rslt=mysql_query($stmt, $link);
			$statuses_to_print = mysql_num_rows($rslt);
			$o=0;
			while ($statuses_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$o++;

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><font size=1>$rowx[1]</td><td><font size=1>$rowx[0]</td><td><font size=1>$rowx[2]</td><td><font size=1><a href=\"$PHP_SELF?ADD=43&campaign_id=$campaign_id&status=$rowx[0]&hotkey=$rowx[1]&action=DELETE\">DELETE</a></td></tr>\n";

			}

		echo "</table></center>\n";

		echo "<br><br><font color=navy>ADD NEW CUSTOM CAMPAIGN HOT KEY</font><BR><form action=$PHP_SELF method=POST><br>\n";
		echo "<input type=hidden name=ADD value=23>\n";
		echo "<input type=hidden name=selectable value=Y>\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "Hotkey: <select size=1 name=hotkey>\n";
		echo "<option>1</option>\n";
		echo "<option>2</option>\n";
		echo "<option>3</option>\n";
		echo "<option>4</option>\n";
		echo "<option>5</option>\n";
		echo "<option>6</option>\n";
		echo "<option>7</option>\n";
		echo "<option>8</option>\n";
		echo "<option>9</option>\n";
		echo "</select> &nbsp; \n";
		echo "Status: <select size=1 name=HKstatus>\n";
		echo "$HKstatuses_list\n";
		echo "<option value=\"ALTPH2-----Alternate Phone Hot Dial\">ALTPH2 - Alternate Phone Hot Dial</option>\n";
		echo "<option value=\"ADDR3-----Address3 Hot Dial\">ADDR3 - Address3 Hot Dial</option>\n";
		echo "</select> &nbsp; \n";
		echo "<input type=submit name=submit value=ADD><BR>\n";
		echo "</form><BR>\n";
		}

	##### CAMPAIGN LEAD RECYCLING #####
	if ($SUB==25)
		{
		echo "<br><br><b><font color=navy size=+1>LEAD RECYCLING WITHIN THIS CAMPAIGN &nbsp; $NWB#osdial_lead_recycle$NWE</font></b><br><br>\n";
		echo "<TABLE width=500 cellspacing=3>\n";
		echo "<tr><td><font color=navy>STATUS</font></td><td><font color=navy>ATTEMPT DELAY</font></td><td><font color=navy>ATTEMPT MAXIMUM</font></td><td><font color=navy>ACTIVE</font></td><td> </td><td><font color=navy>DELETE</font></td></tr>\n";

			$stmt="SELECT * from vicidial_lead_recycle where campaign_id='$campaign_id' order by status";
			$rslt=mysql_query($stmt, $link);
			$recycle_to_print = mysql_num_rows($rslt);
			$o=0;
			while ($recycle_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$o++;

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><font size=1>$rowx[2]<form action=$PHP_SELF method=POST>\n";
			echo "<input type=hidden name=status value=\"$rowx[2]\">\n";
			echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
			echo "<input type=hidden name=ADD value=45></td>\n";
			echo "<td><font size=1><input type=text size=7 maxlength=5 name=attempt_delay value=\"$rowx[3]\"></td>\n";
			echo "<td><font size=1><input type=text size=5 maxlength=3 name=attempt_maximum value=\"$rowx[4]\"></td>\n";
			echo "<td><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$rowx[5]</option></select></td>\n";
			echo "<td><font size=1><input type=submit name=submit value=MODIFY></form></td>\n";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=65&campaign_id=$campaign_id&status=$rowx[2]\">DELETE</a></td></tr>\n";
			}

		echo "</table>\n";

		echo "<br><br><font color=navy>ADD NEW CAMPAIGN LEAD RECYCLE</font><BR><form action=$PHP_SELF method=POST><br>\n";
		echo "<input type=hidden name=ADD value=25>\n";
		echo "<input type=hidden name=active value=\"N\">\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "Status: <select size=1 name=status>\n";
		echo "$LRstatuses_list\n";
		echo "</select> &nbsp; \n";
		echo "Attempt Delay: <input type=text size=7 maxlength=5 name=attempt_delay>\n";
		echo "Attempt Maximum: <input type=text size=5 maxlength=3 name=attempt_maximum>\n";
		echo "<input type=submit name=submit value=ADD><BR>\n";

		echo "</FORM><br>\n";
		}

	##### CAMPAIGN AUTO-ALT-NUMBER DIALING #####
	if ($SUB==26)
		{
		echo "<br><br><b><font color=navy size=+1>AUTO ALT NUMBER DIALING FOR THIS CAMPAIGN &nbsp; $NWB#osdial_auto_alt_dial_statuses$NWE</font></b><br><br>\n";
		echo "<TABLE width=500 cellspacing=3>\n";
		echo "<tr><td><font color=navy>STATUSES</font></td><td><font color=navy>DELETE</font></td></tr>\n";

		$auto_alt_dial_statuses = preg_replace("/ -$/","",$auto_alt_dial_statuses);
		$AADstatuses = explode(" ", $auto_alt_dial_statuses);
		$AADs_to_print = (count($AADstatuses) -1);

		$o=0;
		while ($AADs_to_print > $o) 
			{
			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}
			$o++;

			echo "<tr $bgcolor><td><font size=1>$AADstatuses[$o]</td>\n";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=66&campaign_id=$campaign_id&status=$AADstatuses[$o]\">DELETE</a></td></tr>\n";
			}

		echo "</table>\n";

		echo "<br><br><font color=navy>ADD NEW AUTO ALT NUMBER DIALING STATUS</font><BR><form action=$PHP_SELF method=POST><br>\n";
		echo "<input type=hidden name=ADD value=26>\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "Status: <select size=1 name=status>\n";
		echo "$LRstatuses_list\n";
		echo "</select> &nbsp; \n";
		echo "<input type=submit name=submit value=ADD><BR>\n";

		echo "</FORM><br>\n";
		}

	##### CAMPAIGN PAUSE CODES #####
	if ($SUB==27)
		{
		echo "<br><br><b><font color=navy size=+1>AGENT PAUSE CODES FOR THIS CAMPAIGN &nbsp; $NWB#osdial_pause_codes$NWE</font></b><br><br>\n";
		echo "<TABLE width=500 cellspacing=3>\n";
		echo "<tr><td><font color=navy>PAUSE CODES</font></td><td><font color=navy>BILLABLE</font></td><td><font color=navy>MODIFY</font></td><td><font color=navy>DELETE</font></td></tr>\n";

			$stmt="SELECT * from vicidial_pause_codes where campaign_id='$campaign_id' order by pause_code";
			$rslt=mysql_query($stmt, $link);
			$pause_codes_to_print = mysql_num_rows($rslt);
			$o=0;
			while ($pause_codes_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$o++;

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><form action=$PHP_SELF method=POST><font size=1>$rowx[0]\n";
			echo "<input type=hidden name=ADD value=47>\n";
			echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
			echo "<input type=hidden name=pause_code value=\"$rowx[0]\"> &nbsp;\n";
			echo "<input type=text size=20 maxlength=30 name=pause_code_name value=\"$rowx[1]\"></td>\n";
			echo "<td><select size=1 name=billable><option>YES</option><option>NO</option><option>HALF</option><option SELECTED>$rowx[2]</option></select></td>\n";
			echo "<td><font size=1><input type=submit name=submit value=MODIFY></form></td>\n";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=67&campaign_id=$campaign_id&pause_code=$rowx[0]\">DELETE</a></td></tr>\n";
			}

		echo "</table>\n";

		echo "<br><br><font color=navy>ADD NEW AGENT PAUSE CODE</font><BR><form action=$PHP_SELF method=POST><br>\n";
		echo "<input type=hidden name=ADD value=27>\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "Pause Code: <input type=text size=8 maxlength=6 name=pause_code>\n";
		echo "Pause Code Name: <input type=text size=20 maxlength=30 name=pause_code_name>\n";
		echo " &nbsp; Billable: <select size=1 name=billable><option>YES</option><option>NO</option><option>HALF</option></select>\n";
		echo "<input type=submit name=submit value=ADD><BR>\n";

		echo "</center></FORM><br>\n";
		}


	if ($SUB < 1)
		{
		echo "<BR><BR>\n";
		echo "<a href=\"$PHP_SELF?ADD=52&campaign_id=$campaign_id\">LOG ALL AGENTS OUT OF THIS CAMPAIGN</a><BR><BR>\n";
		echo "<a href=\"$PHP_SELF?ADD=53&campaign_id=$campaign_id\">EMERGENCY VDAC CLEAR FOR THIS CAMPAIGN</a><BR><BR>\n";

		if ($LOGdelete_campaigns > 0)
			{
			echo "<br><br><a href=\"$PHP_SELF?ADD=51&campaign_id=$campaign_id\">DELETE THIS CAMPAIGN</a>\n";
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=34 modify campaign info in the system - Basic View
######################

if ( ($ADD==34) and ( (!eregi("$campaign_id",$LOGallowed_campaigns)) and (!eregi("ALL-CAMPAIGNS",$LOGallowed_campaigns)) ) ) 
	{$ADD=30;}	# send to not allowed screen if not in vicidial_user_groups allowed_campaigns list

if ($ADD==34)
{
	if ($LOGmodify_campaigns==1)
	{
		if ($stage=='show_dialable')
		{
			$stmt="UPDATE vicidial_campaigns set display_dialable_count='Y' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
		}
		if ($stage=='hide_dialable')
		{
			$stmt="UPDATE vicidial_campaigns set display_dialable_count='N' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
		}

		$stmt="SELECT * from vicidial_campaigns where campaign_id='$campaign_id';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$dial_status_a = $row[3];
		$dial_status_b = $row[4];
		$dial_status_c = $row[5];
		$dial_status_d = $row[6];
		$dial_status_e = $row[7];
		$lead_order = $row[8];
		$hopper_level = $row[13];
		$auto_dial_level = $row[14];
		$next_agent_call = $row[15];
		$local_call_time = $row[16];
		$voicemail_ext = $row[17];
		$dial_timeout = $row[18];
		$dial_prefix = $row[19];
		$campaign_cid = $row[20];
		$campaign_vdad_exten = $row[21];
		$script_id = $row[25];
		$get_call_launch = $row[26];
		$lead_filter_id = $row[35];
			if ($lead_filter_id=='') {$lead_filter_id='NONE';}
		$display_dialable_count = $row[39];
		$dial_method = $row[46];
		$adaptive_intensity = $row[51];
		$campaign_description = $row[57];
		$campaign_changedate = $row[58];
		$campaign_stats_refresh = $row[59];
		$campaign_logindate = $row[60];
		$dial_statuses = $row[61];
		$list_order_mix = $row[64];
		$default_xfer_group = $row[67];
		$campaign_allow_inbound = $row[65];
		$default_xfer_group = $row[67];

	if (ereg("DISABLED",$list_order_mix))
		{$DEFlistDISABLE = '';	$DEFstatusDISABLED=0;}
	else
		{$DEFlistDISABLE = 'disabled';	$DEFstatusDISABLED=1;}

		$stmt="SELECT * from vicidial_statuses order by status";
		$rslt=mysql_query($stmt, $link);
		$statuses_to_print = mysql_num_rows($rslt);
		$statuses_list='';
		$dial_statuses_list='';
		$o=0;
		while ($statuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			if ($rowx[0] != 'CBHOLD') {$dial_statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
			$statname_list["$rowx[0]"] = "$rowx[1]";
			$LRstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>\n";
			if (eregi("Y",$rowx[2]))
				{$HKstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>\n";}
			$o++;
			}

		$stmt="SELECT * from vicidial_campaign_statuses where campaign_id='$campaign_id' order by status";
		$rslt=mysql_query($stmt, $link);
		$Cstatuses_to_print = mysql_num_rows($rslt);

		$o=0;
		while ($Cstatuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			if ($rowx[0] != 'CBHOLD') {$dial_statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";}
			$statname_list["$rowx[0]"] = "$rowx[1]";
			$LRstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>\n";
			if (eregi("Y",$rowx[2]))
				{$HKstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>\n";}
			$o++;
			}

		$dial_statuses = preg_replace("/ -$/","",$dial_statuses);
		$Dstatuses = explode(" ", $dial_statuses);
		$Ds_to_print = (count($Dstatuses) -1);

	$stmt="SELECT count(*) from vicidial_campaigns_list_mix where campaign_id='$campaign_id' and status='ACTIVE'";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	if ($rowx[0] < 1)
		{
		$mixes_list="<option SELECTED value=\"DISABLED\">DISABLED</option>\n";
		$mixname_list["DISABLED"] = "DISABLED";
		}
	else
		{
		##### get list_mix listings for dynamic pulldown
		$stmt="SELECT vcl_id,vcl_name from vicidial_campaigns_list_mix where campaign_id='$campaign_id' and status='ACTIVE' limit 1";
		$rslt=mysql_query($stmt, $link);
		$mixes_to_print = mysql_num_rows($rslt);
		$mixes_list="<option value=\"DISABLED\">DISABLED</option>\n";

		$o=0;
		while ($mixes_to_print > $o)
			{
			$rowx=mysql_fetch_row($rslt);
			$mixes_list .= "<option value=\"ACTIVE\">ACTIVE ($rowx[0] - $rowx[1])</option>\n";
			$mixname_list["ACTIVE"] = "$rowx[0] - $rowx[1]";
			$o++;
			}
		}

	if ($SUB<1)		{$camp_detail_color=$subcamp_color;}
		else		{$camp_detail_color=$campaigns_color;}
	if ($SUB==22)	{$camp_statuses_color=$subcamp_color;}
		else		{$camp_statuses_color=$campaigns_color;}
	if ($SUB==23)	{$camp_hotkeys_color=$subcamp_color;}
		else		{$camp_hotkeys_color=$campaigns_color;}
	if ($SUB==25)	{$camp_recycle_color=$subcamp_color;}
		else		{$camp_recycle_color=$campaigns_color;}
	if ($SUB==26)	{$camp_autoalt_color=$subcamp_color;}
		else		{$camp_autoalt_color=$campaigns_color;}
	if ($SUB==27)	{$camp_pause_color=$subcamp_color;}
		else		{$camp_pause_color=$campaigns_color;}
	if ($SUB==29)	{$camp_listmix_color=$subcamp_color;}
		else		{$camp_listmix_color=$campaigns_color;}
	echo "<TABLE WIDTH=$page_width CELLPADDING=2 CELLSPACING=0><TR class='no-ul' BGCOLOR=\"$campaigns_color\">\n";
	echo "<TD><!-- font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\"> <B>$row[0]</B>: </font --></TD>";
	echo "<TD align=center BGCOLOR=\"$camp_detail_color\"><a href=\"$PHP_SELF?ADD=34&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Basic View </font></a></TD>";
	echo "<TD align=center> <a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Detail View</font></a> </TD>";
	echo "<TD align=center BGCOLOR=\"$camp_listmix_color\"> <a href=\"$PHP_SELF?ADD=34&SUB=29&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">List Mix</font></a> </TD>";
	echo "<TD align=center> <a href=\"./AST_timeonVDADall.php?RR=4&DB=0&group=$row[0]\"><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\">Real-Time Screen</font></a></TD>\n";
	echo "<TD WIDTH=300><font size=2 color=$subcamp_font face=\"ARIAL,HELVETICA\"> &nbsp; </font></TD>\n";
	echo "</TR></TABLE>\n";

	if ($SUB < 1)
		{
		echo "<TABLE align=center><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
		echo "<center><br><font color=navy size=+1>MODIFY CAMPAIGN</font><form action=$PHP_SELF method=POST></center>\n";
		echo "<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=ADD value=44>\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "<TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign ID: </td><td align=left><b>$row[0]</b>$NWB#osdial_campaigns-campaign_id$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Name: </td><td align=left><input type=text name=campaign_name size=40 maxlength=40 value=\"$row[1]\">$NWB#osdial_campaigns-campaign_name$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Description: </td><td align=left>$row[57]$NWB#osdial_campaigns-campaign_description$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Change Date: </td><td align=left>$campaign_changedate &nbsp; $NWB#osdial_campaigns-campaign_changedate$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign Login Date: </td><td align=left>$campaign_logindate &nbsp; $NWB#osdial_campaigns-campaign_logindate$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$row[2]</option></select>$NWB#osdial_campaigns-active$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Park Extension: </td><td align=left>$row[9] - $row[10]$NWB#osdial_campaigns-park_ext$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 1: </td><td align=left>$row[11]$NWB#osdial_campaigns-web_form_address$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 2: </td><td align=left>$row[68]$NWB#osdial_campaigns-web_form_address$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Allow Closers: </td><td align=left>$row[12] $NWB#osdial_campaigns-allow_closers$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Default Transfer Group: </td><td align=left>$default_xfer_group $NWB#osdial_campaigns-default_xfer_group$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Allow Inbound and Blended: </td><td align=left>$campaign_allow_inbound $NWB#osdial_campaigns-campaign_allow_inbound$NWE</td></tr>\n";

		$o=0;
		while ($Ds_to_print > $o) 
			{
			$o++;
			$Dstatus = $Dstatuses[$o];

			echo "<tr bgcolor=#C1D6DF><td align=right>Dial Status $o: </td><td align=left> \n";
			if ($DEFstatusDISABLED > 0)
				{
				echo "<font color=grey><DEL><b>$Dstatus</b> - $statname_list[$Dstatus] &nbsp; &nbsp; &nbsp; &nbsp; <font size=2>\n";
				echo "REMOVE</DEL></td></tr>\n";
				}
			else
				{
				echo "<b>$Dstatus</b> - $statname_list[$Dstatus] &nbsp; &nbsp; &nbsp; &nbsp; <font size=2>\n";
				echo "<a href=\"$PHP_SELF?ADD=68&campaign_id=$campaign_id&status=$Dstatuses[$o]\">REMOVE</a></td></tr>\n";
				}
			}

		echo "<tr bgcolor=#C1D6DF><td align=right>Add A Dial Status: </td><td align=left><select size=1 name=dial_status $DEFlistDISABLE>\n";
		echo "<option value=\"\"> - NONE - </option>\n";

		echo "$dial_statuses_list";
		echo "</select> &nbsp; \n";
		echo "<input type=submit name=submit value=ADD> &nbsp; &nbsp; $NWB#osdial_campaigns-dial_status$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>List Order: </td><td align=left><select size=1 name=lead_order ><option>DOWN</option><option>UP</option><option>DOWN PHONE</option><option>UP PHONE</option><option>DOWN LAST NAME</option><option>UP LAST NAME</option><option>DOWN COUNT</option><option>UP COUNT</option><option>DOWN 2nd NEW</option><option>DOWN 3rd NEW</option><option>DOWN 4th NEW</option><option>DOWN 5th NEW</option><option>DOWN 6th NEW</option><option>UP 2nd NEW</option><option>UP 3rd NEW</option><option>UP 4th NEW</option><option>UP 5th NEW</option><option>UP 6th NEW</option><option>DOWN PHONE 2nd NEW</option><option>DOWN PHONE 3rd NEW</option><option>DOWN PHONE 4th NEW</option><option>DOWN PHONE 5th NEW</option><option>DOWN PHONE 6th NEW</option><option>UP PHONE 2nd NEW</option><option>UP PHONE 3rd NEW</option><option>UP PHONE 4th NEW</option><option>UP PHONE 5th NEW</option><option>UP PHONE 6th NEW</option><option>DOWN LAST NAME 2nd NEW</option><option>DOWN LAST NAME 3rd NEW</option><option>DOWN LAST NAME 4th NEW</option><option>DOWN LAST NAME 5th NEW</option><option>DOWN LAST NAME 6th NEW</option><option>UP LAST NAME 2nd NEW</option><option>UP LAST NAME 3rd NEW</option><option>UP LAST NAME 4th NEW</option><option>UP LAST NAME 5th NEW</option><option>UP LAST NAME 6th NEW</option><option>DOWN COUNT 2nd NEW</option><option>DOWN COUNT 3rd NEW</option><option>DOWN COUNT 4th NEW</option><option>DOWN COUNT 5th NEW</option><option>DOWN COUNT 6th NEW</option><option>UP COUNT 2nd NEW</option><option>UP COUNT 3rd NEW</option><option>UP COUNT 4th NEW</option><option>UP COUNT 5th NEW</option><option>UP COUNT 6th NEW</option><option SELECTED>$lead_order</option></select>$NWB#osdial_campaigns-lead_order$NWE</td></tr>\n";
		#echo "<tr bgcolor=#C1D6DF><td align=right>List Order: </td><td align=left><select size=1 name=lead_order><option>DOWN</option><option>UP</option><option>UP PHONE</option><option>DOWN PHONE</option><option>UP LAST NAME</option><option>DOWN LAST NAME</option><option>UP COUNT</option><option>DOWN COUNT</option><option>DOWN COUNT 2nd NEW</option><option>DOWN COUNT 3rd NEW</option><option>DOWN COUNT 4th NEW</option><option>DOWN COUNT 5th NEW</option><option>DOWN COUNT 6th NEW</option><option SELECTED>$lead_order</option></select>$NWB#osdial_campaigns-lead_order$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaign_id&vcl_id=$list_order_mix\">List Mix</a>: </td><td align=left><select size=1 name=list_order_mix>\n";
		echo "$mixes_list";
		if (ereg("DISABLED",$list_order_mix))
			{echo "<option selected value=\"$list_order_mix\">$list_order_mix - $mixname_list[$list_order_mix]</option>\n";}
		else
			{echo "<option selected value=\"ACTIVE\">ACTIVE ($mixname_list[ACTIVE])</option>\n";}
		echo "</select>$NWB#osdial_campaigns-list_order_mix$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$lead_filter_id\">Lead Filter</a>: </td><td align=left><select size=1 name=lead_filter_id>\n";
		echo "$filters_list";
		echo "<option selected value=\"$lead_filter_id\">$lead_filter_id - $filtername_list[$lead_filter_id]</option>\n";
		echo "</select>$NWB#osdial_campaigns-lead_filter_id$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Hopper Level: </td><td align=left><select size=1 name=hopper_level><option>1</option><option>5</option><option>10</option><option>20</option><option>50</option><option>100</option><option>200</option><option>500</option><option>700</option><option>1000</option><option>2000</option><option SELECTED>$hopper_level</option></select>$NWB#osdial_campaigns-hopper_level$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Force Reset of Hopper: </td><td align=left><select size=1 name=reset_hopper><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_campaigns-force_reset_hopper$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Dial Method: </td><td align=left><select size=1 name=dial_method><option >MANUAL</option><option>RATIO</option><option>ADAPT_HARD_LIMIT</option><option>ADAPT_TAPERED</option><option>ADAPT_AVERAGE</option><option SELECTED>$dial_method</option></select>$NWB#osdial_campaigns-dial_method$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Auto Dial Level: </td><td align=left><select size=1 name=auto_dial_level><option >0</option><option>1</option><option>1.1</option><option>1.2</option><option>1.3</option><option>1.4</option><option>1.5</option><option>1.6</option><option>1.7</option><option>1.8</option><option>1.9</option><option>2.0</option><option>2.2</option><option>2.5</option><option>2.7</option><option>3.0</option><option>3.5</option><option>4.0</option><option SELECTED>$auto_dial_level</option></select>(0 = off)$NWB#osdial_campaigns-auto_dial_level$NWE</td></tr>\n";

		echo "<tr bgcolor=#9FD79F><td align=right>Adapt Intensity Modifier: </td><td align=left><select size=1 name=adaptive_intensity>\n";
		$n=40;
		while ($n>=-40)
			{
			$dtl = 'Balanced';
			if ($n<0) {$dtl = 'Less Intense';}
			if ($n>0) {$dtl = 'More Intense';}
			if ($n == $adaptive_intensity) 
				{echo "<option SELECTED value=\"$n\">$n - $dtl</option>\n";}
			else
				{echo "<option value=\"$n\">$n - $dtl</option>\n";}
			$n--;
			}
		echo "</select> $NWB#osdial_campaigns-adaptive_intensity$NWE</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=3111111&script_id=$script_id\">Script</a>: </td><td align=left>$script_id</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Get Call Launch: </td><td align=left>$get_call_launch</td></tr>\n";

		echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
		echo "</TABLE></center></FORM>\n";

		echo "<center>\n";
		echo "<br><br><b><font color=navy>LISTS WITHIN THIS CAMPAIGN: &nbsp; $NWB#osdial_campaign_lists$NWE</font></b><br>\n";
		echo "<TABLE width=400 cellspacing=3>\n";
		echo "<tr><td><font color=navy>LIST ID</font></td><td><font color=navy>LIST NAME</font></td><td><font color=navy>ACTIVE</font></td></tr>\n";

			$active_lists = 0;
			$inactive_lists = 0;
			$stmt="SELECT list_id,active,list_name from vicidial_lists where campaign_id='$campaign_id'";
			$rslt=mysql_query($stmt, $link);
			$lists_to_print = mysql_num_rows($rslt);
			$camp_lists='';

			$o=0;
			while ($lists_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$o++;
			if (ereg("Y", $rowx[1])) {$active_lists++;   $camp_lists .= "'$rowx[0]',";}
			if (ereg("N", $rowx[1])) {$inactive_lists++;}

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=311&list_id=$rowx[0]\">$rowx[0]</a></td><td><font size=1>$rowx[2]</td><td><font size=1>$rowx[1]</td></tr>\n";

			}

		echo "</table></center><br>\n";
		echo "<center><b>\n";

		$filterSQL = $filtersql_list[$lead_filter_id];
		$filterSQL = eregi_replace("^and|and$|^or|or$","",$filterSQL);
		if (strlen($filterSQL)>4)
			{$fSQL = "and $filterSQL";}
		else
			{$fSQL = '';}

			$camp_lists = eregi_replace(".$","",$camp_lists);
		echo "<font color=navy>This campaign has $active_lists active lists and $inactive_lists inactive lists</font><br><br>\n";


		if ($display_dialable_count == 'Y')
			{
			### call function to calculate and print dialable leads
			dialable_leads($DB,$link,$local_call_time,$dial_statuses,$camp_lists,$fSQL);
			echo " - <font size=1><a href=\"$PHP_SELF?ADD=34&campaign_id=$campaign_id&stage=hide_dialable\">HIDE</a></font><BR><BR>";
			}
		else
			{
			echo "<a href=\"$PHP_SELF?ADD=73&campaign_id=$campaign_id\" target=\"_blank\">Popup Dialable Leads Count</a>";
			echo " - <font size=1><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id&stage=show_dialable\">SHOW</a></font><BR><BR>";
			}



			$stmt="SELECT count(*) FROM vicidial_hopper where campaign_id='$campaign_id' and status IN('READY')";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$rowx=mysql_fetch_row($rslt);
			$hopper_leads = "$rowx[0]";

		echo "<font color=navy>This campaign has $hopper_leads leads in the dial hopper<br><br>\n";
		echo "<a href=\"./AST_VICIDIAL_hopperlist.php?group=$campaign_id\">Click here to see what leads are in the hopper right now</a><br><br>\n";
		echo "<a href=\"$PHP_SELF?ADD=81&campaign_id=$campaign_id\">Click here to see all CallBack Holds in this campaign</a><BR><BR>\n";
		echo "<a href=\"./AST_VDADstats.php?group=$campaign_id\">Click here to see a Call report for this campaign</a></font><BR><BR>\n";
		echo "</b></center>\n";

		echo "<br>\n";

		### list of agent rank or skill-level for this campaign
		echo "<center>\n";
		echo "<br><b><font color=navy>AGENT RANKS FOR THIS CAMPAIGN:</font></b><br>\n";
		echo "<TABLE width=400 cellspacing=3>\n";
		echo "<tr><td><font color=navy>USER</font></td><td> &nbsp; &nbsp; <font color=navy>RANK</font></td><td> &nbsp; &nbsp; <font color=navy>CALLS TODAY</font></td></tr>\n";

			$stmt="SELECT user,campaign_rank,calls_today from vicidial_campaign_agents where campaign_id='$campaign_id'";
			$rsltx=mysql_query($stmt, $link);
			$users_to_print = mysql_num_rows($rsltx);

			$o=0;
			while ($users_to_print > $o) {
				$rowx=mysql_fetch_row($rsltx);
				$o++;

			if (eregi("1$|3$|5$|7$|9$", $o))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=3&user=$rowx[0]\">$rowx[0]</a></td><td><font size=1>$rowx[1]</td><td><font size=1>$rowx[2]</td></tr>\n";
			}

		echo "</table></center><br>\n";


		echo "<a href=\"$PHP_SELF?ADD=52&campaign_id=$campaign_id\">LOG ALL AGENTS OUT OF THIS CAMPAIGN</a><BR><BR>\n";


		if ($LOGdelete_campaigns > 0)
			{
			echo "<br><br><a href=\"$PHP_SELF?ADD=51&campaign_id=$campaign_id\">DELETE THIS CAMPAIGN</a>\n";
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=31 or 34 and SUB=29 for list mixes
######################
if ( ( ($ADD==34) or ($ADD==31) ) and ( (!eregi("$campaign_id",$LOGallowed_campaigns)) and (!eregi("ALL-CAMPAIGNS",$LOGallowed_campaigns)) ) ) 
	{$ADD=30;}	# send to not allowed screen if not in vicidial_user_groups allowed_campaigns list

if ( ($ADD==34) or ($ADD==31) )
{
	if ($LOGmodify_campaigns==1)
	{
	##### CAMPAIGN LIST MIX SETTINGS #####
	if ($SUB==29)
		{
		##### get list_id listings for dynamic pulldown
		$stmt="SELECT list_id,list_name from vicidial_lists where campaign_id='$campaign_id' order by list_id";
		$rslt=mysql_query($stmt, $link);
		$mixlists_to_print = mysql_num_rows($rslt);
		$mixlists_list="";

		$o=0;
		while ($mixlists_to_print > $o)
			{
			$rowx=mysql_fetch_row($rslt);
			$mixlists_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$mixlistsname_list["$rowx[0]"] = "$rowx[1]";
			$o++;
			}


		echo "<br><br><b><font color=navy size=+1>LIST MIXES FOR THIS CAMPAIGN &nbsp; $NWB#osdial_campaigns-list_order_mix$NWE</font></b><br>\n";

		echo "<br><br><b><font color=red size=+1>Feature in development - NON-FUNCTIONAL!!!</font></b><br>\n";


		$stmt="SELECT * from vicidial_campaigns_list_mix where campaign_id='$campaign_id' order by status, vcl_id";
		$rslt=mysql_query($stmt, $link);
		$listmixes = mysql_num_rows($rslt);
		$o=0;
		while ($listmixes > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$vcl_id=$rowx[0];
			$o++;

			if ($o < 2)
				{$tablecolor='bgcolor="#99FF99"';   $bgcolor='bgcolor="#CCFFCC"';}
			else
				{
				if (eregi("1$|3$|5$|7$|9$", $o))
					{$tablecolor='bgcolor="#CBDCE0"';   $bgcolor='bgcolor="#C1D6DB"';} 
				else
					{$tablecolor='bgcolor="#C1D6DB"';   $bgcolor='bgcolor="#CBDCE0"';}
				}
			echo "<a name=\"$vcl_id\"><BR>\n";
			echo "<span id=\"LISTMIX$US$vcl_id$US$o\">";
			echo "<TABLE width=740 cellspacing=3 $tablecolor>\n";
			echo "<tr><td colspan=6>\n";
			echo "<form action=\"$PHP_SELF#$vcl_id\" method=POST name=$vcl_id id=$vcl_id>\n";
			echo "<input type=hidden name=ADD value=49>\n";
			echo "<input type=hidden name=SUB value=29>\n";
			echo "<input type=hidden name=stage value=\"MODIFY\">\n";
			echo "<input type=hidden name=vcl_id value=\"$vcl_id\">\n";
			echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
			echo "<input type=hidden name=list_mix_container$US$vcl_id id=list_mix_container$US$vcl_id value=\"\">\n";
			echo "<B>$vcl_id:</B>\n";
			echo "<input type=text size=40 maxlength=50 name=vcl_name$US$vcl_id id=vcl_name$US$vcl_id value=\"$rowx[1]\">\n";
			echo " &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=49&SUB=29&stage=DELMIX&campaign_id=$campaign_id&vcl_id=$vcl_id\">DELETE LIST MIX</a></td></tr>\n";
			echo "<tr><td colspan=3>Status: \n";
			if ($rowx[5]=='INACTIVE')
				{
				echo "<B>$rowx[5]</B>\n";
				echo "<a href=\"$PHP_SELF?ADD=49&SUB=29&stage=SETACTIVE&campaign_id=$campaign_id&vcl_id=$vcl_id\"><font size=1>SET TO ACTIVE</font></a></td>\n";
				}
			else
				{echo "<B>$rowx[5]</B></td>\n";}
			echo "<td colspan=3>Method:\n";
			echo "<select size=1 name=mix_method$US$vcl_id id=method$US$vcl_id><option value=\"EVEN_MIX\">EVEN_MIX</option><option value=\"IN_ORDER\">IN_ORDER</option><option value=\"RANDOM\">RANDOM</option><option SELECTED value=\"$rowx[4]\">$rowx[4]</option></select></td></tr>\n";
			echo "<tr><td>LIST ID</td><td>PRIORITY</td><td>% MIX</td><td>STATUSES</td><td></td></tr>\n";

# list_id|order|percent|statuses|:list_id|order|percent|statuses|:...
# 101|1|40| A B NA -|:102|2|25| NEW -|:103|3|30| DROP CALLBK -|:101|4|5| DROP -|
# INSERT INTO vicidial_campaigns_list_mix values('TESTMIX','TESTCAMP List Mix','TESTCAMP','101|1|40| A B NA -|:102|2|25| NEW -|:103|3|30| DROP CALLBK -|:101|4|5| DROP -|','IN_ORDER','ACTIVE');
# INSERT INTO vicidial_campaigns_list_mix values('TESTMIX2','TESTCAMP List Mix2','TESTCAMP','101|1|20| A B -|:102|2|45| NEW -|:103|3|30| DROP CALLBK -|:101|4|5| DROP -|','IN_ORDER','ACTIVE');
# INSERT INTO vicidial_campaigns_list_mix values('TESTMIX3','TESTCAMP List Mix3','TESTCAMP','101|1|30| A NA -|:102|2|35| NEW -|:103|3|30| DROP CALLBK -|:101|4|5| DROP -|','IN_ORDER','ACTIVE');

			$MIXentries = $MT;
			$MIXentries = explode(":", $rowx[3]);
			$Ms_to_print = (count($MIXentries) - 0);
			$q=0;
			while ($Ms_to_print > $q) 
				{
				$MIXdetails = explode('|', $MIXentries[$q]);
				$MIXdetailsLIST = $MIXdetails[0];

				$dial_statuses = preg_replace("/ -$/","",$dial_statuses);
				$Dstatuses = explode(" ", $dial_statuses);
				$Ds_to_print = (count($Dstatuses) - 0);
				$Dsql = '';
				$r=0;
				while ($Ds_to_print > $r) 
					{
					$r++;
					$Dsql .= "'$Dstatuses[$r]',";
					}
				$Dsql = preg_replace("/,$/","",$Dsql);

				echo "<tr $bgcolor><td NOWRAP><font size=3>\n";
				echo "<input type=hidden name=list_id$US$q$US$vcl_id id=list_id$US$q$US$vcl_id value=$MIXdetailsLIST>\n";
				echo "<a href=\"$PHP_SELF?ADD=311&list_id=$MIXdetailsLIST\">List</a>: $MIXdetailsLIST &nbsp; <font size=1><a href=\"$PHP_SELF?ADD=49&SUB=29&stage=REMOVE&campaign_id=$campaign_id&vcl_id=$vcl_id&mix_container_item=$q&list_id=$MIXdetailsLIST#$vcl_id\">REMOVE</a></font></td>\n";

				echo "<td><select size=1 name=priority$US$q$US$vcl_id id=priority$US$q$US$vcl_id>\n";
				$n=10;
				while ($n>=1)
					{
					echo "<option value=\"$n\">$n</option>\n";
					$n = ($n-1);
					}
				echo "<option SELECTED value=\"$MIXdetails[1]\">$MIXdetails[1]</option></select></td>\n";

				echo "<td><select size=1 name=\"percentage$US$q$US$vcl_id\" id=\"percentage$US$q$US$vcl_id\" onChange=\"mod_mix_percent('$vcl_id','$Ms_to_print')\">\n";
				$n=100;
				while ($n>=0)
					{
					echo "<option value=\"$n\">$n</option>\n";
					$n = ($n-5);
					}
				echo "<option SELECTED value=\"$MIXdetails[2]\">$MIXdetails[2]</option></select></td>\n";

				
				echo "<td><input type=hidden name=status$US$q$US$vcl_id id=status$US$q$US$vcl_id value=\"$MIXdetails[3]\"><input type=text size=20 maxlength=255 name=ROstatus$US$q$US$vcl_id id=ROstatus$US$q$US$vcl_id value=\"$MIXdetails[3]\" READONLY></td>\n";
				echo "<td NOWRAP>\n";



				echo "<select size=1 name=dial_status$US$q$US$vcl_id id=dial_status$US$q$US$vcl_id>\n";
				echo "<option value=\"\"> - Select A Status - </option>\n";

				echo "$dial_statuses_list";
				echo "</select> <font size=2><B>\n";
				echo "<a href=\"#\" onclick=\"mod_mix_status('ADD','$vcl_id','$q');return false;\">ADD</a> &nbsp; \n";
				echo "<a href=\"#\" onclick=\"mod_mix_status('REMOVE','$vcl_id','$q');return false;\">REMOVE</a>\n";
				echo "</font></B></td></tr>\n";


				echo "</td></tr>\n";


				$q++;

				}



			
			echo "<tr $bgcolor><td colspan=3 align=right><font size=2>\n";
			echo "Difference %: <input type=text size=4 name=PCT_DIFF_$vcl_id id=PCT_DIFF_$vcl_id value=0 readonly>\n";
			echo "</td>\n";

			echo "<td colspan=2><input type=button name=submit_$vcl_id id=submit_$vcl_id value=\"SUBMIT\" onClick=\"submit_mix('$vcl_id','$Ms_to_print')\"> &nbsp; \n";
			echo "<span id=ERROR_$vcl_id></span>\n";
			echo "</form></td></tr>\n";


			echo "<tr $bgcolor><td colspan=4 align=right><font size=2>\n";
			echo "<form action=\"$PHP_SELF#$vcl_id\" method=POST name=$vcl_id id=$vcl_id>\n";
			echo "<input type=hidden name=ADD value=49>\n";
			echo "<input type=hidden name=SUB value=29>\n";
			echo "<input type=hidden name=stage value=\"ADD\">\n";
			echo "<input type=hidden name=vcl_id value=\"$vcl_id\">\n";
			echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
			echo "List: <select size=1 name=list_id>\n";
			echo "$mixlists_list";
			echo "<option selected value=\"\">ADD ANOTHER ENTRY</option>\n";
			echo "</select></td>\n";
			
			if ($q > 9) {$AE_disabled = 'DISABLED';}
			else {$AE_disabled = '';}
			echo "<td><input type=submit name=submit value=\"ADD ENTRY\" $AE_disabled>\n";
			echo "</form></td></tr>\n";
			echo "</table></span>\n";
			}


		echo "<br><br><B><font color=navy>ADD NEW LIST MIX</font></B><BR><form action=$PHP_SELF method=POST>\n";
		echo "<table border=0>\n";
		echo "<tr $bgcolor><td><form action=\"$PHP_SELF#$vcl_id\" method=POST>\n";
		echo "<input type=hidden name=ADD value=49>\n";
		echo "<input type=hidden name=SUB value=29>\n";
		echo "<input type=hidden name=stage value=\"NEWMIX\">\n";
		echo "<input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "Mix ID: <input type=text size=20 maxlength=20 name=vcl_id value=\"\"></td>\n";
		echo "<td>Mix Name: <input type=text size=30 maxlength=50 name=vcl_name value=\"\"></td>\n";
		echo "<td>Mix Method: ";
		echo "<select size=1 name=mix_method><option value=\"EVEN_MIX\">EVEN_MIX</option><option value=\"IN_ORDER\">IN_ORDER</option><option value=\"RANDOM\">RANDOM</option></select></td></tr>\n";
		echo "<tr $bgcolor><td>List: <select size=1 name=list_id>\n";
		echo "$mixlists_list";
		echo "</select></td>\n";
		echo "<td>Dial Status: <select size=1 name=status>\n";
		echo "$dial_statuses_list";
		echo "</select></td>\n";
		echo "<td> &nbsp; <input type=submit name=submit value=SUBMIT></form></td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<br>\n";

		}
	}
}


######################
# ADD=30 campaign not allowed
######################

if ($ADD==30)
{
echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
echo "<font color=red>You do not have permission to view campaign $campaign_id</font>\n";
}


######################
# ADD=32 display all campaign statuses
######################
if ($ADD==32)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>CUSTOM CAMPAIGN STATUSES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=#716A5B>\n";
echo "<td><font color=white size=1>CAMPAIGN</font></td>\n";
echo "<td><font color=white size=1>NAME</font></td>\n";
echo "<td><font color=white size=1>STATUSES</font></td>\n";
echo "<td><font color=white size=1>LINKS</font></td>\n";
echo "</tr>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		$campaigns_id_list[$o] = $row[0];
		$campaigns_name_list[$o] = $row[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaigns_id_list[$o]\">$campaigns_id_list[$o]</a></td>";
		echo "<td><font size=1> $campaigns_name_list[$o] </td>";
		echo "<td><font size=1> ";

		$stmt="SELECT status from vicidial_campaign_statuses where campaign_id='$campaigns_id_list[$o]' order by status";
		$rslt=mysql_query($stmt, $link);
		$campstatus_to_print = mysql_num_rows($rslt);
		$p=0;
		while ( ($campstatus_to_print > $p) and ($p < 10) )
			{
			$row=mysql_fetch_row($rslt);
			echo "$row[0] ";
			$p++;
			}
		if ($p<1) 
			{echo "<font color=grey><DEL>NONE</DEL></font>";}
		echo "</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaigns_id_list[$o]\">MODIFY STATUSES</a></td></tr>\n";
		$o++;
		}

echo "</TABLE></center>\n";
}


######################
# ADD=33 display all campaign hotkeys
######################
if ($ADD==33)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>CAMPAIGN HOTKEYS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>\n";
echo "<td><font color=white size=1>CAMPAIGN</font></td>\n";
echo "<td><font color=white size=1>NAME</font></td>\n";
echo "<td><font color=white size=1>HOTKEYS</font></td>\n";
echo "<td><font color=white size=1>LINKS</font></td>\n";
echo "</tr>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		$campaigns_id_list[$o] = $row[0];
		$campaigns_name_list[$o] = $row[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=23&campaign_id=$campaigns_id_list[$o]\">$campaigns_id_list[$o]</a></td>";
		echo "<td><font size=1> $campaigns_name_list[$o] </td>";
		echo "<td><font size=1> ";

		$stmt="SELECT status from vicidial_campaign_hotkeys where campaign_id='$campaigns_id_list[$o]' order by status";
		$rslt=mysql_query($stmt, $link);
		$campstatus_to_print = mysql_num_rows($rslt);
		$p=0;
		while ( ($campstatus_to_print > $p) and ($p < 10) )
			{
			$row=mysql_fetch_row($rslt);
			echo "$row[0] ";
			$p++;
			}
		if ($p<1) 
			{echo "<font color=grey><DEL>NONE</DEL></font>";}
		echo "</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=23&campaign_id=$campaigns_id_list[$o]\">MODIFY HOTKEYS</a></td></tr>\n";
		$o++;
		}

echo "</TABLE></center>\n";
}


######################
# ADD=35 display all campaign lead recycle entries
######################
if ($ADD==35)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>CAMPAIGN LEAD RECYCLES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>\n";
echo "<td><font color=white size=1>CAMPAIGN</font></td>\n";
echo "<td><font color=white size=1>NAME</font></td>\n";
echo "<td><font color=white size=1>LEAD RECYCLES</font></td>\n";
echo "<td><font color=white size=1>LINKS</font></td>\n";
echo "</tr>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		$campaigns_id_list[$o] = $row[0];
		$campaigns_name_list[$o] = $row[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=25&campaign_id=$campaigns_id_list[$o]\">$campaigns_id_list[$o]</a></td>";
		echo "<td><font size=1> $campaigns_name_list[$o] </td>";
		echo "<td><font size=1> ";

		$stmt="SELECT status from vicidial_lead_recycle where campaign_id='$campaigns_id_list[$o]' order by status";
		$rslt=mysql_query($stmt, $link);
		$campstatus_to_print = mysql_num_rows($rslt);
		$p=0;
		while ( ($campstatus_to_print > $p) and ($p < 10) )
			{
			$row=mysql_fetch_row($rslt);
			echo "$row[0] ";
			$p++;
			}
		if ($p<1) 
			{echo "<font color=grey><DEL>NONE</DEL></font>";}
		echo "</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=25&campaign_id=$campaigns_id_list[$o]\">MODIFY LEAD RECYCLES</a></td></tr>\n";
		$o++;
		}

echo "</TABLE></center>\n";
}


######################
# ADD=36 display all campaign auto-alt dial entries
######################
if ($ADD==36)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>CAMPAIGN LEAD AUTO-ALT DIALS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>\n";
echo "<td><font color=white size=1>CAMPAIGN</font></td>\n";
echo "<td><font color=white size=1>NAME</font></td>\n";
echo "<td><font color=white size=1>AUTO-ALT DIAL</font></td>\n";
echo "<td><font color=white size=1>LINKS</font></td>\n";
echo "</tr>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		$campaigns_id_list[$o] = $row[0];
		$campaigns_name_list[$o] = $row[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=26&campaign_id=$campaigns_id_list[$o]\">$campaigns_id_list[$o]</a></td>";
		echo "<td><font size=1> $campaigns_name_list[$o] </td>";
		echo "<td><font size=1> ";

		$stmt="SELECT auto_alt_dial_statuses from vicidial_campaigns where campaign_id='$campaigns_id_list[$o]';";
		$rslt=mysql_query($stmt, $link);
		$campstatus_to_print = mysql_num_rows($rslt);
		$p=0;
		while ( ($campstatus_to_print > $p) and ($p < 10) )
			{
			$row=mysql_fetch_row($rslt);
			echo "$row[0] ";
			$p++;
			}
		if (strlen($row[0])<3) 
			{echo "<font color=grey><DEL>NONE</DEL></font>";}
		echo "</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=26&campaign_id=$campaigns_id_list[$o]\">MODIFY AUTO-ALT DIAL</a></td></tr>\n";
		$o++;
		}

echo "</TABLE></center>\n";
}


######################
# ADD=37 display all campaign agent pause codes
######################
if ($ADD==37)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>CAMPAIGN AGENT PAUSE CODES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>\n";
echo "<td><font color=white size=1>CAMPAIGN</font></td>\n";
echo "<td><font color=white size=1>NAME</font></td>\n";
echo "<td><font color=white size=1>PAUSE CODES</font></td>\n";
echo "<td><font color=white size=1>LINKS</font></td>\n";
echo "</tr>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		$campaigns_id_list[$o] = $row[0];
		$campaigns_name_list[$o] = $row[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=27&campaign_id=$campaigns_id_list[$o]\">$campaigns_id_list[$o]</a></td>";
		echo "<td><font size=1> $campaigns_name_list[$o] </td>";
		echo "<td><font size=1> ";

		$stmt="SELECT pause_code from vicidial_pause_codes where campaign_id='$campaigns_id_list[$o]' order by pause_code;";
		$rslt=mysql_query($stmt, $link);
		$campstatus_to_print = mysql_num_rows($rslt);
		$p=0;
		while ( ($campstatus_to_print > $p) and ($p < 10) )
			{
			$row=mysql_fetch_row($rslt);
			echo "$row[0] ";
			$p++;
			}
		if ($p<1) 
			{echo "<font color=grey><DEL>NONE</DEL></font>";}
		echo "</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=27&campaign_id=$campaigns_id_list[$o]\">MODIFY PAUSE CODES</a></td></tr>\n";
		$o++;
		}

echo "</TABLE></center>\n";
}


######################
# ADD=39 display all campaign list mixes
######################
if ($ADD==39)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>CAMPAIGN LIST MIXES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=navy>\n";
echo "<td><font color=white size=1>CAMPAIGN</font></td>\n";
echo "<td><font color=white size=1>NAME</font></td>\n";
echo "<td><font color=white size=1>LIST MIX</font></td>\n";
echo "<td><font color=white size=1>LINKS</font></td>\n";
echo "</tr>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		$campaigns_id_list[$o] = $row[0];
		$campaigns_name_list[$o] = $row[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaigns_id_list[$o]\">$campaigns_id_list[$o]</a></td>";
		echo "<td><font size=1> $campaigns_name_list[$o] </td>";
		echo "<td><font size=1> ";

		$stmt="SELECT vcl_id from vicidial_campaigns_list_mix where campaign_id='$campaigns_id_list[$o]' order by status,vcl_id;";
		$rslt=mysql_query($stmt, $link);
		$campstatus_to_print = mysql_num_rows($rslt);
		$p=0;
		while ( ($campstatus_to_print > $p) and ($p < 10) )
			{
			$row=mysql_fetch_row($rslt);
			echo "$row[0] ";
			$p++;
			}
		if ($p<1) 
			{echo "<font color=grey><DEL>NONE</DEL></font>";}
		echo "</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaigns_id_list[$o]\">MODIFY LIST MIX</a></td></tr>\n";
		$o++;
		}

echo "</TABLE></center>\n";
}





######################
# ADD=311 modify list info in the system
######################

if ($ADD==311)
{
	if ($LOGmodify_lists==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_lists where list_id='$list_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$campaign_id = $row[2];
	$active = $row[3];
	$list_description = $row[4];
	$list_changedate = $row[5];
	$list_lastcalldate = $row[6];

	# grab names of global statuses and statuses in the selected campaign
	$stmt="SELECT * from vicidial_statuses order by status";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($statuses_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$statuses_list["$rowx[0]"] = "$rowx[1]";
		$o++;
	}

	$stmt="SELECT * from vicidial_campaign_statuses where campaign_id='$campaign_id' order by status";
	$rslt=mysql_query($stmt, $link);
	$Cstatuses_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($Cstatuses_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$statuses_list["$rowx[0]"] = "$rowx[1]";
		$o++;
	}
	# end grab status names


	echo "<center><br><font color=navy size=+1>MODIFY A LIST</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411>\n";
	echo "<input type=hidden name=list_id value=\"$row[0]\">\n";
	echo "<input type=hidden name=old_campaign_id value=\"$row[2]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List ID: </td><td align=left><b>$row[0]</b>$NWB#osdial_lists-list_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20 value=\"$row[1]\">$NWB#osdial_lists-list_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Description: </td><td align=left><input type=text name=list_description size=30 maxlength=255 value=\"$list_description\">$NWB#osdial_lists-list_description$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=34&campaign_id=$campaign_id\">Campaign</a>: </td><td align=left><select size=1 name=campaign_id>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);
	$campaigns_list='';

	$o=0;
	while ($campaigns_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
	}
	echo "$campaigns_list";
	echo "<option SELECTED>$campaign_id</option>\n";
	echo "</select>$NWB#osdial_lists-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$active</option></select>$NWB#osdial_lists-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Reset Lead-Called-Status for this list: </td><td align=left><select size=1 name=reset_list><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_lists-reset_list$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Change Date: </td><td align=left>$list_changedate &nbsp; $NWB#osdial_lists-list_changedate$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Last Call Date: </td><td align=left>$list_lastcalldate &nbsp; $NWB#osdial_lists-list_lastcalldate$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";

	echo "<center>\n";
	echo "<br><font color=navy size=+1>STATUSES WITHIN THIS LIST</font></b><br><br>\n";
	echo "<TABLE width=500 cellspacing=3>\n";
	echo "<tr><td><font color=navy>STATUS</font></td><td><font color=navy>STATUS NAME</font></td><td><font color=navy>CALLED</font></td><td><font color=navy>NOT CALLED</font></td></tr>\n";

	$leads_in_list = 0;
	$leads_in_list_N = 0;
	$leads_in_list_Y = 0;
	$stmt="SELECT status,called_since_last_reset,count(*) from vicidial_list where list_id='$list_id' group by status,called_since_last_reset order by status,called_since_last_reset";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);

	$o=0;
	$lead_list['count'] = 0;
	$lead_list['Y_count'] = 0;
	$lead_list['N_count'] = 0;
	while ($statuses_to_print > $o) 
	{
	    $rowx=mysql_fetch_row($rslt);
	    
	    $lead_list['count'] = ($lead_list['count'] + $rowx[2]);
	    if ($rowx[1] == 'N') 
	    {
		$since_reset = 'N';
		$since_resetX = 'Y';
	    }
	    else 
	    {
		$since_reset = 'Y';
		$since_resetX = 'N';
	    } 
	    $lead_list[$since_reset][$rowx[0]] = ($lead_list[$since_reset][$rowx[0]] + $rowx[2]);
	    $lead_list[$since_reset.'_count'] = ($lead_list[$since_reset.'_count'] + $rowx[2]);
	    #If opposite side is not set, it may not in the future so give it a value of zero
	    if (!isset($lead_list[$since_resetX][$rowx[0]])) 
	    {
		$lead_list[$since_resetX][$rowx[0]]=0;
	    }
	    $o++;
	}
 
	$o=0;
	if ($lead_list['count'] > 0)
	{
		while (list($dispo,) = each($lead_list[$since_reset]))
		{

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		if ($dispo == 'CBHOLD')
			{
			$CLB="<a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">";
			$CLE="</a>";
			}
		else
			{
			$CLB='';
			$CLE='';
			}

		echo "<tr $bgcolor><td><font size=1>$CLB$dispo$CLE</td><td><font size=1>$statuses_list[$dispo]</td><td><font size=1>".$lead_list['Y'][$dispo]."</td><td><font size=1>".$lead_list['N'][$dispo]." </td></tr>\n";
		$o++;
		}
	}

	echo "<tr><td colspan=2><font size=1><font color=navy>SUBTOTALS</font></td><td><font size=1>$lead_list[Y_count]</td><td><font size=1>$lead_list[N_count]</td></tr>\n";
	echo "<tr bgcolor=\"#C1D6DB\"><td><font size=1>TOTAL</td><td colspan=3 align=center><font size=1>$lead_list[count]</td></tr>\n";

	echo "</table></center><br>\n";
	unset($lead_list);


	echo "<center>\n";
	echo "<br><font color=navy size=+1>TIME ZONES WITHIN THIS LIST</font></b><br><br>\n";
	echo "<TABLE width=500 cellspacing=3>\n";
	echo "<tr><td><font color=navy>GMT OFFSET NOW (local time)</font></td><td><font color=navy>CALLED</font></td><td><font color=navy>NOT CALLED</font></td></tr>\n";

	$stmt="SELECT gmt_offset_now,called_since_last_reset,count(*) from vicidial_list where list_id='$list_id' group by gmt_offset_now,called_since_last_reset order by gmt_offset_now,called_since_last_reset";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);

	$o=0;
	$plus='+';
	$lead_list['count'] = 0;
	$lead_list['Y_count'] = 0;
	$lead_list['N_count'] = 0;
	while ($statuses_to_print > $o) 
	{
	    $rowx=mysql_fetch_row($rslt);
	    
	    $lead_list['count'] = ($lead_list['count'] + $rowx[2]);
	    if ($rowx[1] == 'N') 
	    {
		$since_reset = 'N';
		$since_resetX = 'Y';
	    }
	    else 
	    {
		$since_reset = 'Y';
		$since_resetX = 'N';
	    } 
	    $lead_list[$since_reset][$rowx[0]] = ($lead_list[$since_reset][$rowx[0]] + $rowx[2]);
	    $lead_list[$since_reset.'_count'] = ($lead_list[$since_reset.'_count'] + $rowx[2]);
	    #If opposite side is not set, it may not in the future so give it a value of zero
	    if (!isset($lead_list[$since_resetX][$rowx[0]])) 
	    {
		$lead_list[$since_resetX][$rowx[0]]=0;
	    }
	    $o++;
	}

	if ($lead_list['count'] > 0)
	{
		while (list($tzone,) = each($lead_list[$since_reset]))
		{
		$LOCALzone=3600 * $tzone;
		$LOCALdate=gmdate("D M Y H:i", time() + $LOCALzone);

		if ($tzone >= 0) {$DISPtzone = "$plus$tzone";}
		else {$DISPtzone = "$tzone";}
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><font size=1>".$DISPtzone." &nbsp; &nbsp; ($LOCALdate)</td><td><font size=1>".$lead_list['Y'][$tzone]."</td><td><font size=1>".$lead_list['N'][$tzone]."</td></tr>\n";
		}
	}

	echo "<tr><td><font size=1><font color=navy>SUBTOTALS</font></td><td><font size=1>$lead_list[Y_count]</td><td><font size=1>$lead_list[N_count]</td></tr>\n";
	echo "<tr bgcolor=\"#C1D6DB\"><td><font size=1>TOTAL</td><td colspan=2 align=center><font size=1>$lead_list[count]</td></tr>\n";

	echo "</table></center><br>\n";
	unset($lead_list);



	$leads_in_list = 0;
	$leads_in_list_N = 0;
	$leads_in_list_Y = 0;
	$stmt="SELECT status,called_count,count(*) from vicidial_list where list_id='$list_id' group by status,called_count order by status,called_count";
	$rslt=mysql_query($stmt, $link);
	$status_called_to_print = mysql_num_rows($rslt);

	$o=0;
	$sts=0;
	$first_row=1;
	$all_called_first=1000;
	$all_called_last=0;
	while ($status_called_to_print > $o) 
	{
	$rowx=mysql_fetch_row($rslt);
	$leads_in_list = ($leads_in_list + $rowx[2]);
	$count_statuses[$o]			= "$rowx[0]";
	$count_called[$o]			= "$rowx[1]";
	$count_count[$o]			= "$rowx[2]";
	$all_called_count[$rowx[1]] = ($all_called_count[$rowx[1]] + $rowx[2]);

	if ( (strlen($status[$sts]) < 1) or ($status[$sts] != "$rowx[0]") )
		{
		if ($first_row) {$first_row=0;}
		else {$sts++;}
		$status[$sts] = "$rowx[0]";
		$status_called_first[$sts] = "$rowx[1]";
		if ($status_called_first[$sts] < $all_called_first) {$all_called_first = $status_called_first[$sts];}
		}
	$leads_in_sts[$sts] = ($leads_in_sts[$sts] + $rowx[2]);
	$status_called_last[$sts] = "$rowx[1]";
	if ($status_called_last[$sts] > $all_called_last) {$all_called_last = $status_called_last[$sts];}

	$o++;
	}




	echo "<center>\n";
	echo "<br><font color=navy size=+1>CALLED COUNTS WITHIN THIS LIST</font></b><br><br>\n";
	echo "<TABLE width=500 cellspacing=1>\n";
	echo "<tr><td align=left><font size=1 color=navy>STATUS</td><td align=center><font size=1 color=navy>STATUS NAME</td>";
	$first = $all_called_first;
	while ($first <= $all_called_last)
		{
		if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='bgcolor="#AFEEEE"';} 
		else{$AB='bgcolor="#E0FFFF"';}
		echo "<td align=center $AB><font size=1>$first</td>";
		$first++;
		}
	echo "<td align=center><font size=1 color=navy>SUBTOTAL</td></tr>\n";

		$sts=0;
		$statuses_called_to_print = count($status);
		while ($statuses_called_to_print > $sts) 
		{
		$Pstatus = $status[$sts];
		if (eregi("1$|3$|5$|7$|9$", $sts))
			{$bgcolor='bgcolor="#CBDCE0"';   $AB='bgcolor="#C1D6DB"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';   $AB='bgcolor="#CBDCE0"';}
	#	echo "$status[$sts]|$status_called_first[$sts]|$status_called_last[$sts]|$leads_in_sts[$sts]|\n";
	#	echo "$status[$sts]|";
		echo "<tr $bgcolor><td><font size=1>$Pstatus</td><td><font size=1>$statuses_list[$Pstatus]</td>";

		$first = $all_called_first;
		while ($first <= $all_called_last)
			{
			if (eregi("1$|3$|5$|7$|9$", $sts))
				{
				if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='bgcolor="#C1D6DB"';} 
				else{$AB='bgcolor="#CBDCE0"';}
				}
			else
				{
				if (eregi("0$|2$|4$|6$|8$", $first)) {$AB='bgcolor="#C1D6DB"';} 
				else{$AB='bgcolor="#CBDCE0"';}
				}

			$called_printed=0;
			$o=0;
			while ($status_called_to_print > $o) 
				{
				if ( ($count_statuses[$o] == "$Pstatus") and ($count_called[$o] == "$first") )
					{
					$called_printed++;
					echo "<td $AB><font size=1> $count_count[$o]</td>";
					}


				$o++;
				}
			if (!$called_printed) 
				{echo "<td $AB><font size=1> &nbsp;</td>";}
			$first++;
			}
		echo "<td><font size=1>$leads_in_sts[$sts]</td></tr>\n\n";

		$sts++;
		}

	echo "<tr><td align=center colspan=2><b><font size=1><font color=navy>TOTAL</font></td>";
	$first = $all_called_first;
	while ($first <= $all_called_last)
		{
		if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='bgcolor="#AFEEEE"';} 
		else{$AB='bgcolor="#E0FFFF"';}
		echo "<td align=center $AB><b><font size=1>$all_called_count[$first]</td>";
		$first++;
		}
	echo "<td align=center><b><font size=1>$leads_in_list</td></tr>\n";

	echo "</table></center><br>\n";





	echo "<center>\n";
	echo "<br><br><a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">Click here to see all CallBack Holds in this list</a><BR><BR>\n";
	echo "</center>\n";
	
	if ($LOGdelete_lists > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=511&list_id=$list_id\">DELETE THIS LIST</a>\n";
    echo "<br><br><a href=\"$PHP_SELF?ADD=511&SUB=1&list_id=$list_id\">DELETE THIS LIST AND ITS LEADS</a> (WARNING: Will damage call-backs made in this list!)\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}



######################
# ADD=3111 modify in-group info in the system
######################

if ($ADD==3111)
{
	if ($LOGmodify_ingroups==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_inbound_groups where group_id='$group_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$group_name =				$row[1];
	$group_color =				$row[2];
	$active =					$row[3];
	$web_form_address =			$row[4];
	$voicemail_ext =			$row[5];
	$next_agent_call =			$row[6];
	$fronter_display =			$row[7];
	$script_id =				$row[8];
	$get_call_launch =			$row[9];
	$xferconf_a_dtmf =			$row[10];
	$xferconf_a_number =		$row[11];
	$xferconf_b_dtmf =			$row[12];
	$xferconf_b_number =		$row[13];
	$drop_call_seconds =		$row[14];
	$drop_message =				$row[15];
	$drop_exten =				$row[16];
	$call_time_id =				$row[17];
	$after_hours_action =		$row[18];
	$after_hours_message_filename =	$row[19];
	$after_hours_exten =		$row[20];
	$after_hours_voicemail =	$row[21];
	$welcome_message_filename =	$row[22];
	$moh_context =				$row[23];
	$onhold_prompt_filename =	$row[24];
	$prompt_interval =			$row[25];
	$agent_alert_exten =		$row[26];
	$agent_alert_delay =		$row[27];
	$default_xfer_group =		$row[28];
	$web_form_address2 =		$row[29];

	##### get in-groups listings for dynamic pulldown
	$stmt="SELECT group_id,group_name from vicidial_inbound_groups order by group_id";
	$rslt=mysql_query($stmt, $link);
	$Xgroups_to_print = mysql_num_rows($rslt);
	$Xgroups_menu='';
	$Xgroups_selected=0;
	$o=0;
	while ($Xgroups_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$Xgroups_menu .= "<option ";
		if ($default_xfer_group == "$rowx[0]") 
			{
			$Xgroups_menu .= "SELECTED ";
			$Xgroups_selected++;
			}
		$Xgroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
		}
	if ($Xgroups_selected < 1) 
		{$Xgroups_menu .= "<option SELECTED value=\"---NONE---\">---NONE---</option>\n";}
	else 
		{$Xgroups_menu .= "<option value=\"---NONE---\">---NONE---</option>\n";}


	echo "<center><br><font color=navy size=+1>MODIFY AN IN-GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=4111>\n";
	echo "<input type=hidden name=group_id value=\"$row[0]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group ID: </td><td align=left><b>$row[0]</b>$NWB#osdial_inbound_groups-group_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group Name: </td><td align=left><input type=text name=group_name size=30 maxlength=30 value=\"$row[1]\">$NWB#osdial_inbound_groups-group_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group Color: </td><td align=left bgcolor=\"$row[2]\" id=\"group_color_td\"><input type=text name=group_color size=7 maxlength=7 value=\"$row[2]\">$NWB#osdial_inbound_groups-group_color$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$active</option></select>$NWB#osdial_inbound_groups-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 1: </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"$web_form_address\">$NWB#osdial_inbound_groups-web_form_address$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Web Form 2: </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"$web_form_address2\">$NWB#osdial_inbound_groups-web_form_address$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Next Agent Call: </td><td align=left><select size=1 name=next_agent_call><option >random</option><option>oldest_call_start</option><option>oldest_call_finish</option><option>overall_user_level</option><option>inbound_group_rank</option><option>campaign_rank</option><option>fewest_calls</option><option>fewest_calls_campaign</option><option SELECTED>$next_agent_call</option></select>$NWB#osdial_inbound_groups-next_agent_call$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Fronter Display: </td><td align=left><select size=1 name=fronter_display><option>Y</option><option>N</option><option SELECTED>$fronter_display</option></select>$NWB#osdial_inbound_groups-fronter_display$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=3111111&script_id=$script_id\">Script</a>: </td><td align=left><select size=1 name=script_id>\n";
	echo "$scripts_list";
	echo "<option selected value=\"$script_id\">$script_id - $scriptname_list[$script_id]</option>\n";
	echo "</select>$NWB#osdial_inbound_groups-ingroup_script$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Get Call Launch: </td><td align=left><select size=1 name=get_call_launch><option selected>NONE</option><option>SCRIPT</option><option>WEBFORM</option><option selected>$get_call_launch</option></select>$NWB#osdial_inbound_groups-get_call_launch$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Transfer-Conf DTMF 1: </td><td align=left><input type=text name=xferconf_a_dtmf size=20 maxlength=50 value=\"$xferconf_a_dtmf\">$NWB#osdial_inbound_groups-xferconf_a_dtmf$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Transfer-Conf Number 1: </td><td align=left><input type=text name=xferconf_a_number size=20 maxlength=50 value=\"$xferconf_a_number\">$NWB#osdial_inbound_groups-xferconf_a_dtmf$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Transfer-Conf DTMF 2: </td><td align=left><input type=text name=xferconf_b_dtmf size=20 maxlength=50 value=\"$xferconf_b_dtmf\">$NWB#osdial_inbound_groups-xferconf_a_dtmf$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Transfer-Conf Number 2: </td><td align=left><input type=text name=xferconf_b_number size=20 maxlength=50 value=\"$xferconf_b_number\">$NWB#osdial_inbound_groups-xferconf_a_dtmf$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Drop Call Seconds: </td><td align=left><input type=text name=drop_call_seconds size=5 maxlength=4 value=\"$drop_call_seconds\">$NWB#osdial_inbound_groups-drop_call_seconds$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Voicemail: </td><td align=left><input type=text name=voicemail_ext size=10 maxlength=10 value=\"$voicemail_ext\">$NWB#osdial_inbound_groups-voicemail_ext$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Use Drop Message: </td><td align=left><select size=1 name=drop_message><option>Y</option><option>N</option><option SELECTED>$drop_message</option></select>$NWB#osdial_inbound_groups-drop_message$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Drop Exten: </td><td align=left><input type=text name=drop_exten size=10 maxlength=20 value=\"$drop_exten\">$NWB#osdial_inbound_groups-drop_exten$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$call_time_id\">Call Time: </a></td><td align=left><select size=1 name=call_time_id>\n";
	echo "$call_times_list";
	echo "<option selected value=\"$call_time_id\">$call_time_id - $call_timename_list[$call_time_id]</option>\n";
	echo "</select>$NWB#osdial_inbound_groups-call_time_id$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>After Hours Action: </td><td align=left><select size=1 name=after_hours_action><option>HANGUP</option><option>MESSAGE</option><option>EXTENSION</option><option>VOICEMAIL</option><option SELECTED>$after_hours_action</option></select>$NWB#osdial_inbound_groups-after_hours_action$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>After Hours Message Filename: </td><td align=left><input type=text name=after_hours_message_filename size=20 maxlength=50 value=\"$after_hours_message_filename\">$NWB#osdial_inbound_groups-after_hours_message_filename$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>After Hours Extension: </td><td align=left><input type=text name=after_hours_exten size=10 maxlength=20 value=\"$after_hours_exten\">$NWB#osdial_inbound_groups-after_hours_exten$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>After Hours Voicemail: </td><td align=left><input type=text name=after_hours_voicemail size=10 maxlength=20 value=\"$after_hours_voicemail\">$NWB#osdial_inbound_groups-after_hours_voicemail$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Welcome Message Filename: </td><td align=left><input type=text name=welcome_message_filename size=20 maxlength=50 value=\"$welcome_message_filename\">$NWB#osdial_inbound_groups-welcome_message_filename$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Music On Hold Context: </td><td align=left><input type=text name=moh_context size=10 maxlength=20 value=\"$moh_context\">$NWB#osdial_inbound_groups-moh_context$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>On Hold Prompt Filename: </td><td align=left><input type=text name=onhold_prompt_filename size=20 maxlength=50 value=\"$onhold_prompt_filename\">$NWB#osdial_inbound_groups-onhold_prompt_filename$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>On Hold Prompt Interval: </td><td align=left><input type=text name=prompt_interval size=5 maxlength=5 value=\"$prompt_interval\">$NWB#osdial_inbound_groups-prompt_interval$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Agent Alert Extension: </td><td align=left><input type=text name=agent_alert_exten size=10 maxlength=20 value=\"$agent_alert_exten\">$NWB#osdial_inbound_groups-agent_alert_exten$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Agent Alert Delay: </td><td align=left><input type=text name=agent_alert_delay size=6 maxlength=6 value=\"$agent_alert_delay\">$NWB#osdial_inbound_groups-agent_alert_delay$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>Default Transfer Group: </td><td align=left><select size=1 name=default_xfer_group>";
	echo "$Xgroups_menu";
	echo "</select>$NWB#osdial_inbound_groups-default_xfer_group$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";

	### list of agent rank or skill-level for this inbound group
	echo "<center>\n";
	echo "<br><font color=navy size=+1>AGENT RANKS FOR THIS INBOUND GROUP</font<br><br>\n";
	echo "<TABLE width=400 cellspacing=3>\n";
	echo "<tr><td><font color=navy>&nbsp;&nbsp;USER</font></td><td> &nbsp; &nbsp; <font color=navy>RANK</font></td><td> &nbsp; &nbsp; <font color=navy>CALLS TODAY</font></td></tr>\n";

		$stmt="SELECT user,group_rank,calls_today from vicidial_inbound_group_agents where group_id='$group_id'";
		$rsltx=mysql_query($stmt, $link);
		$users_to_print = mysql_num_rows($rsltx);

		$o=0;
		while ($users_to_print > $o) {
			$rowx=mysql_fetch_row($rsltx);
			$o++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=3&user=$rowx[0]\">$rowx[0]</a></td><td><font size=1>$rowx[1]</td><td><font size=1>$rowx[2]</td></tr>\n";
		}

	//echo "</table><br>\n";

	echo "</table><br><br>\n";

	echo "<a href=\"./AST_CLOSERstats.php?group=$group_id\">Click here to see a report for this inbound group</a><BR><BR>\n";

	echo "</center><br><br>\n";

	if ($LOGdelete_ingroups > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=53&campaign_id=$group_id&stage=IN\">EMERGENCY VDAC CLEAR FOR THIS IN-GROUP</a><BR><BR>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=5111&group_id=$group_id\">DELETE THIS IN-GROUP</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}



######################
# ADD=31111 modify remote agents info in the system
######################

if ($ADD==31111)
{
	if ($LOGmodify_remoteagents==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_remote_agents where remote_agent_id='$remote_agent_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$remote_agent_id =	$row[0];
	$user_start =		$row[1];
	$number_of_lines =	$row[2];
	$server_ip =		$row[3];
	$conf_exten =		$row[4];
	$status =			$row[5];
	$campaign_id =		$row[6];

	echo "<center><br><font color=navy size=+1>MODIFY A REMOTE AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111>\n";
	echo "<input type=hidden name=remote_agent_id value=\"$row[0]\">\n";
	
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Agent ID Start: </td><td align=left><input type=text name=user_start size=6 maxlength=6 value=\"$user_start\"> (numbers only, incremented)$NWB#osdial_remote_agents-user_start$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Number of Lines: </td><td align=left><input type=text name=number_of_lines size=3 maxlength=3 value=\"$number_of_lines\"> (numbers only)$NWB#osdial_remote_agents-number_of_lines$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";
	echo "$servers_list";
	echo "<option SELECTED>$row[3]</option>\n";
	echo "</select>$NWB#osdial_remote_agents-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>External Extension: </td><td align=left><input type=text name=conf_exten size=20 maxlength=20 value=\"$conf_exten\"> (dial plan number dialed to reach agents)$NWB#osdial_remote_agents-conf_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Status: </td><td align=left><select size=1 name=status><option SELECTED>ACTIVE</option><option>INACTIVE</option><option SELECTED>$status</option></select>$NWB#osdial_remote_agents-status$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
	echo "$campaigns_list";
	echo "<option SELECTED>$campaign_id</option>\n";
	echo "</select>$NWB#osdial_remote_agents-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Inbound Groups: </td><td align=left>\n";
	echo "$groups_list";
	echo "$NWB#osdial_remote_agents-closer_campaigns$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	
	echo "NOTE: It can take up to 30 seconds for changes submitted on this screen to go live\n";


	if ($LOGdelete_remote_agents > 0)
		{
		echo "<br><br><br><a href=\"$PHP_SELF?ADD=51111&remote_agent_id=$remote_agent_id\">DELETE THIS REMOTE AGENT</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=311111 modify user group info in the system
######################

if ($ADD==311111)
{
	if ($LOGmodify_usergroups==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_user_groups where user_group='$user_group';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$user_group =		$row[0];
	$group_name =		$row[1];
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>MODIFY A USER GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411111>\n";
	echo "<input type=hidden name=OLDuser_group value=\"$user_group\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Group: </td><td align=left><input type=text name=user_group size=15 maxlength=20 value=\"$user_group\"> (no spaces or punctuation)$NWB#osdial_user_groups-user_group$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Description: </td><td align=left><input type=text name=group_name size=40 maxlength=40 value=\"$group_name\"> (description of group)$NWB#osdial_user_groups-group_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Allowed Campaigns: </td><td align=left>\n";
	echo "$campaigns_list";
	echo "$NWB#osdial_user_groups-allowed_campaigns$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";


	### list of users in this user group

		$active_confs = 0;
		$stmt="SELECT user,full_name,user_level from vicidial_users where user_group='$user_group'";
		$rsltx=mysql_query($stmt, $link);
		$users_to_print = mysql_num_rows($rsltx);

		echo "<center>\n";
		echo "<br><b>AGENTS WITHIN THIS AGENTS GROUP: $users_to_print</b><br>\n";
		echo "<TABLE width=400 cellspacing=3>\n";
		echo "<tr><td>USER</td><td>FULL NAME</td><td>LEVEL</td></tr>\n";

		$o=0;
		while ($users_to_print > $o) 
		{
			$rowx=mysql_fetch_row($rsltx);
			$o++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor>\n";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=3&user=$rowx[0]\">$rowx[0]</a></td>\n";
		echo "<td><font size=1>$rowx[1]</td>\n";
		echo "<td><font size=1>$rowx[2]</td>\n";
		echo "</tr>\n";
		}

	echo "</table></center><br>\n";



	echo "<br><br><a href=\"$PHP_SELF?ADD=8111&user_group=$user_group\">Click here to see all CallBack Holds in this user group</a><BR><BR>\n";

	if ($LOGdelete_user_groups > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=511111&user_group=$user_group\">DELETE THIS USER GROUP</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=3111111 modify script info in the system
######################

if ($ADD==3111111)
{
	if ($LOGmodify_scripts==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_scripts where script_id='$script_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$script_name =		$row[1];
	$script_comments =	$row[2];
	$script_text =		$row[3];
	$active =			$row[4];
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>MODIFY A SCRIPT</font><form name=scriptForm action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=4111111>\n";
	echo "<input type=hidden name=script_id value=\"$script_id\">\n";
	echo "<TABLE>";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script ID: </td><td align=left><B>$script_id</B>$NWB#osdial_scripts-script_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script Name: </td><td align=left><input type=text name=script_name size=40 maxlength=50 value=\"$script_name\"> (title of the script)$NWB#osdial_scripts-script_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script Comments: </td><td align=left><input type=text name=script_comments size=50 maxlength=255 value=\"$script_comments\"> $NWB#osdial_scripts-script_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option><option selected>$active</option></select>$NWB#osdial_scripts-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Script Text: <BR><BR><B><a href=\"javascript:openNewWindow('$PHP_SELF?ADD=7111111&script_id=$script_id')\">Preview Script</a></B> </td><td align=left>";
	# BEGIN Insert Field
	echo "<select id=\"selectedField\" name=\"selectedField\">";
	echo "<option>vendor_lead_code</option>";
	echo "<option>source_id</option>";
	echo "<option>list_id</option>";
	echo "<option>gmt_offset_now</option>";
	echo "<option>called_since_last_reset</option>";
	echo "<option>phone_code</option>";
	echo "<option>phone_number</option>";
	echo "<option>title</option>";
	echo "<option>first_name</option>";
	echo "<option>middle_initial</option>";
	echo "<option>last_name</option>";
	echo "<option>address1</option>";
	echo "<option>address2</option>";
	echo "<option>address3</option>";
	echo "<option>city</option>";
	echo "<option>state</option>";
	echo "<option>province</option>";
	echo "<option>postal_code</option>";
	echo "<option>country_code</option>";
	echo "<option>gender</option>";
	echo "<option>date_of_birth</option>";
	echo "<option>alt_phone</option>";
	echo "<option>email</option>";
	echo "<option>security_phrase</option>";
	echo "<option>comments</option>";
	echo "<option>lead_id</option>";
	echo "<option>campaign</option>";
	echo "<option>phone_login</option>";
	echo "<option>group</option>";
	echo "<option>channel_group</option>";
	echo "<option>SQLdate</option>";
	echo "<option>epoch</option>";
	echo "<option>uniqueid</option>";
	echo "<option>customer_zap_channel</option>";
	echo "<option>server_ip</option>";
	echo "<option>SIPexten</option>";
	echo "<option>session_id</option>";
	echo "</select>";
	echo "<input type=\"button\" name=\"insertField\" value=\"Insert\" onClick=\"scriptInsertField();\"><BR>";
	# END Insert Field
	echo "<TEXTAREA NAME=script_text ROWS=20 COLS=50>$script_text</TEXTAREA> $NWB#osdial_scripts-script_text$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";

	if ($LOGdelete_scripts > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=5111111&script_id=$script_id\">DELETE THIS SCRIPT</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=31111111 modify filter info in the system
######################

if ($ADD==31111111)
{
	if ($LOGmodify_filters==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_lead_filters where lead_filter_id='$lead_filter_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$lead_filter_name =		$row[1];
	$lead_filter_comments =	$row[2];
	$lead_filter_sql =		$row[3];
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>MODIFY A FILTER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111111>\n";
	echo "<input type=hidden name=lead_filter_id value=\"$lead_filter_id\">\n";
	echo "<TABLE>";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter ID: </td><td align=left><B>$lead_filter_id</B>$NWB#osdial_lead_filters-lead_filter_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter Name: </td><td align=left><input type=text name=lead_filter_name size=40 maxlength=50 value=\"$lead_filter_name\"> (short description of the filter)$NWB#osdial_lead_filters-lead_filter_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter Comments: </td><td align=left><input type=text name=lead_filter_comments size=50 maxlength=255 value=\"$lead_filter_comments\"> $NWB#osdial_lead_filters-lead_filter_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter SQL:</td><td align=left><TEXTAREA NAME=lead_filter_sql ROWS=20 COLS=50>$lead_filter_sql</TEXTAREA> $NWB#osdial_lead_filters-lead_filter_sql$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</table></form>\n";

		##### get campaigns listing for dynamic pulldown
		$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
		$rslt=mysql_query($stmt, $link);
		$campaigns_to_print = mysql_num_rows($rslt);
		$campaigns_list='';

		$o=0;
		while ($campaigns_to_print > $o)
			{
			$rowx=mysql_fetch_row($rslt);
			$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
			}

	echo "<BR><BR>";
	echo "<center><br><font color=navy>TEST ON CAMPAIGN<form action=$PHP_SELF method=POST target=\"_blank\"><br>\n";
	echo "<input type=hidden name=lead_filter_id value=\"$lead_filter_id\">\n";
	echo "<input type=hidden name=ADD value=\"73\">\n";
	echo "<select size=1 name=campaign_id>\n";
	echo "$campaigns_list";
	echo "</select>\n";
	echo "<input type=submit name=SUBMIT value=SUBMIT></center>\n";
	echo "<br><br></center>";


	if ($LOGdelete_filters > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=51111111&lead_filter_id=$lead_filter_id\">DELETE THIS FILTER</a>\n";
		}
		echo "";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
	
}


######################
# ADD=321111111 modify call time definition info in the system
######################

if ($ADD==321111111)
{
if ($LOGmodify_call_times==1)
{

if ( ($stage=="ADD") and (strlen($state_rule)>0) )
	{
	$stmt="SELECT ct_state_call_times from vicidial_call_times where call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$ct_state_call_times = $row[0];

	if (eregi("\|$",$ct_state_call_times))
		{$ct_state_call_times = "$ct_state_call_times$state_rule\|";}
	else
		{$ct_state_call_times = "$ct_state_call_times\|$state_rule\|";}
	$stmt="UPDATE vicidial_call_times set ct_state_call_times='$ct_state_call_times' where call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	echo "State Rule Added: $state_rule<BR>\n";
	}
if ( ($stage=="REMOVE") and (strlen($state_rule)>0) )
	{
	$stmt="SELECT ct_state_call_times from vicidial_call_times where call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$ct_state_call_times = $row[0];

	$ct_state_call_times = eregi_replace("\|$state_rule\|",'|',$ct_state_call_times);
	$stmt="UPDATE vicidial_call_times set ct_state_call_times='$ct_state_call_times' where call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	echo "State Rule Removed: $state_rule<BR>\n";
	}

$ADD=311111111;
}
else
{
echo "<font color=red>You are not authorized to view this page. Please go back.</font>";
}

}


######################
# ADD=311111111 modify call time definition info in the system
######################

if ($ADD==311111111)
{

if ($LOGmodify_call_times==1)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_call_times where call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$call_time_name =		$row[1];
	$call_time_comments =	$row[2];
	$ct_default_start =		$row[3];
	$ct_default_stop =		$row[4];
	$ct_sunday_start =		$row[5];
	$ct_sunday_stop =		$row[6];
	$ct_monday_start =		$row[7];
	$ct_monday_stop =		$row[8];
	$ct_tuesday_start =		$row[9];
	$ct_tuesday_stop =		$row[10];
	$ct_wednesday_start =	$row[11];
	$ct_wednesday_stop =	$row[12];
	$ct_thursday_start =	$row[13];
	$ct_thursday_stop =		$row[14];
	$ct_friday_start =		$row[15];
	$ct_friday_stop =		$row[16];
	$ct_saturday_start =	$row[17];
	$ct_saturday_stop =		$row[18];
	$ct_state_call_times =	$row[19];

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>MODIFY A CALL TIME</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=411111111>\n";
echo "<input type=hidden name=call_time_id value=\"$call_time_id\">\n";
echo "<TABLE>";
echo "<tr bgcolor=#C1D6DF><td align=right>Call Time ID: </td><td align=left colspan=3><B>$call_time_id</B>$NWB#osdial_call_times-call_time_id$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Call Time Name: </td><td align=left colspan=3><input type=text name=call_time_name size=40 maxlength=50 value=\"$call_time_name\"> (short description of the call time)$NWB#osdial_call_times-call_time_name$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Call Time Comments: </td><td align=left colspan=3><input type=text name=call_time_comments size=50 maxlength=255 value=\"$call_time_comments\"> $NWB#osdial_call_times-call_time_comments$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Default Start:</td><td align=left><input type=text name=ct_default_start size=5 maxlength=4 value=\"$ct_default_start\"> </td><td align=right>Default Stop:</td><td align=left><input type=text name=ct_default_stop size=5 maxlength=4 value=\"$ct_default_stop\"> $NWB#osdial_call_times-ct_default_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Sunday Start:</td><td align=left><input type=text name=ct_sunday_start size=5 maxlength=4 value=\"$ct_sunday_start\"> </td><td align=right>Sunday Stop:</td><td align=left><input type=text name=ct_sunday_stop size=5 maxlength=4 value=\"$ct_sunday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Monday Start:</td><td align=left><input type=text name=ct_monday_start size=5 maxlength=4 value=\"$ct_monday_start\"> </td><td align=right>Monday Stop:</td><td align=left><input type=text name=ct_monday_stop size=5 maxlength=4 value=\"$ct_monday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Tuesday Start:</td><td align=left><input type=text name=ct_tuesday_start size=5 maxlength=4 value=\"$ct_tuesday_start\"> </td><td align=right>Tuesday Stop:</td><td align=left><input type=text name=ct_tuesday_stop size=5 maxlength=4 value=\"$ct_tuesday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Wednesday Start:</td><td align=left><input type=text name=ct_wednesday_start size=5 maxlength=4 value=\"$ct_wednesday_start\"> </td><td align=right>Wednesday Stop:</td><td align=left><input type=text name=ct_wednesday_stop size=5 maxlength=4 value=\"$ct_wednesday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Thursday Start:</td><td align=left><input type=text name=ct_thursday_start size=5 maxlength=4 value=\"$ct_thursday_start\"> </td><td align=right>Thursday Stop:</td><td align=left><input type=text name=ct_thursday_stop size=5 maxlength=4 value=\"$ct_thursday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Friday Start:</td><td align=left><input type=text name=ct_friday_start size=5 maxlength=4 value=\"$ct_friday_start\"> </td><td align=right>Friday Stop:</td><td align=left><input type=text name=ct_friday_stop size=5 maxlength=4 value=\"$ct_friday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Saturday Start:</td><td align=left><input type=text name=ct_saturday_start size=5 maxlength=4 value=\"$ct_saturday_start\"> </td><td align=right>Saturday Stop:</td><td align=left><input type=text name=ct_saturday_stop size=5 maxlength=4 value=\"$ct_saturday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=center colspan=4><input type=submit name=SUBMIT value=SUBMIT></FORM></td></tr>\n";

$ct_srs=1;
$b=0;
$srs_SQL ='';
if (strlen($ct_state_call_times)>2)
	{
	$state_rules = explode('|',$ct_state_call_times);
	$ct_srs = ((count($state_rules)) - 1);
	}
echo "<tr bgcolor=#C1D6DF><td align=center rowspan=$ct_srs>Active State Call Time Definitions for this Record: </td>\n";
echo "<td align=center colspan=3>&nbsp;</td></tr>\n";
while($ct_srs >= $b)
	{
	if (strlen($state_rules[$b])>0)
		{
		$stmt="SELECT state_call_time_state,state_call_time_name from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=3111111111&call_time_id=$state_rules[$b]\">$state_rules[$b] </a> - <a href=\"$PHP_SELF?ADD=321111111&call_time_id=$call_time_id&state_rule=$state_rules[$b]&stage=REMOVE\">REMOVE </a></td><td align=left colspan=2>$row[0] - $row[1]</td></tr>\n";
		$srs_SQL .= "'$state_rules[$b]',";
		$srs_state_SQL .= "'$row[0]',";
		}
	$b++;
	}
if (strlen($srs_SQL)>2)
	{
	$srs_SQL = "$srs_SQL''";
	$srs_state_SQL = "$srs_state_SQL''";
	$srs_SQL = "where state_call_time_id NOT IN($srs_SQL) and state_call_time_state NOT IN($srs_state_SQL)";
	}
else
	{$srs_SQL='';}
$stmt="SELECT state_call_time_id,state_call_time_name from vicidial_state_call_times $srs_SQL order by state_call_time_id;";
$rslt=mysql_query($stmt, $link);
$sct_to_print = mysql_num_rows($rslt);
$sct_list='';

$o=0;
while ($sct_to_print > $o) {
	$rowx=mysql_fetch_row($rslt);
	$sct_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
	$o++;
}
echo "<tr bgcolor=#C1D6DF><td align=right><form action=$PHP_SELF method=POST>\n";
echo "<input type=hidden name=ADD value=321111111>\n";
echo "<input type=hidden name=stage value=\"ADD\">\n";
echo "<input type=hidden name=call_time_id value=\"$call_time_id\">\n";
echo "Add state call time rule: </td><td align=left colspan=2><select size=1 name=state_rule>\n";
echo "$sct_list";
echo "</select></td>\n";
echo "<td align=center colspan=4><input type=submit name=SUBMIT value=SUBMIT></FORM></td></tr>\n";

echo "</TABLE><BR><BR>\n";
echo "<font color=navy size=+1>CAMPAIGNS USING THIS CALL TIME</font><br><BR>\n";
echo "<TABLE>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns where local_call_time='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	$camps_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($camps_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "<TR><TD><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[0]\">$row[0] </a></TD><TD> $row[1]<BR></TD></TR>\n";
		$o++;
	}

echo "</TABLE>\n";
echo "<br><font color=navy size=+1>INBOUND GROUPS USING THIS CALL TIME</font><BR><br>\n";
echo "<TABLE>\n";

	$stmt="SELECT group_id,group_name from vicidial_inbound_groups where call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	$camps_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($camps_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "<TR><TD><a href=\"$PHP_SELF?ADD=3111&group_id=$row[0]\">$row[0] </a></TD><TD> $row[1]<BR></TD></TR>\n";
		$o++;
	}

echo "</TABLE>\n";
echo "</center><BR><BR>\n";

if ($LOGdelete_call_times > 0)
	{
	echo "<br><br><a href=\"$PHP_SELF?ADD=511111111&call_time_id=$call_time_id\">DELETE THIS CALL TIME DEFINITION</a>\n";
	}
}
else
{
echo "<font color=red>You are not authorized to view this page. Please go back.</font>";
}

}


######################
# ADD=3111111111 modify state call time definition info in the system
######################

if ($ADD==3111111111)
{

if ($LOGmodify_call_times==1)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_state_call_times where state_call_time_id='$call_time_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$state_call_time_state =$row[1];
	$call_time_name =		$row[2];
	$call_time_comments =	$row[3];
	$ct_default_start =		$row[4];
	$ct_default_stop =		$row[5];
	$ct_sunday_start =		$row[6];
	$ct_sunday_stop =		$row[7];
	$ct_monday_start =		$row[8];
	$ct_monday_stop =		$row[9];
	$ct_tuesday_start =		$row[10];
	$ct_tuesday_stop =		$row[11];
	$ct_wednesday_start =	$row[12];
	$ct_wednesday_stop =	$row[13];
	$ct_thursday_start =	$row[14];
	$ct_thursday_stop =		$row[15];
	$ct_friday_start =		$row[16];
	$ct_friday_stop =		$row[17];
	$ct_saturday_start =	$row[18];
	$ct_saturday_stop =		$row[19];

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>MODIFY A STATE CALL TIME</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=4111111111>\n";
echo "<input type=hidden name=call_time_id value=\"$call_time_id\">\n";
echo "<TABLE>";
echo "<tr bgcolor=#C1D6DF><td align=right>Call Time ID: </td><td align=left colspan=3><B>$call_time_id</B>$NWB#osdial_call_times-call_time_id$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>State Call Time State: </td><td align=left colspan=3><input type=text name=state_call_time_state size=4 maxlength=2 value=\"$state_call_time_state\"> $NWB#osdial_call_times-state_call_time_state$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>State Call Time Name: </td><td align=left colspan=3><input type=text name=call_time_name size=40 maxlength=50 value=\"$call_time_name\"> (short description of the call time)$NWB#osdial_call_times-call_time_name$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>State Call Time Comments: </td><td align=left colspan=3><input type=text name=call_time_comments size=50 maxlength=255 value=\"$call_time_comments\"> $NWB#osdial_call_times-call_time_comments$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Default Start:</td><td align=left><input type=text name=ct_default_start size=5 maxlength=4 value=\"$ct_default_start\"> </td><td align=right>Default Stop:</td><td align=left><input type=text name=ct_default_stop size=5 maxlength=4 value=\"$ct_default_stop\"> $NWB#osdial_call_times-ct_default_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Sunday Start:</td><td align=left><input type=text name=ct_sunday_start size=5 maxlength=4 value=\"$ct_sunday_start\"> </td><td align=right>Sunday Stop:</td><td align=left><input type=text name=ct_sunday_stop size=5 maxlength=4 value=\"$ct_sunday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Monday Start:</td><td align=left><input type=text name=ct_monday_start size=5 maxlength=4 value=\"$ct_monday_start\"> </td><td align=right>Monday Stop:</td><td align=left><input type=text name=ct_monday_stop size=5 maxlength=4 value=\"$ct_monday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Tuesday Start:</td><td align=left><input type=text name=ct_tuesday_start size=5 maxlength=4 value=\"$ct_tuesday_start\"> </td><td align=right>Tuesday Stop:</td><td align=left><input type=text name=ct_tuesday_stop size=5 maxlength=4 value=\"$ct_tuesday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Wednesday Start:</td><td align=left><input type=text name=ct_wednesday_start size=5 maxlength=4 value=\"$ct_wednesday_start\"> </td><td align=right>Wednesday Stop:</td><td align=left><input type=text name=ct_wednesday_stop size=5 maxlength=4 value=\"$ct_wednesday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Thursday Start:</td><td align=left><input type=text name=ct_thursday_start size=5 maxlength=4 value=\"$ct_thursday_start\"> </td><td align=right>Thursday Stop:</td><td align=left><input type=text name=ct_thursday_stop size=5 maxlength=4 value=\"$ct_thursday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Friday Start:</td><td align=left><input type=text name=ct_friday_start size=5 maxlength=4 value=\"$ct_friday_start\"> </td><td align=right>Friday Stop:</td><td align=left><input type=text name=ct_friday_stop size=5 maxlength=4 value=\"$ct_friday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Saturday Start:</td><td align=left><input type=text name=ct_saturday_start size=5 maxlength=4 value=\"$ct_saturday_start\"> </td><td align=right>Saturday Stop:</td><td align=left><input type=text name=ct_saturday_stop size=5 maxlength=4 value=\"$ct_saturday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";

echo "<tr bgcolor=#C1D6DF><td align=center colspan=4><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
echo "</TABLE><BR><BR>\n";
echo "<BR><font color=navy size=+1>CALL TIMES USING THIS STATE CALL TIME</font><BR>\n";
echo "<TABLE>\n";

	$stmt="SELECT call_time_id,call_time_name from vicidial_call_times where ct_state_call_times LIKE \"%|$call_time_id|%\";";
	$rslt=mysql_query($stmt, $link);
	$camps_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($camps_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "<TR><TD><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$row[0]\">$row[0] </a></TD><TD> $row[1]<BR></TD></TR>\n";
		$o++;
	}

echo "</TABLE>\n";
echo "</center><BR><BR><br>\n";

if ($LOGdelete_call_times > 0)
	{
	echo "<br><br><a href=\"$PHP_SELF?ADD=5111111111&call_time_id=$call_time_id\">DELETE THIS STATE CALL TIME DEFINITION</a>\n";
	}

}
else
{
echo "<font color=red>You are not authorized to view this page. Please go back.</font>";
}

}


######################
# ADD=31111111111 modify phone record in the system
######################

if ($ADD==31111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from phones where extension='$extension' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);

	echo "<center><br><font color=navy size=+1>MODIFY A PHONE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111111111>\n";
	echo "<input type=hidden name=old_extension value=\"$row[0]\">\n";
	echo "<input type=hidden name=old_server_ip value=\"$row[5]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Phone extension: </td><td align=left><input type=text name=extension size=20 maxlength=100 value=\"$row[0]\">$NWB#phones-extension$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Dial Plan Number: </td><td align=left><input type=text name=dialplan_number size=15 maxlength=20 value=\"$row[1]\"> (digits only)$NWB#phones-dialplan_number$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Voicemail Box: </td><td align=left><input type=text name=voicemail_id size=10 maxlength=10 value=\"$row[2]\"> (digits only)$NWB#phones-voicemail_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Outbound CallerID: </td><td align=left><input type=text name=outbound_cid size=10 maxlength=20 value=\"$row[65]\"> (digits only)$NWB#phones-outbound_cid$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Phone IP address: </td><td align=left><input type=text name=phone_ip size=20 maxlength=15 value=\"$row[3]\"> (optional)$NWB#phones-phone_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Computer IP address: </td><td align=left><input type=text name=computer_ip size=20 maxlength=15 value=\"$row[4]\"> (optional)$NWB#phones-computer_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=311111111111&server_ip=$row[5]\">Server IP</a>: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	echo "<option SELECTED>$row[5]</option>\n";
	echo "</select>$NWB#phones-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Login: </td><td align=left><input type=text name=login size=10 maxlength=10 value=\"$row[6]\">$NWB#phones-login$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=pass size=10 maxlength=10 value=\"$row[7]\">$NWB#phones-pass$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Status: </td><td align=left><select size=1 name=status><option>ACTIVE</option><option>SUSPENDED</option><option>CLOSED</option><option>PENDING</option><option>ADMIN</option><option selected>$row[8]</option></select>$NWB#phones-status$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active Account: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option selected>$row[9]</option></select>$NWB#phones-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Phone Type: </td><td align=left><input type=text name=phone_type size=20 maxlength=50 value=\"$row[10]\">$NWB#phones-phone_type$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Full Name: </td><td align=left><input type=text name=fullname size=20 maxlength=50 value=\"$row[11]\">$NWB#phones-fullname$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Company: </td><td align=left><input type=text name=company size=10 maxlength=10 value=\"$row[12]\">$NWB#phones-company$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Picture: </td><td align=left><input type=text name=picture size=20 maxlength=19 value=\"$row[13]\">$NWB#phones-picture$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>New Messages: </td><td align=left><b>$row[14]</b>$NWB#phones-messages$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Old Messages: </td><td align=left><b>$row[15]</b>$NWB#phones-old_messages$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Client Protocol: </td><td align=left><select size=1 name=protocol><option>SIP</option><option>Zap</option><option>IAX2</option><option>EXTERNAL</option><option selected>$row[16]</option></select>$NWB#phones-protocol$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$row[17]</option></select> (Do NOT Adjust for DST)$NWB#phones-local_gmt$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Login: </td><td align=left><input type=text name=ASTmgrUSERNAME size=20 maxlength=20 value=\"$row[18]\">$NWB#phones-ASTmgrUSERNAME$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Secret: </td><td align=left><input type=text name=ASTmgrSECRET size=20 maxlength=20 value=\"$row[19]\">$NWB#phones-ASTmgrSECRET$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Default Agent: </td><td align=left><input type=text name=login_user size=20 maxlength=20 value=\"$row[20]\">$NWB#phones-login_user$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Default Pass: </td><td align=left><input type=text name=login_pass size=20 maxlength=20 value=\"$row[21]\">$NWB#phones-login_pass$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Default Campaign: </td><td align=left><input type=text name=login_campaign size=10 maxlength=10 value=\"$row[22]\">$NWB#phones-login_campaign$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Park Exten: </td><td align=left><input type=text name=park_on_extension size=10 maxlength=10 value=\"$row[23]\">$NWB#phones-park_on_extension$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Conf Exten: </td><td align=left><input type=text name=conf_on_extension size=10 maxlength=10 value=\"$row[24]\">$NWB#phones-conf_on_extension$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Park Exten: </td><td align=left><input type=text name=VICIDIAL_park_on_extension size=10 maxlength=10 value=\"$row[25]\">$NWB#phones-OSDial_park_on_extension$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Park File: </td><td align=left><input type=text name=VICIDIAL_park_on_filename size=10 maxlength=10 value=\"$row[26]\">$NWB#phones-OSDial_park_on_filename$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Monitor Prefix: </td><td align=left><input type=text name=monitor_prefix size=10 maxlength=10 value=\"$row[27]\">$NWB#phones-monitor_prefix$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Recording Exten: </td><td align=left><input type=text name=recording_exten size=10 maxlength=10 value=\"$row[28]\">$NWB#phones-recording_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>VMailMain Exten: </td><td align=left><input type=text name=voicemail_exten size=10 maxlength=10 value=\"$row[29]\">$NWB#phones-voicemail_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>VMailDump Exten: </td><td align=left><input type=text name=voicemail_dump_exten size=20 maxlength=20 value=\"$row[30]\">$NWB#phones-voicemail_dump_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Exten Context: </td><td align=left><input type=text name=ext_context size=20 maxlength=20 value=\"$row[31]\">$NWB#phones-ext_context$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DTMFSend Channel: </td><td align=left><input type=text name=dtmf_send_extension size=40 maxlength=100 value=\"$row[32]\">$NWB#phones-dtmf_send_extension$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Outbound Call Group: </td><td align=left><input type=text name=call_out_number_group size=40 maxlength=100 value=\"$row[33]\">$NWB#phones-call_out_number_group$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Browser Location: </td><td align=left><input type=text name=client_browser size=40 maxlength=100 value=\"$row[34]\">$NWB#phones-client_browser$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Install Directory: </td><td align=left><input type=text name=install_directory size=40 maxlength=100 value=\"$row[35]\">$NWB#phones-install_directory$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>CallerID URL: </td><td align=left><input type=text name=local_web_callerID_URL size=40 maxlength=255 value=\"$row[36]\">$NWB#phones-local_web_callerID_URL$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Default URL: </td><td align=left><input type=text name=VICIDIAL_web_URL size=40 maxlength=255 value=\"$row[37]\">$NWB#phones-OSDial_web_URL$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Call Logging: </td><td align=left><select size=1 name=AGI_call_logging_enabled><option>1</option><option>0</option><option selected>$row[38]</option></select>$NWB#phones-AGI_call_logging_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Agent Switching: </td><td align=left><select size=1 name=user_switching_enabled><option>1</option><option>0</option><option selected>$row[39]</option></select>$NWB#phones-user_switching_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Conferencing: </td><td align=left><select size=1 name=conferencing_enabled><option>1</option><option>0</option><option selected>$row[40]</option></select>$NWB#phones-conferencing_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Admin Hang Up: </td><td align=left><select size=1 name=admin_hangup_enabled><option>1</option><option>0</option><option selected>$row[41]</option></select>$NWB#phones-admin_hangup_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Admin Hijack: </td><td align=left><select size=1 name=admin_hijack_enabled><option>1</option><option>0</option><option selected>$row[42]</option></select>$NWB#phones-admin_hijack_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Admin Monitor: </td><td align=left><select size=1 name=admin_monitor_enabled><option>1</option><option>0</option><option selected>$row[43]</option></select>$NWB#phones-admin_monitor_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Call Park: </td><td align=left><select size=1 name=call_parking_enabled><option>1</option><option>0</option><option selected>$row[44]</option></select>$NWB#phones-call_parking_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Updater Check: </td><td align=left><select size=1 name=updater_check_enabled><option>1</option><option>0</option><option selected>$row[45]</option></select>$NWB#phones-updater_check_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>AF Logging: </td><td align=left><select size=1 name=AFLogging_enabled><option>1</option><option>0</option><option selected>$row[46]</option></select>$NWB#phones-AFLogging_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Queue Enabled: </td><td align=left><select size=1 name=QUEUE_ACTION_enabled><option>1</option><option>0</option><option selected>$row[47]</option></select>$NWB#phones-QUEUE_ACTION_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>CallerID Popup: </td><td align=left><select size=1 name=CallerID_popup_enabled><option>1</option><option>0</option><option selected>$row[48]</option></select>$NWB#phones-CallerID_popup_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>VMail Button: </td><td align=left><select size=1 name=voicemail_button_enabled><option>1</option><option>0</option><option selected>$row[49]</option></select>$NWB#phones-voicemail_button_enabled$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Fast Refresh: </td><td align=left><select size=1 name=enable_fast_refresh><option>1</option><option>0</option><option selected>$row[50]</option></select>$NWB#phones-enable_fast_refresh$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Fast Refresh Rate: </td><td align=left><input type=text size=5 name=fast_refresh_rate value=\"$row[51]\">(in ms)$NWB#phones-fast_refresh_rate$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Persistant MySQL: </td><td align=left><select size=1 name=enable_persistant_mysql><option>1</option><option>0</option><option selected>$row[52]</option></select>$NWB#phones-enable_persistant_mysql$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Auto Dial Next Number: </td><td align=left><select size=1 name=auto_dial_next_number><option>1</option><option>0</option><option selected>$row[53]</option></select>$NWB#phones-auto_dial_next_number$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Stop Rec after each call: </td><td align=left><select size=1 name=VDstop_rec_after_each_call><option>1</option><option>0</option><option selected>$row[54]</option></select>$NWB#phones-VDstop_rec_after_each_call$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Enable SIPSAK Messages: </td><td align=left><select size=1 name=enable_sipsak_messages><option>1</option><option>0</option><option selected>$row[66]</option></select>$NWB#phones-enable_sipsak_messages$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBX Server: </td><td align=left><input type=text name=DBX_server size=15 maxlength=15 value=\"$row[55]\"> (Primary DB Server)$NWB#phones-DBX_server$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBX Database: </td><td align=left><input type=text name=DBX_database size=15 maxlength=15 value=\"$row[56]\"> (Primary Server Database)$NWB#phones-DBX_database$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBX User: </td><td align=left><input type=text name=DBX_user size=15 maxlength=15 value=\"$row[57]\"> (Primary DB Login)$NWB#phones-DBX_user$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBX Pass: </td><td align=left><input type=text name=DBX_pass size=15 maxlength=15 value=\"$row[58]\"> (Primary DB Secret)$NWB#phones-DBX_pass$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBX Port: </td><td align=left><input type=text name=DBX_port size=6 maxlength=6 value=\"$row[59]\"> (Primary DB Port)$NWB#phones-DBX_port$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBY Server: </td><td align=left><input type=text name=DBY_server size=15 maxlength=15 value=\"$row[60]\"> (Secondary DB Server)$NWB#phones-DBY_server$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBY Database: </td><td align=left><input type=text name=DBY_database size=15 maxlength=15 value=\"$row[61]\"> (Secondary Server Database)$NWB#phones-DBY_database$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBY User: </td><td align=left><input type=text name=DBY_user size=15 maxlength=15 value=\"$row[62]\"> (Secondary DB Login)$NWB#phones-DBY_user$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBY Pass: </td><td align=left><input type=text name=DBY_pass size=15 maxlength=15 value=\"$row[63]\"> (Secondary DB Secret)$NWB#phones-DBY_pass$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>DBY Port: </td><td align=left><input type=text name=DBY_port size=6 maxlength=6 value=\"$row[64]\"> (Secondary DB Port)$NWB#phones-DBY_port$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";

	echo "<br><br><a href=\"./phone_stats.php?extension=$row[0]&server_ip=$row[5]\">Click here for phone stats</a><br><br>\n";

	if ($LOGast_delete_phones > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=51111111111&extension=$extension&server_ip=$server_ip\">DELETE THIS PHONE</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=311111111111 modify server record in the system
######################

if ($ADD==311111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from servers where server_id='$server_id' or server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$server_id = $row[0];
	$server_ip = $row[2];

	echo "<center><br><font color=navy size=+1>MODIFY A SERVER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411111111111>\n";
	echo "<input type=hidden name=old_server_id value=\"$server_id\">\n";
	echo "<input type=hidden name=old_server_ip value=\"$row[2]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server ID: </td><td align=left><input type=text name=server_id size=10 maxlength=10 value=\"$row[0]\">$NWB#servers-server_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server Description: </td><td align=left><input type=text name=server_description size=30 maxlength=255 value=\"$row[1]\">$NWB#servers-server_description$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP Address: </td><td align=left><input type=text name=server_ip size=20 maxlength=15 value=\"$row[2]\">$NWB#servers-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option selected>$row[3]</option></select>$NWB#servers-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Asterisk Version: </td><td align=left><input type=text name=asterisk_version size=20 maxlength=20 value=\"$row[4]\">$NWB#servers-asterisk_version$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Max OSDial Trunks: </td><td align=left><input type=text name=max_vicidial_trunks size=5 maxlength=4 value=\"$row[5]\">$NWB#servers-max_osdial_trunks$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Balance Dialing: </td><td align=left><select size=1 name=vicidial_balance_active><option>Y</option><option>N</option><option selected>$row[20]</option></select>$NWB#servers-osdial_balance_active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Balance Offlimits: </td><td align=left><input type=text name=balance_trunks_offlimits size=5 maxlength=4 value=\"$row[21]\">$NWB#servers-balance_trunks_offlimits$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Telnet Host: </td><td align=left><input type=text name=telnet_host size=20 maxlength=20 value=\"$row[6]\">$NWB#servers-telnet_host$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Telnet Port: </td><td align=left><input type=text name=telnet_port size=6 maxlength=5 value=\"$row[7]\">$NWB#servers-telnet_port$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager User: </td><td align=left><input type=text name=ASTmgrUSERNAME size=20 maxlength=20 value=\"$row[8]\">$NWB#servers-ASTmgrUSERNAME$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Secret: </td><td align=left><input type=text name=ASTmgrSECRET size=20 maxlength=20 value=\"$row[9]\">$NWB#servers-ASTmgrSECRET$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Update User: </td><td align=left><input type=text name=ASTmgrUSERNAMEupdate size=20 maxlength=20 value=\"$row[10]\">$NWB#servers-ASTmgrUSERNAMEupdate$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Listen User: </td><td align=left><input type=text name=ASTmgrUSERNAMElisten size=20 maxlength=20 value=\"$row[11]\">$NWB#servers-ASTmgrUSERNAMElisten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Send User: </td><td align=left><input type=text name=ASTmgrUSERNAMEsend size=20 maxlength=20 value=\"$row[12]\">$NWB#servers-ASTmgrUSERNAMEsend$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$row[13]</option></select> (Do NOT Adjust for DST)$NWB#servers-local_gmt$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>VMail Dump Exten: </td><td align=left><input type=text name=voicemail_dump_exten size=20 maxlength=20 value=\"$row[14]\">$NWB#servers-voicemail_dump_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial AD extension: </td><td align=left><input type=text name=answer_transfer_agent size=20 maxlength=20 value=\"$row[15]\">$NWB#servers-answer_transfer_agent$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Default Context: </td><td align=left><input type=text name=ext_context size=20 maxlength=20 value=\"$row[16]\">$NWB#servers-ext_context$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>System Performance: </td><td align=left><select size=1 name=sys_perf_log><option>Y</option><option>N</option><option selected>$row[17]</option></select>$NWB#servers-sys_perf_log$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server Logs: </td><td align=left><select size=1 name=vd_server_logs><option>Y</option><option>N</option><option selected>$row[18]</option></select>$NWB#servers-vd_server_logs$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>AGI Output: </td><td align=left><select size=1 name=agi_output><option>NONE</option><option>STDERR</option><option>FILE</option><option>BOTH</option><option selected>$row[19]</option></select>$NWB#servers-agi_output$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center></form>\n";


	### vicidial server trunk records for this server
	echo "<br><br><center><font color=navy size=+1>OSDial TRUNKS FOR THIS SERVER &nbsp;</font> $NWB#osdial_server_trunks$NWE<br>\n";
	echo "<TABLE width=500 cellspacing=3>\n";
	echo "<tr><td><font color=navy>CAMPAIGN</font></td><td><font color=navy>TRUNKS</font> </td><td><font color=navy>RESTRICTION</font> </td><td> </td><td><font color=navy>DELETE</font> </td></tr>\n";

		$stmt="SELECT * from vicidial_server_trunks where server_ip='$server_ip' order by campaign_id";
		$rslt=mysql_query($stmt, $link);
		$recycle_to_print = mysql_num_rows($rslt);
		$o=0;
		while ($recycle_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$o++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1>$rowx[1]</font><form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
		echo "<input type=hidden name=campaign_id value=\"$rowx[1]\">\n";
		echo "<input type=hidden name=ADD value=421111111111></td>\n";
		echo "<td><font size=1><input size=6 maxlength=4 name=dedicated_trunks value=\"$rowx[2]\"></font></td>\n";
		echo "<td><select size=1 name=trunk_restriction><option>MAXIMUM_LIMIT</option><option>OVERFLOW_ALLOWED</option><option SELECTED>$rowx[3]</option></select></td>\n";
		echo "<td><font size=1><input type=submit name=submit value=MODIFY></form></font></td>\n";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=621111111111&campaign_id=$rowx[1]&server_ip=$server_ip\">DELETE</a></font></td></tr>\n";
		}

	echo "</table></font></center><br><br>\n";

	echo "<br><center><font color=navy>ADD NEW SERVER OSDial TRUNK<BR><br></font></font><form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=ADD value=221111111111>\n";
	echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
	echo "TRUNKS: <input size=6 maxlength=4 name=dedicated_trunks><BR><br>\n";
	echo "CAMPAIGN: <select size=1 name=campaign_id>\n";
	echo "$campaigns_list\n";
	echo "</select><BR><br>\n";
	echo "RESTRICTION: <select size=1 name=trunk_restriction><option>MAXIMUM_LIMIT</option><option>OVERFLOW_ALLOWED</option></select><BR><br>\n";
	echo "<input type=submit name=submit value=ADD><BR></font>\n";

	echo "</center></FORM><br>\n";


	### list of phones on this server
	echo "<center>\n";
	echo "<br><font color=navy>PHONES WITHIN THIS SERVER</font><br><br>\n";
	echo "<TABLE width=400 cellspacing=3>\n";
	echo "<tr><td><font color=navy>EXTENSION</font></td><td><font color=navy>NAME</font></td><td><font color=navy>ACTIVE</font></td></tr>\n";

		$active_phones = 0;
		$inactive_phones = 0;
		$stmt="SELECT extension,active,fullname from phones where server_ip='$row[2]'";
		$rsltx=mysql_query($stmt, $link);
		$lists_to_print = mysql_num_rows($rsltx);
		$camp_lists='';

		$o=0;
		while ($lists_to_print > $o) {
			$rowx=mysql_fetch_row($rsltx);
			$o++;
		if (ereg("Y", $rowx[1])) {$active_phones++;   $camp_lists .= "'$rowx[0]',";}
		if (ereg("N", $rowx[1])) {$inactive_phones++;}

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111111111&extension=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td><td><font size=1>$rowx[2]</td><td><font size=1>$rowx[1]</td></tr>\n";
		}

	echo "</table></font></center><br>\n";


	### list of conferences on this server
	echo "<center>\n";
	echo "<br><br><font color=navy>CONFERENCES WITHIN THIS SERVER</font><br><br>\n";
	echo "<TABLE width=400 cellspacing=3>\n";
	echo "<tr><td>CONFERENCE</td><td>EXTENSION</td></tr>\n";

		$active_confs = 0;
		$stmt="SELECT conf_exten,extension from conferences where server_ip='$row[2]'";
		$rsltx=mysql_query($stmt, $link);
		$lists_to_print = mysql_num_rows($rsltx);
		$camp_lists='';

		$o=0;
		while ($lists_to_print > $o) {
			$rowx=mysql_fetch_row($rsltx);
			$o++;
			$active_confs++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td><td><font size=1>$rowx[2]</td></tr>\n";
		}

	echo "</table></font></center><br>\n";


	### list of vicidial conferences on this server
	echo "<center>\n";
	echo "<br><br><font color=navy>OSDial CONFERENCES WITHIN THIS SERVER<br><br>\n";
	echo "<TABLE width=400 cellspacing=3>\n";
	echo "<tr><td><font color=navy>VD CONFERENCE</font></td><td><font color=navy>EXTENSION</font></td></tr>\n";

		$active_vdconfs = 0;
		$stmt="SELECT conf_exten,extension from vicidial_conferences where server_ip='$row[2]'";
		$rsltx=mysql_query($stmt, $link);
		$lists_to_print = mysql_num_rows($rsltx);
		$camp_lists='';

		$o=0;
		while ($lists_to_print > $o) {
			$rowx=mysql_fetch_row($rsltx);
			$o++;
			$active_vdconfs++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111111111111&conf_exten=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td><td><font size=1>$rowx[2]</td></tr>\n";
		}

	echo "</table></font></center><br>\n";


	echo "<center><b>\n";

		$camp_lists = eregi_replace(".$","",$camp_lists);
	echo "<font color=navy>This server has $active_phones active phones and $inactive_phones inactive phones<br><br>\n";
	echo "This server has $active_confs active conferences<br><br>\n";
	echo "This server has $active_vdconfs active OSDial conferences</font><br><br>\n";
	echo "</b></center>\n";
	if ($LOGast_delete_phones > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=511111111111&server_id=$server_id&server_ip=$server_ip\">DELETE THIS SERVER</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=3111111111111 modify conference record in the system
######################

if ($ADD==3111111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from conferences where conf_exten='$conf_exten' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$conf_exten = $row[0];
	$server_ip = $row[1];

	echo "<center><br><font color=navy size=+1>MODIFY A CONFERENCE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=4111111111111>\n";
	echo "<input type=hidden name=old_conf_exten value=\"$row[0]\">\n";
	echo "<input type=hidden name=old_server_ip value=\"$row[1]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Conference: </td><td align=left><input type=text name=conf_exten size=10 maxlength=7 value=\"$row[0]\">$NWB#conferences-conf_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=311111111111&server_ip=$row[1]\">Server IP</a>: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	echo "<option SELECTED>$row[1]</option>\n";
	echo "</select>$NWB#conferences-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Current Extension: </td><td align=left><input type=text name=extension size=20 maxlength=20 value=\"$row[2]\"></td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";

	if ($LOGast_delete_phones > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=5111111111111&conf_exten=$conf_exten&server_ip=$server_ip\">DELETE THIS CONFERENCE</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=31111111111111 modify vicidial conference record in the system
######################

if ($ADD==31111111111111)
{
	if ($LOGast_admin_access==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_conferences where conf_exten='$conf_exten' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$conf_exten = $row[0];
	$server_ip = $row[1];

	echo "<center><br><font color=navy size=+1>MODIFY A OSDial CONFERENCE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111111111111>\n";
	echo "<input type=hidden name=old_conf_exten value=\"$row[0]\">\n";
	echo "<input type=hidden name=old_server_ip value=\"$row[1]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Conference: </td><td align=left><input type=text name=conf_exten size=10 maxlength=7 value=\"$row[0]\">$NWB#conferences-conf_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=311111111111&server_ip=$row[1]\">Server IP</a>: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	echo "<option SELECTED>$row[1]</option>\n";
	echo "</select>$NWB#conferences-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Current Extension: </td><td align=left><input type=text name=extension size=20 maxlength=20 value=\"$row[2]\"></td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";

	if ($LOGast_delete_phones > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=51111111111111&conf_exten=$conf_exten&server_ip=$server_ip\">DELETE THIS OSDial CONFERENCE</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}



######################
# ADD=311111111111111 modify vicidial system settings
######################

if ($ADD==311111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT version,install_date,use_non_latin,webroot_writable,enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_url,queuemetrics_log_id,queuemetrics_eq_prepend,vicidial_agent_disable,allow_sipsak_messages,admin_home_url,enable_agc_xfer_log from system_settings;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$version =						$row[0];
	$install_date =					$row[1];
	$use_non_latin =				$row[2];
	$webroot_writable =				$row[3];
	$enable_queuemetrics_logging =	$row[4];
	$queuemetrics_server_ip =		$row[5];
	$queuemetrics_dbname =			$row[6];
	$queuemetrics_login =			$row[7];
	$queuemetrics_pass =			$row[8];
	$queuemetrics_url =				$row[9];
	$queuemetrics_log_id =			$row[10];
	$queuemetrics_eq_prepend =		$row[11];
	$vicidial_agent_disable =		$row[12];
	$allow_sipsak_messages =		$row[13];
	$admin_home_url =				$row[14];
	$enable_agc_xfer_log =			$row[15];

	echo "<center><br><font color=navy size=+1>MODIFY OSDial SYSTEM SETTINGS</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411111111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Version: </td><td align=left> $version</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Install Date: </td><td align=left> $install_date</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Use Non-Latin: </td><td align=left><select size=1 name=use_non_latin><option>1</option><option>0</option><option selected>$use_non_latin</option></select>$NWB#settings-use_non_latin$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Webroot Writable: </td><td align=left><select size=1 name=webroot_writable><option>1</option><option>0</option><option selected>$webroot_writable</option></select>$NWB#settings-webroot_writable$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Enable QueueMetrics Logging: </td><td align=left><select size=1 name=enable_queuemetrics_logging><option>1</option><option>0</option><option selected>$enable_queuemetrics_logging</option></select>$NWB#settings-enable_queuemetrics_logging$NWE</td></tr>\n";

	echo "<tr bgcolor=#C1D6DF><td align=right>QueueMetrics Server IP: </td><td align=left><input type=text name=queuemetrics_server_ip size=18 maxlength=15 value=\"$queuemetrics_server_ip\">$NWB#settings-queuemetrics_server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>QueueMetrics DB Name: </td><td align=left><input type=text name=queuemetrics_dbname size=18 maxlength=50 value=\"$queuemetrics_dbname\">$NWB#settings-queuemetrics_dbname$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>QueueMetrics DB Login: </td><td align=left><input type=text name=queuemetrics_login size=18 maxlength=50 value=\"$queuemetrics_login\">$NWB#settings-queuemetrics_login$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>QueueMetrics DB Password: </td><td align=left><input type=text name=queuemetrics_pass size=18 maxlength=50 value=\"$queuemetrics_pass\">$NWB#settings-queuemetrics_pass$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>QueueMetrics URL: </td><td align=left><input type=text name=queuemetrics_url size=50 maxlength=255 value=\"$queuemetrics_url\">$NWB#settings-queuemetrics_url$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>QueueMetrics Log ID: </td><td align=left><input type=text name=queuemetrics_log_id size=12 maxlength=10 value=\"$queuemetrics_log_id\">$NWB#settings-queuemetrics_log_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>QueueMetrics EnterQueue Prepend: </td><td align=left><select size=1 name=queuemetrics_eq_prepend>\n";
	echo "<option value=\"NONE\">NONE</option>\n";
	echo "<option value=\"lead_id\">lead_id</option>\n";
	echo "<option value=\"list_id\">list_id</option>\n";
	echo "<option value=\"source_id\">source_id</option>\n";
	echo "<option value=\"vendor_lead_code\">vendor_lead_code</option>\n";
	echo "<option value=\"address3\">address3</option>\n";
	echo "<option value=\"security_phrase\">security_phrase</option>\n";
	echo "<option selected value=\"$queuemetrics_eq_prepend\">$queuemetrics_eq_prepend</option>\n";
	echo "</select>$NWB#settings-queuemetrics_eq_prepend$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Agent Disable Display: </td><td align=left><select size=1 name=vicidial_agent_disable>\n";
	echo "<option value=\"NOT_ACTIVE\">NOT_ACTIVE</option>\n";
	echo "<option value=\"LIVE_AGENT\">LIVE_AGENT</option>\n";
	echo "<option value=\"EXTERNAL\">EXTERNAL</option>\n";
	echo "<option value=\"ALL\">ALL</option>\n";
	echo "<option selected value=\"$vicidial_agent_disable\">$vicidial_agent_disable</option>\n";
	echo "</select>$NWB#settings-osdial_agent_disable$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Allow SIPSAK Messages: </td><td align=left><select size=1 name=allow_sipsak_messages><option>1</option><option>0</option><option selected>$allow_sipsak_messages</option></select>$NWB#settings-allow_sipsak_messages$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Admin Home URL: </td><td align=left><input type=text name=admin_home_url size=50 maxlength=255 value=\"$admin_home_url\">$NWB#settings-admin_home_url$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Enable Agent Transfer Logfile: </td><td align=left><select size=1 name=enable_agc_xfer_log><option>1</option><option>0</option><option selected>$enable_agc_xfer_log</option></select>$NWB#settings-enable_agc_xfer_log$NWE</td></tr>\n";


	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "</form>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}

######################
# ADD=399111111111111 modify archive server settings
######################

if ($ADD=="399111111111111") {
	if ($LOGmodify_servers==1) {
		echo "<TABLE align=center><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$stmt="SELECT name,data FROM configuration WHERE name LIKE 'Archive%';";
		$rslt = mysql_query($stmt, $link);
		$rows = mysql_num_rows($rslt);

                $c = 0;
		while ($rows > $c) {
			$row = mysql_fetch_row($rslt);
			if ($row[0] == "ArchiveHostname") {
				$archive_hostname = $row[1];
			} elseif ($row[0] == "ArchiveTransferMethod") {
				$archive_transfer_method = $row[1];
			} elseif ($row[0] == "ArchivePort") {
				$archive_port = $row[1];
			} elseif ($row[0] == "ArchiveUsername") {
				$archive_username = $row[1];
			} elseif ($row[0] == "ArchivePassword") {
				$archive_password = $row[1];
			} elseif ($row[0] == "ArchivePath") {
				$archive_path = $row[1];
			} elseif ($row[0] == "ArchiveWebPath") {
				$archive_web_path = $row[1];
			} elseif ($row[0] == "ArchiveMixFormat") {
				$archive_mix_format = $row[1];
			}
			$c++;
		}

		echo "<center><br><font color=navy size=+1>MODIFY ARCHIVE SERVER SETTINGS</font><br><form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=ADD value=499111111111111>\n";
		echo "<center><TABLE width=$section_width cellspacing=3>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Archive Server Address: </td><td align=left><input type=text name=archive_hostname size=30 maxlength=30 value=\"$archive_hostname\">$NWB#settings-archive_hostname$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Transfer Method: </td><td align=left><select size=1 name=archive_transfer_method><option>FTP</option><option>SFTP</option><option>SCP</option><option selected>$archive_transfer_method</option></select>$NWB#settings-archive_transfer_method$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Port: </td><td align=left><input type=text name=archive_port size=6 maxlength=5 value=\"$archive_port\">$NWB#settings-archive_port$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Username: </td><td align=left><input type=text name=archive_username size=20 maxlength=20 value=\"$archive_username\">$NWB#settings-archive_username$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=archive_password size=20 maxlength=20 value=\"$archive_password\">$NWB#settings-archive_password$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Path: </td><td align=left><input type=text name=archive_path size=40 maxlength=255 value=\"$archive_path\">$NWB#settings-archive_path$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Web Path: </td><td align=left><input type=text name=archive_web_path size=40 maxlength=255 value=\"$archive_web_path\">$NWB#settings-archive_web_path$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Mix Format: </td><td align=left><select size=1 name=archive_mix_format><option>MP3</option><option>WAV</option><option>GSM</option><option>OGG</option><option selected>$archive_mix_format</option></select>$NWB#settings-archive_mix_format$NWE</td></tr>\n";


		echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
		echo "</TABLE></center>\n";
		echo "</form>\n";
	} else {
		echo "You do not have permission to view this page\n";
		exit;
	}
}



######################
# ADD=399211111111111 modify QC server settings
######################

if ($ADD=="399211111111111") {
	if ($LOGmodify_servers==1) {
		echo "<TABLE align=center><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$stmt="SELECT id,name,description,host,transfer_method,transfer_type FROM qc_servers;";
		$rslt = mysql_query($stmt, $link);
		$rows = mysql_num_rows($rslt);

		echo "<center><br><font color=navy size=+1>QC SERVER LIST</font><br>\n";
		echo "<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=ADD value=399211111111111>\n";
		echo "<input type=hidden name=SUB value=1>\n";
		echo "<center><TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=navy>";
		echo "<td align=center><font size=1 color=white>#</font></td>";
		echo "<td align=center><font size=1 color=white>Name</font></td>";
		echo "<td align=center><font size=1 color=white>Description</font></td>";
		echo "<td align=center><font size=1 color=white>Host</font></td>";
		echo "<td align=center><font size=1 color=white>Method</font></td>";
		echo "<td align=center><font size=1 color=white>Type</font></td>";
		echo "<td colspan=2><font size=1 color=white>&nbsp;</font></td>";
		echo "</tr>\n";
                $c = 0;
		while ($rows > $c) {
			$row = mysql_fetch_row($rslt);

			if (eregi("1$|3$|5$|7$|9$", $c)) {
				$bgcolor='bgcolor="#CBDCE0"'; 
			} else {
				$bgcolor='bgcolor="#C1D6DB"';
			}

			echo "<tr $bgcolor>";
			echo "<td><font size=1>$c</td>";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=399211111111111&SUB=2&qc_server_id=$row[0]\">$row[1]</a></td>";
			echo "<td><font size=1>$row[2]</td>";
			echo "<td><font size=1>$row[3]</td>";
			echo "<td><font size=1>$row[4]</td>";
			echo "<td><font size=1>$row[5]</td>";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=399211111111111&SUB=2&qc_server_id=$row[0]\">MODIFY</a></td>";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=699211111111111&SUB=2&qc_server_id=$row[0]\">REMOVE</a></td>";
			echo "</tr>\n";

			$c++;
		}
		echo "<tr bgcolor=#C1D6DF><td align=center colspan=8><input type=submit name=submit VALUE=NEW></td></tr>\n";
		echo "</TABLE></center>\n";
		echo "</form>\n";

		if ($SUB==1) {
			echo "<br><font color=navy>NEW QC SERVER</font>\n";
			echo "<form action=$PHP_SELF method=POST>\n";
			$qc_server_transfer_method   = "FTP";
			$qc_server_home_path         = "/home/USERNAME";
			$qc_server_location_template = "[campaign_id]/[date]";
			$qc_server_transfer_type     = "IMMEDIATE";
			$qc_server_archive           = "NONE";
			$qc_server_active            = "N";
			$qc_server_batch_time        = "0";
		} elseif ($SUB>1) {
			# Modify server
			echo "<br><font color=navy>MODIFY QC SERVER</font>\n";
			echo "<form action=$PHP_SELF method=POST>\n";
			echo "<input type=hidden name=qc_server_id value=$qc_server_id>\n";

			$stmt="SELECT * FROM qc_servers WHERE id='$qc_server_id';";
			$rslt = mysql_query($stmt, $link);
			$row = mysql_fetch_row($rslt);

			$qc_server_name              = $row[1];
			$qc_server_description       = $row[2];
			$qc_server_transfer_method   = $row[3];
			$qc_server_host              = $row[4];
			$qc_server_username          = $row[5];
			$qc_server_password          = $row[6];
			$qc_server_home_path         = $row[7];
			$qc_server_location_template = $row[8];
			$qc_server_transfer_type     = $row[9];
			$qc_server_archive           = $row[10];
			$qc_server_active            = $row[11];
			$qc_server_batch_time        = $row[12];
		}

		if ($SUB>0) {
			# New Server
			echo "<input type=hidden name=ADD value=499211111111111>\n";
			echo "<input type=hidden name=SUB value=$SUB>\n";
			echo "<center><TABLE width=$section_width cellspacing=1>\n";
	
			echo "<tr bgcolor=#C1D6DF><td align=right>Name: </td><td align=left><input type=text name=qc_server_name size=20 maxlength=20 value=\"$qc_server_name\">$NWB#qc-server_name$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Description: </td><td align=left><input type=text name=qc_server_description size=40 maxlength=100 value=\"$qc_server_description\">$NWB#qc-server_description$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Transfer Method: </td><td align=left><select size=1 name=qc_server_transfer_method><option>FTP</option><option>SFTP</option><option>SCP</option><option selected>$qc_server_transfer_method</option></select>$NWB#qc-server_transfer_method$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Hostname/IP: </td><td align=left><input type=text name=qc_server_host size=30 maxlength=50 value=\"$qc_server_host\">$NWB#qc-server_host$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Username: </td><td align=left><input type=text name=qc_server_username size=30 maxlength=30 value=\"$qc_server_username\">$NWB#qc-server_username$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=qc_server_password size=30 maxlength=30 value=\"$qc_server_password\">$NWB#qc-server_password$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Home Path: </td><td align=left><input type=text name=qc_server_home_path size=40 maxlength=100 value=\"$qc_server_home_path\">$NWB#qc-server_home_path$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Location Template: </td><td align=left><input type=text name=qc_server_location_template size=40 maxlength=255 value=\"$qc_server_location_template\">$NWB#qc-server_location_template$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Transfer Type: </td><td align=left><select size=1 name=qc_server_transfer_type><option>IMMEDIATE</option><option>BATCH</option><option>ARCHIVE</option><option selected>$qc_server_transfer_type</option></select>$NWB#qc-server_transfer_type$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Archive/Compression: </td><td align=left><select size=1 name=qc_server_archive><option>NONE</option><option>ZIP</option><option>TAR</option><option>TGZ</option><option>TBZ2</option><option selected>$qc_server_archive</option></select>$NWB#qc-server_archive$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Batch Time (hour): </td><td align=left><select size=1 name=qc_server_batch_time><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option><option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option><option>19</option><option>20</option><option>21</option><option>22</option><option>23</option><option selected>$qc_server_batch_time</option></select>$NWB#qc-server_batch_time$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=qc_server_active><option>Y</option><option>N</option><option selected>$qc_server_active</option></select>$NWB#qc-server_active$NWE</td></tr>\n";
	
			echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
			echo "</TABLE></center>\n";
			echo "</form>\n";
		}

		if ($SUB>1) {
			# List QC rules
			echo "<br><font color=navy>QC SERVER RULES</font>\n";
			echo "<center><table width=$section_width cellspacing=1>\n";
			echo "<tr bgcolor=navy>";
			echo "<td align=center bgcolor=navy><font size=1 color=white>#</font></td>";
			echo "<td align=center bgcolor=navy><font size=1 color=white>Query</font></td>";
			echo "<td colspan=2 align=center bgcolor=navy><font size=1 color=white>&nbsp;</font></td>";
			echo "</tr>\n";

			$stmt="SELECT * FROM qc_server_rules WHERE qc_server_id='$qc_server_id';";
			$rslt = mysql_query($stmt, $link);
			$rows = mysql_num_rows($rslt);
                	$c = 0;
			while ($rows > $c) {
				$row = mysql_fetch_row($rslt);
	
				if (eregi("1$|3$|5$|7$|9$", $c)) {
					$bgcolor='bgcolor="#CBDCE0"'; 
				} else {
					$bgcolor='bgcolor="#C1D6DB"';
				}
	
				echo "<tr $bgcolor>";
				echo "<td><font size=1>$c</td>";
				echo "<td><font size=1>$row[2]</td>";
				echo "<td><font size=1><a href=\"$PHP_SELF?ADD=399211111111111&SUB=4&qc_server_id=$qc_server_id&qc_server_rule_id=$row[0]\">MODIFY</a></td>";
				echo "<td><font size=1><a href=\"$PHP_SELF?ADD=699211111111111&SUB=4&qc_server_id=$qc_server_id&qc_server_rule_id=$row[0]\">REMOVE</a></td>";
				echo "</tr>\n";
	
				$c++;
			}

			$qcfld = "<form action=$PHP_SELF method=POST>\n";
			$qcfld .= "<input type=hidden name=ADD value=499211111111111>\n";
			$qcfld .= "<input type=hidden name=qc_server_id value=$qc_server_id>\n";
			if ($SUB==4) {
				# Modify QC rule
				$qcfld .= "<input type=hidden name=SUB value=4>\n";
				$qcfld .= "<input type=hidden name=qc_server_rule_id value=$qc_server_rule_id>\n";
				$stmt="SELECT * FROM qc_server_rules WHERE id='$qc_server_rule_id';";
				$rslt = mysql_query($stmt, $link);
				$row = mysql_fetch_row($rslt);
				$qcract = "MODIFY";
				$qc_server_rule_query = $row[2];
			} else {
				# New QC rule
				$qcfld .= "<input type=hidden name=SUB value=3>\n";
				$qcract = "NEW";
			}
			$qcfld .= "<tr bgcolor=#C1D6DF>";
			$qcfld .= "<td>&nbsp;</td>";
			$qcfld .= "<td align=left><input type=text name=qc_server_rule_query size=60 maxlength=255 value=\"$qc_server_rule_query\">$NWB#qc-server_rule_query$NWE</td>";
			$qcfld .= "<td align=center colspan=2><input type=submit name=submit VALUE=$qcract></td>";
			$qcfld .= "</tr></form>\n";
			echo $qcfld;
			echo "</table>\n";
		}


	} else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
		exit;
	}
}



######################
# ADD=321111111111111 modify vicidial system statuses
######################

if ($ADD==321111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<br><center>\n";
	echo "<b><font color=navy size=+1>OSDial STATUSES WITHIN THIS SYSTEM &nbsp; $NWB#osdial_statuses$NWE</font></b><br><br>\n";
	echo "<TABLE width=750 cellspacing=0>\n";
	echo "<tr bgcolor=$menubarcolor><td align=center><font size=1 color=white>STATUS</font></td>";
	echo "<td align=center><font size=1 color=white>DESCRIPTION</font></td>";
	echo "<td><font size=1 color=white>SELECT-<BR>ABLE</font></td>";
	echo "<td><font size=1 color=white>HUMAN<BR>ANSWER</font></td>";
	echo "<td align=center><font size=1 color=white>CATEGORY</font></td>";
	echo "<td align=center><font size=1 color=white>MODIFY / DELETE</font></td></tr>\n";

	##### get status category listings for dynamic pulldown
	$stmt="SELECT vsc_id,vsc_name from vicidial_status_categories order by vsc_id desc";
	$rslt=mysql_query($stmt, $link);
	$cats_to_print = mysql_num_rows($rslt);
	$cats_list="";

	$o=0;
	while ($cats_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$cats_list .= "<option value=\"$rowx[0]\">$rowx[0] - " . substr($rowx[1],0,20) . "</option>\n";
		$catsname_list["$rowx[0]"] = substr($rowx[1],0,20);
		$o++;
		}


	$stmt="SELECT * from vicidial_statuses order by status;";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($statuses_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$AScategory = $rowx[4];
		$o++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=ADD value=421111111111111>\n";
		echo "<input type=hidden name=stage value=modify>\n";
		echo "<input type=hidden name=status value=\"$rowx[0]\">\n";
		echo "<font size=2><B>$rowx[0]</B></td>\n";
		echo "<td align=center><input type=text name=status_name size=20 maxlength=30 value=\"$rowx[1]\"></td>\n";
		echo "<td><select size=1 name=selectable><option>Y</option><option>N</option><option selected>$rowx[2]</option></select></td>\n";
		echo "<td><select size=1 name=human_answered><option>Y</option><option>N</option><option selected>$rowx[3]</option></select></td>\n";
		echo "<td align=center>\n";
		echo "<select size=1 name=category>\n";
		echo "$cats_list";
		echo "<option selected value=\"$AScategory\">$AScategory - $catsname_list[$AScategory]</option>\n";
		echo "</select>\n";
		echo "</td>\n";
		echo "<td align=center nowrap><font size=1><input type=submit name=submit value=MODIFY> &nbsp; &nbsp; \n";
		echo " &nbsp; \n";
		
		if (preg_match("/^B$|^NA$|^DNC$|^NA$|^DROP$|^INCALL$|^QUEUE$|^NEW$/i",$rowx[0]))
			{
			echo "<DEL>DELETE</DEL>\n";
			}
		else
			{
			echo "<a href=\"$PHP_SELF?ADD=421111111111111&status=$rowx[0]&stage=delete\">DELETE</a>\n";
			}
		echo "</form></td></tr>\n";
		}

	echo "</table>\n";

	echo "<br><font color=navy>ADD NEW SYSTEM STATUS<BR><form action=$PHP_SELF method=POST><br>\n";
	echo "<input type=hidden name=ADD value=221111111111111>\n";
	echo "Status: <input type=text name=status size=7 maxlength=6> &nbsp; \n";
	echo "Description: <input type=text name=status_name size=30 maxlength=30><BR><br>\n";
	echo "Selectable: <select size=1 name=selectable><option>Y</option><option>N</option></select> &nbsp; \n";
	echo "Human Answer: <select size=1 name=human_answered><option>Y</option><option>N</option></select> &nbsp; \n";
	echo "Category: \n";
	echo "<select size=1 name=category>\n";
	echo "$cats_list";
	echo "<option selected value=\"$AScategory\">$AScategory - $catsname_list[$AScategory]</option>\n";
	echo "</select> &nbsp; <BR><br>\n";
	echo "<input type=submit name=submit value=ADD><BR></font>\n";

	echo "</FORM><br>\n";

	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}





######################
# ADD=331111111111111 modify vicidial status categories
######################

if ($ADD==331111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<br>\n";
	echo "<b><center><font color=navy>OSDial STATUS CATEGORIES &nbsp; $NWB#osdial_status_categories$NWE</font></center></b><br>\n";
	echo "<TABLE width=700 cellspacing=3>\n";
	echo "<tr><td><font size=2 color=navy>CATEGORY</font></td><td><font size=2 color=navy>NAME</font></td><td><font size=2 color=navy>TO&nbsp;OSDail</font></td><td><font size=2 color=navy>STATUSES IN THIS CATEGORY</font></td></tr>\n";

		$stmt="SELECT * from vicidial_status_categories order by vsc_id;";
		$rslt=mysql_query($stmt, $link);
		$statuses_to_print = mysql_num_rows($rslt);
		$o=0;
		while ($statuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);

			$Avsc_id[$o] = $rowx[0];
			$Avsc_name[$o] = $rowx[1];
			$Avsc_description[$o] = $rowx[2];
			$Atovdad_display[$o] = $rowx[3];

			$o++;
			}
		$p=0;
		while ($o > $p)
			{
			if (eregi("1$|3$|5$|7$|9$", $p))
				{$bgcolor='bgcolor="#CBDCE0"';} 
			else
				{$bgcolor='bgcolor="#C1D6DB"';}

			$CATstatuses='';
			$stmt="SELECT status from vicidial_statuses where category='$Avsc_id[$p]' order by status;";
			$rslt=mysql_query($stmt, $link);
			$statuses_to_print = mysql_num_rows($rslt);
			$q=0;
			while ($statuses_to_print > $q) 
				{
				$rowx=mysql_fetch_row($rslt);
				$CATstatuses.=" $rowx[0]";
				$q++;
				}
			$stmt="SELECT status from vicidial_campaign_statuses where category='$Avsc_id[$p]' order by status;";
			$rslt=mysql_query($stmt, $link);
			$statuses_to_print = mysql_num_rows($rslt);
			$q=0;
			while ($statuses_to_print > $q) 
				{
				$rowx=mysql_fetch_row($rslt);
				$CATstatuses.=" $rowx[0]";
				$q++;
				}

			echo "<tr $bgcolor><td><form action=$PHP_SELF method=POST>\n";
			echo "<input type=hidden name=ADD value=431111111111111>\n";
			echo "<input type=hidden name=stage value=modify>\n";
			echo "<input type=hidden name=vsc_id value=\"$Avsc_id[$p]\">\n";
			echo "<font size=2><B>$Avsc_id[$p]</B></td>\n";
			echo "<td><input type=text name=vsc_name size=30 maxlength=50 value=\"$Avsc_name[$p]\"></td>\n";
			echo "<td><select size=1 name=tovdad_display><option>Y</option><option>N</option><option selected>$Atovdad_display[$p]</option></select></td>\n";
			echo "<td><font size=1>\n";
			echo "$CATstatuses";
			echo "</td></tr>\n";
			echo "<tr $bgcolor><td colspan=4><font size=1>Description: <input type=text name=vsc_description size=90 maxlength=255 value=\"$Avsc_description[$p]\"></td></tr>\n";
			echo "<tr $bgcolor><td colspan=4 align=center><font size=1><input type=submit name=submit value=MODIFY> &nbsp; &nbsp; &nbsp; &nbsp; \n";
			echo " &nbsp; <a href=\"$PHP_SELF?ADD=431111111111111&vsc_id=$Avsc_id[$p]&stage=delete\">DELETE</a></td></tr>\n";
			echo "<tr><td colspan=4><font size=1> &nbsp; </form></td></tr>\n";

			$p++;
			}

	echo "</table>\n";

	echo "<center><br><font color=navy>ADD NEW STATUS CATEGORY<BR><br></center><form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=ADD value=231111111111111>\n";
	echo "Category ID: <input type=text name=vsc_id size=20 maxlength=20> &nbsp; \n";
	echo "Name: <input type=text name=vsc_name size=20 maxlength=50> &nbsp; \n";
	echo "Time On Dialer Display: <select size=1 name=tovdad_display><option>Y</option><option>N</option></select> &nbsp; <BR><br>\n";
	echo "Description: <input type=text name=vsc_description size=80 maxlength=255><BR><br>\n";
	echo "<center><input type=submit name=submit value=ADD></center></font>\n";

	echo "</FORM><br>\n";

	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}






######################
# ADD=550 search form
######################

if ($ADD==550)
{
echo "<TABLE align=center><TR><TD>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>SEARCH FOR A USER</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=660>\n";
echo "<TABLE width=$section_width cellspacing=3>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Agent Number: </td><td align=left><input type=text name=user size=20 maxlength=20></td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=30 maxlength=30></td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>User Level: </td><td align=left><select size=1 name=user_level><option selected>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option></select></td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>User Group: </td><td align=left><select size=1 name=user_group>\n";

	$stmt="SELECT * from vicidial_user_groups order by user_group";
	$rslt=mysql_query($stmt, $link);
	$groups_to_print = mysql_num_rows($rslt);
	$o=0;
	$groups_list='';
	while ($groups_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$groups_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
	}
echo "$groups_list</select></td></tr>\n";

echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=search value=search></td></tr>\n";
echo "</TABLE></center>\n";

}

######################
# ADD=660 user search results
######################

if ($ADD==660)
{
echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$SQL = '';
	if ($user) {$SQL .= " user LIKE \"%$user%\" and";}
	if ($full_name) {$SQL .= " full_name LIKE \"%$full_name%\" and";}
	if ($user_level > 0) {$SQL .= " user_level LIKE \"%$user_level%\" and";}
	if ($user_group) {$SQL .= " user_group = '$user_group' and";}
	$SQL = eregi_replace(" and$", "", $SQL);
	if (strlen($SQL)>5) {$SQL = "where $SQL";}

	$stmt="SELECT * from vicidial_users $SQL order by full_name desc;";
#	echo "\n|$stmt|\n";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<br><font color=navy> SEARCH RESULTS:</font>\n";
echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1>$row[1]</td><td><font size=1>$row[3]</td><td><font size=1>$row[4]</td><td><font size=1>$row[5]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=3&user=$row[1]\">MODIFY</a> | <a href=\"./user_stats.php?user=$row[1]\">STATS</a> | <a href=\"./user_status.php?user=$row[1]\">STATUS</a> | <a href=\"./AST_agent_time_sheet.php?agent=$row[1]\">TIME</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";

}


######################################################################################################
######################################################################################################
#######   8 series, Callback lists
######################################################################################################
######################################################################################################

######################
# ADD=8 find all callbacks on hold by an Agent
######################
if ($ADD==8)
{
	if ($LOGmodify_users==1)
	{
		if ($SUB==89)
		{
		$stmt="UPDATE vicidial_callbacks SET status='INACTIVE' where user='$user' and status='LIVE' and callback_time < '$past_month_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>Agent ($user) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899)
		{
		$stmt="UPDATE vicidial_callbacks SET status='INACTIVE' where user='$user' and status='LIVE' and callback_time < '$past_week_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>Agent ($user) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
$CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=8&SUB=89&user=$user\"><font color=navy>Remove LIVE Callbacks older than one month for this user</font></a><BR><a href=\"$PHP_SELF?ADD=8&SUB=899&user=$user\"><font color=navy>Remove LIVE Callbacks older than one week for this user</font></a><BR>";

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$CBquerySQLwhere = "and user='$user'";

echo "<br><font color=navy> USER CALLBACK HOLD LISTINGS: $user</font>\n";
$oldADD = "ADD=8&user=$user";
$ADD='82';
}

######################
# ADD=81 find all callbacks on hold within a Campaign
######################
if ($ADD==81)
{
	if ($LOGmodify_campaigns==1)
	{
		if ($SUB==89)
		{
		$stmt="UPDATE vicidial_callbacks SET status='INACTIVE' where campaign_id='$campaign_id' and status='LIVE' and callback_time < '$past_month_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>campaign($campaign_id) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899)
		{
		$stmt="UPDATE vicidial_callbacks SET status='INACTIVE' where campaign_id='$campaign_id' and status='LIVE' and callback_time < '$past_week_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>campaign($campaign_id) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
$CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=81&SUB=89&campaign_id=$campaign_id\"><font color=navy>Remove LIVE Callbacks older than one month for this campaign</font></a><BR><a href=\"$PHP_SELF?ADD=81&SUB=899&campaign_id=$campaign_id\"><font color=navy>Remove LIVE Callbacks older than one week for this campaign</font></a><BR>";

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$CBquerySQLwhere = "and campaign_id='$campaign_id'";

echo "<br><font color=navy> CAMPAIGN CALLBACK HOLD LISTINGS: $campaign_id</font>\n";
$oldADD = "ADD=81&campaign_id=$campaign_id";
$ADD='82';
}

######################
# ADD=811 find all callbacks on hold within a List
######################
if ($ADD==811)
{
	if ($LOGmodify_lists==1)
	{
		if ($SUB==89)
		{
		$stmt="UPDATE vicidial_callbacks SET status='INACTIVE' where list_id='$list_id' and status='LIVE' and callback_time < '$past_month_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>list($list_id) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899)
		{
		$stmt="UPDATE vicidial_callbacks SET status='INACTIVE' where list_id='$list_id' and status='LIVE' and callback_time < '$past_week_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>list($list_id) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
$CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=811&SUB=89&list_id=$list_id\"><font color=navy>Remove LIVE Callbacks older than one month for this list</font></a><BR><a href=\"$PHP_SELF?ADD=811&SUB=899&list_id=$list_id\"><font color=navy>Remove LIVE Callbacks older than one week for this list</font></a><BR>";

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$CBquerySQLwhere = "and list_id='$list_id'";

echo "<br><font color=navy> LIST CALLBACK HOLD LISTINGS: $list_id</font>\n";
$oldADD = "ADD=811&list_id=$list_id";
$ADD='82';
}

######################
# ADD=8111 find all callbacks on hold within a user group
######################
if ($ADD==8111)
{
	if ($LOGmodify_usergroups==1)
	{
		if ($SUB==89)
		{
		$stmt="UPDATE vicidial_callbacks SET status='INACTIVE' where user_group='$user_group' and status='LIVE' and callback_time < '$past_month_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>user group($user_group) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899)
		{
		$stmt="UPDATE vicidial_callbacks SET status='INACTIVE' where user_group='$user_group' and status='LIVE' and callback_time < '$past_week_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>user group($user_group) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
	$CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=8111&SUB=89&user_group=$user_group\"><font color=navy>Remove LIVE Callbacks older than one month for this user group</font></a><BR><a href=\"$PHP_SELF?ADD=8111&SUB=899&user_group=$user_group\"><font color=navy>Remove LIVE Callbacks older than one week for this user group</font></a><BR>";

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$CBquerySQLwhere = "and user_group='$user_group'";

echo "<br><font color=navy> USER GROUP CALLBACK HOLD LISTINGS: $list_id</font>\n";
$oldADD = "ADD=8111&user_group=$user_group";
$ADD='82';
}

######################
# ADD=82 display all callbacks on hold
######################
if ($ADD==82)
{

$USERlink='stage=USERIDDOWN';
$GROUPlink='stage=GROUPDOWN';
$ENDATElink='stage=ENDATEDOWN';
$SQLorder='order by ';
if (eregi("USERIDDOWN",$stage)) {$SQLorder='order by user desc,';   $USERlink='stage=USERIDUP';}
if (eregi("GROUPDOWN",$stage)) {$SQLorder='order by user_group desc,';   $NAMElink='stage=NAMEUP';}
if (eregi("ENDATEDOWN",$stage)) {$SQLorder='order by entry_time desc,';   $LEVELlink='stage=LEVELUP';}

	$stmt="SELECT * from vicidial_callbacks where status IN('ACTIVE','LIVE') $CBquerySQLwhere $SQLorder recipient,status desc,callback_time";
	$rslt=mysql_query($stmt, $link);
	$cb_to_print = mysql_num_rows($rslt);

echo "<TABLE><TR><TD>\n";
echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=navy>\n";
echo "<td><font size=1 color=white>LEAD</td><td><font size=1 color=white>LIST</td>\n";
echo "<td><font size=1 color=white> CAMPAIGN</td>\n";
echo "<td><a href=\"$PHP_SELF?$oldADD&$ENDATElink\"><font size=1 color=white><B>ENTRY DATE</B></a></td>\n";
echo "<td><font size=1 color=white>CALLBACK DATE</td>\n";
echo "<td><a href=\"$PHP_SELF?$oldADD&$USERlink\"><font size=1 color=white><B>USER</B></a></td>\n";
echo "<td><font size=1 color=white>RECIPIENT</td>\n";
echo "<td><font size=1 color=white>STATUS</td>\n";
echo "<td><a href=\"$PHP_SELF?$oldADD&$GROUPlink\"><font size=1 color=white><B>GROUP</B></a></td></tr>\n";

	$o=0;
	while ($cb_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor>";
		echo "<td><font size=1><A HREF=\"admin_modify_lead.php?lead_id=$row[1]\" target=\"_blank\">$row[1]</A></td>";
		echo "<td><font size=1><A HREF=\"$PHP_SELF?ADD=311&list_id=$row[2]\">$row[2]</A></td>";
		echo "<td><font size=1><A HREF=\"$PHP_SELF?ADD=31&campaign_id=$row[3]\">$row[3]</A></td>";
		echo "<td><font size=1>$row[5]</td>";
		echo "<td><font size=1>$row[6]</td>";
		echo "<td><font size=1><A HREF=\"$PHP_SELF?ADD=3&user=$row[8]\">$row[8]</A></td>";
		echo "<td><font size=1>$row[9]</td>";
		echo "<td><font size=1>$row[4]</td>";
		echo "<td><font size=1><A HREF=\"$PHP_SELF?ADD=311111&user_group=$row[11]\">$row[11]</A></td>";
		echo "</tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";

echo "$CBinactiveLINK";
}



######################################################################################################
######################################################################################################
#######   0 series, displays and searches
######################################################################################################
######################################################################################################

######################
# ADD=0 display all active users
######################
if ($ADD==0)
{
echo "<TABLE align=center><TR><TD>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

$USERlink='stage=USERIDDOWN';
$NAMElink='stage=NAMEDOWN';
$LEVELlink='stage=LEVELDOWN';
$GROUPlink='stage=GROUPDOWN';
$SQLorder='order by full_name';
if (eregi("USERIDUP",$stage)) {$SQLorder='order by user asc';   $USERlink='stage=USERIDDOWN';}
if (eregi("USERIDDOWN",$stage)) {$SQLorder='order by user desc';   $USERlink='stage=USERIDUP';}
if (eregi("NAMEUP",$stage)) {$SQLorder='order by full_name asc';   $NAMElink='stage=NAMEDOWN';}
if (eregi("NAMEDOWN",$stage)) {$SQLorder='order by full_name desc';   $NAMElink='stage=NAMEUP';}
if (eregi("LEVELUP",$stage)) {$SQLorder='order by user_level asc';   $LEVELlink='stage=LEVELDOWN';}
if (eregi("LEVELDOWN",$stage)) {$SQLorder='order by user_level desc';   $LEVELlink='stage=LEVELUP';}
if (eregi("GROUPUP",$stage)) {$SQLorder='order by user_group asc';   $GROUPlink='stage=GROUPDOWN';}
if (eregi("GROUPDOWN",$stage)) {$SQLorder='order by user_group desc';   $GROUPlink='stage=GROUPUP';}
	$stmt="SELECT * from vicidial_users $SQLorder";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font size=+1 color=navy>AGENTS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1 align=center>\n";
echo "<tr bgcolor=#716A5B>";
echo "<td><a href=\"$PHP_SELF?ADD=0&$USERlink\"><font size=1 color=white><B>USER ID</B></a></td>";
echo "<td><a href=\"$PHP_SELF?ADD=0&$NAMElink\"><font size=1 color=white><B>FULL NAME</B></a></td>";
echo "<td><a href=\"$PHP_SELF?ADD=0&$LEVELlink\"><font size=1 color=white><B>LEVEL</B></a></td>";
echo "<td><a href=\"$PHP_SELF?ADD=0&$GROUPlink\"><font size=1 color=white><B>GROUP</B></a></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td></tr>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><a href=\"$PHP_SELF?ADD=3&user=$row[1]\"><font size=1 color=black>$row[1]</a></td><td><font size=1>$row[3]</td><td><font size=1>$row[4]</td><td><font size=1>$row[5]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=3&user=$row[1]\">MODIFY</a> | <a href=\"./user_stats.php?user=$row[1]\">STATS</a> | <a href=\"./user_status.php?user=$row[1]\">STATUS</a> | <a href=\"./AST_agent_time_sheet.php?agent=$row[1]\">TIME</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}

######################
# ADD=10 display all campaigns
######################
if ($ADD==10)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>CAMPAIGNS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=#716A5B>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td align=center><font size=1 color=white><B>ACTIVE</B></td>";
echo "<td align=center colspan=7><font size=1 color=white><B>LINKS</B></td>";
	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=34&campaign_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1] </td>";
		echo "<td align=center><font size=1> $row[2]</td>";
		echo "<td><font size=1> $row[3]</td><td><font size=1>$row[4]</td><td><font size=1>$row[5]</td>";
		echo "<td><font size=1> $row[6]</td><td><font size=1>$row[7]</td><td><font size=1> &nbsp;</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}


######################
# ADD=100 display all lists
######################
if ($ADD==100)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_lists order by list_id";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>LISTS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td><font size=1 color=white><B>CAMPAIGN</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td align=center><font size=1 color=white><B>MODIFIED</B></td>";
echo "<td align=center><font size=1 color=white><B>ACTIVE</B></td>";
echo "<td align=center colspan=3><font size=1 color=white><B>LINKS</B></td>";
	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td><font size=1> $row[2]</td>";
		echo "<td><font size=1>$row[4]</td>";
		echo "<td align=center><font size=1>$row[5]</td>";
		echo "<td align=center><font size=1> $row[3]</td>";
		echo "<td><font size=1>$row[7]</td>";
		echo "<td><font size=1> &nbsp;</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}



######################
# ADD=1000 display all inbound groups
######################
if ($ADD==1000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_inbound_groups order by group_id";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>INBOUND GROUPS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td align=center><font size=1 color=white><B>ACTIVE</B></td>";
echo "<td align=center><font size=1 color=white><B>VOICEMAIL</B></td>";
echo "<td align=center><font size=1 color=white><B>COLOR</B></td>";
echo "<td align=center colspan=1><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=3111&group_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td align=center><font size=1> $row[3]</td>";
		echo "<td align=center><font size=1> $row[5]</td>";
		echo "<td width=6 bgcolor=\"$row[2]\"><font size=1> &nbsp;</td>";
		echo "<td><font size=1>&nbsp;<a href=\"$PHP_SELF?ADD=3111&group_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}


######################
# ADD=10000 display all remote agents
######################
if ($ADD==10000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_remote_agents order by server_ip,campaign_id,user_start";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>OFF-HOOK AGENTS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td align=center><font size=1 color=white><B>LINES</B></td>";
echo "<td align=center><font size=1 color=white><B>SERVER</B></td>";
echo "<td><font size=1 color=white><B>EXTENSION</B></td>";
echo "<td align=center><font size=1 color=white><B>ACTIVE</B></td>";
echo "<td><font size=1 color=white><B>CAMPAIGN</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111&remote_agent_id=$row[0]\">$row[1]</a></td>";
		echo "<td align=center><font size=1> $row[2]</td>";
		echo "<td align=center><font size=1> $row[3]</td>";
		echo "<td><font size=1> $row[4]</td>";
		echo "<td align=center><font size=1> $row[5]</td>";
		echo "<td><font size=1> $row[6]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31111&remote_agent_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}


######################
# ADD=100000 display all user groups
######################
if ($ADD==100000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_user_groups order by user_group";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>USER GROUPS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=311111&user_group=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=311111&user_group=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}


######################
# ADD=1000000 display all scripts
######################
if ($ADD==1000000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_scripts order by script_id";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>SCRIPTS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=3111111&script_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=3111111&script_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}


######################
# ADD=10000000 display all filters
######################
if ($ADD==10000000)
{
echo "<TABLE align=center><TR><TD>\n";

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_lead_filters order by lead_filter_id";
	$rslt=mysql_query($stmt, $link);
	$filters_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>LEAD FILTERS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($filters_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}


######################
# ADD=100000000 display all call times
######################
if ($ADD==100000000)
{
echo "<TABLE align=center><TR><TD>\n";

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_call_times order by call_time_id";
	$rslt=mysql_query($stmt, $link);
	$filters_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>CALL TIMES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td align=center><font size=1 color=white><B>START</B></td>";
echo "<td align=center><font size=1 color=white><B>STOP</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($filters_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td align=center><font size=1> $row[3] </td>";
		echo "<td align=center><font size=1> $row[4] </td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}

######################
# ADD=1000000000 display all state call times
######################
if ($ADD==1000000000)
{
echo "<TABLE align=center><TR><TD>\n";

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_state_call_times order by state_call_time_id";
	$rslt=mysql_query($stmt, $link);
	$filters_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>STATE CALL TIMES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=navy>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td align=center><font size=1 color=white><B>STATE</B></td>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td><font size=1 color=white><B>COMMENT</B></td>";
echo "<td align=center><font size=1 color=white><B>START</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($filters_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=3111111111&call_time_id=$row[0]\">$row[0]</a></td>";
		echo "<td align=center><font size=1> $row[1]</td>";
		echo "<td><font size=1> $row[2]</td>";
		echo "<td><font size=1> $row[3]</td>";
		echo "<td align=center><font size=1> $row[4] </td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=3111111111&call_time_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}

######################
# ADD=10000000000 display all phones
######################
if ($ADD==10000000000)
{
echo "<TABLE align=center><TR><TD>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

$EXTENlink='stage=EXTENDOWN';
$PROTOlink='stage=PROTODOWN';
$SERVERlink='stage=SERVERDOWN';
$STATUSlink='stage=STATUSDOWN';
$SQLorder='order by extension,server_ip';
if (eregi("EXTENUP",$stage)) {$SQLorder='order by extension asc';   $EXTENlink='stage=EXTENDOWN';}
if (eregi("EXTENDOWN",$stage)) {$SQLorder='order by extension desc';   $EXTENlink='stage=EXTENUP';}
if (eregi("PROTOUP",$stage)) {$SQLorder='order by protocol asc';   $PROTOlink='stage=PROTODOWN';}
if (eregi("PROTODOWN",$stage)) {$SQLorder='order by protocol desc';   $PROTOlink='stage=PROTOUP';}
if (eregi("SERVERUP",$stage)) {$SQLorder='order by server_ip asc';   $SERVERlink='stage=SERVERDOWN';}
if (eregi("SERVERDOWN",$stage)) {$SQLorder='order by server_ip desc';   $SERVERlink='stage=SERVERUP';}
if (eregi("STATUSUP",$stage)) {$SQLorder='order by status asc';   $STATUSlink='stage=STATUSDOWN';}
if (eregi("STATUSDOWN",$stage)) {$SQLorder='order by status desc';   $STATUSlink='stage=STATUSUP';}
	$stmt="SELECT * from phones $SQLorder";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>PHONES<br><br><font size=-2>(<a href=#VMList>VoiceMail List</a>)</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><a href=\"$PHP_SELF?ADD=10000000000&$EXTENlink\"><font size=1 color=white><B>EXTEN</B></a></td>";
echo "<td><a href=\"$PHP_SELF?ADD=10000000000&$PROTOlink\"><font size=1 color=white><B>PROTO</B></a></td>";
echo "<td><a href=\"$PHP_SELF?ADD=10000000000&$SERVERlink\"><font size=1 color=white><B>SERVER</B></a></td>";
echo "<td colspan=2><font size=1 color=white><B>DIAL PLAN</B></td>";
echo "<td><a href=\"$PHP_SELF?ADD=10000000000&$STATUSlink\"><font size=1 color=white><B>STATUS</B></a></td>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td colspan=2><font size=1 color=white><B>VMAIL</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td></tr>\n";

	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><a href=\"$PHP_SELF?ADD=31111111111&extension=$row[0]&server_ip=$row[5]\"><font size=1 color=black>$row[0]</font></a></td><td><font size=1>$row[16]</td><td><font size=1>$row[5]</td><td><font size=1>$row[1]</td><td><font size=1>$row[2]</td><td><font size=1>$row[8]</td><td><font size=1>$row[11]</td><td><font size=1>$row[14]</td><td><font size=1>$row[15]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31111111111&extension=$row[0]&server_ip=$row[5]\">MODIFY</a> | <a href=\"./phone_stats.php?extension=$row[0]&server_ip=$row[5]\">STATS</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";

// List all voicemail on dialer 1
echo "<a name=VMList></a>";
echo '<br><br><br><br>';
echo '<center>';
echo '<b><font color=navy size=-1>VOICE MAIL</b><br>';
if (file_exists ('VMnow.txt') ) {
	echo "<font color=navy><p> As of " . date("l dS o F h:i:s A",filectime('VMnow.txt') )  . "</p></font>";
	echo "<table align=center width=560><tr>";
	echo "<td width=10 align=center><font color=navy>Context</td>";
	echo "<td width=30 align=center><font color=navy>Mbox</font></td>";
	echo "<td width=110 align=center><font color=navy>Agent</font></td>";
	// no vmZone defined  echo "<td width=50><font color=navy>Zone</font></td>";
	echo "<td width=35 align=right><font color=navy>NewMsgs</font></td><tr>";
	echo "</table>";
	// get a web page into an array and print it out ("l dS of F Y h:i:s A")
	$fcontents = file( 'VMnow.txt' );
	echo "<table>";
	while ( list( $line_num, $line ) = each( $fcontents ) ) {
		// Exit if the Verbosity line shows up - Obscured by only listing vm context 'default'
		//if ( substr($line,0,9) == "Verbosity") {
		//        break;
		//}
		// Ensuring only vm entries show up
		if ( substr($line,0,7) == "default" ) {
			echo "<tr><td><pre>" . $line . "</td></tr>";
		}
	}
	echo "</table>";
} else {
	echo "Error! VMnow.txt is missing!";
}

}

######################
# ADD=100000000000 display all servers
######################
if ($ADD==100000000000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from servers order by server_id";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>SERVERS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td><font size=1 color=white><B>SERVER</B></td>";
echo "<td><font size=1 color=white><B>ASTERISK</B></td>";
echo "<td align=center><font size=1 color=white><B>ACTIVE</B></td>";
echo "<td align=center colspan=2><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=311111111111&server_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1>$row[1]</td>";
		echo "<td><font size=1> $row[2]</td>";
		echo "<td><font size=1> $row[4]</td>";
		echo "<td align=center><font size=1> $row[3]</td>";
		echo "<td><font size=1> &nbsp;</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=311111111111&server_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}

######################
# ADD=1000000000000 display all conferences
######################
if ($ADD==1000000000000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from conferences order by conf_exten";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>CONFERENCES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>SERVER</B></td>";
echo "<td><font size=1 color=white><B>EXTENSION</B></td>";
echo "<td align=center colspan=3><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$row[0]&server_ip=$row[1]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td><font size=1> $row[2]</td>";
		echo "<td><font size=1>$row[4]</td>";
		echo "<td><font size=1> &nbsp;</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$row[0]&server_ip=$row[1]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}

######################
# ADD=10000000000000 display all vicidial conferences
######################
if ($ADD==10000000000000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_conferences order by conf_exten";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>CONFERENCES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=navy>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>SERVER</B></td>";
echo "<td><font size=1 color=white><B>EXTENSION</B></td>";
echo "<td align=center colspan=3><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111111111111&conf_exten=$row[0]&server_ip=$row[1]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td><font size=1> $row[2]</td><td><font size=1>$row[4]</td><td><font size=1> &nbsp;</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31111111111111&conf_exten=$row[0]&server_ip=$row[1]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}

######################
# ADD=999999 display reports section
######################
if ($ADD==999999)
{
	if ($LOGview_reports==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_conferences order by conf_exten";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

	$stmt="select * from servers;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$servers_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $servers_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$server_id[$i] =			$row[0];
		$server_description[$i] =	$row[1];
		$server_ip[$i] =			$row[2];
		$active[$i] =				$row[3];
		$i++;
		}

	$stmt="SELECT enable_queuemetrics_logging,queuemetrics_url from system_settings;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$enable_queuemetrics_logging_LU =	$row[0];
	$queuemetrics_url_LU =				$row[1];

	?>

	<HTML>
	<HEAD>

	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
	<TITLE>OSDial: Server Stats and Reports</TITLE></HEAD><BODY BGCOLOR=WHITE>
	<FONT SIZE=4 color=navy><br><center>SERVER STATS AND REPORTS</center></font><BR><BR>
	<UL class=>
	<LI><a href="AST_timeonVDADall.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Time On Dialer (per campaign)</a> &nbsp;  <a href="AST_timeonVDADallSUMMARY.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>(all campaigns SUMMARY)</a> &nbsp; &nbsp; SIP <a href="AST_timeonVDADall.php?SIPmonitorLINK=1"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Listen</a> - <a href="AST_timeonVDADall.php?SIPmonitorLINK=2"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Barge</a> &nbsp; &nbsp; IAX <a href="AST_timeonVDADall.php?IAXmonitorLINK=1"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Listen</a> - <a href="AST_timeonVDADall.php?IAXmonitorLINK=2"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Barge</a></FONT>
	<LI><a href="AST_VDADstats.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Call Report</a></FONT>
	<LI><a href="AST_CLOSERstats.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Closer Report</a></FONT>
	<LI><a href="AST_agent_performance_detail.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Agent Performance Detail</a></FONT>
	<LI><a href="vicidial_sales_viewer.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Agent Spreadsheet Performance</a></FONT>
	<LI><a href="AST_server_performance.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Server Performance</a></FONT>
<?
	if ($enable_queuemetrics_logging_LU > 0)
		{
		echo "<LI><a href=\"$queuemetrics_url_LU\"><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>QUEUEMETRICS REPORTS</a></FONT>\n";
		}
?>
	</UL>
	<PRE><table frame=box CELLPADDING=0 cellspacing=4>
	<TR>
		<TD align=center><font color=#1C4754>&nbsp;Server&nbsp;</TD>
		<TD align=center><font color=#1C4754>&nbsp;Description&nbsp;</TD>
		<TD align=center><font color=#1C4754>&nbsp;IP Address&nbsp;</TD>
		<TD align=center><font color=#1C4754>&nbsp;Active&nbsp;</TD>
		<TD align=center><font color=#1C4754>&nbsp;Dialer Time&nbsp;</TD>
		<TD align=center><font color=#1C4754>&nbsp;Park Time&nbsp;</TD>
		<TD align=center><font color=#1C4754>&nbsp;Closer/Inbound Time&nbsp;</TD>
	</TR>
	<? 

		$o=0;
		while ($servers_to_print > $o)
		{
		echo "<TR>";
		echo "	<TD align=center>$server_id[$o]</TD>\n";
		echo "	<TD align=center>$server_description[$o]</TD>\n";
		echo "	<TD align=center>$server_ip[$o]</TD>\n";
		echo "	<TD align=center>$active[$o]</TD>\n";
		echo "	<TD align=center><a href=\"AST_timeonVDAD.php?server_ip=$server_ip[$o]\">LINK</a></TD>\n";
		echo "	<TD align=center><a href=\"AST_timeonpark.php?server_ip=$server_ip[$o]\">LINK</a></TD>\n";
		echo "	<TD align=center><a href=\"AST_timeonVDAD.php?server_ip=$server_ip[$o]&closer_display=1\">LINK</a></TD>\n";
		echo "</TR>";
		$o++;
		}

	echo "</TABLE>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


#################################################################################









###################################### Footer ########################################### 


echo "</TD></TR></TABLE></center>\n";

$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);

echo "<br><br><br><br><br>";
echo "<table width=100% cellspacing=0 cellpadding=0>";
echo "	<TR><TD ALIGN=LEFT COLSPAN=3 HEIGHT=1 BGCOLOR=#999999></TD></TR>";
echo "	<tr bgcolor=#A3C1C9>";
echo "		<td height=15 align=left width=33%><font size=0 color=navy>&nbsp;&nbsp;Script Runtime: $RUNtime sec</td>";
echo "    	<td align=center width=33%><font size=0 color=navy>Version: $admin_version</td>";
echo "    	<td align=right width=33%><font size=0 color=navy>Build: $build&nbsp;&nbsp;</td>";
echo "	</tr>";
echo "<TR><TD ALIGN=LEFT COLSPAN=3 HEIGHT=1 BGCOLOR=#666666></TD></TR>";
echo "</table>";
?>


</TD></TR>
</TABLE>
<br>
</div>
</body>
</html>

<?
	
exit;




##### CALCULATE DIALABLE LEADS #####
function dialable_leads($DB,$link,$local_call_time,$dial_statuses,$camp_lists,$fSQL)
{
##### BEGIN calculate what gmt_offset_now values are within the allowed local_call_time setting ###
if (isset($camp_lists))
	{
	if (strlen($camp_lists)>1)
		{
		if (strlen($dial_statuses)>2)
			{
			$g=0;
			$p='13';
			$GMT_gmt[0] = '';
			$GMT_hour[0] = '';
			$GMT_day[0] = '';
			while ($p > -13)
				{
				$pzone=3600 * $p;
				$pmin=(gmdate("i", time() + $pzone));
				$phour=( (gmdate("G", time() + $pzone)) * 100);
				$pday=gmdate("w", time() + $pzone);
				$tz = sprintf("%.2f", $p);	
				$GMT_gmt[$g] = "$tz";
				$GMT_day[$g] = "$pday";
				$GMT_hour[$g] = ($phour + $pmin);
				$p = ($p - 0.25);
				$g++;
				}

			$stmt="SELECT * FROM vicidial_call_times where call_time_id='$local_call_time';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$rowx=mysql_fetch_row($rslt);
			$Gct_default_start =	"$rowx[3]";
			$Gct_default_stop =		"$rowx[4]";
			$Gct_sunday_start =		"$rowx[5]";
			$Gct_sunday_stop =		"$rowx[6]";
			$Gct_monday_start =		"$rowx[7]";
			$Gct_monday_stop =		"$rowx[8]";
			$Gct_tuesday_start =	"$rowx[9]";
			$Gct_tuesday_stop =		"$rowx[10]";
			$Gct_wednesday_start =	"$rowx[11]";
			$Gct_wednesday_stop =	"$rowx[12]";
			$Gct_thursday_start =	"$rowx[13]";
			$Gct_thursday_stop =	"$rowx[14]";
			$Gct_friday_start =		"$rowx[15]";
			$Gct_friday_stop =		"$rowx[16]";
			$Gct_saturday_start =	"$rowx[17]";
			$Gct_saturday_stop =	"$rowx[18]";
			$Gct_state_call_times = "$rowx[19]";

			$ct_states = '';
			$ct_state_gmt_SQL = '';
			$ct_srs=0;
			$b=0;
			if (strlen($Gct_state_call_times)>2)
				{
				$state_rules = explode('|',$Gct_state_call_times);
				$ct_srs = ((count($state_rules)) - 2);
				}
			while($ct_srs >= $b)
				{
				if (strlen($state_rules[$b])>1)
					{
					$stmt="SELECT * from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
					$rslt=mysql_query($stmt, $link);
					$row=mysql_fetch_row($rslt);
					$Gstate_call_time_id =		"$row[0]";
					$Gstate_call_time_state =	"$row[1]";
					$Gsct_default_start =		"$row[4]";
					$Gsct_default_stop =		"$row[5]";
					$Gsct_sunday_start =		"$row[6]";
					$Gsct_sunday_stop =			"$row[7]";
					$Gsct_monday_start =		"$row[8]";
					$Gsct_monday_stop =			"$row[9]";
					$Gsct_tuesday_start =		"$row[10]";
					$Gsct_tuesday_stop =		"$row[11]";
					$Gsct_wednesday_start =		"$row[12]";
					$Gsct_wednesday_stop =		"$row[13]";
					$Gsct_thursday_start =		"$row[14]";
					$Gsct_thursday_stop =		"$row[15]";
					$Gsct_friday_start =		"$row[16]";
					$Gsct_friday_stop =			"$row[17]";
					$Gsct_saturday_start =		"$row[18]";
					$Gsct_saturday_stop =		"$row[19]";

					$ct_states .="'$Gstate_call_time_state',";

					$r=0;
					$state_gmt='';
					while($r < $g)
						{
						if ($GMT_day[$r]==0)	#### Sunday local time
							{
							if (($Gsct_sunday_start==0) and ($Gsct_sunday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gsct_sunday_start) and ($GMT_hour[$r]<$Gsct_sunday_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==1)	#### Monday local time
							{
							if (($Gsct_monday_start==0) and ($Gsct_monday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gsct_monday_start) and ($GMT_hour[$r]<$Gsct_monday_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==2)	#### Tuesday local time
							{
							if (($Gsct_tuesday_start==0) and ($Gsct_tuesday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gsct_tuesday_start) and ($GMT_hour[$r]<$Gsct_tuesday_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==3)	#### Wednesday local time
							{
							if (($Gsct_wednesday_start==0) and ($Gsct_wednesday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gsct_wednesday_start) and ($GMT_hour[$r]<$Gsct_wednesday_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==4)	#### Thursday local time
							{
							if (($Gsct_thursday_start==0) and ($Gsct_thursday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gsct_thursday_start) and ($GMT_hour[$r]<$Gsct_thursday_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==5)	#### Friday local time
							{
							if (($Gsct_friday_start==0) and ($Gsct_friday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gsct_friday_start) and ($GMT_hour[$r]<$Gsct_friday_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						if ($GMT_day[$r]==6)	#### Saturday local time
							{
							if (($Gsct_saturday_start==0) and ($Gsct_saturday_stop==0))
								{
								if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							else
								{
								if ( ($GMT_hour[$r]>=$Gsct_saturday_start) and ($GMT_hour[$r]<$Gsct_saturday_stop) )
									{$state_gmt.="'$GMT_gmt[$r]',";}
								}
							}
						$r++;
						}
					$state_gmt = "$state_gmt'99'";
					$ct_state_gmt_SQL .= "or (state='$Gstate_call_time_state' and gmt_offset_now IN($state_gmt)) ";
					}

				$b++;
				}
			if (strlen($ct_states)>2)
				{
				$ct_states = eregi_replace(",$",'',$ct_states);
				$ct_statesSQL = "and state NOT IN($ct_states)";
				}
			else
				{
				$ct_statesSQL = "";
				}

			$r=0;
			$default_gmt='';
			while($r < $g)
				{
				if ($GMT_day[$r]==0)	#### Sunday local time
					{
					if (($Gct_sunday_start==0) and ($Gct_sunday_stop==0))
						{
						if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					else
						{
						if ( ($GMT_hour[$r]>=$Gct_sunday_start) and ($GMT_hour[$r]<$Gct_sunday_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					}
				if ($GMT_day[$r]==1)	#### Monday local time
					{
					if (($Gct_monday_start==0) and ($Gct_monday_stop==0))
						{
						if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					else
						{
						if ( ($GMT_hour[$r]>=$Gct_monday_start) and ($GMT_hour[$r]<$Gct_monday_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					}
				if ($GMT_day[$r]==2)	#### Tuesday local time
					{
					if (($Gct_tuesday_start==0) and ($Gct_tuesday_stop==0))
						{
						if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					else
						{
						if ( ($GMT_hour[$r]>=$Gct_tuesday_start) and ($GMT_hour[$r]<$Gct_tuesday_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					}
				if ($GMT_day[$r]==3)	#### Wednesday local time
					{
					if (($Gct_wednesday_start==0) and ($Gct_wednesday_stop==0))
						{
						if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					else
						{
						if ( ($GMT_hour[$r]>=$Gct_wednesday_start) and ($GMT_hour[$r]<$Gct_wednesday_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					}
				if ($GMT_day[$r]==4)	#### Thursday local time
					{
					if (($Gct_thursday_start==0) and ($Gct_thursday_stop==0))
						{
						if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					else
						{
						if ( ($GMT_hour[$r]>=$Gct_thursday_start) and ($GMT_hour[$r]<$Gct_thursday_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					}
				if ($GMT_day[$r]==5)	#### Friday local time
					{
					if (($Gct_friday_start==0) and ($Gct_friday_stop==0))
						{
						if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					else
						{
						if ( ($GMT_hour[$r]>=$Gct_friday_start) and ($GMT_hour[$r]<$Gct_friday_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					}
				if ($GMT_day[$r]==6)	#### Saturday local time
					{
					if (($Gct_saturday_start==0) and ($Gct_saturday_stop==0))
						{
						if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					else
						{
						if ( ($GMT_hour[$r]>=$Gct_saturday_start) and ($GMT_hour[$r]<$Gct_saturday_stop) )
							{$default_gmt.="'$GMT_gmt[$r]',";}
						}
					}
				$r++;
				}

			$default_gmt = "$default_gmt'99'";
			$all_gmtSQL = "(gmt_offset_now IN($default_gmt) $ct_statesSQL) $ct_state_gmt_SQL";


			$dial_statuses = preg_replace("/ -$/","",$dial_statuses);
			$Dstatuses = explode(" ", $dial_statuses);
			$Ds_to_print = (count($Dstatuses) - 0);
			$Dsql = '';
			$o=0;
			while ($Ds_to_print > $o) 
				{
				$o++;
				$Dsql .= "'$Dstatuses[$o]',";
				}
			$Dsql = preg_replace("/,$/","",$Dsql);

			$stmt="SELECT count(*) FROM vicidial_list where called_since_last_reset='N' and status IN($Dsql) and list_id IN($camp_lists) and ($all_gmtSQL) $fSQL";
			#$DB=1;
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$rslt_rows = mysql_num_rows($rslt);
			if ($rslt_rows)
				{
				$rowx=mysql_fetch_row($rslt);
				$active_leads = "$rowx[0]";
				}
			else {$active_leads = '0';}

			echo "|$DB|\n";
			echo "<font color=navy> This campaign has $active_leads leads to be dialed in those lists</font>\n";
			}
		else
			{
			echo "<font color=navy> No dial statuses selected for this campaign</font>\n";
			}
		}
	else
		{
		echo "<font color=navy> No active lists selected for this campaign</font>\n";
		}
	}
else
	{
	echo "<font color=navy> No active lists selected for this campaign</font>\n";
	}
##### END calculate what gmt_offset_now values are within the allowed local_call_time setting ###
}
?>
