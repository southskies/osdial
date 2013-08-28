<?php
# osdial_acct.php - OSDial
# 
# Copyright (C) 2013  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
#
#     This file is part of OSDial.
#
#     OSDial is free software: you can redistribute it and/or modify
#     it under the terms of the GNU Affero General Public License as
#     published by the Free Software Foundation, either version 3 of
#     the License, or (at your option) any later version.
#
#     OSDial is distributed in the hope that it will be useful,
#     but WITHOUT ANY WARRANTY; without even the implied warranty of
#     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#     GNU Affero General Public License for more details.
#
#     You should have received a copy of the GNU Affero General Public
#     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
#
#
#
# Includes
require_once("include/dbconnect.php");
require_once("include/functions.php");

$output = date('Y-m-d H:i:s') . "\n";

$ip = $_SERVER['REMOTE_ADDR'];
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
$remote_name = gethostbyaddr($ip);
$output.="Remote Name:$remote_name\n";

if (OSDpreg_match('/paypal/',$remote_name)) {
	$ppvar = array();
	$payment_method = 'Paypal';
	$payment_type = '';
	$payment_details = '';
	$payment_amount = '0.00';
	$verifysite = 'https://www.paypal.com/cgi-bin/webscr';
	if (OSDpreg_match('/sandbox/',$remote_name)) $verifysite = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

	$verifyparams = 'cmd=_notify-validate';

	$output.="TEST:$TEST\n\n";
	$output.="SERVER:\n";
	foreach ($_SERVER as $k => $v) {
		$output .= $k.": ".$v."\n";
	}
	#$output.="\n\nPOST:\n";
	foreach ($_POST as $k => $v) {
		$verifyparams.='&'.$k.'='.htmlentities($v);
		if ($k=="payment_date") {
			$tpd=strtotime($v);
			$ppvar['payment_date_orig']=$v;
			$v=date('Y-m-d H:i:s',$tpd);
		}
		$ppvar[$k]=$v;
		#$output .= $k.": ".$v."\n";
	}
	$verifyparams = OSDpreg_replace('/ /','+',$verifyparams);
	$verifyurl=$verifysite.'?'.$verifyparams;
	$output.="\n\nVERIFY:".$verifyurl;
	$output.="\n\n";

	$ch = curl_init($verifyurl);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$curl_content = curl_exec($ch);
	curl_close($ch);
	$output.='CURL:'.$curl_content."\n\n";

	$error='';

	if ($curl_content == 'VERIFIED') {
		$email=$ppvar['payer_email'];
		$purchase_type=$ppvar['item_number'];
		$payment_type=$ppvar['payment_status'].':'.$ppvar['txn_type'];
		$payment_amount=$ppvar['payment_gross'];
		$payment_date=$ppvar['payment_date'];
		$payment_transid=$ppvar['txn_id'];
		$purchase_quantity=$ppvar['quantity'];
		ksort($ppvar);
		$output.="\n\nPPVAR:\n";
		foreach ($ppvar as $ppvk=>$ppvv) {
			$output .= $ppvk.": ".$ppvv."\n";
			$payment_details.=$ppvk.':='.$ppvv."\n";
		}

		$trans_id=0;
		$comp = get_first_record($link, 'osdial_companies', '*', sprintf("email='%s'",mres($email)) );
		$pck = get_first_record($link, 'osdial_acct_packages', '*', sprintf("code='%s'",mres($purchase_type)) );
		if ($pck['ptype']=='OTHER' and $pck['other_action']=='CREATE_NEW_COMPANY') {
			if ($comp['email']!=$email) {
				$default_acct_method=$config['settings']['default_acct_method'];
				if ($pck['other_newcomp_acct_method']!='') $default_acct_method=$pck['other_newcomp_acct_method'];

				$stmt=sprintf("INSERT INTO osdial_companies SET name='%s',acct_method='%s',acct_cutoff='%s',acct_expire_days='%s',email='%s',contact_name='%s',contact_phone_number='%s',contact_address='%s',contact_address2='%s',contact_city='%s',contact_state='%s',contact_postal_code='%s',contact_country='%s',status='ACTIVE',ext_context='%s';",mres($ppvar['option_selection1']),mres($default_acct_method),mres($config['settings']['default_acct_cutoff']),mres($config['settings']['default_acct_expire_days']),mres($email),mres($ppvar['first_name'].' '.$ppvar['last_name']),mres($ppvar['option_selection2']),mres($ppvar['address_street']),mres($ppvar['address_street2']),mres($ppvar['address_city']),mres($ppvar['address_state']),mres($ppvar['address_zip']),mres($ppvar['address_country_code']),mres($config['settings']['default_ext_context']));
				$rslt=mysql_query($stmt, $link);
				$company_id =  mysql_insert_id($link);

				$server_ip=$config['VARserver_ip'];

				$cmp = (($company_id * 1) + 100);

				# Add inital sample configuration.
				$pins = "INSERT INTO phones VALUES ";
				$pins .= sprintf("('%s1001','%s1001','%s1001','','','%s','%s1001','1001','ACTIVE','Y','SIP','Ext %s 1001','%s','NA',0,0,'SIP','%s','cron','1234','','','','8301','8302','8301','park','8612','8309','8501','85026666666666','%s','local/8500998@osdial','Zap/g2/','/usr/bin/mozilla','/usr/local/perl_TK','http://localhost/test_callerid_output.php','http://localhost/test_osdial_output.php','1','1','1','0','0','1','1','1','1','1','1','1','0',1000,'0','1','1','','asterisk','cron','1234',3306,'','asterisk','cron','1234',3306,'%s1001','0','','1234',''),",$cmp,$cmp,$cmp,$server_ip,$cmp,$cmp,$cmp,$local_gmt,$config['settings']['default_ext_context'],$cmp);
				$pins .= sprintf("('%s1002','%s1002','%s1002','','','%s','%s1002','1002','ACTIVE','Y','SIP','Ext %s 1002','%s','NA',0,0,'SIP','%s','cron','1234','','','','8301','8302','8301','park','8612','8309','8501','85026666666666','%s','local/8500998@osdial','Zap/g2/','/usr/bin/mozilla','/usr/local/perl_TK','http://localhost/test_callerid_output.php','http://localhost/test_osdial_output.php','1','1','1','0','0','1','1','1','1','1','1','1','0',1000,'0','1','1','','asterisk','cron','1234',3306,'','asterisk','cron','1234',3306,'%s1002','0','','1234',''),",$cmp,$cmp,$cmp,$server_ip,$cmp,$cmp,$cmp,$local_gmt,$config['settings']['default_ext_context'],$cmp);
				$pins .= sprintf("('%s9999','9999','%s9999','','','%s','%s9999','9999','ACTIVE','Y','Test','Test Phone','%s','NA',0,0,'EXTERNAL','%s','cron','1234','','','','8301','8302','8301','park','8612','8309','8501','85026666666666','%s','local/8500998@osdial','Zap/g2/','/usr/bin/mozilla','/usr/local/perl_TK','http://localhost/test_callerid_output.php','http://localhost/test_osdial_output.php','1','1','1','0','0','1','1','1','1','1','1','1','0',1000,'0','1','1','','asterisk','cron','1234',3306,'','asterisk','cron','1234',3306,'%s9999','0','','1234','');",$cmp,$cmp,$server_ip,$cmp,$cmp,$local_gmt,$config['settings']['default_ext_context'],$cmp);
				$rslt=mysql_query($pins, $link);

				$uins = "INSERT INTO osdial_users VALUES ";
				$uins .= sprintf("('','%sadmin','admin','Admin %s',9,'%sADMIN','','','1','1','1','1','1','1','1','1','1','1','1','1','0','1','1','','1','0','1','1','1','1','1','0','1','1','1','1','1','1','1','1','1','1','1','1','DISABLED','NOT_ACTIVE',-1,'1','1','1','1','0','','1','1','1'),",$cmp,$cmp,$cmp);
				$uins .= sprintf("('','%s1001','1001','Agent %s 1001',4,'%sAGENTS','','','0','0','0','0','0','0','0','0','0','0','0','0','1','0','0','','1','1','1','1','1','0','0','1','0','0','0','0','0','0','0','0','0','0','0','0','DISABLED','NOT_ACTIVE',-1,'1','0','0','0','0','','1','0','0'),",$cmp,$cmp,$cmp);
				$uins .= sprintf("('','%s1002','1002','Agent %s 1002',4,'%sAGENTS','','','0','0','0','0','0','0','0','0','0','0','0','0','1','0','0','','1','1','1','1','1','0','0','1','0','0','0','0','0','0','0','0','0','0','0','0','DISABLED','NOT_ACTIVE',-1,'1','0','0','0','0','','1','0','0');",$cmp,$cmp,$cmp);
				$rslt=mysql_query($uins, $link);

				$ugins = "INSERT INTO osdial_user_groups (user_group,group_name,allowed_campaigns,allowed_scripts,allowed_email_templates,allowed_ingroups) VALUES ";
				$ugins .= sprintf("('%sADMIN','OSDIAL ADMINISTRATORS',' -ALL-CAMPAIGNS- - -',' -ALL-SCRIPTS- -',' -ALL-EMAIL-TEMPLATES- -',' -ALL-INGROUPS- -'),('%sAGENTS','Agent User Group',' -ALL-CAMPAIGNS- - -',' -ALL-SCRIPTS- -',' -ALL-EMAIL-TEMPLATES- -',' -ALL-INGROUPS- -');",$cmp,$cmp);
				$rslt=mysql_query($ugins, $link);

				$sins = "INSERT INTO osdial_scripts VALUES ";
				$sins .= sprintf("('%sTEST','Test Script','Just a quick test','Hello Mr/Mrs [[last_name]], are you the [[organization_title]] with [[organization]],<br><br>We are calling you at [[phone_number]].<br><br>Your address is:<br>[[address1]]<br>[[city]], [[state]] [[postal_code]]<br><br>Thank-you','Y');",$cmp);
				$rslt=mysql_query($sins, $link);

				$olins = "INSERT INTO osdial_lists VALUES ";
				$olins .= sprintf("(%s998,'Default inbound list','%sTEST','N',NULL,NULL,NULL,'N',NULL,'',0,'','',''),",$cmp,$cmp);
				$olins .= sprintf("(%s999,'Default manual list','%sTEST','N',NULL,NULL,NULL,'N',NULL,'',0,'','','');",$cmp,$cmp);
				$rslt=mysql_query($olins, $link);

				$ocins = "INSERT INTO osdial_campaigns VALUES ";
				$ocins .= sprintf("('%sTEST','Test Campaign %s','Y','','','','','','DOWN','8301','park','/osdial/agent/webform_redirect.php','Y',200,'0','oldest_call_finish','24hours','',28,'9','0000000000','8368','8309','ONDEMAND','CAMPAIGN_AGENT_FULLDATE_CUSTPHONE','','NONE','8320','Y','','','','','N','Y','NONE',8,'Y','8307','Y',0,'Wrapup Call','','Y',0,'N','MANUAL','N',3,'3.0','2100','0',0,'AUTO','NONE',' A AA B N NA DC -','N','Test Campaign','2010-03-08 00:19:25','N',NULL,' A AA AL AM B CALLBK DROP NEW N NA -','N','Y','DISABLED','Y',%s999,'---NONE---','','/osdial/agent/webform_redirect.php','Y',0,'',10,'Y','Y','Y','NORMAL','N','2008-01-01 00:00:00','','CAMPAIGN','N','%s','','N','N','N','N','N','N','N','N','N','N','N',0);",$cmp,$cmp,$cmp,$config['settings']['default_carrier_id']);
				$rslt=mysql_query($ocins, $link);

				$ochkins = "INSERT INTO osdial_campaign_hotkeys VALUES ";
				$ochkins .= sprintf("('N','1','No Answer','Y','%sTEST',''),",$cmp);
				$ochkins .= sprintf("('A','2','Answering Machine','Y','%sTEST',''),",$cmp);
				$ochkins .= sprintf("('NI','3','Not Interested','Y','%sTEST',''),",$cmp);
				$ochkins .= sprintf("('CALLBK','4','Call Back','Y','%sTEST',''),",$cmp);
				$ochkins .= sprintf("('SALE','5','Sale Made','Y','%sTEST','');",$cmp);
				$rslt=mysql_query($ochkins, $link);

				$comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",mres($company_id)) );

				if ($comp['acct_method']=='NONE') {
					if ($comp['acct_expire_days'] != '0') {
						$purchase_expire_date = date('Y-m-d');
						$tdate = strtotime($purchase_expire_date);
						$purchase_expire_date = date('Y-m-d',$tdate);
					}
					if ($purchase_expire_date == '' or $purchase_expire_date == '0') $purchase_expire_date = '0000-00-00';
					if ($purchase_expire_date == '0000-00-00') {
						$purchase_expire_date .= ' 00:00:00';
					} else {
						$purchase_expire_date .= ' 23:59:59';
					}
					$purchase_val=OSDsubstr($purchase_expire_date,0,10);

					$stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($comp['id']),mres('DATE'),mres('0'),mres($purchase_expire_date));
					$rslt=mysql_query($stmt, $link);
					$trans_id =  mysql_insert_id($link);

					$stmt=sprintf("UPDATE osdial_companies SET acct_enddate='%s' WHERE id='%s';",mres($purchase_expire_date),mres($comp['id']));
					$rslt=mysql_query($stmt, $link);

				} elseif ($comp['acct_method']=='RANGE') {
					if ($comp['acct_expire_days'] != '0') {
						$purchase_expire_date = date('Y-m-d');
						$tdate = strtotime($purchase_expire_date);
						$tdate = strtotime("+".$comp['acct_expire_days']." day", $tdate);
						$purchase_expire_date = date('Y-m-d',$tdate);
					}
					if ($purchase_expire_date == '' or $purchase_expire_date == '0') $purchase_expire_date = '0000-00-00';
					if ($purchase_expire_date == '0000-00-00') {
						$purchase_expire_date .= ' 00:00:00';
					} else {
						$purchase_expire_date .= ' 23:59:59';
					}
					$purchase_val=OSDsubstr($purchase_expire_date,0,10);

					$stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($comp['id']),mres('DATE'),mres('0'),mres($purchase_expire_date));
					$rslt=mysql_query($stmt, $link);
					$trans_id =  mysql_insert_id($link);

					$stmt=sprintf("UPDATE osdial_companies SET acct_enddate='%s' WHERE id='%s';",mres($purchase_expire_date),mres($comp['id']));
					$rslt=mysql_query($stmt, $link);

				} else {
					if ($comp['acct_expire_days'] != '0') {
						$purchase_expire_date = date('Y-m-d');
						$tdate = strtotime($purchase_expire_date);
						$tdate = strtotime("+".$comp['acct_expire_days']." day", $tdate);
						$purchase_expire_date = date('Y-m-d',$tdate);
					}
					if ($purchase_expire_date == '' or $purchase_expire_date == '0') $purchase_expire_date = '0000-00-00';
					if ($purchase_expire_date == '0000-00-00') {
						$purchase_expire_date .= ' 00:00:00';
					} else {
						$purchase_expire_date .= ' 23:59:59';
					}

					$purchase_val=($pck['other_newcomp_initial_units'] * 1);
					$trans_sec=$purchase_val * 60;
	
					$stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($comp['id']),mres('CREDIT'),mres($trans_sec),mres($purchase_expire_date));
					$rslt=mysql_query($stmt, $link);
					$trans_id =  mysql_insert_id($link);
	
					$stmt=sprintf("INSERT INTO osdial_acct_trans_daily SET company_id='%s',trans_type='%s',trans_sec='%s',created=NOW();",mres($comp['id']),mres('CREDIT'),mres($trans_sec));
					$rslt=mysql_query($stmt, $link);
				}
			} else {
				$error = 'Email address already assigned to company!';
			}
		}
		if (empty($error) and $comp['email']==$email) {
			if ($ppvar['payment_status'] == 'Completed') {

				# Add transactions for minute based accounting
				if ($pck['ptype']=='MINUTES') {
					if ($comp['acct_expire_days'] != '0') {
						$purchase_expire_date = date('Y-m-d');
						$tdate = strtotime($purchase_expire_date);
						$tdate = strtotime("+".$comp['acct_expire_days']." day", $tdate);
						$purchase_expire_date = date('Y-m-d',$tdate);
					}
					if ($purchase_expire_date == '' or $purchase_expire_date == '0') $purchase_expire_date = '0000-00-00';
					if ($purchase_expire_date == '0000-00-00') {
						$purchase_expire_date .= ' 00:00:00';
					} else {
						$purchase_expire_date .= ' 23:59:59';
					}

					$purchase_val=(($pck['units'] * 1) * $purchase_quantity);
					$trans_sec=$purchase_val * 60;
	
					$stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($comp['id']),mres('CREDIT'),mres($trans_sec),mres($purchase_expire_date));
					$rslt=mysql_query($stmt, $link);
					$trans_id =  mysql_insert_id($link);
	
					$stmt=sprintf("INSERT INTO osdial_acct_trans_daily SET company_id='%s',trans_type='%s',trans_sec='%s',created=NOW();",mres($comp['id']),mres('CREDIT'),mres($trans_sec));
					$rslt=mysql_query($stmt, $link);
	
				# Add transactions for day based accounting
				} elseif ($pck['ptype']=='DAYS') {
					if ($comp['acct_expire_days'] == '0') $comp['acct_expire_days']='30';
					$Denddate = strtotime($comp['acct_enddate']);
					if ($comp['acct_enddate']=='0000-00-00 00:00:00') $Denddate=strtotime(date('Y-m-d'));
					if ($Denddate < strtotime(date('Y-m-d'))) $Denddate = strtotime(date('Y-m-d'));
					$Denddate_date = date('Y-m-d',$Denddate);
					$tdate = strtotime($Denddate_date);
					$tdate = strtotime("+".$pck['units']." day", $tdate);
					$purchase_expire_date = date('Y-m-d',$tdate);
					if ($purchase_expire_date == '' or $purchase_expire_date == '0') $purchase_expire_date = '0000-00-00';
					if ($purchase_expire_date == '0000-00-00') {
						$purchase_expire_date .= ' 00:00:00';
					} else {
						$purchase_expire_date .= ' 23:59:59';
					}
					$purchase_val=OSDsubstr($purchase_expire_date,0,10);

					$stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($comp['id']),mres('DATE'),mres('0'),mres($purchase_expire_date));
					$rslt=mysql_query($stmt, $link);
					$trans_id =  mysql_insert_id($link);

					$stmt=sprintf("UPDATE osdial_companies SET acct_enddate='%s' WHERE id='%s';",mres($purchase_expire_date),mres($comp['id']));
					$rslt=mysql_query($stmt, $link);
				}

				$stmt=sprintf("INSERT INTO osdial_acct_purchases SET company_id='%s',trans_id='%s',purchase_type='%s',purchase_val='%s',purchase_expire_date='%s',payment_method='%s',payment_type='%s',payment_amount='%s',payment_transid='%s',payment_date='%s',created=NOW();",mres($comp['id']),mres($trans_id),mres($purchase_type),mres($purchase_val),mres($purchase_expire_date),mres($payment_method),mres($payment_type),mres($payment_amount),mres($payment_transid),mres($payment_date));
				$rslt=mysql_query($stmt, $link);
			} elseif ($ppvar['payment_status'] == 'Refunded') {
				if ($pck['ptype']=='MINUTES') {
					$purchase_val=(($pck['units'] * 1) * $purchase_quantity) * -1;
					$trans_sec=$purchase_val * 60;
	
					$stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',created=NOW();",mres($comp['id']),mres('CREDIT'),mres($trans_sec));
					$rslt=mysql_query($stmt, $link);
					$trans_id =  mysql_insert_id($link);
	
					$stmt=sprintf("INSERT INTO osdial_acct_trans_daily SET company_id='%s',trans_type='%s',trans_sec='%s',created=NOW();",mres($comp['id']),mres('CREDIT'),mres($trans_sec));
					$rslt=mysql_query($stmt, $link);
				} elseif ($pck['ptype']=='MINUTES') {
					if ($comp['acct_expire_days'] == '0') $comp['acct_expire_days']='30';
					$Denddate = strtotime($comp['acct_enddate']);
					if ($comp['acct_enddate']=='0000-00-00 00:00:00') $Denddate=strtotime(date('Y-m-d'));
					if ($Denddate < strtotime(date('Y-m-d'))) $Denddate = strtotime(date('Y-m-d'));
					$Denddate_date = date('Y-m-d',$Denddate);
					$tdate = strtotime($Denddate_date);
					$tdate = strtotime("-".$pck['units']." day", $tdate);
					$purchase_expire_date = date('Y-m-d',$tdate);
					if ($purchase_expire_date == '' or $purchase_expire_date == '0') $purchase_expire_date = '0000-00-00';
					if ($purchase_expire_date == '0000-00-00') {
						$purchase_expire_date .= ' 00:00:00';
					} else {
						$purchase_expire_date .= ' 23:59:59';
					}
					$purchase_val=OSDsubstr($purchase_expire_date,0,10);

					$stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',trans_type='%s',trans_sec='%s',expire_date='%s',created=NOW();",mres($comp['id']),mres('DATE'),mres('0'),mres($purchase_expire_date));
					$rslt=mysql_query($stmt, $link);
					$trans_id =  mysql_insert_id($link);

					$stmt=sprintf("UPDATE osdial_companies SET acct_enddate='%s' WHERE id='%s';",mres($purchase_expire_date),mres($comp['id']));
					$rslt=mysql_query($stmt, $link);
				}
				$stmt=sprintf("INSERT INTO osdial_acct_purchases SET company_id='%s',trans_id='%s',purchase_type='%s',purchase_val='%s',purchase_expire_date='%s',payment_method='%s',payment_type='%s',payment_amount='%s',payment_transid='%s',payment_date='%s',created=NOW();",mres($comp['id']),mres($trans_id),mres($purchase_type),mres($purchase_val),mres($purchase_expire_date),mres($payment_method),mres($payment_type),mres($payment_amount),mres($payment_transid),mres($payment_date));
				$rslt=mysql_query($stmt, $link);
			}
		} else {
			$error='Could not find company!';
		}
	}

}

#$output.="\n";
#$fp = fopen("/opt/osdial/html/admin/acct.txt", "a");
#fwrite ($fp, $output . "\n");
#fclose($fp);


echo "OK";

?>
