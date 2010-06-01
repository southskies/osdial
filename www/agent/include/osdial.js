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
 * # 090410-1155 - Added custom2 field.
 * # 090410-1750 - Added allow_tab_switch.
 * # 090428-0938 - Added external_key to web_form_vars.
 * # 090515-0135 - Added preview_force_dial_time
 * # 090515-0140 - Added manual_preview_default
 * # 090515-0538 - Added web_form_extwindow and web_form2_extwindow
 * # 090520-1915 - Changed inbound in manual mode to work without the INBOUND_MAN dial status.
 * # 090603-1735 - Added to manual-dial bug that prevented manual dial from being used after a hotkey diposition.
 * # 090603-2306 - Added fix to allow diposition on manual call using hotkeys if call is active > 5 seconds.
 */


	var MTvar;
	var NOW_TIME = '<? echo $NOW_TIME ?>';
	var SQLdate = '<? echo $NOW_TIME ?>';
	var StarTtimE = '<? echo $StarTtimE ?>';
	var UnixTime = '<? echo $StarTtimE ?>';
	var UnixTimeMS = 0;
	var t = new Date();
	var c = new Date();
	LCAe = new Array('','','','','','');
	LCAc = new Array('','','','','','');
	LCAt = new Array('','','','','','');
	LMAe = new Array('','','','','','');
	var CalL_XC_a_Dtmf = '<? echo $xferconf_a_dtmf ?>';
	var CalL_XC_a_NuMber = '<? echo $xferconf_a_number ?>';
	var CalL_XC_b_Dtmf = '<? echo $xferconf_b_dtmf ?>';
	var CalL_XC_b_NuMber = '<? echo $xferconf_b_number ?>';
	var VU_hotkeys_active = '<? echo $VU_hotkeys_active ?>';
	var VU_agent_choose_ingroups = '<? echo $VU_agent_choose_ingroups ?>';
	var VU_agent_choose_ingroups_DV = '';
	var PostDatETimE = '';
	var CallBackDatETimE = '';
	var CallBackrecipient = '';
	var CallBackCommenTs = '';
	var scheduled_callbacks = '<? echo $scheduled_callbacks ?>';
	var dispo_check_all_pause = '<? echo $dispo_check_all_pause ?>';
	var agent_pause_codes_active = '<? echo $agent_pause_codes_active ?>';
	VARpause_codes = new Array(<? echo $VARpause_codes ?>);
	VARpause_code_names = new Array(<? echo $VARpause_code_names ?>);
	var VD_pause_codes_ct = '<? echo $VD_pause_codes_ct ?>';
	VARstatuses = new Array(<? echo $VARstatuses ?>);
	VARstatusnames = new Array(<? echo $VARstatusnames ?>);
	var VD_statuses_ct = '<? echo $VD_statuses_ct ?>';
	VARingroups = new Array(<? echo $VARingroups ?>);
	var INgroupCOUNT = '<? echo $INgrpCT ?>';
	VARxfergroups = new Array(<? echo $VARxfergroups ?>);
	VARxfergroupsnames = new Array(<? echo $VARxfergroupsnames ?>);
	var XFgroupCOUNT = '<? echo $XFgrpCT ?>';
	var default_xfer_group = '<? echo $default_xfer_group ?>';
	var default_xfer_group_name = '<? echo $default_xfer_group_name ?>';
	var LIVE_default_xfer_group = '<? echo $default_xfer_group ?>';
	var HK_statuses_camp = '<? echo $HK_statuses_camp ?>';
	HKhotkeys = new Array(<? echo $HKhotkeys ?>);
	HKstatuses = new Array(<? echo $HKstatuses ?>);
	HKstatusnames = new Array(<? echo $HKstatusnames ?>);
	HKxferextens = new Array(<? echo $HKxferextens ?>);
	var hotkeys = new Array();
	<? $h=0;
	while ($HK_statuses_camp > $h)
	{
	echo "hotkeys['$HKhotkey[$h]'] = \"$HKstatus[$h] ----- $HKstatus_name[$h] ----- $HKxfer_exten[$h]\";\n";
	$h++;
	}
	?>
	var HKdispo_display = 0;
	var HKbutton_allowed = 1;
	var HKfinish = 0;
	var scriptnames = new Array();
	<? $h=0;
	while ($MM_scripts > $h)
	{
	echo "scriptnames['$MMscriptid[$h]'] = \"$MMscriptname[$h]\";\n";
	$h++;
	}
	?>
	var scripttexts = new Array();
	<? $h=0;
	while ($MM_scripts > $h)
	{
	echo "scripttexts['$MMscriptid[$h]'] = \"$MMscripttext[$h]\";\n";
	$h++;
	}
	?>
	var decoded = '';
	var view_scripts = '<? echo $view_scripts ?>';
	var LOGfullname = '<? echo $LOGfullname ?>';
	var LOGxfer_agent2agent = '<? echo $LOGxfer_agent2agent ?>';
	var recLIST = '';
	var filename = '';
	var last_filename = '';
	var LCAcount = 0;
	var LMAcount = 0;
	var filedate = '<? echo $FILE_TIME ?>';
	var agcDIR = '<? echo $agcDIR ?>';
	var agcPAGE = '<? echo $agcPAGE ?>';
	var extension = '<? echo $extension ?>';
	var extension_xfer = '<? echo $extension ?>';
	var dialplan_number = '<? echo $dialplan_number ?>';
	var ext_context = '<? echo $ext_context ?>';
	var protocol = '<? echo $protocol ?>';
	var agentchannel = '';
	var local_gmt ='<? echo $local_gmt ?>';
	var server_ip = '<? echo $server_ip ?>';
	var server_ip_dialstring = '<? echo $server_ip_dialstring ?>';
	var asterisk_version = '<? echo $asterisk_version ?>';
<?
if ($enable_fast_refresh < 1) {echo "\tvar refresh_interval = 1000;\n";}
	else {echo "\tvar refresh_interval = $fast_refresh_rate;\n";}
?>
	var session_id = '<? echo $session_id ?>';
	var OSDiaL_closer_login_checked = 0;
	var OSDiaL_closer_login_selected = 0;
	var OSDiaL_pause_calling = 1;
	var CalLCID = '';
	var MDnextCID = '';
	var XDnextCID = '';
	var LasTCID = '';
	var lead_dial_number = '';
	var MD_channel_look = 0;
	var XD_channel_look = 0;
	var MDuniqueid = '';
	var MDchannel = '';
	var MD_ring_secondS = 0;
	var MDlogEPOCH = 0;
	var alt_dial_menu = 0;
	var agentphonelive = 0;
	var manual_dial_menu = 0;
	var VD_live_customer_call = 0;
	var VD_live_call_secondS = 0;
	var XD_live_customer_call = 0;
	var XD_live_call_secondS = 0;
	var open_dispo_screen = 0;
	var AgentDispoing = 0;
	var logout_stop_timeouts = 0;
	var OSDiaL_allow_closers = '<? echo $OSDiaL_allow_closers ?>';
	var OSDiaL_closer_blended = '0';
	var VU_closer_default_blended = '<? echo $VU_closer_default_blended ?>';
	var VDstop_rec_after_each_call = '<? echo $VDstop_rec_after_each_call ?>';
	var phone_login = '<? echo $phone_login ?>';
	var phone_pass = '<? echo $phone_pass ?>';
	var user = '<? echo $VD_login ?>';
	var user_abb = '<? echo $user_abb ?>';
	var pass = '<? echo $VD_pass ?>';
	var campaign = '<? echo $VD_campaign ?>';
	var group = '<? echo $VD_campaign ?>';
	var OSDiaL_web_form_address_enc = '<? echo $OSDiaL_web_form_address_enc ?>';
	var OSDiaL_web_form_address = '<? echo $OSDiaL_web_form_address ?>';
	var OSDiaL_web_form_address2 = '<? echo $OSDiaL_web_form_address2 ?>';
	var OSDiaL_web_form_address2_enc = '<? echo $OSDiaL_web_form_address2_enc ?>';
	var VDIC_web_form_address = '<? echo $OSDiaL_web_form_address ?>';
	var VDIC_web_form_address2 = '<? echo $OSDiaL_web_form_address2 ?>';
	var wf_enc_address = '<? echo $OSDiaL_web_form_address ?>';
	var wf2_enc_address = '<? echo $OSDiaL_web_form_address2 ?>';
	var web_form_extwindow = <?= $web_form_extwindow ?>;
	var web_form2_extwindow = <?= $web_form2_extwindow ?>;
	var CalL_ScripT_id = '';
	var CalL_AutO_LauncH = '';
	var CalL_allow_tab = '';
	var panel_bgcolor = '<?=$panel_bg?>';
	var CusTCB_bgcolor = '<?=$status_callback_bg?>';
	var auto_dial_level = '<? echo $auto_dial_level ?>';
	var starting_dial_level = '<? echo $auto_dial_level ?>';
	var dial_timeout = '<? echo $dial_timeout ?>';
	var dial_prefix = '<? echo $dial_prefix ?>';
	var cid = '<? echo $campaign_cid ?>';
	var cid_name = '<? echo $campaign_cid_name ?>';
	var campaign_cid = '<? echo $campaign_cid ?>';
	var campaign_cid_name = '<? echo $campaign_cid_name ?>';
	var phone_cid = '<? echo $phone_cid ?>';
	var phone_cid_name = '<? echo $phone_cid_name ?>';
	var xfer_cid_mode = '<? echo $xfer_cid_mode ?>';
	var lead_cid = '<? echo $campaign_cid ?>';
	var lead_cust2_cid = '<? echo $campaign_cid ?>';
	var use_custom2_callerid = '<? echo $use_custom2_callerid ?>';
	var campaign_vdad_exten = '<? echo $campaign_vdad_exten ?>';
	var campaign_leads_to_call = '<? echo $campaign_leads_to_call ?>';
	var epoch_sec = <? echo $StarTtimE ?>;
	var dtmf_send_extension = '<? echo $dtmf_send_extension ?>';
	var recording_exten = '<? echo $campaign_rec_exten ?>';
	var campaign_recording = '<? echo $campaign_recording ?>';
	var campaign_rec_filename = '<? echo $campaign_rec_filename ?>';
	var campaign_script = '<? echo $campaign_script ?>';
	var get_call_launch = '<? echo $get_call_launch ?>';
	var allow_tab_switch = '<? echo $allow_tab_switch ?>';
	var campaign_am_message_exten = '<? echo $campaign_am_message_exten ?>';
	var park_on_extension = '<? echo $OSDiaL_park_on_extension ?>';
	var park_count=0;
	var park_refresh=0;
	var customerparked=0;
	var check_n = 0;
	var conf_check_recheck = 0;
	var lastconf='';
	var lastcustchannel='';
	var lastcustserverip='';
	var lastxferchannel='';
	var custchannellive=0;
	var xferchannellive=0;
	var nochannelinsession=0;
	var agc_dial_prefix = dial_prefix + '1';
	var conf_silent_prefix = '<? echo $conf_silent_prefix ?>';
	var menuheight = 30;
	var menuwidth = 30;
	var menufontsize = 8;
	var textareafontsize = 10;
	var check_s;
	var active_display = 1;
	var conf_channels_xtra_display = 0;
	var display_message = '';
	var web_form_vars = '';
	var web_form_vars2 = '';
	var Nactiveext;
	var Nbusytrunk;
	var Nbusyext;
	var extvalue = extension;
	var activeext_query;
	var busytrunk_query;
	var busyext_query;
	var busytrunkhangup_query;
	var busylocalhangup_query;
	var activeext_order='asc';
	var busytrunk_order='asc';
	var busyext_order='asc';
	var busytrunkhangup_order='asc';
	var busylocalhangup_order='asc';
	var xmlhttp=false;
	var XfeR_channel = '';
	var XDcheck = '';
	var agent_log_id = '<? echo $agent_log_id ?>';
	var session_name = '<? echo $session_name ?>';
	var AutoDialReady = 0;
	var AutoDialWaiting = 0;
	var fronter = '';
	var VDCL_group_id = '';
	var previous_dispo = '';
	var previous_called_count = '';
	var hot_keys_active = 0;
	var dtmf_keys_active = 0;
	var all_record = 'NO';
	var all_record_count = 0;
	var LeaDDispO = '';
	var LeaDPreVDispO = '';
	var AgaiNHanguPChanneL = '';
	var AgaiNHanguPServeR = '';
	var AgainCalLSecondS = '';
	var AgaiNCalLCID = '';
	var CB_count_check = 60;
	var callholdstatus = '<? echo $callholdstatus ?>'
	var agentcallsstatus = '<? echo $agentcallsstatus ?>'
	var campagentstatctmax = '<? echo $campagentstatctmax ?>'
	var campagentstatct = '0';
	var manual_dial_in_progress = 0;
	var auto_dial_alt_dial = 0;
	var reselect_preview_dial = <?= $manual_preview_default ?>;
	var reselect_alt_dial = 0;
	var alt_dial_active = 0;
	var mdnLisT_id = '<? echo $manual_dial_list_id ?>';
	var VU_osdial_transfers = '<? echo $VU_osdial_transfers ?>';
	var agentonly_callbacks = '<? echo $agentonly_callbacks ?>';
	var agentcall_manual = '<? echo $agentcall_manual ?>';
	var manual_dial_preview = '<? echo $manual_dial_preview ?>';
	var starting_alt_phone_dialing = '<? echo $alt_phone_dialing ?>';
	var alt_phone_dialing = '<? echo $alt_phone_dialing ?>';
	var wrapup_seconds = '<? echo $wrapup_seconds ?>';
	var wrapup_message = '<? echo $wrapup_message ?>';
	var wrapup_counter = 0;
	var wrapup_waiting = 0;
	var use_internal_dnc = '<? echo $use_internal_dnc ?>';
	var allcalls_delay = '<? echo $allcalls_delay ?>';
	var omit_phone_code = '<? echo $omit_phone_code ?>';
	var no_delete_sessions = '<? echo $no_delete_sessions ?>';
	var webform_session = '<? echo $webform_sessionname ?>';
	var local_consult_xfers = '<? echo $local_consult_xfers ?>';
	var osdial_agent_disable = '<? echo $osdial_agent_disable ?>';
	var CBentry_time = '';
	var CBcallback_time = '';
	var CBuser = '';
	var CBcomments = '';
	var volumecontrol_active = '<? echo $volumecontrol_active ?>';
	var PauseCode_HTML = '';
	var manual_auto_hotkey = 0;
	var dialed_number = '';
	var dialed_label = '';
	var source_id = '';
	var external_key = '';
	var DispO3waychannel = '';
	var DispO3wayXtrAchannel = '';
	var DispO3wayCalLserverip = '';
	var DispO3wayCalLxfernumber = '';
	var DispO3wayCalLcamptail = '';
	var PausENotifYCounTer = 0;
	var RedirecTxFEr = 0;
	var phone_ip = '<? echo $phone_ip ?>';
	var enable_sipsak_messages = '<? echo $enable_sipsak_messages ?>';
	var allow_sipsak_messages = '<? echo $allow_sipsak_messages ?>';
	var HidEMonitoRSessionS = '<? echo $HidEMonitoRSessionS ?>';
	var LogouTKicKAlL = '<? echo $LogouTKicKAlL ?>';
	var flag_channels = '<? echo $flag_channels ?>';
	var flag_string = '<? echo $flag_string ?>';
	var DiaLControl_auto_HTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\"Pause\"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume.gif\" border=0 alt=\"Resume\"></a>";
	var DiaLControl_auto_HTML_ready = "<a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADpause','NEW_ID');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause.gif\" border=0 alt=\"Pause\"></a><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\">";
	var DiaLControl_auto_HTML_OFF = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\"Pause\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\">";
	var DiaLControl_manual_HTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber.gif\" border=0 alt=\"Dial Next Number\"></a>";
	var image_blank = new Image();
		image_blank.src="templates/<?= $agent_template ?>/images/blank.gif";
	var image_livecall_OFF = new Image();
		image_livecall_OFF.src="templates/<?= $agent_template ?>/images/agc_live_call_OFF.gif";
	var image_livecall_ON = new Image();
		image_livecall_ON.src="templates/<?= $agent_template ?>/images/agc_live_call_ON.gif";
	var image_LB_dialnextnumber = new Image();
		image_LB_dialnextnumber.src="templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber.gif";
	var image_LB_hangupcustomer = new Image();
		image_LB_hangupcustomer.src="templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer.gif";
	var image_LB_transferconf = new Image();
		image_LB_transferconf.src="templates/<?= $agent_template ?>/images/vdc_LB_transferconf.gif";
	var image_LB_grabparkedcall = new Image();
		image_LB_grabparkedcall.src="templates/<?= $agent_template ?>/images/vdc_LB_grabparkedcall.gif";
	var image_LB_parkcall = new Image();
		image_LB_parkcall.src="templates/<?= $agent_template ?>/images/vdc_LB_parkcall.gif";
	var image_LB_webform = new Image();
		image_LB_webform.src="templates/<?= $agent_template ?>/images/vdc_LB_webform.gif";
	var image_LB_webform = new Image();
		image_LB_webform.src="templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif";
	var image_LB_stoprecording = new Image();
		image_LB_stoprecording.src="templates/<?= $agent_template ?>/images/vdc_LB_stoprecording.gif";
	var image_LB_startrecording = new Image();
		image_LB_startrecording.src="templates/<?= $agent_template ?>/images/vdc_LB_startrecording.gif";
	var image_LB_pause = new Image();
		image_LB_pause.src="templates/<?= $agent_template ?>/images/vdc_LB_pause.gif";
	var image_LB_resume = new Image();
		image_LB_resume.src="templates/<?= $agent_template ?>/images/vdc_LB_resume.gif";
	var image_LB_senddtmf = new Image();
		image_LB_senddtmf.src="templates/<?= $agent_template ?>/images/vdc_LB_senddtmf.gif";
	var image_LB_dialnextnumber_OFF = new Image();
		image_LB_dialnextnumber_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif";
	var image_LB_hangupcustomer_OFF = new Image();
		image_LB_hangupcustomer_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer_OFF.gif";
	var image_LB_transferconf_OFF = new Image();
		image_LB_transferconf_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_transferconf_OFF.gif";
	var image_LB_grabparkedcall_OFF = new Image();
		image_LB_grabparkedcall_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_grabparkedcall_OFF.gif";
	var image_LB_parkcall_OFF = new Image();
		image_LB_parkcall_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_parkcall_OFF.gif";
	var image_LB_webform_OFF = new Image();
		image_LB_webform_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_webform_OFF.gif";
	var image_LB_webform_OFF = new Image();
		image_LB_webform_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_webform2_OFF.gif";
	var image_LB_stoprecording_OFF = new Image();
		image_LB_stoprecording_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_stoprecording_OFF.gif";
	var image_LB_startrecording_OFF = new Image();
		image_LB_startrecording_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_startrecording_OFF.gif";
	var image_LB_pause_OFF = new Image();
		image_LB_pause_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif";
	var image_LB_resume_OFF = new Image();
		image_LB_resume_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif";
	var image_LB_senddtmf_OFF = new Image();
		image_LB_senddtmf_OFF.src="templates/<?= $agent_template ?>/images/vdc_LB_senddtmf_OFF.gif";
	var dtmf_0 = new Image(); dtmf_0.src="templates/<?= $agent_template ?>/images/dtmf_0.png";
	var dtmf_1 = new Image(); dtmf_1.src="templates/<?= $agent_template ?>/images/dtmf_1.png";
	var dtmf_2 = new Image(); dtmf_2.src="templates/<?= $agent_template ?>/images/dtmf_2.png";
	var dtmf_3 = new Image(); dtmf_3.src="templates/<?= $agent_template ?>/images/dtmf_3.png";
	var dtmf_4 = new Image(); dtmf_4.src="templates/<?= $agent_template ?>/images/dtmf_4.png";
	var dtmf_5 = new Image(); dtmf_5.src="templates/<?= $agent_template ?>/images/dtmf_5.png";
	var dtmf_6 = new Image(); dtmf_6.src="templates/<?= $agent_template ?>/images/dtmf_6.png";
	var dtmf_7 = new Image(); dtmf_7.src="templates/<?= $agent_template ?>/images/dtmf_7.png";
	var dtmf_8 = new Image(); dtmf_8.src="templates/<?= $agent_template ?>/images/dtmf_8.png";
	var dtmf_9 = new Image(); dtmf_9.src="templates/<?= $agent_template ?>/images/dtmf_9.png";
	var dtmf_star = new Image(); dtmf_star.src="templates/<?= $agent_template ?>/images/dtmf_star.png";
	var dtmf_hash = new Image(); dtmf_hash.src="templates/<?= $agent_template ?>/images/dtmf_hash.png";
	var dtmf_0_OFF = new Image(); dtmf_0_OFF.src="templates/<?= $agent_template ?>/images/dtmf_0_OFF.png";
	var dtmf_1_OFF = new Image(); dtmf_1_OFF.src="templates/<?= $agent_template ?>/images/dtmf_1_OFF.png";
	var dtmf_2_OFF = new Image(); dtmf_2_OFF.src="templates/<?= $agent_template ?>/images/dtmf_2_OFF.png";
	var dtmf_3_OFF = new Image(); dtmf_3_OFF.src="templates/<?= $agent_template ?>/images/dtmf_3_OFF.png";
	var dtmf_4_OFF = new Image(); dtmf_4_OFF.src="templates/<?= $agent_template ?>/images/dtmf_4_OFF.png";
	var dtmf_5_OFF = new Image(); dtmf_5_OFF.src="templates/<?= $agent_template ?>/images/dtmf_5_OFF.png";
	var dtmf_6_OFF = new Image(); dtmf_6_OFF.src="templates/<?= $agent_template ?>/images/dtmf_6_OFF.png";
	var dtmf_7_OFF = new Image(); dtmf_7_OFF.src="templates/<?= $agent_template ?>/images/dtmf_7_OFF.png";
	var dtmf_8_OFF = new Image(); dtmf_8_OFF.src="templates/<?= $agent_template ?>/images/dtmf_8_OFF.png";
	var dtmf_9_OFF = new Image(); dtmf_9_OFF.src="templates/<?= $agent_template ?>/images/dtmf_9_OFF.png";
	var dtmf_star_OFF = new Image(); dtmf_star_OFF.src="templates/<?= $agent_template ?>/images/dtmf_star_OFF.png";
	var dtmf_hash_OFF = new Image(); dtmf_hash_OFF.src="templates/<?= $agent_template ?>/images/dtmf_hash_OFF.png";
	//var image_ShowCallbackInfo_OFF = new Image();
	//	image_ShowCallbackInfo_OFF.src = "templates/<?= $agent_template ?>/images/ShowCallbackInfo.png";

	// Manual Dial: Force Manual Dial after given time.
	var previewFD_timeout_id;
	var previewFD_display_id = 0;
	var previewFD_time = <?= $previewFD_time ?>;
	var previewFD_time_remaining = previewFD_time;

	var dial_timedout = 0;

	var web_form_frame_open1 = 0;
	var web_form_frame_open2 = 0;

	var dial_method = '<? echo $dial_method ?>';
	var campaign_allow_inbound = <? echo $campaign_allow_inbound ?>;
	var inbound_man = '<? echo $inbound_man ?>';

	var submit_method = '<? echo $submit_method ?>';

	var manual_dial_allow_skip = '<? echo $VU_manual_dial_allow_skip ?>';

	var multicomp = '<? echo $multicomp ?>';

	var recording_id = 0;

	var PCSpause = 0;

	var debugWindowOpened = 0;
	var debugLevel = 0;
	var debugWindow = 0;
	var debugLevelColors = new Array();
	debugLevelColors[0] = '#000000';
	debugLevelColors[1] = '#000000';
	debugLevelColors[2] = '#330099';
	debugLevelColors[3] = '#336633';
	debugLevelColors[4] = '#993300';
	debugLevelColors[5] = '#CC0000';

// ################################################################################
// Send Hangup command for Live call connected to phone now to Manager
	function livehangup_send_hangup(taskvar) {
		debug("<b>livehangup_send_hangup:</b> taskvar=" + taskvar,2);
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			var queryCID = "HLagcW" + epoch_sec + user_abb;
			var hangupvalue = taskvar;
			livehangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Hangup&format=text&channel=" + hangupvalue + "&queryCID=" + queryCID;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(livehangup_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}
	}

	// ################################################################################
	// Send volume control command for meetme participant
	function volume_control(taskdirection,taskvolchannel,taskagentmute) {
		debug("<b>volume_control:</b> taskdirection=" + taskdirection + " taskvolchannel=" + taskvolchannel + " taskagentmute=" + taskagentmute,2);
		if (taskagentmute=='AgenT') {
			taskvolchannel = agentchannel;
		}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			var queryCID = "VCagcW" + epoch_sec + user_abb;
			var volchanvalue = taskvolchannel;
			livevolume_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=VolumeControl&format=text&channel=" + volchanvalue + "&stage=" + taskdirection + "&exten=" + session_id + "&ext_context=" + ext_context + "&queryCID=" + queryCID;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(livevolume_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}
		if (taskagentmute=='AgenT') {
			if (taskdirection=='MUTING') {
				document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('UNMUTE','" + agentchannel + "','AgenT');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_UNMUTE.gif\" BORDER=0></a>";
				document.getElementById("MutedWarning").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('UNMUTE','" + agentchannel + "','AgenT');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/muted.gif\" BORDER=0></a>";
			} else {
				document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_MUTE.gif\" BORDER=0></a>";
				document.getElementById("MutedWarning").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/mutedoff.gif\" border=0></a>";
			}
		}
	}


// ################################################################################
// park customer and place 3way call
	function xfer_park_dial() {
		debug("<b>xfer_park_dial</b>",2);
		mainxfer_send_redirect('ParK',lastcustchannel,lastcustserverip);

		SendManualDial('YES');
	}

// ################################################################################
// place 3way and customer into other conference and fake-hangup the lines
	function leave_3way_call(tempvarattempt) {
		debug("<b>leave_3way_call:</b> tempvarattempt=" + tempvarattempt,2);
		mainxfer_send_redirect('3WAY','','',tempvarattempt);

		//document.osdial_form.callchannel.value = '';
		document.getElementById("callchannel").innerHTML = '';
		document.osdial_form.callserverip.value = '';
		if( document.images ) {
			document.images['livecall'].src = image_livecall_OFF.src;
		}
		dialedcall_send_hangup();

		document.osdial_form.xferchannel.value = '';
		xfercall_send_hangup();
	}

// ################################################################################
// filter manual dialstring and pass on to originate call
	function SendManualDial(taskFromConf) {
		debug("<b>SendManualDial:</b> taskFromConf=" + taskFromConf,2);
		var regXFvars = new RegExp("XFER","g");
		if (taskFromConf == 'YES') {
                        if (document.osdial_form.xfernumber.value == '' && CalL_XC_a_NuMber.match(regXFvars)) {
                                document.osdial_form.xfernumber.value = CalL_XC_a_NuMber;
                        }
			var manual_number = document.osdial_form.xfernumber.value;
			var manual_string = manual_number.toString();
			var dial_conf_exten = session_id;
		} else {
			var manual_number = document.osdial_form.xfernumber.value;
			var manual_string = manual_number.toString();
		}
		if (manual_string.match(regXFvars)) {
			var donothing=1;
		} else {
			if (document.osdial_form.xferoverride.checked==false) {
				if (manual_string.length=='10') {
					manual_string = '1' + manual_string;
				}
				if (manual_string.length=='7' || manual_string.length >= 10) {
					manual_string = dial_prefix + manual_string;
				}
			}
		}
		if (manual_string != '') {
			if (taskFromConf == 'YES') {
				basic_originate_call(manual_string,'NO','YES',dial_conf_exten,'NO',taskFromConf);
			} else {
				basic_originate_call(manual_string,'NO','NO');
			}
			MD_ring_secondS=0;
		} else {
			 alert("You must enter a number.");
		}
	}

// ################################################################################
// Send Originate command to manager to place a phone call
	function basic_originate_call(tasknum,taskprefix,taskreverse,taskdialvalue,tasknowait,taskconfxfer) {
		debug("<b>basic_originate_call:</b> tasknum=" + tasknum + " taskprefix=" + taskprefix + " taskreverse=" + taskreverse + " taskdialvalue=" + taskdialvalue+ " tasknowait=" + tasknowait + " taskconfxfer=" + taskconfxfer,2);
		var regCXFvars = new RegExp("CXFER","g");
		var tasknum_string = tasknum.toString();
		if (tasknum_string.match(regCXFvars)) {
			var Ctasknum = tasknum_string.replace(regCXFvars, '');
			if (Ctasknum.length < 2) {
				Ctasknum = '990009';
			}
			var XfeRSelecT = document.getElementById("XfeRGrouP");
			tasknum = Ctasknum + "*" + XfeRSelecT.value + '*CXFER*' + document.osdial_form.lead_id.value + '**' + document.osdial_form.phone_number.value + '*' + user + '*';
			CustomerData_update();
		}
		var regAXFvars = new RegExp("AXFER","g");
		if (tasknum_string.match(regAXFvars)) {
			var Ctasknum = tasknum_string.replace(regAXFvars, '');
			if (Ctasknum.length < 2) {
				Ctasknum = '83009';
			}
			var closerxfercamptail = '_L';
			if (closerxfercamptail.length < 3) {
				closerxfercamptail = 'IVR';
			}
			tasknum = Ctasknum + '*' + document.osdial_form.phone_number.value + '*' + document.osdial_form.lead_id.value + '*' + campaign + '*' + closerxfercamptail + '*' + user + '*';
			CustomerData_update();
		}


		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) {
			if (taskprefix == 'NO') {
				var orig_prefix = '';
			} else {
				var orig_prefix = agc_dial_prefix;
			}
			if (taskreverse == 'YES') {
				if (taskdialvalue.length < 2) {
					var dialnum = dialplan_number;
				} else {
					var dialnum = taskdialvalue;
				}
				var originatevalue = "Local/" + tasknum + "@" + ext_context;
			} else {
				var dialnum = tasknum;
				if ( (protocol == 'EXTERNAL') || (protocol == 'Local') )  {
					var protodial = 'Local';
					var extendial = extension + "@" + ext_context;
				} else {
					var protodial = protocol;
					var extendial = extension;
				}
				var originatevalue = protodial + "/" + extendial;
			}
			if (taskconfxfer == 'YES') {
				var queryCID = "DCagcW" + epoch_sec + user_abb;
				document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"bothcall_send_hangup();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer.gif\" border=0 alt=\"Hangup Customer\"></a>";
			} else {
				var queryCID = "DVagcW" + epoch_sec + user_abb;
			}

			lead_cust2_cid = document.osdial_form.custom2.value;
			lead_cid = document.osdial_form.phone_number.value;
			if (xfer_cid_mode == 'LEAD_CUSTOM2' && lead_cid != '') {
				cid = lead_cust2_cid;
				cid_name = lead_cust2_cid;
			} else if (xfer_cid_mode == 'LEAD') {
				cid = lead_cid;
				cid_name = lead_cid;
			} else if (xfer_cid_mode == 'PHONE') {
				cid = phone_cid;
				cid_name = phone_cid_name;
			} else {
				cid = campaign_cid;
				cid_name = campaign_cid_name;
			}
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + orig_prefix + "" + dialnum + "&ext_context=" + ext_context + "&ext_priority=1&outbound_cid=" + cid + "&outbound_cid_name=" + cid_name;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(xmlhttp.responseText);

					if ((taskdialvalue.length > 0) && (tasknowait != 'YES')) {
						XDnextCID = queryCID;
						MD_channel_look=1;
						XDcheck = 'YES';

				//		document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"xfercall_send_hangup();return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_hangupxferline.gif\" border=0 alt=\"Hangup Xfer Line\"></a>";
					}
				}
			}
			delete xmlhttp;
		}
	}

// ################################################################################
// filter conf_dtmf send string and pass on to originate call
	function SendConfDTMF(taskconfdtmf) {
		debug("<b>SendConfDTMF:</b> taskconfdtmf=" + taskconfdtmf,2);
		var dtmf_number = document.osdial_form.conf_dtmf.value;
		var dtmf_string = dtmf_number.toString();
		var conf_dtmf_room = taskconfdtmf;

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			var queryCID = dtmf_string;
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=SysCIDOriginate&format=text&channel=" + dtmf_send_extension + "&queryCID=" + queryCID + "&exten=" + conf_silent_prefix + '' + conf_dtmf_room + "&ext_context=" + ext_context + "&ext_priority=1";
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}
		document.osdial_form.conf_dtmf.value = '';
	}

// ################################################################################
// Check to see if there are any channels live in the agent's conference meetme room
	function check_for_conf_calls(taskconfnum,taskforce) {
		debug("<b>check_for_conf_calls:</b> taskconfnum=" + taskconfnum + " taskforce=" + taskforce,5);
		if (typeof(xmlhttprequestcheckconf) == "undefined") {
			//alert (xmlhttprequestcheckconf == xmlhttpSendConf);
			custchannellive--;
			if ( (agentcallsstatus == '1') || (callholdstatus == '1') ) {
				campagentstatct++;
				if (campagentstatct > campagentstatctmax) {
					campagentstatct=0;
					var campagentstdisp = 'YES';
				} else {
					var campagentstdisp = 'NO';
				}
			} else {
				var campagentstdisp = 'NO';
			}

			xmlhttprequestcheckconf=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttprequestcheckconf = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttprequestcheckconf = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttprequestcheckconf = false;
			  }
			 }
			@end @*/
			//alert ("1");
			if (!xmlhttprequestcheckconf && typeof XMLHttpRequest!='undefined') {
				xmlhttprequestcheckconf = new XMLHttpRequest();
			}
			if (xmlhttprequestcheckconf) { 
				checkconf_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&client=vdc&conf_exten=" + taskconfnum + "&auto_dial_level=" + auto_dial_level + "&campagentstdisp=" + campagentstdisp;
				xmlhttprequestcheckconf.open('POST', 'conf_exten_check.php'); 
				xmlhttprequestcheckconf.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttprequestcheckconf.send(checkconf_query); 
				xmlhttprequestcheckconf.onreadystatechange = function() { 
					if (xmlhttprequestcheckconf.readyState == 4 && xmlhttprequestcheckconf.status == 200) {
						var check_conf = null;
						var LMAforce = taskforce;
						check_conf = xmlhttprequestcheckconf.responseText;
						//alert(checkconf_query);
						//alert(xmlhttprequestcheckconf.responseText);
						var check_ALL_array=check_conf.split("\n");
						var check_time_array=check_ALL_array[0].split("|");
						var Time_array = check_time_array[1].split("UnixTime: ");
						UnixTime = Time_array[1];
						UnixTime = parseInt(UnixTime);
						UnixTimeMS = (UnixTime * 1000);
						t.setTime(UnixTimeMS);
						if ( (callholdstatus == '1') || (agentcallsstatus == '1') || (osdial_agent_disable != 'NOT_ACTIVE') ) {
							var Alogin_array = check_time_array[2].split("Logged-in: ");
							var AGLogiN = Alogin_array[1];
							var CamPCalLs_array = check_time_array[3].split("CampCalls: ");
							var CamPCalLs = CamPCalLs_array[1];
							var DiaLCalLs_array = check_time_array[5].split("DiaLCalls: ");
							var DiaLCalLs = DiaLCalLs_array[1];
							var TimeSync_array = check_time_array[6].split("TimeSync: ");
							var TimeSyncInfo = TimeSync_array[1];
							if (AGLogiN != 'N') {
								document.getElementById("AgentStatusStatus").innerHTML = AGLogiN;
							}
							if (CamPCalLs != 'N') {
								document.getElementById("AgentStatusCalls").innerHTML = CamPCalLs;
							}
							if (DiaLCalLs != 'N') {
								document.getElementById("AgentStatusDiaLs").innerHTML = DiaLCalLs;
							}
							if ( (AGLogiN == 'DEAD_VLA') && ( (osdial_agent_disable == 'LIVE_AGENT') || (osdial_agent_disable == 'ALL') ) ) {
								if (manual_dial_menu==1 || alt_dial_menu==1 || MD_channel_look==1 || VD_live_customer_call==1) {
									MD_channel_look=0;
									document.osdial_form.DispoSelection.value = 'NA';
									dialedcall_send_hangup('NO','YES');
								}
								showDiv('AgenTDisablEBoX');
							}
							if ( (AGLogiN == 'DEAD_EXTERNAL') && ( (osdial_agent_disable == 'EXTERNAL') || (osdial_agent_disable == 'ALL') ) ) {
								if (manual_dial_menu==1 || alt_dial_menu==1 || MD_channel_look==1 || VD_live_customer_call==1) {
									document.osdial_form.DispoSelection.value = 'NA';
									dialedcall_send_hangup('NO','YES');
								}
								showDiv('AgenTDisablEBoX');
							}
							if ( (AGLogiN == 'TIME_SYNC') && (osdial_agent_disable == 'ALL') ) {
								document.getElementById("SysteMDisablEInfo").innerHTML = TimeSyncInfo;
								showDiv('SysteMDisablEBoX');
							}
						}
						var VLAStatuS_array = check_time_array[4].split("Status: ");
						var VLAStatuS = VLAStatuS_array[1];
						if ( (VLAStatuS == 'PAUSED') && (AutoDialWaiting == 1) ) {
							if (PausENotifYCounTer > 10) {
								AutoDial_ReSume_PauSe('VDADpause','NEW_ID');
								PausENotifYCounTer=0;
								alert('Your session has been paused');
							} else {
								PausENotifYCounTer++;
							}
						} else {
							PausENotifYCounTer=0;
						}

						var check_conf_array=check_ALL_array[1].split("|");
						var live_conf_calls = check_conf_array[0];
						var conf_chan_array = check_conf_array[1].split(" ~");
						if ( (conf_channels_xtra_display == 1) || (conf_channels_xtra_display == 0) ) {
							if (live_conf_calls > 0) {
								var loop_ct=0;
								var ARY_ct=0;
								var LMAalter=0;
								var LMAcontent_change=0;
								var LMAcontent_match=0;
								agentphonelive=0;
								var conv_start=-1;
								var live_conf_HTML = "<font face=\"Arial,Helvetica\"><B>LIVE CALLS IN YOUR SESSION:</B></font><BR><TABLE WIDTH=<?=$SDwidth ?>><TR><TD><font class=\"log_title\">#</TD><TD><font class=\"log_title\">REMOTE CHANNEL</TD><TD><font class=\"log_title\">HANGUP</TD><TD><font class=\"log_title\">VOLUME</TD></TR>";
								if ( (LMAcount > live_conf_calls)  || (LMAcount < live_conf_calls) || (LMAforce > 0)) {
									LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
									LMAcount=0;   LMAcontent_change++;
								}
								while (loop_ct < live_conf_calls) {
									loop_ct++;
									loop_s = loop_ct.toString();
									if (loop_s.match(/1$|3$|5$|7$|9$/)) {
										var row_color = '<?=$oddrows?>';
									} else {
										var row_color = '<?=$evenrows?>';
									}
									var conv_ct = (loop_ct + conv_start);
									var channelfieldA = conf_chan_array[conv_ct];
									var regXFcred = new RegExp(flag_string,"g");
									if ( (channelfieldA.match(regXFcred)) && (flag_channels>0) ) {
										var chan_name_color = 'log_text_red';
									} else {
										var chan_name_color = 'log_text';
									}
									if ( (HidEMonitoRSessionS==1) && (channelfieldA.match(/ASTblind/)) ) {
										var hide_channel=1;
									} else if (volumecontrol_active!=1) {
										live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldA + "');return false;\">HANGUP</a></td><td></td></tr>";
									} else {
										live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldA + "');return false;\">HANGUP</a></td><td><a href=\"#\" onclick=\"volume_control('UP','" + channelfieldA + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_up.gif\" BORDER=0></a> &nbsp; <a href=\"#\" onclick=\"volume_control('DOWN','" + channelfieldA + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_down.gif\" BORDER=0></a> &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"volume_control('MUTING','" + channelfieldA + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_MUTE.gif\" BORDER=0></a> &nbsp; <a href=\"#\" onclick=\"volume_control('UNMUTE','" + channelfieldA + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_UNMUTE.gif\" BORDER=0></a></td></tr>";
									}
									//var debugspan = document.getElementById("debugbottomspan").innerHTML;

									if (channelfieldA == lastcustchannel) {
										custchannellive++;
									} else {
										if(customerparked == 1) {
											custchannellive++;
										}
										// allow for no customer hungup errors if call from another server
										if(server_ip == lastcustserverip) {
											var nothing='';
										} else {
											custchannellive++;
										}
									}

									if (volumecontrol_active > 0) {
										if ( (protocol != 'EXTERNAL') && (protocol != 'Local')) {
											var regAGNTchan = new RegExp(protocol + '/' + extension,"g");
											if  ( (channelfieldA.match(regAGNTchan)) && (agentchannel != channelfieldA) ) {
												agentchannel = channelfieldA;

												document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_MUTE.gif\" BORDER=0></a>";
											}
										} else {
											if (agentchannel.length < 3) {
												agentchannel = channelfieldA;

												document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_MUTE.gif\" BORDER=0></a>";
											}
										}
									}

									//document.getElementById("debugbottomspan").innerHTML = debugspan + '<BR>' + channelfieldA + '|' + lastcustchannel + '|' + custchannellive + '|' + LMAcontent_change + '|' + LMAalter;

									if (!LMAe[ARY_ct]) {
										LMAe[ARY_ct] = channelfieldA;
										LMAcontent_change++;
										LMAalter++;
									} else if (LMAe[ARY_ct].length < 1) {
										LMAe[ARY_ct] = channelfieldA;
										LMAcontent_change++;
										LMAalter++;
									} else if (LMAe[ARY_ct] == channelfieldA) {
										LMAcontent_match++;
									} else {
										LMAe[ARY_ct] = channelfieldA;
										LMAcontent_change++;
									}
									if (LMAalter > 0) {
										LMAcount++;
									}
									if (agentchannel==channelfieldA){agentphonelive++;}
									ARY_ct++;
								}
								//var debug_LMA = LMAcontent_match+"|"+LMAcontent_change+"|"+LMAcount+"|"+live_conf_calls+"|"+LMAe[0]+LMAe[1]+LMAe[2]+LMAe[3]+LMAe[4]+LMAe[5];
								//document.getElementById("confdebug").innerHTML = debug_LMA + "<BR>";

								if (agentphonelive < 1) { agentchannel=''; }

								live_conf_HTML = live_conf_HTML + "</table>";

								if (LMAcontent_change > 0) {
									if (conf_channels_xtra_display == 1) {
										document.getElementById("outboundcallsspan").innerHTML = live_conf_HTML;
									}
								}
								nochannelinsession=0;
							} else {
								LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
								LMAcount=0;
								if (conf_channels_xtra_display == 1) {
									if (document.getElementById("outboundcallsspan").innerHTML.length > 2) {
										document.getElementById("outboundcallsspan").innerHTML = '';
									}
								}
								custchannellive = -99;
								nochannelinsession++;
							}
						}
						xmlhttprequestcheckconf = undefined; 
						delete xmlhttprequestcheckconf;						
					}
				}
			}
		}
	}

// ################################################################################
// Send MonitorConf/StopMonitorConf command for recording of conferences
	function conf_send_recording(taskconfrectype,taskconfrec,taskconffile) {
		debug("<b>check_send_recording:</b> taskconfrectype=" + taskconfrectype + " taskconfrec=" + taskconfrec + " taskconffile=" + taskconffile,2);
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			if (taskconfrectype == 'MonitorConf') {
				//var campaign_recording = '<? echo $campaign_recording ?>';
				//var campaign_rec_filename = '<? echo $campaign_rec_filename ?>';
				//CAMPAIGN CUSTPHONE FULLDATE TINYDATE EPOCH AGENT
				var REGrecCAMPAIGN = new RegExp("CAMPAIGN","g");
				var REGrecCUSTPHONE = new RegExp("CUSTPHONE","g");
				var REGrecFULLDATE = new RegExp("FULLDATE","g");
				var REGrecTINYDATE = new RegExp("TINYDATE","g");
				var REGrecEPOCH = new RegExp("EPOCH","g");
				var REGrecAGENT = new RegExp("AGENT","g");
				filename = campaign_rec_filename;
				filename = filename.replace(REGrecCAMPAIGN, campaign);
				filename = filename.replace(REGrecCUSTPHONE, lead_dial_number);
				filename = filename.replace(REGrecFULLDATE, filedate);
				filename = filename.replace(REGrecTINYDATE, tinydate);
				filename = filename.replace(REGrecEPOCH, epoch_sec);
				filename = filename.replace(REGrecAGENT, user);
				//filename = filedate + "_" + user_abb;
				var query_recording_exten = recording_exten;
				var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
				var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('StopMonitorConf','" + taskconfrec + "','" + filename + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_stoprecording.gif\" border=0 alt=\"Stop Recording\"></a>";

				if (campaign_recording == 'ALLFORCE') {
					document.getElementById("RecorDControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_startrecording_OFF.gif\" border=0 alt=\"Start Recording\">";
				} else {
					document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;
				}
			}
			if (taskconfrectype == 'StopMonitorConf') {
				filename = taskconffile;
				var query_recording_exten = session_id;
				var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
				var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + taskconfrec + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_startrecording.gif\" border=0 alt=\"Start Recording\"></a>";
				if (campaign_recording == 'ALLFORCE') {
					document.getElementById("RecorDControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_startrecording_OFF.gif\" border=0 alt=\"Start Recording\">";
				} else {
					document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;
				}
			}
			confmonitor_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + taskconfrectype + "&format=text&channel=" + channelrec + "&filename=" + filename + "&exten=" + query_recording_exten + "&ext_context=" + ext_context + "&lead_id=" + document.osdial_form.lead_id.value + "&ext_priority=1&CalLCID=" + CalLCID;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(confmonitor_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var RClookResponse = null;
					//document.getElementById("busycallsdebug").innerHTML = confmonitor_query;
					//alert(xmlhttp.responseText);
					RClookResponse = xmlhttp.responseText;
					var RClookResponse_array=RClookResponse.split("\n");
					var RClookFILE = RClookResponse_array[1];
					var RClookID = RClookResponse_array[2];
					var RClookFILE_array = RClookFILE.split("Filename: ");
					var RClookID_array = RClookID.split("RecorDing_ID: ");
					if (RClookID_array.length > 0) {
						var RecDispNamE = RClookFILE_array[1];
						if (RecDispNamE.length > 30) {
							RecDispNamE = RecDispNamE.substr(0,30);
							RecDispNamE = RecDispNamE + '...';
						} 
						document.getElementById("RecorDingFilename").innerHTML = RecDispNamE;
						document.getElementById("RecorDID").innerHTML = RClookID_array[1];
						recording_id = RClookID_array[1];
					}
				}
			}
			delete xmlhttp;
		}
	}

// ################################################################################
// Send Redirect command for live call to Manager sends phone name where call is going to
// Covers the following types: XFER, VMAIL, ENTRY, CONF, PARK, FROMPARK, XfeRLOCAL, XfeRINTERNAL, XfeRBLIND, VfeRVMAIL
	function mainxfer_send_redirect(taskvar,taskxferconf,taskserverip,taskdebugnote) {
		debug("<b>mainxfer_send_redirect:</b> taskvar=" + taskvar + " taskxferconf=" + taskxferconf + " taskserverip=" + taskserverip + " taskdebugnote=" + taskdebugnote,2);
		if (auto_dial_level == 0) {
			RedirecTxFEr = 1;
		}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			var redirectvalue = MDchannel;
			var redirectserverip = lastcustserverip;
			if (redirectvalue.length < 2) {
				redirectvalue = lastcustchannel
			}
			if ( (taskvar == 'XfeRBLIND') || (taskvar == 'XfeRVMAIL') ) {
				var queryCID = "XBvdcW" + epoch_sec + user_abb;
				var blindxferdialstring = document.osdial_form.xfernumber.value;
				var regXFvars = new RegExp("XFER","g");
				if (blindxferdialstring.match(regXFvars)) {
					var regAXFvars = new RegExp("AXFER","g");
					if (blindxferdialstring.match(regAXFvars)) {
						var Ctasknum = blindxferdialstring.replace(regAXFvars, '');
						if (Ctasknum.length < 2) {
							Ctasknum = '83009';
						}
						var closerxfercamptail = '_L';
						if (closerxfercamptail.length < 3) {
							closerxfercamptail = 'IVR';
						}
						blindxferdialstring = Ctasknum + '*' + document.osdial_form.phone_number.value + '*' + document.osdial_form.lead_id.value + '*' + campaign + '*' + closerxfercamptail + '*' + user + '*';
					}
				} else if (document.osdial_form.xferoverride.checked==false) {
					if (blindxferdialstring.length=='11') {
						blindxferdialstring = dial_prefix + "" + blindxferdialstring;
					} else if (blindxferdialstring.length=='10') {
						blindxferdialstring = dial_prefix + "1" + blindxferdialstring;
					} else if (blindxferdialstring.length=='7') {
						blindxferdialstring = dial_prefix + ""  + blindxferdialstring;
					}
				}
				if (taskvar == 'XfeRVMAIL') {
					var blindxferdialstring = campaign_am_message_exten;
				}
				if (blindxferdialstring.length<'2') {
					xferredirect_query='';
					taskvar = 'NOTHING';
					alert("Transfer number must have more than 1 digit:" + blindxferdialstring);
				} else {
					xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + blindxferdialstring + "&ext_context=" + ext_context + "&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + campaign + "&uniqueid=" + document.osdial_form.uniqueid.value + "&lead_id=" + document.osdial_form.lead_id.value + "&secondS=" + VD_live_call_secondS + "&session_id=" + session_id;
				}
			}
			if (taskvar == 'XfeRINTERNAL') {
				var closerxferinternal = '';
				taskvar = 'XfeRLOCAL';
			} else {
				var closerxferinternal = '9';
			}
			if (taskvar == 'XfeRLOCAL') {
				CustomerData_update();
				var XfeRSelecT = document.getElementById("XfeRGrouP");
				var queryCID = "XLvdcW" + epoch_sec + user_abb;
				//"90009*$group**$lead_id**$phone_number*$user*";
				var redirectdestination = closerxferinternal + '90009*' + XfeRSelecT.value + '**' + document.osdial_form.lead_id.value + '**' + document.osdial_form.phone_number.value + '*' + user + '*';

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + campaign + "&uniqueid=" + document.osdial_form.uniqueid.value + "&lead_id=" + document.osdial_form.lead_id.value + "&secondS=" + VD_live_call_secondS + "&session_id=" + session_id;
			}
			if (taskvar == 'XfeR') {
				var queryCID = "LRvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.osdial_form.extension_xfer.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectName&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&extenName=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
			}
			if (taskvar == 'VMAIL') {
				var queryCID = "LVvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.osdial_form.extension_xfer.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectNameVmail&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + voicemail_dump_exten + "&extenName=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
			}
			if (taskvar == 'ENTRY') {
				var queryCID = "LEvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.osdial_form.extension_xfer_entry.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Redirect&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
			}
			if (taskvar == '3WAY') {
				xferredirect_query='';

				var queryCID = "VXvdcW" + epoch_sec + user_abb;
				var redirectdestination = "NEXTAVAILABLE";
				var redirectXTRAvalue = XDchannel;
				var redirecttype_test = document.osdial_form.xfernumber.value;
				var XfeRSelecT = document.getElementById("XfeRGrouP");
				var regRXFvars = new RegExp("CXFER","g");
				if ( (redirecttype_test.match(regRXFvars)) && (local_consult_xfers > 0) ) {
					var redirecttype = 'RedirectXtraCX';
				} else {
					var redirecttype = 'RedirectXtra';
				}
				DispO3waychannel = redirectvalue;
				DispO3wayXtrAchannel = redirectXTRAvalue;
				DispO3wayCalLserverip = redirectserverip;
				DispO3wayCalLxfernumber = document.osdial_form.xfernumber.value;
				DispO3wayCalLcamptail = '';


				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + redirecttype + "&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&extrachannel=" + redirectXTRAvalue + "&lead_id=" + document.osdial_form.lead_id.value + "&phone_code=" + document.osdial_form.phone_code.value + "&phone_number=" + document.osdial_form.phone_number.value+ "&filename=" + taskdebugnote + "&campaign=" + XfeRSelecT.value + "&session_id=" + session_id;

				if (taskdebugnote == 'FIRST') {
					document.getElementById("DispoSelectHAspan").innerHTML = "<a href=\"#\" onclick=\"DispoLeavE3wayAgaiN()\">Leave 3Way Call Again</a>";
				}
			}
			if (taskvar == 'ParK') {
				var queryCID = "LPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;
				var parkedby = protocol + "/" + extension;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectToPark&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + park_on_extension + "&ext_context=" + ext_context + "&ext_priority=1&extenName=park&parkedby=" + parkedby + "&session_id=" + session_id;

				document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('FROMParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_grabparkedcall.gif\" border=0 alt=\"Grab Parked Call\"></a>";
				customerparked=1;
			}
			if (taskvar == 'FROMParK') {
				var queryCID = "FPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;

				if( (server_ip == taskserverip) && (taskserverip.length > 6) ) {
					var dest_dialstring = session_id;
				} else {
					if(taskserverip.length > 6) {
						var dest_dialstring = server_ip_dialstring + "" + session_id;
					} else {
						var dest_dialstring = session_id;
					}
				}

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectFromPark&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + dest_dialstring + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;

				document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_parkcall.gif\" border=0 alt=\"Park Call\"></a>";
				customerparked=0;
			}


			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(xferredirect_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}

		// used to send second Redirect  for manual dial calls
		if (auto_dial_level == 0) {
			RedirecTxFEr = 1;
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
				xmlhttp = new XMLHttpRequest();
			}
			if (xmlhttp) { 
				xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(xferredirect_query + "&stage=2NDXfeR"); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
						//alert(RedirecTxFEr + "|" + xmlhttp.responseText);
					}
				}
				delete xmlhttp;
			}
		}

		if ( (taskvar == 'XfeRLOCAL') || (taskvar == 'XfeRBLIND') || (taskvar == 'XfeRVMAIL') ) {
			if (auto_dial_level == 0) {
				RedirecTxFEr = 1;
			}
			//document.osdial_form.callchannel.value = '';
			document.getElementById("callchannel").innerHTML = '';
			document.osdial_form.callserverip.value = '';
			if( document.images ) {
				document.images['livecall'].src = image_livecall_OFF.src;
			}
			//alert(RedirecTxFEr + "|" + auto_dial_level);
                        if (taskvar == 'XfeRVMAIL') {
                                document.osdial_form.DispoSelection.value = 'AM';
                                dialedcall_send_hangup('NO');
                              	alt_dial_active=0;
                                reselect_alt_dial=0;
                                DispoSelect_submit();
                        } else {
                                dialedcall_send_hangup();
                        }
		}
	}

// ################################################################################
// Finish the alternate dialing and move on to disposition the call
	function ManualDialAltDonE() {
		debug("<b>ManualDialAltDonE:</b>",2);
		alt_phone_dialing=starting_alt_phone_dialing;
		alt_dial_active = 0;
		open_dispo_screen=1;
		document.getElementById("MainStatuSSpan").innerHTML = "Dial Next Number";
	}

// ################################################################################
// Insert or update the osdial_log entry for a customer call
	function DialLog(taskMDstage) {
		debug("<b>DialLog:</b> taskMDstage=" + taskMDstage,2);
		if (taskMDstage == "start") {
			var MDlogEPOCH = 0;
            var UID_test = document.osdial_form.uniqueid.value;
            if (UID_test.length < 4)
                {
                UID_test = epoch_sec + '.' + random;
                document.osdial_form.uniqueid.value = UID_test;
                }
		} else if (alt_phone_dialing == 1) {
			if (document.osdial_form.DiaLAltPhonE.checked==true) {
				reselect_alt_dial = 1;
				alt_dial_active = 1;
				alt_dial_menu = 1;
				var man_status = "Dial Alt Phone Number: <a href=\"#\" id=\"mainphonelink\" onclick=\"document.getElementById('mainphonelink').setAttribute('onclick','void(0);'); alt_dial_menu=0; ManualDialOnly('MaiNPhonE');\"><font class=\"preview_text\">MAIN PHONE</font></a> or <a href=\"#\" id=\"altphonelink\" onclick=\"document.getElementById('altphonelink').setAttribute('onclick','void(0);'); alt_dial_menu=0; ManualDialOnly('ALTPhoneE');\"><font class=\"preview_text\">ALT PHONE</font></a> or <a href=\"#\" id=\"address3link\" onclick=\"document.getElementById('address3link').setAttribute('onclick','void(0);'); alt_dial_menu=0; ManualDialOnly('AddresS3');\"><font class=\"preview_text\">ADDRESS3</font></a> or <a href=\"#\" id=\"finishleadlink\" onclick=\"document.getElementById('finishleadlink').setAttribute('onclick','void(0);'); alt_dial_menu=0; ManualDialAltDonE();\"><font class=\"preview_text_red\" style=color:<?=$status_preview_color?>>FINISH LEAD</font></a>"; 
				document.getElementById("MainStatuSSpan").innerHTML = man_status;
			}
		}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			manDiaLlog_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLlogCaLL&stage=" + taskMDstage + "&uniqueid=" + document.osdial_form.uniqueid.value + 
			"&user=" + user + "&pass=" + pass + "&campaign=" + campaign + 
			"&lead_id=" + document.osdial_form.lead_id.value + 
			"&list_id=" + document.osdial_form.list_id.value + 
			"&length_in_sec=0&phone_code=" + document.osdial_form.phone_code.value + 
			"&phone_number=" + lead_dial_number + 
			"&exten=" + extension + "&channel=" + lastcustchannel + "&start_epoch=" + MDlogEPOCH + "&auto_dial_level=" + auto_dial_level + "&VDstop_rec_after_each_call=" + VDstop_rec_after_each_call + "&conf_silent_prefix=" + conf_silent_prefix + "&protocol=" + protocol + "&extension=" + extension + "&ext_context=" + ext_context + "&conf_exten=" + session_id + "&user_abb=" + user_abb + "&agent_log_id=" + agent_log_id + "&MDnextCID=" + LasTCID + "&DB=0";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			//document.getElementById("busycallsdebug").innerHTML = "vdc_db_query.php?" + manDiaLlog_query;
			xmlhttp.send(manDiaLlog_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var MDlogResponse = null;
					//alert(xmlhttp.responseText);
					MDlogResponse = xmlhttp.responseText;
					var MDlogResponse_array=MDlogResponse.split("\n");
					MDlogLINE = MDlogResponse_array[0];
					if ( (MDlogLINE == "LOG NOT ENTERED") && (VDstop_rec_after_each_call != 1) ) {
						//alert("error: log not entered\n");
					} else {
						MDlogEPOCH = MDlogResponse_array[1];
						//alert("OSDIAL Call log entered:\n" + document.osdial_form.uniqueid.value);
						if ( (taskMDstage != "start") && (VDstop_rec_after_each_call == 1) ) {
							var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + session_id + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_startrecording.gif\" border=0 alt=\"Start Recording\"></a>";
							if ( (campaign_recording == 'NEVER') || (campaign_recording == 'ALLFORCE') ) {
								document.getElementById("RecorDControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_startrecording_OFF.gif\" border=0 alt=\"Start Recording\">";
							} else {
								document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;
							}
							
							MDlogRecorDings = MDlogResponse_array[3];
							if (window.MDlogRecorDings) {
								var MDlogRecorDings_array=MDlogRecorDings.split("|");
								var RecDispNamE = MDlogRecorDings_array[2];
								if (RecDispNamE.length > 25) {
									RecDispNamE = RecDispNamE.substr(0,22);
									RecDispNamE = RecDispNamE + '...';
								}
								document.getElementById("RecorDingFilename").innerHTML = RecDispNamE;
								document.getElementById("RecorDID").innerHTML = MDlogRecorDings_array[3];
								recording_id = MDlogRecorDings_array[3];
							}
						}
					}
				}
			}
			delete xmlhttp;
		}
		RedirecTxFEr=0;
	}


// ################################################################################
// Request number of USERONLY callbacks for this agent
	function CalLBacKsCounTCheck() {
		debug("<b>CalLBacKsCounTCheck:</b>",4);
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			CBcount_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=CalLBacKCounT&campaign=" + campaign + "&format=text";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(CBcount_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(xmlhttp.responseText);
					var CBcounT = xmlhttp.responseText;
					var CBstatusHTML = "<a href=\"#\" onclick=\"CalLBacKsLisTCheck();return false;\">";
					if (CBcounT == 0) {
						CBstatusHTML += "NO ACTIVE CALLBACKS";
					} else {
						CBstatusHTML += "<font color=\"#FFFF00\">" + CBcounT + " ACTIVE CALLBACKS</font>";
					}
					CBstatusHTML += "</a>";
					document.getElementById("CBstatusSpan").innerHTML = CBstatusHTML;
				}
			}
			delete xmlhttp;
		}
	}


// ################################################################################
// Request list of USERONLY callbacks for this agent
	function CalLBacKsLisTCheck() {
		debug("<b>CalLBacKsLisTCheck:</b>",2);
		if ( (VD_live_customer_call==1) || (alt_dial_active==1) ) {
			alert("You must hangup and disposition your active call\nbefore you can place a call to a callback. ");
		} else {
			if (AutoDialWaiting==1 && VD_live_customer_call==0 && alt_dial_active==0) {
				AutoDial_ReSume_PauSe('VDADpause','NEW_ID');
				PCSpause=1;
			}
			showDiv('CallBacKsLisTBox');

			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
				xmlhttp = new XMLHttpRequest();
			}
			if (xmlhttp) { 
				var CBlist_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=CalLBacKLisT&campaign=" + campaign + "&format=text";
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(CBlist_query); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						//alert(xmlhttp.responseText);
						var all_CBs = null;
						all_CBs = xmlhttp.responseText;
						var all_CBs_array=all_CBs.split("\n");
						var CB_calls = all_CBs_array[0];
						var loop_ct=0;
						var conv_start=0;
						var CB_HTML = "<table width=900><tr bgcolor=<?=$callback_bg2?>><td><font class=\"log_title\">#</td><td align=\"center\"><font class=\"log_title\"> CALLBACK DATE/TIME</td><td align=\"center\"><font class=\"log_title\">NUMBER</td><td align=\"center\"><font class=\"log_title\">NAME</td><td align=\"center\"><font class=\"log_title\"> STATUS</td><td align=\"center\"><font class=\"log_title\">CAMPAIGN</td><td align=\"center\"><font class=\"log_title\">LAST CALL DATE/TIME</td><td align=\"left\"><font class=\"log_title\"> COMMENTS</td></tr>";
						while (loop_ct < CB_calls) {
							loop_ct++;
							loop_s = loop_ct.toString();
							if (loop_s.match(/1$|3$|5$|7$|9$/)) {
								var row_color = '<?=$oddrows?>';
							} else {
								var row_color = '<?=$evenrows?>';
							}
							var conv_ct = (loop_ct + conv_start);
							var call_array = all_CBs_array[conv_ct].split(" ~");
							var CB_name = call_array[0] + " " + call_array[1];
							var CB_phone = call_array[2];
							var CB_id = call_array[3];
							var CB_lead_id = call_array[4];
							var CB_campaign = call_array[5];
							var CB_status = call_array[6];
							var CB_lastcall_time = call_array[7];
							var CB_callback_time = call_array[8];
							var CB_comments = call_array[9];
							CB_HTML = CB_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"log_text\">" + CB_callback_time + "</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"new_callback_call('" + CB_id + "','" + CB_lead_id + "');return false;\">" + CB_phone + "</a></td><td><font class=\"log_text\">" + CB_name + "</td><td><font class=\"log_text\">" + CB_status + "</td><td><font class=\"log_text\">" + CB_campaign + "</td><td align=right><font class=\"log_text\">" + CB_lastcall_time + "&nbsp;</td><td align=right><font class=\"log_text\">" + CB_comments + "&nbsp;</td></tr>";
					
						}
						CB_HTML = CB_HTML + "</table>";
						document.getElementById("CallBacKsLisT").innerHTML = CB_HTML;
					}
				}
				delete xmlhttp;
			}
		}
	}


// ################################################################################
// Open up a callback customer record as manual dial preview mode
	function new_callback_call(taskCBid,taskLEADid) {
		debug("<b>new_callback_call:</b> taskCBid=" + taskCBid + " taskLEADid=" + taskLEADid,2);
		alt_phone_dialing=1;
		auto_dial_level=0;
		manual_dial_in_progress=1;
		MainPanelToFront();
		buildDiv('DiaLLeaDPrevieW');
		buildDiv('DiaLDiaLAltPhonE');
		document.osdial_form.LeadPreview.checked=true;
		document.osdial_form.DiaLAltPhonE.checked=true;
		hideDiv('CallBacKsLisTBox');
		ManualDialNext(taskCBid,taskLEADid,'','','');
	}


// ################################################################################
// Finish Callback and go back to original screen
	function manual_dial_finished() {
		debug("<b>manual_dial_finished:</b>",2);
		alt_phone_dialing=starting_alt_phone_dialing;
		auto_dial_level=starting_dial_level;
		MainPanelToFront();
		CalLBacKsCounTCheck();
		manual_dial_in_progress=0;
		dial_timedout=0;
	}


// ################################################################################
// Open page to enter details for a new manual dial lead
	function NeWManuaLDiaLCalL(TVfast) {
		debug("<b>NeWManuaLDiaLCalL:</b> TVfast=" + TVfast,2);
		dial_timedout=0;
		if ( (VD_live_customer_call==1) || (alt_dial_active==1) ) {
			alert("You must hangup and disposition your active call\nbefore you can place a call to a manually entered number. ");
		} else {
			if (AutoDialWaiting==1 && VD_live_customer_call==0 && alt_dial_active==0) {
				AutoDial_ReSume_PauSe('VDADpause','NEW_ID');
				PCSpause=1;
			}
			if (TVfast=='FAST') {
				NeWManuaLDiaLCalLSubmiTfast();
			} else {
				showDiv('NeWManuaLDiaLBox');
			}
		}
	}


// ################################################################################
// Insert the new manual dial as a lead and go to manual dial screen
	function NeWManuaLDiaLCalLSubmiT() {
		debug("<b>NeWManuaLDiaLCalLSubmiT:</b>",2);
		dial_timedout=0;
		hideDiv('NeWManuaLDiaLBox');
		var MDDiaLCodEform = document.osdial_form.MDDiaLCodE.value;
		var MDPhonENumbeRform = document.osdial_form.MDPhonENumbeR.value;
		var MDDiaLOverridEform = document.osdial_form.MDDiaLOverridE.value;
		var MDLookuPLeaD = 'new';
		if (document.osdial_form.LeadLookuP.checked==true) {
			MDLookuPLeaD = 'lookup';
		}

		if (MDDiaLOverridEform.length > 0) {
			basic_originate_call(session_id,'NO','YES',MDDiaLOverridEform,'YES');
		} else {
			alt_phone_dialing=1;
			auto_dial_level=0;
			manual_dial_in_progress=1;
			MainPanelToFront();
			buildDiv('DiaLLeaDPrevieW');
			buildDiv('DiaLDiaLAltPhonE');
			document.osdial_form.LeadPreview.checked=true;
			document.osdial_form.DiaLAltPhonE.checked=true;
			ManualDialNext("","",MDDiaLCodEform,MDPhonENumbeRform,MDLookuPLeaD);
		}

		document.osdial_form.MDPhonENumbeR.value = '';
		document.osdial_form.MDDiaLOverridE.value = '';
	}

// ################################################################################
// Fast version of manual dial
	function NeWManuaLDiaLCalLSubmiTfast() {
		debug("<b>NeWManuaLDiaLCalLSubmiTfast:</b>",2);
		dial_timedout=0;
		if ( document.osdial_form.phone_code.value.length < 1  ) {
			document.osdial_form.phone_code.value = "1";
		}
		var MDDiaLCodEform = document.osdial_form.phone_code.value;
		var MDPhonENumbeRform = document.osdial_form.phone_number.value;

		if ( (MDDiaLCodEform.length < 1) || (MDPhonENumbeRform.length < 5) ) {
			alert("You must enter a number in the \"Phone\" field fast dial. \nThe \"CountryCode\" will default to \"1\".");
		} else {
			var MDLookuPLeaD = 'new';
			if (document.osdial_form.LeadLookuP.checked==true) {
				MDLookuPLeaD = 'lookup';
			}
		
			alt_phone_dialing=1;
			auto_dial_level=0;
			manual_dial_in_progress=1;
			MainPanelToFront();
			buildDiv('DiaLLeaDPrevieW');
			buildDiv('DiaLDiaLAltPhonE');
			document.osdial_form.LeadPreview.checked=false;
			document.osdial_form.DiaLAltPhonE.checked=true;
			ManualDialNext("","",MDDiaLCodEform,MDPhonENumbeRform,MDLookuPLeaD);
		}
	}

// ################################################################################
// Request lookup of manual dial channel
	function ManualDialCheckChanneL(taskCheckOR) {
		debug("<b>ManualDialCheckChanneL:</b>",4);
		if (taskCheckOR == 'YES') {
			var CIDcheck = XDnextCID;
		} else {
			var CIDcheck = MDnextCID;
		}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			manDiaLlook_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLlookCaLL&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&MDnextCID=" + CIDcheck + "&agent_log_id=" + agent_log_id + "&lead_id=" + document.osdial_form.lead_id.value;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLlook_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var MDlookResponse = null;
					//alert(xmlhttp.responseText);
					MDlookResponse = xmlhttp.responseText;
					var MDlookResponse_array=MDlookResponse.split("\n");
					var MDlookCID = MDlookResponse_array[0];
					if (MDlookCID == "NO") {
						if (dial_timedout == 0) {
							MD_ring_secondS++;
							var dispnum = lead_dial_number;
							var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);

							document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
							document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=<?=$status_bg?>>UID: " + CIDcheck + "</font><font color=<?=$status_intense_color?> style='text-decoration:blink;'><b>Waiting for Ring... " + MD_ring_secondS + " seconds<b></font>";
							//alert("channel not found yet:\n" + campaign);
						}
					} else {
						var regMDL = new RegExp("^Local","ig");
						if (taskCheckOR == 'YES') {
							XDuniqueid = MDlookResponse_array[0];
							XDchannel = MDlookResponse_array[1];
							if ( (XDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') && (MD_ring_secondS < 10) ) {
								// bad grab of Local channel, try again
								MD_ring_secondS++;
								var dispnum = lead_dial_number;
								var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
								document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
								document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=<?=$status_bg?>>UID: " + CIDcheck + "</font><font color=<?=$status_intense_color?> style='text-decoration:blink;'><b>Waiting for Ring... " + MD_ring_secondS + " seconds<b></font>";
							} else {
								document.osdial_form.xferuniqueid.value	= MDlookResponse_array[0];
								document.osdial_form.xferchannel.value	= MDlookResponse_array[1];
								lastxferchannel = MDlookResponse_array[1];
								document.osdial_form.xferlength.value		= 0;

								XD_live_customer_call = 1;
								XD_live_call_secondS = 0;
								MD_channel_look=0;

								document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
								document.getElementById("MainStatuSSpan").innerHTML = " Called 3rd party: " + document.osdial_form.xfernumber.value + "&nbsp;&nbsp;<font color=<?=$status_bg?>>UID: " + CIDcheck;

								document.getElementById("Leave3WayCall").innerHTML ="<a href=\"#\" onclick=\"leave_3way_call('FIRST');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_leave3waycall.gif\" border=0 alt=\"LEAVE 3-WAY CALL\"></a>";

								document.getElementById("DialWithCustomer").innerHTML ="<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_dialwithcustomer_OFF.gif\" border=0 alt=\"Dial With Customer\">";

								document.getElementById("ParkCustomerDial").innerHTML ="<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_parkcustomerdial_OFF.gif\" border=0 alt=\"Park Customer Dial\">";

								document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"xfercall_send_hangup();return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_hangupxferline.gif\" border=0 alt=\"Hangup Xfer Line\"></a>";

								document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_hangupbothlines.gif\" border=0 alt=\"Hangup Both Lines\"></a>";

								xferchannellive=1;
								XDcheck = '';
							}
						} else {
							MDuniqueid = MDlookResponse_array[0];
							MDchannel = MDlookResponse_array[1];
							if ( (MDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') ) {
								// bad grab of Local channel, try again
								MD_ring_secondS++;
								var dispnum = lead_dial_number;
								var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);

								document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
								document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=<?=$status_bg?>>UID: " + CIDcheck + "</font><font color=<?=$status_intense_color?> style='text-decoration:blink;'><b>Waiting for Ring... " + MD_ring_secondS + " seconds<b></font>";
							} else {
								custchannellive=1;

								document.osdial_form.uniqueid.value		= MDlookResponse_array[0];
								//document.osdial_form.callchannel.value	= MDlookResponse_array[1];
								document.getElementById("callchannel").innerHTML = MDlookResponse_array[1];
								lastcustchannel = MDlookResponse_array[1];
								if( document.images ) {
									document.images['livecall'].src = image_livecall_ON.src;
								}
								document.osdial_form.SecondS.value		= 0;

								VD_live_customer_call = 1;
								VD_live_call_secondS = 0;

								MD_channel_look=0;
								var dispnum = lead_dial_number;
								var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
								document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';

								document.getElementById("MainStatuSSpan").innerHTML = " Called " + status_display_number + "&nbsp;&nbsp;&nbsp;&nbsp;<font color=<?=$status_bg?>>UID: " + CIDcheck + " &nbsp;</font>"; 

								document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_parkcall.gif\" border=0 alt=\"Park Call\"></a>";

								document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer.gif\" border=0 alt=\"Hangup Customer\"></a>";

								document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_transferconf.gif\" border=0 alt=\"Transfer - Conference\"></a>";

								document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_localcloser.gif\" border=0 alt=\"LOCAL CLOSER\"></a>";

								document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_blindtransfer.gif\" border=0 alt=\"Dial Blind Transfer\"></a>";

								document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_ammessage.gif\" border=0 alt=\"Blind Transfer VMail Message\"></a>";
								document.getElementById("DialBlindVMail2").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_ammessage.gif\" border=0 alt=\"Blind Transfer VMail Message\"></a>";

								document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + MDchannel + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_up.gif\" BORDER=0></a>";
								document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + MDchannel + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_down.gif\" BORDER=0></a>";

								document.getElementById("DTMFDialPad0").innerHTML = "<a href=\"#\" alt=\"0\" onclick=\"document.osdial_form.conf_dtmf.value='0'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_0.png\" border=0></a>";
								document.getElementById("DTMFDialPad1").innerHTML = "<a href=\"#\" alt=\"1\" onclick=\"document.osdial_form.conf_dtmf.value='1'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_1.png\" border=0></a>";
								document.getElementById("DTMFDialPad2").innerHTML = "<a href=\"#\" alt=\"2 - ABC\" onclick=\"document.osdial_form.conf_dtmf.value='2'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_2.png\" border=0></a>";
								document.getElementById("DTMFDialPad3").innerHTML = "<a href=\"#\" alt=\"3 - DEF\" onclick=\"document.osdial_form.conf_dtmf.value='3'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_3.png\" border=0></a>";
								document.getElementById("DTMFDialPad4").innerHTML = "<a href=\"#\" alt=\"4 - GHI\" onclick=\"document.osdial_form.conf_dtmf.value='4'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_4.png\" border=0></a>";
								document.getElementById("DTMFDialPad5").innerHTML = "<a href=\"#\" alt=\"5 - JKL\" onclick=\"document.osdial_form.conf_dtmf.value='5'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_5.png\" border=0></a>";
								document.getElementById("DTMFDialPad6").innerHTML = "<a href=\"#\" alt=\"6 - MNO\" onclick=\"document.osdial_form.conf_dtmf.value='6'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_6.png\" border=0></a>";
								document.getElementById("DTMFDialPad7").innerHTML = "<a href=\"#\" alt=\"7 - PQRS\" onclick=\"document.osdial_form.conf_dtmf.value='7'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_7.png\" border=0></a>";
								document.getElementById("DTMFDialPad8").innerHTML = "<a href=\"#\" alt=\"8 - TUV\" onclick=\"document.osdial_form.conf_dtmf.value='8'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_8.png\" border=0></a>";
								document.getElementById("DTMFDialPad9").innerHTML = "<a href=\"#\" alt=\"9 - WXYZ\" onclick=\"document.osdial_form.conf_dtmf.value='9'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_9.png\" border=0></a>";
								document.getElementById("DTMFDialPadStar").innerHTML = "<a href=\"#\" alt=\"*\" onclick=\"document.osdial_form.conf_dtmf.value='*'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_star.png\" border=0></a>";
								document.getElementById("DTMFDialPadHash").innerHTML = "<a href=\"#\" alt=\"#\" onclick=\"document.osdial_form.conf_dtmf.value='#'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_hash.png\" border=0></a>";


								// INSERT OSDIAL_LOG ENTRY FOR THIS CALL PROCESS
								DialLog("start");

								custchannellive=1;
							}
						}
					}
				}
			}
			delete xmlhttp;
		}

		if (MD_ring_secondS > 49) {
			MD_channel_look=0;
			MD_ring_secondS=0;
			dial_timedout = 1;
			//alert("Dial timed out, contact your system administrator\n");
			//alert("Dial timed out, click Hangup and try again or dial next number.\n");
			var dispnum = lead_dial_number;
			var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
			document.getElementById("MainStatuSSpan").innerHTML = " Attempted: " + status_display_number + "&nbsp;&nbsp;<font color=<?=$status_alert_color?> style='text-decoration:blink;'><b>Dial timed out, click Hangup and try again or dial next number.<b></font>";
		}
	}

// ################################################################################
// Send the Manual Dial Next Number request
	function ManualDialNext(mdnCBid,mdnBDleadid,mdnDiaLCodE,mdnPhonENumbeR,mdnStagE) {
		debug("<b>ManualDialNext:</b> mdnCBid=" + mdnCBid + " mdnBDleadid=" + mdnBDleadid + " mdnDiaLCodE=" + mdnDiaLCodE + " mdnPhonENumbeR=" + mdnPhonENumbeR + " mdnStagE=" + mdnStagE,2);
		dial_timedout=0;
		if (previewFD_time > 0) {
			clearTimeout(previewFD_timeout_id);
			clearInterval(previewFD_display_id);
			document.getElementById("PreviewFDTimeSpan").innerHTML = "";
		}
		all_record = 'NO';
		all_record_count=0;
		if (inbound_man > 0) {
			auto_dial_level=0;

			if (AutoDialReady==0)
				document.osdial_form.DispoSelectStop.checked=true;

			AutoDial_ReSume_PauSe('VDADpause','NEW_ID');

			document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\" Pause \"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\"><BR><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
		} else {
			document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
		}
		if (document.osdial_form.LeadPreview.checked==true) {
			reselect_preview_dial = 1;
			var man_preview = 'YES';
			var man_status = "&nbsp;&nbsp;&nbsp;&nbsp;<font style='text-decoration: blink;color:<?=$status_intense_color?>;'>Preview the Lead then <a href=\"#\" id=\"dialleadlink\" onclick=\"document.getElementById('dialleadlink').setAttribute('onclick','void(0);'); manual_dial_menu=0; ManualDialOnly();\"><font class=\"preview_text\" color=<?=$status_preview_color?>>DIAL LEAD</font></a><font style='{text-decoration: blink;color:<?=$status_intense_color?>;'>";
			if (manual_dial_allow_skip == 1) {
				man_status = man_status + " or </font><a href=\"#\" id=\"skipleadlink\"onclick=\"document.getElementById('skipleadlink').setAttribute('onclick','void(0);'); manual_dial_menu=0; ManualDialSkip();\"><font class=\"preview_text\" color=<?=$status_preview_color?>>SKIP LEAD</font></a>"; 
			} else {
				man_status = man_status + "</font>";
			}
		} else {
			reselect_preview_dial = 0;
			var man_preview = 'NO';
			var man_status = " Waiting for Ring..."; 
		}

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			lead_cust2_cid = document.osdial_form.custom2.value;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			if (use_custom2_callerid == 'Y' && lead_cust2_cid != '') {
				cid = lead_cust2_cid;
				cid_name = lead_cust2_cid;
			}
			manDiaLnext_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLnextCaLL&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ext_context=" + ext_context + "&dial_timeout=" + dial_timeout + "&dial_prefix=" + dial_prefix + "&campaign_cid=" + cid + "&campaign_cid_name=" + cid_name + "&preview=" + man_preview + "&agent_log_id=" + agent_log_id + "&callback_id=" + mdnCBid + "&lead_id=" + mdnBDleadid + "&phone_code=" + mdnDiaLCodE + "&phone_number=" + mdnPhonENumbeR + "&list_id=" + mdnLisT_id + "&stage=" + mdnStagE  + "&use_internal_dnc=" + use_internal_dnc + "&omit_phone_code=" + omit_phone_code;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLnext_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var MDnextResponse = null;
					//alert(xmlhttp.responseText);
					MDnextResponse = xmlhttp.responseText;

					var MDnextResponse_array=MDnextResponse.split("\n");
					MDnextCID = MDnextResponse_array[0];

					var regMNCvar = new RegExp("HOPPER","ig");
					var regMDFvarDNC = new RegExp("DNC","ig");
					var regMDFvarCAMP = new RegExp("CAMPLISTS","ig");
					if ( (MDnextCID.match(regMNCvar)) || (MDnextCID.match(regMDFvarDNC)) || (MDnextCID.match(regMDFvarCAMP)) ) {
						var alert_displayed=0;
						alt_phone_dialing=starting_alt_phone_dialing;
						auto_dial_level=starting_dial_level;
						MainPanelToFront();
						CalLBacKsCounTCheck();

						if (MDnextCID.match(regMNCvar)) {
							alert("No more leads in the hopper for campaign:\n" + campaign);   alert_displayed=1;
						}
						if (MDnextCID.match(regMDFvarDNC)) {
							alert("This phone number is in the DNC list:\n" + mdnPhonENumbeR);   alert_displayed=1;
						}
						if (MDnextCID.match(regMDFvarCAMP)) {
							alert("This phone number is not in the campaign lists:\n" + mdnPhonENumbeR);   alert_displayed=1;
						}
						if (alert_displayed==0) {
							alert("Unspecified error:\n" + mdnPhonENumbeR + "|" + MDnextCID);   alert_displayed=1;
						}

						if (starting_dial_level == 0) {
							document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber.gif\" border=0 alt=\"Dial Next Number\"></a>";
						} else {
							if (inbound_man > 0) {
								auto_dial_level=starting_dial_level;
								document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\" Pause \"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume.gif\" border=0 alt=\"Resume\"></a><BR><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
							} else {
								document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
							}
							document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
							reselect_alt_dial = 0;
						}
					} else {
						fronter = user;
						LasTCID											= MDnextResponse_array[0];
						document.osdial_form.lead_id.value			= MDnextResponse_array[1];
						LeaDPreVDispO									= MDnextResponse_array[2];
						document.osdial_form.vendor_lead_code.value	= MDnextResponse_array[4];
						document.osdial_form.list_id.value			= MDnextResponse_array[5];
						document.osdial_form.gmt_offset_now.value		= MDnextResponse_array[6];
						document.osdial_form.phone_code.value			= MDnextResponse_array[7];
						document.osdial_form.phone_number.value		= MDnextResponse_array[8];
						document.osdial_form.title.value				= MDnextResponse_array[9];
						document.osdial_form.first_name.value			= MDnextResponse_array[10];
						document.osdial_form.middle_initial.value		= MDnextResponse_array[11];
						document.osdial_form.last_name.value			= MDnextResponse_array[12];
						document.osdial_form.address1.value			= MDnextResponse_array[13];
						document.osdial_form.address2.value			= MDnextResponse_array[14];
						document.osdial_form.address3.value			= MDnextResponse_array[15];
						document.osdial_form.city.value				= MDnextResponse_array[16];
						document.osdial_form.state.value				= MDnextResponse_array[17];
						document.osdial_form.province.value			= MDnextResponse_array[18];
						document.osdial_form.postal_code.value		= MDnextResponse_array[19];
						document.osdial_form.country_code.value		= MDnextResponse_array[20];
						document.osdial_form.gender.value				= MDnextResponse_array[21];
						document.osdial_form.date_of_birth.value		= MDnextResponse_array[22];
						document.osdial_form.alt_phone.value			= MDnextResponse_array[23];
						document.osdial_form.email.value				= MDnextResponse_array[24];
						document.osdial_form.custom1.value	= MDnextResponse_array[25];
						var REGcommentsNL = new RegExp("!N","g");
						MDnextResponse_array[26] = MDnextResponse_array[26].replace(REGcommentsNL, "\n");
						document.osdial_form.comments.value			= MDnextResponse_array[26];
						document.osdial_form.called_count.value		= MDnextResponse_array[27];
						previous_called_count							= MDnextResponse_array[27];
						previous_dispo									= MDnextResponse_array[2];
						CBentry_time									= MDnextResponse_array[28];
						CBcallback_time									= MDnextResponse_array[29];
						CBuser											= MDnextResponse_array[30];
						CBcomments										= MDnextResponse_array[31];
						dialed_number									= MDnextResponse_array[32];
						dialed_label									= MDnextResponse_array[33];
						source_id										= MDnextResponse_array[34];
						document.osdial_form.custom2.value	= MDnextResponse_array[35];
						external_key										= MDnextResponse_array[36];
						document.osdial_form.post_date.value	= MDnextResponse_array[37];
						VDIC_web_form_address = MDnextResponse_array[38];
						VDIC_web_form_address2 = MDnextResponse_array[39];
						if (VDIC_web_form_address == '') VDIC_web_form_address = OSDiaL_web_form_address;
						if (VDIC_web_form_address2 == '') VDIC_web_form_address2 = OSDiaL_web_form_address2;
						if (MDnextResponse_array[40] == "Y") {
							web_form_extwindow = 1;
						} else {
							web_form_extwindow = 0;
						}
						if (MDnextResponse_array[41] == "Y") {
							web_form2_extwindow = 1;
						} else {
							web_form2_extwindow = 0;
						}
						if (MDnextResponse_array[42] != "") {
							campaign_script = MDnextResponse_array[42];
						}
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        $rcnt = $cnt + 43;
        echo '          document.osdial_form.' . $ffields[$cnt] . ".value = MDnextResponse_array[" . $rcnt . "];\n";
        $cnt++;
    }
?>
						
						lead_dial_number = document.osdial_form.phone_number.value;
						var dispnum = document.osdial_form.phone_number.value;
						var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);

						document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
						document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=<?=$status_bg?>>UID: " + MDnextCID + "</font> &nbsp; " + man_status;
						if ( (dialed_label.length < 3) || (dialed_label=='NONE') ) {
							dialed_label='MAIN';
						}

						web_form_vars = 
							"lead_id=" + document.osdial_form.lead_id.value + 
							"&vendor_id=" + document.osdial_form.vendor_lead_code.value + 
							"&list_id=" + document.osdial_form.list_id.value + 
							"&gmt_offset_now=" + document.osdial_form.gmt_offset_now.value + 
							"&phone_code=" + document.osdial_form.phone_code.value + 
							"&phone_number=" + document.osdial_form.phone_number.value + 
							"&title=" + document.osdial_form.title.value + 
							"&first_name=" + document.osdial_form.first_name.value + 
							"&middle_initial=" + document.osdial_form.middle_initial.value + 
							"&last_name=" + document.osdial_form.last_name.value + 
							"&address1=" + document.osdial_form.address1.value + 
							"&address2=" + document.osdial_form.address2.value + 
							"&address3=" + document.osdial_form.address3.value + 
							"&city=" + document.osdial_form.city.value + 
							"&state=" + document.osdial_form.state.value + 
							"&province=" + document.osdial_form.province.value + 
							"&postal_code=" + document.osdial_form.postal_code.value + 
							"&country_code=" + document.osdial_form.country_code.value + 
							"&gender=" + document.osdial_form.gender.value + 
							"&date_of_birth=" + document.osdial_form.date_of_birth.value + 
							"&alt_phone=" + document.osdial_form.alt_phone.value + 
							"&email=" + document.osdial_form.email.value + 
							"&custom1=" + document.osdial_form.custom1.value + 
							"&custom2=" + document.osdial_form.custom2.value + 
							"&comments=" + document.osdial_form.comments.value + 
							"&user=" + user + 
							"&pass=" + pass + 
							"&campaign=" + campaign + 
							"&phone_login=" + phone_login + 
							"&phone_pass=" + phone_pass + 
							"&fronter=" + fronter + 
							"&closer=" + user + 
							"&group=" + campaign + 
							"&channel_group=" + campaign + 
							"&SQLdate=" + SQLdate + 
							"&epoch=" + UnixTime + 
							"&uniqueid=" + document.osdial_form.uniqueid.value + 
							"&customer_zap_channel=" + lastcustchannel + 
							"&server_ip=" + server_ip + 
							"&SIPexten=" + extension + 
							"&session_id=" + session_id + 
							"&phone=" + document.osdial_form.phone_number.value + 
							"&parked_by=" + document.osdial_form.lead_id.value +
							"&dispo=" + LeaDDispO + '' +
							"&dialed_number=" + dialed_number + '' +
							"&dialed_label=" + dialed_label + '' +
							"&source_id=" + source_id + '' +
							"&external_key=" + external_key + '' +
							"&post_date=" + document.osdial_form.post_date.value + 
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        echo '          "&' . $jfield . '=" + document.osdial_form.' . $ffields[$cnt] . ".value +\n";
        $cnt++;
    }
?>
							"&recording_id=" + recording_id + 
							webform_session;
						
						//$OSDIAL_web_QUERY_STRING =~ s/ /+/gi;
						//$OSDIAL_web_QUERY_STRING =~ s/\`|\~|\:|\;|\#|\'|\"|\{|\}|\(|\)|\*|\^|\%|\$|\!|\%|\r|\t|\n//gi;

						var regWFspace = new RegExp(" ","ig");
						web_form_vars = web_form_vars.replace(regWF, '');
						var regWF = new RegExp("\\`|\\~|\\:|\\;|\\#|\\'|\\\"|\\{|\\}|\\(|\\)|\\*|\\^|\\%|\\$|\\!|\\%|\\r|\\t|\\n","ig");
						web_form_vars = web_form_vars.replace(regWFspace, '+');
						web_form_vars = web_form_vars.replace(regWF, '');

						if (LeaDPreVDispO == 'CALLBK') {
							document.getElementById("CusTInfOSpaN").innerHTML = "&nbsp;&nbsp;<B><font color=<?=$status_intense_color?>>Previous Callback</font>&nbsp;</B>";
							//document.getElementById("CusTInfOSpaN").style.background = CusTCB_bgcolor;
							document.getElementById("CBcommentsBoxA").innerHTML = "<b>Last Call: </b>" + CBentry_time;
							document.getElementById("CBcommentsBoxB").innerHTML = "<b>CallBack: </b>" + CBcallback_time;
							document.getElementById("CBcommentsBoxC").innerHTML = "<b>Agent: </b>" + CBuser;
							document.getElementById("CBcommentsBoxD").innerHTML = "<b>Comments: </b><br>" + CBcomments;
							showDiv('CBcommentsBox');
						}

						web_form_vars2 = web_form_vars;

						var regWFAvars = new RegExp("\\?","ig");
						if (VDIC_web_form_address.match(regWFAvars)) {
							web_form_vars = '&' + web_form_vars;
						} else {
							web_form_vars = '?' + web_form_vars;
						}

						if (VDIC_web_form_address2.match(regWFAvars)) {
							web_form_vars2 = '&' + web_form_vars2;
						} else {
							web_form_vars2 = '?' + web_form_vars2;
						}

						wf_enc_address = webform_rewrite(VDIC_web_form_address);
						if (wf_enc_address == VDIC_web_form_address) wf_enc_address += web_form_vars;

						wf2_enc_address = webform_rewrite(VDIC_web_form_address2);
						if (wf2_enc_address == VDIC_web_form_address2) wf2_enc_address += web_form_vars2;

						if (web_form_extwindow == 1) {
							document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOver=\"WebFormRefresH();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
						} else {
							document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
						}
							
						if (web_form2_extwindow == 1) {
							document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOver=\"WebFormRefresH();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
						} else {
							document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
						}

						if (previewFD_time > 0 && document.osdial_form.LeadPreview.checked==true) {
							previewFD_time_remaining =  previewFD_time;
							previewFD_timeout_id = setTimeout("ManualDialOnly()", previewFD_time * 1000);
							previewFD_display_id = setInterval("previewFDDisplayTime()", 1000);
						}

						reselect_preview_dial = 1;
						if (document.osdial_form.LeadPreview.checked==false || previewFD_time > 0) {
							if (document.osdial_form.LeadPreview.checked==false) {
								reselect_preview_dial = 0;
								MD_channel_look=1;
								custchannellive=1;

								document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer.gif\" border=0 alt=\"Hangup Customer\"></a>";

								if ( (campaign_recording == 'ALLCALLS') || (campaign_recording == 'ALLFORCE') ) {
									all_record = 'YES';
								}
							}

							if ( (view_scripts == 1) && (campaign_script.length > 0) ) {
								// test code for scripts output
								URLDecode(scriptnames[campaign_script],'NO');
								var textname = decoded;
								URLDecode(scripttexts[campaign_script],'YES');
								var texttext = decoded;
								var regWFplus = new RegExp("\\+","ig");
								textname = textname.replace(regWFplus, ' ');
								texttext = texttext.replace(regWFplus, ' ');
								var testscript = "<br><center><B>" + textname + "</B></center>\n\n<BR><BR>\n\n" + texttext;
								document.getElementById("ScriptContents").innerHTML = testscript;
								scriptUpdateFields();
							}

							if (get_call_launch == 'SCRIPT') {
								ScriptPanelToFront();
							}

							if (get_call_launch == 'WEBFORM') {
								if (web_form_extwindow == 1) {
									window.open(wf_enc_address, 'webform', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
								} else {
									CloseWebFormPanels();
									WebFormPanelDisplay(wf_enc_address);
								}
							}
							if (get_call_launch == 'WEBFORM2') {
								if (web_form2_extwindow == 1) {
									window.open(wf2_enc_address, 'webform2', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
								} else {
									CloseWebFormPanels();
									WebFormPanelDisplay2(wf2_enc_address);
								}
							}
						} else {
						    reselect_preview_dial = 1;
                        }
					}
				}
			}
			delete xmlhttp;
		}
	}


// ################################################################################
// Send the Manual Dial Skip
	function ManualDialSkip() {
		debug("<b>ManualDialSkip:</b>",2);
		dial_timedout=0;
		if (manual_dial_in_progress==1) {
			alert('You cannot skip a Call-Back or a call placed to a manually entered number.');
		} else {
			if (previewFD_time > 0) {
				clearTimeout(previewFD_timeout_id);
				clearInterval(previewFD_display_id);
				document.getElementById("PreviewFDTimeSpan").innerHTML = "";
			}

			if (inbound_man > 0) {
				auto_dial_level=starting_dial_level;
				document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\" Pause \"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\"><BR><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
			} else {
				document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
			}

			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
				xmlhttp = new XMLHttpRequest();
			}
			if (xmlhttp) { 
				manDiaLskip_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLskip&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&lead_id=" + document.osdial_form.lead_id.value + "&stage=" + previous_dispo + "&called_count=" + previous_called_count;
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(manDiaLskip_query); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						var MDSnextResponse = null;
						//alert(manDiaLskip_query);
						//alert(xmlhttp.responseText);
						MDSnextResponse = xmlhttp.responseText;

						var MDSnextResponse_array=MDSnextResponse.split("\n");
						MDSnextCID = MDSnextResponse_array[0];
						if (MDSnextCID == "LEAD NOT REVERTED") {
							alert("Lead was not reverted, there was an error:\n" + MDSnextResponse);
						} else {
							document.osdial_form.lead_id.value		='';
							document.osdial_form.vendor_lead_code.value='';
							document.osdial_form.list_id.value		='';
							document.osdial_form.gmt_offset_now.value	='';
							document.osdial_form.phone_code.value		='';
							document.osdial_form.phone_number.value	='';
							document.osdial_form.title.value			='';
							document.osdial_form.first_name.value		='';
							document.osdial_form.middle_initial.value	='';
							document.osdial_form.last_name.value		='';
							document.osdial_form.address1.value		='';
							document.osdial_form.address2.value		='';
							document.osdial_form.address3.value		='';
							document.osdial_form.city.value			='';
							document.osdial_form.state.value			='';
							document.osdial_form.province.value		='';
							document.osdial_form.postal_code.value	='';
							document.osdial_form.country_code.value	='';
							document.osdial_form.gender.value			='';
							document.osdial_form.date_of_birth.value	='';
							document.osdial_form.alt_phone.value		='';
							document.osdial_form.email.value			='';
							document.osdial_form.custom1.value='';
							document.osdial_form.custom2.value='';
							document.osdial_form.comments.value		='';
							document.osdial_form.called_count.value	='';
							document.osdial_form.post_date.value='';
							VDCL_group_id = '';
							fronter = '';
							previous_called_count = '';
							previous_dispo = '';
							custchannellive=1;
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        echo '          document.osdial_form.' . $ffields[$cnt] . ".value = '';\n";
        $cnt++;
    }
?>

							document.getElementById("MainStatuSSpan").innerHTML = " Lead skipped, go on to next lead";

							if (inbound_man > 0) {
								AutoDial_ReSume_PauSe('VDADready');
							} else {
								document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber.gif\" border=0 alt=\"Dial Next Number\"></a>";
							}
						}
					}
				}
				delete xmlhttp;
			}
		}
	}


// ################################################################################
// Send the Manual Dial Only - dial the previewed lead
	function ManualDialOnly(taskaltnum) {
		debug("<b>ManualDialOnly:</b> taskaltnum=" + taskaltnum,2);
		dial_timedout=0;
		if (previewFD_time > 0) {
			clearTimeout(previewFD_timeout_id);
			clearInterval(previewFD_display_id);
			document.getElementById("PreviewFDTimeSpan").innerHTML = "";
		}
		all_record = 'NO';
		all_record_count=0;
		if (taskaltnum == 'ALTPhoneE') {
			var manDiaLonly_num = document.osdial_form.alt_phone.value;
			lead_dial_number = document.osdial_form.alt_phone.value;
			dialed_number = lead_dial_number;
			dialed_label = 'ALT';
			WebFormRefresH('');
		} else {
			if (taskaltnum == 'AddresS3') {
				var manDiaLonly_num = document.osdial_form.address3.value;
				lead_dial_number = document.osdial_form.address3.value;
				dialed_number = lead_dial_number;
				dialed_label = 'ADDR3';
				WebFormRefresH('');
			} else {
				var manDiaLonly_num = document.osdial_form.phone_number.value;
				lead_dial_number = document.osdial_form.phone_number.value;
				dialed_number = lead_dial_number;
				dialed_label = 'MAIN';
				WebFormRefresH('');
			}
		}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			lead_cust2_cid = document.osdial_form.custom2.value;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			if (use_custom2_callerid == 'Y' && lead_cust2_cid != '') {
				cid = lead_cust2_cid;
				cid_name = lead_cust2_cid;
			}
			manDiaLonly_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLonly&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&lead_id=" + document.osdial_form.lead_id.value + "&phone_number=" + manDiaLonly_num + "&phone_code=" + document.osdial_form.phone_code.value + "&campaign=" + campaign + "&ext_context=" + ext_context + "&dial_timeout=" + dial_timeout + "&dial_prefix=" + dial_prefix + "&campaign_cid=" + cid + "&campaign_cid_name=" + cid_name + "&omit_phone_code=" + omit_phone_code;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLonly_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var MDOnextResponse = null;
					//alert(xmlhttp.responseText);
					MDOnextResponse = xmlhttp.responseText;

					var MDOnextResponse_array=MDOnextResponse.split("\n");
					MDnextCID = MDOnextResponse_array[0];
					if (MDnextCID == " CALL NOT PLACED") {
						alert("call was not placed, there was an error:\n" + MDOnextResponse);
					} else {
						MD_channel_look=1;

						var dispnum = manDiaLonly_num;
						var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);

						document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
						document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=<?=$status_bg?>>UID: " + MDnextCID + "</font> Waiting for Ring...";

						document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer.gif\" border=0 alt=\"Hangup Customer\"></a>";

						if ( (campaign_recording == 'ALLCALLS') || (campaign_recording == 'ALLFORCE') ) {
							all_record = 'YES';
						}

						if ( (view_scripts == 1) && (campaign_script.length > 0) ) {
							// test code for scripts output
							URLDecode(scriptnames[campaign_script],'NO');
							var textname = decoded;
							URLDecode(scripttexts[campaign_script],'YES');
							var texttext = decoded;
							var regWFplus = new RegExp("\\+","ig");
							textname = textname.replace(regWFplus, ' ');
							texttext = texttext.replace(regWFplus, ' ');
							var testscript = "<br><center><B>" + textname + "</B></center>\n\n<BR><BR>\n\n" + texttext;
							document.getElementById("ScriptContents").innerHTML = testscript;
							scriptUpdateFields();
						}

						if (get_call_launch == 'SCRIPT') {
							ScriptPanelToFront();
						}

						if (get_call_launch == 'WEBFORM') {
							if (web_form_extwindow == 1) {
								window.open(wf_enc_address, 'webform', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
							} else {
								CloseWebFormPanels();
								WebFormPanelDisplay(wf_enc_address);
							}
						}
						if (get_call_launch == 'WEBFORM2') {
							if (web_form2_extwindow == 1) {
								window.open(wf2_enc_address, 'webform2', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
							} else {
								CloseWebFormPanels();
								WebFormPanelDisplay2(wf2_enc_address);
							}
						}
					}
				}
			}
			delete xmlhttp;
		}
	}


// ################################################################################
// Set the client to READY and start looking for calls (VDADready, VDADpause)
	function AutoDial_ReSume_PauSe(taskaction,taskagentlog,taskwrapup) {
		debug("<b>AutoDial_ReSume_PauSe:</b> taskaction=" + taskaction + " taskagentlog=" + taskagentlog + " taskwrapup=" + taskwrapup,2);
		if (taskaction == 'VDADready') {
			var VDRP_stage = 'READY';
			if (INgroupCOUNT > 0) {
				if (OSDiaL_closer_blended == 0) {
					VDRP_stage = 'CLOSER';
				} else {
					VDRP_stage = 'READY';
				}
			}
			AutoDialReady = 1;
			AutoDialWaiting = 1;
			manual_dial_menu=0;
			alt_dial_menu=0;
			PCSpause=0;
			if (inbound_man > 0) {
				auto_dial_level=starting_dial_level;
				document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADpause','NEW_ID');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause.gif\" border=0 alt=\" Pause \"></a><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\"></a><BR><a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber.gif\" border=0 alt=\"Dial Next Number\"></a>";
			} else {
				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
			}
		} else {
			var VDRP_stage = 'PAUSED';
			AutoDialReady = 0;
			AutoDialWaiting = 0;
			manual_dial_menu=0;
			alt_dial_menu=0;
			if (inbound_man > 0) {
				auto_dial_level=starting_dial_level;
				document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\" Pause \"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume.gif\" border=0 alt=\"Resume\"></a><BR><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
			} else {
				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
			}
		}

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			autoDiaLready_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=" + taskaction + "&user=" + user + "&pass=" + pass + "&stage=" + VDRP_stage + "&agent_log_id=" + agent_log_id + "&agent_log=" + taskagentlog + "&wrapup=" + taskwrapup + "&campaign=" + campaign;
			debug("<b>AutoDial_ReSume_PauSe called:</b> vdc_db_query.php?" + autoDiaLready_query,4);
			xmlhttp.open('POST', 'vdc_db_query.php',false); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(autoDiaLready_query); 
			//xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var check_dispo = null;
					check_dispo = xmlhttp.responseText;
					var check_DS_array=check_dispo.split("\n");
					debug("<b>AutoDial_ReSume_PauSe return:</b> " + check_DS_array[0] + "|" + check_DS_array[1] + "|" + check_DS_array[2] + "|",3);
					if (check_DS_array[1] == 'Next agent_log_id:' && taskagentlog=="NEW_ID") {
						agent_log_id = check_DS_array[2];
						debug("<b>AutoDial_ReSume_PauSe agent_log_id set:</b> " + agent_log_id,4);
					}
				}
			//}
			delete xmlhttp;
		}
		//if (VDRP_stage=='PAUSED' && inbound_man < 1 && agent_pause_codes_active=='Y') {PCSpause=1; PauseCodeSelectContent_create();}
	}


// ################################################################################
// Check to see if there is a call being sent from the auto-dialer to agent conf
	function ReChecKCustoMerChaN() {
		debug("<b>ReChecKCustoMerChaN:</b>",2);
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			recheckVDAI_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=VDADREcheckINCOMING" + "&agent_log_id=" + agent_log_id + "&lead_id=" + document.osdial_form.lead_id.value;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(recheckVDAI_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var recheck_incoming = null;
					recheck_incoming = xmlhttp.responseText;
					//alert(xmlhttp.responseText);
					var recheck_VDIC_array=recheck_incoming.split("\n");
					if (recheck_VDIC_array[0] == '1') {
						var reVDIC_data_VDAC=recheck_VDIC_array[1].split("|");
						if (reVDIC_data_VDAC[3] == lastcustchannel) {
							// do nothing
						} else {
							//alert("Channel has changed from:\n" + lastcustchannel + '|' + lastcustserverip + "\nto:\n" + reVDIC_data_VDAC[3] + '|' + reVDIC_data_VDAC[4]);
							//document.osdial_form.callchannel.value	= reVDIC_data_VDAC[3];
							document.getElementById("callchannel").innerHTML = reVDIC_data_VDAC[3];
							lastcustchannel = reVDIC_data_VDAC[3];
							document.osdial_form.callserverip.value	= reVDIC_data_VDAC[4];
							lastcustserverip = reVDIC_data_VDAC[4];
							custchannellive = 1;
						}
					}
				}
			}
			delete xmlhttp;
		}
	}

// ################################################################################
// Check to see if there is a call being sent from the auto-dialer to agent conf
	function check_for_auto_incoming() {
		debug("<b>check_for_auto_incoming:</b>",5);
		if (typeof(xmlhttprequestcheckauto) == "undefined") {
			all_record = 'NO';
			all_record_count=0;
			document.osdial_form.lead_id.value = '';
			var xmlhttprequestcheckauto=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttprequestcheckauto = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttprequestcheckauto = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttprequestcheckauto = false;
			  }
			 }
			@end @*/
			if (!xmlhttprequestcheckauto && typeof XMLHttpRequest!='undefined') {
				xmlhttprequestcheckauto = new XMLHttpRequest();
			}
			if (xmlhttprequestcheckauto) { 
				checkVDAI_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=VDADcheckINCOMING" + "&agent_log_id=" + agent_log_id;
				xmlhttprequestcheckauto.open('POST', 'vdc_db_query.php'); 
				xmlhttprequestcheckauto.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttprequestcheckauto.send(checkVDAI_query); 
				xmlhttprequestcheckauto.onreadystatechange = function() { 
					if (xmlhttprequestcheckauto.readyState == 4 && xmlhttprequestcheckauto.status == 200) {
						var check_incoming = null;
						check_incoming = xmlhttprequestcheckauto.responseText;
						//alert(checkVDAI_query);
						//alert(xmlhttprequestcheckauto.responseText);
						var check_VDIC_array=check_incoming.split("\n");
						if (check_VDIC_array[0] == '1') {
							//alert(xmlhttprequestcheckauto.responseText);
							AutoDialWaiting = 0;

							var VDIC_data_VDAC=check_VDIC_array[1].split("|");
							var VDIC_fronter='';

							var VDIC_data_VDIG=check_VDIC_array[2].split("|");
							if (VDIC_data_VDIG[0].length > 5) {
								VDIC_web_form_address = VDIC_data_VDIG[0];
							}
							var VDCL_group_name			= VDIC_data_VDIG[1];
							var VDCL_group_color		= VDIC_data_VDIG[2];
							var VDCL_fronter_display	= VDIC_data_VDIG[3];
							VDCL_group_id				= VDIC_data_VDIG[4];
							CalL_ScripT_id				= VDIC_data_VDIG[5];
							CalL_AutO_LauncH			= VDIC_data_VDIG[6];
							CalL_XC_a_Dtmf				= VDIC_data_VDIG[7];
							CalL_XC_a_NuMber			= VDIC_data_VDIG[8];
							CalL_XC_b_Dtmf				= VDIC_data_VDIG[9];
							CalL_XC_b_NuMber			= VDIC_data_VDIG[10];
							if (VDIC_data_VDIG[11].length > 0) {
								LIVE_default_xfer_group = VDIC_data_VDIG[11];
							} else {
								LIVE_default_xfer_group = default_xfer_group;
							}
							CalL_allow_tab			    = VDIC_data_VDIG[12];
							if (VDIC_data_VDIG[13].length > 5) {
								VDIC_web_form_address2 = VDIC_data_VDIG[13];
							}

							if (VDIC_web_form_address == '') VDIC_web_form_address = OSDiaL_web_form_address;
							if (VDIC_web_form_address2 == '') VDIC_web_form_address2 = OSDiaL_web_form_address2;

							if (VDIC_data_VDIG[14] == "Y") {
								web_form_extwindow = 1;
							} else {
								web_form_extwindow = 0;
							}
							if (VDIC_data_VDIG[15] == "Y") {
								web_form2_extwindow = 1;
							} else {
								web_form2_extwindow = 0;
							}

							wf_enc_address = webform_rewrite(VDIC_web_form_address);
							if (wf_enc_address == VDIC_web_form_address) wf_enc_address += web_form_vars;

							wf2_enc_address = webform_rewrite(VDIC_web_form_address2);
							if (wf2_enc_address == VDIC_web_form_address2) wf2_enc_address += web_form_vars2;

							var VDIC_data_VDFR=check_VDIC_array[3].split("|");
							if ( (VDIC_data_VDFR[1].length > 1) && (VDCL_fronter_display == 'Y') ) {
								VDIC_fronter = "  Fronter: " + VDIC_data_VDFR[0] + " - " + VDIC_data_VDFR[1];
							}
							
							document.osdial_form.lead_id.value		= VDIC_data_VDAC[0];
							document.osdial_form.uniqueid.value		= VDIC_data_VDAC[1];
							CIDcheck									= VDIC_data_VDAC[2];
							CalLCID										= VDIC_data_VDAC[2];
							//document.osdial_form.callchannel.value	= VDIC_data_VDAC[3];
							document.getElementById("callchannel").innerHTML = VDIC_data_VDAC[3];
							lastcustchannel = VDIC_data_VDAC[3];
							document.osdial_form.callserverip.value	= VDIC_data_VDAC[4];
							lastcustserverip = VDIC_data_VDAC[4];
							if( document.images ) {
								document.images['livecall'].src = image_livecall_ON.src;
							}
							document.osdial_form.SecondS.value		= 0;

							VD_live_customer_call = 1;
							VD_live_call_secondS = 0;

							// INSERT OSDIAL_LOG ENTRY FOR THIS CALL PROCESS
							//DialLog("start");

							custchannellive=1;

							LasTCID											= check_VDIC_array[4];
							LeaDPreVDispO									= check_VDIC_array[6];
							fronter											= check_VDIC_array[7];
							document.osdial_form.vendor_lead_code.value	= check_VDIC_array[8];
							document.osdial_form.list_id.value			= check_VDIC_array[9];
							document.osdial_form.gmt_offset_now.value		= check_VDIC_array[10];
							document.osdial_form.phone_code.value			= check_VDIC_array[11];
							document.osdial_form.phone_number.value		= check_VDIC_array[12];
							document.osdial_form.title.value				= check_VDIC_array[13];
							document.osdial_form.first_name.value			= check_VDIC_array[14];
							document.osdial_form.middle_initial.value		= check_VDIC_array[15];
							document.osdial_form.last_name.value			= check_VDIC_array[16];
							document.osdial_form.address1.value			= check_VDIC_array[17];
							document.osdial_form.address2.value			= check_VDIC_array[18];
							document.osdial_form.address3.value			= check_VDIC_array[19];
							document.osdial_form.city.value				= check_VDIC_array[20];
							document.osdial_form.state.value				= check_VDIC_array[21];
							document.osdial_form.province.value			= check_VDIC_array[22];
							document.osdial_form.postal_code.value		= check_VDIC_array[23];
							document.osdial_form.country_code.value		= check_VDIC_array[24];
							document.osdial_form.gender.value				= check_VDIC_array[25];
							document.osdial_form.date_of_birth.value		= check_VDIC_array[26];
							document.osdial_form.alt_phone.value			= check_VDIC_array[27];
							document.osdial_form.email.value				= check_VDIC_array[28];
							document.osdial_form.custom1.value	= check_VDIC_array[29];
							var REGcommentsNL = new RegExp("!N","g");
							check_VDIC_array[30] = check_VDIC_array[30].replace(REGcommentsNL, "\n");
							document.osdial_form.comments.value			= check_VDIC_array[30];
							document.osdial_form.called_count.value		= check_VDIC_array[31];
							CBentry_time									= check_VDIC_array[32];
							CBcallback_time									= check_VDIC_array[33];
							CBuser											= check_VDIC_array[34];
							CBcomments										= check_VDIC_array[35];
							dialed_number									= check_VDIC_array[36];
							dialed_label									= check_VDIC_array[37];
							source_id										= check_VDIC_array[38];
							document.osdial_form.custom2.value	= check_VDIC_array[39];
							external_key										= check_VDIC_array[40];
							document.osdial_form.post_date.value	= check_VDIC_array[41];
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        $rcnt = $cnt + 42;
        echo '          document.osdial_form.' . $ffields[$cnt] . ".value = check_VDIC_array[" . $rcnt . "];\n";
        $cnt++;
    }
?>

							lead_dial_number = document.osdial_form.phone_number.value;
							var dispnum = document.osdial_form.phone_number.value;
							var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);

							document.getElementById("MainStatuSSpan").style.backgroundColor = '';
							document.getElementById("MainStatuSSpan").innerHTML = " Outgoing: " + status_display_number + "&nbsp;&nbsp;<font color=<?=$status_bg?>>UID: " + CIDcheck + "</font> &nbsp; " + VDIC_fronter; 

							document.getElementById("RepullControl").innerHTML = "<a href=\"#\" onclick=\"RepullLeadData('all');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_RPLD_on.gif\" border=0 alt=\"Repull Lead Data\"></a>";

							if (LeaDPreVDispO == 'CALLBK') {
								document.getElementById("CusTInfOSpaN").innerHTML = "&nbsp;<B>PREVIOUS CALLBACK</B>";
								document.getElementById("CusTInfOSpaN").style.backgroundColor = CusTCB_bgcolor;
								document.getElementById("CBcommentsBoxA").innerHTML = "<b>Last Call: </b>" + CBentry_time;
								document.getElementById("CBcommentsBoxB").innerHTML = "<b>CallBack: </b>" + CBcallback_time;
								document.getElementById("CBcommentsBoxC").innerHTML = "<b>Agent: </b>" + CBuser;
								document.getElementById("CBcommentsBoxD").innerHTML = "<b>Comments: </b><br>" + CBcomments;
								showDiv('CBcommentsBox');
							}

							if (VDIC_data_VDIG[1].length > 0) {
								if (VDIC_data_VDIG[2].length > 2) {
									document.getElementById("MainStatuSSpan").style.backgroundColor = VDIC_data_VDIG[2];
								}
								var dispnum = document.osdial_form.phone_number.value;
								var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);

								document.getElementById("MainStatuSSpan").innerHTML = " Incoming: " + status_display_number + " Group- " + VDIC_data_VDIG[1] + " &nbsp; " + VDIC_fronter; 
							}

							document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_parkcall.gif\" border=0 alt=\"Park Call\"></a>";

							document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer.gif\" border=0 alt=\"Hangup Customer\"></a>";

							document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_transferconf.gif\" border=0 alt=\"Transfer - Conference\"></a>";

							document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_localcloser.gif\" border=0 alt=\"LOCAL CLOSER\"></a>";

							document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_blindtransfer.gif\" border=0 alt=\"Dial Blind Transfer\"></a>";

							document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_ammessage.gif\" border=0 alt=\"Blind Transfer VMail Message\"></a>";
							document.getElementById("DialBlindVMail2").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_ammessage.gif\" border=0 alt=\"Blind Transfer VMail Message\"></a>";

							document.getElementById("DTMFDialPad0").innerHTML = "<a href=\"#\" alt=\"0\" onclick=\"document.osdial_form.conf_dtmf.value='0'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_0.png\" border=0></a>";
							document.getElementById("DTMFDialPad1").innerHTML = "<a href=\"#\" alt=\"1\" onclick=\"document.osdial_form.conf_dtmf.value='1'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_1.png\" border=0></a>";
							document.getElementById("DTMFDialPad2").innerHTML = "<a href=\"#\" alt=\"2 - ABC\" onclick=\"document.osdial_form.conf_dtmf.value='2'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_2.png\" border=0></a>";
							document.getElementById("DTMFDialPad3").innerHTML = "<a href=\"#\" alt=\"3 - DEF\" onclick=\"document.osdial_form.conf_dtmf.value='3'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_3.png\" border=0></a>";
							document.getElementById("DTMFDialPad4").innerHTML = "<a href=\"#\" alt=\"4 - GHI\" onclick=\"document.osdial_form.conf_dtmf.value='4'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_4.png\" border=0></a>";
							document.getElementById("DTMFDialPad5").innerHTML = "<a href=\"#\" alt=\"5 - JKL\" onclick=\"document.osdial_form.conf_dtmf.value='5'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_5.png\" border=0></a>";
							document.getElementById("DTMFDialPad6").innerHTML = "<a href=\"#\" alt=\"6 - MNO\" onclick=\"document.osdial_form.conf_dtmf.value='6'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_6.png\" border=0></a>";
							document.getElementById("DTMFDialPad7").innerHTML = "<a href=\"#\" alt=\"7 - PQRS\" onclick=\"document.osdial_form.conf_dtmf.value='7'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_7.png\" border=0></a>";
							document.getElementById("DTMFDialPad8").innerHTML = "<a href=\"#\" alt=\"8 - TUV\" onclick=\"document.osdial_form.conf_dtmf.value='8'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_8.png\" border=0></a>";
							document.getElementById("DTMFDialPad9").innerHTML = "<a href=\"#\" alt=\"9 - WXYZ\" onclick=\"document.osdial_form.conf_dtmf.value='9'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_9.png\" border=0></a>";
							document.getElementById("DTMFDialPadStar").innerHTML = "<a href=\"#\" alt=\"*\" onclick=\"document.osdial_form.conf_dtmf.value='*'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_star.png\" border=0></a>";
							document.getElementById("DTMFDialPadHash").innerHTML = "<a href=\"#\" alt=\"#\" onclick=\"document.osdial_form.conf_dtmf.value='#'; SendConfDTMF('<?=$session_id ?>');return false;\"><img src=\"templates/<?= $agent_template ?>/images/dtmf_hash.png\" border=0></a>";
	
							if (lastcustserverip == server_ip) {
								document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + lastcustchannel + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_up.gif\" BORDER=0></a>";
								document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + lastcustchannel + "','');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_down.gif\" BORDER=0></a>";
							}

							if (inbound_man > 0) {
								document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\" Pause \"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\"><BR><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
							} else {
								document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
							}

							if (VDCL_group_id.length > 1) {
								var group = VDCL_group_id;
							} else {
								var group = campaign;
							}
							if ( (dialed_label.length < 3) || (dialed_label=='NONE') ) {
								dialed_label='MAIN';
							}

							web_form_vars = 
								"lead_id=" + document.osdial_form.lead_id.value + 
								"&vendor_id=" + document.osdial_form.vendor_lead_code.value + 
								"&list_id=" + document.osdial_form.list_id.value + 
								"&gmt_offset_now=" + document.osdial_form.gmt_offset_now.value + 
								"&phone_code=" + document.osdial_form.phone_code.value + 
								"&phone_number=" + document.osdial_form.phone_number.value + 
								"&title=" + document.osdial_form.title.value + 
								"&first_name=" + document.osdial_form.first_name.value + 
								"&middle_initial=" + document.osdial_form.middle_initial.value + 
								"&last_name=" + document.osdial_form.last_name.value + 
								"&address1=" + document.osdial_form.address1.value + 
								"&address2=" + document.osdial_form.address2.value + 
								"&address3=" + document.osdial_form.address3.value + 
								"&city=" + document.osdial_form.city.value + 
								"&state=" + document.osdial_form.state.value + 
								"&province=" + document.osdial_form.province.value + 
								"&postal_code=" + document.osdial_form.postal_code.value + 
								"&country_code=" + document.osdial_form.country_code.value + 
								"&gender=" + document.osdial_form.gender.value + 
								"&date_of_birth=" + document.osdial_form.date_of_birth.value + 
								"&alt_phone=" + document.osdial_form.alt_phone.value + 
								"&email=" + document.osdial_form.email.value + 
								"&custom1=" + document.osdial_form.custom1.value + 
								"&custom2=" + document.osdial_form.custom2.value + 
								"&comments=" + document.osdial_form.comments.value + 
								"&user=" + user + 
								"&pass=" + pass + 
								"&campaign=" + campaign + 
								"&phone_login=" + phone_login + 
								"&phone_pass=" + phone_pass + 
								"&fronter=" + fronter + 
								"&closer=" + user + 
								"&group=" + group + 
								"&channel_group=" + group + 
								"&SQLdate=" + SQLdate + 
								"&epoch=" + UnixTime + 
								"&uniqueid=" + document.osdial_form.uniqueid.value + 
								"&customer_zap_channel=" + lastcustchannel + 
								"&customer_server_ip=" + lastcustserverip +
								"&server_ip=" + server_ip + 
								"&SIPexten=" + extension + 
								"&session_id=" + session_id + 
								"&phone=" + document.osdial_form.phone_number.value + 
								"&parked_by=" + document.osdial_form.lead_id.value +
								"&dispo=" + LeaDDispO + '' +
								"&dialed_number=" + dialed_number + '' +
								"&dialed_label=" + dialed_label + '' +
								"&source_id=" + source_id + '' +
								"&external_key=" + external_key + '' +
								"&post_date=" + document.osdial_form.post_date.value + 
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        echo '          "&' . $jfield . '=" + document.osdial_form.' . $ffields[$cnt] . ".value +\n";
        $cnt++;
    }
?>
								"&recording_id=" + recording_id + 
								webform_session;
							
							var regWFspace = new RegExp(" ","ig");
							web_form_vars = web_form_vars.replace(regWF, '');
							var regWF = new RegExp("\\`|\\~|\\:|\\;|\\#|\\'|\\\"|\\{|\\}|\\(|\\)|\\*|\\^|\\%|\\$|\\!|\\%|\\r|\\t|\\n","ig");
							web_form_vars = web_form_vars.replace(regWFspace, '+');
							web_form_vars = web_form_vars.replace(regWF, '');

							web_form_vars2 = web_form_vars;

							var regWFAvars = new RegExp("\\?","ig");
							if (VDIC_web_form_address.match(regWFAvars)) {
								web_form_vars = '&' + web_form_vars;
							} else {
								web_form_vars = '?' + web_form_vars;
							}
							if (VDIC_web_form_address2.match(regWFAvars)) {
								web_form_vars2 = '&' + web_form_vars2;
							} else {
								web_form_vars2 = '?' + web_form_vars2;
							}

							wf_enc_address = webform_rewrite(VDIC_web_form_address);
							if (wf_enc_address == VDIC_web_form_address) wf_enc_address += web_form_vars;

							wf2_enc_address = webform_rewrite(VDIC_web_form_address2);
							if (wf2_enc_address == VDIC_web_form_address2) wf2_enc_address += web_form_vars2;

							if (web_form_extwindow == 1) {
								document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOver=\"WebFormRefresH();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
							} else {
								document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
							}

							if (web_form2_extwindow == 1) {
								document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOver=\"WebFormRefresH();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
							} else {
								document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
							}

							if ( (campaign_recording == 'ALLCALLS') || (campaign_recording == 'ALLFORCE') ) {
								all_record = 'YES';
							}

							if ( (view_scripts == 1) && (CalL_ScripT_id.length > 0) ) {
								// test code for scripts output
								URLDecode(scriptnames[CalL_ScripT_id],'NO');
								var textname = decoded;
								URLDecode(scripttexts[CalL_ScripT_id],'YES');
								var texttext = decoded;
								var regWFplus = new RegExp("\\+","ig");
								textname = textname.replace(regWFplus, ' ');
								texttext = texttext.replace(regWFplus, ' ');
								var testscript = "<br><center><B>" + textname + "</B></center>\n\n<BR><BR>\n\n" + texttext;
								document.getElementById("ScriptContents").innerHTML = testscript;
								scriptUpdateFields();
							}

							if (CalL_AutO_LauncH == 'SCRIPT') {
								ScriptPanelToFront();
							}

							if (CalL_AutO_LauncH == 'WEBFORM') {
								if (web_form_extwindow == 1) {
									window.open(wf_enc_address, 'webform', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
								} else {
									CloseWebFormPanels();
									WebFormPanelDisplay(wf_enc_address);
								}
							}
							if (CalL_AutO_LauncH == 'WEBFORM2') {
								if (web_form2_extwindow == 1) {
									window.open(wf2_enc_address, 'webform2', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
								} else {
									CloseWebFormPanels();
									WebFormPanelDisplay2(wf2_enc_address);
								}
							}

						} else {
							// do nothing
						}
						xmlhttprequestcheckauto = undefined;
						delete xmlhttprequestcheckauto;
					}
				}
			}
		}
	}

/*
	function ShowCallbackInfo() {
		if (LeaDPreVDispO == 'CALLBK') {
		//	document.getElementById("CusTInfOSpaN").innerHTML = "&nbsp;<B>PREVIOUS CALLBACK</B>";
		//	document.getElementById("CusTInfOSpaN").style.background = CusTCB_bgcolor;
		//	document.getElementById("CBcommentsBoxA").innerHTML = "<b>Last Call: </b>" + CBentry_time;
		//	document.getElementById("CBcommentsBoxB").innerHTML = "<b>CallBack: </b>" + CBcallback_time;
		//	document.getElementById("CBcommentsBoxC").innerHTML = "<b>Agent: </b>" + CBuser;
		//	document.getElementById("CBcommentsBoxD").innerHTML = "<b>Comments: </b><br>" + CBcomments;
			showDiv('CBcommentsBox');
		}
	} 
*/

// ################################################################################
// Check to see if there is a call being sent from the auto-dialer to agent conf
	function RepullLeadData(lookup) {
		debug("<b>RepullLeadData:</b> lookup=" + lookup,2);
		if (typeof(xmlhttprequestrepull) == "undefined") {
			var oldlead = document.osdial_form.lead_id.value;
			var oldphone = document.osdial_form.phone_number.value;
			var curuniqueid = document.osdial_form.uniqueid.value;
			var list_id = document.osdial_form.list_id.value;
			if (dialed_number == oldphone) {
				alert("Please enter a different phone number.");
				return;
			}
			var xmlhttprequestrepull=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5) //<>
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttprequestrepull = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttprequestrepull = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttprequestrepull = false;
			  }
			 }
			@end @*/
			if (!xmlhttprequestrepull && typeof XMLHttpRequest!='undefined') {
				xmlhttprequestrepull = new XMLHttpRequest();
			}
			if (xmlhttprequestrepull) {
				checkRPLD_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=RepullLeadData" + "&agent_log_id=" + agent_log_id + "&oldphone=" + oldphone + "&oldlead=" + oldlead + "&uniqueid=" + curuniqueid + "&lookup=" + lookup + "&list_id=" + list_id;
				xmlhttprequestrepull.open('POST', 'vdc_db_query.php'); 
				xmlhttprequestrepull.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttprequestrepull.send(checkRPLD_query); 
				xmlhttprequestrepull.onreadystatechange = function() { 
					if (xmlhttprequestrepull.readyState == 4 && xmlhttprequestrepull.status == 200) {
						var check_incoming = null;
						check_incoming = xmlhttprequestrepull.responseText;
						//alert(checkRPLD_query);
						//alert(xmlhttprequestrepull.responseText);
						var check_RPLD_array=check_incoming.split("\n");
						if (check_RPLD_array[0] > 0) { //<>
							//alert(xmlhttprequestrepull.responseText);

							if (VDIC_web_form_address == '') VDIC_web_form_address = OSDiaL_web_form_address;
							if (VDIC_web_form_address2 == '') VDIC_web_form_address2 = OSDiaL_web_form_address2;

							document.osdial_form.lead_id.value		= check_RPLD_array[0];
							document.osdial_form.vendor_lead_code.value	= check_RPLD_array[1];
							document.osdial_form.list_id.value		= check_RPLD_array[2];
							document.osdial_form.gmt_offset_now.value	= check_RPLD_array[3];
							document.osdial_form.phone_code.value		= check_RPLD_array[4];
							document.osdial_form.phone_number.value	= check_RPLD_array[5];
							document.osdial_form.title.value		= check_RPLD_array[6];
							document.osdial_form.first_name.value		= check_RPLD_array[7];
							document.osdial_form.middle_initial.value	= check_RPLD_array[8];
							document.osdial_form.last_name.value		= check_RPLD_array[9];
							document.osdial_form.address1.value		= check_RPLD_array[10];
							document.osdial_form.address2.value		= check_RPLD_array[11];
							document.osdial_form.address3.value		= check_RPLD_array[12];
							document.osdial_form.city.value		= check_RPLD_array[13];
							document.osdial_form.state.value		= check_RPLD_array[14];
							document.osdial_form.province.value		= check_RPLD_array[15];
							document.osdial_form.postal_code.value	= check_RPLD_array[16];
							document.osdial_form.country_code.value	= check_RPLD_array[17];
							document.osdial_form.gender.value		= check_RPLD_array[18];
							document.osdial_form.date_of_birth.value	= check_RPLD_array[19];
							document.osdial_form.alt_phone.value		= check_RPLD_array[20];
							document.osdial_form.email.value		= check_RPLD_array[21];
							document.osdial_form.custom1.value	= check_RPLD_array[22];

							var REGcommentsNL = new RegExp("!N","g");
							check_RPLD_array[23] = check_RPLD_array[23].replace(REGcommentsNL, "\n");
							document.osdial_form.comments.value		= check_RPLD_array[23];

							document.osdial_form.called_count.value	= check_RPLD_array[24];
							document.osdial_form.custom2.value	= check_RPLD_array[25];
							external_key	= check_RPLD_array[26];
							document.osdial_form.post_date.value	= check_RPLD_array[27];

							if ( (dialed_label.length < 3) || (dialed_label=='NONE') )
								dialed_label='MAIN';
							dialed_number = oldphone;
							source_id = oldlead;

<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        $rcnt = $cnt + 28;
        echo '          document.osdial_form.' . $ffields[$cnt] . ".value = check_RPLD_array[" . $rcnt . "];\n";
        $cnt++;
    }
?>

							web_form_vars = 
								"lead_id=" + document.osdial_form.lead_id.value + 
								"&vendor_id=" + document.osdial_form.vendor_lead_code.value + 
								"&list_id=" + document.osdial_form.list_id.value + 
								"&gmt_offset_now=" + document.osdial_form.gmt_offset_now.value + 
								"&phone_code=" + document.osdial_form.phone_code.value + 
								"&phone_number=" + document.osdial_form.phone_number.value + 
								"&title=" + document.osdial_form.title.value + 
								"&first_name=" + document.osdial_form.first_name.value + 
								"&middle_initial=" + document.osdial_form.middle_initial.value + 
								"&last_name=" + document.osdial_form.last_name.value + 
								"&address1=" + document.osdial_form.address1.value + 
								"&address2=" + document.osdial_form.address2.value + 
								"&address3=" + document.osdial_form.address3.value + 
								"&city=" + document.osdial_form.city.value + 
								"&state=" + document.osdial_form.state.value + 
								"&province=" + document.osdial_form.province.value + 
								"&postal_code=" + document.osdial_form.postal_code.value + 
								"&country_code=" + document.osdial_form.country_code.value + 
								"&gender=" + document.osdial_form.gender.value + 
								"&date_of_birth=" + document.osdial_form.date_of_birth.value + 
								"&alt_phone=" + document.osdial_form.alt_phone.value + 
								"&email=" + document.osdial_form.email.value + 
								"&custom1=" + document.osdial_form.custom1.value + 
								"&custom2=" + document.osdial_form.custom2.value + 
								"&comments=" + document.osdial_form.comments.value + 
								"&user=" + user + 
								"&pass=" + pass + 
								"&campaign=" + campaign + 
								"&phone_login=" + phone_login + 
								"&phone_pass=" + phone_pass + 
								"&fronter=" + fronter + 
								"&closer=" + user + 
								"&group=" + group + 
								"&channel_group=" + group + 
								"&SQLdate=" + SQLdate + 
								"&epoch=" + UnixTime + 
								"&uniqueid=" + document.osdial_form.uniqueid.value + 
								"&customer_zap_channel=" + lastcustchannel + 
								"&customer_server_ip=" + lastcustserverip +
								"&server_ip=" + server_ip + 
								"&SIPexten=" + extension + 
								"&session_id=" + session_id + 
								"&phone=" + document.osdial_form.phone_number.value + 
								"&parked_by=" + document.osdial_form.lead_id.value +
								"&dispo=" + LeaDDispO + '' +
								"&dialed_number=" + dialed_number + '' +
								"&dialed_label=" + dialed_label + '' +
								"&source_id=" + source_id + '' +
								"&external_key=" + external_key + '' +
								"&post_date=" + document.osdial_form.post_date.value + '' +
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        echo '          "&' . $jfield . '=" + document.osdial_form.' . $ffields[$cnt] . ".value +\n";
        $cnt++;
    }
?>
								"&recording_id=" + recording_id + 
								webform_session;
							
							var regWFspace = new RegExp(" ","ig");
							web_form_vars = web_form_vars.replace(regWF, '');
							var regWF = new RegExp("\\`|\\~|\\:|\\;|\\#|\\'|\\\"|\\{|\\}|\\(|\\)|\\*|\\^|\\%|\\$|\\!|\\%|\\r|\\t|\\n","ig");
							web_form_vars = web_form_vars.replace(regWFspace, '+');
							web_form_vars = web_form_vars.replace(regWF, '');

							web_form_vars2 = web_form_vars;

							var regWFAvars = new RegExp("\\?","ig");
							if (VDIC_web_form_address.match(regWFAvars)) {
								web_form_vars = '&' + web_form_vars;
							} else {
								web_form_vars = '?' + web_form_vars;
							}
							if (VDIC_web_form_address2.match(regWFAvars)) {
								web_form_vars2 = '&' + web_form_vars2;
							} else {
								web_form_vars2 = '?' + web_form_vars2;
							}

							wf_enc_address = webform_rewrite(VDIC_web_form_address);
							if (wf_enc_address == VDIC_web_form_address) wf_enc_address += web_form_vars;

							wf2_enc_address = webform_rewrite(VDIC_web_form_address2);
							if (wf2_enc_address == VDIC_web_form_address2) wf2_enc_address += web_form_vars2;

							if (web_form_extwindow == 1) {
								document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOver=\"WebFormRefresH();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
							} else {
								document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
							}

							if (web_form2_extwindow == 1) {
								document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOver=\"WebFormRefresH();\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
							} else {
								document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
							}

						}
						xmlhttprequestrepull = undefined;
						delete xmlhttprequestrepull;
					}
				}
			}
		}
	}

// ################################################################################
// refresh the content of the web form URL
	function WebFormRefresH(taskrefresh) {
		debug("<b>WebFormRefresH:</b> taskrefresh=" + taskrefresh,2);
		if (VDCL_group_id.length > 1) {
			var group = VDCL_group_id;
		} else {
			var group = campaign;
		}
		if ( (dialed_label.length < 3) || (dialed_label=='NONE') ) {
			dialed_label='MAIN';
		}

		web_form_vars = 
			"lead_id=" + document.osdial_form.lead_id.value + 
			"&vendor_id=" + document.osdial_form.vendor_lead_code.value + 
			"&list_id=" + document.osdial_form.list_id.value + 
			"&gmt_offset_now=" + document.osdial_form.gmt_offset_now.value + 
			"&phone_code=" + document.osdial_form.phone_code.value + 
			"&phone_number=" + document.osdial_form.phone_number.value + 
			"&title=" + document.osdial_form.title.value + 
			"&first_name=" + document.osdial_form.first_name.value + 
			"&middle_initial=" + document.osdial_form.middle_initial.value + 
			"&last_name=" + document.osdial_form.last_name.value + 
			"&address1=" + document.osdial_form.address1.value + 
			"&address2=" + document.osdial_form.address2.value + 
			"&address3=" + document.osdial_form.address3.value + 
			"&city=" + document.osdial_form.city.value + 
			"&state=" + document.osdial_form.state.value + 
			"&province=" + document.osdial_form.province.value + 
			"&postal_code=" + document.osdial_form.postal_code.value + 
			"&country_code=" + document.osdial_form.country_code.value + 
			"&gender=" + document.osdial_form.gender.value + 
			"&date_of_birth=" + document.osdial_form.date_of_birth.value + 
			"&alt_phone=" + document.osdial_form.alt_phone.value + 
			"&email=" + document.osdial_form.email.value + 
			"&custom1=" + document.osdial_form.custom1.value + 
			"&custom2=" + document.osdial_form.custom2.value + 
			"&comments=" + document.osdial_form.comments.value + 
			"&user=" + user + 
			"&pass=" + pass + 
			"&campaign=" + campaign + 
			"&phone_login=" + phone_login + 
			"&phone_pass=" + phone_pass + 
			"&fronter=" + fronter + 
			"&closer=" + user + 
			"&group=" + group + 
			"&channel_group=" + group + 
			"&SQLdate=" + SQLdate + 
			"&epoch=" + UnixTime + 
			"&uniqueid=" + document.osdial_form.uniqueid.value + 
			"&customer_zap_channel=" + lastcustchannel + 
			"&customer_server_ip=" + lastcustserverip +
			"&server_ip=" + server_ip + 
			"&SIPexten=" + extension + 
			"&session_id=" + session_id + 
			"&phone=" + document.osdial_form.phone_number.value + 
			"&parked_by=" + document.osdial_form.lead_id.value +
			"&dispo=" + LeaDDispO + '' +
			"&dialed_number=" + dialed_number + '' +
			"&dialed_label=" + dialed_label + '' +
			"&source_id=" + source_id + '' +
			"&external_key=" + external_key + '' +
			"&post_date=" + document.osdial_form.post_date.value + 
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        echo '          "&' . $jfield . '=" + document.osdial_form.' . $ffields[$cnt] . ".value +\n";
        $cnt++;
    }
?>
			"&recording_id=" + recording_id + 
			webform_session;
		
		var regWFspace = new RegExp(" ","ig");
		web_form_vars = web_form_vars.replace(regWF, '');
		var regWF = new RegExp("\\`|\\~|\\:|\\;|\\#|\\'|\\\"|\\{|\\}|\\(|\\)|\\*|\\^|\\%|\\$|\\!|\\%|\\r|\\t|\\n","ig");
		web_form_vars = web_form_vars.replace(regWFspace, '+');
		web_form_vars = web_form_vars.replace(regWF, '');

		web_form_vars2 = web_form_vars;

		var regWFAvars = new RegExp("\\?","ig");
		if (VDIC_web_form_address.match(regWFAvars)) {
			web_form_vars = '&' + web_form_vars;
		} else {
			web_form_vars = '?' + web_form_vars;
		}
		if (VDIC_web_form_address2.match(regWFAvars)) {
			web_form_vars2 = '&' + web_form_vars2;
		} else {
			web_form_vars2 = '?' + web_form_vars2;
		}

		wf_enc_address = webform_rewrite(VDIC_web_form_address);
		if (wf_enc_address == VDIC_web_form_address) wf_enc_address += web_form_vars;

		wf2_enc_address = webform_rewrite(VDIC_web_form_address2);
		if (wf2_enc_address == VDIC_web_form_address2) wf2_enc_address += web_form_vars2;


		if (taskrefresh == 'OUT') {
			if (web_form_extwindow == 1) {
				document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOver=\"WebFormRefresH('IN');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
			} else {
				document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
			}

			if (web_form2_extwindow == 1) {
				document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOver=\"WebFormRefresH('IN');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
			} else {
				document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
			}
		} else {
			if (web_form_extwindow == 1) {
				document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOut=\"WebFormRefresH('OUT');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
			} else {
				document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform.gif\" border=0 alt=\"Web Form\"></a>";
			}

			if (web_form2_extwindow == 1) {
				document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOut=\"WebFormRefresH('OUT');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
			} else {
				document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform2.gif\" border=0 alt=\"Web Form2\"></a>";
			}
		}
	}


// ################################################################################
// Send hangup a second time from the dispo screen 
	function DispoHanguPAgaiN() {
		debug("<b>DispoHanguPAgaiN:</b>",2);
		form_cust_channel = AgaiNHanguPChanneL;
		//document.osdial_form.callchannel.value = AgaiNHanguPChanneL;
		document.getElementById("callchannel").innerHTML = AgaiNHanguPChanneL;
		document.osdial_form.callserverip.value = AgaiNHanguPServeR;
		lastcustchannel = AgaiNHanguPChanneL;
		lastcustserverip = AgaiNHanguPServeR;
		VD_live_call_secondS = AgainCalLSecondS;
		CalLCID = AgaiNCalLCID;

		document.getElementById("DispoSelectHAspan").innerHTML = "";
	
		dialedcall_send_hangup();
	}


// ################################################################################
// Send leave 3way call a second time from the dispo screen 
	function DispoLeavE3wayAgaiN() {
		debug("<b>DispoLeavE3wayAgaiN:</b>",2);
		XDchannel = DispO3wayXtrAchannel;
		document.osdial_form.xfernumber.value = DispO3wayCalLxfernumber;
		MDchannel = DispO3waychannel;
		lastcustserverip = DispO3wayCalLserverip;

		document.getElementById("DispoSelectHAspan").innerHTML = "";

		leave_3way_call('SECOND');

		DispO3waychannel = '';
		DispO3wayXtrAchannel = '';
		DispO3wayCalLserverip = '';
		DispO3wayCalLxfernumber = '';
		DispO3wayCalLcamptail = '';
	}


// ################################################################################
// Start Hangup Functions for both 
	function bothcall_send_hangup() {
		debug("<b>bothcall_send_hangup:</b>",2);
		if (lastxferchannel.length > 3) {
			xfercall_send_hangup();
		}
		if (lastcustchannel.length > 3) {
			dialedcall_send_hangup();
		}
	}

// ################################################################################
// Send Hangup command for customer call connected to the conference now to Manager
	function dialedcall_send_hangup(dispowindow,hotkeysused,altdispo) {
		debug("<b>dialedcall_send_hangup:</b> dispowindow=" + dispowindow + " hotkeysused=" + hotkeysused + " altdispo=" + altdispo,2);
		//var form_cust_channel = document.osdial_form.callchannel.value;
		var form_cust_channel = document.getElementById("callchannel").innerHTML;
		var form_cust_serverip = document.osdial_form.callserverip.value;
		var customer_channel = lastcustchannel;
		var customer_server_ip = lastcustserverip;
		AgaiNHanguPChanneL = lastcustchannel;
		AgaiNHanguPServeR = lastcustserverip;
		AgainCalLSecondS = VD_live_call_secondS;
		AgaiNCalLCID = CalLCID;
		var process_post_hangup=0;
		if ( (RedirecTxFEr < 1) && ( (MD_channel_look==1) || (auto_dial_level == 0) ) ) {
			MD_channel_look=0;
			DialTimeHangup();
		}
		if (form_cust_channel.length > 3) {
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
				xmlhttp = new XMLHttpRequest();
			}
			if (xmlhttp) { 
				var queryCID = "HLvdcW" + epoch_sec + user_abb;
				var hangupvalue = customer_channel;
				//alert(auto_dial_level + "|" + CalLCID + "|" + customer_server_ip + "|" + hangupvalue + "|" + VD_live_call_secondS);
				custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Hangup&format=text&user=" + user + "&pass=" + pass + "&channel=" + hangupvalue + "&call_server_ip=" + customer_server_ip + "&queryCID=" + queryCID + "&auto_dial_level=" + auto_dial_level + "&CalLCID=" + CalLCID + "&secondS=" + VD_live_call_secondS + "&exten=" + session_id;
				xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(custhangup_query); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
						//alert(xmlhttp.responseText);
						//var HU_debug = xmlhttp.responseText;
						//var HU_debug_array=HU_debug.split(" ");
						//if (HU_debug_array[0] == 'Call') {
							//alert(xmlhttp.responseText);
						//}
					}
				}
				process_post_hangup=1;
				delete xmlhttp;
			}
		} else {
			process_post_hangup=1;
		}
		if (process_post_hangup==1) {
			VD_live_customer_call = 0;
			VD_live_call_secondS = 0;
			MD_ring_secondS = 0;
			CalLCID = '';

		//	UPDATE OSDIAL_LOG ENTRY FOR THIS CALL PROCESS
			DialLog("end");
			if (dispowindow == 'NO') {
				open_dispo_screen=0;
			} else {
				if (auto_dial_level == 0)			{
					if (document.osdial_form.DiaLAltPhonE.checked==true) {
						reselect_alt_dial = 1;
						open_dispo_screen=0;
					} else {
						reselect_alt_dial = 0;
						open_dispo_screen=1;
					}
				} else {
					if (document.osdial_form.DiaLAltPhonE.checked==true) {
						reselect_alt_dial = 1;
						open_dispo_screen=0;
						auto_dial_level=0;
						manual_dial_in_progress=1;
						auto_dial_alt_dial=1;
					} else {
						reselect_alt_dial = 0;
						open_dispo_screen=1;
					}
				}
			}

		//  DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
			//document.osdial_form.callchannel.value = '';
			document.getElementById("callchannel").innerHTML = '';
			document.osdial_form.callserverip.value = '';
			lastcustchannel='';
			lastcustserverip='';

			if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
			document.getElementById("WebFormSpan").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform_OFF.gif\" border=0 alt=\"Web Form\">";
			document.getElementById("WebFormSpan2").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_webform_OFF.gif\" border=0 alt=\"Web Form\">";
			document.getElementById("ParkControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_parkcall_OFF.gif\" border=0 alt=\"Park Call\">";
			document.getElementById("HangupControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer_OFF.gif\" border=0 alt=\"Hangup Customer\">";
			document.getElementById("XferControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_transferconf_OFF.gif\" border=0 alt=\"Transfer - Conference\">";
			document.getElementById("LocalCloser").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_localcloser_OFF.gif\" border=0 alt=\"LOCAL CLOSER\">";
			document.getElementById("DialBlindTransfer").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_blindtransfer_OFF.gif\" border=0 alt=\"Dial Blind Transfer\">";
			document.getElementById("DialBlindVMail").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_ammessage_OFF.gif\" border=0 alt=\"Blind Transfer VMail Message\">";
			document.getElementById("DialBlindVMail2").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_ammessage_OFF.gif\" border=0 alt=\"Blind Transfer VMail Message\">";
			document.getElementById("VolumeUpSpan").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_up_off.gif\" BORDER=0>";
			document.getElementById("VolumeDownSpan").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_volume_down_off.gif\" BORDER=0>";
			document.getElementById("RepullControl").innerHTML = "";

			document.getElementById("DTMFDialPad0").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_0_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad1").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_1_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad2").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_2_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad3").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_3_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad4").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_4_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad5").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_5_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad6").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_6_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad7").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_7_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad8").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_8_OFF.png\" border=0>";
			document.getElementById("DTMFDialPad9").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_9_OFF.png\" border=0>";
			document.getElementById("DTMFDialPadStar").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_star_OFF.png\" border=0>";
			document.getElementById("DTMFDialPadHash").innerHTML = "<img src=\"templates/<?= $agent_template ?>/images/dtmf_hash_OFF.png\" border=0>";

			document.osdial_form.custdatetime.value		= '';

			if (auto_dial_level == 0) {
				if (document.osdial_form.DiaLAltPhonE.checked==true) {
					reselect_alt_dial = 1;
					if (altdispo == 'ALTPH2') {
						ManualDialOnly('ALTPhoneE');
					} else {
						if (altdispo == 'ADDR3') {
							ManualDialOnly('AddresS3');
						} else {
							if (hotkeysused == 'YES') {
								reselect_alt_dial = 0;
								manual_auto_hotkey = 1;
								alt_dial_active = 0;
								alt_dial_menu = 0;
							}
						}
					}
				} else {
					if (hotkeysused == 'YES') {
						manual_auto_hotkey = 1;
						alt_dial_active = 0;
						alt_dial_menu = 0;
					} else {
						document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber.gif\" border=0 alt=\"Dial Next Number\"></a>";
					}
					reselect_alt_dial = 0;
				}
			} else {
				if (document.osdial_form.DiaLAltPhonE.checked==true) {
					reselect_alt_dial = 1;
					if (altdispo == 'ALTPH2') {
						ManualDialOnly('ALTPhoneE');
					} else {
						if (altdispo == 'ADDR3') {
							ManualDialOnly('AddresS3');
						} else {
							if (hotkeysused == 'YES') {
								manual_auto_hotkey=1;
								alt_dial_active=0;
								alt_dial_menu = 0;
								document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
								document.getElementById("MainStatuSSpan").innerHTML = '';
								if (inbound_man > 0) {
									document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\" Pause \"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\"><BR><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
								} else {
									document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
								}
								reselect_alt_dial = 0;
							}
						}
					}
				} else {
					document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
					document.getElementById("MainStatuSSpan").innerHTML = '';
					if (inbound_man > 0) {
						document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\" Pause \"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\"><BR><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
					} else {
						document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
					}
					reselect_alt_dial = 0;
				}
			}
			ShoWTransferMain('OFF');
		}
	}


// ################################################################################
// Send Hangup command for 3rd party call connected to the conference now to Manager
	function xfercall_send_hangup() {
		debug("<b>xfercall_send_hangup:</b>",2);
		var xferchannel = document.osdial_form.xferchannel.value;
		var xfer_channel = lastxferchannel;
		var process_post_hangup=0;
		if (MD_channel_look==1) {
			MD_channel_look=0;
			DialTimeHangup();
		}
		if (xferchannel.length > 3) {
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
				xmlhttp = new XMLHttpRequest();
			}
			if (xmlhttp) { 
				var queryCID = "HXvdcW" + epoch_sec + user_abb;
				var hangupvalue = xfer_channel;
				custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Hangup&format=text&user=" + user + "&pass=" + pass + "&channel=" + hangupvalue + "&queryCID=" + queryCID;
				xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(custhangup_query); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
						//alert(xmlhttp.responseText);
					}
				}
				process_post_hangup=1;
				delete xmlhttp;
			}
		} else {
			process_post_hangup=1;
		}
		if (process_post_hangup==1) {
			XD_live_customer_call = 0;
			XD_live_call_secondS = 0;
			MD_ring_secondS = 0;
			MD_channel_look=0;
			XDnextCID = '';
			XDcheck = '';
			xferchannellive=0;

		//  DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
			document.osdial_form.xferchannel.value = "";
			lastxferchannel='';

			document.getElementById("Leave3WayCall").innerHTML ="<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_leave3waycall_OFF.gif\" border=0 alt=\"LEAVE 3-WAY CALL\">";

			document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_dialwithcustomer.gif\" border=0 alt=\"Dial With Customer\"></a>";

			document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_parkcustomerdial.gif\" border=0 alt=\"Park Customer Dial\"></a>";

			document.getElementById("HangupXferLine").innerHTML ="<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_hangupxferline_OFF.gif\" border=0 alt=\"Hangup Xfer Line\">";

			document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_hangupbothlines.gif\" border=0 alt=\"Hangup Both Lines\"></a>";
		}
	}

// ################################################################################
// Send Hangup command for any Local call that is not in the quiet(7) entry - used to stop manual dials even if no connect
	function DialTimeHangup() {
		debug("<b>DialTimeHangup:</b>",2);
		if (RedirecTxFEr < 1) {
			//alert("RedirecTxFEr|" + RedirecTxFEr);
			MD_channel_look=0;
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
		 	try {
		  	xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 	} catch (e) {
		  	try {
		   	xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  	} catch (E) {
		   	xmlhttp = false;
		  	}
		 	}
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
				xmlhttp = new XMLHttpRequest();
			}
			if (xmlhttp) { 
				var queryCID = "HTvdcW" + epoch_sec + user_abb;
				custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=HangupConfDial&format=text&user=" + user + "&pass=" + pass + "&exten=" + session_id + "&ext_context=" + ext_context + "&queryCID=" + queryCID;
				xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(custhangup_query); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
						//alert(xmlhttp.responseText);
					}
				}
				delete xmlhttp;
			}
		}
	}


// ################################################################################
// Update osdial_list lead record with all altered values from form
	function CustomerData_update() {
		debug("<b>CustomerData_update:</b>",2);

		var REGcommentsAMP = new RegExp('&',"g");
		var REGcommentsQUES = new RegExp("\\?","g");
		var REGcommentsPOUND = new RegExp("\\#","g");
		var REGcommentsRESULT = document.osdial_form.comments.value.replace(REGcommentsAMP, "--AMP--");
		REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsQUES, "--QUES--");
		REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsPOUND, "--POUND--");

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			VLupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&campaign=" + campaign +  "&ACTION=updateLEAD&format=text&user=" + user + "&pass=" + pass + 
				"&lead_id=" + document.osdial_form.lead_id.value + 
				"&vendor_lead_code=" + document.osdial_form.vendor_lead_code.value + 
				"&phone_number=" + document.osdial_form.phone_number.value + 
				"&title=" + document.osdial_form.title.value + 
				"&first_name=" + document.osdial_form.first_name.value + 
				"&middle_initial=" + document.osdial_form.middle_initial.value + 
				"&last_name=" + document.osdial_form.last_name.value + 
				"&address1=" + document.osdial_form.address1.value + 
				"&address2=" + document.osdial_form.address2.value + 
				"&address3=" + document.osdial_form.address3.value + 
				"&city=" + document.osdial_form.city.value + 
				"&state=" + document.osdial_form.state.value + 
				"&province=" + document.osdial_form.province.value + 
				"&postal_code=" + document.osdial_form.postal_code.value + 
				"&country_code=" + document.osdial_form.country_code.value + 
				"&gender=" + document.osdial_form.gender.value + 
				"&date_of_birth=" + document.osdial_form.date_of_birth.value + 
				"&alt_phone=" + document.osdial_form.alt_phone.value + 
				"&email=" + document.osdial_form.email.value + 
				"&custom1=" + document.osdial_form.custom1.value + 
				"&custom2=" + document.osdial_form.custom2.value + 
				"&post_date=" + document.osdial_form.post_date.value + 
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        echo '      "&' . $ffields[$cnt] . '=" + document.osdial_form.' . $ffields[$cnt] . ".value +\n";
        $cnt++;
    }
?>
				"&comments=" + REGcommentsRESULT;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VLupdate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}
	}

// ################################################################################
// Generate the Call Disposition Chooser panel
function DispoSelectContent_create(taskDSgrp,taskDSstage) {
	debug("<b>DispoSelectContent_create:</b> taskDSgrp=" + taskDSstage,2);
	AgentDispoing = 1;
	var VD_statuses_ct_half = parseInt(VD_statuses_ct / 2);
	var scroll = '';
	if (VD_statuses_ct_half > 12) scroll="overflow-y:scroll;";
	var dispo_HTML = "<br><table frame=border cellpadding=5 cellspacing=5 width=620><tr><td colspan=2 align=center><font color=<?=$dispo_fc?>><b>Call Dispositions</b></td></tr><tr><td colspan=2 align=center><div style=\"height:320;" + scroll + "\"><table cellpadding=5 cellspacing=5><tr><td bgcolor=\"<?=$dispo_bg?>\" height=320 width=300 valign=top><font class=\"log_text\"><div id=DispoSelectA>";
	var loop_ct = 0;
	while (loop_ct < VD_statuses_ct) {
		if (taskDSgrp == VARstatuses[loop_ct]) {
			dispo_HTML = dispo_HTML + "<font size=3 style=\"BACKGROUND-COLOR: <?=$dispo_bg2?>\"><b><a href=\"#\" onclick=\"DispoSelect_submit();return false;\">" + VARstatuses[loop_ct] + " - " + VARstatusnames[loop_ct] + "</a></b></font><BR><BR>";
		} else {
			dispo_HTML = dispo_HTML + "<a href=\"#\" onclick=\"DispoSelectContent_create('" + VARstatuses[loop_ct] + "','ADD');return false;\">" + VARstatuses[loop_ct] + " - " + VARstatusnames[loop_ct] + "</a><font size=-2><BR><BR></font>";
		}
		if (loop_ct == VD_statuses_ct_half) {
			dispo_HTML = dispo_HTML + "</div></font></td><td bgcolor=\"<?=$dispo_bg?>\" height=320 width=300 valign=top><font class=\"log_text\"><div id=DispoSelectB>";
		}
		loop_ct++;
	}
	dispo_HTML = dispo_HTML + "</div></font></td></tr></table></div></td></tr></table>";
	if (taskDSstage == 'ReSET') {
		document.osdial_form.DispoSelection.value = '';
	} else {
		document.osdial_form.DispoSelection.value = taskDSgrp;
	}
	document.getElementById("DispoSelectContent").innerHTML = dispo_HTML;
}


// ################################################################################
// Generate the Pause Code Chooser panel
	function PauseCodeSelectContent_create() {
		debug("<b>PauseCodeSelectContent_create:</b>",2);
		if ( (VD_live_customer_call==1) || (alt_dial_active==1) ) {
			alert("You must hangup and disposition your call before clicking \"Pause\". ");
		} else {
			if (AutoDialReady==1) {
				AutoDial_ReSume_PauSe('VDADpause','NEW_ID');
				PCSpause = 1;
			}
			showDiv('PauseCodeSelectBox');
			WaitingForNextStep=1;
			PauseCode_HTML = '';
			document.osdial_form.PauseCodeSelection.value = '';		
			var VD_pause_codes_ct_half = parseInt(VD_pause_codes_ct / 2);
			PauseCode_HTML = "<table frame=box bgcolor=<?=$pause_bg?> cellpadding=5 cellspacing=5 width=500><tr><td colspan=2 align=center><B><font color=<?=$pause_fc?>>Pause Codes</font></B></td></tr> <tr><td bgcolor=\"<?=$pause_bg2?>\" height=300 width=240 valign=top><font class=\"log_text\"><span id=PauseCodeSelectA>";
			var loop_ct = 0;
			while (loop_ct < VD_pause_codes_ct) {
				PauseCode_HTML = PauseCode_HTML + "<font size=3 style=\"BACKGROUND-COLOR: <?=$pause_bg2?>\"><b><a href=\"#\" onclick=\"PauseCodeSelect_submit('" + VARpause_codes[loop_ct] + "');return false;\">" + VARpause_codes[loop_ct] + " - " + VARpause_code_names[loop_ct] + "</a></b></font><BR><BR>";
				loop_ct++;
				if (loop_ct == VD_pause_codes_ct_half) {
					PauseCode_HTML = PauseCode_HTML + "</span></font></td><td bgcolor=\"<?=$pause_bg2?>\" height=300 width=240 valign=top><font class=\"log_text\"><span id=PauseCodeSelectB>";
				}
			}
			PauseCode_HTML = PauseCode_HTML + "</span></font></td></tr></table><BR><BR><font size=3 \"><b><a href=\"#\" onclick=\"if (PCSpause==1) {AutoDial_ReSume_PauSe('VDADready');} PauseCodeSelect_submit('');return false;\">Go Back</a>";
			document.getElementById("PauseCodeSelectContent").innerHTML = PauseCode_HTML;
		}
	}

// ################################################################################
// open web form, then submit disposition
	function WeBForMDispoSelect_submit() {
		debug("<b>WeBForMDispoSelect_submit:</b>",2);
		var DispoChoice = document.osdial_form.DispoSelection.value;

		if (DispoChoice.length < 1) {
			alert("You Must Select a Disposition");
		} else {
			document.getElementById("CusTInfOSpaN").style.backgroundColor = panel_bgcolor;
			document.getElementById("CusTInfOSpaN").innerHTML = "";

			LeaDDispO = DispoChoice;
			WebFormRefresH();

			if (submit_method == 2) {
				window.open(wf2_enc_address, 'webform2', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
			} else {
				window.open(wf_enc_address, 'webform', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
			}

			DispoSelect_submit();
		}
	}


// ################################################################################
// Update osdial_list lead record with disposition selection
	function DispoSelect_submit() {
		debug("<b>DispoSelect_submit:</b>",2);

		var group = campaign;
		if (VDCL_group_id.length > 1) group = VDCL_group_id;

		var DispoChoice = document.osdial_form.DispoSelection.value;

		if (DispoChoice.length < 1) {
			alert("You Must Select a Disposition");
		} else {
			document.getElementById("CusTInfOSpaN").innerHTML = "";
			document.getElementById("CusTInfOSpaN").style.backgroundColor = panel_bgcolor;

			if (submit_method > 0) {
				LeaDDispO = DispoChoice;
				WebFormRefresH();
				if (submit_method == 2) {
					window.open(wf2_enc_address, 'webform2', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
				} else {
					window.open(wf_enc_address, 'webform', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
				}
			}

			if ( (DispoChoice == 'CALLBK') && (scheduled_callbacks > 0) ) {
				showDiv('CallBackSelectBox');
			} else if ( (DispoChoice == 'PD' && PostDatETimE == '' && (document.osdial_form.post_date.value == '0000-00-00' || document.osdial_form.post_date.value == '0000-00-00 00:00:00') ) ) {
				showDiv('PostDateSelectBox');
			} else {
				var xmlhttp=false;
				/*@cc_on @*/
				/*@if (@_jscript_version >= 5)
				// JScript gives us Conditional compilation, we can cope with old IE versions.
				// and security blocked creation of the objects.
				 try {
				  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				 } catch (e) {
				  try {
				   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				  } catch (E) {
				   xmlhttp = false;
				  }
				 }
				@end @*/
				if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
					xmlhttp = new XMLHttpRequest();
				}
				if (xmlhttp) { 
					DSupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=updateDISPO&format=text&user=" + user + "&pass=" + pass + "&dispo_choice=" + DispoChoice + "&lead_id=" + document.osdial_form.lead_id.value + "&campaign=" + campaign + "&auto_dial_level=" + auto_dial_level + "&agent_log_id=" + agent_log_id + "&PostDatETimE=" + PostDatETimE + "&CallBackDatETimE=" + CallBackDatETimE + "&list_id=" + document.osdial_form.list_id.value + "&recipient=" + CallBackrecipient + "&use_internal_dnc=" + use_internal_dnc + "&MDnextCID=" + LasTCID + "&stage=" + group + "&comments=" + CallBackCommenTs;
					debug("<b>updateDISPO called:</b> vdc_db_query.php?" + DSupdate_query,4);
					xmlhttp.open('POST', 'vdc_db_query.php',false); 
					xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttp.send(DSupdate_query); 
					//xmlhttp.onreadystatechange = function() { 
						debug(xmlhttp.readyState,1);
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							var check_dispo = null;
							check_dispo = xmlhttp.responseText;
							var check_DS_array=check_dispo.split("\n");
							debug("<b>updateDISPO return:</b> " + check_DS_array[0] + "|" + check_DS_array[1] + "|" + check_DS_array[2] + "|",3);
							if (check_DS_array[1] == 'Next agent_log_id:') {
								agent_log_id = check_DS_array[2];
								debug("<b>updateDISPO agent_log_id set:</b> " + agent_log_id,4);
							}
						}
					//}
					delete xmlhttp;
				}
				// CLEAR ALL FORM VARIABLES
				document.osdial_form.lead_id.value		='';
				document.osdial_form.vendor_lead_code.value='';
				document.osdial_form.list_id.value		='';
				document.osdial_form.gmt_offset_now.value	='';
				document.osdial_form.phone_code.value		='';
				document.osdial_form.phone_number.value	='';
				document.osdial_form.title.value			='';
				document.osdial_form.first_name.value		='';
				document.osdial_form.middle_initial.value	='';
				document.osdial_form.last_name.value		='';
				document.osdial_form.address1.value		='';
				document.osdial_form.address2.value		='';
				document.osdial_form.address3.value		='';
				document.osdial_form.city.value			='';
				document.osdial_form.state.value			='';
				document.osdial_form.province.value		='';
				document.osdial_form.postal_code.value	='';
				document.osdial_form.country_code.value	='';
				document.osdial_form.gender.value			='';
				document.osdial_form.date_of_birth.value	='';
				document.osdial_form.alt_phone.value		='';
				document.osdial_form.email.value			='';
				document.osdial_form.custom1.value='';
				document.osdial_form.custom2.value='';
				document.osdial_form.comments.value		='';
				document.osdial_form.called_count.value	='';
				document.osdial_form.post_date.value	='';
<?
    $cnt = 0;
    foreach ($jfields as $jfield) {
        echo '          document.osdial_form.' . $ffields[$cnt] . ".value = '';\n";
        $cnt++;
    }
?>
				VDCL_group_id = '';
				fronter = '';

				var rp_newid="NEW_ID";
				if (manual_dial_in_progress==1) {
					manual_dial_finished();
					rp_newid="";
				}
				hideDiv('DispoSelectBox');
				hideDiv('DispoButtonHideA');
				hideDiv('DispoButtonHideB');
				hideDiv('DispoButtonHideC');
				document.getElementById("DispoSelectBox").style.top = 1;
				document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMinimize()\">minimize</a>";
				document.getElementById("DispoSelectHAspan").innerHTML = "<a href=\"#\" onclick=\"DispoHanguPAgaiN()\">Hangup Again</a>";

				document.getElementById("RecorDingFilename").innerHTML = "&nbsp;";
				document.getElementById("RecorDID").innerHTML = "&nbsp;";
				recording_id=0;

				document.getElementById("MainStatuSSpan").style.backgroundColor = '<?=$status_bg?>';
				document.getElementById("MainStatuSSpan").innerHTML = "";

				CloseWebFormPanels();

				CBcommentsBoxhide();

				AgentDispoing = 0;

				if (wrapup_waiting == 0) {
					if (document.osdial_form.DispoSelectStop.checked==true) {
						if (auto_dial_level != '0') {
							AutoDialWaiting = 0;
							AutoDial_ReSume_PauSe("VDADpause",rp_newid);
							//document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
						}
						OSDiaL_pause_calling = 1;
						if (dispo_check_all_pause != '1') {
							document.osdial_form.DispoSelectStop.checked=false;
						}
					} else {
						if (auto_dial_level != '0') {
							AutoDialWaiting = 1;
							AutoDial_ReSume_PauSe("VDADready",rp_newid);
							//document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
						} else {
							// trigger HotKeys manual dial automatically go to next lead
							if (manual_auto_hotkey == '1') {
								manual_auto_hotkey = 0;
								ManualDialNext('','','','','');
							}
						}
					}
				}
			}
		}
	}


// ################################################################################
// Submit the Pause Code 
	function PauseCodeSelect_submit(newpausecode) {
		debug("<b>PauseCodeSelect_submit:</b> newpausecode=" + newpausecode,2);
		hideDiv('PauseCodeSelectBox');
		WaitingForNextStep=0;

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			VMCpausecode_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=PauseCodeSubmit&format=text&status=" + newpausecode + "&agent_log_id=" + agent_log_id + "&campaign=" + campaign + "&extension=" + extension + "&protocol=" + protocol + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCpausecode_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}
	}



// ################################################################################
// Populate the dtmf and xfer number for each preset link in xfer-conf frame
	function DtMf_PreSet_a() {
		debug("<b>DtMf_PreSet_a:</b>",2);
		document.osdial_form.conf_dtmf.value = CalL_XC_a_Dtmf;
		document.osdial_form.xfernumber.value = CalL_XC_a_NuMber;
	}

	function DtMf_PreSet_b() {
		debug("<b>DtMf_PreSet_b:</b>",2);
		document.osdial_form.conf_dtmf.value = CalL_XC_b_Dtmf;
		document.osdial_form.xfernumber.value = CalL_XC_b_NuMber;
	}

	function DtMf_PreSet_a_DiaL() {
		debug("<b>DtMf_PreSet_a_DiaL:</b>",2);
		ShoWTransferMain("ON");
		document.osdial_form.conf_dtmf.value = CalL_XC_a_Dtmf;
		document.osdial_form.xfernumber.value = CalL_XC_a_NuMber;
		//basic_originate_call(CalL_XC_a_NuMber,'NO','YES',session_id,'YES');
		SendManualDial('YES');
	}

	function DtMf_PreSet_b_DiaL() {
		debug("<b>DtMf_PreSet_b_DiaL:</b>",2);
		ShoWTransferMain("ON");
		document.osdial_form.conf_dtmf.value = CalL_XC_b_Dtmf;
		document.osdial_form.xfernumber.value = CalL_XC_b_NuMber;
		//basic_originate_call(CalL_XC_b_NuMber,'NO','YES',session_id,'YES');
		SendManualDial('YES');
	}

// ################################################################################
// Show message that customer has hungup the call before agent has
	function CustomerChanneLGone() {
		debug("<b>CustomerChanneLGone:</b>",2);
		showDiv('CustomerGoneBox');

		//document.osdial_form.callchannel.value = '';
		document.getElementById("callchannel").innerHTML = '';
		document.osdial_form.callserverip.value = '';
		document.getElementById("CustomerGoneChanneL").innerHTML = lastcustchannel;
		if( document.images ) {
			document.images['livecall'].src = image_livecall_OFF.src;
		}
		WaitingForNextStep=1;
	}

	function CustomerGoneOK() {
		debug("<b>CustomerGoneOK:</b>",2);
		hideDiv('CustomerGoneBox');
		WaitingForNextStep=0;
		custchannellive=0;
	}

	function CustomerGoneHangup() {
		debug("<b>CustomerGoneHangup:</b>",2);
		hideDiv('CustomerGoneBox');
		WaitingForNextStep=0;
		custchannellive=0;

		dialedcall_send_hangup();
	}

// ################################################################################
// Show message that there are no voice channels in the OSDIAL session
	function NoneInSession() {
		debug("<b>NoneInSession:</b>",2);
		showDiv('NoneInSessionBox');

		document.getElementById("NoneInSessionID").innerHTML = session_id;
		WaitingForNextStep=1;
	}

	function NoneInSessionOK() {
		debug("<b>NoneInSessionOK:</b>",2);
		hideDiv('NoneInSessionBox');
		WaitingForNextStep=0;
		nochannelinsession=0;
	}

	function NoneInSessionCalL() {
		debug("<b>NoneInSessionCalL:</b>",2);
		hideDiv('NoneInSessionBox');
		WaitingForNextStep=0;
		nochannelinsession=0;

		if (protocol == 'EXTERNAL' || protocol == 'Local') {
			var protodial = 'Local';
			var extendial = extension;
			//var extendial = extension + "@" + ext_context;
		} else {
			var protodial = protocol;
			var extendial = extension;
		}

		var originatevalue = protodial + "/" + extendial;
		var queryCID = "ACagcW" + epoch_sec + user_abb;

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=OriginateVDRelogin&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + session_id + "&ext_context=" + ext_context + "&ext_priority=1" + "&extension=" + extension + "&protocol=" + protocol + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&allow_sipsak_messages=" + allow_sipsak_messages + "&campaign=" + campaign;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}
		if (auto_dial_level > 0) {
			AutoDial_ReSume_PauSe("VDADpause","NEW_ID");
		}
	}


// ################################################################################
// Generate the Closer In Group Chooser panel
	function CloserSelectContent_create() {
		debug("<b>CloserSelectContent_create:</b>",2);
		if (VU_agent_choose_ingroups == '1') {
			var live_CSC_HTML = "<table class=acrossagent cellpadding=5 cellspacing=5 width=500><tr><td align=center><B><font color=<?=$closer_fc?>>Groups Not Selected</font></B></td><td align=center><B><font color=<?=$closer_fc?>>Selected Groups</font></B></td></tr><tr><td bgcolor=\"<?=$closer_bg?>\" height=300 width=240 valign=top><font class=\"log_text\"><span id=CloserSelectAdd>";
			var loop_ct = 0;
			while (loop_ct < INgroupCOUNT) {
				if (VARingroups[loop_ct].substr(0,4) != "A2A_") {
					live_CSC_HTML = live_CSC_HTML + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','ADD');return false;\">";
					if (multicomp > 0) {
						live_CSC_HTML = live_CSC_HTML + VARingroups[loop_ct].substr(3);
					} else {
						live_CSC_HTML = live_CSC_HTML + VARingroups[loop_ct];
					}
					live_CSC_HTML = live_CSC_HTML + "</a><BR>";
				}
				loop_ct++;
			}
			live_CSC_HTML = live_CSC_HTML + "</span></font></td><td height=300 width=240 valign=top bgcolor=\"<?=$closer_bg?>\"><font class=\"log_text\"><span id=CloserSelectDelete></span></font></td></tr></table>";

			document.osdial_form.CloserSelectList.value = '';
			document.getElementById("CloserSelectContent").innerHTML = live_CSC_HTML;
		} else {
			VU_agent_choose_ingroups_DV = "MGRLOCK";
			var live_CSC_HTML = "<br><br><br><table frame=box><tr bgcolor=<?=$closer_bg?>><td><font color=<?=$closer_fc2?>>&nbsp;Manager has selected groups for you!&nbsp;</td></tr></table><br>";
			document.osdial_form.CloserSelectList.value = '';
			document.getElementById("CloserSelectContent").innerHTML = live_CSC_HTML;
		}
	}

// ################################################################################
// Move a Closer In Group record to the selected column or reverse
	function CloserSelect_change(taskCSgrp,taskCSchange) {
		debug("<b>CloserSelect_change:</b> taskCSgrp=" + taskCSgrp + " taskCSchange=" + taskCSchange,2);
		var CloserSelectListValue = document.osdial_form.CloserSelectList.value;
		var CSCchange = 0;
		var regCS = new RegExp(" "+taskCSgrp+" ","ig");
		if ( (CloserSelectListValue.match(regCS)) && (CloserSelectListValue.length > 3) ) {
			if (taskCSchange == 'DELETE') {
				CSCchange = 1;
			}
		} else {
			if (taskCSchange == 'ADD') {
				CSCchange = 1;
			}
		}

		//alert(taskCSgrp+"|"+taskCSchange+"|"+CloserSelectListValue.length+"|"+CSCchange+"|"+CSCcolumn)

		if (CSCchange==1) {
			var loop_ct = 0;
			var CSCcolumn = '';
			var live_CSC_HTML_ADD = '';
			var live_CSC_HTML_DELETE = '';
			var live_CSC_LIST_value = " ";
			while (loop_ct < INgroupCOUNT) {
				var regCSL = new RegExp(" "+VARingroups[loop_ct]+" ","ig");
				if (CloserSelectListValue.match(regCSL)) {
					CSCcolumn = 'DELETE';
				} else {
					CSCcolumn = 'ADD';
				}
				if ( (VARingroups[loop_ct] == taskCSgrp) && (taskCSchange == 'DELETE') ) {
					CSCcolumn = 'ADD';
				}
				if ( (VARingroups[loop_ct] == taskCSgrp) && (taskCSchange == 'ADD') ) {
					CSCcolumn = 'DELETE';
				}

				if (VARingroups[loop_ct].substr(0,4) != "A2A_") {
					if (CSCcolumn == 'DELETE') {
						live_CSC_HTML_DELETE = live_CSC_HTML_DELETE + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','DELETE');return false;\">";
						if (multicomp > 0) {
							live_CSC_HTML_DELETE = live_CSC_HTML_DELETE + VARingroups[loop_ct].substr(3);
						} else {
							live_CSC_HTML_DELETE = live_CSC_HTML_DELETE + VARingroups[loop_ct];
						}
						live_CSC_HTML_DELETE = live_CSC_HTML_DELETE + "</a><BR>";
						live_CSC_LIST_value = live_CSC_LIST_value + VARingroups[loop_ct] + " ";
					} else {
						live_CSC_HTML_ADD = live_CSC_HTML_ADD + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','ADD');return false;\">";
						if (multicomp > 0) {
							live_CSC_HTML_ADD = live_CSC_HTML_ADD + VARingroups[loop_ct].substr(3);
						} else {
							live_CSC_HTML_ADD = live_CSC_HTML_ADD + VARingroups[loop_ct];
						}
						live_CSC_HTML_ADD = live_CSC_HTML_ADD + "</a><BR>";
					}
				}
				loop_ct++;
			}

			document.osdial_form.CloserSelectList.value = live_CSC_LIST_value;
			document.getElementById("CloserSelectAdd").innerHTML = live_CSC_HTML_ADD;
			document.getElementById("CloserSelectDelete").innerHTML = live_CSC_HTML_DELETE;
		}
	}

// ################################################################################
// Update osdial_live_agents record with closer in group choices
	function CloserSelect_submit() {
		debug("<b>CloserSelect_submit:</b>",2);
		if (inbound_man > 0) {
			document.osdial_form.CloserSelectBlended.checked=false;
		}
		if (document.osdial_form.CloserSelectBlended.checked==true) {
			OSDiaL_closer_blended = 1;
		} else {
			OSDiaL_closer_blended = 0;
		}

		var CloserSelectChoices = document.osdial_form.CloserSelectList.value;

		if (VU_agent_choose_ingroups_DV == "MGRLOCK") {
			CloserSelectChoices = "MGRLOCK";
		}

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			CSCupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=regCLOSER&format=text&user=" + user + "&pass=" + pass + "&comments=" + VU_agent_choose_ingroups_DV + "&closer_choice=" + CloserSelectChoices + "-";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(CSCupdate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}

		hideDiv('CloserSelectBox');
		MainPanelToFront();
		CloserSelecting = 0;
	}


// ################################################################################
// Log the user out of the system when they close their browser while logged in
	function BrowserCloseLogout() {
		debug("<b>BrowseCloseLogout:</b>",2);
		if (logout_stop_timeouts < 1) {
			LogouT('CLOSE');
			alert("PLEASE CLICK THE LOGOUT LINK TO LOG OUT NEXT TIME!\n");
		}
	}


// ################################################################################
// Log the user out of the system, if active call or active dial is occuring, don't let them.
	function LogouT(tempreason) {
		debug("<b>LogouT:</b>",2);
		if (manual_dial_menu==1) {
			if (tempreason=='CLOSE') {
				document.osdial_form.DispoSelection.value = 'NA';
				dialedcall_send_hangup('NO','YES');
			} else {
				alert("You cannot log out during a Manual Dial. \nPlease click \"Dial Lead\" or \"Skip Lead\" (if available).");
				return;
			}
		} else if (alt_dial_menu==1) {
			if (tempreason=='CLOSE') {
				document.osdial_form.DispoSelection.value = 'NA';
				dialedcall_send_hangup('NO','YES');
			} else {
				alert("You cannot log out without reattempting or dispositioning this lead. \nYou may reattempt by selecting \"Main Phone\", \"Alt Phone\", or \"Address3\". \n To disposition the lead, click \"Finish Lead\".");
				return;
			}
		} else if (MD_channel_look==1) {
			if (tempreason=='CLOSE') {
				document.osdial_form.DispoSelection.value = 'NA';
				dialedcall_send_hangup('NO','YES');
			} else {
				alert("You cannot log out during a Dial attempt. \nWait 50 seconds for the dial to fail out if it is not answered");
				return;
			}
		} else if (VD_live_customer_call==1) {
			if (tempreason=='CLOSE') {
				document.osdial_form.DispoSelection.value = 'NA';
				dialedcall_send_hangup('NO','YES');
			} else {
				alert("STILL A LIVE CALL! Hang it up then you can log out.\n" + VD_live_customer_call);
				return;
			}
		}
		if (previewFD_time > 0) {
			clearTimeout(previewFD_timeout_id);
			clearInterval(previewFD_display_id);
			document.getElementById("PreviewFDTimeSpan").innerHTML = "";
		}

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			VDlogout_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=userLOGout&format=text&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&agent_log_id=" + agent_log_id + "&no_delete_sessions=" + no_delete_sessions + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&LogouTKicKAlL=" + LogouTKicKAlL + "&ext_context=" + ext_context;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			debug("<b>userLOGout called:</b> vdc_db_query.php?" + VDlogout_query,1);
			xmlhttp.send(VDlogout_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(VDlogout_query);
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}


		if (tempreason=='CLOSE') return;

		hideDiv('MainPanel');
		showDiv('LogouTBox');

			//document.getElementById("LogouTBoxLink").innerHTML = "<a href=\"" + agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass + "\"><img src='images/LoginAgainUp.png' width='128' height='28' align=center border='0'></a>";
				
		document.getElementById("LogouTBoxLink").innerHTML = "<map=Loginmap><a OnMouseOver=\"lagain.src='templates/<?= $agent_template ?>/images/LoginAgainDn.png'\" OnMouseOut=\"lagain.src='templates/<?= $agent_template ?>/images/LoginAgainUp.png'\" usemap=Loginmap href=\"" + agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass + "\"><img src='templates/<?= $agent_template ?>/images/LoginAgainUp.png' width='128' height='28' align=center border='0' name=lagain></a>";

		logout_stop_timeouts = 1;
					
		//window.location= agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass;

	}

<?
if ($useIE > 0) {
?>
// ################################################################################
// MSIE-only hotkeypress function to bind hotkeys defined in the campaign to dispositions
	function hotkeypress(evt) {
		debug("<b>hotkeypress:</b> evt=" + evt,5);
		enter_disable();
		if ( (hot_keys_active==1) && ((VD_live_customer_call==1) || (MD_ring_secondS>5) ) ) {
			var e = evt? evt : window.event;
			if(!e) return;
			var key = 0;
			// for moz/fb, if keyCode==0 use 'which'
			if (e.keyCode) {
				key = e.keyCode;
			} else if (typeof(e.which)!= 'undefined') {
				key = e.which;
			}

			var HKdispo = hotkeys[String.fromCharCode(key)];
			//alert("|" + key + "|" + HKdispo + "|");
			if (HKdispo) {
				//document.osdial_form.inert_button.focus();
				//document.osdial_form.inert_button.blur();
				CustomerData_update();
				var HKdispo_ary = HKdispo.split(" ----- ");
				if ( (HKdispo_ary[0] == 'ALTPH2') || (HKdispo_ary[0] == 'ADDR3') ) {
					if (document.osdial_form.DiaLAltPhonE.checked==true) {
						dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
					}
				} else {
					HKdispo_display = 4;
					HKfinish=1;
					document.getElementById("HotKeyDispo").innerHTML = HKdispo_ary[0] + " - " + HKdispo_ary[1] + " - " + HKdispo_ary[2];
					showDiv('HotKeyActionBox');
					hideDiv('HotKeyEntriesBox');
					document.osdial_form.DispoSelection.value = HKdispo_ary[0];
					if ( (HKdispo_ary[2] == '') ) {
						dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
					} else {
						document.osdial_form.xfernumber.value = HKdispo_ary[2];
						mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
						dialedcall_send_hangup('NO');
						alt_dial_active=0;
						alt_dial_menu=0;
						reselect_alt_dial=0;
						DispoSelect_submit();
						document.osdial_form.xfernumber.value = '';
					}
				}
			}
		} else if ( (dtmf_keys_active==1) && ( (VD_live_customer_call==1) || (MD_ring_secondS>5) ) ) {
			var e = evt? evt : window.event;
			if(!e) return;
			var key = 0;
 			// for moz/fb, if keyCode==0 use 'which'
			if (e.keyCode) {
				key = e.keyCode;
			} else if (typeof(e.which)!= 'undefined') {
				key = e.which;
			}

			var dtmf_key = String.fromCharCode(key);
			if (dtmf_key == "0" || dtmf_key == "1" || dtmf_key == "2" || dtmf_key == "3" || dtmf_key == "4" || dtmf_key == "5" ||
		    	  dtmf_key == "6" || dtmf_key == "7" || dtmf_key == "8" || dtmf_key == "9" || dtmf_key == "#" || dtmf_key == "*") { 
				document.osdial_form.conf_dtmf.value=dtmf_key;
				SendConfDTMF('<?=$session_id ?>');
			}
		}
	}
<?
} else {
?>
// ################################################################################
// W3C-compliant hotkeypress function to bind hotkeys defined in the campaign to dispositions
	function hotkeypress(evt) {
		debug("<b>hotkeypress:</b> evt=" + evt,5);
		enter_disable();
		if ( (hot_keys_active==1) && ( (VD_live_customer_call==1) || (MD_ring_secondS>5) ) ) {
			var e = evt? evt : window.event;
			if(!e) return;
			var key = 0;
			// for moz/fb, if keyCode==0 use 'which'
			if (e.keyCode) {
				key = e.keyCode;
			} else if (typeof(e.which)!= 'undefined') {
				key = e.which;
			}
			var HKdispo = hotkeys[String.fromCharCode(key)];
			if (HKdispo) {
				document.osdial_form.inert_button.focus();
				document.osdial_form.inert_button.blur();
				CustomerData_update();
				var HKdispo_ary = HKdispo.split(" ----- ");
				if ( (HKdispo_ary[0] == 'ALTPH2') || (HKdispo_ary[0] == 'ADDR3') ) {
					if (document.osdial_form.DiaLAltPhonE.checked==true) {
						dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
					}
				} else {
					HKdispo_display = 4;
					HKfinish=1;
					document.getElementById("HotKeyDispo").innerHTML = HKdispo_ary[0] + " - " + HKdispo_ary[1] + " - " + HKdispo_ary[2];
					showDiv('HotKeyActionBox');
					hideDiv('HotKeyEntriesBox');
					document.osdial_form.DispoSelection.value = HKdispo_ary[0];
					if ( (HKdispo_ary[2] == '') ) {
						dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
					} else {
						document.osdial_form.xfernumber.value = HKdispo_ary[2];
						mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
						dialedcall_send_hangup('NO');
						alt_dial_active=0;
						alt_dial_menu=0;
						reselect_alt_dial=0;
						DispoSelect_submit();
						document.osdial_form.xfernumber.value = '';
					}
				}
				//DispoSelect_submit();
				//AutoDialWaiting = 1;
				//AutoDial_ReSume_PauSe("VDADready");
				//alert(HKdispo + " - " + HKdispo_ary[0] + " - " + HKdispo_ary[1]);
			}
		} else if ( (dtmf_keys_active==1) && ( (VD_live_customer_call==1) || (MD_ring_secondS>5) ) ) {
			var e = evt? evt : window.event;
			if(!e) return;
			var key = 0;
			// for moz/fb, if keyCode==0 use 'which'
			if (e.keyCode) {
				key = e.keyCode;
			} else if (typeof(e.which)!= 'undefined') {
				key = e.which;
			}

			var dtmf_key = String.fromCharCode(key);
			if (dtmf_key == "0" || dtmf_key == "1" || dtmf_key == "2" || dtmf_key == "3" || dtmf_key == "4" || dtmf_key == "5" ||
		    	  dtmf_key == "6" || dtmf_key == "7" || dtmf_key == "8" || dtmf_key == "9" || dtmf_key == "#" || dtmf_key == "*") { 
				document.osdial_form.conf_dtmf.value=dtmf_key;
				SendConfDTMF('<?=$session_id ?>');
			}
		}
	}

<?
}
### end of onkeypress functions
?>


// ################################################################################
// disable enter/return keys to not clear out vars on customer info
	function enter_disable(evt) {
		debug("<b>enter_disable:</b> evt=" + evt,5);
		var e = evt? evt : window.event;
		if(!e) return;
		var key = 0;
		// for moz/fb, if keyCode==0 use 'which'
		if (e.keyCode) {
			key = e.keyCode;
		} else if (typeof(e.which)!= 'undefined') {
			key = e.which;
		}
		return key != 13;
	}


// ################################################################################
// An additional encodeURIComponent which also encodes (+) plus.
	function encodeURIComponent2(component) {
		return encodeURIComponent(component).replace(/%20/g, '+');
	}

// ################################################################################
// Do the variable substituion on the given string.
	function webform_rewrite(wf_encoded) {
		debug("<b>webform_rewrite:</b> wf_encoded=" + wf_encoded,2);

		var SCvendor_lead_code = encodeURIComponent2(document.osdial_form.vendor_lead_code.value);
		var SCsource_id = encodeURIComponent2(source_id);
		var SClist_id = encodeURIComponent2(document.osdial_form.list_id.value);
		var SCgmt_offset_now = encodeURIComponent2(document.osdial_form.gmt_offset_now.value);
		var SCcalled_since_last_reset = encodeURIComponent2("");
		var SCphone_code = encodeURIComponent2(document.osdial_form.phone_code.value);
		var SCphone_number = encodeURIComponent2(document.osdial_form.phone_number.value);
		var SCphone = encodeURIComponent2(document.osdial_form.phone_number.value);
		var SCdialed_number = encodeURIComponent2(dialed_number);
		var SCdialed_label = encodeURIComponent2(dialed_label);
		var SCtitle = encodeURIComponent2(document.osdial_form.title.value);
		var SCfirst_name = encodeURIComponent2(document.osdial_form.first_name.value);
		var SCmiddle_initial = encodeURIComponent2(document.osdial_form.middle_initial.value);
		var SClast_name = encodeURIComponent2(document.osdial_form.last_name.value);
		var SCaddress1 = encodeURIComponent2(document.osdial_form.address1.value);
		var SCaddress2 = encodeURIComponent2(document.osdial_form.address2.value);
		var SCaddress3 = encodeURIComponent2(document.osdial_form.address3.value);
		var SCcity = encodeURIComponent2(document.osdial_form.city.value);
		var SCstate = encodeURIComponent2(document.osdial_form.state.value);
		var SCprovince = encodeURIComponent2(document.osdial_form.province.value);
		var SCpostal_code = encodeURIComponent2(document.osdial_form.postal_code.value);
		var SCcountry_code = encodeURIComponent2(document.osdial_form.country_code.value);
		var SCgender = encodeURIComponent2(document.osdial_form.gender.value);
		var SCdate_of_birth = encodeURIComponent2(document.osdial_form.date_of_birth.value);
		var SCalt_phone = encodeURIComponent2(document.osdial_form.alt_phone.value);
		var SCemail = encodeURIComponent2(document.osdial_form.email.value);
		var SCcustom1 = encodeURIComponent2(document.osdial_form.custom1.value);
		var SCcustom2 = encodeURIComponent2(document.osdial_form.custom2.value);
		var SCcomments = encodeURIComponent2(document.osdial_form.comments.value);
		var SCfullname = encodeURIComponent2(LOGfullname);
		var SCfronter = encodeURIComponent2(fronter);
		var SCuser = encodeURIComponent2(user);
		var SCpass = encodeURIComponent2(pass);
		var SClead_id = encodeURIComponent2(document.osdial_form.lead_id.value);
		var SCcampaign = encodeURIComponent2(campaign);
		var SCcampaign_id = encodeURIComponent2(campaign);
		var SCphone_login = encodeURIComponent2(phone_login);
		var SCphone_pass = encodeURIComponent2(phone_pass);
		var SCgroup = encodeURIComponent2(group);
		var SCchannel_group = encodeURIComponent2(group);
		var SCSQLdate = encodeURIComponent2(SQLdate);
		var SCepoch = encodeURIComponent2(UnixTime);
		var SCuniqueid = encodeURIComponent2(document.osdial_form.uniqueid.value);
		var SCcustomer_zap_channel = encodeURIComponent2(lastcustchannel);
		var SCserver_ip = encodeURIComponent2(server_ip);
		var SCSIPexten = encodeURIComponent2(extension);
		var SCsession_id = encodeURIComponent2(session_id);
		var SCdispo = encodeURIComponent2(LeaDDispO);
		var SCdisposition = encodeURIComponent2(LeaDDispO);
		var SCstatus = encodeURIComponent2(LeaDDispO);
		var SCexternal_key = encodeURIComponent2(external_key);
		var SCpost_date = encodeURIComponent2(document.osdial_form.post_date.value);
		var SCrecording_id = encodeURIComponent2(recording_id);
		//var SCwebform_session = encodeURIComponent2(webform_session);


		// New Variable substitution
		var RGvendor_lead_code = new RegExp("\\[\\[vendor_lead_code\\]\\]","g");
		var RGsource_id = new RegExp("\\[\\[source_id\\]\\]","g");
		var RGlist_id = new RegExp("\\[\\[list_id\\]\\]","g");
		var RGgmt_offset_now = new RegExp("\\[\\[gmt_offset_now\\]\\]","g");
		var RGcalled_since_last_reset = new RegExp("\\[\\[called_since_last_reset\\]\\]","g");
		var RGphone_code = new RegExp("\\[\\[phone_code\\]\\]","g");
		var RGphone_number = new RegExp("\\[\\[phone_number\\]\\]","g");
		var RGphone = new RegExp("\\[\\[phone\\]\\]","g");
		var RGdialed_number = new RegExp("\\[\\[dialed_number\\]\\]","g");
		var RGdialed_label = new RegExp("\\[\\[dialed_label\\]\\]","g");
		var RGtitle = new RegExp("\\[\\[title\\]\\]","g");
		var RGfirst_name = new RegExp("\\[\\[first_name\\]\\]","g");
		var RGmiddle_initial = new RegExp("\\[\\[middle_initial\\]\\]","g");
		var RGlast_name = new RegExp("\\[\\[last_name\\]\\]","g");
		var RGaddress1 = new RegExp("\\[\\[address1\\]\\]","g");
		var RGaddress2 = new RegExp("\\[\\[address2\\]\\]","g");
		var RGaddress3 = new RegExp("\\[\\[address3\\]\\]","g");
		var RGcity = new RegExp("\\[\\[city\\]\\]","g");
		var RGstate = new RegExp("\\[\\[state\\]\\]","g");
		var RGprovince = new RegExp("\\[\\[province\\]\\]","g");
		var RGpostal_code = new RegExp("\\[\\[postal_code\\]\\]","g");
		var RGcountry_code = new RegExp("\\[\\[country_code\\]\\]","g");
		var RGgender = new RegExp("\\[\\[gender\\]\\]","g");
		var RGdate_of_birth = new RegExp("\\[\\[date_of_birth\\]\\]","g");
		var RGalt_phone = new RegExp("\\[\\[alt_phone\\]\\]","g");
		var RGemail = new RegExp("\\[\\[email\\]\\]","g");
		var RGcustom1 = new RegExp("\\[\\[custom1\\]\\]","g");
		var RGcustom2 = new RegExp("\\[\\[custom2\\]\\]","g");
		var RGcomments = new RegExp("\\[\\[comments\\]\\]","g");
		var RGfullname = new RegExp("\\[\\[fullname\\]\\]","g");
		var RGfronter = new RegExp("\\[\\[fronter\\]\\]","g");
		var RGuser = new RegExp("\\[\\[user\\]\\]","g");
		var RGpass = new RegExp("\\[\\[pass\\]\\]","g");
		var RGlead_id = new RegExp("\\[\\[lead_id\\]\\]","g");
		var RGcampaign = new RegExp("\\[\\[campaign\\]\\]","g");
		var RGcampaign_id = new RegExp("\\[\\[campaign_id\\]\\]","g");
		var RGphone_login = new RegExp("\\[\\[phone_login\\]\\]","g");
		var RGphone_pass = new RegExp("\\[\\[phone_pass\\]\\]","g");
		var RGgroup = new RegExp("\\[\\[group\\]\\]","g");
		var RGchannel_group = new RegExp("\\[\\[channel_group\\]\\]","g");
		var RGSQLdate = new RegExp("\\[\\[SQLdate\\]\\]","g");
		var RGepoch = new RegExp("\\[\\[epoch\\]\\]","g");
		var RGuniqueid = new RegExp("\\[\\[uniqueid\\]\\]","g");
		var RGcustomer_zap_channel = new RegExp("\\[\\[customer_zap_channel\\]\\]","g");
		var RGserver_ip = new RegExp("\\[\\[server_ip\\]\\]","g");
		var RGSIPexten = new RegExp("\\[\\[SIPexten\\]\\]","g");
		var RGsession_id = new RegExp("\\[\\[session_id\\]\\]","g");
		var RGdispo = new RegExp("\\[\\[dispo\\]\\]","g");
		var RGdisposition = new RegExp("\\[\\[disposition\\]\\]","g");
		var RGstatus = new RegExp("\\[\\[status\\]\\]","g");
		var RGexternal_key = new RegExp("\\[\\[external_key\\]\\]","g");
		var RGpost_date = new RegExp("\\[\\[post_date\\]\\]","g");
		var RGrecording_id = new RegExp("\\[\\[recording_id\\]\\]","g");
		//var RGwebform_session = new RegExp("\\[\\[webform_session\\]\\]","g");
<?
$cnt = 0;
foreach ($forms as $form) {
    $fcamps = split(',',$form['campaigns']);
    foreach ($fcamps as $fcamp) {
        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
	if (is_array($fields)) {
            foreach ($fields as $field) {
                if ($fcamp == 'ALL' or $fcamp == $VD_campaign) {
                    echo "      var SC" . $form['name'] . '_' . $field['name'] . ' = ' . "encodeURIComponent2(document.getElementById('AF" . $field['id'] . "').value);\n";
                    echo "      var RG" . $form['name'] . '_' . $field['name'] . ' = new RegExp("\\\\[\\\\[' . $form['name'] . '_' . $field['name'] . '\\\\]\\\\]","g");' . "\n";
                    echo "      wf_encoded = wf_encoded.replace(RG" . $form['name'] . '_' . $field['name'] . ',SC' . $form['name'] . '_' . $field['name'] . ");\n";
                    $cnt++;
		}
            }
        }
    }
}
?>

		// New substitution
		wf_encoded = wf_encoded.replace(RGvendor_lead_code, SCvendor_lead_code);
		wf_encoded = wf_encoded.replace(RGsource_id, SCsource_id);
		wf_encoded = wf_encoded.replace(RGlist_id, SClist_id);
		wf_encoded = wf_encoded.replace(RGgmt_offset_now, SCgmt_offset_now);
		wf_encoded = wf_encoded.replace(RGcalled_since_last_reset, SCcalled_since_last_reset);
		wf_encoded = wf_encoded.replace(RGphone_code, SCphone_code);
		wf_encoded = wf_encoded.replace(RGphone_number, SCphone_number);
		wf_encoded = wf_encoded.replace(RGphone, SCphone);
		wf_encoded = wf_encoded.replace(RGdialed_number, SCdialed_number);
		wf_encoded = wf_encoded.replace(RGdialed_label, SCdialed_label);
		wf_encoded = wf_encoded.replace(RGtitle, SCtitle);
		wf_encoded = wf_encoded.replace(RGfirst_name, SCfirst_name);
		wf_encoded = wf_encoded.replace(RGmiddle_initial, SCmiddle_initial);
		wf_encoded = wf_encoded.replace(RGlast_name, SClast_name);
		wf_encoded = wf_encoded.replace(RGaddress1, SCaddress1);
		wf_encoded = wf_encoded.replace(RGaddress2, SCaddress2);
		wf_encoded = wf_encoded.replace(RGaddress3, SCaddress3);
		wf_encoded = wf_encoded.replace(RGcity, SCcity);
		wf_encoded = wf_encoded.replace(RGstate, SCstate);
		wf_encoded = wf_encoded.replace(RGprovince, SCprovince);
		wf_encoded = wf_encoded.replace(RGpostal_code, SCpostal_code);
		wf_encoded = wf_encoded.replace(RGcountry_code, SCcountry_code);
		wf_encoded = wf_encoded.replace(RGgender, SCgender);
		wf_encoded = wf_encoded.replace(RGdate_of_birth, SCdate_of_birth);
		wf_encoded = wf_encoded.replace(RGalt_phone, SCalt_phone);
		wf_encoded = wf_encoded.replace(RGemail, SCemail);
		wf_encoded = wf_encoded.replace(RGcustom1, SCcustom1);
		wf_encoded = wf_encoded.replace(RGcustom2, SCcustom2);
		wf_encoded = wf_encoded.replace(RGcomments, SCcomments);
		wf_encoded = wf_encoded.replace(RGfullname, SCfullname);
		wf_encoded = wf_encoded.replace(RGfronter, SCfronter);
		wf_encoded = wf_encoded.replace(RGuser, SCuser);
		wf_encoded = wf_encoded.replace(RGpass, SCpass);
		wf_encoded = wf_encoded.replace(RGlead_id, SClead_id);
		wf_encoded = wf_encoded.replace(RGcampaign, SCcampaign);
		wf_encoded = wf_encoded.replace(RGcampaign_id, SCcampaign_id);
		wf_encoded = wf_encoded.replace(RGphone_login, SCphone_login);
		wf_encoded = wf_encoded.replace(RGphone_pass, SCphone_pass);
		wf_encoded = wf_encoded.replace(RGgroup, SCgroup);
		wf_encoded = wf_encoded.replace(RGchannel_group, SCchannel_group);
		wf_encoded = wf_encoded.replace(RGSQLdate, SCSQLdate);
		wf_encoded = wf_encoded.replace(RGepoch, SCepoch);
		wf_encoded = wf_encoded.replace(RGuniqueid, SCuniqueid);
		wf_encoded = wf_encoded.replace(RGcustomer_zap_channel, SCcustomer_zap_channel);
		wf_encoded = wf_encoded.replace(RGserver_ip, SCserver_ip);
		wf_encoded = wf_encoded.replace(RGSIPexten, SCSIPexten);
		wf_encoded = wf_encoded.replace(RGsession_id, SCsession_id);
		wf_encoded = wf_encoded.replace(RGdispo, SCdispo);
		wf_encoded = wf_encoded.replace(RGdisposition, SCdisposition);
		wf_encoded = wf_encoded.replace(RGstatus, SCstatus);
		wf_encoded = wf_encoded.replace(RGexternal_key, SCexternal_key);
		wf_encoded = wf_encoded.replace(RGpost_date, SCpost_date);
		wf_encoded = wf_encoded.replace(RGrecording_id, SCrecording_id);
		//wf_encoded = wf_encoded.replace(RGwebform_session, SCwebform_session);

		debug("<b>webform_rewrite:</b> DONE wf_encoded=" + wf_encoded,2);
		return wf_encoded;
	}


// ################################################################################
// decode the scripttext and scriptname so that it can be displayed
	function URLDecode(encodedvar,scriptformat) {
		debug("<b>URLDecode:</b> encodedvar=" + encodedvar + " scriptformat=" + scriptformat,2);
		// Replace %ZZ with equivalent character
		// Put [ERR] in output if %ZZ is invalid.
		var HEXCHAR = "0123456789ABCDEFabcdef"; 
		var encoded = encodedvar;
		decoded = '';
		var i = 0;
		var RGnl = new RegExp("[\r]\n","g");
		var RGplus = new RegExp(" ","g");
		var RGiframe = new RegExp("iframe","gi");

		var xtest;
		xtest=unescape(encoded);
		encoded=utf8_decode(xtest);

		if (scriptformat == 'YES') {
			var SCvendor_lead_code = document.osdial_form.vendor_lead_code.value;
			var SCsource_id = source_id;
			var SClist_id = document.osdial_form.list_id.value;
			var SCgmt_offset_now = document.osdial_form.gmt_offset_now.value;
			var SCcalled_since_last_reset = "";
			var SCphone_code = document.osdial_form.phone_code.value;
			var SCphone_number = document.osdial_form.phone_number.value;
			var SCtitle = document.osdial_form.title.value;
			var SCfirst_name = document.osdial_form.first_name.value;
			var SCmiddle_initial = document.osdial_form.middle_initial.value;
			var SClast_name = document.osdial_form.last_name.value;
			var SCaddress1 = document.osdial_form.address1.value;
			var SCaddress2 = document.osdial_form.address2.value;
			var SCaddress3 = document.osdial_form.address3.value;
			var SCcity = document.osdial_form.city.value;
			var SCstate = document.osdial_form.state.value;
			var SCprovince = document.osdial_form.province.value;
			var SCpostal_code = document.osdial_form.postal_code.value;
			var SCcountry_code = document.osdial_form.country_code.value;
			var SCgender = document.osdial_form.gender.value;
			var SCdate_of_birth = document.osdial_form.date_of_birth.value;
			var SCalt_phone = document.osdial_form.alt_phone.value;
			var SCemail = document.osdial_form.email.value;
			var SCcustom1 = document.osdial_form.custom1.value;
			var SCcustom2 = document.osdial_form.custom2.value;
			var SCcomments = document.osdial_form.comments.value;
			var SCfullname = LOGfullname;
			var SCfronter = fronter;
			var SCuser = user;
			var SCpass = pass;
			var SClead_id = document.osdial_form.lead_id.value;
			var SCcampaign = campaign;
			var SCphone_login = phone_login;
			var SCgroup = group;
			var SCchannel_group = group;
			var SCSQLdate = SQLdate;
			var SCepoch = UnixTime;
			var SCuniqueid = document.osdial_form.uniqueid.value;
			var SCcustomer_zap_channel = lastcustchannel;
			var SCserver_ip = server_ip;
			var SCSIPexten = extension;
			var SCsession_id = session_id;
<?
$cnt = 0;
foreach ($forms as $form) {
    $fcamps = split(',',$form['campaigns']);
    foreach ($fcamps as $fcamp) {
        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
	if (is_array($fields)) {
            foreach ($fields as $field) {
                if ($fcamp == 'ALL' or $fcamp == $VD_campaign) {
                    $var = "      var SC" . $form['name'] . '_' . $field['name'] . ' = "';
                    if ($field['options'] == '') {
                        $var .= "<input type=text size=" . $field['length'] . " maxlength=255 name=" . $form['name'] . '_' . $field['name'] . ' id=' . $form['name'] . '_' . $field['name'];
                        $var .= " onfocus=document.getElementById('" . $form['name'] . '_' . $field['name'] . "').value=document.getElementById('AF" . $field['id'] . "').value";
                        $var .= " onchange=document.getElementById('AF" . $field['id'] . "').value=document.getElementById('" . $form['name'] . '_' . $field['name'] . "').value";
                        $var .= ' class=cust_form value=\"\">';
                    } else {
                        $var .= "<select name=" . $form['name'] . '_' . $field['name'] . ' id=' . $form['name'] . '_' . $field['name'];
                        $var .= " onfocus=document.getElementById('" . $form['name'] . '_' . $field['name'] . "').value=document.getElementById('AF" . $field['id'] . "').value";
                        $var .= " onchange=document.getElementById('AF" . $field['id'] . "').value=document.getElementById('" . $form['name'] . '_' . $field['name'] . "').value";
                        $var .= ">";
                        $options = split(',',$field['options']);
                        foreach ($options as $opt) {
                            $var .= "<option>" . $opt . "</option>";
                        }
                        $var .= "</select>";
                    }
                    $var .= '";';
                    echo $var . "\n";
                    $cnt++;
		}
            }
        }
    }
}
?>

			if (encoded.match(RGiframe)) {
				SCvendor_lead_code = SCvendor_lead_code.replace(RGplus,'+');
				SCsource_id = SCsource_id.replace(RGplus,'+');
				SClist_id = SClist_id.replace(RGplus,'+');
				SCgmt_offset_now = SCgmt_offset_now.replace(RGplus,'+');
				SCcalled_since_last_reset = SCcalled_since_last_reset.replace(RGplus,'+');
				SCphone_code = SCphone_code.replace(RGplus,'+');
				SCphone_number = SCphone_number.replace(RGplus,'+');
				SCtitle = SCtitle.replace(RGplus,'+');
				SCfirst_name = SCfirst_name.replace(RGplus,'+');
				SCmiddle_initial = SCmiddle_initial.replace(RGplus,'+');
				SClast_name = SClast_name.replace(RGplus,'+');
				SCaddress1 = SCaddress1.replace(RGplus,'+');
				SCaddress2 = SCaddress2.replace(RGplus,'+');
				SCaddress3 = SCaddress3.replace(RGplus,'+');
				SCcity = SCcity.replace(RGplus,'+');
				SCstate = SCstate.replace(RGplus,'+');
				SCprovince = SCprovince.replace(RGplus,'+');
				SCpostal_code = SCpostal_code.replace(RGplus,'+');
				SCcountry_code = SCcountry_code.replace(RGplus,'+');
				SCgender = SCgender.replace(RGplus,'+');
				SCdate_of_birth = SCdate_of_birth.replace(RGplus,'+');
				SCalt_phone = SCalt_phone.replace(RGplus,'+');
				SCemail = SCemail.replace(RGplus,'+');
				SCcustom1 = SCcustom1.replace(RGplus,'+');
				SCcustom2 = SCcustom2.replace(RGplus,'+');
				SCcomments = SCcomments.replace(RGplus,'+');
				SCfullname = SCfullname.replace(RGplus,'+');
				SCfronter = SCfronter.replace(RGplus,'+');
				SCuser = SCuser.replace(RGplus,'+');
				SCpass = SCpass.replace(RGplus,'+');
				SClead_id = SClead_id.replace(RGplus,'+');
				SCcampaign = SCcampaign.replace(RGplus,'+');
				SCphone_login = SCphone_login.replace(RGplus,'+');
				SCgroup = SCgroup.replace(RGplus,'+');
				SCchannel_group = SCchannel_group.replace(RGplus,'+');
				SCSQLdate = SCSQLdate.replace(RGplus,'+');
				SCuniqueid = SCuniqueid.replace(RGplus,'+');
				SCcustomer_zap_channel = SCcustomer_zap_channel.replace(RGplus,'+');
				SCserver_ip = SCserver_ip.replace(RGplus,'+');
				SCSIPexten = SCSIPexten.replace(RGplus,'+');
<?
$cnt = 0;
foreach ($forms as $form) {
    $fcamps = split(',',$form['campaigns']);
    foreach ($fcamps as $fcamp) {
        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
	if (is_array($fields)) {
            foreach ($fields as $field) {
                if ($fcamp == 'ALL' or $fcamp == $VD_campaign) {
                    echo "      	var SC" . $form['name'] . '_' . $field['name'] . ' = SC' . $form['name'] . '_' . $field['name'] . ".replace(RGplus,'+');\n";
                    $cnt++;
		}
            }
        }
    }
}
?>
			}

			// Old Variable substitution
			var RGOvendor_lead_code = new RegExp("--A--vendor_lead_code--B--","g");
			var RGOsource_id = new RegExp("--A--source_id--B--","g");
			var RGOlist_id = new RegExp("--A--list_id--B--","g");
			var RGOgmt_offset_now = new RegExp("--A--gmt_offset_now--B--","g");
			var RGOcalled_since_last_reset = new RegExp("--A--called_since_last_reset--B--","g");
			var RGOphone_code = new RegExp("--A--phone_code--B--","g");
			var RGOphone_number = new RegExp("--A--phone_number--B--","g");
			var RGOtitle = new RegExp("--A--title--B--","g");
			var RGOfirst_name = new RegExp("--A--first_name--B--","g");
			var RGOmiddle_initial = new RegExp("--A--middle_initial--B--","g");
			var RGOlast_name = new RegExp("--A--last_name--B--","g");
			var RGOaddress1 = new RegExp("--A--address1--B--","g");
			var RGOaddress2 = new RegExp("--A--address2--B--","g");
			var RGOaddress3 = new RegExp("--A--address3--B--","g");
			var RGOcity = new RegExp("--A--city--B--","g");
			var RGOstate = new RegExp("--A--state--B--","g");
			var RGOprovince = new RegExp("--A--province--B--","g");
			var RGOpostal_code = new RegExp("--A--postal_code--B--","g");
			var RGOcountry_code = new RegExp("--A--country_code--B--","g");
			var RGOgender = new RegExp("--A--gender--B--","g");
			var RGOdate_of_birth = new RegExp("--A--date_of_birth--B--","g");
			var RGOalt_phone = new RegExp("--A--alt_phone--B--","g");
			var RGOemail = new RegExp("--A--email--B--","g");
			var RGOcustom1 = new RegExp("--A--custom1--B--","g");
			var RGOcustom2 = new RegExp("--A--custom2--B--","g");
			var RGOcomments = new RegExp("--A--comments--B--","g");
			var RGOfullname = new RegExp("--A--fullname--B--","g");
			var RGOfronter = new RegExp("--A--fronter--B--","g");
			var RGOuser = new RegExp("--A--user--B--","g");
			var RGOpass = new RegExp("--A--pass--B--","g");
			var RGOlead_id = new RegExp("--A--lead_id--B--","g");
			var RGOcampaign = new RegExp("--A--campaign--B--","g");
			var RGOphone_login = new RegExp("--A--phone_login--B--","g");
			var RGOgroup = new RegExp("--A--group--B--","g");
			var RGOchannel_group = new RegExp("--A--channel_group--B--","g");
			var RGOSQLdate = new RegExp("--A--SQLdate--B--","g");
			var RGOepoch = new RegExp("--A--epoch--B--","g");
			var RGOuniqueid = new RegExp("--A--uniqueid--B--","g");
			var RGOcustomer_zap_channel = new RegExp("--A--customer_zap_channel--B--","g");
			var RGOserver_ip = new RegExp("--A--server_ip--B--","g");
			var RGOSIPexten = new RegExp("--A--SIPexten--B--","g");
			var RGOsession_id = new RegExp("--A--session_id--B--","g");

			// New Variable substitution
			var RGvendor_lead_code = new RegExp("\\[\\[vendor_lead_code\\]\\]","g");
			var RGsource_id = new RegExp("\\[\\[source_id\\]\\]","g");
			var RGlist_id = new RegExp("\\[\\[list_id\\]\\]","g");
			var RGgmt_offset_now = new RegExp("\\[\\[gmt_offset_now\\]\\]","g");
			var RGcalled_since_last_reset = new RegExp("\\[\\[called_since_last_reset\\]\\]","g");
			var RGphone_code = new RegExp("\\[\\[phone_code\\]\\]","g");
			var RGphone_number = new RegExp("\\[\\[phone_number\\]\\]","g");
			var RGtitle = new RegExp("\\[\\[title\\]\\]","g");
			var RGfirst_name = new RegExp("\\[\\[first_name\\]\\]","g");
			var RGmiddle_initial = new RegExp("\\[\\[middle_initial\\]\\]","g");
			var RGlast_name = new RegExp("\\[\\[last_name\\]\\]","g");
			var RGaddress1 = new RegExp("\\[\\[address1\\]\\]","g");
			var RGaddress2 = new RegExp("\\[\\[address2\\]\\]","g");
			var RGaddress3 = new RegExp("\\[\\[address3\\]\\]","g");
			var RGcity = new RegExp("\\[\\[city\\]\\]","g");
			var RGstate = new RegExp("\\[\\[state\\]\\]","g");
			var RGprovince = new RegExp("\\[\\[province\\]\\]","g");
			var RGpostal_code = new RegExp("\\[\\[postal_code\\]\\]","g");
			var RGcountry_code = new RegExp("\\[\\[country_code\\]\\]","g");
			var RGgender = new RegExp("\\[\\[gender\\]\\]","g");
			var RGdate_of_birth = new RegExp("\\[\\[date_of_birth\\]\\]","g");
			var RGalt_phone = new RegExp("\\[\\[alt_phone\\]\\]","g");
			var RGemail = new RegExp("\\[\\[email\\]\\]","g");
			var RGcustom1 = new RegExp("\\[\\[custom1\\]\\]","g");
			var RGcustom2 = new RegExp("\\[\\[custom2\\]\\]","g");
			var RGcomments = new RegExp("\\[\\[comments\\]\\]","g");
			var RGfullname = new RegExp("\\[\\[fullname\\]\\]","g");
			var RGfronter = new RegExp("\\[\\[fronter\\]\\]","g");
			var RGuser = new RegExp("\\[\\[user\\]\\]","g");
			var RGpass = new RegExp("\\[\\[pass\\]\\]","g");
			var RGlead_id = new RegExp("\\[\\[lead_id\\]\\]","g");
			var RGcampaign = new RegExp("\\[\\[campaign\\]\\]","g");
			var RGphone_login = new RegExp("\\[\\[phone_login\\]\\]","g");
			var RGgroup = new RegExp("\\[\\[group\\]\\]","g");
			var RGchannel_group = new RegExp("\\[\\[channel_group\\]\\]","g");
			var RGSQLdate = new RegExp("\\[\\[SQLdate\\]\\]","g");
			var RGepoch = new RegExp("\\[\\[epoch\\]\\]","g");
			var RGuniqueid = new RegExp("\\[\\[uniqueid\\]\\]","g");
			var RGcustomer_zap_channel = new RegExp("\\[\\[customer_zap_channel\\]\\]","g");
			var RGserver_ip = new RegExp("\\[\\[server_ip\\]\\]","g");
			var RGSIPexten = new RegExp("\\[\\[SIPexten\\]\\]","g");
			var RGsession_id = new RegExp("\\[\\[session_id\\]\\]","g");
<?
$cnt = 0;
foreach ($forms as $form) {
    $fcamps = split(',',$form['campaigns']);
    foreach ($fcamps as $fcamp) {
        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
	if (is_array($fields)) {
            foreach ($fields as $field) {
                if ($fcamp == 'ALL' or $fcamp == $VD_campaign) {
                    echo "      	var RGO" . $form['name'] . '_' . $field['name'] . ' = new RegExp("--A--' . $form['name'] . '_' . $field['name'] . '--B--","g");' . "\n";
                    echo "      	var RG" . $form['name'] . '_' . $field['name'] . ' = new RegExp("\\\\[\\\\[' . $form['name'] . '_' . $field['name'] . '\\\\]\\\\]","g");' . "\n";
                    echo "      	encoded = encoded.replace(RGO" . $form['name'] . '_' . $field['name'] . ',SC' . $form['name'] . '_' . $field['name'] . ");\n";
                    echo "      	encoded = encoded.replace(RG" . $form['name'] . '_' . $field['name'] . ',SC' . $form['name'] . '_' . $field['name'] . ");\n";
                    $cnt++;
		}
            }
        }
    }
}
?>

			// Old substitution
			encoded = encoded.replace(RGOvendor_lead_code, SCvendor_lead_code);
			encoded = encoded.replace(RGOsource_id, SCsource_id);
			encoded = encoded.replace(RGOlist_id, SClist_id);
			encoded = encoded.replace(RGOgmt_offset_now, SCgmt_offset_now);
			encoded = encoded.replace(RGOcalled_since_last_reset, SCcalled_since_last_reset);
			encoded = encoded.replace(RGOphone_code, SCphone_code);
			encoded = encoded.replace(RGOphone_number, SCphone_number);
			encoded = encoded.replace(RGOtitle, SCtitle);
			encoded = encoded.replace(RGOfirst_name, SCfirst_name);
			encoded = encoded.replace(RGOmiddle_initial, SCmiddle_initial);
			encoded = encoded.replace(RGOlast_name, SClast_name);
			encoded = encoded.replace(RGOaddress1, SCaddress1);
			encoded = encoded.replace(RGOaddress2, SCaddress2);
			encoded = encoded.replace(RGOaddress3, SCaddress3);
			encoded = encoded.replace(RGOcity, SCcity);
			encoded = encoded.replace(RGOstate, SCstate);
			encoded = encoded.replace(RGOprovince, SCprovince);
			encoded = encoded.replace(RGOpostal_code, SCpostal_code);
			encoded = encoded.replace(RGOcountry_code, SCcountry_code);
			encoded = encoded.replace(RGOgender, SCgender);
			encoded = encoded.replace(RGOdate_of_birth, SCdate_of_birth);
			encoded = encoded.replace(RGOalt_phone, SCalt_phone);
			encoded = encoded.replace(RGOemail, SCemail);
			encoded = encoded.replace(RGOcustom1, SCcustom1);
			encoded = encoded.replace(RGOcustom2, SCcustom2);
			encoded = encoded.replace(RGOcomments, SCcomments);
			encoded = encoded.replace(RGOfullname, SCfullname);
			encoded = encoded.replace(RGOfronter, SCfronter);
			encoded = encoded.replace(RGOuser, SCuser);
			encoded = encoded.replace(RGOpass, SCpass);
			encoded = encoded.replace(RGOlead_id, SClead_id);
			encoded = encoded.replace(RGOcampaign, SCcampaign);
			encoded = encoded.replace(RGOphone_login, SCphone_login);
			encoded = encoded.replace(RGOgroup, SCgroup);
			encoded = encoded.replace(RGOchannel_group, SCchannel_group);
			encoded = encoded.replace(RGOSQLdate, SCSQLdate);
			encoded = encoded.replace(RGOepoch, SCepoch);
			encoded = encoded.replace(RGOuniqueid, SCuniqueid);
			encoded = encoded.replace(RGOcustomer_zap_channel, SCcustomer_zap_channel);
			encoded = encoded.replace(RGOserver_ip, SCserver_ip);
			encoded = encoded.replace(RGOSIPexten, SCSIPexten);
			encoded = encoded.replace(RGOsession_id, SCsession_id);

			// New substitution
			encoded = encoded.replace(RGvendor_lead_code, SCvendor_lead_code);
			encoded = encoded.replace(RGsource_id, SCsource_id);
			encoded = encoded.replace(RGlist_id, SClist_id);
			encoded = encoded.replace(RGgmt_offset_now, SCgmt_offset_now);
			encoded = encoded.replace(RGcalled_since_last_reset, SCcalled_since_last_reset);
			encoded = encoded.replace(RGphone_code, SCphone_code);
			encoded = encoded.replace(RGphone_number, SCphone_number);
			encoded = encoded.replace(RGtitle, SCtitle);
			encoded = encoded.replace(RGfirst_name, SCfirst_name);
			encoded = encoded.replace(RGmiddle_initial, SCmiddle_initial);
			encoded = encoded.replace(RGlast_name, SClast_name);
			encoded = encoded.replace(RGaddress1, SCaddress1);
			encoded = encoded.replace(RGaddress2, SCaddress2);
			encoded = encoded.replace(RGaddress3, SCaddress3);
			encoded = encoded.replace(RGcity, SCcity);
			encoded = encoded.replace(RGstate, SCstate);
			encoded = encoded.replace(RGprovince, SCprovince);
			encoded = encoded.replace(RGpostal_code, SCpostal_code);
			encoded = encoded.replace(RGcountry_code, SCcountry_code);
			encoded = encoded.replace(RGgender, SCgender);
			encoded = encoded.replace(RGdate_of_birth, SCdate_of_birth);
			encoded = encoded.replace(RGalt_phone, SCalt_phone);
			encoded = encoded.replace(RGemail, SCemail);
			encoded = encoded.replace(RGcustom1, SCcustom1);
			encoded = encoded.replace(RGcustom2, SCcustom2);
			encoded = encoded.replace(RGcomments, SCcomments);
			encoded = encoded.replace(RGfullname, SCfullname);
			encoded = encoded.replace(RGfronter, SCfronter);
			encoded = encoded.replace(RGuser, SCuser);
			encoded = encoded.replace(RGpass, SCpass);
			encoded = encoded.replace(RGlead_id, SClead_id);
			encoded = encoded.replace(RGcampaign, SCcampaign);
			encoded = encoded.replace(RGphone_login, SCphone_login);
			encoded = encoded.replace(RGgroup, SCgroup);
			encoded = encoded.replace(RGchannel_group, SCchannel_group);
			encoded = encoded.replace(RGSQLdate, SCSQLdate);
			encoded = encoded.replace(RGepoch, SCepoch);
			encoded = encoded.replace(RGuniqueid, SCuniqueid);
			encoded = encoded.replace(RGcustomer_zap_channel, SCcustomer_zap_channel);
			encoded = encoded.replace(RGserver_ip, SCserver_ip);
			encoded = encoded.replace(RGSIPexten, SCSIPexten);
			encoded = encoded.replace(RGsession_id, SCsession_id);
		}
		decoded=encoded;
		return false;
	}


// ################################################################################
// Taken form php.net Angelos
function utf8_decode(utftext) {
	debug("<b>utf8_decode:</b> utftext=" + utftext,5);
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }


// ################################################################################
// Move the Dispo frame out of the way and change the link to maximize
	function DispoMinimize() {
		debug("<b>DispoMinimize:</b>",2);
		showDiv('DispoButtonHideA');
		showDiv('DispoButtonHideB');
		showDiv('DispoButtonHideC');
		document.getElementById("DispoSelectBox").style.top = 340;
		document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMaximize()\">maximize</a>";
	}


// ################################################################################
// Move the Dispo frame to the top and change the link to minimize
	function DispoMaximize() {
		debug("<b>DispoMaximize:</b>",2);
		document.getElementById("DispoSelectBox").style.top = 1;
		document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMinimize()\">minimize</a>";
		hideDiv('DispoButtonHideA');
		hideDiv('DispoButtonHideB');
		hideDiv('DispoButtonHideC');
	}


// ################################################################################
// Hide the CBcommentsBox span upon click
	function CBcommentsBoxhide() {
		debug("<b>CDcommentsBoxhide:</b>",2);
		CBentry_time = '';
		CBcallback_time = '';
		CBuser = '';
		CBcomments = '';
		document.getElementById("CBcommentsBoxA").innerHTML = "";
		document.getElementById("CBcommentsBoxB").innerHTML = "";
		document.getElementById("CBcommentsBoxC").innerHTML = "";
		document.getElementById("CBcommentsBoxD").innerHTML = "";
		hideDiv('CBcommentsBox');
	}


// ################################################################################
// Populating the date field in the callback frame prior to submission
	function CB_date_pick(taskdate) {
		debug("<b>CB_date_pick:</b> taskdate=" + taskdate,2);
		document.osdial_form.CallBackDatESelectioN.value = taskdate;
		document.getElementById("CallBackDatEPrinT").innerHTML = taskdate;
	}


// ################################################################################
// Submitting the callback date and time to the system
	function CallBackDatE_submit() {
		debug("<b>CallBackDatE_submit:</b>",2);
		CallBackDatEForM = document.osdial_form.CallBackDatESelectioN.value;
		CallBackCommenTs = document.osdial_form.CallBackCommenTsField.value;
		if (CallBackDatEForM.length < 2) {
			alert("You must choose a date");
		} else {

<?
if ($useIE > 0) {
?>

			var CallBackTimEHouRFORM = document.getElementById('CBT_hour');
			var CallBackTimEHouR = CallBackTimEHouRFORM[CallBackTimEHouRFORM.selectedIndex].text;
			//var CallBackTimEHouRIDX = CallBackTimEHouRFORM.value;

			var CallBackTimEMinuteSFORM = document.getElementById('CBT_minute');
			var CallBackTimEMinuteS = CallBackTimEMinuteSFORM[CallBackTimEMinuteSFORM.selectedIndex].text;
			//var CallBackTimEMinuteSIDX = CallBackTimEMinuteSFORM.value;

			var CallBackTimEAmpMFORM = document.getElementById('CBT_ampm');
			var CallBackTimEAmpM = CallBackTimEAmpMFORM[CallBackTimEAmpMFORM.selectedIndex].text;
			//var CallBackTimEAmpMIDX = CallBackTimEAmpMFORM.value;

			//alert (CallBackTimEHouR + "|" + CallBackTimEHouRFORM + "|" + CallBackTimEHouRIDX + "|");
			//alert (CallBackTimEMinuteS + "|" + CallBackTimEMinuteSFORM + "|" + CallBackTimEMinuteSIDX + "|");
			//alert (CallBackTimEAmpM + "|" + CallBackTimEAmpMFORM + "|" + CallBackTimEAmpMIDX + "|");

			CallBackTimEHouRFORM.selectedIndex = '0';
			CallBackTimEMinuteSFORM.selectedIndex = '0';
			CallBackTimEAmpMFORM.selectedIndex = '1';
<?
} else {
?>
			CallBackTimEHouR = document.osdial_form.CBT_hour.value;
			CallBackTimEMinuteS = document.osdial_form.CBT_minute.value;
			CallBackTimEAmpM = document.osdial_form.CBT_ampm.value;

			document.osdial_form.CBT_hour.value = '01';
			document.osdial_form.CBT_minute.value = '00';
			document.osdial_form.CBT_ampm.value = 'PM';

<?
}
?>
			if (CallBackTimEHouR == '12') {
				if (CallBackTimEAmpM == 'AM') {
					CallBackTimEHouR = '00';
				}
			} else {
				if (CallBackTimEAmpM == 'PM') {
					CallBackTimEHouR = CallBackTimEHouR * 1;
					CallBackTimEHouR = (CallBackTimEHouR + 12);
				}
			}
			CallBackDatETimE = CallBackDatEForM + " " + CallBackTimEHouR + ":" + CallBackTimEMinuteS + ":00";

			if (document.osdial_form.CallBackOnlyMe.checked==true) {
				CallBackrecipient = 'USERONLY';
			} else {
				CallBackrecipient = 'ANYONE';
			}
			document.getElementById("CallBackDatEPrinT").innerHTML = "Select a Date Below";
			document.osdial_form.CallBackOnlyMe.checked=false;
			document.osdial_form.CallBackDatESelectioN.value = '';
			document.osdial_form.CallBackCommenTsField.value = '';

			//alert(CallBackDatETimE + "|" + CallBackCommenTs);

			document.osdial_form.DispoSelection.value = 'CBHOLD';
			hideDiv('CallBackSelectBox');
			DispoSelect_submit();
		}
	}


// ################################################################################
// Populating the date field in the postdate frame prior to submission
	function PD_date_pick(taskdate) {
		debug("<b>PD_date_pick:</b> taskdate=" + taskdate,2);
		document.osdial_form.PostDatESelectioN.value = taskdate;
		document.getElementById("PostDatEPrinT").innerHTML = taskdate;
	}

// ################################################################################
// Submitting the post date and time to the system
	function PostDatE_submit() {
		debug("<b>PostDatE_submit:</b>",2);
		PostDatEForM = document.osdial_form.PostDatESelectioN.value;
		if (PostDatEForM.length < 2) {
			alert("You must choose a date");
		} else {
			PostDatETimE = PostDatEForM + " " + "00:00:00";

			document.getElementById("PostDatEPrinT").innerHTML = "Select a Date Below";
			document.osdial_form.PostDatESelectioN.value = '';

			document.osdial_form.DispoSelection.value = 'PD';
			hideDiv('PostDateSelectBox');
			DispoSelect_submit();
		}
	}


// ################################################################################
// Finish the wrapup timer early
	function WrapupFinish() {
		debug("<b>WrapupFinish:</b>",2);
		wrapup_counter=999;
	}


// ################################################################################
// GLOBAL FUNCTIONS
	function begin_all_refresh() {
		debug("<b>begin_all_refresh:</b>",2);
		<? if ( ($HK_statuses_camp > 0) && ( ($user_level>=$HKuser_level) or ($VU_hotkeys_active > 0) ) ) {echo "document.onkeypress = hotkeypress;\n";} ?>
		all_refresh();
	}

	function start_all_refresh() {
		debug("<b>start_all_refresh:</b>",5);
		if (OSDiaL_closer_login_checked==0) {
			hideDiv('NothingBox');
			hideDiv('CBcommentsBox');
			hideDiv('HotKeyActionBox');
			hideDiv('HotKeyEntriesBox');
			hideDiv('MainPanel');
			hideDiv('ScriptPanel');
			hideDiv('DispoSelectBox');
			hideDiv('LogouTBox');
			hideDiv('AgenTDisablEBoX');
            		hideDiv('SysteMDisablEBoX');
			hideDiv('CustomerGoneBox');
			hideDiv('NoneInSessionBox');
			hideDiv('WrapupBox');
			hideDiv('TransferMain');
			hideDiv('WelcomeBoxA');
			hideDiv('CallBackSelectBox');
			hideDiv('PostDateSelectBox');
			hideDiv('DispoButtonHideA');
			hideDiv('DispoButtonHideB');
			hideDiv('DispoButtonHideC');
			hideDiv('CallBacKsLisTBox');
			hideDiv('NeWManuaLDiaLBox');
			hideDiv('PauseCodeSelectBox');
			if (agentonly_callbacks != '1') {
				hideDiv('CallbacksButtons');
			}
			//if ( (agentcall_manual != '1') && (starting_dial_level > 0) )
			if (agentcall_manual != '1') {
				hideDiv('ManuaLDiaLButtons');
			}
			if (callholdstatus != '1') {
				hideDiv('AgentStatusCalls');
			}
			if (agentcallsstatus != '1') {
				hideDiv('AgentStatusSpan');
			}
			if ( ( (auto_dial_level > 0) && (inbound_man != 0) ) || (manual_dial_preview < 1) ) {
				clearDiv('DiaLLeaDPrevieW');
			}
			if (alt_phone_dialing != 1) {
				clearDiv('DiaLDiaLAltPhonE');
			}
			if (volumecontrol_active != '1') {
				hideDiv('VolumeControlSpan');
			}
			document.osdial_form.LeadLookuP.checked=true;

			if (agent_pause_codes_active=='Y') {
				document.getElementById("PauseCodeLinkSpan").innerHTML = "<a href=\"#\" onclick=\"PauseCodeSelectContent_create();return false;\">ENTER A PAUSE CODE</a>";
			}
			if (OSDiaL_allow_closers < 1) {
				document.getElementById("LocalCloser").style.visibility = 'hidden';
			}
			document.getElementById("sessionIDspan").innerHTML = session_id;
			if ( (campaign_recording == 'NEVER') || (campaign_recording == 'ALLFORCE') ) {
				document.getElementById("RecorDControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_startrecording_OFF.gif\" border=0 alt=\"Start Recording\">";
			}
			if (INgroupCOUNT > 0 && (dial_method != "MANUAL" || inbound_man > 0)) {
				if (VU_closer_default_blended == 1) {
					document.osdial_form.CloserSelectBlended.checked=true;
				}
				showDiv('CloserSelectBox');
				var CloserSelecting = 1;
				CloserSelectContent_create();
			} else {
				hideDiv('CloserSelectBox');
				MainPanelToFront();
				var CloserSelecting = 0;
				if (inbound_man > 0) {
					inbound_man=0;
					auto_dial_level=0;
					starting_dial_level=0;
					document.getElementById("DiaLControl").innerHTML = DiaLControl_manual_HTML;
				}
			}
			OSDiaL_closer_login_checked = 1;
		} else {

			var WaitingForNextStep=0;
			if (CloserSelecting==1)	{
				WaitingForNextStep=1;
			}
			if (open_dispo_screen==1) {
				wrapup_counter=0;
				if (wrapup_seconds > 0)	{
					showDiv('WrapupBox');
					document.getElementById("WrapupTimer").innerHTML = wrapup_seconds;
					wrapup_waiting=1;
				}
				CustomerData_update();
				showDiv('DispoSelectBox');
				DispoSelectContent_create('','ReSET');
				WaitingForNextStep=1;
				open_dispo_screen=0;
				LIVE_default_xfer_group = default_xfer_group;
				document.getElementById("DispoSelectPhonE").innerHTML = document.osdial_form.phone_number.value;
				if (auto_dial_level == 0) {
					if (document.osdial_form.DiaLAltPhonE.checked==true) {
						reselect_alt_dial = 1;
						document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber.gif\" border=0 alt=\"Dial Next Number\"></a>";

						document.getElementById("MainStatuSSpan").innerHTML = "Dial Next Call";
					} else {
						reselect_alt_dial = 0;
					}
				}
			}
			if (AgentDispoing==1) {
				WaitingForNextStep=1;
				check_for_conf_calls(session_id, '0');
			}
			if (logout_stop_timeouts==1) {
				WaitingForNextStep=1;
			}
			if ( (custchannellive < -30) && (lastcustchannel.length > 3) ) {
				CustomerChanneLGone();
			}
			if ( (custchannellive < -10) && (lastcustchannel.length > 3) ) {
				ReChecKCustoMerChaN();
			}
			if ( (nochannelinsession > 16) && (check_n > 15) ) {
				NoneInSession();
			}
			if (WaitingForNextStep==0) {
				// check for live channels in conference room and get current datetime
				check_for_conf_calls(session_id, '0');
				if (agentonly_callbacks == '1') {
					CB_count_check++;
				}

				if (AutoDialWaiting == 1) {
					check_for_auto_incoming();
				}
				// look for a channel name for the manually dialed call
				if (MD_channel_look==1) {
					ManualDialCheckChanneL(XDcheck);
				}
				if ( (CB_count_check > 19) && (agentonly_callbacks == '1') ) {
					CalLBacKsCounTCheck();
					CB_count_check=0;
				}
				if (VD_live_customer_call==1) {
					VD_live_call_secondS++;
					document.osdial_form.SecondS.value		= VD_live_call_secondS;
				}
				if (XD_live_customer_call==1) {
					XD_live_call_secondS++;
					document.osdial_form.xferlength.value		= XD_live_call_secondS;
				}
				if (HKdispo_display > 0) {
					if ( (HKdispo_display == 3) && (HKfinish==1) ) {
						HKfinish=0;
						DispoSelect_submit();
						//AutoDialWaiting = 1;
						//AutoDial_ReSume_PauSe("VDADready");
					}
					if (HKdispo_display == 1) {
						if (hot_keys_active==1) {
							showDiv('HotKeyEntriesBox');
						}
						hideDiv('HotKeyActionBox');
					}
					HKdispo_display--;
				}
				if (all_record == 'YES') {
					if (all_record_count < allcalls_delay) {
						all_record_count++;
					} else {
						conf_send_recording('MonitorConf',session_id ,'');
						all_record = 'NO';
						all_record_count=0;
					}
				}

				if (active_display==1) {
					check_s = check_n.toString();
					if ( (check_s.match(/00$/)) || (check_n<2) ) {
						//check_for_conf_calls();
					}
				}
				if (check_n<2) {
					//nothing?
				} else {
					//check_for_live_calls();
					check_s = check_n.toString();
					if ( (park_refresh > 0) && (check_s.match(/0$|5$/)) ) {
						//parked_calls_display_refresh();
					}
				}
				if (wrapup_seconds > 0)	{
					document.getElementById("WrapupTimer").innerHTML = (wrapup_seconds - wrapup_counter);
					wrapup_counter++;
					if ( (wrapup_counter > wrapup_seconds) && (document.getElementById("WrapupBox").style.visibility == 'visible') ) {
						wrapup_waiting=0;
						hideDiv('WrapupBox');
						if (document.osdial_form.DispoSelectStop.checked==true) {
							if (auto_dial_level != '0') {
								AutoDialWaiting = 0;
								AutoDial_ReSume_PauSe("VDADpause","NEW_ID");
								//document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
							}
							OSDiaL_pause_calling = 1;
							if (dispo_check_all_pause != '1') {
								document.osdial_form.DispoSelectStop.checked=false;
							}
						} else {
							if (auto_dial_level != '0') {
								AutoDialWaiting = 1;
								AutoDial_ReSume_PauSe("VDADready","NEW_ID","WRAPUP");
								//document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
							}
						}
					}
				}
			}
		}
		setTimeout("all_refresh()", refresh_interval);
	}


	function all_refresh() {
		epoch_sec++;
		debug("<b>all_refresh:</b> " + epoch_sec,5);
		check_n++;
		var year= t.getYear()
		var month= t.getMonth()
		month++;
		var daym= t.getDate()
		var hours = t.getHours();
		var min = t.getMinutes();
		var sec = t.getSeconds();
		if (year < 1000) {
			year+=1900;
		}
		if (month< 10) {
			month= "0" + month;
		}
		if (daym< 10) {
			daym= "0" + daym;
		}
		if (hours < 10) {
			hours = "0" + hours;
		}
		if (min < 10) {
			min = "0" + min;
		}
		if (sec < 10) {
			sec = "0" + sec;
		}
		var Tyear = (year-2000);
		filedate = year + "" + month + "" + daym + "-" + hours + "" + min + "" + sec;
		tinydate = Tyear + "" + month + "" + daym + "" + hours + "" + min + "" + sec;
		SQLdate = year + "-" + month + "-" + daym + " " + hours + ":" + min + ":" + sec;
		document.getElementById("status").innerHTML = year + "-" + month + "-" + daym + " " + hours + ":" + min + ":" + sec  + display_message;
		if (VD_live_customer_call==1) {
			var customer_gmt = parseFloat(document.osdial_form.gmt_offset_now.value);
			var AMPM = 'AM';
			var customer_gmt_diff = (customer_gmt - local_gmt);
			var UnixTimec = (UnixTime + (3600 * customer_gmt_diff));
			var UnixTimeMSc = (UnixTimec * 1000);
			c.setTime(UnixTimeMSc);
			var Cmon= c.getMonth()
			//Cmon++;
			var Cdaym= c.getDate()
			var Chours = c.getHours();
			var Cmin = c.getMinutes();
			var Csec = c.getSeconds();
			if (Cmon < 10) {
				Cmon= "0" + Cmon;
			}
			if (Cdaym < 10) {
				Cdaym= "0" + Cdaym;
			}
			if (Chours < 10) {
				Chours = "0" + Chours;
			}
			if ( (Cmin < 10) && (Cmin.length < 2) ) {
				Cmin = "0" + Cmin;
			}
			if ( (Csec < 10) && (Csec.length < 2) ) {
				Csec = "0" + Csec;
			}

			if (Cmon == 0) {
				Cmon = "JAN";
			}
			if (Cmon == 1) {
				Cmon = "FEB";
			}
			if (Cmon == 2) {
				Cmon = "MAR";
			}
			if (Cmon == 3) {
				Cmon = "APR";
			}
			if (Cmon == 4) {
				Cmon = "MAY";
			}
			if (Cmon == 5) {
				Cmon = "JUN";
			}
			if (Cmon == 6) {
				Cmon = "JLY";
			}
			if (Cmon == 7) {
				Cmon = "AUG";
			}
			if (Cmon == 8) {
				Cmon = "SEP";
			}
			if (Cmon == 9) {
				Cmon = "OCT";
			}
			if (Cmon == 10) {
				Cmon = "NOV";
			}
			if (Cmon == 11) {
				Cmon = "DEC";
			}

			if (Chours == 12) {
				AMPM = 'PM';
			}
			if (Chours > 12) {
				Chours = (Chours - 12);
				AMPM = 'PM';
			}

			if (Cmin < 10) {
				Cmin = "0" + Cmin;
			}
			if (Csec < 10) {
				Csec = "0" + Csec;
			}

			var customer_local_time = Cmon + " " + Cdaym + "   " + Chours + ":" + Cmin + ":" + Csec + " " + AMPM;
			document.osdial_form.custdatetime.value		= customer_local_time;

		}
		start_all_refresh();
	}

// Pauses the refreshing of the lists
	function pause() {
		debug("<b>pause:</b>",2);
		active_display=2;
		display_message="  - ACTIVE DISPLAY PAUSED - ";
	}

// resumes the refreshing of the lists
	function start() {
		debug("<b>start:</b>",2);
		active_display=1;
		display_message='';
	}

// lowers by 1000 milliseconds the time until the next refresh
	function faster() {
		debug("<b>faster:</b>",2);
		if (refresh_interval>1001) {
			refresh_interval=(refresh_interval - 1000);
		}
	}


// raises by 1000 milliseconds the time until the next refresh
	function slower() {
		debug("<b>slower:</b>",2);
		refresh_interval=(refresh_interval + 1000);
	}



// activeext-specific functions


// forces immediate refresh of list content
	function activeext_force_refresh() {
		debug("<b>activeext_force_refresh:</b>",2);
		getactiveext();
	}

// changes order of activeext list to ascending
	function activeext_order_asc() {
		debug("<b>activeext_order_asc:</b>",2);
		activeext_order="asc";
		getactiveext();
		desc_order_HTML ='<a href="#" onclick="activeext_order_desc();return false;">ORDER</a>';
		document.getElementById("activeext_order").innerHTML = desc_order_HTML;
	}

// changes order of activeext list to descending
	function activeext_order_desc() {
		debug("<b>activeext_order_desc:</b>",2);
		activeext_order="desc";   getactiveext();
		asc_order_HTML ='<a href="#" onclick="activeext_order_asc();return false;">ORDER</a>';
		document.getElementById("activeext_order").innerHTML = asc_order_HTML;
	}



// busytrunk-specific functions


// forces immediate refresh of list content
	function busytrunk_force_refresh() {
		debug("<b>busytrunk_force_refresh:</b>",2);
		getbusytrunk();
	}

// changes order of busytrunk list to ascending
	function busytrunk_order_asc() {
		debug("<b>busytrunk_order_asc:</b>",2);
		busytrunk_order="asc";
		getbusytrunk();
		desc_order_HTML ='<a href="#" onclick="busytrunk_order_desc();return false;">ORDER</a>';
		document.getElementById("busytrunk_order").innerHTML = desc_order_HTML;
	}

// changes order of busytrunk list to descending
	function busytrunk_order_desc() {
		debug("<b>busytrunk_order_desc:</b>",2);
		busytrunk_order="desc";
		getbusytrunk();
		asc_order_HTML ='<a href="#" onclick="busytrunk_order_asc();return false;">ORDER</a>';
		document.getElementById("busytrunk_order").innerHTML = asc_order_HTML;
	}

// forces immediate refresh of list content
	function busytrunkhangup_force_refresh() {
		debug("<b>busytrunkhangup_force_refresh:</b>",2);
		busytrunkhangup();
	}

	

// busyext-specific functions

// forces immediate refresh of list content
	function busyext_force_refresh() {
		debug("<b>busyext_force_refresh:</b>",2);
		getbusyext();
	}

// changes order of busyext list to ascending
	function busyext_order_asc() {
		debug("<b>busyext_order_asc:</b>",2);
		busyext_order="asc";
		getbusyext();
		desc_order_HTML ='<a href="#" onclick="busyext_order_desc();return false;">ORDER</a>';
		document.getElementById("busyext_order").innerHTML = desc_order_HTML;
	}

// changes order of busyext list to descending
	function busyext_order_desc() {
		debug("<b>busyext_order_desc:</b>",2);
		busyext_order="desc";
		getbusyext();
		asc_order_HTML ='<a href="#" onclick="busyext_order_asc();return false;">ORDER</a>';
		document.getElementById("busyext_order").innerHTML = asc_order_HTML;
	}

// forces immediate refresh of list content
	function busylocalhangup_force_refresh() {
		debug("<b>busylocalhangup_force_refresh:</b>",2);
		busylocalhangup();
	}


// functions to hide and show different DIVs
	function showDiv(divvar) {
		debug("<b>showDiv:</b> divvar=" + divvar,4);
		if (document.getElementById(divvar)) {
			divref = document.getElementById(divvar).style;
			divref.visibility = 'visible';
		}
	}

	function hideDiv(divvar) {
		debug("<b>hideDiv:</b> divvar=" + divvar,4);
		if (document.getElementById(divvar)) {
			divref = document.getElementById(divvar).style;
			divref.visibility = 'hidden';
		}
	}

	function clearDiv(divvar) {
		debug("<b>clearDiv:</b> divvar=" + divvar,4);
		if (document.getElementById(divvar)) {
			document.getElementById(divvar).innerHTML = '';
			if (divvar == 'DiaLLeaDPrevieW') {
				var buildDivHTML = "<font class=\"preview_text\"> <input type=checkbox name=LeadPreview size=1 value=\"0\"> LEAD PREVIEW<BR></font>";
				document.getElementById("DiaLLeaDPrevieWHide").innerHTML = buildDivHTML;
			}
			if (divvar == 'DiaLDiaLAltPhonE') {
				var buildDivHTML = "<font class=\"preview_text\"> <input type=checkbox name=DiaLAltPhonE size=1 value=\"0\"> ALT PHONE DIAL<BR></font>";
				document.getElementById("DiaLDiaLAltPhonEHide").innerHTML = buildDivHTML;
			}
		}
	}

	function buildDiv(divvar) {
		debug("<b>buildDiv:</b> divvar=" + divvar,4);
		if (document.getElementById(divvar)) {
			var buildDivHTML = "";
			if (divvar == 'DiaLLeaDPrevieW') {
				document.getElementById("DiaLLeaDPrevieWHide").innerHTML = '';
				var buildDivHTML = "<font class=\"preview_text\"> <input type=checkbox name=LeadPreview size=1 value=\"0\"> LEAD PREVIEW<BR></font>";
				document.getElementById(divvar).innerHTML = buildDivHTML;
				if (reselect_preview_dial==1) {
					document.osdial_form.LeadPreview.checked=true;
				}
			}
			if (divvar == 'DiaLDiaLAltPhonE') {
				document.getElementById("DiaLDiaLAltPhonEHide").innerHTML = '';
				var buildDivHTML = "<font class=\"preview_text\"> <input type=checkbox name=DiaLAltPhonE size=1 value=\"0\"> ALT PHONE DIAL<BR></font>";
				document.getElementById(divvar).innerHTML = buildDivHTML;
				if (reselect_alt_dial==1) {
					document.osdial_form.DiaLAltPhonE.checked=true;
				}
			}
		}
	}

	function conf_channels_detail(divvar) {
		debug("<b>conf_channels_detail:</b> divvar=" + divvar,2);
		if (divvar == 'SHOW') {
			conf_channels_xtra_display = 1;
			document.getElementById("busycallsdisplay").innerHTML = "<a href=\"#\"  onclick=\"conf_channels_detail('HIDE');\">Hide conference call channel information</a>";
			LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
			LMAcount=0;
		} else {
			conf_channels_xtra_display = 0;
			document.getElementById("busycallsdisplay").innerHTML = "<a href=\"#\"  onclick=\"conf_channels_detail('SHOW');\">Show conference call channel information</a><BR><BR>&nbsp;";
			document.getElementById("outboundcallsspan").innerHTML = '';
			LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
			LMAcount=0;
		}
	}

	function HotKeys(HKstate) {
		debug("<b>HotKeys:</b> HKstate=" + HKstate,2);
		if ( (HKstate == 'ON') && (HKbutton_allowed == 1) ) {
			showDiv('HotKeyEntriesBox');
			hot_keys_active = 1;
			document.getElementById("hotkeysdisplay").innerHTML = "<a href=\"#\" onMouseOut=\"HotKeys('OFF')\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_hotkeysactive.gif\" border=0 alt=\"HOT KEYS ACTIVE\"></a>";
		} else {
			hideDiv('HotKeyEntriesBox');
			hot_keys_active = 0;
			document.getElementById("hotkeysdisplay").innerHTML = "<a href=\"#\" onMouseOver=\"HotKeys('ON')\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_XB_hotkeysactive_OFF.gif\" border=0 alt=\"HOT KEYS INACTIVE\"></a>";
		}
	}

	function DTMFKeys(DTMFstate) {
		debug("<b>DTMFKeys:</b> DTMFstate=" + DTMFstate,2);
		if ( (DTMFstate == 'ON') && (VD_live_customer_call == 1) ) {
			dtmf_keys_active = 1;
			document.getElementById("DTMFDialPad").setAttribute("onMouseOut", "DTMFKeys('OFF');");
		} else {
			dtmf_keys_active = 0;
			document.getElementById("DTMFDialPad").setAttribute("onMouseOver", "DTMFKeys('ON');");
		}
	}

	function ShoWTransferMain(showxfervar,showoffvar) {
		debug("<b>ShoWTransferMain:</b> showxfervar=" + showxfervar + " showoffvar=" + showoffvar,2);
		if (VU_osdial_transfers == '1') {
			if (showxfervar == 'ON') {
				var xfer_height = <?=$HTheight ?>;
				if (alt_phone_dialing>0) {
					xfer_height = (xfer_height + 20);
				}
				if ( (auto_dial_level == 0) && (manual_dial_preview == 1) ) {
					xfer_height = (xfer_height + 20);
				}
				document.getElementById("TransferMain").style.top = xfer_height;
				HKbutton_allowed = 0;
				showDiv('TransferMain');
				document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('OFF','YES');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_transferconf.gif\" border=0 alt=\"Transfer - Conference\"></a>";
				var loop_ct = 0;
				var live_XfeR_HTML = '';
				var XfeR_SelecT = '';
				while (loop_ct < XFgroupCOUNT) {
					if (VARxfergroups[loop_ct] == LIVE_default_xfer_group) {
						XfeR_SelecT = 'SELECTED ';
					} else {
						XfeR_SelecT = '';
					}
					live_XfeR_HTML = live_XfeR_HTML + "<option " + XfeR_SelecT + "value=\"" + VARxfergroups[loop_ct] + "\">";
					if (VARxfergroups[loop_ct].substr(0,4) == "A2A_") {
                                                live_XfeR_HTML = live_XfeR_HTML + "Agent " + VARxfergroups[loop_ct].substr(4);
                                        } else {
						if (multicomp > 0) {
							live_XfeR_HTML = live_XfeR_HTML + VARxfergroups[loop_ct].substr(3);
						} else {
							live_XfeR_HTML = live_XfeR_HTML + VARxfergroups[loop_ct];
						}
						live_XfeR_HTML = live_XfeR_HTML + " - " + VARxfergroupsnames[loop_ct];
					}
					live_XfeR_HTML = live_XfeR_HTML + "</option>\n";
					loop_ct++;
				}

				document.getElementById("XfeRGrouPLisT").innerHTML = "<select size=1 name=XfeRGrouP class=\"cust_form\" id=XfeRGrouP>" + live_XfeR_HTML + "</select>";
			} else {
				HKbutton_allowed = 1;
				hideDiv('TransferMain');
				if (showoffvar == 'YES') {
					document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_transferconf.gif\" border=0 alt=\"Transfer - Conference\"></a>";
				}
			}
		} else {
			if (showxfervar != 'OFF') {
				alert('You do not have permissions to transfer calls.');
			}
		}
	}

	function MainPanelToFront(resumevar) {
		debug("<b>MainPanelToFront:</b> resumevar=" + resumevar,2);
		document.getElementById("MainTable").style.backgroundColor="<?=$panel_bg?>";
		document.getElementById("MaiNfooter").style.backgroundColor="<?=$panel_bg?>";
		hideDiv('ScriptPanel');
		showDiv('MainPanel');
		if (resumevar != 'NO') {
			if (alt_phone_dialing == 1) {
				buildDiv('DiaLDiaLAltPhonE');
			} else {
				clearDiv('DiaLDiaLAltPhonE');
			}
			if (auto_dial_level == 0) {
				if (auto_dial_alt_dial==1) {
					auto_dial_alt_dial=0;
					document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
				} else {
					document.getElementById("DiaLControl").innerHTML = DiaLControl_manual_HTML;
					if (manual_dial_preview == 1) {
						buildDiv('DiaLLeaDPrevieW');
					}
				}
			} else {
				if (inbound_man > 0) {
					document.getElementById("DiaLControl").innerHTML = "<IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_pause_OFF.gif\" border=0 alt=\" Pause \"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_resume.gif\" border=0 alt=\"Resume\"></a><BR><IMG SRC=\"templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif\" border=0 alt=\"Dial Next Number\">";
					if (manual_dial_preview == 1) {
						buildDiv('DiaLLeaDPrevieW');
					}
				} else {
					document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
					clearDiv('DiaLLeaDPrevieW');
				}
			}
		}
		panel_bgcolor='<?=$panel_bg?>';
		//document.getElementById("MainStatuSSpan").style.backgroundColor = panel_bgcolor;
		document.getElementById("FormButtons").src = "templates/<?= $agent_template ?>/images/vdc_tab_buttons1.gif";
	}

	function ScriptPanelToFront() {
		debug("<b>ScriptPanelToFront:</b>",2);
		showDiv('ScriptPanel');
		document.getElementById("MainTable").style.backgroundColor="<?=$script_bg?>";
		document.getElementById("MaiNfooter").style.backgroundColor="<?=$script_bg?>";
		panel_bgcolor='<?=$panel_bg?>';
		//document.getElementById("MainStatuSSpan").style.backgroundColor= panel_bgcolor;
		document.getElementById("FormButtons").src = "templates/<?= $agent_template ?>/images/vdc_tab_buttons2.gif";
	}
	
	function ChangeImageX(img, new_src) {
		debug("<b>ChangeImageX:</b> img=" + img + " new_src=" + new_src,2);
		var cur_src = img.src.substring(img.src.lastIndexOf("/")+1);
		
		if (cur_src == new_src) {
			img.src = img.old_src;
		} else {
			img.old_src = cur_src;
			img.src = new_src;
		}
	}

	function ChooseForm() {
		debug("<b>ChooseForm:</b>",2);
		var main_img = "vdc_tab_buttons1.gif";
		var scrpt_img = "vdc_tab_buttons2.gif";

		var img = document.getElementById("FormButtons");
		var cur_src = img.src.substring(img.src.lastIndexOf("/")+1);

		if (allow_tab_switch == 'Y' || CalL_allow_tab == 'Y') {
			if (cur_src == scrpt_img) {
				MainPanelToFront('NO');
			} else {
				ScriptPanelToFront();
			}
		}
	}
	
	function imageSwap(buttonID, img1) {
		debug("<b>imageSwap:</b> buttonID=" + buttonID + " img1=" + img1,2);
		document.getElementById(buttonID).src = img1;
	}

	function AddtlFormOver() {
		debug("<b>AddtlFormOver:</b>",2);
		document.getElementById('AddtlFormTab').style.visibility='hidden'; 
		document.getElementById('AddtlFormTabExpanded').style.visibility='visible'; 
	}

	function AddtlFormButOver(AFform) {
		debug("<b>AddtlFormButOver:</b> AFform=" + AFform,2);
		document.getElementById('AddtlFormBut' + AFform).style.background='url(templates/<?= $agent_template ?>/images/agentsidetab_select.png)'; 
	}

	function AddtlFormButOut(AFform) {
		debug("<b>AddtlFormButOut:</b> AFform=" + AFform,2);
		document.getElementById('AddtlFormBut' + AFform).style.background='url(templates/<?= $agent_template ?>/images/agentsidetab_extra.png)'; 
	}

	function AddtlFormSelect(AFform) {
		debug("<b>AddtlFormSelect:</b> AFform=" + AFform,2);
		if (AFform != 'Cancel') {
			document.getElementById('AddtlFormBut' + AFform).style.background='url(templates/<?= $agent_template ?>/images/agentsidetab_press.png)'; 
<?
    foreach ($forms as $form) {
        $fcamps = split(',',$form['campaigns']);
        foreach ($fcamps as $fcamp) {
            if ($fcamp == 'ALL' or $fcamp == $VD_campaign) {
                echo "      document.getElementById('AddtlForms" . $form['name'] . "').style.visibility='hidden';\n";
            }
        }
    }
?>
			document.getElementById('AddtlForms' + AFform).style.visibility='visible'; 
			document.getElementById('AddtlFormBut' + AFform).style.background='url(templates/<?= $agent_template ?>/images/agentsidetab_extra.png)'; 
		}
		document.getElementById('AddtlFormTabExpanded').style.visibility='hidden'; 
		document.getElementById('AddtlFormTab').style.visibility='visible'; 
	}

	function scriptUpdateFields() {
		debug("<b>scriptUpdateFields:</b>",2);
<?
$cnt = 0;
foreach ($forms as $form) {
    $fcamps = split(',',$form['campaigns']);
    foreach ($fcamps as $fcamp) {
        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
	if (is_array($fields)) {
            foreach ($fields as $field) {
                if ($fcamp == 'ALL' or $fcamp == $VD_campaign) {
                    echo "    try {\n";
                    echo "      document.getElementById(\"" . $form['name'] . '_' . $field['name'] . "\").value = document.getElementById(\"AF" . $field['id'] . "\").value;\n";
		    echo "    }\n";
                    echo "    catch(error) {\n";
		    echo "      var a=1;\n";
		    echo "    }\n";
                    $cnt++;
                }
            }
        }
    }
}
?>
	}

	function previewFDDisplayTime() {
		debug("<b>previewFDDisplayTime:</b>",3);
		if (previewFD_time > 0 ) {
			if ( previewFD_time_remaining > 0 ) {
				previewFD_time_remaining--;
				document.getElementById("PreviewFDTimeSpan").innerHTML = "Dialing in " + previewFD_time_remaining + "...";
			} else {
				document.getElementById("PreviewFDTimeSpan").innerHTML = "";
				clearInterval(previewFD_display_id);
			}
		}
	}

	function WebFormPanelDisplay(webform) {
		debug("<b>WebFormPanelDisplay:</b> webform=" + webform,2);
		WebFormRefresH();
		if (web_form2_extwindow == 0 && web_form_frame_open2 > 0) {
			document.getElementById('WebFormPanel2').style.visibility='hidden'; 
			web_form_frame_open2 = 1;
		}
		if (web_form_frame_open1 == 0) {
			document.getElementById('WebFormPF1').src=webform;
			document.getElementById('WebFormPanel1').style.visibility='visible'; 
			web_form_frame_open1 = 2;
		} else if (web_form_frame_open1 == 1) {
			document.getElementById('WebFormPanel1').style.visibility='visible'; 
			web_form_frame_open1 = 2;
		} else if (web_form_frame_open1 == 2) {
			document.getElementById('WebFormPanel1').style.visibility='hidden'; 
			web_form_frame_open1 = 1;
		}
	}

	function WebFormPanelDisplay2(webform) {
		debug("<b>WebFormPanelDisplay2:</b> webform=" + webform,2);
		WebFormRefresH();
		if (web_form_extwindow == 0 && web_form_frame_open1 > 0) {
			document.getElementById('WebFormPanel1').style.visibility='hidden'; 
			web_form_frame_open1 = 1;
		}
		if (web_form_frame_open2 == 0) {
			document.getElementById('WebFormPF2').src=webform;
			document.getElementById('WebFormPanel2').style.visibility='visible'; 
			web_form_frame_open2 = 2;
		} else if (web_form_frame_open2 == 1) {
			document.getElementById('WebFormPanel2').style.visibility='visible'; 
			web_form_frame_open2 = 2;
		} else if (web_form_frame_open2 == 2) {
			document.getElementById('WebFormPanel2').style.visibility='hidden'; 
			web_form_frame_open2 = 1;
		}
	}

	function CloseWebFormPanels() {
		debug("<b>CloseWebFormPanels:</b>",2);
		if (web_form_extwindow == 0) {
			if (web_form_frame_open1 > 0) {
				document.getElementById('WebFormPanel1').style.visibility = 'hidden';
			}
			web_form_frame_open1 = 0;
			document.getElementById('WebFormPF1').src = '/osdial/agent/blank.php';
		}
		if (web_form2_extwindow == 0) {
			if (web_form_frame_open2 > 0) {
				document.getElementById('WebFormPanel2').style.visibility = 'hidden';
			}
			web_form_frame_open2 = 0;
			document.getElementById('WebFormPF2').src = '/osdial/agent/blank.php';
		}
	}

	function openDebugWindow() {
		if (debugWindowOpened==1) {
			if (debugWindow.closed) {
				debugWindowOpened=0;
			}
		}
		if (debugWindowOpened==0) {
			debugWindow = window.open("", 'osddebug', 'dependent=1,toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=800,height=300');
			var dhead = debugWindow.document.createElement("div");
			dhead.innerHTML = "<h2>OSDial Debug<h2>";
			debugWindow.document.body.appendChild(dhead);
			debugWindowOpened++;
		}
		debugLevel++;
		if (debugLevel > 5) debugLevel=1;
		var dh = debugWindow.document.createElement("div");
		dh.innerHTML = "<b>Setting Debug Level to " + debugLevel + "</b><br>";
		debugWindow.document.body.appendChild(dh);
		debugWindow.focus();
	}

	function debug(debugOutput,dlevel) {
		if (!dlevel) dlevel=1;
		if (debugWindowOpened==1) {
			if (!debugWindow.closed) {
				if (dlevel <= debugLevel) {
					var dh = debugWindow.document.createElement("div");
					dh.setAttribute("style","color:" + debugLevelColors[dlevel] + ";font-size:8pt;");
					dh.innerHTML = debugOutput + "<br><br>";
					debugWindow.document.body.appendChild(dh);
				}
			}
		}
	}

// ################################################################################
// Log the button click in a script.
	function ScriptButtonLog(sid,sbid) {

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			xmlhttp = new XMLHttpRequest();
		}
		if (xmlhttp) { 
			sbl_data = "ACTION=ScriptButtonLog&format=text&user=" + user + "&pass=" + pass + "&lead_id=" + document.osdial_form.lead_id.value + "&script_id=" + sid + "&script_button_id=" + sbid;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(sbl_data); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//alert(xmlhttp.responseText);
				}
			}
			delete xmlhttp;
		}
	}
