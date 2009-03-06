#!/usr/bin/perl
#
# AST_sort_recordings.pl
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
#
#
# DESCRIPTION:
# Sorts recordings and adds initial qc_transfer_log record.
#
# 80906-1905 - Initial build.

use strict;
use DBI;
use Getopt::Long;
use Net::Ping;
use Net::FTP;
$|++;

my $prog = "AST_sort_recordings.pl";

# Get AGC configuration directives.
my $config = getAGCconfig('/etc/osdial.conf');

unless ($config->{PATHarchive_home} and $config->{PATHarchive_mixed} and $config->{PATHarchive_sorted}) {
	print "ERROR!\n";
	print "PATHarchive_home, PATHarchive_mixed and PATHarchive_sorted\n";
	print "  must be defined in /etc/osdial.conf!!!\n\n";
	exit 1;
}

my($dbhA,$sthA,$sthArows,$stmtA);
my($CLOhelp,$verbose,$CLOtest);

if (scalar @ARGV) {
	GetOptions(
		'help!' => \$CLOhelp,
		'verbose+' => \$verbose,
		'test!' => \$CLOtest
	);
	if ($CLOhelp) {
		print "\n\n" . $prog . "\n";
		print "allowed run-time options:\n";
		print "  [--help]         = This screen\n";
		print "  [-v|--verbose]   = verbose\n";
		print "  [-t|--test]      = test only\n\n";
		exit 0;
	}
	print "----- DEBUGGING -----\n" if ($verbose > 1);
	print "----- EXTRA-VERBOSE DEBUGGING -----\n" if ($verbose > 2);
	print "----- Testing Mode -----\n" if ($CLOtest);
}	

$dbhA = DBI->connect('DBI:mysql:' .
	$config->{VARDB_database} . ':' . $config->{VARDB_server} . ':' . $config->{VARDB_port},
	$config->{VARDB_user}, $config->{VARDB_pass})
 or die "Couldn't connect to database: " . DBI->errstr;


### directory where -all recordings are
my $rdir = $config->{PATHarchive_home} . '/' . $config->{PATHarchive_mixed};
my $odir = $config->{PATHarchive_home} . '/' . $config->{PATHarchive_sorted};

opendir(FILE, $rdir."/");
my @files = readdir(FILE);

# Cycle through each file in the mixed directory
foreach my $file (@files) {
	# Grab a completed file of some format.
	if ($file =~ /\.wav|\.gsm|\.ogg|\.mp3/i) {
		# Pull SQL info from recording_log, list and campaign tables.
		my $SQLfile = $file;
		$SQLfile =~ s/-all\.wav|-all\.gsm|-all\.ogg|-all\.mp3//gi;
		$stmtA = "SELECT recording_log.recording_id,osdial_lists.campaign_id,DATE(recording_log.start_time),recording_log.lead_id FROM recording_log,osdial_list,osdial_lists WHERE recording_log.lead_id=osdial_list.lead_id AND osdial_list.list_id=osdial_lists.list_id AND filename='$SQLfile' ORDER BY recording_id DESC LIMIT 1;";
		print "$stmtA|\n" if($verbose > 2);
		$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
		$sthArows=$sthA->rows;
		if ($sthArows > 0) {
			my(@aryA,$camp,$date,$extdir,$destdir);
			my($rcid,$lead) = (0,0);
			
			@aryA = $sthA->fetchrow_array;
			$rcid = $aryA[0];
			$camp = $aryA[1];
			$date = $aryA[2];
			$lead = $aryA[3];

			# If we can't identify it, tag it.
			$camp = "UNKNOWN" if ($camp eq "");

			# Following on applies to Vista.
			$extdir = "/AIG" if ($file =~ /AIG/);

			# Move file into sorted location.
			$destdir = $odir.'/'.$camp.'/'.$date.$extdir;
			system("mkdir", "-p", $destdir) unless ($CLOtest);
			system("mv", $rdir.'/'.$file, $destdir.'/'.$file) unless ($CLOtest);
			system("chmod", "777", $destdir.'/'.$file) unless ($CLOtest);
			print "Would mv $rdir/$file $destdir/$file\n" if ($CLOtest);

			# Update the recording_log with the http link.
			$stmtA = "UPDATE recording_log SET location='" . $config->{VARHTTP_path} . '/' . $config->{PATHarchive_sorted} . "/$camp/$date$extdir/$file' WHERE recording_id='$rcid';";
			print "$stmtA|\n" if($verbose > 2);
			my $rlsts = $dbhA->do($stmtA) unless ($CLOtest);
			print "." if ($verbose > 1);
            
			# If lead was found mark it as PENDING transfer, otherwise NOTFOUND...insert into qc_transfer_log.
			$stmtA = "INSERT IGNORE INTO qc_recordings (recording_id,lead_id,filename,location) VALUES('$rcid','$lead','$file','$destdir');";
			print "$stmtA|\n" if($verbose > 2);
			my $qtlsts = $dbhA->do($stmtA) unless ($CLOtest or $file =~ /AIG/);
			print "-" if ($verbose > 1);
			
			my $event = $file . ' sorted';
			$event .= ', added to recording_log (' . $rlsts . ')' if ($rlsts > 0);
			$event .= ', added to qc_recordings (' . $qtlsts . ')' if ($qtlsts > 0);
			print $event . "\n" if ($verbose);
			eventLogger($config->{PATHlogs},'sort_recordings',$event);
			
		}
		$sthA->finish();
	}
}

print "DONE... EXITING\n\n" if ($verbose);
$dbhA->disconnect();

exit 0;


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
