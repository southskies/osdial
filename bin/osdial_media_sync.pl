#!/usr/bin/perl

use strict;
use OSDial;
use Getopt::Long;
use Cwd qw(abs_path);
$|++;

my $DB=0;
my $QUIET=0;
my $HELP=0;
my $add_file='';
my $add_dir='';


if (scalar @ARGV) {
	GetOptions(
		'help!' => \$HELP,
		'debug!' => \$DB,
		'quiet!' => \$QUIET,
		'file=s' => \$add_file,
		'dir=s' => \$add_dir,
	);
	if ($HELP) {
		print "osdial_media_sync.pl: Allowed run time options:\n";
		print "  [--help]          = This screen.\n";
		print "  [--debug]         = debug\n";
		print "  [--quiet]         = Silence Output\n";
		print "  [--file=xxxx]     = Load File into Media Manager\n";
		print "  [--dir=xxxx]      = Load Directory into Media Manager\n";
		exit;
	}
}

my $osd = OSDial->new('DB'=>$DB);

if ($add_file) {
	$add_file = abs_path($add_file);
	if (-e $add_file and ! -d $add_file) {
		print "\n  Add File: " . $osd->media_add_file($add_file) . "\n\n" unless ($QUIET);
	} else {
		print "\n  Add File Failed: File Not Found.\n\n" unless ($QUIET);
	}
} elsif ($add_dir) {
	$add_dir = abs_path($add_dir);
	if (-e $add_dir and -d $add_dir) {
		print "\n  Add Directory: " . $add_dir . "\n" unless ($QUIET);
		my @addfiles = $osd->media_add_files($add_dir);
		foreach my $file (@addfiles) {
			print "    Add File: " . $file . "\n" unless ($QUIET);
		}
		print "\n" unless ($QUIET);
	} else {
		print "\n  Add Directory Failed: File Not Found.\n\n" unless ($QUIET);
	}
} else {
	my @addfiles;
	push @addfiles, $osd->media_add_files('/var/lib/asterisk/sounds','8510.*');
	push @addfiles, $osd->media_add_files('/var/lib/asterisk/sounds.ramfs','8510.*');
	push @addfiles, $osd->media_add_files('/var/lib/asterisk/OSDprompts');
	push @addfiles, $osd->media_add_files('/var/lib/asterisk/sounds/osdial');
	push @addfiles, $osd->media_add_files('/mnt/ramdisk/sounds','8510.*');
	push @addfiles, $osd->media_add_files('/mnt/ramdisk/sounds/osdial');
	push @addfiles, $osd->media_add_files('/opt/osdial/html/ivr');
	push @addfiles, $osd->media_add_files('/opt/osdial/media');
	foreach my $file (@addfiles) {
		$osd->debug(1,'osdial_media_sync.pl','  Adding file:%s',$file);
	}
}

my @savefiles;
push @savefiles, $osd->media_save_files('/opt/osdial/media');
push @savefiles, $osd->media_save_files('/var/lib/asterisk/sounds/osdial') if (-d "/var/lib/asterisk/sounds");
push @savefiles, $osd->media_save_files('/mnt/ramdisk/sounds/osdial') if (-d "/mnt/ramdisk/sounds");
foreach my $file (@savefiles) {
	$osd->debug(1,'osdial_media_sync.pl','  Saving file:%s',$file);
}
