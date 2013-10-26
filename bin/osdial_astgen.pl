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


use strict;
use OSDial;
use Getopt::Long;
use IO::Interface::Simple;
use Data::Dumper;
$|++;

# Identify myself.
my $prog = 'osdial_astgen.pl';

# Declare command-line options.
my($DB, $CLOhelp, $CLOtest, $CLOshowip, $CLOquiet, $CLOexpand, $CLOrealtime, $CLOrealtime_off);
my(%reloads);

my $realtime_ext = {};

my $osdial = OSDial->new('DB'=>$DB);

# Auto-creation header.
my $achead = ";\n; WARNING: AUTO-CREATED FILE.\n; Any changes you make will be overwritten!\n;\n";

$CLOrealtime=1;

# Read in command-line options.
if (scalar @ARGV) {
        GetOptions(
                'help!' => \$CLOhelp,
                'debug!' => \$DB,
                'test!' => \$CLOtest,
                'quiet!' => \$CLOquiet,
                'expand!' => \$CLOexpand,
                'realtime!' => \$CLOrealtime,
                'Realtime!' => \$CLOrealtime_off,
                'showip!' => \$CLOshowip
        );
	$CLOrealtime=0 if ($CLOrealtime_off);
        if ($DB) {
                print "----- DEBUGGING -----\n";
                print "----- Testing Mode -----\n" if ($CLOtest);
                print "VARS-\n";
                print "CLOhelp-     $CLOhelp\n";
                print "CLOshowip-   $CLOshowip\n";
                print "CLOquiet-    $CLOquiet\n";
                print "CLOexpand-   $CLOexpand\n";
                print "CLOrealtime- $CLOrealtime\n";
                print "CLORealtimeO-$CLOrealtime_off\n";
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
                print "  [-e|--expand]    = Expand Pattern Matching Extensions\n";
                print "  [-r|--realtime]  = Utilize Asterisk Realtime Configuration (default)\n";
                print "  [-R]             = Disable Asterisk Realtime Configuration\n";
                print "  [-s|--showip]    = Show Interface IPs\n\n";
                exit 0;
        }
}

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


my $pass = calc_password();

my $oext = '';
my $oblock = '';

my $trunkblind='';
my $trunkloop='';

my $asterisk_version;
if (-e "/usr/sbin/asterisk" and -f "/etc/asterisk/osdial_extensions.conf") {
	if ($CLOrealtime>0) {
		my $sqlsect='';
		my $sqlparams={};
		my $sqlfile = "/etc/asterisk/res_mysql.conf";
		my($sqldata);
		open(SQL, $sqlfile);
		while (my $sqlline = <SQL>) {
			$sqlline =~ s/\n|\r\n//g;
			if ($sqlline =~ /(^\s*\[|^\s*\w+\s*\=|^[^\;].*$)/) {
				my $tsqlsect = $sqlline;
				$tsqlsect =~ s/^\s*[\[](\w+)[\]]\s*.*$|^.*$/$1/;
				$sqlsect = $tsqlsect if ($tsqlsect ne '');
				if ($sqlsect ne '') {
					$sqlline =~ s/^\s*(\w+)\s*(\=)\s*(.*)$|^.*$/$1$2$3/;
					my ($sqlkey,$sqlval) = split(/\=/,$sqlline,2);
					$sqlparams->{$sqlsect}{$sqlkey} = $sqlval if (defined($sqlkey) and $sqlkey ne '');
				}
			}
		}
		close(SQL);
		my $rtdb='dialer';
		my $sts = $osdial->sql_connect('RT',$sqlparams->{$rtdb}{'dbname'},$sqlparams->{$rtdb}{'dbhost'},$sqlparams->{$rtdb}{'dbport'},$sqlparams->{$rtdb}{'dbuser'},$sqlparams->{$rtdb}{'dbpass'});
		$CLOrealtime=0 unless($sts);
		$CLOexpand=1 if ($CLOrealtime>0);
	}

	#Check and set Asterisk version.
	$asterisk_version = `/usr/sbin/asterisk -V`;
	chomp $asterisk_version;
	$asterisk_version =~ s/Asterisk //;
	if ($asterisk_version ne "") {
		my $stmt = sprintf("UPDATE servers SET asterisk_version='\%s' WHERE server_ip='\%s';",$asterisk_version,$osdial->{VARserver_ip});
		$osdial->sql_execute($stmt) if (!$CLOtest);
		print STDERR "\n|$stmt|\n" if ($DB);

		my $sret = $osdial->sql_query("SELECT count(*) AS fndarchive FROM configuration WHERE ((name='ArchiveHostname' AND data='') OR (name='ArchiveWebPath' AND data='http'));");
		if ($sret->{fndarchive}) {
			$osdial->sql_execute("UPDATE configuration SET data='" . $osdial->mres($osdial->{VARFTP_host}) . "' WHERE name='ArchiveHostname';");
			$osdial->sql_execute("UPDATE configuration SET data='FTP' WHERE name='ArchiveTransferMethod';");
			$osdial->sql_execute("UPDATE configuration SET data='" . $osdial->mres($osdial->{VARFTP_port}) . "' WHERE name='ArchivePort';");
			$osdial->sql_execute("UPDATE configuration SET data='" . $osdial->mres($osdial->{VARFTP_user}) . "' WHERE name='ArchiveUsername';");
			$osdial->sql_execute("UPDATE configuration SET data='" . $osdial->mres($osdial->{VARFTP_pass}) . "' WHERE name='ArchivePassword';");
			$osdial->sql_execute("UPDATE configuration SET data='" . $osdial->mres($osdial->{VARFTP_dir}) . "' WHERE name='ArchivePath';");
			$osdial->sql_execute("UPDATE configuration SET data='" . $osdial->mres($osdial->{VARHTTP_path}) . "' WHERE name='ArchiveWebPath';");
			$osdial->sql_execute("UPDATE configuration SET data='MP3' WHERE name='ArchiveMixFormat';");
			$osdial->sql_execute("UPDATE configuration SET data='" . $osdial->mres($osdial->{VARREPORT_dir}) . "' WHERE name='ArchiveReportPath';");
		}
	}

	#Fix some version related config differences
	my $modfile = "/etc/asterisk/modules.conf";
	$modfile = $osdial->{PATHdocs} . "/conf_examples/modules.conf" if (-s $modfile < 250);

	my $oefile = "/etc/asterisk/osdial_extensions.conf";
	$oefile = $osdial->{PATHdocs} . "/conf_examples/osdial_extensions.conf" if (-s $oefile < 250);

	my $vmfile = "/etc/asterisk/voicemail.conf";
	$vmfile = $osdial->{PATHdocs} . "/conf_examples/voicemail.conf" if (-s $vmfile < 250);

	my($oedata);
	open(OE, $oefile);
	while (my $oeline = <OE>) {
		$oedata .= $oeline;
	}
	close(OE);
	my($moddata);
	open(MOD, $modfile);
	while (my $modline = <MOD>) {
		$moddata .= $modline;
	}
	close(MOD);
	my($vmdata);
	open(VM, $vmfile);
	while (my $vmline = <VM>) {
		$vmdata .= $vmline;
	}
	close(VM);
	my $oereload;
	$oedata =~ s/\r\n/\n/gm;
	$trunkloop = "IAX2\/ASTloop:$pass\@127.0.0.1:40569";
	$trunkblind = "IAX2\/ASTblind:$pass\@127.0.0.1:41569";
	$oedata =~ s/^TRUNKloop.*$/TRUNKloop = $trunkloop/m;
	$oedata =~ s/^TRUNKblind.*$/TRUNKblind = $trunkblind/m;
	#$oedata = "TRUNKloop = $trunkloop\n"   . $oedata unless ($oedata =~ /^TRUNKloop.*$/m);
	#$oedata = "TRUNKblind = $trunkblind\n" . $oedata unless ($oedata =~ /^TRUNKblind.*$/m);
	if ($asterisk_version =~ /^1\.8|^10\.|^11\./) {
		$oereload = "dialplan reload";
		$oedata =~ s/^exten => h,1,DeadAGI/exten => h,1,AGI/gm;
		$moddata =~ s/^noload => chan_agent.so/load => chan_agent.so/gm;
		$moddata =~ s/^noload => app_queue.so/load => app_queue.so/gm;
		$moddata =~ s/^load => res_config_mysql.so/preload => res_config_mysql.so/gm;
		if (-e "/etc/asterisk/zapata.conf" and not -e "/etc/asterisk/chan_dahdi.conf") {
			my $pr = `cp /etc/asterisk/zapata.conf /etc/asterisk/chan_dahdi.conf`;
		}
	} elsif ($asterisk_version =~ /^1\.6/) {
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
	unless ($vmdata =~ /^\[osdial\]$/m) {
		$vmdata =~ s/^\[default\]$/[osdial]\n#include osdial_voicemail.conf\n\n[default]/gm;
	}
	write_reload($oedata,'osdial_extensions',$oereload);
	write_reload($moddata,'modules','reload');
	write_reload($vmdata,'voicemail','voicemail reload');

	# Generate intra-server extensions and iax communication.
	# (osdial_extensions_servers.conf osdial_iax_servers.conf)
	my($ssreg,$isreg) = gen_servers($pass);

	# Generate carrier configurations
	my($screg,$icreg,$dcc,$dccdp) = gen_carriers();

	# Generate SIP/IAX registrations
	# (osdial_iax_registrations.conf osdial_sip_registrations.conf)
	gen_registrations($ssreg.$screg, $isreg.$icreg);

	# Generate meetme conferences and extensions.
	# (osdial_extensions_conferences.conf osdial_meetme.conf)
	gen_conferences();

	# Generate agent extensions, and sip/iax agent phones.
	# (osdial_extensions_phones.conf osdial_sip_phones.conf osdial_iax_phones.conf)
	gen_phones($dcc,$dccdp);

	# Generate system level extensions, custom extensions, media, tts, etc.
	gen_osdial_extensions();


	if ($CLOrealtime>0 and scalar keys %{$realtime_ext} > 0) {
		# Cycle through all extensions and priorities, remove non-existant entries.
		my $rtsql = sprintf("SELECT context,exten,priority,count(*) as extcount FROM extensions GROUP BY context,exten,priority;");
		my $rtext_del = {};
		my $rtprio_del = {};
		while (my $sret = $osdial->sql_query($rtsql,'RT')) {
			if (!defined($realtime_ext->{$sret->{context}}{$sret->{exten}})) {
				$rtext_del->{$sret->{context}.'::'.$sret->{exten}} = 1;
			} elsif (!defined($realtime_ext->{$sret->{context}}{$sret->{exten}}{$sret->{priority}})) {
				$rtprio_del->{$sret->{context}.'::'.$sret->{exten}.'::'.$sret->{priority}}=1;
			}
		}
		foreach my $extdel (keys %{$rtext_del}) {
			my($rtcontext,$rtexten) = split(/::/g,$extdel);
			my $rtsql = sprintf("DELETE FROM extensions WHERE context='%s' AND exten='%s';",$osdial->mres($rtcontext),$osdial->mres($rtexten));
			$osdial->sql_execute($rtsql,'RT');
		}
		foreach my $priodel (keys %{$rtprio_del}) {
			my($rtcontext,$rtexten,$rtprio) = split(/::/g,$priodel);
			my $rtsql = sprintf("DELETE FROM extensions WHERE context='%s' AND exten='%s' AND priority='%s';",$osdial->mres($rtcontext),$osdial->mres($rtexten),$osdial->mres($rtprio));
			$osdial->sql_execute($rtsql,'RT');
		}

		my $rtins_cnt=0;
		my $rtins_sql = '';
		foreach my $rtcontext (sort {$a <=> $b} keys %{$realtime_ext}) {
			foreach my $rtexten (sort {$a <=> $b} keys %{$realtime_ext->{$rtcontext}}) {
				my $rtext_mod = 0;
				my $rtprio_cnt = 0;
				my $rtprio_last = 0;
				foreach my $rtprio (sort {$a <=> $b} keys %{$realtime_ext->{$rtcontext}{$rtexten}}) {
					my $rtprio_mod = 0;
					my $rtprio_exist = 0;
					my $rtapp = $realtime_ext->{$rtcontext}{$rtexten}{$rtprio}{'app'};
					my $rtappdata = $realtime_ext->{$rtcontext}{$rtexten}{$rtprio}{'appdata'};
					my $rtsql = sprintf("SELECT * FROM extensions WHERE context='%s' AND exten='%s' AND priority='%d';",$osdial->mres($rtcontext),$osdial->mres($rtexten),$osdial->mres($rtprio));
					while (my $sret = $osdial->sql_query($rtsql,'RT')) {
						unless ($sret->{'app'} eq $rtapp and $sret->{'appdata'} eq $rtappdata) {
							$rtprio_mod++;
							$rtext_mod++;
						}
						$rtprio_last = $sret->{'priority'};
						$rtprio_exist++;
					}
					# If priority exists and is different, perform update.
					if ($rtprio_exist>0 and $rtprio_mod>0) {
						my $rtsql = sprintf("UPDATE extensions SET app='%s',appdata='%s' WHERE context='%s' AND exten='%s' AND priority='%d';",$osdial->mres($rtapp),$osdial->mres($rtappdata),$osdial->mres($rtcontext),$osdial->mres($rtexten),$osdial->mres($rtprio));
						$osdial->sql_execute($rtsql,'RT');
						$rtext_mod--;
					}
					$rtprio_cnt++;
				}
				# Delete all matching extensions if the priority count doesn't match or the exten was modified.
				if ($rtprio_last!=0 and ($rtext_mod>0 or $rtprio_cnt!=$rtprio_last)) {
					my $rtsql = sprintf("DELETE FROM extensions WHERE context='%s' AND exten='%s';",$osdial->mres($rtcontext),$osdial->mres($rtexten));
					$osdial->sql_execute($rtsql,'RT');
					$rtprio_last=0;
				}
				# Insert new records if extension was modified, or that db priorities are 0 and there are more than 0 priorities to add.
				if ($rtext_mod>0 or ($rtprio_last==0 and $rtprio_cnt>0)) {
					foreach my $rtprio (sort {$a <=> $b} keys %{$realtime_ext->{$rtcontext}{$rtexten}}) {
						my $rtapp = $realtime_ext->{$rtcontext}{$rtexten}{$rtprio}{'app'};
						my $rtappdata = $realtime_ext->{$rtcontext}{$rtexten}{$rtprio}{'appdata'};
						if ($rtins_cnt++==0) {
							$rtins_sql .= sprintf("INSERT INTO extensions (context,exten,priority,app,appdata) VALUES ('%s','%s','%d','%s','%s')",$osdial->mres($rtcontext),$osdial->mres($rtexten),$osdial->mres($rtprio),$osdial->mres($rtapp),$osdial->mres($rtappdata));
						} else {
							$rtins_sql .= sprintf(",('%s','%s','%d','%s','%s')",$osdial->mres($rtcontext),$osdial->mres($rtexten),$osdial->mres($rtprio),$osdial->mres($rtapp),$osdial->mres($rtappdata));
						}
						if ($rtins_cnt>500) {
							$rtins_sql .= ';';
							$rtins_cnt=0;
							$osdial->sql_execute($rtins_sql,'RT');
							$rtins_sql='';
						}
					}
				}
			}
		}
		if ($rtins_sql ne '') {
			$rtins_sql .= ';';
			$rtins_cnt=0;
			$osdial->sql_execute($rtins_sql,'RT');
			$rtins_sql='';
		}

	}


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
	my $stmt = "SELECT MD5(CONCAT((SELECT company_name FROM system_settings LIMIT 1),SUM(INET_ATON(server_ip)))) AS calc FROM servers;";
	my $sret = $osdial->sql_query($stmt);
	my $pass = $sret->{calc};
	$pass = '6l5a4i3d2s1o0o1s2d3i4a5l6' if ($pass eq '');;
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
	my $ssvr = $achead;
	my $sreg='';
	my $ireg='';

	my $pmmask="deny=0.0.0.0/0.0.0.0\n";
	$pmmask.="permit=127.0.0.1/255.255.255.255\n";
	my $stmt = "SELECT * FROM servers WHERE active='Y' AND server_profile IN ('AIO','DIALER') ORDER BY server_ip;";
	while (my $sret = $osdial->sql_query($stmt)) {
		$pmmask.="permit=" . $sret->{server_ip} . "/255.255.255.255\n";
	}

	$isvr .= ";\n; IAX loopback for testing\n";
	$isvr .= "[ASTloop]\n";
	$isvr .= "type=friend\n";
	$isvr .= "accountcode=ASTloop\n";
	$isvr .= "context=osdial\n";
	$isvr .= "auth=plaintext\n";
	$isvr .= "trunk=yes\n";
	$isvr .= "host=dynamic\n";
	$isvr .= $pmmask;
	$isvr .= "secret=$pass\n";
	$isvr .= "disallow=all\n";
	$isvr .= "allow=ulaw,alaw,gsm,slin,slin16,g729,g726,g722,g723\n";
	$isvr .= "requirecalltoken=no\n";
	$isvr .= "qualify=no\n";

	$isvr .= ";\n; IAX loopback for blind monitoring\n";
	$isvr .= "[ASTblind]\n";
	$isvr .= "type=friend\n";
	$isvr .= "accountcode=ASTblind\n";
	$isvr .= "context=osdial\n";
	$isvr .= "auth=plaintext\n";
	$isvr .= "trunk=yes\n";
	$isvr .= "host=dynamic\n";
	$isvr .= $pmmask;
	$isvr .= "secret=$pass\n";
	$isvr .= "disallow=all\n";
	$isvr .= "allow=ulaw,alaw,gsm,slin,slin16,g729,g726,g722,g723\n";
	$isvr .= "requirecalltoken=no\n";
	$isvr .= "qualify=no\n";

	$ireg .= "register => ASTloop:$pass\@127.0.0.1:40569\n";
	$ireg .= "register => ASTblind:$pass\@127.0.0.1:41569\n";



	# Get my server
	my $stmt = "SELECT server_id,server_ip FROM servers WHERE (";
	foreach my $ip (@myips) {
		$stmt .= " server_ip=\'" . $ip . "\' OR";
	}
	chop $stmt; chop $stmt; chop $stmt;
	$stmt .= ") AND server_profile IN ('AIO','DIALER');";
	print $stmt . "\n" if ($DB);
	while (my $sret = $osdial->sql_query($stmt)) {
		$sret->{server_id} =~ s/-|\./_/g;
		my @sip = split /\./, $sret->{server_ip};
		my $fsip = sprintf('%.3d*%.3d*%.3d*%.3d',@sip);
		my $fsip2 = sprintf('%.3d%.3d%.3d%.3d',@sip);
		$esvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		#$esvr .= procexten("osdial","_".$fsip."*.","1","Goto","osdial,\${EXTEN:16},1");
		#$esvr .= procexten("osdial","_".$fsip."#.","1","Goto","osdial,\${EXTEN:16},1");
		#$esvr .= procexten("osdial","_".$fsip2.".","1","Goto","osdial,\${EXTEN:12},1");
		$esvr .= procexten("osdial","_".$fsip."*.","1","Dial","Local/\${EXTEN:16}\@osdial,,o");
		$esvr .= procexten("osdial","_".$fsip."*.","2","Hangup","");
		$esvr .= procexten("osdial","_".$fsip."#.","1","Dial","Local/\${EXTEN:16}\@osdial,,o");
		$esvr .= procexten("osdial","_".$fsip."#.","2","Hangup","");
		$esvr .= procexten("osdial","_".$fsip2.".","1","Dial","Local/\${EXTEN:12}\@osdial,,o");
		$esvr .= procexten("osdial","_".$fsip2.".","2","Hangup","");

		$oblock .= procexten("osdialBLOCK","_".$fsip."*.","1","Goto","osdial,\${EXTEN:16},1");
		$oblock .= procexten("osdialBLOCK","_".$fsip."#.","1","Goto","osdial,\${EXTEN:16},1");
		$oblock .= procexten("osdialBLOCK","_".$fsip2.".","1","Goto","osdial,\${EXTEN:12},1");

		$isvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$isvr .= "[" . $sret->{server_id} . "]\n";
		$isvr .= "type=friend\n";
		$isvr .= "username=" . $sret->{server_id} . "\n";
		$isvr .= "host=" . $sret->{server_ip} . "\n";
		$isvr .= $pmmask;
		$isvr .= "trunk=yes\n";
		$isvr .= "tos=ef\n";
		$isvr .= "cos=5\n";
		$isvr .= "qualify=no\n";
		$isvr .= "auth=plaintext\n";
		$isvr .= "secret=$pass\n";
		$isvr .= "disallow=all\n";
		$isvr .= "allow=ulaw,alaw,gsm,slin,slin16,g729,g726,g722,g723\n";
		$isvr .= "context=osdial\n";
		$isvr .= "requirecalltoken=no\n";

		$ssvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$ssvr .= "[" . $sret->{server_id} . "]\n";
		$ssvr .= "type=friend\n";
		$ssvr .= "username=" . $sret->{server_id} . "\n";
		$ssvr .= "host=" . $sret->{server_ip} . "\n";
		$ssvr .= $pmmask;
		$ssvr .= "tos_sip=cs3\n";
		$ssvr .= "tos_audio=ef\n";
		$ssvr .= "cos_sip=3\n";
		$ssvr .= "cos_audio=5\n";
		$ssvr .= "qualify=no\n";
		$ssvr .= "secret=$pass\n";
		$ssvr .= "disallow=all\n";
		$ssvr .= "allow=ulaw,alaw,gsm,slin,slin16,g729,g726,g722,g723\n";
		$ssvr .= "dtmfmode=auto\n";
		$ssvr .= "relaxdtmf=yes\n";
		$ssvr .= "context=osdial\n";
		$ssvr .= "insecure=port\n";
		$ssvr .= "canreinvite=no\n";
	}

	# Get other servers 
	my $stmt = "SELECT server_id,server_ip FROM servers WHERE active='Y' AND server_profile IN ('AIO','DIALER') AND";
	foreach my $ip (@myips) {
		$stmt .= " server_ip!=\'" . $ip . "\' AND";
	}
	chop $stmt; chop $stmt; chop $stmt; chop $stmt;
	$stmt .= ';';
	print $stmt . "\n" if ($DB);
	while (my $sret = $osdial->sql_query($stmt)) {
		$sret->{server_id} =~ s/-|\./_/g;
		my @sip = split /\./, $sret->{server_ip};
		my $fsip = sprintf('%.3d*%.3d*%.3d*%.3d',@sip);
		$esvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$esvr .= procexten("osdial","_" . $fsip . "*.","1","Dial","SIP/" . $sret->{server_id} . "/\${EXTEN},,o");
		$esvr .= procexten("osdial","_" . $fsip . "*.","2","Hangup","");
		$esvr .= procexten("osdial","_" . $fsip . "#.","1","Dial","IAX2/" . $sret->{server_id} . "/\${EXTEN},,o");
		$esvr .= procexten("osdial","_" . $fsip . "#.","2","Hangup","");

		$oblock .= procexten("osdialBLOCK","_" . $fsip . "*.","1","Goto","osdial,\${EXTEN},1");
		$oblock .= procexten("osdialBLOCK","_" . $fsip . "#.","1","Goto","osdial,\${EXTEN},1");

		$isvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$isvr .= "[" . $sret->{server_id} . "]\n";
		$isvr .= "type=friend\n";
		$isvr .= "username=" . $sret->{server_id} . "\n";
		$isvr .= "host=" . $sret->{server_ip} . "\n";
		$isvr .= $pmmask;
		$isvr .= "trunk=yes\n";
		$isvr .= "tos=ef\n";
		$isvr .= "cos=5\n";
		$isvr .= "qualify=no\n";
		$isvr .= "auth=plaintext\n";
		$isvr .= "secret=$pass\n";
		$isvr .= "disallow=all\n";
		$isvr .= "allow=ulaw,alaw,gsm,slin,slin16,g729,g726,g722,g723\n";
		$isvr .= "context=osdial\n";
		$isvr .= "requirecalltoken=no\n";

		$ssvr .= ";\n;" . $sret->{server_id} . ' - ' . $sret->{server_ip} . "\n";
		$ssvr .= "[" . $sret->{server_id} . "]\n";
		$ssvr .= "type=friend\n";
		$ssvr .= "username=" . $sret->{server_id} . "\n";
		$ssvr .= "host=" . $sret->{server_ip} . "\n";
		$ssvr .= $pmmask;
		$ssvr .= "tos_sip=cs3\n";
		$ssvr .= "tos_audio=ef\n";
		$ssvr .= "cos_sip=3\n";
		$ssvr .= "cos_audio=5\n";
		$ssvr .= "qualify=no\n";
		$ssvr .= "secret=$pass\n";
		$ssvr .= "disallow=all\n";
		$ssvr .= "allow=ulaw,alaw,gsm,slin,slin16,g729,g726,g722,g723\n";
		$ssvr .= "dtmfmode=auto\n";
		$ssvr .= "relaxdtmf=yes\n";
		$ssvr .= "context=osdial\n";
		$ssvr .= "insecure=port\n";
		$ssvr .= "canreinvite=no\n";
	}

	my $extreload = "extensions reload";
	if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
		$extreload = "dialplan reload";
	}
	write_reload($esvr,'osdial_extensions_servers',$extreload);
	write_reload($isvr,'osdial_iax_servers','iax2 reload');
	write_reload($ssvr,'osdial_sip_servers','sip reload');
	
	return ($sreg, $ireg);
}


# Generate meetme conferences and extensions.
# (osdial_extensions_conferences.conf osdial_meetme.conf)
sub gen_conferences {
	my $cnf = $achead;
	my $mtm = $achead;
	my $rtmtm = {};
	if ($CLOrealtime>0) {
		$osdial->sql_execute("INSERT INTO meetme (confno,pin) VALUES ('8600',NULL),('8601','1234') ON DUPLICATE KEY UPDATE pin=pin;",'RT');
	} else {
		$mtm .= "conf => 8600\nconf => 8601,1234\n";
	}
	if (!$CLOexpand) {
		$cnf .= ";\n;Volume adjustments\n";
		$cnf .= procexten("osdial","_X4860XXXX","1","MeetMeAdmin","\${EXTEN:2},T,\${EXTEN:0:1}");
		$cnf .= procexten("osdial","_X4860XXXX","2","Hangup","");
		$cnf .= procexten("osdial","_X3860XXXX","1","MeetMeAdmin","\${EXTEN:2},t,\${EXTEN:0:1}");
		$cnf .= procexten("osdial","_X3860XXXX","2","Hangup","");
		$cnf .= procexten("osdial","_X2860XXXX","1","MeetMeAdmin","\${EXTEN:2},m,\${EXTEN:0:1}");
		$cnf .= procexten("osdial","_X2860XXXX","2","Hangup","");
		$cnf .= procexten("osdial","_X1860XXXX","1","MeetMeAdmin","\${EXTEN:2},M,\${EXTEN:0:1}");
		$cnf .= procexten("osdial","_X1860XXXX","2","Hangup","");
		$cnf .= procexten("osdial","_5555860XXXX","1","MeetMeAdmin","\${EXTEN:4},K");
		$cnf .= procexten("osdial","_5555860XXXX","2","Hangup","");
		$cnf .= procexten("osdial","_555587XXXXXX","1","MeetMeAdmin","\${EXTEN:4},K");
		$cnf .= procexten("osdial","_555587XXXXXX","2","Hangup","");
	}
	if (!$CLOexpand) {
		$cnf .= ";\n;Local blind monitoring\n";
		$cnf .= procexten("osdial","_0860XXXX","1","Dial",$trunkblind."/6\${EXTEN:1},55,o");
		$cnf .= procexten("osdial","_0860XXXX","2","Hangup","");
		$cnf .= procexten("osdial","_0X860XXXX","1","Dial",$trunkblind."/\${EXTEN:1},55,o");
		$cnf .= procexten("osdial","_0X860XXXX","2","Hangup","");
	}

	my $stmt = "SELECT conf_exten,server_ip FROM conferences WHERE";
	foreach my $ip (@myips) {
		$stmt .= " server_ip=\'" . $ip . "\' OR";
	}

	#if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
	#	$cnf .= ";\n; DAHDIBarge direct channel extensions\n";
	#	$cnf .= procexten("osdial","_8612XXX","1","DAHDIBarge","\${EXTEN:4}");
	#} else {
	#	$cnf .= ";\n; ZapBarge direct channel extensions\n";
	#	$cnf .= procexten("osdial","_8612XXX","1","ZapBarge","\${EXTEN:4}");
	#}
	#$cnf .= procexten("osdial","_8612XXX","2","Hangup","");

	chop $stmt; chop $stmt; chop $stmt;
	print $stmt . "\n" if ($DB);
	$stmt .= ';';
	my ($cnf2,$mtm2,$cf,$cl);
	while (my $sret = $osdial->sql_query($stmt)) {
		$cf = $sret->{conf_exten} unless ($cf);
		$cl = $sret->{conf_exten};
		if ($CLOexpand or $cl !~ /^8600...$/) {
			if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
				if ($CLOexpand) {
					foreach my $prenum (0..9) {
						if ($prenum==0) {
							foreach my $prenum2 (0..9) {
								$cnf2 .= procexten("osdial",$prenum.$prenum2.$sret->{conf_exten},"1","Dial",$trunkblind."/\${EXTEN:1},55,o");
								$cnf2 .= procexten("osdial",$prenum.$prenum2.$sret->{conf_exten},"2","Hangup","");
							}
						} else {
							$cnf2 .= procexten("osdial",$prenum."4".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:2},T,\${EXTEN:0:1}");
							$cnf2 .= procexten("osdial",$prenum."4".$sret->{conf_exten},"2","Hangup","");
							$cnf2 .= procexten("osdial",$prenum."3".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:2},t,\${EXTEN:0:1}");
							$cnf2 .= procexten("osdial",$prenum."3".$sret->{conf_exten},"2","Hangup","");
							$cnf2 .= procexten("osdial",$prenum."2".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:2},m,\${EXTEN:0:1}");
							$cnf2 .= procexten("osdial",$prenum."2".$sret->{conf_exten},"2","Hangup","");
							$cnf2 .= procexten("osdial",$prenum."1".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:2},M,\${EXTEN:0:1}");
							$cnf2 .= procexten("osdial",$prenum."1".$sret->{conf_exten},"2","Hangup","");
						}
					}
					$cnf2 .= procexten("osdial","0".$sret->{conf_exten},"1","Dial",$trunkblind."/6\${EXTEN:1},55,o");
					$cnf2 .= procexten("osdial","0".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","5555".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:4},K");
					$cnf2 .= procexten("osdial","5555".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial",$sret->{conf_exten},"1","AGI","agi-OSDagent_conf.agi,genconf");
					$cnf2 .= procexten("osdial",$sret->{conf_exten},"2","Hangup","");
				} else {
					$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"1","Meetme","\${EXTEN},q");
					$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"2","Hangup","");
				}
			} else {
				$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"1","Meetme","\${EXTEN}|q");
				$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"2","Hangup","");
			}
		}
		if ($CLOrealtime>0) {
			$rtmtm->{$sret->{conf_exten}}=1;
		} else {
			$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
		}
	}
	if (!$CLOexpand) {
		$cnf2 .= procexten("osdial","_8600XXX","1","AGI","agi-OSDagent_conf.agi,genconf");
		$cnf2 .= procexten("osdial","_8600XXX","2","Hangup","");
	}

	$cnf .= ";\n; OSDial Conferences $cf - $cl\n";
	$cnf .= $cnf2;
	if (!$CLOrealtime>0) {
		$mtm .= ";\n; OSDial Conferences $cf - $cl\n";
		$mtm .= $mtm2;
	}

	my $stmt = "SELECT conf_exten,server_ip FROM osdial_conferences WHERE";
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
		if ($CLOexpand or $cl !~ /^8601...$/) {
			if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
				if ($CLOexpand) {
					foreach my $prenum (0..9) {
						if ($prenum==0) {
							foreach my $prenum2 (0..9) {
								$cnf2 .= procexten("osdial",$prenum.$prenum2.$sret->{conf_exten},"1","Dial",$trunkblind."/\${EXTEN:1},55,o");
								$cnf2 .= procexten("osdial",$prenum.$prenum2.$sret->{conf_exten},"2","Hangup","");
							}
						} else {
							$cnf2 .= procexten("osdial",$prenum."4".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:2},T,\${EXTEN:0:1}");
							$cnf2 .= procexten("osdial",$prenum."4".$sret->{conf_exten},"2","Hangup","");
							$cnf2 .= procexten("osdial",$prenum."3".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:2},t,\${EXTEN:0:1}");
							$cnf2 .= procexten("osdial",$prenum."3".$sret->{conf_exten},"2","Hangup","");
							$cnf2 .= procexten("osdial",$prenum."2".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:2},m,\${EXTEN:0:1}");
							$cnf2 .= procexten("osdial",$prenum."2".$sret->{conf_exten},"2","Hangup","");
							$cnf2 .= procexten("osdial",$prenum."1".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:2},M,\${EXTEN:0:1}");
							$cnf2 .= procexten("osdial",$prenum."1".$sret->{conf_exten},"2","Hangup","");
						}
					}
					$cnf2 .= procexten("osdial","0".$sret->{conf_exten},"1","Dial",$trunkblind."/6\${EXTEN:1},55,o");
					$cnf2 .= procexten("osdial","0".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","5555".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:4},K");
					$cnf2 .= procexten("osdial","5555".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial",$sret->{conf_exten},"1","AGI","agi-OSDagent_conf.agi,agentconf");
					$cnf2 .= procexten("osdial",$sret->{conf_exten},"2","Hangup","");
					foreach my $prenum (qw(1 2 3 6 7 9)) {
						$cnf2 .= procexten("osdial",$prenum.$sret->{conf_exten},"1","AGI","agi-OSDagent_conf.agi,agentmon");
						$cnf2 .= procexten("osdial",$prenum.$sret->{conf_exten},"2","Hangup","");
					}
				} else {
					$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"1","Meetme","\${EXTEN},F");
					$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_1".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},F");
					$cnf2 .= procexten("osdial","_1".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"1","Set","SPYGROUP=\${EXTEN:1}");
					$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"2","Meetme","\${EXTEN:1},F");
					$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"3","Hangup","");
					$cnf2 .= procexten("osdial","_3".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Flq");
					$cnf2 .= procexten("osdial","_3".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_6".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Flq");
					$cnf2 .= procexten("osdial","_6".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_7".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Fq");
					$cnf2 .= procexten("osdial","_7".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_9".$sret->{conf_exten},"1","Chanspy",",g(\${EXTEN:1})qwES");
					$cnf2 .= procexten("osdial","_9".$sret->{conf_exten},"2","Hangup","");
				}
			} else {
				$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"1","Meetme","\${EXTEN}|F");
				$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_1".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|F");
				$cnf2 .= procexten("osdial","_1".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|F");
				$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_3".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fmq");
				$cnf2 .= procexten("osdial","_3".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_6".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fmq");
				$cnf2 .= procexten("osdial","_6".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_7".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fq");
				$cnf2 .= procexten("osdial","_7".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_9".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fmq");
				$cnf2 .= procexten("osdial","_9".$sret->{conf_exten},"2","Hangup","");
			}
		}
		if ($CLOrealtime>0) {
			$rtmtm->{$sret->{conf_exten}}=1;
		} else {
			$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
		}
        }
	if (!$CLOexpand) {
		$cnf2 .= procexten("osdial","_8601XXX","1","AGI","agi-OSDagent_conf.agi,agentconf");
		$cnf2 .= procexten("osdial","_8601XXX","2","Hangup","");
		$cnf2 .= procexten("osdial","_68601XXX","1","AGI","agi-OSDagent_conf.agi,agentmon");
		$cnf2 .= procexten("osdial","_68601XXX","2","Hangup","");
		$cnf2 .= procexten("osdial","_78601XXX","1","AGI","agi-OSDagent_conf.agi,agentmon");
		$cnf2 .= procexten("osdial","_78601XXX","2","Hangup","");
		$cnf2 .= procexten("osdial","_98601XXX","1","AGI","agi-OSDagent_conf.agi,agentmon");
		$cnf2 .= procexten("osdial","_98601XXX","2","Hangup","");
		$cnf2 .= procexten("osdial","_Z8601XXX","1","AGI","agi-OSDagent_conf.agi,agentmon");
		$cnf2 .= procexten("osdial","_Z8601XXX","2","Hangup","");
	}

	$cnf .= ";\n; OSDIAL Agent Conferences $cf - $cl\n";
	$cnf .= "; quiet entry and leaving conferences for OSDIAL $cf - $cl\n";
	$cnf .= "; quiet monitor extensions for meetme rooms (for room managers)  $cf - $cl\n";
	$cnf .= $cnf2;
	if (!$CLOrealtime>0) {
		$mtm .= ";\n; OSDIAL Agent Conferences $cf - $cl\n";
		$mtm .= $mtm2;
	}

	my $stmt = "SELECT conf_exten FROM osdial_remote_agents WHERE user_start LIKE 'va\%';";
        my ($cnf2,$mtm2,$cf,$cl);
	while (my $sret = $osdial->sql_query($stmt)) {
		if ($CLOexpand or $sret->{conf_exten} !~ /^87......$/) {
			if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
				if ($CLOexpand) {
					$cnf2 .= procexten("osdial","5555".$sret->{conf_exten},"1","MeetMeAdmin","\${EXTEN:4},K");
					$cnf2 .= procexten("osdial","5555".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial",$sret->{conf_exten},"1","Answer","");
					$cnf2 .= procexten("osdial",$sret->{conf_exten},"2","Playback","sip-silence");
					$cnf2 .= procexten("osdial",$sret->{conf_exten},"3","AGI","agi-OSDagent_conf.agi,vaconf");
					$cnf2 .= procexten("osdial",$sret->{conf_exten},"4","Hangup","");
					foreach my $prenum (qw(1 2 3 6 7 8 9)) {
						$cnf2 .= procexten("osdial",$prenum.$sret->{conf_exten},"1","AGI","agi-OSDagent_conf.agi,vamon");
						$cnf2 .= procexten("osdial",$prenum.$sret->{conf_exten},"2","Hangup","");
					}
				} else {
					$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"1","Answer","");
					$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"2","Playback","sip-silence");
					$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"3","Meetme","\${EXTEN},Fq");
					$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"4","Hangup","");
					$cnf2 .= procexten("osdial","_1".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Fq");
					$cnf2 .= procexten("osdial","_1".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Fq");
					$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_3".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Flq");
					$cnf2 .= procexten("osdial","_3".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_6".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Flq");
					$cnf2 .= procexten("osdial","_6".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_7".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Fq");
					$cnf2 .= procexten("osdial","_7".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_8".$sret->{conf_exten},"1","Meetme","\${EXTEN:1},Fq");
					$cnf2 .= procexten("osdial","_8".$sret->{conf_exten},"2","Hangup","");
					$cnf2 .= procexten("osdial","_9".$sret->{conf_exten},"1","Chanspy",",g(\${EXTEN:1})qwES");
					$cnf2 .= procexten("osdial","_9".$sret->{conf_exten},"2","Hangup","");
				}
			} else {
				$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"1","Answer","");
				$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"2","Playback","sip-silence");
				$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"3","Meetme","\${EXTEN}|Fq");
				$cnf2 .= procexten("osdial","_".$sret->{conf_exten},"4","Hangup","");
				$cnf2 .= procexten("osdial","_1".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fq");
				$cnf2 .= procexten("osdial","_1".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fq");
				$cnf2 .= procexten("osdial","_2".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_3".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fmq");
				$cnf2 .= procexten("osdial","_3".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_6".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fmq");
				$cnf2 .= procexten("osdial","_6".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_7".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fq");
				$cnf2 .= procexten("osdial","_7".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_8".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fq");
				$cnf2 .= procexten("osdial","_8".$sret->{conf_exten},"2","Hangup","");
				$cnf2 .= procexten("osdial","_9".$sret->{conf_exten},"1","Meetme","\${EXTEN:1}|Fmq");
				$cnf2 .= procexten("osdial","_9".$sret->{conf_exten},"2","Hangup","");
			}
		}
		if ($CLOrealtime>0) {
			$rtmtm->{$sret->{conf_exten}}=1;
		} else {
			$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
		}
	}

	if (!$CLOexpand) {
		$cnf2 .= procexten("osdial","_87XXXXXX","1","Answer","");
		$cnf2 .= procexten("osdial","_87XXXXXX","2","Playback","sip-silence");
		$cnf2 .= procexten("osdial","_87XXXXXX","3","AGI","agi-OSDagent_conf.agi,vaconf");
		$cnf2 .= procexten("osdial","_87XXXXXX","4","Hangup","");
		$cnf2 .= procexten("osdial","_Z87XXXXXX","1","AGI","agi-OSDagent_conf.agi,vamon");
		$cnf2 .= procexten("osdial","_Z87XXXXXX","2","Hangup","");
	}

	if (defined($osdial->{VARoldivr})) {
		$cnf2 .= procexten("osdial","487487","1","Playback","sip-silence");
		$cnf2 .= procexten("osdial","487487","2","AGI","agi-OSDivr-old.agi,\${EXTEN}");
		$cnf2 .= procexten("osdial","487487","3","Hangup","");
	}
	$cnf2 .= procexten("osdial","487488","1","Answer","");
	$cnf2 .= procexten("osdial","487488","2","Playback","sip-silence");
	$cnf2 .= procexten("osdial","487488","3","AGI","agi-OSDivr.agi,\${EXTEN}");
	$cnf2 .= procexten("osdial","487488","4","Hangup","");
	$cnf2 .= procexten("osdial","_487489.","1","ChanSpy",",g(\${EXTEN:6})qES");
	$cnf2 .= procexten("osdial","_487489.","2","Hangup","");
	$cnf .= ";\n; OSDIAL Virtual Agent Conferences\n";
	$cnf .= $cnf2;
	if (!$CLOrealtime>0) {
		$mtm .= ";\n; OSDIAL Virtual Agent Conferences\n";
		$mtm .= $mtm2;
	}

	my $extreload = "extensions reload";
	my $mmreload = "reload app_meetme.so";
	if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
		$extreload = "dialplan reload";
		$mmreload = "config reload /etc/asterisk/meetme.conf";
	}
	write_reload($cnf,'osdial_extensions_conferences',$extreload);
	if ($CLOrealtime>0) {
		my $rtmmcur = {};
		my $rtmmadd = {};
		my $rtmmdel = {};
		my $stmt = sprintf("SELECT confno FROM meetme WHERE confno!='8600' AND confno !='8601';");
		while (my $sret = $osdial->sql_query($stmt,'RT')) {
			$rtmmcur->{$sret->{'confno'}} = 1;
		}
		foreach my $mm (keys %{$rtmmcur}) {
			$rtmmdel->{$mm}=1 if (!defined($rtmtm->{$mm}));
		}
		foreach my $mm (keys %{$rtmtm}) {
			$rtmmadd->{$mm}=1 if (!defined($rtmmcur->{$mm}));
		}
		foreach my $mm (keys %{$rtmmdel}) {
			$osdial->sql_execute(sprintf("DELETE FROM meetme WHERE confno='%s';",$mm),'RT');
		}
		foreach my $mm (keys %{$rtmmadd}) {
			$osdial->sql_execute(sprintf("INSERT INTO meetme SET confno='%s';",$mm),'RT');
		}
	}
	write_reload($mtm,'osdial_meetme',$mmreload);
}

# Generate agent extensions, and sip/iax agent phones.
# (osdial_extensions_phones.conf osdial_sip_phones.conf osdial_iax_phones.conf)
sub gen_phones {
	my ($dcc,$dccdp) = @_;
	my $sphn = $achead;
	my $iphn = $achead;
	my $ephn = $achead;
	my $vphn = $achead;

	$ephn .= ";\n;Station Spying.\n";
	$ephn .= ";   6000 = monitoring, prompted\n;   6+agent_exten = monitoring, direct\n";
	$ephn .= ";   7000 = barge, prompted\n;   7+agent_exten = barge, direct\n";
	$ephn .= ";   9000 = whisper, prompted\n;   9+agent_exten = whisper, direct\n";
	if (!$CLOexpand) {
		$ephn .= procexten("osdial","_6XXX","1","AGI","agi-OSDstation_spy.agi");
		$ephn .= procexten("osdial","_7XXX","1","AGI","agi-OSDstation_spy.agi");
		$ephn .= procexten("osdial","_9XXX","1","AGI","agi-OSDstation_spy.agi");
	}

	my $oeofile = "/etc/asterisk/osdial_extensions_outbound.conf";
	$oeofile = $osdial->{PATHdocs} . "/conf_examples/osdial_extensions_outbound.conf" if (-s $oeofile < 250);
	my($oeodata);
	open(OEO, $oeofile);
	while (my $oeoline = <OEO>) {
		$oeodata .= $oeoline;
	}
	close(OEO);
	my $oeodataorig = $oeodata;
	if ($dcc) {
		$ephn .= ";\n;\n; The $dcc carrier was selected as the system default.\n";
		$ephn .= "; In order to override the extensions in osdial_extensions_outbound.conf, we must add them into this file.\n";
		$ephn .= ";\n";
		$ephn .= procexten("osdial","_011XXXXXXXX.","1","Goto",$dcc.",".$dccdp."\${EXTEN},1");
		$ephn .= procexten("osdial","_[89]011XXXXXXXX.","1","Goto",$dcc.",\${EXTEN},1");
		$ephn .= procexten("osdial","_00XXXXXXXX.","1","Goto",$dcc.",".$dccdp."\${EXTEN},1");
		$ephn .= procexten("osdial","_[89]00XXXXXXXX.","1","Goto",$dcc.",\${EXTEN},1");
		$ephn .= procexten("osdial","_NXXNXXXXXX","1","Goto",$dcc.",".$dccdp."1\${EXTEN},1");
		$ephn .= procexten("osdial","_1NXXNXXXXXX","1","Goto",$dcc.",".$dccdp."\${EXTEN},1");
		$ephn .= procexten("osdial","_[89]NXXNXXXXXX","1","Goto",$dcc.",\${EXTEN:0:1}1\${EXTEN:1},1");
		$ephn .= procexten("osdial","_[89]1NXXNXXXXXX","1","Goto",$dcc.",\${EXTEN},1");
		$ephn .= ";\n";
		$ephn .= ";\n";

		$oeodata =~ s/^exten/;DEFSEL;exten/gm; 
	} else {
		$oeodata =~ s/^;DEFSEL;exten/exten/gm; 
	}

	my $stmt = "SELECT * FROM phones WHERE protocol IN ('SIP','IAX2','Zap','DAHDI','EXTERNAL') AND active='Y' AND (";
	foreach my $ip (@myips) {
		$stmt .= " server_ip=\'" . $ip . "\' OR";
	}
	chop $stmt; chop $stmt; chop $stmt;
	$stmt .= ');';
	print $stmt . "\n" if ($DB);
	while (my $sret = $osdial->sql_query($stmt)) {
		$sret->{ext_context} = 'osdial' if ($sret->{ext_context} eq "");
		$sret->{outbound_cid} = $sret->{dialplan_number} if ($sret->{outbound_cid} eq "");
		$sret->{outbound_cid} =~ s/[^0-9]//g;
		$sret->{outbound_cid} = "0000000000" if ($sret->{outbound_cid} eq "");
		$sret->{outbound_cid_name} = $sret->{fullname} if ($sret->{outbound_cid_name} eq "");
		$sret->{outbound_cid_name} =~ s/[^0-9a-zA-Z\ \.\-\_]//g;
		$sret->{outbound_cid_name} = "Unknown" if ($sret->{outbound_cid_name} eq "");
		if ($sret->{protocol} eq "SIP" and $sret->{extension} !~ /\@/) {
			$sphn .= ";\n[". $sret->{extension} ."]\n";
			$sphn .= "type=friend\n";
			$sphn .= "username=" . $sret->{extension} . "\n";
			$sphn .= "secret=" . $sret->{pass} . "\n";
			$sphn .= "callerid=\"" . $sret->{outbound_cid_name} . "\" <" . $sret->{outbound_cid} . ">\n";
			if ($sret->{phone_ip}) {
				$sphn .= "host=" . $sret->{phone_ip} . "\n";
			} else {
				$sphn .= "host=dynamic\n";
			}
			$sphn .= "dtmfmode=auto\n";
			$sphn .= "relaxdtmf=yes\n";
			$sphn .= "disallow=all\n";
			$sphn .= "allow=ulaw\n";
			$sphn .= "allow=gsm\n";
			$sphn .= "allow=g729\n";
			$sphn .= "qualify=5000\n";
			$sphn .= "nat=yes\n" if ($sret->{phone_type} =~ /NAT/i);
			$sphn .= "context=" . $sret->{ext_context} . "\n";
			$sphn .= "mailbox=" . $sret->{voicemail_id} . "\@osdial\n" if ($sret->{voicemail_id});
		} elsif ($sret->{protocol} eq "IAX2" and $sret->{extension} !~ /\@|\//) {
			$iphn .= ";\n[". $sret->{extension} ."]\n";
			$iphn .= "type=friend\n";
			$iphn .= "username=" . $sret->{extension} . "\n";
			$iphn .= "secret=" . $sret->{pass} . "\n";
			$iphn .= "callerid=\"" . $sret->{outbound_cid_name} . "\" <" . $sret->{outbound_cid} . ">\n";
			if ($sret->{phone_ip}) {
				$iphn .= "host=" . $sret->{phone_ip} . "\n";
			} else {
				$iphn .= "host=dynamic\n";
			}
			$iphn .= "disallow=all\n";
			$iphn .= "allow=ulaw\n";
			$iphn .= "allow=gsm\n";
			$iphn .= "allow=g729\n";
			$iphn .= "qualify=5000\n";
			$iphn .= "requirecalltoken=no\n";
			$iphn .= "nat=yes\n" if ($sret->{phone_type} =~ /NAT/i);
			$iphn .= "context=" . $sret->{ext_context} . "\n";
			$iphn .= "mailbox=" . $sret->{voicemail_id} . "\@osdial\n" if ($sret->{voicemail_id});
		}
		$vphn .= $sret->{voicemail_id} . ' => ' . $sret->{voicemail_password} . ',' . $sret->{fullname} . ',' . $sret->{voicemail_email} . ',,' . "\n";
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
			if ($sret->{voicemail_id} ne "") {
				$ephn .= procexten("osdial",$sret->{dialplan_number},"1","Dial",$dext.",30,o");
				$ephn .= procexten("osdial",$sret->{dialplan_number},"2","GotoIf","\$[\"\${DIALSTATUS}\" = \"NOANSWER\"|\"\${DIALSTATUS}\" = \"BUSY\"|\"\${DIALSTATUS}\" = \"CONGESTION\"|\"\${DIALSTATUS}\" = \"CHANUNAVAIL\"]?3:4");
				$ephn .= procexten("osdial",$sret->{dialplan_number},"3","Voicemail",$sret->{voicemail_id}."\@osdial");
				$ephn .= procexten("osdial",$sret->{dialplan_number},"4","Hangup","");
			} else {
				$ephn .= procexten("osdial",$sret->{dialplan_number},"1","Dial",$dext.",60,o");
				$ephn .= procexten("osdial",$sret->{dialplan_number},"2","Hangup","");
			}
			$oblock .= procexten("osdialBLOCK",$sret->{dialplan_number},"1","Goto","osdial,\${EXTEN},1");
		}
		if ($CLOexpand) {
			foreach my $prenum (qw(6 7 9)) {
				$ephn .= procexten("osdial",$prenum.$sret->{login},"1","AGI","agi-OSDstation_spy.agi");
				$oext .= procexten("osdialEXT",$prenum.$sret->{login},"1","Goto","osdial,\${EXTEN},1");
			}
		}
	}

	my $extreload = "extensions reload";
	if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
		$extreload = "dialplan reload";
	}
	write_reload($sphn,'osdial_sip_phones','sip reload');
	write_reload($iphn,'osdial_iax_phones','iax2 reload');
	write_reload($ephn,'osdial_extensions_phones',$extreload);
	if ($oeodataorig ne $oeodata) {
		write_reload($oeodata,'osdial_extensions_outbound',$extreload);
	}
	write_reload($vphn,'osdial_voicemail','voicemail reload');
}

sub gen_osdial_extensions {
	my $oeout=$achead;

	# Build ARI VM context
	my $ari='';
	my $incoming='';
	my $ocore='';
	my $oblock='';
	my $ctx={};
	my $stmt = "SELECT oe.ext_context,oe.exten,oed.ext_priority,oed.ext_app,oed.ext_appdata,ext_type,ivr_id FROM osdial_extensions AS oe LEFT JOIN osdial_extensions_data AS oed ON oed.exten_id=oe.id ORDER BY oe.ext_context ASC, (LPAD(oe.exten,20,' ')) ASC, oed.ext_priority ASC;";
	while (my $sret = $osdial->sql_query($stmt)) {
		if ($sret->{ext_type} eq 'DIALPLAN') {
			$ctx->{$sret->{ext_context}} .= procexten($sret->{ext_context}, $sret->{exten}, $sret->{ext_priority}, $sret->{ext_app}, $sret->{ext_appdata});
		} elsif ($sret->{ext_type} eq 'IVR') {
			$ctx->{$sret->{ext_context}} .= procexten($sret->{ext_context}, $sret->{exten}, '1', 'AGI', 'agi-OSDivr.agi,${EXTEN},'.$sret->{ivr_id});
		}
	}
	$ari = $ctx->{'osdial_arivmcall'};
	$incoming = $ctx->{'incoming'};
	$oblock = $ctx->{'osdialBLOCK'};
	$ocore = $ctx->{'osdial'};
	$oext = $ctx->{'osdialEXT'};

	# Build TTS extensions.
	my $stmt = "SELECT * FROM osdial_tts WHERE extension!='';";
	while (my $sret = $osdial->sql_query($stmt)) {
		$ocore .= "; Text-to-Speech Extension\n";
		$ocore .= procexten("osdial",$sret->{extension},"1","Playback","sip-silence");
		$ocore .= procexten("osdial",$sret->{extension},"2","AGI","agi-OSDtts.agi,\${EXTEN}");
		$ocore .= procexten("osdial",$sret->{extension},"3","Hangup","");
		$oblock .= procexten("osdialBLOCK",$sret->{extension},"1","Goto","osdial,\${EXTEN},1");
		$ocore .= ";\n";
	}

	# Get custom build media file extensions.
	my $stmt = "SELECT * FROM osdial_media WHERE extension!='' AND extension NOT LIKE '8510____';";
	while (my $sret = $osdial->sql_query($stmt)) {
		$sret->{filename} =~ s/\..*$//;
		$ocore .= "; Media Extension\n";
		$ocore .= procexten("osdial",$sret->{extension},"1","Playback",$sret->{filename});
		$ocore .= procexten("osdial",$sret->{extension},"2","Hangup","");
		$oblock .= procexten("osdialBLOCK",$sret->{extension},"1","Goto","osdial,\${EXTEN},1");
		$ocore .= ";\n";
	}

	$oeout .= "#include osdial_extensions_carriers.conf\n\n";
	$oeout .= "[incoming]\n";
	$oeout .= $incoming."\n";
	$oeout .= "[osdialBLOCK]\n";
	$oeout .= $oblock."\n";
	$oeout .= "[osdialEXT]\n";
	$oeout .= $oext."\n";
	$oeout .= "[osdial_arivmcall]\n";
	$oeout .= "switch => Realtime/osdial_arivmcall\@extensions/p\n" if($CLOrealtime>0);
	$oeout .= $ari."\n";
	$oeout .= "[osdial-Switch]\n" if($CLOrealtime>0);
	$oeout .= "switch => Realtime/osdial\@extensions/p\n\n" if($CLOrealtime>0);
	$oeout .= "[osdial-Patterns]\n";
	$oeout .= "#include osdial_extensions_phones.conf\n";
	$oeout .= "#include osdial_extensions_outbound.conf\n";
	$oeout .= "#include osdial_extensions_inbound.conf\n";
	$oeout .= "#include osdial_extensions_servers.conf\n";
	$oeout .= "#include osdial_extensions_conferences.conf\n";
	$oeout .= "#include osdial_extensions_testing.conf\n";
	$oeout .= "#include osdial_extensions_custom.conf\n";
	$oeout .= ";\n; Prefixing any extension # appending to 8307 will be played as a file.\n";
	$oeout .= procexten("osdial-Patterns","_8307.","1","Answer","");
	$oeout .= procexten("osdial-Patterns","_8307.","2","Playback","\${EXTEN:4}");
	$oeout .= procexten("osdial-Patterns","_8307.","3","Hangup","");
	$oeout .= ";\n; this is used for playing a message to an answering machine forwarded from AMD in OSDIAL\n";
	$oeout .= ";    any extension # appending to 8320 will be played as a file.\n";
	$oeout .= procexten("osdial-Patterns","_8320.","1","WaitForSilence","1000,2,20");
	$oeout .= procexten("osdial-Patterns","_8320.","2","Playback","\${EXTEN:4}");
	$oeout .= procexten("osdial-Patterns","_8320.","3","AGI","agi-OSDamd_post.agi,\${EXTEN}");
	$oeout .= procexten("osdial-Patterns","_8320.","4","Hangup","");
	$oeout .= ";\n; playback of recorded prompts\n";
	$oeout .= procexten("osdial-Patterns","_851XXXXX","1","Answer","");
	$oeout .= procexten("osdial-Patterns","_851XXXXX","2","Playback","\${EXTEN}");
	$oeout .= procexten("osdial-Patterns","_851XXXXX","3","Hangup","");
	$oeout .= ";\n; playback of recorded prompts, after waiting for silence.\n";
	$oeout .= ";exten => _8851XXXXX,1,Wait(10)\n";
	$oeout .= procexten("osdial-Patterns","_8851XXXXX","1","WaitForSilence","1000,2,20");
	$oeout .= procexten("osdial-Patterns","_8851XXXXX","2","Playback","\${EXTEN:1}");
	$oeout .= procexten("osdial-Patterns","_8851XXXXX","3","AGI","agi-OSDamd_post.agi,\${EXTEN}");
	$oeout .= procexten("osdial-Patterns","_8851XXXXX","4","Hangup","");
	$oeout .= ";\n; this is used to allow the GUI to send live calls directly into voicemail\n";
	$oeout .= ";     don't forget to set GUI variable \$voicemail_dump_exten to this extension\n";
	$oeout .= procexten("osdial-Patterns","_85026666666666.","1","AGI","agi-OSDvmail_finder.agi,\${EXTEN:14},85027777777777");
	$oeout .= procexten("osdial-Patterns","_85027777777777.","1","Wait","2");
	$oeout .= procexten("osdial-Patterns","_85027777777777.","2","Voicemail","\${EXTEN:14}\@osdial,u");
	$oeout .= procexten("osdial-Patterns","_85027777777777.","3","Hangup","");
	$oeout .= ";\n; Other Voicemail Reroutes\n";
	$oeout .= procexten("osdial-Patterns","_8502XXXX","1","AGI","agi-OSDvmail_finder.agi,\${EXTEN:4},8503XXXX");
	$oeout .= procexten("osdial-Patterns","_8503XXXX","1","Voicemail","\${EXTEN:4}\@osdial");
	$oeout .= ";\n; Fix CXFER.\n";
	$oeout .= procexten("osdial-Patterns","_860XXXX*.","1","AGI","agi-OSDfixCXFER.agi");
	$oeout .= procexten("osdial-Patterns","_7860XXXX*.","1","AGI","agi-OSDfixCXFER.agi");
	$oeout .= ";\n; inbound OSDIAL transfer calls\n";
	$oeout .= procexten("osdial-Patterns","_90009.","1","Answer","");
	$oeout .= procexten("osdial-Patterns","_90009.","2","Playback","sip-silence");
	$oeout .= procexten("osdial-Patterns","_90009.","3","Set",'IVR_UNIQUEID=${UNIQUEID}');
	$oeout .= procexten("osdial-Patterns","_90009.","4","Set",'IVR_CHANNEL=${CHANNEL}');
	$oeout .= procexten("osdial-Patterns","_90009.","5","Set",'IVR_CONTEXT=${CONTEXT}');
	$oeout .= procexten("osdial-Patterns","_90009.","6","Set",'IVR_ACCOUNTCODE=${CDR(accountcode)}');
	$oeout .= procexten("osdial-Patterns","_90009.","7","Set",'IVR_PRIORITY=${PRIORITY}');
	$oeout .= procexten("osdial-Patterns","_90009.","8","Set",'IVR_EXTENSION=${EXTEN}');
	$oeout .= procexten("osdial-Patterns","_90009.","9","Set",'IVR_CALLERIDNAME=${CALLERID(name)}');
	$oeout .= procexten("osdial-Patterns","_90009.","10","Set",'IVR_CALLERID=${CALLERID(num)}');
	$oeout .= procexten("osdial-Patterns","_90009.","11","Set",'IVR_ARGS=CLOSER-----LO-----CL_TESTCAMP-----7275551212-----Closer-----park----------999-----1');
	$oeout .= procexten("osdial-Patterns","_90009.","12","AGI",'agi://127.0.0.1:4577/call_log');
	$oeout .= procexten("osdial-Patterns","_90009.","13","ExternalIVR","/var/lib/asterisk/agi-bin/ivr-OSDinbound.pl(1)");
	$oeout .= procexten("osdial-Patterns","_90009.","14","GotoIf",'$["${IVR_GOTO}" = "1"]?15:16');
	$oeout .= procexten("osdial-Patterns","_90009.","15","Goto",'${IVR_CONTEXT},${IVR_EXTEN},${IVR_PRIORITY}');
	$oeout .= procexten("osdial-Patterns","_90009.","16","Hangup","");
	$oeout .= procexten("osdial-Patterns","_990009.","1","Answer","");
	$oeout .= procexten("osdial-Patterns","_990009.","2","Playback","sip-silence");
	$oeout .= procexten("osdial-Patterns","_990009.","3","Set",'IVR_UNIQUEID=${UNIQUEID}');
	$oeout .= procexten("osdial-Patterns","_990009.","4","Set",'IVR_CHANNEL=${CHANNEL}');
	$oeout .= procexten("osdial-Patterns","_990009.","5","Set",'IVR_CONTEXT=${CONTEXT}');
	$oeout .= procexten("osdial-Patterns","_990009.","6","Set",'IVR_ACCOUNTCODE=${CDR(accountcode)}');
	$oeout .= procexten("osdial-Patterns","_990009.","7","Set",'IVR_PRIORITY=${PRIORITY}');
	$oeout .= procexten("osdial-Patterns","_990009.","8","Set",'IVR_EXTENSION=${EXTEN}');
	$oeout .= procexten("osdial-Patterns","_990009.","9","Set",'IVR_CALLERIDNAME=${CALLERID(name)}');
	$oeout .= procexten("osdial-Patterns","_990009.","10","Set",'IVR_CALLERID=${CALLERID(num)}');
	$oeout .= procexten("osdial-Patterns","_990009.","11","Set",'IVR_ARGS=CLOSER-----LO-----CL_TESTCAMP-----7275551212-----Closer-----park----------999-----1');
	$oeout .= procexten("osdial-Patterns","_990009.","12","AGI",'agi://127.0.0.1:4577/call_log');
	$oeout .= procexten("osdial-Patterns","_990009.","13","ExternalIVR","/var/lib/asterisk/agi-bin/ivr-OSDinbound.pl(1)");
	$oeout .= procexten("osdial-Patterns","_990009.","14","GotoIf",'$["${IVR_GOTO}" = "1"]?15:16');
	$oeout .= procexten("osdial-Patterns","_990009.","15","Goto",'${IVR_CONTEXT},${IVR_EXTEN},${IVR_PRIORITY}');
	$oeout .= procexten("osdial-Patterns","_990009.","16","Hangup","");

	#$oeout .= procexten("osdial-Patterns","_90009.","1","Answer","");
	#$oeout .= procexten("osdial-Patterns","_90009.","2","AGI","agi-VDAD_ALL_inbound.agi,CLOSER-----LO-----CL_TESTCAMP-----7275551212-----Closer-----park----------999-----1");
	#$oeout .= procexten("osdial-Patterns","_90009.","3","Hangup","");
	#$oeout .= procexten("osdial-Patterns","_990009.","1","Answer","");
	#$oeout .= procexten("osdial-Patterns","_990009.","2","AGI","agi-VDAD_ALL_inbound.agi,CLOSER-----LO-----CL_TESTCAMP-----7275551212-----Closer-----park----------999-----1");
	#$oeout .= procexten("osdial-Patterns","_990009.","3","Hangup","");
	$oeout .= "\n";
	$oeout .= "[osdial]\n";
	$oeout .= "include => osdial-Switch\n" if($CLOrealtime>0);
	$oeout .= "include => osdial-Patterns\n";
	$oeout .= $ocore."\n" if(!$CLOrealtime>0);

	my $extreload = "extensions reload";
	if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
		$extreload = "dialplan reload";
	}
	write_reload($oeout,'osdial_extensions',$extreload);
}


sub gen_carriers {
	my $sip_config=$achead;
	my $iax_config=$achead;
	my $sip_registrations='';
	my $iax_registrations='';
	my $dialplan=$achead;

	my $default_carrier_context;
	my $default_carrier_prefix;
	my $carriers = {};
	my $stmt = "SELECT * FROM osdial_carriers WHERE active='Y';";
	while (my $carrier = $osdial->sql_query($stmt)) {
		$carriers->{$carrier->{id}} = $carrier;
	}
	foreach my $carrier (sort keys %{$carriers}) {
		$default_carrier_context = 'OOUT' . $carriers->{$carrier}{name} if ($osdial->{settings}{default_carrier_id}==$carrier);
		$default_carrier_prefix = $carriers->{$carrier}{default_prefix} if ($osdial->{settings}{default_carrier_id}==$carrier);
		# Override server with server specific options if set.
		my $stmt = sprintf("SELECT * FROM osdial_carrier_servers WHERE carrier_id='\%s' AND server_ip='\%s';",$carrier,$osdial->{VARserver_ip});
		while (my $carrier_server = $osdial->sql_query($stmt)) {
			$carriers->{$carrier}{protocol_config} = $carrier_server->{protocol_config} if ($carrier_server->{protocol_config} ne '');
			$carriers->{$carrier}{registrations} =   $carrier_server->{registrations}   if ($carrier_server->{registrations} ne '');
			$carriers->{$carrier}{dialplan} =        $carrier_server->{dialplan}        if ($carrier_server->{dialplan} ne '');
		}
		$carriers->{$carrier}{protocol_config} =~ s/\r\n/\n/gm;
		$carriers->{$carrier}{registrations} =~ s/\r\n/\n/gm;
		$carriers->{$carrier}{dialplan} =~ s/\r\n/\n/gm;

		my $context = 'OIN' . $carriers->{$carrier}{name};

		# Add contexts to protocol_config for inbound calls.
		$carriers->{$carrier}{protocol_config} =~ s/^\[(.*)\]$/\[$1\]\ncontext=$context/mg;

		# Do the variable substitutions.
		$carriers->{$carrier}{dialplan} =~ s/<NAME>/$carriers->{$carrier}{name}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<PROTOCOL>/$carriers->{$carrier}{protocol}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<STRIP_MSD>/$carriers->{$carrier}{strip_msd}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<ALLOW_INTERNATIONAL>/$carriers->{$carrier}{allow_international}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<DEFAULT_CALLERID>/$carriers->{$carrier}{default_callerid}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<DEFAULT_AREACODE>/$carriers->{$carrier}{default_areacode}/mg;
		$carriers->{$carrier}{dialplan} =~ s/<DEFAULT_PREFIX>/$carriers->{$carrier}{default_prefix}/mg;

		# Separate the configuration based on the protocol.
		if ($carriers->{$carrier}{protocol} eq "SIP") {
			$sip_config .= $carriers->{$carrier}{protocol_config} . "\n\n";
			foreach my $regstr (split/\n/,$carriers->{$carrier}{registrations}) {
				$sip_registrations .= 'register => ' . $regstr . "\n\n" if ($regstr ne '');
			}
		} elsif ($carriers->{$carrier}{protocol} eq "IAX2") {
			$iax_config .= $carriers->{$carrier}{protocol_config} . "\n\n";
			foreach my $regstr (split/\n/,$carriers->{$carrier}{registrations}) {
				$iax_registrations .= 'register => ' . $regstr . "\n\n" if ($regstr ne '');
			}
		}

		# Create Outbound dialplan for the carrier.
		$dialplan .= '';
		$dialplan .= "[OOUT" . $carriers->{$carrier}{name} . "-Switch]\n" if($CLOrealtime>0);
		$dialplan .= "switch => Realtime/OOUT".$carriers->{$carrier}{name}."\@extensions/p\n\n" if($CLOrealtime>0);
		$dialplan .= "[OOUT" . $carriers->{$carrier}{name} . "-Patterns]\n";

		# Create failover dialplan, which will attempt another carrier based on the DIALSTATUS.
		my $failover = '';
		if (!$carriers->{$carrier}{failover_id}>0) {
			$failover .= procexten("OOUT".$carriers->{$carrier}{name},"_failover.","1","Hangup","");
		} else {
			my $stmt = sprintf("SELECT * FROM osdial_carriers WHERE id='\%s';",$carriers->{$carrier}{failover_id});
			my $failover_carrier = $osdial->sql_query($stmt);
			if ($carriers->{$carrier}{failover_condition} eq 'CHANUNAVAIL') {
				$failover .= procexten("OOUT".$carriers->{$carrier}{name},"_failover.","1","GotoIf","\$[\"\${DIALSTATUS}\" = \"CHANUNAVAIL\"]?2:4");
			} elsif ($carriers->{$carrier}{failover_condition} eq 'CONGESTION') {
				$failover .= procexten("OOUT".$carriers->{$carrier}{name},"_failover.","1","GotoIf","\$[\"\${DIALSTATUS}\" = \"CONGESTION\"]?2:4");
			} elsif ($carriers->{$carrier}{failover_condition} eq 'BOTH') {
				$failover .= procexten("OOUT".$carriers->{$carrier}{name},"_failover.","1","GotoIf","\$[\"\${DIALSTATUS}\" = \"CHANUNAVAIL\"|\"\${DIALSTATUS}\" = \"CONGESTION\"]?2:4");
			}
			$failover .= procexten("OOUT".$carriers->{$carrier}{name},"_failover.","2","AGI","agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----\${HANGUPCAUSE}-----\${DIALSTATUS}-----\${DIALEDTIME}-----\${ANSWEREDTIME}");
			$failover .= procexten("OOUT".$carriers->{$carrier}{name},"_failover.","3","Goto","OOUT".$failover_carrier->{name}.",\${EXTEN:8},1");
			$failover .= procexten("OOUT".$carriers->{$carrier}{name},"_failover.","4","Hangup","");
		}

		$dialplan .= $failover;
		$dialplan .= $carriers->{$carrier}{dialplan} . "\n";
		$dialplan .= procexten("OOUT".$carriers->{$carrier}{name},"h","1","AGI","agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----\${HANGUPCAUSE}-----\${DIALSTATUS}-----\${DIALEDTIME}-----\${ANSWEREDTIME}");

		$dialplan .= "[OOUT" . $carriers->{$carrier}{name} . "]\n";
		$dialplan .= "include => OOUT" . $carriers->{$carrier}{name} . "-Switch\n" if($CLOrealtime>0);
		$dialplan .= "include => OOUT" . $carriers->{$carrier}{name} . "-Patterns\n";
		$dialplan .= "\n\n";

		# Create Inbound dialplan for the carrier, ie DIDs..
		$dialplan .= "[" . $context . "-Switch]\n" if($CLOrealtime>0);
		$dialplan .= "switch => Realtime/".$context."\@extensions/p\n\n" if($CLOrealtime>0);
		$dialplan .= "[" . $context . "-Patterns]\n";
		my %didchk;
		my $dids = {};
		my $stmt = sprintf("SELECT * FROM osdial_carrier_dids WHERE carrier_id='\%s';",$carrier);
		while (my $did = $osdial->sql_query($stmt)) {
			$dids->{$did->{did}} = $did;
		}
		foreach my $did (sort keys %{$dids}) {
			my $didmatch = '';
			$didmatch = '_' if ($dids->{$did}{did} =~ /X|Z|N|\[|\]|\.|\!/ and $dids->{$did}{did} !~ /^_/);
			if (!defined $didchk{$didmatch.$dids->{$did}{did}}) {
				$didchk{$didmatch.$dids->{$did}{did}} = 1;
				my $prio=1;
				$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"AGI","agi://127.0.0.1:4577/call_log");
				if ($dids->{$did}{did_action} eq 'INGROUP') {
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Answer","");
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Playback","sip-silence");
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_UNIQUEID=${UNIQUEID}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_CHANNEL=${CHANNEL}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_CONTEXT=${CONTEXT}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_ACCOUNTCODE=${CDR(accountcode)}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_PRIORITY=${PRIORITY}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_EXTENSION=${EXTEN}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_CALLERIDNAME=${CALLERID(name)}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_CALLERID=${CALLERID(num)}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Set",'IVR_ARGS='.$dids->{$did}{lookup_method}."-----".$dids->{$did}{server_allocation}
					."-----".$dids->{$did}{ingroup}."-----\${EXTEN}-----\${CALLERID(number)}-----".$dids->{$did}{park_file}."-----".$dids->{$did}{initial_status}."-----".$dids->{$did}{default_list_id}
					."-----".$dids->{$did}{default_phone_code}."-----".$dids->{$did}{search_campaign});
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"ExternalIVR","/var/lib/asterisk/agi-bin/ivr-OSDinbound.pl(1)");
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"GotoIf",'$["${IVR_GOTO}" = "1"]?15:16');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Goto",'${IVR_CONTEXT},${IVR_EXTEN},${IVR_PRIORITY}');
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Hangup","");
					#$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"AGI","agi-VDAD_ALL_inbound.agi,".$dids->{$did}{lookup_method}."-----".$dids->{$did}{server_allocation}
					#."-----".$dids->{$did}{ingroup}."-----\${EXTEN}-----\${CALLERID(number)}-----".$dids->{$did}{park_file}."-----".$dids->{$did}{initial_status}."-----".$dids->{$did}{default_list_id}
					#."-----".$dids->{$did}{default_phone_code}."-----".$dids->{$did}{search_campaign});
				} elsif ($dids->{$did}{did_action} eq 'PHONE') {
					my $stmt = sprintf("SELECT * FROM phones WHERE extension='\%s' LIMIT 1;",$dids->{$did}{phone});
					while (my $phone = $osdial->sql_query($stmt)) {
						my @sip = split /\./, $phone->{server_ip};
						my $fsip = sprintf('%.3d*%.3d*%.3d*%.3d',@sip);
						my $isp='*';
						$isp='#' if ($osdial->{settings}{intra_server_protocol} eq 'IAX2');
						$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Goto","osdial,".$fsip.$isp.$phone->{dialplan_number}.",1");
					}
				} elsif ($dids->{$did}{did_action} eq 'EXTENSION') {
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Goto",$dids->{$did}{extension_context}.",".$dids->{$did}{extension}.",1");
				} elsif ($dids->{$did}{did_action} eq 'VOICEMAIL') {
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Voicemail",$dids->{$did}{voicemail});
				}
				$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"Hangup","");
			}
		}
		# Display an alert about any unhandled DIDs.
		if (!defined $didchk{'_X.'}) {
			#$dialplan .= procexten($context,"_X.","1","AGI","agi://127.0.0.1:4577/call_log");
			$dialplan .= procexten($context,"_X.","1","NoOp","***ALERT***  Unconfigured DID:\${EXTEN} from Carrier:".$carriers->{$carrier}{name});
			$dialplan .= procexten($context,"_X.","2","Hangup","17");
		}
		if (!defined $didchk{'h'}) {
			$dialplan .= procexten($context,"h","1","AGI","agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----\${HANGUPCAUSE}-----\${DIALSTATUS}-----\${DIALEDTIME}-----\${ANSWEREDTIME}");
		}
		$dialplan .= "\n\n";
		$dialplan .= "[" . $context . "]\n";
		$dialplan .= "include => ".$context."-Switch\n" if($CLOrealtime>0);
		$dialplan .= "include => ".$context."-Patterns\n\n";
		

	}
	my $extreload = "extensions reload";
	if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
		$extreload = "dialplan reload";
	}
	write_reload($sip_config,'osdial_sip_carriers','sip reload');
	write_reload($iax_config,'osdial_iax_carriers','iax2 reload');
	write_reload($dialplan,'osdial_extensions_carriers',$extreload);
	return ($sip_registrations, $iax_registrations, $default_carrier_context, $default_carrier_prefix);
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
			$reloads{$reload} = 1 if ($reload ne '');
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

sub procexten {
	my ($context,$exten,$priority,$app,$appdata) = @_;
	my $extout = '';
	if ($exten!~/^_/ and $CLOrealtime>0) {
		$realtime_ext->{$context}{$exten}{sprintf('%d',$priority)} = {'app'=>$app, 'appdata' => $appdata};
	} else {
		$extout .= sprintf("exten => %s,%s,%s(%s)\n",$exten,$priority,$app,$appdata);
	}
	return $extout;
}
