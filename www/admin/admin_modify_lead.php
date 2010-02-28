<?
# admin_modify_lead.php
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

# 
# AST GUI database administration modify lead in osdial_list
# admin_modify_lead.php
#
# this is the administration lead information modifier screen, the administrator 
# just needs to enter the leadID and then they can view and modify the 
# information in the record for that lead
#
# CHANGES
#
# 60419-1705 - Added ability to change lead callback record from USERONLY to ANYONE or USERONLY-user
# 60421-1459 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60609-1112 - Added DNC list addition if status changed to DNC
# 60619-1539 - Added variable filtering to eliminate SQL injection attack threat
# 61130-1639 - Added recording_log lookup and list for this lead_id
# 61201-1136 - Added recording_log user(TSR) display and link
# 70305-1133 - Changed to default CHECKED modify logs upon status change
# 70424-1128 - Added campaign-specific statuses, reformatted recordings list
# 70702-1259 - Added recording location link and truncation
# 70906-2132 - Added closer_log records display
# 80428-0144 - UTF8 cleanup
# 80516-0936 - Cleanup of logging changes, added osdial_agent_log display
# 80701-0832 - Changed to allow for altering of main phone number
#
# 090410-1131 - Added field custom2
# 090410-1541 - Added field external_key

require("include/dbconnect.php");
require("include/functions.php");
require("include/variables.php");

$add_closer_record = get_variable("add_closer_record");
$address1 = get_variable("address1");
$address2 = get_variable("address2");
$address3 = get_variable("address3");
$alt_phone = get_variable("alt_phone");
$alf_id = get_variable("alf_id");
$alf_fld_id = get_variable("alf_fld_id");
$alf_val = get_variable("alf_val");
$call_began = get_variable("call_began");
$callback_id = get_variable("callback_id");
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
$date_of_birth = get_variable("date_of_birth");
$dispo = get_variable("dispo");
$email = get_variable("email");
$end_call = get_variable("end_call");
$gender = get_variable("gender");
$middle_initial = get_variable("middle_initial");
$modify_logs = get_variable("modify_logs");
$modify_closer_logs = get_variable("modify_closer_logs");
$modify_agent_logs = get_variable("modify_agent_logs");
$middle_initial = get_variable("middle_initial");
$old_phone = get_variable("old_phone");
$parked_time = get_variable("parked_time");
$phone_code = get_variable("phone_code");
$post_date = get_variable("post_date");
$postal_code = get_variable("postal_code");
$province = get_variable("province");
$save_aff = get_variable("save_aff");
$server_ip = get_variable("server_ip");
$source_id = get_variable("source_id");
$state = get_variable("state");
$title = get_variable("title");
$tsr = get_variable("tsr");

$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

if ($non_latin < 1) {
	$old_phone = ereg_replace("[^0-9]","",$old_phone);
	$phone_number = ereg_replace("[^0-9]","",$phone_number);
	$alt_phone = ereg_replace("[^0-9]","",$alt_phone);
}
if (strlen($phone_number)<6) $phone_number=$old_phone;

$stmt = sprintf("SELECT count(*) from osdial_users where user='%s' and pass='%s' and user_level > 7 and modify_leads='1';", mres($PHP_AUTH_USER), mres($PHP_AUTH_PW));
if ($DB) echo "|$stmt|\n";
if ($non_latin > 0) $rslt=mysql_query("SET NAMES 'UTF8'");
$rslt = mysql_query($stmt, $link);
$row = mysql_fetch_row($rslt);
$auth = $row[0];

if ($WeBRooTWritablE > 0) $fp = fopen ("./project_auth_entries.txt", "a");

$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
    Header("WWW-Authenticate: Basic realm=\"OSDIAL\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
} elseif($auth>0) {
	$stmt = sprintf("SELECT full_name,modify_leads from osdial_users where user='%s' and pass='%s'", mres($PHP_AUTH_USER), mres($PHP_AUTH_PW));
	$rslt = mysql_query($stmt, $link);
	$row = mysql_fetch_row($rslt);
	$LOGfullname = $row[0];
	$LOGmodify_leads = $row[1];

	if ($WeBRooTWritablE > 0) {
		fwrite ($fp, "OSDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
		fclose($fp);
	}
} elseif ($WeBRooTWritablE > 0) {
	fwrite ($fp, "OSDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
	fclose($fp);
}

require($WeBServeRRooT . "/admin/templates/default/display.php");
include($WeBServeRRooT . "/admin/templates/" . $system_settings['admin_template'] . "/display.php");

echo "<head>\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $system_settings['admin_template'] . "/styles.css\" media=\"screen\">\n";
echo "</head>\n";
echo "<body>\n";
echo "<br>\n";
echo "<table cellpadding='0' cellspacing='0' bgcolor='#E9E8D9' align='center' width='900'>\n";
echo "  <tr>\n";
echo "    <td margin='10' align='center'>\n";
echo "      <font color=$default_text size='3'><b>LEAD MODIFICATION</b></font><br><br>\n";
echo "      <a target=\"_parent\" href=\"./admin.php?ADD=112\">[ Search Again ]</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a target=\"_parent\" href=\"./admin.php?ADD=1122\">[ Advanced Search ]</a><br><br>\n";
			
if ($end_call > 0) {
	### update the lead record in the osdial_list table 
	$stmt = sprintf("UPDATE osdial_list SET status='%s',source_id='%s',title='%s',first_name='%s',middle_initial='%s',last_name='%s',address1='%s',address2='%s',address3='%s',city='%s',state='%s',province='%s',postal_code='%s',country_code='%s',alt_phone='%s',phone_code='%s',phone_number='%s',email='%s',custom1='%s',custom2='%s',external_key='%s',comments='%s',date_of_birth='%s',post_date='%s',cost='%s' WHERE lead_id='%s';", mres($status), mres($source_id), mres($title), mres($first_name), mres($middle_initial), mres($last_name), mres($address1), mres($address2), mres($address3), mres($city), mres($state), mres($province), mres($postal_code), mres($country_code), mres($alt_phone), mres($phone_code), mres($phone_number), mres($email), mres($custom1), mres($custom2), mres($external_key), mres($comments), mres($date_of_birth), mres($post_date), mres($cost), mres($lead_id));
	if ($DB) echo "|$stmt|\n";
	$rslt=mysql_query($stmt, $link);
			
    echo "<a href=\"./admin_modify_lead.php?lead_id=$lead_id\">[ Go Back to Lead #$lead_id ]</a><br><br>\n";
	echo "<b>Information Modified.</b><br><br>\n";
	#echo "<form><input type=button value=\"Close This Window\" onClick=\"javascript:window.close();\"></form>\n";
    #echo "<script language='javascript'>\nwindow.location='/admin/admin_modify_lead.php?lead_id=$lead_id';\n</script>\n";
				
	### inactivate osdial_callbacks record for this lead 
	if ($dispo != $status and $dispo == 'CBHOLD') {
		$stmt = sprintf("UPDATE osdial_callbacks set status='INACTIVE' where lead_id='%s' and status='ACTIVE';", mres($lead_id));
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
		echo "<BR>osdial_callback record inactivated: $lead_id<BR>\n";
	}

	### inactivate osdial_callbacks record for this lead 
	if ($dispo != $status and $dispo == 'CALLBK') {
		$stmt="UPDATE osdial_callbacks set status='INACTIVE' where lead_id='" . mres($lead_id) . "' and status IN('ACTIVE','LIVE');";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
		echo "<BR>osdial_callback record inactivated: $lead_id<BR>\n";
	}
			
	### add lead to the internal DNC list 
	if ($dispo != $status and $status == 'DNC') {
		$stmt="INSERT INTO osdial_dnc (phone_number) values('" . mres($phone_number) . "');";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
		echo "<BR>Lead added to DNC List: $lead_id - $phone_number<BR>\n";
	}

	### update last record in osdial_log table
	if ($dispo != $status and $modify_logs > 0) {
		$stmt="UPDATE osdial_log set status='" . mres($status) . "' where lead_id='" . mres($lead_id) . "' order by call_date desc limit 1";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
	}
			
	### update last record in osdial_closer_log table
	if (($dispo != $status) and ($modify_closer_logs > 0)) {
		$stmt="UPDATE osdial_closer_log set status='" . mres($status) . "' where lead_id='" . mres($lead_id) . "' order by call_date desc limit 1";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
	}
			
	### update last record in osdial_agent_log table
	if (($dispo != $status) and ($modify_agent_logs > 0)) {
		$stmt="UPDATE osdial_agent_log set status='" . mres($status) . "' where lead_id='" . mres($lead_id) . "' order by agent_log_id desc limit 1";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
	}
			
	### insert a NEW record to the osdial_closer_log table 
	if ($add_closer_record > 0) {
		$stmt="INSERT INTO osdial_closer_log (lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed) values('" . mres($lead_id) . "','" . mres($list_id) . "','" . mres($campaign_id) . "','" . mres($parked_time) . "','$NOW_TIME','$STARTtime','1','" . mres($status) . "','" . mres($phone_code) . "','" . mres($phone_number) . "','$PHP_AUTH_USER','" . mres($comments) . "','Y')";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
	}
			
} else {

	### inactivate osdial_callbacks record for this lead 
	if ($CBchangeUSERtoANY == 'YES') {
		$stmt="UPDATE osdial_callbacks set recipient='ANYONE' where callback_id='" . mres($callback_id) . "';";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
		echo "<BR>osdial_callback record changed to ANYONE<BR>\n";
	}

	### inactivate osdial_callbacks record for this lead 
	if ($CBchangeUSERtoUSER == 'YES') {
		$stmt="UPDATE osdial_callbacks set user='" . mres($CBuser) . "' where callback_id='" . mres($callback_id) . "';";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
		echo "<BR>osdial_callback record user changed to $CBuser<BR>\n";
	}	

	### inactivate osdial_callbacks record for this lead 
	if ($CBchangeANYtoUSER == 'YES') {
		$stmt="UPDATE osdial_callbacks set user='" . mres($CBuser) . "',recipient='USERONLY' where callback_id='" . mres($callback_id) . "';";
		if ($DB) echo "|$stmt|\n";
		$rslt=mysql_query($stmt, $link);
		echo "<BR>osdial_callback record changed to USERONLY, user: $CBuser<BR>\n";
	}	
				
			
    $ld = get_first_record($link, 'osdial_list', '*', sprintf("lead_id='%s'",mres($lead_id)));
			
	if ($ld[lead_id] > 0) {
			
		##### grab osdial_log records #####
		$stmt="select * from osdial_log where lead_id='" . mres($lead_id) . "' order by uniqueid desc limit 500;";
		$rslt=mysql_query($stmt, $link);
		$logs_to_print = mysql_num_rows($rslt);
			
		$u=0;
		$call_log = '';
		$log_campaign = '';
		while ($logs_to_print > $u) {
			$row=mysql_fetch_row($rslt);
			if (strlen($log_campaign)<1) $log_campaign = $row[3];
			if (eregi("1$|3$|5$|7$|9$", $u)) {
				$bgcolor='bgcolor=' . $oddrows; 
			} else {
				$bgcolor='bgcolor=' . $evenrows;
            }
			
			$u++;
			$call_log .= "  <tr $bgcolor class=\"row font1\">\n";
			$call_log .= "    <td>$u</td>\n";
			$call_log .= "    <td>$row[4]</td>\n";
			$call_log .= "    <td align=left>$row[7]</td>\n";
			$call_log .= "    <td align=left>$row[8]</td>\n";
			$call_log .= "    <td align=left><a href=\"admin.php?ADD=999999&SUB=21&agent=$row[11]\" target=\"_blank\">$row[11]</a></td>\n";
			$call_log .= "    <td align=right>$row[3]</td>\n";
			$call_log .= "    <td align=right>$row[2]</td>\n";
			$call_log .= "    <td align=right>$row[1]</td>\n";
			$call_log .= "    <td align=right>$row[15]</td>\n";
            $call_log .= "  </tr>\n";
			
			$campaign_id = $row[3];
		}
			
		##### grab osdial_agent_log records #####
		$stmt="select * from osdial_agent_log where lead_id='" . mres($lead_id) . "' order by agent_log_id desc limit 500;";
		$rslt=mysql_query($stmt, $link);
		$Alogs_to_print = mysql_num_rows($rslt);
			
		$y=0;
		$agent_log = '';
		$Alog_campaign = '';
		while ($Alogs_to_print > $y) {
			$row=mysql_fetch_row($rslt);
			if (strlen($Alog_campaign)<1) $Alog_campaign = $row[5];
			if (eregi("1$|3$|5$|7$|9$", $y)) {
				$bgcolor='bgcolor=' . $oddrows; 
			} else {
				$bgcolor='bgcolor=' . $evenrows;
            }
			$y++;
			$agent_log .= "  <tr $bgcolor class=\"row font1\">\n";
			$agent_log .= "    <td>$y</td>\n";
			$agent_log .= "    <td>$row[3]</td>\n";
			$agent_log .= "    <td align=left>$row[5]</td>\n";
			$agent_log .= "    <td align=left><a href=\"admin.php?ADD=999999&SUB=21&agent=$row[11]\" target=\"_blank\">$row[1]</a></td>\n";
			$agent_log .= "    <td align=right>$row[7]</td>\n";
			$agent_log .= "    <td align=right>$row[9]</td>\n";
			$agent_log .= "    <td align=right>$row[11]</td>\n";
			$agent_log .= "    <td align=right>$row[13]</td>\n";
			$agent_log .= "    <td align=right>$row[14]</td>\n";
			$agent_log .= "    <td align=right>$row[15]</td>\n";
			$agent_log .= "    <td align=right>$row[17]</td>\n";
            $agent_log .= "  </tr>\n";
		
			$campaign_id = $row[5];
		}
			
	    ##### grab osdial_closer_log records #####
		$stmt="select * from osdial_closer_log where lead_id='" . mres($lead_id) . "' order by closecallid desc limit 500;";
		$rslt=mysql_query($stmt, $link);
		$Clogs_to_print = mysql_num_rows($rslt);
			
		$y=0;
		$closer_log = '';
		$Clog_campaign = '';
		while ($Clogs_to_print > $y) {
			$row=mysql_fetch_row($rslt);
			if (strlen($Clog_campaign)<1) $Clog_campaign = $row[3];
			if (eregi("1$|3$|5$|7$|9$", $y)) {
				$bgcolor='bgcolor=' . $oddrows; 
			} else {
				$bgcolor='bgcolor=' . $evenrows;
            }
			$y++;
			$closer_log .= "  <tr $bgcolor class=\"row font1\">\n";
			$closer_log .= "    <td>$y</td>\n";
			$closer_log .= "    <td>$row[4]</td>\n";
			$closer_log .= "    <td align=left>$row[7]</td>\n";
			$closer_log .= "    <td align=left>$row[8]</td>\n";
			$closer_log .= "    <td align=left><a href=\"admin.php?&ADD=999999&SUB=21&agent=$row[11]\" target=\"_blank\">$row[11]</a></td>\n";
			$closer_log .= "    <td align=right>$row[3]</td>\n";
			$closer_log .= "    <td align=right>$row[2]</td>\n";
			$closer_log .= "    <td align=right>$row[1]</td>\n";
			$closer_log .= "    <td align=right>$row[14]</td>\n";
			$closer_log .= "  </tr>\n";
			
			$campaign_id = $row[3];
		}

        if ($save_aff > 0) {
            if (strlen($alf_id) > 0) {
                $stmt = sprintf("UPDATE osdial_list_fields SET value='%s' WHERE id='%s';",mres($alf_val), mres($alf_id));
            } else {
                $stmt = sprintf("INSERT INTO osdial_list_fields SET lead_id='%s',field_id='%s',value='%s';", mres($ld['lead_id']), mres($alf_fld_id), mres($alf_val));
            }
            $rslt = mysql_query($stmt, $link);
        }
			
		echo "  <br>\n";
        echo "  <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
		echo "    <input type=hidden name=end_call value=1>\n";
		echo "    <input type=hidden name=DB value=\"$DB\">\n";
		echo "    <input type=hidden name=lead_id value=\"$ld[lead_id]\">\n";
		echo "    <input type=hidden name=dispo value=\"$ld[status]\">\n";
		echo "    <input type=hidden name=list_id value=\"$ld[list_id]\">\n";
		echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		echo "    <input type=hidden name=old_phone value=\"$ld[phone_number]\">\n";
		echo "    <input type=hidden name=server_ip value=\"$server_ip\">\n";
		echo "    <input type=hidden name=extension value=\"$extension\">\n";
		echo "    <input type=hidden name=channel value=\"$channel\">\n";
		echo "    <input type=hidden name=call_began value=\"$call_began\">\n";
		echo "    <input type=hidden name=parked_time value=\"$parked_time\">\n";

        echo "    <font color='#1C4754' size=2>Call information: $ld[first_name] $ld[last_name] - $ld[phone_number]<br></font>\n";
        echo "    <font color='#1C4754' size=2>Last Call Time: $ld[last_local_call_time] - GMT: $ld[gmt_offset_now]<br></font>\n";
        echo "    <font color='#1C4754' size=1>\n";
		echo "      <table cellspacing=0 cellpadding=1 width=600>\n";
		echo "        <tr class=tabheader>\n";
        echo "          <td width='50%'><font size=+1>Lead ID: $ld[lead_id]</font></td>\n";
        echo "          <td width='50%'><font size=+1>List ID: $ld[list_id]</font></td>\n";
        echo "        </tr>\n";
		echo "        <tr class=tabheader>\n";
        echo "          <td width='50%'>Fronter:&nbsp;<a href=\"admin.php?&ADD=999999&SUB=21&agent=$ld[user]\">$ld[user]</a></td>\n";
        echo "          <td width='50%'>Vendor ID: $ld[vendor_lead_code]</td>\n";
        echo "        </tr>\n";
		echo "      </table>\n";
					
		echo "      <table cellspacing=3 width=600>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=center colspan=2>Title:&nbsp;<input type=text name=title size=4 maxlength=4 value=\"$ld[title]\"> &nbsp; First:&nbsp;<input type=text name=first_name size=15 maxlength=30 value=\"$ld[first_name]\"> &nbsp; M.I.&nbsp;<input type=text name=middle_initial size=2 maxlength=1 value=\"$ld[middle_initial]\"> &nbsp; Last:&nbsp;<input type=text name=last_name size=15 maxlength=30 value=\"$ld[last_name]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr class=tabheader>\n";
        echo "          <td colspan=2></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
	    echo "          <td width=30% align=center>Gender:&nbsp;<select name=gender><option>M</option><option>F</option><option selected>$ld[gender]</gender></select></td>\n";
	    echo "          <td align=center>Birth&nbsp;Date:&nbsp;<input type=text name=date_of_birth size=10 maxlength=10 value=\"$ld[date_of_birth]\"><font size=1>&nbsp;(YYYY-MM-DD)</font></td>\n";
        echo "        </tr>\n";
		echo "        <tr class=tabheader>\n";
        echo "          <td colspan=2></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
	    echo "          <td align=right>Address 1:&nbsp;</td>\n";
		echo "          <td align=left><input type=text name=address1 size=30 maxlength=30 value=\"$ld[address1]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Address 2:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=address2 size=30 maxlength=30 value=\"$ld[address2]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Address 3:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=address3 size=30 maxlength=30 value=\"$ld[address3]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>City:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=city size=30 maxlength=30 value=\"$ld[city]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>State:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=state size=2 maxlength=2 value=\"$ld[state]\"> &nbsp; Postal Code:&nbsp;<input type=text name=postal_code size=10 maxlength=10 value=\"$ld[postal_code]\"> </td>\n";
        echo "        </tr>\n";
			
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Province:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=province size=30 maxlength=30 value=\"$ld[province]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Country:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=country_code size=3 maxlength=3 value=\"$ld[country_code]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr class=tabheader>\n";
        echo "          <td colspan=2></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Phone Code:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=phone_code size=10 maxlength=10 value=\"$ld[phone_code]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Main Phone:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=phone_number size=20 maxlength=20 value=\"$ld[phone_number]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Alt Phone:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=alt_phone size=20 maxlength=20 value=\"$ld[alt_phone]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr class=tabheader>\n";
        echo "          <td colspan=2></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Email:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=email size=30 maxlength=50 value=\"$ld[email]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Source ID:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=source_id size=6 maxlength=6 value=\"$ld[source_id]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>External Key:&nbsp;</td>\n";
        echo "          <td align=left><input type=text name=external_key size=30 maxlength=100 value=\"$ld[external_key]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
	    echo "          <td align=right>Post Date:&nbsp;</td>\n";
		echo "          <td align=left><input type=text name=post_date size=19 maxlength=19 value=\"$ld[post_date]\"><font size=1> (YYYY-MM-DD HH:MM:SS)</font></td>\n";
        echo "        </tr>\n";
        if ($ld[cost] == 0) $ld[cost] = '0.000';
		echo "        <tr bgcolor=$oddrows>\n";
	    echo "          <td align=right>Lead Cost:&nbsp;</td>\n";
		echo "          <td align=left><input type=text name=cost size=6 maxlength=6 value=\"$ld[cost]\"></td>\n";
        echo "        </tr>\n";
		echo "        <tr class=tabheader>\n";
        echo "          <td colspan=2></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Comments:&nbsp;</td>\n";
        echo "          <td align=left><textarea name=comments cols=50 rows=3>$ld[comments]</textarea></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Custom1:&nbsp;</td>\n";
        echo "          <td align=left><textarea name=custom1 cols=50 rows=3>$ld[custom1]</textarea></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Custom2:&nbsp;</td>\n";
        echo "          <td align=left><textarea name=custom2 cols=50 rows=5>$ld[custom2]</textarea></td>\n";
        echo "        </tr>\n";
		echo "        <tr class=tabheader>\n";
        echo "          <td colspan=2></td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Disposition:&nbsp;</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=status>\n";
			
		$stmt="SELECT * FROM osdial_statuses WHERE selectable='Y' ORDER BY status;";
		$rslt=mysql_query($stmt, $link);
		$statuses_to_print = mysql_num_rows($rslt);
		$statuses_list='';
			
		$o=0;
		$DS=0;
		while ($statuses_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			if (strlen($ld[status]) == strlen($rowx[0]) and eregi($ld[status],$rowx[0])) {
                $statuses_list .= "          <option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                $DS++;
            } else {
                $statuses_list .= "          <option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
            }
			$o++;
		}
			
        if (strlen($log_campaign)>0) {
		    $stmt = sprintf("SELECT * FROM osdial_campaign_statuses WHERE selectable='Y' AND campaign_id='%s' ORDER BY status;", mres($log_campaign));
		    $rslt=mysql_query($stmt, $link);
		    $CAMPstatuses_to_print = mysql_num_rows($rslt);
			
		    $o=0;
		    while ($CAMPstatuses_to_print > $o) {
			    $rowx=mysql_fetch_row($rslt);
			    if (strlen($ld[status]) ==  strlen($rowx[0]) and eregi($ld[status],$rowx[0]) ) {
                    $statuses_list .= "          <option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                    $DS++;
                } else {
                    $statuses_list .= "          <option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                }
			    $o++;
		    }
        }
			
			
		if ($DS < 1) $statuses_list .= "          <option SELECTED value=\"$ld[status]\">$ld[status]</option>\n";
	    echo "$statuses_list";
	    echo "            </select>\n";
        if (strlen($log_campaign)>0) echo "             <font size=1><i>(with $log_campaign statuses)</i></font>\n";
        echo "          </td>\n";
        echo "        </tr>\n";
			
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td></td>\n";
        echo "          <td align=left><input type=checkbox name=modify_logs value=\"1\" CHECKED>&nbsp;-&nbsp;Modify&nbsp;OSDial&nbsp;Log</td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td></td>\n";
        echo "          <td align=left><input type=checkbox name=modify_agent_logs value=\"1\" CHECKED>&nbsp;-&nbsp;Modify&nbsp;Agent&nbsp;Log</td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td></td>\n";
        echo "          <td align=left><input type=checkbox name=modify_closer_logs value=\"1\">&nbsp;-&nbsp;Modify&nbsp;Closer&nbsp;Log</td>\n";
        echo "        </tr>\n";
		echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td></td>\n";
        echo "          <td align=left><input type=checkbox name=add_closer_record value=\"1\">&nbsp;-&nbsp;Add&nbsp;Closer&nbsp;Log&nbsp;Record</td>\n";
        echo "        </tr>\n";
			
		echo "        <tr class=tabfooter>\n";
        echo "          <td colspan=2 align=center class=tabbutton><input type=submit name=submit value=\"SUBMIT\"></td>\n";
        echo "        </tr>\n";
		echo "      </table>\n";
        echo "    </form>\n";
		echo "    <br>\n";



        $list = get_first_record($link, 'osdial_lists', '*', sprintf("list_id='%s'",$ld['list_id']));
        $camp = $list['campaign_id'];
        $affrms = get_krh($link, 'osdial_campaign_forms', '*', 'priority ASC', sprintf("campaigns='ALL' OR campaigns='%s' OR campaigns LIKE '%s,%%' OR campaigns LIKE '%%,%s'",mres($camp),mres($camp),mres($camp)), '');
        if (count($affrms) > 0) {
	        echo "    <table width=600 cellspacing=0 cellpadding=1>\n";
	        echo "      <tr class=tabheader>\n";
	        echo "        <td colspan=4><font size='+1'>ADDITIONAL FORM FIELDS</font></td>\n";
	        echo "      </tr>\n";
	        echo "      <tr class=tabheader>\n";
            echo "        <td>Form</td>\n";
            echo "        <td>Field</td>\n";
            echo "        <td>Value</td>\n";
            echo "        <td>Action</td>\n";
	        echo "      </tr>\n";

            $u=0;
            $lastfrm='';
	        foreach ($affrms as $affrm) {
                if ($lastfrm!='' and $lastfrm != $affrm['id']) {
		            echo "        <tr class=tabheader>\n";
                    echo "          <td colspan=4></td>\n";
                    echo "        </tr>\n";
                }
                $lastfrm = $affrm['id'];

                $afflds = get_krh($link, 'osdial_campaign_fields', '*', 'priority ASC', sprintf("form_id='%s'",mres($affrm['id'])), '');
	            foreach ($afflds as $affld) {
                    $afldel='';
                    $skip_fld=0;
		            if (eregi("1$|3$|5$|7$|9$", $u)) {
			            $bgcolor='bgcolor=' . $oddrows; 
		            } else {
			            $bgcolor='bgcolor=' . $evenrows;
                    }
                    $alf = get_first_record($link, 'osdial_list_fields', '*', sprintf("lead_id='%s' AND field_id='%s'",$ld['lead_id'],$affld['id']));
                    if ($affrm['deleted'] > 0 or $affld['deleted'] > 0) {
                        if (strlen($alf['value']) > 0) {
                            $afldel = "color=red";
                            $bgcolor = 'bgcolor=#FFA07A title="This field has been deleted from the system, however, this lead still has data for it."';
                        } else {
                            $skip_fld=1;
                        }
                    }
                    if ($skip_fld < 1) {
                        echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
		                echo '<input type="hidden" name="save_aff" value=1>';
		                echo '<input type="hidden" name="lead_id" value="' . $ld[lead_id] . '">';
		                echo '<input type="hidden" name="alf_id" value="' . $alf[id] . '">';
		                echo '<input type="hidden" name="alf_fld_id" value="' . $affld[id] . '">';
		                echo "    <tr $bgcolor class=\"row font1\">\n";
                        echo "      <td align=center><font $afldel><b>$affrm[name]</b></font></td>\n";
                        echo "      <td align=center><font $afldel><b>$affld[name]</b></font></td>\n";
                        echo "      <td align=center class=tabinput><input type=\"text\" name=\"alf_val\" size=\"30\" maxlength=\"255\" value=\"$alf[value]\"></td>\n";
                        echo "      <td align=center class=tabbutton1><input type=\"submit\" value=\"Save\"></td>\n";
                        echo "    </tr>\n";
                        echo "    </form>\n";
                        $u++;
                    }
                }
            }
            echo "      <tr class=tabfooter>\n";
            echo "        <td colspan=4></td>\n";
            echo "      </tr>\n";
	        echo "    </table>\n";
        }
	    echo "  <br><br>\n";
	    echo "  <br><br>\n";


			
		if ($ld[status] == 'CALLBK' or $ld[status] == 'CBHOLD') {
			### find any osdial_callback records for this lead 
			$stmt="select * from osdial_callbacks where lead_id='" . mres($ld[lead_id]) . "' and status IN('ACTIVE','LIVE') order by callback_id desc LIMIT 1;";
			if ($DB) echo "|$stmt|\n";
			$rslt=mysql_query($stmt, $link);
			$CB_to_print = mysql_num_rows($rslt);
			$rowx=mysql_fetch_row($rslt);
			
			if ($CB_to_print>0) {
				if ($rowx[9] == 'USERONLY') {
					echo "    <br>\n";
                    echo "    <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
					echo "      <input type=hidden name=CBchangeUSERtoANY value=\"YES\">\n";
					echo "      <input type=hidden name=DB value=\"$DB\">\n";
					echo "      <input type=hidden name=lead_id value=\"$ld[lead_id]\">\n";
					echo "      <input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					echo "      <input type=submit name=submit value=\"CHANGE TO ANYONE CALLBACK\">\n";
                    echo "    </form><br>\n";
			
					echo "    <br>\n";
                    echo "    <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
					echo "      <input type=hidden name=CBchangeUSERtoUSER value=\"YES\">\n";
					echo "      <input type=hidden name=DB value=\"$DB\">\n";
					echo "      <input type=hidden name=lead_id value=\"$ld[lead_id]\">\n";
					echo "      <input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					echo "      New Callback Owner UserID: <input type=text name=CBuser size=8 maxlength=10 value=\"$rowx[8]\"> \n";
					echo "      <input type=submit name=submit value=\"CHANGE USERONLY CALLBACK USER\">\n";
                    echo "    </form><br>\n";
				} else {
					echo "    <br>\n";
					echo "    <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
					echo "      <input type=hidden name=CBchangeANYtoUSER value=\"YES\">\n";
					echo "      <input type=hidden name=DB value=\"$DB\">\n";
					echo "      <input type=hidden name=lead_id value=\"$ld[lead_id]\">\n";
					echo "      <input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					echo "      New Callback Owner UserID: <input type=text name=CBuser size=8 maxlength=10 value=\"$rowx[8]\"> \n";
					echo "      <input type=submit name=submit value=\"CHANGE TO USERONLY CALLBACK\">\n";
                    echo "    </form><br>\n";
				}
			} else {
				echo "    <br>No Callback records found<br>\n";
			}
		}

	} else {
		echo "lead lookup FAILED for lead_id $ld[lead_id] &nbsp; &nbsp; &nbsp; $NOW_TIME\n<br><br>\n";
        exit;
	}
			
	echo "    <br>\n";
	echo "    <table width=550 cellspacing=0 cellpadding=1>\n";
	echo "      <tr class=tabheader>\n";
	echo "        <td colspan=9><font size='+1'>CALLS TO THIS LEAD</font></td>\n";
	echo "      </tr>\n";
	echo "      <tr class=tabheader>\n";
    echo "        <td># </td>\n";
    echo "        <td>DATE/TIME</td>\n";
    echo "        <td>LENGTH</td>\n";
    echo "        <td>STATUS</td>\n";
	echo "        <td>TSR</td>\n";
	echo "        <td>CAMPAIGN</td>\n";
	echo "        <td>LIST</td>\n";
	echo "        <td>LEAD</td>\n";
    echo "        <td>TERM REASON</td>\n";
	echo "      </tr>\n";
	echo "$call_log\n";
    echo "      <tr class=tabfooter>\n";
    echo "        <td colspan=9></td>\n";
    echo "      </tr>\n";
	echo "    </table>\n";
	echo "    <br><br>\n";
	echo "    <br><br>\n";
			
	echo "    <table width=650 cellspacing=0 cellpadding=1>\n";
	echo "      <tr class=tabheader>\n";
	echo "        <td colspan=9><font size='+1'>CLOSER RECORDS FOR THIS LEAD</font></td>\n";
	echo "      </tr>\n";
	echo "      <tr class=tabheader>\n";
    echo "        <td># </td>\n";
    echo "        <td>DATE/TIME</td>\n";
    echo "        <td>LENGTH</td>\n";
    echo "        <td>STATUS</td>\n";
    echo "        <td>TSR</td>\n";
    echo "        <td>CAMPAIGN</td>\n";
    echo "        <td>LIST</td>\n";
    echo "        <td>LEAD</td>\n";
    echo "        <td>WAIT</td>\n";
    echo "      </tr>\n";
	echo "$closer_log\n";
    echo "      <tr class=tabfooter>\n";
    echo "        <td colspan=9></td>\n";
    echo "      </tr>\n";
	echo "    </table>\n";
	echo "    <br><br>\n";
	echo "    <br><br>\n";
			
			
	echo "    <table width=750 cellspacing=0 cellpadding=1>\n";
	echo "      <tr class=tabheader>\n";
	echo "        <td colspan=11><font size='+1'>AGENT LOG RECORDS FOR THIS LEAD</font></td>\n";
	echo "      </tr>\n";
	echo "      <tr class=tabheader>\n";
    echo "        <td># </td>\n";
    echo "        <td>DATE/TIME</td>\n";
    echo "        <td>CAMPAIGN</td>\n";
    echo "        <td>TSR</td>\n";
    echo "        <td>PAUSE</td>\n";
    echo "        <td>WAIT</td>\n";
    echo "        <td>TALK</td>\n";
    echo "        <td>DISPO</td>\n";
    echo "        <td>STATUS</td>\n";
    echo "        <td>GROUP</td>\n";
    echo "        <td>SUB</td>\n";
    echo "      </tr>\n";
	echo "$agent_log\n";
    echo "      <tr class=tabfooter>\n";
    echo "        <td colspan=11></td>\n";
    echo "      </tr>\n";
	echo "    </table>\n";
	echo "    <br><br>\n";
	echo "    <br><br>\n";
			
	echo "    <table width=750 cellspacing=0 cellpadding=1>\n";
	echo "      <tr class=tabheader>\n";
	echo "        <td colspan=8><font size='+1'>RECORDINGS FOR THIS LEAD</font></td>\n";
	echo "      </tr>\n";
	echo "      <tr class=tabheader>\n";
    echo "        <td># </td>\n";
    echo "        <td>LEAD</td>\n";
    echo "        <td>DATE/TIME</td>\n";
    echo "        <td>SECONDS</td>\n";
    echo "        <td>RECID</td>\n";
    echo "        <td>FILENAME</td>\n";
    echo "        <td>LOCATION</td>\n";
    echo "        <td>TSR</td>\n";
    echo "      </tr>\n";
			
    $rlogs = get_krh($link, 'recording_log', '*', 'recording_id DESC', sprintf("lead_id='%s'",mres($ld[lead_id])), '500');
	$u=0;
	foreach ($rlogs as $rl) {
		if (eregi("1$|3$|5$|7$|9$", $u)) {
			$bgcolor='bgcolor=' . $oddrows; 
		} else {
			$bgcolor='bgcolor=' . $evenrows;
        }
			
		$location = $rl['location'];
		$locat = ellipse($location,27,true);
		if (eregi("http",$location) or eregi("^//", $location)) {
			$location = eregi_replace("^//","/",$location);
			$location = "<a href=\"$location\">$locat</a>";
		} else {
			$location = $locat;
		}
		$u++;
		echo "      <tr $bgcolor class=\"row font1\">\n";
		echo "        <td>$u</td>\n";
		echo "        <td align=left>" . $rl['lead_id'] . "</td>\n";
		echo "        <td align=left>" . $rl['starttime'] . "</td>\n";
		echo "        <td align=left>" . $rl['length_in_sec'] . "</td>\n";
		echo "        <td align=left>" . $rl['recording_id'] . "</td>\n";
		echo "        <td align=center>" . $rl['filename'] . "</td>\n";
		echo "        <td align=left>$location</td>\n";
		echo "        <td align=left><a href=\"admin.php?ADD=999999&SUB=21&agent=" . $rl['user'] . "\" target=\"_blank\">" . $rl['user'] . "</a></td>\n";
		echo "      </tr>\n";
	}
		
    echo "      <tr class=tabfooter>\n";
    echo "        <td colspan=8></td>\n";
    echo "      </tr>\n";
	echo "    </table><br><br><br>\n";
	echo "  </td>\n";
	echo "</tr>\n";
    echo "</table>\n";
}


$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds</font>";

echo "</body>\n";
?>
