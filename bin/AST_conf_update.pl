#!/usr/bin/perl
#
# AST_conf_update.pl version 0.4   *DBI-version*
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
# DESCRIPTION:
# uses the Asterisk Manager interface and Net::MySQL to update whether a conference
# is still in use or not. If not in use 3 times in a row the extension in the 
# conferences DB record is erased freeing that conference to be used again
#
# SUMMARY:
# This program was designed for people using the Asterisk PBX with conferences
#
# This program should be in the cron running every minute (like AST_vm_update.pl)
# 
# For this program to work you need to have the "asterisk" MySQL database 
# created with the conferences table in it, also make sure
# that the account running this program has read/write/update/delete access 
# to that database
# 
# It is recommended that you run this program on the local Asterisk machine
#
# If this script is run ever minute there is a theoretical limit of 
# 600 conferences that it can check due to the wait interval. If you have 
# more than this either change the cron when this script is run or change the 
# wait interval below
#
# 50810-1532 - Added database server variable definitions lookup
# 50823-1456 - Added commandline arguments for debug at runtime
# 60717-1135 - changed to DBI by Marin Blu
# 60717-1536 - changed to use /etc/osdial.conf for configs
#

# constants
$DB=0;  # Debug flag, set to 0 for no debug messages per minute
$US='__';
@MT=();
$|++;

### begin parsing run-time options ###
if (length($ARGV[0])>1) {
	$i=0;
	while ($#ARGV >= $i) {
		$args = "$args $ARGV[$i]";
		$i++;
	}

	if ($args =~ /--help/i) {
		print "allowed run time options:\n  [-t] = test\n  [-debug] = verbose debug messages\n\n";
	} else {
		if ($args =~ /-debug/i) {
			$DB=1; # Debug flag
			print "-- DEBUGGING ENABLED --\n\n";
		}
		if ($args =~ /-t/i) {
			$TEST=1;
			$T=1;
		}
	}
} else {
	#print "no command line options set\n";
}
### end parsing run-time options ###

# default path to osdial.configuration file:
$PATHconf =		'/etc/osdial.conf';

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf) {
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ($line =~ /^PATHhome/ and $CLIhome < 1)
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ($line =~ /^PATHlogs/ and $CLIlogs < 1)
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ($line =~ /^PATHagi/ and $CLIagi < 1)
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ($line =~ /^PATHweb/ and $CLIweb < 1)
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ($line =~ /^PATHsounds/ and $CLIsounds < 1)
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ($line =~ /^PATHmonitor/ and $CLImonitor < 1)
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ($line =~ /^VARserver_ip/ and $CLIserver_ip < 1)
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ($line =~ /^VARDB_server/ and $CLIDB_server < 1)
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;}
	if ($line =~ /^VARDB_database/ and $CLIDB_database < 1)
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ($line =~ /^VARDB_user/ and $CLIDB_user < 1)
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ($line =~ /^VARDB_pass/ and $CLIDB_pass < 1)
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ($line =~ /^VARDB_port/ and $CLIDB_port < 1)
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	$i++;
}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

$VARDB_port='3306' if (!$VARDB_port);

use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl sleep command of less than one second
use DBI;
use Net::Telnet ();
	  
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;

### Grab Server values from the database
$stmtA = "SELECT telnet_host,telnet_port,ASTmgrUSERNAME,ASTmgrSECRET,ASTmgrUSERNAMEupdate,ASTmgrUSERNAMElisten,ASTmgrUSERNAMEsend,max_osdial_trunks,answer_transfer_agent,local_gmt,ext_context,asterisk_version FROM servers WHERE server_ip='$server_ip';";
print "|$stmtA|\n" if ($DB);
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
if ($sthArows > 0) {
	@aryA = $sthA->fetchrow_array;
	$DBtelnet_host		=	$aryA[0];
	$DBtelnet_port		=	$aryA[1];
	$DBASTmgrUSERNAME	=	$aryA[2];
	$DBASTmgrSECRET		=	$aryA[3];
	$DBASTmgrUSERNAMEupdate	=	$aryA[4];
	$DBASTmgrUSERNAMElisten	=	$aryA[5];
	$DBASTmgrUSERNAMEsend	=	$aryA[6];
	$DBmax_osdial_trunks	=	$aryA[7];
	$DBanswer_transfer_agent=	$aryA[8];
	$DBSERVER_GMT		=	$aryA[9];
	$DBext_context		=	$aryA[10];
	$DBasterisk_version	=	$aryA[11];
	if ($DBtelnet_host)		{$telnet_host = $DBtelnet_host;}
	if ($DBtelnet_port)		{$telnet_port = $DBtelnet_port;}
	if ($DBASTmgrUSERNAME)		{$ASTmgrUSERNAME = $DBASTmgrUSERNAME;}
	if ($DBASTmgrSECRET)		{$ASTmgrSECRET = $DBASTmgrSECRET;}
	if ($DBASTmgrUSERNAMEupdate)	{$ASTmgrUSERNAMEupdate = $DBASTmgrUSERNAMEupdate;}
	if ($DBASTmgrUSERNAMElisten)	{$ASTmgrUSERNAMElisten = $DBASTmgrUSERNAMElisten;}
	if ($DBASTmgrUSERNAMEsend)	{$ASTmgrUSERNAMEsend = $DBASTmgrUSERNAMEsend;}
	if ($DBmax_osdial_trunks)	{$max_osdial_trunks = $DBmax_osdial_trunks;}
	if ($DBanswer_transfer_agent)	{$answer_transfer_agent = $DBanswer_transfer_agent;}
	if ($DBSERVER_GMT)		{$SERVER_GMT = $DBSERVER_GMT;}
	if ($DBext_context)		{$ext_context = $DBext_context;}
}
$sthA->finish(); 

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}

$now_date_epoch = time();
$now_date = "$year-$mon-$mday $hour:$min:$sec";

##### Find date-time two hours in the past
$secX = time();
$TDtarget = ($secX - 7200);
($Tsec,$Tmin,$Thour,$Tmday,$Tmon,$Tyear,$Twday,$Tyday,$Tisdst) = localtime($TDtarget);
$Tyear = ($Tyear + 1900);
$Tmon++;
if ($Tmon < 10) {$Tmon = "0$Tmon";}
if ($Tmday < 10) {$Tmday = "0$Tmday";}
if ($Thour < 10) {$Thour = "0$Thour";}
if ($Tmin < 10) {$Tmin = "0$Tmin";}
if ($Tsec < 10) {$Tsec = "0$Tsec";}
$TDSQLdate = "$Tyear-$Tmon-$Tmday $Thour:$Tmin:$Tsec";
$TDnum = "$Tyear$Tmon$Tmday$Thour$Tmin$Tsec";




######################################################################
##### CLEAR osdial_conferences ENTRIES IN LEAVE-3WAY FOR MORE THAN TWO HOURS
######################################################################
@PTextensions=@MT; @PT_conf_extens=@MT; @PTmessages=@MT; @PTold_messages=@MT; @NEW_messages=@MT; @OLD_messages=@MT;
$stmtA = "SELECT SQL_NO_CACHE conf_exten FROM osdial_conferences WHERE server_ip='$server_ip' AND leave_3way='1' AND leave_3way_datetime<\"$TDSQLdate\";";
print "|$stmtA|\n" if ($DB);
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count) {
	@aryA = $sthA->fetchrow_array;
	$PT_conf_extens[$rec_count] =	 "$aryA[0]";
	print "|$PT_conf_extens[$rec_count]|$PTextensions[$rec_count]|\n" if ($DB);
	$rec_count++;
}
$sthA->finish();
$k=0;
while ($k < $rec_count) {
	$queryCID = "ULGC35$TDnum";

	$stmtA=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Command','%s','Command: %s','','','','','','','','','');",$now_date,$server_ip,$queryCID,'meetme kick '.$PT_conf_extens[$k].' all');
	$affected_rows = $dbhA->do($stmtA);

	$stmtA = "UPDATE osdial_conferences SET extension='',leave_3way='0' WHERE server_ip='$server_ip' AND conf_exten='$PT_conf_extens[$k]';";
	print STDERR "\n|$stmtA|\n" if ($DB);
	$affected_rows = $dbhA->do($stmtA);

	$k++;
}




######################################################################
##### CHECK osdial_conferences TABLE #####
######################################################################
@PTextensions=@MT; @PT_conf_extens=@MT; @PTmessages=@MT; @PTold_messages=@MT; @NEW_messages=@MT; @OLD_messages=@MT;
$stmtA = "SELECT SQL_NO_CACHE extension,conf_exten FROM osdial_conferences WHERE server_ip='$server_ip' AND leave_3way='1';";
print "|$stmtA|\n" if ($DB);
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count) {
	@aryA = $sthA->fetchrow_array;
	$PTextensions[$rec_count] = $aryA[0];
	$PT_conf_extens[$rec_count] = $aryA[1];
	print "|$PT_conf_extens[$rec_count]|$PTextensions[$rec_count]|\n" if ($DB);
	$rec_count++;
}
$sthA->finish(); 

$telnet_port = '5038' if (!$telnet_port);

### connect to asterisk manager through telnet
$t = new Net::Telnet (Port => $telnet_port, Prompt => '/.*[\$%#>] $/', Output_record_separator => '');
if (length($ASTmgrUSERNAMEsend) > 3) {
	$telnet_login = $ASTmgrUSERNAMEsend;
} else {
	$telnet_login = $ASTmgrUSERNAME;
}

$t->open($telnet_host); 
if ($DBasterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
	if ($DBasterisk_version =~ /^11\./) {
		$t->waitfor('/3\n$/');
	} elsif ($DBasterisk_version =~ /^10\./) {
		$t->waitfor('/2\n$/');
	} else {
		$t->waitfor('/1\n$/');
	}
	my($s, $usec) = gettimeofday();
	$t->print("Action: Login\nActionID: U$usec~Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
} else {
	$t->waitfor('/0\n$/');
	$t->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
}
$t->waitfor('/Authentication accepted/');


$i=0;
foreach(@PTextensions) {
	@list_channels=@MT;
	$t->buffer_empty;
	$COMMAND = "Action: Command\nCommand: meetme list $PT_conf_extens[$i]\n\n";
	print "|$PT_conf_extens[$i]|$COMMAND|\n" if ($DB);
	@list_channels = $t->cmd(String => "$COMMAND", Prompt => '/--END COMMAND-.*/'); 


	$j=0;
	$conf_empty[$i]=0;
	$conf_users[$i]={};
	foreach my $meetme (@list_channels) {
		print "|$meetme|\n" if ($DB);
		### mark all empty conferences and conferences with only one channel as empty
		$conf_empty[$i]++ if ($meetme =~ /No active conferences|No such conference/i);
		$conf_empty[$i]++ if ($meetme =~ /1 users in that conference/i);
		if ($meetme =~ /^User /i) {
			$meetme =~ s/User \#: //gi;
			$userno = $meetme;
			$userno =~ s/ .*$//;
			$conf_users[$i]->{$userno} = $meetme;
		}
		$j++;
	}

	if (scalar (keys %{$conf_users[$i]}) <= 2) {
		foreach my $cuser (keys %{$conf_users[$i]}) {
			$chan = $conf_users[$i]->{$cuser};
			$chan =~ s/^.*Channel: (\S+) .*$/$1/gi;
			if ($chan =~ /Local\/3$PT_conf_extens[$i]@/ and $conf_users[$i]->{$cuser} =~ /\(Listen only\)/) {
				$conf_empty[$i]++;
				$queryCID = "ULGC38$TDnum";
				$stmtA=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Command','%s','Command: %s','','','','','','','','','');",$now_date,$server_ip,$queryCID,'meetme kick '.$PT_conf_extens[$i].' '.$cuser);
				$affected_rows = $dbhA->do($stmtA);
				print STDERR "\n|$affected_rows|$stmtA|\n" if ($DB);
			}
		}
	}

	print "Meetme list $PT_conf_extens[$i]-  Exten:|$PTextensions[$i]| Empty:|$conf_empty[$i]|    " if ($DB);
	if (!$conf_empty[$i]) {
		print "CONFERENCE STILL HAS PARTICIPANTS, DOING NOTHING FOR THIS CONFERENCE\n" if ($DB);
		if ($PTextensions[$i] =~ /Xtimeout\d$/i) {
			$PTextensions[$i] =~ s/Xtimeout\d$//gi;
			$stmtA = "UPDATE osdial_conferences set extension='$PTextensions[$i]' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
			print STDERR "\n|$stmtA|\n" if ($DB);
			$affected_rows = $dbhA->do($stmtA);
		}
	} else {
		$NEWexten[$i] = $PTextensions[$i];
		$leave_3waySQL='1';
		$NEWexten[$i] =~ s/Xtimeout3$/Xtimeout2/gi if ($PTextensions[$i] =~ /Xtimeout3$/i);
		$NEWexten[$i] =~ s/Xtimeout2$/Xtimeout1/gi if ($PTextensions[$i] =~ /Xtimeout2$/i);
		if ($PTextensions[$i] =~ /Xtimeout1$/i) {
			$NEWexten[$i] = '';
			$leave_3waySQL='0';
		}
		$NEWexten[$i] .= 'Xtimeout3' if ($PTextensions[$i] !~ /Xtimeout\d$/i and length($PTextensions[$i])> 0);

		if ($NEWexten[$i] =~ /Xtimeout1$/i) {
			### Kick all participants if there are any left in the conference so it can be reused
			$queryCID = "ULGC36$TDnum";
			$stmtA=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Command','%s','Command: %s','','','','','','','','','');",$now_date,$server_ip,$queryCID,'meetme kick '.$PT_conf_extens[$i].' all');
			$affected_rows = $dbhA->do($stmtA);
			print STDERR "\n|$affected_rows|$stmtA|\n" if ($DB);
		}

		$stmtA = "UPDATE osdial_conferences set extension='$NEWexten[$i]',leave_3way='$leave_3waySQL' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
		print STDERR "\n|$stmtA|\n" if ($DB);
		$affected_rows = $dbhA->do($stmtA);
	}

	$i++;
	### sleep for 10 hundredths of a second
	usleep(1*100*1000);
}

$t->buffer_empty;
@hangup = $t->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
$t->buffer_empty;
$ok = $t->close;
sleep(1);





######################################################################
##### CHECK conferences TABLE #####
######################################################################
@PTextensions=@MT; @PT_conf_extens=@MT; @PTmessages=@MT; @PTold_messages=@MT; @NEW_messages=@MT; @OLD_messages=@MT;
$stmtA = "SELECT SQL_NO_CACHE extension,conf_exten FROM conferences WHERE server_ip='$server_ip' AND extension IS NOT NULL AND extension!='';";
print "|$stmtA|\n" if ($DB);
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count) {
	@aryA = $sthA->fetchrow_array;
	$PTextensions[$rec_count] =		 "$aryA[0]";
	$PT_conf_extens[$rec_count] =	 "$aryA[1]";
	print "|$PT_conf_extens[$rec_count]|$PTextensions[$rec_count]|\n" if ($DB);
	$rec_count++;
}
$sthA->finish(); 

$telnet_port = '5038' if (!$telnet_port);

### connect to asterisk manager through telnet
$t = new Net::Telnet (Port => $telnet_port, Prompt => '/.*[\$%#>] $/', Output_record_separator => '');
if (length($ASTmgrUSERNAMEsend) > 3) {
	$telnet_login = $ASTmgrUSERNAMEsend;
} else {
	$telnet_login = $ASTmgrUSERNAME;
}

$t->open($telnet_host); 
if ($DBasterisk_version =~ /^1\.6|^1\.8|^10\.|^11\./) {
	if ($DBasterisk_version =~ /^11\./) {
		$t->waitfor('/3\n$/');
	} elsif ($DBasterisk_version =~ /^10\./) {
		$t->waitfor('/2\n$/');
	} else {
		$t->waitfor('/1\n$/');
	}
	my($s, $usec) = gettimeofday();
	$t->print("Action: Login\nActionID: U$usec~Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
} else {
	$t->waitfor('/0\n$/');
	$t->print("Action: Login\nUsername: $telnet_login\nSecret: $ASTmgrSECRET\n\n");
}
$t->waitfor('/Authentication accepted/');


$i=0;
foreach(@PTextensions) {
	@list_channels=@MT;
	$t->buffer_empty;
	$COMMAND = "Action: Command\nCommand: meetme list $PT_conf_extens[$i]\n\n";
	print "|$PT_conf_extens[$i]|$COMMAND|\n" if ($DB);
	@list_channels = $t->cmd(String => "$COMMAND", Prompt => '/--END COMMAND-.*/'); 


	$j=0;
	$conf_empty[$i]=0;
	$conf_users[$i]={};
	foreach $meetme (@list_channels) {
		print "|$meetme|\n" if ($DB);
		$conf_empty[$i]++ if ($meetme =~ /No active conferences|No such conference/i);
		if ($meetme =~ /^User /i) {
			$meetme =~ s/User \#: //gi;
			$userno = $meetme;
			$userno =~ s/ .*$//;
			$conf_users[$i]->{$userno} = $meetme;
		}
		$j++;
	}

	print "Meetme list $PT_conf_extens[$i]-  Exten:|$PTextensions[$i]| Empty:|$conf_empty[$i]|    " if ($DB);
	if (!$conf_empty[$i]) {
		print "CONFERENCE STILL HAS PARTICIPANTS, DOING NOTHING FOR THIS CONFERENCE\n" if ($DB);
		if ($PTextensions[$i] =~ /Xtimeout\d$/i) {
			$PTextensions[$i] =~ s/Xtimeout\d$//gi;
			$stmtA = "UPDATE conferences set extension='$PTextensions[$i]' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
			print STDERR "\n|$stmtA|\n" if ($DB);
			$affected_rows = $dbhA->do($stmtA);
		}
	} else {
		$NEWexten[$i] = $PTextensions[$i];
		$NEWexten[$i] =~ s/Xtimeout3$/Xtimeout2/gi if ($PTextensions[$i] =~ /Xtimeout3$/i);
		$NEWexten[$i] =~ s/Xtimeout2$/Xtimeout1/gi if ($PTextensions[$i] =~ /Xtimeout2$/i);
		$NEWexten[$i] = '' if ($PTextensions[$i] =~ /Xtimeout1$/i);
		$NEWexten[$i] .= 'Xtimeout3' if ($PTextensions[$i] !~ /Xtimeout\d$/i and length($PTextensions[$i])> 0);

		$stmtA = "UPDATE conferences set extension='$NEWexten[$i]' where server_ip='$server_ip' and conf_exten='$PT_conf_extens[$i]';";
		print STDERR "\n|$stmtA|\n" if ($DB);
		$affected_rows = $dbhA->do($stmtA);
	}

	$i++;
	### sleep for 10 hundredths of a second
	usleep(1*100*1000);
}


$t->buffer_empty;
@hangup = $t->cmd(String => "Action: Logoff\n\n", Prompt => "/.*/"); 
$t->buffer_empty;
$ok = $t->close;

$dbhA->disconnect();

print "DONE... Exiting... Goodbye... See you later... \n" if ($DB);

exit;




