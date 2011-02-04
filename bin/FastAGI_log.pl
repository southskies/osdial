#!/usr/bin/perl
#
# FastAGI_log.pl version 2.0.5   *DBI-version*
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
# 80224-0040 - Fixed bugs in osdial_log updates
# 80430-0907 - Added term_reason to osdial_log and osdial_closer_log
# 80507-1138 - Fixed osdial_closer_log CALLER hangups
# 80510-0414 - Fixed crossover logging bugs
# 80510-2058 - Fixed status override bug
# 81021-0306 - Added Local channel logging support and while-to-if changes
# 81026-1247 - Changed to allow for better remote agent calling
#
# 090514-1630 - Delete temporary agent on OutboundIVR.
# 090514-1902 - Fix early hangups in OutboundIVR to not be tagged as DROP.


# defaults for PreFork
$VARfastagi_log_min_servers = '3';
$VARfastagi_log_max_servers = '16';
$VARfastagi_log_min_spare_servers = '2';
$VARfastagi_log_max_spare_servers = '8';
$VARfastagi_log_max_requests = '1000';
$VARfastagi_log_checkfordead = '30';
$VARfastagi_log_checkforwait = '60';

# default path to osdial.configuration file:
$PATHconf = '/etc/osdial.conf';

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
$mon = '0'.$mon if ($mon < 10);
$mday = '0'.$mday if ($mday < 10);
$hour = '0'.$hour if ($hour < 10);
$min = '0'.$min if ($min < 10);
$sec = '0'.$sec if ($sec < 10);

$VARDB_port='3306' unless ($VARDB_port);

$SERVERLOG = 'N';
$log_level = '0';

use DBI;
$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
	or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtB = "SELECT vd_server_logs FROM servers WHERE server_ip='$VARserver_ip';";
$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
$sthBrows=$sthB->rows;
$rec_count=0;
while ($sthBrows > $rec_count)
	{
	@aryB = $sthB->fetchrow_array;
		$SERVERLOG = $aryB[0];
	$rec_count++;
	}
$sthB->finish();
$dbhB->disconnect();

if ($SERVERLOG =~ /Y/)
	{
	$childLOGfile = $PATHlogs.'/FastAGIchildLOG.'.$year.'-'.$mon.'-'.$mday;
	$log_level = '4';
	print "SERVER LOGGING ON: LEVEL-$log_level FILE-$childLOGfile\n";
	}

package VDfastAGI;

use Net::Server;
use Asterisk::AGI;
use vars qw(@ISA);
use Net::Server::PreFork; # any personality will do
@ISA = qw(Net::Server::PreFork);




sub process_request {
	$process = 'begin';
	$script = 'VDfastAGI';
	########## Get current time, parse configs, get logging preferences ##########
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
	$year = ($year + 1900);
	$mon++;
	$mon = '0'.$mon if ($mon < 10);
	$mday = '0'.$mday if ($mday < 10);
	$hour = '0'.$hour if ($hour < 10);
	$min = '0'.$min if ($min < 10);
	$sec = '0'.$sec if ($sec < 10);

	$now_date_epoch = time();
	$now_date = "$year-$mon-$mday $hour:$min:$sec";


	# default path to osdial.configuration file:
	$PATHconf = '/etc/osdial.conf';

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

	$VARDB_port='3306' unless ($VARDB_port);
	$AGILOGfile = $PATHlogs.'/FASTagiout.'.$year.'-'.$mon.'-'.$mday unless ($AGILOGfile);

	$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
		or die "Couldn't connect to database: " . DBI->errstr;

	### Grab Server values from the database
	$stmtA = "SELECT agi_output,asterisk_version FROM servers WHERE server_ip='$VARserver_ip';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		$AGILOG = '0';
		@aryA = $sthA->fetchrow_array;
			$DBagi_output = $aryA[0];
			$DBasterisk_version = $aryA[1];
			$AGILOG = '1' if ($DBagi_output =~ /STDERR/);
			$AGILOG = '2' if ($DBagi_output =~ /FILE/);
			$AGILOG = '3' if ($DBagi_output =~ /BOTH/);
			$ZorD = 'Zap';
			$ZorD = 'DAHDI' if ($DBasterisk_version =~ /^1\.6/);
		$rec_count++;
		}
	$sthA->finish();




	if ($AGILOG)
		{
		$agi_string = '+++++++++++++++++ FastAGI Start ++++++++++++++++++++++++++++++++++++++++';
		&agi_output;
		}



	### begin parsing run-time options ###
	if (length($ARGV[0])>1)
	{
		if ($AGILOG)
			{
			$agi_string = 'Perl Environment Dump:';
			&agi_output;
			}
		$i=0;
		while ($#ARGV >= $i)
		{
			$args .= ' '.$ARGV[$i];
			if ($AGILOG)
				{
				$agi_string = $i.'|'.$ARGV[$i];
				&agi_output;
				}
			$i++;
		}
	}
	$HVcauses=0;
	$fullCID=0;
	$callerid='';
	$calleridname='';
	$accountcode='';
	$|=1;
	while(<STDIN>) {
		chomp;
		last unless length($_);
		$AGI{$1} = $2 if ($AGILOG and /^agi_(\w+)\:\s+(.*)$/);

		$uniqueid = $1 if (/^agi_uniqueid\:\s+(.*)$/);
		$priority = $1 if (/^agi_priority\:\s+(.*)$/);
		$channel = $1 if (/^agi_channel\:\s+(.*)$/);
		$extension = $1 if (/^agi_extension\:\s+(.*)$/);
		$type = $1 if (/^agi_type\:\s+(.*)$/);
		$request = $1 if (/^agi_request\:\s+(.*)$/);
		$accountcode = $1 if (/^agi_accountcode\:\s+(.*)$/);
		if ( ($request =~ /--fullCID--/i) && (!$fullCID) )
			{
			$fullCID=1;
			@CID = split(/-----/, $request);
			$callerid = $CID[2];
			$calleridname = $CID[3];
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
		if (!$fullCID) # if no fullCID sent
			{
			$callerid = $1 if (/^agi_callerid\:\s+(.*)$/);
			$calleridname = $1 if (/^agi_calleridname\:\s+(.*)$/);
			$calleridname =~ s/\"//gi if ( $calleridname =~ /\"/);
			$callerid = $calleridname if (((length($calleridname)>5) and (!$callerid or $callerid =~ /unknown|private|00000000/i or $callerid =~ /5551212/ )) or (length($calleridname)>17 and $calleridname =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/));

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
		$agi_string = "AGI Variables: |$uniqueid|$channel|$extension|$type|$callerid|$accountcode|";
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
			$channel_group='';
			$number_dialed='';
			$orig_extension = $extension;
			$extension =~ s/^dial.//g;

			if ($AGILOG) {$agi_string = "+++++ CALL LOG START : $now_date";   &agi_output;}

			if ($channel =~ /^SIP/) {
				$channel_line = $channel;
				$channel_line =~ s/^SIP\/|\-.*//gi;

				$stmtA = "SELECT count(*) FROM phones WHERE server_ip='$VARserver_ip' AND extension='$channel_line' AND protocol='SIP';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone = $aryA[0];
				$sthA->finish();

				if ($is_client_phone<1) {
					$channel_group = 'SIP Trunk';
				} else {
					$channel_group = 'SIP Phone';
					$number_dialed = $extension;
					$extension = $channel_line;
				}
				if ($AGILOG) {$agi_string = $channel_group . ": $aryA[0]|$channel_line|";   &agi_output;}
			}
			if ($channel =~ /^IAX2/) {
				$channel_line = $channel;
				$channel_line =~ s/^IAX2\/|\-.*//gi;

				$stmtA = "SELECT count(*) FROM phones WHERE server_ip='$VARserver_ip' AND extension='$channel_line' AND protocol='IAX2';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone = $aryA[0];
				$sthA->finish();

				if ($is_client_phone<1) {
					$channel_group = 'IAX2 Trunk';
				} else {
					$channel_group = 'IAX2 Phone';
					$number_dialed = $extension;
					$extension = $channel_line;
				}
				if ($AGILOG) {$agi_string = $channel_group . ": $aryA[0]|$channel_line|";   &agi_output;}
			}
			if ($channel =~ /^$ZorD\//)
				{
				$channel_line = $channel;
				$channel_line =~ s/^$ZorD\///gi;

				$stmtA = "SELECT count(*) FROM phones WHERE server_ip='$VARserver_ip' AND extension='$channel_line' AND protocol='$ZorD';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone = $aryA[0];
				$sthA->finish();

				if ($is_client_phone<1) {
					$channel_group = $ZorD . ' Trunk';
				} else {
					$channel_group = $ZorD . ' Phone';
					$number_dialed = $extension;
					$extension = $channel_line;
				}
				if ($AGILOG) {$agi_string = $channel_group . ": $aryA[0]|$channel_line|";   &agi_output;}
				}
			if ($channel =~ /^Local\//)
				{
				$channel_line = $channel;
				$channel_line =~ s/^Local\/|\@.*//gi;
			
				$stmtA = "SELECT count(*),extension FROM phones WHERE server_ip='$VARserver_ip' AND dialplan_number='$channel_line' AND protocol='EXTERNAL' LIMIT 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				@aryA = $sthA->fetchrow_array;
				$is_client_phone = $aryA[0];
				$phone_ext = $aryA[1];
				$sthA->finish();
			
				if ($is_client_phone<1) {
					$channel_group = 'Local Channel';
				} else {
					$channel_group = 'EXTERNAL Phone';
					$number_dialed = $channel_line;
					$extension = $phone_ext;
				}
				if ($AGILOG) {$agi_string = $channel_group . ": $aryA[0]|$channel_line|";   &agi_output;}
								}

			if ( ($accountcode =~ /^V|^M|^DC/) && ($accountcode =~ /\d\d\d\d\d\d\d\d\d/) && (length($number_dialed)<1) )
				{
				$stmtA = "SELECT SQL_NO_CACHE cmd_line_b,cmd_line_d FROM osdial_manager WHERE callerid='$accountcode' LIMIT 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				if ($sthArows > 0) {
					@aryA = $sthA->fetchrow_array;
					$cmd_line_b = $aryA[0];
					$cmd_line_d = $aryA[1];
					if ($accountcode =~ /^DC/) {
						$cmd_line_d =~ s/Exten: //gi;
						$cmd_line_b =~ s/Channel: Local\/|\@.*//gi;
						$extension=$cmd_line_d;
						$number_dialed=$cmd_line_b;
					} else {
						$cmd_line_b =~ s/Exten: //gi;
						$cmd_line_d =~ s/Channel: Local\/|\@.*//gi;
						if ($accountcode =~ /^V/) {$extension=$cmd_line_b; $number_dialed=$cmd_line_d;}
						if ($accountcode =~ /^M/) {$extension=$cmd_line_d; $number_dialed=$cmd_line_b;}
						$rec_count++;
					}
				}
				$sthA->finish();
				$extension =~ s/\D//gi;
				$number_dialed =~ s/\D//gi;
				$number_dialed=$extension if (length($number_dialed)<1);
				}
			if ( ($accountcode =~ /^Y/) && ($accountcode =~ /\d\d\d\d\d\d\d\d\d/) && (length($number_dialed)<1) )
				{
				$number_dialed = $callerid;
				$number_dialed='' if (length($number_dialed)<1);
				}

			if ( ($accountcode =~ /^Y/) ) {
				$channel_group = 'Inbound';
				if ( ($extension =~ /^800|^888|^877|^866/) && (length($number_dialed)==10) )
					{$channel_group = 'Inbound 800';}
			} else {
			### This section breaks the outbound dialed number down(or builds it up) to a 10 digit number and gives it a description
				if ( ($number_dialed =~ /^.01144/) && (length($number_dialed)==16) )  #test 207 608 6400
					{$number_dialed =~ s/^.//; $channel_group = 'Outbound Intl UK';}
				if ( ($number_dialed =~ /^01144/) && (length($number_dialed)==15) )  #test 207 608 6400
					{$channel_group = 'Outbound Intl UK';}
				if ( ($number_dialed =~ /^.01161/) && (length($number_dialed)==15) )  #test  39 417 2011
					{$number_dialed =~ s/^.//; $channel_group = 'Outbound Intl AUS';}
				if ( ($number_dialed =~ /^01161/) && (length($number_dialed)==14) )  #test  39 417 2011
					{$channel_group = 'Outbound Intl AUS';}
				if ( ($number_dialed =~ /^.1800|^.1888|^.1877|^.1866/) && (length($number_dialed)==12) )
					{$number_dialed =~ s/^.1//; $channel_group = 'Outbound 800';}
				if ( ($number_dialed =~ /^1800|^1888|^1877|^1866/) && (length($number_dialed)==11) )
					{$number_dialed =~ s/^1//; $channel_group = 'Outbound 800';}
				if ( ($number_dialed =~ /^800|^888|^877|^866/) && (length($number_dialed)==10) )
					{$channel_group = 'Outbound 800';}
				if ( ($number_dialed =~ /^.1/) && (length($number_dialed)==12) )
					{$number_dialed =~ s/^.1//; $channel_group = 'Outbound';}
				if ( ($number_dialed =~ /^1/) && (length($number_dialed)==11) )
					{$number_dialed =~ s/^1//; $channel_group = 'Outbound';}
				if ((length($number_dialed)==10) )
					{$channel_group = 'Outbound';}
				
			}

			$stmtA = "INSERT INTO call_log (uniqueid,channel,channel_group,type,server_ip,extension,number_dialed,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,caller_code) VALUES('$uniqueid','$channel','$channel_group','$type','$VARserver_ip','$extension','$number_dialed','$now_date','$now_date_epoch','','','','','$accountcode')";

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
			$stmtA = "SELECT SQL_NO_CACHE uniqueid,start_epoch FROM call_log WHERE uniqueid='$uniqueid';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$start_time = $aryA[1];
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

				$stmtA = "UPDATE call_log SET end_time='$now_date',end_epoch='$now_date_epoch',length_in_sec=$length_in_sec,length_in_min='$length_in_min',channel='$channel',isup_result='$hangup_cause' WHERE uniqueid='$uniqueid';";

				if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
				$affected_rows = $dbhA->do($stmtA);
				}

			$stmtA = "DELETE FROM live_inbound WHERE uniqueid='$uniqueid' AND server_ip='$VARserver_ip';";
			if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
			$affected_rows = $dbhA->do($stmtA);

		##### BEGIN Park Log entry check and update #####
			$stmtA = "SELECT SQL_NO_CACHE UNIX_TIMESTAMP(parked_time),UNIX_TIMESTAMP(grab_time) FROM park_log WHERE uniqueid='$uniqueid' AND server_ip='$VARserver_ip' LIMIT 1;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$parked_time = $aryA[0];
				$grab_time = $aryA[1];
				if ($AGILOG) {$agi_string = "|$aryA[0]|$aryA[1]|";   &agi_output;}
				$rec_count++;
				}
			$sthA->finish();

			if ($rec_count)
			{
			if ($AGILOG) {$agi_string = "*****Entry found for $uniqueid-$VARserver_ip in park_log: $parked_time|$grab_time";   &agi_output;}
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

				$stmtA = "UPDATE park_log SET status='HUNGUP',hangup_time='$now_date',parked_sec='$parked_sec',talked_sec='$talked_sec' WHERE uniqueid='$uniqueid' AND server_ip='$VARserver_ip';";
				$affected_rows = $dbhA->do($stmtA);
			}
		##### END Park Log entry check and update #####

		# $dbhA->disconnect();

			if ($AGILOG) {$agi_string = "+++++ CALL LOG HUNGUP: |$uniqueid|$channel|$extension|$now_date|min: $length_in_min|";   &agi_output;}


		##### BEGIN former VD_hangup section functions #####

			$VDADcampaign='';
			$VDADphone='';
			$VDADphone_code='';

			if ($DEBUG =~ /^DEBUG$/)
			{
				### open the hangup cause out file for writing ###
				open(out, ">>$PATHlogs/HANGUP_cause-output.txt")
						|| die "Can't open $PATHlogs/HANGUP_cause-output.txt: $!\n";

				print out "$now_date|$hangup_cause|$dialstatus|$dial_time|$ring_time|$uniqueid|$channel|$extension|$type|$accountcode|$priority|\n";

				close(out);
			}
			else
			{
			if ($AGILOG) {$agi_string = "DEBUG: $DEBUG";   &agi_output;}
			}


			$CIDlead_id = $accountcode;
			$CIDlead_id = substr($CIDlead_id, 11, 9);
			$CIDlead_id = ($CIDlead_id + 0);

			if ($AGILOG) {$agi_string = "VD_hangup : $accountcode $channel $priority $CIDlead_id";   &agi_output;}

			if ($channel =~ /^Local/)
			{
				if ( ($PRI =~ /^PRI$/) && ($accountcode =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) && ( ($dialstatus =~ /BUSY|CONGESTION/) || ($hangup_cause =~ /^27$|^29$|^34$|^38$/) || ( ($dialstatus =~ /CHANUNAVAIL/) && ($hangup_cause =~ /^1$|^28$/) ) ) )
				{
					if ($dialstatus =~ /CONGESTION/) {$VDL_status='CRC'; $VDAC_status = 'BUSY';}
					if ($dialstatus =~ /BUSY/) {$VDL_status = 'B'; $VDAC_status = 'BUSY';}
					if ($dialstatus =~ /CHANUNAVAIL/) {$VDL_status = 'DC'; $VDAC_status = 'DISCONNECT';}

					if ($hangup_cause =~ /^38$/) {
						# Carrier Failure
						$VDL_status = 'CRF'; $VDAC_status = 'BUSY';
					} elsif ($hangup_cause =~ /^29$/) {
						# Carrier Rejected
						$VDL_status = 'CRR'; $VDAC_status = 'BUSY';
					} elsif ($hangup_cause =~ /^27$/) {
						# Destination Out of Order
						$VDL_status = 'CRO'; $VDAC_status = 'BUSY';
					} elsif ($hangup_cause =~ /^34$/) {
						# General Congestion
						$VDL_status = 'CRC'; $VDAC_status = 'BUSY';
					}

					$stmtA = "UPDATE osdial_list SET status='$VDL_status' WHERE lead_id='$CIDlead_id';";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$VDADaffected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--   VDAD osdial_list update: |$VDADaffected_rows|$CIDlead_id";   &agi_output;}

					$stmtA = "UPDATE osdial_auto_calls SET status='$VDAC_status' WHERE callerid='$accountcode';";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$VDACaffected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--   VDAC update: |$VDACaffected_rows|$CIDlead_id";   &agi_output;}

					#$stmtA = "UPDATE osdial_log set status='$VDL_status' where uniqueid = '$uniqueid';";
						$Euniqueid=$uniqueid;
						$Euniqueid =~ s/\.\d+$//gi;
					$stmtA = "UPDATE osdial_log FORCE INDEX(lead_id) SET status='$VDL_status' WHERE lead_id='$CIDlead_id' AND uniqueid LIKE '$Euniqueid%';";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$VDLaffected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--   VDAD osdial_log update: |$VDLaffected_rows|$uniqueid|$VDACuniqueid|";   &agi_output;}

					sleep(1);

					$dbhA->disconnect();
				}
				else
				{
					if ($AGILOG) {$agi_string = "--   VD_hangup Local DEBUG: |$PRI|$accountcode|$dialstatus|$hangup_cause|";   &agi_output;}
				}

				if ($AGILOG) {$agi_string = "+++++ VDAD START LOCAL CHANNEL: EXITING- $priority";   &agi_output;}
				sleep(1) if ($priority > 2);
			}
			else
			{

				########## FIND AND DELETE osdial_auto_calls ##########
				$VD_alt_dial = 'NONE';
				$stmtA = "SELECT SQL_NO_CACHE lead_id,callerid,campaign_id,alt_dial,stage,UNIX_TIMESTAMP(call_time),uniqueid,status FROM osdial_auto_calls WHERE uniqueid='$uniqueid' OR callerid='$accountcode' LIMIT 1;";
					if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_countCUSTDATA=0;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$VD_lead_id = $aryA[0];
					$VD_callerid = $aryA[1];
					$VD_campaign_id = $aryA[2];
					$VD_alt_dial = $aryA[3];
					$VD_stage = $aryA[4];
					$VD_start_epoch = $aryA[5];
					$VD_uniqueid = $aryA[6];
					$VD_status = $aryA[7];
					$rec_countCUSTDATA++;
					}
				$sthA->finish();

				if (!$rec_countCUSTDATA)
					{
					if ($AGILOG) {$agi_string = "VD hangup: no VDAC record found: $uniqueid $accountcode";   &agi_output;}
					}
				else
					{

					$stmtA = "SELECT SQL_NO_CACHE user,extension FROM osdial_live_agents WHERE uniqueid='$uniqueid' AND (extension LIKE 'R/va\%' OR extension LIKE 'R/tmp\%') LIMIT 1;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($sthArows > 0) {
						@aryA = $sthA->fetchrow_array;
						$OLAuser = $aryA[0];
						$OLAext = $aryA[1];
						$talksec = ($now_date_epoch - $VD_start_epoch);
						if ( ($OLAext =~ /^R\/tmp/) && ($OLAuser =~ /^tmp/) ) {
							$stmtA = "DELETE FROM osdial_users WHERE user='$OLAuser' LIMIT 1;";
							my $affected_rows = $dbhA->do($stmtA);
							$stmtA = "DELETE FROM osdial_live_agents WHERE uniqueid='$uniqueid' LIMIT 1;";
							my $affected_rows = $dbhA->do($stmtA);
						}
						$stmtA = "SELECT status,comments FROM osdial_list WHERE lead_id='$VD_lead_id';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0) {
							@aryA = $sthA->fetchrow_array;
							$lstat = $aryA[0];
							$lcomm = $aryA[1];
							@aryA = $sthA->fetchrow_array;
							$stmtA = "INSERT INTO osdial_agent_log SET user='$OLAuser',server_ip='$VARserver_ip',event_time=NOW(),lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',talk_epoch='$VD_start_epoch',talk_sec='$talksec',status='$lstat',user_group='VIRTUAL',comments='$lcomm';";
							my $affected_rows = $dbhA->do($stmtA);
						}
					}

					$stmtA = "DELETE FROM osdial_auto_calls WHERE uniqueid='$uniqueid' ORDER BY call_time DESC LIMIT 1;";
					$affected_rows = $dbhA->do($stmtA);
					if ($AGILOG) {$agi_string = "--   VDAC record deleted: |$affected_rows|   |$VD_lead_id|$uniqueid|$VD_uniqueid|$VD_callerid|$VARserver_ip|$VD_status|";   &agi_output;}

					#############################################
					##### START QUEUEMETRICS LOGGING LOOKUP #####
					$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,enable_multicompany FROM system_settings;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
							$enable_queuemetrics_logging = $aryA[0];
							$queuemetrics_server_ip = $aryA[1];
							$queuemetrics_dbname = $aryA[2];
							$queuemetrics_login= $aryA[3];
							$queuemetrics_pass = $aryA[4];
							$queuemetrics_log_id = $aryA[5];
							$enable_multicompany = $aryA[6];
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
						$VD_stage=0 if ($VD_stage < 0.25);

						$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
						or die "Couldn't connect to database: " . DBI->errstr;

						print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n" if ($DBX);

						$stmtB = "SELECT agent FROM queue_log WHERE call_id='$VD_callerid' AND verb='CONNECT';";
						$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
						$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
						$sthBrows=$sthB->rows;
						$rec_count=0;
						while ($sthBrows > $rec_count)
							{
							@aryB = $sthB->fetchrow_array;
							$VD_agent = $aryB[0];
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


					$epc_countCUSTDATA=0;
					$VD_closecallid='';
					$VDL_update=0;
					$Euniqueid=$uniqueid;
					$Euniqueid =~ s/\.\d+$//gi;

					if ($accountcode !~ /^Y\d\d\d\d/)
						{
						########## FIND AND UPDATE osdial_log ##########
						$stmtA = "SELECT SQL_NO_CACHE start_epoch,status,user,term_reason,comments FROM osdial_log FORCE INDEX(lead_id) WHERE lead_id='$VD_lead_id' AND uniqueid LIKE \"$Euniqueid%\" LIMIT 1;";
							if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$VD_start_epoch = $aryA[0];
							$VD_status = $aryA[1];
							$VD_user = $aryA[2];
							$VD_term_reason = $aryA[3];
							$VD_comments = $aryA[4];
							$epc_countCUSTDATA++;

							}
						$sthA->finish();
						}

					if ( (!$epc_countCUSTDATA) || ($accountcode =~ /^Y\d\d\d\d/) )
						{
						if ($AGILOG) {$agi_string = "no VDL record found: $uniqueid $accountcode $VD_lead_id $uniqueid $VD_uniqueid";   &agi_output;}

						$secX = time();
						$Rtarget = ($secX - 21600); # look for VDCL entry within last 6 hours
						($Rsec,$Rmin,$Rhour,$Rmday,$Rmon,$Ryear,$Rwday,$Ryday,$Risdst) = localtime($Rtarget);
						$Ryear = ($Ryear + 1900);
						$Rmon++;
						$Rmon = '0'.$Rmon if ($Rmon < 10);
						$Rmday = '0'.$Rmday if ($Rmday < 10);
						$Rhour = '0'.$Rhour if ($Rhour < 10);
						$Rmin = '0'.$Rmin if ($Rmin < 10);
						$Rsec = '0'.$Rsec if ($Rsec < 10);
						$RSQLdate = $Ryear.'-'.$Rmon.'-'.$Rmday.' '.$Rhour.':'.$Rmin.':'.$Rsec;

						$stmtA = "SELECT SQL_NO_CACHE start_epoch,status,closecallid,user,term_reason,length_in_sec,queue_seconds,comments FROM osdial_closer_log WHERE lead_id='$VD_lead_id' AND call_date>'$RSQLdate' AND end_epoch IS NULL ORDER BY call_date ASC LIMIT 1;";

							if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$epc_countCUSTDATA=0;
						$VD_closecallid='';
						if ($sthArows > 0)
							{
							@aryA = $sthA->fetchrow_array;
							$VD_start_epoch = $aryA[0];
							$VD_status = $aryA[1];
							$VD_closecallid = $aryA[2];
							$VD_user = $aryA[3];
							$VD_term_reason = $aryA[4];
							$VD_length_in_sec = $aryA[5];
							$VD_queue_seconds = $aryA[6];
							$VD_comments = $aryA[7];
							$epc_countCUSTDATA++;
							}
						$sthA->finish();
						}
					if (!$epc_countCUSTDATA)
						{
						if ($AGILOG) {$agi_string = "no VDL or VDCL record found: $uniqueid $accountcode $VD_lead_id $uniqueid $VD_uniqueid";   &agi_output;}
						}
					else
						{
						$VD_seconds = ($now_date_epoch - $VD_start_epoch);

						$SQL_status='';
						if ( ($VD_status =~ /^NA$|^NEW$|^QUEUE$|^XFER$/) && ($VD_comments !~ /REMOTE/) && ($VD_user ne "IVR") )
							{
							if ( ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT/) && ( ($VD_user =~ /VDAD|VDCL/) || (length($VD_user) < 1) ) )
								{$VDLSQL_term_reason = "term_reason='ABANDON',";}
							else
								{
								if ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT/)
									{$VDLSQL_term_reason = "term_reason='CALLER',";}
								else
									{$VDLSQL_term_reason = '';}
								}
							$SQL_status = "status='DROP',$VDLSQL_term_reason";


							########## FIND AND UPDATE osdial_list ##########
							$stmtA = "UPDATE osdial_list set status='DROP' WHERE lead_id='$VD_lead_id';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$affected_rows = $dbhA->do($stmtA);
							if ($AGILOG) {$agi_string = "--   VDAD osdial_list update: |$affected_rows|$VD_lead_id";   &agi_output;}
							}
						else
							{
							$SQL_status = "term_reason='CALLER',";
							}

						if ($accountcode !~ /^Y\d\d\d\d/)
							{
							$VDL_update=1;
							$stmtA = "UPDATE osdial_log FORCE INDEX(lead_id) SET $SQL_status end_epoch='$now_date_epoch',length_in_sec='$VD_seconds' WHERE lead_id='$VD_lead_id' AND uniqueid LIKE \"$Euniqueid%\";";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$VLaffected_rows = $dbhA->do($stmtA);
							if ($AGILOG) {$agi_string = "--   VDAD osdial_log update: |$VLaffected_rows|$uniqueid|$VD_status|";   &agi_output;}
							}



						########## UPDATE osdial_closer_log ##########
						if ( (length($VD_closecallid) < 1) || ($VDL_update > 0) )
							{
							if ($AGILOG) {$agi_string = "no VDCL record found: $uniqueid|$accountcode|$VD_lead_id|$uniqueid|$VD_uniqueid|$VDL_update";   &agi_output;}
							}
						else
							{
							if ($VD_status =~ /^DONE$|^INCALL$|^XFER$/)
								{$VDCLSQL_update = "term_reason='CALLER',";}
							else
								{
								if ( ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT|AFTERHOURS|HOLDRECALLXFER|HOLDTIME/) && ( ($VD_user =~ /VDAD|VDCL/) || (length($VD_user) < 1) ) )
									{$VDCLSQL_term_reason = "term_reason='ABANDON',";}
								else
									{
									if ($VD_term_reason !~ /AGENT|CALLER|QUEUETIMEOUT|AFTERHOURS|HOLDRECALLXFER|HOLDTIME/)
										{$VDCLSQL_term_reason = "term_reason='CALLER',";}
									else
										{$VDCLSQL_term_reason = '';}
									}
								if ($VD_status =~ /QUEUE/)
									{
									$VDCLSQL_status = "status='DROP',";
									$VDCLSQL_queue_seconds = "queue_seconds='$VD_seconds',";
									}
								else
									{
									$VDCLSQL_status = '';
									$VDCLSQL_queue_seconds = '';
									}

								$VDCLSQL_update = $VDCLSQL_status.$VDCLSQL_term_reason.$VDCLSQL_queue_seconds;
								}


							$VD_seconds = ($now_date_epoch - $VD_start_epoch);
							$stmtA = "UPDATE osdial_closer_log SET $VDCLSQL_update end_epoch='$now_date_epoch',length_in_sec='$VD_seconds' WHERE closecallid='$VD_closecallid';";
								if ($AGILOG) {$agi_string = "|$VDCLSQL_update|$VD_status|$VD_length_in_sec|$VD_term_reason|$VD_queue_seconds|\n|$stmtA|";   &agi_output;}
							$affected_rows = $dbhA->do($stmtA);
							if ($AGILOG) {$agi_string = "--   VDCL update: |$affected_rows|$uniqueid|$VD_closecallid|";   &agi_output;}

							}
						}

					##### BEGIN AUTO ALT PHONE DIAL SECTION #####
					### check to see if campaign has alt_dial enabled
					$VD_auto_alt_dial = 'NONE';
					$VD_auto_alt_dial_statuses='';
					$stmtA="SELECT auto_alt_dial,auto_alt_dial_statuses,use_internal_dnc FROM osdial_campaigns WHERE campaign_id='$VD_campaign_id';";
						if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$epc_countCAMPDATA=0;
					while ($sthArows > $epc_countCAMPDATA)
						{
						@aryA = $sthA->fetchrow_array;
						$VD_auto_alt_dial = $aryA[0];
						$VD_auto_alt_dial_statuses = $aryA[1];
						$VD_use_internal_dnc = $aryA[2];
						$epc_countCAMPDATA++;
						}
					$sthA->finish();
					if ($VD_auto_alt_dial_statuses =~ / $VD_status | $VDL_status /)
						{
						if ( ($VD_auto_alt_dial =~ /ALT_ONLY|ALT_AND_ADDR3|ALT_ADDR3_AND_AFFAP/) && ($VD_alt_dial =~ /NONE|MAIN/) )
							{
							$VD_alt_phone='';
							$stmtA="SELECT alt_phone,gmt_offset_now,state,list_id FROM osdial_list WHERE lead_id='$VD_lead_id';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							$epc_countCAMPDATA=0;
							while ($sthArows > $epc_countCAMPDATA)
								{
								@aryA = $sthA->fetchrow_array;
								$VD_alt_phone = $aryA[0];
								$VD_alt_phone =~ s/\D//gi;
								$VD_gmt_offset_now = $aryA[1];
								$VD_state = $aryA[2];
								$VD_list_id = $aryA[3];
								$epc_countCAMPDATA++;
								}
							$sthA->finish();
							$VD_alt_dnc_count=0;
							if (length($VD_alt_phone)>5) {
								if ($VD_use_internal_dnc =~ /Y/) {
									$dncsskip=0;
									if ($enable_multicompany > 0) {
										$comp_id=0;
										$dnc_method='';
										$stmtA="SELECT comp_id,dnc_method FROM osdial_companies WHERE company_id='" . ((substr($VD_campaign_id,0,3) * 1) - 100) . "';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										if (@aryA = $sthA->fetchrow_array) {
											$comp_id = $aryA[0];
											$dnc_method = $aryA[1];
										}
										$sthA->finish();
										if ($dnc_method =~ /COMPANY|BOTH/) {
											$stmtA="SELECT SQL_NO_CACHE count(*) FROM osdial_dnc_company WHERE company_id='$comp_id' AND (phone_number='$VD_alt_phone' OR phone_number='" . substr($VD_alt_phone,0,3) . "XXXXXXX');";
											if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											if (@aryA = $sthA->fetchrow_array) {
												$VD_alt_dnc_count += $aryA[0];
											}
											$sthA->finish();
										}
										if ($dnc_method =~ /COMPANY/) {
											$dncsskip++;
										}
									}
									if ($dncsskip==0) {
										$stmtA="SELECT SQL_NO_CACHE count(*) FROM osdial_dnc WHERE (phone_number='$VD_alt_phone' OR phone_number='" . substr($VD_alt_phone,0,3) . "XXXXXXX');";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										if (@aryA = $sthA->fetchrow_array) {
											$VD_alt_dnc_count += $aryA[0];
										}
										$sthA->finish();
									}
								}
								if ($VD_alt_dnc_count < 1) {
									$stmtA = "INSERT INTO osdial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ALT',user='',priority='25';";
									$affected_rows = $dbhA->do($stmtA);
									if ($AGILOG) {$agi_string = "--   VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
								}
							}
							if ($VD_alt_dnc_count>0 or length($VD_alt_phone)<=5) {
								$VD_alt_dial='ALT';
							}
						}
						if ( ( ($VD_auto_alt_dial =~ /(ADDR3_ONLY)/) && ($VD_alt_dial =~ /NONE|MAIN/) ) || ( ($VD_auto_alt_dial =~ /ALT_AND_ADDR3|ALT_ADDR3_AND_AFFAP/) && ($VD_alt_dial =~ /ALT/) ) )
							{
							$VD_address3='';
							$stmtA="SELECT address3,gmt_offset_now,state,list_id FROM osdial_list WHERE lead_id='$VD_lead_id';";
								if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							$epc_countCAMPDATA=0;
							while ($sthArows > $epc_countCAMPDATA)
								{
								@aryA = $sthA->fetchrow_array;
								$VD_address3 = $aryA[0];
								$VD_address3 =~ s/\D//gi;
								$VD_gmt_offset_now = $aryA[1];
								$VD_state = $aryA[2];
								$VD_list_id = $aryA[3];
								$epc_countCAMPDATA++;
								}
							$sthA->finish();
							$VD_addr3_dnc_count=0;
							if (length($VD_address3)>5) {
								if ($VD_use_internal_dnc =~ /Y/) {
									$dncsskip=0;
									if ($enable_multicompany > 0) {
										$comp_id=0;
										$dnc_method='';
										$stmtA="SELECT comp_id,dnc_method FROM osdial_companies WHERE company_id='" . ((substr($VD_campaign_id,0,3) * 1) - 100) . "';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										if (@aryA = $sthA->fetchrow_array) {
											$comp_id = $aryA[0];
											$dnc_method = $aryA[1];
										}
										$sthA->finish();
										if ($dnc_method =~ /COMPANY|BOTH/) {
											$stmtA="SELECT SQL_NO_CACHE count(*) FROM osdial_dnc_company WHERE company_id='$comp_id' AND (phone_number='$VD_address3' OR phone_number='" . substr($VD_address3,0,3) . "XXXXXXX');";
											if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											if (@aryA = $sthA->fetchrow_array) {
												$VD_addr3_dnc_count += $aryA[0];
											}
											$sthA->finish();
										}
										if ($dnc_method =~ /COMPANY/) {
											$dncsskip++;
										}
									}
									if ($dncsskip==0) {
										$stmtA="SELECT SQL_NO_CACHE count(*) FROM osdial_dnc WHERE (phone_number='$VD_address3' OR phone_number='" . substr($VD_address3,0,3) . "XXXXXXX');";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
										$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
										$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
										if (@aryA = $sthA->fetchrow_array) {
											$VD_addr3_dnc_count += $aryA[0];
										}
										$sthA->finish();
									}
								}
								if ($VD_addr3_dnc_count < 1) {
									$stmtA = "INSERT INTO osdial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ADDR3',user='',priority='25';";
									$affected_rows = $dbhA->do($stmtA);
									if ($AGILOG) {$agi_string = "--   VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
								}
							}
							if ($VD_addr3_dnc_count>0 or length($VD_address3)<=5) {
								$VD_alt_dial='ADDR3';
							}
						}
						if ($VD_auto_alt_dial =~ /ALT_ADDR3_AND_AFFAP/ && $VD_alt_dial =~ /ADDR3|AFFAP/) {
							$aff_number = '';
							$cur_aff = 1;
							if ($VD_alt_dial ne 'ADDR3') {
								$cur_aff = (substr($VD_alt_dial,5) * 1) + 1;
							}
							while ($cur_aff < 10) {
								$stmtA="SELECT SQL_NO_CACHE value FROM osdial_list_fields WHERE field_id=(SELECT id FROM osdial_campaign_fields WHERE name='AFFAP$cur_aff') AND lead_id='$VD_lead_id';";
								$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
								$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
								$sthArows=$sthA->rows;
								if ($sthArows > 0) {
									@aryA = $sthA->fetchrow_array;
									$aff_number = $aryA[0];
									$aff_number =~ s/\D//gi;
								}
								$sthA->finish();

								$VD_aff_dnc_count=0;
								if (length($aff_number)>5) {
									$stmtA="SELECT gmt_offset_now,state,list_id FROM osdial_list WHERE lead_id='$VD_lead_id';";
										if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
									$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
									$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
									$sthArows=$sthA->rows;
									$epc_countCAMPDATA=0;
									while ($sthArows > $epc_countCAMPDATA) {
										@aryA = $sthA->fetchrow_array;
										$VD_gmt_offset_now = $aryA[0];
										$VD_state = $aryA[1];
										$VD_list_id = $aryA[2];
										$epc_countCAMPDATA++;
									}
									$sthA->finish();

									if ($VD_use_internal_dnc =~ /Y/) {
										$dncsskip=0;
										if ($enable_multicompany > 0) {
											$comp_id=0;
											$dnc_method='';
											$stmtA="SELECT comp_id,dnc_method FROM osdial_companies WHERE company_id='" . ((substr($VD_campaign_id,0,3) * 1) - 100) . "';";
											if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											if (@aryA = $sthA->fetchrow_array) {
												$comp_id = $aryA[0];
												$dnc_method = $aryA[1];
											}
											$sthA->finish();
											if ($dnc_method =~ /COMPANY|BOTH/) {
												$stmtA="SELECT SQL_NO_CACHE count(*) FROM osdial_dnc_company WHERE company_id='$comp_id' AND (phone_number='$aff_number' OR phone_number='" . substr($aff_number,0,3) . "XXXXXXX');";
												if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
												$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
												$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
												if (@aryA = $sthA->fetchrow_array) {
													$VD_aff_dnc_count += $aryA[0];
												}
												$sthA->finish();
											}
											if ($dnc_method =~ /COMPANY/) {
												$dncsskip++;
											}
										}
										if ($dncsskip==0) {
											$stmtA="SELECT SQL_NO_CACHE count(*) FROM osdial_dnc WHERE (phone_number='$aff_number' OR phone_number='" . substr($aff_number,0,3) . "XXXXXXX');";
											if ($AGILOG) {$agi_string = "|$stmtA|";   &agi_output;}
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											if (@aryA = $sthA->fetchrow_array) {
												$VD_aff_dnc_count += $aryA[0];
											}
											$sthA->finish();
										}
									}

									if ($VD_aff_dnc_count < 1) {
										$stmtA = "INSERT INTO osdial_hopper SET lead_id='$VD_lead_id',campaign_id='$VD_campaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='AFFAP$cur_aff',user='',priority='25';";
										$affected_rows = $dbhA->do($stmtA);
										if ($AGILOG) {$agi_string = "--   VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
										$cur_aff=10;
									}
								}
								$cur_aff++ if ($VD_aff_dnc_count>0 or length($aff_number)<=5);
								}
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
	$nothing=0 if ($process =~ /^VD_hangup/);
	###################################################################
	##### END VD_hangup process #######################################
	###################################################################


}


VDfastAGI->run(
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
	print STDERR "$now_date|$script|$process|$agi_string\n" if ($AGILOG == '1' or $AGILOG == '3');
$agi_string='';
}
