#!/usr/bin/perl
#
# FastAGI_log.pl version 2.0.4   *DBI-version*
#
## Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
## Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
##
##     This file is part of OSDial.
##
##     OSDial is free software: you can redistribute it and/or modify
##     it under the terms of the GNU Affero General Public License as
##     published by the Free Software Foundation, either version 3 of
##     the License, or (at your option) any later version.
##
##     OSDial is distributed in the hope that it will be useful,
##     but WITHOUT ANY WARRANTY; without even the implied warranty of
##     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
##     GNU Affero General Public License for more details.
##
##     You should have received a copy of the GNU Affero General Public
##     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
##
#
# 
# Experimental Deamon using perl Net::Server that runs as FastAGI to reduce load
# replaces the following AGI scripts:
# - call_log.agi
# - call_logCID.agi
# - VD_hangup.agi
#
# This script needs to be running all of the time for AGI requests to work
# 
# You need to put lines similar to those below in your extensions.conf file:
# 
# ;outbound dialing:
# exten => _91NXXNXXXXXX,1,AGI(agi://127.0.0.1:4577/call_log) 
#
# ;inbound calls:
# exten => 101,1,AGI(agi://127.0.0.1:4577/call_log)
#   or
# exten => 101,1,AGI(agi://127.0.0.1:4577/call_log--fullCID--${EXTEN}-----${CALLERID}-----${CALLERIDNUM}-----${CALLERIDNAME})
#
# 
# ;all hangups:
# exten => h,1,DeadAGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----${HANGUPCAUSE}-----${DIALSTATUS}-----${DIALEDTIME}-----${ANSWEREDTIME})
# 
#
# CHANGELOG:
# 61010-1007 - First test build
# 70116-1619 - Added Auto Alt Dial code
# 70215-1258 - Added queue_log entry when deleting vac record
# 70808-1425 - Moved VD_hangup section to the call_log end stage to improve efficiency
# 71030-2039 - Added priority to hopper insertions
# 80303-1438 - Fixed problem with false hangupcause data
#


# defaults for PreFork
$VARfastagi_log_min_servers =	'3';
$VARfastagi_log_max_servers =	'16';
$VARfastagi_log_min_spare_servers = '2';
$VARfastagi_log_max_spare_servers = '8';
$VARfastagi_log_max_requests =	'1000';
$VARfastagi_log_checkfordead =	'30';
$VARfastagi_log_checkforwait =	'60';

# default path to osdial.configuration file:
$PATHconf =		'/etc/osdial.conf';

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_min_servers/) && ($CLIVARfastagi_log_min_servers < 1) )
		{$VARfastagi_log_min_servers = $line;   $VARfastagi_log_min_servers =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_max_servers/) && ($CLIVARfastagi_log_max_servers < 1) )
		{$VARfastagi_log_max_servers = $line;   $VARfastagi_log_max_servers =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_min_spare_servers/) && ($CLIVARfastagi_log_min_spare_servers < 1) )
		{$VARfastagi_log_min_spare_servers = $line;   $VARfastagi_log_min_spare_servers =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_max_spare_servers/) && ($CLIVARfastagi_log_max_spare_servers < 1) )
		{$VARfastagi_log_max_spare_servers = $line;   $VARfastagi_log_max_spare_servers =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_max_requests/) && ($CLIVARfastagi_log_max_requests < 1) )
		{$VARfastagi_log_max_requests = $line;   $VARfastagi_log_max_requests =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_checkfordead/) && ($CLIVARfastagi_log_checkfordead < 1) )
		{$VARfastagi_log_checkfordead = $line;   $VARfastagi_log_checkfordead =~ s/.*=//gi;}
	if ( ($line =~ /^VARfastagi_log_checkforwait/) && ($CLIVARfastagi_log_checkforwait < 1) )
		{$VARfastagi_log_checkforwait = $line;   $VARfastagi_log_checkforwait =~ s/.*=//gi;}
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

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$Fhour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}

if (!$VARDB_port) {$VARDB_port='3306';}

$SERVERLOG = 'N';
$log_level = '0';

use DBI;
$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
	or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtB = "SELECT vd_server_logs FROM servers where server_ip = '$VARserver_ip';";
$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
$sthBrows=$sthB->rows;
$rec_count=0;
while ($sthBrows > $rec_count)
	{
	 @aryB = $sthB->fetchrow_array;
		$SERVERLOG =	"$aryB[0]";
	 $rec_count++;
	}
$sthB->finish();
$dbhB->disconnect();

if ($SERVERLOG =~ /Y/) 
	{
	$childLOGfile = "$PATHlogs/FastAGIchildLOG.$year-$mon-$mday";
	$log_level = "4";
	print "SERVER LOGGING ON: LEVEL-$log_level FILE-$childLOGfile\n";
	}

package TEST_VDfastAGI;

use Net::Server;
use Asterisk::AGI;
use vars qw(@ISA);
use Net::Server::PreFork; # any personality will do
@ISA = qw(Net::Server::PreFork);




sub process_request {
	$process = 'begin';
	$script = 'TEST_VDfastAGI';
	########## Get current time, parse configs, get logging preferences ##########
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
	$year = ($year + 1900);
	$mon++;
	if ($mon < 10) {$mon = "0$mon";}
	if ($mday < 10) {$mday = "0$mday";}
	if ($hour < 10) {$Fhour = "0$hour";}
	if ($min < 10) {$min = "0$min";}
	if ($sec < 10) {$sec = "0$sec";}

	$now_date_epoch = time();
	$now_date = "$year-$mon-$mday $hour:$min:$sec";


	# default path to osdial.configuration file:
	$PATHconf =		'/etc/osdial.conf';

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

	if (!$VARDB_port) {$VARDB_port='3306';}
	if (!$AGILOGfile) {$AGILOGfile = "$PATHlogs/FASTagiout.$year-$mon-$mday";}

	$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
		or die "Couldn't connect to database: " . DBI->errstr;

	### Grab Server values from the database
	$stmtA = "SELECT agi_output FROM servers where server_ip = '$VARserver_ip';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		$AGILOG = '0';
		 @aryA = $sthA->fetchrow_array;
			$DBagi_output =			"$aryA[0]";
			if ($DBagi_output =~ /STDERR/)	{$AGILOG = '1';}
			if ($DBagi_output =~ /FILE/)	{$AGILOG = '2';}
			if ($DBagi_output =~ /BOTH/)	{$AGILOG = '3';}
		 $rec_count++;
		}
	$sthA->finish();




	if ($AGILOG) 
		{
		$agi_string = "+++++++++++++++++ FastAGI Start ++++++++++++++++++++++++++++++++++++++++"; 
		&agi_output;
		}



	### begin parsing run-time options ###
	if (length($ARGV[0])>1)
	{
		if ($AGILOG) 
			{
			$agi_string = "Perl Environment Dump:"; 
			&agi_output;
			}
		$i=0;
		while ($#ARGV >= $i)
		{
			$args = "$args $ARGV[$i]";
			if ($AGILOG) 
				{
				$agi_string = "$i|$ARGV[$i]";   
				&agi_output;
				}
			$i++;
		}
	}
	$HVcauses=0;
	$fullCID=0;
	$callerid='';
	$calleridname='';
	$|=1;
	while(<STDIN>) {
		chomp;
		last unless length($_);
		if ($AGILOG)
		{
			if (/^agi_(\w+)\:\s+(.*)$/)
			{
				$AGI{$1} = $2;
			}
		}

		if (/^agi_uniqueid\:\s+(.*)$/)		{$unique_id = $1; $uniqueid = $unique_id;}
		if (/^agi_priority\:\s+(.*)$/)		{$priority = $1;}
		if (/^agi_channel\:\s+(.*)$/)		{$channel = $1;}
		if (/^agi_extension\:\s+(.*)$/)		{$extension = $1;}
		if (/^agi_type\:\s+(.*)$/)			{$type = $1;}
		if (/^agi_request\:\s+(.*)$/)		{$request = $1;}
		if ( ($request =~ /--fullCID--/i) && (!$fullCID) )
			{
			$fullCID=1;
			@CID = split(/-----/, $request);
			$callerid =	$CID[2];
			$calleridname =	$CID[3];
			$agi_string = "URL fullCID: |$callerid|$calleridname|$request|";   
			&agi_output;
			}
		if ( ($request =~ /--HVcauses--/i) && (!$HVcauses) )
			{
			$HVcauses=1;
			@ARGV_vars = split(/-----/, $request);
			$PRI = $ARGV_vars[0];
			$PRI =~ s/.*--HVcauses--//gi;
			$DEBUG = $ARGV_vars[1];
			$hangup_cause = $ARGV_vars[2];
			$dialstatus = $ARGV_vars[3];
			$dial_time = $ARGV_vars[4];
			$ring_time = $ARGV_vars[5];
			$agi_string = "URL HVcauses: |$PRI|$DEBUG|$hangup_cause|$dialstatus|$dial_time|$ring_time|";   
			&agi_output;
			}
		if (!$fullCID)	# if no fullCID sent
			{
			if (/^agi_callerid\:\s+(.*)$/)		{$callerid = $1;}
			if (/^agi_calleridname\:\s+(.*)$/)	{$calleridname = $1;}
			if ( $calleridname =~ /\"/)  {$calleridname =~ s/\"//gi;}
	#	if ( (length($calleridname)>5) && ( (!$callerid) or ($callerid =~ /unknown|private|00000000/i) or ($callerid =~ /5551212/) ) )
		if ( ( 
		(length($calleridname)>5) && ( (!$callerid) or ($callerid =~ /unknown|private|00000000/i) or ($callerid =~ /5551212/) )
		) or ( (length($calleridname)>17) && ($calleridname =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) ) )
			{$callerid = $calleridname;}

			### allow for ANI being sent with the DNIS "*3125551212*9999*"
			if ($extension =~ /^\*\d\d\d\d\d\d\d\d\d\d\*/)
				{
				$callerid = $extension;
				$callerid =~ s/\*\d\d\d\d\*$//gi;
				$callerid =~ s/^\*//gi;
				$extension =~ s/^\*\d\d\d\d\d\d\d\d\d\d\*//gi;
				$extension =~ s/\*$//gi;
				}
			$calleridname = $callerid;
			}
	}

	if ($AGILOG) 
		{
		$agi_string = "AGI Environment Dump:";   
		&agi_output;
		}

	foreach $i (sort keys %AGI) 
	{
		if ($AGILOG) 
			{
			$agi_string = " -- $i = $AGI{$i}";   
			&agi_output;
			}
	}


	if ($AGILOG) 
		{
		$agi_string = "AGI Variables: |$unique_id|$channel|$extension|$type|$callerid|";   
		&agi_output;
		}

	if ( ($extension =~ /h/i) && (length($extension) < 3))  {$stage = 'END';}
	else {$stage = 'START';}

	$process = $request;
	$process =~ s/agi:\/\///gi;
	$process =~ s/.*\/|--.*//gi;
	if ($AGILOG) 
		{
		$agi_string = "Process to run: |$request|$process|$stage|";   
		&agi_output;
		}


	###################################################################
	##### START call_log process ######################################
	###################################################################
	if ($process =~ /^call_log/)
		{
		### call start stage
		if ($stage =~ /START/)
			{
			if ($AGILOG) {$agi_string = "+++++ CALL LOG START : $now_date";   &agi_output;}

			if ($channel =~ /^SIP/) {$channel =~ s/-.*//gi;}
			if ($channel =~ /^IAX2/) {$channel =~ s/\/\d+$//gi;}
			if ($channel =~ /^Zap\/|^Local\//)
				{
				$channel_line = $channel;
				$channel_line =~ s/^Zap\///gi;

				$stmtA = "SELECT count(*) FROM phones where server_ip='$VARserver_ip' and extension='$channel_line' and protocol='Zap';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone	 = "$aryA[0]";
				$sthA->finish();

				if ($is_client_phone < 1)
					{
					$channel_group = 'Zap Trunk Line';
					$number_dialed = $callerid;
					}
				else
					{
					$channel_group = 'Zap Client Phone';
					}
				if ($AGILOG) {$agi_string = "Local Zap phone: $aryA[0]|$channel_line|";   &agi_output;}
				}
			### This section breaks the outbound dialed number down(or builds it up) to a 10 digit number and gives it a description
			if ( ($channel =~ /^SIP|^IAX2/) or ($is_client_phone > 0) )
				{
				if ( ($extension =~ /^901144/) && (length($extension)==16) )  #test 207 608 6400 
					{$extension =~ s/^9//gi;	$channel_group = 'Outbound Intl UK';}
				if ( ($extension =~ /^901161/) && (length($extension)==15) )  #test  39 417 2011
					{$extension =~ s/^9//gi;	$channel_group = 'Outbound Intl AUS';}
				if ( ($extension =~ /^91800|^91888|^91877|^91866/) && (length($extension)==12) )
					{$extension =~ s/^91//gi;	$channel_group = 'Outbound Local 800';}
				if ( ($extension =~ /^9/) && (length($extension)==8) )
					{$extension =~ s/^9/727/gi;	$channel_group = 'Outbound Local';}
				if ( ($extension =~ /^9/) && (length($extension)==11) )
					{$extension =~ s/^9//gi;	$channel_group = 'Outbound Local';}
				if ( ($extension =~ /^91/) && (length($extension)==12) )
					{$extension =~ s/^91//gi;	$channel_group = 'Outbound Long Distance';}
				if ($is_client_phone > 0)
					{$channel_group = 'Zap Client Phone';}
				
				$SIP_ext = $channel;	$SIP_ext =~ s/SIP\/|IAX2\/|Zap\///gi;

				$number_dialed = $extension;
				$extension = $SIP_ext;
				}

			if ( ($callerid =~ /^V|^M/) && ($callerid =~ /\d\d\d\d\d\d\d\d\d/) && (length($number_dialed)<1) )
				{
				$stmtA = "SELECT cmd_line_b,cmd_line_d FROM osdial_manager where callerid='$callerid' limit 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$cmd_line_b	=	"$aryA[0]";
					$cmd_line_d	=	"$aryA[1]";
						$cmd_line_b =~ s/Exten: //gi;
						$cmd_line_d =~ s/Channel: Local\/|@.*//gi;
					$rec_count++;
					}
				$sthA->finish();
				if ($callerid =~ /^V/) {$number_dialed = "$cmd_line_d";}
				if ($callerid =~ /^M/) {$number_dialed = "$cmd_line_b";}
				$number_dialed =~ s/\D//gi;
				if (length($number_dialed)<1) {$number_dialed=$extension;}
				}
			$stmtA = "INSERT INTO call_log (uniqueid,channel,channel_group,type,server_ip,extension,number_dialed,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,caller_code) values('$unique_id','$channel','$channel_group','$type','$VARserver_ip','$extension','$number_dialed','$now_date','$now_date_epoch','','','','','$callerid')";

			if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
			$affected_rows = $dbhA->do($stmtA);

			$dbhA->disconnect();
			}


		### call end stage
		else		 
			{
			if ($AGILOG) {$agi_string = "|CALL HUNG UP|";   &agi_output;}
			if ($request =~ /--HVcauses--/i)
				{
				$HVcauses=1;
				@ARGV_vars = split(/-----/, $request);
				$PRI = $ARGV_vars[0];
				$PRI =~ s/.*--HVcauses--//gi;
				$DEBUG = $ARGV_vars[1];
				$hangup_cause = $ARGV_vars[2];
				$dialstatus = $ARGV_vars[3];
				$dial_time = $ARGV_vars[4];
				$ring_time = $ARGV_vars[5];
				$agi_string = "URL HVcauses: |$PRI|$DEBUG|$hangup_cause|$dialstatus|$dial_time|$ring_time|";   
				&agi_output;
				}

			### get uniqueid and start_epoch from the call_log table
			$stmtA = "SELECT uniqueid,start_epoch FROM call_log where uniqueid='$unique_id';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$start_time	=			"$aryA[1]";
				if ($AGILOG) {$agi_string = "|$aryA[0]|$aryA[1]|";   &agi_output;}
				$rec_count++;
				}
			$sthA->finish();

			if ($rec_count)
				{
				$length_in_sec = ($now_date_epoch - $start_time);
				$length_in_min = ($length_in_sec / 60);
				$length_in_min = sprintf("%8.2f", $length_in_min);

				if ($AGILOG) {$agi_string = "QUERY done: start time = $start_time | sec: $length_in_sec | min: $length_in_min |";   &agi_output;}

				$stmtA = "UPDATE call_log set end_time='$now_date',end_epoch='$now_date_epoch',length_in_sec=$length_in_sec,length_in_min='$length_in_min',channel='$channel' where uniqueid='$unique_id'";

				if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
				$affected_rows = $dbhA->do($stmtA);
				}

			$stmtA = "DELETE from live_inbound where uniqueid='$unique_id' and server_ip='$VARserver_ip'";
			if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
			$affected_rows = $dbhA->do($stmtA);

		##### BEGIN Park Log entry check and update #####
			$stmtA = "SELECT UNIX_TIMESTAMP(parked_time),UNIX_TIMESTAMP(grab_time) FROM park_log where uniqueid='$unique_id' and server_ip='$VARserver_ip' LIMIT 1;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$parked_time	=			"$aryA[0]";
				$grab_time	=			"$aryA[1]";
				if ($AGILOG) {$agi_string = "|$aryA[0]|$aryA[1]|";   &agi_output;}
				$rec_count++;
				}
			$sthA->finish();

			  if ($rec_count)
			  {
			if ($AGILOG) {$agi_string = "*****Entry found for $unique_id-$VARserver_ip in park_log: $parked_time|$grab_time";   &agi_output;}
				 if ($parked_time > $grab_time)
				 {
				$parked_sec=($now_date_epoch - $parked_time);
				$talked_sec=0;
				 }
				 else
				 {
				$talked_sec=($now_date_epoch - $parked_time);
				$parked_sec=($grab_time - $parked_time);
				 }

				$stmtA = "UPDATE park_log set status='HUNGUP',hangup_time='$now_date',parked_sec='$parked_sec',talked_sec='$talked_sec' where uniqueid='$unique_id' and server_ip='$VARserver_ip'";
				$affected_rows = $dbhA->do($stmtA);
			   }
		##### END Park Log entry check and update #####

		#	$dbhA->disconnect();

			if ($AGILOG) {$agi_string = "+++++ CALL LOG HUNGUP: |$unique_id|$channel|$extension|$now_date|min: $length_in_min|";   &agi_output;}


		##### BEGIN former VD_hangup section functions #####

			$VDADcampaign='';
			$VDADphone='';
			$VDADphone_code='';

			if ($DEBUG =~ /^DEBUG$/)
			{
				### open the hangup cause out file for writing ###
				open(out, ">>$PATHlogs/HANGUP_cause-output.txt")
						|| die "Can't open $PATHlogs/HANGUP_cause-output.txt: $!\n";

				print out "$now_date|$hangup_cause|$dialstatus|$dial_time|$ring_time|$unique_id|$channel|$extension|$type|$callerid|$calleridname|$priority|\n";

				close(out);
			}
			else 
			{
			if ($AGILOG) {$agi_string = "DEBUG: $DEBUG";   &agi_output;}
			}


			$callerid =~ s/\"//gi;
			$CIDlead_id = $callerid;
			$CIDlead_id = substr($CIDlead_id, 11, 9);
			$CIDlead_id = ($CIDlead_id + 0);

			if ($AGILOG) {$agi_string = "VD_hangup : $callerid $channel $priority $CIDlead_id";   &agi_output;}

			if ($channel =~ /^Local/)
			{
				if ( ($PRI =~ /^PRI$/) && ($callerid =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) && ( ($dialstatus =~ /BUSY/) || ( ($dialstatus =~ /CHANUNAVAIL/) && ($hangup_cause =~ /^1$|^28$/) ) ) )
				{
					if ($dialstatus =~ /BUSY/) {$VDL_status = 'B'; $VDAC_status = 'BUSY';}
					if ($dialstatus =~ /CHANUNAVAIL/) {$VDL_status = 'DC'; $VDAC_status = 'DISCONNECT';}

					$stmtA = "UPDATE osdial_auto_calls set status='$VDAC_status' where callerid = '$callerid';";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$affected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--    VDAC update: |$affected_rows|$CIDlead_id";   &agi_output;}

					$stmtA = "UPDATE osdial_list set status='$VDL_status' where lead_id = '$CIDlead_id';";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$affected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--    VDAD osdial_list update: |$affected_rows|$CIDlead_id";   &agi_output;}

					$stmtA = "UPDATE osdial_log set status='$VDL_status' where uniqueid = '$uniqueid';";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$affected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--    VDAD osdial_log update: |$affected_rows|$uniqueid|";   &agi_output;}

					$dbhA->disconnect();
				}
				else
				{
					if ($AGILOG) {$agi_string = "--    VD_hangup Local DEBUG: |$PRI|$callerid|$dialstatus|$hangup_cause|";   &agi_output;}
				}

				if ($AGILOG) {$agi_string = "+++++ VDAD START LOCAL CHANNEL: EXITING- $priority";   &agi_output;}
				if ($priority > 2) {sleep(1);}
			}
			else
			{

				########## FIND AND DELETE osdial_auto_calls ##########
				$VD_alt_dial = 'NONE';
				$stmtA = "SELECT lead_id,callerid,campaign_id,alt_dial,stage,UNIX_TIMESTAMP(call_time) FROM osdial_auto_calls where uniqueid = '$uniqueid' limit 1;";
					if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				 $rec_countCUSTDATA=0;
				while ($sthArows > $rec_countCUSTDATA)
					{
					@aryA = $sthA->fetchrow_array;
					$VD_lead_id	=		"$aryA[0]";
					$VD_callerid	=	"$aryA[1]";
					$VD_campaign_id	=	"$aryA[2]";
					$VD_alt_dial	=	"$aryA[3]";
					$VD_stage =			"$aryA[4]";
					$VD_start_epoch =	"$aryA[5]";
					 $rec_countCUSTDATA++;
					}
				$sthA->finish();

				if (!$rec_countCUSTDATA)
					{
					if ($AGILOG) {$agi_string = "VD hangup: no VDAC record found: $uniqueid $calleridname";   &agi_output;}
					}
				else
					{
					$stmtA = "DELETE FROM osdial_auto_calls where uniqueid='$uniqueid' order by call_time desc limit 1;";
					$affected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--    VDAC record deleted: |$affected_rows|   |$VD_lead_id|$uniqueid|$VD_callerid|$VARserver_ip";   &agi_output;}

					#############################################
					##### START QUEUEMETRICS LOGGING LOOKUP #####
					$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						 @aryA = $sthA->fetchrow_array;
							$enable_queuemetrics_logging =	"$aryA[0]";
							$queuemetrics_server_ip	=		"$aryA[1]";
							$queuemetrics_dbname =			"$aryA[2]";
							$queuemetrics_login=			"$aryA[3]";
							$queuemetrics_pass =			"$aryA[4]";
							$queuemetrics_log_id =			"$aryA[5]";
						 $rec_count++;
						}
					$sthA->finish();
					##### END QUEUEMETRICS LOGGING LOOKUP #####
					###########################################
					if ($enable_queuemetrics_logging > 0)
						{
						$VD_agent='NONE';
						$secX = time();
						$VD_call_length = ($secX - $VD_start_epoch);
						$VD_stage =~ s/.*-//gi;
						if ($VD_stage < 0.25) {$VD_stage=0;}

						$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
						 or die "Couldn't connect to database: " . DBI->errstr;

						if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

						$stmtB = "SELECT agent from queue_log where call_id='$VD_callerid' and verb='CONNECT';";
						$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
						$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
						$sthBrows=$sthB->rows;
						$rec_count=0;
						while ($sthBrows > $rec_count)
							{
							@aryB = $sthB->fetchrow_array;
							$VD_agent =	"$aryB[0]";
							$rec_count++;
							}
						$sthB->finish();

						if ($rec_count < 1)
							{
							$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$VD_callerid',queue='$VD_campaign_id',agent='$VD_agent',verb='ABANDON',data1='1',data2='1',data3='$VD_stage',serverid='$queuemetrics_log_id';";
							$Baffected_rows = $dbhB->do($stmtB);
							}
						else
							{
							$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$VD_callerid',queue='$VD_campaign_id',agent='$VD_agent',verb='COMPLETECALLER',data1='$VD_stage',data2='$VD_call_length',data3='1',serverid='$queuemetrics_log_id';";
							$Baffected_rows = $dbhB->do($stmtB);
							}

						$dbhB->disconnect();
						}


					########## FIND AND UPDATE osdial_log ##########
					$stmtA = "SELECT start_epoch,status FROM osdial_log where uniqueid='$uniqueid' and lead_id='$VD_lead_id' limit 1;";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					 $epc_countCUSTDATA=0;
					 $VD_closecallid='';
					while ($sthArows > $epc_countCUSTDATA)
						{
						@aryA = $sthA->fetchrow_array;
						$VD_start_epoch	= "$aryA[0]";
						$VD_status	= "$aryA[1]";
						 $epc_countCUSTDATA++;
						}
					$sthA->finish();

					if (!$epc_countCUSTDATA)
						{
						if ($AGILOG) {$agi_string = "no VDL record found: $uniqueid $calleridname $VD_lead_id $uniqueid";   &agi_output;}

						$secX = time();
						$Rtarget = ($secX - 21600);	# look for VDCL entry within last 6 hours
						($Rsec,$Rmin,$Rhour,$Rmday,$Rmon,$Ryear,$Rwday,$Ryday,$Risdst) = localtime($Rtarget);
						$Ryear = ($Ryear + 1900);
						$Rmon++;
						if ($Rmon < 10) {$Rmon = "0$Rmon";}
						if ($Rmday < 10) {$Rmday = "0$Rmday";}
						if ($Rhour < 10) {$Rhour = "0$Rhour";}
						if ($Rmin < 10) {$Rmin = "0$Rmin";}
						if ($Rsec < 10) {$Rsec = "0$Rsec";}
							$RSQLdate = "$Ryear-$Rmon-$Rmday $Rhour:$Rmin:$Rsec";

						$stmtA = "SELECT start_epoch,status,closecallid FROM osdial_closer_log where lead_id = '$VD_lead_id' and call_date > \"$RSQLdate\" order by call_date desc limit 1;";
							if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						 $epc_countCUSTDATA=0;
						 $VD_closecallid='';
						while ($sthArows > $epc_countCUSTDATA)
							{
							@aryA = $sthA->fetchrow_array;
							$VD_start_epoch	= "$aryA[0]";
							$VD_status	= "$aryA[1]";
							$VD_closecallid	= "$aryA[2]";
							 $epc_countCUSTDATA++;
							}
						$sthA->finish();
						}
					if (!$epc_countCUSTDATA)
						{
						if ($AGILOG) {$agi_string = "no VDL or VDCL record found: $uniqueid $calleridname $VD_lead_id $uniqueid";   &agi_output;}
						}
					else
						{
						$VD_seconds = ($now_date_epoch - $VD_start_epoch);

						$SQL_status='';
						if ($VD_status =~ /^NA$|^NEW$|^QUEUE$|^XFER$/) 
							{
							$SQL_status = "status='DROP',";

							########## FIND AND UPDATE osdial_list ##########
							$stmtA = "UPDATE osdial_list set status='DROP' where lead_id = '$VD_lead_id';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$affected_rows = $dbhA->do($stmtA);
							if ($AGILOG) {$agi_string = "--    VDAD osdial_list update: |$affected_rows|$VD_lead_id";   &agi_output;}
							}

						$stmtA = "UPDATE osdial_log set $SQL_status end_epoch='$now_date_epoch',length_in_sec='$VD_seconds' where uniqueid = '$uniqueid';";
							if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
						$affected_rows = $dbhA->do($stmtA);
						if ($AGILOG) {$agi_string = "--    VDAD osdial_log update: |$affected_rows|$uniqueid|$VD_status|";   &agi_output;}




						########## UPDATE osdial_closer_log ##########
						if (length($VD_closecallid) < 1)
							{
							if ($AGILOG) {$agi_string = "no VDCL record found: $uniqueid $calleridname $VD_lead_id $uniqueid";   &agi_output;}
							}
						else
							{
							if ($VD_status =~ /^DONE$|^INCALL$|^XFER$/) 
								{$VDCLSQL_status = "";}
							else
								{$VDCLSQL_status = "status='DROP',queue_seconds='$VD_seconds',";}

							$VD_seconds = ($now_date_epoch - $VD_start_epoch);
							$stmtA = "UPDATE osdial_closer_log set $VDCLSQL_status end_epoch='$now_date_epoch',length_in_sec='$VD_seconds' where closecallid = '$VD_closecallid';";
								if ($AGILOG) {$agi_string = "|$VDCLSQL_status|$VD_status|\n|$stmtA|";   &agi_output;}
							$affected_rows = $dbhA->do($stmtA);
							if ($AGILOG) {$agi_string = "--    VDCL update: |$affected_rows|$uniqueid|$VD_closecallid|";   &agi_output;}

							}
						}

					##### BEGIN AUTO ALT PHONE DIAL SECTION #####
					### check to see if campaign has alt_dial enabled
					$VD_auto_alt_dial = 'NONE';
					$VD_auto_alt_dial_statuses='';
					$stmtA="SELECT auto_alt_dial,auto_alt_dial_statuses FROM osdial_campaigns where campaign_id='$VD_campaign_id';";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					 $epc_countCAMPDATA=0;
					while ($sthArows > $epc_countCAMPDATA)
						{
						@aryA = $sthA->fetchrow_array;
						$VD_auto_alt_dial	=			"$aryA[0]";
						$VD_auto_alt_dial_statuses	=	"$aryA[1]";
						 $epc_countCAMPDATA++;
						}
					$sthA->finish();
					if ($VD_auto_alt_dial_statuses =~ / $VD_status | $VDL_status /)
						{
						if ( ($VD_auto_alt_dial =~ /(ALT_ONLY|ALT_AND_ADDR3)/) && ($VD_alt_dial =~ /NONE|MAIN/) )
							{
							$VD_alt_phone='';
							$stmtA="SELECT alt_phone,gmt_offset_now,state,list_id FROM osdial_list where lead_id='$VD_lead_id';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							 $epc_countCAMPDATA=0;
							while ($sthArows > $epc_countCAMPDATA)
								{
								@aryA = $sthA->fetchrow_array;
								$VD_alt_phone =			"$aryA[0]";
								$VD_alt_phone =~ s/\D//gi;
								$VD_gmt_offset_now =	"$aryA[1]";
								$VD_state =				"$aryA[2]";
								$VD_list_id =			"$aryA[3]";
								 $epc_countCAMPDATA++;
								}
							$sthA->finish();
							if (length($VD_alt_phone)>5)
								{
								$stmtA = "INSERT INTO osdial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ALT',user='',priority='25';";
								$affected_rows = $dbhA->do($stmtA);
								if ($AGILOG) {$agi_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
								}
							}
						if ( ( ($VD_auto_alt_dial =~ /(ADDR3_ONLY)/) && ($VD_alt_dial =~ /NONE|MAIN/) ) || ( ($VD_auto_alt_dial =~ /(ALT_AND_ADDR3)/) && ($VD_alt_dial =~ /ALT/) ) )
							{
							$VD_address3='';
							$stmtA="SELECT address3,gmt_offset_now,state,list_id FROM osdial_list where lead_id='$VD_lead_id';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							 $epc_countCAMPDATA=0;
							while ($sthArows > $epc_countCAMPDATA)
								{
								@aryA = $sthA->fetchrow_array;
								$VD_address3 =			"$aryA[0]";
								$VD_address3 =~ s/\D//gi;
								$VD_gmt_offset_now =	"$aryA[1]";
								$VD_state =				"$aryA[2]";
								$VD_list_id =			"$aryA[3]";
								 $epc_countCAMPDATA++;
								}
							$sthA->finish();
							if (length($VD_address3)>5)
								{
								$stmtA = "INSERT INTO osdial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ADDR3',user='',priority='20';";
								$affected_rows = $dbhA->do($stmtA);
								if ($AGILOG) {$agi_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
								}
							}
						}
					##### END AUTO ALT PHONE DIAL SECTION #####
					}

				}

			$dbhA->disconnect();

			}
		}
	###################################################################
	##### END call_log process ########################################
	###################################################################





	###################################################################
	##### START VD_hangup process #####################################
	###################################################################
	if ($process =~ /^VD_hangup/)
	{
	$nothing=0;
	}
	###################################################################
	##### END VD_hangup process #######################################
	###################################################################


}


TEST_VDfastAGI->run(
					port=>4577,
					user=>'root',
					group=>'root',
					min_servers=>$VARfastagi_log_min_servers,
					max_servers=>$VARfastagi_log_max_servers,
					min_spare_servers=>$VARfastagi_log_min_spare_servers,
					max_spare_servers=>$VARfastagi_log_max_spare_servers,
					max_requests=>$VARfastagi_log_max_requests,
					check_for_dead=>$VARfastagi_log_checkfordead,
					check_for_waiting=>$VARfastagi_log_checkforwait,
					log_file=>$childLOGfile,
					log_level=>$log_level
					);
exit;





sub agi_output
{
if ($AGILOG >=2)
	{
	### open the log file for writing ###
	open(Lout, ">>$AGILOGfile")
			|| die "Can't open $AGILOGfile: $!\n";
	print Lout "$now_date|$script|$process|$agi_string\n";
	close(Lout);
	}
	### send to STDERR writing ###
if ( ($AGILOG == '1') || ($AGILOG == '3') )
	{
	print STDERR "$now_date|$script|$process|$agi_string\n";
	}
$agi_string='';
}
