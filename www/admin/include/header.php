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
echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
if ($ADD==999999 && ($SUB==11 || $SUB==12 || $SUB==13 || $SUB==14)) {
    if (!isset($RR)) $RR=4;
    if ($RR <1) $RR=4;
    $metadetail = '';
    if ($SUB==12 || $SUB==14) {
        if ($campaign_id == '') $campaign_id = $group;
        $metadetail .= "&group=$group&campaign_id=$campaign_id&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname";
        $metadetail .= "&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo";
    }
    echo "  <meta http-equiv=Refresh content=\"$RR; URL=$PHP_SELF?ADD=$ADD&SUB=$SUB&RR=$RR&DB=$DB&adastats=$adastats$metadetail\">\n";
}
echo "  <link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $system_settings['admin_template'] . "/styles.css\" media=\"screen\">\n";
echo "  <link rel=\"stylesheet\" type=\"text/css\" href=\"styles-print.css\" media=\"print\">\n";

echo "  <title>$t1 Administrator: $title</title>\n";

echo "  <script language=\"Javascript\">\n";
require('include/admin.js');
require('include/CalendarPopup.js');
echo "  </script>\n";
echo "</head>\n";

echo "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onload=\"updateClock(); setInterval('updateClock()', 1000 )\" onunload=\"stop()\">\n";

echo "<script language=\"JavaScript\">document.write(getCalendarStyles());</script>\n";


######################### HTML HEADER BEGIN #######################################
if ($hh=='users') 
    {$users_hh="bgcolor =\"$users_color\""; $users_fc="$users_font"; $users_bold="$header_selected_bold";}
    else {$users_hh=''; $users_fc='WHITE'; $users_bold="$header_nonselected_bold";}
if ($hh=='campaigns') 
    {$campaigns_hh="bgcolor=\"$campaigns_color\""; $campaigns_fc="$campaigns_font"; $campaigns_bold="$header_selected_bold";}
    else {$campaigns_hh=''; $campaigns_fc='WHITE'; $campaigns_bold="$header_nonselected_bold";}
if ($hh=='lists') 
    {$lists_hh="bgcolor=\"$lists_color\""; $lists_fc="$lists_font"; $lists_bold="$header_selected_bold";}
    else {$lists_hh=''; $lists_fc='WHITE'; $lists_bold="$header_nonselected_bold";}
if ($hh=='ingroups') 
    {$ingroups_hh="bgcolor=\"$ingroups_color\""; $ingroups_fc="$ingroups_font"; $ingroups_bold="$header_selected_bold";}
    else {$ingroups_hh=''; $ingroups_fc='WHITE'; $ingroups_bold="$header_nonselected_bold";}
if ($hh=='remoteagent') 
    {$remoteagent_hh="bgcolor=\"$remoteagent_color\""; $remoteagent_fc="$remoteagent_font"; $remoteagent_bold="$header_selected_bold";}
    else {$remoteagent_hh=''; $remoteagent_fc='WHITE'; $remoteagent_bold="$header_nonselected_bold";}
if ($hh=='usergroups') 
    {$usergroups_hh="bgcolor=\"$usergroups_color\""; $usergroups_fc="$usergroups_font"; $usergroups_bold="$header_selected_bold";}
    else {$usergroups_hh=''; $usergroups_fc='WHITE'; $usergroups_bold="$header_nonselected_bold";}
if ($hh=='scripts') 
    {$scripts_hh="bgcolor=\"$scripts_color\""; $scripts_fc="$scripts_font"; $scripts_bold="$header_selected_bold";}
    else {$scripts_hh=''; $scripts_fc='WHITE'; $scripts_bold="$header_nonselected_bold";}
if ($hh=='filters') 
    {$filters_hh="bgcolor=\"$filters_color\""; $filters_fc="$filters_font"; $filters_bold="$header_selected_bold";}
    else {$filters_hh=''; $filters_fc='WHITE'; $filters_bold="$header_nonselected_bold";}
if ($hh=='admin') 
    {$admin_hh="bgcolor=\"$admin_color\""; $admin_fc="$admin_font"; $admin_bold="$header_selected_bold";}
    else {$admin_hh=''; $admin_fc='WHITE'; $admin_bold="$header_nonselected_bold";}
if ($hh=='reports') 
    {$reports_hh="bgcolor=\"$reports_color\""; $reports_fc="$reports_font"; $reports_bold="$header_selected_bold";}
    else {$reports_hh=''; $reports_fc='WHITE'; $reports_bold="$header_nonselected_bold";}



echo "<div class=container>";
echo "<div class=header>";
echo "<table width=900 Oldwidth$page_width bgcolor=$maintable_color cellpadding=0 cellspacing=0 align=center class=across>\n";
echo "  <tr>\n";
echo "    <td colspan=10>\n";
echo "      <table align='center' border='0' cellspacing='0' cellpadding='0'>\n";
echo "        <tr>    <!-- First draw the top row  -->\n";
echo "          <td class='across-top' width='15'><img src='templates/$system_settings[admin_template]/images/topleft.png' width='15' height='16' align='left'></td>\n";
echo "          <td class='across-top' align='center'></td>\n";
echo "          <td class='across-top' width='15'><img src='templates/$system_settings[admin_template]/images/topright.png' width='15' height='16' align='right'></td>\n";
echo "        </tr>\n";
echo "        <tr valign='top'>\n";
echo "          <td align=left width=33%>\n";
echo "              <font face=\"arial,helvetica\" color=white size=2>&nbsp;&nbsp;</font><B><a href=\"$admin_home_url_LU\"><font face=\"arial,helvetica\" color=$home_color size=1>HOME</font></a><font face=\"arial,helvetica\" color=$default_text size=2>&nbsp;|&nbsp;</font><a href=\"$PHP_SELF?force_logout=1\"><font face=\"arial,helvetica\" color=$logout_color size=1>Logout</font></a>\n";
echo "          </td>\n";
echo "          <td class='user-company' align=center width=33%>\n";
echo "              <font color=$company_color>$user_company</font><br />\n";
echo "              <font color=$header_color size=2><b><br>$t1 Administrator</b><br><br><br></font>\n";
echo "          </td>\n";
echo "          <td align=right width=33%>";
echo "              <font face=\"arial,helvetica\" color=$clock_color size=2>" . date("l F j, Y") . "&nbsp;&nbsp;<br>";
echo "              <div style=\"width: 10em; text-align: right; margin: 5px;\"><div id=\"clock\" style=\"color: $clock_color\"></div></div>";
echo "          </td>\n";
echo "        </tr>\n";
echo "      </table>\n";
echo "    </td>\n";
echo "  </tr>\n";



# BEGIN main menu.
$cauth=0;
$mmenu = '';
$mmenu .= "    <td height=25 align=center $users_hh><a href=\"$PHP_SELF?ADD=0\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> Agents </font></a></td>\n";
$mmenu .= "    <td height=25 align=center $campaigns_hh><a href=\"$PHP_SELF?ADD=10\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> Campaigns </font></a></td>\n";
$mmenu .= "    <td height=25 align=center $lists_hh><a href=\"$PHP_SELF?ADD=100\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> Lists </font></a></td>\n";

if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_scripts'] == 1) {
    $mmenu .= "    <td height=25 align=center $scripts_hh><a href=\"$PHP_SELF?ADD=1000000\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> Scripts </font></a></td>\n";
} else {
    $cauth++;
}

if (($system_settings['enable_filters'] > 0) and ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_filters'] == 1)) {
    $mmenu .= "    <td height=25 align=center $filters_hh><a href=\"$PHP_SELF?ADD=10000000\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> Filters </font></a></td>\n";
} else {
    $cauth++;
}

if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_ingroups'] == 1) {
    $mmenu .= "    <td height=25 align=center $ingroups_hh><a href=\"$PHP_SELF?ADD=1000\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> In-Groups </font></a></td>\n";
} else {
    $cauth++;
}

$mmenu .= "    <td height=25 align=center $usergroups_hh><a href=\"$PHP_SELF?ADD=100000\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> User Groups </font></a></td>\n";

if (($system_settings['enable_external_agents'] > 0) and ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_external_agents'] == 1)) {
    $mmenu .= "    <td height=25 align=center $remoteagent_hh><a href=\"$PHP_SELF?ADD=10000\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> External Agents </font></a></td>\n";
} else {
    $cauth++;
}

if ($LOG['view_reports'] > 0) {
    $mmenu .= "    <td height=25 align=center $reports_hh><a href=\"$PHP_SELF?ADD=999999\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> Reports </font></a></td>\n";
} else {
    $cauth++;
}

if ($LOG['user_level'] > 8 and ($LOG['multicomp_user'] == 0 or ($LOG['company']['enable_system_calltimes'] + $LOG['company']['enable_system_phones'] + $LOG['company']['enable_system_conferences'] + $LOG['company']['enable_system_servers'] + $LOG['company']['enable_system_statuses']) != 0)) {
    $mmenu .= "    <td height=25 align=center $admin_hh><a href=\"$PHP_SELF?ADD=10000000000\"><font face=\"arial,helvetica\" color=$menu_h1_color size=$header_font_size> Setup </font></a></td>\n";
} else {
    $cauth++;
}

if ($cauth) {
    if ($cauth > 1) {
        $mmenu = "    <td height=25 colspan=" . ($cauth - round($cauth/2)) . " align=center>&nbsp;</td>\n" . $mmenu;
    }
    $mmenu .= "    <td height=25 colspan=" . (round($cauth/2)) . " align=center>&nbsp;</td>\n";
}

echo "  <tr class='no-ul'>\n";
echo $mmenu;
echo "  </tr>\n";
# END Main Menu



### Agents Menu
if (strlen($users_hh) > 1) { 
    echo "  <tr class='no-ul' bgcolor=$users_color>\n";
    echo "    <td align=left colspan=10 height=20>\n";
    echo "      <font face=\"arial,helvetica\" color=$menu_h2_color size=$subheader_font_size> &nbsp;\n";
    echo "        <a href=\"$PHP_SELF\"> Show Agents </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
    echo "        <a href=\"$PHP_SELF?ADD=1\"> Add A New Agent </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
    echo "        <a href=\"$PHP_SELF?ADD=1A\"> Copy Agent </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
    echo "        <a href=\"$PHP_SELF?ADD=550\"> Search For An Agent </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
    if ($system_settings['enable_lead_allocation'] > 0) {
        echo "        <a href=\"$PHP_SELF?ADD=9\"> Lead Allocation </a> &nbsp; &nbsp;\n";
    } else {
        echo "        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;\n";
    }
    if ($user != "" and $ADD!=550 and $ADD!=660) {
        echo "        | &nbsp; &nbsp;\n";
        if ($LOG['view_agent_stats']) echo "        <a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$user\"> Stats </a> &nbsp; &nbsp; &nbsp;\n";
        if ($LOG['view_agent_status']) echo "        <a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$user\"> Status </a> &nbsp; &nbsp; &nbsp;\n";
        if ($LOG['view_agent_timesheet']) echo "        <a href=\"$PHP_SELF?ADD=999999&SUB=20&agent=$user\"> Time </a> &nbsp; &nbsp; &nbsp;\n";
        echo "        <a href=\"$PHP_SELF?ADD=8&agent=$user\"> Callbacks </a> \n";
    }
    echo "      </font>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
} 



### Campaigns Menu
if (strlen($campaigns_hh) > 1) { 

    if ($sh=='basic') $sh='list';
    if ($sh=='detail') $sh='list';
    if ($sh=='dialstat') $sh='list';
    if ($sh=='realtime') $sh='list';
    if ($sh=='ivr') $sh='list';
    if ($sh=='status'  and $ADD != 32) $sh='list';
    if ($sh=='hotkey'  and $ADD != 33) $sh='list';
    if ($sh=='recycle' and $ADD != 35) $sh='list';
    if ($sh=='autoalt' and $ADD != 36) $sh='list';
    if ($sh=='pause'   and $ADD != 37) $sh='list';
    if ($sh=='listmix' and $ADD != 39) $sh='list';

    if ($sh=='list') {$list_sh="bgcolor=\"$subcamp_color\""; $list_fc="$subcamp_font";}
        else {$list_sh=''; $list_fc=$menu_h2_color;}
    if ($sh=='status') {$status_sh="bgcolor=\"$subcamp_color\""; $status_fc="$subcamp_font";}
        else {$status_sh=''; $status_fc=$menu_h2_color;}
    if ($sh=='hotkey') {$hotkey_sh="bgcolor=\"$subcamp_color\""; $hotkey_fc="$subcamp_font";}
        else {$hotkey_sh=''; $hotkey_fc=$menu_h2_color;}
    if ($sh=='recycle') {$recycle_sh="bgcolor=\"$subcamp_color\""; $recycle_fc="$subcamp_font";}
        else {$recycle_sh=''; $recycle_fc=$menu_h2_color;}
    if ($sh=='autoalt') {$autoalt_sh="bgcolor=\"$subcamp_color\""; $autoalt_fc="$subcamp_font";}
        else {$autoalt_sh=''; $autoalt_fc=$menu_h2_color;}
    if ($sh=='pause') {$pause_sh="bgcolor=\"$subcamp_color\""; $pause_fc="$subcamp_font";}
        else {$pause_sh=''; $pause_fc=$menu_h2_color;}
    if ($sh=='fields') {$fields_sh="bgcolor=\"$subcamp_color\""; $fields_fc="$subcamp_font";}
        else {$fields_sh=''; $fields_fc=$menu_h2_color;}
    if ($sh=='listmix') {$listmix_sh="bgcolor=\"$subcamp_color\""; $listmix_fc="$subcamp_font";}
        else {$listmix_sh=''; $listmix_fc=$menu_h2_color;}

    $cauth=0;
    echo "  <tr class='no-ul' bgcolor=$campaigns_color>\n";
    echo "    <td height=20 align=center $list_sh colspan=2><a href=\"$PHP_SELF?ADD=10\"><font face=\"arial,helvetica\" color=$list_fc size=$subcamp_font_size> Campaigns Main </font></a></td>\n";
    echo "    <td align=center $status_sh colspan=1><a href=\"$PHP_SELF?ADD=32\"><font face=\"arial,helvetica\" color=$status_fc size=$subcamp_font_size> Statuses </font></a></td>\n";
    echo "    <td align=center $hotkey_sh colspan=1><a href=\"$PHP_SELF?ADD=33\"><font face=\"arial,helvetica\" color=$hotkey_fc size=$subcamp_font_size> HotKeys </font></a></td>\n";
    echo "    <td align=center $recycle_sh colspan=1><a href=\"$PHP_SELF?ADD=35\"><font face=\"arial,helvetica\" color=$recycle_fc size=$subcamp_font_size> Lead Recycle </font></a></td>\n";
    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_campaign_listmix'] == 1) {
        echo "    <td align=center $listmix_sh colspan=1><a href=\"$PHP_SELF?ADD=39\"><font face=\"arial,helvetica\" color=$listmix_fc size=$subcamp_font_size> List Mix </font></a></td>\n";
    } else {
        $cauth++;
    }
    echo "    <td align=center $autoalt_sh colspan=1><a href=\"$PHP_SELF?ADD=36\"><font face=\"arial,helvetica\" color=$autoalt_fc size=$subcamp_font_size> Auto-Alt Dial </font></a></td>\n";
    echo "    <td align=center $pause_sh colspan=1><a href=\"$PHP_SELF?ADD=37\"><font face=\"arial,helvetica\" color=$pause_fc size=$subcamp_font_size> Pause Codes </font></a></td>\n";
    echo "    <td align=center $fields_sh colspan=2><a href=\"$PHP_SELF?ADD=3fields\"><font face=\"arial,helvetica\" color=$fields_fc size=$subcamp_font_size> Additional Fields </font></a></td>\n";
    if ($cauth) {
        echo "      <td colspan=$cauth align=center><font size=2 color=$subcamp_font_size face=\"arial,helvetica\">&nbsp;</font></td>\n";
    }
    echo "  </tr>\n";

    if (strlen($list_sh) > 1) { 
        echo "  <tr class='no-ul' bgcolor=$subcamp_color>\n";
        echo "    <td height=20 align=left colspan=10>\n";
        echo "      <font face=\"arial,helvetica\" color=$default_text size=$subcamp_font_size> &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=10\"> Show Campaigns </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=11\"> Add A New Campaign </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
        echo "        <a href=\"$PHP_SELF?ADD=12\"> Copy Campaign </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n";
        if ($LOG['view_agent_realtime_summary']) echo "        <a href=\"$PHP_SELF?ADD=999999&SUB=13\"> Real-Time Campaigns Summary </a>\n";
        echo "      </font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        if ($campaign_id != '') {
            # Detailed sub-menu
            if ($ADD == 34 and $SUB<1)        {$camp_basic_color=$subcamp_color;}
                else        {$camp_basic_color=$campaigns_color;}
            if ($ADD == 31 and $SUB<1)        {$camp_detail_color=$subcamp_color;}
                else        {$camp_detail_color=$campaigns_color;}
            if ($SUB==22)    {$camp_statuses_color=$subcamp_color;}
                else        {$camp_statuses_color=$campaigns_color;}
            if ($SUB==23)    {$camp_hotkeys_color=$subcamp_color;}
                else        {$camp_hotkeys_color=$campaigns_color;}
            if ($SUB==25)    {$camp_recycle_color=$subcamp_color;}
                else        {$camp_recycle_color=$campaigns_color;}
            if ($SUB==26)    {$camp_autoalt_color=$subcamp_color;}
                else        {$camp_autoalt_color=$campaigns_color;}
            if ($SUB==27)    {$camp_pause_color=$subcamp_color;}
                else        {$camp_pause_color=$campaigns_color;}
            if ($SUB==29)    {$camp_listmix_color=$subcamp_color;}
                else        {$camp_listmix_color=$campaigns_color;}
            if ($SUB==14)    {$camp_real_color=$subcamp_color;}
                else        {$camp_real_color=$campaigns_color;}
            if ($SUB=="2keys")    {$camp_oivr_color=$subcamp_color;}
                else        {$camp_oivr_color=$campaigns_color;}

            $cauth=0;
            echo "  <tr class='no-ul' bgcolor=$campaigns_color>\n";
            echo "    <td height=18 align=left colspan=10>\n";
            echo "      <table width=$page_width cellpadding=2 cellspacing=0>\n";
            echo "        <tr class='no-ul' bgcolor=$campaigns_color>\n";
            echo "          <td align=center bgcolor=$camp_basic_color><a href=\"$PHP_SELF?ADD=34&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">Basic</font></a></td>\n";
            echo "          <td align=center bgcolor=$camp_detail_color><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">Detail</font></a></td>\n";
            echo "          <td align=center bgcolor=$camp_statuses_color><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">Statuses</font></a></td>\n";
            echo "          <td align=center bgcolor=$camp_hotkeys_color><a href=\"$PHP_SELF?ADD=31&SUB=23&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">HotKeys</font></a></td>\n";
            echo "          <td align=center bgcolor=$camp_recycle_color><a href=\"$PHP_SELF?ADD=31&SUB=25&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">Lead Recycling</font></a></td>\n";
            if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_campaign_listmix'] == 1) {
                echo "          <td align=center bgcolor=$camp_listmix_color><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">List Mix</font></a></td>\n";
            } else {
                $cauth++;
            }
            echo "          <td align=center bgcolor=$camp_autoalt_color><a href=\"$PHP_SELF?ADD=31&SUB=26&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">Auto Alt Dial</font></a></td>\n";
            echo "          <td align=center bgcolor=$camp_pause_color><a href=\"$PHP_SELF?ADD=31&SUB=27&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">Pause Codes</font></a></td>\n";
            if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_campaign_ivr'] == 1) {
                echo "          <td align=center bgcolor=$camp_oivr_color><a href=\"$PHP_SELF?ADD=3menu&SUB=2keys&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">In/Out IVR</font></a></td>\n";
            } else {
                $cauth++;
            }
            echo "          <td align=center bgcolor=$camp_real_color>";
            if ($LOG['view_agent_realtime']) echo "<a href=\"$PHP_SELF?ADD=999999&SUB=14&group=$campaign_id&campaign_id=$campaign_id\"><font size=2 color=$subcamp_font face=\"arial,helvetica\">Real-Time</font></a>";
            echo "</td>\n";
            if ($cauth) {
                echo "          <td colspan=$cauth align=center bgcolor=$camp_real_color><font size=2 color=$subcamp_font face=\"arial,helvetica\">&nbsp;</font></td>\n";
            }
            echo "        </tr>\n";
            echo "      </table>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
        }
    } 
} 



### Lists Menu
if (strlen($lists_hh) > 1) { 
    echo "  <tr class='no-ul' bgcolor=$lists_color>\n";
    echo "    <td height=20 align=left colspan=10>\n";
    echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=100\"> Show Lists </a> &nbsp; &nbsp; &nbsp;\n";
    echo "        <a href=\"$PHP_SELF?ADD=111\"> Add A New List </a> &nbsp; &nbsp; &nbsp;\n";
    if ($LOG['view_lead_search_advanced']) {
        echo "        <a href=\"$PHP_SELF?ADD=999999&SUB=26\"> Lead Search </a> &nbsp; &nbsp; &nbsp;\n";
    } elseif ($LOG['view_lead_search']) {
        echo "        <a href=\"$PHP_SELF?ADD=999999&SUB=27\"> Lead Search </a> &nbsp; &nbsp; &nbsp;\n";
    }
    echo "        <a href=\"$PHP_SELF?ADD=121\"> Add Number To DNC </a> &nbsp; &nbsp; &nbsp;\n";
    if ($LOG['user_level'] > 7 && $LOG['load_leads'] > 0) {
        echo "        <a href=\"$PHP_SELF?ADD=122\"> Load Leads </a> &nbsp; &nbsp; &nbsp;\n";
    }
    if ($LOG['user_level'] > 8 && $LOG['export_leads'] > 0 && ($LOG['multicomp_user'] == 0 or $LOG['company']['export_leads'] > 0)) {
        echo "        <a href=\"$PHP_SELF?ADD=131\"> Export </a>\n";
    }
    echo "      </font>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
} 



### Scripts Menu
if (strlen($scripts_hh) > 1) { 
    echo "  <tr class='no-ul' bgcolor=$scripts_color>\n";
    echo "    <td height=20 align=left colspan=10>\n";
    echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=1000000\"> Show Scripts </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=1111111\"> Add A New Script </a>\n";
    echo "      </font>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
} 



### Filters Menu
if (strlen($filters_hh) > 1) { 
    echo "  <tr class='no-ul' bgcolor=$filters_color>\n";
    echo "    <td height=20 align=left colspan=10>\n";
    echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=10000000\"> Show Filters </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=11111111\"> Add A New Filter </a>\n";
    echo "      </font>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
} 



### InGroups Menu
if (strlen($ingroups_hh) > 1) { 
    echo "  <tr class='no-ul' bgcolor=$ingroups_color>\n";
    echo "    <td height=20 align=left colspan=10>\n";
    echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=1000\"> Show In-Groups </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=1111\"> Add A New In-Group </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=1211\"> Copy In-Group </a>\n";
    echo "      </font>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
} 



### External/Remote Agents Menu
if (strlen($remoteagent_hh) > 1) { 
    echo "  <tr class='no-ul' bgcolor=$remoteagent_color>\n";
    echo "    <td height=20 align=left colspan=10>\n";
    echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=10000\"> Show External Agents </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=11111\"> Add New External Agents </a>\n";
    echo "      </font>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
} 



### Usergroups Menu
if (strlen($usergroups_hh) > 1) { 
    echo "  <tr class='no-ul' bgcolor=$usergroups_color>\n";
    echo "    <td height=20 align=left colspan=10>\n";
    echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=100000\"> Show User Groups </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
    echo "        <a href=\"$PHP_SELF?ADD=111111\"> Add A New User Group </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
    if ($LOG['view_usergroup_hourly_stats']) echo "        <a href=\"$PHP_SELF?ADD=999999&SUB=24&group=$LOG[user_group]\"> Group Hourly Report </a>\n";
    echo "      </font>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
} 



### Reports Menu
if (strlen($reports_hh) > 1) { 
    echo "  <tr bgcolor=$reports_color>\n";
    echo "    <td height=20 align=left colspan=10>\n";
    echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size>&nbsp;</font>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
}



### Admin/Setup menu.
if (strlen($admin_hh) > 1) { 
    if ($sh=='times') {$times_sh="bgcolor=\"$times_color\""; $times_fc="$times_font";} # hard teal
        else {$times_sh=''; $times_fc=$menu_h2_color;}
    if ($sh=='phones') {$phones_sh="bgcolor=\"$server_color\""; $phones_fc="$phones_font";} # pink
        else {$phones_sh=''; $phones_fc=$menu_h2_color;}
    if ($sh=='server') {$server_sh="bgcolor=\"$server_color\""; $server_fc="$server_font";} # pink
        else {$server_sh=''; $server_fc=$menu_h2_color;}
    if ($sh=='company') {$company_sh="bgcolor=\"$server_color\""; $company_fc="$server_font";} # pink
        else {$company_sh=''; $company_fc=$menu_h2_color;}
    if ($sh=='conference') {$conference_sh="bgcolor=\"$server_color\""; $conference_fc="$server_font";} # pink
        else {$conference_sh=''; $conference_fc=$menu_h2_color;}
    if ($sh=='settings') {$settings_sh="bgcolor=\"$settings_color\""; $settings_fc="$settings_font";} # pink
        else {$settings_sh=''; $settings_fc=$menu_h2_color;}
    if ($sh=='status') {$status_sh="bgcolor=\"$status_color\""; $status_fc="$status_font";} # pink
        else {$status_sh=''; $status_fc=$menu_h2_color;}

    $amenu = '';
    $acnt = 0;

    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_calltimes'] == 1) {
        $amenu .= "    <td height=20 align=center $times_sh colspan=2><a href=\"$PHP_SELF?ADD=100000000\"><font face=\"arial,helvetica\" color=$times_fc size=$header_font_size> Call Times </font></a></td>\n";
    } else {
        $acnt += 2;
    }

    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_phones'] == 1) {
        $amenu .= "    <td align=center $phones_sh colspan=2><a href=\"$PHP_SELF?ADD=10000000000\"><font face=\"arial,helvetica\" color=$phones_fc size=$header_font_size> Phones </font></a></td>\n";
    } else {
        $acnt += 2;
    }

    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_conferences'] == 1) {
        $amenu .= "    <td align=center $conference_sh colspan=1><a href=\"$PHP_SELF?ADD=1000000000000\"><font face=\"arial,helvetica\" color=$conference_fc size=$header_font_size> Conferences </font></a></td>\n";
    } else {
        $acnt += 1;
    }


    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_servers'] == 1) {
        $amenu .= "    <td align=center $server_sh colspan=1><a href=\"$PHP_SELF?ADD=100000000000\"><font face=\"arial,helvetica\" color=$server_fc size=$header_font_size> Servers </font></a></td>\n";
    } else {
        $acnt += 1;
    }

    if ($LOG["multicomp_admin"] > 0) {
        $amenu .= "    <td align=center $company_sh colspan=1><a href=\"$PHP_SELF?ADD=10comp\"><font face=\"arial,helvetica\" color=$company_fc size=$header_font_size> Companies </font></a></td>\n";
    } else {
        $acnt += 1;
    }

    if ($LOG['multicomp_user'] == 0) {
        $amenu .= "    <td align=center $settings_sh colspan=1><a href=\"$PHP_SELF?ADD=311111111111111\"><font face=\"arial,helvetica\" color=$settings_fc size=$header_font_size> System Settings </font></a></td>\n";
    } else {
        $acnt += 1;
    }

    if ($LOG['multicomp_user'] == 0 or $LOG['company']['enable_system_statuses'] == 1) {
        $amenu .= "    <td align=center $status_sh colspan=2><a href=\"$PHP_SELF?ADD=321111111111111\"><font face=\"arial,helvetica\" color=$status_fc size=$header_font_size> System Statuses </font></a></td>\n";
    } else {
        $acnt += 2;
    }

    if ($acnt) {
        if ($acnt > 1) {
            $amenu = "    <td colspan=" . ($acnt - round($acnt/2)) . " align=center>&nbsp;</td>\n" . $amenu;
        }
        $amenu .= "    <td colspan=" . (round($acnt/2)) . " align=center>&nbsp;</td>\n";
    }

    echo "  <tr class='no-ul' bgcolor=$admin_color>\n";
    echo $amenu;
    echo "  </tr>\n";


    ### Call-times Sub-Menu.
    if (strlen($times_sh) > 1) { 
        echo "  <tr class='no-ul' bgcolor=$times_color>\n";
        echo "    <td height=20 align=left colspan=10>&nbsp;\n";
        echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=100000000\"> Show Call Times </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=111111111\"> Add A New Call Time </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=1000000000\"> Show State Call Times </a> &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=1111111111\"> Add A New State Call Time </a> &nbsp; \n";
        echo "      </font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    } 



    ### Phones Sub-Menu.
    if (strlen($phones_sh) > 1) { 
        echo "  <tr class='no-ul' bgcolor=$phones_color>\n";
        echo "    <td height=20 align=left colspan=10>&nbsp;\n";
        echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=10000000000\"> Show Phones </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=11111111111\"> Add A New Phone </a>\n";
        echo "      </font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



    ### Conferences Sub-Menu.
    if (strlen($conference_sh) > 1) { 
        echo "  <tr class='no-ul' bgcolor=$conference_color>\n";
        echo "    <td height=20 align=left colspan=10> &nbsp;\n";
        echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=1000000000000\"> Show Conferences </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=1111111111111\"> Add A New Conference </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=10000000000000\"> Show $t1 Conferences </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=11111111111111\"> Add A New $t1 Conference </a>\n";
        echo "      </font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



    ### Server Sub-Menu.
    if (strlen($server_sh) > 1) { 
        echo "  <tr class='no-ul' bgcolor=$server_color>\n";
        echo "    <td height=20 align=left colspan=10> &nbsp;\n";
        echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=100000000000\"> Show Servers </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=111111111111\"> Add A New Server </a> &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=399111111111111\"> Archive Server </a> &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=399211111111111\"> QC Servers </a> &nbsp; &nbsp;\n";
        echo "        <a href=\"$PHP_SELF?ADD=399911111111111\"> External DNC Database </a>\n";
        echo "      </font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



    ### Company Sub-Menu.
    if (strlen($company_sh) > 1) { 
        echo "  <tr class='no-ul' bgcolor=$server_color>\n";
        echo "    <td height=20 align=left colspan=10>&nbsp;\n";
        echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=10comp\"> Show Companies </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=11comp\"> Add A New Company </a>\n";
        echo "      </font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



    ### Settings Sub-Menu.
    if (strlen($settings_sh) > 1) { 
        echo "  <tr class='no-ul' bgcolor=$settings_color>\n";
        echo "    <td height=20 align=left colspan=10> &nbsp;\n";
        echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size> &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=311111111111111\"> System Settings </a>\n";
        echo "      </font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



    ### Statuses Sub-Menu.
    if (strlen($status_sh) > 1) { 
        echo "  <tr class='no-ul' bgcolor=$status_color>\n";
        echo "    <td height=20 align=left colspan=10> &nbsp;\n";
        echo "      <font face=\"arial,helvetica\" color=$default_text size=$subheader_font_size>\n";
        echo "        <a href=\"$PHP_SELF?ADD=321111111111111\"> System Statuses </a> &nbsp; &nbsp; &nbsp; \n";
        echo "        <a href=\"$PHP_SELF?ADD=331111111111111\"> Status Categories </a>\n";
        echo "      </font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    }



    ### Do nothing if admin has no permissions
    if ($LOGast_admin_access < 1) {
        $ADD='99999999999999999999';
        echo "</table></center>\n";
        echo "<font color=red>You are not authorized to view this page. Please go back.</font>\n";
    }
} 




echo "  <tr>\n";
echo "    <td align=left colspan=10 HEIGHT=1 bgcolor=#666666></td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "</div>";
echo "<div class=content>";
echo "<table width=100% class=maintable bgcolor=$maintable_color cellpadding=0 cellspacing=0 align=center>\n";
echo "  <tr>\n";
echo "    <td align=left colspan=10>\n";
######################### HTML HEADER END #######################################



?>
