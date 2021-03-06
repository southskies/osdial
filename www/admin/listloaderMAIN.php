<?php
# listloaderMAIN.php
# 
#
# Copyright (C) 2008  Matt Florell,Joe Johnson <vicidial@gmail.com>  LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>            LICENSE: AGPLv3
# Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>          LICENSE: AGPLv3
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
# this is the main frame page for the lead loading section. This is where you 
# would upload a file and have it inserted into osdial_list
#
# changes:
# 60620-1149 - Added variable filtering to eliminate SQL injection attack threat
# 60822-1105 - fixed for nonwritable directories
#
session_start();

require("dbconnect.php");

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

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_datetime = $STARTtime;

$stmt="SELECT count(*) from osdial_users where user='".mysql_real_escape_string($PHP_AUTH_USER)."' and pass='".mysql_real_escape_string($PHP_AUTH_PW)."' and user_level > 7;";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0) {$fp = fopen ("./project_auth_entries.txt", "a");}
$date = date("r");
$ip = $_SERVER["REMOTE_ADDR"];
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
$browser = $_SERVER["HTTP_USER_AGENT"];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    if ($config['settings']['use_old_admin_auth']) {
        Header("WWW-Authenticate: Basic realm=\"OSDIAL-LEAD-LOADER\"");
        Header("HTTP/1.0 401 Unauthorized");
    }
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
  else
	{
	header ("Content-type: text/html; charset=utf-8");
	if($auth>0)
		{
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT load_leads from osdial_users where user='".mysql_real_escape_string($PHP_AUTH_USER)."' and pass='".mysql_real_escape_string($PHP_AUTH_PW)."'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGload_leads				=$row[0];

		if ($LOGload_leads < 1)
			{
			echo "You do not have permissions to load leads\n";
			exit;
			}
		if ($WeBRooTWritablE > 0) 
			{
			fwrite ($fp, "LIST_LOAD|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0) 
			{
			fwrite ($fp, "LIST_LOAD|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}

?><HTML>
<HEAD>
<TITLE>OSDIAL: Lead Loader Module</TITLE>
</HEAD>
<FRAMESET ROWS="300,*" border=0>
<FRAME SRC="listloader.php" NAME="main">
<FRAME SRC="count.htm" NAME="lead_count">
</HTML>
