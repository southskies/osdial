#!/usr/bin/perl
#
# AST_send_action_child.pl version 0.5   *DBI-version
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
# This script is spawned every time an action is to be executed by the main
# AST_manager_send.pl script and sends actions blindly to the Asterisk manager
# 
# SUMMARY:
# This program was designed as the blind-send part of the ACQS. It's job is to
# be spawned by the AST_manager_send.pl script lookup the record in the MySQL DB
# to be executed. connect to the manager interface, send the action and logoff
# then exit.
#
# CHANGES
# 50810-1547 - Added database server variable definitions lookup
# 50823-1527 - Altered commandline debug options with debug printouts
# 50902-1032 - Changed default logging to fulllog
# 60718-0909 - changed to DBI by Marin Blu
# 60718-1024 - changed to use /etc/osdial.conf for configs
# 60814-1720 - added option for no logging to file
# 60817-1244 - removed all DB calls and config file open to reduce footprint
# 61004-1728 - added ability to parse volume control and lookup meetme IDs
# 61220-1720 - optimize and clean code, lc.
# 61221-1942 - additional modification to structure, lc.
#

$|++;
use strict;
use Getopt::Long;
use Net::Telnet;
use Time::HiRes qw(gettimeofday);
use OSDial;

### Initialize date/time vars ###
my $secX = time(); #Start time

### Initialize run-time variables ###
my ($CLOhelp, $SYSLOG, $PATHlogs, $telnet_host, $telnet_port, $ASTmgrUSERNAME);
my ($ASTmgrSECRET, $ASTmgrUSERNAMEsend, $man_id, $action, $cmd_line_b, $cmd_line_c);
my ($cmd_line_d, $cmd_line_e, $cmd_line_f, $cmd_line_g, $cmd_line_h);
my ($cmd_line_i, $cmd_line_j, $cmd_line_k, $DB, $DBX, $asterisk_version);
my $FULL_LOG = 1;

### begin parsing run-time options ###
if (scalar @ARGV) {
	GetOptions('help!' => \$CLOhelp,
		'SYSLOG!' => \$SYSLOG,
		'PATHlogs=s' => \$PATHlogs,
		'telnet_host=s' => \$telnet_host,
		'telnet_port=s' => \$telnet_port,
		'ASTmgrUSERNAME=s' => \$ASTmgrUSERNAME,
		'ASTmgrSECRET=s' => \$ASTmgrSECRET,
		'ASTmgrUSERNAMEsend=s' => \$ASTmgrUSERNAMEsend,
		'asterisk_version=s' => \$asterisk_version,
		'man_id=s' => \$man_id,
		'action=s' => \$action,
		'cmd_line_b=s' => \$cmd_line_b,
		'cmd_line_c=s' => \$cmd_line_c,
		'cmd_line_d=s' => \$cmd_line_d,
		'cmd_line_e=s' => \$cmd_line_e,
		'cmd_line_f=s' => \$cmd_line_f,
		'cmd_line_g=s' => \$cmd_line_g,
		'cmd_line_h=s' => \$cmd_line_h,
		'cmd_line_i=s' => \$cmd_line_i,
		'cmd_line_j=s' => \$cmd_line_j,
		'cmd_line_k=s' => \$cmd_line_k,
		'debug!' => \$DB,
		'debugX!' => \$DBX,
		'fulllog!' => \$FULL_LOG);

	$DB = 1 if ($DBX);
	if ($DB) {
		print "\n----- DEBUGGING -----\n\n";
		print "\n----- SUPER-DUPER DEBUGGING -----\n\n" if ($DBX);
		print "  SYSLOG:                $SYSLOG\n" if ($SYSLOG);
		print "  telnet_host:           $telnet_host\n" if ($telnet_host);
		print "  telnet_port:           $telnet_port\n" if ($telnet_port);
		print "  ASTmgrUSERNAME:        $ASTmgrUSERNAME\n" if ($ASTmgrUSERNAME);
		print "  ASTmgrSECRET:          $ASTmgrSECRET\n" if ($ASTmgrSECRET);
		print "  ASTmgrUSERNAMEsend:    $ASTmgrUSERNAMEsend\n" if ($ASTmgrUSERNAMEsend);
		print "  asterisk_version:      $asterisk_version\n" if ($asterisk_version);
		print "  man_id:                $man_id\n" if ($man_id);
		print "  action:                $action\n" if ($action);
		print "  cmd_line_b:            $cmd_line_b\n" if ($cmd_line_b);
		print "  cmd_line_b:            $cmd_line_b\n" if ($cmd_line_b);
		print "  cmd_line_c:            $cmd_line_c\n" if ($cmd_line_c);
		print "  cmd_line_d:            $cmd_line_d\n" if ($cmd_line_d);
		print "  cmd_line_e:            $cmd_line_e\n" if ($cmd_line_e);
		print "  cmd_line_f:            $cmd_line_f\n" if ($cmd_line_f);
		print "  cmd_line_g:            $cmd_line_g\n" if ($cmd_line_g);
		print "  cmd_line_h:            $cmd_line_h\n" if ($cmd_line_h);
		print "  cmd_line_i:            $cmd_line_i\n" if ($cmd_line_i);
		print "  cmd_line_j:            $cmd_line_j\n" if ($cmd_line_j);
		print "  cmd_line_k:            $cmd_line_k\n" if ($cmd_line_k);
		print "\n";
	}
	if ($CLOhelp) {
		print "allowed run time options:\n";
		print "  [--help] = this help screen\n";
		print "  [--SYSLOG] = whether to log actions or not\n";
		print "required flags:\n";
		print "  [--PATHlogs] = logs directory path\n";
		print "  [--telnet_host] = IP address to connect to Asterisk Manager\n";
		print "  [--telnet_port] = port to connect to Asterisk Manager\n";
		print "  [--ASTmgrUSERNAME] = username for Asterisk Manager login\n";
		print "  [--ASTmgrSECRET] = secret or password for Asterisk Manager login\n";
		print "  [--ASTmgrUSERNAMEsend] = username specific for sending actions for Asterisk Manager login\n";
		print "  [--man_id] = The manager ID, if present will be used as the ActionID\n";
		print "  [--action] = type of manager action to send\n";
		print "  [--cmd_line_X] = lines to send to Manager after action\n";
		print "                   X replaced with b-k (10 lines)\n";
		print "\n";
		print "You may prefix an option with 'no' to disable it.\n";
		print " ie. --noSYSLOG or --noFULLLOG\n";

		exit 0;
	}
}

if ($action) {
	$PATHlogs =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_b =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_c =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_d =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_e =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_f =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_g =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_h =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_i =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_j =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$cmd_line_k =~	s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	if ($DB) {
		print "  SYSLOG:                $SYSLOG\n" if ($SYSLOG);
		print "  telnet_host:           $telnet_host\n" if ($telnet_host);
		print "  telnet_port:           $telnet_port\n" if ($telnet_port);
		print "  ASTmgrUSERNAME:        $ASTmgrUSERNAME\n" if ($ASTmgrUSERNAME);
		print "  ASTmgrSECRET:          $ASTmgrSECRET\n" if ($ASTmgrSECRET);
		print "  ASTmgrUSERNAMEsend:    $ASTmgrUSERNAMEsend\n" if ($ASTmgrUSERNAMEsend);
		print "  cmd_line_b:            $cmd_line_b\n" if ($cmd_line_b);
		print "  cmd_line_c:            $cmd_line_c\n" if ($cmd_line_c);
		print "  cmd_line_d:            $cmd_line_d\n" if ($cmd_line_d);
		print "  cmd_line_e:            $cmd_line_e\n" if ($cmd_line_e);
		print "  cmd_line_f:            $cmd_line_f\n" if ($cmd_line_f);
		print "  cmd_line_g:            $cmd_line_g\n" if ($cmd_line_g);
		print "  cmd_line_h:            $cmd_line_h\n" if ($cmd_line_h);
		print "  cmd_line_i:            $cmd_line_i\n" if ($cmd_line_i);
		print "  cmd_line_j:            $cmd_line_j\n" if ($cmd_line_j);
		print "  cmd_line_k:            $cmd_line_k\n" if ($cmd_line_k);
	}
	  
	$telnet_port = '5038' if (!$telnet_port);

	### connect to asterisk manager through telnet
	my $tn = new Net::Telnet (Port => $telnet_port,
		Prompt => '/.*[\$%#>] $/',
		Output_record_separator => '',
		Errmode    => "return");
	#$fh = $tn->dump_log("$PATHlogs/SAC_telnet_log.txt");  # uncomment for telnet log
	my $telnet_login;
	if ($ASTmgrUSERNAMEsend) {
		$telnet_login = $ASTmgrUSERNAMEsend;
	} else {
		$telnet_login = $ASTmgrUSERNAME;
	}
	$tn->open($telnet_host); 
	my $action_id = '';
	if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
		if ($asterisk_version =~ /^11\./) {
			$tn->waitfor('/3\n$/');
		} elsif ($asterisk_version =~ /^10\./) {
			$tn->waitfor('/2\n$/');
		} else {
			$tn->waitfor('/1\n$/');
		}
		$action_id = 'ActionID: ';
		$action_id .= 'M' . $man_id if ($man_id);
		my($s, $usec) = gettimeofday();
		my $usid = sprintf('U%d.%06d',$s,$usec);
		$action_id .= '~'.$usid;
		$tn->print("Action: Login\n$action_id~Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
	} else {
		$tn->waitfor('/0\n$/');			# print login
		$tn->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
	}
	$tn->waitfor('/Authentication accepted/'); # waitfor auth accepted

	$tn->buffer_empty;

	if ($cmd_line_b =~ /XXYYXXYYXXYYXX/) {
		#	Action: Command
		#	Command: meetme list 8600051
		#
		#	Response: Follows
		#	Privilege: Command
		#	User #: 03          261 261iax               Channel: IAX2/261iax-13    (unmonitored)
		#	1 users in that conference.
		#	--END COMMAND--
		my $meetme_command = "Action: Command\nCommand: meetme list $cmd_line_k\n\n";
		if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
			$meetme_command = "Action: Command\n$action_id~Command\nCommand: meetme list $cmd_line_k\n\n";
		}
		print nowDate() . "|$SYSLOG|\n$meetme_command";

		my $participant;
		my @list_meetme = $tn->cmd(String => $meetme_command,
			Prompt => '/--END COMMAND-.*/');
		foreach my $meetme (@list_meetme) {
			if ($meetme =~ /$cmd_line_j /i) {
				$meetme =~ s/User \#: //gi;
				my @participants = split(/ /, $meetme);
				$participant = ($participants[0] + 0);
			}
		#	print "$participant|$cmd_line_j|$meetme";
		}
		$participant = "1" unless ($participant > 0);
		$cmd_line_b =~ s/XXYYXXYYXXYYXX/$participant/gi;
		$cmd_line_j = '';
		$cmd_line_k = '';
	}

	my $originate_command;
	$originate_command .= "Action: $action\n";
	$originate_command .= $action_id . '~' . $action . "\n" if ($action_id);
	#if (length($cmd_line_b)>3) {$originate_command .= "$cmd_line_b\n";}
	$originate_command .= $cmd_line_b . "\n" if ($cmd_line_b);
	$originate_command .= $cmd_line_c . "\n" if ($cmd_line_c);
	$originate_command .= $cmd_line_d . "\n" if ($cmd_line_d);
	$originate_command .= $cmd_line_e . "\n" if ($cmd_line_e);
	$originate_command .= $cmd_line_f . "\n" if ($cmd_line_f);
	$originate_command .= $cmd_line_g . "\n" if ($cmd_line_g);
	$originate_command .= $cmd_line_h . "\n" if ($cmd_line_h);
	$originate_command .= $cmd_line_i . "\n" if ($cmd_line_i);
	$originate_command .= $cmd_line_j . "\n" if ($cmd_line_j);
	$originate_command .= $cmd_line_k . "\n" if ($cmd_line_k);
	$originate_command .= "\n";

	my @list_channels = $tn->cmd(String => $originate_command,
		Prompt => '/.*/'); 

	print nowDate() . "|$SYSLOG|\n$originate_command";
	my $event_string = "0|" . $SYSLOG . "|";
	$event_string .= "\n" . $originate_command;

	eventLogger($PATHlogs,'full',$event_string) if ($FULL_LOG and $SYSLOG);


	sleep(1);
	
	my $data1;  # ? Useless ?
	my $event_string='';
	my $res_count = 1;
	foreach my $channel (@list_channels) {
		$event_string .= $res_count . '|' . $data1 . '|' . $channel;
		$res_count++;
	}
	my $read_input_buf = $tn->get(Errmode => "return",
		Timeout => 1);
	$event_string .= $read_input_buf;
	my @res_line = split(/\n/, $read_input_buf);
	my %ame;
	foreach my $rline (@res_line) {
		if ($rline =~ /: /) {
			my ($clkey, $clval) = split(/: /,$rline);
			$clkey = lc($clkey);
			$ame{$clkey} = $clval;
		}
	}
	if ($ame{response} eq 'Error') {
		if ($ame{actionid} =~ /Hangup$/i and $ame{message} =~ /No such channel/i) {
			my ($man_id,$usec_id,$action) = split(/~/,$ame{actionid});
			$man_id =~ s/^M//;
			$usec_id =~ s/^U//;
			if ($man_id ne '') {
				my $osdial = OSDial->new('DB'=>$DB);
				$osdial->sql_execute(sprintf("UPDATE osdial_manager SET status='DEAD' WHERE man_id='%s';",$man_id));
				$event_string .= "*** Marking failed command ($man_id) as DEAD.\n";
			}
		}
	}
	eventLogger($PATHlogs,'full',$event_string) if ($FULL_LOG and $SYSLOG);


	$tn->buffer_empty;
	#@hangup = $tn->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
	if ($asterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
		$tn->cmd(String => "Action: Logoff\n$action_id~Logoff\n\n", Prompt => "/.*/"); 
	} else {
		$tn->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
	}
	sleep(1);

	my $event_string='';
	my $res_count = 1;
	foreach my $channel (@list_channels) {
		$event_string .= $res_count . '|' . $data1 . '|' . $channel;
		$res_count++;
	}
	my $read_input_buf = $tn->get(Errmode => "return",
		Timeout => 1);
	$event_string .= $read_input_buf;
	if ($FULL_LOG and $SYSLOG) {
		eventLogger($PATHlogs,'full',$event_string);
	}

	$tn->buffer_empty;
	$tn->close;
}

my $secZ = time();
my $script_time = ($secZ - $secX);
print "DONE execute time: $script_time seconds\n";

exit 0;
# Program ends.


### Start of subs.

# eventLogger usage:
#    eventLgger($LogFileDir, $LogType, $EventString);
# Requires:
#    $LogFilePath : Directory where log files are.
#    $LogType     : Type of log, ie process, send, launch, full
#    $EventString : String to record in log.
sub eventLogger {
	my ($path,$type,$string) = @_;
	open(LOG, ">>" . $path . "/action_" . $type . "." . logDate())
		|| die "Can't open " . $path . "/action_" . $type . "." .
			logDate() . ": " . $! . "\n";
	print LOG nowDate() . "|" . $string . "|\n";
	close(LOG);
}

# getTime usage:
#   getTime($SecondsSinceEpoch);
# Options:
#   $SecondsSinceEpoch : Request time in seconds, defaults to current date/time.
# Returns:
#   ($sec, $min, $hour. $day, $mon, $year)
sub getTime {
	my ($tms) = @_;
	$tms = time unless ($tms);
	my($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)=localtime($tms);
	$year += 1900;
	$mon++;
	$mon = "0" . $mon if ($mon < 10);
	$mday = "0" . $mday if ($mday < 10);
	$min = "0" . $min if ($min < 10);
	$sec = "0" . $sec if ($sec < 10);
	return ($sec,$min,$hour,$mday,$mon,$year);
}

# nowDate usage:
#   nowDate($SecondsSinceEpoch);
# Options:
#   $SecondsSinceEpoch : Request time in seconds, defaults to current date/time.
# Returns:
#   scalar date/time string (MySQL formatted) ie "2007-01-01 00:00:00"
sub nowDate {
	my ($tms) = @_;
	my($sec,$min,$hour,$mday,$mon,$year) = getTime($tms);
	return $year.'-'.$mon.'-'.$mday.' '.$hour.':'.$min.':'.$sec;
}

# logDate usage:
#   logDate($SecondsSinceEpoch);
# Options:
#   $SecondsSinceEpoch : Request time in seconds, defaults to current date/time.
# Returns:
#   scalar date string ie "2007-01-01"
sub logDate {
	my ($tms) = @_;
	my($sec,$min,$hour,$mday,$mon,$year) = getTime($tms);
	return  $year . '-' . $mon . '-' . $mday;
}

### End of subs


