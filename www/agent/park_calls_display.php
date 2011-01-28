<?
### park_calls_display.php
### 
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
### This script is designed purely to send the details on the parked calls on the server
### This script depends on the server_ip being sent and also needs to have a valid user/pass from the osdial_users table
### 
### required variables:
###  - $server_ip
###  - $session_name
###  - $user
###  - $pass
### optional variables:
###  - $format - ('text','debug')
###  - $exten - ('cc101','testphone','49-1','1234','913125551212',...)
###  - $protocol - ('SIP','Zap','IAX2',...)
### 

# changes
# 50524-1515 - First build of script
# 50711-1208 - removed HTTP authentication in favor of user/pass vars
# 60421-1111 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1205 - Added variable filters to close security holes for login form
# 

require("dbconnect.php");

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["pass"]))					{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))			{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))				{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))			{$session_name=$_GET["session_name"];}
	elseif (isset($_POST["session_name"]))	{$session_name=$_POST["session_name"];}
if (isset($_GET["format"]))					{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))		{$format=$_POST["format"];}
if (isset($_GET["exten"]))					{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))			{$exten=$_POST["exten"];}
if (isset($_GET["protocol"]))				{$protocol=$_GET["protocol"];}
	elseif (isset($_POST["protocol"]))		{$protocol=$_POST["protocol"];}

$user=preg_replace("/[^0-9a-zA-Z]/","",$user);
$pass=preg_replace("/[^0-9a-zA-Z]/","",$pass);

# default optional vars if not set
if (!isset($format))		{$format="text";}
if (!isset($park_limit))	{$park_limit="1000";}

$version = '0.0.4';
$build = '60619-1205';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) {$query_date = $NOW_DATE;}

	$stmt="SELECT count(*) FROM osdial_users WHERE user='$user' AND pass='$pass' AND user_level>0;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0) )
	{
    echo "Invalid Username/Password: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) ) 		{
		echo "Invalid server_ip: |$server_ip|  or  Invalid session_name: |$session_name|\n";
		exit;
		}
	else
		{
		$stmt="SELECT count(*) FROM web_client_sessions WHERE session_name='$session_name' AND server_ip='$server_ip';";
		if ($DB) {echo "|$stmt|\n";}
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$SNauth=$row[0];
		  if($SNauth==0)
			{
			echo "Invalid session_name: |$session_name|$server_ip|\n";
			exit;
			}
		  else
			{
			# do nothing for now
			}
		}
	}

if ($format=='debug')
{
echo "<html>\n";
echo "<head>\n";
echo "<!-- VERSION: $version     BUILD: $build    EXTEN: $exten   server_ip: $server_ip-->\n";
echo "<title>Parked Calls Display";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}


	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($exten)<1) or (strlen($protocol)<3) )
	{
	$channel_live=0;
	echo "Exten $exten is not valid or protocol $protocol is not valid\n";
	exit;
	}
	else
	{
	##### print parked calls from the parked_channels table
	$stmt="SELECT SQL_NO_CACHE * FROM parked_channels WHERE server_ip='$server_ip' ORDER BY parked_time LIMIT $park_limit;";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	$park_calls_count = mysql_num_rows($rslt);
	echo "$park_calls_count\n";
	$loop_count=0;
		while ($park_calls_count>$loop_count)
		{
		$loop_count++;
		$row=mysql_fetch_row($rslt);
		echo "$row[0] ~$row[2] ~$row[3] ~$row[4] ~$row[5]|";
		}
	echo "\n";
	}



if ($format=='debug') 
	{
	$ENDtime = date("U");
	$RUNtime = ($ENDtime - $StarTtime);
	echo "\n<!-- script runtime: $RUNtime seconds -->";
	echo "\n</body>\n</html>\n";
	}
	
exit; 

?>
