/*TODO: remove######################
# ADD=31 or 34 and SUB=29 for list mixes
######################
if ( ( ($ADD==34) or ($ADD==31) or ($ADD==49) ) and ($SUB==29) and ($LOGmodify_campaigns==1) and ( (eregi("$campaign_id",$LOGallowed_campaigns)) or (eregi("ALL-CAMPAIGNS",$LOGallowed_campaigns)) ) ) 
{

?->*/
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
/*TODO: remove<-?
}
?->*/

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

function scriptInsertAddtlField() {
	openField = '--A--';
	closeField = '--B--';
	var textBox = document.scriptForm.script_text;
	var scriptIndex = document.getElementById("selectedAddtlField").selectedIndex;
	var insValue =  document.getElementById('selectedAddtlField').options[scriptIndex].value;
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

