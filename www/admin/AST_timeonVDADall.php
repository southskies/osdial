<? 
# AST_timeonVDADall.php
# 
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# live real-time stats for the VICIDIAL Auto-Dialer all servers
#
# STOP=4000, SLOW=40, GO=4 seconds refresh interval
# 
# CHANGELOG:
# 50406-0920 - Added Paused agents < 1 min (Chris Doyle)
# 51130-1218 - Modified layout and info to show all servers in a vicidial system
# 60421-1043 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60511-1343 - Added leads and drop info at the top of the screen
# 60608-1539 - Fixed CLOSER tallies for active calls
# 60619-1658 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 60626-1453 - Added display of system load to bottom (Angelito Manansala)
# 60901-1123 - Changed display elements at the top of the screen
# 60905-1342 - Fixed non INCALL|QUEUE timer column
# 61002-1642 - Added TRUNK SHORT/FILL stats
# 61101-1318 - Added SIP and IAX Listen and Barge links option
# 61101-1647 - Added Usergroup column and user name option as well as sorting
# 61102-1155 - Made display of columns more modular, added ability to hide server info
# 61215-1131 - Added answered calls and drop percent taken from answered calls
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70123-1151 - Added non_latin options for substr in display variables, thanks Marin Blu
# 70206-1140 - Added call-type statuses to display(A-Auto, M-Manual, I-Inbound/Closer)
# 70619-1339 - Added Status Category tally display
# 71029-1900 - Changed CLOSER-type to not require campaign_id restriction
# 80424-0515 - Added non_latin lookup from system_settings
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"]))			{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))	{$server_ip=$_POST["server_ip"];}
if (isset($_GET["RR"]))					{$RR=$_GET["RR"];}
	elseif (isset($_POST["RR"]))		{$RR=$_POST["RR"];}
if (isset($_GET["inbound"]))				{$inbound=$_GET["inbound"];}
	elseif (isset($_POST["inbound"]))		{$inbound=$_POST["inbound"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["usergroup"]))			{$usergroup=$_GET["usergroup"];}
	elseif (isset($_POST["usergroup"]))	{$usergroup=$_POST["usergroup"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["adastats"]))			{$adastats=$_GET["adastats"];}
	elseif (isset($_POST["adastats"]))	{$adastats=$_POST["adastats"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}
if (isset($_GET["SIPmonitorLINK"]))				{$SIPmonitorLINK=$_GET["SIPmonitorLINK"];}
	elseif (isset($_POST["SIPmonitorLINK"]))	{$SIPmonitorLINK=$_POST["SIPmonitorLINK"];}
if (isset($_GET["IAXmonitorLINK"]))				{$IAXmonitorLINK=$_GET["IAXmonitorLINK"];}
	elseif (isset($_POST["IAXmonitorLINK"]))	{$IAXmonitorLINK=$_POST["IAXmonitorLINK"];}
if (isset($_GET["UGdisplay"]))			{$UGdisplay=$_GET["UGdisplay"];}
	elseif (isset($_POST["UGdisplay"]))	{$UGdisplay=$_POST["UGdisplay"];}
if (isset($_GET["UidORname"]))			{$UidORname=$_GET["UidORname"];}
	elseif (isset($_POST["UidORname"]))	{$UidORname=$_POST["UidORname"];}
if (isset($_GET["orderby"]))			{$orderby=$_GET["orderby"];}
	elseif (isset($_POST["orderby"]))	{$orderby=$_POST["orderby"];}
if (isset($_GET["SERVdisplay"]))			{$SERVdisplay=$_GET["SERVdisplay"];}
	elseif (isset($_POST["SERVdisplay"]))	{$SERVdisplay=$_POST["SERVdisplay"];}
if (isset($_GET["CALLSdisplay"]))			{$CALLSdisplay=$_GET["CALLSdisplay"];}
	elseif (isset($_POST["CALLSdisplay"]))	{$CALLSdisplay=$_POST["CALLSdisplay"];}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################


if (!isset($RR))			{$gRRroup=4;}
if (!isset($group))			{$group='';}
if (!isset($usergroup))		{$usergroup='';}
if (!isset($UGdisplay))		{$UGdisplay=0;}	# 0=no, 1=yes
if (!isset($UidORname))		{$UidORname=0;}	# 0=id, 1=name
if (!isset($orderby))		{$orderby='timeup';}
if (!isset($SERVdisplay))	{$SERVdisplay=1;}	# 0=no, 1=yes
if (!isset($CALLSdisplay))	{$CALLSdisplay=1;}	# 0=no, 1=yes


function get_server_load($windows = false) {
$os = strtolower(PHP_OS);
if(strpos($os, "win") === false) {
if(file_exists("/proc/loadavg")) {
$load = file_get_contents("/proc/loadavg");
$load = explode(' ', $load);
return $load[0];
}
elseif(function_exists("shell_exec")) {
$load = explode(' ', `uptime`);
return $load[count($load)-1];
}
else {
return false;
}
}
elseif($windows) {
if(class_exists("COM")) {
$wmi = new COM("WinMgmts:\\\\.");
$cpus = $wmi->InstancesOf("Win32_Processor");

$cpuload = 0;
$i = 0;
while ($cpu = $cpus->Next()) {
$cpuload += $cpu->LoadPercentage;
$i++;
}

$cpuload = round($cpuload / $i, 2);
return "$cpuload%";
}
else {
return false;
}
}
}

function HorizLine($Width) {
	for ($i = 1; $i <= $Width; $i++) {
		$HDLine.="&#x2550;";
	}
	return $HDLine;
}
function CenterLine($Width) {
	for ($i = 1; $i <= $Width; $i++) {
		$HDLine.="&#x2500;";
	}
	return $HDLine;
}

// Get server loads, txt file from other servers
$load_ave = get_server_load(true);
$s1_load = file('S1_load.txt');
$d1_load = file('D1_load.txt');
$d2_load = file('D2_load.txt');
$d3_load = file('D3_load.txt');
$d4_load = file('D4_load.txt');
$d5_load = file('D5_load.txt');
$d6_load = file('D6_load.txt');
list( $line_num, $line ) = each( $s1_load );
$load_ave_s1=$line;
list( $line_num, $line ) = each( $d1_load ) ;
$load_ave_d1=$line;
list( $line_num, $line ) = each( $d2_load );
$load_ave_d2=$line;
list( $line_num, $line ) = each( $d3_load );
$load_ave_d3=$line;
list( $line_num, $line ) = each( $d4_load );
$load_ave_d4=$line;
list( $line_num, $line ) = each( $d5_load );
$load_ave_d5=$line;
list( $line_num, $line ) = each( $d6_load );
$load_ave_d6=$line;


while ( list( $line_num, $line ) = each( $fcontents ) ) {
        	// Exit if the Verbosity line shows up - Obscured by only listing vm context 'default'
                //if ( substr($line,0,9) == "Verbosity") {
                //        break;
                //}
                // Ensuring only vm entries show up
                if ( substr($line,0,7) == "default" ) {
			echo "<tr><td><pre>" . $line . "</td></tr>";
                }
        }

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_TIME = date("Y-m-d H:i:s");
$NOW_DAY = date("Y-m-d");
$NOW_HOUR = date("H:i:s");
$STARTtime = date("U");
$epochSIXhoursAGO = ($STARTtime - 21600);
$timeSIXhoursAGO = date("Y-m-d H:i:s",$epochSIXhoursAGO);

$stmt="select campaign_id from vicidial_campaigns where active='Y';";
$rslt=mysql_query($stmt, $link);
if (!isset($DB))   {$DB=0;}
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =$row[0];
	$i++;
	}

$stmt="select * from vicidial_user_groups;";
$rslt=mysql_query($stmt, $link);
if (!isset($DB))   {$DB=0;}
if ($DB) {echo "$stmt\n";}
$usergroups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $usergroups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$usergroups[$i] =$row[0];
	$i++;
	}

if (!isset($RR))   {$RR=4;}

$NFB = '<b><font size=6 face="courier">';
$NFE = '</font></b>';
$F=''; $FG=''; $B=''; $BG='';

?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
	.green {color: white; background-color: green}
	.red {color: white; background-color: red}
	.lightblue {color: black; background-color: #ADD8E6}
	.blue {color: white; background-color: blue}
	.midnightblue {color: white; background-color: #191970}
	.purple {color: white; background-color: purple}
	.violet {color: black; background-color: #EE82EE} 
	.thistle {color: black; background-color: #D8BFD8} 
	.olive {color: white; background-color: #808000}
	.yellow {color: black; background-color: yellow}
	.khaki {color: black; background-color: #F0E68C}
	.orange {color: black; background-color: orange}

	.r1 {color: black; background-color: #FFCCCC}
	.r2 {color: black; background-color: #FF9999}
	.r3 {color: black; background-color: #FF6666}
	.r4 {color: white; background-color: #FF0000}
	.b1 {color: black; background-color: #CCCCFF}
	.b2 {color: black; background-color: #9999FF}
	.b3 {color: black; background-color: #6666FF}
	.b4 {color: white; background-color: #0000FF}
<?
	$stmt="select group_id,group_color from vicidial_inbound_groups;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$INgroups_to_print = mysql_num_rows($rslt);
		if ($INgroups_to_print > 0)
		{
		$g=0;
		while ($g < $INgroups_to_print)
			{
			$row=mysql_fetch_row($rslt);
			$group_id[$g] = $row[0];
			$group_color[$g] = $row[1];
			echo "   .$group_id[$g] {color: black; background-color: $group_color[$g]}\n";
			$g++;
			}
		}

echo "\n-->\n
</STYLE>\n";

$stmt = "select count(*) from vicidial_campaigns where campaign_id='$group' and campaign_allow_inbound='Y';";
$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$campaign_allow_inbound = $row[0];

echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
echo"<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?RR=$RR&DB=$DB&group=$group&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">\n";
echo "<TITLE>OSDIAL: Time On Campaign: $group</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "<font color=navy><b>OSDIAL</b>&nbsp;&nbsp;&nbsp;&nbsp;Campaign:</font> \n";
echo "<INPUT TYPE=HIDDEN NAME=RR VALUE=\"$RR\">\n";
echo "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
echo "<INPUT TYPE=HIDDEN NAME=adastats VALUE=\"$adastats\">\n";
echo "<INPUT TYPE=HIDDEN NAME=SIPmonitorLINK VALUE=\"$SIPmonitorLINK\">\n";
echo "<INPUT TYPE=HIDDEN NAME=IAXmonitorLINK VALUE=\"$IAXmonitorLINK\">\n";
echo "<INPUT TYPE=HIDDEN NAME=usergroup VALUE=\"$usergroup\">\n";
echo "<INPUT TYPE=HIDDEN NAME=UGdisplay VALUE=\"$UGdisplay\">\n";
echo "<INPUT TYPE=HIDDEN NAME=UidORname VALUE=\"$UidORname\">\n";
echo "<INPUT TYPE=HIDDEN NAME=orderby VALUE=\"$orderby\">\n";
echo "<INPUT TYPE=HIDDEN NAME=SERVdisplay VALUE=\"$SERVdisplay\">\n";
echo "<INPUT TYPE=HIDDEN NAME=CALLSdisplay VALUE=\"$CALLSdisplay\">\n";
echo "<SELECT SIZE=1 NAME=group>\n";
echo "<option value=\"XXXX-ALL-ACTIVE-XXXX\">ALL ACTIVE</option>\n";
	$o=0;
	while ($groups_to_print > $o)
	{
		if ($groups[$o] == $group) {echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
		  else {echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
		$o++;
	}
echo "</SELECT>\n";
if ($UGdisplay > 0)
	{
	echo "<SELECT SIZE=1 NAME=usergroup>\n";
	echo "<option value=\"\">ALL USER GROUPS</option>\n";
		$o=0;
		while ($usergroups_to_print > $o)
		{
			if ($usergroups[$o] == $usergroup) {echo "<option selected value=\"$usergroups[$o]\">$usergroups[$o]</option>\n";}
			  else {echo "<option value=\"$usergroups[$o]\">$usergroups[$o]</option>\n";}
			$o++;
		}
	echo "</SELECT>\n";
	}
echo "<INPUT type=submit NAME=SUBMIT VALUE=SUBMIT>";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=navy SIZE=2>&nbsp;&nbsp;&nbsp;&nbsp;Update: \n";
echo "<a href=\"$PHP_SELF?group=$group&RR=600&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">10min</a> | ";
echo "<a href=\"$PHP_SELF?group=$group&RR=30&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">30sec</a> | ";
echo "<a href=\"$PHP_SELF?group=$group&RR=4&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">4sec</a>";
echo " &nbsp; &nbsp; &nbsp;<a href=\"./admin.php?ADD=31&campaign_id=$group\">Modify</a>&nbsp;\n";
echo "<a href=\"./AST_timeonVDADallSUMMARY.php?group=$group&RR=$RR&DB=$DB&adastats=$adastats\">Summary</a>&nbsp;\n";
echo "<a href=\"./admin.php?ADD=10\">Campaigns</a>&nbsp;&nbsp;<a href=\"./admin.php?ADD=999999\">Reports</a></FONT>\n";
echo "\n\n";


if (!$group) {echo "<BR><BR>please select a campaign from the pulldown above</FORM>\n"; exit;}
else
{
$stmt="select auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout,dial_statuses from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
if ($group=='XXXX-ALL-ACTIVE-XXXX') 
	{
	$stmt="select avg(auto_dial_level),min(dial_status_a),min(dial_status_b),min(dial_status_c),min(dial_status_d),min(dial_status_e),min(lead_order),min(lead_filter_id),sum(hopper_level),min(dial_method),avg(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),min(available_only_ratio_tally),min(adaptive_latest_server_time),min(local_call_time),avg(dial_timeout),min(dial_statuses) from vicidial_campaigns;";
	}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$DIALlev =		$row[0];
$DIALstatusA =	$row[1];
$DIALstatusB =	$row[2];
$DIALstatusC =	$row[3];
$DIALstatusD =	$row[4];
$DIALstatusE =	$row[5];
$DIALorder =	$row[6];
$DIALfilter =	$row[7];
$HOPlev =		$row[8];
$DIALmethod =	$row[9];
$maxDIALlev =	$row[10];
$DROPmax =		$row[11];
$targetDIFF =	$row[12];
$ADAintense =	$row[13];
$ADAavailonly =	$row[14];
$TAPERtime =	$row[15];
$CALLtime =		$row[16];
$DIALtimeout =	$row[17];
$DIALstatuses =	$row[18];
$DIALstatuses = (preg_replace("/ -$|^ /","",$DIALstatuses));
$DIALstatuses = (ereg_replace(' ',', ',$DIALstatuses));

$stmt="select count(*) from vicidial_hopper where campaign_id='" . mysql_real_escape_string($group) . "';";
if ($group=='XXXX-ALL-ACTIVE-XXXX') 
	{
	$stmt="select count(*) from vicidial_hopper;";
	}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$VDhop = $row[0];

$stmt="select dialable_leads,calls_today,drops_today,drops_answers_today_pct,differential_onemin,agents_average_onemin,balance_trunk_fill,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4 from vicidial_campaign_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
if ($group=='XXXX-ALL-ACTIVE-XXXX') 
	{
	$stmt="select sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),min(status_category_1),sum(status_category_count_1),min(status_category_2),sum(status_category_count_2),min(status_category_3),sum(status_category_count_3),min(status_category_4),sum(status_category_count_4) from vicidial_campaign_stats;";
	}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$DAleads =		$row[0];
$callsTODAY =	$row[1];
$dropsTODAY =	$row[2];
$drpctTODAY =	$row[3];
$diffONEMIN =	$row[4];
$agentsONEMIN = $row[5];
$balanceFILL =	$row[6];
$answersTODAY = $row[7];
$VSCcat1 =		$row[8];
$VSCcat1tally = $row[9];
$VSCcat2 =		$row[10];
$VSCcat2tally = $row[11];
$VSCcat3 =		$row[12];
$VSCcat3tally = $row[13];
$VSCcat4 =		$row[14];
$VSCcat4tally = $row[15];

if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) )
	{
	$diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
	$diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
	}
else {$diffpctONEMIN = '0.00';}

$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
if ($group=='XXXX-ALL-ACTIVE-XXXX') 
	{
	$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats;";
	}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$balanceSHORT = $row[0];

echo "<BR><table cellpadding=0 cellspacing=0><TR>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALlev&nbsp;&nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>TRUNK SHORT/FILL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $balanceSHORT / $balanceFILL &nbsp;&nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>FILTER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALfilter &nbsp;&nbsp;</TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>TIME:</B></TD><TD ALIGN=LEFT><font size=2 color=navy>&nbsp; $NOW_TIME </TD>";
echo "";
echo "</TR>";

if ($adastats>1)
	{
	echo "<TR BGCOLOR=\"#CCCCCC\">";
	echo "<TD ALIGN=RIGHT><font size=2><B>MAX LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $maxDIALlev &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED MAX:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DROPmax% &nbsp; &nbsp;</TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>TARGET DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $targetDIFF &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>INTENSITY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAintense &nbsp; &nbsp; </TD>";
	echo "</TR>";

	echo "<TR BGCOLOR=\"#CCCCCC\">";
	echo "<TD ALIGN=RIGHT><font size=2><B>DIAL TIMEOUT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALtimeout &nbsp;</TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>TAPER TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $TAPERtime &nbsp;</TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>LOCAL TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $CALLtime &nbsp;</TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>AVAIL ONLY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAavailonly &nbsp;</TD>";
	echo "</TR>";
	
	echo "<TR BGCOLOR=\"#CCCCCC\">";
	echo "<TD ALIGN=RIGHT><font size=2><B>DL DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffONEMIN &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffpctONEMIN% &nbsp; &nbsp; </TD>";
	echo "</TR>";
	}

echo "<TR>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIALABLE LEADS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DAleads &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>AVG AGENTS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $agentsONEMIN &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL METHOD:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALmethod &nbsp; &nbsp; </TD>";
echo "</TR>";

echo "<TR>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>HOPPER LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $HOPlev &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED / ANSWERED:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY / $answersTODAY &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>STATUSES:</B></TD><TD ALIGN=LEFT colspan=3><font size=2>&nbsp; $DIALstatuses &nbsp; &nbsp; </TD>";
echo "</TR>";

echo "<TR>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>LEADS IN HOPPER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $VDhop &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; ";
if ($drpctTODAY >= $DROPmax)
	{echo "<font color=red><B>$drpctTODAY%</B></font>";}
else
	{echo "$drpctTODAY%";}
echo " &nbsp; &nbsp;</TD>";
echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>ORDER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALorder &nbsp; &nbsp; </TD>";
echo "</tr><tr>";
if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) )
	{echo "<TD ALIGN=right><font size=2 color=navy><B>$VSCcat1:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1tally&nbsp;&nbsp;</td>\n";}
if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) )
	{echo "<TD ALIGN=right><font size=2 color=navy><B>$VSCcat2:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2tally&nbsp;&nbsp;</td>\n";}
if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) )
	{echo "<TD ALIGN=right><font size=2 color=navy><B>$VSCcat3:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3tally&nbsp;&nbsp;</td>\n";}
if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) )
	{echo "<TD ALIGN=right><font size=2 color=navy><B>$VSCcat4:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4tally&nbsp;&nbsp;</td>\n";}
echo "</TR>";

echo "<TR>";
echo "<TD ALIGN=LEFT COLSPAN=8>";

if ($adastats<2)
	{
	echo "<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=2&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>VIEW MORE SETTINGS</font></a>";
	} 
else 
	{
	echo "<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=1&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>VIEW LESS SETTINGS</font></a>";
	}
if ($UGdisplay>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=0&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>HIDE USER GROUP</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=1&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>VIEW USER GROUP</font></a>";
	}
if ($UidORname>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=0&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>SHOW AGENT ID</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=1&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\"><font size=1>SHOW AGENT NAME</font></a>";
	}
if ($SERVdisplay>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=0&CALLSdisplay=$CALLSdisplay\"><font size=1>HIDE SERVER INFO</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=1&CALLSdisplay=$CALLSdisplay\"><font size=1>SHOW SERVER INFO</font></a>";
	}
if ($CALLSdisplay>0)
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=0\"><font size=1>HIDE WAITING CALLS DETAIL</font></a>";
	}
else
	{
	echo " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=1\"><font size=1>SHOW WAITING CALLS DETAIL</font></a>";
	}
echo "</TD>";
echo "</TR>";
echo "</TABLE>";

echo "</FORM>\n\n";
}
###################################################################################
###### INBOUND/OUTBOUND CALLS
###################################################################################
if ($campaign_allow_inbound > 0)
	{
	$stmt="select closer_campaigns from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$closer_campaigns = preg_replace("/^ | -$/","",$row[0]);
	$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
	$closer_campaigns = "'$closer_campaigns'";

	$stmtB="from vicidial_auto_calls where status NOT IN('XFER') and ( (call_type='IN' and campaign_id IN($closer_campaigns)) or (campaign_id='" . mysql_real_escape_string($group) . "' and call_type IN('OUT','OUTBALANCE')) ) order by campaign_id,call_time;";
	}
else
	{
	if ($group=='XXXX-ALL-ACTIVE-XXXX') {$groupSQL = '';}
	else {$groupSQL = " and campaign_id='" . mysql_real_escape_string($group) . "'";}

	$stmtB="from vicidial_auto_calls where status NOT IN('XFER') $groupSQL order by campaign_id,call_time;";
	}
if ($CALLSdisplay > 0)
	{
	$stmtA = "SELECT status,campaign_id,phone_number,server_ip,UNIX_TIMESTAMP(call_time),call_type";
	}
else
	{
	$stmtA = "SELECT status";
	}


$k=0;
$stmt = "$stmtA $stmtB";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0)
	{
	$i=0;
	$out_total=0;
	$out_ring=0;
	$out_live=0;
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);

		if (eregi("LIVE",$row[0])) 
			{
			$out_live++;

			if ($CALLSdisplay > 0)
				{
				$CDstatus[$k] =			$row[0];
				$CDcampaign_id[$k] =	$row[1];
				$CDphone_number[$k] =	$row[2];
				$CDserver_ip[$k] =		$row[3];
				$CDcall_time[$k] =		$row[4];
				$CDcall_type[$k] =		$row[5];
				$k++;
				}
			}
		else
			{
			if (eregi("CLOSER",$row[0])) 
				{$nothing=1;}
			else 
				{$out_ring++;}
			}

		$out_total++;
		$i++;
		}

		if ($out_live > 0) {$F='<FONT class="r1">'; $FG='</FONT>';}
		if ($out_live > 4) {$F='<FONT class="r2">'; $FG='</FONT>';}
		if ($out_live > 9) {$F='<FONT class="r3">'; $FG='</FONT>';}
		if ($out_live > 14) {$F='<FONT class="r4">'; $FG='</FONT>';}

		if ($campaign_allow_inbound > 0)
			{echo "$NFB$out_total$NFE <font color=blue>current active calls</font> &nbsp; &nbsp; &nbsp; \n";}
		else
			{echo "$NFB$out_total$NFE <font color=blue>calls being placed</font> &nbsp; &nbsp; &nbsp; \n";}
		
		echo "$NFB$out_ring$NFE <font color=blue>calls ringing</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$F &nbsp;$out_live $FG$NFE <font color=blue>calls waiting for agents</font> &nbsp; &nbsp; &nbsp; \n";
		}
	else
	{
		echo "&nbsp;<font color=red>NO LIVE CALLS WAITING</font>&nbsp;";
	}



###################################################################################
###### CALLS WAITING
###################################################################################
// Changed to draw solid lines
$LNtopleft="&#x2554;";
$LNleft="&#x2551;";
$LNright="&#x2551;";
$LNcenterleft="&#x255F;";
$LNcenterbar="&#x2502;";
$LNtopdown="&#x2564;";
$LNtopright="&#x2557;";
$LNbottomleft="&#x255A;";
$LNbottomright="&#x255D;";
$LNcentcross="&#x253C;";
$LNcentright="&#x2562;";
$LNbottomup="&#x2567;";
// column length 8|14|14|17|9|12
$Cecho = '';
$Cecho .= "<font color=navy>&nbsp;&nbsp;VICIDIAL: Calls Waiting                      $NOW_TIME\n";
//$Cecho .= "+--------+--------------+--------------+-----------------+---------+------------+\n";
$Cecho .=$LNtopleft.HorizLine(8).$LNtopdown.HorizLine(14).$LNtopdown.HorizLine(14).$LNtopdown.HorizLine(17).$LNtopdown.HorizLine(9).$LNtopdown.HorizLine(12).$LNtopright."<br>";
$Cecho .="$LNleft STATUS $LNcenterbar CAMPAIGN     $LNcenterbar PHONE NUMBER $LNcenterbar SERVER_IP       $LNcenterbar DIALTIME$LNcenterbar CALL TYPE  $LNright\n";
$Cecho .=$LNcenterleft.CenterLine(8).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."<br>";
//$Cecho .= "| STATUS | CAMPAIGN     | PHONE NUMBER | SERVER_IP       | DIALTIME| CALL TYPE  |\n";
//$Cecho .= "+--------+--------------+--------------+-----------------+---------+------------+\n";

/*
$Cecho .=$LNtopleft.HorizLine(8).$LNtopdown.HorizLine(14).HorizLine(14).$LNtopdown.HorizLine(17).$LNtopdown.HorizLine(9).$LNtopdown.HorizLine(12).$LNtopright."<br>";
$Cecho .="$LNleft STATUS $LNcenterbar CAMPAIGN     $LNcenterbar PHONE NUMBER $LNcenterbar SERVER_IP       $LNcenterbar DIALTIME$LNcenterbar CALL TYPE  $LNright\n";
$Cecho .=$LNbottomleft.HorizLine(8).$LNtopup.HorizLine(14).$LNtopup.HorizLine(14).$LNtopup.HorizLine(17).$LNtopup.HorizLine(9).$LNtopup.HorizLine(12).$LNbottomright;
*/

$p=0;
while($p<$k)
	{
	$Cstatus =			sprintf("%-6s", $CDstatus[$p]);
	$Ccampaign_id =		sprintf("%-12s", $CDcampaign_id[$p]);
	$Cphone_number =	sprintf("%-12s", $CDphone_number[$p]);
	$Cserver_ip =		sprintf("%-15s", $CDserver_ip[$p]);
	$Ccall_type =		sprintf("%-10s", $CDcall_type[$p]);

	$Ccall_time_S = ($STARTtime - $CDcall_time[$p]);
	$Ccall_time_M = ($Ccall_time_S / 60);
	$Ccall_time_M = round($Ccall_time_M, 2);
	$Ccall_time_M_int = intval("$Ccall_time_M");
	$Ccall_time_SEC = ($Ccall_time_M - $Ccall_time_M_int);
	$Ccall_time_SEC = ($Ccall_time_SEC * 60);
	$Ccall_time_SEC = round($Ccall_time_SEC, 0);
	if ($Ccall_time_SEC < 10) {$Ccall_time_SEC = "0$Ccall_time_SEC";}
	$Ccall_time_MS = "$Ccall_time_M_int:$Ccall_time_SEC";
	$Ccall_time_MS =		sprintf("%7s", $Ccall_time_MS);

	$G = '';		$EG = '';
	if ($CDcall_type[$p] == 'IN')
		{
		$G="<SPAN class=\"$CDcampaign_id[$p]\"><B>"; $EG='</B></SPAN>';
		}
	$Cecho .= "$LNleft $G$Cstatus$EG $LNcenterbar $G$Ccampaign_id$EG $LNcenterbar $G$Cphone_number$EG $LNcenterbar $G$Cserver_ip$EG $LNcenterbar $G$Ccall_time_MS$EG $LNcenterbar $G$Ccall_type$EG $LNright\n";

	$p++;
	}

//$Cecho .= "+--------+--------------+--------------+-----------------+---------+------------+\n\n";
$Cecho .=$LNbottomleft.HorizLine(8).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."<br></font>";

if ($p<1)
	{$Cecho='';}

###################################################################################
###### TIME ON SYSTEM
###################################################################################


$agent_incall=0;
$agent_ready=0;
$agent_paused=0;
$agent_total=0;
$Aecho = '';
$Aecho .= "<font color=navy size=1 face=fixed,monospace>&nbsp;&nbsp;OSDIAL: Agents Time On Calls Campaign: $group                      $NOW_TIME\n";

// Changed to draw solid lines
$LNtopleft="&#x2554;";
$LNleft="&#x2551;";
$LNright="&#x2551;";
$LNcenterleft="&#x255F;";
$LNcenterbar="&#x2502;";
$LNtopdown="&#x2564;";
$LNtopright="&#x2557;";
$LNbottomleft="&#x255A;";
$LNbottomright="&#x255D;";
$LNcentcross="&#x253C;";
$LNcentright="&#x2562;";
$LNbottomup="&#x2567;";

$HDbegin =	"&#x2554;"; // top left double line 
$HTbegin =	"&#x2502;"; // |
$HDstation =	HorizLine(12)."&#x2564;"; //12
$HTstation =	"  STATION   &#x2502;";
$HDuser =		HorizLine(20)."&#x2564;"; //20
$HTuser =		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=userup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">USER</a>          &#x2502;";
$HDusergroup =		HorizLine(14)."&#x2564;"; //14
$HTusergroup =		" <a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=groupup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">USER GROUP</a>   &#x2502;";
$HDsessionid =		HorizLine(11)."&#x2564;"; //18
$HTsessionid =		" SESSIONID       &#x2502;";
$HDbarge =		HorizLine(7)."&#x2564;"; //7
$HTbarge =		" BARGE &#x2502;";
$HDstatus =		HorizLine(10)."&#x2564;"; //10
$HTstatus =		"  STATUS  &#x2502;";
$HDserver_ip =		HorizLine(17)."&#x2564;"; //17
$HTserver_ip =		"    SERVER IP    &#x2502;";
$HDcall_server_ip =	HorizLine(17)."&#x2564;"; //17
$HTcall_server_ip =	" CALL SERVER IP  &#x2502;";
$HDtime =			HorizLine(9)."&#x2564;"; //9
$HTtime =			"&nbsp;&nbsp;<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=timeup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">MM:SS</a>  &#x2502;";
$HDcampaign =		HorizLine(12)."&#x2557;"; //12
$HTcampaign =		"&nbsp;&nbsp;<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=campaignup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay\">CAMPAIGN</a>  &#x2551;";

if ($UGdisplay < 1)	{
	$HDusergroup =	'';
	$HTusergroup =	'';
}
if ( ($SIPmonitorLINK<1) && ($IAXmonitorLINK<1) ) {
	$HDsessionid =	HorizLine(11)."&#x2564";; //11
	$HTsessionid =	" SESSIONID &#x2502;";
}
if ( ($SIPmonitorLINK<2) && ($IAXmonitorLINK<2) ) {
	$HDbarge =		'';
	$HTbarge =		'';
}
if ($SERVdisplay < 1)	{
	$HDserver_ip =		'';
	$HTserver_ip =		'';
	$HDcall_server_ip =	'';
	$HTcall_server_ip =	'';
}
	
$Aline  = "$LNtopleft$HDstation$HDuser$HDusergroup$HDsessionid$HDbarge$HDstatus$HDserver_ip$HDcall_server_ip$HDtime$HDcampaign\n";
$Bline  = "$LNleft$HTstation$HTuser$HTusergroup$HTsessionid$HTbarge$HTstatus$HTserver_ip$HTcall_server_ip$HTtime$HTcampaign\n";
if ($UGdisplay < 1)	{
	$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$LNcentcross.CenterLine(11).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
	$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$LNbottomup.HorizLine(11).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";
	if ($SERVdisplay < 1) {
		$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$LNcentcross.CenterLine(11).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
		$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$LNbottomup.HorizLine(11).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";
	}
} else {	
	$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(11).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
	$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(11).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";
	if ($SERVdisplay < 1) {
		$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(11).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
		$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(11).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";
	}
}




$Aecho .= "$Aline";
$Aecho .= "$Bline";
$Aecho .= "$Cline";

if ($orderby=='timeup') {$orderSQL='status,last_call_time';}
if ($orderby=='timedown') {$orderSQL='status desc,last_call_time desc';}
if ($orderby=='campaignup') {$orderSQL='campaign_id,status,last_call_time';}
if ($orderby=='campaigndown') {$orderSQL='campaign_id desc,status desc,last_call_time desc';}
if ($orderby=='groupup') {$orderSQL='user_group,status,last_call_time';}
if ($orderby=='groupdown') {$orderSQL='user_group desc,status desc,last_call_time desc';}
if ($UidORname > 0)
	{
	if ($orderby=='userup') {$orderSQL='full_name,status,last_call_time';}
	if ($orderby=='userdown') {$orderSQL='full_name desc,status desc,last_call_time desc';}
	}
else
	{
	if ($orderby=='userup') {$orderSQL='vicidial_live_agents.user';}
	if ($orderby=='userdown') {$orderSQL='vicidial_live_agents.user desc';}
	}

if ($group=='XXXX-ALL-ACTIVE-XXXX') {$groupSQL = '';}
else {$groupSQL = " and campaign_id='" . mysql_real_escape_string($group) . "'";}
if (strlen($usergroup)<1) {$usergroupSQL = '';}
else {$usergroupSQL = " and user_group='" . mysql_real_escape_string($usergroup) . "'";}

$stmt="select extension,vicidial_live_agents.user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id,vicidial_users.user_group,vicidial_users.full_name,vicidial_live_agents.comments from vicidial_live_agents,vicidial_users where vicidial_live_agents.user=vicidial_users.user $groupSQL $usergroupSQL order by $orderSQL;";

#$stmt="select extension,vicidial_live_agents.user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id,vicidial_users.user_group,vicidial_users.full_name from vicidial_live_agents,vicidial_users where vicidial_live_agents.user=vicidial_users.user and campaign_id='" . mysql_real_escape_string($group) . "' order by $orderSQL;";

$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0)
	{
	$i=0;
	$agentcount=0;
	while ($i < $talking_to_print)
		{
		$row=mysql_fetch_row($rslt);
			if (eregi("READY|PAUSED",$row[3]))
			{
			$row[5]=$row[6];
			}
			if ($non_latin < 1)
			{
			$extension = eregi_replace('Local/',"",$row[0]);
			$extension =		sprintf("%-10s", $extension);
			while(strlen($extension)>10) {$extension = substr("$extension", 0, -1);}
			}
			else
			{
			$extension = eregi_replace('Local/',"",$row[0]);
			$extension =		sprintf("%-40s", $extension);
			while(mb_strlen($extension, 'utf-8')>10) {$extension = mb_substr("$extension", 0, -1,'utf8');}
			}
		$Luser =			$row[1];
		$user =				sprintf("%-18s", $row[1]);
		$Lsessionid =		$row[2];
		$sessionid =		sprintf("%-9s", $row[2]);
		$Lstatus =			$row[3];
		$status =			sprintf("%-6s", $row[3]);
		$server_ip =		sprintf("%-15s", $row[4]);
		$call_server_ip =	sprintf("%-15s", $row[7]);
		$campaign_id =	sprintf("%-10s", $row[8]);
		$comments=		$row[11];

		if (eregi("INCALL",$Lstatus)) 
			{
			if ( (eregi("AUTO",$comments)) or (strlen($comments)<1) )
				{$CM='A';}
			else
				{
				if (eregi("INBOUND",$comments)) 
					{$CM='I';}
				else
					{$CM='M';}
				} 
			}
		else {$CM=' ';}

		if ($UGdisplay > 0)
			{
				if ($non_latin < 1)
				{
				$user_group =		sprintf("%-12s", $row[9]);
				while(strlen($user_group)>12) {$user_group = substr("$user_group", 0, -1);}
				}
				else
				{
				$user_group =		sprintf("%-40s", $row[9]);
				while(mb_strlen($user_group, 'utf-8')>12) {$user_group = mb_substr("$user_group", 0, -1,'utf8');}
				}
			}
		if ($UidORname > 0)
			{
				if ($non_latin < 1)
				{
				$user =		sprintf("%-18s", $row[10]);
				while(strlen($user)>18) {$user = substr("$user", 0, -1);}
				}
				else
				{
				$user =		sprintf("%-40s", $row[10]);
				while(mb_strlen($user, 'utf-8')>18) {$user = mb_substr("$user", 0, -1,'utf8');}
				}
			}
		if (!eregi("INCALL|QUEUE",$row[3]))
			{$call_time_S = ($STARTtime - $row[6]);}
		else
			{$call_time_S = ($STARTtime - $row[5]);}

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
		if ($Lstatus=='INCALL')
			{
			if ($call_time_S >= 10) {$G='<SPAN class="thistle"><B>'; $EG='</B></SPAN>';}
			if ($call_time_M_int >= 1) {$G='<SPAN class="violet"><B>'; $EG='</B></SPAN>';}
			if ($call_time_M_int >= 5) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
	#		if ($call_time_M_int >= 10) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
			}
		if (eregi("PAUSED",$row[3])) 
			{
			if ($call_time_M_int >= 360) 
				{$i++; continue;} 
			else
				{
				$agent_paused++;  $agent_total++;
				$G=''; $EG='';
				if ($call_time_S >= 10) {$G='<SPAN class="khaki"><B>'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 1) {$G='<SPAN class="yellow"><B>'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 5) {$G='<SPAN class="olive"><B>'; $EG='</B></SPAN>';}
				}
			}
#		if ( (strlen($row[7])> 4) and ($row[7] != "$row[4]") )
#				{$G='<SPAN class="orange"><B>'; $EG='</B></SPAN>';}

		if ( (eregi("INCALL",$status)) or (eregi("QUEUE",$status)) ) {$agent_incall++;  $agent_total++;}
		if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {$agent_ready++;  $agent_total++;}
		if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) 
			{
			$G='<SPAN class="lightblue"><B>'; $EG='</B></SPAN>';
			if ($call_time_M_int >= 1) {$G='<SPAN class="blue"><B>'; $EG='</B></SPAN>';}
			if ($call_time_M_int >= 5) {$G='<SPAN class="midnightblue"><B>'; $EG='</B></SPAN>';}
			}

		$L='';
		$R='';
		if ($SIPmonitorLINK>0) {$L=" <a href=\"sip:6$Lsessionid@$server_ip\">LISTEN</a>";   $R='';}
		if ($IAXmonitorLINK>0) {$L=" <a href=\"iax:6$Lsessionid@$server_ip\">LISTEN</a>";   $R='';}
		if ($SIPmonitorLINK>1) {$R=" $LNcenterbar <a href=\"sip:$Lsessionid@$server_ip\">BARGE</a>";}
		if ($IAXmonitorLINK>1) {$R=" $LNcenterbar <a href=\"iax:$Lsessionid@$server_ip\">BARGE</a>";}

		//if ($UGdisplay > 0)	{$UGD = " $G$user_group$EG $LNcenterbar";}
		if ($UGdisplay > 0)	{$UGD = " $user_group $LNcenterbar";}
		else	{$UGD = "";}

		//if ($SERVdisplay > 0)	{$SVD = "$G$server_ip$EG $LNcenterbar $G$call_server_ip$EG $LNcenterbar ";}
		if ($SERVdisplay > 0)	{$SVD = "$server_ip $LNcenterbar $call_server_ip $LNcenterbar ";}
		else	{$SVD = "";}

		$agentcount++;

		//$Aecho .= "$LNleft $G$extension$EG $LNcenterbar <a href=\"./user_status.php?user=$Luser\" target=\"_blank\">$G$user$EG</a> $LNcenterbar$UGD $G$sessionid$EG$L$R $LNcenterbar $G$status$EG $CM $LNcenterbar $SVD$G$call_time_MS$EG $LNcenterbar $G$campaign_id$EG $LNright\n";
		
		$Aecho .= "$LNleft $G$extension $LNcenterbar <a href=\"./user_status.php?user=$Luser\" target=\"_blank\">$G$user$EG</a> $LNcenterbar$UGD $sessionid$L$R $LNcenterbar $status $CM $LNcenterbar $SVD$call_time_MS $LNcenterbar $campaign_id$EG $LNright\n";

		$i++;
		}

		$Aecho .= "$Dline";
		
		$Aecho .= "  $agentcount <font color=navy>agents logged in on all servers</font>\n";
		$Aecho .= "<PRE><FONT face=Fixed,monospace SIZE=1>";
		if (file_exists('S1_load.txt')) {
			$Aecho .= "  <font color=navy>Apache   Load Average:</font> $load_ave<br>";
			$Aecho .= "  <font color=navy>MySQL    Load Average:</font> $load_ave_s1<br>";
		} elseif (!file_exists('D1_load.txt')&& !file_exists('D2_load.txt') && !file_exists('D3_load.txt') && !file_exists('D4_load.txt') && !file_exists('D5_load.txt') && !file_exists('D6_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer Load Average:</font> $load_ave<br>";
		} else {
			$Aecho .= "  <font color=navy>SQL/Web  Load Average:</font> $load_ave<br>";
		}
		if (file_exists('D1_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 1 Load Average:</font> $load_ave_d1";
		}
		if (file_exists('D2_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 2 Load Average:</font> $load_ave_d2";
		}
		if (file_exists('D3_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 3 Load Average:</font> $load_ave_d3";
		}
		if (file_exists('D4_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 4 Load Average:</font> $load_ave_d4";
		}
		if (file_exists('D5_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 5 Load Average:</font> $load_ave_d5";
		}
		if (file_exists('D6_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 6 Load Average:</font> $load_ave_d6";
		}
		$Aecho .= "<br></font>";
		
	#	$Aecho .= "  <SPAN class=\"orange\"><B>          </SPAN> - Balanced call</B>\n";
	/*	$Aecho .= "  <font color=navy><SPAN class=\"lightblue\"><B>          </SPAN> - Agent waiting for call</B>\n";
		$Aecho .= "  <SPAN class=\"blue\"><B>          </SPAN> - Agent waiting for call > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"midnightblue\"><B>          </SPAN> - Agent waiting for call > 5 minutes</B>\n";
		$Aecho .= "  <SPAN class=\"thistle\"><B>          </SPAN> - Agent on call > 10 seconds</B>\n";
		$Aecho .= "  <SPAN class=\"violet\"><B>          </SPAN> - Agent on call > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"purple\"><B>          </SPAN> - Agent on call > 5 minutes</B>\n";
		$Aecho .= "  <SPAN class=\"khaki\"><B>          </SPAN> - Agent Paused > 10 seconds</B>\n";
		$Aecho .= "  <SPAN class=\"yellow\"><B>          </SPAN> - Agent Paused > 1 minute</B>\n";
		$Aecho .= "  <SPAN class=\"olive\"><B>          </SPAN> - Agent Paused > 5 minutes</B></font>\n";
*/
		if ($agent_ready > 0) {$B='<FONT class="b1">'; $BG='</FONT>';}
		if ($agent_ready > 4) {$B='<FONT class="b2">'; $BG='</FONT>';}
		if ($agent_ready > 9) {$B='<FONT class="b3">'; $BG='</FONT>';}
		if ($agent_ready > 14) {$B='<FONT class="b4">'; $BG='</FONT>';}


		echo "\n<BR>\n";

		echo "$NFB$agent_total$NFE <font color=blue>agents logged in</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_incall$NFE <font color=blue>agents in calls</font> &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$B &nbsp;$agent_ready $BG$NFE <font color=blue>agents waiting</font> &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_paused$NFE <font color=blue>paused agents</font> &nbsp; &nbsp; &nbsp; \n";
		
		echo "<PRE><FONT SIZE=2>";
		echo "";
		echo "$Cecho";
		echo "$Aecho";
		
		echo "<table width=730><tr><td>";
		echo "  <font color=navy><SPAN class=\"khaki\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 10 seconds</B><br>";
		echo "  <SPAN class=\"yellow\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 1 minute</B><br>";
		echo "  <SPAN class=\"olive\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 5 minutes</B></font>";
		echo "</td><td>";
		echo "  <font color=navy><SPAN class=\"lightblue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call</B><br>";
		echo "  <SPAN class=\"blue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call > 1 minute</B><br>";
		echo "  <SPAN class=\"midnightblue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call > 5 minutes</B>";
		echo "</td><td>";
		echo "  <font color=navy><SPAN class=\"thistle\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 10 seconds</B><br>";
		echo "  <SPAN class=\"violet\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 1 minute</B><br>";
		echo "  <SPAN class=\"purple\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 5 minutes</B>";
		echo "</td></tr></table>";
	}
	else
	{
		echo "&nbsp;&nbsp;<font color=red>&bull;&nbsp;&nbsp;NO AGENTS ON CALLS</font> \n";
		$Aecho ="<br><br>";
		$Aecho .= "<PRE><FONT face=Fixed,monospace SIZE=1>";
		if (file_exists('S1_load.txt')) {
			$Aecho .= "  <font color=navy>Apache   Load Average:</font> $load_ave<br>";
			$Aecho .= "  <font color=navy>MySQL    Load Average:</font> $load_ave_s1<br>";
		} elseif (!file_exists('D1_load.txt')&& !file_exists('D2_load.txt') && !file_exists('D3_load.txt') && !file_exists('D4_load.txt') && !file_exists('D5_load.txt') && !file_exists('D6_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer Load Average:</font> $load_ave<br>";
		} else {
			$Aecho .= "  <font color=navy>SQL/Web  Load Average:</font> $load_ave<br>";
		}
		if (file_exists('D1_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 1 Load Average:</font> $load_ave_d1";
		}
		if (file_exists('D2_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 2 Load Average:</font> $load_ave_d2";
		}
		if (file_exists('D3_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 3 Load Average:</font> $load_ave_d3";
		}
		if (file_exists('D4_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 4 Load Average:</font> $load_ave_d4";
		}
		if (file_exists('D5_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 5 Load Average:</font> $load_ave_d5";
		}
		if (file_exists('D6_load.txt')) {
			$Aecho .= "  <font color=navy>Dialer 6 Load Average:</font> $load_ave_d6";
		}
		echo "$Aecho";
	}

?>
</PRE>

</BODY></HTML>
