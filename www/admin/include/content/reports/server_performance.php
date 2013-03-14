<?php
# server_performance.php
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
# CHANGES
#
# 60619-1732 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 70417-1106 - Changed time frame to be definable per time range on a single day
#            - Fixed vertical scaling issues
#


function report_server_performance() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

$html = '';

$query_date=get_variable('query_date');
$begin_query_time=get_variable('begin_query_time');
$end_query_time=get_variable('end_query_time');
$group=get_variable('group');
$submit=get_variable('submit');
$SUBMIT=get_variable('SUBMIT');


# path from root to where ploticus files will be stored
$PLOTroot = "admin/ploticus";
$DOCroot = "$WeBServeRRooT/$PLOTroot/";

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");

if ($query_date=='') {$query_date = $NOW_DATE;}
if ($begin_query_time=='') {$begin_query_time = Date('H:i:s',strtotime(date('H:i:s'))-3600);}
if ($end_query_time=='') {$end_query_time = date('H:i:s');}

$stmt="select server_ip from servers;";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$servers_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $servers_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =$row[0];
	$i++;
	}
//$html .= "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
//$html .= "<TITLE>OSDIAL: Server Performance</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
$html .= "<table align=center cellpadding=0 cellspacing=0>";
$html .= "<tr><td align=center>";
$html .= "<br><font class=top_header color=$default_text size=+1>SERVER PERFORMANCE</font><br><br>";
$html .= "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
$html .= "<input type=hidden name=ADD value=\"$ADD\">\n";
$html .= "<input type=hidden name=SUB value=\"$SUB\">\n";
$html .= "Date: <INPUT TYPE=TEXT NAME=query_date SIZE=12 MAXLENGTH=10 VALUE=\"$query_date\"> &nbsp; \n";
$html .= "From: <INPUT TYPE=TEXT NAME=begin_query_time SIZE=10 MAXLENGTH=8 VALUE=\"$begin_query_time\"> \n";
$html .= "To: <INPUT TYPE=TEXT NAME=end_query_time SIZE=10 MAXLENGTH=8 VALUE=\"$end_query_time\"> \n";
$html .= "&nbsp;Server: <SELECT SIZE=1 NAME=group>\n";
	$o=0;
	while ($servers_to_print > $o)
	{
		if ($groups[$o] == $group) {$html .= "<option selected value=\"$groups[$o]\">$groups[$o]</option>\n";}
		  else {$html .= "<option value=\"$groups[$o]\">$groups[$o]</option>\n";}
		$o++;
	}
$html .= "</SELECT> \n";
$html .= "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
//$html .= "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
$html .= "</FORM>\n\n";

$html .= "<PRE><FONT SIZE=2>\n";


if (!$group)
{
$html .= "Please Select A Server, Date And Time Range\n";
}

else
{

$query_date_BEGIN = "$query_date $begin_query_time";   
$query_date_BEGIN = dateToServer($link,'first',$query_date_BEGIN,$webClientAdjGMT,'',$webClientDST,0);
$query_date_END = "$query_date $end_query_time";
$query_date_END = dateToServer($link,'first',$query_date_END,$webClientAdjGMT,'',$webClientDST,0);
$time_BEGIN = "$begin_query_time";   
$time_END = "$end_query_time";

$html .= "OSDIAL: Server Performance                             " . dateToLocal($link,'first',date('Y-m-d H:i:s'),$webClientAdjGMT,'',$webClientDST,1) . "\n";
$html .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
$html .= "---------- TOTALS, PEAKS and AVERAGES\n";

$stmt="select AVG(sysload),AVG(channels_total),MAX(sysload),MAX(channels_total),MAX(processes) from server_performance where start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
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
if ($HIGHlimit == "") {$HIGHlimit=100;}

$stmt="select AVG(cpu_user_percent),AVG(cpu_system_percent),AVG(cpu_idle_percent) from server_performance where start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$row=mysql_fetch_row($rslt);
$AVGcpuUSER =	sprintf("%10s", $row[0]);
$AVGcpuSYSTEM =	sprintf("%10s", $row[1]);
$AVGcpuIDLE =	sprintf("%10s", $row[2]);

$stmt="select count(*),SUM(length_in_min) from call_log where extension NOT IN('8365','8366','8367') and  start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$row=mysql_fetch_row($rslt);
$TOTALcalls =	sprintf("%10s", $row[0]);
$OFFHOOKtime =	sprintf("%10s", $row[1]);


$html .= "Total Calls in/out on this server:        $TOTALcalls\n";
$html .= "Total Off-Hook time on this server (min): $OFFHOOKtime\n";
$html .= "Average/Peak channels in use for server:  $AVGchannels / $HIGHchannels\n";
$html .= "Average/Peak load for server:             $AVGload / $HIGHload\n";
$html .= "Average USER process cpu percentage:      $AVGcpuUSER %\n";
$html .= "Average SYSTEM process cpu percentage:    $AVGcpuSYSTEM %\n";
$html .= "Average IDLE process cpu percentage:      $AVGcpuIDLE %\n";

$html .= "\n";
$html .= "---------- LINE GRAPH:\n";



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
if ($DB) {$html .= "$stmt\n";}
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


passthru("/usr/bin/pl -png $DOCroot/$HTMfile -o $DOCroot/$PNGfile");

sleep(1);

$html .= "</PRE>\n";
$html .= "\n";
$html .= "<IMG SRC=\"/$PLOTroot/$PNGfile\">\n";


$html .= "<!-- /usr/bin/pl -png $DOCroot/$HTMfile -o $DOCroot/$PNGfile -->";

}

return $html;
}


?>
