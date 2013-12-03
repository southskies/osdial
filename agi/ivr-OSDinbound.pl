#!/usr/bin/perl
#
# ivr-OSDinbound.pl
#
## Copyright (C) 2013  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
# You need to put lines similar to those below in your extensions.conf file:
# ; Below are the parameters needed for the script to be run properly
# ; 1. the method of call handling for the script:
# ; 	- CID - 	CID received, add record with phone number
# ; 	- CIDLOOKUP - 	Lookup CID to find record in whole system
# ; 	- CIDLOOKUPRL -	Restrict lookup to one list
# ; 	- CIDLOOKUPRC -	Restrict lookup to one campaign's lists
# ;     - CLOSER -      Closer calls from osdial fronters
# ; 	- ANI - 	ANI received, add record with phone number
# ; 	- ANILOOKUP - 	Lookup ANI to find record in whole system
# ; 	- ANILOOKUPRL -	Restrict lookup to one list
# ; 	- 3DIGITID - 	Enter 3 digit code to go to agent
# ; 	- 4DIGITID - 	Enter 4 digit code to go to agent
# ; 	- 5DIGITID - 	Enter 5 digit code to go to agent
# ; 	- 10DIGITID - 	Enter 10 digit code to go to agent
# ; 2. the method of searching for an available agent:
# ; 	- LO - Load Balance Overflow only (priority to home server)
# ; 	- LB - <default> Load Balance total system
# ; 	- SO - Home server only
# ; 3. the full name of the IN GROUP to be used in osdial for the inbound call
# ; 4. the phone number that was called, for the log entry
# ; 5. the callerID or lead_id of the person that called(usually overridden)
# ; 6. the park extension audio file name if used
# ; 7. the status of the call initially(usually not used)
# ; 8. the list_id to insert the new lead under if it is new (and CID/ANI available)
# ; 9. the phone dialing code to insert with the new lead if new (and CID/ANI available)
# ; 10. the campaign_id to search within lists if CIDLOOKUPRC
#
# ;inbound osdial calls:
#exten => 1234,1,Answer                  ; Answer the line
#exten => 1234,2,AGI(agi-VDAD_ALL_inbound.agi,CID-----LB-----INB-----7274515134-----Closer-----park----------999-----1-----OUTB)
#exten => 1234,3,Hangup
$|++;

use strict;
use OSDial;
use IO::Select;
use Data::Dumper;
use Time::HiRes ('gettimeofday','usleep','sleep');

my $script='ivr-OSDinbound.agi';
my $DB=0;

my $osdial = OSDial->new('DB'=>0);
$osdial->{_agi}{mod}=$script;

my $s = IO::Select->new();
$s->add(\*STDIN);

my $params = {};
my $vars = {};
my $streamsize = {};
my $streampos = {};
my @fullin;
my $in_enterpin=0;
my $docallback=0;
my $incallback=0;
my $inloop=0;
my $inhold=0;
my $inprompt=0;
my $inwelcome=0;
my $loopwelcome=0;
my $inafterhours=0;
my $inaftercall=0;
my $start_moh=2;
my $hold='conf';
my $welcome='';

sub infunc {
	my @ins = (undef,time());
	@fullin=@ins;
	my $input=undef;
	if ($s->can_read(1)) {
		$input = <STDIN>;
		chomp($input);
		print STDERR "Got '$input' from STDIN\n" if($DB);
		@ins = split(/,/,$input);
		@fullin=@ins;
		if ($ins[0] eq 'P') {
			$params->{'script'} = $ins[2];
			$params->{'args'} = $ins[3];
		} elsif ($ins[0] eq 'G') {
			foreach my $gvar (@ins) {
				if ($gvar =~ /=/) {
					my ($key,$val) = split(/=/,$gvar);
					$key =~ s/^IVR_//;
					$key = lc($key);
					$vars->{$key}=$val;
					$osdial->{_agi}{$key}=$val;
				}       
			}       
		} elsif ($ins[0] eq 'E') {
			exit 0; 
		} elsif ($ins[0] eq 'H') {
			exit 0; 
		} elsif ($ins[0] eq 'B') {
			$streampos->{$ins[2]} = 0;
			$streamsize->{$ins[2]} = $ins[3];
		} elsif ($loopwelcome==1 and $ins[0] eq 'F' and $ins[2] eq $welcome) {
			$loopwelcome=0;
		} elsif ($inhold==1 and $ins[0] eq 'F' and $ins[2] eq $hold) {
			#print "A,$hold\n";
			$streampos->{$ins[2]} = 0;
			$start_moh=2;
			$inhold=0;
		} elsif ($incallback==1 and $ins[0] eq 'F' and $ins[2] eq 'sip-silence') {
			$incallback=0;
			print "H,callback\n";
		} elsif ($inaftercall==1 and $ins[0] eq 'F' and $ins[2] eq 'sip-silence') {
			$inaftercall=0;
		} elsif ($inafterhours==1 and $ins[0] eq 'F' and $ins[2] eq 'sip-silence') {
			$inafterhours=0;
		} elsif ($inwelcome==1 and $ins[0] eq 'F' and $ins[2] eq 'sip-silence') {
			$inwelcome=0;
		} elsif ($inprompt==1 and $ins[0] eq 'F' and $ins[2] eq 'sip-silence') {
			$inprompt=0;
			$start_moh=2;
		} elsif ($inhold==1 and $start_moh==1 and $ins[0] eq 'F') {
			$start_moh=2;
		} elsif ($ins[0] eq 'D') {
			$streampos->{$ins[2]} = 0;
			$inhold=0 if ($inhold==1 and $ins[2] eq $hold);
		} elsif ($ins[0] eq 'T') {
			$streampos->{$ins[2]} = $ins[4];
			$inhold=0 if ($inhold==1 and $ins[2] eq $hold);
		} elsif ($inafterhours) {
			$inafterhours=0;
			if ($ins[0] eq '*') {
				return $ins[0];
			}
		} elsif ($inwelcome) {
			$inwelcome=0;
			if ($ins[0] eq '*') {
				return $ins[0];
			}
		} elsif ($inloop) {
			$inloop=0;
			if ($ins[0] eq '*') {
				return $ins[0];
			}
		} elsif ($incallback) {
			if ($ins[0] =~ /[0-9\*#]/) {
				return $ins[0];
			}
		} elsif ($in_enterpin) {
			if ($ins[0] =~ /[0-9]/) {
				return $ins[0];
			}
		}       
	}       
	return $ins[0];
}

### default number of seconds to wait until you drop a waiting call
my $DROP_TIME = 360;

my $start_epoch = time();
my $now_date_epoch = $start_epoch;

my $now_date = $osdial->get_datetime($now_date_epoch);
my $start_time = $now_date;

my $CIDdate = $now_date;
$CIDdate =~ s/\D//g;
$CIDdate = substr($CIDdate,4,10);

my $tsSQLdate = $now_date;
$tsSQLdate =~ s/\D//g;

my $SQLdate = $now_date;
my $SQLdateBEGIN = $now_date;

my $hm = $now_date;
$hm =~ s/\D//g;
$hm = substr($hm,8,4);
$hm = ($hm + 0);

my $hms = $now_date;
$hms =~ s/\D//g;
$hms = substr($hms,8,6);
$hms = ($hms + 0);


my $BD_epoch = ($now_date_epoch - 5);
my $BDtsSQLdate = $osdial->get_datetime($BD_epoch);
$BDtsSQLdate =~ s/\D//g;


my $stmtA;
my $affected_rows;
my $agi_string='';


my $rec_countCUSTDATA=0;
my $rec_countWAIT=0;

my $pin;

my $call_handle_method;
my $agent_search_method;
my $channel_group;
my $inbound_number;
my $outbound_number;
my $parked_by;
my $park_extension;
my $status;
my $list_id;
my $phone_code;
my $Scampaign_id;
my $referring_extension;
my $phone_number;
my $fronter;
my $CIDlead_id;


print STDERR "Started Inbound Group.\n" if ($DB);

my $newin;
print "T,".time()."\n";
print "P,".time()."\n";
#print "O,noautoclear\n";
print "G,IVR_ARGS\n";
while ($newin = infunc() and !($newin eq 'G')) {}
print "G,IVR_UNIQUEID,IVR_CHANNEL,IVR_ACCOUNTCODE\n";
while ($newin = infunc() and !($newin eq 'G')) {}
print "G,IVR_CONTEXT,IVR_PRIORITY,IVR_EXTENSION\n";
while ($newin = infunc() and !($newin eq 'G')) {}
print "G,IVR_CALLERIDNAME,IVR_CALLERID\n";
while ($newin = infunc() and !($newin eq 'G')) {}



### begin parsing run-time options ###
if (length($vars->{'args'})>1) {
	$osdial->agi_output("Perl Environment Dump:");

	### list of command-line array arguments:
	my @ARGV_vars = split(/-----/, $vars->{'args'});

	my $i=0;
	foreach my $arg (@ARGV_vars) {
		$osdial->agi_output($i."|".$arg);
		$i++;
	}
	
	$call_handle_method = $ARGV_vars[0];
	$agent_search_method = $ARGV_vars[1];
	$channel_group = $ARGV_vars[2];
	$inbound_number = $ARGV_vars[3];
	$parked_by = $phone_number = $ARGV_vars[4];
	$park_extension = $ARGV_vars[5];
	$status = $ARGV_vars[6];
	$list_id = $ARGV_vars[7];
	$phone_code = $ARGV_vars[8];
	$Scampaign_id = $ARGV_vars[9];

	$inbound_number =~ s/^\+1//;
	$phone_number =~ s/^\+1//;
	$park_extension="8301" if ($park_extension eq "");
}



if ($call_handle_method =~ /^CLOSER/) {
	### allow for internal PRI/IAX/SIP transfer data string "90009*CL_uk3survy_*8301*10000123*universal***"
	if ($vars->{extension} =~ /^900\d\d\*|^9900\d\d\*/) {
		my @EXT_vars = split(/\*/, $vars->{extension});
		
		$referring_extension = $EXT_vars[0]; # initial extension sent
		$channel_group = $EXT_vars[1]; # name of the parked group
		$outbound_number = $EXT_vars[2]; # extension to send call to after parsing
		$parked_by = $EXT_vars[3]; # leadID
		$park_extension = $EXT_vars[4]; # filename of the on-hold music file
		$phone_number = $EXT_vars[5]; # N/A
		$fronter = $EXT_vars[6]; # N/A

		$park_extension="8301" if ($park_extension eq "");
		$CIDlead_id = $parked_by;

		my $PADlead_id = sprintf("%09s", $parked_by);
		while (length($PADlead_id) > 9) {
			chop($PADlead_id);
		}
		# JmmddhhmmssLLLLLLLLL
		my $JqueryCID = "J$CIDdate$PADlead_id";
		$vars->{accountcode} = $JqueryCID;
		set_variable("CDR(accountcode)",$JqueryCID);
		$osdial->agi_output("--    AccountCode changed: $JqueryCID");
	}
}

my $VLcomments = '';
if ($call_handle_method =~ /^CID/) {
	$phone_number = $vars->{callerid} if (length($vars->{callerid})>0);
	$VLcomments = $vars->{calleridname} if (length($vars->{calleridname})>0);
}

if ($call_handle_method =~ /^ANI/) {
	$phone_number='';
	### allow for external ANI to be collected on older RBS T1 circuits
	if ($vars->{extension} =~ /\*\d\d\d\d\d\d\d\d\d\d\*/) {
		my @EXT_vars = split(/\*/, $vars->{extension});
		$phone_number =	$EXT_vars[1];
		$osdial->agi_output("--    ANI found: |$phone_number|");
	}
}

$phone_code = $osdial->{settings}{default_phone_code} if ($phone_code eq '');
$phone_number =~ s/^\+1//;
$phone_number = $inbound_number if ($phone_number eq '');
$pin=$inbound_number if (length($pin) < 1);
$fronter = $pin if(length($pin)>0);

#$vars->{channel} =~ s/-.*//gi if ($vars->{channel} =~ /^SIP/);
#$vars->{channel} =~ s/-\d$//gi if ($vars->{channel} =~ /^Zap\//);
$vars->{accountcode} = $parked_by if (length($vars->{accountcode})<10);
$vars->{accountcode} = $pin if (length($pin)>0);

$osdial->agi_output("+++++ INBOUND CALL OSDCL STARTED : |$channel_group|".$vars->{accountcode}."|".$vars->{callerid}."-$pin|$now_date");


if ($vars->{channel} =~ /Local/i) {
	if ( ($outbound_number =~ /CXFER/) || ($call_handle_method =~ /^CLOSER/) ) {
		$osdial->agi_output("+++++ OSDAD START LOCAL CHANNEL: CXFER OVERRIDE- ".$vars->{priority});
		if ($call_handle_method =~ /^CLOSER/) {
			sleep(1);
			$stmtA = sprintf("SELECT SQL_NO_CACHE count(*) AS cnt FROM osdial_auto_calls WHERE lead_id='%s' AND call_type='IN' AND campaign_id='%s' AND channel NOT LIKE 'Local%%';",$osdial->mres($CIDlead_id),$osdial->mres($channel_group));
			my $rec = $osdial->sql_query($stmtA);
			if ($rec->{cnt}>0) {
				$osdial->agi_output("+++++ INBOUND LOCAL DUPLICATE: EXITING- ".$vars->{priority});
				exit;
			}
		}
	} else {
		$osdial->agi_output("+++++ OSDAD START LOCAL CHANNEL: EXITING- ".$vars->{priority});
		exit;
	}
}

### Grab inbound groups values from the database
my $groupidSQL='';
$groupidSQL = sprintf("group_id='%s'",$osdial->mres($channel_group));
$groupidSQL = sprintf("group_id LIKE '___%s'",$osdial->mres(substr($channel_group,3))) if ($osdial->{settings}{enable_multicompany} and $channel_group =~ /^...IN_/);
$stmtA = sprintf("SELECT * FROM osdial_inbound_groups WHERE %s LIMIT 1;",$groupidSQL);
my $ingroup = $osdial->sql_query($stmtA);
set_variable("CHANNEL(language)",$ingroup->{'prompt_language'}) if ($ingroup->{'prompt_language'} ne '');

if ($ingroup->{group_id} ne '') {
	$hold = $ingroup->{background_music_filename};
	$hold = 'conf' if ($hold eq 'default');
	$channel_group = $ingroup->{group_id};
	$DROP_TIME = $ingroup->{drop_call_seconds};
}

exit 0 if ($ingroup->{group_id} eq '' or $ingroup->{active} eq 'N');

# If the channel_group is an A2A and the call was an inbound, use the agent alert from the initial ingroup.
if ($channel_group =~ /^A2A_/ and $phone_number ne '') {
	my $a2aingroup='';
	$stmtA = sprintf("SELECT campaign_id FROM osdial_auto_calls WHERE callerid='%s' AND call_type='IN' AND campaign_id NOT LIKE 'A2A_%%' LIMIT 1;",$osdial->mres($phone_number));
	while (my $rec = $osdial->sql_query($stmtA)) {
		$a2aingroup = $rec->{campaign_id};
		$stmtA = sprintf("SELECT agent_alert_exten,agent_alert_delay FROM osdial_inbound_groups WHERE group_id='%s' LIMIT 1;",$osdial->mres($a2aingroup));
		while (my $rec = $osdial->sql_query($stmtA)) {
			$ingroup->{agent_alert_exten} = $rec->{agent_alert_exten};
			$ingroup->{agent_alert_delay} = $rec->{agent_alert_delay};
		}
	}
}

# Set agent_alert_exten and agent_alert_delay defaults.
if ($ingroup->{agent_alert_exten} =~ /^$|^X$|^8304$/) {
	$ingroup->{agent_alert_exten} = '8304';
	$ingroup->{agent_alert_delay} = '350';
}

my $dayname;
my $wday = (localtime())[6];
$dayname='sunday' if ($wday==0);
$dayname='monday' if ($wday==1);
$dayname='tuesday' if ($wday==2);
$dayname='wednesday' if ($wday==3);
$dayname='thursday' if ($wday==4);
$dayname='friday' if ($wday==5);
$dayname='saturday' if ($wday==6);
my $daySQL=',ct_'.$dayname.'_start,ct_'.$dayname.'_stop';

### Grab call_times values from the database
$stmtA = sprintf("SELECT ct_default_start,ct_default_stop%s FROM osdial_call_times WHERE call_time_id='%s';",$daySQL,$osdial->mres($ingroup->{call_time_id}));
my $rec = $osdial->sql_query($stmtA);
my $ct_default_start = $rec->{ct_default_start};
my $ct_default_stop = $rec->{ct_default_stop};
my $ct_day_start = $rec->{'ct_'.$dayname.'_start'};
my $ct_day_stop = $rec->{'ct_'.$dayname.'_stop'};

if ($ct_day_start>0 or $ct_day_stop>0) {
	$ct_default_start = $ct_day_start;
	$ct_default_stop = $ct_day_stop;
}

stream_file('sip-silence');
stream_file('sip-silence');

if ($call_handle_method =~ /DIGITID$/) {
	my $digits_to_collect = $call_handle_method;
	$digits_to_collect =~ s/DIGITID//gi;
	$fronter = enter_pin_number($digits_to_collect);
}

my $insert_lead_id = $CIDlead_id;
if ($call_handle_method =~ /CLOSER/) {
	### Grab call lead parameters from osdial_list table
	$stmtA = sprintf("SELECT SQL_NO_CACHE phone_number,phone_code,user FROM osdial_list WHERE lead_id='%s' LIMIT 1;",$osdial->mres($CIDlead_id));
	while (my $rec = $osdial->sql_query($stmtA)) {
		$phone_number = $rec->{phone_number};
		$phone_code = $rec->{phone_code};
		$fronter = $rec->{user};
	}
} else {
	# Create list if it doesn't exist.
	$stmtA = sprintf("SELECT count(*) AS cnt FROM osdial_lists WHERE list_id='%s';",$osdial->mres($list_id));
	my $rec = $osdial->sql_query($stmtA);
	if ($rec->{cnt} == 0) {
		$osdial->agi_output("--    OSDAD osdial_lists entry missing, adding |$list_id|");
		my $incamp = 'TEST';
		if ($osdial->{settings}{enable_multicompany}) {
			$incamp=$list_id;
			$incamp =~ s/\D//g;
			$incamp = substr($incamp,0,3);
			if ($incamp>100 and $incamp<1000) {
				$incamp.='TEST';
			} else {
				$incamp = '101TEST';
			}
		}
		$stmtA = sprintf("INSERT INTO osdial_lists SET list_id='%s',list_name='AutoLeads Inbound %s',list_description='AutoLeads Created From Ingroup, should not be active.',campaign_id='%s',active='N';",$osdial->mres($list_id),$osdial->mres($list_id),$osdial->mres($incamp));
		$affected_rows = $osdial->sql_execute($stmtA);
	}

	if ($call_handle_method =~ /LOOKUP/) {
		my $listSQL = '';
		if ($call_handle_method =~ /LOOKUPRL$/) {
			$listSQL = sprintf("AND list_id='%s'",$osdial->mres($list_id));
		} elsif ($call_handle_method =~ /LOOKUPRC$/) {
			my $SlistsSQL='';
			$stmtA = sprintf("SELECT list_id FROM osdial_lists WHERE campaign_id='%s' LIMIT 100;",$osdial->mres($Scampaign_id));
			while (my $rec = $osdial->sql_query($stmtA)) {
				$SlistsSQL .= sprintf("'%s',",$osdial->mres($rec->{list_id}));
			}
			chop($SlistsSQL);
			$listSQL = sprintf("AND list_id IN (%s)",$SlistsSQL) if (length($SlistsSQL)>3);
		}

		$stmtA = sprintf("SELECT SQL_NO_CACHE lead_id FROM osdial_list FORCE INDEX (list_id) WHERE phone_number='%s' OR alt_phone='%s' OR address3='%s' %s ORDER BY last_local_call_time LIMIT 1;",$osdial->mres($phone_number),$osdial->mres($phone_number),$osdial->mres($phone_number),$listSQL);
		$agi_string = "--    OSDAD osdial_list search |$phone_number";
		$agi_string .= "\n|$stmtA|" if ($DB>1);
		$osdial->agi_output($agi_string);
		my $rec = $osdial->sql_query($stmtA);
		if ($rec->{lead_id} > 0) {
			$rec_countCUSTDATA=0;	
			$insert_lead_id = $rec->{lead_id};
			$agi_string = "--    OSDAD osdial_list found |$insert_lead_id";
			$agi_string .= "\n|$stmtA|" if ($DB>1);
			$osdial->agi_output($agi_string)
		} else {
			### Search AFF fields for phone else add new record
			$stmtA= sprintf("SELECT SQL_NO_CACHE osdial_list_fields.lead_id FROM osdial_list_fields JOIN osdial_list ON (osdial_list_fields.lead_id=osdial_list.lead_id) WHERE value='%s' %s LIMIT 1;",$osdial->mres($phone_number),$listSQL);
			$agi_string = "--    OSDAD osdial_list_fields search |$phone_number";
			$agi_string .= "\n|$stmtA|" if ($DB>1);
			$osdial->agi_output($agi_string);
			my $rec = $osdial->sql_query($stmtA);
			if ($rec->{lead_id} > 0) {
				$rec_countCUSTDATA=0;	
				$insert_lead_id = $rec->{lead_id};

				$agi_string = "--    OSDAD osdial_list_fields found |$insert_lead_id";
				$agi_string .= "\n|$stmtA|" if ($DB>1);
				$osdial->agi_output($agi_string);
			} else {
				### insert a record into the osdial_list table 
				$stmtA = sprintf("INSERT INTO osdial_list (entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,called_since_last_reset,phone_code,phone_number,custom1,called_count,gmt_offset_now,comments) VALUES ('%s','%s','INBND','%s','%s','VDCL','%s','Y','%s','%s','%s','1','-5.00','%s');",$osdial->mres($SQLdate),$osdial->mres($tsSQLdate),$osdial->mres($fronter),$osdial->mres($inbound_number),$osdial->mres($list_id),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($channel_group),$osdial->mres($VLcomments));
				$affected_rows = $osdial->sql_execute($stmtA);
				$insert_lead_id = $osdial->sql_insert_id();

				$agi_string = "--    OSDAD osdial_list insert |$insert_lead_id";
				$agi_string .= "\n|$stmtA|" if ($DB>1);
				$osdial->agi_output($agi_string);
			}
		}
	} else {
		### insert a record into the osdial_list table if no record was found above
		$stmtA = sprintf("INSERT INTO osdial_list (entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,called_since_last_reset,phone_code,phone_number,custom1,called_count,gmt_offset_now,comments) VALUES ('%s','%s','INBND','%s','%s','VDCL','%s','Y','%s','%s','%s','1','-5.00','%s');",$osdial->mres($SQLdate),$osdial->mres($tsSQLdate),$osdial->mres($fronter),$osdial->mres($inbound_number),$osdial->mres($list_id),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($channel_group),$osdial->mres($VLcomments));
		$affected_rows = $osdial->sql_execute($stmtA);
		$insert_lead_id = $osdial->sql_insert_id();
	}
}



my $PADlead_id = sprintf("%09s", $insert_lead_id);
while (length($PADlead_id) > 9) {
	chop($PADlead_id);
}
# YmmddhhmmssLLLLLLLLL
my $YqueryCID = "Y$CIDdate$PADlead_id";
$vars->{accountcode} = $YqueryCID;
### set the accountcode 
set_variable("CDR(accountcode)",$YqueryCID);
$osdial->agi_output("--    AccountCode changed: $YqueryCID");


##### BEGIN AFTER HOURS CHECK #####
$osdial->agi_output("--    After Hours Check: |$hm|$ct_default_start|$ct_default_stop|");
if ($hm<$ct_default_start or $hm>$ct_default_stop) {
	my $VHqueryCID = "VA$CIDdate$hms";

	if ($ingroup->{after_hours_action} =~ /EXTENSION|VOICEMAIL/) {
		my $DROPexten = '';
		$DROPexten = $ingroup->{after_hours_exten} if ($ingroup->{after_hours_action} =~ /EXTENSION/);
		$DROPexten = $osdial->{server}{voicemail_dump_exten}.$ingroup->{after_hours_voicemail} if ($ingroup->{after_hours_action} =~ /VOICEMAIL/);
		### if DROP extension is defined then send the dropped call there instead of hangup
		if (length($DROPexten)>0) {
			stream_file('sip-silence');
			#sleep(1);
			$osdial->agi_output("exiting the OSDAD app after hours, transferring call to $DROPexten");
			set_context($osdial->{server}{ext_context});
			set_extension($DROPexten);
			set_priority(1);
		}
	}
	$stmtA = sprintf("DELETE FROM osdial_auto_calls WHERE callerid='%s' AND server_ip='%s' ORDER BY call_time DESC LIMIT 1;",$osdial->mres($vars->{accountcode}),$osdial->mres($osdial->{'VARserver_ip'}));
	$affected_rows = $osdial->sql_execute($stmtA);
	$osdial->agi_output("--    OSDCL vac record deleted: |$affected_rows| $channel_group|");

	$stmtA = sprintf("INSERT INTO osdial_closer_log SET status='DROP',start_epoch='%s',end_epoch='%s',length_in_sec='1',queue_seconds='0',lead_id='%s',campaign_id='%s',user='VDCL',list_id='%s',call_date='%s',phone_code='%s',phone_number='%s',comments='AFTER HOURS DROP',term_reason='AFTERHOURS',uniqueid='%s',callerid='%s';",$osdial->mres($now_date_epoch),$osdial->mres($now_date_epoch),$osdial->mres($insert_lead_id),$osdial->mres($channel_group),$osdial->mres($list_id),$osdial->mres($now_date),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($vars->{uniqueid}),$osdial->mres($vars->{accountcode}));
	$affected_rows = $osdial->sql_execute($stmtA);
	$agi_string = "--    OSDCL ocl insert: |$affected_rows|$insert_lead_id";
	$agi_string .= "\n|$stmtA|" if ($DB>1);
	$osdial->agi_output($agi_string);

	$stmtA = sprintf("UPDATE osdial_list SET status='DROP' WHERE lead_id='%s';",$osdial->mres($insert_lead_id));
	$affected_rows = $osdial->sql_execute($stmtA);
	$agi_string = "--    OSDCL ol update: |$affected_rows|$insert_lead_id";
	$agi_string .= "\n|$stmtA|" if ($DB>1);
	$osdial->agi_output($agi_string);

	if ($ingroup->{after_hours_action} =~ /HANGUP/) {
		$stmtA = sprintf("INSERT INTO osdial_manager VALUES ('','','%s','NEW','N','%s','%s','Hangup','%s','Channel: %s','','','','','','','','','');",$osdial->mres($SQLdate),$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($vars->{channel}),$osdial->mres($VHqueryCID),$osdial->mres($vars->{channel}));
		$affected_rows = $osdial->sql_execute($stmtA);
		$osdial->agi_output("--    OSDCL call_hungup after hours: |$VHqueryCID||".$vars->{channel}."|insert to osdial_manager");

	} elsif ($ingroup->{after_hours_action} =~ /CALLBACK/) {
		$inafterhours=1;
		stream_start_file($ingroup->{after_hours_message_filename});
		stream_file('sip-silence');
		while ($inafterhours==1) {
			my $innew = infunc();
			$docallback=1;
		}
		callback_prompt() if ($docallback);
		$stmtA = sprintf("INSERT INTO osdial_manager VALUES ('','','%s','NEW','N','%s','%s','Hangup','%s','Channel: %s','','','','','','','','','');",$osdial->mres($SQLdate),$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($vars->{channel}),$osdial->mres($VHqueryCID),$osdial->mres($vars->{channel}));
		$affected_rows = $osdial->sql_execute($stmtA);
		$osdial->agi_output("--    OSDCL call_hungup after hours: |$VHqueryCID||".$vars->{channel}."|insert to osdial_manager");

	} elsif ($ingroup->{after_hours_action} =~ /MESSAGE/) {
		$inafterhours=1;
		stream_start_file($ingroup->{after_hours_message_filename});
		stream_file('sip-silence');
		$stmtA = sprintf("INSERT INTO osdial_manager VALUES ('','','%s','NEW','N','%s','%s','Hangup','%s','Channel: %s','','','','','','','','','');",$osdial->mres($SQLdate),$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($vars->{channel}),$osdial->mres($VHqueryCID),$osdial->mres($vars->{channel}));
		$affected_rows = $osdial->sql_execute($stmtA);
		$osdial->agi_output("--    OSDCL call_hungup after hours: |$VHqueryCID||".$vars->{channel}."|insert to osdial_manager");
		if ($ingroup->{callback_interval}>0) {
			while ($inafterhours==1) {
				my $innew = infunc();
				$docallback=1 if ($innew eq $ingroup->{callback_interrupt_key});
			}
			callback_prompt() if ($docallback);
		}
	}
	$osdial->sql_disconnect();
	exit_ivr();
}
##### END AFTER HOURS CHECK #####



##### PLAY WELCOME MESSAGE #####
$inwelcome=1;
my $loopstart = time();
stream_file('silence/1');
if ($ingroup->{'welcome_message_filename'} !~ /---NONE---/ and $ingroup->{'welcome_message_filename'} ne '') {
	$welcome = $ingroup->{'welcome_message_filename'};
	stream_file($welcome);
	stream_file('silence/2');
	if ($ingroup->{'welcome_message_min_playtime'} eq '' or $ingroup->{'welcome_message_min_playtime'}>0) {
		$loopwelcome=1;
	}
	while ($loopwelcome==1) {
		my $innew = infunc();
		my $loopcur = time();
		$loopwelcome=0 if ($ingroup->{'welcome_message_min_playtime'}>0 and ($loopcur-$loopstart)>$ingroup->{'welcome_message_min_playtime'});
	}
}


my $INBOUNDcampsSQL='';
$stmtA = "SELECT campaign_id FROM osdial_campaigns WHERE active='Y' AND campaign_allow_inbound='Y';";
while (my $rec = $osdial->sql_query($stmtA)) {
	$INBOUNDcampsSQL .= sprintf("'%s',",$osdial->mres($rec->{campaign_id}));
}
chop($INBOUNDcampsSQL);

$osdial->agi_output("|$stmtA|$insert_lead_id|") if ($DB>1); 

if ($call_handle_method =~ /DIGITID$/) {
		$stmtA = sprintf("INSERT INTO osdial_log (uniqueid,lead_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,processed,server_ip) VALUES ('%s','%s','%s','%s','%s','XFER','%s','%s','%s','N','%s');",$osdial->mres($vars->{uniqueid}),$osdial->mres($insert_lead_id),$osdial->mres($channel_group),$osdial->mres($SQLdate),$osdial->mres($now_date_epoch),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($fronter),$osdial->mres($osdial->{'VARserver_ip'}));
		$agi_string = "--    OSDAD : |$insert_lead_id|$fronter|insert to osdial_log: ".$vars->{uniqueid};
		$agi_string .= "\n|$stmtA|" if ($DB>1);
		$osdial->agi_output($agi_string);
		$affected_rows = $osdial->sql_execute($stmtA);
}

if ($call_handle_method =~ /CLOSER|DIGITID$/) {
		$stmtA = sprintf("INSERT INTO osdial_xfer_log (lead_id,campaign_id,call_date,phone_code,phone_number,user,closer,uniqueid) VALUES ('%s','%s','%s','%s','%s','%s','VDXL','%s');",$osdial->mres($insert_lead_id),$osdial->mres($channel_group),$osdial->mres($SQLdate),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($fronter),$osdial->mres($vars->{uniqueid}));
		$agi_string = "--    OSDXL : |$insert_lead_id|insert to osdial_xfer_log";
		$agi_string .= "\n|$stmtA|" if ($DB>1);
		$osdial->agi_output($agi_string);
		$affected_rows = $osdial->sql_execute($stmtA);
}


if ($osdial->{settings}{enable_queuemetrics_logging} > 0) {
	my $data2 = '';
	$stmtA = sprintf("SELECT SQL_NO_CACHE phone_number FROM osdial_auto_calls WHERE lead_id='$CIDlead_id';",$osdial->mres($CIDlead_id));
	while (my $res = $osdial->sql_query($stmtA)) {
		 $data2 = $res->{phone_number};
	}

	if (length($osdial->{settings}{queuemetrics_eq_prepend})>0 and $osdial->{settings}{queuemetrics_eq_prepend} !~ /NONE/) {
		$stmtA = sprintf("SELECT SQL_NO_CACHE %s FROM osdial_list WHERE lead_id='%s';",$osdial->{settings}{queuemetrics_eq_prepend},$osdial->mres($CIDlead_id));
		while (my $res = $osdial->sql_query($stmtA)) {
			 $data2 = $res->{$osdial->{settings}{queuemetrics_eq_prepend}}."-".$phone_number;
		}
	}

	$osdial->sql_connect('QM',$osdial->{settings}{queuemetrics_dbname},$osdial->{settings}{queuemetrics_server_ip},'3306',$osdial->{settings}{queuemetrics_login},$osdial->{settings}{queuemetrics_pass});
	$osdial->agi_output("CONNECTED TO DATABASE:  ".$osdial->{settings}{queuemetrics_server_ip}."|".$osdial->{settings}{queuemetrics_dbname}) if ($DB>1);

	my $stmtB = sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='NONE',verb='ENTERQUEUE',data2='%s',serverid='%s';",$osdial->mres($now_date_epoch),$osdial->mres($YqueryCID),$osdial->mres($channel_group),$osdial->mres($data2),$osdial->mres($osdial->{settings}{queuemetrics_log_id}));
	$osdial->sql_execute($stmtB,'QM');

	$osdial->sql_disconnect('QM');
}


### insert a LIVE record to the osdial_auto_calls table 
$stmtA = sprintf("INSERT INTO osdial_auto_calls (server_ip,campaign_id,status,lead_id,uniqueid,callerid,channel,phone_code,phone_number,call_time,call_type,stage) VALUES ('%s','%s','LIVE','%s','%s','%s','%s','%s','%s','%s','IN','LIVE-0');",$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($channel_group),$osdial->mres($insert_lead_id),$osdial->mres($vars->{uniqueid}),$osdial->mres($vars->{accountcode}),$osdial->mres($vars->{channel}),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($SQLdate));
$agi_string = "--    OSDAC : |$insert_lead_id|insert to osdial_auto_calls";
$agi_string .= "\n |$stmtA|" if ($DB>1); 
$osdial->agi_output($agi_string);
$affected_rows = $osdial->sql_execute($stmtA);

$stmtA = sprintf("INSERT INTO osdial_closer_log (lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,processed,uniqueid,callerid) VALUES ('%s','%s','%s','%s','%s','QUEUE','%s','%s','VDCL','N','%s','%s');",$osdial->mres($insert_lead_id),$osdial->mres($list_id),$osdial->mres($channel_group),$osdial->mres($SQLdate),$osdial->mres($now_date_epoch),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($vars->{uniqueid}),$osdial->mres($vars->{accountcode}));
$affected_rows = $osdial->sql_execute($stmtA);
$agi_string = "--    OSDCL : |$insert_lead_id|insert to osdial_closer_log";
$agi_string .= "\n |$stmtA|" if ($DB>1); 
$osdial->agi_output($agi_string);



my $drop_timer=0;
my $drop_seconds=0;
my $hold_message_counter=0;
$hold_message_counter=$ingroup->{prompt_interval}-$ingroup->{onhold_startdelay} if ($ingroup->{prompt_interval}>0 and $ingroup->{onhold_startdelay}>0);
my $callback_message_counter=0;
$callback_message_counter=$ingroup->{callback_interval}-$ingroup->{callback_startdelay} if ($ingroup->{callback_interval}>0 and $ingroup->{callback_startdelay}>0);
my $placement_counter=0;
$placement_counter=$ingroup->{placement_interval}-$ingroup->{placement_startdelay} if ($ingroup->{placement_interval}>0 and $ingroup->{placement_startdelay}>0);
my $queuetime_counter=0;
$queuetime_counter=$ingroup->{queuetime_interval}-$ingroup->{queuetime_startdelay} if ($ingroup->{queuetime_interval}>0 and $ingroup->{queuetime_startdelay}>0);
my $placement_playcount=0;
my $queuetime_playcount=0;
my $last_played=12;
my $prompt_buffer=15;
my $term_reason = 'QUEUETIMEOUT';

if ($ingroup->{drop_trigger} eq 'NO_AGENTS_AVAILABLE') {
	my $soSQL='';
	$soSQL = sprintf("AND server_ip='%s'",$osdial->mres($osdial->{'VARserver_ip'})) if ($agent_search_method =~ /^SO/);
	$stmtA = sprintf("SELECT SQL_NO_CACHE count(*) AS cnt FROM osdial_live_agents WHERE status IN ('CLOSER','READY') AND closer_campaigns LIKE '%% %s %%' %s;",$osdial->mres($channel_group),$soSQL);
	$agi_string = "--    No agents availabile.";
	$agi_string .= "\n|$stmtA|" if ($DB>1);
	$osdial->agi_output($agi_string);
	my $res = $osdial->sql_query($stmtA);
	if ($res->{cnt}==0) {
		$drop_timer = $DROP_TIME + 1;
		$term_reason = 'NOAGENTSAVAILABLE';
		if ($ingroup->{callback_interval}>0) {
			stream_file('sip-silence');
			while ($inwelcome==1) {
				my $innew = infunc();
				$docallback=1 if ($innew eq $ingroup->{callback_interrupt_key});
			}
		}
	}

} elsif ($ingroup->{drop_trigger} eq 'NO_AGENTS_CONNECTED') {
	my $soSQL='';
	$soSQL = sprintf("AND server_ip='%s'",$osdial->mres($osdial->{'VARserver_ip'})) if ($agent_search_method =~ /^SO/);
	$stmtA = sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_live_agents WHERE closer_campaigns LIKE '%% %s %%' %s;",$osdial->mres($channel_group),$soSQL);
	$agi_string = "--    No agents connected.";
	$agi_string .= "\n|$stmtA|" if ($DB>1);
	$osdial->agi_output($agi_string);
	my $res = $osdial->sql_query($stmtA);
	if ($res->{cnt}==0) {
		$drop_timer = $DROP_TIME + 1;
		$term_reason = 'NOAGENTS';
		if ($ingroup->{callback_interval}>0) {
			stream_file('sip-silence');
			while ($inwelcome==1) {
				my $innew = infunc();
				$docallback=1 if ($innew eq $ingroup->{callback_interrupt_key});
			}
		}
	}
}

callback_prompt() if ($docallback);

$inloop=1;
my $aco_sub=0;
while ($drop_timer <= $DROP_TIME) {
	$rec_countCUSTDATA=0;
	$rec_countWAIT=0;
	my $agent_call_order='ORDER BY last_call_finish';
	$agent_call_order='ORDER BY user_level desc,last_call_finish' if ($ingroup->{next_agent_call} =~ /overall_user_level/i);
	$agent_call_order='ORDER BY last_call_time' if ($ingroup->{next_agent_call} =~ /oldest_call_start/i);
	$agent_call_order='ORDER BY last_call_finish' if ($ingroup->{next_agent_call} =~ /oldest_call_finish/i);
	$agent_call_order='ORDER BY random_id' if ($ingroup->{next_agent_call} =~ /random/i);
	$agent_call_order='ORDER BY campaign_weight desc,last_call_finish' if ($ingroup->{next_agent_call} =~ /campaign_rank/i);
	$agent_call_order='ORDER BY osdial_live_agents.calls_today,osdial_live_agents.last_call_finish' if ($ingroup->{next_agent_call} =~ /fewest_calls_campaign/i);
	if ($ingroup->{next_agent_call} =~ /inbound_group_rank/i)	{
		$aco_sub=1;
		$agent_call_order='ORDER BY group_weight desc';
	}
	if ($ingroup->{next_agent_call} =~ /fewest_calls/i) {
		$aco_sub=1;
		$agent_call_order='ORDER BY osdial_live_inbound_agents.calls_today,osdial_live_inbound_agents.last_call_finish';
	}


	my $INBOUNDcampsSQL='';
	$stmtA = "SELECT campaign_id FROM osdial_campaigns WHERE active='Y' AND campaign_allow_inbound='Y';";
	while (my $rec = $osdial->sql_query($stmtA)) {
		$INBOUNDcampsSQL .= sprintf("'%s',",$osdial->mres($rec->{campaign_id}));
	}
	chop($INBOUNDcampsSQL);


	###################################################################################################
	##### Attempt to send the call.
	my $VDADconf_exten='';
	my $VDADuser='';
	my $VDADextension='';
	my $VDADserver_ip='';
	my $found_agents=0;

	# LO passes thru twice, once on the local server and once as a remote.
	# SO passes through once, on the local server.
	# LB passes once as a remote.
	my $ASattempt=0;
	while ($ASattempt<1 or ($agent_search_method eq 'LO' and $ASattempt<2)) {
		my $svrSQLwhere='';
		my $csvrSQLfld='';
		my $ASmethod='LOCAL';
		if ($agent_search_method eq 'SO' or ($agent_search_method eq 'LO' and $ASattempt<1)) {
			$svrSQLwhere=sprintf("AND server_ip='%s'",$osdial->mres($osdial->{'VARserver_ip'}));
		} else {
			$csvrSQLfld=sprintf(",call_server_ip='%s'",$osdial->mres($osdial->{'VARserver_ip'}));
			$ASmethod='REMOTE';
		}

		$rec_countWAIT=0;
		$stmtA = sprintf("SELECT SQL_NO_CACHE count(*) AS cnt FROM osdial_auto_calls WHERE status='LIVE' AND campaign_id='%s' AND call_time<'%s' AND lead_id!='%s' %s;",$osdial->mres($channel_group),$osdial->mres($SQLdateBEGIN),$osdial->mres($insert_lead_id),$svrSQLwhere);
		$agi_string = "--    Find auto call record.";
		$agi_string .= "\n|$stmtA|" if ($DB>1);
		$osdial->agi_output($agi_string);
		while (my $rec = $osdial->sql_query($stmtA)) {
			$rec_countWAIT = $rec->{cnt};
		}
	
		if ($rec_countWAIT < 1) {
			if ($aco_sub>0) {
				#$stmtA = "LOCK TABLES osdial_live_agents WRITE, osdial_live_inbound_agents WRITE;";
				#$affected_rows = $osdial->sql_execute($stmtA);


				$stmtA = sprintf("SELECT SQL_NO_CACHE osdial_live_agents.conf_exten,osdial_live_agents.user,osdial_live_agents.extension,osdial_live_agents.server_ip FROM osdial_live_agents JOIN osdial_live_inbound_agents ON osdial_live_agents.user=osdial_live_inbound_agents.user WHERE status IN ('CLOSER','READY') AND osdial_live_inbound_agents.group_id='%s' AND last_update_time>'%s' %s %s LIMIT 1;",$osdial->mres($channel_group),$osdial->mres($BDtsSQLdate),$svrSQLwhere,$agent_call_order);
				$agi_string = "--    Find live agent record.";
				$agi_string .= "\n|$stmtA|" if ($DB>1);
				$osdial->agi_output($agi_string);
				$rec_countCUSTDATA=0;
				my $rec = $osdial->sql_query($stmtA);
				$VDADconf_exten	= $rec->{conf_exten};
				$VDADuser = $rec->{user};
				$VDADextension = $rec->{extension};
				$VDADserver_ip = $rec->{server_ip};

				$affected_rows=0;
				if ($rec->{conf_exten} ne '') {
					my $random = int( rand(9999999)) + 10000000;
					$stmtA = sprintf("UPDATE osdial_live_agents SET status='QUEUE',lead_id='%s',random_id='%s',uniqueid='%s',channel='%s',callerid='%s'%s WHERE user='%s';",$osdial->mres($insert_lead_id),$osdial->mres($random),$osdial->mres($vars->{uniqueid}),$osdial->mres($vars->{channel}),$osdial->mres($vars->{accountcode}),$csvrSQLfld,$osdial->mres($VDADuser));
					$affected_rows = $osdial->sql_execute($stmtA);
					$found_agents=$affected_rows;
				}

				#$stmtA = "UNLOCK TABLES;";
				#$affected_rows = $osdial->sql_execute($stmtA);
			} else {
				my $random = int( rand(9999999)) + 10000000;
				$stmtA = sprintf("UPDATE osdial_live_agents SET status='QUEUE',random_id='%s',lead_id='%s',uniqueid='%s',channel='%s',callerid='%s'%s WHERE status IN('CLOSER','READY') AND campaign_id IN (%s) AND closer_campaigns LIKE '%% %s %%' AND last_update_time>'%s' %s LIMIT 1;",$osdial->mres($random),$osdial->mres($insert_lead_id),$osdial->mres($vars->{uniqueid}),$osdial->mres($vars->{channel}),$osdial->mres($vars->{accountcode}),$csvrSQLfld,$INBOUNDcampsSQL,$osdial->mres($channel_group),$osdial->mres($BDtsSQLdate),$agent_call_order);
				$affected_rows = $osdial->sql_execute($stmtA);
				$found_agents=$affected_rows;
			}
			$agi_string = "--    OSDAD get agent: |$DROP_TIME|$drop_timer|$affected_rows|$found_agents|update of vla table: $channel_group";
			$agi_string .= "\n|$stmtA|" if ($DB>1);
			$osdial->agi_output($agi_string);
			if ($found_agents > 0) {
				if ($aco_sub<1) {
					$stmtA = sprintf("SELECT SQL_NO_CACHE conf_exten,user,extension,server_ip FROM osdial_live_agents WHERE status='QUEUE' AND campaign_id IN (%s) AND callerid='%s' AND channel='%s' %s ORDER BY last_call_time LIMIT 1;",$INBOUNDcampsSQL,$osdial->mres($vars->{accountcode}),$osdial->mres($vars->{channel}),$svrSQLwhere);
					$agi_string = "--    OSDAD get agent QUEUEd: |$DROP_TIME|$drop_timer|$affected_rows|$found_agents|update of vla table: $channel_group";
					$agi_string .= "\n|$stmtA|" if ($DB>1);
					$osdial->agi_output($agi_string);
					$rec_countCUSTDATA=0;
					my $rec = $osdial->sql_query($stmtA);
					$VDADconf_exten	= $rec->{conf_exten};
					$VDADuser = $rec->{user};
					$VDADextension = $rec->{extension};
					$VDADserver_ip = $rec->{server_ip};
				}
				$stmtA = sprintf("UPDATE osdial_auto_calls SET status='CLOSER',stage='CLOSER-%s' WHERE callerid='%s';",$osdial->mres($drop_timer),$osdial->mres($vars->{accountcode}));
				$affected_rows = $osdial->sql_execute($stmtA);
				$agi_string = "--    OSDCL XFER $ASmethod: |$affected_rows|update of vac table: ".$vars->{accountcode};
				$agi_string .= "\n|$stmtA|" if ($DB>1);
				$osdial->agi_output($agi_string);
				if ($affected_rows < 1) {
					$stmtA = sprintf("INSERT INTO osdial_auto_calls (server_ip,campaign_id,status,lead_id,uniqueid,callerid,channel,phone_code,phone_number,call_time,call_type,stage) VALUES ('%s','%s','CLOSER','%s','%s','%s','%s','%s','%s','%s','IN','CLOSER-%s');",$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($channel_group),$osdial->mres($insert_lead_id),$osdial->mres($vars->{uniqueid}),$osdial->mres($vars->{accountcode}),$osdial->mres($vars->{channel}),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($SQLdate),$osdial->mres($drop_timer));
					$affected_rows = $osdial->sql_execute($stmtA);
					$agi_string = "--    $affected_rows|OSDAC-reinsert";
					$agi_string .= "\n|$stmtA|" if ($DB>1);
					$osdial->agi_output($agi_string);
				}

				if ($osdial->{settings}{enable_queuemetrics_logging} > 0) {
					$osdial->sql_connect('QM',$osdial->{settings}{queuemetrics_dbname},$osdial->{settings}{queuemetrics_server_ip},'3306',$osdial->{settings}{queuemetrics_login},$osdial->{settings}{queuemetrics_pass});
					$osdial->agi_output("CONNECTED TO DATABASE:  ".$osdial->{settings}{queuemetrics_server_ip}."|".$osdial->{settings}{queuemetrics_dbname}) if ($DB>1);
					$now_date_epoch = time();
					$now_date = $osdial->get_datetime($now_date_epoch);

					my $stmtB = sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='Agent/%s',verb='CONNECT',data1='%s',serverid='%s';",$osdial->mres($now_date_epoch),$osdial->mres($YqueryCID),$osdial->mres($channel_group),$osdial->mres($VDADuser),$osdial->mres($drop_timer),$osdial->mres($osdial->{settings}{queuemetrics_log_id}));
					$osdial->sql_execute($stmtB,'QM');

					$osdial->sql_disconnect('QM');
				}

				if ($call_handle_method =~ /CLOSER|DIGITID$/) {
					$stmtA = sprintf("UPDATE osdial_xfer_log SET closer='%s' WHERE lead_id='%s' ORDER BY call_date DESC LIMIT 1;",$osdial->mres($VDADuser),$osdial->mres($insert_lead_id));
					$affected_rows = $osdial->sql_execute($stmtA);
					$agi_string = "--    OSDXL osdial_xfer_log update: |$affected_rows|$insert_lead_id|$VDADuser";
					$agi_string .= "\n|$stmtA|" if ($DB>1);
					$osdial->agi_output($agi_string);
				}

				$stmtA = sprintf("UPDATE osdial_closer_log SET user='%s' WHERE lead_id='%s' ORDER BY call_date DESC LIMIT 1;",$osdial->mres($VDADuser),$osdial->mres($insert_lead_id));
				$affected_rows = $osdial->sql_execute($stmtA);
				$agi_string = "--    closer log : |$affected_rows|update of ocl table: $insert_lead_id";
				$agi_string .= "\n|$stmtA|" if ($DB>1);
				$osdial->agi_output($agi_string);

				my $VDADremDIALstr='';
				if ($agent_search_method eq 'LB' or ($agent_search_method eq 'LO' and $ASattempt>0)) {
					### format the remote server dialstring to get the call to the overflow agent meetme room
					if ($VDADserver_ip =~ m/(\S+)\.(\S+)\.(\S+)\.(\S+)/ and $VDADserver_ip ne $osdial->{VARserver_ip}) {
						$VDADremDIALstr = sprintf('%.3d*%.3d*%.3d*%.3d',$1,$2,$3,$4);
						if ($osdial->{settings}{intra_server_protocol} eq 'IAX2') {
							$VDADremDIALstr .= '#';
						} else {
							$VDADremDIALstr .= '*';
						}
					}
				}
				set_variable("LASTAGENTCONF",$VDADconf_exten);
				my $alertVDADremDIALstr = $VDADremDIALstr;
				$alertVDADremDIALstr .= "9".$VDADconf_exten;

				### if agent alert exten is not disabled, then trigger the alert and wait
				if ($ingroup->{agent_alert_exten} !~ /X/i and $VDADconf_exten !~ /^87......$|^687......$|^767......$/) {
					my $VHqueryCID = "VH$CIDdate$VDADconf_exten";

					### insert a NEW record to the osdial_manager table to play the alert message to the agent
					$stmtA = sprintf("INSERT INTO osdial_manager VALUES ('','','%s','NEW','N','%s','','Originate','%s','Exten: %s','Context: %s','Channel: Local/%s\@%s','Priority: 1','Callerid: %s','Timeout: 10','Account: %s','','','');",$osdial->mres($SQLdate),$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($VHqueryCID),$osdial->mres($alertVDADremDIALstr),$osdial->mres($osdial->{server}{ext_context}),$osdial->mres($ingroup->{agent_alert_exten}),$osdial->mres($osdial->{server}{ext_context}),$osdial->mres($VHqueryCID),$osdial->mres($VHqueryCID));
					$affected_rows = $osdial->sql_execute($stmtA);
					$osdial->agi_output("--    OSDCL agent alert: |$VHqueryCID|$alertVDADremDIALstr|".$vars->{channel}."|insert to osdial_manager");

					usleep(1 * ($ingroup->{agent_alert_delay}+500) * 1000);

					set_variable("CHANALERTLEAVE",'8306');
					set_variable("CHANNEL(hangup_handler_wipe)","osdial|8330|1");
				}

				#stream_file('sip-silence'); # stop music-on-hold process

				### update calls_today for osdial_live_inbound_agents ###
				$stmtA = sprintf("SELECT SQL_NO_CACHE calls_today,user FROM osdial_inbound_group_agents WHERE user='%s' AND group_id='%s';",$osdial->mres($VDADuser),$osdial->mres($channel_group));
				my $rec = $osdial->sql_query($stmtA);
				my $calls_today = $rec->{calls_today};

				$agi_string = "--    OSDLIA find: |$calls_today|$VDADuser|$channel_group|";
				$agi_string .= "\n|$stmtA|" if ($DB>1);
				$osdial->agi_output($agi_string);

				if ($rec->{user} ne '') {
					$stmtA = sprintf("UPDATE osdial_live_inbound_agents SET calls_today='%s' WHERE user='%s' AND group_id='%s';",$osdial->mres($calls_today),$osdial->mres($VDADuser),$osdial->mres($channel_group));
					$affected_rows = $osdial->sql_execute($stmtA);
					$stmtA = sprintf("UPDATE osdial_inbound_group_agents SET calls_today='%s' WHERE user='%s' AND group_id='%s';",$osdial->mres($calls_today),$osdial->mres($VDADuser),$osdial->mres($channel_group));
					$affected_rows = $osdial->sql_execute($stmtA);
					$osdial->agi_output("--    OSDLIA agent calls: |$calls_today|$VDADuser|$channel_group|");
				}

				if ($VDADconf_exten =~ /^87......$|^687......$|^767......$/) {
					if (defined($osdial->{VARoldivr})) {
						$VDADremDIALstr .= "7$VDADconf_exten";
						$stmtA=sprintf("INSERT INTO osdial_manager VALUES ('','','%s','NEW','N','%s','','Originate','%s','Channel: Local/%s\@%s','Context: %s','Exten: 487487','Priority: 1','Account: %s','','','','','');",$osdial->mres($SQLdate),$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($vars->{accountcode}),$osdial->mres($VDADremDIALstr),$osdial->mres($osdial->{server}{ext_context}),$osdial->mres($osdial->{server}{ext_context}),$osdial->mres($vars->{accountcode}));
					} else {
						set_variable("SPYGROUP",$vars->{accountcode});
						$stmtA=sprintf("INSERT INTO osdial_manager VALUES ('','','%s','NEW','N','%s','','Originate','%s','Channel: Local/7%s\@%s','Context: %s','Exten: 487489%s','Priority: 1','Account: %s','','','','','');",$osdial->mres($SQLdate),$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($vars->{accountcode}),$osdial->mres($VDADconf_exten),$osdial->mres($osdial->{server}{ext_context}),$osdial->mres($osdial->{server}{ext_context}),$osdial->mres($vars->{accountcode}),$osdial->mres($vars->{accountcode}));
						$VDADremDIALstr .= '487488';
					}
					$affected_rows = $osdial->sql_execute($stmtA);
					$agi_string = "--    IVR insert into manager.";
					$agi_string .= "\n|$stmtA|" if ($DB>1);
					$osdial->agi_output($agi_string);
				} else {
					usleep(1*500*1000);
					$VDADremDIALstr .= "7".$VDADconf_exten;
				}

				$osdial->agi_output("exiting OSDAD app, transferring call to ".$VDADremDIALstr);
				set_context($osdial->{server}{ext_context});
				set_extension($VDADremDIALstr);
				set_priority(1);

				$stmtA = sprintf("UPDATE osdial_closer_log SET queue_seconds='%s' WHERE lead_id='%s' AND call_date='%s';",$osdial->mres($drop_timer),$osdial->mres($insert_lead_id),$osdial->mres($SQLdate));
				$affected_rows = $osdial->sql_execute($stmtA);
				$agi_string = "--    OSDCL ocl update: |$affected_rows|$insert_lead_id";
				$agi_string .= "\n|$stmtA|" if ($DB>1);
				$osdial->agi_output($agi_string);

				$now_date_epoch = time();
				$now_date = $osdial->get_datetime($now_date_epoch);
				$osdial->agi_output("XXXXX OSDAD transferred: start|stop  $start_time|$now_date");
			
				$osdial->sql_disconnect();
				exit_ivr();
			} else {
				$osdial->agi_output("NNNNN No available $ASmethod agent found");
			}
		} else {
			### For Debugging purposes display the call ahead of this call
			if ($rec_countWAIT=='1' and $drop_timer>3 and $osdial->{server}{agi_output} ne "NONE") {
				$stmtA = sprintf("SELECT SQL_NO_CACHE call_time,campaign_id,last_update_time,callerid,status,channel FROM osdial_auto_calls WHERE status='LIVE' AND campaign_id='%s' AND call_time<'%s' AND lead_id!='%s' %s;",$osdial->mres($channel_group),$osdial->mres($SQLdateBEGIN),$osdial->mres($insert_lead_id),$svrSQLwhere);
				while (my $rec = $osdial->sql_query($stmtA)) {
					$agi_string .= "|".$rec->{call_time}."|".$rec->{campaign_id}."|".$rec->{last_update_time}."|".$rec->{callerid}."|".$rec->{status}."|".$rec->{channel}."|\n";
				}
				$agi_string="--    Call ahead of this one\n";
				$agi_string .= "\n|$stmtA|" if ($DB>1);
				$osdial->agi_output($agi_string); 
			}
			$osdial->agi_output("WWWWW OSDAD XFER $ASmethod WAIT: |$rec_countWAIT|$channel_group|".$vars->{channel}."|".$vars->{accountcode}."|".$vars->{uniqueid}."|$drop_timer|");
		}

		$ASattempt++;
	}

	if ($inwelcome==1) {
		stream_file('sip-silence');
		stream_file('silence/2');
		while ($inwelcome==1) {
			my $innew = infunc();
			$docallback=1 if ($innew eq '*');
		}
	}

	if ($inprompt==0) {
		if ($last_played>$prompt_buffer and $hold_message_counter > $ingroup->{prompt_interval} and $ingroup->{prompt_interval} != 0) {
			$inprompt=1;
			stop_stream();
			stream_file($ingroup->{onhold_prompt_filename});
			stream_file('sip-silence');
			stream_file('silence/2');
			$hold_message_counter = 0;
			$last_played=0;
		} elsif ($last_played>$prompt_buffer and $callback_message_counter > $ingroup->{callback_interval} and $ingroup->{callback_interval} != 0) {
			$inprompt=1;
			stop_stream();
			stream_file('to-be-called-back');
			my $cbkey = $ingroup->{callback_interrupt_key};
			$cbkey = 'star' if ($cbkey eq '*');
			$cbkey = 'pound' if ($cbkey eq '#');
			stream_file('digits/'.$cbkey);
			stream_file('sip-silence');
			stream_file('silence/2');
			$callback_message_counter = 0;
			$last_played=0;
		} elsif ($last_played>$prompt_buffer and ($ingroup->{placement_max_repeat}==0 or $placement_playcount<$ingroup->{placement_max_repeat}) and $ingroup->{placement_interval} != 0 and $placement_counter > $ingroup->{placement_interval}) {
			$inprompt=1;
			$stmtA = sprintf("SELECT * FROM osdial_auto_calls WHERE lead_id='%s' LIMIT 1;",$osdial->mres($insert_lead_id));
			my $curac = $osdial->sql_query($stmtA);
			$stmtA = sprintf("SELECT count(*) AS cnt FROM osdial_auto_calls WHERE campaign_id='%s' AND auto_call_id<'%s';",$osdial->mres($channel_group),$osdial->mres($curac->{auto_call_id}));
			my $rec = $osdial->sql_query($stmtA);
			stop_stream();
			my $dcnt = $rec->{cnt};
			if ($dcnt==0) {
				stream_file('queue-youarenext');
			} else {
				stream_file('queue-thereare');
				if (length($dcnt)==3) {
					my $dig = substr($dcnt,0,1);
					stream_file('digits/'.$dig);
					stream_file('hundred');
					$dcnt = substr($dcnt,1,2);
				}
				if (length($dcnt)==2 and $dcnt!=0) {
					my $dig = substr($dcnt,0,1);
					stream_file('digits/'.$dig.'0');
					$dcnt = substr($dcnt,1,1);
				}
				if (length($dcnt)==1 and $dcnt!=0) {
					my $dig = substr($dcnt,0,1);
					stream_file('digits/'.$dig);
				}
				stream_file('queue-callswaiting');
			}
			stream_file('sip-silence');
			stream_file('silence/2');
			$placement_counter = 0;
			$placement_playcount++;
			$last_played=0;
		} elsif ($last_played>$prompt_buffer and ($ingroup->{queuetime_max_repeat}==0 or $queuetime_playcount<$ingroup->{queuetime_max_repeat}) and $ingroup->{queuetime_interval} != 0 and $queuetime_counter > $ingroup->{queuetime_interval}) {
			$inprompt=1;
			my $qstartdate = $osdial->get_datetime(time()-1800);
			my $qenddate = $osdial->get_datetime();
			$stmtA = sprintf("SELECT AVG(queue_seconds) AS avgq FROM osdial_closer_log WHERE campaign_id='%s' AND call_date BETWEEN '%s' AND '%s';",$osdial->mres($channel_group),$qstartdate,$qenddate);
			my $curq = $osdial->sql_query($stmtA);
			my $qsec = int($curq->{avgq});
			my $qmin;
			# Round up to the next minute.
			if ($qsec%60>0) { $qsec += (60-$qsec%60);}
 			$qmin = $qsec/60;
			print STDERR "hold:".$qsec."\n" if ($DB);
			my $dcnt=$qmin;
			$dcnt=1 if ($dcnt<1);
			stop_stream();
			stream_file('queue-holdtime');
			if ($qmin<6) {
				stream_file('queue-less-than');
			}
			if (length($dcnt)==2) {
				my $dig = substr($dcnt,0,1);
				stream_file('digits/'.$dig.'0');
				$dcnt = substr($dcnt,1,1);
			}
			if (length($dcnt)==1 and $dcnt!=0) {
				my $dig = substr($dcnt,0,1);
				stream_file('digits/'.$dig);
			}
			if ($qmin==1) {
				stream_file('queue-minute');
			} else {
				stream_file('queue-minutes');
			}
			stream_file('sip-silence');
			stream_file('silence/2');
			$queuetime_counter = 0;
			$queuetime_playcount++;
			$last_played=0;
		}
	
		if ($inhold>0) {
			if ($last_played>$prompt_buffer) {
				$hold_message_counter++;
				$callback_message_counter++;
				$placement_counter++;
				$queuetime_counter++;
			}
			$last_played++;
		} elsif ($start_moh>1) {
			$start_moh=0;
			stream_file($hold,$streampos->{$hold});
			$inhold=1;
		}
	}

	my $newin = infunc();
	if ($ingroup->{callback_interval}>0) {
		$docallback=1 if ($newin eq $ingroup->{callback_interrupt_key});
		if ($docallback) {
			my $cbret = callback_prompt();
			$start_moh=2 if ($cbret);
		}
	}
	$drop_timer = time() - $start_epoch;

	$stmtA = sprintf("UPDATE osdial_auto_calls SET stage='LIVE-%s' WHERE callerid='%s';",$osdial->mres($drop_timer),$osdial->mres($vars->{accountcode}));
	$affected_rows = $osdial->sql_execute($stmtA);
	if ($affected_rows<1) {
		$stmtA = sprintf("INSERT INTO osdial_auto_calls (server_ip,campaign_id,status,lead_id,uniqueid,callerid,channel,phone_code,phone_number,call_time,call_type,stage) VALUES ('%s','%s','CLOSER','%s','%s','%s','%s','%s','%s','%s','IN','CLOSER-%s');",$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($channel_group),$osdial->mres($insert_lead_id),$osdial->mres($vars->{uniqueid}),$osdial->mres($vars->{accountcode}),$osdial->mres($vars->{channel}),$osdial->mres($phone_code),$osdial->mres($phone_number),$osdial->mres($SQLdate),$osdial->mres($drop_timer));
		$affected_rows = $osdial->sql_execute($stmtA);
		$agi_string = "--    $affected_rows|OSDAC-reinsert";
		$agi_string .= "|$stmtA|" if ($DB>1);
		$osdial->agi_output($agi_string);
	}
}










#### After call handling.
if ($drop_timer > $DROP_TIME) {
	stop_stream();
	$inaftercall=1;
	$now_date_epoch = time();
	$now_date = $osdial->get_datetime($now_date_epoch);
	$CIDdate = $now_date;
	$CIDdate =~ s/\D//g;
	$CIDdate = substr($CIDdate,4,10);
	$tsSQLdate = $now_date;
	$tsSQLdate =~ s/\D//g;
	$SQLdate = $now_date;
	$SQLdateBEGIN = $now_date;
	$drop_seconds = time() - $start_epoch;

	if ($osdial->{settings}{enable_queuemetrics_logging} > 0) {
		$osdial->sql_connect('QM',$osdial->{settings}{queuemetrics_dbname},$osdial->{settings}{queuemetrics_server_ip},'3306',$osdial->{settings}{queuemetrics_login},$osdial->{settings}{queuemetrics_pass});
		$osdial->agi_output("CONNECTED TO DATABASE:  ".$osdial->{settings}{queuemetrics_server_ip}."|".$osdial->{settings}{queuemetrics_dbname}) if ($DB>1);

        	my $place=0;
		$place = $rec_countWAIT if ($rec_countWAIT > 0);
        	$place++;

		my $stmtB = sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='NONE',verb='EXITWITHTIMEOUT',data1='%s',serverid='%s';",$osdial->mres($now_date_epoch),$osdial->mres($YqueryCID),$osdial->mres($channel_group),$osdial->mres($place),$osdial->mres($osdial->{settings}{queuemetrics_log_id}));
		$osdial->sql_execute($stmtB,'QM');

		$stmtB = sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='NONE',verb='CALLSTATUS',data1='DROP',serverid='%s';",$osdial->mres($now_date_epoch),$osdial->mres($YqueryCID),$osdial->mres($channel_group),$osdial->mres($osdial->{settings}{queuemetrics_log_id}));
		$osdial->sql_execute($stmtB,'QM');

		$osdial->sql_disconnect('QM');
	}

	my $DROPexten = '8307';
	if ($ingroup->{drop_action} =~ /EXTENSION/) {
		$DROPexten = $ingroup->{drop_exten};
	} elsif ($ingroup->{drop_action} =~ /CALLBACK/) {
		stream_start_file($ingroup->{drop_message_filename});
		stream_file('sip-silence');
		while ($inaftercall==1) {
			my $innew = infunc();
			$docallback=1;
		}
		callback_prompt() if ($docallback);
	} elsif ($ingroup->{drop_action} =~ /MESSAGE/) {
		stream_start_file($ingroup->{drop_message_filename});
		$DROPexten = '8307';
	} elsif ($ingroup->{drop_action} =~ /HANGUP/) {
		$DROPexten = '8307';
	} elsif ($ingroup->{drop_action} =~ /VOICEMAIL/) {
		$DROPexten = $osdial->{server}{voicemail_dump_exten}.$ingroup->{voicemail_ext} if (length($ingroup->{voicemail_ext})>0);
	}

	my $VHqueryCID = "VH$CIDdate$DROPexten";

	if (length($DROPexten)>0) {
		$osdial->agi_output("exiting the OSDAD app, transferring call to $DROPexten | $term_reason");
		stream_file('sip-silence');
		set_context($osdial->{server}{ext_context});
		set_extension($DROPexten);
		set_priority(1);
	} else {
		$stmtA = sprintf("INSERT INTO osdial_manager VALUES ('','','%s','NEW','N','%s','%s','Hangup','%s','Channel: %s','','','','','','','','','')",$osdial->mres($SQLdate),$osdial->mres($osdial->{'VARserver_ip'}),$osdial->mres($vars->{channel}),$osdial->mres($VHqueryCID),$osdial->mres($vars->{channel}));
		$affected_rows = $osdial->sql_execute($stmtA);
		$osdial->agi_output("--    OSDCL call_hungup timout: |$VHqueryCID|$DROPexten|".$vars->{channel}."|insert to osdial_manager");
	}

	$stmtA = sprintf("DELETE FROM osdial_auto_calls WHERE callerid='%s' AND server_ip='%s' ORDER BY call_time DESC LIMIT 1;",$osdial->mres($vars->{accountcode}),$osdial->mres($osdial->{'VARserver_ip'}));
	$affected_rows = $osdial->sql_execute($stmtA);
	$osdial->agi_output("--    OSDCL vac record deleted: |$affected_rows| $channel_group|");

	$stmtA = sprintf("UPDATE osdial_closer_log SET status='DROP',end_epoch='%s',length_in_sec='%s',queue_seconds='%s',term_reason='%s' WHERE lead_id='%s' ORDER BY start_epoch DESC LIMIT 1;",$osdial->mres($now_date_epoch),$osdial->mres($drop_seconds),$osdial->mres($drop_seconds),$osdial->mres($term_reason),$osdial->mres($insert_lead_id));
	$affected_rows = $osdial->sql_execute($stmtA);
	$agi_string = "--    OSDCL ocl update: |$affected_rows|$insert_lead_id";
	$agi_string .= "\n|$stmtA|" if ($DB>1);
	$osdial->agi_output($agi_string);

	$stmtA = sprintf("UPDATE osdial_list SET status='XDROP' WHERE lead_id='%s';",$osdial->mres($insert_lead_id));
	$affected_rows = $osdial->sql_execute($stmtA);
	$agi_string = "--    OSDCL ol update: |$affected_rows|$insert_lead_id";
	$agi_string .= "\n|$stmtA|" if ($DB>1);
	$osdial->agi_output($agi_string);
}


$osdial->sql_disconnect();
exit_ivr();


sub stop_stream {
	print "I,".time()."\n";
}

sub start_hold {
	$start_moh=1;
}

sub stream_start_file {
	my ($file,$pos) = @_;
	$pos=0 if (!defined($pos));
	print "S,$file,$pos\n";
}

sub stream_file {
	my ($file,$pos) = @_;
	$pos=0 if (!defined($pos));
	print "A,$file,$pos\n";
}

sub set_variable {
	my ($vkey,$vval) = @_;
	print "V,$vkey=$vval\n";
}

sub set_context {
	my ($context) = @_;
	print "V,IVR_GOTO=1\n";
	print "V,IVR_CONTEXT=$context\n";
}

sub set_extension {
	my ($extension) = @_;
	print "V,IVR_GOTO=1\n";
	print "V,IVR_EXTEN=$extension\n";
}

sub set_priority {
	my ($priority) = @_;
	print "V,IVR_GOTO=1\n";
	print "V,IVR_PRIORITY=$priority\n";
}

sub exit_ivr {
	my ($hangup) = @_;
	my $exit = 'E';
	$exit = 'H' if ($hangup);
	print "$exit,.\n";
	my $t=1;
	while ($t++) {
		exit if ($t>100);
		my $newin = infunc();
		sleep 1;
	}
}

sub callback_prompt {
	$docallback=0;
	$inafterhours=0;
	$inwelcome=0;
	$incallback=1;
	my $cbnumber='';
	stream_start_file('to-be-called-back');
	stream_file('digits/1');
	my $cbnext=0;
	my $cbtime=0;
	while ($incallback==1 and $cbnext==0) {
		my $newin = infunc();
		if ($newin eq '1') {
			$cbnext=1;
		} elsif ($newin =~ /[02-9\*#]/) {
			$cbnext=2;
		} elsif ($cbtime>10) {
			$cbnext=2;
		}
		$cbtime++ if (!defined($newin));
	}
	stop_stream();
	if ($cbnext==1) {
		if ($vars->{callerid} eq '') {
			$cbnext=2;
		} else {
			stream_start_file('to-call-this-number');
			stream_file('press-1');
			stream_file('to-enter-a-number');
			stream_file('press-2');
			$cbnext=0;
			$cbtime=0;
			while ($incallback==1 and $cbnext==0) {
				my $newin = infunc();
				if ($newin eq '1') {
					$cbnext=1;
					$cbnumber=$vars->{callerid};
				} elsif ($newin eq '2') {
					$cbnext=2;
				} elsif ($newin =~ /[03-9\*#]/) {
					$cbnext=3;
				} elsif ($cbtime>10) {
					$cbnext=3;
				}
				$cbtime++ if (!defined($newin));
			}
			stop_stream();
		}
		if ($cbnext==3) {
			$incallback=0;
			return 1;
		}
		if ($cbnext==1) {
			stream_start_file('your');
			stream_file('number');
			stream_file('is');
			my @nums = split(//,$cbnumber);
			foreach my $num (@nums) {
				stream_file('digits/'.$num);
			}
			stream_file('if-this-is-correct-press');
			stream_file('digits/1');
			stream_file('to-enter-a-number');
			stream_file('press-2');
			$cbnext=0;
			$cbtime=0;
			while ($incallback==1 and $cbnext==0) {
				my $newin = infunc();
				if ($newin eq '1') {
					$cbnext=1;
					$cbnumber=$vars->{callerid};
				} elsif ($newin eq '2') {
					$cbnext=2;
				} elsif ($newin =~ /[03-9\*#]/) {
					$cbnext=3;
				} elsif ($cbtime>10) {
					$cbnext=3;
				}
				$cbtime++ if (!defined($newin));
			}
		}
		if ($cbnext==3) {
			$incallback=0;
			return 1;
		}
		while ($cbnext==2) {
			$cbnumber='';
			stream_start_file('please-enter-your');
			stream_file('number');
			stream_file('and-prs-pound-whn-finished');
			$cbnext=0;
			$cbtime=0;
			while ($incallback==1 and $cbnext==0) {
				my $newin = infunc();
				if ($newin =~ /[0-9]/) {
					$cbnumber .= $newin;
					$cbtime=0;
				} elsif ($newin eq '#') {
					$cbnext=1;
				} elsif ($cbtime>10) {
					$cbnext=3;
				}
				$cbtime++ if (!defined($newin));
			}
			if ($cbnext==3) {
				$incallback=0;
				return 1;
			}
			if ($cbnext==1) {
				stream_start_file('you-entered');
				my @nums = split(//,$cbnumber);
				foreach my $num (@nums) {
					stream_file('digits/'.$num);
				}
				stream_file('if-this-is-correct');
				stream_file('press-1');
				stream_file('if-this-is-not-correct');
				stream_file('press-2');
				$cbnext=0;
				$cbtime=0;
				while ($incallback==1 and $cbnext==0) {
					my $newin = infunc();
					if ($newin eq '1') {
						$cbnext=1;
					} elsif ($newin eq '2') {
						$cbnext=2;
					} elsif ($newin =~ /[03-9\*#]/) {
						$cbnext=3;
					} elsif ($cbtime>10) {
						$cbnext=3;
					}
					$cbtime++ if (!defined($newin));
				}
				if ($cbnext==3) {
					$incallback=0;
					return 1;
				}
			}
		}
		$stmtA = sprintf("UPDATE osdial_list SET status='CBHOLD',called_since_last_reset='N',called_count='0',phone_number='%s' WHERE lead_id='%s';",$osdial->mres($cbnumber),$osdial->mres($insert_lead_id));
		$osdial->sql_execute($stmtA);

		$stmtA = sprintf("SELECT * FROM osdial_lists WHERE list_id='%s';",$osdial->mres($list_id));
		my $list = $osdial->sql_query($stmtA);

		$now_date_epoch = time();
		$now_date = $osdial->get_datetime($now_date_epoch);
		my $cbtime = $osdial->get_datetime($now_date_epoch+120);
		$drop_seconds = $now_date_epoch - $start_epoch;

		$stmtA = sprintf("INSERT INTO osdial_callbacks SET lead_id='%s',list_id='%s',campaign_id='%s',status='ACTIVE',entry_time=NOW(),callback_time='%s',user='VDCL',recipient='ANYONE',comments='Inbound call to %s, group %s, requested callback while waiting in queue.';",$osdial->mres($insert_lead_id),$osdial->mres($list_id),$osdial->mres($list->{campaign_id}),$osdial->mres($cbtime),$osdial->mres($inbound_number),$osdial->mres($channel_group));
		$osdial->sql_execute($stmtA);

		$stmtA = sprintf("DELETE FROM osdial_auto_calls WHERE callerid='%s' AND server_ip='%s' ORDER BY call_time DESC LIMIT 1;",$osdial->mres($vars->{accountcode}),$osdial->mres($osdial->{'VARserver_ip'}));
		$affected_rows = $osdial->sql_execute($stmtA);
		$osdial->agi_output("--    OSDCL vac record deleted: |$affected_rows| $channel_group|");

		$stmtA = sprintf("UPDATE osdial_closer_log SET status='CALLBK',phone_number='%s',end_epoch='%s',length_in_sec='%s',queue_seconds='%s',term_reason='CALLER' WHERE lead_id='%s' ORDER BY start_epoch DESC LIMIT 1;",$osdial->mres($cbnumber),$osdial->mres($now_date_epoch),$osdial->mres($drop_seconds),$osdial->mres($drop_seconds),$osdial->mres($insert_lead_id));
		$affected_rows = $osdial->sql_execute($stmtA);
		$agi_string = "--    OSDCL ocl update: |$affected_rows|$insert_lead_id";
		$agi_string .= "\n|$stmtA|" if ($DB>1);
		$osdial->agi_output($agi_string);

		stream_start_file('thank-you-for-calling');
		sleep 5;
		stream_start_file('sip-silence');
	} else {
		$incallback=0;
		return 1;
	}
}

sub enter_pin_number {
	my ($digits_to_collect) = @_;
	$in_enterpin=1;

	stop_stream();
	stream_file('four_digit_id');
	my $totalDTMF='';
	my $newin = infunc();
	$totalDTMF=$newin;
	$osdial->agi_output("interrupt_digit |".$newin."|");

	my $digit_loop_counter=length($totalDTMF);
	while ($digit_loop_counter<$digits_to_collect) {
		my $newin = infunc();
		my $digit = $newin;
		if ($digit =~ /\d/) {
			$totalDTMF .= $digit;
			$osdial->agi_output("digit |$digit|     TotalDTMF |$totalDTMF|");
			undef $digit;
		} else {
			$digit_loop_counter=$digits_to_collect;
		}
		$totalDTMF =~ s/\D//gi;
		$osdial->agi_output("digit |$digit|     TotalDTMF |$totalDTMF|") if ($totalDTMF);
		$digit_loop_counter++;
	}
	$in_enterpin=0;
	$start_moh=2;

	return $totalDTMF;
}
