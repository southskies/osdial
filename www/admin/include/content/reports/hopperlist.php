<?php
### hopperlist.php
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
# CHANGES
#
# 60619-1654 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 70115-1614 - Added ALT field for osdial_hopper alt_dial column
# 71029-0852 - Added list_id to the output
# 71030-2118 - Added priority to display
#

function report_hopperlist() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

$html='<center><br><br><br>';
$group = get_variable('group');
$submit = get_variable('submit');
$SUBMIT = get_variable('SUBMIT');

$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
if (!isset($server_ip)) {$server_ip = '10.10.10.15';}

$stmt="select campaign_id,campaign_name from osdial_campaigns order by campaign_id;";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$campaigns_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $campaigns_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$campaign_id[$i] =$row[0];
	$campaign_name[$i] =$row[1];
	$i++;
	}

$html .= "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
$html .= "<input type=hidden name=ADD value=\"$ADD\">\n";
$html .= "<input type=hidden name=SUB value=\"$SUB\">\n";
#$html .= "<INPUT TYPE=HIDDEN NAME=server_ip VALUE=\"$server_ip\">\n";
#$html .= "<INPUT TYPE=TEXT NAME=query_date SIZE=10 MAXLENGTH=10 VALUE=\"$query_date\">\n";
$html .= "<SELECT SIZE=1 NAME=group>\n";
	$o=0;
	while ($campaigns_to_print > $o)
	{
		if ($campaign_id[$o] == $group) {$html .= "<option selected value=\"$campaign_id[$o]\">$campaign_id[$o] - $campaign_name[$o]</option>\n";}
		  else {$html .= "<option value=\"$campaign_id[$o]\">$campaign_id[$o] - $campaign_name[$o]</option>\n";}
		$o++;
	}
$html .= "</SELECT>\n";
$html .= "<INPUT TYPE=SUBMIT NAME=SUBMIT VALUE=SUBMIT>\n";
$html .= " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=\"./admin.php?ADD=34&campaign_id=$group\">MODIFY</a> \n";
$html .= "</FORM>\n\n";

$html .= "<PRE><FONT SIZE=2>\n\n";


if (!$group)
{
$html .= "\n\n";
$html .= "PLEASE SELECT A CAMPAIGN ABOVE AND CLICK SUBMIT\n";
}

else
{


$html .= "OSDIAL: Live Current Hopper List                      " . dateToLocal($link,'first',date('Y-m-d H:i:s'),$webClientAdjGMT,'',$webClientDST,1) . "\n";

$html .= "\n";
$html .= "---------- TOTALS\n";

$stmt="SELECT count(*) FROM osdial_hopper WHERE campaign_id='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$row=mysql_fetch_row($rslt);

$TOTALcalls =	sprintf("%10s", $row[0]);

$html .= "Total leads in hopper right now:       $TOTALcalls\n";


##############################
#########  LEAD STATS

$html .= "\n";
$html .= "---------- LEADS IN HOPPER\n";
$html .= "+------+---------+----------+--------------+--------------+-----------------+-------+---------+-------+--------+-------+\n";
$html .= "| NUM  | HSTATUS | PRIORITY | LEAD ID      | LIST ID      | PHONE NUM       | STATE | LSTATUS | COUNT | GMT    | ALT   |\n";
$html .= "+------+---------+----------+--------------+--------------+-----------------+-------+---------+-------+--------+-------+\n";

$stmt="SELECT osdial_hopper.lead_id,phone_number,osdial_hopper.state,osdial_list.status,called_count,osdial_hopper.gmt_offset_now,hopper_id,alt_dial,osdial_hopper.list_id,osdial_hopper.priority,osdial_hopper.status FROM osdial_hopper,osdial_list WHERE osdial_hopper.campaign_id='" . mysql_real_escape_string($group) . "' AND osdial_hopper.lead_id=osdial_list.lead_id ORDER BY osdial_hopper.status DESC, priority DESC,hopper_id limit 2000;";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$users_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $users_to_print)
	{
	$row=mysql_fetch_row($rslt);

	$FMT_i =		sprintf("%-4s", $i);
	$lead_id =		sprintf("%-12s", $row[0]);
	$phone_number =	sprintf("%-15s", $row[1]);
	$state =		sprintf("%-5s", $row[2]);
	$status =		sprintf("%-7s", $row[3]);
	$count =		sprintf("%-5s", $row[4]);
	$gmt =			sprintf("%-6s", $row[5]);
	$hopper_id =	sprintf("%-6s", $row[6]);
	$alt_dial =		sprintf("%-5s", $row[7]);
	$list_id =		sprintf("%-12s", $row[8]);
	$priority =		sprintf("%-8s", $row[9]);
	$hstatus =		sprintf("%-7s", $row[10]);

if ($DB) {$html .= "| $FMT_i | $hstatus | $priority | $lead_id | $list_id | $phone_number | $state | $status | $count | $gmt | $hopper_id |\n";}
else {$html .= "| $FMT_i | $hstatus | $priority | $lead_id | $list_id | $phone_number | $state | $status | $count | $gmt | $alt_dial |\n";}

	$i++;
	}

$html .= "+------+---------+----------+--------------+--------------+-----------------+-------+---------+-------+--------+-------+\n";


}

$html .= "</center>";
return $html;
}
?>
