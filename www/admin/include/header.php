<?php
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
# 090410-1118 - Rename of Remote/Off-Hook Agent to External Agent.


if ($ADD==131 && $SUB==2) {
    header ("Content-type: text/csv; charset=utf-8");
    header ("Content-Disposition: inline; filename=export-" . $list_id . '_' . date("Ymd-His") . ".csv");
    require('include/content/lists/export.php');
    exit;
}

#header ("Content-type: text/html; charset=utf-8");
echo "<html>\n";
echo "<head>\n";
echo "  <!-- SESSION_ID: " . session_id() . " -->\n";
echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
$oacjs='';
if ($ADD==999999 && ($SUB==11 || $SUB==12 || $SUB==13 || $SUB==14 )) {
    if (empty($RR) and !empty($useOAC)) $RR=2;
    if (empty($RR)) $RR=4;
    if ($RR <1) $RR=4;
    $metadetail = '';
    if ($SUB==12 || $SUB==14) {
        if ($campaign_id == '') $campaign_id = $group;
        $metadetail .= "&group=$group&campaign_id=$campaign_id&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname";
        $metadetail .= "&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo";
    }
    if (empty($OAC)) {
        if (!empty($useOAC)) {
            $oacjs="setTimeout(function() { refreshOAC('$PHP_SELF','".urlencode("useOAC=$useOAC&OAC=$useOAC&ADD=$ADD&SUB=$SUB&RR=$RR&DB=$DB&adastats=$adastats&active_only=$active_only$metadetail")."',".($RR*1000)."); }, ".($RR*1000).");";
        } else {
            echo "  <meta http-equiv=Refresh content=\"$RR; URL=$PHP_SELF?ADD=$ADD&SUB=$SUB&RR=$RR&DB=$DB&adastats=$adastats&active_only=$active_only$metadetail\">\n";
        }
    }
}
echo "  <link rel=\"stylesheet\" type=\"text/css\" href=\"templates/".$config['settings']['admin_template']."/styles.css\" media=\"screen\">\n";
echo "  <link rel=\"stylesheet\" type=\"text/css\" href=\"styles-print.css\" media=\"print\">\n";

echo "  <title>$t1 Administrator: $title</title>\n";

echo "  <script language=\"Javascript\">\n";
require('include/admin.js');
require('include/CalendarPopup.js');
require('include/EditableSelect.js');
echo "  </script>\n";
echo "</head>\n";

echo "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onload=\"osdialOnLoad(); $oacjs\" onunload=\"osdialOnUnload()\">\n";

echo "<script language=\"JavaScript\">\n";
echo "document.write(getCalendarStyles());\n";
echo "document.write(getEditableSelectStyles());\n";
echo "setEditableSelectImagePath('templates/default/images');\n";
echo "</script>\n";

if (!empty($_COOKIE['webClientGMT'])) $webClientGMT = $_COOKIE['webClientGMT'];
if (!empty($_COOKIE['webClientDST'])) $webClientDST = $_COOKIE['webClientDST'];
if ($webClientDST) $webClientAdjGMT = $webClientGMT - 1;
echo "  <!-- webServerGMT:" . $webServerGMT. " webServerDST:" . $webServerDST . " webServerAdjGMT:" . $webServerAdjGMT . " -->\n";
echo "  <!-- webClientGMT:" . $webClientGMT. " webClientDST:" . $webClientDST . " webClientAdjGMT:" . $webClientAdjGMT . " -->\n";

######################### HTML HEADER BEGIN #######################################
if ($hh=='users') {
    $users_hh="bgcolor =\"$users_color\"";
    $users_fc="$users_font";
    $users_bold="$header_selected_bold";
    $agents_menu1_class='rounded-menu1select';
} else {
    $users_hh=''; $users_fc='WHITE';
    $users_bold="$header_nonselected_bold";
    $agents_menu1_class='rounded-menu1';
}
if ($hh=='campaigns') {
    $campaigns_hh="bgcolor=\"$campaigns_color2\"";
    $campaigns_fc="$campaigns_font";
    $campaigns_bold="$header_selected_bold";
    $campaigns_menu1_class='rounded-menu1select';
} else {
    $campaigns_hh='';
    $campaigns_fc='WHITE';
    $campaigns_bold="$header_nonselected_bold";
    $campaigns_menu1_class='rounded-menu1';
}
if ($hh=='lists') {
    $lists_hh="bgcolor=\"$lists_color\"";
    $lists_fc="$lists_font";
    $lists_bold="$header_selected_bold";
    $lists_menu1_class='rounded-menu1select';
} else {
    $lists_hh=''; $lists_fc='WHITE';
    $lists_bold="$header_nonselected_bold";
    $lists_menu1_class='rounded-menu1';
}
if ($hh=='ingroups') {
    $ingroups_hh="bgcolor=\"$ingroups_color\"";
    $ingroups_fc="$ingroups_font";
    $ingroups_bold="$header_selected_bold";
    $ingroups_menu1_class='rounded-menu1select';
} else {
    $ingroups_hh=''; $ingroups_fc='WHITE';
    $ingroups_bold="$header_nonselected_bold";
    $ingroups_menu1_class='rounded-menu1';
}
if ($hh=='remoteagent') {
    $remoteagent_hh="bgcolor=\"$remoteagent_color\"";
    $remoteagent_fc="$remoteagent_font";
    $remoteagent_bold="$header_selected_bold";
    $remoteagents_menu1_class='rounded-menu1select';
} else {
    $remoteagent_hh='';
    $remoteagent_fc='WHITE';
    $remoteagent_bold="$header_nonselected_bold";
    $remoteagents_menu1_class='rounded-menu1';
}
if ($hh=='usergroups') {
    $usergroups_hh="bgcolor=\"$usergroups_color\"";
    $usergroups_fc="$usergroups_font";
    $usergroups_bold="$header_selected_bold";
    $usergroups_menu1_class='rounded-menu1select';
} else {
    $usergroups_hh=''; $usergroups_fc='WHITE';
    $usergroups_bold="$header_nonselected_bold";
    $usergroups_menu1_class='rounded-menu1';
}
if ($hh=='scripts') {
    $scripts_hh="bgcolor=\"$scripts_color\"";
    $scripts_fc="$scripts_font";
    $scripts_bold="$header_selected_bold";
    $scripts_menu1_class='rounded-menu1select';
} else {
    $scripts_hh='';
    $scripts_fc='WHITE';
    $scripts_bold="$header_nonselected_bold";
    $scripts_menu1_class='rounded-menu1';
}
if ($hh=='filters') {
    $filters_hh="bgcolor=\"$filters_color\"";
    $filters_fc="$filters_font";
    $filters_bold="$header_selected_bold";
    $filters_menu1_class='rounded-menu1select';
} else {
    $filters_hh='';
    $filters_fc='WHITE';
    $filters_bold="$header_nonselected_bold";
    $filters_menu1_class='rounded-menu1';
}
if ($hh=='admin') {
    $admin_hh="bgcolor=\"$admin_color\"";
    $admin_fc="$admin_font";
    $admin_bold="$header_selected_bold";
    $setup_menu1_class='rounded-menu1select';
} else {
    $admin_hh='';
    $admin_fc='WHITE';
    $admin_bold="$header_nonselected_bold";
    $setup_menu1_class='rounded-menu1';
}
if ($hh=='reports') {
    $reports_hh="bgcolor=\"$reports_color\"";
    $reports_fc="$reports_font";
    $reports_bold="$header_selected_bold";
    $reports_menu1_class='rounded-menu1select';
    $reports_heading_bgcolor='bgcolor=#E9E8D9';
} else {
    $reports_hh='';
    $reports_fc='WHITE';
    $reports_bold="$header_nonselected_bold";
    $reports_menu1_class='rounded-menu1';
    $reports_heading_bgcolor='rounded-menu1';
} 

$settings_menucols2=12;
$settings_menucols1=10;

echo "<div class=container>";

echo "<div class=header>";
echo "<table border=0 cellpadding=0 cellspacing=0 align=center class=maintable_top>\n";
echo "  <tr>\n";
echo "    <td colspan=$settings_menucols2>\n";
echo "      <table align='center' border='0' cellspacing='0' cellpadding='0' width=100%>\n";
echo "        <tr>    <!-- First draw the top row  -->\n";
#echo "          <td class='across-top' width='15'><img src='templates/".$config['settings']['admin_template']."/images/topleft.png' width='15' height='16' align='left'></td>\n";
#echo "          <td class='across-top' align='center'></td>\n";
#echo "          <td class='across-top' width='15'><img src='templates/".$config['settings']['admin_template']."/images/topright.png' width='15' height='16' align='right'></td>\n";
echo "          <td colspan=3>&nbsp;";
echo "        </tr>\n";
echo "        <tr class=no-ul valign='top'>\n";
echo "          <td align=left width=33%>\n";
echo "            <table border=0 cellspacing=0 cellpadding=0 width=105> <tr>";
echo "              <td align=center width=38><span class=\"font1 fghome\"><a href=\"".$config['settings']['admin_home_url']."\">HOME</a></span><span class=\"font2 fgdefault\"></span></td>";
echo "              <td>|</td>";
echo "              <td align=left><span class=\"font1 fglogout\"><a href=\"$PHP_SELF?force_logout=1\">Logout</span></a></td>";
echo "            </tr></table>";
echo "            <br /><br />\n";
#echo "         &nbsp;&nbsp;<font size=2>Credit Left:</font><font size=2 color=#060> 5 days</font>";
#echo "         &nbsp;&nbsp;<font size=2>Credit Left:</font><font size=2 color=#600> 1 day</font>";
echo "          </td>\n";
echo "          <td class='user-company' align=center width=33%>\n";
echo "              <span class=fgcompany>".$config['settings']['company_name']."</span><br />\n";
echo "              <span class=\"font2 fgheader\"><b><br>$t1 Administrator</b><br><br><br></span>\n";
echo "          </td>\n";
echo "          <td align=right width=33%>";
echo "              <span class=\"font2 fgclock\" style=\"color: $clock_color\">" . date("l F j, Y") . "&nbsp;&nbsp;</span><br>";
echo "              <div style=\"width: 10em; text-align: right; margin: 5px;\"><div id=\"clock\" style=\"color: $clock_color\"></div></div>";
echo "          </td>\n";
echo "        </tr>\n";
echo "      </table>\n";
echo "    </td>\n";
echo "  </tr>\n";



# BEGIN main menu ====================================================================================================

$height_row1=20;
$cauth=0;
$mmenu = '';

# Start piece
$mmenu .= "<td class='narrow-space' width=15>&nbsp;</td>";

$mmenu .= "    <td class=$agents_menu1_class height=$height_row1 align=center $users_hh><span class=\"font3 fgnavy \"><a href=\"$PHP_SELF?ADD=0\"> Agents </a></span></td>\n";
$mmenu .= "    <td class=$campaigns_menu1_class height=$height_row1 align=center $campaigns_hh width=100><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=10\"> Campaigns </a></span></td>\n";
$mmenu .= "    <td class=$lists_menu1_class height=$height_row1 align=center $lists_hh width=65><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=100\"> Lists </a></span></td>\n";

if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_scripts'] == 1) {
    $mmenu .= "    <td class=$scripts_menu1_class height=$height_row1 align=center $scripts_hh><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=1000000\"> Scripts </a></span></td>\n";
} else {
    $cauth++;
}

if (($config['settings']['enable_filters'] > 0) and ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_filters'] == 1)) {
    $mmenu .= "    <td class=$filters_menu1_class height=$height_row1 align=center $filters_hh><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=10000000\"> Filters </a></span></td>\n";
} else {
    $cauth++;
}

if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_ingroups'] == 1) {
    $mmenu .= "    <td class=$ingroups_menu1_class height=$height_row1 align=center $ingroups_hh><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=1000\"> In-Groups </a></span></td>\n";
} else {
    $cauth++;
}

$mmenu .= "    <td class=$usergroups_menu1_class height=$height_row1 align=center $usergroups_hh width=110><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=100000\">User Groups</a></span></td>\n";

if (($config['settings']['enable_external_agents'] > 0) and ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_external_agents'] == 1)) {
    $mmenu .= "    <td class=$remoteagents_menu1_class height=$height_row1 align=center $remoteagent_hh width=130><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=10000\">External Agents</a></span></td>\n";
} else {
    $cauth++;
}

if ($LOG['view_reports'] > 0) {
    $mmenu .= "    <td class=$reports_menu1_class height=$height_row1 align=center $reports_hh><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=999999\"> Reports </a></span></td>\n";
} else {
    $cauth++;
}

if ($LOG['user_level'] > 8 and ($LOG['multicomp_user'] == 0 or ($LOG['company']['enable_system_calltimes'] + $LOG['company']['enable_system_phones'] + $LOG['company']['enable_system_conferences'] + $LOG['company']['enable_system_servers'] + $LOG['company']['enable_system_statuses']) != 0)) {
    $mmenu .= "    <td class=$setup_menu1_class height=$height_row1 align=center $admin_hh><span class=\"font3 fgnavy\"><a href=\"$PHP_SELF?ADD=10000000000\"> Setup </a></span></td>\n";
} else {
    $cauth++;
}

# End piece
$mmenu .= "<td width=15>&nbsp;</td>";
echo "  <tr class='no-ul'>\n";
echo $mmenu;
echo "  </tr>\n";

# END Main Menu ====================================================================================================



### Agents Menu
if (OSDstrlen($users_hh) > 1) { 
    if (OSDpreg_match('/^(0|6)$/',$ADD)) {
        $agent_show_color=$activemenu2_color;
        $agent_show_class='rounded-menu2select';
    } else {
        $agent_show_color=$inactivemenu2_color;
        $agent_show_class='rounded-menu2';
    }
    if ($ADD == '1') {
        $agent_add_color=$activemenu2_color;
        $agent_add_class='rounded-menu2select';
    } else {
        $agent_add_color=$inactivemenu2_color;
        $agent_add_class='rounded-menu2';
    }
    if ($ADD == '1A')  {
        $agent_copy_color=$activemenu2_color;
        $agent_copy_class='rounded-menu2select';
    } else {
        $agent_copy_color=$inactivemenu2_color;
        $agent_copy_class='rounded-menu2';
    }
    if (OSDpreg_match('/^(550|551|660|661)$/',$ADD))  {
        $agent_search_color=$activemenu2_color;
        $agent_search_class='rounded-menu2select';
    } else {
        $agent_search_color=$inactivemenu2_color;
        $agent_search_class='rounded-menu2';
    }
    if ($ADD == 9) {
        $agent_lead_color=$activemenu2_color;
        $agent_lead_class='rounded-menu2select';
    } else {
        $agent_lead_color=$inactivemenu2_color;
        $agent_lead_class='rounded-menu2';
    }
    if ($ADD == 999999 and $SUB==21) {
        $agent_stats_color=$inactivemenu3_color;
        $agent_stats_class='rounded-menu3';
    } else {
        $agent_stats_color=$inactivemenu3_color;
        $agent_stats_class='rounded-menu3';
    }
    if ($ADD == 999999 and $SUB==22) {
        $agent_status_color=$activemenu3_color;
        $agent_status_class='rounded-menu3';
    } else {
        $agent_status_color=$inactivemenu3_color;
        $agent_status_class='rounded-menu3';
    }
    if ($ADD == 999999 and $SUB==20) {
        $agent_time_color=$activemenu3_color;
        $agent_time_class='rounded-menu3';
    } else {
        $agent_time_color=$inactivemenu3_color;
        $agent_time_class='rounded-menu3';
    }
    if ($ADD == 8) {
        $agent_callbk_color=$activemenu3_color;
        $agent_callbk_class='rounded-menu3select';
    } else {
        $agent_callbk_color=$inactivemenu3_color;
        $agent_callbk_class='rounded-menu3';
    }
    if (OSDpreg_match('/^(2A|3|4|4A|4B)$/',$ADD) and OSDstrlen($user) > 0) {
        $agent_modify_color=$activemenu3_color;
        $agent_modify_class='rounded-menu3select';
    } else {
        $agent_modify_color=$inactivemenu3_color;
        $agent_modify_class='rounded-menu3';
    }
    if (OSDpreg_match('/^(5)$/',$ADD) and OSDstrlen($user) > 0) {
        $agent_delete_color=$activemenu3_color;
        $agent_delete_class='rounded-menu3select';
    } else {
        $agent_delete_color=$inactivemenu3_color;
        $agent_delete_class='rounded-menu3';
    }
    echo "<tr>";
    echo "  <td colspan=$settings_menucols2>";
    echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "      <tr class='no-ul' height=22>\n";
    echo "        <td bgcolor=$bgmenu_color width=5>&nbsp;</td>";
    echo "        <td class=$agent_show_class align=center bgcolor=$agent_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF\"> Show Agents </a></span></td>";
    echo "        <td class=$agent_add_class align=center bgcolor=$agent_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1\"> Add A New Agent </a></span></td>";
    echo "        <td class=$agent_copy_class align=center bgcolor=$agent_copy_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1A\"> Copy Agent </a></span></td>";
    echo "        <td class=$agent_search_class align=center bgcolor=$agent_search_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=550\"> Search For An Agent </a></span></td>";
    if ($config['settings']['enable_lead_allocation'] > 0) {
        echo "        <td class=$agent_lead_class align=center bgcolor=$agent_lead_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=9\"> Lead Allocation </a></span></td>";
    } else {
        echo "        <td bgcolor=$agent_lead_color>&nbsp;</td>\n";
    }
    echo "        <td bgcolor=$inactivemenu2_color class=rounded-menu2 width=200>&nbsp;</td>";
    echo "        <td bgcolor=$bgmenu_color width=5>&nbsp;</td>";

    if ((OSDpreg_match('/^(2A|3|4|4A|4B|5)$/',$ADD)) and OSDstrlen($user) > 0) {
        echo "      <tr>";
        echo "        <td colspan=$settings_menucols2>";
        echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
        echo "            <tr class=no-ul height=25>";
        echo "              <td align=center bgcolor=$agent_modify_color class=$agent_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3&user=$user\"> Modify Agent </a></span></td>";
        if ($LOG['delete_users'] > 0) {
            echo "              <td align=center bgcolor=$agent_delete_color class=$agent_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5&user=$user\"> Delete Agent </a></span></td>";
        }
        if ((!OSDpreg_match('/^(550|551|660|661)$/',$ADD)) and OSDstrlen($user) > 0) {
            if ($LOG['view_agent_stats']) {
                echo "             <td class=$agent_stats_class align=center bgcolor=$agent_stats_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$user\"> Stats </a></span></td>";
            } else {
                echo "             <td bgcolor=$users_color>&nbsp;</td>";
            }
            if ($LOG['view_agent_status']) {
                echo "             <td class=$agent_status_class align=center bgcolor=$agent_status_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$user\"> Status </a></span></td>";
            } else {
                echo "             <td bgcolor=$users_color>&nbsp;</td>";
            }
            if ($LOG['view_agent_timesheet']) {
                echo "            <td class=$agent_time_class align=center bgcolor=$agent_time_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=20&agent=$user\"> Time </a></span></td>";
            } else {
                echo "            <td bgcolor=$users_color>&nbsp;</td>";
            }
            echo "            <td class=$agent_callbk_class align=center bgcolor=$agent_callbk_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=8&user=$user\"> Callbacks </a></span></td> \n";
        } else {
            echo "        <td bgcolor=$inactivemenu2_color class='rounded-menu2' colspan=2 width=100>&nbsp;</td>";
        }
        echo "              <td bgcolor=$inactivemenu3_color class=rounded-menu3 width=250>&nbsp;</td>";
        echo "            </tr>";
        echo "          </table>";
        echo "        </td>";
    }
    echo "      </tr>";
    echo "    </table>";
    echo "  </td>";
    echo "</tr>";
} 


### Campaigns Menu
if (OSDstrlen($campaigns_hh) > 1) { 

    if ($sh=='basic') $sh='list';
    if ($sh=='detail') $sh='list';
    if ($sh=='modify') $sh='list';
    if ($sh=='dialstat') $sh='list';
    if ($sh=='realtime') $sh='list';
    if ($sh=='ivr') $sh='list';
    if ($sh=='cid_areacode') $sh='list';
    if ($sh=='email_blacklist') $sh='list';
    if ($sh=='status'  and $ADD != 32) $sh='list';
    if ($sh=='hotkey'  and $ADD != 33) $sh='list';
    if ($sh=='recycle' and $ADD != 35) $sh='list';
    if ($sh=='autoalt' and $ADD != 36) $sh='list';
    if ($sh=='pause'   and $ADD != 37) $sh='list';
    if ($sh=='listmix' and $ADD != 39) $sh='list';
    if ($sh=='fields') $sh='list';

    if ($sh=='list') {
        $list_sh="bgcolor=\"$subcamp_color\"";
        $list_fc=$menu_h2_color;
    } else {
        $list_sh='';
        $list_fc=$menu_h2_color;
    }
    if ($sh=='status') {
        $status_sh="bgcolor=\"$subcamp_color\"";
        $status_fc="fgnavy";
    } else {
        $status_sh='';
        $status_fc=$menu_h2_color;
    }
    if ($sh=='hotkey') {
        $hotkey_sh="bgcolor=\"$subcamp_color\"";
        $hotkey_fc="fgnavy";
    } else {
        $hotkey_sh='';
        $hotkey_fc=$menu_h2_color;
    }
    if ($sh=='recycle') {
        $recycle_sh="bgcolor=\"$subcamp_color\"";
        $recycle_fc="fgnavy";
    } else {
        $recycle_sh='';
        $recycle_fc=$menu_h2_color;
    }
    if ($sh=='autoalt') {
        $autoalt_sh="bgcolor=\"$subcamp_color\"";
        $autoalt_fc="fgnavy";
    } else {
        $autoalt_sh='';
        $autoalt_fc=$menu_h2_color;
    }
    if ($sh=='pause') {
        $pause_sh="bgcolor=\"$subcamp_color\"";
        $pause_fc="fgnavy";
    } else {
        $pause_sh='';
        $pause_fc=$menu_h2_color;
    }
    if ($sh=='fields') {
        $fields_sh="bgcolor=\"$subcamp_color\"";
        $fields_fc="fgnavy";
    } else {
        $fields_sh='';
        $fields_fc=$menu_h2_color;
    }
    if ($sh=='listmix') {
        $listmix_sh="bgcolor=\"$subcamp_color\"";
        $listmix_fc="fgnavy";
    } else {
        $listmix_sh='';
        $listmix_fc=$menu_h2_color;
    }


    if (OSDstrlen($list_sh) > 1) { 
        if (OSDpreg_match('/^(10|16|61)$/',$ADD)) {
            $camp_show_color=$activemenu2_color;
            $camp_show_class='rounded-menu2select';
        } else {
            $camp_show_color=$inactivemenu2_color;
            $camp_show_class='rounded-menu2';
        }
        if ($ADD == 11) {
            $camp_add_color=$activemenu2_color;
            $camp_add_class='rounded-menu2select';
        } else {
            $camp_add_color=$inactivemenu2_color;
            $camp_add_class='rounded-menu2';
        }
        if ($ADD == 12)  {
            $camp_copy_color=$activemenu2_color;
            $camp_copy_class='rounded-menu2select';
        } else {
            $camp_copy_color=$inactivemenu2_color;
            $camp_copy_class='rounded-menu2';
        }
        if ($SUB == 13)  {
            $camp_real_color=$activemenu2_color;
            $camp_real_class='rounded-menu2select';
        } else {
            $camp_real_color=$inactivemenu2_color;
            $camp_real_class='rounded-menu2';
        }
        if (OSDpreg_match('/^(71|1form|2form|4form|6form)$/',$ADD)) {
            $camp_addform_color=$activemenu2_color;
            $camp_addform_class='rounded-menu2select';
            $camp_modifyform_color=$activemenu2_color;
            $camp_modifyform_class='rounded-menu2select';
            $camp_deleteform_color=$inactivemenu3_color;
            $camp_deleteform_class='rounded-menu3';
        } else {
            if (OSDpreg_match('/^(72|2fields|3fields|4fields|6fields)$/',$ADD)) {
                $camp_modifyform_color=$activemenu2_color;
                $camp_modifyform_class='rounded-menu2select';
                $camp_addform_color=$activemenu2_color;
                $camp_addform_class='rounded-menu2select';
                $camp_deleteform_color=$inactivemenu3_color;
                $camp_deleteform_class='rounded-menu3';
            } else {
                $camp_modifyform_color=$inactivemenu2_color;
                $camp_modifyform_class='rounded-menu2';
                $camp_addform_color=$inactivemenu2_color;
                $camp_addform_class='rounded-menu2';
                if (OSDpreg_match('/^(5form)$/',$ADD)) {
                    $camp_deleteform_color=$activemenu3_color;
                    $camp_deleteform_class='rounded-menu3select';
                } else {
                    $camp_deleteform_color=$inactivemenu3_color;
                    $camp_deleteform_class='rounded-menu3';
                }
            }
        }
        $settings_menucols3=$settings_menucols1 - 5;    // = 5

        echo "<tr><td colspan=$settings_menucols2><table border=0 cellpadding=0 cellspacing=0 width=100%>";
        echo "  <tr class='no-ul' bgcolor=$bgmenu_color Xbgcolor=$activemenu_color>\n";
        echo "    <td height=22 class='narrow-space' bgcolor=$bgmenu_color width=10>&nbsp;</td>";
        echo "    <td class=$camp_show_class align=center bgcolor=$camp_show_color colspan=2><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=10\">Show Campaigns</a></span></td>\n";
        echo "    <td class=$camp_add_class align=center bgcolor=$camp_add_color colspan=2><span class=\"font2 $fgfont_add\"><a href=\"$PHP_SELF?ADD=11\">Add A New Campaign</a></span></td>\n";
        echo "    <td class=$camp_copy_class align=center bgcolor=$camp_copy_color colspan=2><span class=\"font2 $fgfont_copy\"><a href=\"$PHP_SELF?ADD=12\">Copy Campaign</a></span></td>\n";

        if ($LOG['view_agent_realtime_summary']) {
            echo "    <td class=$camp_real_class align=center bgcolor=$camp_real_color colspan=2><span class=\"font2 $fgfont_real\"><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=13\"> Real-Time Campaigns Summary </a></span></td>\n";
        }

        echo "    <td class=$camp_addform_class align=center bgcolor=$camp_addform_color colspan=1><span class=\"font2 $fgfont_fc\"><a href=\"$PHP_SELF?ADD=71\"> Show Additional Forms </a></span></td>\n";
        echo "    <td class='narrow-space' bgcolor=$bgmenu_color width=10>&nbsp;</td>";
        echo "  </tr>\n";
        if (OSDpreg_match('/^(72|2fields|3fields|4fields|6fields|71|2form|4form)$/',$ADD) and (OSDstrlen($id) > 0 or OSDstrlen($form_id) > 0)) {
            echo "      <tr>";
            echo "        <td colspan=$settings_menucols2>";
            echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
            echo "            <tr class=no-ul height=25>";
            if ($LOG['modify_campaigns'] > 0) {
                echo "              <td align=center bgcolor=$camp_modifyform_color class=$camp_modifyform_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=71&id=".($id?$id:$form_id)."\"> Modify Form </a></span></td>";
                echo "              <td align=center bgcolor=$camp_deleteform_color class=$camp_deleteform_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=6form&form_id=".($id?$id:$form_id)."\"> Delete Form </a></span></td>";
            }
            echo "              <td bgcolor=$inactivemenu3_color class='rounded-menu3'>&nbsp;</td>";
            echo "            </tr>";
            echo "          </table>";
            echo "        </td>";
            echo "      </tr>";
        }

        if ($campaign_id != '' and $ADD!=61) {
            $subcamp_color2=$activemenu_color;
            $admin_color2=$inactivemenu_color;
            if (OSDpreg_match('/^(20|21|31|41|51)$/',$ADD) and $SUB<1) {
                $camp_modify_color=$activemenu3_color;
                $camp_modify_class='rounded-menu3select';
            } else {
                $camp_modify_color=$inactivemenu3_color;
                $camp_modify_class='rounded-menu3';
            }
            if (OSDpreg_match('/^(22|42)$/',$ADD) or $SUB==22) {
                $camp_statuses_color=$activemenu3_color;
                $camp_statuses_class='rounded-menu3select';
            } else {
                $camp_statuses_color=$inactivemenu3_color;
                $camp_statuses_class='rounded-menu3';
            }
            if ($ADD==23 or $SUB==23) {
                $camp_hotkeys_color=$activemenu3_color;
                $camp_hotkeys_class='rounded-menu3select';
            } else {
                $camp_hotkeys_color=$inactivemenu3_color;
                $camp_hotkeys_class='rounded-menu3';
            }
            if (OSDpreg_match('/^(25|45|65)$/',$ADD) or $SUB==25) {
                $camp_recycle_color=$activemenu3_color;
                $camp_recycle_class='rounded-menu3select';
            } else {
                $camp_recycle_color=$inactivemenu3_color;
                $camp_recycle_class='rounded-menu3';
            }
            if (OSDpreg_match('/^(26|66)$/',$ADD) or $SUB==26) {
                $camp_autoalt_color=$activemenu3_color;
                $camp_autoalt_class='rounded-menu3select';
            } else {
                $camp_autoalt_color=$inactivemenu3_color;
                $camp_autoalt_class='rounded-menu3';
            }
            if (OSDpreg_match('/^(27|47|67)$/',$ADD) or $SUB==27) {
                $camp_pause_color=$activemenu3_color;
                $camp_pause_class='rounded-menu3select';
            } else {
                $camp_pause_color=$inactivemenu3_color;
                $camp_pause_class='rounded-menu3';
            }
            if (OSDpreg_match('/^(29|39|49|69)$/',$ADD) or $SUB==29) {
                $camp_listmix_color=$activemenu3_color;
                $camp_listmix_class='rounded-menu3select';
            } else {
                $camp_listmix_color=$inactivemenu3_color;
                $camp_listmix_class='rounded-menu3';
            }
            if ($SUB==14) {
                $camp_real_color=$activemenu3_color;
                $camp_real_class='rounded-menu3select';
            } else {
                $camp_real_color=$inactivemenu3_color;
                $camp_real_class='rounded-menu3';
            }
            if (OSDpreg_match('/^(1menu|3menu|4menu|1keys|2keys|3keys|4keys|6keys)$/',$ADD) or $SUB=="2keys") {
                $camp_oivr_color=$activemenu3_color;
                $camp_oivr_class='rounded-menu3select';
            } else {
                $camp_oivr_color=$inactivemenu3_color;
                $camp_oivr_class='rounded-menu3';
            }
            $camp_realtime_class='rounded-menu3';

            $cauth=0;
            echo "  <tr class='no-ul' bgcolor=$bgmenu_color>\n";
            echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
            echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>\n";
            echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
            echo "          <td class=$camp_modify_class align=center bgcolor=$camp_modify_color width=150><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id\">Modify Campaign</a></span></td>\n";
            echo "          <td class=$camp_statuses_class align=center bgcolor=$camp_statuses_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaign_id\">Statuses</a></span></td>\n";
            echo "          <td class=$camp_hotkeys_class align=center bgcolor=$camp_hotkeys_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&SUB=23&campaign_id=$campaign_id\">HotKeys</a></span></td>\n";
            echo "          <td class=$camp_recycle_class align=center bgcolor=$camp_recycle_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&SUB=25&campaign_id=$campaign_id\">Lead Recycling</a></span></td>\n";
            if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_campaign_listmix'] == 1) {
                echo "          <td class=$camp_listmix_class align=center bgcolor=$camp_listmix_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaign_id\">List Mix</a></span></td>\n";
            } else {
                $cauth++;
            }
            echo "          <td class=$camp_autoalt_class align=center bgcolor=$camp_autoalt_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&SUB=26&campaign_id=$campaign_id\">Auto Alt Dial</a></span></td>\n";
            echo "          <td class=$camp_pause_class align=center bgcolor=$camp_pause_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&SUB=27&campaign_id=$campaign_id\">Pause Codes</a></span></td>\n";
            if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_campaign_ivr'] == 1) {
                echo "          <td class=$camp_oivr_class align=center bgcolor=$camp_oivr_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3menu&SUB=2keys&campaign_id=$campaign_id\">In/Out IVR</a></span></td>\n";
            } else {
                $cauth++;
            }
            echo "          <td class=$camp_real_class align=center bgcolor=$camp_real_color>";
            if ($LOG['view_agent_realtime']) {
                echo "              <span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=14&group=$campaign_id&campaign_id=$campaign_id\">Real-Time</a></span>";
            }
            echo "</td>\n";
            if ($cauth) {
                echo "          <td colspan=$cauth align=center bgcolor=$inactivemenu3_color><span class=\"font2 fgnavy\">&nbsp;</span></td>\n";
            }
            echo "        </tr>\n";
            echo "      </table>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
        }
        echo "  </table></td></tr>\n";
    } 
} 



### Lists Menu
if (OSDstrlen($lists_hh) > 1) { 
    if ($ADD == 100) {
        $list_show_color=$activemenu2_color;
        $list_show_class='rounded-menu2select';
    } else {
        $list_show_color=$inactivemenu2_color;
        $list_show_class='rounded-menu2';
    }
    if ($ADD == 111) {
        $list_add_color=$activemenu2_color;
        $list_add_class='rounded-menu2select';
    } else {
        $list_add_color=$inactivemenu2_color;
        $list_add_class='rounded-menu2';
    }
    if ($ADD == 121)  {
        $list_dnc_color=$activemenu2_color;
        $list_dnc_class='rounded-menu2select';
    } else {
        $list_dnc_color=$inactivemenu2_color;
        $list_dnc_class='rounded-menu2';
    }
    if ($ADD == 122) {
        $list_load_color=$activemenu2_color;
        $list_load_class='rounded-menu2select';
    } else {
        $list_load_color=$inactivemenu2_color;
        $list_load_class='rounded-menu2';
    }
    if ($ADD == 131) {
        $list_export_color=$activemenu2_color;
        $list_export_class='rounded-menu2select';
    } else {
        $list_export_color=$inactivemenu2_color;
        $list_export_class='rounded-menu2';
    }
    if ($ADD == 999999 and $SUB==26) {
        $list_search_color=$activemenu2_color;
        $list_search_class='rounded-menu2select';
    } else {
        $list_search_color=$inactivemenu2_color;
        $list_search_class='rounded-menu2';
    }
    if (OSDpreg_match('/^(211|311|411)$/',$ADD)) {
        $list_modify_color=$activemenu3_color;
        $list_modify_class='rounded-menu3select';
    } else {
        $list_modify_color=$inactivemenu3_color;
        $list_modify_class='rounded-menu3';
    }
    if ($ADD == 811) {
        $list_showholds_color=$activemenu3_color;
        $list_showholds_class='rounded-menu3select';
    } else {
        $list_showholds_color=$inactivemenu3_color;
        $list_showholds_class='rounded-menu3';
    }
    if ($ADD == 511 and $SUB!=1 and OSDstrlen($list_id) >0) {
        $list_delete_color=$activemenu3_color;
        $list_delete_class='rounded-menu3select';
    } else {
        $list_delete_color=$inactivemenu3_color;
        $list_delete_class='rounded-menu3';
    }
    if ($ADD == 511 and $SUB==1 and OSDstrlen($list_id) >0) {
        $list_deleteall_color=$activemenu3_color;
        $list_deleteall_class='rounded-menu3select';
    } else {
        $list_deleteall_color=$inactivemenu3_color;
        $list_deleteall_class='rounded-menu3';
    }
    echo "<tr>";
    echo "  <td colspan=12 $settings_menucols2>";
    echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "      <tr class='no-ul' bgcolor=$lists_color>\n";
    echo "        <td height=20 align=left bgcolor=$bgmenu_color width=5>\n";
    echo "        <td class=$list_show_class align=center bgcolor=$list_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=100\"> Show Lists </a></span></td>";
    echo "        <td class=$list_add_class align=center bgcolor=$list_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=111\"> Add A New List </a></span></td>";
    echo "        <td class=$list_dnc_class align=center bgcolor=$list_dnc_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=121\"> Do-Not-Call </a></span></td>";
    if ($LOG['user_level'] > 7 && $LOG['load_leads'] > 0) {
        echo "        <td class=$list_load_class align=center bgcolor=$list_load_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=122\"> Load Leads </a></span></td>";
    }
    if ($LOG['user_level'] > 8 && $LOG['export_leads'] > 0 && ($LOG['multicomp_user'] == 0 or $LOG['company']['export_leads'] > 0)) {
        echo "        <td class=$list_export_class align=center bgcolor=$list_export_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=131\"> Export </a></span></td>";
    }
    if ($LOG['view_lead_search_advanced']) {
        echo "        <td class=$list_search_class align=center bgcolor=$list_search_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=26\"> Lead Search </a></span></td>";
    } elseif ($LOG['view_lead_search']) {
        echo "        <td align=center bgcolor=$list_search_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=27\"> Lead Search </a></span></td>";
    }
    echo "        <td bgcolor=$inactivemenu2_color class='rounded-menu2' width=200>&nbsp;</td>";
    echo "        <td class='narrow-space' bgcolor=$bgmenu_color width=3>&nbsp;</td>";
    echo "  </tr>\n";
    if (OSDpreg_match('/^(211|311|411|511|811)$/',$ADD) and OSDstrlen($list_id) > 0) {
        echo "      <tr>";
        echo "        <td colspan=$settings_menucols2>";
        echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
        echo "            <tr class=no-ul height=25>";
        echo "              <td align=center bgcolor=$list_modify_color class=$list_modify_class Xwidth=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=311&list_id=$list_id\"> Modify List </a></span></td>";
        echo "              <td align=center class=$list_showholds_class bgcolor=$list_showholds_color Xwidth=200><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=811&list_id=$list_id\"> Show Call Back Holds </a></span></td>\n";
        if ($LOG['delete_lists'] > 0) {
            echo "              <td align=center bgcolor=$list_delete_color class=$list_delete_class Xwidth=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=511&list_id=$list_id\"> Delete List </a></span></td>";
            echo "              <td align=center bgcolor=$list_deleteall_color class=$list_deleteall_class Xwidth=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=511&SUB=1&list_id=$list_id\"> Delete List & Leads </a></span></td>";
        }
        echo "              <td bgcolor=$inactivemenu3_color class='rounded-menu3' width=400>&nbsp;</td>";
        echo "            </tr>";
        echo "          </table>";
        echo "        </td>";
        echo "      </tr>";
    } 
    echo "    </table>";
    echo "  </td>";
    echo "</tr>";
} 



### Scripts Menu
if (OSDstrlen($scripts_hh) > 1) { 
    if ($ADD == 1000000) {
        $script_show_color=$activemenu2_color;
        $script_show_class='rounded-menu2select';
    } else {
        $script_show_color=$inactivemenu2_color;
        $script_show_class='rounded-menu2';
    }
    if ($ADD == 1111111) {
        $script_add_color=$activemenu2_color;
        $script_add_class='rounded-menu2select';
    } else {
        $script_add_color=$inactivemenu2_color;
        $script_add_class='rounded-menu2';
    }
    if (OSDpreg_match('/^(3111111|4111111|6111111)$/',$ADD) and OSDstrlen($script_id) > 0) {
        $script_modify_color=$activemenu3_color;
        $script_modify_class='rounded-menu3select';
    } else {
        $script_modify_color=$inactivemenu3_color;
        $script_modify_class='rounded-menu3';
    }
    if ($ADD==5111111 and OSDstrlen($script_id) > 0) {
        $script_delete_color=$activemenu3_color;
        $script_delete_class='rounded-menu3select';
    } else {
        $script_delete_color=$inactivemenu3_color;
        $script_delete_class='rounded-menu3';
    }
    if (OSDpreg_match('/^(0email|2email|6email)$/',$ADD)) {
        $script_showemail_color=$activemenu2_color;
        $script_showemail_class='rounded-menu2select';
    } else {
        $script_showemail_color=$inactivemenu2_color;
        $script_showemail_class='rounded-menu2';
    }
    if ($ADD == '1email') {
        $script_addemail_color=$activemenu2_color;
        $script_addemail_class='rounded-menu2select';
    } else {
        $script_addemail_color=$inactivemenu2_color;
        $script_addemail_class='rounded-menu2';
    }
    if (OSDpreg_match('/^(3email|4email)$/',$ADD) and OSDstrlen($et_id) > 0) {
        $script_emailmodify_color=$activemenu3_color;
        $script_emailmodify_class='rounded-menu3select';
    } else {
        $script_emailmodify_color=$inactivemenu3_color;
        $script_emailmodify_class='rounded-menu3';
    }
    if ($ADD=='5email' and OSDstrlen($et_id) > 0) {
        $script_emaildelete_color=$activemenu3_color;
        $script_emaildelete_class='rounded-menu3select';
    } else {
        $script_emaildelete_color=$inactivemenu3_color;
        $script_emaildelete_class='rounded-menu3';
    }
    echo "<tr>";
    echo "  <td colspan=$settings_menucols2>";
    echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "      <tr class='no-ul' height=20>\n";
    echo "        <td bgcolor=$bgmenu_color align=left width=5>\n";
    echo "        <td align=center class=$script_show_class bgcolor=$script_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1000000\"> Show Scripts </a></span></td>\n";
    echo "        <td align=center class=$script_add_class bgcolor=$script_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1111111\"> Add A New Script</a></span></td>\n";

    if (file_exists($WeBServeRRooT . '/admin/include/content/scripts/email_templates.php')) {
        echo "        <td align=center class=$script_showemail_class bgcolor=$script_showemail_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=0email\"> Show Email Templates </a></span></td>\n";
        echo "        <td align=center class=$script_addemail_class bgcolor=$script_addemail_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1email\"> Add A New Email Template </a></span></td>\n";
        echo "        <td bgcolor=$inactivemenu2_color class='rounded-menu2' width=300>&nbsp;</td>";

    } else {
        echo "        <td bgcolor=$inactivemenu2_color class='rounded-menu2' colspan=3>&nbsp;</td>";
    }
    echo "    <td bgcolor=$bgmenu_color colspan=4 width=5>&nbsp;</td>";
    echo "  </tr>\n";
    if ((OSDpreg_match('/^(3111111|4111111|5111111)$/',$ADD) or ($ADD==6111111 AND ($SUB!='' or $script_button_id!=''))) and OSDstrlen($script_id) > 0) {
        $smodlabel = 'Script';
        if (($SUB!='' or $script_button_id!='') and $ADD!=6111111) $smodlabel = 'Button/Objection';
        echo "      <tr>";
        echo "        <td colspan=$settings_menucols2>";
        echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
        echo "            <tr class=no-ul height=25>";
        echo "              <td align=center bgcolor=$script_modify_color class=$script_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3111111&script_id=$script_id\"> Modify $smodlabel </a></span></td>";
        if ($LOG['delete_scripts'] > 0) {
            echo "              <td align=center bgcolor=$script_delete_color class=$script_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5111111&script_id=$script_id".($SUB!=''?'&script_button_id='.$SUB:'')."\" class=alert> Delete $smodlabel </a></span></td>";
        }
        echo "              <td bgcolor=$inactivemenu3_color class='rounded-menu3' colspan=$settings_menucols2>&nbsp;</td>";
        echo "            </tr>";
        echo "          </table>";
        echo "        </td>";
        echo "      </tr>";
    }
    if ((OSDpreg_match('/^(3email|4email|5email)$/',$ADD) and OSDstrlen($et_id) > 0)) {
        echo "      <tr>";
        echo "        <td colspan=$settings_menucols2>";
        echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
        echo "            <tr class=no-ul height=25>";
        echo "              <td align=center bgcolor=$script_emailmodify_color class=$script_emailmodify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3email&et_id=$et_id\"> Modify Email Template </a></span></td>";
        if ($LOG['delete_scripts'] > 0) {
            echo "              <td align=center bgcolor=$script_emaildelete_color class=$script_emaildelete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5email&et_id=$et_id\" class=alert> Delete Email Template </a></span></td>";
        }
        echo "              <td bgcolor=$inactivemenu3_color class='rounded-menu3' colspan=$settings_menucols2>&nbsp;</td>";
        echo "            </tr>";
        echo "          </table>";
        echo "        </td>";
        echo "      </tr>";
    }
    echo "    </table>";
    echo "  </td>\n";
    echo "</tr>";
} 



### Filters Menu
if (OSDstrlen($filters_hh) > 1) { 
    if (OSDpreg_match('/^(10000000|21111111|61111111)$/',$ADD)) {
        $filter_show_color=$activemenu2_color;
        $filter_show_class='rounded-menu2select';
    } else {
        $filter_show_color=$inactivemenu2_color;
        $filter_show_class='rounded-menu2';
    }
    if ($ADD == 11111111) {
        $filter_add_color=$activemenu2_color;
        $filter_add_class='rounded-menu2select';
    } else {
        $filter_add_color=$inactivemenu2_color;
        $filter_add_class='rounded-menu2';
    }
    if (OSDpreg_match('/^(31111111|41111111)$/',$ADD) and OSDstrlen($lead_filter_id) > 0) {
        $filter_modify_color=$activemenu3_color;
        $filter_modify_class='rounded-menu3select';
    } else {
        $filter_modify_color=$inactivemenu3_color;
        $filter_modify_class='rounded-menu3';
    }
    if ($ADD==51111111 and OSDstrlen($lead_filter_id) > 0) {
        $filter_delete_color=$activemenu3_color;
        $filter_delete_class='rounded-menu3select';
    } else {
        $filter_delete_color=$inactivemenu3_color;
        $filter_delete_class='rounded-menu3';
    }
    echo "<tr>";
    echo "  <td colspan=$settings_menucols2>";
    echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "      <tr class='no-ul' height=22>\n";
    echo "        <td bgcolor=$bgmenu_color class='narrow-space'>&nbsp;</td>";
    echo "        <td align=center bgcolor=$filter_show_color class=$filter_show_class><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=10000000\"> Show Filters </a></span></td>\n";
    echo "        <td align=center bgcolor=$filter_add_color class=$filter_add_class><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=11111111\"> Add A New Filter </a></span></td>\n";
    echo "        <td bgcolor=$inactivemenu2_color class=rounded-menu2 colspan=2 width=400>&nbsp;</td>";
    echo "        <td bgcolor=$bgmenu_color width=3>&nbsp;</td>";

    echo "    </td>\n";
    echo "  </tr>\n";
    if (OSDpreg_match('/^(31111111|41111111|51111111)$/',$ADD) and OSDstrlen($lead_filter_id) > 0) {
        echo "      <tr>";
        echo "        <td colspan=$settings_menucols2>";
        echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
        echo "            <tr class=no-ul height=25>";
        echo "              <td align=center bgcolor=$filter_modify_color class=$filter_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$lead_filter_id\"> Modify Filter </a></span></td>";
        if ($LOG['delete_filters'] > 0) {
            echo "              <td align=center bgcolor=$filter_delete_color class=$filter_delete_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=51111111&lead_filter_id=$lead_filter_id\" class=alert> Delete Filter </a></span></td>";
        }
        echo "              <td bgcolor=$inactivemenu3_color class='rounded-menu3' colspan=$settings_menucols2>&nbsp;</td>";
        echo "            </tr>";
        echo "          </table>";
        echo "        </td>";
        echo "      </tr>";
    }
    echo "    </table>";
    echo "  </td>\n";
    echo "</tr>";
} 



### InGroups Menu
if (OSDstrlen($ingroups_hh) > 1) { 
    if (OSDpreg_match('/^(1000|6111)$/',$ADD)) {
        $ingroup_show_color=$activemenu2_color;
        $ingroup_show_class='rounded-menu2select';
    } else {
        $ingroup_show_color=$inactivemenu2_color;
        $ingroup_show_class='rounded-menu2';
    }
    if ($ADD == 1001) {
        $ingroup_a2a_color=$activemenu2_color;
        $ingroup_a2a_class='rounded-menu2select';
    } else {
        $ingroup_a2a_color=$inactivemenu2_color;
        $ingroup_a2a_class='rounded-menu2';
    }
    if ($ADD == 1111) {
        $ingroup_add_color=$activemenu2_color;
        $ingroup_add_class='rounded-menu2select';
    } else {
        $ingroup_add_color=$inactivemenu2_color;
        $ingroup_add_class='rounded-menu2';
    }
    if ($ADD == 1211) {
        $ingroup_copy_color=$activemenu2_color;
        $ingroup_copy_class='rounded-menu2select';
    } else {
        $ingroup_copy_color=$inactivemenu2_color;
        $ingroup_copy_class='rounded-menu2';
    }
    if (OSDpreg_match('/^(2011|2111|3111|4111)$/',$ADD) and OSDstrlen($group_id) > 0) {
        $ingroup_modify_color=$activemenu3_color;
        $ingroup_modify_class='rounded-menu3select';
    } else {
        $ingroup_modify_color=$inactivemenu3_color;
        $ingroup_modify_class='rounded-menu3';
    }
    if ($ADD==5111 and OSDstrlen($group_id) > 0) {
        $ingroup_delete_color=$activemenu3_color;
        $ingroup_delete_class='rounded-menu3select';
    } else {
        $ingroup_delete_color=$inactivemenu3_color;
        $ingroup_delete_class='rounded-menu3';
    }
    if ($ADD==53 and OSDstrlen($group_id) > 0) {
        $ingroup_deleteauto_color=$activemenu3_color;
        $ingroup_deleteauto_class='rounded-menu3select';
    } else {
        $ingroup_deleteauto_color=$inactivemenu3_color;
        $ingroup_deleteauto_class='rounded-menu3';
    }
    echo "<tr>";
    echo "  <td colspan=$settings_menucols2>";
    echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "      <tr class='no-ul' height=22>\n";
    echo "        <td bgcolor=$bgmenu_color width=5>&nbsp;</td>\n";
    echo "        <td align=center class=$ingroup_show_class bgcolor=$ingroup_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1000&let=''\"> Show In-Groups </a></span></td>\n";
    echo "        <td align=center class=$ingroup_add_class bgcolor=$ingroup_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1111\"> Add A New In-Group </a></span></td>\n";
    echo "        <td align=center class=$ingroup_copy_class bgcolor=$ingroup_copy_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1211\"> Copy In-Group </a></span></td>\n";
    echo "        <td align=center class=$ingroup_a2a_class bgcolor=$ingroup_a2a_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1001\"> Show Agent2Agent Groups </a></span></td>\n";
    echo "        <td bgcolor=$inactivemenu2_color class=rounded-menu2 width=325>&nbsp;</td>";
    echo "        <td bgcolor=$bgmenu_color width=5>&nbsp;</td>";
    echo "  </tr>\n";
    if (OSDpreg_match('/^(2011|2111|3111|4111|5111|53)$/',$ADD) and OSDstrlen($group_id) > 0) {
        echo "      <tr>";
        echo "        <td colspan=$settings_menucols2>";
        echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
        echo "            <tr class=no-ul height=25>";
        echo "              <td align=center bgcolor=$ingroup_modify_color class=$ingroup_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3111&group_id=$group_id\"> Modify In-Group </a></span></td>";
        if ($LOG['delete_ingroups'] > 0) {
            echo "              <td align=center bgcolor=$ingroup_delete_color class=$ingroup_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5111&group_id=$group_id\"> Delete In-Group </a></span></td>";
            echo "              <td align=center bgcolor=$ingroup_deleteauto_color class=$ingroup_deleteauto_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=53&campaign_id=$group_id&stage=IN\"> Clear Auto Calls </a></span></td>";
        }
        echo "              <td bgcolor=$inactivemenu3_color class='rounded-menu3'>&nbsp;</td>";
        echo "            </tr>";
        echo "          </table>";
        echo "        </td>";
        echo "      </tr>";
    }
    echo "    </table>";
    echo "  </td>";
    echo "</tr>";
} 



### Usergroups Menu
if (OSDstrlen($usergroups_hh) > 1) { 
    if (OSDpreg_match('/^(100000|611111)$/',$ADD)) {
        $ugroups_show_color=$activemenu2_color;
        $ugroups_show_class='rounded-menu2select';
    } else {
        $ugroups_show_color=$inactivemenu2_color;
        $ugroups_show_class='rounded-menu2';
    }
    if ($ADD == 111111) {
        $ugroups_add_color=$activemenu2_color;
        $ugroups_add_class='rounded-menu2select';
    } else {
        $ugroups_add_color=$inactivemenu2_color;
        $ugroups_add_class='rounded-menu2';
    }
    if ($ADD == 999999 and $SUB==24 and $group==$LOG[user_group]) {
        $ugroups_report_color=$activemenu2_color;
        $ugroups_report_class='rounded-menu3select';
    } else {
        $ugroups_report_color=$inactivemenu2_color;
        $ugroups_report_class='rounded-menu3';
    }
    if (OSDpreg_match('/^(211111|311111|411111)$/',$ADD) and OSDstrlen($user_group) > 0) {
        $ugroups_modify_color=$activemenu3_color;
        $ugroups_modify_class='rounded-menu3select';
    } else {
        $ugroups_modify_color=$inactivemenu3_color;
        $ugroups_modify_class='rounded-menu3';
    }
    if ($ADD==511111 and OSDstrlen($user_group)> 0) {
        $ugroups_delete_color=$activemenu3_color;
        $ugroups_delete_class='rounded-menu3select';
    } else {
        $ugroups_delete_color=$inactivemenu3_color;
        $ugroups_delete_class='rounded-menu3';
    }
    if ($ADD==8111 and OSDstrlen($user_group)> 0) {
        $ugroups_showholds_color=$activemenu3_color;
        $ugroups_showholds_class='rounded-menu3select';
    } else {
        $ugroups_showholds_color=$inactivemenu3_color;
        $ugroups_showholds_class='rounded-menu3';
    }
    echo "<tr>";
    echo "  <td colspan=$settings_menucols2>";
    echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "      <tr class='no-ul' height=22>\n";
    echo "        <td bgcolor=$bgmenu_color width=5>&nbsp;</td>\n";
    echo "        <td align=center class=$ugroups_show_class bgcolor=$ugroups_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=100000\"> Show User Groups </a></span></td>\n";
    echo "        <td align=center class=$ugroups_add_class bgcolor=$ugroups_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=111111\"> Add A New User Group </a></span></td>\n";
    if ($LOG['view_usergroup_hourly_stats']) {
        echo "        <td align=center class=$ugroups_report_class bgcolor=$ugroups_report_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=24&group=$LOG[user_group]\"> Group Hourly Report </a></span></td>\n";
    } else {
        echo "    <td bgcolor=$inactivemenu2_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
    }
    echo "        <td bgcolor=$inactivemenu2_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
    echo "      </span>\n";
    echo "        <td bgcolor=$bgmenu_color colspan=4 width=3>&nbsp;</td>";
    echo "    </td>\n";
    echo "  </tr>\n";
    if (OSDpreg_match('/^(211111|311111|411111|511111|8111)$/',$ADD) and OSDstrlen($user_group) > 0) {
        echo "      <tr>";
        echo "        <td colspan=$settings_menucols2>";
        echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
        echo "            <tr class=no-ul height=25>";
        echo "              <td align=center class=$ugroups_modify_class bgcolor=$ugroups_modify_color width=175><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=311111&user_group=$user_group\"> Modify User Group </a></span></td>\n";
        echo "              <td align=center class=$ugroups_showholds_class bgcolor=$ugroups_showholds_color width=200><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=8111&user_group=$user_group\"> Show Callback Holds </a></span></td>\n";
        if ($LOG['delete_user_groups'] > 0 and $LOG['allowed_campaignsALL'] > 0) {
            echo "              <td align=center class=$ugroups_delete_class bgcolor=$ugroups_delete_color width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=511111&user_group=$user_group\"> Delete User Group </a></span></td>\n";
        }
        echo "              <td bgcolor=$inactivemenu3_color class='rounded-menu3' width=810>&nbsp;</td>";
        echo "            </tr>";
        echo "          </table>";
        echo "        </td>";
        echo "      </tr>";
        echo "    <tr>";
    }
    echo "    </table>";
    echo "  </td>";
    echo "</tr>";
}



### External/Remote Agents Menu
if (OSDstrlen($remoteagent_hh) > 1) { 
    if (OSDpreg_match('/^(10000|61111)$/',$ADD)) {
        $extagents_show_color=$activemenu2_color;
        $extagents_show_class='rounded-menu2select';
    } else {
        $extagents_show_color=$inactivemenu2_color;
        $extagents_show_class='rounded-menu2';
    }
    if ($ADD == 11111) {
        $extagents_add_color=$activemenu2_color;
        $extagents_add_class='rounded-menu2select';
    } else {
        $extagents_add_color=$inactivemenu2_color;
        $extagents_add_class='rounded-menu2';
    }
    if (OSDpreg_match('/^(21111|31111|41111)$/',$ADD) and (OSDstrlen($remote_agent_id) > 0 or OSDstrlen($user_start) > 0)) {
        $extagents_modify_color=$activemenu3_color;
        $extagents_modify_class='rounded-menu3select';
    } else {
        $extagents_modify_color=$inactivemenu3_color;
        $extagents_modify_class='rounded-menu3';
    }
    if ($ADD==51111 and OSDstrlen($remote_agent_id)>0) {
        $extagents_delete_color=$activemenu3_color;
        $extagents_delete_class='rounded-menu3select';
    } else {
        $extagents_delete_color=$inactivemenu3_color;
        $extagents_delete_class='rounded-menu3';
    }
    echo "<tr>";
    echo "  <td colspan=$settings_menucols2>";
    echo "    <table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "      <tr class='no-ul' height=22>\n";
    echo "        <td bgcolor=$bgmenu_color width=5>&nbsp;</td>\n";
    echo "        <td height=20 bgcolor=$inactivemenu_color align=left>\n";
    echo "        <td align=center bgcolor=$extagents_show_color class=$extagents_show_class><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=10000\"> Show External Agents </a></span></td>\n";
    echo "        <td align=center bgcolor=$extagents_add_color class=$extagents_add_class><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=11111\"> Add New External Agents </a></span></td>\n";
    echo "        <td bgcolor=$inactivemenu2_color class='rounded-menu2' colspan=2 width=425>&nbsp;</td>";
    echo "        <td bgcolor=$bgmenu_color width=5>&nbsp;</td>";
    echo "    </td>\n";
    echo "  </tr>\n";
    if (OSDpreg_match('/^(21111|31111|41111|51111)$/',$ADD) and (OSDstrlen($remote_agent_id) > 0 or OSDstrlen($user_start) > 0)) {
        echo "  <tr>";
        echo "    <td colspan=7>";
        echo "      <table cellpadding=0 cellspacing=0 width=100% border=0>";
        echo "        <tr class=no-ul height=25>";
        if ($LOG['modify_remoteagents']==1) {
            echo "          <td align=center bgcolor=$extagents_modify_color class=$extagents_modify_class width=200><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=31111&remote_agent_id=$remote_agent_id\"> Modify External Agent </a></span></td>\n";
            if ($LOG['delete_remote_agents'] > 0) {
                echo "          <td align=center bgcolor=$extagents_delete_color class=$extagents_delete_class width=200><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=51111&remote_agent_id=$remote_agent_id\"> Delete External Agent </a></span></td>\n";
            }
        } else {
            echo "          <td align=center bgcolor=$extagents_delete_color class=$extagents_delete_class width=200><span class=\"font2 alert\">Not Allowed</span></td>\n";
        }
        echo "          <td bgcolor=$inactivemenu3_color class='rounded-menu3' colspan=1 width=775>&nbsp;</td>";
        echo "        </tr>";
        echo "      </table>";
        echo "    </td>";
        echo "  </tr>";
    }echo "    </table>";
    echo "  </td>";
    echo "</tr>";
} 



### Reports Menu
if (OSDstrlen($reports_hh) > 1) { 
    if ($ADD == 999999) {
        $reports_show_color=$activemenu2_color;
        $reports_show_class='rounded-menu2select';
    } else {
        $reports_show_color=$inactivemenu2_color;
        $reports_show_class='rounded-menu2';
    }

    echo "<tr>";
    echo "    <td height=20 align=left bgcolor=$bgmenu_color colspan=$settings_menucols2>\n";
    echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=$filters_color>\n";
    echo "        <td align=center bgcolor=$reports_show_color class=$reports_show_class><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999\"> Show Reports </a></span></td>\n";
    echo "        <td bgcolor=$inactivemenu2_color class=rounded-menu2 colspan=2 width=400>&nbsp;</td>";
    echo "        <td bgcolor=$bgmenu_color width=3>&nbsp;</td>";
    echo "  </tr>\n";
    echo "  </table>\n";
    echo "  </td>\n";
    echo "</tr>";
}



### Admin/Setup menu.
if (OSDstrlen($admin_hh) > 1 and $LOG['ast_admin_access']>0) { 
    if (OSDpreg_match('/^(311111111111111|411111111111111)$/', $ADD)) {
        $ssettings_color=$activemenu2_color;
        $ssettings_class='rounded-menu2select';
        $ssettings_sh=1;
    } else {
        $ssettings_color=$inactivemenu2_color;
        $ssettings_class='rounded-menu2';
        $ssettings_sh=0;
    }
    if (OSDpreg_match('/^(10comp|11comp|21comp|31comp|41comp|51comp|61comp)$/', $ADD)) {
        $company_color=$activemenu2_color;
        $company_class='rounded-menu2select';
        $company_sh=1;
    } else {
        $company_color=$inactivemenu2_color;
        $company_class='rounded-menu2';
        $company_sh=0;
    }
    if (OSDpreg_match('/^(100000000000|111111111111|399111111111111|499111111111111|399211111111111|499211111111111|699211111111111|399911111111111|499911111111111|211111111111|311111111111|411111111111|511111111111|611111111111|221111111111|421111111111|621111111111)$/', $ADD)) {
        $server_color=$activemenu2_color;
        $server_class='rounded-menu2select';
        $server_sh=1;
    } else {
        $server_color=$inactivemenu2_color;
        $server_class='rounded-menu2';
        $server_sh=0;
    }
    if (OSDpreg_match('/^(1carrier|2carrier|3carrier|4carrier|5carrier|6carrier)$/', $ADD)) {
        $carriers_color=$activemenu2_color;
        $carriers_class='rounded-menu2select';
        $carriers_sh=1;
    } else {
        $carriers_color=$inactivemenu2_color;
        $carriers_class='rounded-menu2';
        $carriers_sh=0;
    }
    if (OSDpreg_match('/^(10000000000|11111111111|21111111111|31111111111|41111111111|51111111111|61111111111)$/', $ADD)) {
        $phones_color=$activemenu2_color;
        $phones_class='rounded-menu2select';
        $phones_sh=1;
    } else {
        $phones_color=$inactivemenu2_color;
        $phones_class='rounded-menu2';
        $phones_sh=0;
    }
    if (OSDpreg_match('/^(1000000000000|1111111111111|2111111111111|3111111111111|4111111111111|5111111111111|6111111111111|10000000000000|11111111111111|21111111111111|31111111111111|41111111111111|5111111111111|61111111111111)$/', $ADD)) {
        $conference_color=$activemenu2_color;
        $conference_class='rounded-menu2select';
        $conference_sh=1;
    } else {
        $conference_color=$inactivemenu2_color;
        $conference_class='rounded-menu2';
        $conference_sh=0;
    }
    if (OSDpreg_match('/^(100000000|111111111|211111111|311111111|411111111|511111111|611111111|1000000000|1111111111|2111111111|3111111111|4111111111|5111111111|6111111111|321111111)$/', $ADD)) {
        $times_color=$activemenu2_color;
        $times_class='rounded-menu2select';
        $times_sh=1;
    } else {
        $times_color=$inactivemenu2_color;
        $times_class='rounded-menu2';
        $times_sh=0;
    }
    if (OSDpreg_match('/^(10media|11media|21media|31media|41media|51media|61media|10tts|11tts|21tts|31tts|41tts|51tts|61tts)$/', $ADD)) {
        $media_color=$activemenu2_color;
        $media_class='rounded-menu2select';
        $media_sh=1;
    } else {
        $media_color=$inactivemenu2_color;
        $media_class='rounded-menu2';
        $media_sh=0;
    }
    if (OSDpreg_match('/^(221111111111111|321111111111111|421111111111111|231111111111111|331111111111111|431111111111111)$/', $ADD)) {
        $status_color=$activemenu2_color;
        $status_class='rounded-menu2select';
        $status_sh=1;
    } else {
        $status_color=$inactivemenu2_color;
        $status_class='rounded-menu2';
        $status_sh=0;
    }
    if ($ADD==311111) {
        $ugroups_color=$activemenu2_color;
        $ugroups_class='rounded-menu2select';
        $ugroups_sh=1;
    } else {
        $ugroups_color=$inactivemenu2_color;
        $ugroups_class='rounded-menu2';
        $ugroups_sh=0;
    }


    $amenu = '';
    $acnt = 0;

    $amenu .= "<tr><td height=20 align=left Xbgcolor=$bgmenu_color colspan=$settings_menucols2>";
    $amenu .= "<table border=0 class=no-ul cellpadding=0 cellspacing=0 width=100%>";
    $amenu .= "  <tr>";
    $amenu .= "    <td bgcolor=$bgmenu_color width=10>&nbsp;</td>";

    if ($LOG['multicomp_user'] == 0) {
        $amenu .= "    <td height=20 align=center bgcolor=$ssettings_color class=$ssettings_class colspan=1><span class=\"font2 $settings_fc\"><a href=\"$PHP_SELF?ADD=311111111111111\"> System Settings </a></span></td>\n";
    } else {
        $acnt += 2;
    }
    if ($LOG["multicomp_admin"] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$company_color class=$company_class colspan=1><span class=\"font2 $company_fc\"><a href=\"$PHP_SELF?ADD=10comp\"> Companies </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_servers'] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$server_color class=$server_class colspan=1><span class=\"font2 $server_fc\"><a href=\"$PHP_SELF?ADD=100000000000\"> Servers </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_carriers'] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$carriers_color class=$carriers_class colspan=1><span class=\"font2 $carriers_fc\"><a href=\"$PHP_SELF?ADD=3carrier\"> Carriers </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_phones'] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$phones_color class=$phones_class colspan=1><span class=\"font2 $phones_fc\"><a href=\"$PHP_SELF?ADD=10000000000\"> Phones </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_conferences'] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$conference_color class=$conference_class colspan=1><span class=\"font2 $conference_fc\"><a href=\"$PHP_SELF?ADD=1000000000000\"> Conferences </a></span></td>\n";
    } else {
        $acnt += 1;
    }if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_calltimes'] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$times_color class=$times_class colspan=1><span class=\"font2 $times_fc\"><a href=\"$PHP_SELF?ADD=100000000\"> Call Times </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    if ($LOG['multicomp_user'] == 0) {
        $amenu .= "    <td height=20 align=center bgcolor=$media_color class=$media_class colspan=1><span class=\"font2 $media_fc\"><a href=\"$PHP_SELF?ADD=10media\"> Media </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_statuses'] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$status_color class=$status_class colspan=1><span class=\"font2 $status_fc\"><a href=\"$PHP_SELF?ADD=321111111111111\"> System Statuses </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    $amenu .= "    <td bgcolor=$bgmenu_color width=10>&nbsp;</td>";
    $amenu .= "  </tr>\n";
    echo $amenu;



### Settings Sub-Menu.
    if ($ssettings_sh > 0) {
        if (OSDpreg_match('/^(311111111111111|411111111111111)$/', $ADD)) {
            $ssettings_show_color=$activemenu3_color;
            $ssettings_show_class='rounded-menu3select';
        } else {
            $ssettings_show_color=$inactivemenu3_color;
            $ssettings_show_class='rounded-menu3';
        }
        echo "  <tr class='no-ul' Xbgcolor=$bgmenu_color>\n";
        echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>\n";
        echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
        echo "          <td bgcolor=$ssettings_show_color class=$ssettings_show_class align=center width=175><span class=\"font2 fgnavy\"><a href=> Modify System Settings </a></span></td>\n";
        echo "          <td bgcolor=$inactivemenu3_color class='rounded-menu3' colspan=2 width=400>&nbsp;</td>";
        echo "        </tr>\n";
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



### Company Sub-Menu.
    if ($company_sh > 0) { 
        if (OSDpreg_match('/^(10comp|61comp)$/',$ADD)) {
            $company_show_color=$activemenu3_color;
            $company_show_class='rounded-menu3select';
        } else {
            $company_show_color=$inactivemenu3_color;
            $company_show_class='rounded-menu3';
        }
        if ($ADD=='11comp') {
            $company_add_color=$activemenu3_color;
            $company_add_class='rounded-menu3select';
        } else {
            $company_add_color=$inactivemenu3_color;
            $company_add_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(21comp|31comp|41comp)$/',$ADD)) {
            $company_modify_color=$activemenu4_color;
            $company_modify_class='rounded-menu4select';
        } else {
            $company_modify_color=$inactivemenu4_color;
            $company_modify_class='rounded-menu4';
        }
        if ($ADD=='51comp') {
            $company_delete_color=$activemenu4_color;
            $company_delete_class='rounded-menu4select';
        } else {
            $company_delete_color=$inactivemenu4_color;
            $company_delete_class='rounded-menu4';
        }
        echo "  <tr class='no-ul'>";
        echo "    <td class='narrow-space' colspan=11>";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>";
        echo "        <tr class='no-ul' height=25>";
        echo "          <td width=6></td>\n";
        echo "          <td align=center bgcolor=$company_show_color class=$company_show_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=10comp\"> Show Companies </a></span></td>";
        echo "          <td align=center bgcolor=$company_add_color class=$company_add_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=11comp\"> Add A New Company </a></span</td>";
        echo "          <td colspan=$cauth align=center class=rounded-menu3 bgcolor=$inactivemenu3_color width=650><span class=\"font2 fgnavy\">&nbsp;</span></td>\n";
        echo "          <td bgcolor=$bgmenu_color width=1>&nbsp;</td>\n";
        echo "        </tr>\n";
        if (OSDpreg_match('/^(21comp|31comp|41comp|51comp)$/',$ADD) and (OSDstrlen($company_id) > 0 or OSDstrlen($company_name) > 0)) {
            echo "    <tr>";
            echo "      <td colspan=5>";
            echo "        <table cellpadding=0 cellspacing=0 width=100% border=0>";
            echo "          <tr height=25>";
            echo "            <td align=center bgcolor=$company_modify_color class=$company_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31comp&company_id=$company_id\"> Modify Company </a></span></td>";
            if ($LOG['multicomp_admin']>0) {
                echo "            <td align=center bgcolor=$company_delete_color class=$company_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=51comp&company_id=$company_id\"> Delete Company </a></span></td>";
            }
            echo "            <td bgcolor=$inactivemenu4_color class='rounded-menu4' width=600></td>";
            echo "          </tr>";
            echo "        </table>";
            echo "     </td>";
            echo "   </tr>";
        }
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



### Servers Sub-Menu.
    if ($server_sh > 0) { 
        if (OSDpreg_match('/^(100000000000|611111111111)$/',$ADD)) {
            $server_show_color=$activemenu3_color;
            $server_show_class='rounded-menu3select';
        } else {
            $server_show_color=$inactivemenu3_color;
            $server_show_class='rounded-menu3';
        }
        if ($ADD==111111111111) {
            $server_add_color=$activemenu3_color;
            $server_add_class='rounded-menu3select';
        } else {
            $server_add_color=$inactivemenu3_color;
            $server_add_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(399111111111111|499111111111111)$/', $ADD)) {
            $server_arch_color=$activemenu3_color;
            $server_arch_class='rounded-menu3select';
        } else {
            $server_arch_color=$inactivemenu3_color;
            $server_arch_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(399211111111111|499211111111111|699211111111111)$/', $ADD)) {
            $server_qcs_color=$activemenu3_color;
            $server_qcs_class='rounded-menu3select';
        } else {
            $server_qcs_color=$inactivemenu3_color;
            $server_qcs_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(399911111111111|499911111111111)$/', $ADD)) {
            $server_extdnc_color=$activemenu3_color;
            $server_extdnc_class='rounded-menu3select';
        } else {
            $server_extdnc_color=$inactivemenu3_color;
            $server_extdnc_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(211111111111|311111111111|411111111111|221111111111|421111111111|621111111111)$/',$ADD) and (OSDstrlen($server_ip) > 0 or OSDstrlen($server_id) > 0)) {
            $server_modify_color=$activemenu4_color;
            $server_modify_class='rounded-menu4select';
        } else {
            $server_modify_color=$inactivemenu4_color;
            $server_modify_class='rounded-menu4';
        }
        if ($ADD==511111111111 and OSDstrlen($server_id) >0) {
            $server_delete_color=$activemenu4_color;
            $server_delete_class='rounded-menu4select';
        } else {
            $server_delete_color=$inactivemenu4_color;
            $server_delete_class='rounded-menu4';
        }
        echo "  <tr class='no-ul'>\n";
        echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>\n";
        echo "        <tr align=center class='no-ul' height=25>\n";
        echo "          <td width=6></td>\n";
        echo "          <td class=$server_show_class align=center bgcolor=$server_show_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=100000000000\"> Show Servers </a></span></td>\n";
        echo "          <td class=$server_add_class align=center bgcolor=$server_add_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=111111111111\"> Add A New Server </a></span></td>\n";
        echo "          <td class=$server_arch_class align=center bgcolor=$server_arch_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=399111111111111\"> Archive Server </a></span></td>\n";
        echo "          <td class=$server_qcs_class align=center bgcolor=$server_qcs_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=399211111111111\"> QC Servers </a></span></td>\n";
        echo "          <td class=$server_extdnc_class align=center bgcolor=$server_extdnc_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=399911111111111\"> External DNC Database </a></span></td>\n";
        echo "          <td Xcolspan=$cauth align=center class=rounded-menu3 bgcolor=$inactivemenu3_color width=350><span class=\"font2 fgnavy\">&nbsp;</span></td>\n";
        echo "          <td width=6></td>\n";
        echo "        </tr>\n";
        if (OSDpreg_match('/^(211111111111|311111111111|411111111111|511111111111|221111111111|421111111111|621111111111)$/',$ADD) and (OSDstrlen($server_ip) > 0 or OSDstrlen($server_id) > 0)) {
            echo "      <tr>";
            echo "        <td colspan=7>";
            echo "          <table border=0 cellpadding=0 cellspacing=0 width=100%>";
            echo "            <tr class=no-ul height=25>";
            if ($LOG['modify_servers']==1) {
                echo "              <td align=center bgcolor=$server_modify_color class=$server_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=311111111111&server_id=$server_id\"> Modify Server </a></span></td>\n";
                echo "              <td align=center bgcolor=$server_delete_color class=$server_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=511111111111&server_id=$server_id&server_ip=$server_ip\"> Delete Server </a></span></td>\n";
            } else {
                echo "              <td align=center bgcolor=$server_modify_color class=$server_modify_class width=175><span class=\"font2 fgnavy\"> Not Allowed </span></td>\n";
            }
            echo "              <td bgcolor=$inactivemenu4_color class='rounded-menu3' colspan=1>&nbsp;</td>";
            echo "            </tr>";
            echo "          </table>";
            echo "        </td>";
            echo "      </tr>";
        }
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }




### Carriers Sub-Menu. 
    if ($carriers_sh >0) { 
        if (($ADD=='6carrier' and $SUB==2) or ($ADD=='3carrier' and $SUB==1) or ($ADD=='3carrier' and $carrier_id ==0)) {
            $carriers_show_color=$activemenu3_color;
            $carriers_show_class='rounded-menu3select';
        } else {
            $carriers_show_color=$inactivemenu3_color;
            $carriers_show_class='rounded-menu3';
        }
        if ($ADD=='1carrier' and $SUB==2) {
            $carriers_add_color=$activemenu3_color;
            $carriers_add_class='rounded-menu3select';
        } else {
            $carriers_add_color=$inactivemenu3_color;
            $carriers_add_class='rounded-menu3';
        }
        if ($ADD=='1carrier' and $SUB==4) {
            $carriers_adddid_color=$activemenu4_color;
            $carriers_adddid_class='rounded-menu4select';
        } else {
            $carriers_adddid_color=$inactivemenu4_color;
            $carriers_adddid_class='rounded-menu4';
        }
        if (OSDpreg_match('/^(2carrier|3carrier|4carrier)$/',$ADD) and ($SUB==2)) {
            $carriers_modify_color=$activemenu4_color;
            $carriers_modify_class='rounded-menu4select';
        } else {
            $carriers_modify_color=$inactivemenu4_color;
            $carriers_modify_class='rounded-menu4';
        }
        if (OSDpreg_match('/^(2carrier|3carrier|4carrier)$/',$ADD) and $SUB==3) {
            $carriers_modifyserver_color=$activemenu4_color;
            $carriers_modifyserver_class='rounded-menu4select';
        } else {
            $carriers_modifyserver_color=$inactivemenu4_color;
            $carriers_modifyserver_class='rounded-menu4';
        }
        if (OSDpreg_match('/^(2carrier|3carrier|4carrier)$/',$ADD) and $SUB==4) {
            $carriers_modifydid_color=$activemenu4_color;
            $carriers_modifydid_class='rounded-menu4select';
        } else {
            $carriers_modifydid_color=$inactivemenu4_color;
            $carriers_modifydid_class='rounded-menu4';
        }
        if ($ADD=='5carrier' and $SUB==2) {
            $carriers_delete_color=$activemenu4_color;
            $carriers_delete_class='rounded-menu4select';
        } else {
            $carriers_delete_color=$inactivemenu4_color;
            $carriers_delete_class='rounded-menu4';
        }
        if ($ADD=='5carrier' and $SUB==4) {
            $carriers_deletedid_color=$activemenu4_color;
            $carriers_deletedid_class='rounded-menu4select';
        } else {
            $carriers_deletedid_color=$inactivemenu4_color;
            $carriers_deletedid_class='rounded-menu4';
        }
        if (($ADD=='3carrier' or $ADD=='5carrier') and ($SUB==3 or $SUB==4)) {
            $carriers_return_color=$inactivemenu4_color;
            $carriers_return_class='rounded-menu4';
        } else {
            $carriers_return_color=$inactivemenu4_color;
            $carriers_return_class='rounded-menu4';
        }
        echo "  <tr class='no-ul'>\n";
        echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>\n";
        echo "        <tr class='no-ul' height=25>\n";
        echo "          <td width=6></td>\n";
        echo "          <td align=center bgcolor=$carriers_show_color class=$carriers_show_class width=150><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=1\"> Show Carriers </a></span></td>\n";
        echo "          <td align=center bgcolor=$carriers_add_color class=$carriers_add_class width=150><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=2\"> Add New Carrier </a></span></td>\n";
        echo "          <td bgcolor=$inactivemenu3_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
        echo "          <td width=6></td>\n";
        echo "        </tr>\n";
        if ((OSDpreg_match('/^(2carrier|3carrier|4carrier|5carrier)$/',$ADD) and (OSDpreg_match('/^(2|3|4)$/', $SUB))) or ($ADD=='1carrier' and $SUB==4)) {
            echo "      <tr>";
            echo "        <td colspan=$settings_menucols2>";
            echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
            echo "            <tr class=no-ul height=25>";
            echo "              <td align=center bgcolor=$carriers_modify_color class=$carriers_modify_class width=145><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\"> Modify Carrier </a></span></td>\n";
            $active_colspan=1;
            if ($SUB==2) {
                if ($LOG['ast_admin_access'] == 1) {
                    $active_colspan++;
                    echo "              <td align=center bgcolor=$carriers_delete_color class=$carriers_delete_class width=145><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5carrier&SUB=2&carrier_id=$carrier_id\" class=alert> Delete Carrier </a></span></td>\n";
                }
                $active_colspan++;
                echo "              <td align=center bgcolor=$carriers_adddid_color class=$carriers_adddid_class width=145><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=4&carrier_id=$carrier_id\"> Add New DID </a></span></td>\n";
            } elseif ($SUB==3) {
                $active_colspan++;
                echo "              <td align=center bgcolor=$carriers_modifyserver_color class=$carriers_modifyserver_class width=170><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=3&carrier_server_ip=$carrier_server_ip&carrier_id=$carrier_id\"> Modify Carrier Option </a></span></td>\n";
            } elseif ($SUB==4) {
                if ($ADD=='1carrier') {
                    $active_colspan++;
                    echo "              <td align=center bgcolor=$carriers_adddid_color class=$carriers_adddid_class width=145><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=4&carrier_id=$carrier_id\"> Add New DID </a></span></td>\n";
                } else {
                    $active_colspan++;
                    echo "              <td align=center bgcolor=$carriers_modifydid_color class=$carriers_modifydid_class width=150><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=4&did_id=$did_id&did=$did&carrier_id=$carrier_id\"> Modify DID </a></span></td>\n";
                    if ($LOG['ast_admin_access'] == 1) {
                        $active_colspan++;
                        echo "              <td align=center bgcolor=$carriers_deletedid_color class=$carriers_deletedid_class width=150><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5carrier&SUB=4&did_id=$did_id&did=$did&carrier_id=$carrier_id\" class=alert> Delete DID </a></span></td>\n";
                    }
                }
            }
            if ($SUB==3 or $SUB==4) {
                $active_colspan++;
                echo "                  <td align=center bgcolor=$carriers_return_color class=$carriers_return_class width=150><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\">Return To Carrier</a></span></td>";
            }
            echo "              <td bgcolor=$inactivemenu4_color class='rounded-menu3' colspan=".(7-$active_colspan).">&nbsp;</td>";
            echo "            </tr>";
            echo "          </table>";
            echo "        </td>";
            echo "      </tr>";
        }
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



### Phones Sub-Menu.
    if ($phones_sh > 0) { 
        if (OSDpreg_match('/^(10000000000|61111111111)$/',$ADD)) {
            $phones_show_color=$activemenu3_color;
            $phones_show_class='rounded-menu3select';
        } else {
            $phones_show_color=$inactivemenu3_color;
            $phones_show_class='rounded-menu3';
        }
        if ($ADD==11111111111) {
            $phones_add_color=$activemenu3_color;
            $phones_add_class='rounded-menu3select';
        } else {
            $phones_add_color=$inactivemenu3_color;
            $phones_add_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(21111111111|31111111111|41111111111)$/',$ADD) and OSDstrlen($extension) > 0) {
            $phones_modify_color=$activemenu4_color;
            $phones_modify_class='rounded-menu4select';
        } else {
            $phones_modify_color=$inactivemenu4_color;
            $phones_modify_class='rounded-menu4';
        }
        if ($ADD==51111111111 and OSDstrlen($extension) > 0) {
            $phones_delete_color=$activemenu4_color;
            $phones_delete_class='rounded-menu4select';
        } else {
            $phones_delete_color=$inactivemenu4_color;
            $phones_delete_class='rounded-menu4';
        }
        $phones_stats_color=$inactivemenu4_color;
        $phones_stats_class='rounded-menu4';
        $phones_vmail_color=$inactivemenu4_color;
        $phones_vmail_class='rounded-menu4';
        echo "  <tr class='no-ul'>\n";
        echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>\n";
        echo "        <tr align=center class='no-ul' height=25>\n";
        echo "          <td width=6></td>\n";
        echo "          <td class=$phones_show_class align=center bgcolor=$phones_show_color width=175><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=10000000000\"> Show Phones </a></span></td>\n";
        echo "          <td class=$phones_add_class align=center bgcolor=$phones_add_color width=175><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=11111111111\"> Add A New Phone </a></span></td>\n";
        echo "          <td class=$phones_vmail_class align=center bgcolor=$phones_vmail_color width=175><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=10000000000?#VMList\">Show VoiceMail List</a></span></td>\n";
        echo "          <td bgcolor=$inactivemenu3_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
        echo "          <td width=6></td>\n";
        echo "        </tr>\n";
        if (OSDpreg_match('/^(21111111111|31111111111|41111111111|51111111111)$/',$ADD) and OSDstrlen($extension) > 0) {
            echo "      <tr>";
            echo "        <td colspan=$settings_menucols2>";
            echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
            echo "            <tr class=no-ul height=25>";
            echo "              <td align=center bgcolor=$phones_modify_color class=$phones_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31111111111&extension=$extension&server_ip=$server_ip\"> Modify Phone </a></span></td>\n";
            if ($LOG['ast_delete_phones'] > 0) {
                echo "              <td align=center bgcolor=$phones_delete_color class=$phones_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=51111111111&extension=$extension&server_ip=$server_ip\"> Delete Phone </a></span></td>\n";
            }
            echo "              <td align=center bgcolor=$phones_stats_color class=$phones_stats_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=999999&SUB=31&phone_extension=$row[0]&phone_server_ip=$row[5]'\">Phone Stats</a></span>";
            echo "              <td bgcolor=$inactivemenu4_color class='rounded-menu3' colspan=4>&nbsp;</td>";
            echo "            </tr>";
            echo "          </table>";
            echo "        </td>";
            echo "      </tr>";
        }
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }


### Conferences Sub-Menu.
    if ($conference_sh > 0) { 
        if (OSDpreg_match('/^(1000000000000|6111111111111)$/',$ADD)) {
            $conference_show_color=$activemenu3_color;
            $conference_show_class='rounded-menu3select';
        } else {
            $conference_show_color=$inactivemenu3_color;
            $conference_show_class='rounded-menu3';
        }
        if ($ADD==1111111111111) {
            $phones_add_color=$activemenu3_color;
            $phones_add_class='rounded-menu3select';
        } else {
            $phones_add_color=$inactivemenu3_color;
            $phones_add_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(10000000000000|61111111111111)$/',$ADD)) {
            $conference_showosd_color=$activemenu3_color;
            $conference_showosd_class='rounded-menu3select';
        } else {
            $conference_showosd_color=$inactivemenu3_color;
            $conference_showosd_class='rounded-menu3';
        }
        if ($ADD==11111111111111) {
            $conference_addosd_color=$activemenu3_color;
            $conference_addosd_class='rounded-menu3select';
        } else {
            $conference_addosd_color=$inactivemenu3_color;
            $conference_addosd_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(2111111111111|3111111111111|4111111111111)$/',$ADD)) {
            $conference_modify_color=$activemenu4_color;
            $conference_modify_class='rounded-menu4select';
        } else {
            $conference_modify_color=$inactivemenu4_color;
            $conference_modify_class='rounded-menu4';
        }
        if (OSDpreg_match('/^(21111111111111|31111111111111|41111111111111)$/',$ADD)) {
            $conference_modifyosd_color=$activemenu4_color;
            $conference_modifyosd_class='rounded-menu4select';
        } else {
            $conference_modifyosd_color=$inactivemenu4_color;
            $conference_modifyosd_class='rounded-menu4';
        }
        if ($ADD==5111111111111) {
            $conference_delete_color=$activemenu4_color;
            $conference_delete_class='rounded-menu4select';
        } else {
            $conference_delete_color=$inactivemenu4_color;
            $conference_delete_class='rounded-menu4';
        }
        if ($ADD==51111111111111) {
            $conference_deleteosd_color=$activemenu4_color;
            $conference_deleteosd_class='rounded-menu4select';
        } else {
            $conference_deleteosd_color=$inactivemenu4_color;
            $conference_deleteosd_class='rounded-menu4';
        }
        echo "  <tr class='no-ul'>\n";
        echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>\n";
        echo "        <tr align=center class='no-ul' height=25>\n";
        echo "          <td width=6></td>\n";
        echo "          <td align=center bgcolor=$conference_show_color class=$conference_show_class ><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=1000000000000\"> Show Conferences </a></span></td>";
        echo "          <td align=center bgcolor=$phones_add_color class=$phones_add_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=1111111111111\"> Add A New Conference </a></span></td>";
        echo "          <td align=center bgcolor=$conference_showosd_color class=$conference_showosd_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=10000000000000\"> Show $t1 Conferences </a></span></td>";
        echo "          <td align=center bgcolor=$conference_addosd_color class=$conference_addosd_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=11111111111111\"> Add A New $t1 Conference </a></span></td>";
        echo "          <td bgcolor=$inactivemenu3_color class='rounded-menu3' colspan=2 width=200>&nbsp;</td>";
        echo "          <td width=6></td>\n";
        echo "        </tr>\n";
        if (OSDpreg_match('/^(2111111111111|3111111111111|4111111111111|5111111111111)$/',$ADD) and OSDstrlen($conf_exten) > 0) {
            echo "        <tr class=no-ul>";
            echo "          <td colspan=8 height=25>";
            echo "            <table cellpadding=0 cellspacing=0 border=0 width=100%>";
            echo "              <tr align=center class='no-ul' height=27>";
            echo "                <td align=center bgcolor=$conference_modify_color class=$conference_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$conf_exten&server_ip=$server_ip\"> Modify Conference </a></span></td>\n";
            if ($LOG['ast_delete_phones'] > 0) {
                echo "        <td align=center bgcolor=$conference_delete_color class=$conference_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5111111111111&conf_exten=$conf_exten&server_ip=$server_ip\"> Delete Conference </a></span></td>\n";
            } else {
                echo "                <td align=center bgcolor=$conference_delete_color class=$conference_delete_class colspan=2><span class=\"font2 alert\">Not Allowed</span></td>\n";
            }
            echo "                <td bgcolor=$inactivemenu4_color class='rounded-menu2' Xcolspan=4>&nbsp;</td>";
            echo "              </tr>";
            echo "            </table>";
            echo "          </td>";
        }
        if (OSDpreg_match('/^(21111111111111|31111111111111|41111111111111|51111111111111)$/',$ADD) and OSDstrlen($conf_exten) > 0) {
            echo "        <tr class=no-ul>";
            echo "          <td colspan=8 height=25>";
            echo "            <table cellpadding=0 cellspacing=0 border=0 width=100%>";
            echo "              <tr align=center class='no-ul' height=27>";
            echo "                <td align=center bgcolor=$conference_modifyosd_color class=$conference_modifyosd_class width=200><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$conf_exten&server_ip=$server_ip\"> Modify OSDial Conference </a></span></td>\n";
            if ($LOG['ast_delete_phones'] > 0) {
                echo "        <td align=center bgcolor=$conference_deleteosd_color class=$conference_deleteosd_class width=200><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5111111111111&conf_exten=$conf_exten&server_ip=$server_ip\"> Delete OSDial Conference </a></span></td>\n";
            } else {
                echo "                <td align=center bgcolor=$conference_deleteosd_color class=$conference_deleteosd_class width=175><span class=\"font2 alert\">Not Allowed</span></td>\n";
            }
            echo "                <td bgcolor=$inactivemenu4_color class='rounded-menu3' Xcolspan=4>&nbsp;</td>";
            echo "              </tr>";
            echo "            </table>";
            echo "          </td>";
        }
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }


### Call-times Sub-Menu.
    if ($times_sh > 0) { 
        if (OSDpreg_match('/^(100000000|611111111)$/',$ADD)) {
            $times_show_color=$activemenu3_color;
            $times_show_class='rounded-menu3select';
        } else {
            $times_show_color=$inactivemenu3_color;
            $times_show_class='rounded-menu3';
        }
        if ($ADD==111111111) {
            $times_add_color=$activemenu3_color;
            $times_add_class='rounded-menu3select';
        } else {
            $times_add_color=$inactivemenu3_color;
            $times_add_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(1000000000|6111111111)$/',$ADD)) {
            $times_showstate_color=$activemenu3_color;
            $times_showstate_class='rounded-menu3select';
        } else {
            $times_showstate_color=$inactivemenu3_color;
            $times_showstate_class='rounded-menu3';
        }
        if ($ADD==1111111111) {
            $times_addstate_color=$activemenu3_color;
            $times_addstate_class='rounded-menu3select';
        } else {
            $times_addstate_color=$inactivemenu3_color;
            $times_addstate_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(211111111|311111111|411111111|321111111)$/',$ADD) and OSDstrlen($call_time_id) > 0) {
            $times_modify_color=$activemenu4_color;
            $times_modify_class='rounded-menu4select';
        } else {
            $times_modify_color=$inactivemenu4_color;
            $times_modify_class='rounded-menu4';
        }
        if ($ADD==511111111 and OSDstrlen($call_time_id) > 0) {
            $times_delete_color=$activemenu4_color;
            $times_delete_class='rounded-menu4select';
        } else {
            $times_delete_color=$inactivemenu4_color;
            $times_delete_class='rounded-menu4';
        }
        if (OSDpreg_match('/^(2111111111|3111111111|4111111111)$/',$ADD) and OSDstrlen($call_time_id) > 0) {
            $times_modifystate_color=$activemenu4_color;
            $times_modifystate_class='rounded-menu4select';
        } else {
            $times_modifystate_color=$inactivemenu4_color;
            $times_modifystate_class='rounded-menu4';
        }
        if ($ADD==5111111111 and OSDstrlen($call_time_id) > 0) {
            $times_deletestate_color=$activemenu4_color;
            $times_deletestate_class='rounded-menu4select';
        } else {
            $times_deletestate_color=$inactivemenu4_color;
            $times_deletestate_class='rounded-menu4';
        }
        echo "  <tr class='no-ul'>\n";
        echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>\n";
        echo "        <tr align=center class='no-ul' height=25>\n";
        echo "          <td width=6></td>\n";
        echo "          <td align=center bgcolor=$times_show_color class=$times_show_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=100000000\"> Show Call Times </a></span></td>\n";
        echo "          <td align=center bgcolor=$times_add_color class=$times_add_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=111111111\"> Add A New Call Time </a></span></td>\n";
        echo "          <td align=center bgcolor=$times_showstate_color class=$times_showstate_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=1000000000\"> Show State Call Times </a></span></td>\n";
        echo "          <td align=center bgcolor=$times_addstate_color class=$times_addstate_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=1111111111\"> Add A New State Call Time </a></span></td>\n";
        echo "          <td bgcolor=$inactivemenu3_color class='rounded-menu3' colspan=2 width=200>&nbsp;</td>";
        echo "          <td width=6></td>\n";
        echo "  </tr>\n";
        if (OSDpreg_match('/^(211111111|311111111|411111111|511111111|321111111)$/',$ADD) and OSDstrlen($call_time_id) > 0) {
            echo "      <tr>";
            echo "        <td colspan=$settings_menucols2>";
            echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
            echo "            <tr class=no-ul height=25>";
            echo "              <td align=center bgcolor=$times_modify_color class=$times_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$call_time_id\"> Modify Call Time </a></span></td>\n";
            if ($LOG['delete_call_times'] > 0) {
                echo "              <td align=center bgcolor=$times_delete_color class=$times_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=511111111&call_time_id=$call_time_id\"> Delete Call Time </a></span></td>\n";
            }
            echo "              <td bgcolor=$inactivemenu4_color class='rounded-menu3' colspan=4>&nbsp;</td>";
            echo "            </tr>";
            echo "          </table>";
            echo "        </td>";
            echo "      </tr>";
        }
        if (OSDpreg_match('/^(2111111111|3111111111|4111111111|5111111111)$/',$ADD) and OSDstrlen($call_time_id) > 0) {
            echo "      <tr>";
            echo "        <td colspan=$settings_menucols2>";
            echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
            echo "            <tr class=no-ul height=25>";
            echo "              <td align=center bgcolor=$times_modifystate_color class=$times_modifystate_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3111111111&call_time_id=$call_time_id\"> Modify State Call Time </a></span></td>\n";
            if ($LOG['delete_call_times'] > 0) {
                echo "              <td align=center bgcolor=$times_deletestate_color class=$times_deletestate_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=5111111111&call_time_id=$call_time_id\"> Delete State Call Time </a></span></td>\n";
            }
            echo "              <td bgcolor=$inactivemenu4_color class='rounded-menu3' colspan=4>&nbsp;</td>";
            echo "            </tr>";
            echo "          </table>";
            echo "        </td>";
            echo "      </tr>";
        }
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    } 



### Media Sub-Menu.
    if ($media_sh > 0) { 
        if (OSDpreg_match('/^(10media|61media)$/',$ADD)) {
            $media_show_color=$activemenu3_color;
            $media_show_class='rounded-menu3select';
        } else {
            $media_show_color=$inactivemenu3_color;
            $media_show_class='rounded-menu3';
        }
        if ($ADD=='11media') {
            $media_add_color=$activemenu3_color;
            $media_add_class='rounded-menu3select';
        } else {
            $media_add_color=$inactivemenu3_color;
            $media_add_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(10tts|61tts)$/',$ADD)) {
            $media_showtts_color=$activemenu3_color;
            $media_showtts_class='rounded-menu3select';
        } else {
            $media_showtts_color=$inactivemenu3_color;
            $media_showtts_class='rounded-menu3';
        }
        if ($ADD=='11tts') {
            $media_addtts_color=$activemenu3_color;
            $media_addtts_class='rounded-menu3select';
        } else {
            $media_addtts_color=$inactivemenu3_color;
            $media_addtts_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(21media|31media|41media)$/',$ADD) and OSDstrlen($media_id) > 0) {
            $media_modify_color=$activemenu4_color;
            $media_modify_class='rounded-menu4select';
        } else {
            $media_modify_color=$inactivemenu4_color;
            $media_modify_class='rounded-menu4';
        }
        if ($ADD=='51media' and OSDstrlen($media_id) > 0) {
            $media_delete_color=$activemenu4_color;
            $media_delete_class='rounded-menu4select';
        } else {
            $media_delete_color=$inactivemenu4_color;
            $media_delete_class='rounded-menu4';
        }
        if (OSDpreg_match('/^(21tts|31tts|41tts)$/',$ADD) and OSDstrlen($tts_id) > 0) {
            $media_modifytts_color=$activemenu4_color;
            $media_modifytts_class='rounded-menu4select';
        } else {
            $media_modifytts_color=$inactivemenu4_color;
            $media_modifytts_class='rounded-menu4';
        }
        if ($ADD=='51tts' and OSDstrlen($tts_id) > 0) {
            $media_deletetts_color=$activemenu4_color;
            $media_deletetts_class='rounded-menu4select';
        } else {
            $media_deletetts_color=$inactivemenu4_color;
            $media_deletetts_class='rounded-menu4';
        }
        echo "  <tr class='no-ul'>";
        echo "    <td class='narrow-space' bgcolor=$admin_color></td>";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>";
        echo "        <tr align=center class='no-ul' height=25>";
        echo "          <td width=6></td>\n";
        echo "          <td align=center bgcolor=$media_show_color class=$media_show_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=10media\"> Show Media Files </a></span></td>";
        echo "          <td align=center bgcolor=$media_add_color class=$media_add_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=11media\"> Add Media File </a></span></td>";
        echo "          <td align=center bgcolor=$media_showtts_color class=$media_showtts_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=10tts\"> Show TTS Templates </a></span></td>";
        echo "          <td align=center bgcolor=$media_addtts_color class=$media_addtts_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=11tts\"> Add TTS Template </a></span></td>";
        echo "          <td bgcolor=$inactivemenu3_color class=rounded-menu3 width=350>&nbsp;</td>";
        echo "          <td width=6></td>\n";
        echo "        </tr>\n";
        if (OSDpreg_match('/^(21media|31media|41media|51media)$/',$ADD) and OSDstrlen($media_id) > 0) {
            echo "      <tr>";
            echo "        <td colspan=$settings_menucols2>";
            echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
            echo "            <tr class=no-ul height=25>";
            echo "              <td align=center bgcolor=$media_modify_color class=$media_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31media&media_id=$media_id\"> Modify Media </a></span></td>";
            if ($LOG['modify_servers']>0) {
                echo "              <td align=center bgcolor=$media_delete_color class=$media_delete_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=51media&media_id=$media_id\"> Delete Media </a></span></td>";
            }
            echo "              <td bgcolor=$inactivemenu4_color class='rounded-menu3'>&nbsp;</td>";
            echo "            </tr>";
            echo "          </table>";
            echo "        </td>";
            echo "      </tr>";
        }
        if (OSDpreg_match('/^(21tts|31tts|41tts|51tts)$/',$ADD) and OSDstrlen($tts_id) > 0) {
            echo "      <tr>";
            echo "        <td colspan=$settings_menucols2>";
            echo "          <table cellpadding=0 cellspacing=0 width=100% border=0>";
            echo "            <tr class=no-ul height=25>";
            echo "              <td align=center bgcolor=$media_modifytts_color class=$media_modifytts_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31tts&tts_id=$tts_id\"> Modify TTS Template </a></span></td>";
            if ($LOG['modify_servers']>0) {
                echo "              <td align=center bgcolor=$media_deletetts_color class=$media_deletetts_class width=175><span class=\"font2 alert\"><a href=\"$PHP_SELF?ADD=51tts&tts_id=$tts_id\"> Delete TTS Template </a></span></td>";
            }
            echo "              <td bgcolor=$inactivemenu4_color class='rounded-menu3'>&nbsp;</td>";
            echo "            </tr>";
            echo "          </table>";
            echo "        </td>";
            echo "      </tr>";
        }
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



### Statuses Sub-Menu.
    if ($status_sh > 0) { 
        if (OSDpreg_match('/^(221111111111111|321111111111111|421111111111111)$/',$ADD)) {
            $status_show_color=$activemenu3_color;
            $status_show_class='rounded-menu3select';
        } else {
            $status_show_color=$inactivemenu3_color;
            $status_show_class='rounded-menu3';
        }
        if (OSDpreg_match('/^(231111111111111|331111111111111|431111111111111)$/',$ADD)) {
            $status_showcat_color=$activemenu3_color;
            $status_showcat_class='rounded-menu3select';
        } else {
            $status_showcat_color=$inactivemenu3_color;
            $status_showcat_class='rounded-menu3';
        }
        echo "  <tr class='no-ul'>";
        echo "    <td class='narrow-space' bgcolor=$admin_color></td>";
        echo "      <table width=100% cellpadding=0 cellspacing=0 border=0>";
        echo "        <tr align=center class='no-ul' height=25>";
        echo "          <td width=6></td>\n";
        echo "          <td align=center bgcolor=$status_show_color class=$status_show_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=321111111111111\"> System Statuses </a></span></td>";
        echo "          <td align=center bgcolor=$status_showcat_color class=$status_showcat_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=331111111111111\"> Status Categories </a></span></td>";
        echo "          <td bgcolor=$inactivemenu2_color class='rounded-menu3' width=650>&nbsp;</td>";
        echo "          <td width=6></td>\n";
        echo "        </tr>";
        echo "      </table>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }

    echo "</table></td></tr>"; // END 2'nd table, leave first for following apps.


} elseif ($admin_hh > 0 and $LOG['ast_admin_access']<1) { 
### Do nothing if admin has no permissions
    $ADD='99999999999999999999';
    echo "  <tr class='no-ul' bgcolor=$inactivemenu_color>\n";
    echo "    <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
    echo "    <td height=20 align=left colspan=$settings_menucols2>\n";
    echo "      <span class=fgred>You are not authorized to view this page.</span>\n";
    echo "    </td>\n";
    echo "    <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
    echo "  </tr>\n";
} 




echo "</table>\n";
echo "</div>";
######################### HTML HEADER END #######################################



?>
