<?php
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

# The version/build variables get set to the SVN revision automatically in release package.
# Do not change.
$version = 'SVN_Version';
$build = 'SVN_Build';

header('Cache-Control: public, no-cache, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() - 60)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

require_once("dbconnect.php");
require_once("functions.php");

$user = get_variable("user");
$pass = get_variable("pass");
$server_ip = get_variable("server_ip");
$session_name = get_variable("session_name");
$format = get_variable("format");
$ACTION = get_variable("ACTION");
$client = get_variable("client");
$conf_exten = get_variable("conf_exten");
$exten = get_variable("exten");
$auto_dial_level = get_variable("auto_dial_level");
$campagentstdisp = get_variable("campagentstdisp");
$user_contact_url = get_variable("user_contact_url");

#if ($non_latin < 1) {
#    $user=OSDpreg_replace("/[^0-9a-zA-Z]/","",$user);
#    $pass=OSDpreg_replace("/[^0-9a-zA-Z]/","",$pass);
#}

# default optional vars if not set
if (empty($format)) $format="text";
if (empty($ACTION)) $ACTION="refresh";
if (empty($client)) $client="agc";

$Alogin='N';
$RingCalls='N';
$DiaLCalls='N';

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$FILE_TIME = date("Ymd_His");
if (!isset($query_date)) $query_date = $NOW_DATE;
$random = (rand(1000000, 9999999) + 10000000);


$stmt=sprintf("SELECT count(*) FROM osdial_users WHERE user='%s' AND pass='%s' AND user_level>0;",mres($user),mres($pass));
if ($format=='debug') echo "<!-- |$stmt| -->\n";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];



if( (OSDstrlen($user)<2) or (OSDstrlen($pass)<2) or ($auth==0)) {
    echo "Invalid Username/Password: |$user|$pass|\n";
    exit;
} else {
    if( (OSDstrlen($server_ip)<6) or ($server_ip=='') or ( (OSDstrlen($session_name)<12) or ($session_name=='') ) ) {
        echo "Invalid server_ip: |$server_ip|  or  Invalid session_name: |$session_name|\n";
        exit;
    } else {
        $stmt=sprintf("SELECT count(*) FROM web_client_sessions WHERE session_name='%s' AND server_ip='%s';",mres($session_name),mres($server_ip));
        if ($format=='debug') echo "<!--|$stmt|-->\n";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $SNauth=$row[0];
        if($SNauth==0) {
            echo "Invalid session_name: |$session_name|$server_ip|\n";
            exit;
        } else {
            # do nothing for now
        }
    }
}



if ($format=='debug') {
    echo "<html>\n";
    echo "<head>\n";
    echo "<!-- VERSION: $version     BUILD: $build    MEETME: $conf_exten   server_ip: $server_ip-->\n";
    echo "<title>Conf Extension Check";
    echo "</title>\n";
    echo "</head>\n";
    echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}



if ($ACTION == 'refresh') {
    $MT[0]='';
    $row='';
    $rowx='';
    $channel_live=1;
    if (OSDstrlen($conf_exten)<1) {
        $channel_live=0;
        echo "Conf Exten $conf_exten is not valid\n";
        exit;
    } else {
        if ($client == 'vdc') {
            $RingCallsin=0;
            $RingCallsout=0;
            $RingCallsinMC=0;
            $ParkCalls=0;
            $Acount=0;
            $Astatus='';
            $AexternalDEAD=0;
            $DiaLCalls='N';

            ### see if the agent has a record in the osdial_live_agents table
            $stmt=sprintf("SELECT count(*) FROM osdial_live_agents WHERE user='%s' AND server_ip='%s';",mres($user),mres($server_ip));
            if ($format=='debug') echo "<!-- |$stmt| -->\n";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $Acount=$row[0];

            if ($Acount==0) {
                $RingCalls='N';
                $DiaLCalls='N';
                $Alogin='DEAD_OLA';

                ### Check if conference records have been removed as well...No OLA and no conference means emergency logout.
                $stmt=sprintf("SELECT count(*) FROM osdial_conferences WHERE extension='%s' AND server_ip='%s';",mres($user_contact_url),mres($server_ip));
                if ($format=='debug') echo "<!-- |$stmt| -->\n";
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $conf_count=$row[0];

                if ($conf_count==0) $Alogin='EMERGENCY_LOGOUT';

                #### find out if external table shows agent should be disabled
                #$stmt="SELECT count(*) FROM another_table WHERE user='$user' AND status='DEAD';";
                #if ($format=='debug') {echo "<!-- |$stmt| -->\n";}
                #$rslt=mysql_query($stmt, $link);
                #$row=mysql_fetch_row($rslt);
                #$AexternalDEAD=$row[0];
                if ($AexternalDEAD > 0) $Alogin='DEAD_EXTERNAL';
            } else {
                if ($Acount > 0) {
                    $stmt=sprintf("SELECT status FROM osdial_live_agents WHERE user='%s' AND server_ip='%s';",mres($user),mres($server_ip));
                    if ($format=='debug') echo "<!-- |$stmt| -->\n";
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $Astatus=$row[0];
                }

                ### update the osdial_live_agents every second with a new random number so it is shown to be alive
                $stmt=sprintf("UPDATE osdial_live_agents SET random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($random),mres($user),mres($server_ip));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $time_diff = 0;
                $sql_diff = 0;
                $dialer_diff = 0;

                if ($auto_dial_level > 0) {
                    if ($campagentstdisp == 'YES') {
                        ### grab the status of this agent to display
                        $stmt=sprintf("SELECT status,campaign_id,closer_campaigns FROM osdial_live_agents WHERE user='%s' AND server_ip='%s';",mres($user),mres($server_ip));
                        if ($format=='debug') echo "<!-- |$stmt| -->\n";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $Alogin=$row[0];
                        $Acampaign=$row[1];
                        $AccampSQL=$row[2];
                        $AccampSQL = mres(OSDpreg_replace('/ -/','', $AccampSQL));
                        $AccampSQL = OSDpreg_replace('/ /',"','", $AccampSQL);

                        ### grab the number of calls being placed from this campaign
                        $stmt=sprintf("SELECT count(*) FROM osdial_auto_calls WHERE status IN('LIVE') AND campaign_id='%s';",mres($Acampaign));
                        if ($format=='debug') echo "<!-- |$stmt| -->\n";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $RingCallsout=$row[0];

                        ### grab the number of calls being placed into this agents ingroups
                        $stmt=sprintf("SELECT count(*) FROM osdial_auto_calls WHERE status IN('LIVE') AND campaign_id IN('%s');",$AccampSQL);
                        if ($format=='debug') echo "<!-- |$stmt| -->\n";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $RingCallsin=$row[0];

                        ### grab the number of calls being placed into this agents ingroups which have multicall turned on.
                        $stmt=sprintf("SELECT count(*) FROM osdial_auto_calls JOIN osdial_inbound_groups ON (campaign_id=group_id) WHERE status IN('LIVE') AND campaign_id IN('%s') AND allow_multicall='Y';",$AccampSQL);
                        if ($format=='debug') echo "<!-- |$stmt| -->\n";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $RingCallsinMC=$row[0];

                        $RingCalls=($RingCallsout+$RingCallsin);
                        if ($RingCalls > 0) {
                            $RingCalls = "<font class=\"queue_text_red\">Call Queue: In-$RingCallsin Out-$RingCallsout</font>";
                        } else {
                            $RingCalls = "<span style='margin-left:25px;'><font class=\"queue_text\">Call Queue: $RingCalls</font></span>";
                        }

                        ### grab the number of calls being placed from this server and campaign
                        $stmt=sprintf("SELECT count(*) FROM osdial_auto_calls WHERE status NOT IN('XFER') AND (campaign_id='%s' OR campaign_id IN('%s'));",mres($Acampaign),$AccampSQL);
                        if ($format=='debug') echo "<!-- |$stmt| -->\n";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $DiaLCalls=$row[0];

                        ### grab the extension used by the agent.
                        $stmt=sprintf("SELECT extension FROM osdial_conferences WHERE server_ip='%s' AND conf_exten='%s';",mres($server_ip),mres($conf_exten));
                        if ($format=='debug') echo "<!-- |$stmt| -->\n";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $phone_exten=$row[0];

                        ### grab the count of parked_channels.
                        $stmt=sprintf("SELECT count(*) FROM parked_channels WHERE parked_by='%s';",mres($phone_exten));
                        if ($format=='debug') echo "<!-- |$stmt| -->\n";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $ParkCalls=$row[0];

                        ### calculate if server is properly synchronized.
                        $web_epoch = date("U");
                        $stmt=sprintf("SELECT SQL_NO_CACHE UNIX_TIMESTAMP(last_update),UNIX_TIMESTAMP(sql_time) FROM server_updater WHERE server_ip='%s';",mres($server_ip));
                        if ($format=='debug') echo "<!-- |$stmt| -->\n";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $dialer_epoch = $row[0];
                        $sql_epoch = $row[1];
                        $time_diff = ($dialer_epoch - $sql_epoch);
                        $sql_diff = ($web_epoch - $sql_epoch);
                        $dialer_diff = ($web_epoch - $dialer_epoch);
                        if ($time_diff > 8 or $time_diff < -8) $Alogin='TIME_SYNC';


                    } else {
                        $Alogin='N';
                        $RingCalls='N';
                        $DiaLCalls='N';
                    }
                } else {
                    $Alogin='N';
                    $RingCalls='N';
                    $DiaLCalls='N';
                }
            }


            echo 'DateTime: ' . $NOW_TIME . '|UnixTime: ' . $StarTtime . '|Logged-in: ' . $Alogin . '|CampCalls: ' . $RingCalls . '|CallQueueIn: ' . $RingCallsin . '|CallQueueOut: ' . $RingCallsout . '|CallQueueInMC: ' . $RingCallsinMC . '|ParkCalls: ' . $ParkCalls . '|Status: ' . $Astatus . '|DiaLCalls: ' . $DiaLCalls . "|TimeSync: Diff=$time_diff Dialer=$dialer_diff SQL=$sql_diff|\n";

        }
        $total_conf=0;
        $stmt=sprintf("SELECT channel,channel_group FROM live_sip_channels WHERE server_ip='%s' AND extension='%s';",mres($server_ip),mres($conf_exten));
        # Hide monitoring channels
        #$stmt="SELECT channel FROM live_sip_channels WHERE server_ip='$server_ip' AND extension='$conf_exten' AND channel NOT LIKE 'Local/686_____@%' AND channel NOT LIKE 'Local/786_____@%';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        if ($rslt) $sip_list = mysql_num_rows($rslt);
        #echo "$sip_list|";
        $loop_count=0;
        while ($sip_list>$loop_count) {
            $loop_count++;
            $total_conf++;
            $row=mysql_fetch_row($rslt);
            $tchan=$row[0];
            if (OSDpreg_match('/^Local/',$row[0]) and OSDpreg_match('/^'.addcslashes($row[0],'/@').'\-/',$row[1])) $tchan=$row[1];
            $ChannelA[$total_conf] = $tchan;
            if ($format=='debug') echo "\n<!-- $ChannelA[$total_conf] -->";
        }
        $stmt=sprintf("SELECT channel FROM live_channels WHERE server_ip='%s' AND extension='%s';",mres($server_ip),mres($conf_exten));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        if ($rslt) $channels_list = mysql_num_rows($rslt);
        #echo "$channels_list|";
        $loop_count=0;
        while ($channels_list>$loop_count) {
            $loop_count++;
            $total_conf++;
            $row=mysql_fetch_row($rslt);
            $ChannelA[$total_conf] = $row[0];
            if ($format=='debug') echo "\n<!-- $row[0] -->";
        }
    }
    $channels_list = ($channels_list + $sip_list);
    echo "$channels_list|";

    $counter=0;
    $countecho='';
    while($total_conf > $counter) {
        $counter++;
        $countecho = "$countecho$ChannelA[$counter] ~";
        #echo "$ChannelA[$counter] ~";
    }

    echo "$countecho\n";
}



if ($ACTION == 'register') {
    $MT[0]='';
    $row='';
    $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($conf_exten)<1) || (OSDstrlen($exten)<1) ) {
        $channel_live=0;
        echo "Conf Exten $conf_exten is not valid or Exten $exten is not valid\n";
        exit;
    } else {
        $stmt=sprintf("UPDATE conferences SET extension='%s' WHERE server_ip='%s' AND conf_exten='%s';",mres($exten),mres($server_ip),mres($conf_exten));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
    }
    echo "Conference $conf_exten has been registered to $exten\n";
}



if ($format=='debug') {
    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $StarTtime);
    echo "\n<!-- script runtime: $RUNtime seconds -->";
    echo "\n</body>\n</html>\n";
}
    
exit; 

?>
