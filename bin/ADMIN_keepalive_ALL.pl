#!/usr/bin/perl
#
# ADMIN_keepalive_ALL.pl
#
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

$|++;
use strict;
use OSDial;
use Getopt::Long;
use Proc::ProcessTable;

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

##### list of codes for active_keepalives and what processes they correspond to
#	X - NO KEEPALIVE PROCESSES (use only if you want none to be keepalive)\n";
#	1 - AST_update\n";
#	2 - AST_send_listen\n";
#	3 - AST_VDauto_dial\n";
#	4 - AST_VDremote_agents\n";
#	5 - AST_VDadapt (If multi-server system, this must only be on one server)\n";
#	6 - FastAGI_log\n";
#	7 - AST_VDauto_dial_FILL\n";
#	8 - ip_relay for blind monitoring (deprecated)\n";
#	9 - OSDcampaign_stats (If multi-server system, this must only be on one server)\n";

if ($osdial->{'VARactive_keepalives'} =~ /X/) {
	print "X in active_keepalives, exiting...\n" if ($DB);
	exit;
}

my %keepalive;
my %running;

check_keepalive('1', 'AST_update.pl');
check_keepalive('2', 'AST_manager_send.pl');
check_keepalive('2', 'AST_manager_listen.pl');
check_keepalive('3', 'AST_VDauto_dial.pl');
check_keepalive('4', 'AST_VDremote_agents.pl');
check_keepalive('5', 'AST_VDadapt.pl');
check_keepalive('6', 'FastAGI_log.pl');
check_keepalive('7', 'AST_VDauto_dial_FILL.pl');
check_keepalive('9', 'OSDcampaign_stats.pl');


# check which processes are running and start if needed
my $proctab = new Proc::ProcessTable;
print "Processes " . scalar($proctab) . "\n" if ($DBX);
foreach my $proc (@{$proctab->table}) {
	# Only check process if it is run by perl and is not a thread.
	if ($proc->exec eq "/usr/bin/perl" and $proc->ppid != $proc->sess) {
		foreach my $progname (keys %keepalive) {
			if ($proc->cmndline =~ /$progname/) {
				$running{$progname}++;
				print $progname . " RUNNING:              |" . $proc->pid . "\n" if ($DB);
				if ($running{$progname}>1) {
					if ($progname eq "AST_manager_listen.pl") {
						print "Detected second instance of " . $progname. ", killing pid " . $proc->pid . ".\n" if ($DB);
						kill 9, $proc->pid unless ($TEST);
					}
				}
			}
		}
	}
}



# start keepalives programs as needed.
foreach my $progname (keys %keepalive) {
	if ($keepalive{$progname}) {
		unless ($running{$progname}) {
			print "Starting " . $progname . "...\n" if ($DB);
			my $screenid = $progname;
			$screenid =~ s/AST|VD|OSD|_|\.pl$//g;
			# add a '-L' to the screen command below to activate logging
			`/usr/bin/screen -d -m -S OSD$screenid $osdial->{'PATHhome'}/$progname` unless ($TEST);
		}
	}
}

print "DONE\n" if ($DB);
exit 0;



sub check_keepalive {
	my ($index, $progname) = @_;
	$keepalive{$progname}=0;
	$running{$progname}=0;
	$keepalive{$progname}=1 if ($osdial->{'VARactive_keepalives'} =~ /$index/);
	print $progname . " set to " . $keepalive{$progname} . "\n" if ($DB);
}
