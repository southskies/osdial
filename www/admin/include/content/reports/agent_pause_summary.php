<?
### agent_pause_summary.php
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

function report_agent_pause_summary() {
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
    $head .= "<center><font color=$default_text size=4>PAUSE SUMMARY</font></center><br>\n";
    if ($agent) {
        $stmt=sprintf("SELECT full_name,user_group FROM osdial_users WHERE user_group IN %s AND user='%s';",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $full_name = $row[0];
        $agent_user_group = $row[1];

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
    } else {

        $agentsql='';
        if ($agent) $agentsql = sprintf("AND osdial_agent_log.user='%s'", $company_prefix . mres($agent));


        $stmt=sprintf("SELECT sub_status AS pause_code,count(*),SUM(pause_sec) FROM osdial_agent_log WHERE osdial_agent_log.user_group IN %s %s AND event_time BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND pause_sec>0 GROUP BY pause_code;",$LOG['allowed_usergroupsSQL'],$agentsql,mres($begin_date),mres($end_date));
        if ($DB) $html .= $stmt;
        $rslt=mysql_query($stmt, $link);
        $pauses_to_print = mysql_num_rows($rslt);

        $CSVrows=0;
        $export = "<form target=\"_new\" method=\"POST\" action=\"/admin/tocsv.php\">";
        $export .= "<input type=hidden name=\"name\" value=\"apu\">";
        $csvhead = "Code|Count|Time|Avg";
        $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $csvhead . "\">";
        $CSVrows++;

        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=4>PAUSE CODE USAGE SUMMARY</font></center>\n";
        $table .= "  <table align=center width=350 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>CODE</td>\n";
        $table .= "      <td>COUNT</td>\n";
        $table .= "      <td>TIME</td>\n";
        $table .= "      <td>AVG</td>\n";
        $table .= "    </tr>\n";

        $psecs=0;
        $pcnt=0;
        $u=0;
        while ($pauses_to_print > $u) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $u)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }

            $avg = $row[2] / $row[1];
            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td align=left>$row[0]</td>\n";
            $table .= "    <td align=right>$row[1]</td>\n";
            $table .= "    <td align=right>" . fmt_hms($row[2]) . "</td>\n";
            $table .= "    <td align=right>" . fmt_hms($avg) . "</td>\n";
            $table .= "  </tr>\n";
            $psecs += $row[2];
            $pcnt += $row[1];

            $line = $row[0] . '|' . $row[1] . '|' . fmt_hms($row[2]) . '|' . fmt_hms($row[3]);
            $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $line . "\">";
        
            $CSVrows++;
            $u++;
        }

        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL</td>";
        $table .= "    <td align=right>" . $pcnt . "</td>\n";
        $table .= "    <td align=right>" . fmt_hms($psecs) . "</td>\n";
        $table .= "    <td align=right>" . fmt_hms($psecs/$pcnt) . "</td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        $export .= "<input type=hidden name=\"rows\" value=\"" . $CSVrows . "\">";
        if ($LOG['export_agent_pause_summary']) $export .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
        $export .= "</form>";

        $table .= "<div class=noprint><center>$export</center></div>";



        #$stmt=sprintf("SELECT user,event_time AS pause_start,DATE_ADD(event_time,INTERVAL pause_sec SECOND) AS pause_end,pause_sec,sub_status AS pause_code FROM osdial_agent_log WHERE osdial_agent_log.user_group IN %s %s AND event_time BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND sub_status IS NOT NULL AND sub_status != 'LOGIN' AND pause_sec>0 ORDER BY user,event_time;",$LOG['allowed_usergroupsSQL'],$agentsql,mres($begin_date),mres($end_date));
        $stmt=sprintf("SELECT user,sub_status AS pause_code,count(*),SUM(pause_sec) FROM osdial_agent_log WHERE osdial_agent_log.user_group IN %s %s AND event_time BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND pause_sec>0 GROUP BY user,pause_code;",$LOG['allowed_usergroupsSQL'],$agentsql,mres($begin_date),mres($end_date));
        if ($DB) $html .= $stmt;
        $rslt=mysql_query($stmt, $link);
        $pauses_to_print = mysql_num_rows($rslt);

        $CSVrows=0;
        $export = "<form target=\"_new\" method=\"POST\" action=\"/admin/tocsv.php\">";
        $export .= "<input type=hidden name=\"name\" value=\"aps\">";
        $csvhead = "Agent|Code|Count|Time|Avg";
        $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $csvhead . "\">";
        $CSVrows++;

        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=4>PAUSE CODE SUMMARY PER AGENT</font></center>\n";
        $table .= "  <table align=center width=350 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>AGENT</td>\n";
        $table .= "      <td>CODE</td>\n";
        $table .= "      <td>COUNT</td>\n";
        $table .= "      <td>TIME</td>\n";
        $table .= "      <td>AVG</td>\n";
        $table .= "    </tr>\n";

        $psecs=0;
        $pcnt=0;
        $u=0;
        while ($pauses_to_print > $u) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $u)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }

            $avg = $row[3] / $row[2];
            $table .= "  <tr $bgcolor class=\"row font1\">\n";
            $table .= "    <td align=left>$row[0]</td>\n";
            $table .= "    <td align=left>$row[1]</td>\n";
            $table .= "    <td align=right>$row[2]</td>\n";
            $table .= "    <td align=right>" . fmt_hms($row[3]) . "</td>\n";
            $table .= "    <td align=right>" . fmt_hms($avg) . "</td>\n";
            $table .= "  </tr>\n";
            $psecs += $row[3];
            $pcnt += $row[2];

            $line = $row[0] . '|' . $row[1] . '|' . $row[2] . '|' . fmt_hms($row[3]) . '|' . fmt_hms($row[4]);
            $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $line . "\">";
        
            $CSVrows++;
            $u++;
        }

        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL</td>";
        $table .= "    <td></td>\n";
        $table .= "    <td align=right>" . $pcnt . "</td>\n";
        $table .= "    <td align=right>" . fmt_hms($psecs) . "</td>\n";
        $table .= "    <td align=right>" . fmt_hms($psecs/$pcnt) . "</td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        $export .= "<input type=hidden name=\"rows\" value=\"" . $CSVrows . "\">";
        if ($LOG['export_agent_pause_summary']) $export .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
        $export .= "</form>";

        $table .= "<div class=noprint><center>$export</center></div>";



        #$stmt=sprintf("SELECT user,event_time AS pause_start,DATE_ADD(event_time,INTERVAL pause_sec SECOND) AS pause_end,pause_sec,sub_status AS pause_code FROM osdial_agent_log WHERE osdial_agent_log.user_group IN %s %s AND event_time BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND sub_status IS NOT NULL AND sub_status != 'LOGIN' AND pause_sec>0 ORDER BY user,event_time;",$LOG['allowed_usergroupsSQL'],$agentsql,mres($begin_date),mres($end_date));
        $stmt=sprintf("SELECT user,event_time AS pause_start,DATE_ADD(event_time,INTERVAL pause_sec SECOND) AS pause_end,pause_sec,sub_status AS pause_code FROM osdial_agent_log WHERE osdial_agent_log.user_group IN %s %s AND event_time BETWEEN '%s 0:00:01' AND '%s 23:59:59' AND pause_sec>0 ORDER BY user,event_time;",$LOG['allowed_usergroupsSQL'],$agentsql,mres($begin_date),mres($end_date));
        if ($DB) $html .= $stmt;
        $rslt=mysql_query($stmt, $link);
        $pauses_to_print = mysql_num_rows($rslt);

        $CSVrows=0;
        $export = "<form target=\"_new\" method=\"POST\" action=\"/admin/tocsv.php\">";
        $export .= "<input type=hidden name=\"name\" value=\"apd\">";
        $csvhead = "Agent|Start|End|Time|Code";
        $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $csvhead . "\">";
        $CSVrows++;

        
        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=4>PAUSE CODE DETAILS</font></center>\n";
        $table .= "  <table align=center width=500 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>AGENT</td>\n";
        $table .= "      <td>START</td>\n";
        $table .= "      <td>END</td>\n";
        $table .= "      <td>TIME</td>\n";
        $table .= "      <td>CODE</td>\n";
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
            $table .= "    <td align=left>$row[4]</td>\n";
            $table .= "  </tr>\n";
            $psecs += $row[3];

            $line = $row[0] . '|' . $row[1] . '|' . $row[2] . '|' . fmt_hms($row[3]) . '|' . $row[4];
            $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $line . "\">";
        
            $CSVrows++;
            $u++;
        }

        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td>TOTAL</td>";
        $table .= "    <td></td>\n";
        $table .= "    <td></td>\n";
        $table .= "    <td align=right>" . fmt_hms($psecs) . "</td>\n";
        $table .= "    <td></td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        $export .= "<input type=hidden name=\"rows\" value=\"" . $CSVrows . "\">";
        if ($LOG['export_agent_pause_summary']) $export .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
        $export .= "</form>";

        $table .= "<div class=noprint><center>$export</center></div>";

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
