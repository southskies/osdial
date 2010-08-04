<?php ?>
/*
 * #
 * # Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
 * # Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
 * # Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
 * #
 * #     This file is part of OSDial.
 * #
 * #     OSDial is free software: you can redistribute it and/or modify
 * #     it under the terms of the GNU Affero General Public License as
 * #     published by the Free Software Foundation, either version 3 of
 * #     the License, or (at your option) any later version.
 * #
 * #     OSDial is distributed in the hope that it will be useful,
 * #     but WITHOUT ANY WARRANTY; without even the implied warranty of
 * #     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * #     GNU Affero General Public License for more details.
 * #
 * #     You should have received a copy of the GNU Affero General Public
 * #     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
 * #
 * #
 */

var AFforms=new Array(<?=$AFforms_js?>);
var AFids=new Array(<?=$AFids_js?>);
var AFlengths=new Array(<?=$AFlengths_js?>);
var AFnames=new Array(<?=$AFnames_js?>);
var AFoptions=new Array(<?=$AFoptions_js?>);
var agcDIR='<?=$agcDIR?>';
var agcPAGE='<?=$agcPAGE?>';
var agentcall_manual='<?=$agentcall_manual?>';
var agentcallsstatus='<?=$agentcallsstatus?>'
var agent_log_id='<?=$agent_log_id?>';
var agentonly_callbacks='<?=$agentonly_callbacks?>';
var agent_pause_codes_active='<?=$agent_pause_codes_active?>';
var agent_template='<?=$agent_template?>';
var allcalls_delay='<?=$allcalls_delay?>';
var allow_sipsak_messages='<?=$allow_sipsak_messages?>';
var allow_tab_switch='<?=$allow_tab_switch?>';
var alt_phone_dialing='<?=$alt_phone_dialing?>';
var asterisk_version='<?=$asterisk_version?>';
var auto_dial_level='<?=$auto_dial_level?>';
var cal_bg1='<?=$cal_bg1?>';
var cal_bg2='<?=$cal_bg2?>';
var cal_bg3='<?=$cal_bg3?>';
var cal_bg4='<?=$cal_bg4?>';
var cal_bg5='<?=$cal_bg5?>';
var cal_border1='<?=$cal_border1?>';
var cal_border2='<?=$cal_border2?>';
var cal_border3='<?=$cal_border3?>';
var cal_fc='<?=$cal_fc?>';
var callback_bg2='<?=$callback_bg2?>';
var callholdstatus='<?=$callholdstatus?>'
var CalL_XC_a_Dtmf='<?=$xferconf_a_dtmf?>';
var CalL_XC_a_NuMber='<?=$xferconf_a_number?>';
var CalL_XC_b_Dtmf='<?=$xferconf_b_dtmf?>';
var CalL_XC_b_NuMber='<?=$xferconf_b_number?>';
var campagentstatctmax='<?=$campagentstatctmax?>'
var campaign_allow_inbound=<?=$campaign_allow_inbound?>;
var campaign_am_message_exten='<?=$campaign_am_message_exten?>';
var campaign_cid='<?=$campaign_cid?>';
var campaign_cid_name='<?=$campaign_cid_name?>';
var campaign_leads_to_call='<?=$campaign_leads_to_call?>';
var campaign_rec_filename='<?=$campaign_rec_filename?>';
var campaign_recording='<?=$campaign_recording?>';
var campaign_script='<?=$campaign_script?>';
var campaign_vdad_exten='<?=$campaign_vdad_exten?>';
var campaign='<?=$VD_campaign?>';
var cid='<?=$campaign_cid?>';
var cid_name='<?=$campaign_cid_name?>';
var closer_bg='<?=$closer_bg?>';
var closer_fc2='<?=$closer_fc2?>';
var closer_fc='<?=$closer_fc?>';
var conf_silent_prefix='<?=$conf_silent_prefix?>';
var CusTCB_bgcolor='<?=$status_callback_bg?>';
var default_xfer_group='<?=$default_xfer_group?>';
var default_xfer_group_name='<?=$default_xfer_group_name?>';
var dial_method='<?=$dial_method?>';
var dialplan_number='<?=$dialplan_number?>';
var dial_context='<?=$dial_context?>';
var dial_prefix='<?=$dial_prefix?>';
var dial_timeout='<?=$dial_timeout?>';
var dispo_bg2='<?=$dispo_bg2?>';
var dispo_bg='<?=$dispo_bg?>';
var dispo_check_all_pause='<?=$dispo_check_all_pause?>';
var dispo_fc='<?=$dispo_fc?>';
var dtmf_send_extension='<?=$dtmf_send_extension?>';
var enable_fast_refresh=<?=$enable_fast_refresh?>;
var enable_sipsak_messages='<?=$enable_sipsak_messages?>';
var epoch_sec=<?=$StarTtimE?>;
var evenrows='<?=$evenrows?>';
var ext_context='<?=$ext_context?>';
var extension='<?=$extension?>';
var extension_xfer='<?=$extension?>';
var fast_refresh_rate='<?=$fast_refresh_rate?>';
var filedate='<?=$FILE_TIME?>';
var flag_channels='<?=$flag_channels?>';
var flag_string='<?=$flag_string?>';
var get_call_launch='<?=$get_call_launch?>';
var group='<?=$VD_campaign?>';
var HidEMonitoRSessionS='<?=$HidEMonitoRSessionS?>';
var HKhotkeys=new Array(<?=$HKhotkeys?>);
var HK_statuses_camp='<?=$HK_statuses_camp?>';
var HKstatuses=new Array(<?=$HKstatuses?>);
var HKstatusnames=new Array(<?=$HKstatusnames?>);
var HKuser_level='<?=$HKuser_level?>';
var HKxferextens=new Array(<?=$HKxferextens?>);
var HTheight=<?=$HTheight?>;
var inbound_man='<?=$inbound_man?>';
var INgroupCOUNT='<?=$INgrpCT?>';
var lead_cid='<?=$campaign_cid?>';
var lead_cust2_cid='<?=$campaign_cid?>';
var LIVE_default_xfer_group='<?=$default_xfer_group?>';
var local_consult_xfers='<?=$local_consult_xfers?>';
var local_gmt ='<?=$local_gmt?>';
var LOGfullname='<?=$LOGfullname?>';
var LogouTKicKAlL='<?=$LogouTKicKAlL?>';
var LOGxfer_agent2agent='<?=$LOGxfer_agent2agent?>';
var manual_dial_allow_skip='<?=$VU_manual_dial_allow_skip?>';
var manual_dial_preview='<?=$manual_dial_preview?>';
var mdnLisT_id='<?=$manual_dial_list_id?>';
var multicomp='<?=$multicomp?>';
var multi_line_comments='<?=$multi_line_comments?>';
var no_delete_sessions='<?=$no_delete_sessions?>';
var NOW_TIME='<?=$NOW_TIME?>';
var oddrows='<?=$oddrows?>';
var omit_phone_code='<?=$omit_phone_code?>';
var osdial_agent_disable='<?=$osdial_agent_disable?>';
var OSDiaL_allow_closers='<?=$OSDiaL_allow_closers?>';
var OSDiaL_web_form_address2_enc='<?=$OSDiaL_web_form_address2_enc?>';
var OSDiaL_web_form_address2='<?=$OSDiaL_web_form_address2?>';
var OSDiaL_web_form_address_enc='<?=$OSDiaL_web_form_address_enc?>';
var OSDiaL_web_form_address='<?=$OSDiaL_web_form_address?>';
var panel_bgcolor='<?=$panel_bg?>';
var panel_bg='<?=$panel_bg?>';
var park_on_extension='<?=$OSDiaL_park_on_extension?>';
var pass='<?=$VD_pass?>';
var pause_bg2='<?=$pause_bg2?>';
var pause_bg='<?=$pause_bg?>';
var pause_fc='<?=$pause_fc?>';
var phone_cid_name='<?=$phone_cid_name?>';
var phone_cid='<?=$phone_cid?>';
var phone_ip='<?=$phone_ip?>';
var phone_login='<?=$phone_login?>';
var phone_pass='<?=$phone_pass?>';
var previewFD_time=<?=$previewFD_time?>;
var protocol='<?=$protocol?>';
var recording_exten='<?=$campaign_rec_exten?>';
var reselect_preview_dial=<?=$manual_preview_default?>;
var scheduled_callbacks='<?=$scheduled_callbacks?>';
var script_bg='<?=$script_bg?>';
var SDwidth=<?=$SDwidth?>;
var server_ip_dialstring='<?=$server_ip_dialstring?>';
var server_ip='<?=$server_ip?>';
var session_id='<?=$session_id?>';
var session_name='<?=$session_name?>';
var SQLdate='<?=$NOW_TIME?>';
var starting_alt_phone_dialing='<?=$alt_phone_dialing?>';
var starting_dial_level='<?=$auto_dial_level?>';
var StarTtimE='<?=$StarTtimE?>';
var status_alert_color='<?=$status_alert_color?>';
var status_bg='<?=$status_bg?>';
var status_intense_color='<?=$status_intense_color?>';
var status_preview_color='<?=$status_preview_color?>';
var submit_method='<?=$submit_method?>';
var submit_method_tmp='<?=$submit_method?>';
var system_alert_bg2='<?=$system_alert_bg2?>';
var t1='<?=$t1?>';
var UnixTime='<?=$StarTtimE?>';
var use_cid_areacode_map='<?=$use_cid_areacode_map?>';
var use_custom2_callerid='<?=$use_custom2_callerid?>';
var use_internal_dnc='<?=$use_internal_dnc?>';
var user_abb='<?=$user_abb?>';
var user_level='<?=$user_level?>';
var user='<?=$VD_login?>';
var VARcid_areacode_names=new Array(<?=$VARcid_areacode_names?>);
var VARcid_areacode_numbers=new Array(<?=$VARcid_areacode_numbers?>);
var VARcid_areacodes=new Array(<?=$VARcid_areacodes?>);
var VARingroups=new Array(<?=$VARingroups?>);
var VARpause_code_names=new Array(<?=$VARpause_code_names?>);
var VARpause_codes=new Array(<?=$VARpause_codes?>);
var VARstatuses=new Array(<?=$VARstatuses?>);
var VARstatusnames=new Array(<?=$VARstatusnames?>);
var VARxfergroupsnames=new Array(<?=$VARxfergroupsnames?>);
var VARxfergroups=new Array(<?=$VARxfergroups?>);
var VDIC_web_form_address2='<?=$OSDiaL_web_form_address2?>';
var VDIC_web_form_address='<?=$OSDiaL_web_form_address?>';
var VD_pause_codes_ct='<?=$VD_pause_codes_ct?>';
var VD_statuses_ct='<?=$VD_statuses_ct?>';
var VDstop_rec_after_each_call='<?=$VDstop_rec_after_each_call?>';
var view_scripts='<?=$view_scripts?>';
var volumecontrol_active='<?=$volumecontrol_active?>';
var VU_agent_choose_ingroups='<?=$VU_agent_choose_ingroups?>';
var VU_closer_default_blended='<?=$VU_closer_default_blended?>';
var VU_hotkeys_active='<?=$VU_hotkeys_active?>';
var VU_osdial_transfers='<?=$VU_osdial_transfers?>';
var web_form2_extwindow=<?=$web_form2_extwindow?>;
var web_form_extwindow=<?=$web_form_extwindow?>;
var webform_session='<?=$webform_sessionname?>';
var wf2_enc_address='<?=$OSDiaL_web_form_address2?>';
var wf_enc_address='<?=$OSDiaL_web_form_address?>';
var wrapup_message='<?=$wrapup_message?>';
var wrapup_seconds='<?=$wrapup_seconds?>';
var xfer_cid_mode='<?=$xfer_cid_mode?>';
var XFgroupCOUNT='<?=$XFgrpCT?>';

