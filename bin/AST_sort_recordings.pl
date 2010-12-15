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
#
# AST_sort_recordings.pl
#
# This is a STEP-3 program in the audio archival process
#
# runs every minute and moves the recordings into their permanent home.
# 
# * * * * * /opt/osdial/bin/AST_sort_recordings.pl
#
#
# This program assumes that recordings are saved by Asterisk as .wav
# 

use strict;
use OSDial;
use Getopt::Long;
use Proc::Exists ('pexists');
use Time::HiRes ('gettimeofday','usleep','sleep');

$|++;
$SIG{INT} = 'exit_now';

my $prog = "AST_sort_recordings.pl";

my $use_size_checks = 0;


my($CLOhelp,$verbose,$CLOtest,$clear_lock,$DB,$DBX);
my $lock_file = "/tmp/.osdial_sort_recordings.lock";

if (scalar @ARGV) {
	GetOptions(
		'help!' => \$CLOhelp,
		'debug!' => \$DB,
		'debugX!' => \$DBX,
		'verbose+' => \$verbose,
		'test!' => \$CLOtest,
		'clear_lock!' => \$clear_lock
	);
	$DB++ if ($DBX);
	$verbose++ if ($DBX);
	$verbose++ if ($DB);
	if ($CLOhelp) {
		print "\n\n" . $prog . "\n";
		print "allowed run-time options:\n";
		print "  [--help]         = This screen\n";
		print "  [--clear_lock]   = Clear the lock file.\n";
		print "  [--debug]        = 1x verbose\n";
		print "  [--debugX]       = 2x verbose\n";
		print "  [-v|--verbose]   = verbose\n";
		print "  [-t|--test]      = test only\n\n";
		exit 0;
	}
	print "----- DEBUGGING -----\n" if ($verbose > 1);
	print "----- EXTRA-VERBOSE DEBUGGING -----\n" if ($verbose > 2);
	print "----- Testing Mode -----\n" if ($CLOtest);
}	

my $osdial = OSDial->new('DB'=>$verbose);

unless ($osdial->{PATHarchive_home} and $osdial->{PATHarchive_mixed} and $osdial->{PATHarchive_sorted}) {
	print "ERROR!\n";
	print "PATHarchive_home, PATHarchive_mixed and PATHarchive_sorted\n";
	print "  must be defined in /etc/osdial.conf!!!\n\n";
	exit 1;
}

if ($clear_lock) {
	if (-e $lock_file) {
		print "Clearing lock file.\n\n";
		open(LOCK, $lock_file);
		my $pid = <LOCK>;
		kill 9, $pid if ($pid>0 and pexists($pid));
		close(LOCK);
		unlink($lock_file);
		exit 0;
	} else {
		print "No lock file found.\n";
	}
}

if (-e $lock_file) {
	open(LOCK, $lock_file);
	my $pid = <LOCK>;
	close(LOCK);
	if ($pid>0 and pexists($pid)) {
		print STDERR "ERROR: lock file found and process running.  If hung, run with --clear_lock to remove.\n\n";
		exit 1;
	} else {
		print STDERR "ERROR: lock file found and process NOT running, clearing file.\n\n";
		unlink($lock_file);
	}
}
if (! -e $lock_file) {
	open(LOCK, '>' . $lock_file);
	print LOCK $$;
	close(LOCK);
}


my $webpath = $osdial->{VARHTTP_path};
$webpath = $osdial->{configuration}{ArchiveWebPath} if ($osdial->{configuration}{ArchiveWebPath} ne "");
	

### directory where -all recordings are
my $rdir = $osdial->{PATHarchive_home} . '/' . $osdial->{PATHarchive_mixed};
my $odir = $osdial->{PATHarchive_home} . '/' . $osdial->{PATHarchive_sorted};

opendir(FILE, $rdir."/");
my @files = readdir(FILE);

my $cps = 2;
# Cycle through each file in the mixed directory
foreach my $file (@files) {
	if ($file =~ /\.wav$|\.gsm$|\.ogg$|\.mp3$/i) {
		# Check to see if filesize has changed.
		my $size_checks = 10;
		if ($use_size_checks) {
			foreach (1..$size_checks) {
				my $size1 = (-s "$rdir/$file");
				usleep(1000000/$cps);
				my $size2 = (-s "$rdir/$file");
				print "$file:  $size1 - $size2\n" if ($verbose);
				$size_checks-- if ($size1 eq $size2);
			}
		} else {
			$size_checks = 1;
			my $lsof_ret = `/usr/sbin/lsof -Xt '$rdir/$file'`;
			$size_checks = 0 unless ($lsof_ret);
		}

		# Grab a completed file of some format.
		if ($size_checks == 0) {
			# Pull SQL info from recording_log, list and campaign tables.
			my $SQLfile = $file;
			$SQLfile =~ s/-all\.wav|-all\.gsm|-all\.ogg|-all\.mp3//gi;
			my $stmt = "SELECT recording_log.recording_id AS rcid,osdial_lists.campaign_id AS camp,DATE(recording_log.start_time) AS date,recording_log.lead_id AS lead,recording_log.extension AS rlext FROM recording_log LEFT JOIN osdial_list ON (recording_log.lead_id=osdial_list.lead_id) LEFT JOIN osdial_lists ON (osdial_list.list_id=osdial_lists.list_id) WHERE filename='$SQLfile' ORDER BY recording_id DESC LIMIT 1;";
			print "$stmt|\n" if($verbose > 2);
			my $sret = $osdial->sql_query($stmt);

			my $extdir;
			my $rcid = $sret->{rcid};
			my $camp = $sret->{camp};
			my $date = $sret->{date};
			my $lead = $sret->{lead};
			my $rlext = $sret->{rlext};

			$extdir = "/" . $rlext if ($extdir eq "" and $SQLfile =~ /^PBX-IN|^PBX-OUT/);
	
			# If we can't identify it, tag it.
			$camp = "UNKNOWN" if ($camp eq "");
	
			# Following on applies to Vista.
			$extdir = "/AIG" if ($extdir eq "" and $file =~ /AIG/);
	
			# Move file into sorted location.
			my $destdir = $odir.'/'.$camp.'/'.$date.$extdir;
			system("mkdir", "-p", $destdir) unless ($CLOtest);
			system("mv", $rdir.'/'.$file, $destdir.'/'.$file) unless ($CLOtest);
			system("chmod", "777", $destdir.'/'.$file) unless ($CLOtest);
			print "Would mv $rdir/$file $destdir/$file\n" if ($CLOtest);
	
			my $event = $file . ' sorted';
	
			if ($rcid > 0) {
				# Update the recording_log with the http link.
				my $stmt = "UPDATE recording_log SET location='" . $webpath . '/' . $osdial->{PATHarchive_sorted} . "/$camp/$date$extdir/$file' WHERE recording_id='$rcid';";
				print "$stmt|\n" if($verbose > 2);
				my $rlsts = $osdial->sql_execute($stmt);
				print "." if ($verbose > 1);
				$event .= ', added to recording_log (' . $rlsts . ')' if ($rlsts > 0);
            	
				# If lead was found mark it as PENDING transfer, otherwise NOTFOUND...insert into qc_transfer_log.
				my $stmt = "INSERT IGNORE INTO qc_recordings (recording_id,lead_id,filename,location) VALUES('$rcid','$lead','$file','$destdir');";
				print "$stmt|\n" if($verbose > 2);
				my $qtlsts = $osdial->sql_execute($stmt);
				print "-" if ($verbose > 1);
				$event .= ', added to qc_recordings (' . $qtlsts . ')' if ($qtlsts > 0);
			} else {
				$event .= ', was not found in recording_log';
			}
	
			print $event . "\n" if ($verbose);
			$osdial->event_logger('sort_recordings',$event);
		}
	}
}

print "DONE... EXITING\n\n" if ($verbose);
unlink($lock_file);

exit 0;


sub exit_now {
        print "Killed: Clearing lock file.\n\n";
        unlink($lock_file);
}

