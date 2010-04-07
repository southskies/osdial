#!/usr/bin/perl
#
# AST_VDcampaign_stats.pl
#
## Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
## Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
## Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
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
# Calculates and inserts data into osdial_campaign_stats
#
# Loosely based on AST_VDadapt.pl
# 80903-0013 - Initial build.
#
# 090420-0140 - Look for stats record, create it.
# 090511-2116 - Add status_category_hour_counts.

use strict;
use DBI;
use Getopt::Long;
$|++;

my $prog = "AST_VDcampaign_stats.pl";

my $secStart = time();


# Get AGC configuration directives.
my $config = getAGCconfig('/etc/osdial.conf');

my ($dbhA,$stmtA,$sthA,$sthArows,$rec_count);
my ($DB, $DBX, $CLOhelp, $CLOcampaign, $CLOrecalc, $CLOtest, $CLOloops, $CLOdelay, $event_ext);

### begin parsing run-time options ###
$CLOloops = 1000000;
$CLOdelay = 1;
$CLOrecalc = 10;


if (scalar @ARGV) {
	GetOptions(
		'help!' => \$CLOhelp,
		'loops=i' => \$CLOloops,
		'delay=i' => \$CLOdelay,
		'recalc=i' => \$CLOrecalc,
		'campaign=s' => \$CLOcampaign,
		'debug!' => \$DB,
		'debugX!' => \$DBX,
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
		print "CLOrecalc-   $CLOrecalc\n";
		print "CLOloops-    $CLOloops\n";
		print "CLOdelay-    $CLOdelay\n";
		print "\n";
	}
	if ($CLOhelp) {
		print "\n\n" . $prog . "\n";
		print "allowed run-time options:\n";
		print "  [--help]         = This screen\n";
		print "  [--loops=XXX]    = force a number of loops of XXX\n";
		print "  [--delay=XXX]    = force a loop delay of XXX seconds\n";
		print "  [--recalc=XXX]   = recalc stats every XXX loops\n";
		print "  [--campaign=XXX] = run for campaign XXX only\n";
		print "  [--debug]        = debug\n";
		print "  [--debugX]       = super debug\n";
		print "  [-t|--test]      = test only\n\n";
		exit 0;
	}
}	

# Intiial connection to database.
$dbhA = DBI->connect( 'DBI:mysql:' . $config->{VARDB_database} . ':' . $config->{VARDB_server} . ':' . $config->{VARDB_port}, $config->{VARDB_user}, $config->{VARDB_pass} )
  or die "Couldn't connect to database: " . DBI->errstr;
print 'CONNECTED TO DATABASE:  ' . $config->{VARDB_server} . '|' . $config->{VARDB_database} . "\n" if ($DBX);

# Check to make sure we have all the stats records.
#my $stmtA = "INSERT INTO osdial_campaign_stats (campaign_id) (SELECT campaign_id FROM osdial_campaigns) on duplicate key update campaign_id=VALUES(campaign_id);";
#my $affected_rows = $dbhA->do($stmtA);

my($master_loop, $stat_count) = (0,0);
my($drop_count_updater, $RESETdrop_count_updater) = (0,0);

### Start master loop ###
while ( $master_loop < $CLOloops ) {
	#Get times
	my $secX = time();
	my $VDL_date =     logDate($secX);
	my $VDL_hour =     nowDate($secX - 3600);
	my $VDL_halfhour = nowDate($secX - 1800);
	my $VDL_five =     nowDate($secX - 300);
	my $VDL_one =      nowDate($secX - 60);
	my $VDL_ninty =            $secX - 90;

	my($stmtA,$sthA,$sthArows,$rec_count);
	my(@campaign_id,@campaign_lastcall,@campaign_stats_refresh);
	
	
	
	# Process campaigns
	my($swhere);
	if ($CLOcampaign) {
		$swhere = "campaign_id='$CLOcampaign'";
	} else {
		$swhere = "( (active='Y') or (campaign_stats_refresh='Y') )";
	}
	$stmtA = "SELECT campaign_id,UNIX_TIMESTAMP(campaign_lastcall),campaign_stats_refresh,(TIME_TO_SEC(SUBTIME(TIME(SYSDATE()),TIME(campaign_lastcall)))/60) from osdial_campaigns where ". $swhere;
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows  = $sthA->rows;
	$rec_count = 0;
	while ( $sthArows > $rec_count ) {
		my @aryA = $sthA->fetchrow_array;
		if ($aryA[3] <= 121 or $aryA[2] eq "Y") {
			$campaign_id[$rec_count]                    = $aryA[0];
			$campaign_lastcall[$rec_count]            = $aryA[1];
			$campaign_stats_refresh[$rec_count]         = $aryA[2];
			$rec_count++;
		} else {
			$sthArows--;
		}
	}
	$sthA->finish();
	print logDate() . " CAMPAIGNS TO PROCESSES CAMPAIGN STATS FOR:  $rec_count|$#campaign_id       IT: $master_loop\n" if ($DB);

	##### LOOP THROUGH EACH CAMPAIGN AND PROCESS THE HOPPER #####
	my $i=0;
	foreach (@campaign_id) {
		my($total_agents,$total_agents_total,$total_agents_avg,@stat_total_agents);
		
		### Find out how many leads are in the hopper from a specific campaign
		my $hopper_ready_count = 0;
		$stmtA = "SELECT count(*) from osdial_hopper where campaign_id='$campaign_id[$i]' and status='READY';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA               = $sthA->fetchrow_array;
			$hopper_ready_count = $aryA[0];
			print "     $campaign_id[$i] hopper READY count:   $hopper_ready_count\n" if ($DB);
			$rec_count++;
		}
		$sthA->finish();
		my $event_string = "|$campaign_id[$i]|$hopper_ready_count|$drop_count_updater|";
		print "$i     $event_string\n" if ($DBX);

		if ( $campaign_stats_refresh[$i] =~ /Y/ ) {
			print "     REFRESH OVERRIDE: $campaign_id[$i]\n" if ($DB);
			updateStats($campaign_id[$i],$VDL_date,$VDL_one,$VDL_five,$VDL_hour,$VDL_halfhour);
			$RESETdrop_count_updater++;
			$stmtA = "UPDATE osdial_campaigns SET campaign_stats_refresh='N' where campaign_id='$campaign_id[$i]';";
			if ($CLOtest) {
				print $stmtA . "\n";
			} else {
				my $affected_rows = $dbhA->do($stmtA);
			}
		} elsif ( $campaign_lastcall[$i] >= $VDL_ninty ) {
			print "     CHANGEDATE OVERRIDE: $campaign_id[$i]\n" if ($DB);
			updateStats($campaign_id[$i],$VDL_date,$VDL_one,$VDL_five,$VDL_hour,$VDL_halfhour);
			$RESETdrop_count_updater++;
		} elsif ($drop_count_updater > $CLOrecalc) {
			print "     RECALC: $campaign_id[$i]\n" if ($DB);
			updateStats($campaign_id[$i],$VDL_date,$VDL_one,$VDL_five,$VDL_hour,$VDL_halfhour);
			$RESETdrop_count_updater++;
		}
		$i++;
	}

	if ( $RESETdrop_count_updater > 0 ) {
		$RESETdrop_count_updater = 0;
		$drop_count_updater      = 0;
	}
	$drop_count_updater += $CLOdelay;

	sleep($CLOdelay);

	$stat_count++;
	$master_loop++;
}

$dbhA->disconnect();

my $secTime = time() - $secStart;
print "DONE. Script execution time in seconds: " . $secTime . "\n" if ($DB);

exit 0;

sub updateStats {
	my ($campaign_id,$VDL_date,$VDL_one,$VDL_five,$VDL_hour,$VDL_halfhour) = @_;

	my $multi_sql='';
	my $stat_rows='';
	my $sstat_rows='';
	my $astat_rows='';

	my $VSCupdateSQLcol='';
	my $VSCupdateSQLdat='';
	my $g=0;
	while ( $g < 4 ) {
		$g++;
		$VSCupdateSQLcol .= "status_category_$g,status_category_count_$g,status_category_hour_count_$g,";
		$VSCupdateSQLdat .= "status_category_$g=VALUES(status_category_$g),status_category_count_$g=VALUES(status_category_count_$g),status_category_hour_count_$g=VALUES(status_category_hour_count_$g),";
	}
	chop($VSCupdateSQLcol);
	chop($VSCupdateSQLdat);
	
	my $osdial_log = 'osdial_log FORCE INDEX (call_date) ';
	#my $osdial_log = 'osdial_log';
	my $camp_ANS_STAT_SQL;
	
	my($stmtA,$sthA,$sthArows,$rec_count);
	my $VCSdrops_answers_today_pct=0;

	# GET LIST OF HUMAN-ANSWERED STATUSES
	$stmtA = "SELECT status from osdial_statuses where human_answered='Y';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows  = $sthA->rows;
	$rec_count = 0;
	while ( $sthArows > $rec_count ) {
		my @aryA = $sthA->fetchrow_array;
		$camp_ANS_STAT_SQL .= "'$aryA[0]',";
		$rec_count++;
	}
	$sthA->finish();

	$stmtA = "SELECT status from osdial_campaign_statuses where campaign_id='$campaign_id' and human_answered='Y';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows  = $sthA->rows;
	$rec_count = 0;
	while ( $sthArows > $rec_count ) {
		my @aryA = $sthA->fetchrow_array;
		$camp_ANS_STAT_SQL .= "'$aryA[0]',";
		$rec_count++;
	}
	$sthA->finish();
	chop($camp_ANS_STAT_SQL);

	print "     CAMPAIGN ANSWERED STATUSES: $campaign_id|$camp_ANS_STAT_SQL|\n" if ($DBX);

	# Gather call count data
	my ($VCScalls_today,$VCSanswers_today,$VCSdrops_today,$VCSdrops_today_pct) = calculateDrops ($dbhA, $osdial_log, $campaign_id, $camp_ANS_STAT_SQL, $VDL_date);
	my ($VCScalls_hour,$VCSanswers_hour,$VCSdrops_hour,$VCSdrops_hour_pct) = calculateDrops ($dbhA, $osdial_log, $campaign_id, $camp_ANS_STAT_SQL, $VDL_hour);
	my ($VCScalls_halfhour,$VCSanswers_halfhour,$VCSdrops_halfhour,$VCSdrops_halfhour_pct) = calculateDrops ($dbhA, $osdial_log, $campaign_id, $camp_ANS_STAT_SQL, $VDL_halfhour);
	my ($VCScalls_five,$VCSanswers_five,$VCSdrops_five,$VCSdrops_five_pct) = calculateDrops ($dbhA, $osdial_log, $campaign_id, $camp_ANS_STAT_SQL, $VDL_five);
	my ($VCScalls_one,$VCSanswers_one,$VCSdrops_one,$VCSdrops_one_pct) = calculateDrops ($dbhA, $osdial_log, $campaign_id, $camp_ANS_STAT_SQL, $VDL_one);
	$VCSdrops_answers_today_pct = ( ( $VCSdrops_today / $VCSanswers_today ) * 100 ) if ($VCSanswers_today > 0);
	$VCSdrops_answers_today_pct = sprintf( "%.2f", $VCSdrops_answers_today_pct );
	print "** $campaign_id|$VCSdrops_five_pct|$VCSdrops_today_pct|$VCSdrops_answers_today_pct\n" if ($DBX);

	# Get misc data, AMD/Fails
	my $camp_AMD_SQL = "'AA','AL','AM'";
	my ($AMDcalls_one,$AMDanswers_one,$AMDdrops_one,$AMDdrops_one_pct) = calculateDrops ($dbhA, $osdial_log, $campaign_id, $camp_AMD_SQL, $VDL_one);
	my $camp_FAIL_SQL = "'CRC','CRO','CRF','CRR'";
	my ($FAILcalls_one,$FAILanswers_one,$FAILdrops_one,$FAILdrops_one_pct) = calculateDrops ($dbhA, $osdial_log, $campaign_id, $camp_FAIL_SQL, $VDL_one);

	# DETERMINE WHETHER TO GATHER STATUS CATEGORY STATISTICS
	my @VSC_categories;
	my $VSCupdateSQL;
	$stmtA = "SELECT vsc_id from osdial_status_categories where tovdad_display='Y';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows  = $sthA->rows;
	$rec_count = 0;
	while ( $sthArows > $rec_count ) {
		my @aryA = $sthA->fetchrow_array;
		$VSC_categories[$rec_count] = $aryA[0];
		$rec_count++;
	}
	$sthA->finish();

	# Get status category counts
	my $g;
	foreach (@VSC_categories) {
		my $VSCcategory = $VSC_categories[$g];
		my $VSCtally;
		my $CATstatusesSQL;

		# FIND STATUSES IN STATUS CATEGORY
		$stmtA = "SELECT status from osdial_statuses where category='$VSCcategory';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA = $sthA->fetchrow_array;
			$CATstatusesSQL .= "'$aryA[0]',";
			$rec_count++;
		}

		# FIND CAMPAIGN_STATUSES IN STATUS CATEGORY
		$stmtA = "SELECT status from osdial_campaign_statuses where category='$VSCcategory' and campaign_id='$campaign_id';";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA = $sthA->fetchrow_array;
			$CATstatusesSQL .= "'$aryA[0]',";
			$rec_count++;
		}
		chop($CATstatusesSQL);
		if ( length($CATstatusesSQL) > 2 ) {

			# FIND STATUSES IN STATUS CATEGORY
			$stmtA = "SELECT count(*) from $osdial_log where campaign_id='$campaign_id' and call_date > '$VDL_date' and status IN($CATstatusesSQL);";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows  = $sthA->rows;
			$rec_count = 0;
			while ( $sthArows > $rec_count ) {
				my @aryA     = $sthA->fetchrow_array;
					$VSCtally = $aryA[0];
				$rec_count++;
			}
		} else {
			$CATstatusesSQL = "'----'";
		}
		# Get category count for last hour, as CATanswers_hour
		my ($CATcalls_hour,$CATanswers_hour,$CATdrops_hour,$CATdrops_hour_pct) = calculateDrops ($dbhA, $osdial_log, $campaign_id, $CATstatusesSQL, $VDL_hour);
		$g++;
		#$VSCupdateSQL .= "status_category_$g='$VSCcategory',status_category_count_$g='$VSCtally',status_category_hour_count_$g='$CATanswers_hour',";
		$VSCupdateSQL .= "'$VSCcategory','$VSCtally','$CATanswers_hour',";
		print "     $campaign_id|$g|$VSCcategory|$VSCtally|$CATanswers_hour|$CATstatusesSQL|\n" if ($DBX);
	}
	while ( $g < 4 ) {
		$g++;
		#$VSCupdateSQL .= "status_category_$g='',status_category_count_$g='0',status_category_hour_count_$g='0',";
		$VSCupdateSQL .= "'','0','0',";
	}
	chop($VSCupdateSQL);


	# Ok, write all the data we have collected to the campaigns stats table.
	#$stmtA = "UPDATE osdial_campaign_stats SET calls_today='$VCScalls_today',answers_today='$VCSanswers_today',drops_today='$VCSdrops_today',drops_today_pct='$VCSdrops_today_pct',drops_answers_today_pct='$VCSdrops_answers_today_pct',calls_hour='$VCScalls_hour',answers_hour='$VCSanswers_hour',drops_hour='$VCSdrops_hour',drops_hour_pct='$VCSdrops_hour_pct',calls_halfhour='$VCScalls_halfhour',answers_halfhour='$VCSanswers_halfhour',drops_halfhour='$VCSdrops_halfhour',drops_halfhour_pct='$VCSdrops_halfhour_pct',calls_fivemin='$VCScalls_five',answers_fivemin='$VCSanswers_five',drops_fivemin='$VCSdrops_five',drops_fivemin_pct='$VCSdrops_five_pct',calls_onemin='$VCScalls_one',answers_onemin='$VCSanswers_one',drops_onemin='$VCSdrops_one',drops_onemin_pct='$VCSdrops_one_pct',amd_onemin='$AMDanswers_one',failed_onemin='$FAILanswers_one',$VSCupdateSQL where campaign_id='$campaign_id';";
	$multi_sql = "('$campaign_id','$VCScalls_today','$VCSanswers_today','$VCSdrops_today','$VCSdrops_today_pct','$VCSdrops_answers_today_pct','$VCScalls_hour','$VCSanswers_hour','$VCSdrops_hour','$VCSdrops_hour_pct','$VCScalls_halfhour','$VCSanswers_halfhour','$VCSdrops_halfhour','$VCSdrops_halfhour_pct','$VCScalls_five','$VCSanswers_five','$VCSdrops_five','$VCSdrops_five_pct','$VCScalls_one','$VCSanswers_one','$VCSdrops_one','$VCSdrops_one_pct','$AMDanswers_one','$FAILanswers_one',$VSCupdateSQL)";

	$stmtA = "INSERT INTO osdial_campaign_stats (campaign_id,calls_today,answers_today,drops_today,drops_today_pct,drops_answers_today_pct,calls_hour,answers_hour,drops_hour,drops_hour_pct,calls_halfhour,answers_halfhour,drops_halfhour,drops_halfhour_pct,calls_fivemin,answers_fivemin,drops_fivemin,drops_fivemin_pct,calls_onemin,answers_onemin,drops_onemin,drops_onemin_pct,amd_onemin,failed_onemin,$VSCupdateSQLcol) VALUES $multi_sql ON DUPLICATE KEY UPDATE calls_today=VALUES(calls_today),answers_today=VALUES(answers_today),drops_today=VALUES(drops_today),drops_today_pct=VALUES(drops_today_pct),drops_answers_today_pct=VALUES(drops_answers_today_pct),calls_hour=VALUES(calls_hour),answers_hour=VALUES(answers_hour),drops_hour=VALUES(drops_hour),drops_hour_pct=VALUES(drops_hour_pct),calls_halfhour=VALUES(calls_halfhour),answers_halfhour=VALUES(answers_halfhour),drops_halfhour=VALUES(drops_halfhour),drops_halfhour_pct=VALUES(drops_halfhour_pct),calls_fivemin=VALUES(calls_fivemin),answers_fivemin=VALUES(answers_fivemin),drops_fivemin=VALUES(drops_fivemin),drops_fivemin_pct=VALUES(drops_fivemin_pct),calls_onemin=VALUES(calls_onemin),answers_onemin=VALUES(answers_onemin),drops_onemin=VALUES(drops_onemin),drops_onemin_pct=VALUES(drops_onemin_pct),amd_onemin=VALUES(amd_onemin),failed_onemin=VALUES(failed_onemin),$VSCupdateSQLdat;";
	if ($CLOtest) {
		print $stmtA . "\n";
	} else {
		$stat_rows = $dbhA->do($stmtA);
	}
	print "$campaign_id|$stmtA|\n" if ($DBX);
	my $event_string = "|$campaign_id|";
	$event_string .= join('|',$VCScalls_today,$VCSanswers_today,$VCSdrops_today,$VCSdrops_today_pct,$VCSdrops_answers_today_pct,$VCScalls_hour,$VCSanswers_hour,$VCSdrops_hour,$VCSdrops_hour_pct,$VCScalls_halfhour,$VCSanswers_halfhour,$VCSdrops_halfhour,$VCSdrops_halfhour_pct,$VCScalls_five,$VCSanswers_five,$VCSdrops_five,$VCSdrops_five_pct,$VCScalls_one,$VCSanswers_one,$VCSdrops_one,$VCSdrops_one_pct);
	eventLogger($config->{PATHlogs},"campaign_stats".$event_ext,$event_string);


	#############################################################
	# Get server IPs and update campaign stats for each server. #
	#############################################################
	my @servers;
	$multi_sql='';
	$stmtA = "SELECT server_ip FROM servers WHERE active='Y';";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	while ( my @aryA = $sthA->fetchrow_array ) {
		push @servers, $aryA[0];
	}
	$sthA->finish();

	foreach my $server (@servers) {
		# Create campaign/server entry if it doesn't exist.
		#my $stmtA = "INSERT INTO osdial_campaign_server_stats (campaign_id,server_ip) VALUES ('$campaign_id','$server') on duplicate key update campaign_id='$campaign_id';";
		#my $affected_rows = $dbhA->do($stmtA);

		my ($VCScalls_one,$VCSanswers_one,$VCSdrops_one,$VCSdrops_one_pct) = calculateDropsServer ($dbhA, $osdial_log, $campaign_id, $server, $camp_ANS_STAT_SQL, $VDL_one);
		my ($AMDcalls_one,$AMDanswers_one,$AMDdrops_one,$AMDdrops_one_pct) = calculateDropsServer ($dbhA, $osdial_log, $campaign_id, $server, $camp_AMD_SQL, $VDL_one);
		my ($FAILcalls_one,$FAILanswers_one,$FAILdrops_one,$FAILdrops_one_pct) = calculateDropsServer ($dbhA, $osdial_log, $campaign_id, $server, $camp_FAIL_SQL, $VDL_one);
		#$stmtA = "UPDATE osdial_campaign_server_stats SET calls_onemin='$VCScalls_one',answers_onemin='$VCSanswers_one',drops_onemin='$VCSdrops_one',amd_onemin='$AMDanswers_one',failed_onemin='$FAILanswers_one' WHERE campaign_id='$campaign_id' AND server_ip='$server';";
		$multi_sql .= "('$campaign_id','$server','$VCScalls_one','$VCSanswers_one','$VCSdrops_one','$AMDanswers_one','$FAILanswers_one'),";
		#if ($CLOtest) {
		#	print $stmtA . "\n";
		#} else {
		#	$affected_rows = $dbhA->do($stmtA);
		#}
	}
	if ($multi_sql ne '') {
		chop($multi_sql);
		$stmtA = "INSERT INTO osdial_campaign_server_stats (campaign_id,server_ip,calls_onemin,answers_onemin,drops_onemin,amd_onemin,failed_onemin) VALUES $multi_sql ON DUPLICATE KEY UPDATE calls_onemin=VALUES(calls_onemin),answers_onemin=VALUES(answers_onemin),drops_onemin=VALUES(drops_onemin),amd_onemin=VALUES(amd_onemin),failed_onemin=VALUES(failed_onemin);";
		if ($CLOtest) {
			print $stmtA . "\n";
		} else {
			$sstat_rows = $dbhA->do($stmtA);
		}
	}

	########################################################
	# Get Agents and update campaign stats for each agent. #
	########################################################
	my %agents;
	$multi_sql='';
	#$stmtA = "SELECT user FROM osdial_users;";
	$stmtA = "SELECT user,count(*),(TIME_TO_SEC(SUBTIME(TIME(SYSDATE()),TIME(MAX(event_time))))/60) FROM osdial_agent_log WHERE sub_status='LOGIN' AND event_time >= DATE(NOW()) group by user;";
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	while ( my @aryA = $sthA->fetchrow_array ) {
		if ($aryA[2] <= 121) {
			$agents{$aryA[0]} = $aryA[2];
		}
	}
	$sthA->finish();

	foreach my $agent (keys %agents) {
		# Create campaign/server entry if it doesn't exist.
		#my $stmtA = "INSERT INTO osdial_campaign_agent_stats (campaign_id,user) VALUES ('$campaign_id','$agent') on duplicate key update campaign_id='$campaign_id';";
		#my $affected_rows = $dbhA->do($stmtA);
		my ($VCScalls_today,$VCSanswers_today) = (0,0);
		my ($VCScalls_hour,$VCSanswers_hour) = (0,0);
		my ($VCScalls_halfhour,$VCSanswers_halfhour) = (0,0);
		my ($VCScalls_five,$VCSanswers_five) = (0,0);
		my ($VCScalls_one,$VCSanswers_one) = (0,0);

		($VCScalls_today,$VCSanswers_today) = calculateCallsAgent ($dbhA, $osdial_log, $campaign_id, $agent, $camp_ANS_STAT_SQL, $VDL_date);
		($VCScalls_hour,$VCSanswers_hour) = calculateCallsAgent ($dbhA, $osdial_log, $campaign_id, $agent, $camp_ANS_STAT_SQL, $VDL_hour) if ($agents{$agent} <= 121);
		($VCScalls_halfhour,$VCSanswers_halfhour) = calculateCallsAgent ($dbhA, $osdial_log, $campaign_id, $agent, $camp_ANS_STAT_SQL, $VDL_halfhour) if ($agents{$agent} <= 61);
		($VCScalls_five,$VCSanswers_five) = calculateCallsAgent ($dbhA, $osdial_log, $campaign_id, $agent, $camp_ANS_STAT_SQL, $VDL_five) if ($agents{$agent} <= 11);
		($VCScalls_one,$VCSanswers_one) = calculateCallsAgent ($dbhA, $osdial_log, $campaign_id, $agent, $camp_ANS_STAT_SQL, $VDL_one) if ($agents{$agent} <= 3);

		my $VSCupdateSQL='';
		my $g=0;
		foreach (@VSC_categories) {
			my $VSCcategory = $VSC_categories[$g];
			my $VSCtally;
			my $CATstatusesSQL;

			# FIND STATUSES IN STATUS CATEGORY
			$stmtA = "SELECT status from osdial_statuses where category='$VSCcategory';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows  = $sthA->rows;
			$rec_count = 0;
			while ( $sthArows > $rec_count ) {
				my @aryA = $sthA->fetchrow_array;
				$CATstatusesSQL .= "'$aryA[0]',";
				$rec_count++;
			}

			# FIND CAMPAIGN_STATUSES IN STATUS CATEGORY
			$stmtA = "SELECT status from osdial_campaign_statuses where category='$VSCcategory' and campaign_id='$campaign_id';";
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows  = $sthA->rows;
			$rec_count = 0;
			while ( $sthArows > $rec_count ) {
				my @aryA = $sthA->fetchrow_array;
				$CATstatusesSQL .= "'$aryA[0]',";
				$rec_count++;
			}
			chop($CATstatusesSQL);
			if ( length($CATstatusesSQL) > 2 ) {

				# FIND STATUSES IN STATUS CATEGORY
				$stmtA = "SELECT count(*) from $osdial_log where campaign_id='$campaign_id' and call_date > '$VDL_date' and status IN($CATstatusesSQL) and user='$agent';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows  = $sthA->rows;
				$rec_count = 0;
				while ( $sthArows > $rec_count ) {
					my @aryA     = $sthA->fetchrow_array;
					$VSCtally = $aryA[0];
					$rec_count++;
				}
			} else {
				$CATstatusesSQL = "'----'";
			}
			# Get category count for last hour, as CATanswers_hour
			my ($CATcalls_hour,$CATanswers_hour) = (0,0);
			($CATcalls_hour,$CATanswers_hour) = calculateCallsAgent ($dbhA, $osdial_log, $campaign_id, $agent, $CATstatusesSQL, $VDL_hour) if ($agents{$agent} <= 121);
			$g++;
			#$VSCupdateSQL .= "status_category_$g='$VSCcategory',status_category_count_$g='$VSCtally',status_category_hour_count_$g='$CATanswers_hour',";
			$VSCupdateSQL .= "'$VSCcategory','$VSCtally','$CATanswers_hour',";
			print "     $campaign_id|$g|$VSCcategory|$VSCtally|$CATanswers_hour|$CATstatusesSQL|\n" if ($DBX);
		}
		while ( $g < 4 ) {
			$g++;
			#$VSCupdateSQL .= "status_category_$g='',status_category_count_$g='0',status_category_hour_count_$g='0',";
			$VSCupdateSQL .= "'','0','0',";
		}
		chop($VSCupdateSQL);

		$multi_sql .= "('$campaign_id','$agent','$VCScalls_today','$VCSanswers_today','$VCScalls_hour','$VCSanswers_hour','$VCScalls_halfhour','$VCSanswers_halfhour','$VCScalls_five','$VCSanswers_five','$VCScalls_one','$VCSanswers_one',$VSCupdateSQL),";
		#$stmtA = "UPDATE osdial_campaign_agent_stats SET calls_today='$VCScalls_today',answers_today='$VCSanswers_today',calls_hour='$VCScalls_hour',answers_hour='$VCSanswers_hour',calls_halfhour='$VCScalls_halfhour',answers_halfhour='$VCSanswers_halfhour',calls_fivemin='$VCScalls_five',answers_fivemin='$VCSanswers_five',calls_onemin='$VCScalls_one',answers_onemin='$VCSanswers_one',$VSCupdateSQL WHERE campaign_id='$campaign_id' AND user='$agent';";
		#if ($CLOtest) {
		#	print $stmtA . "\n";
		#} else {
		#	$affected_rows = $dbhA->do($stmtA);
		#}
	}
	if ($multi_sql ne '') {
		chop($multi_sql);
		$stmtA = "INSERT INTO osdial_campaign_agent_stats (campaign_id,user,calls_today,answers_today,calls_hour,answers_hour,calls_halfhour,answers_halfhour,calls_fivemin,answers_fivemin,calls_onemin,answers_onemin,$VSCupdateSQLcol) VALUES $multi_sql ON DUPLICATE KEY UPDATE calls_today=VALUES(calls_today),answers_today=VALUES(answers_today),calls_hour=VALUES(calls_hour),answers_hour=VALUES(answers_hour),calls_halfhour=VALUES(calls_halfhour),answers_halfhour=VALUES(answers_halfhour),calls_fivemin=VALUES(calls_fivemin),answers_fivemin=VALUES(answers_fivemin),calls_onemin=VALUES(calls_onemin),answers_onemin=VALUES(answers_onemin),$VSCupdateSQLdat;";
		if ($CLOtest) {
			print $stmtA . "\n";
		} else {
			$astat_rows = $dbhA->do($stmtA);
		}
	}

}

sub calculateDrops {
	my ($dbhA,$osdial_log,$campaign_id,$camp_ANS_STAT_SQL,$VDL) = @_;

	my($stmtA,$sthA,$sthArows,$rec_count);
	my($VCScalls,$VCSanswers,$VCSdrops,$VCSdrops_pct) = (0,0,0,0);

	# CALL AND DROP STATS
	$stmtA = "SELECT count(*) from $osdial_log where campaign_id=\'$campaign_id\' and call_date > \'$VDL\';";
	print $stmtA . "\n" if ($DBX);
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows  = $sthA->rows;
	$rec_count = 0;
	while ( $sthArows > $rec_count ) {
		my @aryA = $sthA->fetchrow_array;
		$VCScalls = $aryA[0];
		$rec_count++;
	}
	$sthA->finish();
	if ( $VCScalls > 0 ) {

		# ANSWERS
		$stmtA = "SELECT count(*) from $osdial_log where campaign_id=\'$campaign_id\' and call_date > \'$VDL\' and status IN($camp_ANS_STAT_SQL);";
		print $stmtA . "\n" if ($DBX);
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA = $sthA->fetchrow_array;
			$VCSanswers = $aryA[0];
			$rec_count++;
		}
		$sthA->finish();

		# DROPS
		$stmtA = "SELECT count(*) from $osdial_log where campaign_id=\'$campaign_id\' and call_date > \'$VDL\' and status IN(\'DROP\',\'XDROP\');";
		print $stmtA . "\n" if ($DBX);
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA = $sthA->fetchrow_array;
			$VCSdrops = $aryA[0];
			if ( $VCSdrops > 0 ) {
				$VCSdrops_pct = ( ( $VCSdrops / $VCScalls ) * 100 );
				$VCSdrops_pct = sprintf( "%.2f", $VCSdrops_pct );
			}
			$rec_count++;
		}
		$sthA->finish();
	}
	return ($VCScalls,$VCSanswers,$VCSdrops,$VCSdrops_pct);
}

sub calculateDropsServer {
	my ($dbhA,$osdial_log,$campaign_id,$server,$camp_ANS_STAT_SQL,$VDL) = @_;

	my($stmtA,$sthA,$sthArows,$rec_count);
	my($VCScalls,$VCSanswers,$VCSdrops,$VCSdrops_pct) = (0,0,0,0);

	# CALL AND DROP STATS
	$stmtA = "SELECT count(*) from $osdial_log where campaign_id=\'$campaign_id\' and call_date > \'$VDL\' and server_ip=\'$server\';";
	print $stmtA . "\n" if ($DBX);
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows  = $sthA->rows;
	$rec_count = 0;
	while ( $sthArows > $rec_count ) {
		my @aryA = $sthA->fetchrow_array;
		$VCScalls = $aryA[0];
		$rec_count++;
	}
	$sthA->finish();
	if ( $VCScalls > 0 ) {

		# ANSWERS
		$stmtA = "SELECT count(*) from $osdial_log where campaign_id=\'$campaign_id\' and call_date > \'$VDL\' and status IN($camp_ANS_STAT_SQL) and server_ip=\'$server\';";
		print $stmtA . "\n" if ($DBX);
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA = $sthA->fetchrow_array;
			$VCSanswers = $aryA[0];
			$rec_count++;
		}
		$sthA->finish();

		# DROPS
		$stmtA = "SELECT count(*) from $osdial_log where campaign_id=\'$campaign_id\' and call_date > \'$VDL\' and status IN(\'DROP\',\'XDROP\') and server_ip=\'$server\';";
		print $stmtA . "\n" if ($DBX);
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA = $sthA->fetchrow_array;
			$VCSdrops = $aryA[0];
			if ( $VCSdrops > 0 ) {
				$VCSdrops_pct = ( ( $VCSdrops / $VCScalls ) * 100 );
				$VCSdrops_pct = sprintf( "%.2f", $VCSdrops_pct );
			}
			$rec_count++;
		}
		$sthA->finish();
	}
	return ($VCScalls,$VCSanswers,$VCSdrops,$VCSdrops_pct);
}

sub calculateCallsAgent {
	my ($dbhA,$osdial_log,$campaign_id,$agent,$camp_ANS_STAT_SQL,$VDL) = @_;

	my($stmtA,$sthA,$sthArows,$rec_count);
	my($VCScalls,$VCSanswers) = (0,0);

	# CALL STATS
	$stmtA = "SELECT count(*) from $osdial_log where campaign_id=\'$campaign_id\' and call_date > \'$VDL\' and user=\'$agent\';";
	print $stmtA . "\n" if ($DBX);
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows  = $sthA->rows;
	$rec_count = 0;
	while ( $sthArows > $rec_count ) {
		my @aryA = $sthA->fetchrow_array;
		$VCScalls = $aryA[0];
		$rec_count++;
	}
	$sthA->finish();
	if ( $VCScalls > 0 ) {

		# ANSWERS
		$stmtA = "SELECT count(*) from $osdial_log where campaign_id=\'$campaign_id\' and call_date > \'$VDL\' and status IN($camp_ANS_STAT_SQL) and user=\'$agent\';";
		print $stmtA . "\n" if ($DBX);
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows  = $sthA->rows;
		$rec_count = 0;
		while ( $sthArows > $rec_count ) {
			my @aryA = $sthA->fetchrow_array;
			$VCSanswers = $aryA[0];
			$rec_count++;
		}
		$sthA->finish();
	}
	return ($VCScalls,$VCSanswers);
}





### Lott's Global Subs, Mmmmmmm, tasty ###


# getAGCconfig usage:
#    $config = getAGCconfig($agcConfigPath);
# Requires:
#    $agcConfigPath : Usually '/etc/osdial.conf'
# Returns:
#    hashref with configuration directives in listed file.
sub getAGCconfig {
	my($AGCpath) = @_;
	my %config;
	$config{PATHconf} = $AGCpath;

	# Begin Parsing osdial.config file.
	open(CONF, $config{PATHconf}) || die "can't open " . $config{PATHconf} . ": " . $! . "\n";
	while (my $line = <CONF>) {
		$line =~ s/ |>|"|'|\n|\r|\t|\#.*|;.*//gi;
		if ($line =~ /=|:/) {
			my($key,$val) = split /=|:/, $line;
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


