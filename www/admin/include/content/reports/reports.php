<?php



######################
# ADD=999999 display reports section
######################
if ($ADD==999999)
{
	if ($LOGview_reports==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_conferences order by conf_exten";
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
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_timeonVDADall.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Time On Dialer (per campaign)</a> &nbsp;  <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_timeonVDADallSUMMARY.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>(all campaigns SUMMARY)</a> &nbsp; &nbsp; SIP <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_timeonVDADall.php?SIPmonitorLINK=1"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Listen</a> - <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_timeonVDADall.php?SIPmonitorLINK=2"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Barge</a> &nbsp; &nbsp; IAX <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_timeonVDADall.php?IAXmonitorLINK=1"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Listen</a> - <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_timeonVDADall.php?IAXmonitorLINK=2"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Barge</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_VDADstats.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Call Report</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_CLOSERstats.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Closer Report</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=AST_agent_performance_detail.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Agent Performance Detail</a></FONT>
	<LI><a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=9&iframe=vicidial_sales_viewer.php"><FONT FACE="ARIAL,HELVETICA" SIZE=2>Agent Spreadsheet Performance</a></FONT>
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
}


?>
