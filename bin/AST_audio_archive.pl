#!/usr/bin/perl
#
# AST_audio_archive.pl
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
# AST_audio_archive.pl
#
# This is a STEP-1 program in the audio archival process
#
# runs every minute and copies the recording files to the recording directory or FTP server
# 
# * * * * * /opt/osdial/bin/AST_audio_archive.pl
#
# FLAG FOR NO DATE DIRECTORY ON FTP
# --NODATEDIR
#
# make sure that the following directories exist:
# /var/spool/asterisk/monitorDONE	# where the mixed -all files are put
# 
# This program assumes that recordings are saved by Asterisk as .wav
# 

use strict;
use OSDial;
use Getopt::Long;
use Net::Ping;
use Net::FTP;
use Proc::Exists ('pexists');
use Time::HiRes ('gettimeofday','usleep','sleep');

$|++;
$SIG{INT} = 'exit_now';

my $DB=0;
my $DBX=0;
my $VERBOSE=0;
my $TEST=0;
my $HELP=0;

my $CPS=2;
my $NODATEDIR=1;

my $use_size_checks = 0;
my $clear_lock=0;

my $lock_file = "/tmp/.osdial_audio_archive.lock";


if (scalar @ARGV) {
	GetOptions(
		'help!' => \$HELP,
		'debug!' => \$DB,
		'debugX!' => \$DBX,
		'verbose+' => \$VERBOSE,
		'test!' => \$TEST,
		'size_checks!' => \$use_size_checks,
		'clear_lock!' => \$clear_lock,
		'nodatedir!' => \$NODATEDIR,
		'CPS=i' => \$CPS
	);
	$DB=1 if ($VERBOSE);
	$DBX=1 if ($VERBOSE>1);
	$DB=1 if ($DBX);
	$DB=1 if ($TEST);
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
		print "nodatedir    $NODATEDIR\n";
		print "CPS-         $CPS\n";
		print "\n";
	}
	if ($HELP) {
		print "AST_audio_compress.pl: Allowed run time options:\n";
		print "  [--help] = This screen.\n";
		print "  [--debug] = debug\n";
		print "  [--debugX] = super debug\n";
		print "  [-v|--verbose] = verbose (debug)\n";
		print "  [-t|--test] = Run in test mode.\n";
		print "  [--nodatedir] = Do not create date sub-directory on FTP site (default).\n";
		print "  [--clear_lock] = Remove lock file.\n";
		print "  [--size_checks] = Use file-size check instead of open-file check.\n";
		print "  [--CPS] = Size checks per second.\n";
		exit;
	}
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






my $archive_host = $osdial->{VARFTP_host};
my $archive_port = $osdial->{VARFTP_port};
my $archive_user = $osdial->{VARFTP_user};
my $archive_pass = $osdial->{VARFTP_pass};
my $archive_path = $osdial->{VARFTP_dir};
my $archive_web_path = $osdial->{VARHTTP_path};

if ($osdial->{configuration}{ArchiveHostname} ne "") {
	$archive_host = $osdial->{configuration}{ArchiveHostname};
	$archive_port = $osdial->{configuration}{ArchivePort};
	$archive_user = $osdial->{configuration}{ArchiveUsername};
	$archive_pass = $osdial->{configuration}{ArchivePassword};
	$archive_path = $osdial->{configuration}{ArchivePath};
	$archive_web_path = $osdial->{configuration}{ArchiveWebPath};
}

my $dir2 = $osdial->{PATHmonitor};

opendir(FILE, "$dir2/");

my $i=0;
foreach my $file (readdir(FILE)) {
	my $size1 = 0;
	my $size2 = 0;

	if (length($file) > 4 and $file !~ /-all\.wav|-out\.wav|archive.lock|lost\+found/i and not -d $file) {

		my $size_checks = 10;
		if (-e "/var/spool/asterisk/record_cache") {
			$size_checks = 0;
		} else {
			if ($use_size_checks) {
				foreach (1..$size_checks) {
					$size1 = (-s "$dir2/$file");
					print "$file $size1\n" if ($DBX);
					usleep(1000000/$CPS);
					$size2 = (-s "$dir2/$file");
					print "$file $size2\n\n" if ($DBX);
					$size_checks-- if ($size1 eq $size2);
				}
			} else {
				$size_checks = 1;
				my $lsof_ret = `/usr/sbin/lsof -Xt '$dir2/$file'`;
				$size_checks = 0 unless ($lsof_ret);
			}
		}

		if ($size_checks==0) {
			my $recording_id;
			my $SQLfile = $file;
			$SQLfile =~ s/-in\.wav|-all\.wav//gi;
			my $ALLfile = $SQLfile . "-all.wav";
			my $INfile = $SQLfile . "-in.wav";
			my $OUTfile = $SQLfile . "-out.wav";

			if (-e $dir2 . "/" . $INfile) {
				`mv -f '$dir2/$INfile' '$dir2/$ALLfile'`;
				`rm -f '$dir2/$OUTfile'`;
			}
			
			if ($SQLfile =~ /^PBX-IN|^PBX-OUT/) {
				my($pcmp,$pdat,$puid,$pext,$pcnl) = split(/_/,$SQLfile);
				my $stmt = "SELECT SQL_NO_CACHE * FROM call_log WHERE server_ip='" . $osdial->{VARserver_ip} . "' AND uniqueid='$puid' LIMIT 1;";
				print STDERR "\n|$stmt|\n" if ($DBX);
				my $sret = $osdial->sql_query($stmt);
				if ($sret->{uniqueid} > 0) {
					my $clstart = $sret->{start_time};
					my $clstart_epoch = $sret->{start_epoch};
					my $clend = $sret->{end_time};
					my $clend_epoch = $sret->{end_epoch};
					my $cllensec = $sret->{length_in_sec};
					my $cllenmin = $sret->{length_in_min};
					my $psid;
					my $plist;
					my $plead;
					my $pcom;
					if ($SQLfile =~ /^PBX-IN/) {
						$psid = 'PBXIN';
						$plist = '10';
						$pcom = "PBX/External Inbound Call, from $pcnl to $pext.";
					} else {
						$psid = 'PBXOUT';
						$plist = '11';
						$pcom = "PBX/External Outbound Call, from $pext to $pcnl.";
					}
					$pext = '0000000000' if ($pext eq "");
					$pcnl = '0000000000' if ($pcnl eq "");

					my $ins = "INSERT INTO osdial_list SET entry_date='$clstart',modify_date='$clend',status='$pcmp',user='$pcmp',vendor_lead_code='$pext',custom1='$pext',custom2='$pext',external_key='" . $osdial->{VARserver_ip} . ":$puid',source_id='$psid',phone_code='1',phone_number='$pcnl',list_id='$plist',comments='$pcom';";
					$osdial->sql_execute($ins);

					my $stmt = "SELECT SQL_NO_CACHE lead_id FROM osdial_list WHERE external_key='" . $osdial->{VARserver_ip} . ":$puid' LIMIT 1;";
					print STDERR "\n|$stmt|\n" if ($DBX);
					my $sret = $osdial->sql_query($stmt);
					$plead = $sret->{lead_id} if ($sret->{lead_id} > 0);

					my $ins = "INSERT INTO recording_log SET channel='$pcnl',server_ip='" . $osdial->{VARserver_ip} . "',extension='$pext',start_time='$clstart',start_epoch='$clstart_epoch',end_time='$clend',end_epoch='$clend_epoch',length_in_sec='$cllensec',length_in_min='$cllenmin',filename='$SQLfile',lead_id='$plead',user='$pcmp',uniqueid='$puid';";
					$osdial->sql_execute($ins);
				}
			}


			my $start_date;
			my $dnt = 1;
			my $stmt = "SELECT SQL_NO_CACHE recording_id,start_time FROM recording_log WHERE filename='$SQLfile' ORDER BY recording_id DESC LIMIT 1;";
			print STDERR "\n|$stmt|\n" if ($DBX);
			my $sret = $osdial->sql_query($stmt);
			if ($sret->{recording_id} > 0) {
				$recording_id =	$sret->{recording_id};
				$start_date = $sret->{start_time};
				$start_date =~ s/ .*//gi;
				$dnt = 1;
			} else {
				$dnt = 0;
				$start_date = "NOTFOUND";
			}
			print "|$recording_id|$start_date|$ALLfile|     |$SQLfile|$dnt|\n" if ($DB);



			### BEGIN Remote file transfer
			my $p = Net::Ping->new();
			my $ping_good = $p->ping($archive_host);

			if ($ping_good) {
				my $start_date_PATH='';
				my $FTPdb=0;
				my $sts=0;
				`cp -f '$dir2/$ALLfile' '$osdial->{PATHarchive_backup}'`;
				if ($archive_host eq "127.0.0.1" or $archive_host eq 'localhost') {
						`mv '$dir2/$ALLfile' '$osdial->{PATHarchive_home}/$osdial->{PATHarchive_unmixed}'`;
				} else {
					$FTPdb=1 if ($DBX);
					my $ftp = Net::FTP->new($archive_host, Port => $archive_port, Debug => $FTPdb);
					if ($ftp->login($archive_user,$archive_pass)) {
						$ftp->cwd($archive_path);
						if ($NODATEDIR < 1) {
							$ftp->mkdir($start_date);
							$ftp->cwd($start_date);
							$start_date_PATH = "$start_date/";
						}
						$ftp->binary();
						$ftp->put("$dir2/$ALLfile", $ALLfile);
						$ftp->quit;

						`rm -f '$dir2/$ALLfile'`;
					}
				}

				my $stmt = "UPDATE recording_log SET location='" . $archive_web_path . "/" . $archive_path . "/$start_date_PATH$ALLfile' WHERE recording_id='$recording_id';" if ($dnt);
				print STDERR "\n|$stmt|\n" if ($DB);
				$osdial->sql_execute($stmt);
				$osdial->event_logger('audio_archive', "Recording: $recording_id  File: $ALLfile  Sent to: $archive_user\@$archive_host:$archive_web_path/$archive_path/$start_date_PATH");
			}
			### END Remote file transfer
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

