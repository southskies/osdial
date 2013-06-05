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
$CLOexpand=1 if ($CLOrealtime);
$CLOrealtime=0 if ($CLOrealtime_off);

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
	$oedata =~ s/^TRUNKloop.*$/TRUNKloop = IAX2\/ASTloop:$pass\@127.0.0.1:40569/m;
	$oedata =~ s/^TRUNKblind.*$/TRUNKblind = IAX2\/ASTblind:$pass\@127.0.0.1:41569/m;
	$oedata = "TRUNKblind = IAX2\/ASTblind:$pass\@127.0.0.1:41569\n" . $oedata unless ($oedata =~ /^TRUNKblind.*$/m);
	$oedata = "TRUNKloop = IAX2\/ASTloop:$pass\@127.0.0.1:40569\n"   . $oedata unless ($oedata =~ /^TRUNKloop.*$/m);
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


	foreach my $reload (keys %reloads) {
		print "    Executing " . $reload . "...\n" unless ($CLOquiet);
		`/usr/sbin/asterisk -rx "$reload" > /dev/null 2>&1`;
		sleep 5;
	}

	if (scalar keys %{$realtime_ext} > 0) {
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
		$osdial->sql_connect('RT',$sqlparams->{$rtdb}{'dbname'},$sqlparams->{$rtdb}{'dbhost'},$sqlparams->{$rtdb}{'dbport'},$sqlparams->{$rtdb}{'dbuser'},$sqlparams->{$rtdb}{'dbpass'});

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

		procexten("osdialEXT","_".$fsip."*.","1","Goto","osdial,\${EXTEN:16},1");
		procexten("osdialEXT","_".$fsip."#.","1","Goto","osdial,\${EXTEN:16},1");
		procexten("osdialEXT","_".$fsip2.".","1","Goto","osdial,\${EXTEN:12},1");

		procexten("osdialBLOCK","_".$fsip."*.","1","Goto","osdial,\${EXTEN:16},1");
		procexten("osdialBLOCK","_".$fsip."#.","1","Goto","osdial,\${EXTEN:16},1");
		procexten("osdialBLOCK","_".$fsip2.".","1","Goto","osdial,\${EXTEN:12},1");

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

		procexten("osdialEXT","_" . $fsip . "*.","1","Goto","osdial,\${EXTEN},1");
		procexten("osdialEXT","_" . $fsip . "#.","1","Goto","osdial,\${EXTEN},1");
		procexten("osdialBLOCK","_" . $fsip . "*.","1","Goto","osdial,\${EXTEN},1");
		procexten("osdialBLOCK","_" . $fsip . "#.","1","Goto","osdial,\${EXTEN},1");

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
	$mtm .= "conf => 8600\nconf => 8601,1234\n";
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
		$cnf .= procexten("osdial","_0860XXXX","1","Dial","\${TRUNKblind}/6\${EXTEN:1},55,o");
		$cnf .= procexten("osdial","_0860XXXX","2","Hangup","");
		$cnf .= procexten("osdial","_0X860XXXX","1","Dial","\${TRUNKblind}/\${EXTEN:1},55,o");
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
								$cnf2 .= procexten("osdial",$prenum.$prenum2.$sret->{conf_exten},"1","Dial","\${TRUNKblind}/6\${EXTEN:1},55,o");
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
					$cnf2 .= procexten("osdial","0".$sret->{conf_exten},"1","Dial","\${TRUNKblind}/6\${EXTEN:1},55,o");
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
		$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
	}
	if (!$CLOexpand) {
		$cnf2 .= procexten("osdial","_8600XXX","1","AGI","agi-OSDagent_conf.agi,genconf");
		$cnf2 .= procexten("osdial","_8600XXX","2","Hangup","");
	}

	$cnf .= ";\n; OSDial Conferences $cf - $cl\n";
	$cnf .= $cnf2;
	$mtm .= ";\n; OSDial Conferences $cf - $cl\n";
	$mtm .= $mtm2;

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
								$cnf2 .= procexten("osdial",$prenum.$prenum2.$sret->{conf_exten},"1","Dial","\${TRUNKblind}/6\${EXTEN:1},55,o");
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
					$cnf2 .= procexten("osdial","0".$sret->{conf_exten},"1","Dial","\${TRUNKblind}/6\${EXTEN:1},55,o");
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
		$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
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
	$mtm .= ";\n; OSDIAL Agent Conferences $cf - $cl\n";
	$mtm .= $mtm2;

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
		$mtm2 .= "conf => " . $sret->{conf_exten} . "\n";
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
	$mtm .= ";\n; OSDIAL Virtual Agent Conferences\n";
	$mtm .= $mtm2;

	my $extreload = "extensions reload";
	my $mmreload = "reload app_meetme.so";
	if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
		$extreload = "dialplan reload";
		$mmreload = "config reload /etc/asterisk/meetme.conf";
	}
	write_reload($cnf,'osdial_extensions_conferences',$extreload);
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
	$ephn .= procexten("osdial","6000","1","AGI","agi-OSDstation_spy_prompted.agi");
	$ephn .= procexten("osdial","7000","1","AGI","agi-OSDstation_spy_prompted.agi");
	$ephn .= procexten("osdial","9000","1","AGI","agi-OSDstation_spy_prompted.agi");
	$ephn .= procexten("osdialEXT","6000","1","Goto","osdial,\${EXTEN},1");
	$ephn .= procexten("osdialEXT","7000","1","Goto","osdial,\${EXTEN},1");
	$ephn .= procexten("osdialEXT","9000","1","Goto","osdial,\${EXTEN},1");
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
			procexten("osdialEXT",$sret->{dialplan_number},"1","Goto","osdial,\${EXTEN},1");
			procexten("osdialBLOCK",$sret->{dialplan_number},"1","Goto","osdial,\${EXTEN},1");
		}
		if ($CLOexpand) {
			foreach my $prenum (qw(6 7 9)) {
				$ephn .= procexten("osdial",$prenum.$sret->{login},"1","AGI","agi-OSDstation_spy.agi");
				procexten("osdialEXT",$prenum.$sret->{login},"1","Goto","osdial,\${EXTEN},1");
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
	my $ephn = $achead;

	# Build ARI VM context
	procexten("osdial_arivmcall","s","1","Answer","");
	procexten("osdial_arivmcall","s","2","Wait","1");
	procexten("osdial_arivmcall","s","3","Background",'${MSG}');
	procexten("osdial_arivmcall","s","4","Background",'silence/2');
	procexten("osdial_arivmcall","s","5","Background",'vm-repeat');
	procexten("osdial_arivmcall","s","6","Background",'vm-starmain');
	procexten("osdial_arivmcall","s","7","WaitExten","15");
	procexten("osdial_arivmcall","5","1","Goto","osdial_arivmcall,s,3");
	procexten("osdial_arivmcall","#","1","Playback","vm-goodbye");
	procexten("osdial_arivmcall","#","2","Hangup","");
	procexten("osdial_arivmcall","*","1","Set",'VMCONTEXT=${DB(AMPUSER/${MBOX}/voicemail)}');
	procexten("osdial_arivmcall","*","2","VoiceMailMain",'${MBOX}@${VMCONTEXT},s');
	procexten("osdial_arivmcall","i","1","Playback","pm-invalid-option");
	procexten("osdial_arivmcall","i","2","Goto","osdial_arivmcall,s,3");
	procexten("osdial_arivmcall","t","1","Playback","vm-goodbye");
	procexten("osdial_arivmcall","t","2","Hangup","");
	procexten("osdial_arivmcall","h","1","Hangup","");
	procexten("osdial_arivmcall","s-ANSWER","1","Noop","Call successfully answered - Hanging up now");

	# Static, non-pattern goto's for osdialEXT and osdialBLOCK
	foreach my $pexten (qw(# t i h 9 43 *97 *98 8500998 9998 9999 487487 487488 99999999999 999999999999)) {
		procexten("osdialEXT",$pexten,"1","Goto","osdial,\${EXTEN},1");
		procexten("osdialBLOCK",$pexten,"1","Goto","osdial,\${EXTEN},1");
	}

	procexten("incoming","h","1","Goto","osdial,\${EXTEN},1");

	# Build TTS extensions.
	my $stmt = "SELECT * FROM osdial_tts WHERE extension!='';";
	while (my $sret = $osdial->sql_query($stmt)) {
		$ephn .= "; Text-to-Speech Extension\n";
		$ephn .= procexten("osdial",$sret->{extension},"1","Playback","sip-silence");
		$ephn .= procexten("osdial",$sret->{extension},"2","AGI","agi-OSDtts.agi,\${EXTEN}");
		$ephn .= procexten("osdial",$sret->{extension},"3","Hangup","");
		procexten("osdialEXT",$sret->{extension},"1","Goto","osdial,\${EXTEN},1");
		procexten("osdialBLOCK",$sret->{extension},"1","Goto","osdial,\${EXTEN},1");
		$ephn .= ";\n";
	}

	# Get custom build media file extensions.
	my $stmt = "SELECT * FROM osdial_media WHERE extension!='' AND extension NOT LIKE '8510____';";
	while (my $sret = $osdial->sql_query($stmt)) {
		$sret->{filename} =~ s/\..*$//;
		$ephn .= "; Media Extension\n";
		$ephn .= procexten("osdial",$sret->{extension},"1","Playback",$sret->{filename});
		$ephn .= procexten("osdial",$sret->{extension},"2","Hangup","");
		procexten("osdialEXT",$sret->{extension},"1","Goto","osdial,\${EXTEN},1");
		procexten("osdialBLOCK",$sret->{extension},"1","Goto","osdial,\${EXTEN},1");
		$ephn .= ";\n";
	}

	# Build core osdial extensions
	procexten("osdial","t","1","Goto","osdial,#,1");
	procexten("osdial","i","1","Playback","invalid");
	procexten("osdial","h","1","AGI",'agi://127.0.0.1:4577/call_log--HVcauses--PRI-----NODEBUG-----${HANGUPCAUSE}-----${DIALSTATUS}-----${DIALEDTIME}-----${ANSWEREDTIME}');
	procexten("osdial","#","1","Playback","invalid");
	procexten("osdial","#","2","Hangup","");
	procexten("osdial","*97","1","Goto","osdial,8501,1");
	procexten("osdial","*98","1","Goto","osdial,8500,1");
	procexten("osdial","9","1","Playback","invalid");
	procexten("osdial","43","1","Echo","");
	# barge monitoring extension
	procexten("osdial","8159","1","DahdiBarge","");
	procexten("osdial","8159","2","Hangup","");
	# prompt recording AGI script, ID is 4321
	procexten("osdial","8167","1","Answer","");
	procexten("osdial","8167","2","AGI",'agi-record_prompts.agi,wav,720000');
	procexten("osdial","8167","3","Hangup","");
	procexten("osdial","8168","1","Answer","");
	procexten("osdial","8168","2","AGI",'agi-record_prompts.agi,gsm,720000');
	procexten("osdial","8168","3","Hangup","");
	procexten("osdial","8169","1","Answer","");
	procexten("osdial","8169","2","AGI",'agi-record_prompts.agi,ulaw,720000');
	procexten("osdial","8169","3","Hangup","");
	procexten("osdial","8300","1","Hangup","");
	# park channel for client GUI parking, hangup after 30 minutes
	procexten("osdial","8301","1","Answer","");
	procexten("osdial","8301","2","AGI",'agi-OSDpark.agi');
	procexten("osdial","8301","3","Hangup","");
	# park channel for client GUI conferencing, hangup after 30 minutes
	procexten("osdial","8302","1","Answer","");
	procexten("osdial","8302","2","Playback",'conf');
	procexten("osdial","8302","3","Hangup","");
	procexten("osdial","8303","1","Answer","");
	procexten("osdial","8303","2","AGI",'agi-OSDpark.agi');
	procexten("osdial","8303","3","Hangup","");
	procexten("osdial","8304","1","Answer","");
	procexten("osdial","8304","2","Playback",'ding');
	procexten("osdial","8304","3","Hangup","");
	# default audio for safe harbor 2-second-after-hello message then hangup
	procexten("osdial","8307","1","Answer","");
	procexten("osdial","8307","2","Playback",'vm-goodbye');
	procexten("osdial","8307","3","Hangup","");
	# this is used for recording conference calls, the client app sends the filename value as a callerID recordings go to /var/spool/asterisk/monitor (WAV)
	procexten("osdial","8309","1","Answer","");
	procexten("osdial","8309","2","MixMonitor",'/var/spool/asterisk/record_cache/${CALLERID(name)}-in.wav,,/bin/mv -v ^{MIXMONITOR_FILENAME} /var/spool/asterisk/VDmonitor');
	procexten("osdial","8309","3","Wait","7200");
	procexten("osdial","8309","4","Hangup","");
	# this is used for recording conference calls, the client app sends the filename value as a callerID recordings go to /var/spool/asterisk/monitor (GSM)
	procexten("osdial","8310","1","Answer","");
	procexten("osdial","8310","2","MixMonitor",'/var/spool/asterisk/record_cache/${CALLERID(name)}-in.gsm,,/bin/mv -v ^{MIXMONITOR_FILENAME} /var/spool/asterisk/VDmonitor');
	procexten("osdial","8310","3","Wait","7200");
	procexten("osdial","8310","4","Hangup","");
	# this is used for recording conference calls, the client app sends the filename value as a callerID recordings go to /var/spool/asterisk/monitor (WAV)
	procexten("osdial","8311","1","Answer","");
	procexten("osdial","8311","2","MixMonitor",'/var/spool/asterisk/record_cache/${CALLERID(name)}-in.wav,,/bin/mv -v ^{MIXMONITOR_FILENAME} /var/spool/asterisk/VDmonitor');
	procexten("osdial","8311","3","Wait","7200");
	procexten("osdial","8311","4","Hangup","");
	# this is used for playing a message to an answering machine forwarded from AMD in OSDIAL replace conf with the message file you want to leave
	procexten("osdial","8320","1","WaitForSilence","1000,2,20");
	procexten("osdial","8320","2","GotoIf",'$["${AMDAUDIO}" != ""]?4');
	procexten("osdial","8320","3","Set",'AMDAUDIO=vm-goodbye');
	procexten("osdial","8320","4","Playback",'${AMDAUDIO}');
	procexten("osdial","8320","5","Wait",'4');
	procexten("osdial","8320","6","AGI",'agi-OSDamd_post.agi,${EXTEN},${AMDAUDIO}');
	procexten("osdial","8320","7","Hangup",'');
	procexten("osdial","8321","1","AGI",'agi_OSDamd.agi,${EXTEN}-----YES');
	procexten("osdial","8321","2","Hangup","");
	# use for selective CallerID hangup by area code(hard-coded)
	procexten("osdial","8352","1","AGI",'agi-VDADselective_CID_hangup.agi,${EXTEN}');
	procexten("osdial","8352","2","Playback","safe_harbor");
	procexten("osdial","8352","3","Hangup","");

	# 8364: no agent campaign transfer
	# 8365: single server agent transfer
	# 8366: old single server with initial survey
	# 8367: multi-server agent transfer, load-balance overflow
	# 8368: multi-server agent transfer, load-balance
	# 8372: reminder script
	foreach my $pexten (qw(8364 8365 8366 8367 8368 8372)) {
		procexten("osdial",$pexten,"1","NoOp","");
		procexten("osdial",$pexten,"2","Playback","sip-silence");
		procexten("osdial",$pexten,"3","AGI",'agi://127.0.0.1:4577/call_log');
		procexten("osdial",$pexten,"4","AGI",'agi-OSDoutbound.agi,${EXTEN}');
		procexten("osdial",$pexten,"5","AGI",'agi-OSDoutbound.agi,${EXTEN}');
		procexten("osdial",$pexten,"6","AGI",'agi-OSDoutbound.agi,${EXTEN}');
		procexten("osdial",$pexten,"7","Hangup","");
	}
	# 8369: multi-server agent transfer, load-balance and plus AMD
	# 8375: Auto-agent script.
	foreach my $pexten (qw(8369 8375)) {
		procexten("osdial",$pexten,"1","NoOp","");
		procexten("osdial",$pexten,"2","Playback","sip-silence");
		procexten("osdial",$pexten,"3","AGI",'agi://127.0.0.1:4577/call_log');
		procexten("osdial",$pexten,"4","AGI",'agi-OSDamd.agi,${EXTEN}');
		procexten("osdial",$pexten,"5","AGI",'agi-OSDoutbound.agi,${EXTEN}');
		procexten("osdial",$pexten,"6","AGI",'agi-OSDoutbound.agi,${EXTEN}');
		procexten("osdial",$pexten,"7","AGI",'agi-OSDoutbound.agi,${EXTEN}');
		procexten("osdial",$pexten,"8","Hangup","");
	}

	# Give voicemail at extension 8500
	procexten("osdial","8500","1","VoiceMainMain",'@osdial');
	procexten("osdial","8500","2","Hangup","");
	#procexten("osdial","8500","2","Goto","osdial,s,6");
	# this is used to allow the GUI to send you directly into voicemail don't forget to set GUI variable $voicemail_exten to this extension
	procexten("osdial","8501","1","VoiceMainMain",'s${CALLERID(number)}@osdial');
	procexten("osdial","8501","2","Hangup","");
	# this is used for sending DTMF signals within conference calls, the client app sends the digits to be played in the callerID field sound files must be placed in /var/lib/asterisk/sounds
	procexten("osdial","8500998","1","Answer","");
	procexten("osdial","8500998","2","Playback","silence");
	procexten("osdial","8500998","3","AGI",'agi-OSDdtmf.agi');
	procexten("osdial","8500998","4","Hangup","");
	

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
		$dialplan = "[OOUT" . $carriers->{$carrier}{name} . "]\n";
		$dialplan .= "switch => Realtime/OOUT".$carriers->{$carrier}{name}."\@extensions/p\n";

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
		$dialplan .= "\n\n";

		# Create Inbound dialplan for the carrier, ie DIDs..
		$dialplan .= "[" . $context . "]\n";
		$dialplan .= "switch => Realtime/".$context."\@extensions/p\n";
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
					$dialplan .= procexten($context,$didmatch.$dids->{$did}{did},$prio++,"AGI","agi-VDAD_ALL_inbound.agi,".$dids->{$did}{lookup_method}."-----".$dids->{$did}{server_allocation}
					."-----".$dids->{$did}{ingroup}."-----\${EXTEN}-----\${CALLERID(number)}-----".$dids->{$did}{park_file}."-----".$dids->{$did}{initial_status}."-----".$dids->{$did}{default_list_id}
					."-----".$dids->{$did}{default_phone_code}."-----".$dids->{$did}{search_campaign});
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

sub procexten {
	my ($context,$exten,$priority,$app,$appdata) = @_;
	my $extout = '';
	if ($exten!~/^_/ and $CLOrealtime) {
		$realtime_ext->{$context}{$exten}{sprintf('%d',$priority)} = {'app'=>$app, 'appdata' => $appdata};
	} else {
		$extout .= sprintf("exten => %s,%s,%s(%s)\n",$exten,$priority,$app,$appdata);
	}
	return $extout;
}
