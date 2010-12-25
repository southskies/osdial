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



// ################################################################################
// Calculates the style.width for any text fields.
    function initTextWidths() {
		inps = document.osdial_form.getElementsByTagName('input');
		for (var i=0; i<inps.length; i++) {
			if (inps[i].getAttribute('type') == 'text') {
				if (inps[i].size>0) {
					if (!inps[i].style.width) {
					    var len = inps[i].size;
					    if (len>0) {
					        var px = parseInt(inps[i].style.fontSize.replace('px',''));
                            if (!px>0) px=12;
                            inps[i].style.width = ((len * px/2) + px*2.5 - 2);
                        }
                    }
				}
			}
		}
    }



// ################################################################################
// Submitting the callback date and time to the system
	function CallBackDatE_submit() {
		debug("<b>CallBackDatE_submit:</b>",2);
		CallBackDatEForM = document.osdial_form.CallBackDatESelectioN.value;
		CallBackCommenTs = document.osdial_form.CallBackCommenTsField.value;
		if (CallBackDatEForM.length < 2) {
			osdalert("You must choose a date",5);
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

			//osdalert(CallBackDatETimE + "|" + CallBackCommenTs,30);

			document.osdial_form.DispoSelection.value = 'CBHOLD';
			hideDiv('CallBackSelectBox');
			DispoSelect_submit();
		}
	}

<?
if ($useIE > 0) {
?>
// ################################################################################
// MSIE-only hotkeypress function to bind hotkeys defined in the campaign to dispositions
	function hotkeypress(evt) {
		debug("<b>hotkeypress:</b> evt=" + evt,5);
		enter_disable(evt);
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
			//osdalert("|" + key + "|" + HKdispo + "|",30);
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
					HKdispo_display=4;
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
				SendConfDTMF(session_id);
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
		enter_disable(evt);
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
				//osdalert(HKdispo + " - " + HKdispo_ary[0] + " - " + HKdispo_ary[1],30);
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
				SendConfDTMF(session_id);
			}
		}
	}

<?
}
### end of onkeypress functions
?>



