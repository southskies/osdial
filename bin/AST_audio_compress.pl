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
# AST_CRON_audio_2_compress.pl
#
# This is a STEP-2 program in the audio archival process
#
# runs every 3 minutes and compresses the -all recording files to GSM format
# 
# put an entry into the cron of of your asterisk machine to run this script 
# every 3 minutes or however often you desire
#
# ### recording mixing/compressing/ftping scripts
##0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57 * * * * /opt/osdial/bin/AST_CRON_audio_1_move_mix.pl
# 0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48,51,54,57 * * * * /opt/osdial/bin/AST_CRON_audio_1_move_VDonly.pl
# 1,4,7,10,13,16,19,22,25,28,31,34,37,40,43,46,49,52,55,58 * * * * /opt/osdial/bin/AST_CRON_audio_2_compress.pl --GSM
# 2,5,8,11,14,17,20,23,26,29,32,35,38,41,44,47,50,53,56,59 * * * * /opt/osdial/bin/AST_CRON_audio_3_ftp.pl --GSM
#
# FLAGS FOR COMPRESSION CODECS
# --GSM = GSM 6.10 codec
# --MP3 = MPEG Layer3 codec
# --OGG = OGG Vorbis codec
#
# make sure that the following directories exist:
# /var/spool/asterisk/monitorDONE	# where the mixed -all files are put
# 
# This program assumes that recordings are saved by Asterisk as .wav
# 
# 80302-1958 - First Build
#

$GSM=0;   $MP3=0;   $OGG=0;   $WAV=0;

### begin parsing run-time options ###
if (length($ARGV[0])>1) {
	$i=0;
	while ($#ARGV >= $i) {
		$args = "$args $ARGV[$i]";
		$i++;
	}

	if ($args =~ /--help/i) {
		print "allowed run time options:\n  [--debug] = debug\n  [--debugX] = super debug\n  [-t] = test\n  [--GSM] = compress into GSM codec\n  [--MP3] = compress into MPEG-Layer-3 codec\n  [--OGG] = compress into OGG Vorbis codec\n\n";
		exit;
	} else {
		if ($args =~ /--debug/i) {
			$DB=1;
			print "\n----- DEBUG -----\n\n";
		}
		if ($args =~ /--debugX/i) {
			$DBX=1;
			print "\n----- SUPER DEBUG -----\n\n";
		}
		if ($args =~ /-t/i) {
			$T=1;   $TEST=1;
			print "\n-----TESTING -----\n\n";
		}
		if ($args =~ /--GSM/i) {
			$GSM=1;
			if ($DB) {print "GSM compression\n";}
		} elsif ($args =~ /--MP3/i) {
			$MP3=1;
			if ($DB) {print "MP3 compression\n";}
		} elsif ($args =~ /--OGG/i) {
			$OGG=1;
			if ($DB) {print "OGG compression\n";}
		} elsif ($args =~ /--WAV/i) {
			$WAV=1;
			if ($DB) {print "WAV compression\n";}
		}
	}
}
else
{
#print "no command line options set\n";
$MP3=1;
}


# default path to osdial.configuration file:
$PATHconf =		'/etc/osdial.conf';

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ( ($line =~ /^PATHhome/) && ($CLIhome < 1) )
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^PATHagi/) && ($CLIagi < 1) )
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ( ($line =~ /^PATHweb/) && ($CLIweb < 1) )
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ( ($line =~ /^PATHsounds/) && ($CLIsounds < 1) )
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ( ($line =~ /^PATHmonitor/) && ($CLImonitor < 1) )
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ( ($line =~ /PATHDONEmonitor/) && ($CLIDONEmonitor < 1) )
		{$PATHDONEmonitor = $line;   $PATHDONEmonitor =~ s/.*=//gi;}
	if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_server/) && ($CLIDB_server < 1) )
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_database/) && ($CLIDB_database < 1) )
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_user/) && ($CLIDB_user < 1) )
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_pass/) && ($CLIDB_pass < 1) )
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_port/) && ($CLIDB_port < 1) )
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	if ( ($line =~ /^VARFTP_host/) && ($CLIFTP_host < 1) )
		{$VARFTP_host = $line;   $VARFTP_host =~ s/.*=//gi;}
	if ( ($line =~ /^VARFTP_user/) && ($CLIFTP_user < 1) )
		{$VARFTP_user = $line;   $VARFTP_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARFTP_pass/) && ($CLIFTP_pass < 1) )
		{$VARFTP_pass = $line;   $VARFTP_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARFTP_port/) && ($CLIFTP_port < 1) )
		{$VARFTP_port = $line;   $VARFTP_port =~ s/.*=//gi;}
	if ( ($line =~ /^VARFTP_dir/) && ($CLIFTP_dir < 1) )
		{$VARFTP_dir = $line;   $VARFTP_dir =~ s/.*=//gi;}
	if ( ($line =~ /^VARHTTP_path/) && ($CLIHTTP_path < 1) )
		{$VARHTTP_path = $line;   $VARHTTP_path =~ s/.*=//gi;}
	if ( ($line =~ /^PATHarchive_home/) && ($CLIPATHarchive_home < 1) )
		{$PATHarchive_home = $line;   $PATHarchive_home =~ s/.*=//gi;}
	if ( ($line =~ /^PATHarchive_unmixed/) && ($CLIPATHarchive_unmixed < 1) )
		{$PATHarchive_unmixed = $line;   $PATHarchive_unmixed =~ s/.*=//gi;}
	if ( ($line =~ /^PATHarchive_mixed/) && ($CLIPATHarchive_mixed < 1) )
		{$PATHarchive_mixed = $line;   $PATHarchive_mixed =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP
if (!$VARDB_port) {$VARDB_port='3306';}

use DBI;	  

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


if ( ($GSM > 0) || ($OGG > 0) || ($WAV >0))
	{
	### find sox binary to do the compression
	$soxbin = '';
	if ( -e ('/usr/bin/sox')) {$soxbin = '/usr/bin/sox';}
	else 
		{
		if ( -e ('/usr/local/bin/sox')) {$soxbin = '/usr/local/bin/sox';}
		else
			{
			print "Can't find sox binary! Exiting...\n";
			exit
			}
		}
	}

if ($MP3 > 0)
	{
	### find lame mp3 encoder binary to do the compression
	$lamebin = '';
	if ( -e ('/usr/bin/lame')) {$lamebin = '/usr/bin/lame';}
	else 
		{
		if ( -e ('/usr/local/bin/lame')) {$lamebin = '/usr/local/bin/lame';}
		else
			{
			print "Can't find lame binary! Exiting...\n";
			exit
			}
		}
	}



### directory where -all recordings are
$dir1 = $PATHarchive_home . '/' . $PATHarchive_unmixed;
$dir2 = $PATHarchive_home . '/' . $PATHarchive_mixed . '/..';

 opendir(FILE, "$dir1/");
 @FILES = readdir(FILE);

$i=0;
foreach(@FILES) {
	$size1 = 0;
	$size2 = 0;

	if ( (length($FILES[$i]) > 4) && (!-d $FILES[$i]) ) {

		$size1 = (-s "$dir1/$FILES[$i]");
		if ($DBX) {print "$FILES[$i] $size1\n";}
		#sleep(1);
		$size2 = (-s "$dir1/$FILES[$i]");
		if ($DBX) {print "$FILES[$i] $size2\n\n";}

		if ( ($FILES[$i] !~ /out\.|in\.|lost\+found/i) && ($size1 eq $size2) && (length($FILES[$i]) > 4)) {
			$recording_id='';
			$ALLfile = $FILES[$i];
			$SQLFILE = $FILES[$i];
			$SQLFILE =~ s/-all\.wav|-all\.gsm//gi;

			$stmtA = "select recording_id from recording_log where filename='$SQLFILE' order by recording_id desc LIMIT 1;";
			if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0) {
				@aryA = $sthA->fetchrow_array;
				$recording_id =	"$aryA[0]";
			}
			$sthA->finish();


			if ($GSM > 0) {
				$GSMfile = $FILES[$i];
				$GSMfile =~ s/-all\.wav/-all.gsm/gi;

				if ($DB) {print "|$recording_id|$ALLfile|$GSMfile|     |$SQLfile|\n";}

				`$soxbin "$dir1/$ALLfile" "$dir2/mixed/$GSMfile"`;

				$stmtA = "UPDATE recording_log set location='http://$server_ip/$PATHarchive_mixed/../mixed/$GSMfile' where recording_id='$recording_id';";
					if($DBX){print STDERR "\n|$stmtA|\n";}
				$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			} elsif ($OGG > 0) {
				$OGGfile = $FILES[$i];
				$OGGfile =~ s/-all\.wav/-all.ogg/gi;

				if ($DB) {print "|$recording_id|$ALLfile|$OGGfile|     |$SQLfile|\n";}

				`$soxbin "$dir1/$ALLfile" "$dir2/mixed/$OGGfile"`;

				$stmtA = "UPDATE recording_log set location='http://$server_ip/$PATHarchive_mixed/../mixed/$OGGfile' where recording_id='$recording_id';";
					if($DBX){print STDERR "\n|$stmtA|\n";}
				$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			} elsif ($MP3 > 0) {
				$MP3file = $FILES[$i];
				$MP3file =~ s/-all\.wav/-all.mp3/gi;

				if ($DB) {print "|$recording_id|$ALLfile|$MP3file|     |$SQLfile|\n";}

				`$lamebin -b 16 -m m --silent "$dir1/$ALLfile" "$dir2/mixed/$MP3file"`;

				$stmtA = "UPDATE recording_log set location='http://$server_ip/$PATHarchive_mixed/../mixed/$MP3file' where recording_id='$recording_id';";
					if($DBX){print STDERR "\n|$stmtA|\n";}
				$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
				if ($affected_rows >= 1) {
					`rm -f "$dir1/$ALLfile"`;
				}
			} elsif ($WAV > 0) {
				$WAVfile = $FILES[$i];
				$WAVfile =~ s/-all\.wav/-all.wav/gi;

				if ($DB) {print "|$recording_id|$ALLfile|$WAVfile|     |$SQLfile|\n";}

				`$soxbin "$dir1/$ALLfile" "$dir2/mixed/$WAVfile"`;

				$stmtA = "UPDATE recording_log set location='http://$server_ip/$PATHarchive_mixed/../mixed/$WAVfile' where recording_id='$recording_id';";
					if($DBX){print STDERR "\n|$stmtA|\n";}
				$affected_rows = $dbhA->do($stmtA); #  or die  "Couldn't execute query:|$stmtA|\n";
			}
		}
	}
	$i++;
}

if ($DB) {print "DONE... EXITING\n\n";}

$dbhA->disconnect();


exit;
