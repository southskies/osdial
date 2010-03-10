<? 
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
        if (substr($agent,0,3) == $LOG['company_prefix']) {
            $agent = substr($agent,3);
        }
    }

    $NOW_DATE = date("Y-m-d");
    $NOW_TIME = date("Y-m-d H:i:s");
    $STARTtime = date("U");

    if ($query_date == "") {$query_date = $NOW_DATE;}

    $head .= "<br>\n";
    $head .= "<center><font size=4 color=$default_text>AGENT TIMESHEET</font></center><br>\n";
    if ($agent) {
        $stmt=sprintf("SELECT full_name FROM osdial_users WHERE user_group IN %s AND user='%s';",$LOG['allowed_usergroupsSQL'],$company_prefix . mres($agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $full_name = $row[0];

        $head .= "<center><font color=$default_text size=3><b>$agent - $full_name</b></font></center>\n";
        $head .= "<center>\n";
        $head .= "<span class=font2><a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$agent\">Agent Status</a>\n";
        $head .= " - <a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$agent&begin_date=$query_date&end_date=$query_date\">Agent Stats</a>\n";
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
    $form .= "<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\"></div>\n";

    if ($agent) {
        $query_date_BEGIN = "$query_date 00:00:00";   
        $query_date_END = "$query_date 23:59:59";
        $time_BEGIN = "00:00:00";   
        $time_END = "23:59:59";

        $plain .= "OSDIAL: Agent Time Sheet                             $NOW_TIME\n";

        $plain .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
        $plain .= "---------- AGENT TIME SHEET: " . $agent . " - $full_name -------------\n\n";
        


        $stmt=sprintf("SELECT event_time,UNIX_TIMESTAMP(event_time) FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' and event_time >= '%s' and user='%s' ORDER BY event_time LIMIT 1;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN),$company_prefix . mres($agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);

        $plain .= "FIRST LOGIN:          $row[0]\n";
        $firstlog = $row[0];
        $start = $row[1];

        $stmt=sprintf("SELECT event_time,UNIX_TIMESTAMP(event_time) FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' and event_time >= '%s' and user='%s' ORDER BY event_time DESC LIMIT 1;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN),$company_prefix . mres($agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$rslt .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);

        $plain .= "LAST LOG ACTIVITY:    $row[0]\n";
        $lastlog = $row[0];
        $end = $row[1];

        $login_time = ($end - $start);
        $LOGIN_TIME_H = ($login_time / 3600);
        $LOGIN_TIME_H = round($LOGIN_TIME_H, 2);
        $LOGIN_TIME_H_int = intval("$LOGIN_TIME_H");
        $LOGIN_TIME_M = ($LOGIN_TIME_H - $LOGIN_TIME_H_int);
        $LOGIN_TIME_M = ($LOGIN_TIME_M * 60);
        $LOGIN_TIME_M = round($LOGIN_TIME_M, 2);
        $LOGIN_TIME_M_int = intval("$LOGIN_TIME_M");
        $LOGIN_TIME_S = ($LOGIN_TIME_M - $LOGIN_TIME_M_int);
        $LOGIN_TIME_S = ($LOGIN_TIME_S * 60);
        $LOGIN_TIME_S = round($LOGIN_TIME_S, 0);
        if ($LOGIN_TIME_S < 10) {$LOGIN_TIME_S = "0$LOGIN_TIME_S";}
        if ($LOGIN_TIME_M_int < 10) {$LOGIN_TIME_M_int = "0$LOGIN_TIME_M_int";}
        $LOGIN_TIME_HMS = "$LOGIN_TIME_H_int:$LOGIN_TIME_M_int:$LOGIN_TIME_S";
        $pfLOGIN_TIME_HMS =        sprintf("%8s", $LOGIN_TIME_HMS);

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
        $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
        $table .= "          <td align=center>FIRST LOGIN</td>\n";
        $table .= "          <td align=right>$firstlog</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
        $table .= "          <td align=center>LAST ACTIVITY</td>\n";
        $table .= "          <td align=right>$lastlog</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabfooter>\n";
        $table .= "          <td>TOTAL LOGIN TIME</td>\n";
        $table .= "          <td align=right>$pfLOGIN_TIME_HMS</td>\n";
        $table .= "        </tr>\n";
        $table .= "      </table>\n";
        $table .= "    </td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

        # Call Summary
        $stmt=sprintf("SELECT count(*) as calls,sum(talk_sec) as talk,avg(talk_sec),sum(pause_sec),avg(pause_sec),sum(wait_sec),avg(wait_sec),sum(dispo_sec),avg(dispo_sec),avg(talk_sec+pause_sec+wait_sec+dispo_sec) FROM osdial_agent_log WHERE user_group IN %s AND event_time <= '%s' AND event_time >= '%s' AND user='%s' AND pause_sec<48800 AND wait_sec<48800 AND talk_sec<48800 AND dispo_sec<48800 LIMIT 1;",$LOG['allowed_usergroupsSQL'],mres($query_date_END),mres($query_date_BEGIN),$company_prefix . mres($agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$plain .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);

        $TOTAL_TIME = ($row[1] + $row[3] + $row[5] + $row[7]);

        $TOTAL_TIME_H = ($TOTAL_TIME / 3600);
        $TOTAL_TIME_H = round($TOTAL_TIME_H, 2);
        $TOTAL_TIME_H_int = intval("$TOTAL_TIME_H");
        $TOTAL_TIME_M = ($TOTAL_TIME_H - $TOTAL_TIME_H_int);
        $TOTAL_TIME_M = ($TOTAL_TIME_M * 60);
        $TOTAL_TIME_M = round($TOTAL_TIME_M, 2);
        $TOTAL_TIME_M_int = intval("$TOTAL_TIME_M");
        $TOTAL_TIME_S = ($TOTAL_TIME_M - $TOTAL_TIME_M_int);
        $TOTAL_TIME_S = ($TOTAL_TIME_S * 60);
        $TOTAL_TIME_S = round($TOTAL_TIME_S, 0);
        if ($TOTAL_TIME_S < 10) {$TOTAL_TIME_S = "0$TOTAL_TIME_S";}
        if ($TOTAL_TIME_M_int < 10) {$TOTAL_TIME_M_int = "0$TOTAL_TIME_M_int";}
        $TOTAL_TIME_HMS = "$TOTAL_TIME_H_int:$TOTAL_TIME_M_int:$TOTAL_TIME_S";
        $pfTOTAL_TIME_HMS =        sprintf("%8s", $TOTAL_TIME_HMS);

        $TALK_TIME_H = ($row[1] / 3600);
        $TALK_TIME_H = round($TALK_TIME_H, 2);
        $TALK_TIME_H_int = intval("$TALK_TIME_H");
        $TALK_TIME_M = ($TALK_TIME_H - $TALK_TIME_H_int);
        $TALK_TIME_M = ($TALK_TIME_M * 60);
        $TALK_TIME_M = round($TALK_TIME_M, 2);
        $TALK_TIME_M_int = intval("$TALK_TIME_M");
        $TALK_TIME_S = ($TALK_TIME_M - $TALK_TIME_M_int);
        $TALK_TIME_S = ($TALK_TIME_S * 60);
        $TALK_TIME_S = round($TALK_TIME_S, 0);
        if ($TALK_TIME_S < 10) {$TALK_TIME_S = "0$TALK_TIME_S";}
        if ($TALK_TIME_M_int < 10) {$TALK_TIME_M_int = "0$TALK_TIME_M_int";}
        $TALK_TIME_HMS = "$TALK_TIME_H_int:$TALK_TIME_M_int:$TALK_TIME_S";
        $pfTALK_TIME_HMS =        sprintf("%8s", $TALK_TIME_HMS);

        $PAUSE_TIME_H = ($row[3] / 3600);
        $PAUSE_TIME_H = round($PAUSE_TIME_H, 2);
        $PAUSE_TIME_H_int = intval("$PAUSE_TIME_H");
        $PAUSE_TIME_M = ($PAUSE_TIME_H - $PAUSE_TIME_H_int);
        $PAUSE_TIME_M = ($PAUSE_TIME_M * 60);
        $PAUSE_TIME_M = round($PAUSE_TIME_M, 2);
        $PAUSE_TIME_M_int = intval("$PAUSE_TIME_M");
        $PAUSE_TIME_S = ($PAUSE_TIME_M - $PAUSE_TIME_M_int);
        $PAUSE_TIME_S = ($PAUSE_TIME_S * 60);
        $PAUSE_TIME_S = round($PAUSE_TIME_S, 0);
        if ($PAUSE_TIME_S < 10) {$PAUSE_TIME_S = "0$PAUSE_TIME_S";}
        if ($PAUSE_TIME_M_int < 10) {$PAUSE_TIME_M_int = "0$PAUSE_TIME_M_int";}
        $PAUSE_TIME_HMS = "$PAUSE_TIME_H_int:$PAUSE_TIME_M_int:$PAUSE_TIME_S";
        $pfPAUSE_TIME_HMS =        sprintf("%8s", $PAUSE_TIME_HMS);

        $WAIT_TIME_H = ($row[5] / 3600);
        $WAIT_TIME_H = round($WAIT_TIME_H, 2);
        $WAIT_TIME_H_int = intval("$WAIT_TIME_H");
        $WAIT_TIME_M = ($WAIT_TIME_H - $WAIT_TIME_H_int);
        $WAIT_TIME_M = ($WAIT_TIME_M * 60);
        $WAIT_TIME_M = round($WAIT_TIME_M, 2);
        $WAIT_TIME_M_int = intval("$WAIT_TIME_M");
        $WAIT_TIME_S = ($WAIT_TIME_M - $WAIT_TIME_M_int);
        $WAIT_TIME_S = ($WAIT_TIME_S * 60);
        $WAIT_TIME_S = round($WAIT_TIME_S, 0);
        if ($WAIT_TIME_S < 10) {$WAIT_TIME_S = "0$WAIT_TIME_S";}
        if ($WAIT_TIME_M_int < 10) {$WAIT_TIME_M_int = "0$WAIT_TIME_M_int";}
        $WAIT_TIME_HMS = "$WAIT_TIME_H_int:$WAIT_TIME_M_int:$WAIT_TIME_S";
        $pfWAIT_TIME_HMS =        sprintf("%8s", $WAIT_TIME_HMS);

        $WRAPUP_TIME_H = ($row[7] / 3600);
        $WRAPUP_TIME_H = round($WRAPUP_TIME_H, 2);
        $WRAPUP_TIME_H_int = intval("$WRAPUP_TIME_H");
        $WRAPUP_TIME_M = ($WRAPUP_TIME_H - $WRAPUP_TIME_H_int);
        $WRAPUP_TIME_M = ($WRAPUP_TIME_M * 60);
        $WRAPUP_TIME_M = round($WRAPUP_TIME_M, 2);
        $WRAPUP_TIME_M_int = intval("$WRAPUP_TIME_M");
        $WRAPUP_TIME_S = ($WRAPUP_TIME_M - $WRAPUP_TIME_M_int);
        $WRAPUP_TIME_S = ($WRAPUP_TIME_S * 60);
        $WRAPUP_TIME_S = round($WRAPUP_TIME_S, 0);
        if ($WRAPUP_TIME_S < 10) {$WRAPUP_TIME_S = "0$WRAPUP_TIME_S";}
        if ($WRAPUP_TIME_M_int < 10) {$WRAPUP_TIME_M_int = "0$WRAPUP_TIME_M_int";}
        $WRAPUP_TIME_HMS = "$WRAPUP_TIME_H_int:$WRAPUP_TIME_M_int:$WRAPUP_TIME_S";
        $pfWRAPUP_TIME_HMS =        sprintf("%8s", $WRAPUP_TIME_HMS);

        $TALK_AVG_M = ($row[2] / 60);
        $TALK_AVG_M = round($TALK_AVG_M, 2);
        $TALK_AVG_M_int = intval("$TALK_AVG_M");
        $TALK_AVG_S = ($TALK_AVG_M - $TALK_AVG_M_int);
        $TALK_AVG_S = ($TALK_AVG_S * 60);
        $TALK_AVG_S = round($TALK_AVG_S, 0);
        if ($TALK_AVG_S < 10) {$TALK_AVG_S = "0$TALK_AVG_S";}
        $TALK_AVG_MS = "$TALK_AVG_M_int:$TALK_AVG_S";
        $pfTALK_AVG_MS =        sprintf("%6s", $TALK_AVG_MS);

        $PAUSE_AVG_M = ($row[4] / 60);
        $PAUSE_AVG_M = round($PAUSE_AVG_M, 2);
        $PAUSE_AVG_M_int = intval("$PAUSE_AVG_M");
        $PAUSE_AVG_S = ($PAUSE_AVG_M - $PAUSE_AVG_M_int);
        $PAUSE_AVG_S = ($PAUSE_AVG_S * 60);
        $PAUSE_AVG_S = round($PAUSE_AVG_S, 0);
        if ($PAUSE_AVG_S < 10) {$PAUSE_AVG_S = "0$PAUSE_AVG_S";}
        $PAUSE_AVG_MS = "$PAUSE_AVG_M_int:$PAUSE_AVG_S";
        $pfPAUSE_AVG_MS =        sprintf("%6s", $PAUSE_AVG_MS);

        $WAIT_AVG_M = ($row[6] / 60);
        $WAIT_AVG_M = round($WAIT_AVG_M, 2);
        $WAIT_AVG_M_int = intval("$WAIT_AVG_M");
        $WAIT_AVG_S = ($WAIT_AVG_M - $WAIT_AVG_M_int);
        $WAIT_AVG_S = ($WAIT_AVG_S * 60);
        $WAIT_AVG_S = round($WAIT_AVG_S, 0);
        if ($WAIT_AVG_S < 10) {$WAIT_AVG_S = "0$WAIT_AVG_S";}
        $WAIT_AVG_MS = "$WAIT_AVG_M_int:$WAIT_AVG_S";
        $pfWAIT_AVG_MS =        sprintf("%6s", $WAIT_AVG_MS);

        $WRAPUP_AVG_M = ($row[8] / 60);
        $WRAPUP_AVG_M = round($WRAPUP_AVG_M, 2);
        $WRAPUP_AVG_M_int = intval("$WRAPUP_AVG_M");
        $WRAPUP_AVG_S = ($WRAPUP_AVG_M - $WRAPUP_AVG_M_int);
        $WRAPUP_AVG_S = ($WRAPUP_AVG_S * 60);
        $WRAPUP_AVG_S = round($WRAPUP_AVG_S, 0);
        if ($WRAPUP_AVG_S < 10) {$WRAPUP_AVG_S = "0$WRAPUP_AVG_S";}
        $WRAPUP_AVG_MS = "$WRAPUP_AVG_M_int:$WRAPUP_AVG_S";
        $pfWRAPUP_AVG_MS =        sprintf("%6s", $WRAPUP_AVG_MS);

        $TOTAL_AVG_M = ($row[9] / 60);
        $TOTAL_AVG_M = round($TOTAL_AVG_M, 2);
        $TOTAL_AVG_M_int = intval("$TOTAL_AVG_M");
        $TOTAL_AVG_S = ($TOTAL_AVG_M - $TOTAL_AVG_M_int);
        $TOTAL_AVG_S = ($TOTAL_AVG_S * 60);
        $TOTAL_AVG_S = round($TOTAL_AVG_S, 0);
        if ($TOTAL_AVG_S < 10) {$TOTAL_AVG_S = "0$TOTAL_AVG_S";}
        $TOTAL_AVG_MS = "$TOTAL_AVG_M_int:$TOTAL_AVG_S";
        $pfTOTAL_AVG_MS =        sprintf("%6s", $TOTAL_AVG_MS);

        $plain .= "TOTAL CALLS TAKEN: $row[0]\n";
        $plain .= "TALK TIME:               $pfTALK_TIME_HMS     AVERAGE: $pfTALK_AVG_MS\n";
        $plain .= "PAUSE TIME:              $pfPAUSE_TIME_HMS     AVERAGE: $pfPAUSE_AVG_MS\n";
        $plain .= "WAIT TIME:               $pfWAIT_TIME_HMS     AVERAGE: $pfWAIT_AVG_MS\n";
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
        $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
        $table .= "          <td align=center>TALK</td>\n";
        $table .= "          <td align=right>$pfTALK_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfTALK_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
        $table .= "          <td align=center>PAUSE</td>\n";
        $table .= "          <td align=right>$pfPAUSE_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfPAUSE_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$oddrows class=\"row font1\">\n";
        $table .= "          <td align=center>WAIT</td>\n";
        $table .= "          <td align=right>$pfWAIT_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfWAIT_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr bgcolor=$evenrows class=\"row font1\">\n";
        $table .= "          <td align=center>DISPO</td>\n";
        $table .= "          <td align=right>$pfWRAPUP_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfWRAPUP_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabfooter>\n";
        $table .= "          <td>TOTALS</td>\n";
        $table .= "          <td align=right>$pfTOTAL_TIME_HMS</td>\n";
        $table .= "          <td align=right>$pfTOTAL_AVG_MS</td>\n";
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabfooter>\n";
        $table .= "          <td>CALLS</td>\n";
        $table .= "          <td align=right>$row[0]</td>\n";
        $table .= "          <td></td>\n";
        $table .= "        </tr>\n";
        $table .= "      </table>\n";
        $table .= "    </td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";

    }

    $html .= "<div class=noprint>$head</div>\n";
    $html .= "<div class=noprint>$form</div>\n";
    $html .= "<div class=noprint>$table</div>\n";
    $html .= "<div class=onlyprint><pre>\n\n$plain\n</pre></div>\n";

    return $html;
}
