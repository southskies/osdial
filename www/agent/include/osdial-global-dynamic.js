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

var AFforms=new Array(<?php echo $AFforms_js; ?>);
var AFids=new Array(<?php echo $AFids_js; ?>);
var AFlengths=new Array(<?php echo $AFlengths_js; ?>);
var AFnames=new Array(<?php echo $AFnames_js; ?>);
var AFoptions=new Array(<?php echo $AFoptions_js; ?>);
var agcDIR='<?php echo $agcDIR; ?>';
var agcPAGE='<?php echo $agcPAGE; ?>';
var agentcall_manual='<?php echo $agentcall_manual; ?>';
var agentcallsstatus='<?php echo $agentcallsstatus; ?>'
var agent_log_id='<?php echo $agent_log_id; ?>';
var agentonly_callbacks='<?php echo $agentonly_callbacks; ?>';
var agent_pause_codes_active='<?php echo $agent_pause_codes_active; ?>';
var agc_dial_prefix='<?php echo $dial_prefix; ?>';
var agent_template='<?php echo $config['settings']['agent_template']; ?>';
var allcalls_delay='<?php echo $allcalls_delay; ?>';
var allow_sipsak_messages='<?php echo $config['settings']['allow_sipsak_messages']; ?>';
var allow_tab_switch='<?php echo $allow_tab_switch; ?>';
var alt_phone_dialing='<?php echo $alt_phone_dialing; ?>';
var asterisk_version='<?php echo $config['server']['asterisk_version']; ?>';
var auto_dial_level='<?php echo $auto_dial_level; ?>';
var cal_bg1='<?php echo $cal_bg1; ?>';
var cal_bg2='<?php echo $cal_bg2; ?>';
var cal_bg3='<?php echo $cal_bg3; ?>';
var cal_bg4='<?php echo $cal_bg4; ?>';
var cal_bg5='<?php echo $cal_bg5; ?>';
var cal_border1='<?php echo $cal_border1; ?>';
var cal_border2='<?php echo $cal_border2; ?>';
var cal_border3='<?php echo $cal_border3; ?>';
var cal_fc='<?php echo $cal_fc; ?>';
var callback_bg2='<?php echo $callback_bg2; ?>';
var callholdstatus='<?php echo $callholdstatus; ?>'
var CalL_XC_a_Dtmf='<?php echo $xferconf_a_dtmf; ?>';
var CalL_XC_a_NuMber='<?php echo $xferconf_a_number; ?>';
var CalL_XC_b_Dtmf='<?php echo $xferconf_b_dtmf; ?>';
var CalL_XC_b_NuMber='<?php echo $xferconf_b_number; ?>';
var campagentstatctmax='<?php echo $campagentstatctmax; ?>'
var campaign_allow_inbound=<?php echo $campaign_allow_inbound; ?>;
var campaign_am_message_exten='<?php echo $campaign_am_message_exten; ?>';
var campaign_cid='<?php echo $campaign_cid; ?>';
var campaign_cid_name='<?php echo $campaign_cid_name; ?>';
var campaign_leads_to_call='<?php echo $campaign_leads_to_call; ?>';
var campaign_rec_filename='<?php echo $campaign_rec_filename; ?>';
var campaign_recording='<?php echo $campaign_recording; ?>';
var campaign_script='<?php echo $campaign_script; ?>';
var campaign_vdad_exten='<?php echo $campaign_vdad_exten; ?>';
var campaign='<?php echo $VD_campaign; ?>';
var cid='<?php echo $campaign_cid; ?>';
var cid_name='<?php echo $campaign_cid_name; ?>';
var closer_bg='<?php echo $closer_bg; ?>';
var closer_fc2='<?php echo $closer_fc2; ?>';
var closer_fc='<?php echo $closer_fc; ?>';
var conf_silent_prefix='<?php echo $conf_silent_prefix; ?>';
var conf_check_attempts=<?php echo $conf_check_attempts; ?>;
var conf_check_attempts_cleanup=<?php echo ($conf_check_attempts + 2); ?>;
var CusTCB_bgcolor='<?php echo $status_callback_bg; ?>';
var default_xfer_group='<?php echo $default_xfer_group; ?>';
var default_xfer_group_name='<?php echo $default_xfer_group_name; ?>';
var dial_method='<?php echo $dial_method; ?>';
var dialplan_number='<?php echo $dialplan_number; ?>';
var dial_context='<?php echo $dial_context; ?>';
var dial_prefix='<?php echo $dial_prefix; ?>';
var dial_timeout='<?php echo $dial_timeout; ?>';
var dispo_bg2='<?php echo $dispo_bg2; ?>';
var dispo_bg='<?php echo $dispo_bg; ?>';
var dispo_check_all_pause='<?php echo $dispo_check_all_pause; ?>';
var dispo_fc='<?php echo $dispo_fc; ?>';
var dtmf_send_extension='<?php echo $dtmf_send_extension; ?>';
var email_template_actions=new Array(<?php echo $email_template_actions; ?>);
var enable_fast_refresh=<?php echo $enable_fast_refresh; ?>;
var enable_sipsak_messages='<?php echo $enable_sipsak_messages; ?>';
var epoch_sec=<?php echo $StarTtimE; ?>;
var evenrows='<?php echo $evenrows; ?>';
var ext_context='<?php echo $ext_context; ?>';
var extension='<?php echo $extension; ?>';
var extension_xfer='<?php echo $extension; ?>';
var extvalue=extension;
var fast_refresh_rate='<?php echo $fast_refresh_rate; ?>';
var filedate='<?php echo $FILE_TIME; ?>';
var isodate='<?php echo $FILE_TIME; ?>';
var flag_channels='<?php echo $flag_channels; ?>';
var flag_string='<?php echo $flag_string; ?>';
var get_call_launch='<?php echo $get_call_launch; ?>';
var group='<?php echo $VD_campaign; ?>';
var hangup_all_non_reserved=<?php echo $hangup_all_non_reserved; ?>;
var hide_xfer_local_closer='<?php echo $hide_xfer_local_closer; ?>';
var hide_xfer_dial_override='<?php echo $hide_xfer_dial_override; ?>';
var hide_xfer_hangup_xfer='<?php echo $hide_xfer_hangup_xfer; ?>';
var hide_xfer_leave_3way='<?php echo $hide_xfer_leave_3way; ?>';
var hide_xfer_dial_with='<?php echo $hide_xfer_dial_with; ?>';
var hide_xfer_hangup_both='<?php echo $hide_xfer_hangup_both; ?>';
var hide_xfer_blind_xfer='<?php echo $hide_xfer_blind_xfer; ?>';
var hide_xfer_park_dial='<?php echo $hide_xfer_park_dial; ?>';
var hide_xfer_blind_vmail='<?php echo $hide_xfer_blind_vmail; ?>';
var HidEMonitoRSessionS='<?php echo $HidEMonitoRSessionS; ?>';
var HKhotkeys=new Array(<?php echo $HKhotkeys; ?>);
var HK_statuses_camp='<?php echo $HK_statuses_camp; ?>';
var HKstatuses=new Array(<?php echo $HKstatuses; ?>);
var HKstatusnames=new Array(<?php echo $HKstatusnames; ?>);
var HKuser_level='<?php echo $HKuser_level; ?>';
var HKxferextens=new Array(<?php echo $HKxferextens; ?>);
var HTheight=<?php echo $HTheight; ?>;
var inbound_man='<?php echo $inbound_man; ?>';
var INgroupCOUNT='<?php echo $INgrpCT; ?>';
var lead_cid='<?php echo $campaign_cid; ?>';
var lead_cust2_cid='<?php echo $campaign_cid; ?>';
var LIVE_default_xfer_group='<?php echo $default_xfer_group; ?>';
var local_consult_xfers='<?php echo $local_consult_xfers; ?>';
var local_gmt ='<?php echo $local_gmt; ?>';
var LOGfullname='<?php echo $LOGfullname; ?>';
var LogouTKicKAlL='<?php echo $LogouTKicKAlL; ?>';
var LOGxfer_agent2agent='<?php echo $LOGxfer_agent2agent; ?>';
var manual_dial_allow_skip='<?php echo $VU_manual_dial_allow_skip; ?>';
var manual_dial_preview='<?php echo $manual_dial_preview; ?>';
var mdnLisT_id='<?php echo $manual_dial_list_id; ?>';
var multicomp='<?php echo $config['settings']['enable_multicompany']; ?>';
var multi_line_comments='<?php echo $multi_line_comments; ?>';
var no_delete_sessions='<?php echo $no_delete_sessions; ?>';
var NOW_TIME='<?php echo $NOW_TIME; ?>';
var oddrows='<?php echo $oddrows; ?>';
var omit_phone_code='<?php echo $omit_phone_code; ?>';
var default_phone_code='<?php echo $default_phone_code; ?>';
var osdial_agent_disable='<?php echo $config['settings']['osdial_agent_disable']; ?>';
var OSDiaL_allow_closers='<?php echo $OSDiaL_allow_closers; ?>';
var OSDiaL_web_form_address2_enc='<?php echo $OSDiaL_web_form_address2_enc; ?>';
var OSDiaL_web_form_address2='<?php echo $OSDiaL_web_form_address2; ?>';
var OSDiaL_web_form_address_enc='<?php echo $OSDiaL_web_form_address_enc; ?>';
var OSDiaL_web_form_address='<?php echo $OSDiaL_web_form_address; ?>';
var panel_bgcolor='<?php echo $panel_bg; ?>';
var panel_bg='<?php echo $panel_bg; ?>';
var park_on_extension='<?php echo $OSDiaL_park_on_extension; ?>';
var pass='<?php echo $VD_pass; ?>';
var pause_bg2='<?php echo $pause_bg2; ?>';
var pause_bg='<?php echo $pause_bg; ?>';
var pause_fc='<?php echo $pause_fc; ?>';
var phone_cid_name='<?php echo $phone_cid_name; ?>';
var phone_cid='<?php echo $phone_cid; ?>';
var phone_ip='<?php echo $phone_ip; ?>';
var phone_login='<?php echo $phone_login; ?>';
var phone_pass='<?php echo $phone_pass; ?>';
var previewFD_time=<?php echo $previewFD_time; ?>;
var previewFD_time_remaining=<?php echo $previewFD_time; ?>;
var protocol='<?php echo $protocol; ?>';
var recording_exten='<?php echo $campaign_rec_exten; ?>';
var reselect_preview_dial=<?php echo $manual_preview_default; ?>;
var scheduled_callbacks='<?php echo $scheduled_callbacks; ?>';
var script_bg='<?php echo $script_bg; ?>';
var SDwidth=<?php echo $SDwidth; ?>;
var server_ip_dialstring='<?php echo $server_ip_dialstring; ?>';
var server_ip='<?php echo $server_ip; ?>';
var session_id='<?php echo $session_id; ?>';
var session_name='<?php echo $session_name; ?>';
var SQLdate='<?php echo $NOW_TIME; ?>';
var starting_alt_phone_dialing='<?php echo $alt_phone_dialing; ?>';
var starting_dial_level='<?php echo $auto_dial_level; ?>';
var StarTtimE='<?php echo $StarTtimE; ?>';
var status_alert_color='<?php echo $status_alert_color; ?>';
var status_bg='<?php echo $status_bg; ?>';
var status_intense_color='<?php echo $status_intense_color; ?>';
var status_preview_color='<?php echo $status_preview_color; ?>';
var submit_method='<?php echo $submit_method; ?>';
var submit_method_tmp='<?php echo $submit_method; ?>';
var system_alert_bg2='<?php echo $system_alert_bg2; ?>';
var t1='<?php echo $t1; ?>';
var UnixTime='<?php echo $StarTtimE; ?>';
var use_cid_areacode_map='<?php echo $use_cid_areacode_map; ?>';
var use_custom2_callerid='<?php echo $use_custom2_callerid; ?>';
var use_internal_dnc='<?php echo $use_internal_dnc; ?>';
var user_abb='<?php echo $user_abb; ?>';
var user_level='<?php echo $user_level; ?>';
var user='<?php echo $VD_login; ?>';
var VARcid_areacode_names=new Array(<?php echo $VARcid_areacode_names; ?>);
var VARcid_areacode_numbers=new Array(<?php echo $VARcid_areacode_numbers; ?>);
var VARcid_areacodes=new Array(<?php echo $VARcid_areacodes; ?>);
var VARingroups=new Array(<?php echo $VARingroups; ?>);
var VARpause_code_names=new Array(<?php echo $VARpause_code_names; ?>);
var VARpause_codes=new Array(<?php echo $VARpause_codes; ?>);
var VARstatuses=new Array(<?php echo $VARstatuses; ?>);
var VARstatusnames=new Array(<?php echo $VARstatusnames; ?>);
var VARxfergroupsnames=new Array(<?php echo $VARxfergroupsnames; ?>);
var VARxfergroups=new Array(<?php echo $VARxfergroups; ?>);
var VDIC_web_form_address2='<?php echo $OSDiaL_web_form_address2; ?>';
var VDIC_web_form_address='<?php echo $OSDiaL_web_form_address; ?>';
var VD_pause_codes_ct='<?php echo $VD_pause_codes_ct; ?>';
var VD_statuses_ct='<?php echo $VD_statuses_ct; ?>';
var VDstop_rec_after_each_call='<?php echo $VDstop_rec_after_each_call; ?>';
var view_scripts='<?php echo $view_scripts; ?>';
var voicemail_id='<?php echo $voicemail_id; ?>';
var voicemail_password='<?php echo $voicemail_password; ?>';
var voicemail_email='<?php echo $voicemail_email; ?>';
var volumecontrol_active='<?php echo $volumecontrol_active; ?>';
var VU_agent_choose_ingroups='<?php echo $VU_agent_choose_ingroups; ?>';
var VU_closer_default_blended='<?php echo $VU_closer_default_blended; ?>';
var VU_hotkeys_active='<?php echo $VU_hotkeys_active; ?>';
var VU_osdial_transfers='<?php echo $VU_osdial_transfers; ?>';
var web_form2_extwindow=<?php echo $web_form2_extwindow; ?>;
var web_form_extwindow=<?php echo $web_form_extwindow; ?>;
var webform_session='<?php echo $webform_sessionname; ?>';
var wf2_enc_address='<?php echo $OSDiaL_web_form_address2; ?>';
var wf_enc_address='<?php echo $OSDiaL_web_form_address; ?>';
var wrapup_message='<?php echo $wrapup_message; ?>';
var wrapup_seconds='<?php echo $wrapup_seconds; ?>';
var xfer_cid_mode='<?php echo $xfer_cid_mode; ?>';
var XFgroupCOUNT='<?php echo $XFgrpCT; ?>';

var aapath="templates/<?php echo $config['settings']['agent_template']; ?>/images/";
var DiaLControl_auto_HTML="<img src=\""+aapath+"vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\"Pause\"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\""+aapath+"vdc_LB_resume.gif\" width=70 height=18 border=0 alt=\"Resume\"></a>";
var DiaLControl_auto_HTML_OFF="<img src=\""+aapath+"vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\"Pause\"><img src=\""+aapath+"vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\">";
var DiaLControl_auto_HTML_ready="<a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADpause','NEW_ID');\"><img src=\""+aapath+"vdc_LB_pause.gif\" width=70 height=18 border=0 alt=\"Pause\"></a><img src=\""+aapath+"vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\">";
var DiaLControl_manual_HTML="<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><img src=\""+aapath+"vdc_LB_dialnextnumber.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\"></a>";
var dtmf_0=new Image(); dtmf_0.src=aapath+"dtmf_0.png";
var dtmf_0_OFF=new Image(); dtmf_0_OFF.src=aapath+"dtmf_0_OFF.png";
var dtmf_1=new Image(); dtmf_1.src=aapath+"dtmf_1.png";
var dtmf_1_OFF=new Image(); dtmf_1_OFF.src=aapath+"dtmf_1_OFF.png";
var dtmf_2=new Image(); dtmf_2.src=aapath+"dtmf_2.png";
var dtmf_2_OFF=new Image(); dtmf_2_OFF.src=aapath+"dtmf_2_OFF.png";
var dtmf_3=new Image(); dtmf_3.src=aapath+"dtmf_3.png";
var dtmf_3_OFF=new Image(); dtmf_3_OFF.src=aapath+"dtmf_3_OFF.png";
var dtmf_4=new Image(); dtmf_4.src=aapath+"dtmf_4.png";
var dtmf_4_OFF=new Image(); dtmf_4_OFF.src=aapath+"dtmf_4_OFF.png";
var dtmf_5=new Image(); dtmf_5.src=aapath+"dtmf_5.png";
var dtmf_5_OFF=new Image(); dtmf_5_OFF.src=aapath+"dtmf_5_OFF.png";
var dtmf_6=new Image(); dtmf_6.src=aapath+"dtmf_6.png";
var dtmf_6_OFF=new Image(); dtmf_6_OFF.src=aapath+"dtmf_6_OFF.png";
var dtmf_7=new Image(); dtmf_7.src=aapath+"dtmf_7.png";
var dtmf_7_OFF=new Image(); dtmf_7_OFF.src=aapath+"dtmf_7_OFF.png";
var dtmf_8=new Image(); dtmf_8.src=aapath+"dtmf_8.png";
var dtmf_8_OFF=new Image(); dtmf_8_OFF.src=aapath+"dtmf_8_OFF.png";
var dtmf_9=new Image(); dtmf_9.src=aapath+"dtmf_9.png";
var dtmf_9_OFF=new Image(); dtmf_9_OFF.src=aapath+"dtmf_9_OFF.png";
var dtmf_hash=new Image(); dtmf_hash.src=aapath+"dtmf_hash.png";
var dtmf_hash_OFF=new Image(); dtmf_hash_OFF.src=aapath+"dtmf_hash_OFF.png";
var dtmf_star=new Image(); dtmf_star.src=aapath+"dtmf_star.png";
var dtmf_star_OFF=new Image(); dtmf_star_OFF.src=aapath+"dtmf_star_OFF.png";
var image_agenttopleft=new Image(); image_agenttopleft.src=aapath+"AgentTopLeft.png";
var image_agenttopleft2=new Image(); image_agenttopleft2.src=aapath+"AgentTopLeft2.png";
var image_agenttopright=new Image(); image_agenttopright.src=aapath+"AgentTopRight.png";
var image_agenttoprights=new Image(); image_agenttoprights.src=aapath+"AgentTopRightS.png";
var image_agentsidetab_tab=new Image(); image_agentsidetab_tab.src=aapath+"agentsidetab_tab.png";
var image_agentsidetab_top=new Image(); image_agentsidetab_top.src=aapath+"agentsidetab_top.png";
var image_agentsidetab_cancel=new Image(); image_agentsidetab_cancel.src=aapath+"agentsidetab_cancel.png";
var image_agentsidetab_extra=new Image(); image_agentsidetab_extra.src=aapath+"agentsidetab_extra.png";
var image_agentsidetab_line=new Image(); image_agentsidetab_line.src=aapath+"agentsidetab_line.png";
var image_blank=new Image(); image_blank.src=aapath+"blank.gif";
var image_LB_dialnextnumber=new Image(); image_LB_dialnextnumber.src=aapath+"vdc_LB_dialnextnumber.gif";
var image_LB_dialnextnumber_OFF=new Image(); image_LB_dialnextnumber_OFF.src=aapath+"vdc_LB_dialnextnumber_OFF.gif";
var image_LB_grabparkedcall=new Image(); image_LB_grabparkedcall.src=aapath+"vdc_LB_grabparkedcall.gif";
var image_LB_grabparkedcall_OFF=new Image(); image_LB_grabparkedcall_OFF.src=aapath+"vdc_LB_grabparkedcall_OFF.gif";
var image_LB_hangupcustomer=new Image(); image_LB_hangupcustomer.src=aapath+"vdc_LB_hangupcustomer.gif";
var image_LB_hangupcustomer_OFF=new Image(); image_LB_hangupcustomer_OFF.src=aapath+"vdc_LB_hangupcustomer_OFF.gif";
var image_LB_parkcall=new Image(); image_LB_parkcall.src=aapath+"vdc_LB_parkcall.gif";
var image_LB_parkcall_OFF=new Image(); image_LB_parkcall_OFF.src=aapath+"vdc_LB_parkcall_OFF.gif";
var image_LB_pause=new Image(); image_LB_pause.src=aapath+"vdc_LB_pause.gif";
var image_LB_pause_OFF=new Image(); image_LB_pause_OFF.src=aapath+"vdc_LB_pause_OFF.gif";
var image_LB_resume=new Image(); image_LB_resume.src=aapath+"vdc_LB_resume.gif";
var image_LB_resume_OFF=new Image(); image_LB_resume_OFF.src=aapath+"vdc_LB_resume_OFF.gif";
var image_LB_senddtmf=new Image(); image_LB_senddtmf.src=aapath+"vdc_LB_senddtmf.gif";
var image_LB_senddtmf_OFF=new Image(); image_LB_senddtmf_OFF.src=aapath+"vdc_LB_senddtmf_OFF.gif";
var image_LB_startrecording=new Image(); image_LB_startrecording.src=aapath+"vdc_LB_startrecording.gif";
var image_LB_startrecording_OFF=new Image(); image_LB_startrecording_OFF.src=aapath+"vdc_LB_startrecording_OFF.gif";
var image_LB_stoprecording=new Image(); image_LB_stoprecording.src=aapath+"vdc_LB_stoprecording.gif";
var image_LB_stoprecording_OFF=new Image(); image_LB_stoprecording_OFF.src=aapath+"vdc_LB_stoprecording_OFF.gif";
var image_LB_transferconf=new Image(); image_LB_transferconf.src=aapath+"vdc_LB_transferconf.gif";
var image_LB_transferconf_OFF=new Image(); image_LB_transferconf_OFF.src=aapath+"vdc_LB_transferconf_OFF.gif";
var image_LB_webform2=new Image(); image_LB_webform2.src=aapath+"vdc_LB_webform2.gif";
var image_LB_webform=new Image(); image_LB_webform.src=aapath+"vdc_LB_webform.gif";
var image_LB_webform2_OFF=new Image(); image_LB_webform2_OFF.src=aapath+"vdc_LB_webform2_OFF.gif";
var image_LB_webform_OFF=new Image(); image_LB_webform_OFF.src=aapath+"vdc_LB_webform_OFF.gif";
var image_livecall_OFF=new Image(); image_livecall_OFF.src=aapath+"agc_live_call_OFF.gif";
var image_livecall_ON=new Image(); image_livecall_ON.src=aapath+"agc_live_call_ON.gif";
//var image_ShowCallbackInfo_OFF=new Image(); image_ShowCallbackInfo_OFF.src=aapath+"ShowCallbackInfo.png";


if (enable_fast_refresh) refresh_interval=fast_refresh_rate;
var hotkeys=new Array();
for (var h=0; h<HK_statuses_camp; h++) {
 hotkeys[HKhotkeys[h]]=HKstatuses[h]+" ----- "+HKstatusnames[h]+ " ----- "+HKxferextens[h];
}

