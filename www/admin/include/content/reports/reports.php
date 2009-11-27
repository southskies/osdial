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
if ($ADD==999999 and $SUB=='') {
	if ($LOGview_reports==1) {
	    echo "<table align=center><tr><td>\n";
	    echo "<font face=\"arial,helvetica\" color=$default_text size=2>";

	    $stmt="SELECT * from osdial_conferences order by conf_exten";
	    $rslt=mysql_query($stmt, $link);
	    $phones_to_print = mysql_num_rows($rslt);

	    $stmt="select * from servers;";
	    $rslt=mysql_query($stmt, $link);
	    if ($DB) {echo "$stmt\n";}
	    $servers_to_print = mysql_num_rows($rslt);
	    $i=0;
	    while ($i < $servers_to_print) {
		    $row=mysql_fetch_row($rslt);
		    $server_id[$i] =			$row[0];
		    $server_description[$i] =	$row[1];
		    $server_ip[$i] =			$row[2];
		    $active[$i] =				$row[3];
		    $i++;
		}

	    $stmt="SELECT enable_queuemetrics_logging,queuemetrics_url from system_settings;";
	    $rslt=mysql_query($stmt, $link);
	    $row=mysql_fetch_row($rslt);
	    $enable_queuemetrics_logging_LU =	$row[0];
	    $queuemetrics_url_LU =				$row[1];


	    echo "<html>";
	    echo "<head>";

	    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
	    echo "<title>$t1: Server Stats and Reports</title></head><body bgcolor=white>";
	    echo "<font size=4 color=$default_text><br><center>SERVER STATS AND REPORTS</center></font><br><br>";
	    echo "<ul class=>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=12\"><font face=\"arial,helvetica\" size=2>Time On Dialer (per campaign)</a> &nbsp;  <a href=\"$PHP_SELF?ADD=999999&SUB=11\"><font face=\"arial,helvetica\" size=2>(all campaigns SUMMARY)</a> &nbsp; &nbsp; SIP <a href=\"$PHP_SELF?ADD=999999&SUB=12&SIPmonitorLINK=1\"><font face=\"arial,helvetica\" size=2>Listen</a> - <a href=\"$PHP_SELF?ADD=999999&SUB=12&SIPmonitorLINK=2\"><font face=\"arial,helvetica\" size=2>Barge</a> &nbsp; &nbsp; IAX <a href=\"$PHP_SELF?ADD=999999&SUB=12&IAXmonitorLINK=1\"><font face=\"arial,helvetica\" size=2>Listen</a> - <a href=\"$PHP_SELF?ADD=999999&SUB=12&IAXmonitorLINK=2\"><font face=\"arial,helvetica\" size=2>Barge</a></font>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=15\"><font face=\"arial,helvetica\" size=2>Call Report</a></font>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_CLOSERstats.php\"><font face=\"arial,helvetica\" size=2>Inbound / Closer Report</a></font>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=19\"><font face=\"arial,helvetica\" size=2>Agent Performance Detail</a></font>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=osdial_sales_viewer.php\"><font face=\"arial,helvetica\" size=2>Agent Spreadsheet Performance</a></font>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=16\"><font face=\"arial,helvetica\" size=2>List Cost by Entry Date</a></font>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=17\"><font face=\"arial,helvetica\" size=2>Lead Performance and Analysis by Campaign</a></font>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=18\"><font face=\"arial,helvetica\" size=2>Lead Performance and Analysis by List</a></font>";
	    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_server_performance.php\"><font face=\"arial,helvetica\" size=2>Server Performance</a></font>";

	    if ($enable_queuemetrics_logging_LU > 0) {
		    echo "<li><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=$queuemetrics_url_LU\"><font face=\"arial,helvetica\" size=2>QUEUEMETRICS REPORTS</a></font>\n";
	    }

        echo "	</ul>";
        echo "	<pre><table frame=box cellpadding=0 cellspacing=4>";
        echo "	<tr>";
        echo "		<td align=center><font color=$default_text>&nbsp;Server&nbsp;&nbsp;</td>";
        echo "		<td align=center><font color=$default_text>&nbsp;Description&nbsp;&nbsp;</td>";
        echo "		<td align=center><font color=$default_text>&nbsp;IP Address&nbsp;&nbsp;</td>";
        echo "		<td align=center><font color=$default_text>&nbsp;Active&nbsp;&nbsp;</td>";
        echo "		<td align=center><font color=$default_text>&nbsp;Dialer Time&nbsp;&nbsp;</td>";
        echo "		<td align=center><font color=$default_text>&nbsp;Park Time&nbsp;&nbsp;</td>";
        echo "		<td align=center><font color=$default_text>&nbsp;Closer/Inbound Time&nbsp;</td>";
        echo "	</tr>";

		$o=0;
		while ($servers_to_print > $o) {
		    echo "<tr>";
		    echo "	<td align=center>$server_id[$o]</td>\n";
		    echo "	<td align=center>$server_description[$o]</td>\n";
		    echo "	<td align=center>$server_ip[$o]</td>\n";
		    echo "	<td align=center>$active[$o]</td>\n";
		    echo "	<td align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonVDAD.php?server_ip=$server_ip[$o]\">LINK</a></td>\n";
		    echo "	<td align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonpark.php?server_ip=$server_ip[$o]\">LINK</a></td>\n";
		    echo "	<td align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonVDAD.php?server_ip=$server_ip[$o]&closer_display=1\">LINK</a></td>\n";
		    echo "</tr>";
		    $o++;
		}

	    echo "</table>\n";
	} else {
	    echo "<font color=red>You do not have permission to view this page</font>\n";
	}

} elseif ($ADD==999999) {
    if ($SUB==11) {
        require($WeBServeRRooT . '/admin/include/content/reports/realtime_summary.php');
        echo report_realtime_summary();
    } elseif ($SUB==12) {
        require($WeBServeRRooT . '/admin/include/content/reports/realtime_detail.php');
        echo report_realtime_detail();
    } elseif ($SUB==15) {
        require($WeBServeRRooT . '/admin/include/content/reports/call_stats.php');
        echo report_call_stats();
    } elseif ($SUB==16) {
        require($WeBServeRRooT . '/admin/include/content/reports/list_cost.php');
        echo report_list_cost();
    } elseif ($SUB==17) {
        require($WeBServeRRooT . '/admin/include/content/reports/lead_performance_campaign.php');
        echo report_lead_performance_campaign();
    } elseif ($SUB==18) {
        require($WeBServeRRooT . '/admin/include/content/reports/lead_performance_list.php');
        echo report_lead_performance_list();
    } elseif ($SUB==19) {
        require($WeBServeRRooT . '/admin/include/content/reports/agent_performance_detail.php');
        echo report_agent_performance_detail();
    }
}


?>
