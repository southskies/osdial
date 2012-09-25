<?php
### AST_agent_performance_detail.php
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
# 71119-2359 - First build
# 71121-0144 - Replace existing AST_agent_performance_detail.php script with this one
#            - Fixed zero division bug
# 71218-1155 - added end_date for multi-day reports
# 80428-0144 - UTF8 cleanup
#

function report_agent_performance_detail() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $query_date = get_variable('query_date');
    $end_date = get_variable('end_date');
    $group = get_variable('group');
    $user_group = get_variable('user_group');
    $shift = get_variable('shift');
    $submit = get_variable('submit');
    $SUBMIT = get_variable('SUBMIT');
    $DB = get_variable('DB');

    if (OSDstrlen($shift)<2) {$shift='ALL';}

    $html = '';

    $NOW_DATE = date("Y-m-d");
    $NOW_TIME = date("Y-m-d H:i:s");
    $STARTtime = date("U");
    if (!isset($query_date)) {$query_date = $NOW_DATE;}
    if (!isset($end_date)) {$end_date = $NOW_DATE;}
    if ($query_date=="") {$query_date = $NOW_DATE;}
    if ($end_date=="") {$end_date = $NOW_DATE;}

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
    $stmt=sprintf("SELECT user_group FROM osdial_user_groups WHERE user_group IN %s;",$LOG['allowed_usergroupsSQL']);
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $user_groups_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $user_groups_to_print) {
        $row=mysql_fetch_row($rslt);
        $user_groups[$i] =$row[0];
        $i++;
    }

    $html .= "<div class=noprint>\n";
    $html .= " <br>\n";
    $html .= " <form action=\"$PHP_SELF\" method=GET>\n";
    $html .= "  <input type=hidden name=ADD value=\"$ADD\">\n";
    $html .= "  <input type=hidden name=SUB value=\"$SUB\">\n";
    $html .= "  <input type=hidden name=DB value=\"$DB\">\n";
    $html .= "  <table width=750 align=center cellpadding=0 cellspacing=0>\n";
    $html .= "    <tr>\n";
    $html .= "      <td align=center colspan=5>\n";
    $html .= "        <font color=$default_text size=4>AGENT PERFORMANCE REPORT</font>\n";
    $html .= "      </td>\n";
    $html .= "    <tr><td colspan=5>&nbsp;</td></tr>\n";
    $html .= "    <tr class=tabheader>\n";
    $html .= "      <td align=center>Date Range</td>\n";
    $html .= "      <td align=center>Campaign</td>\n";
    $html .= "      <td align=center>User Group</td>\n";
    $html .= "      <td align=center>Shift</td>\n";
    $html .= "      <td align=center></td>\n";
    $html .= "    </tr>\n";
    $html .= "    <tr class=tabheader>\n";
    $html .= "      <td align=center>\n";
    $html .= "        <script>\nvar cal1 = new CalendarPopup('caldiv1');\ncal1.showNavigationDropdowns();\n</script>\n";
    $html .= "        <input type=text name=query_date size=10 maxlength=10 value=\"$query_date\">\n";
    $html .= "        <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(formatDate(parseDate(document.forms[0].end_date.value).addDays(1),'yyyy-MM-dd'),null);cal1.select(document.forms[0].query_date,'acal1','yyyy-MM-dd'); return false;\" name=acal1 id=acal1>\n";
    $html .= "        <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $html .= "        to <input type=text name=end_date size=10 maxlength=10 value=\"$end_date\">\n";
    $html .= "        <a href=# onclick=\"cal1.addDisabledDates('clear','clear');cal1.addDisabledDates(null,formatDate(parseDate(document.forms[0].query_date.value).addDays(-1),'yyyy-MM-dd'));cal1.select(document.forms[0].end_date,'acal2','yyyy-MM-dd'); return false;\" name=acal2 id=acal2>\n";
    $html .= "        <img width=18 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
    $html .= "      </td>\n";
    $html .= "      <td align=center>\n";
    $html .= "        <select size=1 name=group>\n";
    $html .= "          <option value=\"--ALL--\">-- ALL CAMPAIGNS --</option>\n";
    $o=0;
    while ($campaigns_to_print > $o) {
        $gsel='';
        if ($groups[$o] == $group) {
            $gsel = 'selected';
        }
        $html .= "          <option $gsel value=\"$groups[$o]\">" . mclabel($groups[$o]) . "</option>\n";
        $o++;
    }
    $html .= "        </select>\n";
    $html .= "      </td>\n";
    $html .= "      <td align=center>\n";
    $html .= "        <select size=1 name=user_group>\n";
    $html .= "          <option value=\"\">-- ALL USER GROUPS --</option>\n";
    $o=0;
    while ($user_groups_to_print > $o) {
        $gsel='';
        if ($user_groups[$o] == $user_group) {
            $gsel = 'selected';
        }
        $html .= "          <option $gsel value=\"$user_groups[$o]\">" . mclabel($user_groups[$o]) . "</option>\n";
        $o++;
    }
    $html .= "        </select>\n";
    $html .= "      </td>\n";
    $html .= "      <td align=center>\n";
    $html .= "        <select size=1 name=shift>\n";
    $html .= "          <option selected value=\"$shift\">$shift</option>\n";
    $html .= "          <option value=\"\">--</option>\n";
    $html .= "          <option value=\"AM\">AM</option>\n";
    $html .= "          <option value=\"PM\">PM</option>\n";
    $html .= "          <option value=\"ALL\">ALL</option>\n";
    $html .= "        </select>\n";
    $html .= "      </td>\n";
    $html .= "    </tr>\n";
    $html .= "    <tr class=tabheader>\n";
    $html .= "      <td align=center colspan=4 class=tabbutton>\n";
    $html .= "        <input type=submit name=submit value=SUBMIT>\n";
    $html .= "      </td>\n";
    $html .= "    </tr>\n";
    $html .= "  </table>\n";
    $html .= " </form>\n\n";
    $html .= "<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";
    $html .= "</div>\n";

    $plain = '';
    $plain_status = '';
    $table = '';
    $export = '';

    $CSVrows = 0;
    $export .= "<form method=post target=\"_new\" action=\"/admin/tocsv.php\">\n";
    $export .= "<input type=hidden name=\"name\" value=\"css\">\n";

    if ($group) {
        $time_BEGIN='00:00:00';
        $time_END='23:59:59';
        if ($shift == 'AM') {
            $time_BEGIN=$AM_shift_BEGIN;
            $time_END=$AM_shift_END;
            if (OSDstrlen($time_BEGIN) < 6) {$time_BEGIN = "03:45:00";}   
            if (OSDstrlen($time_END) < 6) {$time_END = "15:15:00";}
        }
        if ($shift == 'PM') {
            $time_BEGIN=$PM_shift_BEGIN;
            $time_END=$PM_shift_END;
            if (OSDstrlen($time_BEGIN) < 6) {$time_BEGIN = "15:15:00";}
            if (OSDstrlen($time_END) < 6) {$time_END = "23:15:00";}
        }
        if ($shift == 'ALL') {
            if (OSDstrlen($time_BEGIN) < 6) {$time_BEGIN = "00:00:00";}
            if (OSDstrlen($time_END) < 6) {$time_END = "23:59:59";}
        }
        $query_date_BEGIN = "$query_date $time_BEGIN";
        $query_date_BEGIN = dateToServer($link,'first',$query_date_BEGIN,$webClientAdjGMT,'',$webClientDST,0);
        $query_date_END = "$end_date $time_END";
        $query_date_END = dateToServer($link,'first',$query_date_END,$webClientAdjGMT,'',$webClientDST,0);

        $ugSQL = sprintf(' AND osdial_agent_log.user_group IN %s ',$LOG['allowed_usergroupsSQL']);
        if (OSDstrlen($user_group)>0) {
            $ugSQL .= sprintf(" AND osdial_agent_log.user_group='%s' ",mres($user_group));
        }

        $groupSQL = sprintf(' AND campaign_id IN %s ',$LOG['allowed_campaignsSQL']);
        if ($group!="--ALL--") {
            $groupSQL .= sprintf(" AND campaign_id='%s' ", mres($group));	
        }

        $plain .= "OSDIAL: Agent Performance Detail                        " . dateToLocal($link,'first',date('Y-m-d H:i:s'),$webClientAdjGMT,'',$webClientDST,1) . "\n";

        $plain .= "Time range: $query_date_BEGIN to $query_date_END\n\n";
        $plain .= "---------- AGENTS Details -------------\n\n";





        $statuses='--';
        $statusesTXT='';
        $statusesHEAD='';
        $statusesHTML='';
        $statusesARY=Array();
        $j=0;
        $users='--';
        $usersARY[0]='';
        $user_namesARY[0]='';
        $k=0;

        $stmt="select count(*) as calls,sum(talk_sec) as talk,full_name,osdial_users.user,sum(pause_sec),sum(wait_sec),sum(dispo_sec),status,sum(if(lead_called_count='1',1,0)) from osdial_users,osdial_agent_log where event_time <= '$query_date_END' and event_time >= '$query_date_BEGIN' and osdial_users.user=osdial_agent_log.user $groupSQL and pause_sec<36000 and wait_sec<36000 and talk_sec<36000 and dispo_sec<36000 $ugSQL group by full_name,status order by status desc,full_name limit 100000;";
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $rows_to_print = mysql_num_rows($rslt);
        $i=0;
        while ($i < $rows_to_print) {
            $row=mysql_fetch_row($rslt);
            #$row[0] = ($row[0] - 1);    # subtract 1 for login/logout event compensation
        
            $calls[$i] =        $row[0];
            $talk_sec[$i] =        $row[1];
            $full_name[$i] =    $row[2];
            $user[$i] =            $row[3];
            $pause_sec[$i] =    $row[4];
            $wait_sec[$i] =        $row[5];
            $dispo_sec[$i] =    $row[6];
            $status[$i] =        $row[7];
            $new_calls[$i] =    $row[8];
            if ( (!OSDpreg_match("/--$status[$i]--/", $statuses)) and (OSDstrlen($status[$i])>0) ) {
                $statusesTXT = sprintf("%8s", $status[$i]);
                $statusesHEAD .= "----------+";
                $statusesHTML .= " $statusesTXT |";
                $statuses .= "$status[$i]--";
                $statusesARY[$j] = $status[$i];
                $j++;
            }
            if (!OSDpreg_match("/--$user[$i]--/", $users)) {
                $users .= "$user[$i]--";
                $usersARY[$k] = $user[$i];
                $user_namesARY[$k] = $full_name[$i];
                $k++;
            }

            $i++;
        }


        $plain .= "+-----------------+----------+--------+---------+--------+--------+--------+--------+--------+--------+--------+--------+\n";
        $plain .= "|   AGENT NAME    |    ID    | CALLS  |  TIME   | PAUSE  |PAUSEAVG|  WAIT  | WAITAVG|  TALK  | TALKAVG| DISPO  | DISPAVG|\n";
        $plain .= "+-----------------+----------+--------+---------+--------+--------+--------+--------+--------+--------+--------+--------+\n";
        $plain_status .= "+-----------------+----------+$statusesHEAD\n";
        $plain_status .= "|   AGENT NAME    |    ID    |$statusesHTML\n";
        $plain_status .= "+-----------------+----------+$statusesHEAD\n";

        $table .= "<br><br>\n";
        $table .= "<table align=center cellspacing=0 cellpadding=0 width=$section_width\">\n";
        $table .= "  <tr><td align=center><font color=$default_text size=3>AGENT PERFORMANCE DETAIL</font></td></tr>\n";
        $table .= "  <tr>\n";
        $table .= "    <td align=center>\n";
        $table .= "      <div style=\"overflow: auto; width:" . $section_width . "px;\">\n";
        $table .= "      <table width=800 align=center cellspacing=1 bgcolor=grey style=\"cursor:crosshair;\">\n";
        $table .= "        <tr class=tabheader style=\"font-size: 8pt;\">\n";
        $table .= "          <td colspan=4></td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=center colspan=2>PAUSE</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=center colspan=2>WAIT</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=center colspan=2>TALK</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=center colspan=2>DISPO</td>\n";
        if (count($statusesARY) > 0) {
            $table .= "          <td align=center bgcolor=grey></td>\n";
            $table .= "          <td align=center colspan=" . count($statusesARY) . ">STATUSES</td>\n";
        }
        $table .= "        </tr>\n";
        $table .= "        <tr class=tabheader style=\"font-size: 8pt;\">\n";
        $table .= "          <td align=center nowrap>Agent Name</td>\n";
        $table .= "          <td align=center>ID</td>\n";
        $table .= "          <td align=center>Calls</td>\n";
        #$table .= "          <td align=center>NewCalls</td>\n";
        $table .= "          <td align=center>Time</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=center>Tot</td>\n";
        $table .= "          <td align=center>Avg</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=center>Tot</td>\n";
        $table .= "          <td align=center>Avg</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=center>Tot</td>\n";
        $table .= "          <td align=center>Avg</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=center>Tot</td>\n";
        $table .= "          <td align=center>Avg</td>\n";
        $head = "AGENT NAME|AGENT ID|CALLS|TIME|PAUSE TIME|PAUSE AVG|WAIT TIME|WAIT AVG|TALK TIME|TALK AVG|DISPO TIME|DISPO AVG";
        if (count($statusesARY) > 0) {
            $table .= "          <td align=center bgcolor=grey></td>\n";
            foreach ($statusesARY as $st1) {
                $head .= '|' . $st1;
                $st2 = sprintf("%6s", $st1);
                $st2 = OSDpreg_replace("/ /","&nbsp;",$st2);
                $table .= "          <td align=right style=\"font-family: monospace;\">$st2</td>\n";
            }
        }
        $table .= "        </tr>\n";

        $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $head . "\">\n";
        $CSVrows++;


        ### BEGIN loop through each user ###
        $TOTcalls=0;
        $TOTnew_calls=0;
        $TOTtime=0;
        $TOTtotTALK=0;
        $TOTtotWAIT=0;
        $TOTtotPAUSE=0;
        $TOTtotDISPO=0;
        $m=0;
        while ($m < $k) {
            $Suser=$usersARY[$m];
            $Sfull_name=$user_namesARY[$m];
            $Stime=0;
            $Scalls=0;
            $Snew_calls=0;
            $Stalk_sec=0;
            $Spause_sec=0;
            $Swait_sec=0;
            $Sdispo_sec=0;
            $SstatusesHTML='';
            $SstatusesARY = Array();

            ### BEGIN loop through each status ###
            $n=0;
            while ($n < $j) {
                $Sstatus=$statusesARY[$n];
                $SstatusTXT='';
                ### BEGIN loop through each stat line ###
                $i=0; $status_found=0;
                while ($i < $rows_to_print) {
                    if ( ($Suser=="$user[$i]") and ($Sstatus=="$status[$i]") ) {
                        $Scalls =        ($Scalls + $calls[$i]);
                        $Snew_calls =    ($Snew_calls + $new_calls[$i]);
                        $Stalk_sec =    ($Stalk_sec + $talk_sec[$i]);
                        $Spause_sec =    ($Spause_sec + $pause_sec[$i]);
                        $Swait_sec =    ($Swait_sec + $wait_sec[$i]);
                        $Sdispo_sec =    ($Sdispo_sec + $dispo_sec[$i]);
                        $SstatusTXT = sprintf("%8s", $calls[$i]);
                        $SstatusesHTML .= " $SstatusTXT |";
                        $SstatusesARY[] = $calls[$i];
                        $status_found++;
                    }
                $i++;
                }
                if ($status_found < 1) {
                    $SstatusesHTML .= "        0 |";
                    $SstatusesARY[] = '0';
                }
                ### END loop through each stat line ###
                $n++;
            }
            ### END loop through each status ###
            $Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
            $TOTcalls=($TOTcalls + $Scalls);
            $TOTnew_calls=($TOTnew_calls + $Snew_calls);
            $TOTtime=($TOTtime + $Stime);
            $TOTtotTALK=($TOTtotTALK + $Stalk_sec);
            $TOTtotWAIT=($TOTtotWAIT + $Swait_sec);
            $TOTtotPAUSE=($TOTtotPAUSE + $Spause_sec);
            $TOTtotDISPO=($TOTtotDISPO + $Sdispo_sec);
            $Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
            $Stalk_avg=0; if ( ($Scalls > 0) and ($Stalk_sec > 0) ) {$Stalk_avg = ($Stalk_sec/$Scalls);}
            $Spause_avg=0; if ( ($Scalls > 0) and ($Spause_sec > 0) ) {$Spause_avg = ($Spause_sec/$Scalls);}
            $Swait_avg=0; if ( ($Scalls > 0) and ($Swait_sec > 0) ) {$Swait_avg = ($Swait_sec/$Scalls);}
            $Sdispo_avg=0; if ( ($Scalls > 0) and ($Sdispo_sec > 0) ) {$Sdispo_avg = ($Sdispo_sec/$Scalls);}

            $Tcalls = $Scalls;;
            $Scalls =    sprintf("%6s", $Scalls);
            $Tnew_calls = $Snew_calls;
            $Snew_calls =    sprintf("%6s", $Snew_calls);

            $Tfull_name = $Sfull_name;
            $Sfull_name=    sprintf("%-15s", $Sfull_name); 
            while(OSDstrlen($Sfull_name)>15) {$Sfull_name = OSDsubstr("$Sfull_name", 0, -1);}
            $Tuser = mclabel($Suser);
            $Suser =        sprintf("%-8s", mclabel($Suser));
            while(OSDstrlen($Suser)>8) {$Suser = OSDsubstr("$Suser", 0, -1);}

            $USERtime_M = ($Stime / 60);
            $USERtime_M = round($USERtime_M, 2);
            $USERtime_M_int = intval("$USERtime_M");
            $USERtime_S = ($USERtime_M - $USERtime_M_int);
            $USERtime_S = ($USERtime_S * 60);
            $USERtime_S = round($USERtime_S, 0);
            if ($USERtime_S < 10) {$USERtime_S = "0$USERtime_S";}
            $USERtime_MS = "$USERtime_M_int:$USERtime_S";
            $pfUSERtime_MS =        sprintf("%7s", $USERtime_MS);

            $USERtotTALK_M = ($Stalk_sec / 60);
            $USERtotTALK_M = round($USERtotTALK_M, 2);
            $USERtotTALK_M_int = intval("$USERtotTALK_M");
            $USERtotTALK_S = ($USERtotTALK_M - $USERtotTALK_M_int);
            $USERtotTALK_S = ($USERtotTALK_S * 60);
            $USERtotTALK_S = round($USERtotTALK_S, 0);
            if ($USERtotTALK_S < 10) {$USERtotTALK_S = "0$USERtotTALK_S";}
            $USERtotTALK_MS = "$USERtotTALK_M_int:$USERtotTALK_S";
            $pfUSERtotTALK_MS =        sprintf("%6s", $USERtotTALK_MS);

            $USERavgTALK_M = ($Stalk_avg / 60);
            $USERavgTALK_M = round($USERavgTALK_M, 2);
            $USERavgTALK_M_int = intval("$USERavgTALK_M");
            $USERavgTALK_S = ($USERavgTALK_M - $USERavgTALK_M_int);
            $USERavgTALK_S = ($USERavgTALK_S * 60);
            $USERavgTALK_S = round($USERavgTALK_S, 0);
            if ($USERavgTALK_S < 10) {$USERavgTALK_S = "0$USERavgTALK_S";}
            $USERavgTALK_MS = "$USERavgTALK_M_int:$USERavgTALK_S";
            $pfUSERavgTALK_MS =        sprintf("%6s", $USERavgTALK_MS);

            $USERtotPAUSE_M = ($Spause_sec / 60);
            $USERtotPAUSE_M = round($USERtotPAUSE_M, 2);
            $USERtotPAUSE_M_int = intval("$USERtotPAUSE_M");
            $USERtotPAUSE_S = ($USERtotPAUSE_M - $USERtotPAUSE_M_int);
            $USERtotPAUSE_S = ($USERtotPAUSE_S * 60);
            $USERtotPAUSE_S = round($USERtotPAUSE_S, 0);
            if ($USERtotPAUSE_S < 10) {$USERtotPAUSE_S = "0$USERtotPAUSE_S";}
            $USERtotPAUSE_MS = "$USERtotPAUSE_M_int:$USERtotPAUSE_S";
            $pfUSERtotPAUSE_MS =        sprintf("%6s", $USERtotPAUSE_MS);

            $USERavgPAUSE_M = ($Spause_avg / 60);
            $USERavgPAUSE_M = round($USERavgPAUSE_M, 2);
            $USERavgPAUSE_M_int = intval("$USERavgPAUSE_M");
            $USERavgPAUSE_S = ($USERavgPAUSE_M - $USERavgPAUSE_M_int);
            $USERavgPAUSE_S = ($USERavgPAUSE_S * 60);
            $USERavgPAUSE_S = round($USERavgPAUSE_S, 0);
            if ($USERavgPAUSE_S < 10) {$USERavgPAUSE_S = "0$USERavgPAUSE_S";}
            $USERavgPAUSE_MS = "$USERavgPAUSE_M_int:$USERavgPAUSE_S";
            $pfUSERavgPAUSE_MS =        sprintf("%6s", $USERavgPAUSE_MS);

            $USERtotWAIT_M = ($Swait_sec / 60);
            $USERtotWAIT_M = round($USERtotWAIT_M, 2);
            $USERtotWAIT_M_int = intval("$USERtotWAIT_M");
            $USERtotWAIT_S = ($USERtotWAIT_M - $USERtotWAIT_M_int);
            $USERtotWAIT_S = ($USERtotWAIT_S * 60);
            $USERtotWAIT_S = round($USERtotWAIT_S, 0);
            if ($USERtotWAIT_S < 10) {$USERtotWAIT_S = "0$USERtotWAIT_S";}
            $USERtotWAIT_MS = "$USERtotWAIT_M_int:$USERtotWAIT_S";
            $pfUSERtotWAIT_MS =        sprintf("%6s", $USERtotWAIT_MS);

            $USERavgWAIT_M = ($Swait_avg / 60);
            $USERavgWAIT_M = round($USERavgWAIT_M, 2);
            $USERavgWAIT_M_int = intval("$USERavgWAIT_M");
            $USERavgWAIT_S = ($USERavgWAIT_M - $USERavgWAIT_M_int);
            $USERavgWAIT_S = ($USERavgWAIT_S * 60);
            $USERavgWAIT_S = round($USERavgWAIT_S, 0);
            if ($USERavgWAIT_S < 10) {$USERavgWAIT_S = "0$USERavgWAIT_S";}
            $USERavgWAIT_MS = "$USERavgWAIT_M_int:$USERavgWAIT_S";
            $pfUSERavgWAIT_MS =        sprintf("%6s", $USERavgWAIT_MS);
        
            $USERtotDISPO_M = ($Sdispo_sec / 60);
            $USERtotDISPO_M = round($USERtotDISPO_M, 2);
            $USERtotDISPO_M_int = intval("$USERtotDISPO_M");
            $USERtotDISPO_S = ($USERtotDISPO_M - $USERtotDISPO_M_int);
            $USERtotDISPO_S = ($USERtotDISPO_S * 60);
            $USERtotDISPO_S = round($USERtotDISPO_S, 0);
            if ($USERtotDISPO_S < 10) {$USERtotDISPO_S = "0$USERtotDISPO_S";}
            $USERtotDISPO_MS = "$USERtotDISPO_M_int:$USERtotDISPO_S";
            $pfUSERtotDISPO_MS =        sprintf("%6s", $USERtotDISPO_MS);

            $USERavgDISPO_M = ($Sdispo_avg / 60);
            $USERavgDISPO_M = round($USERavgDISPO_M, 2);
            $USERavgDISPO_M_int = intval("$USERavgDISPO_M");
            $USERavgDISPO_S = ($USERavgDISPO_M - $USERavgDISPO_M_int);
            $USERavgDISPO_S = ($USERavgDISPO_S * 60);
            $USERavgDISPO_S = round($USERavgDISPO_S, 0);
            if ($USERavgDISPO_S < 10) {$USERavgDISPO_S = "0$USERavgDISPO_S";}
            $USERavgDISPO_MS = "$USERavgDISPO_M_int:$USERavgDISPO_S";
            $pfUSERavgDISPO_MS =        sprintf("%6s", $USERavgDISPO_MS);

            $eline = "$Tfull_name|$Tuser|$Tcalls|$USERtime_MS|$USERtotPAUSE_MS|$USERavgPAUSE_MS|$USERtotWAIT_MS|$USERavgWAIT_MS|$USERtotTALK_MS|$USERavgTALK_MS|$USERtotDISPO_MS|$USERavgDISPO_MS";
            $plain .= "| $Sfull_name | $Suser | $Scalls | $pfUSERtime_MS | $pfUSERtotPAUSE_MS | $pfUSERavgPAUSE_MS | $pfUSERtotWAIT_MS | $pfUSERavgWAIT_MS | $pfUSERtotTALK_MS | $pfUSERavgTALK_MS | $pfUSERtotDISPO_MS | $pfUSERavgDISPO_MS |\n";
            $plain_status .= "| $Sfull_name | $Suser |$SstatusesHTML\n";

            $tid = "$Tfull_name ($Tuser)";
            $table .= "        <tr " . bgcolor($m) . " class=\"row\" style=\"font-size: 8pt;\">\n";
            $table .= "          <td align=left title=\"$tid\" nowrap>$Tfull_name</td>\n";
            $table .= "          <td align=left title=\"$tid\">".OSDpreg_replace('/ /','&nbsp;',$Tuser)."</td>\n";
            $table .= "          <td align=right title=\"$tid Total Calls: $Tcalls\">$Tcalls</td>\n";
            #$table .= "          <td align=right title=\"$tid\">$Tnew_Calls</td>\n";
            $table .= "          <td align=right title=\"$tid Total Time: $Tcalls\">$USERtime_MS</td>\n";
            $table .= "          <td align=center bgcolor=grey title=\"$tid\"></td>\n";
            $table .= "          <td align=right title=\"$tid Total Pause Time: $USERtotPAUSE_MS\">$USERtotPAUSE_MS</td>\n";
            $table .= "          <td align=right title=\"$tid Average Paused Time: $USERavgPAUSE_MS\">$USERavgPAUSE_MS</td>\n";
            $table .= "          <td align=center bgcolor=grey title=\"$tid\"></td>\n";
            $table .= "          <td align=right title=\"$tid Total Time Wait Time: $USERtotWAIT_MS \">$USERtotWAIT_MS</td>\n";
            $table .= "          <td align=right title=\"$tid Average Wait Time: $USERavgWAIT_MS \">$USERavgWAIT_MS</td>\n";
            $table .= "          <td align=center bgcolor=grey title=\"$tid\"></td>\n";
            $table .= "          <td align=right title=\"$tid Total Talk Time: $USERtotTALK_MS\">$USERtotTALK_MS</td>\n";
            $table .= "          <td align=right title=\"$tid Average Talk Time: $USERavgTALK_MS \">$USERavgTALK_MS</td>\n";
            $table .= "          <td align=center bgcolor=grey title=\"$tid\"></td>\n";
            $table .= "          <td align=right title=\"$tid Total Diposition Time $USERtotDISPO_MS \">$USERtotDISPO_MS</td>\n";
            $table .= "          <td align=right title=\"$tid Average Diposition Time $USERavgDISPO_MS \">$USERavgDISPO_MS</td>\n";
            if (count($SstatusesARY) > 0) {
                $table .= "          <td align=center bgcolor=grey title=\"$tid\"></td>\n";
                $sac=0;
                foreach ($SstatusesARY as $sa1) {
                    $eline .= '|' . $sa1;
                    $table .= "          <td align=right title=\"$tid Dispositioned $sa1 Calls as $statusesARY[$sac]\" style=\"font-size: 7pt; font-family: monospace;\">$sa1</td>\n";
                    $sac++;
                }
            }
            $table .= "        </tr>\n";
            $export .= "<input type=hidden name=\"row" . $CSVrows . "\" value=\"" . $eline . "\">\n";
            $CSVrows++;

            $m++;
        }
        ### END loop through each user ###


        ###### LAST LINE FORMATTING ##########
        ### BEGIN loop through each status ###
        $SUMstatusesHTML='';
        $SUMstatusesARY=Array();
        $n=0;
        while ($n < $j) {
            $Scalls=0;
            $Sstatus=$statusesARY[$n];
            $SUMstatusTXT='';
            ### BEGIN loop through each stat line ###
            $i=0; $status_found=0;
            while ($i < $rows_to_print) {
                if ($Sstatus=="$status[$i]") {
                    $Scalls =        ($Scalls + $calls[$i]);
                    $status_found++;
                }
                $i++;
            }
            ### END loop through each stat line ###
            if ($status_found < 1) {
                $SUMstatusesHTML .= "        0 |";
                $SUMstatusesARY[] = "0";
            } else {
                $SUMstatusTXT = sprintf("%8s", $Scalls);
                $SUMstatusesHTML .= " $SUMstatusTXT |";
                $SUMstatusesARY[] = $Scalls;
            }
        $n++;
        }
        ### END loop through each status ###


        $TOTcalls =    sprintf("%7s", $TOTcalls);
        $TOTnew_calls =    sprintf("%6s", $TOTnew_calls);
        $TOT_AGENTS = sprintf("%-4s", $m);

        $TOTtime_M = ($TOTtime / 60);
        $TOTtime_M = round($TOTtime_M, 2);
        $TOTtime_M_int = intval("$TOTtime_M");
        $TOTtime_S = ($TOTtime_M - $TOTtime_M_int);
        $TOTtime_S = ($TOTtime_S * 60);
        $TOTtime_S = round($TOTtime_S, 0);
        if ($TOTtime_S < 10) {$TOTtime_S = "0$TOTtime_S";}
        $TOTtime_MS = "$TOTtime_M_int:$TOTtime_S";
        $TOTtime_MS =        sprintf("%8s", $TOTtime_MS);
            while(OSDstrlen($TOTtime_MS)>8) {$TOTtime_MS = OSDsubstr("$TOTtime_MS", 0, -1);}

        $TOTtotTALK_M = ($TOTtotTALK / 60);
        $TOTtotTALK_M = round($TOTtotTALK_M, 2);
        $TOTtotTALK_M_int = intval("$TOTtotTALK_M");
        $TOTtotTALK_S = ($TOTtotTALK_M - $TOTtotTALK_M_int);
        $TOTtotTALK_S = ($TOTtotTALK_S * 60);
        $TOTtotTALK_S = round($TOTtotTALK_S, 0);
        if ($TOTtotTALK_S < 10) {$TOTtotTALK_S = "0$TOTtotTALK_S";}
        $TOTtotTALK_MS = "$TOTtotTALK_M_int:$TOTtotTALK_S";
        $TOTtotTALK_MS =        sprintf("%8s", $TOTtotTALK_MS);
            while(OSDstrlen($TOTtotTALK_MS)>8) {$TOTtotTALK_MS = OSDsubstr("$TOTtotTALK_MS", 0, -1);}

        $TOTtotDISPO_M = ($TOTtotDISPO / 60);
        $TOTtotDISPO_M = round($TOTtotDISPO_M, 2);
        $TOTtotDISPO_M_int = intval("$TOTtotDISPO_M");
        $TOTtotDISPO_S = ($TOTtotDISPO_M - $TOTtotDISPO_M_int);
        $TOTtotDISPO_S = ($TOTtotDISPO_S * 60);
        $TOTtotDISPO_S = round($TOTtotDISPO_S, 0);
        if ($TOTtotDISPO_S < 10) {$TOTtotDISPO_S = "0$TOTtotDISPO_S";}
        $TOTtotDISPO_MS = "$TOTtotDISPO_M_int:$TOTtotDISPO_S";
        $TOTtotDISPO_MS =        sprintf("%8s", $TOTtotDISPO_MS);
            while(OSDstrlen($TOTtotDISPO_MS)>8) {$TOTtotDISPO_MS = OSDsubstr("$TOTtotDISPO_MS", 0, -1);}

        $TOTtotPAUSE_M = ($TOTtotPAUSE / 60);
        $TOTtotPAUSE_M = round($TOTtotPAUSE_M, 2);
        $TOTtotPAUSE_M_int = intval("$TOTtotPAUSE_M");
        $TOTtotPAUSE_S = ($TOTtotPAUSE_M - $TOTtotPAUSE_M_int);
        $TOTtotPAUSE_S = ($TOTtotPAUSE_S * 60);
        $TOTtotPAUSE_S = round($TOTtotPAUSE_S, 0);
        if ($TOTtotPAUSE_S < 10) {$TOTtotPAUSE_S = "0$TOTtotPAUSE_S";}
        $TOTtotPAUSE_MS = "$TOTtotPAUSE_M_int:$TOTtotPAUSE_S";
        $TOTtotPAUSE_MS =        sprintf("%8s", $TOTtotPAUSE_MS);
            while(OSDstrlen($TOTtotPAUSE_MS)>8) {$TOTtotPAUSE_MS = OSDsubstr("$TOTtotPAUSE_MS", 0, -1);}

        $TOTtotWAIT_M = ($TOTtotWAIT / 60);
        $TOTtotWAIT_M = round($TOTtotWAIT_M, 2);
        $TOTtotWAIT_M_int = intval("$TOTtotWAIT_M");
        $TOTtotWAIT_S = ($TOTtotWAIT_M - $TOTtotWAIT_M_int);
        $TOTtotWAIT_S = ($TOTtotWAIT_S * 60);
        $TOTtotWAIT_S = round($TOTtotWAIT_S, 0);
        if ($TOTtotWAIT_S < 10) {$TOTtotWAIT_S = "0$TOTtotWAIT_S";}
        $TOTtotWAIT_MS = "$TOTtotWAIT_M_int:$TOTtotWAIT_S";
        $TOTtotWAIT_MS =        sprintf("%8s", $TOTtotWAIT_MS);
            while(OSDstrlen($TOTtotWAIT_MS)>8) {$TOTtotWAIT_MS = OSDsubstr("$TOTtotWAIT_MS", 0, -1);}


        $TOTavgTALK_M = 0;
        if ($TOTcalls>0) $TOTavgTALK_M = ( ($TOTtotTALK / $TOTcalls) / 60);
        $TOTavgTALK_M = round($TOTavgTALK_M, 2);
        $TOTavgTALK_M_int = intval("$TOTavgTALK_M");
        $TOTavgTALK_S = ($TOTavgTALK_M - $TOTavgTALK_M_int);
        $TOTavgTALK_S = ($TOTavgTALK_S * 60);
        $TOTavgTALK_S = round($TOTavgTALK_S, 0);
        if ($TOTavgTALK_S < 10) {$TOTavgTALK_S = "0$TOTavgTALK_S";}
        $TOTavgTALK_MS = "$TOTavgTALK_M_int:$TOTavgTALK_S";
        $TOTavgTALK_MS =        sprintf("%6s", $TOTavgTALK_MS);
            while(OSDstrlen($TOTavgTALK_MS)>6) {$TOTavgTALK_MS = OSDsubstr("$TOTavgTALK_MS", 0, -1);}

        $TOTavgDISPO_M = 0;
        if ($TOTcalls>0) $TOTavgDISPO_M = ( ($TOTtotDISPO / $TOTcalls) / 60);
        $TOTavgDISPO_M = round($TOTavgDISPO_M, 2);
        $TOTavgDISPO_M_int = intval("$TOTavgDISPO_M");
        $TOTavgDISPO_S = ($TOTavgDISPO_M - $TOTavgDISPO_M_int);
        $TOTavgDISPO_S = ($TOTavgDISPO_S * 60);
        $TOTavgDISPO_S = round($TOTavgDISPO_S, 0);
        if ($TOTavgDISPO_S < 10) {$TOTavgDISPO_S = "0$TOTavgDISPO_S";}
        $TOTavgDISPO_MS = "$TOTavgDISPO_M_int:$TOTavgDISPO_S";
        $TOTavgDISPO_MS =        sprintf("%6s", $TOTavgDISPO_MS);
            while(OSDstrlen($TOTavgDISPO_MS)>6) {$TOTavgDISPO_MS = OSDsubstr("$TOTavgDISPO_MS", 0, -1);}

        $TOTavgPAUSE_M = 0;
        if ($TOTcalls>0) $TOTavgPAUSE_M = ( ($TOTtotPAUSE / $TOTcalls) / 60);
        $TOTavgPAUSE_M = round($TOTavgPAUSE_M, 2);
        $TOTavgPAUSE_M_int = intval("$TOTavgPAUSE_M");
        $TOTavgPAUSE_S = ($TOTavgPAUSE_M - $TOTavgPAUSE_M_int);
        $TOTavgPAUSE_S = ($TOTavgPAUSE_S * 60);
        $TOTavgPAUSE_S = round($TOTavgPAUSE_S, 0);
        if ($TOTavgPAUSE_S < 10) {$TOTavgPAUSE_S = "0$TOTavgPAUSE_S";}
        $TOTavgPAUSE_MS = "$TOTavgPAUSE_M_int:$TOTavgPAUSE_S";
        $TOTavgPAUSE_MS =        sprintf("%6s", $TOTavgPAUSE_MS);
            while(OSDstrlen($TOTavgPAUSE_MS)>6) {$TOTavgPAUSE_MS = OSDsubstr("$TOTavgPAUSE_MS", 0, -1);}

        $TOTavgWAIT_M = 0;
        if ($TOTcalls>0) $TOTavgWAIT_M = ( ($TOTtotWAIT / $TOTcalls) / 60);
        $TOTavgWAIT_M = round($TOTavgWAIT_M, 2);
        $TOTavgWAIT_M_int = intval("$TOTavgWAIT_M");
        $TOTavgWAIT_S = ($TOTavgWAIT_M - $TOTavgWAIT_M_int);
        $TOTavgWAIT_S = ($TOTavgWAIT_S * 60);
        $TOTavgWAIT_S = round($TOTavgWAIT_S, 0);
        if ($TOTavgWAIT_S < 10) {$TOTavgWAIT_S = "0$TOTavgWAIT_S";}
        $TOTavgWAIT_MS = "$TOTavgWAIT_M_int:$TOTavgWAIT_S";
        $TOTavgWAIT_MS =        sprintf("%6s", $TOTavgWAIT_MS);
            while(OSDstrlen($TOTavgWAIT_MS)>6) {$TOTavgWAIT_MS = OSDsubstr("$TOTavgWAIT_MS", 0, -1);}


        $plain .= "+-----------------+----------+--------+---------+--------+--------+--------+--------+--------+--------+--------+--------+\n";
        $plain .= "|  TOTALS        AGENTS:$TOT_AGENTS | $TOTcalls| $TOTtime_MS|$TOTtotPAUSE_MS| $TOTavgPAUSE_MS |$TOTtotWAIT_MS| $TOTavgWAIT_MS |$TOTtotTALK_MS| $TOTavgTALK_MS |$TOTtotDISPO_MS| $TOTavgDISPO_MS |\n";
        $plain .= "+----------------------------+--------+---------+--------+--------+--------+--------+--------+--------+--------+--------+\n";
        $plain .= "\n";
        $plain_status .= "+-----------------+----------+$statusesHEAD\n";
        $plain_status .= "|  TOTALS                    |$SUMstatusesHTML\n";
        $plain_status .= "+-----------------+----------+$statusesHEAD\n";
        $plain_status .= "\n";

        $table .= "        <tr class=tabfooter style=\"font-size: 8pt;\">\n";
        $table .= "          <td colspan=2 align=center title=\"Total Number of Agents: $TOT_AGENTS\" nowrap>AGENTS: $TOT_AGENTS</td>\n";
        $table .= "          <td align=right title=\"Total Calls: $TOTcalls\" nowrap>&nbsp;&nbsp;$TOTcalls</td>\n";
        #$table .= "          <td align=right>$TOTnew_calls</td>\n";
        $table .= "          <td align=right title=\"Total Time: $TOTtime_MS\" nowrap>&nbsp;&nbsp;$TOTtime_MS</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=right title=\"Total Pause Time: $TOTtotPAUSE_MS\" nowrap>&nbsp;&nbsp;$TOTtotPAUSE_MS</td>\n";
        $table .= "          <td align=right title=\"Average Pause Time: $TOTavgPAUSE_MS\" nowrap>&nbsp;&nbsp;$TOTavgPAUSE_MS</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=right title=\"Total Wait Time: $TOTtotWAIT_MS\" nowrap>&nbsp;&nbsp;$TOTtotWAIT_MS</td>\n";
        $table .= "          <td align=right title=\"Average Wait Time: $TOTavgWAIT_MS\" nowrap>&nbsp;&nbsp;$TOTavgWAIT_MS</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=right title=\"Total Talk Time: $TOTtotTALK_MS\" nowrap>&nbsp;&nbsp;$TOTtotTALK_MS</td>\n";
        $table .= "          <td align=right title=\"Average Talk Time: $TOTavgTALK_MS\" nowrap>&nbsp;&nbsp;$TOTavgTALK_MS</td>\n";
        $table .= "          <td align=center bgcolor=grey></td>\n";
        $table .= "          <td align=right title=\"Total Disposition Time: $TOTtotDISPO_MS\" nowrap>&nbsp;&nbsp;$TOTtotDISPO_MS</td>\n";
        $table .= "          <td align=right title=\"Average Disposition Time: $TOTavgDISPO_MS\" nowrap>&nbsp;&nbsp;$TOTavgDISPO_MS</td>\n";
        if (count($SUMstatusesARY) > 0) {
            $table .= "          <td align=center bgcolor=grey></td>\n";
            $ssc=0;
            foreach ($SUMstatusesARY as $ss1) {
                $table .= "          <td align=right title=\"$ss1 Calls Dispositioned as $statusesARY[$ssc]\" style=\"font-family: monospace;\" nowrap>$ss1</td>\n";
                $ssc++;
            }
        }
        $table .= "        </tr>\n";
        $table .= "      </table>\n";
        $table .= "      </div>\n";
        $table .= "    </td>\n";
        $table .= "  </tr>\n";
        $table .= "</table>\n";
        $export .= "<input type=hidden name=\"rows\" value=\"" . $CSVrows . "\">\n";
        if ($LOG['export_agent_performance_detail']) $export .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
        $export .= "</form>\n";

        $html .= "<div class=onlyprint><font size=2><pre>\n$plain\n\n\n\n$plain_status</pre></font></div>\n";
        $html .= "<div class=noprint>$table<br><center>$export</center></div>";

    }

    $html .= "</body>\n";
    $html .= "</html>\n";

    return $html;
}   
?>
