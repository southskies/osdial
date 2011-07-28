#!/usr/bin/perl
#
#  osdial_ast_hangup_all.pl: Reads asterisk realtime table and issues hangup
#                            for all channels found.
#
#  Copyright (C) 2011  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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


$|++;

use strict;
use OSDial;


my $DB = 0;
my $TEST = 0;
my $VERBOSE = 1;

# Identify myself.
my $prog = 'ast_hangup_all.pl';

my $osdial = OSDial->new('DB'=>$DB);

my $NOW_DATE = $osdial->get_datetime();
my $CIDdate = $NOW_DATE;
$CIDdate =~ s/[\-: ]//g;
my $HUqueryCID = "HUA" . $CIDdate;

my $c=0;
$osdial->sql_connect('B','dialer','127.0.0.1','3306','osdial','osdial1234');
while (my $sret = $osdial->sql_query("SELECT REPLACE(channel,'^3B',';') AS chanesc FROM channels;",'B')) {
	my $HUqueryCIDnew = sprintf('%s%03d',$HUqueryCID,$c++);

	my $huchan = $sret->{'chanesc'};
	my $stmtA = sprintf("INSERT INTO osdial_manager values('','','\%s','NEW','N','\%s','\%s','Hangup','\%s','Channel: \%s','','','','','','','','','');",$NOW_DATE,$osdial->{'VARserver_ip'},$huchan,$HUqueryCIDnew,$huchan);
	print "  Hanging up on channel: " . $sret->{'chanesc'} . "  QueryCID: " . $HUqueryCIDnew . "\n" if ($VERBOSE);
	print "      SQL: " . $stmtA . "\n" if ($DB);
	$osdial->sql_execute($stmtA,'A') if (!$TEST);
}

if (!$c) {
	print "  No Active Channels to Hangup!" . "\n" if ($VERBOSE);
}

exit 0;
