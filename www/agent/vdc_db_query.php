<?php
# vdc_db_query.php
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
#  - $ACTION - ('regCLOSER','manDiaLnextCALL','manDiaLskip','manDiaLonly','manDiaLlookCALL','manDiaLlogCALL','userLOGout','updateDISPO','VDADpause','VDADready','VDADcheckINCOMING','CalLBacKLisT','CalLBacKCounT','PauseCodeSubmit','LogiNCamPaigns')
#  - $stage - ('start','finish','lookup','new')
#  - $closer_choice - ('CL_TESTCAMP_L CL_OUT123_L -')
#  - $conf_exten - ('8600011',...)
#  - $exten - ('123test',...)
#  - $ext_context - ('default','demo',...)
#  - $ext_priority - ('1','2',...)
#  - $campaign - ('testcamp',...)
#  - $dial_timeout - ('60','26',...)
#  - $dial_prefix - ('9','8',...)
#  - $campaign_cid - ('3125551212','0000000000',...)
#  - $MDnextCID - ('M06301413000000002',...)
#  - $uniqueid - ('1120232758.2406800',...)
#  - $lead_id - ('36524',...)
#  - $list_id - ('101','123456',...)
#  - $length_in_sec - ('12',...)
#  - $phone_code - ('1',...)
#  - $phone_number - ('3125551212',...)
#  - $channel - ('Zap/12-1',...)
#  - $start_epoch - ('1120236911',...)
#  - $vendor_lead_code - ('1234test',...)
#  - $title - ('Mr.',...)
#  - $first_name - ('Bob',...)
#  - $middle_initial - ('L',...)
#  - $last_name - ('Wilson',...)
#  - $address1 - ('1324 Main St.',...)
#  - $address2 - ('Apt. 12',...)
#  - $address3 - ('co Robert Wilson',...)
#  - $city - ('Chicago',...)
#  - $state - ('IL',...)
#  - $province - ('NA',...)
#  - $postal_code - ('60054',...)
#  - $country_code - ('USA',...)
#  - $gender - ('M',...)
#  - $date_of_birth - ('1970-01-01',...)
#  - $alt_phone - ('3125551213',...)
#  - $email - ('bob@bob.com',...)
#  - $custom1 - ('Hello',...)
#  - $comments - ('Good Customer',...)
#  - $auto_dial_level - ('0','1','1.2',...)
#  - $VDstop_rec_after_each_call - ('0','1')
#  - $conf_silent_prefix - ('7','8','',...)
#  - $extension - ('123','user123','25-1',...)
#  - $protocol - ('Zap','SIP','IAX2',...)
#  - $user_abb - ('1234','6666',...)
#  - $preview - ('YES','NO',...)
#  - $called_count - ('0','1','2',...)
#  - $agent_log_id - ('123456',...)
#  - $agent_log - ('NO',...)
#  - $favorites_list - (",'cc160','cc100'",...)
#  - $CallBackDatETimE - ('2006-04-21 14:30:00',...)
#  - $recipient - ('ANYONE,'USERONLY')
#  - $callback_id - ('12345','12346',...)
#  - $use_internal_dnc - ('Y','N')
#  - $omit_phone_code - ('Y','N')
#  - $no_delete_sessions - ('0','1')
#  - $LogouTKicKAlL - ('0','1');

# CHANGELOG:
# 50629-1044 - First build of script
# 50630-1422 - Added manual dial action and MD channel lookup
# 50701-1451 - Added dial log for start and end of osdial calls
# 50705-1239 - Added call disposition update
# 50804-1627 - Fixed updateDispo to update osdial_log entry
# 50816-1605 - Added VDADpause/ready for auto dialing
# 50816-1811 - Added basic autodial call pickup functions
# 50817-1005 - Altered logging functions to accomodate auto_dialing
# 50818-1305 - Added stop-all-recordings-after-each-osdial-call option
# 50818-1411 - Added hangup of agent phone after Logout
# 50901-1315 - Fixed CLOSER IN-GROUP Web Form bug
# 50902-1507 - Fixed CLOSER log length_in_sec bug
# 50902-1730 - Added functions for manual preview dialing and revert
# 50913-1214 - Added agent random update to leadupdate
# 51020-1421 - Added agent_log_id framework for detailed agent activity logging
# 51021-1717 - Allows for multi-line comments (changes \n to !N in database)
# 51111-1046 - Added osdial_agent_log lead_id earlier for manual dial
# 51121-1445 - Altered echo statements for several small PHP speed optimizations
# 51122-1328 - Fixed UserLogout issue not removing conference reservation
# 51129-1012 - Added ability to accept calls from other OSDIAL servers
# 51129-1729 - Changed manual dial to use the '/n' flag for calls
# 51221-1154 - Added SCRIPT id lookup and sending to osdial.php for display
# 60105-1059 - Added Updating of osdial favorites in the DB
# 60208-1617 - Added dtmf buttons output per call
# 60213-1521 - Added closer_campaigns update to osdial_users
# 60215-1036 - Added Callback date-time entry into osdial_callbacks table
# 60413-1541 - Added USERONLY Callback listings output - CalLBacKLisT
#            - Added USERONLY Callback count output - CalLBacKCounT
# 60414-1140 - Added Callback lead lookup for manual dialing
# 60419-1517 - After CALLBK is sent to agent, update callback record to INACTIVE
# 60421-1419 - Check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60427-1236 - Fixed closer_choice error for CLOSER campaigns
# 60609-1148 - Added ability to check for manual dial numbers in DNC
# 60619-1117 - Added variable filters to close security holes for login form
# 60623-1414 - Fixed variable filter for phone_code and fixed manual dial logic
# 60821-1600 - Added ability to omit the phone code on osdial lead dialing
# 60821-1647 - Added ability to not delete sessions at logout
# 60906-1124 - Added lookup and sending of callback data for CALLBK calls
# 61128-2229 - Added osdial_live_agents and osdial_auto_calls manual dial entries
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70115-1733 - Added alt_dial functionality in auto-dial modes
# 70118-1501 - Added user_group to osdial_log,_agent_log,_closer_log,_callbacks
# 70123-1357 - Fixed bug that would not update osdial_closer_log status to dispo
# 70202-1438 - Added pause code submit function
# 70203-0930 - Added dialed_number to lead info output
# 70203-1030 - Added dialed_label to lead info output
# 70206-1126 - Added INBOUND status for inbound/closer calls in osdial_live_agents
# 70212-1253 - Fixed small issue with CXFER
# 70213-1431 - Added QueueMetrics PAUSE/UNPAUSE/AGENTLOGIN/AGENTLOGOFF actions
# 70214-1231 - Added queuemetrics_log_id field for server_id in queue_log
# 70215-1210 - Added queuemetrics COMPLETEAGENT action
# 70216-1051 - Fixed double call complete queuemetrics logging
# 70222-1616 - Changed queue_log PAUSE/UNPAUSE to PAUSEALL/UNPAUSEALL
# 70309-1034 - Allow amphersands and questions marks in comments to pass through
# 70313-1052 - Allow pound signs(hash) in comments to pass through
# 70319-1544 - Added agent disable update customer data function
# 70322-1545 - Added sipsak display ability
# 70413-1253 - Fixed bug for outbound call time in CLOSER-type blended campaigns
# 70424-1100 - Fixed bug for fronter/closer calls that would delete vdac records
# 70802-1729 - Fixed bugs with pause_sec and wait_sec under certain call handling 
# 70828-1443 - Added source_id to output of SCRIPTtab-IFRAME and WEBFORM
# 71029-1855 - removed campaign_id naming restrictions for CLOSER-type campaigns
# 71030-2047 - added hopper priority for auto alt dial entries
# 71116-1011 - added calls_today count updating of the osdial_live_agents upon INCALL
# 71120-1520 - added LogiNCamPaigns to show only allowed campaigns for agents upon login
# 71125-1751 - Added inbound-group default inbound group sending to osdial.php
# 71129-2025 - restricted callbacks count and list to campaign only
# 71226-1117 - added option to kick all calls from conference upon logout
# 80128-1159 - fixed osdial_log bugs
# 80402-0121 - Fixes for manual dial transfers on some systems, removed /n persist flag
# 80424-0442 - Added non_latin lookup from system_settings
#
# 090410-1159 - Added custom2 field
# 090410-1744 - Added allow_tab_switch
# 090428-0936 - Added external_key

# The version/build variables get set to the SVN revision automatically in release package.
# Do not change.
$version = 'SVN_Version';
$build = 'SVN_Build';

header('Cache-Control: public, no-cache, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() - 60)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

$DB = 0;
$DBFILE=0;

require_once("dbconnect.php");
require_once('functions.php');

### If you have globals turned off uncomment these lines
$ACTION = get_variable("ACTION");
$address1 = get_variable("address1");
$address2 = get_variable("address2");
$address3 = get_variable("address3");
$agent_log = get_variable("agent_log");
$agent_log_id = get_variable("agent_log_id");
$agent_log_type = get_variable("agent_log_type");
$agent_log_time = get_variable("agent_log_time");
$agentchannel = get_variable("agentchannel");
$agentserver_ip = get_variable("agentserver_ip");
$alt_dial = get_variable("alt_dial");
$alt_num_status = get_variable("alt_num_status");
$alt_phone = get_variable("alt_phone");
$auto_dial_level = get_variable("auto_dial_level");
$blind_transfer = get_variable("blind_transfer");
$callback_id = get_variable("callback_id");
$CallBackDatETimE = get_variable("CallBackDatETimE");
$called_count = get_variable("called_count");
$campaign = get_variable("campaign");
$campaign_cid = get_variable("campaign_cid");
$campaign_cid_name = get_variable("campaign_cid_name");
$channel = get_variable("channel");
$city = get_variable("city");
$closer_choice = get_variable("closer_choice");
$company_id = get_variable("company_id");
$company_prefix = get_variable("company_prefix");
$comments = get_variable("comments");
$conf_dialed = get_variable("conf_dialed");
$conf_exten = get_variable("conf_exten");
$conf_silent_prefix = get_variable("conf_silent_prefix");
$country_code = get_variable("country_code");
$custom1 = get_variable("custom1");
$custom2 = get_variable("custom2");
$date_of_birth = get_variable("date_of_birth");
$dial_context = get_variable("dial_context");
$dial_timeout = get_variable("dial_timeout");
$dial_prefix = get_variable("dial_prefix");
$dispo_choice = get_variable("dispo_choice");
$email = get_variable("email");
$enable_sipsak_messages = get_variable("enable_sipsak_messages");
$et_id = get_variable("et_id");
$ext_context = get_variable("ext_context");
$ext_priority = get_variable("ext_priority");
$exten = get_variable("exten");
$extension = get_variable("extension");
$favorites_list = get_variable("favorites_list");
$first_name = get_variable("first_name");
$format = get_variable("format");
$gender = get_variable("gender");
$hopper_id = get_variable("hopper_id");
$hopper_add = get_variable("hopper_add");
$hangup_all_non_reserved = get_variable("hangup_all_non_reserved");
$inOUT = get_variable("inOUT");
$inbound_man = get_variable("inbound_man");
$last_name = get_variable("last_name");
$lead_id = get_variable("lead_id");
$length_in_sec = get_variable("length_in_sec");
$leaving_threeway = get_variable("leaving_threeway");
$list_id = get_variable("list_id");
$LogouTKicKAlL = get_variable("LogouTKicKAlL");
$lookup = get_variable("lookup");
$MDnextCID = get_variable("MDnextCID");
$middle_initial = get_variable("middle_initial");
$multicall_agentlogid = get_variable("multicall_agentlogid");
$multicall_agentlogtype = get_variable("multicall_agentlogtype");
$multicall_agentlogtime = get_variable("multicall_agentlogtime");
$multicall_channel = get_variable("multicall_channel");
$multicall_serverip = get_variable("multicall_serverip");
$multicall_uniqueid = get_variable("multicall_uniqueid");
$multicall_callerid = get_variable("multicall_callerid");
$multicall_leadid = get_variable("multicall_leadid");
$multicall_liveseconds = get_variable("multicall_liveseconds");
$nodeletevdac = get_variable("nodeletevdac");
$oldlead = get_variable("oldlead");
$oldphone = get_variable("oldphone");
$omit_phone_code = get_variable("omit_phone_code");
$organization = get_variable("organization");
$organization_title = get_variable("organization_title");
$park_on_extension = get_variable("park_on_extension");
$pass = get_variable("pass");
$phone_code = get_variable("phone_code");
$phone_ip = get_variable("phone_ip");
$phone_number = get_variable("phone_number");
$phone_local_gmt = get_variable("phone_local_gmt");
$phone_gmt = get_variable("phone_gmt");
$postal_code = get_variable("postal_code");
$PostDatETimE = get_variable("PostDatETimE");
$mdnFlag = get_variable("mdnFlag");
$preview = get_variable("preview");
$protocol = get_variable("protocol");
$province = get_variable("province");
$recipient = get_variable("recipient");
$script_id = get_variable("script_id");
$script_button_id = get_variable("script_button_id");
$server_ip = get_variable("server_ip");
$status_extended = get_variable("status_extended");
$session_name = get_variable("session_name");
$stage = get_variable("stage");
$start_epoch = get_variable("start_epoch");
$state = get_variable("state");
$status = get_variable("status");
$title = get_variable("title");
$uniqueid = get_variable("uniqueid");
$use_internal_dnc = get_variable("use_internal_dnc");
$user = get_variable("user");
$user_group = get_variable("user_group");
$user_abb = get_variable("user_abb");
$VDstop_rec_after_each_call = get_variable("VDstop_rec_after_each_call");
$vendor_lead_code = get_variable("vendor_lead_code");
$wrapup = get_variable("wrapup");


#if ($config['settings']['use_non_latin'] < 1) {
#    $user=OSDpreg_replace("/[^0-9a-zA-Z]/","",$user);
#    $pass=OSDpreg_replace("/[^0-9a-zA-Z]/","",$pass);
#}

$length_in_sec = OSDpreg_replace("/[^0-9]/","",$length_in_sec);
if (empty($length_in_sec)) $length_in_sec=0;
$phone_code = OSDpreg_replace("/[^0-9]/","",$phone_code);
$phone_number = OSDpreg_replace("/[^0-9]/","",$phone_number);

# default optional vars if not set
if (empty($format)) $format="text";
if ($format=='debug') $DB=1;
if (empty($ACTION)) $ACTION="refresh";

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");
if (!isset($query_date)) $query_date = $NOW_DATE;
$MT[0]='';
$agents='@agents';

if ($ACTION == 'LogiNCamPaigns') {
    $skip_user_validation=1;
} else {
    $stmt = sprintf("SELECT count(*) FROM osdial_users WHERE user='%s' AND pass='%s' AND user_level>0;",mres($user),mres($pass));
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
}

if ($format=='debug') {
    echo "<html>\n";
    echo "<head>\n";
    echo "<!-- VERSION: $version     BUILD: $build    USER: $user   server_ip: $server_ip-->\n";
    echo "<title>OSDiaL Database Query Script";
    echo "</title>\n";
    echo "</head>\n";
    echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}



################################################################################
### LogiNCamPaigns - generates an HTML SELECT list of allowed campaigns for a 
###                  specific agent on the login screen
################################################################################
if ($ACTION == 'LogiNCamPaigns') {
    if ( (OSDstrlen($user)<1) ) {
        echo "<select size=1 name=VD_campaign id=VD_campaign onfocus=\"login_focus();\">\n";
        echo "<option value=\"\">-- ERROR --</option>\n";
        echo "</select>\n";
        exit;
    } else {

        $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' AND pass='%s';",mres($user),mres($pass));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $VU_user_group=$row[0];

        $LOGallowed_campaignsSQL='';

        $stmt=sprintf("SELECT allowed_campaigns FROM osdial_user_groups WHERE user_group='%s';",mres($VU_user_group));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ( (!OSDpreg_match("/ALL-CAMPAIGNS/",$row[0])) ) {
            $LOGallowed_campaignsSQL = mres(OSDpreg_replace('/ -/','',$row[0]));
            $LOGallowed_campaignsSQL = OSDpreg_replace('/ /',"','",$LOGallowed_campaignsSQL);
            $LOGallowed_campaignsSQL = sprintf("AND campaign_id IN('%s')",$LOGallowed_campaignsSQL);
        }

        if ($config['settings']['enable_multicompany'] > 0) {
            $stmt=sprintf("SELECT count(*) FROM osdial_companies WHERE id='%s' AND status='ACTIVE';",mres(((OSDsubstr($user,0,3) * 1) - 100)) );
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] < 1) {
                echo "<select size=1 name=VD_campaign id=VD_campaign onfocus=\"login_focus();\">\n";
                echo "<option value=\"\">-- ERROR --</option>\n";
                echo "</select>\n";
                exit;
            }

            $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE active='Y' AND campaign_id LIKE '%s__%%' %s ORDER BY campaign_id;",mres(OSDsubstr($user,0,3)),$LOGallowed_campaignsSQL);
        } else {
            $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE active='Y' %s ORDER BY campaign_id;",$LOGallowed_campaignsSQL);
        }
        $rslt=mysql_query($stmt, $link);
        $camps_to_print = mysql_num_rows($rslt);

        echo "<select style=\"font-size:8pt;\" size=1 name=VD_campaign id=VD_campaign>\n";
        echo "<option value=\"\">-- PLEASE SELECT A CAMPAIGN --</option>\n";

        $o=0;
        while ($camps_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $csel=''; if ($rowx[0] == $campaign) $csel = ' selected';
            echo "<option value=\"$rowx[0]\" $csel>" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
            $o++;
        }
        echo "</select>\n";
        exit;
    }
}



################################################################################
### regCLOSER - update the osdial_live_agents table to reflect the closer
###             inbound choices made upon login
################################################################################
if ($ACTION == 'regCLOSER') {
    $row='';
    $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($closer_choice)<1) || (OSDstrlen($user)<1) ) {
        $channel_live=0;
        echo "Group Choice $closer_choice is not valid\n";
        exit;
    } else {
        $stmt=sprintf("SELECT closer_campaigns,xfer_agent2agent FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $row=mysql_fetch_row($rslt);
        $closer_campaigns =$row[0];
        $xfer_agent2agent =$row[1];

        if ($closer_choice == "MGRLOCK-") $closer_choice = $closer_campaigns;
        if ($xfer_agent2agent > 0) $closer_choice = rtrim($closer_choice,'-') . "A2A_$user -";

        $random = (rand(1000000, 9999999) + 10000000);
        $stmt=sprintf("UPDATE osdial_live_agents SET closer_campaigns='%s',random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($closer_choice),mres($random),mres($user),mres($server_ip));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_live_inbound_agents WHERE user='%s';",mres($user));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        $in_groups_pre = OSDpreg_replace('/-$/','',$closer_choice);
        $in_groups = explode(" ",$in_groups_pre);
        $in_groups_ct = count($in_groups);
        $k=1;
        while ($k < $in_groups_ct) {
            if (OSDstrlen($in_groups[$k])>1) {
                $stmt=sprintf("SELECT group_weight,calls_today FROM osdial_inbound_group_agents WHERE user='%s' AND group_id='%s';",mres($user),mres($in_groups[$k]));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $viga_ct = mysql_num_rows($rslt);
                if (OSDpreg_match('/^A2A_/',$in_groups[$k])) {
                    $group_weight = 10;
                    $calls_today  = $row[1];
                } elseif ($viga_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $group_weight = $row[0];
                    $calls_today  = $row[1];
                } else {
                    $group_weight = 0;
                    $calls_today  = 0;
                }
                $stmt=sprintf("INSERT INTO osdial_live_inbound_agents set user='%s',group_id='%s',group_weight='%s',calls_today='%s',last_call_time='%s',last_call_finish='%s';",mres($user),mres($in_groups[$k]),mres($group_weight),mres($calls_today),mres($NOW_TIME),mres($NOW_TIME));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }
            $k++;
        }
    }
    echo "Closer In Group Choice $closer_choice has been registered to user $user\n";
}


################################################################################
### manDiaLnextCALL - for manual OSDiaL dialing this will grab the next lead
###                   in the campaign, reserve it, send data back to client and
###                   place the call by inserting into osdial_manager
################################################################################
if ($ACTION == 'manDiaLnextCaLL') {
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($conf_exten)<1) || (OSDstrlen($campaign)<1)  || (OSDstrlen($ext_context)<1) ) {
        $channel_live=0;
        echo "HOPPER EMPTY\n";
        echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
        exit;
    } else {

        ##### grab number of calls today in this campaign and increment
        $stmt=sprintf("SELECT SQL_NO_CACHE calls_today FROM osdial_live_agents WHERE user='%s' AND campaign_id='%s';",mres($user),mres($campaign));
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $vla_cc_ct = mysql_num_rows($rslt);
        if ($vla_cc_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $calls_today =$row[0];
        } else {
            $calls_today ='0';
        }
        $calls_today++;

        ### check if this is a hopper list lead, if it is, skip the grabbing of a new lead and update the hopper table.
        if ( (OSDstrlen($callback_id)==0) and (OSDstrlen($lead_id)>0) ) {
            $affected_rows=1;

            $stmt=sprintf("UPDATE osdial_hopper SET status='QUEUE',user='%s' WHERE campaign_id='%s' AND lead_id='%s';",mres($user),mres($campaign),mres($lead_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

        ### check if this is a callback, if it is, skip the grabbing of a new lead and mark the callback as INACTIVE
        } else if ( (OSDstrlen($callback_id)>0) and (OSDstrlen($lead_id)>0) ) {
            $affected_rows=1;
            $CBleadIDset=1;

            $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE callback_id='%s';",mres($callback_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
        } else {
            if (OSDstrlen($phone_number)>3) {
                if ($use_internal_dnc=='Y') {
                    $dncs=0;
                    $dncsskip=0;

                    if ($config['settings']['enable_multicompany'] > 0) {
                        $dnc_method='';
                        $stmt=sprintf("SELECT id,dnc_method FROM osdial_companies WHERE id='%s';",mres(((OSDsubstr($user,0,3) * 1) - 100)));
                        $rslt=mysql_query($stmt, $link);
                        if ($DB) echo "$stmt\n";
                        $row=mysql_fetch_row($rslt);
                        $comp_id=$row[0];
                        $dnc_method=$row[1];

                        if (OSDpreg_match('/COMPANY|BOTH/',$dnc_method)) {
                            $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_dnc_company WHERE company_id='%s' AND phone_number='%s';",mres($comp_id),mres($phone_number));
                            $rslt=mysql_query($stmt, $link);
                            if ($DB) echo "$stmt\n";
                            $row=mysql_fetch_row($rslt);
                            $dncs+=$row[0];
                        }

                        if (OSDpreg_match('/COMPANY/',$dnc_method)) $dncsskip++;
                    }

                    if ($dncsskip==0) {
                        $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_dnc WHERE phone_number='%s';",mres($phone_number));
                        $rslt=mysql_query($stmt, $link);
                        if ($DB) echo "$stmt\n";
                        $row=mysql_fetch_row($rslt);
                        $dncs+=$row[0];
                    }

                    if ($dncs > 0) {
                        echo "DNC NUMBER\n";
                        exit;
                    }
                }
                if ($stage=='lookup') {
                    $stmt=sprintf("SELECT SQL_NO_CACHE lead_id FROM osdial_list JOIN osdial_lists ON (osdial_list.list_id=osdial_lists.list_id) WHERE campaign_id='%s' AND phone_number='%s' ORDER BY modify_date DESC LIMIT 1;",mres($campaign),mres($phone_number));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $man_leadID_ct = mysql_num_rows($rslt);
                    if ($man_leadID_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $affected_rows=1;
                        $lead_id =$row[0];
                        $CBleadIDset=1;
                    } else {
                        ### insert a new lead in the system with this phone number
                        $stmt=sprintf("INSERT INTO osdial_list SET phone_code='%s',phone_number='%s',list_id='%s',status='QUEUE',user='%s',called_since_last_reset='Y',entry_date='%s',last_local_call_time='%s';",mres($phone_code),mres($phone_number),mres($list_id),mres($user),mres($ENTRYdate),mres($NOW_TIME));
                        if ($DB) echo "$stmt\n";
                        $rslt=mysql_query($stmt, $link);
                        $affected_rows = mysql_affected_rows($link);
                        $lead_id = mysql_insert_id($link);
                        $CBleadIDset=1;
                    }
                } else {
                    ### insert a new lead in the system with this phone number
                    $stmt=sprintf("INSERT INTO osdial_list SET phone_code='%s',phone_number='%s',list_id='%s',status='QUEUE',user='%s',called_since_last_reset='Y',entry_date='%s',last_local_call_time='%s';",mres($phone_code),mres($phone_number),mres($list_id),mres($user),mres($ENTRYdate),mres($NOW_TIME));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);
                    $lead_id = mysql_insert_id($link);
                    $CBleadIDset=1;
                }
            } else {
                $stmt=sprintf("SELECT manual_dial_new_limit FROM osdial_users WHERE user='%s';",mres($user));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $mdn_user_ct = mysql_num_rows($rslt);
                if ($mdn_user_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $mdn_limit =$row[0];
                }
                $stmt=sprintf("SELECT SQL_NO_CACHE manual_dial_new_today FROM osdial_campaign_agent_stats WHERE user='%s' AND campaign_id='%s';",mres($user),mres($campaign));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $mdn_user_ct = mysql_num_rows($rslt);
                if ($mdn_user_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $mdn_today =$row[0];
                }
                ### grab the next lead in the hopper for this campaign and reserve it for the user
                if ($mdn_limit < 0 or $mdn_today <= $mdn_limit) {
                    $stmt=sprintf("UPDATE osdial_hopper SET status='QUEUE',user='%s' WHERE campaign_id='%s' AND status IN ('API','READY') ORDER BY status DESC, priority DESC, hopper_id LIMIT 1;",mres($user),mres($campaign));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);
                } else {
                    $stmt=sprintf("SELECT SQL_NO_CACHE hopper_id FROM osdial_hopper AS oh,osdial_list AS ol WHERE oh.lead_id=ol.lead_id AND oh.campaign_id='%s' AND oh.status IN('API','READY') AND ol.status!='NEW' ORDER BY oh.status DESC,oh.priority DESC,hopper_id LIMIT 1;",mres($campaign));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $mdn_hopper_ct = mysql_num_rows($rslt);
                    if ($mdn_hopper_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $hopper_id =$row[0];

                        $stmt=sprintf("UPDATE osdial_hopper SET status='QUEUE',user='%s' WHERE hopper_id='%s';",mres($user),mres($hopper_id));
                        if ($DB) echo "$stmt\n";
                        $rslt=mysql_query($stmt, $link);
                        $affected_rows = mysql_affected_rows($link);
                    }
                }
            }
        }

        if ($affected_rows > 0) {
            if (!$CBleadIDset) {
                ##### grab the lead_id of the reserved user in osdial_hopper
                $stmt=sprintf("SELECT SQL_NO_CACHE lead_id FROM osdial_hopper WHERE campaign_id='%s' AND status='QUEUE' AND user='%s' LIMIT 1;",mres($campaign),mres($user));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $hopper_leadID_ct = mysql_num_rows($rslt);
                if ($hopper_leadID_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $lead_id =$row[0];
                }
            }

            ##### grab the data from osdial_list for the lead_id
            $stmt=sprintf("SELECT SQL_NO_CACHE * FROM osdial_list where lead_id='%s' LIMIT 1;",mres($lead_id));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $list_lead_ct = mysql_num_rows($rslt);
            if ($list_lead_ct > 0) {
                $row=mysql_fetch_row($rslt);
                #$lead_id		= trim($row[0]);
                $dispo			= trim($row[3]);
                $tsr			= trim($row[4]);
                $vendor_id		= trim($row[5]);
                $source_id		= trim($row[6]);
                $list_id		= trim($row[7]);
                $gmt_offset_now	= trim($row[8]);
                $phone_code		= trim($row[10]);
                $phone_number	= trim($row[11]);
                $title			= trim($row[12]);
                $first_name		= trim($row[13]);
                $middle_initial	= trim($row[14]);
                $last_name		= trim($row[15]);
                $address1		= trim($row[16]);
                $address2		= trim($row[17]);
                $address3		= trim($row[18]);
                $city			= trim($row[19]);
                $state			= trim($row[20]);
                $province		= trim($row[21]);
                $postal_code	= trim($row[22]);
                $country_code	= trim($row[23]);
                $gender			= trim($row[24]);
                $date_of_birth	= trim($row[25]);
                $alt_phone		= trim($row[26]);
                $email			= trim($row[27]);
                $custom1		= trim($row[28]);
                $comments		= trim($row[29]);
                $called_count	= trim($row[30]);
                $custom2		= trim($row[31]);
                $external_key	= trim($row[32]);
                $post_date  	= trim($row[35]);
                $organization  	= trim($row[36]);
                $organization_title = trim($row[37]);
            }

            $called_count++;

            if ($config['settings']['enable_agc_xfer_log'] > 0) {
                #	DATETIME|campaign|lead_id|phone_number|user|type
                #	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|M
                if ($DBFILE) debugLog('xfer_log',"$NOW_TIME|$campaign|$lead_id|$phone_number|$user|M");
            }

            ##### if lead is a callback, grab the callback comments
            $CBentry_time =		'';
            $CBcallback_time =	'';
            $CBuser =			'';
            $CBcomments =		'';
            if (OSDpreg_match("/CALLBK/",$dispo)) {
                $stmt=sprintf("SELECT SQL_NO_CACHE entry_time,callback_time,user,comments FROM osdial_callbacks WHERE lead_id='%s' ORDER BY callback_id DESC LIMIT 1;",mres($lead_id));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $cb_record_ct = mysql_num_rows($rslt);
                if ($cb_record_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $CBentry_time =		trim($row[0]);
                    $CBcallback_time =	trim($row[1]);
                    $CBuser =			trim($row[2]);
                    $CBcomments =		trim($row[3]);
                }
            }

            if ($hopper_leadID_ct > 0) {
                if (OSDpreg_match("/NEW/",$dispo)) {
                    $stmt=sprintf("UPDATE osdial_campaign_agent_stats SET manual_dial_new_today=manual_dial_new_today+1 WHERE user='%s' AND campaign_id='%s';",mres($user),mres($campaign));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                }
            }

            $LLCT_DATE_offset = ($config['server']['local_gmt'] - $gmt_offset_now);
            $LLCT_DATE = date("Y-m-d H:i:s", mktime(date("H")-$LLCT_DATE_offset,date("i"),date("s"),date("m"),date("d"),date("Y")));

            if (OSDpreg_match('/Y/',$called_since_last_reset)) {
                $called_since_last_reset = OSDpreg_replace('/Y/','',$called_since_last_reset);
                if (OSDstrlen($called_since_last_reset) < 1) $called_since_last_reset = 0;
                $called_since_last_reset++;
                $called_since_last_reset = "Y$called_since_last_reset";
            } else {
                $called_since_last_reset = 'Y';
            }
            ### flag the lead as called and change it's status to INCALL
            $stmt=sprintf("UPDATE osdial_list SET status='INCALL',called_since_last_reset='%s',called_count='%s',user='%s',last_local_call_time='%s' WHERE lead_id='%s';",mres($called_since_last_reset),mres($called_count),mres($user),mres($LLCT_DATE),mres($lead_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            if (!$CBleadIDset) {
                ### delete the lead from the hopper
                $stmt=sprintf("DELETE FROM osdial_hopper WHERE lead_id='%s';",mres($lead_id));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
            }

            $stmt=sprintf("UPDATE osdial_agent_log SET lead_id='%s',comments='MANUAL',prev_status='%s',lead_called_count='%s' WHERE agent_log_id='%s';",mres($lead_id),mres($dispo),mres($called_count),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            ### if preview dialing, do not send the call	
            if ( (OSDstrlen($preview)<1) || ($preview == 'NO') ) {
                ### prepare variables to place manual call from OSDiaL
                $CCID_on=0;
                $CCID='';
                $CCID_NAME='';
                $local_DEF = 'Local/';
                $local_AMP = '@';
                $Local_out_prefix = '9';
                $Local_dial_timeout = '60';
                $Local_persist = '';
                if ($dial_timeout > 4) $Local_dial_timeout = $dial_timeout;
                $Local_dial_timeout = ($Local_dial_timeout * 1000);
                if (OSDstrlen($dial_prefix) > 0) $Local_out_prefix = $dial_prefix;
                if (OSDstrlen($campaign_cid) > 6) {
                    $CCID = $campaign_cid;
                    $CCID_NAME = $campaign_cid_name;
                    $CCID_on++;
                }
                if (OSDpreg_match("/x/i",$dial_prefix)) $Local_out_prefix = '';

                $PADlead_id = sprintf("%09s", $lead_id);
                while (OSDstrlen($PADlead_id) > 9) $PADlead_id = OSDsubstr($PADlead_id, 0, -1);

                # Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
                $MqueryCID = "M$CIDdate$PADlead_id";
                if ($CCID_on) {
                    $CIDstring = "\"$CCID_NAME\" <$CCID>";
                } else {
                    $CIDstring = "\"\" <0000000000>";
                }

                $int_prefix = '';
                if ($phone_code != '1' and !OSDpreg_match('/^0/',$phone_code)) $int_prefix='011';
                
                ### whether to omit phone_code or not
                if (OSDpreg_match('/Y/i',$omit_phone_code)) {
                    $Ndialstring = "$Local_out_prefix$phone_number";
                } else {
                    $Ndialstring = "$Local_out_prefix$int_prefix$phone_code$phone_number";
                }

                ### insert the call action into the osdial_manager table to initiate the call
                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Originate','%s','Exten: %s','Context: %s','Channel: %s','Priority: 1','Callerid: %s','Timeout: %s','Account: %s','','','');",mres($NOW_TIME),mres($server_ip),mres($MqueryCID),mres($Ndialstring),mres($dial_context),mres($local_DEF.$conf_exten.$local_AMP.$ext_context.$Local_persist),mres($CIDstring),mres($Local_dial_timeout),mres($MqueryCID));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("INSERT INTO osdial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) VALUES('%s','%s','XFER','%s','%s','%s','%s','%s','OUT');",mres($server_ip),mres($campaign),mres($lead_id),mres($MqueryCID),mres($phone_code),mres($phone_number),mres($NOW_TIME));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                ### update the agent status to INCALL in osdial_live_agents
                $random = (rand(1000000, 9999999) + 10000000);
                $stmt=sprintf("UPDATE osdial_live_agents SET status='INCALL',last_call_time='%s',callerid='%s',lead_id='%s',comments='MANUAL',calls_today='%s',random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($NOW_TIME),mres($MqueryCID),mres($lead_id),mres($calls_today),mres($random),mres($user),mres($server_ip));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                ### update calls_today count in osdial_campaign_agents
                $stmt=sprintf("UPDATE osdial_campaign_agents SET calls_today='%s' WHERE user='%s' AND campaign_id='%s';",mres($calls_today),mres($user),mres($campaign));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                if ($mdnFlag=="fast" || $preview=="NO") {
                    $pause_sec=0;
                    $stmt=sprintf("SELECT pause_epoch FROM osdial_agent_log WHERE agent_log_id='%s';",mres($agent_log_id));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $VDpr_ct = mysql_num_rows($rslt);
                    if ($VDpr_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $pause_sec = ($StarTtime - $row[0]);
                    }
                    $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_epoch='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($StarTtime),mres($agent_log_id));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);
                }
            }

            $comments = OSDpreg_replace("/\r/",'',$comments);
            $comments = OSDpreg_replace("/\n/",'!N',$comments);

            $phone_code = OSDpreg_replace('/^011|^010|^00/', '', $phone_code);

            $LeaD_InfO =	$MqueryCID . "\n";
            $LeaD_InfO .=	$lead_id . "\n";
            $LeaD_InfO .=	$dispo . "\n";
            $LeaD_InfO .=	$tsr . "\n";
            $LeaD_InfO .=	$vendor_id . "\n";
            $LeaD_InfO .=	$list_id . "\n";
            $LeaD_InfO .=	$gmt_offset_now . "\n";
            $LeaD_InfO .=	$phone_code . "\n";
            $LeaD_InfO .=	$phone_number . "\n";
            $LeaD_InfO .=	$title . "\n";
            $LeaD_InfO .=	$first_name . "\n";
            $LeaD_InfO .=	$middle_initial . "\n";
            $LeaD_InfO .=	$last_name . "\n";
            $LeaD_InfO .=	$address1 . "\n";
            $LeaD_InfO .=	$address2 . "\n";
            $LeaD_InfO .=	$address3 . "\n";
            $LeaD_InfO .=	$city . "\n";
            $LeaD_InfO .=	$state . "\n";
            $LeaD_InfO .=	$province . "\n";
            $LeaD_InfO .=	$postal_code . "\n";
            $LeaD_InfO .=	$country_code . "\n";
            $LeaD_InfO .=	$gender . "\n";
            $LeaD_InfO .=	$date_of_birth . "\n";
            $LeaD_InfO .=	$alt_phone . "\n";
            $LeaD_InfO .=	$email . "\n";
            $LeaD_InfO .=	$custom1 . "\n";
            $LeaD_InfO .=	$comments . "\n";
            $LeaD_InfO .=	$called_count . "\n";
            $LeaD_InfO .=	$CBentry_time . "\n";
            $LeaD_InfO .=	$CBcallback_time . "\n";
            $LeaD_InfO .=	$CBuser . "\n";
            $LeaD_InfO .=	$CBcomments . "\n";
            $LeaD_InfO .=	$phone_number . "\n";
            $LeaD_InfO .=	"MAIN\n";
            $LeaD_InfO .=	$source_id . "\n";
            $LeaD_InfO .=	$custom2 . "\n";
            $LeaD_InfO .=	$external_key . "\n";
            $LeaD_InfO .=	$post_date . "\n";
            $LeaD_InfO .=	$organization . "\n";
            $LeaD_InfO .=	$organization_title . "\n";

            $web_form_address = "";
            $web_form_address2 = "";
            $web_form_extwindow = "";
            $web_form2_extwindow = "";
            $campaign_script = "";

            # Get web_form_address vars from campaign.
            $stmt=sprintf("SELECT web_form_address,web_form_address2,web_form_extwindow,web_form2_extwindow,campaign_script FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $list_cnt = mysql_num_rows($rslt);
            if ($list_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                $web_form_address = $row[0];
                $web_form_address2 = $row[1];
                $web_form_extwindow = $row[2];
                $web_form2_extwindow = $row[3];
                $campaign_script = $row[4];
            }

            # Get web_form_address vars from list.
            $stmt=sprintf("SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='%s';",mres($list_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $list_cnt = mysql_num_rows($rslt);
            if ($list_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                if (!empty($row[0])) $web_form_address = $row[0];
                if (!empty($row[1])) $web_form_address2 = $row[1];
                if (!empty($row[2])) $campaign_script = $row[2];
            }

            # Get script override from user.
            $stmt=sprintf("SELECT script_override FROM osdial_users WHERE user='%s';",mres($user));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $user_cnt = mysql_num_rows($rslt);
            if ($user_cnt > 0) {
                $row=mysql_fetch_row($rslt);
                if (!empty($row[0])) $campaign_script = $row[0];
            }

            $LeaD_InfO .=	$web_form_address . "\n";
            $LeaD_InfO .=	$web_form_address2 . "\n";
            $LeaD_InfO .=	$web_form_extwindow . "\n";
            $LeaD_InfO .=	$web_form2_extwindow . "\n";
            $LeaD_InfO .=	$campaign_script . "\n";

            $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
            $cnt = 0;
            foreach ($forms as $form) {
                $fcamps = OSDpreg_split('/,/',$form['campaigns']);
                foreach ($fcamps as $fcamp) {
                    if ($fcamp == 'ALL' or OSDstrtoupper($fcamp) == OSDstrtoupper($campaign)) {
                        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', sprintf("deleted='0' AND form_id='%s'",mres($form['id'])) );
                        if (is_array($fields)) {
                            foreach ($fields as $field) {
                                $vdlf = get_first_record($link, 'osdial_list_fields', '*', sprintf("lead_id='%s' AND field_id='%s'",mres($lead_id),mres($field['id'])) );
                                $LeaD_InfO .= $vdlf['value'] . "\n";
                                $cnt++;
                            }
                        }
                    }
                }
            }
            echo $LeaD_InfO;

        } else {
            echo "HOPPER EMPTY\n";
        }
    }
}


################################################################################
### manDiaLskip - for manual OSDiaL dialing this skips the lead that was
###               previewed in the step above and puts it back in orig status
################################################################################
if ($ACTION == 'manDiaLskip') {
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($stage)<1) || (OSDstrlen($called_count)<1) || (OSDstrlen($lead_id)<1) ) {
        $channel_live=0;
        echo "LEAD NOT REVERTED\n";
        echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
        exit;
    } else {
        $called_count = ($called_count - 1);
        ### flag the lead as called and change it's status to INCALL
        $stmt=sprintf("UPDATE osdial_list SET status='%s',called_count='%s',user='%s' WHERE lead_id='%s';",mres($stage),mres($called_count),mres($user),mres($lead_id));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);

        echo "LEAD REVERTED\n";
    }
}


################################################################################
### manDiaLonly - for manual OSDiaL dialing this sends the call that was
###               previewed in the step above
################################################################################
if ($ACTION == 'manDiaLonly') {
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($conf_exten)<1) || (OSDstrlen($campaign)<1) || (OSDstrlen($ext_context)<1) || (OSDstrlen($phone_number)<1) || (OSDstrlen($lead_id)<1) ) {
        $channel_live=0;
        echo " CALL NOT PLACED\n";
        echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
        exit;
    } else {
        ##### grab number of calls today in this campaign and increment
        $stmt=sprintf("SELECT calls_today FROM osdial_live_agents WHERE user='%s' AND campaign_id='%s';",mres($user),mres($campaign));
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $vla_cc_ct = mysql_num_rows($rslt);
        if ($vla_cc_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $calls_today =$row[0];
        } else {
            $calls_today ='0';
        }
        $calls_today++;

        ### prepare variables to place manual call from OSDiaL
        $CCID_on=0;
        $CCID='';
        $CCID_NAME='';
        $local_DEF = 'Local/';
        $local_AMP = '@';
        $Local_out_prefix = '9';
        $Local_dial_timeout = '60';
        $Local_persist = '/n';
        if ($dial_timeout > 4) $Local_dial_timeout = $dial_timeout;
        $Local_dial_timeout = ($Local_dial_timeout * 1000);
        if (OSDstrlen($dial_prefix) > 0) $Local_out_prefix = $dial_prefix;
        if (OSDstrlen($campaign_cid) > 6) {
            $CCID = $campaign_cid;
            $CCID_NAME = $campaign_cid_name;
            $CCID_on++;
        }
        if (OSDpreg_match("/x/i",$dial_prefix)) $Local_out_prefix = '';

        $PADlead_id = sprintf("%09s", $lead_id);
        while (OSDstrlen($PADlead_id) > 9) $PADlead_id = OSDsubstr($PADlead_id, 0, -1);

        # Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
        $MqueryCID = "M$CIDdate$PADlead_id";
        if ($CCID_on) {
            $CIDstring = "\"$CCID_NAME\" <$CCID>";
        } else {
            $CIDstring = "\"\" <0000000000>";
        }

        $int_prefix = '';
        if ($phone_code != '1' and !OSDpreg_match('/^0/',$phone_code)) $int_prefix='011';

        ### whether to omit phone_code or not
        if (OSDpreg_match('/Y/i',$omit_phone_code)) {
            $Ndialstring = "$Local_out_prefix$phone_number";
        } else {
            $Ndialstring = "$Local_out_prefix$int_prefix$phone_code$phone_number";
        }
        ### insert the call action into the osdial_manager table to initiate the call
        $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Originate','%s','Exten: %s','Context: %s','Channel: %s','Priority: 1','Callerid: %s','Timeout: %s','Account: %s','','','');",mres($NOW_TIME),mres($server_ip),mres($MqueryCID),mres($Ndialstring),mres($dial_context),mres($local_DEF.$conf_exten.$local_AMP.$ext_context.$Local_persist),mres($CIDstring),mres($Local_dial_timeout),mres($MqueryCID));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("INSERT INTO osdial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) VALUES('%s','%s','XFER','%s','%s','%s','%s','%s','OUT')",mres($server_ip),mres($campaign),mres($lead_id),mres($MqueryCID),mres($phone_code),mres($phone_number),mres($NOW_TIME));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);

        ### update the agent status to INCALL in osdial_live_agents
        $random = (rand(1000000, 9999999) + 10000000);
        $stmt=sprintf("UPDATE osdial_live_agents set status='INCALL',last_call_time='%s',callerid='%s',lead_id='%s',comments='MANUAL',calls_today='%s',random_id='%s' where user='%s' and server_ip='%s';",mres($NOW_TIME),mres($MqueryCID),mres($lead_id),mres($calls_today),mres($random),mres($user),mres($server_ip));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("UPDATE osdial_campaign_agents SET calls_today='%s' WHERE user='%s' AND campaign_id='%s';",mres($calls_today),mres($user),mres($campaign));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);

        $pause_sec=0;
        $stmt=sprintf("SELECT pause_epoch FROM osdial_agent_log WHERE agent_log_id='%s';",mres($agent_log_id));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $pause_sec = ($StarTtime - $row[0]);
        }
        $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_epoch='%s',lead_id='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($StarTtime),mres($lead_id),mres($agent_log_id));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        echo "$MqueryCID\n";
    }
}


################################################################################
### manDiaLlookCALL - for manual OSDiaL dialing this will attempt to look up
###                   the trunk channel that the call was placed on
################################################################################
if ($ACTION == 'manDiaLlookCaLL') {
    $MT[0]='';
    $row='';
    $rowx='';
    $call_good=0;
    if (OSDstrlen($MDnextCID)<18) {
        echo "NO\n";
        echo "MDnextCID $MDnextCID is not valid\n";
        exit;
    } else {
        ##### look for the channel in the UPDATED osdial_manager record of the call initiation
        $stmt=sprintf("SELECT uniqueid,channel FROM osdial_manager WHERE callerid='%s' AND server_ip='%s' AND status IN('UPDATED','DEAD') LIMIT 1;",mres($MDnextCID),mres($server_ip));
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $VM_mancall_ct = mysql_num_rows($rslt);
        if ($VM_mancall_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $uniqueid =$row[0];
            $channel =$row[1];
            $call_output = "$uniqueid\n$channel\n";
            $call_good++;
        } else {
            ### after 10 seconds, start checking for call termination in the carrier log
            if ($DiaL_SecondS>0 and OSDpreg_match("/0$/",$DiaL_SecondS)) {
                $stmt=sprintf("SELECT uniqueid,channel,end_epoch,isup_result FROM call_log WHERE caller_code='%s' AND server_ip='%s' ORDER BY start_time DESC LIMIT 1;",mres($MDnextCID),mres($server_ip));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $VM_mancallX_ct = mysql_num_rows($rslt);
                if ($VM_mancallX_ct>0) {
                    $row=mysql_fetch_row($rslt);
                    $uniqueid =     $row[0];
                    $channel =      $row[1];
                    $end_epoch =    $row[2];
                    $hangup_cause =  $row[3];

                    #$stmt="SELECT status FROM osdial_log WHERE uniqueid='$uniqueid' AND server_ip='$server_ip' AND channel='$channel' AND status IN('BUSY','CHANUNAVAIL','CONGESTION') LIMIT 1;";
                    $stmt=sprintf("SELECT status FROM osdial_log WHERE uniqueid='%s' AND server_ip='%s' AND channel='%s' AND ((status IN('B','CPRB') AND user='VDAD') OR status IN('CRR','CRF','CRO','CRC','CPRUNK','CPRNA','CPRATB','CPRCR','CPRLR','CPRSNC','CPRSRO','CPRSIC','CPRSIO','CPRSVC','CPSHU','CPSUNK')) LIMIT 1;",mres($uniqueid),mres($server_ip),mres($channel));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $CL_mancall_ct = mysql_num_rows($rslt);
                    if ($CL_mancall_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $dialstatus =$row[0];

                        $channel = $dialstatus;
                        $hangup_cause_msg = "Cause: " . $hangup_cause . " - " . hangup_cause_description($hangup_cause);

                        $call_output = "$uniqueid\n$channel\nERROR\n" . $hangup_cause_msg;
                        $call_good++;

                        ### Delete call record
                        $stmt=sprintf("DELETE from osdial_auto_calls WHERE callerid='%s';",mres($MDnextCID));
                        if ($format=='debug') echo "\n<!-- $stmt -->";
                        $rslt=mysql_query($stmt, $link);
                    }
                }
            }
        }

        if ($call_good>0) {
            $dead_epochSQL = '';
            $pause_sec=0;
            $wait_epoch='';
            $wait_sec=0;
            $stmt=sprintf("SELECT pause_epoch,wait_epoch FROM osdial_agent_log WHERE agent_log_id='%s';",mres($agent_log_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $VDpr_ct = mysql_num_rows($rslt);
            if ($VDpr_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $pause_epoch = $row[0];
                $wait_epoch = $row[1];
                if (OSDstrlen($wait_epoch)<5) $wait_epoch=$StarTtime;
                $pause_sec = ($StarTtime - $pause_epoch);
                $wait_sec = ($StarTtime - $wait_epoch);
                if ($wait_sec<0) $wait_sec=0;
            }
            $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',lead_id='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($StarTtime),mres($lead_id),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("UPDATE osdial_auto_calls SET uniqueid='%s',channel='%s' WHERE callerid='%s';",mres($uniqueid),mres($channel),mres($MDnextCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("UPDATE call_log SET uniqueid='%s',channel='%s' WHERE caller_code='%s';",mres($uniqueid),mres($channel),mres($MDnextCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            echo "$call_output";
        } else {
            echo "NO\n$DiaL_SecondS\n";
        }
    }
}

if ($ACTION == 'OLDmanDiaLlookCaLL') {
    $MT[0]='';
    $row='';   $rowx='';
    if (OSDstrlen($MDnextCID)<18) {
        echo "NO\n";
        echo "MDnextCID $MDnextCID is not valid\n";
        exit;
    } else {
        ##### look for the channel in the UPDATED osdial_manager record of the call initiation
        $stmt=sprintf("SELECT SQL_NO_CACHE uniqueid,channel FROM osdial_manager WHERE callerid='%s' AND server_ip='%s' AND status IN('UPDATED','DEAD') LIMIT 1;",mres($MDnextCID),mres($server_ip));
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $VM_mancall_ct = mysql_num_rows($rslt);
        if ($VM_mancall_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $uniqueid =$row[0];
            $channel =$row[1];
            echo "$uniqueid\n$channel";

            if (!OSDpreg_match('/^Local\//',$channel)) {
                $PADlead_id = sprintf("%09s", $lead_id);
                while (OSDstrlen($PADlead_id) > 9) $PADlead_id = OSDsubstr($PADlead_id, 0, -1);

                # Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
                $MqueryCID = "M$CIDdate$PADlead_id";
                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Redirect','%s','Channel: %s','Context: osdial','Exten: 7%s','Priority: 1','CallerID: %s','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($channel),mres($MDnextCID),mres($channel),mres($conf_exten),mres($MDnextCID),mres($MDnextCID));
                if ($DB) echo "$stmt\n"; 
                $rslt=mysql_query($stmt, $link);
            }

            $stmt=sprintf("SELECT SQL_NO_CACHE pause_epoch,wait_epoch FROM osdial_agent_log WHERE agent_log_id='%s';",mres($agent_log_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $VDpr_ct = mysql_num_rows($rslt);
            if ($VDpr_ct > 0) {
                $pause_sec=0;
                $wait_epoch='';
                $wait_sec=0;
                $row=mysql_fetch_row($rslt);
                $pause_epoch = $row[0];
                $wait_epoch = $row[1];
                if (OSDstrlen($wait_epoch)<5) $wait_epoch=$StarTtime;
                $wait_sec = ($StarTtime - $wait_epoch);
                if ($wait_sec<0) $wait_sec=0;
                $pause_sec=($wait_epoch-$pause_epoch);

                $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',lead_id='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($StarTtime),mres($lead_id),mres($agent_log_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }

            $stmt=sprintf("UPDATE osdial_live_agents SET uniqueid='%s',channel='%s',call_server_ip='%s' WHERE callerid='%s';",mres($uniqueid),mres($channel),mres($server_ip),mres($MDnextCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);


            $stmt=sprintf("UPDATE osdial_auto_calls SET uniqueid='%s',channel='%s' WHERE callerid='%s';",mres($uniqueid),mres($channel),mres($MDnextCID));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        } else {
            echo "NO\n";
        }
    }
}




################################################################################
### manDiaLlogCALL - for manual OSDiaL logging of calls places record in
###                  osdial_log and then sends process to call_log entry
################################################################################
if ($ACTION == 'manDiaLlogCaLL') {
    $MT[0]='';
    $row='';
    $rowx='';

    if ($stage == "start") {
        if ( (OSDstrlen($uniqueid)<1) || (OSDstrlen($lead_id)<1) || (OSDstrlen($list_id)<1) || (OSDstrlen($phone_number)<1) || (OSDstrlen($campaign)<1) ) {
            debugLog('osdial_debug',"$NOW_TIME|VL_LOG_0|$uniqueid|$lead_id|$user|$list_id|$campaign|$start_epoch|$phone_number|$agent_log_id|");
            echo "LOG NOT ENTERED\n";
            echo "uniqueid $uniqueid or lead_id: $lead_id or list_id: $list_id or phone_number: $phone_number or campaign: $campaign is not valid\n";
            exit;
        } else {
            $user_group='';
            $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $ug_record_ct = mysql_num_rows($rslt);
            if ($ug_record_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $user_group =		trim($row[0]);
            }

            $manualVLexists=0;
            $stmt=sprintf("SELECT count(*) FROM osdial_log WHERE lead_id='%s' AND user='%s' AND phone_number='%s' AND uniqueid='%s';",mres($lead_id),mres($user),mres($phone_number),mres($uniqueid));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $VL_exists_ct = mysql_num_rows($rslt);
            if ($VL_exists_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $manualVLexists =       $row[0];
            }

            $manualVLexistsDUP=0;

            if ($manualVLexists<1) {
                ##### insert log into osdial_log for manual OSDiaL call
                $stmt=sprintf("INSERT INTO osdial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,server_ip) VALUES('%s','%s','%s','%s','%s','%s','INCALL','%s','%s','%s','MANUAL','N','%s','%s');",mres($uniqueid),mres($lead_id),mres($list_id),mres($campaign),mres($NOW_TIME),mres($StarTtime),mres($phone_code),mres($phone_number),mres($user),mres($user_group),mres($server_ip));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $DUPerrno = mysql_errno($link);
                if ($DUPerrno>0) $manualVLexistsDUP=1;
                $affected_rows = mysql_affected_rows($link);
            }
            if ($manualVLexists>0 or $manualVLexistsDUP>0) {
                ##### insert log into osdial_log for manual OSDial call
                $stmt=sprintf("UPDATE osdial_log SET list_id='%s',comments='MANUAL',user_group='%s',server_ip='%s' WHERE lead_id='%s' AND user='%s' AND phone_number='%s' AND uniqueid='%s';",mres($list_id),mres($user_group),mres($server_ip),mres($lead_id),mres($user),mres($phone_number),mres($uniqueid));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
            }

            if ($affected_rows > 0) {
                echo "OSDiaL_LOG Inserted: $uniqueid|$channel|$NOW_TIME\n";
                echo "$StarTtime\n";
            } else {
                echo "LOG NOT ENTERED\n";
            }

            $stmt=sprintf("UPDATE osdial_auto_calls SET uniqueid='%s' WHERE lead_id='%s';",mres($uniqueid),mres($lead_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
        }
    }

    if ($stage == "end") {
        $status_dispo='NONE';
        if ($alt_num_status > 0) $status_dispo = 'ALTNUM';
        $LAcomments='NONE';
        $stmt=sprintf("SELECT SQL_NO_CACHE comments FROM osdial_live_agents WHERE user='%s' ORDER BY last_update_time DESC LIMIT 1;",mres($user));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $LAcnt = mysql_num_rows($rslt);
        if ($LAcnt>0) {
            $row=mysql_fetch_row($rslt);
            $LAcomments = $row[0];
        }

        if (OSDstrlen($uniqueid)<1 and $LAcomments == 'INBOUND') {
            debugLog('osdial_debug', "$NOW_TIME|INBND_LOG_0|$uniqueid|$lead_id|$user|$inOUT|$VLA_inOUT|$start_epoch|$phone_number|$agent_log_id|");
            $uniqueid='6666.1';
        }

        if ( (OSDstrlen($uniqueid)<1) || (OSDstrlen($lead_id)<1) ) {
            echo "LOG NOT ENTERED\n";
            echo "uniqueid $uniqueid or lead_id: $lead_id is not valid\n";
            exit;
        } else {
            $term_reason='NONE';
            $four_hours_ago = date("Y-m-d H:i:s", mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y")));
            if ($start_epoch < 1000) {
                if ($LAcomments == 'INBOUND') {
                    ##### look for the start epoch in the osdial_closer_log table
                    $stmt=sprintf("SELECT SQL_NO_CACHE start_epoch,term_reason,closecallid,campaign_id,status FROM osdial_closer_log WHERE phone_number='%s' AND lead_id='%s' AND user='%s' AND call_date>'%s' ORDER BY closecallid DESC LIMIT 1;",mres($phone_number),mres($lead_id),mres($user),mres($four_hours_ago));
                } else {
                    ##### look for the start epoch in the osdial_log table
                    $stmt=sprintf("SELECT SQL_NO_CACHE start_epoch,term_reason,uniqueid,campaign_id,status FROM osdial_log WHERE uniqueid='%s' AND lead_id='%s' ORDER BY call_date DESC LIMIT 1;",mres($uniqueid),mres($lead_id));
                    $VDIDselect = "VDL_UIDLID $uniqueid $lead_id";
                }
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $VM_mancall_ct = mysql_num_rows($rslt);
                if ($VM_mancall_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $start_epoch =  $row[0];
                    $Lterm_reason = $row[1];
                    $Luniqueid =    $row[2];
                    $Lcampaign_id = $row[3];
                    $Lstatus =      $row[4];
                    $length_in_sec = ($StarTtime - $start_epoch);
                } else {
                    $length_in_sec = 0;
                }

                if ( ($length_in_sec < 1) and ($LAcomments == 'INBOUND') ) {
                    debugLog('osdial_debug',"$NOW_TIME|INBND_LOG_1|$uniqueid|$lead_id|$user|$inOUT|$length_in_sec|$Lterm_reason|$Luniqueid|$start_epoch|");

                    ##### start epoch in the osdial_log table, couldn't find one in osdial_closer_log
                    $stmt=sprintf("SELECT SQL_NO_CACHE start_epoch,term_reason,campaign_id,status FROM osdial_log WHERE uniqueid='%s' AND lead_id='%s' ORDER BY call_date DESC LIMIT 1;",mres($uniqueid),mres($lead_id));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VM_mancall_ct = mysql_num_rows($rslt);
                    if ($VM_mancall_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $start_epoch =  $row[0];
                        $Lterm_reason = $row[1];
                        $Lcampaign_id = $row[2];
                        $Lstatus =      $row[2];
                        $length_in_sec = ($StarTtime - $start_epoch);
                    } else {
                        $length_in_sec = 0;
                    }
                }
            } else {
                $length_in_sec = ($StarTtime - $start_epoch);
            }

            if (OSDstrlen($Lcampaign_id)<1) $Lcampaign_id = $campaign;

            if ($LAcomments == 'INBOUND') {
                $ocl_statusSQL='';
                if ($Lstatus=='INCALL') $ocl_statusSQL = sprintf(",status='%s'",mres($status_dispo));
                $stmt=sprintf("UPDATE osdial_closer_log SET end_epoch='%s',length_in_sec='%s'%s WHERE lead_id='%s' AND user='%s' AND call_date>'%s' ORDER BY call_date DESC LIMIT 1;",mres($StarTtime),mres($length_in_sec),$ocl_statusSQL,mres($lead_id),mres($user),mres($four_hours_ago));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                if ($affected_rows > 0) {
                    echo "$uniqueid\n$channel\n";
                } else {
                    debugLog('osdial_debug',"$NOW_TIME|INBND_LOG_2|$uniqueid|$lead_id|$user|$inOUT|$length_in_sec|$Lterm_reason|$Luniqueid|$start_epoch|");
                }
            }

            if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);
            }

            if ($auto_dial_level > 0) {
                ### check to see if campaign has alt_dial enabled
                $stmt=sprintf("SELECT auto_alt_dial,use_internal_dnc FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $VAC_mancall_ct = mysql_num_rows($rslt);
                if ($VAC_mancall_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $auto_alt_dial =    $row[0];
                    $use_internal_dnc = $row[1];
                } else {
                    $auto_alt_dial = 'NONE';
                }
                if (OSDpreg_match("/(ALT_ONLY|ADDR3_ONLY|ALT_AND_ADDR3|ALT_ADDR3_AND_AFFAP)/",$auto_alt_dial)) {
                    ### check to see if lead should be alt_dialed
                    if (OSDstrlen($alt_dial)<2) $alt_dial = 'NONE';

                    ### check if inbound call, if so find a recent outbound call to pull alt_dial value from
                    if ($VLA_inOUT == 'INBOUND') {
                        $one_hour_ago = date("Y-m-d H:i:s", mktime(date("H")-1,date("i"),date("s"),date("m"),date("d"),date("Y")));
                        ##### find a recent outbound call associated with this inbound call
                        $stmt=sprintf("SELECT alt_dial FROM osdial_log WHERE lead_id='%s' AND status IN('DROP','XDROP') AND call_date>'%s' ORDER BY call_date DESC LIMIT 1;",mres($lead_id),mres($one_hour_ago));
                        $rslt=mysql_query($stmt, $link);
                        if ($DB) echo "$stmt\n";
                        $VL_alt_ct = mysql_num_rows($rslt);
                        if ($VL_alt_ct > 0) {
                            $row=mysql_fetch_row($rslt);
                            $alt_dial = $row[0];
                        }
                    }

                    if (OSDpreg_match("/(NONE|MAIN)/",$alt_dial) and OSDpreg_match("/(ALT_ONLY|ALT_AND_ADDR3|ALT_ADDR3_AND_AFFAP)/",$auto_alt_dial)) {
                        $alt_dial_skip=0;
                        $stmt=mres("SELECT alt_phone,gmt_offset_now,state FROM osdial_list WHERE lead_id='%s';",mres($lead_id));
                        $rslt=mysql_query($stmt, $link);
                        if ($DB) echo "$stmt\n";
                        $VAC_mancall_ct = mysql_num_rows($rslt);
                        if ($VAC_mancall_ct > 0) {
                            $row=mysql_fetch_row($rslt);
                            $alt_phone =		$row[0];
                            $alt_phone = OSDpreg_replace("/[^0-9]/","",$alt_phone);
                            $gmt_offset_now =	$row[1];
                            $state =			$row[2];
                        } else {
                            $alt_phone = '';
                        }

                        $VD_alt_dnc_count=0;
                        if (OSDstrlen($alt_phone)>5) {
                            if (OSDpreg_match("/Y/",$use_internal_dnc)) {
                                if ($config['settings']['enable_multicompany']>0) {
                                    $comp_id=0;
                                    $dnc_method='';
                                    $stmtA=sprintf("SELECT comp_id,dnc_method FROM osdial_companies WHERE company_id='%s';",mres((OSDsubstr($campaign,0,3)*1)-100));
                                    $rslt=mysql_query($stmtA, $link);
                                    if ($DB) echo "$stmtA\n";
                                    $OC_ct = mysql_num_rows($rslt);
                                    if ($OC_ct > 0) {
                                        $row=mysql_fetch_row($rslt);
                                        $comp_id = $row[0];
                                        $dnc_method = $row[1];
                                    }
                                    if (OSDpreg_match("/COMPANY|BOTH/",$dnc_method)) {
                                        $stmtA=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_dnc_company WHERE company_id='%s' AND (phone_number='%s' OR phone_number='%s');",mres($comp_id),mres($alt_phone),mres(OSDsubstr($alt_phone,0,3)."XXXXXXX"));
                                        $rslt=mysql_query($stmtA, $link);
                                        if ($DB) echo "$stmtA\n";
                                        $ODC_ct = mysql_num_rows($rslt);
                                        if ($ODC_ct > 0) {
                                            $row=mysql_fetch_row($rslt);
                                            $VD_alt_dnc_count += $row[0];
                                        }
                                    }
                                    if (OSDpreg_match("/COMPANY/",$dnc_method)) $alt_dial_skip++;
                                }

                                if ($alt_dial_skip==0) {
                                    $stmtA=sprintf("SELECT count(*) FROM osdial_dnc WHERE phone_number='%s';",mres($alt_phone));
                                    $rslt=mysql_query($stmtA, $link);
                                    if ($DB) echo "$stmtA\n";
                                    $VLAP_dnc_ct = mysql_num_rows($rslt);
                                    if ($VLAP_dnc_ct > 0) {
                                        $row=mysql_fetch_row($rslt);
                                        $VD_alt_dnc_count += $row[0];
                                    }
                                }
                            }

                            if ($VD_alt_dnc_count < 1) {
                                ### insert record into osdial_hopper for alt_phone call attempt
                                $stmt=sprintf("INSERT INTO osdial_hopper SET lead_id='%s',campaign_id='%s',status='HOLD',list_id='%s',gmt_offset_now='%s',state='%s',alt_dial='ALT',user='',priority='25';",mres($lead_id),mres($campaign),mres($list_id),mres($gmt_offset_now),mres($state));
                                if ($DB) echo "$stmt\n";
                                $rslt=mysql_query($stmt, $link);
                            } else {
                                $alt_dial_skip=1;
                            }
                        } else {
                            $alt_dial_skip=1;
                        }
                        if ($alt_dial_skip>0) $alt_dial='ALT';
                    }
                    if ( (OSDpreg_match("/(ALT)/",$alt_dial) and OSDpreg_match("/ALT_AND_ADDR3|ALT_ADDR3_AND_AFFAP/",$auto_alt_dial)) or (OSDpreg_match("/(NONE|MAIN)/",$alt_dial) and OSDpreg_match("/ADDR3_ONLY/",$auto_alt_dial)) ) {
                        $addr3_dial_skip=0;
                        $stmt=sprintf("SELECT address3,gmt_offset_now,state FROM osdial_list WHERE lead_id='%s';",mres($lead_id));
                        $rslt=mysql_query($stmt, $link);
                        if ($DB) echo "$stmt\n";
                        $VAC_mancall_ct = mysql_num_rows($rslt);
                        if ($VAC_mancall_ct > 0) {
                            $row=mysql_fetch_row($rslt);
                            $address3 =			$row[0];
                            $address3 = OSDpreg_replace("/[^0-9]/","",$address3);
                            $gmt_offset_now =	$row[1];
                            $state =			$row[2];
                        } else {
                            $address3 = '';
                        }

                        $VD_addr3_dnc_count=0;
                        if (OSDstrlen($address3)>5) {
                            if (OSDpreg_match("/Y/",$use_internal_dnc)) {
                                if ($config['settings']['enable_multicompany']>0) {
                                    $comp_id=0;
                                    $dnc_method='';
                                    $stmtA=sprintf("SELECT comp_id,dnc_method FROM osdial_companies WHERE company_id='%s';",mres((OSDsubstr($campaign,0,3)*1)-100));
                                    $rslt=mysql_query($stmtA, $link);
                                    if ($DB) echo "$stmtA\n";
                                    $OC_ct = mysql_num_rows($rslt);
                                    if ($OC_ct > 0) {
                                        $row=mysql_fetch_row($rslt);
                                        $comp_id = $row[0];
                                        $dnc_method = $row[1];
                                    }
                                    if (OSDpreg_match("/COMPANY|BOTH/",$dnc_method)) {
                                        $stmtA=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_dnc_company WHERE company_id='%s' AND (phone_number='%s' OR phone_number='%s');",mres($comp_id),mres($address3),mres(OSDsubstr($address3,0,3)."XXXXXXX"));
                                        $rslt=mysql_query($stmtA, $link);
                                        if ($DB) echo "$stmtA\n";
                                        $ODC_ct = mysql_num_rows($rslt);
                                        if ($ODC_ct > 0) {
                                            $row=mysql_fetch_row($rslt);
                                            $VD_addr3_dnc_count += $row[0];
                                        }
                                    }
                                    if (OSDpreg_match("/COMPANY/",$dnc_method)) $addr3_dial_skip++;
                                }

                                if ($VD_addr3_dnc_count < 1) {
                                    $stmtA=sprintf("SELECT count(*) FROM osdial_dnc WHERE phone_number='%s';",mres($address3));
                                    $rslt=mysql_query($stmtA, $link);
                                    if ($DB) echo "$stmtA\n";
                                    $VLAP_dnc_ct = mysql_num_rows($rslt);
                                    if ($VLAP_dnc_ct > 0) {
                                        $row=mysql_fetch_row($rslt);
                                        $VD_addr3_dnc_count += $row[0];
                                    }
                                }
                            }

                            if ($VD_addr3_dnc_count < 1) {
                                ### insert record into osdial_hopper for address3 call attempt
                                $stmt=sprintf("INSERT INTO osdial_hopper SET lead_id='%s',campaign_id='%s',status='HOLD',list_id='%s',gmt_offset_now='%s',state='%s',alt_dial='ADDR3',user='',priority='20';",mres($lead_id),mres($campaign),mres($list_id),mres($gmt_offset_now),mres($state));
                                if ($DB) echo "$stmt\n";
                                $rslt=mysql_query($stmt, $link);
                            } else {
                                $addr3_dial_skip=1;
                            }
                        } else {
                            $addr3_dial_skip=1;
                        }
                        if ($addr3_dial_skip>0) $alt_dial='ADDR3';
                    }
                    if (OSDpreg_match("/(ADDR3|AFFAP)/",$alt_dial) and OSDpreg_match("/ALT_ADDR3_AND_AFFAP/",$auto_alt_dial)) {
                        $affap_dial_skip=0;
                        $VD_affap_dnc_count=0;
                        $aff_number = '';
                        $cur_aff = 1;
                        if ($alt_dial!='ADDR3') $cur_aff = (substr($alt_dial,5) * 1) + 1;
                        while ($cur_aff<10) {
                            $stmt=sprintf("SELECT SQL_NO_CACHE value FROM osdial_list_fields WHERE field_id=(SELECT id FROM osdial_campaign_fields WHERE name='AFFAP%s' LIMIT 1) AND lead_id='%s';",mres($cur_aff),mres($lead_id));
                            $rslt=mysql_query($stmt, $link);
                            if ($DB) echo "$stmt\n";
                            $VAC_mancall_ct = mysql_num_rows($rslt);
                            if ($VAC_mancall_ct > 0) {
                                $row=mysql_fetch_row($rslt);
                                $affap = $row[0];
                                $affap = OSDpreg_replace("/[^0-9]/","",$affap);
                            } else {
                                $affap = '';
                            }

                            if (OSDstrlen($affap)>5) {
                                $stmtA=sprintf("SELECT gmt_offset_now,state FROM osdial_list WHERE lead_id='%s';",mres($lead_id));
                                $rslt=mysql_query($stmtA, $link);
                                if ($DB) echo "$stmtA\n";
                                $OL_ct = mysql_num_rows($rslt);
                                if ($OL_ct > 0) {
                                    $row=mysql_fetch_row($rslt);
                                    $gmt_offset_now = $row[0];
                                    $state = $row[0];
                                }

                                if (OSDpreg_match("/Y/",$use_internal_dnc)) {
                                    if ($config['settings']['enable_multicompany']>0) {
                                        $comp_id=0;
                                        $dnc_method='';
                                        $stmtA=sprintf("SELECT comp_id,dnc_method FROM osdial_companies WHERE company_id='%s';",mres((OSDsubstr($campaign,0,3)*1)-100));
                                        $rslt=mysql_query($stmtA, $link);
                                        if ($DB) echo "$stmtA\n";
                                        $OC_ct = mysql_num_rows($rslt);
                                        if ($OC_ct > 0) {
                                            $row=mysql_fetch_row($rslt);
                                            $comp_id = $row[0];
                                            $dnc_method = $row[1];
                                        }
                                        if (OSDpreg_match("/COMPANY|BOTH/",$dnc_method)) {
                                            $stmtA=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_dnc_company WHERE company_id='%s' AND (phone_number='%s' OR phone_number='%s');",mres($comp_id),mres($affap),mres(OSDsubstr($affap,0,3)."XXXXXXX"));
                                            $rslt=mysql_query($stmtA, $link);
                                            if ($DB) echo "$stmtA\n";
                                            $ODC_ct = mysql_num_rows($rslt);
                                            if ($ODC_ct > 0) {
                                                $row=mysql_fetch_row($rslt);
                                                $VD_affap_dnc_count += $row[0];
                                            }
                                        }
                                        if (OSDpreg_match("/COMPANY/",$dnc_method)) $affap_dial_skip++;
                                    }

                                    if ($affap_dial_skip==0) {
                                        $stmtA=sprintf("SELECT count(*) FROM osdial_dnc WHERE phone_number='%s';",mres($affap));
                                        $rslt=mysql_query($stmtA, $link);
                                        if ($DB) echo "$stmtA\n";
                                        $VLAP_dnc_ct = mysql_num_rows($rslt);
                                        if ($VLAP_dnc_ct > 0) {
                                            $row=mysql_fetch_row($rslt);
                                            $VD_affap_dnc_count += $row[0];
                                        }
                                    }
                                }

                                if ($VD_affap_dnc_count < 1) {
                                    ### insert record into osdial_hopper for affap call attempt
                                    $stmt=sprintf("INSERT INTO osdial_hopper SET lead_id='%s',campaign_id='%s',status='HOLD',list_id='%s',gmt_offset_now='%s',state='%s',alt_dial='AFFAP%s',user='',priority='20';",mres($lead_id),mres($campaign),mres($list_id),mres($gmt_offset_now),mres($state),mres($cur_aff));
                                    if ($DB) echo "$stmt\n";
                                    $rslt=mysql_query($stmt, $link);
                                    $cur_aff=10;
                                }
                            }
                            if ($VD_affap_dnc_count>0 or OSDstrlen($aff_number)<=5) $cur_aff++;
                        }
                    }
                }

                if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                    ### check to see if lead should be alt_dialed
                    $stmt=sprintf("SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid FROM osdial_auto_calls WHERE lead_id='%s';",mres($lead_id));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VAC_qm_ct = mysql_num_rows($rslt);
                    if ($VAC_qm_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $auto_call_id	= $row[0];
                        $CLlead_id		= $row[1];
                        $CLphone_number	= $row[2];
                        $CLstatus		= $row[3];
                        $CLcampaign_id	= $row[4];
                        $CLphone_code	= $row[5];
                        $CLalt_dial		= $row[6];
                        $CLstage		= $row[7];
                        $CLcallerid		= $row[8];
                        $CLuniqueid		= $row[9];
                    }

                    $CLstage = OSDpreg_replace("/.*-/",'',$CLstage);
                    if (OSDstrlen($CLstage) < 1) $CLstage=0;

                    $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                    mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                    if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);


                    $stmt=sprintf("SELECT count(*) FROM queue_log WHERE call_id='%s' AND verb='COMPLETECALLER';",mres($MDnextCID));
                    $rslt=mysql_query($stmt, $linkB);
                    if ($DB) echo "$stmt\n";
                    $VAC_cc_ct = mysql_num_rows($rslt);
                    if ($VAC_cc_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $caller_complete	= $row[0];
                    }

                    if ($caller_complete < 1) {
                        $term_reason='AGENT';
                    } else {
                        $term_reason='CALLER';
                    }
                    mysql_close($linkB);
                }

                if ($nodeletevdac<1) {
                    ### delete call record from  osdial_auto_calls
                    $stmt=sprintf("DELETE FROM osdial_auto_calls WHERE lead_id='%s' AND campaign_id='%s' AND uniqueid='%s';",mres($lead_id),mres($campaign),mres($uniqueid));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                }

                $random = (rand(1000000, 9999999) + 10000000);
                $stmt=sprintf("UPDATE osdial_live_agents SET status='PAUSED',uniqueid='',lead_id='',callerid='',channel='',call_server_ip='',last_call_finish='%s',random_id='%s',comments='DISPO' WHERE user='%s' AND server_ip='%s';",mres($NOW_TIME),mres($random),mres($user),mres($server_ip));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                if ($affected_rows > 0) {
                    if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                        $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                        mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                        if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                        $data4SQL='';
                        $stmt=sprintf("SELECT queuemetrics_phone_environment FROM osdial_campaigns WHERE campaign_id='%s' AND queuemetrics_phone_environment!='';",mres($campaign));
                        $rslt=mysql_query($stmt, $link);
                        if ($DB) echo "$stmt\n";
                        $cqpe_ct = mysql_num_rows($rslt);
                        if ($cqpe_ct > 0) {
                            $row=mysql_fetch_row($rslt);
                            $data4SQL = sprintf(",data4='%s'",mres($row[0]));
                        }


                        $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='NONE',agent='Agent/%s',verb='PAUSEALL',serverid='%s'%s;",mres($StarTtime),mres($user),mres($config['settings']['queuemetrics_log_id']),$data4SQL);
                        if ($DB) echo "$stmt\n";
                        $rslt=mysql_query($stmt, $linkB);
                        $affected_rows = mysql_affected_rows($linkB);

                        mysql_close($linkB);
                    }
                }
            } else {
                if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                    ### check to see if lead should be alt_dialed
                    $stmt=sprintf("SELECT auto_call_id,lead_id,phone_number,status,campaign_id,phone_code,alt_dial,stage,callerid,uniqueid FROM osdial_auto_calls WHERE lead_id='%s';",mres($lead_id));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VAC_qm_ct = mysql_num_rows($rslt);
                    if ($VAC_qm_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $auto_call_id	= $row[0];
                        $CLlead_id		= $row[1];
                        $CLphone_number	= $row[2];
                        $CLstatus		= $row[3];
                        $CLcampaign_id	= $row[4];
                        $CLphone_code	= $row[5];
                        $CLalt_dial		= $row[6];
                        $CLstage		= $row[7];
                        $CLcallerid		= $row[8];
                        $CLuniqueid		= $row[9];
                    }

                    $CLstage = OSDpreg_replace("/XFER|CLOSER|-/",'',$CLstage);
                    if ($CLstage < 0.25) $CLstage=0;

                    $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                    mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                    if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                    $data4SQL='';
                    $stmt=sprintf("SELECT queuemetrics_phone_environment FROM osdial_campaigns WHERE campaign_id='%s' AND queuemetrics_phone_environment!='';",mres($campaign));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $cqpe_ct = mysql_num_rows($rslt);
                    if ($cqpe_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $data4SQL = sprintf(",data4='%s'",mres($row[0]));
                    }

                    $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='Agent/%s',verb='COMPLETEAGENT',data1='%s',data2='%s',data3='1',serverid='%s'%s;",mres($StarTtime),mres($MDnextCID),mres($campaign),mres($user),mres($CLstage),mres($length_in_sec),mres($config['settings']['queuemetrics_log_id']),$data4SQL);
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);

                    mysql_close($linkB);
                }

                if ($nodeletevdac<1) {
                    $stmt=sprintf("DELETE FROM osdial_auto_calls WHERE lead_id='%s' AND campaign_id='%s' AND callerid LIKE 'M%%';",mres($lead_id),mres($campaign));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                }

                $random = (rand(1000000, 9999999) + 10000000);
                $stmt=sprintf("UPDATE osdial_live_agents SET status='PAUSED',uniqueid='',lead_id='',callerid='',channel='',call_server_ip='',last_call_finish='%s',random_id='%s',comments='DISPO' where user='%s' and server_ip='%s';",mres($NOW_TIME),mres($random),mres($user),mres($server_ip));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                if ($affected_rows > 0) {
                    if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                        $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                        mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                        if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                        $data4SQL='';
                        $stmt=sprintf("SELECT queuemetrics_phone_environment FROM osdial_campaigns WHERE campaign_id='%s' AND queuemetrics_phone_environment!='';",mres($campaign));
                        $rslt=mysql_query($stmt, $link);
                        if ($DB) echo "$stmt\n";
                        $cqpe_ct = mysql_num_rows($rslt);
                        if ($cqpe_ct > 0) {
                            $row=mysql_fetch_row($rslt);
                            $data4SQL = sprintf(",data4='%s'",mres($row[0]));
                        }

                        $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='NONE',agent='Agent/%s',verb='PAUSEALL',serverid='%s'%s;",mres($StarTtime),mres($user),mres($config['settings']['queuemetrics_log_id']),$data4SQL);
                        if ($DB) echo "$stmt\n";
                        $rslt=mysql_query($stmt, $linkB);
                        $affected_rows = mysql_affected_rows($linkB);

                        mysql_close($linkB);
                    }
                }
            }

            if (OSDpreg_match('/AUTO|MANUAL/',$LAcomments)) {
                $SQLterm=sprintf("term_reason='%s',",mres($term_reason));

                if ( (OSDpreg_match("/NONE/",$term_reason)) or (OSDpreg_match("/NONE/",$Lterm_reason)) or (OSDstrlen($Lterm_reason) < 1) ) {
                    ### check to see if lead should be alt_dialed
                    $stmt=sprintf("SELECT SQL_NO_CACHE term_reason,uniqueid,status FROM osdial_log WHERE uniqueid='%s' AND lead_id='%s' ORDER BY call_date DESC LIMIT 1;",mres($uniqueid),mres($lead_id));
                    $rslt=mysql_query($stmt, $link);
                    $VAC_qm_ct = mysql_num_rows($rslt);
                    if ($VAC_qm_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $Lterm_reason = $row[0];
                        $Luniqueid =    $row[1];
                        $Lstatus =      $row[2];
                        $VDIDselect =   "VDL_UIDLID $uniqueid $lead_id";
                    }
                    if (OSDpreg_match("/CALLER/",$Lterm_reason)) {
                        $SQLterm = "";
                    } else {
                        $SQLterm = "term_reason='AGENT',";
                    }
                }

                ### check to see if the osdial_log record exists, if not, insert it
                $manualVLexists=0;
                $stmt=sprintf("SELECT status FROM osdial_log WHERE lead_id='%s' AND user='%s' AND phone_number='%s' AND uniqueid='%s';",mres($lead_id),mres($user),mres($phone_number),mres($uniqueid));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $manualVLexists = mysql_num_rows($rslt);
                if ($manualVLexists > 0) {
                    $row=mysql_fetch_row($rslt);
                    $Lstatus = $row[0];
                }

                if ($manualVLexists < 1) {
                    ##### insert log into osdial_log for manual OSDial call
                    $stmt=sprintf("INSERT INTO osdial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed,user_group,server_ip) VALUES('%s','%s','%s','%s','%s','%s','DONEM','%s','%s','%s','MANUAL','N','%s','%s');",mres($uniqueid),mres($lead_id),mres($list_id),mres($campaign),mres($NOW_TIME),mres($StarTtime),mres($phone_code),mres($phone_number),mres($user),mres($user_group),mres($server_ip));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);

                    if ($affected_rows > 0) {
                        echo "OSDiaL_LOG Inserted: $uniqueid|$channel|$NOW_TIME\n";
                        echo "$StarTtime\n";
                    } else {
                        echo "LOG NOT ENTERED\n";
                    }
                } else {
                    $stmt=sprintf("UPDATE osdial_log SET uniqueid='%s' WHERE lead_id='%s' AND user='%s' AND phone_number='%s' AND uniqueid='%s';",mres($uniqueid),mres($lead_id),mres($user),mres($phone_number),mres($uniqueid));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);
                }

                ##### update the duration and end time in the osdial_log table
                $ol_statusSQL='';
                if ($Lstatus=='INCALL') $ol_statusSQL = sprintf(",status='%s'",mres($status_dispo));
                $stmt=sprintf("UPDATE osdial_log SET %send_epoch='%s',length_in_sec='%s'%s WHERE uniqueid='%s' AND lead_id='%s' AND user='%s' ORDER BY call_date DESC LIMIT 1;",$SQLterm,mres($StarTtime),mres($length_in_sec),$ol_statusSQL,mres($uniqueid),mres($lead_id),mres($user));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);

                if ($affected_rows > 0) {
                    echo "$uniqueid\n$channel\n";
                } else {
                    echo "LOG NOT ENTERED\n\n";
                }
            } else {
                $SQLterm=sprintf("term_reason='%s',",mres($term_reason));
                $QL_term='';

                if ( (OSDpreg_match("/NONE/",$term_reason)) or (OSDpreg_match("/NONE/",$Lterm_reason)) or (OSDstrlen($Lterm_reason) < 1) ) {
                    ### check to see if lead should be alt_dialed
                    $stmt=sprintf("SELECT SQL_NO_CACHE term_reason,closecallid FROM osdial_closer_log WHERE lead_id='%s' AND call_date>'%s' ORDER BY call_date DESC LIMIT 1;",mres($lead_id),mres($four_hours_ago));
                    $rslt=mysql_query($stmt, $link);
                    $VAC_qm_ct = mysql_num_rows($rslt);
                    if ($VAC_qm_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $Lterm_reason = $row[0];
                        $Lclosecallid = $row[1];
                        $VDIDselect =   "VDCL_LID4HOUR $lead_id $four_hours_ago";
                    }
                    if (OSDpreg_match("/CALLER/",$Lterm_reason)) {
                        $SQLterm = "";
                    } else {
                        $SQLterm = "term_reason='AGENT'";
                        $QL_term = 'COMPLETEAGENT';
                    }
                }

                if (OSDstrlen($SQLterm) > 0) {
                    ##### update the duration and end time in the osdial_log table
                    $stmt=sprintf("UPDATE osdial_closer_log SET %s WHERE lead_id='%s' AND call_date>'%s' ORDER BY call_date DESC LIMIT 1;",$SQLterm,mres($lead_id),mres($four_hours_ago));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);
                }

                if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                    $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                    mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                    if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                    if (OSDstrlen($QL_term) > 0 and $leaving_threeway > 0) {
                        $stmt=sprintf("SELECT count(*) FROM queue_log WHERE call_id='%s' AND verb='COMPLETEAGENT' AND queue='%s';",mres($MDnextCID),mres($Lcampaign_id));
                        $rslt=mysql_query($stmt, $linkB);
                        if ($DB) echo "$stmt\n";
                        $VAC_cc_ct = mysql_num_rows($rslt);
                        if ($VAC_cc_ct > 0) {
                            $row=mysql_fetch_row($rslt);
                            $agent_complete = $row[0];
                        }
                        if ($agent_complete < 1) {
                            $data4SQL='';
                            $stmt=sprintf("SELECT queuemetrics_phone_environment FROM osdial_campaigns WHERE campaign_id='%s' AND queuemetrics_phone_environment!='';",mres($campaign));
                            $rslt=mysql_query($stmt, $link);
                            if ($DB) echo "$stmt\n";
                            $cqpe_ct = mysql_num_rows($rslt);
                            if ($cqpe_ct > 0) {
                                $row=mysql_fetch_row($rslt);
                                $data4SQL = sprintf(",data4='%s'",mres($row[0]));
                            }

                            $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='Agent/%s',verb='COMPLETEAGENT',data1='%s',data2='%s',data3='1',serverid='%s'%s;",mres($StarTtime),mres($MDnextCID),mres($Lcampaign_id),mres($user),mres($CLstage),mres($length_in_sec),mres($config['settings']['queuemetrics_log_id']),$data4SQL);
                            if ($DB) echo "$stmt\n";
                            $rslt=mysql_query($stmt, $linkB);
                            $affected_rows = mysql_affected_rows($linkB);
                        }
                    }
                }
            }
        }

        echo $VDstop_rec_after_each_call . '|' . $extension . '|' . $conf_silent_prefix . '|' . $conf_exten . '|' . $user_abb . "|\n";

        ##### if OSDiaL call and hangup_after_each_call activated, find all recording 
        ##### channels and hang them up while entering info into recording_log and 
        ##### returning filename/recordingID
        if ($VDstop_rec_after_each_call == 1) {
            $local_DEF = 'Local/';
            $local_AMP = '@';
            $total_rec=0;
            $total_hangup=0;
            $loop_count=0;
            $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_sip_channels WHERE server_ip='%s' AND extension='%s' ORDER BY channel DESC;",mres($server_ip),mres($conf_exten));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            if ($rslt) $rec_list = mysql_num_rows($rslt);
            while ($rec_list>$loop_count) {
                $row=mysql_fetch_row($rslt);
                if (OSDpreg_match("/Local\/$conf_silent_prefix$conf_exten\@/i",$row[0])) {
                    $rec_channels[$total_rec] = $row[0];
                    $total_rec++;
                } else {
                    if ( ($agentchannel == $row[0]) or (OSDpreg_match('/ASTblind/',$row[0])) ) {
                        $donothing=1;
                    } else {
                        $hangup_channels[$total_hangup] = $row[0];
                        $total_hangup++;
                    }
                }
                if ($format=='debug') echo "\n<!-- $row[0] -->";
                $loop_count++; 
            }

            $loop_count=0;
            $stmt=sprintf("SELECT SQL_NO_CACHE channel FROM live_channels WHERE server_ip='%s' AND extension='%s' ORDER BY channel DESC;",mres($server_ip),mres($conf_exten));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            if ($rslt) $rec_list = mysql_num_rows($rslt);
            while ($rec_list>$loop_count) {
                $row=mysql_fetch_row($rslt);
                if (OSDpreg_match("/Local\/$conf_silent_prefix$conf_exten\@/i",$row[0])) {
                    $rec_channels[$total_rec] = $row[0];
                    $total_rec++;
                } else {
                    #if (OSDpreg_match("/$agentchannel/i",$row[0]))
                    if ( ($agentchannel == $row[0]) or (OSDpreg_match('/ASTblind/',$row[0])) ) {
                        $donothing=1;
                    } else {
                        $hangup_channels[$total_hangup] = $row[0];
                        $total_hangup++;
                    }
                }
                if ($format=='debug') echo "\n<!-- $row[0] -->";
                $loop_count++;
            }

            ### if a conference call or 3way call was attempted, then hangup all channels except for the agentchannel
            if (($conf_dialed>0 or $hangup_all_non_reserved>0) and $leaving_threeway<1 and $blind_transfer<1) {
                $loop_count=0;
                while($loop_count < $total_hangup) {
                    if (OSDstrlen($hangup_channels[$loop_count])>5) {
                        $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Hangup','CH12346%s','Channel: %s','','','','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($hangup_channels[$loop_count]),mres($StarTtime.$loop_count),mres($hangup_channels[$loop_count]));
                        if ($format=='debug') echo "\n<!-- $stmt -->";
                        $rslt=mysql_query($stmt, $link);
                    }
                    $loop_count++;
                }
            }

            $total_recFN=0;
            $loop_count=0;
            $filename=$MT;		# not necessary : and cmd_line_f LIKE \"%_$user_abb\"
            $stmt=sprintf("SELECT SQL_NO_CACHE cmd_line_f FROM osdial_manager WHERE server_ip='%s' AND action='Originate' AND cmd_line_b='Channel: %s' ORDER BY entry_date DESC LIMIT %s;",mres($server_ip),mres($local_DEF.$conf_silent_prefix.$conf_exten.$local_AMP.$ext_context),$total_rec);
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            if ($rslt) $recFN_list = mysql_num_rows($rslt);
            while ($recFN_list>$loop_count) {
                $row=mysql_fetch_row($rslt);
                $filename[$total_recFN] = OSDpreg_replace("/Callerid: /i","",$row[0]);
                if ($format=='debug') echo "\n<!-- $row[0] -->";
                $total_recFN++;
                $loop_count++; 
            }

            $loop_count=0;
            while($loop_count < $total_rec) {
                if (OSDstrlen($rec_channels[$loop_count])>5) {
                    $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Hangup','RH12345%s','Channel: %s','','','','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($rec_channels[$loop_count]),mres($StarTtime.$loop_count),mres($rec_channels[$loop_count]));
                    if ($format=='debug') echo "\n<!-- $stmt -->";
                    $rslt=mysql_query($stmt, $link);

                    echo "REC_STOP|$rec_channels[$loop_count]|$filename[$loop_count]|";
                    if (OSDstrlen($filename)>2) {
                        $stmt=sprintf("SELECT SQL_NO_CACHE recording_id,start_epoch FROM recording_log WHERE filename='%s';",mres($filename[$loop_count]));
                        if ($format=='debug') echo "\n<!-- $stmt -->";
                        $rslt=mysql_query($stmt, $link);
                        if ($rslt) $fn_count = mysql_num_rows($rslt);
                        if ($fn_count) {
                            $row=mysql_fetch_row($rslt);
                            $recording_id = $row[0];
                            $start_time = $row[1];

                            $length_in_sec = ($StarTtime - $start_time);
                            $length_in_min = ($length_in_sec / 60);
                            $length_in_min = sprintf("%8.2f", $length_in_min);

                            $stmt=sprintf("UPDATE recording_log SET end_time='%s',end_epoch='%s',length_in_sec='%s',length_in_min='%s',uniqueid='%s' WHERE filename='%s' AND end_epoch IS NULL;",mres($NOW_TIME),mres($StarTtime),mres($length_in_sec),mres($length_in_min),mres($uniqueid),mres($filename[$loop_count]));
                            if ($format=='debug') echo "\n<!-- $stmt -->";
                            $rslt=mysql_query($stmt, $link);

                            echo "$recording_id|$length_in_min|";
                        } else {
                            echo "||";
                        }
                    } else {
                        echo "||";
                    }
                    echo "\n";
                }
                $loop_count++;
            }
        }

        $talk_epoch=''; 
        $talk_sec=0;
        $lead_id_commentsSQL='';
        $stmt=sprintf("SELECT SQL_NO_CACHE wait_epoch,talk_epoch,lead_id,comments FROM osdial_agent_log WHERE agent_log_id='%s';",mres($agent_log_id));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);

        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $wait_epoch=$row[0];
            $talk_epoch=$row[1];
            if (OSDstrlen($talk_epoch)<5) $talk_epoch=$StarTtime;
            $talk_sec = ($StarTtime - $talk_epoch);
            if ($talk_sec<0) $talk_sec=0;
            if (($auto_dial_level<1 or OSDpreg_match('/^M/',$MDnextCID)) and $inbound_man>0) {
                if (OSDpreg_match("/NULL/",$row[3]) or OSDstrlen($row[3])<1) {
                    $lead_id_commentsSQL .= ",comments='MANUAL'";
                }
                if (OSDpreg_match("/NULL/",$row[2]) or $row[2]<1 or OSDstrlen($row[2])<1) {
                    $lead_id_commentsSQL .= sprintf(",lead_id='%s'",mres($lead_id));
                }
            }
        }

        $stmt=sprintf("UPDATE osdial_agent_log SET talk_epoch='%s',talk_sec='%s',dispo_epoch='%s',uniqueid='%s'%s where agent_log_id='%s';",mres($talk_epoch),mres($talk_sec),mres($StarTtime),mres($uniqueid),$lead_id_commentsSQL,mres($agent_log_id));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        #TODO - Add queuemetrics_dispo_pause to system_settings table.
        #### if queuemetrics_dispo_pause dispo tag is enabled, log it here
        #if (OSDstrlen($config['settings']['queuemetrics_dispo_pause'])>0) {
        #    $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
        #    mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
        #    if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

        #    $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='NONE',agent='Agent/%s',verb='PAUSEREASON',serverid='%s',data1='%s';",mres($StarTtime),mres($MDnextCID),mres($user),mres($config['settings']['queuemetrics_log_id']),mres($config['settings']['queuemetrics_dispo_pause']));
        #    if ($DB) echo "$stmt\n";
        #    $rslt=mysql_query($stmt, $linkB);
        #    $affected_rows = mysql_affected_rows($linkB);
        #    mysql_close($linkB);
        #}
    }
}


################################################################################
### VDADREcheckINCOMING - for auto-dial OSDiaL dialing this will recheck for
###                       calls to see if the channel has updated
################################################################################
if ($ACTION == 'VDADREcheckINCOMING') {
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($campaign)<1) || (OSDstrlen($server_ip)<1) || (OSDstrlen($lead_id)<1) ) {
        $channel_live=0;
        echo "0\n";
        echo "Campaign $campaign is not valid\n";
        echo "lead_id $lead_id is not valid\n";
        exit;
    } else {
        ### grab the call and lead info from the osdial_live_agents table
        $stmt=sprintf("SELECT SQL_NO_CACHE lead_id,uniqueid,callerid,channel,call_server_ip FROM osdial_live_agents WHERE server_ip='%s' AND user='%s' AND campaign_id='%s' AND lead_id='%s';",mres($server_ip),mres($user),mres($campaign),mres($lead_id));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $queue_leadID_ct = mysql_num_rows($rslt);

        if ($queue_leadID_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $lead_id	=$row[0];
            $uniqueid	=$row[1];
            $callerid	=$row[2];
            $channel	=$row[3];
            $call_server_ip	=$row[4];
            if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;
            echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";
        }
    }
}


################################################################################
### VDADcheckINCOMING - for auto-dial OSDiaL dialing this will check for calls
###                     in the osdial_live_agents table in QUEUE status, then
###                     lookup the lead info and pass it back to osdial.php
################################################################################
if ($ACTION == 'VDADcheckINCOMING') {
    $Ctype = 'A';
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($campaign)<1) || (OSDstrlen($server_ip)<1) ) {
        $channel_live=0;
        echo "0\n";
        echo "Campaign $campaign is not valid\n";
        exit;
    } else {
        ### grab the call and lead info from the osdial_live_agents table
        $stmt=sprintf("SELECT SQL_NO_CACHE lead_id,uniqueid,callerid,channel,call_server_ip FROM osdial_live_agents WHERE server_ip='%s' AND user='%s' AND campaign_id='%s' AND status='QUEUE';",mres($server_ip),mres($user),mres($campaign));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $queue_leadID_ct = mysql_num_rows($rslt);

        if ($queue_leadID_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $lead_id	=$row[0];
            $uniqueid	=$row[1];
            $callerid	=$row[2];
            $channel	=$row[3];
            $call_server_ip	=$row[4];
            if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;
            echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";

            ##### grab number of calls today in this campaign and increment
            $stmt=sprintf("SELECT SQL_NO_CACHE calls_today FROM osdial_live_agents WHERE user='%s' AND campaign_id='%s';",mres($user),mres($campaign));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $vla_cc_ct = mysql_num_rows($rslt);
            if ($vla_cc_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $calls_today =$row[0];
            } else {
                $calls_today ='0';
            }
            $calls_today++;

            ### update the agent status to INCALL in osdial_live_agents
            $random = (rand(1000000, 9999999) + 10000000);
            $stmt=sprintf("UPDATE osdial_live_agents SET status='INCALL',last_call_time='%s',comments='AUTO',calls_today='%s',random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($NOW_TIME),mres($calls_today),mres($random),mres($user),mres($server_ip));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("UPDATE osdial_campaign_agents SET calls_today='%s' WHERE user='%s' AND campaign_id='%s';",mres($calls_today),mres($user),mres($campaign));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            ##### grab the data from osdial_list for the lead_id
            $stmt=sprintf("SELECT * FROM osdial_list WHERE lead_id='%s' LIMIT 1;",mres($lead_id));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $list_lead_ct = mysql_num_rows($rslt);
            if ($list_lead_ct > 0) {
                $row=mysql_fetch_row($rslt);
                #$lead_id		= trim($row[0]);
                $dispo			= trim($row[3]);
                $tsr			= trim($row[4]);
                $vendor_id		= trim($row[5]);
                $source_id		= trim($row[6]);
                $list_id		= trim($row[7]);
                $gmt_offset_now	= trim($row[8]);
                $phone_code		= trim($row[10]);
                $phone_number	= trim($row[11]);
                $title			= trim($row[12]);
                $first_name		= trim($row[13]);
                $middle_initial	= trim($row[14]);
                $last_name		= trim($row[15]);
                $address1		= trim($row[16]);
                $address2		= trim($row[17]);
                $address3		= trim($row[18]);
                $city			= trim($row[19]);
                $state			= trim($row[20]);
                $province		= trim($row[21]);
                $postal_code	= trim($row[22]);
                $country_code	= trim($row[23]);
                $gender			= trim($row[24]);
                $date_of_birth	= trim($row[25]);
                $alt_phone		= trim($row[26]);
                $email			= trim($row[27]);
                $custom1		= trim($row[28]);
                $comments		= stripslashes(trim($row[29]));
                $called_count	= trim($row[30]);
                $custom2		= trim($row[31]);
                $external_key	= trim($row[32]);
                $post_date  	= trim($row[35]);
                $organization  	= trim($row[36]);
                $organization_title = trim($row[37]);
            }

            ##### if lead is a callback, grab the callback comments
            $CBentry_time =		'';
            $CBcallback_time =	'';
            $CBuser =			'';
            $CBcomments =		'';
            if (OSDpreg_match("/CALLBK/",$dispo)) {
                $stmt=sprintf("SELECT entry_time,callback_time,user,comments FROM osdial_callbacks WHERE lead_id='%s' ORDER BY callback_id DESC LIMIT 1;",mres($lead_id));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $cb_record_ct = mysql_num_rows($rslt);
                if ($cb_record_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $CBentry_time =		trim($row[0]);
                    $CBcallback_time =	trim($row[1]);
                    $CBuser =			trim($row[2]);
                    $CBcomments =		trim($row[3]);
                }
            }

            ### update the lead status to INCALL
            $stmt=sprintf("UPDATE osdial_list SET status='INCALL',user='%s' WHERE lead_id='%s';",mres($user),mres($lead_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            ### update the log status to INCALL
            $user_group='';
            $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $ug_record_ct = mysql_num_rows($rslt);
            if ($ug_record_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $user_group =		trim($row[0]);
            }

            $stmt=sprintf("SELECT SQL_NO_CACHE campaign_id,phone_number,alt_dial,call_type FROM osdial_auto_calls WHERE callerid='%s' ORDER BY call_time DESC LIMIT 1;",mres($callerid));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $VDAC_cid_ct = mysql_num_rows($rslt);
            if ($VDAC_cid_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $VDADchannel_group	=$row[0];
                $dialed_number		=$row[1];
                $dialed_label		=$row[2];
                $call_type			=$row[3];
            } else {
                $dialed_number = $phone_number;
                $dialed_label = 'MAIN';
                if (OSDpreg_match('/^M|^V/',$callerid)) {
                    $call_type = 'OUT';
                    $VDADchannel_group = $campaign;
                } else {
                    $call_type = 'IN';
                    $stmt=sprintf("SELECT SQL_NO_CACHE campaign_id FROM osdial_closer_log WHERE lead_id='%s' ORDER BY call_date DESC LIMIT 1;",mres($lead_id));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $VDCL_mvac_ct = mysql_num_rows($rslt);
                    if ($VDCL_mvac_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $VDADchannel_group  =$row[0];
                    }
                }

                if ($DBFILE) debugLog('osdial_debug',"$NOW_TIME|INBND|$callerid|$user|$user_group|$list_id|$lead_id|$phone_number|$uniqueid|");
            }

            if ( ($call_type=='OUT') or ($call_type=='OUTBALANCE') ) {
                $stmt=sprintf("UPDATE osdial_log SET user='%s',comments='AUTO',list_id='%s',status='INCALL',user_group='%s' WHERE lead_id='%s' AND uniqueid='%s';",mres($user),mres($list_id),mres($user_group),mres($lead_id),mres($uniqueid));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("SELECT web_form_address,campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,allow_tab_switch,web_form_address2,web_form_extwindow,web_form2_extwindow FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDIG_cid_ct = mysql_num_rows($rslt);
                if ($VDIG_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $VDCL_web_form_address      = $row[0];
                    $VDCL_campaign_script       = $row[1];
                    $VDCL_get_call_launch       = $row[2];
                    $VDCL_xferconf_a_dtmf       = $row[3];
                    $VDCL_xferconf_a_number     = $row[4];
                    $VDCL_xferconf_b_dtmf       = $row[5];
                    $VDCL_xferconf_b_number     = $row[6];
                    $VDCL_default_xfer_group    = $row[7];
                    $VDCL_allow_tab_switch      = $row[8];
                    $VDCL_web_form_address2     = $row[9];
                    $VDCL_web_form_extwin       = $row[10];
                    $VDCL_web_form_extwin2      = $row[11];
                }

                $stmt=sprintf("SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='%s';",mres($list_id));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $list_cnt = mysql_num_rows($rslt);
                if ($list_cnt > 0) {
                    $row=mysql_fetch_row($rslt);
                    if (!empty($row[0])) $VDCL_web_form_address = $row[0];
                    if (!empty($row[1])) $VDCL_web_form_address2 = $row[1];
                    if (!empty($row[2])) $VDCL_campaign_script = $row[2];
                }

                # Get script override from user.
                $stmt=sprintf("SELECT script_override FROM osdial_users WHERE user='%s';",mres($user));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $user_cnt = mysql_num_rows($rslt);
                if ($user_cnt > 0) {
                    $row=mysql_fetch_row($rslt);
                    if (!empty($row[0])) $VDCL_campaign_script = $row[0];
                }

                echo "$VDCL_web_form_address|||||$VDCL_campaign_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_allow_tab_switch|$VDCL_web_form_address2|$VDCL_web_form_extwin|$VDCL_web_form_extwin2|\n|\n";

                $stmt=sprintf("SELECT SQL_NO_CACHE phone_number,alt_dial FROM osdial_auto_calls WHERE callerid='%s' ORDER BY call_time DESC LIMIT 1;",mres($callerid));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDAC_cid_ct = mysql_num_rows($rslt);
                if ($VDAC_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $dialed_number  =$row[0];
                    $dialed_label   =$row[1];
                } else {
                    $dialed_number = $phone_number;
                    $dialed_label = 'MAIN';
                }
            } else {
                ### update the osdial_closer_log user to INCALL
                $stmt=sprintf("UPDATE osdial_closer_log SET user='%s',comments='AUTO',list_id='%s',status='INCALL',user_group='%s' WHERE lead_id='%s' ORDER BY closecallid DESC LIMIT 1;",mres($user),mres($list_id),mres($user_group),mres($lead_id));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_log WHERE lead_id='%s' AND uniqueid='%s';",mres($lead_id),mres($uniqueid));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDL_cid_ct = mysql_num_rows($rslt);
                if ($VDL_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $VDCL_front_VDlog = $row[0];
                }

                $stmt=sprintf("SELECT * FROM osdial_inbound_groups WHERE group_id='%s';",mres($VDADchannel_group));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDIG_cid_ct = mysql_num_rows($rslt);
                if ($VDIG_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $VDCL_group_name        = $row[1];
                    $VDCL_group_color       = $row[2];
                    $VDCL_group_web         = $row[4];
                    $VDCL_fronter_display   = $row[7];
                    $VDCL_ingroup_script    = $row[8];
                    $VDCL_get_call_launch   = $row[9];
                    $VDCL_xferconf_a_dtmf   = $row[10];
                    $VDCL_xferconf_a_number = $row[11];
                    $VDCL_xferconf_b_dtmf   = $row[12];
                    $VDCL_xferconf_b_number = $row[13];
                    $VDCL_default_xfer_group= $row[28];
                    $VDCL_group_web2        = $row[29];
                    $VDCL_allow_tab_switch  = $row[30];
                    $VDCL_web_form_extwin   = $row[31];
                    $VDCL_web_form_extwin2  = $row[32];

                    $stmt=sprintf("SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='%s';",mres($list_id));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $list_cnt = mysql_num_rows($rslt);
                    if ($list_cnt > 0) {
                        $row=mysql_fetch_row($rslt);
                        if (!empty($row[0])) $VDCL_group_web = $row[0];
                        if (!empty($row[1])) $VDCL_group_web2 = $row[1];
                        if (!empty($row[2])) $VDCL_ingroup_script = $row[2];
                    }

                    $stmt=sprintf("SELECT campaign_script,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,web_form_address,web_form_address2 FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $VDIG_cidOR_ct = mysql_num_rows($rslt);
                    if ($VDIG_cidOR_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        if ( ( (OSDpreg_match('/NONE/',$VDCL_ingroup_script)) and (OSDstrlen($VDCL_ingroup_script) < 5) ) or (OSDstrlen($VDCL_ingroup_script) < 1) ) $VDCL_ingroup_script = $row[0];
                        if (OSDstrlen($VDCL_xferconf_a_dtmf) < 1) $VDCL_xferconf_a_dtmf =    $row[1];
                        if (OSDstrlen($VDCL_xferconf_a_number) < 1) $VDCL_xferconf_a_number =  $row[2];
                        if (OSDstrlen($VDCL_xferconf_b_dtmf) < 1) $VDCL_xferconf_b_dtmf =    $row[3];
                        if (OSDstrlen($VDCL_xferconf_b_number) < 1) $VDCL_xferconf_b_number =  $row[4];
                        if (OSDstrlen($VDCL_group_web) < 1) $VDCL_group_web =  $row[5];
                        if (OSDstrlen($VDCL_group_web2) < 1) $VDCL_group_web2 =  $row[6];
                    }


                    ### update the comments in osdial_live_agents record
                    $random = (rand(1000000, 9999999) + 10000000);
                    $stmt=sprintf("UPDATE osdial_live_agents SET comments='INBOUND',random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($random),mres($user),mres($server_ip));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);

                    $Ctype = 'I';
                } else {
                    $stmt=sprintf("SELECT campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,allow_tab_switch,web_form_address,web_form_address2 FROM osdial_campaigns WHERE campaign_id='%s';",mres($VDADchannel_group));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $VDIG_cid_ct = mysql_num_rows($rslt);
                    if ($VDIG_cid_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $VDCL_ingroup_script    = $row[0];
                        $VDCL_get_call_launch   = $row[1];
                        $VDCL_xferconf_a_dtmf   = $row[2];
                        $VDCL_xferconf_a_number = $row[3];
                        $VDCL_xferconf_b_dtmf   = $row[4];
                        $VDCL_xferconf_b_number = $row[5];
                        $VDCL_allow_tab_switch  = $row[6];
                        $VDCL_group_web         = $row[7];
                        $VDCL_group_web2        = $row[8];
                    }

                    $stmt=sprintf("SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='%s';",mres($list_id));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $list_cnt = mysql_num_rows($rslt);
                    if ($list_cnt > 0) {
                        $row=mysql_fetch_row($rslt);
                        if (!empty($row[0])) $VDCL_group_web = $row[0];
                        if (!empty($row[1])) $VDCL_group_web2 = $row[1];
                        if (!empty($row[2])) $VDCL_ingroup_script = $row[2];
                    }
                }

                # Get script override from user.
                $stmt=sprintf("SELECT script_override FROM osdial_users WHERE user='%s';",mres($user));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $user_cnt = mysql_num_rows($rslt);
                if ($user_cnt > 0) {
                    $row=mysql_fetch_row($rslt);
                    if (!empty($row[0])) $VDCL_ingroup_script = $row[0];
                }

                #### if web form is set then send on to osdial.php for override of WEB_FORM address
                echo "$VDCL_group_web|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_allow_tab_switch|$VDCL_group_web2|$VDCL_web_form_extwin|$VDCL_web_form_extwin2|\n";

                $stmt=sprintf("SELECT full_name FROM osdial_users WHERE user='%s';",mres($tsr));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDU_cid_ct = mysql_num_rows($rslt);
                if ($VDU_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $fronter_full_name  = $row[0];
                    echo $fronter_full_name . '|' . $tsr . "\n";
                } else {
                    echo '|' . $tsr . "\n";
                }
            }

            $comments = OSDpreg_replace("/\r/",'',$comments);
            $comments = OSDpreg_replace("/\n/",'!N',$comments);

            $phone_code = OSDpreg_replace('/^011|^010|^00/', '', $phone_code);

            $LeaD_InfO =    $callerid . "\n";
            $LeaD_InfO .=   $lead_id . "\n";
            $LeaD_InfO .=   $dispo . "\n";
            $LeaD_InfO .=   $tsr . "\n";
            $LeaD_InfO .=   $vendor_id . "\n";
            $LeaD_InfO .=   $list_id . "\n";
            $LeaD_InfO .=   $gmt_offset_now . "\n";
            $LeaD_InfO .=   $phone_code . "\n";
            $LeaD_InfO .=   $phone_number . "\n";
            $LeaD_InfO .=   $title . "\n";
            $LeaD_InfO .=   $first_name . "\n";
            $LeaD_InfO .=   $middle_initial . "\n";
            $LeaD_InfO .=   $last_name . "\n";
            $LeaD_InfO .=   $address1 . "\n";
            $LeaD_InfO .=   $address2 . "\n";
            $LeaD_InfO .=   $address3 . "\n";
            $LeaD_InfO .=   $city . "\n";
            $LeaD_InfO .=   $state . "\n";
            $LeaD_InfO .=   $province . "\n";
            $LeaD_InfO .=   $postal_code . "\n";
            $LeaD_InfO .=   $country_code . "\n";
            $LeaD_InfO .=   $gender . "\n";
            $LeaD_InfO .=   $date_of_birth . "\n";
            $LeaD_InfO .=   $alt_phone . "\n";
            $LeaD_InfO .=   $email . "\n";
            $LeaD_InfO .=   $custom1 . "\n";
            $LeaD_InfO .=   $comments . "\n";
            $LeaD_InfO .=   $called_count . "\n";
            $LeaD_InfO .=   $CBentry_time . "\n";
            $LeaD_InfO .=   $CBcallback_time . "\n";
            $LeaD_InfO .=   $CBuser . "\n";
            $LeaD_InfO .=   $CBcomments . "\n";
            $LeaD_InfO .=   $dialed_number . "\n";
            $LeaD_InfO .=   $dialed_label . "\n";
            $LeaD_InfO .=   $source_id . "\n";
            $LeaD_InfO .=   $custom2 . "\n";
            $LeaD_InfO .=   $external_key . "\n";
            $LeaD_InfO .=   $post_date . "\n";
            $LeaD_InfO .=   $organization . "\n";
            $LeaD_InfO .=   $organization_title . "\n";

            $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
            $cnt = 0;
            foreach ($forms as $form) {
                $fcamps = OSDpreg_split('/,/',$form['campaigns']);
                foreach ($fcamps as $fcamp) {
                    if ($fcamp == 'ALL' or OSDstrtoupper($fcamp) == OSDstrtoupper($campaign)) {
                        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', sprintf("deleted='0' AND form_id='%s'",mres($form['id'])) );
                        foreach ($fields as $field) {
                            $vdlf = get_first_record($link, 'osdial_list_fields', '*', sprintf("lead_id='%s' AND field_id='%s'",mres($lead_id),mres($field['id'])) );
                            $LeaD_InfO .= $vdlf['value'] . "\n";
                            $cnt++;
                        }
                    }
                }
            }
            echo $LeaD_InfO;

            $stmt=sprintf("SELECT SQL_NO_CACHE start_epoch FROM osdial_log WHERE uniqueid='%s' LIMIT 1;",mres($uniqueid));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $OLstart_epoch = $row[0];

            $pause_epoch='';
            $wait_epoch='';
            $talk_epoch='';
            $wait_sec=0;
            $stmt=sprintf("SELECT SQL_NO_CACHE pause_epoch,wait_epoch,talk_epoch,dispo_epoch FROM osdial_agent_log WHERE agent_log_id='%s';",mres($agent_log_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $VDpr_ct = mysql_num_rows($rslt);
            if ($VDpr_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $pause_epoch=$row[0];
                if (OSDstrlen($pause_epoch)<5) $pause_epoch=$StarTtime;
                $wait_epoch=$row[1];
                if (OSDstrlen($wait_epoch)<5) $wait_epoch=$pause_epoch;
                $talk_epoch=$row[2];
                if ($OLstart_epoch>0) $talk_epoch=$OLstart_epoch;
                $dispo_epoch=$row[3];
                if (OSDstrlen($talk_epoch)<5) $talk_epoch=$StarTtime;
                $dispo_epoch=$StarTtime;
                $pause_sec = ($wait_epoch - $pause_epoch);
                if ($pause_sec<0) $pause_sec=0;
                $wait_sec = ($talk_epoch - $wait_epoch);
                if ($wait_sec<0) $wait_sec=0;
                $talk_sec = ($dispo_epoch - $talk_epoch);
                if ($talk_sec<0) $talk_sec=0;
            }
            $stmt=sprintf("UPDATE osdial_agent_log SET pause_epoch='%s',pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',talk_sec='%s',dispo_epoch='%s',lead_id='%s',uniqueid='%s',prev_status='%s',lead_called_count='%s' WHERE agent_log_id='%s';",mres($pause_epoch),mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($talk_epoch),mres($talk_sec),mres($dispo_epoch),mres($lead_id),mres($uniqueid),mres($dispo),mres($called_count),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            ### If CALLBK, change osdial_callback record to INACTIVE
            if (OSDpreg_match("/CALLBK|CBHOLD/", $dispo)) {
                $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE lead_id='$lead_id' AND status NOT IN('INACTIVE','DEAD','ARCHIVE');",mres($lead_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }

            if ($config['settings']['enable_agc_xfer_log'] > 0) {
                #	DATETIME|campaign|lead_id|phone_number|user|type
                #	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|A
                if ($DBFILE) debugLog('xfer_log',"$NOW_TIME|$campaign|$lead_id|$phone_number|$user|$Ctype");
            }

        } else {
            echo "0\n";
            #echo "No calls in QUEUE for $user on $server_ip\n";
            exit;
        }
    }
}


################################################################################
### multicallQueueSwap - for auto-dial OSDiaL dialing this will check for calls
###                     in the osdial_live_agents table in QUEUE status, then
###                     lookup the lead info and pass it back to osdial.php
################################################################################
if ($ACTION == 'multicallQueueSwap') {
    $Ctype = 'A';
    $MT[0]='';
    $row='';   $rowx='';
    $channel_live=1;
    if ( (OSDstrlen($campaign)<1) || (OSDstrlen($server_ip)<1) ) {
        $channel_live=0;
        echo "0\n";
        echo "Campaign $campaign is not valid\n";
        exit;
    } else {

        if (!empty($channel)) {
            if (empty($park_on_extension)) $park_on_extension='8301';
            $queryCID = "LPvdcW$StarTtime$user_abb";
            $stmt=sprintf("INSERT INTO parked_channels VALUES('%s','%s','','park','%s','%s');",mres($channel),mres($server_ip),mres($agentchannel),mres($NOW_TIME));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Redirect','%s','Channel: %s','Context: osdial','Exten: %s','Priority: 1','CallerID: %s','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($channel),mres($queryCID),mres($channel),mres($park_on_extension),mres($queryCID),mres($queryCID));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("SELECT pause_epoch,wait_epoch,talk_epoch,dispo_epoch FROM osdial_agent_log WHERE agent_log_id='%s' LIMIT 1;",mres($agent_log_id));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $pause_epoch=$row[0];
            $wait_epoch=$row[1];
            $talk_epoch=$row[2];
            if (OSDstrlen($talk_epoch)<5) $talk_epoch=$StarTtime;
            $dispo_epoch=$row[3];
            $dispo_epoch=$StarTtime;
            $pause_sec = ($wait_epoch-$pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch-$wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch-$talk_epoch);
            if ($talk_sec<0) $talk_sec=0;

            $stmt=sprintf("UPDATE osdial_agent_log SET pause_epoch='%s',pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',talk_sec='%s',dispo_epoch='%s' WHERE agent_log_id='%s';",mres($pause_epoch),mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($talk_epoch),mres($talk_sec),mres($dispo_epoch),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

        }

        $stmt=sprintf("SELECT lead_id,campaign_id,uniqueid,callerid,NOW()-call_time AS seconds FROM osdial_auto_calls WHERE server_ip='%s' AND channel='%s';",mres($multicall_serverip),mres($multicall_channel));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $auto_call_ct = mysql_num_rows($rslt);

        $mccalltime=0;
        if ($auto_call_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $mcleadid = $row[0];
            $mcingroup = $row[1];
            $mcuniqueid = $row[2];
            $mccallerid = $row[3];
            $mccalltime = $row[4];

            $dest_dialstring = $conf_exten;
            if ($multicall_serverip!=$agentserver_ip) {
                $S='*';
                $D_s_ip = explode('.', $agentserver_ip);
                if (OSDstrlen($D_s_ip[0])<2) {$D_s_ip[0] = "0$D_s_ip[0]";}
                if (OSDstrlen($D_s_ip[0])<3) {$D_s_ip[0] = "0$D_s_ip[0]";}
                if (OSDstrlen($D_s_ip[1])<2) {$D_s_ip[1] = "0$D_s_ip[1]";}
                if (OSDstrlen($D_s_ip[1])<3) {$D_s_ip[1] = "0$D_s_ip[1]";}
                if (OSDstrlen($D_s_ip[2])<2) {$D_s_ip[2] = "0$D_s_ip[2]";}
                if (OSDstrlen($D_s_ip[2])<3) {$D_s_ip[2] = "0$D_s_ip[2]";}
                if (OSDstrlen($D_s_ip[3])<2) {$D_s_ip[3] = "0$D_s_ip[3]";}
                if (OSDstrlen($D_s_ip[3])<3) {$D_s_ip[3] = "0$D_s_ip[3]";}
                $dest_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$config[settings][intra_server_sep]$conf_exten";
            }

            $stmt=sprintf("DELETE FROM parked_channels WHERE server_ip='%s' AND channel='%s';",mres($multicall_serverip),mres($multicall_channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $queryCID = "LFvdcW$StarTtime$user_abb";
            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','%s','%s','NEW','N','%s','%s','Redirect','%s','Channel: %s','Context: osdial','Exten: %s','Priority: 1','CallerID: %s','Account: %s','','','','');",mres($mcuniqueid),mres($NOW_TIME),mres($multicall_serverip),mres($multicall_channel),mres($queryCID),mres($multicall_channel),mres($dest_dialstring),mres($queryCID),mres($mccallerid));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("UPDATE osdial_auto_calls SET status='CLOSER',stage='CLOSER-0' WHERE server_ip='%s' AND channel='%s';",mres($multicall_serverip),mres($multicall_channel));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("UPDATE osdial_live_agents SET lead_id='%s',uniqueid='%s',callerid='%s',call_server_ip='%s',channel='%s',status='QUEUE' WHERE user='%s' AND server_ip='%s';",mres($mcleadid),mres($mcuniqueid),mres($mccallerid),mres($multicall_serverip),mres($multicall_channel),mres($user),mres($agentserver_ip));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
        }

        ### grab the call and lead info from the osdial_live_agents table
        $stmt=sprintf("SELECT SQL_NO_CACHE lead_id,uniqueid,callerid,channel,call_server_ip,campaign_id FROM osdial_live_agents WHERE server_ip='%s' AND user='%s' AND status='QUEUE';",mres($server_ip),mres($user));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $queue_leadID_ct = mysql_num_rows($rslt);

        if ($queue_leadID_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $lead_id	=$row[0];
            $uniqueid	=$row[1];
            $callerid	=$row[2];
            $channel	=$row[3];
            $call_server_ip	=$row[4];
            if (OSDstrlen($call_server_ip)<7) $call_server_ip = $server_ip;
            $campaign	=$row[5];

            $stmt=sprintf("SELECT agent_log_id,pause_epoch,wait_epoch,talk_epoch,dispo_epoch FROM osdial_agent_log WHERE uniqueid='%s' LIMIT 1;",mres($uniqueid));
            $rslt=mysql_query($stmt, $link);
            $row2=mysql_fetch_row($rslt);
            $agent_log_id=$row2[0];
            $pause_epoch=$row2[1];
            $wait_epoch=$row2[2];
            $talk_epoch=$row2[3];
            if (OSDstrlen($talk_epoch)<5) $talk_epoch=$StarTtime;
            $dispo_epoch=$row2[4];
            $dispo_epoch=$StarTtime;
            $pause_sec = ($wait_epoch-$pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch-$wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch-$talk_epoch);
            if ($talk_sec<0) $talk_sec=0;

            $stmt=sprintf("UPDATE osdial_agent_log SET pause_epoch='%s',pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',talk_sec='%s',dispo_epoch='%s' WHERE agent_log_id='%s';",mres($pause_epoch),mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($talk_epoch),mres($talk_sec),mres($dispo_epoch),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $agent_log_type='TALK';
            $agent_log_time = $talk_sec;

            echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . '|' . $mccalltime . '|' . $agent_log_id . '|' . $agent_log_type . '|' . $agent_log_time . "|\n";
        } else {
            $lead_id	=$multicall_leadid;
            $uniqueid	=$multicall_uniqueid;
            $callerid	=$multicall_callerid;
            $channel	=$multicall_channel;
            $call_server_ip	=$multicall_serverip;
            $campaign	=$campaign;

            $stmt=sprintf("SELECT agent_log_id,pause_epoch,wait_epoch,talk_epoch,dispo_epoch FROM osdial_agent_log WHERE uniqueid='%s' LIMIT 1;",mres($multicall_uniqueid));
            $rslt=mysql_query($stmt, $link);
            $row2=mysql_fetch_row($rslt);
            $mcagent_log_id=$row2[0];
            $pause_epoch=$row2[1];
            $wait_epoch=$row2[2];
            $talk_epoch=$row2[3];
            if (OSDstrlen($talk_epoch)<5) $talk_epoch=$StarTtime;
            $dispo_epoch=$row2[4];
            $dispo_epoch=$StarTtime;
            $pause_sec = ($wait_epoch-$pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch-$wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch-$talk_epoch);
            if ($talk_sec<0) $talk_sec=0;

            $stmt=sprintf("UPDATE osdial_agent_log SET pause_epoch='%s',pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',talk_sec='%s',dispo_epoch='%s' WHERE agent_log_id='%s';",mres($pause_epoch),mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($talk_epoch),mres($talk_sec),mres($dispo_epoch),mres($mcagent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $mcagent_log_type='TALK';
            $mcagent_log_time = $talk_sec;

            $agent_log_id = $mcagent_log_id;
            $agent_log_type = $mcagent_log_type;
            $agent_log_time = $mcagent_log_time;
            echo "2\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . '|' . $mccalltime . '|' . $agent_log_id . '|' . $agent_log_type . '|' . $agent_log_time . "|\n";
        }

        if (!empty($lead_id)) {
            ##### grab number of calls today in this campaign and increment
            $stmt=sprintf("SELECT SQL_NO_CACHE calls_today FROM osdial_live_agents WHERE user='%s' AND campaign_id='%s';",mres($user),mres($campaign));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $vla_cc_ct = mysql_num_rows($rslt);
            if ($vla_cc_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $calls_today =$row[0];
            } else {
                $calls_today ='0';
            }
            $calls_today++;

            ### update the agent status to INCALL in osdial_live_agents
            $random = (rand(1000000, 9999999) + 10000000);
            $stmt=sprintf("UPDATE osdial_live_agents SET status='INCALL',last_call_time='%s',comments='AUTO',calls_today='%s',random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($NOW_TIME),mres($calls_today),mres($random),mres($user),mres($server_ip));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("UPDATE osdial_campaign_agents SET calls_today='%s' WHERE user='%s' AND campaign_id='%s';",mres($calls_today),mres($user),mres($campaign));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            ##### grab the data from osdial_list for the lead_id
            $stmt=sprintf("SELECT * FROM osdial_list WHERE lead_id='%s' LIMIT 1;",mres($lead_id));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $list_lead_ct = mysql_num_rows($rslt);
            if ($list_lead_ct > 0) {
                $row=mysql_fetch_row($rslt);
                #$lead_id		= trim($row[0]);
                $dispo			= trim($row[3]);
                $tsr			= trim($row[4]);
                $vendor_id		= trim($row[5]);
                $source_id		= trim($row[6]);
                $list_id		= trim($row[7]);
                $gmt_offset_now	= trim($row[8]);
                $phone_code		= trim($row[10]);
                $phone_number	= trim($row[11]);
                $title			= trim($row[12]);
                $first_name		= trim($row[13]);
                $middle_initial	= trim($row[14]);
                $last_name		= trim($row[15]);
                $address1		= trim($row[16]);
                $address2		= trim($row[17]);
                $address3		= trim($row[18]);
                $city			= trim($row[19]);
                $state			= trim($row[20]);
                $province		= trim($row[21]);
                $postal_code	= trim($row[22]);
                $country_code	= trim($row[23]);
                $gender			= trim($row[24]);
                $date_of_birth	= trim($row[25]);
                $alt_phone		= trim($row[26]);
                $email			= trim($row[27]);
                $custom1		= trim($row[28]);
                $comments		= stripslashes(trim($row[29]));
                $called_count	= trim($row[30]);
                $custom2		= trim($row[31]);
                $external_key	= trim($row[32]);
                $post_date  	= trim($row[35]);
                $organization  	= trim($row[36]);
                $organization_title = trim($row[37]);
            }

            ##### if lead is a callback, grab the callback comments
            $CBentry_time =		'';
            $CBcallback_time =	'';
            $CBuser =			'';
            $CBcomments =		'';
            if (OSDpreg_match("/CALLBK/",$dispo)) {
                $stmt=sprintf("SELECT entry_time,callback_time,user,comments FROM osdial_callbacks WHERE lead_id='%s' ORDER BY callback_id DESC LIMIT 1;",mres($lead_id));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $cb_record_ct = mysql_num_rows($rslt);
                if ($cb_record_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $CBentry_time =		trim($row[0]);
                    $CBcallback_time =	trim($row[1]);
                    $CBuser =			trim($row[2]);
                    $CBcomments =		trim($row[3]);
                }
            }

            ### update the lead status to INCALL
            $stmt=sprintf("UPDATE osdial_list SET status='INCALL',user='%s' WHERE lead_id='%s';",mres($user),mres($lead_id));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);

            ### update the log status to INCALL
            $user_group='';
            $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $ug_record_ct = mysql_num_rows($rslt);
            if ($ug_record_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $user_group =		trim($row[0]);
            }

            $stmt=sprintf("SELECT SQL_NO_CACHE campaign_id,phone_number,alt_dial,call_type FROM osdial_auto_calls WHERE callerid='%s' ORDER BY call_time DESC LIMIT 1;",mres($callerid));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $VDAC_cid_ct = mysql_num_rows($rslt);
            if ($VDAC_cid_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $VDADchannel_group	=$row[0];
                $dialed_number		=$row[1];
                $dialed_label		=$row[2];
                $call_type			=$row[3];
            } else {
                $dialed_number = $phone_number;
                $dialed_label = 'MAIN';
                if (OSDpreg_match('/^M|^V/',$callerid)) {
                    $call_type = 'OUT';
                    $VDADchannel_group = $campaign;
                } else {
                    $call_type = 'IN';
                    $stmt=sprintf("SELECT SQL_NO_CACHE campaign_id FROM osdial_closer_log WHERE lead_id='%s' ORDER BY call_date DESC LIMIT 1;",mres($lead_id));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $VDCL_mvac_ct = mysql_num_rows($rslt);
                    if ($VDCL_mvac_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $VDADchannel_group  =$row[0];
                    }
                }

                if ($DBFILE) debugLog('osdial_debug',"$NOW_TIME|INBND|$callerid|$user|$user_group|$list_id|$lead_id|$phone_number|$uniqueid|");
            }

            if ( ($call_type=='OUT') or ($call_type=='OUTBALANCE') ) {
                $stmt=sprintf("UPDATE osdial_log SET user='%s',comments='AUTO',list_id='%s',status='INCALL',user_group='%s' WHERE lead_id='%s' AND uniqueid='%s';",mres($user),mres($list_id),mres($user_group),mres($lead_id),mres($uniqueid));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("SELECT web_form_address,campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,default_xfer_group,allow_tab_switch,web_form_address2,web_form_extwindow,web_form2_extwindow FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDIG_cid_ct = mysql_num_rows($rslt);
                if ($VDIG_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $VDCL_web_form_address      = $row[0];
                    $VDCL_campaign_script       = $row[1];
                    $VDCL_get_call_launch       = $row[2];
                    $VDCL_xferconf_a_dtmf       = $row[3];
                    $VDCL_xferconf_a_number     = $row[4];
                    $VDCL_xferconf_b_dtmf       = $row[5];
                    $VDCL_xferconf_b_number     = $row[6];
                    $VDCL_default_xfer_group    = $row[7];
                    $VDCL_allow_tab_switch      = $row[8];
                    $VDCL_web_form_address2     = $row[9];
                    $VDCL_web_form_extwin       = $row[10];
                    $VDCL_web_form_extwin2      = $row[11];
                }

                $stmt=sprintf("SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='%s';",mres($list_id));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $list_cnt = mysql_num_rows($rslt);
                if ($list_cnt > 0) {
                    $row=mysql_fetch_row($rslt);
                    if (!empty($row[0])) $VDCL_web_form_address = $row[0];
                    if (!empty($row[1])) $VDCL_web_form_address2 = $row[1];
                    if (!empty($row[2])) $VDCL_campaign_script = $row[2];
                }

                # Get script override from user.
                $stmt=sprintf("SELECT script_override FROM osdial_users WHERE user='%s';",mres($user));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $user_cnt = mysql_num_rows($rslt);
                if ($user_cnt > 0) {
                    $row=mysql_fetch_row($rslt);
                    if (!empty($row[0])) $VDCL_campaign_script = $row[0];
                }

                echo "$VDCL_web_form_address|||||$VDCL_campaign_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_allow_tab_switch|$VDCL_web_form_address2|$VDCL_web_form_extwin|$VDCL_web_form_extwin2|\n|\n";

                $stmt=sprintf("SELECT SQL_NO_CACHE phone_number,alt_dial FROM osdial_auto_calls WHERE callerid='%s' ORDER BY call_time DESC LIMIT 1;",mres($callerid));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDAC_cid_ct = mysql_num_rows($rslt);
                if ($VDAC_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $dialed_number  =$row[0];
                    $dialed_label   =$row[1];
                } else {
                    $dialed_number = $phone_number;
                    $dialed_label = 'MAIN';
                }
            } else {
                ### update the osdial_closer_log user to INCALL
                $stmt=sprintf("UPDATE osdial_closer_log SET user='%s',comments='AUTO',list_id='%s',status='INCALL',user_group='%s' WHERE lead_id='%s' ORDER BY closecallid DESC LIMIT 1;",mres($user),mres($list_id),mres($user_group),mres($lead_id));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_log WHERE lead_id='%s' AND uniqueid='%s';",mres($lead_id),mres($uniqueid));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDL_cid_ct = mysql_num_rows($rslt);
                if ($VDL_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $VDCL_front_VDlog = $row[0];
                }

                $stmt=sprintf("SELECT * FROM osdial_inbound_groups WHERE group_id='%s';",mres($VDADchannel_group));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDIG_cid_ct = mysql_num_rows($rslt);
                if ($VDIG_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $VDCL_group_name        = $row[1];
                    $VDCL_group_color       = $row[2];
                    $VDCL_group_web         = $row[4];
                    $VDCL_fronter_display   = $row[7];
                    $VDCL_ingroup_script    = $row[8];
                    $VDCL_get_call_launch   = $row[9];
                    $VDCL_xferconf_a_dtmf   = $row[10];
                    $VDCL_xferconf_a_number = $row[11];
                    $VDCL_xferconf_b_dtmf   = $row[12];
                    $VDCL_xferconf_b_number = $row[13];
                    $VDCL_default_xfer_group= $row[28];
                    $VDCL_group_web2        = $row[29];
                    $VDCL_allow_tab_switch  = $row[30];
                    $VDCL_web_form_extwin   = $row[31];
                    $VDCL_web_form_extwin2  = $row[32];

                    $stmt=sprintf("SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='$list_id';",mres($list_id));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $list_cnt = mysql_num_rows($rslt);
                    if ($list_cnt > 0) {
                        $row=mysql_fetch_row($rslt);
                        if (!empty($row[0])) $VDCL_group_web = $row[0];
                        if (!empty($row[1])) $VDCL_group_web2 = $row[1];
                        if (!empty($row[2])) $VDCL_ingroup_script = $row[2];
                    }

                    $stmt=sprintf("SELECT campaign_script,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,web_form_address,web_form_address2 FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $VDIG_cidOR_ct = mysql_num_rows($rslt);
                    if ($VDIG_cidOR_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        if ( ( (OSDpreg_match('/NONE/',$VDCL_ingroup_script)) and (OSDstrlen($VDCL_ingroup_script) < 5) ) or (OSDstrlen($VDCL_ingroup_script) < 1) ) $VDCL_ingroup_script = $row[0];
                        if (OSDstrlen($VDCL_xferconf_a_dtmf) < 1) $VDCL_xferconf_a_dtmf =    $row[1];
                        if (OSDstrlen($VDCL_xferconf_a_number) < 1) $VDCL_xferconf_a_number =  $row[2];
                        if (OSDstrlen($VDCL_xferconf_b_dtmf) < 1) $VDCL_xferconf_b_dtmf =    $row[3];
                        if (OSDstrlen($VDCL_xferconf_b_number) < 1) $VDCL_xferconf_b_number =  $row[4];
                        if (OSDstrlen($VDCL_group_web) < 1) $VDCL_group_web =  $row[5];
                        if (OSDstrlen($VDCL_group_web2) < 1) $VDCL_group_web2 =  $row[6];
                    }


                    ### update the comments in osdial_live_agents record
                    $random = (rand(1000000, 9999999) + 10000000);
                    $stmt=sprintf("UPDATE osdial_live_agents SET comments='INBOUND',random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($random),mres($user),mres($server_ip));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);

                    $Ctype = 'I';
                } else {
                    $stmt=sprintf("SELECT campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,allow_tab_switch,web_form_address,web_form_address2 FROM osdial_campaigns WHERE campaign_id='%s';",mres($VDADchannel_group));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $VDIG_cid_ct = mysql_num_rows($rslt);
                    if ($VDIG_cid_ct > 0) {
                        $row=mysql_fetch_row($rslt);
                        $VDCL_ingroup_script    = $row[0];
                        $VDCL_get_call_launch   = $row[1];
                        $VDCL_xferconf_a_dtmf   = $row[2];
                        $VDCL_xferconf_a_number = $row[3];
                        $VDCL_xferconf_b_dtmf   = $row[4];
                        $VDCL_xferconf_b_number = $row[5];
                        $VDCL_allow_tab_switch  = $row[6];
                        $VDCL_group_web         = $row[7];
                        $VDCL_group_web2        = $row[8];
                    }

                    $stmt=sprintf("SELECT web_form_address,web_form_address2,list_script FROM osdial_lists WHERE list_id='%s';",mres($list_id));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $list_cnt = mysql_num_rows($rslt);
                    if ($list_cnt > 0) {
                        $row=mysql_fetch_row($rslt);
                        if (!empty($row[0])) $VDCL_group_web = $row[0];
                        if (!empty($row[1])) $VDCL_group_web2 = $row[1];
                        if (!empty($row[2])) $VDCL_ingroup_script = $row[2];
                    }
                }

                # Get script override from user.
                $stmt=sprintf("SELECT script_override FROM osdial_users WHERE user='%s';",mres($user));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $user_cnt = mysql_num_rows($rslt);
                if ($user_cnt > 0) {
                    $row=mysql_fetch_row($rslt);
                    if (!empty($row[0])) $VDCL_ingroup_script = $row[0];
                }

                #### if web form is set then send on to osdial.php for override of WEB_FORM address
                echo "$VDCL_group_web|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|$VDCL_default_xfer_group|$VDCL_allow_tab_switch|$VDCL_group_web2|$VDCL_web_form_extwin|$VDCL_web_form_extwin2|\n";

                $stmt=sprintf("SELECT full_name FROM osdial_users WHERE user='%s';",mres($tsr));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $VDU_cid_ct = mysql_num_rows($rslt);
                if ($VDU_cid_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $fronter_full_name  = $row[0];
                    echo $fronter_full_name . '|' . $tsr . "\n";
                } else {
                    echo '|' . $tsr . "\n";
                }
            }

            $comments = OSDpreg_replace("/\r/",'',$comments);
            $comments = OSDpreg_replace("/\n/",'!N',$comments);

            $phone_code = OSDpreg_replace('/^011|^010|^00/', '', $phone_code);

            $LeaD_InfO =    $callerid . "\n";
            $LeaD_InfO .=   $lead_id . "\n";
            $LeaD_InfO .=   $dispo . "\n";
            $LeaD_InfO .=   $tsr . "\n";
            $LeaD_InfO .=   $vendor_id . "\n";
            $LeaD_InfO .=   $list_id . "\n";
            $LeaD_InfO .=   $gmt_offset_now . "\n";
            $LeaD_InfO .=   $phone_code . "\n";
            $LeaD_InfO .=   $phone_number . "\n";
            $LeaD_InfO .=   $title . "\n";
            $LeaD_InfO .=   $first_name . "\n";
            $LeaD_InfO .=   $middle_initial . "\n";
            $LeaD_InfO .=   $last_name . "\n";
            $LeaD_InfO .=   $address1 . "\n";
            $LeaD_InfO .=   $address2 . "\n";
            $LeaD_InfO .=   $address3 . "\n";
            $LeaD_InfO .=   $city . "\n";
            $LeaD_InfO .=   $state . "\n";
            $LeaD_InfO .=   $province . "\n";
            $LeaD_InfO .=   $postal_code . "\n";
            $LeaD_InfO .=   $country_code . "\n";
            $LeaD_InfO .=   $gender . "\n";
            $LeaD_InfO .=   $date_of_birth . "\n";
            $LeaD_InfO .=   $alt_phone . "\n";
            $LeaD_InfO .=   $email . "\n";
            $LeaD_InfO .=   $custom1 . "\n";
            $LeaD_InfO .=   $comments . "\n";
            $LeaD_InfO .=   $called_count . "\n";
            $LeaD_InfO .=   $CBentry_time . "\n";
            $LeaD_InfO .=   $CBcallback_time . "\n";
            $LeaD_InfO .=   $CBuser . "\n";
            $LeaD_InfO .=   $CBcomments . "\n";
            $LeaD_InfO .=   $dialed_number . "\n";
            $LeaD_InfO .=   $dialed_label . "\n";
            $LeaD_InfO .=   $source_id . "\n";
            $LeaD_InfO .=   $custom2 . "\n";
            $LeaD_InfO .=   $external_key . "\n";
            $LeaD_InfO .=   $post_date . "\n";
            $LeaD_InfO .=   $organization . "\n";
            $LeaD_InfO .=   $organization_title . "\n";

            $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
            $cnt = 0;
            foreach ($forms as $form) {
                $fcamps = OSDpreg_split('/,/',$form['campaigns']);
                foreach ($fcamps as $fcamp) {
                    if ($fcamp == 'ALL' or OSDstrtoupper($fcamp) == OSDstrtoupper($campaign)) {
                        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', sprintf("deleted='0' AND form_id='%s'",mres($form['id'])) );
                        foreach ($fields as $field) {
                            $vdlf = get_first_record($link, 'osdial_list_fields', '*', sprintf("lead_id='%s' AND field_id='%s'",mres($lead_id),mres($field['id'])) );
                            $LeaD_InfO .= $vdlf['value'] . "\n";
                            $cnt++;
                        }
                    }
                }
            }
            echo $LeaD_InfO;

            $stmt=sprintf("UPDATE osdial_agent_log SET lead_id='%s',uniqueid='%s',prev_status='%s',lead_called_count='%s' WHERE agent_log_id='%s';",mres($lead_id),mres($uniqueid),mres($dispo),mres($called_count),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            ### If CALLBK, change osdial_callback record to INACTIVE
            if (OSDpreg_match("/CALLBK|CBHOLD/", $dispo)) {
                $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE lead_id='%s' AND status NOT IN('INACTIVE','DEAD','ARCHIVE');",mres($lead_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }

            if ($config['settings']['enable_agc_xfer_log'] > 0) {
                #	DATETIME|campaign|lead_id|phone_number|user|type
                #	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|A
                if ($DBFILE) debugLog('xfer_log',"$NOW_TIME|$campaign|$lead_id|$phone_number|$user|$Ctype");
            }

        } else {
            echo "0\n";
            #echo "No calls in QUEUE for $user on $server_ip\n";
            exit;
        }
    }
}


################################################################################
### userLOGout - Logs the user out of OSDiaL client, deleting db records and 
###              inserting into osdial_user_log
################################################################################
if ($ACTION == 'userLOGout') {
    $MT[0]='';
    $row='';   $rowx='';
    if ( (OSDstrlen($campaign)<1) || (OSDstrlen($conf_exten)<1) ) {
        echo "NO\n";
        echo "campaign $campaign or conf_exten $conf_exten is not valid\n";
        exit;
    } else {
        $user_group='';
        $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
        $rslt=mysql_query($stmt, $link);
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $ug_record_ct = mysql_num_rows($rslt);
        if ($ug_record_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $user_group = trim($row[0]);
        }
        ##### Insert a LOGOUT record into the user log
        $stmt=sprintf("INSERT INTO osdial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) VALUES('%s','LOGOUT','%s','%s','%s','%s');",mres($user),mres($campaign),mres($NOW_TIME),mres($StarTtime),mres($user_group));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $vul_insert = mysql_affected_rows($link);

        if ($no_delete_sessions < 1) {
            ##### Remove the reservation on the osdial_conferences meetme room
            $stmt=sprintf("UPDATE osdial_conferences SET extension='' WHERE server_ip='%s' AND conf_exten='%s';",mres($server_ip),mres($conf_exten));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $vc_remove = mysql_affected_rows($link);
        }

        ##### Delete the osdial_live_agents record for this session
        $stmt=sprintf("DELETE FROM osdial_live_agents WHERE server_ip='%s' AND user='%s';",mres($server_ip),mres($user));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $vla_delete = mysql_affected_rows($link);

        ##### Delete the osdial_live_inbound_agents records for this session
        $stmt=sprintf("DELETE FROM osdial_live_inbound_agents WHERE user='%s';",mres($user));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $vlia_delete = mysql_affected_rows($link);

        ##### Delete the web_client_sessions
        $stmt=sprintf("DELETE FROM web_client_sessions WHERE server_ip='%s' AND session_name='%s';",mres($server_ip),mres($session_name));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $wcs_delete = mysql_affected_rows($link);

        ##### Hangup the client phone
        $stmt=sprintf("SELECT SQL_NO_CACHE channel,channel_group FROM live_sip_channels WHERE server_ip='%s' AND channel LIKE '%s/%s%%' ORDER BY channel DESC;",mres($server_ip),mres($protocol),mres($extension));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        if ($rslt) {
            $row=mysql_fetch_row($rslt);
            $agent_channel = $row[0];
            if (OSDpreg_match('/^Local/',$row[0]) and OSDpreg_match('/^'.addcslashes($row[0],'/@').'\-/',$row[1])) $agent_channel=$row[1];
            if ($format=='debug') echo "\n<!-- $row[0] -->";
            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','%s','Hangup','ULGH3459%s','Channel: %s','','','','','','','','','');",mres($NOW_TIME),mres($server_ip),mres($agent_channel),mres($StarTtime),mres($agent_channel));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        if ($LogouTKicKAlL > 0) {
            $local_DEF = 'Local/5555';
            $local_AMP = '@';
            $kick_local_channel = "$local_DEF$conf_exten$local_AMP$ext_context";
            $queryCID = "ULGH3458$StarTtime";

            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Originate','%s','Channel: %s','Context: %s','Exten: 8300','Priority: 1','Callerid: %s','Account: %s','','','%s','%s');",mres($NOW_TIME),mres($server_ip),mres($queryCID),mres($kick_local_channel),mres($ext_context),mres($queryCID),mres($queryCID),mres($channel),mres($exten));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        $pause_sec=0;
        $stmt=sprintf("SELECT SQL_NO_CACHE pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec FROM osdial_agent_log WHERE agent_log_id='%s';",mres($agent_log_id));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $pause_epoch = $row[0];
            $pause_sec   = $row[1];
            $wait_epoch  = $row[2];
            $wait_sec    = $row[3];
            $talk_epoch  = $row[4];
            $talk_sec    = $row[5];
            $dispo_epoch = $row[6];
            $dispo_sec   = $row[7];

            if (OSDstrlen($wait_epoch)<5) $wait_epoch = $StarTtime;
            if (OSDstrlen($talk_epoch)<5) $talk_epoch = $StarTtime;
            if (OSDstrlen($dispo_epoch)<5) $dispo_epoch = $StarTtime;
            $pause_sec = ($wait_epoch - $pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch - $wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch - $talk_epoch);
            if ($talk_sec<0) $talk_sec=0;
            $dispo_sec = ($StarTtime - $talk_epoch);
            if ($dispo_sec<0) $dispo_sec=0;

            $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',talk_sec='%s',dispo_epoch='%s',dispo_sec='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($talk_epoch),mres($talk_sec),mres($dispo_epoch),mres($dispo_sec),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        if ($vla_delete > 0) {
            if ( ($enable_sipsak_messages > 0) and ($config['settings']['allow_sipsak_messages'] > 0) and (OSDpreg_match("/SIP/",$protocol)) ) {
                $SIPSAK_message = 'LOGGED OUT';
                passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_message\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
            }

            if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                $stmt=sprintf("SELECT time_id FROM queue_log WHERE agent='Agent/%s' AND verb='AGENTLOGIN' ORDER BY time_id DESC LIMIT 1;",mres($user));
                $rslt=mysql_query($stmt, $linkB);
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $li_conf_ct = mysql_num_rows($rslt);
                $i=0;
                while ($i < $li_conf_ct) {
                    $row=mysql_fetch_row($rslt);
                    $logintime = $row[0];
                    $i++;
                }

                $time_logged_in = ($StarTtime - $logintime);
                if ($time_logged_in > 1000000) $time_logged_in=1;

                $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='%s',agent='Agent/%s',verb='AGENTLOGOFF',data1='%s',data2='%s',serverid='%s';",mres($StarTtime),mres($campaign),mres($user),mres($user.$agents),mres($time_logged_in),mres($config['settings']['queuemetrics_log_id']));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $linkB);
                $affected_rows = mysql_affected_rows($linkB);
                mysql_close($linkB);
            }
        }

        echo "$vul_insert|$vc_remove|$vla_delete|$wcs_delete|$agent_channel|$vlia_delete\n";
    }
}


################################################################################
### updateDISPO - update the osdial_list table to reflect the agent choice of
###               call disposition for that leand
################################################################################
if ($ACTION == 'updateDISPO') {
    $MT[0]='';
    $row='';   $rowx='';
    if ( (OSDstrlen($dispo_choice)<1) || (OSDstrlen($lead_id)<1) ) {
        echo "Dispo Choice $dispo or lead_id $lead_id is not valid\n";
        exit;
    } else {
        $phone_local_gmt = $phone_local_gmt * 1;
        $phone_gmt = $phone_gmt * 1;
        $tzs = parseTimezones();
        $tzoffsets = $tzs['tzoffsets'];
        $tzrefid = $tzs['tzrefid'];
        $phoneGMTname = $tzrefid[$tzoffsets[$phone_gmt]];
        $phonetz = new Date_TimeZone($phoneGMTname);
        $phoneDST = $phonetz->inDaylightTime(new Date(time()));
        $phoneDST = $phoneDST * 1;

        $random = (rand(1000000, 9999999) + 10000000);
        $stmt=sprintf("UPDATE osdial_live_agents SET comments='',lead_id='',last_call_finish='%s',random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($NOW_TIME),mres($random),mres($user),mres($server_ip));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("UPDATE osdial_list SET status='%s',user='%s' WHERE lead_id='%s';",mres($dispo_choice),mres($user),mres($lead_id));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("UPDATE osdial_campaigns SET campaign_lastcall=NOW() WHERE campaign_id='%s';",mres($campaign));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        if ($dispo_choice == 'PD') {
            $adjPOSTdate = dateToServer($link,$server_ip,$PostDatETimE,$phone_gmt,'',$phoneDST,0);
            $stmt=sprintf("UPDATE osdial_list SET post_date='%s' WHERE lead_id='%s';",mres($adjPOSTdate),mres($lead_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        $stmt=sprintf("SELECT count(*) FROM osdial_inbound_groups WHERE group_id='%s';",mres($stage));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            $stmt=sprintf("UPDATE osdial_closer_log SET status='%s' WHERE lead_id='%s' AND user='%s' ORDER BY closecallid DESC LIMIT 1;",mres($dispo_choice),mres($lead_id),mres($user));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        } else {
            $stmt=sprintf("UPDATE osdial_log SET status='%s' WHERE lead_id='%s' AND user='%s' ORDER BY uniqueid DESC LIMIT 1;",mres($dispo_choice),mres($lead_id),mres($user));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        if ( ($use_internal_dnc=='Y') and ($dispo_choice=='DNC') ) {
            $dnc_method='';
            $comp_id=0;
            if ($config['settings']['enable_multicompany'] > 0) {
                $dnc_method='';
                $stmt=sprintf("SELECT id,dnc_method FROM osdial_companies WHERE id='%s';",mres(((OSDsubstr($user,0,3) * 1) - 100)));
                $rslt=mysql_query($stmt, $link);
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $row=mysql_fetch_row($rslt);
                $comp_id=$row[0];
                $dnc_method=$row[1];
            }

            $stmt=sprintf("SELECT phone_number FROM osdial_list WHERE lead_id='%s';",mres($lead_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);

            if (OSDpreg_match('/COMPANY|BOTH/',$dnc_method)) {
                $stmt=sprintf("INSERT INTO osdial_dnc_company (company_id,phone_number) VALUES('%s','%s');",mres($comp_id),mres($row[0]));
            } else {
                $stmt=sprintf("INSERT INTO osdial_dnc (phone_number) VALUES('%s');",mres($row[0]));
            }
            $rslt=mysql_query($stmt, $link);
            if ($format=='debug') echo "\n<!-- $stmt -->";
        }

        $user_group='';
        $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
        $rslt=mysql_query($stmt, $link);
        if ($format=='debug') echo "\n<!-- $stmt -->";

        $ug_record_ct = mysql_num_rows($rslt);
        if ($ug_record_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $user_group = trim($row[0]);
        }

        $dispo_sec=0;
        $dispo_epochSQL='';
        $stmt=sprintf("SELECT SQL_NO_CACHE dispo_epoch,dispo_sec,talk_epoch,talk_sec,wait_epoch,wait_sec,pause_epoch,pause_sec FROM osdial_agent_log WHERE agent_log_id='%s' AND status IS NULL;",mres($agent_log_id));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $dispo_epoch = $row[0];
            $dispo_sec   = $row[1];
            $talk_epoch  = $row[2];
            $talk_sec    = $row[3];
            $wait_epoch  = $row[4];
            $wait_sec    = $row[5];
            $pause_epoch = $row[6];
            $pause_sec   = $row[7];
            if (OSDstrlen($talk_epoch)<5 and OSDstrlen($dispo_epoch)<5) $wait_epoch = $StarTtime;
            if (OSDstrlen($talk_epoch)<5) $talk_epoch = $StarTtime;
            if (OSDstrlen($dispo_epoch)<5) $dispo_epoch = $StarTtime;
            $pause_sec = ($wait_epoch - $pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch - $wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch - $talk_epoch);
            if ($talk_sec<0) $talk_sec=0;
            $dispo_sec = ($StarTtime - $dispo_epoch);
            if ($dispo_sec<0) $dispo_sec=0;

            $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',talk_sec='%s',dispo_epoch='%s',dispo_sec='%s',status='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($talk_epoch),mres($talk_sec),mres($dispo_epoch),mres($dispo_sec),mres($dispo_choice),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        } else {
            $stmt=sprintf("INSERT INTO osdial_agent_log SET user='%s',server_ip='%s',event_time='%s',campaign_id='%s',lead_id='%s',user_group='%s',pause_epoch='%s',pause_sec='0',wait_epoch='%s',wait_sec='0',talk_epoch='%s',talk_sec='0',dispo_epoch='%s',dispo_sec='0',status='%s';",mres($user),mres($server_ip),mres($NOW_TIME),mres($campaign),mres($lead_id),mres($user_group),mres($StarTtime),mres($StarTtime),mres($StarTtime),mres($StarTtime),mres($dispo_choice));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        if ($auto_dial_level == 0 or $VDpr_ct == 0) {
            $stmt=sprintf("INSERT INTO osdial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group) VALUES('%s','%s','%s','%s','%s','0','%s','%s');",mres($user),mres($server_ip),mres($NOW_TIME),mres($campaign),mres($StarTtime),mres($StarTtime),mres($user_group));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            $agent_log_id = mysql_insert_id($link);
        }


        ### CALLBACK ENTRY
        $adjCBdate='';
        if ( ($dispo_choice == 'CBHOLD') and (OSDstrlen($CallBackDatETimE)>10) ) {
            $adjCBdate = dateToServer($link,$server_ip,$CallBackDatETimE,$phone_gmt,'',$phoneDST,0);
            $stmt=sprintf("INSERT INTO osdial_callbacks (lead_id,list_id,campaign_id,status,entry_time,callback_time,user,recipient,comments,user_group) VALUES('%s','%s','%s','ACTIVE','%s','%s','%s','%s','%s','%s');",mres($lead_id),mres($list_id),mres($campaign),mres($NOW_TIME),mres($adjCBdate),mres($user),mres($recipient),mres($comments),mres($user_group));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        $stmt=sprintf("SELECT auto_alt_dial_statuses FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);

        if ( ($auto_dial_level > 0) and (OSDpreg_match("/ $dispo_choice /",$row[0])) ) {
            $stmt=sprintf("SELECT count(*) FROM osdial_hopper WHERE lead_id='%s' AND status='HOLD';",mres($lead_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);

            if ($row[0] > 0) {
                $stmt=sprintf("UPDATE osdial_hopper SET status='READY' WHERE lead_id='%s' AND status='HOLD' LIMIT 1;",mres($lead_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }
        } else {
            $stmt=sprintf("DELETE FROM osdial_hopper WHERE lead_id='%s' AND status='HOLD';",mres($lead_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        if ($config['settings']['enable_queuemetrics_logging'] > 0) {
            $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
            mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
            if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

            $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='%s',queue='%s',agent='Agent/%s',verb='CALLSTATUS',data1='%s',serverid='%s';",mres($StarTtime),mres($MDnextCID),mres($campaign),mres($user),mres($dispo_choice),mres($config['settings']['queuemetrics_log_id']));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $linkB);
            $affected_rows = mysql_affected_rows($linkB);

            mysql_close($linkB);
        }

        echo 'Lead ' . $lead_id . ' has been changed to ' . $dispo_choice . " Status - phoneGMTname:$phoneGMTname phoneDST:$phoneDST sip:$server_ip pgmt: $phone_gmt CBdate:$CallBackDatETimE adjCBdate:$adjCBdate\nNext agent_log_id:\n" . $agent_log_id . "\n";
    }
}

################################################################################
### updateLEAD - update the osdial_list table to reflect the values that are
###              in the agents screen at time of call hangup
################################################################################
if ($ACTION == 'updateLEAD') {
    $MT[0]='';
    $row='';   $rowx='';
    $DO_NOT_UPDATE=0;
    $DO_NOT_UPDATE_text='';
    if ( (OSDstrlen($phone_number)<1) || (OSDstrlen($lead_id)<1) ) {
        echo "phone_number $phone_number or lead_id $lead_id is not valid\n";
        exit;
    } else {
        $stmt=sprintf("SELECT disable_alter_custdata FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign));
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $dac_conf_ct = mysql_num_rows($rslt);
        $i=0;
        while ($i < $dac_conf_ct) {
            $row=mysql_fetch_row($rslt);
            $disable_alter_custdata = $row[0];
            $i++;
        }
        if (OSDpreg_match('/Y/',$disable_alter_custdata)) {
            $DO_NOT_UPDATE=1;
            $DO_NOT_UPDATE_text=' NOT';
            $stmt=sprintf("SELECT alter_custdata_override FROM osdial_users WHERE user='%s';",mres($user));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $aco_conf_ct = mysql_num_rows($rslt);
            $i=0;
            while ($i < $aco_conf_ct) {
                $row=mysql_fetch_row($rslt);
                $alter_custdata_override = $row[0];
                $i++;
            }
            if (OSDpreg_match('/ALLOW_ALTER/',$alter_custdata_override)) {
                $DO_NOT_UPDATE=0;
                $DO_NOT_UPDATE_text='';
            }
        }

        if ($DO_NOT_UPDATE < 1) {
            $comments = OSDpreg_replace("/\r/",'',$comments);
            $comments = OSDpreg_replace("/\n/",'!N',$comments);
            $comments = OSDpreg_replace("/--AMP--/",'&',$comments);
            $comments = OSDpreg_replace("/--QUES--/",'?',$comments);
            $comments = OSDpreg_replace("/--POUND--/",'#',$comments);

            $status_exteneddSQL='';
            if (!empty($status_extended)) $status_extendedSQL=sprintf(",status_extended='%s'",mres($status_extended));

            $stmt=sprintf("UPDATE osdial_list SET vendor_lead_code='%s',title='%s',first_name='%s',middle_initial='%s',last_name='%s',address1='%s',address2='%s',address3='%s',city='%s',state='%s',province='%s',postal_code='%s',country_code='%s',gender='%s',date_of_birth='%s',alt_phone='%s',email='%s',custom1='%s',custom2='%s',comments='%s',phone_number='%s',phone_code='%s',organization='%s',organization_title='%s'%s WHERE lead_id='%s';",mres($vendor_lead_code),mres($title),mres($first_name),mres($middle_initial),mres($last_name),mres($address1),mres($address2),mres($address3),mres($city),mres($state),mres($province),mres($postal_code),mres($country_code),mres($gender),mres($date_of_birth),mres($alt_phone),mres($email),mres($custom1),mres($custom2),mres($comments),mres($phone_number),mres($phone_code),mres($organization),mres($organization_title),$status_extendedSQL,mres($lead_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        $stmt=sprintf("UPDATE osdial_list SET email='%s' WHERE lead_id='%s';",mres($email),mres($lead_id));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
        $cnt = 0;
        foreach ($forms as $form) {
            $fcamps = OSDpreg_split('/,/',$form['campaigns']);
            foreach ($fcamps as $fcamp) {
                if ($fcamp == 'ALL' or OSDstrtoupper($fcamp) == OSDstrtoupper($campaign)) {
                    $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', sprintf("deleted='0' AND form_id='%s'",mres($form['id'])) );
                    foreach ($fields as $field) {
                        $afvar = get_variable('AF' . $field['id']);
                        if (!empty($afvar)) {
                            $stmt=sprintf("INSERT INTO osdial_list_fields (lead_id,field_id,value) VALUES ('%s','%s','%s') ON DUPLICATE KEY UPDATE value='%s';",mres($lead_id),mres($field['id']),mres($afvar),mres($afvar));
                            $rslt=mysql_query($stmt, $link);
                        }
                        $cnt++;
                    }
                }
            }
        }

        $random = (rand(1000000, 9999999) + 10000000);
        $stmt=sprintf("UPDATE osdial_live_agents SET random_id='%s' WHERE user='%s' AND server_ip='%s';",mres($random),mres($user),mres($server_ip));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        echo "Lead $lead_id information has$DO_NOT_UPDATE_text been updated\n";
    }
}


################################################################################
### VDADpause - update the osdial_live_agents table to show that the agent is
###  or ready   now active and ready to take calls
################################################################################
if ( ($ACTION == 'VDADpause') || ($ACTION == 'VDADready') ) {
    $MT[0]='';
    $row='';   $rowx='';
    if ( (OSDstrlen($stage)<2) || (OSDstrlen($server_ip)<1) ) {
        echo "stage $stage is not valid\n";
        exit;
    } else {
        $random = (rand(1000000, 9999999) + 10000000);
        $stmt=sprintf("UPDATE osdial_live_agents SET uniqueid='',lead_id='',callerid='',channel='',call_server_ip='',last_call_finish='%s',random_id='%s',comments='',status='%s' WHERE user='%s' AND server_ip='%s';",mres($NOW_TIME),mres($random),mres($stage),mres($user),mres($server_ip));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);

        $affected_rows = mysql_affected_rows($link);
        if ($affected_rows > 0) {
            if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);
                if (OSDpreg_match('/READY/',$stage)) $QMstatus='UNPAUSEALL';
                if (OSDpreg_match('/PAUSE/',$stage)) $QMstatus='PAUSEALL';

                $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='NONE',agent='Agent/%s',verb='%s',serverid='%s';",mres($StarTtime),mres($user),mres($QMstatus),mres($config['settings']['queuemetrics_log_id']));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $linkB);
                $affected_rows = mysql_affected_rows($linkB);
                mysql_close($linkB);
            }
        }

        $pause_sec=0;
        $stmt=sprintf("SELECT SQL_NO_CACHE pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec FROM osdial_agent_log WHERE agent_log_id='%s';",mres($agent_log_id));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $VDpr_ct = mysql_num_rows($rslt);
        if ($VDpr_ct > 0) {
            $row=mysql_fetch_row($rslt);
            $pause_epoch = $row[0];
            $pause_sec   = $row[1];
            $wait_epoch  = $row[2];
            $wait_sec    = $row[3];
            $talk_epoch  = $row[4];
            $talk_sec    = $row[5];
            $dispo_epoch = $row[6];
            $dispo_sec   = $row[7];
        }

        if ($ACTION == 'VDADready') {
            if ( (OSDpreg_match("/NULL/",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
                $pause_sec = $StarTtime - $pause_epoch;
                if ($pause_sec<0) $pause_sec=0;
                $wait_epoch = $StarTtime;
                $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_epoch='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($wait_epoch),mres($agent_log_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }
        } elseif ($ACTION == 'VDADpause') {
            if ( (OSDpreg_match("/NULL/",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
                $pause_sec = $wait_epoch - $pause_epoch;
                if ($pause_sec<0) $pause_sec=0;
                $wait_sec = ($StarTtime - $wait_epoch);
                if ($wait_sec<0) $wait_sec=0;
                $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_sec='%s',talk_epoch='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($wait_sec),mres($StarTtime),mres($agent_log_id));
                if ($format=='debug') echo "\n<!-- $stmt -->";
                $rslt=mysql_query($stmt, $link);
            }
        }

        if ($wrapup == 'WRAPUP') {
            if ( (OSDpreg_match("/NULL/",$dispo_epoch)) or ($dispo_epoch < 1000) ) {
                $stmt=sprintf("UPDATE osdial_agent_log SET dispo_epoch='%s', dispo_sec='0' WHERE agent_log_id='%s';",mres($StarTtime),mres($agent_log_id));
            } else {
                $dispo_sec = ($StarTtime - $dispo_epoch);
                if ($dispo_sec<0) $dispo_sec=0;
                $talk_sec = ($dispo_epoch - $talk_epoch);
                if ($talk_sec<0) $talk_sec=0;
                $stmt=sprintf("UPDATE osdial_agent_log SET talk_sec='%s',talk_epoch='%s',dispo_sec='%s' WHERE agent_log_id='%s';",mres($talk_sec),mres($talk_epoch),mres($dispo_sec),mres($agent_log_id));
            }

            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);
        }

        #if ($agent_log == 'NEW_ID' and (OSDstrlen($talk_epoch)>5 or OSDstrlen($dispo_epoch)>5)) {
        if ($agent_log == 'NEW_ID' and (OSDstrlen($wait_epoch)>5 or OSDstrlen($talk_epoch)>5 or OSDstrlen($dispo_epoch)>5)) {
            $user_group='';
            $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $ug_record_ct = mysql_num_rows($rslt);
            if ($ug_record_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $user_group =       trim($row[0]);
            }

            if (OSDstrlen($wait_epoch)<5) $wait_epoch = $StarTtime;
            if (OSDstrlen($talk_epoch)<5) $talk_epoch = $StarTtime;
            if (OSDstrlen($dispo_epoch)<5) $dispo_epoch = $StarTtime;
            $pause_sec = ($wait_epoch - $pause_epoch);
            if ($pause_sec<0) $pause_sec=0;
            $wait_sec = ($talk_epoch - $wait_epoch);
            if ($wait_sec<0) $wait_sec=0;
            $talk_sec = ($dispo_epoch - $talk_epoch);
            if ($talk_sec<0) $talk_sec=0;
            $dispo_sec = ($StarTtime - $dispo_epoch);
            if ($dispo_sec<0) $dispo_sec=0;

            $stmt=sprintf("UPDATE osdial_agent_log SET pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s',talk_sec='%s',dispo_epoch='%s',dispo_sec='%s' WHERE agent_log_id='%s';",mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($talk_epoch),mres($talk_sec),mres($dispo_epoch),mres($dispo_sec),mres($agent_log_id));
            if ($format=='debug') echo "\n<!-- $stmt -->";
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("INSERT INTO osdial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,user_group) VALUES('%s','%s','%s','%s','%s','0','%s');",mres($user),mres($server_ip),mres($NOW_TIME),mres($campaign),mres($StarTtime),mres($user_group));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            $agent_log_id = mysql_insert_id($link);
        }

        echo 'Agent ' . $user . ' is now in status ' . $stage . "\nNext agent_log_id:\n$agent_log_id\n";
    }
}



################################################################################
### PauseCodeSubmit - Update osdial_agent_log with pause code
################################################################################
if ($ACTION == 'PauseCodeSubmit') {
    $row='';   $rowx='';
    if ( (OSDstrlen($status)<1) || (OSDstrlen($agent_log_id)<1) ) {
        echo "agent_log_id $agent_log_id or pause_code $status is not valid\n";
        exit;
    } else {
        $stmt=sprintf("UPDATE osdial_agent_log SET sub_status='%s' WHERE agent_log_id='%s';",mres($status),mres($agent_log_id));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $affected_rows = mysql_affected_rows($link);
        if ($affected_rows > 0) {
            if ( ($enable_sipsak_messages > 0) and ($config['settings']['allow_sipsak_messages'] > 0) and (OSDpreg_match("/SIP/",$protocol)) ) {
                $SIPSAK_prefix = 'BK-';
                passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$status\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
            }
            if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='%s',agent='Agent/%s',verb='PAUSEREASON',serverid='%s',data1='%s';",mres($StarTtime),mres($campaign),mres($user),mres($config['settings']['queuemetrics_log_id']),mres($status));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $linkB);
                $affected_rows = mysql_affected_rows($linkB);
                mysql_close($linkB);
            }
        }

        echo "Pause Code has been updated to $status for $agent_log_id\n";
    }
}


################################################################################
### MDHopperList - List the entries in hopper for this campaign
################################################################################
if ($ACTION == 'MDHopperList') {
    $stmt=sprintf("SELECT hopper_id,lead_id,campaign_id,status,user,list_id,gmt_offset_now,state,alt_dial,priority FROM osdial_hopper WHERE campaign_id='%s' AND status IN('READY') AND user='' ORDER BY priority DESC,hopper_id ASC;",mres($campaign));
    if ($DB) echo "$stmt\n";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $hopper_count = mysql_num_rows($rslt);
    echo "$hopper_count\n";
    $loop_count=0;
    while ($hopper_count>$loop_count) {
        $row=mysql_fetch_row($rslt);
        $hopper_id[$loop_count]      = $row[0];
        $lead_id[$loop_count]        = $row[1];
        $campaign_id[$loop_count]    = $row[2];
        $status[$loop_count]         = $row[3];
        $user[$loop_count]           = $row[4];
        $list_id[$loop_count]        = $row[5];
        $gmt_offset_now[$loop_count] = (OSDpreg_replace('/\.$/','',OSDpreg_replace('/0$/','',OSDpreg_replace('/0$/','',$row[6])))) - date("I");
        $state[$loop_count]          = $row[7];
        $alt_dial[$loop_count]       = $row[8];
        $priority[$loop_count]       = $row[9];
        $loop_count++;
    }
    $loop_count=0;
    while ($hopper_count>$loop_count) {
        $stmt=sprintf("SELECT first_name,last_name,phone_number,city,postal_code,modify_date,called_count,status,phone_code,vendor_lead_code,source_id FROM osdial_list WHERE lead_id='%s';",mres($lead_id[$loop_count]));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);

        echo "$row[0] ~$row[1] ~$row[2] ~$hopper_id[$loop_count] ~$lead_id[$loop_count] ~$campaign_id[$loop_count] ~$status[$loop_count] ~$user[$loop_count] ~$list_id[$loop_count] ~$gmt_offset_now[$loop_count] ~$state[$loop_count] ~$alt_dial[$loop_count] ~$priority[$loop_count] ~$row[3] ~$row[4] ~$row[5] ~$row[6] ~$row[7] ~$row[8] ~$row[9] ~$row[10]\n";
        $loop_count++;
    }

}

if ($ACTION == 'MDHopperListAddPriority') {
    $stmt=sprintf("SELECT priority FROM osdial_hopper WHERE hopper_id='%s' LIMIT 1;",mres($hopper_id));
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $new_priority = ($row[0] + $hopper_add);
    $stmt=sprintf("UPDATE osdial_hopper SET priority='%s' WHERE hopper_id='%s';",mres($new_priority),mres($hopper_id));
    $rslt=mysql_query($stmt, $link);
    $affected_rows = mysql_affected_rows($link);
    echo "Done.\n";
}


################################################################################
### CalLBacKLisT - List the USERONLY callbacks for an agent
################################################################################
if ($ACTION == 'CalLBacKLisT') {
    $stmt=sprintf("SELECT callback_id,lead_id,campaign_id,status,entry_time,callback_time,comments FROM osdial_callbacks WHERE recipient='USERONLY' AND user='%s' AND campaign_id='%s' AND status NOT IN('INACTIVE','DEAD') ORDER BY callback_time;",mres($user),mres($campaign));
    if ($DB) echo "$stmt\n";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $callbacks_count = mysql_num_rows($rslt);
    echo "$callbacks_count\n";
    $loop_count=0;
    while ($callbacks_count>$loop_count) {
        $row=mysql_fetch_row($rslt);
        $callback_id[$loop_count]   = $row[0];
        $lead_id[$loop_count]       = $row[1];
        $campaign_id[$loop_count]   = $row[2];
        $status[$loop_count]        = $row[3];
        $entry_time[$loop_count]    = $row[4];
        $callback_time[$loop_count] = $row[5];
        $comments[$loop_count]      = $row[6];
        $loop_count++;
    }
    $loop_count=0;
    while ($callbacks_count>$loop_count) {
        $stmt=sprintf("SELECT first_name,last_name,phone_number FROM osdial_list WHERE lead_id='$lead_id[$loop_count]';",mres($lead_id[$loop_count]));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);

        echo "$row[0] ~$row[1] ~$row[2] ~$callback_id[$loop_count] ~$lead_id[$loop_count] ~$campaign_id[$loop_count] ~$status[$loop_count] ~$entry_time[$loop_count] ~$callback_time[$loop_count] ~$comments[$loop_count]\n";
        $loop_count++;
    }

}


################################################################################
### CalLBacKCounT - send the count of the USERONLY callbacks for an agent
################################################################################
if ($ACTION == 'CalLBacKCounT') {
    $stmt=sprintf("SELECT count(*) FROM osdial_callbacks WHERE recipient='USERONLY' AND user='%s' AND campaign_id='%s' AND status NOT IN('INACTIVE','DEAD');",mres($user),mres($campaign));
    if ($DB) echo "$stmt\n";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $cbcount=$row[0];

    echo $cbcount;
}

################################################################################
### RepullLeadData - Repull the lead data for a given phone number.  Mainly for
###                  inbound calls which dial in using a different number.
################################################################################
if ($ACTION == 'RepullLeadData')
{
    $listSQL = "";
    if ($lookup == "campaign") {
        ##### grab all the lists in the campaign.
        $stmt=sprintf("SELECT list_id FROM osdial_lists WHERE campaign_id='%s';",mres($campaign));
        $rslt=mysql_query($stmt, $link);
        if ($DB) echo "$stmt\n";
        $list_ct = mysql_num_rows($rslt);
        $cnt = 0;
        $listSQL = "list_id IN(";
        while ($cnt < $list_ct) {
            $row=mysql_fetch_row($rslt);
            $listSQL .= sprintf("'%s',",mres($row[0]));
            $cnt++;
        }
        $listSQL = rtrim($listSQL,',');
        $listSQL .= ")";
    } elseif ($lookup == "list") {
        $listSQL = sprintf("list_id='%s'",mres($list_id));
    } else {
        $listSQL = "list_id>'999'";
    }
    ##### grab the data from osdial_list for the lead_id
    $stmt=sprintf("SELECT * FROM osdial_list WHERE phone_number='%s' AND lead_id!='%s' AND %s LIMIT 1;",mres($oldphone),mres($oldlead),$listSQL);
    $rslt=mysql_query($stmt, $link);
    if ($DB) echo "$stmt\n";
    $list_lead_ct = mysql_num_rows($rslt);
    if ($list_lead_ct > 0) {
        $row=mysql_fetch_row($rslt);
        $lead_id        = trim($row[0]);
        $vendor_id      = trim($row[5]);
        $source_id      = trim($row[6]);
        $list_id        = trim($row[7]);
        $gmt_offset_now = trim($row[8]);
        $phone_code     = trim($row[10]);
        $phone_number   = trim($row[11]);
        $title          = trim($row[12]);
        $first_name     = trim($row[13]);
        $middle_initial = trim($row[14]);
        $last_name      = trim($row[15]);
        $address1       = trim($row[16]);
        $address2       = trim($row[17]);
        $address3       = trim($row[18]);
        $city           = trim($row[19]);
        $state          = trim($row[20]);
        $province       = trim($row[21]);
        $postal_code    = trim($row[22]);
        $country_code   = trim($row[23]);
        $gender         = trim($row[24]);
        $date_of_birth  = trim($row[25]);
        $alt_phone      = trim($row[26]);
        $email          = trim($row[27]);
        $custom1        = trim($row[28]);
        $comments       = trim($row[29]);
        $called_count   = trim($row[30]);
        if (empty($alt_phone)) {
            $alt_phone = $oldphone;
        } elseif (empty($address3)) {
            $address3 = $oldphone;
        }
        $custom2        = trim($row[31]);
        $external_key   = trim($row[32]);
        $post_date      = trim($row[35]);
        $organization   = trim($row[36]);
        $organization_title = trim($row[37]);
    }

    ### update the old lead status to REPULL
    $stmt=sprintf("UPDATE osdial_list SET status='REPULL' WHERE lead_id='%s';",mres($oldlead));
    if ($DB) echo "$stmt\n";
    $rslt=mysql_query($stmt, $link);
    ### update the lead status to INCALL
    $stmt=sprintf("UPDATE osdial_list SET status='INCALL',user='%s' WHERE lead_id='%s';",mres($user),mres($lead_id));
    if ($DB) echo "$stmt\n";
    $rslt=mysql_query($stmt, $link);

    $stmt=sprintf("SELECT SQL_NO_CACHE call_type FROM osdial_auto_calls WHERE uniqueid='%s' ORDER BY call_time DESC LIMIT 1;",mres($uniqueid));
    if ($DB) echo "$stmt\n";
    $rslt=mysql_query($stmt, $link);
    $VDAC_cid_ct = mysql_num_rows($rslt);
    if ($VDAC_cid_ct > 0) {
        $row=mysql_fetch_row($rslt);
        $call_type =$row[0];
    }

    # update the logs with the new lead/list ids
    if ( ($call_type=='OUT') or ($call_type=='OUTBALANCE') ) {
        $stmt=sprintf("UPDATE osdial_log SET list_id='%s',lead_id='%s' WHERE lead_id='%s' AND uniqueid='%s';",mres($list_id),mres($lead_id),mres($oldlead),mres($uniqueid));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
    } else {
        ### update the osdial_closer_log user to INCALL
        $stmt=sprintf("UPDATE osdial_closer_log SET list_id='%s',lead_id='%s' WHERE lead_id='%s' ORDER BY closecallid DESC LIMIT 1;",mres($list_id),mres($lead_id),mres($oldlead));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
    }

    $comments = OSDpreg_replace("/\r/",'',$comments);
    $comments = OSDpreg_replace("/\n/",'!N',$comments);

    $phone_code = OSDpreg_replace('/^011|^010|^00/', '', $phone_code);

    $LeaD_InfO =    $lead_id . "\n";
    $LeaD_InfO .=   $vendor_id . "\n";
    $LeaD_InfO .=   $list_id . "\n";
    $LeaD_InfO .=   $gmt_offset_now . "\n";
    $LeaD_InfO .=   $phone_code . "\n";
    $LeaD_InfO .=   $phone_number . "\n";
    $LeaD_InfO .=   $title . "\n";
    $LeaD_InfO .=   $first_name . "\n";
    $LeaD_InfO .=   $middle_initial . "\n";
    $LeaD_InfO .=   $last_name . "\n";
    $LeaD_InfO .=   $address1 . "\n";
    $LeaD_InfO .=   $address2 . "\n";
    $LeaD_InfO .=   $address3 . "\n";
    $LeaD_InfO .=   $city . "\n";
    $LeaD_InfO .=   $state . "\n";
    $LeaD_InfO .=   $province . "\n";
    $LeaD_InfO .=   $postal_code . "\n";
    $LeaD_InfO .=   $country_code . "\n";
    $LeaD_InfO .=   $gender . "\n";
    $LeaD_InfO .=   $date_of_birth . "\n";
    $LeaD_InfO .=   $alt_phone . "\n";
    $LeaD_InfO .=   $email . "\n";
    $LeaD_InfO .=   $custom1 . "\n";
    $LeaD_InfO .=   $comments . "\n";
    $LeaD_InfO .=   $called_count . "\n";
    $LeaD_InfO .=   $custom2 . "\n";
    $LeaD_InfO .=   $external_key . "\n";
    $LeaD_InfO .=   $post_date . "\n";
    $LeaD_InfO .=   $organization . "\n";
    $LeaD_InfO .=   $organization_title . "\n";

    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
    $cnt = 0;
    foreach ($forms as $form) {
        $fcamps = OSDpreg_split('/,/',$form['campaigns']);
        foreach ($fcamps as $fcamp) {
            if ($fcamp == 'ALL' or OSDstrtoupper($fcamp) == OSDstrtoupper($campaign)) {
                $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', sprintf("deleted='0' AND form_id='%s'",mres($form['id'])) );
                foreach ($fields as $field) {
                    $vdlf = get_first_record($link, 'osdial_list_fields', '*', sprintf("lead_id='%s' AND field_id='%s'",mres($lead_id),mres($field['id'])) );
                    $LeaD_InfO .= $vdlf['value'] . "\n";
                    $cnt++;
                }
            }
        }
    }

    echo $LeaD_InfO;
}

################################################################################
### ScriptButtonLog - log the button press within the script.              
################################################################################
if ($ACTION == 'ScriptButtonLog') {
    $stmt=sprintf("INSERT INTO osdial_script_button_log SET lead_id='%s',script_id='%s',script_button_id='%s',user='%s';",mres($lead_id),mres($script_id),mres($script_button_id),mres($user));
    if ($DB) echo "$stmt\n";
    $rslt=mysql_query($stmt, $link);

    echo "DONE.";
}

if ($ACTION == 'logTimeTrans') {
    if ($company_id != '') {
        $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",mres($company_id)));

        if ($comp['acct_method'] != 'NONE' and $comp['acct_method'] != 'RANGE') {
            $trans_sec = $agent_log_time;
            if ($comp['acct_method'] == 'TALK_ROUNDUP') {
                $trans_sec=60;
            }
            if ($comp['acct_method'] == 'AVAILABLE' and $agent_log_type == 'PAUSE') {
                $trans_sec=0;
            } elseif ($comp['acct_method'] == 'TALK' and $agent_log_type != 'TALK') {
                $trans_sec=0;
            } elseif ($comp['acct_method'] == 'TALK_ROUNDUP' and $agent_log_type != 'TALK') {
                $trans_sec=0;
            }

            if ($trans_sec>0) {
                $trans_sec = $trans_sec * -1;
                $stmt=sprintf("INSERT INTO osdial_acct_trans_daily SET company_id='%s',agent_log_id='%s',trans_type='%s',trans_sec='%s',created=NOW();",mres($company_id),mres($agent_log_id),mres($agent_log_type),mres($trans_sec));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);

                $tranid=0;
                $trans = get_krh($link, 'osdial_acct_trans', '*', '', sprintf("company_id='%s' AND agent_log_id='%s'",mres($company_id),mres($agent_log_id)));
                foreach ($trans as $tran) {
                    $tranid=$tran['id'];
                }
                if ($tranid>0) {
                    $stmt=sprintf("UPDATE osdial_acct_trans SET trans_sec=trans_sec+%s WHERE company_id='%s' AND agent_log_id='%s';",mres($trans_sec),mres($company_id),mres($agent_log_id));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                } else {
                    $stmt=sprintf("INSERT INTO osdial_acct_trans SET company_id='%s',agent_log_id='%s',trans_type='%s',trans_sec='%s',created=NOW();",mres($company_id),mres($agent_log_id),mres('DEBIT'),mres($trans_sec));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                }
            }
        }
    }
    echo "DONE.";
}
if ($ACTION == 'getTimeTransStats') {
    if ($company_id != '') {
        $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",mres($company_id)));

        $secremain=0;
        if ($comp['acct_method'] != 'NONE' and $comp['acct_method'] != 'RANGE') {
            $secremain=$comp['acct_remaining_time'];
        }
    }
    echo $comp['status'] . "\n";
    echo $secremain . "\n";
    echo $comp['acct_method'] . "\n";
    echo "DONE.";
}


################################################################################
### Send Email to lead based on given template
################################################################################
if ($ACTION == 'Email') {
    $et = get_first_record($link, 'osdial_email_templates', '*', sprintf("et_id='%s'",mres($et_id)));
    $lead = get_first_record($link, 'osdial_list', '*', sprintf("lead_id='%s'",mres($lead_id)));

    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
    foreach ($forms as $form) {
        $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', sprintf("deleted='0' AND form_id='%s'",mres($form['id'])) );
        foreach ($fields as $field) {
            $vdlf = get_first_record($link, 'osdial_list_fields', '*', sprintf("lead_id='%s' AND field_id='%s'",mres($lead_id),mres($field['id'])) );
            if (!empty($vdlf['value'])) $lead[$form['name'] . '_' . $field['name']] = $vdlf['value'];
        }
    }

    foreach ($lead as $k => $v) {
        $et['et_subject'] = OSDpreg_replace('/\[\[' . $k . '\]\]/imU', $v, $et['et_subject']);
        $et['et_body_html'] = OSDpreg_replace('/\[\[' . $k . '\]\]/imU', $v, $et['et_body_html']);
        $et['et_body_text'] = OSDpreg_replace('/\[\[' . $k . '\]\]/imU', $v, $et['et_body_text']);
    }

    $eb = get_first_record($link, 'osdial_campaign_email_blacklist', 'count(*) AS count', sprintf("campaign_id='%s' AND email='%s'",mres($campaign),mres($lead['email'])));
    if ($eb['count'] > 0) {
        echo "BLACKLISTED\n";
        echo $lead['email'] . "\n";
    } else {
        send_email($et['et_host'], $et['et_port'], $et['et_user'], $et['et_pass'], $lead['email'], $et['et_from'], $et['et_subject'], $et['et_body_html'], $et['et_body_text']);
        echo "DONE.\nsend_email($et[et_host], $et[et_port], $et[et_user], $et[et_pass], $lead[email], $et[et_from], $et[et_subject], $et[et_body_html], $et[et_body_text]);";
    }
}


################################################################################
### Check if email is in blacklist.
################################################################################
if ($ACTION == 'EmailCheckBlacklist') {
    $eb = get_first_record($link, 'osdial_campaign_email_blacklist', 'count(*) AS count', sprintf("campaign_id='%s' AND email='%s'",mres($campaign),mres($lead['email'])));
    if ($eb['count'] > 0) {
        echo "BLACKLISTED\n";
        echo $email . "\n";
    }
}

if ($ACTION == 'MulticallGetChannel') {
    $stmt=sprintf("SELECT status,campaign_id,closer_campaigns FROM osdial_live_agents WHERE user='%s' AND server_ip='%s';",mres($user),mres($server_ip));
    if ($format=='debug') echo "<!-- |$stmt| -->\n";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $Alogin=$row[0];
    $Acampaign=$row[1];
    $AccampSQL=$row[2];
    $AccampSQL = mres(OSDpreg_replace('/ -/','', $AccampSQL));
    $AccampSQL = OSDpreg_replace('/ /',"','", $AccampSQL);

    $Auser_group='';
    $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $Auser_group = trim($row[0]);

    $stmt=sprintf("SELECT channel,server_ip,callerid,group_id,voicemail_ext,uniqueid,lead_id,drop_call_seconds,agent_alert_exten FROM osdial_auto_calls JOIN osdial_inbound_groups ON (campaign_id=group_id) WHERE status IN('LIVE') AND campaign_id IN('%s') AND allow_multicall='Y' ORDER BY campaign_id DESC LIMIT 1;",$AccampSQL);
    if ($format=='debug') echo "<!-- |$stmt| -->\n";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $MCChannel=$row[0];
    $MCServerIP=$row[1];
    $MCCID=$row[2];
    $MCIG=$row[3];
    $MCvoicemail=$row[4];
    $MCuniqueid=$row[5];
    $MCleadid=$row[6];
    $MCvmdrop=$row[7]-3;
    $MCaaexten=$row[8];

    if (OSDpreg_match('/^A2A_/', $MCIG) and OSDpreg_match('/^Local\/8870/',$MCChannel)) {
        $tchan = OSDpreg_replace('/^Local\/8|@osdial.*$/','',$MCChannel);
        $stmt=sprintf("SELECT agent_alert_exten FROM osdial_auto_calls JOIN osdial_inbound_groups ON (campaign_id=group_id) WHERE channel LIKE 'Local/_%s%%' LIMIT 1;",mres($tchan));
        if ($format=='debug') echo "<!-- |$stmt| -->\n";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $MCaaexten=$row[0];
    }

    $stmt=sprintf("SELECT count(*) FROM osdial_agent_log WHERE uniqueid='%s' LIMIT 1;",mres($MCuniqueid));
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $ALcnt = $row[0];

    $MCagentlogid='';
    $MCagentlogtype='';
    $MCagentlogtime='';
    if ($ALcnt==0) {
        $pause_epoch=$StarTtime;
        $wait_epoch=$StarTtime;
        $talk_epoch=$StarTtime;
        $stmt=sprintf("INSERT INTO osdial_agent_log (server_ip,event_time,lead_id,pause_epoch,wait_epoch,talk_epoch,uniqueid) VALUES('%s','%s','%s','%s','%s','%s','%s');",mres($MCServerIP),mres($NOW_TIME),mres($MCleadid),mres($pause_epoch),mres($wait_epoch),mres($talk_epoch),mres($MCuniqueid));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $affected_rows = mysql_affected_rows($link);
        $MCagentlogid = mysql_insert_id($link);
        $MCagentlogtype='WAIT';
        $MCagentlogtime='0';

    } else {
        $stmt=sprintf("SELECT agent_log_id,pause_epoch,wait_epoch FROM osdial_agent_log WHERE uniqueid='%s' LIMIT 1;",mres($MCuniqueid));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $MCagentlogid=$row[0];
        $pause_epoch=$row[1];
        $wait_epoch=$row[2];
        $talk_epoch=$StarTtime;
        $pause_sec = ($wait_epoch-$pause_epoch);
        if ($pause_sec<0) $pause_sec=0;
        $wait_sec = ($talk_epoch-$wait_epoch);
        if ($wait_sec<0) $wait_sec=0;

        $stmt=sprintf("UPDATE osdial_agent_log SET pause_epoch='%s',pause_sec='%s',wait_epoch='%s',wait_sec='%s',talk_epoch='%s' WHERE agent_log_id='%s';",mres($pause_epoch),mres($pause_sec),mres($wait_epoch),mres($wait_sec),mres($talk_epoch),mres($MCagentlogid));
        if ($format=='debug') echo "\n<!-- $stmt -->";
        $rslt=mysql_query($stmt, $link);
        $MCagentlogtype='WAIT';
        $MCagentlogtime=$wait_sec;
    }

    $lead = get_first_record($link, 'osdial_list', '*', sprintf("lead_id='%s'",mres($MCleadid)));

    echo $MCChannel . "\n";
    echo $MCServerIP . "\n";
    echo $MCCID . "\n";
    echo $MCIG . "\n";
    echo $MCvoicemail . "\n";
    echo $MCuniqueid . "\n";
    echo $MCleadid . "\n";
    echo $lead['phone_code'] . "\n";
    echo $lead['phone_number'] . "\n";
    echo $lead['comments'] . "\n";
    echo $lead['first_name'] . "\n";
    echo $lead['last_name'] . "\n";
    echo $lead['address1'] . "\n";
    echo $lead['city'] . "\n";
    echo $lead['state'] . "\n";
    echo $lead['postal_code'] . "\n";
    echo $MCvmdrop . "\n";
    echo $MCaaexten . "\n";
    echo $MCagentlogid . "\n";
    echo $MCagentlogtype . "\n";
    echo $MCagentlogtime . "\n";
}



################################################################################
### Get agent_message from user_groups.
################################################################################
if ($ACTION == 'AgentMessage') {
    $group = get_first_record($link, 'osdial_user_groups', '*', sprintf("user_group='%s'",mres($user_group)));
    echo $group['agent_message'];
}


if ($format=='debug') {
    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $StarTtime);
    echo "\n<!-- script runtime: $RUNtime seconds -->";
    echo "\n</body>\n</html>\n";
}

exit; 

?>
