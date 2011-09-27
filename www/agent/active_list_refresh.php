<?php
### active_list_refresh.php
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
###
### This script is designed purely to serve updates of the live data to the display scripts
### This script depends on the server_ip being sent and also needs to have a valid user/pass from the osdial_users table
### 
### required variables:
###  - $server_ip
###  - $session_name
###  - $user
###  - $pass
### optional variables:
###  - $ADD - ('1','2','3','4','5')
###  - $order - ('asc','desc')
###  - $format - ('text','table','menu','selectlist','textarea')
###  - $bgcolor - ('#123456','white','black','etc...')
###  - $txtcolor - ('#654321','black','white','etc...')
###  - $txtsize - ('1','2','3','etc...')
###  - $selectsize - ('2','3','4','etc...')
###  - $selectfontsize - ('8','10','12','etc...')
###  - $selectedext - ('cc100')
###  - $selectedtrunk - ('Zap/25-1')
###  - $selectedlocal - ('SIP/cc100')
###  - $textareaheight - ('8','10','12','etc...')
###  - $textareawidth - ('8','10','12','etc...')
###  - $field_name - ('extension','busyext','extension_xfer','etc...')
### 

# changes
# 50323-1147 - First build of script
# 50401-1132 - small formatting changes
# 50502-1402 - added field_name as modifiable variable
# 50503-1213 - added session_name checking for extra security
# 50503-1311 - added conferences list
# 50610-1155 - Added NULL check on MySQL results to reduced errors
# 50711-1209 - removed HTTP authentication in favor of user/pass vars
# 60421-1155 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60619-1118 - Added variable filters to close security holes for login form
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
$ADD=get_variable("ADD");
$order=get_variable("order");
$bgcolor=get_variable("bgcolor");
$txtcolor=get_variable("txtcolor");
$txtsize=get_variable("txtsize");
$selectsize=get_variable("selectsize");
$selectfontsize=get_variable("selectfontsize");
$selectedext=get_variable("selectedext");
$selectedtrunk=get_variable("selectedtrunk");
$selectedlocal=get_variable("selectedlocal");
$textareaheight=get_variable("textareaheight");
$textareawidth=get_variable("textareawidth");
$field_name=get_variable("field_name");

### security strip all non-alphanumeric characters out of the variables ###
#$user=OSDpreg_replace("/[^0-9a-zA-Z]/","",$user);
#$pass=OSDpreg_replace("/[^0-9a-zA-Z]/","",$pass);
$ADD=OSDpreg_replace("/[^0-9]/","",$ADD);
$order=OSDpreg_replace("/[^0-9a-zA-Z]/","",$order);
$format=OSDpreg_replace("/[^0-9a-zA-Z]/","",$format);
$bgcolor=OSDpreg_replace("/[^\#0-9a-zA-Z]/","",$bgcolor);
$txtcolor=OSDpreg_replace("/[^\#0-9a-zA-Z]/","",$txtcolor);
$txtsize=OSDpreg_replace("/[^0-9a-zA-Z]/","",$txtsize);
$selectsize=OSDpreg_replace("/[^0-9a-zA-Z]/","",$selectsize);
$selectfontsize=OSDpreg_replace("/[^0-9a-zA-Z]/","",$selectfontsize);
$selectedext=OSDpreg_replace("/[^ \#\*\:\/\@\.\-\_0-9a-zA-Z]/","",$selectedext);
$selectedtrunk=OSDpreg_replace("/[^ \#\*\:\/\@\.\-\_0-9a-zA-Z]/","",$selectedtrunk);
$selectedlocal=OSDpreg_replace("/[^ \#\*\:\/\@\.\-\_0-9a-zA-Z]/","",$selectedlocal);
$textareaheight=OSDpreg_replace("/[^0-9a-zA-Z]/","",$textareaheight);
$textareawidth=OSDpreg_replace("/[^0-9a-zA-Z]/","",$textareawidth);
$field_name=OSDpreg_replace("/[^ \#\*\:\/\@\.\-\_0-9a-zA-Z]/","",$field_name);

# default optional vars if not set
if (empty($ADD)) $ADD="1";
if (empty($order)) $order='desc';
if (empty($format)) $format="text";
if ($format=='table') $DB=1;
if (empty($bgcolor)) $bgcolor='white';
if (empty($txtcolor)) $txtcolor='black';
if (empty($txtsize)) $txtsize='2';
if (empty($selectsize)) $selectsize='4';
if (empty($selectfontsize)) $selectfontsize='10';
if (empty($textareaheight)) $textareaheight='10';
if (empty($textareawidth)) $textareawidth='20';

$version='SVN_Version';
$build='SVN_Build';

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
if (!isset($query_date)) $query_date=$NOW_DATE;
$pt='pt';

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

if ($format=='table') {
    echo "<html>\n";
    echo "<head>\n";
    echo "<!-- VERSION: $version     BUILD: $build    ADD: $ADD   server_ip: $server_ip-->\n";
    echo "<title>List Display: ";
    if ($ADD==1) echo "Live Extensions";
    if ($ADD==2) echo "Busy Extensions";
    if ($ADD==3) echo "Outside Lines";
    if ($ADD==4) echo "Local Extensions";
    if ($ADD==5) echo "Conferences";
    if ($ADD==99999) echo "HELP";
    echo "</title>\n";
    echo "</head>\n";
    echo "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}



######################
# ADD=1 display all live extensions on a server
######################
if ($ADD==1) {
    if (!$field_name) $field_name = 'extension';
    if ($format=='table') echo "<table width=120 bgcolor=$bgcolor cellpadding=0 cellspacing=0>\n";
    if ($format=='menu') echo "<select size=1 name=\"$field_name\">\n";
    if ($format=='selectlist') echo "<select size=$selectsize name=\"$field_name\" style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">\n";
    if ($format=='textarea') echo "<textarea rows=$textareaheight cols=$textareawidth name=extension wrap=off style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">";

    $stmt=sprintf("SELECT extension,fullname FROM phones WHERE server_ip='%s' ORDER BY extension %s;",mres($server_ip),$order);
    if ($format=='table') echo "\n<!-- $stmt -->";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $phones_to_print = mysql_num_rows($rslt);
    $o=0;
    while ($phones_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        if ($format=='table') {
            echo "<tr><td align=left nowrap><font face=\"Arial,Helvetica\" color=$txtcolor size=$txtsize>";
            echo "$row[0] - $row[1]";
            echo "</td></tr>\n";
        }
        if ( ($format=='text') or ($format=='textarea') ) echo "$row[0] - $row[1]\n";
        if ( ($format=='menu') or ($format=='selectlist') ) {
            echo "<option ";
            if ($row[0]=="$selectedext") echo "selected ";
            echo "value=\"$row[0]\">";
            echo "$row[0] - $row[1]";
            echo "</option>\n";
        }
        $o++;
    }

    if ($format=='table') echo "</table>\n";
    if ($format=='menu') echo "</select>\n";
    if ($format=='selectlist') echo "</select>\n";
    if ($format=='textarea') echo "</textarea>\n";
}



######################
# ADD=2 display all busy extensions on a server
######################
if ($ADD==2) {
    if (!$field_name) $field_name = 'busyext';
    if ($format=='table') echo "<table width=120 bgcolor=$bgcolor cellpadding=0 cellspacing=0>\n";
    if ($format=='menu') echo "<select size=1 name=\"$field_name\">\n";
    if ($format=='selectlist') echo "<select size=$selectsize name=\"$field_name\" style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">\n";
    if ($format=='textarea') echo "<textarea rows=$textareaheight cols=$textareawidth name=extension wrap=off style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">";

    $stmt=sprintf("SELECT extension FROM live_channels WHERE server_ip='%s' ORDER BY extension %s;",mres($server_ip),$order);
    if ($format=='table') echo "\n<!-- $stmt -->";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $busys_to_print = mysql_num_rows($rslt);
    $o=0;
    while ($busys_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        if ($format=='table') {
            echo "<tr><td align=left nowrap><font face=\"Arial,Helvetica\" color=$txtcolor size=$txtsize>";
            echo "$row[0]";
            echo "</td></tr>\n";
        }
        if ( ($format=='text') or ($format=='textarea') ) echo "$row[0]\n";
        if ( ($format=='menu') or ($format=='selectlist') ) {
            echo "<option ";
            if ($row[0]=="$selectedext") echo "selected ";
            echo "value=\"$row[0]\">";
            echo "$row[0]";
            echo "</option>\n";
        }
        $o++;
    }

    if ($format=='table') echo "</table>\n";
    if ($format=='menu') echo "</select>\n";
    if ($format=='selectlist') echo "</select>\n";
    if ($format=='textarea') echo "</textarea>\n";
}



######################
# ADD=3 display all busy outside lines(trunks) on a server
######################
if ($ADD==3) {
    if (!$field_name) $field_name = 'trunk';
    if ($format=='table') echo "<table width=120 bgcolor=$bgcolor cellpadding=0 cellspacing=0>\n";
    if ($format=='menu') echo "<select size=1 name=\"$field_name\">\n";
    if ($format=='selectlist') echo "<select size=$selectsize name=\"$field_name\" style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">\n";
    if ($format=='textarea') echo "<textarea rows=$textareaheight cols=$textareawidth name=extension wrap=off style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">";

    $stmt=sprintf("SELECT channel,extension FROM live_channels WHERE server_ip='%s' ORDER BY channel %s;",mres($server_ip),$order);
    if ($format=='table') echo "\n<!-- $stmt -->";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $busys_to_print = mysql_num_rows($rslt);
    $o=0;
    while ($busys_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        if ($format=='table') {
            echo "<tr><td align=left nowrap><font face=\"Arial,Helvetica\" color=$txtcolor size=$txtsize>";
            echo "$row[0] - $row[1]";
            echo "</td></tr>\n";
        }
        if ( ($format=='text') or ($format=='textarea') ) echo "$row[0] - $row[1]\n";
        if ( ($format=='menu') or ($format=='selectlist') ) {
            echo "<option ";
            if ($row[0]=="$selectedtrunk") echo "selected ";
            echo "value=\"$row[0]\">";
            echo "$row[0] - $row[1]";
            echo "</option>\n";
        }
        $o++;
    }

    if ($format=='table') echo "</table>\n";
    if ($format=='menu') echo "</select>\n";
    if ($format=='selectlist') echo "</select>\n";
    if ($format=='textarea') echo "</textarea>\n";
}



######################
# ADD=4 display all busy Local lines on a server
######################
if ($ADD==4) {
    if (!$field_name) $field_name = 'local';
    if ($format=='table') echo "<table width=120 bgcolor=$bgcolor cellpadding=0 cellspacing=0>\n";
    if ($format=='menu') echo "<select size=1 name=\"$field_name\">\n";
    if ($format=='selectlist') echo "<select size=$selectsize name=\"$field_name\" style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">\n";
    if ($format=='textarea') echo "<textarea rows=$textareaheight cols=$textareawidth name=extension wrap=off style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">";

    $stmt=sprintf("SELECT channel,extension FROM live_sip_channels WHERE server_ip='%s' ORDER BY channel %s;",mres($server_ip),$order);
    if ($format=='table') echo "\n<!-- $stmt -->";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $busys_to_print = mysql_num_rows($rslt);
    $o=0;
    while ($busys_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        if ($format=='table') {
            echo "<tr><td align=left nowrap><font face=\"Arial,Helvetica\" color=$txtcolor size=$txtsize>";
            echo "$row[0] - $row[1]";
            echo "</td></tr>\n";
        }
        if ( ($format=='text') or ($format=='textarea') ) echo "$row[0] - $row[1]\n";
        if ( ($format=='menu') or ($format=='selectlist') ) {
            echo "<option ";
            if ($row[0]=="$selectedlocal") echo "selected ";
            echo "value=\"$row[0]\">";
            echo "$row[0] - $row[1]";
            echo "</option>\n";
        }
        $o++;
    }

    if ($format=='table') echo "</table>\n";
    if ($format=='menu') echo "</select>\n";
    if ($format=='selectlist') echo "</select>\n";
    if ($format=='textarea') echo "</textarea>\n";
}



######################
# ADD=5 display all agc-usable conferences on a server
######################
if ($ADD==5) {
    if (!$field_name) $field_name = 'conferences';
    if ($format=='table') echo "<table width=120 bgcolor=$bgcolor cellpadding=0 cellspacing=0>\n";
    if ($format=='menu') echo "<select size=1 name=\"$field_name\">\n";
    if ($format=='selectlist') echo "<select size=$selectsize name=\"$field_name\" style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">\n";
    if ($format=='textarea') echo "<textarea rows=$textareaheight cols=$textareawidth name=extension wrap=off style=\"font-family:sans-serif;font-size:$selectfontsize$pt;\">";

    $stmt=sprintf("SELECT conf_exten,extension FROM conferences WHERE server_ip='%s' ORDER BY conf_exten %s;",mres($server_ip),$order);
    if ($format=='table') echo "\n<!-- $stmt -->";
    $rslt=mysql_query($stmt, $link);
    if ($rslt) $phones_to_print = mysql_num_rows($rslt);
    $o=0;
    while ($phones_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        if ($format=='table') {
            echo "<tr><td align=left nowrap><font face=\"Arial,Helvetica\" color=$txtcolor size=$txtsize>";
            echo "$row[0] - $row[1]";
            echo "</td></tr>\n";
        }
        if ( ($format=='text') or ($format=='textarea') ) echo "$row[0] - $row[1]\n";
        if ( ($format=='menu') or ($format=='selectlist') ) {
            echo "<option ";
            if ($row[0]=="$selectedext") echo "selected ";
            echo "value=\"$row[0]\">";
            echo "$row[0] - $row[1]";
            echo "</option>\n";
        }
        $o++;
    }

    if ($format=='table') echo "</table>\n";
    if ($format=='menu') echo "</select>\n";
    if ($format=='selectlist') echo "</select>\n";
    if ($format=='textarea') echo "</textarea>\n";
}



$ENDtime = date("U");
$RUNtime = ($ENDtime - $StarTtime);
if ($format=='table') echo "\n<!-- script runtime: $RUNtime seconds -->";
if ($format=='table') echo "\n</body>\n</html>\n";

exit; 

?>
