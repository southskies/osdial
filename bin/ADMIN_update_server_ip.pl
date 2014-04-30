#!/usr/bin/perl

# ADMIN_update_server_ip.pl - updates IP address in DB and conf file
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
# This script is designed to update all database tables and the local 
# osdial.conf file to reflect a change in IP address. The script will 
# automatically default to the first eth address in the ifconfig output.
#
# CHANGELOG
# 71205-2144 - added display of extensions.conf example for call routing
# 80321-0220 - updated for new settings
#
#
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
	if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
		{$VARold_server_ip = $line;   $VARold_server_ip =~ s/.*=//gi;}
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

############################################

$CLIold_server_ip=0;
$CLIserver_ip=0;

$secX = time();

# constants
$DB=0;  # Debug flag, set to 0 for no debug messages, lots of output
$US='_';
@MT=();

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
	print "ADMIN_update_server_ip.pl - updates server_ip in the $VARDB_database\n";
	print "database and in the local /etc/osdial.conf file.\n";
	print "\n";
	print "command-line options:\n";
	print "  [--help] = this help screen\n";
	print "  [--debug] = verbose debug messages\n";
	print "  [--auto] = no prompts\n";
	print "configuration options:\n";
	print "  [--old-server_ip=192.168.0.1] = define old server IP address at runtime\n";
	print "  [--server_ip=192.168.0.2] = define new server IP address at runtime\n";
	print "  [--noprompt] = no prompts\n";
	print "\n";

	exit;
	}
	else
	{
		if ($args =~ /--debug/i) # Debug flag
		{
		$DB=1;
		}
		if ($args =~ /--auto/i) # no prompts flag
		{
		$AUTO=1;
		}
		if ($args =~ /--noprompt/i) # no prompts flag
		{
		$NOPROMPT=1;
		}
		if ($args =~ /--old-server_ip=/i) # CLI defined old server IP address
		{
		@CLIoldserver_ipARY = split(/--old-server_ip=/,$args);
		@CLIoldserver_ipARX = split(/ /,$CLIoldserver_ipARY[1]);
		if (length($CLIoldserver_ipARX[0])>2)
			{
			$VARold_server_ip = $CLIoldserver_ipARX[0];
			$VARold_server_ip =~ s/\/$| |\r|\n|\t//gi;
			$CLIold_server_ip=1;
			print "  CLI defined old server IP:  $VARold_server_ip\n";
			}
		}
		if ($args =~ /--server_ip=/i) # CLI defined server IP address
		{
		@CLIserver_ipARY = split(/--server_ip=/,$args);
		@CLIserver_ipARX = split(/ /,$CLIserver_ipARY[1]);
		if (length($CLIserver_ipARX[0])>2)
			{
			$VARserver_ip = $CLIserver_ipARX[0];
			$VARserver_ip =~ s/\/$| |\r|\n|\t//gi;
			$CLIserver_ip=1;
			print "  CLI defined server IP:      $VARserver_ip\n";
			}
		}
	}
}
else
{
#	print "no command line options set\n";
}
### end parsing run-time options ###

if (-e "$PATHconf") 
	{
	print "Previous OSDial configuration file found at: $PATHconf\n";
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
		if ( ($line =~ /^PATHDONEmonitor/) && ($CLIDONEmonitor < 1) )
			{$PATHDONEmonitor = $line;   $PATHDONEmonitor =~ s/.*=//gi;}
#		if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
#			{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
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
		if ( ($line =~ /^VARactive_keepalives/) && ($CLIactive_keepalives < 1) )
			{$VARactive_keepalives = $line;   $VARactive_keepalives =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_host/) && ($CLIFTP_host < 1) )
			{$VARFTP_host = $line;   $VARFTP_host =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_user/) && ($CLIFTP_user < 1) )
			{$VARFTP_user = $line;   $VARFTP_user =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_pass/) && ($CLIFTP_pass < 1) )
			{$VARFTP_pass = $line;   $VARFTP_pass =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_port/) && ($CLIFTP_port < 1) )
			{$VARFTP_port = $line;   $VARFTP_port =~ s/.*=//gi;}
		if ( ($line =~ /^VARFTP_dir/) && ($CLIFTP_dir < 1) )
			{$VARFTP_dir = $line;   $VARFTP_dir =~ s/.*=//gi;}
		if ( ($line =~ /^VARHTTP_path/) && ($CLIHTTP_path < 1) )
			{$VARHTTP_path = $line;   $VARHTTP_path =~ s/.*=//gi;}
		if ( ($line =~ /^VARREPORT_host/) && ($CLIREPORT_host < 1) )
			{$VARREPORT_host = $line;   $VARREPORT_host =~ s/.*=//gi;}
		if ( ($line =~ /^VARREPORT_user/) && ($CLIREPORT_user < 1) )
			{$VARREPORT_user = $line;   $VARREPORT_user =~ s/.*=//gi;}
		if ( ($line =~ /^VARREPORT_pass/) && ($CLIREPORT_pass < 1) )
			{$VARREPORT_pass = $line;   $VARREPORT_pass =~ s/.*=//gi;}
		if ( ($line =~ /^VARREPORT_port/) && ($CLIREPORT_port < 1) )
			{$VARREPORT_port = $line;   $VARREPORT_port =~ s/.*=//gi;}
		if ( ($line =~ /^VARREPORT_dir/) && ($CLIREPORT_dir < 1) )
			{$VARREPORT_dir = $line;   $VARREPORT_dir =~ s/.*=//gi;}
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
		$i++;
		}
	}

if ($AUTO)
	{
	$manual='n';
	@ip = `/sbin/ifconfig`;
	$j=0;
	while($#ip>=$j)
		{
		if ($ip[$j] =~ /inet addr/) {$VARserver_ip = $ip[$j]; $j=1000;}
		$j++;
		}
	$VARserver_ip =~ s/.*addr:| Bcast.*|\r|\n|\t| //gi;
	}
elsif ($NOPROMPT)
	{
	$manual='n';
	}
else
	{
	print("\nWould you like to use interactive mode (y/n): [y] ");
	$manual = <STDIN>;
	chomp($manual);
	}

if ($manual =~ /n/i)
	{
	$manual=0;
	}
else
	{
	$config_finished='NO';
	while ($config_finished =~/NO/)
		{
		print "\nSTARTING SERVER IP ADDRESS CHANGE FOR OSDIAL...\n";

		##### BEGIN old_server_ip propmting and check #####
		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nOld server IP address or press enter for default: [$VARold_server_ip] ");
			$PROMPTold_server_ip = <STDIN>;
			chomp($PROMPTold_server_ip);
			if (length($PROMPTold_server_ip)>6)
				{
				$PROMPTold_server_ip =~ s/ |\n|\r|\t|\/$//gi;
				$VARold_server_ip=$PROMPTold_server_ip;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END old_server_ip propmting and check  #####

		##### BEGIN server_ip propmting and check #####
		if (length($VARserver_ip)<7)
			{	
			### get best guess of IP address from ifconfig output ###
			# inet addr:10.10.11.17  Bcast:10.10.255.255  Mask:255.255.0.0
			@ip = `/sbin/ifconfig`;
			$j=0;
			while($#ip>=$j)
				{
				if ($ip[$j] =~ /inet addr/) {$VARserver_ip = $ip[$j]; $j=1000;}
				$j++;
				}
			$VARserver_ip =~ s/.*addr:| Bcast.*|\r|\n|\t| //gi;
			}

		$continue='NO';
		while ($continue =~/NO/)
			{
			print("\nserver IP address or press enter for default: [$VARserver_ip] ");
			$PROMPTserver_ip = <STDIN>;
			chomp($PROMPTserver_ip);
			if (length($PROMPTserver_ip)>6)
				{
				$PROMPTserver_ip =~ s/ |\n|\r|\t|\/$//gi;
				$VARserver_ip=$PROMPTserver_ip;
				$continue='YES';
				}
			else
				{
				$continue='YES';
				}
			}
		##### END server_ip propmting and check  #####



		print "\n";
		print "  old server_ip:      $VARold_server_ip\n";
		print "  new server_ip:      $VARserver_ip\n";
		print "\n";

		print("Are these settings correct?(y/n): [y] ");
		$PROMPTconfig = <STDIN>;
		chomp($PROMPTconfig);
		if ( (length($PROMPTconfig)<1) or ($PROMPTconfig =~ /y/i) )
			{
			$config_finished='YES';
			}
		}
	}

print "Writing change to osdial.conf file: $PATHconf\n";
$junk = `/usr/bin/perl -pi -e 's|^VARserver_ip => $VARold_server_ip|VARserver_ip => $VARserver_ip|' $PATHconf`;

print "\nSTARTING DATABASE TABLES UPDATES PHASE...\n";

if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
	or die "Couldn't connect to database: " . DBI->errstr;

print "  Updating call_log table...\n";
$stmtA = "UPDATE call_log SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating conferences table...\n";
$stmtA = "UPDATE conferences SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating inbound_numbers table...\n";
$stmtA = "UPDATE inbound_numbers SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating live_channels table...\n";
$stmtA = "UPDATE live_channels SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating live_inbound table...\n";
$stmtA = "UPDATE live_inbound SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating live_sip_channels table...\n";
$stmtA = "UPDATE live_sip_channels SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_agent_log table...\n";
$stmtA = "UPDATE osdial_agent_log SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_auto_calls table...\n";
$stmtA = "UPDATE osdial_auto_calls SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_campaign_server_stats table...\n";
$stmtA = "UPDATE osdial_campaign_server_stats SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_carrier_servers table...\n";
$stmtA = "UPDATE osdial_carrier_servers SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_closer_log table...\n";
$stmtA = "UPDATE osdial_closer_log SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_companies table...\n";
$stmtA = "UPDATE osdial_companies SET default_server_ip='$VARserver_ip' where default_server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_conferences table...\n";
$stmtA = "UPDATE osdial_conferences SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_cpa_log table...\n";
$stmtA = "UPDATE osdial_cpa_log SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_events table...\n";
$stmtA = "UPDATE osdial_events SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_live_agents table (server_ip)...\n";
$stmtA = "UPDATE osdial_live_agents SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_live_agents table (call_server_ip)...\n";
$stmtA = "UPDATE osdial_live_agents SET call_server_ip='$VARserver_ip' where call_server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_log table...\n";
$stmtA = "UPDATE osdial_log SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_manager table...\n";
$stmtA = "UPDATE osdial_manager SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_remote_agents table...\n";
$stmtA = "UPDATE osdial_remote_agents SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating osdial_server_trunks table...\n";
$stmtA = "UPDATE osdial_server_trunks SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating park_log table...\n";
$stmtA = "UPDATE park_log SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating parked_channels table...\n";
$stmtA = "UPDATE parked_channels SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating phones table...\n";
$stmtA = "UPDATE phones SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating recording_log table...\n";
$stmtA = "UPDATE recording_log SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating server_keepalive_processes table...\n";
$stmtA = "UPDATE server_keepalive_processes SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating server_performance table...\n";
$stmtA = "UPDATE server_performance SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating server_stats table...\n";
$stmtA = "UPDATE server_stats SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating server_updater table...\n";
$stmtA = "UPDATE server_updater SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating servers table...\n";
$stmtA = "UPDATE servers SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

print "  Updating web_client_sessions table...\n";
$stmtA = "UPDATE web_client_sessions SET server_ip='$VARserver_ip' where server_ip='$VARold_server_ip';";
$affected_rows = $dbhA->do($stmtA);
if ($DB) {print "     |$affected_rows|$stmtA|\n";}

$dbhA->disconnect();

# Now we need to kill off all the keep-alives, so they will pick up the active server_ip...
$pids = `ps -ef | grep -E '(SCREEN|FastAGI)' | grep -v 'sbin/asterisk' | awk '{ print \$2 }'`;
$pids =~ s/\n/ /g;
if ($pids) {
        $jumk = `kill -9 $pids > /dev/null 2>&1`;
}


print "\nSERVER IP ADDRESS CHANGE FOR OSDIAL FINISHED!\n";


$secy = time();		$secz = ($secy - $secX);		$minz = ($secz/60);		# calculate script runtime so far
print "\n     - process runtime      ($secz sec) ($minz minutes)\n";


exit;


sub leading_zero($) 
{
    $_ = $_[0];
    s/^(\d)$/0$1/;
    s/^(\d\d)$/0$1/;
    return $_;
} # End of the leading_zero() routine.

