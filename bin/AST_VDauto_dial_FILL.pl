#!/usr/bin/perl
#
# AST_VDauto_dial_FILL.pl version 2.0.3   *DBI-version*
#
# DESCRIPTION:
# Places auto_dial calls on the VICIDIAL dialer system across all servers only 
# for campaigns that have a shortfall in number of lines dialed.
#
# Script needs to be started by ADMIN_keepalive_ALL.pl script (option 7)
#
# Not for use in systems with only one Asterisk/VICIDIAL server
#
# Should only be run on one server in a multi-server Asterisk/VICIDIAL cluster
#
# Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# CHANGELOG:
# 61115-1246 - First build, framework setup, non-functional
# 61120-2008 - second alpha version, functional and tested in production
# 70205-1425 - Added code for last called date update
# 71030-2054 - Added hopper priority sorting
# 71111-2349 - fixed overdialing bug
# 


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
	print "allowed run time options:\n  [-t] = test\n  [-debug] = verbose debug messages\n  [--delay=XXX] = delay of XXX seconds per loop, default 2.5 seconds\n\n";
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
		$loop_delay = '2500';
		}
		if ($args =~ /-debug/i)
		{
		$DB=1; # Debug flag, set to 0 for no debug messages
		print "\n-- DEBUG --\n\n";
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
	$loop_delay = '2500';
	$DB=1;
}
### end parsing run-time options ###


# constants
$US='__';
$MT[0]='';
$RECcount=''; ### leave blank for no REC count
$RECprefix='7'; ### leave blank for no REC prefix


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

	&get_time_now;	# update time/date variables

if (!$VDADLOGfile) {$VDADLOGfile = "$PATHlogs/vdautodial_FILL.$year-$mon-$mday";}

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;
	
	### connect to MySQL database defined in the conf file
	$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
    or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT telnet_host,telnet_port,ASTmgrUSERNAME,ASTmgrSECRET,ASTmgrUSERNAMEupdate,ASTmgrUSERNAMElisten,ASTmgrUSERNAMEsend,max_vicidial_trunks,answer_transfer_agent,local_gmt,ext_context,vd_server_logs FROM servers where server_ip = '$server_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
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
		$DBvd_server_logs =			"$aryA[11]";
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
		if ($DBvd_server_logs =~ /Y/)	{$SYSLOG = '1';}
			else {$SYSLOG = '0';}
	 $rec_count++;
	}
$sthA->finish();

	$event_string='LOGGED INTO MYSQL SERVER ON 1 CONNECTION|';
	&event_logger;

$one_day_interval = 12;		# 1 month loops for one year 
while($one_day_interval > 0)
{

	$endless_loop=5760000;		# 30 days minutes at XXX seconds per loop
	$stat_count=1;

	while($endless_loop > 0)
	{
		&get_time_now;

		$VDADLOGfile = "$PATHlogs/vdautodial_FILL.$year-$mon-$mday";

	###############################################################################
	###### first figure out how many calls should be placed for each campaign per server
	###############################################################################
		@DBfill_campaign=@MT;
		@DBfill_shortage=@MT;
		@DBfill_tally=@MT;
		@DBfill_needed=@MT;
		@DBfill_current_balance=@MT;
		@DBlive_campaign=@MT;
		@DBlive_conf_exten=@MT;
		@DBlive_status=@MT;
		@DBcampaigns=@MT;
		@DBIPaddress=@MT;
		@DBIPcampaign=@MT;
		@DBIPactive=@MT;
		@DBIPvdadexten=@MT;
		@DBIPcount=@MT;
		@DBIPACTIVEcount=@MT;
		@DBIPINCALLcount=@MT;
		@DBIPadlevel=@MT;
		@DBIPdialtimeout=@MT;
		@DBIPdialprefix=@MT;
		@DBIPcampaigncid=@MT;
		@DBIPexistcalls=@MT;
		@DBIPgoalcalls=@MT;
		@DBIPmakecalls=@MT;
		@DBIPlivecalls=@MT;
		@DBIPclosercamp=@MT;
		@DBIPomitcode=@MT;
		@DBIPtrunk_shortage=@MT;
		@DBIPold_trunk_shortage=@MT;
		@DBIPserver_trunks_limit=@MT;
		@DBIPserver_trunks_other=@MT;
		@DBIPserver_trunks_allowed=@MT;

		$active_line_counter=0;
		$camp_counter=0;
		$total_shortage=0;
		$balance_servers=0;
		$lists_update = '';
		$LUcount=0;

		$stmtA = "SELECT count(*) FROM servers where vicidial_balance_active = 'Y';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$balance_servers =	"$aryA[0]";
			$rec_count++;
			}
		$sthA->finish();

		##### Get a listing of the campaigns that have shortages of trunks
		$stmtA = "SELECT campaign_id,sum(local_trunk_shortage) FROM vicidial_campaign_server_stats where update_time > '$XDSQLdate' and local_trunk_shortage > 0 group by campaign_id;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
			$DBfill_campaign[$camp_counter] =	"$aryA[0]";
			$DBfill_shortage[$camp_counter] =	"$aryA[1]";
			$total_shortage = ($total_shortage + $aryA[1]);
			if($DB) {print "$DBfill_campaign[$camp_counter]: $DBfill_shortage[$camp_counter]\n";}

			$camp_counter++;
			$rec_count++;
			}
		$sthA->finish();

		$event_string="SERVERS WITH TRUNK BALANCE: $balance_servers\n";
		$event_string.="CAMPAIGNS WITH TRUNK SHORTAGE: $camp_counter| TOTAL SHORTAGE: $total_shortage";
		&event_logger;


		##################################################################################
		##### START LOOP IF THERE ARE BALANCE SERVERS AND THERE ARE SHORTAGES
		##################################################################################
		if ( ($balance_servers > 0) && ($camp_counter > 0) )
			{
			$camp_CIPct = 0;
			foreach(@DBfill_campaign)
				{
				$camp_counter=0;
				$DB_balance_fill=0;
				$VAC_balance_fill=0;
				$AVAIL_balance_servers=0;
				$DBfill_tally[$camp_CIPct]=0;

				### grab the dial_level and multiply by active agents to get your goalcalls
				$DBIPadlevel[$camp_CIPct]=0;
				$stmtA = "SELECT dial_timeout,dial_prefix,campaign_cid,active,campaign_vdad_exten,omit_phone_code FROM vicidial_campaigns where campaign_id='$DBfill_campaign[$camp_CIPct]'";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				$active_only=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
						$DBIPdialtimeout[$camp_CIPct] =	"$aryA[0]";
						$DBIPdialprefix[$camp_CIPct] =	"$aryA[1]";
						$DBIPcampaigncid[$camp_CIPct] =	"$aryA[2]";
						$DBIPactive[$camp_CIPct] =		"$aryA[3]";
						$DBIPvdadexten[$camp_CIPct] =	"$aryA[4]";
						$omit_phone_code =				"$aryA[5]";
							if ($omit_phone_code =~ /Y/) {$DBIPomitcode[$camp_CIPct] = 1;}
							else {$DBIPomitcode[$camp_CIPct] = 0;}
					$rec_count++;
					}
				$sthA->finish();

				$stmtA = "SELECT balance_trunk_fill FROM vicidial_campaign_stats where campaign_id='$DBfill_campaign[$camp_CIPct]'";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$DB_balance_fill =	"$aryA[0]";
					$rec_count++;
					}
				$sthA->finish();

				$stmtA = "SELECT count(*) FROM vicidial_auto_calls where campaign_id='$DBfill_campaign[$camp_CIPct]' and call_type='OUTBALANCE';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$VAC_balance_fill =	"$aryA[0]";
					$rec_count++;
					}
				$sthA->finish();
				$DBfill_current_balance[$camp_CIPct] = "$VAC_balance_fill";

				$event_string="               CAMPAIGN: $DBfill_campaign[$camp_CIPct]\n";
				$event_string.="DB_balance_fill: $DB_balance_fill   VAC_balance_fill: $VAC_balance_fill\n";

				$DBfill_needed[$camp_CIPct] = ($DBfill_shortage[$camp_CIPct] - $VAC_balance_fill);
				$event_string.="Additional Balance Calls Needed For This Campaign: $DBfill_needed[$camp_CIPct]\n";


				##### Get a listing of the servers in the campaign that have shortages of trunks
				$full_servers='|';
				$full_serversSQL='';
				$stmtA = "SELECT server_ip FROM vicidial_campaign_server_stats where update_time > '$XDSQLdate' and local_trunk_shortage > 0 and campaign_id='$DBfill_campaign[$camp_CIPct]'";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$full_servers .=	"$aryA[0]|";
					$full_serversSQL .=	"'$aryA[0]',";
					$rec_count++;
					}
				$sthA->finish();
				chop($full_serversSQL);
				if (length($full_serversSQL)<6) {$full_serversSQL="''";}
				$event_string.="SERVERS WITH TRUNK FULL for $DBfill_campaign[$camp_CIPct]: $full_servers |$full_serversSQL|";
				&event_logger;

				##### Check if there are any balance-enabled servers outside of the ones with trunk shortage
				$stmtA = "SELECT count(*) FROM servers where vicidial_balance_active = 'Y' and server_ip NOT IN($full_serversSQL);";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$AVAIL_balance_servers =	"$aryA[0]";
					$rec_count++;
					}
				$sthA->finish();

				##################################################################################
				##### CONTINUE IF THERE ARE BALANCE SERVERS AVAILABLE THAT ARE NOT IN TRUNK SHORTAGE
				##################################################################################
				if ($AVAIL_balance_servers > 0)
					{
					$event_string="Balance Servers available: $AVAIL_balance_servers";
					&event_logger;

					$DB_camp_servers=0;
					@DB_camp_server_server_ip=@MT;
					@DB_camp_server_max_vicidial_trunks=@MT;
					@DB_camp_server_balance_trunks_offlimits=@MT;
					@DB_camp_server_dedicated_trunks=@MT;
					@DB_camp_server_trunk_restriction=@MT;
					@DB_NONcamp_server_dedicated_trunks=@MT;
					@DB_camp_server_available=@MT;
					@DB_camp_server_trunks_to_dial=@MT;
					##### Get the trunk settings for the campaign across all servers
					$stmtA = "SELECT server_ip,max_vicidial_trunks,balance_trunks_offlimits FROM servers where vicidial_balance_active = 'Y' and server_ip NOT IN($full_serversSQL) order by server_ip;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$DB_camp_server_server_ip[$DB_camp_servers] =					"$aryA[0]";
						$DB_camp_server_max_vicidial_trunks[$DB_camp_servers] =			"$aryA[1]";
						$DB_camp_server_balance_trunks_offlimits[$DB_camp_servers] =	"$aryA[2]";
						$DB_camp_servers++;
						$rec_count++;
						}
					$sthA->finish();


					##################################################################################
					##### LOOP THROUGH SERVERS, CALCULATE TRUNKS FOR EACH FOR THIS CAMPAIGN
					##################################################################################
					$server_CIPct = 0;
					foreach(@DB_camp_server_server_ip)
						{
						$DB_camp_server_dedicated_trunks[$server_CIPct]=0;
						$DB_camp_server_trunk_restriction[$server_CIPct]=0;
						$DB_NONcamp_server_dedicated_trunks[$server_CIPct]=0;
						##### Get the campaign-specific trunk settings for the campaign on this server
						$stmtA = "SELECT dedicated_trunks,trunk_restriction FROM vicidial_server_trunks where server_ip='$DB_camp_server_server_ip[$server_CIPct]' and campaign_id='$DBfill_campaign[$camp_CIPct]';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$DB_camp_server_dedicated_trunks[$server_CIPct] =	"$aryA[0]";
							$DB_camp_server_trunk_restriction[$server_CIPct] =	"$aryA[1]";
							$rec_count++;
							}
						$sthA->finish();

						##### Get the campaign-specific dedicated trunks count for other campaigns on this server
						$stmtA = "SELECT sum(dedicated_trunks) FROM vicidial_server_trunks where server_ip='$DB_camp_server_server_ip[$server_CIPct]' and campaign_id NOT IN('$DBfill_campaign[$camp_CIPct]');";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$DB_NONcamp_server_dedicated_trunks[$server_CIPct] =	"$aryA[0]";
							$rec_count++;
							}
						$sthA->finish();

						$VAC_server_camp=0;
						$VAC_server_NONcamp=0;

						$stmtA = "SELECT count(*) FROM vicidial_auto_calls where server_ip='$DB_camp_server_server_ip[$server_CIPct]' and campaign_id='$DBfill_campaign[$camp_CIPct]' and call_type='OUTBALANCE';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$VAC_server_BALcamp =	"$aryA[0]";
							$rec_count++;
							}
						$sthA->finish();

						$stmtA = "SELECT count(*) FROM vicidial_auto_calls where server_ip='$DB_camp_server_server_ip[$server_CIPct]' and campaign_id='$DBfill_campaign[$camp_CIPct]';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$VAC_server_camp =	"$aryA[0]";
							$rec_count++;
							}
						$sthA->finish();

						$stmtA = "SELECT count(*) FROM vicidial_auto_calls where server_ip='$DB_camp_server_server_ip[$server_CIPct]' and campaign_id!='$DBfill_campaign[$camp_CIPct]';";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$VAC_server_NONcamp =	"$aryA[0]";
							$rec_count++;
							}
						$sthA->finish();

						if($DB) {print "VAC CALLS: |$VAC_server_camp|$VAC_server_NONcamp|$VAC_server_BALcamp|\n";}
						if($DB) {print "SETTINGS:  |$DB_camp_server_dedicated_trunks[$server_CIPct]|$DB_camp_server_max_vicidial_trunks[$server_CIPct]|$DB_camp_server_balance_trunks_offlimits[$server_CIPct]||\n";}

						if ($DB_camp_server_trunk_restriction[$server_CIPct] =~ /MAXIMUM_LIMIT/)
							{
							$DB_camp_server_available[$server_CIPct] = $DB_camp_server_dedicated_trunks[$server_CIPct];
							}
						else
							{
							$DB_camp_server_available[$server_CIPct] = ( ($DB_camp_server_max_vicidial_trunks[$server_CIPct] - $DB_camp_server_balance_trunks_offlimits[$server_CIPct]) -  $DB_NONcamp_server_dedicated_trunks[$server_CIPct]);
							}
						$temp_tally = ($DBfill_needed[$camp_CIPct] - $DBfill_tally[$camp_CIPct]);
						$temp_avail = ( ($DB_camp_server_max_vicidial_trunks[$server_CIPct] - $VAC_server_camp) -  $VAC_server_NONcamp);
						$DB_camp_server_available[$server_CIPct] = ($DB_camp_server_available[$server_CIPct] - $VAC_server_BALcamp);
						if ($DB_camp_server_available[$server_CIPct] < 0) {$DB_camp_server_available[$server_CIPct]=0;}

						if($DB) {print "TEMPVALS:  |$temp_tally|$temp_avail|$DB_camp_server_available[$server_CIPct]||\n";}
						if ($DB_camp_server_available[$server_CIPct] >= $temp_tally)
							{$DB_camp_server_trunks_to_dial[$server_CIPct] = $temp_tally;}
						else
							{$DB_camp_server_trunks_to_dial[$server_CIPct] = $DB_camp_server_available[$server_CIPct];}

						if ($temp_avail < $DB_camp_server_trunks_to_dial[$server_CIPct]) 
							{$DB_camp_server_trunks_to_dial[$server_CIPct] = $temp_avail;}

						$DBfill_tally[$camp_CIPct] = ($DBfill_tally[$camp_CIPct] + $DB_camp_server_trunks_to_dial[$server_CIPct]);

						$event_string="     Server: $DB_camp_server_server_ip[$server_CIPct]   AVAIL: $DB_camp_server_available[$server_CIPct]   DIAL: $DB_camp_server_trunks_to_dial[$server_CIPct]";
						$event_string.="     Campaign Dial Fill tally: $DBfill_tally[$camp_CIPct]/$DBfill_needed[$camp_CIPct]";
						&event_logger;


						$stmtA = "SELECT count(*) FROM vicidial_live_agents where campaign_id='$DBfill_campaign[$camp_CIPct]' and status NOT IN('PAUSED');";
						$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
						$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
						$sthArows=$sthA->rows;
						$rec_count=0;
						while ($sthArows > $rec_count)
							{
							@aryA = $sthA->fetchrow_array;
							$LVA_count =	$aryA[0];
							$rec_count++;
							}
						$sthA->finish();

						if ($LVA_count > 0)
							{
							##################################################################################
							##### PLACE THE CALLS
							##################################################################################
							$event_string="$DBfill_campaign[$camp_CIPct] $DB_camp_server_server_ip[$server_CIPct]: CALLING";
							&event_logger;
							$call_CMPIPct=0;
							$lead_id_call_list='|';
							my $UDaffected_rows=0;
							if ($call_CMPIPct < $DB_camp_server_trunks_to_dial[$server_CIPct])
								{
								$stmtA = "UPDATE vicidial_hopper set status='QUEUE', user='VDAD_$DB_camp_server_server_ip[$server_CIPct]' where campaign_id='$DBfill_campaign[$camp_CIPct]' and status='READY' order by priority desc,hopper_id LIMIT $DB_camp_server_trunks_to_dial[$server_CIPct]";
								print "|$stmtA|\n";
							   $UDaffected_rows = $dbhA->do($stmtA);
								print "hopper rows updated to QUEUE: |$UDaffected_rows|\n";

									if ($UDaffected_rows)
									{
									$lead_id=''; $phone_code=''; $phone_number=''; $called_count='';
										while ($call_CMPIPct < $UDaffected_rows)
										{
										$stmtA = "SELECT lead_id FROM vicidial_hopper where campaign_id='$DBfill_campaign[$camp_CIPct]' and status='QUEUE' and user='VDAD_$DB_camp_server_server_ip[$server_CIPct]' order by priority desc,hopper_id LIMIT 1";
										print "|$stmtA|\n";
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											$rec_count=0;
											 $rec_countCUSTDATA=0;
											while ($sthArows > $rec_count)
												{
												@aryA = $sthA->fetchrow_array;
													$lead_id =		"$aryA[0]";
												$rec_count++;
												}
											$sthA->finish();

										if ($lead_id_call_list =~ /\|$lead_id\|/)
											{
											print "!!!!!!!!!!!!!!!!duplicate lead_id for this run: |$lead_id|     $lead_id_call_list\n";
											if ($SYSLOG)
												{
												open(DUPout, ">>$PATHlogs/VDAD_DUPLICATE.$file_date")
														|| die "Can't open $PATHlogs/VDAD_DUPLICATE.$file_date: $!\n";
												print DUPout "$now_date-----$lead_id_call_list-----$lead_id\n";
												close(DUPout);
												}
											}
										else
											{
											$stmtA = "UPDATE vicidial_hopper set status='INCALL' where lead_id='$lead_id'";
											print "|$stmtA|\n";
										   $UQaffected_rows = $dbhA->do($stmtA);
											print "hopper row updated to INCALL: |$UQaffected_rows|$lead_id|\n";

											$stmtA = "SELECT * FROM vicidial_list where lead_id='$lead_id';";
											$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
											$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
											$sthArows=$sthA->rows;
											$rec_count=0;
											 $rec_countCUSTDATA=0;
											while ($sthArows > $rec_count)
												{
												@aryA = $sthA->fetchrow_array;
													$list_id =					"$aryA[7]";
													$gmt_offset_now	=			"$aryA[8]";
													$called_since_last_reset =	"$aryA[9]";
													$phone_code	=				"$aryA[10]";
													$phone_number =				"$aryA[11]";
													$called_count =				"$aryA[30]";

													$rec_countCUSTDATA++;
												$rec_count++;
												}
											$sthA->finish();

											if ($rec_countCUSTDATA)
												{
												### update called_count
												$called_count++;
												if ($called_since_last_reset =~ /^Y/)
													{
													if ($called_since_last_reset =~ /^Y$/) {$CSLR = 'Y1';}
													else
														{
														$called_since_last_reset =~ s/^Y//gi;
														$called_since_last_reset++;
														$CSLR = "Y$called_since_last_reset";
														}
													}
												else {$CSLR = 'Y';}

												$stmtA = "UPDATE vicidial_list set called_since_last_reset='$CSLR', called_count='$called_count',user='VDAD' where lead_id='$lead_id'";
												$affected_rows = $dbhA->do($stmtA);

												$stmtA = "DELETE FROM vicidial_hopper where lead_id='$lead_id'";
												$affected_rows = $dbhA->do($stmtA);

												$CCID_on=0;   $CCID='';
												$local_DEF = 'Local/';
												$local_AMP = '@';
												$Local_out_prefix = '9';
												$Local_dial_timeout = '60';
											   if ($DBIPdialtimeout[$camp_CIPct] > 4) {$Local_dial_timeout = $DBIPdialtimeout[$camp_CIPct];}
												$Local_dial_timeout = ($Local_dial_timeout * 1000);
											   if (length($DBIPdialprefix[$camp_CIPct]) > 0) {$Local_out_prefix = "$DBIPdialprefix[$camp_CIPct]";}
											   if (length($DBIPvdadexten[$camp_CIPct]) > 0) {$VDAD_dial_exten = "$DBIPvdadexten[$camp_CIPct]";}
											   else {$VDAD_dial_exten = "$answer_transfer_agent";}
											   
											   if (length($DBfill_campaigncid[$camp_CIPct]) > 6) {$CCID = "$DBfill_campaigncid[$camp_CIPct]";   $CCID_on++;}
											   if ($DBIPdialprefix[$camp_CIPct] =~ /x/i) {$Local_out_prefix = '';}

												if ($RECcount)
													{
													if ( (length($RECprefix)>0) && ($called_count < $RECcount) )
													   {$Local_out_prefix .= "$RECprefix";}
													}
												$PADlead_id = sprintf("%09s", $lead_id);	while (length($PADlead_id) > 9) {chop($PADlead_id);}

												if ($lists_update !~ /'$list_id'/) {$lists_update .= "'$list_id',"; $LUcount++;}

											   $lead_id_call_list .= "$lead_id|";

												### whether to omit phone_code or not
												if ($DBIPomitcode[$camp_CIPct] > 0) 
													{$Ndialstring = "$Local_out_prefix$phone_number";}
												else
													{$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}

												### use manager middleware-app to connect the next call to the meetme room
												# VmmddhhmmssLLLLLLLLL
													$VqueryCID = "V$CIDdate$PADlead_id";
												if ($CCID_on) {$CIDstring = "\"$VqueryCID\" <$CCID>";}
												else {$CIDstring = "$VqueryCID";}
												### insert a NEW record to the vicidial_manager table to be processed
													$stmtA = "INSERT INTO vicidial_manager values('','','$SQLdate','NEW','N','$DB_camp_server_server_ip[$server_CIPct]','','Originate','$VqueryCID','Exten: $VDAD_dial_exten','Context: $ext_context','Channel: $local_DEF$Ndialstring$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','')";
													$affected_rows = $dbhA->do($stmtA);

													$event_string = "|     number call dialed|$DBfill_campaign[$camp_CIPct]|$VqueryCID|$stmtA|$gmt_offset_now|";
													 &event_logger;

												### insert a SENT record to the vicidial_auto_calls table 
													$stmtA = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$DB_camp_server_server_ip[$server_CIPct]','$DBfill_campaign[$camp_CIPct]','SENT','$lead_id','$VqueryCID','$phone_code','$phone_number','$SQLdate','OUTBALANCE')";
													$affected_rows = $dbhA->do($stmtA);

												### sleep for a tenth of a second to not flood the server with new calls
												usleep(1*25*1000);

												}
											}
										$call_CMPIPct++;
										}
									}
								}
							}
						else
							{
							$event_string.="No Agents logged in, not dialing";
							&event_logger;
							}





						$server_CIPct++;
						}

					}
				else
					{
					$event_string.="No Balance Servers available that do not have a shortage";
					&event_logger;
					}

				$temp_balance_total = ($DBfill_current_balance[$camp_CIPct] + $DBfill_tally[$camp_CIPct]);
				if ($DB) {print "CURRENT FILL: $temp_balance_total = ($DBfill_current_balance[$camp_CIPct] + $DBfill_tally[$camp_CIPct])\n";}
				$stmtA = "UPDATE vicidial_campaign_stats SET balance_trunk_fill='$temp_balance_total' where campaign_id='$DBfill_campaign[$camp_CIPct]';";
				$affected_rows = $dbhA->do($stmtA);

				$camp_CIPct++;
				}

			}
	##################################################################################
	##### END LOOP IF THERE ARE BALANCE SERVERS AND THERE ARE SHORTAGES
	##################################################################################
		else
			{
			if ($DB) {print "No Balance servers or no shortages\n";}
			$stmtA = "UPDATE vicidial_campaign_stats SET balance_trunk_fill='0';";
			$affected_rows = $dbhA->do($stmtA);

			$event_string.="No Balance Servers available or No Shortages";
			&event_logger;
			}


# so far we have a check of balance servers, a check of shortage servers and we begin traversing the campaigns array
#exit;






	&get_time_now;


		if ($LUcount > 0)
			{
			chop($lists_update);
			$stmtA = "UPDATE vicidial_lists SET list_lastcalldate='$SQLdate' where list_id IN($lists_update);";
			$affected_rows = $dbhA->do($stmtA);
			$event_string = "|     lastcalldate UPDATED $affected_rows|$lists_update|";
			 &event_logger;
			}

	###############################################################################
	###### last, wait for a little bit and repeat the loop
	###############################################################################

		### sleep for 2 and a half seconds before beginning the loop again
		usleep(1*$loop_delay*1000);

	$endless_loop--;
		if($DB){print STDERR "\nloop counter: |$endless_loop|\n";}

		### putting a blank file called "VDADfull.kill" in the directory will automatically safely kill this program
		if (-e "$PATHhome/VDADfull.kill")
			{
			unlink("$PATHhome/VDADfull.kill");
			$endless_loop=0;
			$one_day_interval=0;
			print "\nPROCESS KILLED MANUALLY... EXITING\n\n"
			}
		if ($endless_loop =~ /0$/)	# run every ten cycles (about 25 seconds)
			{
			### Grab Server values from the database
				$stmtA = "SELECT vd_server_logs FROM servers where server_ip = '$VARserver_ip';";
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

			### Grab Server values from the database in case they've changed
			$stmtA = "SELECT max_vicidial_trunks,answer_transfer_agent,local_gmt,ext_context FROM servers where server_ip = '$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$DBmax_vicidial_trunks	=	"$aryA[0]";
					$DBanswer_transfer_agent=	"$aryA[1]";
					$DBSERVER_GMT		=		"$aryA[2]";
					$DBext_context	=			"$aryA[3]";
					if ($DBmax_vicidial_trunks)		{$max_vicidial_trunks = $DBmax_vicidial_trunks;}
					if ($DBanswer_transfer_agent)	{$answer_transfer_agent = $DBanswer_transfer_agent;}
					if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
					if ($DBext_context)				{$ext_context = $DBext_context;}
				$rec_count++;
				}
			$sthA->finish();

			$event_string = "|     updating server parameters $max_vicidial_trunks|$answer_transfer_agent|$SERVER_GMT|$ext_context|$DBvd_server_logs|$SYSLOG|";
			&event_logger;
			&get_time_now;
			}

	$stat_count++;
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

	if ($DB) {print "TIME DEBUG: $LOCAL_GMT_OFF_STD|$LOCAL_GMT_OFF|$isdst|   GMT: $hour:$min\n";}

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}

$now_date_epoch = time();
$now_date = "$year-$mon-$mday $hour:$min:$sec";
$file_date = "$year-$mon-$mday";
	$CIDdate = "$mon$mday$hour$min$sec";
	$tsSQLdate = "$year$mon$mday$hour$min$sec";
	$SQLdate = "$year-$mon-$mday $hour:$min:$sec";

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
	$halfminSQLdate = "$Pyear-$Pmon-$Pmday $Phour:$Pmin:$Psec";

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
	open(Lout, ">>$VDADLOGfile")
			|| die "Can't open $VDADLOGfile: $!\n";
	print Lout "$now_date|$event_string|\n";
	close(Lout);
	}
$event_string='';
}
