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

echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0 onload=\"updateClock(); setInterval('updateClock()', 1000 )\" onunload=\"stop()\">\n";




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
<TABLE WIDTH=900 Oldwidth<?=$page_width ?> BGCOLOR=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>
<tr><td colspan=10>

	<table align='center' border='0' cellspacing='0' cellpadding='0'>
		<tr>	<!-- First draw the top row  -->
			<td width='15'><img src='images/topleft.png' width='15' height='16' align='left'></td>
			<td class='across-top' align='center'></td>
			<td width='15'><img src='images/topright.png' width='15' height='16' align='right'></td>
		</tr>
		<tr valign='top'>
			<td align=left width=33%>
				<font face="arial,helvetica" color=white size=2>&nbsp;&nbsp;<B><a href="<? echo $admin_home_url_LU ?>"><font face="arial,helvetica" color=white size=1>HOME</a> | <a href="<? echo $PHP_SELF ?>?force_logout=1"><font face="arial,helvetica" color=yellow size=1>Logout</a>
			</td>
			<td class='user-company' align=center width=33%>
				<font color=#1C4754><? echo $user_company ?></font><br />
				<font color=#1C4754 size=2><b><br>OSDial Administrator<b><br><br><br></font>
			</td>
			<td align=right width=33%>
				<font face="arial,helvetica" color=white size=2><? echo date("l F j, Y") ?>&nbsp;&nbsp;<br>
				<div style="width: 10em; text-align: right; margin: 5px;">
					<span id="clock">&nbsp;&nbsp;</span>
				</div>
				<? //echo date("G:i:s A") ?>
			</td>
		</tr>
	</table>

</TR>
<TR class='no-ul'>
	
	<TD height=25 ALIGN=CENTER <?=$users_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=0"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Agents </a></TD>
	<TD height=25 ALIGN=CENTER <?=$campaigns_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Campaigns </a></TD>
	<TD height=25 ALIGN=CENTER <?=$lists_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=100"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Lists </a></TD>
	<TD height=25 ALIGN=CENTER <?=$scripts_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=1000000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Scripts </a></TD>
	<TD height=25 ALIGN=CENTER <?=$filters_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Filters </a></TD>
	<TD height=25 ALIGN=CENTER <?=$ingroups_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=1000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> In-Groups </a></TD>
	<TD height=25 ALIGN=CENTER <?=$usergroups_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=100000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> User Groups </a></TD>
	<TD height=25 ALIGN=CENTER <?=$remoteagent_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Off-Hook Agents </a></TD>
	<TD height=25 ALIGN=CENTER <?=$reports_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=999999"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Reports </a></TD>
	<TD height=25 ALIGN=CENTER <?=$admin_hh ?>><a href="<? echo $PHP_SELF ?>?ADD=10000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=navy SIZE=<?=$header_font_size ?>> Setup </a></TD>
</TR>

<? if (strlen($users_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$users_color ?>>
	<TD ALIGN=LEFT COLSPAN=10 height=20>
	<FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Agents </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Agent </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1A"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Copy Agent </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=550"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Search For An Agent </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=1&iframe=user_stats.php?user=<?=$user ?>"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Agent Stats </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=1&iframe=user_status.php?user=<?=$user ?>"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Agent Status </a> 
	</TD>
</TR>
<? } 
if (strlen($campaigns_hh) > 1) 
	{ 

	if ($sh=='basic') {$sh='list';}
	if ($sh=='detail') {$sh='list';}
	if ($sh=='dialstat') {$sh='list';}

	if ($sh=='list') {$list_sh="bgcolor=\"$subcamp_color\""; $list_fc="$subcamp_font";}
		else {$list_sh=''; $list_fc='BLACK';}
	if ($sh=='status') {$status_sh="bgcolor=\"$subcamp_color\""; $status_fc="$subcamp_font";}
		else {$status_sh=''; $status_fc='BLACK';}
	if ($sh=='hotkey') {$hotkey_sh="bgcolor=\"$subcamp_color\""; $hotkey_fc="$subcamp_font";}
		else {$hotkey_sh=''; $hotkey_fc='BLACK';}
	if ($sh=='recycle') {$recycle_sh="bgcolor=\"$subcamp_color\""; $recycle_fc="$subcamp_font";}
		else {$recycle_sh=''; $recycle_fc='BLACK';}
	if ($sh=='autoalt') {$autoalt_sh="bgcolor=\"$subcamp_color\""; $autoalt_fc="$subcamp_font";}
		else {$autoalt_sh=''; $autoalt_fc='BLACK';}
	if ($sh=='pause') {$pause_sh="bgcolor=\"$subcamp_color\""; $pause_fc="$subcamp_font";}
		else {$pause_sh=''; $pause_fc='BLACK';}
	if ($sh=='fields') {$fields_sh="bgcolor=\"$subcamp_color\""; $fields_fc="$subcamp_font";}
		else {$fields_sh=''; $fields_fc='BLACK';}
	if ($sh=='listmix') {$listmix_sh="bgcolor=\"$subcamp_color\""; $listmix_fc="$subcamp_font";}
		else {$listmix_sh=''; $listmix_fc='BLACK';}

	?>
<TR class='no-ul' BGCOLOR=<?=$campaigns_color ?>>
<TD height=20 ALIGN=CENTER <?=$list_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=10"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$list_fc ?> SIZE=<?=$subcamp_font_size ?>> Campaigns Main </a></TD>
<TD ALIGN=CENTER <?=$status_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=32"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$status_fc ?> SIZE=<?=$subcamp_font_size ?>> Statuses </a></TD>
<TD ALIGN=CENTER <?=$hotkey_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=33"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$hotkey_fc ?> SIZE=<?=$subcamp_font_size ?>> HotKeys </a></TD>
<TD ALIGN=CENTER <?=$recycle_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=35"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$recycle_fc ?> SIZE=<?=$subcamp_font_size ?>> Lead Recycle </a></TD>
<TD ALIGN=CENTER <?=$autoalt_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=36"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$autoalt_fc ?> SIZE=<?=$subcamp_font_size ?>> Auto-Alt Dial </a></TD>
<TD ALIGN=CENTER <?=$pause_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=37"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$pause_fc ?> SIZE=<?=$subcamp_font_size ?>> Pause Codes </a></TD>
<TD ALIGN=CENTER <?=$fields_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=3fields"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$fields_fc ?> SIZE=<?=$subcamp_font_size ?>> Additional Fields </a></TD>
<TD ALIGN=CENTER <?=$listmix_sh ?> COLSPAN=1><!--a href="<? echo $PHP_SELF ?>?ADD=39"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$listmix_fc ?> SIZE=<?=$subcamp_font_size ?>> List Mix </a --></TD>
</TR>
	<?
	if (strlen($list_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$subcamp_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> Show Campaigns </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> Add A New Campaign </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=12"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> Copy Campaign </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=13&iframe=AST_timeonVDADallSUMMARY.php"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subcamp_font_size ?>> Real-Time Campaigns Summary </a></TD></TR>
		<? } 

	} 
if (strlen($lists_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$lists_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=100"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Lists </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New List </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=112"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Search For A Lead </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=121"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add Number To DNC </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=122"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Load New Leads </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<?=$PHP_SELF?>?ADD=131"><font face="arial,helvetica" color="black" size=<?=$subheader_font_size?>> Lead Export </font></a></TD></TR>
<? } 
if (strlen($scripts_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$scripts_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Scripts </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Script </a></TD></TR>
<? } 
if (strlen($filters_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$filters_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Filters </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Filter </a></TD></TR>
<? } 
if (strlen($ingroups_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$ingroups_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show In-Groups </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New In-Group </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1211"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Copy In-Group </a></TD></TR>
<? } 
if (strlen($usergroups_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$usergroups_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=100000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show User Groups </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New User Group </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=999999&SUB=7&iframe=group_hourly_stats.php"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Group Hourly Report </a></TD></TR>
<? } 
if (strlen($remoteagent_hh) > 1) { 
	?>
<TR class='no-ul' BGCOLOR=<?=$remoteagent_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Off-Hook Agents </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add New Off-Hook Agents </a></TD></TR>
<? } 

if (strlen($admin_hh) > 1) { 
	if ($sh=='times') {$times_sh="bgcolor=\"$times_color\""; $times_fc="$times_font";} # hard teal
		else {$times_sh=''; $times_fc='BLACK';}
	if ($sh=='phones') {$phones_sh="bgcolor=\"$server_color\""; $phones_fc="$phones_font";} # pink
		else {$phones_sh=''; $phones_fc='BLACK';}
	if ($sh=='server') {$server_sh="bgcolor=\"$server_color\""; $server_fc="$server_font";} # pink
		else {$server_sh=''; $server_fc='BLACK';}
	if ($sh=='conference') {$conference_sh="bgcolor=\"$server_color\""; $conference_fc="$server_font";} # pink
		else {$conference_sh=''; $conference_fc='BLACK';}
	if ($sh=='settings') {$settings_sh="bgcolor=\"$settings_color\""; $settings_fc="$settings_font";} # pink
		else {$settings_sh=''; $settings_fc='BLACK';}
	if ($sh=='status') {$status_sh="bgcolor=\"$status_color\""; $status_fc="$status_font";} # pink
		else {$status_sh=''; $status_fc='BLACK';}

	?>
<TR class='no-ul' BGCOLOR=<?=$admin_color ?>>
<TD height=20 ALIGN=center <?=$times_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=100000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$times_fc ?> SIZE=<?=$header_font_size ?>> Call Times </a></TD>
<TD ALIGN=center <?=$phones_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=10000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$phones_fc ?> SIZE=<?=$header_font_size ?>> Phones </a></TD>
<TD ALIGN=center <?=$conference_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=1000000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$conference_fc ?> SIZE=<?=$header_font_size ?>> Conferences </a></TD>
<TD ALIGN=center <?=$server_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=100000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$server_fc ?> SIZE=<?=$header_font_size ?>> Servers </a></TD>
<TD ALIGN=center <?=$settings_sh ?> COLSPAN=1><a href="<? echo $PHP_SELF ?>?ADD=311111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$settings_fc ?> SIZE=<?=$header_font_size ?>> System Settings </a></TD>
<TD ALIGN=center <?=$status_sh ?> COLSPAN=2><a href="<? echo $PHP_SELF ?>?ADD=321111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=<?=$status_fc ?> SIZE=<?=$header_font_size ?>> System Statuses </a></TD>
</TR>
	<?
	if (strlen($times_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$times_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=100000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Call Times </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Call Time </a> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show State Call Times </a> &nbsp; &nbsp; |  &nbsp; &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New State Call Time </a> &nbsp; </TD></TR>
		<? } 
	if (strlen($phones_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$phones_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Phones </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Phone </a></TD></TR>
		<? }
	if (strlen($conference_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$conference_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<a href="<? echo $PHP_SELF ?>?ADD=1000000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Conferences </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=1111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Conference </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=10000000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show OSDial Conferences </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=11111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New OSDial Conference </a></TD></TR>
		<? }
	if (strlen($server_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$server_color ?>>
		<TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<a href="<? echo $PHP_SELF ?>?ADD=100000000000"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Show Servers </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Add A New Server </a> | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=399111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>>Archive Server</a> | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=399211111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>>QC Servers</a>
		</TD></TR>
	<?}
	if (strlen($settings_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$settings_color ?>>
		<TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<a href="<? echo $PHP_SELF ?>?ADD=311111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> System Settings </a></TD>
	</TR>
	<?}
	if (strlen($status_sh) > 1) { 
		?>
	<TR class='no-ul' BGCOLOR=<?=$status_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10>&nbsp;<a href="<? echo $PHP_SELF ?>?ADD=321111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> System Statuses </a> &nbsp; | &nbsp; <a href="<? echo $PHP_SELF ?>?ADD=331111111111111"><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>> Status Categories </a></TD></TR>
	<?}

### Do nothing if admin has no permissions
if($LOGast_admin_access < 1) 
	{
	$ADD='99999999999999999999';
	echo "</TABLE></center>\n";
	echo "<font color=red>You are not authorized to view this page. Please go back.</font>\n";
	}

} 
if (strlen($reports_hh) > 1) { 
	?>
<TR BGCOLOR=<?=$reports_color ?>><TD height=20 ALIGN=LEFT COLSPAN=10><FONT FACE="ARIAL,HELVETICA" COLOR=BLACK SIZE=<?=$subheader_font_size ?>><B> &nbsp; </B></TD></TR>
<? } ?>


<TR><TD ALIGN=LEFT COLSPAN=10 HEIGHT=1 BGCOLOR=#666666></TD></TR>
<TR><TD ALIGN=LEFT COLSPAN=10>
<? 
######################### HTML HEADER END #######################################



?>
