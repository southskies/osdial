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
*/

// Globals
var sess_timer = null;
var oac_timer = null;
var oac_last_script = '';
var oac_last_params = '';
var oac_last_delay = 0;
var ctrl_key = false;

var helppopon = 0;

function osdialOnLoad() {
	fixChromeTableCollapse();
	updateClock();
	setInterval('updateClock()', 1000);
	editableSelectOnload();
}

function osdialOnUnload() {
	stop();
}

function editableSelectOnload() {
	var divs = document.querySelectorAll('div.selectBox');
	for (var d = 0; d < divs.length; d++) {
		var inputs = divs[d].querySelectorAll('.selectBoxInput');
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].onload) {
				inputs[i].onload();
			}
		}
	}
}

function fixChromeTableCollapse() {
	if (typeof(window.chrome) != "undefined") {
		var tabs = document.getElementsByTagName('table');
		for (var t = 0; t < tabs.length; t++) {
			var tbodys = tabs[t].childNodes;
			for (var b = 0; b < tbodys.length; b++) {
				if (tbodys[b].nodeName == 'TBODY') {
					var trs = tbodys[b].childNodes;
					for (var i = 0; i < trs.length; i++) {
						if (trs[i].nodeName == 'TR') {
							if (typeof(trs[i].style) != "undefined" && typeof(trs[i].style.visibility) != "undefined" && trs[i].style.visibility == 'collapse') {
								var tdcells = trs[i].childNodes;
								if (typeof(tabs[t].classList) != "undefined" && ! tabs[t].classList.contains('rounded-corners') && ! tabs[t].classList.contains('rounded-corners2') && tabs[t].style.borderCollapse != 'collapse') tabs[t].style.borderCollapse = 'collapse';
								for (var i2 = 0; i2 < tdcells.length; i2++) {
									var newdiv = document.createElement('DIV');
									newdiv.classList.add('chrome_collapsed_div');
									var divs = tdcells[i2].childNodes;
									var clonediv = 0;
									if (divs.length == 1) {
										if (! (divs[0].nodeName == 'DIV' && typeof(divs[0].classList) != "undefined" && divs[0].classList.contains('chrome_collapsed_div'))) clonediv = 1;
									} else if (divs.length > 1) {
										clonediv = 1;
									}
									if (clonediv) {
										for (var dl = 0; dl < divs.length; dl++) {
											var dnode = tdcells[i2].removeChild(divs[dl]);
											newdiv.appendChild(dnode);
										}
										tdcells[i2].appendChild(newdiv);
									}
									if (typeof(tdcells[i2].classList) != "undefined" && ! tdcells[i2].classList.contains('chrome_collapsed_row')) tdcells[i2].classList.add('chrome_collapsed_row');
								}
							}
						}
					}
				}
			}
		}
	}
}

function fixChromeTableExpand(trid) {
	if (typeof(window.chrome) != "undefined") {
		var tr = document.getElementById(trid);
		if (tr.style.visibility == 'collapse') {
			if (typeof(tr.parentNode.parentNode.classList) != "undefined" && ! tr.parentNode.parentNode.classList.contains('rounded-corners') && ! tr.parentNode.parentNode.classList.contains('rounded-corners2') && tr.parentNode.parentNode.style.borderCollapse != 'collapse') tr.parentNode.parentNode.style.borderCollapse = 'separate';
			var tdcells = tr.childNodes;
			for (var i2 = 0; i2 < tdcells.length; i2++) {
				if (typeof(tdcells[i2].classList) != "undefined" && tdcells[i2].classList.contains('chrome_collapsed_row')) tdcells[i2].classList.remove('chrome_collapsed_row');
			}
		}
	}
}

function fixChromeTableExpand2(trname) {
	if (typeof(window.chrome) != "undefined") {
		var trs = document.getElementsByName(trname);
		for (var ti = 0; ti < trs.length; ti++) {
			var tr = trs[ti];
			if (tr.style.visibility == 'collapse') {
				if (typeof(tr.parentNode.parentNode.classList) != "undefined" && ! tr.parentNode.parentNode.classList.contains('rounded-corners') && ! tr.parentNode.parentNode.classList.contains('rounded-corners2') && tr.parentNode.parentNode.style.borderCollapse != 'collapse') tr.parentNode.parentNode.style.borderCollapse = 'separate';
				var tdcells = tr.childNodes;
				for (var i2 = 0; i2 < tdcells.length; i2++) {
					if (typeof(tdcells[i2].classList) != "undefined" && tdcells[i2].classList.contains('chrome_collapsed_row')) tdcells[i2].classList.remove('chrome_collapsed_row');
				}
			}
		}
	}
}

// ################################################################################
// getXHR() - Returns an xmlhttprequest or MS equiv.
var getXHR = function() {
	var xmlhttp = false;
	try {
		xmlhttp = new XMLHttpRequest();
		if (xmlhttp) getXHR = function() {
			return new XMLHttpRequest();
		};
	} catch(e) {
		var msxml = ['MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'];
		for (var i = 0, len = msxml.length; i < len; ++i) {
			try {
				xmlhttp = new ActiveXObject(msxml[i]);
				if (xmlhttp) {
					if (i == 0) {
						getXHR = function() {
							return new ActiveXObject('MSXML2.XMLHTTP.3.0');
						};
					} else if (i == 1) {
						getXHR = function() {
							return new ActiveXObject('MSXML2.XMLHTTP');
						};
					} else if (i == 2) {
						getXHR = function() {
							return new ActiveXObject('Microsoft.XMLHTTP');
						};
					}
				}
				break;
			} catch(e) {}
		}
	}
	return xmlhttp;
}

// List Mix status add and remove
function mod_mix_status(stage, vcl_id, entry) {
	var mod_status = document.getElementById("dial_status_" + entry + "_" + vcl_id);
	if (mod_status.value.length < 1) {
		alert("You must select a status first");
	} else {
		var old_statuses = document.getElementById("status_" + entry + "_" + vcl_id);
		var ROold_statuses = document.getElementById("ROstatus_" + entry + "_" + vcl_id);
		var MODstatus = new RegExp(" " + mod_status.value + " ", "g");
		if (stage == "ADD") {
			if (old_statuses.value.match(MODstatus)) {
				alert("The status " + mod_status.value + " is already present");
			} else {
				var new_statuses = " " + mod_status.value + "" + old_statuses.value;
				old_statuses.value = new_statuses;
				ROold_statuses.value = new_statuses;
				mod_status.value = "";
			}
		}
		if (stage == "REMOVE") {
			var MODstatus = new RegExp(" " + mod_status.value + " ", "g");
			old_statuses.value = old_statuses.value.replace(MODstatus, " ");
			ROold_statuses.value = ROold_statuses.value.replace(MODstatus, " ");
		}
	}
}

// List Mix percent difference calculation and warning message
function mod_mix_percent(vcl_id, entries) {
	var i = 0;
	var total_percent = 0;
	var percent_diff = '';
	while (i < entries) {
		var mod_percent_field = document.getElementById("percentage_" + i + "_" + vcl_id);
		temp_percent = mod_percent_field.value * 1;
		total_percent = (total_percent + temp_percent);
		i++;
	}

	var mod_diff_percent = document.getElementById("PCT_DIFF_" + vcl_id);
	percent_diff = (total_percent - 100);
	if (percent_diff > 0) {
		percent_diff = '+' + percent_diff;
	}
	var mix_list_submit = document.getElementById("submit_" + vcl_id);
	if ((percent_diff > 0) || (percent_diff < 0)) {
		mix_list_submit.disabled = true;
		document.getElementById("ERROR_" + vcl_id).innerHTML = "<span class=fgred><B>The Difference % must be 0</B></span>";
	} else {
		mix_list_submit.disabled = false;
		document.getElementById("ERROR_" + vcl_id).innerHTML = "";
	}

	mod_diff_percent.value = percent_diff;
}

function submit_mix(vcl_id, entries) {
	var h = 1;
	var j = 1;
	var list_mix_container = '';
	var mod_list_mix_container_field = document.getElementById("list_mix_container_" + vcl_id);
	while (h < 11) {
		var i = 0;
		while (i < entries) {
			var mod_list_id_field = document.getElementById("list_id_" + i + "_" + vcl_id);
			var mod_priority_field = document.getElementById("priority_" + i + "_" + vcl_id);
			var mod_percent_field = document.getElementById("percentage_" + i + "_" + vcl_id);
			var mod_statuses_field = document.getElementById("status_" + i + "_" + vcl_id);
			if (mod_priority_field.value == h) {
				list_mix_container = list_mix_container + mod_list_id_field.value + "|" + j + "|" + mod_percent_field.value + "|" + mod_statuses_field.value + "|:";
				j++;
			}
			i++;
		}
		h++
	}
	mod_list_mix_container_field.value = list_mix_container;
	var form_to_submit = document.getElementById("" + vcl_id);
	form_to_submit.submit();
}

function scriptInsertField() {
	//openField = '--A--';
	openField = '[[';
	//closeField = '--B--';
	closeField = ']]';
	var textBox = document.scriptForm.script_text;
	var scriptIndex = document.getElementById("selectedField").selectedIndex;
	var insValue = document.getElementById('selectedField').options[scriptIndex].value;
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
		textBox.value = textBox.value.substring(0, startPos) + openField + insValue + closeField + textBox.value.substring(endPos, textBox.value.length);
		document.scriptForm.script_text.focus();
		textBox.selectionStart = startPos;
		textBox.selectionEnd = startPos + openField.length + insValue.length + closeField.length;
	} else {
		textBox.value += openField + insValue + closeField;
	}
}

function scriptInsertAddtlField() {
	//openField = '--A--';
	openField = '[[';
	//closeField = '--B--';
	closeField = ']]';
	var textBox = document.scriptForm.script_text;
	var scriptIndex = document.getElementById("selectedAddtlField").selectedIndex;
	var insValue = document.getElementById('selectedAddtlField').options[scriptIndex].value;
	if (document.selection) {
		//IE
		textBox = document.scriptForm.script_text;
		insValue = document.scriptForm.selectedAddtlField.options[document.scriptForm.selectedAddtlField.selectedIndex].text;
		textBox.focus();
		sel = document.selection.createRange();
		sel.text = openField + insValue + closeField;
	} else if (textBox.selectionStart || textBox.selectionStart == 0) {
		//Mozilla
		var startPos = textBox.selectionStart;
		var endPos = textBox.selectionEnd;
		textBox.value = textBox.value.substring(0, startPos) + openField + insValue + closeField + textBox.value.substring(endPos, textBox.value.length);
		document.scriptForm.script_text.focus();
		textBox.selectionStart = startPos;
		textBox.selectionEnd = startPos + openField.length + insValue.length + closeField.length;
	} else {
		textBox.value += openField + insValue + closeField;
	}
}

function scriptInsertButton() {
	//openField = '--A--';
	openField = '{{';
	//closeField = '--B--';
	closeField = '}}';
	var textBox = document.scriptForm.script_text;
	var scriptIndex = document.getElementById("selectedButton").selectedIndex;
	var insValue = document.getElementById('selectedButton').options[scriptIndex].value;
	if (document.selection) {
		//IE
		textBox = document.scriptForm.script_text;
		insValue = document.scriptForm.selectedButton.options[document.scriptForm.selectedButton.selectedIndex].text;
		textBox.focus();
		sel = document.selection.createRange();
		sel.text = openField + insValue + closeField;
	} else if (textBox.selectionStart || textBox.selectionStart == 0) {
		//Mozilla
		var startPos = textBox.selectionStart;
		var endPos = textBox.selectionEnd;
		textBox.value = textBox.value.substring(0, startPos) + openField + insValue + closeField + textBox.value.substring(endPos, textBox.value.length);
		document.scriptForm.script_text.focus();
		textBox.selectionStart = startPos;
		textBox.selectionEnd = startPos + openField.length + insValue.length + closeField.length;
	} else {
		textBox.value += openField + insValue + closeField;
	}
}

// Show the time 
var tick;
function stop() {
	clearTimeout(tick);
}

function usnotime() {
	var ut = new Date();
	var h, m, s;
	var time = "      ";
	h = ut.getHours();
	m = ut.getMinutes();
	s = ut.getSeconds();
	if (s <= 9) s = "0" + s;
	if (m <= 9) m = "0" + m;
	if (h <= 9) h = "0" + h;
	time += h + ":" + m + ":" + s;
	document.rclock.rtime.value = time;
	tick = setTimeout("usnotime()", 1000);
}

function openNewWindow(url) {
	window.open(url, "_blank", '');
}

function ShowProgress(good, bad, total, dup, post, affcnt) {
	parent.lead_count.document.open();
	parent.lead_count.document.write('<html><body><table frame=border width=285 cellpadding=2 cellspacing=0 align=center valign=top style="box-shadow: rgba(0,0,0,0.6) 1px 1px 10px;"><tr bgcolor="#FFF"><th colspan=4><span class="font2 fgwhite">File Load Status</span><br/><br/></th></tr><tr bgcolor="#FFF"><td align=left width=60% colspan=2><span class="font1 fgwhite"><B>&nbsp;Good:</B></span></td><td align=left width=15%><span class="font1 fgwhite"><B>' + good + '</B></span></td><td width=25%>&nbsp</td></tr><tr bgcolor="#FFF"><td align=left colspan=2><span class="font1 fgwhite"><B>&nbsp;Duplicate:</B></span></td><td align=left><span class="font1 fgwhite"><B>' + dup + '</B></span></td><td>&nbsp;</td></tr><tr bgcolor="#FFF"><td align=left colspan=2><span class="font1 fgwhite"><B>&nbsp;Bad:</B></span></td><td align=left><span class="font1 fgwhite"><B>' + bad + '</B></span></td><td></td></tr><tr bgcolor="#FFF"><td align=left colspan=2><span class="font1 fgwhite"><B>&nbsp;Total:</B></span></td><td align=left><span class="font1 fgwhite"><B>' + total + '</B></span></td><td>&nbsp;</td></tr><tr bgcolor="#FFF"><td align=left colspan=2><span class="font1 fgwhite"></span></td><td align=left><span class="font1 fgwhite"></span></td><td></td></tr><tr bgcolor="#FFF"><td align=left colspan=2><span class="font1 fgwhite"><B>&nbsp;Postal/GMT&nbsp;Match:</B></span></td><td align=left><span class="font1 fgwhite"><B>' + post + '</B></span></td><td>&nbsp;</td></tr><tr bgcolor="#FFF"><td align=left colspan=2><span class="font1 fgwhite"><B>&nbsp;AFF&nbsp;Record&nbsp;Count:</B></span></td><td align=left><span class="font1 fgwhite"><B>' + affcnt + '</B></span></td><td>&nbsp;</td></tr><tr bgcolor="#FFF"><td align=left colspan=2><span class="font1 fgwhite"></span></td><td align=left><span class="font1 fgwhite"></span></td><td></td></tr></table><body></html>');
	parent.lead_count.document.close();
}

function ParseFileName() {
	if (!document.forms[0].OK_to_process) {
		var endstr = document.forms[0].leadfile.value.lastIndexOf('\\');
		if (endstr > - 1) {
			endstr++;
			var filename = document.forms[0].leadfile.value.substring(endstr);
			document.forms[0].leadfile_name.value = filename;
		}
	}
}

function stopOAC() {
	if (oac_timer != null) {
		clearTimeout(oac_timer);
		oac_timer = null;
	}
}

function resumeOAC() {
	if (oac_timer == null && oac_last_delay != 0) {
		oac_timer = setTimeout(function() {
			refreshOAC(oac_last_script, oac_last_params, oac_last_delay);
		},
		oac_last_delay);
	}
}

function refreshOAC(script, params, delay) {
	var xmlhttp = getXHR();
	//console.log(script+' : '+unescape(params)+' : '+delay);
	if (xmlhttp) {
		async = true;
		xmlhttp.open('POST', script, async);
		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		handleresponse = function() {
			if (!async || xmlhttp.readyState == 4) {
				if (xmlhttp.status == 200) {
					htmlcontent = xmlhttp.responseText;
					document.getElementById("content").innerHTML = htmlcontent;
				}
				if (oac_timer != null) {
					clearTimeout(oac_timer);
					oac_timer = null;
				}
				oac_last_script = script;
				oac_last_params = params;
				oac_last_delay = delay;
				oac_timer = setTimeout(function() {
					refreshOAC(script, params, delay);
				},
				delay);
			}
		}
		xmlhttp.send(unescape(params));
		if (async) {
			xmlhttp.onreadystatechange = handleresponse;
		} else {
			handleresponse();
		}
		delete xmlhttp;
	}
}

function sessionCheck() {
	var xmlhttp = getXHR();
	if (xmlhttp) {
		async = true;
		xmlhttp.open('POST', window.location.pathname, async);
		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		handleresponse = function() {
			if (!async || xmlhttp.readyState == 4) {
				if (xmlhttp.status == 200) {
					htmlcontent = xmlhttp.responseText;
					if (htmlcontent=='EXPIRED') window.location=window.location.pathname+'?sess_timeout=1';
					if (htmlcontent=='AUTH_FAILED') window.location=window.location.pathname+'?sess_login=1';
				}
				if (sess_timer != null) {
					clearTimeout(sess_timer);
					sess_timer = null;
				}
				sess_timer = setTimeout(function() {
					sessionCheck();
				},
				60000);
			}
		}
		xmlhttp.send("sess_check=1");
		if (async) {
			xmlhttp.onreadystatechange = handleresponse;
		} else {
			handleresponse();
		}
		delete xmlhttp;
	}
}

// Display time
function clockinit() {
	document.getElementById("clock").innerHTML = "";
}

function updateClock() {
	var currentTime = new Date();
	var currentHours = currentTime.getHours();
	var currentMinutes = currentTime.getMinutes();
	var currentSeconds = currentTime.getSeconds();
	currentMinutes = (currentMinutes < 10 ? "0": "") + currentMinutes;
	currentSeconds = (currentSeconds < 10 ? "0": "") + currentSeconds;
	var timeOfDay = (currentHours < 12) ? "AM": "PM";
	currentHours = (currentHours > 12) ? currentHours - 12: currentHours;
	currentHours = (currentHours == 0) ? 12: currentHours;
	var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
	document.getElementById("clock").innerHTML = currentTimeString;
}

Date.prototype.addDays = function(days) {
	this.setDate(this.getDate() + days);
	return this;
}

function keyextonly(evt) {
	var e = evt? evt : window.event;
	if(!e) return;
	var key = 0;
	if (e.keyCode) {
		key = e.keyCode;
	} else if (typeof(e.which)!= 'undefined') {
		key = e.which;
	}
	if (!((key>=48 && key<=57)||key==8||key==9||key==13||key==37||key==39||key==88||key==90||key==78||key==46||key==91||key==93||key==45||key==95||key==116||key==104||key==105||key==42||key==35||key==115)) {
		e.cancel=true;
		e.returnValue=false;
	}
}
function keynumonly(evt) {
	var e = evt? evt : window.event;
	if(!e) return;
	var key = 0;
	if (e.keyCode) {
		key = e.keyCode;
	} else if (typeof(e.which)!= 'undefined') {
		key = e.which;
	}
	if (!((key>=48 && key<=57)||key==8||key==9||key==13||key==37||key==39)) {
		e.cancel=true;
		e.returnValue=false;
	}
}

// The following functions get the GMT and DST of the client browser and store as a cookie.
var cgexp = new Date();
cgexp.setTime(cgexp.getTime() + 24 * 60 * 60 * 1000);

var webClientDate = new Date();
var webClientGMT = - (webClientDate.getTimezoneOffset() / 60);
document.cookie = "webClientGMT=" + webClientGMT + "; expires=" + cgexp;

var webClientDST = 0;
var dstchk = new Date();
var dstchk1 = new Date();
var dstchk7 = new Date();
dstchk.setDate(1);
dstchk1.setDate(1);
dstchk7.setDate(1);
dstchk1.setMonth(0);
dstchk7.setMonth(6);
var janGMTtmp = - (dstchk1.getTimezoneOffset() / 60);
var julGMTtmp = - (dstchk7.getTimezoneOffset() / 60);
// January and July do not have the same GMT Offset...check if we are in DST.
if (janGMTtmp != julGMTtmp) {
	var nodstgmt = 13;
	for (var i = 0; i < 12; i++) {
		dstchk.setMonth(i);
		var webClientGMTmonth = - (dstchk.getTimezoneOffset() / 60);
		if (nodstgmt > webClientGMTmonth) nodstgmt = webClientGMTmonth;
	}
	if (webClientGMT != nodstgmt) webClientDST = 1;
}
document.cookie = "webClientDST=" + webClientDST + "; expires=" + cgexp;

// Ctrl-Z to turn copy/paste selection on/off.
document.onkeypress = function(evt) {
	var e = evt? evt : window.event;
	if(!e) return;
	var key = 0;
	if (e.keyCode) {
		key = e.keyCode;
	} else if (typeof(e.which)!= 'undefined') {
		key = e.which;
	}
	if (key==26) {
		if (document.getElementById('content').style.webkitUserSelect!='initial') {
			document.getElementById('content').style.webkitUserSelect='initial';
		} else {
			document.getElementById('content').style.webkitUserSelect='none';
		}
	}
}
document.onkeyup = function(evt) {
	var e = evt? evt : window.event;
	if(!e) return;
	var key = 0;
	if (e.keyCode) {
		key = e.keyCode;
	} else if (typeof(e.which)!= 'undefined') {
		key = e.which;
	}
	if (key==17) {
		ctrl_key = false;
	}
}
document.onkeydown = function(evt) {
	var e = evt? evt : window.event;
	if(!e) return;
	var key = 0;
	if (e.keyCode) {
		key = e.keyCode;
	} else if (typeof(e.which)!= 'undefined') {
		key = e.which;
	}
	if (key==17) {
		ctrl_key = true;
	}
}

// ################################################################################
// Return seconds as hh:mm:sec as applicable
function sec2time(secIn) {
	var hours = Math.floor(secIn / 3600);
	var minutes = Math.floor((secIn - (hours * 3600)) / 60);
	var seconds = secIn - (hours * 3600) - (minutes * 60);

	if (hours < 10) {
		hours = hours;
	}
	if (minutes < 10 && hours != 0) {
		minutes = "0" + minutes;
	}
	if (seconds < 10 && (hours != 0 || minutes != 0)) {
		seconds = "0" + seconds;
	}

	var timeOut = hours + ':' + minutes + ':' + seconds;

	if (hours == 0 && minutes > 0) {
		timeOut = minutes + ':' + seconds;
	}
	if (hours == 0 && minutes == 0) {
		timeOut = seconds;
	}

	return timeOut;
}

