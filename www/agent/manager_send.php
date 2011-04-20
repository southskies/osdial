<?
# manager_send.php
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
#
# This script is designed purely to insert records into the osdial_manager table to signal Actions to an asterisk server
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the osdial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $ACTION - ('Originate','Redirect','Hangup','Command','Monitor','StopMonitor','SysCIDOriginate','RedirectName','RedirectNameVmail','MonitorConf','StopMonitorConf','RedirectXtra','RedirectXtraCX','RedirectVD','HangupConfDial','VolumeControl','OriginateVDRelogin')
#  - $queryCID - ('CN012345678901234567',...)
#  - $format - ('text','debug')
#  - $channel - ('Zap/41-1','SIP/test101-1jut','IAX2/iaxy@iaxy',...)
#  - $exten - ('1234','913125551212',...)
#  - $ext_context - ('default','demo',...)
#  - $ext_priority - ('1','2',...)
#  - $filename - ('20050406-125623_44444',...)
#  - $extenName - ('phone100',...)
#  - $parkedby - ('phone100',...)
#  - $extrachannel - ('Zap/41-1','SIP/test101-1jut','IAX2/iaxy@iaxy',...)
#  - $auto_dial_level - ('0','1','1.1',...)
#  - $campaign - ('CLOSER','TESTCAMP',...)
#  - $uniqueid - ('1120232758.2406800',...)
#  - $lead_id - ('1234',...)
#  - $seconds - ('32',...)
#  - $outbound_cid - ('3125551212','0000000000',...)
#  - $agent_log_id - ('123456',...)
#  - $call_server_ip - ('10.10.10.15',...)
#  - $CalLCID - ('VD01234567890123456',...)
#  - $stage - ('UP','DOWN','2NDXfeR')
#  - $session_id - ('8600051')

# CHANGELOG:
# 50401-1002 - First build of script, Hangup function only
# 50404-1045 - Redirect basic function enabled
# 50406-1522 - Monitor basic function enabled
# 50407-1647 - Monitor and StopMonitor full functions enabled
# 50422-1120 - basic Originate function enabled
# 50428-1451 - basic SysCIDOriginate function enabled for checking voicemail
# 50502-1539 - basic RedirectName and RedirectNameVmail added
# 50503-1227 - added session_name checking for extra security
# 50523-1341 - added Conference call start/stop recording
# 50523-1421 - added OriginateName and OriginateNameVmail for local calls
# 50524-1602 - added RedirectToPark and RedirectFromPark
# 50531-1203 - added RedirecXtra for dual channel redirection
# 50630-1100 - script changed to not use HTTP login vars, user/pass instead
# 50804-1148 - Added RedirectVD for OSDIAL blind redirection with logging
# 50815-1204 - Added NEXTAVAILABLE to RedirectXtra function
# 50903-2343 - Added HangupConfDial function to hangup in-dial channels in conf
# 50913-1057 - Added outbound_cid set if present to originate call
# 51020-1556 - Added agent_log_id framework for detailed agent activity logging
# 51118-1204 - Fixed Blind transfer bug from OSDIAL when in manual dial mode
# 51129-1014 - Added ability to accept calls from other OSDIAL servers
# 51129-1253 - Fixed Hangups of other agents channels in OSDIAL AD
# 60310-2022 - Fixed NEXTAVAILABLE bug in leave-3way-call redirect function
# 60421-1413 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1158 - Added variable filters to close security holes for login form
# 60809-1544 - Added direct transfers to leave-3ways in consultative transfers
# 61004-1526 - Added parsing of volume control command and lookup or number
# 61130-1617 - Added lead_id to MonitorConf for recording_log
# 61201-1115 - Added user to MonitorConf for recording_log
# 70111-1600 - added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70226-1251 - Added Mute/UnMute to conference volume control
# 70320-1502 - Added option to allow retry of leave-3way-call and debug logging
# 70322-1636 - Added sipsak display ability
# 80331-1433 - Added second transfer try for OSDIAL transfers on manual dial calls
# 80402-0121 - Fixes for manual dial transfers on some systems
# 80424-0442 - Added non_latin lookup from system_settings
#

# The version/build variables get set to the SVN revision automatically in release package.
# Do not change.
$version = 'SVN_Version';
$build = 'SVN_Build';

header('Cache-Control: public, no-cache, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() - 60)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

require("dbconnect.php");
require("functions.php");

### These are variable assignments for PHP globals off
$user = get_variable("user");
$pass = get_variable("pass");
$server_ip = get_variable("server_ip");
$session_name = get_variable("session_name");
$ACTION = get_variable("ACTION");
$phone_ip = get_variable("account");
$queryCID = get_variable("queryCID");
$format = get_variable("format");
$channel = get_variable("channel");
$agentchannel = get_variable("agentchannel");
$exten = get_variable("exten");
$ext_context = get_variable("ext_context");
$ext_priority = get_variable("ext_priority");
$filename = get_variable("filename");
$extenName = get_variable("extenName");
$parkedby = get_variable("parkedby");
$extrachannel = get_variable("extrachannel");
$auto_dial_level = get_variable("auto_dial_level");
$campaign = get_variable("campaign");
$uniqueid = get_variable("uniqueid");
$lead_id = get_variable("lead_id");
$secondS = get_variable("secondS");
$outbound_cid = get_variable("outbound_cid");
$outbound_cid_name = get_variable("outbound_cid_name");
$agent_log_id = get_variable("agent_log_id");
$call_server_ip = get_variable("call_server_ip");
$CalLCID = get_variable("CalLCID");
$phone_code = get_variable("phone_code");
$phone_number = get_variable("phone_number");
$stage = get_variable("stage");
$extension = get_variable("extension");
$protocol = get_variable("protocol");
$phone_ip = get_variable("phone_ip");
$enable_sipsak_messages = get_variable("enable_sipsak_messages");
$allow_sipsak_messages = get_variable("allow_sipsak_messages");
$session_id = get_variable("session_id");
$server_dialstring = get_variable("server_dialstring");

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,intra_server_protocol FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) echo "$stmt\n";
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct) {
    $row=mysql_fetch_row($rslt);
    $non_latin = $row[0];
    $isp='*';
    if ($row[1]=='IAX2') $isp='#';
    $i++;
}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1) {
    $user=preg_replace("/[^0-9a-zA-Z]/","",$user);
    $pass=preg_replace("/[^0-9a-zA-Z]/","",$pass);
    $secondS = preg_replace("/[^0-9]/","",$secondS);
}

# default optional vars if not set
if ($ACTION=='') $ACTION="Originate";
if ($format=='') $format="alert";
if ($ext_priority=='') $ext_priority="1";

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$NOWnum = date("YmdHis");
if (!isset($query_date)) $query_date = $NOW_DATE;

$stmt="SELECT count(*) FROM osdial_users WHERE user='$user' AND pass='$pass' AND user_level>0;";
if ($DB) echo "|$stmt|\n";
if ($non_latin > 0) $rslt=mysql_query("SET NAMES 'UTF8'");
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0)) {
    echo "Invalid Username/Password: |$user|$pass|\n";
    exit;
} else {
    if( (strlen($server_ip)<6) or ($server_ip=='') or ( (strlen($session_name)<12) or ($session_name=='') ) ) {
        echo "Invalid server_ip: |$server_ip|  or  Invalid session_name: |$session_name|\n";
        exit;
    } else {
        $stmt="SELECT count(*) FROM web_client_sessions WHERE session_name='$session_name' AND server_ip='$server_ip';";
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
    echo "<!-- VERSION: $version     BUILD: $build    ACTION: $ACTION   server_ip: $server_ip-->\n";
    echo "<title>Manager Send: ";
    if ($ACTION=="Originate") echo "Originate";
    if ($ACTION=="Redirect") echo "Redirect";
    if ($ACTION=="RedirectName") echo "RedirectName";
    if ($ACTION=="Hangup") echo "Hangup";
    if ($ACTION=="Command") echo "Command";
    if ($ACTION==99999)    echo "HELP";
    echo "</title>\n";
    echo "</head>\n";
    echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}





######################
# ACTION=SysCIDOriginate  - insert Originate Manager statement allowing small CIDs for system calls
######################
if ($ACTION=="SysCIDOriginate") {
    if ( (strlen($exten)<1) or (strlen($channel)<1) or (strlen($ext_context)<1) or (strlen($queryCID)<1) ) {
        echo "Exten $exten is not valid or queryCID $queryCID is not valid, Originate command not inserted\n";
    } else {
        $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','Callerid: $queryCID','Account: $queryCID','','','','');";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        echo "Originate command sent for Exten $exten Channel $channel on $server_ip\n";
    }
}



######################
# ACTION=Originate, OriginateName, OriginateNameVmail  - insert Originate Manager statement
######################
if ($ACTION=="OriginateName") {
    if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "extenName $extenName must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nOriginateName Action not sent\n";
    } else {
        $stmt="SELECT SQL_NO_CACHE dialplan_number FROM phones WHERE server_ip='$server_ip' AND extension='$extenName';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $name_count = mysql_num_rows($rslt);
        if ($name_count>0) {
            $row=mysql_fetch_row($rslt);
            $exten = $row[0];
            $ACTION="Originate";
        }
    }
}

if ($ACTION=="OriginateNameVmail") {
    if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($exten)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "extenName $extenName must be set\n";
        echo "exten $exten must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nOriginateNameVmail Action not sent\n";
    } else {
        $stmt="SELECT voicemail_id FROM phones WHERE server_ip='$server_ip' AND extension='$extenName';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $name_count = mysql_num_rows($rslt);
        if ($name_count>0) {
            $row=mysql_fetch_row($rslt);
            $exten = "$exten$row[0]";
            $ACTION="Originate";
        }
    }
}

if ($ACTION=="OriginateVDRelogin") {
    if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (preg_match("/SIP/",$protocol)) ) {
        $CIDdate = date("ymdHis");
        $DS='-';
        $SIPSAK_prefix = 'LIN-';
        print "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
        passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
        $queryCID = "$SIPSAK_prefix$campaign$DS$CIDdate";
    }
    $user_channel = $channel;
    if ($protocol == 'EXTERNAL' or $protocol == 'Local') {
        $user_channel .= '@' . $ext_context;
    }
    $outbound_cid_name = "OSDial#$user_channel";
    $outbound_cid = "0000000000";
    $ACTION="Originate";
}

if ($ACTION=="Originate") {
    if ( (strlen($exten)<1) or (strlen($channel)<1) or (strlen($ext_context)<1) or (strlen($queryCID)<10) ) {
        echo "Exten $exten is not valid or queryCID $queryCID is not valid, Originate command not inserted\n";
    } else {
        if (strlen($outbound_cid)>1) {
            $outCID = "\"$outbound_cid_name\" <$outbound_cid>";
        } else {
            $outCID = "\"\" <0000000000>";
        }
        $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','Callerid: $outCID','Account: $queryCID','','','','');";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        echo "Originate command sent for Exten $exten Channel $channel on $server_ip\n";
    }
}



######################
# ACTION=HangupConfDial  - find the Local channel that is in the conference and needs to be hung up
######################
$local_hangup=0;
if ($ACTION=="HangupConfDial") {
    $row='';
    $rowx='';
    $channel_live=1;
    if (strlen($exten)<3 or strlen($queryCID)<15 or strlen($ext_context)<1) {
        $channel_live=0;
        echo "conference $exten is not valid or ext_context $ext_context or queryCID $queryCID is not valid, Hangup command not inserted\n";
    } else {
        $local_DEF = 'Local/';
        $local_AMP = '@';
        $hangup_channel_prefix = "$local_DEF$exten$local_AMP$ext_context";

        $stmt="SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='$server_ip' AND channel LIKE '$hangup_channel_prefix%';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row > 0) {
            $stmt="SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='$server_ip' AND channel LIKE '$hangup_channel_prefix%';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            $channel=$rowx[0];
            $local_hangup=1;
            $ACTION="Hangup";
            $queryCID = preg_replace("/^./","G",$queryCID);
        }
    }
}



######################
# ACTION=Hangup  - insert Hangup Manager statement
######################
if ($ACTION=="Hangup") {
    $row='';
    $rowx='';
    $channel_live=1;
    if ( (strlen($channel)<3) or (strlen($queryCID)<15) ) {
        $channel_live=0;
        echo "Channel $channel is not valid or queryCID $queryCID is not valid, Hangup command not inserted\n";
    } else {
        if (strlen($call_server_ip)<7) $call_server_ip = $server_ip;
        if ($local_hangup>0) {
            $dbout = "$NOW_TIME|LOCHU|$user|$channel|$server_ip|$call_server_ip|$exten|$ext_context|$hangup_channel_prefix|$channel|";
        } elseif ($auto_dial_level>0) {
            $dbout = "$NOW_TIME|ADCHU|$user|$channel|$call_server_ip|$exten|$secondS|$CalLCID|";
            if (strlen($CalLCID)>2 and strlen($exten)>2 and $secondS>0) {
                $stmt="SELECT SQL_NO_CACHE count(*) FROM osdial_auto_calls WHERE channel='$channel' AND callerid='$CalLCID';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                $dbout .= "ACL:$rowx[0]|";
                if ($rowx[0]==0) {
                    echo "Call $CalLCID $channel is not live on $call_server_ip, Checking Live Channel...\n";

                    $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='$call_server_ip' AND channel='$channel' AND extension LIKE '%$exten';";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $dbout .= "LC:$row[0]|";
                    if ($row[0]==0) {
                        $channel_live=0;
                        echo "Channel $channel is not live on $call_server_ip, Hangup command not inserted $rowx[0]\n$stmt\n";
                        $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE channel='$channel' AND extension LIKE '%$exten';";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $dbout .= "LC:$row[0]|";
                    } else {
                        echo "$stmt\n";
                    }
                }    
            } else {
                $dbout .= "BADDATA|";
            }
        } else {
            $dbout = "$NOW_TIME|MDCHU|$user|$channel|$call_server_ip|$exten|$stage|";
            if (strlen($stage)>2 and strlen($channel)>2 and strlen($exten)>2) {
                $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='$call_server_ip' AND channel='$channel' AND extension NOT LIKE '%$exten%';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $dbout .= "AA:$row[0]|";
                if ($row[0] > 0) {
                    $channel_live=0;
                    echo "Channel $channel in use by another agent on $call_server_ip, Hangup command not inserted $row[0]\n$stmt\n";
                } else {
                    echo "$stmt\n";
                }
            } else {
                $dbout .= "NOSTAGE|";
            }
        }
        $dbout .= "LIVE:$channel_live|";

        if ($channel_live==1) {
            $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Hangup','$queryCID','Channel: $channel','','','','','','','','','');";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            echo "Hangup command sent for Channel $channel on $call_server_ip\n";
        }

        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./osdial_debug.txt", "a");
            fwrite ($fp, "$dbout\n");
            fclose($fp);
        }
    }
}



######################
# ACTION=Redirect, RedirectName, RedirectNameVmail, RedirectToPark, RedirectFromPark, RedirectVD, RedirectXtra, RedirectXtraCX
# - insert Redirect Manager statement using extensions name
######################
if ($ACTION=="RedirectVD") {
    if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($exten)<1) or (strlen($campaign)<1) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) or (strlen($uniqueid)<2) or (strlen($lead_id)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "exten $exten must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "auto_dial_level $auto_dial_level must be set\n";
        echo "campaign $campaign must be set\n";
        echo "uniqueid $uniqueid must be set\n";
        echo "lead_id $lead_id must be set\n";
        echo "\nRedirectVD Action not sent\n";
    } else {
        if (strlen($call_server_ip)>6) $server_ip = $call_server_ip;
        $stmt = "select count(*) from osdial_campaigns where campaign_id='$campaign' and campaign_allow_inbound='Y';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            $stmt = "UPDATE osdial_closer_log set end_epoch='$StarTtime', length_in_sec='$secondS',status='XFER' where lead_id='$lead_id' order by start_epoch desc limit 1;";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }
        if ($auto_dial_level < 1) {
            $stmt = "UPDATE osdial_log set end_epoch='$StarTtime', length_in_sec='$secondS',status='XFER' where uniqueid='$uniqueid';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        } else {
            $stmt = "DELETE from osdial_auto_calls where uniqueid='$uniqueid';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }
        $ACTION="Redirect";
    }
}

if ($ACTION=="RedirectToPark") {
    if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($exten)<1) or (strlen($extenName)<1) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) or (strlen($parkedby)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "exten $exten must be set\n";
        echo "extenName $extenName must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "parkedby $parkedby must be set\n";
        echo "\nRedirectToPark Action not sent\n";
    } else {
        if (strlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
        $stmt = "INSERT INTO parked_channels values('$channel','$server_ip','','$extenName','$parkedby','$NOW_TIME');";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $ACTION="Redirect";
    }
}

if ($ACTION=="RedirectFromPark") {
    if (strlen($server_dialstring)>0) $exten = $server_dialstring . $isp . $exten;
    if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($exten)<1) or (strlen($ext_context)<1) or (strlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "exten $exten must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nRedirectFromPark Action not sent\n";
    } else {
        if (strlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
        $stmt = "DELETE FROM parked_channels where server_ip='$server_ip' and channel='$channel';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $ACTION="Redirect";
    }
}

if ($ACTION=="RedirectName") {
    if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "extenName $extenName must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nRedirectName Action not sent\n";
    } else {
        $stmt="SELECT SQL_NO_CACHE dialplan_number FROM phones WHERE server_ip='$server_ip' AND extension='$extenName';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $name_count = mysql_num_rows($rslt);
        if ($name_count>0) {
            $row=mysql_fetch_row($rslt);
            $exten = $row[0];
            $ACTION="Redirect";
        }
    }
}

if ($ACTION=="RedirectNameVmail") {
    if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($extenName)<1)  or (strlen($exten)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "extenName $extenName must be set\n";
        echo "exten $exten must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nRedirectNameVmail Action not sent\n";
    } else {
        $stmt="SELECT voicemail_id FROM phones WHERE server_ip='$server_ip' AND extension='$extenName';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $name_count = mysql_num_rows($rslt);
        if ($name_count>0) {
            $row=mysql_fetch_row($rslt);
            $exten = "$exten$row[0]";
            $ACTION="Redirect";
        }
    }
}

if ($ACTION=="RedirectXtraCX") {
    $DBout='';
    $row='';
    $rowx='';
    $channel_liveX=1;
    $channel_liveY=1;
    if ((strlen($channel)<3 or strlen($queryCID)<15 or strlen($exten)<1 or strlen($ext_context)<1 or strlen($ext_priority)<1 or strlen($extrachannel)<3) and !preg_match("/NEXTAVAILABLE/",$exten)) {
        $channel_liveX=0;
        $channel_liveY=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "ExtraChannel $extrachannel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "exten $exten must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nRedirect Action not sent\n";
        if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./osdial_debug.txt", "a");
                fwrite ($fp, "$NOW_TIME|RDCXC|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|\n");
                fclose($fp);
            }
        }
    } else {
        if (preg_match("/NEXTAVAILABLE/",$exten)) {
            $stmt="UPDATE osdial_conferences SET extension='$protocol/$extension$NOWnum',leave_3way='0' WHERE server_ip='$server_ip' AND (extension='' OR extension IS NULL) AND conf_exten!='$session_id' LIMIT 1;";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            if ($affected_rows > 0) {
                $stmt="SELECT SQL_NO_CACHE conf_exten FROM osdial_conferences WHERE server_ip='$server_ip' AND extension='$protocol/$extension$NOWnum' AND conf_exten!='$session_id';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $exten = $row[0];

                $stmt="SELECT SQL_NO_CACHE channel_group FROM live_sip_channels WHERE server_ip='$server_ip' AND channel='$agentchannel';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                if ($row[0] != '') $agentchannel = $row[0];

                if ( (preg_match("/^8300/",$extension)) and ($protocol == 'Local') ) $extension = "$extension$user";

                $stmt="UPDATE osdial_conferences SET extension='$protocol/$extension' WHERE server_ip='$server_ip' AND conf_exten='$exten' LIMIT 1;";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $stmt="UPDATE osdial_conferences SET leave_3way='1',leave_3way_datetime='$NOW_TIME',extension='3WAY_$user' WHERE server_ip='$server_ip' AND conf_exten='$session_id';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $queryCID = "CXAR24$NOWnum";
                $stmtB="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $agentchannel','Context: $ext_context','Exten: $exten','Priority: 1','CallerID: $queryCID','Account: $queryCID','','','','');";
                if ($format=='debug') echo "\n<!-- $stmtB -->";
                $rslt=mysql_query($stmtB, $link);

                $stmt="UPDATE osdial_live_agents SET conf_exten='$exten' WHERE server_ip='$server_ip' AND user='$user';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                if ($auto_dial_level < 1) {
                    $stmt = "DELETE FROM osdial_auto_calls WHERE lead_id='$lead_id' AND callerid LIKE 'M%';";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                }

                echo "NeWSessioN|$exten|\n";
                echo "|$stmtB|\n";

                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./osdial_debug.txt", "a");
                    fwrite ($fp, "$NOW_TIME|RDCXCNA|$filename|$user-$NOWnum|$campaign|$agentchannel|$exten|\n");
                    fclose($fp);
                }
                exit;

            } else {
                $channel_liveX=0;
                echo "Cannot find empty conference on $server_ip, Redirect command not inserted\n|$stmt|";
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Cannot find empty conference on $server_ip";
            }

        }

        if (strlen($call_server_ip)<7) $call_server_ip = $server_ip;

        $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='$call_server_ip' AND channel='$channel';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt="SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='$call_server_ip' AND channel='$channel';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                $channel_liveX=0;
                echo "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $call_server_ip";
            }    
        }
        $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='$server_ip' AND channel='$extrachannel';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt="SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='$server_ip' and channel='$extrachannel';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                $channel_liveY=0;
                echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $server_ip";
            }    
        }
        if ( ($channel_liveX==1) && ($channel_liveY==1) ) {
            $stmt="SELECT SQL_NO_CACHE count(*) FROM osdial_live_agents WHERE lead_id='$lead_id' AND user!='$user';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0] < 1) {
                $channel_liveY=0;
                echo "No Local agent to send call to, Redirect command not inserted\n";
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "No Local agent to send call to";
            } else {
                $stmt="SELECT SQL_NO_CACHE server_ip,conf_exten,user FROM osdial_live_agents WHERE lead_id='$lead_id' AND user!='$user';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                $dest_server_ip = $rowx[0];
                $dest_session_id = $rowx[1];
                $dest_user = $rowx[2];
                $S='*';

                $D_s_ip = explode('.', $dest_server_ip);
                if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$isp$dest_session_id$S$lead_id$S$dest_user$S$phone_code$S$phone_number$S$campaign$S";

                $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $dest_dialstring','Priority: $ext_priority','CallerID: $queryCID','Account: $queryCID','','','','');";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','$queryCID','Channel: $extrachannel','','','','','','','','','');";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                echo "RedirectXtraCX command sent for Channel $channel on $call_server_ip and \nHungup $extrachannel on $server_ip\n";
                if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel on $call_server_ip, Hungup $extrachannel on $server_ip";
            }
        } else {
            if ($channel_liveX==1) {
                $ACTION="Redirect";
                $server_ip = $call_server_ip;
            }
            if ($channel_liveY==1) {
                $ACTION="Redirect";
                $channel = $extrachannel;
            }
            if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Changed to Redirect: $channel on $server_ip";
        }

        if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./osdial_debug.txt", "a");
                fwrite ($fp, "$NOW_TIME|RDCXC|$filename|$user|$campaign|$DBout|\n");
                fclose($fp);
            }
        }
    }
}

if ($ACTION=="RedirectXtra") {
    if ($channel=="$extrachannel") {
        $ACTION="Redirect";
    } else {
        $row='';
        $rowx='';
        $channel_liveX=1;
        $channel_liveY=1;
        if ((strlen($channel)<3 or strlen($queryCID)<15 or strlen($exten)<1 or strlen($ext_context)<1 or strlen($ext_priority)<1 or strlen($extrachannel)<3) and !preg_match("/NEXTAVAILABLE/",$exten)) {
            $channel_liveX=0;
            $channel_liveY=0;
            echo "One of these variables is not valid:\n";
            echo "Channel $channel must be greater than 2 characters\n";
            echo "ExtraChannel $extrachannel must be greater than 2 characters\n";
            echo "queryCID $queryCID must be greater than 14 characters\n";
            echo "exten $exten must be set\n";
            echo "ext_context $ext_context must be set\n";
            echo "ext_priority $ext_priority must be set\n";
            echo "\nRedirect Action not sent\n";
            if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./osdial_debug.txt", "a");
                    fwrite ($fp, "$NOW_TIME|RDX|$filename|$user|$campaign|$$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|\n");
                    fclose($fp);
                }
            }
        } else {
            if (preg_match("/NEXTAVAILABLE/",$exten)) {
                $stmt="UPDATE osdial_conferences SET extension='$protocol/$extension$NOWnum',leave_3way='0' WHERE server_ip='$server_ip' AND (extension='' OR extension IS NULL) AND conf_exten!='$session_id' LIMIT 1;";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                if ($affected_rows > 0) {
                    $stmt="SELECT SQL_NO_CACHE conf_exten FROM osdial_conferences WHERE server_ip='$server_ip' AND extension='$protocol/$extension$NOWnum' AND conf_exten!='$session_id';";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $exten = $row[0];

                    $stmt="SELECT SQL_NO_CACHE channel_group FROM live_sip_channels WHERE server_ip='$server_ip' AND channel='$agentchannel';";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    if ($row[0] != '') $agentchannel = $row[0];

                    $stmt="UPDATE osdial_conferences SET extension='$protocol/$extension' WHERE server_ip='$server_ip' AND conf_exten='$exten' LIMIT 1;";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    $stmt="UPDATE osdial_conferences SET leave_3way='1',leave_3way_datetime='$NOW_TIME',extension='3WAY_$user' WHERE server_ip='$server_ip' AND conf_exten='$session_id';";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    $queryCID = "CXAR23$NOWnum";
                    $stmtB="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $agentchannel','Context: $ext_context','Exten: $exten','Priority: 1','CallerID: $queryCID','Account: $queryCID','','','','');";
                    if ($format=='debug') echo "\n<!-- $stmtB -->";
                    $rslt=mysql_query($stmtB, $link);

                    $stmt="UPDATE osdial_live_agents SET conf_exten='$exten' WHERE server_ip='$server_ip' AND user='$user';";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    if ($auto_dial_level < 1) {
                        $stmt = "DELETE FROM osdial_auto_calls WHERE lead_id='$lead_id' AND callerid LIKE 'M%';";
                        if ($format=='debug') echo "\n<!-- $stmt -->";
                        $rslt=mysql_query($stmt, $link);
                    }

                    echo "NeWSessioN|$exten|\n";
                    echo "|$stmtB|\n";

                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./osdial_debug.txt", "a");
                        fwrite ($fp, "$NOW_TIME|RDXNA|$filename|$user-$NOWnum|$campaign|$agentchannel|$exten|\n");
                        fclose($fp);
                    }
                    exit;

                } else {
                    $channel_liveX=0;
                    echo "Cannot find empty conference on $server_ip, Redirect command not inserted\n|$stmt|";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Cannot find empty conference on $server_ip";
                }
            }

            if (strlen($call_server_ip)<7) $call_server_ip = $server_ip;

            $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='$call_server_ip' AND channel='$channel';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ( ($row[0]==0) && (!preg_match("/SECOND/",$filename)) ) {
                $stmt="SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='$call_server_ip' AND channel='$channel';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                if ($rowx[0]==0) {
                    $channel_liveX=0;
                    echo "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $call_server_ip";
                }    
            }
            $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='$server_ip' AND channel='$extrachannel';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ( ($row[0]==0) && (!preg_match("/SECOND/",$filename)) ) {
                $stmt="SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='$server_ip' AND channel='$extrachannel';";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                if ($rowx[0]==0) {
                    $channel_liveY=0;
                    echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $server_ip";
                }    
            }
            if ( ($channel_liveX==1) && ($channel_liveY==1) ) {
                if ( ($server_ip=="$call_server_ip") or (strlen($call_server_ip)<7) ) {
                    $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $channel','ExtraChannel: $extrachannel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','Account: $queryCID','','','');";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    echo "RedirectXtra command sent for Channel $channel and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel and $extrachannel to $exten on $server_ip";
                } else {
                    $S='*';
                    $D_s_ip = explode('.', $server_ip);
                    if (strlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                    if (strlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                    if (strlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                    if (strlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                    if (strlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                    if (strlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                    if (strlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                    if (strlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                    $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$isp$exten";

                    $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$call_server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $dest_dialstring','Priority: $ext_priority','CallerID: $queryCID','Account: $queryCID','','','','');";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $extrachannel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $queryCID','Account: $queryCID','','','','');";
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    echo "RedirectXtra command sent for Channel $channel on $call_server_ip and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                    if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel/$call_server_ip and $extrachannel/$server_ip to $exten";
                }
            } else {
                if ($channel_liveX==1) {
                    $ACTION="Redirect";
                    $server_ip = $call_server_ip;
                }
                if ($channel_liveY==1) {
                    $ACTION="Redirect";
                    $channel = $extrachannel;
                }
            }

            if (preg_match("/SECOND|FIRST|DEBUG/",$filename)) {
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./osdial_debug.txt", "a");
                    fwrite ($fp, "$NOW_TIME|RDX|$filename|$user|$campaign|$DBout|\n");
                    fclose($fp);
                }
            }
        }
    }
}

if ($ACTION=="Redirect") {
    ### for manual dial OSDIAL calls send the second attempt to transfer the call
    if ($stage=="2NDXfeR") {
        $local_DEF = 'Local/';
        $local_AMP = '@';
        $hangup_channel_prefix = "$local_DEF$session_id$local_AMP$ext_context";

        $stmt="SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='$server_ip' AND channel LIKE '$hangup_channel_prefix%';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row > 0) {
            $stmt="SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='$server_ip' AND channel LIKE '$hangup_channel_prefix%';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            $channel=$rowx[0];
            $channel = preg_replace("/1$/","2",$channel);
            $queryCID = preg_replace("/^./","Q",$queryCID);
        }
    }

    $row='';
    $rowx='';
    $channel_live=1;
    if ( (strlen($channel)<3) or (strlen($queryCID)<15)  or (strlen($exten)<1)  or (strlen($ext_context)<1)  or (strlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "exten $exten must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nRedirect Action not sent\n";
    } else {
        if (strlen($call_server_ip)>6) $server_ip = $call_server_ip;
        $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='$server_ip' AND channel='$channel';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt="SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='$server_ip' AND channel='$channel';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                $channel_live=0;
                echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
            }    
        }
        if ($channel_live==1) {
            if (strlen($outbound_cid)>1) {
                $outCID = "\"$outbound_cid_name\" <$outbound_cid>";
                $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Setvar','$queryCID','Channel: $channel','Variable: CALLERID(all)','Value: $outCID','Account: $queryCID','','','','','','');";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            } else {
                $outCID = $queryCID;
            }
            $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Redirect','$queryCID','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','CallerID: $outCID','Account: $queryCID','','','','');";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            echo "Redirect command sent for Channel $channel on $server_ip\n";
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./osdial_debug.txt", "a");
                fwrite ($fp, "$NOW_TIME|RD|$queryCID|$channel|$exten|$ext_context|$outCID|\n");
                fclose($fp);
            }
        }
    }
}



######################
# ACTION=Monitor or Stop Monitor  - insert Monitor/StopMonitor Manager statement to start recording on a channel
######################
if ( ($ACTION=="Monitor") || ($ACTION=="StopMonitor") ) {
    if ($ACTION=="StopMonitor") {
        $SQLfile = "";
    } else {
        $SQLfile = "File: $filename";
    }

    $row='';
    $rowx='';
    $channel_live=1;
    if ( (strlen($channel)<3) or (strlen($queryCID)<15) or (strlen($filename)<2) ) {
        $channel_live=0;
        echo "Channel $channel is not valid or queryCID $queryCID is not valid or filename: $filename is not valid, $ACTION command not inserted\n";
    } else {
        $stmt="SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='$server_ip' AND channel='$channel';";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt="SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='$server_ip' AND channel='$channel';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                $channel_live=0;
                echo "Channel $channel is not live on $server_ip, $ACTION command not inserted\n";
            }    
        }
        if ($channel_live==1) {
            $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','$ACTION','$queryCID','Channel: $channel','$SQLfile','Account: $queryCID','','','','','','','');";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            if ($ACTION=="Monitor") {
                $stmt = "INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename,lead_id,user,uniqueid) values('$channel','$server_ip','$exten','$NOW_TIME','$StarTtime','$filename','$lead_id','$user','$uniqueid')";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $stmt="SELECT SQL_NO_CACHE recording_id FROM recording_log WHERE filename='$filename';";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $row=mysql_fetch_row($rslt);
                $recording_id = $row[0];
            } else {
                $stmt="SELECT SQL_NO_CACHE recording_id,start_epoch FROM recording_log WHERE filename='$filename';";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $rec_count = mysql_num_rows($rslt);
                if ($rec_count>0) {
                    $row=mysql_fetch_row($rslt);
                    $recording_id = $row[0];
                    $start_time = $row[1];
                    $length_in_sec = ($StarTtime - $start_time);
                    $length_in_min = ($length_in_sec / 60);
                    $length_in_min = sprintf("%8.2f", $length_in_min);

                    $stmt = "UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtime',length_in_sec=$length_in_sec,length_in_min='$length_in_min' where filename='$filename'";
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                }
            }
            echo "$ACTION command sent for Channel $channel on $server_ip\nFilename: $filename\nRecorDing_ID: $recording_id\n";
        }
    }
}



######################
# ACTION=MonitorConf or StopMonitorConf  - insert Monitor/StopMonitor Manager statement to start recording on a conference
######################
if ( ($ACTION=="MonitorConf") || ($ACTION=="StopMonitorConf") ) {
    $row='';
    $rowx='';
    $channel_live=1;
    if ( (strlen($exten)<3) or (strlen($channel)<4) or (strlen($filename)<5) ) {
        $channel_live=0;
        echo "Channel $channel is not valid or exten $exten is not valid or filename: $filename is not valid, $ACTION command not inserted\n";
    } else {

        if ($ACTION=="MonitorConf") {
            $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$filename','Channel: $channel','Context: $ext_context','Exten: $exten','Priority: $ext_priority','Callerid: $filename','Account: $CalLCID','','','','');";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $stmt = "INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename,lead_id,user,uniqueid) values('$channel','$server_ip','$exten','$NOW_TIME','$StarTtime','$filename','$lead_id','$user','$uniqueid')";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $stmt="SELECT SQL_NO_CACHE recording_id FROM recording_log WHERE filename='$filename'";
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $row=mysql_fetch_row($rslt);
            $recording_id = $row[0];
        } else {
            $stmt="SELECT SQL_NO_CACHE recording_id,start_epoch FROM recording_log WHERE filename='$filename'";
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $rec_count = mysql_num_rows($rslt);
            if ($rec_count>0) {
                $row=mysql_fetch_row($rslt);
                $recording_id = $row[0];
                $start_time = $row[1];
                $length_in_sec = ($StarTtime - $start_time);
                $length_in_min = ($length_in_sec / 60);
                $length_in_min = sprintf("%8.2f", $length_in_min);

                $stmt = "UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtime',length_in_sec=$length_in_sec,length_in_min='$length_in_min' where filename='$filename'";
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
            }

            # find and hang up all recordings going on in this conference # and extension = '$exten' 
            $stmt="SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='$server_ip' AND channel LIKE '$channel%' AND channel LIKE '%,1';";
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            #$rec_count = intval(mysql_num_rows($rslt) / 2);
            $rec_count = mysql_num_rows($rslt);
            $h=0;
            while ($rec_count>$h) {
                $rowx=mysql_fetch_row($rslt);
                $HUchannel[$h] = $rowx[0];
                $h++;
            }
            $i=0;
            while ($h>$i) {
                $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','RH12345$StarTtime$i','Channel: $HUchannel[$i]','','','','','','','','','');";
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $i++;
            }
        }
        echo "$ACTION command sent for Channel $channel on $server_ip\nFilename: $filename\nRecorDing_ID: $recording_id\n RECORDING WILL LAST UP TO 60 MINUTES\n";
    }
}



######################
# ACTION=VolumeControl  - raise or lower the volume of a meetme participant
######################
if ($ACTION=="VolumeControl") {
    if ( (strlen($exten)<1) or (strlen($channel)<1) or (strlen($stage)<1) or (strlen($queryCID)<1) ) {
        echo "Conference $exten, Stage $stage is not valid or queryCID $queryCID is not valid, Originate command not inserted\n";
    } else {
        $participant_number='XXYYXXYYXXYYXX';
        if (preg_match('/UP/',$stage)) $vol_prefix='4';
        if (preg_match('/DOWN/',$stage)) $vol_prefix='3';
        if (preg_match('/UNMUTE/',$stage)) $vol_prefix='2';
        if (preg_match('/MUTING/',$stage)) $vol_prefix='1';
        $local_DEF = 'Local/';
        $local_AMP = '@';
        $volume_local_channel = "$local_DEF$participant_number$vol_prefix$exten$local_AMP$ext_context";

        $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $volume_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','Account: $queryCID','','','$channel','$exten');";
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        echo "Volume command sent for Conference $exten, Stage $stage Channel $channel on $server_ip\n";
    }
}



$ENDtime = date("U");
$RUNtime = ($ENDtime - $StarTtime);
if ($format=='debug') echo "\n<!-- script runtime: $RUNtime seconds -->";
if ($format=='debug') echo "\n</body>\n</html>\n";

exit; 

?>





