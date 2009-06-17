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
use DBI;
use Getopt::Long;
use IO::Interface::Simple;
$|++;

# Identify myself.
my $prog = 'osdial_astgen.pl';

# Get OSD configuration directives.
my $config = getOSDconfig('/etc/osdial.conf');

# Auto-creation header.
my $achead = ";\n; WARNING: AUTO-CREATED FILE.\n; Any changes you make will be overwritten!\n;\n";

# Declare command-line options.
my($DB, $CLOhelp, $CLOtest, $CLOshowip, $CLOquiet);
my(%reloads);

# Read in command-line options.
if (scalar @ARGV) {
        GetOptions(
                'help!' => \$CLOhelp,
                'debug!' => \$DB,
                'test!' => \$CLOtest,
                'quiet!' => \$CLOquiet,
                'showip!' => \$CLOshowip
        );
        if ($DB) {
                print "----- DEBUGGING -----\n";
                print "----- Testing Mode -----\n" if ($CLOtest);
                print "VARS-\n";
                print "CLOhelp-     $CLOhelp\n";
                print "CLOshowip-   $CLOshowip\n";
                print "CLOquiet-    $CLOquiet\n";
                print "CLOtest-     $CLOtest\n";
                print "\n";
        }
        if ($CLOhelp) {
                print "\n\n" . $prog;
                print "allowed run-time options:\n";
                print "  [--help]         = This screen\n";
                print "  [--debug]        = debug\n";
                print "  [-t|--test]      = test only\n";
                print "  [-q|--quiet]     = Quiet output\n";
                print "  [-s|--showip]    = Show Interface IPs\n\n";
                exit 0;
        }
}


# Connect to database.
my $dbhA = DBI->connect("DBI:mysql:" . $config->{VARDB_database} . ":" . $config->{VARDB_server} . ":" . $config->{VARDB_port}, $config->{VARDB_user}, $config->{VARDB_pass} )
  or die "Couldn't connect to database: " . DBI->errstr;
print "    Connected to Database:  " . $config->{VARDB_server} . "|" . $config->{VARDB_database} . "\n" if ($DB);


# Get all of the IPs on this machines interfaces.
my $interfaces = get_myips($dbhA);
my @myips = (values %{$interfaces});
if ($CLOshowip) {
	print "\n  Found these interfaces / IPs\n";
	foreach my $key ( keys %{$interfaces} ) {
		print "    " . $key . " - " . $interfaces->{$key} . "\n";
	}
	print "\n";
	exit 0;
}


if (-f "/etc/asterisk/osdial_extensions.conf") {
	# TODO: Fix calc_password to not be so aggressive.
	#my $pass = calc_password();
	my $pass = '6l5a4i3d2s1o0o1s2d3i4a5l6';
	# Generate intra-server extensions and iax communication.
	# (osdial_extensions_servers.conf osdial_iax_servers.conf)
	gen_servers($dbhA,$pass);

	# Generate SIP/IAX registrations
	# (osdial_iax_registrations.conf osdial_sip_registrations.conf)
	gen_registrations($dbhA,$pass);

	# Generate meetme conferences and extensions.
	# (osdial_extensions_conferences.conf osdial_meetme.conf)
	gen_conferences($dbhA);

	# Generate agent extensions, and sip/iax agent phones.
	# (osdial_extensions_phones.conf osdial_sip_phones.conf osdial_iax_phones.conf)
	gen_phones($dbhA);

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
	my $stmtA = "select (sum(user) + sum(extension)) * count(user) from osdial_users,phones;";
	print $stmtA . "\n" if ($DB);
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my @aryA = $sthA->fetchrow_array;
	my $calc = $aryA[0];
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
	my ($dbhA,$pass) = @_;

	my $ireg = $achead;
	my $sreg = $achead;

	$ireg .= "register => ASTloop:$pass\@127.0.0.1:40569\n";
	$ireg .= "register => ASTblind:$pass\@127.0.0.1:41569\n";

	write_reload($ireg,'osdial_iax_registrations','iax2 reload');
	write_reload($sreg,'osdial_sip_registrations','sip reload');
}


# Generate intra-server extensions and iax communication.
# (osdial_extensions_servers.conf osdial_iax_servers.conf)
sub gen_servers {
	my ($dbhA,$pass) = @_;

	my $esvr = $achead;
	my $isvr = $achead;

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
	$isvr .= "allow=ulaw\n";
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
	$isvr .= "allow=ulaw\n";
	$isvr .= "qualify=no\n";

	# Get my server
	my $stmtA = "SELECT server_id,server_ip FROM servers WHERE";
	foreach my $ip (@myips) {
		$stmtA .= " server_ip=\'" . $ip . "\' OR";
	}
	chop $stmtA; chop $stmtA; chop $stmtA;
	$stmtA .= ';';
	print $stmtA . "\n" if ($DB);
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my $iname;
	while (my @aryA = $sthA->fetchrow_array) {
		my @sip = split /\./, $aryA[1];
		my $fsip = sprintf('%.3d*%.3d*%.3d*%.3d',@sip);
		$iname = $aryA[0];
		$esvr .= ";\n;" . $aryA[0] . ' - ' . $aryA[1] . "\n";
		$esvr .= "exten => _" . $fsip . "*.,1,Goto(osdial,\${EXTEN:16},1)\n";
		$isvr .= ";\n;" . $aryA[0] . ' - ' . $aryA[1] . "\n";
		$isvr .= "[" . $aryA[0] . "]\n";
		$isvr .= "type=user\n";
		$isvr .= "trunk=yes\n";
		$isvr .= "tos=0x18\n";
		$isvr .= "auth=md5\n";
		$isvr .= "secret=$pass\n";
		$isvr .= "disallow=all\n";
		$isvr .= "allow=ulaw\n";
		$isvr .= "context=osdial\n";
		$isvr .= "nat=no\n";

	}

	# Get other servers 
	my $stmtA = "SELECT server_id,server_ip FROM servers WHERE";
	foreach my $ip (@myips) {
		$stmtA .= " server_ip!=\'" . $ip . "\' AND";
	}
	chop $stmtA; chop $stmtA; chop $stmtA; chop $stmtA;
	$stmtA .= ';';
	print $stmtA . "\n" if ($DB);
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	while (my @aryA = $sthA->fetchrow_array) {
		my @sip = split /\./, $aryA[1];
		my $fsip = sprintf('%.3d*%.3d*%.3d*%.3d',@sip);
		$esvr .= ";\n;" . $aryA[0] . ' - ' . $aryA[1] . "\n";
		$esvr .= "exten => _" . $fsip . "*.,1,Dial(IAX2/" . $aryA[0] . "/\${EXTEN},,o)\n";
		$isvr .= ";\n;" . $aryA[0] . ' - ' . $aryA[1] . "\n";
		$isvr .= "[" . $aryA[0] . "]\n";
		$isvr .= "type=peer\n";
		$isvr .= "username=" . $aryA[0] . "\n";
		$isvr .= "host=" . $aryA[1] . "\n";
		$isvr .= "trunk=yes\n";
		$isvr .= "tos=0x18\n";
		$isvr .= "qualify=5000\n";
		$isvr .= "auth=md5\n";
		$isvr .= "secret=$pass\n";
		$isvr .= "disallow=all\n";
		$isvr .= "allow=ulaw\n";
		$isvr .= "peercontext=osdial\n";
		$isvr .= "nat=no\n";
	}

	write_reload($esvr,'osdial_extensions_servers','extensions reload');
	write_reload($isvr,'osdial_iax_servers','iax2 reload');
	
}


# Generate meetme conferences and extensions.
# (osdial_extensions_conferences.conf osdial_meetme.conf)
sub gen_conferences {
	my ($dbhA) = @_;
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

	my $stmtA = "SELECT conf_exten,server_ip FROM conferences where";
	my $stmtB = "SELECT asterisk_version FROM servers where";
	foreach my $ip (@myips) {
		$stmtA .= " server_ip=\'" . $ip . "\' OR";
		$stmtB .= " server_ip=\'" . $ip . "\' OR";
	}
	chop $stmtB; chop $stmtB; chop $stmtB;
	$stmtB .= ' limit 1;';
	print $stmtB . "\n" if ($DB);
	my $sthA = $dbhA->prepare($stmtB) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my @aryA = $sthA->fetchrow_array;
	my $asterisk_version = $aryA[0];

	if ($asterisk_version =~ /^1\.6/) {
		$cnf .= ";\n; DAHDIBarge direct channel extensions\n";
		$cnf .= "exten => _8612XXX,1,DAHDIBarge(\${EXTEN:4})\n";
		$cnf .= "exten => _8612XXX,2,Hangup\n";
	} else {
		$cnf .= ";\n; ZapBarge direct channel extensions\n";
		$cnf .= "exten => _8612XXX,1,ZapBarge(\${EXTEN:4})\n";
		$cnf .= "exten => _8612XXX,2,Hangup\n";
	}

	chop $stmtA; chop $stmtA; chop $stmtA;
	print $stmtA . "\n" if ($DB);
	$stmtA .= ';';
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my ($cnf2,$mtm2,$cf,$cl);
	while (my @aryA = $sthA->fetchrow_array) {
		$cf = $aryA[0] unless ($cf);
		$cl = $aryA[0];
		if ($asterisk_version =~ /^1\.6/) {
			$cnf2 .= "exten => _" . $aryA[0] . ",1,Meetme(\${EXTEN},q)\n";
		} else {
			$cnf2 .= "exten => _" . $aryA[0] . ",1,Meetme,\${EXTEN}|q\n";
		}
		$mtm2 .= "conf => " . $aryA[0] . "\n";
	}

	$cnf .= ";\n; OSDial Conferences $cf - $cl\n";
	$cnf .= $cnf2;
	$mtm .= ";\n; OSDial Conferences $cf - $cl\n";
	$mtm .= $mtm2;

	my $stmtA = "SELECT conf_exten,server_ip FROM osdial_conferences where";
        foreach my $ip (@myips) {
                $stmtA .= " server_ip=\'" . $ip . "\' OR";
        }
        chop $stmtA; chop $stmtA; chop $stmtA;
        $stmtA .= ";";
        print $stmtA . "\n" if ($DB);
        my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
        $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
        my ($cnf2,$mtm2,$cf,$cl);
        while (my @aryA = $sthA->fetchrow_array) {
                $cf = $aryA[0] unless ($cf);
                $cl = $aryA[0];
		if ($asterisk_version =~ /^1\.6/) {
			$cnf2 .= "exten => _" . $aryA[0] . ",1,Meetme(\${EXTEN},F)\n";
			$cnf2 .= "exten => _" . $aryA[0] . ",2,Hangup\n";
			$cnf2 .= "exten => _7" . $aryA[0] . ",1,Meetme(\${EXTEN:1},Fq)\n";
			$cnf2 .= "exten => _7" . $aryA[0] . ",2,Hangup\n";
			$cnf2 .= "exten => _6" . $aryA[0] . ",1,Meetme(\${EXTEN:1},Flq)\n";
			$cnf2 .= "exten => _6" . $aryA[0] . ",2,Hangup\n";
		} else {
			$cnf2 .= "exten => _" . $aryA[0] . ",1,Meetme,\${EXTEN}|F\n";
			$cnf2 .= "exten => _" . $aryA[0] . ",2,Hangup\n";
			$cnf2 .= "exten => _7" . $aryA[0] . ",1,Meetme,\${EXTEN:1}|Fq\n";
			$cnf2 .= "exten => _7" . $aryA[0] . ",2,Hangup\n";
			$cnf2 .= "exten => _6" . $aryA[0] . ",1,Meetme,\${EXTEN:1}|Fmq\n";
			$cnf2 .= "exten => _6" . $aryA[0] . ",2,Hangup\n";
		}
		$mtm2 .= "conf => " . $aryA[0] . "\n";
        }
	$cnf .= ";\n; OSDIAL Agent Conferences $cf - $cl\n";
	$cnf .= "; quiet entry and leaving conferences for OSDIAL $cf - $cl\n";
	$cnf .= "; quiet monitor extensions for meetme rooms (for room managers)  $cf - $cl\n";
	$cnf .= $cnf2;
	$mtm .= ";\n; OSDIAL Agent Conferences $cf - $cl\n";
	$mtm .= $mtm2;

	my $stmtA = "SELECT conf_exten FROM osdial_remote_agents WHERE user_start LIKE 'va\%'";
        my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
        my ($cnf2,$mtm2,$cf,$cl);
	while (my @aryA = $sthA->fetchrow_array) {
		if ($asterisk_version =~ /^1\.6/) {
			$cnf2 .= "exten => _" . $aryA[0] .  ",1,Meetme(\${EXTEN},Fq)\n";
			$cnf2 .= "exten => _" . $aryA[0] .  ",2,Hangup\n";
			$cnf2 .= "exten => _6" . $aryA[0] . ",1,Meetme(\${EXTEN:1},Flq)\n";
			$cnf2 .= "exten => _6" . $aryA[0] . ",2,Hangup\n";
			$cnf2 .= "exten => _7" . $aryA[0] . ",1,Meetme(\${EXTEN:1},Fq)\n";
			$cnf2 .= "exten => _7" . $aryA[0] . ",2,Hangup\n";
			$cnf2 .= "exten => _8" . $aryA[0] . ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _8" . $aryA[0] . ",2,Meetme(\${EXTEN:1},Fq)\n";
			$cnf2 .= "exten => _8" . $aryA[0] . ",3,Hangup\n";
		} else {
			$cnf2 .= "exten => _" . $aryA[0] .  ",1,Meetme,\${EXTEN}|Fq\n";
			$cnf2 .= "exten => _" . $aryA[0] .  ",2,Hangup\n";
			$cnf2 .= "exten => _6" . $aryA[0] . ",1,Meetme,\${EXTEN:1}|Fmq\n";
			$cnf2 .= "exten => _6" . $aryA[0] . ",2,Hangup\n";
			$cnf2 .= "exten => _7" . $aryA[0] . ",1,Meetme,\${EXTEN:1}|Fq\n";
			$cnf2 .= "exten => _7" . $aryA[0] . ",2,Hangup\n";
			$cnf2 .= "exten => _8" . $aryA[0] . ",1,Playback(sip-silence)\n";
			$cnf2 .= "exten => _8" . $aryA[0] . ",2,Meetme,\${EXTEN:1}|Fq\n";
			$cnf2 .= "exten => _8" . $aryA[0] . ",3,Hangup\n";
		}
		$mtm2 .= "conf => " . $aryA[0] . "\n";
	}
	$cnf2 .= "exten => 487487,1,AGI(agi-OSDivr.agi,\${EXTEN})\n";
	$cnf2 .= "exten => 487487,n,Hangup\n";
	$cnf .= ";\n; OSDIAL Virtual Agent Conferences\n";
	$cnf .= $cnf2;
	$mtm .= ";\n; OSDIAL Virtual Agent Conferences\n";
	$mtm .= $mtm2;

	write_reload($cnf,'osdial_extensions_conferences','extensions reload');
	write_reload($mtm,'osdial_meetme','reload app_meetme.so');
}

# Generate agent extensions, and sip/iax agent phones.
# (osdial_extensions_phones.conf osdial_sip_phones.conf osdial_iax_phones.conf)
sub gen_phones {
	my($dbhA) = @_;
	my $sphn = $achead;
	my $iphn = $achead;
	my $ephn = $achead;

	my $stmtA = "SELECT extension,dialplan_number,phone_ip,pass,protocol,phone_type FROM phones WHERE protocol IN ('SIP','IAX2','Zap','DAHDI') AND active='Y' AND (";
	foreach my $ip (@myips) {
		$stmtA .= " server_ip=\'" . $ip . "\' OR";
	}
	chop $stmtA; chop $stmtA; chop $stmtA;
	$stmtA .= ');';
	print $stmtA . "\n" if ($DB);
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	while (my @aryA = $sthA->fetchrow_array) {
		if ($aryA[4] eq "SIP" and $aryA[0] !~ /\@/) {
			$sphn .= ";\n[". $aryA[0] ."]\n";
			$sphn .= "type=friend\n";
			$sphn .= "disallow=all\n";
			$sphn .= "allow=ulaw\n";
			$sphn .= "username=" . $aryA[0] . "\n";
			$sphn .= "secret=" . $aryA[3] . "\n";
			if ($aryA[2]) {
				$sphn .= "host=" . $aryA[2] . "\n";
			} else {
				$sphn .= "host=dynamic\n";
			}
			if ($aryA[5] =~ /Grandstream/i) {
				$sphn .= "dtmfmode=info\n";
				$sphn .= "relaxdtmf=yes\n";
			} else {
				$sphn .= "dtmfmode=inband\n";
			}
			$sphn .= "qualify=yes\n";
			$sphn .= "nat=yes\n";
		} elsif ($aryA[4] eq "IAX2") {
			$iphn .= ";\n[". $aryA[0] ."]\n";
			$iphn .= "type=friend\n";
			$iphn .= "disallow=all\n";
			$iphn .= "allow=ulaw\n";
			$iphn .= "username=" . $aryA[0] . "\n";
			$iphn .= "secret=" . $aryA[3] . "\n";
			if ($aryA[2]) {
				$iphn .= "host=" . $aryA[2] . "\n";
			} else {
				$iphn .= "host=dynamic\n";
			}
			$iphn .= "dtmfmode=inband\n";
			$iphn .= "qualify=yes\n";
			$iphn .= "nat=yes\n";
		}
		my $dext = $aryA[4] . "/" . $aryA[0];
		if ($aryA[4] eq "SIP" and $aryA[0] =~ /\@/) {
			my($sext,$ssrv) = split /\@/,$aryA[0];
			$dext = $aryA[4] . "/" . $ssrv . "/" . $sext;
		}
		$ephn .= "exten => _" . $aryA[1] . ",1,Dial(" . $dext . ",55,to)\n";
	}

	write_reload($sphn,'osdial_sip_phones','sip reload');
	write_reload($iphn,'osdial_iax_phones','iax2 reload');
	write_reload($ephn,'osdial_extensions_phones','extensions reload');
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
	my ($dbhA) = @_;
        my %IPs;
        my @ints = IO::Interface::Simple->interfaces;
        foreach my $int (@ints) {
                     my $ip = IO::Interface::Simple->new($int);
                     $IPs{$int} = $ip->address if ($ip->address);
        }

	# Delete loopback interface if more than 1 active server.
	my $stmtA = "SELECT count(*) FROM servers WHERE active='Y';";
	print $stmtA . "\n" if ($DB);
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ", $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	my @aryA = $sthA->fetchrow_array;
	delete $IPs{'lo'} if ($aryA[0] > 1);

        return \%IPs;
}




###############################################################
sub getOSDconfig {
	my($AGCpath) = @_;
	my %config;
	$config{PATHconf} = $AGCpath;
	open(CONF, $config{PATHconf}) || die "can't open " . $config{PATHconf} . ": " . $! . "\n";
	while (my $line = <CONF>) {
		$line =~ s/ |>|"|'|\n|\r|\t|\#.*|;.*//gi;
		if ($line =~ /=|:/) {
			my($key,$val) = split /=|:/, $line;
			$config{$key} = $val;
		}
	}
	$config{VARDB_port} = '3306' unless ($config{VARDB_port});
	$config{VARFTP_port} = '21' unless ($config{VARFTP_port});
	$config{VARREPORT_port} = '21' unless ($config{VARREPORT_port});
	return \%config;
}

