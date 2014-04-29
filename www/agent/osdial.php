<?php
# 
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009-2013  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
# Copyright (C) 2009-2013  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
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


# The version/build variables get set to the SVN revision automatically in release package.
# Do not change.
$version = 'SVN_Version';
$build = 'SVN_Build';

header('Cache-Control: public, no-cache, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s', (time() - 60)).' GMT');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

require_once("dbconnect.php");
require_once('functions.php');

$DB=get_variable("DB");
$phone_login=get_variable("phone_login");
$phone_pass=get_variable("phone_pass");
$VD_login=get_variable("VD_login");
$VD_pass=get_variable("VD_pass");
$VD_campaign=get_variable("VD_campaign");
$relogin=get_variable("relogin");
$logout=get_variable("logout");
$admin_version=$config['settings']['version'];

if (empty($phone_login)) $phone_login=get_variable("pl");
if (empty($phone_pass)) $phone_pass=get_variable("pp");
if (!empty($VD_campaign)) $VD_campaign = OSDpreg_replace('/ /','',OSDstrtoupper($VD_campaign));

if (empty($flag_channels)) {
    $flag_channels=0;
    $flag_string='';
}

### security strip all non-alphanumeric characters out of the variables ###
$DB=OSDpreg_replace('[^0-9a-z]','',$DB);
#$phone_login=OSDpreg_replace('[^0-9a-zA-Z]','',$phone_login);
#$phone_pass=OSDpreg_replace('[^0-9a-zA-Z]','',$phone_pass);
#$VD_login=OSDpreg_replace('[^0-9a-zA-Z]','',$VD_login);
#$VD_pass=OSDpreg_replace('[^0-9a-zA-Z]','',$VD_pass);
#$VD_campaign=OSDpreg_replace('[^0-9a-zA-Z_]','',$VD_campaign);


$forever_stop=0;

if (isset($force_logout)) {
    echo "You have now logged out. Thank you\n";
    exit;
}

$timeout2mainJS='';
if ($logout>0 or $relogin=='YES') {
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie('ARI', '', time() - 60, $params["path"], $_SERVER["SERVER_NAME"], $params["secure"], $params["httponly"]);
        if (!empty($_COOKIE["BALANCEID"])) setcookie('BALANCEID', 'balancer.', 0, $params["path"], $_SERVER["SERVER_NAME"], $params["secure"], $params["httponly"]);
    }
    if ($logout>0) {
        # Return to home screen after waiting on return login screen for 5 minutes.
        $timeout2mainJS = "setTimeout(function() { window.location='".$config['settings']['admin_home_url']."'; },300000);";
    }
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

if (empty($config['settings']['agent_template'])) $config['settings']['agent_template']='default';
if (empty($config['settings']['enable_multicompany'])) $config['settings']['enable_multicompany']=0;

require("templates/default/display.php");
include("templates/" . $config['settings']['agent_template'] . "/display.php");


if (empty($config['settings']['default_phone_code'])) $config['settings']['default_phone_code']='1';
$default_phone_code = $config['settings']['default_phone_code'];
$use_php_self           = '1';  # Use relative script positioning instead of uri deconstruction.
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
$hangup_all_non_reserved= '1';  # set to 1 to force hangup all non-reserved channels upon Hangup Customer
$LogouTKicKAlL          = '1';  # set to 1 to hangup all calls in session upon agent logout
$conf_check_attempts    = '3';  # number of attempts to try before loosing webserver connection, for bad network setups

$TEST_all_statuses      = '0';  # TEST variable allows all statuses in dispo screen

$BROWSER_HEIGHT         = 500;  # set to the minimum browser height, default=500
$BROWSER_WIDTH          = 980;  # set to the minimum browser width, default=770

### SCREEN WIDTH AND HEIGHT CALCULATIONS ###

$MASTERwidth  = ($BROWSER_WIDTH - 340);
$MASTERheight = ($BROWSER_HEIGHT - 200);
if ($MASTERwidth < 430) $MASTERwidth = '430';
if ($MASTERheight < 300) $MASTERheight = '300';

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

$title1width=60;

$US='_';
$CL=':';
$AT='@';
$DS='-';
$date = date("r");
$PHP_SELF=$_SERVER['PHP_SELF'];
$ip = $_SERVER["REMOTE_ADDR"];
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
$browser = $_SERVER["HTTP_USER_AGENT"];
if ($use_php_self>0) {
    $agcPAGE=$PHP_SELF;
} else {
    $script_name = getenv("SCRIPT_NAME");
    $server_name = getenv("SERVER_NAME");
    $server_port = getenv("SERVER_PORT");
    if (OSDpreg_match('/443/',$server_port)) {
        $HTTPprotocol = 'https://';
    } else {
        $HTTPprotocol = 'http://';
    }
    if ($server_port == '80' or $server_port == '443' or !empty($_COOKIE["BALANCEID"])) {
        $server_port='';
    } else {
        $server_port = ":" . $server_port;
    }
    $agcPAGE = $HTTPprotocol . $server_name . $server_port .$script_name;
}

$t1="OSDial"; if (OSDpreg_match('/^Sli/',$config['settings']['agent_template'])) $t1=$config['settings']['agent_template'];
$agcDIR = OSDpreg_replace('/osdial.php/','',$agcPAGE);

$VD_pause_codes_ct=0;
$VARpause_codes='';
$VARpause_code_names='';
$VARcid_areacodes='';
$VARcid_areacode_numbers='';
$VARcid_areacode_names='';
$INgrpCT=0;
$default_xfer_group_name='';
$MMscripttexts='';
$MMscriptnames='';
$MMscriptids='';
$VARxfergroupsnames='';
$HKxferextens='';
$HKstatusnames='';
$HKstatuses='';
$HKhotkeys='';
$VARstatuses='';
$VARstatusnames='';
$VARstatusesEXT='';
$VARstatusesEXTJSON='';
$VARstatusnamesEXT='';
$VU_user_group='';
$agent_message='';

echo "<html>\n";
echo "<head>\n";
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
$wsc .= "<span id=WelcomeBoxTitle style=font-size:36px;><b>$t1</b></span>";
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


$company_id=0;
$company_prefix='';
if ($config['settings']['enable_multicompany'] > 0) {
    $company_prefix = OSDsubstr($phone_login,0,3);
    $company_id = ($company_prefix * 1) - 100;
    if (!empty($VD_login) and !empty($phone_login)) {
        if (OSDsubstr($VD_login,0,3) != OSDsubstr($phone_login,0,3)) {
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
        $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s' AND pass='%s';",mres($VD_login),mres($VD_pass));

        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $VU_user_group=$row[0];

        $stmt=sprintf("SELECT allowed_campaigns FROM osdial_user_groups WHERE user_group='%s';",mres($VU_user_group));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if (!OSDpreg_match("/ALL-CAMPAIGNS/",$row[0])) {
            $LOGallowed_campaignsSQL = mres(OSDpreg_replace('/ -/','',$row[0]));
            $LOGallowed_campaignsSQL = OSDpreg_replace('/ /',"','",$LOGallowed_campaignsSQL);
            $LOGallowed_campaignsSQL = "AND campaign_id IN('$LOGallowed_campaignsSQL')";
        }
    }

    if ($config['settings']['enable_multicompany']) $LOGallowed_campaignsSQL .= sprintf(" AND campaign_id LIKE '%s%%'",mres($company_prefix));

    $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE active='Y' %s ORDER BY campaign_id;",$LOGallowed_campaignsSQL);
    $rslt=mysql_query($stmt, $link);
    $camps_to_print = mysql_num_rows($rslt);

    $o=0;
    while ($camps_to_print > $o) {
        $rowx=mysql_fetch_row($rslt);
        $campname = '';
        if ($show_campname_pulldown) $campname = " - $rowx[1]";

        if (strtoupper($VD_campaign) == strtoupper($rowx[0])) {
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


//  Relogin
if ($relogin == 'YES') {
    echo "<title>$t1 web client: Re-Login</title>\n";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
    #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
    echo "<script type=\"text/javascript\">\n";
    echo "\n".$timeout2mainJS."\n";
    require('include/osdial-login.js');
    echo "</script>\n";
    echo "</head>\n";

    echo "<body bgcolor=white name=osdial>\n";
    echo $welcome_span;

    echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
    echo "<input type=hidden name=DB value=\"$DB\">\n";

        echo "<div class=containera><div class=acrosslogin2>\n";

    echo "<table width=500 cellpadding=0 cellspacing=0 border=0>\n";
    echo "  <tr>\n";
//     echo "    <td width=22><Ximg src=\"templates/" . $config['settings']['agent_template'] . "/images/AgentTopLeft2.png\" width=22 height=22 align=left></td>\n";
    echo "    <td class=across-top align=center colspan=4>&nbsp;</td>\n";
//     echo "    <td width=22><Ximg src=\"templates/" . $config['settings']['agent_template'] . "/images/AgentTopRightS.png\" width=22 height=22 align=right></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left>&nbsp;</td>\n";
    echo "    <td align=center colspan=2><font color=" . $login_fc . "><b>Agent Login</b></font></td>\n";
    echo "    <td align=left>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=right colspan=4>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Phone&nbsp;Login:&nbsp;</font></td>\n";
    echo "    <td align=left><input type=text name=phone_login size=10 maxlength=20 value=\"$phone_login\"></td>\n";
    echo "    <td align=left>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Phone&nbsp;Password:&nbsp;</font></td>\n";
    echo "    <td align=left><input type=password name=phone_pass size=10 maxlength=20 value=\"$phone_pass\"></td>\n";
    echo "    <td align=right>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">User&nbsp;Login:&nbsp;</font></td>\n";
    echo "    <td align=left><input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\"></td>\n";
    echo "    <td align=left>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">User&nbsp;Password:&nbsp;</font></td>\n";
    echo "    <td align=left><input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"></td>\n";
    echo "    <td align=left>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Campaign:&nbsp;</font></td>\n";
    echo "    <td align=left>$camp_form_code</td>\n";
    echo "    <td align=left>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr><td colspan=4>&nbsp;</td></tr>\n";
    echo "  <tr>\n";
    echo "    <td align=center colspan=4><input class=submit type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=Submit></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=right colspan=4><font size=1>&nbsp;Version: $version</font>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "</div></div>\n";
    echo "</form>\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;
}

if ($user_login_first == 1) {
    if (OSDstrlen($VD_login)<1 or OSDstrlen($VD_pass)<1 or OSDstrlen($VD_campaign)<1) {
        echo "<title>$t1 web client: Campaign Login</title>\n";
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
        #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
        echo "<script type=\"text/javascript\">\n";
        echo "\n".$timeout2mainJS."\n";
        require('include/osdial-login.js');
        echo "</script>\n";
        echo "</head>\n";
        echo "<body bgcolor=white name=osdial>\n";
        
        echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";
        
        echo "<div class=containera><div class=acrosslogin2>\n";
        echo "<table width=500 cellpadding=0 cellspacing=0 border=0>\n";
        echo "  <tr>\n";
        echo "    <td align=center colspan=4>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left>&nbsp;</td>\n";
        echo "    <td align=center colspan=2><font color=" . $login_fc . "><b>Agent Login</b></font></td>\n";
        echo "    <td align=left>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right colspan=4>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">User&nbsp;Login:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\"></td>\n";
        echo "    <td align=left>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">User&nbsp;Password:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"></td>\n";
        echo "    <td align=left>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">Campaign:&nbsp;</font></td>\n";
        echo "    <td align=left>$camp_form_code</td>\n";
        echo "    <td align=left>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr><td colspan=4>&nbsp;</td></tr>\n";
        echo "  <tr>\n";
        echo "     <td align=center colspan=4 class=submit><input type=submit name=SUBMIT value=Submit></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right colspan=4><font size=1>&nbsp;Version: $version</font>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div></div>\n";
        echo "</form>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;
    } else {
        if (OSDstrlen($phone_login)<2 or OSDstrlen($phone_pass)<2) {
            $stmt=sprintf("SELECT phone_login,phone_pass FROM osdial_users WHERE user='%s' AND pass='%s' AND user_level>0;",mres($VD_login),mres($VD_pass));
            if ($DB) echo "|$stmt|\n";
            
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $phone_login=$row[0];
            $phone_pass=$row[1];
    
            echo "<title>$t1 web client: Login</title>\n";
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
            #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
            echo "<script type=\"text/javascript\">\n";
            echo "\n".$timeout2mainJS."\n";
            require('include/osdial-login.js');
            echo "</script>\n";
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
            echo "      <input type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=SUBMIT><span class=refresh id=\"LogiNReseT\"></span>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr><td align=left colspan=2><font size=1><br>VERSION: $version</font></td></tr>\n";
            echo "</table>\n";
            echo "</form>\n";
            echo "</body>\n";
            echo "</html>\n";
            exit;
        }
    }
}

// Phone Login from welcome scren
if (OSDstrlen($phone_login)<2 or OSDstrlen($phone_pass)<2) {
    echo "<title>$t1 web client:  Phone Login</title>\n";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
    #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
    echo "<script type=\"text/javascript\">\n";
    echo "\n".$timeout2mainJS."\n";
    require('include/osdial-login.js');
    echo "</script>\n";
    echo "</head>\n";
    echo "<body bgcolor=white name=osdial>\n";
    
    echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
    echo "<input type=hidden name=DB value=\"$DB\">\n";
    
    echo "<div class=containera><div class=acrosslogin2>\n";
    echo "<table align=center width=500 cellpadding=0 cellspacing=0 border=0>\n";
    echo "  <tr>\n";
//     echo "    <td width='22'><Ximg src='templates/" . $config['settings']['agent_template'] . "/images/AgentTopLeft2.png' width='22' height='22' align='left'></td>\n";
    echo "    <td align='center' colspan=4>&nbsp;</td>\n";
//     echo "    <td width='22'><Ximg src='templates/" . $config['settings']['agent_template'] . "/images/AgentTopRightS.png' width='22' height='22' align='right'></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "    <td align=center colspan=2><font color=" . $login_fc . "><b>Login To Your Phone</font></td>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td colspan=4>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Phone Login:&nbsp;</font></td>\n";
    echo "    <td align=left><input type=text name=phone_login size=10 maxlength=20 value=\"\"></td>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "    <td align=right><font color=" . $login_fc . ">Phone Password:&nbsp;</font></td>\n";
    echo "    <td align=left><input type=password name=phone_pass size=10 maxlength=20 value=\"\"></td>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr><td colspan=4>&nbsp;</td></tr>\n";
    echo "  <tr>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "    <td align=center colspan=2><input class=submit type=submit name=SUBMIT value=Submit> &nbsp; <span id=\"LogiNReseT\"></span></td>\n";
    echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td align=right colspan=4><font size=1><br>&nbsp;Version: $version</font>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "</div></div>\n";
    echo "</form>\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;

} else {
    $VDloginDISPLAY=0;

    $company_name='';
    $company_status='';
    $company_acct_cutoff=0;
    if ($config['settings']['enable_multicompany'] > 0) {
        if ($company_id > 0) {
            $stmt=sprintf("SELECT name,status,acct_cutoff FROM osdial_companies WHERE id='%s';",mres($company_id));
            if ($DB) echo "|$stmt|\n";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $company_name=$row[0];
            $company_status=$row[1];
            $company_acct_cutoff=$row[2];
         }
    }

    if (OSDstrlen($VD_login)<2 or OSDstrlen($VD_pass)<2 or OSDstrlen($VD_campaign)<2) {
        $VDloginDISPLAY=1;
        if ($config['settings']['enable_multicompany'] > 0) {
            if ($company_status=='ACTIVE') {
                $VDdisplayMESSAGE = "&nbsp;";
            } elseif ($company_status=='INACTIVE') {
                $VDdisplayMESSAGE = "Your company profile is not yet active, please contact the system administrator.";
            } elseif ($company_status=='SUSPENDED') {
                $VDdisplayMESSAGE = "Your company profile has been suspended for insufficient funds,<br/>please add funds or contact the system administrator.";
            } elseif ($company_status=='TERMINATED') {
                $VDdisplayMESSAGE = "Yout company profile has been removed, please contact the system administrator.";
            }
        }
    } else {
        $stmt=sprintf("SELECT count(*) FROM osdial_users WHERE user='%s' AND pass='%s' AND user_level>0;",mres($VD_login),mres($VD_pass));
        if ($DB) echo "|$stmt|\n";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $auth=$row[0];

        if($auth>0) {
            if ($config['settings']['enable_multicompany'] > 0) {
                if ($company_id > 0) {
                    if ($company_status!='ACTIVE') $auth=0;
                } else {
                    $auth=0;
                }
            }
        }

        if($auth>0) {
            $myscripts = Array();

            $login=OSDstrtoupper($VD_login);
            $password=OSDstrtoupper($VD_pass);
            ##### grab the full name of the agent
            $stmt=sprintf("SELECT full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,osdial_recording,osdial_transfers,closer_default_blended,user_group,osdial_recording_override,manual_dial_allow_skip,xfer_agent2agent,script_override FROM osdial_users WHERE user='%s' AND pass='%s';",mres($VD_login),mres($VD_pass));
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
            if (!empty($script_override)) $myscripts[$script_override] = 1;

            debugLog('osdial_auth_entries',"vdweb|GOOD|$date|$VD_login|$VD_pass|$ip|$browser|$LOGfullname|");
            $user_abb = "$VD_login$VD_login$VD_login$VD_login";

            while (OSDstrlen($user_abb) > 4 and $forever_stop < 200) {
                $user_abb = OSDpreg_replace('/^./','',$user_abb);
                $forever_stop++;
            }

            $stmt=sprintf("SELECT allowed_campaigns,agent_message FROM osdial_user_groups WHERE user_group='%s';",mres($VU_user_group));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $LOGallowed_campaigns = $row[0];
            $agent_message=urlencode($row[1]);

            if (!OSDpreg_match("/ $VD_campaign /i",$LOGallowed_campaigns) and !OSDpreg_match("/ALL-CAMPAIGNS/",$LOGallowed_campaigns)) {
                echo "<title>$t1 web client: $t1 Campaign Login</title>\n";
                echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
                #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
                echo "<script type=\"text/javascript\">\n";
                echo "\n".$timeout2mainJS."\n";
                require('include/osdial-login.js');
                echo "</script>\n";
                echo "</head>\n";
                echo "<body bgcolor=white name=osdial>\n";
                echo $welcome_span;
                echo "<b>Sorry, you are not allowed to login to this campaign: $VD_campaign</b>\n";
                echo "<form name=osdial_form id=osdial_form action=\"$PHP_SELF\" method=post>\n";
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
            $stmt=sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_id='%s' AND active='Y';",mres($VD_campaign));
            if ($DB) echo "|$stmt|\n";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $CAMPactive=$row[0];
            if($CAMPactive>0) {
                if ($TEST_all_statuses > 0) {
                    $selectableSQL = '';
                } else {
                    $selectableSQL = "1=1 AND";
                }
                $DISPstatus = Array();
                $PSstatuses = Array();
                $statuses = Array();
                $status_names = Array();
                $statremove = "'NEW',";
                $TMPstatuses = Array();
                ##### grab the campaign-specific statuses that can be used for dispositioning by an agent
                $stmt=sprintf("SELECT status,status_name,IF(selectable='Y',1,0) FROM osdial_campaign_statuses WHERE %s status NOT IN(%s) AND campaign_id='%s' ORDER BY status;",$selectableSQL,rtrim($statremove,','),mres($VD_campaign));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $VD_statuses_camp = mysql_num_rows($rslt);
                $j=0;
                while ($j < $VD_statuses_camp) {
                    $row=mysql_fetch_row($rslt);
                    $DISPstatus[$row[0]] = $row[2];
                    if ($row[2] > 0) {
                        $TMPstatuses[$row[0]] = $row[1];
                        $PSstatuses[$row[0]] = $row[1];
                        $statremove .= sprintf("'%s',",mres($row[0]));
                    }
                    $j++;
                }
                $statremove = rtrim($statremove, ',');

                $TEstatuses = Array();
                $CEstatuses = Array();
                foreach ($TMPstatuses as $k=>$v) {
                    $stmt=sprintf("SELECT status,status_name,IF(selectable='Y',1,0),parents FROM osdial_campaign_statuses_extended WHERE campaign_id='%s' AND (parents='%s' OR parents LIKE '%s:%%') ORDER BY status;",mres($VD_campaign),mres($k),mres($k));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VD_statuses_camp_ext = mysql_num_rows($rslt);
                    $j=0;
                    while ($j < $VD_statuses_camp_ext) {
                        $row=mysql_fetch_row($rslt);
                        if ($row[2] > 0) {
                            $CEstatuses[$row[3].':'.$row[0]] = $row[1];
                            $TEstatuses[$row[3].':'.$row[0]] = $row[1];
                        }
                        $j++;
                    }
                }

                $TMPstatuses = Array();
                ##### grab the statuses that can be used for dispositioning by an agent
                $stmt=sprintf("SELECT status,status_name,IF(selectable='Y',1,0) FROM osdial_statuses WHERE %s status NOT IN(%s) ORDER BY status;",$selectableSQL,$statremove);
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $VD_statuses_ct = mysql_num_rows($rslt);
                $j=0;
                while ($j < $VD_statuses_ct) {
                    $row=mysql_fetch_row($rslt);
                    if ($row[2] > 0 and (!isset($DISPstatus[$row[0]]) or OSDpreg_match('/^$|^1$/',$DISPstatus[$row[0]]))) {
                        $PSstatuses[$row[0]] = $row[1];
                        $TMPstatuses[$row[0]] = $row[1];
                    }
                    $j++;
                }

                $SEstatuses = Array();
                foreach ($TMPstatuses as $k=>$v) {
                    $stmt=sprintf("SELECT status,status_name,IF(selectable='Y',1,0),parents FROM osdial_statuses_extended WHERE parents='%s' OR parents LIKE '%s:%%' ORDER BY status;",mres($k),mres($k));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VD_statuses_ext = mysql_num_rows($rslt);
                    $j=0;
                    while ($j < $VD_statuses_ext) {
                        $row=mysql_fetch_row($rslt);
                        if ($row[2] > 0) {
                            $SEstatuses[$row[3].':'.$row[0]] = $row[1];
                            $TEstatuses[$row[3].':'.$row[0]] = $row[1];
                        }
                        $j++;
                    }
                }

                ksort($PSstatuses);
                $VD_statuses_ct=0;
                foreach ($PSstatuses as $Pstatus => $Pstatusname) {
                        $statuses[$VD_statuses_ct]=$Pstatus;
                        $status_names[$VD_statuses_ct]=$Pstatusname;
                        $VARstatuses .= sprintf("'%s',",mres($Pstatus));
                        $VARstatusnames .= sprintf("'%s',",mres($Pstatusname));
                        $VD_statuses_ct++;
                }
                $VARstatuses = rtrim($VARstatuses, ','); 
                $VARstatusnames = rtrim($VARstatusnames, ','); 

                ksort($TEstatuses);
                foreach ($TEstatuses as $sk=>$nv) {
                        $VARstatusesEXT .= sprintf("'%s',",mres($sk));
                        $VARstatusnamesEXT .= sprintf("'%s',",mres($nv));
                }
                $VARstatusesEXT = rtrim($VARstatusesEXT, ','); 
                $VARstatusnamesEXT = rtrim($VARstatusnamesEXT, ','); 


                $thash = array();
                foreach ($TEstatuses as $sk=>$nv) {
                    $tmp = &$thash;
                    $ary = preg_split('/\:/',$sk);
                    $endary = end($ary);
                    foreach ($ary as $aa) {
                        if (array_key_exists($aa,$tmp)) {
                        } else {
                            if ($aa===$endary) {
                                $tmp[$aa] = array("name"=>$nv, "path"=>$sk);
                                $tmp['keys'][] = $aa;
                            } else {
                                $tmp[$aa] = array();
                            }
                        }
                        $tmp = &$tmp[$aa];
                    }
                    unset($tmp);
                }
                $thash['keys'] = array_keys($thash);
                $VARstatusesEXTJSON = json_encode($thash); 

                ##### grab the campaign-specific HotKey statuses that can be used for dispositioning by an agent
                $stmt=sprintf("SELECT count(*) FROM osdial_campaign_hotkeys WHERE selectable='Y' AND status!='NEW' AND campaign_id='%s' ORDER BY hotkey LIMIT 9;",mres($VD_campaign));
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $tothotkeys=$row[0];
                $maxHKacols=round(($tothotkeys/2),0,PHP_ROUND_HALF_UP);

                if ($DB) echo "$stmt\n";
                $stmt=sprintf("SELECT hotkey,status,status_name,xfer_exten FROM osdial_campaign_hotkeys WHERE selectable='Y' AND status!='NEW' AND campaign_id='%s' ORDER BY hotkey LIMIT 9;",mres($VD_campaign));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $HK_statuses_camp = mysql_num_rows($rslt);
                $w=0;
                $HKboxA='';
                $HKboxB='';
                while ($w < $HK_statuses_camp) {
                    $row=mysql_fetch_row($rslt);
                    $HKhotkey[$w] = $row[0];
                    $HKstatus[$w] = $row[1];
                    $HKstatus_name[$w] = $row[2];
                    $HKxfer_exten[$w] = $row[3];
                    $HKhotkeys .= sprintf("'%s',",mres($HKhotkey[$w]));
                    $HKstatuses .= sprintf("'%s',",mres($HKstatus[$w]));
                    $HKstatusnames .= sprintf("'%s',",mres($HKstatus_name[$w]));
                    $HKxferextens .= sprintf("'%s',",mres($HKxfer_exten[$w]));
                    if ($w < $maxHKacols) $HKboxA .= "&nbsp;&nbsp;<span class=hotkeyitem onclick=\"HotKeyDispo(" . $HKhotkey[$w] . ");return false;\"><span class=\"skb_text\">" . $HKhotkey[$w] . "</span><div style=\"width:55px;display:inline-block;overflow:hidden;white-space:nowrap;\">&nbsp;" . $HKstatus[$w] . "</div><span class=\"font1\" style=\"overflow:hidden;white-space:nowrap;\"> - " . $HKstatus_name[$w] . "</span></span><br/>";
                    if ($w >= $maxHKacols) $HKboxB .= "&nbsp;&nbsp;<span class=hotkeyitem onclick=\"HotKeyDispo(" . $HKhotkey[$w] . ");return false;\"><span class=\"skb_text\">" . $HKhotkey[$w] . "</span><div style=\"width:55px;display:inline-block;overflow:hidden;white-space:nowrap;\">&nbsp;" . $HKstatus[$w] . "</div><span class=\"font1\" style=\"overflow:hidden;white-space:nowrap;\"> - " . $HKstatus_name[$w] . "</span></span><br/>";
                    $w++;
                }
                $HKhotkeys = rtrim($HKhotkeys, ','); 
                $HKstatuses = rtrim($HKstatuses, ','); 
                $HKstatusnames = rtrim($HKstatusnames, ','); 
                $HKxferextens = rtrim($HKxferextens, ','); 

                ##### grab the statuses to be dialed for your campaign as well as other campaign settings
                $stmt="SELECT park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,";
                $stmt.="campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf,xferconf_a_number,";
                $stmt.="xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,";
                $stmt.="allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,";
                $stmt.="xfer_groups,web_form_address2,allow_tab_switch,preview_force_dial_time,manual_preview_default,web_form_extwindow,web_form2_extwindow,dial_method,";
                $stmt.="submit_method,use_custom2_callerid,campaign_cid_name,xfer_cid_mode,use_cid_areacode_map,carrier_id,email_templates,disable_manual_dial,";
                $stmt.="hide_xfer_local_closer,hide_xfer_dial_override,hide_xfer_hangup_xfer,hide_xfer_leave_3way,hide_xfer_dial_with,hide_xfer_hangup_both,";
                $stmt.="hide_xfer_blind_xfer,hide_xfer_park_dial,hide_xfer_blind_vmail,allow_md_hopperlist ";
                $stmt.=sprintf("FROM osdial_campaigns WHERE campaign_id='%s';",mres($VD_campaign));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $row=mysql_fetch_row($rslt);
                $park_ext =                $row[0];
                if (empty($park_ext)) $park_ext="8301";
                $park_file_name =            $row[1];
                if (empty($park_file_name)) $park_file_name="park";
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
                if (!empty($campaign_script)) $myscripts[$campaign_script] = 1;
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
                $disable_manual_dial =         $row[47];
                $hide_xfer_local_closer  = hide_element($row[48]);
                $hide_xfer_dial_override = hide_element($row[49]);
                $hide_xfer_hangup_xfer   = hide_element($row[50]);
                $hide_xfer_leave_3way    = hide_element($row[51]);
                $hide_xfer_dial_with     = hide_element($row[52]);
                $hide_xfer_hangup_both   = hide_element($row[53]);
                $hide_xfer_blind_xfer    = hide_element($row[54]);
                $hide_xfer_park_dial     = hide_element($row[55]);
                $hide_xfer_blind_vmail   = hide_element($row[56]);
                $allow_md_hopperlist   = $row[57];

                if ($disable_manual_dial=='Y') $agentcall_manual=0;
                $email_template_actions='';
                if ($email_templates) {
                    $ets = explode(',',$email_templates);
                    $email_templates='';
                    foreach ($ets as $eto) {
                        $et = get_first_record($link, 'osdial_email_templates', '*', sprintf("et_id='%s' AND active='Y'",mres($eto)) );
                        $email_templates.=sprintf("'%s',",mres($et['et_id']));
                        $email_template_actions.=sprintf("'%s',",mres($et['et_send_action']));
                    }
                    $email_templates = rtrim($email_templates,',');
                    $email_template_actions = rtrim($email_template_actions,',');
                }

                if (empty($previewFD_time)) $previewFD_time = "0";
                if ($use_custom2_callerid != "Y") $use_custom2_callerid = "N";

                if (!OSDpreg_match('/DISABLED/i',$VU_osdial_recording_override) and $VU_osdial_recording > 0) {
                    $campaign_recording = $VU_osdial_recording_override;
                    print "<!-- USER RECORDING OVERRIDE: |$VU_osdial_recording_override|$campaign_recording| -->\n";
                }
                if ($VC_scheduled_callbacks=='Y' and $VU_scheduled_callbacks=='1') { $scheduled_callbacks='1'; } else { $scheduled_callbacks='0'; }
                if ($alt_number_dialing=='Y') {$alt_phone_dialing='1';} else {$alt_phone_dialing='0';}
                if ($manual_preview_default=='Y') {$manual_preview_default='1';} else {$manual_preview_default='0';}
                if ($web_form_extwindow=='Y') {$web_form_extwindow='1';} else {$web_form_extwindow='0';}
                if ($web_form2_extwindow=='Y') {$web_form2_extwindow='1';} else {$web_form2_extwindow='0';}
                if ($campaign_allow_inbound=='Y') {$campaign_allow_inbound='1';} else {$campaign_allow_inbound='0';}
                if ($dial_method=='MANUAL' and $campaign_allow_inbound > 0) {$inbound_man=1;} else {$inbound_man=0;}

                if ($VU_osdial_recording=='0') $campaign_recording='NEVER';
                $closer_campaigns = mres(OSDpreg_replace("/^ | -$/","",$closer_campaigns));
                $closer_campaigns = OSDpreg_replace("/ /","','",$closer_campaigns);
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
                    $stmt=sprintf("SELECT name FROM osdial_carriers WHERE id='%s' AND active='Y' LIMIT 1;",mres($carrier_id));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $carriers = mysql_num_rows($rslt);
                    if ($carriers > 0) {
                        $row=mysql_fetch_row($rslt);
                        $dial_context = "OOUT" . $row[0];
                    }
                }

                if ($use_cid_areacode_map=='Y') {
                    $stmt=sprintf("SELECT areacode,cid_number,cid_name FROM osdial_campaign_cid_areacodes WHERE campaign_id='%s';",mres($VD_campaign));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VD_cid_areacodes = mysql_num_rows($rslt);
                    $j=0;
                    while ($j < $VD_cid_areacodes) {
                        $row=mysql_fetch_row($rslt);
                        $cid_areacodes[$j] = $row[0];
                        $cid_areacode_numbers[$j] = $row[1];
                        $cid_areacode_names[$j] = $row[2];
                        $VARcid_areacodes .= sprintf("'%s',",mres($row[0]));
                        $VARcid_areacode_numbers .= sprintf("'%s',",mres($row[1]));
                        $VARcid_areacode_names .= sprintf("'%s',",mres($row[2]));
                        $j++;
                    }
                    $VD_cid_areacodes_ct += $VD_cid_areacodes_ct;
                    $VARcid_areacodes = rtrim($VARcid_areacodes,',');
                    $VARcid_areacode_numbers = rtrim($VARcid_areacode_numbers,',');
                    $VARcid_areacode_names = rtrim($VARcid_areacode_names,',');
                }

                if ($agent_pause_codes_active=='Y') {
                    ##### grab the pause codes for this campaign
                    $stmt=sprintf("SELECT pause_code,pause_code_name FROM osdial_pause_codes WHERE campaign_id='%s' ORDER BY pause_code LIMIT 50;",mres($VD_campaign));
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $VD_pause_codes = mysql_num_rows($rslt);
                    $j=0;
                    while ($j < $VD_pause_codes) {
                        $row=mysql_fetch_row($rslt);
                        $pause_codes[$j] = $row[0];
                        $pause_code_names[$j] = $row[1];
                        $VARpause_codes .= sprintf("'%s',",mres($pause_codes[$j]));
                        $VARpause_code_names .= sprintf("'%s',",mres($pause_code_names[$j]));
                        $j++;
                    }
                    $VD_pause_codes_ct += $VD_pause_codes;
                    $VARpause_codes = rtrim($VARpause_codes, ',');
                    $VARpause_code_names = rtrim($VARpause_code_names, ',');
                }

                ##### grab all scripts for inbound groups.
                if (!empty($company_prefix)) $inSQL = sprintf("AND group_id LIKE '%s%%'",mres($company_prefix));
                $stmt=sprintf("SELECT group_id,ingroup_script FROM osdial_inbound_groups WHERE active='Y' %s ORDER BY group_id LIMIT 60;",$inSQL);
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $closer_ct = mysql_num_rows($rslt);
                $INgrpCT=0;
                while ($INgrpCT < $closer_ct) {
                    $row=mysql_fetch_row($rslt);
                    $ingroup_script = $row[1];
                    if (!empty($ingroup_script)) $myscripts[$ingroup_script] = 1;
                    $INgrpCT++;
                }

                ##### grab the inbound groups to choose from if campaign contains CLOSER
                $VARingroups="''";
                $INgrpCT=0;
                if ($campaign_allow_inbound > 0) {
                    $VARingroups='';
                    $closerSQL = sprintf("group_id IN(%s)",$closer_campaigns);
                    if ($LOGxfer_agent2agent > 0) $closerSQL = sprintf("(%s OR group_id='A2A_%s')",$closerSQL,mres($VD_login));
                    $stmt=sprintf("SELECT group_id,ingroup_script FROM osdial_inbound_groups WHERE active='Y' AND %s ORDER BY group_id LIMIT 60;",$closerSQL);
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $closer_ct = mysql_num_rows($rslt);
                    while ($INgrpCT < $closer_ct) {
                        $row=mysql_fetch_row($rslt);
                        $closer_groups[$INgrpCT] = $row[0];
                        $ingroup_script = $row[1];
                        if (!empty($ingroup_script)) $myscripts[$ingroup_script] = 1;
                        $VARingroups .= sprintf("'%s',",mres($closer_groups[$INgrpCT]));
                        $INgrpCT++;
                    }
                    $VARingroups = rtrim($VARingroups, ','); 
                }

                ##### grab the allowable inbound groups to choose from for transfer options
                $xfer_groups = mres(OSDpreg_replace("/^ | -$/","",$xfer_groups));
                $xfer_groups = OSDpreg_replace("/ /","','",$xfer_groups);
                $xfer_groups = "'$xfer_groups'";
                $VARxfergroups="''";
                if ($allow_closers == 'Y') {
                    $VARxfergroups='';
                    $xferSQL = sprintf("group_id IN(%s)",$xfer_groups);
                    if ($LOGxfer_agent2agent > 0) $xferSQL = sprintf("(%s OR group_id LIKE 'A2A_%s%%') AND group_id != 'A2A_%s'",$xferSQL,mres($company_prefix),mres($VD_login));
                    $stmt=sprintf("SELECT group_id,group_name FROM osdial_inbound_groups WHERE active='Y' AND %s ORDER BY group_id LIMIT 60;",$xferSQL);
                    $rslt=mysql_query($stmt, $link);
                    if ($DB) echo "$stmt\n";
                    $xfer_ct = mysql_num_rows($rslt);
                    $XFgrpCT=0;
                    while ($XFgrpCT < $xfer_ct) {
                        $row=mysql_fetch_row($rslt);
                        $VARxfergroups .= sprintf("'%s',",mres($row[0]));
                        $VARxfergroupsnames .= sprintf("'%s',",mres($row[1]));
                        if ($row[0] == $default_xfer_group) $default_xfer_group_name = $row[1];
                        $XFgrpCT++;
                    }
                    $VARxfergroups = rtrim($VARxfergroups, ','); 
                    $VARxfergroupsnames = rtrim($VARxfergroupsnames, ','); 
                }

                ##### grab the number of leads in the hopper for this campaign
                $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_hopper WHERE campaign_id='%s' AND status IN('API','READY');",mres($VD_campaign));
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
            debugLog('osdial_auth_entries',"vdweb|FAIL|$date|$VD_login|$VD_pass|$ip|$browser|");
            $VDloginDISPLAY=1;
            if ($config['settings']['enable_multicompany'] > 0) {
                if ($company_status=='ACTIVE') {
                    $VDdisplayMESSAGE = "Login incorrect, please try again";
                } elseif ($company_status=='INACTIVE') {
                    $VDdisplayMESSAGE = "Your company profile is not yet active, please contact the system administrator.";
                } elseif ($company_status=='SUSPENDED') {
                    $VDdisplayMESSAGE = "Your company profile has been suspended for insufficient funds,<br/>please add funds or contact the system administrator.";
                } elseif ($company_status=='TERMINATED') {
                    $VDdisplayMESSAGE = "Yout company profile has been removed, please contact the system administrator.";
                }
            } else {
                $VDdisplayMESSAGE = "Login incorrect, please try again.";
            }
        }
    }


    if ($VDloginDISPLAY) {
        echo "<title>$t1 web client: Campaign Login</title>\n";
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
        #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
        echo "<script type=\"text/javascript\">\n";
        echo "\n".$timeout2mainJS."\n";
        require('include/osdial-login.js');
        echo "</script>\n";
        echo "</head>\n";
        echo "<body bgcolor=white name=osdial>\n";
        echo $welcome_span;
    
        echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";
        echo "<input type=hidden name=phone_login value=\"$phone_login\">\n";
        echo "<input type=hidden name=phone_pass value=\"$phone_pass\">\n";
    
        echo "<div class=containera><div class=acrosslogin2>\n";
        echo "<table align=center width=500 cellpadding=0 cellspacing=0 border=0>\n";
        echo "  <tr>\n";
//         echo "    <td width=22><img src=\"templates/" . $config['settings']['agent_template'] . "/images/AgentTopLeft2.png\" width=22 height=22 align=left></td>\n";
        echo "    <td align=center colspan=4>&nbsp;</td>\n";
//         echo "    <td width=22><img src=\"templates/" . $config['settings']['agent_template'] . "/images/AgentTopRightS.png\" width=22 height=22 align=right></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left>&nbsp;&nbsp;</td>\n";
        echo "    <td align=center colspan=2><font color=" . $login_fc . "><b>Login To A Campaign</b></font></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left colspan=4><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">User Login:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\"></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <rt>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">User Password:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">Campaign:&nbsp;</font></td>\n";
        echo "    <td align=left><span id=\"LogiNCamPaigns\">$camp_form_code</span></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr><td colspan=4><center><font size=1 color=red>$VDdisplayMESSAGE</font></center></td></tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=center colspan=2><input class=submit type=button onclick=\"login_submit(); return false;\" name=SUBMIT value=Submit>&nbsp;<span id=\"LogiNReseT\"></span></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right colspan=4 class=rbborder><font size=1><br>&nbsp;Version: $version</font>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div></div>\n";
        echo "</form>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;
    }

    $authphone=0;
    $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM phones WHERE login='%s' AND pass='%s' AND active='Y';",mres($phone_login),mres($phone_pass));
    if ($DB) echo "|$stmt|\n";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $authphone=$row[0];
    if (!$authphone) {
        echo "<title>$t1 web client: Phone Login Error</title>\n";
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
        #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
        echo "<script type=\"text/javascript\">\n";
        echo "\n".$timeout2mainJS."\n";
        require('include/osdial-login.js');
        echo "</script>\n";
        echo "</head>\n";
        echo "<body bgcolor=white name=osdial>\n";
    
        echo "<form name=osdial_form id=osdial_form action=\"$agcPAGE\" method=post>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";
    
        echo "<div class=containera><div class=acrosslogin2>\n";
        echo "<table align=center width=500 cellpadding=0 cellspacing=0 border=0>\n";
        echo "  <tr>\n";
        echo "    <td align='center' colspan=4>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=center colspan=2><font color='red'>Invalid Login, please try again!</font></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td colspan=4>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">Phone Login:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=text name=phone_login size=10 maxlength=20 value=\"\"></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=right><font color=" . $login_fc . ">Phone Password:&nbsp;</font></td>\n";
        echo "    <td align=left><input type=password name=phone_pass size=10 maxlength=20 value=\"\"></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr><td colspan=4><center><font size=1 color=red>$VDdisplayMESSAGE</font></center></td></tr>\n";
        echo "  <tr>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "    <td align=center colspan=2><input class=submit type=submit name=SUBMIT value=Submit> &nbsp; <span id=\"LogiNReseT\"></span></td>\n";
        echo "    <td align=left><font size=1>&nbsp;</font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right colspan=4><font size=1><br>&nbsp;Version: $version</font>&nbsp;</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</div></div>\n";
        echo "</form>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;

    } else {
        echo "<title>$t1 web client</title>\n";
        $stmt=sprintf("SELECT * FROM phones WHERE login='%s' AND pass='%s' AND active='Y';",mres($phone_login),mres($phone_pass));
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
        if (empty($park_on_extension)) $park_on_extension="8301";
        $conf_on_extension=$row[24];
        if (empty($conf_on_extension)) $conf_on_extension="8302";
        $OSDiaL_park_on_extension=$row[25];
        if (empty($OSDiaL_park_on_extension)) $OSDiaL_park_on_extension="8301";
        $OSDiaL_park_on_filename=$row[26];
        if (empty($OSDiaL_park_on_filename)) $OSDiaL_park_on_filename="park";
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
        if (OSDpreg_match('/^$|dial_output.php$/i',$OSDiaL_web_URL)) $OSDiaL_web_URL = '/osdial/agent/webform_redirect.php';
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
        $voicemail_password=$row[68];
        $voicemail_email=$row[69];

        $phone_gmt = $local_gmt;
        if ($clientDST) $local_gmt = ($local_gmt + $isdst);
        if ($protocol == 'EXTERNAL' or (OSDpreg_match('/SIP|IAX/',$protocol) and OSDpreg_match('/^.*@.*$/',$extension))) {
            $protocol = 'Local';
            $extension = "$dialplan_number$AT$ext_context";
            $SIP_user_DiaL = "$protocol/$extension/n";
        } else {
            $SIP_user_DiaL = "$protocol/$extension";
        }
        $SIP_user = "$protocol/$extension";

        # If a park extension is not set, use the default one
        if (OSDstrlen($park_ext)>0 and OSDstrlen($park_file_name)>0) {
            $OSDiaL_park_on_extension = $park_ext;
            $OSDiaL_park_on_filename = $park_file_name;
            print "<!-- CAMPAIGN CUSTOM PARKING:  |$OSDiaL_park_on_extension|$OSDiaL_park_on_filename| -->\n";
        }
        print "<!-- CAMPAIGN DEFAULT PARKING: |$OSDiaL_park_on_extension|$OSDiaL_park_on_filename| -->\n";

        # If a web form address is not set, use the default one
        if (OSDstrlen($web_form_address)>0) {
            $OSDiaL_web_form_address = $web_form_address;
        } elseif (OSDstrlen($OSDiaL_web_URL)>0) {
            $OSDiaL_web_form_address = $OSDiaL_web_URL;
        } else {
            $OSDiaL_web_form_address = '/osdial/agent/webform_redirect.php';
        }
        print "<!-- CAMPAIGN DEFAULT WEB FORM:  |$OSDiaL_web_form_address| -->\n";
        $OSDiaL_web_form_address_enc = rawurlencode($OSDiaL_web_form_address);

        # If web form 2 address is not set, use the default one
        if (OSDstrlen($web_form_address2)>0) {
            $OSDiaL_web_form_address2 = $web_form_address2;
        } elseif (OSDstrlen($OSDiaL_web_URL)>0) {
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


        $session_ext = OSDpreg_replace("/[^a-z0-9]/i", "", $extension);
        if (OSDstrlen($session_ext) > 10) {$session_ext = OSDsubstr($session_ext, 0, 10);}
        $session_rand = (rand(1,9999999) + 10000000);
        $session_name = "$StarTtimE$US$session_ext$session_rand";

        if ($webform_sessionname) {
            $webform_sessionname = "&session_name=$session_name";
        } else {
            $webform_sessionname = '';
        }

        $stmt=sprintf("DELETE FROM web_client_sessions WHERE start_time<'%s' AND extension='%s' AND server_ip='%s' AND program='osdial';",mres($past_month_date),mres($extension),mres($server_ip));
        if ($DB) echo "|$stmt|\n";
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("INSERT INTO web_client_sessions VALUES('%s','%s','osdial','%s','%s');",mres($extension),mres($server_ip),mres($NOW_TIME),mres($session_name));
        if ($DB) echo "|$stmt|\n";
        $rslt=mysql_query($stmt, $link);

        if ($campaign_allow_inbound > 0 or $campaign_leads_to_call > 0 or $no_hopper_leads_logins == 'Y') {
            ### insert an entry into the user log for the login event
            $stmt=sprintf("INSERT INTO osdial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) VALUES('%s','LOGIN','%s','%s','%s','%s')",mres($VD_login),mres($VD_campaign),mres($NOW_TIME),mres($StarTtimE),mres($VU_user_group));
            if ($DB) echo "|$stmt|\n";
            $rslt=mysql_query($stmt, $link);

            ##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
            $stmt=sprintf("SELECT SQL_NO_CACHE conf_exten FROM osdial_conferences WHERE extension='%s' AND server_ip='%s' LIMIT 1;",mres($SIP_user),mres($server_ip));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $prev_conf_ct = mysql_num_rows($rslt);
            $got_conf = 0;
            if ($prev_conf_ct > 0) {
                $row=mysql_fetch_row($rslt);
                $session_id =$row[0];

                echo "<!-- Using previous conference $session_id | $SIP_user | $server_ip -->\n";

                $stmt=sprintf("UPDATE osdial_list SET status='N',user='' WHERE status IN('QUEUE','INCALL') AND user='%s';",mres($VD_login));
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                echo "<!-- old QUEUE and INCALL reverted list:   |$affected_rows| -->\n";

                $stmt=sprintf("DELETE FROM osdial_hopper WHERE status IN('QUEUE','INCALL','DONE') AND user='%s';",mres($VD_login));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                echo "<!-- old QUEUE and INCALL reverted hopper: |$affected_rows| -->\n";
            } else {
                # Lets get a new one...
                $stmt=sprintf("SELECT SQL_NO_CACHE count(*) FROM osdial_conferences WHERE server_ip='%s' AND (extension='' OR extension IS NULL);",mres($server_ip));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $row=mysql_fetch_row($rslt);
                $new_conf_ct = $row[0];
                if ($new_conf_ct > 0) {
                    $stmt=sprintf("UPDATE osdial_conferences SET extension='%s' WHERE server_ip='%s' AND (extension='' OR extension is null) LIMIT 1;",mres($SIP_user),mres($server_ip));
                    $rslt=mysql_query($stmt, $link);

                    $stmt=sprintf("SELECT SQL_NO_CACHE conf_exten FROM osdial_conferences WHERE extension='%s' AND server_ip='%s';",mres($SIP_user),mres($server_ip));
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $session_id=$row[0];
                    $got_conf=1;
                    echo "<!-- Using new conference $session_id | $SIP_user | $server_ip -->\n";
                }
            }

            # User is logged in elsewhere
            $stmt=sprintf("SELECT SQL_NO_CACHE user,extension,server_ip,conf_exten FROM osdial_live_agents WHERE user='%s' LIMIT 1;",mres($VD_login));
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $ola_user_ct = mysql_num_rows($rslt);

            if ($ola_user_ct) {
                $row=mysql_fetch_row($rslt);
                
                echo "<title>$t1 web client: $t1 Campaign Login</title>\n";
                echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
                #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
                echo "<script type=\"text/javascript\">\n";
                echo "\n".$timeout2mainJS."\n";
                require('include/osdial-login.js');
                echo "</script>\n";
                echo "</head>\n";
                echo "<body bgcolor=white name=osdial>\n";
                echo "<div align=center>";
                echo "<div align=left class=mainpage style='width:550;'><br/>";
                echo $welcome_span;
                echo "<div align=center><font color=white><b>LOGIN ERROR!</b><br/><br/>\n";
                echo "Please let your manager know your login is already in use</font></div><br>\n";
                echo "<table align=center border=0 width=275 cellpadding=2>\n";
                echo "  <tr>\n";
                echo "    <td>UserID:</td>\n";
                echo "    <td>$row[0]</td>\n";
                echo "  </tr>\n";
                echo "  <tr>\n";
                echo "    <td>Extension:</td>\n";
                echo "    <td>$row[1]</td>\n";
                echo "  </tr>\n";
                echo "  <tr>\n";
                echo "    <td>ServerID:</td>\n";
                echo "    <td>$row[2]</td>\n";  
                echo "  </tr>\n";
                echo "  <tr>\n";
                echo "    <td>SessionID:</td>\n";
                echo "    <td>$row[3]</td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "<hr>\n";
                echo "<div style='margin:10px 0 10px 10px;color:white;'>";
                echo "Once your manager have given the OK to continue try to login again</div>";
                echo "<div style='margin:10px 0 10px 10px;color:white;'>";
                echo "<form name=osdial_form id=osdial_form action=\"$PHP_SELF\" method=post>\n";
                echo "<input type=hidden name=DB value=\"$DB\">\n";
                echo "<input type=hidden name=phone_login value=\"$phone_login\">\n";
                echo "<input type=hidden name=phone_pass value=\"$phone_pass\">\n";
                echo "<div style='margin-left:100px;'>Login: <input style='margin-left:37px;' type=text name=VD_login size=10 maxlength=20 value=\"$VD_login\">\n<br>";
                echo "Password: <input type=password name=VD_pass size=10 maxlength=20 value=\"$VD_pass\"><br>\n";
                echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br></div>\n";
                echo "<div style='margin:5px 0 0 220px;'><input class=submit type=submit name=SUBMIT value=Login></div>\n";
                echo "<span id=\"LogiNReseT\"></span>\n";
                echo "</form>\n";
                echo "</div>&nbsp;";
                echo "</body>\n";
                echo "</html>\n";
                echo "</div></div>";
                exit;
            }


            $OSDiaL_is_logged_in=1;

            ### set the callerID for manager middleware-app to connect the phone to the user
            $SIqueryCID = "S$CIDdate$session_id";

            if ($enable_sipsak_messages > 0 and $config['settings']['allow_sipsak_messages'] > 0 and OSDpreg_match("/SIP/i",$protocol)) {
                $SIPSAK_prefix = 'LIN-';
                print "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
                passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$VD_campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
                $SIqueryCID = "$SIPSAK_prefix$VD_campaign$DS$CIDdate";
            }

            ### insert a NEW record to the osdial_manager table to be processed
            $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Originate','%s','Channel: %s','Context: %s','Exten: 2%s','Priority: 1','Callerid: \"OSDial#%s\" <%s>','Account: %s','','','','');",mres($NOW_TIME),mres($server_ip),mres($SIqueryCID),mres($SIP_user_DiaL),mres($ext_context),mres($session_id),mres($SIP_user),mres($campaign_cid),mres($SIqueryCID));
            if ($DB) echo "$stmt\n";
            $rslt=mysql_query($stmt, $link);
            $affected_rows = mysql_affected_rows($link);
            print "<!-- call placed to session_id: $session_id from phone: $SIP_user_DiaL -->\n";

            if ($auto_dial_level > 0) {
                print "<!-- campaign is set to auto_dial_level: $auto_dial_level -->\n";

                ##### grab the campaign_weight and number of calls today on that campaign for the agent
                $stmt=sprintf("SELECT campaign_weight,calls_today FROM osdial_campaign_agents WHERE user='%s' AND campaign_id='%s';",mres($VD_login),mres($VD_campaign));
                $rslt=mysql_query($stmt, $link);
                if ($DB) echo "$stmt\n";
                $vca_ct = mysql_num_rows($rslt);
                if ($vca_ct > 0) {
                    $row=mysql_fetch_row($rslt);
                    $campaign_weight = $row[0];
                    $calls_today = $row[1];
                    $i++;
                } else {
                    $campaign_weight = '0';
                    $calls_today = '0';
                    $stmt=sprintf("INSERT INTO osdial_campaign_agents (user,campaign_id,campaign_rank,campaign_weight,calls_today) VALUES('%s','%s','0','0','%s');",mres($VD_login),mres($VD_campaign),mres($calls_today));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $link);
                    $affected_rows = mysql_affected_rows($link);
                    print "<!-- new osdial_campaign_agents record inserted: |$affected_rows| -->\n";
                }
                $closer_chooser_string='';
                $stmt=sprintf("INSERT INTO osdial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,closer_campaigns,user_level,campaign_weight,calls_today) VALUES('%s','%s','%s','%s','PAUSED','','%s','','','','%s','%s','%s','%s','%s','%s','%s','%s');",mres($VD_login),mres($server_ip),mres($session_id),mres($SIP_user),mres($VD_campaign),mres($random),mres($NOW_TIME),mres($tsNOW_TIME),mres($NOW_TIME),mres($closer_chooser_string),mres($user_level),mres($campaign_weight),mres($calls_today));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                print "<!-- new osdial_live_agents record inserted: |$affected_rows| -->\n";

                if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                    $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                    mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                    if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                    $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='%s',agent='Agent/%s',verb='AGENTLOGIN',data1='%s@agents',serverid='%s';",mres($StarTtimE),mres($VD_campaign),mres($VD_login),mres($VD_login),mres($config['settings']['queuemetrics_log_id']));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);
                    print "<!-- queue_log AGENTLOGIN entry added: $VD_login|$affected_rows -->\n";

                    $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='NONE',agent='Agent/%s',verb='PAUSEALL',serverid='%s';",mres($StarTtimE),mres($VD_login),mres($config['settings']['queuemetrics_log_id']));
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

                $stmt=sprintf("INSERT INTO osdial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,user_level) VALUES('%s','%s','%s','%s','PAUSED','','%s','','','','%s','%s','%s','%s','%s');",mres($VD_login),mres($server_ip),mres($session_id),mres($SIP_user),mres($VD_campaign),mres($random),mres($NOW_TIME),mres($tsNOW_TIME),mres($NOW_TIME),mres($user_level));
                if ($DB) echo "$stmt\n";
                $rslt=mysql_query($stmt, $link);
                $affected_rows = mysql_affected_rows($link);
                print "<!-- new osdial_live_agents record inserted: |$affected_rows| -->\n";

                if ($config['settings']['enable_queuemetrics_logging'] > 0) {
                    $linkB=mysql_connect($config['settings']['queuemetrics_server_ip'], $config['settings']['queuemetrics_login'], $config['settings']['queuemetrics_pass']);
                    mysql_select_db($config['settings']['queuemetrics_dbname'], $linkB);
                    if ($config['settings']['use_non_latin'] > 0) $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$linkB);

                    $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='%s',agent='Agent/%s',verb='AGENTLOGIN',data1='%s@agents',serverid='%s';",mres($StarTtimE),mres($VD_campaign),mres($VD_login),mres($VD_login),mres($config['settings']['queuemetrics_log_id']));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);
                    print "<!-- queue_log AGENTLOGIN entry added: $VD_login|$affected_rows -->\n";

                    $stmt=sprintf("INSERT INTO queue_log SET partition='P001',time_id='%s',call_id='NONE',queue='NONE',agent='Agent/%s',verb='PAUSEALL',serverid='%s';",mres($StarTtimE),mres($VD_login),mres($config['settings']['queuemetrics_log_id']));
                    if ($DB) echo "$stmt\n";
                    $rslt=mysql_query($stmt, $linkB);
                    $affected_rows = mysql_affected_rows($linkB);
                    print "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

                    mysql_close($linkB);
                    mysql_select_db($VARDB_database, $link);
                }
            }

            osdevent($link,['event'=>'AGENT_LOGIN','server_ip'=>$server_ip,'campaign_id'=>$VD_campaign,'user'=>$VD_login,'data1'=>$SIP_user,'data2'=>$SIP_user_DiaL,'data3'=>$session_id]);
            osdevent($link,['event'=>'AGENT_PAUSE','server_ip'=>$server_ip,'campaign_id'=>$VD_campaign,'user'=>$VD_login]);

        } else {
            echo "<title>$t1 web client: $t1 Campaign Login</title>\n";
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
            #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
            echo "<script type=\"text/javascript\">\n";
            echo "\n".$timeout2mainJS."\n";
            require('include/osdial-login.js');
            echo "</script>\n";
            echo "</head>\n";
            echo "<body bgcolor=white name=osdial>\n";
            echo $welcome_span;
            echo "<b>Sorry, there are no leads in the hopper for this campaign</b>\n";
            echo "<form name=osdial_form id=osdial_form action=\"$PHP_SELF\" method=post>\n";
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

        if (OSDstrlen($session_id) < 1) {
            echo "<title>$t1 web client: $t1 Campaign Login</title>\n";
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";
            #echo "<script type=\"text/javascript\" src=\"include/osdial-login.js\"></script>\n";
            echo "<script type=\"text/javascript\">\n";
            echo "\n".$timeout2mainJS."\n";
            require('include/osdial-login.js');
            echo "</script>\n";
            echo "</head>\n";
            echo "<body bgcolor=white name=osdial>\n";
            echo $welcome_span;
            echo "<b>Sorry, there are no available sessions</b>\n";
            echo "<form name=osdial_form id=osdial_form action=\"$PHP_SELF\" method=post>\n";
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

        if (OSDpreg_match('/MSIE/',$browser)) {
            $useIE=1;
            print "<!-- client web browser used: MSIE |$browser|$useIE| -->\n";
        } else {
            $useIE=0;
            print "<!-- client web browser used: W3C-Compliant |$browser|$useIE| -->\n";
        }

        $StarTtimE = date("U");
        $NOW_TIME = date("Y-m-d H:i:s");
        ##### Agent is going to log in so insert the osdial_agent_log entry now
        $stmt=sprintf("INSERT INTO osdial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) VALUES('%s','%s','%s','%s','%s','0','%s','%s','LOGIN');",mres($VD_login),mres($server_ip),mres($NOW_TIME),mres($VD_campaign),mres($StarTtimE),mres($StarTtimE),mres($VU_user_group));
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $affected_rows = mysql_affected_rows($link);
        $agent_log_id = mysql_insert_id($link);
        print "<!-- osdial_agent_log record inserted: |$affected_rows|$agent_log_id| -->\n";

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
        $server_ip_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]";

        $scriptSQL = '';
        foreach ($myscripts as $k => $v) {
            $scriptSQL .= "'" . $k . "',";
        }
        $scriptSQL = rtrim($scriptSQL,',');
        $MM_scripts = 0;
        if (!empty($scriptSQL)) {
            ##### grab the datails of all active scripts in the system
            $stmt=sprintf("SELECT script_id,script_name,script_text FROM osdial_scripts WHERE active='Y' AND script_id IN (%s) ORDER BY script_id LIMIT 100;",$scriptSQL);
            $rslt=mysql_query($stmt, $link);
            if ($DB) echo "$stmt\n";
            $MM_scripts = mysql_num_rows($rslt);
        }
        $e=0;
        while ($e < $MM_scripts) {
            $row=mysql_fetch_row($rslt);
            $MMscriptid[$e] =$row[0];
            $MMscriptname[$e] = urlencode($row[1]);

            $PMMscripttext = "<span style=\"display:block;\" id=\"SCRIPT_MAIN\">" . $row[2] . "</span>";

            $buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', sprintf("script_id='%s'",mres($row[0])) );
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
                    $PMMscripttext = OSDpreg_replace('/\{\{' . $button['script_button_id'] . '\}\}/imU', '{{' . $button['script_button_id'] . ':' . $button['script_button_label'] . '}}',$PMMscripttext);
                    $PMMscripttext = OSDpreg_replace('/\{\{' . $button['script_button_id'] . ':(.*)\}\}/imU', '<input type="button" value="$1" onclick="ScriptButtonLog(\'' . $row[0] . '\' &#43; script_last_click,\'' . $button['script_button_id'] . '\'); script_last_click=\'_' . $button['script_button_id'] . '\'; ' . $hidebuttons . ' document.getElementById(\'SCRIPT_' . $button['script_button_id'] . '\').style.display=\'block\';">',$PMMscripttext);
                }
            }

            $PMMscripttext = OSDpreg_replace('/\{\{DISPO:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="document.getElementById(\'HotKeyDispo\').innerHTML=\'$1 - $2\';showDiv(\'HotKeyActionBox\');document.osdial_form.DispoSelection.value=\'$1\';CustomerData_update();HKdispo_display=3;HKfinish=1;dialedcall_send_hangup(\'NO\',\'YES\',\'\');">',$PMMscripttext);
            $PMMscripttext = OSDpreg_replace('/\{\{DISPO_NORMAL:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="document.getElementById(\'HotKeyDispo\').innerHTML=\'$1 - $2\';showDiv(\'HotKeyActionBox\');document.osdial_form.DispoSelection.value=\'$1\';CustomerData_update();submit_method_tmp=submit_method;submit_method=0;HKdispo_display=3;HKfinish=1;dialedcall_send_hangup(\'NO\',\'YES\',\'\');">',$PMMscripttext);
            $PMMscripttext = OSDpreg_replace('/\{\{DISPO_WEBFORM1:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="document.getElementById(\'HotKeyDispo\').innerHTML=\'$1 - $2\';showDiv(\'HotKeyActionBox\');document.osdial_form.DispoSelection.value=\'$1\';CustomerData_update();submit_method_tmp=submit_method;submit_method=1;HKdispo_display=3;HKfinish=1;dialedcall_send_hangup(\'NO\',\'YES\',\'\');">',$PMMscripttext);
            $PMMscripttext = OSDpreg_replace('/\{\{DISPO_WEBFORM2:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="document.getElementById(\'HotKeyDispo\').innerHTML=\'$1 - $2\';showDiv(\'HotKeyActionBox\');document.osdial_form.DispoSelection.value=\'$1\';CustomerData_update();submit_method_tmp=submit_method;submit_method=2;HKdispo_display=3;HKfinish=1;dialedcall_send_hangup(\'NO\',\'YES\',\'\');">',$PMMscripttext);

            $MMscripttext[$e] = urlencode($PMMscripttext);
            $MMscriptids .= "'$MMscriptid[$e]',";
            $MMscriptnames .= "'$MMscriptname[$e]',";
            $MMscripttexts .= "'$MMscripttext[$e]',";
            $e++;
        }

        $MMscriptids = rtrim($MMscriptids, ','); 
        $MMscriptnames = rtrim($MMscriptnames, ','); 
        $MMscripttexts = rtrim($MMscripttexts, ','); 
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
    foreach (OSDpreg_split('/,/',$form['campaigns']) as $fcamp) {
        if ($fcamp == '-ALL-' or OSDstrtoupper($fcamp) == OSDstrtoupper($VD_campaign)) {
            $AFforms_js .= "'" . $form['name'] . "',";
            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', sprintf("deleted='0' AND form_id='%s'",mres($form['id'])) );
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


#load_status('Initializing global namespace...<br>&nbsp;<br>&nbsp;');
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


echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $config['settings']['agent_template'] . "/styles.css\" media=\"screen\">\n";



#echo "<script type=\"text/javascript\" src=\"include/osdial-global.js\"></script>\n";
#load_status('Initializing static functions...<br>&nbsp;<br>&nbsp;');
#echo "<script type=\"text/javascript\" src=\"include/osdial-static.js\"></script>\n";

echo "<script type=\"text/javascript\">\n";
require('include/osdial-global.js');
echo "</script>\n";

echo "<script type=\"text/javascript\">\n";
require('include/osdial-static.js');
echo "</script>\n";

#echo "<script type=\"text/javascript\">\n";
#echo "document.write('$wsc');\n";
#echo "</script>\n";







echo "</head>\n";
flush();

?>

<!-- ===================================================================================================================== -->

<body onload="begin_all_refresh();"  onunload="BrowserCloseLogout();" name=osdial>
<?php echo $welcome_span; ?>

<?php load_status('Initializing GUI...<br>&nbsp;<br>&nbsp;'); ?>
        
<form name=osdial_form>

    <span style="position:absolute;left:0px;top:0px;z-index:2;" id="Header">
        <!-- Desktop --><!-- 1st line, login info -->
        <table cellpadding=0 cellspacing=0 bgcolor=white width=<?php echo $MNwidth; ?> border=0> 
            <tr valign=top align=left>
                <td colspan=3 valign=top align=center>
                    <input type=hidden name=extension>
                    <font class="body_text">
                    <?php echo "<font color=#698DBB>Logged in as user <font color=navy><b> " . mclabel($VD_login) . "</font> </b> on phone <font color=navy><b> " . mclabel($phone_login) . "</font> </b> to campaign <font color=navy><b> " . mclabel($VD_campaign) . "</b></font>\n"; ?>
                    <span id="DebugLink"><font color=black class="body_text"><a href="#" style="cursor:default;" onclick="openDebugWindow();return false;">.</a></font></span>
                    </font>
                </td>
                <td colspan=3 valign=top align=right></td>
            </tr>
        </table>
    </span>


    <?php load_status('Initializing GUI...<br>Tabs<br>&nbsp;'); ?>
    <!-- 2nd line -->
    <span style="position:absolute;left:0px;top:4px;z-index:1;margin-top:7px;" id="Tabs">
        <table width=<?php echo ($MNwidth-10); ?> height=30 border=0> 
            <tr valign=top align=left>
                <td colspan=2>
                    <!-- Placeholder for Panel buttons. See PanelSelect element. -->
                    <div style="width:200;height:30;">&nbsp;</div>
                    <!--img id="FormButtons" onclick="ChooseForm();" src="templates/<?php //echo $config['settings']['agent_template']; ?>/images/vdc_tab_buttons1.gif" border="0" width="223" height="30"-->
                </td>
                <td width=<?php echo $HSwidth; ?> valign=middle align=center>
<!--                     <font class="body_text" color=<?php echo $default_fc; ?>><b><span id=status>LIVE</span></b></font> -->
                </td>
                <td valign='middle' width=300>
<!--                     <font class="body_text" color=navy>Session ID: <span id=sessionIDspan></span></font> -->
                </td>
                <td valign='middle' width=400> 
<!--                     <font class="body_small" color=navy <?php echo $default_fc; ?>><span id=AgentStatusCalls></span></font> -->
                </td>
                <td valign='middle' width=65>
<!--                     &nbsp;<span class=logout><a href="#" onclick="LogouT('NORMAL');return false;"><font size=1 Xcolor='red'>LOGOUT</font></a></span>&nbsp; -->
                </td>
                <td width=110>
<!--                     <font class="body_text"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/agc_live_call_OFF.gif" name=livecall alt="Live Call" width=109 height=30 border=0></font> -->
                </td>
            </tr>
        </table>
        
        <div style="position:absolute;left:270px;top:13px;"><font class="body_text" color=white>Session ID: <span id=sessionIDspan></span></font></div>

        <div style="position:absolute;left:420px;top:11px;text-shadow: rgba(0,0,0,0.2) 1px 1px 1px;"><font class="body_text" color=navy><b><span id=status>LIVE</span></b></font></div>
    
        <div style="position:absolute;left:610px;top:11px;text-shadow: rgba(0,0,0,0.2) 1px 1px 1px;"><font Xclass="body_tiny" color=navy><span id=AgentStatusCalls></span></font></div>
    
        <div style="position:absolute;left:785px;top:11px;"><span class=logout><a href="#" onclick="LogouT('NORMAL');return false;"><font>LOGOUT</font></a></span></div>
    
        <div style="position:absolute;left:855px;top:2px;"><font class="body_text"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/agc_live_call_OFF.gif" name=livecall alt="Live Call" width=109 height=30 border=0></font></div>
<!--         <div class=livecall style="position:absolute;left:850px;top:2px;">NO LIVE CALL</div> -->
    
    </span>
    
    <span style="position:absolute;left:29px;top:18px;z-index:25;" id="PanelSelection">
      <div id="AgentPanelMAIN" style="padding:4px;position:relative;cursor:pointer;" class="AgentPanelSelect" onclick="ChoosePanel('MAIN');">
        FORM
      </div>
      <div id="AgentPanelSCRIPT" style="padding:4px;position:relative;left:-14px;cursor:pointer" class="AgentPanel" onclick="ChoosePanel('SCRIPT');">
        SCRIPT
      </div>
    </span>



    <!-- Logout Link -->
    <!--<span style="position:absolute;left:1px;top:1px;z-index:30;background-image: URL('templates/<?php echo $config['settings']['agent_template']; ?>/images/loginagain-bg.png');background-repeat:no-repeat;visibility:hidden;" id="LogouTBox">-->
    <?php $AdmVer=substr($admin_version,0,3); ?>
    <span style="position:absolute;top:0px;left:0px;z-index:100;background:white;visibility:hidden;" id="LogouTBox">
        <div bgcolor=white class=login_again2 style="left:1px;top:1px;">
            <!--<div style="position:absolute;left:417px;top:280px;" id="LogouTBoxLink"></div>-->
            <div style="position:absolute;left:345px;top:218px;width:300px;align:center;" id="LogouTBoxLink"></div>
            <div style="position:absolute;left:30px;top:495px;"><img class=homepagelogo src='templates/default/images/osdial-logo.gif' height=100></div>
            <div class=homepagever style="position:absolute;left:895px;top:550px;"><font style='font-size:17pt;'></font><?php echo $AdmVer; ?></div>
        </div>
    </span>
    
    
    <!-- Manual Dial Link -->
    <!--<span style="position:absolute;left:-58px;top:430px;z-index:12;visibility:hidden;" id="ManuaLDiaLButtons">-->
    <span style="position:relative;left:-60px;top:453px;z-index:12;visibility:hidden;" id="ManuaLDiaLButtons">
        <font class="body_text">
            <span id="MDstatusSpan"><span id="MDHopperListLink" style="position:relative;top:-17px;left:171px;<?php if ($allow_md_hopperlist!='Y') echo "visibility:hidden;"; ?>"><a href="#" onclick="MDHopperListCheck();return false;">HOPPER LIST</a></span> &nbsp; &nbsp; &nbsp; <a href="#" onclick="NeWManuaLDiaLCalL('NO');return false;">MANUAL DIAL</a></span> &nbsp; &nbsp;<a style="letter-spacing:1px;" href="#" onclick="NeWManuaLDiaLCalL('FAST');return false;">FAST DIAL</a><br>
        </font>
    </span>

    <!-- Call Back Link -->
    <!--<span style="position:absolute;left:40px;top:458px;z-index:13;visibility:hidden;" id="CallbacksButtons">-->
    <span style="position:relative;left:40px;top:458px;z-index:13;visibility:hidden;" id="CallbacksButtons">
        <font class="body_text">
            <span id="CBstatusSpan">&nbsp;&nbsp;&nbsp;Checking Callbacks...</span><br>
        </font>
    </span>
                                        
    <!-- Footer Links -->
    <?php load_status('Initializing GUI...<br>MaiNfooterspan<br>&nbsp;'); ?>
    <span style="position:relative;left:46px;top:463px;z-index:9;" id="MaiNfooterspan">
        <font class="body_small"><span id="busycallsdisplay"><a href="#"  onclick="conf_channels_detail('SHOW');">Channel Information</a><br></span></font>
        <span id="outboundcallsspan"></span>
        <span id="debugbottomspan"></span>
        
    <!-- Debug -->
    <span style="position:relative;left:-40px;top:10px <?php //echo $DBheight; ?>;" id="DebugLink">
        <font class="body_text"><a href="#" onclick="openDebugWindow();return false;">.</a></font>
    </span>
    </span>
    

    <!-- Pause Code Link -->
    <span style="position:absolute;left:36px;top:<?php echo ($CBheight+4); ?>px;z-index:14;visibility:hidden;" id="PauseCodeButtons">
        <font class="body_text">
            <span id="PauseCodeLinkSpan"><a href="#" onclick="PauseCodeSelectContent_create();return false;">PAUSE CODE</a></span><br>
        </font>
    </span>

    <!-- Voicmeail Button -->
    <span style="position:absolute;left:820px;top:430px;z-index:16;" id="voicemailbutton">
        <a href="#" title="You have no messages!" onclick="voicemail_ariopen();"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/agc_check_voicemail_OFF.gif" width=170 height=30 border=0 alt="VOICEMAIL"></a>
    </span>


    <!-- D1, D2, Mute Links -->
    <span style="position:absolute;left:855px;top:465px;z-index:22;" id="AgentMuteANDPreseTDiaL">
        <font class="body_text">
            <?php if ($PreseT_DiaL_LinKs) {
                if (OSDstrlen($xferconf_a_number)) { 
                    echo "<a href=\"#\" onclick=\"DtMf_PreSet_a_DiaL();return false;\"><font class=\"$diallink_class\">D1 - DIAL</font></a><br>\n";
                }
                if (OSDstrlen($xferconf_b_number)) { 
                    echo "<a href=\"#\" onclick=\"DtMf_PreSet_b_DiaL();return false;\"><font class=\"$diallink_class\">D2 - DIAL</font></a><br>\n";
                }
                echo "<span id=\"DialBlindVMail2\"><img src=\"templates/" . $config['settings']['agent_template'] . "/images/vdc_XB_ammessage_OFF.gif\" width=36 height=17 border=0 alt=\"Blind Transfer VMail Message\"></span>\n";
            } else {
                echo "<br><br>\n";
            } ?>
            <span id="AgentMuteSpan" style="position:absolute;top:0px;left:54px;"></span>
        </font>
    </span>
    <span style="position:absolute;left:560px;top:447px;z-index:44;" id="MutedWarning"></span>

    
    <?php load_status('Initializing GUI...<br>VolumeControlSpan<br>&nbsp;'); ?>
    <!-- Volume Control Links -->
    <span style="position:absolute;left:945px;top:465px;z-index:19;visibility:hidden;" id="VolumeControlSpan">
        <span id="VolumeUpSpan" style="position:absolute;left:0px;top:0px;"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/vdc_volume_up_off.gif" width=28 height=15 border=0></span>
        <span id="VolumeDownSpan" style="position:absolute;left:0px;top:15px;"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/vdc_volume_down_off.gif" width=28 height=15 border=0></span>
    </span>

    

    <!-- Preview Force-Dial Timout -->
    <font id="PreviewFDTimeSpan" style="font-size:35pt; font-weight: bold; color: <?php echo $forcedial_fc; ?>; position:absolute;left:325px;top:380px;z-index:25;"></font>
    

    <?php load_status('Initializing GUI...<br>CallBacKsLisTBox<br>&nbsp;'); ?>
    <!-- Choose From Available Call Backs -->
    <span style="position:absolute;left:2px;top:18px;z-index:0;visibility:hidden;" id="CallBacKsLisTBox">
        <table class=coveragent border=0 bgcolor="<?php echo $callback_bg; ?>" width=<?php echo ($CAwidth+13); ?> height=500>
            <tr>
                <td align=center valign=top>
                    Callbacks For Agent <?php echo $VD_login; ?>
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


    <?php load_status('Initializing GUI...<br>MDHopperListBox<br>&nbsp;'); ?>
    <!-- Choose From Leads in Hopper - Manual Dial only -->
    <span style="position:absolute;left:0px;top:18px;z-index:1;visibility:hidden;" id="MDHopperListBox">
        <table border=1 bgcolor="<?php echo $callback_bg; ?>" width=<?php echo ($CAwidth+13); ?> height=545>
            <tr>
                <td align=center valign=top>
                    <br/>
                    <b>Hopper List</b>
                    <br/>
                    Click on a phone number below to call the customer now.<br>
                    <font class="body_tiny">(When you click on a record below to call it, it will be removed from the list.)<br></font>
                    <br/>
                    <div style="height:350px;width:920px;overflow:scroll;" id="MDHopperList"></div>
                    <br/> &nbsp; 
                    <a href="#" onclick="MDHopperListCheck();return false;">Refresh</a>
                    &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; 
                    <a href="#" onclick="if (PCSpause==1) {AutoDial_ReSume_PauSe('VDADready');} hideDiv('MDHopperListBox');document.getElementById('MDHopperListBox').style.zIndex='0';return false;">Go Back</a>
                </td>
            </tr>
        </table>
    </span>

    

    <?php load_status('Initializing GUI...<br>NeWManuaLDiaLBox<br>&nbsp;'); ?>
    <!-- Manual Dial -->
    <span class=coveragent style="position:absolute;left:2px;top:18px;z-index:39;visibility:hidden;" id="NeWManuaLDiaLBox">
        <table class=coveragent border=0 bgcolor="<?php echo $mandial_bg; ?>" width=<?php echo ($CAwidth); ?> height=545>
            <tr>
                <td align=center valign=top>
                    <br><b><font color=<?php echo $mandial_fc; ?>>New Manual Dial Lead For </font><font color=<?php echo $mandial_bfc; ?>><?php echo $VD_login; ?></font><font color=<?php echo $mandial_fc; ?>> In Campaign </font><font color=<?php echo $mandial_bfc; ?>><?php echo $VD_campaign; ?></font></b>
                    <font color=<?php echo $mandial_fc; ?>>
                        <br><br>Enter information below for the new lead you wish to call.<br>
                        <?php  if (OSDpreg_match("/X/",$dial_prefix)) {
                            echo "Note: a dial prefix of $dial_prefix will be added to the beginning of this number<br>\n";
                        } ?>
                        Note: all new manual dial leads will go into list <?php echo $manual_dial_list_id; ?><br><br>
                    </font>
                    <table>
                        <tr>
                            <td align=right><font class="body_text"><font color=<?php echo $mandial_fc; ?>> Country Code: </font></font></td>
                            <td align=left><font class="body_text"><input type=text size=7 maxlength=10 name=MDDiaLCodE class="cust_form" value="<?php echo $default_phone_code; ?>">&nbsp; <font color=<?php echo $mandial_fc; ?>>(This is usually a 1 in the USA-Canada)</font></font></td>
                        </tr>
                        <tr>
                            <td align=right><font class="body_text"><font color=<?php echo $mandial_fc; ?>> Phone Number: </font></font></td>
                            <td align=left><font class="body_text"><input type=text size=14 maxlength=12 name=MDPhonENumbeR class="cust_form" value="">&nbsp; <font color=<?php echo $mandial_fc; ?>>(12 digits max - digits only)</font></font></td>
                        </tr>
                        <tr>
                            <td align=right><font class="body_text"><font color=<?php echo $mandial_fc; ?>> Search Existing Leads: </font></font></td>
                            <td align=left><font class="body_text"><input type=checkbox name=LeadLookuP size=1 value="0">&nbsp; <font color=<?php echo $mandial_fc; ?>>(If checked will attempt to Find the phone number in the system before inserting it as a New Lead)</font></font></td>
                        </tr>
                        <tr>
                            <td align=center colspan=2>
                                <!-- Manual Dial Override has been disabled because it causes too much trouble -->
                                <span style="display:none;">
                                    <font class="body_text" color=<?php echo $mandial_fc; ?>>&nbsp;<br>
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
    
    <!-- Hot Key Button -->
    <?php if ($HK_statuses_camp > 0 and ($user_level >= $HKuser_level or $VU_hotkeys_active > 0)) { ?>
        <span style="position:absolute;left:720px;top:450px;z-index:16;" id="hotkeysdisplay">
<!--             <a href="#" onMouseOver="HotKeys('ON');" onClick="HotKeys('ONC');"><div class=hotkeysoff>HOT KEYS</div></a> -->
            <a href="#" onmouseover="HotKeys('ON');" onmouseout="HotKeys('OFF');" onclick="HotKeysClick();"><div id=hotkeysbutton class=hotkeyoff>HOT KEYS</div></a>
        </span>
    <?php } ?>
                            
    <?php load_status('Initializing GUI...<br>HotKeyEntriesBox<br>&nbsp;'); ?>
    <!-- Disposition Hot Keys Window -->
    <span style="position:absolute;left:190px;top:415px;width:510px;z-index:24;visibility:hidden;" id="HotKeyEntriesBox">
        <table class=hotkeywindow frame=box bgcolor="<?php echo $hotkey_bg1; ?>" height=70 align=center cellspacing=1 cellpadding=1>
            <tr bgcolor="<?php echo $hotkey_bg2; ?>">
                <td align=center colspan=2 width=530><font class="sh_text">Disposition Hot Keys: </font><font class="font1">Press a number for automatic hangup and disposition.</font></td>
            </tr>
            <tr>
                <td valign=top width=50%>
                    <font class="sk_text" style="overflow:hidden;white-space:nowrap;">
                        <span id="HotKeyBoxA"><?php echo $HKboxA; ?></span>
                    </font>
                </td>
                <td valign=top width=50%>
                    <font class="sk_text" style="overflow:hidden;white-space:nowrap;">
                        <span id="HotKeyBoxB"><?php echo $HKboxB; ?></span>
                    </font>
                </td>
            </tr>
        </table>
    </span>

    
    
    <?php load_status('Initializing GUI...<br>AgentStatusSpan<br>&nbsp;'); ?>
    <!-- Agent Status In Progress -->
    <span style="position:absolute;left:35px;top:<?php echo $CBheight; ?>px;z-index:20;visibility:hidden;" id="AgentStatusSpan">
        <font class="body_text">
            Your Status: 
            <span id="AgentStatusStatus"></span> 
            <br>Calls Dialing: <span id="AgentStatusDiaLs"></span> 
        </font>
    </span>

    
    <?php load_status('Initializing GUI...<br>TransferMain<br>&nbsp;'); ?>
    <!-- Transfer Link -->
    <span style="position:absolute;left:185px;top:<?php echo $HTheight; ?>px;z-index:21;visibility:hidden;" id="TransferMain">
        <table bgcolor="<?php echo $xfer_bg1; ?>" frame=box width=<?php echo ($XFwidth-260); ?>>
            <tr>
                <td align=left>
                    <div class="text_input" id="TransferMaindiv">
                        <font class="body_text">
                            <table width=100%>
                                <tr>
                                    <td align=center colspan=5><font color=<?php echo $xfer_fc; ?>><b>Transfer & Conference Functions</b><br></font></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><font size=1 color=<?php echo $xfer_fc; ?>>Transfer Group:&nbsp;</font><span id="XfeRGrouPLisT"><select size=1 name=XfeRGrouP class="cust_form"><option>-- SELECT A GROUP TO SEND YOUR CALL TO --</option></select></span></td>
                                    <td align=center><div style="visibility:<?php echo $hide_xfer_local_closer; ?>;" id="LocalCloser"><span class="XferLocalCloserButtonOff">Local Closer</span></div></td>
                                    <td align=center><div style="visibility:<?php echo $hide_xfer_dial_with; ?>;" id="DialWithCustomer"><a href="#" onclick="SendManualDial('YES');return false;"><span class="XferDialButton">Dial With Customer</span></a></div></td>
                                    <td align=center><?php if (OSDstrlen($xferconf_a_number)) { ?><a href="#" onclick="DtMf_PreSet_a();return false;"><font class="<?php echo $diallink_class; ?>">D1</font></a><?php } ?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><font size=1 color=<?php echo $xfer_fc; ?>>Number:&nbsp;<input type=text size=26 name=xfernumber maxlength=25 class="cust_form" value="<?php echo $xferconf_a_number; ?>"><input type=hidden name=xferuniqueid></font></td>
                                    <td align=center><div style="visibility:<?php echo $hide_xfer_blind_xfer; ?>;" id="DialBlindTransfer"><span class="XferBlindDialButtonOff">Dial Blind Transfer</span></div></td>
                                    <td align=center><div style="visibility:<?php echo $hide_xfer_park_dial; ?>;" id="ParkCustomerDial"><a href="#" onclick="xfer_park_dial();return false;"><span class="XferParkDialButton">Park Customer Dial</span></a></div></td>
                                    <td align=center><?php if (OSDstrlen($xferconf_b_number)) { ?><a href="#" onclick="DtMf_PreSet_b();return false;"><font class="<?php echo $diallink_class; ?>">D2</font></a><?php } ?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <font size=1 color=<?php echo $xfer_fc; ?>>Seconds:&nbsp;<input type=text size=3 name=xferlength maxlength=4 class="cust_form"></font>&nbsp;
                                        <span style="visibility:<?php echo $hide_xfer_dial_override; ?>;" id="XferOverride"><input type=checkbox name=xferoverride id=xferoverride size=1 value="0"><font size=1 color=<?php echo $xfer_fc; ?>><label for=xferoverride>Override</label></font></span>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td align=center><div style="visibility:<?php echo $hide_xfer_leave_3way; ?>;" id="Leave3WayCall"><span class="XferLeave3wayButtonOff">Leave 3WAY Call</span></div></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><font size=1 color=<?php echo $xfer_fc; ?>>Channel:&nbsp;<input type=text size=26 name=xferchannel maxlength=200 class="cust_form"></font></td>
                                    <td align=center><div style="visibility:<?php echo $hide_xfer_hangup_both; ?>;" id="HangupBothLines"><a href="#" onclick="bothcall_send_hangup();return false;"><span class="XferHangupBothButton">Hangup Both Lines</span></a></div></td>
                                    <td align=center><div style="visibility:<?php echo $hide_xfer_hangup_xfer; ?>;" id="HangupXferLine"><span class="XferHangupButtonOff">Hangup XFER Line</span></div></td>
                                    <td align=center><span style="visibility:<?php echo $hide_xfer_blind_vmail; ?>;background-color:<?php echo $xfer_bg2; ?>;" id="DialBlindVMail"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/vdc_XB_ammessage_OFF.gif" width=36 height=16 border=0 alt="Blind Transfer VMail Message"></span></td>
                                </tr>
                            </table>
                        </font>
                    </div>
                </td>
            </tr>
        </table>
    </span>

    
    <?php load_status('Initializing GUI...<br>HotKeyActionBox<br>&nbsp;'); ?>
    <!-- Dispositioned -->
    <span style="position:absolute;left:5px;top:<?php echo ($HTheight+20); ?>px;z-index:23;visibility:hidden;" id="HotKeyActionBox">
        <table border=0 bgcolor="<?php echo $hotkey_done_bg1; ?>" width=<?php echo $HCwidth; ?> height=70>
            <tr bgcolor="<?php echo $hotkey_done_bg1; ?>">
                <td height=70>
                    <font class="sh_text"> Lead Dispositioned As: </font>
                    <br><br>
                    <center><font class="sd_text"><span id="HotKeyDispo"> - </span></font></center>
                </td>
            </tr>
        </table>
    </span>

    
    <?php load_status('Initializing GUI...<br>CBcommentsBox<br>&nbsp;'); ?>
    <!-- Previous Callback Info -->
    <span style="position:absolute;left:10px;top:<?php echo ($HTheight+20); ?>px;z-index:25;visibility:hidden;" id="CBcommentsBox">
        <table frame=box bgcolor="<?php echo $cbinfo_bg1; ?>" width=<?php echo $HCwidth; ?> height=70>
            <tr bgcolor="<?php echo $cbinfo_bg2; ?>">
                <td align=center><font class="sh_text" color=<?php echo $cbinfo_bfc; ?>>&nbsp;Previous Callback Information</font></td>
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

    
    <?php load_status('Initializing GUI...<br>NoneInSessionBox<br>&nbsp;'); ?>
    <!-- Phone Is Hungup -->
    <span style="position:absolute;left:0px;top:18px;z-index:26;visibility:hidden;" id="NoneInSessionBox">
        <table border=1 bgcolor="<?php echo $noone_bg; ?>" width=1010 height=545>
            <tr>
                <td align=center>
                    <b><font color=<?php echo $noone_fc; ?>>Your phone has either not been answered, or was hung up! <br><br><font color=<?php echo $noone_bg; ?>>(Session ID: <span id="NoneInSessionID"></span>)</font></font></b>
                    <br><br>
                    <a href="#" onclick="NoneInSessionCalL();return false;" style='text-decoration: blink;color:<?php echo $noone_fc; ?>;'><u><b>Try Calling Your Phone Here</b></u></a>
                    <br><br><br>
                    <a href="#" onclick="NoneInSessionOK();return false;" style='color:<?php echo $noone_fc2; ?>;'>Go Back</a>
                </td>
            </tr>
        </table>
    </span>
    

    <?php load_status('Initializing GUI...<br>CustomerGoneBox<br>&nbsp;'); ?>
    <!-- Customer Hungup -->
    <span style="position:absolute;left:0px;top:0px;z-index:27;visibility:hidden;" id="CustomerGoneBox">
        <table border=1 bgcolor="<?php echo $custgone_bg; ?>" width=<?php echo $CAwidth; ?> height=500>
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
    

    <?php load_status('Initializing GUI...<br>WrapupBox<br>&nbsp;'); ?>
    <!-- Call Wrapup -->
    <span style="position:absolute;left:0px;top:0px;z-index:28;visibility:hidden;" id="WrapupBox">
        <table border=1 bgcolor="<?php echo $wrapup_bg; ?>" width=<?php echo $CAwidth; ?> height=550>
            <tr>
                <td align=center>
                    Call Wrapup: <span id="WrapupTimer"></span> seconds remaining in wrapup
                    <br><br>
                    <span id="WrapupMessage"><?php echo $wrapup_message; ?></span>
                    <br><br>
                    <a href="#" onclick="WrapupFinish();return false;">Finish Wrapup and Move On</a>
                </td>
            </tr>
        </table>
    </span>

    
    <?php load_status('Initializing GUI...<br>EmergencYLogouTBoX<br>&nbsp;'); ?>
    <!-- Agent Disabled -->
    <span style="position:absolute;left:0px;top:0px;z-index:29;visibility:hidden;" id="EmergencYLogouTBoX">
        <table class=acrossagent border=0 width=<?php echo $CAwidth; ?> height=564>
            <tr>
                <td align=center  bgcolor="<?php echo $system_alert_bg2; ?>">
                    <font color=<?php echo $login_fc; ?>>
                        <font color=red size="+1"><b>EMERGENCY LOGOUT</b></font>
                        <br><br>
                        A manager has used an Emergency Logout to terminate your agent session.<br>There may be serious issues with your account or the system,<br>please check with your supervisor before attempting to re-login.
                        <br><br>
                        <br><br>
                        <br><br>
                        <br><br>
                        <a href="#" onclick="LogouT('DISABLED');return false;"><font style="font-size:36px;font-weight:bold;">OK</font></a>
                    </font>
                </td>
            </tr>
        </table>
    </span>

    <?php load_status('Initializing GUI...<br>AgenTDisablEBoX<br>&nbsp;'); ?>
    <!-- Agent Disabled -->
    <span style="position:absolute;left:0px;top:0px;z-index:29;visibility:hidden;" id="AgenTDisablEBoX">
        <table class=acrossagent border=0 width=<?php echo $CAwidth; ?> height=564>
            <tr>
                <td align=center>
                    <font color=<?php echo $login_fc; ?>>
                        <font color=red size="+1"><b>AGENT SESSION DISABLED</b></font>
                        <br><br>
                        You have been logged-out because your session record contained old, invalid, or missing data.<br>Logging in again will recreate you session and you can continue where you left off.<br>Please contact your supervisor if the problem persists.
                        <br><br>
                        <br><br>
                        <br><br>
                        <br><br>
                        <a href="#" onclick="LogouT('DISABLED');return false;"><font style="font-size:36px;font-weight:bold;">OK</font></a>
                    </font>
                </td>
            </tr>
        </table>
    </span>


    <?php load_status('Initializing GUI...<br>SysteMDisablEBoX<br>&nbsp;'); ?>
    <!-- System Disabled -->
    <span style="position:absolute;left:0px;top:0px;z-index:29;visibility:hidden;" id="SysteMDisablEBoX">
        <table class=acrossagent border=0 width=<?php echo $CAwidth; ?> height=564>
            <tr>
                <td align=center>
                    <font color=<?php echo $login_fc; ?>>
                        There is a time synchronization problem with your system, please tell your system administrator.<br>
                        <span id="SysteMDisablEInfo"></span>
                        <br><br>
                        <a href="#" onclick="hideDiv('SysteMDisablEBoX');return false;"><font color=grey>Go Back</font></a>
                    </font>
                </td>
            </tr>
        </table>
    </span>


    <?php load_status('Initializing GUI...<br>ARIPanel<br>&nbsp;'); ?>
    <!-- ARIPanel -->
    <span style="visibility:hidden;position:absolute;left:3px;top:40px;z-index:-40;padding:15;background-color:<?php echo $wrapup_bg; ?>;" name="ARIPanel" id="ARIPanel">
        <iframe src="/agent/blank.php" width="<?php echo $CAwidth; ?>" height="500" name="ARIFrame" id="ARIFrame" style="background-color: white;"></iframe>
    </span>


    <?php load_status('Initializing GUI...<br>MultiCallAlerTBoX<br>&nbsp;'); ?>
    <!-- Multicall Alert -->
    <span style="position:absolute;left:0px;top:50px;z-index:41;visibility:hidden;" id="MultiCallAlerTBoX">
        <table class=acrossagent border=1 height=500 cellspacing=20>
            <tr>
                <td align=center bgcolor="<?php echo $system_alert_bg2; ?>">
                    <font color=<?php echo $login_fc; ?>>
                        Alert - Inbound Call<br/>
                        <br/>
                        <span id="MultiCallAlerTInfo">
                            Caller ID Number: ....<br/>
                            Caller ID Name: ...<br/>
                            Has Lead? y/n<br/>
                            Lead Information...<br/>
                        </span>
                        <br/><br/><br/>
                        <font size=2>
                            <a href="#" onclick="multicall_send2voicemail();return false;"><font color=grey>[Send to Voicemail (<span id="MulticallAlerTTimer"></span>)]</font></a>
                            &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <a href="#" onclick="multicall_answer();return false;"><font color=grey>[Answer]</font></a>
                        </font>
                    </font>
                </td>
            </tr>
        </table>
    </span>

    <?php load_status('Initializing GUI...<br>SysteMAlerTBoX<br>&nbsp;'); ?>
    <!-- System Alert -->
    <span style="position:absolute;left:2px;top:300px;z-index:41;visibility:hidden;" id="SysteMAlerTBoX">
        <table class=acrossagent border=1 width=<?php echo $CAwidth; ?> height=300 cellspacing=20>
            <tr>
                <td align=center bgcolor="<?php echo $system_alert_bg2; ?>">
                    <font color=<?php echo $login_fc; ?>>
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
    
    
    <?php load_status('Initializing GUI...<br>DispoSelectBox<br>&nbsp;'); ?>
    <!-- Disposition Window -->
    <span style="position:absolute;left:0px;top:0px;z-index:34;visibility:hidden;" id="DispoSelectBox">
        <table border=1 bgcolor="<?php echo $dispo_bg; ?>"  width=<?php echo ($CAwidth+15); ?> height=550 class=acrossagent>
            <tr>
                <td align=center valign=top>
                    <font color=<?php echo $dispo_fc; ?>>
                        <br> DISPOSITION CALL: <font color=white><span id="DispoSelectPhonE"></span></font> &nbsp; &nbsp; &nbsp; <span id="DispoSelectHAspan"><a href="#" onclick="DispoHanguPAgaiN()">Hangup Again</a></span> &nbsp; &nbsp; &nbsp; <span id="DispoSelectMaxMin"><a href="#" onclick="DispoMinimize()">minimize</a></span><br>
                        <br>
                        <table border=0 cellpadding=5 cellspacing=5 width=620>
                            <tr>
                                <td colspan=2 align=center>
                                    <font color=<?php echo $dispo_fc; ?>><b>Call Dispositions</b></font>
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
                        PAUSE <?php echo (($inbound_man>0)?"INBOUND CALLS":"AGENT DIALING") ?> <br>
                        <a href="#" onclick="DispoMaximize();DispoSelectContent_create('','ReSET');return false;">CLEAR FORM</a>
                        &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; 
                        <a href="#" onclick="DispoMaximize();DispoSelect_submit();return false;"><b>SUBMIT</b></a>
                        <?php if ($submit_method < 1) {
                            echo "<br><br>\n";
                            echo "<a href=\"#\" onclick=\"DispoMaximize();WeBForMDispoSelect_submit();return false;\">WEB FORM SUBMIT</a>\n";
                        } ?>
                        <br> &nbsp;
                    </font>
                </td>
            </tr>
        </table>
    </span>
    <!-- Hide Disposition Button A -->
    <span style="position:absolute;left:4px;top:50px;z-index:31;opacity:0.7;visibility:hidden;" id="DispoButtonHideA">
        <table border=0 bgcolor="<?php echo $dispo_hide; ?>" width=200 height=30 id="DispoButtonHideATable">
            <tr>
                <td align=center valign=top></td>
            </tr>
        </table>
    </span>
    <!-- Hide Disposition Button B -->
    <span style="position:absolute;left:4px;top:118px;z-index:32;opacity:0.7;visibility:hidden;" id="DispoButtonHideB">
        <table border=0 bgcolor="<?php echo $dispo_hide; ?>" width=200 height=370 id="DispoButtonHideBTable">
            <tr>
                <td align=center valign=top>&nbsp;</td>
            </tr>
        </table>
    </span>
    <!-- Hide Disposition Button C -->
    <span style="position:absolute;left:3px;top:15px;z-index:33;opacity:0.8;visibility:hidden;" id="DispoButtonHideC">
        <table border=0 bgcolor="<?php echo $dispo_hide; ?>" width=<?php echo ($CAwidth+1); ?> height=53>
            <tr valign=bottom>
                <td align=center><font size=2 color="#D9DD73"><b>WARNING: All customer information modifications will be saved after disposition.</b></font></td>
            </tr>
        </table>
    </span>

    
    <?php load_status('Initializing GUI...<br>PauseCodeSelectBox<br>&nbsp;'); ?>
    <!-- Pause Code Window -->
    <span style="position:absolute;left:2px;top:18px;z-index:40;visibility:hidden;" id="PauseCodeSelectBox">
        <table class=acrossagent frame=box width=<?php echo ($CAwidth-10); ?> height=500>
            <tr>
                <td align=center valign=top>
                    <font color=<?php echo $pause_fc; ?>>
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

    <?php load_status('Initializing GUI...<br>ExtendedStatusSelectBox<br>&nbsp;'); ?>
    <!-- ExtendedStatus Window -->
    <span style="position:absolute;left:0px;top:0px;z-index:35;visibility:hidden;" id="ExtendedStatusSelectBox">
        <table border=1 bgcolor="<?php echo $dispo_bg; ?>"  width=<?php echo ($CAwidth+15); ?> height=550 class=acrossagent>
            <tr>
                <td align=center valign=top>
                    <font color=<?php echo $dispo_fc; ?>>
                        Extended Status Selection: <b><span id="DispoStatus"></span></b><br>
                        <input type=hidden name=ExtendedStatusParents id="ExtendedStatusParents">
                        <input type=hidden name=ExtendedStatusStatus id="ExtendedStatusStatus">
                        <br>
                        <table border=0 cellpadding=5 cellspacing=5 width=620>
                            <tr>
                                <td colspan=2 align=center>
                                    <font color=<?php echo $dispo_fc; ?>><b>Extended Statuses</b></font>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2 align=center>
                                    <div style="height:320px;overflow-y:auto;">
                                        <span id="ExtendedStatusList"> End-of-call Extended Status Selection </span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        Click the extended status item once to select it, click it a second time to submit.<br>
                        You may be prompted with several levels of selections.
                        <br><br> &nbsp; 
                    </font>
                </td>
            </tr>
        </table>
    </span>
    
    <?php load_status('Initializing GUI...<br>CallBackSelectBox<br>&nbsp;'); ?>
    <!-- Callback Window -->
    <span style="position:absolute;left:2px;top:18px;z-index:35;visibility:hidden;" id="CallBackSelectBox">
        <table border=1 bgcolor="<?php echo $callback_bg3; ?>" width=<?php echo $CAwidth; ?> height=480>
            <tr>
                <td align=center valign=top>
                    <font color=<?php echo $callback_fc; ?>>
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
                        <?php if ($agentonly_callbacks) {
                            echo "<input type=checkbox name=CallBackOnlyMe id=CallBackOnlyMe size=1 value=\"0\"> MY CALLBACK ONLY <br>";
                        } ?>
                        CB Comments: <input type=text name="CallBackCommenTsField" id="CallBackCommenTsField" size=50>
                        <br><br>
                        <a href="#" onclick="CallBackDatE_submit();return false;">SUBMIT</a>
                        <br><br>
                        <span id="CallBackDateContent"><?php echo $CBcal; ?></span>
                        <br><br> &nbsp; 
                    </font>
                </td>
            </tr>
        </table>
    </span>


    <?php load_status('Initializing GUI...<br>PostDateSelectBox<br>&nbsp;'); ?>
    <!-- PostDate Window -->
    <span style="position:absolute;left:2px;top:18px;z-index:35;visibility:hidden;" id="PostDateSelectBox">
        <table border=1 bgcolor="<?php echo $callback_bg3; ?>" width=<?php echo $CAwidth; ?> height=480>
            <tr>
                <td align=center valign=top>
                    <font color=<?php echo $callback_fc; ?>>
                        Select a Post-Date :<span id="PostDatE"></span><br>
                        <input type=hidden name=PostDatESelectioN id="PostDatESelectioN">
                        <span id="PostDatEPrinT">Select a Date Below</span> &nbsp;
                        <br>
                        <a href="#" onclick="PostDatE_submit();return false;">SUBMIT</a>
                        <br><br>
                        <span id="PostDateContent"><?php echo $PDcal; ?></span>
                        <br><br> &nbsp; 
                    </font>
                </td>
            </tr>
        </table>
    </span>

    
    <?php load_status('Initializing GUI...<br>CloserSelectBox<br>&nbsp;'); ?>
    <!-- Closer Inbound Group Window -->
    <span style="position:absolute;left:0px;top:0px;z-index:36;visibility:hidden;" id="CloserSelectBox">
        <table class=acrossagent border=0 height=565>
            <tr>
                <td align=center valign=top>
                    <br><font size=+1 color=<?php echo $closer_fc2; ?>><b>Closer Inbound Group Selection</b></font>
                    <br><br>
                    <span id="CloserSelectContent"> Closer Inbound Group Selection </span>
                    <input type=hidden name=CloserSelectList><br>
                    <input type=checkbox name=CloserSelectBlended id=CloserSelectBlended size=1 value="0">&nbsp;<font color=<?php echo $closer_fc2; ?>><label for=CloserSelectBlended>BLENDED CALLING (outbound activated)</label></font>
                    <br><br>
                    <a href="#" onclick="CloserSelectContent_create();return false;">RESET</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#" onclick="CloserSelect_submit();return false;"><b>SUBMIT</b></a>
                    <br><br><br><br> &nbsp; 
                </td>
            </tr>
        </table>
    </span>
    

    <?php load_status('Initializing GUI...<br>NothingBox<br>&nbsp;'); ?>
    <!-- Preview hide -->
    <span style="position:absolute;left:0px;top:0px;z-index:37;visibility:hidden;" id="NothingBox">
        <button type=button name="inert_button"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/blank.gif" width=1 height=1></button>
        <span id="DiaLLeaDPrevieWHide">Channel</span>
        <span id="DiaLDiaLAltPhonEHide">Channel</span>
        <?php if (!$agentonly_callbacks) {
            echo "<input type=checkbox name=CallBackOnlyMe size=1 value=\"0\"> MY CALLBACK ONLY <br>";
        } ?>
    </span>

    
    <?php load_status('Initializing GUI...<br>ScriptPanel<br>&nbsp;'); ?>
    <!-- Script window -->
    <span style="position:absolute;left:190px;top:90px;z-index:17;width:<?php echo $SSwidth; ?>;height:<?php echo ($SSheight+12); ?>;overflow-x:hidden;overflow-y:scroll;visibility:hidden;" id="ScriptPanel">
        <table class=script_window border=0 bgcolor="<?php echo $script_bg; ?>" width=<?php echo $SSwidth; ?> height=<?php echo ($SSheight+12); ?>>
            <tr>
                <td align=left valign=top><font class="sb_text"><span class="scroll_script" id="ScriptContents">The script will show here once a call is in progress.</span></font></td>
            </tr>
        </table>
    </span>
    
    
    

    
    <!-- =======================================   Here is the main OSDIAL display panel  ======================================= -->
    
<?php $citablewidth=490;$borderwidth=0; $large_comments='1';
if(isset($_SERVER['HTTP_USER_AGENT'])){
    $browser = $_SERVER['HTTP_USER_AGENT'];
} else {
    $browser="unknown";
}
?>
    
    <?php load_status('Initializing GUI...<br>MainPanel<br>&nbsp;'); ?>
    <span style="position:absolute;left:2px;top:46px;z-index:4;" id="MainPanel" border=<?php echo $borderwidth; ?>>
        <table cellpadding=0 cellspacing=0>
            <tr>
                <td>


                    <?php load_status('Initializing GUI...<br>MainPanel<br>MainTable'); ?>
                    <!-- Column widths 205 + 505 + 270 = 980 -->
                    <table id="MainTable" class=mainpage Xstyle="background-color:<?php //echo $panel_bg; ?>;" border=<?php echo $borderwidth; ?> cellpadding=0 cellspacing=0 border=1>
                        <tr>
                            <td height=20 width=22 colspan=3 class=curve2 style="vertical-align:bottom;">&nbsp;
<!--                                 <img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/AgentTopLeft.png" width=22 height=22 align=left> -->
                                <font style='margin-left:180px;' class="body_text" color=<?php echo $status_fct; ?>>STATUS:&nbsp;</font>
                                <font class="body_text" color=<?php echo $status_fc; ?>><span bgcolor=#6987B1 id="MainStatuSSpan"></span></font>
                            </td>
<!--                             <td width=22><img src="templates/<?php //echo $config['settings']['agent_template']; ?>/images/AgentTopRight.png" width=22 height=22 align=right></td> -->
                        </tr>
                        <tr>
                            <td colspan=3><span id="busycallsdebug"></span></td>
                        </tr>
                        <tr>


                            <?php load_status('Initializing GUI...<br>MainPanel<br>AgentActions'); ?>
                            <td width=205 align=left valign=top class=curve3>
                                <div class="body_text" style="overflow:hidden;width:205px;height:372px;">
                                    <center>
                                        <!--<div style="" id="DiaLControl"><a href="#" onclick="ManualDialNext('','','','','','');"><img src="templates/<?php //echo $config['settings']['agent_template']; ?>/images/vdc_LB_dialnextnumber_OFF.gif" width=145 height=16 border=0 alt="Dial Next Number"></a></div>-->
                                        <div id="DiaLControl"><a href="#" onclick="ManualDialNext('','','','','','');"><span class=DialNextButtonOff>Dial Next Number</span></a></div>
                                        <div id="DiaLLeaDPrevieW"><div id="SpacerPreview" style="width:145px;height:4px;border:0px;"></div><span class="CBOptions"><label for="LeadPreview" style="position:relative;top:-1px;"><input type=checkbox style="margin:0 10 0 16;position:relative;top:3px;" name=LeadPreview id=LeadPreview size=1 value="0"> Lead Preview</span></label></div>

                                        <div id="DiaLDiaLAltPhonE"><div id="SpacerAltPhone" style="width:145px;height:4px;border:0px;"></div><span class="CBOptions"><label for="DaiLAltPhonE" style="position:relative;top:-1px;"><input type=checkbox style="margin:0 10 0 16;position:relative;top:3px;" name=DiaLAltPhonE id=DiaLAltPhonE size=1 value="0"> Alt Phone Dial</span></label></div>
                                        <!-- <div id="DiaLLeaDPrevieW"><font class="body_tiny"><input type=checkbox name=LeadPreview id=LeadPreview size=1 value="0"><label for="LeadPreview"> LEAD PREVIEW</label><br></font></div>
                                        <div id="DiaLDiaLAltPhonE"><font class="body_tiny"><input type=checkbox name=DiaLAltPhonE id=DiaLAltPhonE size=1 value="0"><label for="DaiLAltPhonE"> ALT PHONE DIAL</label><br></font></div> -->
                                        <div id="SpacerSpanRSTop" style="width:145px;height:2px;border:0px;"></div>
                                        <div id="RecordStatusControl" style="width:205px:height:35px;overflow:hidden;">
                                            <font color=<?php echo $form_fc; ?>>Recording File</font><br>
                                            <font class="body_tiny">&nbsp;<span id="RecorDingFilename"></span></font><br>
                                            <font color=<?php echo $form_fc; ?>>Recording ID:&nbsp;</font><font class="body_small">&nbsp;<span id="RecorDID"></span></font>
                                        </div>
                                        <div id="SpacerSpanRSBottom" style="width:145px;height:2px;border:0px;"></div>
                                        <!--<div id="RecorDControl"><a href="#" onclick="conf_send_recording('MonitorConf','<?php echo $session_id; ?>','');return false;"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/vdc_LB_startrecording.gif" width=145 height=16 border=0 alt="Start Recording"></a></div>-->
                                        <div id="RecorDControl"><a href="#" onclick="conf_send_recording('MonitorConf','<?php echo $session_id; ?>','');return false;"><span class=RecordingButton>Start Recording</span></a></div>
                                        <div id="SpacerSpanA" style="width:145px;height:16px;border:0px;"></div>
                                        <div id="WebFormSpan"><span class=WebFormButtonOff>Web Form 1</span></div>
                                        <div id="SpacerSpanB" style="width:145px;height:4px;border:0px;"></div>
                                        <div id="WebFormSpan2"><span class=WebFormButtonOff>Web Form 2</span></div>
                                        <div id="SpacerSpanC" style="width:145px;height:16px;border:0px;"></div>
                                        <div id="ParkControl"><span class=ParkButtonOff>Park Call</span></div>
                                        <div id="SpacerSpanD" style="width:145px;height:4px;border:0px;"></div>
                                        <div id="XferControl"><span class=XferButtonOff>Transfer / Conference</span></div>
                                        <div id="SpacerSpanE" style="width:145px;height:10px;border:0px;"></div>
                                        <div class="text_input" id="DTMFDialPad" onMouseOver="DTMFKeys('ON');">
                                            <table cellspacing=1 cellpadding=1 border=0>
                                                <tr>
                                                    <td align=center><span id="DTMFDialPad1"><a href="#" alt="1"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_1_OFF.png" width=26 height=19 border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad2"><a href="#" alt="2 - ABC"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_2_OFF.png" width=26 height=19 border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad3"><a href="#" alt="3 - DEF"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_3_OFF.png" width=26 height=19 border=0></a></span></td>
                                                </tr>
                                                <tr>
                                                    <td align=center><span id="DTMFDialPad4"><a href="#" alt="4 - GHI"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_4_OFF.png" width=26 height=19 border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad5"><a href="#" alt="5 - JKL"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_5_OFF.png" width=26 height=19 border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad6"><a href="#" alt="6 - MNO"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_6_OFF.png" width=26 height=19 border=0></a></span></td>
                                                </tr>
                                                <tr>
                                                    <td align=center><span id="DTMFDialPad7"><a href="#" alt="7 - PQRS"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_7_OFF.png" width=26 height=19 border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad8"><a href="#" alt="8 - TUV"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_8_OFF.png" width=26 height=19 border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad9"><a href="#" alt="9 - WXYZ"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_9_OFF.png" width=26 height=19 border=0></a></span></td>
                                                </tr>
                                                <tr>
                                                    <td align=center><span id="DTMFDialPadStar"><a href="#" alt="*"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_star_OFF.png" width=26 height=19 border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPad0"><a href="#" alt="0"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_0_OFF.png" width=26 height=19 border=0></a></span></td>
                                                    <td align=center><span id="DTMFDialPadHash"><a href="#" alt="#"><img src="templates/<?php echo $config['settings']['agent_template']; ?>/images/dtmf_hash_OFF.png" width=26 height=19 border=0></a></span></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="text_input" id="SendDTMFdiv" style='margin-top:5px'>
                                            <span id="SendDTMF"><a href="#" onclick="SendConfDTMF('<?php echo $session_id; ?>');return false;"><span class=SendDTMF>Send DTMF</span></a> <input type=text size=6 name=conf_dtmf class="cust_form" value="" maxlength=50></span>
                                        </div>
                                        <div id="SpacerSpanF" style="width:145px;height:12px;border:0px;"></div>
                                        <div id="HangupControl"><span class=HangupButtonOff>Hangup Customer</span></div>
                                        <div id="SpacerSpanG" style="width:145px;height:12px;border:0px;"></div>
                                        <div id="RepullControl"></div>
                                        <div id="SpacerSpanH" style="width:145px;height:16px;border:0px;"></div>
                                    </center>
                                </div>
                            </td>


                            <?php load_status('Initializing GUI...<br>MainPanel<br>CustomerInformation'); ?>
                            <td width=505 align=left valign=top>
                                <input type=hidden name=list_id value="">
                                <input type=hidden name=called_count value="">
                                <input type=hidden name=gmt_offset_now value="">
                                <input type=hidden name=country_code value="">
                                <input type=hidden name=uniqueid value="">
                                <input type=hidden name=callserverip value="">
                                <input type=hidden name=status_extended value="">

                                <!-- Customer Information -->
                                <div class="text_input" style="white-space:nowrap;overflow:hidden;" id="MainPanelCustInfo">
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr valign=top>
                                            <td align=center>
                                                <table width=100% align=center border=0>
                                                    <tr valign=top>
                                                        <td>
                                                            <font class="body_text" color=<?php echo $form_fc; ?>><label for=SecondS>Call Duration:&nbsp;</label></font>
                                                            <font class="body_input_rev"><input type=text size=4 name=SecondS id=SecondS class="display_field_ro" value="" readonly></font>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <font class="body_text" color=<?php echo $form_fc; ?>><label for=custdatetime>Customer's Time:&nbsp;</label></font>
                                                            <font class="body_input_rev"><input type=text size=17 maxlength=22 name=custdatetime id=custdatetime class="display_field_ro" value="" readonly></font>
                                                            <span id=callchannel style="font-size:5pt;overflow:hidden;visibility:hidden;"></span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=center valign=top><font class=AFHead><b>Customer Information</b></font><span id="CusTInfOSpaN"></span></td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=lead_id>Lead ID:</label></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_input"><input type=text size=11 name=lead_id id=lead_id maxlength=11 class="display_field_ro" value="" readonly></font>
                                            </td>
                                            <td width=55>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=source_id>Source ID:</label></font>
                                            </td>
                                            <td>
                                                <font class="body_input"><input type=text size=6 name=source_id id=source_id maxlength=6 class="display_field_ro" value="" readonly></font>
                                            </td>
                                            <td align=right>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=external_key>External Key:</label></font>
                                            </td>
                                            <td align=right width=70>
                                                <font class="body_input"><input type=text size=6 name=external_key id=external_key maxlength=100 class="display_field_ro" value="" readonly></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text"><font color=<?php echo $form_fc; ?>><label for=title>Mr / Ms:</label></font>
                                            </td>
                                            <td align=left colspan=2>
                                                <font class="body_input_rev"><input type=text size=4 name=title id=title maxlength=4 class="cust_form" value=""></font>
                                            </td>
                                            <td>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=first_name>&nbsp;First:</label></font>
                                            </td>
                                            <td>
                                                <font class="body_input"> <input type=text size=13 name=first_name id=first_name maxlength=30 class="cust_form" value=""></font>
                                            </td>
                                            <td>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=middle_initial>&nbsp;MI:</label></font>
                                            </td>
                                            <td>
                                                <font class="body_input"><input type=text size=1 name=middle_initial id=middle_initial maxlength=1 class="cust_form" value=""></font>
                                            </td>
                                            <td align=right>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=last_name>&nbsp;Last:</label></font>
                                            </td>
                                            <td align=right width=110>
                                                <font class="body_input"><input type=text size=13 name=last_name id=last_name maxlength=30 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=organization>Company:</label></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_input"><input type=text size=30 name=organization id=organization maxlength=255 class="cust_form" value=""></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=organization_title>Title:</label></font>
                                            </td>
                                            <td align=right>
                                                <font class="body_input"><input type=text size=20 name=organization_title id=organization_title maxlength=255 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=address1>Address 1:</label></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_input"><input type=text size=66 name=address1 id=address1 maxlength=100 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=address2>Address 2:</label></font>
                                            </td>
                                            <td align=left >
                                                <font class="body_input"><input type=text size=22 name=address2 id=address2 maxlength=100 class="cust_form" value=""></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=address3>Address 3 / Phone 3:</label></font>
                                            </td>
                                            <td align=right width=125>
                                                <font class="body_input"><input type=text size=16 name=address3 id=address3 maxlength=100 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=city>City:</label></font>
                                            </td>
                                            <td align=left width=181>
                                                <font class="body_input"><input type=text size=22 name=city id=city maxlength=50 class="cust_form" value=""></font>
                                            </td>
                                            <td align=left width=43> 
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=state>State:</label></font>
                                            </td>
                                            <td>
                                                <font class="body_input"><input type=text size=2 name=state id=state maxlength=2 class="cust_form" value=""></font>
                                            </td>
                                            <td align=right>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=postal_code>Zip:</label></font>
                                                </td>
                                            <td align=right width=90>
                                                <font class="body_input"><input type=text size=9 name=postal_code id=postal_code maxlength=10 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=province>Province:</label></font>
                                            </td>
                                            <td align=lef>
                                                <font class="body_input"><input type=text size=22 name=province id=province maxlength=50 class="cust_form" value=""></font>
                                            </td>
                                            <td align=left width=38>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=email>Email:</label></font>
                                            </td>
                                            <td align=right width=200>
                                                <font class="body_input"><input type=text size=28 name=email id=email maxlength=70 class="cust_form" value="" onchange="checkEmailBlacklist();" onkeyup="checkEmailBlacklist();"></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=phone_number>Phone:</label></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_input"><input type=text size=11 name=phone_number id=phone_number maxlength=12 class="cust_form" value=""></font>
                                            </td>
                                            <td width=80>
                                                <font class="body_text" color=<?php echo $form_fc; ?>>&nbsp;<label for=phone_code>Country Code:</label></font>
                                            </td>
                                            <td>
                                                <font class="body_input"><input type=text size=4 name=phone_code id=phone_code maxlength=10 class="cust_form" value=""></font>
                                            </td>
                                            <td align=right>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=alt_phone>Phone 2:</label></font>
                                            </td>
                                            <td align=right width=100>
                                                <font class="body_input"><input type=text size=11 name=alt_phone id=alt_phone maxlength=12 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=480 <?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left valign=top width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=comments style='margin-right:2px;'>Comments:</label></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_tiny">
                                                    <?php if ($multi_line_comments) { ?>
                                                        <?php if ($comment_size=='3') { ?>
                                                                <textarea name=comments id=comments rows=20 cols=81 class="cust_form" style="height:250px;width:424px !important;"></textarea>
                                                            <?php } elseif ($comment_size=='2') { ?>
                                                                <textarea name=comments id=comments rows=10 cols=81 class="cust_form" style="height:125px;width:424px !important;"></textarea>
                                                            <?php } else { ?>
                                                                <textarea name=comments id=comments rows=3 cols=100% class="cust_form" style="height:45px;width:424px !important;"></textarea>
                                                            <?php } ?>
                                                    <?php } else { ?>
                                                        <input type=text size=81 name=comments id=comments maxlength=255 class="cust_form" value="">
                                                    <?php } ?>
                                                </font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text"><font color=<?php echo $form_fc; ?>><label for=date_of_birth>Birth Date:</label></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_input"><input type=text size=12 name=date_of_birth id=date_of_birth maxlength=10 class="cust_form" value=""></font>
                                            </td>
                                            <td align=right>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=gender>Gender:</label></font>
                                            </td>
                                            <td align=right width=50>
                                                <font class="body_input"><select name=gender id=gender class="cust_form"><option></option><option>M</option><option>F</option></select></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text"><font color=<?php echo $form_fc; ?>><label for=post_date>Post Date:</label></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_input"><input type=text size=22 name=post_date id=post_date maxlength=20 class="cust_form" value=""></font>
                                            </td>
                                            <td align=right>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=vendor_lead_code>Vendor Code:</label></font>
                                            </td>
                                            <td align=right width=125>
                                                <font class="body_input"><input type=text size=15 name=vendor_lead_code id=vendor_lead_code maxlength=20 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding=0 cellspacing=2 width=<?php echo $citablewidth; ?> border=<?php echo $borderwidth; ?>>
                                        <tr>
                                            <td align=left width=<?php echo $title1width; ?>>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=custom1>Custom 1:</label></font>
                                            </td>
                                            <td align=left>
                                                <font class="body_input"><input type=text size=22 name=custom1 id=custom1 maxlength=100 class="cust_form" value=""></font>
                                            </td>
                                            <td align=right>
                                                <font class="body_text" color=<?php echo $form_fc; ?>><label for=custom2>Custom 2:</label></font>
                                            </td>
                                            <td align=right width=170>
                                                <font class="body_input"><input type=text size=22 name=custom2 id=custom2 maxlength=100 class="cust_form" value=""></font>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            
                                <font size=-3><br/><br/></font>

                            </td>

                            <?php load_status('Initializing GUI...<br>MainPanel<br>AdditionalFormFields'); ?>
                            <td width=270 align=center valign=top class=borderright>
                                <div class="AFHead">Additional Forms</div>
                                <?php
                                $cnt = 0;

                                if ($email_templates) {
                                    echo "  <div id=\"AddtlFormsEmailTemplates\" style=" . $cssvis . "position:absolute;left:710px;top:44px;z-index:6;width:265px;height:330px;overflow-x:hidden;overflow-y:auto;border-width:1px;border-style:solid;border-color:$form_fc;border-top-color:#CDEEE3;border-left-color:#CDEEE3;>\n";
                                    echo "  <table width=265><tr><td><table align=center>\n";
                                    echo "      <tr>\n";
                                    echo "          <td colspan=3 align=center>\n";
                                    echo "              <font color=$form_fc class=body_text style=\"font-size:12px\"><b>Email Templates<br />Select Emails to Send After Call</b></font>\n";
                                    echo "          </td>\n";
                                    echo "      </tr>\n";

                                    $ets = explode(',',$email_templates);
                                    foreach ($ets as $eto) {
                                        $eto = ltrim(rtrim($eto,"'"),"'");
                                        $et = get_first_record($link, 'osdial_email_templates', '*', sprintf("et_id='%s' AND active='Y'",mres($eto)) );
                                        echo "      <tr title=\"$desc\">\n";
                                        echo "        <td width=95 align=left colspan=2>\n";
                                        echo "          <div style=\"overflow:hidden;white-space:nowrap;\">\n";
                                        echo "            <input type=checkbox style=\"font-size:10px;\" name=ETids id=\"ET" . $et['et_id'] . "\" value=\"" . $et['et_id'] . "\" class=cust_form>\n";
                                        echo "            <font color=$form_fc class=body_text style=\"font-size:10px;\"><label for=\"ET" . $et['et_id'] . "\">&nbsp;" . $et['et_name'] . "</label></font>\n";
                                        echo "          </div>\n";
                                        echo "        </td>\n";
                                        echo "        <td align=center>\n";
                                        echo "          <a href=\"#\" onclick=\"CustomerData_update(); window.open('print_email_template.php?user=' + user + '&pass=' + pass + '&lead_id=' + document.osdial_form.lead_id.value + '&et_id=" . $et['et_id'] . "', 'osdetprint', 'dependent=1,toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=800,height=300');\">\n";
                                        echo "            <span style=\"font-size:7px;\">[print]</span>\n";
                                        echo "          </a>\n";
                                        echo "        </td>\n";
                                        echo "      </tr>\n";
                                    }

                                    echo "  </table></td></tr></table>\n";
                                    echo "  </div>\n";
                                    $cnt++;
                                }

                                foreach ($forms as $form) {
                                    foreach (OSDpreg_split('/,/',$form['campaigns']) as $fcamp) {
                                        if ($fcamp == '-ALL-' or OSDstrtoupper($fcamp) == OSDstrtoupper($VD_campaign)) {
                                            $cssvis='';
                                            if ($cnt > 0) {
                                                $cssvis = 'visibility:hidden;';
                                            }
                                            echo "  <div id=\"AddtlForms" . $form['name'] . "\" style=" . $cssvis . "position:absolute;left:710px;top:44px;z-index:6;width:265px;height:330px;overflow-x:hidden;overflow-y:auto;border-width:1px;border-style:solid;border-color:$form_fc;border-top-color:#CDEEE3;border-left-color:#CDEEE3;>\n";
                                            echo "  <table width=265><tr><td><table align=center>\n";
                                            echo "      <tr>\n";
                                            echo "          <td colspan=3 align=center>\n";
                                            echo "              <font color=$form_fc class=body_text style=\"font-size:12px\"><b>" . $form['description'] . "<br />" . $form['description2'] . "&nbsp;<b></font>\n";
                                            echo "          </td>\n";
                                            echo "      </tr>\n";
                                            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', sprintf("deleted='0' AND form_id='%s'",mres($form['id'])) );
                                            foreach ($fields as $field) {
                                                $desc = OSDpreg_replace('/"/','',$field['description']);
                                                echo "      <tr title=\"$desc\" valign=\"top\">\n";
                                                echo "          <td width=95 align=left><div style=\"overflow:hidden;white-space:nowrap;\"><font color=$form_fc class=body_text style=\"font-size:10px;\"><label for=\"AF" . $field['id'] . "\">" . $field['description'] . ":&nbsp;</label></font></div></td>\n";
                                                echo "          <td align=left>\n";
                                                if (empty($field['options']) and $field['length']==0) {
                                                    echo "          <textarea style=\"height:32px;font-size:11px;\" cols=\"22\" rows=\"2\" name=AF" . $field['id'] . " id=AF" . $field['id'];
                                                    echo "            onchange=\"var afv=this;";
                                                    echo "              var aflist=document.getElementsByName('" . $form['name'] . '_' . $field['name'] . "');";
                                                    echo "              for(var afli=0;afli<aflist.length;afli++){";
                                                    echo "                if(afv.value!=aflist[afli].value) aflist[afli].value=afv.value;";
                                                    echo "              };\"";
                                                    echo "            class=cust_form></textarea>\n";
                                                } elseif (empty($field['options'])) {
                                                    echo "          <input type=text style=\"font-size:11px;\" size=" . $field['length'] . " maxlength=255 name=AF" . $field['id'] . " id=AF" . $field['id'];
                                                    echo "            onchange=\"var afv=this;";
                                                    echo "              var aflist=document.getElementsByName('" . $form['name'] . '_' . $field['name'] . "');";
                                                    echo "              for(var afli=0;afli<aflist.length;afli++){";
                                                    echo "                if(afv.value!=aflist[afli].value) aflist[afli].value=afv.value;";
                                                    echo "              };\"";
                                                    echo "            class=cust_form value=\"\">\n";
                                                } else {
                                                    echo "          <select style=\"font-size:11px;\" name=AF" . $field['id'] . " id=AF" . $field['id'];
                                                    echo "            onchange=\"var afv=this;";
                                                    echo "              var aflist=document.getElementsByName('" . $form['name'] . '_' . $field['name'] . "');";
                                                    echo "              for(var afli=0;afli<aflist.length;afli++){";
                                                    echo "                if(afv.value!=aflist[afli].value) aflist[afli].value=afv.value;";
                                                    echo "              };\"";
                                                    echo "          >\n";
                                                    $options = OSDpreg_split('/,/',$field['options']);
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
                                    echo "  <div id=\"AddtlFormsNONE\" style=position:absolute;left:710px;top:42px;z-index:6;width:265px;height:325px;overflow-x:hidden;overflow-y:auto;border-width:1px;border-style:solid;border-color:$form_fc;border-top-color:#CDEEE3;border-left-color:#CDEEE3;>\n";
                                    echo "    <table width=265><tr><td><table align=center><tr><td>\n";
                                    echo "      <font color=$form_fc class=body_text style=\"font-size:12px\"><b>No Additional Fields Available<b></font>\n";
                                    echo "    </td></tr></table></td></tr></table>\n";
                                    echo "  </div>\n";
                                } ?>
                            </td>
                        </tr>
                        <tr class=border>
                            <td align=left colspan=3 height=<?php echo $BPheight; ?>>&nbsp;</td>
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
            </tr>
        </table>
        <div id="AddtlFormTab" style="visibility:hidden;position:absolute;left:966px;top:36px;z-index:9;" class=sidetab onclick="AddtlFormOver();">
            FORMS
        </div>
        <div id="AddtlFormTabExpanded" style="visibility:hidden;position:absolute;left:835px;top:19px;z-index:9;box-shadow: rgba(0,0,0,0.5) -1px -1px 18px;background:#8FB1DC;">
                <div id="AddtlFormTabExpandedTop" height=16 onclick="AddtlFormSelect('Cancel');" style='width:142px !important;'>
                    Select A Form
                </div>
                <?php
                if ($email_templates) {
                    echo "  <div class=AddtlFormTabExpandedItem height=29>\n";
                    echo "    <div class=AddtlFormTabExpandedButton id=AddtlFormButEmailTemplates";
                    echo "     onclick=\"AddtlFormSelect('EmailTemplates');\" onmouseover=\"AddtlFormButOver('EmailTemplates');\" onmouseout=\"AddtlFormButOut('EmailTemplates');\" >\n";
                    echo "      EmailTemplates\n";
                    echo "    </div>\n";
                    echo "  </div>\n";
                }
                foreach ($forms as $form) {
                    foreach (OSDpreg_split('/,/',$form['campaigns']) as $fcamp) {
                        if ($fcamp == '-ALL-' or OSDstrtoupper($fcamp) == OSDstrtoupper($VD_campaign)) {
                            echo "  <div class=AddtlFormTabExpandedItem height=29>\n";
                            echo "    <div class=AddtlFormTabExpandedButton id=AddtlFormBut" . $form['name'];
                            echo "     onclick=\"AddtlFormSelect('" . $form['name'] . "');\" onmouseover=\"AddtlFormButOver('" . $form['name'] . "');\" onmouseout=\"AddtlFormButOut('" . $form['name'] . "');\" >\n";
                            echo "     ".$form['name']."\n";
                            echo "    </div>\n";
                            echo "  </div>\n";
                        }
                    }
                } ?>
            <div id="AddtlFormTabExpandedCancel" style="position:absolute;left:127px;top:19px;z-index:9;box-shadow: rgba(0,0,0,0.5) 1px -4px 18px;background:#8FB1DC;" onclick="AddtlFormSelect('Cancel')";>
                CANCEL
            </div>
        </div>
    </span>
<!-- END *********   The end of the main OSDial display panel -->


<?php load_status('Initializing GUI...<br>WebFormPanel1<br>&nbsp;'); ?>
<!-- Inline webform here -->
<span style="visibility:hidden; position:absolute;left:190px;top:92px;z-index:17;" name="WebFormPanel1" id="WebFormPanel1">
    <iframe src="/agent/blank.php" width="785" height="325" name="WebFormPF1" id="WebFormPF1" style="background-color: white;"></iframe>
</span>


<?php load_status('Initializing GUI...<br>WebFormPanel2<br>&nbsp;'); ?>
<span style="visibility:hidden; position:absolute;left:190px;top:92px;z-index:18;" name="WebFormPanel2" id="WebFormPanel2">
    <iframe src="/agent/blank.php" width="785" height="325" name="WebFormPF2" id="WebFormPF2" style="background-color: white;"></iframe>
</span>


<?php
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
echo "</form>\n";
echo "</body>\n";
echo "</html>\n";

    
exit; 


?>
