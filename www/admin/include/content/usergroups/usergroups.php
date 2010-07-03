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


######################
# ADD=111111 display the ADD NEW AGENTS GROUP SCREEN
######################

if ($ADD==111111) {
    if ($LOGmodify_usergroups==1 and $LOG['allowed_campaignsALL'] > 0) {
        echo "<TABLE align=center><TR><TD>\n";
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

        echo "<center><br><font color=$default_text size=+1>ADD NEW AGENTS GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=211111>\n";
        echo "<TABLE width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Group: </td><td align=left>";
        if ($LOG['multicomp_admin'] > 0) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "<select name=company_id>\n";
            foreach ($comps as $comp) {
                echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "</select>\n";
        } elseif ($LOG['multicomp']>0) {
            echo "<input type=hidden name=company_id value=$LOG[company_id]>";
            #echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
        }
        echo "<input type=text name=user_group size=15 maxlength=20> (no spaces or punctuation)$NWB#osdial_user_groups-user_group$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=group_name size=40 maxlength=40> (description of group)$NWB#osdial_user_groups-group_name$NWE</td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";
    } elseif ($LOG['allowed_campaignsALL'] < 1) {
        echo "<br><font color=red>USER GROUP NOT MODIFIED - You may only view your User Group resources.</font><br>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=211111 adds new user group to the system
######################

if ($ADD==211111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
    $preuser_group = $user_group;
    if ($LOG['multicomp'] > 0) $preuser_group = (($company_id * 1) + 100) . $user_group;
	$stmt="SELECT count(*) from osdial_user_groups where user_group='$preuser_group';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>USER GROUP NOT ADDED - there is already a user group entry with this name</font>\n";}
	else
		{
		 if ( (strlen($user_group) < 2) or (strlen($group_name) < 2) )
			{
			 echo "<br><font color=red>USER GROUP NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Group name and description must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
            if ($LOG['multicomp'] > 0) $user_group = (($company_id * 1) + 100) . $user_group;
            $LOG['allowed_usergroupsSQL'] = rtrim($LOG['allowed_usergroupsSQL'],')');
            $LOG['allowed_usergroupsSQL'] .= ",'$user_group')";
            $LOG['allowed_usergroupsSTR'] .= "$user_group:";
			$stmt="INSERT INTO osdial_user_groups(user_group,group_name,allowed_campaigns) values('$user_group','$group_name','-ALL-CAMPAIGNS-');";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=$default_text>USER GROUP ADDED: $user_group</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW USER GROUP ENTRY     |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=100000;
}


######################
# ADD=411111 modify user group info in the system
######################

if ($ADD==411111) {
    if ($LOGmodify_usergroups==1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
        $preuser_group = $user_group;
        if ($LOG['multicomp'] > 0) $preuser_group = (($company_id * 1) + 100) . $user_group;

        if ($LOG['allowed_campaignsALL'] < 1) {
            echo "<br><font color=red>USER GROUP NOT MODIFIED - You may only view your User Group resources.</font><br>\n";
        } elseif ( (strlen($user_group) < 2) or (strlen($group_name) < 2) ) {
            echo "<br><font color=red>USER GROUP NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>Group name and description must be at least 2 characters in length</font><br>\n";
        } else {
            if ($LOG['multicomp'] > 0) $user_group = (($company_id * 1) + 100) . $user_group;
            $stmt  = "UPDATE osdial_user_groups SET user_group='%s',group_name='%s',allowed_campaigns='%s',";
            $stmt .= "view_agent_pause_summary='%s',export_agent_pause_summary='%s',view_agent_performance_detail='%s',export_agent_performance_detail='%s',";
            $stmt .= "view_agent_realtime='%s',view_agent_realtime_iax_barge='%s',view_agent_realtime_iax_listen='%s',view_agent_realtime_sip_barge='%s',view_agent_realtime_sip_listen='%s',view_agent_realtime_summary='%s',";
            $stmt .= "view_agent_stats='%s',view_agent_status='%s',view_agent_timesheet='%s',export_agent_timesheet='%s',view_campaign_call_report='%s',export_campaign_call_report='%s',";
            $stmt .= "view_campaign_recent_outbound_sales='%s',export_campaign_recent_outbound_sales='%s',view_ingroup_call_report='%s',export_ingroup_call_report='%s',";
            $stmt .= "view_lead_performance_campaign='%s',export_lead_performance_campaign='%s',view_lead_performance_list='%s',export_lead_performance_list='%s',";
            $stmt .= "view_lead_search='%s',view_lead_search_advanced='%s',export_lead_search_advanced='%s',view_list_cost_entry='%s',export_list_cost_entry='%s',";
            $stmt .= "view_server_performance='%s',view_server_times='%s',view_usergroup_hourly_stats='%s' ";
            $stmt .= "WHERE user_group='%s';";

            $stmt=sprintf($stmt,
                mres($user_group),mres($group_name),mres($campaigns_value),
                mres($view_agent_pause_summary),mres($export_agent_pause_summary),mres($view_agent_performance_detail),mres($export_agent_performance_detail),
                mres($view_agent_realtime),mres($view_agent_realtime_iax_barge),mres($view_agent_realtime_iax_listen),mres($view_agent_realtime_sip_barge),mres($view_agent_realtime_sip_listen),mres($view_agent_realtime_summary),
                mres($view_agent_stats),mres($view_agent_status),mres($view_agent_timesheet),mres($export_agent_timesheet),mres($view_campaign_call_report),mres($export_campaign_call_report),
                mres($view_campaign_recent_outbound_sales),mres($export_campaign_recent_outbound_sales),mres($view_ingroup_call_report),mres($export_ingroup_call_report),
                mres($view_lead_performance_campaign),mres($export_lead_performance_campaign),mres($view_lead_performance_list),mres($export_lead_performance_list),
                mres($view_lead_search),mres($view_lead_search_advanced),mres($export_lead_search_advanced),mres($view_list_cost_entry),
                mres($export_list_cost_entry),mres($view_server_performance),mres($view_server_times),mres($view_usergroup_hourly_stats),
                mres($OLDuser_group));
            $rslt=mysql_query($stmt, $link);


            echo "<br><B><font color=$default_text>USER GROUP MODIFIED</font></B>\n";

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY USER GROUP ENTRY     |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $ADD=311111;	# go to user group modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=511111 confirmation before deletion of user group record
######################

if ($ADD==511111) {
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

    if ($LOG['allowed_campaignsALL'] < 1) {
        echo "<br><font color=red>USER GROUP NOT DELETED - You may only view your User Group resources.</font><br>\n";
    } elseif ( (strlen($user_group) < 2) or ($LOGdelete_user_groups < 1) ) {
        echo "<br><font color=red>USER GROUP NOT DELETED - Please go back and look at the data you entered\n";
        echo "<br>User_group be at least 2 characters in length</font><br>\n";
    } else {
        echo "<br><B><font color=$default_text>USER GROUP DELETION CONFIRMATION: $user_group</B>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=611111&user_group=$user_group&CoNfIrM=YES\">Click here to delete user group $user_group</a></font><br><br><br>\n";
    }

    $ADD='311111';		# go to user group modification below
}

######################
# ADD=611111 delete user group record
######################

if ($ADD==611111) {
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

    if ($LOG['allowed_campaignsALL'] < 1) {
        echo "<br><font color=red>USER GROUP NOT DELETED - You may only view your User Group resources.</font><br>\n";
    } elseif ( (strlen($user_group) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_user_groups < 1) ) {
        echo "<br><font color=red>USER GROUP NOT DELETED - Please go back and look at the data you entered\n";
        echo "<br>User_group be at least 2 characters in length</font><br>\n";
    } else {
        $stmt="DELETE from osdial_user_groups where user_group='$user_group' limit 1;";
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!DELETING USRGROUP!!|$PHP_AUTH_USER|$ip|user_group='$user_group'|\n");
            fclose($fp);
        }
        echo "<br><B><font color=$default_text>USER GROUP DELETION COMPLETED: $user_group</font></B>\n";
        echo "<br><br>\n";
    }

    $ADD='100000';		# go to user group list
}

######################
# ADD=311111 modify user group info in the system
######################

if ($ADD==311111)
{
	if ($LOGmodify_usergroups==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$stmt="SELECT * from osdial_user_groups where user_group='$user_group';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$user_group =		$row[0];
	$group_name =		$row[1];
    $view_agent_pause_summary = $row[3];
    $export_agent_pause_summary = $row[4];
    $view_agent_performance_detail = $row[5];
    $export_agent_performance_detail = $row[6];
    $view_agent_realtime = $row[7];
    $view_agent_realtime_iax_barge = $row[8];
    $view_agent_realtime_iax_listen = $row[9];
    $view_agent_realtime_sip_barge = $row[10];
    $view_agent_realtime_sip_listen = $row[11];
    $view_agent_realtime_summary = $row[12];
    $view_agent_stats = $row[13];
    $view_agent_status = $row[14];
    $view_agent_timesheet = $row[15];
    $export_agent_timesheet = $row[16];
    $view_campaign_call_report = $row[17];
    $export_campaign_call_report = $row[18];
    $view_campaign_recent_outbound_sales = $row[19];
    $export_campaign_recent_outbound_sales = $row[20];
    $view_ingroup_call_report = $row[21];
    $export_ingroup_call_report = $row[22];
    $view_lead_performance_campaign = $row[23];
    $export_lead_performance_campaign = $row[24];
    $view_lead_performance_list = $row[25];
    $export_lead_performance_list = $row[26];
    $view_lead_search = $row[27];
    $view_lead_search_advanced = $row[28];
    $export_lead_search_advanced = $row[29];
    $view_list_cost_entry = $row[30];
    $export_list_cost_entry = $row[31];
    $view_server_performance = $row[32];
    $view_server_times = $row[33];
    $view_usergroup_hourly_stats = $row[34];
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<center><br><font color=$default_text size=+1>MODIFY A USER GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411111>\n";
	echo "<input type=hidden name=OLDuser_group value=\"$user_group\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Group: </td><td align=left>";
    if ($LOG['multicomp_admin'] > 0) {
        $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
        echo "<select name=company_id>\n";
        foreach ($comps as $comp) {
            $csel = "";
            if ((substr($user_group,0,3) * 1 - 100) == $comp['id']) $csel = "selected";
            echo "<option value=$comp[id] $csel>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
        }
        echo "</select>\n";
    } elseif ($LOG['multicomp']>0) {
        echo "<input type=hidden name=company_id value=$LOG[company_id]>";
        #echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
    }
    echo "<input type=text name=user_group size=15 maxlength=20 value=\"";
    if ($LOG['multicomp']>0 and preg_match($LOG['companiesRE'],$user_group)) {
        echo substr($user_group,3);
    } else {
        echo $user_group;
    }
    echo "\">";
    echo " (no spaces or punctuation)$NWB#osdial_user_groups-user_group$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=group_name size=40 maxlength=40 value=\"$group_name\"> (description of group)$NWB#osdial_user_groups-group_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Allowed Campaigns: </td><td align=left>\n";
	echo "$campaigns_list";
	echo "$NWB#osdial_user_groups-allowed_campaigns$NWE</td></tr>\n";

	echo "<tr bgcolor=$oddrows>\n";
    echo "  <td colspan=2 align=center>";
    echo "    <br>\n";
    echo "    <table bgcolor=grey width=50%>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td align=center colspan=4>Agent / UserGroup Reports</td>\n";
    echo "      </tr>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td width=60% align=center>REPORT NAME</td>\n";
    echo "        <td width=15% align=center>VIEW</td>\n";
    echo "        <td width=15% align=center>EXPORT</td>\n";
    echo "        <td width=10% align=center></td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime\">\n";
    echo "        <td align=left>Realtime</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_realtime><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_realtime$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime - IAX Barge\">\n";
    echo "        <td align=left>Realtime - IAX Barge</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_realtime_iax_barge><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_iax_barge) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_realtime_iax_barge$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime - IAX Listen\">\n";
    echo "        <td align=left>Realtime - IAX Listen</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_realtime_iax_listen><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_iax_listen) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_realtime_iax_listen$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime - SIP Barge\">\n";
    echo "        <td align=left>Realtime - SIP Barge</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_realtime_sip_barge><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_sip_barge) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_realtime_sip_barge$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime - SIP Listen\">\n";
    echo "        <td align=left>Realtime - SIP Listen</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_realtime_sip_listen><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_sip_listen) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_realtime_sip_listen$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime Summary\">\n";
    echo "        <td align=left>Realtime Summary</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_realtime_summary><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_summary) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_realtime_summary$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Pause Summary\">\n";
    echo "        <td align=left>Agent Pause Summary</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_pause_summary><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_pause_summary) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_agent_pause_summary><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_agent_pause_summary) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_pause_summary$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Performance Detail\">\n";
    echo "        <td align=left>Agent Performance Detail</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_performance_detail><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_performance_detail) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_agent_performance_detail><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_agent_performance_detail) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_performance_detail$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Stats\">\n";
    echo "        <td align=left>Agent Stats</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_stats><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_stats) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_stats$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Status\">\n";
    echo "        <td align=left>Agent Status</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_status><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_status) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_status$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Timesheet\">\n";
    echo "        <td align=left>Agent Timesheet</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_agent_timesheet><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_timesheet) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_agent_timesheet><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_agent_timesheet) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_agent_timesheet$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"UserGroup Hourly Stats\">\n";
    echo "        <td align=left>UserGroup Hourly Stats</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_usergroup_hourly_stats><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_usergroup_hourly_stats) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_usergroup_hourly_stats$NWE</td>\n";
    echo "      </tr>\n";
    echo "    </table>\n";
    echo "  </td>\n";
    echo "</tr>\n";

	echo "<tr bgcolor=$oddrows>\n";
    echo "  <td colspan=2 align=center>";
    echo "    <br>\n";
    echo "    <table bgcolor=grey width=50%>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td align=center colspan=4>InGroup / Closer Reports</td>\n";
    echo "      </tr>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td width=60% align=center>REPORT NAME</td>\n";
    echo "        <td width=15% align=center>VIEW</td>\n";
    echo "        <td width=15% align=center>EXPORT</td>\n";
    echo "        <td width=10% align=center></td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"InGroup Call Report\">\n";
    echo "        <td align=left>InGroup Call Report</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_ingroup_call_report><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_ingroup_call_report) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_ingroup_call_report><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_ingroup_call_report) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_ingroup_call_report$NWE</td>\n";
    echo "        </td>\n";
    echo "      </tr>\n";
    echo "    </table>\n";
    echo "  </td>\n";
    echo "</tr>\n";

	echo "<tr bgcolor=$oddrows>\n";
    echo "  <td colspan=2 align=center>";
    echo "    <br>\n";
    echo "    <table bgcolor=grey width=50%>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td align=center colspan=4>Campaign Reports</td>\n";
    echo "      </tr>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td width=60% align=center>REPORT NAME</td>\n";
    echo "        <td width=15% align=center>VIEW</td>\n";
    echo "        <td width=15% align=center>EXPORT</td>\n";
    echo "        <td width=10% align=center></td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Campaign Call Report\">\n";
    echo "        <td align=left>Campaign Call Report</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_campaign_call_report><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_campaign_call_report) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_campaign_call_report><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_campaign_call_report) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_campaign_call_report$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Campaign Recent Outbound Sales\">\n";
    echo "        <td align=left>Campaign Recent Outbound Sales</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_campaign_recent_outbound_sales><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_campaign_recent_outbound_sales) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_campaign_recent_outbound_sales><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_campaign_recent_outbound_sales) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_campaign_recent_outbound_sales$NWE</td>\n";
    echo "      </tr>\n";
    echo "    </table>\n";
    echo "  </td>\n";
    echo "</tr>\n";

	echo "<tr bgcolor=$oddrows>\n";
    echo "  <td colspan=2 align=center>";
    echo "    <br>\n";
    echo "    <table bgcolor=grey width=50%>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td align=center colspan=4>List / Lead Reports</td>\n";
    echo "      </tr>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td width=60% align=center>REPORT NAME</td>\n";
    echo "        <td width=15% align=center>VIEW</td>\n";
    echo "        <td width=15% align=center>EXPORT</td>\n";
    echo "        <td width=10% align=center></td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Lead Performance by Campaign\">\n";
    echo "        <td align=left>Lead Performance by Campaign</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_lead_performance_campaign><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_lead_performance_campaign) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_lead_performance_campaign><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_lead_performance_campaign) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_lead_performance_campaign$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Lead Performance by List\">\n";
    echo "        <td align=left>Lead Performance by List</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_lead_performance_list><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_lead_performance_list) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_lead_performance_list><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_lead_performance_list) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_lead_performance_list$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Lead Search - Basic\">\n";
    echo "        <td align=left>Lead Search - Basic</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_lead_search><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_lead_search) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_lead_search$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Lead Search - Advanced\">\n";
    echo "        <td align=left>Lead Search - Advanced</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_lead_search_advanced><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_lead_search_advanced) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_lead_search_advanced><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_lead_search_advanced) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_lead_search_advanced$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"List Cost by Entry Date\">\n";
    echo "        <td align=left>List Cost by Entry Date</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_list_cost_entry><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_list_cost_entry) . "</select></td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=export_list_cost_entry><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_list_cost_entry) . "</select></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_list_cost_entry$NWE</td>\n";
    echo "      </tr>\n";
    echo "    </table>\n";
    echo "  </td>\n";
    echo "</tr>\n";

	echo "<tr bgcolor=$oddrows>\n";
    echo "  <td colspan=2 align=center>";
    echo "    <br>\n";
    echo "    <table bgcolor=grey width=50%>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td align=center colspan=4>Server Reports</td>\n";
    echo "      </tr>\n";
    echo "      <tr class=tabheader>\n";
    echo "        <td width=60% align=center>REPORT NAME</td>\n";
    echo "        <td width=15% align=center>VIEW</td>\n";
    echo "        <td width=15% align=center>EXPORT</td>\n";
    echo "        <td width=10% align=center></td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Server Performance\">\n";
    echo "        <td align=left>Server Performance</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_server_performance><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_server_performance) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_server_performance$NWE</td>\n";
    echo "      </tr>\n";
	echo "      <tr bgcolor=$oddrows class=\"row font2\" title=\"Server Times\">\n";
    echo "        <td align=left>Server Times</td>\n";
    echo "        <td align=center class=tabinput><select size=1 name=view_server_times><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_server_times) . "</select></td>\n";
    echo "        <td align=center class=tabinput></td>\n";
    echo "        <td align=center>$NWB#osdial_user_groups-view_server_times$NWE</td>\n";
    echo "      </tr>\n";
    echo "    </table>\n";
    echo "    <br>\n";
    echo "  </td>\n";
    echo "</tr>\n";

	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";


	### list of users in this user group

		$active_confs = 0;
		$stmt="SELECT user,full_name,user_level from osdial_users where user_group='$user_group'";
		$rsltx=mysql_query($stmt, $link);
		$users_to_print = mysql_num_rows($rsltx);

		echo "<center>\n";
		echo "<br><font color=$default_text size=4><b>AGENTS WITHIN THIS GROUP</b></font><br>\n";
		echo "<table bgcolor=grey width=400 cellspacing=1>\n";
		echo "  <tr class=tabheader>\n";
        echo "    <td>USER</td>\n";
        echo "    <td>FULL NAME</td>\n";
        echo "    <td>LEVEL</td>\n";
        echo "  </tr>\n";

		$o=0;
		while ($users_to_print > $o) 
		{
			$rowx=mysql_fetch_row($rsltx);
			$o++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}

		echo "  <tr $bgcolor class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=3&user=$rowx[0]');\">\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=3&user=$rowx[0]\">" . mclabel($rowx[0]) . "</a></td>\n";
		echo "    <td>$rowx[1]</td>\n";
		echo "    <td>$rowx[2]</td>\n";
		echo "  </tr>\n";
		}

    echo "  <tr class=tabfooter>\n";
    echo "    <td colspan=3></td>\n";
    echo "  </tr>\n";
	echo "</table></center><br>\n";



	echo "<br><br><a href=\"$PHP_SELF?ADD=8111&user_group=$user_group\">Click here to see all CallBack Holds in this user group</a><BR><BR>\n";

	if ($LOGdelete_user_groups > 0 and $LOG['allowed_campaignsALL'] > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=511111&user_group=$user_group\">DELETE THIS USER GROUP</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}

######################
# ADD=8111 find all callbacks on hold within a user group
######################
if ($ADD==8111)
{
	if ($LOGmodify_usergroups==1)
	{
		if ($SUB==89)
		{
		$stmt="UPDATE osdial_callbacks SET status='INACTIVE' where user_group='$user_group' and status='LIVE' and callback_time < '$past_month_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>user group($user_group) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899)
		{
		$stmt="UPDATE osdial_callbacks SET status='INACTIVE' where user_group='$user_group' and status='LIVE' and callback_time < '$past_week_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>user group($user_group) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
	$CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=8111&SUB=89&user_group=$user_group\"><font color=$default_text>Remove LIVE Callbacks older than one month for this user group</font></a><BR><a href=\"$PHP_SELF?ADD=8111&SUB=899&user_group=$user_group\"><font color=$default_text>Remove LIVE Callbacks older than one week for this user group</font></a><BR>";

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$CBquerySQLwhere = "and user_group='$user_group'";

echo "<br><font color=$default_text> USER GROUP CALLBACK HOLD LISTINGS: $list_id</font>\n";
$oldADD = "ADD=8111&user_group=$user_group";
$ADD='82';
}

######################
# ADD=100000 display all user groups
######################
if ($ADD==100000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$stmt=sprintf("SELECT * from osdial_user_groups where user_group IN %s order by user_group", $LOG['allowed_usergroupsSQL']);
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=$default_text size=+1>USER GROUPS</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>NAME</td>\n";
echo "    <td>DESCRIPTION</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}
		echo "  <tr $bgcolor class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=311111&user_group=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=311111&user_group=$row[0]\">" . mclabel($row[0]) . "</a></td>\n";
		echo "    <td>$row[1]</td>\n";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=311111&user_group=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=3></td>\n";
echo "  </tr>\n";
echo "</TABLE></center>\n";
}



?>
