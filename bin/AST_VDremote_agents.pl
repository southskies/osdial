#!/usr/bin/perl
#
# AST_VDremote_agents.pl version 2.0.5   *DBI-version*
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
# DESCRIPTION:
# uses Net::MySQL to keep remote agents logged in to the OSDIAL system 
#
# SUMMARY:
# This program was designed for people using the Asterisk PBX with OSDIAL
#
# For the client to use OSDIAL with remote agents, this must always be running 
# 
# It is recommended that you run this program on the local Asterisk machine
#
# This script is to run perpetually querying every second to update the remote 
# agents that should appear to be logged in so that the calls can be transferred 
# out to them properly.
#
# It is good practice to keep this program running by placing the associated 
# KEEPALIVE script running every minute to ensure this program is always running
#
# CHANGELOG:
# 50215-0954 - First version of script
# 50810-1615 - Added database server variable definitions lookup
# 60807-1003 - Changed to DBI
#            - Changed to use /etc/osdial.conf for configs
# 60814-1726 - Added option for no logging to file
# 60814-1726 - Added option for no logging to file
# 61012-1025 - Added performance testing options
# 61110-1443 - Added user_level from osdial_user record into osdial_live_agents
# 70213-1306 - Added queuemetrics logging
# 70214-1243 - Added queuemetrics_log_id field to queue_log logging
# 70222-1606 - Changed queue_log PAUSE/UNPAUSE to PAUSEALL/UNPAUSEALL
# 70417-1346 - Fixed bug that would add unneeded simulated agent lines
# 80128-0105 - Fixed calls_today bug
# 81007-1746 - Added debugX output for count statements and changed to if from while
# 81026-1246 - Added better logging of calls and fixed the DROP bug
#
# 090418-2145 - Allow non-numeric remote agents from the Outbound IVR

$|++;

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
	print "allowed run time options:\n  [-t] = test\n  [-v] = verbose debug messages\n  [--delay=XXX] = delay of XXX seconds per loop, default 2 seconds\n\n";
	}
	else
	{
		if ($args =~ /--delay=/i)
		{
		@data_in = split(/--delay=/,$args);
			$loop_delay = $data_in[1];
			print "     LOOP DELAY OVERRIDE!!!!! = $loop_delay seconds\n\n";
			$loop_delay = ($loop_delay * 1000);
		}
		else
		{
		$loop_delay = '2000';
		}
		if ($args =~ /--debug/i)
		{
		$DB=1;
		print "\n-----DEBUGGING -----\n\n";
		}
		if ($args =~ /--debugX/i)
		{
		$DBX=1;
		print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
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
print "no command line options set\n";
	$loop_delay = '2000';
}
### end parsing run-time options ###


# constants
$US='__';
@MT=();
$local_DEF = 'Local/';
$conf_silent_prefix = '7';
$local_AMP = '@';
$agents = '@agents';

# default path to osdial configuration file:
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

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP


	&get_time_now;	# update time/date variables

if (!$VDRLOGfile) {$VDRLOGfile = "$PATHlogs/remoteagent.$year-$mon-$mday";}
if (!$VARDB_port) {$VARDB_port='3306';}

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use Proc::ProcessTable;
use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;
$dbhB = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
	$stmtA = "SELECT vd_server_logs,local_gmt,ext_context FROM servers WHERE server_ip='$VARserver_ip';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	$rec_count=0;
	while ($sthArows > $rec_count)
		{
		 @aryA = $sthA->fetchrow_array;
			$DBvd_server_logs =			"$aryA[0]";
			$DBSERVER_GMT =				"$aryA[1]";
			$ext_context =				"$aryA[2]";
			if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
				else {$SYSLOG = '0';}
			if (length($DBSERVER_GMT)>0)	{$SERVER_GMT = $DBSERVER_GMT;}
		 $rec_count++;
		}
	$sthA->finish();


	&get_time_now;	# update time/date variables

	$event_string='PROGRAM STARTED||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||';
	&event_logger;	# writes to the log and if debug flag is set prints to STDOUT


	$event_string='LOGGED INTO MYSQL SERVER ON 1 CONNECTION|';
	&event_logger;

$one_day_interval = 12;		# 1 month loops for one year 
while($one_day_interval > 0)
{

	$endless_loop=5760000;		# 30 days minutes at XXX seconds per loop

	while($endless_loop > 0)
	{
		&get_time_now;

		$VDRLOGfile = "$PATHlogs/remoteagent.$year-$mon-$mday";

		if ($endless_loop =~ /0$|5$/)
		{
		### Grab Server values from the database
			$stmtA = "SELECT vd_server_logs FROM servers WHERE server_ip='$VARserver_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				 @aryA = $sthA->fetchrow_array;
					$DBvd_server_logs =			"$aryA[0]";
					if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
						else {$SYSLOG = '0';}
				 $rec_count++;
				}
			$sthA->finish();

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
				$queuemetrics_login	=			"$aryA[3]";
				$queuemetrics_pass =			"$aryA[4]";
				$queuemetrics_log_id =			"$aryA[5]";
			 $rec_count++;
			}
		$sthA->finish();
		##### END QUEUEMETRICS LOGGING LOOKUP #####
		###########################################



		### delete call records that are LIVE for over 10 minutes and last_update_time < '$PDtsSQLdate'
		$stmtA = "DELETE FROM osdial_live_agents WHERE server_ip='$server_ip' AND status IN('PAUSED') AND extension LIKE \"R/%\";";
		$affected_rows = $dbhA->do($stmtA);

		if ($affected_rows > 0) {
			$event_string = "|     lagged call vla agent DELETED $affected_rows";
			$event_string .= "\n$stmtA" if ($DBX);
			&event_logger;
		}

		##### grab number of calls today in this campaign and increment
		$stmtA="SELECT SQL_NO_CACHE calls_today FROM osdial_live_agents WHERE extension LIKE \"R/%\";";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$vla_cc_ct=$sthA->rows;
		if ($vla_cc_ct > 0)
			{
			 @aryA = $sthA->fetchrow_array;
				$calls_today =	"$aryA[0]";
			 $rec_count++;
			}
		else
			{$calls_today ='0';}
		$calls_today++;
		$sthA->finish();

		@QHlive_agent_id=@MT;
		@QHlead_id=@MT;
		@QHuniqueid=@MT;
		@QHuser=@MT;
		@QHcall_type=@MT;
		##### grab number of calls today in this campaign and increment
		$stmtA = "SELECT SQL_NO_CACHE vla.live_agent_id,vla.lead_id,vla.uniqueid,vla.user,vac.call_type FROM osdial_live_agents vla,osdial_auto_calls vac WHERE vla.server_ip='$server_ip' AND vla.status IN('QUEUE') AND vla.extension LIKE \"R/%\" AND vla.uniqueid=vac.uniqueid AND vla.channel=vac.channel;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$vla_qh_ct=$sthA->rows;
		$w=0;
		while ($vla_qh_ct > $w)
			{
			@aryA = $sthA->fetchrow_array;
			$QHlive_agent_id[$w] =	"$aryA[0]";
			$QHlead_id[$w] =		"$aryA[1]";
			$QHuniqueid[$w] =		"$aryA[2]";
			$QHuser[$w] =			"$aryA[3]";
			$QHcall_type[$w] =		"$aryA[4]";
			$w++;
			}
		$sthA->finish();

		$w=0;
		while ($vla_qh_ct > $w)
			{
			$stmtA = "UPDATE osdial_live_agents SET status='INCALL',last_call_time='$SQLdate',comments='REMOTE',calls_today='$calls_today' WHERE live_agent_id='$QHlive_agent_id[$w]';";
			$Aaffected_rows = $dbhA->do($stmtA);
			if ($Aaffected_rows > 0) {
				$event_string = "|     set agent to INCALL $Aaffected_rows";
				$event_string .= "\n$stmtA" if ($DBX);
				&event_logger;
			}

			if ($QHcall_type[$w] =~ /IN/)
				{
				$stmtC = "UPDATE osdial_closer_log SET status='XFER',user='$QHuser[$w]',comments='REMOTE' WHERE uniqueid='$QHuniqueid[$w]' AND status NOT LIKE 'V%';";
				$Caffected_rows = $dbhA->do($stmtC);
				}
			else
				{
				$stmtC = "UPDATE osdial_log SET status='XFER',user='$QHuser[$w]',comments='REMOTE' WHERE uniqueid='$QHuniqueid[$w]' AND status NOT LIKE 'V%';";
				$Caffected_rows = $dbhA->do($stmtC);
				}

			if ($Caffected_rows > 0) {
				$stmtB = "UPDATE osdial_list SET status='XFER',user='$QHuser[$w]' WHERE lead_id='$QHlead_id[$w]';";
				$Baffected_rows = $dbhA->do($stmtB);
			}

			$event_string = "|     QUEUEd listing UPDATEd |$Aaffected_rows|$Baffected_rows|$Caffected_rows|     |$QHlive_agent_id[$w]|$QHlead_id[$w]|$QHuniqueid[$w]|$QHuser[$w]|$QHcall_type[$w]|";
			 &event_logger;

			$w++;
			}

		$running_listen = 0;
                my $proctab = new Proc::ProcessTable;
                foreach my $proc (@{$proctab->table}) {
                        if ($proc->cmndline =~ /perl.*manager_listen/) {
                                $running_listen++;
                                print "LISTEN  RUNNING: |" . $proc->pid . "]|\n" if ($DB);
                                last;
                        }
                }

		if (!$running_listen) 
			{
			$endless_loop=0;
			$one_day_interval=0;
			print "\nPROCESS KILLED NO LISTENER RUNNING... EXITING\n\n";
			}

		if($DB){print "checking to see if listener is dead |$running_listen|\n";}
		}



		$user_counter=0;
		$DELusers='';
		@DBuser_start=@MT;
		@DBuser_level=@MT;
		@DBremote_user=@MT;
		@DBremote_server_ip=@MT;
		@DBremote_campaign=@MT;
		@DBremote_conf_exten=@MT;
		@DBremote_closer=@MT;
		@DBremote_random=@MT;
		@loginexistsRANDOM=@MT;
		@loginexistsALL=@MT;
		@VD_user=@MT;
		@VD_extension=@MT;
		@VD_status=@MT;
		@VD_uniqueid=@MT;
		@VD_callerid=@MT;
		@VD_random=@MT;
		@autocallexists=@MT;
		@calllogfinished=@MT;
		@calllogfinished2=@MT;
		@closerlogfinished=@MT;

		# Update closer campaigns if ivr is set to allow inbound.
		$stmtA = "UPDATE osdial_remote_agents AS ra,osdial_campaigns AS c,osdial_ivr AS i SET ra.closer_campaigns=c.closer_campaigns WHERE ra.campaign_id=c.campaign_id AND c.campaign_id=i.campaign_id AND i.allow_inbound='Y' AND ra.user_start LIKE 'va%';";
		$affected_rows = $dbhA->do($stmtA);
		print "|$affected_rows|$stmtA\n" if ($DBX);
		$stmtA = "UPDATE osdial_remote_agents AS ra,osdial_campaigns AS c,osdial_ivr AS i SET ra.closer_campaigns='' WHERE ra.campaign_id=c.campaign_id AND c.campaign_id=i.campaign_id AND i.allow_inbound='N' AND ra.user_start LIKE 'va%';";
		$affected_rows = $dbhA->do($stmtA);
		print "|$affected_rows|$stmtA\n" if ($DBX);

		$stmtA = "UPDATE osdial_users AS u,osdial_remote_agents AS ra SET u.closer_campaigns=ra.closer_campaigns WHERE u.user=ra.user_start AND ra.user_start LIKE 'va%';";
		$affected_rows = $dbhA->do($stmtA);
		print "|$affected_rows|$stmtA\n" if ($DBX);
		$stmtA = "UPDATE osdial_users AS u,osdial_live_agents AS la SET la.closer_campaigns=u.closer_campaigns WHERE u.user=la.user AND la.user LIKE 'va%';";
		$affected_rows = $dbhA->do($stmtA);
		print "|$affected_rows|$stmtA\n" if ($DBX);

	###############################################################################
	###### first grab all of the ACTIVE remote agents information from the database
	###############################################################################
		$stmtA = "SELECT * FROM osdial_remote_agents WHERE status IN('ACTIVE') AND server_ip='$server_ip' ORDER BY user_start;";
		if ($DBX) {print STDERR "Find all ACTIVE agents|$stmtA|\n";}
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$TESTrun=0;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
				$user_start =				"$aryA[1]";
				$number_of_lines =			"$aryA[2]";
				$conf_exten =				"$aryA[4]";
				$campaign_id =				"$aryA[6]";
				$closer_campaigns =			"$aryA[7]";
				if ($user_start =~ s/^va$campaign_id//) {
					$upad = (length($user_start) + length($campaign_id)) - (length(($user_start * 1)) + length($campaign_id));
					$va = 'va' . $campaign_id . sprintf('%0' . $upad . 'd',0);
				} else {
					$va = '';
				}

				$y=0;
				while ($y < $number_of_lines)
					{
					$random = int( rand(9999999)) + 10000000;
					$user_id = ($user_start + $y);
					$DBuser_start[$user_counter] =			"$va$user_start";
					$DBremote_user[$user_counter] =			"$va$user_id";
					$DBremote_server_ip[$user_counter] =	"$server_ip";
					$DBremote_campaign[$user_counter] =		"$campaign_id";
					$DBremote_conf_exten[$user_counter] =	"$conf_exten";
						if ($conf_exten =~ /999999999999/)
							{
							$TESTrun++;
							$DBremote_conf_exten[$user_counter] = ($user_counter + 8600051);
							}
					$DBremote_closer[$user_counter] =		"$closer_campaigns";
					$DBremote_random[$user_counter] =		"$random";
					
					$y++;
					$user_counter++;
					}
				
			$rec_count++;
			}
		$sthA->finish();
		if ($DB) {print STDERR "$user_counter live remote agents ACTIVE\n";}
   


	###############################################################################
	###### second grab all of the INACTIVE remote agents information from the database
	###############################################################################
		$stmtA = "SELECT * FROM osdial_remote_agents WHERE status IN('INACTIVE') AND server_ip='$server_ip' ORDER BY user_start;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
				$Duser_start =				"$aryA[1]";
				$Dnumber_of_lines =			"$aryA[2]";
				$Dcampaign_id =				"$aryA[6]";
				if ($Duser_start =~ s/^va$Dcampaign_id//) {
					$Dupad = (length($Duser_start) + length($Dcampaign_id)) - (length(($Duser_start * 1)) + length($Dcampaign_id));
					$Dva = 'va' . $Dcampaign_id . sprintf('%0' . $Dupad . 'd',0);
				} else {
					$Dva = '';
				}
				$w=0;
				while ($w < $Dnumber_of_lines)
					{
					$Duser_id = ($Duser_start + $w);
					$DELusers .= "R/$Dva$Duser_id|";
					$w++;
					}
			$rec_count++;
			}
		$sthA->finish();
		if ($DBX) {print STDERR "INACTIVE remote agents: |$DELusers|\n";}



	###############################################################################
	###### third traverse array of remote agents to be active and insert or update 
	###### in osdial_live_agents table 
	###############################################################################
		$h=0;
		foreach(@DBremote_user) 
			{
			if ($DBX) {print STDERR "osdial_live_agent check $DBremote_user[$h]|\n";}
			if (length($DBremote_user[$h])>1 and $DBremote_server_ip[$h] eq $server_ip) 
				{
				### check to see if the record exists and only needs random number update
				$stmtA = "SELECT SQL_NO_CACHE count(*) FROM osdial_live_agents WHERE user='$DBremote_user[$h]' AND server_ip='$server_ip' AND campaign_id='$DBremote_campaign[$h]' AND conf_exten='$DBremote_conf_exten[$h]';";
					if ($DBX) {print STDERR "|$stmtA|\n";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$loginexistsRANDOM[$h] =	"$aryA[0]";
					}
				$sthA->finish();
				
				&get_time_now;
				if ($loginexistsRANDOM[$h] > 0)
					{
					$stmtA = "UPDATE osdial_live_agents SET random_id='$DBremote_random[$h]',last_update_time='$FDtsSQLdate' WHERE user='$DBremote_user[$h]' AND server_ip='$server_ip' AND campaign_id='$DBremote_campaign[$h]' AND conf_exten='$DBremote_conf_exten[$h]';";
					$affected_rows = $dbhA->do($stmtA);
					if ($affected_rows > 0 and $DBX) {
						$event_string = "|     $DBremote_user[$h] $DBremote_campaign[$h] ONLY RANDOM ID UPDATE: $affected_rows";
						$event_string .= "\n$stmtA";
						&event_logger;
					}
					}
				### check if record for user on server exists at all in osdial_live_agents
				else
					{
					$stmtA = "SELECT SQL_NO_CACHE count(*) FROM osdial_live_agents WHERE user='$DBremote_user[$h]' AND server_ip='$server_ip';";
						if ($DBX) {print STDERR "|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					if ($sthArows > 0)
						{
						@aryA = $sthA->fetchrow_array;
						$loginexistsALL[$h] =	"$aryA[0]";
						}
					$sthA->finish();

					if ($loginexistsALL[$h] > 0)
						{
						$stmtA = "UPDATE osdial_live_agents SET random_id='$DBremote_random[$h]',last_update_time='$FDtsSQLdate',campaign_id='$DBremote_campaign[$h]',conf_exten='$DBremote_conf_exten[$h]',closer_campaigns='$DBremote_closer[$h]',status='READY' WHERE user='$DBremote_user[$h]' AND server_ip='$server_ip';";
						$affected_rows = $dbhA->do($stmtA);
						if ($affected_rows > 0 and $DBX) {
							$event_string = "|     $DBremote_user[$h] ALL UPDATE: $affected_rows";
							$event_string .= "\n$stmtA";
							&event_logger;
						}
			#			if ($affected_rows>0) 
			#				{
			#				if ($enable_queuemetrics_logging > 0)
			#					{
			#					$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
			#					 or die "Couldn't connect to database: " . DBI->errstr;

			#					if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

			#					$stmtB = "INSERT INTO queue_log SET partition='P001',time_id='$secX',call_id='NONE',queue='$DBremote_campaign[$h]',agent='Agent/$DBremote_user[$h]',verb='UNPAUSE',serverid='1';";
			#					$Baffected_rows = $dbhB->do($stmtB);

			#					$dbhB->disconnect();
			#					}

			#				}
						}
					### no records exist so insert a new one
					else
						{
						# grab the user_level of the agent
						$DBuser_level[$h]='7';
						$stmtA = "SELECT user_level FROM osdial_users WHERE user='$DBuser_start[$h]';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$DBuser_level[$h] =	"$aryA[0]";
							$rec_count++;
							}
						$sthA->finish();

						$stmtA = "INSERT INTO osdial_live_agents (user,server_ip,conf_exten,extension,status,campaign_id,random_id,last_call_time,last_update_time,last_call_finish,closer_campaigns,channel,uniqueid,callerid,user_level,comments) values('$DBremote_user[$h]','$server_ip','$DBremote_conf_exten[$h]','R/$DBremote_user[$h]','READY','$DBremote_campaign[$h]','$DBremote_random[$h]','$SQLdate','$FDtsSQLdate','$SQLdate','$DBremote_closer[$h]','','','','$DBuser_level[$h]','REMOTE');";
						$affected_rows = $dbhA->do($stmtA);
						if ($affected_rows > 0) {
							$event_string = "|     $DBremote_user[$h] NEW INSERT: $affected_rows";
							$event_string .= "\n$stmtA" if ($DBX);
							&event_logger;
						}
						if ($TESTrun > 0)
							{
							$stmtA = "SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE extension LIKE \"%999999999999\" AND server_ip='$server_ip';";
							$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
							$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
							$sthArows=$sthA->rows;
							if ($sthArows > 0)
								{
								@aryA = $sthA->fetchrow_array;
								$LSC_count =	"$aryA[0]";
								}
							$sthA->finish();

							if ($number_of_lines > $LSC_count)
								{
								$SIqueryCID = "T$CIDdate$DBremote_conf_exten[$h]";
								$stmtA="INSERT INTO osdial_manager values('','','$SQLdate','NEW','N','$server_ip','','Originate','$SIqueryCID','Channel: $local_DEF$DBremote_conf_exten[$h]$local_AMP$ext_context','Context: $ext_context','Exten: 999999999999','Priority: 1','Callerid: $SIqueryCID','Account: $SIqueryCID','','','','');";
								$affected_rows = $dbhA->do($stmtA);
								if ($affected_rows > 0) {
									$event_string = "|     TESTrun CALL PLACED: 999999999999 $DBremote_conf_exten[$h] $DBremote_user[$h] NEW INSERT: |$affected_rows|";
									$event_string .= "\n$stmtA" if ($DBX);
									&event_logger;
								}
								}
							else {print STDERR "Agent test calls already adequate $number_of_lines !> $LSC_count\n";}
							}
						if ($affected_rows>0) 
							{
							if ($enable_queuemetrics_logging > 0)
								{
								$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
								 or die "Couldn't connect to database: " . DBI->errstr;

								if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

								$stmtB = "INSERT INTO queue_log SET partition='P001',time_id='$secX',call_id='NONE',queue='$DBremote_campaign[$h]',agent='Agent/$DBremote_user[$h]',verb='AGENTLOGIN',data1='$DBremote_user[$h]$agents',serverid='$queuemetrics_log_id';";
								$Baffected_rows = $dbhB->do($stmtB);

								$stmtB = "INSERT INTO queue_log SET partition='P001',time_id='$secX',call_id='NONE',queue='$DBremote_campaign[$h]',agent='Agent/$DBremote_user[$h]',verb='UNPAUSE',serverid='$queuemetrics_log_id';";
								$Baffected_rows = $dbhB->do($stmtB);

								$dbhB->disconnect();
								}
							}
						}
					}
				}
			$h++;
			}


	###############################################################################
	###### fourth validate that the calls that the osdial_live_agents are on are not dead
	###### and if they are wipe out the values and set the agent record back to READY
	###############################################################################
		$stmtA = "UPDATE osdial_live_agents SET status='PAUSED' WHERE extension LIKE 'R/\%' AND server_ip='$server_ip' AND lead_id='0' AND uniqueid='' AND callerid='' AND status='INCALL';";
		$affected_rows = $dbhA->do($stmtA);
		if ($affected_rows > 0 and $DBX) {
			$event_string = "|     $VD_user[$z] CALL WIPE UPDATE: $affected_rows|PAUSED|$VD_uniqueid[$z]|$VD_user[$z]|";
			$event_string .= "\n$stmtA";
			&event_logger;
		}

		$stmtA = "SELECT SQL_NO_CACHE user,extension,status,uniqueid,callerid,lead_id,campaign_id FROM osdial_live_agents WHERE extension LIKE \"R/%\" AND server_ip='$server_ip' AND uniqueid>10;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
			$z=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$VDuser =				"$aryA[0]";
			$VDextension =			"$aryA[1]";
			$VDstatus =				"$aryA[2]";
			$VDuniqueid =			"$aryA[3]";
			$VDcallerid =			"$aryA[4]";
			$VDlead_id =			"$aryA[5]";
			$VDcampaign_id =		"$aryA[6]";
			$VDrandom = int( rand(9999999)) + 10000000;

			$VD_user[$z] =			"$VDuser";
			$VD_extension[$z] =		"$VDextension";
			$VD_status[$z] =		"$VDstatus";
			$VD_uniqueid[$z] =		"$VDuniqueid";
			$VD_callerid[$z] =		"$VDcallerid";
			$VD_lead_id[$z] =		"$VDlead_id";
			$VD_campaign_id[$z] =	"$VDcampaign_id";
			$VD_random[$z] =		"$VDrandom";

			$z++;				
			$rec_count++;
			}
		$sthA->finish();
		if ($DB) {print STDERR "$z remote agents on calls\n";}

		$z=0;
		foreach(@VD_user) 
			{
			$stmtA = "SELECT SQL_NO_CACHE count(*) FROM osdial_auto_calls WHERE uniqueid='$VD_uniqueid[$z]' AND server_ip='$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$autocallexists[$z] =	"$aryA[0]";
				}
			$sthA->finish();
			
			if ($autocallexists[$z] < 1)
				{
				if ($DELusers =~ /R\/$VD_user[$z]\|/)
					{
					$stmtA = "UPDATE osdial_live_agents SET random_id='$VD_random[$z]',status='PAUSED',last_call_finish='$SQLdate',lead_id='',uniqueid='',callerid='',channel='' WHERE user='$VD_user[$z]' AND server_ip='$server_ip';";
					$affected_rows = $dbhA->do($stmtA);
					if ($affected_rows > 0 and $DBX) {
						$event_string = "|     $VD_user[$z] CALL WIPE UPDATE: $affected_rows|PAUSED|$VD_uniqueid[$z]|$VD_user[$z]|";
						$event_string .= "\n$stmtA";
						&event_logger;
					}
					if ($affected_rows>0) 
						{
						if ($enable_queuemetrics_logging > 0)
							{
							$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
							 or die "Couldn't connect to database: " . DBI->errstr;

							if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

							$stmtB = "INSERT INTO queue_log SET partition='P001',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$VD_user[$z]',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
							$Baffected_rows = $dbhB->do($stmtB);

							$stmtB = "SELECT time_id FROM queue_log WHERE agent='Agent/$VD_user[$z]' AND verb='AGENTLOGIN' ORDER BY time_id DESC LIMIT 1;";
							$sthB = $dbhB->prepare($stmtB) or die "preparing: ",$dbhB->errstr;
							$sthB->execute or die "executing: $stmtB ", $dbhB->errstr;
							$sthBrows=$sthB->rows;
							$rec_count=0;
							while ($sthBrows > $rec_count)
								{
								@aryB = $sthB->fetchrow_array;
								$logintime =	"$aryB[0]";
								$rec_count++;
								}
							$sthB->finish();
							if ($DBX) {print "LOGOFF TIME LOG:  $logintime|$secX|$stmtB|\n";}

							$time_logged_in = ($secX - $logintime);
							if ($time_logged_in > 1000000) {$time_logged_in=1;}

							$stmtB = "INSERT INTO queue_log SET partition='P001',time_id='$secX',call_id='NONE',queue='$VD_campaign_id[$z]',agent='Agent/$VD_user[$z]',verb='AGENTLOGOFF',data1='$VD_user[$z]$agents',data2='$time_logged_in',serverid='$queuemetrics_log_id';";
							$Baffected_rows = $dbhB->do($stmtB);

							$dbhB->disconnect();
							}

						}
					}
				else
					{
					$stmtA = "SELECT SQL_NO_CACHE count(*) FROM call_log WHERE caller_code='$VD_callerid[$z]' AND channel NOT LIKE 'Local%' AND end_epoch>10;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$calllogfinished[$z] =	"$aryA[0]";
						$rec_count++;
						}
					$sthA->finish();
					$stmtA = "SELECT SQL_NO_CACHE count(*) FROM call_log WHERE caller_code='$VD_callerid[$z]' AND channel LIKE 'Local%' AND end_epoch>10;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$calllogfinished2[$z] =	"$aryA[0]";
						$rec_count++;
						}
					$sthA->finish();
					if ($calllogfinished[$z] == 0)
						{
						$stmtA = "SELECT SQL_NO_CACHE count(*) FROM osdial_closer_log AS cl,osdial_live_agents AS la WHERE cl.callerid='$VD_callerid[$z]' AND cl.callerid=la.callerid AND server_ip='$server_ip' AND end_epoch>10;";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$closerlogfinished[$z] =	"$aryA[0]";
							$rec_count++;
							}
						$sthA->finish();
						}
					if ($calllogfinished[$z] > 0 or $calllogfinished2[$z] > 1 or $closerlogfinished[$z] > 0)
						{
						$stmtA = "SELECT SQL_NO_CACHE channel FROM osdial_live_agents WHERE callerid='$VD_callerid[$z]';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						@aryA = $sthA->fetchrow_array;
						if ($aryA[0]) {
							$stmtA = "INSERT INTO osdial_manager values('','','$SQLdate','NEW','N','$VARserver_ip','','Hangup','$VD_callerid[$z]','Channel: $aryA[0]','','','','','','','','','')";
							$affected_rows = $dbhA->do($stmtA);
							if ($affected_rows > 0 and $DBX) {
								$event_string = "|     $VD_user[$z] CALL HANGUP SENT: $affected_rows|READY|$VD_uniqueid[$z]|$VD_user[$z]|";
								$event_string .= "\n$stmtA";
								&event_logger;
							}
						}

						$stmtA = "UPDATE osdial_live_agents SET random_id='$VD_random[$z]',last_call_finish='$SQLdate',lead_id='',uniqueid='',callerid='',channel='' WHERE user='$VD_user[$z]' AND server_ip='$server_ip';";
						$affected_rows = $dbhA->do($stmtA);
						$stmtA = "UPDATE osdial_live_agents SET status='READY' WHERE user='$VD_user[$z]' AND server_ip='$server_ip';";
						my $Baffected_rows = $dbhA->do($stmtA);
						if (($affected_rows > 0 or $Baffected_rows > 0) and $DBX) {
							$event_string = "|     $VD_user[$z] CALL WIPE UPDATE: $affected_rows/$Baffected_rows|READY|$VD_uniqueid[$z]|$VD_user[$z]|";
							$event_string .= "\n$stmtA" if ($DBX);
							&event_logger;
						}

						if ($affected_rows>0) 
							{
							if ($enable_queuemetrics_logging > 0)
								{
								$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
							 	or die "Couldn't connect to database: " . DBI->errstr;
	
								if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}
	
								$stmtB = "INSERT INTO queue_log SET partition='P001',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$VD_user[$z]',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
								$Baffected_rows = $dbhB->do($stmtB);
	
								$stmtB = "INSERT INTO queue_log SET partition='P001',time_id='$secX',call_id='NONE',queue='NONE',agent='Agent/$VD_user[$z]',verb='UNPAUSEALL',serverid='$queuemetrics_log_id';";
								$Baffected_rows = $dbhB->do($stmtB);
	
								$dbhB->disconnect();
								}

							}
						}
					}
				}
	### possible future active call checker
	#		else
	#			{
	#			$stmtA = "SELECT SQL_NO_CACHE count(*) FROM call_log WHERE caller_code='$VD_callerid[$z]' AND server_ip='$server_ip' AND end_epoch>10;";
	#			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	#			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	#			$sthArows=$sthA->rows;
	#			$rec_count=0;
	#			while ($sthArows > $rec_count)
	#				{
	#				@aryA = $sthA->fetchrow_array;
	#				$calllogfinished[$z] =	"$aryA[0]";
	#				$rec_count++;
	#				}
	#			$sthA->finish();
	#			if ($calllogfinished[$z] > 1)
	#				{
	#				$stmtA = "UPDATE osdial_live_agents SET random_id='$VD_random[$z]',status='READY',last_call_finish='$SQLdate',lead_id='',uniqueid='',callerid='',channel='' WHERE user='$VD_user[$z]' AND server_ip='$server_ip';";
	#				$affected_rows = $dbhA->do($stmtA);
	#				if ($DB) {print STDERR "$VD_user[$z] AGENT READY UPDATE: $affected_rows|READY|$VD_uniqueid[$z]|$VD_callerid[$z]|$VD_user[$z]|\n";}
	#
	#				$stmtA = "DELETE FROM osdial_auto_calls WHERE callerid='$VD_callerid[$z]' AND server_ip='$server_ip';";
	#				$affected_rows = $dbhA->do($stmtA);
	#				if ($DB) {print STDERR "$VD_user[$z] VAC DELETE: $affected_rows|$VD_callerid[$z]|\n";}
	#				}
	#			}

			$z++;
			}






	###############################################################################
	###### last, wait for a little bit and repeat the loop
	###############################################################################

		### sleep for X seconds before beginning the loop again
		usleep(1*$loop_delay*1000);

	$endless_loop--;
		if($DB){print STDERR "\nloop counter: |$endless_loop|\n";}

		### putting a blank file called "VDAD.kill" in the directory will automatically safely kill this program
		if (-e "$PATHhome/VDAD.kill")
			{
			unlink("$PATHhome/VDAD.kill");
			$endless_loop=0;
			$one_day_interval=0;
			print "\nPROCESS KILLED MANUALLY... EXITING\n\n"
			}

		$bad_grabber_counter=0;


	}


		if($DB){print "DONE... Exiting... Goodbye... See you later... Not really, initiating next loop...\n";}

		$event_string='HANGING UP|';
		&event_logger;

	$one_day_interval--;

}

		$event_string='CLOSING DB CONNECTION|';
		&event_logger;


$dbhA->disconnect();


	if($DB){print "DONE... Exiting... Goodbye... See you later... Really I mean it this time\n";}


exit;













sub get_time_now	#get the current date and time and epoch for logging call lengths and datetimes
{
$secX = time();
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($secX);
	$LOCAL_GMT_OFF = $SERVER_GMT;
	$LOCAL_GMT_OFF_STD = $SERVER_GMT;
	if ($isdst) {$LOCAL_GMT_OFF++;} 

$GMT_now = ($secX - ($LOCAL_GMT_OFF * 3600));
	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($GMT_now);
	if ($hour < 10) {$hour = "0$hour";}
	if ($min < 10) {$min = "0$min";}

	if ($DBX) {print "TIME DEBUG: $LOCAL_GMT_OFF_STD|$LOCAL_GMT_OFF|$isdst|   GMT: $hour:$min\n";}

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
	$CIDdate = "$mon$mday$hour$min$sec";
	$tsSQLdate = "$year$mon$mday$hour$min$sec";
	$SQLdate = "$year-$mon-$mday $hour:$min:$sec";
	$filedate = "$year-$mon-$mday";

$FDtarget = ($secX + 60);
($Fsec,$Fmin,$Fhour,$Fmday,$Fmon,$Fyear,$Fwday,$Fyday,$Fisdst) = localtime($FDtarget);
$Fyear = ($Fyear + 1900);
$Fmon++;
if ($Fmon < 10) {$Fmon = "0$Fmon";}
if ($Fmday < 10) {$Fmday = "0$Fmday";}
if ($Fhour < 10) {$Fhour = "0$Fhour";}
if ($Fmin < 10) {$Fmin = "0$Fmin";}
if ($Fsec < 10) {$Fsec = "0$Fsec";}
	$FDtsSQLdate = "$Fyear$Fmon$Fmday$Fhour$Fmin$Fsec";

$BDtarget = ($secX - 10);
($Bsec,$Bmin,$Bhour,$Bmday,$Bmon,$Byear,$Bwday,$Byday,$Bisdst) = localtime($BDtarget);
$Byear = ($Byear + 1900);
$Bmon++;
if ($Bmon < 10) {$Bmon = "0$Bmon";}
if ($Bmday < 10) {$Bmday = "0$Bmday";}
if ($Bhour < 10) {$Bhour = "0$Bhour";}
if ($Bmin < 10) {$Bmin = "0$Bmin";}
if ($Bsec < 10) {$Bsec = "0$Bsec";}
	$BDtsSQLdate = "$Byear$Bmon$Bmday$Bhour$Bmin$Bsec";

$PDtarget = ($secX - 30);
($Psec,$Pmin,$Phour,$Pmday,$Pmon,$Pyear,$Pwday,$Pyday,$Pisdst) = localtime($PDtarget);
$Pyear = ($Pyear + 1900);
$Pmon++;
if ($Pmon < 10) {$Pmon = "0$Pmon";}
if ($Pmday < 10) {$Pmday = "0$Pmday";}
if ($Phour < 10) {$Phour = "0$Phour";}
if ($Pmin < 10) {$Pmin = "0$Pmin";}
if ($Psec < 10) {$Psec = "0$Psec";}
	$PDtsSQLdate = "$Pyear$Pmon$Pmday$Phour$Pmin$Psec";

$XDtarget = ($secX - 120);
($Xsec,$Xmin,$Xhour,$Xmday,$Xmon,$Xyear,$Xwday,$Xyday,$Xisdst) = localtime($XDtarget);
$Xyear = ($Xyear + 1900);
$Xmon++;
if ($Xmon < 10) {$Xmon = "0$Xmon";}
if ($Xmday < 10) {$Xmday = "0$Xmday";}
if ($Xhour < 10) {$Xhour = "0$Xhour";}
if ($Xmin < 10) {$Xmin = "0$Xmin";}
if ($Xsec < 10) {$Xsec = "0$Xsec";}
	$XDSQLdate = "$Xyear-$Xmon-$Xmday $Xhour:$Xmin:$Xsec";

$TDtarget = ($secX - 600);
($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($TDtarget);
$Tyear = ($Tyear + 1900);
$Tmon++;
if ($Tmon < 10) {$Tmon = "0$Tmon";}
if ($Tmday < 10) {$Tmday = "0$Tmday";}
if ($Thour < 10) {$Thour = "0$Thour";}
if ($Tmin < 10) {$Tmin = "0$Tmin";}
if ($Tsec < 10) {$Tsec = "0$Tsec";}
	$TDSQLdate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";

}





sub event_logger
{
if ($DB) {print "$now_date|$event_string|\n";}
if ($SYSLOG)
	{
	### open the log file for writing ###
	open(Lout, ">>$VDRLOGfile")
			|| die "Can't open $VDRLOGfile: $!\n";
	print Lout "$now_date|$event_string|\n";
	close(Lout);
	}
$event_string='';
}
