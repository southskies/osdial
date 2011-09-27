<?php
### report_phone_stats.php
### 
#
# Copyright (C) 2011  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

function report_phone_stats() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $phone_extension = get_variable('phone_extension');
    $phone_server_ip = get_variable('phone_server_ip');
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
        if (OSDsubstr($phone_extension,0,3) == $LOG['company_prefix']) {
            $phone_extension = OSDsubstr($phone_extension,3);
        }
    }
    
    if ($begin_date == "") {$begin_date = $TODAY;}
    if ($end_date == "") {$end_date = $TODAY;}
    
    $head .= "<br>\n";
    $head .= "<center><font color=$default_text size=4>PHONE STATS</font></center><br>\n";
    if ($phone_extension) {
        $stmt=sprintf("SELECT fullname,protocol FROM phones WHERE extension='%s' AND server_ip='%s' ORDER BY extension LIMIT 1;",mres($company_prefix.$phone_extension),mres($phone_server_ip));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $phone_fullname = $row[0];
        $phone_protocol = $row[1];

        $head .= "<center><font color=$default_text size=3><b>$phone_extension - $phone_fullname - $phone_protocol - $phone_server_ip</b></font></center>\n";
        $head .= "<center>\n";
        $head .= "<span class=font2>\n";
        $head .= "<a href=\"./admin.php?ADD=31111111111&extension=$phone_extension&server_ip=$phone_server_ip\">Modify Phone</a>\n";
        $head .= "</span>\n";
        $head .= "</center><br>\n";
    }

    $head .= "<form name=range action=$PHP_SELF method=POST>\n";
    $head .= "<input type=hidden name=ADD value=\"$ADD\">\n";
    $head .= "<input type=hidden name=SUB value=\"$SUB\">\n";
    $head .= "<input type=hidden name=DB value=\"$DB\">\n";
    $head .= "<input type=hidden name=phone_extension value=\"$phone_extension\">\n";
    $head .= "<input type=hidden name=phone_server_ip value=\"$phone_server_ip\">\n";
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
    $head .= "      <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(null,formatDate(parseDate(document.forms[0].begin_date.value).addDays(-1),'yyyy-MM-dd'));cal1.select(document.forms[0].end_date,'acal2','yyyy-MM-dd'); return false;\" name=acal2 id=acal2>\n";
    $head .= "      <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $head .= "    </td>\n";
    $head .= "    <td>\n";
    $head .= "<script>\n";
    $head .= "var phone_extensions = new Array();\n";
    $head .= "var phone_servers = new Array();\n";
    $head .= "</script>\n";
    $stmt=sprintf("SELECT fullname,extension,protocol,server_ip FROM phones ORDER BY extension;");
    $rslt=mysql_query($stmt, $link);
    $exts_to_print = mysql_num_rows($rslt);
    $o=0;
    while ($exts_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        $pes_fullname =  $row[0];
        $pes_extension = $row[1];
        $pes_protocol =  $row[2];
        $pes_server_ip = $row[3];
        $esel = ''; if ($phone_server_ip == $pes_server_ip and "$compant_prefix$phone_extension" == $pes_extension) $esel = 'selected';
        $thead .= "<option value=$o $esel>";
        $thead .= "$pes_extension - $pes_protocol - $pes_server_ip - $pes_fullname";
        $thead .= "</option>\n";
        $head .= "<script>\n";
        $head .= "phone_extensions.push('$pes_extension');\n";
        $head .= "phone_servers.push('$pes_server_ip');\n";
        $head .= "</script>\n";
        $o++;
    }
    $head .= "      <select name=pes_select onchange=\"document.range.phone_extension.value=phone_extensions[document.range.pes_select.options[this.selectedIndex].value];document.range.phone_server_ip.value=phone_servers[document.range.pes_select.options[this.selectedIndex].value];\">\n";
    $head .= $thead;
    $head .= "      </select>\n";
    $head .= "    </td>\n";
    $head .= "  </tr>\n";
    $head .= "  <tr class=tabheader>\n";
    $head .= "    <td colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
    $head .= "  </tr>\n";
    $head .= "</table>\n";
    $head .= "</form>\n";
    $head .= "<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";
    
    if (!$LOG['view_reports']) {
        $table .= "<center><font color=red>You do not have permission to view this page</font></center>\n";
    } elseif($phone_extension) {
        $query_date_BEGIN = "$start_date 00:00:00";
        $query_date_BEGIN = dateToServer($link,'first',$query_date_BEGIN,$webClientAdjGMT,'',$webClientDST,0);
        $query_date_END = "$end_date 23:59:59";
        $query_date_END = dateToServer($link,'first',$query_date_END,$webClientAdjGMT,'',$webClientDST,0);

        $stmt=sprintf("SELECT count(*),channel_group,sum(length_in_sec) FROM call_log WHERE extension='%s' AND server_ip='%s' AND start_time>='%s' AND start_time<='%s' GROUP BY channel_group ORDER BY channel_group;",mres($company_prefix.$phone_extension),mres($phone_server_ip),mres($query_date_BEGIN),mres($query_date_END));
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);
        
        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=3><b>CALL TIME AND CHANNELS</b></font></center>\n";
        $table .= "  <table align=center width=400 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>CHANNEL GROUP</td>\n";
        $table .= "      <td>COUNT</td>\n";
        $table .= "      <td>TIME</td>\n";
        $table .= "    </tr>\n";
        
        $total_calls=0;
        $total_seconds=0;
        $o=0;
        while ($statuses_to_print > $o) {
            $row=mysql_fetch_row($rslt);
            $total_calls += $row[0];
            $total_seconds += $row[2];
            $call_seconds = $row[2];

            $table .= "  <tr " . bgcolor($o) . " class=\"row font1\" style=\"white-space:nowrap;\">\n";
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



        $stmt=sprintf("SELECT number_dialed,channel_group,start_time,length_in_min FROM call_log WHERE extension='%s' AND server_ip='%s' AND start_time>='%s' AND start_time<='%s' LIMIT 1000;",mres($company_prefix.$phone_extension),mres($phone_server_ip),mres($query_date_BEGIN),mres($query_date_END));
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);
        
        $table .= "  <br>\n";
        $table .= "  <center><font color=$default_text size=3><b>LAST 1000 CALLS FOR DATE RANGE</b></font></center>\n";
        $table .= "  <table align=center width=400 cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>NUMBER</td>\n";
        $table .= "      <td>CHANNEL GROUP</td>\n";
        $table .= "      <td>DATE</td>\n";
        $table .= "      <td>TIME</td>\n";
        $table .= "    </tr>\n";
        
        $total_calls=0;
        $total_seconds=0;
        $o=0;
        while ($statuses_to_print > $o) {
            $row=mysql_fetch_row($rslt);
            $total_calls += $row[0];
            $total_seconds += $row[2];
            $call_seconds = $row[2];

            $table .= "  <tr " . bgcolor($o) . " class=\"row font1\" style=\"white-space:nowrap;\">\n";
            $table .= "    <td>$row[0]</td>\n";
            $table .= "    <td align=right>$row[1]</td>\n";
            $table .= "    <td align=right>".dateToLocal($link,'first',$row[2],$webClientAdjGMT,'',$webClientDST,1)."</td>\n";
            $table .= "    <td align=right>" . fmt_hms($call_seconds) . "</td>\n";
            $table .= "  </tr>\n";
        
            $o++;
        }
        
        $table .= "  <tr class=tabfooter>\n";
        $table .= "    <td colspan=2>TOTAL CALLS</td>\n";
        $table .= "    <td align=right>$total_calls</td>\n";
        $table .= "    <td align=right>" . fmt_hms($total_seconds) . "</td>\n";
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
