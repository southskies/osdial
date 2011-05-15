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
# 090410-1415 - Reformatted to use get_variable.
# 090410-1731 - Added allow_tab_switch
# 090420-1846 - Added answers_per_hour_limit
# 090515-0135 - Added preview_force_dial_time
# 090515-0140 - Added manual_preview_default
# 090515-0538 - Added web_form_extwindow and web_form2_extwindow


# The build variable gets set to the SVN revision automatically in release package.
# Do no change.
$build = 'SVN_Build';

$STARTtime = date("U");
$SQLdate = date("Y-m-d H:i:s");
$MT[0]='';
$US='_';

$month_old = mktime(0, 0, 0, date("m")-1, date("d"),  date("Y"));
$past_month_date = date("Y-m-d H:i:s",$month_old);
$week_old = mktime(0, 0, 0, date("m"), date("d")-7,  date("Y"));
$past_week_date = date("Y-m-d H:i:s",$week_old);

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];

$system_settings = get_first_record($link, 'system_settings', '*','');
$t1="OSDial"; if (preg_match("/^Sli/",$system_settings['admin_template'])){ $t1=$system_settings['admin_template']; };
$non_latin = $system_settings['use_non_latin'];
$admin_home_url_LU = $system_settings['admin_home_url'];
$user_company = $system_settings['company_name'];
$admin_version = $system_settings['version'];

$webServerGMT = date('Z')/3600;
$webServerDST = date('I');
$webServerAdjGMT = $webServerGMT;
if ($webServerDST) $webServerAdjGMT = $webServerGMT - 1;
$webClientGMT = $webServerGMT;
$webClientDST = $webServerDST;
$webClientAdjGMT = $webClientGMT;

# The following creates arrays with TZ mappings.
require_once 'Date.php';
$tzrefid = array();
$tzrefidDST = array();
$tzalt = array();
$tzaltDST = array();
$tznames = array();
$tznamesDST = array();
$tznames2 = array();
$tznamesDST2 = array();
$tzoffsets = array();
$tzoffsetsDST = array();
$tzids = Date_TimeZone::getAvailableIDs();
arsort($tzids);
$tzorder = array('^US.*','^SystemV.*','^America.*','^Europe.*','^Asia.*','^Africa.*','^Atlantic.*','^Pacific.*');
foreach ($tzorder as $tzmatch) {
    foreach ($tzids as $tzid) {
        if (preg_match('/'.$tzmatch.'/',$tzid)) {
            $tmptz = new Date_TimeZone($tzid);
            $tzsn = $tmptz->getShortName();
            if (!empty($tzsn)) { 
                $tzln = $tmptz->getLongName();
                $tzdsn = $tmptz->getDSTShortName();
                $tzoff = $tmptz->getRawOffset() / 3600000;
                $tzoffDST = $tmptz->getOffset(new Date) / 3600000;

                if ($tzoff > -27 and $tzoff < 27 ) {
                    $tzsep = '';
                    if (!isset($tzoffsets[$tzsep . $tzoff])) $tzoffsets[$tzsep . $tzoff] = $tzsn;
                    if (!isset($tznames[$tzsn])) $tznames[$tzsn] = $tzoff;
                    if (!isset($tzalt[$tzsn])) {
                        if (empty($tzdsn)) $tzdsn = $tzsn;
                        $tzalt[$tzsn] = $tzdsn;
                    }
                    $tzsep = '';
                    if ($tzoff >= 0) $tzsep = '+';
                    if (!isset($tznames2[$tzsn . $tzsep . $tzoff])) $tznames2[$tzsn . $tzsep . $tzoff] = $tzoff;
                    if (!isset($tzrefid[$tzsn])) $tzrefid[$tzsn] = $tzid;
                }

                if ($tzoffDST > -27 and $tzoffDST < 27 ) {
                    $tzsep = '';
                    if (empty($tzdsn)) $tzdsn = $tzsn;
                    if (!isset($tzoffsetsDST[$tzsep . $tzoffDST])) $tzoffsetsDST[$tzsep . $tzoffDST] = $tzdsn;
                    if (!isset($tznamesDST[$tzdsn])) $tznamesDST[$tzdsn] = $tzoffDST;
                    if (!isset($tzaltDST[$tzdsn])) $tzaltDST[$tzdsn] = $tzsn;
                    $tzsep = '';
                    if ($tzoffDST >= 0) $tzsep = '+';
                    if (!isset($tznamesDST2[$tzdsn . $tzsep . $tzoffDST])) $tznamesDST2[$tzdsn . $tzsep . $tzoffDST] = $tzoffDST;
                    if (!isset($tzrefidDST[$tzdsn])) $tzrefidDST[$tzdsn] = $tzid;
                }
            }
        }
    }
}
ksort($tzrefid);
ksort($tzrefidDST);
ksort($tzalt);
ksort($tzaltDST);
ksort($tznames);
ksort($tznamesDST);
ksort($tznames2);
ksort($tznamesDST2);
ksort($tzoffsets);
ksort($tzoffsetsDST);

######################################################################################################
######################################################################################################
#######   Form variable declaration
######################################################################################################
######################################################################################################

$active = get_variable("active");
$adastats = get_variable("adastats");
$adaptive_dl_diff_target = get_variable("adaptive_dl_diff_target");
$adaptive_dropped_percentage = get_variable("adaptive_dropped_percentage");
$adaptive_intensity = get_variable("adaptive_intensity");
$adaptive_latest_server_time = get_variable("adaptive_latest_server_time");
$adaptive_maximum_level = get_variable("adaptive_maximum_level");
$ADD = get_variable("ADD");
$address1_field = get_variable("address1_field");
$address2_field = get_variable("address2_field");
$address3_field = get_variable("address3_field");
$admin_api_access = get_variable("admin_api_access");
$admin_hangup_enabled = get_variable("admin_hangup_enabled");
$admin_hijack_enabled = get_variable("admin_hijack_enabled");
$admin_home_url = get_variable("admin_home_url");
$admin_monitor_enabled = get_variable("admin_monitor_enabled");
$admin_template = get_variable("admin_template");
$AFLogging_enabled = get_variable("AFLogging_enabled");
$after_hours_action = get_variable("after_hours_action");
$after_hours_exten = get_variable("after_hours_exten");
$after_hours_message_filename = get_variable("after_hours_message_filename");
$after_hours_voicemail = get_variable("after_hours_voicemail");
$agent_alert_delay = get_variable("agent_alert_delay");
$agent_alert_exten = get_variable("agent_alert_exten");
$agent_api_access = get_variable("agent_api_access");
$agent_choose_ingroups = get_variable("agent_choose_ingroups");
$agent_template = get_variable("agent_template");
$agentcall_manual = get_variable("agentcall_manual");
$agentonly_callbacks = get_variable("agentonly_callbacks");
$agent_pause_codes_active = get_variable("agent_pause_codes_active");
$AGI_call_logging_enabled = get_variable("AGI_call_logging_enabled");
$agi_output = get_variable("agi_output");
$allcalls_delay = get_variable("allcalls_delay");
$allow_closers = get_variable("allow_closers");
$allow_sipsak_messages = get_variable("allow_sipsak_messages");
$allow_tab_switch = get_variable("allow_tab_switch");
$allow_multicall = get_variable("allow_multicall");
$alt_phone_field = get_variable("alt_phone_field");
$alter_custdata_override = get_variable("alter_custdata_override");
$alt_number_dialing = get_variable("alt_number_dialing");
$alter_agent_interface_options = get_variable("alter_agent_interface_options");
$am_message_exten = get_variable("am_message_exten");
$amd_send_to_vmx = get_variable("amd_send_to_vmx");
$answer_transfer_agent = get_variable("answer_transfer_agent");
$areacode = get_variable("areacode");
$archive_hostname = get_variable("archive_hostname");
$archive_mix_format = get_variable("archive_mix_format");
$archive_password = get_variable("archive_password");
$archive_report_path = get_variable("archive_report_path");
$archive_path = get_variable("archive_path");
$archive_port = get_variable("archive_port");
$archive_transfer_method = get_variable("archive_transfer_method");
$archive_username = get_variable("archive_username");
$archive_web_path = get_variable("archive_web_path");
$ast_admin_access = get_variable("ast_admin_access");
$ast_delete_phones = get_variable("ast_delete_phones");
$asterisk_version = get_variable("asterisk_version");
$ASTmgrSECRET = get_variable("ASTmgrSECRET");
$ASTmgrUSERNAME = get_variable("ASTmgrUSERNAME");
$ASTmgrUSERNAMElisten = get_variable("ASTmgrUSERNAMElisten");
$ASTmgrUSERNAMEsend = get_variable("ASTmgrUSERNAMEsend");
$ASTmgrUSERNAMEupdate = get_variable("ASTmgrUSERNAMEupdate");
$attempt_delay = get_variable("attempt_delay");
$attempt_maximum = get_variable("attempt_maximum");
$auto_alt_dial = get_variable("auto_alt_dial");
$auto_dial_level = get_variable("auto_dial_level");
$auto_dial_next_number = get_variable("auto_dial_next_number");
$available_only_ratio_tally = get_variable("available_only_ratio_tally");

$add_closer_record = get_variable("add_closer_record");
$address1 = get_variable("address1");
$address2 = get_variable("address2");
$address3 = get_variable("address3");
$alt_phone = get_variable("alt_phone");
$alf_id = get_variable("alf_id");
$alf_fld_id = get_variable("alf_fld_id");
$alf_val = get_variable("alf_val");

$balance_trunks_offlimits = get_variable("balance_trunks_offlimits");
$billable = get_variable("billable");

$CALLSdisplay = get_variable("CALLSdisplay");
$call_out_number_group = get_variable("call_out_number_group");
$call_parking_enabled = get_variable("call_parking_enabled");
$call_time_comments = get_variable("call_time_comments");
$call_time_id = get_variable("call_time_id");
$call_time_name = get_variable("call_time_name");
$answers_per_hour_limit = get_variable("answers_per_hour_limit");
$CallerID_popup_enabled = get_variable("CallerID_popup_enabled");
$campaign_allow_inbound = get_variable("campaign_allow_inbound");
$campaign_cid = get_variable("campaign_cid");
$campaign_cid_name = get_variable("campaign_cid_name");
$campaign_description = get_variable("campaign_description");
$campaign_detail = get_variable("campaign_detail");
$campaign_id = get_variable("campaign_id");
$campaign_name = get_variable("campaign_name");
$campaign_rank = get_variable("campaign_rank");
$campaign_rec_exten = get_variable("campaign_rec_exten");
$campaign_rec_filename = get_variable("campaign_rec_filename");
$campaign_recording = get_variable("campaign_recording");
$campaigns = get_variable("campaigns");
$campaigns_values = get_variable("campaigns_values");
$campaign_call_time = get_variable("campaign_call_time");
$campaign_stats_refresh = get_variable("campaign_stats_refresh");
$campaign_vdad_exten = get_variable("campaign_vdad_exten");
$category = get_variable("category");
$change_agent_campaign = get_variable("change_agent_campaign");
$city_field = get_variable("city_field");
$cid_number = get_variable("cid_number");
$cid_name = get_variable("cid_name");
$client_browser = get_variable("client_browser");
$closer_default_blended = get_variable("closer_default_blended");
$comments_field = get_variable("comments_field");
$company = get_variable("company");
$company_id = get_variable("company_id");
$company_name = get_variable("company_name");
$company_status = get_variable("company_status");
$company_enable_campaign_ivr = get_variable("company_enable_campaign_ivr");
$company_enable_campaign_listmix = get_variable("company_enable_campaign_listmix");
$company_export_leads = get_variable("company_export_leads");
$company_enable_scripts = get_variable("company_enable_scripts");
$company_enable_filters = get_variable("company_enable_filters");
$company_enable_ingroups = get_variable("company_enable_ingroups");
$company_enable_external_agents = get_variable("company_enable_external_agents");
$company_enable_system_calltimes = get_variable("company_enable_system_calltimes");
$company_enable_system_carriers = get_variable("company_enable_system_carriers");
$company_enable_system_phones = get_variable("company_enable_system_phones");
$company_enable_system_conferences = get_variable("company_enable_system_conferences");
$company_enable_system_servers = get_variable("company_enable_system_servers");
$company_enable_system_statuses = get_variable("company_enable_system_statuses");
$company_api_access = get_variable("company_api_access");
$company_dnc_method = get_variable("company_dnc_method");
$computer_ip = get_variable("computer_ip");
$confirm_sale = get_variable("confirm_sale");
$confirm_id = get_variable("confirm_id");
$confirm_status = get_variable("confirm_status");
$concurrent_transfers = get_variable("concurrent_transfers");
$conf_exten = get_variable("conf_exten");
$conf_on_extension = get_variable("conf_on_extension");
$conferencing_enabled = get_variable("conferencing_enabled");
$CoNfIrM = get_variable("CoNfIrM");
$cost = get_variable("cost");
$cost_field = get_variable("cost_field");
$country_code_field = get_variable("country_code_field");
$ct_default_start = get_variable("ct_default_start");
$ct_default_stop = get_variable("ct_default_stop");
$ct_friday_start = get_variable("ct_friday_start");
$ct_friday_stop = get_variable("ct_friday_stop");
$ct_monday_start = get_variable("ct_monday_start");
$ct_monday_stop = get_variable("ct_monday_stop");
$ct_saturday_start = get_variable("ct_saturday_start");
$ct_saturday_stop = get_variable("ct_saturday_stop");
$ct_sunday_start = get_variable("ct_sunday_start");
$ct_sunday_stop = get_variable("ct_sunday_stop");
$ct_thursday_start = get_variable("ct_thursday_start");
$ct_thursday_stop = get_variable("ct_thursday_stop");
$ct_tuesday_start = get_variable("ct_tuesday_start");
$ct_tuesday_stop = get_variable("ct_tuesday_stop");
$ct_wednesday_start = get_variable("ct_wednesday_start");
$ct_wednesday_stop = get_variable("ct_wednesday_stop");
$custom1_field = get_variable("custom1_field");
$custom2_field = get_variable("custom2_field");
$cpuinfo = get_variable("cpuinfo");

$call_began = get_variable("call_began");
$callback_id = get_variable("callback_id");
$carrier_id = get_variable("carrier_id");
$carrier_name = get_variable("carrier_name");
$carrier_description = get_variable("carrier_description");
$carrier_active = get_variable("carrier_active");
$carrier_selectable = get_variable("carrier_selectable");
$carrier_protocol = get_variable("carrier_protocol");
$carrier_protocol_config = get_variable("carrier_protocol_config");
$carrier_registrations = get_variable("carrier_registrations");
$carrier_dialplan = get_variable("carrier_dialplan");
$carrier_failover_id = get_variable("carrier_failover_id");
$carrier_failover_condition = get_variable("carrier_failover_condition");
$carrier_server_ip = get_variable("carrier_server_ip");
$carrier_strip_msd = get_variable("carrier_strip_msd");
$carrier_allow_international = get_variable("carrier_allow_international");
$carrier_default_callerid = get_variable("carrier_default_callerid");
$carrier_default_areacode = get_variable("carrier_default_areacode");
$carrier_default_prefix = get_variable("carrier_default_prefix");
$channel = get_variable("channel");
$city = get_variable("city");
$comments = get_variable("comments");
$country_code = get_variable("country_code");
$custom1 = get_variable("custom1");
$custom2 = get_variable("custom2");
$CBchangeANYtoUSER = get_variable("CBchangeANYtoUSER");
$CBchangeUSERtoANY = get_variable("CBchangeUSERtoANY");
$CBchangeUSERtoUSER = get_variable("CBchangeUSERtoUSER");
$CBuser = get_variable("CBuser");

$did_id = get_variable("did_id");
$did = get_variable("did");
$did_action = get_variable("did_action");
$did_phone = get_variable("did_phone");
$did_extension = get_variable("did_extension");
$did_extension_context = get_variable("did_extension_context");
$did_voicemail = get_variable("did_voicemail");
$did_ingroup = get_variable("did_ingroup");
$did_server_allocation = get_variable("did_server_allocation");
$did_park_file = get_variable("did_park_file");
$did_lookup_method = get_variable("did_lookup_method");
$did_initial_status = get_variable("did_initial_status");
$did_default_list_id = get_variable("did_default_list_id");
$did_default_phone_code = get_variable("did_default_phone_code");
$did_search_campaign = get_variable("did_search_campaign");
$date_of_birth_field = get_variable("date_of_birth_field");
$date_of_birth = get_variable("date_of_birth");
$dispo = get_variable("dispo");
$DB = get_variable("DB");
$DBX_database = get_variable("DBX_database");
$DBX_pass = get_variable("DBX_pass");
$DBX_port = get_variable("DBX_port");
$DBX_server = get_variable("DBX_server");
$DBX_user = get_variable("DBX_user");
$DBY_database = get_variable("DBY_database");
$DBY_pass = get_variable("DBY_pass");
$DBY_port = get_variable("DBY_port");
$DBY_server = get_variable("DBY_server");
$DBY_user = get_variable("DBY_user");
$dedicated_trunks = get_variable("dedicated_trunks");
$default_xfer_group = get_variable("default_xfer_group");
$delete_call_times = get_variable("delete_call_times");
$delete_campaigns = get_variable("delete_campaigns");
$delete_dnc = get_variable("delete_dnc");
$delete_filters = get_variable("delete_filters");
$delete_ingroups = get_variable("delete_ingroups");
$delete_lists = get_variable("delete_lists");
$delete_remote_agents = get_variable("delete_remote_agents");
$delete_scripts = get_variable("delete_scripts");
$delete_user_groups = get_variable("delete_user_groups");
$delete_users = get_variable("delete_users");
$dial_level_override = get_variable("dial_level_override");
$dial_method = get_variable("dial_method");
$dial_prefix = get_variable("dial_prefix");
$dial_status = get_variable("dial_status");
$dial_status_a = get_variable("dial_status_a");
$dial_status_b = get_variable("dial_status_b");
$dial_status_c = get_variable("dial_status_c");
$dial_status_d = get_variable("dial_status_d");
$dial_status_e = get_variable("dial_status_e");
$dial_timeout = get_variable("dial_timeout");
$dialplan_number = get_variable("dialplan_number");
$disable_alter_custdata = get_variable("disable_alter_custdata");
$disable_manual_dial = get_variable("disable_manual_dial");
$dnc_search_phone = get_variable("dnc_search_phone");
$dnc_add_phone = get_variable("dnc_add_phone");
$dnc_delete_phone = get_variable("dnc_delete_phone");
$drop_call_seconds = get_variable("drop_call_seconds");
$drop_exten = get_variable("drop_exten");
$drop_message = get_variable("drop_message");
$drop_trigger = get_variable("drop_trigger");
$dtmf_send_extension = get_variable("dtmf_send_extension");
$dupcheck = get_variable("dupcheck");


$et_id = get_variable("et_id");
$et_name = get_variable("et_name");
$et_send_action = get_variable("et_send_action");
$et_comments = get_variable("et_comments");
$et_host = get_variable("et_host");
$et_port = get_variable("et_port");
$et_user = get_variable("et_user");
$et_pass = get_variable("et_pass");
$et_from = get_variable("et_from");
$et_subject = get_variable("et_subject");
$et_body_html = get_variable("et_body_html");
$et_body_text = get_variable("et_body_text");
$email_templates = get_variable("email_templates");
$email = get_variable("email");
$end_call = get_variable("end_call");
$email_field = get_variable("email_field");
$emails_values = get_variable("emails_values");
$enable_agc_xfer_log = get_variable("enable_agc_xfer_log");
$enable_fast_refresh = get_variable("enable_fast_refresh");
$enable_persistant_mysql = get_variable("enable_persistant_mysql");
$enable_sipsak_messages = get_variable("enable_sipsak_messages");
$enable_queuemetrics_logging = get_variable("enable_queuemetrics_logging");
$enable_lead_allocation = get_variable("enable_lead_allocation");
$enable_filters = get_variable("enable_filters");
$enable_external_agents = get_variable("enable_external_agents");
$enable_multicompany = get_variable("enable_multicompany");
$export_dnc = get_variable("export_dnc");
$export_leads = get_variable("export_leads");
$export_agent_pause_summary = get_variable("export_agent_pause_summary");
$export_agent_performance_detail = get_variable("export_agent_performance_detail");
$export_agent_timesheet = get_variable("export_agent_timesheet");
$export_campaign_call_report = get_variable("export_campaign_call_report");
$export_campaign_recent_outbound_sales = get_variable("export_campaign_recent_outbound_sales");
$export_ingroup_call_report = get_variable("export_ingroup_call_report");
$export_lead_performance_campaign = get_variable("export_lead_performance_campaign");
$export_lead_performance_list = get_variable("export_lead_performance_list");
$export_lead_search_advanced = get_variable("export_lead_search_advanced");
$export_list_cost_entry = get_variable("export_list_cost_entry");
$ext_context = get_variable("ext_context");
$extension = get_variable("extension");
$external_key = get_variable("external_key");
$external_key_field = get_variable("external_key_field");
$external_dnc_active = get_variable("external_dnc_active");
$external_dnc_address = get_variable("external_dnc_address");
$external_dnc_database = get_variable("external_dnc_database");
$external_dnc_username = get_variable("external_dnc_username");
$external_dnc_password = get_variable("external_dnc_password");
$external_dnc_sql = get_variable("external_dnc_sql");

$fast_refresh_rate = get_variable("fast_refresh_rate");
$fields = get_variable('fields');
$field_description = get_variable('field_description');
$field_id = get_variable('field_id');
$field_length = get_variable('field_length');
$field_name = get_variable('field_name');
$field_options = get_variable('field_options');
$field_priority = get_variable('field_priority');
$file_layout = get_variable("file_layout");
$first_name = get_variable('first_name');
$first_name_field = get_variable("first_name_field");
$force_logout = get_variable("force_logout");
$form_description = get_variable('form_description');
$form_description2 = get_variable('form_description2');
$form_id = get_variable('form_id');
$form_name = get_variable('form_name');
$form_priority = get_variable('form_priority');
$fronter_display = get_variable("fronter_display");
$full_name = get_variable("full_name");
$fullname = get_variable("fullname");

$gender = get_variable("gender");
$gender_field = get_variable("gender_field");
$get_call_launch = get_variable("get_call_launch");
$group = get_variable("group");
$group_color = get_variable("group_color");
$group_id = get_variable("group_id");
$group_name = get_variable("group_name");
$group_rank = get_variable("group_rank");
$groups = get_variable("groups");

$hide_xfer_local_closer = get_variable("hide_xfer_local_closer");
$hide_xfer_dial_override = get_variable("hide_xfer_dial_override");
$hide_xfer_hangup_xfer = get_variable("hide_xfer_hangup_xfer");
$hide_xfer_leave_3way = get_variable("hide_xfer_leave_3way");
$hide_xfer_dial_with = get_variable("hide_xfer_dial_with");
$hide_xfer_hangup_both = get_variable("hide_xfer_hangup_both");
$hide_xfer_blind_xfer = get_variable("hide_xfer_blind_xfer");
$hide_xfer_park_dial = get_variable("hide_xfer_park_dial");
$hide_xfer_blind_vmail = get_variable("hide_xfer_blind_vmail");
$HKstatus = get_variable("HKstatus");
$hopper_level = get_variable("hopper_level");
$hotkey = get_variable("hotkey");
$hotkeys_active = get_variable("hotkeys_active");
$human_answered = get_variable("human_answered");

$id = get_variable('id');
$iframe = get_variable('iframe');
$ingroups_values = get_variable('ingroups_values');
$IAXmonitorLINK = get_variable("IAXmonitorLINK");
$install_directory = get_variable("install_directory");
$intra_server_protocol = get_variable("intra_server_protocol");

$last_name = get_variable("last_name");
$last_name_field = get_variable("last_name_field");
$lead_file = get_variable("lead_file");
$lead_filter_comments = get_variable("lead_filter_comments");
$lead_filter_id = get_variable("lead_filter_id");
$lead_filter_name = get_variable("lead_filter_name");
$lead_filter_sql = get_variable("lead_filter_sql");
$leadfile_name = get_variable("leadfile_name");
$lead_id = get_variable("lead_id");
$lead_order = get_variable("lead_order");
$list_description = get_variable("list_description");
$list_id = get_variable("list_id");
$list_id_field = get_variable("list_id_field");
$list_id_override = get_variable("list_id_override");
$list_mix_container = get_variable("list_mix_container");
$list_name = get_variable("list_name");
$list_order_mix = get_variable("list_order_mix");
$load_dnc = get_variable("load_dnc");
$load_leads = get_variable("load_leads");
$local_call_time = get_variable("local_call_time");
$local_gmt = get_variable("local_gmt");
$local_web_callerID_URL = get_variable("local_web_callerID_URL");
$login = get_variable("login");
$login_campaign = get_variable("login_campaign");
$login_pass = get_variable("login_pass");
$login_user = get_variable("login_user");

$manual_dial_allow_skip = get_variable("manual_dial_allow_skip");
$manual_dial_list_id = get_variable("manual_dial_list_id");
$manual_preview_default = get_variable("manual_preview_default");
$max_osdial_trunks = get_variable("max_osdial_trunks");
$media_id = get_variable("media_id");
$media_filename = get_variable("media_filename");
$media_mimetype = get_variable("media_mimetype");
$media_description = get_variable("media_description");
$media_extension = get_variable("media_extension");
$middle_initial = get_variable("middle_initial");
$middle_initial_field = get_variable("middle_initial_field");
$mix_container_item = get_variable("mix_container_item");
$mix_method = get_variable("mix_method");
$modify_call_times = get_variable("modify_call_times");
$modify_campaigns = get_variable("modify_campaigns");
$modify_filters = get_variable("modify_filters");
$modify_ingroups = get_variable("modify_ingroups");
$modify_leads = get_variable("modify_leads");
$modify_lists = get_variable("modify_lists");
$monitor_prefix = get_variable("monitor_prefix");
$modify_remoteagents = get_variable("modify_remoteagents");
$modify_scripts = get_variable("modify_scripts");
$modify_servers = get_variable("modify_servers");
$modify_usergroups = get_variable("modify_usergroups");
$modify_users = get_variable("modify_users");
$moh_context = get_variable("moh_context");
$modify_logs = get_variable("modify_logs");
$modify_closer_logs = get_variable("modify_closer_logs");
$modify_agent_logs = get_variable("modify_agent_logs");
$multicompany_admin = get_variable("multicompany_admin");

$next_agent_call = get_variable("next_agent_call");
$no_hopper_leads_logins = get_variable("no_hopper_leads_logins");
$number_of_lines = get_variable("number_of_lines");

$OK_to_process = get_variable("OK_to_process");
$old_campaign_id = get_variable("old_campaign_id");
$old_conf_exten = get_variable("old_conf_exten");
$old_extension = get_variable("old_extension");
$old_server_id = get_variable("old_server_id");
$old_server_ip = get_variable("old_server_ip");
$OLDuser_group = get_variable("OLDuser_group");
$omit_phone_code = get_variable("omit_phone_code");
$onhold_prompt_filename = get_variable("onhold_prompt_filename");
$orderby = get_variable("orderby");
$orddir = get_variable("orddir");
$osdial_agent_disable = get_variable("osdial_agent_disable");
$osdial_balance_active = get_variable("osdial_balance_active");
$OSDIAL_park_on_extension = get_variable("OSDIAL_park_on_extension");
$OSDIAL_park_on_filename = get_variable("OSDIAL_park_on_filename");
$osdial_recording = get_variable("osdial_recording");
$osdial_recording_override = get_variable("osdial_recording_override");
$osdial_transfers = get_variable("osdial_transfers");
$OSDIAL_web_URL = get_variable("OSDIAL_web_URL");
$outbound_cid = get_variable("outbound_cid");
$outbound_cid_name = get_variable("outbound_cid_name");

$oi1 = get_variable("oi1");
$oi2 = get_variable("oi2");
$oi3 = get_variable("oi3");
$oi4 = get_variable("oi4");
$oi5 = get_variable("oi5");
$oi6 = get_variable("oi6");
$oi7 = get_variable("oi7");
$oi8 = get_variable("oi8");
$oi9 = get_variable("oi9");
$oi10 = get_variable("oi10");
$oi11 = get_variable("oi11");
$oi12 = get_variable("oi12");
$oi13 = get_variable("oi13");
$oi14 = get_variable("oi14");
$oi15 = get_variable("oi15");
$oi16 = get_variable("oi16");
$oi17 = get_variable("oi17");
$oi18 = get_variable("oi18");
$oi19 = get_variable("oi19");
$oi20 = get_variable("oi20");
$oivr_id = get_variable("oivr_id");
$oivr_campaign_id = get_variable("oivr_campaign_id");
$oivr_name = get_variable("oivr_name");
$oivr_allow_agent_extensions = get_variable("oivr_allow_agent_extensions");
$oivr_allow_inbound = get_variable("oivr_allow_inbound");
$oivr_announcement = get_variable("oivr_announcement");
$oivr_repeat_loops = get_variable("oivr_repeat_loops");
$oivr_wait_loops = get_variable("oivr_wait_loops");
$oivr_wait_timeout = get_variable("oivr_wait_timeout");
$oivr_answered_status = get_variable("oivr_answered_status");
$oivr_virtual_agents = get_variable("oivr_virtual_agents");
$oivr_reserve_agents = get_variable("oivr_reserve_agents");
$oivr_timeout_action = get_variable("oivr_timeout_action");
$oivr_status = get_variable("oivr_status");
$oivr_opt_id = get_variable("oivr_opt_id");
$oivr_opt_parent_id = get_variable("oivr_opt_parent_id");
$oivr_opt_keypress = get_variable("oivr_opt_keypress");
$oivr_opt_action = get_variable("oivr_opt_action");
$oivr_opt_action_data = get_variable("oivr_opt_action_data");
$oivr_opt_last_state = get_variable("oivr_opt_last_state");
$old_phone = get_variable("old_phone");

$park_ext = get_variable("park_ext");
$park_file_name = get_variable("park_file_name");
$park_on_extension = get_variable("park_on_extension");
$pass = get_variable("pass");
$pause_code = get_variable("pause_code");
$pause_code_name = get_variable("pause_code_name");
$phone = get_variable("phone");
$phone_code_field = get_variable("phone_code_field");
$phone_code_override = get_variable("phone_code_override");
$phone_ip = get_variable("phone_ip");
$phone_login = get_variable("phone_login");
$phone_number = get_variable("phone_number");
$phone_number_field = get_variable("phone_number_field");
$phone_pass = get_variable("phone_pass");
$phone_type = get_variable("phone_type");
$picture = get_variable("picture");
$postal_code_field = get_variable("postal_code_field");
$postalgmt = get_variable("postalgmt");
$preview_force_dial_time = get_variable("preview_force_dial_time");
$prompt_interval = get_variable("prompt_interval");
$protocol = get_variable("protocol");
$province_field = get_variable("province_field");

$parked_time = get_variable("parked_time");
$phone_code = get_variable("phone_code");
$post_date = get_variable("post_date");
$postal_code = get_variable("postal_code");
$province = get_variable("province");

$qc_server_archive = get_variable("qc_server_archive");
$qc_server_active = get_variable("qc_server_active");
$qc_server_batch_time = get_variable("qc_server_batch_time");
$qc_server_description = get_variable("qc_server_description");
$qc_server_home_path = get_variable("qc_server_home_path");
$qc_server_host = get_variable("qc_server_host");
$qc_server_id = get_variable("qc_server_id");
$qc_server_location_template = get_variable("qc_server_location_template");
$qc_server_name = get_variable("qc_server_name");
$qc_server_password = get_variable("qc_server_password");
$qc_server_transfer_type = get_variable("qc_server_transfer_type");
$qc_server_rule_id = get_variable("qc_server_rule_id");
$qc_server_rule_query = get_variable("qc_server_rule_query");
$qc_server_transfer_method = get_variable("qc_server_transfer_method");
$qc_server_username = get_variable("qc_server_username");
$QUEUE_ACTION_enabled = get_variable("QUEUE_ACTION_enabled");
$queuemetrics_eq_prepend = get_variable("queuemetrics_eq_prepend");
$queuemetrics_dbname = get_variable("queuemetrics_dbname");
$queuemetrics_log_id = get_variable("queuemetrics_log_id");
$queuemetrics_login = get_variable("queuemetrics_login");
$queuemetrics_pass = get_variable("queuemetrics_pass");
$queuemetrics_server_ip = get_variable("queuemetrics_server_ip");
$queuemetrics_url = get_variable("queuemetrics_url");

$recording_exten = get_variable("recording_exten");
$remote_agent_id = get_variable("remote_agent_id");
$reset_hopper = get_variable("reset_hopper");
$reset_list = get_variable("reset_list");
$RR = get_variable("RR");

$safe_harbor_exten = get_variable("safe_harbor_exten");
$safe_harbor_message = get_variable("safe_harbor_message");
$scheduled_callbacks = get_variable("scheduled_callbacks");
$scripts_values = get_variable("scripts_values");
$script_comments = get_variable("script_comments");
$script_id = get_variable("script_id");
$script_name = get_variable("script_name");
$script_override = get_variable("script_override");
$script_text = get_variable("script_text");
$script_button_id = get_variable("script_button_id");
$script_button_description = get_variable("script_button_description");
$script_button_label = get_variable("script_button_label");
$script_button_text = get_variable("script_button_text");
$scrub_dnc = get_variable("scrub_dnc");
$selectable = get_variable("selectable");
$SERVdisplay = get_variable("SERVdisplay");
$server_description = get_variable("server_description");
$server_id = get_variable("server_id");
$server_ip = get_variable("server_ip");
$server_profile = get_variable("server_profile");
$stage = get_variable("stage");
$SIPmonitorLINK = get_variable("SIPmonitorLINK");
$source_campaign_id = get_variable("source_campaign_id");
$source_group_id = get_variable("source_group_id");
$source_id_field = get_variable("source_id_field");
$source_user_id = get_variable("source_user_id");
$state_call_time_state = get_variable("state_call_time_state");
$state_field = get_variable("state_field");
$state_rule = get_variable("state_rule");
$status = get_variable("status");
$status_name = get_variable("status_name");
$statuses = get_variable('statuses');
$SUB = get_variable("SUB");
$submit = get_variable("submit");
$SUBMIT = get_variable("SUBMIT");
$submit_file = get_variable("submit_file");
$submit_method = get_variable("submit_method");
$sys_perf_log = get_variable("sys_perf_log");

$save_aff = get_variable("save_aff");
$source_id = get_variable("source_id");
$state = get_variable("state");

$telnet_host = get_variable("telnet_host");
$telnet_port = get_variable("telnet_port");
$title = get_variable("title");
$title_field = get_variable("title_field");
$tovdad_display = get_variable("tovdad_display");
$trunk_restriction = get_variable("trunk_restriction");
$tsr = get_variable("tsr");

$UGdisplay = get_variable("UGdisplay");
$UidORname = get_variable("UidORname");
$updater_check_enabled = get_variable("updater_check_enabled");
$use_internal_dnc = get_variable("use_internal_dnc");
$use_non_latin = get_variable("use_non_latin");
$user = get_variable("user");
$user_group = get_variable("user_group");
$user_level = get_variable("user_level");
$user_start = get_variable("user_start");
$user_switching_enabled = get_variable("user_switching_enabled");
$use_custom2_callerid = get_variable("use_custom2_callerid");
$use_cid_areacode_map = get_variable("use_cid_areacode_map");
$use_recycle_gap = get_variable("use_recycle_gap");

$VAdisplay = get_variable("VAdisplay");
$vcl_id = get_variable("vcl_id");
$vcl_name = get_variable("vcl_name");
$vd_server_logs = get_variable("vd_server_logs");
$VDstop_rec_after_each_call = get_variable("VDstop_rec_after_each_call");
$vendor_id = get_variable("vendor_id");
$vendor_lead_code_field = get_variable("vendor_lead_code_field");
$view_reports = get_variable("view_reports");
$view_agent_pause_summary = get_variable("view_agent_pause_summary");
$view_agent_performance_detail = get_variable("view_agent_performance_detail");
$view_agent_realtime = get_variable("view_agent_realtime");
$view_agent_realtime_iax_barge = get_variable("view_agent_realtime_iax_barge");
$view_agent_realtime_iax_listen = get_variable("view_agent_realtime_iax_listen");
$view_agent_realtime_sip_barge = get_variable("view_agent_realtime_sip_barge");
$view_agent_realtime_sip_listen = get_variable("view_agent_realtime_sip_listen");
$view_agent_realtime_summary = get_variable("view_agent_realtime_summary");
$view_agent_stats = get_variable("view_agent_stats");
$view_agent_status = get_variable("view_agent_status");
$view_agent_timesheet = get_variable("view_agent_timesheet");
$view_campaign_call_report = get_variable("view_campaign_call_report");
$view_campaign_recent_outbound_sales = get_variable("view_campaign_recent_outbound_sales");
$view_ingroup_call_report = get_variable("view_ingroup_call_report");
$view_lead_performance_campaign = get_variable("view_lead_performance_campaign");
$view_lead_performance_list = get_variable("view_lead_performance_list");
$view_lead_search = get_variable("view_lead_search");
$view_lead_search_advanced = get_variable("view_lead_search_advanced");
$view_list_cost_entry = get_variable("view_list_cost_entry");
$view_server_performance = get_variable("view_server_performance");
$view_server_times = get_variable("view_server_times");
$view_usergroup_hourly_stats = get_variable("view_usergroup_hourly_stats");
$voicemail_button_enabled = get_variable("voicemail_button_enabled");
$voicemail_dump_exten = get_variable("voicemail_dump_exten");
$voicemail_ext = get_variable("voicemail_ext");
$voicemail_exten = get_variable("voicemail_exten");
$voicemail_id = get_variable("voicemail_id");
$voicemail_password = get_variable("voicemail_password");
$voicemail_email = get_variable("voicemail_email");
$vsc_id = get_variable("vsc_id");
$vsc_description = get_variable("vsc_description");
$vsc_name = get_variable("vsc_name");

$web_form_address = get_variable("web_form_address");
$web_form_address2 = get_variable("web_form_address2");
$web_form_extwindow = get_variable("web_form_extwindow");
$web_form2_extwindow = get_variable("web_form2_extwindow");
$webroot_writable = get_variable("webroot_writable");
$welcome_message_filename = get_variable("welcome_message_filename");
$wrapup_message = get_variable("wrapup_message");
$wrapup_seconds = get_variable("wrapup_seconds");

$xferconf_a_dtmf = get_variable("xferconf_a_dtmf");
$xferconf_a_number = get_variable("xferconf_a_number");
$xferconf_b_dtmf = get_variable("xferconf_b_dtmf");
$xferconf_b_number = get_variable("xferconf_b_number");
$xfer_cid_mode = get_variable("xfer_cid_mode");
$xfer_exten = get_variable("xfer_exten");
$XFERgroups = get_variable("XFERgroups");
$xfer_agent2agent = get_variable("xfer_agent2agent");
$xfer_agent2agent_allow_multicall = get_variable("xfer_agent2agent_allow_multicall");
$xfer_agent2agent_wait = get_variable("xfer_agent2agent_wait");
$xfer_agent2agent_wait_seconds = get_variable("xfer_agent2agent_wait_seconds");
$xfer_agent2agent_wait_action = get_variable("xfer_agent2agent_wait_action");
$xfer_agent2agent_wait_extension = get_variable("xfer_agent2agent_wait_extension");



if (isset($script_id)) {
    $script_id= strtoupper($script_id);
}
if (isset($script_button_id)) {
    $script_button_id= strtoupper($script_button_id);
}
if (isset($lead_filter_id)) {
    $lead_filter_id = strtoupper($lead_filter_id);
}

if (strlen($dial_status) > 0) {
	$ADD='28';
	$status = $dial_status;
}

?>
