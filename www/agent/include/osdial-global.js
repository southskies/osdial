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

var aapath="templates/"+agent_template+"/images/";
var active_display=1;
var activeext_order='asc';
var activeext_query;
var AgaiNCalLCID='';
var AgainCalLSecondS='';
var AgaiNHanguPChanneL='';
var AgaiNHanguPServeR='';
var agc_dial_prefix=dial_prefix+'1';
var agentchannel='';
var AgentDispoing=0;
var agentphonelive=0;
var all_record_count=0;
var all_record='NO';
var alt_dial_active=0;
var alt_dial_menu=0;
var auto_dial_alt_dial=0;
var AutoDialReady=0;
var AutoDialWaiting=0;
var busyext_order='asc';
var busyext_query;
var busylocalhangup_order='asc';
var busylocalhangup_query;
var busytrunkhangup_order='asc';
var busytrunkhangup_query;
var busytrunk_order='asc';
var busytrunk_query;
var CalL_allow_tab='';
var CalL_AutO_LauncH='';
var CallBackCommenTs='';
var CallBackDatETimE='';
var CallBackrecipient='';
var CalLCID='';
var CalL_ScripT_id='';
var campagentstatct='0';
var CBcallback_time='';
var CBcomments='';
var CB_count_check=60;
var CBentry_time='';
var CBuser='';
var CCALlast_pick;
var check_n=0;
var check_s;
var c=new Date();
var conf_channels_xtra_display=0;
var conf_check_recheck=0;
var custchannellive=0;
var customerparked=0;
var debugLevel=0;
var debugLevelColors=new Array('#000000','#000000','#330099','#336633','#993300','#CC0000');
var debugWindow=0;
var debugWindowOpened=0;
var decoded='';
var DiaLControl_auto_HTML="<img src=\""+aapath+"vdc_LB_pause_OFF.gif\" border=0 alt=\"Pause\"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\""+aapath+"vdc_LB_resume.gif\" border=0 alt=\"Resume\"></a>";
var DiaLControl_auto_HTML_OFF="<img src=\""+aapath+"vdc_LB_pause_OFF.gif\" border=0 alt=\"Pause\"><img src=\""+aapath+"vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\">";
var DiaLControl_auto_HTML_ready="<a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADpause','NEW_ID');\"><img src=\""+aapath+"vdc_LB_pause.gif\" border=0 alt=\"Pause\"></a><img src=\""+aapath+"vdc_LB_resume_OFF.gif\" border=0 alt=\"Resume\">";
var DiaLControl_manual_HTML="<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><img src=\""+aapath+"vdc_LB_dialnextnumber.gif\" border=0 alt=\"Dial Next Number\"></a>";
var dialed_label='';
var dialed_number='';
var dial_timedout=0;
var display_message='';
var DispO3wayCalLcamptail='';
var DispO3wayCalLserverip='';
var DispO3wayCalLxfernumber='';
var DispO3waychannel='';
var DispO3wayXtrAchannel='';
var dtmf_keys_active=0;
var extvalue=extension;
var filename='';
var fronter='';
var HKbutton_allowed=1;
var HKdispo_display=0;
var HKfinish=0;
var hot_keys_active=0;
var hotkeys=new Array();
var LasTCID='';
var lastconf='';
var lastcustchannel='';
var lastcustserverip='';
var last_filename='';
var lastxferchannel='';
var LCAc=new Array('','','','','','');
var LCAcount=0;
var LCAe=new Array('','','','','','');
var LCAt=new Array('','','','','','');
var lead_dial_number='';
var LeaDDispO='';
var LeaDPreVDispO='';
var LMAcount=0;
var LMAe=new Array('','','','','','');
var logout_stop_timeouts=0;
var manual_auto_hotkey=0;
var manual_dial_in_progress=0;
var manual_dial_menu=0;
var MDchannel='';
var MD_channel_look=0;
var MDlogEPOCH=0;
var MDnextCID='';
var MD_ring_secondS=0;
var MDuniqueid='';
var menufontsize=8;
var menuheight=30;
var menuwidth=30;
var MTvar;
var Nactiveext;
var Nbusyext;
var Nbusytrunk;
var nochannelinsession=0;
var open_dispo_screen=0;
var osdalert_timer=0;
var OSDiaL_closer_blended='0';
var OSDiaL_closer_login_checked=0;
var OSDiaL_closer_login_selected=0;
var OSDiaL_pause_calling=1;
var park_count=0;
var park_refresh=0;
var PauseCode_HTML='';
var PausENotifYCounTer=0;
var PCSpause=0;
var PDCALlast_pick;
var PostDatETimE='';
var previewFD_display_id=0;
var previewFD_timeout_id;
var previewFD_time_remaining=previewFD_time;
var previous_called_count='';
var previous_dispo='';
var recLIST='';
var recording_id=0;
var RedirecTxFEr=0;
var refresh_interval=1000;
var reselect_alt_dial=0;
var script_last_click='';
var textareafontsize=10;
var t=new Date();
var UnixTimeMS=0;
var VDCL_group_id='';
var VD_live_call_secondS=0;
var VD_live_customer_call=0;
var VU_agent_choose_ingroups_DV='';
var web_form_frame_open1=0;
var web_form_frame_open2=0;
var web_form_vars='';
var web_form_vars2='';
var wrapup_counter=0;
var wrapup_waiting=0;
var XD_channel_look=0;
var XDcheck='';
var XD_live_call_secondS=0;
var XD_live_customer_call=0;
var XDnextCID='';
var XfeR_channel='';
var xferchannellive=0;
var xmlhttp=false;
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
for (var h=0; h<HK_statuses_camp; h++) {
 hotkeys[HKhotkeys[h]]=HKstatuses[h]+" ----- "+HKstatusnames[h]+ " ----- "+HKxferextens[h];
}

