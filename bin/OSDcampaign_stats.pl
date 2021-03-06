#!/usr/bin/perl
#
# OSDcampaign_stats.pl
#
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

use strict;
use OSDial;
use Getopt::Long;
use Time::HiRes ('gettimeofday','usleep','sleep');
$|++;

my $prog = "OSDcampaign_stats.pl";

my $secStart = time();


my ($DB, $DBX, $CLOhelp, $CLOcampaign, $CLOrecalc, $CLOloops, $CLOdelay);

### begin parsing run-time options ###
$CLOloops = 60; # 60 * 10 1-second waits = 600 seconds = 10min
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
		'debugX!' => \$DBX
	);
	$DB=2 if ($DBX);
	if ($DB) {
		print "----- DEBUGGING -----\n";
		print "----- EXTRA-VERBOSE DEBUGGING -----\n" if ($DBX);
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
		exit 0;
	}
}	
$DB=0 unless($DB);

my $osdial = OSDial->new('DB'=>$DB);

if ($osdial->server_process_tracker($prog,$osdial->{VARserver_ip},$$,0)) {
	print STDERR "ERROR: Process already running!\n\n";
	exit 1;
}

my $loop = 0;
my $calc = 0;
my $start_time = 0;
my $end_time = 0;
while ($loop < $CLOloops) {
	$start_time = time();
	print "  -- OSDcampaign_stats.pl: Entering loop. $calc $loop\n" if ($osdial->{DB}>1);
	if ($calc >= $CLOrecalc) {
		if ($osdial->server_process_tracker($prog,$osdial->{VARserver_ip},$$,0)) {
			print STDERR "ERROR: Process already running!\n\n";
			exit 1;
		}
		my $camp_stats = get_campaign_stats($osdial,$CLOcampaign);
		print "  -- OSDcampaign_stats.pl: Stats collection completed.\n" if ($osdial->{DB}>0);

		set_campaign_stats($osdial,$camp_stats);
		print "  -- OSDcampaign_stats.pl: Stats written successfully.\n" if ($osdial->{DB}>0);
		$end_time = time();
		print "  -- OSDcampaign_stats.pl: Stats run-time(" . ($end_time - $start_time) . ").\n" if ($osdial->{DB}>0);
		$calc=0;
		$loop++;
	}
	usleep($CLOdelay*1000*1000);
	$calc++;
}


sub set_campaign_stats {
	my ($osdial, $cdata) = @_;
	my $cinshead  = "INSERT INTO osdial_campaign_stats (campaign_id,";
	my $chdone    = 0;
	my $cinsmulti = '';
	my $cinsonupd = ' ON DUPLICATE KEY UPDATE ';
	my $ainshead  = "INSERT INTO osdial_campaign_agent_stats (campaign_id,user,";
	my $ahdone    = 0;
	my $ainsmulti = '';
	my $ainsonupd = ' ON DUPLICATE KEY UPDATE ';
	my $sinshead  = "INSERT INTO osdial_campaign_server_stats (campaign_id,server_ip,";
	my $shdone    = 0;
	my $sinsmulti = '';
	my $sinsonupd = ' ON DUPLICATE KEY UPDATE ';
	my $lastccols=0;
	my $lastacols=0;
	my $lastscols=0;
	foreach my $campaign (sort keys %{$cdata}) {
		my $ccols = scalar keys %{$cdata->{$campaign}{campaign}};
		if ($ccols>$lastccols) {
			$cinshead  = "INSERT INTO osdial_campaign_stats (campaign_id,";
			$cinsonupd = ' ON DUPLICATE KEY UPDATE ';
			$chdone    = 0;
			$lastccols=$ccols;
		}
		my $cadd=1;
		$cadd=0 if (scalar(keys %{$cdata->{$campaign}{campaign}}) < 30);
		print "  -- OSDcampaign_stats.pl: set_campaign_stats: got campaign $campaign.\n" if ($osdial->{DB}>1);
		$cinsmulti .= "('" . $osdial->mres($campaign) . "'," if ($cadd);
		foreach my $cfld (sort keys %{$cdata->{$campaign}{campaign}}) {
			print "      -- OSDcampaign_stats.pl: set_campaign_stats: campaign: $campaign  got field $cfld.\n" if ($osdial->{DB}>1);
			$cinshead .= $cfld . ',' if ($chdone == 0);
			$cinsmulti .= "'" . $osdial->mres($cdata->{$campaign}{campaign}{$cfld}) . "'," if ($cadd);;
			$cinsonupd .= $cfld . '=VALUES(' . $cfld . '),' if ($chdone == 0);
		}
		chop($cinsmulti) if ($cadd);;
		$cinsmulti .= '),' if ($cadd);;
		if ($chdone == 0) {
			chop($cinshead);
			$cinshead .= ') VALUES ';
			chop($cinsonupd);
			$cinsonupd .= ';';
			$chdone++;
		}
	
		foreach my $agent (sort keys %{$cdata->{$campaign}{agent}}) {
			my $acols = scalar keys %{$cdata->{$campaign}{agent}{$agent}};
			if ($acols>$lastacols) {
				$ainshead  = "INSERT INTO osdial_campaign_agent_stats (campaign_id,user,";
				$ainsonupd = ' ON DUPLICATE KEY UPDATE ';
				$ahdone    = 0;
				$lastacols=$acols;
			}
			print "    -- OSDcampaign_stats.pl: set_campaign_stats: campaign: $campaign  got agent $agent.\n" if ($osdial->{DB}>1);
			$ainsmulti .= "('" . $osdial->mres($campaign) . "','" . $osdial->mres($agent) . "',";
			foreach my $afld (sort keys %{$cdata->{$campaign}{agent}{$agent}}) {
				print "      -- OSDcampaign_stats.pl: set_campaign_stats: campaign: $campaign  agent: $agent  got field $afld.\n" if ($osdial->{DB}>1);
				$ainshead .= $afld . ',' if ($ahdone == 0);
				$ainsmulti .= "'" . $osdial->mres($cdata->{$campaign}{agent}{$agent}{$afld}) . "',";
				$ainsonupd .= $afld . '=VALUES(' . $afld . '),' if ($ahdone == 0);
			}
			chop($ainsmulti);
			$ainsmulti .= '),';
			if ($ahdone == 0) {
				chop($ainshead);
				$ainshead .= ') VALUES ';
				chop($ainsonupd);
				$ainsonupd .= ';';
				$ahdone++;
			}
		}
		foreach my $server (sort keys %{$cdata->{$campaign}{server}}) {
			my $scols = scalar keys %{$cdata->{$campaign}{server}{$server}};
			if ($scols>$lastscols) {
				$sinshead  = "INSERT INTO osdial_campaign_server_stats (campaign_id,server_ip,";
				$shdone    = 0;
				$sinsonupd = ' ON DUPLICATE KEY UPDATE ';
				$lastscols=$scols;
			}
			print "    -- OSDcampaign_stats.pl: set_campaign_stats: campaign: $campaign  got server $server.\n" if ($osdial->{DB}>1);
			$sinsmulti .= "('" . $osdial->mres($campaign) . "','$server',";
			foreach my $sfld (sort keys %{$cdata->{$campaign}{server}{$server}}) {
				print "      -- OSDcampaign_stats.pl: set_campaign_stats: campaign: $campaign  got server $server  got field $sfld.\n" if ($osdial->{DB}>1);
				$sinshead .= $sfld . ',' if ($shdone == 0);
				$sinsmulti .= "'" . $osdial->mres($cdata->{$campaign}{server}{$server}{$sfld}) . "',";
				$sinsonupd .= $sfld . '=VALUES(' . $sfld . '),' if ($shdone == 0);
			}
			chop($sinsmulti);
			$sinsmulti .= '),';
			if ($shdone == 0) {
				chop($sinshead);
				$sinshead .= ') VALUES ';
				chop($sinsonupd);
				$sinsonupd .= ';';
				$shdone++;
			}
		}
		#usleep(1*10*1000);
	}
	chop($cinsmulti);
	chop($ainsmulti);
	chop($sinsmulti);
	if ($chdone>0) {
		print "  -- OSDcampaign_stats.pl: set_campaign_stats: cins: " . $cinshead . $cinsmulti . $cinsonupd . "\n\n\n" if ($osdial->{DB}>1);
		$osdial->sql_execute($cinshead . $cinsmulti . $cinsonupd);
		usleep(1*1*1000);
	}
	if ($ahdone>0) {
		print "  -- OSDcampaign_stats.pl: set_campaign_stats: ains: " . $ainshead . $ainsmulti . $ainsonupd . "\n\n\n" if ($osdial->{DB}>1);
		$osdial->sql_execute($ainshead . $ainsmulti . $ainsonupd);
		usleep(1*1*1000);
	}
	if ($shdone>0) {
		print "  -- OSDcampaign_stats.pl: set_campaign_stats: sins: " . $sinshead . $sinsmulti . $sinsonupd . "\n\n\n" if ($osdial->{DB}>1);
		$osdial->sql_execute($sinshead . $sinsmulti . $sinsonupd);
		usleep(1*1*1000);
	}
	return 1;
}

sub get_campaign_stats {
	my ($osdial, $CLOcampaign) = @_;

	my $secX = time();
	my $Stoday    = $secX - 86400;
	my $Shour     = $secX - 3600;
	my $Shalfhour = $secX - 1800;
	my $Sfivemin  = $secX - 300;
	my $Sonemin   = $secX - 60;
	my $today = $osdial->get_date($secX);


	# Get Status Categories for Display Setting
	my @scorder;
	my $scref;
	my $scpos=1;
	while ( my $sret = $osdial->sql_query("SELECT vsc_id,tovdad_display FROM osdial_status_categories ORDER BY vsc_id;") ) {
		my $vscid = $sret->{vsc_id};
		$scref->{$vscid}{display} = 0;
		$scref->{$vscid}{position} = 0;
		if ($sret->{tovdad_display} eq 'Y' and $scpos < 5) {
			$scref->{$vscid}{display} = 1;
			$scref->{$vscid}{position} = $scpos++;
			push @scorder, $vscid;
		}
	}

	# Load in System Statuses.
	my $statuses={};
	while ( my $sret2 = $osdial->sql_query("SELECT status,human_answered,category FROM osdial_statuses;",'B') ) {
		my $status   = $sret2->{status};
		my $category = $sret2->{category};
		$category    = 'UNDEFINED' if ($category eq '');
		# Categorize IVR statuses.
		$category    = 'SALE'      if ($status =~ /^VAXFER$|^VEXFER$|^VIXFER$/);
		$category    = 'CONTACT'   if ($status =~ /^VDNC$|^VNI$|^VPLAY$|^VPU$|^VTO$/);
		# Categorize CPA statuses.
		$category    = 'SYSTEM'    if ($status =~ /^CPRATB$|^CPRCR$|^CPRLR$|^CPRSNC$|^CPRSRO$|^CPRSIC$|^CPRSIO$|^CPRSVC$/);
		$category    = 'NOCONTACT' if ($status =~ /^CPRB$|^CPRNA$|^CPSHU$|^CPSAA$|^CPSFAX$/);
		$category    = 'CONTACT'   if ($status =~ /^CPSHMN$|^CPSUNK$/);
		$statuses->{$status}{'category'} = $category;
		$statuses->{$status}{'human_answered'} = $sret2->{human_answered};
	}

	# Build initial data stucture for stat collection, $cdata.
	my $statusref;
	my $cdata;
	my $campSQL = 'campaign_id IN(';
	my $campSQLwhere = '';
	my $campcnt = 0;
	my $swhere = '1=1';
	$swhere = "(active='Y' or campaign_stats_refresh='Y')" if ($loop%25==0);
	$swhere = "campaign_id='" . $osdial->mres($CLOcampaign) . "'" if ($CLOcampaign);
	while ( my $sret = $osdial->sql_query("SELECT campaign_id FROM osdial_campaigns WHERE $swhere;") ) {
		my $campaign = uc($sret->{campaign_id});
		$campSQL .= "'".$osdial->mres($sret->{campaign_id})."',";
		$cdata->{$campaign}{campaign} = { 
			'calls_today'=>0,   'answers_today'=>0,   'drops_today'=>0,   'drops_today_pct'=>0,   'drops_answers_today_pct'=>0,
			'calls_hour'=>0,    'answers_hour'=>0,    'drops_hour'=>0,    'drops_hour_pct'=>0,
			'calls_halfhour'=>0,'answers_halfhour'=>0,'drops_halfhour'=>0,'drops_halfhour_pct'=>0,
			'calls_fivemin'=>0, 'answers_fivemin'=>0, 'drops_fivemin'=>0, 'drops_fivemin_pct'=>0,
			'calls_onemin'=>0,  'answers_onemin'=>0,  'drops_onemin'=>0,  'drops_onemin_pct'=>0,  'amd_onemin'=>0,'failed_onemin'=>0};
		my $scpos=1;
		foreach my $sc (@scorder) {
			$cdata->{$campaign}{campaign}{"status_category_".$scpos} = $sc;
			$cdata->{$campaign}{campaign}{"status_category_count_".$scpos} = 0;
			$cdata->{$campaign}{campaign}{"status_category_hour_count_".$scpos++} = 0;
		}
		foreach my $stat (%{$statuses}) {
			$statusref->{$campaign}{$stat}{$statuses->{$stat}{'category'}} = 0;
			$statusref->{$campaign}{$stat}{$statuses->{$stat}{'category'}} = 1 if ($statuses->{$stat}{human_answered} eq 'Y');
		}
		$campcnt++;
	}
	if ($campcnt>0) {
		chop $campSQL;
		$campSQL .= ') ';
		$campSQLwhere = ' WHERE ' . $campSQL;
		$campSQL = ' AND ' . $campSQL;
	} else {
		$campSQL = '';
		$campSQLwhere = '';
	}
	$osdial->sql_execute("UPDATE osdial_campaigns SET campaign_stats_refresh='N';");

	# Load in Custom Campaign Statuses, overriding System Statuses per Campaign.
	while ( my $sret = $osdial->sql_query(sprintf('SELECT campaign_id,status,human_answered,category FROM osdial_campaign_statuses%s ORDER BY campaign_id;',$campSQLwhere)) ) {
		my $campaign = uc($sret->{campaign_id});
		my $status   = $sret->{status};
		my $category = $sret->{category};
		$category    = 'UNDEFINED' if ($category eq '');
		$statusref->{$campaign}{$status}{$category} = 0;
		$statusref->{$campaign}{$status}{$category} = 1 if ($sret->{human_answered} eq 'Y');
	}


	# Start campaign stat data collection
	while (my $res = $osdial->sql_query(sprintf("SELECT campaign_id,user,status,server_ip,start_epoch FROM osdial_log WHERE call_date BETWEEN '%s' AND '%s'%s ORDER BY campaign_id;",$osdial->mres($today.' 00:00:00'),$osdial->mres($osdial->get_datetime($secX)),$campSQL )) ) {
		if ($res->{campaign_id} ne "") {
			my $agent    = $res->{user};
			my $campaign = uc($res->{campaign_id});
			my $status   = $res->{status};
			my $server   = $res->{server_ip};
			my $statcat  = join('',keys %{$statusref->{$campaign}{$status}});
			$statcat     = "UNDEFINED" if ($statcat eq '');
			$status      = "NA"        if ($status eq '');
			$agent       = "VDAD"      if ($campaign eq '');

			# Define Agent, if we havent already.
			if (!defined $cdata->{$campaign}{agent}{$agent}) {
				$cdata->{$campaign}{agent}{$agent} = {
					'calls_today'=>0,'answers_today'=>0,
					'calls_hour'=>0,'answers_hour'=>0,
					'calls_halfhour'=>0,'answers_halfhour'=>0,
					'calls_fivemin'=>0,'answers_fivemin'=>0,
					'calls_onemin'=>0,'answers_onemin'=>0};
				my $scpos=1;
				foreach my $sc (@scorder) {
					$cdata->{$campaign}{agent}{$agent}{"status_category_".$scpos} = $sc;
					$cdata->{$campaign}{agent}{$agent}{"status_category_count_".$scpos} = 0;
					$cdata->{$campaign}{agent}{$agent}{"status_category_hour_count_".$scpos} = 0;
				}
			}

			# Define Servers, if we havent already.
			if (!$cdata->{$campaign}{server}{$server}) {
				$cdata->{$campaign}{server}{$server} = { 'calls_onemin'=>0,'answers_onemin'=>0, 'amd_onemin'=>0,'failed_onemin'=>0, 'drops_onemin'=>0 };
			}


			# Calculate Todays Calls
			if ($res->{start_epoch} >= $Stoday) {
				# Calls
				$cdata->{$campaign}{campaign}{calls_today}++;
				$cdata->{$campaign}{agent}{$agent}{calls_today}++;

				# Answers
				if ($statusref->{$campaign}{$status}{$statcat} == 1) {
					$cdata->{$campaign}{agent}{$agent}{answers_today}++;
					$cdata->{$campaign}{campaign}{answers_today}++;
				}

				# Drops
				$cdata->{$campaign}{campaign}{drops_today}++         if ($status =~ /^DROP$|^XDROP$|^TIMEOT$/);;
				$cdata->{$campaign}{campaign}{drops_today_pct} = 
					sprintf("%.2f", (($cdata->{$campaign}{campaign}{drops_today} / $cdata->{$campaign}{campaign}{calls_today}) * 100))   if ($cdata->{$campaign}{campaign}{calls_today}>0);
				$cdata->{$campaign}{campaign}{drops_answers_today_pct} =
					sprintf("%.2f", (($cdata->{$campaign}{campaign}{drops_today} / $cdata->{$campaign}{campaign}{answers_today}) * 100)) if ($cdata->{$campaign}{campaign}{answers_today}>0);

				# Categories
				$cdata->{$campaign}{agent}{$agent}{"status_category_count_".$scref->{$statcat}{position}}++ if (defined $cdata->{$campaign}{agent}{$agent}{"status_category_count_".$scref->{$statcat}{position}});
				$cdata->{$campaign}{campaign}{"status_category_count_".$scref->{$statcat}{position}}++      if (defined $cdata->{$campaign}{campaign}{"status_category_count_".$scref->{$statcat}{position}});
			}

			# Calculate Calls for Last Hour.
			if ($res->{start_epoch} >= $Shour) {
				# Calls
				$cdata->{$campaign}{campaign}{calls_hour}++;
				$cdata->{$campaign}{agent}{$agent}{calls_hour}++;

				# Answers
				if ($statusref->{$campaign}{$status}{$statcat} == 1) {
					$cdata->{$campaign}{agent}{$agent}{answers_hour}++;
					$cdata->{$campaign}{campaign}{answers_hour}++;
				}

				# Drops
				$cdata->{$campaign}{campaign}{drops_hour}++          if ($status =~ /^DROP$|^XDROP$|^TIMEOT$/);
				$cdata->{$campaign}{campaign}{drops_hour_pct} =
					sprintf("%.2f", (($cdata->{$campaign}{campaign}{drops_hour} / $cdata->{$campaign}{campaign}{calls_hour}) * 100)) if ($cdata->{$campaign}{campaign}{calls_hour}>0);

				# Categories
				$cdata->{$campaign}{campaign}{"status_category_hour_count_".$scref->{$statcat}{position}}++      if (defined $cdata->{$campaign}{campaign}{"status_category_hour_count_".$scref->{$statcat}{position}});
				$cdata->{$campaign}{agent}{$agent}{"status_category_hour_count_".$scref->{$statcat}{position}}++      if (defined $cdata->{$campaign}{agent}{$agent}{"status_category_hour_count_".$scref->{$statcat}{position}});
			}

			# Calculate Calls for Last Half-Hour.
			if ($res->{start_epoch} >= $Shalfhour) {
				# Calls
				$cdata->{$campaign}{campaign}{calls_halfhour}++;
				$cdata->{$campaign}{agent}{$agent}{calls_halfhour}++;

				# Answers
				if ($statusref->{$campaign}{$status}{$statcat} == 1) {
					$cdata->{$campaign}{agent}{$agent}{answers_halfhour}++;
					$cdata->{$campaign}{campaign}{answers_halfhour}++;
				}

				# Drops
				$cdata->{$campaign}{campaign}{drops_halfhour}++      if ($status =~ /^DROP$|^XDROP$|^TIMEOT$/);;
				$cdata->{$campaign}{campaign}{drops_halfhour_pct} =
					sprintf("%.2f", (($cdata->{$campaign}{campaign}{drops_halfhour} / $cdata->{$campaign}{campaign}{calls_halfhour}) * 100)) if ($cdata->{$campaign}{campaign}{calls_halfhour}>0);
			}

			# Calculate Calls for Last Five-minutes.
			if ($res->{start_epoch} >= $Sfivemin) {
				# Calls
				$cdata->{$campaign}{campaign}{calls_fivemin}++;
				$cdata->{$campaign}{agent}{$agent}{calls_fivemin}++;

				# Answers
				if ($statusref->{$campaign}{$status}{$statcat} == 1) {
					$cdata->{$campaign}{agent}{$agent}{answers_fivemin}++;
					$cdata->{$campaign}{campaign}{answers_fivemin}++;
				}

				# Drops
				$cdata->{$campaign}{campaign}{drops_fivemin}++       if ($status =~ /^DROP$|^XDROP$|^TIMEOT$/);;
				$cdata->{$campaign}{campaign}{drops_fivemin_pct} =
					sprintf("%.2f", (($cdata->{$campaign}{campaign}{drops_fivemin} / $cdata->{$campaign}{campaign}{calls_fivemin}) * 100)) if ($cdata->{$campaign}{campaign}{calls_fivemin}>0);
			}

			# Calculate Calls for Last Minute.
			if ($res->{start_epoch} >= $Sonemin) {
				# Calls
				$cdata->{$campaign}{campaign}{calls_onemin}++;
				$cdata->{$campaign}{server}{$server}{calls_onemin}++;
				$cdata->{$campaign}{agent}{$agent}{calls_onemin}++;

				# Answers
				if ($statusref->{$campaign}{$status}{$statcat} == 1) {
					$cdata->{$campaign}{agent}{$agent}{answers_onemin}++;
					$cdata->{$campaign}{campaign}{answers_onemin}++;
				}

				# Drops
				$cdata->{$campaign}{campaign}{drops_onemin}++        if ($status =~ /^DROP$|^XDROP$|^TIMEOT$/);;
				$cdata->{$campaign}{server}{$server}{drops_onemin}++ if ($status =~ /^DROP$|^XDROP$|^TIMEOT$/);;
				$cdata->{$campaign}{campaign}{drops_onemin_pct} =
					sprintf("%.2f", (($cdata->{$campaign}{campaign}{drops_onemin} / $cdata->{$campaign}{campaign}{calls_onemin}) * 100)) if ($cdata->{$campaign}{campaign}{calls_onemin}>0);

				# AMD
				$cdata->{$campaign}{campaign}{amd_onemin}++          if ($status =~ /^A$|^AA$|^AL$|^AM$|^CPSAA$|^CPSFAX$/);;
				$cdata->{$campaign}{server}{$server}{amd_onemin}++   if ($status =~ /^A$|^AA$|^AL$|^AM$|^CPSAA$|^CPSFAX$/);;

				# Failed Calls.
				$cdata->{$campaign}{campaign}{failed_onemin}++       if ($status =~ /^CRC$|^CRO$|^CRF$|^CRR$|^CPRATB$|^CPRCR$|^CPRLR$|^CPRSNC$|^CPRSRO$|^CPRSIC$|^CPRSIO$|^CPRSVC$/);;
				$cdata->{$campaign}{server}{$server}{failed_onemin}++ if ($status =~ /^CRC$|^CRO$|^CRF$|^CRR$|^CPRATB$|^CPRCR$|^CPRLR$|^CPRSNC$|^CPRSRO$|^CPRSIC$|^CPRSIO$|^CPRSVC$/);;
			}
		}
	}
	usleep(1*1*1000);
	return $cdata;
}

exit 0;
