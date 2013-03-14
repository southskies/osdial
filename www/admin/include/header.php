<?php
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
            $oacjs="setTimeout(function() { refreshOAC('$PHP_SELF','".urlencode("useOAC=$useOAC&OAC=$useOAC&ADD=$ADD&SUB=$SUB&RR=$RR&DB=$DB&adastats=$adastats$metadetail")."',".($RR*1000)."); }, ".($RR*1000).");";
        } else {
            echo "  <meta http-equiv=Refresh content=\"$RR; URL=$PHP_SELF?ADD=$ADD&SUB=$SUB&RR=$RR&DB=$DB&adastats=$adastats$metadetail\">\n";
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

echo "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onload=\"updateClock(); setInterval('updateClock()', 1000 ); $oacjs\" onunload=\"stop()\">\n";

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
// $users_color='#C6D9DE';
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
	$ingroups_menu1_class='';
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
} /*else {
	$reports_hh=''; 
	$reports_fc='WHITE'; 
	$reports_bold="$header_nonselected_bold";
	$reports_menu1_class='rounded-rmenu1';
	$reports_heading_bgcolor=''; 
} */

$settings_menucols2=12;
$settings_menucols1=10;

echo "<div class=container>";

echo "<div class=header>";
echo "<table width=900 Oldwidth$page_width bgcolor=$maintable_color cellpadding=0 cellspacing=0 align=center class=across border=0>\n";
echo "  <tr>\n";
echo "    <td colspan=$settings_menucols2>\n";
echo "      <table align='center' border='0' cellspacing='0' cellpadding='0'>\n";
echo "        <tr>    <!-- First draw the top row  -->\n";
echo "          <td class='across-top' width='15'><img src='templates/".$config['settings']['admin_template']."/images/topleft.png' width='15' height='16' align='left'></td>\n";
echo "          <td class='across-top' align='center'></td>\n";
echo "          <td class='across-top' width='15'><img src='templates/".$config['settings']['admin_template']."/images/topright.png' width='15' height='16' align='right'></td>\n";
echo "        </tr>\n";
echo "        <tr valign='top'>\n";
echo "          <td align=left width=33%>\n";
echo "              <span class=\"font2 fgwhite\">&nbsp;&nbsp;</font><B><a href=\"".$config['settings']['admin_home_url']."\"><span class=\"font1 fghome\">HOME</span></a><span class=\"font2 fgdefault\">&nbsp;|&nbsp;</span><a href=\"$PHP_SELF?force_logout=1\"><span class=\"font1 fglogout\">Logout</span></a><br /><br />\n";
// echo "			&nbsp;&nbsp;<font size=2>Credit Left:</font><font size=2 color=#060> 5 days</font>";
// echo "			&nbsp;&nbsp;<font size=2>Credit Left:</font><font size=2 color=#600> 1 day</font>";
echo "          </td>\n";
echo "          <td class='user-company' align=center width=33%>\n";
echo "              <span class=fgcompany>".$config['settings']['company_name']."</span><br />\n";
echo "              <span class=\"font2 fgheader\"><b><br>$t1 Administrator</b><br><br><br></font>\n";
echo "          </td>\n";
echo "          <td align=right width=33%>";
echo "              <span class=\"font2 fgclock\">" . date("l F j, Y") . "&nbsp;&nbsp;</span><br>";
echo "              <div style=\"width: 10em; text-align: right; margin: 5px;\"><div id=\"clock\" style=\"color: $clock_color\"></div></div>";
echo "          </td>\n";
echo "        </tr>\n";
echo "      </table>\n";
echo "    </td>\n";
echo "  </tr>\n";



# BEGIN main menu.
$height_row1=20;
$cauth=0;
$mmenu = '';

# Start piece
$mmenu .= "<tr class=no-ul><td class='narrow-space' width=12>&nbsp;</td>";

$mmenu .= "    <td class=$agents_menu1_class height=$height_row1 align=center $users_hh><a href=\"$PHP_SELF?ADD=0\"><span class=\"font3 fgnavy\"> Agents </span></a></td>\n";
$mmenu .= "    <td class=$campaigns_menu1_class height=$height_row1 align=center $campaigns_hh width=100><a href=\"$PHP_SELF?ADD=10\"><span class=\"font3 fgnavy\"> Campaigns </span></a></td>\n";
$mmenu .= "    <td class=$lists_menu1_class height=$height_row1 align=center $lists_hh width=65><a href=\"$PHP_SELF?ADD=100\"><span class=\"font3 fgnavy\"> Lists </span></a></td>\n";

if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_scripts'] == 1) {
    $mmenu .= "    <td class=$scripts_menu1_class height=$height_row1 align=center $scripts_hh><a href=\"$PHP_SELF?ADD=1000000\"><span class=\"font3 fgnavy\"> Scripts </span></a></td>\n";
} else {
    $cauth++;
}

if (($config['settings']['enable_filters'] > 0) and ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_filters'] == 1)) {
    $mmenu .= "    <td class=$filters_menu1_class height=$height_row1 align=center $filters_hh><a href=\"$PHP_SELF?ADD=10000000\"><span class=\"font3 fgnavy\"> Filters </span></a></td>\n";
} else {
    $cauth++;
}

if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_ingroups'] == 1) {
    $mmenu .= "    <td class=$ingroups_menu1_class height=$height_row1 align=center $ingroups_hh><a href=\"$PHP_SELF?ADD=1000\"><span class=\"font3 fgnavy\"> In-Groups </span></a></td>\n";
} else {
    $cauth++;
}

$mmenu .= "    <td class=$usergroups_menu1_class height=$height_row1 align=center $usergroups_hh width=110><a href=\"$PHP_SELF?ADD=100000\"><span class=\"font3 fgnavy\">User Groups</span></a></td>\n";

if (($config['settings']['enable_external_agents'] > 0) and ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_external_agents'] == 1)) {
    $mmenu .= "    <td class=$remoteagents_menu1_class height=$height_row1 align=center $remoteagent_hh width=130><a href=\"$PHP_SELF?ADD=10000\"><span class=\"font3 fgnavy\">External Agents</span></a></td>\n";
} else {
    $cauth++;
}

if ($LOG['view_reports'] > 0) {
    $mmenu .= "    <td $reports_heading_bgcolorX class=$reports_menu1_class height=$height_row1 align=center $reports_hh><a href=\"$PHP_SELF?ADD=999999\"><span class=\"font3 fgnavy\"> Reports </span></a></td>\n";
} else {
    $cauth++;
}

if ($LOG['user_level'] > 8 and ($LOG['multicomp_user'] == 0 or ($LOG['company']['enable_system_calltimes'] + $LOG['company']['enable_system_phones'] + $LOG['company']['enable_system_conferences'] + $LOG['company']['enable_system_servers'] + $LOG['company']['enable_system_statuses']) != 0)) {
    $mmenu .= "    <td class=$setup_menu1_class height=$height_row1 align=center $admin_hh><a href=\"$PHP_SELF?ADD=10000000000\"><span class=\"font3 fgnavy\"> Setup </span></a></td>\n";
} else {
    $cauth++;
}

# End piece
$mmenu .= "<td>&nbsp;</td>";

if ($cauth) {
    if ($cauth > 1) {
        $mmenu = "    <td height=$height_row1 colspan=" . ($cauth - round($cauth/2)) . " align=center>&nbsp;</td>\n" . $mmenu;
    }
    $mmenu .= "    <td class=rounded-menu1 height=$height_row1 colspan=" . (round($cauth/2)) . " align=center>&nbsp;</td>\n";
}

echo "  <tr class='no-ul'>\n";
echo $mmenu;
echo "  </tr>\n";
# END Main Menu

#echo "<tr>($cauth)</tr>";


### Agents Menu
if (OSDstrlen($users_hh) > 1) { 
	if ($ADD == 0) {
		$agent_show_color=$campaigns_color;
		$agent_show_class='rounded-menu2select';
	} else {
		$agent_show_color=$settings_color;
		$agent_show_class='rounded-menu2';
	}
	if ($ADD == '1') {
		$agent_add_color=$campaigns_color;
		$agent_add_class='rounded-menu2select';
	} else {
		//$camp_add_color=$subcamp_color;$fgfont_add='fgdefault';
		$agent_add_color=$settings_color;
		$agent_add_class='rounded-menu2';
	}
	if ($ADD == '1A')  {
		$agent_copy_color=$campaigns_color;
		$agent_copy_class='rounded-menu2select';
	} else {
		$agent_copy_color=$settings_color;
		$agent_copy_class='rounded-menu2';
	}
	if ($ADD == 550)  {
		$agent_search_color=$campaigns_color;
		$agent_search_class='rounded-menu2select';
	} else {
		$agent_search_color=$settings_color;
		$agent_search_class='rounded-menu2';
	}
	if ($ADD == 9) {
		$agent_lead_color=$campaigns_color;
		$agent_lead_class='rounded-menu2select';
	} else {
		$agent_lead_color=$settings_color;
		$agent_lead_class='rounded-menu2';
	}
	if ($ADD == 999999 and $SUB==21) {
		$agent_stats_color=$settings_color;
		$agent_stats_class='rounded-menu2';
	} else {
		$agent_stats_color=$settings_color;
		$agent_stats_class='rounded-menu2';
	}
	if ($ADD == 999999 and $SUB==22) {
		$agent_status_color=$campaigns_color;
		$agent_status_class='rounded-menu2';
	} else {
		$agent_status_color=$settings_color;
		$agent_status_class='rounded-menu2';
	}
	if ($ADD == 999999 and $SUB==20) {
		$agent_time_color=$campaigns_color;
		$agent_time_class='rounded-menu2';
	} else {
		$agent_time_color=$settings_color;
		$agent_time_class='rounded-menu2';
	}
	if ($ADD == 8) {
		$agent_callbk_color=$campaigns_color;
		$agent_callbk_class='rounded-menu2select';
	} else {
		$agent_callbk_color=$settings_color;
		$agent_callbk_class='rounded-menu2';
	}

	echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=$users_color height=22>\n";
	echo "    <td bgcolor=#E2EBEE class='narrow-space'>&nbsp;</td>";
    //echo "    <td align=left Xcolspan=$settings_menucols2 height=20>\n";
    echo "      <span class=\"font2 fgblack\">";
    echo "        <td class=$agent_show_class align=center bgcolor=$agent_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF\"> Show Agents </a></span></td>";
    echo "        <td class=$agent_add_class align=center bgcolor=$agent_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1\"> Add A New Agent </a></span></td>";
    echo "        <td class=$agent_copy_class align=center bgcolor=$agent_copy_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1A\"> Copy Agent </a></span></td>";
    echo "        <td class=$agent_search_class align=center bgcolor=$agent_search_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=550\"> Search For An Agent </a></span></td>";
    if ($config['settings']['enable_lead_allocation'] > 0) {
        echo "        <td class=$agent_lead_class align=center bgcolor=$agent_lead_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=9\"> Lead Allocation </a></span></td>";
    } else {
        echo "        <td bgcolor=$agent_lead_color>&nbsp;</td>\n";
    }
    if ($user != "" and $ADD!=550 and $ADD!=660) {
        //echo "        | &nbsp; \n";
        if ($LOG['view_agent_stats']) {
			echo "        <td class=$agent_stats_class align=center bgcolor=$agent_stats_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$user\"> Stats </a></span></td>";
		} else {
			echo "<td bgcolor=$users_color>&nbsp;</td>";
		}
        if ($LOG['view_agent_status']) {
			echo "        <td class=$agent_status_class align=center bgcolor=$agent_status_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$user\"> Status </a></span></td>";
		} else {
			echo "<td bgcolor=$users_color>&nbsp;</td>";
		}
        if ($LOG['view_agent_timesheet']) {
			echo "        <td class=$agent_time_class align=center bgcolor=$agent_time_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=20&agent=$user\"> Time </a></span></td>";
		} else {
			echo "<td bgcolor=$users_color>&nbsp;</td>";
		}
		echo "        <td class=$agent_callbk_class align=center bgcolor=$agent_callbk_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=8&user=$user\"> Callbacks </a></span></td> \n";
		echo "<td bgcolor=#E2EBEE $users_color1 colspan=1>&nbsp;</td>";
		
    } else {
// 		echo "<td bgcolor=#E2EBEE $users_color colspan=4 width=200>&nbsp;</td>";
		echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=2 width=100>&nbsp;</td>";
    }
    echo "<td bgcolor=#E2EBEE $users_color1 width=5>&nbsp;</td>";
    echo "      </span>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table></tr>";
} 

// echo " campaigns_hh =$campaigns_hh - ";

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
    
    /*
    $cauth=0;
    echo "  <tr class='no-ul' bgcolor=$campaigns_color>\n";
	echo "<td class='narrow-space'>&nbsp;</td>";
    echo "    <td height=20 align=center $list_sh colspan=2><a href=\"$PHP_SELF?ADD=10\"><span class=\"font2 $list_fc\"> Campaigns Main </span></a></td>\n";
    echo "    <td align=center $status_sh colspan=1><a href=\"$PHP_SELF?ADD=32\"><span class=\"font2 $status_fc\"> Statuses </span></a></td>\n";
    echo "    <td align=center $hotkey_sh colspan=1><a href=\"$PHP_SELF?ADD=33\"><span class=\"font2 $hotkey_fc\"> HotKeys </span></a></td>\n";
    echo "    <td align=center $recycle_sh colspan=1><a href=\"$PHP_SELF?ADD=35\"><span class=\"font2 $recycle_fc\"> Lead Recycle </span></a></td>\n";
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_campaign_listmix'] == 1) {
        echo "    <td align=center $listmix_sh colspan=1><a href=\"$PHP_SELF?ADD=39\"><span class=\"font2 $listmix_fc\"> List Mix </span></a></td>\n";
    } else {
        $cauth++;
    }
    echo "    <td align=center $autoalt_sh colspan=1><a href=\"$PHP_SELF?ADD=36\"><span class=\"font2 $autoalt_fc\"> Auto-Alt Dial </span></a></td>\n";
    echo "    <td align=center $pause_sh colspan=1><a href=\"$PHP_SELF?ADD=37\"><span class=\"font2 $pause_fc\"> Pause Codes </span></a></td>\n";
    echo "    <td align=center $fields_sh colspan=2><a href=\"$PHP_SELF?ADD=3fields\"><span class=\"font2 $fields_fc\"> Additional Fields </span></a></td>\n";
    
    if ($cauth) {
        echo "      <td colspan=$cauth align=center><span class=font2>&nbsp;</span></td>\n";
    }
	echo "<td class='narrow-space'>&nbsp;</td>";
    echo "  </tr>\n";
*/
// 	echo "list_sh =$list_sh";

    if (OSDstrlen($list_sh) > 1) { 
		if ($ADD == 10) {
			$camp_show_color=$campaigns_color;
			$camp_show_class='rounded-menu2select';
		} else {
			$camp_show_color=$settings_color;
			$camp_show_class='rounded-menu2';
		}
		if ($ADD == 11) {
			$camp_add_color=$campaigns_color;
			$camp_add_class='rounded-menu2select';
		} else {
			//$camp_add_color=$subcamp_color;$fgfont_add='fgdefault';
			$camp_add_color=$settings_color;
			$camp_add_class='rounded-menu2';
		}
		if ($ADD == 12)  {
			$camp_copy_color=$campaigns_color;
			$camp_copy_class='rounded-menu2select';
		} else {
			$camp_copy_color=$settings_color;
			$camp_copy_class='rounded-menu2';
		}
		if ($SUB == 13)  {
			$camp_real_color=$campaigns_color;
			$camp_real_class='rounded-menu2select';
		} else {
			$camp_real_color=$settings_color;
			$camp_real_class='rounded-menu2';
		}
// 		if ($ADD == '3fields') {
		if ($ADD == 71) {
			$camp_addfc_color=$campaigns_color;
			$camp_addfc_class='rounded-menu2select';
		} else {
			$camp_addfc_color=$settings_color;
			$camp_addfc_class='rounded-menu2';
		}
	
		$settings_menucols3=$settings_menucols1 - 5;	// = 5

		echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
        echo "  <tr class='no-ul' bgcolor=#E2EBEE Xbgcolor=$campaigns_color>\n";
		echo "	  <td height=22 class='narrow-space' bgcolor=#E2EBEE width=10>&nbsp;</td>";
        echo "    <td class=$camp_show_class align=center bgcolor=$camp_show_color colspan=2><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=10\">Show Campaigns</a></span></td>\n";
        echo "    <td class=$camp_add_class align=center bgcolor=$camp_add_color colspan=2><span class=\"font2 $fgfont_add\"><a href=\"$PHP_SELF?ADD=11\">Add A New Campaign</a></span></td>\n";
        echo "    <td class=$camp_copy_class align=center bgcolor=$camp_copy_color colspan=2><span class=\"font2 $fgfont_copy\"><a href=\"$PHP_SELF?ADD=12\">Copy Campaign</a></span></td>\n";

        if ($LOG['view_agent_realtime_summary']) {
			echo "    <td class=$camp_real_class align=center bgcolor=$camp_real_color colspan=2><span class=\"font2 $fgfont_real\"><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=13\"> Real-Time Campaigns Summary </a></span></td>\n";
		}
		
// 		echo "    <td align=center bgcolor=$camp_addfc_color colspan=1><span class=\"font2 $fgfont_fc\"><a href=\"$PHP_SELF?ADD=3fields&SUB=2fields\"> Additional Fields </a></span></td>\n";
		echo "    <td class=$camp_addfc_class align=center bgcolor=$camp_addfc_color colspan=1><span class=\"font2 $fgfont_fc\"><a href=\"$PHP_SELF?ADD=71\"> Additional Fields </a></span></td>\n";
        echo "      </span>\n";
        echo "    </td>\n";
		echo "	  <td class='narrow-space' bgcolor=#E2EBEE width=10>&nbsp;</td>";
        echo "  </tr>\n";

        if ($campaign_id != '') {
            # Detailed sub-menu
//             if ($ADD == 34 and $SUB<1) {
// 				$camp_basic_color=$subcamp_color2;
// 			} else {
// 				$camp_basic_color=$admin_color2;
// 			}
			$subcamp_color2=$campaigns_color;
			$admin_color2=$settings_color;
            if ($ADD == 31 and $SUB<1) {
				$camp_modify_color=$campaigns_color;
				$camp_modify_class='rounded-menu3select';
			} else {
				$camp_modify_color=$settings_color;
				$camp_modify_class='rounded-menu3';
			}
            if ($SUB==22) {
				$camp_statuses_color=$campaigns_color;
				$camp_statuses_class='rounded-menu3select';
			} else {
				$camp_statuses_color=$settings_color;
				$camp_statuses_class='rounded-menu3';
			}
            if ($SUB==23) {
				$camp_hotkeys_color=$campaigns_color;
				$camp_hotkeys_class='rounded-menu3select';
			} else {
				$camp_hotkeys_color=$settings_color;
				$camp_hotkeys_class='rounded-menu3';
			}
            if ($SUB==25) {
				$camp_recycle_color=$campaigns_color;
				$camp_recycle_class='rounded-menu3select';
			} else {
				$camp_recycle_color=$settings_color;
				$camp_recycle_class='rounded-menu3';
			}
            if ($SUB==26) {
				$camp_autoalt_color=$subcamp_color2;
				$camp_autoalt_class='rounded-menu3select';
			} else {
				$camp_autoalt_color=$settings_color;
				$camp_autoalt_class='rounded-menu3';
			}
            if ($SUB==27) {
				$camp_pause_color=$campaigns_color;
				$camp_pause_class='rounded-menu3select';
			} else {
				$camp_pause_color=$settings_color;
				$camp_pause_class='rounded-menu3';
			}
            if ($SUB==29) {
				$camp_listmix_color=$campaigns_color;
				$camp_listmix_class='rounded-menu3select';
			} else {
				$camp_listmix_color=$settings_color;
				$camp_listmix_class='rounded-menu3';
			}
            if ($SUB==14) {
				$camp_real_color=$campaigns_color;
				$camp_real_class='rounded-menu3select';
			} else {
				$camp_real_color=$settings_color;
				$camp_real_class='rounded-menu3';
			}
            if ($SUB=="2keys") {
				$camp_oivr_color=$campaigns_color;
				$camp_oivr_class='rounded-menu3select';
			} else {
				$camp_oivr_color=$settings_color;
				$camp_oivr_class='rounded-menu3';
			}
			$camp_realtime_class='rounded-menu3';

            $cauth=0;
#            echo "  <tr class='no-ul' bgcolor=$campaigns_color>\n";
            echo "  <tr class='no-ul' bgcolor=#E2EBEE>\n";
            echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
            echo "      <table width=100% cellpadding=2 cellspacing=0 border=0>\n";
#            echo "        <tr class='no-ul' bgcolor=$campaigns_color>\n";
			echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
            echo "          <td class=$camp_modify_class align=center bgcolor=$camp_modify_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id\">Modify</a></span></td>\n";
            echo "          <td class=$camp_statuses_class align=center bgcolor=$camp_statuses_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaign_id\">Statuses</a></span></td>\n";
            echo "          <td class=$camp_hotkeys_class align=center bgcolor=$camp_hotkeys_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=31&SUB=23&campaign_id=$campaign_id\">HotKeys</span></a></td>\n";
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
            echo "          <td class=$camp_realtime_class align=center bgcolor=$camp_real_color>";
            if ($LOG['view_agent_realtime']) {
				echo "<span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=14&group=$campaign_id&campaign_id=$campaign_id\">Real-Time</a></span>";
			}
            echo "</td>\n";
            if ($cauth) {
                echo "          <td colspan=$cauth align=center bgcolor=$camp_real_color><span class=\"font2 fgnavy\">&nbsp;</span></td>\n";
            }
            echo "        </tr>\n";
            echo "      </table>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "</table></tr>";
        }
    } 
} 



### Lists Menu
if (OSDstrlen($lists_hh) > 1) { 
	if ($ADD == 100) {
		$list_show_color=$campaigns_color;
		$list_show_class='rounded-menu2select';
	} else {
		$list_show_color=$settings_color;
		$list_show_class='rounded-menu2';
	}
	if ($ADD == 111) {
		$list_add_color=$campaigns_color;
		$list_add_class='rounded-menu2select';
	} else {
		$list_add_color=$settings_color;
		$list_add_class='rounded-menu2';
	}
	if ($ADD == 121)  {
		$list_dnc_color=$campaigns_color;
		$list_dnc_class='rounded-menu2select';
	} else {
		$list_dnc_color=$settings_color;
		$list_dnc_class='rounded-menu2';
	}
	if ($ADD == 122) {
		$list_load_color=$campaigns_color;
		$list_load_class='rounded-menu2select';
	} else {
		$list_load_color=$settings_color;
		$list_load_class='rounded-menu2';
	}
	if ($ADD == 131) {
		$list_export_color=$campaigns_color;
		$list_export_class='rounded-menu2select';
	} else {
		$list_export_color=$settings_color;
		$list_export_class='rounded-menu2';
	}
	if ($ADD == 999999 and $SUB==26) {
		$list_search_color=$campaigns_color;
		$list_search_class='rounded-menu2select';
	} else {
		$list_search_color=$settings_color;
		$list_search_class='rounded-menu2';
	}
	
	echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=$lists_color>\n";
    echo "    <td height=20 align=left bgcolor=#E2EBEE colspan=$settings_menucols2>\n";
    echo "      <span class=\"font2 fgdefault\"> &nbsp; \n";
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
    echo "	  <td class='narrow-space' bgcolor=#E2EBEE width=3>&nbsp;</td>";
    echo "      </span>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table></tr>";
} 



### Scripts Menu
if (OSDstrlen($scripts_hh) > 1) { 
	if ($ADD == 1000000) {
		$script_show_color=$campaigns_color;
		$script_show_class='rounded-menu2select';
	} else {
		$script_show_color=$settings_color;
		$script_show_class='rounded-menu2';
	}
	if ($ADD == 1111111) {
		$script_add_color=$campaigns_color;
		$script_add_class='rounded-menu2select';
	} else {
		$script_add_color=$settings_color;
		$script_add_class='rounded-menu2';
	}
	if ($ADD == '0email') {
		$script_email_color=$campaigns_color;
		$script_showemail_class='rounded-menu2select';
	} else {
		$script_email_color=$settings_color;
		$script_showemail_class='rounded-menu2';
	}
	if ($ADD == '1email') {
		$script_addemail_color=$campaigns_color;
		$script_addemail_class='rounded-menu2select';
	} else {
		$script_addemail_color=$settings_color;
		$script_addemail_class='rounded-menu2';
	}
	
	echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=#E2EBEE>\n";
    echo "    <td height=20 bgcolor=#E2EBEE align=left colspan=$settings_menucols2>\n";
    echo "      <span class=\"font2 fgdefault\">&nbsp;\n";
    echo "        <td align=center class=$script_show_class bgcolor=$script_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1000000\"> Show Scripts </a></span></td>\n";
    echo "        <td align=center class=$script_add_class bgcolor=$script_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1111111\"> Add A New Script</a></span></td>\n";
    
    if (file_exists($WeBServeRRooT . '/admin/include/content/scripts/email_templates.php')) {
        echo "        <td align=center class=$script_showemail_class bgcolor=$script_email_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=0email\"> Show Email Templates </a></span></td>\n";
        echo "        <td align=center class=$script_addemail_class bgcolor=$script_addemail_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1email\"> Add A New Email Template </a></span></td>\n";
        echo "		  <td bgcolor=#E2EBEE colspan=2 width=100>&nbsp;</td>";
    } else {
		echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
    }
    echo "      </span>\n";
    echo "		  <td bgcolor=#E2EBEE colspan=4 width=3>&nbsp;</td>";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table></tr>";
} 



### Filters Menu
if (OSDstrlen($filters_hh) > 1) { 
	if ($ADD == 10000000) {
		$filter_show_color=$campaigns_color;
		$filter_show_class='rounded-menu2select';
	} else {
		$filter_show_color=$settings_color;
		$filter_show_class='rounded-menu2';
	}
	if ($ADD == 11111111) {
		$filter_add_color=$campaigns_color;
		$filter_add_class='rounded-menu2select';
	} else {
		$filter_add_color=$settings_color;
		$filter_add_class='rounded-menu2';
	}
	
	echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=$filters_color>\n";
    echo "    <td height=20 align=left bgcolor=#E2EBEE colspan=$settings_menucols2>\n";
    echo "      <span class=\"font2 fgdefault\"> &nbsp; \n";
    echo "        <td align=center bgcolor=$filter_show_color class=$filter_show_class><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=10000000\"> Show Filters </a></span></td>\n";
    echo "        <td align=center bgcolor=$filter_add_color class=$filter_add_class><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=11111111\"> Add A New Filter </a></span></td>\n";
    echo "		  <td bgcolor=$settings_color class=rounded-menu2 colspan=2 width=400>&nbsp;</td>";
    echo "		  <td bgcolor=#E2EBEE width=3>&nbsp;</td>";
    echo "      </span>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table></tr>";
} 



### InGroups Menu
if (OSDstrlen($ingroups_hh) > 1) { 
	if ($ADD == 1000) {
		$ingroup_show_color=$campaigns_color;
		$ingroup_show_class='rounded-menu2select';
	} else {
		$ingroup_show_color=$settings_color;
		$ingroup_show_class='rounded-menu2';
	}
	if ($ADD == 1001) {
		$ingroup_a2a_color=$campaigns_color;
		$ingroup_a2a_class='rounded-menu2select';
	} else {
		$ingroup_a2a_color=$settings_color;
		$ingroup_a2a_class='rounded-menu2';
	}
	if ($ADD == 1111) {
		$ingroup_add_color=$campaigns_color;
		$ingroup_add_class='rounded-menu2select';
	} else {
		$ingroup_add_color=$settings_color;
		$ingroup_add_class='rounded-menu2';
	}
	if ($ADD == 1211) {
		$ingroup_copy_color=$campaigns_color;
		$ingroup_copy_class='rounded-menu2select';
	} else {
		$ingroup_copy_color=$settings_color;
		$ingroup_copy_class='rounded-menu2';
	}
	
	echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=$ingroups_color>\n";
    echo "    <td height=20 align=left bgcolor=#E2EBEE colspan=$settings_menucols2 width=3>\n";
    echo "      <span class=\"font2 fgdefault\"> &nbsp; \n";
    echo "        <td align=center class=$ingroup_show_class bgcolor=$ingroup_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1000&let=''\"> Show In-Groups </a></span></td>\n";
    echo "        <td align=center class=$ingroup_add_class bgcolor=$ingroup_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1111\"> Add A New In-Group </a></span></td>\n";
    echo "        <td align=center class=$ingroup_copy_class bgcolor=$ingroup_copy_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1211\"> Copy In-Group </a></span></td>\n";
    echo "        <td align=center class=$ingroup_a2a_class bgcolor=$ingroup_a2a_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=1001\"> Show Agent2Agent Groups </a></span></td>\n";
    echo "		  <td bgcolor=$settings_color class=rounded-menu2 width=200>&nbsp;</td>";
    echo "      </span>\n";
    echo "	  <td bgcolor=#E2EBEE colspan=4 width=3>&nbsp;</td>";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table></tr>";
} 



### External/Remote Agents Menu
if (OSDstrlen($remoteagent_hh) > 1) { 
	if ($ADD == 10000) {
		$extagents_show_color=$campaigns_color;
		$extagents_show_class='rounded-menu2select';
	} else {
		$extagents_show_color=$settings_color;
		$extagents_show_class='rounded-menu2';
	}
	if ($ADD == 11111) {
		$extagents_add_color=$campaigns_color;
		$extagents_add_class='rounded-menu2select';
	} else {
		$extagents_add_color=$settings_color;
		$extagents_add_class='rounded-menu2';
	}
	
	echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=$remoteagent_color>\n";
    echo "    <td height=20 bgcolor=#E2EBEE align=left colspan=$settings_menucols2 width=3>\n";
    echo "      <span class=\"font2 fgdefault\"> &nbsp; \n";
    echo "        <td class=$extagents_show_class align=center bgcolor=$extagents_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=10000\"> Show External Agents </a></span></td>\n";
    echo "        <td class=$extagents_add_class align=center bgcolor=$extagents_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=11111\"> Add New External Agents </a></span></td>\n";
    echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
    echo "      </span>\n";
    echo "<td bgcolor=#E2EBEE colspan=4 width=3>&nbsp;</td>";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table></tr>";
} 



### Usergroups Menu
if (OSDstrlen($usergroups_hh) > 1) { 
	if ($ADD == 100000) {
		$ugroups_show_color=$campaigns_color;
		$ugroups_show_class='rounded-menu2select';
	} else {
		$ugroups_show_color=$settings_color;
		$ugroups_show_class='rounded-menu2';
	}
	if ($ADD == 111111) {
		$ugroups_add_color=$campaigns_color;
		$ugroups_add_class='rounded-menu2select';
	} else {
		$ugroups_add_color=$settings_color;
		$ugroups_add_class='rounded-menu2';
	}
	if ($ADD == 999999 and $SUB==24 and $group==$LOG[user_group]) {
		$ugroups_report_color=$campaigns_color;
		$ugroups_report_class='rounded-menu2select';
	} else {
		$ugroups_report_color=$settings_color;
		$ugroups_report_class='rounded-menu2';
	}
	
	echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=$usergroups_color>\n";
    echo "    <td height=20 bgcolor=#E2EBEE align=left colspan=$settings_menucols2>\n";
    echo "      <span class=\"font2 fgdefault\"> &nbsp; \n";
    echo "        <td align=center class=$ugroups_show_class bgcolor=$ugroups_show_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=100000\"> Show User Groups </a></span></td>\n";
    echo "        <td align=center class=$ugroups_add_class bgcolor=$ugroups_add_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=111111\"> Add A New User Group </a></span></td>\n";
    if ($LOG['view_usergroup_hourly_stats']) {
		echo "        <td align=center class=$ugroups_report_class bgcolor=$ugroups_report_color><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=999999&SUB=24&group=$LOG[user_group]\"> Group Hourly Report </a></span></td>\n";
	} else {
		echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
	}
	echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
    echo "      </span>\n";
    echo "		  <td bgcolor=#E2EBEE colspan=4 width=3>&nbsp;</td>";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table></tr>";
} 



### Reports Menu
if (OSDstrlen($reports_hh) > 1) { /*
    echo "  <tr bgcolor=#E9E8D9>\n";
    echo "    <td height=20 align=left colspan=$settings_menucols2>\n";
    echo "      <span class=\"font2 fgdefault\">&nbsp;</span>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    */
    $reports_show_color=$campaigns_color;
    $reports_show_class='rounded-menu2select';
    
    echo "<tr><table border=0 cellpadding=0 cellspacing=0 width=100%>";
    echo "  <tr class='no-ul' bgcolor=$filters_color>\n";
    echo "    <td height=20 align=left bgcolor=#E2EBEE colspan=$settings_menucols2>\n";
    echo "      <span class=\"font2 fgdefault\"> &nbsp; \n";
    echo "        <td align=center bgcolor=$reports_show_color class=$reports_show_class><span class=\"font2 $fgfont_show\"><a href=\"$PHP_SELF?ADD=10000000\"> Show Reports </a></span></td>\n";
    echo "		  <td bgcolor=$settings_color class=rounded-menu2 colspan=2 width=400>&nbsp;</td>";
    echo "		  <td bgcolor=#E2EBEE width=3>&nbsp;</td>";
    echo "      </span>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table></tr>";
}



### Admin/Setup menu.
if (OSDstrlen($admin_hh) > 1 and $LOG['ast_admin_access']>0) { 
	if ($ADD == 311111111111111) {
		$ssettings_color=$settings_color;
		$ssettings_class='rounded-menu2select';
		$ssettings_sh=1;
	} else {
		$ssettings_color=$settings_color;
		$ssettings_class='rounded-menu2';
		$ssettings_sh=0;
	}
	if ($ADD == '3carrier' or $SUB>0) {
		$carriers_color=$settings_color;
		$carriers_class='rounded-menu2select';
		$carriers_sh=1;
	} else {
		$carriers_color=$settings_color;
		$carriers_class='rounded-menu2';
		$carriers_sh=0;
	}
	if ($ADD == 100000000000 or $ADD==111111111111 or $ADD==399211111111111 or $ADD==399911111111111 or $ADD==311111111111) {
		$server_color=$settings_color;
		$server_class='rounded-menu2select';
		$server_sh=1;
	} else {
		$server_color=$settings_color;
		$server_class='rounded-menu2';
		$server_sh=0;
	}
	if ($ADD == 10000000000 or $ADD==31111111111 or $ADD==11111111111) {
		$phones_color=$settings_color;
		$phones_class='rounded-menu2select';
		$phones_sh=1;
	} else {
		$phones_color=$settings_color;
		$phones_class='rounded-menu2';
		$phones_sh=0;
	}
	if ($ADD == 1000000000000 or $ADD==1111111111111 or $ADD==10000000000000 or $ADD==11111111111111) {
		$conference_color=$settings_color;
		$conference_class='rounded-menu2select';
		$conference_sh=1;
	} else {
		$conference_color=$settings_color;
		$conference_class='rounded-menu2';
		$conference_sh=0;
	}
	if ($ADD == 100000000 or $ADD==111111111 or $ADD==1000000000 or $ADD==1111111111 or $ADD==311111111) {
		$times_color=$settings_color;
		$times_class='rounded-menu2select';
		$times_sh=1;
	} else {
		$times_color=$settings_color;
		$times_class='rounded-menu2';
		$times_sh=0;
	}
	if ($ADD == '10media') {
		$media_color=$settings_color;
		$media_class='rounded-menu2select';
		$media_sh=1;
	} else {
		$media_color=$settings_color;
		$media_class='rounded-menu2';
		$media_sh=0;
	}
	if ($ADD == 321111111111111) {
		$status_color=$settings_color;
		$status_class='rounded-menu2select';
		$status_sh=1;
	} else {
		$status_color=$settings_color;
		$status_class='rounded-menu2';
		$status_sh=0;
	}
	
	

    $amenu = '';
    $acnt = 0;

    $amenu .= "<tr><table bgcolor=$campaigns_color border=0 class=no-ul cellpadding=0 cellspacing=0 width=100%><tr>";
	$amenu .= "<td bgcolor=#E2EBEE width=5>&nbsp;</td>";
	
    if ($LOG['multicomp_user'] == 0) {
        $amenu .= "    <td height=20 align=center bgcolor=$ssettings_color class=$ssettings_class colspan=2><span class=\"font2 $settings_fc\"><a href=\"$PHP_SELF?ADD=311111111111111\"> System Settings </a></span></td>\n";
    } else {
        $acnt += 2;
    }
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_carriers'] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$carriers_color class=$carriers_class colspan=1><span class=\"font2 $carriers_fc\"><a href=\"$PHP_SELF?ADD=3carrier\"> Carriers </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_servers'] == 1) {
        $amenu .= "    <td height=20 align=center bgcolor=$server_color class=$server_class colspan=1><span class=\"font2 $server_fc\"><a href=\"$PHP_SELF?ADD=100000000000\"> Servers </a></span></td>\n";
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
    }
    if ($LOG["multicomp_admin"] > 0) {
        $amenu .= "    <td height=20 align=center bgcolor=$company_color class=$company_class colspan=1><span class=\"font2 $company_fc\"><a href=\"$PHP_SELF?ADD=10comp\"> Companies </a></span></td>\n";
    } else {
        $acnt += 1;
    }
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_calltimes'] == 1) {
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
	$amenu .= "<td bgcolor=#E2EBEE width=5>&nbsp;</td>";
/*
    if ($acnt) {
        if ($acnt > 1) {
            $amenu = "    <td height=20 colspan=" . ($acnt - round($acnt/2)) . " align=center><span class=font2>&nbsp;</span></td>\n" . $amenu;
        }
        $amenu .= "    <td height=20 colspan=" . (round($acnt/2)) . " align=center><span class=font2>&nbsp;</span></td>\n";
    }
*/
    echo $amenu;
    echo "  </tr>\n";



    ### Settings Sub-Menu.
    if ($ssettings_sh > 0) {
		if ($ADD==311111111111111) {
			$ssettings_show_color=$campaigns_color;
			$ssettings_show_class='rounded-menu3select';
		} else {
			$ssettings_show_color=$settings_color;
			$ssettings_show_class='rounded-menu3';
		}
		echo "  <tr class='no-ul' bgcolor=#E2EBEE>\n";
		echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
		echo "      <table width=100% cellpadding=2 cellspacing=0 border=0>\n";
		echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
		echo "          <td class=$ssettings_show_class align=center bgcolor=$ssettings_show_color width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=311111111111111\"> Modify System Settings </a></span></td>\n";
		echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
		echo "        </tr>\n";
		echo "      </table>\n";
		echo "    </td>\n";
		echo "  </tr>\n";
    /*
        echo "  <tr class='no-ul' bgcolor=$settings_color>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "    <td height=20 align=left colspan=$settings_menucols1>\n";
        echo "      <td align=center bgcolor=$filter_show_color class=$filter_show_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=311111111111111\"> System Settings </a></span></td>\n";
        echo "      </span>\n";
        echo "    </td>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "  </tr>\n";
    */
    }

/*
	Carriers = 3carrier
	Show Carriers = 3carriers sub1
	Add New Carrier = 1carrier sub2
	Modify = 3carrier sub2
	
*/


    ### Carriers Sub-Menu. 
    if ($carriers_sh >0) { 
		if ($ADD=='3carrier' and $SUB==1 or ($ADD=='3carrier' and $carrier_id ==0)) {
			$carriers_show_color=$campaigns_color;
			$carriers_show_class='rounded-menu3select';
		} else {
			$carriers_show_color=$settings_color;
			$carriers_show_class='rounded-menu3';
		}
		if ($ADD=='1carrier' and $SUB==2) {
			$carriers_add_color=$campaigns_color;
			$carriers_add_class='rounded-menu3select';
		} else {
			$carriers_add_color=$settings_color;
			$carriers_add_class='rounded-menu3';
		}
		if ($ADD=='1carrier' and $SUB==4) {
			$carriers_adddid_color=$campaigns_color;
			$carriers_adddid_class='rounded-menu3select';
		} else {
			$carriers_adddid_color=$settings_color;
			$carriers_adddid_class='rounded-menu3';
		}
		if ($ADD=='3carrier' and $SUB==2) {
			$carriers_back_color=$campaigns_color;
			$carriers_back_class='rounded-menu3select';
		} else {
			$carriers_back_color=$settings_color;
			$carriers_back_class='rounded-menu3';
		}
/*
		if ($ADD=='3carrier' and ($SUB==1 or $SUB=='')) {
			$carriers_show_color=$campaigns_color;
			$carriers_show_class='rounded-menu3select';
		} else {
			$carriers_show_color=$settings_color;
			$carriers_show_class='rounded-menu3';
		}
		if ($ADD=='1carrier' and $SUB==2) {
			$carriers_add_color=$campaigns_color;
			$carriers_add_class='rounded-menu3select';
			$carriers_adddid_color=$campaigns_color;
			$carriers_adddid_class='rounded-menu3select';
		} else {
			$carriers_add_color=$settings_color;
			$carriers_add_class='rounded-menu3';
			$carriers_adddid_color=$settings_color;
			$carriers_adddid_class='rounded-menu3';
		}
		if ($ADD=='3carrier' and $SUB==3) {
			$carriers_add_color=$campaigns_color;
			$carriers_add_class='rounded-menu3select';
			$carriers_back_color=$campaigns_color;
			$carriers_back_class='rounded-menu3select';
		} else {
			$carriers_add_color=$settings_color;
			$carriers_add_class='rounded-menu3';
			$carriers_back_color=$settings_color;
			$carriers_back_class='rounded-menu3';
		}
		if ($ADD=='3carrier' and $SUB==4) {
			$carriers_adddid_color=$campaigns_color;
			$carriers_adddid_class='rounded-menu3select';
			$carriers_back_color=$campaigns_color;
			$carriers_back_class='rounded-menu3select';
		} else {
			$carriers_adddid_color=$settings_color;
			$carriers_adddid_class='rounded-menu3';
			$carriers_back_color=$settings_color;
			$carriers_back_class='rounded-menu3';
		}
*/
		echo "  <tr class='no-ul' bgcolor=#E2EBEE>\n";
		echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
		echo "      <table width=100% cellpadding=2 cellspacing=0 border=0>\n";
		echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
		
		echo "          <td align=center bgcolor=$carriers_show_color class=$carriers_show_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=1\"> Show Carriers </a></span></td>\n";
		echo "          <td align=center bgcolor=$carriers_add_color class=$carriers_add_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=2\"> Add New Carrier sub2 </a></span></td>\n";
/* 		if ($SUB==4) {
// 			echo "          <td align=center bgcolor=$carriers_back_color class=$carriers_back_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\"> Back to Carrier </a></span></td>";
// 			echo "          <td align=center bgcolor=$carriers_adddid_color class=$carriers_adddid_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=4&carrier_id=$carrier_id\"> Add New DID </a></span></td>\n";
		} elseif ($SUB==3) {
			echo "          <td align=center bgcolor=$carriers_back_color class=$carriers_back_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\"> Back to Carrier sub3 </a></span></td>\n";
			
		} elseif ($SUB==2) {
			
            if ($carrier_id>0) {
				echo "          <td align=center bgcolor=$carriers_add_color class=$carriers_add_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=2\"> Add New Carrier </a></span></td>\n";
				
			
		
			} else {
				echo "          <td align=center bgcolor=$carriers_add_color class=$carriers_add_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=2\"> Add New Carrier </a>\n";
			}
		} else {
				echo "          <td align=center bgcolor=$carriers_add_color class=$carriers_add_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=2\"> Add New Carrier </a>\n";
		}
*/
		echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
		echo "        </tr>\n";
		if ($SUB==2 and $carrier_id >0 or ($ADD=='1carrier' and $SUB==4)) {
				echo "          <tr><td align=center bgcolor=$carriers_back_color class=$carriers_back_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\"> Modify Carrier </a></span></td>\n";
				echo "          <td align=center bgcolor=$carriers_adddid_color class=$carriers_adddid_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=4&carrier_id=$carrier_id\"> Add New DID </a></span></td>\n";
				echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=3>&nbsp;</td></tr>";
			}
		echo "      </table>\n";
		echo "    </td>\n";
		echo "  </tr>\n";
		/*
        echo "  <tr class='no-ul' bgcolor=$carriers_color>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "    <td height=20 align=left colspan=$settings_menucols1>\n";
        echo "      <span class=\"font2 fgdefault\"> &nbsp \n";
        echo "        <a href=\"$PHP_SELF?ADD=3carrier&SUB=1\"> Show Carriers </a>\n";
        if ($SUB==4) {
            echo "        <a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\"> Back to Carrier </a>\n";
            echo "        <a href=\"$PHP_SELF?ADD=1carrier&SUB=4&carrier_id=$carrier_id\"> Add New DID </a>\n";
        } elseif ($SUB==3) {
            echo "        <a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\"> Back to Carrier </a>\n";
        } elseif ($SUB==2) {
            if ($carrier_id>0) echo "        <a href=\"$PHP_SELF?ADD=1carrier&SUB=2\"> Add New Carrier </a>\n";
            if ($carrier_id>0) echo "        <a href=\"$PHP_SELF?ADD=1carrier&SUB=4&carrier_id=$carrier_id\"> Add New DID </a>\n";
        } else {
            echo "        <a href=\"$PHP_SELF?ADD=1carrier&SUB=2\"> Add New Carrier </a>\n";
        }
        echo "      </span>\n";
        echo "    </td>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "  </tr>\n";
        */
    }

    

    ### Servers Sub-Menu.
    if ($server_sh > 0) { 
		if ($ADD==100000000000) {
			$server_show_color=$campaigns_color;
			$server_show_class='rounded-menu3select';
		} else {
			$server_show_color=$settings_color;
			$server_show_class='rounded-menu3';
		}
		if ($ADD==111111111111) {
			$server_add_color=$campaigns_color;
			$server_add_class='rounded-menu3select';
		} else {
			$server_add_color=$settings_color;
			$server_add_class='rounded-menu3';
		}
		if ($ADD==399211111111111) {
			$server_qcs_color=$campaigns_color;
			$server_qcs_class='rounded-menu3select';
		} else {
			$server_qcs_color=$settings_color;
			$server_qcs_class='rounded-menu3';
		}
		if ($ADD==399911111111111) {
			$server_extdnc_color=$campaigns_color;
			$server_extdnc_class='rounded-menu3select';
		} else {
			$server_extdnc_color=$settings_color;
			$server_extdnc_class='rounded-menu3';
		}
		if ($ADD==311111111111 and OSDstrlen($server_id) >0) {
			$server_modify_color=$campaigns_color;
			$server_modify_class='rounded-menu3select';
		}
		
		echo "  <tr class='no-ul' bgcolor=#E2EBEE>\n";
		echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
		echo "      <table width=100% cellpadding=2 cellspacing=0 border=0>\n";
		echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
		echo "          <td class=$server_show_class align=center bgcolor=$server_show_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=100000000000\"> Show Servers </a></span></td>\n";
		echo "          <td class=$server_add_class align=center bgcolor=$server_add_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=111111111111\"> Add A New Server </a></span></td>\n";
		echo "          <td class=$server_qcs_class align=center bgcolor=$server_qcs_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=399211111111111\"> QC Servers </a></span></td>\n";
		echo "          <td class=$server_extdnc_class align=center bgcolor=$server_extdnc_color><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=399911111111111\"> External DNC Database </a></span></td>\n";
		echo "          <td colspan=$cauth align=center class=rounded-menu3 bgcolor=$settings_color width=350><span class=\"font2 fgnavy\">&nbsp;</span></td>\n";
		echo "        </tr>\n";
		if ($ADD==311111111111 and OSDstrlen($server_id) > 0) {
			echo "        <tr><td align=center bgcolor=$server_modify_color class=$server_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=4\"> Modify Server </a></span></td>\n";
			echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=4>&nbsp;</td></tr>";
		}
		echo "      </table>\n";
		echo "    </td>\n";
		echo "  </tr>\n";
		echo "</table></tr>";
		
		/*
        echo "  <tr class='no-ul' bgcolor=$server_color>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "    <td height=20 align=left colspan=$settings_menucols1>\n";
        echo "      <span class=\"font2 fgdefault\"> &nbsp \n";
        echo "        <a href=\"$PHP_SELF?ADD=100000000000\"> Show Servers </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=111111111111\"> Add A New Server </a> &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=399111111111111\"> Archive Server </a> &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=399211111111111\"> QC Servers </a> &nbsp; &nbsp;\n";
        echo "        <a href=\"$PHP_SELF?ADD=399911111111111\"> External DNC Database </a>\n";
        echo "      </span>\n";
        echo "    </td>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "  </tr>\n";
        */
    }



    ### Phones Sub-Menu.
    if ($phones_sh > 0) { 
		if ($ADD==10000000000) {
			$phones_show_color=$campaigns_color;
			$phones_show_class='rounded-menu3select';
		} else {
			$phones_show_color=$settings_color;
			$phones_show_class='rounded-menu3';
		}
		if ($ADD==11111111111) {
			$phones_add_color=$campaigns_color;
			$phones_add_class='rounded-menu3select';
		} else {
			$phones_add_color=$settings_color;
			$phones_add_class='rounded-menu3';
		}
		if ($ADD==31111111111 and $extension > 0) {
			$phones_modify_color=$campaigns_color;
			$phones_modify_class='rounded-menu3select';
		} else {
			$phones_modify_color=$settings_color;
			$phones_modify_class='rounded-menu3';
		}
        echo "  <tr class='no-ul' bgcolor=#E2EBEE>\n";
		echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
		echo "      <table width=100% cellpadding=2 cellspacing=0 border=0>\n";
		echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
        echo "          <td class=$phones_show_class align=center bgcolor=$phones_show_color width=175><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=10000000000\"> Show Phones </a></span></td>\n";
        echo "          <td class=$phones_add_class align=center bgcolor=$phones_add_color width=175><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=11111111111\"> Add A New Phone </a></span></td>\n";
        echo "		    <td bgcolor=$phones_color class='rounded-menu2' colspan=2 width=400>&nbsp;</td>";
		echo "        </tr>\n";
		if ($ADD==31111111111 and $extension > 0) {
			echo "        <tr><td align=center bgcolor=$phones_modify_color class=$phones_modify_class width=175><span class=\"font2 fgnavy\"><a href=\"$PHP_SELF?ADD=1carrier&SUB=4\"> Modify Server </a></span></td>\n";
			echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=4>&nbsp;</td></tr>";
		}
		echo "      </table>\n";
		echo "    </td>\n";
		echo "  </tr>\n";
    }


        ### Conferences Sub-Menu.
    if ($conference_sh > 0) { 
		if ($ADD==1000000000000) {
			$conference_show_color=$campaigns_color;
			$conference_show_class='rounded-menu3select';
		} else {
			$conference_show_color=$settings_color;
			$conference_show_class='rounded-menu3';
		}
		if ($ADD==1111111111111) {
			$phones_add_color=$campaigns_color;
			$phones_add_class='rounded-menu3select';
		} else {
			$phones_add_color=$settings_color;
			$phones_add_class='rounded-menu3';
		}
		if ($ADD==10000000000000) {
			$conference_showosd_color=$campaigns_color;
			$conference_showosd_class='rounded-menu3select';
		} else {
			$conference_showosd_color=$settings_color;
			$conference_showosd_class='rounded-menu3';
		}
		if ($ADD==11111111111111) {
			$conference_addosd_color=$campaigns_color;
			$conference_addosd_class='rounded-menu3select';
		} else {
			$conference_addosd_color=$settings_color;
			$conference_addosd_class='rounded-menu3';
		}
		echo "  <tr class='no-ul' bgcolor=#E2EBEE>\n";
		echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
		echo "      <table width=100% cellpadding=2 cellspacing=0 border=0>\n";
		echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
		echo "    		<td align=center bgcolor=$conference_show_color class=$conference_show_class ><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=1000000000000\"> Show Conferences </a></span></td>";
        echo "    		<td align=center bgcolor=$phones_add_color class=$phones_add_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=1111111111111\"> Add A New Conference </a></span></td>";
        echo "    		<td align=center bgcolor=$conference_showosd_color class=$conference_showosd_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=10000000000000\"> Show $t1 Conferences </a></span></td>";
        echo "    		<td align=center bgcolor=$conference_addosd_color class=$conference_addosd_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=11111111111111\"> Add A New $t1 Conference </a></span></td>";
        echo "		    <td bgcolor=$phones_color class='rounded-menu3' colspan=2 width=200>&nbsp;</td>";
        echo "  	  </tr>\n";
        echo "      </table></td></tr>";
    }


    ### Call-times Sub-Menu.
    if ($times_sh > 0) { 
		if ($ADD==100000000) {
			$times_show_color=$campaigns_color;
			$times_show_class='rounded-menu3select';
		} else {
			$times_show_color=$settings_color;
			$times_show_class='rounded-menu3';
		}
		if ($ADD==111111111) {
			$times_add_color=$campaigns_color;
			$times_add_class='rounded-menu3select';
		} else {
			$times_add_color=$settings_color;
			$times_add_class='rounded-menu3';
		}
		if ($ADD==1000000000) {
			$times_showstate_color=$campaigns_color;
			$times_showstate_class='rounded-menu3select';
		} else {
			$times_showstate_color=$settings_color;
			$times_showstate_class='rounded-menu3';
		}
		if ($ADD==1111111111) {
			$times_addstate_color=$campaigns_color;
			$times_addstate_class='rounded-menu3select';
		} else {
			$times_addstate_color=$settings_color;
			$times_addstate_class='rounded-menu3';
		}
		if ($ADD==311111111 and OSDstrlen($call_time_id) > 0) {
			$times_modify_color=$campaigns_color;
			$times_modify_class='rounded-menu3select';
		} else {
			$times_modify_color=$settings_color;
			$times_modify_class='rounded-menu3';
		}
        echo "  <tr class='no-ul' bgcolor=#E2EBEE>\n";
		echo "    <td height=18 align=left colspan=$settings_menucols2>\n";
		echo "      <table width=100% cellpadding=2 cellspacing=0 border=0>\n";
		echo "        <tr align=center class='no-ul' bgcolor=$admin_color2 height=25>\n";
        echo "    <td align=center bgcolor=$times_show_color class=$times_show_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=100000000\"> Show Call Times </a></span></td>\n";
        echo "    <td align=center bgcolor=$times_add_color class=$times_add_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=111111111\"> Add A New Call Time </a></span></td>\n";
        echo "    <td align=center bgcolor=$times_showstate_color class=$times_showstate_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=1000000000\"> Show State Call Times </a></span></td>\n";
        echo "    <td align=center bgcolor=$times_addstate_color class=$times_addstate_class><span class=\"font2 fgdefault\"><a href=\"$PHP_SELF?ADD=1111111111\"> Add A New State Call Time </a></span></td>\n";
		echo "		    <td bgcolor=$phones_color class='rounded-menu3' colspan=2 width=200>&nbsp;</td>";
        echo "  </tr>\n";
        if ($ADD==311111111 and OSDstrlen($call_time_id) > 0) {
			echo "        <tr><td align=center bgcolor=$times_modify_color class=$times_modify_class width=175><span class=\"font2 fgnavy\"><a href=> Modify Call Time </a></span></td>\n";
			echo "		  <td bgcolor=$settings_color class='rounded-menu2' colspan=4>&nbsp;</td></tr>";
		}
    } 



	### Media Sub-Menu.
    if ($media_sh > 0) { 
        echo "  <tr class='no-ul' bgcolor=$server_color>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "    <td height=20 align=left colspan=$settings_menucols1>\n";
        echo "      <span class=\"font2 fgdefault\"> &nbsp \n";
        echo "        <a href=\"$PHP_SELF?ADD=10media\"> Show Media Files </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=11media\"> Add Media File </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=10tts\"> Show TTS Templates </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=11tts\"> Add TTS Template </a>\n";
        echo "      </span>\n";
        echo "    </td>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "  </tr>\n";
    }


    
    ### Company Sub-Menu.
    if ($company_sh > 0) { 
        echo "  <tr class='no-ul' bgcolor=$server_color>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "    <td height=20 align=left colspan=$settings_menucols1>\n";
        echo "      <span class=\"font2 fgdefault\"> &nbsp \n";
        echo "        <a href=\"$PHP_SELF?ADD=10comp\"> Show Companies </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=11comp\"> Add A New Company </a>\n";
        echo "      </span>\n";
        echo "    </td>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "  </tr>\n";
    }




    ### Statuses Sub-Menu.
    if ($status_sh > 0) { 
        echo "  <tr class='no-ul' bgcolor=$status_color>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "    <td height=20 align=left colspan=$settings_menucols1>\n";
        echo "      <span class=\"font2 fgdefault\"> &nbsp \n";
        echo "        <a href=\"$PHP_SELF?ADD=321111111111111\"> System Statuses </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=331111111111111\"> Status Categories </a>\n";
        echo "      </span>\n";
        echo "    </td>\n";
		echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
        echo "  </tr>\n";
    }
    
    echo "</table></tr>";


} elseif ($admin_hh > 0 and $LOG['ast_admin_access']<1) { 
    ### Do nothing if admin has no permissions
    $ADD='99999999999999999999';
    echo "  <tr class='no-ul' bgcolor=$settings_color>\n";
	echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
    echo "    <td height=20 align=left colspan=$settings_menucols2>\n";
    echo "      <span class=fgred>You are not authorized to view this page.</span>\n";
    echo "    </td>\n";
	echo "	  <td class='narrow-space' bgcolor=$admin_color>&nbsp;</td>";
    echo "  </tr>\n";
} 



/*
echo "  <tr>\n";
echo "    <td align=left colspan=$settings_menucols2 HEIGHT=1 bgcolor=#666666></td>\n";
echo "  </tr>\n";
*/
echo "</table>\n";
echo "</div>";
######################### HTML HEADER END #######################################



?>
