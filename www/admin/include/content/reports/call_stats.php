<?php
# AST_VDADstats.php
# 
# Copyright (C) 2009  Matt Florell <osdial@gmail.com>        LICENSE: AGPLv2
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
# 60619-1718 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
# 61215-1139 - Added drop percentage of answered and round-2 decimal
# 71008-1436 - Added shift to be defined in dbconnect.php
# 71218-1155 - Added end_date for multi-day reports
# 80430-1920 - Added Customer hangup cause stats
# 80620-0031 - Fixed human answered calculation for drop perfentage
# 80709-0230 - Added time stats to call statuses
# 80717-2118 - Added calls/hour out of agent login time in status summary
# 80722-2049 - Added Status Category stats
# 81109-2341 - Added Productivity Rating
# 90225-1140 - Changed to multi-campaign capability
# 90310-2034 - Admin header
#
# 090511-1438 - Functionalize, add status grouping by comments, export to CSV
#


function report_call_stats() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $use_agent_log = get_variable("use_agent_log");
    $use_closer_log = get_variable("use_closer_log");
    $comment_grouping = get_variable("comment_grouping");
    $agent_hours = get_variable("agent_hours");
    $group = get_variable("group");
    $query_date = get_variable("query_date");
    $end_date = get_variable("end_date");
    $time_begin = get_variable("time_begin");
    $time_end = get_variable("time_end");
    $DB = get_variable("DB");
    $submit = get_variable("submit");
    $SUBMIT = get_variable("SUBMIT");

    $html = '';

    $NOW_DATE = date("Y-m-d");
    $NOW_TIME = date("Y-m-d H:i:s");
    $STARTtime = date("U");
    if (!isset($group)) {$group = '';}
    if (!isset($query_date)) {$query_date = $NOW_DATE;}
    if (!isset($end_date)) {$end_date = $NOW_DATE;}
    if ($query_date == '') {$query_date = $NOW_DATE;}
    if ($end_date == '') {$end_date = $NOW_DATE;}
    
    $stmt=sprintf("SELECT campaign_id FROM osdial_campaigns WHERE campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $campaigns_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $campaigns_to_print) {
        $row=mysql_fetch_row($rslt);
        $groups[$i] =$row[0];
        $i++;
    }

    $i=0;
    $group_string='|';
    $group_ct = count($group);
    $group_list = '';
    while($i < $group_ct) {
        $group_string .= "$group[$i]|";
        $group_SQL .= "'$group[$i]',";
        $groupQS .= "&group[]=$group[$i]";
        $group_list .= mclabel($group[$i]);
        if ($i != $group_ct - 1) $group_list .= ", ";
        $i++;
    }
    if ( (preg_match("/--ALL--/",$group_string) ) or ($group_ct < 1) ) {
        $group_SQLand = sprintf("AND campaign_id IN %s",$LOG['allowed_campaignsSQL']);
        $group_SQL = sprintf("WHERE campaign_id IN %s",$LOG['allowed_campaignsSQL']);
        $group_list = "--ALL--";
    } else {
        $group_SQL = preg_replace("/,$/",'',$group_SQL);
        $group_SQLand = sprintf("AND campaign_id IN %s AND campaign_id IN(%s)",$LOG['allowed_campaignsSQL'],$group_SQL);
        $group_SQL = sprintf("WHERE campaign_id IN %s AND campaign_id IN(%s)",$LOG['allowed_campaignsSQL'],$group_SQL);
    }

    $stmt="SELECT vsc_id,vsc_name FROM osdial_status_categories;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $statcats_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $statcats_to_print) {
        $row=mysql_fetch_row($rslt);
        $vsc_id[$i] =    $row[0];
        $vsc_name[$i] =    $row[1];
        $vsc_count[$i] = 0;
        $i++;
    }

    $short_header=1;


    $html .= "<br>\n";
    $html .= "<table align=center>\n";
    $html .= "  <tr>\n";
    $html .= "    <td>\n";
    $html .= "      <center><font color=$default_text size=4>CALL REPORT</font></center>\n";

    if ($time_begin == '') {
        $time_begin = '00:00';
    }
    if ($time_end == '') {
        $time_end = '23:59';
    }
    $html .= "      <div class=\"noprint\">\n";
    $html .= "      <form action=\"$PHP_SELF\" method=GET>\n";
    $html .= "      <input type=hidden name=agent_hours value=\"$agent_hours\">\n";
    $html .= "      <input type=hidden name=ADD value=\"$ADD\">\n";
    $html .= "      <input type=hidden name=SUB value=\"$SUB\">\n";
    $html .= "      <input type=hidden name=DB value=\"$DB\">\n";
    $html .= "      <table align=center bgcolor=$oddrows cellspacing=3>\n";
    $html .= "        <tr>\n";
    $html .= "          <td colspan=3 align=center>\n";
    $html .= "            <font face=\"dejavu sans,verdana,sans-serif\" color=$default_text size=2>\n";
    if (strlen($group[0]) > 1) {
        $html .= "              <a href=\"./admin.php?ADD=34&campaign_id=$group[0]\">MODIFY</a> | \n";
    } else {
        $html .= "              <a href=\"./admin.php?ADD=10\">CAMPAIGNS</a> | \n";
    }
    $html .= "              <a href=\"./admin.php?ADD=999999\">REPORTS</a>\n";
    $html .= "            </font><br><br>\n";
    $html .= "          </td>\n";
    $html .= "        </tr>\n";
    $html .= "        <tr>\n";
    $html .= "          <td> Dates:<br>\n";
    $html .= "            <script>\nvar cal1 = new CalendarPopup('caldiv1');\ncal1.showNavigationDropdowns();\n</script>\n";
    $html .= "            <input type=text name=query_date size=10 maxlength=10 value=\"$query_date\">\n";
    $html .= "            <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(formatDate(parseDate(document.forms[0].end_date.value).addDays(1),'yyyy-MM-dd'),null);cal1.select(document.forms[0].query_date,'acal1','yyyy-MM-dd'); return false;\" name=acal1 id=acal1>\n";
    $html .= "            <img width=12 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $html .= "            <input type=text name=time_begin size=5 maxlength=5 value=\"$time_begin\">\n";
    $html .= "            <br> to <br>\n";
    $html .= "            <input type=text name=end_date size=10 maxlength=10 value=\"$end_date\">\n";
    $html .= "            <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(null,formatDate(parseDate(document.forms[0].query_date.value).addDays(-1),'yyyy-MM-dd'));cal1.select(document.forms[0].end_date,'acal2','yyyy-MM-dd'); return false;\" name=acal2 id=acal2>\n";
    $html .= "            <img width=12 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $html .= "            <input type=text name=time_end size=5 maxlength=5 value=\"$time_end\">\n";
    $html .= "          </td>\n";
    $html .= "          <td> Campaigns:<br>\n";
    $html .= "            <select size=5 name=group[] multiple>\n";
    $gsel=''; if  (preg_match("/--ALL--/",$group_string)) $gsel = "selected";
    $html .= "              <option value=\"--ALL--\" $gsel>-- ALL CAMPAIGNS --</option>\n";
    $o=0;
    while ($campaigns_to_print > $o) {
        $gsel=''; if (preg_match("/$groups[$o]\|/",$group_string)) $gsel = "selected";
        $html .= "              <option value=\"$groups[$o]\" $gsel>" . mclabel($groups[$o]) . "</option>\n";
        $o++;
    }
    $html .= "            </select>\n";
    $html .= "          </td>\n";
    $html .= "          <td>\n";
    $uclc = ''; if ($use_closer_log) {$uclc = 'checked'; $use_agent_log=0;}
    $ualc = ''; if ($use_agent_log or $group=='') $ualc = 'checked';
    $cgc = ''; if ($comment_grouping) $cgc = 'checked';
    $html .= "            <br><input type=\"checkbox\" name=\"use_closer_log\" id=\"use_closer_log\" value=\"1\" $uclc><label for=\"use_closer_log\"> Include Closer/Inbound Stats</label><br />\n";
    $html .= "            <input type=\"checkbox\" name=\"use_agent_log\" id=\"use_agent_log\" value=\"1\" $ualc><label for=\"use_agent_log\"> Use Agent Log for Agent Stats</label><br />\n";
    $html .= "            <input type=\"checkbox\" name=\"comment_grouping\" id=\"comment_grouping\" value=\"1\" $cgc><label for=\"comment_grouping\"> Group Call Status by Extended Log Data</label><br /><br>\n";
    $html .= "          </td>\n";
    $html .= "        </tr>\n";
    $html .= "        <tr><td colspan=3>&nbsp;</td></tr>\n";
    $html .= "        <tr class=tabfooter>\n";
    $html .= "          <td colspan=3 align=center class=tabbutton>\n";
    $html .= "            <input type=submit name=submit value=submit>\n";
    $html .= "          </td>\n";
    $html .= "        </tr>\n";
    $html .= "      </table>\n";
    $html .= "      </form>\n";
    $html .= "      <div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";

    $html .= "      </div>\n\n";
    

    # If no campaign, return now.
    if (strlen($group[0]) < 1) {
        $html .= "    </td>\n";
        $html .= "  </tr>\n";
        $html .= "</table>\n";
        return $html;
    }

    $plain='';
    $table='';
    $query_date_BEGIN = "$query_date $time_begin:00";   
    $query_date_BEGIN = dateToServer($link,'first',$query_date_BEGIN,$webClientAdjGMT,'',$webClientDST,0);
    $query_date_END = "$end_date $time_end:59";
    $query_date_END = dateToServer($link,'first',$query_date_END,$webClientAdjGMT,'',$webClientDST,0);

    $html .= "<div class=onlyprint><pre>\n\n";
    $html .= "OSDIAL: Auto-dial Stats                             " . dateToLocal($link,'first',$NOW_TIME,$webClientAdjGMT,'',$webClientDST,1) . "\n";

    $html .= "\n";
    $html .= "Time range: $query_date_BEGIN to $query_date_END\n\n";

    $stmt = "SELECT closer_campaigns FROM osdial_campaigns $group_SQL;";
    $rslt=mysql_query($stmt, $link);
    $ccamps_to_print = mysql_num_rows($rslt);
    $c=0;
    while ($ccamps_to_print > $c) {
        $row=mysql_fetch_row($rslt);
        $closer_campaigns = $row[0];
        $closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
        $closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
        $closer_campaignsSQL .= "'$closer_campaigns',";
        $c++;
    }
    $closer_campaignsSQL = preg_replace("/,$/",'',$closer_campaignsSQL);
    $closer_SQLand = "and campaign_id IN($closer_campaignsSQL)";

    if ($use_closer_log) {
        $stmt="SELECT count(*),sum(length_in_sec) FROM ((select uniqueid,length_in_sec from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand) UNION (select uniqueid,length_in_sec from osdial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $closer_SQLand)) AS t;";
        $ccprint = preg_replace("/'',/","",$closer_campaignsSQL);
        $ccprint2 = preg_replace("/,/",", ",$ccprint);
        $ccprint2 = preg_replace("/'/","    ",$ccprint2);
        $ccprint = preg_replace("/,/","\n",$ccprint);
        $ccprint = preg_replace("/'/","    ",$ccprint);
        $html .= "In-Groups included in this report:\n";
        $html .= $ccprint . "\n\n";
    } else {
        $stmt="select count(*),sum(length_in_sec) from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand;";
    }
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $row=mysql_fetch_row($rslt);

    $html .= "</pre></div>";

    $html .= "<div class=onlyprint><pre>\n\n";
    $html .= "---------- TOTALS\n";

    $TOTALcalls =    sprintf("%10s", $row[0]);
    $TOTALsec =        $row[1];
    if ( ($row[0] < 1) or ($TOTALsec < 1) ) {
        $average_hold_seconds = '         0';
    } else {
        $average_hold_seconds = ($TOTALsec / $row[0]);
        $average_hold_seconds = round($average_hold_seconds, 2);
        $average_hold_seconds =    sprintf("%10s", $average_hold_seconds);
    }
    $TOTAVGseconds = $average_hold_seconds;
    $html .= "Total Calls placed from this Campaign:        $TOTALcalls\n";
    $html .= "Average Call Length for all Calls in seconds: $average_hold_seconds\n";
    $html .= "</pre></div>";


    $html .= "<div class=onlyprint><pre>\n\n";
    $html .= "---------- DROPS\n";

    if ($use_closer_log) {
        $stmt="SELECT count(*),sum(length_in_sec) FROM ((select uniqueid,length_in_sec from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status='DROP' and (length_in_sec <= 6000 or length_in_sec is null)) UNION (select uniqueid,length_in_sec from osdial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $closer_SQLand and status='DROP' and (length_in_sec <= 6000 or length_in_sec is null))) AS t;";
    } else {
        $stmt="select count(*),sum(length_in_sec) from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status='DROP' and (length_in_sec <= 6000 or length_in_sec is null);";
    }
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $row=mysql_fetch_row($rslt);

    $DROPcalls =    sprintf("%10s", $row[0]);
    $DROPcallsRAW =    $row[0];
    $DROPseconds =    $row[1];


    # GET LIST OF ALL STATUSES and create SQL from human_answered statuses
    $q=0;
    $stmt = "SELECT status,status_name,human_answered,category from osdial_statuses;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $statuses_to_print = mysql_num_rows($rslt);
    $p=0;
    while ($p < $statuses_to_print) {
        $row=mysql_fetch_row($rslt);
        $status[$q] =            $row[0];
        $status_name[$q] =        $row[1];
        $human_answered[$q] =    $row[2];
        $category[$q] =            $row[3];
        $statname_list["$status[$q]"] = "$status_name[$q]";
        $statcat_list["$status[$q]"] = "$category[$q]";
        if ($human_answered[$q]=='Y') {$camp_ANS_STAT_SQL .=     "'$row[0]',";}
        $q++;
        $p++;
    }

    $stmt = "SELECT distinct status,status_name,human_answered,category from osdial_campaign_statuses $group_SQL;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $statuses_to_print = mysql_num_rows($rslt);
    $p=0;
    while ($p < $statuses_to_print) {
        $row=mysql_fetch_row($rslt);
        $status[$q] =            $row[0];
        $status_name[$q] =        $row[1];
        $human_answered[$q] =    $row[2];
        $category[$q] =            $row[3];
        $statname_list["$status[$q]"] = "$status_name[$q]";
        $statcat_list["$status[$q]"] = "$category[$q]";
        if ($human_answered[$q]=='Y') {$camp_ANS_STAT_SQL .=     "'$row[0]',";}
        $q++;
        $p++;
    }
    $camp_ANS_STAT_SQL = preg_replace("/,$/",'',$camp_ANS_STAT_SQL);

    
    if ($use_closer_log) {
        $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status IN($camp_ANS_STAT_SQL)) UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $closer_SQLand and status IN($camp_ANS_STAT_SQL))) AS t;";
    } else {
        $stmt="select count(*) from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status IN($camp_ANS_STAT_SQL);";
    }
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $row=mysql_fetch_row($rslt);

    $ANSWERcalls =    $row[0];
    
    if ( ($DROPcalls < 1) or ($TOTALcalls < 1) ) {
        $DROPpercent = '0';
    } else {
        $DROPpercent = (($DROPcallsRAW / $TOTALcalls) * 100);
        $DROPpercent = round($DROPpercent, 2);
    }
    
    if ( ($DROPcalls < 1) or ($ANSWERcalls < 1) ) {
        $DROPANSWERpercent = '0';
    } else {
        $DROPANSWERpercent = (($DROPcallsRAW / $ANSWERcalls) * 100);
        $DROPANSWERpercent = round($DROPANSWERpercent, 2);
    }
    
    if ( ($DROPseconds < 1) or ($DROPcallsRAW < 1) ) {
        $average_hold_seconds = '         0';
    } else {
        $average_hold_seconds = ($DROPseconds / $DROPcallsRAW);
        $average_hold_seconds = round($average_hold_seconds, 2);
        $average_hold_seconds =    sprintf("%10s", $average_hold_seconds);
    }
    
    $html .= "Total DROP Calls:                             $DROPcalls  $DROPpercent%\n";
    $html .= "Percent of DROP Calls taken out of Answers:   $DROPcalls / $ANSWERcalls  $DROPANSWERpercent%\n";
    $html .= "Average Length for DROP Calls in seconds:     $average_hold_seconds\n";
    
    
    
    $stmt="select count(*) from osdial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $closer_SQLand and status NOT IN('DROP','XDROP','HXFER','QVMAIL','HOLDTO','LIVE','QUEUE');";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $row=mysql_fetch_row($rslt);
    $TOTALanswers = ($row[0] + $ANSWERcalls);
    
    $stmt = "SELECT sum(wait_sec + talk_sec + dispo_sec) from osdial_agent_log where event_time >= '$query_date_BEGIN' and event_time <= '$query_date_END' $group_SQLand;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $row=mysql_fetch_row($rslt);
    $agent_non_pause_sec = $row[0];
    
    if ($agent_non_pause_sec > 0) {
        $AVG_ANSWERagent_non_pause_sec = (($TOTALanswers / $agent_non_pause_sec) * 60);
        $AVG_ANSWERagent_non_pause_sec = round($AVG_ANSWERagent_non_pause_sec, 2);
    } else {
        $AVG_ANSWERagent_non_pause_sec=0;
    }
    $AVG_ANSWERagent_non_pause_sec = sprintf("%10s", $AVG_ANSWERagent_non_pause_sec);
    
    $html .= "Productivity Rating:                          $AVG_ANSWERagent_non_pause_sec\n";
    $html .= "</pre></div>";
    
    
    
    
    $html .= "<div class=onlyprint><pre>\n\n";
    $html .= "---------- AUTO-DIAL NO ANSWERS\n";
    
    if ($use_closer_log) {
        $stmt="SELECT count(*),sum(length_in_sec) FROM ((select uniqueid,length_in_sec from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status IN('NA','B') and (length_in_sec <= 60 or length_in_sec is null)) UNION (select uniqueid,length_in_sec from osdial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $closer_SQLand and status IN('NA','B') and (length_in_sec <= 60 or length_in_sec is null))) AS t;";
    } else {
        $stmt="select count(*),sum(length_in_sec) from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and status IN('NA','B') and (length_in_sec <= 60 or length_in_sec is null);";
    }
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $row=mysql_fetch_row($rslt);

    $NAcalls =    sprintf("%10s", $row[0]);
    if ( ($NAcalls < 1) or ($TOTALcalls < 1) ) {
        $NApercent = '0';
    } else {
        $NApercent = (($NAcalls / $TOTALcalls) * 100);
        $NApercent = round($NApercent, 2);
    }
    
    if ( ($row[0] < 1) or ($row[1] < 1) ) {
        $average_na_seconds = '         0';
    } else {
        $average_na_seconds = ($row[1] / $row[0]);
        $average_na_seconds = round($average_na_seconds, 2);
        $average_na_seconds =    sprintf("%10s", $average_na_seconds);
    }
    
    $html .= "Total NA calls -Busy,Disconnect,RingNoAnswer: $NAcalls  $NApercent%\n";
    $html .= "Average Call Length for NA Calls in seconds:  $average_na_seconds\n";
    $html .= "</pre></div>\n";

    $table .= "<br><br>\n";
    $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
    $table .= "  <tr><td align=center><font color=$default_text size=3>CALL REPORT SUMMARY INFORMATION</font></td></tr>\n";
    $table .= "  <tr>\n";
    $table .= "    <td align=center>\n";
    $table .= "      <table width=400 align=center cellspacing=1 bgcolor=grey>\n";
    $table .= "        <tr class=tabheader>\n";
    $table .= "          <td align=center colspan=2>Report Details</td>\n";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
    $table .= "          <td>Date/Time Report Was Run</td><td>" . dateToLocal($link,'first',$NOW_TIME,$webClientAdjGMT,'',$webClientDST,1) . "</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
    $table .= "          <td>Date/Time Start</td><td>$query_date_BEGIN</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
    $table .= "          <td>Date/Time End</td><td>$query_date_END</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
    $table .= "          <td>Selected Campaigns</td><td>$group_list</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
    $table .= "          <td>Included InGroups</td><td>$ccprint2</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr class=tabheader>\n";
    $table .= "          <td align=center colspan=2>TOTALS</td>\n";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
    $table .= "          <td>Totals Calls for Selected Campaigns</td><td>$TOTALcalls</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
    $table .= "          <td>Average Call Legnth</td><td>$TOTAVGseconds seconds</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
    $table .= "          <td>Total Human Answered Calls</td><td>$ANSWERcalls</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
    $table .= "          <td>Productivity Rating</td><td>$AVG_ANSWERagent_non_pause_sec</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr class=tabheader>\n";
    $table .= "          <td align=center colspan=2>DROPS</td>\n";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
    $table .= "          <td>Total DROP Calls</td><td>$DROPcalls</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
    $table .= "          <td>Percentage of DROP to Total Calls</td><td>$DROPpercent%</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
    $table .= "          <td>Percentage of DROP to Human Answered Calls</td><td>$DROPANSWERpercent%</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
    $table .= "          <td>Average Call Length for DROP Calls</td><td>$average_hold_seconds seconds</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr class=tabheader>\n";
    $table .= "          <td align=center colspan=2>AUTO-DIAL NO-ANSWERS</td>\n";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
    $table .= "          <td>Total NA Calls</td><td>$NAcalls</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
    $table .= "          <td>Percentage of NA to Total Calls</td><td>$NApercent%</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
    $table .= "          <td>Average Call Length for NA Calls</td><td>$average_na_seconds seconds</td>";
    $table .= "        </tr>\n";
    $table .= "        <tr class=tabfooter>\n";
    $table .= "          <td colspan=2></td>\n";
    $table .= "        </tr>\n";
    $table .= "      </table>\n";
    $table .= "    </td>\n";
    $table .= "  </tr>\n";
    $table .= "</table>\n";

    $html .= "<div class=noprint>$table</div>\n";
    
    
    ##############################
    #########  CALL HANGUP REASON STATS
    
    $TOTALcalls = 0;
    $plain='';
    $table='';
    
    $plain .= "---------- CALL HANGUP REASON STATS\n";
    $plain .= "+----------------------+------------+\n";
    $plain .= "| HANGUP REASON        | CALLS      |\n";
    $plain .= "+----------------------+------------+\n";

    $table .= "<br><br>\n";
    $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
    $table .= "  <tr><td align=center><font color=$default_text size=3>CALL HANGUP REASON STATS</font></td></tr>\n";
    $table .= "  <tr>\n";
    $table .= "    <td align=center>\n";
    $table .= "      <table width=300 align=center cellspacing=1 bgcolor=grey>\n";
    $table .= "        <tr class=tabheader>\n";
    $table .= "          <td align=center>Hangup Reason</td>\n";
    $table .= "          <td align=center>Calls</td>\n";
    $table .= "        </tr>\n";
    
    if ($use_closer_log) {
        $stmt="SELECT count(*),term_reason FROM ((select uniqueid,term_reason from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand) UNION (select uniqueid,term_reason from osdial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $closer_SQLand)) AS t group by term_reason;";
    } else {
        $stmt="select count(*),term_reason from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand group by term_reason;";
    }
    if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $reasons_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $reasons_to_print) {
        $row=mysql_fetch_row($rslt);
        $TOTALcalls = ($TOTALcalls + $row[0]);
        $REASONcount = sprintf("%10s", $row[0]);
        while(strlen($REASONcount)>10) {
            $REASONcount = substr("$REASONcount", 0, -1);
        }
        $reason = sprintf("%-20s", $row[1]);
        while(strlen($reason)>20) {
            $reason = substr("$reason", 0, -1);
        }
        if (preg_match("/NONE/",$reason))    {$reason = 'NO CONTACT          ';}
        if (preg_match("/CALLER/",$reason)) {$reason = 'CUSTOMER            ';}
    
        $plain .= "| $reason | $REASONcount |\n";

        $table .= "        <tr " . bgcolor($i) . " class=\"row font1\">\n";
        $table .= "          <td align=left>$reason</td>\n";
        $table .= "          <td align=right>$REASONcount</td>\n";
        $table .= "        </tr>\n";
        $i++;
    }
    
    $TOTALcalls =        sprintf("%10s", $TOTALcalls);
    
    $plain .= "+----------------------+------------+\n";
    $plain .= "| TOTAL:               | $TOTALcalls |\n";
    $plain .= "+----------------------+------------+\n";

    $table .= "        <tr class=tabfooter>\n";
    $table .= "          <td align=left>TOTAL</td>\n";
    $table .= "          <td align=right>$TOTALcalls</td>\n";
    $table .= "        </tr>\n";
    $table .= "      </table>\n";
    $table .= "    </td>\n";
    $table .= "  </tr>\n";
    $table .= "</table>\n";

    $html .= "<div class=onlyprint><pre>\n\n$plain</pre></div>\n";
    $html .= "<div class=noprint>$table</div>";
    
    
    ##############################
    #########  CALL STATUS STATS
    $table='';
    $plain='';
    $export='';

    $CSVrows = 0;
    $export .= "<form target=\"_new\" action=\"/admin/tocsv.php\">";
    $export .= "<input type=hidden name=\"name\" value=\"css\">";
    
    $TOTALcalls = 0;
    
    $plain .= "\n\n";
    $plain .= "---------- CALL DISPOSITION STATS\n";
    $head = '';
    $table .= "<br><br>\n";
    $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
    $table .= "  <tr><td align=center><font color=$default_text size=3>CALL DISPOSITION STATS</font></td></tr>\n";
    $table .= "  <tr>\n";
    $table .= "    <td align=center>\n";
    $table .= "      <table width=700 align=center cellspacing=1 bgcolor=grey>\n";
    if ($comment_grouping) {
        $plain .= "+------------------------------------------+--------+----------------------+----------------------+------------+----------------------------------+----------+\n";
        $plain .= "|                                          |        |                      |                      |            |      CALL TIME                   |AGENT TIME|\n";
        $plain .= "| EXTENDED DATA                            | STATUS | DESCRIPTION          | CATEGORY             | CALLS      | TOTAL TIME | AVG TIME |CALLS/HOUR|CALLS/HOUR|\n";
        $plain .= "+------------------------------------------+--------+----------------------+----------------------+------------+------------+----------+----------+----------+\n";
        $table .= "        <tr class=tabheader>\n";
        $table .= "          <td colspan=5>&nbsp;</td>\n";
        $table .= "          <td colspan=3 align=center>Call Time</td>\n";
        $table .= "          <td align=center>Agent Time</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabheader>\n";
        $table .= "          <td align=center>Extended Data</td>\n";
        $table .= "          <td align=center>Status</td>\n";
        $table .= "          <td align=center>Description</td>\n";
        $table .= "          <td align=center>Category</td>\n";
        $table .= "          <td align=center>Calls</td>\n";
        $table .= "          <td align=center>Total Time</td>\n";
        $table .= "          <td align=center>Avg Time</td>\n";
        $table .= "          <td align=center>Calls/Hour</td>\n";
        $table .= "          <td align=center>Calls/Hour</td>\n";
        $table .= "        </tr>\n";
        $head = "Extended Data|Status|Description|Category|Calls|Call Total Time|Call Avg Time|Calls/Hour|Agent Calls/Hour";
    } else {
        $plain .= "+--------+----------------------+----------------------+------------+----------------------------------+----------+\n";
        $plain .= "|        |                      |                      |            |      CALL TIME                   |AGENT TIME|\n";
        $plain .= "| STATUS | DESCRIPTION          | CATEGORY             | CALLS      | TOTAL TIME | AVG TIME |CALLS/HOUR|CALLS/HOUR|\n";
        $plain .= "+--------+----------------------+----------------------+------------+------------+----------+----------+----------+\n";
        $table .= "        <tr class=tabheader>\n";
        $table .= "          <td colspan=4>&nbsp;</td>\n";
        $table .= "          <td colspan=3 align=center>Call Time</td>\n";
        $table .= "          <td align=center>Agent Time</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabheader>\n";
        $table .= "          <td align=center>Status</td>\n";
        $table .= "          <td align=center>Description</td>\n";
        $table .= "          <td align=center>Category</td>\n";
        $table .= "          <td align=center>Calls</td>\n";
        $table .= "          <td align=center>Total Time</td>\n";
        $table .= "          <td align=center>Avg Time</td>\n";
        $table .= "          <td align=center>Calls/Hour</td>\n";
        $table .= "          <td align=center>Calls/Hour</td>\n";
        $table .= "        </tr>\n";
        $head = "Status|Description|Category|Calls|Call Total Time|Call Avg Time|Calls/Hour|Agent Calls/Hour";
    }
    $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $head . "\">";
    $CSVrows++;
    
    
    ## Pull the count of agent seconds for the total tally
    $stmt="SELECT sum(pause_sec + wait_sec + talk_sec + dispo_sec) from osdial_agent_log where event_time >= '$query_date_BEGIN' and event_time <= '$query_date_END' $group_SQLand and pause_sec<36000 and wait_sec<36000 and talk_sec<36000 and dispo_sec<36000;";
    $rslt=mysql_query($stmt, $link);
    $Ctally_to_print = mysql_num_rows($rslt);
    if ($Ctally_to_print > 0) {
        $rowx=mysql_fetch_row($rslt);
        $AGENTsec = "$rowx[0]";
    }
    
    
    ## get counts and time totals for all statuses in this campaign
    if ($use_closer_log) {
        if ($comment_grouping) {
            $stmt="SELECT count(*),status,sum(length_in_sec),comments FROM ((select uniqueid,status,length_in_sec,comments from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END'  $group_SQLand) UNION (select uniqueid,status,length_in_sec,comments from osdial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END'  $closer_SQLand)) AS t group by status,comments;";
        } else {
            $stmt="SELECT count(*),status,sum(length_in_sec) FROM ((select uniqueid,status,length_in_sec from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END'  $group_SQLand) UNION (select uniqueid,status,length_in_sec from osdial_closer_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END'  $closer_SQLand)) AS t group by status;";
        }
    } else {
        if ($comment_grouping) {
            $stmt="select count(*),status,sum(length_in_sec),comments from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END'  $group_SQLand group by status,comments;";
        } else {
            $stmt="select count(*),status,sum(length_in_sec) from osdial_log where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END'  $group_SQLand group by status;";
        }
    }
    $cnvrate = Array();
    if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $statuses_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $statuses_to_print) {
        $row=mysql_fetch_row($rslt);
    
        if ($comment_grouping and $row[3] != '' and $row[3] != 'REMOTE' and $row[3] != 'AFTER HOURS DROP') {
            $cnvrate[$row[3]][$statcat_list[$row[1]]] += $row[0];
        }

        $STATUScount =    $row[0];
        $RAWstatus =    $row[1];
        $RAWcomment =    preg_replace("/\.wav$|\.mp3$|\.gsm$/","",$row[3]);
        $r=0;
        while ($r < $statcats_to_print) {
            if ($statcat_list[$RAWstatus] == "$vsc_id[$r]") {
                $vsc_count[$r] = ($vsc_count[$r] + $STATUScount);
            }
            $r++;
        }
        if ($AGENTsec < 1) {$AGENTsec=1;}
        $TOTALcalls = ($TOTALcalls + $row[0]);
        $STATUSrate = ($STATUScount / ($TOTALsec / 3600) );
        $AGENTrate = ($STATUScount / ($AGENTsec / 3600) );

        $STATUSrate = sprintf("%.2f", $STATUSrate);
        $AGENTrate = sprintf("%.2f", $AGENTrate);
    
        $STATUShours_H =    ($row[2] / 3600);
        $STATUShours_H_int = round($STATUShours_H, 2);
        $STATUShours_H_int = intval("$STATUShours_H_int");
        $STATUShours_M = ($STATUShours_H - $STATUShours_H_int);
        $STATUShours_M = ($STATUShours_M * 60);
        $STATUShours_M_int = round($STATUShours_M, 2);
        $STATUShours_M_int = intval("$STATUShours_M_int");
        $STATUShours_S = ($STATUShours_M - $STATUShours_M_int);
        $STATUShours_S = ($STATUShours_S * 60);
        $STATUShours_S = round($STATUShours_S, 0);
        if ($STATUShours_S < 10) {$STATUShours_S = "0$STATUShours_S";}
        if ($STATUShours_M_int < 10) {$STATUShours_M_int = "0$STATUShours_M_int";}
        $STATUShours = "$STATUShours_H_int:$STATUShours_M_int:$STATUShours_S";
    
        $STATUSavg_H =    (($row[2] / 3600) / $STATUScount);
        $STATUSavg_H_int = round($STATUSavg_H, 2);
        $STATUSavg_H_int = intval("$STATUSavg_H_int");
        $STATUSavg_M = ($STATUSavg_H - $STATUSavg_H_int);
        $STATUSavg_M = ($STATUSavg_M * 60);
        $STATUSavg_M_int = round($STATUSavg_M, 2);
        $STATUSavg_M_int = intval("$STATUSavg_M_int");
        $STATUSavg_S = ($STATUSavg_M - $STATUSavg_M_int);
        $STATUSavg_S = ($STATUSavg_S * 60);
        $STATUSavg_S = round($STATUSavg_S, 0);
        if ($STATUSavg_S < 10) {$STATUSavg_S = "0$STATUSavg_S";}
        if ($STATUSavg_M_int < 10) {$STATUSavg_M_int = "0$STATUSavg_M_int";}
        $STATUSavg = "$STATUSavg_H_int:$STATUSavg_M_int:$STATUSavg_S";
    
        # TODO:  What?  We can't trust sprintf?
        $STATUScount = sprintf("%10s", $row[0]);
        while(strlen($STATUScount)>10) {$STATUScount = substr("$STATUScount", 0, -1);}
        $status = sprintf("%-6s", $row[1]);
        while(strlen($status)>6) {$status = substr("$status", 0, -1);}
        $STATUShours = sprintf("%10s", $STATUShours);
        while(strlen($STATUShours)>10) {$STATUShours = substr("$STATUShours", 0, -1);}
        $STATUSavg = sprintf("%8s", $STATUSavg);
        while(strlen($STATUSavg)>8) {$STATUSavg = substr("$STATUSavg", 0, -1);}
        $STATUSrate = sprintf("%8s", $STATUSrate);
        while(strlen($STATUSrate)>8) {$STATUSrate = substr("$STATUSrate", 0, -1);}
        $AGENTrate = sprintf("%8s", $AGENTrate);
        while(strlen($AGENTrate)>8) {$AGENTrate = substr("$AGENTrate", 0, -1);}
    
        if ($non_latin < 1) {
            $status_name =    sprintf("%-20s", $statname_list[$RAWstatus]); 
            while(strlen($status_name)>20) {$status_name = substr("$status_name", 0, -1);}    
            $statcat =    sprintf("%-20s", $statcat_list[$RAWstatus]); 
            while(strlen($statcat)>20) {$statcat = substr("$statcat", 0, -1);}    
            $comment =    sprintf("%-40s", $RAWcomment); 
            while(strlen($comment)>40) {$comment = substr("$comment", 0, -1);}    
        } else {
            $status_name =    sprintf("%-60s", $statname_list[$RAWstatus]); 
            while(mb_strlen($status_name,'utf-8')>20) {$status_name = mb_substr("$status_name", 0, -1,'utf-8');}    
            $statcat =    sprintf("%-60s", $statcat_list[$RAWstatus]); 
            while(mb_strlen($statcat,'utf-8')>20) {$statcat = mb_substr("$statcat", 0, -1,'utf-8');}    
            $comment =    sprintf("%-120s", $$RAWcomment); 
            while(mb_strlen($comment,'utf-8')>40) {$comment = mb_substr("$comment", 0, -1,'utf-8');}    
        }
    
        $line = '';
        if ($comment_grouping) {
            $table .= "        <tr " . bgcolor($i) . " class=\"row font1\">\n";
            $table .= "          <td align=left>$comment</td>\n";
            $table .= "          <td align=left>$status</td>\n";
            $table .= "          <td align=left>$status_name</td>\n";
            $table .= "          <td align=left>$statcat</td>\n";
            $table .= "          <td align=right>$STATUScount</td>\n";
            $table .= "          <td align=right>$STATUShours</td>\n";
            $table .= "          <td align=right>$STATUSavg</td>\n";
            $table .= "          <td align=right>$STATUSrate</td>\n";
            $table .= "          <td align=right>$AGENTrate</td>\n";
            $table .= "        </tr>\n";
            $line = "$comment | $status | $status_name | $statcat | $STATUScount | $STATUShours | $STATUSavg | $STATUSrate | $AGENTrate";
        } else {
            $table .= "        <tr " . bgcolor($i) . " class=\"row font1\">\n";
            $table .= "          <td align=left>$status</td>\n";
            $table .= "          <td align=left>$status_name</td>\n";
            $table .= "          <td align=left>$statcat</td>\n";
            $table .= "          <td align=right>$STATUScount</td>\n";
            $table .= "          <td align=right>$STATUShours</td>\n";
            $table .= "          <td align=right>$STATUSavg</td>\n";
            $table .= "          <td align=right>$STATUSrate</td>\n";
            $table .= "          <td align=right>$AGENTrate</td>\n";
            $table .= "        </tr>\n";
            $line = "$status | $status_name | $statcat | $STATUScount | $STATUShours | $STATUSavg | $STATUSrate | $AGENTrate";
        }
        $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $line . "\">";
        $plain .= "| $line |\n";
        $CSVrows++;
    
        $i++;
    }
    
    if ($TOTALcalls < 1) {
        $TOTALhours = '0:00:00';
        $TOTALavg = '0:00:00';
        $TOTALrate = '0.00';
    } else {
        $TOTALrate = ($TOTALcalls / ($TOTALsec / 3600) );
        $TOTALrate = sprintf("%.2f", $TOTALrate);
        $aTOTALrate = ($TOTALcalls / ($AGENTsec / 3600) );
        $aTOTALrate = sprintf("%.2f", $aTOTALrate);
    
        $aTOTALhours_H = ($AGENTsec / 3600);
        $aTOTALhours_H_int = round($aTOTALhours_H, 2);
        $aTOTALhours_H_int = intval("$aTOTALhours_H_int");
        $aTOTALhours_M = ($aTOTALhours_H - $aTOTALhours_H_int);
        $aTOTALhours_M = ($aTOTALhours_M * 60);
        $aTOTALhours_M_int = round($aTOTALhours_M, 2);
        $aTOTALhours_M_int = intval("$aTOTALhours_M_int");
        $aTOTALhours_S = ($aTOTALhours_M - $aTOTALhours_M_int);
        $aTOTALhours_S = ($aTOTALhours_S * 60);
        $aTOTALhours_S = round($aTOTALhours_S, 0);
        if ($aTOTALhours_S < 10) {$aTOTALhours_S = "0$aTOTALhours_S";}
        if ($aTOTALhours_M_int < 10) {$aTOTALhours_M_int = "0$aTOTALhours_M_int";}
        $aTOTALhours = "$aTOTALhours_H_int:$aTOTALhours_M_int:$aTOTALhours_S";
    
        $TOTALhours_H =    ($TOTALsec / 3600);
        $TOTALhours_H_int = round($TOTALhours_H, 2);
        $TOTALhours_H_int = intval("$TOTALhours_H_int");
        $TOTALhours_M = ($TOTALhours_H - $TOTALhours_H_int);
        $TOTALhours_M = ($TOTALhours_M * 60);
        $TOTALhours_M_int = round($TOTALhours_M, 2);
        $TOTALhours_M_int = intval("$TOTALhours_M_int");
        $TOTALhours_S = ($TOTALhours_M - $TOTALhours_M_int);
        $TOTALhours_S = ($TOTALhours_S * 60);
        $TOTALhours_S = round($TOTALhours_S, 0);
        if ($TOTALhours_S < 10) {$TOTALhours_S = "0$TOTALhours_S";}
        if ($TOTALhours_M_int < 10) {$TOTALhours_M_int = "0$TOTALhours_M_int";}
        $TOTALhours = "$TOTALhours_H_int:$TOTALhours_M_int:$TOTALhours_S";
    
        $TOTALavg_H =    (($TOTALsec / 3600) / $TOTALcalls);
        $TOTALavg_H_int = round($TOTALavg_H, 2);
        $TOTALavg_H_int = intval("$TOTALavg_H_int");
        $TOTALavg_M = ($TOTALavg_H - $TOTALavg_H_int);
        $TOTALavg_M = ($TOTALavg_M * 60);
        $TOTALavg_M_int = round($TOTALavg_M, 2);
        $TOTALavg_M_int = intval("$TOTALavg_M_int");
        $TOTALavg_S = ($TOTALavg_M - $TOTALavg_M_int);
        $TOTALavg_S = ($TOTALavg_S * 60);
        $TOTALavg_S = round($TOTALavg_S, 0);
        if ($TOTALavg_S < 10) {$TOTALavg_S = "0$TOTALavg_S";}
        if ($TOTALavg_M_int < 10) {$TOTALavg_M_int = "0$TOTALavg_M_int";}
        $TOTALavg = "$TOTALavg_H_int:$TOTALavg_M_int:$TOTALavg_S";
    }
    $TOTALcalls = sprintf("%10s", $TOTALcalls);
    $TOTALhours = sprintf("%10s", $TOTALhours);
    while(strlen($TOTALhours)>10) {$TOTALhours = substr("$TOTALhours", 0, -1);}
    $aTOTALhours = sprintf("%10s", $aTOTALhours);
    while(strlen($aTOTALhours)>10) {$aTOTALhours = substr("$aTOTALhours", 0, -1);}
    $TOTALavg = sprintf("%8s", $TOTALavg);
    while(strlen($TOTALavg)>8) {$TOTALavg = substr("$TOTALavg", 0, -1);}
    $TOTALrate = sprintf("%8s", $TOTALrate);
    while(strlen($TOTALrate)>8) {$TOTALrate = substr("$TOTALrate", 0, -1);}
    $aTOTALrate = sprintf("%8s", $aTOTALrate);
    while(strlen($aTOTALrate)>8) {$aTOTALrate = substr("$aTOTALrate", 0, -1);}
    
    if ($comment_grouping) {
        $plain .= "+------------------------------------------+--------+----------------------+----------------------+------------+------------+----------+----------+----------+\n";
        $plain .= "| TOTAL:                                                                                          | $TOTALcalls | $TOTALhours | $TOTALavg | $TOTALrate |          |\n";
        $plain .= "|   AGENT TIME                                                                                    |            | $aTOTALhours |                     | $aTOTALrate |\n";
        $plain .= "+-------------------------------------------------------------------------------------------------+------------+------------+---------------------+----------+\n";
        $table .= "        <tr class=tabfooter>\n";
        $table .= "          <td colspan=4 align=left>Total</td>\n";
        $table .= "          <td align=right>$TOTALcalls</td>\n";
        $table .= "          <td align=right>$TOTALhours</td>\n";
        $table .= "          <td align=right>$TOTALavg</td>\n";
        $table .= "          <td align=right>$TOTALrate</td>\n";
        $table .= "          <td>&nbsp;</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabfooter>\n";
        $table .= "          <td colspan=4 align=left>Agent Time</td>\n";
        $table .= "          <td align=left>&nbsp;</td>\n";
        $table .= "          <td align=right>$aTOTALhours</td>\n";
        $table .= "          <td colspan=2>&nbsp;</td>\n";
        $table .= "          <td align=right>$aTOTALrate</td>\n";
        $table .= "        </tr>\n";
    } else {
        $plain .= "+--------+----------------------+----------------------+------------+------------+----------+----------+----------+\n";
        $plain .= "| TOTAL:                                               | $TOTALcalls | $TOTALhours | $TOTALavg | $TOTALrate |          |\n";
        $plain .= "|   AGENT TIME                                         |            | $aTOTALhours |                     | $aTOTALrate |\n";
        $plain .= "+------------------------------------------------------+------------+------------+---------------------+----------+\n";
        $table .= "        <tr class=tabfooter>\n";
        $table .= "          <td colspan=3 align=left>TOTALS</td>\n";
        $table .= "          <td align=right>$TOTALcalls</td>\n";
        $table .= "          <td align=right>$TOTALhours</td>\n";
        $table .= "          <td align=right>$TOTALavg</td>\n";
        $table .= "          <td align=right>$TOTALrate</td>\n";
        $table .= "          <td>&nbsp;</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabfooter>\n";
        $table .= "          <td colspan=3 align=left>AGENT TIME</td>\n";
        $table .= "          <td align=left>&nbsp;</td>\n";
        $table .= "          <td align=right>$aTOTALhours</td>\n";
        $table .= "          <td colspan=2>&nbsp;</td>\n";
        $table .= "          <td align=right>$aTOTALrate</td>\n";
        $table .= "        </tr>\n";
    }
    $export .= "<input type=hidden name=\"rows\" value=\"" . $CSVrows . "\">";
    if ($LOG['export_campaign_call_report']) $export .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
    $export .= "</form>";

    $table .= "      </table>\n";
    $table .= "    </td>\n";
    $table .= "  </tr>\n";
    $table .= "</table>\n";

    $html .= "<div class=onlyprint><pre>\n\n$plain</pre></div>\n";
    $html .= "<div class=noprint>$table<br><center>$export</center></div>";
    
    
    
    ##############################
    #########  STATUS CATEGORY STATS
    $table='';
    $plain='';
    $export='';

    $CSVrows = 0;
    $export .= "<form target=\"_new\" action=\"/admin/tocsv.php\">";
    $export .= "<input type=hidden name=\"name\" value=\"cscs\">";
    
    $table .= "<br><br>\n";
    $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
    $table .= "  <tr><td align=center><font color=$default_text size=3>CUSTOM STATUS CATEGORY STATS</font></td></tr>\n";
    $table .= "  <tr>\n";
    $table .= "    <td align=center>\n";
    $table .= "      <table width=500 align=center cellspacing=1 bgcolor=grey>\n";
    $table .= "        <tr class=tabheader>\n";
    $table .= "          <td align=center>Category</td>\n";
    $table .= "          <td align=center>Description</td>\n";
    $table .= "          <td align=center>Calls</td>\n";
    $table .= "        </tr>\n";

    $plain .= "---------- CUSTOM STATUS CATEGORY STATS\n";
    $plain .= "+----------------------+--------------------------------+------------+\n";
    $plain .= "| CATEGORY             | DESCRIPTION                    | CALLS      |\n";
    $plain .= "+----------------------+--------------------------------+------------+\n";
    $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"Category|Description|Calls\">";
    $CSVrows++;
    
    
    $csgs_table = '';
    $csgs_plain = '';
    $TOTCATcalls=0;
    $TOTCATcontact=0;
    $TOTCATsale=0;
    $found_undef=0;
    $r=0;
    $r2=0;
    while ($r < $statcats_to_print) {
        $TOTCATcalls = ($TOTCATcalls + $vsc_count[$r]);
        $category =    sprintf("%-20s", $vsc_id[$r]); while(strlen($category)>20) {$category = substr("$category", 0, -1);}
        $CATcount =    sprintf("%10s", $vsc_count[$r]); while(strlen($CATcount)>10) {$CATcount = substr("$CATcount", 0, -1);}
        $CATname =    sprintf("%-30s", $vsc_name[$r]); while(strlen($CATname)>30) {$CATname = substr("$CATname", 0, -1);}
    
        if ($vsc_id[$r] == 'CONTACT' or $vsc_id[$r] == 'DNC') {
            $TOTCATcontact += $CATcount;
        } elseif ($vsc_id[$r] == 'SALE' or $vsc_id[$r] == 'XFER') {
            $TOTCATcontact += $CATcount;
            $TOTCATsale += $CATcount;
        }
        if ($CATcount > 0) {
            # Put "Undefined" on bottom.
            if ($vsc_id[$r] != 'UNDEFINED') {
                $plain .= "| $category | $CATname | $CATcount |\n";
                $table .= "        <tr " . bgcolor($r2) . " class=\"row font1\">\n";
                $table .= "          <td>$category</td>\n";
                $table .= "          <td>$CATname</td>\n";
                $table .= "          <td align=right>$CATcount</td>\n";
                $table .= "        </tr>\n";
            } else {
                $csgs_plain = "| $category | $CATname | $CATcount |\n";
                $ccgs_table = "          <td>$category</td>\n";
                $ccgs_table .= "          <td>$CATname</td>\n";
                $ccgs_table .= "          <td align=right>$CATcount</td>\n";
                $ccgs_table .= "        </tr>\n";
                $found_undef++;
            }
            $r2++;
        }
        $export .= "<input type=hidden name=\"row$CSVrows\" value=\"$category|$CATname|$CATcount\">";
        $CSVrows++;
        $r++;
    }
    if ($found_undef > 0) {
        $plain .= $csgs_plain;
        $table .= "        <tr " . bgcolor($r2) . " class=\"row font1\">\n" . $ccgs_table;
    }
    
    if ($TOTCATcontact > 0) {
        $TOTCATconversion = ($TOTCATsale / $TOTCATcontact) * 100;
    }
    $TOTCATconversion =    sprintf("%3.2f%%   ", $TOTCATconversion);
    while(strlen($TOTCATconversion)>7) {$TOTCATconversion = substr("$TOTCATconversion", 0, -1);}
    $TOTCATcalls =    sprintf("%10s", $TOTCATcalls);
    while(strlen($TOTCATcalls)>10) {$TOTCATcalls = substr("$TOTCATcalls", 0, -1);}
    
    $plain .= "+-------------------------------------------------------+------------+\n";
    $plain .= "| TOTAL                                                 | $TOTCATcalls |\n";
    $plain .= "+-------------------------------------------------------+------------+\n";
    $plain .= "| CONVERSION RATE  (CONTACTS / SALE+XFER)               |    $TOTCATconversion |\n";
    $plain .= "+-------------------------------------------------------+------------+\n";
    $table .= "        <tr class=tabfooter>\n";
    $table .= "          <td colspan=2 align=left>TOTAL</td>\n";
    $table .= "          <td align=right>$TOTCATcalls</td>\n";
    $table .= "        </tr>\n";
    $table .= "        <tr class=tabfooter>\n";
    $table .= "          <td colspan=2 align=left>CONVERSION RATE <span style=\"font-size: 6pt;\">(CONTACTS / SALE+XFER)</span></td>\n";
    $table .= "          <td align=right>$TOTCATconversion</td>\n";
    $table .= "        </tr>\n";
    $table .= "      </table>\n";
    $table .= "    </td>\n";
    $table .= "  </tr>\n";
    $table .= "</table>\n";
    $export .= "<input type=hidden name=\"rows\" value=\"" . $CSVrows . "\">";
    if ($LOG['export_campaign_call_report']) $export .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
    $export .= "</form>";

    $html .= "<div class=onlyprint><pre>\n\n$plain</pre></div>\n";
    $html .= "<div class=noprint>$table<br><center>$export</center></div>";



    if ($comment_grouping) {
        $table='';
        $plain='';
        $export='';

        $plain .= "---------- CONVERSION RATE BASED ON EXTENDED DATA\n";
        $plain .= "+----------------------------------------------------+----------+----------+----------+----------+\n";
        $plain .= "| EXTENDED DATA                                      | CONTACTS |    DNC   |   SALES  |   RATE   |\n";
        $plain .= "+----------------------------------------------------+----------+----------+----------+----------+\n";
        $table .= "<br><br>\n";
        $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
        $table .= "  <tr><td align=center><font color=$default_text size=3>CONVERSION RATE BASED ON EXTENDED DATA</font></td></tr>\n";
        $table .= "  <tr>\n";
        $table .= "    <td align=center>\n";
        $table .= "      <table width=600 align=center cellspacing=1 bgcolor=grey>\n";
        $table .= "        <tr class=tabheader>\n";
        $table .= "          <td align=center>Extended Data</td>\n";
        $table .= "          <td align=center>Contacts</td>\n";
        $table .= "          <td align=center>DNC</td>\n";
        $table .= "          <td align=center>SALES</td>\n";
        $table .= "          <td align=center>Rate</td>\n";
        $table .= "        </tr>\n";

        $TOTCATcontact = 0;
        $TOTCATdnc = 0;
        $TOTCATsale = 0;
        $TOTCATconversion = 0;
        $o=0;
        foreach ($cnvrate as $edkey => $edval) {
            $CATcontact = 0;
            $CATdnc = 0;
            $CATsale = 0;
            $CATconversion = 0;
            foreach ($edval as $sgkey => $sgval) {
                if ($sgkey == 'CONTACT') {
                    $CATcontact += $sgval;
                    $TOTCATcontact += $sgval;
                } elseif ($sgkey == 'DNC') {
                    $CATdnc += $sgval;
                    $TOTCATdnc += $sgval;
                } elseif ($sgkey == 'SALE' or $sgkey == 'XFER') {
                    $CATsale += $sgval;
                    $TOTCATsale += $sgval;
                }
            }
            if ($CATcontact > 0) {
                $CATconversion = ($CATsale / ($CATcontact + $CATdnc + $CATsale)) * 100;
                $CATconversion =    sprintf("%3.2f%%   ", $CATconversion);
                while(strlen($CATconversion)>7) {$CATconversion = substr("$CATconversion", 0, -1);}
                $CATcontact =    sprintf("%5s", $CATcontact);
                while(strlen($CATcontact)>5) {$CATcontact = substr("$CATcontact", 0, -1);}
                $CATdnc =    sprintf("%5s", $CATdnc);
                while(strlen($CATdnc)>5) {$CATdnc = substr("$CATdnc", 0, -1);}
                $CATsale =    sprintf("%5s", $CATsale);
                while(strlen($CATsale)>5) {$CATsale = substr("$CATsale", 0, -1);}
                $ed =    sprintf("%-50s", $edkey); 
                while(strlen($ed)>50) {$ed = substr("$ed", 0, -1);}    
                $plain .= '| ' . $ed . ' |    ' . $CATcontact . ' |   ' . $CATdnc . '  |   ' . $CATsale . '  |   ' . $CATconversion . "|\n";
                $table .= "        <tr " . bgcolor($o) . " class=\"row font1\">\n";
                $table .= "          <td>$ed</td>\n";
                $table .= "          <td align=right>$CATcontact</td>\n";
                $table .= "          <td align=right>$CATdnc</td>\n";
                $table .= "          <td align=right>$CATsale</td>\n";
                $table .= "          <td align=right>$CATconversion</td>\n";
                $table .= "        </tr>\n";
                $o++;
            }
        }
        if ($TOTCATcontact > 0) {
            $TOTCATconversion = ($TOTCATsale / ($TOTCATcontact + $TOTCATdnc + $TOTCATsale)) * 100;
            $TOTCATconversion =    sprintf("%3.2f%%   ", $TOTCATconversion);
            while(strlen($TOTCATconversion)>7) {$TOTCATconversion = substr("$TOTCATconversion", 0, -1);}
            $TOTCATcontact =    sprintf("%5s", $TOTCATcontact);
            while(strlen($TOTCATcontact)>5) {$TOTCATcontact = substr("$TOTCATcontact", 0, -1);}
            $TOTCATdnc =    sprintf("%5s", $TOTCATdnc);
            while(strlen($TOTCATdnc)>5) {$TOTCATdnc = substr("$TOTCATdnc", 0, -1);}
            $TOTCATsale =    sprintf("%5s", $TOTCATsale);
            while(strlen($TOTCATsale)>5) {$TOTCATsale = substr("$TOTCATsale", 0, -1);}
            $plain .= "+----------------------------------------------------+----------+----------+----------+----------+\n";
            $plain .= "+ TOTALS                                             ";
            $plain .= '|    ' . $TOTCATcontact . ' |   ' . $TOTCATdnc . '  |   ' . $TOTCATsale . '  |   ' . $TOTCATconversion . "|\n";
            $table .= "        <tr class=tabfooter>\n";
            $table .= "          <td>TOTALS</td>\n";
            $table .= "          <td align=right>$TOTCATcontact</td>\n";
            $table .= "          <td align=right>$TOTCATdnc</td>\n";
            $table .= "          <td align=right>$TOTCATsale</td>\n";
            $table .= "          <td align=right>$TOTCATconversion</td>\n";
            $table .= "        </tr>\n";
        } else {
            $table .= "        <tr class=tabfooter>\n";
            $table .= "          <td colspan=5></td>\n";
            $table .= "        </tr>\n";
        }
        $plain .= "+----------------------------------------------------+----------+----------+----------+----------+\n\n";
        $table .= "      </table>\n";
        $table .= "    </td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        $html .= "<div class=onlyprint><pre>\n\n$plain</pre></div>\n";
        $html .= "<div class=noprint>$table</div>";
    }
    
    ##############################
    #########  USER STATS
    $table='';
    $plain='';
    $export='';

    $CSVrows = 0;
    $export .= "<form target=\"_new\" action=\"/admin/tocsv.php\">";
    $export .= "<input type=hidden name=\"name\" value=\"us\">";
    
    $TOTagents=0;
    $TOTcalls=0;
    $TOTtime=0;
    $TOTavg=0;
    
    $plain .= "---------- AGENT STATS\n";
    $plain .= "+---------------------------------------------+------------+----------+--------+\n";
    $plain .= "| AGENT                                       | CALLS      | TIME M   | AVRG M |\n";
    $plain .= "+---------------------------------------------+------------+----------+--------+\n";
    $table .= "<br><br>\n";
    $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
    $table .= "  <tr><td align=center><font color=$default_text size=3>AGENT STATS</font></td></tr>\n";
    $table .= "  <tr>\n";
    $table .= "    <td align=center>\n";
    $table .= "      <table width=600 align=center cellspacing=1 bgcolor=grey>\n";
    $table .= "        <tr class=tabheader>\n";
    $table .= "          <td align=center>Agent</td>\n";
    $table .= "          <td align=center>Calls</td>\n";
    $table .= "          <td align=center>Time M</td>\n";
    $table .= "          <td align=center>Avg M</td>\n";
    $table .= "        </tr>\n";
    $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"Agent|Calls|Time|Average\">";
    $CSVrows++;
    
    if ($use_closer_log) {
        $stmt="select Tuser,full_name,count(*),sum(Slength_in_sec),avg(Alength_in_sec) from ((select osdial_log.user AS Tuser,full_name,uniqueid,length_in_sec AS Slength_in_sec,length_in_sec AS Alength_in_sec from osdial_log,osdial_users where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and osdial_log.user is not null and length_in_sec is not null and length_in_sec > 0 and osdial_log.user=osdial_users.user) UNION (select osdial_closer_log.user AS Tuser,full_name,uniqueid,length_in_sec AS Slength_in_sec,length_in_sec AS Alength_in_sec from osdial_closer_log,osdial_users where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $closer_SQLand and osdial_closer_log.user is not null and length_in_sec is not null and length_in_sec > 0 and osdial_closer_log.user=osdial_users.user)) AS t group by Tuser;";
    } else if ($use_agent_log) {
        $stmt="select osdial_agent_log.user,full_name,count(*),sum(talk_sec),avg(talk_sec) from osdial_agent_log left join osdial_users ON (osdial_agent_log.user=osdial_users.user) where event_time >= '$query_date_BEGIN' and event_time <= '$query_date_END' $group_SQLand and osdial_agent_log.user is not null and talk_sec is not null and talk_sec > 0 group by osdial_agent_log.user;";
    } else {
        $stmt="select osdial_log.user,full_name,count(*),sum(length_in_sec),avg(length_in_sec) from osdial_log,osdial_users where call_date >= '$query_date_BEGIN' and call_date <= '$query_date_END' $group_SQLand and osdial_log.user is not null and length_in_sec is not null and length_in_sec > 0 and osdial_log.user=osdial_users.user group by osdial_log.user;";
    }
    if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $users_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $users_to_print) {
        $row=mysql_fetch_row($rslt);
    
        $TOTcalls = ($TOTcalls + $row[2]);
        $TOTtime = ($TOTtime + $row[3]);
    
        $user = sprintf("%-20s", $row[0]);while(strlen($user)>20) {$user = substr("$user", 0, -1);}
        if ($non_latin < 1) {
          $full_name = sprintf("%-20s", $row[1]); while(strlen($full_name)>20) {$full_name = substr("$full_name", 0, -1);}    
        } else {
         $full_name = sprintf("%-45s", $row[1]); while(mb_strlen($full_name,'utf-8')>20) {$full_name = mb_substr("$full_name", 0, -1,'utf-8');}    
        }
        $USERcalls = sprintf("%10s", $row[2]);
        $USERtotTALK = $row[3];
        $USERavgTALK = $row[4];
    
        $USERtotTALK_M = ($USERtotTALK / 60);
        $USERtotTALK_M_int = round($USERtotTALK_M, 2);
        $USERtotTALK_M_int = intval("$USERtotTALK_M_int");
        $USERtotTALK_S = ($USERtotTALK_M - $USERtotTALK_M_int);
        $USERtotTALK_S = ($USERtotTALK_S * 60);
        $USERtotTALK_S = round($USERtotTALK_S, 0);
        if ($USERtotTALK_S < 10) {$USERtotTALK_S = "0$USERtotTALK_S";}
        $USERtotTALK_MS = "$USERtotTALK_M_int:$USERtotTALK_S";
        $USERtotTALK_MS = sprintf("%6s", $USERtotTALK_MS);
    
        $USERavgTALK_M = ($USERavgTALK / 60);
        $USERavgTALK_M_int = round($USERavgTALK_M, 2);
        $USERavgTALK_M_int = intval("$USERavgTALK_M_int");
        $USERavgTALK_S = ($USERavgTALK_M - $USERavgTALK_M_int);
        $USERavgTALK_S = ($USERavgTALK_S * 60);
        $USERavgTALK_S = round($USERavgTALK_S, 0);
        if ($USERavgTALK_S < 10) {$USERavgTALK_S = "0$USERavgTALK_S";}
        $USERavgTALK_MS = "$USERavgTALK_M_int:$USERavgTALK_S";
        $USERavgTALK_MS = sprintf("%6s", $USERavgTALK_MS);
    
        $line = "$user - $full_name | $USERcalls |   $USERtotTALK_MS | $USERavgTALK_MS";
        $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"$line\">";
        $plain .= "| $line |\n";
        $table .= "        <tr " . bgcolor($i) . " class=\"row font1\">\n";
        $table .= "          <td>$user - $full_name</td>\n";
        $table .= "          <td align=right>$USERcalls</td>\n";
        $table .= "          <td align=right>$USERtotTALK_MS</td>\n";
        $table .= "          <td align=right>$USERavgTALK_MS</td>\n";
        $table .= "        </tr>\n";
        $CSVrows++;
    
        $i++;
    }
    
    if (!$TOTcalls) {$TOTcalls = 1;}
    $TOTavg = ($TOTtime / $TOTcalls);
    $TOTavg = round($TOTavg, 0);
    $TOTavg_M = ($TOTavg / 60);
    $TOTavg_M_int = round($TOTavg_M, 2);
    $TOTavg_M_int = intval("$TOTavg_M_int");
    $TOTavg_S = ($TOTavg_M - $TOTavg_M_int);
    $TOTavg_S = ($TOTavg_S * 60);
    $TOTavg_S = round($TOTavg_S, 0);
    if ($TOTavg_S < 10) {$TOTavg_S = "0$TOTavg_S";}
    $TOTavg_MS = "$TOTavg_M_int:$TOTavg_S";
    $TOTavg = sprintf("%6s", $TOTavg_MS);
    
    $TOTtime_M = ($TOTtime / 60);
    $TOTtime_M_int = round($TOTtime_M, 2);
    $TOTtime_M_int = intval("$TOTtime_M_int");
    $TOTtime_S = ($TOTtime_M - $TOTtime_M_int);
    $TOTtime_S = ($TOTtime_S * 60);
    $TOTtime_S = round($TOTtime_S, 0);
    if ($TOTtime_S < 10) {$TOTtime_S = "0$TOTtime_S";}
    $TOTtime_MS = "$TOTtime_M_int:$TOTtime_S";
    $TOTtime = sprintf("%6s", $TOTtime_MS);
    
    $TOTagents = sprintf("%10s", $i);
    $TOTcalls = sprintf("%10s", $TOTcalls);
    $TOTtime = sprintf("%8s", $TOTtime);
    $TOTavg = sprintf("%6s", $TOTavg);
    
    $stmt="select avg(wait_sec) from osdial_agent_log where event_time >= '$query_date_BEGIN' and event_time <= '$query_date_END' $group_SQLand;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $row=mysql_fetch_row($rslt);
    
    $AVGwait = $row[0];
    $AVGwait_M = ($AVGwait / 60);
    $AVGwait_M_int = round($AVGwait_M, 2);
    $AVGwait_M_int = intval("$AVGwait_M_int");
    $AVGwait_S = ($AVGwait_M - $AVGwait_M_int);
    $AVGwait_S = ($AVGwait_S * 60);
    $AVGwait_S = round($AVGwait_S, 0);
    if ($AVGwait_S < 10) {$AVGwait_S = "0$AVGwait_S";}
    $AVGwait_MS = "$AVGwait_M_int:$AVGwait_S";
    $AVGwait = sprintf("%6s", $AVGwait_MS);
    
    $plain .= "+---------------------------------------------+------------+----------+--------+\n";
    $plain .= "| TOTAL Agents:                   $TOTagents  | $TOTcalls | $TOTtime | $TOTavg |\n";
    $plain .= "+---------------------------------------------+------------+----------+--------+\n";
    $plain .= "| Average Wait time between calls                                       $AVGwait |\n";
    $plain .= "+------------------------------------------------------------------------------+\n";
    $table .= "        <tr class=tabfooter>\n";
    $table .= "          <td>TOTAL Agents: $TOTagents</td>\n";
    $table .= "          <td align=right>$TOTcalls</td>\n";
    $table .= "          <td align=right>$TOTtime</td>\n";
    $table .= "          <td align=right>$TOTavg</td>\n";
    $table .= "        </tr>\n";
    $table .= "        <tr class=tabfooter>\n";
    $table .= "          <td colspan=3 align=left>Average Wait Time Between Calls</td>\n";
    $table .= "          <td align=right>$AVGwait</td>\n";
    $table .= "        </tr>\n";
    $table .= "      </table>\n";
    $table .= "    </td>\n";
    $table .= "  </tr>\n";
    $table .= "</table>\n";

    $export .= "<input type=hidden name=\"rows\" value=\"" . $CSVrows . "\">";
    if ($LOG['export_campaign_call_report']) $export .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
    $export .= "</form>";

    $html .= "<div class=onlyprint><pre>\n\n$plain</pre></div>\n";
    $html .= "<div class=noprint>$table<br><center>$export</center></div>";
    
    ##############################
    #########  TIME STATS
    $plain='';
    $table='';
    
    $hi_hour_count=0;
    $last_full_record=0;
    $i=0;
    $h=0;
    while ($i < 96) {
        if ($use_closer_log) {
            $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date $h:00:00' and call_date <= '$query_date $h:14:59' $group_SQLand) UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date $h:00:00' and call_date <= '$query_date $h:14:59' $closer_SQLand)) AS t;";
        } else {
            $stmt="select count(*) from osdial_log where call_date >= '$query_date $h:00:00' and call_date <= '$query_date $h:14:59' $group_SQLand;";
        }
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $hour_count[$i] = $row[0];
        if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
        if ($hour_count[$i] > 0) {$last_full_record = $i;}
        if ($use_closer_log) {
            $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date $h:00:00' and call_date <= '$query_date $h:14:59' $group_SQLand and status LIKE '%DROP') UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date $h:00:00' and call_date <= '$query_date $h:14:59' $closer_SQLand and status LIKE '%DROP')) AS t;";
        } else {
            $stmt="select count(*) from osdial_log where call_date >= '$query_date $h:00:00' and call_date <= '$query_date $h:14:59' $group_SQLand and status='DROP';";
        }
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $drop_count[$i] = $row[0];
        $i++;
    
    
        if ($use_closer_log) {
            $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date $h:15:00' and call_date <= '$query_date $h:29:59' $group_SQLand) UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date $h:15:00' and call_date <= '$query_date $h:29:59' $closer_SQLand)) AS t;";
        } else {
            $stmt="select count(*) from osdial_log where call_date >= '$query_date $h:15:00' and call_date <= '$query_date $h:29:59' $group_SQLand;";
        }
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $hour_count[$i] = $row[0];
        if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
        if ($hour_count[$i] > 0) {$last_full_record = $i;}
        if ($use_closer_log) {
            $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date $h:15:00' and call_date <= '$query_date $h:29:59' $group_SQLand and status LIKE '%DROP') UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date $h:15:00' and call_date <= '$query_date $h:29:59' $closer_SQLand and status LIKE '%DROP')) AS t;";
        } else {
            $stmt="select count(*) from osdial_log where call_date >= '$query_date $h:15:00' and call_date <= '$query_date $h:29:59' $group_SQLand and status='DROP';";
        }
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $drop_count[$i] = $row[0];
        $i++;
    
        if ($use_closer_log) {
            $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date $h:30:00' and call_date <= '$query_date $h:44:59' $group_SQLand) UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date $h:30:00' and call_date <= '$query_date $h:44:59' $closer_SQLand)) AS t;";
        } else {
            $stmt="select count(*) from osdial_log where call_date >= '$query_date $h:30:00' and call_date <= '$query_date $h:44:59' $group_SQLand;";
        }
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $hour_count[$i] = $row[0];
        if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
        if ($hour_count[$i] > 0) {$last_full_record = $i;}
        if ($use_closer_log) {
            $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date $h:30:00' and call_date <= '$query_date $h:44:59' $group_SQLand and status LIKE '%DROP') UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date $h:30:00' and call_date <= '$query_date $h:44:59' $closer_SQLand and status LIKE '%DROP')) AS t;";
        } else {
            $stmt="select count(*) from osdial_log where call_date >= '$query_date $h:30:00' and call_date <= '$query_date $h:44:59' $group_SQLand and status='DROP';";
        }
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $drop_count[$i] = $row[0];
        $i++;
    
        if ($use_closer_log) {
            $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date $h:45:00' and call_date <= '$query_date $h:59:59' $group_SQLand) UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date $h:45:00' and call_date <= '$query_date $h:59:59' $closer_SQLand)) AS t;";
        } else {
            $stmt="select count(*) from osdial_log where call_date >= '$query_date $h:45:00' and call_date <= '$query_date $h:59:59' $group_SQLand;";
        }
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $hour_count[$i] = $row[0];
        if ($hour_count[$i] > $hi_hour_count) {$hi_hour_count = $hour_count[$i];}
        if ($hour_count[$i] > 0) {$last_full_record = $i;}
        if ($use_closer_log) {
            $stmt="SELECT count(*) FROM ((select uniqueid from osdial_log where call_date >= '$query_date $h:45:00' and call_date <= '$query_date $h:59:59' $group_SQLand and status LIKE '%DROP') UNION (select uniqueid from osdial_closer_log where call_date >= '$query_date $h:45:00' and call_date <= '$query_date $h:59:59' $closer_SQLand and status LIKE '%DROP')) AS t;";
        } else {
            $stmt="select count(*) from osdial_log where call_date >= '$query_date $h:45:00' and call_date <= '$query_date $h:59:59' $group_SQLand and status='DROP';";
        }
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $drop_count[$i] = $row[0];
        $i++;
        $h++;
    }
    
    if ($hi_hour_count < 1) {
        $hour_multiplier = 0;
    } else {
        $hour_multiplier = (100 / $hi_hour_count);
    }
    
    $plain .= "<!-- HICOUNT: $hi_hour_count|$hour_multiplier -->\n";
    $plain .= "---------- TIME STATS\n";
    $plain .= "GRAPH IN 15 MINUTE INCREMENTS OF TOTAL CALLS PLACED FROM SELECTED CAMPAIGNS\n";

    $table .= "<br><br>\n";
    $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
    $table .= "  <tr>\n";
    $table .= "    <td align=center><font color=$default_text size=3>TIME STATS</font></td>\n";
    $table .= "  <tr>\n";
    $table .= "  </tr>\n";
    $table .= "    <td align=center><font color=$default_text size=1>GRAPH IN 15 MINUTE INCREMENTS OF TOTAL CALLS PLACED FROM SELECTED CAMPAIGNS</font></td>\n";
    $table .= "  </tr>\n";
    $table .= "  <tr>\n";
    $table .= "    <td align=center>\n";
    $table .= "      <table width=600 align=center cellspacing=0 cellpadding=0 style=\"border-spacing:0px 2px;\" bgcolor=grey>\n";
    $table .= "        <tr class=tabheader style=\"font-size: 6pt; font-family: monospace;\">\n";
    $table .= "          <td>&nbsp;</td>\n";
    $table .= "          <td align=center>|</td>\n";
    $table .= "          <td colspan=103 align=center style=\"font-size:8pt;\">Call Scale</td>\n";
    $table .= "          <td align=center>|</td>\n";
    $table .= "          <td colspan=2>&nbsp;</td>\n";
    $table .= "        </tr>\n";
    $table .= "        <tr class=tabheader style=\"font-size: 6pt; font-family: monospace;\">\n";
    $table .= "          <td align=center>&nbsp;&nbsp;TIME&nbsp;&nbsp;</td>\n";
    $table .= "          <td align=center>|</td>\n";
    
    $k=1;
    $Mk=0;
    $call_scale = '0';
    $table_scale = Array();
    $table_scale[] = "          <td align=center>0</td>\n";
    while ($k <= 102) {
        if ($Mk >= 5) {
            $Mk=0;
            if ( ($k < 1) or ($hour_multiplier <= 0) ) {
                $scale_num = 100;
            } else {
                $scale_num=($k / $hour_multiplier);
                $scale_num = round($scale_num, 0);
            }
            $LENscale_num = (strlen($scale_num));
            $k = ($k + $LENscale_num);
            if ($k > 103) {
                $call_scale = substr($call_scale,0,(103-$LENscale_num));
                foreach (range(1,$k-103) as $ele) {
                    $junk = array_pop($table_scale);
                }
            }

            $call_scale .= "$scale_num";
            foreach (range(0,$LENscale_num-1) as $ele) {
                $table_scale[] = "          <td align=center>" . substr($scale_num,$ele,1) . "</td>\n";
            }
        } else {
            $call_scale .= " ";
            $table_scale[] = "          <td align=center>&nbsp;</td>\n";
            $k++;   $Mk++;
        }
    }

    foreach ($table_scale as $ele) {
        $table .= $ele;
    }

    $table .= "          <td align=center>|</td>\n";
    $table .= "          <td align=center>&nbsp;&nbsp;DROPS&nbsp;</td>\n";
    $table .= "          <td align=center>&nbsp;TOTAL&nbsp;</td>\n";
    $table .= "        </tr>\n";
    
    $plain .= "+------+-------------------------------------------------------------------------------------------------------+-------+-------+\n";
    $plain .= "| TIME |$call_scale| DROPS | TOTAL |\n";
    $plain .= "+------+-------------------------------------------------------------------------------------------------------+-------+-------+\n";
    
    $i=0;
    $h=4;
    $hour= -1;
    $no_lines_yet=1;
    
    while ($i <= 96) {
        $char_counter=0;
        $time = '      ';
        if ($h >= 4) {
            $hour++;
            $h=0;
            if ($hour < 10) {$hour = "0$hour";}
            $time = "$hour:00";
            $etime = "$hour:14";
        }
        if ($h == 1) {$time = "$hour:15"; $etime = "$hour:29";}
        if ($h == 2) {$time = "$hour:30"; $etime = "$hour:44";}
        if ($h == 3) {$time = "$hour:45"; $etime = "$hour:59";}
        $Ghour_count = $hour_count[$i];
        if ($Ghour_count < 1) {
            if ( ($no_lines_yet) or ($i > $last_full_record) ) {
                $do_nothing=1;
            } else {
                $hour_count[$i] =    sprintf("%-5s", $hour_count[$i]);
                $plain .= "| $time|";
                $table .= "        <tr " . bgcolor($i) . " class=\"row\" title=\"Time Period: $time to $etime\" style=\"font-weight: bold; font-family: monospace; font-size: 7pt;\">\n";
                $table .= "          <td align=center>$time</td>\n";
                $table .= "          <td align=center style=\"font-size: 6pt;\">|</td>\n";
                $k=0;   while ($k <= 102) {$plain .= " ";  $table .= "          <td align=center>&nbsp;</td>\n"; $k++;}
                $plain .= "| 0     | $hour_count[$i] |\n";
                $table .= "          <td align=center style=\"font-size: 6pt;\">|</td>\n";
                $table .= "          <td align=right>0&nbsp;</td>\n";
                $table .= "          <td align=right>$Ghour_count&nbsp;</td>\n";
                $table .= "        </tr>\n";
            }
        } else {
            $no_lines_yet=0;
            $Xhour_count = ($Ghour_count * $hour_multiplier);
            $Yhour_count = (99 - $Xhour_count);
    
            $Gdrop_count = $drop_count[$i];
            if ($Gdrop_count < 1) {
                $hour_count[$i] =    sprintf("%-5s", $hour_count[$i]);
    
                $plain .= "| $time|<SPAN class=\"green\">";
                $table .= "        <tr " . bgcolor($i) . " class=\"row\" title=\"Time Period: $time to $etime\" style=\"font-weight: bold; font-family: monospace; font-size: 7pt;\">\n";
                $table .= "          <td align=center>$time</td>\n";
                $table .= "          <td align=center style=\"font-size: 6pt;\">|</td>\n";
                $table .= "          <td align=center>&nbsp;</td>\n";

                $k=0;   while ($k <= $Xhour_count) {$plain .= "*";   $table .= "          <td align=center style=\"padding-top: 1px; padding-bottom: 1px;\"><span style=\"background-color: green;\">&nbsp;</span></td>\n"; $k++;   $char_counter++;}
                $plain .= "*X</SPAN>";   $char_counter++;

                $k=0;   while ($k <= $Yhour_count) {$plain .= " ";   $table .= "          <td align=center>&nbsp;</td>\n"; $k++;   $char_counter++;}
                    while ($char_counter <= 101) {$plain .= " ";   $table .= "          <td align=center>&nbsp;</td>\n"; $char_counter++;}

                $plain .= "| 0     | $hour_count[$i] |\n";
                $table .= "          <td align=center>&nbsp;</td>\n";
                $table .= "          <td align=center style=\"font-size: 6pt;\">|</td>\n";
                $table .= "          <td align=right>0&nbsp;</td>\n";
                $table .= "          <td align=right>$Ghour_count&nbsp;</td>\n";
                $table .= "        </tr>\n";
    
            } else {
                $Xdrop_count = ($Gdrop_count * $hour_multiplier);
    
                $XXhour_count = ( ($Xhour_count - $Xdrop_count) - 1 );
    
                $hour_count[$i] =    sprintf("%-5s", $hour_count[$i]);
                $drop_count[$i] =    sprintf("%-5s", $drop_count[$i]);
    
                $plain .= "| $time|<SPAN class=\"red\">";
                $table .= "        <tr " . bgcolor($i) . " class=\"row\" title=\"Time Period: $time to $etime\" style=\"font-weight: bold; font-family: monospace; font-size: 7pt;\">\n";
                $table .= "          <td align=center>$time</td>\n";
                $table .= "          <td align=center style=\"font-size: 6pt;\">|</td>\n";
                $table .= "          <td align=center>&nbsp;</td>\n";

                $k=0;   while ($k <= $Xdrop_count) {$plain .= ">";   $table .= "          <td align=center style=\"padding-top: 1px; padding-bottom: 1px;\"><span style=\"background-color: red;\">&nbsp;</span></td>\n"; $k++;   $char_counter++;}
                $plain .= "D</SPAN><SPAN class=\"green\">";   $char_counter++;

                $k=0;   while ($k <= $XXhour_count) {$plain .= "*";   $table .= "          <td align=center style=\"padding-top: 1px; padding-bottom: 1px;\"><span style=\"background-color: green;\">&nbsp;</span></td>\n"; $k++;   $char_counter++;}
                $plain .= "X</SPAN>";   $char_counter++;

                $k=0;   while ($k <= $Yhour_count) {$plain .= " ";   $table .= "          <td align=center>&nbsp;</td>\n"; $k++;   $char_counter++;}
                while ($char_counter <= 102) {$plain .= " ";   $table .= "          <td align=center>&nbsp;</td>\n"; $char_counter++;}

                $plain .= "| $drop_count[$i] | $hour_count[$i] |\n";
                $table .= "          <td align=center>&nbsp;</td>\n";
                $table .= "          <td align=center style=\"font-size: 6pt;\">|</td>\n";
                $table .= "          <td align=right>$Gdrop_count&nbsp;</td>\n";
                $table .= "          <td align=right>$Ghour_count&nbsp;</td>\n";
                $table .= "        </tr>\n";
            }
        }
        $i++;
        $h++;
    }
    
    $plain .= "+------+-------------------------------------------------------------------------------------------------------+-------+-------+\n";
    $table .= "        <tr class=tabfooter style=\"font-size: 6pt; font-family: monospace;\">\n";
    $table .= "          <td>&nbsp;</td>\n";
    $table .= "          <td align=center>|</td>\n";
    $table .= "          <td colspan=103 align=center>&nbsp;</td>\n";
    $table .= "          <td align=center>|</td>\n";
    $table .= "          <td colspan=2>&nbsp;</td>\n";
    $table .= "        </tr>\n";
    $table .= "      </table>\n";
    $table .= "    </td>\n";
    $table .= "  </tr>\n";
    $table .= "</table>\n";

    $html .= "<div class=onlyprint><font size=0><pre>\n\n$plain</pre></font></div>\n";
    $html .= "<div class=noprint>$table</div>\n";

    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $STARTtime);
    $html .= "<div class=onlyprint><pre>\n\n\nRun Time: $RUNtime seconds</pre></div>\n";
    $html .= "<div class=noprint><br><br><br><center><font size=2 color=$default_text>Run Time: $RUNtime seconds</font></center></div>\n";
    $html .= "    </td>\n";
    $html .= "  </tr>\n";
    $html .= "</table>\n";
    
    ##$html .= "</BODY></HTML>\n";
	#$html .= "</td>";
	#$html .= "<TABLE WIDTH='$page_width' BGCOLOR=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>";

    return $html;
}

?>
