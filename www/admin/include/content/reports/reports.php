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
	if ($LOGview_reports==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from osdial_conferences order by conf_exten";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

	$stmt="select * from servers;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$servers_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $servers_to_print)
		{
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

	?>

	<HTML>
	<HEAD>

	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
	<TITLE>OSDial: Server Stats and Reports</TITLE></HEAD><BODY BGCOLOR=WHITE>
	<FONT SIZE=4 color=navy><br><center>SERVER STATS AND REPORTS</center></font><BR><BR>
	<UL class=>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=12"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Time On Dialer (per campaign)</a> &nbsp;  <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=11"><FONT FACE="ARIAL,HELVETICA" SIZE=2>(all campaigns SUMMARY)</a> &nbsp; &nbsp; SIP <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=12&SIPmonitorLINK=1"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Listen</a> - <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=12&SIPmonitorLINK=2"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Barge</a> &nbsp; &nbsp; IAX <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=12&IAXmonitorLINK=1"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Listen</a> - <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=12&IAXmonitorLINK=2"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Barge</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_VDADstats.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Call Report</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_CLOSERstats.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Closer Report</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_agent_performance_detail.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Agent Performance Detail</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=osdial_sales_viewer.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Agent Spreadsheet Performance</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_server_performance.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Server Performance</a></FONT>
<?
	if ($enable_queuemetrics_logging_LU > 0)
		{
		echo "<LI><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=$queuemetrics_url_LU\"><FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>QUEUEMETRICS REPORTS</a></FONT>\n";
		}
?>
	</UL>
	<PRE><table frame=box CELLPADDING=0 cellspacing=4>
	<TR>
		<TD align=center><font color=navy>&nbsp;Server&nbsp;&nbsp;</TD>
		<TD align=center><font color=navy>&nbsp;Description&nbsp;&nbsp;</TD>
		<TD align=center><font color=navy>&nbsp;IP Address&nbsp;&nbsp;</TD>
		<TD align=center><font color=navy>&nbsp;Active&nbsp;&nbsp;</TD>
		<TD align=center><font color=navy>&nbsp;Dialer Time&nbsp;&nbsp;</TD>
		<TD align=center><font color=navy>&nbsp;Park Time&nbsp;&nbsp;</TD>
		<TD align=center><font color=navy>&nbsp;Closer/Inbound Time&nbsp;</TD>
	</TR>
	<? 

		$o=0;
		while ($servers_to_print > $o)
		{
		echo "<TR>";
		echo "	<TD align=center>$server_id[$o]</TD>\n";
		echo "	<TD align=center>$server_description[$o]</TD>\n";
		echo "	<TD align=center>$server_ip[$o]</TD>\n";
		echo "	<TD align=center>$active[$o]</TD>\n";
		echo "	<TD align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonVDAD.php?server_ip=$server_ip[$o]\">LINK</a></TD>\n";
		echo "	<TD align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonpark.php?server_ip=$server_ip[$o]\">LINK</a></TD>\n";
		echo "	<TD align=center><a href=\"$PHP_SELF?ADD=999999&SUB=9&iframe=AST_timeonVDAD.php?server_ip=$server_ip[$o]&closer_display=1\">LINK</a></TD>\n";
		echo "</TR>";
		$o++;
		}

	echo "</TABLE>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
} elseif ($ADD==999999) {
    if ($SUB==11) {
        require($WeBServeRRooT . '/admin/include/content/reports/realtime_summary.php');
        echo report_realtime_summary();
    } elseif ($SUB==12) {
        require($WeBServeRRooT . '/admin/include/content/reports/realtime_detail.php');
        echo report_realtime_detail();
    }
}


?>
