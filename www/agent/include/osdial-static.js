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


// ################################################################################
// getXHR() - Returns an xmlhttprequest or MS equiv.
	var getXHR = function () {
		var xmlhttp=false;
		try {
			xmlhttp = new XMLHttpRequest();
			if (xmlhttp) getXHR = function() { return new XMLHttpRequest(); };
		} catch(e) {
			var msxml = ['MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'];
			for (var i=0, len = msxml.length; i < len; ++i) {
				try {
					xmlhttp = new ActiveXObject(msxml[i]);
					if (xmlhttp) {
						if (i==0) {
							getXHR = function() { return new ActiveXObject('MSXML2.XMLHTTP.3.0'); };
						} else if (i==1) {
							getXHR = function() { return new ActiveXObject('MSXML2.XMLHTTP'); };
						} else if (i==2) {
							getXHR = function() { return new ActiveXObject('Microsoft.XMLHTTP'); };
						}
					}
					break;
				} catch(e) {}
			}
		}
		return xmlhttp;
	}




// ################################################################################
// Send Request for allowable campaigns to populate the campaigns pull-down
    function login_allowable_campaigns() {
        var xmlhttp=getXHR();
        if (xmlhttp) { 
            logincampaign_query = "&user=" + document.osdial_form.VD_login.value + "&pass=" + document.osdial_form.VD_pass.value + "&ACTION=LogiNCamPaigns&format=html";
            xmlhttp.open('POST', 'vdc_db_query.php'); 
            xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
            xmlhttp.send(logincampaign_query); 
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    Nactiveext = null;
                    Nactiveext = xmlhttp.responseText;
                    //	alert(logincampaign_query);
                    //	alert(xmlhttp.responseText);
                    document.getElementById("LogiNCamPaigns").innerHTML = Nactiveext;
                    document.getElementById("LogiNReseT").innerHTML = "<INPUT TYPE=BUTTON VALUE=\"Refresh Campaign List\" OnClick=\"login_allowable_campaigns()\">";
                    document.getElementById("VD_campaign").focus();
                    }
                }
                delete xmlhttp;
            }
        }

        function login_focus() {
            document.osdial_form.VD_campaign.blur();
            document.osdial_form.VD_login.focus();
            document.osdial_form.VD_campaign.onfocus=function(){login_allowable_campaigns();};
        }

        function login_submit() {
            document.getElementById("WelcomeBoxStatus").innerHTML = 'Authenticating...';
            document.getElementById("WelcomeBoxA").style.visibility = 'visible';
            document.osdial_form.submit();
        }

// ################################################################################
// Send Hangup command for Live call connected to phone now to Manager
	function livehangup_send_hangup(taskvar) {
		debug("<b>livehangup_send_hangup:</b> taskvar=" + taskvar,2);
		var xmlhttp=getXHR();
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
					osdalert(xmlhttp.responseText);
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
		var xmlhttp=getXHR();
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
					//osdalert(xmlhttp.responseText,30);
				}
			}
			delete xmlhttp;
		}
		if (taskagentmute=='AgenT') {
			if (taskdirection=='MUTING') {
				document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('UNMUTE','" + agentchannel + "','AgenT');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_UNMUTE.gif\" width=28 height=29 BORDER=0></a>";
				document.getElementById("MutedWarning").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('UNMUTE','" + agentchannel + "','AgenT');return false;\"><img src=\"templates/" + agent_template + "/images/muted.gif\" width=148 height=35 BORDER=0></a>";
			} else {
				document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_MUTE.gif\" width=28 height=29 BORDER=0></a>";
				document.getElementById("MutedWarning").innerHTML = "<img src=\"templates/" + agent_template + "/images/mutedoff.gif\" width=148 height=35 border=0></a>";
			}
		}
	}


// ################################################################################
// park customer and place 3way call
	function xfer_park_dial() {
		debug("<b>xfer_park_dial</b>",2);
		conf_dialed=1;
		mainxfer_send_redirect('ParK',lastcustchannel,lastcustserverip);

		SendManualDial('YES');
	}

// ################################################################################
// place 3way and customer into other conference and fake-hangup the lines
	function leave_3way_call(tempvarattempt) {
		debug("<b>leave_3way_call:</b> tempvarattempt=" + tempvarattempt,2);
		leaving_threeway=1;
		mainxfer_send_redirect('3WAY','','',tempvarattempt);

		if (customerparked > 0) {
			mainxfer_send_redirect('FROMParK',lastcustchannel,lastcustserverip);
		}

		//document.getElementById("callchannel").innerHTML = '';
		//document.osdial_form.callserverip.value = '';
		//dialedcall_send_hangup();
		//document.osdial_form.xferchannel.value = '';
		//xfercall_send_hangup();
		if( document.images ) {
			document.images['livecall'].src = image_livecall_OFF.src;
		}
	}

// ################################################################################
// filter manual dialstring and pass on to originate call
	function SendManualDial(taskFromConf) {
		debug("<b>SendManualDial:</b> taskFromConf=" + taskFromConf,2);
		conf_dialed=1;
		var regXFvars = new RegExp("XFER","g");
		if (taskFromConf == 'YES') {
                        if (document.osdial_form.xfernumber.value == '' && CalL_XC_a_NuMber.match(regXFvars)) {
                                document.osdial_form.xfernumber.value = CalL_XC_a_NuMber;
                        }
			var manual_dialcode = ''+document.osdial_form.phone_code.value;
			if (manual_dialcode.length==0) manual_dialcode='1';
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
				if (manual_dialcode!='1' && manual_dialcode.substring(0,1)!='0') manual_dialcode = '011' + manual_dialcode;
				manual_string = manual_dialcode + manual_string;
			}
		}
		if (manual_string != '') {
			if (taskFromConf == 'YES') {
				basic_originate_call(manual_string,'YES','YES',dial_conf_exten,'NO',taskFromConf);
			} else {
				basic_originate_call(manual_string,'YES','NO');
			}
			MD_ring_secondS=0;
		} else {
			 osdalert("You must enter a number.",5);
		}
	}

// ################################################################################
// Send Originate command to manager to place a phone call
	function basic_originate_call(tasknum,taskprefix,taskreverse,taskdialvalue,tasknowait,taskconfxfer) {
		debug("<b>basic_originate_call:</b> tasknum=" + tasknum + " taskprefix=" + taskprefix + " taskreverse=" + taskreverse + " taskdialvalue=" + taskdialvalue+ " tasknowait=" + tasknowait + " taskconfxfer=" + taskconfxfer,2);

		var cxmatch = 0;
		var ext_context2 = ext_context;
		var dial_context2 = dial_context;

		var regCXFvars = new RegExp("CXFER","g");
		var tasknum_string = tasknum.toString();
		if (tasknum_string.match(regCXFvars)) {
			ext_context2 = 'osdial';
			dial_context2 = 'osdial';
			var Ctasknum = tasknum_string.replace(regCXFvars, '');
			if (Ctasknum.length < 2) Ctasknum = '990009';
			var XfeRSelecT = document.getElementById("XfeRGrouP");
			tasknum = Ctasknum + "*" + XfeRSelecT.value + '*CXFER*' + document.osdial_form.lead_id.value + '**' + document.osdial_form.phone_number.value + '*' + user + '*';
			CustomerData_update();
			cxmatch++;
		}

		var regAXFvars = new RegExp("AXFER","g");
		if (tasknum_string.match(regAXFvars)) {
			ext_context2 = 'osdial';
			dial_context2 = 'osdial';
			var Ctasknum = tasknum_string.replace(regAXFvars, '');
			if (Ctasknum.length < 2) Ctasknum = '83009';
			var closerxfercamptail = '_L';
			if (closerxfercamptail.length < 3) closerxfercamptail = 'IVR';
			tasknum = Ctasknum + '*' + document.osdial_form.phone_number.value + '*' + document.osdial_form.lead_id.value + '*' + campaign + '*' + closerxfercamptail + '*' + user + '*';
			CustomerData_update();
			cxmatch++;
		}

		var xmlhttp=getXHR();
		if (xmlhttp) {
			var channel_context = ext_context2;
			var channel_value = '';
			var extension_context = dial_context2;
			var extension_value = '';
			var destination = tasknum;

			if (taskprefix == 'YES') {
				if (document.osdial_form.xferoverride.checked==false) {
					if (cxmatch == 0) destination = dial_prefix + "" + tasknum;
				}
			}
			if (taskreverse == 'YES') {
				channel_context = dial_context2;
				extension_context = ext_context2;
				if (taskdialvalue.length < 2) {
					extension_value = dialplan_number;
				} else {
					extension_value = taskdialvalue;
				}
				channel_value = "Local/" + destination + "@" + channel_context;
			} else {
				extension_value = destination;
				var protochan = protocol;
				var extenchan = extension;
				if ( (protocol == 'EXTERNAL') || (protocol == 'Local') )  {
					protochan = 'Local';
					extenchan = extension + "@" + channel_context;
				}
				channel_value = protochan + "/" + extenchan;
			}
			var dest_areacode = '';
			if (destination.length == 12) {
				dest_areacode = destination.substr(2,3);
			} else if (destination.length == 11) {
				dest_areacode = destination.substr(1,3);
			} else if (destination.length == 10) {
				dest_areacode = destination.substr(0,3);
			}
			if (taskconfxfer == 'YES') {
				var queryCID = "DCagcW" + epoch_sec + user_abb;
				document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"bothcall_send_hangup();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_hangupcustomer.gif\" width=145 height=16 border=0 alt=\"Hangup Customer\"></a>";
				lead_cust1_cid = document.osdial_form.custom1.value;
				lead_cust2_cid = document.osdial_form.custom2.value;
				lead_cid = document.osdial_form.phone_number.value;
				if (xfer_cid_mode == 'LEAD_CUSTOM1' && lead_cid != '') {
					cid = lead_cust1_cid;
					cid_name = lead_cust1_cid;
				} else if (xfer_cid_mode == 'LEAD_CUSTOM2' && lead_cid != '') {
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
					if (use_cid_areacode_map=='Y') {
						for (var c=0; c<VARcid_areacodes.length; c++) {
							if (VARcid_areacodes[c] == dest_areacode) {
								cid = VARcid_areacode_numbers[c];
								cid_name = VARcid_areacode_names[c];
							}
						}
					}
				}
			} else {
				var queryCID = "DVagcW" + epoch_sec + user_abb;
				cid = campaign_cid;
				cid_name = campaign_cid_name;
				if (use_cid_areacode_map=='Y') {
					for (var c=0; c<VARcid_areacodes.length; c++) {
						if (VARcid_areacodes[c] == dest_areacode) {
							cid = VARcid_areacode_numbers[c];
							cid_name = VARcid_areacode_names[c];
						}
					}
				}
			}

			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=" + channel_value + "&queryCID=" + queryCID + "&exten=" + extension_value + "&ext_context=" + extension_context + "&ext_priority=1&outbound_cid=" + cid + "&outbound_cid_name=" + cid_name;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(xmlhttp.responseText,30);

					if ((taskdialvalue.length > 0) && (tasknowait != 'YES')) {
						XDnextCID = queryCID;
						MD_channel_look=1;
						XDcheck = 'YES';

				//		document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"xfercall_send_hangup();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_hangupxferline.gif\" width=145 height=16 border=0 alt=\"Hangup Xfer Line\"></a>";
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

		var xmlhttp=getXHR();
		if (xmlhttp) { 
			var queryCID = dtmf_string;
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=SysCIDOriginate&format=text&channel=" + dtmf_send_extension + "&queryCID=" + queryCID + "&exten=" + conf_silent_prefix + '' + conf_dtmf_room + "&ext_context=" + ext_context + "&ext_priority=1";
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(xmlhttp.responseText,30);
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

			xmlhttprequestcheckconf=getXHR();
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
						debug("<b>check_for_conf_calls:</b> checkconf_query=" + checkconf_query,3);
						//osdalert(checkconf_query,30);
						//osdalert(xmlhttprequestcheckconf.responseText,30);
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
							var CallQueueIn_array = check_time_array[4].split("CallQueueIn: ");
							var CallQueueOut_array = check_time_array[5].split("CallQueueOut: ");
							var CallQueueInMC_array = check_time_array[6].split("CallQueueInMC: ");
							var ParkCalls_array = check_time_array[7].split("ParkCalls: ");
							var DiaLCalLs_array = check_time_array[9].split("DiaLCalls: ");
							var DiaLCalLs = DiaLCalLs_array[1];
							var TimeSync_array = check_time_array[10].split("TimeSync: ");
							var TimeSyncInfo = TimeSync_array[1];
							if (AGLogiN != 'N') {
								document.getElementById("AgentStatusStatus").innerHTML = AGLogiN;
								call_queue_in = CallQueueIn_array[1];
								call_queue_out = CallQueueOut_array[1];
								call_queue_in_mc = CallQueueInMC_array[1];
								park_count = ParkCalls_array[1];
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
						var VLAStatuS_array = check_time_array[8].split("Status: ");
						var VLAStatuS = VLAStatuS_array[1];
						if ( (VLAStatuS == 'PAUSED') && (AutoDialWaiting == 1) ) {
							if (PausENotifYCounTer > 10) {
								AutoDial_ReSume_PauSe('VDADpause','NEW_ID');
								PausENotifYCounTer=0;
								osdalert('Your session has been paused',600);
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
								var live_conf_HTML = "<font face=\"Arial,Helvetica\"><B>LIVE CALLS IN YOUR SESSION:</B></font><BR><TABLE WIDTH=" + SDwidth + "><TR><TD><font class=\"log_title\">#</TD><TD><font class=\"log_title\">REMOTE CHANNEL</TD><TD><font class=\"log_title\">HANGUP</TD><TD><font class=\"log_title\">VOLUME</TD></TR>";
								if ( (LMAcount > live_conf_calls)  || (LMAcount < live_conf_calls) || (LMAforce > 0)) {
									LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
									LMAcount=0;   LMAcontent_change++;
								}
								while (loop_ct < live_conf_calls) {
									loop_ct++;
									loop_s = loop_ct.toString();
									if (loop_s.match(/1$|3$|5$|7$|9$/)) {
										var row_color = oddrows;
									} else {
										var row_color = evenrows;
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
										live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td><font class=\"log_text\">" + loop_ct + "</td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</td><td><font class=\"log_text\"><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldA + "');return false;\">HANGUP</a></td><td><a href=\"#\" onclick=\"volume_control('UP','" + channelfieldA + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_up.gif\" width=28 height=15 BORDER=0></a> &nbsp; <a href=\"#\" onclick=\"volume_control('DOWN','" + channelfieldA + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_down.gif\" width=28 height=15 BORDER=0></a> &nbsp; &nbsp; &nbsp; <a href=\"#\" onclick=\"volume_control('MUTING','" + channelfieldA + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_MUTE.gif\" width=28 height=28 BORDER=0></a> &nbsp; <a href=\"#\" onclick=\"volume_control('UNMUTE','" + channelfieldA + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_UNMUTE.gif\" width=28 height=28 BORDER=0></a></td></tr>";
									}
									//var debugspan = document.getElementById("debugbottomspan").innerHTML;

									if (channelfieldA == lastcustchannel) {
										custchannellive++;
									} else if (lastcustchannel.match(/Local\/8870.....@/)) {
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

												document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_MUTE.gif\" width=28 height=29 BORDER=0></a>";
											}
										} else {
											if (agentchannel.length < 3) {
												agentchannel = channelfieldA;

												document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_MUTE.gif\" width=28 height=29 BORDER=0></a>";
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
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			if (taskconfrectype == 'MonitorConf') {
				//CAMPAIGN CUSTPHONE FULLDATE TINYDATE EPOCH AGENT
				var REGrecCAMPAIGN = new RegExp("CAMPAIGN","g");
				var REGrecCUSTPHONE = new RegExp("CUSTPHONE","g");
				var REGrecFULLDATE = new RegExp("FULLDATE","g");
				var REGrecISODATE = new RegExp("ISODATE","g");
				var REGrecTINYDATE = new RegExp("TINYDATE","g");
				var REGrecEPOCH = new RegExp("EPOCH","g");
				var REGrecAGENT = new RegExp("AGENT","g");
				var REGrecLASTNAME = new RegExp("LASTNAME","g");
				var REGrecFIRSTNAME = new RegExp("FIRSTNAME","g");
				var reclastname = document.osdial_form.last_name.value;
				if (reclastname=='') reclastname='Unknown';
				var recfirstname = document.osdial_form.first_name.value;
				if (recfirstname=='') recfirstname='Unknown';
				filename = campaign_rec_filename;
				filename = filename.replace(REGrecCAMPAIGN, campaign);
				filename = filename.replace(REGrecCUSTPHONE, lead_dial_number);
				filename = filename.replace(REGrecFULLDATE, filedate);
				filename = filename.replace(REGrecISODATE, isodate);
				filename = filename.replace(REGrecEPOCH, epoch_sec);
				filename = filename.replace(REGrecAGENT, user);
				filename = filename.replace(REGrecLASTNAME, reclastname);
				filename = filename.replace(REGrecFIRSTNAME, recfirstname);
				//filename = filedate + "_" + user_abb;
				var query_recording_exten = recording_exten;
				var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
				var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('StopMonitorConf','" + taskconfrec + "','" + filename + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_stoprecording.gif\" width=145 height=16 border=0 alt=\"Stop Recording\"></a>";

				if (campaign_recording == 'ALLFORCE') {
					document.getElementById("RecorDControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_startrecording_OFF.gif\" width=145 height=16 border=0 alt=\"Start Recording\">";
				} else {
					document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;
				}
			}
			if (taskconfrectype == 'StopMonitorConf') {
				filename = taskconffile;
				var query_recording_exten = session_id;
				var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
				var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + taskconfrec + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_startrecording.gif\" width=145 height=16 border=0 alt=\"Start Recording\"></a>";
				if (campaign_recording == 'ALLFORCE') {
					document.getElementById("RecorDControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_startrecording_OFF.gif\" width=145 height=16 border=0 alt=\"Start Recording\">";
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
					//osdalert(xmlhttp.responseText,30);
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
		blind_transfer=1;
		if (auto_dial_level == 0) {
			RedirecTxFEr = 1;
		}
                var outbound_cid;
                var outbound_cid_name;
                lead_cust1_cid = document.osdial_form.custom1.value;
                lead_cust2_cid = document.osdial_form.custom2.value;
                lead_cid = document.osdial_form.phone_number.value;
                if (xfer_cid_mode == 'LEAD_CUSTOM1' && lead_cid != '') {
                    outbound_cid = lead_cust1_cid; 
                    outbound_cid_name = lead_cust1_cid;
                } else if (xfer_cid_mode == 'LEAD_CUSTOM2' && lead_cid != '') {
                    outbound_cid = lead_cust2_cid;
                    outbound_cid_name = lead_cust2_cid;
                } else if (xfer_cid_mode == 'LEAD') {
                    outbound_cid = lead_cid;
                    outbound_cid_name = lead_cid;
                } else if (xfer_cid_mode == 'PHONE') {
                    outbound_cid = phone_cid;
                    outbound_cid_name = phone_cid_name;
                } else {
                    outbound_cid = campaign_cid;
                    outbound_cid_name = campaign_cid_name;
                    camap_number = document.osdial_form.xfernumber.value;
                    if (use_cid_areacode_map=='Y' && camap_number.length==10) {
                        for (var c=0; c<VARcid_areacodes.length; c++) {
                            if (VARcid_areacodes[c] == camap_number.substr(0,3)) {
				outbound_cid = VARcid_areacode_numbers[c];
                                outbound_cid_name = VARcid_areacode_names[c];
                            }
                        }
                    }
                }
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			var redirectvalue = MDchannel;
			var redirectserverip = lastcustserverip;
			if (redirectvalue.length < 2) {
				redirectvalue = lastcustchannel
			}
			var closerxferinternal = '9';
			if (taskvar == 'XfeRINTERNAL') {
				closerxferinternal = '';
				taskvar = 'XfeRLOCAL';
			}
			if (taskvar == 'XfeRBLIND' || taskvar == 'XfeRVMAIL') {
				var queryCID = "XBvdcW" + epoch_sec + user_abb;
				var blindxferdialstring = document.osdial_form.xfernumber.value;
				var blindxfercontext = ext_context;
				if (taskvar == 'XfeRVMAIL') {
					blindxferdialstring = campaign_am_message_exten;
					blindxfercontext = ext_context;
				} else {
					var regXFvars = new RegExp("XFER","g");
					if (blindxferdialstring.match(regXFvars)) {
						var regAXFvars = new RegExp("AXFER","g");
						if (blindxferdialstring.match(regAXFvars)) {
							var Ctasknum = blindxferdialstring.replace(regAXFvars, '');
							if (Ctasknum.length < 2) Ctasknum = '83009';

							var closerxfercamptail = '_L';
							if (closerxfercamptail.length < 3) closerxfercamptail = 'IVR';

							blindxferdialstring = Ctasknum + '*' + document.osdial_form.phone_number.value + '*' + document.osdial_form.lead_id.value + '*' + campaign + '*' + closerxfercamptail + '*' + user + '*';
						}
					} else if (document.osdial_form.xferoverride.checked==false) {
						if (blindxferdialstring.length == 10) blindxferdialstring = "1" + blindxferdialstring;
						if (blindxferdialstring.length == 7 || blindxferdialstring.length >= 11) {
							blindxferdialstring = dial_prefix + "" + blindxferdialstring;
							blindxfercontext = dial_context;
						}
					}
				}
				if (blindxferdialstring.length < 2) {
					xferredirect_query='';
					taskvar = 'NOTHING';
					osdalert("Transfer number must have more than 1 digit:" + blindxferdialstring,5);
				} else {
					xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + blindxferdialstring + "&ext_context=" + blindxfercontext + "&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + campaign + "&uniqueid=" + document.osdial_form.uniqueid.value + "&lead_id=" + document.osdial_form.lead_id.value + "&secondS=" + VD_live_call_secondS + "&session_id=" + session_id + "&outbound_cid=" + outbound_cid + "&outbound_cid_name=" + outbound_cid_name;
				}
			} else if (taskvar == 'XfeRLOCAL') {
				CustomerData_update();
				var XfeRSelecT = document.getElementById("XfeRGrouP");
				var queryCID = "XLvdcW" + epoch_sec + user_abb;
				//"90009*$group**$lead_id**$phone_number*$user*";
				var redirectdestination = closerxferinternal + '90009*' + XfeRSelecT.value + '**' + document.osdial_form.lead_id.value + '**' + document.osdial_form.phone_number.value + '*' + user + '*';

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + campaign + "&uniqueid=" + document.osdial_form.uniqueid.value + "&lead_id=" + document.osdial_form.lead_id.value + "&secondS=" + VD_live_call_secondS + "&session_id=" + session_id;
			} else if (taskvar == '3WAY') {
				xferredirect_query='';

				var queryCID = "VXvdcW" + epoch_sec + user_abb;
				var redirectdestination = "NEXTAVAILABLE";
				var redirectXTRAvalue = XDchannel;
				var redirecttype_test = document.osdial_form.xfernumber.value;
				var XfeRSelecT = document.getElementById("XfeRGrouP");
				var regRXFvars = new RegExp("CXFER","g");
				var redirecttype = 'RedirectXtra';
				if (redirecttype_test.match(regRXFvars) && local_consult_xfers > 0) {
					redirecttype = 'RedirectXtraCX';
				}
				DispO3waychannel = redirectvalue;
				DispO3wayXtrAchannel = redirectXTRAvalue;
				DispO3wayCalLserverip = redirectserverip;
				DispO3wayCalLxfernumber = document.osdial_form.xfernumber.value;
				DispO3wayCalLcamptail = '';


				var manual_dialcode = ''+document.osdial_form.phone_code.value;
				if (manual_dialcode!='1' && manual_dialcode.substring(0,1)!='0') manual_dialcode = '011' + manual_dialcode;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + redirecttype + "&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&extrachannel=" + redirectXTRAvalue + "&lead_id=" + document.osdial_form.lead_id.value + "&phone_code=" + manual_dialcode + "&phone_number=" + document.osdial_form.phone_number.value+ "&filename=" + taskdebugnote + "&campaign=" + XfeRSelecT.value + "&session_id=" + session_id + "&agentchannel=" + agentchannel + "&protocol=" + protocol + "&extension=" + extension + "&auto_dial_level=" + auto_dial_level + "&outbound_cid=" + outbound_cid + "&outbound_cid_name=" + outbound_cid_name;

				if (taskdebugnote == 'FIRST') {
					document.getElementById("DispoSelectHAspan").innerHTML = "<a href=\"#\" onclick=\"DispoLeavE3wayAgaiN()\">Leave 3Way Call Again</a>";
				}
			} else if (taskvar == 'ParK') {
				blind_transfer=0;
				var queryCID = "LPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;
				var parkedby = protocol + "/" + extension;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectToPark&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + park_on_extension + "&ext_context=" + ext_context + "&ext_priority=1&extenName=park&parkedby=" + parkedby + "&session_id=" + session_id;

				document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('FROMParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_grabparkedcall.gif\" width=145 height=16 border=0 alt=\"Grab Parked Call\"></a>";
				customerparked=1;
			} else if (taskvar == 'FROMParK') {
				blind_transfer=0;
				var queryCID = "FPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;
				var dest_dialstring = session_id;
				var server_dialstring = '';

				if (server_ip != taskserverip && taskserverip.length > 6) {
					server_dialstring = server_ip_dialstring;
				}

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectFromPark&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + dest_dialstring + "&server_dialstring=" + server_dialstring + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;

				document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_parkcall.gif\" width=145 height=16 border=0 alt=\"Park Call\"></a>";
				customerparked=0;
			}


			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(xferredirect_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var XfeRRedirecToutput = null;
					XfeRRedirecToutput = xmlhttp.responseText;
					var XfeRRedirecToutput_array=XfeRRedirecToutput.split("|");
					var XFRDop = XfeRRedirecToutput_array[0];
					if (XFRDop == "NeWSessioN") {
						document.getElementById("callchannel").innerHTML = '';
						document.osdial_form.callserverip.value = '';
						dialedcall_send_hangup();

						document.osdial_form.xferchannel.value = '';
						xfercall_send_hangup();

						session_id = XfeRRedirecToutput_array[1];
						document.getElementById("sessionIDspan").innerHTML = session_id;

						//alert("session_id changed to: " + session_id);
					}
				}
			}
			delete xmlhttp;
		}

		// used to send second Redirect  for manual dial calls
		if (auto_dial_level == 0 && taskvar != '3WAY') {
			RedirecTxFEr = 1;
			var xmlhttp2=getXHR();
			if (xmlhttp2) { 
				xmlhttp2.open('POST', 'manager_send.php'); 
				xmlhttp2.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp2.send(xferredirect_query + "&stage=2NDXfeR"); 
				xmlhttp2.onreadystatechange = function() { 
					if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200) {
						Nactiveext = null;
						Nactiveext = xmlhttp2.responseText;
						//osdalert(RedirecTxFEr + "|" + xmlhttp2.responseText,30);
					}
				}
				delete xmlhttp2;
			}
		}

		if (taskvar == 'XfeRLOCAL' || taskvar == 'XfeRBLIND' || taskvar == 'XfeRVMAIL') {
			if (auto_dial_level == 0) {
				RedirecTxFEr = 1;
			}
			//document.osdial_form.callchannel.value = '';
			document.getElementById("callchannel").innerHTML = '';
			document.osdial_form.callserverip.value = '';
			if( document.images ) {
				document.images['livecall'].src = image_livecall_OFF.src;
			}
			//osdalert(RedirecTxFEr + "|" + auto_dial_level,30);
                        if (taskvar == 'XfeRVMAIL') {
                                document.osdial_form.DispoSelection.value = 'AM';
                                dialedcall_send_hangup('NO');
                              	alt_dial_active=0;
                                reselect_alt_dial=0;
                                DispoSelect_submit();
                        } else if (taskvar == 'XfeRBLIND') {
                                document.osdial_form.DispoSelection.value = 'XFER';
                                dialedcall_send_hangup('NO');
                              	alt_dial_active=0;
                                reselect_alt_dial=0;
				document.osdial_form.DispoSelectStop.checked=true;
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
				var man_status = "Dial Alt Phone Number: <a href=\"#\" id=\"mainphonelink\" onclick=\"document.getElementById('mainphonelink').setAttribute('onclick','void(0);'); alt_dial_menu=0; ManualDialOnly('MaiNPhonE');\"><font class=\"preview_text\">MAIN PHONE</font></a> or <a href=\"#\" id=\"altphonelink\" onclick=\"document.getElementById('altphonelink').setAttribute('onclick','void(0);'); alt_dial_menu=0; ManualDialOnly('ALTPhoneE');\"><font class=\"preview_text\">ALT PHONE</font></a> or <a href=\"#\" id=\"address3link\" onclick=\"document.getElementById('address3link').setAttribute('onclick','void(0);'); alt_dial_menu=0; ManualDialOnly('AddresS3');\"><font class=\"preview_text\">ADDRESS3</font></a> or <a href=\"#\" id=\"finishleadlink\" onclick=\"document.getElementById('finishleadlink').setAttribute('onclick','void(0);'); alt_dial_menu=0; ManualDialAltDonE();\"><font class=\"preview_text_red\" style=color:" + status_preview_color + ">FINISH LEAD</font></a>"; 
				document.getElementById("MainStatuSSpan").innerHTML = man_status;
			}
		}
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			var manual_dialcode = ''+document.osdial_form.phone_code.value;
			if (manual_dialcode!='1' && manual_dialcode.substring(0,1)!='0') manual_dialcode = '011' + manual_dialcode;
			manDiaLlog_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLlogCaLL&stage=" + taskMDstage + "&uniqueid=" + document.osdial_form.uniqueid.value + 
			"&user=" + user + "&pass=" + pass + "&campaign=" + campaign + 
			"&lead_id=" + document.osdial_form.lead_id.value + 
			"&list_id=" + document.osdial_form.list_id.value + 
			"&length_in_sec=0&phone_code=" + manual_dialcode + 
			"&phone_number=" + lead_dial_number + 
			"&exten=" + extension + "&channel=" + lastcustchannel + "&start_epoch=" + MDlogEPOCH + "&auto_dial_level=" + auto_dial_level + "&VDstop_rec_after_each_call=" + VDstop_rec_after_each_call + "&conf_silent_prefix=" + conf_silent_prefix + "&protocol=" + protocol + "&extension=" + extension + "&ext_context=" + ext_context + "&conf_exten=" + session_id + "&user_abb=" + user_abb + "&agent_log_id=" + agent_log_id + "&MDnextCID=" + LasTCID + "&alt_dial=" + dialed_label + "&DB=0" + "&agentchannel=" + agentchannel + "&conf_dialed=" + conf_dialed + "&leaving_threeway=" + leaving_threeway + "&hangup_all_non_reserved=" + hangup_all_non_reserved + "&blind_transfer=" + blind_transfer;;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			//document.getElementById("busycallsdebug").innerHTML = "vdc_db_query.php?" + manDiaLlog_query;
			xmlhttp.send(manDiaLlog_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var MDlogResponse = null;
					//osdalert(xmlhttp.responseText,30);
					MDlogResponse = xmlhttp.responseText;
					var MDlogResponse_array=MDlogResponse.split("\n");
					MDlogLINE = MDlogResponse_array[0];
					if ( (MDlogLINE == "LOG NOT ENTERED") && (VDstop_rec_after_each_call != 1) ) {
						//osdalert("error: log not entered\n",30);
					} else {
						MDlogEPOCH = MDlogResponse_array[1];
						//osdalert("OSDIAL Call log entered:\n" + document.osdial_form.uniqueid.value,30);
						if ( (taskMDstage != "start") && (VDstop_rec_after_each_call == 1) ) {
							var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + session_id + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_startrecording.gif\" width=145 height=16 border=0 alt=\"Start Recording\"></a>";
							if ( (campaign_recording == 'NEVER') || (campaign_recording == 'ALLFORCE') ) {
								document.getElementById("RecorDControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_startrecording_OFF.gif\" width=145 height=16 border=0 alt=\"Start Recording\">";
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
		conf_dialed=0;
	}


// ################################################################################
// Request number of USERONLY callbacks for this agent
	function CalLBacKsCounTCheck() {
		debug("<b>CalLBacKsCounTCheck:</b>",4);
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			CBcount_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=CalLBacKCounT&campaign=" + campaign + "&format=text";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(CBcount_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(xmlhttp.responseText,30);
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
			osdalert("You must hangup and disposition your active call before you can place a call to a callback.");
		} else {
			if (AutoDialWaiting==1 && VD_live_customer_call==0 && alt_dial_active==0) {
				AutoDial_ReSume_PauSe('VDADpause','NEW_ID');
				PCSpause=1;
			}
			showDiv('CallBacKsLisTBox');

			var xmlhttp=getXHR();
			if (xmlhttp) { 
				var CBlist_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=CalLBacKLisT&campaign=" + campaign + "&format=text";
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(CBlist_query); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						//osdalert(xmlhttp.responseText,30);
						var all_CBs = null;
						all_CBs = xmlhttp.responseText;
						var all_CBs_array=all_CBs.split("\n");
						var CB_calls = all_CBs_array[0];
						var loop_ct=0;
						var conv_start=0;
						var CB_HTML = "<table width=900><tr bgcolor=" + callback_bg2 + "><td><font class=\"log_title\">#</td><td align=\"center\"><font class=\"log_title\"> CALLBACK DATE/TIME</td><td align=\"center\"><font class=\"log_title\">NUMBER</td><td align=\"center\"><font class=\"log_title\">NAME</td><td align=\"center\"><font class=\"log_title\"> STATUS</td><td align=\"center\"><font class=\"log_title\">CAMPAIGN</td><td align=\"center\"><font class=\"log_title\">LAST CALL DATE/TIME</td><td align=\"left\"><font class=\"log_title\"> COMMENTS</td></tr>";
						while (loop_ct < CB_calls) {
							loop_ct++;
							loop_s = loop_ct.toString();
							if (loop_s.match(/1$|3$|5$|7$|9$/)) {
								var row_color = oddrows;
							} else {
								var row_color = evenrows;
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
			osdalert("You must hangup and disposition your active call before you can place a call to a manually entered number.");
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
			basic_originate_call(session_id,'YES','YES',MDDiaLCodEform + "" + MDDiaLOverridEform,'YES');
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
			document.osdial_form.phone_code.value = '1';
		}
		var manual_dialcode = ''+document.osdial_form.phone_code.value;
		if (manual_dialcode!='1' && manual_dialcode.substring(0,1)!='0') manual_dialcode = '011' + manual_dialcode;
		var MDPhonENumbeRform = document.osdial_form.phone_number.value;

		if ( (manual_dialcode.length < 1) || (MDPhonENumbeRform.length < 5) ) {
			osdalert("You must enter a number in the \"Phone\" field fast dial. The \"CountryCode\" will default to \"1\".");
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
			ManualDialNext("","",manual_dialcode,MDPhonENumbeRform,MDLookuPLeaD);
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
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			manDiaLlook_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLlookCaLL&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&MDnextCID=" + CIDcheck + "&agent_log_id=" + agent_log_id + "&lead_id=" + document.osdial_form.lead_id.value;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLlook_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var MDlookResponse = null;
					//osdalert(xmlhttp.responseText,30);
					MDlookResponse = xmlhttp.responseText;
					var MDlookResponse_array=MDlookResponse.split("\n");
					var MDlookCID = MDlookResponse_array[0];
					if (MDlookCID == "NO") {
						if (dial_timedout == 0) {
							MD_ring_secondS++;
							var status_display_number = formatPhone(document.osdial_form.phone_code.value,dialed_number);

							document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
							document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=" + status_bg + ">UID: " + CIDcheck + "</font><font color=" + status_intense_color + " style='text-decoration:blink;'><b>Waiting for Ring... " + MD_ring_secondS + " seconds<b></font>";
							//osdalert("channel not found yet:\n" + campaign,30);
						}
					} else {
						var regMDL = new RegExp("^Local","ig");
						if (taskCheckOR == 'YES') {
							XDuniqueid = MDlookResponse_array[0];
							XDchannel = MDlookResponse_array[1];
							if ( (XDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') && (MD_ring_secondS < 10) ) {
								// bad grab of Local channel, try again
								MD_ring_secondS++;
								var status_display_number = formatPhone(document.osdial_form.phone_code.value,dialed_number);
								document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
								document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=" + status_bg + ">UID: " + CIDcheck + "</font><font color=" + status_intense_color + " style='text-decoration:blink;'><b>Waiting for Ring... " + MD_ring_secondS + " seconds<b></font>";
							} else {
								document.osdial_form.xferuniqueid.value	= MDlookResponse_array[0];
								document.osdial_form.xferchannel.value	= MDlookResponse_array[1];
								lastxferchannel = MDlookResponse_array[1];
								document.osdial_form.xferlength.value		= 0;

								XD_live_customer_call = 1;
								XD_live_call_secondS = 0;
								MD_channel_look=0;

								document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
								document.getElementById("MainStatuSSpan").innerHTML = " Called 3rd party: " + document.osdial_form.xfernumber.value + "&nbsp;&nbsp;<font color=" + status_bg + ">UID: " + CIDcheck;

								document.getElementById("Leave3WayCall").innerHTML ="<a href=\"#\" onclick=\"leave_3way_call('FIRST');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_leave3waycall.gif\" width=137 height=16 border=0 alt=\"LEAVE 3-WAY CALL\"></a>";

								document.getElementById("DialWithCustomer").innerHTML ="<img src=\"templates/" + agent_template + "/images/vdc_XB_dialwithcustomer_OFF.gif\" width=144 height=16 border=0 alt=\"Dial With Customer\">";

								document.getElementById("ParkCustomerDial").innerHTML ="<img src=\"templates/" + agent_template + "/images/vdc_XB_parkcustomerdial_OFF.gif\" width=147 height=16 border=0 alt=\"Park Customer Dial\">";

								document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"xfercall_send_hangup();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_hangupxferline.gif\" width=145 height=16 border=0 alt=\"Hangup Xfer Line\"></a>";

								document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_hangupbothlines.gif\" width=145 height=16 border=0 alt=\"Hangup Both Lines\"></a>";

								xferchannellive=1;
								XDcheck = '';
							}
						} else {
							MDuniqueid = MDlookResponse_array[0];
							MDchannel = MDlookResponse_array[1];
							if ( (MDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') ) {
								// bad grab of Local channel, try again
								MD_ring_secondS++;
								var status_display_number = formatPhone(document.osdial_form.phone_code.value,dialed_number);

								document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
								document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=" + status_bg + ">UID: " + CIDcheck + "</font><font color=" + status_intense_color + " style='text-decoration:blink;'><b>Waiting for Ring... " + MD_ring_secondS + " seconds<b></font>";
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
								//var dispnum = lead_dial_number;
								var dispnum = dialed_number;
								var status_display_number = dispnum;
								if (dispnum.length==10) status_display_number = '('+dispnum.substring(0,3)+')'+dispnum.substring(3,6)+'-'+dispnum.substring(6,10);
								document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;

								document.getElementById("MainStatuSSpan").innerHTML = " Called " + status_display_number + "&nbsp;&nbsp;&nbsp;&nbsp;<font color=" + status_bg + ">UID: " + CIDcheck + " &nbsp;</font>"; 

								document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_parkcall.gif\" width=145 height=16 border=0 alt=\"Park Call\"></a>";

								document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_hangupcustomer.gif\" width=145 height=16 border=0 alt=\"Hangup Customer\"></a>";

								document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_transferconf.gif\" width=145 height=16 border=0 alt=\"Transfer - Conference\"></a>";

								document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_localcloser.gif\" width=107 height=16 border=0 alt=\"LOCAL CLOSER\"></a>";

								document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_blindtransfer.gif\" width=137 height=16 border=0 alt=\"Dial Blind Transfer\"></a>";

								document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_ammessage.gif\" width=36 height=16 border=0 alt=\"Blind Transfer VMail Message\"></a>";
								document.getElementById("DialBlindVMail2").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_ammessage.gif\" width=36 height=16 border=0 alt=\"Blind Transfer VMail Message\"></a>";

								document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + MDchannel + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_up.gif\" width=28 height=15 BORDER=0></a>";
								document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + MDchannel + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_down.gif\" width=28 height=15 BORDER=0></a>";

								document.getElementById("DTMFDialPad0").innerHTML = "<a href=\"#\" alt=\"0\" onclick=\"document.osdial_form.conf_dtmf.value='0'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_0.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad1").innerHTML = "<a href=\"#\" alt=\"1\" onclick=\"document.osdial_form.conf_dtmf.value='1'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_1.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad2").innerHTML = "<a href=\"#\" alt=\"2 - ABC\" onclick=\"document.osdial_form.conf_dtmf.value='2'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_2.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad3").innerHTML = "<a href=\"#\" alt=\"3 - DEF\" onclick=\"document.osdial_form.conf_dtmf.value='3'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_3.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad4").innerHTML = "<a href=\"#\" alt=\"4 - GHI\" onclick=\"document.osdial_form.conf_dtmf.value='4'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_4.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad5").innerHTML = "<a href=\"#\" alt=\"5 - JKL\" onclick=\"document.osdial_form.conf_dtmf.value='5'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_5.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad6").innerHTML = "<a href=\"#\" alt=\"6 - MNO\" onclick=\"document.osdial_form.conf_dtmf.value='6'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_6.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad7").innerHTML = "<a href=\"#\" alt=\"7 - PQRS\" onclick=\"document.osdial_form.conf_dtmf.value='7'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_7.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad8").innerHTML = "<a href=\"#\" alt=\"8 - TUV\" onclick=\"document.osdial_form.conf_dtmf.value='8'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_8.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPad9").innerHTML = "<a href=\"#\" alt=\"9 - WXYZ\" onclick=\"document.osdial_form.conf_dtmf.value='9'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_9.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPadStar").innerHTML = "<a href=\"#\" alt=\"*\" onclick=\"document.osdial_form.conf_dtmf.value='*'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_star.png\" width=26 height=19 border=0></a>";
								document.getElementById("DTMFDialPadHash").innerHTML = "<a href=\"#\" alt=\"#\" onclick=\"document.osdial_form.conf_dtmf.value='#'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_hash.png\" width=26 height=19 border=0></a>";


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
			//osdalert("Dial timed out, contact your system administrator\n",30);
			//osdalert("Dial timed out, click Hangup and try again or dial next number.\n",30);
			//var dispnum = lead_dial_number;
			var dispnum = dialed_number;
			var status_display_number = dispnum;
			if (dispnum.length==10) status_display_number = '('+dispnum.substring(0,3)+')'+dispnum.substring(3,6)+'-'+dispnum.substring(6,10);
			document.getElementById("MainStatuSSpan").innerHTML = " Attempted: " + status_display_number + "&nbsp;&nbsp;<font color=" + status_alert_color + " style='text-decoration:blink;'><b>Dial timed out, click Hangup and try again or dial next number.<b></font>";
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
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			lead_cust2_cid = document.osdial_form.custom2.value;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			if (use_cid_areacode_map=='Y' && manDiaLonly_num.length==10) {
				for (var c=0; c<VARcid_areacodes.length; c++) {
					if (VARcid_areacodes[c] == manDiaLonly_num.substr(0,3)) {
						cid = VARcid_areacode_numbers[c];
						cid_name = VARcid_areacode_names[c];
					}
				}
			}
			if (use_custom2_callerid == 'Y' && lead_cust2_cid != '') {
				cid = lead_cust2_cid;
				cid_name = lead_cust2_cid;
			}
			var manual_dialcode = ''+document.osdial_form.phone_code.value;
			if (manual_dialcode!='1' && manual_dialcode.substring(0,1)!='0') manual_dialcode = '011' + manual_dialcode;
			manDiaLonly_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLonly&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&lead_id=" + document.osdial_form.lead_id.value + "&phone_number=" + manDiaLonly_num + "&phone_code=" + manual_dialcode + "&campaign=" + campaign + "&ext_context=" + ext_context + "&dial_context=" + dial_context + "&dial_timeout=" + dial_timeout + "&dial_prefix=" + dial_prefix + "&campaign_cid=" + cid + "&campaign_cid_name=" + cid_name + "&omit_phone_code=" + omit_phone_code;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			debug("<b>ManualDialOnly:</b> vdc_db_query: manDiaLonly_query=" + manDiaLonly_query,3);
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLonly_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var MDOnextResponse = null;
					//osdalert(xmlhttp.responseText,30);
					MDOnextResponse = xmlhttp.responseText;

					var MDOnextResponse_array=MDOnextResponse.split("\n");
					MDnextCID = MDOnextResponse_array[0];
					if (MDnextCID == " CALL NOT PLACED") {
						osdalert("call was not placed, there was an error:" + MDOnextResponse);
					} else {
						MD_channel_look=1;

						var status_display_number = formatPhone(document.osdial_form.phone_code.value,dialed_number);

						document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
						document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=" + status_bg + ">UID: " + MDnextCID + "</font> Waiting for Ring...";

						document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_hangupcustomer.gif\" width=145 height=16 border=0 alt=\"Hangup Customer\"></a>";

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
				document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADpause','NEW_ID');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_pause.gif\" width=70 height=18 border=0 alt=\" Pause \"></a><img src=\"templates/" + agent_template + "/images/vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\"></a><BR><a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\"></a>";
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
				document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume.gif\" width=70 height=18 border=0 alt=\"Resume\"></a><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
			} else {
				document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
			}
		}

		var xmlhttp=getXHR();
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
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			recheckVDAI_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=VDADREcheckINCOMING" + "&agent_log_id=" + agent_log_id + "&lead_id=" + document.osdial_form.lead_id.value;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(recheckVDAI_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var recheck_incoming = null;
					recheck_incoming = xmlhttp.responseText;
					//osdalert(xmlhttp.responseText,30);
					var recheck_VDIC_array=recheck_incoming.split("\n");
					if (recheck_VDIC_array[0] == '1') {
						var reVDIC_data_VDAC=recheck_VDIC_array[1].split("|");
						if (reVDIC_data_VDAC[3] == lastcustchannel) {
							// do nothing
						} else {
							//osdalert("Channel has changed from:\n" + lastcustchannel + '|' + lastcustserverip + "\nto:\n" + reVDIC_data_VDAC[3] + '|' + reVDIC_data_VDAC[4],30);
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
			var xmlhttp=getXHR();
			if (xmlhttp) { 
				var queryCID = "HLvdcW" + epoch_sec + user_abb;
				var hangupvalue = customer_channel;
				//osdalert(auto_dial_level + "|" + CalLCID + "|" + customer_server_ip + "|" + hangupvalue + "|" + VD_live_call_secondS,30);
				custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Hangup&format=text&user=" + user + "&pass=" + pass + "&channel=" + hangupvalue + "&call_server_ip=" + customer_server_ip + "&queryCID=" + queryCID + "&auto_dial_level=" + auto_dial_level + "&CalLCID=" + CalLCID + "&secondS=" + VD_live_call_secondS + "&exten=" + session_id;
				xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(custhangup_query); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
						//osdalert(xmlhttp.responseText,30);
						//var HU_debug = xmlhttp.responseText;
						//var HU_debug_array=HU_debug.split(" ");
						//if (HU_debug_array[0] == 'Call') {
							//osdalert(xmlhttp.responseText,30);
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
			conf_dialed=0;
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
			document.getElementById("WebFormSpan").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_webform_OFF.gif\" width=145 height=16 border=0 alt=\"Web Form\">";
			document.getElementById("WebFormSpan2").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_webform_OFF.gif\" width=145 height=16 border=0 alt=\"Web Form\">";
			document.getElementById("ParkControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_parkcall_OFF.gif\" width=145 height=16 border=0 alt=\"Park Call\">";
			document.getElementById("HangupControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_hangupcustomer_OFF.gif\" width=145 height=16 border=0 alt=\"Hangup Customer\">";
			document.getElementById("XferControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_transferconf_OFF.gif\" width=145 height=16 border=0 alt=\"Transfer - Conference\">";
			document.getElementById("LocalCloser").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_XB_localcloser_OFF.gif\" width=107 height=16 border=0 alt=\"LOCAL CLOSER\">";
			document.getElementById("DialBlindTransfer").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_XB_blindtransfer_OFF.gif\" width=137 height=16 border=0 alt=\"Dial Blind Transfer\">";
			document.getElementById("DialBlindVMail").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_XB_ammessage_OFF.gif\" width=36 height=16 border=0 alt=\"Blind Transfer VMail Message\">";
			document.getElementById("DialBlindVMail2").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_XB_ammessage_OFF.gif\" width=36 height=16 border=0 alt=\"Blind Transfer VMail Message\">";
			document.getElementById("VolumeUpSpan").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_volume_up_off.gif\" width=28 height=15 BORDER=0>";
			document.getElementById("VolumeDownSpan").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_volume_down_off.gif\" width=28 height=15 BORDER=0>";
			document.getElementById("RepullControl").innerHTML = "";

			document.getElementById("DTMFDialPad0").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_0_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad1").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_1_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad2").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_2_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad3").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_3_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad4").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_4_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad5").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_5_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad6").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_6_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad7").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_7_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad8").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_8_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPad9").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_9_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPadStar").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_star_OFF.png\" width=26 height=19 border=0>";
			document.getElementById("DTMFDialPadHash").innerHTML = "<img src=\"templates/" + agent_template + "/images/dtmf_hash_OFF.png\" width=26 height=19 border=0>";

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
						document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\"></a>";
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
								document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
								document.getElementById("MainStatuSSpan").innerHTML = '';
								if (inbound_man > 0) {
									document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\"><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
								} else {
									document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
								}
								reselect_alt_dial = 0;
							}
						}
					}
				} else {
					document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
					document.getElementById("MainStatuSSpan").innerHTML = '';
					if (inbound_man > 0) {
						document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\"><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
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
		if (MD_channel_look==1 && leaving_threeway<1) {
			MD_channel_look=0;
			DialTimeHangup('XFER');
		}
		if (xferchannel.length > 3) {
			var xmlhttp=getXHR();
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
						//osdalert(xmlhttp.responseText,30);
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

			document.getElementById("Leave3WayCall").innerHTML ="<img src=\"templates/" + agent_template + "/images/vdc_XB_leave3waycall_OFF.gif\" width=137 height=16 border=0 alt=\"LEAVE 3-WAY CALL\">";

			document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_dialwithcustomer.gif\" width=144 height=16 border=0 alt=\"Dial With Customer\"></a>";

			document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_parkcustomerdial.gif\" width=147 height=16 border=0 alt=\"Park Customer Dial\"></a>";

			document.getElementById("HangupXferLine").innerHTML ="<img src=\"templates/" + agent_template + "/images/vdc_XB_hangupxferline_OFF.gif\" width=145 height=16 border=0 alt=\"Hangup Xfer Line\">";

			document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_hangupbothlines.gif\" width=145 height=16 border=0 alt=\"Hangup Both Lines\"></a>";
		}
	}

// ################################################################################
// Send Hangup command for any Local call that is not in the quiet(7) entry - used to stop manual dials even if no connect
	function DialTimeHangup() {
		debug("<b>DialTimeHangup:</b>",2);
		if (RedirecTxFEr < 1 && leaving_threeway<1) {
			//osdalert("RedirecTxFEr|" + RedirecTxFEr,30);
			MD_channel_look=0;
			var xmlhttp=getXHR();
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
						//osdalert(xmlhttp.responseText,30);
					}
				}
				delete xmlhttp;
			}
		}
	}



// ################################################################################
// Generate the Call Disposition Chooser panel
function DispoSelectContent_create(taskDSgrp,taskDSstage) {
	debug("<b>DispoSelectContent_create:</b> taskDSgrp=" + taskDSstage,2);
	AgentDispoing = 1;
	var VD_statuses_ct_half = parseInt(VD_statuses_ct / 2);
	//var scroll = '';
	//if (VD_statuses_ct_half > 12) scroll="overflow-y:scroll;";
	//var dispo_HTML = "<br><table frame=border cellpadding=5 cellspacing=5 width=620><tr><td colspan=2 align=center><font color=" + dispo_fc + "><b>Call Dispositions</b></td></tr><tr><td colspan=2 align=center><div style=\"height:320;" + scroll + "\"><table cellpadding=5 cellspacing=5><tr><td bgcolor=\"" + dispo_bg + "\" height=320 width=300 valign=top><font class=\"log_text\"><div id=DispoSelectA>";
	var dispo_HTML = "<table cellpadding=5 cellspacing=5><tr><td bgcolor=\"" + dispo_bg + "\" height=320 width=300 valign=top><font class=\"log_text\"><div id=DispoSelectA>";
	var loop_ct = 0;
	while (loop_ct < VD_statuses_ct) {
		if (taskDSgrp == VARstatuses[loop_ct]) {
			dispo_HTML = dispo_HTML + "<font size=3 style=\"BACKGROUND-COLOR: " + dispo_bg2 + "\"><b><a href=\"#\" onclick=\"DispoSelect_submit();return false;\">" + VARstatuses[loop_ct] + " - " + VARstatusnames[loop_ct] + "</a></b></font><BR><BR>";
		} else {
			dispo_HTML = dispo_HTML + "<a href=\"#\" onclick=\"DispoSelectContent_create('" + VARstatuses[loop_ct] + "','ADD');return false;\">" + VARstatuses[loop_ct] + " - " + VARstatusnames[loop_ct] + "</a><font size=-2><BR><BR></font>";
		}
		if (loop_ct == VD_statuses_ct_half) {
			dispo_HTML = dispo_HTML + "</div></font></td><td bgcolor=\"" + dispo_bg + "\" height=320 width=300 valign=top><font class=\"log_text\"><div id=DispoSelectB>";
		}
		loop_ct++;
	}
	dispo_HTML = dispo_HTML + "</td></tr></table>";
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
			osdalert("You must hangup and disposition your call before clicking \"Pause\".");
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
			PauseCode_HTML = "<table frame=box bgcolor=" + pause_bg + " cellpadding=5 cellspacing=5 width=500><tr><td colspan=2 align=center><B><font color=" + pause_fc + ">Pause Codes</font></B></td></tr> <tr><td bgcolor=\"" + pause_bg2 + "\" height=300 width=240 valign=top><font class=\"log_text\"><span id=PauseCodeSelectA>";
			var loop_ct = 0;
			while (loop_ct < VD_pause_codes_ct) {
				PauseCode_HTML = PauseCode_HTML + "<font size=3 style=\"BACKGROUND-COLOR: " + pause_bg2 + "\"><b><a href=\"#\" onclick=\"PauseCodeSelect_submit('" + VARpause_codes[loop_ct] + "');return false;\">" + VARpause_codes[loop_ct] + " - " + VARpause_code_names[loop_ct] + "</a></b></font><BR><BR>";
				loop_ct++;
				if (loop_ct == VD_pause_codes_ct_half) {
					PauseCode_HTML = PauseCode_HTML + "</span></font></td><td bgcolor=\"" + pause_bg2 + "\" height=300 width=240 valign=top><font class=\"log_text\"><span id=PauseCodeSelectB>";
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

		leaving_threeway=0;
		blind_transfer=0;
		document.getElementById("callchannel").innerHTML = '';
		document.osdial_form.callserverip.value = '';
		document.osdial_form.xferchannel.value = '';
		document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_dialwithcustomer.gif\" border=0 alt=\"Dial With Customer\"></a>";
		document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_parkcustomerdial.gif\" border=0 alt=\"Park Customer Dial\"></a>";
		document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_hangupbothlines.gif\" border=0 alt=\"Hangup Both Lines\"></a>";

		var DispoChoice = document.osdial_form.DispoSelection.value;

		if (DispoChoice.length < 1) {
			osdalert("You Must Select a Disposition",5);
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
			submit_method = submit_method_tmp;

			DispoSelect_submit();
		}
	}


// ################################################################################
// Submit the Pause Code 
	function PauseCodeSelect_submit(newpausecode) {
		debug("<b>PauseCodeSelect_submit:</b> newpausecode=" + newpausecode,2);
		hideDiv('PauseCodeSelectBox');
		WaitingForNextStep=0;

		var xmlhttp=getXHR();
		if (xmlhttp) { 
			VMCpausecode_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=PauseCodeSubmit&format=text&status=" + newpausecode + "&agent_log_id=" + agent_log_id + "&campaign=" + campaign + "&extension=" + extension + "&protocol=" + protocol + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCpausecode_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(xmlhttp.responseText,30);
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
		var regAXFvars = new RegExp("AXFER","g");
		if (CalL_XC_a_NuMber.match(regAXFvars)) {
			mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
		} else {
			SendManualDial('YES');
		}
	}

	function DtMf_PreSet_b_DiaL() {
		debug("<b>DtMf_PreSet_b_DiaL:</b>",2);
		ShoWTransferMain("ON");
		document.osdial_form.conf_dtmf.value = CalL_XC_b_Dtmf;
		document.osdial_form.xfernumber.value = CalL_XC_b_NuMber;
		//basic_originate_call(CalL_XC_b_NuMber,'NO','YES',session_id,'YES');
		var regAXFvars = new RegExp("AXFER","g");
		if (CalL_XC_a_NuMber.match(regAXFvars)) {
			mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
		} else {
			SendManualDial('YES');
		}
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

		var xmlhttp=getXHR();
		if (xmlhttp) { 
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=OriginateVDRelogin&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=2" + session_id + "&ext_context=" + ext_context + "&ext_priority=1" + "&extension=" + extension + "&protocol=" + protocol + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&allow_sipsak_messages=" + allow_sipsak_messages + "&campaign=" + campaign;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(xmlhttp.responseText,30);
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
			var live_CSC_HTML = "<table class=acrossagent cellpadding=5 cellspacing=5 width=500><tr><td align=center><b><font color=" + closer_fc + ">Groups Not Selected</font></b></td><td align=center><b><font color=" + closer_fc + ">Selected Groups</font></b></td></tr><tr><td bgcolor=\"" + closer_bg + "\" height=300 width=240 valign=top><font class=\"log_text\"><span id=CloserSelectAdd>";
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
			live_CSC_HTML = live_CSC_HTML + "</span></font></td><td height=300 width=240 valign=top bgcolor=\"" + closer_bg + "\"><font class=\"log_text\"><span id=CloserSelectDelete></span></font></td></tr></table>";

			document.osdial_form.CloserSelectList.value = '';
			document.getElementById("CloserSelectContent").innerHTML = live_CSC_HTML;
		} else {
			VU_agent_choose_ingroups_DV = "MGRLOCK";
			var live_CSC_HTML = "<br><br><br><table frame=box><tr bgcolor=" + closer_bg + "><td><font color=" + closer_fc2 + ">&nbsp;Manager has selected groups for you!&nbsp;</font></td></tr></table><br>";
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

		//osdalert(taskCSgrp+"|"+taskCSchange+"|"+CloserSelectListValue.length+"|"+CSCchange+"|"+CSCcolumn,30)

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

		var xmlhttp=getXHR();
		if (xmlhttp) { 
			CSCupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=regCLOSER&format=text&user=" + user + "&pass=" + pass + "&comments=" + VU_agent_choose_ingroups_DV + "&closer_choice=" + CloserSelectChoices + "-";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(CSCupdate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(xmlhttp.responseText,30);
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
			osdalert("PLEASE CLICK THE LOGOUT LINK TO LOG OUT NEXT TIME!");
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
				osdalert("You cannot log out during a Manual Dial. Please click \"Dial Lead\" or \"Skip Lead\" (if available).");
				return;
			}
		} else if (alt_dial_menu==1) {
			if (tempreason=='CLOSE') {
				document.osdial_form.DispoSelection.value = 'NA';
				dialedcall_send_hangup('NO','YES');
			} else {
				osdalert("You cannot log out without reattempting or dispositioning this lead. You may reattempt by selecting \"Main Phone\", \"Alt Phone\", or \"Address3\". To disposition the lead, click \"Finish Lead\".");
				return;
			}
		} else if (MD_channel_look==1) {
			if (tempreason=='CLOSE') {
				document.osdial_form.DispoSelection.value = 'NA';
				dialedcall_send_hangup('NO','YES');
			} else {
				osdalert("You cannot log out during a Dial attempt. Wait 50 seconds for the dial to fail out if it is not answered.");
				return;
			}
		} else if (VD_live_customer_call==1) {
			if (tempreason=='CLOSE') {
				document.osdial_form.DispoSelection.value = 'NA';
				dialedcall_send_hangup('NO','YES');
			} else {
				osdalert("STILL A LIVE CALL! Hang it up then you can log out. " + VD_live_customer_call);
				return;
			}
		}
		if (previewFD_time > 0) {
			clearTimeout(previewFD_timeout_id);
			clearInterval(previewFD_display_id);
			document.getElementById("PreviewFDTimeSpan").innerHTML = "";
		}
		document.getElementById('MultiCallAlerTBoX').style.visibility='hidden';
		voicemail_ariclose();

		var xmlhttp=getXHR();
		if (xmlhttp) { 
			VDlogout_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=userLOGout&format=text&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&agent_log_id=" + agent_log_id + "&no_delete_sessions=" + no_delete_sessions + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&LogouTKicKAlL=" + LogouTKicKAlL + "&ext_context=" + ext_context;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			debug("<b>userLOGout called:</b> vdc_db_query.php?" + VDlogout_query,1);
			xmlhttp.send(VDlogout_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(VDlogout_query,30);
					//osdalert(xmlhttp.responseText,30);
				}
			}
			delete xmlhttp;
		}


		if (tempreason=='CLOSE') return;

		if (conf_channels_xtra_display==1) conf_channels_detail('HIDE');
		hideDiv('MainPanel');
		hideDiv('SysteMAlerTBoX');
		showDiv('LogouTBox');

			//document.getElementById("LogouTBoxLink").innerHTML = "<a href=\"" + agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass + "\"><img src='images/LoginAgainUp.png' width='128' height='28' align=center border='0'></a>";
				
		document.getElementById("LogouTBoxLink").innerHTML = "<map=Loginmap><a OnMouseOver=\"lagain.src='templates/" + agent_template + "/images/LoginAgainDn.png'\" OnMouseOut=\"lagain.src='templates/" + agent_template + "/images/LoginAgainUp.png'\" usemap=Loginmap href=\"" + agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass + "\"><img src='templates/" + agent_template + "/images/LoginAgainUp.png' width='128' height='28' align=center border='0' name=lagain></a>";

		logout_stop_timeouts = 1;
					
		//window.location= agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass;

	}



// ################################################################################
// disable enter/return keys to not clear out vars on customer info
	function enter_disable(evt) {
		debug("<b>enter_disable:</b> evt=" + evt,1);
		var e = evt? evt : window.event;
		if(!e) return;
		var key = 0;
		// for moz/fb, if keyCode==0 use 'which'
		if (e.keyCode) {
			key = e.keyCode;
		} else if (typeof(e.which)!= 'undefined') {
			key = e.which;
		}
		if (key == 13 && document.activeElement) {
			var cur = document.activeElement;
			if (cur.tagName == "INPUT" || cur.tagName == "SELECT") {
				var next;
				var titleField=0;
				for (var c=0; c<cur.form.length; c++) {
					if (cur.form[c].id=='title') titleField=c;
					if (cur.id == cur.form[c].id) {
						next = c+1;
						if (next==cur.form.length) next=titleField;
						if ((cur.form[next].tagName=='SELECT' || cur.form[next].readOnly==false) && cur.form[next].disabled==false && cur.form[next].type!='hidden') break;
					}
				}
				cur.form[next].focus();
			} else if (cur.tagName == "TEXTAREA") {
				return;
			}
		}
		return key != 13;
	}


// ################################################################################
// An additional encodeURIComponent which also encodes (+) plus.
	function encodeURIComponent2(component) {
		return encodeURIComponent(component).replace(/%20/g, '+');
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
		document.getElementById("DispoSelectBox").style.top = '340px';
		document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMaximize()\">maximize</a>";
	}


// ################################################################################
// Move the Dispo frame to the top and change the link to minimize
	function DispoMaximize() {
		debug("<b>DispoMaximize:</b>",2);
		document.getElementById("DispoSelectBox").style.top = '0px';
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
	function CB_date_pick(taskdate,ele) {
		debug("<b>CB_date_pick:</b> taskdate=" + taskdate,2);
        	if (CCALlast_pick) CCALlast_pick.style.backgroundColor = cal_bg1;
        	ele.style.backgroundColor = cal_bg5;
		document.osdial_form.CallBackDatESelectioN.value = taskdate;
		document.getElementById("CallBackDatEPrinT").innerHTML = taskdate;
        	CCALlast_pick = ele;
	}

    	function CBsel() {
        	if (document.osdial_form.CallBackDatESelectioN.value!='')
            	CallBackDatE_submit();
    	}


// ################################################################################
// Populating the date field in the postdate frame prior to submission
	function PD_date_pick(taskdate,ele) {
		debug("<b>PD_date_pick:</b> taskdate=" + taskdate,2);
        	if (PDCALlast_pick) PDCALlast_pick.style.backgroundColor = cal_bg1;
        	ele.style.backgroundColor = cal_bg5;
		document.osdial_form.PostDatESelectioN.value = taskdate;
		document.getElementById("PostDatEPrinT").innerHTML = taskdate;
        	PDCALlast_pick = ele;
	}

    	function PDsel() {
        	if (document.osdial_form.PostDatESelectioN.value!='')
            	PostDatE_submit();
    	}

// ################################################################################
// Submitting the post date and time to the system
	function PostDatE_submit() {
		debug("<b>PostDatE_submit:</b>",2);
		PostDatEForM = document.osdial_form.PostDatESelectioN.value;
		if (PostDatEForM.length < 2) {
			osdalert("You must choose a date",5);
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
		if (HK_statuses_camp>0 && (user_level>=HKuser_level || VU_hotkeys_active>0)) {
			document.onkeypress = hotkeypress;
		} else {
			document.onkeypress = enter_disable;
		}
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
			hideDiv('SysteMAlerTBoX');
			hideDiv('CustomerGoneBox');
			hideDiv('NoneInSessionBox');
			hideDiv('WrapupBox');
			hideDiv('TransferMain');
			hideDiv('CallBackSelectBox');
			hideDiv('PostDateSelectBox');
			hideDiv('DispoButtonHideA');
			hideDiv('DispoButtonHideB');
			hideDiv('DispoButtonHideC');
			hideDiv('CallBacKsLisTBox');
			hideDiv('NeWManuaLDiaLBox');
			hideDiv('PauseCodeSelectBox');
			if (scheduled_callbacks != '1') {
				hideDiv('CallbacksButtons');
			} else {
				showDiv('CallbacksButtons');
            		}
			//if ( (agentcall_manual != '1') && (starting_dial_level > 0) )
			if (agentcall_manual != '1') {
				hideDiv('ManuaLDiaLButtons');
			} else {
				showDiv('ManuaLDiaLButtons');
            		}
			if (agentcall_manual != '1') {
				hideDiv('PauseCodeButtons');
			} else {
				showDiv('PauseCodeButtons');
            		}
			if (callholdstatus != '1') {
				hideDiv('AgentStatusCalls');
			} else {
				showDiv('AgentStatusCalls');
            		}
			if (agentcallsstatus != '1') {
				hideDiv('AgentStatusSpan');
			} else {
				showDiv('AgentStatusSpan');
            		}
			if ( ( (auto_dial_level > 0) && (inbound_man != 0) ) || (manual_dial_preview < 1) ) {
				clearDiv('DiaLLeaDPrevieW');
			}
			if (alt_phone_dialing != 1) {
				clearDiv('DiaLDiaLAltPhonE');
			}
			if (volumecontrol_active != '1') {
				hideDiv('VolumeControlSpan');
			} else {
				showDiv('VolumeControlSpan');
            		}
			document.osdial_form.LeadLookuP.checked=true;

			if (agent_pause_codes_active=='Y') {
				showDiv('PauseCodeButtons');
			} else {
				hideDiv('PauseCodeButtons');
			}
			if (OSDiaL_allow_closers < 1) {
				document.getElementById("LocalCloser").style.visibility = 'hidden';
			} else {
				document.getElementById("LocalCloser").style.visibility = hide_xfer_local_closer;
			}
			document.getElementById("XferOverride").style.visibility = hide_xfer_dial_override;
			document.getElementById("HangupXferLine").style.visibility = hide_xfer_hangup_xfer;
			document.getElementById("Leave3WayCall").style.visibility = hide_xfer_leave_3way;
			document.getElementById("DialWithCustomer").style.visibility = hide_xfer_dial_with;
			document.getElementById("HangupBothLines").style.visibility = hide_xfer_hangup_both;
			document.getElementById("DialBlindTransfer").style.visibility = hide_xfer_blind_xfer;
			document.getElementById("ParkCustomerDial").style.visibility = hide_xfer_park_dial;
			document.getElementById("DialBlindVMail").style.visibility = hide_xfer_blind_vmail;

			document.getElementById("sessionIDspan").innerHTML = session_id;
			if ( (campaign_recording == 'NEVER') || (campaign_recording == 'ALLFORCE') ) {
				document.getElementById("RecorDControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_startrecording_OFF.gif\" width=145 height=16 border=0 alt=\"Start Recording\">";
			}
			if (INgroupCOUNT > 0 && (dial_method != "MANUAL" || inbound_man > 0)) {
			    hideDiv('WelcomeBoxA');
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
			hideDiv('WelcomeBoxA');
			document.getElementById('AddtlFormTab').style.visibility='visible';
			OSDiaL_closer_login_checked = 1;
		} else {

			var WaitingForNextStep=0;
			if (CloserSelecting==1)	{
				WaitingForNextStep=1;
			}
			if (open_dispo_screen==1) {
				document.getElementById('AddtlFormTab').style.visibility='hidden';
				document.getElementById('AddtlFormTabExpanded').style.visibility='hidden';
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
						document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\"></a>";

						document.getElementById("MainStatuSSpan").innerHTML = "Dial Next Call";
					} else {
						reselect_alt_dial = 0;
					}
				}
			}
			if (osdalert_timer>=0) {
				document.getElementById("SysteMAlerTTimer").innerHTML = osdalert_timer;
				if (osdalert_timer==0) hideDiv('SysteMAlerTBoX');
				osdalert_timer--;
			}

			// If the voicemail drop timer is set, start counting.  Xfer to voicemail when we hit 0.
			if (multicall_vmdrop_timer>=0) {
				document.getElementById("MulticallAlerTTimer").innerHTML = multicall_vmdrop_timer;
				if (multicall_vmdrop_timer==0) {
					multicall_send2voicemail();
					multicall_alert=0;
					document.getElementById('MultiCallAlerTBoX').style.visibility='hidden';
					multicall_waitchannel = multicall_waitserverip = multicall_waitcallerid = multicall_waituniqueid = "";
					multicall_waitleadid = multicall_waitingroup = multicall_waitvoicemail = "";
				}
				multicall_vmdrop_timer--;
			}

			// voicemail polling timer.
			if (vmail_check_timer--<=0) {
				check_voicemail();
				vmail_check_timer=30;
			}

			if (AgentDispoing>0) {
				WaitingForNextStep=1;
				check_for_conf_calls(session_id, '0');
				AgentDispoing++;
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

				//check for multicall incoming, but only if the check_for_auto_incoming did not hit.
				if (!(VD_live_customer_call==1 && VD_live_call_secondS==0)) {
					// multicall waiting, pick it up.
					if (call_queue_in_mc>0) {
						check_multicall_incoming();
					// multicall disappeared, clear any open alerts.
					} else if (multicall_alert>0) {
						multicall_alert=0;
						document.getElementById('MultiCallAlerTBoX').style.visibility='hidden';
						multicall_waitchannel = multicall_waitserverip = multicall_waitcallerid = multicall_waituniqueid = "";
						multicall_waitleadid = multicall_waitingroup = multicall_waitvoicemail = "";
						multicall_vmdrop_timer=-1;
					}
				}

				// If we have 2 call going, progressively change the button to flash faster over time.
				if (multicall_channel!='' && multicall_lastchannel!='') {
					var multicall_swapcalls_delay=1000;
					if (++multicall_liveseconds>180) {
						multicall_swapcalls_delay=200;
					} else if (multicall_liveseconds>60) {
						multicall_swapcalls_delay=400;
					} else if (multicall_liveseconds>30) {
						multicall_swapcalls_delay=600;
					} else if (multicall_liveseconds>15) {
						multicall_swapcalls_delay=800;
					}
					document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"multicall_queue_swap();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_swapcalls" + multicall_swapcalls_delay + ".gif\" width=145 height=16 border=0 alt=\"Swap Calls\"></a>";
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
					document.getElementById("voicemailbutton").innerHTML = "<a href=\"#\" onclick=\"voicemail_ariopen();\"><img src=\"templates/" + agent_template + "/images/agc_check_voicemail_OFF.gif\" width=170 height=30 border=0 alt=\"VOICEMAIL\"></a>";
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
						// Calls are gone (and wrapped), clean up all the multicall states.
						multicall_active = multicall_alert = multicall_liveseconds = 0;
						multicall_channel = multicall_serverip = multicall_callerid = multicall_ingroup = "";
						multicall_voicemail = multicall_uniqueid = multicall_leadid = "";
						multicall_lastchannel = multicall_lastserverip = multicall_lastcallerid = multicall_lastingroup = "";
						multicall_lastvoicemail = multicall_lastuniqueid = multicall_lastleadid = "";
						multicall_waitchannel = multicall_waitserverip = multicall_waitcallerid = multicall_waitingroup = "";
						multicall_waitvoicemail = multicall_waituniqueid = multicall_waitleadid = "";
						multicall_tmpchannel = multicall_tmpserverip = multicall_tmpcallerid = multicall_tmpingroup = "";
						multicall_tmpvoicemail = multicall_tmpuniqueid = multicall_tmpleadid = "";
						multicall_vmdrop_timer = -1;

						wrapup_waiting=0;
						hideDiv('WrapupBox');
						document.getElementById('AddtlFormTab').style.visibility='visible';
						document.getElementById('AddtlFormTabExpanded').style.visibility='hidden';
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
						check_voicemail();
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
		isodate = year + "-" + month + "-" + daym;
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
				var buildDivHTML = "<font class=\"preview_text\"><input type=checkbox name=LeadPreview id=LeadPreview size=1 value=\"0\"><label for=\"LeadPreview\"> LEAD PREVIEW</label><br></font>";
				document.getElementById("DiaLLeaDPrevieWHide").innerHTML = buildDivHTML;
			}
			if (divvar == 'DiaLDiaLAltPhonE') {
				var buildDivHTML = "<font class=\"preview_text\"><input type=checkbox name=DiaLAltPhonE id=DiaLAltPhonE size=1 value=\"0\"><label for=\"DiaLAltPhonE\"> ALT PHONE DIAL</label><br></font>";
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
				var buildDivHTML = "<font class=\"preview_text\"><input type=checkbox name=LeadPreview id=LeadPreview size=1 value=\"0\"><label for=\"LeadPreview\"> LEAD PREVIEW</label><br></font>";
				document.getElementById(divvar).innerHTML = buildDivHTML;
				if (reselect_preview_dial==1) {
					document.osdial_form.LeadPreview.checked=true;
				}
			}
			if (divvar == 'DiaLDiaLAltPhonE') {
				document.getElementById("DiaLDiaLAltPhonEHide").innerHTML = '';
				var buildDivHTML = "<font class=\"preview_text\"><input type=checkbox name=DiaLAltPhonE id=DiaLAltPhonE size=1 value=\"0\"><label for=\"DiaLAltPhonE\"> ALT PHONE DIAL</label><br></font>";
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
			document.getElementById("hotkeysdisplay").innerHTML = "<a href=\"#\" onMouseOut=\"HotKeys('OFF')\"><img src=\"templates/" + agent_template + "/images/vdc_XB_hotkeysactive.gif\" width=137 height=32 border=0 alt=\"HOT KEYS ACTIVE\"></a>";
		} else {
			hideDiv('HotKeyEntriesBox');
			hot_keys_active = 0;
			document.getElementById("hotkeysdisplay").innerHTML = "<a href=\"#\" onMouseOver=\"HotKeys('ON')\"><img src=\"templates/" + agent_template + "/images/vdc_XB_hotkeysactive_OFF.gif\" width=137 height=32 border=0 alt=\"HOT KEYS INACTIVE\"></a>";
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
				var xfer_height = HTheight;
				if (alt_phone_dialing>0) {
					xfer_height = (xfer_height + 20);
				}
				if ( (auto_dial_level == 0) && (manual_dial_preview == 1) ) {
					xfer_height = (xfer_height + 20);
				}
				document.getElementById("TransferMain").style.top = xfer_height;
				HKbutton_allowed = 0;
				showDiv('TransferMain');
				document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('OFF','YES');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_transferconf.gif\" width=145 height=16 border=0 alt=\"Transfer - Conference\"></a>";
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
					document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_transferconf.gif\" width=145 height=16 border=0 alt=\"Transfer - Conference\"></a>";
				}
			}
		} else {
			if (showxfervar != 'OFF') {
				osdalert('You do not have permissions to transfer calls.');
			}
		}
	}

	function MainPanelToFront(resumevar) {
		debug("<b>MainPanelToFront:</b> resumevar=" + resumevar,2);
		//document.getElementById("MainTable").style.backgroundColor=panel_bg;
		//document.getElementById("MaiNfooter").style.backgroundColor=panel_bg;
		voicemail_ariclose();
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
					document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume.gif\" width=70 height=18 border=0 alt=\"Resume\"></a><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
					if (manual_dial_preview == 1) {
						buildDiv('DiaLLeaDPrevieW');
					}
				} else {
					document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
					clearDiv('DiaLLeaDPrevieW');
				}
			}
		}
		panel_bgcolor=panel_bg;
		//document.getElementById("MainStatuSSpan").style.backgroundColor = panel_bgcolor;
		document.getElementById("FormButtons").src = "templates/" + agent_template + "/images/vdc_tab_buttons1.gif";
	}

	function ScriptPanelToFront() {
		debug("<b>ScriptPanelToFront:</b>",2);
		voicemail_ariclose();
		showDiv('ScriptPanel');
		//document.getElementById("MainTable").style.backgroundColor=script_bg;
		//document.getElementById("MaiNfooter").style.backgroundColor=script_bg;
		panel_bgcolor=panel_bg;
		//document.getElementById("MainStatuSSpan").style.backgroundColor= panel_bgcolor;
		document.getElementById("FormButtons").src = "templates/" + agent_template + "/images/vdc_tab_buttons2.gif";
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
			document.getElementById('WebFormPF1').src = '/agent/blank.php';
		}
		if (web_form2_extwindow == 0) {
			if (web_form_frame_open2 > 0) {
				document.getElementById('WebFormPanel2').style.visibility = 'hidden';
			}
			web_form_frame_open2 = 0;
			document.getElementById('WebFormPF2').src = '/agent/blank.php';
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
		debug("<b>ScriptButtonLog:</b> sid=" + sid + " sbid=" + sbid,2);

		var xmlhttp=getXHR();
		if (xmlhttp) { 
			sbl_data = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=ScriptButtonLog&format=text&user=" + user + "&pass=" + pass + "&lead_id=" + document.osdial_form.lead_id.value + "&script_id=" + sid + "&script_button_id=" + sbid;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(sbl_data); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(xmlhttp.responseText,30);
				}
			}
			delete xmlhttp;
		}
	}

// ################################################################################
// alert replacement function
	function osdalert(amess,atimer) {
        osdalert_timer=10;
        if (atimer>0) osdalert_timer=atimer;
		document.getElementById("SysteMAlerTInfo").style.backgroundColor = system_alert_bg2;
		document.getElementById("SysteMAlerTInfo").innerHTML = "<font style='text-decoration:blink;' size=3 color='" + status_alert_color + "'><b>ALERT:&nbsp;&nbsp;</b></font><font size=2>" + amess + "</font>";
		document.getElementById("SysteMAlerTTimer").innerHTML = osdalert_timer;
		showDiv('SysteMAlerTBoX');
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

			document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\"><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
		} else {
			document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
		}
		if (document.osdial_form.LeadPreview.checked==true) {
			reselect_preview_dial = 1;
			var man_preview = 'YES';
			var man_status = "&nbsp;&nbsp;&nbsp;&nbsp;<font style='text-decoration: blink;color:" + status_intense_color + ";'>Preview the Lead then <a href=\"#\" id=\"dialleadlink\" onclick=\"document.getElementById('dialleadlink').setAttribute('onclick','void(0);'); manual_dial_menu=0; ManualDialOnly();\"><font class=\"preview_text\" color=" + status_preview_color + ">DIAL LEAD</font></a><font style='{text-decoration: blink;color:" + status_intense_color + ";'>";
			if (manual_dial_allow_skip == 1) {
				man_status = man_status + " or </font><a href=\"#\" id=\"skipleadlink\"onclick=\"document.getElementById('skipleadlink').setAttribute('onclick','void(0);'); manual_dial_menu=0; ManualDialSkip();\"><font class=\"preview_text\" color=" + status_preview_color + ">SKIP LEAD</font></a>"; 
			} else {
				man_status = man_status + "</font>";
			}
		} else {
			reselect_preview_dial = 0;
			var man_preview = 'NO';
			var man_status = " Waiting for Ring..."; 
		}

		var xmlhttp=getXHR();
		if (xmlhttp) { 
			lead_cust2_cid = document.osdial_form.custom2.value;
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			if (use_cid_areacode_map=='Y' && mdnPhonENumbeR.length==10) {
				for (var c=0; c<VARcid_areacodes.length; c++) {
					if (VARcid_areacodes[c] == mdnPhonENumbeR.substr(0,3)) {
						cid = VARcid_areacode_numbers[c];
						cid_name = VARcid_areacode_names[c];
					}
				}
			}
			if (use_custom2_callerid == 'Y' && lead_cust2_cid != '') {
				cid = lead_cust2_cid;
				cid_name = lead_cust2_cid;
			}
			manDiaLnext_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLnextCaLL&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ext_context=" + ext_context + "&dial_context=" + dial_context + "&dial_timeout=" + dial_timeout + "&dial_prefix=" + dial_prefix + "&campaign_cid=" + cid + "&campaign_cid_name=" + cid_name + "&preview=" + man_preview + "&agent_log_id=" + agent_log_id + "&callback_id=" + mdnCBid + "&lead_id=" + mdnBDleadid + "&phone_code=" + mdnDiaLCodE + "&phone_number=" + mdnPhonENumbeR + "&list_id=" + mdnLisT_id + "&stage=" + mdnStagE  + "&use_internal_dnc=" + use_internal_dnc + "&omit_phone_code=" + omit_phone_code;
			debug("ManualDialNext: manDiaLnext_query: " + manDiaLnext_query,4);
			cid = campaign_cid;
			cid_name = campaign_cid_name;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLnext_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var MDnextResponse = null;
					debug("ManualDialNext: xmlhttp.responseText: " + xmlhttp.responseText,4);
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
							osdalert("No more leads in the hopper for campaign: " + campaign,60);
							alert_displayed=1;
						}
						if (MDnextCID.match(regMDFvarDNC)) {
							osdalert("This phone number is in the DNC list: " + mdnPhonENumbeR,30);
							alert_displayed=1;
						}
						if (MDnextCID.match(regMDFvarCAMP)) {
							osdalert("This phone number is not in the campaign lists: " + mdnPhonENumbeR,30);
							alert_displayed=1;
						}
						if (alert_displayed==0) {
							osdalert("Unspecified error: " + mdnPhonENumbeR + " | " + MDnextCID,60);
							alert_displayed=1;
						}

						if (starting_dial_level == 0) {
							document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\"></a>";
						} else {
							if (inbound_man > 0) {
								auto_dial_level=starting_dial_level;
								document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume.gif\" width=70 height=18 border=0 alt=\"Resume\"></a><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
							} else {
								document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
							}
							document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
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
						if (MDnextResponse_array[26]) MDnextResponse_array[26] = MDnextResponse_array[26].replace(REGcommentsNL, "\n");
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
						document.osdial_form.source_id.value										= MDnextResponse_array[34];
						document.osdial_form.custom2.value	= MDnextResponse_array[35];
						document.osdial_form.external_key.value										= MDnextResponse_array[36];
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

						var pos = 43;
						for (var i=0; i<AFids.length; i++) {
							debug('AFids: ' + AFids[i],2); 
							document.getElementById(AFids[i]).value = MDnextResponse_array[pos];
							pos++;
						}
						
						emailTemplatesDisable();

						lead_dial_number = dialed_number;
						var status_display_number = formatPhone(document.osdial_form.phone_code.value,dialed_number);

						document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
						document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=" + status_bg+ ">UID: " + MDnextCID + "</font> &nbsp; " + man_status;
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
							"&source_id=" + document.osdial_form.source_id.value + '' +
							"&external_key=" + document.osdial_form.external_key.value + '' +
							"&post_date=" + document.osdial_form.post_date.value + 
							"&recording_id=" + recording_id + 
							webform_session;

							for (var i=0; i<AFids.length; i++) {
								web_form_vars += '&' + AFnames[i] + '=' + document.getElementById(AFids[i]).value;
							}

						
						//$OSDIAL_web_QUERY_STRING =~ s/ /+/gi;
						//$OSDIAL_web_QUERY_STRING =~ s/\`|\~|\:|\;|\#|\'|\"|\{|\}|\(|\)|\*|\^|\%|\$|\!|\%|\r|\t|\n//gi;

						var regWFspace = new RegExp(" ","ig");
						web_form_vars = web_form_vars.replace(regWF, '');
						var regWF = new RegExp("\\`|\\~|\\:|\\;|\\#|\\'|\\\"|\\{|\\}|\\(|\\)|\\*|\\^|\\%|\\$|\\!|\\%|\\r|\\t|\\n","ig");
						web_form_vars = web_form_vars.replace(regWFspace, '+');
						web_form_vars = web_form_vars.replace(regWF, '');

						if (LeaDPreVDispO == 'CALLBK') {
							document.getElementById("CusTInfOSpaN").innerHTML = "&nbsp;&nbsp;<B><font color=" + status_intense_color + ">Previous Callback</font>&nbsp;</B>";
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
							document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOver=\"WebFormRefresH();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
						} else {
							document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
						}
							
						if (web_form2_extwindow == 1) {
							document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOver=\"WebFormRefresH();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
						} else {
							document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
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

								document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_hangupcustomer.gif\" width=145 height=16 border=0 alt=\"Hangup Customer\"></a>";

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
			osdalert('You cannot skip a Call-Back or a call placed to a manually entered number.');
		} else {
			if (previewFD_time > 0) {
				clearTimeout(previewFD_timeout_id);
				clearInterval(previewFD_display_id);
				document.getElementById("PreviewFDTimeSpan").innerHTML = "";
			}

			if (inbound_man > 0) {
				auto_dial_level=starting_dial_level;
				document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\"><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
			} else {
				document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
			}

			var xmlhttp=getXHR();
			if (xmlhttp) { 
				manDiaLskip_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLskip&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&lead_id=" + document.osdial_form.lead_id.value + "&stage=" + previous_dispo + "&called_count=" + previous_called_count;
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(manDiaLskip_query); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						var MDSnextResponse = null;
						//osdalert(manDiaLskip_query,30);
						//osdalert(xmlhttp.responseText,30);
						MDSnextResponse = xmlhttp.responseText;

						var MDSnextResponse_array=MDSnextResponse.split("\n");
						MDSnextCID = MDSnextResponse_array[0];
						if (MDSnextCID == "LEAD NOT REVERTED") {
							osdalert("Lead was not reverted, there was an error: " + MDSnextResponse);
						} else {
							previous_called_count = '';
							previous_dispo = '';
							custchannellive=1;

							afterCallClearing();

							document.getElementById("MainStatuSSpan").innerHTML = " Lead skipped, go on to next lead";

							if (inbound_man > 0) {
								AutoDial_ReSume_PauSe('VDADready');
							} else {
								document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\"></a>";
							}
						}
					}
				}
				delete xmlhttp;
			}
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
			var xmlhttprequestcheckauto=getXHR();
			if (xmlhttprequestcheckauto) { 
				checkVDAI_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=VDADcheckINCOMING" + "&agent_log_id=" + agent_log_id;
				debug("<b>check_for_auto_incoming:</b> checkVDAI_query: " + checkVDAI_query,5);
				xmlhttprequestcheckauto.open('POST', 'vdc_db_query.php'); 
				xmlhttprequestcheckauto.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttprequestcheckauto.send(checkVDAI_query); 
				xmlhttprequestcheckauto.onreadystatechange = function() { 
					if (xmlhttprequestcheckauto.readyState == 4 && xmlhttprequestcheckauto.status == 200) {
						var check_incoming = null;
						check_incoming = xmlhttprequestcheckauto.responseText;
						//osdalert(checkVDAI_query,30);
						//osdalert(xmlhttprequestcheckauto.responseText,30);
						var check_VDIC_array=check_incoming.split("\n");
						if (check_VDIC_array[0] == '1') {
							//osdalert(xmlhttprequestcheckauto.responseText,30);
							AutoDialWaiting = 0;

							voicemail_ariclose();

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
							document.osdial_form.source_id.value										= check_VDIC_array[38];
							document.osdial_form.custom2.value	= check_VDIC_array[39];
							document.osdial_form.external_key.value										= check_VDIC_array[40];
							document.osdial_form.post_date.value	= check_VDIC_array[41];

							var pos = 42;
							for (var i=0; i<AFids.length; i++) {
								document.getElementById(AFids[i]).value = check_VDIC_array[pos];
								pos++;
							}

							emailTemplatesDisable();

							lead_dial_number = dialed_number;
							var status_display_number = formatPhone(document.osdial_form.phone_code.value,dialed_number);

							document.getElementById("MainStatuSSpan").style.backgroundColor = '';
							document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=" + status_bg + ">UID: " + CIDcheck + "</font> &nbsp; " + VDIC_fronter; 

							document.getElementById("RepullControl").innerHTML = "<a href=\"#\" onclick=\"RepullLeadData('all');\"><img src=\"templates/" + agent_template + "/images/vdc_RPLD_on.gif\" width=145 height=16 border=0 alt=\"Repull Lead Data\"></a>";

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
								var status_display_number = formatPhone(document.osdial_form.phone_code.value,document.osdial_form.phone_number.value);

								document.getElementById("MainStatuSSpan").innerHTML = " Incoming: " + status_display_number + " Group- " + VDIC_data_VDIG[1] + " &nbsp; " + VDIC_fronter; 
							}

							document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_parkcall.gif\" width=145 height=16 border=0 alt=\"Park Call\"></a>";

							document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_hangupcustomer.gif\" width=145 height=16 border=0 alt=\"Hangup Customer\"></a>";

							document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_transferconf.gif\" width=145 height=16 border=0 alt=\"Transfer - Conference\"></a>";

							document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_localcloser.gif\" width=107 height=16 border=0 alt=\"LOCAL CLOSER\"></a>";

							document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_blindtransfer.gif\" width=137 height=16 border=0 alt=\"Dial Blind Transfer\"></a>";

							document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_ammessage.gif\" width=36 height=16 border=0 alt=\"Blind Transfer VMail Message\"></a>";
							document.getElementById("DialBlindVMail2").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_ammessage.gif\" width=36 height=16 border=0 alt=\"Blind Transfer VMail Message\"></a>";

							document.getElementById("DTMFDialPad0").innerHTML = "<a href=\"#\" alt=\"0\" onclick=\"document.osdial_form.conf_dtmf.value='0'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_0.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad1").innerHTML = "<a href=\"#\" alt=\"1\" onclick=\"document.osdial_form.conf_dtmf.value='1'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_1.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad2").innerHTML = "<a href=\"#\" alt=\"2 - ABC\" onclick=\"document.osdial_form.conf_dtmf.value='2'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_2.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad3").innerHTML = "<a href=\"#\" alt=\"3 - DEF\" onclick=\"document.osdial_form.conf_dtmf.value='3'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_3.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad4").innerHTML = "<a href=\"#\" alt=\"4 - GHI\" onclick=\"document.osdial_form.conf_dtmf.value='4'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_4.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad5").innerHTML = "<a href=\"#\" alt=\"5 - JKL\" onclick=\"document.osdial_form.conf_dtmf.value='5'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_5.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad6").innerHTML = "<a href=\"#\" alt=\"6 - MNO\" onclick=\"document.osdial_form.conf_dtmf.value='6'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_6.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad7").innerHTML = "<a href=\"#\" alt=\"7 - PQRS\" onclick=\"document.osdial_form.conf_dtmf.value='7'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_7.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad8").innerHTML = "<a href=\"#\" alt=\"8 - TUV\" onclick=\"document.osdial_form.conf_dtmf.value='8'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_8.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPad9").innerHTML = "<a href=\"#\" alt=\"9 - WXYZ\" onclick=\"document.osdial_form.conf_dtmf.value='9'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_9.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPadStar").innerHTML = "<a href=\"#\" alt=\"*\" onclick=\"document.osdial_form.conf_dtmf.value='*'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_star.png\" width=26 height=19 border=0></a>";
							document.getElementById("DTMFDialPadHash").innerHTML = "<a href=\"#\" alt=\"#\" onclick=\"document.osdial_form.conf_dtmf.value='#'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_hash.png\" width=26 height=19 border=0></a>";
	
							if (lastcustserverip == server_ip) {
								document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + lastcustchannel + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_up.gif\" width=28 height=15 BORDER=0></a>";
								document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + lastcustchannel + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_down.gif\" width=28 height=15 BORDER=0></a>";
							}

							if (inbound_man > 0) {
								document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\"><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
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
								"&source_id=" + document.osdial_form.source_id.value + '' +
								"&external_key=" + document.osdial_form.external_key.value + '' +
								"&post_date=" + document.osdial_form.post_date.value + 
								"&recording_id=" + recording_id + 
								webform_session;

								for (var i=0; i<AFids.length; i++) {
									web_form_vars += '&' + AFnames[i] + '=' + document.getElementById(AFids[i]).value;
								}

							
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
								document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOver=\"WebFormRefresH();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
							} else {
								document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
							}

							if (web_form2_extwindow == 1) {
								document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOver=\"WebFormRefresH();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
							} else {
								document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
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
				osdalert("Please enter a different phone number.",5);
				return;
			}
			var xmlhttprequestrepull=getXHR();
			if (xmlhttprequestrepull) {
				checkRPLD_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=RepullLeadData" + "&agent_log_id=" + agent_log_id + "&oldphone=" + oldphone + "&oldlead=" + oldlead + "&uniqueid=" + curuniqueid + "&lookup=" + lookup + "&list_id=" + list_id;
				xmlhttprequestrepull.open('POST', 'vdc_db_query.php'); 
				xmlhttprequestrepull.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttprequestrepull.send(checkRPLD_query); 
				xmlhttprequestrepull.onreadystatechange = function() { 
					if (xmlhttprequestrepull.readyState == 4 && xmlhttprequestrepull.status == 200) {
						var check_incoming = null;
						check_incoming = xmlhttprequestrepull.responseText;
						//osdalert(checkRPLD_query,30);
						//osdalert(xmlhttprequestrepull.responseText,30);
						var check_RPLD_array=check_incoming.split("\n");
						if (check_RPLD_array[0] > 0) { //<>
							//osdalert(xmlhttprequestrepull.responseText,30);

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
							document.osdial_form.external_key.value	= check_RPLD_array[26];
							document.osdial_form.post_date.value	= check_RPLD_array[27];

							if ( (dialed_label.length < 3) || (dialed_label=='NONE') )
								dialed_label='MAIN';
							dialed_number = oldphone;
							document.osdial_form.source_id.value = oldlead;

							var pos = 28;
							for (var i=0; i<AFids.length; i++) {
								document.getElementById(AFids[i]).value = check_RPLD_array[pos];
								pos++;
							}

							emailTemplatesDisable();

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
								"&source_id=" + document.osdial_form.source_id.value + '' +
								"&external_key=" + document.osdial_form.external_key.value + '' +
								"&post_date=" + document.osdial_form.post_date.value + '' +
								"&recording_id=" + recording_id + 
								webform_session;

								for (var i=0; i<AFids.length; i++) {
									web_form_vars += '&' + AFnames[i] + '=' + document.getElementById(AFids[i]).value;
								}

							
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
								document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOver=\"WebFormRefresH();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
							} else {
								document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
							}

							if (web_form2_extwindow == 1) {
								document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOver=\"WebFormRefresH();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
							} else {
								document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
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
			"&source_id=" + document.osdial_form.source_id.value + '' +
			"&external_key=" + document.osdial_form.external_key.value + '' +
			"&post_date=" + document.osdial_form.post_date.value +
			"&recording_id=" + recording_id + 
			webform_session;

			for (var i=0; i<AFids.length; i++) {
				web_form_vars += '&' + AFnames[i] + '=' + document.getElementById(AFids[i]).value;
			}

		
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
				document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOver=\"WebFormRefresH('IN');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
			} else {
				document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
			}

			if (web_form2_extwindow == 1) {
				document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOver=\"WebFormRefresH('IN');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
			} else {
				document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
			}
		} else {
			if (web_form_extwindow == 1) {
				document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + wf_enc_address + "\" target=\"webform\" onMouseOut=\"WebFormRefresH('OUT');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
			} else {
				document.getElementById("WebFormSpan").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay('" + wf_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform.gif\" width=145 height=16 border=0 alt=\"Web Form\"></a>";
			}

			if (web_form2_extwindow == 1) {
				document.getElementById("WebFormSpan2").innerHTML = "<a href=\"" + wf2_enc_address + "\" target=\"webform2\" onMouseOut=\"WebFormRefresH('OUT');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
			} else {
				document.getElementById("WebFormSpan2").innerHTML = "<a href='#' onclick=\"WebFormPanelDisplay2('" + wf2_enc_address + "');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_webform2.gif\" width=145 height=16 border=0 alt=\"Web Form2\"></a>";
			}
		}
	}


// ################################################################################
// Update osdial_list lead record with all altered values from form
	function CustomerData_update() {
		debug("<b>CustomerData_update:</b>",2);

		var xmlhttp=getXHR();
		if (xmlhttp) { 
			VLupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name +
				"&campaign=" + campaign +  "&ACTION=updateLEAD&format=text&user=" + user + "&pass=" + pass + 
				"&lead_id=" + encodeURIComponent2(document.osdial_form.lead_id.value) + 
				"&vendor_lead_code=" + encodeURIComponent2(document.osdial_form.vendor_lead_code.value) + 
				"&phone_code=" + encodeURIComponent2(document.osdial_form.phone_code.value) + 
				"&phone_number=" + encodeURIComponent2(document.osdial_form.phone_number.value) + 
				"&title=" + encodeURIComponent2(document.osdial_form.title.value) + 
				"&first_name=" + encodeURIComponent2(document.osdial_form.first_name.value) + 
				"&middle_initial=" + encodeURIComponent2(document.osdial_form.middle_initial.value) + 
				"&last_name=" + encodeURIComponent2(document.osdial_form.last_name.value) + 
				"&address1=" + encodeURIComponent2(document.osdial_form.address1.value) + 
				"&address2=" + encodeURIComponent2(document.osdial_form.address2.value) + 
				"&address3=" + encodeURIComponent2(document.osdial_form.address3.value) + 
				"&city=" + encodeURIComponent2(document.osdial_form.city.value) + 
				"&state=" + encodeURIComponent2(document.osdial_form.state.value) + 
				"&province=" + encodeURIComponent2(document.osdial_form.province.value) + 
				"&postal_code=" + encodeURIComponent2(document.osdial_form.postal_code.value) + 
				"&country_code=" + encodeURIComponent2(document.osdial_form.country_code.value) + 
				"&gender=" + encodeURIComponent2(document.osdial_form.gender.value) + 
				"&date_of_birth=" + encodeURIComponent2(document.osdial_form.date_of_birth.value) + 
				"&alt_phone=" + encodeURIComponent2(document.osdial_form.alt_phone.value) + 
				"&email=" + encodeURIComponent2(document.osdial_form.email.value) + 
				"&custom1=" + encodeURIComponent2(document.osdial_form.custom1.value) + 
				"&custom2=" + encodeURIComponent2(document.osdial_form.custom2.value) + 
				"&post_date=" + encodeURIComponent2(document.osdial_form.post_date.value);

			for (var i=0; i<AFids.length; i++) {
				VLupdate_query += '&' + AFids[i] + '=' + encodeURIComponent2(document.getElementById(AFids[i]).value);
			}

			VLupdate_query += "&comments=" + encodeURIComponent2(document.osdial_form.comments.value);

			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VLupdate_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					//osdalert(xmlhttp.responseText,30);
				}
			}
			delete xmlhttp;
		}
	}



// ################################################################################
// Update osdial_list lead record with disposition selection
	function DispoSelect_submit() {
		debug("<b>DispoSelect_submit:</b>",2);

		var group = campaign;
		if (VDCL_group_id.length > 1) group = VDCL_group_id;
		leaving_threeway=0;
		blind_transfer=0;
		document.getElementById("callchannel").innerHTML = '';
		document.osdial_form.callserverip.value = '';
		document.osdial_form.xferchannel.value = '';
		document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_dialwithcustomer.gif\" border=0 alt=\"Dial With Customer\"></a>";
		document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_parkcustomerdial.gif\" border=0 alt=\"Park Customer Dial\"></a>";
		document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_hangupbothlines.gif\" border=0 alt=\"Hangup Both Lines\"></a>";

		var DispoChoice = document.osdial_form.DispoSelection.value;

		if (DispoChoice.length < 1) {
			osdalert("You Must Select a Disposition",5);
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
			submit_method = submit_method_tmp;

			if ( (DispoChoice == 'CALLBK') && (scheduled_callbacks > 0) ) {
				showDiv('CallBackSelectBox');
			} else if ( (DispoChoice == 'PD' && PostDatETimE == '' && (document.osdial_form.post_date.value == '0000-00-00' || document.osdial_form.post_date.value == '0000-00-00 00:00:00') ) ) {
				showDiv('PostDateSelectBox');
			} else {
				emailTemplatesSend();

				var xmlhttp=getXHR();
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

				afterCallClearing();

				var rp_newid="NEW_ID";
				if (manual_dial_in_progress==1) {
					manual_dial_finished();
					rp_newid="";
				}
				hideDiv('DispoSelectBox');
				hideDiv('DispoButtonHideA');
				hideDiv('DispoButtonHideB');
				hideDiv('DispoButtonHideC');
				document.getElementById("DispoSelectBox").style.top = '0px';
				document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMinimize()\">minimize</a>";
				document.getElementById("DispoSelectHAspan").innerHTML = "<a href=\"#\" onclick=\"DispoHanguPAgaiN()\">Hangup Again</a>";

				document.getElementById("RecorDingFilename").innerHTML = "&nbsp;";
				document.getElementById("RecorDID").innerHTML = "&nbsp;";
				recording_id=0;

				document.getElementById("MainStatuSSpan").style.backgroundColor = status_bg;
				document.getElementById("MainStatuSSpan").innerHTML = "";

				CloseWebFormPanels();

				CBcommentsBoxhide();

				AgentDispoing = 0;

				// We just hungup the active multicall, so clear its associated variabled.
				if (multicall_active>0) {
					multicall_lastchannel = multicall_lastserverip = multicall_lastcallerid = multicall_lastingroup = "";
					multicall_lastvoicemail = multicall_lastuniqueid = multicall_lastleadid = "";
					// If there both multicall sets are empty flag the multicall as inactive.
					if (multicall_channel=='' && multicall_lastchannel=='') multicall_active=0;
				}

				// If the multicall is active, swap after hangup to grab the other channel.
				if (multicall_active>0) {
					multicall_queue_swap();
					document.getElementById('AddtlFormTab').style.visibility='visible';
					document.getElementById('AddtlFormTabExpanded').style.visibility='hidden';
				} else {
					if (wrapup_waiting == 0) {
						//We are all done with the call, clean up all the multicall states.
						multicall_active = multicall_alert = multicall_liveseconds = 0;
						multicall_channel = multicall_serverip = multicall_callerid = multicall_ingroup = "";
						multicall_voicemail = multicall_uniqueid = multicall_leadid = "";
						multicall_lastchannel = multicall_lastserverip = multicall_lastcallerid = multicall_lastingroup = "";
						multicall_lastvoicemail = multicall_lastuniqueid = multicall_lastleadid = "";
						multicall_waitchannel = multicall_waitserverip = multicall_waitcallerid = multicall_waitingroup = "";
						multicall_waitvoicemail = multicall_waituniqueid = multicall_waitleadid = "";
						multicall_tmpchannel = multicall_tmpserverip = multicall_tmpcallerid = multicall_tmpingroup = "";
						multicall_tmpvoicemail = multicall_tmpuniqueid = multicall_tmpleadid = "";
						multicall_vmdrop_timer = -1;

						document.getElementById('AddtlFormTab').style.visibility='visible';
						document.getElementById('AddtlFormTabExpanded').style.visibility='hidden';
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
						check_voicemail();
					}
				}
			}
		}
	}



// ################################################################################
// Do the variable substituion on the given string.
	function webform_rewrite(wf_encoded) {
		debug("<b>webform_rewrite:</b> wf_encoded=" + wf_encoded,2);

		var SCvendor_lead_code = encodeURIComponent2(document.osdial_form.vendor_lead_code.value);
		var SCsource_id = encodeURIComponent2(document.osdial_form.source_id.value);
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
		var SCexternal_key = encodeURIComponent2(document.osdial_form.external_key.value);
		var SCpost_date = encodeURIComponent2(document.osdial_form.post_date.value);
		var SCrecording_id = encodeURIComponent2(recording_id);
		//var SCwebform_session = encodeURIComponent2(webform_session);


		// New Variable substitution
		var RGsource_id = new RegExp("\\[\\[source_id\\]\\]","g");
		var RGlist_id = new RegExp("\\[\\[list_id\\]\\]","g");
		var RGgmt_offset_now = new RegExp("\\[\\[gmt_offset_now\\]\\]","g");
		var RGcalled_since_last_reset = new RegExp("\\[\\[called_since_last_reset\\]\\]","g");
		var RGphone = new RegExp("\\[\\[phone\\]\\]","g");
		var RGdialed_number = new RegExp("\\[\\[dialed_number\\]\\]","g");
		var RGdialed_label = new RegExp("\\[\\[dialed_label\\]\\]","g");
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
		var RGrecording_id = new RegExp("\\[\\[recording_id\\]\\]","g");
		//var RGwebform_session = new RegExp("\\[\\[webform_session\\]\\]","g");

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
		var RGphone_code = new RegExp("\\[\\[phone_code\\]\\]","g");
		var RGphone_number = new RegExp("\\[\\[phone_number\\]\\]","g");
		var RGalt_phone = new RegExp("\\[\\[alt_phone\\]\\]","g");
		var RGgender = new RegExp("\\[\\[gender\\]\\]","g");
		var RGdate_of_birth = new RegExp("\\[\\[date_of_birth\\]\\]","g");
		var RGemail = new RegExp("\\[\\[email\\]\\]","g");
		var RGvendor_lead_code = new RegExp("\\[\\[vendor_lead_code\\]\\]","g");
		var RGcomments = new RegExp("\\[\\[comments\\]\\]","g");
		var RGcustom1 = new RegExp("\\[\\[custom1\\]\\]","g");
		var RGcustom2 = new RegExp("\\[\\[custom2\\]\\]","g");
		var RGpost_date = new RegExp("\\[\\[post_date\\]\\]","g");

		// Editable Fields
		var RGEFtitle = new RegExp("\\[\\[EFtitle\\]\\]","g");
		var RGEFfirst_name = new RegExp("\\[\\[EFfirst_name\\]\\]","g");
		var RGEFmiddle_initial = new RegExp("\\[\\[EFmiddle_initial\\]\\]","g");
		var RGEFlast_name = new RegExp("\\[\\[EFlast_name\\]\\]","g");
		var RGEFaddress1 = new RegExp("\\[\\[EFaddress1\\]\\]","g");
		var RGEFaddress2 = new RegExp("\\[\\[EFaddress2\\]\\]","g");
		var RGEFaddress3 = new RegExp("\\[\\[EFaddress3\\]\\]","g");
		var RGEFcity = new RegExp("\\[\\[EFcity\\]\\]","g");
		var RGEFstate = new RegExp("\\[\\[EFstate\\]\\]","g");
		var RGEFprovince = new RegExp("\\[\\[EFprovince\\]\\]","g");
		var RGEFpostal_code = new RegExp("\\[\\[EFpostal_code\\]\\]","g");
		var RGEFcountry_code = new RegExp("\\[\\[EFcountry_code\\]\\]","g");
		var RGEFphone_code = new RegExp("\\[\\[EFphone_code\\]\\]","g");
		var RGEFphone_number = new RegExp("\\[\\[EFphone_number\\]\\]","g");
		var RGEFalt_phone = new RegExp("\\[\\[EFalt_phone\\]\\]","g");
		var RGEFgender = new RegExp("\\[\\[EFgender\\]\\]","g");
		var RGEFdate_of_birth = new RegExp("\\[\\[EFdate_of_birth\\]\\]","g");
		var RGEFemail = new RegExp("\\[\\[EFemail\\]\\]","g");
		var RGEFvendor_lead_code = new RegExp("\\[\\[EFvendor_lead_code\\]\\]","g");
		var RGEFcomments = new RegExp("\\[\\[EFcomments\\]\\]","g");
		var RGEFcustom1 = new RegExp("\\[\\[EFcustom1\\]\\]","g");
		var RGEFcustom2 = new RegExp("\\[\\[EFcustom2\\]\\]","g");
		var RGEFpost_date = new RegExp("\\[\\[EFpost_date\\]\\]","g");

		for (var i=0; i<AFids.length; i++) {
			wf_encoded = wf_encoded.replace(
				new RegExp("\\[\\[" + AFnames[i] + "\\]\\]","g"),
				encodeURIComponent2(document.getElementById(AFids[i]).value));
		}

		// New substitution
		wf_encoded = wf_encoded.replace(RGsource_id, SCsource_id);
		wf_encoded = wf_encoded.replace(RGlist_id, SClist_id);
		wf_encoded = wf_encoded.replace(RGgmt_offset_now, SCgmt_offset_now);
		wf_encoded = wf_encoded.replace(RGcalled_since_last_reset, SCcalled_since_last_reset);
		wf_encoded = wf_encoded.replace(RGphone, SCphone);
		wf_encoded = wf_encoded.replace(RGdialed_number, SCdialed_number);
		wf_encoded = wf_encoded.replace(RGdialed_label, SCdialed_label);
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
		wf_encoded = wf_encoded.replace(RGrecording_id, SCrecording_id);
		//wf_encoded = wf_encoded.replace(RGwebform_session, SCwebform_session);

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
		wf_encoded = wf_encoded.replace(RGphone_code, SCphone_code);
		wf_encoded = wf_encoded.replace(RGphone_number, SCphone_number);
		wf_encoded = wf_encoded.replace(RGalt_phone, SCalt_phone);
		wf_encoded = wf_encoded.replace(RGgender, SCgender);
		wf_encoded = wf_encoded.replace(RGdate_of_birth, SCdate_of_birth);
		wf_encoded = wf_encoded.replace(RGvendor_lead_code, SCvendor_lead_code);
		wf_encoded = wf_encoded.replace(RGemail, SCemail);
		wf_encoded = wf_encoded.replace(RGcomments, SCcomments);
		wf_encoded = wf_encoded.replace(RGcustom1, SCcustom1);
		wf_encoded = wf_encoded.replace(RGcustom2, SCcustom2);
		wf_encoded = wf_encoded.replace(RGpost_date, SCpost_date);

		// Editable Fields
		wf_encoded = wf_encoded.replace(RGEFtitle, SCtitle);
		wf_encoded = wf_encoded.replace(RGEFfirst_name, SCfirst_name);
		wf_encoded = wf_encoded.replace(RGEFmiddle_initial, SCmiddle_initial);
		wf_encoded = wf_encoded.replace(RGEFlast_name, SClast_name);
		wf_encoded = wf_encoded.replace(RGEFaddress1, SCaddress1);
		wf_encoded = wf_encoded.replace(RGEFaddress2, SCaddress2);
		wf_encoded = wf_encoded.replace(RGEFaddress3, SCaddress3);
		wf_encoded = wf_encoded.replace(RGEFcity, SCcity);
		wf_encoded = wf_encoded.replace(RGEFstate, SCstate);
		wf_encoded = wf_encoded.replace(RGEFprovince, SCprovince);
		wf_encoded = wf_encoded.replace(RGEFpostal_code, SCpostal_code);
		wf_encoded = wf_encoded.replace(RGEFcountry_code, SCcountry_code);
		wf_encoded = wf_encoded.replace(RGEFphone_code, SCphone_code);
		wf_encoded = wf_encoded.replace(RGEFphone_number, SCphone_number);
		wf_encoded = wf_encoded.replace(RGEFalt_phone, SCalt_phone);
		wf_encoded = wf_encoded.replace(RGEFemail, SCemail);
		wf_encoded = wf_encoded.replace(RGEFcomments, SCcomments);
		wf_encoded = wf_encoded.replace(RGEFdate_of_birth, SCdate_of_birth);
		wf_encoded = wf_encoded.replace(RGEFgender, SCgender);
		wf_encoded = wf_encoded.replace(RGEFpost_date, SCpost_date);
		wf_encoded = wf_encoded.replace(RGEFvendor_lead_code, SCvendor_lead_code);
		wf_encoded = wf_encoded.replace(RGEFcustom1, SCcustom1);
		wf_encoded = wf_encoded.replace(RGEFcustom2, SCcustom2);

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
			var SCsource_id = document.osdial_form.source_id.value;
			var SClist_id = document.osdial_form.list_id.value;
			var SCgmt_offset_now = document.osdial_form.gmt_offset_now.value;
			var SCcalled_since_last_reset = "";
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

			var SCtitle = document.osdial_form.title.value;
			var SCfirst_name = document.osdial_form.first_name.value;
			var SCmiddle_initial = document.osdial_form.middle_initial.value;
			var SClast_name = document.osdial_form.last_name.value;
			var SCaddress1 = document.osdial_form.address1.value;
			var SCaddress2 = document.osdial_form.address2.value;
			var SCaddress3 = document.osdial_form.address3.value;
			var SCcity = document.osdial_form.city.value;
			var SCstate = document.osdial_form.state.value;
			var SCpostal_code = document.osdial_form.postal_code.value;
			var SCprovince = document.osdial_form.province.value;
			var SCcountry_code = document.osdial_form.country_code.value;
			var SCemail = document.osdial_form.email.value;
			var SCphone_code = document.osdial_form.phone_code.value;
			var SCphone_number = document.osdial_form.phone_number.value;
			var SCalt_phone = document.osdial_form.alt_phone.value;
			var SCcomments = document.osdial_form.comments.value;
			var SCdate_of_birth = document.osdial_form.date_of_birth.value;
			var SCgender = document.osdial_form.gender.value;
			var SCpost_date = document.osdial_form.post_date.value;
			var SCvendor_lead_code = document.osdial_form.vendor_lead_code.value;
			var SCcustom1 = document.osdial_form.custom1.value;
			var SCcustom2 = document.osdial_form.custom2.value;

			// Editable Fields
			var SCEFVtitle =            document.osdial_form.title.value;
			var SCEFVfirst_name =       document.osdial_form.first_name.value;
			var SCEFVmiddle_initial =   document.osdial_form.middle_initial.value;
			var SCEFVlast_name =        document.osdial_form.last_name.value;
			var SCEFVaddress1 =         document.osdial_form.address1.value;
			var SCEFVaddress2 =         document.osdial_form.address2.value;
			var SCEFVaddress3 =         document.osdial_form.address3.value;
			var SCEFVcity =             document.osdial_form.city.value;
			var SCEFVstate =            document.osdial_form.state.value;
			var SCEFVpostal_code =      document.osdial_form.postal_code.value;
			var SCEFVprovince =         document.osdial_form.province.value;
			var SCEFVcountry_code =     document.osdial_form.country_code.value;
			var SCEFVemail =            document.osdial_form.email.value;
			var SCEFVphone_code =       document.osdial_form.phone_code.value;
			var SCEFVphone_number =     document.osdial_form.phone_number.value;
			var SCEFValt_phone =        document.osdial_form.alt_phone.value;
			var SCEFVcomments =         document.osdial_form.comments.value;
			var SCEFVdate_of_birth =    document.osdial_form.date_of_birth.value;
			var SCEFVgender =           document.osdial_form.gender.value;
			var SCEFVpost_date =        document.osdial_form.post_date.value;
			var SCEFVvendor_lead_code = document.osdial_form.vendor_lead_code.value;
			var SCEFVcustom1 =          document.osdial_form.custom1.value;
			var SCEFVcustom2 =          document.osdial_form.custom2.value;

			var SCEFtitle =            '<input type=text size=4 maxlength=4 name=EFtitle id=EFtitle class=cust_form ' + scriptEFCreateJS('title') + ' >';
			var SCEFfirst_name =       '<input type=text size=14 maxlength=30 name=EFfirst_name id=EFfirst_name class=cust_form ' + scriptEFCreateJS('first_name') + ' >';
			var SCEFmiddle_initial =   '<input type=text size=1 maxlength=1 name=EFmiddle_initial id=EFmiddle_initial class=cust_form ' + scriptEFCreateJS('middle_initial') + ' >';
			var SCEFlast_name =        '<input type=text size=15 maxlength=30 name=EFlast_name id=EFlast_name class=cust_form ' + scriptEFCreateJS('last_name') + ' >';
			var SCEFaddress1 =         '<input type=text size=58 maxlength=100 name=EFaddress1 id=EFaddress1 class=cust_form ' + scriptEFCreateJS('address1') + ' >';
			var SCEFaddress2 =         '<input type=text size=22 maxlength=100 name=EFaddress2 id=EFaddress2 class=cust_form ' + scriptEFCreateJS('address2') + ' >';
			var SCEFaddress3 =         '<input type=text size=22 maxlength=100 name=EFaddress3 id=EFaddress3 class=cust_form ' + scriptEFCreateJS('address3') + ' >';
			var SCEFcity =             '<input type=text size=22 maxlength=50 name=EFcity id=EFcity class=cust_form ' + scriptEFCreateJS('city') + ' >';
			var SCEFstate =            '<input type=text size=2 maxlength=2 name=EFstate id=EFstate class=cust_form ' + scriptEFCreateJS('state') + ' >';
			var SCEFpostal_code =      '<input type=text size=9 maxlength=10 name=EFpostal_code id=EFpostal_code class=cust_form ' + scriptEFCreateJS('postal_code') + ' >';
			var SCEFprovince =         '<input type=text size=22 maxlength=50 name=EFprovince id=EFprovince class=cust_form ' + scriptEFCreateJS('province') + ' >';
			var SCEFcountry_code =     '<input type=text size=5 maxlength=5 name=EFcountry_code id=EFcountry_code class=cust_form ' + scriptEFCreateJS('country_code') + ' >';
			var SCEFemail =            '<input type=text size=22 maxlength=70 name=EFemail id=EFemail class=cust_form ' + scriptEFCreateJS('email') + ' >';
			var SCEFphone_code =       '<input type=text size=4 maxlength=10 name=EFphone_code id=EFphone_code class=cust_form ' + scriptEFCreateJS('phone_code') + ' >';
			var SCEFphone_number =     '<input type=text size=11 maxlength=12 name=EFphone_number id=EFphone_number class=cust_form ' + scriptEFCreateJS('phone_number') + ' >';
			var SCEFalt_phone =        '<input type=text size=12 maxlength=12 name=EFalt_phone id=EFalt_phone class=cust_form ' + scriptEFCreateJS('alt_phone') + ' >';
			var SCEFcomments =         '';
			if (multi_line_comments) {
				SCEFcomments =         '<textarea rows=2 cols=56 name=EFcomments id=EFcomments class=cust_form ' + scriptEFCreateJS('comments') + ' ></textarea>';
			} else {
				SCEFcomments =         '<input type=text size=56 maxlength=255 name=EFcomments id=EFcomments class=cust_form ' + scriptEFCreateJS('comments') + ' >';
			}
			var SCEFdate_of_birth =    '<input type=text size=12 maxlength=10 name=EFdate_of_birth id=EFdate_of_birth class=cust_form ' + scriptEFCreateJS('date_of_birth') + ' >';
			var SCEFgender =           '<select name=EFgender id=EFgender class=cust_form ' + scriptEFCreateJS('gender') + ' ><option></option><option>M</option><option>F</option></select>';
			var SCEFpost_date =        '<input type=text size=12 maxlength=10 name=EFpost_date id=EFpost_date class=cust_form ' + scriptEFCreateJS('post_date') + ' >';
			var SCEFvendor_lead_code = '<input type=text size=15 maxlength=20 name=EFvendor_lead_code id=EFvendor_lead_code class=cust_form ' + scriptEFCreateJS('vendor_lead_code') + ' >';
			var SCEFcustom1 =          '<input type=text size=22 maxlength=100 name=EFcustom1 id=EFcustom1 class=cust_form ' + scriptEFCreateJS('custom1') + ' >';
			var SCEFcustom2 =          '<input type=text size=22 maxlength=100 name=EFcustom2 id=EFcustom2 class=cust_form ' + scriptEFCreateJS('custom2') + ' >';



			if (encoded.match(RGiframe)) {
				SCsource_id = SCsource_id.replace(RGplus,'+');
				SClist_id = SClist_id.replace(RGplus,'+');
				SCgmt_offset_now = SCgmt_offset_now.replace(RGplus,'+');
				SCcalled_since_last_reset = SCcalled_since_last_reset.replace(RGplus,'+');
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

				SCtitle = SCtitle.replace(RGplus,'+');
				SCfirst_name = SCfirst_name.replace(RGplus,'+');
				SCmiddle_initial = SCmiddle_initial.replace(RGplus,'+');
				SClast_name = SClast_name.replace(RGplus,'+');
				SCaddress1 = SCaddress1.replace(RGplus,'+');
				SCaddress2 = SCaddress2.replace(RGplus,'+');
				SCaddress3 = SCaddress3.replace(RGplus,'+');
				SCcity = SCcity.replace(RGplus,'+');
				SCstate = SCstate.replace(RGplus,'+');
				SCpostal_code = SCpostal_code.replace(RGplus,'+');
				SCprovince = SCprovince.replace(RGplus,'+');
				SCcountry_code = SCcountry_code.replace(RGplus,'+');
				SCphone_code = SCphone_code.replace(RGplus,'+');
				SCphone_number = SCphone_number.replace(RGplus,'+');
				SCalt_phone = SCalt_phone.replace(RGplus,'+');
				SCemail = SCemail.replace(RGplus,'+');
				SCcomments = SCcomments.replace(RGplus,'+');
				SCdate_of_birth = SCdate_of_birth.replace(RGplus,'+');
				SCgender = SCgender.replace(RGplus,'+');
				SCpost_date = SCpost_date.replace(RGplus,'+');
				SCvendor_lead_code = SCvendor_lead_code.replace(RGplus,'+');
				SCcustom1 = SCcustom1.replace(RGplus,'+');
				SCcustom2 = SCcustom2.replace(RGplus,'+');

				// Editable Fields
				SCEFtitle = SCEFtitle.replace(RGplus,'+');
				SCEFfirst_name = SCEFfirst_name.replace(RGplus,'+');
				SCEFmiddle_initial = SCEFmiddle_initial.replace(RGplus,'+');
				SCEFlast_name = SCEFlast_name.replace(RGplus,'+');
				SCEFaddress1 = SCEFaddress1.replace(RGplus,'+');
				SCEFaddress2 = SCEFaddress2.replace(RGplus,'+');
				SCEFaddress3 = SCEFaddress3.replace(RGplus,'+');
				SCEFcity = SCEFcity.replace(RGplus,'+');
				SCEFstate = SCEFstate.replace(RGplus,'+');
				SCEFpostal_code = SCEFpostal_code.replace(RGplus,'+');
				SCEFprovince = SCEFprovince.replace(RGplus,'+');
				SCEFcountry_code = SCEFcountry_code.replace(RGplus,'+');
				SCEFphone_code = SCEFphone_code.replace(RGplus,'+');
				SCEFphone_number = SCEFphone_number.replace(RGplus,'+');
				SCEFalt_phone = SCEFalt_phone.replace(RGplus,'+');
				SCEFemail = SCEFemail.replace(RGplus,'+');
				SCEFcomments = SCEFcomments.replace(RGplus,'+');
				SCEFdate_of_birth = SCEFdate_of_birth.replace(RGplus,'+');
				SCEFgender = SCEFgender.replace(RGplus,'+');
				SCEFpost_date = SCEFpost_date.replace(RGplus,'+');
				SCEFvendor_lead_code = SCEFvendor_lead_code.replace(RGplus,'+');
				SCEFcustom1 = SCEFcustom1.replace(RGplus,'+');
				SCEFcustom2 = SCEFcustom2.replace(RGplus,'+');

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
			var RGpost_date = new RegExp("\\[\\[post_date\\]\\]","g");

			// Editable Fields
			var RGEFtitle = new RegExp("\\[\\[EFtitle\\]\\]","g");
			var RGEFfirst_name = new RegExp("\\[\\[EFfirst_name\\]\\]","g");
			var RGEFmiddle_initial = new RegExp("\\[\\[EFmiddle_initial\\]\\]","g");
			var RGEFlast_name = new RegExp("\\[\\[EFlast_name\\]\\]","g");
			var RGEFaddress1 = new RegExp("\\[\\[EFaddress1\\]\\]","g");
			var RGEFaddress2 = new RegExp("\\[\\[EFaddress2\\]\\]","g");
			var RGEFaddress3 = new RegExp("\\[\\[EFaddress3\\]\\]","g");
			var RGEFcity = new RegExp("\\[\\[EFcity\\]\\]","g");
			var RGEFstate = new RegExp("\\[\\[EFstate\\]\\]","g");
			var RGEFpostal_code = new RegExp("\\[\\[EFpostal_code\\]\\]","g");
			var RGEFprovince = new RegExp("\\[\\[EFprovince\\]\\]","g");
			var RGEFcountry_code = new RegExp("\\[\\[EFcountry_code\\]\\]","g");
			var RGEFemail = new RegExp("\\[\\[EFemail\\]\\]","g");
			var RGEFphone_code = new RegExp("\\[\\[EFphone_code\\]\\]","g");
			var RGEFphone_number = new RegExp("\\[\\[EFphone_number\\]\\]","g");
			var RGEFalt_phone = new RegExp("\\[\\[EFalt_phone\\]\\]","g");
			var RGEFcomments = new RegExp("\\[\\[EFcomments\\]\\]","g");
			var RGEFgender = new RegExp("\\[\\[EFgender\\]\\]","g");
			var RGEFdate_of_birth = new RegExp("\\[\\[EFdate_of_birth\\]\\]","g");
			var RGEFpost_date = new RegExp("\\[\\[EFpost_date\\]\\]","g");
			var RGEFvendor_lead_code = new RegExp("\\[\\[EFvendor_lead_code\\]\\]","g");
			var RGEFcustom1 = new RegExp("\\[\\[EFcustom1\\]\\]","g");
			var RGEFcustom2 = new RegExp("\\[\\[EFcustom2\\]\\]","g");

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

			// Editable Fields
			encoded = encoded.replace(RGEFtitle,            SCEFtitle);
			encoded = encoded.replace(RGEFfirst_name,       SCEFfirst_name);
			encoded = encoded.replace(RGEFmiddle_initial,   SCEFmiddle_initial);
			encoded = encoded.replace(RGEFlast_name,        SCEFlast_name);
			encoded = encoded.replace(RGEFaddress1,         SCEFaddress1);
			encoded = encoded.replace(RGEFaddress2,         SCEFaddress2);
			encoded = encoded.replace(RGEFaddress3,         SCEFaddress3);
			encoded = encoded.replace(RGEFcity,             SCEFcity);
			encoded = encoded.replace(RGEFstate,            SCEFstate);
			encoded = encoded.replace(RGEFprovince,         SCEFprovince);
			encoded = encoded.replace(RGEFpostal_code,      SCEFpostal_code);
			encoded = encoded.replace(RGEFcountry_code,     SCEFcountry_code);
			encoded = encoded.replace(RGEFphone_code,       SCEFphone_code);
			encoded = encoded.replace(RGEFphone_number,     SCEFphone_number);
			encoded = encoded.replace(RGEFalt_phone,        SCEFalt_phone);
			encoded = encoded.replace(RGEFemail,            SCEFemail);
			encoded = encoded.replace(RGEFcomments,         SCEFcomments);
			encoded = encoded.replace(RGEFdate_of_birth,    SCEFdate_of_birth);
			encoded = encoded.replace(RGEFgender,           SCEFgender);
			encoded = encoded.replace(RGEFpost_date,        SCEFpost_date);
			encoded = encoded.replace(RGEFvendor_lead_code, SCEFvendor_lead_code);
			encoded = encoded.replace(RGEFcustom1,          SCEFcustom1);
			encoded = encoded.replace(RGEFcustom2,          SCEFcustom2);


			for (var i=0; i<AFids.length; i++) {
				var SCt='';
				if (AFoptions[i] && AFoptions[i] != '') {
					SCt += "<select name=" + AFnames[i] + ' id=' + AFnames[i];
					SCt += " onfocus=\"this.value=document.getElementById('" + AFids[i] + "').value;\"";
					SCt += " onchange=\"var afv=this; document.getElementById('" + AFids[i] + "').value=afv.value;";
					SCt += " var aflist=document.getElementsByName('" + AFnames[i] + "');";
					//SCt += " for(var afli=0;afli<aflist.length;afli%2B%2B)";
					SCt += " for(var afli=0;afli<aflist.length;afli++)";
					SCt += " {if(afv.value!=aflist[afli].value) aflist[afli].value=afv.value;};\">";
					for (var option in AFoptions[i].split(',')) {
						SCt += "<option>" + option + "</option>";
					}
					SCt += "</select>";
				} else {
					SCt += "<input type=text size="+AFlengths[i]+" maxlength=255 name="+AFnames[i]+' id='+AFnames[i];
					SCt += " onfocus=\"this.value=document.getElementById('" + AFids[i] + "').value;\"";
					SCt += " onchange=\"var afv=this; document.getElementById('" + AFids[i] + "').value=afv.value;";
					SCt += " var aflist=document.getElementsByName('" + AFnames[i] + "');";
					SCt += " for(var afli=0;afli<aflist.length;afli++)";
					SCt += " {if(afv.value!=aflist[afli].value) aflist[afli].value=afv.value;};\"";
					SCt += ' class=cust_form value=\"\">';
				}

				if (encoded.match(RGiframe)) {
                    			SCt = SCt.replace(RGplus,'+');
				}

				encoded = encoded.replace(new RegExp("--A--" + AFnames[i] + "--B--","g"), SCt);
				encoded = encoded.replace(new RegExp("\\[\\[" + AFnames[i] + "\\]\\]","g"), SCt);
			}

		}
		decoded=encoded;
	}




// ###################################################################################################################################################
// AddtlFormOver() - On mouseover, hide Form tab and show form buttons.
	function AddtlFormOver() {
		debug("<b>AddtlFormOver:</b>",2);
		document.getElementById('AddtlFormTab').style.visibility='hidden';
		document.getElementById('AddtlFormTabExpanded').style.visibility='visible';
	}



// ###################################################################################################################################################
// AddtlFormButOver(AFform) - Change to the selected image of AFform on mouseover.
	function AddtlFormButOver(AFform) {
		debug("<b>AddtlFormButOver:</b> AFform=" + AFform,2);
		document.getElementById('AddtlFormBut' + AFform).style.background='url(templates/' + agent_template + '/images/agentsidetab_select.png)'; 
	}



// ###################################################################################################################################################
// AddtlFormButOut(AFform) - Change to the deselected image of AFform on mouseout.
	function AddtlFormButOut(AFform) {
		debug("<b>AddtlFormButOut:</b> AFform=" + AFform,2);
		document.getElementById('AddtlFormBut' + AFform).style.background='url(templates/' + agent_template + '/images/agentsidetab_extra.png)'; 
	}



// ###################################################################################################################################################
// AddtlFormSelect(AFform) - Hide all Additional Forms and display the selected AFform.
	function AddtlFormSelect(AFform) {
		debug("<b>AddtlFormSelect:</b> AFform=" + AFform,2);
		if (AFform != 'Cancel') {
			document.getElementById('AddtlFormBut' + AFform).style.background='url(templates/' + agent_template + '/images/agentsidetab_press.png)'; 
			if (document.getElementById('AddtlFormsEmailTemplates')) document.getElementById('AddtlFormsEmailTemplates').style.visibility='hidden';
			for (var i=0; i<AFforms.length; i++) {
				document.getElementById('AddtlForms' + AFforms[i]).style.visibility='hidden';
			}
			document.getElementById('AddtlForms' + AFform).style.visibility='visible'; 
			document.getElementById('AddtlFormBut' + AFform).style.background='url(templates/' + agent_template + '/images/agentsidetab_extra.png)'; 
		}
		document.getElementById('AddtlFormTabExpanded').style.visibility='hidden';
		document.getElementById('AddtlFormTab').style.visibility='visible';
	}



// ###################################################################################################################################################
// scriptEFCreateJS(elename) - Creates javascript for Editable Field elements that retrieves the Form Field data on focus and updates it on change.
	function scriptEFCreateJS(elename) {
		var eled;
                eled = " onfocus=\"this.value=document.getElementById('" + elename + "').value;\"";
                eled += " onchange=\"document.getElementById('" + elename + "').value=this.value; scriptEFUpdateData('" + elename + "',this);\" ";
		return eled;
	}



// ###################################################################################################################################################
// scriptEFUpdateData(elename,eleobj) - If eleobj is an Editable Field and is not identical to other elename Editable Fields,
//                                      update those fields with the contents of this Editable Field.
//                                      If eleobj is null (the elename Form Field), update all the elename Editable Fields
//                                      with its contents, and add the appropriate javascript to the Form Field.
	function scriptEFUpdateData(elename,eleobj) {
		try {
                	var efv;
			if (eleobj) {
				efv=eleobj;
			} else {
				efv=document.getElementById(elename);
				efv.setAttribute("onchange","scriptEFUpdateData('" + elename + "',this);");
			}
                	var eflist=document.getElementsByName('EF' + elename);
			for (var efli=0; efli<eflist.length; efli++) {
				if(efv.value!=eflist[efli].value)
					eflist[efli].value=efv.value;
			}
		} catch(error) {
			var a=1;
		}
	}



// ###################################################################################################################################################
// scriptUpdateFields() - Assign the Form Field contents to all the Additional Fields and Editable Fields while add appropriate
//                        javascript to update the Form Field if one of the Editable Fields is updated.
	function scriptUpdateFields() {
		debug("<b>scriptUpdateFields:</b>",2);

		for (var i=0; i<AFids.length; i++) {
			try {
				var afv=document.getElementById(AFids[i]);
				var aflist=document.getElementsByName(AFnames[i]);
				for(var afli=0;afli<aflist.length;afli++){
					if(afv.value!=aflist[afli].value) aflist[afli].value=afv.value;
				}
			}
			catch(error) {
				var a=1;
			}
		}

		// Editable Fields
		scriptEFUpdateData('title',null);
		scriptEFUpdateData('first_name',null);
		scriptEFUpdateData('middle_initial',null);
		scriptEFUpdateData('last_name',null);
		scriptEFUpdateData('address1',null);
		scriptEFUpdateData('address2',null);
		scriptEFUpdateData('address3',null);
		scriptEFUpdateData('city',null);
		scriptEFUpdateData('state',null);
		scriptEFUpdateData('province',null);
		scriptEFUpdateData('postal_code',null);
		scriptEFUpdateData('country_code',null);
		scriptEFUpdateData('phone_code',null);
		scriptEFUpdateData('phone_number',null);
		scriptEFUpdateData('alt_phone',null);
		scriptEFUpdateData('email',null);
		scriptEFUpdateData('comments',null);
		scriptEFUpdateData('date_of_birth',null);
		scriptEFUpdateData('gender',null);
		scriptEFUpdateData('post_date',null);
		scriptEFUpdateData('vendor_lead_code',null);
		scriptEFUpdateData('custom1',null);
		scriptEFUpdateData('custom2',null);
	}



// ###################################################################################################################################################
// emailTemplatesDisable(etact) - If ecact is true, prevent the Email Templates from being checked.
	function emailTemplatesDisable(etact) {
		if (document.getElementsByName('ETids')) {
			var et_ids = document.getElementsByName('ETids');
			var disableET=false;
			if (etact==true) disableET=true;
			for (var i=0; i<et_ids.length; i++) {
				et_ids[i].onclick = function (){var et=1;};
				var allcheck=false;
				if (email_template_actions[i] == 'ALL') {
					allcheck=true;
				} else if (email_template_actions[i] == 'ALLFORCE') {
					allcheck=true;
					disableET=true;
				}
				if (disableET) {
					if (allcheck) {
						et_ids[i].onclick = function (){this.checked=true;};
					} else {
						et_ids[i].onclick = function (){this.checked=false;};
					}
				}
				et_ids[i].checked=allcheck;
			}
		}
	}



// ###################################################################################################################################################
// emailTemplatesSend() - Cycle through each checked templated and call sendEmail for its template.
	function emailTemplatesSend() {
		if (document.getElementsByName('ETids')) {
			var et_ids = document.getElementsByName('ETids');
			for (var i=0; i<et_ids.length; i++) {
				if (et_ids[i].checked) {
					sendEmail(et_ids[i].value);
				}
			}
		}
	}



// ###################################################################################################################################################
// sendEmail(et_id) - Initiate the sending of an email to the given email template.
	function sendEmail(et_id) {
	    if (document.osdial_form.lead_id.value.length>0) {
	      if (document.osdial_form.email.value.length>0) {
		osdalert('Sending ' + et_id + ' Email...',60);
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			sbl_data = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Email&format=text&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&lead_id=" + document.osdial_form.lead_id.value + "&et_id=" + et_id;
			xmlhttp.open('POST', 'vdc_db_query.php', false); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(sbl_data); 
			//xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					osdalert('Email Sent',1);
					//osdalert(xmlhttp.responseText,30);
				}
			//}
			delete xmlhttp;
		}
	      //} else {
		//osdalert('Email not sent, not a valid email address!',3);
              }
	    } else {
		osdalert('No lead!',3);
	    }
	}



// ###################################################################################################################################################
// checkEmailBlacklist() - Check if email address is in the blacklist.
	function checkEmailBlacklist() {
		if (document.osdial_form.email.value.length>0) {
			var xmlhttp=getXHR();
			if (xmlhttp) { 
				sbl_data = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=EmailCheckBlacklist&format=text&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&email=" + document.osdial_form.email.value;
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(sbl_data); 
				xmlhttp.onreadystatechange = function() { 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						//osdalert(xmlhttp.responseText,30);
						var EBcheck = xmlhttp.responseText.split("\n");
						debug("<b>checkEmailBlacklist return:</b> " + EBcheck[0] + "|",3);
						if (EBcheck[0] == 'BLACKLISTED') {
							osdalert('Email Address on Blacklist',5);
							document.osdial_form.email.style.backgroundColor=system_alert_bg2;
						} else {
							document.osdial_form.email.style.backgroundColor='#FFFFFF';
						}
					}
				}
				delete xmlhttp;
			}
		}
	}



// ###################################################################################################################################################
// afterCallClearing() - Clears all form fields, scripts, script fields and  additional form fields after each call.
	function afterCallClearing() {
		VDCL_group_id='';
		fronter='';

		document.osdial_form.lead_id.value='';
		document.osdial_form.vendor_lead_code.value='';
		document.osdial_form.list_id.value='';
		document.osdial_form.gmt_offset_now.value='';
		document.osdial_form.phone_code.value='';
		document.osdial_form.phone_number.value='';
		document.osdial_form.title.value='';
		document.osdial_form.first_name.value='';
		document.osdial_form.middle_initial.value='';
		document.osdial_form.last_name.value='';
		document.osdial_form.address1.value='';
		document.osdial_form.address2.value='';
		document.osdial_form.address3.value='';
		document.osdial_form.city.value='';
		document.osdial_form.state.value='';
		document.osdial_form.province.value='';
		document.osdial_form.postal_code.value='';
		document.osdial_form.country_code.value='';
		document.osdial_form.gender.value='';
		document.osdial_form.date_of_birth.value='';
		document.osdial_form.alt_phone.value='';
		document.osdial_form.email.value='';
		document.osdial_form.custom1.value='';
		document.osdial_form.custom2.value='';
		document.osdial_form.comments.value='';
		document.osdial_form.called_count.value='';
		document.osdial_form.post_date.value='';
		document.osdial_form.source_id.value='';
		document.osdial_form.external_key.value='';

		for (var i=0; i<AFids.length; i++) {
			//alert(document.getElementById(AFids[i]).type);
			if (document.getElementById(AFids[i]).type=='select-one') {
				document.getElementById(AFids[i]).selectedIndex=0;
			} else {
				document.getElementById(AFids[i]).value='';
			}
		}

		if ( (view_scripts == 1) && (campaign_script.length > 0) ) {
			document.getElementById("ScriptContents").innerHTML = t1 + " Script Will Show Here";
			scriptUpdateFields();
		}

		emailTemplatesDisable(true);
	}


// ###################################################################################################################################################
// formatPhone() - Format the given number for locale (phone_code).
	function formatPhone(phcode, phnum) {
		var resnum=phnum;

		// Strip off intl prefix
		if (phcode.substring(0,4)=='0011' && phcode.length>4) phcode=phcode.substring(4);
		if (phcode.substring(0,3)=='001' && phcode.length>3) phcode=phcode.substring(3);
		if (phcode.substring(0,3)=='010' && phcode.length>3) phcode=phcode.substring(3);
		if (phcode.substring(0,3)=='011' && phcode.length>3) phcode=phcode.substring(3);
		if (phcode.substring(0,2)=='00') phcode=phcode.substring(2);

		//Assume NorthAmerica if phcode is blank and phnum is 10 digits.
		if (phcode=='' && phnum.length==10) phcode='1';

		// North America
		if (phcode=='1') {
			// (xxx)xxx-xxxx
			if (phnum.length==10) resnum = '('+phnum.substring(0,3)+')'+phnum.substring(3,6)+'-'+phnum.substring(6,10);

		// UK
		} else if (phcode=='44') {
			// +(44)(xxx) xxxx xxx
			if (phnum.length==10) resnum = '+('+phcode+')('+phnum.substring(0,3)+') '+phnum.substring(3,7)+' '+phnum.substring(7,10);

		// Hong Kong
		} else if (phcode=='852') {
			// +(852)(xxx) xxxx xxxx
			resnum = '+('+phcode+')('+phnum.substring(0,3)+') '+phnum.substring(3,7)+' '+phnum.substring(7);
	
		// Macau
		} else if (phcode=='853') {
			// +(853) xxxxxxxx
			resnum = '+('+phcode+') '+phnum;

		// China
		} else if (phcode=='86') {
			// +(86) xxx-xxxx-xxxx
			resnum = '+('+phcode+') '+phnum.substring(0,3)+'-'+phnum.substring(3,7)+'-'+phnum.substring(7);

		// Australia
		} else if (phcode=='61') {
			// Geographic (xx) xxxx xxxx
			resnum = '('+phnum.substring(0,2)+') '+phnum.substring(2,6)+' '+phnum.substring(6);
			// Mobile x4xx xxx xxx
			if (phnum.substring(1,1)=='4') resnum = phnum.substring(0,4)+' '+phnum.substring(4,7)+' '+phnum.substring(7);

		// New Zealand
		} else if (phcode=='64') {
			// (xx) xxx-xxxx
			resnum = '('+phnum.substring(0,2)+') '+phnum.substring(2,5)+'-'+phnum.substring(5);

		}

		return resnum;
	}



// ###################################################################################################################################################
// multicall_answer() - Answer the new incoming multicall.
	function multicall_answer() {
		voicemail_ariclose();
		if (alt_dial_active==0 && multicall_waitchannel!='' && multicall_channel=='') multicall_queue_swap();
	}



// ###################################################################################################################################################
// multicall_send2voicemail() - Send the new incoming multicall to voicemail.
	function multicall_send2voicemail() {
		if (multicall_waitchannel!='') {
			var xmlhttp=getXHR();
			if (xmlhttp) { 
				xferredirect_query = "server_ip=" + multicall_waitserverip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + multicall_waitchannel + "&queryCID=" + multicall_waitcallerid + "&exten=85026666666666" + multicall_waitvoicemail + "&ext_context=osdial&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + multicall_waitingroup + "&lead_id=" + multicall_waitleadid + "&uniqueid=" + multicall_waituniqueid;
				xmlhttp.open('POST', 'manager_send.php', false); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				debug('<b>multicall_send2voicemail:</b> ' + xferredirect_query,3);
				xmlhttp.send(xferredirect_query); 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					multicall_waitchannel = multicall_waitserverip = multicall_waitcallerid = multicall_waitingroup = "";
					multicall_waitvoicemail = multicall_waituniqueid = multicall_waitleadid = "";
					multicall_vmdrop_timer = -1;
				}
				delete xmlhttp;
			}
		}
		document.getElementById('MultiCallAlerTBoX').style.visibility='hidden';
		multicall_alert=0;
	}



// ###################################################################################################################################################
// check_multicall_incoming() - If a multicall is waiting/queued, read in the state variables and display the alert window.  If there is an active
//                            - non-multicall (outbound or inbound), copy the channel/lead information into the multicall stucture.
	function check_multicall_incoming() {
		if (call_queue_in_mc > 0 && multicall_alert==0 && multicall_channel=='' && multicall_waitchannel=='') {
			var xmlhttp=getXHR();
			if (xmlhttp) { 
				var mcgc_data = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=MulticallGetChannel&format=text&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&lead_id=" + document.osdial_form.lead_id.value;
				xmlhttp.open('POST', 'vdc_db_query.php', false); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(mcgc_data); 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					debug('<b>check_multicall_incoming:</b> mcgc_data: ' + mcgc_data,3);
					var MCsplit = xmlhttp.responseText.split("\n");
					if (MCsplit[0]!='') {
						multicall_waitchannel = MCsplit[0];
						multicall_waitserverip = MCsplit[1];
						multicall_waitcallerid = MCsplit[2];
						multicall_waitingroup = MCsplit[3];
						multicall_waitvoicemail = MCsplit[4];
						multicall_waituniqueid = MCsplit[5];
						multicall_waitleadid = MCsplit[6];
						if (VD_live_customer_call==1 && multicall_lastchannel=='') {
							multicall_lastchannel = lastcustchannel;
							multicall_lastserverip = lastcustserverip;
							multicall_lastcallerid = "LPvdcW" + epoch_sec + user_abb;
							multicall_lastuniqueid = document.osdial_form.uniqueid.value;
							multicall_lastleadid = document.osdial_form.lead_id.value;
							multicall_lastingroup = campaign;
							multicall_lastvoicemail = '8307';
						}
						var catab = '<table>';
						document.getElementById("MultiCallAlerTInfo").innerHTML = '<table>';
						if (MCsplit[8] != '') catab += "<tr><td><font size=2>CallerID Number: </font></td><td><font size=2>" + formatPhone(MCsplit[7],MCsplit[8]) + "</font></td></tr>";
						if (MCsplit[9] != '') catab += "<tr><td><font size=2>CallerID Name: </font></td><td><font size=2>" + MCsplit[9] + "</font></td></tr>";
						if (MCsplit[6] != '') catab += "<tr><td><font size=2>Lead ID: </font></td><td><font size=2>" + MCsplit[6] + "</font></td></tr>";
						if (MCsplit[10] != '') catab += "<tr><td><font size=2>First Name: </font></td><td><font size=2>" + MCsplit[10] + "</font></td></tr>";
						if (MCsplit[11] != '') catab += "<tr><td><font size=2>Last Name: </font></td><td><font size=2>" + MCsplit[11] + "</font></td></tr>";
						if (MCsplit[12] != '') catab += "<tr><td><font size=2>Address: </font></td><td><font size=2>" + MCsplit[12] + "</font></td></tr>";
						if (MCsplit[13] != '') catab += "<tr><td><font size=2>City: </font></td><td><font size=2>" + MCsplit[13] + "</font></td></tr>";
						if (MCsplit[14] != '') catab += "<tr><td><font size=2>State: </font></td><td><font size=2>" + MCsplit[14] + "</font></td></tr>";
						if (MCsplit[15] != '') catab += "<tr><td><font size=2>Postal Code: </font></td><td><font size=2>" + MCsplit[15] + "</font></td></tr>";
						catab += '</table>';
						document.getElementById("MultiCallAlerTInfo").innerHTML = catab;
						multicall_alert++;
						document.getElementById('MultiCallAlerTBoX').style.visibility='visible';
						multicall_vmdrop_timer = MCsplit[16];
						document.getElementById("MulticallAlerTTimer").innerHTML = multicall_vmdrop_timer;

						// Whisper a ding to the agent.
						var dingCID = "MCalrt" + epoch_sec + user_abb;
						var dingoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=Local/9" + session_id + "@" + ext_context + "&queryCID=" + dingCID + "&exten=8304&ext_context=" + ext_context + "&ext_priority=1";
						xmlhttp.open('POST', 'manager_send.php', false); 
						xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
						xmlhttp.send(dingoriginate_query); 
					} else {
						multicall_alert=0;
						multicall_vmdrop_timer = -1;
						document.getElementById('MultiCallAlerTBoX').style.visibility='hidden';
					}
					debug('<b>check_multicall_incoming:</b> multicall_waitchannel:' + multicall_waitchannel + " multicall_waitserverip: " + multicall_waitserverip + " multicall_waitvoicemail: " + multicall_waitvoicemail,3);
				}
				delete xmlhttp;
			}
		}
	}



// ###################################################################################################################################################
// multicall_queue_swap() - Check if a call is waiting in an inbound group for this agent.  If the ingroup allows multicall handling, 
//                        - park call A, answer call B, and load lead data for call B.  If there is already two active mutlicalls,
//                        - park call A, unpark call B, load lead data for call B.
	function multicall_queue_swap() {
		debug("<b>multicall_queue_swap:</b>",3);
		all_record = 'NO';
		all_record_count=0;
		// If we have a multicall queued/waiting, juggle the states so that we don't loose anything.
		if (multicall_waitchannel!='') {
			if (multicall_channel=='' && multicall_lastchannel=='') {
				multicall_channel = multicall_waitchannel;
				multicall_serverip = multicall_waitserverip;
				multicall_callerid = multicall_waitcallerid;
				multicall_uniqueid = multicall_waituniqueid;
				multicall_leadid = multicall_waitleadid;
				multicall_ingroup = multicall_waitingroup;
				multicall_voicemail = multicall_waitvoicemail;
			} else if (multicall_channel=='' && multicall_lastchannel!='') {
				multicall_channel = multicall_waitchannel;
				multicall_serverip = multicall_waitserverip;
				multicall_callerid = multicall_waitcallerid;
				multicall_uniqueid = multicall_waituniqueid;
				multicall_leadid = multicall_waitleadid;
				multicall_ingroup = multicall_waitingroup;
				multicall_voicemail = multicall_waitvoicemail;
			} else if (multicall_channel!='' && multicall_lastchannel=='') {
				multicall_lastchannel = multicall_channel;
				multicall_lastserverip = multicall_serverip;
				multicall_lastcallerid = multicall_callerid;
				multicall_lastuniqueid = multicall_uniqueid;
				multicall_lastleadid = multicall_leadid;
				multicall_lastingroup = multicall_ingroup;
				multicall_lastvoicemail = multicall_voicemail;

				multicall_channel = multicall_waitchannel;
				multicall_serverip = multicall_waitserverip;
				multicall_callerid = multicall_waitcallerid;
				multicall_uniqueid = multicall_waituniqueid;
				multicall_leadid = multicall_waitleadid;
				multicall_ingroup = multicall_waitingroup;
				multicall_voicemail = multicall_waitvoicemail;
			}
			// Clear the queued/waiting state variables.
			multicall_waitchannel = multicall_waitserverip = multicall_waitcallerid = multicall_waituniqueid = "";
			multicall_waitleadid = multicall_waitingroup = multicall_waitvoicemail = "";
			multicall_vmdrop_timer = -1;
		}
		var xmlhttpqs=getXHR();
		if (xmlhttpqs) { 
			if (lastcustserverip == '') lastcustserverip=server_ip;
			checkMCIN_query = "server_ip=" + lastcustserverip + "&channel=" + lastcustchannel + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=multicallQueueSwap" + "&agent_log_id=" + agent_log_id + "&agentchannel=" + agentchannel + "&agentserver_ip=" + server_ip + "&multicall_channel=" + multicall_channel + "&multicall_serverip=" + multicall_serverip + "&multicall_leadid=" + multicall_leadid + "&multicall_callerid=" + multicall_callerid + "&multicall_uniqueid=" + multicall_uniqueid + "&multicall_liveseconds=" + multicall_liveseconds + "&conf_exten=" + session_id + "&user_abb=" + user_abb + "&park_on_extension=" + park_on_extension;
			xmlhttpqs.open('POST', 'vdc_db_query.php', false); 
			xmlhttpqs.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttpqs.send(checkMCIN_query); 
			debug('multicall_queue_swap: checkMCIN_query: ' + checkMCIN_query,3);
			if (xmlhttpqs.readyState == 4 && xmlhttpqs.status == 200) {
				var queue_swap = xmlhttpqs.responseText;
				//osdalert(queue_swap,30);
				//osdalert(xmlhttprequestqs.responseText,30);
				var check_MCIC_array=queue_swap.split("\n");
				debug('multicall_queue_swap: check_MCIC_array[0]: ' + check_MCIC_array[0],4);
				// Array segment 0 value is 1 if the channel is still live, 2 if the remote party has hungup.
				// This allows us to reload the lead data for the dead call.
				if (check_MCIC_array[0] == '1' || check_MCIC_array[0] == '2') {
					// Swap the two multicall states.  Copy current live channel (call B) into tmp.
					multicall_tmpchannel = multicall_channel;
					multicall_tmpserverip = multicall_serverip;
					multicall_tmpcallerid = multicall_callerid;
					multicall_tmpuniqueid = multicall_uniqueid;
					multicall_tmpleadid = multicall_leadid;
					multicall_tmpingroup = multicall_ingroup;
					multicall_tmpvoicemail = multicall_voicemail;
					// Copy old channel (call A) into the parked/held channel.
					multicall_channel = multicall_lastchannel;
					multicall_serverip = multicall_lastserverip;
					multicall_callerid = multicall_lastcallerid;
					multicall_uniqueid = multicall_lastuniqueid;
					multicall_leadid = multicall_lastleadid;
					multicall_ingroup = multicall_lastingroup;
					multicall_voicemail = multicall_lastvoicemail;
					// Copy tmp into the current live channel (call B).
					multicall_lastchannel = multicall_tmpchannel;
					multicall_lastserverip = multicall_tmpserverip;
					multicall_lastcallerid = multicall_tmpcallerid;
					multicall_lastuniqueid = multicall_tmpuniqueid;
					multicall_lastleadid = multicall_tmpleadid;
					multicall_lastingroup = multicall_tmpingroup;
					multicall_lastvoicemail = multicall_tmpvoicemail;

					document.images['livecall'].src = image_livecall_ON.src;
					VD_live_customer_call=1;

					document.getElementById('MultiCallAlerTBoX').style.visibility='hidden';
					multicall_liveseconds = multicall_alert = 0;
					multicall_active = 1;

					debug('multicall_queue_swap:' + " multicall_alert:"+multicall_alert+" multicall_active:"+multicall_active+" multicall_channel:"+multicall_channel+" multicall_lastchannel:"+multicall_lastchannel+" multicall_waitchannel:"+multicall_waitchannel,3);

					CustomerData_update();
					afterCallClearing();

					// Parse in lead data and setup interface.
					debug('multicall_queue_swap: check_MCIC_array[1]: ' + check_MCIC_array[1],4);
					var VDIC_data_MCAC=check_MCIC_array[1].split("|");
					var VDIC_fronter='';

					debug('multicall_queue_swap: check_MCIC_array[2]: ' + check_MCIC_array[2],4);
					var VDIC_data_MCIG=check_MCIC_array[2].split("|");
					if (VDIC_data_MCIG[0].length > 5) VDIC_web_form_address = VDIC_data_MCIG[0];
					var VDCL_group_name		= VDIC_data_MCIG[1];
					var VDCL_group_color		= VDIC_data_MCIG[2];
					var VDCL_fronter_display	= VDIC_data_MCIG[3];
					VDCL_group_id			= VDIC_data_MCIG[4];
					CalL_ScripT_id			= VDIC_data_MCIG[5];
					CalL_AutO_LauncH		= VDIC_data_MCIG[6];
					CalL_XC_a_Dtmf			= VDIC_data_MCIG[7];
					CalL_XC_a_NuMber		= VDIC_data_MCIG[8];
					CalL_XC_b_Dtmf			= VDIC_data_MCIG[9];
					CalL_XC_b_NuMber		= VDIC_data_MCIG[10];
					LIVE_default_xfer_group = default_xfer_group;
					if (VDIC_data_MCIG[11].length > 0) LIVE_default_xfer_group = VDIC_data_MCIG[11];
					CalL_allow_tab		 	= VDIC_data_MCIG[12];
					if (VDIC_data_MCIG[13].length > 5) VDIC_web_form_address2 = VDIC_data_MCIG[13];
					if (VDIC_web_form_address == '') VDIC_web_form_address = OSDiaL_web_form_address;
					if (VDIC_web_form_address2 == '') VDIC_web_form_address2 = OSDiaL_web_form_address2;
					web_form_extwindow = 0;
					if (VDIC_data_MCIG[14] == "Y") web_form_extwindow = 1;
					web_form2_extwindow = 0;
					if (VDIC_data_MCIG[15] == "Y") web_form2_extwindow = 1;
					wf_enc_address = webform_rewrite(VDIC_web_form_address);
					if (wf_enc_address == VDIC_web_form_address) wf_enc_address += web_form_vars;
					wf2_enc_address = webform_rewrite(VDIC_web_form_address2);
					if (wf2_enc_address == VDIC_web_form_address2) wf2_enc_address += web_form_vars2;

					var VDIC_data_MCFR=check_MCIC_array[3].split("|");
					if (VDIC_data_MCFR[1].length > 1 && VDCL_fronter_display == 'Y') VDIC_fronter = "  Fronter: " + VDIC_data_MCFR[0] + " - " + VDIC_data_MCFR[1];

					document.osdial_form.lead_id.value						= VDIC_data_MCAC[0];
					document.osdial_form.uniqueid.value						= VDIC_data_MCAC[1];
					CIDcheck 						= CalLCID 		= VDIC_data_MCAC[2];
					document.getElementById("callchannel").innerHTML	= lastcustchannel 	= VDIC_data_MCAC[3];
					document.osdial_form.callserverip.value 		= lastcustserverip 	= VDIC_data_MCAC[4];
					document.osdial_form.SecondS.value 			= VD_live_call_secondS 	= (VDIC_data_MCAC[5]*1).toFixed(0);


					LasTCID						= check_MCIC_array[4];
					LeaDPreVDispO					= check_MCIC_array[6];
					fronter						= check_MCIC_array[7];
					document.osdial_form.vendor_lead_code.value	= check_MCIC_array[8];
					document.osdial_form.list_id.value		= check_MCIC_array[9];
					document.osdial_form.gmt_offset_now.value	= check_MCIC_array[10];
					document.osdial_form.phone_code.value		= check_MCIC_array[11];
					document.osdial_form.phone_number.value		= check_MCIC_array[12];
					document.osdial_form.title.value		= check_MCIC_array[13];
					document.osdial_form.first_name.value		= check_MCIC_array[14];
					document.osdial_form.middle_initial.value	= check_MCIC_array[15];
					document.osdial_form.last_name.value		= check_MCIC_array[16];
					document.osdial_form.address1.value		= check_MCIC_array[17];
					document.osdial_form.address2.value		= check_MCIC_array[18];
					document.osdial_form.address3.value		= check_MCIC_array[19];
					document.osdial_form.city.value			= check_MCIC_array[20];
					document.osdial_form.state.value		= check_MCIC_array[21];
					document.osdial_form.province.value		= check_MCIC_array[22];
					document.osdial_form.postal_code.value		= check_MCIC_array[23];
					document.osdial_form.country_code.value		= check_MCIC_array[24];
					document.osdial_form.gender.value		= check_MCIC_array[25];
					document.osdial_form.date_of_birth.value	= check_MCIC_array[26];
					document.osdial_form.alt_phone.value		= check_MCIC_array[27];
					document.osdial_form.email.value		= check_MCIC_array[28];
					document.osdial_form.custom1.value		= check_MCIC_array[29];
					var REGcommentsNL = new RegExp("!N","g");
					check_MCIC_array[30] = check_MCIC_array[30].replace(REGcommentsNL, "\n");
					document.osdial_form.comments.value		= check_MCIC_array[30];
					document.osdial_form.called_count.value		= check_MCIC_array[31];
					CBentry_time					= check_MCIC_array[32];
					CBcallback_time					= check_MCIC_array[33];
					CBuser						= check_MCIC_array[34];
					CBcomments					= check_MCIC_array[35];
					dialed_number					= check_MCIC_array[36];
					dialed_label					= check_MCIC_array[37];
					document.osdial_form.source_id.value		= check_MCIC_array[38];
					document.osdial_form.custom2.value		= check_MCIC_array[39];
					document.osdial_form.external_key.value		= check_MCIC_array[40];
					document.osdial_form.post_date.value		= check_MCIC_array[41];
					var pos = 42;
					for (var i=0; i<AFids.length; i++) {
						document.getElementById(AFids[i]).value = check_MCIC_array[pos];
						pos++;
					}

					emailTemplatesDisable();

					lead_dial_number = dialed_number;
					var status_display_number = formatPhone(document.osdial_form.phone_code.value,dialed_number);

					document.getElementById("MainStatuSSpan").style.backgroundColor = '';
					document.getElementById("MainStatuSSpan").innerHTML = " Calling: " + status_display_number + "&nbsp;&nbsp;<font color=" + status_bg + ">UID: " + CIDcheck + "</font> &nbsp; " + VDIC_fronter; 
					document.getElementById("RepullControl").innerHTML = "<a href=\"#\" onclick=\"RepullLeadData('all');\"><img src=\"templates/" + agent_template + "/images/vdc_RPLD_on.gif\" width=145 height=16 border=0 alt=\"Repull Lead Data\"></a>";

					if (LeaDPreVDispO == 'CALLBK') {
						document.getElementById("CusTInfOSpaN").innerHTML = "&nbsp;<B>PREVIOUS CALLBACK</B>";
						document.getElementById("CusTInfOSpaN").style.backgroundColor = CusTCB_bgcolor;
						document.getElementById("CBcommentsBoxA").innerHTML = "<b>Last Call: </b>" + CBentry_time;
						document.getElementById("CBcommentsBoxB").innerHTML = "<b>CallBack: </b>" + CBcallback_time;
						document.getElementById("CBcommentsBoxC").innerHTML = "<b>Agent: </b>" + CBuser;
						document.getElementById("CBcommentsBoxD").innerHTML = "<b>Comments: </b><br>" + CBcomments;
						showDiv('CBcommentsBox');
					}

					if (VDIC_data_MCIG[1].length > 0) {
						if (VDIC_data_MCIG[2].length > 2) document.getElementById("MainStatuSSpan").style.backgroundColor = VDIC_data_MCIG[2];
						var status_display_number = formatPhone(document.osdial_form.phone_code.value,document.osdial_form.phone_number.value);
						document.getElementById("MainStatuSSpan").innerHTML = " Incoming: " + status_display_number + " Group- " + VDIC_data_MCIG[1] + " &nbsp; " + VDIC_fronter; 
					}

					// If we only have one active multicall state, only display the Park Call button, otherwise, display the Swap Calls button.
					if (multicall_channel=='' || multicall_lastchannel=='') {
						document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_parkcall.gif\" width=145 height=16 border=0 alt=\"Park Call\"></a>";
					} else {
						document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"multicall_queue_swap();return false;\"><img src=\"templates/" + agent_template + "/images/vdc_LB_swapcalls1000.gif\" width=145 height=16 border=0 alt=\"Swap Calls\"></a>";
					}

					document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"templates/" + agent_template + "/images/vdc_LB_hangupcustomer.gif\" width=145 height=16 border=0 alt=\"Hangup Customer\"></a>";
					document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');\"><img src=\"templates/" + agent_template + "/images/vdc_LB_transferconf.gif\" width=145 height=16 border=0 alt=\"Transfer - Conference\"></a>";
					document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_localcloser.gif\" width=107 height=16 border=0 alt=\"LOCAL CLOSER\"></a>";
					document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_blindtransfer.gif\" width=137 height=16 border=0 alt=\"Dial Blind Transfer\"></a>";
					document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_ammessage.gif\" width=36 height=16 border=0 alt=\"Blind Transfer VMail Message\"></a>";
					document.getElementById("DialBlindVMail2").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_XB_ammessage.gif\" width=36 height=16 border=0 alt=\"Blind Transfer VMail Message\"></a>";
					document.getElementById("DTMFDialPad0").innerHTML = "<a href=\"#\" alt=\"0\" onclick=\"document.osdial_form.conf_dtmf.value='0'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_0.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad1").innerHTML = "<a href=\"#\" alt=\"1\" onclick=\"document.osdial_form.conf_dtmf.value='1'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_1.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad2").innerHTML = "<a href=\"#\" alt=\"2 - ABC\" onclick=\"document.osdial_form.conf_dtmf.value='2'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_2.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad3").innerHTML = "<a href=\"#\" alt=\"3 - DEF\" onclick=\"document.osdial_form.conf_dtmf.value='3'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_3.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad4").innerHTML = "<a href=\"#\" alt=\"4 - GHI\" onclick=\"document.osdial_form.conf_dtmf.value='4'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_4.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad5").innerHTML = "<a href=\"#\" alt=\"5 - JKL\" onclick=\"document.osdial_form.conf_dtmf.value='5'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_5.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad6").innerHTML = "<a href=\"#\" alt=\"6 - MNO\" onclick=\"document.osdial_form.conf_dtmf.value='6'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_6.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad7").innerHTML = "<a href=\"#\" alt=\"7 - PQRS\" onclick=\"document.osdial_form.conf_dtmf.value='7'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_7.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad8").innerHTML = "<a href=\"#\" alt=\"8 - TUV\" onclick=\"document.osdial_form.conf_dtmf.value='8'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_8.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPad9").innerHTML = "<a href=\"#\" alt=\"9 - WXYZ\" onclick=\"document.osdial_form.conf_dtmf.value='9'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_9.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPadStar").innerHTML = "<a href=\"#\" alt=\"*\" onclick=\"document.osdial_form.conf_dtmf.value='*'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_star.png\" width=26 height=19 border=0></a>";
					document.getElementById("DTMFDialPadHash").innerHTML = "<a href=\"#\" alt=\"#\" onclick=\"document.osdial_form.conf_dtmf.value='#'; SendConfDTMF('" + session_id + "');return false;\"><img src=\"templates/" + agent_template + "/images/dtmf_hash.png\" width=26 height=19 border=0></a>";

					if (lastcustserverip == server_ip) {
						document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + lastcustchannel + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_up.gif\" width=28 height=15 BORDER=0></a>";
						document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + lastcustchannel + "','');return false;\"><img src=\"templates/" + agent_template + "/images/vdc_volume_down.gif\" width=28 height=15 BORDER=0></a>";
					}

					document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
					if (inbound_man > 0) document.getElementById("DiaLControl").innerHTML = "<img src=\"templates/" + agent_template + "/images/vdc_LB_pause_OFF.gif\" width=70 height=18 border=0 alt=\" Pause \"><img src=\"templates/" + agent_template + "/images/vdc_LB_resume_OFF.gif\" width=70 height=18 border=0 alt=\"Resume\"><BR><img src=\"templates/" + agent_template + "/images/vdc_LB_dialnextnumber_OFF.gif\" width=145 height=16 border=0 alt=\"Dial Next Number\">";
					WebFormRefresH();
					if (campaign_recording == 'ALLCALLS' || campaign_recording == 'ALLFORCE') all_record = 'YES';
					if (view_scripts == 1 && CalL_ScripT_id.length > 0) {
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

				}
				xmlhttpqs = undefined;
				delete xmlhttpqs;
			}
		}
	}



	function apixml(xmlfunc, xmlmode, xmlvdcompat, xmldebug, xmltest) {
		if (typeof(xmlfunc)=='undefined') xmlfunc='version';
		if (typeof(xmlmode)=='undefined') xmlmode='agent';
		if (typeof(xmlvdcompat)=='undefined') xmlvdcompat='0';
		if (typeof(xmldebug)=='undefined') xmldebug='0';
		if (typeof(xmltest)=='undefined') xmltest='0';
		var xmltext = '<api><params></params></api>';
		var parser = new DOMParser();
		var xmlDoc = parser.parseFromString(xmltext, "text/xml");

		var apinode = xmlDoc.getElementsByTagName('api')[0];
		apinode.setAttribute('user',user);
		apinode.setAttribute('pass',pass);
		apinode.setAttribute('function',xmlfunc);
		apinode.setAttribute('mode',xmlmode);
		apinode.setAttribute('vdcompat',xmlvdcompat);
		apinode.setAttribute('debug',xmldebug);
		apinode.setAttribute('test',xmltest);

		return xmlDoc;
	}

	function apixmlparams(xmlDoc, xmlname, xmlvalue) {
		if (typeof(xmlvalue)=='undefined') xmlvalue='';
		var newnode = xmlDoc.createElement(xmlname);
		newnode.appendChild(xmlDoc.createTextNode(xmlvalue));
		var paramsnode = xmlDoc.getElementsByTagName('params')[0];
		paramsnode.appendChild(newnode);
	}

	function xmltostring(xmlDoc) {
		var xmlString = (new XMLSerializer()).serializeToString(xmlDoc);
		return xmlString;
	}

	function check_voicemail() {
		var xmlhttp=getXHR();
		if (xmlhttp) { 
			var xmlDoc = apixml('vmail_check');
			apixmlparams(xmlDoc, 'vmail_box', voicemail_id);
			apixmlparams(xmlDoc, 'server_ip', server_ip);
			api_query = 'xml=' + encodeURIComponent2(xmltostring(xmlDoc));
			xmlhttp.open('POST', '../admin/api.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			debug('<b>check_voicemail:</b> ' + api_query,3);
			xmlhttp.send(api_query); 
			xmlhttp.onreadystatechange = function() { 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					xmlRes = xmlhttp.responseXML;
					vmail_messages = xmlRes.getElementsByTagName("messages")[0].textContent;
					vmail_old_messages = xmlRes.getElementsByTagName("old_messages")[0].textContent;
                    if ((vmail_messages + vmail_old_messages) > 0) {
					    document.getElementById("voicemailbutton").innerHTML = "<a href=\"#\" onclick=\"voicemail_ariopen();\"><img src=\"templates/" + agent_template + "/images/agc_check_voicemail_ON.gif\" width=170 height=30 border=0 alt=\"VOICEMAIL\"></a>";
                    } else {
					    document.getElementById("voicemailbutton").innerHTML = "<a href=\"#\" onclick=\"voicemail_ariopen();\"><img src=\"templates/" + agent_template + "/images/agc_check_voicemail_OFF.gif\" width=170 height=30 border=0 alt=\"VOICEMAIL\"></a>";
                    }
				}
			}
			delete xmlhttp;
		}
	}

	function voicemail_ariopen() {
		if (VD_live_customer_call==0 && (vmail_messages + vmail_old_messages)>0) {
			document.getElementById('ARIPanel').style.visibility='visible';
			document.getElementById('ARIFrame').src = '/voicemail/' + server_ip + '/ari/index.php?username='+voicemail_id+'&password='+voicemail_password+'&sessionid='+session_id;
		}
	}

	function voicemail_ariclose() {
		document.getElementById('ARIPanel').style.visibility='hidden';
		document.getElementById('ARIFrame').src = '/agent/blank.php';
	}
