<?php
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

$DB=0;
$DBFILE=0;

header('Cache-Control: public, no-cache, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() - 60)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

require_once("dbconnect.php");
require_once("functions.php");

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
$nodeletevdac = get_variable("nodeletevdac");
$log_campaign = get_variable("log_campaign");

$secondS = OSDpreg_replace("/[^0-9]/","",$secondS);
#if ($config['settings']['use_non_latin'] < 1) {
#    $user=OSDpreg_replace("/[^0-9a-zA-Z]/","",$user);
#    $pass=OSDpreg_replace("/[^0-9a-zA-Z]/","",$pass);
#}

# default optional vars if not set
if (empty($ACTION)) $ACTION="Originate";
if (empty($format)) $format="alert";
if (empty($ext_priority)) $ext_priority="1";

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$NOWnum = date("YmdHis");
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
    echo "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}





######################
# ACTION=SysCIDOriginate  - insert Originate Manager statement allowing small CIDs for system calls
######################
if ($ACTION=="SysCIDOriginate") {
    if ( (OSDstrlen($exten)<1) or (OSDstrlen($channel)<1) or (OSDstrlen($ext_context)<1) or (OSDstrlen($queryCID)<1) ) {
        echo "Exten $exten is not valid or queryCID $queryCID is not valid, Originate command not inserted\n";
    } else {
        $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Originate','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','Callerid: %s','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($channel),mres($ext_context),mres($exten),mres($ext_priority),mres($queryCID),mres($queryCID));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        echo "Originate command sent for Exten $exten Channel $channel on $server_ip\n";
    }
}



######################
# ACTION=Originate, OriginateName, OriginateNameVmail  - insert Originate Manager statement
######################
if ($ACTION=="OriginateName") {
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15)  or (OSDstrlen($extenName)<1)  or (OSDstrlen($ext_context)<1)  or (OSDstrlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "extenName $extenName must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nOriginateName Action not sent\n";
    } else {
        $stmt=sprintf("SELECT SQL_NO_CACHE dialplan_number FROM phones WHERE server_ip='%s' AND extension='%s';",mres($server_ip),mres($extenName));
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
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15)  or (OSDstrlen($extenName)<1)  or (OSDstrlen($exten)<1)  or (OSDstrlen($ext_context)<1)  or (OSDstrlen($ext_priority)<1) ) {
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
        $stmt=sprintf("SELECT voicemail_id FROM phones WHERE server_ip='%s' AND extension='%s';",mres($server_ip),mres($extenName));
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
    if ( ($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (OSDpreg_match("/SIP/",$protocol)) ) {
        $CIDdate = date("ymdHis");
        $DS='-';
        $SIPSAK_prefix = 'LIN-';
        print "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
        passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
        $queryCID = "$SIPSAK_prefix$campaign$DS$CIDdate";
    }
    $user_channel = $channel;
    if ($protocol == 'EXTERNAL' or $protocol == 'Local') {
        if (!OSDpreg_match('/\@'.$ext_context.'/',$user_channel)) $user_channel .= '@' . $ext_context;
    }
    $outbound_cid_name = "OSDial#$user_channel";
    $outbound_cid = "0000000000";
    $ACTION="Originate";
}

if ($ACTION=="Originate") {
    if ( (OSDstrlen($exten)<1) or (OSDstrlen($channel)<1) or (OSDstrlen($ext_context)<1) or (OSDstrlen($queryCID)<10) ) {
        echo "Exten $exten is not valid or queryCID $queryCID is not valid, Originate command not inserted\n";
    } else {
        if (OSDstrlen($outbound_cid)>1) {
            $outCID = "\"$outbound_cid_name\" <$outbound_cid>";
        } else {
            $outCID = "\"\" <0000000000>";
        }
        $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Originate','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','Callerid: %s','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($channel),mres($ext_context),mres($exten),mres($ext_priority),mres($outCID),mres($queryCID));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        echo "Originate command sent for Exten $exten Channel $channel on $server_ip\n";
    }
}


######################
# ACTION=HangupConfDial  - find the Local channel that is in the conference and needs to be hung up
######################
if ($ACTION=="HangupConfDial") {
    $row='';
    $rowx='';
    $channel_live=1;
    if (OSDstrlen($exten)<3 or OSDstrlen($queryCID)<15 or OSDstrlen($ext_context)<1) {
        $channel_live=0;
        echo "conference $exten is not valid or ext_context $ext_context or queryCID $queryCID is not valid, Hangup command not inserted\n";
    } else {
        $local_DEF = 'Local/';
        $local_AMP = '@';
        $hangup_channel_prefix = "$local_DEF"."7"."$exten$local_AMP$ext_context";
        
        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s%%';",mres($server_ip),mres($hangup_channel_prefix));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row > 0) {
            $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s%%';",mres($server_ip),mres($hangup_channel_prefix));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            $channel=$rowx[0];
            $local_hangup=1;
            $ACTION="Hangup";
            $queryCID = OSDpreg_replace("/^./","G",$queryCID);
        }
    }    
}


######################
# ACTION=Hangup- insert Hangup Manager statement
######################
if ($ACTION=="Hangup") {
    #$stmt=sprintf("UPDATE osdial_live_agents SET external_hangup='0' WHERE user='%s';",mres($user));
    #if ($format=='debug') echo "\n<!-- $stmt -->";
    #$rslt=mysql_query($stmt, $link);

    $row='';
    $rowx='';
    $channel_live=1;
    if (OSDstrlen($channel)<3 or OSDstrlen($queryCID)<15) {
        $channel_live=0;
        echo "Channel $channel is not valid or queryCID $queryCID is not valid, Hangup command not inserted\n";
    } else {
        if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;

        if ($auto_dial_level>0 and OSDstrlen($CalLCID)>2 and OSDstrlen($exten)>2 and $secondS > 0) {
            $stmt=sprintf("SELECT count(*) FROM osdial_auto_calls WHERE channel='%s' AND callerid='%s';",mres($channel),mres($CalLCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                echo "Call $CalLCID $channel is not live on $call_server_ip, Checking Live Channel...\n";

                $stmt=sprintf("SELECT count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s' AND extension LIKE '%%%s';",mres($call_server_ip),mres($channel),mres($exten));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                if ($row[0]==0) {
                    $channel_live=0;
                    echo "Channel $channel is not live on $call_server_ip, Hangup command not inserted $rowx[0]\n$stmt\n";
                } else {
                    echo "$stmt\n";
                }
            }
        }
        if ($auto_dial_level<1 and OSDstrlen($stage)>2 and OSDstrlen($channel)>2 and OSDstrlen($exten)>2) {
            $stmt=sprintf("SELECT count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s' AND extension NOT LIKE '%%%s%%';",mres($call_server_ip),mres($channel),mres($exten));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0) {
                $channel_live=0;
                echo "Channel $channel in use by another agent on $call_server_ip, Hangup command not inserted $rowx[0]\n$stmt\n";
                if ($DBFILE) debugLog('osdial_debug',"$NOW_TIME|MDCHU|$user|$channel|$call_server_ip|$exten|\n");
            } else {
                echo "$stmt\n";
            }
        }

        $lccnt=0;
        $lsccnt=0;
        $stmt=sprintf("SELECT count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $lccnt=$row[0];

        $stmt=sprintf("SELECT count(*) FROM live_sip_channels WHERE server_ip='%s' AND (channel='%s' OR channel_group='%s');",mres($call_server_ip),mres($channel),mres($channel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $lsccnt=$row[0];

        if ($lccnt==0 and $lsccnt==0) {
            $channel_live=0;
        }

        $stmt=sprintf("SELECT uniqueid,campaign_id,group_id,lead_id,UNIX_TIMESTAMP(event_time) FROM osdial_events WHERE event='CAMP_IN_ANSWER' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $oecnt = mysql_num_rows($rslt);
        if ($oecnt > 0) {
            $row=mysql_fetch_row($rslt);
            $CIAuniqueid = $row[0];
            $CIAcampaign = $row[1];
            $CIAgroup = $row[2];
            $CIAlead = $row[3];
            $CIAtime = $row[4];
            $StarTtime = date("U");
            $CIAsecs = $StarTtime - $CIAtime;
            if ($CIAsecs<1) $CIAsecs=0;
            $stmt=sprintf("SELECT count(*) FROM osdial_events WHERE event='CAMP_IN_HANGUP' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $oecnt = $row[0];
            if ($oecnt==0) {
                osdevent($link,['event'=>'CAMP_IN_HANGUP','uniqueid'=>$CIAuniqueid,'server_ip'=>$call_server_ip,'callerid'=>$CalLCID,'campaign_id'=>$CIAcampaign,'group_id'=>$CIAgroup,'lead_id'=>$CIAlead,'user'=>$user,'data1'=>$CIAsecs]);
            }
        }
        $stmt=sprintf("SELECT uniqueid,campaign_id,lead_id,UNIX_TIMESTAMP(event_time) FROM osdial_events WHERE event='CAMP_OUT_ANSWER' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $oecnt = mysql_num_rows($rslt);
        if ($oecnt > 0) {
            $row=mysql_fetch_row($rslt);
            $COAuniqueid = $row[0];
            $COAcampaign = $row[1];
            $COAlead = $row[2];
            $COAtime = $row[3];
            $StarTtime = date("U");
            $COAsecs = $StarTtime - $COAtime;
            if ($COAsecs<1) $COAsecs=0;
            $stmt=sprintf("SELECT count(*) FROM osdial_events WHERE event='CAMP_OUT_HANGUP' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $oecnt = $row[0];
            if ($oecnt==0) {
                osdevent($link,['event'=>'CAMP_OUT_HANGUP','uniqueid'=>$COAuniqueid,'server_ip'=>$call_server_ip,'callerid'=>$CalLCID,'campaign_id'=>$COAcampaign,'lead_id'=>$COAlead,'user'=>$user,'data1'=>$COAsecs]);
            }
        }
        $stmt=sprintf("SELECT uniqueid,campaign_id,lead_id,UNIX_TIMESTAMP(event_time) FROM osdial_events WHERE event='CB_ANSWER' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $oecnt = mysql_num_rows($rslt);
        if ($oecnt > 0) {
            $row=mysql_fetch_row($rslt);
            $CBuniqueid = $row[0];
            $CBcampaign = $row[1];
            $CBlead = $row[2];
            $CBtime = $row[3];
            $StarTtime = date("U");
            $CBsecs = $StarTtime - $CBtime;
            if ($CBsecs<1) $CBsecs=0;
            $stmt=sprintf("SELECT count(*) FROM osdial_events WHERE event='CB_HANGUP' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $oecnt = $row[0];
            if ($oecnt==0) {
                osdevent($link,['event'=>'CB_HANGUP','uniqueid'=>$CBuniqueid,'server_ip'=>$call_server_ip,'callerid'=>$CalLCID,'campaign_id'=>$CBcampaign,'lead_id'=>$CBlead,'user'=>$user,'data1'=>$CBsecs]);
            }
        }
        $stmt=sprintf("SELECT uniqueid,campaign_id,lead_id,UNIX_TIMESTAMP(event_time) FROM osdial_events WHERE event='IVR_CB_ANSWER' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $oecnt = mysql_num_rows($rslt);
        if ($oecnt > 0) {
            $row=mysql_fetch_row($rslt);
            $ICBuniqueid = $row[0];
            $ICBcampaign = $row[1];
            $ICBlead = $row[2];
            $ICBtime = $row[3];
            $StarTtime = date("U");
            $ICBsecs = $StarTtime - $ICBtime;
            if ($ICBsecs<1) $ICBsecs=0;
            $stmt=sprintf("SELECT count(*) FROM osdial_events WHERE event='IVR_CB_HANGUP' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $oecnt = $row[0];
            if ($oecnt==0) {
                osdevent($link,['event'=>'IVR_CB_HANGUP','uniqueid'=>$ICBuniqueid,'server_ip'=>$call_server_ip,'callerid'=>$CalLCID,'campaign_id'=>$ICBcampaign,'lead_id'=>$ICBlead,'user'=>$user,'data1'=>$ICBsecs]);
            }
        }

        if ($channel_live==1) {
            if (OSDstrlen($CalLCID)>15 and $secondS>0) {
                $stmt=sprintf("SELECT count(*) FROM osdial_auto_calls WHERE callerid='%s';",mres($CalLCID));
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                if ($format=='debug') echo "\n<!-- $rowx[0]|$stmt -->";
                if ($rowx[0] > 0) {
                    $stmt=sprintf("SELECT uniqueid,UNIX_TIMESTAMP(event_time) FROM osdial_events WHERE event='CALL_START' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                    $oecnt = mysql_num_rows($rslt);
                    if ($oecnt > 0) {
                        $row=mysql_fetch_row($rslt);
                        $Cuniqueid = $row[0];
                        $Ctime = $row[1];
                        $Csecs = date("U") - $Ctime;
                        if ($Csecs<1) $Csecs=0;
                        $stmt=sprintf("SELECT count(*) FROM osdial_events WHERE event='CALL_END' AND server_ip='%s' AND callerid='%s';",mres($call_server_ip),mres($CalLCID));
                        if ($format=='debug') echo "\n<!-- $stmt -->";
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $oecnt = $row[0];
                        if ($oecnt==0) {
                            osdevent($link,['event'=>'CALL_END','uniqueid'=>$Cuniqueid,'server_ip'=>$call_server_ip,'callerid'=>$CalLCID]);
                        }
                    }
                   if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                        $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                        mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                        if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                        $stmt=sprintf("SELECT count(*) FROM queue_log WHERE call_id='%s' AND verb='CONNECT';",mres($CalLCID));
                        $rslt=mysql_query($stmt, $linkB);
                        $VAC_cn_ct = mysql_num_rows($rslt);
                        if ($VAC_cn_ct > 0) {
                            $row=mysql_fetch_row($rslt);
                            $caller_connect = $row[0];
                        }
                        if ($format=='debug') echo "\n<!-- $caller_connect|$stmt -->";
                        if ($caller_connect > 0) {
                            $CLqueue_position='1';
                            ### grab call lead information needed for QM logging
                            $stmt=sprintf("SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid,queue_position FROM osdial_auto_calls WHERE callerid='%s' ORDER BY call_time LIMIT 1;",mres($CalLCID));
                            $rslt=mysql_query($stmt, $link);
                            $VAC_qm_ct = mysql_num_rows($rslt);
                            if ($VAC_qm_ct > 0) {
                                $row=mysql_fetch_row($rslt);
                                $auto_call_id =         $row[0];
                                $CLlead_id =            $row[1];
                                $CLphone_number =       $row[2];
                                $CLstatus =             $row[3];
                                $CLcampaign_id =        $row[4];
                                $CLphone_code =         $row[5];
                                $CLalt_dial =           $row[6];
                                $CLstage =              $row[7];
                                $CLcallerid =           $row[8];
                                $CLuniqueid =           $row[9];
                                $CLqueue_position =     $row[10];
                            }
                            if ($format=='debug') echo "\n<!-- $CLcampaign_id|$stmt -->";

                            $CLstage = OSDpreg_replace("/.*-/",'',$CLstage);
                            if (OSDstrlen($CLstage) < 1) $CLstage=0;

                            $stmt=sprintf("SELECT count(*) FROM queue_log WHERE call_id='%s' AND verb='COMPLETECALLER' AND queue='%s';",mres($CalLCID),mres($CLcampaign_id));
                            $rslt=mysql_query($stmt, $linkB);
                            $VAC_cc_ct = mysql_num_rows($rslt);
                            if ($VAC_cc_ct > 0) {
                                $row=mysql_fetch_row($rslt);
                                $caller_complete    = $row[0];
                            }
                            if ($format=='debug') echo "\n<!-- $caller_complete|$stmt -->";

                            if ($caller_complete < 1) {
                                $time_id=0;
                                $stmt=sprintf("SELECT time_id FROM queue_log WHERE call_id='%s' AND verb IN('ENTERQUEUE','CALLOUTBOUND') AND queue='%s';",mres($CalLCID),mres($CLcampaign_id));
                                $rslt=mysql_query($stmt, $linkB);
                                $VAC_eq_ct = mysql_num_rows($rslt);
                                if ($VAC_eq_ct > 0) {
                                    $row=mysql_fetch_row($rslt);
                                    $time_id    = $row[0];
                                }
                                $StarTtime = date("U");
                                if ($time_id > 100000) $secondS = ($StarTtime - $time_id);

                                $data4SQL='';
                                $stmt=sprintf("SELECT queuemetrics_phone_environment FROM osdial_campaigns WHERE campaign_id='%s' AND queuemetrics_phone_environment!='';",mres($log_campaign));
                                $rslt=mysql_query($stmt, $link);
                                if ($DB) echo "$stmt\n";
                                $cqpe_ct = mysql_num_rows($rslt);
                                if ($cqpe_ct > 0) {
                                    $row=mysql_fetch_row($rslt);
                                    $data4SQL = ",data4='$row[0]'";
                                }

                                if ($format=='debug') echo "\n<!-- $caller_complete|$stmt -->";
                                $stmt = sprintf("INSERT INTO queue_log SET partition='P01',time_id='%s',call_id='%s',queue='%s',agent='Agent/%s',verb='COMPLETEAGENT',data1='%s',data2='%s',data3='%s',serverid='%s' %s;",mres($StarTtime),mres($CalLCID),mres($CLcampaign_id),mres($user),mres($CLstage),mres($secondS),mres($CLqueue_position),mres($config['settings']['queuemetrics_log_id']),$data4SQL);
                                $rslt=mysql_query($stmt, $linkB);
                                $affected_rows = mysql_affected_rows($linkB);
                                if ($format=='debug') echo "\n<!-- $affected_rows|$stmt -->";
                            }
                        }
                    }
                }
            }

            $huchans=array();

            $stmt=sprintf("SELECT channel_group FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $chgrpcnt = mysql_num_rows($rslt);
            if ($chgrpcnt>0) {
                $c=0;
                while ($c<$chgrpcnt) {
                    $row=mysql_fetch_row($rslt);
                    $huchans[] = $row[0];
                    $c++;
                }
            } else {
                $huchans[] = $channel;
            }

            foreach ($huchans as $chan) {
                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Hangup','%s','Channel: %s','','','','','','','','','');",mres($NOW_TIME),mres($call_server_ip),mres($chan),mres($queryCID),mres($chan));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }
            echo "Hangup command sent for Channel $channel on $call_server_ip\n";
        }
    }
}

if ($ACTION=="OLDHangup") {
    $row='';
    $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15) ) {
        $channel_live=0;
        echo "Channel $channel is not valid or queryCID $queryCID is not valid, Hangup command not inserted\n";
    } else {
        if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;
        if ($local_hangup>0) {
            $dbout = "$NOW_TIME|LOCHU|$user|$channel|$server_ip|$call_server_ip|$exten|$ext_context|$hangup_channel_prefix|$channel|";
        } elseif ($auto_dial_level>0) {
            $dbout = "$NOW_TIME|ADCHU|$user|$channel|$call_server_ip|$exten|$secondS|$CalLCID|";
            if (OSDstrlen($CalLCID)>2 and OSDstrlen($exten)>2 and $secondS>0) {
                $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_auto_calls WHERE channel='%s' AND callerid='%s';",mres($channel),mres($CalLCID));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                $dbout .= "ACL:$rowx[0]|";
                if ($rowx[0]==0) {
                    echo "Call $CalLCID $channel is not live on $call_server_ip, Checking Live Channel...\n";

                    $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s' AND extension LIKE '%%%s';",mres($call_server_ip),mres($channel),mres($exten));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $dbout .= "LC:$row[0]|";
                    if ($row[0]==0) {
                        $channel_live=0;
                        echo "Channel $channel is not live on $call_server_ip, Hangup command not inserted $rowx[0]\n$stmt\n";
                        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE channel='%s' AND extension LIKE '%%%s';",mres($channel),mres($exten));
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
            if (OSDstrlen($stage)>2 and OSDstrlen($channel)>2 and OSDstrlen($exten)>2) {
                $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s' AND extension NOT LIKE '%%%s%%';",mres($call_server_ip),mres($channel),mres($exten));
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
            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Hangup','%s','Channel: %s','','','','','','','','','');",mres($NOW_TIME),mres($call_server_ip),mres($channel),mres($queryCID),mres($channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            echo "Hangup command sent for Channel $channel on $call_server_ip\n";
        }

        if ($DBFILE) debugLog('osdial_debug',$dbout);
    }
}


if ($ACTION=="EventXferBlindNumber") {
    if ( (OSDstrlen($uniqueid)<2) or (OSDstrlen($server_ip)<1) or (OSDstrlen($queryCID)<1) or (OSDstrlen($campaign)<1) or (OSDstrlen($user)<1) or (OSDstrlen($lead_id)<1) or (OSDstrlen($exten)<1)) {
        echo "\nAction not sent\n";
    } else {
        if (OSDstrlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
        osdevent($link,['event'=>'CALL_XFER_BLIND','uniqueid'=>$uniqueid,'server_ip'=>$server_ip,'callerid'=>$queryCID,'campaign_id'=>$campaign,'user'=>$user,'lead_id'=>$lead_id,'data1'=>$exten]);
    }
}

if ($ACTION=="EventXferBlindGroup") {
    if ( (OSDstrlen($uniqueid)<2) or (OSDstrlen($server_ip)<1) or (OSDstrlen($queryCID)<1) or (OSDstrlen($campaign)<1) or (OSDstrlen($user)<1) or (OSDstrlen($lead_id)<1) or (OSDstrlen($exten)<1)) {
        echo "\nAction not sent\n";
    } else {
        if (OSDstrlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
        osdevent($link,['event'=>'CALL_XFER_BLIND','uniqueid'=>$uniqueid,'server_ip'=>$server_ip,'callerid'=>$queryCID,'campaign_id'=>$campaign,'group_id'=>$exten,'user'=>$user,'lead_id'=>$lead_id]);
    }
}

if ($ACTION=="Event3wayNumber") {
    if ( (OSDstrlen($uniqueid)<2) or (OSDstrlen($server_ip)<1) or (OSDstrlen($queryCID)<1) or (OSDstrlen($campaign)<1) or (OSDstrlen($user)<1) or (OSDstrlen($lead_id)<1) or (OSDstrlen($exten)<1)) {
        echo "\nAction not sent\n";
    } else {
        osdevent($link,['event'=>'CALL_3WAY','uniqueid'=>$uniqueid,'server_ip'=>$server_ip,'callerid'=>$queryCID,'campaign_id'=>$campaign,'user'=>$user,'lead_id'=>$lead_id,'data1'=>$exten]);
    }
}

if ($ACTION=="Event3wayGroup") {
    if ( (OSDstrlen($uniqueid)<2) or (OSDstrlen($server_ip)<1) or (OSDstrlen($queryCID)<1) or (OSDstrlen($campaign)<1) or (OSDstrlen($user)<1) or (OSDstrlen($lead_id)<1) or (OSDstrlen($exten)<1)) {
        echo "\nAction not sent\n";
    } else {
        osdevent($link,['event'=>'CALL_3WAY','uniqueid'=>$uniqueid,'server_ip'=>$server_ip,'callerid'=>$queryCID,'campaign_id'=>$campaign,'group_id'=>$exten,'user'=>$user,'lead_id'=>$lead_id]);
    }
}

if ($ACTION=="Event3wayLeave") {
    if ( (OSDstrlen($uniqueid)<2) or (OSDstrlen($server_ip)<1) or (OSDstrlen($queryCID)<1) or (OSDstrlen($campaign)<1) or (OSDstrlen($user)<1) or (OSDstrlen($lead_id)<1) or (OSDstrlen($exten)<1)) {
        echo "\nAction not sent\n";
    } else {
        osdevent($link,['event'=>'CALL_3WAY_LEAVE','uniqueid'=>$uniqueid,'server_ip'=>$server_ip,'callerid'=>$queryCID,'campaign_id'=>$campaign,'group_id'=>$exten,'user'=>$user,'lead_id'=>$lead_id]);
    }
}

if ($ACTION=="EventXferMessage") {
    if ( (OSDstrlen($uniqueid)<2) or (OSDstrlen($server_ip)<1) or (OSDstrlen($queryCID)<1) or (OSDstrlen($campaign)<1) or (OSDstrlen($user)<1) or (OSDstrlen($lead_id)<1) or (OSDstrlen($exten)<1)) {
        echo "\nAction not sent\n";
    } else {
        osdevent($link,['event'=>'CALL_XFER_MESSAGE','uniqueid'=>$uniqueid,'server_ip'=>$server_ip,'callerid'=>$queryCID,'campaign_id'=>$campaign,'user'=>$user,'lead_id'=>$lead_id,'data1'=>$exten]);
    }
}

######################
# ACTION=Redirect, RedirectName, RedirectNameVmail, RedirectToPark, RedirectFromPark, RedirectVD, RedirectXtra, RedirectXtraCX
# - insert Redirect Manager statement using extensions name
######################
if ($ACTION=="RedirectVD") {
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15) or (OSDstrlen($exten)<1) or (OSDstrlen($campaign)<1) or (OSDstrlen($ext_context)<1) or (OSDstrlen($ext_priority)<1) or (OSDstrlen($uniqueid)<2) or (OSDstrlen($lead_id)<1) ) {
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
        if (OSDstrlen($call_server_ip)>6) $server_ip = $call_server_ip;
        $stmt=sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_id='%s' AND campaign_allow_inbound='Y';",mres($campaign));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            $stmt=sprintf("UPDATE osdial_closer_log SET end_epoch='%s',length_in_sec='%s',status='XFER' WHERE lead_id='%s' ORDER BY start_epoch DESC LIMIT 1;",mres($StarTtime),mres($secondS),mres($lead_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }
        if ($auto_dial_level < 1) {
            $stmt=sprintf("UPDATE osdial_log SET end_epoch='%s',length_in_sec='%s',status='XFER' WHERE uniqueid='%s' AND server_ip='%s';",mres($StarTtime),mres($secondS),mres($uniqueid),mres($server_ip));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        } else {
            $stmt=sprintf("DELETE FROM osdial_auto_calls WHERE uniqueid='%s';",mres($uniqueid));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }
        $ACTION="Redirect";
    }
}

if ($ACTION=="RedirectToPark") {
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15) or (OSDstrlen($exten)<1) or (OSDstrlen($extenName)<1) or (OSDstrlen($ext_context)<1) or (OSDstrlen($ext_priority)<1) or (OSDstrlen($parkedby)<1) ) {
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
        if ($config['settings']['enable_queuemetrics_logging'] > 0) {
            $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
            mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
            if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

            $stmt=sprintf("SELECT time_id,queue,agent FROM queue_log WHERE call_id='%s' AND verb='CONNECT' ORDER BY time_id DESC LIMIT 1;",mres($CalLCID));
            $rslt=mysql_query($stmt, $linkB);
            $VAC_cn_ct = mysql_num_rows($rslt);
            if ($VAC_cn_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $time_id = $row[0];
                $queue = $row[1];
                $agent = $row[2];
            }
            $StarTtime = date("U");
            if ($time_id > 100000) {
                $secondS = ($StarTtime - $time_id);
            }

            if ($VAC_eq_ct > 0) {
                $stmt = sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='Agent/%s',verb='CALLERONHOLD',data1='PARK',serverid='%s';",mres($StarTtime),mres($CalLCID),mres($queue),mres($user),mres($queuemetrics_log_id));
                $rslt=mysql_query($stmt, $linkB);
                if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'02101',$user,$server_ip,$session_name,$one_mysql_log);}
                $affected_rows = mysql_affected_rows($linkB);
                if ($format=='debug') {echo "\n<!-- $affected_rows|$stmt -->";}
            }
        }

        if (OSDstrlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
        osdevent($link,['event'=>'CALL_ONHOLD','uniqueid'=>$uniqueid,'server_ip'=>$server_ip,'user'=>$user,'data1'=>$channel,'data2'=>$exten]);
        $stmt=sprintf("INSERT INTO parked_channels VALUES('%s','%s','','%s','%s','%s');",mres($channel),mres($server_ip),mres($extenName),mres($parkedby),mres($NOW_TIME));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $ACTION="Redirect";
    }
}

if ($ACTION=="RedirectFromPark") {
    if (OSDstrlen($server_dialstring)>0) $exten = $server_dialstring . $config['settings']['intra_server_sep'] . $exten;
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15) or (OSDstrlen($exten)<1) or (OSDstrlen($ext_context)<1) or (OSDstrlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "exten $exten must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nRedirectFromPark Action not sent\n";
    } else {
        $parked_sec=0;
        if ($config['settings']['enable_queuemetrics_logging'] > 0) {
            $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
            mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
            if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

            $stmt=sprintf("SELECT time_id,queue,agent FROM queue_log WHERE call_id='%s' AND verb='CONNECT' ORDER BY time_id DESC LIMIT 1;",mres($CalLCID));
            $rslt=mysql_query($stmt, $linkB);
            $VAC_cn_ct = mysql_num_rows($rslt);
            if ($VAC_cn_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $time_id = $row[0];
                $queue = $row[1];
                $agent = $row[2];
            }
            $StarTtime = date("U");
            if ($time_id > 100000) {
                $secondS = ($StarTtime - $time_id);
            }

            if ($VAC_eq_ct > 0) {
                $stmt = sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='Agent/%s',verb='CALLEROFFHOLD',data1='%s',serverid='%s';",mres($StarTtime),mres($CalLCID),mres($queue),mres($user),mres($parked_sec),mres($queuemetrics_log_id));
                $rslt=mysql_query($stmt, $linkB);
                if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'02101',$user,$server_ip,$session_name,$one_mysql_log);}
                $affected_rows = mysql_affected_rows($linkB);
                if ($format=='debug') {echo "\n<!-- $affected_rows|$stmt -->";}
            }
        }
        if (OSDstrlen($call_server_ip)>6) {$server_ip = $call_server_ip;}
        $Dsecs=0;
        $stmt=sprintf("SELECT UNIX_TIMESTAMP(event_time) FROM osdial_events WHERE event='CALL_ONHOLD' AND uniqueid='%s' AND server_ip='%s' LIMIT 1;",mres($uniqueid),mres($server_ip));
        $rslt=mysql_query($stmt, $link);
        $hold_ct = mysql_num_rows($rslt);
        if ($hold_ct>0) {
            $row=mysql_fetch_row($rslt);
            $Dsecs = Date("U") - $row[0];
            if ($Dsecs<1) $Dsecs=0;
        }
        osdevent($link,['event'=>'CALL_OFFHOLD','uniqueid'=>$uniqueid,'server_ip'=>$server_ip,'user'=>$user,'data1'=>$channel,'data2'=>$exten,'data3'=>$Dsecs]);
        $stmt=sprintf("DELETE FROM parked_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($channel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $ACTION="Redirect";
    }
}

if ($ACTION=="RedirectName") {
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15)  or (OSDstrlen($extenName)<1)  or (OSDstrlen($ext_context)<1)  or (OSDstrlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "extenName $extenName must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nRedirectName Action not sent\n";
    } else {
        $stmt=sprintf("SELECT SQL_NO_CACHE dialplan_number FROM phones WHERE server_ip='%s' AND extension='%s';",mres($server_ip),mres($extenName));
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
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15)  or (OSDstrlen($extenName)<1)  or (OSDstrlen($exten)<1)  or (OSDstrlen($ext_context)<1)  or (OSDstrlen($ext_priority)<1) ) {
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
        $stmt=sprintf("SELECT voicemail_id FROM phones WHERE server_ip='%s' AND extension='%s';",mres($server_ip),mres($extenName));
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


if ($ACTION=="RedirectXtraCXNeW") {
    $DBout='';
    $row='';
    $rowx='';
    $channel_liveX=1;
    $channel_liveY=1;
    if (OSDstrlen($channel)<3 or OSDstrlen($queryCID)<15 or OSDstrlen($ext_context)<1 or OSDstrlen($ext_priority)<1 or OSDstrlen($session_id)<3 or ((OSDstrlen($extrachannel)<3 or OSDstrlen($exten)<1) and !OSDpreg_match("/NEXTAVAILABLE/",$exten)) ) {
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
        if ($DBFILE and OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) debugLog("osdial_debug","$NOW_TIME|RDCXC|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|");
    } else {
        if (OSDpreg_match("/NEXTAVAILABLE/",$exten)) {
            $exten='';
            $stmtA=sprintf("SELECT count(*) FROM osdial_conferences WHERE server_ip='%s' AND (extension='' OR extension IS NULL) AND conf_exten!='%s';",mres($server_ip),mres($session_id));
            if ($format=='debug') echo "\n<!-- $stmtA -->";
            $rslt=mysql_query($stmtA, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0]>0) {
                $stmtB=sprintf("UPDATE osdial_conferences SET extension='%s',leave_3way='0' WHERE server_ip='%s' AND (extension='' OR extension IS NULL) AND conf_exten!='%s' LIMIT 1;",mres($protocol.'/'.$extension.$NOWnum),mres($server_ip),mres($session_id));
                if ($format=='debug') echo "\n<!-- $stmtB -->";
                $rslt=mysql_query($stmtB, $link);

                $stmtC=sprintf("SELECT conf_exten FROM osdial_conferences WHERE server_ip='%s' AND extension='%s' AND conf_exten!='%s';",mres($server_ip),mres($protocol.'/'.$extension.$NOWnum),mres($session_id));
                if ($format=='debug') echo "\n<!-- $stmtC -->";
                $rslt=mysql_query($stmtC, $link);
                $row=mysql_fetch_row($rslt);
                $exten = $row[0];

                if (OSDpreg_match("/^8300/",$extension) and $protocol == 'Local') $extension = "$extension$user";

                $stmtD=sprintf("UPDATE osdial_conferences SET leave_3way='1',leave_3way_datetime='%s',extension='3WAY_%s' WHERE server_ip='%s' AND conf_exten='%s';",mres($NOW_TIME),mres($user),mres($server_ip),mres($session_id));
                if ($format=='debug') echo "\n<!-- $stmtD -->";
                $rslt=mysql_query($stmtD, $link);

                $stmtE=sprintf("UPDATE osdial_conferences SET extension='%s' WHERE server_ip='%s' AND conf_exten='%s' LIMIT 1;",mres($protocol.'/'.$extension),mres($server_ip),mres($exten));
                if ($format=='debug') echo "\n<!-- $stmtE -->";
                $rslt=mysql_query($stmtE, $link);

                $stmtF=sprintf("SELECT channel_group FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($agentchannel));
                if ($format=='debug') echo "\n<!-- $stmtF -->";
                $rslt=mysql_query($stmtF, $link);
                $row=mysql_fetch_row($rslt);
                if (!empty($row[0])) $agentchannel = $row[0];

                $queryCID = "CXAR24$NOWnum";
                $stmtG=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: 1','CallerID: %s','','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($agentchannel),mres($ext_context),mres($exten),mres($queryCID));
                if ($format=='debug') echo "\n<!-- $stmtG -->";
                $rslt=mysql_query($stmtG, $link);

                $stmtH=sprintf("UPDATE osdial_live_agents SET conf_exten='%s' WHERE server_ip='%s' AND user='%s';",mres($exten),mres($server_ip),mres($user));
                if ($format=='debug') echo "\n<!-- $stmtH -->";
                $rslt=mysql_query($stmtH, $link);

                if ($auto_dial_level<1) {
                    $stmtI=sprintf("DELETE FROM osdial_auto_calls WHERE lead_id='%s' AND callerid LIKE 'M%%';",mres($lead_id));
                    if ($format=='debug') echo "\n<!-- $stmtI -->";
                    $rslt=mysql_query($stmtI, $link);
                }

                echo "NeWSessioN|$exten|\n";
                exit;
            } else {
                $channel_liveX=0;
                echo "Cannot find empty osdial_conference on $server_ip, Redirect command not inserted\n|$stmt|";
                if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Cannot find empty conference on $server_ip";
            }
        }

        if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;

        $stmt=sprintf("SELECT count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt=sprintf("SELECT count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            if ($rowx[0]==0) {
                 $channel_liveX=0;
                 echo "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                 if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $call_server_ip";
            }
        }
        $stmt=sprintf("SELECT count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($extrachannel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt=sprintf("SELECT count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($extrachannel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                 $channel_liveY=0;
                 echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                 if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $server_ip";
            }
        }

        if ($channel_liveX==1 && $channel_liveY==1) {
            $stmt=sprintf("SELECT count(*) FROM osdial_live_agents WHERE lead_id='%s' AND user!='%s';",mres($lead_id),mres($user));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0] < 1) {
                $channel_liveY=0;
                echo "No Local agent to send call to, Redirect command not inserted\n";
                if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "No Local agent to send call to";
            } else {
                $stmt=sprintf("SELECT server_ip,conf_exten,user FROM osdial_live_agents WHERE lead_id='%s' AND user!='%s';",mres($lead_id),mres($user));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                $dest_server_ip = $rowx[0];
                $dest_session_id = $rowx[1];
                $dest_user = $rowx[2];
                $S='*';

                $D_s_ip = explode('.', $dest_server_ip);
                if (OSDstrlen($D_s_ip[0])<2) $D_s_ip[0] = "0$D_s_ip[0]";
                if (OSDstrlen($D_s_ip[0])<3) $D_s_ip[0] = "0$D_s_ip[0]";
                if (OSDstrlen($D_s_ip[1])<2) $D_s_ip[1] = "0$D_s_ip[1]";
                if (OSDstrlen($D_s_ip[1])<3) $D_s_ip[1] = "0$D_s_ip[1]";
                if (OSDstrlen($D_s_ip[2])<2) $D_s_ip[2] = "0$D_s_ip[2]";
                if (OSDstrlen($D_s_ip[2])<3) $D_s_ip[2] = "0$D_s_ip[2]";
                if (OSDstrlen($D_s_ip[3])<2) $D_s_ip[3] = "0$D_s_ip[3]";
                if (OSDstrlen($D_s_ip[3])<3) $D_s_ip[3] = "0$D_s_ip[3]";
                $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]" . $config['settings']['intra_server_sep'] . "$dest_session_id$S$lead_id$S$dest_user$S$phone_code$S$phone_number$S$campaign$S";

                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','','','','','');",mres($NOW_TIME),mres($call_server_ip),mres($queryCID),mres($channel),mres($ext_context),mres($dest_dialstring),mres($ext_priority),mres($queryCID));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Hangup','%s','Channel: %s','','','','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($extrachannel),mres($queryCID),mres($extrachannel));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                echo "RedirectXtraCX command sent for Channel $channel on $call_server_ip and \nHungup $extrachannel on $server_ip\n";
                if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel on $call_server_ip, Hungup $extrachannel on $server_ip";
            }
        } else {
            if ($channel_liveX==1) {
                $ACTION="Redirect";
                $server_ip=$call_server_ip;
            }
            if ($channel_liveY==1) {
                $ACTION="Redirect";
                $channel=$extrachannel;
            }
            if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Changed to Redirect: $channel on $server_ip";
        }

        if ($DBFILE and OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) debugLog('osdial_debug', "$NOW_TIME|RDCXC|$filename|$user|$campaign|$DBout|");
    }
}

if ($ACTION=="RedirectXtraCX") {
    $DBout='';
    $row='';
    $rowx='';
    $channel_liveX=1;
    $channel_liveY=1;
    if ((OSDstrlen($channel)<3 or OSDstrlen($queryCID)<15 or OSDstrlen($exten)<1 or OSDstrlen($ext_context)<1 or OSDstrlen($ext_priority)<1 or OSDstrlen($extrachannel)<3) and !OSDpreg_match("/NEXTAVAILABLE/",$exten)) {
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
        if ($DBFILE and OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) debugLog('osdial_debug',"$NOW_TIME|RDCXC|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|");
    } else {
        if (OSDpreg_match("/NEXTAVAILABLE/",$exten)) {
            $stmt=sprintf("UPDATE osdial_conferences SET extension='%s',leave_3way='0' WHERE server_ip='%s' AND (extension='' OR extension IS NULL) AND conf_exten!='%s' LIMIT 1;",mres($protocol.'/'.$extension.$NOWnum),mres($server_ip),mres($session_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            if ($affected_rows > 0) {
                $stmt=sprintf("SELECT SQL_NO_CACHE conf_exten FROM osdial_conferences WHERE server_ip='%s' AND extension='%s' AND conf_exten!='%s';",mres($server_ip),mres($protocol.'/'.$extension.$NOWnum),mres($session_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $exten = $row[0];

                $stmt=sprintf("SELECT SQL_NO_CACHE channel_group FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($agentchannel));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                if (!empty($row[0])) $agentchannel = $row[0];

                if ( (OSDpreg_match("/^8300/",$extension)) and ($protocol == 'Local') ) $extension = "$extension$user";

                $stmt=sprintf("UPDATE osdial_conferences SET extension='%s' WHERE server_ip='%s' AND conf_exten='%s' LIMIT 1;",mres($protocol.'/'.$extension),mres($server_ip),mres($exten));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("UPDATE osdial_conferences SET leave_3way='1',leave_3way_datetime='%s',extension='3WAY_%s' WHERE server_ip='%s' AND conf_exten='%s';",mres($NOW_TIME),mres($user),mres($server_ip),mres($session_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $queryCID = "CXAR24$NOWnum";
                $stmtB=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: 1','CallerID: %s','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($agentchannel),mres($ext_context),mres($exten),mres($queryCID),mres($queryCID));
                if ($format=='debug') echo "\n<!-- $stmtB -->";
                $rslt=mysql_query($stmtB, $link);

                $stmt=sprintf("UPDATE osdial_live_agents SET conf_exten='%s' WHERE server_ip='%s' AND user='%s';",mres($exten),mres($server_ip),mres($user));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                if ($auto_dial_level < 1) {
                    $stmt=sprintf("DELETE FROM osdial_auto_calls WHERE lead_id='%s' AND callerid LIKE 'M%%';",mres($lead_id));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                }

                echo "NeWSessioN|$exten|\n";
                echo "|$stmtB|\n";

                if ($DBFILE) debugLog('osdial_debug',"$NOW_TIME|RDCXCNA|$filename|$user-$NOWnum|$campaign|$agentchannel|$exten|");
                exit;

            } else {
                $channel_liveX=0;
                echo "Cannot find empty conference on $server_ip, Redirect command not inserted\n|$stmt|";
                if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Cannot find empty conference on $server_ip";
            }

        }

        if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;

        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                $channel_liveX=0;
                echo "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $call_server_ip";
            }    
        }
        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($extrachannel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' and channel='%s';",mres($server_ip),mres($extrachannel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                $channel_liveY=0;
                echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $server_ip";
            }    
        }
        if ( ($channel_liveX==1) && ($channel_liveY==1) ) {
            $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_live_agents WHERE lead_id='%s' AND user!='%s';",mres($lead_id),mres($user));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0] < 1) {
                $channel_liveY=0;
                echo "No Local agent to send call to, Redirect command not inserted\n";
                if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "No Local agent to send call to";
            } else {
                $stmt=sprintf("SELECT SQL_NO_CACHE server_ip,conf_exten,user FROM osdial_live_agents WHERE lead_id='%s' AND user!='%s';",mres($lead_id),mres($user));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                $dest_server_ip = $rowx[0];
                $dest_session_id = $rowx[1];
                $dest_user = $rowx[2];
                $S='*';

                $D_s_ip = explode('.', $dest_server_ip);
                if (OSDstrlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                if (OSDstrlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                if (OSDstrlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                if (OSDstrlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                if (OSDstrlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                if (OSDstrlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                if (OSDstrlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                if (OSDstrlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$config[settings][intra_server_sep]$dest_session_id$S$lead_id$S$dest_user$S$phone_code$S$phone_number$S$campaign$S";

                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','Account: %s','','','','');",mres($NOW_TIME),mres($call_server_ip),mres($queryCID),mres($channel),mres($ext_context),mres($dest_dialstring),mres($ext_priority),mres($queryCID),mres($queryCID));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Hangup','%s','Channel: %s','','','','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($extrachannel),mres($queryCID),mres($extrachannel));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                echo "RedirectXtraCX command sent for Channel $channel on $call_server_ip and \nHungup $extrachannel on $server_ip\n";
                if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel on $call_server_ip, Hungup $extrachannel on $server_ip";
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
            if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Changed to Redirect: $channel on $server_ip";
        }

        if ($DBFILE and OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) debugLog('osdial_debug',"$NOW_TIME|RDCXC|$filename|$user|$campaign|$DBout|");
    }
}






if ($ACTION=="RedirectXtraNeW") {
    if ($channel==$extrachannel) {
        $ACTION="Redirect";
    } else {
        $row='';
        $rowx='';
        $channel_liveX=1;
        $channel_liveY=1;
        if (OSDstrlen($channel)<3 or OSDstrlen($queryCID)<15 or OSDstrlen($ext_context)<1 or OSDstrlen($ext_priority)<1 or OSDstrlen($session_id)<3 or ((OSDstrlen($extrachannel)<3 or OSDstrlen($exten)<1) and !OSDpreg_match("/NEXTAVAILABLE/",$exten))) {
            $channel_liveX=0;
            $channel_liveY=0;
            echo "One of these variables is not valid:\n";
            echo "Channel $channel must be greater than 2 characters\n";
            echo "ExtraChannel $extrachannel must be greater than 2 characters\n";
            echo "queryCID $queryCID must be greater than 14 characters\n";
            echo "exten $exten must be set\n";
            echo "ext_context $ext_context must be set\n";
            echo "ext_priority $ext_priority must be set\n";
            echo "session_id $session_id must be set\n";
            echo "\nRedirect Action not sent\n";
            if ($DBFILE and OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) debugLog('osdial_debug', "$NOW_TIME|RDX|$filename|$user|$campaign|$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|$session_id|");
        } else {
            if (OSDpreg_match("/NEXTAVAILABLE/",$exten)) {
                $exten='';
                $stmtA=sprintf("SELECT count(*) FROM osdial_conferences WHERE server_ip='%s' AND (extension='' OR extension IS NULL) AND conf_exten!='%s';",mres($server_ip),mres($session_id));
                if ($format=='debug') echo "\n<!-- $stmtA -->";
                $rslt=mysql_query($stmtA, $link);
                $row=mysql_fetch_row($rslt);
                if ($row[0]>0) {
                    $stmtB=sprintf("UPDATE osdial_conferences SET extension='%s',leave_3way='0' WHERE server_ip='%s' AND (extension='' OR extension IS NULL) AND conf_exten!='%s' LIMIT 1;",mres($protocol.'/'.$extension.$NOWnum),mres($server_ip),mres($session_id));
                    if ($format=='debug') echo "\n<!-- $stmtB -->";
                    $rslt=mysql_query($stmtB, $link);

                    $stmtC=sprintf("SELECT conf_exten FROM osdial_conferences WHERE server_ip='%s' AND extension='%s' AND conf_exten!='%s';",mres($server_ip),mres($protocol.'/'.$extension.$NOWnum),mres($session_id));
                    if ($format=='debug') echo "\n<!-- $stmtC -->";
                    $rslt=mysql_query($stmtC, $link);
                    $row=mysql_fetch_row($rslt);
                    $exten = $row[0];

                    $stmtD=sprintf("UPDATE osdial_conferences SET leave_3way='1',leave_3way_datetime='%s',extension='3WAY_%s' WHERE server_ip='%s' AND conf_exten='%s';",mres($NOW_TIME),mres($user),mres($server_ip),mres($session_id));
                    if ($format=='debug') echo "\n<!-- $stmtD -->";
                    $rslt=mysql_query($stmtD, $link);

                    $stmtE=sprintf("UPDATE osdial_conferences SET extension='%s' WHERE server_ip='%s' AND conf_exten='%s' LIMIT 1;",mres($protocol.'/'.$extension),mres($server_ip),mres($exten));
                    if ($format=='debug') echo "\n<!-- $stmtE -->";
                    $rslt=mysql_query($stmtE, $link);

                    $stmtF=sprintf("SELECT SQL_NO_CACHE channel_group FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($agentchannel));
                    if ($format=='debug') echo "\n<!-- $stmtF -->";
                    $rslt=mysql_query($stmtF, $link);
                    $row=mysql_fetch_row($rslt);
                    if (!empty($row[0])) $agentchannel = $row[0];

                    $queryCID = "CXAR23$NOWnum";
                    $stmtG=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: 7%s','Priority: 1','CallerID: %s','','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($agentchannel),mres($ext_context),mres($exten),mres($queryCID));
                    if ($format=='debug') echo "\n<!-- $stmtG -->";
                    $rslt=mysql_query($stmtG, $link);

                    $stmtH=sprintf("UPDATE osdial_live_agents SET conf_exten='%s' WHERE server_ip='%s' AND user='%s';",mres($exten),mres($server_ip),mres($user));
                    if ($format=='debug') echo "\n<!-- $stmtH -->";
                    $rslt=mysql_query($stmtH, $link);

                    if ($auto_dial_level<1) {
                        $stmtI=sprintf("DELETE FROM osdial_auto_calls WHERE lead_id='%s' AND callerid LIKE 'M%%';",mres($lead_id));
                        if ($format=='debug') echo "\n<!-- $stmtI -->";
                        $rslt=mysql_query($stmtI, $link);
                    }

                    echo "NeWSessioN|$exten|\n";
                    echo "|$stmtA|$stmtB|$stmtC|$stmtD|$stmtE|$stmtF|$stmtG|$stmtH|$stmtI|\n";
                    exit;

                } else {
                    $channel_liveX=0;
                    echo "Cannot find empty osdial_conference on $server_ip, Redirect command not inserted\n|$stmt|";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Cannot find empty conference on $server_ip";
                }
            }

            if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;

            $stmt=sprintf("SELECT count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0]==0 and !OSDpreg_match("/SECOND/",$filename)) {
                $stmt=sprintf("SELECT count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                if ($rowx[0]==0) {
                    $channel_liveX=0;
                    echo "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $call_server_ip";
                }
            }
            $stmt=sprintf("SELECT count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($extrachannel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0]==0 and !OSDpreg_match("/SECOND/",$filename)) {
                $stmt=sprintf("SELECT count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($extrachannel));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                if ($rowx[0]==0) {
                    $channel_liveY=0;
                    echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $server_ip";
                }
            }
            if ($channel_liveX==1 and $channel_liveY==1) {
                if ($server_ip==$call_server_ip or OSDstrlen($call_server_ip)<7) {
                    $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','ExtraChannel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($channel),mres($extrachannel),mres($ext_context),mres($exten),mres($ext_priority),mres($queryCID));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    echo "RedirectXtra command sent for Channel $channel and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel and $extrachannel to $exten on $server_ip";
                } else {
                    $S='*';
                    $D_s_ip = explode('.', $server_ip);
                    if (OSDstrlen($D_s_ip[0])<2) $D_s_ip[0] = "0$D_s_ip[0]";
                    if (OSDstrlen($D_s_ip[0])<3) $D_s_ip[0] = "0$D_s_ip[0]";
                    if (OSDstrlen($D_s_ip[1])<2) $D_s_ip[1] = "0$D_s_ip[1]";
                    if (OSDstrlen($D_s_ip[1])<3) $D_s_ip[1] = "0$D_s_ip[1]";
                    if (OSDstrlen($D_s_ip[2])<2) $D_s_ip[2] = "0$D_s_ip[2]";
                    if (OSDstrlen($D_s_ip[2])<3) $D_s_ip[2] = "0$D_s_ip[2]";
                    if (OSDstrlen($D_s_ip[3])<2) $D_s_ip[3] = "0$D_s_ip[3]";
                    if (OSDstrlen($D_s_ip[3])<3) $D_s_ip[3] = "0$D_s_ip[3]";
                    $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$config[settings][intra_server_sep]$exten";

                    $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','','','','','');",mres($NOW_TIME),mres($call_server_ip),mres($queryCID),mres($channel),mres($ext_context),mres($dest_dialstring),mres($ext_priority),mres($queryCID));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($extrachannel),mres($ext_context),mres($exten),mres($ext_priority),mres($queryCID));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    echo "RedirectXtra command sent for Channel $channel on $call_server_ip and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel/$call_server_ip and $extrachannel/$server_ip to $exten";
                }
            } else {
                if ($channel_liveX==1) {
                    $ACTION="Redirect";
                    $server_ip = $call_server_ip;
                }
                if ($channel_liveY==1) {
                    $ACTION="Redirect";
                    $channel=$extrachannel;
                }
            }

            if ($DBFILE and OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) debugLog('osdial_debug', "$NOW_TIME|RDX|$filename|$user|$campaign|$DBout|");
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
        if ((OSDstrlen($channel)<3 or OSDstrlen($queryCID)<15 or OSDstrlen($exten)<1 or OSDstrlen($ext_context)<1 or OSDstrlen($ext_priority)<1 or OSDstrlen($extrachannel)<3) and !OSDpreg_match("/NEXTAVAILABLE/",$exten)) {
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
            if ($DBFILE and OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) debugLog('osdial_debug',"$NOW_TIME|RDX|$filename|$user|$campaign|$$channel|$extrachannel|$queryCID|$exten|$ext_context|ext_priority|");
        } else {
            if (OSDpreg_match("/NEXTAVAILABLE/",$exten)) {
                $stmt=sprintf("UPDATE osdial_conferences SET extension='%s',leave_3way='0' WHERE server_ip='%s' AND (extension='' OR extension IS NULL) AND conf_exten!='%s' LIMIT 1;",mres($protocol.'/'.$extension.$NOWnum),mres($server_ip),mres($session_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                if ($affected_rows > 0) {
                    $stmt=sprintf("SELECT SQL_NO_CACHE conf_exten FROM osdial_conferences WHERE server_ip='%s' AND extension='%s' AND conf_exten!='%s';",mres($server_ip),mres($protocol.'/'.$extension.$NOWnum),mres($session_id));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $exten = $row[0];

                    $stmt=sprintf("SELECT SQL_NO_CACHE channel_group FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($agentchannel));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    if (!empty($row[0])) $agentchannel = $row[0];

                    $stmt=sprintf("UPDATE osdial_conferences SET extension='%s' WHERE server_ip='%s' AND conf_exten='%s' LIMIT 1;",mres($protocol.'/'.$extension),mres($server_ip),mres($exten));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    $stmt=sprintf("UPDATE osdial_conferences SET leave_3way='1',leave_3way_datetime='%s',extension='3WAY_%s' WHERE server_ip='%s' AND conf_exten='%s';",mres($NOW_TIME),mres($user),mres($server_ip),mres($session_id));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    $queryCID = "CXAR23$NOWnum";
                    $stmtB=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: 1','CallerID: %s','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($agentchannel),mres($ext_context),mres($exten),mres($queryCID),mres($queryCID));
                    if ($format=='debug') echo "\n<!-- $stmtB -->";
                    $rslt=mysql_query($stmtB, $link);

                    $stmt=sprintf("UPDATE osdial_live_agents SET conf_exten='%s' WHERE server_ip='%s' AND user='%s';",mres($exten),mres($server_ip),mres($user));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    if ($auto_dial_level < 1) {
                        $stmt=sprintf("DELETE FROM osdial_auto_calls WHERE lead_id='%s' AND callerid LIKE 'M%%';",mres($lead_id));
                        if ($format=='debug') echo "\n<!-- $stmt -->";
                        $rslt=mysql_query($stmt, $link);
                    }

                    echo "NeWSessioN|$exten|\n";
                    echo "|$stmtB|\n";

                    if ($DBFILE) debugLog('osdial_debug',"$NOW_TIME|RDXNA|$filename|$user-$NOWnum|$campaign|$agentchannel|$exten|");
                    exit;

                } else {
                    $channel_liveX=0;
                    echo "Cannot find empty conference on $server_ip, Redirect command not inserted\n|$stmt|";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "Cannot find empty conference on $server_ip";
                }
            }

            if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;

            $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ( ($row[0]==0) && (!OSDpreg_match("/SECOND/",$filename)) ) {
                $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($call_server_ip),mres($channel));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                if ($rowx[0]==0) {
                    $channel_liveX=0;
                    echo "Channel $channel is not live on $call_server_ip, Redirect command not inserted\n";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $call_server_ip";
                }    
            }
            $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($extrachannel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ( ($row[0]==0) && (!OSDpreg_match("/SECOND/",$filename)) ) {
                $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($extrachannel));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
                $rowx=mysql_fetch_row($rslt);
                if ($rowx[0]==0) {
                    $channel_liveY=0;
                    echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel is not live on $server_ip";
                }    
            }
            if ( ($channel_liveX==1) && ($channel_liveY==1) ) {
                if ( ($server_ip=="$call_server_ip") or (OSDstrlen($call_server_ip)<7) ) {
                    $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','ExtraChannel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','Account: %s','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($channel),mres($extrachannel),mres($ext_context),mres($exten),mres($ext_priority),mres($queryCID),mres($queryCID));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    echo "RedirectXtra command sent for Channel $channel and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel and $extrachannel to $exten on $server_ip";
                } else {
                    $S='*';
                    $D_s_ip = explode('.', $server_ip);
                    if (OSDstrlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                    if (OSDstrlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                    if (OSDstrlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                    if (OSDstrlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                    if (OSDstrlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                    if (OSDstrlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                    if (OSDstrlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                    if (OSDstrlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                    $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$config[settings][intra_server_sep]$exten";

                    $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','Account: %s','','','','');",mres($NOW_TIME),mres($call_server_ip),mres($queryCID),mres($channel),mres($ext_context),mres($dest_dialstring),mres($ext_priority),mres($queryCID),mres($queryCID));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($extrachannel),mres($ext_context),mres($exten),mres($ext_priority),mres($queryCID),mres($queryCID));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    echo "RedirectXtra command sent for Channel $channel on $call_server_ip and \nExtraChannel $extrachannel\n to $exten on $server_ip\n";
                    if (OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) $DBout .= "$channel/$call_server_ip and $extrachannel/$server_ip to $exten";
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

            if ($DBFILE and OSDpreg_match("/SECOND|FIRST|DEBUG/",$filename)) debugLog('osdial_debug',"$NOW_TIME|RDX|$filename|$user|$campaign|$DBout|");
        }
    }
}

if ($ACTION=="Redirect") {
    ### for manual dial OSDIAL calls send the second attempt to transfer the call
    if ($stage=="2NDXfeR") {
        $local_DEF = 'Local/';
        $local_AMP = '@';
        $hangup_channel_prefix = "$local_DEF$session_id$local_AMP$ext_context";

        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s%%';",mres($server_ip),mres($hangup_channel_prefix));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row > 0) {
            $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s%%';",mres($server_ip),mres($hangup_channel_prefix));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            $channel=$rowx[0];
            $channel = OSDpreg_replace("/1$/","2",$channel);
            $queryCID = OSDpreg_replace("/^./","Q",$queryCID);
        }
    }

    $row='';
    $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15)  or (OSDstrlen($exten)<1)  or (OSDstrlen($ext_context)<1)  or (OSDstrlen($ext_priority)<1) ) {
        $channel_live=0;
        echo "One of these variables is not valid:\n";
        echo "Channel $channel must be greater than 2 characters\n";
        echo "queryCID $queryCID must be greater than 14 characters\n";
        echo "exten $exten must be set\n";
        echo "ext_context $ext_context must be set\n";
        echo "ext_priority $ext_priority must be set\n";
        echo "\nRedirect Action not sent\n";
    } else {
        if (OSDstrlen($call_server_ip)>6) $server_ip = $call_server_ip;
        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($channel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                $channel_live=0;
                echo "Channel $channel is not live on $server_ip, Redirect command not inserted\n";
            }    
        }
        if ($channel_live==1) {
            if (OSDstrlen($outbound_cid)>1) {
                $outCID = "\"$outbound_cid_name\" <$outbound_cid>";
                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Setvar','%s','Channel: %s','Variable: CALLERID(all)','Value: %s','Account: %s','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($channel),mres($outCID),mres($queryCID));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            } else {
                $outCID = $queryCID;
            }
            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Redirect','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','CallerID: %s','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($channel),mres($ext_context),mres($exten),mres($ext_priority),mres($outCID),mres($queryCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            echo "Redirect command sent for Channel $channel on $server_ip\n";
            if ($DBFILE) debugLog('osdial_debug',"$NOW_TIME|RD|$queryCID|$channel|$exten|$ext_context|$outCID|");
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
    if ( (OSDstrlen($channel)<3) or (OSDstrlen($queryCID)<15) or (OSDstrlen($filename)<2) ) {
        $channel_live=0;
        echo "Channel $channel is not valid or queryCID $queryCID is not valid or filename: $filename is not valid, $ACTION command not inserted\n";
    } else {
        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($channel));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0]==0) {
            $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM live_sip_channels WHERE server_ip='%s' AND channel='%s';",mres($server_ip),mres($channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $rowx=mysql_fetch_row($rslt);
            if ($rowx[0]==0) {
                $channel_live=0;
                echo "Channel $channel is not live on $server_ip, $ACTION command not inserted\n";
            }    
        }
        if ($channel_live==1) {
            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','%s','%s','Channel: %s','%s','Account: %s','','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($ACTION),mres($queryCID),mres($channel),mres($SQLfile),mres($queryCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            if ($ACTION=="Monitor") {
                $stmt=sprintf("INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename,lead_id,user,uniqueid) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s')",mres($channel),mres($server_ip),mres($exten),mres($NOW_TIME),mres($StarTtime),mres($filename),mres($lead_id),mres($user),mres($uniqueid));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("SELECT SQL_NO_CACHE recording_id FROM recording_log WHERE filename='%s';",mres($filename));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $row=mysql_fetch_row($rslt);
                $recording_id = $row[0];
            } else {
                $stmt=sprintf("SELECT SQL_NO_CACHE recording_id,start_epoch FROM recording_log WHERE filename='%s';",mres($filename));
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

                    $stmt=sprintf("UPDATE recording_log SET end_time='%s',end_epoch='%s',length_in_sec='%s',length_in_min='%s',uniqueid='%s' WHERE filename='%s';",mres($NOW_TIME),mres($StarTtime),mres($length_in_sec),mres($length_in_min),mres($uniqueid),mres($filename));
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
    if ( (OSDstrlen($exten)<3) or (OSDstrlen($channel)<4) or (OSDstrlen($filename)<5) ) {
        $channel_live=0;
        echo "Channel $channel is not valid or exten $exten is not valid or filename: $filename is not valid, $ACTION command not inserted\n";
    } else {

        if ($ACTION=="MonitorConf") {
            $CIDdate = date("mdHis");
            $PADlead_id = sprintf("%09s", $lead_id);
            while (OSDstrlen($PADlead_id) > 9) $PADlead_id = OSDsubstr($PADlead_id, 0, -1);
            $queryCID = "W$CIDdate$PADlead_id";

            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Originate','%s','Channel: %s','Context: %s','Exten: %s','Priority: %s','Callerid: %s','Account: %s','Variable: FILENAME=%s','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($channel),mres($ext_context),mres($exten),mres($ext_priority),mres($queryCID),mres($queryCID),mres($filename));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("INSERT INTO recording_log (channel,server_ip,extension,start_time,start_epoch,filename,lead_id,user,uniqueid) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s');",mres($channel),mres($server_ip),mres($exten),mres($NOW_TIME),mres($StarTtime),mres($filename),mres($lead_id),mres($user),mres($uniqueid));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("SELECT SQL_NO_CACHE recording_id FROM recording_log WHERE filename='%s';",mres($filename));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $row=mysql_fetch_row($rslt);
            $recording_id = $row[0];
        } else {
            $stmt=sprintf("SELECT SQL_NO_CACHE recording_id,start_epoch FROM recording_log WHERE filename='%s';",mres($filename));
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

                $stmt=sprintf("UPDATE recording_log SET end_time='%s',end_epoch='%s',length_in_sec='%s',length_in_min='%s',unqiueid='%s' WHERE filename='%s';",mres($NOW_TIME),mres($StarTtime),mres($length_in_sec),mres($length_in_min),mres($uniqueid),mres($filename));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
            }

            # find and hang up all recordings going on in this conference # and extension = '$exten' 
            $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s%%' AND (channel LIKE '%%,1' OR channel LIKE '%%,2' OR channel LIKE '%%;1' OR channel LIKE '%%;2');",mres($server_ip),mres($channel));
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
                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Hangup','RH12345%s','Channel: %s','','','','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($HUchannel[$i]),mres($StarTtime.$i),mres($HUchannel[$i]));
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
    if ( (OSDstrlen($exten)<1) or (OSDstrlen($channel)<1) or (OSDstrlen($stage)<1) or (OSDstrlen($queryCID)<1) ) {
        echo "Conference $exten, Stage $stage is not valid or queryCID $queryCID is not valid, Originate command not inserted\n";
    } else {
        $stmt='';
        $participant_number='XXYYXXYYXXYYXX';
        if (OSDpreg_match('/MUTING|UNMUTE/',$stage)) {
            $vol_cmd='mute';
            if (OSDpreg_match('/UNMUTE/',$stage)) $vol_cmd='unmute';
            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Command','%s','Command: %s','','','','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres('meetme '.$vol_cmd.' '.$exten.' '.$participant_number));
        } elseif (OSDpreg_match('/UP|DOWN/',$stage)) {
            $vol_cmd='T';
            if (OSDpreg_match('/DOWN/',$stage)) $vol_cmd='t';
            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Originate','%s','Data: %s','Application: %s','Channel: %s','Callerid: %s','Account: %s','','','','%s','%s');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($exten.','.$vol_cmd.','.$participant_number),mres('MeetMeAdmin'),mres('Local/8307@osdial/n'),mres($queryCID),mres($queryCID),mres($channel),mres($exten));
        }

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





