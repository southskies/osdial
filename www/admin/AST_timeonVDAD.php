<? 
### AST_timeonVDAD.php
### 
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
###
# live real-time stats for the OSDIAL Auto-Dialer
#
# CHANGES
#
# 60620-1037 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 61114-2004 - Changed to display CLOSER and DEFAULT, added trunk shortage
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["reset_counter"]))			{$reset_counter=$_GET["reset_counter"];}
	elseif (isset($_POST["reset_counter"]))	{$reset_counter=$_POST["reset_counter"];}
if (isset($_GET["submit"]))					{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))					{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["closer_display"]))				{$closer_display=$_GET["closer_display"];}
	elseif (isset($_POST["closer_display"]))	{$closer_display=$_POST["closer_display"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

	$stmt="SELECT count(*) from osdial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"OSIDAL-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
$epochSIXhoursAGO = ($STARTtime - 21600);
$timeSIXhoursAGO = date("Y-m-d H:i:s",$epochSIXhoursAGO);

$reset_counter++;

if ($reset_counter > 7)
	{
	$reset_counter=0;

	$stmt="update park_log set status='HUNGUP' where hangup_time is not null;";
#	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}

	if ($DB)
		{	
		$stmt="delete from park_log where grab_time < '$timeSIXhoursAGO' and (hangup_time is null or hangup_time='');";
#		$rslt=mysql_query($stmt, $link);
		 echo "$stmt\n";
		}
	}

?>

<HTML>
<HEAD>
<?
echo "<STYLE type=\"text/css\">\n";
echo "<!--\n";

if ($closer_display>0)
{
	$stmt="select group_id,group_color from osdial_inbound_groups;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$groups_to_print = mysql_num_rows($rslt);
		if ($groups_to_print > 0)
		{
		$g=0;
		while ($g < $groups_to_print)
			{
			$row=mysql_fetch_row($rslt);
			$group_id[$g] = $row[0];
			$group_color[$g] = $row[1];
			echo "   .$group_id[$g] {color: black; background-color: $group_color[$g]}\n";
			$g++;
			}
		}
}
?>
   .DEAD       {color: white; background-color: black}
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
   .blue {color: white; background-color: blue}
   .purple {color: white; background-color: purple}
   .yellow {color: black; background-color: yellow}
-->
 </STYLE>

<? 
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"4; URL=$PHP_SELF?server_ip=$server_ip&DB=$DB&reset_counter=$reset_counter&closer_display=$closer_display\">\n";
echo "<TITLE>OSDIAL: Time On VDAD</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<PRE><FONT SIZE=3>";

###################################################################################
###### SERVER INFORMATION
###################################################################################

$stmt="select sum(local_trunk_shortage) from osdial_campaign_server_stats where server_ip='" . mysql_real_escape_string($server_ip) . "';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$balanceSHORT = $row[0];

echo "SERVER: $server_ip\n";



###################################################################################
###### TIME ON SYSTEM
###################################################################################

if ($closer_display>0) {$closer_display_reverse=0;   $closer_reverse_link='DEFAULT';}
else {$closer_display_reverse=1;   $closer_reverse_link='CLOSER';}

echo "OSDIAL: Agents Time On Calls           $NOW_TIME    <a href=\"$PHP_SELF?server_ip=$server_ip&DB=$DB&reset_counter=$reset_counter&closer_display=$closer_display_reverse\">$closer_reverse_link</a> | <a href=\"./admin.php?ADD=999999\">REPORTS</a>\n\n";

if ($closer_display>0)
{
echo "+------------|--------+-----------+---------------------+--------+----------+---------+--------------+--------+\n";
echo "| STATION    | USER   | SESSIONID | CHANNEL             | STATUS | CALLTIME | MINUTES | CAMPAIGN     | FRONT  |\n";
echo "+------------|--------+-----------+---------------------+--------+----------+---------+--------------+--------+\n";
}
else
{
echo "+------------|--------+-----------+---------------------+--------+----------+---------+\n";
echo "| STATION    | USER   | SESSIONID | CHANNEL             | STATUS | CALLTIME | MINUTES |\n";
echo "+------------|--------+-----------+---------------------+--------+----------+---------+\n";
}

$stmt="select extension,user,conf_exten,channel,status,last_call_time,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),uniqueid,lead_id from osdial_live_agents where status NOT IN('PAUSED') and server_ip='" . mysql_real_escape_string($server_ip) . "' order by extension;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0)
	{
	$i=0;
	while ($i < $talking_to_print)
		{
		$row=mysql_fetch_row($rslt);
			if (eregi("READY|PAUSED|CLOSER",$row[4]))
			{
			$row[3]='';
			$row[5]='- WAIT -';
			$row[6]=$row[7];
			}
		$extension[$i] = eregi_replace('Local/',"",$row[0]);
		$extension[$i] =		sprintf("%-10s", $extension[$i]);
			while(strlen($extension[$i])>10) {$extension[$i] = substr("$extension[$i]", 0, -1);}
		$user[$i] =				sprintf("%-6s", $row[1]);
		$sessionid[$i] =		sprintf("%-9s", $row[2]);
		$channel[$i] =			sprintf("%-19s", $row[3]);
			$cc[$i]=0;
		while ( (strlen($channel[$i]) > 19) and ($cc[$i] < 100) )
			{
			$channel[$i] = eregi_replace(".$","",$channel[$i]);   
			$cc[$i]++;
			if (strlen($channel[$i]) <= 19) {$cc[$i]=101;}
			}
		$status[$i] =			sprintf("%-6s", $row[4]);
		$start_time[$i] =		sprintf("%-8s", $row[5]);
			$cd[$i]=0;
		while ( (strlen($start_time[$i]) > 8) and ($cd[$i] < 100) )
			{
			$start_time[$i] = eregi_replace("^.","",$start_time[$i]);   
			$cd[$i]++;
			if (strlen($start_time[$i]) <= 8) {$cd[$i]=101;}
			}
		$uniqueid[$i] =			$row[8];
		$lead_id[$i] =			$row[9];
		$closer[$i] =			$row[1];
		$call_time_S[$i] = ($STARTtime - $row[6]);

		$call_time_M[$i] = ($call_time_S[$i] / 60);
		$call_time_M[$i] = round($call_time_M[$i], 2);
		$call_time_M_int[$i] = intval("$call_time_M[$i]");
		$call_time_SEC[$i] = ($call_time_M[$i] - $call_time_M_int[$i]);
		$call_time_SEC[$i] = ($call_time_SEC[$i] * 60);
		$call_time_SEC[$i] = round($call_time_SEC[$i], 0);
		if ($call_time_SEC[$i] < 10) {$call_time_SEC[$i] = "0$call_time_SEC[$i]";}
		$call_time_MS[$i] = "$call_time_M_int[$i]:$call_time_SEC[$i]";
		$call_time_MS[$i] =		sprintf("%7s", $call_time_MS[$i]);

		if ($closer_display<1)
			{
			$G = '';		$EG = '';
			if ($call_time_M_int[$i] >= 5) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
			if ($call_time_M_int[$i] >= 10) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
			if (eregi("PAUSED",$row[4])) 
				{
				if ($call_time_M_int >= 1) 
					{$i++; continue;} 
				else
					{$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';}
				}
			$agentcount++;
			echo "| $G$extension[$i]$EG | $G$user[$i]$EG | $G$sessionid[$i]$EG | $G$channel[$i]$EG | $G$status[$i]$EG | $G$start_time[$i]$EG | $G$call_time_MS[$i]$EG |\n";
			}
		$i++;
		}

		if ($closer_display>0)
		{

			$ext_count = $i;
			$i=0;
		while ($i < $ext_count)
			{

			$stmt="select campaign_id from osdial_auto_calls where lead_id='$lead_id[$i]' and server_ip='" . mysql_real_escape_string($server_ip) . "';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$camp_to_print = mysql_num_rows($rslt);
			if ($camp_to_print > 0)
				{
				$row=mysql_fetch_row($rslt);
				$campaign = sprintf("%-12s", $row[0]);
				$camp_color = $row[0];
				}
			else
				{$campaign = 'DEAD        ';   	$camp_color = 'DEAD';}
			if (eregi("READY|PAUSED|CLOSER",$status[$i]))
				{$campaign = '            ';   	$camp_color = '';}

			$stmt="select user from osdial_xfer_log where lead_id='$lead_id[$i]' and closer='$closer[$i]' order by call_date desc limit 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$xfer_to_print = mysql_num_rows($rslt);
			if ($xfer_to_print > 0)
				{
				$row=mysql_fetch_row($rslt);
				$fronter = sprintf("%-6s", $row[0]);
				}
			else
				{$fronter = '      ';}

			$G = '';		$EG = '';
			$G="<SPAN class=\"$camp_color\"><B>"; $EG='</B></SPAN>';
		#	if ($call_time_M_int[$i] >= 5) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
		#	if ($call_time_M_int[$i] >= 10) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}

			echo "| $G$extension[$i]$EG | $G$user[$i]$EG | $G$sessionid[$i]$EG | $G$channel[$i]$EG | $G$status[$i]$EG | $G$start_time[$i]$EG | $G$call_time_MS[$i]$EG | $G$campaign$EG | $G$fronter$EG |\n";

			$i++;
			}
		echo "+------------|--------+-----------+---------------------+--------+----------+---------+--------------+--------+\n";
		echo "  $i agents logged in on server $server_ip\n\n";
	#	echo "  <SPAN class=\"blue\"><B>          </SPAN> - 5 minutes or more on call</B>\n";
	#	echo "  <SPAN class=\"purple\"><B>          </SPAN> - Over 10 minutes on call</B>\n";
		}
	else
		{
		echo "+------------|--------+-----------+---------------------+--------+----------+---------+\n";
		echo "  $agentcount agents logged in on server $server_ip\n\n";

		echo "  <SPAN class=\"yellow\"><B>          </SPAN> - Paused agents</B>\n";
		echo "  <SPAN class=\"blue\"><B>          </SPAN> - 5 minutes or more on call</B>\n";
		echo "  <SPAN class=\"purple\"><B>          </SPAN> - Over 10 minutes on call</B>\n";
		}

	}
	else
	{
	echo "**************************************************************************************\n";
	echo "**************************************************************************************\n";
	echo "********************************* NO AGENTS ON CALLS *********************************\n";
	echo "**************************************************************************************\n";
	echo "**************************************************************************************\n";
	}


###################################################################################
###### OUTBOUND CALLS
###################################################################################
#echo "\n\n";
echo "----------------------------------------------------------------------------------------";
echo "\n\n";
echo "OSDIAL: Time On Dialer          TRUNK SHORT: $balanceSHORT              $NOW_TIME\n\n";
echo "+---------------------+--------+--------------+--------------------+----------+---------+\n";
echo "| CHANNEL             | STATUS | CAMPAIGN     | PHONE NUMBER       | CALLTIME | MINUTES |\n";
echo "+---------------------+--------+--------------+--------------------+----------+---------+\n";

$stmt="select channel,status,campaign_id,phone_code,phone_number,call_time,UNIX_TIMESTAMP(call_time) from osdial_auto_calls where status NOT IN('XFER') and server_ip='" . mysql_real_escape_string($server_ip) . "' order by auto_call_id desc;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0)
	{
	$i=0;
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);

		$channel =			sprintf("%-19s", $row[0]);
			$cc=0;
		while ( (strlen($channel) > 19) and ($cc < 100) )
			{
			$channel = eregi_replace(".$","",$channel);   
			$cc++;
			if (strlen($channel) <= 19) {$cc=101;}
			}
		$start_time =		sprintf("%-8s", $row[5]);
			$cd=0;
		while ( (strlen($start_time) > 8) and ($cd < 100) )
			{
			$start_time = eregi_replace("^.","",$start_time);   
			$cd++;
			if (strlen($start_time) <= 8) {$cd=101;}
			}
		$status =			sprintf("%-6s", $row[1]);
		$campaign =			sprintf("%-12s", $row[2]);
			$all_phone = "$row[3]$row[4]";
		$number_dialed =	sprintf("%-18s", $all_phone);
		$call_time_S = ($STARTtime - $row[6]);

		$call_time_M = ($call_time_S / 60);
		$call_time_M = round($call_time_M, 2);
		$call_time_M_int = intval("$call_time_M");
		$call_time_SEC = ($call_time_M - $call_time_M_int);
		$call_time_SEC = ($call_time_SEC * 60);
		$call_time_SEC = round($call_time_SEC, 0);
		if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
		$call_time_MS = "$call_time_M_int:$call_time_SEC";
		$call_time_MS =		sprintf("%7s", $call_time_MS);
		$G = '';		$EG = '';
		if (eregi("LIVE",$status)) {$G='<SPAN class="green"><B>'; $EG='</B></SPAN>';}
	#	if ($call_time_M_int >= 6) {$G='<SPAN class="red"><B>'; $EG='</B></SPAN>';}

		echo "| $G$channel$EG | $G$status$EG | $G$campaign$EG | $G$number_dialed$EG | $G$start_time$EG | $G$call_time_MS$EG |\n";

		$i++;
		}

		echo "+---------------------+--------+--------------+--------------------+----------+---------+\n";
		echo "  $i calls being placed on server $server_ip\n\n";

		echo "  <SPAN class=\"green\"><B>          </SPAN> - LIVE CALL WAITING</B>\n";
	#	echo "  <SPAN class=\"red\"><B>          </SPAN> - Over 5 minutes on hold</B>\n";

		}
	else
	{
	echo "***************************************************************************************\n";
	echo "***************************************************************************************\n";
	echo "******************************* NO LIVE CALLS WAITING *********************************\n";
	echo "***************************************************************************************\n";
	echo "***************************************************************************************\n";
	}


?>
</PRE>

</BODY></HTML>
