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

var active_display=1;
var activeext_order='asc';
var activeext_query;
var old_agent_message='';
var AgaiNCalLCID='';
var AgainCalLSecondS='';
var AgaiNHanguPChanneL='';
var AgaiNHanguPServeR='';
var agentchannel='';
var AgentDispoing=0;
var agentphonelive=0;
var agent_log_type='PAUSE';
var agent_log_time=0;
var agent_log_time_total=0;
var agent_log_epoch=0;
var agent_log_epoch_start=0;
var agentlogtime_timeout_id;
var all_record_count=0;
var all_record='NO';
var alt_dial_active=0;
var alt_dial_menu=0;
var acct_remaining=0;
var acct_method='NONE';
var auto_dial_alt_dial=0;
var AutoDialReady=0;
var AutoDialWaiting=0;
var blind_transfer=0;
var busyext_order='asc';
var busyext_query;
var busylocalhangup_order='asc';
var busylocalhangup_query;
var busytrunkhangup_order='asc';
var busytrunkhangup_query;
var busytrunk_order='asc';
var busytrunk_query;
var call_queue_in=0;
var call_queue_in_mc=0;
var call_queue_out=0;
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
var conf_dialed=0;
var custchannellive=0;
var customerparked=0;
var debugLevel=0;
var debugLevelColors=new Array('#000000','#000000','#330099','#336633','#993300','#CC0000');
var debugWindow=0;
var debugWindowOpened=0;
var decoded='';
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
var extendedStatusesBypass=0;
var filename='';
var fronter='';
var HKbutton_allowed=1;
var HKdispo_display=0;
var HKfinish=0;
var hideHKduringDispo=0;
var hot_keys_active=0;
var hot_keys_active_click=0;
var hotkeys_clicked=0;
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
var leaving_threeway=0;
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
var multicall_active=0;
var multicall_agentlogid='';
var multicall_agentlogstate='';
var multicall_agentlogtime=0;
var multicall_alert=0;
var multicall_liveseconds=0;
var multicall_channel='';
var multicall_serverip='';
var multicall_callerid='';
var multicall_ingroup='';
var multicall_voicemail='';
var multicall_leadid='';
var multicall_uniqueid='';
var multicall_lastchannel='';
var multicall_lastserverip='';
var multicall_lastcallerid='';
var multicall_lastingroup='';
var multicall_lastvoicemail='';
var multicall_lastleadid='';
var multicall_lastuniqueid='';
var multicall_tmpchannel='';
var multicall_tmpserverip='';
var multicall_tmpcallerid='';
var multicall_tmpingroup='';
var multicall_tmpvoicemail='';
var multicall_tmpleadid='';
var multicall_tmpuniqueid='';
var multicall_waitchannel='';
var multicall_waitserverip='';
var multicall_waitcallerid='';
var multicall_waitingroup='';
var multicall_waitvoicemail='';
var multicall_waitleadid='';
var multicall_waituniqueid='';
var multicall_vmdrop_timer=-1;
var multicall_agentlogid='';
var multicall_tmpagentlogid='';
var multicall_lastagentlogid='';
var multicall_agentlogstate='';
var multicall_tmpagentlogstate='';
var multicall_lastagentlogstate='';
var multicall_agentlogtime=0;
var multicall_tmpagentlogtime=0;
var multicall_lastagentlogtime=0;
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
var previous_called_count='';
var previous_dispo='';
var recLIST='';
var recording_id=0;
var RedirecTxFEr=0;
var refresh_interval=1000;
var reselect_alt_dial=0;
var script_last_click='';
var start_all_timeout_id;
var textareafontsize=10;
var threeway_end=0;
var t=new Date();
var UnixTimeMS=0;
var vmail_check_timer=0;
var vmail_messages='';
var vmail_old_messages='';
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
var VARstatCUR;
var XD_live_call_secondS=0;
var XD_live_customer_call=0;
var XDnextCID='';
var XfeR_channel='';
var xferchannellive=0;
var xmlhttp=false;
