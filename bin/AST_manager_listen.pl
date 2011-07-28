#!/usr/bin/perl
#
# AST_manager_listen.pl version 2.0.4   *DBI-version*
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
# Part of the Asterisk Central Queue System (ACQS)
#
# DESCRIPTION:
# connects to the Asterisk Manager interface and updates records in the 
# osdial_manager table of the asterisk database in MySQL based upon the 
# events that it receives
# 
# SUMMARY:
# This program was designed as the listen-only part of the ACQS. It's job is to
# look for certain events and based upon either the uniqueid or the callerid of 
# the call update the status and information of an action record in the 
# osdial_manager table of the asterisk MySQL database.
# 
# For this program to work you need to have the "asterisk" MySQL database 
# created and create the tables listed in the CONF_MySQL.txt file, also make sure
# that the machine running this program has read/write/update/delete access 
# to that database
# 
# In your Asterisk server setup you also need to have several things activated
# and defined. See the CONF_Asterisk.txt file for details
#
# CHANGES
# 50322-1300 - changed callerid parsing to remove quotes and number
# 50616-1559 - Added NewCallerID parsing and updating 
# 50621-1406 - Added Asterisk server shutdown and connection dead detection 
# 50810-1534 - Added database server variable definitions lookup
# 50824-1606 - Altered CVS/1.2 support for different output
# 50901-2359 - Another CVS/1.2 output parsing fix
# 51222-1553 - fixed parentheses bug in manager output
# 60403-1230 - Added SVN/1.4 support for different output
# 60718-0909 - changed to DBI by Marin Blu
# 60718-0955 - changed to use /etc/osdial.conf for configs
# 60720-1142 - added keepalive to MySQL connection every 50 seconds
# 60814-1733 - added option for no logging to file
# 60906-1714 - added updating for special osdial conference calls
# 71129-2004 - Fixed SQL error
#

$|++;

use strict;
use OSDial;
use Getopt::Long;
#use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use Time::HiRes ('gettimeofday');
use Net::Telnet;


my $DB=0;
my $DBX=0;
my $VERBOSE=0;
my $TEST=0;
my $HELP=0;

if (scalar @ARGV) {
	GetOptions(
		'help!' => \$HELP,
		'debug!' => \$DB,
		'debugX!' => \$DBX,
		'verbose+' => \$VERBOSE,
		'test!' => \$TEST
	);
	$DB=$VERBOSE if ($VERBOSE);
	$DBX=$VERBOSE if ($VERBOSE>1);
	$DB=$VERBOSE if ($DBX);
	$DB=$VERBOSE=1 if ($TEST and $DB==0);
	if ($DB) {
		print "----- DEBUGGING -----\n";
		print "----- Testing Mode -----\n" if ($TEST);
		print "VARS-\n";
		print "help-        $HELP\n";
		print "debug-       $DB\n";
		print "debugX-      $DBX\n";
		print "verbose-     $VERBOSE\n";
		print "test-        $TEST\n";
		print "\n";
	}
	if ($HELP) {
		print "ADMIN_keepalive_ALL.pl: Allowed run time options:\n";
		print "  [--help] = This screen.\n";
		print "  [--debug] = debug\n";
		print "  [--debugX] = super debug\n";
		print "  [-v|--verbose] = verbose (debug)\n";
		print "  [-t|--test] = Run in test mode.\n";
		exit;
	}
}

my $osdial = OSDial->new('DB'=>$DB);


my $telnet_login = $osdial->{server}{ASTmgrUSERNAME};
$telnet_login = $osdial->{server}{ASTmgrUSERNAMElisten} if (length($osdial->{server}{ASTmgrUSERNAMElisten}) > 3);

$osdial->event_logger('listen_process', 'LOGGED INTO MYSQL SERVER ON 1 CONNECTION|');

my $keepalive_count_loop = 0;

my $one_day_interval = 90;
while($one_day_interval > 0) {

	$osdial->event_logger('listen_process', "STARTING NEW MANAGER TELNET CONNECTION||ATTEMPT|ONE DAY INTERVAL:$one_day_interval|");

	### connect to asterisk manager through telnet
	my $tn = new Net::Telnet (Port => $osdial->{server}{telnet_port},
						  Prompt => '/.*[\$%#>] $/',
						  Output_record_separator => '',);
	$tn->open($osdial->{server}{telnet_host}); 

	# print login
	if ($osdial->{server}{asterisk_version} =~ /^1\.6|^1\.8/) {
		$tn->waitfor('/1\n$/');
		my($s, $usec) = gettimeofday();
		$tn->print("Action: Login\nActionID: $usec\nUsername: $telnet_login\nSecret: " . $osdial->{server}{ASTmgrSECRET} . "\n\n");
	} else {
		$tn->waitfor('/0\n$/');
		$tn->print("Action: Login\nUsername: $telnet_login\nSecret: " . $osdial->{server}{ASTmgrSECRET} . "\n\n");
	}
	# waitfor auth accepted
	$tn->waitfor('/Authentication accepted/');
	$tn->buffer_empty;

	$osdial->event_logger('listen_process', "STARTING NEW MANAGER TELNET CONNECTION|$telnet_login|CONFIRMED CONNECTION|ONE DAY INTERVAL:$one_day_interval|");

	# 1 day at .10 seconds per loop
	my $endless_loop=864000;

	my $input_buf = '';

	while($endless_loop > 0) {
                #$tn->print("Action:\n");
                $tn->print("Action: Ping\n\n") if ($endless_loop % 10 == 0);

		### sleep for 10 hundredths of a second
		#usleep(1*10*1000);

		my $msg='';
		my $read_input_buf = $tn->get(Errmode => "return", Timeout => 1);
		my $input_buf_length = length($read_input_buf);
		$msg = $tn->errmsg;
		if ($msg =~ /filehandle isn\'t open/i) {
			$endless_loop=0;
			$one_day_interval=0;
			print "ERRMSG: |$msg|\n";
			print "\nAsterisk server shutting down, PROCESS KILLED... EXITING\n\n";
			$osdial->event_logger('listen_process', "Asterisk server shutting down, PROCESS KILLED... EXITING|ONE DAY INTERVAL:$one_day_interval|$msg|");
		}

		if ($read_input_buf !~ /\n\n/ or $input_buf_length < 10) {
			$input_buf .= $read_input_buf;
		} else {
			my $partial=0;
			my $partial_input_buf='';

			if ($read_input_buf !~ /\n\n$/) {
				$read_input_buf =~ s/\(|\)/ /gi;
				$partial_input_buf = $read_input_buf;
				$partial_input_buf =~ s/\n/-----/gi;
				$partial_input_buf =~ s/\*/\\\*/gi;
				$partial_input_buf =~ s/.*----------//gi;
				$partial_input_buf =~ s/-----/\n/gi;
				$read_input_buf =~ s/$partial_input_buf$//gi;
				$partial++;
			}

			$input_buf .= $read_input_buf;
			my @input_lines = split(/\n\n/, $input_buf);

			print "-----[$partial_input_buf]-----\n\n" if ($DB and $partial);

			$input_buf = $partial_input_buf;

			my @command_line;
			foreach my $inline (@input_lines) {
				my $logmsg;
				@command_line=split(/\n/, $inline);
				my %ame;
				foreach my $line (@command_line) {
					if ($line =~ /: /) {
						my ($clkey, $clval) = split(/: /,$line);
						$clkey = lc($clkey);

						if ($clkey eq "desination") {
							$clval =~ s/\s*$//g;
						} elsif ($clkey =~ /^callerid/) {
							$clval =~ s/\s*$//g;
							$clval =~ s/^\"//g;
							$clval =~ s/\".*$//g;
						}
						$ame{$clkey} = $clval;
					}
				}
				#Asterisk 1.4 / 1.6 / 1.8 event key conversion
				$ame{callerid} = $ame{calleridnum} if ($ame{calleridnum});
				$ame{state} = $ame{channelstatedesc} if ($ame{channelstatedesc});
				$ame{srcuniqueid} = $ame{uniqueid} if ($ame{uniqueid});
				$ame{accountcode} = $ame{account} if ($ame{account});

				$ame{zombie} = 1 if ($inline =~ s/<ZOMBIE>//g);


				# Got Reponse from keepalive (ping).
				if ($ame{ping} =~ /pong/i) {
					$logmsg = "Keepalive response.";

				} elsif ($ame{zombie}==1) {
					$logmsg = "Skipping ZOMBIE channel.";

				# Clear conference when meetme ends.
				} elsif ($ame{event} =~ /VMChangePassword/i) {
					my $mailbox = $ame{mailbox};
					if ($mailbox =~ /\@osdial$/) {
						$mailbox =~ s/\@osdial$//;
						my $stmtA = sprintf("UPDATE phones SET voicemail_password='%s' WHERE voicemail_id='%s' AND server_ip='%s';",$ame{newpassword}, $mailbox, $osdial->{VARserver_ip});
						my $affected_rows = $osdial->sql_execute($stmtA);
						print "|$affected_rows Updated VM Password|$stmtA\n|" if ($DB);
						$logmsg = "Got voicemail password change request, updating database.";
					} else {
						$logmsg = "Got voicemail password change request, but not an 'osdial' box.";
					}

				# Clear conference when meetme ends.
				} elsif ($ame{event} =~ /MeetmeEnd/i) {
					my $stmtA = sprintf("UPDATE conferences SET extension='' WHERE server_ip='%s' AND conf_exten='%s';",$osdial->{VARserver_ip}, $ame{meetme});
					my $affected_rows = $osdial->sql_execute($stmtA);
					print "|$affected_rows Conference cleared|$stmtA\n|" if ($DB);
					my $stmtA = sprintf("UPDATE osdial_conferences SET extension='' WHERE server_ip='%s' AND conf_exten='%s' AND extension LIKE '3WAY%';", $osdial->{VARserver_ip}, $ame{meetme});
					my $affected_rows = $osdial->sql_execute($stmtA);
					print "|$affected_rows Conference cleared|$stmtA\n|" if ($DB);
					$logmsg = "End of conference, clear from database."

				# got Shutdown of asterisk.
				} elsif ($ame{event} =~ /Shutdown/i) {
					$endless_loop=0;
					$one_day_interval=0;
					print "\nAsterisk server shutting down, PROCESS KILLED... EXITING\n\n";
					$osdial->event_logger('listen_process', "Asterisk server shutting down, PROCESS KILLED... EXITING|ONE DAY INTERVAL:$one_day_interval|");
					$logmsg = "Asterisk server shutting down."

				# Got hangup on channel, update manager.
				} elsif ($ame{event} =~ /Hangup/i) {
					if ($ame{accountcode} =! /DCagcW/) {
						my $stmtA = sprintf("UPDATE osdial_manager SET status='DEAD',channel='%s' WHERE server_ip='%s' AND uniqueid='%s' AND callerid NOT LIKE 'DCagcW%';",$ame{channel},$osdial->{VARserver_ip},$ame{uniqueid});
						my $affected_rows = $osdial->sql_execute($stmtA);
						print "|$affected_rows HANGUPs updated|$stmtA|\n" if ($DB);
						if ($ame{calleridname} =~ /^OSDial#/ and $ame{channel} =~ /^Local\/.*@.*-....;.*$/) {
							$ame{channel} =~ s/-....;.*$//;
						}
						my $stmtA = sprintf("UPDATE osdial_manager SET status='DEAD',uniqueid='%s' WHERE server_ip='%s' AND channel='%s' AND uniqueid='' AND action='Hangup' AND callerid NOT LIKE 'DCagcW%';",$ame{uniqueid},$osdial->{VARserver_ip},$ame{channel});
						my $affected_rows = $osdial->sql_execute($stmtA);
						print "|$affected_rows HANGUPs updated|$stmtA|\n" if ($DB);
						$logmsg = "Hangup on channel, update manager AS DEAD.";
					} else {
						$logmsg = "Hangup on channel, DCagcW channel, not updating manager.";
					}

				# Update manager on new callerid or accountcode in channel.
				} elsif ($ame{event} =~ /NewCallerid|NewAccountCode/i) {
					my $stmtA = sprintf("UPDATE osdial_manager SET status='UPDATED',channel='%s',uniqueid='%s' WHERE server_ip='%s' AND callerid='%s' LIMIT 1;", $ame{channel}, $ame{uniqueid}, $osdial->{VARserver_ip}, $ame{accountcode});
					my $affected_rows = $osdial->sql_execute($stmtA);
					print "|$affected_rows NEWCALLIDs updated|$stmtA|\n" if ($DB);
					$logmsg = $ame{event} . " on channel, update manager.";


				} elsif ($ame{event} =~ /RedirectStatus/i) {
					if ($ame{accountcode} ne "") {
 						if ($ame{status} =~ /Failure/i and $ame{extrastatus} =~ /Failure/i) {
							my $stmtA = sprintf("UPDATE osdial_manager SET status='DEAD',channel='%s',uniqueid='%s' WHERE action='Redirect' AND server_ip='%s' AND callerid='%s';",$ame{channel},$ame{uniqueid},$osdial->{VARserver_ip},$ame{accountcode});
							my $affected_rows = $osdial->sql_execute($stmtA);
							$logmsg = "Redirect dual failure on channel, update manager.";
 						} elsif ($ame{status} =~ /Success/i and $ame{extrastatus} =~ /Failure/i) {
							my $stmtA = sprintf("UPDATE osdial_manager SET status='UPDATED',channel='%s',uniqueid='%s' WHERE action='Redirect' AND server_ip='%s' AND callerid='%s';",$ame{channel},$ame{uniqueid},$osdial->{VARserver_ip},$ame{accountcode});
							my $affected_rows = $osdial->sql_execute($stmtA);
							$logmsg = "Redirect dual with secondary failure on channel, update manager.";
 						} elsif ($ame{status} =~ /Success/i and $ame{extrastatus} =~ /Success/i) {
							my $stmtA = sprintf("UPDATE osdial_manager SET status='UPDATED',channel='%s',uniqueid='%s' WHERE action='Redirect' AND server_ip='%s' AND callerid='%s';",$ame{channel},$ame{uniqueid},$osdial->{VARserver_ip},$ame{accountcode});
							my $affected_rows = $osdial->sql_execute($stmtA);
							$logmsg = "Redirect dual success on channel, update manager.";
 						} elsif ($ame{status} =~ /Failure/i) {
							my $stmtA = sprintf("UPDATE osdial_manager SET status='DEAD',channel='%s',uniqueid='%s' WHERE action='Redirect' AND server_ip='%s' AND callerid='%s';",$ame{channel},$ame{uniqueid},$osdial->{VARserver_ip},$ame{accountcode});
							my $affected_rows = $osdial->sql_execute($stmtA);
							$logmsg = "Redirect failure on channel, update manager.";
 						} elsif ($ame{status} =~ /Success/i) {
							my $stmtA = sprintf("UPDATE osdial_manager SET status='UPDATED',channel='%s',uniqueid='%s' WHERE action='Redirect' AND server_ip='%s' AND callerid='%s';",$ame{channel},$ame{uniqueid},$osdial->{VARserver_ip},$ame{accountcode});
							my $affected_rows = $osdial->sql_execute($stmtA);
							$logmsg = "Redirect success on channel, update manager.";
						}
					}

				} elsif ($ame{event} =~ /Newstate/i and $ame{accountcode} =! /DCagcW/) {
					if ($ame{accountcode} eq "") {
						$logmsg = "Unhandled newstate (" . $ame{state} . ") on channel, no accountcode.";
					} elsif ($ame{state} =~ /Dialing/i) {
						my $stmtA = sprintf("UPDATE osdial_manager SET status='SENT',channel='%s',uniqueid='%s' WHERE server_ip='%s' AND callerid='%s';",$ame{channel},$ame{uniqueid},$osdial->{VARserver_ip},$ame{accountcode});
						my $affected_rows = $osdial->sql_execute($stmtA);
						print "|$affected_rows DIALINGs updated|$stmtA|\n" if ($DB);
						$logmsg = "Newstate (dialing) on channel, update manager as SENT.";

					} elsif ($ame{state} =~ /Ringing|Up/i) {
						#if ($ame{channel} =~ /local/i) {
						#	$logmsg = "Newstate (ringing/up) on channel, Local channel, not updating manager.";
						#} else {
							my $stmtA = sprintf("UPDATE osdial_manager SET status='UPDATED',channel='%s',uniqueid='%s' WHERE server_ip='%s' AND callerid='%s';",$ame{channel},$ame{uniqueid},$osdial->{VARserver_ip},$ame{accountcode});
							my $affected_rows = $osdial->sql_execute($stmtA);
							print "|$affected_rows RINGINGs updated|$stmtA|\n" if ($DB);
							$logmsg = "Newstate (ringing/up) on channel, update manager.";
						#}
					} else {
						$logmsg = "Unhandled newstate (" . $ame{state} . ") on channel.";
					}
		

				##### look for special osdial conference call event #####
				} elsif ($ame{event} =~ /Newstate/i and $ame{accountcode} =~ /DCagcW/) {
					if ($ame{state} =~ /^Up/i) {
						my $stmtA = sprintf("UPDATE osdial_manager SET status='UPDATED',channel='%s',uniqueid='%s' WHERE server_ip='%s' AND callerid='%s' AND status='SENT';",$ame{channel},$ame{srcuniqueid},$osdial->{VARserver_ip},$ame{accountcode});
						my $affected_rows = $osdial->sql_execute($stmtA);
						print "|$affected_rows Conference DIALs updated|$stmtA|\n" if ($DB);
						$logmsg = "Newstate (up) on DCagcW channel, update manager.";

					} else {
						$logmsg = "Unhandled newstate (" . $ame{state} . ") on DCagcW channel.";
					}

				##### look for special osdial conference call event #####
				} elsif ($ame{event} =~ /Dial/i and $ame{accountcode} =~ /DCagcW/) {
					if ($ame{destination} eq "") {
						$logmsg = "Unhandled dial on DCagcW channel, not initiation."
					#} elsif ($ame{destination} =~ /local/i) {
					#	$logmsg = "Unhandled dial on DCagcW channel, Local channel."
					} else {
						my $stmtA = sprintf("UPDATE osdial_manager SET status='UPDATED',channel='%s',uniqueid='%s' WHERE server_ip='%s' AND callerid='%s';", $ame{destination}, $ame{srcuniqueid}, $osdial->{VARserver_ip}, $ame{accountcode});
						my $affected_rows = $osdial->sql_execute($stmtA);
						print "|$affected_rows Conference DIALs updated|$stmtA\n|" if ($DB);
						$logmsg = "Dial on DCagcW non-local channel."
					}

				} elsif ($ame{event} ne "") {
					$logmsg = "Unhandled event (" . $ame{event} . ").";
				}

				if ($DB) {
					print "\n" . $logmsg . "\n";
					foreach my $clkey (keys %ame) {
						print $clkey .":" . $ame{$clkey} . "\n";
					}
					print "\n";
				}

				$osdial->event_logger('listen', $logmsg . "\n" . $inline . "\n") unless ($logmsg eq "");
			}

		}


		$endless_loop--;
		$keepalive_count_loop++;

		print STDERR "loop counter: |$endless_loop|$keepalive_count_loop|\r" if ($DB);


		### run a keepalive command to flush whatever is in the buffer through and to keep the connection alive
		### Also, keep the MySQL connection alive by selecting the server_updater time for this server
		if ($endless_loop =~ /00$|50$/) {
			# Reload configuration.
			$osdial->load_config();

			my @list_lines;
			if ($osdial->{server}{asterisk_version} =~ /^1\.4|^1\.6|^1\.8/) {
				@list_lines = $tn->cmd(	String => "Action: Command\nCommand: core show uptime\n\n",
							Prompt => '/--END COMMAND--.*/',
							Errmode    => "return",
							Timeout    => 1); 
			} else {
				@list_lines = $tn->cmd(	String => "Action: Command\nCommand: show uptime\n\n",
							Prompt => '/--END COMMAND--.*/',
							Errmode    => "return",
							Timeout    => 1); 
			}
			print "input lines: $#list_lines\n" if ($DB);
			print "+++++++++++++++++++++++++++++++sending keepalive transmit line $endless_loop|" . $osdial->get_datetime() . "|\n" if ($DB);
			$keepalive_count_loop=0;

		}
	}


	$osdial->event_logger('listen_process', 'HANGING UP|');
	my @hangup = $tn->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/", Errmode => "return", Timeout => 1); 
	$tn->close;

	print "DONE... Exiting... Goodbye... See you later... Not really, initiating next loop...$one_day_interval left\n" if ($DB);
	$one_day_interval--;
}

$osdial->event_logger('listen_process', 'CLOSING DB CONNECTION|');
print "DONE... Exiting... Goodbye... See you later... Really I mean it this time\n" if ($DB);


exit;
