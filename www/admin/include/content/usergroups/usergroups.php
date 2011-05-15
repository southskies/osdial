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
    if (($LOG['modify_usergroups'] == 1 and $LOG['allowed_campaignsALL'] > 0) OR (preg_match('/^(admin|6666)$/',$PHP_AUTH_USER) and $LOG['user_level']>=9)) {
        echo "<center>\n";
        echo "  <br><font color=$default_text size=+1>ADD NEW AGENTS GROUP</font><br><br>\n";
        echo "  <form action=$PHP_SELF method=POST>\n";
        echo "  <input type=hidden name=ADD value=211111>\n";
        echo "  <table width=$section_width cellspacing=3>\n";
        echo "    <tr bgcolor=$oddrows>\n";
        echo "      <td width=40% align=right>Group: </td>\n";
        echo "      <td align=left>\n";
        if ($LOG['multicomp_admin'] > 0) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "        <select name=company_id>\n";
            foreach ($comps as $comp) {
                echo "          <option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "        </select>\n";
        } elseif ($LOG['multicomp']>0) {
            echo "        <input type=hidden name=company_id value=$LOG[company_id]>";
        }
        echo "        <input type=text name=user_group size=15 maxlength=20> $NWB#osdial_user_groups-user_group$NWE\n";
        echo "      </td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows>\n";
        echo "      <td align=right>Description: </td>\n";
        echo "      <td align=left><input type=text name=group_name size=40 maxlength=40> $NWB#osdial_user_groups-group_name$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "  </table>\n";
        echo "  </form>\n";
        echo "</center>\n";
    } elseif ($LOG['allowed_campaignsALL'] < 1) {
        echo "<br><font color=red>USER GROUP NOT MODIFIED - You may only view your User Group resources.</font><br>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=211111 adds new user group to the system
######################

if ($ADD==211111) {
    $preuser_group = $user_group;
    if ($LOG['multicomp'] > 0) $preuser_group = (($company_id * 1) + 100) . $user_group;
    $stmt="SELECT count(*) FROM osdial_user_groups WHERE user_group='$preuser_group';";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    if ($row[0] > 0) {
        echo "<br><font color=red>USER GROUP NOT ADDED - there is already a user group entry with this name</font>\n";
    } else {
         if (strlen($user_group) < 2 or strlen($group_name) < 2) {
             echo "<br><font color=red>USER GROUP NOT ADDED - Please go back and look at the data you entered\n";
             echo "<br>Group name and description must be at least 2 characters in length</font><br>\n";
         } else {
            if ($LOG['multicomp'] > 0) $user_group = (($company_id * 1) + 100) . $user_group;
            $LOG['allowed_usergroupsSQL'] = rtrim($LOG['allowed_usergroupsSQL'],')');
            $LOG['allowed_usergroupsSQL'] .= ",'$user_group')";
            $LOG['allowed_usergroupsSTR'] .= "$user_group:";
            $stmt="INSERT INTO osdial_user_groups (user_group,group_name,allowed_campaigns) VALUES ('$user_group','$group_name','-ALL-CAMPAIGNS-');";
            $rslt=mysql_query($stmt, $link);

            echo "<br><b><font color=$default_text>USER GROUP ADDED: $user_group</font></b>\n";

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
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
    if ($LOG['modify_usergroups'] == 1) {
        $preuser_group = $user_group;
        if ($LOG['multicomp'] > 0) $preuser_group = (($company_id * 1) + 100) . $user_group;

        if ($LOG['allowed_campaignsALL'] < 1 and !(preg_match('/^(admin|6666)$/',$PHP_AUTH_USER) and $LOG['user_level']>=9)) {
            echo "<br><font color=red>USER GROUP NOT MODIFIED - You may only view your User Group resources.</font><br>\n";
        } elseif ( (strlen($user_group) < 2) or (strlen($group_name) < 2) ) {
            echo "<br><font color=red>USER GROUP NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>Group name and description must be at least 2 characters in length</font><br>\n";
        } else {
            if ($LOG['multicomp'] > 0) $user_group = (($company_id * 1) + 100) . $user_group;

            if ($view_agent_realtime == 0) {
                $view_agent_realtime_iax_barge = 0;
                $view_agent_realtime_iax_listen = 0;
                $view_agent_realtime_sip_barge = 0;
                $view_agent_realtime_sip_listen = 0;
            }
            if ($view_agent_pause_summary == 0) $export_agent_pause_summary = 0;
            if ($view_agent_performance_detail == 0) $export_agent_performance_detail = 0;
            if ($view_agent_timesheet == 0) $export_agent_timesheet = 0;
            if ($view_ingroup_call_report == 0) $export_ingroup_call_report = 0;
            if ($view_campaign_call_report == 0) $export_campaign_call_report = 0;
            if ($view_campaign_recent_outbound_sales == 0) $export_campaign_recent_outbound_sales = 0;
            if ($view_lead_performance_campaign == 0) $export_lead_performance_campaign = 0;
            if ($view_lead_performance_list == 0) $export_lead_performance_list = 0;
            if ($view_lead_search_advanced == 0) $export_lead_search_advanced = 0;
            if ($view_list_cost_entry == 0) $export_list_cost_entry = 0;

            $stmt  = "UPDATE osdial_user_groups SET user_group='%s',group_name='%s',allowed_campaigns='%s',allowed_ingroups='%s',allowed_scripts='%s',allowed_email_templates='%s',";
            $stmt .= "view_agent_pause_summary='%s',export_agent_pause_summary='%s',view_agent_performance_detail='%s',export_agent_performance_detail='%s',";
            $stmt .= "view_agent_realtime='%s',view_agent_realtime_iax_barge='%s',view_agent_realtime_iax_listen='%s',view_agent_realtime_sip_barge='%s',view_agent_realtime_sip_listen='%s',view_agent_realtime_summary='%s',";
            $stmt .= "view_agent_stats='%s',view_agent_status='%s',view_agent_timesheet='%s',export_agent_timesheet='%s',view_campaign_call_report='%s',export_campaign_call_report='%s',";
            $stmt .= "view_campaign_recent_outbound_sales='%s',export_campaign_recent_outbound_sales='%s',view_ingroup_call_report='%s',export_ingroup_call_report='%s',";
            $stmt .= "view_lead_performance_campaign='%s',export_lead_performance_campaign='%s',view_lead_performance_list='%s',export_lead_performance_list='%s',";
            $stmt .= "view_lead_search='%s',view_lead_search_advanced='%s',export_lead_search_advanced='%s',view_list_cost_entry='%s',export_list_cost_entry='%s',";
            $stmt .= "view_server_performance='%s',view_server_times='%s',view_usergroup_hourly_stats='%s' ";
            $stmt .= "WHERE user_group='%s';";

            $campaigns_value = ' ' . implode(' ', $campaigns_values) . ' -';
            $ingroups_value = ' ' . implode(' ', $ingroups_values) . ' -';
            $scripts_value = ' ' . implode(' ', $scripts_values) . ' -';
            $emails_value = ' ' . implode(' ', $emails_values) . ' -';
            if (preg_match('/ -ALL-CAMPAIGNS- /',$campaigns_value)) $campaigns_value = ' -ALL-CAMPAIGNS- -';
            if (preg_match('/ -ALL-INGROUPS- /',$ingroups_value)) $ingroups_value = ' -ALL-INGROUPS- -';
            if (preg_match('/ -ALL-SCRIPTS- /',$scripts_value)) $scripts_value = ' -ALL-SCRIPTS- -';
            if (preg_match('/ -ALL-EMAIL-TEMPLATES- /',$emails_value)) $emails_value = ' -ALL-EMAIL-TEMPLATES- -';

            $stmt=sprintf($stmt,
                mres($user_group),mres($group_name),mres($campaigns_value),mres($ingroups_value),mres($scripts_value),mres($emails_value),
                mres($view_agent_pause_summary),mres($export_agent_pause_summary),mres($view_agent_performance_detail),mres($export_agent_performance_detail),
                mres($view_agent_realtime),mres($view_agent_realtime_iax_barge),mres($view_agent_realtime_iax_listen),mres($view_agent_realtime_sip_barge),mres($view_agent_realtime_sip_listen),mres($view_agent_realtime_summary),
                mres($view_agent_stats),mres($view_agent_status),mres($view_agent_timesheet),mres($export_agent_timesheet),mres($view_campaign_call_report),mres($export_campaign_call_report),
                mres($view_campaign_recent_outbound_sales),mres($export_campaign_recent_outbound_sales),mres($view_ingroup_call_report),mres($export_ingroup_call_report),
                mres($view_lead_performance_campaign),mres($export_lead_performance_campaign),mres($view_lead_performance_list),mres($export_lead_performance_list),
                mres($view_lead_search),mres($view_lead_search_advanced),mres($export_lead_search_advanced),mres($view_list_cost_entry),
                mres($export_list_cost_entry),mres($view_server_performance),mres($view_server_times),mres($view_usergroup_hourly_stats),
                mres($OLDuser_group));
            $rslt=mysql_query($stmt, $link);


            echo "<br><b><font color=$default_text>USER GROUP MODIFIED</font></b>\n";

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY USER GROUP ENTRY     |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $ADD=311111;    # go to user group modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=511111 confirmation before deletion of user group record
######################

if ($ADD==511111) {
    if ($LOG['allowed_campaignsALL'] < 1 and !(preg_match('/^(admin|6666)$/',$PHP_AUTH_USER) and $LOG['user_level']>=9)) {
        echo "<br><font color=red>USER GROUP NOT DELETED - You may only view your User Group resources.</font><br>\n";
    } elseif ( (strlen($user_group) < 2) or ($LOGdelete_user_groups < 1) ) {
        echo "<br><font color=red>USER GROUP NOT DELETED - Please go back and look at the data you entered\n";
        echo "<br>User_group be at least 2 characters in length</font><br>\n";
    } else {
        echo "<br><b><font color=$default_text>USER GROUP DELETION CONFIRMATION: $user_group</b>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=611111&user_group=$user_group&CoNfIrM=YES\">Click here to delete user group $user_group</a></font><br><br><br>\n";
    }
    $ADD='311111';        # go to user group modification below
}

######################
# ADD=611111 delete user group record
######################

if ($ADD==611111) {
    if ($LOG['allowed_campaignsALL'] < 1 and !(preg_match('/^(admin|6666)$/',$PHP_AUTH_USER) and $LOG['user_level']>=9)) {
        echo "<br><font color=red>USER GROUP NOT DELETED - You may only view your User Group resources.</font><br>\n";
    } elseif ( (strlen($user_group) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_user_groups < 1) ) {
        echo "<br><font color=red>USER GROUP NOT DELETED - Please go back and look at the data you entered\n";
        echo "<br>User_group be at least 2 characters in length</font><br>\n";
    } else {
        $stmt="DELETE FROM osdial_user_groups WHERE user_group='$user_group' LIMIT 1;";
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!DELETING USRGROUP!!|$PHP_AUTH_USER|$ip|user_group='$user_group'|\n");
            fclose($fp);
        }
        echo "<br><b><font color=$default_text>USER GROUP DELETION COMPLETED: $user_group</font></b>\n";
        echo "<br><br>\n";
    }
    $ADD='100000';        # go to user group list
}

######################
# ADD=311111 modify user group info in the system
######################

if ($ADD==311111) {
    if ($LOG['modify_usergroups'] == 1) {
        $stmt = sprintf("SELECT * FROM osdial_user_groups WHERE user_group IN %s AND user_group='%s';",$LOG['allowed_usergroupsSQL'],$user_group);
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $user_group = $row[0];
        $group_name = $row[1];
        $allowed_campaigns = $row[2];
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
        $allowed_scripts = $row[35];
        $allowed_email_templates = $row[36];
        $allowed_ingroups = $row[37];

        echo "<center>\n";
        echo "  <br><font color=$default_text size=+1>MODIFY A USER GROUP</font><br><br>\n";
        echo "  <form action=$PHP_SELF method=POST>\n";
        echo "  <input type=hidden name=ADD value=411111>\n";
        echo "  <input type=hidden name=OLDuser_group value=\"$user_group\">\n";


        echo "  <table width=$section_width cellspacing=3>\n";
        echo "    <tr bgcolor=$oddrows>\n";
        echo "      <td width=40% align=right>Group: </td>\n";
        echo "      <td align=left>\n";
        if ($LOG['multicomp_admin'] > 0) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "        <select name=company_id>\n";
            foreach ($comps as $comp) {
                $csel = "";
                if ((substr($user_group,0,3) * 1 - 100) == $comp['id']) $csel = "selected";
                echo "          <option value=$comp[id] $csel>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "        </select>\n";
        } elseif ($LOG['multicomp']>0) {
            echo "        <input type=hidden name=company_id value=$LOG[company_id]>\n";
        }
        $mcug = $user_group;
        if ($LOG['multicomp']>0 and preg_match($LOG['companiesRE'],$user_group)) $mcug = substr($user_group,3);
        echo "        <input type=text name=user_group size=15 maxlength=20 value=\"$mcug\">\n";
        echo "        $NWB#osdial_user_groups-user_group$NWE\n";
        echo "      </td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows>\n";
        echo "      <td align=right>Description: </td>\n";
        echo "      <td align=left><input type=text name=group_name size=40 maxlength=40 value=\"$group_name\"> $NWB#osdial_user_groups-group_name$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "  </table>\n";



        ### Allowed Campaigns ###
        $acampaigns = get_krh($link, 'osdial_campaigns', '*','',sprintf("campaign_id IN %s",$LOG['allowed_campaignsSQL']),'');
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center colspan=2>Allowed Campaigns</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td></td>\n";
        echo "      <td align=left>Campaign</td>\n";
        echo "    </tr>\n";
        $sel = '';
        if (preg_match('/ -ALL-CAMPAIGNS- /',$allowed_campaigns)) $sel='checked';
        echo "    <tr bgcolor=$oddrows class=\"row font1\">\n";
        echo "      <td align=center class=tabinput><input type=checkbox name=campaigns_values[] id=campaigns_values value=\"-ALL-CAMPAIGNS-\" $sel onclick=\"var ctmp=this.checked; for(var i=0;i<document.getElementsByName('campaigns_values[]').length;i++) { document.getElementsByName('campaigns_values[]')[i].checked=false; }; this.checked=ctmp;\"></td>\n";
        echo "      <td align=left><b><label for=campaigns_values>ALL-CAMPAIGNS -</label></b></td>\n";
        echo "    </tr>\n";
        foreach ($acampaigns as $campaign) {
            $sel = '';
            if (preg_match('/ ' . $campaign['campaign_id'] . ' /',$allowed_campaigns)) $sel='checked';
            echo "    <tr bgcolor=$oddrows class=\"row font1\">\n";
            echo "      <td align=center class=tabinput><input type=checkbox name=campaigns_values[] id=campaigns_values" . $campaign['campaign_id'] . " value=\"" . $campaign['campaign_id'] . "\" $sel onclick=\"document.getElementById('campaigns_values').checked=false;\"></td>\n";
            $ccstyle=''; if ($campaign['active']=='N') $ccstyle=' style="color:#800000;"';
            echo "      <td align=left $ccstyle><label for=campaigns_values" . $campaign['campaign_id'] . ">" . $campaign['campaign_id'] . ' - ' . $campaign['campaign_name'] . "</label></td>\n";
            echo "    </tr>\n";
        }
        echo "    <tr class=tabfooter><td align=center colspan=2 class=tabbutton nowrap><input type=submit name=SUBMIT value=SUBMIT>$NWB#osdial_user_groups-allowed_campaigns$NWE</td></tr>\n";
        echo "  </table>\n";



        ### Allowed Ingroups ###
        $aingroups = get_krh($link, 'osdial_inbound_groups', '*','',sprintf("group_id IN %s",$LOG['allowed_ingroupsSQL']),'');
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center colspan=2>Allowed InGroups</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td></td>\n";
        echo "      <td align=left>Inbound Group</td>\n";
        echo "    </tr>\n";
        $sel = '';
        if (preg_match('/ -ALL-INGROUPS- /',$allowed_ingroups)) $sel='checked';
        echo "    <tr bgcolor=$oddrows class=\"row font1\">\n";
        echo "      <td align=center class=tabinput><input type=checkbox name=ingroups_values[] id=ingroups_values value=\"-ALL-INGROUPS-\" $sel onclick=\"var igtmp=this.checked; for(var i=0;i<document.getElementsByName('ingroups_values[]').length;i++) { if (typeof(document.getElementsByName('ingroups_values[]')[i].checked)!='undefined') document.getElementsByName('ingroups_values[]')[i].checked=false; }; this.checked=igtmp;\"></td>\n";
        echo "      <td align=left><b><label for=ingroups_values>ALL-INGROUPS -</label></b></td>\n";
        echo "    </tr>\n";
        foreach ($aingroups as $ingroup) {
            $sel = '';
            if (preg_match('/ ' . $ingroup['group_id'] . ' /',$allowed_ingroups)) $sel='checked';
            if (preg_match('/^A2A/',$ingroup['group_id'])) {
                echo "<input type=hidden name=ingroups_values[] id=ingroups_values" . $ingroup['group_id'] . " value=\"" . $ingroup['group_id'] . "\">";
            } else {
                echo "    <tr bgcolor=$oddrows class=\"row font1\">\n";
                echo "      <td align=center class=tabinput><input type=checkbox name=ingroups_values[] id=ingroups_values" . $ingroup['group_id'] . " value=\"" . $ingroup['group_id'] . "\" $sel onclick=\"document.getElementById('ingroups_values').checked=false;\"></td>\n";
                $igstyle=''; if ($ingroup['active']=='N') $igstyle=' style="color:#800000;"';
                echo "      <td align=left $igstyle><label for=ingroups_values" . $ingroup['group_id'] . ">" . $ingroup['group_id'] . ' - ' . $ingroup['group_name'] . "</label></td>\n";
                echo "    </tr>\n";
            }
        }
        echo "    <tr class=tabfooter><td align=center colspan=2 class=tabbutton nowrap><input type=submit name=SUBMIT value=SUBMIT>$NWB#osdial_user_groups-allowed_ingroups$NWE</td></tr>\n";
        echo "  </table>\n";



        ### Allowed scripts ###
        $scripts = get_krh($link, 'osdial_scripts', '*','',sprintf("script_id IN %s",$LOG['allowed_scriptsSQL']),'');
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center colspan=2>Allowed Scripts</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td></td>\n";
        echo "      <td align=left>Script</td>\n";
        echo "    </tr>\n";
        $sel = '';
        if (preg_match('/ -ALL-SCRIPTS- /',$allowed_scripts)) $sel='checked';
        echo "    <tr bgcolor=$oddrows class=\"row font1\">\n";
        echo "      <td align=center class=tabinput><input type=checkbox name=scripts_values[] id=scripts_values value=\"-ALL-SCRIPTS-\" $sel onclick=\"var ctmp=this.checked; for(var i=0;i<document.getElementsByName('scripts_values[]').length;i++) { document.getElementsByName('scripts_values[]')[i].checked=false; }; this.checked=ctmp;\"></td>\n";
        echo "      <td align=left><b><label for=scripts_values>ALL-SCRIPTS -</label></b></td>\n";
        echo "    </tr>\n";
        foreach ($scripts as $script) {
            $sel = '';
            if (preg_match('/ ' . $script['script_id'] . ' /',$allowed_scripts)) $sel='checked';
            echo "    <tr bgcolor=$oddrows class=\"row font1\">\n";
            echo "      <td align=center class=tabinput><input type=checkbox name=scripts_values[] id=scripts_values" . $script['script_id'] . " value=\"" . $script['script_id'] . "\" $sel onclick=\"document.getElementById('scripts_values').checked=false;\"></td>\n";
            $scstyle=''; if ($script['active']=='N') $scstyle=' style="color:#800000;"';
            echo "      <td align=left $scstyle><label for=scripts_values" . $script['script_id'] . ">" . $script['script_id'] . ' - ' . $script['script_name'] . "</label></td>\n";
            echo "    </tr>\n";
        }
        echo "    <tr class=tabfooter><td align=center colspan=2 class=tabbutton nowrap><input type=submit name=SUBMIT value=SUBMIT>$NWB#osdial_user_groups-allowed_scripts$NWE</td></tr>\n";
        echo "  </table>\n";


        if (file_exists($WeBServeRRooT . '/admin/include/content/scripts/email_templates.php')) {
            ### Allowed Email Templates ###
            $emails = get_krh($link, 'osdial_email_templates', '*','',sprintf("et_id IN %s",$LOG['allowed_email_templatesSQL']),'');
            echo "  <br>\n";
            echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
            echo "    <tr class=tabheader>\n";
            echo "      <td align=center colspan=2>Allowed Email Templates</td>\n";
            echo "    </tr>\n";
            echo "    <tr class=tabheader>\n";
            echo "      <td></td>\n";
            echo "      <td align=left>Template</td>\n";
            echo "    </tr>\n";
            $sel = '';
            if (preg_match('/ -ALL-EMAIL-TEMPLATES- /',$allowed_email_templates)) $sel='checked';
            echo "    <tr bgcolor=$oddrows class=\"row font1\">\n";
            echo "      <td align=center class=tabinput><input type=checkbox name=emails_values[] id=emails_values value=\"-ALL-EMAIL-TEMPLATES-\" $sel onclick=\"var ctmp=this.checked; for(var i=0;i<document.getElementsByName('emails_values[]').length;i++) { document.getElementsByName('emails_values[]')[i].checked=false; }; this.checked=ctmp;\"></td>\n";
            echo "      <td align=left><b><label for=emails_values>ALL-EMAIL-TEMPLATES - </label></b></td>\n";
            echo "    </tr>\n";
            foreach ($emails as $email) {
                $sel = '';
                if (preg_match('/ ' . $email['et_id'] . ' /',$allowed_email_templates)) $sel='checked';
                echo "    <tr bgcolor=$oddrows class=\"row font1\">\n";
                echo "      <td align=center class=tabinput><input type=checkbox name=emails_values[] id=emails_values" . $email['et_id'] . " value=\"" . $email['et_id'] . "\" $sel onclick=\"document.getElementById('emails_values').checked=false;\"></td>\n";
                $etstyle=''; if ($email['active']=='N') $etstyle=' style="color:#800000;"';
                echo "      <td align=left $etstyle><label for=emails_values" . $email['et_id'] . ">" . $email['et_id'] . ' - ' . $email['et_name'] . "</label></td>\n";
                echo "    </tr>\n";
            }
            echo "    <tr class=tabfooter><td align=center colspan=2 class=tabbutton nowrap><input type=submit name=SUBMIT value=SUBMIT>$NWB#osdial_user_groups-allowed_email_templates$NWE</td></tr>\n";
            echo "  </table>\n";
        }



        ### Agent / UserGroup Reports ###
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center colspan=4>Agent / UserGroup Reports</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td width=60% align=center>REPORT NAME</td>\n";
        echo "      <td width=15% align=center>VIEW</td>\n";
        echo "      <td width=15% align=center>EXPORT</td>\n";
        echo "      <td width=10% align=center></td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime\">\n";
        echo "      <td align=left>Realtime</td>\n";
        echo "      <td align=center><select size=1 name=view_agent_realtime><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime) . "</select></td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_realtime$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime - IAX Barge\">\n";
        echo "      <td align=left>Realtime - IAX Barge</td>\n";
        echo "      <td align=center>\n";
        if ($view_agent_realtime) {
            echo "        <select size=1 name=view_agent_realtime_iax_barge><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_iax_barge) . "</select>\n";
        } else {
            echo "        <input type=hidden name=view_agent_realtime_iax_barge value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_realtime_iax_barge$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime - IAX Listen\">\n";
        echo "      <td align=left>Realtime - IAX Listen</td>\n";
        echo "      <td align=center>\n";
        if ($view_agent_realtime) {
            echo "        <select size=1 name=view_agent_realtime_iax_listen><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_iax_listen) . "</select>\n";
        } else {
            echo "        <input type=hidden name=view_agent_realtime_iax_listen value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_realtime_iax_listen$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime - SIP Barge\">\n";
        echo "      <td align=left>Realtime - SIP Barge</td>\n";
        echo "      <td align=center>\n";
        if ($view_agent_realtime) {
            echo "        <select size=1 name=view_agent_realtime_sip_barge><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_sip_barge) . "</select>\n";
        } else {
            echo "        <input type=hidden name=view_agent_realtime_sip_barge value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_realtime_sip_barge$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime - SIP Listen\">\n";
        echo "      <td align=left>Realtime - SIP Listen</td>\n";
        echo "      <td align=center>\n";
        if ($view_agent_realtime) {
            echo "        <select size=1 name=view_agent_realtime_sip_listen><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_sip_listen) . "</select>\n";
        } else {
            echo "        <input type=hidden name=view_agent_realtime_sip_listen value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_realtime_sip_listen$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Realtime Summary\">\n";
        echo "      <td align=left>Realtime Summary</td>\n";
        echo "      <td align=center><select size=1 name=view_agent_realtime_summary><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_realtime_summary) . "</select></td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_realtime_summary$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Pause Summary\">\n";
        echo "      <td align=left>Agent Pause Summary</td>\n";
        echo "      <td align=center><select size=1 name=view_agent_pause_summary><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_pause_summary) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_agent_pause_summary) {
            echo "        <select size=1 name=export_agent_pause_summary><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_agent_pause_summary) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_agent_pause_summary value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_pause_summary$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Performance Detail\">\n";
        echo "      <td align=left>Agent Performance Detail</td>\n";
        echo "      <td align=center><select size=1 name=view_agent_performance_detail><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_performance_detail) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_agent_performance_detail) {
            echo "        <select size=1 name=export_agent_performance_detail><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_agent_performance_detail) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_agent_performance_detail value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_performance_detail$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Stats\">\n";
        echo "      <td align=left>Agent Stats</td>\n";
        echo "      <td align=center><select size=1 name=view_agent_stats><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_stats) . "</select></td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_stats$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Status\">\n";
        echo "      <td align=left>Agent Status</td>\n";
        echo "      <td align=center><select size=1 name=view_agent_status><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_status) . "</select></td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_status$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Agent Timesheet\">\n";
        echo "      <td align=left>Agent Timesheet</td>\n";
        echo "      <td align=center><select size=1 name=view_agent_timesheet><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_agent_timesheet) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_agent_timesheet) {
            echo "        <select size=1 name=export_agent_timesheet><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_agent_timesheet) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_agent_timesheet value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_agent_timesheet$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"UserGroup Hourly Stats\">\n";
        echo "      <td align=left>UserGroup Hourly Stats</td>\n";
        echo "      <td align=center><select size=1 name=view_usergroup_hourly_stats><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_usergroup_hourly_stats) . "</select></td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_usergroup_hourly_stats$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabfooter><td align=center colspan=4 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "  </table>\n";



        ### InGroup / Closer Reports ###
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center colspan=4>InGroup / Closer Reports</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td width=60% align=center>REPORT NAME</td>\n";
        echo "      <td width=15% align=center>VIEW</td>\n";
        echo "      <td width=15% align=center>EXPORT</td>\n";
        echo "      <td width=10% align=center></td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"InGroup Call Report\">\n";
        echo "      <td align=left>InGroup Call Report</td>\n";
        echo "      <td align=center><select size=1 name=view_ingroup_call_report><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_ingroup_call_report) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_ingroup_call_report) {
            echo "        <select size=1 name=export_ingroup_call_report><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_ingroup_call_report) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_ingroup_call_report value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_ingroup_call_report$NWE</td>\n";
        echo "      </td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabfooter><td align=center colspan=4 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "  </table>\n";



        ### Campaign Reports ###
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center colspan=4>Campaign Reports</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td width=60% align=center>REPORT NAME</td>\n";
        echo "      <td width=15% align=center>VIEW</td>\n";
        echo "      <td width=15% align=center>EXPORT</td>\n";
        echo "      <td width=10% align=center></td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Campaign Call Report\">\n";
        echo "      <td align=left>Campaign Call Report</td>\n";
        echo "      <td align=center><select size=1 name=view_campaign_call_report><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_campaign_call_report) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_campaign_call_report) {
            echo "        <select size=1 name=export_campaign_call_report><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_campaign_call_report) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_campaign_call_report value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_campaign_call_report$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Campaign Recent Outbound Sales\">\n";
        echo "      <td align=left>Campaign Recent Outbound Sales</td>\n";
        echo "      <td align=center><select size=1 name=view_campaign_recent_outbound_sales><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_campaign_recent_outbound_sales) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_campaign_recent_outbound_sales) {
            echo "        <select size=1 name=export_campaign_recent_outbound_sales><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_campaign_recent_outbound_sales) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_campaign_recent_outbound_sales value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_campaign_recent_outbound_sales$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabfooter><td align=center colspan=4 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "  </table>\n";



        ### List / Lead Reports ###
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center colspan=4>List / Lead Reports</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td width=60% align=center>REPORT NAME</td>\n";
        echo "      <td width=15% align=center>VIEW</td>\n";
        echo "      <td width=15% align=center>EXPORT</td>\n";
        echo "      <td width=10% align=center></td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Lead Performance by Campaign\">\n";
        echo "      <td align=left>Lead Performance by Campaign</td>\n";
        echo "      <td align=center><select size=1 name=view_lead_performance_campaign><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_lead_performance_campaign) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_lead_performance_campaign) {
            echo "        <select size=1 name=export_lead_performance_campaign><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_lead_performance_campaign) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_lead_performance_campaign value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_lead_performance_campaign$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Lead Performance by List\">\n";
        echo "      <td align=left>Lead Performance by List</td>\n";
        echo "      <td align=center><select size=1 name=view_lead_performance_list><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_lead_performance_list) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_lead_performance_list) {
            echo "        <select size=1 name=export_lead_performance_list><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_lead_performance_list) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_lead_performance_list value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_lead_performance_list$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Lead Search - Basic\">\n";
        echo "      <td align=left>Lead Search - Basic</td>\n";
        echo "      <td align=center><select size=1 name=view_lead_search><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_lead_search) . "</select></td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_lead_search$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Lead Search - Advanced\">\n";
        echo "      <td align=left>Lead Search - Advanced</td>\n";
        echo "      <td align=center><select size=1 name=view_lead_search_advanced><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_lead_search_advanced) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_lead_search_advanced) {
            echo "        <select size=1 name=export_lead_search_advanced><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_lead_search_advanced) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_lead_search_advanced value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_lead_search_advanced$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"List Cost by Entry Date\">\n";
        echo "      <td align=left>List Cost by Entry Date</td>\n";
        echo "      <td align=center><select size=1 name=view_list_cost_entry><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_list_cost_entry) . "</select></td>\n";
        echo "      <td align=center>\n";
        if ($view_list_cost_entry) {
            echo "        <select size=1 name=export_list_cost_entry><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_list_cost_entry) . "</select>\n";
        } else {
            echo "        <input type=hidden name=export_list_cost_entry value=0>N\n";
        }
        echo "      </td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_list_cost_entry$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabfooter><td align=center colspan=4 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "  </table>\n";



        ### Server Reports ###
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=50% cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center colspan=4>Server Reports</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td width=60% align=center>REPORT NAME</td>\n";
        echo "      <td width=15% align=center>VIEW</td>\n";
        echo "      <td width=15% align=center>EXPORT</td>\n";
        echo "      <td width=10% align=center></td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Server Performance\">\n";
        echo "      <td align=left>Server Performance</td>\n";
        echo "      <td align=center><select size=1 name=view_server_performance><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_server_performance) . "</select></td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_server_performance$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr bgcolor=$oddrows class=\"row font2\" title=\"Server Times\">\n";
        echo "      <td align=left>Server Times</td>\n";
        echo "      <td align=center><select size=1 name=view_server_times><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_server_times) . "</select></td>\n";
        echo "      <td align=center></td>\n";
        echo "      <td align=center>$NWB#osdial_user_groups-view_server_times$NWE</td>\n";
        echo "    </tr>\n";
        echo "    <tr class=tabfooter><td align=center colspan=4 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "  </table>\n";



        echo "  </form>\n";


        ### List agents within this group ###
        $active_confs = 0;
        $stmt=sprintf("SELECT user,full_name,user_level FROM osdial_users WHERE user_group IN %s AND user_group='%s';",$LOG['allowed_usergroupsSQL'],$user_group);
        $rsltx=mysql_query($stmt, $link);
        $users_to_print = mysql_num_rows($rsltx);
        echo "  <br>\n";
        echo "  <font color=$default_text size=4><b>AGENTS WITHIN THIS GROUP</b></font>\n";
        echo "  <br>\n";
        echo "  <table bgcolor=grey width=400 cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td>USER</td>\n";
        echo "      <td>FULL NAME</td>\n";
        echo "      <td>LEVEL</td>\n";
        echo "    </tr>\n";
        $o=0;
        while ($users_to_print > $o) {
            $rowx=mysql_fetch_row($rsltx);
            $o++;
            echo "    <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=3&user=$rowx[0]');\">\n";
            echo "      <td><a href=\"$PHP_SELF?ADD=3&user=$rowx[0]\">" . mclabel($rowx[0]) . "</a></td>\n";
            echo "      <td>$rowx[1]</td>\n";
            echo "      <td>$rowx[2]</td>\n";
            echo "    </tr>\n";
        }
        echo "    <tr class=tabfooter>\n";
        echo "      <td colspan=3></td>\n";
        echo "    </tr>\n";
        echo "  </table>\n";



        echo "</center>\n";

        echo "<br><br><br>\n";
        echo "<a href=\"$PHP_SELF?ADD=8111&user_group=$user_group\">Click here to see all CallBack Holds in this user group</a>\n";

        if ($LOG['delete_user_groups'] > 0 and $LOG['allowed_campaignsALL'] > 0) {
            echo "<br><br><br><br>\n";
            echo "<a href=\"$PHP_SELF?ADD=511111&user_group=$user_group\">DELETE THIS USER GROUP</a>\n";
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=8111 find all callbacks on hold within a user group
######################
if ($ADD==8111) {
    if ($LOG['modify_usergroups'] == 1) {
        if ($SUB == 89) {
            $stmt="UPDATE osdial_callbacks SET status='INACTIVE' WHERE user_group='$user_group' AND status='LIVE' AND callback_time<'$past_month_date';";
            $rslt=mysql_query($stmt, $link);
            echo "<br>UserGroup ($user_group) callback listings LIVE for more than one month have been made INACTIVE.\n";
        }
        if ($SUB == 899) {
            $stmt="UPDATE osdial_callbacks SET status='INACTIVE' WHERE user_group='$user_group' AND status='LIVE' AND callback_time<'$past_week_date';";
            $rslt=mysql_query($stmt, $link);
            echo "<br>UserGroup ($user_group) callback listings LIVE for more than one week have been made INACTIVE.\n";
        }
    }
    $CBinactiveLINK =  "<br><a href=\"$PHP_SELF?ADD=8111&SUB=89&user_group=$user_group\"><font color=$default_text>Remove LIVE Callbacks older than one month for this UserGroup</font></a><br>\n";
    $CBinactiveLINK .= "<a href=\"$PHP_SELF?ADD=8111&SUB=899&user_group=$user_group\"><font color=$default_text>Remove LIVE Callbacks older than one week for this UserGroup</font></a><br>\n";

    $CBquerySQLwhere = "AND user_group='$user_group'";

    echo "<br>USER GROUP CALLBACK HOLD LISTINGS: $list_id\n";
    $oldADD = "ADD=8111&user_group=$user_group";
    $ADD='82';
}

######################
# ADD=100000 display all user groups
######################
if ($ADD==100000) {
    $stmt=sprintf("SELECT * FROM osdial_user_groups WHERE user_group IN %s ORDER BY user_group", $LOG['allowed_usergroupsSQL']);
    $rslt=mysql_query($stmt, $link);
    $people_to_print = mysql_num_rows($rslt);

    echo "<center>\n";
    echo "  <br><font color=$default_text size=+1>USER GROUPS</font><br><br>\n";
    echo "  <table width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "    <tr class=tabheader>\n";
    echo "      <td>NAME</td>\n";
    echo "      <td>DESCRIPTION</td>\n";
    echo "      <td align=center>LINKS</td>\n";
    echo "    </tr>\n";

    $o=0;
    while ($people_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        echo "    <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=311111&user_group=$row[0]';\">\n";
        echo "      <td><a href=\"$PHP_SELF?ADD=311111&user_group=$row[0]\">" . mclabel($row[0]) . "</a></td>\n";
        echo "      <td>$row[1]</td>\n";
        echo "      <td align=center><a href=\"$PHP_SELF?ADD=311111&user_group=$row[0]\">MODIFY</a></td>\n";
        echo "    </tr>\n";
        $o++;
    }

    echo "    <tr class=tabfooter>\n";
    echo "      <td colspan=3></td>\n";
    echo "    </tr>\n";
    echo "  </table>\n";
    echo "</center>\n";
}



?>
