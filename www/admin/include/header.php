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

header ("Content-type: text/html; charset=utf-8");
echo "<html>\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
if ($ADD==999999 && ($SUB==13 || $SUB==14)) {
	if (!isset($RR)) { $RR=4; }
	if ($RR <1) { $RR=4; }
	$metadetail = '';
	if ($SUB==14) {
		$metadetail .= "&group=$group&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname";
		$metadetail .= "&orderby=$orderby&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay";
	}
	echo "<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?ADD=$ADD&SUB=$SUB&RR=$RR&DB=$DB&adastats=$adastats$metadetail\">\n";
}
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\">\n";

echo "<title>OSDial Administrator: $title</title>\n";

echo "<script language=\"Javascript\">\n";
require('include/admin.js');
echo "</script>\n";
echo "</head>\n";

echo "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onload=\"updateClock(); setInterval('updateClock()', 1000 )\" onunload=\"stop()\">\n";




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


?>

<div class=container>
<table width=900 Oldwidth<?= $page_width ?> bgcolor=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>
    <tr>
        <td colspan=10>
            <table align='center' border='0' cellspacing='0' cellpadding='0'>
                <tr>    <!-- First draw the top row  -->
                    <td width='15'><img src='images/topleft.png' width='15' height='16' align='left'></td>
                    <td class='across-top' align='center'></td>
                    <td width='15'><img src='images/topright.png' width='15' height='16' align='right'></td>
                </tr>
                <tr valign='top'>
                    <td align=left width=33%>
                        <font face="arial,helvetica" color=white size=2>&nbsp;&nbsp;<B><a href="<?= $admin_home_url_LU ?>"><font face="arial,helvetica" color=white size=1>HOME</a>
                        | <a href="<?= $PHP_SELF ?>?force_logout=1"><font face="arial,helvetica" color=yellow size=1>Logout</a>
                    </td>
                    <td class='user-company' align=center width=33%>
                        <font color=#1C4754><?= $user_company ?></font><br />
                        <font color=#1C4754 size=2><b><br>OSDial Administrator<b><br><br><br></font>
                    </td>
                    <td align=right width=33%>
                        <font face="arial,helvetica" color=white size=2><?= date("l F j, Y") ?>&nbsp;&nbsp;<br>
                        <div style="width: 10em; text-align: right; margin: 5px;"><div id="clock"></div></div>
                        <? //echo date("G:i:s A") ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class='no-ul'>
        <td height=25 align=center <?= $users_hh ?>><a href="<?= $PHP_SELF ?>?ADD=0"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> Agents </font></a></td>
        <td height=25 align=center <?= $campaigns_hh ?>><a href="<?= $PHP_SELF ?>?ADD=10"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> Campaigns </font></a></td>
        <td height=25 align=center <?= $lists_hh ?>><a href="<?= $PHP_SELF ?>?ADD=100"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> Lists </font></a></td>
        <td height=25 align=center <?= $scripts_hh ?>><a href="<?= $PHP_SELF ?>?ADD=1000000"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> Scripts </font></a></td>
        <td height=25 align=center <?= $filters_hh ?>><a href="<?= $PHP_SELF ?>?ADD=10000000"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> Filters </font></a></td>
        <td height=25 align=center <?= $ingroups_hh ?>><a href="<?= $PHP_SELF ?>?ADD=1000"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> In-Groups </font></a></td>
        <td height=25 align=center <?= $usergroups_hh ?>><a href="<?= $PHP_SELF ?>?ADD=100000"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> User Groups </font></a></td>
        <td height=25 align=center <?= $remoteagent_hh ?>><a href="<?= $PHP_SELF ?>?ADD=10000"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> External Agents </font></a></td>
        <td height=25 align=center <?= $reports_hh ?>><a href="<?= $PHP_SELF ?>?ADD=999999"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> Reports </font></a></td>
        <td height=25 align=center <?= $admin_hh ?>><a href="<?= $PHP_SELF ?>?ADD=10000000000"><font face="arial,helvetica" color=navy size=<?= $header_font_size ?>> Setup </font></a></td>
    </tr>
    <?
if (strlen($users_hh) > 1) { 
    ?>
    <tr class='no-ul' bgcolor=<?= $users_color ?>>
        <td align=left colspan=10 height=20>
            <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp;
                <a href="<?= $PHP_SELF ?>"> Show Agents </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <a href="<?= $PHP_SELF ?>?ADD=1"> Add A New Agent </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <a href="<?= $PHP_SELF ?>?ADD=1A"> Copy Agent </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <a href="<?= $PHP_SELF ?>?ADD=550"> Search For An Agent </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <a href="<?= $PHP_SELF ?>?ADD=999999&SUB=1&iframe=user_stats.php?user=<?= $user ?>"> Agent Stats </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <a href="<?= $PHP_SELF ?>?ADD=999999&SUB=1&iframe=user_status.php?user=<?= $user ?>"> Agent Status </a> 
            </font>
        </td>
    </tr>
    <?
} 
if (strlen($campaigns_hh) > 1) { 

    if ($sh=='basic') {$sh='list';}
    if ($sh=='detail') {$sh='list';}
    if ($sh=='dialstat') {$sh='list';}
    if ($sh=='realtime') {$sh='list';}
    if ($sh=='outbound_ivr') {$sh='list';}

    if ($sh=='list') {$list_sh="bgcolor=\"$subcamp_color\""; $list_fc="$subcamp_font";}
        else {$list_sh=''; $list_fc='black';}
    if ($sh=='status') {$status_sh="bgcolor=\"$subcamp_color\""; $status_fc="$subcamp_font";}
        else {$status_sh=''; $status_fc='black';}
    if ($sh=='hotkey') {$hotkey_sh="bgcolor=\"$subcamp_color\""; $hotkey_fc="$subcamp_font";}
        else {$hotkey_sh=''; $hotkey_fc='black';}
    if ($sh=='recycle') {$recycle_sh="bgcolor=\"$subcamp_color\""; $recycle_fc="$subcamp_font";}
        else {$recycle_sh=''; $recycle_fc='black';}
    if ($sh=='autoalt') {$autoalt_sh="bgcolor=\"$subcamp_color\""; $autoalt_fc="$subcamp_font";}
        else {$autoalt_sh=''; $autoalt_fc='black';}
    if ($sh=='pause') {$pause_sh="bgcolor=\"$subcamp_color\""; $pause_fc="$subcamp_font";}
        else {$pause_sh=''; $pause_fc='black';}
    if ($sh=='fields') {$fields_sh="bgcolor=\"$subcamp_color\""; $fields_fc="$subcamp_font";}
        else {$fields_sh=''; $fields_fc='black';}
    if ($sh=='listmix') {$listmix_sh="bgcolor=\"$subcamp_color\""; $listmix_fc="$subcamp_font";}
        else {$listmix_sh=''; $listmix_fc='black';}
    ?>

    <tr class='no-ul' bgcolor=<?= $campaigns_color ?>>
        <td height=20 align=center <?= $list_sh ?> colspan=2><a href="<?= $PHP_SELF ?>?ADD=10"><font face="arial,helvetica" color=<?= $list_fc ?> size=<?= $subcamp_font_size ?>> Campaigns Main </font></a></td>
        <td align=center <?= $status_sh ?> colspan=1><a href="<?= $PHP_SELF ?>?ADD=32"><font face="arial,helvetica" color=<?= $status_fc ?> size=<?= $subcamp_font_size ?>> Statuses </font></a></td>
        <td align=center <?= $hotkey_sh ?> colspan=1><a href="<?= $PHP_SELF ?>?ADD=33"><font face="arial,helvetica" color=<?= $hotkey_fc ?> size=<?= $subcamp_font_size ?>> HotKeys </font></a></td>
        <td align=center <?= $recycle_sh ?> colspan=2><a href="<?= $PHP_SELF ?>?ADD=35"><font face="arial,helvetica" color=<?= $recycle_fc ?> size=<?= $subcamp_font_size ?>> Lead Recycle </font></a></td>
        <td align=center <?= $autoalt_sh ?> colspan=1><a href="<?= $PHP_SELF ?>?ADD=36"><font face="arial,helvetica" color=<?= $autoalt_fc ?> size=<?= $subcamp_font_size ?>> Auto-Alt Dial </font></a></td>
        <td align=center <?= $pause_sh ?> colspan=1><a href="<?= $PHP_SELF ?>?ADD=37"><font face="arial,helvetica" color=<?= $pause_fc ?> size=<?= $subcamp_font_size ?>> Pause Codes </font></a></td>
        <td align=center <?= $fields_sh ?> colspan=1><a href="<?= $PHP_SELF ?>?ADD=3fields"><font face="arial,helvetica" color=<?= $fields_fc ?> size=<?= $subcamp_font_size ?>> Additional Fields </font></a></td>
        <td align=center <?= $listmix_sh ?> colspan=1><!--a href="<?= $PHP_SELF ?>?ADD=39"><font face="arial,helvetica" color=<?= $listmix_fc ?> size=<?= $subcamp_font_size ?>> List Mix </font></a --></td>
    </tr>
    <?
  if (strlen($list_sh) > 1) { 
    ?>
    <tr class='no-ul' bgcolor=<?= $subcamp_color ?>>
        <td height=20 align=left colspan=10>
            <font face="arial,helvetica" color=black size=<?= $subcamp_font_size ?>> &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=10"> Show Campaigns </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=11"> Add A New Campaign </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <a href="<?= $PHP_SELF ?>?ADD=12"> Copy Campaign </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <a href="<?= $PHP_SELF ?>?ADD=999999&SUB=13&iframe=AST_timeonVDADallSUMMARY.php"> Real-Time Campaigns Summary </a>
            </font>
        </td>
    </tr>
    <?

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
        ?>
        <tr class='no-ul' bgcolor="<?= $campaigns_color ?>">
        <td height=18 align=left colspan=10>
        <table width=<?= $page_width ?> CELLPADDING=2 CELLSPACING=0>
            <tr class='no-ul' bgcolor="<?= $campaigns_color ?>">
                <td align=center bgcolor="<?= $camp_basic_color ?>"> <a href="<?= $PHP_SELF ?>?ADD=34&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">Basic </font></a></td>
                <td align=center bgcolor="<?= $camp_detail_color ?>"> <a href="<?= $PHP_SELF ?>?ADD=31&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">Detail </font></a> </td>
                <td align=center bgcolor="<?= $camp_statuses_color ?>"><a href="<?= $PHP_SELF ?>?ADD=31&SUB=22&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">Statuses</font></a></td>
                <td align=center bgcolor="<?= $camp_hotkeys_color ?>"><a href="<?= $PHP_SELF ?>?ADD=31&SUB=23&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">HotKeys</font></a></td>
                <td align=center bgcolor="<?= $camp_recycle_color ?>"><a href="<?= $PHP_SELF ?>?ADD=31&SUB=25&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">Lead Recycling</font></a></td>
                <td align=center bgcolor="<?= $camp_autoalt_color ?>"><a href="<?= $PHP_SELF ?>?ADD=31&SUB=26&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">Auto Alt Dial</font></a></td>
                <td align=center bgcolor="<?= $camp_pause_color ?>"><a href="<?= $PHP_SELF ?>?ADD=31&SUB=27&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">Pause Codes</font></a></td>
                <td align=center bgcolor="<?= $camp_oivr_color ?>"> <a href="<?= $PHP_SELF ?>?ADD=3menu&SUB=2keys&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">Outbound IVR</font></a></td>
                <!-- <td align=center bgcolor="<?= $camp_listmix_color ?>"><a href="<?= $PHP_SELF ?>?ADD=31&SUB=29&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">List Mix</font></a></td>-->
                <td align=center bgcolor="<?= $camp_real_color ?>"> <a href="<?= $PHP_SELF ?>?ADD=999999&SUB=14&group=<?= $campaign_id ?>&campaign_id=<?= $campaign_id ?>"><font size=2 color=<?= $subcamp_font ?> face="arial,helvetica">Real-Time</font></a></td>
            </tr>
        </table>
        </td>
        </tr>
        <?
    }
  } 
} 

if (strlen($lists_hh) > 1) { 
    ?>
    <tr class='no-ul' bgcolor=<?= $lists_color ?>>
        <td height=20 align=left colspan=10>
            <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=100"> Show Lists </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=111"> Add A New List </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=112"> Search For A Lead </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=121"> Add Number To DNC </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=122"> Load New Leads </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=131"> Lead Export </a>
            </font>
        </td>
    </tr>
    <?
} 
if (strlen($scripts_hh) > 1) { 
    ?>
    <tr class='no-ul' bgcolor=<?= $scripts_color ?>>
        <td height=20 align=left colspan=10>
            <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=1000000"> Show Scripts </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=1111111"> Add A New Script </a>
            </font>
        </td>
    </tr>
    <?
} 
if (strlen($filters_hh) > 1) { 
    ?>
     <tr class='no-ul' bgcolor=<?= $filters_color ?>>
        <td height=20 align=left colspan=10>
            <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=10000000"> Show Filters </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=11111111"> Add A New Filter </a>
            </font>
        </td>
    </tr>
    <?
} 
if (strlen($ingroups_hh) > 1) { 
    ?>
    <tr class='no-ul' bgcolor=<?= $ingroups_color ?>>
        <td height=20 align=left colspan=10>
            <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=1000"> Show In-Groups </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=1111"> Add A New In-Group </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=1211"> Copy In-Group </a>
            </font>
        </td>
    </tr>
    <?
} 
if (strlen($usergroups_hh) > 1) { 
    ?>
    <tr class='no-ul' bgcolor=<?= $usergroups_color ?>>
        <td height=20 align=left colspan=10>
            <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=100000"> Show User Groups </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=111111"> Add A New User Group </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=999999&SUB=7&iframe=group_hourly_stats.php"> Group Hourly Report </a>
            </font>
        </td>
    </tr>
    <?
} 
if (strlen($remoteagent_hh) > 1) { 
    ?>
    <tr class='no-ul' bgcolor=<?= $remoteagent_color ?>>
        <td height=20 align=left colspan=10>
            <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=10000"> Show External Agents </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                <a href="<?= $PHP_SELF ?>?ADD=11111"> Add New External Agents </a>
            </font>
        </td>
    </tr>
    <?
} 

if (strlen($admin_hh) > 1) { 
    if ($sh=='times') {$times_sh="bgcolor=\"$times_color\""; $times_fc="$times_font";} # hard teal
        else {$times_sh=''; $times_fc='black';}
    if ($sh=='phones') {$phones_sh="bgcolor=\"$server_color\""; $phones_fc="$phones_font";} # pink
        else {$phones_sh=''; $phones_fc='black';}
    if ($sh=='server') {$server_sh="bgcolor=\"$server_color\""; $server_fc="$server_font";} # pink
        else {$server_sh=''; $server_fc='black';}
    if ($sh=='conference') {$conference_sh="bgcolor=\"$server_color\""; $conference_fc="$server_font";} # pink
        else {$conference_sh=''; $conference_fc='black';}
    if ($sh=='settings') {$settings_sh="bgcolor=\"$settings_color\""; $settings_fc="$settings_font";} # pink
        else {$settings_sh=''; $settings_fc='black';}
    if ($sh=='status') {$status_sh="bgcolor=\"$status_color\""; $status_fc="$status_font";} # pink
        else {$status_sh=''; $status_fc='black';}

        ?>
        <tr class='no-ul' bgcolor=<?= $admin_color ?>>
            <td height=20 align=center <?= $times_sh ?> colspan=2><a href="<?= $PHP_SELF ?>?ADD=100000000"><font face="arial,helvetica" color=<?= $times_fc ?> size=<?= $header_font_size ?>> Call Times </font></a></td>
            <td align=center <?= $phones_sh ?> colspan=2><a href="<?= $PHP_SELF ?>?ADD=10000000000"><font face="arial,helvetica" color=<?= $phones_fc ?> size=<?= $header_font_size ?>> Phones </font></a></td>
            <td align=center <?= $conference_sh ?> colspan=2><a href="<?= $PHP_SELF ?>?ADD=1000000000000"><font face="arial,helvetica" color=<?= $conference_fc ?> size=<?= $header_font_size ?>> Conferences </font></a></td>
            <td align=center <?= $server_sh ?> colspan=1><a href="<?= $PHP_SELF ?>?ADD=100000000000"><font face="arial,helvetica" color=<?= $server_fc ?> size=<?= $header_font_size ?>> Servers </font></a></td>
            <td align=center <?= $settings_sh ?> colspan=1><a href="<?= $PHP_SELF ?>?ADD=311111111111111"><font face="arial,helvetica" color=<?= $settings_fc ?> size=<?= $header_font_size ?>> System Settings </font></a></td>
            <td align=center <?= $status_sh ?> colspan=2><a href="<?= $PHP_SELF ?>?ADD=321111111111111"><font face="arial,helvetica" color=<?= $status_fc ?> size=<?= $header_font_size ?>> System Statuses </font></a></td>
        </tr>
        <?
    if (strlen($times_sh) > 1) { 
        ?>
        <tr class='no-ul' bgcolor=<?= $times_color ?>>
            <td height=20 align=left colspan=10>&nbsp;
                <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=100000000"> Show Call Times </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=111111111"> Add A New Call Time </a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=1000000000"> Show State Call Times </a> &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=1111111111"> Add A New State Call Time </a> &nbsp; 
                </font>
            </td>
        </tr>
        <?
    } 
    if (strlen($phones_sh) > 1) { 
        ?>
        <tr class='no-ul' bgcolor=<?= $phones_color ?>>
            <td height=20 align=left colspan=10>&nbsp;
                <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=10000000000"> Show Phones </a> &nbsp; &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=11111111111"> Add A New Phone </a>
                </font>
            </td>
        </tr>
        <?
    }
    if (strlen($conference_sh) > 1) { 
        ?>
        <tr class='no-ul' bgcolor=<?= $conference_color ?>>
            <td height=20 align=left colspan=10> &nbsp;
                <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=1000000000000"> Show Conferences </a> &nbsp; &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=1111111111111"> Add A New Conference </a> &nbsp; &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=10000000000000"> Show OSDial Conferences </a> &nbsp; &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=11111111111111"> Add A New OSDial Conference </a>
                </font>
            </td>
        </tr>
        <?
    }
    if (strlen($server_sh) > 1) { 
        ?>
        <tr class='no-ul' bgcolor=<?= $server_color ?>>
            <td height=20 align=left colspan=10> &nbsp;
                <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=100000000000"> Show Servers </a> &nbsp; &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=111111111111"> Add A New Server </a> &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=399111111111111"> Archive Server</a> &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=399211111111111"> QC Servers</a>
                </font>
            </td>
        </tr>
        <?}
    if (strlen($settings_sh) > 1) { 
        ?>
        <tr class='no-ul' bgcolor=<?= $settings_color ?>>
            <td height=20 align=left colspan=10> &nbsp;
                <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>> &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=311111111111111"> System Settings </a>
                </font>
            </td>
        </tr>
        <?
    }
    if (strlen($status_sh) > 1) { 
        ?>
        <tr class='no-ul' bgcolor=<?= $status_color ?>>
            <td height=20 align=left colspan=10> &nbsp;
                <font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>>
                    <a href="<?= $PHP_SELF ?>?ADD=321111111111111"> System Statuses </a> &nbsp; &nbsp; &nbsp; 
                    <a href="<?= $PHP_SELF ?>?ADD=331111111111111"> Status Categories </a>
                </font>
            </td>
        </tr>
        <?
    }

    ### Do nothing if admin has no permissions
    if ($LOGast_admin_access < 1) {
        $ADD='99999999999999999999';
        echo "</table></center>\n";
        echo "<font color=red>You are not authorized to view this page. Please go back.</font>\n";
    }
} 

if (strlen($reports_hh) > 1) { 
    ?>
    <tr bgcolor=<?= $reports_color ?>><td height=20 align=left colspan=10><font face="arial,helvetica" color=black size=<?= $subheader_font_size ?>><B> &nbsp; </B></td></tr>
    <?
}
    ?>
    <tr><td align=left colspan=10 HEIGHT=1 bgcolor=#666666></td></tr>
    <tr><td align=left colspan=10>
    <?

######################### HTML HEADER END #######################################



?>
