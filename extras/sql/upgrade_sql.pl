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
my($DB, $CLOhelp, $CLOinfo, $CLOinst, $CLOsaf, $CLOtest, $CLOlatin, $CLOutf8, $CLOconvert);
my($dbhT,$dbhA);

# Get OSD configuration directives.
my $config = getOSDconfig('/etc/osdial.conf');

if (scalar @ARGV) {
	GetOptions(
		'debug!' => \$DB,
		'help!' => \$CLOhelp,
		'info!' => \$CLOinfo,
		'install!' => \$CLOinst,
		'skip-auth-fix!' => \$CLOsaf,
		'test!' => \$CLOtest,
		'use-latin1!' => \$CLOlatin,
		'use-utf8!' => \$CLOutf8,
		'convert!' => \$CLOconvert
	);
	if ($DB) {
		print "----- DEBUGGING -----\n";
		print "----- Testing Mode -----\n" if ($CLOtest);
		print "VARS-\n";
		print "DB-          $DB\n";
		print "CLOhelp-     $CLOhelp\n";
		print "CLOinfo-     $CLOinfo\n";
		print "CLOinst-     $CLOinst\n";
		print "CLOsaf-      $CLOsaf\n";
		print "CLOtest-     $CLOtest\n";
		print "CLOlatin-    $CLOlatin\n";
		print "CLOutf8-     $CLOutf8\n";
		print "CLOconvert-  $CLOconvert\n";
		print "\n";
	}
	if ($CLOhelp) {
		print "\n\n" . $prog . "\n";
		print "allowed run-time options:\n";
		print "  [--debug]                = debug\n";
		print "  [--help]                 = This screen\n";
		print "  [--info]                 = Display detailed information on changes.\n";
		print "  [--install]              = Install DB on this server if not found.\n";
		print "  [--skip-auth-fix]        = Skip the DB user authentication fixes.\n";
		print "  [-t|--test]              = test only.\n";
		print "  [--use-latin1|--use-utf8]= Use Latin1 (default) or UTF8 character-set.\n\n";
		print "  [--convert]              = Use this option with one of the above character-set\n";
		print "                             options to convert you database to that character-set.\n\n";
		exit 0;
	}
}

my $use_latin=1;
$use_latin=0 if ($CLOutf8);
$use_latin=1 if ($CLOlatin);

my $charmode = "SET NAMES 'latin1' COLLATE 'latin1_swedish_ci';";
$charmode = "SET NAMES 'utf8' COLLATE 'utf8_general_ci';" unless ($use_latin);
my $charsql = "CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci'";
$charsql = "CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'" unless ($use_latin);

$CLOsaf=1 if ($CLOconvert);

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
			my $cdb = "CREATE DATABASE " . $config->{VARDB_database} . " " . $charsql . ";";
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
		$dbhT->do($charmode) or ($connerr=1);
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
		$dbhT->do($charmode) or ($connerr=1);
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
		print "\n\n      GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
		print   "\n      GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
		print   "\n      GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
		print   "\n      GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARDB_server} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";

		if ($config->{VARDB_server} ne $config->{VARserver_ip}) {
			print "\n\n      GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARserver_ip} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
			print   "\n      GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARserver_ip} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
			print   "\n      GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARserver_ip} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
			print   "\n      GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARserver_ip} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
		}

		
		my @interfaces = IO::Interface::Simple->interfaces;
		foreach my $interface (@interfaces) {
			my $ip = IO::Interface::Simple->new($interface);
                        if ($interface =~ /^eth/ and $ip->address =~ /^10\.|^192\.168\.|^172\.(1[6-9]|2[0-9]|3[01])\./ and $ip->address ne $config->{VARDB_server} and $ip->address ne $config->{VARserver_ip}) {
				print "\n\n      GRANT GRANT OPTION on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARserver_ip} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
				print   "\n      GRANT GRANT OPTION on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARserver_ip} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
				print   "\n      GRANT UPDATE on mysql.* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARserver_ip} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
				print   "\n      GRANT ALL on " . $config->{VARDB_database} . ".* TO '" . $config->{VARDB_user} . "'\@'" . $config->{VARserver_ip} . "' IDENTIFIED BY '" . $config->{VARDB_pass} . "';";
			}
		}
		print "\n\n";
	}
}

my @dbiconn = ('DBI:mysql:' . $config->{VARDB_database} . ':' . $config->{VARDB_server} . ':' . $config->{VARDB_port}, $config->{VARDB_user}, $config->{VARDB_pass});
$dbhA = DBI->connect(@dbiconn)
  or die "Couldn't connect to database: " . DBI->errstr;
$dbhA->do($charmode);
print '    Connected to Database:  ' . $config->{VARDB_server} . '|' . $config->{VARDB_database} . "\n" if ($DB);

my ($stmtA, $sthA, @aryA, $ver);


if ($CLOconvert) {
	print "    Starting character-set conversion loop...\n";
	print "     Using \"$charsql\" for conversion string.\n";
	print "      Setting defaults for database " . $config->{VARDB_database} . " to ";
	if ($use_latin) {
		print "Latin1...";
	} else {
		print "UTF8...";
	}
	$dbhA->do("ALTER DATABASE " . $config->{VARDB_database} . " " . $charsql . ";") unless ($CLOtest);
	print "\n";

	my $stmtA = "SHOW TABLES;";
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my $dbh = DBI->connect(@dbiconn);
	$dbh->do($charmode);
	while (my @tables = $sthA->fetchrow_array) {
		print "       Converting table \"$tables[0]\" to ";
		if ($use_latin) {
			print "Latin1...";
		} else {
			print "UTF8...";
		}
		my $tdone :shared = 0; 
		my $thr1 = threads->create(
			sub {
				my @chars = qw(- \ | /);
				my $cur = 0;
				while (!$tdone) {
					$cur = 0 if ($cur == @chars);
					print $chars[$cur++];
					usleep(100000);
					print "\b";
				}
			});
		if ($use_latin) {
			$dbh->do("ALTER TABLE $tables[0] $charsql;") unless ($CLOtest);
		} else {
			$dbh->do("ALTER TABLE $tables[0] CONVERT TO $charsql;") unless ($CLOtest);
		}
		$tdone = 1;
		$thr1->join();
		print "\n";
	}
	$dbh->disconnect();
	if ($use_latin) {
		print "     Enabling the Latin1 character-set in OSDial...";
		$dbhA->do("UPDATE system_settings SET use_non_latin='0';") unless ($CLOtest);
	} else {
		print "     Enabling the UTF8 character-set in OSDial...";
		$dbhA->do("UPDATE system_settings SET use_non_latin='1';") unless ($CLOtest);
	}
	print "\n";
	print "    Finished character-set conversion loop...\n";
} else {
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
			my $sql;
			my $gosql = 0;
			my $linepos = 0;
			my $sqlfile = $vmap{$ver} . ".sql"; 
			print "        Current DB Version: $ver       SQL Update File: $sqlfile\n";
			open (SQL, $path . $sqlfile);
			my $dbh = DBI->connect(@dbiconn);
			$dbh->do($charmode);
			while (my $line = <SQL>) {
				chomp $line;
				if ($line !~ /^#/ and $line ne '') {
					$sql .= $line;
					$gosql = 1 if ($line =~ /;$/);
				}
				if ($gosql) {
					#my $nsql :shared;
					#my $comm :shared;
					my $tdone :shared = 0; 
					my ($nsql,$comm) = split(/##\|##/,$sql);
					#$tdone = 0;
					if ($CLOinfo != 1 or $comm eq "") {
						print "\n  Trying ($nsql) " if ($DB);
						my $thr1 = threads->create(
							sub {
								my @chars = qw(- \ | /);
								my $cur = 0;
								while (!$tdone) {
									$cur = 0 if ($cur == @chars);
									print $chars[$cur++];
									usleep(100000);
									print "\b";
								}
							});
						$dbh->do($nsql) unless ($CLOtest);
						$tdone = 1;
						$thr1->join();
						if ($linepos > 79) {
							print "\n";
							$linepos=0;
						}
						print ".";
						$linepos++;
					} else {
						$comm =~ s/#SQL#/$nsql/g;
						$comm =~ s/ ##/\n/g;
						$comm =~ s/;$//g;
						print "\n-------------------------------------------------------------------------------";
						print $comm;
						print "\nApplying...";
						my $thr1 = threads->create(
							sub {
								my @chars = qw(- \ | /);
								my $cur = 0;
								while (!$tdone) {
									$cur = 0 if ($cur == @chars);
									print $chars[$cur++];
									usleep(100000);
									print "\b";
								}
							});
						$dbh->do($nsql) unless ($CLOtest);
						$tdone = 1;
						$thr1->join();
						print "*DONE*\n";
						$linepos=0;
					}
					$gosql = 0;
					$sql = '';
				}
			}
			$dbh->disconnect();
			print "\n";
		}
	}
	$uploop--;
	print "    Finished upgrade loop...\n";
	exit $uploop;
}





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
