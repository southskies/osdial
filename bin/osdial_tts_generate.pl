#!/usr/bin/perl
#
# osdial_tts_generate - Generate TTS cache.
#
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
#
#     This file is part of OSDial.
#
#     OSDial is free software: you can redistribute it and/or modify
#     it under the terms of the GNU Affero General Public License as
#     published by the Free Software Foundation, either version 3 of
#     the License, or (at your option) any later version.
#
#     OSDial is distributed in the hope that it will be useful,
#     but WITHOUT ANY WARRANTY; without even the implied warranty of
#     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#     GNU Affero General Public License for more details.
#
#     You should have received a copy of the GNU Affero General Public
#     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
#

$|++;

use strict;
use OSDial;
use Time::HiRes qw(gettimeofday usleep sleep time);
use Getopt::Long;


my $prog = 'osdial_tts_generate.pl';
my($DB, $CLOhelp, $CLOtest, $CLOleadid, $CLOquiet, $CLOextension, $CLOmerge, $CLOid);
my $start_epoch = time();

# Read in command-line options.
if (scalar @ARGV) {
        GetOptions(
                'help!' => \$CLOhelp,
                'debug!' => \$DB,
                'test!' => \$CLOtest,
		'lead=i' => \$CLOleadid,
		'extension=i' => \$CLOextension,
		'id=i' => \$CLOid,
                'quiet!' => \$CLOquiet,
                'merge!' => \$CLOmerge
        );
        if ($DB) {
                print "----- DEBUGGING -----\n";
                print "----- Testing Mode -----\n" if ($CLOtest);
                print "VARS-\n";
                print "CLOhelp-      $CLOhelp\n";
                print "CLOquiet-     $CLOquiet\n";
                print "CLOtest-      $CLOtest\n";
                print "CLOleadid-    $CLOleadid\n";
                print "CLOextension- $CLOextension\n";
                print "CLOid-        $CLOid\n";
                print "CLOmerge-     $CLOmerge\n";
                print "\n";
        }
        if ($CLOhelp) {
                print "\n\n" . $prog;
                print "allowed run-time options:\n";
                print "  [--help]          = This screen\n";
                print "  [--debug]         = debug\n";
                print "  [-t|--test]       = test only\n";
                print "  [-q|--quiet]      = Quiet output\n";
		print "  [--merge]         = Merge into a single WAV.\n";
		print "  [--lead=XXX]      = Use data from given lead#\n";
		print "                      to fill merge fields.\n";
		print "  [--id=XXX]        = Just run for one TTS ID.\n";
		print "  [--extension=XXX] = Just run for one TTS extension.\n";
		print "                      (defaults to all)\n\n";
                exit 0;
        }
}

my $osdial = OSDial->new(DB=>$DB);
$osdial->debug(0,$prog,"START- (\%s).",$start_epoch) unless ($CLOquiet);

my $leaddata = {};
if ($CLOleadid>0) {
	$leaddata = $osdial->sql_query(sprintf("SELECT * FROM osdial_list WHERE lead_id='\%s';",$osdial->mres($CLOleadid)));

	my $affcnt=0;
	while (my $aff = $osdial->sql_query(sprintf("SELECT concat(osdial_campaign_forms.name,'_',osdial_campaign_fields.name) AS affid,value FROM osdial_list_fields JOIN osdial_campaign_fields ON (osdial_list_fields.field_id=osdial_campaign_fields.id) JOIN osdial_campaign_forms ON (osdial_campaign_fields.form_id=osdial_campaign_forms.id) WHERE lead_id='\%s';",$osdial->mres($CLOleadid)))) {
		$leaddata->{$aff->{affid}} = $aff->{value};
		$affcnt++;
	}
}
if (!defined($leaddata->{lead_id})) {
	$leaddata->{vendor_lead_code} = 'LEADCODE';
	$leaddata->{source_id} = 'RAW';
	$leaddata->{list_id} = $osdial->get_datetime();
	$leaddata->{list_id} =~ s/[\-\ :]//g;
	$leaddata->{gmt_offset_now} = '-5.0';
	$leaddata->{called_since_last_reset} = "N";
	$leaddata->{phone_code} = '1';
	$leaddata->{phone_number} = '3215551212';
	$leaddata->{title} = 'MR';
	$leaddata->{first_name} = 'JOHN';
	$leaddata->{middle_initial} = 'Q';
	$leaddata->{last_name} = 'SMITH';
	$leaddata->{address1} = '1234 MAIN STREET';
	$leaddata->{address2} = 'ADDRESS 2';
	$leaddata->{address3} = 'ADDRESS 3';
	$leaddata->{city} = 'ORLANDO';
	$leaddata->{state} = 'FL';
	$leaddata->{province} = 'PROVINCE';
	$leaddata->{postal_code} = '12345';
	$leaddata->{country_code} = 'USA';
	$leaddata->{gender} = 'M';
	$leaddata->{date_of_birth} = '1970-01-01';
	$leaddata->{alt_phone} = '3215551212';
	$leaddata->{email} = 'test@google.com';
	$leaddata->{custom1} = 'CUSTOM 1';
	$leaddata->{custom2} = 'CUSTOM 2';
	$leaddata->{comments} = 'COMMENTS';
	$leaddata->{lead_id} = '1234';
	$leaddata->{campaign} = 'TEST';
	$leaddata->{phone_login} = '1234';
	$leaddata->{group} = 'TEST';
	$leaddata->{channel_group} = 'TEST';
	$leaddata->{SQLdate} = $osdial->get_datetime();
	$leaddata->{epoch} = $start_epoch;
	$leaddata->{uniqueid} = '1163095830.4136';
	$leaddata->{customer_zap_channel} = 'DAHDI/1-1';
	$leaddata->{server_ip} = $osdial->{VARserver_ip};
	$leaddata->{SIPexten} = 'SIP/gs102';
	$leaddata->{session_id} = '8600051';
}


my $fcnt=0;
my $stmt = sprintf("SELECT * FROM osdial_tts;");
$stmt = sprintf("SELECT * FROM osdial_tts WHERE extension='\%s';",$osdial->mres($CLOextension)) if ($CLOextension>0);
$stmt = sprintf("SELECT * FROM osdial_tts WHERE id='\%s';",$osdial->mres($CLOid)) if ($CLOid>0);
while (my $tts = $osdial->sql_query($stmt)) {
	if (defined($tts->{phrase})) {
		my @tts_files;
		my @tts_merge;
		my $start_tts = time();
		foreach my $phrase ($tts->{phrase}) {
			my @tparse = $osdial->tts_osdial_parse($phrase,$leaddata);
			foreach my $tp (@tparse) {
				push @tts_files, $osdial->tts_generate($tp,$tts->{voice}) unless ($CLOtest);
			}
		}
		my $end_tts = time();
		$osdial->debug(0,$prog,"TTS-\%s processing time (\%s).",$tts->{id},($end_tts-$start_tts)) unless ($CLOquiet);
		foreach my $tts_file (@tts_files) {
			$osdial->debug(0,$prog,"TTS-\%s files (\%s).",$tts->{id},$tts_file) unless ($CLOquiet);
			if ($CLOmerge) {
				if ($tts_file =~ /^tts/) {
					if (-e "/opt/osdial/".$tts_file.".wav") {
						$osdial->debug(1,$prog,"Merge: Found tts file in /opt/osdial/tts.") unless ($CLOquiet);
						push @tts_merge, "/opt/osdial/".$tts_file.".wav";
					} elsif (-e "/var/lib/asterisk/sounds/".$tts_file.".wav") {
						$osdial->debug(1,$prog,"Merge: Found tts file in /var/lib/asterisk/sounds.") unless ($CLOquiet);
						push @tts_merge, "/var/lib/asterisk/sounds/".$tts_file.".wav";
					}
				} elsif (-e "/var/lib/asterisk/sounds/".$tts_file.".wav") {
					$osdial->debug(1,$prog,"Merge: Found Asterisk file, already in wav format.") unless ($CLOquiet);
					push @tts_merge, "/var/lib/asterisk/sounds/".$tts_file.".wav";
				} elsif (-e "/var/lib/asterisk/sounds/".$tts_file.".gsm") {
					$osdial->debug(1,$prog,"Merge: Found Asterisk file, converting...gsm->wav.") unless ($CLOquiet);
					my $retval = system("/usr/bin/sox -t gsm -r 8k /var/lib/asterisk/sounds/".$tts_file.".gsm -r 8k -b 16 /tmp/osdial_tts_generate.$$.$fcnt.wav");
					push @tts_merge, "/tmp/osdial_tts_generate.$$.$fcnt.wav";
				} elsif (-e "/var/lib/asterisk/sounds/".$tts_file.".ulaw") {
					$osdial->debug(1,$prog,"Merge: Found Asterisk file, converting...ulaw->wav.") unless ($CLOquiet);
					my $retval = system("/usr/bin/sox -t ul -r 8k /var/lib/asterisk/sounds/".$tts_file.".ulaw -r 8k -b 16 /tmp/osdial_tts_generate.$$.$fcnt.wav");
					push @tts_merge, "/tmp/osdial_tts_generate.$$.$fcnt.wav";
				}
			}
			$fcnt++;
		}
		if ($CLOmerge) {
			#my $retval = system(("/usr/bin/sox","--combine","concatenate",@tts_merge,"/tmp/osdial_tts_merge-".$tts->{id}.".wav"));
			my $retval = system("/usr/bin/sox --combine concatenate " . join(' ',@tts_merge) . " /tmp/osdial_tts_merge-$$-".$tts->{id}.".wav");
			$osdial->debug(0,$prog,"Merge: Completed (\%s).","/tmp/osdial_tts_merge-$$-".$tts->{id}.".wav") unless ($CLOquiet);
			print "/tmp/osdial_tts_merge-$$-".$tts->{id}.".wav\n";
			foreach my $tts_file (@tts_merge) {
				$osdial->debug(0,$prog,"MERGE-\%s files (\%s).",$tts->{id},$tts_file) unless ($CLOquiet);
				#unlink($tts_file);
			}
		}
	}
}

my $end_epoch = time();
$osdial->debug(0,$prog,"END- (\%s).",($end_epoch-$start_epoch)) unless ($CLOquiet);
exit 0;
