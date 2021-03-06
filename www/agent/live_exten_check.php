<?php
### live_exten_check.php
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
### This script is designed purely to send whether the client channel is live and to what channel it is connected
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
# 50404-1249 - First build of script
# 50406-1402 - added connected trunk lookup
# 50428-1452 - added live_inbound check for exten on 2nd line of output
# 50503-1233 - added session_name checking for extra security
# 50524-1429 - added parked calls count
# 50610-1204 - Added NULL check on MySQL results to reduced errors
# 50711-1204 - removed HTTP authentication in favor of user/pass vars
# 60103-1541 - added favorite extens status display
# 60421-1359 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1203 - Added variable filters to close security holes for login form
# 60825-1029 - Fixed translation variable issue ChannelA
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

$DB=0;
$user = get_variable("user");
$pass = get_variable("pass");
$server_ip = get_variable("server_ip");
$session_name = get_variable("session_name");
$format = get_variable("format");
$exten = get_variable("exten");
$protocol = get_variable("protocol");
$favorites_count = get_variable("favorites_count");
$favorites_list = get_variable("favorites_list");

#$user=OSDpreg_replace("/[^0-9a-zA-Z]/","",$user);
#$pass=OSDpreg_replace("/[^0-9a-zA-Z]/","",$pass);

# default optional vars if not set
if (empty($format)) $format="text";
if ($format=='debug') $DB=1;

$version = 'SVN_Version';
$build = 'SVN_Build';
$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) $query_date = $NOW_DATE;



$stmt=sprintf("SELECT count(*) FROM osdial_users WHERE user='%s' AND pass='%s' AND user_level>0;",mres($user),mres($pass));
if ($DB) echo "|$stmt|\n";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];



if( (OSDstrlen($user)<2) or (OSDstrlen($pass)<2) or ($auth==0)) {
    echo "Invalid Username/Password: |$user|$pass|\n";
    exit;
} else {
    if( (OSDstrlen($server_ip)<6) or (empty($server_ip)) or ( (OSDstrlen($session_name)<12) or (empty($session_name)) ) ) {
        echo "Invalid server_ip: |$server_ip|  or  Invalid session_name: |$session_name|\n";
        exit;
    } else {
        $stmt=sprintf("SELECT count(*) FROM web_client_sessions WHERE session_name='%s' AND server_ip='%s';",mres($session_name),mres($server_ip));
        if ($DB) echo "|$stmt|\n";
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
    echo "<!-- VERSION: $version     BUILD: $build    EXTEN: $exten   server_ip: $server_ip-->\n";
    echo "<title>Live Extension Check";
    echo "</title>\n";
    echo "</head>\n";
    echo "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}



echo "DateTime: $NOW_TIME|";
echo "UnixTime: $StarTtime|";

$stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM parked_channels WHERE server_ip='%s';",mres($server_ip));
if ($format=='debug') echo "\n<!-- $stmt -->";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
echo "$row[0]|";

$MT[0]='';
$row='';
$rowx='';
$channel_live=1;
if ( (OSDstrlen($exten)<1) or (OSDstrlen($protocol)<3) ) {
    $channel_live=0;
    echo "Exten $exten is not valid or protocol $protocol is not valid\n";
    exit;
} else {
    $stmt=sprintf("SELECT SQL_NO_CACHE channel,extension FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s/%s%%';",mres($server_ip),mres($protocol),mres($exten));
    if ($format=='debug') echo "\n<!-- $stmt -->";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $channels_list = mysql_num_rows($rslt);
    echo "$channels_list|";
    $loop_count=0;
    while ($channels_list>$loop_count) {
        $loop_count++;
        $row=mysql_fetch_row($rslt);
        $ChanneLA[$loop_count] = $row[0];
        $ChanneLB[$loop_count] = $row[1];
        if ($format=='debug') echo "\n<!-- $row[0]     $row[1] -->";
    }
}

$counter=0;
while($loop_count > $counter) {
    $counter++;
    $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_channels WHERE server_ip='%s' AND channel_data='%s';",mres($server_ip),mres($ChanneLA[$counter]));
    if ($format=='debug') echo "\n<!-- $stmt -->";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $trunk_count = mysql_num_rows($rslt);
    if ($trunk_count>0) {
        $row=mysql_fetch_row($rslt);
        echo "Conversation: $counter ~";
        echo "ChannelA: $ChanneLA[$counter] ~";
        echo "ChannelB: $ChanneLB[$counter] ~";
        echo "ChannelBtrunk: $row[0]|";
    } else {
        $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='%s' AND channel_data='%s';",mres($server_ip),mres($ChanneLA[$counter]));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        if ($rslt) {$trunk_count = mysql_num_rows($rslt);}
        if ($trunk_count>0) {
            $row=mysql_fetch_row($rslt);
            echo "Conversation: $counter ~";
            echo "ChannelA: $ChanneLA[$counter] ~";
            echo "ChannelB: $ChanneLB[$counter] ~";
            echo "ChannelBtrunk: $row[0]|";
        } else {
            $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s%%';",mres($server_ip),mres($ChanneLB[$counter]));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            if ($rslt) $trunk_count = mysql_num_rows($rslt);
            if ($trunk_count>0) {
                $row=mysql_fetch_row($rslt);
                echo "Conversation: $counter ~";
                echo "ChannelA: $ChanneLA[$counter] ~";
                echo "ChannelB: $ChanneLB[$counter] ~";
                echo "ChannelBtrunk: $row[0]|";
            } else {
                $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_channels WHERE server_ip='%s' AND channel LIKE '%s%%';",mres($server_ip),mres($ChanneLB[$counter]));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                if ($rslt) $trunk_count = mysql_num_rows($rslt);
                if ($trunk_count>0) {
                    $row=mysql_fetch_row($rslt);
                    echo "Conversation: $counter ~";
                    echo "ChannelA: $ChanneLA[$counter] ~";
                    echo "ChannelB: $ChanneLB[$counter] ~";
                    echo "ChannelBtrunk: $row[0]|";
                } else {
                    echo "Conversation: $counter ~";
                    echo "ChannelA: $ChanneLA[$counter] ~";
                    echo "ChannelB: $ChanneLB[$counter] ~";
                    echo "ChannelBtrunk: $ChanneLA[$counter]|";
                }
            }
        }
    }
}
echo "\n";



### check for live_inbound entry
$stmt=sprintf("SELECT SQL_NO_CACHE * FROM live_inbound WHERE server_ip='%s' AND phone_ext='%s' AND acknowledged='N';",mres($server_ip),mres($exten));
if ($format=='debug') echo "\n<!-- $stmt -->";
$rslt=mysql_query($stmt, $link);
if ($rslt) $channels_list = mysql_num_rows($rslt);
if ($channels_list>0) {
    $row=mysql_fetch_row($rslt);
    $LIuniqueid = $row[0];
    $LIchannel = $row[1];
    $LIcallerid = $row[3];
    $LIdatetime = $row[6];
    echo "$LIuniqueid|$LIchannel|$LIcallerid|$LIdatetime|$row[8]|$row[9]|$row[10]|$row[11]|$row[12]|$row[13]|";
    if ($format=='debug') echo "\n<!-- $row[0]|$row[1]|$row[2]|$row[3]|$row[4]|$row[5]|$row[6]|$row[7]|$row[8]|$row[9]|$row[10]|$row[11]|$row[12]|$row[13]| -->";
}
echo "\n";



### if favorites are present do a lookup to see if any are active
if ($favorites_count > 0) {
    $favorites = explode(',',$favorites_list);
    $h=0;
    $favs_print='';
    while ($favorites_count > $h) {
        $fav_extension = explode('/',$favorites[$h]);
        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s%%';",mres($server_ip),mres($favorites[$h]));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $favs_print .= "$fav_extension[1]: $row[0] ~";
        $h++;
    }
    echo "$favs_print\n";
}
if ($format=='debug') echo "\n<!-- |$favorites_count|$favorites_list| -->";



if ($format=='debug') {
    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $StarTtime);
    echo "\n<!-- script runtime: $RUNtime seconds -->";
    echo "\n</body>\n</html>\n";
}
    
exit; 

?>
