#!/usr/bin/perl
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
# AST_audio_compress.pl
#
# This is a STEP-2 program in the audio archival process
#
# runs every minute and compresses the recording files to MP3/GSM/WAV/OGG format
# 
# * * * * * /opt/osdial/bin/AST_audio_compress.pl --MP3
#
# FLAGS FOR COMPRESSION CODECS
# --GSM = GSM 6.10 codec
# --MP3 = MPEG Layer3 codec
# --OGG = OGG Vorbis codec
#
# This program assumes that recordings are saved by Asterisk as .wav

use strict;
use Time::HiRes ('gettimeofday','usleep','sleep');
use OSDial;
use Proc::Exists ('pexists');
use Getopt::Long;

$|++;
$SIG{INT} = 'exit_now';


my $DB=0;
my $DBX=0;
my $VERBOSE=0;
my $TEST=0;
my $HELP=0;

my $GSM=0;
my $MP3=0;
my $OGG=0;
my $WAV=0;

my $CPS=2;
my $use_size_checks=0;
my $clear_lock=0;

my $lock_file = "/tmp/.osdial_audio_compress.lock";


if (scalar @ARGV) {
	GetOptions(
		'help!' => \$HELP,
		'debug!' => \$DB,
		'debugX!' => \$DBX,
		'verbose+' => \$VERBOSE,
		'test!' => \$TEST,
		'size_checks!' => \$use_size_checks,
		'clear_lock!' => \$clear_lock,
		'CPS=i' => \$CPS,
		'GSM!' => \$GSM,
		'MP3!' => \$MP3,
		'OGG!' => \$OGG,
		'WAV!' => \$WAV
	);
	$DB=1 if ($VERBOSE);
	$DBX=1 if ($VERBOSE>1);
	$DB=1 if ($DBX);
	$DB=1 if ($TEST);
	if ($MP3) {
		$GSM=0;
		$OGG=0;
		$WAV=0;
	} elsif ($GSM) {
		$MP3=0;
		$OGG=0;
		$WAV=0;
	} elsif ($OGG) {
		$GSM=0;
		$MP3=0;
		$WAV=0;
	} elsif ($WAV) {
		$GSM=0;
		$OGG=0;
		$MP3=0;
	} else {
		$MP3=1;
	}
	if ($DB) {
		print "----- DEBUGGING -----\n";
		print "----- Testing Mode -----\n" if ($TEST);
		print "VARS-\n";
		print "help-        $HELP\n";
		print "debug-       $DB\n";
		print "debugX-      $DBX\n";
		print "verbose-     $VERBOSE\n";
		print "test-        $TEST\n";
		print "clear_lock-  $clear_lock\n";
		print "size_checks- $use_size_checks\n";
		print "CPS-         $CPS\n";
		print "GSM-         $GSM\n";
		print "MP3-         $MP3\n";
		print "OGG-         $OGG\n";
		print "WAV-         $WAV\n";
		print "\n";
	}
	if ($HELP) {
		print "AST_audio_compress.pl: Allowed run time options:\n";
		print "  [--help] = This screen.\n";
		print "  [--debug] = debug\n";
		print "  [--debugX] = super debug\n";
		print "  [-v|--verbose] = verbose (debug)\n";
		print "  [-t|--test] = Run in test mode.\n";
		print "  [--clear_lock] = Remove lock file.\n";
		print "  [--size_checks] = Use file-size check instead of open-file check.\n";
		print "  [--CPS] = Size checks per second.\n";
		print "  [--GSM] = compress into GSM codec\n";
		print "  [--MP3] = compress into MPEG-Layer-3 codec\n";
		print "  [--OGG] = compress into OGG Vorbis codec\n\n";
		print "  [--WAV] = compress into WAV\n\n";
		exit;
	}
} else {
	$MP3=1;
}

my $osdial = OSDial->new('DB'=>$DB);


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


my $sret = $osdial->sql_query("SELECT data FROM configuration WHERE name='ArchiveMixFormat';");
if ($osdial->{configuration}{ArchiveMixFormat} ne "") {
	$MP3=0;
	$OGG=0;
	$GSM=0;
	$WAV=0;
	$MP3=1 if ($osdial->{configuration}{ArchiveMixFormat} eq "MP3");
	$OGG=1 if ($osdial->{configuration}{ArchiveMixFormat} eq "OGG");
	$GSM=1 if ($osdial->{configuration}{ArchiveMixFormat} eq "GSM");
	$WAV=1 if ($osdial->{configuration}{ArchiveMixFormat} eq "WAV");
	print "Overriding Mix Format with " . $osdial->{configuration}{ArchiveMixFormat} . ".\n" if ($DB);
}


my $soxbin = '';
if ( -e '/usr/bin/sox') {
	$soxbin = '/usr/bin/sox';
} elsif ( -e '/usr/local/bin/sox') {
	$soxbin = '/usr/local/bin/sox';
}

my $lamebin = '';
my $lameopts = '';
if (-e '/usr/bin/toolame') {
	$lamebin = '/usr/bin/toolame';
	$lameopts = '-s 16 -b 16 -m j';
} elsif (-e '/usr/local/bin/toolame') {
	$lamebin = '/usr/local/bin/toolame';
	$lameopts = '-s 16 -b 16 -m j';
} elsif (-e '/usr/bin/lame') {
	$lamebin = '/usr/bin/lame';
	$lameopts = '-b 16 -m m --silent';
} elsif (-e '/usr/local/bin/lame') {
	$lamebin = '/usr/local/bin/lame';
	$lameopts = '-b 16 -m m --silent';
}

if ($MP3) {
	### Exit if lamebin not found and GSM/OGG/WAV
	if ($lamebin eq '') {
		print "Can't find lame binary! Exiting...\n";
		exit 2;
	}
} else {
	### Exit if soxbin not found and GSM/OGG/WAV
	if ($soxbin eq '') {
		print "Can't find sox binary! Exiting...\n";
		exit 2;
	}
}



my $dir1 = $osdial->{PATHarchive_home} . '/' . $osdial->{PATHarchive_unmixed};
my $dir2 = $osdial->{PATHarchive_home} . '/' . $osdial->{PATHarchive_mixed} . '/..';

opendir(FILE, "$dir1/");

my $i = 0;
foreach my $file (readdir(FILE)) {
	my $size1 = 0;
	my $size2 = 0;

	if (length($file) > 4 and $file !~ /out\.|in\.|lost\+found/i and not -d $file) {
		my $size_checks = 10;
		if ($use_size_checks) {
			foreach (1..$size_checks) {
				$size1 = (-s "$dir1/$file");
				print "$file $size1\n" if ($DBX);
				usleep(1000000/$CPS);
				$size2 = (-s "$dir1/$file");
				print "$file $size2\n\n" if ($DBX);
				$size_checks-- if ($size1 eq $size2);
			}
		} else {
			$size_checks = 1;
			if ($file =~ /\.wav$/i) {
				my $lsof_ret = `/usr/sbin/lsof -Xt '$dir1/$file'`;
				$size_checks = 0 unless ($lsof_ret);
				print "$dir1/$file $size_checks\n" if ($DBX);
			}
		}

		if ($size_checks == 0) {
			my $recording_id;
			my $SQLfile = $file;
			$SQLfile =~ s/-all\.wav|-all\.gsm//gi;

			my $stmt = "SELECT recording_id FROM recording_log WHERE filename='$SQLfile' ORDER BY recording_id DESC LIMIT 1;";
			print STDERR "\n|$stmt|\n" if ($DBX);
			my $sret = $osdial->sql_query($stmt);
			$recording_id =	$sret->{recording_id};

			my $location;
			my $CNVfile = $file;

			if ($TEST) {
				print "|$recording_id|$file|$CNVfile|     |$SQLfile|\n";

			} elsif (-s "$dir1/$file" == 0) {
				unlink("$dir1/$file");
				$osdial->event_logger('audio_compress',"Removed empty file: $dir1/$file   |$recording_id|$SQLfile||");

			} elsif ($GSM or $OGG or $WAV) {
				$CNVfile =~ s/-all\.wav/-all.gsm/gi if ($GSM);
				$CNVfile =~ s/-all\.wav/-all.ogg/gi if ($OGG);
				$CNVfile =~ s/-all\.wav/-all.wav/gi if ($WAV);
				print "|$recording_id|$file|$CNVfile|     |$SQLfile|\n" if ($DB);
				`$soxbin '$dir1/$file' '$dir2/mixed/$CNVfile'`;
				unlink("$dir1/$file") if (-e "$dir2/mixed/$CNVfile");
				$location = "http://" . $osdial->{VARserver_ip} . "/" . $osdial->{PATHarchive_mixed} . "/../mixed/" . $CNVfile;

			} elsif ($MP3 > 0) {
				$CNVfile =~ s/-all\.wav/-all.mp3/gi;
				print "|$recording_id|$file|$CNVfile|     |$SQLfile|\n" if ($DB);

				# WAV must be 16k to convert using toolame
				if ($lamebin =~ /toolame/) {
					my $junk = `mv '$dir1/$file' /tmp`;
					$junk = `$soxbin '/tmp/$file' -r 16000 -c 1 '$dir1/$file' resample -ql`;
					$junk = unlink("/tmp/$file");
				}

				my $junk = `$lamebin $lameopts '$dir1/$file' '$dir2/mixed/$CNVfile'`;
				unlink("$dir1/$file") if (-e "$dir2/mixed/$CNVfile");
				$location = "http://" . $osdial->{VARserver_ip} . "/" . $osdial->{PATHarchive_mixed} . "/../mixed/" . $CNVfile;

			}
			if (!$TEST) {
				my $stmt = "UPDATE recording_log SET location='" . $location . "' WHERE recording_id='" . $recording_id . "';";
				print STDERR "\n|$stmt|\n" if ($DBX);
				$osdial->sql_execute($stmt);
				$osdial->event_logger('audio_compress',"Compressed: $file   to   $CNVfile   |$recording_id|$SQLfile|$location|");
			}
		}
	}
	$i++;
}

print "DONE... EXITING\n\n" if ($DB);
unlink($lock_file);


exit 0;

sub exit_now {
	print "Killed: Clearing lock file.\n\n";
	unlink($lock_file);
}
