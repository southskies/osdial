<?php
### vtiger_search.php
### 
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
# Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
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
###
# This page does a search against a standard vtiger CRM system. If the record 
# is not present, it will create a new one and send the agent's screen to that new page.
#
# This code is tested against vtiger 4.2
#
# CHANGES
# 60719-1615 - First version
# 60801-2304 - added mysql debug and auto-forward
# 60802-1111 - added insertion of not-found record into vtiger system
#

#Asterisk/OSDIAL Server (ASTERISK)
#Internal : 10.10.10.15
#External : 55.55.55.55

#vtiger Server(TEST1)
#Internal : 10.10.10.16
#External : 55.55.55.56
session_start();


### alter to connect to your vtiger database
$link=mysql_connect("10.10.10.16", "cron",'1234');
if (!$link) {die('Could not connect: ' . mysql_error());}
echo 'Connected successfully';
mysql_select_db("vtigercrm4_2");


$PHP_AUTH_USER='';
$PHP_AUTH_PW='';
if ($config['settings']['use_old_admin_auth']) {
    if (isset($_SERVER['PHP_AUTH_USER'])) $PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
    if (isset($_SERVER['PHP_AUTH_PW'])) $PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
} else {
    if (isset($_SESSION[KEY]['valid'])) {
        $_SESSION[KEY]['last_update'] = time();
        if (isset($_SESSION[KEY]['PHP_AUTH_USER'])) $PHP_AUTH_USER=$_SESSION[KEY]['PHP_AUTH_USER'];
        if (isset($_SESSION[KEY]['PHP_AUTH_PW'])) $PHP_AUTH_PW=$_SESSION[KEY]['PHP_AUTH_PW'];
    }
    if (empty($PHP_AUTH_USER)) $PHP_AUTH_USER=get_variable('PHP_AUTH_USER');
    if (empty($PHP_AUTH_PW)) $PHP_AUTH_PW=get_variable('PHP_AUTH_PW');
}
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["address1"]))				{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))		{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))				{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))		{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))				{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))		{$address3=$_POST["address3"];}
if (isset($_GET["alt_phone"]))				{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))		{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["call_began"]))				{$call_began=$_GET["call_began"];}
	elseif (isset($_POST["call_began"]))		{$call_began=$_POST["call_began"];}
if (isset($_GET["campaign_id"]))				{$campaign_id=$_GET["campaign_id"];}
	elseif (isset($_POST["campaign_id"]))		{$campaign_id=$_POST["campaign_id"];}
if (isset($_GET["channel"]))				{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))		{$channel=$_POST["channel"];}
if (isset($_GET["channel_group"]))				{$channel_group=$_GET["channel_group"];}
	elseif (isset($_POST["channel_group"]))		{$channel_group=$_POST["channel_group"];}
if (isset($_GET["city"]))				{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))		{$city=$_POST["city"];}
if (isset($_GET["comments"]))				{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))		{$comments=$_POST["comments"];}
if (isset($_GET["country_code"]))				{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))		{$country_code=$_POST["country_code"];}
if (isset($_GET["customer_zap_channel"]))				{$customer_zap_channel=$_GET["customer_zap_channel"];}
	elseif (isset($_POST["customer_zap_channel"]))		{$customer_zap_channel=$_POST["customer_zap_channel"];}
if (isset($_GET["DB"]))				{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["dispo"]))				{$dispo=$_GET["dispo"];}
	elseif (isset($_POST["dispo"]))		{$dispo=$_POST["dispo"];}
if (isset($_GET["email"]))				{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))		{$email=$_POST["email"];}
if (isset($_GET["end_call"]))				{$end_call=$_GET["end_call"];}
	elseif (isset($_POST["end_call"]))		{$end_call=$_POST["end_call"];}
if (isset($_GET["extension"]))				{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$extension=$_POST["extension"];}
if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))		{$first_name=$_POST["first_name"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["last_name"]))				{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))		{$last_name=$_POST["last_name"];}
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["list_id"]))				{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))		{$list_id=$_POST["list_id"];}
if (isset($_GET["parked_time"]))				{$parked_time=$_GET["parked_time"];}
	elseif (isset($_POST["parked_time"]))		{$parked_time=$_POST["parked_time"];}
if (isset($_GET["pass"]))				{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))		{$pass=$_POST["pass"];}
if (isset($_GET["phone_code"]))				{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))		{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))				{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))		{$phone_number=$_POST["phone_number"];}
if (isset($_GET["phone"]))				{$phone=$_GET["phone"];}
	elseif (isset($_POST["phone"]))		{$phone=$_POST["phone"];}
if (isset($_GET["postal_code"]))				{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))		{$postal_code=$_POST["postal_code"];}
if (isset($_GET["province"]))				{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))		{$province=$_POST["province"];}
if (isset($_GET["custom1"]))				{$custom1=$_GET["custom1"];}
	elseif (isset($_POST["custom1"]))		{$custom1=$_POST["custom1"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_id"]))				{$session_id=$_GET["session_id"];}
	elseif (isset($_POST["session_id"]))		{$session_id=$_POST["session_id"];}
if (isset($_GET["state"]))				{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))		{$state=$_POST["state"];}
if (isset($_GET["status"]))				{$status=$_GET["status"];}
	elseif (isset($_POST["status"]))		{$status=$_POST["status"];}
if (isset($_GET["tsr"]))				{$tsr=$_GET["tsr"];}
	elseif (isset($_POST["tsr"]))		{$tsr=$_POST["tsr"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["vendor_id"]))				{$vendor_id=$_GET["vendor_id"];}
	elseif (isset($_POST["vendor_id"]))		{$vendor_id=$_POST["vendor_id"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

#$DB = '1';	# DEBUG override
$US = '_';
$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$REC_TIME = date("Ymd-His");
$FILE_datetime = $STARTtime;
$parked_time = $STARTtime;

# $ext_context = 'default'; defined in dbconnect file

#	$stmt="SELECT count(*) from osdial_users where user='$user' and pass='$pass' and user_level > 0;";
#		if ($DB) {echo "$stmt\n";}
#	$rslt=mysql_query($stmt, $link);
#	$row=mysql_fetch_row($rslt);
#	$auth=$row[0];

#$fp = fopen ("./project_auth_entries.txt", "a");
#$date = date("r");
#$ip = getenv("REMOTE_ADDR");
#$browser = getenv("HTTP_USER_AGENT");

#  if( (strlen($user)<2) or (strlen($pass)<2) or (!$auth))
#	{
#    Header("WWW-Authenticate: Basic realm=\"OSDIAL-CLOSER\"");
#    Header("HTTP/1.0 401 Unauthorized");
#    echo "Invalid Username/Password: |$user|$pass|\n";
#    exit;
#	}
#  else
#	{
#
#	if($auth>0)
#		{
#		$office_no=strtoupper($user);
#		$password=strtoupper($pass);
#			$stmt="SELECT full_name from osdial_users where user='$user' and pass='$pass'";
#			if ($DB) {echo "$stmt\n";}
#			$rslt=mysql_query($stmt, $link);
#			$row=mysql_fetch_row($rslt);
#			$LOGfullname=$row[0];
#			$fullname = $row[0];
#		fwrite ($fp, "VD_CLOSER|GOOD|$date|$user|$pass|$ip|$browser|$LOGfullname|\n");
#		fclose($fp);
#		
#		if ( (strlen($customer_zap_channel)>2) and (preg_match('/zap/i',$customer_zap_channel)) )
#			{
#			echo "\n<!-- zap channel: $customer_zap_channel -->\n";
#			echo "\n<!-- session_id: $session_id -->\n";
#
#			}
#		else
#			{
#			echo "Bad channel: $customer_zap_channel\n";
#			echo "Make sure the Zap channel is live and try again\n";
#			exit;
#			}
#
#		}
#	else
#		{
#		fwrite ($fp, "VD_CLOSER|FAIL|$date|$user|$pass|$ip|$browser|\n");
#		fclose($fp);
#		}
#	}

echo "<html>\n";
echo "<head>\n";
echo "<title>OSDIAL Vtiger Lookup</title>\n";
echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">\n";


$stmt="SELECT count(*) from account where phone='$phone' or otherphone='$phone' or fax='$phone';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
if (!$rslt) {
   die('Could not execute: ' . mysql_error());
}
$row=mysql_fetch_row($rslt);
$found_count = $row[0];

#echo "<BR>\n|$phone|$found_count|\n";

if ($found_count < 1)
{
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onLoad=\"document.forms[0].search_phone.focus(); setTimeout('document.forms[0].phone.focus()', 1000); self.focus()\">\n";
	echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";
	echo "$phone not found, creating account...\n";

	if ($DB) {echo "<PRE>";}
	$stmt="SELECT id from users where user_name='$user';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$user_id = $row[0];
	if (!$rslt) {die('Could not execute: ' . mysql_error());}

	$stmt = "INSERT INTO crmentity (smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted) VALUES ('$user_id', '$user_id', 0, 'Accounts', NULL, '$NOW_TIME', '$NOW_TIME', '$NOW_TIME', NULL, 0, 1, 1);";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$recordID = mysql_insert_id($link);
	if ($DB) {echo "|$recordID|\n";}
	if (!$rslt) {die('Could not execute: ' . mysql_error());}

#	$stmt = "INSERT INTO accountscf (accountid, cf_393, cf_397, cf_403, cf_405, cf_407, cf_409, cf_411) VALUES ('$recordID', NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
#	if ($DB) {echo "|$stmt|\n";}
#	$rslt=mysql_query($stmt, $link);
#	if (!$rslt) {die('Could not execute: ' . mysql_error());}

	$stmt = "INSERT INTO account (accountid,accountname,contactname,phone,otherphone,email1) values('$recordID','$last_name, $first_name','$first_name $last_name','$phone','$alt_phone','$email');";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	if (!$rslt) {die('Could not execute: ' . mysql_error());}

	$stmt = "INSERT INTO accountbillads (accountaddressid,city,code,country,state,street) values('$recordID','$city','$postal_code','US','$state','$address1');";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	if (!$rslt) {die('Could not execute: ' . mysql_error());}

	$stmt = "INSERT INTO accountshipads (accountaddressid,city,code,country,state,street) values('$recordID','$city','$postal_code','US','$state','$address1');";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	if (!$rslt) {die('Could not execute: ' . mysql_error());}

	if ($DB) {echo "DONE\n";}

	$account_URL = "http://55.55.55.56/index.php?module=Accounts&action=DetailView&record=$recordID";
	echo "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$account_URL\">\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onLoad=\"document.forms[0].search_phone.focus(); setTimeout('document.forms[0].phone.focus()', 1000); self.focus()\">\n";
	echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";

	echo "<PRE>";
	echo "account created!\n";
	echo "accountid:   <a href=\"$account_URL\">$recordID</a>\n";
	echo "phone:       $phone\n";
	echo "</PRE><BR>";

}
else
{
	$stmt="SELECT accountid,accountname,contactname from account where phone='$phone' or otherphone='$phone' or fax='$phone';";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$row=mysql_fetch_row($rslt);
	$accountid = $row[0];
	$accountname = $row[1];
	$contactname = $row[2];

	$account_URL = "http://55.55.55.56/index.php?module=Accounts&action=DetailView&record=$accountid";
	echo "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$account_URL\">\n";
	echo "</head>\n";
	echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onLoad=\"document.forms[0].search_phone.focus(); setTimeout('document.forms[0].phone.focus()', 1000); self.focus()\">\n";
	echo "<CENTER><FONT FACE=\"Courier\" COLOR=BLACK SIZE=3>\n";

	echo "<PRE>";
	echo "account found!\n";
	echo "accountid:   <a href=\"$account_URL\">$accountid</a>\n";
	echo "phone:       $phone\n";
	echo "accountname: $accountname\n";
	echo "contactname: $contactname\n";
	echo "</PRE><BR>";

}



$ENDtime = date("U");

$RUNtime = ($ENDtime - $STARTtime);

echo "\n\n\n<br><br><br>\n\n";


echo "<font size=0>\n\n\n<br><br><br>\nscript runtime: $RUNtime seconds</font>";


?>


</body>
</html>

<?php
	
exit; 



?>





