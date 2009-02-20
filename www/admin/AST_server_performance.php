<? 
# AST_server_performance.php
# 
# Copyright (C) 2007  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# CHANGES
#
# 60619-1732 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 70417-1106 - Changed time frame to be definable per time range on a single day
#            - Fixed vertical scaling issues
#

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["query_date"]))				{$query_date=$_GET["query_date"];}
	elseif (isset($_POST["query_date"]))	{$query_date=$_POST["query_date"];}
if (isset($_GET["begin_query_time"]))			{$begin_query_time=$_GET["begin_query_time"];}
	elseif (isset($_POST["begin_query_time"]))	{$begin_query_time=$_POST["begin_query_time"];}
if (isset($_GET["end_query_time"]))				{$end_query_time=$_GET["end_query_time"];}
	elseif (isset($_POST["end_query_time"]))	{$end_query_time=$_POST["end_query_time"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))	{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and modify_servers='1';";
	if ($DB) {echo "|$stmt|\n";}
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

# path from root to where ploticus files will be stored
$PLOTroot = "vicidial/ploticus";
$DOCroot = "$WeBServeRRooT/$PLOTroot/";

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");

if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($begin_query_time)) {$begin_query_time = '09:00:00';}
if (!isset($end_query_time)) {$end_query_time = '15:30:00';}
if (!isset($group)) {$group = '';}

$stmt="select server_ip from servers;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$servers_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $servers_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =$row[0];
	$i++;
	}
?>
<link rel="stylesheet" type="text/css" href="styles.css" media="screen">
<!-- HTML>
<HEAD -->
<STYLE type="text/css">
<!--
   .green {color: white; background-color: green}
   .red {color: white; background-color: red}
   .blue {color: white; background-color: blue}
   .purple {color: white; background-color: purple}
-->
 </STYLE>

<? 
//echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
//echo "<TITLE>OSDIAL: Server Performance</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<table align=center cellpadding=0 cellspacing=0>";
echo "<tr><td align=center>";
echo "<br><font color=navy size=+1>SERVER PERFORMANCE</font><br><br>";
echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
echo "Date: <INPUT TYPE=TEXT NAME=query_date SIZE=12 MAXLENGTH=10 VALUE=\"$query_date\"> &nbsp; \n";
echo "From: <INPUT TYPE=TEXT NAME=begin_query_time SIZE=10 MAXLENGTH=8 VALUE=\"$begin_query_time\"> \n";
echo "To: <INPUT TYPE=TEXT NAME=end_query_time SIZE=10 MAXLENGTH=8 VALUE=\"$end_query_time\"> \n";
echo "&nbsp;Server: <SELECT SIZE=1 NAME=group>\n";
	$o=0;
	while ($servers_to_print > $o)
	{
		if ($groups[$o] == $group) {echo "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
		  else {echo "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
		$o++;
	}
echo "</SELECT> \n";
echo "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
//echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
echo "</FORM>\n\n";

echo "<PRE><FONT SIZE=2>\n";


if (!$group)
{
echo "Please Select A Server, Date And Time Range\n";
}

else
{

$query_date_BEGIN = "$query_date $begin_query_time";   
$query_date_END = "$query_date $end_query_time";
$time_BEGIN = "$begin_query_time";   
$time_END = "$end_query_time";

echo "OSDIAL: Server Performance                             $NOW_TIME\n";

echo "Time range: $query_date_BEGIN to $query_date_END\n\n";
echo "---------- TOTALS, PEAKS and AVERAGES\n";

$stmt="select AVG(sysload),AVG(channels_total),MAX(sysload),MAX(channels_total),MAX(processes) from server_performance where start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);
$AVGload =	sprintf("%10s", $row[0]);
$AVGchannels =	sprintf("%10s", $row[1]);
$HIGHload =	$row[2];
	$HIGHmulti = intval($HIGHload / 100);
$HIGHchannels =	$row[3];
$HIGHprocesses =$row[4];
if ($row[2] > $row[3]) {$HIGHlimit = $row[2];}
else {$HIGHlimit = $row[3];}
if ($HIGHlimit < $row[4]) {$HIGHlimit = $row[4];}

$stmt="select AVG(cpu_user_percent),AVG(cpu_system_percent),AVG(cpu_idle_percent) from server_performance where start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);
$AVGcpuUSER =	sprintf("%10s", $row[0]);
$AVGcpuSYSTEM =	sprintf("%10s", $row[1]);
$AVGcpuIDLE =	sprintf("%10s", $row[2]);

$stmt="select count(*),SUM(length_in_min) from call_log where extension NOT IN('8365','8366','8367') and  start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$row=mysql_fetch_row($rslt);
$TOTALcalls =	sprintf("%10s", $row[0]);
$OFFHOOKtime =	sprintf("%10s", $row[1]);


echo "Total Calls in/out on this server:        $TOTALcalls\n";
echo "Total Off-Hook time on this server (min): $OFFHOOKtime\n";
echo "Average/Peak channels in use for server:  $AVGchannels / $HIGHchannels\n";
echo "Average/Peak load for server:             $AVGload / $HIGHload\n";
echo "Average USER process cpu percentage:      $AVGcpuUSER %\n";
echo "Average SYSTEM process cpu percentage:    $AVGcpuSYSTEM %\n";
echo "Average IDLE process cpu percentage:      $AVGcpuIDLE %\n";

echo "\n";
echo "---------- LINE GRAPH:\n";



##############################
#########  Graph stats

$DAT = '.dat';
$HTM = '.htm';
$PNG = '.png';
$filedate = date("Y-m-d_His");
$DATfile = "$group$query_date$shift$filedate$DAT";
$HTMfile = "$group$query_date$shift$filedate$HTM";
$PNGfile = "$group$query_date$shift$filedate$PNG";

$HTMfp = fopen ("$DOCroot/$HTMfile", "a");
$DATfp = fopen ("$DOCroot/$DATfile", "a");

$stmt="select DATE_FORMAT(start_time,'%H:%i:%s') as timex,sysload,processes,channels_total,live_recordings,cpu_user_percent,cpu_system_percent from server_performance where server_ip='" . mysql_real_escape_string($group) . "' and start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' order by timex;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$rows_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $rows_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$row[5] = intval(($row[5] + $row[6]) * $HIGHmulti);
	$row[6] = intval($row[6] * $HIGHmulti);
	fwrite ($DATfp, "$row[5]\t$row[6]\t$row[0]\t$row[1]\t$row[2]\t$row[3]\n");
	$i++;
	}
fclose($DATfp);

$rows_to_max = ($rows_to_print + 100);

#print "rows: $i\n";

$time_scale_abb = '5 minutes';
$time_scale_tick = '1 minute';
if ($i > 1000) {$time_scale_abb = '10 minutes';   $time_scale_tick = '2 minutes';}
if ($i > 2000) {$time_scale_abb = '20 minutes';   $time_scale_tick = '4 minutes';}
if ($i > 3000) {$time_scale_abb = '30 minutes';   $time_scale_tick = '5 minutes';}
if ($i > 4000) {$time_scale_abb = '40 minutes';   $time_scale_tick = '10 minutes';}
if ($i > 5000) {$time_scale_abb = '60 minutes';   $time_scale_tick = '15 minutes';}
if ($i > 6000) {$time_scale_abb = '90 minutes';   $time_scale_tick = '15 minutes';}
if ($i > 7000) {$time_scale_abb = '120 minutes';   $time_scale_tick = '30 minutes';}

$HTMcontent  = '';
$HTMcontent .= "#proc page\n";
$HTMcontent .= "#if @DEVICE in png,gif\n";
$HTMcontent .= "   scale: 0.6\n";
$HTMcontent .= "\n";
$HTMcontent .= "#endif\n";
$HTMcontent .= "#proc getdata\n";
$HTMcontent .= "file: $DOCroot/$DATfile\n";
$HTMcontent .= "fieldnames: userproc sysproc time load processes channels\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc areadef\n";
$HTMcontent .= "title: Server $group   $query_date_BEGIN to $query_date_END\n";
$HTMcontent .= "titledetails: size=14  align=C\n";
$HTMcontent .= "rectangle: 1 1 14 7\n";
$HTMcontent .= "xscaletype: time hh:mm:ss\n";
$HTMcontent .= "xrange: $time_BEGIN $time_END\n";
$HTMcontent .= "yrange: 0 $HIGHlimit\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc xaxis\n";
$HTMcontent .= "stubs: inc $time_scale_abb\n";
$HTMcontent .= "minorticinc: $time_scale_tick\n";
$HTMcontent .= "stubformat: hh:mm:ssa\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc yaxis\n";
$HTMcontent .= "stubs: inc 50\n";
$HTMcontent .= "grid: color=yellow\n";
$HTMcontent .= "gridskip: min\n";
$HTMcontent .= "ticincrement: 100 1000\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc lineplot\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: userproc\n";
$HTMcontent .= "linedetails: color=purple width=.5\n";
$HTMcontent .= "fill: lavender\n";
$HTMcontent .= "legendlabel: user proc%\n";
$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc lineplot\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: sysproc\n";
$HTMcontent .= "linedetails: color=yelloworange width=.5\n";
$HTMcontent .= "fill: dullyellow\n";
$HTMcontent .= "legendlabel: system proc%\n";
$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc curvefit\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: load\n";
$HTMcontent .= "linedetails: color=blue width=.5\n";
$HTMcontent .= "legendlabel: load\n";
$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc curvefit\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: processes\n";
$HTMcontent .= "linedetails: color=red width=.5\n";
$HTMcontent .= "legendlabel: processes\n";
$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc curvefit\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: channels\n";
$HTMcontent .= "linedetails: color=green width=.5\n";
$HTMcontent .= "legendlabel: channels\n";
$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc legend\n";
$HTMcontent .= "location: max-2 max\n";
$HTMcontent .= "seglen: 0.2\n";
$HTMcontent .= "\n";

fwrite ($HTMfp, "$HTMcontent");
fclose($HTMfp);


passthru("/usr/local/bin/pl -png $DOCroot/$HTMfile -o $DOCroot/$PNGfile");

sleep(1);

echo "</PRE>\n";
echo "\n";
echo "<IMG SRC=\"/$PLOTroot/$PNGfile\">\n";


echo "<!-- /usr/local/bin/pl -png $DOCroot/$HTMfile -o $DOCroot/$PNGfile -->";

}



?>

<!-- /BODY></HTML -->