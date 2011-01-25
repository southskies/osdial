#!/usr/bin/perl
#
# AST_manager_send.pl version 0.3   *DBI-version*
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
# spawns child processes (AST_send_action_child.pl) to execute action commands 
# on the Asterisk manager interface from records in the osdial_manager table
# of the asterisk database in MySQL that are marked as a status of NEW
#
# SUMMARY:
# This program was designed as the send-only part of the ACQS. It's job is to
# pick NEW actions from the osdial_manager table and send them to be executed
# by separate child process. This allows for a higher degree of flexibility and
# scalability over just using a single process. Also, this means that a single
# action execution lock cannot bring the entire system down.
# 
# CHANGES
# 50823-1514 - Added commandline debug options with debug printouts
# 50902-1051 - Added extra debug output launch sub(commented out)
# 60718-0909 - changed to DBI by Marin Blu
# 60718-1005 - changed to use /etc/osdial.conf for configs
# 60718-1211 - removed need for ADMIN_keepalive_send_listen.at launching
# 60814-1712 - added option for no logging to file
# 60817-1211 - added more ARGS to go to child process to remove DBI from child
# 61221-1926 - optimize and clean code, lc
#

$|++;
use strict;
use DBI;
use Getopt::Long;
use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second

# constants and globals
my $servConf;
my %conf;
$conf{PATHconf} = '/etc/osdial.conf'; # default path to osdial configuration file:
my $COUNTER_OUTPUT=1;	# set to 1 to display the counter as the script runs
my ($CLOhelp, $sendonlyone, $TEST, $DB, $DBX, $SYSLOG);

### begin parsing run-time options ###
if (scalar @ARGV) {
	GetOptions('help!' => \$CLOhelp,
		'sendonlyone!' => \$sendonlyone,
		'counteroutput!' => \$COUNTER_OUTPUT,
		't!' => \$TEST, #Fucntionality doesn't actually exist.
		'debug!' => \$DB,
		'debugX!' => \$DBX );
	$DB = 1 if ($DBX);
	if ($DB) {
		print "----- DEBUGGING -----\n";
		print "----- EXTRA-VERBOSE DEBUGGING -----\n" if ($DBX);
		print "COUNTER_OUTPUT:     $COUNTER_OUTPUT\n";
		print "sendonlyone:        $sendonlyone\n" if ($sendonlyone);
		print "TEST:               $TEST\n" if ($TEST);
	}
	if ($CLOhelp) {
		print "\nAST_manager_send.pl\n";
		print "allowed run time options:\n";
		print "  [--help] = This help screen.\n";
		print "  [--sendonlyone] = Send only one command\n";
		print "  [--nocounteroutput] = Do not display counter as script runs\n";
		print "  [-t] = Test Mode\n";
		print "  [--debug] or [-v] = Verbose debug messages\n";
		print "  [--debugX] or [-vv] = Extra-verbose debug messages\n\n";
		exit 0;
	}
}
### end parsing run-time options ###

# Begin Parsing osdial.config file.
open(CONF, $conf{PATHconf}) || die "can't open " . $conf{PATHconf} . ": " . $! . "\n";
while (my $line = <CONF>) {
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	foreach my $key (qw( PATHhome PATHlogs PATHagi PATHweb PATHsounds PATHmontior
	  VARserver_ip VARDB_server VARDB_database VARDB_user VARDB_pass VARDB_port)) {
		if ($line =~ /^$key/) {
			$conf{$key} = $line;
			$conf{$key} =~ s/.*=//gi;
		}
	}
}
$conf{VARDB_port} = '3306' unless ($conf{VARDB_port});

# Connect to DB 
my $dbhA = DBI->connect("DBI:mysql:" . $conf{VARDB_database} . ":" . $conf{VARDB_server} . ":" . $conf{VARDB_port},
	$conf{VARDB_user}, $conf{VARDB_pass}) or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$servConf = getServerConfig($dbhA, $conf{VARserver_ip});
$SYSLOG = 1 if ($servConf->{vd_server_logs} =~ /Y/);
my $event_string='LOGGED INTO MYSQL SERVER ON 1 CONNECTION|';
eventLogger($conf{PATHlogs}, 'process', $event_string);

my $stmtA = "SELECT asterisk_version FROM servers WHERE server_ip='" . $conf{VARserver_ip} . "';";
my $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
my $DBasterisk_version = ($sthA->fetchrow_array)[0];
$sthA->finish();

my $one_day_interval = 90;		# 1 day loops for 3 months
while ($one_day_interval > 0) {
	my $endless_loop = 1728000;		# 2 days at .10 seconds per loop
	my $affected_rows;
	my $NEW_actions;
	while ($endless_loop > 0) {
		my $stmtA = "SELECT SQL_NO_CACHE count(*) FROM osdial_manager WHERE server_ip='" . $conf{VARserver_ip} . "' AND status='NEW';";
	    	my $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
		$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
	 	my $NEW_actions = ($sthA->fetchrow_array)[0];
		print STDERR $NEW_actions . " NEW Actions to send on server " . $conf{VARserver_ip} . "      $endless_loop\n" if ($DB);
	    	$sthA->finish();

		if ($NEW_actions) {
			my $stmtA = "UPDATE osdial_manager SET status='QUEUE' WHERE server_ip='" . $conf{VARserver_ip} . "' AND status='NEW' ORDER BY entry_date LIMIT 1;";
			$affected_rows = $dbhA->do($stmtA);
			print STDERR "rows updated to QUEUE: |$affected_rows|\n" if ($DB);
		} else {
			$affected_rows = 0;
		}

		if ($affected_rows) {
			my $stmtA = "SELECT SQL_NO_CACHE * FROM osdial_manager WHERE server_ip='" . $conf{VARserver_ip} . "' AND status='QUEUE' ORDER BY entry_date DESC LIMIT 1;";
			eventLogger($conf{'PATHlogs'}, 'process', "SQL_QUERY|" . $stmtA . "|");

			my $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;

		 	while (my $vdm = $sthA->fetchrow_hashref) {

				print STDERR $vdm->{man_id} . "|" . $vdm->{uniqueid} . "|" . $vdm->{channel} . "|" .
					$vdm->{action} . "|" . $vdm->{callerid} . "\n" if ($DB);

				my $originate_command = "Action: ". $vdm->{action} . "\n";
				$originate_command .= $vdm->{cmd_line_b} . "\n" if ($vdm->{cmd_line_b});
				$originate_command .= $vdm->{cmd_line_c} . "\n" if ($vdm->{cmd_line_c});
				$originate_command .= $vdm->{cmd_line_d} . "\n" if ($vdm->{cmd_line_d});
				$originate_command .= $vdm->{cmd_line_e} . "\n" if ($vdm->{cmd_line_e});
				$originate_command .= $vdm->{cmd_line_f} . "\n" if ($vdm->{cmd_line_f});
				$originate_command .= $vdm->{cmd_line_g} . "\n" if ($vdm->{cmd_line_g});
				$originate_command .= $vdm->{cmd_line_h} . "\n" if ($vdm->{cmd_line_h});
				$originate_command .= $vdm->{cmd_line_i} . "\n" if ($vdm->{cmd_line_i});
				$originate_command .= $vdm->{cmd_line_j} . "\n" if ($vdm->{cmd_line_j});
				$originate_command .= $vdm->{cmd_line_k} . "\n" if ($vdm->{cmd_line_k});
				$originate_command .= "\n";

				my $SENDNOW=1;
				if ($originate_command =~ /Action: Hangup|Action: Redirect/) {
					$SENDNOW=0;
					print STDERR "\n|checking for dead call before executing|" . $vdm->{callerid} . "|" . $vdm->{uniqueid} . "|\n" if ($DB);
					my $stmtB = "SELECT SQL_NO_CACHE count(*) FROM osdial_manager WHERE server_ip='" . $conf{VARserver_ip} . "' AND callerid='" . $vdm->{callerid} . "' AND status='DEAD';";
					my $sthB = $dbhA->prepare($stmtB) or die "preparing: ",$dbhA->errstr;
					$sthB->execute or die "executing: $stmtA ", $dbhA->errstr;
					my $dead_count = ($sthB->fetchrow_array)[0];
					$sthB->finish();
		   
					if ($dead_count) {
						print STDERR "\n|not sending command line is dead|" . $vdm->{callerid} . "|" . $vdm->{uniqueid} . "|\n" if ($DB);
					} else {
						$SENDNOW=1;
					}
				}

				my $event_string = "----BEGIN NEW COMMAND----\n$originate_command----END NEW COMMAND----\n";
				eventLogger($conf{'PATHlogs'}, 'process', $event_string);

				if ($SENDNOW) {
					my $cPATHlogs = $conf{PATHlogs};
					$cPATHlogs =~       s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_b} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_c} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_d} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_e} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_f} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_g} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_h} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_i} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_j} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;
					$vdm->{cmd_line_k} =~ s/([^A-Za-z0-9])/sprintf("%%%02X", ord($1))/seg;

					my $launch = $conf{PATHhome} . "/AST_send_action_child.pl";
					$launch .= " --SYSLOG" if ($SYSLOG);
					$launch .= " --PATHlogs=" . $cPATHlogs;
					$launch .= " --telnet_host=" . $servConf->{telnet_host};
					$launch .= " --telnet_port=" . $servConf->{telnet_port};
					$launch .= " --ASTmgrUSERNAME=" . $servConf->{ASTmgrUSERNAME};
					$launch .= " --ASTmgrSECRET=" . $servConf->{ASTmgrSECRET};
					$launch .= " --ASTmgrUSERNAMEsend=" . $servConf->{ASTmgrUSERNAMEsend};
					$launch .= " --asterisk_version=" . $DBasterisk_version;
					$launch .= " --man_id=" . $vdm->{man_id};
					$launch .= " --action=" . $vdm->{action};
					$launch .= " --cmd_line_b=" . $vdm->{cmd_line_b} if ($vdm->{cmd_line_b});
					$launch .= " --cmd_line_c=" . $vdm->{cmd_line_c} if ($vdm->{cmd_line_c});
					$launch .= " --cmd_line_d=" . $vdm->{cmd_line_d} if ($vdm->{cmd_line_d});
					$launch .= " --cmd_line_e=" . $vdm->{cmd_line_e} if ($vdm->{cmd_line_e});
					$launch .= " --cmd_line_f=" . $vdm->{cmd_line_f} if ($vdm->{cmd_line_f});
					$launch .= " --cmd_line_g=" . $vdm->{cmd_line_g} if ($vdm->{cmd_line_g});
					$launch .= " --cmd_line_h=" . $vdm->{cmd_line_h} if ($vdm->{cmd_line_h});
					$launch .= " --cmd_line_i=" . $vdm->{cmd_line_i} if ($vdm->{cmd_line_i});
					$launch .= " --cmd_line_j=" . $vdm->{cmd_line_j} if ($vdm->{cmd_line_j});
					$launch .= " --cmd_line_k=" . $vdm->{cmd_line_k} if ($vdm->{cmd_line_k});
					eventLogger($conf{'PATHlogs'}, 'launch', $launch . "  " .
						$vdm->{callerid} . " " . $vdm->{uniqueid} . " " . $vdm->{channel});


					$launch .= " >> " . $conf{PATHlogs} . "/action_send." . logDate() if ($SYSLOG);
					system($launch . ' &');

			#		$launch = "SENT " . $vdm->{man_id} . "  " . $vdm->{callerid} . ' ' . $vdm->{uniqueid} . ' ' . $vdm->{channel};
			#		eventLogger($conf{'PATHlogs'}, 'launch', $launch);;

					my $stmtA = "UPDATE osdial_manager set status='SENT' where man_id='" . $vdm->{man_id} . "'";
					print STDERR "\n|$stmtA|\n" if ($DB);
					$affected_rows = $dbhA->do($stmtA);

					$event_string = "SQL_QUERY|$stmtA|";
					eventLogger($conf{'PATHlogs'}, 'process', $event_string);
				} else {
					$stmtA = "UPDATE osdial_manager set status='DEAD' where man_id='" . $vdm->{man_id} . "'";
					print STDERR "\n|$stmtA|\n" if ($DB);
					$affected_rows = $dbhA->do($stmtA);
					$event_string="COMMAND NOT SENT, SQL_QUERY|$stmtA|";
					eventLogger($conf{'PATHlogs'}, 'process', $event_string);;
				}
			}
			$sthA->finish();
		}

		if ($affected_rows) {
			### sleep for 1 hundredth of a second
			usleep(1*10*1000);
		} else {
			### sleep for 5 hundredths of a second
			usleep(5*10*1000);
		}

		$endless_loop--;
		print STDERR "loop counter: |$endless_loop|\r" if($COUNTER_OUTPUT or $DB);

		### putting a blank file called "sendmgr.kill" in a directory will automatically safely kill this program
		if (-e $conf{PATHhome} . "/sendmgr.kill" or $sendonlyone) {
			unlink($conf{PATHhome} . "/sendmgr.kill");
			$endless_loop = 0;
			$one_day_interval = 0;
			print "\nPROCESS KILLED MANUALLY... EXITING\n\n";
		}

		my $running_listen = 0;
		if ($endless_loop =~ /0$/) {
			### Grab Server values from the database
			$servConf = getServerConfig($dbhA, $conf{VARserver_ip});

			print "checking to see if listener is dead |$sendonlyone|$running_listen|\n" if($COUNTER_OUTPUT or $DB);
			my $psoutput = `/usr/bin/pgrep AST_manager_li`;
			if ($psoutput) {
				$running_listen++;
				print "SEND RUNNING: |$psoutput]|\n" if ($DB);
			}
		}

	}

	print "DONE... Exiting... Goodbye... See you later... Not really, initiating next loop...$one_day_interval left\n" if($COUNTER_OUTPUT or $DB);
	$one_day_interval--;

}

$event_string='CLOSING DB CONNECTION|';
eventLogger($conf{'PATHlogs'}, 'process', $event_string);

$dbhA->disconnect();

print "DONE... Exiting... Goodbye... See you later... Really I mean it this time\n" if($COUNTER_OUTPUT or $DB);
exit 0;
# Program ends.


### Start of subs.

# getServerConfig usage:
#    getServerConfig($dbh, $serverIP);
# Requires:
#    $dbh      : Database handle to current open DB.
#    $serverIP : IP of server to get config for.
# Returns:
#    hashref with conents of table entry.
sub getServerConfig {
	my ($dbhA, $serverip) = @_;
	my $stmtA = "SELECT * FROM servers WHERE server_ip='" . $serverip ."';";
	my $sthA = $dbhA->prepare($stmtA) or die "preparing: " . $dbhA->errstr;
	$sthA->execute or die "executing: $stmtA " . $dbhA->errstr;
	my $servConf = $sthA->fetchrow_hashref;
	$SYSLOG = 1 if ($servConf->{vd_server_logs} =~ /Y/);
	$sthA->finish();
	return $servConf;
}

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
