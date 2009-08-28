#!/usr/bin/perl
#
# AST_DB_optimize.pl version 0.2   *DBI-version*
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
# DESCRIPTION:
# optimizes the tables used in the asterisk MySQL database
#
# It is recommended that you run this program on the local Asterisk machine
#
# CHANGES
# 60717-1242 - changed to DBI by Marin Blu
# 60718-1645 - changed to use /etc/osdial.conf for configs
# 71030-2020 - Added deletions of stats and inbound live agents
# 71109-1725 - fixed osdial_campaign_stats bug
# 71215-0410 - fixed UPDATE/DELETE results
#
# 090511-2005 - Remove optimizations for log tables.
# 090511-2007 - Set status_category_hour_counts to 0.

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

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


	#$stmtA = "optimize table call_log;";
	#	if($DB){print STDERR "\n|$stmtA|\n";}
	#	if (!$T) {
	#				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   	#				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   	#				$sthArows=$sthA->rows;
	#				 @aryA = $sthA->fetchrow_array;
   	#				 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	#				$sthA->finish();
	#			 }


	#$stmtA = "optimize table park_log;";
	#	if($DB){print STDERR "\n|$stmtA|\n";}
	#	if (!$T) {
	#				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   	#				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   	#				$sthArows=$sthA->rows;
	#				 @aryA = $sthA->fetchrow_array;
   	#				 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	#				$sthA->finish();
	#			 }


	#$stmtA = "optimize table osdial_log;";
	#	if($DB){print STDERR "\n|$stmtA|\n";}
	#	if (!$T) {
	#				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   	#				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   	#				$sthArows=$sthA->rows;
	#				 @aryA = $sthA->fetchrow_array;
   	#				 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	#				$sthA->finish();
	#			 }


	#$stmtA = "optimize table osdial_closer_log;";
	#	if($DB){print STDERR "\n|$stmtA|\n";}
	#	if (!$T) {
	#				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   	#				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   	#				$sthArows=$sthA->rows;
	#				 @aryA = $sthA->fetchrow_array;
   	#				 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	#				$sthA->finish();
	#			 }


	#$stmtA = "optimize table osdial_xfer_log;";
	#	if($DB){print STDERR "\n|$stmtA|\n";}
	#	if (!$T) {
	#				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   	#				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   	#				$sthArows=$sthA->rows;
	#				 @aryA = $sthA->fetchrow_array;
   	#				 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	#				$sthA->finish();
	#			 }

	#$stmtA = "optimize table osdial_list;";
	#	if($DB){print STDERR "\n|$stmtA|\n";}
	#	if (!$T) {
	#				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   	#				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   	#				$sthArows=$sthA->rows;
	#				 @aryA = $sthA->fetchrow_array;
   	#				 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	#				$sthA->finish();
	#			 }


	$stmtA = "optimize table osdial_manager;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }


	$stmtA = "optimize table osdial_auto_calls;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }


	$stmtA = "optimize table osdial_live_agents;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }

	$stmtA = "optimize table osdial_campaign_stats;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }

	$stmtA = "optimize table osdial_campaign_server_stats;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }

	$stmtA = "optimize table osdial_dnc;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }

	$stmtA = "optimize table osdial_callbacks;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }

	#$stmtA = "optimize table osdial_agent_log;";
	#	if($DB){print STDERR "\n|$stmtA|\n";}
	#	if (!$T) {
	#				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   	#				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   	#				$sthArows=$sthA->rows;
	#				 @aryA = $sthA->fetchrow_array;
   	#				 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
	#				$sthA->finish();
	#			 }

	$stmtA = "optimize table osdial_conferences;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }

	$stmtA = "optimize table osdial_hopper;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) {
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
   					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
   					$sthArows=$sthA->rows;
					 @aryA = $sthA->fetchrow_array;
   					 if (!$Q) {print "|",$aryA[0],"|",$aryA[1],"|",$aryA[2],"|",$aryA[3],"|","\n";}
					$sthA->finish();
				 }


	$stmtA = "UPDATE osdial_campaign_stats SET dialable_leads='0', calls_today='0', answers_today='0', drops_today='0', drops_today_pct='0', drops_answers_today_pct='0', calls_hour='0', answers_hour='0', drops_hour='0', drops_hour_pct='0', calls_halfhour='0', answers_halfhour='0', drops_halfhour='0', drops_halfhour_pct='0', calls_fivemin='0', answers_fivemin='0', drops_fivemin='0', drops_fivemin_pct='0', calls_onemin='0', answers_onemin='0', drops_onemin='0', drops_onemin_pct='0', differential_onemin='0', agents_average_onemin='0', balance_trunk_fill='0', status_category_count_1='0', status_category_count_2='0', status_category_count_3='0', status_category_count_4='0', status_category_hour_count_1='0', status_category_hour_count_2='0', status_category_hour_count_3='0', status_category_hour_count_4='0', recycle_total='0', recycle_sched='0', amd_onemin='0', failed_onemin='0', agents_paused='0', agents_incall='0', agents_waiting='0', waiting_calls='0';"
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) 
			{
			$affected_rows = $dbhA->do($stmtA);
			if(!$Q){print STDERR "\n|$affected_rows osdial_campaign_stats records reset|\n";}
			}

	$stmtA = "UPDATE osdial_campaign_server_stats SET local_trunk_shortage='0', calls_onemin='0', answers_onemin='0', drops_onemin='0', amd_onemin='0', failed_onemin='0';";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) 
			{
			$affected_rows = $dbhA->do($stmtA);
			if(!$Q){print STDERR "\n|$affected_rows osdial_campaign_server_stats records deleted|\n";}
			}

	$stmtA = "delete from osdial_live_inbound_agents;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) 
			{
			$affected_rows = $dbhA->do($stmtA);
			if(!$Q){print STDERR "\n|$affected_rows osdial_live_inbound_agents records deleted|\n";}
			}

	$stmtA = "update osdial_inbound_group_agents SET calls_today=0;;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) 
			{
			$affected_rows = $dbhA->do($stmtA);
			if(!$Q){print STDERR "\n|$affected_rows osdial_inbound_group_agents call counts reset|\n";}
			}

	$stmtA = "update osdial_campaign_agents SET calls_today=0;;";
		if($DB){print STDERR "\n|$stmtA|\n";}
		if (!$T) 
			{
			$affected_rows = $dbhA->do($stmtA);
			if(!$Q){print STDERR "\n|$affected_rows osdial_campaign_agents call counts reset|\n";}
			}

        $stmtA = "delete from osdial_campaign_agent_stats;";
                if($DB){print STDERR "\n|$stmtA|\n";}
                if (!$T)
                        {
                        $affected_rows = $dbhA->do($stmtA);
                        if(!$Q){print STDERR "\n|$affected_rows osdial_campaign_agent_stats reset|\n";}
                        }


		$dbhA->disconnect();


exit;






