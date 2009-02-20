#!/usr/bin/perl
#
# AST_VDadapt.pl version 2.0.4   *DBI-version*
#
# DESCRIPTION:
# adjusts the auto_dial_level for vicidial adaptive-predictive campaigns.
#
# Copyright (C) 2007  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# CHANGELOG
# 60823-1302 - First build from AST_VDhopper.pl
# 60825-1734 - Functional alpha version, no loop
# 60826-0857 - Added loop and CLI flag options
# 60827-0035 - Separate Drop calculation and target dial level calculation into different subroutines
#            - Alter code so that DROP percentages would calculate only about once a minute no matter he loop delay
# 60828-1149 - Add field for target dial_level difference, -1 would target one agent waiting, +1 would target 1 customer waiting
# 60919-1243 - Changed variables to use arrays for all campaign-specific values
# 61215-1110 - Added answered calls stats and use drops as percentage of answered for today
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70205-1429 - Added code for campaign_changedate and campaign_stats_refresh updates
# 70213-1221 - Added code for QueueMetrics queue_log QUEUESTART record
# 70219-1249 - Removed unused references to dial_status_x fields
# 70409-1219 - Removed CLOSER-type campaign restriction
# 70521-1038 - Fixed bug when no live campaigns are running, define $vicidial_log
# 70619-1339 - Added Status Category tally calculations
# 71029-1906 - Changed CLOSER-type campaign_id restriction
# 80901-2234 - Cleaned code to conform with Perl formatting. lc
# 80902-0147 - Renamed all CLI* variables to CLO*, lc
# 80902-0149 - Add GetOpt::Long to handle command-line options, lc
# 80903-0013 - Removal of code relavant to campaign_stats computations. lc
# 80904-1231 - Complete restructure of code using strict, variable declaration. lc
#            - Elimination of unused variables. lc
#            - Apply correct data-types to variables, ie use scalar instead of array. lc
# 80904-1454 - Initialize stat counters prior to inital evaluation. lc
#            - Change behavior of stat avgs to only ever occupy a 15 element array. lc


use strict;
use DBI;
use Getopt::Long;
$|++;

my $prog = "AST_VDadapt.pl";

my $vicidial_log = 'vicidial_log FORCE INDEX (call_date) ';
#$vicidial_log = 'vicidial_log';

my $secT = time();

# Get AGC configuration directives.
my $config = getAGCconfig('/etc/astguiclient.conf');

my ($dbhA,$stmtA,$sthA,$sthArows,$rec_count,$affected_rows);
my ($DB,$DBX,$CLOhelp,$CLOcampaign,$CLOforce,$CLOloops,$CLOdelay,$CLOtest,$CLOminlevel,$CLOoverlimitmod,$limitmod_inc,$event_ext);

### begin parsing run-time options ###
$CLOloops = 1000000;
$CLOdelay = 1;
$config->{VARadapt_min_level} = 1 unless ($config->{VARadapt_min_level});
$CLOminlevel = $config->{VARadapt_min_level} * 1;
$config->{VARadapt_overlimit_mod} = 1 unless ($config->{VARadapt_overlimit_mod}); 
$CLOoverlimitmod = (1000 - $config->{VARadapt_overlimit_mod}) / 1000;
$limitmod_inc = (1000 + $config->{VARadapt_overlimit_mod}) / 1000;

if (scalar @ARGV) {
	GetOptions(
		'help!' => \$CLOhelp,
		'loops=i' => \$CLOloops,
		'minlevel=i' => \$CLOminlevel,
		'overlimitmod=i' => \$CLOoverlimitmod,
		'delay=i' => \$CLOdelay,
		'campaign=s' => \$CLOcampaign,
		'debug!' => \$DB,
		'debugX!' => \$DBX,
		'force!' => \$CLOforce,
		'test!' => \$CLOtest
	);
	$DB = 1 if ($DBX);
	$event_ext = ".test" if ($CLOtest);
	if ($DB) {
		print "----- DEBUGGING -----\n";
		print "----- EXTRA-VERBOSE DEBUGGING -----\n" if ($DBX);
		print "----- Testing Mode -----\n" if ($CLOtest);
		print "VARS-\n";
		print "CLOcampaign- $CLOcampaign\n";
		print "CLOloops-    $CLOloops\n";
		print "CLOdelay-    $CLOdelay\n";
		print "\n";
	}
	if ($CLOhelp) {
		print "\n\n" . $prog . "\n";
		print "allowed run-time options:\n";
		print "  [--help]              = This screen\n";
		print "  [--loops=XXX]        = force a number of loops of XXX\n";
		print "  [--delay=XXX]        = force a loop delay of XXX seconds\n";
		print "  [--campaign=XXX]     = run for campaign XXX only\n";
		print "  [--minlevel=XXX]     = minimum dial level, XXX\n";
		print "  [--overlimitmod=XXX] = dropped/over limit dial level mod, XXX\n";
		print "  [--debug]            = debug\n";
		print "  [--debugX]           = super debug\n";
		print "  [-f|--force]         = force calculation of suggested predictive dial_level\n";
		print "  [-t|--test]          = test only, do not alter dial_level\n\n";
		exit 0;
	}
}	

# Intiial connection to database.
$dbhA = DBI->connect( 'DBI:mysql:' . $config->{VARDB_database} . ':' . $config->{VARDB_server} . ':' . $config->{VARDB_port}, $config->{VARDB_user}, $config->{VARDB_pass} )
  or die "Couldn't connect to database: " . DBI->errstr;
print 'CONNECTED TO DATABASE:  ' . $config->{VARDB_server} . '|' . $config->{VARDB_database} . "\n" if ($DBX);

#############################################
##### START QUEUEMETRICS LOGGING LOOKUP #####
$stmtA = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows  = $sthA->rows;
$rec_count = 0;

my ($enable_queuemetrics_logging,$queuemetrics_server_ip,$queuemetrics_dbname,$queuemetrics_login,$queuemetrics_pass,$queuemetrics_log_id);
while ( $sthArows > $rec_count ) {
	my @aryA                     = $sthA->fetchrow_array;
	$enable_queuemetrics_logging = $aryA[0];
	$queuemetrics_server_ip      = $aryA[1];
	$queuemetrics_dbname         = $aryA[2];
	$queuemetrics_login          = $aryA[3];
	$queuemetrics_pass           = $aryA[4];
	$queuemetrics_log_id         = $aryA[5];
	$rec_count++;
}
$sthA->finish();

my ($dbhB,$stmtB,$Baffected_rows);
if ( $enable_queuemetrics_logging > 0 ) {
	$dbhB = DBI->connect(
		"DBI:mysql:$queuemetrics_dbname:$queuemetrics_server_ip:3306",
		"$queuemetrics_login", "$queuemetrics_pass" )
	  or die "Couldn't connect to database: " . DBI->errstr;

	print "CONNECTED TO DATABASE:  $queuemetrics_server_ip|$queuemetrics_dbname\n" if ($DBX);

	$stmtB = "INSERT INTO queue_log SET partition='P01',time_id='$secT',call_id='NONE',queue='NONE',agent='NONE',verb='QUEUESTART',serverid='$queuemetrics_log_id';";
	if ($CLOtest) {
		print $stmtB;
	} else {
		$Baffected_rows = $dbhB->do($stmtB);
	}
	$dbhB->disconnect();
}
##### END QUEUEMETRICS LOGGING LOOKUP #####
###########################################
my ($diff_ratio_updater,$RESETdiff_ratio_updater) = (0,0);
my ($master_loop,$stat_count) = (0,0);
my (@stat_ready_agents,@stat_waiting_calls,@stat_total_agents);

### Start master loop ###
while ( $master_loop < $CLOloops ) {
	my (@campaign_id,@auto_dial_level,@dial_method,@available_only_ratio_tally,@adaptive_dropped_percentage,$swhere);
	my (@adaptive_maximum_level,@adaptive_latest_server_time,@adaptive_intensity,@adaptive_dl_diff_target,@campaign_allow_inbound);

	#Get times
	my $secX = time();
	my $VDL_date =     logDate($secX);
	my $VDL_hour =     nowDate($secX - 3600);
	my $VDL_halfhour = nowDate($secX - 1800);
	my $VDL_five =     nowDate($secX - 300);
	my $VDL_one =      nowDate($secX - 60);
	my $VDL_ninty =            $secX - 90;
	my @tchm = split / |-|=|:/, nowDate($secX);
	my $current_hourmin = $tchm[3] . $tchm[4];

	### Grab Server values from the database
	my ($serverConfig,$SERVER_GMT,$SYSLOG);
	$serverConfig = getServerConfig($dbhA,$config->{VARserver_ip});
	$SERVER_GMT = $serverConfig->{local_gmt};
	$SYSLOG = 1 if ($serverConfig->{vd_server_logs} eq 'Y');

	#Process campaigns
	if ($CLOcampaign) {
		$swhere = "campaign_id='$CLOcampaign'";
	} else {
		$swhere = "( (active='Y') or (campaign_stats_refresh='Y') )";
	}
	$stmtA = "SELECT campaign_id,auto_dial_level,dial_method,available_only_ratio_tally,adaptive_dropped_percentage,adaptive_maximum_level,adaptive_latest_server_time,adaptive_intensity,adaptive_dl_diff_target,campaign_allow_inbound from vicidial_campaigns where " . $swhere;
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows  = $sthA->rows;
	$rec_count = 0;
	while ( $sthArows > $rec_count ) {
#TODO Use fetchrow_hashref
		my @aryA = $sthA->fetchrow_array;
		$campaign_id[$rec_count]                 = $aryA[0];
		$auto_dial_level[$rec_count]             = $aryA[1];
		$dial_method[$rec_count]                 = $aryA[2];
		$available_only_ratio_tally[$rec_count]  = $aryA[3];
		$adaptive_dropped_percentage[$rec_count] = $aryA[4];
		$adaptive_maximum_level[$rec_count]      = $aryA[5];
		$adaptive_latest_server_time[$rec_count] = $aryA[6];
		$adaptive_intensity[$rec_count]          = $aryA[7];
		$adaptive_dl_diff_target[$rec_count]     = $aryA[8];
		$campaign_allow_inbound[$rec_count]      = $aryA[9];
		$rec_count++;
	}
	$sthA->finish();
	print "$VDL_date CAMPAIGNS TO PROCESSES ADAPT FOR:  $rec_count|$#campaign_id       SIT: $stat_count   IT: $master_loop\n" if ($DB);

	##### LOOP THROUGH EACH CAMPAIGN AND PROCESS THE HOPPER #####
	my $i=0;
	foreach (@campaign_id) {
		my($VCSagent_count,$VCSagent_status,$VCSINCALL,$VCSREADY,$VCSCLOSER,$VCSPAUSED,$ready_agents,$total_agents,$VCSagents,$VCSagents_calc,$VCSagents_active);
		my($differential_target,$differential_mul,$differential_pct_raw,$differential_pct,$intensity_mul,$abs_intensity_mul,$intensity_diff,$intensity_pct);
		my($intensity_diff_mul,$suggested_dial_level,$intensity_dial_level,$last_target_hour_final,$tapered_hours_left,$tapered_rate,$adaptive_string);
		my($differential_onemin,$agents_average_onemin,$waiting_calls,$ready_diff_total,$stat_differential) = (0,0,0,0,0);
		my($waiting_diff_total,$total_agents_total,$ready_diff_avg,$waiting_diff_avg,$total_agents_avg) = (0,0,0,0,0);
		
		my($VCScalls_today,$VCSanswers_today,$VCSdrops_today,$VCSdrops_today_pct,$VCSdrops_answers_today_pct);
		my($VCScalls_hour,$VCSanswers_hour,$VCSdrops_hour,$VCSdrops_hour_pct);
		my($VCScalls_halfhour,$VCSanswers_halfhour,$VCSdrops_halfhour,$VCSdrops_halfhour_pct);
		my($VCScalls_five,$VCSanswers_five,$VCSdrops_five,$VCSdrops_five_pct);
		my($VCScalls_one,$VCSanswers_one,$VCSdrops_one,$VCSdrops_one_pct);
			
		### Find out how many leads are in the hopper from a specific campaign
		my $hopper_ready_count = 0;
		$stmtA = "SELECT count(*) from vicidial_hopper where campaign_id='$campaign_id[$i]' and status='READY';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA = $sthA->fetchrow_array;
			$hopper_ready_count = $aryA[0];
			print "     $campaign_id[$i] hopper READY count:   $hopper_ready_count\n"  if ($DB);
			$rec_count++;
		}
		$sthA->finish();
		my $event_string = "|$campaign_id[$i]|$hopper_ready_count|$diff_ratio_updater|";
		print "$i     $event_string\n" if ($DBX);
		eventLogger($config->{PATHlogs},"adapt" . $event_ext,$event_string) if ($SYSLOG);

		##### IF THERE ARE NO LEADS IN THE HOPPER FOR THE CAMPAIGN WE DO NOT WANT TO ADJUST THE DIAL_LEVEL
		if ( $hopper_ready_count > 0 ) {
			### BEGIN - GATHER STATS FOR THE vicidial_campaign_stats TABLE ###
			$stmtA = "SELECT count(*),status from vicidial_live_agents where campaign_id='$campaign_id[$i]' and last_update_time > '$VDL_one' group by status;";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows  = $sthA->rows;
			$rec_count = 0;
			while ( $sthArows > $rec_count ) {
				my @aryA = $sthA->fetchrow_array;
				$VCSagent_count  = $aryA[0];
				$VCSagent_status = $aryA[1];
				$VCSINCALL    += $VCSagent_count if ( $VCSagent_status =~ /INCALL|QUEUE/ );
				$VCSREADY     += $VCSagent_count if ( $VCSagent_status =~ /READY|DONE/ );
				$VCSCLOSER    += $VCSagent_count if ( $VCSagent_status eq 'CLOSER' );
				$VCSPAUSED    += $VCSagent_count if ( $VCSagent_status eq 'PAUSED' );
				$VCSagents    += $VCSagent_count;
				$rec_count++;
			}
			$sthA->finish();
									
			if ( $available_only_ratio_tally[$i] eq 'Y' ) {
				$VCSagents_calc = $VCSREADY + $VCSCLOSER;
			} else {
				$VCSagents_calc = $VCSINCALL + $VCSREADY + $VCSCLOSER;
			}
			$VCSagents_active = $VCSINCALL + $VCSREADY + $VCSCLOSER;

		
			$stmtA = "SELECT count(*) FROM vicidial_auto_calls where campaign_id='$campaign_id[$i]' and status IN('LIVE','CLOSER');";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows  = $sthA->rows;
			$rec_count = 0;
			while ( $sthArows > $rec_count ) {
				my @aryA = $sthA->fetchrow_array;
				$waiting_calls = $aryA[0];
				$rec_count++;
			}
			$sthA->finish();

			# Initialize Stat counters with current set if in first loop.
			if( $master_loop == 0) {
				foreach my $it (0..14) {
					$stat_ready_agents[$i][$it]  = $VCSREADY;
					$stat_waiting_calls[$i][$it] = $waiting_calls;
					$stat_total_agents[$i][$it]  = $VCSagents_calc;
				}
			}
			
			# Assign Stat counts.
			$stat_ready_agents[$i][$stat_count]  = $VCSREADY;
			$stat_waiting_calls[$i][$stat_count] = $waiting_calls;
			$stat_total_agents[$i][$stat_count]  = $VCSagents_calc;

			# Get last fifteen stat counts		
			foreach my $it (0..14) {
				$ready_diff_total   += $stat_ready_agents[$i][$it];
				$waiting_diff_total += $stat_waiting_calls[$i][$it];
				$total_agents_total += $stat_total_agents[$i][$it];
			}

			# Get stat averages
			$ready_diff_avg = $ready_diff_total / 15     if ( $ready_diff_total > 0);
			$waiting_diff_avg = $waiting_diff_total / 15 if ( $waiting_diff_total > 0);
			$total_agents_avg = $total_agents_total / 15 if ( $total_agents_total > 0);
			$stat_differential = $ready_diff_avg - $waiting_diff_avg;
		
			$event_string = "CAMPAIGN DIFFERENTIAL: $total_agents_avg   $stat_differential   ($ready_diff_avg - $waiting_diff_avg)";
			print "     $campaign_id[$i]\n  " if ($DBX);
			print "     $event_string\n"             if ($DB);
			eventLogger($config->{PATHlogs},"adapt" . $event_ext,$event_string) if ($SYSLOG);

			if ( $total_agents_avg > 0 ) {
				### Calculate and update Dial level every 15 seconds
				if ( $diff_ratio_updater >= 15 ) {
					$RESETdiff_ratio_updater++;

					# GET AVERAGES FROM THIS CAMPAIGN
					$stmtA = "SELECT calls_today,answers_today,drops_today,drops_today_pct,drops_answers_today_pct,calls_hour,answers_hour,drops_hour,drops_hour_pct,calls_halfhour,answers_halfhour,drops_halfhour,drops_halfhour_pct,calls_fivemin,answers_fivemin,drops_fivemin,drops_fivemin_pct,calls_onemin,answers_onemin,drops_onemin,drops_onemin_pct from vicidial_campaign_stats where campaign_id='$campaign_id[$i]';";
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows  = $sthA->rows;
					$rec_count = 0;
					while ( $sthArows > $rec_count ) {
#TODO Use fetchrow_hashref
						my @aryA                    = $sthA->fetchrow_array;
						$VCScalls_today             = $aryA[0];
						$VCSanswers_today           = $aryA[1];
						$VCSdrops_today             = $aryA[2];
						$VCSdrops_today_pct         = $aryA[3];
						$VCSdrops_answers_today_pct = $aryA[4];
						$VCScalls_hour              = $aryA[5];
						$VCSanswers_hour            = $aryA[6];
						$VCSdrops_hour              = $aryA[7];
						$VCSdrops_hour_pct          = $aryA[8];
						$VCScalls_halfhour          = $aryA[9];
						$VCSanswers_halfhour        = $aryA[10];
						$VCSdrops_halfhour          = $aryA[11];
						$VCSdrops_halfhour_pct      = $aryA[12];
						$VCScalls_five              = $aryA[13];
						$VCSanswers_five            = $aryA[14];
						$VCSdrops_five              = $aryA[15];
						$VCSdrops_five_pct          = $aryA[16];
						$VCScalls_one               = $aryA[17];
						$VCSanswers_one             = $aryA[18];
						$VCSdrops_one               = $aryA[19];
						$VCSdrops_one_pct           = $aryA[20];	
						$rec_count++;
					}
					$sthA->finish();

					$agents_average_onemin = $total_agents_avg;
					$differential_onemin   = $stat_differential;

					if ($dial_method[$i] =~ /ADAPT/ or $CLOforce) {
						# Calculate the optimal dial_level differential for the past minute
						$differential_target  = $differential_onemin + $adaptive_dl_diff_target[$i];
						$differential_mul     = $differential_target / $agents_average_onemin;
						$differential_pct_raw = $differential_mul * 100;
						$differential_pct     = sprintf( "%.2f", $differential_pct_raw);
				
						# Factor in the intensity setting
						$intensity_mul         = $adaptive_intensity[$i] / 100;
						if ($differential_pct_raw < 0) {
							$abs_intensity_mul = abs($intensity_mul - 1);
							$intensity_diff    = $differential_pct_raw * $abs_intensity_mul;
						} else {
							$intensity_diff    = $differential_pct_raw * ($intensity_mul + 1);
						}
						$intensity_pct         = sprintf( "%.2f", $intensity_diff);
						$intensity_diff_mul    = $intensity_diff / 100;

						# Suggested dial_level based on differential
						$suggested_dial_level = $auto_dial_level[$i] * ($differential_mul + 1);
						$suggested_dial_level = sprintf( "%.3f", $suggested_dial_level);
				
						# Suggested dial_level based on differential with intensity setting
						$intensity_dial_level = $auto_dial_level[$i] * ($intensity_diff_mul + 1);
						$intensity_dial_level = sprintf( "%.3f", $intensity_dial_level);
				
						# Calculate last timezone target for ADAPT_TAPERED
						$last_target_hour_final = $adaptive_latest_server_time[$i];
						$tapered_hours_left     = $last_target_hour_final - $current_hourmin;
						if ($tapered_hours_left > 1000) {
							$tapered_rate       = 1;
						} else {
							$tapered_rate       = $tapered_hours_left / 1000;
						}
				
						$adaptive_string = "\n";
						$adaptive_string .= "CAMPAIGN:   $campaign_id[$i]\n";
						$adaptive_string .= "SETTINGS-\n";
						$adaptive_string .= "   DIAL LEVEL:    $auto_dial_level[$i]\n";
						$adaptive_string .= "   DIAL METHOD:   $dial_method[$i]\n";
						$adaptive_string .= "   AVAIL ONLY:    $available_only_ratio_tally[$i]\n";
						$adaptive_string .= "   DROP PERCENT:  $adaptive_dropped_percentage[$i]\n";
						$adaptive_string .= "   MAX LEVEL:     $adaptive_maximum_level[$i]\n";
						$adaptive_string .= "   SERVER TIME:   $current_hourmin\n";
						$adaptive_string .= "   LATE TARGET:   $last_target_hour_final     ($tapered_hours_left left|$tapered_rate)\n";
						$adaptive_string .= "   INTENSITY:     $adaptive_intensity[$i]\n";
						$adaptive_string .= "   DLDIFF TARGET: $adaptive_dl_diff_target[$i]\n";
						$adaptive_string .= "CURRENT STATS-\n";
						$adaptive_string .= "   AVG AGENTS:      $agents_average_onemin\n";
						$adaptive_string .= "   AGENTS:          $VCSagents  ACTIVE: $VCSagents_active   CALC: $VCSagents_calc  INCALL: $VCSINCALL    READY: $VCSREADY\n";
						$adaptive_string .= "   DL DIFFERENTIAL: $differential_target = ($differential_onemin + $adaptive_dl_diff_target[$i])\n";
						$adaptive_string .= "DIAL LEVEL SUGGESTION-\n";
						$adaptive_string .= "      PERCENT DIFF: $differential_pct\n";
						$adaptive_string .= "      SUGGEST DL:   $suggested_dial_level = ($auto_dial_level[$i] * ($differential_mul + 1) )\n";
						$adaptive_string .= "      INTENSE DIFF: $intensity_pct\n";
						$adaptive_string .= "      INTENSE DL:   $intensity_dial_level = ($auto_dial_level[$i] * ($intensity_diff_mul + 1) )\n";
				
						if ($intensity_dial_level > $adaptive_maximum_level[$i]) {
							$adaptive_string .= "      DIAL LEVEL OVER CAP! SETTING TO CAP: $adaptive_maximum_level[$i]\n";
							$intensity_dial_level = $adaptive_maximum_level[$i];
						}
						if ($intensity_dial_level < $CLOminlevel) {
							$adaptive_string .= "      DIAL LEVEL TOO LOW! SETTING TO $CLOminlevel\n";
							$intensity_dial_level = $CLOminlevel;
						}

						my $VCSdrops_answers_hour_pct = ($VCSdrops_hour / $VCSanswers_hour) * 100;
						my $VCSdrops_answers_halfhour_pct = ($VCSdrops_halfhour / $VCSanswers_halfhour) * 100;
						my $VCSdrops_answers_five_pct = ($VCSdrops_five / $VCSanswers_five) * 100;
						my $VCSdrops_answers_one_pct = ($VCSdrops_one / $VCSanswers_one) * 100;

						$adaptive_string .= "DROP STATS-\n";
						$adaptive_string .= "   TODAY:     $VCScalls_today   $VCSdrops_today   $VCSdrops_today_pct%\n";
						$adaptive_string .= "     ANSWERS:     $VCSanswers_today   $VCSdrops_answers_today_pct%\n\n";
						$adaptive_string .= "   ONE HOUR:  $VCScalls_hour   $VCSdrops_hour   $VCSdrops_hour_pct%\n";
						$adaptive_string .= "     ANSWERS:     $VCSanswers_hour   $VCSdrops_answers_hour_pct%\n\n";
						$adaptive_string .= "   HALF HOUR: $VCScalls_halfhour   $VCSdrops_halfhour   $VCSdrops_halfhour_pct%\n";
						$adaptive_string .= "     ANSWERS:     $VCSanswers_halfhour   $VCSdrops_answers_halfhour_pct%\n\n";
						$adaptive_string .= "   FIVE MIN:  $VCScalls_five   $VCSdrops_five   $VCSdrops_five_pct%\n";
						$adaptive_string .= "     ANSWERS:     $VCSanswers_five   $VCSdrops_answers_five_pct%\n\n";
						$adaptive_string .= "   ONE MIN:   $VCScalls_one   $VCSdrops_one   $VCSdrops_one_pct%\n";
						$adaptive_string .= "     ANSWERS:     $VCSanswers_one   $VCSdrops_answers_one_pct%\n";
						
						# If > 20 call in last minute and drop percentage > 50% scale back.
						if ($VCScalls_one > 20 and $VCSdrops_one_pct > 50) {
							# Reduce dial level by factor of 75% of CLOoverlimitmod, default .999.
							$intensity_dial_level = $auto_dial_level[$i] * $CLOoverlimitmod * .75;
							$adaptive_string .= "      DROP RATE OVER 50% FOR LAST MINUTE! CUTTING DIAL LEVEL TO: $intensity_dial_level\n";
						} elsif ($VCScalls_today > 50 and $VCSdrops_answers_today_pct > $adaptive_dropped_percentage[$i]) {
							if ($dial_method[$i] eq 'ADAPT_HARD_LIMIT') {
								$intensity_dial_level = $CLOminlevel;
								$adaptive_string .= "      DROP RATE OVER HARD LIMIT FOR TODAY! HARD DIAL LEVEL TO: $CLOminlevel\n";
							} elsif ($dial_method[$i] eq 'ADAPT_AVERAGE') {
								
								if ($VCSdrops_answers_five_pct <= $adaptive_dropped_percentage[$i] and $VCSdrops_answers_one_pct <= $adaptive_dropped_percentage[$i]) {
									# Good drop pct for five minutes, increasing dial level by factor of limitmod_inc
									$intensity_dial_level = $auto_dial_level[$i] * $limitmod_inc;
									$adaptive_string .= "      DROP RATE OVER LIMIT FOR TODAY! STABLE FOR FIVE MIN, INCREASING DIAL LEVEL TO: $intensity_dial_level\n";
								} elsif ($VCSdrops_answers_one_pct <= $adaptive_dropped_percentage[$i]) {
									# Good drop pct for one minute, leave alone.
									$intensity_dial_level = $auto_dial_level[$i];
									$adaptive_string .= "      DROP RATE OVER LIMIT FOR TODAY! STABLE FOR ONE MIN, LEAVING DIAL LEVEL AT: $intensity_dial_level\n";
								} else {
									# Slowly reduce dial level by factor of CLOoverlimitmod.
									$intensity_dial_level = $auto_dial_level[$i] * $CLOoverlimitmod;
									$adaptive_string .= "      DROP RATE OVER LIMIT FOR TODAY! FACTORING DIAL LEVEL TO: $intensity_dial_level\n";
								}
							} elsif ($dial_method[$i] eq 'ADAPT_TAPERED') {
								if ( $tapered_hours_left < 0 ) {
									$intensity_dial_level = $CLOminlevel;
									$adaptive_string .= "      DROP RATE OVER LAST HOUR LIMIT FOR TODAY! TAPERING DIAL LEVEL TO: $CLOminlevel\n";
								} else {
									$intensity_dial_level = $intensity_dial_level * $tapered_rate;
									$adaptive_string .= "      DROP RATE OVER LIMIT FOR TODAY! TAPERING DIAL LEVEL TO: $intensity_dial_level\n";
								}
							}
						}
				
						if ($intensity_dial_level > $adaptive_maximum_level[$i]) {
							$adaptive_string .= "      DIAL LEVEL OVER CAP! SETTING TO CAP: $adaptive_maximum_level[$i]\n";
							$intensity_dial_level = $adaptive_maximum_level[$i];
						}
						### ALWAYS RAISE DIAL_LEVEL TO $CLOminlevel IF IT IS LOWER ###
						if ($intensity_dial_level < $CLOminlevel) {
							$adaptive_string .= "      DIAL LEVEL TOO LOW! SETTING TO $CLOminlevel\n";
							$intensity_dial_level = $CLOminlevel;
						}
						$stmtA = "UPDATE vicidial_campaigns SET auto_dial_level='$intensity_dial_level' where campaign_id='$campaign_id[$i]';";
						# Do not execute if -t is set, only print.
						if ($CLOtest) {
							print $stmtA . "\n";
						} else {
							$affected_rows = $dbhA->do($stmtA);
						}
						$adaptive_string .= "DIAL LEVEL UPDATED TO: $intensity_dial_level          CONFIRM: $affected_rows\n";
					}
					print 'campaign stats updated:  ' . $campaign_id[$i] . '   ' . $adaptive_string . "\n" if ($DB);
					eventLogger($config->{PATHlogs},"VDadaptive-".$campaign_id[$i].$event_ext,$adaptive_string) if ($SYSLOG);
				}
			}
		}
		$i++;
	}

	# Reset the update counters
	if ( $RESETdiff_ratio_updater > 0 ) {
		$RESETdiff_ratio_updater = 0;
		$diff_ratio_updater      = 0;
	}
	$diff_ratio_updater += $CLOdelay;
	sleep($CLOdelay);

	# Increment stat counters, reset to 0 on 15 iterations.
	$stat_count++;
	$stat_count = 0 if ($stat_count > 14);
	
	# Increment current loop count.
	$master_loop++;
}

$dbhA->disconnect();

if ($DB) {
	### calculate time to run script ###
	my $secDone = time() - $secT;
	print "DONE. Script execution time in seconds: $secDone\n";
}

exit;

### Lott's subs, Mmmmmmm, tasty ###


# getAGCconfig usage:
#    $config = getAGCconfig($agcConfigPath);
# Requires:
#    $agcConfigPath : Usually '/etc/astguiclient.conf'
# Returns:
#    hashref with configuration directives in listed file.
sub getAGCconfig {
	my($AGCpath) = @_;
	my %config;
	$config{PATHconf} = $AGCpath;

	# Begin Parsing astguiclient config file.
	open(CONF, $config{PATHconf}) || die "can't open " . $config{PATHconf} . ": " . $! . "\n";
	while (my $line = <CONF>) {
		$line =~ s/ |>|"|\n|\r|\t|\#.*|;.*//gi;
		if ($line =~ /=/) {
			my($key,$val) = split /=/, $line;
			$config{$key} = $val;
		}
	}
	$config{VARDB_port} = '3306' unless ($config{VARDB_port});
	$config{VARFTP_port} = '21' unless ($config{VARFTP_port});
	$config{VARREPORT_port} = '21' unless ($config{VARREPORT_port});

	return \%config;
}


# getServerConfig usage:
#    $serverConfig = getServerConfig($dbh, $serverIP);
# Requires:
#    $dbh      : Database handle to current open DB.
#    $serverIP : IP of server to get config for.
# Returns:
#    hashref with conents of table entry.
sub getServerConfig {
	my ($dbhA, $serverip) = @_;
	my $stmtA = "SELECT * FROM servers where server_ip = '" . $serverip ."';";
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: " . $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA " . $dbhA->errstr;
	my $servConf = $sthA->fetchrow_hashref;
	$sthA->finish();
	return $servConf;
}

# eventLogger usage:
#    eventLgger($LogFileDir, $LogType, $EventString);
# Requires:
#    $LogFilePath : Directory where log files are.
#    $LogType     : Type of log, ie process, send, launch, full
#    $EventString : String to record in log.
sub eventLogger {
	my ($path,$type,$string) = @_;
	open(LOG, ">>" . $path . "/" . $type . "." . logDate())
		|| die "Can't open " . $path . "/" . $type . "." .
		logDate() . ": " . $! . "\n";
	print LOG nowDate() . "|" . $string . "|\n";
	close(LOG);
}

# getTime usage:
#   getTime($SecondsSinceEpoch);
# Options:
#   $SecondsSinceEpoch : Request time in seconds, defaults to current date/time.
# Returns:
#   ($sec, $min, $hour. $day, $mon, $year)
sub getTime {
	my ($tms) = @_;
	$tms = time unless ($tms);
	my($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime($tms);
	$year += 1900;
	$mon++;
	$mon = "0" . $mon if ($mon < 10);
	$mday = "0" . $mday if ($mday < 10);
	$min = "0" . $min if ($min < 10);
	$sec = "0" . $sec if ($sec < 10);
	return ($sec,$min,$hour,$mday,$mon,$year);
}

# nowDate usage:
#   nowDate($SecondsSinceEpoch);
# Options:
#   $SecondsSinceEpoch : Request time in seconds, defaults to current date/time.
# Returns:
#   scalar date/time string (MySQL formatted) ie "2007-01-01 00:00:00"
sub nowDate {
	my ($tms) = @_;
	my($sec,$min,$hour,$mday,$mon,$year) = getTime($tms);
	return $year.'-'.$mon.'-'.$mday.' '.$hour.':'.$min.':'.$sec;
}

# logDate usage:
#   logDate($SecondsSinceEpoch);
# Options:
#   $SecondsSinceEpoch : Request time in seconds, defaults to current date/time.
# Returns:
#   scalar date string ie "2007-01-01"
sub logDate {
	my ($tms) = @_;
	my($sec,$min,$hour,$mday,$mon,$year) = getTime($tms);
	return  $year . '-' . $mon . '-' . $mday;
}

##### End subs

