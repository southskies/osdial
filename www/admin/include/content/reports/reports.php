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




######################
# ADD=999999 display reports section
######################
if ($LOG['view_reports']==1) {
    if ($ADD==999999 and $SUB=='') {

        $stmt="SELECT * FROM servers;";
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $servers_to_print = mysql_num_rows($rslt);
        $i=0;
        while ($i < $servers_to_print) {
            $row=mysql_fetch_row($rslt);
            $server_id[$i] =            $row[0];
            $server_description[$i] =   $row[1];
            $server_ip[$i] =            $row[2];
            $active[$i] =               $row[3];
            $profile[$i] =              $row[22];
            $i++;
        }

        $stmt="SELECT enable_queuemetrics_logging,queuemetrics_url from system_settings;";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $enable_queuemetrics_logging_LU =   $row[0];
        $queuemetrics_url_LU =              $row[1];

        $margins="margin:0 0 0 120px";

        echo "<table width=60% align=center><tr class=no-ul><td>\n";
        echo "<font face=\"dejavu sans,verdana,sans-serif\" size=2>\n";
        echo "<font size=4 class=top_header color=$default_text><br><center>REPORTS</center></font><br><br>";

        echo "<div bgcolor=#DDD class=shadedtable style='margin:0 0 20px 40px'><br />";
        echo "<ul style='margin:0 0 20px 0'>";
        if ($LOG['view_agent_realtime']) echo "<li style=\"$margins\"><font face=\"dejavu sans,verdana,sans-serif\" size=2><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12\">Agent Realtime (per campaign)</a></font>\n";
        if ($LOG['view_agent_realtime_sip_listen']) echo "<li style=\"$margins\"><font face=\"dejavu sans,verdana,sans-serif\" size=2><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12&SIPmonitorLINK=1\">Agent Realtime w/SIP Listen</a></font>\n";
        if ($LOG['view_agent_realtime_sip_barge']) echo "<li style=\"$margins\"><font face=\"dejavu sans,verdana,sans-serif\" size=2><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12&SIPmonitorLINK=2\">Agent Realtime w/SIP Barge</a></font>\n";
        if ($LOG['view_agent_realtime_sip_barge']) echo "<li style=\"$margins\"><font face=\"dejavu sans,verdana,sans-serif\" size=2><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12&SIPmonitorLINK=3\">Agent Realtime w/SIP Whisper</a></font>\n";
        if ($LOG['view_agent_realtime_iax_listen']) echo "<li style=\"$margins\"><font face=\"dejavu sans,verdana,sans-serif\" size=2><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12&IAXmonitorLINK=1\">Agent Realtime w/IAX Listen</a></font>\n";
        if ($LOG['view_agent_realtime_iax_barge']) echo "<li style=\"$margins\"><font face=\"dejavu sans,verdana,sans-serif\" size=2><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12&IAXmonitorLINK=2\">Agent Realtime w/IAX Barge</a></font>\n";
        if ($LOG['view_agent_realtime_iax_barge']) echo "<li style=\"$margins\"><font face=\"dejavu sans,verdana,sans-serif\" size=2><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12&IAXmonitorLINK=3\">Agent Realtime w/IAX Whisper</a></font>\n";
        if ($LOG['view_agent_realtime_summary']) echo "<li style=\"$margins\"><font face=\"dejavu sans,verdana,sans-serif\" size=2><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=11&active_only=Y\">Agent Realtime Summary (all campaigns)</a></font>\n";
        echo "</ul>";
        echo "<ul>";
        if ($LOG['view_agent_pause_summary']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=25\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Agent Pause Summary</a></font>";
        if ($LOG['view_agent_performance_detail']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=19\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Agent Performance Detail</a></font>";
        if ($LOG['view_agent_stats']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=21\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Agent Stats</a></font>";
        if ($LOG['view_agent_status']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=22\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Agent Status</a></font>";
        if ($LOG['view_agent_timesheet']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=20\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Agent Timesheet</a></font>";
        if ($LOG['view_agent_stats']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=31\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Phone Stats</a></font>";
        if ($LOG['view_usergroup_hourly_stats']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=24\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>UserGroup Hourly Stats</a></font>";
        echo "</ul>";
        echo "<ul>";
        if ($LOG['view_ingroup_call_report']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=23\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>In-Group Call Report</a></font>";
        echo "</ul>";
        echo "<ul>";
        if ($LOG['view_campaign_call_report']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=15\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Campaign Call Report</a></font>";
        if ($LOG['view_campaign_recent_outbound_sales']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=osdial_sales_viewer.php\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Recent Outbound Sales</a></font>";
        echo "</ul>";
        echo "<ul>";
        if ($LOG['view_lead_performance_campaign']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=17\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Lead Performance by Campaign</a></font>";
        if ($LOG['view_lead_performance_list']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=18\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Lead Performance by List</a></font>";
        if ($LOG['view_lead_search']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=27\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Lead Search - Basic</a></font>";
        if ($LOG['view_lead_search_advanced']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=26\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Lead Search - Advanced</a></font>";
        if ($LOG['view_list_cost_entry']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=16\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>List Cost by Entry Date</a></font>";
        echo "</ul>";
        echo "<ul>";
        if (file_exists($WeBServeRRooT . '/admin/include/content/reports/acct_detail.php')) {
            if ($LOG['multicomp']>0) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=32\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Accounting Detail</a></font>";
        }
        if ($LOG['multicomp_user'] == 0 and $LOG['modify_servers']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=30\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>View Webserver Admin Log</a></font>";
        if ($LOG['multicomp_user'] == 0 and $LOG['view_server_performance']) echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=29\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>Server Performance</a></font>";

        if ($LOG['multicomp_user'] == 0 and $enable_queuemetrics_logging_LU > 0) {
            echo "<li style=\"$margins\"><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=$queuemetrics_url_LU\"><font face=\"dejavu sans,verdana,sans-serif\" size=2>QUEUEMETRICS REPORTS</a></font>\n";
        }
        echo "</ul><br /></div>";
        echo "</font></td></tr></table>\n";

        if ($LOG['multicomp_user'] == 0 and $LOG['view_server_times']) {
            echo "<center><pre><table class=shadedtable width=$section_width cellspacing=1 bgcolor=grey>";
            echo "<tr class=tabheader>";
            echo "  <td align=center>Server</td>";
            echo "  <td align=center>Description</td>";
            echo "  <td align=center>IP Address</td>";
            echo "  <td align=center>Active</td>";
            echo "  <td align=center>Vitals</td>";
            echo "  <td align=center colspan=3>Dialer Session Time</td>";
            echo "  </tr>";

            $o=0;
            while ($servers_to_print > $o) {
                echo "<tr ".bgcolor($o)." class=row>";
                echo "  <td align=left><font face=\"dejavu sans,verdana,sans-serif\" size=2>&nbsp;$server_id[$o]</font></td>\n";
                echo "  <td align=left><font face=\"dejavu sans,verdana,sans-serif\" size=2>&nbsp;$server_description[$o]</font></td>\n";
                echo "  <td align=right><font face=\"dejavu sans,verdana,sans-serif\" size=2>$server_ip[$o]&nbsp;</font></td>\n";
                echo "  <td align=center><font face=\"dejavu sans,verdana,sans-serif\" size=2>$active[$o]</font></td>\n";
                if ($active[$o] == 'N') {
                    echo "  <td align=center title=\"Not Active\"><font color=red>-----</font></td>\n";
                    echo "  <td align=center title=\"Not Active\"><font color=red>-----</font></td>\n";
                    echo "  <td align=center title=\"Not Active\"><font color=red>-----</font></td>\n";
                    echo "  <td align=center title=\"Not Active\"><font color=red>-----</font></td>\n";
                } else {
                    echo "  <td align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=/sysinfo/$server_ip[$o]/psi/index.php\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>SYSINFO</font></a></td>\n";
                    if ($profile[$o]=='AIO' or $profile[$o]=='DIALER') {
                        echo "  <td align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonVDAD.php?server_ip=$server_ip[$o]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>AGENTS/CHANNELS</font></a></td>\n";
                        echo "  <td align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonpark.php?server_ip=$server_ip[$o]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>PARKING</font></a></td>\n";
                        echo "  <td align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonVDAD.php?server_ip=$server_ip[$o]%26closer_display=1\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>INBOUND/CLOSERS</font></a></td>\n";
                    } else {
                        echo "  <td align=center colspan=3 title=\"Not a Dialer\"></td>\n";
                    }
                }
                echo "</tr>";
                $o++;
            }
            echo "  <tr class=tabfooter><td colspan=8></td></tr>\n";
            echo "</table></pre></center>\n";
        }

    } elseif ($ADD==999999) {
        if ($SUB==11 and $LOG['view_agent_realtime_summary']) {
            require($WeBServeRRooT . '/admin/include/content/reports/realtime_summary.php');
            echo report_realtime_summary();
        } elseif ($SUB==12 and $LOG['view_agent_realtime']) {
            require($WeBServeRRooT . '/admin/include/content/reports/realtime_detail.php');
            echo report_realtime_detail();
        } else {
            if ($SUB==15 and $LOG['view_campaign_call_report']) {
                require($WeBServeRRooT . '/admin/include/content/reports/call_stats.php');
                echo report_call_stats();
            } elseif ($SUB==16 and $LOG['view_list_cost_entry']) {
                require($WeBServeRRooT . '/admin/include/content/reports/list_cost.php');
                echo report_list_cost();
            } elseif ($SUB==17 and $LOG['view_lead_performance_campaign']) {
                require($WeBServeRRooT . '/admin/include/content/reports/lead_performance_campaign.php');
                echo report_lead_performance_campaign();
            } elseif ($SUB==18 and $LOG['view_lead_performance_list']) {
                require($WeBServeRRooT . '/admin/include/content/reports/lead_performance_list.php');
                echo report_lead_performance_list();
            } elseif ($SUB==19 and $LOG['view_agent_performance_detail']) {
                require($WeBServeRRooT . '/admin/include/content/reports/agent_performance_detail.php');
                echo report_agent_performance_detail();
            } elseif ($SUB==20 and $LOG['view_agent_timesheet']) {
                require($WeBServeRRooT . '/admin/include/content/reports/agent_timesheet.php');
                echo report_agent_timesheet();
            } elseif ($SUB==21 and $LOG['view_agent_stats']) {
                require($WeBServeRRooT . '/admin/include/content/reports/agent_stats.php');
                echo report_agent_stats();
            } elseif ($SUB==22 and $LOG['view_agent_status']) {
                require($WeBServeRRooT . '/admin/include/content/reports/agent_status.php');
                echo report_agent_status();
            } elseif ($SUB==23 and $LOG['view_ingroup_call_report']) {
                require($WeBServeRRooT . '/admin/include/content/reports/closer_stats.php');
                echo report_closer_stats();
            } elseif ($SUB==24 and $LOG['view_usergroup_hourly_stats']) {
                require($WeBServeRRooT . '/admin/include/content/reports/usergroup_hourly.php');
                echo report_usergroup_hourly();
            } elseif ($SUB==25 and $LOG['view_agent_pause_summary']) {
                require($WeBServeRRooT . '/admin/include/content/reports/agent_pause_summary.php');
                echo report_agent_pause_summary();
            } elseif ($SUB==26 and $LOG['view_lead_search_advanced']) {
                require($WeBServeRRooT . '/admin/include/content/reports/lead_search_advanced.php');
                flush();
                echo report_lead_search_advanced('form');
                flush();
                echo report_lead_search_advanced('data');
                flush();
            } elseif ($SUB==27 and $LOG['view_lead_search']) {
                require($WeBServeRRooT . '/admin/include/content/reports/lead_search_basic.php');
                echo report_lead_search_basic();
            } elseif ($SUB==28 and $LOG['view_campaign_call_report']) {
                require($WeBServeRRooT . '/admin/include/content/reports/hopperlist.php');
                echo report_hopperlist();
            } elseif ($SUB==29 and $LOG['view_server_performance']) {
                require($WeBServeRRooT . '/admin/include/content/reports/server_performance.php');
                echo report_server_performance();
            } elseif ($SUB==30 and $LOG['modify_servers']) {
                require($WeBServeRRooT . '/admin/include/content/reports/web_admin_log.php');
                echo report_web_admin_log();
            } elseif ($SUB==31 and $LOG['view_agent_stats']) {
                require($WeBServeRRooT . '/admin/include/content/reports/phone_stats.php');
                echo report_phone_stats();
            } elseif ($SUB==32) {
                if (file_exists($WeBServeRRooT . '/admin/include/content/reports/acct_detail.php')) {
                    require($WeBServeRRooT . '/admin/include/content/reports/acct_detail.php');
                    echo report_acct_detail();
                }
            } else {
                echo "<font color=red>You do not have permission to view this page</font>\n";
            }
        }
    }
    if (file_exists($WeBServeRRooT . '/admin/include/content/reports/custom.php')) {
        echo "<table class=shadedtable width=60% align=center><tr><td>\n";
        echo "<font face=\"dejavu sans,verdana,sans-serif\" size=2>\n";
        include($WeBServeRRooT . '/admin/include/content/reports/custom.php');
        echo "</font></td></tr></table>\n";
    }
} else {
    echo "<font color=red>You do not have permission to view this page</font>\n";
}


?>
