#!/usr/bin/perl

### listloader_super.pl   version 2.0.4   *DBI-version*
### 
## Copyright (C) 2008  Matt Florell,Joe Johnson <vicidial@gmail.com>  LICENSE: AGPLv2
## Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>            LICENSE: AGPLv3
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
# CHANGES
# 60616-1548 - Added listID override feature to force all leads into same list
#            - Added gmt_offset_now lookup for each lead
# 60811-1232 - Changed to DBI
# 60811-1329 - changed to use /etc/osdial.conf for configs
# 60906-1056 - added filter of non-digits in alt_phone field
# 61110-1229 - added new USA-Canada DST scheme and Brazil DST scheme
# 61128-1207 - added postal code GMT lookup and duplicate check options
# 70205-1703 - Defaulted phone_code to 1 if not populated
# 70417-1059 - Fixed default phone_code bug
# 70510-1518 - Added campaign and system duplicate check and phonecode override
# 80428-0144 - UTF8 cleanup
#
# 090410-1149 - Added custom2 field.
# 090410-1549 - Added external_key field.
$|++;
use Date::Pcalc qw( Nth_Weekday_of_Month_Year Mktime This_Year );

### begin parsing run-time options ###
if (length($ARGV[0])>1)
{
	$i=0;
	while ($#ARGV >= $i)
	{
	$args = "$args $ARGV[$i]";
	$i++;
	}

	if ($args =~ /--help|-h/i)
	{
	print "allowed run time options:\n  [-forcelistid=1234] = overrides the listID given in the file with the 1234\n  [-h] = this help screen\n\n";

	exit;
	}
	else
	{
		if ($args =~ /-duplicate-check/i)
			{$dupcheck=1;}
		if ($args =~ /-duplicate-campaign-check/i)
			{$dupcheckcamp=1;}
		if ($args =~ /-duplicate-system-check/i)
			{$dupchecksys=1;}
		if ($args =~ /-postal-code-gmt/i)
			{$postalgmt=1;}
		if ($args =~ /--forcelistid=/i)
		{
		@data_in = split(/--forcelistid=/,$args);
			$forcelistid = $data_in[1];
			$forcelistid =~ s/ .*//gi;
		print "\n----- FORCE LISTID OVERRIDE: $forcelistid -----\n\n";
		}
		else
			{$forcelistid = '';}

		if ($args =~ /--forcephonecode=/i)
		{
		@data_in = split(/--forcephonecode=/,$args);
			$forcephonecode = $data_in[1];
			$forcephonecode =~ s/ .*//gi;
		print "\n----- FORCE PHONECODE OVERRIDE: $forcephonecode -----\n\n";
		}
		else
			{$forcephonecode = '';}

		if ($args =~ /--lead-file=/i)
		{
		@data_in = split(/--lead-file=/,$args);
			$lead_file = $data_in[1];
			$lead_file =~ s/ .*//gi;
	#	print "\n----- LEAD FILE: $lead_file -----\n\n";
		}
		else
			{$lead_file = './osdial_temp_file.xls';}
	}
}
### end parsing run-time options ###

use Spreadsheet::ParseExcel;
use Time::Local;
use DBI;	  


# default path to osdial.configuration file:
$PATHconf =		'/etc/osdial.conf';

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ( ($line =~ /^PATHhome/) && ($CLIhome < 1) )
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^PATHagi/) && ($CLIagi < 1) )
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ( ($line =~ /^PATHweb/) && ($CLIweb < 1) )
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ( ($line =~ /^PATHsounds/) && ($CLIsounds < 1) )
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ( ($line =~ /^PATHmonitor/) && ($CLImonitor < 1) )
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_server/) && ($CLIDB_server < 1) )
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_database/) && ($CLIDB_database < 1) )
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_user/) && ($CLIDB_user < 1) )
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_pass/) && ($CLIDB_pass < 1) )
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_port/) && ($CLIDB_port < 1) )
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

if (!$VARDB_port) {$VARDB_port='3306';}

$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


$vars=$ARGV[0];
@xls_fields=split(/\,/, $vars);

$secX = time();

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
$year = ($year + 1900);
$mon++;
if ($hour < 10) {$hour = "0$hour";}
if ($min < 10) {$min = "0$min";}
if ($sec < 10) {$sec = "0$sec";}
if ($mon < 10) {$mon = "0$mon";}
if ($mday < 10) {$mday = "0$mday";}
$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
$pulldate="$year-$mon-$mday $hour:$min:$sec";
$inSD = $pulldate0;
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmtA = "SELECT use_non_latin FROM system_settings;";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0)
	{
	@aryA = $sthA->fetchrow_array;
	$non_latin		=		"$aryA[0]";
	}
$sthA->finish();
##### END SETTINGS LOOKUP #####
###########################################


if ($non_latin > 0) {$affected_rows = $dbhA->do("SET NAMES 'UTF8'");}

### Grab Server values from the database
$stmtA = "SELECT local_gmt FROM servers where server_ip = '$server_ip';";
$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows=$sthA->rows;
$rec_count=0;
while ($sthArows > $rec_count)
	{
	@aryA = $sthA->fetchrow_array;
	$DBSERVER_GMT		=		"$aryA[0]";
	if ($DBSERVER_GMT)				{$SERVER_GMT = $DBSERVER_GMT;}
	$rec_count++;
	}
$sthA->finish();

	$LOCAL_GMT_OFF = $SERVER_GMT;
	$LOCAL_GMT_OFF_STD = $SERVER_GMT;

if ($isdst) {$LOCAL_GMT_OFF++;} 
if ($DB) {print "SEED TIME  $secX      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n";}



$total=0; $good=0; $bad=0;
open(STMT_FILE, "> $PATHlogs/listloader_stmts.txt");

$oBook = Spreadsheet::ParseExcel::Workbook->Parse("$lead_file");
my($iR, $iC, $oWkS, $oWkC);

foreach $oWkS (@{$oBook->{Worksheet}}) {
	for($iR = 0 ; defined $oWkS->{MaxRow} && $iR <= $oWkS->{MaxRow} ; $iR++) {

		$entry_date =			"$pulldate";
		$modify_date =			"";
		$status =				"NEW";
		$user =					"";
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[0]];
		if ($oWkC) {$vendor_lead_code=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[1]];
		if ($oWkC) {$source_code=$oWkC->Value; }
		$source_id=$source_code;
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[2]];
		if ($oWkC) {$list_id=$oWkC->Value; }
		$gmt_offset =			'0';
		$called_since_last_reset='N';
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[3]];
		if ($oWkC) {$phone_code=$oWkC->Value; }
		$phone_code=~s/[^0-9]//g;
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[4]];
		if ($oWkC) {$phone_number=$oWkC->Value; }
		$phone_number=~s/[^0-9]//g;
			$USarea = 			substr($phone_number, 0, 3);
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[5]];
		if ($oWkC) {$title=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[6]];
		if ($oWkC) {$first_name=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[7]];
		if ($oWkC) {$middle_initial=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[8]];
		if ($oWkC) {$last_name=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[9]];
		if ($oWkC) {$address1=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[10]];
		if ($oWkC) {$address2=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[11]];
		if ($oWkC) {$address3=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[12]];
		if ($oWkC) {$city=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[13]];
		if ($oWkC) {$state=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[14]];
		if ($oWkC) {$province=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[15]];
		if ($oWkC) {$postal_code=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[16]];
		if ($oWkC) {$country_code=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[17]];
		if ($oWkC) {$gender=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[18]];
		if ($oWkC) {$date_of_birth=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[19]];
		if ($oWkC) {$alt_phone=$oWkC->Value; }
		$alt_phone=~s/[^0-9]//g;
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[20]];
		if ($oWkC) {$email=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[21]];
		if ($oWkC) {$custom1=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[22]];
		if ($oWkC) {$comments=$oWkC->Value; }
		$comments=~s/^\s*(.*?)\s*$/$1/;
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[23]];
		if ($oWkC) {$custom2=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[24]];
		if ($oWkC) {$external_key=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[25]];
		if ($oWkC) {$cost=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[26]];
		if ($oWkC) {$org=$oWkC->Value; }
		$oWkC = $oWkS->{Cells}[$iR][$xls_fields[27]];
		if ($oWkC) {$org_title=$oWkC->Value; }


		
		if (length($forcelistid) > 0)
			{
			$list_id =	$forcelistid;		# set list_id to override value
			}
		if (length($forcephonecode) > 0)
			{
			$phone_code =	$forcephonecode;	# set phone_code to override value
			}

		##### Check for duplicate phone numbers in osdial_list table entire database #####
		if ($dupchecksys > 0)
			{
			$dup_lead=0;
			$stmtA = "select count(*) from osdial_list where phone_number='$phone_number';";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$dup_lead = $aryA[0];
				$dup_lead_list=$list_id;
				}
			$sthA->finish();
			if ($dup_lead < 1)
				{
				if ($phone_list =~ /\|$phone_number$US$list_id\|/)
					{$dup_lead++;}
				}
			}
		##### Check for duplicate phone numbers in osdial_list table for one list_id #####
		if ($dupcheck > 0)
			{
			$dup_lead=0;
			$stmtA = "select list_id from osdial_list where phone_number='$phone_number' and list_id='$list_id' limit 1;";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
			$sthArows=$sthA->rows;
			if ($sthArows > 0)
				{
				@aryA = $sthA->fetchrow_array;
				$dup_lead_list = $aryA[0];
				$dup_lead++;
				}
			$sthA->finish();
			if ($dup_lead < 1)
				{
				if ($phone_list =~ /\|$phone_number$US$list_id\|/)
					{$dup_lead++;}
				}
			}
		##### Check for duplicate phone numbers in osdial_list table for all lists in a campaign #####
		if ($dupcheckcamp > 0)
			{
			$dup_lead=0;
			$dup_lists='';

			$stmtA = "select count(*) from osdial_lists where list_id='$list_id';";
				if($DBX){print STDERR "\n|$stmtA|\n";}
			$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
			$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				@aryA = $sthA->fetchrow_array;
				$ci_recs = $aryA[0];
			$sthA->finish();
			if ($ci_recs > 0)
				{
				$stmtA = "select campaign_id from osdial_lists where list_id='$list_id';";
					if($DBX){print STDERR "\n|$stmtA|\n";}
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					@aryA = $sthA->fetchrow_array;
					$dup_camp = $aryA[0];
				$sthA->finish();

				$stmtA = "select list_id from osdial_lists where campaign_id='$dup_camp';";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$dup_lists .=	"'$aryA[0]',";
					$rec_count++;
					}
				$sthA->finish();

				chop($dup_lists);
				$stmtA = "select list_id from osdial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
				$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
				$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
				$sthArows=$sthA->rows;
				$rec_count=0;
				while ($sthArows > $rec_count)
					{
					@aryA = $sthA->fetchrow_array;
					$dup_lead_list =	"'$aryA[0]',";
					$rec_count++;
					$dup_lead=1;
					}
				$sthA->finish();
				}
			if ($dup_lead < 1)
				{
				if ($phone_list =~ /\|$phone_number$US$list_id\|/)
					{$dup_lead++;}
				}
			}

		if ( (length($phone_number)>6) && ($dup_lead < 1) )
			{
			$phone_list .= "$phone_number$US$list_id|";
			$postalgmt_found=0;
			if (length($phone_code)<1) {$phone_code = '1';}

			if ( ($postalgmt > 0) && (length($postal_code)>4) )
				{
				if ($phone_code =~ /^1$/)
					{
					$stmtA = "select * from osdial_postal_codes where country_code='$phone_code' and postal_code LIKE \"$postal_code%\";";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[2];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[3];
						$dst_range =	$aryA[4];
						$PC_processed++;
						$rec_count++;
						$postalgmt_found++;
						if ($DBX) {print "     Postal GMT record found for $postal_code: |$gmt_offset|$dst|$dst_range|\n";}
						}
					$sthA->finish();
					}
				}
			if ($postalgmt_found < 1)
				{
				$PC_processed=0;
				### UNITED STATES ###
				if ($phone_code =~ /^1$/)
					{
					$stmtA = "select * from osdial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[4];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[5];
						$dst_range =	$aryA[6];
						$PC_processed++;
						$rec_count++;
						}
					$sthA->finish();
					}
				### MEXICO ###
				if ($phone_code =~ /^52$/)
					{
					$stmtA = "select * from osdial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[4];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[5];
						$dst_range =	$aryA[6];
						$PC_processed++;
						$rec_count++;
						}
					$sthA->finish();
					}
				### AUSTRALIA ###
				if ($phone_code =~ /^61$/)
					{
					$stmtA = "select * from osdial_phone_codes where country_code='$phone_code' and state='$state';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[4];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[5];
						$dst_range =	$aryA[6];
						$PC_processed++;
						$rec_count++;
						}
					$sthA->finish();
					}
				### ALL OTHER COUNTRY CODES ###
				if (!$PC_processed)
					{
					$stmtA = "select * from osdial_phone_codes where country_code='$phone_code';";
						if($DBX){print STDERR "\n|$stmtA|\n";}
					$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
					$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
					$sthArows=$sthA->rows;
					$rec_count=0;
					while ($sthArows > $rec_count)
						{
						@aryA = $sthA->fetchrow_array;
						$gmt_offset =	$aryA[4];  $gmt_offset =~ s/\+| //gi;
						$dst =			$aryA[5];
						$dst_range =	$aryA[6];
						$PC_processed++;
						$rec_count++;
						}
					$sthA->finish();
					}
				}

				my $AC_GMT_diff = ($gmt_offset - $LOCAL_GMT_OFF_STD);
				my $AC_localtime = ($secX + (3600 * $AC_GMT_diff));
					
				if ($dst_range =~ /[FSTL][MS][FMASON]-[FSTL][MS][FMASON]/) {
					my $dstval = dstcalc($dst_range,$AC_localtime);
					$gmt_offset++ if ($dstval);
				} else {
					print "     No DST Method Found.   DST: 0\n" if ($DBX);
				}

			if ($multi_insert_counter > 8) {
				### insert good deal into pending_transactions table ###
				$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00','$org','$org_title');";
				$affected_rows = $dbhA->do($stmtZ);
				print STMT_FILE $stmtZ."\r\n";
				$multistmt='';
				$multi_insert_counter=0;

			} else {
				$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00','$org','$org_title'),";
				$multi_insert_counter++;
			}

			$good++;
		} else {
			if ($bad < 10000) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| $dup_lead_list</font><b>\n";}
			$bad++;
		}
		$total++;
		if ($total%100==0) {
			print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup_lead, $postalgmt_found)</script>";
			sleep(1);
#			flush();
		}
	}
}

if ($multi_insert_counter > 0) {
	$stmtZ = "INSERT INTO osdial_list values ".substr($multistmt, 0, -1).";";
	$affected_rows = $dbhA->do($stmtZ);
	print STMT_FILE $stmtZ."\r\n";
}

print "<BR><BR>Done</B> GOOD: $good &nbsp; &nbsp; &nbsp; BAD: $bad &nbsp; &nbsp; &nbsp; TOTAL: $total</font></center>";

exit;



sub dstcalc {
	my ($method,$time) = @_;

	my $nlet = { 'F'=>1, 'S'=>2, 'T'=>3, 'L'=>5 };
	my $dlet = { 'M'=>1, 'S'=>7 };
	my $mlet = { 'F'=>2, 'M'=>3, 'A'=>4, 'S'=>'9','O'=>10, 'N'=>11 };

	my ($start,$end) = split(/\-/,$method);
	my ($sn,$sd,$sm) = split(//,$start);
	my ($en,$ed,$em) = split(//,$end);

	my $sy = This_Year($time);
	my $ey = This_Year($time);
	$ey++ if ($mlet->{$em}<$mlet->{$sm});

	my ($dsy,$dsm,$dsd);
	unless (($dsy,$dsm,$dsd) = Nth_Weekday_of_Month_Year($sy,$mlet->{$sm},$dlet->{$sd},$nlet->{$sn})) {
		($dsy,$dsm,$dsd) = Nth_Weekday_of_Month_Year($sy,$mlet->{$sm},$dlet->{$sd},$nlet->{$sn}-1);
	}
	my $dststart = Mktime($dsy,$dsm,$dsd,2,0,0);

	my ($dey,$dem,$ded);
	unless (($dey,$dem,$ded) = Nth_Weekday_of_Month_Year($ey,$mlet->{$em},$dlet->{$ed},$nlet->{$en})) {
		($dey,$dem,$ded) = Nth_Weekday_of_Month_Year($ey,$mlet->{$em},$dlet->{$ed},$nlet->{$en}-1);
	}
	my $dstend = Mktime($dey,$dem,$ded,2,0,0);

	my $dstval=0;
	$dstval++ if ($time >= $dststart && $time <= $dstend);
	print "    $method DST: $dsy-$dsm-$dsd 02:00:00 to $dey-$dsm-$dsd 02:00:00  DSTVAL: $dstval\n" if ($DBX);

	return $dstval;
}
