#!/usr/bin/perl
#
#  osdial_astgen.pl: Script used to auto generate the asterisk config files
#                    based on the configuration from the management interface
#                    afterwhich the program compares the new and original
#                    files and if needed, reloads that asterisk component.
#
#  Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
#
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
#
# 090406-1200 - Initial build.
# 090409-1357 - Separate the write and reloads into a single function.
# 090409-1813 - Created routine to generate a password for intra-server comm.
# 090409-1925 - Remove lo interface if more than 1 active server
# 090409-2035 - Added ASTloop/ASTblind IAX loopback servers
# 090409-2104 - Added gen_registrations
# 090519-1932 - Fixed server2server configuration


use strict;
use OSDial;
use Getopt::Long;
use IO::Interface::Simple;
$|++;

# Identify myself.
my $prog = 'osdial_astgen.pl';

# Declare command-line options.
my($DB, $CLOhelp, $CLOtest, $CLOshowip, $CLOquiet, $CLOg729);
my(%reloads);

my $osdial = OSDial->new('DB'=>$DB);

# Auto-creation header.
my $achead = ";\n; WARNING: AUTO-CREATED FILE.\n; Any changes you make will be overwritten!\n;\n";

# Read in command-line options.
if (scalar @ARGV) {
        GetOptions(
                'help!' => \$CLOhelp,
                'debug!' => \$DB,
                'test!' => \$CLOtest,
                'quiet!' => \$CLOquiet,
                'showip!' => \$CLOshowip,
                'g729!' => \$CLOg729
        );
        if ($DB) {
                print "----- DEBUGGING -----\n";
                print "----- Testing Mode -----\n" if ($CLOtest);
                print "VARS-\n";
                print "CLOhelp-     $CLOhelp\n";
                print "CLOshowip-   $CLOshowip\n";
                print "CLOquiet-    $CLOquiet\n";
                print "CLOtest-     $CLOtest\n";
                print "CLOg729-     $CLOg729\n";
                print "\n";
        }
        if ($CLOhelp) {
                print "\n\n" . $prog;
                print "allowed run-time options:\n";
                print "  [--help]         = This screen\n";
                print "  [--debug]        = debug\n";
                print "  [-t|--test]      = test only\n";
                print "  [-q|--quiet]     = Quiet output\n";
                print "  [--g729]         = Use g729 codecs\n";
                print "  [-s|--showip]    = Show Interface IPs\n\n";
                exit 0;
        }
}


my $codec = "ulaw";
$codec = "g729" if ($CLOg729);


# Get all of the IPs on this machines interfaces.
my $interfaces = get_myips();
my @myips = (values %{$interfaces});
if ($CLOshowip) {
	print "\n  Found these interfaces / IPs\n";
	foreach my $key ( keys %{$interfaces} ) {
		print "    " . $key . " - " . $interfaces->{$key} . "\n";
	}
	print "\n";
	exit 0;
}


my $asterisk_version;
if (-e "/usr/sbin/asterisk" and -f "/etc/asterisk/osdial_extensions.conf") {
	#Check and set Asterisk version.
	$asterisk_version = `/usr/sbin/asterisk -V`;
	chomp $asterisk_version;
	$asterisk_version =~ s/Asterisk //;
	if ($asterisk_version ne "") {
		my $stmt = sprintf("UPDATE servers SET asterisk_version='\%s' WHERE server_ip='\%s';",$asterisk_version,$osdial->{VARserver_ip});
		$osdial->sql_execute($stmt) if (!$CLOtest);
		print STDERR "\n|$stmt|\n" if ($DB);
	}

	#Fix some version related config differences
	my($oedata);
	open(OE, "/etc/asterisk/osdial_extensions.conf");
	while (my $oeline = <OE>) {
		$oedata .= $oeline;
	}
	close(OE);
	my($moddata);
	open(MOD, "/etc/asterisk/modules.conf");
	while (my $modline = <MOD>) {
		$moddata .= $modline;
	}
	close(MOD);
	my $oereload;
	if ($asterisk_version =~ /^1\.6/) {
		$oereload = "dialplan reload";
		$oedata =~ s/^exten => h,1,DeadAGI/exten => h,1,AGI/gm;
		$moddata =~ s/^noload => chan_agent.so/load => chan_agent.so/gm;
		$moddata =~ s/^noload => app_queue.so/load => app_queue.so/gm;
		if (-e "/etc/asterisk/zapata.conf" and not -e "/etc/asterisk/chan_dahdi.conf") {
			my $pr = `cp /etc/asterisk/zapata.conf /etc/asterisk/chan_dahdi.conf`;
		}
	} elsif ($asterisk_version =~ /^1\.2/) {
		$oereload = "extensions reload";
		$oedata =~ s/^exten => h,1,AGI/exten => h,1,DeadAGI/gm; 
		$moddata =~ s/^load => chan_agent.so/noload => chan_agent.so/gm;
		$moddata =~ s/^load => app_queue.so/noload => app_queue.so/gm;
		if (-e "/etc/asterisk/chan_dahdi.conf" and not -e "/etc/asterisk/zapata.conf") {
			my $pr = `cp /etc/asterisk/chan_dahdi.conf /etc/asterisk/zapata.conf`;
		}
	}
	write_reload($oedata,'osdial_extensions',$oereload);
	write_reload($moddata,'modules','reload');

	# TODO: Fix calc_password to not be so aggressive.
	#my $pass = calc_password();
	my $pass = '6l5a4i3d2s1o0o1s2d3i4a5l6';

	# Generate intra-server extensions and iax communication.
	# (osdial_extensions_servers.conf osdial_iax_servers.conf)
	my($ssreg,$isreg) = gen_servers($pass);

	# Generate carrier configurations
	my($screg,$icreg) = gen_carriers();

	# Generate SIP/IAX registrations
	# (osdial_iax_registrations.conf osdial_sip_registrations.conf)
	gen_registrations($ssreg.$screg, $isreg.$icreg);

	# Generate meetme conferences and extensions.
	# (osdial_extensions_conferences.conf osdial_meetme.conf)
	gen_conferences();

	# Generate agent extensions, and sip/iax agent phones.
	# (osdial_extensions_phones.conf osdial_sip_phones.conf osdial_iax_phones.conf)
	gen_phones();


	foreach my $reload (keys %reloads) {
		print "    Executing " . $reload . "...\n" unless ($CLOquiet);
		`/usr/sbin/asterisk -rx "$reload" > /dev/null 2>&1`;
		sleep 5;
	}
}


# Exit normally
exit 0;



###############################################################
# calculate a unique password that will remain constant across servers.
#   (total of user ids + total of phone extensions) * number of users
sub calc_password {
	my $stmt = "SELECT ((sum(user) + sum(extension)) * count(user)) AS calc FROM osdial_users,phones;";
	print $stmt . "\n" if ($DB);
	my $sret = $osdial->sql_query($stmt);
	my $calc = $sret->{calc};
	my $pass;
	my @pad = split(//,'osdialosdialosdialosdialosdial');
	foreach my $c (split(//, $calc)) {
		$pass .= $c . shift(@pad);
	}
	return $pass;
}


# Generate SIP/IAX registrations
# (osdial_iax_registrations.conf osdial_sip_registrations.conf)
sub gen_registrations {
	my ($sip_registrations,$iax_registrations) = @_;

	my $sreg = $achead . $sip_registrations;
	my $ireg = $achead . $iax_registrations;

	write_reload($sreg,'osdial_sip_registrations','sip reload');
	write_reload($ireg,'osdial_iax_registrations','iax2 reload');
}


# Generate intra-server extensions and iax communication.
# (osdial_extensions_servers.conf osdial_iax_servers.conf)
sub gen_servers {
	my ($pass) = @_;

	my $esvr = $achead;
	my $isvr = $achead;
	my $sreg='';
	my $ireg='';

	$isvr .= ";\n; IAX loopback for testing\n";
	$isvr .= "[ASTloop]\n";
	$isvr .= "type=friend\n";
	$isvr .= "accountcode=ASTloop\n";
	$isvr .= "context=osdial\n";
	$isvr .= "auth=md5\n";
	$isvr .= "host=dynamic\n";
	$isvr .= "permit=0.0.0.0/0.0.0.0\n";
	$isvr .= "secret=$pass\n";
	$isvr .= "disallow=all\n";
	$isvr .= "allow=$codec\n";
	$isvr .= "requirecalltoken=no\n";
	$isvr .= "qualify=no\n";

	$isvr .= ";\n; IAX loopback for blind monitoring\n";
	$isvr .= "[ASTblind]\n";
	$isvr .= "type=friend\n";
	$isvr .= "accountcode=ASTblind\n";
	$isvr .= "context=osdial\n";
	$isvr .= "auth=md5\n";
	$isvr .= "host=dynamic\n";
	$isvr .= "permit=0.0.0.0/0.0.0.0\n";
	$isvr .= "secret=$pass\n";
	$isvr .= "disallow=all\n";
	$isvr .= "allow=$codec\n";
	$isvr .= "requirecalltoken=no\n";
	$isvr .= "qualify=no\n";

	$esvr .= "; Local blind monitoring\n";
	$esvr .= "exten => _08600XXX,1,Dial(\${TRUNKblind}/6\${EXTEN:1},55,To)\n";

	$ireg .= "register => ASTloop:$pass\@127.0.0.1:40569\n";
	$ireg .= "register => ASTblind:$pass\@127.0.0.1:41569\n";


	# Get my server
	my $stmt = "SELECT server_id,server_ip FROM servers WHERE";
	foreach my $ip (@myips) {
		$stmt .= " server_ip=\'" . $ip . "\' OR";
	}
	chop $stmt; chop $stmt; chop $stmt;
	$stmt .= ';';
	print $stmt . "\n" if ($DB);
	my $iname;
	while (my $sret = $osdial->sql_query($stmt)) {
		my @sip = split /\./, $sret->{server_ip};
		my $fsip = sprintf('%.3d*%.3d*%.3d*%.3d',@sip);
		my $fsip2 = sprintf('%.3d%.3d%.3d%.3d',@sip);
		$iname = $sret->{server_id};
		$esvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$esvr .= "exten => _" . $fsip . "*.,1,Goto(osdial,\${EXTEN:16},1)\n";
		$esvr .= "exten => _" . $fsip2 . ".,1,Goto(osdial,\${EXTEN:12},1)\n";
		$isvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$isvr .= "[" . $sret->{server_id} . "]\n";
		$isvr .= "type=user\n";
		$isvr .= "trunk=yes\n";
		$isvr .= "tos=0x18\n";
		$isvr .= "auth=md5\n";
		$isvr .= "secret=$pass\n";
		$isvr .= "disallow=all\n";
		$isvr .= "allow=$codec\n";
		$isvr .= "context=osdial\n";
		$isvr .= "requirecalltoken=no\n";
		$isvr .= "nat=no\n";

	}

	# Get other servers 
	my $stmt = "SELECT server_id,server_ip FROM servers WHERE";
	foreach my $ip (@myips) {
		$stmt .= " server_ip!=\'" . $ip . "\' AND";
	}
	chop $stmt; chop $stmt; chop $stmt; chop $stmt;
	$stmt .= ';';
	print $stmt . "\n" if ($DB);
	while (my $sret = $osdial->sql_query($stmt)) {
		my @sip = split /\./, $sret->{server_ip};
		my $fsip = sprintf('%.3d*%.3d*%.3d*%.3d',@sip);
		$esvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$esvr .= "exten => _" . $fsip . "*.,1,Dial(IAX2/" . $sret->{server_id} . ':' . $pass . '@' . $sret->{server_ip} . "/\${EXTEN},,o)\n";
		$isvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$isvr .= "[" . $sret->{server_id} . "]\n";
		$isvr .= "type=peer\n";
		$isvr .= "username=" . $sret->{server_id} . "\n";
		$isvr .= "host=" . $sret->{server_ip} . "\n";
		$isvr .= "trunk=yes\n";
		$isvr .= "tos=0x18\n";
		$isvr .= "qualify=5000\n";
		$isvr .= "auth=md5\n";
		$isvr .= "secret=$pass\n";
		$isvr .= "disallow=all\n";
		$isvr .= "allow=$codec\n";
		$isvr .= "peercontext=osdial\n";
		$isvr .= "requirecalltoken=no\n";
		$isvr .= "nat=no\n";
	}

	my $extreload = "extensions reload";
	if ($asterisk_version =~ /^1\.6/) {
		$extreload = "dialplan reload";
	}
	write_reload($esvr,'osdial_extensions_servers',$extreload);
	write_reload($isvr,'osdial_iax_servers','iax2 reload');
	
	return ($sreg, $ireg);
}


# Generate meetme conferences and extensions.
# (osdial_extensions_conferences.conf osdial_meetme.conf)
sub gen_conferences {
	my $cnf = $achead;
	my $mtm = $achead;
	$mtm .= "conf => 8600\nconf => 8601,1234\n";
	$cnf .= ";\n;Volume adjustments\n";
	$cnf .= "exten => _X4860XXXX,1,MeetMeAdmin(\${EXTEN:2},T,\${EXTEN:0:1})\n";
	$cnf .= "exten => _X4860XXXX,2,Hangup\n;\n";
	$cnf .= "exten => _X3860XXXX,1,MeetMeAdmin(\${EXTEN:2},t,\${EXTEN:0:1})\n";
	$cnf .= "exten => _X3860XXXX,2,Hangup\n;\n";
	$cnf .= "exten => _X2860XXXX,1,MeetMeAdmin(\${EXTEN:2},m,\${EXTEN:0:1})\n";
	$cnf .= "exten => _X2860XXXX,2,Hangup\n;\n";
	$cnf .= "exten => _X1860XXXX,1,MeetMeAdmin(\${EXTEN:2},M,\${EXTEN:0:1})\n";
	$cnf .= "exten => _X1860XXXX,2,Hangup\n;\n";
	$cnf .= "exten => _5555860XXXX,1,MeetMeAdmin(\${EXTEN:4},K)\n";
	$cnf .= "exten => _5555860XXXX,2,Hangup\n";

	my $stmt = "SELECT conf_exten,server_ip FROM conferences where";
	foreach my $ip (@myips) {
		$stmt .= " server_ip=\'" . $ip . "\' OR";
	}

	if ($asterisk_version =~ /^1\.6/) {
		$cnf .= ";\n; DAHDIBarge direct channel extensions\n";
		$cnf .= "exten => _8612XXX,1,DAHDIBarge(\${EXTEN:4})\n";
		$cnf .= "exten => _8612XXX,2,Hangup\n";
	} else {
		$cnf .= ";\n; ZapBarge direct channel extensions\n";
		$cnf .= "exten => _8612XXX,1,ZapBarge(\${EXTEN:4})\n";
		$cnf .= "exten => _8612XXX,2,Hangup\n";
	}

	chop $stmt; chop $stmt; chop $stmt;
	print $stmt . "\n" if ($DB);
	$stmt .= ';';
	my ($cnf2,$mtm2,$cf,$cl);
	while (my $sret = $osdial->sql_query($stmt)) {
		$cf = $sret->{conf_exten} unless ($cf);
		$cl = $sret->{conf_exten};
		if ($asterisk_version =~ /^1\.6/) {
			$cnf2 .= "exten => _" . $sret->{conf_exten} . ",1,Meetme(\${EXTEN},q)\n";
		} else {
			$cnf2 .= "exten => _" . $sret->{conf_exten} . ",1,Meetme,\${EXTEN}|q\n";
		}
		$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
	}

	$cnf .= ";\n; OSDial Conferences $cf - $cl\n";
	$cnf .= $cnf2;
	$mtm .= ";\n; OSDial Conferences $cf - $cl\n";
	$mtm .= $mtm2;

	my $stmt = "SELECT conf_exten,server_ip FROM osdial_conferences where";
        foreach my $ip (@myips) {
                $stmt .= " server_ip=\'" . $ip . "\' OR";
        }
        chop $stmt; chop $stmt; chop $stmt;
        $stmt .= ";";
        print $stmt . "\n" if ($DB);
        my ($cnf2,$mtm2,$cf,$cl);
	while (my $sret = $osdial->sql_query($stmt)) {
                $cf = $sret->{conf_exten} unless ($cf);
                $cl = $sret->{conf_exten};
		if ($asterisk_version =~ /^1\.6/) {
			$cnf2 .= "exten => _" . $sret->{conf_exten} . ",1,Meetme(\${EXTEN},F)\n";
			$cnf2 .= "exten => _" . $sret->{conf_exten} . ",2,Hangup\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",1,Meetme(\${EXTEN:1},Fq)\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",2,Hangup\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",1,Meetme(\${EXTEN:1},Flq)\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",2,Hangup\n";
		} else {
			$cnf2 .= "exten => _" . $sret->{conf_exten} . ",1,Meetme,\${EXTEN}|F\n";
			$cnf2 .= "exten => _" . $sret->{conf_exten} . ",2,Hangup\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",1,Meetme,\${EXTEN:1}|Fq\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",2,Hangup\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",1,Meetme,\${EXTEN:1}|Fmq\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",2,Hangup\n";
		}
		$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
        }
	$cnf .= ";\n; OSDIAL Agent Conferences $cf - $cl\n";
	$cnf .= "; quiet entry and leaving conferences for OSDIAL $cf - $cl\n";
	$cnf .= "; quiet monitor extensions for meetme rooms (for room managers)  $cf - $cl\n";
	$cnf .= $cnf2;
	$mtm .= ";\n; OSDIAL Agent Conferences $cf - $cl\n";
	$mtm .= $mtm2;

	my $stmt = "SELECT conf_exten FROM osdial_remote_agents WHERE user_start LIKE 'va\%';";
        my ($cnf2,$mtm2,$cf,$cl);
	while (my $sret = $osdial->sql_query($stmt)) {
		if ($asterisk_version =~ /^1\.6/) {
			$cnf2 .= "exten => _" . $sret->{conf_exten} .  ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _" . $sret->{conf_exten} .  ",2,Meetme(\${EXTEN},Fq)\n";
			$cnf2 .= "exten => _" . $sret->{conf_exten} .  ",3,Hangup\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",2,Meetme(\${EXTEN:1},Flq)\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",3,Hangup\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",2,Meetme(\${EXTEN:1},Fq)\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",3,Hangup\n";
			$cnf2 .= "exten => _8" . $sret->{conf_exten} . ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _8" . $sret->{conf_exten} . ",2,Meetme(\${EXTEN:1},Fq)\n";
			$cnf2 .= "exten => _8" . $sret->{conf_exten} . ",3,Hangup\n";
		} else {
			$cnf2 .= "exten => _" . $sret->{conf_exten} .  ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _" . $sret->{conf_exten} .  ",2,Meetme,\${EXTEN}|Fq\n";
			$cnf2 .= "exten => _" . $sret->{conf_exten} .  ",3,Hangup\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",2,Meetme,\${EXTEN:1}|Fmq\n";
			$cnf2 .= "exten => _6" . $sret->{conf_exten} . ",3,Hangup\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",2,Meetme,\${EXTEN:1}|Fq\n";
			$cnf2 .= "exten => _7" . $sret->{conf_exten} . ",3,Hangup\n";
			$cnf2 .= "exten => _8" . $sret->{conf_exten} . ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _8" . $sret->{conf_exten} . ",2,Meetme,\${EXTEN:1}|Fq\n";
			$cnf2 .= "exten => _8" . $sret->{conf_exten} . ",3,Hangup\n";
		}
		$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
	}
	$cnf2 .= "exten => 487487,1,Playback(sip-silence)\n";
	$cnf2 .= "exten => 487487,n,AGI(agi-OSDivr.agi,\${EXTEN})\n";
	$cnf2 .= "exten => 487487,n,Hangup\n";
	$cnf .= ";\n; OSDIAL Virtual Agent Conferences\n";
	$cnf .= $cnf2;
	$mtm .= ";\n; OSDIAL Virtual Agent Conferences\n";
	$mtm .= $mtm2;

	my $extreload = "extensions reload";
	my $mmreload = "reload app_meetme.so";
	if ($asterisk_version =~ /^1\.6/) {
		$extreload = "dialplan reload";
		$mmreload = "config reload /etc/asterisk/meetme.conf";
	}
	write_reload($cnf,'osdial_extensions_conferences',$extreload);
	write_reload($mtm,'osdial_meetme',$mmreload);
}

# Generate agent extensions, and sip/iax agent phones.
# (osdial_extensions_phones.conf osdial_sip_phones.conf osdial_iax_phones.conf)
sub gen_phones {
	my $sphn = $achead;
	my $iphn = $achead;
	my $ephn = $achead;

	my $stmt = "SELECT * FROM phones WHERE protocol IN ('SIP','IAX2','Zap','DAHDI','EXTERNAL') AND active='Y' AND (";
	foreach my $ip (@myips) {
		$stmt .= " server_ip=\'" . $ip . "\' OR";
	}
	chop $stmt; chop $stmt; chop $stmt;
	$stmt .= ');';
	print $stmt . "\n" if ($DB);
	while (my $sret = $osdial->sql_query($stmt)) {
		$sret->{voicemail_id} = '9999' if ($sret->{voicemail_id} eq "");
		$sret->{ext_context} = 'osdial' if ($sret->{ext_context} eq "");
		if ($sret->{protocol} eq "SIP" and $sret->{extension} !~ /\@/) {
			$sphn .= ";\n[". $sret->{extension} ."]\n";
			$sphn .= "type=friend\n";
			$sphn .= "username=" . $sret->{extension} . "\n";
			$sphn .= "secret=" . $sret->{pass} . "\n";
			if ($sret->{phone_ip}) {
				$sphn .= "host=" . $sret->{phone_ip} . "\n";
			} else {
				$sphn .= "host=dynamic\n";
			}
			$sphn .= "dtmfmode=auto\n";
			$sphn .= "relaxdtmf=yes\n";
			$sphn .= "disallow=all\n";
			$sphn .= "allow=$codec\n";
			#if ($sret->{phone_type} =~ /Grandstream/i) {
			#	$sphn .= "dtmfmode=rfc2833\n";
			#	$sphn .= "relaxdtmf=yes\n";
			#	$sphn .= "disallow=all\n";
			#	$sphn .= "allow=$codec\n";
			#} else {
			#	$sphn .= "dtmfmode=inband\n";
			#	$sphn .= "disallow=all\n";
			#	$sphn .= "allow=$codec\n";
			#}
			$sphn .= "qualify=5000\n";
			$sphn .= "nat=yes\n";
			$sphn .= "context=" . $sret->{ext_context} . "\n";
			$sphn .= "mailbox=" . $sret->{voicemail_id} . "\@default\n";
		} elsif ($sret->{protocol} eq "IAX2" and $sret->{extension} !~ /\@|\//) {
			$iphn .= ";\n[". $sret->{extension} ."]\n";
			$iphn .= "type=friend\n";
			$iphn .= "username=" . $sret->{extension} . "\n";
			$iphn .= "secret=" . $sret->{pass} . "\n";
			if ($sret->{phone_ip}) {
				$iphn .= "host=" . $sret->{phone_ip} . "\n";
			} else {
				$iphn .= "host=dynamic\n";
			}
			$iphn .= "disallow=all\n";
			$iphn .= "allow=$codec\n";
			$iphn .= "qualify=5000\n";
			$iphn .= "requirecalltoken=no\n";
			$iphn .= "nat=yes\n";
			$iphn .= "context=" . $sret->{ext_context} . "\n";
			$iphn .= "mailbox=" . $sret->{voicemail_id} . "\@default\n";
		}
		my $dext = $sret->{protocol} . "/" . $sret->{extension};
		if ($sret->{protocol} =~ /SIP|IAX2/ and $sret->{extension} =~ /\@/) {
			my($sext,$ssrv) = split /\@/,$sret->{extension};
			$dext = $sret->{protocol} . "/" . $ssrv . "/" . $sext;
		} elsif ($sret->{protocol} =~ /DAHDI|Zap/) {
			$dext = $sret->{protocol} . "/" . $sret->{extension};
		} elsif ($sret->{protocol} =~ /EXTERNAL/ and $sret->{phone_type} =~ /DAHDI|Zap/i) {
			$dext = "";
			my $proto = "";
			$proto = "Zap" if ($sret->{phone_type} =~ /Zap/i);
			$proto = "DAHDI" if ($sret->{phone_type} =~ /DAHDI/i);
			if ($sret->{phone_ip} >= 1 and $sret->{phone_ip} <= 999) {
				$dext = $proto . "/" . $sret->{phone_ip};
			} elsif (length($sret->{extension}) == 3 or length($sret->{extension}) == 4) {
				$dext = $proto . "/" . substr($sret->{extension},1);
			} elsif (length($sret->{extension}) == 5) {
				$dext = $proto . "/" . substr($sret->{extension},2);
			}
		} elsif ($sret->{protocol} =~ /EXTERNAL/) {
			$dext = "";
		}

		if ($dext ne "") {
			$ephn .= "exten => _" . $sret->{dialplan_number} . ",1,Dial(" . $dext . ",55,o)\n";
			$ephn .= "exten => _" . $sret->{dialplan_number} . ",n,Voicemail(" . $sret->{voicemail_id} . ")\n" if ($sret->{voicemail_id} ne "");
			$ephn .= "exten => _" . $sret->{dialplan_number} . ",n,Hangup()\n\n";
		}
	}

	my $extreload = "extensions reload";
	if ($asterisk_version =~ /^1\.6/) {
		$extreload = "dialplan reload";
	}
	write_reload($sphn,'osdial_sip_phones','sip reload');
	write_reload($iphn,'osdial_iax_phones','iax2 reload');
	write_reload($ephn,'osdial_extensions_phones',$extreload);
}


sub gen_carriers {
	my $sip_config=$achead;
	my $iax_config=$achead;
	my $sip_registrations='';
	my $iax_registrations='';
	my $dialplan=$achead;

	my $carriers = {};
	my $stmt = "SELECT * FROM osdial_carriers WHERE active='Y';";
	while (my $carrier = $osdial->sql_query($stmt)) {
		$carriers->{$carrier->{id}} = $carrier;
	}
	foreach my $carrier (sort keys %{$carriers}) {
		# Override server with server specific options if set.
		my $stmt = sprintf("SELECT * FROM osdial_carrier_servers WHERE carrier_id='\%s' AND server_ip='\%s';",$carrier,$osdial->{VARserver_ip});
		while (my $carrier_server = $osdial->sql_query($stmt)) {
			$carriers->{$carrier}{protocol_config} = $carrier_server->{protocol_config} if ($carrier_server->{protocol_config} ne '');
			$carriers->{$carrier}{registrations} =   $carrier_server->{registrations}   if ($carrier_server->{registrations} ne '');
			$carriers->{$carrier}{dialplan} =        $carrier_server->{dialplan}        if ($carrier_server->{dialplan} ne '');
		}

		my $context = 'OIN' . $carriers->{$carrier}{name};

		# Add contexts to protocol_config.
		$carriers->{$carrier}{protocol_config} =~ s/\]\$/]\ncontext=$context/mg;

		# Do the variable substitutions.
		$carriers->{$carrier}{dialplan} =~ s/<NAME>/$carriers->{$carrier}{name}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<PROTOCOL>/$carriers->{$carrier}{protocol}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<STRIP_MSD>/$carriers->{$carrier}{strip_msd}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<ALLOW_INTERNATIONAL>/$carriers->{$carrier}{allow_international}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<DEFAULT_CALLERID>/$carriers->{$carrier}{default_callerid}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<DEFAULT_AREACODE>/$carriers->{$carrier}{default_areacode}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<DEFAULT_PREFIX>/$carriers->{$carrier}{default_prefix}/mg;

		my $failover = '';
		if (!$carriers->{$carrier}{failover_id}>0) {
			$failover .= "exten => _failover.,1,Hangup()\n\n";
		} else {
			my $stmt = sprintf("SELECT * FROM osdial_carriers WHERE id='\%s';",$carriers->{$carrier}{failover_id});
			my $failover_carrier = $osdial->sql_query($stmt);
			if ($carriers->{$carrier}{failover_confition} eq 'CHANUNAVAIL') {
				$failover .= "exten => _failover.,1,GotoIf(\$[\"\${DIALSTATUS}\" = \"CHANUNAVAIL\"]?3)\n";
				$failover .= "exten => _failover.,2,Goto(5)\n";
				$failover .= "exten => _failover.,3,AGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----\${HANGUPCAUSE}-----\${DIALSTATUS}-----\${DIALEDTIME}-----\${ANSWEREDTIME})\n";
				$failover .= "exten => _failover.,4,Goto(OOUT" . $failover_carrier->{name} . ",\${EXTEN:8},1)\n";
				$failover .= "exten => _failover.,5,Hangup()\n\n";
			} elsif ($carriers->{$carrier}{failover_confition} eq 'CONGESTION') {
				$failover .= "exten => _failover.,1,GotoIf(\$[\"\${DIALSTATUS}\" = \"CONGESTION\"]?3)\n";
				$failover .= "exten => _failover.,2,Goto(5)\n";
				$failover .= "exten => _failover.,3,AGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----\${HANGUPCAUSE}-----\${DIALSTATUS}-----\${DIALEDTIME}-----\${ANSWEREDTIME})\n";
				$failover .= "exten => _failover.,4,Goto(OOUT" . $failover_carrier->{name} . ",\${EXTEN:8},1)\n";
				$failover .= "exten => _failover.,5,Hangup()\n\n";
			} elsif ($carriers->{$carrier}{failover_confition} eq 'BOTH') {
				$failover .= "exten => _failover.,1,GotoIf(\$[\"\${DIALSTATUS}\" = \"CHANUNAVAIL\"]?4)\n";
				$failover .= "exten => _failover.,2,GotoIf(\$[\"\${DIALSTATUS}\" = \"CONGESTION\"]?4)\n";
				$failover .= "exten => _failover.,3,Goto(6)\n";
				$failover .= "exten => _failover.,4,AGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----\${HANGUPCAUSE}-----\${DIALSTATUS}-----\${DIALEDTIME}-----\${ANSWEREDTIME})\n";
				$failover .= "exten => _failover.,5,Goto(OOUT" . $failover_carrier->{name} . ",\${EXTEN:8},1)\n";
				$failover .= "exten => _failover.,6,Hangup()\n\n";
			}
		}

		my $hangup = "exten => h,1,AGI(agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----\${HANGUPCAUSE}-----\${DIALSTATUS}-----\${DIALEDTIME}-----\${ANSWEREDTIME})\n\n";

		$dialplan .= "[OOUT" . $carriers->{$carrier}{name} . "]\n" . $hangup . $failover . $carriers->{$carrier}{dialplan} . "\n\n";
		if ($carriers->{$carrier}{protocol} eq "SIP") {
			$sip_config .= $carriers->{$carrier}{protocol_config} . "\n\n";
			$sip_registrations .= $carriers->{$carrier}{registrations} . "\n\n";
		} elsif ($carriers->{$carrier}{protocol} eq "SIP") {
			$iax_config .= $carriers->{$carrier}{protocol_config} . "\n\n";
			$iax_registrations .= $carriers->{$carrier}{registrations} . "\n\n";
		}

		# Process DIDs.
		$dialplan .= "[" . $context . "]\n";
		my $dids = {};
		my $stmt = sprintf("SELECT * FROM osdial_carrier_dids WHERE carrier_id='\%s';",$carrier);
		while (my $did = $osdial->sql_query($stmt)) {
			$dids->{$did->{extension}} = $did;
		}
		foreach my $did (sort keys %{$dids}) {
			$dialplan .= "exten => _" . $dids->{$did}{did} . ",1,Answer\n";
			$dialplan .= "exten => _" . $dids->{$did}{did} . ",n,AGI(agi://127.0.0.1:4577/call_log)\n";
			$dialplan .= "exten => _" . $dids->{$did}{did} . ",n,Ringing\n";
			$dialplan .= "exten => _" . $dids->{$did}{did} . ",n,Wait(3)\n";
			if ($dids->{$did}{did_action} eq 'INGROUP') {
				$dialplan .= "exten => _" . $dids->{$did}{did} . ",n,AGI(agi-VDAD_ALL_inbound.agi," . $dids->{$did}{lookup_method} . "-----" . $dids->{$did}{server_allocation} . "-----" . $dids->{$did}{ingroup};
				$dialplan .= "-----\${EXTEN}-----\${CALLERID(number)}-----" . $dids->{$did}{park_file} . "-----" . $dids->{$did}{initial_status} . "-----" . $dids->{$did}{default_list_id} . "-----";
				$dialplan .= $dids->{$did}{default_phone_code} . "-----" . $dids->{$did}{search_campaign} . ")\n";
			} elsif ($dids->{$did}{did_action} eq 'PHONE') {
				my $stmt = sprintf("SELECT * FROM phones WHERE extension='\%s' LIMIT 1;",$dids->{$did}{phone});
				while (my $phone = $osdial->sql_query($stmt)) {
					my @sip = split /\./, $phone->{server_ip};
					my $fsip = sprintf('%.3d*%.3d*%.3d*%.3d',@sip);
					$dialplan .= "exten => _" . $dids->{$did}{did} . ",n,Goto(osdial," . $fsip . '*' . $phone->{dialplan_number} . ",1)\n";
				}
			} elsif ($dids->{$did}{did_action} eq 'EXTENSION') {
				$dialplan .= "exten => _" . $dids->{$did}{did} . ",n,Goto(" . $dids->{$did}{extension_context} . "," . $dids->{$did}{extension} . ",1)\n";
			} elsif ($dids->{$did}{did_action} eq 'VOICEMAIL') {
				$dialplan .= "exten => _" . $dids->{$did}{did} . ",n,Voicemail(" . $dids->{$did}{voicemail} . ")\n";
			}
			$dialplan .= "exten => _" . $dids->{$did}{did} . ",n,Hangup()\n\n";
		}
	}
	$dialplan =~ s/\r\n/\n/gm;
	$sip_config =~ s/\r\n/\n/gm;
	$iax_config =~ s/\r\n/\n/gm;
	$sip_registrations =~ s/\r\n/\n/gm;
	$iax_registrations =~ s/\r\n/\n/gm;
	write_reload($sip_config,'osdial_sip_carriers','sip reload');
	write_reload($iax_config,'osdial_iax_carriers','iax2 reload');
	write_reload($dialplan,'osdial_extensions_carriers','extensions reload');
	return ($sip_registrations, $iax_registrations);
}


# Write out the output file, compare it to the running copy.
# If its different, overwrite and reload asterisk component.
#   Args:
#      $data   = The asterisk conf data.
#      $file   = The file to output, without extension.
#      $reload = The command to reload in asterisk.
sub write_reload {
	my ($data,$file,$reload) = @_;
	open(FIL, ">/tmp/$file.$$");
	print FIL $data;
	close FIL;
	my $ephnret = system("cmp","-s","/etc/asterisk/$file.conf","/tmp/$file.$$");
	if ($ephnret) {
		print "    " . $file . ".conf has changed, updating...\n" unless ($CLOquiet);
		if (!$CLOtest) {
			`cp /tmp/$file.$$ /etc/asterisk/$file.conf > /dev/null 2>&1`;
			$reloads{$reload} = 1;
		}
		sleep 1;
	} else {
		print "    $file.conf is current...\n" unless ($CLOquiet);
	}
	unlink("/tmp/$file.$$");
}


sub get_myips {
        my %IPs;
        my @ints = IO::Interface::Simple->interfaces;
        foreach my $int (@ints) {
                     my $ip = IO::Interface::Simple->new($int);
                     $IPs{$int} = $ip->address if ($ip->address);
        }

	# Delete loopback interface if more than 1 active server.
	my $stmt = "SELECT count(*) AS count FROM servers WHERE active='Y';";
	print $stmt . "\n" if ($DB);
	my $sret = $osdial->sql_query($stmt);
	delete $IPs{'lo'} if ($sret->{count} > 1);

        return \%IPs;
}

