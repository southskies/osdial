<?php
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
# 090410-1136 - Added custom2 field
# 090410-1541 - Added external_key field




######################
# ADD=411111111111111 modify osdial system settings
######################

if ($ADD==411111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<br><font color=$default_text>$t1 SYSTEM SETTINGS MODIFIED</font>\n";

	$stmt="UPDATE system_settings set use_non_latin='$use_non_latin',webroot_writable='$webroot_writable',enable_queuemetrics_logging='$enable_queuemetrics_logging',queuemetrics_server_ip='$queuemetrics_server_ip',queuemetrics_dbname='$queuemetrics_dbname',queuemetrics_login='$queuemetrics_login',queuemetrics_pass='$queuemetrics_pass',queuemetrics_url='$queuemetrics_url',queuemetrics_log_id='$queuemetrics_log_id',queuemetrics_eq_prepend='$queuemetrics_eq_prepend',osdial_agent_disable='$osdial_agent_disable',allow_sipsak_messages='$allow_sipsak_messages',admin_home_url='$admin_home_url',enable_agc_xfer_log='$enable_agc_xfer_log',company_name='$company_name',admin_template='$admin_template',agent_template='$agent_template';";
	$rslt=mysql_query($stmt, $link);

	### LOG CHANGES TO LOG FILE ###
	if ($WeBRooTWritablE > 0)
		{
		$fp = fopen ("./admin_changes_log.txt", "a");
		fwrite ($fp, "$date|MODIFY SYSTEM SETTINGS|$PHP_AUTH_USER|$ip|$stmt|\n");
		fclose($fp);
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111111111;	# go to osdial system settings form below
}


######################
# ADD=311111111111111 modify osdial system settings
######################

if ($ADD==311111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$stmt="SELECT version,install_date,use_non_latin,webroot_writable,enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_url,queuemetrics_log_id,queuemetrics_eq_prepend,osdial_agent_disable,allow_sipsak_messages,admin_home_url,enable_agc_xfer_log,company_name,admin_template,agent_template from system_settings;";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$version =						$row[0];
	$install_date =					$row[1];
	$use_non_latin =				$row[2];
	$webroot_writable =				$row[3];
	$enable_queuemetrics_logging =	$row[4];
	$queuemetrics_server_ip =		$row[5];
	$queuemetrics_dbname =			$row[6];
	$queuemetrics_login =			$row[7];
	$queuemetrics_pass =			$row[8];
	$queuemetrics_url =				$row[9];
	$queuemetrics_log_id =			$row[10];
	$queuemetrics_eq_prepend =		$row[11];
	$osdial_agent_disable =		    $row[12];
	$allow_sipsak_messages =		$row[13];
	$admin_home_url =				$row[14];
	$enable_agc_xfer_log =			$row[15];
	$company_name =			        $row[16];
	$admin_template =			        $row[17];
	$agent_template =			        $row[18];

	echo "<center><br><font color=$default_text size=+1>MODIFY $t1 SYSTEM SETTINGS</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411111111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Version: </td><td align=left> $version</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Install Date: </td><td align=left> $install_date</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Company Name: </td><td align=left><input type=text name=company_name size=30 maxlength=100 value=\"$company_name\"></td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Use Non-Latin: </td><td align=left><select size=1 name=use_non_latin><option>1</option><option>0</option><option selected>$use_non_latin</option></select>$NWB#settings-use_non_latin$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Webroot Writable: </td><td align=left><select size=1 name=webroot_writable><option>1</option><option>0</option><option selected>$webroot_writable</option></select>$NWB#settings-webroot_writable$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Enable QueueMetrics Logging: </td><td align=left><select size=1 name=enable_queuemetrics_logging><option>1</option><option>0</option><option selected>$enable_queuemetrics_logging</option></select>$NWB#settings-enable_queuemetrics_logging$NWE</td></tr>\n";

	echo "<tr bgcolor=$oddrows><td align=right>QueueMetrics Server IP: </td><td align=left><input type=text name=queuemetrics_server_ip size=18 maxlength=15 value=\"$queuemetrics_server_ip\">$NWB#settings-queuemetrics_server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>QueueMetrics DB Name: </td><td align=left><input type=text name=queuemetrics_dbname size=18 maxlength=50 value=\"$queuemetrics_dbname\">$NWB#settings-queuemetrics_dbname$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>QueueMetrics DB Login: </td><td align=left><input type=text name=queuemetrics_login size=18 maxlength=50 value=\"$queuemetrics_login\">$NWB#settings-queuemetrics_login$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>QueueMetrics DB Password: </td><td align=left><input type=text name=queuemetrics_pass size=18 maxlength=50 value=\"$queuemetrics_pass\">$NWB#settings-queuemetrics_pass$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>QueueMetrics URL: </td><td align=left><input type=text name=queuemetrics_url size=50 maxlength=255 value=\"$queuemetrics_url\">$NWB#settings-queuemetrics_url$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>QueueMetrics Log ID: </td><td align=left><input type=text name=queuemetrics_log_id size=12 maxlength=10 value=\"$queuemetrics_log_id\">$NWB#settings-queuemetrics_log_id$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>QueueMetrics EnterQueue Prepend: </td><td align=left><select size=1 name=queuemetrics_eq_prepend>\n";
	echo "<option value=\"NONE\">NONE</option>\n";
	echo "<option value=\"lead_id\">lead_id</option>\n";
	echo "<option value=\"list_id\">list_id</option>\n";
	echo "<option value=\"source_id\">source_id</option>\n";
	echo "<option value=\"vendor_lead_code\">vendor_lead_code</option>\n";
	echo "<option value=\"address3\">address3</option>\n";
	echo "<option value=\"custom1\">custom1</option>\n";
	echo "<option value=\"custom2\">custom2</option>\n";
	echo "<option value=\"external_key\">external_key</option>\n";
	echo "<option selected value=\"$queuemetrics_eq_prepend\">$queuemetrics_eq_prepend</option>\n";
	echo "</select>$NWB#settings-queuemetrics_eq_prepend$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>$t1 Agent Disable Display: </td><td align=left><select size=1 name=osdial_agent_disable>\n";
	echo "<option value=\"NOT_ACTIVE\">NOT_ACTIVE</option>\n";
	echo "<option value=\"LIVE_AGENT\">LIVE_AGENT</option>\n";
	echo "<option value=\"EXTERNAL\">EXTERNAL</option>\n";
	echo "<option value=\"ALL\">ALL</option>\n";
	echo "<option selected value=\"$osdial_agent_disable\">$osdial_agent_disable</option>\n";
	echo "</select>$NWB#settings-osdial_agent_disable$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Allow SIPSAK Messages: </td><td align=left><select size=1 name=allow_sipsak_messages><option>1</option><option>0</option><option selected>$allow_sipsak_messages</option></select>$NWB#settings-allow_sipsak_messages$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Admin Home URL: </td><td align=left><input type=text name=admin_home_url size=50 maxlength=255 value=\"$admin_home_url\">$NWB#settings-admin_home_url$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Enable Agent Transfer Logfile: </td><td align=left><select size=1 name=enable_agc_xfer_log><option>1</option><option>0</option><option selected>$enable_agc_xfer_log</option></select>$NWB#settings-enable_agc_xfer_log$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Admin Template: </td><td align=left>";
	echo "  <select size=1 name=admin_template>";
	$dir_handle = opendir($WeBServeRRooT . "/admin/templates");
	while ($file = readdir($dir_handle)) {
		if($file!="." && $file!="..")
			echo "<option>$file</option>";
	}
	echo "    <option selected>$admin_template</option>";
	echo "  </select>";
	echo "$NWB#settings-admin_template$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Agent Template: </td><td align=left>";
	echo "  <select size=1 name=agent_template>";
	$dir_handle = opendir($WeBServeRRooT . "/agent/templates");
	while ($file = readdir($dir_handle)) {
		if($file!="." && $file!="..")
			echo "<option>$file</option>";
	}
	echo "    <option selected>$agent_template</option>";
	echo "  </select>";
	echo "$NWB#settings-agent_template$NWE</td></tr>\n";


	echo "<tr bgcolor=$menubarcolor><td align=center colspan=2><input style=\"width: 100%;\" type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "</form>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


?>
