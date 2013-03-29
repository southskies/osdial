#!/usr/bin/perl

# ADMIN_adjust_GMTnow_on_leads.pl   *DBI-version*
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
# program goes throught the osdial_list table and adjusts the gmt_offset_now
# field to change it to today's offset if needed because of Daylight Saving Time
#
# run every time you load leads into the osdial_list table
# 
#
# CHANGES
# 50810-1540 - Added database server variable definitions lookup
# 60615-1717 - Added definition GMT lookup from database not flat text file
# 60717-1045 - changed to DBI by Marin Blu
# 60717-1531 - changed to use /etc/osdial.conf for configuration
# 61108-1320 - added new DST schemes for USA/Canada change and changes in other countries
# 61110-1204 - added new DST scheme for Brazil
# 61128-1034 - added postal code GMT lookup option
# 61219-1106 - fixed updating for NULL gmt_offset records
# 70823-1633 - added ability to restrict by list_id
#

$|++;

use strict;
use DBI;	  
use OSDial;	  
use Date::Pcalc qw( Nth_Weekday_of_Month_Year Mktime This_Year );

my $Q = 0;
my $T = 0;
my $DB = 0;
my $DBX = 0;
my $searchPOST = 0;
my $singlelistid = '';

### begin parsing run-time options ###
if (length($ARGV[0])>1) {
	my $i=0;
	my $args='';
	while ($#ARGV >= $i) {
		$args = "$args $ARGV[$i]";
		$i++;
	}

	if ($args =~ /--help/i) {
		print "allowed run time options:\n";
		print "  [-q] = quiet\n";
		print "  [-t] = test\n";
		print "  [--debug] = debugging messages\n";
		print "  [--debugX] = Super debugging messages\n";
		print "  [--postal-code-gmt] = Attempt postal codes lookup for timezones\n";
		print "  [--singlelistid=XXX] = Only lookup and alter leads in one list_id\n";
		print "\n";

		exit;
	} else {
		if ($args =~ /-q/i) {
			$Q=1;
		}
		if ($args =~ /-t|--test/i) {
			$T=1;
			print "\n-----TESTING -----\n\n";
		}
		if ($args =~ /--debug/i) {
			$DB=1;
			print "\n-----DEBUGGING -----\n\n";
		}
		if ($args =~ /--debugX/i) {
			$DBX=1;
			print "\n----- SUPER-DUPER DEBUGGING -----\n\n";
		}
		if ($args =~ /--postal-code-gmt/i) {
			$searchPOST=1;
			print "\n----- DO POSTAL CODE LOOKUP -----\n\n";
		}
		if ($args =~ /-singlelistid=/i) {
			my @data_in = split(/-singlelistid=/,$args);
			$singlelistid = $data_in[1];
			print "\n----- SINGLE LISTID OVERRIDE: $singlelistid -----\n\n";
		}
	}
} else {
	print "no command line options set\n";
}

print "STARTING TIME ZONE DAYLIGHT SAVING TIME CALCULATION SCRIPT\n\n\n\n" if ($DB);

### end parsing run-time options ###


my $secX = time();
my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($secX);
$mon++;
$year = ($year + 1900);
$mon = "0$mon" if ($mon < 10);
$mday = "0$mday" if ($mday < 10);
$hour = "0$hour" if ($hour < 10);
$min = "0$min" if ($min < 10);
$sec = "0$sec" if ($sec < 10);
my $dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );


my $osd = OSDial->new('DB'=>$DB);

my $LOCAL_GMT_OFF = $osd->{server}{local_gmt};
my $LOCAL_GMT_OFF_STD = $osd->{server}{local_gmt};
$LOCAL_GMT_OFF++ if ($isdst);

print "SEED TIME  $secX      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n" if ($DB);

my $listSQL='';
my $XlistSQL='';
if (length($singlelistid)> 0) {
	$listSQL = sprintf(" WHERE list_id='%s' ",$osd->mres($singlelistid));
	$XlistSQL= sprintf(" AND list_id='%s' ",$osd->mres($singlelistid));
}

my $count=0;
my $phone_codes_list = {};
my $stmtA = sprintf("SELECT DISTINCT phone_code FROM osdial_list %s;",$listSQL);
print STDERR "|$stmtA|\n" if ($DBX);
while (my $sret = $osd->sql_query($stmtA)) {
	my $phone_code = $sret->{'phone_code'};
	$phone_code =~ s/ |\t|\r|\n//gi;
	$phone_code =~ s/^011|^0011|^001|^010|^000|^00|^0|^9011//i;
	if ($phone_code ne '' and length($phone_code)<5) {
		print "|".$sret->{'phone_code'}."|\n" if ($DBX);
		$count++;
		$phone_codes_list->{$sret->{'phone_code'}} = $phone_code;
	}
}
print " - Unique Country dial codes found: $count\n" if ($DB);

##### Put all country/are code records into an array for speed
my $stmtA = "SELECT * FROM osdial_phone_codes;";
print STDERR "|$stmtA|\n" if ($DBX);
my $count=0;
my $phone_codes = {};
while (my $sret = $osd->sql_query($stmtA)) {
	$phone_codes->{$sret->{country_code}}{$sret->{'areacode'}} = $sret;
	$count++;
}
print " - GMT phone codes records: $count\n" if ($DB);

##### Put all postal code records into an array for speed
my $stmtA = "SELECT * FROM osdial_postal_codes;";
print STDERR "|$stmtA|\n" if ($DBX);
my $count=0;
my $postal_codes = {};
if ($searchPOST > 0) {
	while (my $sret = $osd->sql_query($stmtA)) {
		$postal_codes->{$sret->{'country_code'}}{$sret->{'postal_code'}} = $sret;
		$count++;
	}
}
print " - GMT postal codes records: $count\n" if ($DB);

my $area_updated_count=0;
my $postal_updated_count=0;
foreach my $phone_code (sort {$phone_codes_list->{$a} <=> $phone_codes_list->{$b}} keys %{$phone_codes_list}) {
	my $country_code = $phone_codes_list->{$phone_code};
	$country_code = '1' if ($phone_code eq '');

	print "\nRUNNING LOOP FOR COUNTRY CODE: $country_code ($phone_code)\n" if ($DB);

	##### BEGIN RUN LOOP FOR EACH COUNTRY CODE/AREA CODE RECORD THAT IS INSIDE THIS COUNTRY CODE #####
	foreach my $areacode (sort keys %{$phone_codes->{$country_code}}) {
		my $area_GMT = $phone_codes->{$country_code}{$areacode}{'GMT_offset'};
		$area_GMT =~ s/\+//gi;
		$area_GMT = ($area_GMT + 0);
		my $area_GMT_method = $phone_codes->{$country_code}{$areacode}{'DST_range'};
		my $AC_match='';
		# look for state override flag in areacode field
		if ($areacode =~ /S/) {
			$areacode =~ s/\D//gi;
			$AC_match = sprintf(" AND phone_number LIKE '%s%%' AND state='%s' ",$areacode,$osd->mres($phone_codes->{$country_code}{$areacode}{'state'}));
			print "  AREA CODE STATE OVERRIDE: $phone_code  $country_code  $areacode  $phone_codes->{$country_code}{$areacode}{state}\n" if ($DB);
		} else {
			$AC_match = sprintf(" AND phone_number LIKE '%s%%' ",$areacode) unless ($areacode =~ /\*/);
		}
		print "PROCESSING THIS LINE: $phone_code  $country_code  $areacode\n" if ($DBX);
			
		my $stmtA = sprintf("SELECT count(*) AS reccount FROM osdial_list WHERE phone_code='%s' %s %s;",$phone_code,$AC_match,$XlistSQL);
		print STDERR "|$stmtA|\n" if ($DBX);
    		my $reccount=0;
		while (my $sret = $osd->sql_query($stmtA)) {
    			$reccount=$sret->{'reccount'};
		}
			
		unless ($reccount) {
			my $AC_GMT_diff = ($area_GMT - $LOCAL_GMT_OFF_STD);
			my $AC_localtime = ($secX + (3600 * $AC_GMT_diff));

			if ($area_GMT_method =~ /[FSTL][MS][FMASON]-[FSTL][MS][FMASON]/) {
				my $dstval = dstcalc($area_GMT_method,$AC_localtime);
				$area_GMT++ if ($dstval);
			} else {
				print "     No DST Method Found.   DST: 0\n" if ($DBX);
			}

			my $stmtA = sprintf("SELECT count(*) AS reccount FROM osdial_list WHERE phone_code='%s' %s AND gmt_offset_now!='%s' %s;",$phone_code,$AC_match,$area_GMT,$XlistSQL);
			print STDERR "|$stmtA|\n" if ($DBX);
    			my $reccount2=0;
			while (my $sret = $osd->sql_query($stmtA)) {
    				$reccount2=$sret->{'reccount2'};
			}
					
			if (!$reccount2) {
				print "   ALL GMT ALREADY CORRECT FOR : $phone_code  $country_code  $areacode   $area_GMT\n" if ($DBX);
			} else {
				my $stmtA = sprintf("UPDATE osdial_list SET gmt_offset_now='%s',modify_date=modify_date WHERE phone_code='%s' %s AND gmt_offset_now!='%s' %s;",$area_GMT,$phone_code,$AC_match,$area_GMT,$XlistSQL);
				print STDERR "|$stmtA|\n" if ($DBX);
				if (!$T) {
					my $affected_rows = $osd->sql_execute($stmtA);
					$area_updated_count += $affected_rows;
				}
				my $Preccount2 = sprintf("%8s", $reccount2);
				print " $Preccount2 records in $phone_code  $country_code  $areacode   updated to $area_GMT\n" if ($DB);
			}
		}
	}
	##### END RUN LOOP FOR EACH COUNTRY CODE/AREA CODE RECORD THAT IS INSIDE THIS COUNTRY CODE #####


	##### BEGIN RUN LOOP FOR EACH POSTAL CODE RECORD THAT IS INSIDE THIS COUNTRY CODE #####
	if ($searchPOST > 0) {
		print "POSTAL CODE RUN START...\n" if ($DB);
		foreach my $postal_code (sort keys %{$postal_codes->{$country_code}}) {
			my $area_GMT = $postal_codes->{$country_code}{$postal_code}{GMT_offset};
			$area_GMT =~ s/\+//gi;
			$area_GMT = ($area_GMT + 0);
			my $area_GMT_method = $postal_codes->{$country_code}{$postal_code}{DST_range};
			my $AC_match = sprintf(" AND postal_code LIKE '%s%%' ",$postal_code);
			print "PROCESSING THIS LINE: $phone_code  $country_code  $postal_code\n" if ($DBX);
					
			my $stmtA = sprintf("SELECT count(*) AS reccount FROM osdial_list WHERE phone_code='%s' %s %s;",$phone_code,$AC_match,$XlistSQL);
			print STDERR "|$stmtA|\n" if ($DBX);
    			my $reccount=0;
			while (my $sret = $osd->sql_query($stmtA)) {
    				$reccount=$sret->{'reccount'};
			}
				
			unless ($reccount) {
				my $AC_GMT_diff = ($area_GMT - $LOCAL_GMT_OFF_STD);
				my $AC_localtime = ($secX + (3600 * $AC_GMT_diff));
					
				if ($area_GMT_method =~ /[FSTL][MS][FMASON]-[FSTL][MS][FMASON]/) {
					my $dstval = dstcalc($area_GMT_method,$AC_localtime);
					$area_GMT++ if ($dstval);
				} else {
					print "     No DST Method Found.   DST: 0\n" if ($DBX);
				}

				my $stmtA = sprintf("SELECT count(*) AS reccount2 FROM osdial_list WHERE phone_code='%s' %s AND gmt_offset_now!='%s' %s;",$phone_code,$AC_match,$area_GMT,$XlistSQL);
				print STDERR "|$stmtA|\n" if ($DBX);
    				my $reccount2=0;
				while (my $sret = $osd->sql_query($stmtA)) {
    					$reccount2=$sret->{'reccount2'};
				}
							
				if (!$reccount2) {
					print "   ALL GMT ALREADY CORRECT FOR : $phone_code  $country_code  $postal_code   $area_GMT\n" if ($DBX);
				} else {
					my $stmtA = sprintf("UPDATE osdial_list SET gmt_offset_now='%s',modify_date=modify_date WHERE phone_code='%s' %s AND gmt_offset_now!='%s' %s;",$area_GMT,$phone_code,$AC_match,$area_GMT,$XlistSQL);
					print STDERR "|$stmtA|\n" if ($DBX);
					if (!$T) {
						my $affected_rows = $osd->sql_execute($stmtA);
						$postal_updated_count += $affected_rows;
					}
					my $Preccount2 = sprintf("%8s", $reccount2);
					print " $Preccount2 records in $phone_code  $country_code  $postal_code   updated to $area_GMT\n" if ($DB);
				}
			}
		}
		##### END RUN LOOP FOR EACH POSTAL CODE RECORD THAT IS INSIDE THIS COUNTRY CODE #####
	}
}


print "Postal Updates:    $postal_updated_count\n" if ($DB);
print "Area Code Updates: $area_updated_count\n" if ($DB);
print "\nDONE\n" if ($DB);
my $secy = time();
my $secz = ($secy - $secX);
my $minz = ($secz/60);
# calculate script runtime so far
print "\n     - process runtime      ($secz sec) ($minz minutes)\n";
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
