#!/usr/bin/perl
#
# upgrade_sql.pl:  A script to provide easy database migrations from one version
#                  to anohter.
#
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
#
# Todo:
# upstream vendor converion

use strict;
use DBI;
use Getopt::Long;
use IO::Interface::Simple;
use IO::Socket::Multicast;
use threads;
use threads::shared;
use Time::HiRes qw( usleep );
$|++;

my $prog = 'upgrade_sql.pl';

my $secStart = time();
my($DB, $CLOhelp, $CLOtest, $CLOsaf, $CLOinst);
my($dbhT,$dbhA);

# Get OSD configuration directives.
my $config = getOSDconfig('/etc/osdial.conf');

if (scalar @ARGV) {
	GetOptions(
		'help!' => \$CLOhelp,
		'debug!' => \$DB,
		'test!' => \$CLOtest,
		'skip-auth-fix!' => \$CLOsaf,
		'install!' => \$CLOinst
	);
	if ($DB) {
		print "----- DEBUGGING -----\n";
		print "----- Testing Mode -----\n" if ($CLOtest);
		print "VARS-\n";
		print "CLOhelp-     $CLOhelp\n";
		print "CLOtest-     $CLOtest\n";
		print "CLOsaf-      $CLOsaf\n";
		print "CLOinst-     $CLOinst\n";
		print "\n";
	}
	if ($CLOhelp) {
		print "\n\n" . $prog . "\n";
		print "allowed run-time options:\n";
		print "  [--help]         = This screen\n";
		print "  [--debug]        = debug\n";
		print "  [--install]      = Install DB on this server if not found.\n";
		print "  [--skip-auth-fix]= Skip the DB user authentication fixes\n";
		print "  [-t|--test]      = test only\n\n";
		exit 0;
	}
}

my $path = "./";
if ( -f "./upgrade_sql.map" ) {
	$path = "./";
} elsif ( -f "./sql/upgrade_sql.map" ) {
	$path = "./sql/";
} elsif ( -f "./extras/sql/upgrade_sql.map" ) {
	$path = "./extras/sql/";
} else {
	$path = $config->{PATHhome} . "/sql/";
}

my %vmap;
print "    SQL update process started!\n\n";
print "       WARNING: Do not stop this process, doing so will leave your update\n";
print "                in an incomplete state.  If for any reason this process has\n";
print "                been interrupted, you can restart it by running the following:\n";
print "                       /opt/osdial/bin/sql/upgrade_sql.pl\n\n";
print "    Reading version mappings, using " . $path . "upgrade_sql.map\n";
open (FILE, $path . "upgrade_sql.map");
while (my $line = <FILE>) {
	chomp $line;
	my ($l,$s) = split /\|/, $line;
	$vmap{$l} = $s;
}
close FILE;

my $connerr = 0;
my $install = 0;
my $examples = 0;
if ($CLOsaf != 1) {
	if ( ($CLOinst) and (! -f "/var/lib/mysql/" . $config->{VARDB_database} . "/system_settings.ibd") ) {
		if ( (! -d "/var/lib/mysql/" . $config->{VARDB_database}) ) {
			print "    OSDial database (" . $config->{VARDB_database} . ") is not detected, creating.\n";
			my $cdb = "CREATE DATABASE " . $config->{VARDB_database} . ";";
			`echo "$cdb" | mysql -u root`;
		}
		$connerr = 1;
		$install = 1;
		$examples = 1;
		$vmap{'install'} = '000000';
		$vmap{'examples'} = '999999';
	} else {
		print "Testing database connection...\n" if ($DB);
		$dbhT = DBI->connect( 'DBI:mysql:' . $config->{VARDB_database} . ':' . $config->{VARDB_server} . ':' . $config->{VARDB_port}, $config->{VARDB_user}, $config->{VARDB_pass} ) or ($connerr=1);
		$dbhT->do("GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		print "connerr:" . $connerr . "\n" if($DB);
		$dbhT->disconnect();
	}
	
	if (($connerr == 1) and (-d "/var/lib/mysql/" . $config->{VARDB_database})) {
		print "Correcting Database Permissions...\n" if ($DB);
		$connerr = 0;
		$dbhT = DBI->connect( 'DBI:mysql:' . $config->{VARDB_database} . ':127.0.0.1:' . $config->{VARDB_port}, "root", "" )
	  	or die "Couldn't connect to database: " . DBI->errstr;
		$dbhT->do("GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'127.0.0.1' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'localhost' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'127.0.0.1' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'localhost' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'127.0.0.1' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'localhost' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'127.0.0.1' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'localhost' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		$dbhT->do("GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'\%' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
		# Now grant for all IPs on this machine.
		my @interfaces = IO::Interface::Simple->interfaces;
		foreach my $interface (@interfaces) {
			my $ip = IO::Interface::Simple->new($interface);
                        if ($ip->address) {
				$dbhT->do("GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $ip->address . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
				$dbhT->do("GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $ip->address . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
				$dbhT->do("GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $ip->address . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';");
				$dbhT->do("GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $ip->address . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';") or ($connerr=1);
			}
		}
		if ($install < 1) {
			# Update MySQL auth for other servers...
			my $stmtT = "SELECT server_ip FROM servers;";
			my $sthT = $dbhT->prepare($stmtT) or die "preparing: ", $dbhT->errstr;
			$sthT->execute or die "executing: $stmtT ", $dbhT->errstr;
			my @sipary;
			while (my @aryT = $sthT->fetchrow_array) {
				push @sipary, $aryT[0];
			}
			$sthT->finish();
			foreach my $sip (@sipary) {
				$dbhT->do("GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $sip . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';");
				$dbhT->do("GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $sip . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';");
				$dbhT->do("GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $sip . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';");
				$dbhT->do("GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $sip . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';");
			}
		}
		$dbhT->disconnect();
	} elsif ($connerr == 1) {
		print "\n\nWARNING: Cannot update the user permissions in MySQL.";
		print   "\n         If you haven't already, Please run the update process on the MySQL server.";
		print   "\n         If you have and the problem persists, you should execute the following SQL statements directly:";
		print   "\n" . "        GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
		print   "\n" . "        GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
		print   "\n" . "        GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
		print   "\n" . "        GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
		print "\n\n";
	}
}

my @dbiconn = ('DBI:mysql:' . $config->{VARDB_database} . ':' . $config->{VARDB_server} . ':' . $config->{VARDB_port}, $config->{VARDB_user}, $config->{VARDB_pass});
$dbhA = DBI->connect(@dbiconn)
  or die "Couldn't connect to database: " . DBI->errstr;
print '    Connected to Database:  ' . $config->{VARDB_server} . '|' . $config->{VARDB_database} . "\n" if ($DB);

my ($stmtA, $sthA, @aryA, $ver);


print "    Starting upgrade loop...\n";
my $uploop = 0;
while ($uploop < 1) {
	if ($install) {
		$ver = 'install';
		$install = 0;
	} else {
		$stmtA = "SELECT version FROM system_settings;";
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		@aryA = $sthA->fetchrow_array;
		$ver = $aryA[0];
	}

	if ($examples and $vmap{$ver} eq "") {
		$ver = "examples";
		$examples = 0;
	}
	
	if ($vmap{$ver} eq "") {
		print "      Nothing left to do.\n";
		$uploop = 1;
	} elsif ($vmap{$ver} eq "2.0.X" or $vmap{$ver} eq "2.0.5") {
		print "      Upgrade is not possible with this version!\n";
		$uploop = 2;
	} else {
		print "      Got version $ver from database.\n";
		print "      Applying SQL update using " . $vmap{$ver} . ".sql ";
		open (SQL, $path . $vmap{$ver} . '.sql');
		my $sql;
		my $gosql = 0;
		my @chars = qw(- \ | /);
		my $cur = 0;
		while (my $line = <SQL>) {
			chomp $line;
			if ($line !~ /^#/ and $line ne '') {
				$sql .= $line;
				$gosql = 1 if ($line =~ /;$/);
			}
			if ($gosql) {
				my $nsql : shared;
				my $comm : shared;
				my $tdone : shared; 
				($nsql,$comm) = split(/##\|##/,$sql);
				$tdone = 0;
				if ($comm eq "") {
					print "\n  Trying ($nsql) " if ($DB);
					my $thr1 = threads->create(
						sub {
							my($dbh) = DBI->connect(@dbiconn);
							$dbh->do($nsql) unless ($CLOtest);
							$dbh->disconnect();
							$tdone = 1;
						});
					while (!$tdone) {
						$cur = 0 if ($cur == @chars);
						print $chars[$cur++];
						usleep(100000);
						print "\b";
					}
					$thr1->join();
					print ".";
				} else {
					$comm =~ s/#SQL#/$nsql/g;
					$comm =~ s/ ##/\n/g;
					$comm =~ s/;$//g;
					print "\n--------------------------------------------------------------------------------";
					print $comm;
					print "\nApplying...";
					my $thr1 = threads->create(
						sub {
							my($dbh) = DBI->connect(@dbiconn);
							$dbh->do($nsql) unless ($CLOtest);
							$dbh->disconnect();
							$tdone = 1;
						});
					while (!$tdone) {
						$cur = 0 if ($cur == @chars);
						print $chars[$cur++];
						usleep(100000);
						print "\b";
					}
					$thr1->join();
					print "*DONE*\n";
				}
				$gosql = 0;
				$sql = '';
			}
		}
		print "\n";
	}
}
$uploop--;
print "    Finished upgrade loop...\n";
exit $uploop;





########################################################################################
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
