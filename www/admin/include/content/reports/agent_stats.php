<?
### report_agent_stats.php
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
# 60619-1743 - Added variable filtering to eliminate SQL injection attack threat
# 61201-1136 - Added recordings display and changed calls to time range with 10000 limit
# 70118-1605 - Added user group column to login/out and calls lists
# 70702-1231 - Added recording location link and truncation
#

function report_agent_stats() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $agent = get_variable('agent');
    $begin_date = get_variable('begin_date');
    $end_date = get_variable('end_date');
    $submit = get_variable('submit');
    $SUBMIT = get_variable('SUBMIT');
    $DB = get_variable('DB');
    
    $STARTtime = date("U");
    $TODAY = date("Y-m-d");
    
    $html='';
    $head='';
    $table='';

    $company_prefix = "";
    if ($LOG['multicomp_user'] > 0) {
        $company_prefix = $LOG['company_prefix'];
        if (substr($agent,0,3) == $LOG['company_prefix']) {
            $agent = substr($agent,3);
        }
    }
    
    if ($begin_date == "") {$begin_date = $TODAY;}
    if ($end_date == "") {$end_date = $TODAY;}
    
    $head .= "<br>\n";
    $head .= "<center><font color=$default_text size=4>AGENT STATS</font></center><br>\n";
    if ($agent) {
        $stmt=sprintf("SELECT full_name,user_group FROM osdial_users WHERE user_group IN %s AND user='%s';",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $full_name = $row[0];
        $agent_user_group = $row[1];

        $head .= "<center><font color=$default_text size=3><b>$agent - $full_name</b></font></center>\n";
        $head .= "<center>\n";
        $head .= "<span class=font2>\n";
        if ($LOG['view_agent_timesheet']) $head .= "  <a href=\"./admin.php?ADD=999999&SUB=20&agent=$agent&query_date=$begin_date\">Agent Timesheet</a>\n";
        if ($LOG['view_agent_status']) $head .= "  - <a href=\"./admin.php?ADD=999999&SUB=22&agent=$agent\">Agent Status</a>\n";
        $head .= "  - <a href=\"./admin.php?ADD=3&user=$agent\">Modify Agent</a>\n";
        $head .= "</span>\n";
        $head .= "</center><br>\n";
    }

    $head .= "<form name=range action=$PHP_SELF method=POST>\n";
    $head .= "<input type=hidden name=ADD value=\"$ADD\">\n";
    $head .= "<input type=hidden name=SUB value=\"$SUB\">\n";
    $head .= "<input type=hidden name=DB value=\"$DB\">\n";
    #$head .= "<input type=hidden name=agent value=\"$agent\">\n";
    $head .= "<table align=center cellspacing=1 width=350 bgcolor=grey>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td>Date Range</td>\n";
    $head .= "    <td>Agent ID</td>\n";
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td nowrap>\n";
    $head .= "      <script>\nvar cal1 = new CalendarPopup('caldiv1');\ncal1.showNavigationDropdowns();\n</script>\n";
    $head .= "      <input type=text name=begin_date value=\"$begin_date\" size=10 maxsize=10>\n";
    $head .= "      <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(formatDate(parseDate(document.forms[0].end_date.value).addDays(1),'yyyy-MM-dd'),null);cal1.select(document.forms[0].begin_date,'acal1','yyyy-MM-dd'); return false;\" name=acal1 id=acal1>\n";
    $head .= "      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $head .= "      to <input type=text name=end_date value=\"$end_date\" size=10 maxsize=10>\n";
    $head .= "      <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(null,formatDate(parseDate(document.forms[0].begin_date.value).addDays(-1),'yyyy-MM-dd'));cal1.select(document.forms[0].end_date,'acal1','yyyy-MM-dd'); return false;\" name=acal1 id=acal1>\n";
    $head .= "      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $head .= "    </td>\n";
    $head .= "    <td><input type=text name=agent value=\"$agent\" size=10 maxsize=10></td>\n";
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
    $head .= "  </tr>\n";
    $head .= "</table>\n";
    $head .= "</form>\n";
    $head .= "<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\"></div>\n";
    
    if (!$LOGview_reports) {
        $table .= "<center><font color=red>You do not have permission to view this page</font></center>\n";
    } elseif($agent) {
        #$stmt=sprintf("SELECT count(*),status,sum(talk_sec) FROM osdial_agent_log WHERE user_group IN %s AND user='%s' and event_time >= '%s 0:00:01'  and event_time <= '%s 23:59:59' AND status!='' GROUP BY status ORDER BY status;",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        $stmt=sprintf("SELECT count(*),status,sum(length_in_sec) FROM ((SELECT status,length_in_sec FROM osdial_log WHERE user='%s' AND call_date BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND status!='') UNION (SELECT status,length_in_sec FROM osdial_closer_log WHERE user='%s' AND call_date BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND status!='')) AS lr GROUP BY status;",$company_prefix . mres($agent),mres($begin_date),mres($end_date),$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);
        
        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=3><b>CALL DISPOSITION SUMMARY</b></font></center>\n";
        $table .= "  <table align=center width=300 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>STATUS</td>\n";
        $table .= "      <td>COUNT</td>\n";
        $table .= "      <td>TIME</td>\n";
        $table .= "    </tr>\n";
        
        $total_calls=0;
        $total_seconds=0;
        $o=0;
        while ($statuses_to_print > $o) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $o)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }
        
            $total_calls += $row[0];
            $total_seconds += $row[2];
            $call_seconds = $row[2];

            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td>$row[1]</td>\n";
            $table .= "    <td align=right>$row[0]</td>\n";
            $table .= "    <td align=right>" . fmt_hms($call_seconds) . "</td>\n";
            $table .= "  </tr>\n";
        
            $o++;
        }
        
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL CALLS</td>\n";
        $table .= "    <td align=right>$total_calls</td>\n";
        $table .= "    <td align=right>" . fmt_hms($total_seconds) . "</td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        $stmt=sprintf("SELECT count(*),status,sum(talk_sec) FROM osdial_agent_log WHERE user_group IN %s AND user='%s' and event_time >= '%s 0:00:01'  and event_time <= '%s 23:59:59' AND status!='' GROUP BY status ORDER BY status;",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);
        
        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=3><b>AGENT DISPOSITION SUMMARY</b></font></center>\n";
        $table .= "  <table align=center width=300 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>STATUS</td>\n";
        $table .= "      <td>COUNT</td>\n";
        $table .= "      <td>TIME</td>\n";
        $table .= "    </tr>\n";
        
        $total_calls=0;
        $total_seconds=0;
        $o=0;
        while ($statuses_to_print > $o) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $o)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }
        
            $total_calls += $row[0];
            $total_seconds += $row[2];
            $call_seconds = $row[2];

            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td>$row[1]</td>\n";
            $table .= "    <td align=right>$row[0]</td>\n";
            $table .= "    <td align=right>" . fmt_hms($call_seconds) . "</td>\n";
            $table .= "  </tr>\n";
        
            $o++;
        }
        
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL CALLS</td>\n";
        $table .= "    <td align=right>$total_calls</td>\n";
        $table .= "    <td align=right>" . fmt_hms($total_seconds) . "</td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        
        $table .= "<br>\n";
        $table .= "<center><font color=$default_text size=3><b>AGENT ACTIVITY LOG</b></font></center>\n";
        $table .= "<table align=center width=500 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "  <tr class=tabheader>\n";
        $table .= "    <td>EVENT</td>\n";
        $table .= "    <td>DATE</td>\n";
        $table .= "    <td>CAMPAIGN</td>\n";
        $table .= "    <td>GROUP</td>\n";
        $table .= "    <td>TIME</td>\n";
        $table .= "  </tr>\n";
        
        $stmt=sprintf("SELECT event,event_epoch,event_date,campaign_id,user_group FROM osdial_user_log WHERE user_group IN %s AND user='%s' and event_date >= '%s 0:00:01'  and event_date <= '%s 23:59:59'",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        $rslt=mysql_query($stmt, $link);
        $events_to_print = mysql_num_rows($rslt);
        
        $total_calls=0;
        $o=0;
        $event_start_seconds='';
        $event_stop_seconds='';
        while ($events_to_print > $o) {
            $row=mysql_fetch_row($rslt);
            $event = $row[0];
            $event_epoch = $row[1];
            $event_date = $row[2];
            $event_camp = $row[3];
            $user_group = $row[4];
            $event_time = '';
        
            if (ereg("LOGIN", $event)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
                $event_start_seconds = $event_epoch;
            }
            if (ereg("LOGOUT", $event)) {
                $bgcolor='bgcolor="' . $evenrows . '"';
                if ($event_start_seconds) {
                    $event_stop_seconds = $event_epoch;
                    $event_seconds = ($event_stop_seconds - $event_start_seconds);
                    $total_login_time = ($total_login_time + $event_seconds);
                    $event_time = fmt_hms($event_seconds);
                    $event_start_seconds='';
                    $event_stop_seconds='';
                }
            }
            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td>$event</td>\n";
            $table .= "    <td align=center>$event_date</td>\n";
            $table .= "    <td align=left>" . mclabel($event_camp) . "</td>\n";
            $table .= "    <td align=left>" . mclabel($user_group) . "</td>\n";
            $table .= "    <td align=right>$event_time</td>\n";
            $table .= "  </tr>\n";
            $total_calls += $event_epoch;
            $o++;
        }
        
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL</td>";
        $table .= "    <td></td>\n";
        $table .= "    <td></td>\n";
        $table .= "    <td></td>\n";
        $table .= "    <td align=right>" . fmt_hms($total_login_time) . "</td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
        

        $stmt=sprintf("SELECT sub_status AS pause_code,event_time AS pause_start,DATE_ADD(event_time,INTERVAL pause_sec SECOND) AS pause_end,pause_sec FROM osdial_agent_log WHERE osdial_agent_log.user_group IN %s AND osdial_agent_log.user='%s' AND event_time BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND pause_sec>0 ORDER BY event_time;",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        $rslt=mysql_query($stmt, $link);
        $pauses_to_print = mysql_num_rows($rslt);
        
        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=3><b>PAUSE DETAIL</b></font></center>\n";
        $table .= "  <table align=center width=500 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>CODE</td>\n";
        $table .= "      <td>START</td>\n";
        $table .= "      <td>END</td>\n";
        $table .= "      <td>TIME</td>\n";
        $table .= "    </tr>\n";

        $psecs=0;
        $u=0;
        while ($pauses_to_print > $u) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $u)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }

            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td align=left>$row[0]</td>\n";
            $table .= "    <td align=center>$row[1]</td>\n";
            $table .= "    <td align=center>$row[2]</td>\n";
            $table .= "    <td align=right>" . fmt_hms($row[3]) . "</td>\n";
            $table .= "  </tr>\n";
            $psecs += $row[3];
        
            $u++;
        }

        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL</td>";
        $table .= "    <td></td>\n";
        $table .= "    <td></td>\n";
        $table .= "    <td align=right>" . fmt_hms($psecs) . "</td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";



        
        #$stmt="select * from osdial_log where user='" . mres($agent) . "' and call_date >= '" . mres($begin_date) . " 0:00:01'  and call_date <= '" . mres($end_date) . " 23:59:59' order by call_date desc limit 10000;";
        $stmt=sprintf("SELECT osdial_agent_log.event_time, osdial_agent_log.wait_sec, osdial_agent_log.talk_sec, osdial_agent_log.dispo_sec, osdial_agent_log.pause_sec, osdial_agent_log.status, osdial_list.phone_number, osdial_agent_log.user_group, osdial_agent_log.campaign_id, osdial_list.list_id, osdial_agent_log.lead_id FROM osdial_agent_log JOIN osdial_log ON (osdial_agent_log.lead_id=osdial_log.lead_id) JOIN osdial_list ON (osdial_agent_log.lead_id=osdial_list.lead_id) WHERE osdial_agent_log.user_group IN %s AND osdial_agent_log.user='%s' AND event_time BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND osdial_log.user='%s' AND call_date BETWEEN '%s 0:00:01' AND '%s 23:59:59' ORDER BY osdial_agent_log.event_time DESC LIMIT 10000;",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent),mres($begin_date),mres($end_date),$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        $rslt=mysql_query($stmt, $link);
        $logs_to_print = mysql_num_rows($rslt);
        
        $table .= "<br>\n";
        $table .= "<center><font color=$default_text size=3><b>OUTBOUND CALLS</b></font></center>\n";
        $table .= "<center>\n";
        $th=''; if ($logs_to_print>30) $th = "height:500px;";
        $table .= "<div style=\"overflow:auto;width:770px;$th\">\n";
        $table .= "<table align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
        $table .= "  <tr class=tabheader>\n";
        $table .= "    <td colspan=12 style=\"font-size: 7pt;\">W=wait&nbsp;&nbsp;&nbsp;T=talk&nbsp;&nbsp;&nbsp;D=disposition&nbsp;&nbsp;&nbsp;P=pause</td>\n";
        $table .= "  </tr>\n";
        $table .= "  <tr class=tabheader>\n";
        $table .= "    <td># </td>\n";
        $table .= "    <td>DATE/TIME</td>\n";
        $table .= "    <td>W</td>\n";
        $table .= "    <td>T</td>\n";
        $table .= "    <td>D</td>\n";
        $table .= "    <td>P</td>\n";
        $table .= "    <td>STATUS</td>\n";
        $table .= "    <td>PHONE</td>\n";
        $table .= "    <td>GROUP</td>\n";
        $table .= "    <td>CAMPAIGN</td>\n";
        $table .= "    <td>LIST</td>\n";
        $table .= "    <td>LEAD</td>\n";
        $table .= "  </tr>\n";
        
        $u=0;
        while ($logs_to_print > $u) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $u)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }
        
            $u++;
            $event = str_replace(" ", "&nbsp;", $row[0]);
            $table .= "  <tr $bgcolor class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=1121&lead_id=$row[10]');\">\n";
            $table .= "    <td align=left title=\"Record #: $u\">$u</td>\n";
            $table .= "    <td align=center title=\"Date/Time: $event\">$event</td>\n";
            $table .= "    <td align=right title=\"Wait Time: $row[1] seconds\">$row[1]</td>\n";
            $table .= "    <td align=right title=\"Talk Time: $row[2] seconds\">$row[2]</td>\n";
            $table .= "    <td align=right title=\"Disposition Time: $row[3] seconds\">$row[3]</td>\n";
            $table .= "    <td align=right title=\"Pause Time: $row[4] seconds\">$row[4]</td>\n";
            $table .= "    <td align=left title=\"Status: $row[5]\">&nbsp;&nbsp;$row[5]</td>\n";
            $table .= "    <td align=center title=\"Phone #: $row[6]\">$row[6]</td>\n";
            $table .= "    <td align=left title=\"Agent Group: " . mclabel($row[7]) . "\">" . mclabel($row[7]) . "</td>\n";
            $table .= "    <td align=left title=\"Campaign ID: " . mclabel($row[8]) . "\">" . mclabel($row[8]) . "</td>\n";
            $table .= "    <td align=center title=\"List ID: $row[9]\">$row[9]</td>\n";
            $table .= "    <td align=right title=\"Lead #: $row[10]\"><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[10]\" target=\"_blank\">$row[10]</a></td>\n";
            $table .= "  </tr>\n";
        }
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td colspan=12></td>";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
        $table .= "</div>\n";
        $table .= "</center>\n";
        
        


        $stmt=sprintf("SELECT osdial_agent_log.event_time, osdial_agent_log.wait_sec, osdial_agent_log.talk_sec, osdial_agent_log.dispo_sec, osdial_agent_log.pause_sec, osdial_agent_log.status, osdial_list.phone_number, osdial_agent_log.user_group, osdial_agent_log.campaign_id, osdial_list.list_id, osdial_agent_log.lead_id FROM osdial_agent_log JOIN osdial_closer_log ON (osdial_agent_log.lead_id=osdial_closer_log.lead_id) JOIN osdial_list ON (osdial_agent_log.lead_id=osdial_list.lead_id) WHERE osdial_agent_log.user_group IN %s AND osdial_agent_log.user='%s' AND event_time BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND osdial_closer_log.user='%s' AND call_date BETWEEN '%s 0:00:01' AND '%s 23:59:59' ORDER BY osdial_agent_log.event_time DESC LIMIT 10000;",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent),mres($begin_date),mres($end_date),$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        $rslt=mysql_query($stmt, $link);
        $logs_to_print = mysql_num_rows($rslt);
        
        $table .= "<br>\n";
        $table .= "<center><font color=$default_text size=3><b>INBOUND/CLOSER CALLS</b></font></center>\n";
        $table .= "<center>\n";
        $th=''; if ($logs_to_print>30) $th = "height:500px;";
        $table .= "<div style=\"overflow:auto;width:770px;$th\">\n";
        $table .= "<table align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
        $table .= "  <tr class=tabheader>\n";
        $table .= "    <td colspan=12 style=\"font-size: 7pt;\">W=wait&nbsp;&nbsp;&nbsp;T=talk&nbsp;&nbsp;&nbsp;D=disposition&nbsp;&nbsp;&nbsp;P=pause</td>\n";
        $table .= "  </tr>\n";
        $table .= "  <tr class=tabheader>\n";
        $table .= "    <td># </td>\n";
        $table .= "    <td>DATE/TIME</td>\n";
        $table .= "    <td>W</td>\n";
        $table .= "    <td>T</td>\n";
        $table .= "    <td>D</td>\n";
        $table .= "    <td>P</td>\n";
        $table .= "    <td>STATUS</td>\n";
        $table .= "    <td>PHONE</td>\n";
        $table .= "    <td>GROUP</td>\n";
        $table .= "    <td>CAMPAIGN</td>\n";
        $table .= "    <td>LIST</td>\n";
        $table .= "    <td>LEAD</td>\n";
        $table .= "  </tr>\n";
        
        $u=0;
        while ($logs_to_print > $u) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $u)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }
        
            $u++;
            $event = str_replace(" ", "&nbsp;", $row[0]);
            $table .= "  <tr $bgcolor class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=1121&lead_id=$row[10]');\">\n";
            $table .= "    <td align=left title=\"Record #: $u\">$u</td>\n";
            $table .= "    <td align=center title=\"Date/Time: $event\">$event</td>\n";
            $table .= "    <td align=right title=\"Wait Time: $row[1] seconds\">$row[1]</td>\n";
            $table .= "    <td align=right title=\"Talk Time: $row[2] seconds\">$row[2]</td>\n";
            $table .= "    <td align=right title=\"Disposition Time: $row[3] seconds\">$row[3]</td>\n";
            $table .= "    <td align=right title=\"Pause Time: $row[4] seconds\">$row[4]</td>\n";
            $table .= "    <td align=left title=\"Status: $row[5]\">&nbsp;&nbsp;$row[5]</td>\n";
            $table .= "    <td align=center title=\"Phone #: $row[6]\">$row[6]</td>\n";
            $table .= "    <td align=left title=\"Agent Group: " . mclabel($row[7]) . "\">" . mclabel($row[7]) . "</td>\n";
            $table .= "    <td align=left title=\"Campaign ID: " . mclabel($row[8]) . "\">" . mclabel($row[8]) . "</td>\n";
            $table .= "    <td align=center title=\"List ID: $row[9]\">$row[9]</td>\n";
            $table .= "    <td align=right title=\"Lead #: $row[10]\"><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[10]\" target=\"_blank\">$row[10]</a></td>\n";
            $table .= "  </tr>\n";
        }
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td colspan=12></td>";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
        $table .= "</div>\n";
        $table .= "</center>\n";
        

        #$stmt=sprintf("SELECT * FROM osdial_closer_log WHERE user_group IN %s AND user='%s' and call_date >= '%s 0:00:01'  and call_date <= '%s 23:59:59' order by call_date desc limit 10000;",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        #$rslt=mysql_query($stmt, $link);
        #$logs_to_print = mysql_num_rows($rslt);
        
        #$table .= "<br>\n";
        #$table .= "<center><b><font color=$default_text size=3>INBOUND / CLOSER CALLS</b></font></center>\n";
        #$table .= "<center>\n";
        #$th=''; if ($logs_to_print>30) $th = "height:500px;";
        #$table .= "<div style=\"overflow:auto;width:770px;$th\">\n";
        #$table .= "<table align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
        #$table .= "  <tr class=tabheader>\n";
        #$table .= "    <td># </td>\n";
        #$table .= "    <td>DATE/TIME </td>\n";
        #$table .= "    <td>LEN</td>\n";
        #$table .= "    <td>STATUS</td>\n";
        #$table .= "    <td>PHONE</td>\n";
        #$table .= "    <td>CAMPAIGN</td>\n";
        #$table .= "    <td>WAIT</td>\n";
        #$table .= "    <td>LIST</td>\n";
        #$table .= "    <td>LEAD</td>\n";
        #$table .= "  </tr>\n";
        
        #$u=0;
        #while ($logs_to_print > $u) {
        #    $row=mysql_fetch_row($rslt);
        #    if (eregi("1$|3$|5$|7$|9$", $u)) {
        #        $bgcolor='bgcolor="' . $oddrows . '"';
        #    } else {
        #        $bgcolor='bgcolor="' . $evenrows . '"';
        #    }
        
        #    $u++;
        #    $row[4] = str_replace(" ", "&nbsp;", $row[4]);
        #    $table .= "  <tr $bgcolor class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=1121&lead_id=$row[1]');\">\n";
        #    $table .= "    <td title=\"Record #: $u\">$u</td>\n";
        #    $table .= "    <td align=center title=\"Date/Time: $row[4]\">$row[4]</td>\n";
        #    $table .= "    <td align=right title=\"Call Length: $row[7] seconds\">$row[7]</td>\n";
        #    $table .= "    <td align=left title=\"Status: $row[8]\">&nbsp;&nbsp;$row[8]</td>\n";
        #    $table .= "    <td align=center title=\"Phone #: $row[10]\">$row[10]</td>\n";
        #    $table .= "    <td align=left title=\"Campaign ID: " . mclabel($row[3]) . "\">&nbsp;&nbsp;" . mclabel($row[3]) . "</td>\n";
        #    $table .= "    <td align=right title=\"Wait Time: $row[14] seconds\">$row[14]</td>\n";
        #    $table .= "    <td align=left title=\"List ID: $row[2]\">&nbsp;&nbsp;$row[2]</td>\n";
        #    $table .= "    <td align=right title=\"Lead #: $row[1]\"><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[1]\" target=\"_blank\">$row[1]</a></td>\n";
        #    $table .= "  </tr>\n";
        #}
        #$table .= "  <tr class=tabfooter>\n";
        #$table .= "    <td colspan=9></td>";
        #$table .= "  </tr>\n";
        #$table .= "</table>\n";
        #$table .= "</div>\n";
        #$table .= "</center>\n";
        
        
        
        $stmt=sprintf("SELECT recording_log.* FROM recording_log JOIN osdial_users ON (recording_log.user=osdial_users.user) WHERE user_group IN %s AND recording_log.user='%s' and start_time >= '%s 0:00:01'  and start_time <= '%s 23:59:59' order by recording_id desc limit 10000;",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent),mres($begin_date),mres($end_date));
        $rslt=mysql_query($stmt, $link);
        $logs_to_print = mysql_num_rows($rslt);
        
        $table .= "<br>\n";
        $table .= "<center><font color=$default_text size=3><b>RECORDINGS</b></font></center>\n";
        $table .= "<center>\n";
        $th=''; if ($logs_to_print>30) $th = "height:500px;";
        $table .= "<div style=\"overflow:auto;width:770px;$th\">\n";
        $table .= "<table align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
        $table .= "  <tr class=tabheader>\n";
        $table .= "    <td># </td>\n";
        $table .= "    <td>LEAD</td>\n";
        $table .= "    <td>DATE/TIME</td>\n";
        $table .= "    <td>LEN</td>\n";
        $table .= "    <td>RECID</td>\n";
        $table .= "    <td>FILENAME</td>\n";
        $table .= "    <td>LOCATION</td>\n";
        $table .= "  </tr>\n";
        
        $u=0;
        while ($logs_to_print > $u) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $u)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }
        
            $Slocation = eregi_replace("^//", "/", $row[11]);
            $location = $Slocation;
            $locshort = ellipse($location,30,true);
            if (eregi("http",$location) or eregi("^/",$location)) {
                $location = "<a target=\"_new\" title=\"$location\" href=\"$location\">$locshort</a>";
            }
        
            $u++;
            $row[4] = str_replace(" ", "&nbsp;", $row[4]);
            $table .= "  <tr $bgcolor class=\"row font1\" ondblclick=\"openNewWindow('$Slocation');\">\n";
            $table .= "    <td title=\"Record #: $u\">$u</td>\n";
            $table .= "    <td align=left title=\"Lead #: $row[12]\"><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[12]\" target=\"_blank\">$row[12]</a></td>\n";
            $table .= "    <td align=center title=\"Date/Time: $row[4]\">$row[4]</td>\n";
            $table .= "    <td align=right title=\"Recording Length: $row[8] seconds\">$row[8]</td>\n";
            $table .= "    <td align=right title=\"Recording ID $row[0]\">$row[0]</td>\n";
            $table .= "    <td align=center title=\"Filename: $row[10]\">$row[10]</td>\n";
            $table .= "    <td align=center title=\"File Location: $row[11]\">$location</td>\n";
            $table .= "  </tr>\n";
        
        }
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td colspan=7></td>";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
        $table .= "</div>\n";
        $table .= "</center>\n";
    }
        
    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $STARTtime);
        
    $table .= "<br><br><br>\n";
    $table .= "<font size=0>\n";
    $table .= "  Script Runtime: $RUNtime seconds\n";
    $table .= "</font>\n";
        
    $html .= "<div class=noprint>$head</div>\n";
    $html .= "<div class=noprint>$table</div>\n";
        
    return $html;
        
}
        
?>
