#!/usr/bin/perl
#
#  osdial_external_dnc.pl: Script to check leads against an external dnc database.
#
#  Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
#
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
#
# 090630-2112 - Initial build.


use strict;
use DBI;
use Getopt::Long;
use IO::Interface::Simple;
use OSDial;
$|++;

# Identify myself.
my $prog = 'osdial_external_dnc.pl';

# Get OSD configuration directives.
my $config = getOSDconfig('/etc/osdial.conf');

# Auto-creation header.
my $achead = ";\n; WARNING: AUTO-CREATED FILE.\n; Any changes you make will be overwritten!\n;\n";

# Declare command-line options.
my($DB, $CLOhelp, $CLOtest, $CLOsched, $CLOquiet);
my(%reloads);

# Read in command-line options.
if (scalar @ARGV) {
        GetOptions(
                'help!' => \$CLOhelp,
                'debug!' => \$DB,
                'test!' => \$CLOtest,
                'sched=s' => \$CLOsched,
                'quiet!' => \$CLOquiet,
        );
        if ($DB) {
                print "----- DEBUGGING -----\n";
                print "----- Testing Mode -----\n" if ($CLOtest);
                print "VARS-\n";
                print "CLOhelp-     $CLOhelp\n";
                print "CLOquiet-    $CLOquiet\n";
                print "CLOtest-     $CLOtest\n";
                print "CLOsched-    $CLOsched\n";
                print "\n";
        }
        if ($CLOhelp) {
                print "\n\n" . $prog;
                print "allowed run-time options:\n";
                print "  [--help]          = This screen\n";
                print "  [--debug]         = debug\n";
                print "  [-t|--test]       = test only\n";
                print "  [--sched=ALL]     = Schedule all lists to be scrubbed.\n";
                print "  [--sched=list_id] = Schedule a particular list to be scrubbed.\n";
                print "  [-q|--quiet]      = Quiet output\n\n";
                exit 0;
        }
}

my $osdial = OSDial->new('DB'=>$DB);

# Connect to database.
my $dbhA = DBI->connect("DBI:mysql:" . $config->{VARDB_database} . ":" . $config->{VARDB_server} . ":" . $config->{VARDB_port}, $config->{VARDB_user}, $config->{VARDB_pass} )
  or die "Couldn't connect to database: " . DBI->errstr;
print "    Connected to Database:  " . $config->{VARDB_server} . "|" . $config->{VARDB_database} . "\n" if ($DB);
my $dbhC = DBI->connect("DBI:mysql:" . $config->{VARDB_database} . ":" . $config->{VARDB_server} . ":" . $config->{VARDB_port}, $config->{VARDB_user}, $config->{VARDB_pass} )
  or die "Couldn't connect to database2: " . DBI->errstr;


# Get External_DNC options.
my %ext_dnc;
my $stmtA = "SELECT name,data FROM configuration WHERE name LIKE 'External_DNC%';";
my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
print "\n  DNC Options:\n" if ($DB);
while (my @aryA = $sthA->fetchrow_array) {
	print "    $aryA[0] : $aryA[1]\n" if ($DB);
	$ext_dnc{$aryA[0]} = $aryA[1];
}
$sthA->finish();


# Check to see if External DNC is active
if ($ext_dnc{'External_DNC_Active'} ne 'Y') {
	print "\n\n External DNC Check is disabled, exiting...\n\n" if ($DB);
	$dbhA->disconnect();
	exit_now();
}

# Schedule DNC events
if ($CLOsched) {
	print "\n  Setting $CLOsched for scrub.\n" if ($DB);
	my $stmt;
	if ($CLOsched eq 'ALL') {
		$stmt = "UPDATE osdial_lists SET scrub_dnc='Y';";
	} elsif ($CLOsched eq 'NONE') {
		$stmt = "UPDATE osdial_lists SET scrub_dnc='N';";
	} else {
		$stmt = "UPDATE osdial_lists SET scrub_dnc='Y' WHERE list_id='$CLOsched';";
	}
	$dbhA->do($stmt);
	$dbhA->disconnect();
	exit_now();
}


# Check to see if External DNC as more than 3 active processes
my $edncprocs = `ps -ef | grep osdial_external_dnc.pl | grep asterisk | wc -l`;
chomp($edncprocs);
if ($edncprocs > 3) {
	print "\n\n External DNC already has $edncprocs running, exiting...\n\n" if ($DB);
	$dbhA->disconnect();
	exit_now();
}



# Connect to External DNC database
my $dbhB = DBI->connect("DBI:mysql:" . $ext_dnc{'External_DNC_Database'} . ":" . $ext_dnc{'External_DNC_Address'}, $ext_dnc{'External_DNC_Username'}, $ext_dnc{'External_DNC_Password'} )
  or die "Couldn't connect to DNC database: " . DBI->errstr;
print "\n    Connected to DNC Database:  " . $ext_dnc{'External_DNC_Address'} . "|" . $ext_dnc{'External_DNC_Database'} . "\n" if ($DB);


# Get set of lists that need to be scrubbed.
my %lists;
my $stmtA = "SELECT list_id,campaign_id FROM osdial_lists WHERE scrub_dnc='Y';";
my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
if ($sthA->rows==0) {
	$sthA->finish();
	exit_now();
}
print "\n  Scrubbing: " if ($DB);
while (my @aryA = $sthA->fetchrow_array) {
	print " $aryA[0]" if ($DB);
	$lists{$aryA[0]} = $aryA[1];
}
print "\n\n" if ($DB);
$sthA->finish();


# Get lead_id,phone_number of leads to mark as DNCE
foreach my $list_id (keys %lists) {
	my $stmtA = "UPDATE osdial_lists SET scrub_last=NOW(),scrub_dnc='N' WHERE list_id='" . $osdial->mres($list_id) . "';";
	$dbhA->do($stmtA);

	# Get statuses to scan through.
	my $stmtA = "SELECT dial_statuses FROM osdial_campaigns WHERE campaign_id='" . $osdial->mres($lists{$list_id}) . "';";
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my @aryA = $sthA->fetchrow_array();
	my $stats = $osdial->mres($aryA[0]);
	$stats =~ s/ /','/g;
	$stats =~ s/^',//;
	$stats =~ s/,'-$//;

	# Scan main phone number
	my $stmtA = "SELECT lead_id,phone_number FROM osdial_list WHERE list_id='" . $osdial->mres($list_id) . "' and status IN($stats);";
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my $tot_row = $sthA->rows();
	my $cur_row = 0;
	print " Main Rows: $tot_row \n" if ($DB);
	while (my @aryA = $sthA->fetchrow_array) {
		$cur_row++;
		my $lead_id = $aryA[0];
		my $fullphone = $aryA[1];
		my $areacode = substr($fullphone, 0, 3);
		my $phone = substr($fullphone, 3, 7);
		my $esql = $ext_dnc{'External_DNC_SQL'};
		$esql =~ s/%AREACODE%/$areacode/g;
		$esql =~ s/%FULLPHONE%/$fullphone/g;
		$esql =~ s/%NUMBER%/$phone/g;

		my $fcnt = 0;
		my $sthB = $dbhB->prepare($esql) or die "preparing: ", $dbhB->errstr;
		$sthB->execute or die "executing: $esql ", $dbhB->errstr;
		while (my @aryB = $sthB->fetchrow_array) {
			$fcnt = 1 if ($aryB[0] > 0);
		}
		$sthB->finish();
		if ($fcnt) {
			print "  Found $lead_id : $fullphone \n" if ($DB);
			my $stmtC = "UPDATE osdial_lists SET scrub_last=NOW(),scrub_info='$cur_row/$tot_row' WHERE list_id='" . $osdial->mres($list_id) . "';";
			$dbhC->do($stmtC);
			unless ($CLOtest) {
				my $stmtC = "UPDATE osdial_list SET status='DNCE' WHERE lead_id='$lead_id';";
				$dbhC->do($stmtC);
				my $stmtC = "INSERT IGNORE INTO osdial_dnc SET phone_number='$fullphone';";
				$dbhC->do($stmtC);
			}
		}
	}
	my $stmtC = "UPDATE osdial_lists SET scrub_info='$tot_row/$tot_row' WHERE list_id='" . $osdial->mres($list_id) . "';";
	$dbhC->do($stmtC);
	$sthA->finish();

	# Scan alt phone number
	my $stmtA = "SELECT lead_id,alt_phone FROM osdial_list WHERE list_id='" . $osdial->mres($list_id) . "' AND alt_phone!='';";
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my $tot_row = $sthA->rows();
	print " ALT Rows: $tot_row \n" if ($DB);
	while (my @aryA = $sthA->fetchrow_array) {
		my $lead_id = $aryA[0];
		my $fullphone = $aryA[1];
		my $areacode = substr($fullphone, 0, 3);
		my $phone = substr($fullphone, 3, 7);
		my $esql = $ext_dnc{'External_DNC_SQL'};
		$esql =~ s/%AREACODE%/$areacode/g;
		$esql =~ s/%FULLPHONE%/$fullphone/g;
		$esql =~ s/%NUMBER%/$phone/g;

		my $fcnt = 0;
		my $sthB = $dbhB->prepare($esql) or die "preparing: ", $dbhB->errstr;
		$sthB->execute or die "executing: $esql ", $dbhB->errstr;
		while (my @aryB = $sthB->fetchrow_array) {
			$fcnt = 1 if ($aryB[0] > 0);
		}
		$sthB->finish();
		if ($fcnt) {
			print "  Found ALT $lead_id : $fullphone \n" if ($DB);
			unless ($CLOtest) {
				my $stmtC = "INSERT IGNORE INTO osdial_dnc SET phone_number='$fullphone';";
				$dbhC->do($stmtC);
			}
		}
	}
	$sthA->finish();

	# Scan addr3 phone number
	my $stmtA = "SELECT SQL_NO_CACHE lead_id,address3 FROM osdial_list WHERE list_id='" . $osdial->mres($list_id) . "' AND address3!='';";
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my $tot_row = $sthA->rows();
	print " ADDR3 Rows: $tot_row \n" if ($DB);
	while (my @aryA = $sthA->fetchrow_array) {
		my $lead_id = $aryA[0];
		my $fullphone = $aryA[1];
		my $areacode = substr($fullphone, 0, 3);
		my $phone = substr($fullphone, 3, 7);
		my $esql = $ext_dnc{'External_DNC_SQL'};
		$esql =~ s/%AREACODE%/$areacode/g;
		$esql =~ s/%FULLPHONE%/$fullphone/g;
		$esql =~ s/%NUMBER%/$phone/g;

		my $fcnt = 0;
		my $sthB = $dbhB->prepare($esql) or die "preparing: ", $dbhB->errstr;
		$sthB->execute or die "executing: $esql ", $dbhB->errstr;
		while (my @aryB = $sthB->fetchrow_array) {
			$fcnt = 1 if ($aryB[0] > 0);
		}
		$sthB->finish();
		if ($fcnt) {
			print "  Found ADDR3 $lead_id : $fullphone \n" if ($DB);
			unless ($CLOtest) {
				my $stmtC = "INSERT IGNORE INTO osdial_dnc SET phone_number='$fullphone';";
				$dbhC->do($stmtC);
			}
		}
	}
	$sthA->finish();
}


exit_now();
#END

sub exit_now {
	$dbhA->disconnect() if ($dbhA);
	$dbhB->disconnect() if ($dbhB);
	$dbhC->disconnect() if ($dbhC);
	exit 0;
}

###############################################################
sub getOSDconfig {
	my($AGCpath) = @_;
	my %config;
	$config{PATHconf} = $AGCpath;
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

