<?php
# AST_timeonVDAD.php
# 
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# live real-time stats for the ODDIAL Auto-Dialer
#
# CHANGES
#
# 60620-1037 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 61114-2004 - Changed to display CLOSER and DEFAULT, added trunk shortage
# 80422-0305 - Added phone login to display, lower font size to 2
# 81013-2227 - Fixed Remote Agent display bug
# 90310-1945 - Admin header
#
session_start();

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER='';
$PHP_AUTH_PW='';
if ($config['settings']['use_old_admin_auth']) {
    if (isset($_SERVER['PHP_AUTH_USER'])) $PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
    if (isset($_SERVER['PHP_AUTH_PW'])) $PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
} else {
    if (isset($_SESSION[KEY]['valid'])) {
        $_SESSION[KEY]['last_update'] = time();
        if (isset($_SESSION[KEY]['PHP_AUTH_USER'])) $PHP_AUTH_USER=$_SESSION[KEY]['PHP_AUTH_USER'];
        if (isset($_SESSION[KEY]['PHP_AUTH_PW'])) $PHP_AUTH_PW=$_SESSION[KEY]['PHP_AUTH_PW'];
    }
    if (empty($PHP_AUTH_USER)) $PHP_AUTH_USER=get_variable('PHP_AUTH_USER');
    if (empty($PHP_AUTH_PW)) $PHP_AUTH_PW=get_variable('PHP_AUTH_PW');
}
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

	$stmt=sprintf("SELECT count(*) FROM osdial_users WHERE user='%s' AND pass='%s' AND user_level>6 AND view_reports='1';",mysql_real_escape_string($PHP_AUTH_USER),mysql_real_escape_string($PHP_AUTH_PW));
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    if ($config['settings']['use_old_admin_auth']) {
        Header("WWW-Authenticate: Basic realm=\"OSDIAL\"");
        Header("HTTP/1.0 401 Unauthorized");
    }
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
<?php
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

<?php

$LNtopleft    ="&#x2554;";
$LNleft       ="&#x2551;";
$LNright      ="&#x2551;";
$LNcenterleft ="&#x255F;";
$LNcenterbar  ="&#x2502;";
$LNtopdown    ="&#x2564;";
$LNtopright   ="&#x2557;";
$LNbottomleft ="&#x255A;";
$LNbottomright="&#x255D;";
$LNcentcross  ="&#x253C;";
$LNcentright  ="&#x2562;";
$LNbottomup   ="&#x2567;";
$LNhoriz      ="&#x2550;";
$BKbottomhalf ="&#x2584;";

echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"4; URL=$PHP_SELF?server_ip=$server_ip&DB=$DB&reset_counter=$reset_counter&closer_display=$closer_display\">\n";
echo "<TITLE>OSDIAL: Time On AUTO CALLS</TITLE></HEAD><BODY marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";

$short_header=1;

echo "<div align=center>";

echo "<TABLE CELLPADDING=4 CELLSPACING=0><TR><TD>";
echo "<PRE><FONT SIZE=2>";

###################################################################################
###### SERVER INFORMATION
###################################################################################

$stmt="select sum(local_trunk_shortage) from osdial_campaign_server_stats where server_ip='" . mysql_real_escape_string($server_ip) . "';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$balanceSHORT = $row[0];

echo "<br/> SERVER: $server_ip\n";



###################################################################################
###### TIME ON SYSTEM
###################################################################################

if ($closer_display>0) {
	$closer_display_reverse=0;   $closer_reverse_link='DEFAULT';
} else {
	$closer_display_reverse=1;   $closer_reverse_link='CLOSER';
}

echo "<div align=center>OSDIAL: Agents Time On Calls           $NOW_TIME    <a href=\"$PHP_SELF?server_ip=$server_ip&DB=$DB&reset_counter=$reset_counter&closer_display=$closer_display_reverse\">$closer_reverse_link</a></div>\n";

if ($closer_display>0) {
// 	echo "+------------+------------+----------------------+-----------+---------------------+--------+----------+---------+------------------------+-------------+\n";
// 	echo "| STATION    |   PHONE    | USER                 | SESSIONID | CHANNEL             | STATUS | CALLTIME | MINUTES | CAMPAIGN               | FRONT       |\n";
// 	echo "+------------+------------+----------------------+-----------+---------------------+--------+----------+---------+------------------------+-------------+\n";
	echo "$LNtopleft";
	echo str_repeat($LNhoriz,12); // station
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,12); // phone
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,22); // user
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,11); // sessionid
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,21); // channel
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,8); // status
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,10); // calltime
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,9); // minutes
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,24); // campaign
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,13); // front
	echo "$LNtopright<br/>";
	echo "$LNright  STATION   $LNcenterbar   PHONE    $LNcenterbar         USER         $LNcenterbar SESSIONID $LNcenterbar       CHANNEL       $LNcenterbar STATUS $LNcenterbar CALLTIME $LNcenterbar MINUTES $LNcenterbar       CAMPAIGN         $LNcenterbar    FRONT    $LNleft<br/>";
	
	echo "$LNbottomleft";
	echo str_repeat($LNhoriz,12);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,12);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,22);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,11);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,21);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,8);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,10);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,9);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,24);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,13);
	echo "$LNbottomright<br />";
} else {
// 	echo "+------------+------------+----------------------+-----------+---------------------+--------+----------+---------+\n";
// 	echo "| STATION    | PHONE      | USER                 | SESSIONID | CHANNEL             | STATUS | CALLTIME | MINUTES |\n";
// 	echo "+------------+------------+----------------------+-----------+---------------------+--------+----------+---------+\n";
	echo "$LNtopleft";
	echo str_repeat($LNhoriz,12); // station
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,12); // phone
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,22); // user
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,11); // sessionid
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,21); // channel
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,8); // status
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,10); // calltime
	echo "$LNtopdown";
	echo str_repeat($LNhoriz,9); // minutes
	echo "$LNtopright<br/>";
	echo "$LNright  STATION   $LNcenterbar   PHONE    $LNcenterbar         USER         $LNcenterbar SESSIONID $LNcenterbar       CHANNEL       $LNcenterbar STATUS $LNcenterbar CALLTIME $LNcenterbar MINUTES $LNleft<br/>";
	// echo "$LNright  CHANNEL   $LNcenterbar      GROUP      $LNcenterbar      START TIME     $LNcenterbar MINUTES $LNleft<br/>";
	echo "$LNbottomleft";
	echo str_repeat($LNhoriz,12);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,12);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,22);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,11);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,21);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,8);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,10);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,9);
	echo "$LNbottomright<br />";
}

$stmt="select extension,user,conf_exten,channel,status,last_call_time,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),uniqueid,lead_id from osdial_live_agents where status NOT IN('PAUSED') and server_ip='" . mysql_real_escape_string($server_ip) . "' order by extension;";
$rslt=mysql_query($stmt, $link);
if ($DB) {
	echo "$stmt\n";
}
$talking_to_print = mysql_num_rows($rslt);
if ($talking_to_print > 0) {
	$i=0;
	while ($i < $talking_to_print) {
		$row=mysql_fetch_row($rslt);
		$Sextension[$i] =		$row[0];
		$Suser[$i] =			$row[1];
		$Ssessionid[$i] =		$row[2];
		$Schannel[$i] =			$row[3];
		$Sstatus[$i] =			$row[4];
		$Sstart_time[$i] =		$row[5];
		$Scall_time[$i] =		$row[6];
		$Sfinish_time[$i] =		$row[7];
		$Suniqueid[$i] =		$row[8];
		$Slead_id[$i] =			$row[9];
		$i++;
	}

	$i=0;
	while ($i < $talking_to_print) {
		$phone[$i]='          ';
		if (preg_match("/R\//",$Sextension[$i])) {
			$protocol = 'EXTERNAL';
			$dialplan = preg_replace('/R\//',"",$Sextension[$i]);
			$dialplan = preg_replace("/\@.*/",'',$dialplan);
			$exten = "dialplan_number='$dialplan'";
		}
		if (preg_match("/Local\//",$Sextension[$i])) {
			$protocol = 'EXTERNAL';
			$dialplan = preg_replace('/Local\//',"",$Sextension[$i]);
			$dialplan = preg_replace("/\@.*/",'',$dialplan);
			$exten = "dialplan_number='$dialplan'";
		}
		if (preg_match('SIP\//',$Sextension[$i])) {
			$protocol = 'SIP';
			$dialplan = preg_replace('/SIP\//',"",$Sextension[$i]);
			$dialplan = preg_replace("/-.*/",'',$dialplan);
			$exten = "extension='$dialplan'";
		}
		if (preg_match('/IAX2\//',$Sextension[$i])) {
			$protocol = 'IAX2';
			$dialplan = preg_replace('/IAX2\//',"",$Sextension[$i]);
			$dialplan = preg_replace("/-.*/",'',$dialplan);
			$exten = "extension='$dialplan'";
		}
		if (preg_match('/Zap\//',$Sextension[$i])) {
			$protocol = 'Zap';
			$dialplan = preg_replace('/Zap\//',"",$Sextension[$i]);
			$exten = "extension='$dialplan'";
		}

		$stmt="select login from phones where server_ip='" . mysql_real_escape_string($server_ip) . "' and $exten and protocol='$protocol';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$login = $row[0];

		$phone[$i] =			sprintf("%-10s", $login);

		if (preg_match("/READY|PAUSED|CLOSER/",$Sstatus[$i])) {
			$Schannel[$i]='';
			$Sstart_time[$i]='- WAIT -';
			$Scall_time[$i]=$Sfinish_time[$i];
		}
		$extension[$i] = preg_replace('/Local\//',"",$Sextension[$i]);
		$extension[$i] =		sprintf("%-10s", $extension[$i]);
		while(strlen($extension[$i])>10) {
			$extension[$i] = substr("$extension[$i]", 0, -1);
		}
		$user[$i] =				sprintf("%-20s", $Suser[$i]);
		$sessionid[$i] =		sprintf("%-9s", $Ssessionid[$i]);
		$channel[$i] =			sprintf("%-19s", $Schannel[$i]);
		$cc[$i]=0;
		while ((strlen($channel[$i]) > 19) and ($cc[$i] < 100)) {
			$channel[$i] = preg_replace("/.$/","",$channel[$i]);   
			$cc[$i]++;
			if (strlen($channel[$i]) <= 19) {
				$cc[$i]=101;
			}
		}
		$status[$i] =			sprintf("%-6s", $Sstatus[$i]);
		$start_time[$i] =		sprintf("%-8s", $Sstart_time[$i]);
		$cd[$i]=0;
		while ((strlen($start_time[$i]) > 8) and ($cd[$i] < 100)) {
			$start_time[$i] = preg_replace("/^./","",$start_time[$i]);   
			$cd[$i]++;
			if (strlen($start_time[$i]) <= 8) {
				$cd[$i]=101;
			}
		}
		$uniqueid[$i] =			$Suniqueid[$i];
		$lead_id[$i] =			$Slead_id[$i];
		$closer[$i] =			$Suser[$i];
		$call_time_S[$i] = ($STARTtime - $Scall_time[$i]);

		$call_time_M[$i] = ($call_time_S[$i] / 60);
		$call_time_M[$i] = round($call_time_M[$i], 2);
		$call_time_M_int[$i] = intval("$call_time_M[$i]");
		$call_time_SEC[$i] = ($call_time_M[$i] - $call_time_M_int[$i]);
		$call_time_SEC[$i] = ($call_time_SEC[$i] * 60);
		$call_time_SEC[$i] = round($call_time_SEC[$i], 0);
		if ($call_time_SEC[$i] < 10) {$call_time_SEC[$i] = "0$call_time_SEC[$i]";}
		$call_time_MS[$i] = "$call_time_M_int[$i]:$call_time_SEC[$i]";
		$call_time_MS[$i] =		sprintf("%7s", $call_time_MS[$i]);

		if ($closer_display<1) {
			$G = '';		$EG = '';
			if ($call_time_M_int[$i] >= 5) {
				$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';
			}
			if ($call_time_M_int[$i] >= 10) {
				$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';
			}
			if (preg_match("/PAUSED/",$Sstatus[$i])) {
				if ($call_time_M_int >= 1) {
					$i++; continue;
				} else {
					$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';
				}
			}
			$agentcount++;
			echo "$LNright $G$extension[$i]$EG $LNcenterbar $G$phone[$i]$EG $LNcenterbar $G$user[$i]$EG $LNcenterbar $G$sessionid[$i]$EG $LNcenterbar $G$channel[$i]$EG $LNcenterbar $G$status[$i]$EG $LNcenterbar $G$start_time[$i]$EG $LNcenterbar $G$call_time_MS[$i]$EG $LNleft\n";
		}
		$i++;
	}

	if ($closer_display>0) {
		$ext_count = $i;
		$i=0;
		while ($i < $ext_count) {

			$stmt="select campaign_id from osdial_auto_calls where lead_id='$lead_id[$i]' and server_ip='" . mysql_real_escape_string($server_ip) . "';";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {
				echo "$stmt\n";
			}
			$camp_to_print = mysql_num_rows($rslt);
			if ($camp_to_print > 0) {
				$row=mysql_fetch_row($rslt);
				$campaign = sprintf("%-22s", $row[0]);
				$camp_color = $row[0];
			} else {
				$campaign = 'DEAD                  ';   	$camp_color = 'DEAD';
			}
			if (preg_match("/READY|PAUSED|CLOSER/",$status[$i])) {
				$campaign = '                      ';   	$camp_color = '';
			}

			$stmt="select user from osdial_xfer_log where lead_id='$lead_id[$i]' and closer='$closer[$i]' order by call_date desc limit 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {
				echo "$stmt\n";
			}
			$xfer_to_print = mysql_num_rows($rslt);
			if ($xfer_to_print > 0) {
				$row=mysql_fetch_row($rslt);
				$fronter = sprintf("%-9s", $row[0]);
			} else {
				$fronter = '           ';
			}

			$G = '';		$EG = '';
			$G="<SPAN class=\"$camp_color\"><B>"; $EG='</B></SPAN>';
		#	if ($call_time_M_int[$i] >= 5) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
		#	if ($call_time_M_int[$i] >= 10) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}

			echo "$LNright $G$extension[$i]$EG $LNcenterbar $G$phone[$i]$EG $LNcenterbar $G$user[$i]$EG $LNcenterbar $G$sessionid[$i]$EG $LNcenterbar $G$channel[$i]$EG $LNcenterbar $G$status[$i]$EG $LNcenterbar $G$start_time[$i]$EG $LNcenterbar $G$call_time_MS[$i]$EG $LNcenterbar $G$campaign$EG $LNcenterbar $G$fronter$EG $LNleft \n";

			$i++;
		}
// 		echo "+------------+------------+----------------------+-----------+---------------------+--------+----------+---------+------------------------+-------------+\n";
		echo "$LNbottomleft";
		echo str_repeat($LNhoriz,12);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,12);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,22);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,11);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,21);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,8);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,10);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,9);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,24);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,13);
		echo "$LNbottomright<br />";
		echo "$i agents logged in on server $server_ip\n\n";
// 		echo "  <SPAN class=\"blue\"><B>          </SPAN> - 5 minutes or more on call</B>\n";
// 		echo "  <SPAN class=\"purple\"><B>          </SPAN> - Over 10 minutes on call</B>\n";
	} else {
// 		echo "+------------+------------+--------+-----------+---------------------+--------+----------+---------+\n";
		echo "$LNbottomleft";
		echo str_repeat($LNhoriz,12);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,12);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,22);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,11);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,21);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,8);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,10);
		echo "$LNbottomup";
		echo str_repeat($LNhoriz,9);
		echo "$LNbottomright<br />";
		echo "  $agentcount agents logged in on server $server_ip\n\n";

		echo "  <SPAN class=\"yellow\"><B>          </SPAN> - Paused agents</B>\n";
		echo "  <SPAN class=\"blue\"><B>          </SPAN> - 5 minutes or more on call</B>\n";
		echo "  <SPAN class=\"purple\"><B>          </SPAN> - Over 10 minutes on call</B>\n";
	}

} else {
	echo "<div align=center>";
	echo "**************************************************************************************\n";
	echo "********************************                      ********************************\n";
	echo "********************************  NO AGENTS ON CALLS  ********************************\n";
	echo "********************************                      ********************************\n";
	echo "**************************************************************************************\n";
	echo "</div>";
}


###################################################################################
###### OUTBOUND CALLS
###################################################################################
#echo "\n\n";
// echo "--------------------------------------------------------------------------------------------------";

echo "<div align=center>";
echo "\n\n\n";
echo "OSDIAL: Time On VDAD            TRUNK SHORT: $balanceSHORT              $NOW_TIME\n\n";
// echo "+---------------------+--------+------------------------+--------------------+----------+---------+\n";
// echo "| CHANNEL             | STATUS | CAMPAIGN               | PHONE NUMBER       | CALLTIME | MINUTES |\n";
// echo "+---------------------+--------+------------------------+--------------------+----------+---------+\n";

echo "$LNtopleft";
echo str_repeat($LNhoriz,21);
echo "$LNtopdown";
echo str_repeat($LNhoriz,8);
echo "$LNtopdown";
echo str_repeat($LNhoriz,24);
echo "$LNtopdown";
echo str_repeat($LNhoriz,20);
echo "$LNtopdown";
echo str_repeat($LNhoriz,10);
echo "$LNtopdown";
echo str_repeat($LNhoriz,9);
echo "$LNtopright<br/>";
echo "$LNright       CHANNEL       $LNcenterbar STATUS $LNcenterbar        CAMPAIGN        $LNcenterbar    PHONE NUMBER    $LNcenterbar CALLTIME $LNcenterbar MINUTES $LNleft<br/>";
echo "$LNbottomleft";
echo str_repeat($LNhoriz,21);
echo "$LNbottomup";
echo str_repeat($LNhoriz,8);
echo "$LNbottomup";
echo str_repeat($LNhoriz,24);
echo "$LNbottomup";
echo str_repeat($LNhoriz,20);
echo "$LNbottomup";
echo str_repeat($LNhoriz,10);
echo "$LNbottomup";
echo str_repeat($LNhoriz,9);
echo "$LNbottomright<br />";

$stmt="select channel,status,campaign_id,phone_code,phone_number,call_time,UNIX_TIMESTAMP(call_time) from osdial_auto_calls where status NOT IN('XFER') and server_ip='" . mysql_real_escape_string($server_ip) . "' order by auto_call_id desc;";
$rslt=mysql_query($stmt, $link);
if ($DB) {
	echo "$stmt\n";
}
$parked_to_print = mysql_num_rows($rslt);
if ($parked_to_print > 0) {
	$i=0;
	while ($i < $parked_to_print) {
		$row=mysql_fetch_row($rslt);

		$channel =			sprintf("%-19s", $row[0]);
			$cc=0;
		while ((strlen($channel) > 19) and ($cc < 100)) {
			$channel = preg_replace("/.$/","",$channel);   
			$cc++;
			if (strlen($channel) <= 19) {
				$cc=101;
			}
		}
		$start_time =		sprintf("%-8s", $row[5]);
		$cd=0;
		while ((strlen($start_time) > 8) and ($cd < 100)) {
			$start_time = preg_replace("/^./","",$start_time);   
			$cd++;
			if (strlen($start_time) <= 8) {
				$cd=101;
			}
		}
		$status =			sprintf("%-6s", $row[1]);
		$campaign =			sprintf("%-22s", $row[2]);
		$all_phone = "$row[3]$row[4]";
		$number_dialed =	sprintf("%-18s", $all_phone);
		$call_time_S = ($STARTtime - $row[6]);

		$call_time_M = ($call_time_S / 60);
		$call_time_M = round($call_time_M, 2);
		$call_time_M_int = intval("$call_time_M");
		$call_time_SEC = ($call_time_M - $call_time_M_int);
		$call_time_SEC = ($call_time_SEC * 60);
		$call_time_SEC = round($call_time_SEC, 0);
		if ($call_time_SEC < 10) {
			$call_time_SEC = "0$call_time_SEC";
		}
		$call_time_MS = "$call_time_M_int:$call_time_SEC";
		$call_time_MS =		sprintf("%7s", $call_time_MS);
		$G = '';		$EG = '';
		if (preg_match("/LIVE/",$status)) {
			$G='<SPAN class="green"><B>'; $EG='</B></SPAN>';
		}
	#	if ($call_time_M_int >= 6) {$G='<SPAN class="red"><B>'; $EG='</B></SPAN>';}

		echo "$LNright $G$channel$EG $LNcenterbar $G$status$EG $LNcenterbar $G$campaign$EG $LNcenterbar $G$number_dialed$EG $LNcenterbar $G$start_time$EG $LNcenterbar $G$call_time_MS$EG $LNleft\n";

		$i++;
	}

// 	echo "+---------------------+--------+------------------------+--------------------+----------+---------+\n";
	echo "$LNbottomleft";
	echo str_repeat($LNhoriz,12);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,12);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,22);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,11);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,21);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,8);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,10);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,9);
	echo "$LNbottomright<br />";
	echo "$LNbottomleft";
	echo str_repeat($LNhoriz,21);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,8);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,24);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,20);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,10);
	echo "$LNbottomup";
	echo str_repeat($LNhoriz,9);
	echo "$LNbottomright<br />";
	echo "$i calls being placed on server $server_ip\n\n";

	echo "  <SPAN class=\"green\"><B>          </SPAN> - LIVE CALL WAITING</B>\n";
// 	echo "  <SPAN class=\"red\"><B>          </SPAN> - Over 5 minutes on hold</B>\n";

} else {
	echo "**************************************************************************************\n";
	echo "*******************************                         ******************************\n";
	echo "*******************************  NO LIVE CALLS WAITING  ******************************\n";
	echo "*******************************                         ******************************\n";
	echo "**************************************************************************************\n";
	echo "<br/>";
}

echo "</PRE>";
echo "</TD></TR></TABLE>";

echo "</div>";
echo "</BODY></HTML>";

?>

