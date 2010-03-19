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
# 090511-2123 - Added status_category_hour_counts
# 090609-0230 - Added INBOUND and OUTBOUND campaign selections

function report_realtime_detail() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $html = '';
	$pref = '';

    $RR = get_variable('RR');
    $group = get_variable('group');
    $usergroup = get_variable('usergroup');
    $UGdisplay = get_variable('UGdisplay');
    $UidORname = get_variable('UidORname');
    $orderby = get_variable('orderby');
    $SERVdisplay = get_variable('SERVdisplay');
    $CALLSdisplay = get_variable('CALLSdisplay');
    $cpuinfo = get_variable('cpuinfo');
    $DB = get_variable('DB');
    $adastats = get_variable('adastats');
    $SIPmonitorLINK = get_variable('SIPmonitorLINK');
    $IAXmonitorLINK = get_variable('IAXmonitorLINK');
    if ($RR=='') {$RR=4;}
    if ($group=='') {$group='XXXX-ALL-ACTIVE-XXXX';}
    if ($UGdisplay=='') {$UGdisplay=0;}
    if ($UidORname=='') {$UidORname=0;}
    if ($orderby=='') {$orderby='timeup';}
    if ($SERVdisplay=='') {$SERVdisplay=1;}
    if ($CALLSdisplay=='') {$CALLSdisplay=1;}
    if ($cpuinfo=='') {$cpuinfo=1;}
	
	
	$NOW_TIME = date("Y-m-d H:i:s");
	$NOW_DAY = date("Y-m-d");
	$NOW_HOUR = date("H:i:s");
	$STARTtime = date("U");
	$epochSIXhoursAGO = ($STARTtime - 21600);
	$timeSIXhoursAGO = date("Y-m-d H:i:s",$epochSIXhoursAGO);
	
	
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

	
	$stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE active='Y' AND campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
	$rslt=mysql_query($stmt, $link);
	if (!isset($DB))   {$DB=0;}
	if ($DB) {$html .= "$stmt\n";}
	
	$groups_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $groups_to_print)	{
		$row=mysql_fetch_row($rslt);
		$groups[$i] =$row[0];
		$group_names[$i] =$row[1];
		$i++;
	}
	
	$stmt="SELECT * FROM osdial_user_groups;";
	$rslt=mysql_query($stmt, $link);
	if (!isset($DB))   {$DB=0;}
	if ($DB) {$html .= "$stmt\n";}
	$usergroups_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $usergroups_to_print) {
		$row=mysql_fetch_row($rslt);
		$usergroups[$i] =$row[0];
		$i++;
	}
	
	
	$NFB = '<b><font size=6 face="courier">'; 
	$NFE = '</font></b>';
	$F=''; $FG=''; $B=''; $BG='';
	
	
    $html .= "<STYLE type=\"text/css\">\n";
    $html .= "<!--\n";
    $html .= "   .green {color: white; background-color: green}\n";
    $html .= "   .red {color: white; background-color: red}\n";
    $html .= "   .lightblue {color: black; background-color: #ADD8E6}\n";
    $html .= "   .blue {color: white; background-color: blue}\n";
    $html .= "   .midnightblue {color: white; background-color: #191970}\n";
    $html .= "   .purple {color: white; background-color: purple}\n";
    $html .= "   .violet {color: black; background-color: #EE82EE} \n";
    $html .= "   .thistle {color: black; background-color: #D8BFD8} \n";
    $html .= "   .olive {color: white; background-color: #808000}\n";
    $html .= "   .yellow {color: black; background-color: yellow}\n";
    $html .= "   .khaki {color: black; background-color: #F0E68C}\n";
    $html .= "   .orange {color: black; background-color: orange}\n";
	
    $html .= "   .r1 {color: black; background-color: #FFCCCC}\n";
    $html .= "   .r2 {color: black; background-color: #FF9999}\n";
    $html .= "   .r3 {color: black; background-color: #FF6666}\n";
    $html .= "   .r4 {color: white; background-color: #FF0000}\n";
    $html .= "   .b1 {color: black; background-color: #CCCCFF}\n";
    $html .= "   .b2 {color: black; background-color: #9999FF}\n";
    $html .= "   .b3 {color: black; background-color: #6666FF}\n";
    $html .= "   .b4 {color: white; background-color: #0000FF}\n";

	
    $stmt=sprintf("SELECT group_id,group_color FROM osdial_inbound_groups WHERE group_id IN %s OR group_id LIKE 'A2A_%s%%';",$LOG['allowed_ingroupsSQL'],$company_prefix);
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$html .= "$stmt\n";}
	$INgroups_to_print = mysql_num_rows($rslt);
	if ($INgroups_to_print > 0) {
		$g=0;
		while ($g < $INgroups_to_print) {
			$row=mysql_fetch_row($rslt);
			$group_id[$g] = $row[0];
			$group_color[$g] = $row[1];
			$html .= "   .$group_id[$g] {color: black; background-color: $group_color[$g]}\n";
			$g++;
		}
	}
	
	$html .= "\n-->\n
	</STYLE>\n";

	$stmt = sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_allow_inbound='Y' AND campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
	$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$campaign_allow_inbound = $row[0];
	
	
	//$html .= "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";
	//$html .= "<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?RR=$RR&DB=$DB&group=$group&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">\n";
	
	$html .= "<TABLE align=center><TR><TD>\n";
	$html .= "<FONT SIZE=1>";
	
	$html .= "<div class=no-ul>";
	$html .= "<FORM ACTION=\"$PHP_SELF\" METHOD=GET>\n";
	$html .= "<input type=hidden name=ADD value=$ADD>\n";
	$html .= "<input type=hidden name=SUB value=$SUB>\n";
	$html .= "<input type=hidden name=campaign_id value=$campaign_id>\n";
	$html .= "<input type=hidden name=group value=$group>\n";
	$html .= "<input type=hidden name=RR value=$RR>\n";
	
	$html .= "<p class=centered>";
	$html .= "<font color=$default_text size=+1>CAMPAIGN DETAILS</font><br><br>";
	
	$html .= "<FONT COLOR=$default_text SIZE=2>";
	$html .= "Update:&nbsp;";
	if ($RR==38400) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=38400&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">Daily</a>&nbsp;&nbsp;"; 
	
	if ($RR==3600) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=3600&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">Hourly</a>&nbsp;&nbsp;";
	
	if ($RR==60) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=60&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">1min</a>&nbsp;&nbsp;";
	
	if ($RR==10) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=10&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">10sec</a>&nbsp;&nbsp;";
	
	if ($RR==4) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=4&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">4sec</a>&nbsp;&nbsp;";
	
	$html .= "<font size=2>";
	
	$html .= "-&nbsp;&nbsp;<a href=\"./admin.php?ADD=31&campaign_id=$group\">Modify</a>&nbsp;- ";
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=" . ($SUB - 1) . "&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=$adastats\">Summary</a>&nbsp;\n";
	$html .= "<br></font>\n";
	$html .= "</font>";
	
	$html .= "<font color=$default_text size=-1>Campaign:</font>\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=ADD VALUE=\"$ADD\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=SUB VALUE=\"$SUB\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=RR VALUE=\"$RR\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=DB VALUE=\"$DB\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=adastats VALUE=\"$adastats\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=SIPmonitorLINK VALUE=\"$SIPmonitorLINK\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=IAXmonitorLINK VALUE=\"$IAXmonitorLINK\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=usergroup VALUE=\"$usergroup\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=UGdisplay VALUE=\"$UGdisplay\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=UidORname VALUE=\"$UidORname\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=orderby VALUE=\"$orderby\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=SERVdisplay VALUE=\"$SERVdisplay\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=CALLSdisplay VALUE=\"$CALLSdisplay\">\n";
	$html .= "<INPUT TYPE=HIDDEN NAME=cpuinfo VALUE=\"$cpuinfo\">\n";
	$html .= "<SELECT SIZE=1 NAME=group>\n";
	$aasel=''; if ($group == "XXXX-ALL-ACTIVE-XXXX") $aasel = "selected";
	$html .= "<option value=\"XXXX-ALL-ACTIVE-XXXX\" $aasel>XXXX-ALL-ACTIVE-XXXX</option>\n";
	$outsel=''; if ($group == "XXXX-OUTBOUND-XXXX") $outsel = "selected";
	$html .= "<option value=\"XXXX-OUTBOUND-XXXX\" $outsel>XXXX-OUTBOUND-XXXX</option>\n";
	$insel=''; if ($group == "XXXX-INBOUND-XXXX") $insel = "selected";
	$html .= "<option value=\"XXXX-INBOUND-XXXX\" $insel>XXXX-INBOUND-XXXX</option>\n";
	$o=0;

    $group_name = '';
	while ($groups_to_print > $o)	{
        $gsel='';
		if ($groups[$o] == $group) {
            $gsel = 'selected';
            $group_name = $group_names[$o];
		}
		$html .= "<option $gsel value=\"$groups[$o]\">" . mclabel($groups[$o]) . " - $group_names[$o]</option>\n";
		$o++;
	}
	$html .= "</SELECT>\n";
	if ($UGdisplay > 0)	{
		$html .= "<SELECT SIZE=1 NAME=usergroup>\n";
		$html .= "<option value=\"\">ALL USER GROUPS</option>\n";
		$o=0;
		while ($usergroups_to_print > $o) {
			if ($usergroups[$o] == $usergroup) {
				$html .= "<option selected value=\"$usergroups[$o]\">$usergroups[$o]</option>\n";
			} else {
				$html .= "<option value=\"$usergroups[$o]\">$usergroups[$o]</option>\n";
			}
			$o++;
		}
		$html .= "</SELECT>\n";
	}
	$html .= "<INPUT type=submit NAME=SUBMIT VALUE=SUBMIT>";
	$html .= "<br>";

	
	if ($group) {
		$stmt=sprintf("SELECT auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout,dial_statuses,active FROM osdial_campaigns WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$stmt=sprintf("SELECT avg(auto_dial_level),min(dial_status_a),min(dial_status_b),min(dial_status_c),min(dial_status_d),min(dial_status_e),min(lead_order),min(lead_filter_id),sum(hopper_level),min(dial_method),avg(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),min(available_only_ratio_tally),min(adaptive_latest_server_time),min(local_call_time),avg(dial_timeout),min(dial_statuses),active FROM osdial_campaigns WHERE campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		if ($group=='XXXX-OUTBOUND-XXXX') {
			$stmt=sprintf("SELECT avg(auto_dial_level),min(dial_status_a),min(dial_status_b),min(dial_status_c),min(dial_status_d),min(dial_status_e),min(lead_order),min(lead_filter_id),sum(hopper_level),min(dial_method),avg(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),min(available_only_ratio_tally),min(adaptive_latest_server_time),min(local_call_time),avg(dial_timeout),min(dial_statuses),active FROM osdial_campaigns WHERE length(closer_campaigns)<6 AND campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		if ($group=='XXXX-INBOUND-XXXX') {
			$stmt=sprintf("SELECT avg(auto_dial_level),min(dial_status_a),min(dial_status_b),min(dial_status_c),min(dial_status_d),min(dial_status_e),min(lead_order),min(lead_filter_id),sum(hopper_level),min(dial_method),avg(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),min(available_only_ratio_tally),min(adaptive_latest_server_time),min(local_call_time),avg(dial_timeout),min(dial_statuses),active FROM osdial_campaigns WHERE length(closer_campaigns)>5 AND campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$DIALlev =		sprintf('%3.2f',$row[0]);
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
		$active = 	$row[19];

		$DIALstatuses = (preg_replace("/ -$|^ /","",$DIALstatuses));
		$DIALstatuses = (ereg_replace(' ',', ',$DIALstatuses));
		
		$stmt=sprintf("SELECT count(*) FROM osdial_hopper WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$stmt=sprintf("SELECT count(*) FROM osdial_hopper WHERE campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		if ($group=='XXXX-OUTBOUND-XXXX') {
			$stmt=sprintf("SELECT count(*) FROM osdial_hopper LEFT JOIN osdial_campaigns ON (osdial_hopper.campaign_id=osdial_campaigns.campaign_id) WHERE length(closer_campaigns)<6 AND osdial_hopper.campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		if ($group=='XXXX-INBOUND-XXXX') {
			$stmt=sprintf("SELECT count(*) FROM osdial_hopper LEFT JOIN osdial_campaigns ON (osdial_hopper.campaign_id=osdial_campaigns.campaign_id) WHERE length(closer_campaigns)>5 AND osdial_hopper.campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$VDhop = $row[0];
		
		$stmt=sprintf("SELECT dialable_leads,calls_today,drops_today,drops_answers_today_pct,differential_onemin,agents_average_onemin,balance_trunk_fill,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4,status_category_hour_count_1,status_category_hour_count_2,status_category_hour_count_3,status_category_hour_count_4,recycle_total,recycle_sched FROM osdial_campaign_stats WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$stmt=sprintf("SELECT sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),min(status_category_1),sum(status_category_count_1),min(status_category_2),sum(status_category_count_2),min(status_category_3),sum(status_category_count_3),min(status_category_4),sum(status_category_count_4),sum(status_category_hour_count_1),sum(status_category_hour_count_2),sum(status_category_hour_count_3),sum(status_category_hour_count_4),SUM(recycle_total),SUM(recycle_sched) FROM osdial_campaign_stats WHERE campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		if ($group=='XXXX-OUTBOUND-XXXX') {
			$stmt=sprintf("SELECT sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),min(status_category_1),sum(status_category_count_1),min(status_category_2),sum(status_category_count_2),min(status_category_3),sum(status_category_count_3),min(status_category_4),sum(status_category_count_4),sum(status_category_hour_count_1),sum(status_category_hour_count_2),sum(status_category_hour_count_3),sum(status_category_hour_count_4),SUM(recycle_total),SUM(recycle_sched) FROM osdial_campaign_stats,osdial_campaigns WHERE osdial_campaign_stats.campaign_id=osdial_campaigns.campaign_id AND length(closer_campaigns)<6 AND osdial_campaign_stats.campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		if ($group=='XXXX-INBOUND-XXXX') {
			$stmt=sprintf("SELECT sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),min(status_category_1),sum(status_category_count_1),min(status_category_2),sum(status_category_count_2),min(status_category_3),sum(status_category_count_3),min(status_category_4),sum(status_category_count_4),sum(status_category_hour_count_1),sum(status_category_hour_count_2),sum(status_category_hour_count_3),sum(status_category_hour_count_4),SUM(recycle_total),SUM(recycle_sched) FROM osdial_campaign_stats,osdial_campaigns WHERE osdial_campaign_stats.campaign_id=osdial_campaigns.campaign_id AND length(closer_campaigns)>5 AND osdial_campaign_stats.campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$DAleads =		$row[0];
		$callsTODAY =	$row[1];
		$dropsTODAY =	$row[2];
		$drpctTODAY =	sprintf('%3.2f',$row[3]);
		$diffONEMIN =	$row[4];
		$agentsONEMIN = sprintf('%3.2f',$row[5]);
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
		$VSCcat1hourtally = $row[16];
		$VSCcat2hourtally = $row[17];
		$VSCcat3hourtally = $row[18];
		$VSCcat4hourtally = $row[19];
		$recycle_total = $row[20];
		$recycle_sched = $row[21];
		
		if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) ) {
			$diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
			$diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
		} else {
			$diffpctONEMIN = '0.00';
		}
		
		$stmt=sprintf("SELECT sum(local_trunk_shortage) FROM osdial_campaign_server_stats WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$stmt=sprintf("SELECT sum(local_trunk_shortage) FROM osdial_campaign_server_stats WHERE campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		if ($group=='XXXX-OUTBOUND-XXXX') {
			$stmt=sprintf("SELECT sum(local_trunk_shortage) FROM osdial_campaign_server_stats,osdial_campaigns WHERE osdial_campaign_server_stats.campaign_id=osdial_campaigns.campaign_id AND length(closer_campaigns)<6 AND osdial_campaign_server_stats.campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		if ($group=='XXXX-INBOUND-XXXX') {
			$stmt=sprintf("SELECT sum(local_trunk_shortage) FROM osdial_campaign_server_stats,osdial_campaigns WHERE osdial_campaign_server_stats.campaign_id=osdial_campaigns.campaign_id AND length(closer_campaigns)>5 AND osdial_campaign_server_stats.campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
		}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$balanceSHORT = $row[0];

		$html .= "</td></tr><tr><td align=left>";
		$html .= "<font class=indented color=#1C4754 size=2><b>$group - $group_name</b></font>";
		if (ereg("^XXXX",$group)) {
			$html .= '';
		} elseif ($active=='Y') {
                        $html .="<font color='green' size='-1'>&nbsp;&nbsp;(Active)</font>";
                } else {
                        $html .="<font color='red'>&nbsp;&nbsp;(In-Active)</font>";
                }
		$html .= "</td></tr><tr><td align=center>";
		$html .= "<table class=indents cellpadding=0 cellspacing=0><TR>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Dial Level:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALlev&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Trunk Short/Fill:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $balanceSHORT / $balanceFILL&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Filter:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALfilter&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Time:</B></TD><TD ALIGN=LEFT><font size=2 color=$default_text>&nbsp; $NOW_TIME&nbsp;&nbsp;</TD>";
		$html .= "";
		$html .= "</TR>";
	
		if ($adastats > 1) {
			$html .= "<TR BGCOLOR=\"#CCCCCC\">";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Max Level:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $maxDIALlev&nbsp;&nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Dropped Max:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DROPmax%&nbsp;&nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Target Diff:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $targetDIFF&nbsp;&nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Intensity:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAintense&nbsp;&nbsp;</TD>";
			$html .= "</TR>";
		
			$html .= "<TR BGCOLOR=\"#CCCCCC\">";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Dial Timeout:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALtimeout&nbsp;&nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Taper Time:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $TAPERtime&nbsp;&nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Local Time:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $CALLtime&nbsp;&nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Avail Only:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAavailonly&nbsp;&nbsp;</TD>";
			$html .= "</TR>";
			
			$html .= "<TR BGCOLOR=\"#CCCCCC\">";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>DL Diff:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffONEMIN&nbsp;&nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>Diff:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffpctONEMIN%&nbsp;&nbsp;</TD>";
		    $html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Avg Agents:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $agentsONEMIN&nbsp;&nbsp;</TD>";
			$html .= "</TR>";
		}

		$html .= "<TR>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Dialable Leads:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DAleads&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Recycles/Sched:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $recycle_total / $recycle_sched&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Calls Today:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Dial Method:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALmethod&nbsp;&nbsp;</TD>";
		$html .= "</TR>";
		
		$html .= "<TR>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Hopper Level:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $HOPlev&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Drop/Answer:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY / $answersTODAY&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Statuses:</B></TD><TD ALIGN=LEFT colspan=3><font size=2>&nbsp; <span title=\"$DIALstatuses\">" . ellipse($DIALstatuses,40,true) . "</span>&nbsp;&nbsp;</TD>";
		$html .= "</TR>";
		
		$html .= "<TR>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Leads In Hopper:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $VDhop&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Drop %:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; ";
		if ($drpctTODAY >= $DROPmax) {
			$html .= "<font color=red><B>$drpctTODAY%</B></font>";
		} else {
			$html .= "$drpctTODAY%";
		}
		$html .= "&nbsp;&nbsp;</TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=$default_text><B>Order:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALorder&nbsp;&nbsp;</TD>";
		$html .= "</tr><tr>";
		if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
			$html .= "<TD ALIGN=right><font size=2 color=$default_text><B>$VSCcat1:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1tally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
			$html .= "<TD ALIGN=right><font size=2 color=$default_text><B>$VSCcat2:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2tally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) {
			$html .= "<TD ALIGN=right><font size=2 color=$default_text><B>$VSCcat3:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3tally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
			$html .= "<TD ALIGN=right><font size=2 color=$default_text><B>$VSCcat4:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4tally&nbsp;&nbsp;</td>\n";
		}
		$html .= "</tr><tr>";
		if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
			$html .= "<TD ALIGN=right><font size=2 color=$default_text><B>$VSCcat1/hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1hourtally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
			$html .= "<TD ALIGN=right><font size=2 color=$default_text><B>$VSCcat2/hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2hourtally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) {
			$html .= "<TD ALIGN=right><font size=2 color=$default_text><B>$VSCcat3/hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3hourtally&nbsp;&nbsp;</td>\n";
		}
		if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
			$html .= "<TD ALIGN=right><font size=2 color=$default_text><B>$VSCcat4/hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4hourtally&nbsp;&nbsp;</td>\n";
		}
		$html .= "</TR>";
		
		$html .= "<TR>";
		$html .= "<TD ALIGN=LEFT COLSPAN=8>";
		
		if ($adastats<2) {
			$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=2&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\"><font size=1>VIEW MORE SETTINGS</font></a>";
		} else {
			$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=1&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\"><font size=1>VIEW LESS SETTINGS</font></a>";
		}
		if ($UGdisplay>0) {
			$html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=0&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\"><font size=1>HIDE USER GROUP</font></a>";
		} else {
			$html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=1&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\"><font size=1>VIEW USER GROUP</font></a>";
		}
		if ($UidORname>0) {
			$html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=0&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\"><font size=1>SHOW AGENT ID</font></a>";
		} else {
			$html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=1&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\"><font size=1>SHOW AGENT NAME</font></a>";
		}
		if ($SERVdisplay>0) {
			$html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=0&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\"><font size=1>HIDE SERVER INFO</font></a>";
		} else {
			$html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=1&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\"><font size=1>SHOW SERVER INFO</font></a>";
		}
		if ($CALLSdisplay>0) {
			$html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=0&cpuinfo=$cpuinfo\"><font size=1>HIDE WAITING CALLS DETAIL</font></a>";
		} else {
			$html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=1&cpuinfo=$cpuinfo\"><font size=1>SHOW WAITING CALLS DETAIL</font></a>";
		}
		$html .= "</TD>";
		$html .= "</TR>";
		$html .= "</TABLE>";
		
		$html .= "</FORM>\n\n";
		$html .= "<br>";
	}

	###################################################################################
	###### INBOUND/OUTBOUND CALLS
	###################################################################################
	if ($campaign_allow_inbound > 0) {
		$stmt=sprintf("SELECT closer_campaigns FROM osdial_campaigns WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$closer_campaigns = preg_replace("/^ | -$/","",$row[0]);
		$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
		$closer_campaigns = "'$closer_campaigns'";
	
		$stmtB=sprintf("FROM osdial_auto_calls WHERE campaign_id IN %s AND status NOT IN('XFER') AND ( (call_type='IN' AND campaign_id IN(%s)) OR (campaign_id='%s' AND call_type IN('OUT','OUTBALANCE')) ) ORDER BY campaign_id,call_time;",$LOG['allowed_campaignsSQL'],$closer_campaigns,mres($group));
	} else {
		if ($group=='XXXX-ALL-ACTIVE-XXXX') {
			$groupSQL = '';
		} elseif ($group=='XXXX-OUTBOUND-XXXX') {
			$groupSQL = '';
		} elseif ($group=='XXXX-INBOUND-XXXX') {
			$groupSQL = '';
		} else {
			$groupSQL = sprintf(" AND campaign_id='%s'",mres($group));
		}
	
		$stmtB=sprintf("FROM osdial_auto_calls WHERE campaign_id IN %s AND status NOT IN('XFER') %s ORDER BY campaign_id,call_time;", $LOG['allowed_campaignsSQL'],$groupSQL);
	}
	if ($CALLSdisplay > 0) {
		$stmtA = "SELECT status,campaign_id,phone_number,server_ip,UNIX_TIMESTAMP(call_time),call_type";
	} else {
		$stmtA = "SELECT status";
	}
	
	
	$k=0;
	$stmt = "$stmtA $stmtB";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$html .= "$stmt\n";}
	
	$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0) {
		$i=0;
		$out_total=0;
		$out_ring=0;
		$out_live=0;
		while ($i < $parked_to_print)	{
			$row=mysql_fetch_row($rslt);
	
			if (eregi("LIVE",$row[0])) {
				$out_live++;
	
				if ($CALLSdisplay > 0) {
					$CDstatus[$k] =			$row[0];
					$CDcampaign_id[$k] =	$row[1];
					$CDphone_number[$k] =	$row[2];
					$CDserver_ip[$k] =		$row[3];
					$CDcall_time[$k] =		$row[4];
					$CDcall_type[$k] =		$row[5];
					$k++;
				}
			} else {
				if (eregi("CLOSER",$row[0])) {
					$nothing=1;
				} else {
					$out_ring++;
				}
			}
	
			$out_total++;
			$i++;
		}
	
		if ($out_live > 0) {$F='<FONT class="r1">'; $FG='</FONT>';}
		if ($out_live > 4) {$F='<FONT class="r2">'; $FG='</FONT>';}
		if ($out_live > 9) {$F='<FONT class="r3">'; $FG='</FONT>';}
		if ($out_live > 14) {$F='<FONT class="r4">'; $FG='</FONT>';}

		if ($campaign_allow_inbound > 0) {
			$html .= "$NFB$out_total$NFE <font color=blue>current active calls</font> &nbsp; &nbsp; &nbsp; \n";
		} else {
			$html .= "$NFB$out_total$NFE <font color=blue>calls being placed</font> &nbsp; &nbsp; &nbsp; \n";
		}
		
		$html .= "$NFB$out_ring$NFE <font color=blue>calls ringing</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
		$html .= "$NFB$F &nbsp;$out_live $FG$NFE <font color=blue>calls waiting for agents</font> &nbsp; &nbsp; &nbsp; \n";
	} else {
		$html .= "&nbsp;<font color=red>NO LIVE CALLS WAITING</font>&nbsp;";
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
	$Chtml = '';
	$Chtml .= "<font color=$default_text>&nbsp;&nbsp;Calls Waiting                      $NOW_TIME\n";
	//$Chtml .= "+--------+--------------+--------------+-----------------+---------+------------+\n";
	$Chtml .=$LNtopleft.HorizLine(8).$LNtopdown.HorizLine(14).$LNtopdown.HorizLine(14).$LNtopdown.HorizLine(17).$LNtopdown.HorizLine(9).$LNtopdown.HorizLine(12).$LNtopright."<br>";
	$Chtml .="$LNleft STATUS $LNcenterbar CAMPAIGN     $LNcenterbar PHONE NUMBER $LNcenterbar SERVER_IP       $LNcenterbar DIALTIME$LNcenterbar CALL TYPE  $LNright\n";
	$Chtml .=$LNcenterleft.CenterLine(8).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."<br>";
	//$Chtml .= "| STATUS | CAMPAIGN     | PHONE NUMBER | SERVER_IP       | DIALTIME| CALL TYPE  |\n";
	//$Chtml .= "+--------+--------------+--------------+-----------------+---------+------------+\n";
	
	/*
	$Chtml .=$LNtopleft.HorizLine(8).$LNtopdown.HorizLine(14).HorizLine(14).$LNtopdown.HorizLine(17).$LNtopdown.HorizLine(9).$LNtopdown.HorizLine(12).$LNtopright."<br>";
	$Chtml .="$LNleft STATUS $LNcenterbar CAMPAIGN     $LNcenterbar PHONE NUMBER $LNcenterbar SERVER_IP       $LNcenterbar DIALTIME$LNcenterbar CALL TYPE  $LNright\n";
	$Chtml .=$LNbottomleft.HorizLine(8).$LNtopup.HorizLine(14).$LNtopup.HorizLine(14).$LNtopup.HorizLine(17).$LNtopup.HorizLine(9).$LNtopup.HorizLine(12).$LNbottomright;
	*/
	
	$p=0;
	while($p<$k) {
		$Cstatus =			sprintf("%-6s", $CDstatus[$p]);
		$Ccampaign_id =		sprintf("%-12s", mclabel($CDcampaign_id[$p]));
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
		if ($CDcall_type[$p] == 'IN')	{
			$G="<SPAN class=\"$CDcampaign_id[$p]\"><B>"; $EG='</B></SPAN>';
		}
		$Chtml .= "$LNleft $G$Cstatus$EG $LNcenterbar $G$Ccampaign_id$EG $LNcenterbar $G$Cphone_number$EG $LNcenterbar $G$Cserver_ip$EG $LNcenterbar $G$Ccall_time_MS$EG $LNcenterbar $G$Ccall_type$EG $LNright\n";
	
		$p++;
	}
	
	//$Chtml .= "+--------+--------------+--------------+-----------------+---------+------------+\n\n";
	$Chtml .=$LNbottomleft.HorizLine(8).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."<br></font>";
	
	if ($p<1) {$Chtml='';}
	
	###################################################################################
	###### TIME ON SYSTEM
	###################################################################################
	
	
	$agent_incall=0;
	$agent_ready=0;
	$agent_paused=0;
	$agent_total=0;
	$Ahtml = '';
	$Ahtml .= "<font color=$default_text size=1 face=fixed,monospace>&nbsp;&nbsp;Agents Time On Calls Campaign: $group                    $NOW_TIME\n";
	
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
	$HTuser =		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=userup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">USER</a>          &#x2502;";
	$HDusergroup =		HorizLine(14)."&#x2564;"; //14
	$HTusergroup =		" <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=groupup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">USER GROUP</a>   &#x2502;";
    $HXusergroup =      $LNcentcross.CenterLine(14);
    $HBusergroup =      $LNbottomup.HorizLine(14);
	$HDsessionid =		HorizLine(11).HorizLine(7)."&#x2564;"; //10
	$HTsessionid =		"    SESSIONID     &#x2502;";
    $HXmonitor =      CenterLine(7);
    $HBmonitor =      HorizLine(7);
	$HDstatus =		HorizLine(10)."&#x2564;"; //10
	$HTstatus =		"  STATUS  &#x2502;";
	$HDserver_ip =		HorizLine(17)."&#x2564;"; //17
	$HTserver_ip =		"    SERVER IP    &#x2502;";
	$HDcall_server_ip =	HorizLine(17)."&#x2564;"; //17
	$HTcall_server_ip =	" CALL SERVER IP  &#x2502;";
    $HXserver =      $LNcentcross.CenterLine(17).$LNcentcross.CenterLine(17);
    $HBserver =      $LNbottomup.HorizLine(17).$LNbottomup.HorizLine(17);
	$HDtime =			HorizLine(9)."&#x2564;"; //9
	$HTtime =			"&nbsp;&nbsp;<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=timeup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">MM:SS</a>  &#x2502;";
	$HDcampaign =		HorizLine(12)."&#x2557;"; //12
	$HTcampaign =		"&nbsp;&nbsp;<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=campaignup&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">CAMPAIGN</a>  &#x2551;";
	
	if ($UGdisplay < 1)	{
		$HDusergroup =	'';
		$HTusergroup =	'';
		$HXusergroup =	'';
		$HBusergroup =	'';
	}

	if ( ($SIPmonitorLINK<1) and ($IAXmonitorLINK<1) ) {
	    $HDsessionid = HorizLine(11)."&#x2564;";
	    $HTsessionid = " SESSIONID &#x2502;";
    	$HXmonitor = '';
    	$HBmonitor = '';
	}

	if ($SERVdisplay < 1)	{
		$HDserver_ip =		'';
		$HTserver_ip =		'';
		$HDcall_server_ip =	'';
		$HTcall_server_ip =	'';
		$HXserver =	'';
		$HBserver =	'';
	}
		
	$Aline  = "$LNtopleft$HDstation$HDuser$HDusergroup$HDsessionid$HDstatus$HDserver_ip$HDcall_server_ip$HDtime$HDcampaign\n";
	$Bline  = "$LNleft$HTstation$HTuser$HTusergroup$HTsessionid$HTstatus$HTserver_ip$HTcall_server_ip$HTtime$HTcampaign\n";
	$Cline  = $LNcenterleft.CenterLine(12).$LNcentcross.CenterLine(20).$HXusergroup.$LNcentcross.CenterLine(11).$HXmonitor.$LNcentcross.CenterLine(10).$HXserver.$LNcentcross.CenterLine(9).$LNcentcross.CenterLine(12).$LNcentright."\n";
	$Dline  = $LNbottomleft.HorizLine(12).$LNbottomup.HorizLine(20).$HBusergroup.$LNbottomup.HorizLine(11).$HBmonitor.$LNbottomup.HorizLine(10).$HBserver.$LNbottomup.HorizLine(9).$LNbottomup.HorizLine(12).$LNbottomright."</font>\n";

	
	$Ahtml .= "$Aline";
	$Ahtml .= "$Bline";
	$Ahtml .= "$Cline";
	
	if ($orderby=='timeup') {$orderSQL='status,last_call_time';}
	if ($orderby=='timedown') {$orderSQL='status desc,last_call_time desc';}
	if ($orderby=='campaignup') {$orderSQL='campaign_id,status,last_call_time';}
	if ($orderby=='campaigndown') {$orderSQL='campaign_id desc,status desc,last_call_time desc';}
	if ($orderby=='groupup') {$orderSQL='user_group,status,last_call_time';}
	if ($orderby=='groupdown') {$orderSQL='user_group desc,status desc,last_call_time desc';}
	if ($UidORname > 0) {
		if ($orderby=='userup') {$orderSQL='full_name,status,last_call_time';}
		if ($orderby=='userdown') {$orderSQL='full_name desc,status desc,last_call_time desc';}
	} else {
		if ($orderby=='userup') {$orderSQL='osdial_live_agents.user';}
		if ($orderby=='userdown') {$orderSQL='osdial_live_agents.user desc';}
	}
	
	if ($group=='XXXX-ALL-ACTIVE-XXXX') {
		$groupSQL = '';
	} elseif ($group=='XXXX-OUTBOUND-XXXX') {
		$groupSQL = ' and length(osdial_live_agents.closer_campaigns)<6';
	} elseif ($group=='XXXX-INBOUND-XXXX') {
		$groupSQL = ' and length(osdial_live_agents.closer_campaigns)>5';
	} else {
		$groupSQL = " and campaign_id='" . mysql_real_escape_string($group) . "'";
	}
	if (strlen($usergroup)<1) {
		$usergroupSQL = '';
	} else {
		$usergroupSQL = " and user_group='" . mysql_real_escape_string($usergroup) . "'";
	}
	
	$stmt=sprintf("SELECT extension,osdial_live_agents.user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,osdial_live_agents.campaign_id,osdial_users.user_group,osdial_users.full_name,osdial_live_agents.comments,lead_id FROM osdial_live_agents,osdial_users WHERE campaign_id IN %s AND osdial_live_agents.user=osdial_users.user %s %s ORDER BY %s;",$LOG['allowed_campaignsSQL'],$groupSQL,$usergroupSQL,$orderSQL);
	
	#$stmt="select extension,osdial_live_agents.user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id,osdial_users.user_group,osdial_users.full_name from osdial_live_agents,osdial_users where osdial_live_agents.user=osdial_users.user and campaign_id='" . mysql_real_escape_string($group) . "' order by $orderSQL;";
	
	$rslt=mysql_query($stmt, $link);
	if ($DB) {$html .= "$stmt\n";}
	
	$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0) {
		$i=0;
		$agentcount=0;
		while ($i < $talking_to_print) {
			$row=mysql_fetch_row($rslt);
			if (eregi("READY|PAUSED",$row[3])) {
				$row[5]=$row[6];
			}
			if ($non_latin < 1) {
				$extension = eregi_replace('Local/',"",$row[0]);
				$extension =		sprintf("%-10s", $extension);
				while(strlen($extension)>10) {
					$extension = substr("$extension", 0, -1);
				}
			} else {
				$extension = eregi_replace('Local/',"",$row[0]);
				$extension =		sprintf("%-40s", $extension);
				while(mb_strlen($extension, 'utf-8')>10) {
					$extension = mb_substr("$extension", 0, -1,'utf8');
				}
			}
			$Luser =			$row[1];
			$user =				sprintf("%-18s", mclabel($row[1]));
			$Lsessionid =		$row[2];
			$sessionid =		sprintf("%-9s", $row[2]);
			$Lstatus =			$row[3];
			$status =			sprintf("%-6s", $row[3]);
			$server_ip =		sprintf("%-15s", $row[4]);
			$call_server_ip =	sprintf("%-15s", $row[7]);
			$campaign_id =	sprintf("%-10s", mclabel($row[8]));
			$comments=		$row[11];
			$lead_id=		$row[12];

                	$stmtB = "SELECT active FROM osdial_campaigns WHERE campaign_id='$row[8]';";
	            	$rsltB=mysql_query($stmtB, $link);
			$rowB=mysql_fetch_row($rsltB);
			$campaign_active=$rowB[0];

            if ($lead_id > 0) {
                $stmtB = "SELECT status FROM osdial_list WHERE lead_id='$lead_id' AND status LIKE 'V%';";
	            $rsltB=mysql_query($stmtB, $link);
			    $rowB=mysql_fetch_row($rsltB);
                $lead_status = sprintf("%-6s",$rowB[0]);
            }
                    
	
			if (eregi("INCALL",$Lstatus)) {
				if ( (eregi("AUTO",$comments)) or (eregi("REMOTE",$comments)) or (strlen($comments)<1) ) {
					$CM='A';
				} else {
					if (eregi("INBOUND",$comments)) {
						$CM='I';
					} else {
						$CM='M';
					}
				} 
			} else {
				$CM=' ';
			}
	
			if ($UGdisplay > 0) {
				if ($non_latin < 1) {
					$user_group =		sprintf("%-12s", mclabel($row[9]));
					while(strlen($user_group)>12) {
						$user_group = substr("$user_group", 0, -1);
					}
				} else {
					$user_group =		sprintf("%-40s", mclabel($row[9]));
					while(mb_strlen($user_group, 'utf-8')>12) {
						$user_group = mb_substr("$user_group", 0, -1,'utf8');
					}
				}
			}
			if ($UidORname > 0) {
				if ($non_latin < 1) {
					$user =		sprintf("%-18s", $row[10]);
					while(strlen($user)>18) {
						$user = substr("$user", 0, -1);
					}
				} else {
					$user =		sprintf("%-40s", $row[10]);
					while(mb_strlen($user, 'utf-8')>18) {
						$user = mb_substr("$user", 0, -1,'utf8');
					}
				}
			}
			if (!eregi("INCALL|QUEUE",$row[3])) {
				$call_time_S = ($STARTtime - $row[6]);
			} else {
				$call_time_S = ($STARTtime - $row[5]);
			}
	
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
			if ($Lstatus=='INCALL') {
				if ($call_time_S >= 10) {$G='<SPAN class="thistle">'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 1) {$G='<SPAN class="violet">'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 5) {$G='<SPAN class="purple">'; $EG='</B></SPAN>';}
		#		if ($call_time_M_int >= 10) {$G='<SPAN class="purple"><B>'; $EG='</B></SPAN>';}
		
			}
			if (eregi("PAUSED",$row[3])) {
				if ($call_time_M_int >= 360) {
					$i++; continue;
				} else {
					$agent_paused++;  $agent_total++;
					$G=''; $EG='';
					if ($call_time_S >= 10) {$G='<SPAN class="khaki">'; $EG='</B></SPAN>';}
					if ($call_time_M_int >= 1) {$G='<SPAN class="yellow">'; $EG='</B></SPAN>';}
					if ($call_time_M_int >= 5) {$G='<SPAN class="olive">'; $EG='</B></SPAN>';}
				}
			}
	#		if ( (strlen($row[7])> 4) and ($row[7] != "$row[4]") )
	#				{$G='<SPAN class="orange"><B>'; $EG='</B></SPAN>';}
	
			if ( (eregi("INCALL",$status)) or (eregi("QUEUE",$status)) ) {$agent_incall++;  $agent_total++;}
			if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {$agent_ready++;  $agent_total++;}
			if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {
				$G='<SPAN class="lightblue">'; $EG='</B></SPAN>';
				if ($call_time_M_int >= 1) {$G='<SPAN class="blue">'; $EG='</B></SPAN>';}
				if ($call_time_M_int >= 5) {$G='<SPAN class="midnightblue">'; $EG='</B></SPAN>';}
			}
	
			$L='';
			$R='';
			if ($SIPmonitorLINK==1) {$L="<a href=\"sip:6$Lsessionid@$server_ip\">LISTEN</a> ";   $R='';}
			if ($IAXmonitorLINK==1) {$L="<a href=\"iax:6$Lsessionid@$server_ip\">LISTEN</a> ";   $R='';}
			if ($SIPmonitorLINK==2) {$R=" <a href=\"sip:$Lsessionid@$server_ip\">BARGE</a> ";}
			if ($IAXmonitorLINK==2) {$R=" <a href=\"iax:$Lsessionid@$server_ip\">BARGE</a> ";}
	
			//if ($UGdisplay > 0)	{$UGD = " $G$user_group$EG $LNcenterbar";}
			if ($UGdisplay > 0)	{
				$UGD = " $user_group $LNcenterbar";
			} else {
				$UGD = "";
			}
	
			//if ($SERVdisplay > 0)	{$SVD = "$G$server_ip$EG $LNcenterbar $G$call_server_ip$EG $LNcenterbar ";}
			if ($SERVdisplay > 0)	{
				$SVD = "$server_ip $LNcenterbar $call_server_ip $LNcenterbar ";
			} else {
				$SVD = "";
			}
	
			$agentcount++;

            if (eregi("INCALL",$status) and $lead_status != "" and $lead_status != "      ") {
                $status = $lead_status;
            }
	
			$disp_agent = 1;
			if ($group == "XXXX-ALL-ACTIVE-XXXX" and $campaign_active == 'N') {
				$disp_agent = 0;
			}

			if ($disp_agent) {
				$Ahtml .= "$LNleft $G$extension $LNcenterbar <a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$Luser\" target=\"_blank\">$G$user$EG</a> $LNcenterbar$UGD $sessionid$L$R $LNcenterbar $status $CM $LNcenterbar $SVD$call_time_MS $LNcenterbar $campaign_id$EG $LNright\n";
			}
	
			$i++;
		}

		$Ahtml .= "$Dline";
		
		$Ahtml .= "  $agentcount <font color=$default_text>agents logged in on all servers</font>\n";
		
		
		if ($agent_ready > 0) {$B='<FONT class="b1">'; $BG='</FONT>';}
		if ($agent_ready > 4) {$B='<FONT class="b2">'; $BG='</FONT>';}
		if ($agent_ready > 9) {$B='<FONT class="b3">'; $BG='</FONT>';}
		if ($agent_ready > 14) {$B='<FONT class="b4">'; $BG='</FONT>';}
	
	
		$html .= "\n<BR>\n";

		$html .= "$NFB$agent_total$NFE <font color=blue>agents logged in</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
		$html .= "$NFB$agent_incall$NFE <font color=blue>agents in calls</font> &nbsp; &nbsp; &nbsp; \n";
		$html .= "&nbsp;$NFB$B$agent_ready$BG$NFE <font color=blue>agents waiting</font> &nbsp; &nbsp; &nbsp; \n";
		$html .= "$NFB$agent_paused$NFE <font color=blue>paused agents</font> &nbsp; &nbsp; &nbsp; \n";
		
		$html .= "<PRE><FONT SIZE=2>";
		$html .= "";
		$html .= "$Chtmnl";
		$html .= "$Ahtml";
		
		$html .= "<br><br>";
		$html .= "<table width=730><tr><td>";
		$html .= "  <font color=$default_text><SPAN class=\"khaki\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 10 seconds</B><br>";
		$html .= "  <SPAN class=\"yellow\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 1 minute</B><br>";
		$html .= "  <SPAN class=\"olive\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Paused > 5 minutes</B></font>";
		$html .= "</td><td>";
		$html .= "  <font color=$default_text><SPAN class=\"lightblue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call</B><br>";
		$html .= "  <SPAN class=\"blue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call > 1 minute</B><br>";
		$html .= "  <SPAN class=\"midnightblue\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - Waiting for call > 5 minutes</B>";
		$html .= "</td><td>";
		$html .= "  <font color=$default_text><SPAN class=\"thistle\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 10 seconds</B><br>";
		$html .= "  <SPAN class=\"violet\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 1 minute</B><br>";
		$html .= "  <SPAN class=\"purple\"><B>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN> - On call > 5 minutes</B>";
		$html .= "</td></tr></table>";
		
		if (file_exists($pref . 'resources.txt')) {
			$html .= "<br><br><br>";
            $html .= "<center>";
			if ($cpuinfo == 0 ) {
				$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=0\"><font size=1><b>STANDARD INFO</b></font></a>";
				$html .= " - ";
				$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=1\"><font size=1>EXTENDED INFO</font></a>";
				eval("\$html .= \"" . file_get_contents($pref . 'resources.txt') . "\";");
			} else {
				$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=0\"><font size=1>STANDARD INFO</font></a>";
				$html .= " - ";
				$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=1\"><font size=1><b>EXTENDED INFO</b></font></a>";
				eval("\$html .= \"" . file_get_contents($pref . 'resources-xtd.txt') . "\";");
			}
            $html .= "</center>";
		} else {
			$load_ave = rtrim(getloadavg());
			if (!$load_ave>0) $load_ave='-.--';
		
			$Ahtml="<br><pre><font face=Fixed,monospace SIZE=1>";
			if (file_exists($pref . 'S1_load.txt')) {
				$s1_load = file($pref . 'S1_load.txt');
				list( $line_num, $line ) = each( $s1_load );
				$load_ave_s1=rtrim($line);
				if (!$load_ave_s1>0) $load_ave_s1='-.--';
				$Ahtml .= "  <font color=$default_text>Web Srvr Load Average:</font> $load_ave\n";
				$Ahtml .= "  <font color=$default_text>SQL Srvr Load Average:</font> $load_ave_s1\n";
			} elseif (!file_exists($pref . 'D1_load.txt')&& !file_exists($pref . 'D2_load.txt') && !file_exists($pref . 'D3_load.txt') && !file_exists($pref . 'D4_load.txt') && !file_exists($pref . 'D5_load.txt') && !file_exists($pref . 'D6_load.txt')) {
				$Ahtml .= "  <font color=$default_text>Dialer   Load Average:</font> $load_ave\n";
			} else {
				$Ahtml .= "  <font color=$default_text>SQL/Web  Load Average:</font> $load_ave\n";
			}
			if (file_exists($pref . 'D1_load.txt')) {
				$d1_load = file($pref . 'D1_load.txt');
				list( $line_num, $line ) = each( $d1_load );
				$load_ave_d1=rtrim($line);
				if (!$load_ave_d1>0) $load_ave_d1='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 1 Load Average:</font> $load_ave_d1\n";
			}
			if (file_exists($pref . 'D2_load.txt')) {
				$d2_load = file($pref . 'D2_load.txt');
				list( $line_num, $line ) = each( $d2_load );
				$load_ave_d2=rtrim($line);
				if (!$load_ave_d2>0) $load_ave_d2='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 2 Load Average:</font> $load_ave_d2\n";
			}
			if (file_exists($pref . 'D3_load.txt')) {
				$d3_load = file($pref . 'D3_load.txt');
				list( $line_num, $line ) = each( $d3_load );
				$load_ave_d3=rtrim($line);
				if (!$load_ave_d3>0) $load_ave_d3='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 3 Load Average:</font> $load_ave_d3\n";
			}
			if (file_exists($pref . 'D4_load.txt')) {
				$d4_load = file($pref . 'D4_load.txt');
				list( $line_num, $line ) = each( $d4_load );
				$load_ave_d4=rtrim($line);
				if (!$load_ave_d4>0) $load_ave_d4='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 4 Load Average:</font> $load_ave_d4\n";
			}
			if (file_exists($pref . 'D5_load.txt')) {
				$d5_load = file($pref . 'D5_load.txt');
				list( $line_num, $line ) = each( $d5_load );
				$load_ave_d5=rtrim($line);
				if (!$load_ave_d5>0) $load_ave_d5='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 5 Load Average:</font> $load_ave_d5\n";
			}
			if (file_exists($pref . 'D6_load.txt')) {
				$d6_load = file($pref . 'D6_load.txt');
				list( $line_num, $line ) = each( $d6_load );
				$load_ave_d6=rtrim($line);
				if (!$load_ave_d6>0) $load_ave_d6='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 6 Load Average:</font> $load_ave_d6\n";
			}
			//$html .= "<tr><td colspan=10>";
			$html .= "$Ahtml";
			$html .= "</pre>";
		}
	} else {
	
		$html .= "&nbsp;&nbsp;<font color=red>&bull;&nbsp;&nbsp;NO AGENTS ON CALLS</font> \n";
		
		if (file_exists($pref . 'resources.txt')) {
			$html .= "<br><br><br>";
            $html .= "<center>";
			if ($cpuinfo == 0 ) {
				$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=0\"><font size=1><b>STANDARD INFO</b></font></a>";
				$html .= " - ";
				$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=1\"><font size=1>EXTENDED INFO</font></a>";
				eval("\$html .= \"" . file_get_contents($pref . 'resources.txt') . "\";");
			} else {
				$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=0\"><font size=1>STANDARD INFO</font></a>";
				$html .= " - ";
				$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=1\"><font size=1><b>EXTENDED INFO</b></font></a>";
				eval("\$html .= \"" . file_get_contents($pref . 'resources-xtd.txt') . "\";");
			}
            $html .= "</center>";
		} else {
			$Ahtml = "<br><br>";
			$load_ave = rtrim(getloadavg());
			if (!$load_ave>0) $load_ave='-.--';
		
			$Ahtml="<br><pre><font face=Fixed,monospace SIZE=1>";
			if (file_exists($pref . 'S1_load.txt')) {
				$s1_load = file($pref . 'S1_load.txt');
				list( $line_num, $line ) = each( $s1_load );
				$load_ave_s1=rtrim($line);
				if (!$load_ave_s1>0) $load_ave_s1='-.--';
				$Ahtml .= "  <font color=$default_text>Web Srvr Load Average:</font> $load_ave\n";
				$Ahtml .= "  <font color=$default_text>SQL Srvr Load Average:</font> $load_ave_s1\n";
			} elseif (!file_exists($pref . 'D1_load.txt')&& !file_exists($pref . 'D2_load.txt') && !file_exists($pref . 'D3_load.txt') && !file_exists($pref . 'D4_load.txt') && !file_exists($pref . 'D5_load.txt') && !file_exists($pref . 'D6_load.txt')) {
				$Ahtml .= "  <font color=$default_text>Dialer   Load Average:</font> $load_ave\n";
			} else {
				$Ahtml .= "  <font color=$default_text>SQL/Web  Load Average:</font> $load_ave\n";
			}
			if (file_exists($pref . 'D1_load.txt')) {
				$d1_load = file($pref . 'D1_load.txt');
				list( $line_num, $line ) = each( $d1_load ) ;
				$load_ave_d1=rtrim($line);
				if (!$load_ave_d1>0) $load_ave_d1='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 1 Load Average:</font> $load_ave_d1\n";
			}
			if (file_exists($pref . 'D2_load.txt')) {
				$d2_load = file($pref . 'D2_load.txt');
				list( $line_num, $line ) = each( $d2_load );
				$load_ave_d2=rtrim($line);
				if (!$load_ave_d2>0) $load_ave_d2='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 2 Load Average:</font> $load_ave_d2\n";
			}
			if (file_exists($pref . 'D3_load.txt')) {
				$d3_load = file($pref . 'D3_load.txt');
				list( $line_num, $line ) = each( $d3_load );
				$load_ave_d3=rtrim($line);
				if (!$load_ave_d3>0) $load_ave_d3='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 3 Load Average:</font> $load_ave_d3\n";
			}
			if (file_exists($pref . 'D4_load.txt')) {
				$d4_load = file($pref . 'D4_load.txt');
				list( $line_num, $line ) = each( $d4_load );
				$load_ave_d4=rtrim($line);
				if (!$load_ave_d4>0) $load_ave_d4='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 4 Load Average:</font> $load_ave_d4\n";
			}
			if (file_exists($pref . 'D5_load.txt')) {
				$d5_load = file($pref . 'D5_load.txt');
				list( $line_num, $line ) = each( $d5_load );
				$load_ave_d5=rtrim($line);
				if (!$load_ave_d5>0) $load_ave_d5='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 5 Load Average:</font> $load_ave_d5\n";
			}
			if (file_exists($pref . 'D6_load.txt')) {
				$d6_load = file($pref . 'D6_load.txt');
				list( $line_num, $line ) = each( $d6_load );
				$load_ave_d6=rtrim($line);
				if (!$load_ave_d6>0) $load_ave_d6='-.--';
				$Ahtml .= "  <font color=$default_text>Dialer 6 Load Average:</font> $load_ave_d6\n";
			}
			$html .= "$Ahtml";
		}
		
	}
	$html .= "</pre>";
	
	#$html .= "</td>";
	#$html .= "<TABLE WIDTH='$page_width' BGCOLOR=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>";

    return $html;
}




?>
