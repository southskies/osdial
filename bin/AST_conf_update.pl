#!/usr/bin/perl
#
# AST_conf_update.pl version 0.4   *DBI-version*
#
# DESCRIPTION:
# uses the Asterisk Manager interface and Net::MySQL to update whether a conference
# is still in use or not. If not in use 3 times in a row the extension in the 
# conferences DB record is erased freeing that conference to be used again
#
# SUMMARY:
# This program was designed for people using the Asterisk PBX with conferences
#
# This program should be in the cron running every minute (like AST_vm_update.pl)
# 
# For this program to work you need to have the "asterisk" MySQL database 
# created with the conferences table in it, also make sure
# that the account running this program has read/write/update/delete access 
# to that database
# 
# It is recommended that you run this program on the local Asterisk machine
#
# If this script is run ever minute there is a theoretical limit of 
# 600 conferences that it can check due to the wait interval. If you have 
# more than this either change the cron when this script is run or change the 
# wait interval below
#
# Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# 50810-1532 - Added database server variable definitions lookup
# 50823-1456 - Added commandline arguments for debug at runtime
# 60717-1135 - changed to DBI by Marin Blu
# 60717-1536 - changed to use /etc/astguiclient.conf for configs
#

# constants
$DB=0;  # Debug flag, set to 0 for no debug messages per minute
$US='__';
$MT[0]='';

### begin parsing run-time options ###
if (length($ARGV[0])>1)
{
	$i=0;
	while ($#ARGV >= $i)
	{
	$args = "$args $ARGV[$i]";
	$i++;
	}

	if ($args =~ /--help/i)
	{
	print "allowed run time options:\n  [-t] = test\n  [-debug] = verbose debug messages\n\n";
	}
	else
	{
		if ($args =~ /-debug/i)
		{
		$DB=1; # Debug flag
		print "-- DEBUGGING ENABLED --\n\n";
		}
		if ($args =~ /-t/i)
		{
		$TEST=1;
		$T=1;
		}
	}
}
else
{
#	print "no command line options set\n";
}
### end parsing run-time options ###

# default path to astguiclient configuration file:
$PATHconf =		'/etc/astguiclient.conf';

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ( ($line =~ /^PATHhome/) && ($CLIhome < 1) )
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^PATHagi/) && ($CLIagi < 1) )
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ( ($line =~ /^PATHweb/) && ($CLIweb < 1) )
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ( ($line =~ /^PATHsounds/) && ($CLIsounds < 1) )
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ( ($line =~ /^PATHmonitor/) && ($CLImonitor < 1) )
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_server/) && ($CLIDB_server < 1) )
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_database/) && ($CLIDB_database < 1) )
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_user/) && ($CLIDB_user < 1) )
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_pass/) && ($CLIDB_pass < 1) )
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_port/) && ($CLIDB_port < 1) )
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

if (!$VARDB_port) {$VARDB_port='3306';}

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;
use Net::Telnet ();
	  
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT telnet_host,telnet_port,ASTmgrUSERNAME,ASTmgrSECRET,ASTmgrUSERNAMEupdate,ASTmgrUSERNAMElisten,ASTmgrUSERNAMEsend,max_vicidial_trunks,answer_transfer_agent,local_gmt,ext_context FROM servers where server_ip = '$server_ip';";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
if ($sthArows > 0)
    {
	   @aryA = $sthA->fetchrow_array;
		$DBtelnet_host	=			"$aryA[0]";
		$DBtelnet_port	=			"$aryA[1]";
		$DBASTmgrUSERNAME	=		"$aryA[2]";
		$DBASTmgrSECRET	=			"$aryA[3]";
		$DBASTmgrUSERNAMEupdate	=	"$aryA[4]";
		$DBASTmgrUSERNAMElisten	=	"$aryA[5]";
		$DBASTmgrUSERNAMEsend	=	"$aryA[6]";
		$DBmax_vicidial_trunks	=	"$aryA[7]";
		$DBanswer_transfer_agent=	"$aryA[8]";
		$DBSERVER_GMT		=		"$aryA[9]";
		$DBext_context	=			"$aryA[10]";
		if ($DBtelnet_host)				{$telnet_host = $DBtelnet_host;}
		if ($DBtelnet_port)				{$telnet_port = $DBtelnet_port;}
		if ($DBASTmgrUSERNAME)			{$ASTmgrUSERNAME = $DBASTmgrUSERNAME;}
		if ($DBASTmgrSECRET)			{$ASTmgrSECRET = $DBASTmgrSECRET;}
		if ($DBASTmgrUSERNAMEupdate)	{$ASTmgrUSERNAMEupdate = $DBASTmgrUSERNAMEupdate;}
		if ($DBASTmgrUSERNAMElisten)	{$ASTmgrUSERNAMElisten = $DBASTmgrUSERNAMElisten;}
		if ($DBASTmgrUSERNAMEsend)		{$ASTmgrUSERNAMEsend = $DBASTmgrUSERNAMEsend;}
		if ($DBmax_vicidial_trunks)		{$max_vicidial_trunks = $DBmax_vicidial_trunks;}
		if ($DBanswer_transfer_agent)	{$answer_transfer_agent = $DBanswer_transfer_agent;}
		if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
		if ($DBext_context)				{$ext_context = $DBext_context;}
	}
 $sthA->finish(); 



@PTextensions=@MT; @PT_conf_extens=@MT; @PTmessages=@MT; @PTold_messages=@MT; @NEW_messages=@MT; @OLD_messages=@MT;
$stmtA = "SELECT extension,conf_exten from conferences where server_ip='$server_ip' and extension is NOT NULL and extension != '';";
if ($DB) {print "|$stmtA|\n";}
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
    {
	   @aryA = $sthA->fetchrow_array;
		$PTextensions[$rec_count] =		 "$aryA[0]";
		$PT_conf_extens[$rec_count] =	 "$aryA[1]";
			if ($DB) {print "|$PT_conf_extens[$rec_count]|$PTextensions[$rec_count]|\n";}
		$rec_count++;
   }
   $sthA->finish(); 

if (!$telnet_port) {$telnet_port = '5038';}

### connect to asterisk manager through telnet
$t = new Net::Telnet (Port => $telnet_port,
					  Prompt => '/.*[\$%#>] $/',
					  Output_record_separator => '',);
#$fh = $t->dump_log("$telnetlog");  # uncomment for telnet log
	if (length($ASTmgrUSERNAMEsend) > 3) {$telnet_login = $ASTmgrUSERNAMEsend;}
	else {$telnet_login = $ASTmgrUSERNAME;}

$t->open("$telnet_host"); 
$t->waitfor('/0\n$/');			# print login
$t->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
$t->waitfor('/Authentication accepted/');		# waitfor auth accepted


$i=0;
foreach(@PTextensions)
	{
	@list_channels=@MT;
	$t->buffer_empty;
	$COMMAND = "Action: Command\nCommand: Meetme list $PT_conf_extens[$i]\n\nAction: Ping\n\n";
	if ($DB) {print "|$PT_conf_extens[$i]|$COMMAND|\n";}
	@list_channels = $t->cmd(String => "$COMMAND", Prompt => '/Response: Pong.*/'); 


	$j=0;
	$conf_empty[$i]=0;
	$conf_users[$i]='';
	foreach(@list_channels)
		{
		if($DB){print "|$list_channels[$j]|\n";}
		if ($list_channels[$j] =~ /No active conferences|No such conference/i)
			{$conf_empty[$i]++;}
#		if ($list_channels[$j] =~ /^User /i)
#			{
#			$userx = '';
#			$userx = $list_channels[$j];
#			$userx =~ s/User \#: //gi;
#			$conf_users[$i] .= "$userx|";
#			}
		$j++;
		}

	if($DB){print "Meetme list $PT_conf_extens[$i]-  Exten:|$PTextensions[$i]| Empty:|$conf_empty[$i]|    ";}
	if (!$conf_empty[$i])
		{
		if($DB){print "CONFERENCE STILL HAS PARTICIPANTS, DOING NOTHING FOR THIS CONFERENCE\n";}
		if ($PTextensions[$i] =~ /Xtimeout\d$/i) 
			{
			$PTextensions[$i] =~ s/Xtimeout\d$//gi;
			$stmtA = "UPDATE conferences set extension='$PTextensions[$i]' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
				if($DB){print STDERR "\n|$stmtA|\n";}
			$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			}
		}
	else
		{
		$NEWexten[$i] = $PTextensions[$i];
		if ($PTextensions[$i] =~ /Xtimeout3$/i) {$NEWexten[$i] =~ s/Xtimeout3$/Xtimeout2/gi;}
		if ($PTextensions[$i] =~ /Xtimeout2$/i) {$NEWexten[$i] =~ s/Xtimeout2$/Xtimeout1/gi;}
		if ($PTextensions[$i] =~ /Xtimeout1$/i) {$NEWexten[$i] = '';}
		if ( ($PTextensions[$i] !~ /Xtimeout\d$/i) and (length($PTextensions[$i])> 0) ) {$NEWexten[$i] .= 'Xtimeout3';}


		$stmtA = "UPDATE conferences set extension='$NEWexten[$i]' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
			if($DB){print STDERR "\n|$stmtA|\n";}
		$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
		}

	$i++;
		### sleep for 10 hundredths of a second
		usleep(1*100*1000);
	}


$t->buffer_empty;
@hangup = $t->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
$t->buffer_empty;
$ok = $t->close;

$dbhA->disconnect();

if($DB){print "DONE... Exiting... Goodbye... See you later... \n";}

exit;




