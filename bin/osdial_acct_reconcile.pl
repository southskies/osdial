#!/usr/bin/perl
#
#  osdial_acct_reconcile.pl: Reconsile osdial_acct_trans table.
#
#  Copyright (C) 2013  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
use Getopt::Long;
use Mail::Sendmail;


my $DB = 0;
my $TEST = 0;
my $VERBOSE = 1;

my($CLOhelp, $CLOforce);

my $prog = 'osdial_acct_reconcile.pl';
my $osdial = OSDial->new('DB'=>$DB);

# Read in command-line options.
if (scalar @ARGV) {
        GetOptions(
                'help!' => \$CLOhelp,
                'debug!' => \$DB,
                'test!' => \$TEST,
                'verbose!' => \$VERBOSE,
                'force!' => \$CLOforce
        );
        if ($DB) {
                print "----- DEBUGGING -----\n";
                print "----- Testing Mode -----\n" if ($TEST);
                print "VARS-\n";
                print "CLOhelp-     $CLOhelp\n";
                print "VERBOSE-     $VERBOSE\n";
                print "TEST-        $TEST\n";
                print "CLOforce-    $CLOforce\n";
                print "\n";
        }
        if ($CLOhelp) {
                print "\n\n" . $prog;
                print "allowed run-time options:\n";
                print "  [--help]       = This screen\n";
                print "  [--debug]      = debug\n";
                print "  [-t|--test]    = test only\n";
                print "  [-v|--verbose] = Increase output\n";
                print "  [-f|--force]   = Force run of daily reconcile\n";
                exit 0;
        }
}

while (my $comp = $osdial->sql_query(sprintf("SELECT *,IF(NOW()>=acct_startdate OR acct_startdate='0000-00-00 00:00:00',1,0) AS started,IF(NOW()>acct_enddate AND acct_enddate!='0000-00-00 00:00:00',1,0) AS ended,IF(acct_enddate!='0000-00-00 00:00:00',DATEDIFF(acct_enddate,NOW()),0) AS daystillend FROM osdial_companies WHERE acct_method NOT IN ('','NONE');"),"COMP")) {

	# Scan daily trans to see if INIT has been run today.
	my $initcnt=0;
	while (my $sr_oat = $osdial->sql_query(sprintf("SELECT count(*) AS cnt FROM osdial_acct_trans_daily WHERE company_id='%s' AND trans_type='INIT' AND created>='%s';",$comp->{'id'},$osdial->get_date(time()).' 00:00:00'),"OAT")) {
		$initcnt=$sr_oat->{'cnt'};
	}

	my $expired_email={};
	# Run reconcile if no INIT or forced.
	if ($comp->{'acct_method'} ne 'RANGE' and ($initcnt==0 or $CLOforce)) {
		my $compsec = {};
		while (my $sr_oat = $osdial->sql_query(sprintf("SELECT *,IF(NOW()>expire_date AND expire_date!='0000-00-00 00:00:00',1,0) AS expired FROM osdial_acct_trans WHERE company_id='%s' AND trans_sec>0 AND ref_id=0 AND reconciled='0000-00-00 00:00:00' AND trans_type!='DATE';",$comp->{'id'}),"OAT")) {
			my $tid=$sr_oat->{'id'};
			my $company=$sr_oat->{'company_id'};
			my $credit=$sr_oat->{'trans_sec'};
			my $credit2=$sr_oat->{'trans_sec'};
			my $expire_date=$sr_oat->{'expire_date'};
			my $expired=$sr_oat->{'expired'};

			while (my $sr_oat2 = $osdial->sql_query(sprintf("SELECT * FROM osdial_acct_trans WHERE company_id='%s' AND ref_id='%s' AND trans_type!='DATE';",$company,$tid),"OAT2")) {
				$credit+=$sr_oat2->{'trans_sec'};
			}

			if ($credit>0) {
				my $adj=0;
				my $stmt = sprintf("SELECT * FROM osdial_acct_trans WHERE company_id='%s' AND trans_sec<=0 AND ref_id='0' AND updated<NOW()-200 AND trans_type!='DATE';",$company);
				if ($expire_date != '0000-00-00 00:00:00') {
					$stmt = sprintf("SELECT * FROM osdial_acct_trans WHERE company_id='%s' AND trans_sec<=0 AND ref_id='0' AND updated<NOW()-200 AND created<='%s' AND trans_type!='DATE';",$company,$expire_date);
				}
				while (my $sr_oat2 = $osdial->sql_query($stmt,"OAT2")) {
					if ($credit>0) {
						$credit+=$sr_oat2->{'trans_sec'};
						my $stmtA = sprintf("UPDATE osdial_acct_trans SET ref_id='%s' WHERE id='%s';",$tid,$sr_oat2->{'id'});
						$osdial->sql_execute($stmtA,'A') if (!$TEST);
						if ($credit<0) {
							if ($adj==0) {
								my $stmtA = sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',agent_log_id='%s',trans_type='ADJUSTMENT',trans_sec='%s',ref_id='%s',created='%s';",$company,$sr_oat2->{'agent_log_id'},($credit*-1),$tid,$sr_oat2->{'created'});
								$osdial->sql_execute($stmtA,'A') if (!$TEST);
								my $stmtA = sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',agent_log_id='%s',trans_type='ADJUSTMENT',trans_sec='%s',created='%s';",$company,$sr_oat2->{'agent_log_id'},$credit,$sr_oat2->{'created'});
								$osdial->sql_execute($stmtA,'A') if (!$TEST);
								$adj++;
							}
						}
					}
				}
				if ($credit>0) {
					if ($expired>0) {
						my $stmtA = sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='EXPIRED',trans_sec='%s',ref_id='%s',created=NOW();",$company,($credit*-1),$tid);
						$osdial->sql_execute($stmtA,'A') if (!$TEST);

						my $tkey='T'.$tid;
						my $acctdata={};
						$acctdata->{'acct_minutes_remaining'} = '0';
						$acctdata->{'acct_days_remaining'} = '0';
						$acctdata->{'acct_days_remaining'} = $comp->{'daystillend'} if ($comp->{'daystillend'}>0);
						$acctdata->{'acct_credit_total'} = '0';
						$acctdata->{'acct_credit_total'} = int($credit2/60) if ($credit2>0);
						$acctdata->{'acct_credit_expired'} = '0';
						$acctdata->{'acct_credit_expired'} = int($credit/60) if ($credit>0);
						my $cused = ($credit2 - $credit);
						$acctdata->{'acct_credit_used'} = '0';
						$acctdata->{'acct_credit_used'} = int($cused/60) if ($cused>0);
						$expired_email->{$tkey} = $acctdata;
					}
				}
			}

			while (my $sr_oat2 = $osdial->sql_query(sprintf("SELECT * FROM osdial_acct_trans WHERE company_id='%s' AND ref_id='%s' AND trans_type!='DATE';",$company,$tid),"OAT2")) {
				$credit2+=$sr_oat2->{'trans_sec'};
			}
			if ($credit2==0) {
				my $stmtA = sprintf("UPDATE osdial_acct_trans SET reconciled=NOW() WHERE id='%s' AND trans_type!='DATE';",$tid);
				$osdial->sql_execute($stmtA,'A') if (!$TEST);
				my $stmtA = sprintf("UPDATE osdial_acct_trans SET reconciled=NOW() WHERE ref_id='%s' AND trans_type!='DATE';",$tid);
				$osdial->sql_execute($stmtA,'A') if (!$TEST);
			}
			while (my $sr_oat2 = $osdial->sql_query(sprintf("SELECT * FROM osdial_acct_trans WHERE company_id='%s' AND trans_sec<=0 AND reconciled='0000-00-00 00:00:00' AND trans_type!='DATE';",$company),"OAT2")) {
				$credit2-=$sr_oat2->{'trans_sec'};
			}

			$compsec->{'C'.$company}=0 if (!defined($compsec->{'C'.$company}));
			$compsec->{'C'.$company}+=$credit2;
		}

		# Clean daily trans, add fresh INIT record and un-reconciled debits.
		foreach my $comps (keys %{$compsec}) {
			my $stmtA = sprintf("DELETE FROM osdial_acct_trans_daily WHERE company_id='%s';",$comp->{'id'});
			$osdial->sql_execute($stmtA,'A') if (!$TEST);

			my $company=$comps;
			$company =~ s/^C//;
			my $stmtA = sprintf("INSERT INTO osdial_acct_trans_daily SET company_id='%s',trans_type='INIT',trans_sec='%s',created=NOW();",$company,$compsec->{$comps});
			$osdial->sql_execute($stmtA,'A') if (!$TEST);

			while (my $sr_oat2 = $osdial->sql_query(sprintf("SELECT * FROM osdial_acct_trans WHERE company_id='%s' AND trans_sec<=0 AND reconciled='0000-00-00 00:00:00' AND trans_type!='DATE';",$company),"OAT2")) {
				my $stmtA = sprintf("INSERT INTO osdial_acct_trans_daily SET company_id='%s',agent_log_id='%s',trans_type='%s',trans_sec='%s',created='%s';",$company,$sr_oat2->{'agent_log_id'},$sr_oat2->{'trans_type'},$sr_oat2->{'trans_sec'},$sr_oat2->{'created'});
				$osdial->sql_execute($stmtA,'A') if (!$TEST);
			}
		}
	}


	my $cursus = 0;
	my $curact = 0;
	my $cstate = $comp->{'status'};
	$curact++ if ($cstate eq 'ACTIVE');
	$cursus++ if ($cstate eq 'SUSPENDED');

	# Evaluate remaining time and cutoff.
	my $pastcutoff=0;
	my $emailwarning=0;
	my $emailwarningmin=0;
	my $emailwarningday=0;
	my $secremain=0;
	while (my $sr_oat = $osdial->sql_query(sprintf("SELECT company_id,sum(trans_sec) AS secremain FROM osdial_acct_trans_daily WHERE company_id='%s';",$comp->{'id'}),"OAT")) {
		$secremain=$sr_oat->{'secremain'};
	}
	$pastcutoff++ if (($comp->{'acct_cutoff'}*60)>=$secremain);
	if ($comp->{'email'} ne '' and $comp->{'acct_warning_sent'} eq '0000-00-00 00:00:00' and $osdial->{'settings'}{'acct_email_warning_time'} ne '0' and ($osdial->{'settings'}{'acct_email_warning_time'}*60)>=$secremain) {
		$emailwarning++;
		$emailwarningmin++;
	}
	if ($comp->{'email'} ne '' and $comp->{'acct_warning_sent'} eq '0000-00-00 00:00:00' and $comp->{'acct_enddate'} ne '0000-00-00 00:00:00' and $osdial->{'settings'}{'acct_email_warning_expire'} ne '0' and $osdial->{'settings'}{'acct_email_warning_expire'}>=$comp->{'daystillend'}) {
		$emailwarning++;
		$emailwarningday++;
	}

	# Send emails for expired time trans.
	foreach my $expacctkey (keys %{$expired_email}) {
		my $acctdata = $expired_email->{$expacctkey};
		$acctdata->{'acct_minutes_remaining'} = int($secremain / 60) if ($secremain>0);
		my $etid='MCABCREDEXP';
		while (my $et = $osdial->sql_query(sprintf("SELECT * FROM osdial_email_templates WHERE et_id='%s';",$etid),"ET")) {
			my $et_subject = mergeETdata($et->{'et_subject'},$osdial->{'settings'},$comp,$acctdata);
			my $et_body_html = mergeETdata($et->{'et_body_html'},$osdial->{'settings'},$comp,$acctdata);
			my $et_body_text = mergeETdata($et->{'et_body_text'},$osdial->{'settings'},$comp,$acctdata);

			my $eres = $osdial->send_email({host=>$et->{'et_host'},port=>$et->{'et_port'},user=>$et->{'et_user'},pass=>$et->{'et_pass'},to=>$comp->{'email'},from=>$osdial->{'settings'}{'system_email'},subject=>$et_subject,html=>$et_body_html,text=>$et_body_text});
		}
	}

	# Evaluate company startdate and enddate.
	if ($curact) {
		$cstate = 'SUSPENDED' if ($comp->{'started'}==0);
		$cstate = 'SUSPENDED' if ($comp->{'ended'}==1);
		$cstate = 'SUSPENDED' if ($pastcutoff>0);
		$cstate = 'SUSPENDED' if ($secremain<=0);
	} elsif ($cursus) {
		$cstate = 'ACTIVE' if ($comp->{'started'}==1 and $comp->{'ended'}==0 and $pastcutoff==0 and $secremain>0);
	}

	# Send email warnings.
	if ($comp->{'status'} eq 'ACTIVE') {
		if ($comp->{'email'} ne '') {
			my $etid='';
			# warn credit low
			$etid='MCABCREDWARN' if ($emailwarningmin);
			# warn days low
			$etid='MCABACCTWARN' if ($emailwarningday);
			# no credit left;
			$etid='MCABCREDNO' if ($pastcutoff);
			# no days left;
			$etid='MCABACCTEXP' if ($comp->{'ended'}==1);
			while (my $et = $osdial->sql_query(sprintf("SELECT * FROM osdial_email_templates WHERE et_id='%s';",$etid),"ET")) {
				my $acctdata={};
				$acctdata->{'acct_minutes_remaining'} = '0';
				$acctdata->{'acct_minutes_remaining'} = int($secremain / 60) if ($secremain>0);
				$acctdata->{'acct_days_remaining'} = '0';
				$acctdata->{'acct_days_remaining'} = $comp->{'daystillend'} if ($comp->{'daystillend'}>0);
				$acctdata->{'acct_credit_total'} = $acctdata->{'acct_minutes_remaining'};
				$acctdata->{'acct_credit_used'} = '0';
				$acctdata->{'acct_credit_expired'} = '0';
				my $et_subject = mergeETdata($et->{'et_subject'},$osdial->{'settings'},$comp,$acctdata);
				my $et_body_html = mergeETdata($et->{'et_body_html'},$osdial->{'settings'},$comp,$acctdata);
				my $et_body_text = mergeETdata($et->{'et_body_text'},$osdial->{'settings'},$comp,$acctdata);

				my $eres = $osdial->send_email({host=>$et->{'et_host'},port=>$et->{'et_port'},user=>$et->{'et_user'},pass=>$et->{'et_pass'},to=>$comp->{'email'},from=>$osdial->{'settings'}{'system_email'},subject=>$et_subject,html=>$et_body_html,text=>$et_body_text});

				if ($eres) {
					if ($emailwarning) {
						my $stmtA = sprintf("UPDATE osdial_companies SET acct_warning_sent=NOW() WHERE id='%s';",$comp->{'id'});
						$osdial->sql_execute($stmtA,'A') if (!$TEST);
					}
				}
			}
		}
	}
	if ($comp->{'acct_warning_sent'} ne '0000-00-00 00:00:00' and ($osdial->{'settings'}{'acct_email_warning_time'} eq '0' or ($osdial->{'settings'}{'acct_email_warning_time'}*60)<$secremain) and ($osdial->{'settings'}{'acct_email_warning_expire'} eq '0' or $osdial->{'settings'}{'acct_email_warning_expire'}<$comp->{'daystillend'} or $comp->{'acct_enddate'} eq '0000-00-00 00:00:00')) {
		my $stmtA = sprintf("UPDATE osdial_companies SET acct_warning_sent='0000-00-00 00:00:00' WHERE id='%s';",$comp->{'id'});
		$osdial->sql_execute($stmtA,'A') if (!$TEST);
	}

	# Set status and remaining time.
	my $stmtA = sprintf("UPDATE osdial_companies SET status='%s',acct_remaining_time='%s' WHERE id='%s';",$cstate,$secremain,$comp->{'id'});
	$osdial->sql_execute($stmtA,'A') if (!$TEST);
}


sub mergeETdata {
	my ($form,$dsys,$dcomp,$dacct) = @_;

	my $weburl='';
	while (my $wusvr = $osdial->sql_query(sprintf("SELECT * FROM servers WHERE active='Y' AND server_profile IN ('AIO','CONTROL','WEB') ORDER BY web_url DESC, RAND();"),"WU")) {
		$weburl=$wusvr->{'web_url'};
		if ($weburl eq '') {
			if ($wusvr->{'server_id'} eq '' or $wusvr->{'server_domainname'} eq '') {
				$weburl='http://'.$wusvr->{'server_ip'}.'/';
			} else {
				$weburl='http://'.$wusvr->{'server_id'}.'.'.$wusvr->{'server_domainname'}.'/';
			}
		}
	}
	my $cprefix = (($dcomp->{'id'} * 1) + 100);
	$form =~ s/\[\:system_company_name\:\]/$dsys->{'company_name'}/g;
	$form =~ s/\[\:system_email\:\]/$dsys->{'system_email'}/g;
	$form =~ s/\[\:system_web_url\:\]/$weburl/g;
	$form =~ s/\[\:company_prefix\:\]/$cprefix/g;
	$form =~ s/\[\:company_password_prefix\:\]/$dcomp->{'password_prefix'}/g;
	$form =~ s/\[\:company_name\:\]/$dcomp->{'name'}/g;
	$form =~ s/\[\:company_status\:\]/$dcomp->{'status'}/g;
	$form =~ s/\[\:company_email\:\]/$dcomp->{'email'}/g;
	$form =~ s/\[\:company_contact_name\:\]/$dcomp->{'contact_name'}/g;
	$form =~ s/\[\:company_contact_phone_number\:\]/$dcomp->{'contact_phone_number'}/g;
	$form =~ s/\[\:company_contact_address\:\]/$dcomp->{'contact_address'}/g;
	$form =~ s/\[\:company_contact_address2\:\]/$dcomp->{'contact_address2'}/g;
	$form =~ s/\[\:company_contact_city\:\]/$dcomp->{'contact_city'}/g;
	$form =~ s/\[\:company_contact_state\:\]/$dcomp->{'contact_state'}/g;
	$form =~ s/\[\:company_contact_postal_code\:\]/$dcomp->{'contact_postal_code'}/g;
	$form =~ s/\[\:company_contact_country\:\]/$dcomp->{'contact_country'}/g;
	$form =~ s/\[\:acct_method\:\]/$dcomp->{'acct_method'}/g;
	$form =~ s/\[\:acct_start_date\:\]/$dcomp->{'acct_startdate'}/g;
	$form =~ s/\[\:acct_end_date\:\]/$dcomp->{'acct_enddate'}/g;
	$form =~ s/\[\:acct_minutes_remaining\:\]/$dacct->{'acct_minutes_remaining'}/g;
	$form =~ s/\[\:acct_days_remaining\:\]/$dacct->{'acct_days_remaining'}/g;
	$form =~ s/\[\:acct_credit_total\:\]/$dacct->{'acct_credit_total'}/g;
	$form =~ s/\[\:acct_credit_used\:\]/$dacct->{'acct_credit_used'}/g;
	$form =~ s/\[\:acct_credit_expired\:\]/$dacct->{'acct_credit_expired'}/g;

	return $form;
}


exit 0;
