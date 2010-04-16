<?
# conf_exten_check.php
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
#
# This script is designed purely to send whether the meetme conference has live channels connected and which they are
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the osdial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $format - ('text','debug')
#  - $ACTION - ('refresh','register')
#  - $client - ('agc','vdc')
#  - $conf_exten - ('8600011',...)
#  - $exten - ('123test',...)
#  - $auto_dial_level - ('0','1','1.2',...)
#  - $campagentstdisp - ('YES',...)
# 

# changes
# 50509-1054 - First build of script
# 50511-1112 - Added ability to register a conference room
# 50610-1159 - Added NULL check on MySQL results to reduced errors
# 50706-1429 - script changed to not use HTTP login vars, user/pass instead
# 50706-1525 - Added date-time display for osdial client display
# 50816-1500 - Added random update to osdial_live_agents table for vdc users
# 51121-1353 - Altered echo statements for several small PHP speed optimizations
# 60410-1424 - Added ability to grab calls-being-placed and agent status
# 60421-1405 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1201 - Added variable filters to close security holes for login form
# 61128-2255 - Added update for manual dial osdial_live_agents
# 70319-1542 - Added agent disabled display function
# 71122-0205 - Added osdial_live_agent status output
# 80424-0442 - Added non_latin lookup from system_settings
# 80519-1425 - Added calls-in-queue tally
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
if (isset($_GET["ACTION"]))					{$ACTION=$_GET["ACTION"];}
	elseif (isset($_POST["ACTION"]))		{$ACTION=$_POST["ACTION"];}
if (isset($_GET["client"]))					{$client=$_GET["client"];}
	elseif (isset($_POST["client"]))		{$client=$_POST["client"];}
if (isset($_GET["conf_exten"]))				{$conf_exten=$_GET["conf_exten"];}
	elseif (isset($_POST["conf_exten"]))	{$conf_exten=$_POST["conf_exten"];}
if (isset($_GET["exten"]))					{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))			{$exten=$_POST["exten"];}
if (isset($_GET["auto_dial_level"]))			{$auto_dial_level=$_GET["auto_dial_level"];}
	elseif (isset($_POST["auto_dial_level"]))	{$auto_dial_level=$_POST["auto_dial_level"];}
if (isset($_GET["campagentstdisp"]))			{$campagentstdisp=$_GET["campagentstdisp"];}
	elseif (isset($_POST["campagentstdisp"]))	{$campagentstdisp=$_POST["campagentstdisp"];}

header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0


#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$i++;
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
{
$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);
}

# default optional vars if not set
if (!isset($format))   {$format="text";}
if (!isset($ACTION))   {$ACTION="refresh";}
if (!isset($client))   {$client="agc";}

$Alogin='N';
$RingCalls='N';
$DiaLCalls='N';

$version = '2.0.4-12';
$build = '71122-0205';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_TIME = date("Ymd_His");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
$random = (rand(1000000, 9999999) + 10000000);


$stmt="SELECT count(*) from osdial_users where user='$user' and pass='$pass' and user_level > 0;";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

  if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
	{
    echo "Invalid Username/Password: |$user|$pass|\n";
    exit;
	}
  else
	{

	if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
		{
		echo "Invalid server_ip: |$server_ip|  or  Invalid session_name: |$session_name|\n";
		exit;
		}
	else
		{
		$stmt="SELECT count(*) from web_client_sessions where session_name='$session_name' and server_ip='$server_ip';";
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
echo "<!-- VERSION: $version     BUILD: $build    MEETME: $conf_exten   server_ip: $server_ip-->\n";
echo "<title>Conf Extension Check";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}

	if ($ACTION == 'refresh')
	{
		$MT[0]='';
		$row='';   $rowx='';
		$channel_live=1;
		if (strlen($conf_exten)<1)
		{
		$channel_live=0;
		echo "Conf Exten $conf_exten is not valid\n";
		exit;
		}
		else
		{

		if ($client == 'vdc')
			{
			$Acount=0;
			$AexternalDEAD=0;

			### see if the agent has a record in the osdial_live_agents table
			$stmt="SELECT count(*) from osdial_live_agents where user='$user' and server_ip='$server_ip';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$Acount=$row[0];

			if ($Acount > 0)
				{
				$stmt="SELECT status from osdial_live_agents where user='$user' and server_ip='$server_ip';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$Astatus=$row[0];
				}
		#	### find out if external table shows agent should be disabled
		#	$stmt="SELECT count(*) from another_table where user='$user' and status='DEAD';";
		#	if ($DB) {echo "|$stmt|\n";}
		#	$rslt=mysql_query($stmt, $link);
		#	$row=mysql_fetch_row($rslt);
		#	$AexternalDEAD=$row[0];

			if ($auto_dial_level > 0)
				{
				### update the osdial_live_agents every second with a new random number so it is shown to be alive
				$stmt="UPDATE osdial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);

				if ($campagentstdisp == 'YES')
					{
					### grab the status of this agent to display
					$stmt="SELECT status,campaign_id,closer_campaigns from osdial_live_agents where user='$user' and server_ip='$server_ip';";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
					$row=mysql_fetch_row($rslt);
					$Alogin=$row[0];
					$Acampaign=$row[1];
					$AccampSQL=$row[2];
					$AccampSQL = ereg_replace(' -','', $AccampSQL);
					$AccampSQL = ereg_replace(' ',"','", $AccampSQL);

					### grab the number of calls being placed from this server and campaign
					$stmt="SELECT count(*) from osdial_auto_calls where status IN('LIVE') and ( (campaign_id='$Acampaign') or (campaign_id IN('$AccampSQL')) );";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
					$row=mysql_fetch_row($rslt);
					$RingCalls=$row[0];
					if ($RingCalls > 0) {$RingCalls = "<font class=\"queue_text_red\">Calls in Queue: $RingCalls</font>";}
					else {$RingCalls = "<font class=\"queue_text\">Calls in Queue: $RingCalls</font>";}

					### grab the number of calls being placed from this server and campaign
					$stmt="SELECT count(*) from osdial_auto_calls where status NOT IN('XFER') and ( (campaign_id='$Acampaign') or (campaign_id IN('$AccampSQL')) );";
					if ($DB) {echo "|$stmt|\n";}
					$rslt=mysql_query($stmt, $link);
					$row=mysql_fetch_row($rslt);
					$DiaLCalls=$row[0];

					}
				else
					{
					$Alogin='N';
					$RingCalls='N';
					$DiaLCalls='N';
					}
				}
			else
				{
				$Alogin='N';
				$RingCalls='N';
				$DiaLCalls='N';

				### update the osdial_live_agents every second with a new random number so it is shown to be alive
				$stmt="UPDATE osdial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);

				}

            $time_diff = 0; $sql_diff = 0; $dialer_diff = 0;
            if (preg_match('/0$/',$StarTtime)) {
                $web_epoch = date("U");
                $stmt="select UNIX_TIMESTAMP(last_update),UNIX_TIMESTAMP(sql_time) from server_updater where server_ip='$server_ip';";
                if ($DB) {echo "|$stmt|\n";}
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $dialer_epoch = $row[0];
                $sql_epoch = $row[1];
                $time_diff = ($dialer_epoch - $sql_epoch);
                $sql_diff = ($web_epoch - $sql_epoch);
                $dialer_diff = ($web_epoch - $dialer_epoch);

                if ($time_diff > 8 or $time_diff < -8) {
                    $Alogin='TIME_SYNC';
                }
            }

			if ($Acount < 1) {$Alogin='DEAD_VLA';}
			if ($AexternalDEAD > 0) {$Alogin='DEAD_EXTERNAL';}

			echo 'DateTime: ' . $NOW_TIME . '|UnixTime: ' . $StarTtime . '|Logged-in: ' . $Alogin . '|CampCalls: ' . $RingCalls . '|Status: ' . $Astatus . '|DiaLCalls: ' . $DiaLCalls . "|TimeSync: Diff=$time_diff Dialer=$dialer_diff SQL=$sql_diff|\n";

			}
		$total_conf=0;
		$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and extension = '$conf_exten';";
        # Hide monitoring channels
		#$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and extension = '$conf_exten' AND channel NOT LIKE 'Local/686_____@%' AND channel NOT LIKE 'Local/786_____@%';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		if ($rslt) {$sip_list = mysql_num_rows($rslt);}
	#	echo "$sip_list|";
		$loop_count=0;
			while ($sip_list>$loop_count)
			{
			$loop_count++; $total_conf++;
			$row=mysql_fetch_row($rslt);
			$ChannelA[$total_conf] = "$row[0]";
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			}
		$stmt="SELECT channel FROM live_channels where server_ip = '$server_ip' and extension = '$conf_exten';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		if ($rslt) {$channels_list = mysql_num_rows($rslt);}
	#	echo "$channels_list|";
		$loop_count=0;
		while ($channels_list>$loop_count)
			{
			$loop_count++; $total_conf++;
			$row=mysql_fetch_row($rslt);
			$ChannelA[$total_conf] = "$row[0]";
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			}
		}
		$channels_list = ($channels_list + $sip_list);
		echo "$channels_list|";

		$counter=0;
		$countecho='';
		while($total_conf > $counter)
		{
			$counter++;
			$countecho = "$countecho$ChannelA[$counter] ~";
		#	echo "$ChannelA[$counter] ~";
		}

	echo "$countecho\n";
	}

	if ($ACTION == 'register')
	{
		$MT[0]='';
		$row='';   $rowx='';
		$channel_live=1;
		if ( (strlen($conf_exten)<1) || (strlen($exten)<1) )
		{
		$channel_live=0;
		echo "Conf Exten $conf_exten is not valid or Exten $exten is not valid\n";
		exit;
		}
		else
		{
		$stmt="UPDATE conferences set extension='$exten' where server_ip = '$server_ip' and conf_exten = '$conf_exten';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		}
		echo "Conference $conf_exten has been registered to $exten\n";
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
