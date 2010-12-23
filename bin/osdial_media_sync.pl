#!/usr/bin/perl

use strict;
use OSDial;
use Getopt::Long;
$|++;

my $DB=0;
my $HELP=0;

if (scalar @ARGV) {
	GetOptions(
		'help!' => \$HELP,
		'debug!' => \$DB
	);
	if ($HELP) {
		print "osdial_media_sync.pl: Allowed run time options:\n";
		print "  [--help] = This screen.\n";
		print "  [--debug] = debug\n";
		exit;
	}
}

my $osd = OSDial->new('DB'=>$DB);

my @addfiles;
my @savefiles;

push @addfiles, $osd->media_add_files('/var/lib/asterisk/sounds','8510.*');
push @addfiles, $osd->media_add_files('/var/lib/asterisk/sounds.ramfs','8510.*');
push @addfiles, $osd->media_add_files('/mnt/ramdisk/sounds','8510.*');
push @addfiles, $osd->media_add_files('/opt/osdial/html/ivr');
push @addfiles, $osd->media_add_files('/var/lib/asterisk/OSDprompts');
push @addfiles, $osd->media_add_files('/opt/osdial/media');

push @savefiles, $osd->media_save_files('/opt/osdial/media');

foreach my $file (@addfiles) {
	$osd->debug(1,'osdial_media_sync.pl','  Adding file:%s',$file);
}

foreach my $file (@savefiles) {
	$osd->debug(1,'osdial_media_sync.pl','  Saving file:%s',$file);
}
