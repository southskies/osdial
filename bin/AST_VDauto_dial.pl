#!/usr/bin/perl
#
# AST_VDauto_dial.pl version 2.0.3   *DBI-version*
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
# Places auto_dial calls on the OSDIAL dialer system 
#
# SUMMARY:
# This program was designed for people using the Asterisk PBX with OSDIAL
#
# For the client to use OSDIAL, this program must be in the cron constantly 
# 
# For this program to work you need to have the "asterisk" MySQL database 
# created and create the tables listed in the CONF_MySQL.txt file, also make sure
# that the machine running this program has read/write/update/delete access 
# to that database
# 
# It is recommended that you run this program on the local Asterisk machine
#
# This script is to run perpetually querying every second to place new phone
# calls from the osdial_hopper based upon how many available agents there are
# and the value of the auto_dial_level setting in the campaign screen of the 
# admin web page
#
# It is good practice to keep this program running by placing the associated 
# KEEPALIVE script running every minute to ensure this program is always running
#
# CHANGELOG:
# 50125-1201 - Changed dial timeout to 120 seconds from 180 seconds
# 50317-0954 - Added duplicate check per cycle to account for DB lockups
# 50322-1302 - Added campaign custom callerid feature
# 50324-1353 - Added optional variable to record ring time thru separate diap prefix 
# 50606-2308 - Added code to ensure no calls placed out on inactive campaign
# 50617-1248 - Added code to place LOGOUT entry in auto-paused user logs
# 50620-1349 - Added custom vdad transfer AGI extension per campaign
# 50810-1610 - Added database server variable definitions lookup
# 50810-1630 - Added max_osdial_trunks server limiter on active lines
# 50812-0957 - Corrected max_trunks logic, added update of server every 25 sec
# 60120-1522 - Corrected time error for hour variable, caused agi problems
# 60427-1223 - Fixed Blended in/out CLOSER campaign issue
# 60608-1134 - Altered vac table field of call_type for IN OUT distinction
# 60612-1324 - Altered dead call section to accept BUSY detection from VD_hangup
#            - Altered dead call section to accept DISCONNECT detection from VD_hangup
# 60614-1142 - Added code to work with recycled leads, multi called_since_last_reset values
#            - Removed gmt lead validation because it is already done by VDhopper
# 60807-1438 - Changed to DBI
#            - Changed to use /etc/osdial.conf for configs
# 60814-1749 - Added option for no logging to file
# 60821-1546 - Added option to not dial phone_code per campaign
# 60824-1437 - Added available_only_ratio_tally option
# 61003-1353 - Added restrictions for server trunks
# 61113-1625 - Added code for clearing VDAC LIVE jams
# 61115-1725 - Added OUTBALANCE to call calculation for call_type for balance dialing
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70115-1635 - Added initial auto-alt-dial functionality
# 70116-1619 - Added VDAD Ring-No-Answer Auto Alt Dial code
# 70118-1539 - Added user_group logging to osdial_user_log
# 70131-1550 - Fixed Manual dialing trunk shortage bug
# 70205-1414 - Added code for last called date update
# 70207-1031 - Fixed Tally-only-available bug with customer hangups
# 70215-1123 - Added queue_log ABANDON logging
# 70302-1412 - Fixed max_osdial_trunks update if set to 0
# 70320-1458 - Fixed several errors in calculating trunk shortage for campaigns
# 71029-1909 - Changed CLOSER-type campaign_id restriction
# 71030-2054 - Added hopper priority sorting
# 80713-0624 - Added vicidial_list_last_local_call_time field
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
		$DB=1; # Debug flag, set to 0 for no debug messages, On an active system this will generate hundreds of lines of output per minute
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
$useJAMdebugFILE='1'; ### leave blank for no Jam call debug file writing
$max_osdial_trunks=0; ### setting a default value for max_osdial_trunks

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
	if ( ($line =~ /^VARcps/) && ($CLIcps < 1) )
		{$VARcps = $line;   $VARcps =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

$VARcps = 10 unless ($VARcps);

if (!$VARDB_port) {$VARDB_port='3306';}

	&get_time_now;	# update time/date variables

if (!$VDADLOGfile) {$VDADLOGfile = "$PATHlogs/vdautodial.$year-$mon-$mday";}
if (!$JAMdebugFILE) {$JAMdebugFILE = "$PATHlogs/vdad-JAM.$year-$mon-$mday";}

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;
	
	### connect to MySQL database defined in the conf file
	$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
    or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT telnet_host,telnet_port,ASTmgrUSERNAME,ASTmgrSECRET,ASTmgrUSERNAMEupdate,ASTmgrUSERNAMElisten,ASTmgrUSERNAMEsend,max_osdial_trunks,answer_transfer_agent,local_gmt,ext_context,vd_server_logs FROM servers where server_ip = '$server_ip';";
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
		$DBmax_osdial_trunks	=	"$aryA[7]";
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
			$max_osdial_trunks = $DBmax_osdial_trunks;
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


$one_day_interval = 12;		# 1 month loops for one year 
while($one_day_interval > 0)
{

	$endless_loop=5760000;		# 30 days minutes at XXX seconds per loop
	$stat_count=1;

	while($endless_loop > 0)
	{
		&get_time_now;

		$VDADLOGfile = "$PATHlogs/vdautodial.$year-$mon-$mday";

	###############################################################################
	###### first figure out how many calls should be placed for each campaign per server
	###############################################################################
		@DBlive_user=@MT;
		@DBlive_server_ip=@MT;
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
		@DBIPautoaltdial=@MT;
		@DBIPtrunk_shortage=@MT;
		@DBIPold_trunk_shortage=@MT;
		@DBIPserver_trunks_limit=@MT;
		@DBIPserver_trunks_other=@MT;
		@DBIPserver_trunks_allowed=@MT;

		$active_line_counter=0;
		$user_counter=0;
		$user_campaigns = '|';
		$user_campaignsSQL = "''";
		$user_campaigns_counter = 0;
		$user_campaignIP = '|';
		$user_CIPct = 0;
		$active_agents = "'READY','QUEUE','INCALL','DONE'";
		$lists_update = '';
		$LUcount=0;
		$campaigns_update = '';
		$CPcount=0;

		##### Get a listing of the users that are active and ready to take calls
		##### Also get a listing of the campaigns and campaigns/serverIP that will be used
		$stmtA = "SELECT user,server_ip,campaign_id,conf_exten,status FROM osdial_live_agents where status IN($active_agents) and server_ip='$server_ip' and last_update_time > '$BDtsSQLdate' order by last_call_time";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
				$DBlive_user[$user_counter] =		"$aryA[0]";
				$DBlive_server_ip[$user_counter] =	"$aryA[1]";
				$DBlive_campaign[$user_counter] =	"$aryA[2]";
				$DBlive_conf_exten[$user_counter] =	"$aryA[3]";
				$DBlive_status[$user_counter] =		"$aryA[4]";
				
				if ($user_campaigns !~ /\|$DBlive_campaign[$user_counter]\|/i)
					{
					if ($campaigns_update !~ /'$DBlive_campaign[$user_counter]'/) {$campaigns_update .= "'$DBlive_campaign[$user_counter]',"; $CPcount++;}
					$user_campaigns .= "$DBlive_campaign[$user_counter]|";
					$user_campaignsSQL .= ",'$DBlive_campaign[$user_counter]'";
					$DBcampaigns[$user_campaigns_counter] = $DBlive_campaign[$user_counter];
					$user_campaigns_counter++;
					}
				if ($user_campaignIP !~ /\|$DBlive_campaign[$user_counter]__$DBlive_server_ip[$user_counter]\|/i)
					{
					$user_campaignIP .= "$DBlive_campaign[$user_counter]__$DBlive_server_ip[$user_counter]|";
					$DBIPcampaign[$user_CIPct] = "$DBlive_campaign[$user_counter]";
					$DBIPaddress[$user_CIPct] = "$DBlive_server_ip[$user_counter]";
					$user_CIPct++;
					}
				$user_counter++;
			$rec_count++;
			}
		$sthA->finish();

		### see how many total VDAD calls are going on right now for max limiter
		$stmtA = "SELECT count(*) FROM osdial_auto_calls where server_ip='$server_ip' and status IN('SENT','RINGING','LIVE','XFER','CLOSER');";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
				$active_line_counter = "$aryA[0]";
			$rec_count++;
			}
		$sthA->finish();

		$event_string="LIVE AGENTS LOGGED IN: $user_counter   ACTIVE CALLS: $active_line_counter";
		&event_logger;

		$stmtA = "UPDATE osdial_campaign_server_stats set local_trunk_shortage='0' where server_ip='$server_ip' and campaign_id NOT IN($user_campaignsSQL);";
		$UVCSSaffected_rows = $dbhA->do($stmtA);
		if ($UVCSSaffected_rows > 0) 
			{
			$event_string="OLD TRUNK SHORTS CLEARED: $UVCSSaffected_rows |$user_campaignsSQL|";
			&event_logger;
			}
		$user_CIPct = 0;
		foreach(@DBIPcampaign)
			{
			$user_counter=0;
			foreach(@DBlive_campaign)
				{
				if ( ($DBlive_campaign[$user_counter] =~ /$DBIPcampaign[$user_CIPct]/i) && (length($DBlive_campaign[$user_counter]) == length($DBIPcampaign[$user_CIPct])) && ($DBlive_server_ip[$user_counter] =~ /$DBIPaddress[$user_CIPct]/i) )
					{
					$DBIPcount[$user_CIPct]++;
					$DBIPACTIVEcount[$user_CIPct] = ($DBIPACTIVEcount[$user_CIPct] + 0);
					$DBIPINCALLcount[$user_CIPct] = ($DBIPINCALLcount[$user_CIPct] + 0);
					if ($DBlive_status[$user_counter] =~ /READY|DONE/) 
						{
						$DBIPACTIVEcount[$user_CIPct]++;
						}
					else
						{
						$DBIPINCALLcount[$user_CIPct]++;
						}
					}
				$user_counter++;
				}

			### check for osdial_campaign_server_stats record, if non present then create it
			$stmtA = "SELECT local_trunk_shortage FROM osdial_campaign_server_stats where campaign_id='$DBIPcampaign[$user_CIPct]' and server_ip='$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$DBIPold_trunk_shortage[$user_CIPct] =		"$aryA[0]";
				$rec_count++;
				}
			if ($rec_count < 1)
				{
				$stmtA = "INSERT INTO osdial_campaign_server_stats SET local_trunk_shortage='0', server_ip='$server_ip',campaign_id='$DBIPcampaign[$user_CIPct]';";
				$affected_rows = $dbhA->do($stmtA);

				$DBIPold_trunk_shortage[$user_CIPct]=0;

				$event_string="VCSS ENTRY INSERTED: $affected_rows";
				&event_logger;
				}

			$DBIPserver_trunks_limit[$user_CIPct] = '';
			$DBIPserver_trunks_other[$user_CIPct] = 0;
			$DBIPserver_trunks_allowed[$user_CIPct] = $max_osdial_trunks;
			### check for osdial_server_trunks record
			$stmtA = "SELECT dedicated_trunks FROM osdial_server_trunks where campaign_id='$DBIPcampaign[$user_CIPct]' and server_ip='$server_ip' and trunk_restriction='MAXIMUM_LIMIT';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$DBIPserver_trunks_limit[$user_CIPct] =		"$aryA[0]";
				$rec_count++;
				}
			$stmtA = "SELECT sum(dedicated_trunks) FROM osdial_server_trunks where campaign_id NOT IN('$DBIPcampaign[$user_CIPct]') and server_ip='$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$DBIPserver_trunks_other[$user_CIPct] =		"$aryA[0]";
				$rec_count++;
				}

			$DBIPserver_trunks_allowed[$user_CIPct] = ($max_osdial_trunks - $DBIPserver_trunks_other[$user_CIPct]);


			### grab the dial_level and multiply by active agents to get your goalcalls
			$DBIPadlevel[$user_CIPct]=0;
			$stmtA = "SELECT auto_dial_level,local_call_time,dial_timeout,dial_prefix,campaign_cid,active,campaign_vdad_exten,closer_campaigns,omit_phone_code,available_only_ratio_tally,auto_alt_dial,campaign_allow_inbound,calls_per_hour_limit FROM osdial_campaigns where campaign_id='$DBIPcampaign[$user_CIPct]'";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			$active_only=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$DBIPadlevel[$user_CIPct] =		"$aryA[0]";
					$DBIPcalltime[$user_CIPct] =	"$aryA[1]";
					$DBIPdialtimeout[$user_CIPct] =	"$aryA[2]";
					$DBIPdialprefix[$user_CIPct] =	"$aryA[3]";
					$DBIPcampaigncid[$user_CIPct] =	"$aryA[4]";
					$DBIPactive[$user_CIPct] =		"$aryA[5]";
					$DBIPvdadexten[$user_CIPct] =	"$aryA[6]";
					$DBIPclosercamp[$user_CIPct] =	"$aryA[7]";
					$omit_phone_code =				"$aryA[8]";
						if ($omit_phone_code =~ /Y/) {$DBIPomitcode[$user_CIPct] = 1;}
						else {$DBIPomitcode[$user_CIPct] = 0;}
					$available_only_ratio_tally =	"$aryA[9]";
						if ($available_only_ratio_tally =~ /Y/) 
							{
							$DBIPcount[$user_CIPct] = $DBIPACTIVEcount[$user_CIPct];
							$active_only=1;
							}
					$DBIPautoaltdial[$user_CIPct] =	"$aryA[10]";
					$DBIPcampaign_allow_inbound[$user_CIPct] =	"$aryA[11]";
					$DBIPcalls_per_hour_limit[$user_CIPct] =	($aryA[12] * 1);
				$rec_count++;
				}
			$sthA->finish();

			$calls_hour = 0;
			$stmtA = "SELECT calls_hour FROM osdial_campaign_stats where campaign_id='$DBIPcampaign[$user_CIPct]';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0) {
				@aryA = $sthA->fetchrow_array;
				$calls_hour = ($aryA[0] * 1);
			}
			if ($DBIPcalls_per_hour_limit[$user_CIPct] > 0 and $calls_hour > $DBIPcalls_per_hour_limit[$user_CIPct]) {
				$DBIPgoalcalls[$user_CIPct] = 0;
			} else {
				$DBIPgoalcalls[$user_CIPct] = ($DBIPadlevel[$user_CIPct] * $DBIPcount[$user_CIPct]);
			}

			if ($active_only > 0) 
				{
				$tally_xfer_line_counter=0;
				### see how many VDAD calls are live as XFERs to agents
				$stmtA = "SELECT count(*) FROM osdial_auto_calls where server_ip='$DBIPaddress[$user_CIPct]' and campaign_id='$DBIPcampaign[$user_CIPct]' and status IN('XFER','CLOSER');";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
						$tally_xfer_line_counter = "$aryA[0]";
					$rec_count++;
					}
				$sthA->finish();

				$DBIPgoalcalls[$user_CIPct] = ($DBIPgoalcalls[$user_CIPct] + $tally_xfer_line_counter);
				}
			if ($DBIPactive[$user_CIPct] =~ /N/) {$DBIPgoalcalls[$user_CIPct] = 0;}
			$DBIPgoalcalls[$user_CIPct] = sprintf("%.0f", $DBIPgoalcalls[$user_CIPct]);

			$event_string="$DBIPcampaign[$user_CIPct] $DBIPaddress[$user_CIPct]: agents: $DBIPcount[$user_CIPct]     dial_level: $DBIPadlevel[$user_CIPct]";
			&event_logger;


			### see how many calls are alrady active per campaign per server and 
			### subtract that number from goalcalls to determine how many new 
			### calls need to be placed in this loop
			if ($DBIPcampaign_allow_inbound[$user_CIPct] =~ /Y/)
			   {
				if (length($DBIPclosercamp[$user_CIPct]) > 2)
				   {
					$DBIPclosercamp[$user_CIPct] =~ s/^ | -$//gi;
					$DBIPclosercamp[$user_CIPct] =~ s/ /','/gi;
					$DBIPclosercamp[$user_CIPct] = "'$DBIPclosercamp[$user_CIPct]'";
				   }
				  else {$DBIPclosercamp[$user_CIPct]="''";}
				
				$campaign_query = "( (call_type='IN' and campaign_id IN($DBIPclosercamp[$user_CIPct])) or (campaign_id='$DBIPcampaign[$user_CIPct]' and call_type IN('OUT','OUTBALANCE')) )";
				}
			else {$campaign_query = "(campaign_id='$DBIPcampaign[$user_CIPct]' and call_type IN('OUT','OUTBALANCE'))";}
			$stmtA = "SELECT count(*) FROM osdial_auto_calls where $campaign_query and server_ip='$DBIPaddress[$user_CIPct]' and status IN('SENT','RINGING','LIVE','XFER','CLOSER');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$DBIPexistcalls[$user_CIPct] = "$aryA[0]";
				$rec_count++;
				}
			$sthA->finish();

			$active_line_goal=0;
			$DBIPmakecalls[$user_CIPct] = ($DBIPgoalcalls[$user_CIPct] - $DBIPexistcalls[$user_CIPct]);
			$DBIPmakecallsGOAL = $DBIPmakecalls[$user_CIPct];
			$MVT_msg = '';
			$DBIPtrunk_shortage[$user_CIPct] = 0;
			$active_line_goal = ($active_line_counter + $DBIPmakecalls[$user_CIPct]);
			if ($active_line_goal > $max_osdial_trunks) 
				{
				$NEWmakecallsgoal = ($max_osdial_trunks - $active_line_counter);
				if ($DBIPmakecalls[$user_CIPct] > $NEWmakecallsgoal)
					{$DBIPmakecalls[$user_CIPct] = $NEWmakecallsgoal;}
				$DBIPtrunk_shortage[$user_CIPct] = ($active_line_goal - $max_osdial_trunks);
				if ($DBIPtrunk_shortage[$user_CIPct] > $DBIPmakecallsGOAL) 
					{$DBIPtrunk_shortage[$user_CIPct] = $DBIPmakecallsGOAL}
				$MVT_msg = "MVT override: $max_osdial_trunks |$DBIPmakecalls[$user_CIPct] $DBIPtrunk_shortage[$user_CIPct]|";
				}
			if (length($DBIPserver_trunks_limit[$user_CIPct])>0) 
				{
				if ($DBIPserver_trunks_limit[$user_CIPct] < $active_line_goal)
					{
					$MVT_msg .= " TRUNK LIMIT override: $DBIPserver_trunks_limit[$user_CIPct]";
					$DBIPtrunk_shortage[$user_CIPct] = ($active_line_goal - $DBIPserver_trunks_limit[$user_CIPct]);
					if ($DBIPtrunk_shortage[$user_CIPct] > $DBIPmakecallsGOAL) 
						{$DBIPtrunk_shortage[$user_CIPct] = $DBIPmakecallsGOAL}
					$active_line_goal = $DBIPserver_trunks_limit[$user_CIPct];
					$NEWmakecallsgoal = ($active_line_goal - $active_line_counter);
					if ($DBIPmakecalls[$user_CIPct] > $NEWmakecallsgoal)
						{$DBIPmakecalls[$user_CIPct] = $NEWmakecallsgoal;}
					}
				}
			else
				{
				if ($DBIPserver_trunks_allowed[$user_CIPct] < $active_line_goal)
					{
					$MVT_msg .= " OTHER LIMIT override: $DBIPserver_trunks_allowed[$user_CIPct]";
					$DBIPtrunk_shortage[$user_CIPct] = ($active_line_goal - $DBIPserver_trunks_allowed[$user_CIPct]);
					if ($DBIPtrunk_shortage[$user_CIPct] > $DBIPmakecallsGOAL) 
						{$DBIPtrunk_shortage[$user_CIPct] = $DBIPmakecallsGOAL}
					$active_line_goal = $DBIPserver_trunks_allowed[$user_CIPct];
					$NEWmakecallsgoal = ($active_line_goal - $active_line_counter);
					if ($DBIPmakecalls[$user_CIPct] > $NEWmakecallsgoal)
						{$DBIPmakecalls[$user_CIPct] = $NEWmakecallsgoal;}
					}
				}

			if ($DBIPmakecalls[$user_CIPct] > 0) 
				{$active_line_counter = ($DBIPmakecalls[$user_CIPct] + $active_line_counter);}
			$event_string="$DBIPcampaign[$user_CIPct] $DBIPaddress[$user_CIPct]: Calls to place: $DBIPmakecalls[$user_CIPct] ($DBIPgoalcalls[$user_CIPct] - $DBIPexistcalls[$user_CIPct]) $active_line_counter $MVT_msg";
			&event_logger;

			### Calculate campaign-wide agent waiting and calls waiting differential
			### This is used by the AST_VDadapt script to see if the current dial_level
			### should be changed at all
			$total_agents=0;
			$ready_agents=0;
			$waiting_calls=0;

			$stmtA = "SELECT count(*),status from osdial_live_agents where campaign_id='$DBIPcampaign[$user_CIPct]' and last_update_time > '$halfminSQLdate' group by status;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$VCSagent_count =		 "$aryA[0]";
				$VCSagent_status =		 "$aryA[1]";
				$rec_count++;
				if ($VCSagent_status =~ /READY|DONE/) {$ready_agents = ($ready_agents + $VCSagent_count);}
				$total_agents = ($total_agents + $VCSagent_count);
				}
			$sthA->finish();

			$stmtA = "SELECT count(*) FROM osdial_auto_calls where $campaign_query and status IN('LIVE');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$waiting_calls = "$aryA[0]";
				$rec_count++;
				}
			$sthA->finish();

			$stat_ready_agents[$user_CIPct][$stat_count] = $ready_agents;
			$stat_waiting_calls[$user_CIPct][$stat_count] = $waiting_calls;
			$stat_total_agents[$user_CIPct][$stat_count] = $total_agents;

			$stat_it=20;
			$ready_diff_total=0;
			$waiting_diff_total=0;
			$total_agents_total=0;
			$ready_diff_avg=0;
			$waiting_diff_avg=0;
			$total_agents_avg=0;
			$stat_differential=0;
			if ($stat_count < 20) 
				{
				$stat_it = $stat_count;
				$stat_B = 1;
				}
			else
				{
				$stat_B = ($stat_count - 19);
				}
			
			$it=0;
			while($it < $stat_it)
				{
				$it_ary = ($it + $stat_B);
				$ready_diff_total = ($ready_diff_total + $stat_ready_agents[$user_CIPct][$it_ary]);
				$waiting_diff_total = ($waiting_diff_total + $stat_waiting_calls[$user_CIPct][$it_ary]);
				$total_agents_total = ($total_agents_total + $stat_total_agents[$user_CIPct][$it_ary]);
		#		$event_string="$stat_count $it_ary   $stat_total_agents[$user_CIPct][$it_ary]|$stat_ready_agents[$user_CIPct][$it_ary]|$stat_waiting_calls[$user_CIPct][$it_ary]";
		#		&event_logger;
				$it++;
				}
			
			if ($ready_diff_total > 0) 
				{$ready_diff_avg = ($ready_diff_total / $stat_it);}
			if ($waiting_diff_total > 0) 
				{$waiting_diff_avg = ($waiting_diff_total / $stat_it);}
			if ($total_agents_total > 0) 
				{$total_agents_avg = ($total_agents_total / $stat_it);}
			$stat_differential = ($ready_diff_avg - $waiting_diff_avg);

			$event_string="CAMPAIGN DIFFERENTIAL: $total_agents_avg   $stat_differential   ($ready_diff_avg - $waiting_diff_avg)";
			&event_logger;

			$stmtA = "UPDATE osdial_campaign_stats SET differential_onemin='$stat_differential', agents_average_onemin='$total_agents_avg' where campaign_id='$DBIPcampaign[$user_CIPct]';";
			$affected_rows = $dbhA->do($stmtA);

			if ( ($DBIPold_trunk_shortage[$user_CIPct] > $DBIPtrunk_shortage[$user_CIPct]) || ($DBIPold_trunk_shortage[$user_CIPct] < $DBIPtrunk_shortage[$user_CIPct]) )
				{
				if ($DBIPadlevel[$user_CIPct] < 1) 
					{
					$event_string="Manual Dial Override for Shortage |$DBIPadlevel[$user_CIPct]|$DBIPtrunk_shortage[$user_CIPct]|";
					&event_logger;
					$DBIPtrunk_shortage[$user_CIPct] = 0;
					}
				$stmtA = "UPDATE osdial_campaign_server_stats SET local_trunk_shortage='$DBIPtrunk_shortage[$user_CIPct]',update_time='$now_date' where server_ip='$server_ip' and campaign_id='$DBIPcampaign[$user_CIPct]';";
				$affected_rows = $dbhA->do($stmtA);
				}

			$event_string="LOCAL TRUNK SHORTAGE: $DBIPtrunk_shortage[$user_CIPct]|$DBIPold_trunk_shortage[$user_CIPct]  ($active_line_goal - $max_osdial_trunks)";
			&event_logger;

			$user_CIPct++;
			}

	###############################################################################
	###### second lookup leads and place calls for each campaign/server_ip
	######     go one lead at a time and place the call by inserting a record into osdial_manager
	###############################################################################

		$user_CIPct = 0;
		foreach(@DBIPcampaign)
			{
			$event_string="$DBIPcampaign[$user_CIPct] $DBIPaddress[$user_CIPct]: CALLING";
			&event_logger;
			$call_CMPIPct=0;
			$lead_id_call_list='|';
			my $UDaffected_rows=0;
			if ($call_CMPIPct < $DBIPmakecalls[$user_CIPct])
				{
				$stmtA = "UPDATE osdial_hopper set status='QUEUE', user='VDAD_$server_ip' where campaign_id='$DBIPcampaign[$user_CIPct]' and status='READY' order by priority desc,hopper_id LIMIT $DBIPmakecalls[$user_CIPct]";
				print "|$stmtA|\n";
			   $UDaffected_rows = $dbhA->do($stmtA);
				print "hopper rows updated to QUEUE: |$UDaffected_rows|\n";

					if ($UDaffected_rows)
					{
					$lead_id=''; $phone_code=''; $phone_number=''; $called_count='';
						while ($call_CMPIPct < $UDaffected_rows)
						{
						$stmtA = "SELECT lead_id,alt_dial FROM osdial_hopper where campaign_id='$DBIPcampaign[$user_CIPct]' and status='QUEUE' and user='VDAD_$server_ip' order by priority desc,hopper_id LIMIT 1";
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
									$alt_dial =		"$aryA[1]";
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
							$stmtA = "UPDATE osdial_hopper set status='INCALL' where lead_id='$lead_id'";
							print "|$stmtA|\n";
						   $UQaffected_rows = $dbhA->do($stmtA);
							print "hopper row updated to INCALL: |$UQaffected_rows|$lead_id|\n";

							$stmtA = "SELECT * FROM osdial_list where lead_id='$lead_id';";
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
									$address3 =					"$aryA[18]";
									$alt_phone =				"$aryA[26]";
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

								$LLCT_DATE_offset = ($LOCAL_GMT_OFF - $gmt_offset_now);
								$LLCT_DATE_offset_epoch = ( $secX - ($LLCT_DATE_offset * 3600) );
								($Lsec,$Lmin,$Lhour,$Lmday,$Lmon,$Lyear,$Lwday,$Lyday,$Lisdst) = localtime($LLCT_DATE_offset_epoch);
								$Lyear = ($Lyear + 1900);
								$Lmon++;
								if ($Lmon < 10) {$Lmon = "0$Lmon";}
								if ($Lmday < 10) {$Lmday = "0$Lmday";}
								if ($Lhour < 10) {$Lhour = "0$Lhour";}
								if ($Lmin < 10) {$Lmin = "0$Lmin";}
								if ($Lsec < 10) {$Lsec = "0$Lsec";}
								$LLCT_DATE = "$Lyear-$Lmon-$Lmday $Lhour:$Lmin:$Lsec";

								if ( ($alt_dial =~ /ALT|ADDR3/) && ($DBIPautoaltdial[$user_CIPct] =~ /ALT|ADDR/) )
									{
									if ( ($alt_dial =~ /ALT/) && ($DBIPautoaltdial[$user_CIPct] =~ /ALT/) )
										{
										$alt_phone =~ s/\D//gi;
										$phone_number = $alt_phone;
										}
									if ( ($alt_dial =~ /ADDR3/) && ($DBIPautoaltdial[$user_CIPct] =~ /ADDR3/) )
										{
										$address3 =~ s/\D//gi;
										$phone_number = $address3;
										}
									$stmtA = "UPDATE osdial_list set called_since_last_reset='$CSLR',user='VDAD',last_local_call_time='$LLCT_DATE' where lead_id='$lead_id'";
									}
								else
									{
									$stmtA = "UPDATE osdial_list set called_since_last_reset='$CSLR', called_count='$called_count',user='VDAD',last_local_call_time='$LLCT_DATE' where lead_id='$lead_id'";
									}
								$affected_rows = $dbhA->do($stmtA);

								$stmtA = "DELETE FROM osdial_hopper where lead_id='$lead_id'";
								$affected_rows = $dbhA->do($stmtA);

								$CCID_on=0;   $CCID='';
								$local_DEF = 'Local/';
								$local_AMP = '@';
								$Local_out_prefix = '9';
								$Local_dial_timeout = '60';
							   if ($DBIPdialtimeout[$user_CIPct] > 4) {$Local_dial_timeout = $DBIPdialtimeout[$user_CIPct];}
								$Local_dial_timeout = ($Local_dial_timeout * 1000);
							   if (length($DBIPdialprefix[$user_CIPct]) > 0) {$Local_out_prefix = "$DBIPdialprefix[$user_CIPct]";}
							   if (length($DBIPvdadexten[$user_CIPct]) > 0) {$VDAD_dial_exten = "$DBIPvdadexten[$user_CIPct]";}
							   else {$VDAD_dial_exten = "$answer_transfer_agent";}
							   
							   if (length($DBIPcampaigncid[$user_CIPct]) > 6) {$CCID = "$DBIPcampaigncid[$user_CIPct]";   $CCID_on++;}
							   if ($DBIPdialprefix[$user_CIPct] =~ /x/i) {$Local_out_prefix = '';}

								if ($RECcount)
									{
								    if ( (length($RECprefix)>0) && ($called_count < $RECcount) )
									   {$Local_out_prefix .= "$RECprefix";}
									}
								$PADlead_id = sprintf("%09s", $lead_id);	while (length($PADlead_id) > 9) {chop($PADlead_id);}

								if ($lists_update !~ /'$list_id'/) {$lists_update .= "'$list_id',"; $LUcount++;}

							   $lead_id_call_list .= "$lead_id|";

								### whether to omit phone_code or not
								if ($DBIPomitcode[$user_CIPct] > 0) 
									{$Ndialstring = "$Local_out_prefix$phone_number";}
								else
									{$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}

								### use manager middleware-app to connect the next call to the meetme room
								# VmmddhhmmssLLLLLLLLL
									$VqueryCID = "V$CIDdate$PADlead_id";
								if ($CCID_on) {$CIDstring = "\"$VqueryCID\" <$CCID>";}
								else {$CIDstring = "$VqueryCID";}
								### insert a NEW record to the osdial_manager table to be processed
									$stmtA = "INSERT INTO osdial_manager values('','','$SQLdate','NEW','N','$DBIPaddress[$user_CIPct]','','Originate','$VqueryCID','Exten: $VDAD_dial_exten','Context: $ext_context','Channel: $local_DEF$Ndialstring$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','')";
									$affected_rows = $dbhA->do($stmtA);

									$event_string = "|     number call dialed|$DBIPcampaign[$user_CIPct]|$VqueryCID|$stmtA|$gmt_offset_now|$alt_dial|";
									 &event_logger;

								### insert a SENT record to the osdial_auto_calls table 
									$stmtA = "INSERT INTO osdial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type,alt_dial) values('$DBIPaddress[$user_CIPct]','$DBIPcampaign[$user_CIPct]','SENT','$lead_id','$VqueryCID','$phone_code','$phone_number','$SQLdate','OUT','$alt_dial')";
									$affected_rows = $dbhA->do($stmtA);

								### sleep for a tenth of a second to not flood the server with new calls
								usleep(1000000/$VARcps);

								}
							}
						$call_CMPIPct++;
						}
					}

			}

		$user_CIPct++;
		}







	&get_time_now;

	###############################################################################
	###### third we will grab the callerids of the osdial_auto_calls records and check for dead calls
	######    we also check to make sure that it isn't a call that has been transferred, 
	######    if it has been we need to leave the osdial_list status alone
	###############################################################################

		@KLcallerid = @MT;
		@KLserver_ip = @MT;
		@KLchannel = @MT;
		$kill_vac=0;

		$stmtA = "SELECT callerid,server_ip,channel,uniqueid,status FROM osdial_auto_calls where server_ip='$server_ip' order by call_time;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		 $rec_countCUSTDATA=0;
		 $kill_vac=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
				$KLcallerid[$kill_vac]		= "$aryA[0]";
				$KLserver_ip[$kill_vac]		= "$aryA[1]";
				$KLchannel[$kill_vac]		= "$aryA[2]";
				$KLuniqueid[$kill_vac]		= "$aryA[3]";
				$KLstatus[$kill_vac]		= "$aryA[4]";
			$kill_vac++;
			$rec_count++;
			}
		$sthA->finish();

		$kill_vac=0;
		foreach(@KLcallerid)
			{
			if (length($KLserver_ip[$kill_vac]) > 7)
				{
				$start_epoch=0;   $end_epoch=0;   $CLuniqueid='';   $CLlast_update_time=0;
				$KLcalleridCHECK[$kill_vac]=$KLcallerid[$kill_vac];
				$KLcalleridCHECK[$kill_vac] =~ s/\W//gi;

				if ( (length($KLcalleridCHECK[$kill_vac]) > 17) && ($KLcalleridCHECK[$kill_vac] =~ /\d\d\d\d\d\d\d\d\d\d\d\d\d\d/) )
					{
					$stmtA = "SELECT end_epoch,uniqueid,start_epoch FROM call_log where caller_code='$KLcallerid[$kill_vac]' and server_ip='$KLserver_ip[$kill_vac]' order by end_epoch, start_time desc limit 1;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					 $rec_countCUSTDATA=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
							$end_epoch		= "$aryA[0]";
							$CLuniqueid		= "$aryA[1]";
							$start_epoch	= "$aryA[2]";
						$rec_count++;
						}
					$sthA->finish();
					}

				if ( (length($KLuniqueid[$kill_vac]) > 11) && (length($CLuniqueid) < 12) )
					{
					$stmtA = "SELECT end_epoch,uniqueid,start_epoch FROM call_log where uniqueid='$KLuniqueid[$kill_vac]' and server_ip='$KLserver_ip[$kill_vac]' order by end_epoch, start_time desc limit 1;";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					 $rec_countCUSTDATA=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
							$end_epoch		= "$aryA[0]";
							$CLuniqueid		= "$aryA[1]";
							$start_epoch	= "$aryA[2]";
						$rec_count++;
						}
					$sthA->finish();
					}

				$CLlead_id=''; $auto_call_id=''; $CLstatus=''; $CLcampaign_id=''; $CLphone_number=''; $CLphone_code='';

				$stmtA = "SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,call_type,UNIX_TIMESTAMP(last_update_time) FROM osdial_auto_calls where callerid='$KLcallerid[$kill_vac]'";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				 $rec_countCUSTDATA=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
						$auto_call_id	= "$aryA[0]";
						$CLlead_id		= "$aryA[1]";
						$CLphone_number	= "$aryA[2]";
						$CLstatus		= "$aryA[3]";
						$CLcampaign_id	= "$aryA[4]";
						$CLphone_code	= "$aryA[5]";
						$CLalt_dial		= "$aryA[6]";
						$CLstage		= "$aryA[7]";
						$CLcall_type	= "$aryA[8]";
						$CLlast_update_time	= "$aryA[9]";
					$rec_count++;
					}
				$sthA->finish();

				if ($CLcall_type =~ /IN/)
					{
					$stmtA = "SELECT drop_call_seconds FROM osdial_inbound_groups where group_id='$CLcampaign_id';";
					$timeout_leeway = 30;
					}
				else
					{
					$stmtA = "SELECT dial_timeout,drop_call_seconds FROM osdial_campaigns where campaign_id='$CLcampaign_id';";
					$timeout_leeway = 7;
					}

				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				if ($sthArows > 0)
					{
					@aryA = $sthA->fetchrow_array;
					$CLdial_timeout	= "$aryA[0]";
					$CLdrop_call_seconds	= "$aryA[1]";
					}
				$sthA->finish();

				$dialtime_log = 0;
				$dialtime_log = ($end_epoch - $start_epoch) if ($end_epoch > 0);
				$dialtime_catch = ($now_date_epoch - ($start_epoch + $timeout_leeway));
				if ($dialtime_catch > 100000) {$dialtime_catch=0;}
				$call_timeout = ($CLdial_timeout + $CLdrop_call_seconds);
				if ($CLstage =~ /SURVEY|REMIND|IVR/) {$call_timeout = ($call_timeout + 120);}

				if ( ($dialtime_log >= $call_timeout) || ($dialtime_catch >= $call_timeout) || ($CLstatus =~ /BUSY|DISCONNECT|XFER|CLOSER/) )
					{
					if ($CLcall_type !~ /IN/) 
						{
						if ($CLstatus !~ /XFER|CLOSER/) 
							{
							$stmtA = "DELETE from osdial_auto_calls where auto_call_id='$auto_call_id'";
							$affected_rows = $dbhA->do($stmtA);
	
							$event_string = "|     dead call vac deleted|$auto_call_id|$CLlead_id|$CLuniqueid|$KLuniqueid[$kill_vac]|$KLcallerid[$kill_vac]|$end_epoch|$affected_rows|$KLchannel[$kill_vac]|$CLcall_type|$CLdial_timeout|$CLdrop_call_seconds|$call_timeout|$dialtime_log|$dialtime_catch|";
						 	&event_logger;
	
							$CLstage =~ s/LIVE|-//gi;
							if ($CLstage < 0.25) {$CLstage=1;}
	
							if ($CLstatus =~ /BUSY/) {$CLnew_status = 'B';}
							else
								{
								if ($CLstatus =~ /DISCONNECT/) {$CLnew_status = 'DC';}
								else {$CLnew_status = 'NA';}
								}
							if ($CLstatus =~ /LIVE/) {$CLnew_status = 'DROP';}
							else 
								{
								$end_epoch = ($now_date_epoch + 1);
								$stmtA = "INSERT INTO osdial_log (uniqueid,lead_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,processed,length_in_sec,end_epoch) values('$CLuniqueid','$CLlead_id','$CLcampaign_id','$SQLdate','$now_date_epoch','$CLnew_status','$CLphone_code','$CLphone_number','VDAD','N','$CLstage','$end_epoch')";
									if($M){print STDERR "\n|$stmtA|\n";}
								$affected_rows = $dbhA->do($stmtA);
	
								$event_string = "|     dead NA call added to log $CLuniqueid|$CLlead_id|$CLphone_number|$CLstatus|$CLnew_status|$affected_rows|";
							 	&event_logger;
	
								}
	
							if ($CLlead_id > 0)
								{
								$stmtA = "UPDATE osdial_list set status='$CLnew_status' where lead_id='$CLlead_id'";
								$affected_rows = $dbhA->do($stmtA);
	
								$event_string = "|     dead call vac lead marked $CLnew_status|$CLlead_id|$CLphone_number|$CLstatus|";
							 	&event_logger;
								}
	
							$stmtA = "UPDATE osdial_live_agents set status='PAUSED',random_id='10' where  callerid='$KLcallerid[$kill_vac]';";
							$affected_rows = $dbhA->do($stmtA);
	
							$event_string = "|     dead call vla agent PAUSED $affected_rows|$CLlead_id|$CLphone_number|$CLstatus|";
						 	&event_logger;
	
							if ( ($enable_queuemetrics_logging > 0) && ($CLstatus =~ /LIVE/) )
								{
								$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
							 	or die "Couldn't connect to database: " . DBI->errstr;
	
								if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}
	
								$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$KLcallerid[$kill_vac]',queue='$CLcampaign_id',agent='NONE',verb='ABANDON',data1='1',data2='1',data3='$CLstage',serverid='$queuemetrics_log_id';";
								$Baffected_rows = $dbhB->do($stmtB);
	
								$dbhB->disconnect();
								}
	
							##### BEGIN AUTO ALT PHONE DIAL SECTION #####
							### check to see if campaign has alt_dial enabled
							$VD_auto_alt_dial = 'NONE';
							$VD_auto_alt_dial_statuses='';
							$stmtA="SELECT auto_alt_dial,auto_alt_dial_statuses FROM osdial_campaigns where campaign_id='$CLcampaign_id';";
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
							if ( ($VD_auto_alt_dial_statuses =~ / $CLnew_status /) && ($CLlead_id > 0) )
								{
								if ( ($VD_auto_alt_dial =~ /(ALT_ONLY|ALT_AND_ADDR3)/) && ($CLalt_dial =~ /NONE|MAIN/) )
									{
									$VD_alt_phone='';
									$stmtA="SELECT alt_phone,gmt_offset_now,state,list_id FROM osdial_list where lead_id='$CLlead_id';";
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
										$stmtA = "INSERT INTO osdial_hopper SET lead_id='$CLlead_id',campaign_id='$CLcampaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ALT',user='',priority='25';";
										$affected_rows = $dbhA->do($stmtA);
										if ($AGILOG) {$agi_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
										}
									}
								if ( ( ($VD_auto_alt_dial =~ /(ADDR3_ONLY)/) && ($CLalt_dial =~ /NONE|MAIN/) ) || ( ($VD_auto_alt_dial =~ /(ALT_AND_ADDR3)/) && ($CLalt_dial =~ /ALT/) ) )
									{
									$VD_address3='';
									$stmtA="SELECT address3,gmt_offset_now,state,list_id FROM osdial_list where lead_id='$CLlead_id';";
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
										$stmtA = "INSERT INTO osdial_hopper SET lead_id='$CLlead_id',campaign_id='$CLcampaign_id',status='READY',list_id='$VD_list_id',gmt_offset_now='$VD_gmt_offset_now',state='$VD_state',alt_dial='ADDR3',user='',priority='20';";
										$affected_rows = $dbhA->do($stmtA);
										if ($AGILOG) {$agi_string = "--    VDH record inserted: |$affected_rows|   |$stmtA|";   &agi_output;}
										}
									}
								}
							##### END AUTO ALT PHONE DIAL SECTION #####
	
	
	
	
							}
						else
							{
							if ( ($KLcallerid[$kill_vac] =~ /^M\d\d\d\d\d\d\d\d\d\d/) && ($CLlast_update_time < $TDtarget) )
								{
								$stmtA = "DELETE from vicidial_auto_calls where auto_call_id='$auto_call_id'";
								$affected_rows = $dbhA->do($stmtA);
								$event_string = "|   M dead call vac deleted|$auto_call_id|$CLlead_id|$KLcallerid[$kill_vac]|$end_epoch|$affected_rows|$KLchannel[$kill_vac]|$CLcall_type|$CLlast_update_time < $XDtarget|";
								&event_logger;
								}
							else
								{
								$event_string = "|     dead call vac XFERd do nothing|$CLlead_id|$CLphone_number|$CLstatus|";
								&event_logger;
								}
							}
						}
					else
						{
						$event_string = "|     dead call vac INBOUND do nothing|$CLlead_id|$CLphone_number|$CLstatus|";
						&event_logger;
						}
					}
				}
			$kill_vac++;
			}



		### pause agents that have disconnected or closed their apps over 30 seconds ago
		$stmtA = "UPDATE osdial_live_agents set status='PAUSED',random_id='10' where server_ip='$server_ip' and last_update_time < '$PDtsSQLdate' and status NOT IN('PAUSED')";
		$affected_rows = $dbhA->do($stmtA);

		$event_string = "|     lagged call vla agent PAUSED $affected_rows|$PDtsSQLdate|$BDtsSQLdate|$tsSQLdate|";
		 &event_logger;

		if ($affected_rows > 0)
			{
			@VALOuser=@MT; @VALOcampaign=@MT; @VALOtimelog=@MT; @VALOextension=@MT;
			$logcount=0;
			$stmtA = "SELECT user,campaign_id,last_update_time,extension FROM osdial_live_agents where server_ip='$server_ip' and status = 'PAUSED' and random_id='10' order by last_update_time desc limit $affected_rows";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			 $rec_countCUSTDATA=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$VALOuser[$logcount] =		"$aryA[0]";
					$VALOcampaign[$logcount] =	"$aryA[1]";
					$VALOtimelog[$logcount]	=	"$aryA[2]";
					$VALOextension[$logcount] = "$aryA[3]";
					$logcount++;
				$rec_count++;
				}
			$sthA->finish();
			$logrun=0;
			foreach(@VALOuser)
				{
					$VALOuser_group='';
					$stmtA = "SELECT user_group FROM osdial_users where user='$VALOuser[$logrun]';";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$UGrec_count=0;
					while ($sthArows > $UGrec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$VALOuser_group =		"$aryA[0]";
						$UGrec_count++;
						}
					$sthA->finish();
				$stmtA = "INSERT INTO osdial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$VALOuser[$logrun]','LOGOUT','$VALOcampaign[$logrun]','$SQLdate','$now_date_epoch','$VALOuser_group');";
				$affected_rows = $dbhA->do($stmtA);

				$event_string = "|          lagged agent LOGOUT entry inserted $VALOuser[$logrun]|$VALOcampaign[$logrun]|$VALOextension[$logcount]|";
				 &event_logger;

				$logrun++;
				}

			}


		### delete call records that are SENT for over 2 minutes
		$stmtA = "DELETE FROM osdial_auto_calls where server_ip='$server_ip' and call_time < '$XDSQLdate' and status NOT IN('XFER','CLOSER','LIVE')";
		$affected_rows = $dbhA->do($stmtA);

		$event_string = "|     lagged call vac agent DELETED $affected_rows|$XDSQLdate|\n$stmtA";
		 &event_logger;


		### For debugging purposes, try to grab Jammed calls and log them to jam logfile
		if ($useJAMdebugFILE)
			{
			$stmtA = "SELECT * FROM osdial_auto_calls where server_ip='$server_ip' and last_update_time < '$BDtsSQLdate' and status IN('LIVE')";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$JAMrec_count=0;
			while ($sthArows > $JAMrec_count)
				{
				@aryA = $sthA->fetchrow_array;
				$jam_string = "$JAMrec_count|$BDtsSQLdate|     |$aryA[0]|$aryA[1]|$aryA[2]|$aryA[3]|$aryA[4]|$aryA[5]|$aryA[6]|$aryA[7]|$aryA[8]|$aryA[9]|$aryA[10]|$aryA[11]|$aryA[12]|$aryA[13]|$aryA[14]|";
				 &jam_event_logger;
				$JAMrec_count++;
				}
			$sthA->finish();
			}

		### find call records that are LIVE and not updated for over 10 seconds
		$stmtA = "SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid from osdial_auto_calls where server_ip='$server_ip' and last_update_time < '$BDtsSQLdate' and status IN('LIVE');";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			@aryA = $sthA->fetchrow_array;
				$auto_call_id	= "$aryA[0]";
				$CLlead_id		= "$aryA[1]";
				$CLphone_number	= "$aryA[2]";
				$CLstatus		= "$aryA[3]";
				$CLcampaign_id	= "$aryA[4]";
				$CLphone_code	= "$aryA[5]";
				$CLalt_dial		= "$aryA[6]";
				$CLstage		= "$aryA[7]";
				$CLcallerid		= "$aryA[8]";
				$CLuniqueid		= "$aryA[9]";
			$rec_count++;
			}
		$sthA->finish();

		### delete call records that are LIVE and not updated for over 10 seconds
		$rec_count=0;
		while ($sthArows > $rec_count)
			{
			$stmtA = "DELETE from osdial_auto_calls where auto_call_id='$auto_call_id'";
			$affected_rows = $dbhA->do($stmtA);

			$event_string = "|     lagged call vdac call DELETED $affected_rows|$BDtsSQLdate|$auto_call_id|$CLcallerid|$CLuniqueid|$CLphone_number|$CLstatus|";
			 &event_logger;

			if ( ($affected_rows > 0) && ($CLlead_id > 0) ) 
				{
				$jam_string = "|     lagged call vdac call DELETED $affected_rows|$BDtsSQLdate|$auto_call_id|$CLcallerid|$CLuniqueid|$CLphone_number|$CLstatus|";
				 &jam_event_logger;

				$CLstage =~ s/LIVE|-//gi;
				if ($CLstage < 0.25) {$CLstage=1;}

				$end_epoch = ($now_date_epoch + 1);
				$stmtA = "INSERT INTO osdial_log (uniqueid,lead_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,processed,length_in_sec,end_epoch) values('$CLuniqueid','$CLlead_id','$CLcampaign_id','$SQLdate','$now_date_epoch','DROP','$CLphone_code','$CLphone_number','VDAD','N','$CLstage','$end_epoch')";
					if($M){print STDERR "\n|$stmtA|\n";}
				$affected_rows = $dbhA->do($stmtA);

				$event_string = "|     dead NA call added to log $CLuniqueid|$CLlead_id|$CLphone_number|$CLstatus|DROP|$affected_rows|";
				 &event_logger;

				if ($enable_queuemetrics_logging > 0)
					{
					$dbhB = DBI->connect("DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306", "$queuemetrics_login", "$queuemetrics_pass")
					 or die "Couldn't connect to database: " . DBI->errstr;

					if ($DBX) {print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n";}

					$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secX',call_id='$CLcallerid',queue='$CLcampaign_id',agent='NONE',verb='ABANDON',data1='1',data2='1',data3='$CLstage',serverid='$queuemetrics_log_id';";
					$Baffected_rows = $dbhB->do($stmtB);

					$dbhB->disconnect();
					}

				}

			$rec_count++;
			}
		$sthA->finish();


		if ($LUcount > 0)
			{
			chop($lists_update);
			$stmtA = "UPDATE osdial_lists SET list_lastcalldate='$SQLdate' where list_id IN($lists_update);";
			$affected_rows = $dbhA->do($stmtA);
			$event_string = "|     lastcalldate UPDATED $affected_rows|$lists_update|";
			 &event_logger;
			}

		if ($CPcount > 0)
			{
			chop($campaigns_update);
			$stmtA = "UPDATE osdial_campaigns SET campaign_logindate='$SQLdate' where campaign_id IN($campaigns_update);";
			$affected_rows = $dbhA->do($stmtA);
			$event_string = "|     logindate UPDATED $affected_rows|$campaigns_update|";
			 &event_logger;
			}


	###############################################################################
	###### last, wait for a little bit and repeat the loop
	###############################################################################

		### sleep for 2 and a half seconds before beginning the loop again
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

			### delete call records that are LIVE/XFER for over 100 minutes
			$stmtA = "DELETE FROM osdial_auto_calls where server_ip='$server_ip' and call_time < '$TDSQLdate' and status NOT IN('XFER','CLOSER')";
			$affected_rows = $dbhA->do($stmtA);

			$event_string = "|     lagged call vac agent DELETED $affected_rows|$TDSQLdate|LIVE|\n$stmtA";
			 &event_logger;

			### Grab Server values from the database in case they've changed
			$stmtA = "SELECT max_osdial_trunks,answer_transfer_agent,local_gmt,ext_context FROM servers where server_ip = '$server_ip';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			$rec_count=0;
			while ($sthArows > $rec_count)
				{
				@aryA = $sthA->fetchrow_array;
					$DBmax_osdial_trunks	=	"$aryA[0]";
					$DBanswer_transfer_agent=	"$aryA[1]";
					$DBSERVER_GMT		=		"$aryA[2]";
					$DBext_context	=			"$aryA[3]";
						$max_osdial_trunks = $DBmax_osdial_trunks;
					if ($DBanswer_transfer_agent)	{$answer_transfer_agent = $DBanswer_transfer_agent;}
					if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
					if ($DBext_context)				{$ext_context = $DBext_context;}
				$rec_count++;
				}
			$sthA->finish();

			$event_string = "|     updating server parameters $max_osdial_trunks|$answer_transfer_agent|$SERVER_GMT|$ext_context|";
			 &event_logger;

				&get_time_now;

			#@psoutput = `/bin/ps -f --no-headers -A`;
			@psoutput = `/bin/ps -o "%p %a" --no-headers -A`;

			$running_listen = 0;

			$i=0;
			foreach (@psoutput)
				{
					chomp($psoutput[$i]);

				@psline = split(/\/usr\/bin\/perl /,$psoutput[$i]);

				if ($psline[1] =~ /AST_manager_li/) {$running_listen++;}

				$i++;
				}

			if (!$running_listen) 
				{
				$endless_loop=0;
				$one_day_interval=0;
				print "\nPROCESS KILLED NO LISTENER RUNNING... EXITING\n\n";
				}

			if($DB){print "checking to see if listener is dead |$running_listen|\n";}
			}

		$bad_grabber_counter=0;

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

$TDtarget = ($secX - 6000);
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

sub jam_event_logger
{
if ($DB) {print "$now_date|$jam_string|\n";}
if ($useJAMdebugFILE)
	{
	### open the log file for writing ###
	open(Jout, ">>$JAMdebugFILE")
			|| die "Can't open $JAMdebugFILE: $!\n";
	print Jout "$now_date|$jam_string|\n";
	close(Jout);
	}
$jam_string='';
}


