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
$getmap=get_variable('map');
if (empty($getmap)) $getmap=0;


# path from root to where ploticus files will be stored
$PLOTroot = "admin/ploticus";
$DOCroot = "$WeBServeRRooT/$PLOTroot/";

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");

if ($query_date=='') {$query_date = $NOW_DATE;}
if ($begin_query_time=='') {$begin_query_time = Date('H:i',strtotime(date('H:i:s'))-3600).':00';}
if ($end_query_time=='') {$end_query_time = date('H:i').':59';}
$begin_time = OSDpreg_replace('/:\d\d$/','',$begin_query_time);
$end_time = OSDpreg_replace('/:\d\d$/','',$end_query_time);
list($bh,$bm) = OSDpreg_split('/:/',$begin_time);
list($eh,$em) = OSDpreg_split('/:/',$end_time);
$begin_time = sprintf('%02d:%02d',$bh,$bm);
$end_time = sprintf('%02d:%02d',$eh,$em);

$stmt="select server_ip,server_id,server_description from servers;";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$servers_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $servers_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =$row[0];
	$groupsID[$i] =$row[1];
	$groupsDESC[$i] =$row[2];
	$i++;
	}
//$html .= "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
//$html .= "<TITLE>OSDIAL: Server Performance</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
$html .= "<table align=center cellpadding=0 cellspacing=0 width=930>";
$html .= "<tr><td align=center>";
$html .= "<br><font class=top_header color=$default_text size=+1>SERVER PERFORMANCE</font><br><br>";
$html .= "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
$html .= "<input type=hidden name=ADD value=\"$ADD\">\n";
$html .= "<input type=hidden name=SUB value=\"$SUB\">\n";
$html .= "<input type=hidden name=map value=\"$getmap\">\n";
$html .= "Date: ";

$html .= "<script>\nvar cal1 = new CalendarPopup('caldiv1');\ncal1.showNavigationDropdowns();\n</script>\n";
$html .= "<input type=text name=query_date size=11 maxlength=10 value=\"$query_date\">\n";
$html .= "<a href=# onclick=\"cal1.addDisabledDates(formatDate(new Date().addDays(1),'yyyy-MM-dd'),null);cal1.select(document.forms[0].query_date,'acal1','yyyy-MM-dd'); return false;\" name=acal1 id=acal1>\n";
$html .= "<img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";

$html .= "From: <INPUT TYPE=TEXT NAME=begin_query_time SIZE=10 MAXLENGTH=8 VALUE=\"$begin_time\"> \n";
$html .= "To: <INPUT TYPE=TEXT NAME=end_query_time SIZE=10 MAXLENGTH=8 VALUE=\"$end_time\"> \n";
$html .= "<br>&nbsp;Server: <SELECT SIZE=1 NAME=group>\n";
	$o=0;
	while ($servers_to_print > $o)
	{
        $gsel='';
		if ($groups[$o] == $group) $gsel = 'selected';
		$html .= "<option value=\"$groups[$o]\" $gsel>$groups[$o] - $groupsID[$o]: $groupsDESC[$o]</option>\n";
		$o++;
	}
$html .= "</SELECT> \n";
$html .= "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
//$html .= "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=999999\">REPORTS</a> </FONT>\n";
$html .= "</FORM>\n";
$html .= "<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>";

#$html .= "<PRE><FONT SIZE=2>\n";


if (!$group)
{
// $html .= "Please Select A Server, Date And Time Range\n";
}

else
{

$query_date_BEGIN = "$query_date $begin_query_time";   
$query_date_BEGIN = dateToServer($link,'first',$query_date_BEGIN,$webClientAdjGMT,'',$webClientDST,0);
$query_date_END = "$query_date $end_query_time";
$query_date_END = dateToServer($link,'first',$query_date_END,$webClientAdjGMT,'',$webClientDST,0);
$time_BEGIN = "$begin_query_time";   
$time_END = "$end_query_time";

#$html .= "OSDIAL: Server Performance                             " . dateToLocal($link,'first',date('Y-m-d H:i:s'),$webClientAdjGMT,'',$webClientDST,1) . "\n";
#$html .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
#$html .= "---------- TOTALS, PEAKS and AVERAGES\n";

$stmt="select AVG(sysload),AVG(channels_total),MAX(sysload),MAX(channels_total),MAX(processes),UNIX_TIMESTAMP('" . mysql_real_escape_string($query_date_END) . "'),UNIX_TIMESTAMP('" . mysql_real_escape_string($query_date_BEGIN) . "') from server_performance where start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$row=mysql_fetch_row($rslt);
$AVGload =	$row[0]+0;
$AVGchannels = $row[1]+0;
$HIGHload =	$row[2]+0;
$HIGHchannels =	$row[3]+0;
$HIGHprocesses =$row[4]+0;
if ($row[2] > $row[3]) {$HIGHlimit = $row[2];}
else {$HIGHlimit = $row[3];}
if ($HIGHlimit < $row[4]) {$HIGHlimit = $row[4];}
if ($HIGHlimit == "") {$HIGHlimit=100;}

# The multiplier is to get a relativistic view of user and system proc %...we also need to factor in the incrmental graph growth.
    if ($HIGHlimit%50 != 0) $HIGHlimit+=(50-($HIGHlimit%50));
	$HIGHmulti = $HIGHlimit / 100;

$end_epoch=$row[5];
$begin_epoch=$row[6];

$stmt="select AVG(cpu_user_percent),AVG(cpu_system_percent),AVG(cpu_idle_percent) from server_performance where start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$row=mysql_fetch_row($rslt);
$AVGcpuUSER =	sprintf("%12s%%", round($row[0],0)+0);
$plAVGcpuUSER =	sprintf("%s%%", round($row[0],0)+0);
$AVGcpuSYSTEM =	sprintf("%12s%%", round($row[1],0)+0);
$plAVGcpuSYSTEM =	sprintf("%s%%", round($row[1],0)+0);
$AVGcpuIDLE =	sprintf("%12s%%", round($row[2],0)+0);
$plAVGcpuIDLE =	sprintf("%s%%", round($row[2],0)+0);

$stmt="select count(*),SUM(length_in_min) from call_log where extension NOT IN('8365','8366','8367') and  start_time <= '" . mysql_real_escape_string($query_date_END) . "' and start_time >= '" . mysql_real_escape_string($query_date_BEGIN) . "' and server_ip='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$row=mysql_fetch_row($rslt);
$TOTALcalls =	sprintf("%12s", $row[0]+0);
$OFFHOOKtime =	sprintf("%12s", $row[1]+0);

$COMBchannels =	sprintf("%12s", ceil($AVGchannels) . ' / ' . $HIGHchannels);
$COMBload =	sprintf("%12s", round($AVGload,1) . ' / ' . round($HIGHload,1));


#$html .= "Total Calls in/out on this server:        $TOTALcalls\n";
#$html .= "Total Off-Hook time on this server (min): $OFFHOOKtime\n";
#$html .= "Average/Peak channels in use for server:  $COMBchannels\n";
#$html .= "Average/Peak load for server:             $COMBload\n";
#$html .= "Average USER process cpu percentage:      $AVGcpuUSER\n";
#$html .= "Average SYSTEM process cpu percentage:    $AVGcpuSYSTEM\n";
#$html .= "Average IDLE process cpu percentage:      $AVGcpuIDLE\n";

#$html .= "\n";
#$html .= "---------- LINE GRAPH:\n";

$period_min = ceil(($end_epoch-$begin_epoch)/60);



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
	$row[5] = intval($row[5] * $HIGHmulti);
	$row[6] = intval($row[6] * $HIGHmulti);
	fwrite ($DATfp, "$row[5]\t$row[6]\t$row[0]\t$row[1]\t$row[2]\t$row[3]\n");
	$i++;
	}
fclose($DATfp);

$rows_to_max = ($rows_to_print + 100);

#print "rows: $i\n";

$time_scale_abb = ($period_min/12).' minutes';
$time_scale_tick = ($period_min/60).' minute';

$HTMcontent  = '';
$HTMcontent .= "#proc page\n";
$HTMcontent .= "pagesize: 10 7.25\n";
$HTMcontent .= "title: OSDial Server Performance Graph\n";
$HTMcontent .= "Server IP: $group     Date/Time: ". dateToLocal($link,'first',date('Y-m-d H:i:s'),$webClientAdjGMT,'',$webClientDST,1)."\n";
$HTMcontent .= "\n";
$HTMcontent .= "backgroundcolor: xDDDDDD\n";
$HTMcontent .= "titledetails: size=16 align=C color=x000099 font=DejaVuSans-Bold\n";
$HTMcontent .= "#if @DEVICE in png,gif\n";
$HTMcontent .= "   scale: 1.0\n";
$HTMcontent .= "#endif\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc getdata\n";
$HTMcontent .= "file: $DOCroot/$DATfile\n";
$HTMcontent .= "fieldnames: userproc sysproc time load processes channels\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc areadef\n";
$HTMcontent .= "title: $query_date_BEGIN to $query_date_END\n";
$HTMcontent .= "titledetails: size=14  align=C color=x000099 font=DejaVuSans\n";
$HTMcontent .= "rectangle: 1 1 9 5.4\n";
$HTMcontent .= "xscaletype: time hh:mm:ss\n";
$HTMcontent .= "xrange: $time_BEGIN $time_END\n";
$HTMcontent .= "yrange: 0 $HIGHlimit\n";
$HTMcontent .= "areacolor: xEEEEEE\n";
if ($getmap>0) $HTMcontent .= "clickmapurl: javascript:console.log('Xval: @@XVAL, Yval: @@YVAL, userproc%: @@1, sysproc%: @@2, load: @@3, processes: @@4, channels: @@5');false;\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc xaxis\n";
$HTMcontent .= "stubs: inc $time_scale_abb\n";
$HTMcontent .= "minorticinc: $time_scale_tick\n";
$HTMcontent .= "stubformat: hh:mma\n";
$HTMcontent .= "stubdetails: font=DejaVuSans\n";
if ($getmap>0) $HTMcontent .= "clickmap: xygrid\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc yaxis\n";
$HTMcontent .= "stubs: inc 50\n";
$HTMcontent .= "stubdetails: font=DejaVuSans\n";
$HTMcontent .= "grid: color=x547E9E\n";
$HTMcontent .= "gridskip: min\n";
$HTMcontent .= "ticincrement: 100 1000\n";
if ($getmap>0) $HTMcontent .= "clickmap: xygrid\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc lineplot\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: userproc\n";
$HTMcontent .= "linedetails: color=purple width=1\n";
$HTMcontent .= "fill: lavender\n";
$HTMcontent .= "legendlabel: userproc%\n";
#$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc lineplot\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: sysproc\n";
$HTMcontent .= "linedetails: color=yelloworange width=1\n";
$HTMcontent .= "fill: dullyellow\n";
$HTMcontent .= "legendlabel: sysproc%\n";
#$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc curvefit\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: load\n";
$HTMcontent .= "linedetails: color=blue width=1\n";
$HTMcontent .= "legendlabel: load\n";
$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc curvefit\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: processes\n";
$HTMcontent .= "linedetails: color=red width=1\n";
$HTMcontent .= "legendlabel: processes\n";
$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc curvefit\n";
$HTMcontent .= "xfield: time\n";
$HTMcontent .= "yfield: channels\n";
$HTMcontent .= "linedetails: color=green width=1\n";
$HTMcontent .= "legendlabel: channels\n";
$HTMcontent .= "maxinpoints: $rows_to_max\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc legend\n";
$HTMcontent .= "location: max-0.8 max+1\n";
$HTMcontent .= "textdetails: font=DejaVuSans\n";
$HTMcontent .= "seglen: 0.2\n";
$HTMcontent .= "backcolor: xC0C0C0\n";
$HTMcontent .= "\n";
$HTMcontent .= "#proc annotate\n";
$HTMcontent .= "textdetails: size=8 color=x000099 font=DejaVuSansMono\n";
$HTMcontent .= "text: Total Calls In/Out: $TOTALcalls   Average/Peak Channels:$COMBchannels\n";
$HTMcontent .= "Total Off-Hook Time:$OFFHOOKtime   Average/Peak Load:    $COMBload\n";
$HTMcontent .= "Average CPU Use:  $plAVGcpuUSER USER   $plAVGcpuSYSTEM SYSTEM   $plAVGcpuIDLE IDLE\n";
$HTMcontent .= "\n";
$HTMcontent .= "location: 5 6\n";
$HTMcontent .= "\n";

fwrite ($HTMfp, "$HTMcontent");
fclose($HTMfp);


$ENV{'GDFONTPATH'} = '/opt/osdial/html/admin/templates/default';

$html .= "<map name=\"perfmap\">\n";
$mapdata = array();
if ($getmap>0) {
    exec("/usr/bin/pl -csmap -mapfile stdout -png $DOCroot/$HTMfile -o $DOCroot/$PNGfile",$mapdata);
} else {
    exec("/usr/bin/pl -png $DOCroot/$HTMfile -o $DOCroot/$PNGfile",$mapdata);
}
foreach ($mapdata as $line) {
    $html .= $line;
}
$html .= "</map>\n";

#$html .= "</PRE>\n";
#$html .= "\n";
$html .= "<img src=\"/$PLOTroot/$PNGfile\" usemap=\"#perfmap\">\n";


$html .= "<!-- /usr/bin/pl -csmap -png $DOCroot/$HTMfile -o $DOCroot/$PNGfile -->";

}

return $html;
}


?>
