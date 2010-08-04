<?php
# 
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
#
# make sure you have added a user to the osdial_users MySQL table with at least
# user_level 1 or greater to access this page. Also, you need to have the login
# and pass of a phone listed in the asterisk.phones table. The page grabs the 
# server info and other details from this login and pass.
#
# This script works best with Firefox or Mozilla, but will run for a couple
# hours on Internet Explorer before the memory leaks cause a crash.
#
# Other scripts that this application depends on:
# - vdc_db_query.php: Updates information in the database
# - manager_send.php: Sends manager actions to the DB for execution
#
# CHANGELOG
# 50628-1620 - Added some basic formatting and worked on process flow
# 50628-1715 - Startup variables mapped to javascript variables
# 50629-1303 - Added Login Closer in-groups selection box and vla update
# 50629-1530 - Rough layout for customer info form section and button links
# 50630-1453 - Rough Manual Dial/Hangup with customer info displayed
# 50701-1450 - Added osdial_log entries on dial and hangup
# 50701-1634 - Added Logout function
# 50705-1259 - Added call disposition functionality
# 50705-1432 - Added lead info DB update function
# 50705-1658 - Added web form functionality
# 50706-1043 - Added call park and pickup functions
# 50706-1234 - Added Start/Stop Recording functionality
# 50706-1614 - Added conference channels display option
# 50711-1333 - Removed call check redundancy and fixed a span bug
# 50727-1424 - Added customer channel and participant present sensing/alerts
# 50804-1057 - Added SendDTMF function and reconfigured the transfer span
# 50804-1224 - Added Local and Internal Closer transfer functions
# 50804-1628 - Added Blind transfer, activated LIVE CALL image and fixed bugs
# 50804-1808 - Added button images for left buttons
# 50815-1151 - Added 3Way calling functions to Transfer-conf frame
# 50815-1602 - Added images and buttons for xfer functions
# 50816-1813 - Added basic autodial outbound call pickup functions
# 50817-1113 - Fixes to auto_dialing call receipt
# 50817-1234 - Added inbound call receipt capability
# 50817-1541 - Added customer time display
# 50817-1541 - Added customer time display
# 50818-1327 - Added stop-all-recordings-after-each-osdial-call option
# 50818-1703 - Added pretty login section
# 50825-1200 - Modified form field lengths, added double-click dispositions
# 50831-1603 - Fixed customer time bug and fronter display bug for CLOSER
# 50901-1314 - Fixed CLOSER IN-GROUP Web Form bug
# 50903-0904 - Added preview-lead code for manual dialing
# 50904-0016 - Added ability to hangup manual dials before pickup
# 50906-1319 - Added override for filters on xfer calls, fixed login display bug
# 50909-1243 - Added hotkeys functionality for quick dispoing in auto-dial mode
# 50912-0958 - Modified hotkeys function, agent must have user_level >= 5 to use
# 50913-1212 - Added campaign_cid to 3rd party calls
# 50923-1546 - Modified to work with language translation
# 50926-1656 - Added campaign pull-down at login of active campaigns
# 50928-1633 - Added manual dial alternate number dial option
# 50930-1538 - Added session_id empty login failure and fixed 2 minor bugs
# 51004-1656 - Fixed recording filename bug and new Spanish translation
# 51020-1103 - Added campaign-specific recording control abilities
# 51020-1352 - Added Basic osdial_agent_log framework
# 51021-1050 - Fixed custtime display and disable Enter/Return keypresses
# 51021-1718 - Allows for multi-line comments (changes \n to !N in database)
# 51110-1432 - Fixed non-standard http port issue
# 51111-1047 - Added osdial_agent_log lead_id earlier for manual dial
# 51118-1305 - Activate multi-line comments from $multi_line_comments var
# 51118-1313 - Move Transfer DIV to a floating span to preserve 800x600 view
# 51121-1506 - Small PHP optimizations in many scripts and disabled globalize
# 51129-1010 - Added ability to accept calls from other OSDIAL servers
# 51129-1254 - Fixed Hangups of other agents channels when customer hangs up
# 51208-1732 - Created user-first login that looks for default phone info
# 51219-1526 - Added variable framework for campaign and in-group scripts
# 51221-1200 - Added SCRIPT tab, layout and functionality
# 51221-1714 - Added auto-switch-to-SCRIPT-tab and auto-webform-popup
# 51222-1605 - Added VMail message blind transfer button to xfer-conf frame
# 51229-1028 - Added checks on web_form_address to allow for var in the DB value
# 60117-1312 - Added Transfer-conf frame toggle on button press
# 60208-1152 - Added DTMF-xfernumber preset links to xfer-conf frame
# 60213-1129 - Added osdial_users.hotkeys_active  for any user hotkeys
# 60213-1210 - Added ability to sort routing of calls by user_level
# 60214-0932 - Initial Callback calendar display framework
# 60214-1407 - Added ability to minimize the dispo screen to see info below
# 60215-1104 - Added ANYONE scheduled callbacks functionality
# 60410-1116 - Added persistant pause after dispo option and change dispo text
#            - Added web form submit that opens new window with dispo on submit
#            - Added PREVIOUS CALLBACK in customer info to flag callbacks
#            - Added link to try to hangup the call again in the dispo screen
#            - Added link noone-in-session screen to call agent phone again
#            - Added link customer-hungup screen to go straight to dispo screen
# 60410-1532 - Added agent status and campaign calls dialing display option
# 60411-1547 - Add ability to set callback as USERONLY and some basic formatting
# 60413-1752 - Add basic USERONLY callback frame and listings
# 60414-1039 - Changed manual dial preview and alt dial checkboxes to spans
#            - Added beta-level USERONLY callback functionality
#            - Added beta-level manual dialing with lead insertion functionality
# 60415-1534 - Fixed manual dial lead preview and fixed manuald dial override bug
# 60417-1108 - Added capability to do alt-number-dialing in auto-dial mode
#            - Changed several permissions to database-defined
# 60419-1529 - Prevent manual dial or callbacks when alt-dial lead not finished
# 60420-1647 - Fixed DiaLDiaLAltPhonE error, Call Agent Again DialControl error
# 60421-1229 - Check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60424-1005 - Fixed Alt phone disabled bug for callbacks and manual dials
# 60426-1058 - Added osdial_user setting for default blended check for CLOSER
# 60501-1008 - Added option to manual dial screen to manually lookup phone number
# 60503-1653 - Fixed agentonly_callback not-defined bug in scheduled callbacks screen
# 60504-1032 - Fixed manual dial display bug and transfer dispo alert bug
#            - Fixed recording filename display to not overrun 25 characters
# 60510-1051 - Added Wrapup timer and wrapup message on wrapup screen after dispo
# 60608-1453 - Added CLOSER campaign allowable in-groups limitations
# 60609-1123 - Added add-number-to-DNC-list function and manual dial check DNC
# 60619-1047 - Added variable filters to close security holes for login form
# 60804-1710 - fixed scheduled CALLBK for other languages build
# 60808-1145 - Added consultative transfers with customer data
# 60808-2232 - Added campaign name to pulldown for login screen
# 60809-1603 - Added option to locally transfer consult xfers
# 60809-1732 - Added recheck of transferred channels before customer gone mesg
# 60810-1011 - Fixed CXFER leave 3way call bugs
# 60816-1602 - Added ALLCALLS recording delay option allcalls_delay
# 60816-1716 - Fixed customer time display bug and client DST setting
# 60821-1555 - Added option to omit phone_code on dialout of leads
# 60821-1628 - Added ALLFORCE recording option
# 60821-1643 - Added no_delete_sessions option to not delete sessions
# 60822-0512 - Changed phone number fields to be maxlength of 12
# 60829-1531 - Made compatible with WeBRooTWritablE setting in dbconnect.php
# 60906-1152 - Added Previous CallBack info display span
# 60906-1715 - Allow for Local phone extension conferences
# 61004-1729 - Add ability to control volume per channel in "calls in this session"
# 61122-1341 - Added osdial_user_groups allowed_campaigns restrictions
# 61122-1523 - Added more SCRIPT variables
# 61128-2229 - Added osdial_live_agents and osdial_auto_calls manual dial entries
# 61130-1617 - Added lead_id to MonitorConf for recording_log
# 61221-1212 - Changed width to 760 to better fit 800x600 screens, widened SCRIPT
# 70109-1128 - Fixed wrapup timer bug
# 70109-1635 - Added option for HotKeys automatically dialing next number in manual mode
#            - Added option for alternate number dialing with hotkeys
# 70111-1600 - Added ability to use BLEND/INBND/*_C/*_B/*_I as closer campaigns
# 70118-1517 - Added osdial_agent_log and osdial_user_log logging of user_group
# 70201-1249 - Added FAST DIAL option for manually dialing, added UTF8 compatible code
# 70201-1703 - Fixed cursor bug for most text input fields
# 70202-1453 - Added first portions of Agent Pause Codes
# 70203-0108 - Finished Agent Pause Codes functionality
# 70203-0930 - Added dialed_number to webform output
# 70203-1010 - Added dialed_label to webform output
# 70206-1201 - Fixed allow_closers bug
# 70206-1332 - Added osdial_recording_override users setting function
# 70212-1252 - Fixed small issue with CXFER
# 70213-1018 - Changed CXFER and AXFER to update customer information before transfer
# 70214-1233 - Added queuemetrics_log_id field for server_id in queue_log
# 70215-1240 - Added queuemetrics_log_id field for server_id in queue_log
# 70222-1617 - Changed queue_log PAUSE/UNPAUSE to PAUSEALL/UNPAUSEALL
# 70226-1252 - Added Mute/UnMute to agent screen
# 70309-1035 - Allow amphersands and questions marks in comments to pass through
# 70313-1052 - Allow pound signs(hash) in comments to pass through
# 70316-1406 - Moved the MUTE button to be accessible during a transfer/conf
# 70319-1446 - Added agent-deactive-display and disable customer info update functions
# 70319-1626 - Added option to allow agent logins to campaigns with no leads in the hopper
# 70320-1501 - Added option to allow retry of leave-3way-call from dispo screen
# 70322-1545 - Added sipsak display ability
# 70510-1319 - Added onUnload force Logout
# 70806-1530 - Added Presets Dial links above agent mute button
# 70823-2118 - Fixed XMLHTTPRequest, HotKeys and Scheduled Callbacks issues with MSIE
# 70828-1443 - Added source_id to output of SCRIPTtab-IFRAME and WEBFORM
# 71022-1427 - Added formatting of the customer phone number in the main status bar
# 71029-1848 - Changed CLOSER-type campaign to not use campaign_id restrictions
# 71101-1204 - Fixed bug in callback calendar with DST
# 71116-0957 - Added campaign_weight and calls_today to the vla table insertion
# 71120-1719 - Added XMLHTPRequest lookup of allowable campaigns for agents during login
# 71122-0256 - Added auto-pause notification
# 71125-1751 - Changed Transfer section to allow for selection of in-groups to send calls to
# 71127-0408 - Added height and width settings for easier modification of screen size
# 71129-2025 - restricted callbacks count and list to campaign only
# 71226-1117 - added option to kick all calls from conference upon logout
# 80330-0823 - Added new AST_blind monitoring ability and minor cleanup for release
# 80402-0121 - Fixes for Manual Dial hangups and transfers
# 80428-0413 - UTF8 changes and testing
# 80507-0932 - Fixed Script display bug (+ instead of space)
# 80519-1425 - Added calls in queue display
#
# 90201-1001 - Branched to OSDial
#
# 90201-0931 - Modified screens to get uniform colors and a more professional look
#			Replaced all buttons, added a second Web Form,
#			Resized to work on 1024 wide screen, added additional section for new fields
#			Changed to new version (from 2.0.4-121)
# 090410-1156 - Added custom2 field
# 090410-1731 - Added allow_tab_switch
# 090515-0135 - Added preview_force_dial_time
# 090515-0140 - Added manual_preview_default
# 090520-1915 - Changed inbound in manual mode to work without the INBOUND_MAN dial status.

# The version/build variables get set to the SVN revision automatically in release package.
# Do not change.
$version = 'SVN_Version';
$build = 'SVN_Build';

require("dbconnect.php");
require('functions.php');

$DB=get_variable("DB");
$phone_login=get_variable("phone_login");
$phone_pass=get_variable("phone_pass");
$VD_login=get_variable("VD_login");
$VD_pass=get_variable("VD_pass");
$VD_campaign=get_variable("VD_campaign");
$relogin=get_variable("relogin");

if ($phone_login=='') $phone_login=get_variable("pl");
if ($phone_pass=='') $phone_pass=get_variable("pp");
if ($VD_campaign!='') $VD_campaign = preg_replace('/ /','',strtoupper($VD_campaign));

if ($flag_channels=='') {
		$flag_channels=0;
		$flag_string='';
}

### security strip all non-alphanumeric characters out of the variables ###
$DB=preg_replace('[^0-9a-z]','',$DB);
$phone_login=preg_replace('[^0-9a-zA-Z]','',$phone_login);
$phone_pass=preg_replace('[^0-9a-zA-Z]','',$phone_pass);
$VD_login=preg_replace('[^0-9a-zA-Z]','',$VD_login);
$VD_pass=preg_replace('[^0-9a-zA-Z]','',$VD_pass);
$VD_campaign=preg_replace('[^0-9a-zA-Z_]','',$VD_campaign);


$forever_stop=0;

if ($force_logout) {
    echo "You have now logged out. Thank you\n";
    exit;
}

$isdst = date("I");
$StarTtimE = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$FILE_TIME = date("Ymd-His");
$CIDdate = date("ymdHis");
$month_old = mktime(11, 0, 0, date("m"), date("d")-2,  date("Y"));
$past_month_date = date("Y-m-d H:i:s",$month_old);

$random = (rand(1000000, 9999999) + 10000000);

$multicomp=0;

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,agent_template,enable_multicompany FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) echo "$stmt\n";
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct) {
    $row=mysql_fetch_row($rslt);
    $non_latin =        $row[0];
    $agent_template =   $row[1];
    $multicomp =        $row[2];
    $i++;
}
##### END SETTINGS LOOKUP #####
###########################################

require("templates/default/display.php");
include("templates/" . $agent_template . "/display.php");


$conf_silent_prefix     = '7';  # osdial_conferences prefix to enter silently
$HKuser_level           = '5';  # minimum osdial user_level for HotKeys
$campaign_login_list    = '1';  # show drop-down list of campaigns at login	
$manual_dial_preview    = '1';  # allow preview lead option when manual dial
$multi_line_comments    = '1';  # set to 1 to allow multi-line comment box
$user_login_first       = '0';  # set to 1 to have the osdial_user login before the phone login
$view_scripts           = '1';  # set to 1 to show the SCRIPTS tab
$dispo_check_all_pause  = '0';  # set to 1 to allow for persistent pause after dispo
$callholdstatus         = '1';  # set to 1 to show calls on hold count
$agentcallsstatus       = '0';  # set to 1 to show agent status and call dialed count
$campagentstatctmax     = '3';  # Number of seconds for campaign call and agent stats
$show_campname_pulldown = '1';  # set to 1 to show campaign name on login pulldown
$webform_sessionname    = '1';  # set to 1 to include the session_name in webform URL
$local_consult_xfers    = '1';  # set to 1 to send consultative transfers from original server
$clientDST              = '1';  # set to 1 to check for DST on server for agent time
$no_delete_sessions     = '0';  # set to 1 to not delete sessions at logout
$volumecontrol_active   = '1';  # set to 1 to allow agents to alter volume of channels
$PreseT_DiaL_LinKs      = '1';  # set to 1 to show a DIAL link for Dial Presets
$LogiNAJAX              = '1';  # set to 1 to do lookups
$HidEMonitoRSessionS    = '1';  # set to 1 to hide remote monitoring channels from "session calls"
$LogouTKicKAlL          = '1';  # set to 1 to hangup all calls in session upon agent logout

$TEST_all_statuses		= '0';	# TEST variable allows all statuses in dispo screen

$BROWSER_HEIGHT         = 500;  # set to the minimum browser height, default=500
$BROWSER_WIDTH          = 980;  # set to the minimum browser width, default=770

### SCREEN WIDTH AND HEIGHT CALCULATIONS ###

$MASTERwidth=($BROWSER_WIDTH - 340);
$MASTERheight=($BROWSER_HEIGHT - 200);
if ($MASTERwidth < 430) {$MASTERwidth = '430';} 
if ($MASTERheight < 300) {$MASTERheight = '300';} 

$CAwidth = ($MASTERwidth + 340);        # 770 - cover all (none-in-session, customer hunngup, etc...)
$MNwidth = ($MASTERwidth + 330);        # 760 - main frame
$XFwidth = ($MASTERwidth + 320);        # 750 - transfer/conference
$HCwidth = ($MASTERwidth + 310);        # 740 - hotkeys and callbacks
$AMwidth = ($MASTERwidth + 270);        # 700 - agent mute and preset-dial links
$SSwidth = ($MASTERwidth - 120);        # 606 - scroll script
$SDwidth = ($MASTERwidth - 126);        # 600 - scroll script, customer data and calls-in-session
$HKwidth = ($MASTERwidth +  70);        # 500 - Hotkeys button
$HSwidth = ($MASTERwidth +   1);        # 431 - Header spacer

$DBheight = ($MASTERheight + 230);      # Debug
$HKheight = ($MASTERheight + 115);      # 405 - HotKey active Button
$AMheight = ($MASTERheight + 110);      # 400 - Agent mute and preset dial links
$MBheight = ($MASTERheight + 157);      # 365 - Manual Dial Buttons
$CBheight = ($MASTERheight + 140);      # 350 - Agent Callback, pause code, volume control Buttons and agent status
$SSheight = ($MASTERheight +  20);      # 331 - script content
$HTheight = ($MASTERheight +  10);      # 310 - transfer frame, callback comments and hotkey
$BPheight = ($MASTERheight - 275);      # 50  - bottom buffer


$US='_';
$CL=':';
$AT='@';
$DS='-';
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");
$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (preg_match('/443/',$server_port)) {
    $HTTPprotocol = 'https://';
} else {
    $HTTPprotocol = 'http://';
}
if ($server_port == '80' or $server_port == '443') {
	$server_port='';
} else {
    $server_port = ":" . $server_port;
}
$t1="OSDial"; if (preg_match('/^Sli/',$agent_template)) $t1=$agent_template;;
$agcPAGE = $HTTPprotocol . $server_name . $server_port .$script_name;
$agcDIR = preg_replace('/osdial.php/','',$agcPAGE);

echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<meta http-equiv=\"Cache-Control\" content=\"no-cache\">\n";
echo "<meta http-equiv=\"Pragma\" content=\"no-cache\">\n";
echo "<meta http-equiv=\"Expires\" content=\"0\">\n";
echo "<meta name=\"Copyright\" content=\"&copy; 2009-2010 Call Center Service Group, LC\">\n";
echo "<meta name=\"Copyright\" content=\"&copy; 2009-2010 Lott Caskey\">\n";
echo "<meta name=\"Copyright\" content=\"&copy; 2009-2010 Steve Szmidt\">\n";
echo "<meta name=\"Robots\" content=\"none\">\n";
echo "<meta name=\"Version\" content=\"$version/$build\">\n";
echo "<!-- VERSION: $version     BUILD: $build -->\n";

$welcome_span  = "<span style='position:absolute;left:0px;top:0px;z-index:300;visibility:hidden;' id=WelcomeBoxA>\n";
$welcome_span .= "  <table class=acrossagent border=1 width=" . ($CAwidth + 30) . " height=550 cellspacing=50>\n";
$welcome_span .= "    <tr>\n";
$welcome_span .= "      <td align=center bgcolor=$panel_bg>\n";
$welcome_span .= "        <span id=WelcomeBoxAt style='color:$default_fc;font-family:Arial,Helvetica;border:none;'>\n";
$welcome_span .= "          <span id=WelcomeBoxTitle style='font-size:36px;'><b>$t1</b></span>\n";
$welcome_span .= "          <font size=3>\n";
$welcome_span .= "            <br><br><br><br><br><br>One moment please.<br><br><br><br><br>\n";
$welcome_span .= "            <font size=2>\n";
$welcome_span .= "              <span id=WelcomeBoxStatus>Connecting...<br>&nbsp;<br>&nbsp;</span>\n";
$welcome_span .= "            </font>\n";
$welcome_span .= "            <br><br><br>\n";
$welcome_span .= "          </font>\n";
$welcome_span .= "        </span>\n";
$welcome_span .= "      </td>\n";
$welcome_span .= "    </tr>\n";
$welcome_span .= "  </table>\n";
$welcome_span .= "</span>\n";

$wsc = "<span style=position:absolute;left:0px;top:0px;z-index:300;visibility:hidden; id=WelcomeBoxA>";
$wsc .= "<table class=acrossagent border=1 width=" . ($CAwidth + 30) . " height=550 cellspacing=50>";
$wsc .= "<tr>";
$wsc .= "<td align=center bgcolor=$panel_bg>";
$wsc .= "<span id=WelcomeBoxAt style=color:$default_fc;font-family:Arial,Helvetica;border:none;>";
$wsc .= "<span id=WelcomeBoxTitle style=font-size:36px;><b>$t1</b></font>";
$wsc .= "<font size=3>";
$wsc .= "<br><br><br><br><br><br>One moment please.<br><br><br><br><br>";
$wsc .= "<font size=2>";
$wsc .= "<span id=WelcomeBoxStatus>Connecting...<br>&nbsp;<br>&nbsp;</span>";
$wsc .= "</font>";
$wsc .= "<br><br><br>";
$wsc .= "</font>";
$wsc .= "</span>";
$wsc .= "</td>";
$wsc .= "</tr>";
$wsc .= "</table>";
$wsc .= "</span>";

$company_prefix='';
if ($multicomp > 0) {
    $company_prefix = substr($phone_login,0,3);
    if ($VD_login != "" and $phone_login != "") {
        if (substr($VD_login,0,3) != substr($phone_login,0,3)) {
            $phone_login='';
            $phone_pass='';
            $VD_login='';
            $VD_pass='';
            echo "<center><font color=red>ERROR: The Phone Login and User Login must belong to the same company.</font></center>";
        }
    }
}

if ($campaign_login_list > 0) {
    $camp_form_code  = "<select size=1 name=VD_campaign id=VD_campaign onFocus=\"login_allowable_campaigns()\">\n";
    $camp_form_code .= "<option value=\"\"></option>\n";

    $LOGallowed_campaignsSQL='';
    if ($relogin == 'YES') {
        $stmt="SELECT user_group from osdial_users where user='$VD_login' and pass='$VD_pass'";
        if ($non_latin > 0) $rslt=mysql_query("SET NAMES 'UTF8'");

        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $VU_user_group=$row[0];

        $stmt="SELECT allowed_campaigns from osdial_user_groups where user_group='$VU_user_group';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if (!preg_match("/ALL-CAMPAIGNS/",$row[0])) {
            $LOGallowed_campaignsSQL = preg_replace('/ -/','',$row[0]);
            $LOGallowed_campaignsSQL = preg_replace('/ /',"','",$LOGallowed_campaignsSQL);
            $LOGallowed_campaignsSQL = "and campaign_id IN('$LOGallowed_campaignsSQL')";
        }
    }

    if ($multicomp) $LOGallowed_campaignsSQL .= " and campaign_id LIKE '" . $company_prefix . "%'";

    $stmt="SELECT campaign_id,campaign_name from osdial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id";
    if ($non_latin > 0) $rslt=mysql_query("SET NAMES 'UTF8'");
    $rslt=mysql_query($stmt, $link);
    $camps_to_print = mysql_num_rows($rslt);

    $o=0;
    while ($camps_to_print > $o) {
        $rowx=mysql_fetch_row($rslt);
        $campname = '';
        if ($show_campname_pulldown) $campname = " - $rowx[1]";

        if ($VD_campaign == $rowx[0]) {
            $camp_form_code .= "<option value=\"$rowx[0]\" SELECTED>" . mclabel($rowx[0]) . "$campname</option>\n";
        } else {
            $camp_form_code .= "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . "$campname</option>\n";
        }
        $o++;
    }
    $camp_form_code .= "</select>\n";
} else {
    $camp_form_code = "<input type=text name=VD_campaign size=10 maxlength=20 value=\"$VD_campaign\">\n";
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $agent_template . "/styles.css\" media=\"screen\">\n";

echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";


//  Relogin
if ($relogin == 'YES') {
    echo "<title>$t1 web client: Re-Login</title>\n";
    echo "</head>\n";

    echo "<body bgcolor=white name=osdial>\n";
    echo $welcome_span;

    echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
    echo "<input type=hidden name=DB value=\"$DB\">\n";

    echo "<div class=containera>\n";

    echo "<table class=acrosslogin2 width=500 cellpadding=0 cellspacing=0 border=0>\n";
    echo "  <tr>\n";
    echo "    <td width=22><img src=\"templates/" . $agent_template . "/images/AgentTopLeft2.png\" width=22 height=22 align=left></td>\n";
    echo "    <td class=across-top align=center colspan=2>&nbsp;</td>\n";
    echo "    <td width=22><img src=\"templates/" . $agent_template . "/images/AgentTopRightS.png\" width=22 height=22 align=right></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left>&nbsp;</td>\n";
    echo "    <td align=center colspan=2><font color=" . $login_fc . "><b>Agent Login</b></td>\n";
    echo "    <td align=left class=rborder>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=right colspan=4 class=rborder>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Phone&nbsp;Login:&nbsp;</td>\n";
    echo "    <td align=left><input type=text name=phone_login size=10 maxlength=20 value=\"$phone_login\"></td>\n";
    echo "    <td align=left class=rborder>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Phone&nbsp;Password:&nbsp;</td>\n";
    echo "    <td align=left><input type=password name=phone_pass size=10 maxlength=20 value=\"$phone_pass\"></td>\n";
    echo "    <td align=right class=rborder>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">User&nbsp;Login:&nbsp;</td>\n";
    echo "    <td align=left><input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\"></td>\n";
    echo "    <td align=left class=rborder>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">User&nbsp;Password:&nbsp;</td>\n";
    echo "    <td align=left><input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"></td>\n";
    echo "    <td align=left class=rborder>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Campaign:&nbsp;</td>\n";
    echo "    <td align=left>$camp_form_code</td>\n";
    echo "    <td align=left class=rborder>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr><td colspan=4 class=rborder>&nbsp;</td></tr>\n";
    echo "  <tr>\n";
    echo "    <td align=center colspan=4 class=rborder><input type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=SUBMIT></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left colspan=4 class=rbborder><font size=1>&nbsp;Version: $version&nbsp;&nbsp;&nbsp;Build: $build</td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "</div>\n";
    echo "</form>\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;
}

if ($user_login_first == 1) {
    if (strlen($VD_login)<1 or strlen($VD_pass)<1 or strlen($VD_campaign)<1) {
        echo "<title>$t1 web client: Campaign Login</title>\n";
        echo "</head>\n";
        echo "<body bgcolor=white name=osdial>\n";
        
        echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";
        
        echo "<div class=containera>\n";
        echo "<table class=acrosslogin2 width=500 cellpadding=0 cellspacing=0 border=0>\n";
        echo "  <tr>\n";
        echo "    <td width=22><img src=\"templates/" . $agent_template . "/images/AgentTopLeft2.png\" width=22 height=22 align=left></td>\n";
        echo "    <td class=across-top align=center colspan=2>&nbsp;</td>\n";
        echo "    <td width=22><img src=\"templates/" . $agent_template . "/images/AgentTopRightS.png\" width=22 height=22 align=right></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left>&nbsp;</td>\n";
        echo "    <td align=center colspan=2><font color=" . $login_fc . "><b>Agent Login</b></td>\n";
        echo "    <td align=left class=rborder>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right colspan=4 class=rborder>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">User&nbsp;Login:&nbsp;</td>\n";
        echo "    <td align=left><input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\"></td>\n";
        echo "    <td align=left class=rborder>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">User&nbsp;Password:&nbsp;</td>\n";
        echo "    <td align=left><input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"></td>\n";
        echo "    <td align=left class=rborder>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">Campaign:&nbsp;</td>\n";
        echo "    <td align=left>$camp_form_code</td>\n";
        echo "    <td align=left class=rborder>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr><td colspan=4 class=rborder>&nbsp;</td></tr>\n";
        echo "  <tr>\n";
        echo "     <td align=center colspan=4 class=rborder><input type=submit name=SUBMIT value=SUBMIT></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left colspan=4 class=rbborder><font size=1>&nbsp;Version: $version&nbsp;&nbsp;&nbsp;Build: $build</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div>\n";
        echo "</form>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;
    } else {
        if (strlen($phone_login)<2 or strlen($phone_pass)<2) {
            $stmt="SELECT phone_login,phone_pass from osdial_users where user='$VD_login' and pass='$VD_pass' and user_level > 0;";
            if ($DB) echo "|$stmt|\n";
            
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $phone_login=$row[0];
            $phone_pass=$row[1];
    
            echo "<title>$t1 web client: Login</title>\n";
            echo "</head>\n";
            echo "<body bgcolor=white name=osdial>\n";
            echo $welcome_span;
            echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
            echo "<input type=hidden name=DB value=\"$DB\">\n";
            echo "<br><br><br>\n";
            echo "<table class=accrosslogin width=460 cellpadding=0 cellspacing=0>\n";
            echo "  <tr>\n";
            echo "    <td align=left>&nbsp;&nbsp;<font color=blue>$t1</font></td>\n";
            echo "    <td align=center><font color=white>Login</font></td>\n";
            echo "  </tr>\n";
            echo "  <tr><td align=left colspan=2><font size=1>&nbsp;</font></td></tr>\n";
            echo "  <tr>\n";
            echo "    <td align=right><font color=white>Phone Login:</font></td>\n";
            echo "    <td align=left><input type=text name=phone_login size=10 maxlength=20 value=\"$phone_login\"></td>\n";
            echo "  </tr>\n";
            echo "  <tr>\n";
            echo "    <td align=right><font color=white>Phone Password:</font></td>\n";
            echo "    <td align=left><input type=password name=phone_pass size=10 maxlength=20 value=\"$phone_pass\"></td>\n";
            echo "  </tr>\n";
            echo "  <tr>\n";
            echo "    <td align=right><font color=white>User Login:</font></td>\n";
            echo "    <td align=left><input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\"></td>\n";
            echo "  </tr>\n";
            echo "  <tr>\n";
            echo "    <td align=right><font color=white>User Password:</font></td>\n";
            echo "    <td align=left><input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"></td>\n";
            echo "  </tr>\n";
            echo "  <tr>\n";
            echo "    <td align=right><font color=white>Campaign:</font></td>\n";
            echo "    <td align=left><span id=\"LogiNCamPaigns\">$camp_form_code</span></td>\n";
            echo "  </tr>\n";
            echo "  <tr><td colspan=2>&nbsp;</td></tr>\n";
            echo "  <tr>\n";
            echo "    <td align=center colspan=2>\n";
            echo "      <input type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=SUBMIT> &nbsp; <span id=\"LogiNReseT\"></span>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr><td align=left colspan=2><font size=1><br>VERSION: $version &nbsp; &nbsp; &nbsp; BUILD: $build</font></td></tr>\n";
            echo "</table>\n";
            echo "</form>\n";
            echo "</body>\n";
            echo "</html>\n";
            exit;
        }
    }
}

// Phone Login from welcome scren
if (strlen($phone_login)<2 or strlen($phone_pass)<2) {
    echo "<title>$t1 web client:  Phone Login</title>\n";
    echo "</head>\n";
    echo "<body bgcolor=white name=osdial>\n";
    
    echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
    echo "<input type=hidden name=DB value=\"$DB\">\n";
    
    echo "<div class=containera>\n";
    echo "<table align=center class=acrosslogin2 width=460 cellpadding=0 cellspacing=0 border=0>\n";
    echo "  <tr>\n";
    echo "    <td width='22'><img src='templates/" . $agent_template . "/images/AgentTopLeft2.png' width='22' height='22' align='left'></td>\n";
    echo "    <td class='across-top' align='center' colspan=2></td>\n";
    echo "    <td width='22'><img src='templates/" . $agent_template . "/images/AgentTopRightS.png' width='22' height='22' align='right'></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left><font size=1>&nbsp;</td>\n";
    echo "    <td align=center colspan=2><font color=" . $login_fc . "><b>Login To Your Phone</font></td>\n";
    echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td colspan=4 class=rborder>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Phone Login:&nbsp;</font></td>\n";
    echo "    <td align=left><input type=text name=phone_login size=10 maxlength=20 value=\"\"></td>\n";
    echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Phone Password:&nbsp;</font></td>\n";
    echo "    <td align=left><input type=password name=phone_pass size=10 maxlength=20 value=\"\"></td>\n";
    echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr><td colspan=4 class=rborder>&nbsp;</td></tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "    <td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT> &nbsp; <span id=\"LogiNReseT\"></span></td>\n";
    echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left colspan=4 class=rbborder><font size=1><br>&nbsp;Version: $version &nbsp; &nbsp; &nbsp; Build: $build</font></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "</div>\n";
    echo "</form>\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;

} else {
    if ($WeBRooTWritablE > 0) $fp = fopen ("./osdial_auth_entries.txt", "a");
    $VDloginDISPLAY=0;

    if (strlen($VD_login)<2 or strlen($VD_pass)<2 or strlen($VD_campaign)<2) {
        $VDloginDISPLAY=1;
    } else {
        $stmt="SELECT count(*) from osdial_users where user='$VD_login' and pass='$VD_pass' and user_level > 0;";
        if ($DB) echo "|$stmt|\n";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $auth=$row[0];

        if($auth>0) {
            $myscripts = Array();

            $login=strtoupper($VD_login);
            $password=strtoupper($VD_pass);
            ##### grab the full name of the agent
            $stmt="SELECT full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,osdial_recording,osdial_transfers,closer_default_blended,user_group,osdial_recording_override,manual_dial_allow_skip,xfer_agent2agent,script_override from osdial_users where user='$VD_login' and pass='$VD_pass'";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $LOGfullname=$row[0];
            $user_level=$row[1];
            $VU_hotkeys_active=$row[2];
            $VU_agent_choose_ingroups=$row[3];
            $VU_scheduled_callbacks=$row[4];
            $agentonly_callbacks=$row[5];
            $agentcall_manual=$row[6];
            $VU_osdial_recording=$row[7];
            $VU_osdial_transfers=$row[8];
            $VU_closer_default_blended=$row[9];
            $VU_user_group=$row[10];
            $VU_osdial_recording_override=$row[11];
            $VU_manual_dial_allow_skip=$row[12];
            $LOGxfer_agent2agent=$row[13];
            $script_override = $row[14];
            if ($script_override!='') $myscripts[$script_override] = 1;

            if ($WeBRooTWritablE > 0) {
                fwrite ($fp, "vdweb|GOOD|$date|$VD_login|$VD_pass|$ip|$browser|$LOGfullname|\n");
                fclose($fp);
            }
            $user_abb = "$VD_login$VD_login$VD_login$VD_login";

            while (strlen($user_abb) > 4 and $forever_stop < 200) {
                $user_abb = preg_replace('/^./','',$user_abb);
                $forever_stop++;
            }

            $stmt="SELECT allowed_campaigns from osdial_user_groups where user_group='$VU_user_group';";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $LOGallowed_campaigns = $row[0];

            if (!preg_match("/ $VD_campaign /i",$LOGallowed_campaigns) and !preg_match("/ALL-CAMPAIGNS/",$LOGallowed_campaigns)) {
                echo "<title>$t1 web client: $t1 Campaign Login</title>\n";
                echo "</head>\n";
                echo "<body bgcolor=white name=osdial>\n";
                echo $welcome_span;
                echo "<b>Sorry, you are not allowed to login to this campaign: $VD_campaign</b>\n";
                echo "<form action=\"$PHP_SELF\" method=post>\n";
                echo "<input type=hidden name=DB value=\"$DB\">\n";
                echo "<input type=hidden name=phone_login value=\"$phone_login\">\n";
                echo "<input type=hidden name=phone_pass value=\"$phone_pass\">\n";
                echo "Login: <input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\">\n<br>";
                echo "Password: <input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"><br>\n";
                echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br>\n";
                echo "<input type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=SUBMIT> &nbsp; \n";
                echo "<span id=\"LogiNReseT\"></span>\n";
                echo "</form>\n\n";
                echo "</body>\n\n";
                echo "</html>\n\n";
                exit;
            }

            ##### check to see that the campaign is active
            $stmt="SELECT count(*) FROM osdial_campaigns where campaign_id='$VD_campaign' and active='Y';";
            if ($DB) echo "|$stmt|\n";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $CAMPactive=$row[0];
            if($CAMPactive>0) {
                if ($TEST_all_statuses > 0) {
                    $selectableSQL = '';
                } else {
                    $selectableSQL = "1=1 and";
                }
                $VARstatuses='';
                $VARstatusnames='';
                $DISPstatus = Array();
                $statremove = "'NEW'";
                ##### grab the campaign-specific statuses that can be used for dispositioning by an agent
                $stmt="SELECT status,status_name,IF(selectable='Y',1,0) FROM osdial_campaign_statuses WHERE $selectableSQL status NOT IN ($statremove) and campaign_id='$VD_campaign' order by status limit 50;";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $VD_statuses_camp = mysql_num_rows($rslt);
                $i=0;
                $j=0;
                while ($j < $VD_statuses_camp) {
                    $row=mysql_fetch_row($rslt);
                    $DISPstatus[$row[0]] = $row[2];
                    if ($row[2] > 0) {
                        $statuses[$i] =$row[0];
                        $status_names[$i] =$row[1];
                        $VARstatuses = "$VARstatuses'$statuses[$i]',";
                        $VARstatusnames = "$VARstatusnames'$status_names[$i]',";
                        $statremove .= ",'$statuses[$i]'";
                        $i++;
                    }
                    $j++;
                }

                ##### grab the statuses that can be used for dispositioning by an agent
                $stmt="SELECT status,status_name,IF(selectable='Y',1,0) FROM osdial_statuses WHERE $selectableSQL status NOT IN ($statremove) order by status limit 50;";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $VD_statuses_ct = mysql_num_rows($rslt);
                $j=0;
                while ($j < $VD_statuses_ct) {
                    $row=mysql_fetch_row($rslt);
                    if ($row[2] > 0 and preg_match('/^$|^1$/',$DISPstatus[$row[0]])) {
                        $statuses[$i] =$row[0];
                        $status_names[$i] =$row[1];
                        $VARstatuses = "$VARstatuses'$statuses[$i]',";
                        $VARstatusnames = "$VARstatusnames'$status_names[$i]',";
                        $i++;
                    }
                    $j++;
                }

                $VD_statuses_ct = $i;
                $VARstatuses = substr("$VARstatuses", 0, -1); 
                $VARstatusnames = substr("$VARstatusnames", 0, -1); 

                ##### grab the campaign-specific HotKey statuses that can be used for dispositioning by an agent
                $stmt="SELECT hotkey,status,status_name,xfer_exten FROM osdial_campaign_hotkeys WHERE selectable='Y' and status != 'NEW' and campaign_id='$VD_campaign' order by hotkey limit 9;";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $HK_statuses_camp = mysql_num_rows($rslt);
                $w=0;
                $HKboxA='';
                $HKboxB='';
                $HKboxC='';
                while ($w < $HK_statuses_camp) {
                    $row=mysql_fetch_row($rslt);
                    $HKhotkey[$w] =$row[0];
                    $HKstatus[$w] =$row[1];
                    $HKstatus_name[$w] =$row[2];
                    $HKxfer_exten[$w] =$row[3];
                    $HKhotkeys = "$HKhotkeys'$HKhotkey[$w]',";
                    $HKstatuses = "$HKstatuses'$HKstatus[$w]',";
                    $HKstatusnames = "$HKstatusnames'$HKstatus_name[$w]',";
                    $HKxferextens = "$HKxferextens'$HKxfer_exten[$w]',";
                    if ($w < 3) $HKboxA = "$HKboxA <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br>";
                    if ($w >= 3 and $w < 6) $HKboxB = "$HKboxB <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br>";
                    if ($w >= 6) $HKboxC = "$HKboxC <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br>";
                    $w++;
                }
                $HKhotkeys = substr("$HKhotkeys", 0, -1); 
                $HKstatuses = substr("$HKstatuses", 0, -1); 
                $HKstatusnames = substr("$HKstatusnames", 0, -1); 
                $HKxferextens = substr("$HKxferextens", 0, -1); 

                ##### grab the statuses to be dialed for your campaign as well as other campaign settings
                $stmt="SELECT park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,xfer_groups,web_form_address2,allow_tab_switch,preview_force_dial_time,manual_preview_default,web_form_extwindow,web_form2_extwindow,dial_method,submit_method,use_custom2_callerid,campaign_cid_name,xfer_cid_mode,use_cid_areacode_map,carrier_id,email_templates FROM osdial_campaigns where campaign_id = '$VD_campaign';";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $row=mysql_fetch_row($rslt);
                $park_ext =                $row[0];
                $park_file_name =            $row[1];
                $web_form_address =            $row[2];
                $allow_closers =            $row[3];
                $auto_dial_level =            $row[4];
                $dial_timeout =            $row[5];
                $dial_prefix =                $row[6];
                $campaign_cid =            $row[7];
                $campaign_vdad_exten =        $row[8];
                $campaign_rec_exten =        $row[9];
                $campaign_recording =        $row[10];
                $campaign_rec_filename =        $row[11];
                $campaign_script =            $row[12];
                if ($campaign_script!='') $myscripts[$campaign_script] = 1;
                $get_call_launch =            $row[13];
                $campaign_am_message_exten =     $row[14];
                $xferconf_a_dtmf =            $row[15];
                $xferconf_a_number =        $row[16];
                $xferconf_b_dtmf =            $row[17];
                $xferconf_b_number =        $row[18];
                $alt_number_dialing =        $row[19];
                $VC_scheduled_callbacks =    $row[20];
                $wrapup_seconds =            $row[21];
                $wrapup_message =            $row[22];
                $closer_campaigns =            $row[23];
                $use_internal_dnc =            $row[24];
                $allcalls_delay =            $row[25];
                $omit_phone_code =            $row[26];
                $agent_pause_codes_active =    $row[27];
                $no_hopper_leads_logins =    $row[28];
                $campaign_allow_inbound =    $row[29];
                $manual_dial_list_id =        $row[30];
                $default_xfer_group =        $row[31];
                $xfer_groups =                $row[32];
                $web_form_address2 =         $row[33];
                $allow_tab_switch =         $row[34];
                $previewFD_time =             $row[35];
                $manual_preview_default =     $row[36];
                $web_form_extwindow =         $row[37];
                $web_form2_extwindow =         $row[38];
                $dial_method =         $row[39];
                $submit_method =         $row[40];
                $use_custom2_callerid =         $row[41];
                $campaign_cid_name =         $row[42];
                $xfer_cid_mode =         $row[43];
                $use_cid_areacode_map =         $row[44];
                $carrier_id =         $row[45];
                $email_templates =         $row[46];
                if ($email_templates) {
                    $ets = explode(',',$email_templates);
                    $email_templates='';
                    foreach ($ets as $eto) {
                        $et = get_first_record($link, 'osdial_email_templates', '*', "et_id='" . $eto . "' AND active='Y'");
                        $email_templates.=$et['et_id'] . ',';
                    }
                    $email_templates = rtrim($email_templates,',');
                }

                if ($previewFD_time == "") $previewFD_time = "0";
                if ($use_custom2_callerid != "Y") $use_custom2_callerid = "N";

                if (!preg_match('/DISABLED/i',$VU_osdial_recording_override) and $VU_osdial_recording > 0) {
                    $campaign_recording = $VU_osdial_recording_override;
                    print "<!-- USER RECORDING OVERRIDE: |$VU_osdial_recording_override|$campaign_recording| -->\n";
                }
                if ($VC_scheduled_callbacks=='Y' and $VU_scheduled_callbacks=='1') { $scheduled_callbacks='1'; } else { $scheduled_callbacks='0'; }
                if ($alt_number_dialing=='Y') {$alt_phone_dialing='1';} else {$alt_phone_dialing='0';}
                if ($manual_preview_default=='Y') {$manual_preview_default='1';} else {$manual_preview_default='0';}
                if ($web_form_extwindow=='Y') {$web_form_extwindow='1';} else {$web_form_extwindow='0';}
                if ($web_form2_extwindow=='Y') {$web_form2_extwindow='1';} else {$web_form2_extwindow='0';}
                if ($campaign_allow_inbound=='Y') {$campaign_allow_inbound='1';} else {$campaign_allow_inbound='0';}
                if ($dial_method=='MANUAL' and $campaign_allow_inbound > 0) {$VU_closer_default_blended='0'; $inbound_man=1;} else {$inbound_man=0;}

                if ($VU_osdial_recording=='0') $campaign_recording='NEVER';
                $closer_campaigns = preg_replace("/^ | -$/","",$closer_campaigns);
                $closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
                $closer_campaigns = "'$closer_campaigns'";

                if ($submit_method=='WEBFORM1') {
                    $submit_method=1;
                } elseif ($submit_method=='WEBFORM2') {
                    $submit_method=2;
                } else {
                    $submit_method=0;
                }

                $dial_context=$ext_context;
                if ($carrier_id>0) {
                    $stmt="SELECT name FROM osdial_carriers WHERE id='$carrier_id' AND active='Y' LIMIT 1;";
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $carriers = mysql_num_rows($rslt);
                    if ($carriers > 0) {
                        $row=mysql_fetch_row($rslt);
                        $dial_context = "OOUT" . $row[0];
                    }
                }

                if ($use_cid_areacode_map=='Y') {
                    $stmt="SELECT areacode,cid_number,cid_name FROM osdial_campaign_cid_areacodes WHERE campaign_id='$VD_campaign';";
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VD_cid_areacodes = mysql_num_rows($rslt);
                    $j=0;
                    while ($j < $VD_cid_areacodes) {
                        $row=mysql_fetch_row($rslt);
                        $cid_areacodes[$j] = $row[0];
                        $cid_areacode_numbers[$j] = $row[1];
                        $cid_areacode_names[$j] = $row[2];
                        $VARcid_areacodes .= "'" . $row[0] . "',";
                        $VARcid_areacode_numbers .= "'" . $row[1] . "',";
                        $VARcid_areacode_names .= "'" . $row[2] . "',";
                        $j++;
                    }
                    $VD_cid_areacodes_ct += $VD_cid_areacodes_ct;
                    $VARcid_areacodes = rtrim($VARcid_areacodes,',');
                    $VARcid_areacode_numbers = rtrim($VARcid_areacode_numbers,',');
                    $VARcid_areacode_names = rtrim($VARcid_areacode_names,',');
                }

                if ($agent_pause_codes_active=='Y') {
                    ##### grab the pause codes for this campaign
                    $stmt="SELECT pause_code,pause_code_name FROM osdial_pause_codes WHERE campaign_id='$VD_campaign' order by pause_code limit 50;";
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VD_pause_codes = mysql_num_rows($rslt);
                    $j=0;
                    while ($j < $VD_pause_codes) {
                        $row=mysql_fetch_row($rslt);
                        $pause_codes[$i] =$row[0];
                        $pause_code_names[$i] =$row[1];
                        $VARpause_codes = "$VARpause_codes'$pause_codes[$i]',";
                        $VARpause_code_names = "$VARpause_code_names'$pause_code_names[$i]',";
                        $i++;
                        $j++;
                    }
                    $VD_pause_codes_ct = ($VD_pause_codes_ct+$VD_pause_codes);
                    $VARpause_codes = substr("$VARpause_codes", 0, -1); 
                    $VARpause_code_names = substr("$VARpause_code_names", 0, -1); 
                }

                ##### grab the inbound groups to choose from if campaign contains CLOSER
                $VARingroups="''";
                if ($campaign_allow_inbound > 0) {
                    $VARingroups='';
                    $closerSQL = "group_id IN($closer_campaigns)";
                    if ($LOGxfer_agent2agent > 0) $closerSQL = "($closerSQL OR group_id = 'A2A_$VD_login')";
                    $stmt="select group_id,ingroup_script from osdial_inbound_groups where active = 'Y' and $closerSQL order by group_id limit 60;";
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $closer_ct = mysql_num_rows($rslt);
                    $INgrpCT=0;
                    while ($INgrpCT < $closer_ct) {
                        $row=mysql_fetch_row($rslt);
                        $closer_groups[$INgrpCT] =$row[0];
                        $ingroup_script = $row[1];
                        if ($ingroup_script!='') $myscripts[$ingroup_script] = 1;
                        $VARingroups = "$VARingroups'$closer_groups[$INgrpCT]',";
                        $INgrpCT++;
                    }
                    $VARingroups = substr("$VARingroups", 0, -1); 
                }

                ##### grab the allowable inbound groups to choose from for transfer options
                $xfer_groups = preg_replace("/^ | -$/","",$xfer_groups);
                $xfer_groups = preg_replace("/ /","','",$xfer_groups);
                $xfer_groups = "'$xfer_groups'";
                $VARxfergroups="''";
                if ($allow_closers == 'Y') {
                    $VARxfergroups='';
                    $xferSQL = "group_id IN($xfer_groups)";
                    if ($LOGxfer_agent2agent > 0) $xferSQL = "($xferSQL OR group_id LIKE 'A2A_$company_prefix%') AND group_id != 'A2A_$VD_login'";
                    $stmt="select group_id,group_name from osdial_inbound_groups where active = 'Y' and $xferSQL order by group_id limit 60;";
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $xfer_ct = mysql_num_rows($rslt);
                    $XFgrpCT=0;
                    while ($XFgrpCT < $xfer_ct) {
                        $row=mysql_fetch_row($rslt);
                        $VARxfergroups = "$VARxfergroups'$row[0]',";
                        $VARxfergroupsnames = "$VARxfergroupsnames'$row[1]',";
                        if ($row[0] == "$default_xfer_group") {$default_xfer_group_name = $row[1];}
                        $XFgrpCT++;
                    }
                    $VARxfergroups = substr("$VARxfergroups", 0, -1); 
                    $VARxfergroupsnames = substr("$VARxfergroupsnames", 0, -1); 
                }

                ##### grab the number of leads in the hopper for this campaign
                $stmt="SELECT count(*) FROM osdial_hopper WHERE campaign_id = '$VD_campaign' AND status IN ('API','READY');";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $row=mysql_fetch_row($rslt);
                $campaign_leads_to_call = $row[0];
                print "<!-- $campaign_leads_to_call - leads left to call in hopper -->\n";

            } else {
                $VDloginDISPLAY=1;
                $VDdisplayMESSAGE = "Campaign not active, please try again<br>";
            }
        } else {
            if ($WeBRooTWritablE > 0) {
                fwrite ($fp, "vdweb|FAIL|$date|$VD_login|$VD_pass|$ip|$browser|\n");
                fclose($fp);
            }
            $VDloginDISPLAY=1;
            $VDdisplayMESSAGE = "Login incorrect, please try again<br>";
        }
    }


    if ($VDloginDISPLAY) {
        echo "<title>$t1 web client: Campaign Login</title>\n";
        echo "</head>\n";
        echo "<body bgcolor=white name=osdial>\n";
        echo $welcome_span;
    
        echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";
        echo "<input type=hidden name=phone_login value=\"$phone_login\">\n";
        echo "<input type=hidden name=phone_pass value=\"$phone_pass\">\n";
    
        echo "<div class=containera>\n";
        echo "<table align=center class=acrosslogin2 width=460 cellpadding=0 cellspacing=0 border=0>\n";
        echo "  <tr>\n";
        echo "    <td width=22><img src=\"templates/" . $agent_template . "/images/AgentTopLeft2.png\" width=22 height=22 align=left></td>\n";
        echo "    <td class=across-top align=center colspan=2></td>\n";
        echo "    <td width=22><img src=\"templates/" . $agent_template . "/images/AgentTopRightS.png\" width=22 height=22 align=right></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left>&nbsp;&nbsp;</td>\n";
        echo "    <td align=center colspan=2><font color=" . $login_fc . "><b>Login To A Campaign</b></font></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left colspan=4 class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">User Login:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\"></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <rt>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">User Password:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">Campaign:&nbsp;</font></td>\n";
        echo "    <td align=left><span id=\"LogiNCamPaigns\">$camp_form_code</span></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr><td colspan=4 class=rborder>&nbsp;</td></tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=center colspan=2><input type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=SUBMIT>&nbsp;<span id=\"LogiNReseT\"></span></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left colspan=4 class=rbborder><font size=1><br>&nbsp;Version: $version&nbsp;&nbsp;&nbsp;Build: $build</font></td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div>\n";
        echo "</form>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;
    }

    $authphone=0;
    $stmt="SELECT count(*) from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
    if ($DB) echo "|$stmt|\n";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $authphone=$row[0];
    if (!$authphone) {
        echo "<title>$t1 web client: Phone Login Error</title>\n";
        echo "</head>\n";
        echo "<body bgcolor=white name=osdial>\n";
    
        echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";
    
        echo "<div class=containera>\n";
        echo "<table align=center class=acrosslogin2 width=460 cellpadding=0 cellspacing=0 border=0>\n";
        echo "  <tr>\n";
        echo "    <td width='22'><img src='templates/" . $agent_template . "/images/AgentTopLeft2.png' width='22' height='22' align='left'></td>\n";
        echo "    <td class='across-top' align='center' colspan=2></td>\n";
        echo "    <td width='22'><img src='templates/" . $agent_template . "/images/AgentTopRightS.png' width='22' height='22' align='right'></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=center colspan=2><font color=" . $login_fc . "><b><font color='red'>Invalid Login, please try again!</font></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td colspan=4 class=rborder>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">Phone Login:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=text name=phone_login size=10 maxlength=20 value=\"\"></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">Phone Password:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=password name=phone_pass size=10 maxlength=20 value=\"\"></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr><td colspan=4 class=rborder>&nbsp;</td></tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT> &nbsp; <span id=\"LogiNReseT\"></span></td>\n";
        echo "    <td align=left class=rborder><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left colspan=4 class=rbborder><font size=1><br>&nbsp;Version: $version &nbsp; &nbsp; &nbsp; Build: $build</font></td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div>\n";
        echo "</form>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;

    } else {
        echo "<title>$t1 web client</title>\n";
        $stmt="SELECT * from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
        if ($DB) echo "|$stmt|\n";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $extension=$row[0];
        $dialplan_number=$row[1];
        $voicemail_id=$row[2];
        $phone_ip=$row[3];
        $computer_ip=$row[4];
        $server_ip=$row[5];
        $login=$row[6];
        $pass=$row[7];
        $status=$row[8];
        $active=$row[9];
        $phone_type=$row[10];
        $fullname=$row[11];
        $company=$row[12];
        $picture=$row[13];
        $messages=$row[14];
        $old_messages=$row[15];
        $protocol=$row[16];
        $local_gmt=$row[17];
        $ASTmgrUSERNAME=$row[18];
        $ASTmgrSECRET=$row[19];
        $login_user=$row[20];
        $login_pass=$row[21];
        $login_campaign=$row[22];
        $park_on_extension=$row[23];
        $conf_on_extension=$row[24];
        $OSDiaL_park_on_extension=$row[25];
        $OSDiaL_park_on_filename=$row[26];
        $monitor_prefix=$row[27];
        $recording_exten=$row[28];
        $voicemail_exten=$row[29];
        $voicemail_dump_exten=$row[30];
        $ext_context=$row[31];
        $dtmf_send_extension=$row[32];
        $call_out_number_group=$row[33];
        $client_browser=$row[34];
        $install_directory=$row[35];
        $local_web_callerID_URL=$row[36];
        $OSDiaL_web_URL=$row[37];
        if (preg_match('/^$|dial_output.php$/i',$OSDiaL_web_URL)) $OSDiaL_web_URL = '/osdial/agent/webform_redirect.php';
        $AGI_call_logging_enabled=$row[38];
        $user_switching_enabled=$row[39];
        $conferencing_enabled=$row[40];
        $admin_hangup_enabled=$row[41];
        $admin_hijack_enabled=$row[42];
        $admin_monitor_enabled=$row[43];
        $call_parking_enabled=$row[44];
        $updater_check_enabled=$row[45];
        $AFLogging_enabled=$row[46];
        $QUEUE_ACTION_enabled=$row[47];
        $CallerID_popup_enabled=$row[48];
        $voicemail_button_enabled=$row[49];
        $enable_fast_refresh=$row[50];
        $fast_refresh_rate=$row[51];
        $enable_persistant_mysql=$row[52];
        $auto_dial_next_number=$row[53];
        $VDstop_rec_after_each_call=$row[54];
        $DBX_server=$row[55];
        $DBX_database=$row[56];
        $DBX_user=$row[57];
        $DBX_pass=$row[58];
        $DBX_port=$row[59];
        $phone_cid=$row[65];
        $enable_sipsak_messages=$row[66];
        $phone_cid_name=$row[67];

        if ($clientDST) $local_gmt = ($local_gmt + $isdst);
        if ($protocol == 'EXTERNAL') {
            $protocol = 'Local';
            $extension = "$dialplan_number$AT$ext_context";
        }
        $SIP_user = "$protocol/$extension";
        $SIP_user_DiaL = "$protocol/$extension";

        $stmt="SELECT asterisk_version from servers where server_ip='$server_ip';";
        if ($DB) echo "|$stmt|\n";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $asterisk_version=$row[0];

        # If a park extension is not set, use the default one
        if (strlen($park_ext)>0 and strlen($park_file_name)>0) {
            $OSDiaL_park_on_extension = "$park_ext";
            $OSDiaL_park_on_filename = "$park_file_name";
            print "<!-- CAMPAIGN CUSTOM PARKING:  |$OSDiaL_park_on_extension|$OSDiaL_park_on_filename| -->\n";
        }
        print "<!-- CAMPAIGN DEFAULT PARKING: |$OSDiaL_park_on_extension|$OSDiaL_park_on_filename| -->\n";

        # If a web form address is not set, use the default one
        if (strlen($web_form_address)>0) {
            $OSDiaL_web_form_address = $web_form_address;
        } elseif (strlen($OSDiaL_web_URL)>0) {
            $OSDiaL_web_form_address = $OSDiaL_web_URL;
        } else {
            $OSDiaL_web_form_address = '/osdial/agent/webform_redirect.php';
        }
        print "<!-- CAMPAIGN DEFAULT WEB FORM:  |$OSDiaL_web_form_address| -->\n";
        $OSDiaL_web_form_address_enc = rawurlencode($OSDiaL_web_form_address);

        # If web form 2 address is not set, use the default one
        if (strlen($web_form_address2)>0) {
            $OSDiaL_web_form_address2 = $web_form_address2;
        } elseif (strlen($OSDiaL_web_URL)>0) {
            $OSDiaL_web_form_address2 = $OSDiaL_web_URL;
        } else {
            $OSDiaL_web_form_address2 = '/osdial/agent/webform_redirect.php';
        }
        print "<!-- CAMPAIGN DEFAULT WEB FORM2:  |$OSDiaL_web_form_address2| -->\n";
        $OSDiaL_web_form_address2_enc = rawurlencode($OSDiaL_web_form_address2);

        # If closers are allowed on this campaign
        if ($allow_closers=="Y") {
            $OSDiaL_allow_closers = 1;
            print "<!-- CAMPAIGN ALLOWS CLOSERS:    |$OSDiaL_allow_closers| -->\n";
        } else {
            $OSDiaL_allow_closers = 0;
            print "<!-- CAMPAIGN ALLOWS NO CLOSERS: |$OSDiaL_allow_closers| -->\n";
        }


        $session_ext = preg_replace("/[^a-z0-9]/i", "", $extension);
        if (strlen($session_ext) > 10) {$session_ext = substr($session_ext, 0, 10);}
        $session_rand = (rand(1,9999999) + 10000000);
        $session_name = "$StarTtimE$US$session_ext$session_rand";

        if ($webform_sessionname) {
            $webform_sessionname = "&session_name=$session_name";
        } else {
            $webform_sessionname = '';
        }

        $stmt="DELETE FROM web_client_sessions WHERE start_time < '$past_month_date' AND extension='$extension' AND server_ip = '$server_ip' AND program = 'osdial';";
        if ($DB) echo "|$stmt|\n";
        $rslt=mysql_query($stmt, $link);

        $stmt="INSERT INTO web_client_sessions values('$extension','$server_ip','osdial','$NOW_TIME','$session_name');";
        if ($DB) echo "|$stmt|\n";
        $rslt=mysql_query($stmt, $link);

        if ($campaign_allow_inbound > 0 or $campaign_leads_to_call > 0 or $no_hopper_leads_logins == 'Y') {
            ### insert an entry into the user log for the login event
            $stmt = "INSERT INTO osdial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) VALUES('$VD_login','LOGIN','$VD_campaign','$NOW_TIME','$StarTtimE','$VU_user_group')";
            if ($DB) echo "|$stmt|\n";
            $rslt=mysql_query($stmt, $link);

            ##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
            $stmt="SELECT conf_exten FROM osdial_conferences WHERE extension='$SIP_user' AND server_ip='$server_ip' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $prev_conf_ct = mysql_num_rows($rslt);
            $got_conf = 0;
            if ($prev_conf_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $session_id =$row[0];

                echo "<!-- Using previous conference $session_id | $SIP_user | $server_ip -->\n";

                $stmt="UPDATE osdial_list set status='N', user='' where status IN('QUEUE','INCALL') and user ='$VD_login';";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                echo "<!-- old QUEUE and INCALL reverted list:   |$affected_rows| -->\n";

                $stmt="DELETE from osdial_hopper where status IN('QUEUE','INCALL','DONE') and user ='$VD_login';";
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                echo "<!-- old QUEUE and INCALL reverted hopper: |$affected_rows| -->\n";
            } else {
                # Lets get a new one...
                $stmt="SELECT count(*) FROM osdial_conferences WHERE server_ip='$server_ip' AND (extension='' OR extension is null);";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $row=mysql_fetch_row($rslt);
                $new_conf_ct = $row[0];
                if ($new_conf_ct > 0) {
                    $stmt="UPDATE osdial_conferences SET extension='$SIP_user' WHERE server_ip='$server_ip' AND (extension='' OR extension is null) LIMIT 1;";
                    $rslt=mysql_query($stmt, $link);

                    $stmt="SELECT conf_exten FROM osdial_conferences WHERE extension='$SIP_user' AND server_ip='$server_ip';";
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $session_id=$row[0];
                    $got_conf=1;
                    echo "<!-- Using new conference $session_id | $SIP_user | $server_ip -->\n";
                }
            }

            # User is logged in elsewhere
            $stmt="SELECT user,extension,server_ip,conf_exten FROM osdial_live_agents WHERE user='$VD_login' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $ola_user_ct = mysql_num_rows($rslt);

            if ($ola_user_ct) {
                $row=mysql_fetch_row($rslt);
                echo "<title>$t1 web client: $t1 Campaign Login</title>\n";
                echo "</head>\n";
                echo "<body bgcolor=white name=osdial>\n";
                echo $welcome_span;
                echo "<b>Sorry, your user account is logged into another station!</b><br>\n";
                echo "<i>Please see your manager and give him the following information:</i><br>\n";
                echo "<table border=1>\n";
                echo "  <tr>\n";
                echo "    <td>UserID</td>\n";
                echo "    <td>$row[0]</td>\n";
                echo "  </tr>\n";
                echo "  <tr>\n";
                echo "    <td>Extension</td>\n";
                echo "    <td>$row[1]</td>\n";
                echo "  </tr>\n";
                echo "  <tr>\n";
                echo "    <td>ServerID</td>\n";
                echo "    <td>$row[2]</td>\n";  
                echo "  </tr>\n";
                echo "  <tr>\n";
                echo "    <td>SessionID</td>\n";
                echo "    <td>$row[3]</td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "<hr>\n";
                echo "<form action=\"$PHP_SELF\" method=post>\n";
                echo "<input type=hidden name=DB value=\"$DB\">\n";
                echo "<input type=hidden name=phone_login value=\"$phone_login\">\n";
                echo "<input type=hidden name=phone_pass value=\"$phone_pass\">\n";
                echo "Login: <input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\">\n<br>";
                echo "Password: <input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"><br>\n";
                echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br>\n";
                echo "<input type=submit name=SUBMIT value=SUBMIT> &nbsp; \n";
                echo "<span id=\"LogiNReseT\"></span>\n";
                echo "</form>\n";
                echo "</body>\n";
                echo "</html>\n";
                exit;
            }


            $OSDiaL_is_logged_in=1;

            ### set the callerID for manager middleware-app to connect the phone to the user
            $SIqueryCID = "S$CIDdate$session_id";

            #############################################
            ##### START SYSTEM_SETTINGS LOOKUP #####
            $stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,osdial_agent_disable,allow_sipsak_messages FROM system_settings;";
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $qm_conf_ct = mysql_num_rows($rslt);
            $i=0;
            while ($i < $qm_conf_ct) {
                $row=mysql_fetch_row($rslt);
                $enable_queuemetrics_logging =    $row[0];
                $queuemetrics_server_ip    =        $row[1];
                $queuemetrics_dbname =            $row[2];
                $queuemetrics_login    =            $row[3];
                $queuemetrics_pass =            $row[4];
                $queuemetrics_log_id =            $row[5];
                $osdial_agent_disable =        $row[6];
                $allow_sipsak_messages =        $row[7];
                $i++;
            }
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################

            if ($enable_sipsak_messages > 0 and $allow_sipsak_messages > 0 and preg_match("/SIP/i",$protocol)) {
                $SIPSAK_prefix = 'LIN-';
                print "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
                passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$VD_campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
                $SIqueryCID = "$SIPSAK_prefix$VD_campaign$DS$CIDdate";
            }

            ### insert a NEW record to the osdial_manager table to be processed
            $stmt="INSERT INTO osdial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$SIqueryCID','Channel: $SIP_user_DiaL','Context: $ext_context','Exten: $session_id','Priority: 1','Callerid: \"OSDial#$SIP_user\" <$campaign_cid>','Account: $SIqueryCID','','','','');";
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            print "<!-- call placed to session_id: $session_id from phone: $SIP_user_DiaL -->\n";

            if ($auto_dial_level > 0) {
                print "<!-- campaign is set to auto_dial_level: $auto_dial_level -->\n";

                ##### grab the campaign_weight and number of calls today on that campaign for the agent
                $stmt="SELECT campaign_weight,calls_today FROM osdial_campaign_agents where user='$VD_login' and campaign_id = '$VD_campaign';";
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $vca_ct = mysql_num_rows($rslt);
                if ($vca_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $campaign_weight =    $row[0];
                    $calls_today =        $row[1];
                    $i++;
                } else {
                    $campaign_weight =    '0';
                    $calls_today =        '0';
                    $stmt="INSERT INTO osdial_campaign_agents (user,campaign_id,campaign_rank,campaign_weight,calls_today) values('$VD_login','$VD_campaign','0','0','$calls_today');";
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);
                    print "<!-- new osdial_campaign_agents record inserted: |$affected_rows| -->\n";
                }
                $closer_chooser_string='';
                $stmt="INSERT INTO osdial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,closer_campaigns,user_level,campaign_weight,calls_today) values('$VD_login','$server_ip','$session_id','$SIP_user','PAUSED','','$VD_campaign','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','$closer_chooser_string','$user_level','$campaign_weight','$calls_today');";
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                print "<!-- new osdial_live_agents record inserted: |$affected_rows| -->\n";

                if ($enable_queuemetrics_logging > 0) {
                    $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                    mysql_select_db("$queuemetrics_dbname", $linkB);

                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='AGENTLOGIN',data1='$VD_login@agents',serverid='$queuemetrics_log_id';";
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);
                    print "<!-- queue_log AGENTLOGIN entry added: $VD_login|$affected_rows -->\n";

                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);
                    print "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

                    mysql_close($linkB);
                    mysql_select_db("$VARDB_database", $link);
                }

                if ($campaign_allow_inbound > 0) print "<!-- CLOSER-type campaign -->\n";

            } else {
                print "<!-- campaign is set to manual dial: $auto_dial_level -->\n";

                $stmt="INSERT INTO osdial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,user_level) values('$VD_login','$server_ip','$session_id','$SIP_user','PAUSED','','$VD_campaign','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','$user_level');";
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                print "<!-- new osdial_live_agents record inserted: |$affected_rows| -->\n";

                if ($enable_queuemetrics_logging > 0) {
                    $linkB=mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                    mysql_select_db("$queuemetrics_dbname", $linkB);

                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='AGENTLOGIN',data1='$VD_login@agents',serverid='$queuemetrics_log_id';";
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);
                    print "<!-- queue_log AGENTLOGIN entry added: $VD_login|$affected_rows -->\n";

                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEALL',serverid='$queuemetrics_log_id';";
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);
                    print "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

                    mysql_close($linkB);
                    mysql_select_db("$VARDB_database", $link);
                }
            }

        } else {
            echo "<title>$t1 web client: $t1 Campaign Login</title>\n";
            echo "</head>\n";
            echo "<body bgcolor=white name=osdial>\n";
            echo $welcome_span;
            echo "<b>Sorry, there are no leads in the hopper for this campaign</b>\n";
            echo "<form action=\"$PHP_SELF\" method=post>\n";
            echo "<input type=hidden name=DB value=\"$DB\">\n";
            echo "<input type=hidden name=phone_login value=\"$phone_login\">\n";
            echo "<input type=hidden name=phone_pass value=\"$phone_pass\">\n";
            echo "Login: <input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\">\n<br>";
            echo "Password: <input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"><br>\n";
            echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br>\n";
            echo "<input type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=SUBMIT> &nbsp; \n";
            echo "<span id=\"LogiNReseT\"></span>\n";
            echo "</form>\n";
            echo "</body>\n";
            echo "</html>\n";
            exit;
        }

    if (strlen($session_id) < 1) {
        echo "<title>$t1 web client: $t1 Campaign Login</title>\n";
        echo "</head>\n";
        echo "<body bgcolor=white name=osdial>\n";
        echo $welcome_span;
        echo "<b>Sorry, there are no available sessions</b>\n";
        echo "<form action=\"$PHP_SELF\" method=post>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";
        echo "<input type=hidden name=phone_login value=\"$phone_login\">\n";
        echo "<input type=hidden name=phone_pass value=\"$phone_pass\">\n";
        echo "Login: <input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\">\n<br>";
        echo "Password: <input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"><br>\n";
        echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br>\n";
        echo "<input type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=SUBMIT> &nbsp; \n";
        echo "<span id=\"LogiNReseT\"></span>\n";
        echo "</form>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;
    }

    if (preg_match('/MSIE/',$browser)) {
        $useIE=1;
        print "<!-- client web browser used: MSIE |$browser|$useIE| -->\n";
    } else {
        $useIE=0;
        print "<!-- client web browser used: W3C-Compliant |$browser|$useIE| -->\n";
    }

    $StarTtimE = date("U");
    $NOW_TIME = date("Y-m-d H:i:s");
    ##### Agent is going to log in so insert the osdial_agent_log entry now
    $stmt="INSERT INTO osdial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$VD_login','$server_ip','$NOW_TIME','$VD_campaign','$StarTtimE','0','$StarTtimE','$VU_user_group','LOGIN');";
    if ($DB) echo "$stmt\n";
    $rslt=mysql_query($stmt, $link);
    $affected_rows = mysql_affected_rows($link);
    $agent_log_id = mysql_insert_id($link);
    print "<!-- osdial_agent_log record inserted: |$affected_rows|$agent_log_id| -->\n";

    $S='*';
    $D_s_ip = explode('.', $server_ip);
    if (strlen($D_s_ip[0])<2) $D_s_ip[0] = "0$D_s_ip[0]";
    if (strlen($D_s_ip[0])<3) $D_s_ip[0] = "0$D_s_ip[0]";
    if (strlen($D_s_ip[1])<2) $D_s_ip[1] = "0$D_s_ip[1]";
    if (strlen($D_s_ip[1])<3) $D_s_ip[1] = "0$D_s_ip[1]";
    if (strlen($D_s_ip[2])<2) $D_s_ip[2] = "0$D_s_ip[2]";
    if (strlen($D_s_ip[2])<3) $D_s_ip[2] = "0$D_s_ip[2]";
    if (strlen($D_s_ip[3])<2) $D_s_ip[3] = "0$D_s_ip[3]";
    if (strlen($D_s_ip[3])<3) $D_s_ip[3] = "0$D_s_ip[3]";
    $server_ip_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S";

    $scriptSQL = '';
    foreach ($myscripts as $k => $v) {
        $scriptSQL .= "'" . $k . "',";
    }
    $scriptSQL = rtrim($scriptSQL,',');
    ##### grab the datails of all active scripts in the system
    $stmt="SELECT script_id,script_name,script_text FROM osdial_scripts WHERE active='Y' AND script_id IN ($scriptSQL) order by script_id limit 100;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) echo "$stmt\n";
    $MM_scripts = mysql_num_rows($rslt);
    $e=0;
    while ($e < $MM_scripts) {
        $row=mysql_fetch_row($rslt);
        $MMscriptid[$e] =$row[0];
        $MMscriptname[$e] = urlencode($row[1]);

        $PMMscripttext = "<span style=\"display:block;\" id=\"SCRIPT_MAIN\">" . $row[2] . "</span>";

        $buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='" . $row[0] . "'");
        $hidebuttons = "document.getElementById('SCRIPT_MAIN').style.display='none';";

        if (is_array($buttons)) {
            foreach ($buttons as $button) {
                $hidebuttons .= "document.getElementById('SCRIPT_" . $button['script_button_id'] . "').style.display='none';";
            }

            foreach ($buttons as $button) {
                $PMMscripttext .= "<span style=\"display:none;\" id=\"SCRIPT_" . $button['script_button_id'] . "\">";
                $PMMscripttext .= "<center><input type=\"button\" value=\"MAIN\" onclick=\"ScriptButtonLog('" . $row[0] ."','" . $button['script_button_id'] . "'); script_last_click=''; $hidebuttons document.getElementById('SCRIPT_MAIN').style.display='block';\"></center><br>";
                $PMMscripttext .= $button['script_button_text'];
                $PMMscripttext .= "</span>";
            }

            foreach ($buttons as $button) {
                $PMMscripttext = preg_replace('/\{\{' . $button['script_button_id'] . '\}\}/imU', '{{' . $button['script_button_id'] . ':' . $button['script_button_label'] . '}}',$PMMscripttext);
                $PMMscripttext = preg_replace('/\{\{' . $button['script_button_id'] . ':(.*)\}\}/imU', '<input type="button" value="$1" onclick="ScriptButtonLog(\'' . $row[0] . '\' &#43; script_last_click,\'' . $button['script_button_id'] . '\'); script_last_click=\'_' . $button['script_button_id'] . '\'; ' . $hidebuttons . ' document.getElementById(\'SCRIPT_' . $button['script_button_id'] . '\').style.display=\'block\';">',$PMMscripttext);
            }
        }

        $PMMscripttext = preg_replace('/\{\{DISPO:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="document.getElementById(\'HotKeyDispo\').innerHTML=\'$1 - $2\';showDiv(\'HotKeyActionBox\');document.osdial_form.DispoSelection.value=\'$1\';CustomerData_update();HKdispo_display=3;HKfinish=1;dialedcall_send_hangup(\'NO\',\'YES\',\'\');">',$PMMscripttext);

        $MMscripttext[$e] = urlencode($PMMscripttext);
        $MMscriptids = "$MMscriptids'$MMscriptid[$e]',";
        $MMscriptnames = "$MMscriptnames'$MMscriptname[$e]',";
        $MMscripttexts = "$MMscripttexts'$MMscripttext[$e]',";
        $e++;
    }

    $MMscriptids = substr("$MMscriptids", 0, -1); 
    $MMscriptnames = substr("$MMscriptnames", 0, -1); 
    $MMscripttexts = substr("$MMscripttexts", 0, -1); 
    }
}



################################################################
### BEGIN - build the callback calendar (60 months)          ###
################################################################
$CBcal = generate_calendar('CB',60);

################################################################
### BEGIN - build the postdate calendar (12 months)          ###
################################################################
$PDcal = generate_calendar('PD',12);




$AFforms_js = '';
$AFnames = array();
$AFnames_js = '';
$AFids = array();
$AFids_js = '';
$AFoptions_js = '';
$AFlengths_js = '';
$forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
$cnt = 0;
foreach ($forms as $form) {
    foreach (split(',',$form['campaigns']) as $fcamp) {
        if ($fcamp == 'ALL' or strtoupper($fcamp) == strtoupper($VD_campaign)) {
            $AFforms_js .= "'" . $form['name'] . "',";
            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
            foreach ($fields as $field) {
                $AFnames[$cnt] = $form['name'] . '_' . $field['name'];
                $AFnames_js .= "'" . $form['name'] . '_' . $field['name'] . "',";
                $AFids[$cnt] = 'AF' . $field['id'];
                $AFids_js .= "'AF" . $field['id'] . "',";
                $AFoptions_js .= "'" . $field['options'] . "',";
                $AFlengths_js .= "'" . $field['length'] . "',";
                $cnt++;
            }
        }
    }
}
$AFforms_js = rtrim($AFforms_js,',');
$AFnames_js = rtrim($AFnames_js,',');
$AFids_js = rtrim($AFids_js,',');
$AFoptions_js = rtrim($AFoptions_js,',');
$AFlengths_js = rtrim($AFlengths_js,',');




echo "<script type=\"text/javascript\">\n";
echo "document.write('$wsc');\n";
echo "</script>\n";

load_status('Initializing global namespace...<br>&nbsp;<br>&nbsp;');
echo "<script type=\"text/javascript\">\n";
require('include/osdial-global-dynamic.js');
echo "var scriptnames=new Array();\n";
echo "var scripttexts=new Array();\n";
$h=0;
while ($MM_scripts > $h) {
    echo "scriptnames['$MMscriptid[$h]']=\"$MMscriptname[$h]\";\n";
    echo "scripttexts['$MMscriptid[$h]']=\"$MMscripttext[$h]\";\n";
    $h++;
}
echo "</script>\n";

echo "<script type=\"text/javascript\" src=\"include/osdial-global.js\"></script>\n";

load_status('Initializing static functions...<br>&nbsp;<br>&nbsp;');

echo "<script type=\"text/javascript\" src=\"include/osdial-static.js\"></script>\n";

echo "</head>\n";
flush();

?>

<!-- ===================================================================================================================== -->

<body onload="begin_all_refresh();"  onunload="BrowserCloseLogout();" name=osdial>
<?= $welcome_span ?>

<? load_status('Initializing GUI...<br>&nbsp;<br>&nbsp;'); ?>
        
<form name=osdial_form>

    <span style="position:absolute;left:0px;top:0px;z-index:2;" id="Header">
        <!-- Desktop --><!-- 1st line, login info -->
        <table cellpadding=0 cellspacing=0 bgcolor=white width=<?=$MNwidth?> border=0> 
            <tr valign=top align=left>
                <td colspan=3 valign=top align=center>
                    <input type=hidden name=extension>
                    <font class="body_text">
                    <? echo "<font color=" . $login_fc . ">&nbsp;&nbsp;Logged in as user <b>" . mclabel($VD_login) . "</b> on phone <b>" . mclabel($phone_login) . "</b> to campaign <b>" . mclabel($VD_campaign) . "</b>&nbsp;</font>\n"; ?>
                    </font>
                </td>
                <td colspan=3 valign=top align=right></td>
            </tr>
        </table>
    </span>


    <? load_status('Initializing GUI...<br>Tabs<br>&nbsp;'); ?>
    <!-- 2nd line -->
    <span style="position:absolute;left:0px;top:13px;z-index:1;" id="Tabs">
        <table width=<?=$MNwidth-10 ?> height=30 border=0> 
            <tr valign=top align=left>
                <td colspan=2>
                    <img id="FormButtons" onclick="ChooseForm();" src="templates/<?= $agent_template ?>/images/vdc_tab_buttons1.gif" border="0" width="223" height="30">
                </td>
                <td width=<?=$HSwidth ?> valign=middle align=center>
                    <font class="body_text" color=<?=$default_fc?>><b><span id=status>LIVE</span></b></font>
                </td>
                <td valign='middle' width=300>
                    <font class="body_text" color=#FFFFFF>Session ID: <span id=sessionIDspan></span></font>
                </td>
                <td valign='middle' width=400> 
                    &nbsp;<font class="body_tiny" color=<?=$default_fc?>><span id=AgentStatusCalls></span></font>
                </td>
                <td valign='middle'>
                    &nbsp;<a href="#" onclick="LogouT('NORMAL');return false;"><font size=1 color='red'>LOGOUT</font></a>&nbsp;
                </td>
                <td width=110>
                    <font class="body_text"><img src="templates/<?= $agent_template ?>/images/agc_live_call_OFF.gif" name=livecall alt="Live Call" width=109 height=30 border=0></font>
                </td>
            </tr>
        </table>
    </span>


    <!-- Debug -->
    <span style="position:absolute;left:970px;top:<?=$DBheight ?>px;z-index:16;" id="DebugLink">
        <font class="body_text"><a href="#" onclick="openDebugWindow();return false;">o</a></font>
    </span>
    

    <!-- Logout Link -->
    <span style="position:absolute;left:1px;top:1px;z-index:30;background-image: URL('templates/<?= $agent_template ?>/images/loginagain-bg.png');background-repeat:no-repeat;visibility:hidden;" id="LogouTBox">
        <table width=1001 height=608 cellpadding=0 cellspacing=0>
            <tr>
                <td align=center><br><span id="LogouTBoxLink">LOGOUT</span></td>
            </tr>
        </table>
    </span>
    
    
    <!-- Manual Dial Link -->
    <span style="position:absolute;left:300px;top:<?=$MBheight-20 ?>px;z-index:12;visibility:hidden;" id="ManuaLDiaLButtons">
        <font class="body_text">
            <span id="MDstatusSpan"><a href="#" onclick="NeWManuaLDiaLCalL('NO');return false;">MANUAL DIAL</a></span> &nbsp; &nbsp; &nbsp; <a href="#" onclick="NeWManuaLDiaLCalL('FAST');return false;">FAST DIAL</a><br>
        </font>
    </span>
        

    <!-- Call Back Link -->
    <span style="position:absolute;left:490px;top:<?=$CBheight-3 ?>px;z-index:13;visibility:hidden;" id="CallbacksButtons">
        <font class="body_text">
            <span id="CBstatusSpan">X ACTIVE CALLBACKS</span><br>
        </font>
    </span>
        

    <!-- Pause Code Link -->
    <span style="position:absolute;left:650px;top:<?=$CBheight-3 ?>px;z-index:14;visibility:hidden;" id="PauseCodeButtons">
        <font class="body_text">
            <span id="PauseCodeLinkSpan"><a href="#" onclick="PauseCodeSelectContent_create();return false;">ENTER A PAUSE CODE</a></span><br>
        </font>
    </span>


    <!-- Hot Key Button -->
    <? if ($HK_statuses_camp > 0 and ($user_level >= $HKuser_level or $VU_hotkeys_active > 0)) { ?>
        <span style="position:absolute;left:<?=$HKwidth+40 ?>px;top:<?=$HKheight +50 ?>px;z-index:16;" id="hotkeysdisplay">
            <a href="#" onMouseOver="HotKeys('ON')"><img src="templates/<?= $agent_template ?>/images/vdc_XB_hotkeysactive_OFF.gif" border=0 alt="HOT KEYS INACTIVE"></a>
        </span>
    <? } ?>


    <!-- D1, D2, Mute Links -->
    <span style="position:absolute;left:<?=$AMwidth-10 ?>px;top:<?=$AMheight+15 ?>px;z-index:22;" id="AgentMuteANDPreseTDiaL">
        <font class="body_text">
            <? if ($PreseT_DiaL_LinKs) {
                echo "<a href=\"#\" onclick=\"DtMf_PreSet_a_DiaL();return false;\"><font class=\"body_tiny\">D1 - DIAL</font></a><br>\n";
                echo "<a href=\"#\" onclick=\"DtMf_PreSet_b_DiaL();return false;\"><font class=\"body_tiny\">D2 - DIAL</font></a><br>\n";
                echo "<span id=\"DialBlindVMail2\"><img src=\"templates/$agent_template/images/vdc_XB_ammessage_OFF.gif\" border=0 alt=\"Blind Transfer VMail Message\"></span>\n";
            } else {
                echo "<br><br>\n";
            } ?>
            <br><br>
            <span id="AgentMuteSpan"></span>
        </font>
    </span>
    <span style="position:relative;left:480px;top:484px;z-index:22;" id="MutedWarning"></span>


    <!-- Preview Force-Dial Timout -->
    <font id="PreviewFDTimeSpan" style="font-size:35pt; font-weight: bold; color: <?=$forcedial_fc?>; position:absolute;left:325px;top:380px;z-index:22;"></font>
    

    <? load_status('Initializing GUI...<br>CallBacKsLisTBox<br>&nbsp;'); ?>
    <!-- Choose From Available Call Backs -->
    <span style="position:absolute;left:0px;top:18px;z-index:38;visibility:hidden;" id="CallBacKsLisTBox">
        <table border=1 bgcolor="<?=$callback_bg?>" width=<?=$CAwidth+13 ?> height=460>
            <tr>
                <td align=center valign=top>
                    Callbacks For Agent <?= $VD_login ?>
                    <br><br>
                    Click on a callback below to call the customer back now.<br>
                    (When you click on a record below to call it, it will be removed from the list.)<br>
                    <br>
                    <div class="scroll_callback" id="CallBacKsLisT"></div>
                    <br> &nbsp; 
                    <a href="#" onclick="CalLBacKsLisTCheck();return false;">Refresh</a>
                    &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
                    <a href="#" onclick="if (PCSpause==1) {AutoDial_ReSume_PauSe('VDADready');} hideDiv('CallBacKsLisTBox');return false;">Go Back</a>
                </td>
            </tr>
        </table>
    </span>

    

    <? load_status('Initializing GUI...<br>NeWManuaLDiaLBox<br>&nbsp;'); ?>
    <!-- Manual Dial -->
    <span style="position:absolute;left:0px;top:18px;z-index:39;visibility:hidden;" id="NeWManuaLDiaLBox">
        <table border=1 bgcolor="<?= $mandial_bg ?>" width=<?= $CAwidth-10 ?> height=545>
            <tr>
                <td align=center valign=top>
                    <br><b><font color=<?= $mandial_fc ?>>New Manual Dial Lead For </font><font color=<?=$mandial_bfc?>><?= $VD_login ?></font><font color=<?=$mandial_fc?>> In Campaign </font><font color=<?=$mandial_bfc?>><?= $VD_campaign ?></font></b>
                    <font color=<?= $mandial_fc ?>>
                        <br><br>Enter information below for the new lead you wish to call.<br>
                        <?  if (preg_match("/X/",dial_prefix)) {
                            echo "Note: a dial prefix of $dial_prefix will be added to the beginning of this number<br>\n";
                        } ?>
                        Note: all new manual dial leads will go into list <?= $manual_dial_list_id ?><br><br>
                    </font>
                    <table>
                        <tr>
                            <td align=right><font class="body_text"><font color=<?=$mandial_fc?>> Country Code: </font></font></td>
                            <td align=left><font class="body_text"><input type=text size=7 maxlength=10 name=MDDiaLCodE class="cust_form" value="1">&nbsp; <font color=<?=$mandial_fc?>>(This is usually a 1 in the USA-Canada)</font></font></td>
                        </tr>
                        <tr>
                            <td align=right><font class="body_text"><font color=<?=$mandial_fc?>> Phone Number: </font></font></td>
                            <td align=left><font class="body_text"><input type=text size=14 maxlength=12 name=MDPhonENumbeR class="cust_form" value="">&nbsp; <font color=<?=$mandial_fc?>>(12 digits max - digits only)</font></font></td>
                        </tr>
                        <tr>
                            <td align=right><font class="body_text"><font color=<?=$mandial_fc?>> Search Existing Leads: </font></font></td>
                            <td align=left><font class="body_text"><input type=checkbox name=LeadLookuP size=1 value="0">&nbsp; <font color=<?=$mandial_fc?>>(If checked will attempt to Find the phone number in the system before inserting it as a New Lead)</font></font></td>
                        </tr>
                        <tr>
                            <td align=center colspan=2>
                                <!-- Manual Dial Override has been disabled because it causes too much trouble -->
                                <span style="display:none;">
                                    <font class="body_text" color=<?=$mandial_fc?>>&nbsp;<br>
                                        If you want to dial a number and have it NOT be added as a new lead, enter in the exact dialstring that you want to call in the Dial Override field below. To hangup this call you will have to open the CALLS IN THIS SESSION link at the bottom of the screen and hang it up by clicking on its channel link there.<br>&nbsp;<br>
                                        Dial Override: <input type=text size=1 maxlength=1 name=MDDiaLOverridE class="cust_form" value="">(digits only please)
                                    </font>
                                </span>
                            </td>
                        </tr>
                    </table>
                
                    <br>
                    <b><a href="#" onclick="NeWManuaLDiaLCalLSubmiT();return false;">Dial Now</a>
                    &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
                    <a href="#" onclick="if (PCSpause==1) {AutoDial_ReSume_PauSe('VDADready');} hideDiv('NeWManuaLDiaLBox');return false;">Go Back</a></b>
                </td>
            </tr>
        </table>
    </span>
    
    
    <? load_status('Initializing GUI...<br>HotKeyEntriesBox<br>&nbsp;'); ?>
    <!-- Disposition Hot Keys Window -->
    <span style="position:absolute;left:92px;top:<?=$HTheight+45 ?>px;z-index:24;visibility:hidden;" id="HotKeyEntriesBox">
        <table frame=box bgcolor="<?=$hotkey_bg1?>" width=610 height=70>
            <tr bgcolor="<?=$hotkey_bg2?>">
                <td width=200><font class="sh_text"> Disposition Hot Keys: </font></td>
                <td colspan=2 width=410>
                    <font class="body_small">When active, simply press the keyboard key for the desired disposition for this call. The call will then be hungup and dispositioned automatically:</font>
                </td>
            </tr>
            <tr>
                <td width=200>
                    <font class="sk_text">
                        <span id="HotKeyBoxA"><?= $HKboxA ?></span>
                    </font>
                </td>
                <td width=200>
                    <font class="sk_text">
                        <span id="HotKeyBoxB"><?= $HKboxB ?></span>
                    </font>
                </td>
                <td>
                    <font class="sk_text">
                        <span id="HotKeyBoxC"><?= $HKboxC ?></span>
                    </font>
                </td>
            </tr>
        </table>
    </span>

    
    <? load_status('Initializing GUI...<br>VolumeControlSpan<br>&nbsp;'); ?>
    <!-- Volume Control Links -->
    <span style="position:absolute;left:935px;top:<?=$CBheight+26 ?>px;z-index:19;visibility:hidden;" id="VolumeControlSpan">
        <span id="VolumeUpSpan"><img src="templates/<?= $agent_template ?>/images/vdc_volume_up_off.gif" border=0></span>
        <br>
        <span id="VolumeDownSpan"><img src="templates/<?= $agent_template ?>/images/vdc_volume_down_off.gif" border=0></span>
    </span>

    
    <? load_status('Initializing GUI...<br>AgentStatusSpan<br>&nbsp;'); ?>
    <!-- Agent Status In Progress -->
    <span style="position:absolute;left:35px;top:<?=$CBheight ?>px;z-index:20;visibility:hidden;" id="AgentStatusSpan">
        <font class="body_text">
            Your Status: 
            <span id="AgentStatusStatus"></span> 
            <br>Calls Dialing: <span id="AgentStatusDiaLs"></span> 
        </font>
    </span>

    
    <? load_status('Initializing GUI...<br>TransferMain<br>&nbsp;'); ?>
    <!-- Transfer Link -->
    <span style="position:absolute;left:185px;top:<?=$HTheight ?>px;z-index:21;visibility:hidden;" id="TransferMain">
        <table bgcolor="<?=$xfer_bg1?>" frame=box width=<?=$XFwidth-255 ?>>
            <tr>
                <td align=left>
                    <div class="text_input" id="TransferMaindiv">
                        <font class="body_text">
                            <table width=100%>
                                <tr>
                                    <td align=center colspan=5><font color=<?=$xfer_fc?>><b>Transfer & Conference Functions</b><br></font></td>
                                </tr>
                                <tr>
                                    <td><span id="XfeRGrouPLisT"><select size=1 name=XfeRGrouP class="cust_form"><option>-- SELECT A GROUP TO SEND YOUR CALL TO --</option></select></span></td>
                                    <td align=center><span style="background-color: <?=$xfer_bg2?>" id="LocalCloser"><img src="templates/<?= $agent_template ?>/images/vdc_XB_localcloser_OFF.gif" border=0 alt="LOCAL CLOSER"></span></td>
                                    <td align=center><span style="background-color: <?=$xfer_bg2?>" id="HangupXferLine"><img src="templates/<?= $agent_template ?>/images/vdc_XB_hangupxferline_OFF.gif" border=0 alt="Hangup Xfer Line"></span></td>
                                    <td align=center><span style="background-color: <?=$xfer_bg2?>" id="HangupBothLines"><a href="#" onclick="bothcall_send_hangup();return false;"><img src="templates/<?= $agent_template ?>/images/vdc_XB_hangupbothlines.gif" border=0 alt="Hangup Both Lines"></a></span></td>
                                    <td align=center><a href="#" onclick="DtMf_PreSet_a();return false;"><font class="body_tiny">D1</font></a></td>
                                </tr>
                                <tr>
                                    <td><font size=1 color=<?=$xfer_fc?>>Number to call:&nbsp;<input type=text size=15 name=xfernumber maxlength=25 class="cust_form"><input type=hidden name=xferuniqueid></font></td>
                                    <td align=center><input type=checkbox name=xferoverride size=1 value="0"><font size=1 color=<?=$xfer_fc?>>Dial Override</font></td>
                                    <td align=center><span style="background-color: <?=$xfer_bg2?>" id="Leave3WayCall"><img src="templates/<?= $agent_template ?>/images/vdc_XB_leave3waycall_OFF.gif" border=0 alt="LEAVE 3-WAY CALL"></span></td>
                                    <td align=center><span style="background-color: <?=$xfer_bg2?>" id="DialBlindTransfer"><img src="templates/<?= $agent_template ?>/images/vdc_XB_blindtransfer_OFF.gif" border=0 alt="Dial Blind Transfer"></span></td>
                                    <td align=center><a href="#" onclick="DtMf_PreSet_b();return false;"><font class="body_tiny">D2</font></a></td>
                                </tr>
                                <tr>
                                    <td><font size=1 color=<?=$xfer_fc?>>Seconds:&nbsp;<input type=text size=2 name=xferlength maxlength=4 class="cust_form"></font></td>
                                    <td><font size=1 color=<?=$xfer_fc?>>Channel:&nbsp;<input type=text size=12 name=xferchannel maxlength=100 class="cust_form"></font></td>
                                    <td align=center><span style="background-color: <?=$xfer_bg2?>" id="DialWithCustomer"><a href="#" onclick="SendManualDial('YES');return false;"><img src="templates/<?= $agent_template ?>/images/vdc_XB_dialwithcustomer.gif" border=0 alt="Dial With Customer"></a></span></td>
                                    <td align=center><span style="background-color: <?=$xfer_bg2?>" id="ParkCustomerDial"><a href="#" onclick="xfer_park_dial();return false;"><img src="templates/<?= $agent_template ?>/images/vdc_XB_parkcustomerdial.gif" border=0 alt="Park Customer Dial"></a></span></td>
                                    <td align=center><span style="background-color: <?=$xfer_bg2?>" id="DialBlindVMail"><img src="templates/<?= $agent_template ?>/images/vdc_XB_ammessage_OFF.gif" border=0 alt="Blind Transfer VMail Message"></span></td>
                                </tr>
                            </table>
                        </font>
                    </div>
                </td>
            </tr>
        </table>
    </span>

    
    <? load_status('Initializing GUI...<br>HotKeyActionBox<br>&nbsp;'); ?>
    <!-- Dispositioned -->
    <span style="position:absolute;left:5px;top:<?=$HTheight+20 ?>px;z-index:23;visibility:hidden;" id="HotKeyActionBox">
        <table border=0 bgcolor="<?=$hotkey_done_bg1?>" width=<?=$HCwidth ?> height=70>
            <tr bgcolor="<?=$hotkey_done_bg1?>">
                <td height=70>
                    <font class="sh_text"> Lead Dispositioned As: </font>
                    <br><br>
                    <center><font class="sd_text"><span id="HotKeyDispo"> - </span></font></center>
                </td>
            </tr>
        </table>
    </span>

    
    <? load_status('Initializing GUI...<br>CBcommentsBox<br>&nbsp;'); ?>
    <!-- Previous Callback Info -->
    <span style="position:absolute;left:10px;top:<?=$HTheight+20 ?>px;z-index:25;visibility:hidden;" id="CBcommentsBox">
        <table frame=box bgcolor="<?=$cbinfo_bg1?>" width=<?=$HCwidth ?> height=70>
            <tr bgcolor="<?=$cbinfo_bg2?>">
                <td align=center><font class="sh_text" color=<?=$cbinfo_bfc?>>&nbsp;Previous Callback Information</font></td>
                <td align=right><font class="sk_text"> <a href="#" onclick="CBcommentsBoxhide();return false;"><b>CLOSE</b></a>&nbsp;&nbsp;</font></td>
            </tr>
            <tr>
                <td>
                    <font class="sk_text">
                        <span id="CBcommentsBoxA"></span><br>
                        <span id="CBcommentsBoxB"></span><br>
                        <span id="CBcommentsBoxC"></span><br>
                    </font>
                </td>
                <td width=320>
                    <font class="sk_text">
                        <span id="CBcommentsBoxD"></span>
                    </font>
                </td>
            </tr>
        </table>
    </span>

    
    <? load_status('Initializing GUI...<br>NoneInSessionBox<br>&nbsp;'); ?>
    <!-- Phone Is Hungup -->
    <span style="position:absolute;left:0px;top:18px;z-index:26;visibility:hidden;" id="NoneInSessionBox">
        <table border=1 bgcolor="<?=$noone_bg?>" width=<?=$CAwidth ?> height=545>
            <tr>
                <td align=center>
                    <b><font color=<?=$noone_fc?>>Your phone has either not been answered, or was hung up! <br><br><font color=<?=$noone_bg?>>(Session ID: <span id="NoneInSessionID"></span>)</font></font></b>
                    <br><br>
                    <a href="#" onclick="NoneInSessionCalL();return false;" style='text-decoration: blink;color:<?=$noone_fc?>;'><u><b>Try Calling Your Phone Here</b></u></a>
                    <br><br><br>
                    <a href="#" onclick="NoneInSessionOK();return false;" style='color:<?=$noone_fc2?>;'>Go Back</a>
                </td>
            </tr>
        </table>
    </span>
    

    <? load_status('Initializing GUI...<br>CustomerGoneBox<br>&nbsp;'); ?>
    <!-- Customer Hungup -->
    <span style="position:absolute;left:0px;top:0px;z-index:27;visibility:hidden;" id="CustomerGoneBox">
        <table border=1 bgcolor="<?=$custgone_bg?>" width=<?=$CAwidth ?> height=500>
            <tr>
                <td align=center>
                    Customer has hung up: <span id="CustomerGoneChanneL"></span><br>
                    <a href="#" onclick="CustomerGoneOK();return false;">Go Back</a>
                    <br><br>
                    <a href="#" onclick="CustomerGoneHangup();return false;">Finish and Disposition Call</a>
                </td>
            </tr>
        </table>
    </span>
    

    <? load_status('Initializing GUI...<br>WrapupBox<br>&nbsp;'); ?>
    <!-- Call Wrapup -->
    <span style="position:absolute;left:0px;top:0px;z-index:28;visibility:hidden;" id="WrapupBox">
        <table border=1 bgcolor="<?=$wrapup_bg?>" width=<?= $CAwidth ?> height=550>
            <tr>
                <td align=center>
                    Call Wrapup: <span id="WrapupTimer"></span> seconds remaining in wrapup
                    <br><br>
                    <span id="WrapupMessage"><?=$wrapup_message ?></span>
                    <br><br>
                    <a href="#" onclick="WrapupFinish();return false;">Finish Wrapup and Move On</a>
                </td>
            </tr>
        </table>
    </span>

    
    <? load_status('Initializing GUI...<br>AgenTDisablEBoX<br>&nbsp;'); ?>
    <!-- Agent Disabled -->
    <span style="position:absolute;left:0px;top:0px;z-index:29;visibility:hidden;" id="AgenTDisablEBoX">
        <table class=acrossagent border=0 width=<?=$CAwidth ?> height=564>
            <tr>
                <td align=center>
                    <font color=<?=$login_fc?>>
                        Your login session has been disabled, you need to logout.
                        <br><br>
                        <a href="#" onclick="LogouT('DISABLED');return false;"><font style="text-decoration:blink;">LOGOUT</font></a>
                        <br><br>
                        <a href="#" onclick="hideDiv('AgenTDisablEBoX');return false;"><font color=grey>Go Back</font></a>
                    </font>
                </td>
            </tr>
        </table>
    </span>


    <? load_status('Initializing GUI...<br>SysteMDisablEBoX<br>&nbsp;'); ?>
    <!-- System Disabled -->
    <span style="position:absolute;left:0px;top:0px;z-index:29;visibility:hidden;" id="SysteMDisablEBoX">
        <table class=acrossagent border=0 width=<?=$CAwidth ?> height=564>
            <tr>
                <td align=center>
                    <font color=<?=$login_fc?>>
                        There is a time synchronization problem with your system, please tell your system administrator.<br>
                        <span id="SysteMDisablEInfo"></span>
                        <br><br>
                        <a href="#" onclick="hideDiv('SysteMDisablEBoX');return false;"><font color=grey>Go Back</font></a>
                    </font>
                </td>
            </tr>
        </table>
    </span>


    <? load_status('Initializing GUI...<br>SysteMAlerTBoX<br>&nbsp;'); ?>
    <!-- System Alert -->
    <span style="position:absolute;left:0px;top:300px;z-index:41;visibility:hidden;" id="SysteMAlerTBoX">
        <table class=acrossagent border=1 width=<?= $CAwidth ?> height=300 cellspacing=20>
            <tr>
                <td align=center bgcolor="<?= $system_alert_bg2 ?>">
                    <font color=<?=$login_fc?>>
                        <span id="SysteMAlerTInfo"></span>
                        <br><br><br>
                        <font size=2>
                            <a href="#" onclick="hideDiv('SysteMAlerTBoX');return false;"><font color=grey>[Dismiss (<span id="SysteMAlerTTimer"></span>)]</font></a>
                            &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <a href="#" onclick="osdalert_timer=-1;return false;"><font color=grey>[Suspend]</font></a>
                            &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <a href="#" onclick="document.getElementById('SysteMAlerTBoX').style.top='0px';"><font color=grey>[Up]</font></a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#" onclick="document.getElementById('SysteMAlerTBoX').style.top='300px';"><font color=grey>[Down]</font></a>
                        </font>
                    </font>
                </td>
            </tr>
        </table>
    </span>
    
    
    <? load_status('Initializing GUI...<br>DispoSelectBox<br>&nbsp;'); ?>
    <!-- Disposition Window -->
    <span style="position:absolute;left:0px;top:0px;z-index:34;visibility:hidden;" id="DispoSelectBox">
        <table border=1 bgcolor="<?=$dispo_bg?>"  width=<?= $CAwidth + 15 ?> height=550 class=acrossagent>
            <tr>
                <td align=center valign=top>
                    <font color=<?=$dispo_fc?>>
                        <br> DISPOSITION CALL: <span id="DispoSelectPhonE"></span> &nbsp; &nbsp; &nbsp; <span id="DispoSelectHAspan"><a href="#" onclick="DispoHanguPAgaiN()">Hangup Again</a></span> &nbsp; &nbsp; &nbsp; <span id="DispoSelectMaxMin"><a href="#" onclick="DispoMinimize()">minimize</a></span><br>
                        <br>
                        <table frame=border cellpadding=5 cellspacing=5 width=620>
                            <tr>
                                <td colspan=2 align=center>
                                    <font color=<?= $dispo_fc ?>><b>Call Dispositions</b></font>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2 align=center>
                                    <div style="height:320px;overflow-y:auto;">
                                        <span id="DispoSelectContent"> End-of-call Disposition Selection </span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <input type=hidden name=DispoSelection><br>
                        <input type=checkbox name=DispoSelectStop size=1 value="0">
                        PAUSE <? echo (($inbound_man>0)?"INBOUND CALLS":"AGENT DIALING") ?> <br>
                        <a href="#" onclick="DispoSelectContent_create('','ReSET');return false;">CLEAR FORM</a>
                        &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; 
                        <a href="#" onclick="DispoSelect_submit();return false;"><b>SUBMIT</b></a>
                        <? if ($submit_method < 1) {
                            echo "<br><br>\n";
                            echo "<a href=\"#\" onclick=\"WeBForMDispoSelect_submit();return false;\">WEB FORM SUBMIT</a>\n";
                        } ?>
                        <br> &nbsp;
                    </font>
                </td>
            </tr>
        </table>
    </span>
    <!-- Hide Disposition Button A -->
    <span style="position:absolute;left:0px;top:70px;z-index:31;visibility:hidden;" id="DispoButtonHideA">
        <table border=0 bgcolor="<?=$dispo_hide?>" width=165 height=22>
            <tr>
                <td align=center valign=top></td>
            </tr>
        </table>
    </span>
    <!-- Hide Disposition Button B -->
    <span style="position:absolute;left:0px;top:138px;z-index:32;visibility:hidden;" id="DispoButtonHideB">
        <table border=0 bgcolor="<?=$dispo_hide?>" width=165 height=250>
            <tr>
                <td align=center valign=top>&nbsp;</td>
            </tr>
        </table>
    </span>
    <!-- Hide Disposition Button C -->
    <span style="position:absolute;left:0px;top:18px;z-index:33;visibility:hidden;" id="DispoButtonHideC">
        <table border=0 bgcolor="<?=$dispo_hide?>" width=<?=$CAwidth ?> height=47>
            <tr>
                <td align=center valign=top>Any changes made to the customer information below at this time will not be comitted, You must change customer information before you Hangup the call.</td>
            </tr>
        </table>
    </span>

    
    <? load_status('Initializing GUI...<br>PauseCodeSelectBox<br>&nbsp;'); ?>
    <!-- Pause Code Window -->
    <span style="position:absolute;left:0px;top:18px;z-index:40;visibility:hidden;" id="PauseCodeSelectBox">
        <table class=acrossagent frame=box width=<?=$CAwidth -10 ?> height=500>
            <tr>
                <td align=center valign=top>
                    <font color=<?=$pause_fc?>>
                        <br><b>Select A Pause Code</b>
                        <br><br>
                        <span id="PauseCodeSelectContent"> Pause Code Selection </span>
                        <input type=hidden name=PauseCodeSelection>
                        <br><br> &nbsp; 
                    </font>
                </td>
            </tr>
        </table>
    </span>

    
    <? load_status('Initializing GUI...<br>CallBackSelectBox<br>&nbsp;'); ?>
    <!-- Callback Window -->
    <span style="position:absolute;left:0px;top:18px;z-index:35;visibility:hidden;" id="CallBackSelectBox">
        <table border=1 bgcolor="<?= $callback_bg3 ?>" width=<?= $CAwidth ?> height=480>
            <tr>
                <td align=center valign=top>
                    <font color=<?= $callback_fc ?>>
                        Select a CallBack Date :<span id="CallBackDatE"></span><br>
                        <input type=hidden name=CallBackDatESelectioN id="CallBackDatESelectioN">
                        <input type=hidden name=CallBackTimESelectioN id="CallBackTimESelectioN">
                        <span id="CallBackDatEPrinT">Select a Date Below</span> &nbsp;
                        <span id="CallBackTimEPrinT"></span> &nbsp; &nbsp;
                        Hour: 
                        <select size=1 name="CBT_hour" id="CBT_hour">
                            <option>01</option>
                            <option>02</option>
                            <option>03</option>
                            <option>04</option>
                            <option>05</option>
                            <option>06</option>
                            <option>07</option>
                            <option>08</option>
                            <option>09</option>
                            <option>10</option>
                            <option>11</option>
                            <option>12</option>
                        </select> &nbsp;
                        Minutes: 
                        <select size=1 name="CBT_minute" id="CBT_minute">
                            <option>00</option>
                            <option>05</option>
                            <option>10</option>
                            <option>15</option>
                            <option>20</option>
                            <option>25</option>
                            <option>30</option>
                            <option>35</option>
                            <option>40</option>
                            <option>45</option>
                            <option>50</option>
                            <option>55</option>
                        </select> &nbsp;
                
                        <select size=1 name="CBT_ampm" id="CBT_ampm">
                            <option>AM</option>
                            <option selected>PM</option>
                        </select> &nbsp;<br>
                        <? if ($agentonly_callbacks) {
                            echo "<input type=checkbox name=CallBackOnlyMe id=CallBackOnlyMe size=1 value=\"0\"> MY CALLBACK ONLY <br>";
                        } ?>
                        CB Comments: <input type=text name="CallBackCommenTsField" id="CallBackCommenTsField" size=50>
                        <br><br>
                        <a href="#" onclick="CallBackDatE_submit();return false;">SUBMIT</a>
                        <br><br>
                        <span id="CallBackDateContent"><?= $CBcal ?></span>
                        <br><br> &nbsp; 
                    </font>
                </td>
            </tr>
        </table>
    </span>


    <? load_status('Initializing GUI...<br>PostDateSelectBox<br>&nbsp;'); ?>
    <!-- PostDate Window -->
    <span style="position:absolute;left:0px;top:18px;z-index:35;visibility:hidden;" id="PostDateSelectBox">
        <table border=1 bgcolor="<?= $callback_bg3 ?>" width=<?= $CAwidth ?> height=480>
            <tr>
                <td align=center valign=top>
                    <font color=<?= $callback_fc ?>>
                        Select a Post-Date :<span id="PostDatE"></span><br>
                        <input type=hidden name=PostDatESelectioN id="PostDatESelectioN">
                        <span id="PostDatEPrinT">Select a Date Below</span> &nbsp;
                        <br>
                        <a href="#" onclick="PostDatE_submit();return false;">SUBMIT</a>
                        <br><br>
                        <span id="PostDateContent"><?= $PDcal ?></span>
                        <br><br> &nbsp; 
                    </font>
                </td>
            </tr>
        </table>
    </span>

    
    <? load_status('Initializing GUI...<br>CloserSelectBox<br>&nbsp;'); ?>
    <!-- Closer Inbound Group Window -->
    <span style="position:absolute;left:0px;top:0px;z-index:36;visibility:hidden;" id="CloserSelectBox">
        <table class=acrossagent border=0 width=<?=$CAwidth ?> height=565>
            <tr>
                <td align=center valign=top>
                    <br><font size=+1 color=<?=$closer_fc2?>><b>Closer Inbound Group Selection</b></font>
                    <br><br>
                    <span id="CloserSelectContent"> Closer Inbound Group Selection </span>
                    <input type=hidden name=CloserSelectList><br>
                    <input type=checkbox name=CloserSelectBlended size=1 value="0">&nbsp;<font color=<?=$closer_fc2?>>BLENDED CALLING (outbound activated)</font>
                    <br><br>
                    <a href="#" onclick="CloserSelectContent_create();return false;">RESET</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#" onclick="CloserSelect_submit();return false;"><b>SUBMIT</b></a>
                    <br><br><br><br> &nbsp; 
                </td>
            </tr>
        </table>
    </span>
    

    <? load_status('Initializing GUI...<br>NothingBox<br>&nbsp;'); ?>
    <!-- Preview hide -->
    <span style="position:absolute;left:0px;top:0px;z-index:37;visibility:hidden;" id="NothingBox">
        <button type=button name="inert_button"><img src="templates/<?= $agent_template ?>/images/blank.gif"></button>
        <span id="DiaLLeaDPrevieWHide">Channel</span>
        <span id="DiaLDiaLAltPhonEHide">Channel</span>
        <? if (!$agentonly_callbacks) {
            echo "<input type=checkbox name=CallBackOnlyMe size=1 value=\"0\"> MY CALLBACK ONLY <br>";
        } ?>
    </span>

    
    <? load_status('Initializing GUI...<br>ScriptPanel<br>&nbsp;'); ?>
    <!-- Script window -->
    <span style="position:absolute;left:190px;top:95px;z-index:17;width:<?= $SSwidth ?>;height:<?= $SSheight ?>;overflow-x:hidden;overflow-y:scroll;visibility:hidden;" id="ScriptPanel">
        <table border=0 bgcolor="<?= $script_bg ?>" width=<?= $SSwidth ?> height=<?= $SSheight ?>>
            <tr>
                <td align=left valign=top><font class="sb_text"><span class="scroll_script" id="ScriptContents"><?=$t1?> Script Will Show Here</span></font></td>
            </tr>
        </table>
    </span>
    

    <? load_status('Initializing GUI...<br>MaiNfooterspan<br>&nbsp;'); ?>
    <!-- Footer Links -->
    <span style="position:absolute;left:2px;top: 480px;z-index:15;" id="MaiNfooterspan">
        <table id="MaiNfooter" width=<?=$MNwidth+10 ?> class=bottom style="background-color:<?=$panel_bg?>;">
            <tr height=15>
                <td height=15>
                    <font face="Arial,Helvetica" size=1><?=$t1?> Agent version: <?= $version ?>&nbsp;&nbsp;Build: <?= $build ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;Server: <?= $server_ip ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font><br>
                    <font class="body_small"><span id="busycallsdisplay"><a href="#"  onclick="conf_channels_detail('SHOW');">Show conference call channel information</a><br><br>&nbsp;</span></font>
                </td>
                <td align=right height=0></td>
            </tr>
            <tr>
                <td colspan=3><span id="outboundcallsspan"></span></td></tr>
            <tr>
                <td colspan=3>
                    <font class="body_small">
                        <span id="debugbottomspan"></span>
                    </font>
                </td>
            </tr>
        </table>
    </span>
    
    
    <!-- =============================   Here is the main OSDIAL display panel  ============================= -->
    
    <? load_status('Initializing GUI...<br>MainPanel<br>&nbsp;'); ?>
    <span style="position:absolute;left:2px;top:46px;z-index:4;" id="MainPanel">
        <table class=acrossagent cellpadding=0 cellspacing=0>
            <tr>
                <td>


                    <? load_status('Initializing GUI...<br>MainPanel<br>MainTable'); ?>
                    <!-- Column widths 205 + 505 + 270 = 980 -->
                    <table id="MainTable" class=acrossagent style="background-color:<?=$panel_bg?>;" cellpadding=0 cellspacing=0>
                        <tr>
                            <td width=22 colspan=2 class=curve2 style="vertical-align:bottom;">
                                <img src="templates/<?= $agent_template ?>/images/AgentTopLeft.png" width=22 height=22 align=left>
                                <font class="body_text" color=<?= $status_fct ?>>&nbsp;&nbsp;STATUS:&nbsp;&nbsp;</font>
                                <font class="body_text" color=<?= $status_fc ?>><span id="MainStatuSSpan"></span></font>
                            </td>
                            <td width=22><img src="templates/<?= $agent_template ?>/images/AgentTopRight.png" width=22 height=22 align=right></td>
                        </tr>
                        <tr>
                            <td colspan=3><span id="busycallsdebug"></span></td>
                        </tr>
                        <tr>


                            <? load_status('Initializing GUI...<br>MainPanel<br>AgentActions'); ?>
                            <td width=205 height=330 align=left valign=top class=curve3>
                                <font class="body_text" style="">
                                    <center>
                                        <span style="" id="DiaLControl"><a href="#" onclick="ManualDialNext('','','','','');"><img src="templates/<?= $agent_template ?>/images/vdc_LB_dialnextnumber_OFF.gif" border=0 alt="Dial Next Number"></a></span><br>
                                        <span id="DiaLLeaDPrevieW"><font class="preview_text"><input type=checkbox name=LeadPreview id=LeadPreview size=1 value="0"><label for="LeadPreview"> LEAD PREVIEW</label><br></font></span>
                                        <span id="DiaLDiaLAltPhonE"><font class="preview_text"><input type=checkbox name=DiaLAltPhonE id=DiaLAltPhonE size=1 value="0"><label for="DaiLAltPhonE"> ALT PHONE DIAL</label><br></font></span>
                            
                                        <font color=<?=$form_fc?>>Recording File</font><br>
                                        <font class="body_tiny">&nbsp;<span id="RecorDingFilename"></span></font><br>
                                        <font color=<?=$form_fc?>>Recording ID:&nbsp;</font><font class="body_small">&nbsp;<span id="RecorDID"></span></font>
                                        <span id="RecorDControl"><a href="#" onclick="conf_send_recording('MonitorConf','<?=$session_id ?>','');return false;"><img src="templates/<?= $agent_template ?>/images/vdc_LB_startrecording.gif" border=0 alt="Start Recording"></a></span>
                                        
                                        <span id="SpacerSpanA"><img src="templates/<?= $agent_template ?>/images/blank.gif" style="width:145px;height:16px;border:0px;"></span><br>
                                        <span id="WebFormSpan"><img src="templates/<?= $agent_template ?>/images/vdc_LB_webform_OFF.gif" border=0 alt="Web Form"></span>
                                        <span id="SpacerSpanB"><img src="templates/<?= $agent_template ?>/images/blank.gif" style="width:145px;height:2px;border:0px;"></span><br>
                                        <span id="WebFormSpan2"><img src="templates/<?= $agent_template ?>/images/vdc_LB_webform2_OFF.gif" border=0 alt="Web Form"></span>
                                        <span id="SpacerSpanC"><img src="templates/<?= $agent_template ?>/images/blank.gif" style="width:145px;height:16px;border:0px;"></span><br>
                                        <span id="ParkControl"><img src="templates/<?= $agent_template ?>/images/vdc_LB_parkcall_OFF.gif" border=0 alt="Park Call"></span>
                                        <span id="SpacerSpanD"><img src="templates/<?= $agent_template ?>/images/blank.gif" style="width:145px;height:2px;border:0px;"></span><br>
                                        <span id="XferControl"><img src="templates/<?= $agent_template ?>/images/vdc_LB_transferconf_OFF.gif" border=0 alt="Transfer - Conference"></span>
                                        <span id="SpacerSpanE"><img src="templates/<?= $agent_template ?>/images/blank.gif" style="width:145px;height:16px;border:0px;"></span><br>
                                        <span id="HangupControl"><img src="templates/<?= $agent_template ?>/images/vdc_LB_hangupcustomer_OFF.gif" border=0 alt="Hangup Customer"></span>
                                        <span id="SpacerSpanF"><img src="templates/<?= $agent_template ?>/images/blank.gif" style="width:145px;height:16px;border:0px;"></span><br>
            
                                        <div class="text_input" id="DTMFDialPad" onMouseOver="DTMFKeys('ON');">
                                            <table cellspacing=1 cellpadding=1 border=0>
                                                <tr>
                                                    <td align=center><span id="DTMFDialPad1"><a href="#" alt="1"><img src="templates/<?= $agent_template ?>/images/dtmf_1_OFF.png" border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad2"><a href="#" alt="2 - ABC"><img src="templates/<?= $agent_template ?>/images/dtmf_2_OFF.png" border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad3"><a href="#" alt="3 - DEF"><img src="templates/<?= $agent_template ?>/images/dtmf_3_OFF.png" border=0></a></span></td>
                                                </tr>
                                                <tr>
                                                    <td align=center><span id="DTMFDialPad4"><a href="#" alt="4 - GHI"><img src="templates/<?= $agent_template ?>/images/dtmf_4_OFF.png" border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad5"><a href="#" alt="5 - JKL"><img src="templates/<?= $agent_template ?>/images/dtmf_5_OFF.png" border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad6"><a href="#" alt="6 - MNO"><img src="templates/<?= $agent_template ?>/images/dtmf_6_OFF.png" border=0></a></span></td>
                                                </tr>
                                                <tr>
                                                    <td align=center><span id="DTMFDialPad7"><a href="#" alt="7 - PQRS"><img src="templates/<?= $agent_template ?>/images/dtmf_7_OFF.png" border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad8"><a href="#" alt="8 - TUV"><img src="templates/<?= $agent_template ?>/images/dtmf_8_OFF.png" border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad9"><a href="#" alt="9 - WXYZ"><img src="templates/<?= $agent_template ?>/images/dtmf_9_OFF.png" border=0></a></span></td>
                                                </tr>
                                                <tr>
                                                    <td align=center><span id="DTMFDialPadStar"><a href="#" alt="*"><img src="templates/<?= $agent_template ?>/images/dtmf_star_OFF.png" border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad0"><a href="#" alt="0"><img src="templates/<?= $agent_template ?>/images/dtmf_0_OFF.png" border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPadHash"><a href="#" alt="#"><img src="templates/<?= $agent_template ?>/images/dtmf_hash_OFF.png" border=0></a></span></td>
                                                </tr>
                                            </table>
                                        </div>
                                        
                                        <div class="text_input" id="SendDTMFdiv">
                                            <span id="SendDTMF"><a href="#" onclick="SendConfDTMF('<?=$session_id ?>');return false;"><img src="templates/<?= $agent_template ?>/images/vdc_LB_senddtmf.gif" border=0 alt="Send DTMF" align=top></a> <input type=text size=6 name=conf_dtmf class="cust_form" value="" maxlength=50></span>
                                        </div>
                                        
                                        <span id="RepullControl"></span>
                                        <span id="SpacerSpanG"><img src="templates/<?= $agent_template ?>/images/blank.gif" style="width:145px;height:16px;border:0px;"></span><br>
                                    </center>
                                </font>
                            </td>


                            <? load_status('Initializing GUI...<br>MainPanel<br>CustomerInformation'); ?>
                            <td width=505 align=left valign=top>
                                <input type=hidden name=list_id value="">
                                <input type=hidden name=called_count value="">
                                <input type=hidden name=gmt_offset_now value="">
                                <input type=hidden name=country_code value="">
                                <input type=hidden name=uniqueid value="">
                                <input type=hidden name=callserverip value="">
                            
                                <!-- Customer Information -->
                                <div class="text_input" id="MainPanelCustInfo">
                                    <table cellpadding=0 cellspacing=2>
                                        <tr valign=top>
                                            <td align=center colspan=3>
                                                <table width=100% align=center border=0>
                                                    <tr valign=top>
                                                        <td width=40% align=right>
                                                            <font class="body_text" color=<?=$form_fc?>><label for=SecondS>CallDuration:&nbsp;</label></font>
                                                            <font class="body_input"><input type=text size=4 name=SecondS id=SecondS class="cust_form" value="" readonly></font>
                                                        </td>
                                                        <td width=10% align=center><font class="body_text" color=#ABCBD4><span id=callchannel style="font-size:5pt;overflow:hidden;"></span></font></td>
                                                        <td width=50% align=left>
                                                            <font class="body_text" color=<?=$form_fc?>><label for=custdatetime>Cust Time:&nbsp;</label></font>
                                                            <font class="body_input"><input type=text size=19 maxlength=22 name=custdatetime id=custdatetime class="cust_form" value="" readonly></font>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td colspan=2 align=center valign=top><font color=<?=$form_fc?>><b>Customer Information</b></font><span id="CusTInfOSpaN"></span></td>
                                        </tr>
                                        <tr>
                                            <td align=right><font class="body_text" color=<?=$form_fc?>><label for=lead_id>LeadID:&nbsp;</label></font></td>
                                            <td align=left colspan=2>
                                                <font class="body_input"><input type=text size=11 name=lead_id id=lead_id maxlength=11 class="cust_form" value="" readonly></font>
                                                <font class="body_text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
                                                <font class="body_text" color=<?=$form_fc?>><label for=source_id>SourceID:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=6 name=source_id id=source_id maxlength=6 class="cust_form" value="" readonly></font>
                                                <font class="body_text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
                                                <font class="body_text" color=<?=$form_fc?>><label for=external_key>ExternalKey:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=11 name=external_key id=external_key maxlength=100 class="cust_form" value="" readonly></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=right>
                                                <font class="body_text"><font color=<?=$form_fc?>><label for=title>Title:&nbsp;</label></font>
                                            </td>
                                            <td align=left colspan=2>
                                                <font class="body_input"><input type=text size=4 name=title id=title maxlength=4 class="cust_form" value=""></font>
                                                <font class="body_text" color=<?=$form_fc?>><label for=first_name>&nbsp;First:&nbsp;</label></font>
                                                <font class="body_input"> <input type=text size=14 name=first_name id=first_name maxlength=30 class="cust_form" value=""></font>
                                                <font class="body_text" color=<?=$form_fc?>><label for=middle_initial>&nbsp;MI:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=1 name=middle_initial id=middle_initial maxlength=1 class="cust_form" value=""></font>
                                                <font class="body_text" color=<?=$form_fc?>><label for=last_name>&nbsp;Last:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=15 name=last_name id=last_name maxlength=30 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                        <!-- Hooks for company field
                                        <tr>
                                            <td align=right><font class="body_text" color=<?=$form_fc?>><label for=company>Company:&nbsp;</label></font></td>
                                            <td align=left colspan=2><font class="body_input"><input type=text size=58 name=company id=company maxlength=100 class="cust_form" value=""></font></td>
                                        -->
                                        <tr>
                                            <td align=right><font class="body_text" color=<?=$form_fc?>><label for=address1>Address1:&nbsp;</label></font></td>
                                            <td align=left colspan=2><font class="body_input"><input type=text size=58 name=address1 id=address1 maxlength=100 class="cust_form" value=""></font></td>
                                        </tr>
                                        <tr>
                                            <td align=right><font class="body_text" color=<?=$form_fc?>><label for=address2>Address2:&nbsp;</label></font></td>
                                            <td align=left><font class="body_input"><input type=text size=22 name=address2 id=address2 maxlength=100 class="cust_form" value=""></font></td>
                                            <td align=right>
                                                <font class="body_text" color=<?=$form_fc?>><label for=address3>Address3&nbsp;/&nbsp;Phone3:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=15 name=address3 id=address3 maxlength=100 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=right><font class="body_text" color=<?=$form_fc?>><label for=city>City:&nbsp;</label></font></td>
                                            <td align=left><font class="body_input"><input type=text size=22 name=city id=city maxlength=50 class="cust_form" value=""></font></td>
                                            <td align=right> 
                                                <font class="body_text" color=<?=$form_fc?>><label for=state>State:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=2 name=state id=state maxlength=2 class="cust_form" value=""></font>
                                                <font class="body_text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
                                                <font class="body_text" color=<?=$form_fc?>><label for=postal_code>Zip:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=9 name=postal_code id=postal_code maxlength=10 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=right><font class="body_text" color=<?=$form_fc?>><label for=province>Province:&nbsp;</label></font></td>
                                            <td align=left>
                                                <font class="body_input">
                                                    <input type=text size=22 name=province id=province maxlength=50 class="cust_form" value="">
                                                </font>
                                            </td>
                                            <td align=right>
                                                <font class="body_text" color=<?=$form_fc?>><label for=email>Email:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=30 name=email id=email maxlength=70 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=right><font class="body_text" color=<?=$form_fc?>><label for=phone_number>Phone:&nbsp;</label></font></td>
                                            <td align=left colspan=2>
                                                <font class="body_input"><input type=text size=11 name=phone_number id=phone_number maxlength=12 class="cust_form" value=""></font>
                                                <font class="body_text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
                                                <font class="body_text" color=<?=$form_fc?>><label for=phone_code>PhoneCode:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=4 name=phone_code id=phone_code maxlength=10 class="cust_form" value=""></font>
                                                <font class="body_text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
                                                <font class="body_text" color=<?=$form_fc?>><label for=alt_phone>Phone2:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=12 name=alt_phone id=alt_phone maxlength=12 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=right valign=top><font class="body_text" color=<?=$form_fc?>><label for=comments>Comments:&nbsp;</label></font></td>
                                            <td align=left colspan=2>
                                                <font class="body_tiny">
                                                    <? if ($multi_line_comments) { ?>
                                                        <textarea name=comments id=comments rows=3 cols=56 class="cust_form" style="height:45px;"></textarea>
                                                    <? } else { ?>
                                                        <input type=text size=56 name=comments id=comments maxlength=255 class="cust_form" value="">
                                                    <? } ?>
                                                </font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=right><font class="body_text"><font color=<?=$form_fc?>><label for=date_of_birth>Birth&nbsp;Date:&nbsp;</label></font></td>
                                            <td align=left><font class="body_input"><input type=text size=12 name=date_of_birth id=date_of_birth maxlength=10 class="cust_form" value=""></font></td>
                                            <td align=right>
                                                <font class="body_text" color=<?=$form_fc?>><label for=gender>Gender:&nbsp;</label></font>
                                                <font class="body_input"><select name=gender id=gender class="cust_form"><option></option><option>M</option><option>F</option></select></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=right><font class="body_text"><font color=<?=$form_fc?>><label for=post_date>Post&nbsp;Date:&nbsp;</label></font></td>
                                            <td align=left><font class="body_input"><input type=text size=22 name=post_date id=post_date maxlength=20 class="cust_form" value=""></font></td>
                                            <td align=right>
                                                <font class="body_text" color=<?=$form_fc?>><label for=vendor_lead_code>Vendor&nbsp;Code:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=15 name=vendor_lead_code id=vendor_lead_code maxlength=20 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=right><font class="body_text" color=<?=$form_fc?>><label for=custom1>Custom1:&nbsp;</label></font></td>
                                            <td align=left><font class="body_input"><input type=text size=22 name=custom1 id=custom1 maxlength=100 class="cust_form" value=""></font></td>
                                            <td align=right>
                                                <font class="body_text" color=<?=$form_fc?>><label for=custom2>Custom2:&nbsp;</label></font>
                                                <font class="body_input"><input type=text size=22 name=custom2 id=custom2 maxlength=100 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>



                            <? load_status('Initializing GUI...<br>MainPanel<br>AdditionalFormFields'); ?>
                            <td width=270 align=center valign=top class=borderright>
                                <div class="AFHead">Additional Information</div>
                                <?
                                $cnt = 0;

                                if ($email_templates) {
                                    echo "  <div id=\"AddtlFormsEmailTemplates\" style=" . $cssvis . "position:absolute;left:710px;top:42px;z-index:6;height:325px;overflow-x:hidden;overflow-y:auto;border-width:1px;border-style:solid;border-color:$form_fc;border-top-color:#CDEEE3;border-left-color:#CDEEE3;>\n";
                                    echo "  <table width=265><tr><td><table align=center>\n";
                                    echo "      <tr>\n";
                                    echo "          <td colspan=3 align=center>\n";
                                    echo "              <font color=$form_fc class=body_text style=\"font-size:12px\"><b>Email Templates<br />Select Emails to Send After Call<b></font>\n";
                                    echo "          </td>\n";
                                    echo "      </tr>\n";

                                    $ets = explode(',',$email_templates);
                                    foreach ($ets as $eto) {
                                        $et = get_first_record($link, 'osdial_email_templates', '*', "et_id='" . $eto . "' AND active='Y'");
                                        echo "      <tr title=\"$desc\">\n";
                                        echo "        <td width=95 align=left colspan=2>\n";
                                        echo "          <div style=\"width:90px;overflow:hidden;white-space:nowrap;\">\n";
                                        echo "            <input type=checkbox style=\"font-size:10px;\" name=ETids id=ET" . $et['et_id'] . " value=" . $et['et_id'] . " class=cust_form>\n";
                                        echo "            <font color=$form_fc class=body_text style=\"font-size:10px;\"><label for=ET" . $et['et_id'] . ">&nbsp;" . $et['et_name'] . "</label></font>\n";
                                        echo "          </div>\n";
                                        echo "        </td>\n";
                                        echo "        <td><span style=\"font-size:9px;\">&nbsp;</span></td>\n";
                                        echo "      </tr>\n";
                                    }

                                    echo "  </table></td></tr></table>\n";
                                    echo "  </div>\n";
                                    $cnt++;
                                }

                                foreach ($forms as $form) {
                                    foreach (split(',',$form['campaigns']) as $fcamp) {
                                        if ($fcamp == 'ALL' or strtoupper($fcamp) == strtoupper($VD_campaign)) {
                                            if ($cnt > 0) {
                                                $cssvis = 'visibility:hidden;';
                                            }
                                            echo "  <div id=\"AddtlForms" . $form['name'] . "\" style=" . $cssvis . "position:absolute;left:710px;top:42px;z-index:6;height:325px;overflow-x:hidden;overflow-y:auto;border-width:1px;border-style:solid;border-color:$form_fc;border-top-color:#CDEEE3;border-left-color:#CDEEE3;>\n";
                                            echo "  <table width=265><tr><td><table align=center>\n";
                                            echo "      <tr>\n";
                                            echo "          <td colspan=3 align=center>\n";
                                            echo "              <font color=$form_fc class=body_text style=\"font-size:12px\"><b>" . $form['description'] . "<br />" . $form['description2'] . "&nbsp;<b></font>\n";
                                            echo "          </td>\n";
                                            echo "      </tr>\n";
                                            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
                                            foreach ($fields as $field) {
                                                $desc = preg_replace('/"/','',$field['description']);
                                                echo "      <tr title=\"$desc\">\n";
                                                echo "          <td width=95 align=right><div style=\"width:90px;overflow:hidden;white-space:nowrap;\"><font color=$form_fc class=body_text style=\"font-size:10px;\">" . $field['description'] . ":&nbsp;</font></div></td>\n";
                                                echo "          <td align=left>\n";
                                                if ($field['options'] == '') {
                                                    echo "          <input type=text style=\"font-size:10px;\" size=" . $field['length'] . " maxlength=255 name=AF" . $field['id'] . " id=AF" . $field['id'];
                                                    #echo "            onclick=\"alert(document.osdial_form.AF" . $field['id'] . ".clientWidth);\"";
                                                    echo "            onchange=\"var afv=this;";
                                                    echo "              var aflist=document.getElementsByName('" . $form['name'] . '_' . $field['name'] . "');";
                                                    echo "              for(var afli=0;afli<aflist.length;afli++){";
                                                    echo "                if(afv.value!=aflist[afli].value) aflist[afli].value=afv.value;";
                                                    echo "              };\"";
                                                    echo "            class=cust_form value=\"\">\n";
                                                } else {
                                                    echo "          <select style=\"font-size:10px;\" name=AF" . $field['id'] . " id=AF" . $field['id'];
                                                    echo "            onchange=\"var afv=this;";
                                                    echo "              var aflist=document.getElementsByName('" . $form['name'] . '_' . $field['name'] . "');";
                                                    echo "              for(var afli=0;afli<aflist.length;afli++){";
                                                    echo "                if(afv.value!=aflist[afli].value) aflist[afli].value=afv.value;";
                                                    echo "              };\"";
                                                    echo "          >\n";
                                                    $options = split(',',$field['options']);
                                                    foreach ($options as $opt) {
                                                        echo "              <option>" . $opt . "</option>\n";
                                                    }
                                                    echo "          </select>\n";
                                                }
                                                echo "          </td>\n";
                                                echo "          <td><span style=\"font-size:9px;\">&nbsp;</span></td>\n";
                                                echo "      </tr>\n";
                                            }
                                            echo "  </table></td></tr></table>\n";
                                            echo "  </div>\n";
                                            $cnt++;
                                        }
                                    }
                                }
                                if ($cnt==0) {
                                    echo "  <div id=\"AddtlFormsNONE\" style=position:absolute;left:710px;top:42px;z-index:6;height:325px;overflow-x:hidden;overflow-y:auto;border-width:1px;border-style:solid;border-color:$form_fc;border-top-color:#CDEEE3;border-left-color:#CDEEE3;>\n";
                                    echo "    <table width=265><tr><td><table align=center><tr><td>\n";
                                    echo "      <font color=$form_fc class=body_text style=\"font-size:12px\"><b>No Additional Fields Available<b></font>\n";
                                    echo "    </td></tr></table></td></tr></table>\n";
                                    echo "  </div>\n";
                                } ?>
                            </td>
                        </tr>
                        <tr class=border>
                            <td align=left colspan=3 height=<?=$BPheight ?>>&nbsp;</td>
                        </tr>
                        <tr class=border>
                            <td align=left colspan=3>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align=left colspan=3>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align=left colspan=3>&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td align=left valign=top>
                    <br>
                    <div id="AddtlFormTab" style="visibility:hidden;position:absolute;left:980px;top:22px;z-index:9;" onclick="AddtlFormOver();">
                        <img src="templates/<?= $agent_template ?>/images/agentsidetab_tab.png" width="10" height="46" border="0">
                    </div>
                    <div id="AddtlFormTabExpanded" style="visibility:hidden;position:absolute;left:840px;top:22px;z-index:9;">
                        <table width=140 cellspacing=0 cellpadding=0>
                            <tr background="templates/<?= $agent_template ?>/images/agentsidetab_top.png" height=15 onclick="AddtlFormSelect('Cancel');">
                                <td></td>
                            </tr>
                            <?
                            if ($email_templates) {
                                echo "  <tr id=AddtlFormButEmailTemplates style=\"background-image:url(templates/" . $agent_template . "/images/agentsidetab_extra.png);\" height=29 ";
                                echo "    onmouseover=\"AddtlFormButOver('EmailTemplates');\" onmouseout=\"AddtlFormButOut('EmailTemplates');\">\n";
                                echo "      <td align=center onclick=\"AddtlFormSelect('EmailTemplates');\">\n";
                                echo "          <div class=AFMenu>EmailTemplates</div>\n";
                                echo "      </td>\n";
                                echo "  </tr>\n";
                            }
                            foreach ($forms as $form) {
                                foreach (split(',',$form['campaigns']) as $fcamp) {
                                    if ($fcamp == 'ALL' or strtoupper($fcamp) == strtoupper($VD_campaign)) {
                                        echo "  <tr id=AddtlFormBut" . $form['name'] . " style=\"background-image:url(templates/" . $agent_template . "/images/agentsidetab_extra.png);\" height=29 ";
                                        echo "    onmouseover=\"AddtlFormButOver('" . $form['name'] . "');\" onmouseout=\"AddtlFormButOut('" . $form['name'] . "');\">\n";
                                        echo "      <td align=center onclick=\"AddtlFormSelect('" . $form['name'] . "');\">\n";
                                        echo "          <div class=AFMenu>" . $form['name'] . "</div>\n";
                                        echo "      </td>\n";
                                        echo "  </tr>\n";
                                    }
                                }
                            } ?>
                            <tr id="AddtlFormButSelect4" background="templates/<?= $agent_template ?>/images/agentsidetab_line.png" height=1>
                                <td></td>
                            </tr>
                        </table>
                        <div style="position:absolute;left:140px;top:0px;z-index:9;">
                            <img src="templates/<?= $agent_template ?>/images/agentsidetab_cancel.png" width="10" height="46" border="0" onclick="AddtlFormSelect('Cancel');">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </span>
</form>
<!-- END *********   The end of the main OSDial display panel -->


<? load_status('Initializing GUI...<br>WebFormPanel1<br>&nbsp;'); ?>
<!-- Inline webform here -->
<span style="visibility:hidden; position:absolute;left:190px;top:92px;z-index:17;" name="WebFormPanel1" id="WebFormPanel1">
    <iframe src="/osdial/agent/blank.php" width="780" height="375" name="WebFormPF1" id="WebFormPF1" style="background-color: white;"></iframe>
</span>


<? load_status('Initializing GUI...<br>WebFormPanel2<br>&nbsp;'); ?>
<span style="visibility:hidden; position:absolute;left:190px;top:92px;z-index:18;" name="WebFormPanel2" id="WebFormPanel2">
    <iframe src="/osdial/agent/blank.php" width="780" height="375" name="WebFormPF2" id="WebFormPF2" style="background-color: white;"></iframe>
</span>


<?
flush();

load_status('Initializing dynamic functions...<br>&nbsp;<br>&nbsp;');
echo "<script type=\"text/javascript\">\n";
require('include/osdial-dynamic.js');
echo "initTextWidths()\n";
echo "emailTemplatesDisable(true);\n";
echo "</script>\n";


if (file_exists($WeBServeRRooT . '/agent/include/' . $VD_campaign . '_form_validation.js')) {
    load_status('Initializing customized validation functions...<br>&nbsp;<br>&nbsp;');
    echo "<script type=\"text/javascript\">\n";
    include($WeBServeRRooT . '/agent/include/' . $VD_campaign . '_form_validation.js');
    echo "</script>\n";
}

load_status('Complete...<br>&nbsp;<br>&nbsp;');
echo "</body>\n";
echo "</html>\n";

    
exit; 


?>
