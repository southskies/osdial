#!/usr/bin/perl
#
# AST_cleanup_agent_log.pl
#
## Copyright (C) 2011  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
use strict;
use OSDial;
$|++;

my $prog = "AST_cleanup_agent_log.pl";

my $DB=0;
my $DBX=0;
my $osdial = OSDial->new('DB'=>$DB);

my $lastdate = $osdial->get_date(time()-(60*60*24*2)) . ' 00:00:00';

my $i=0;
if ($DB) {print " - cleaning up pause time\n";}
### Grab any pause time record greater than 43999
my $stmtA = "SELECT SQL_NO_CACHE agent_log_id,pause_epoch,wait_epoch FROM osdial_agent_log WHERE event_time>='$lastdate' AND pause_sec>43999;";
if ($DBX) {print "$stmtA\n";}
while (my $sret = $osdial->sql_query($stmtA)) {
	my $DBout='';
	my $agent_log_id = $sret->{'agent_log_id'};
	my $pause_epoch  = $sret->{'pause_epoch'};
	my $wait_epoch   = $sret->{'wait_epoch'};
	my $pause_sec    = int($wait_epoch - $pause_epoch);
	if ($pause_sec < 0 or $pause_sec > 43999) {
		$DBout = "Override output: $pause_sec"; 
		$pause_sec = 0;
	}
	if ($DBX) {print "$i - $agent_log_id     |$wait_epoch|$pause_epoch|$pause_sec|$DBout|\n";}
		   
	$stmtA = "UPDATE osdial_agent_log SET pause_sec='$pause_sec' WHERE event_time>='$lastdate' AND agent_log_id='$agent_log_id';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$osdial->sql_execute($stmtA,'B');
	$i++;
}
if ($DB) {print STDERR "     Pause times fixed: $i\n";}


my $i=0;
if ($DB) {print " - cleaning up wait time\n";}
### Grab any pause time record greater than 43999
my $stmtA = "SELECT SQL_NO_CACHE agent_log_id,wait_epoch,talk_epoch FROM osdial_agent_log WHERE event_time>='$lastdate' AND wait_sec>43999;";
if ($DBX) {print "$stmtA\n";}
while (my $sret = $osdial->sql_query($stmtA)) {
	my $DBout='';
	my $agent_log_id = $sret->{'agent_log_id'};
	my $wait_epoch   = $sret->{'wait_epoch'};
	my $talk_epoch   = $sret->{'talk_epoch'};
	my $wait_sec     = int($talk_epoch - $wait_epoch);
	if ($wait_sec < 0 or $wait_sec > 43999) {
		$DBout = "Override output: $wait_sec"; 
		$wait_sec = 0;
	}
	if ($DBX) {print "$i - $agent_log_id     |$talk_epoch|$wait_epoch|$wait_sec|$DBout|\n";}
    
	$stmtA = "UPDATE osdial_agent_log SET wait_sec='$wait_sec' WHERE event_time>='$lastdate' AND agent_log_id='$agent_log_id';";
	if($DBX){print STDERR "\n|$stmtA|\n";}
	$osdial->sql_execute($stmtA,'B');
	$i++;
}
if ($DB) {print STDERR "     Wait times fixed: $i\n";}


my $i=0;
if ($DB) {print " - cleaning up talk time\n";}
### Grab any pause time record greater than 43999
$stmtA = "SELECT SQL_NO_CACHE agent_log_id,talk_epoch,dispo_epoch FROM osdial_agent_log WHERE event_time>='$lastdate' AND talk_sec>43999;";
if ($DBX) {print "$stmtA\n";}
while (my $sret = $osdial->sql_query($stmtA)) {
	my $DBout='';
	my $agent_log_id = $sret->{'agent_log_id'};
	my $talk_epoch   = $sret->{'talk_epoch'};
	my $dispo_epoch  = $sret->{'dispo_epoch'};
	my $talk_sec     = int($dispo_epoch - $talk_epoch);
	if ($talk_sec < 0 or $talk_sec > 43999) {
		$DBout = "Override output: $talk_sec"; 
		$talk_sec = 0;
	}
	if ($DBX) {print "$i - $agent_log_id     |$dispo_epoch|$talk_epoch|$talk_sec|$DBout|\n";}

	$stmtA = "UPDATE osdial_agent_log SET talk_sec='$talk_sec' WHERE event_time>='$lastdate' AND agent_log_id='$agent_log_id';";
	if($DBX){print STDERR "|$stmtA|\n";}
	$osdial->sql_execute($stmtA,'B');
	$i++;
}
if ($DB) {print STDERR "     Talk times fixed: $i\n";}



if ($DB) {print " - cleaning up dispo time\n";}
$stmtA = "UPDATE osdial_agent_log SET dispo_sec='0' WHERE event_time>='$lastdate' AND dispo_sec>43999;";
if($DBX){print STDERR "|$stmtA|\n";}
my $affected_rows = $osdial->sql_execute($stmtA,'B');
if ($DB) {print STDERR "     Bad Dispo times zeroed out: $affected_rows\n";}

exit 0;
