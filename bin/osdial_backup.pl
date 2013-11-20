#!/usr/bin/perl
$|++;
#
# osdial_backup.pl    version 3.0.1
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

my $prog = 'osdial_backup.pl';
my ($CLOhelp, $DB, $CLOtest, $CLOquiet, $Wall, $WOall, $Wdb, $WOdb, $Wconf, $WOconf, $Wweb, $WOweb, $Wsounds, $WOsounds, $Warchive, $WOarchive, $CLOftp);

# Read in command-line options.
if (scalar @ARGV) {
        GetOptions(
                'help!' => \$CLOhelp,
                'with-all!' => \$Wall,
                'without-all!' => \$WOall,
                'with-db!' => \$Wdb,
                'without-db!' => \$WOdb,
                'with-conf!' => \$Wconf,
                'without-conf!' => \$WOconf,
                'with-sounds!' => \$Wsounds,
                'without-sounds!' => \$WOsounds,
                'with-archive!' => \$Warchive,
                'without-archive!' => \$WOarchive,
                'with-web!' => \$Wweb,
                'without-web!' => \$WOweb,
                'ftp-transfer!' => \$CLOftp,
                'debug!' => \$DB,
                'test!' => \$CLOtest,
                'quiet!' => \$CLOquiet,
        );
        if ($DB) {
                print "----- DEBUGGING -----\n";
                print "----- Testing Mode -----\n" if ($CLOtest);
                print "VARS-\n";
                print "CLOhelp-     $CLOhelp\n";
                print "DB-          $DB\n";
                print "CLOquiet-    $CLOquiet\n";
                print "CLOtest-     $CLOtest\n";
                print "CLOftp-      $CLOftp\n";
                print "Wall-        $Wall\n";
                print "WOall-       $WOall\n";
                print "Wdb-         $Wdb\n";
                print "WOdb-        $WOdb\n";
                print "Wconf-       $Wconf\n";
                print "WOconf-      $WOconf\n";
                print "Wweb-        $Wweb\n";
                print "WOweb-       $WOweb\n";
                print "Wsounds-     $Wsounds\n";
                print "WOsounds-    $WOsounds\n";
                print "Warchive-    $Warchive\n";
                print "WOarchive-   $WOarchive\n";
                print "\n";
        }
        if ($CLOhelp) {
                print "\n\n" . $prog;
                print "allowed run-time options:\n";
                print "  [-h|--help]        = This screen\n";
		print "  [--with-all]       = backup database, configs, webfiles, sounds, and archives.\n";
		print "  [--with-db]        = (default) backup the database\n";
		print "  [--with-conf]      = (default) backup the conf files\n";
		print "  [--with-web]       = (default) backup web files\n";
		print "  [--with-sounds]    = (default) backup asterisk sounds\n";
		print "  [--with-archive]   = backup archived recordings and reports\n";
		print "  [--without-all]    = do not backup database, configs, webfiles, and sounds.\n";
		print "  [--without-db]     = do not backup the database\n";
		print "  [--without-conf]   = do not backup the conf files\n";
		print "  [--without-web]    = do not backup web files\n";
		print "  [--without-sounds] = do not backup asterisk sounds\n";
		print "  [--without-archive]= do not backup archived recordings and reports\n";
		print "  [--ftp-transfer]   = Transfer backup to FTP server\n";
		print "  [-d|--debug]       = debug\n";
                print "  [-t|--test]        = test only\n";
                print "  [-q|--quiet]       = Quiet output\n";
                exit 0;
        }
}
chdir('/');
my $osd = OSDial->new('DB'=>$DB);
my $dt = $osd->get_datetime();
$dt =~ s/[ \-\:]//g;
$osd->{'BACKUPpath'} = '/opt/osdial/backups';
my $DBX=1 if ($DB>1);
my $FTPdebug=1 if ($DB>0);
my $T=1 if ($CLOtest>0);
my $TEST=1 if ($CLOtest>0);
my $blist = {'db'=>1, 'conf'=>1, 'web'=>1, 'sounds'=>1, 'archive'=>0};

while (my $sret = $osd->sql_query(sprintf("SELECT * FROM servers WHERE server_ip='%s';",$osd->mres($osd->{'VARserver_ip'})))) {
	if ($sret->{'server_profile'} eq 'AIO') {
		$blist->{'db'}=1;
		$blist->{'conf'}=1;
		$blist->{'web'}=1;
		$blist->{'sounds'}=1;
		$blist->{'archive'}=0;
	} elsif ($sret->{'server_profile'} eq 'CONTROL') {
		$blist->{'db'}=1;
		$blist->{'conf'}=1;
		$blist->{'web'}=1;
		$blist->{'sounds'}=0;
		$blist->{'archive'}=0;
	} elsif ($sret->{'server_profile'} eq 'SQL') {
		$blist->{'db'}=1;
		$blist->{'conf'}=1;
		$blist->{'web'}=0;
		$blist->{'sounds'}=0;
		$blist->{'archive'}=0;
	} elsif ($sret->{'server_profile'} eq 'WEB') {
		$blist->{'db'}=0;
		$blist->{'conf'}=1;
		$blist->{'web'}=1;
		$blist->{'sounds'}=0;
		$blist->{'archive'}=0;
	} elsif ($sret->{'server_profile'} eq 'DIALER') {
		$blist->{'db'}=0;
		$blist->{'conf'}=1;
		$blist->{'web'}=0;
		$blist->{'sounds'}=1;
		$blist->{'archive'}=0;
	} elsif ($sret->{'server_profile'} eq 'ARCHIVE') {
		$blist->{'db'}=0;
		$blist->{'conf'}=1;
		$blist->{'web'}=0;
		$blist->{'sounds'}=0;
		$blist->{'archive'}=1;
	}
}
print "  -- Setting defaults based on server type, any passed options will override\n" if (!$CLOquiet);
print "       DB:      ".$blist->{'db'}."\n" if (!$CLOquiet);
print "       CONF:    ".$blist->{'conf'}."\n" if (!$CLOquiet);
print "       WEB:     ".$blist->{'web'}."\n" if (!$CLOquiet);
print "       SOUNDS:  ".$blist->{'sounds'}."\n" if (!$CLOquiet);
print "       ARCHIVE: ".$blist->{'archive'}."\n\n" if (!$CLOquiet);

if ($Wall>0) {
	foreach my $key (sort keys %{$blist}) {
		$blist->{$key} = 1;
	}
}
if ($WOall>0) {
	foreach my $key (sort keys %{$blist}) {
		$blist->{$key} = 0;
	}
}
$blist->{'db'}     = 1 if ($Wdb>0);
$blist->{'db'}     = 0 if ($WOdb>0);
$blist->{'conf'}   = 1 if ($Wconf>0);
$blist->{'conf'}   = 0 if ($WOconf>0);
$blist->{'web'}    = 1 if ($Wweb>0);
$blist->{'web'}    = 0 if ($WOweb>0);
$blist->{'sounds'} = 1 if ($Wsounds>0);
$blist->{'sounds'} = 0 if ($WOsounds>0);
$blist->{'archive'} = 1 if ($Warchive>0);
$blist->{'archive'} = 0 if ($WOarchive>0);


### find tar binary to do the archiving
my $tarbin = '';
if (-e '/usr/bin/tar') {
	$tarbin = '/usr/bin/tar';
} else {
	if (-e '/usr/local/bin/tar') {
		$tarbin = '/usr/local/bin/tar';
	} else {
		if (-e '/bin/tar') {
			$tarbin = '/bin/tar';
		} else {
			print "Can't find tar binary! Exiting...\n";
			exit;
		}
	}
}

### find gzip binary to do the archiving
my $gzipbin = '';
if (-e '/usr/bin/gzip') {
	$gzipbin = '/usr/bin/gzip';
} else {
	if (-e '/usr/local/bin/gzip') {
		$gzipbin = '/usr/local/bin/gzip';
	} else {
		if (-e '/bin/gzip') {
			$gzipbin = '/bin/gzip';
		} else {
			print "Can't find gzip binary! Exiting...\n";
			exit;
		}
	}
}

### find mysqldump binary to do the database dump
my $mysqldumpbin = '';
if (-e '/usr/bin/mysqldump') {
	$mysqldumpbin = '/usr/bin/mysqldump';
} else {
	if (-e '/usr/local/mysql/bin/mysqldump') {
		$mysqldumpbin = '/usr/local/mysql/bin/mysqldump';
	} else {
		if (-e '/bin/mysqldump') {
			$mysqldumpbin = '/bin/mysqldump';
		} else {
			print "Can't find mysqldump binary! Exiting...\n";
			exit;
		}
	}
}

my $post='-';
$post.='a' if ($blist->{'archive'}>0);
$post.='b' if ($blist->{'conf'}>0);
$post.='c' if ($blist->{'conf'}>0);
$post.='d' if ($blist->{'db'}>0);
$post.='s' if ($blist->{'sounds'}>0);
$post.='w' if ($blist->{'web'}>0);
my $pre='osdial-';
my $conf='-CNF-';
my $bin='-BIN-';
my $web='-WEB-';
my $sql='-SQL-';
my $sounds='-SND-';
my $archive='-ARC-';
my $all='-';
my $tar='.tar';
my $gz='.gz';
my $tgz='.tgz';

`mkdir -p $osd->{'BACKUPpath'}/temp`;

if ($blist->{'db'}>0) {
	print "  -- Exporting Database\n" if (!$CLOquiet);
	### BACKUP THE MYSQL FILES ON THE DB SERVER ###
	`$mysqldumpbin --lock-tables --flush-logs --port='$osd->{'VARDB_port'}' --host='$osd->{'VARDB_server'}' --user='$osd->{'VARDB_user'}' --password='$osd->{'VARDB_pass'}' $osd->{'VARDB_database'} | $gzipbin > $osd->{'BACKUPpath'}/temp/$pre$osd->{'VARserver_ip'}$sql$dt.sql$gz`;
}

if ($blist->{'conf'}>0) {
	print "  -- Packaging OSDial and System configuration files\n" if (!$CLOquiet);
	my $filelist='';
	my @efileary=qw(./etc/osdial.conf ./etc/dahdi ./etc/asterisk ./etc/openvpn ./etc/php.ini ./etc/sysconfig/iptables ./etc/sysconfig/network ./etc/sysconfig/network-scripts/ifcfg* ./etc/sysconfig/networking ./etc/my.cnf' ./etc/mysqlaccess.conf ./etc/my.cnf.d ./etc/hosts ./etc/rc.d/rc.local ./etc/resolv.conf ./etc/fstab ./etc/profile ./etc/sysctl.conf ./etc/wanpipe/wanpipe1.conf ./etc/wanpipe/wanpipe2.conf ./etc/wanpipe/wanpipe3.conf ./etc/wanpipe/wanpipe4.conf ./etc/wanpipe/wanpipe5.conf ./etc/wanpipe/wanpipe6.conf ./etc/wanpipe/wanpipe7.conf ./etc/wanpipe/wanpipe8.conf ./etc/wanpipe/wanrouter.rc ./etc/zaptel ./etc/zaptel-bak ./etc/haproxy ./etc/httpd/conf.d ./etc/issue ./etc/ssl ./var/spool/cron);
	foreach my $file (sort @efileary) {
		$filelist.=$file.' ' if (-e "$file");
	}
	chop $filelist;
	### BACKUP THE ASTERISK CONF FILES ON THE SERVER ###
	`$tarbin cf $osd->{'BACKUPpath'}/temp/$pre$osd->{'VARserver_ip'}$conf$dt$tar $filelist`;
}

if ($blist->{'web'}>0) {
	print "  -- Packaging Web files\n" if (!$CLOquiet);
	my $filelist='';
	my @efileary=qw(./opt/osdial/html ./var/www/html);
	foreach my $file (sort @efileary) {
		$filelist.=$file.' ' if (-e "$file");
	}
	chop $filelist;
	### BACKUP THE WEB FILES ON THE SERVER ###
	`$tarbin cf $osd->{'BACKUPpath'}/temp/$pre$osd->{'VARserver_ip'}$web$dt$tar $filelist`;
}

if ($blist->{'conf'}>0) {
	print "  -- Packaging Script and Program files\n" if (!$CLOquiet);
	my $filelist='';
	my @efileary=qw(./var/lib/asterisk/agi-bin ./opt/osdial/bin);
	foreach my $file (sort @efileary) {
		$filelist.=$file.' ' if (-e "$file");
	}
	chop $filelist;
	### BACKUP THE OSDIAL AND AGI FILES ON THE SERVER ###
	`$tarbin cf $osd->{'BACKUPpath'}/temp/$pre$osd->{'VARserver_ip'}$bin$dt$tar $filelist`;
}

if ($blist->{'sounds'}>0) {
	print "  -- Packaging Sound files\n" if (!$CLOquiet);
	my $filelist='';
	my @efileary=qw(./var/lib/asterisk/sounds ./var/lib/asterisk/monitor ./var/lib/asterisk/moh ./var/lib/asterisk/meetme ./var/lib/asterisk/dictate ./var/spool/asterisk ./opt/osdial/media);
	foreach my $file (sort @efileary) {
		$filelist.=$file.' ' if (-e "$file");
	}
	chop $filelist;
	### BACKUP THE ASTERISK SOUNDS ON THE SERVER ###
	`$tarbin cf $osd->{'BACKUPpath'}/temp/$pre$osd->{'VARserver_ip'}$sounds$dt$tar $filelist`;
}

if ($blist->{'archive'}>0) {
	print "  -- Packaging Archived Recordings and Reports and Backups files\n" if (!$CLOquiet);
	my $filelist='';
	my @efileary=qw(./opt/osdial/recordings ./opt/osdial/reports ./opt/osdial/backups/recordings ./opt/osdial/backups/*.fio);
	foreach my $file (sort @efileary) {
		$filelist.=$file.' ' if (-e "$file");
	}
	chop $filelist;
	### BACKUP THE ASTERISK BACKUPS AND RECORDINGS ON THE SERVER ###
	`$tarbin cf $osd->{'BACKUPpath'}/temp/$pre$osd->{'VARserver_ip'}$archive$dt$tar $filelist`;
}

print "  -- Combining Package files\n" if (!$CLOquiet);
### PUT EVERYTHING TOGETHER TO BE COMPRESSED ###
`mv $osd->{'BACKUPpath'}/temp $osd->{'BACKUPpath'}/$pre$osd->{'VARserver_ip'}$all$dt$post`;
chdir($osd->{'BACKUPpath'});
`$tarbin czf $osd->{'BACKUPpath'}/$pre$osd->{'VARserver_ip'}$all$dt$post$tgz ./$pre$osd->{'VARserver_ip'}$all$dt$post`;

### REMOVE TEMP FILES ###
`rm -fR $osd->{'BACKUPpath'}/$pre$osd->{'VARserver_ip'}$all$dt$post`;


#### FTP to the Backup server and upload the final file
if ($CLOftp>0) {
	print "  -- Starting FTP process\n" if (!$CLOquiet);
	use Net::FTP;
	my $ftp = Net::FTP->new($osd->{'VARREPORT_host'}, Port=>$osd->{'VARREPORT_port'}, Debug=>"$FTPdebug");
	$ftp->login($osd->{'VARREPORT_user'},$osd->{'VARREPORT_pass'});
	$ftp->cwd($osd->{'VARREPORT_dir'});
	$ftp->binary();
	$ftp->put($osd->{'BACKUPpath'}.'/'.$pre.$osd->{'VARserver_ip'}.$all.$dt.$post.$tgz, $pre.$osd->{'VARserver_ip'}.$all.$dt.$post.$tgz);
	$ftp->quit;
}

print "\n  Success! Backup created as $osd->{'BACKUPpath'}/$pre$osd->{'VARserver_ip'}$all$dt$post$tgz\n" if (!$CLOquiet);


exit;
