<?php
### call_log_display.php
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
### This script is designed purely to send the inbound and outbound calls for a specific phone
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
###  - $in_limit - ('10','20','50','100',...)
###  - $out_limit - ('10','20','50','100',...)
### 

# changes
# 50406-1013 - First build of script
# 50407-1452 - Added definable limits
# 50503-1236 - added session_name checking for extra security
# 50610-1158 - Added NULL check on MySQL results to reduced errors
# 50711-1202 - removed HTTP authentication in favor of user/pass vars
# 60323-1550 - added option for showing different number dialed in log
# 60421-1401 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1202 - Added variable filters to close security holes for login form
# 

$DB=0;

require_once("dbconnect.php");
require_once("functions.php");

### If you have globals turned off uncomment these lines
$user=get_variable("user");
$pass=get_variable("pass");
$server_ip=get_variable("server_ip");
$session_name=get_variable("session_name");
$format=get_variable("format");
$exten=get_variable("exten");
$protocol=get_variable("protocol");

#$user=OSDpreg_replace("/[^0-9a-zA-Z]/","",$user);
#$pass=OSDpreg_replace("/[^0-9a-zA-Z]/","",$pass);

# default optional vars if not set
if (empty($format)) $format="text";
if ($format=='debug') $DB=1;
if (!isset($in_limit)) $in_limit="100";
if (!isset($out_limit)) $out_limit="100";
$number_dialed = 'number_dialed';
#$number_dialed = 'extension';

$version = 'SVN_Version';
$build = 'SVN_Build';

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) $query_date=$NOW_DATE;

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
        $stmt=sprintf("SELECT count(*) FROM web_client_sessions WHERE session_name='$session_name' AND server_ip='$server_ip';",mres($session_name),mres($server_ip));
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
    echo "<title>Call Log Display";
    echo "</title>\n";
    echo "</head>\n";
    echo "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}


$row='';
$rowx='';
$channel_live=1;
if ( (OSDstrlen($exten)<1) or (OSDstrlen($protocol)<3) ) {
    $channel_live=0;
    echo "Exten $exten is not valid or protocol $protocol is not valid\n";
    exit;
    } else {
        ##### print outbound calls from the call_log table
        $stmt=sprintf("SELECT uniqueid,start_time,$number_dialed,length_in_sec FROM call_log WHERE server_ip='%s' AND channel LIKE '%s%%' ORDER BY start_time DESC LIMIT %s;",$number_dialed,mres($server_ip),mres($protocol.'/'.$extension),$out_limit);
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        if ($rslt) $out_calls_count = mysql_num_rows($rslt);
        echo "$out_calls_count|";
        $loop_count=0;
        while ($out_calls_count>$loop_count) {
            $loop_count++;
            $row=mysql_fetch_row($rslt);

            $call_time_M = ($row[3] / 60);
            $call_time_M = round($call_time_M, 2);
            $call_time_M_int = intval("$call_time_M");
            $call_time_SEC = ($call_time_M - $call_time_M_int);
            $call_time_SEC = ($call_time_SEC * 60);
            $call_time_SEC = round($call_time_SEC, 0);
            if ($call_time_SEC < 10) $call_time_SEC = "0$call_time_SEC";
            $call_time_MS = "$call_time_M_int:$call_time_SEC";

            if ($number_dialed == 'extension') $row[2] = OSDsubstr($row[2],-10);
            echo "$row[0] ~$row[1] ~$row[2] ~$call_time_MS|";
        }
        echo "\n";

        ##### print inbound calls from the live_inbound_log table
        $stmt=sprintf("SELECT call_log.uniqueid,live_inbound_log.start_time,live_inbound_log.extension,caller_id,length_in_sec FROM live_inbound_log,call_log WHERE phone_ext='%s' AND live_inbound_log.server_ip='%s' AND call_log.uniqueid=live_inbound_log.uniqueid ORDER BY start_time DESC LIMIT %s;",mres($exten),mres($server_ip),$in_limit);
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        if ($rslt) $in_calls_count = mysql_num_rows($rslt);
        echo "$in_calls_count|";
        $loop_count=0;
        while ($in_calls_count>$loop_count) {
            $loop_count++;
            $row=mysql_fetch_row($rslt);

            $call_time_M = ($row[4] / 60);
            $call_time_M = round($call_time_M, 2);
            $call_time_M_int = intval("$call_time_M");
            $call_time_SEC = ($call_time_M - $call_time_M_int);
            $call_time_SEC = ($call_time_SEC * 60);
            $call_time_SEC = round($call_time_SEC, 0);
            if ($call_time_SEC < 10) $call_time_SEC = "0$call_time_SEC";
            $call_time_MS = "$call_time_M_int:$call_time_SEC";
            $callerIDnum = $row[3];
            $callerIDname = $row[3];
            $callerIDnum = OSDpreg_replace("/.*<|>.*/","",$callerIDnum);
            $callerIDname = OSDpreg_replace("/\"| <\d*>/","",$callerIDname);

            echo "$row[0] ~$row[1] ~$row[2] ~$callerIDnum ~$callerIDname ~$call_time_MS|";
        }
        echo "\n";

}



if ($format=='debug') {
    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $StarTtime);
    echo "\n<!-- script runtime: $RUNtime seconds -->";
    echo "\n</body>\n</html>\n";
}

exit; 

?>
