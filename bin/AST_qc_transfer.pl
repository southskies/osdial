#!/usr/bin/perl
#
# AST_qc_transfer.pl
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
# Transfers recordings to QC location based on use defined SQL.  Tracks
# recordings and the state of the transferred recording.  Has ability to
# transmit archive batches.
#
# 80906-2016 - Initial build.

use strict;
use DBI;
use Getopt::Long;
use IO::File;
use Net::FTP;
use Net::SCP;
use Net::SFTP;
use Net::uFTP;
use OSDial;
$|++;

my $DB=0;

my $prog = "AST_qc_transfer.pl";

# Get AGC configuration directives.
my $config = getAGCconfig('/etc/osdial.conf');

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

my $osdial = OSDial->new('DB'=>$DB);

$dbhA = DBI->connect('DBI:mysql:' .
	$config->{VARDB_database} . ':' . $config->{VARDB_server} . ':' . $config->{VARDB_port},
	$config->{VARDB_user}, $config->{VARDB_pass})
 or die "Couldn't connect to database: " . DBI->errstr;


# Cycle through each server
my @qc_servers;
$stmtA = "SELECT SQL_NO_CACHE *,DATE(batch_lastrun) AS batch_lastrun_date,HOUR(NOW()) AS hour,DATE(NOW()) AS date FROM qc_servers WHERE active='Y';";
print "$stmtA|\n" if($verbose > 2);
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
my $i = 0;
while ($i < $sthArows) {
	my $qcs = $sthA->fetchrow_hashref;
	$qc_servers[$i] = $qcs;
	$i++;  
}
$sthA->finish;
foreach my $qcs (@qc_servers) {
	my $error;
        $ENV{FTP_PASSIVE} = "0";
        if ($qcs->{transfer_method} eq "FTP") {
                $ENV{FTP_PASSIVE} = "1";
        } elsif ($qcs->{transfer_method} eq "FTPA") {
                $qcs->{transfer_method} = "FTP";
        }
	if ($qcs->{transfer_type} eq "IMMEDIATE") {
		$error = modeImmediate($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs);
	} elsif ($qcs->{transfer_type} eq "BATCH" or $qcs->{transfer_type} eq "ARCHIVE") {
		$error = modeBatch($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs) if ($qcs->{hour} >= $qcs->{batch_time} - 25 and $qcs->{batch_lastrun_date} ne $qcs->{date});
	}
	print "WARNING: " . $qcs->{name} . " returned without any SQL entries!\n" if ($verbose and $error eq "NOSQL");
}

print "\nExiting...\n\n" if ($verbose);
$dbhA->disconnect();
exit 0;

sub modeImmediate {
	my($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs) = @_;
	print "modeImmediate|\n" if ($verbose);

	my($recs,$errors);
	$recs = gatherEntries($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs);
	return "NOSQL" if ($recs eq "NOSQL");
	$errors = transferFiles($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs,$recs);
}

sub modeBatch {
	my($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs) = @_;
	print "modeBatch|\n" if ($verbose);

	my $update = "UPDATE qc_servers SET batch_lastrun=NOW() WHERE id='" . $qcs->{id} . "';";
	print "$update|\n" if($verbose > 2);
	$dbhA->do($update) unless ($CLOtest);

	my $recs;
	my $errors = 1;
	$recs = gatherEntries($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs);
	return "NOSQL" if ($recs eq "NOSQL");
	if ($qcs->{transfer_type} eq "ARCHIVE" and $qcs->{archive} ne "NONE") {
		my %dates;
		foreach my $rec (keys %{$recs}) {
			$dates{$recs->{$rec}->{date}} = 1;
		}
		foreach my $date (sort keys %dates) {
			$errors = transferArchive($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs,$recs,$date);
		}
	} else {
		$errors = transferFiles($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs,$recs);
	}

	# Oops, we had problems, reset the batch_lastrun so it will try again.
	if ($errors > 0) {
		my $update = "UPDATE qc_servers SET batch_lastrun=NULL WHERE id='" . $qcs->{id} . "';";
		print "$update|\n" if($verbose > 2);
		$dbhA->do($update) unless ($CLOtest);
	}
}


# Get SQL from qc_server_rules, tag the qc_transfer_log records that match and return the records needing processing.
sub gatherEntries {
	my($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs) = @_;
	my($stmtA,$sthA,$sthArows,$query,$where,$swhere,$insts,$insert,$rlfld);
	
	print "gatherEntries|\n" if ($verbose);
	
	$stmtA = "SELECT SQL_NO_CACHE query FROM qc_server_rules WHERE qc_server_id='" . $qcs->{id} . "';";
	print "$stmtA|\n" if($verbose > 2);
	$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	return "NOSQL" if($sthArows == 0);
	my $i = 0;
	while ($i < $sthArows) {
		my @row = $sthA->fetchrow_array;
		$swhere .= " AND (" . $row[0] . ")";
		$i++;
	}
	$sthA->finish;
	print "Using: " . $swhere . " in where\n" if ($verbose);

	# Setup the common SQL, ie where.
	$where =   "(qc_recordings.recording_id=recording_log.recording_id ";
	$where .=   "AND qc_recordings.lead_id=osdial_list.lead_id ";
	$where .=   "AND osdial_list.list_id=osdial_lists.list_id)";

	# Find recordings which match criteria and insert into the osdial_transfers table.
	$insert = "INSERT INTO qc_transfers (qc_server_id,qc_recording_id) ";
	$insert .= "SELECT prej.qc_server_id,prej.qc_recording_id FROM ";
	$insert .= "  (SELECT " . $qcs->{id} . " AS qc_server_id,qc_recordings.id AS qc_recording_id ";
	$insert .= "    FROM qc_recordings,recording_log,osdial_list,osdial_lists ";
	$insert .= "    WHERE " . $where . $swhere . ") AS prej ";
	$insert .= "LEFT JOIN qc_transfers AS sqct ON (sqct.qc_server_id=prej.qc_server_id AND sqct.qc_recording_id=prej.qc_recording_id) ";
	$insert .= "WHERE sqct.id IS NULL ON DUPLICATE KEY UPDATE qc_transfers.status=qc_transfers.status;";

	print "$insert|\n" if($verbose > 2);
	$insts = $dbhA->do($insert) unless ($CLOtest);

	$rlfld = "recording_log.recording_id,recording_log.channel,recording_log.server_ip,recording_log.extension,recording_log.start_time,recording_log.start_epoch,";
	$rlfld .= "recording_log.end_time,recording_log.end_epoch,recording_log.length_in_sec,recording_log.length_in_min,recording_log.lead_id,recording_log.user";

	$query = "SELECT SQL_NO_CACHE DISTINCT qc_transfers.*,qc_transfers.id AS qctid,qc_recordings.*,DATE(recording_log.start_time) AS date," . $rlfld . ",osdial_list.*,osdial_lists.*,";
	$query .= "DATE_FORMAT(recording_log.start_time,'\%Y') AS dateYYYY,DATE_FORMAT(recording_log.start_time,'\%m') AS dateMM,DATE_FORMAT(recording_log.start_time,'\%d') AS dateDD ";
	$query .= "FROM qc_transfers,qc_recordings,recording_log,osdial_list,osdial_lists ";
	$query .=  "WHERE (qc_transfers.qc_recording_id=qc_recordings.id) AND " . $where . $swhere . " AND (qc_transfers.status!='NOTFOUND' AND qc_transfers.status!='SUCCESS') ";
	$query .=  "AND qc_transfers.qc_server_id='" . $qcs->{id} . "';";

	my $recs;
	print "$query|\n" if($verbose > 2);
	$sthA = $dbhA->prepare($query) or die "preparing: ",$dbhA->errstr;
	$sthA->execute or die "executing: $query ", $dbhA->errstr;
	$sthArows=$sthA->rows;
	return "NOSQL" if($sthArows == 0);
	my $i = 0;
	while ($i < $sthArows) {
		my $thsh = $sthA->fetchrow_hashref;
		$recs->{$thsh->{filename}} = $thsh;
		$i++
	}
	$sthA->finish;
	
	return $recs;
}

sub transferFiles {
	my($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs,$recs) = @_;
	print "transferFiles|\n" if ($verbose);

	my($ftp,$fsts,$errors);
	if ($qcs->{transfer_method} eq "SFTP") {
		$ftp = Net::SFTP->new($qcs->{host}, user=>$qcs->{username}, password=>$qcs->{password}, debug=>0);
	} else {
		$ftp = Net::uFTP->new($qcs->{host}, type=>$qcs->{transfer_method}, debug=>0); 
		$fsts = $ftp->login($qcs->{username},$qcs->{password});
	}

	foreach my $rec (keys %{$recs}) {
		my($status,$update,$template,$file,$remoteloc);
		$recs->{$rec}->{dateND} = $recs->{$rec}->{date};
		$recs->{$rec}->{dateND} =~ s/-//g;
		$template = $qcs->{location_template};
		$template =~ s/\\/\//g;
		foreach my $key (keys %{$recs->{$rec}}) {
			$template =~ s/\[$key\]/$recs->{$rec}->{$key}/gi;
			print $key . " : " . $recs->{$rec}->{$key} . "\n" if ($verbose > 3);
		}
		$template = $recs->{$rec}->{date} if ($template eq '');
		$file = $recs->{$rec}->{location} . '/' . $recs->{$rec}->{filename};
		print "Attempting: $qcs->{transfer_method}://$qcs->{host}:$qcs->{home_path}/$template/$recs->{$rec}->{filename}\n" if($verbose > 0);
		if (-e $file) {
			$status = "SUCCESS";
			$status = "FAILURE" if ($fsts == 0 and $qcs->{transfer_method} ne "SFTP");
			print "Failed to login: $fsts\n" if ($status eq "FAILURE" and $verbose > 2);
			$ftp->cwd($qcs->{home_path}) or $status="FAILURE" unless ($status eq "FAILURE" or $qcs->{transfer_method} eq "SFTP");
			print "Failed to cwd: $qcs->{home_path}\n" if ($status eq "FAILURE" and $verbose > 2);
			if ($template ne "" and $qcs->{transfer_method} ne "SFTP") {
				$ftp->mkdir($template, 1) or $status="FAILURE" unless ($status eq "FAILURE");
				print "Failed to mkdir: $template\n" if ($status eq "FAILURE" and $verbose > 2);
				$ftp->cwd($template) or $status="FAILURE" unless ($status eq "FAILURE");
				print "Failed to cwd: $template\n" if ($status eq "FAILURE" and $verbose > 2);
			}
			$ftp->binary if ($qcs->{transfer_method} eq "FTP");
			$ftp->put($file,$qcs->{home_path} . '/' . $template . '/' . $recs->{$rec}->{filename}) or $status="FAILURE" unless ($status eq "FAILURE");
			print "Failed to put: $file\n" if ($status eq "FAILURE" and $verbose > 2);
			$ftp->ascii if ($qcs->{transfer_method} eq "FTP");
			$remoteloc = $qcs->{home_path};
			if (substr($remoteloc,(length($remoteloc)-1),1) eq "/") {
				$remoteloc .= $template;
			} else {
				$remoteloc .= '/' . $template;
			}
		} else {
			$status = "NOTFOUND";
			$remoteloc = '';
		}

		$update = "UPDATE qc_transfers SET status='" . $status . "',remote_location='" . $osdial->mres($remoteloc) . "',archive_filename='',last_attempt=NOW() WHERE id='" . $recs->{$rec}->{qctid} . "';";
		print "$update|\n" if($verbose > 2);
		$dbhA->do($update) unless ($CLOtest);

		$errors++ if ($status eq "FAILURE");
	}
	$ftp->quit;
	return $errors;
}

sub transferArchive {
	my($osdial,$config,$verbose,$CLOtest,$dbhA,$qcs,$recs,$date) = @_;
	print "transferArchive|\n" if ($verbose);

	# Setup tmp-path and archive filename. Ie /tmp/20080907_name.type
	my $tmpdir = '/tmp';
	my $name = $qcs->{name};
	$name =~ s/\W//g;
	$date =~ s/\D//g;
	$qcs->{archive} = "ZIP" unless ($qcs->{archive});
	$qcs->{archive} = "ZIP" if ($qcs->{archive} eq "NONE");
	my $archive = $date . '_' . $name . '.' . lc($qcs->{archive});
	my $tarchive = $date . '_' . $name . '.tar';
	my($ftp,$fsts,$errors,$status,$remoteloc);

	unlink("/tmp/" . $archive);
	unlink("/tmp/" . $tarchive);

	my ($starttime, $endtime, $tottime);
	$starttime = time();
	my $top_count = scalar keys %{$recs};
	my $count = 1;
	# Add each record to an archive.
	my($template);
	foreach my $rec (keys %{$recs}) {
		my $event = $count . " of " . $top_count . "|";
		$template = $qcs->{location_template};
		$template =~ s/\\/\//g;
		foreach my $key (keys %{$recs->{$rec}}) {
			$template =~ s/\[$key\]/$recs->{$rec}->{$key}/gi;
			print $key . " : " . $recs->{$rec}->{$key} . "\n" if ($verbose > 3);
		}
		print "TEMPLATE: $template\n" if ($verbose > 3);

		# Add files to archives.
		chdir($recs->{$rec}->{location});	
		my $file = $recs->{$rec}->{filename};
		if (-e $file and $recs->{$rec}->{status} ne "SUCCESS") {
			# Process archive.
			my $arch = $tmpdir . '/' . $archive;
			my $tarch = $tmpdir . '/' . $tarchive;
			my(@args,$taropt);
			if ($qcs->{archive} =~ /TAR|TGZ|TBZ2/ ) {
				$taropt = "--create";
				$taropt = "--append" if (-e $tarch);
				@args = ('/bin/tar',$taropt,'-f',$tarch,'--transform=s,^,./' . $date . '_' . $name . '/,',$file);
			} elsif ($qcs->{archive} eq "ZIP") {
				@args = ('/usr/bin/zip','-q','-0','-g',$arch,$file);
			}
			system(@args) == 0 or $errors++;

			my $update = "UPDATE qc_transfers SET status='PROCESSING',remote_location='',archive_filename='',last_attempt=NOW() WHERE id='" . $recs->{$rec}->{qctid} . "';";
			print "$update|\n" if($verbose > 2);
			print "a" if ($verbose == 2);
			$dbhA->do($update) unless ($CLOtest);
			$event .= $qcs->{archive} . "|$arch|$file|";
		} else {
			$event = "NOT ARCHIVED|" . $recs->{$rec}->{status} . "|";
		}
		eventLogger($config->{PATHlogs},"qc_transfer","transferArchive|ARCHIVE|" . $event);
		$count++;
	}

	# Compress the tar archives...
	chdir("/tmp");	
	if ($qcs->{archive} eq 'TGZ' ) {
		system("gzip",$tarchive);
		system("mv",$tarchive . ".gz", $archive);
	} elsif ($qcs->{archive} eq 'TBZ2' ) {
		system("bzip2",$tarchive);
		system("mv",$tarchive . ".bz2", $archive);
	}

	$endtime = time();
	$tottime = $endtime - $starttime;
	eventLogger($config->{PATHlogs},"qc_transfer","transferArchive|ARCHIVE|Total Time " . $tottime ."|");

	$starttime = time();
	# Finally, send the file...
	if (-e $tmpdir . '/' . $archive) {
		$ftp = Net::uFTP->new($qcs->{host}, type=>$qcs->{transfer_method}, debug=>0); 
		$fsts = $ftp->login($qcs->{username},$qcs->{password});
		$status = "SUCCESS";
		$status = "FAILURE" if ($fsts == 0);
		$ftp->cwd($qcs->{home_path}) or $status="FAILURE" unless ($status eq "FAILURE");
		if ($template ne "") {
			$ftp->mkdir($template, 1) or $status="FAILURE" unless ($status eq "FAILURE");
			$ftp->cwd($template) or $status="FAILURE" unless ($status eq "FAILURE");
		}
		$ftp->binary or $status="FAILURE" unless ($status eq "FAILURE");
		$ftp->put($tmpdir . '/' . $archive,$archive) or $status="FAILURE" unless ($status eq "FAILURE");
		$ftp->ascii or $status="FAILURE" unless ($status eq "FAILURE");
		$remoteloc = $qcs->{home_path};
		$remoteloc .= '/' . $template if ($template ne "");
		$ftp->quit;
		$errors++ if ($status eq "FAILURE");

		foreach my $rec (keys %{$recs}) {
			if ($errors > 0) {
				$status = "FAILURE";
				$remoteloc = '';
				$archive = '';
			}
			my $update = "UPDATE qc_transfers SET status='" . $status . "',remote_location='" . $osdial->mres($remoteloc) . "',archive_filename='" . $osdial->mres($archive) . "',last_attempt=NOW() ";
			$update .= "WHERE id='" . $recs->{$rec}->{qctid} . "' AND status!='NOTFOUND';";
			print "$update|\n" if($verbose > 2);
			$dbhA->do($update) unless ($CLOtest);
		}
	}
	unlink $tmpdir . '/' . $archive unless ($verbose);
	unlink $tmpdir . '/' . $tarchive unless ($verbose);
	$endtime = time();
	$tottime = $endtime - $starttime;
	eventLogger($config->{PATHlogs},"qc_transfer","transferArchive|TRANSFER|Total Time " . $tottime ."|");
	unlink("/tmp/" . $archive);
	unlink("/tmp/" . $tarchive);
	return $errors;
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
	my $stmtA = "SELECT * FROM servers where server_ip='" . $serverip ."';";
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
