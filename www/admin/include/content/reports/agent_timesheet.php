<?php
### report_agent_timesheet.php
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
# 60619-1729 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
#

function report_agent_timesheet() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $agent = get_variable("agent");
    $query_date = get_variable("query_date");
    $submit = get_variable("submit");
    $SUBMIT = get_variable("SUBMIT");
    $DB = get_variable("DB");

    $html = '';
    $head = '';
    $form = '';
    $plain = '';
    $table = '';

    $company_prefix = "";
    if ($LOG['multicomp_user'] > 0) {
        $company_prefix = $LOG['company_prefix'];
        if (OSDsubstr($agent,0,3) == $LOG['company_prefix']) {
            $agent = OSDsubstr($agent,3,OSDstrlen($agent));
        }
    }

    $NOW_DATE = date("Y-m-d");
    $NOW_TIME = date("Y-m-d H:i:s");
    $STARTtime = date("U");

    if ($query_date == "") {$query_date = $NOW_DATE;}

    $head .= "<br>\n";
    $head .= "<center><font size=4 class=top_header color=$default_text>AGENT TIMESHEET</font></center><br>\n";
    if ($agent) {
        $stmt=sprintf("SELECT full_name FROM osdial_users WHERE user_group IN %s AND user='%s';",$LOG['allowed_usergroupsSQL'],mres($company_prefix.$agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $full_name = $row[0];

        $head .= "<center><font color=$default_text size=3><b>$agent - $full_name</b></font></center>\n";
        $head .= "<center>\n";
        $head .= "<span class=font2>\n";
        if ($LOG['view_agent_status']) $head .= "<a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$agent\">Agent Status</a>\n";
        if ($LOG['view_agent_stats']) $head .= " - <a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$agent&begin_date=$query_date&end_date=$query_date\">Agent Stats</a>\n";
        $head .= " - <a href=\"$PHP_SELF?ADD=3&user=$agent\">Modify Agent</a></span>\n";
        $head .= "</center>\n";
    }

    $form .= "<br>\n";
    $form .= "<form action=\"$PHP_SELF\" method=get>\n";
    $form .= "  <input type=hidden name=ADD value=\"$ADD\">\n";
    $form .= "  <input type=hidden name=SUB value=\"$SUB\">\n";
    $form .= "  <input type=hidden name=DB value=\"$DB\">\n";
    $form .= "  <table width=350 align=center cellspacing=0 bgcolor=grey>\n";
    $form .= "    <tr class=tabheader>\n";
    $form .= "      <td>Date</td>\n";
    $form .= "      <td>Agent ID</td>\n";
    $form .= "    </tr>\n";
    $form .= "    <tr class=tabheader>\n";
    $form .= "      <td><script>\nvar cal1 = new CalendarPopup('caldiv1');\ncal1.showNavigationDropdowns();\n</script>\n";
    $form .= "      <input type=text name=query_date size=11 maxlength=10 value=\"$query_date\">\n";
    $form .= "      <a href=# onclick=\"cal1.addDisabledDates(formatDate(new Date().addDays(1),'yyyy-MM-dd'),null);cal1.select(document.forms[0].query_date,'acal1','yyyy-MM-dd'); return false;\" name=acal1 id=acal1>\n";
    $form .= "      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a></td>\n";
    $form .= "      <td><input type=text name=agent size=16 maxlength=15 value=\"$agent\"></td>\n";
    $form .= "    </tr>\n";
    $form .= "    <tr class=tabheader>\n";
    $form .= "      <td colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
    $form .= "    </tr>\n";
    $form .= "  </table>\n";
    $form .= "</form>\n\n";
    $form .= "<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";

    $time_BEGIN = "00:00:00";   
    $time_END = "23:59:59";
    $query_date_BEGIN = "$query_date $time_BEGIN";
    $query_date_END = "$query_date $time_END";
    $query_date_BEGIN = dateToServer($link,'first',$query_date_BEGIN,$webClientAdjGMT,'',$webClientDST,0);
    $query_date_END = dateToServer($link,'first',$query_date_END,$webClientAdjGMT,'',$webClientDST,0);

    if ($agent) {

        $plain .= "OSDIAL: Agent Time Sheet                             " . dateToLocal($link,'first',date('Y-m-d H:i:s'),$webClientAdjGMT,'',$webClientDST,1) . "\n";

        $plain .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
        $plain .= "---------- AGENT TIME SHEET: " . $agent . " - $full_name -------------\n\n";
        


        $stmt=sprintf("SELECT event_time,UNIX_TIMESTAMP(event_time),server_ip FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' and event_time >= '%s' and user='%s' ORDER BY event_time LIMIT 1;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN),mres($company_prefix.$agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);

        $plain .= "FIRST LOGIN:          " . dateToLocal($link,$row[2],$row[0],$webClientAdjGMT,'',$webClientDST,1) . "\n";
        $firstlogtitle = $row[0];
        $firstlog = dateToLocal($link,$row[2],$row[0],$webClientAdjGMT,'',$webClientDST,1);
        $start = $row[1];

        $stmt=sprintf("SELECT FROM_UNIXTIME(dispo_epoch),dispo_epoch,server_ip FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' and event_time >= '%s' and user='%s' ORDER BY event_time DESC LIMIT 1;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN),mres($company_prefix.$agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);

        $plain .= "LAST LOG ACTIVITY:    " . dateToLocal($link,$row[2],$row[0],$webClientAdjGMT,'',$webClientDST,1) . "\n";
        $lastlogtitle = $row[0];
        $lastlog = dateToLocal($link,$row[2],$row[0],$webClientAdjGMT,'',$webClientDST,1);
        $end = $row[1];

        if ($end > $start) $pfLOGIN_TIME_HMS = fmt_hms($end - $start);

        $plain .= "-----------------------------------------\n";
        $plain .= "TOTAL LOGGED-IN TIME:    $pfLOGIN_TIME_HMS\n";

        if ($start == "") $start = "NONE";
        if ($end == "") $end = "NONE";
        $table .= "<br>\n";
        $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
        $table .= "  <tr><td align=center><font color=$default_text size=3>AGENT TIMES</font></td></tr>\n";
        $table .= "  <tr>\n";
        $table .= "    <td align=center>\n";
        $table .= "      <table width=300 align=center cellspacing=1 bgcolor=grey>\n";
        $table .= "        <tr class=tabheader>\n";
        $table .= "          <td></td>\n";
        $table .= "          <td>TIME</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$oddrows class=\"row font1\" title=\"FIRST LOGIN: $firstlogtitle\" style=\"white-space:nowrap;\">\n";
        $table .= "          <td align=center>FIRST LOGIN</td>\n";
        $table .= "          <td align=right>$firstlog</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$evenrows class=\"row font1\" title=\"LAST ACTIVITY: $lastlogtitle\" style=\"white-space:nowrap;\">\n";
        $table .= "          <td align=center>LAST ACTIVITY</td>\n";
        $table .= "          <td align=right>$lastlog</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabfooter style=\"white-space:nowrap;\">\n";
        $table .= "          <td>TOTAL LOGIN TIME</td>\n";
        $table .= "          <td align=right>$pfLOGIN_TIME_HMS</td>\n";
        $table .= "        </tr>\n";
        $table .= "      </table>\n";
        $table .= "    </td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        # Call Summary
        $stmt=sprintf("SELECT count(*) as calls,sum(talk_sec) as talk,avg(talk_sec),sum(pause_sec),avg(pause_sec),sum(wait_sec),avg(wait_sec),sum(dispo_sec),avg(dispo_sec),avg(talk_sec+pause_sec+wait_sec+dispo_sec) FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' AND event_time >= '%s' AND user='%s' AND pause_sec<36000 AND wait_sec<36000 AND talk_sec<36000 AND dispo_sec<36000 AND status IS NOT NULL and status != '' LIMIT 1;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN),mres($company_prefix.$agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$plain .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);


        $pfTOTAL_TIME_HMS = fmt_hms($row[1] + $row[3] + $row[5] + $row[7]);
        $pfTALK_TIME_HMS = fmt_hms($row[1]);
        $pfPAUSE_TIME_HMS = fmt_hms($row[3]);
        $pfWAIT_TIME_HMS = fmt_hms($row[5]);
        $pfWRAPUP_TIME_HMS = fmt_hms($row[7]);

        $pfTOTAL_AVG_MS = fmt_ms($row[9]);
        $pfTALK_AVG_MS = fmt_ms($row[2]);
        $pfPAUSE_AVG_MS = fmt_ms($row[4]);
        $pfWAIT_AVG_MS = fmt_ms($row[6]);
        $pfWRAPUP_AVG_MS = fmt_ms($row[8]);


        $plain .= "TOTAL CALLS TAKEN: $row[0]\n";
        $plain .= "PAUSE TIME:              $pfPAUSE_TIME_HMS     AVERAGE: $pfPAUSE_AVG_MS\n";
        $plain .= "WAIT TIME:               $pfWAIT_TIME_HMS     AVERAGE: $pfWAIT_AVG_MS\n";
        $plain .= "TALK TIME:               $pfTALK_TIME_HMS     AVERAGE: $pfTALK_AVG_MS\n";
        $plain .= "WRAPUP TIME:             $pfWRAPUP_TIME_HMS     AVERAGE: $pfWRAPUP_AVG_MS\n";
        $plain .= "----------------------------------------------------------------\n";
        $plain .= "TOTAL ACTIVE AGENT TIME: $pfTOTAL_TIME_HMS\n";
        $plain .= "\n";

        $table .= "<br><br>\n";
        $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
        $table .= "  <tr><td align=center><font color=$default_text size=3>AGENT ACTIVITY TIMES</font></td></tr>\n";
        $table .= "  <tr>\n";
        $table .= "    <td align=center>\n";
        $table .= "      <table width=300 align=center cellspacing=1 bgcolor=grey>\n";
        $table .= "        <tr class=tabheader>\n";
        $table .= "          <td></td>\n";
        $table .= "          <td>TIME</td>\n";
        $table .= "          <td>AVERAGE</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$evenrows class=\"row font1\" style=\"white-space:nowrap;\">\n";
        $table .= "          <td align=center>PAUSE</td>\n";
        $table .= "          <td align=right>$pfPAUSE_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfPAUSE_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$oddrows class=\"row font1\" style=\"white-space:nowrap;\">\n";
        $table .= "          <td align=center>WAIT</td>\n";
        $table .= "          <td align=right>$pfWAIT_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfWAIT_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$oddrows class=\"row font1\" style=\"white-space:nowrap;\">\n";
        $table .= "          <td align=center>TALK</td>\n";
        $table .= "          <td align=right>$pfTALK_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfTALK_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$evenrows class=\"row font1\" style=\"white-space:nowrap;\">\n";
        $table .= "          <td align=center>DISPO</td>\n";
        $table .= "          <td align=right>$pfWRAPUP_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfWRAPUP_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabfooter style=\"white-space:nowrap;\">\n";
        $table .= "          <td>TOTALS</td>\n";
        $table .= "          <td align=right>$pfTOTAL_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfTOTAL_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabfooter style=\"white-space:nowrap;\">\n";
        $table .= "          <td>CALLS</td>\n";
        $table .= "          <td align=right>$row[0]</td>\n";
        $table .= "          <td></td>\n";
        $table .= "        </tr>\n";
        $table .= "      </table>\n";
        $table .= "    </td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

    } else {
        # Call Summary
        $stmt=sprintf("SELECT user,count(*) as calls,sum(talk_sec) as talk,avg(talk_sec),sum(pause_sec),avg(pause_sec),sum(wait_sec),avg(wait_sec),sum(dispo_sec),avg(dispo_sec),avg(talk_sec+pause_sec+wait_sec+dispo_sec) FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' AND event_time >= '%s' AND pause_sec<36000 AND wait_sec<36000 AND talk_sec<36000 AND dispo_sec<36000 AND status IS NOT NULL and status != '' group by user;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$plain .= "$stmt\n";}

        $table .= "<br>\n";
        $table .= "<table align=center cellspacing=0 cellpadding=0>\n";
        $table .= "  <tr><td align=center><font color=$default_text size=3>AGENT TIMES</font></td></tr>\n";
        $table .= "  <tr>\n";
        $table .= "    <td align=center>\n";
        $table .= "      <table width=850 align=center cellspacing=1 bgcolor=grey>\n";
        $table .= "        <tr class=tabheader>\n";
        $table .= "          <td>AGENT</td>\n";
        $table .= "          <td width=17%>FIRST LOGIN</td>\n";
        $table .= "          <td width=17%>LAST ACTIVITY</td>\n";
        $table .= "          <td>CALLS</td>\n";
        $table .= "          <td>TOTAL TIME</td>\n";
        $table .= "          <td>AVG TIME</td>\n";
        $table .= "          <td>TOTAL PAUSE</td>\n";
        $table .= "          <td>AVG PAUSE</td>\n";
        $table .= "          <td>TOTAL WAIT</td>\n";
        $table .= "          <td>AVG WAIT</td>\n";
        $table .= "          <td>TOTAL TALK</td>\n";
        $table .= "          <td>AVG TALK</td>\n";
        $table .= "          <td>TOTAL DISPO</td>\n";
        $table .= "          <td>AVG DISPO</td>\n";
        $table .= "        </tr>\n";

        $CSVrows=0;
        $export = "<form target=\"_new\" method=\"POST\" action=\"/admin/tocsv.php\">";
        $export .= "<input type=hidden name=\"name\" value=\"css\">";
        $csvhead = "Agent|First Login|Last Activity|Calls|Total Time|Avg Time|Total Pause|Avg Pause|Total Wait|Avg Wait|Total Talk|Avg Talk|Total Dispo|Avg Dispo";
        $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $csvhead . "\">";
        $CSVrows++;

        $i=0;
        while ($row=mysql_fetch_row($rslt)) {

            $pfTOTAL_TIME_HMS = fmt_hms($row[2] + $row[4] + $row[6] + $row[8]);
            $pfTALK_TIME_HMS = fmt_hms($row[2]);
            $pfPAUSE_TIME_HMS = fmt_hms($row[4]);
            $pfWAIT_TIME_HMS = fmt_hms($row[6]);
            $pfWRAPUP_TIME_HMS = fmt_hms($row[8]);

            $pfTOTAL_AVG_MS = fmt_ms($row[10]);
            $pfTALK_AVG_MS = fmt_ms($row[3]);
            $pfPAUSE_AVG_MS = fmt_ms($row[5]);
            $pfWAIT_AVG_MS = fmt_ms($row[7]);
            $pfWRAPUP_AVG_MS = fmt_ms($row[9]);

            $stmt2=sprintf("SELECT event_time,server_ip FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' and event_time >= '%s' and user='%s' ORDER BY event_time LIMIT 1;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN),mres($row[0]));
            $rslt2=mysql_query($stmt2, $link);
            if ($DB) {$html .= "$stmt2\n";}
            $row2=mysql_fetch_row($rslt2);
            $firstlogtitle = $row2[0];
            $firstlog = dateToLocal($link,$row2[1],$row2[0],$webClientAdjGMT,'',$webClientDST,1);


            $stmt2=sprintf("SELECT FROM_UNIXTIME(dispo_epoch),server_ip FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' and event_time >= '%s' and user='%s' ORDER BY event_time DESC LIMIT 1;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN),mres($row[0]));
            $rslt2=mysql_query($stmt2, $link);
            if ($DB) {$html .= "$stmt2\n";}
            $row2=mysql_fetch_row($rslt2);
            $lastlogtitle = $row2[0];
            $lastlog = dateToLocal($link,$row2[1],$row2[0],$webClientAdjGMT,'',$webClientDST,1);

            $table .= "        <tr " . bgcolor($i) . " class=\"row font1\" style=\"white-space:nowrap;\">\n";
            $table .= "          <td align=left>$row[0]</td>\n";
            $table .= "          <td align=center title=\"FIRST LOGIN: $firstlogtitle\">$firstlog</td>\n";
            $table .= "          <td align=center title=\"LAST ACTIVITY: $lastlogtitle\">$lastlog</td>\n";
            $table .= "          <td align=right>$row[1]</td>\n";
            $table .= "          <td align=right>$pfTOTAL_TIME_HMS</td>\n";
            $table .= "          <td align=right>$pfTOTAL_AVG_MS</td>\n";
            $table .= "          <td align=right>$pfPAUSE_TIME_HMS</td>\n";
            $table .= "          <td align=right>$pfPAUSE_AVG_MS</td>\n";
            $table .= "          <td align=right>$pfWAIT_TIME_HMS</td>\n";
            $table .= "          <td align=right>$pfWAIT_AVG_MS</td>\n";
            $table .= "          <td align=right>$pfTALK_TIME_HMS</td>\n";
            $table .= "          <td align=right>$pfTALK_AVG_MS</td>\n";
            $table .= "          <td align=right>$pfWRAPUP_TIME_HMS</td>\n";
            $table .= "          <td align=right>$pfWRAPUP_AVG_MS</td>\n";
            $table .= "        </tr>\n";

            $line = $row[0] .'|'. $firstlog .'|'. $lastlog .'|'. $row[1] .'|\''. $pfTOTAL_TIME_HMS .'|\''. $pfTOTAL_AVG_MS .'|\''. $pfPAUSE_TIME_HMS .'|\''. $pfPAUSE_AVG_MS .'|\''. $pfWAIT_TIME_HMS .'|\''. $pfWAIT_AVG_MS .'|\''. $pfTALK_TIME_HMS .'|\''. $pfTALK_AVG_MS .'|\''. $pfWRAPUP_TIME_HMS .'|\''. $pfWRAPUP_AVG_MS;
            $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $line . "\">";
            $CSVrows++;
            $i++;
        }

        $table .= "      </table>\n";
        $table .= "    </td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        $export .= "<input type=hidden name=\"rows\" value=\"" . $CSVrows . "\">";
        if ($LOG['export_agent_timesheet']) $export .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
        $export .= "</form>";

        $table .= "<div class=noprint><center>$export</center></div>";
    }

    $html .= "<div class=noprint>$head</div>\n";
    $html .= "<div class=noprint>$form</div>\n";
    $html .= "<div class=noprint>$table</div>\n";
    $html .= "<div class=onlyprint><pre>\n\n$plain\n</pre></div>\n";

    return $html;
}
