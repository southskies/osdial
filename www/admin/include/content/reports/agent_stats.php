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
    
    if ($begin_date == "") {$begin_date = $TODAY;}
    if ($end_date == "") {$end_date = $TODAY;}
    
    $head .= "<br>\n";
    $head .= "<center><font color=$default_text size=4>AGENT STATS</font></center><br>\n";
    if ($agent) {
        $stmt="SELECT full_name from osdial_users where user='" . mysql_real_escape_string($agent) . "';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $full_name = $row[0];

        $head .= "<center><font color=$default_text size=3><b>$agent - $full_name</b></font></center>\n";
        $head .= "<center>\n";
        $head .= "<span class=font2>\n";
        $head .= "  <a href=\"./admin.php?ADD=999999&SUB=20&agent=$agent&query_date=$begin_date\">Agent Timesheet</a>\n";
        $head .= "  - <a href=\"./admin.php?ADD=999999&SUB=22&agent=$agent\">Agent Status</a>\n";
        $head .= "  - <a href=\"./admin.php?ADD=3&user=$agent\">Modify Agent</a>\n";
        $head .= "</span>\n";
        $head .= "</center><br>\n";
    }

    $head .= "<form name=range action=$PHP_SELF method=POST>\n";
    $head .= "<input type=hidden name=ADD value=\"$ADD\">\n";
    $head .= "<input type=hidden name=SUB value=\"$SUB\">\n";
    $head .= "<input type=hidden name=DB value=\"$DB\">\n";
    $head .= "<input type=hidden name=agent value=\"$agent\">\n";
    $head .= "<table align=center cellspacing=1 width=350 bgcolor=grey>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td>Date Range</td>\n";
    $head .= "    <td>Agent ID</td>\n";
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td nowrap>\n";
    $head .= "      <input type=text name=begin_date value=\"$begin_date\" size=10 maxsize=10> to \n";
    $head .= "      <input type=text name=end_date value=\"$end_date\" size=10 maxsize=10>\n";
    $head .= "    </td>\n";
    $head .= "    <td><input type=text name=agent value=\"$agent\" size=10 maxsize=10></td>\n";
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
    $head .= "  </tr>\n";
    $head .= "</table>\n";
    $head .= "</form>\n";
    
    if (!$LOGview_reports) {
        $table .= "<center><font color=red>You do not have permission to view this page</font></center>\n";

    } elseif($agent) {
        $stmt="SELECT count(*),status,sum(talk_sec) from osdial_agent_log where user='" . mysql_real_escape_string($agent) . "' and event_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and event_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59' and status!='' group by status order by status";
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);
        
        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=3><b>DISPOSITION SUMMARY</b></font></center>\n";
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
            $call_hours = ($call_seconds / 3600);
            $call_hours = round($call_hours, 2);
            $call_hours_int = intval("$call_hours");
            $call_minutes = ($call_hours - $call_hours_int);
            $call_minutes = ($call_minutes * 60);
            $call_minutes_int = round($call_minutes, 0);
            if ($call_minutes_int < 10) {$call_minutes_int = "0$call_minutes_int";}
        
            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td>$row[1]</td>\n";
            $table .= "    <td align=right>$row[0]</td>\n";
            $table .= "    <td align=right>$call_hours_int:$call_minutes_int</td>\n";
            $table .= "  </tr>\n";
        
            $o++;
        }
        
        $total_hours = ($total_seconds / 3600);
        $total_hours = round($total_hours, 2);
        $total_hours_int = intval("$total_hours");
        $total_minutes = ($total_hours - $total_hours_int);
        $total_minutes = ($total_minutes * 60);
        $total_minutes_int = round($total_minutes, 0);
        if ($total_minutes_int < 10) {$total_minutes_int = "0$total_minutes_int";}
        
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL CALLS</td>\n";
        $table .= "    <td align=right>$total_calls</td>\n";
        $table .= "    <td align=right>$total_hours_int:$total_minutes_int</td>\n";
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
        
        $stmt="SELECT event,event_epoch,event_date,campaign_id,user_group from osdial_user_log where user='" . mysql_real_escape_string($agent) . "' and event_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and event_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59'";
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
                    $event_hours = ($event_seconds / 3600);
                    $event_hours = round($event_hours, 2);
                    $event_hours_int = intval("$event_hours");
                    $event_minutes = ($event_hours - $event_hours_int);
                    $event_minutes = ($event_minutes * 60);
                    $event_minutes_int = round($event_minutes, 0);
                    if ($event_minutes_int < 10) {$event_minutes_int = "0$event_minutes_int";}
                    $event_time = "$event_hours_int:$event_minutes_int";
                    $event_start_seconds='';
                    $event_stop_seconds='';
                }
            }
            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td>$event</td>\n";
            $table .= "    <td align=center>$event_date</td>\n";
            $table .= "    <td align=left>$event_camp</td>\n";
            $table .= "    <td align=left>$user_group</td>\n";
            $table .= "    <td align=right>$event_time</td>\n";
            $table .= "  </tr>\n";
            $total_calls += $event_epoch;
            $o++;
        }
        
        $total_login_hours = ($total_login_time / 3600);
        $total_login_hours = round($total_login_hours, 2);
        $total_login_hours_int = intval("$total_login_hours");
        $total_login_minutes = ($total_login_hours - $total_login_hours_int);
        $total_login_minutes = ($total_login_minutes * 60);
        $total_login_minutes_int = round($total_login_minutes, 0);
        if ($total_login_minutes_int < 10) {$total_login_minutes_int = "0$total_login_minutes_int";}
        
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL</td>";
        $table .= "    <td></td>\n";
        $table .= "    <td></td>\n";
        $table .= "    <td></td>\n";
        $table .= "    <td align=right>$total_login_hours_int:$total_login_minutes_int</td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
        

        
        $table .= "<br>\n";
        $table .= "<center><font color=$default_text size=3><b>OUTBOUND CALLS</b></font></center>\n";
        $table .= "<table align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
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
        
        #$stmt="select * from osdial_log where user='" . mysql_real_escape_string($agent) . "' and call_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and call_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by call_date desc limit 10000;";
        $stmt="SELECT event_time, wait_sec, talk_sec, dispo_sec, pause_sec, osdial_agent_log.status, phone_number, user_group, campaign_id, list_id, osdial_agent_log.lead_id FROM osdial_agent_log, osdial_list WHERE osdial_agent_log.lead_id=osdial_list.lead_id AND osdial_agent_log.user='" . mysql_real_escape_string($agent) . "' AND event_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01' AND event_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59' ORDER BY osdial_agent_log.event_time DESC LIMIT 10000;";
        $rslt=mysql_query($stmt, $link);
        $logs_to_print = mysql_num_rows($rslt);
        
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
            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td align=left>$u</td>\n";
            $table .= "    <td align=center>$event</td>\n";
            $table .= "    <td align=right title=\"Wait (seconds)\">$row[1]</td>\n";
            $table .= "    <td align=right title=\"Talk (seconds)\">$row[2]</td>\n";
            $table .= "    <td align=right title=\"Disposition (seconds)\">$row[3]</td>\n";
            $table .= "    <td align=right title=\"Pause (seconds)\">$row[4]</td>\n";
            $table .= "    <td align=left>&nbsp;&nbsp;$row[5]</td>\n";
            $table .= "    <td align=center>$row[6]</td>\n";
            $table .= "    <td align=left>$row[7]</td>\n";
            $table .= "    <td align=left>$row[8]</td>\n";
            $table .= "    <td align=center>$row[9]</td>\n";
            $table .= "    <td align=right><a href=\"admin_modify_lead.php?lead_id=$row[10]\" target=\"_blank\">$row[10]</a></td>\n";
            $table .= "  </tr>\n";
        }
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td colspan=12></td>";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
        
        

        $table .= "<br>\n";
        $table .= "<center><b><font color=$default_text size=3>INBOUND / CLOSER CALLS</b></font></center>\n";
        $table .= "<table align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "  <tr class=tabheader>\n";
        $table .= "    <td># </td>\n";
        $table .= "    <td>DATE/TIME </td>\n";
        $table .= "    <td>LEN</td>\n";
        $table .= "    <td>STATUS</td>\n";
        $table .= "    <td>PHONE</td>\n";
        $table .= "    <td>CAMPAIGN</td>\n";
        $table .= "    <td>WAIT (S)</td>\n";
        $table .= "    <td>LIST</td>\n";
        $table .= "    <td>LEAD</td>\n";
        $table .= "  </tr>\n";
        
        $stmt="select * from osdial_closer_log where user='" . mysql_real_escape_string($agent) . "' and call_date >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and call_date <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by call_date desc limit 10000;";
        $rslt=mysql_query($stmt, $link);
        $logs_to_print = mysql_num_rows($rslt);
        
        $u=0;
        while ($logs_to_print > $u) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $u)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }
        
            $u++;
            $row[4] = str_replace(" ", "&nbsp;", $row[4]);
            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td>$u</td>\n";
            $table .= "    <td align=center>$row[4]</td>\n";
            $table .= "    <td align=right>$row[7]</td>\n";
            $table .= "    <td align=left>&nbsp;&nbsp;$row[8]</td>\n";
            $table .= "    <td align=center>$row[10]</td>\n";
            $table .= "    <td align=left>&nbsp;&nbsp;$row[3]</td>\n";
            $table .= "    <td align=right>$row[14]</td>\n";
            $table .= "    <td align=left>&nbsp;&nbsp;$row[2]</td>\n";
            $table .= "    <td align=right><a href=\"admin_modify_lead.php?lead_id=$row[1]\" target=\"_blank\">$row[1]</a></td>\n";
            $table .= "  </tr>\n";
        }
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td colspan=9></td>";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
        
        
        
        $table .= "<br>\n";
        $table .= "<center><font color=$default_text size=3><b>RECORDINGS</b></font></center>\n";
        $table .= "<table align=center width=750 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "  <tr class=tabheader>\n";
        $table .= "    <td># </td>\n";
        $table .= "    <td>LEAD</td>\n";
        $table .= "    <td>DATE/TIME</td>\n";
        $table .= "    <td>LEN</td>\n";
        $table .= "    <td>RECID</td>\n";
        $table .= "    <td>FILENAME</td>\n";
        $table .= "    <td>LOCATION</td>\n";
        $table .= "  </tr>\n";
        
        $stmt="select * from recording_log where user='" . mysql_real_escape_string($agent) . "' and start_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and start_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by recording_id desc limit 10000;";
        $rslt=mysql_query($stmt, $link);
        $logs_to_print = mysql_num_rows($rslt);
        
        $u=0;
        while ($logs_to_print > $u) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $u)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }
        
            $location = eregi_replace("^//", "/", $row[11]);
            $locshort = ellipse($location,30,true);
            if (eregi("http",$location) or eregi("^/",$location)) {
                $location = "<a target=\"_new\" title=\"$location\" href=\"$location\">$locshort</a>";
            }
        
            $u++;
            $row[4] = str_replace(" ", "&nbsp;", $row[4]);
            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td>$u</td>\n";
            $table .= "    <td align=left><a href=\"admin_modify_lead.php?lead_id=$row[12]\" target=\"_blank\">$row[12]</a></td>\n";
            $table .= "    <td align=center>$row[4]</td>\n";
            $table .= "    <td align=right>$row[8]</td>\n";
            $table .= "    <td align=right>$row[0]</td>\n";
            $table .= "    <td align=center>$row[10]</td>\n";
            $table .= "    <td align=center>$location</td>\n";
            $table .= "  </tr>\n";
        
        }
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td colspan=7></td>";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
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