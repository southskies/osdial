<?
### group_hourly_stats.php
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
# 60620-1014 - Added variable filtering to eliminate SQL injection attack threat
#            - Added required user/pass to gain access to this page
#

function report_usergroup_hourly() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $group = get_variable("group");
    $status = get_variable("status");
    $date_with_hour = get_variable("date_with_hour");
    $submit = get_variable("submit");
    $SUBMIT = get_variable("SUBMIT");

    $STARTtime = date("U");
    $TODAY = date("Y-m-d");
    $date_with_hour_default = date("Y-m-d H");
    $date_no_hour_default = $TODAY;

    if ($date_with_hour == "") {$date_with_hour = $date_with_hour_default;}
    $date_no_hour = $date_with_hour;
    $date_no_hour = eregi_replace(" ([0-9]{2})",'',$date_no_hour);
    if ($status == "") {$status = 'SALE';}

    $statcats = Array();
    $stmt = "SELECT status,category FROM (SELECT status,category FROM osdial_statuses UNION SELECT status,category FROM osdial_campaign_statuses) AS stat GROUP BY status;";
    $rslt=mysql_query($stmt, $link);
    while ($row=mysql_fetch_row($rslt)) {
        $statcats[$row[1]] .= "'" . $row[0] . "',";
    }
    foreach ($statcats as $k => $v) {
        $statcats[$k] = rtrim($v,",");
    }


    $html = '';

    $html .= "  <br><br>\n";
    $html .= "  <center><font color=$default_text size=4>USER-GROUP HOURLY STATUS REPORT</font></center>\n";
    $html .= "  <form action=$PHP_SELF method=POST>\n";
    $html .= "  <input type=hidden name=ADD value=$ADD>\n";
    $html .= "  <input type=hidden name=SUB value=$SUB>\n";
    $html .= "  <input type=hidden name=DB value=$DB>\n";
    $html .= "  <table width=600 align=center cellpadding=0 cellspacing=0>\n";
    $html .= "    <tr class=tabheader>\n";
    $html .= "      <td>Group</td>\n";
    $html .= "      <td>Category</td>\n";
    $html .= "      <td>Date &amp; Hour</td>\n";
    $html .= "    </tr>\n";
    $html .= "    <tr class=tabfooter>\n";
    $html .= "      <td align=center>\n";
    $html .= "        <select size=1 name=group>\n";

    $stmt = sprintf("SELECT * FROM osdial_user_groups WHERE user_group IN %s ORDER BY user_group;",$LOG['allowed_usergroupsSQL']);
    if ($DB) {$html .= "$stmt\n";}
    $rslt=mysql_query($stmt, $link);
    $groups_to_print = mysql_num_rows($rslt);
    $o=0;
    while ($groups_to_print > $o) {
        $rowx=mysql_fetch_row($rslt);
        $gsel = "";
        if ($group == $rowx[0]) $gsel = "selected";
        $html .= "          <option $gsel value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
        $o++;
    }
    $html .= "        </select>\n";
    $html .= "      </td>\n";
    $html .= "      <td align=center>\n";
    $html .= "        <select size=1 name=status>\n";
    ksort($statcats);
    foreach ($statcats as $k => $v) {
        $sel='';
        if ($status==$k) $sel='selected';
        $html .= "          <option $sel>$k</option>\n";
    }
    $html .= "        </select>\n";
    $html .= "      </td>\n";
    #$html .= "      <td align=center><input type=text name=status size=10 maxlength=10 value=\"$status\"></td>\n";
    $html .= "      <td align=center><input type=text name=date_with_hour size=14 maxlength=13 value=\"$date_with_hour\"></td>\n";
    $html .= "    </tr>\n";
    $html .= "    <tr class=tabfooter>\n";
    $html .= "      <td colspan=3 class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
    $html .= "    </tr>\n";
    $html .= "    </table>\n";
    $html .= "    </form>\n";
    $html .= "    <br><br>\n";
    if ($LOG['allowed_usergroupsALL'] < 1 and $LOG['user_group'] != $group) {
        $head .= "<center><font color=red>You can only run the report for others in you User Group.</font></center>\n";
        $agent == "";
    }

    if ( ($group) and ($status) and ($date_with_hour) ) {
        $stmt=sprintf("SELECT user,full_name FROM osdial_users WHERE user_group='%s' ORDER BY full_name DESC;",mres($group));
        if ($DB) {$html .= "$stmt\n";}
        $rslt=mysql_query($stmt, $link);
        $tsrs_to_print = mysql_num_rows($rslt);
        $o=0;
        while($o < $tsrs_to_print) {
            $row=mysql_fetch_row($rslt);
            $VDuser[$o] = "$row[0]";
            $VDname[$o] = "$row[1]";
            $o++;
        }

        $o=0;
        while($o < $tsrs_to_print) {
            $stmt=sprintf("SELECT count(*) FROM osdial_log WHERE call_date>='%s:00:00' AND call_date<='%s:59:59' AND user='%s';",mres($date_with_hour),mres($date_with_hour),$VDuser[$o]);
            if ($DB) {$html .= "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $VDtotal[$o] = "$row[0]";

            $stmt=sprintf("SELECT count(*) FROM osdial_log WHERE call_date>='%s 00:00:00' AND call_date<='%s 23:59:59' AND user='%s' AND status IN (%s);",mres($date_no_hour),mres($date_no_hour),$VDuser[$o],$statcats[$status]);
            if ($DB) {$html .= "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $VDday[$o] = "$row[0]";

            $stmt=sprintf("SELECT count(*) FROM osdial_log WHERE call_date>='%s:00:00' AND call_date<='%s:59:59' AND user='%s' AND status IN (%s);",mres($date_with_hour),mres($date_with_hour),$VDuser[$o],$statcats[$status]);
            if ($DB) {$html .= "$stmt\n";}
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $VDcount[$o] = "$row[0]";
            $o++;
        }

        $html .= "  <center><a href=\"./admin.php?ADD=3111&group_id=$group\">" . strtoupper(mclabel($group)) . "</a></center>\n";
        $html .= "  <table bgcolor=grey align=center width=600 cellspacing=1 cellpadding=0>\n";
        $html .= "    <tr class=tabheader>\n";
        $html .= "      <td colspan=2></td>\n";
        $html .= "      <td colspan=2>$status</td>\n";
        $html .= "      <td colspan=2></td>\n";
        $html .= "    </tr>\n";
        $html .= "    <tr class=tabheader>\n";
        $html .= "      <td>TSR</td>\n";
        $html .= "      <td>ID</td>\n";
        $html .= "      <td>HOUR</td>\n";
        $html .= "      <td>TODAY</td>\n";
        $html .= "      <td>CALLS</td>\n";
        $html .= "      <td>LINKS</td>\n";
        $html .= "    </tr>\n";

        $day_calls=0;
        $hour_calls=0;
        $total_calls=0;
        $o=0;
        while($o < $tsrs_to_print) {
            if (eregi("1$|3$|5$|7$|9$", $o)) {
                $bgcolor='bgcolor="' . $oddrows . '"';
            } else {
                $bgcolor='bgcolor="' . $evenrows . '"';
            }
            $html .= "    <tr $bgcolor class=\"row font1\">\n";
            $html .= "      <td>" . mclabel($VDuser[$o]) . "</td>";
            $html .= "      <td align=left>$VDname[$o]</td>\n";
            $html .= "      <td align=right>$VDcount[$o]</td>\n";
            $html .= "      <td align=right>$VDtotal[$o]</td>\n";
            $html .= "      <td align=right>$VDday[$o]</td>\n";
            $html .= "      <td align=center><a href=\"./admin.php?ADD=3&user=$VDuser[$o]\">MODIFY</a> | <a href=\"./admin.php?ADD=999999&SUB=21&agent=$VDuser[$o]\">STATS</a></td>\n";
            $html .= "    </tr>\n";
            $total_calls = ($total_calls + $VDtotal[$o]);
            $hour_calls = ($hour_calls + $VDcount[$o]);
            $day_calls = ($day_calls + $VDday[$o]);

            $o++;
        }

        $html .= "    <tr class=tabfooter>\n";
        $html .= "      <td>TOTAL</td>\n";
        $html .= "      <td align=right>$status</td>\n";
        $html .= "      <td align=right>$hour_calls</td>\n";
        $html .= "      <td align=right>$total_calls</td>\n";
        $html .= "      <td align=right>$day_calls</td>\n";
        $html .= "      <td></td>\n";
        $html .= "    </tr>\n";
        $html .= "  </table>\n";
    }

    $html .= "  <br><br><br>\n";

    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $STARTtime);

    $html .= "  <center><font size=0><br><br><br>script runtime: $RUNtime seconds</font></center>\n";

    return $html;
}
?>
