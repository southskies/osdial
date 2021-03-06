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
# ADD=1 display the ADD NEW USER FORM SCREEN
######################

if ($ADD=="1") {
    if ($LOG['modify_users']==1) {
        echo "<center><br><font class=top_header color=$default_text size=+1>ADD A NEW AGENT<form action=$PHP_SELF method=POST></font><br><br>\n";
        echo "<input type=hidden name=ADD value=2>\n";
        echo "<div style=\"width:660px;padding:5px;\">";
        echo "<TABLE class=shadedtable border=0 cellspacing=3 cellpadding=2 width=650>\n";
        echo "<tr bgcolor=$oddrows><td align=right with=30%>ID: </td><td align=left width=70%>\n";
        
        if ($LOG['multicomp_admin'] > 0) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "<select name=company_id>\n";
            foreach ($comps as $comp) {
                echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "</select>\n";
        } elseif ($LOG['multicomp']>0) {
            echo "<input type=hidden name=company_id value=$LOG[company_id]><font color=$default_text>" . $LOG[company_prefix] . "</font>&nbsp;";
        }
        echo "<input type=text name=user size=20 maxlength=10>".helptag("osdial_users-user")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10>".helptag("osdial_users-pass")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=20 maxlength=100>".helptag("osdial_users-full_name")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>User Level: </td><td align=left>";
	    $h=$levelMAX;
	    if ($LOG['user_level']==9) {
            $h=$LOG['user_level'];
        } else {
            $h=($LOG['user_level']-1);
        }
        $tuserlevel_list=array();
	    while ($h>=0) {
            $ullabel='';
		    if ($h==0) {
			    $ullabel="Disabled";
		    } elseif ($h>=1 and $h <=3) {
			    $ullabel="Outbound $h";
		    } elseif ($h>=4 and $h <=7) {
			    $ullabel="Outbound/Inbound/Closer $h";
		    } elseif ($h==8) {
			    $ullabel="Manager";
		    } elseif ($h==9) {
			    $ullabel="Administrator";
		    } else {
			    $ullabel="($h)";
		    }
            $tuserlevel_list[$h]=$ullabel;
		    $h--;
	    }
        echo editableSelectBox($tuserlevel_list, "user_level", '', 50, 50, 'selectBoxForce="1" selectBoxLabel=" -- SELECT LEVEL -- "');
        echo helptag("osdial_users-user_level")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>User Group: </td><td align=left>\n";

	    $krh = get_krh($link, 'osdial_user_groups', 'user_group,group_name', 'user_group', sprintf("user_group IN %s",$LOG['allowed_usergroupsSQL']), '');
        $tusergroup_list=array();
        foreach ($krh as $ugs) {
            $tusergroup_list[$ugs['user_group']]=$ugs['group_name'];
        }
        echo editableSelectBox($tusergroup_list, "user_group", '', 50, 50, 'selectBoxForce="1" selectBoxLabel=" -- SELECT GROUP -- "');

        echo helptag("osdial_users-user_group")."</td></tr>\n";

        #echo "<tr bgcolor=$oddrows><td align=right>Phone Login: </td><td align=left><input type=text name=phone_login size=20 maxlength=20>".helptag("osdial_users-phone_login")."</td></tr>\n";
        #echo "<tr bgcolor=$oddrows><td align=right>Phone Pass: </td><td align=left><input type=text name=phone_pass size=20 maxlength=20>".helptag("osdial_users-phone_pass")."</td></tr>\n";

        echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=ADD></td></tr>\n";
        echo "</TABLE></div></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=1A display the COPY USER FORM SCREEN
######################

if ($ADD=="1A")
{
	if ($LOG['modify_users']==1)
	{
	echo "<center><br><font class=top_header color=$default_text size=+1>COPY AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2A>\n";
	echo "<div style=\"width:660px;padding:5px;\">";
	echo "<TABLE class=shadedtable cellspacing=3 width=650>\n";
	echo "<tr bgcolor=$oddrows><td align=center colspan=2 width=100%>ID: ";
    if ($LOG['multicomp_admin'] > 0) {
        $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
        echo "<select name=company_id>\n";
        foreach ($comps as $comp) {
            echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
        }
        echo "</select>\n";
    } elseif ($LOG['multicomp']>0) {
        echo "<input type=hidden name=company_id value=$LOG[company_id]><font color=$default_text>" . $LOG[company_prefix] . "</font>&nbsp;";
    }
    echo "<input type=text name=user size=20 maxlength=10>".helptag("osdial_users-user")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10>".helptag("osdial_users-pass")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=20 maxlength=100>".helptag("osdial_users-full_name")."</td></tr>\n";

	if ($LOG['user_level']==9) {
        $levelMAX=$LOG['user_level'];
    } else {
        $levelMAX=($LOG['user_level']-1);
    }

	echo "<tr bgcolor=$oddrows><td align=right>Source Agent ID: </td><td align=left><select size=1 name=source_user_id>\n";

		$stmt = sprintf("SELECT user,full_name FROM osdial_users WHERE user_level<='%s' AND user NOT IN ('PBX-OUT','PBX-IN') AND user_group IN %s ORDER BY full_name;",mres($levelMAX),$LOG['allowed_usergroupsSQL']);
		$rslt=mysql_query($stmt, $link);
		$Uusers_to_print = mysql_num_rows($rslt);
		$Uusers_list='';

		$o=0;
		while ($Uusers_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$Uusers_list .= "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
			$o++;
		}
	echo "$Uusers_list";
	echo "</select>".helptag("osdial_users-user")."</td></tr>\n";
	echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=COPY></td></tr>\n";
	echo "</TABLE></div></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}

######################
# ADD=2 adds the new user to the system
######################

if ($ADD=="2")
{
    $preuser = $user;
    if ($LOG['multicomp'] > 0) $preuser = (($company_id * 1) + 100) . $user;
	$stmt=sprintf("SELECT count(*) FROM osdial_users WHERE user='%s';",mres($preuser));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> AGENT NOT ADDED - there is already a user in the system with this user number</font>\n";}
	else
		{
		 if ( (OSDstrlen($user) < 2) or (OSDstrlen($pass) < 2) or (OSDstrlen($full_name) < 2) or (OSDstrlen($user) > 15) )
			{
			 echo "<br><font color=red> AGENT NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>user id must be between 2 and 15 characters long\n";
			 echo "<br>full name and password must be at least 2 characters long</font><br>\n";
			}
		 else
			{
			echo "<br><B>AGENT ADDED: $user</B>\n";

            if ($LOG['multicomp'] > 0) $user = (($company_id * 1) + 100) . $user;
			$stmt=sprintf("INSERT INTO osdial_users (user,pass,full_name,user_level,user_group,phone_login,phone_pass) VALUES('%s','%s','%s','%s','%s','%s','%s');",mres($user),mres($pass),mres($full_name),mres($user_level),mres($user_group),mres($phone_login),mres($phone_pass));
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD AN AGENT          |$PHP_AUTH_USER|$ip|'$user','$pass','$full_name','$user_level','$user_group','$phone_login','$phone_pass'|\n");
				fclose($fp);
				}
			}
		}

$ADD=3;
}

######################
# ADD=2A adds the copied new user to the system
######################

if ($ADD=="2A")
{
    $preuser = $user;
    if ($LOG['multicomp'] > 0) $preuser = (($company_id * 1) + 100) . $user;
	$stmt=sprintf("SELECT count(*) FROM osdial_users WHERE user='$preuser';",mres($preuser));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> AGENT NOT ADDED - there is already a user in the system with this user number</font>\n";}
	else
		{
		 if ( (OSDstrlen($user) < 2) or (OSDstrlen($pass) < 2) or (OSDstrlen($full_name) < 2) or (OSDstrlen($user) > 15) )
			{
			 echo "<br><font color=red> AGENT NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>user id must be between 2 and 15 characters long : " . OSDstrlen($user) . " : " . OSDstrlen($pass) . "\n";
			 echo "<br>full name and password must be at least 2 characters long : " . OSDstrlen($full_name) . "</font><br>\n";
			}
		 else
			{
            if ($LOG['multicomp'] > 0) $user = (($company_id * 1) + 100) . $user;
			$stmt=sprintf("INSERT INTO osdial_users (user,pass,full_name,user_level,user_group,phone_login,phone_pass,delete_users,delete_user_groups,delete_lists,delete_campaigns,delete_ingroups,delete_remote_agents,load_leads,campaign_detail,ast_admin_access,ast_delete_phones,delete_scripts,modify_leads,hotkeys_active,change_agent_campaign,agent_choose_ingroups,closer_campaigns,scheduled_callbacks,agentonly_callbacks,agentcall_manual,osdial_recording,osdial_transfers,delete_filters,alter_agent_interface_options,closer_default_blended,delete_call_times,modify_call_times,modify_users,modify_campaigns,modify_lists,modify_scripts,modify_filters,modify_ingroups,modify_usergroups,modify_remoteagents,modify_servers,view_reports,osdial_recording_override,alter_custdata_override,manual_dial_allow_skip,export_leads,admin_api_access,agent_api_access,xfer_agent2agent,script_override,load_dnc,export_dnc,delete_dnc) SELECT '%s','%s','%s',user_level,user_group,phone_login,phone_pass,delete_users,delete_user_groups,delete_lists,delete_campaigns,delete_ingroups,delete_remote_agents,load_leads,campaign_detail,ast_admin_access,ast_delete_phones,delete_scripts,modify_leads,hotkeys_active,change_agent_campaign,agent_choose_ingroups,closer_campaigns,scheduled_callbacks,agentonly_callbacks,agentcall_manual,osdial_recording,osdial_transfers,delete_filters,alter_agent_interface_options,closer_default_blended,delete_call_times,modify_call_times,modify_users,modify_campaigns,modify_lists,modify_scripts,modify_filters,modify_ingroups,modify_usergroups,modify_remoteagents,modify_servers,view_reports,osdial_recording_override,alter_custdata_override,manual_dial_allow_skip,export_leads,admin_api_access,agent_api_access,xfer_agent2agent,script_override,load_dnc,export_dnc,delete_dnc FROM osdial_users WHERE user='%s';",mres($user),mres($pass),mres($full_name),mres($source_user_id));
			$rslt=mysql_query($stmt, $link);

			$stmtA=sprintf("INSERT INTO osdial_inbound_group_agents (user,group_id,group_rank,group_weight,calls_today,allow_multicall) SELECT '%s',group_id,group_rank,group_weight,'0','Y' FROM osdial_inbound_group_agents WHERE user='%s';",mres($user),mres($source_user_id));
			$rslt=mysql_query($stmtA, $link);

			$stmtA=sprintf("INSERT INTO osdial_campaign_agents (user,campaign_id,campaign_rank,campaign_weight,calls_today) SELECT '%s',campaign_id,campaign_rank,campaign_weight,'0' from osdial_campaign_agents where user='%s';",mres($user),mres($source_user_id));
			$rslt=mysql_query($stmtA, $link);

			echo "<br><B><font color=$default_text> AGENT COPIED: $user copied from $source_user_id</font></B>\n";
			echo "<br>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A COPIED AGENT   |$PHP_AUTH_USER|$ip|$user|$source_user_id|$stmt|\n");
				fclose($fp);
				}
			}
		}
    $ADD=3;
}

######################
# ADD=4A submit user modifications to the system - ADMIN
######################

if ($ADD=="4A") {
    if ($LOG['modify_users']==1) {
        if ( (OSDstrlen($pass) < 2) or (OSDstrlen($full_name) < 2) or (OSDstrlen($user_level) < 1) ) {
            echo "<br><font color=red>AGENT NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>AGENT MODIFIED - ADMIN: $user</font></B>\n";

            # Force inclusion through agent interface instead of adding into agents ingroups.
            #if ($xfer_agent2agent > 0) {
            #    if (!OSDpreg_match('/A2A_' . $user . '/',$groups_value)) {
            #        $groups_value = " A2A_$user" . $groups_value;
            #    }
            #}

            $stmt=sprintf("UPDATE osdial_users SET pass='%s',full_name='%s',user_level='%s',user_group='%s',phone_login='%s',phone_pass='%s',delete_users='%s',delete_user_groups='%s',delete_lists='%s',delete_campaigns='%s',delete_ingroups='%s',delete_remote_agents='%s',load_leads='%s',campaign_detail='%s',ast_admin_access='%s',ast_delete_phones='%s',delete_scripts='%s',modify_leads='%s',hotkeys_active='%s',change_agent_campaign='%s',agent_choose_ingroups='%s',closer_campaigns='%s',scheduled_callbacks='%s',agentonly_callbacks='%s',agentcall_manual='%s',osdial_recording='%s',osdial_transfers='%s',delete_filters='%s',alter_agent_interface_options='%s',closer_default_blended='%s',delete_call_times='%s',modify_call_times='%s',modify_users='%s',modify_campaigns='%s',modify_lists='%s',modify_scripts='%s',modify_filters='%s',modify_ingroups='%s',modify_usergroups='%s',modify_remoteagents='%s',modify_servers='%s',view_reports='%s',osdial_recording_override='%s',alter_custdata_override='%s',manual_dial_allow_skip='%s',export_leads='%s',admin_api_access='%s',agent_api_access='%s',xfer_agent2agent='%s',script_override='%s',load_dnc='%s',export_dnc='%s',delete_dnc='%s' WHERE user='%s';",mres($pass),mres($full_name),mres($user_level),mres($user_group),mres($phone_login),mres($phone_pass),mres($delete_users),mres($delete_user_groups),mres($delete_lists),mres($delete_campaigns),mres($delete_ingroups),mres($delete_remote_agents),mres($load_leads),mres($campaign_detail),mres($ast_admin_access),mres($ast_delete_phones),mres($delete_scripts),mres($modify_leads),mres($hotkeys_active),mres($change_agent_campaign),mres($agent_choose_ingroups),mres($groups_value),mres($scheduled_callbacks),mres($agentonly_callbacks),mres($agentcall_manual),mres($osdial_recording),mres($osdial_transfers),mres($delete_filters),mres($alter_agent_interface_options),mres($closer_default_blended),mres($delete_call_times),mres($modify_call_times),mres($modify_users),mres($modify_campaigns),mres($modify_lists),mres($modify_scripts),mres($modify_filters),mres($modify_ingroups),mres($modify_usergroups),mres($modify_remoteagents),mres($modify_servers),mres($view_reports),mres($osdial_recording_override),mres($alter_custdata_override),mres($manual_dial_allow_skip),mres($export_leads),mres($admin_api_access),mres($agent_api_access),mres($xfer_agent2agent),mres($script_override),mres($load_dnc),mres($export_dnc),mres($delete_dnc),mres($user));
            $rslt=mysql_query($stmt, $link);

            $user_level=($user_level*1);
            if ($user_level==0) {
                $stmt=sprintf("UPDATE osdial_callbacks SET recipient='ANYONE' WHERE user='%s' AND status='LIVE';",mres($user));
                $rslt=mysql_query($stmt, $link);
            }

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY AGENT INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }

            if ($xfer_agent2agent > 0) {
                $voicemail_id='';
                $dialplan_number='8307';
                $use_exten='Y';
                if ($phone_login != '') {
	                $stmt=sprintf("SELECT voicemail_id,dialplan_number FROM phones WHERE login='%s';",mres($phone_login));
	                $rslt=mysql_query($stmt, $link);
	                $row=mysql_fetch_row($rslt);
                    $voicemail_id=$row[0];
                    $dialplan_number=$row[1];
                }
                if ($voicemail_id != '') $use_exten='N';

	            $stmt=sprintf("SELECT count(*) FROM osdial_inbound_groups WHERE group_id='A2A_%s';",mres($user));
	            $rslt=mysql_query($stmt, $link);
	            $row=mysql_fetch_row($rslt);

                if ($row[0]==0) {
                    $stmt=sprintf("INSERT INTO osdial_inbound_groups (group_id,group_name,group_color,active,drop_action,voicemail_ext,drop_exten,next_agent_call,fronter_display,drop_call_seconds,agent_alert_exten,allow_multicall) VALUES('A2A_%s','Agent2Agent %s','pink','Y','%s','%s','%s','oldest_call_finish','Y','600','X','Y');",mres($user),mres($user),mres($use_exten),mres($voicemail_id),mres($dialplan_number));
                } else {
                    $stmt=sprintf("UPDATE osdial_inbound_groups SET drop_action='%s',drop_exten='%s',drop_trigger='%s',drop_call_seconds='%s',voicemail_ext='%s',allow_multicall='%s' WHERE group_id='A2A_%s';",mres($xfer_agent2agent_wait_action),mres($xfer_agent2agent_wait_extension),mres($xfer_agent2agent_wait),mres($xfer_agent2agent_wait_seconds),mres($voicemail_id),mres($xfer_agent2agent_allow_multicall),mres($user));
                }

            } else {
                $stmt=sprintf("DELETE FROM osdial_inbound_groups WHERE group_id='A2A_%s';",mres($user));
            }
            $rslt=mysql_query($stmt, $link);
        }
        $ADD=3;		# go to user modification below
    } else {
        echo " <font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=4B submit user modifications to the system - ADMIN
######################

if ($ADD=="4B")
{
	if ($LOG['modify_users']==1)
	{
	 if ( (OSDstrlen($pass) < 2) or (OSDstrlen($full_name) < 2) or (OSDstrlen($user_level) < 1) )
		{
		 echo "<br><font color=red>AGENT NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>AGENT MODIFIED - ADMIN: $user</font></B>\n";

		$stmt=sprintf("UPDATE osdial_users SET pass='%s',full_name='%s',user_level='%s',user_group='%s',phone_login='%s',phone_pass='%s',hotkeys_active='%s',agent_choose_ingroups='%s',closer_campaigns='%s',scheduled_callbacks='%s',agentonly_callbacks='%s',agentcall_manual='%s',osdial_recording='%s',osdial_transfers='%s',closer_default_blended='%s',osdial_recording_override='%s',alter_custdata_override='%s',manual_dial_allow_skip='%s' WHERE user='%s';",mres($pass),mres($full_name),mres($user_level),mres($user_group),mres($phone_login),mres($phone_pass),mres($hotkeys_active),mres($agent_choose_ingroups),mres($groups_value),mres($scheduled_callbacks),mres($agentonly_callbacks),mres($agentcall_manual),mres($osdial_recording),mres($osdial_transfers),mres($closer_default_blended),mres($osdial_recording_override),mres($alter_custdata_override),mres($manual_dial_allow_skip),mres($user));
		$rslt=mysql_query($stmt, $link);

        if ($user_level==0) {
            $stmt=sprintf("UPDATE osdial_callbacks SET recipient='ANYONE' WHERE user='%s' AND status='LIVE';",mres($user));
            $rslt=mysql_query($stmt, $link);
        }

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY AGENT INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
$ADD=3;		# go to user modification below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



######################
# ADD=4 submit user modifications to the system
######################

if ($ADD==4)
{
	if ($LOG['modify_users']==1)
	{
	 if ( (OSDstrlen($pass) < 2) or (OSDstrlen($full_name) < 2) or (OSDstrlen($user_level) < 1) )
		{
		 echo "<br><font color=red>AGENT NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>AGENT MODIFIED: $user</font></B>\n";

		$stmt=sprintf("UPDATE osdial_users SET pass='%s',full_name='%s',user_level='%s',user_group='%s',phone_login='%s',phone_pass='%s' WHERE user='%s';",mres($pass),mres($full_name),mres($user_level),mres($user_group),mres($phone_login),mres($phone_pass),mres($user));
		$rslt=mysql_query($stmt, $link);

        if ($user_level==0) {
            $stmt=sprintf("UPDATE osdial_callbacks SET recipient='ANYONE' WHERE user='%s' AND status='LIVE';",mres($user));
            $rslt=mysql_query($stmt, $link);
        }

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY AGENT INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
$ADD=3;		# go to user modification below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=5 confirmation before deletion of user
######################

if ($ADD==5)
{
	 if ( (OSDstrlen($user) < 2) or ($LOG['delete_users'] < 1) )
		{
		 echo "<br><font color=red>AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Agent be at least 2 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>AGENT DELETION CONFIRMATION: $user</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6&user=$user&CoNfIrM=YES\">Click here to delete user $user</a></font><br><br><br>\n";
		}

$ADD='3';		# go to user modification below
}

######################
# ADD=6 delete user record
######################

if ($ADD==6)
{
	 if ( ( OSDstrlen($user) < 2) or ($CoNfIrM != 'YES') or ($LOG['delete_users'] < 1) )
		{
		 echo "<br><font color=red>AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Agent be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmtA=sprintf("DELETE FROM osdial_users WHERE user='%s' LIMIT 1;",mres($user));
		$rslt=mysql_query($stmtA, $link);

		$stmt=sprintf("DELETE FROM osdial_campaign_agents WHERE user='%s';",mres($user));
		$rslt=mysql_query($stmt, $link);

		$stmt=sprintf("DELETE FROM osdial_inbound_group_agents WHERE user='%s';",mres($user));
		$rslt=mysql_query($stmt, $link);

        $stmt=sprintf("UPDATE osdial_callbacks SET recipient='ANYONE' WHERE user='%s' AND status='LIVE';",mres($user));
        $rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING AGENT!!!!|$PHP_AUTH_USER|$ip|$user|$stmtA|$stmt|\n");
			fclose($fp);
			}
		echo "<br><B><font color=$default_text>AGENT DELETION COMPLETED: $user</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='0';		# go to user list
}

######################
# ADD=3 modify user info in the system
######################

if ($ADD==3) {
	if ($LOG['modify_users']==1) {
		$stmt=sprintf("SELECT * FROM osdial_users WHERE user='%s';",mres($user));
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$user_level =			$row[4];
		$user_group =			$row[5];
		$phone_login =			$row[6];
		$phone_pass =			$row[7];
		$delete_users =			$row[8];
		$delete_user_groups =		$row[9];
		$delete_lists =			$row[10];
		$delete_campaigns =		$row[11];
		$delete_ingroups =		$row[12];
		$delete_remote_agents =		$row[13];
		$load_leads =			$row[14];
		$campaign_detail =		$row[15];
		$ast_admin_access =		$row[16];
		$ast_delete_phones =		$row[17];
		$delete_scripts =		$row[18];
		$modify_leads =			$row[19];
		$hotkeys_active =		$row[20];
		$change_agent_campaign =	$row[21];
		$agent_choose_ingroups =	$row[22];
		$scheduled_callbacks =		$row[24];
		$agentonly_callbacks =		$row[25];
		$agentcall_manual =		$row[26];
		$osdial_recording =		$row[27];
		$osdial_transfers =		$row[28];
		$delete_filters =		$row[29];
		$alter_agent_interface_options =$row[30];
		$closer_default_blended =	$row[31];
		$delete_call_times =		$row[32];
		$modify_call_times =		$row[33];
		$modify_users =			$row[34];
		$modify_campaigns =		$row[35];
		$modify_lists =			$row[36];
		$modify_scripts =		$row[37];
		$modify_filters =		$row[38];
		$modify_ingroups =		$row[39];
		$modify_usergroups =		$row[40];
		$modify_remoteagents =		$row[41];
		$modify_servers =		$row[42];
		$view_reports =			$row[43];
		$osdial_recording_override =	$row[44];
		$alter_custdata_override = 	$row[45];
		$manual_dial_allow_skip = 	$row[47];
		$export_leads = 	$row[48];
		$admin_api_access = 	$row[49];
		$agent_api_access = 	$row[50];
		$xfer_agent2agent = 	$row[51];
		$script_override = 	$row[52];
		$load_dnc = 	$row[53];
		$export_dnc = 	$row[54];
		$delete_dnc = 	$row[55];

		if ($LOG['user_level']==9) {
			$levelMAX=$LOG['user_level'];
		} else {
			$levelMAX=($LOG['user_level']-1);
		}

		if (($user_level > $levelMAX) and ($LOG['user_level'] < 9) and (!OSDpreg_match('/:/' . $user_group . ':', $LOG['allowed_usergroupsSTR']))) {
			echo "<br><font color=red>You do not have permissions to modify this user: $row[1]</font>\n";
		} else {
			echo "<center><br><font class=top_header color=$default_text size=+1>MODIFY AN AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
			if ($LOG['user_level'] > 8)	{
				echo "<input type=hidden name=ADD value=4A>\n";
			} else {
				if ($LOG['alter_agent_interface_options'] == "1") {
					echo "<input type=hidden name=ADD value=4B>\n";
				} else {
					echo "<input type=hidden name=ADD value=4>\n";
				}
			}
			echo "<input type=hidden name=user value=\"$row[1]\">\n";
			echo "<div style=\"width:900px;padding:5px;\">";
			echo "<TABLE class=shadedtable cellspacing=3 cellpadding=2 width=90%>\n";
			echo "<tr bgcolor=$oddrows><td align=right Xwidth=300>ID: </td><td align=left>\n";
			$pcomp='';
			if ($LOG['multicomp']>0 and OSDpreg_match($LOG['companiesRE'],$row[1])) {
				echo "<font color=$default_text>" . OSDsubstr($row[1],0,3) . "</font>&nbsp;";
				echo "<b>" . OSDsubstr($row[1],3,OSDstrlen($row[1])) . "</b>";
				$pcomp = OSDsubstr($row[1],0,3);
			} else {
				echo "<b>$row[1]</b>";
			}
			echo "".helptag("osdial_users-user")."</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10 value=\"$row[2]\">".helptag("osdial_users-pass")."</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=30 maxlength=30 value=\"$row[3]\">".helptag("osdial_users-full_name")."</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>User Level: </td><td align=left>";
			$h=$levelMAX;
            $tuserlevel_list=array();
			while ($h>=0) {
                $ullabel='';
				if ($h==0) {
					$ullabel="Disabled";
				} elseif ($h>=1 and $h <=3) {
					$ullabel="Outbound $h";
				} elseif ($h>=4 and $h <=7) {
					$ullabel="Outbound/Inbound/Closer $h";
				} elseif ($h==8) {
					$ullabel="Manager";
				} elseif ($h==9) {
					$ullabel="Administrator";
				} else {
					$ullabel="($h)";
				}
                $tuserlevel_list[$h]=$ullabel;
				$h--;
			}
            echo editableSelectBox($tuserlevel_list, "user_level", $row[4], 50, 50, 'selectBoxForce="1"');
			echo helptag("osdial_users-user_level")."</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right><A HREF=\"$PHP_SELF?ADD=311111&user_group=$user_group\">User Group</A>: </td><td align=left>\n";
			$krh = get_krh($link, 'osdial_user_groups', 'user_group,group_name', 'user_group', sprintf("user_group IN %s",$LOG['allowed_usergroupsSQL']), '');
            $tusergroup_list=array();
            foreach ($krh as $ugs) {
                $tusergroup_list[$ugs['user_group']]=$ugs['group_name'];
            }
            echo editableSelectBox($tusergroup_list, "user_group", $user_group, 50, 50, 'selectBoxForce="1"');
			echo helptag("osdial_users-user_group")."</td></tr>\n";

			if ($phone_login != '') {
				echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=31111111111&extension=$phn[extension]&server_ip=$phn[server_ip]\">Default Phone / Voicemail</a>: </td><td align=left>\n";
			} else {
				echo "<tr bgcolor=$oddrows><td align=right>Default Phone / Voicemail: </td><td align=left>\n";
			}
            echo phone_extension_text_options($link, 'phone_login', $phone_login, 10, 20, '');
			echo helptag("osdial_users-phone_login")."</td></tr>\n";
			#echo "<tr bgcolor=$oddrows><td align=right>Phone Login: </td><td align=left><input type=text name=phone_login size=20 maxlength=20 value=\"$phone_login\">".helptag("osdial_users-phone_login")."</td></tr>\n";
			#echo "<tr bgcolor=$oddrows><td align=right>Phone Pass: </td><td align=left><input type=text name=phone_pass size=20 maxlength=20 value=\"$phone_pass\">".helptag("osdial_users-phone_pass")."</td></tr>\n";

			echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr></table>\n";
			

			if (($LOG['user_level'] > 8) or ($LOG['alter_agent_interface_options'] == "1")) {
				echo "<br/><TABLE class=shadedtable cellspacing=3 width=550>\n";

				echo "<tr class=\"tabheader font3\"><td class=top_header2 colspan=2 align=center>AGENT INTERFACE OPTIONS</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Agent Choose Ingroups: </td><td align=left><select size=1 name=agent_choose_ingroups><option value=0>N</option><option value=1>Y</option>" . optnum2let($agent_choose_ingroups) . "</select>".helptag("osdial_users-agent_choose_ingroups")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Closer Default Blended: </td><td align=left><select size=1 name=closer_default_blended><option value=0>N</option><option value=1>Y</option>" . optnum2let($closer_default_blended) . "</select>".helptag("osdial_users-closer_default_blended")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Hot Keys Active: </td><td align=left><select size=1 name=hotkeys_active><option value=0>N</option><option value=1>Y</option>" . optnum2let($hotkeys_active) . "</select>".helptag("osdial_users-hotkeys_active")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Manual-Dial: </td><td align=left><select size=1 name=agentcall_manual><option value=0>N</option><option value=1>Y</option>" . optnum2let($agentcall_manual) . "</select>".helptag("osdial_users-agentcall_manual")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Preview-Dial Skip-Lead: </td><td align=left><select size=1 name=manual_dial_allow_skip><option value=0>N</option><option value=1>Y</option>" . optnum2let($manual_dial_allow_skip) . "</select>".helptag("osdial_users-manual_dial_allow_skip")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Scheduled Callbacks: </td><td align=left><select size=1 name=scheduled_callbacks><option value=0>N</option><option value=1>Y</option>" . optnum2let($scheduled_callbacks) . "</select>".helptag("osdial_users-scheduled_callbacks")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Agent-Only Callbacks: </td><td align=left><select size=1 name=agentonly_callbacks><option value=0>N</option><option value=1>Y</option>" . optnum2let($agentonly_callbacks) . "</select>".helptag("osdial_users-agentonly_callbacks")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Recording: </td><td align=left><select size=1 name=osdial_recording><option value=0>N</option><option value=1>Y</option>" . optnum2let($osdial_recording) . "</select>".helptag("osdial_users-osdial_recording")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Recording Mode (Override): </td><td align=left><select size=1 name=osdial_recording_override><option>DISABLED</option><option>NEVER</option><option>ONDEMAND</option><option>ALLCALLS</option><option>ALLFORCE</option><option SELECTED>$osdial_recording_override</option></select>".helptag("osdial_users-osdial_recording_override")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Alter Lead Data (Override): </td><td align=left><select size=1 name=alter_custdata_override><option>NOT_ACTIVE</option><option>ALLOW_ALTER</option><option SELECTED>$alter_custdata_override</option></select>".helptag("osdial_users-alter_custdata_override")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows>\n";
				echo "  <td align=right>Script (Override): </td>\n";
				echo "  <td align=left>\n";
				echo "    <select style=\"font-family:monospace;\" size=1 name=script_override>\n";
				echo get_scripts($link, $script_override);
				echo "    </select>\n";
				echo "  ".helptag("osdial_users-script_override")."</td>\n";
				echo "</tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Transfers: </td><td align=left><select size=1 name=osdial_transfers><option value=0>N</option><option value=1>Y</option>" . optnum2let($osdial_transfers) . "</select>".helptag("osdial_users-osdial_transfers")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows>\n";
				echo "  <td align=right>Agent2Agent Transfers: </td>\n";
				echo "  <td align=left><select size=1 name=xfer_agent2agent><option value=0>N</option><option value=1>Y</option>" . optnum2let($xfer_agent2agent) . "</select>\n";
				echo "  ".helptag("osdial_users-xfer_agent2agent")."</td>\n";
				echo "</tr>\n";
				if ($xfer_agent2agent > 0) {
					$stmt = sprintf("SELECT drop_trigger,drop_call_seconds,drop_action,drop_exten,allow_multicall FROM osdial_inbound_groups WHERE group_id='A2A_%s';",mres($user));
					$rslt=mysql_query($stmt, $link);
					$rowx=mysql_fetch_row($rslt);
					$xfer_agent2agent_wait = $rowx[0];
					$xfer_agent2agent_wait_seconds = $rowx[1];
					$xfer_agent2agent_wait_action = $rowx[2];
					$xfer_agent2agent_wait_extension = $rowx[3];
					$xfer_agent2agent_allow_multicall = $rowx[4];
					echo "<tr bgcolor=$oddrows>\n";
					echo "  <td align=right>Agent2Agent Timeout: </td>\n";
					echo "  <td align=left>\n";
					echo "    <select size=1 name=xfer_agent2agent_wait>\n";
					$wsel=''; if ($xfer_agent2agent_wait=='CALL_SECONDS_TIMEOUT') $wsel='selected';
					echo "      <option $wsel value=CALL_SECONDS_TIMEOUT>ALWAYS WAIT</option>\n";
					$wsel=''; if ($xfer_agent2agent_wait=='NO_AGENTS_CONNECTED') $wsel='selected';
					echo "      <option $wsel value=NO_AGENTS_CONNECTED>WHEN LOGGED IN</option>\n";
					$wsel=''; if ($xfer_agent2agent_wait=='NO_AGENTS_AVAILABLE') $wsel='selected';
					echo "      <option $wsel value=NO_AGENTS_AVAILABLE>NEVER WAIT</option>\n";
					echo "    </select>\n";
					echo "  ".helptag("osdial_users-xfer_agent2agent_wait")."</td>\n";
					echo "</tr>\n";
					echo "<tr bgcolor=$oddrows>\n";
					echo "  <td align=right>Agent2Agent Timeout Seconds: </td>\n";
					echo "  <td align=left><input type=text name=xfer_agent2agent_wait_seconds size=5 maxlength=4 value=\"$xfer_agent2agent_wait_seconds\">".helptag("osdial_users-xfer_agent2agent_wait_seconds")."</td>\n";
					echo "</tr>\n";
					echo "<tr bgcolor=$oddrows>\n";
					echo "  <td align=right>Agent2Agent Timeout Action: </td>\n";
					echo "  <td align=left>\n";
					echo "    <select size=1 name=xfer_agent2agent_wait_action>\n";
                    $wsel=''; if ($xfer_agent2agent_wait_action=='HANGUP') $wsel='selected';
                    echo "      <option $wsel value=\"HANGUP\">HANGUP</option>\n";
                    $wsel=''; if ($xfer_agent2agent_wait_action=='MESSAGE') $wsel='selected';
                    echo "      <option $wsel value=\"MESSAGE\">MESSAGE</option>\n";
                    $wsel=''; if ($xfer_agent2agent_wait_action=='EXTENSION') $wsel='selected';
                    echo "      <option $wsel value=\"EXTENSION\">EXTENSION</option>\n";
                    $wsel=''; if ($xfer_agent2agent_wait_action=='VOICEMAIL') $wsel='selected';
                    echo "      <option $wsel value=\"VOICEMAIL\">VOICEMAIL</option>\n";
                    $wsel=''; if ($xfer_agent2agent_wait_action=='CALLBACK') $wsel='selected';
                    echo "      <option $wsel value=\"CALLBACK\">CALLBACK</option>\n";
					echo "    </select>\n";
					echo "  ".helptag("osdial_users-xfer_agent2agent_wait_action")."</td>\n";
					echo "</tr>\n";
					if ($xfer_agent2agent_wait_action == "EXTENSION") {
						echo "<tr bgcolor=$oddrows>\n";
						echo "  <td align=right>Agent2Agent Timeout Extension: </td>\n";
						echo "  <td align=left><input type=text name=xfer_agent2agent_wait_extension size=10 maxlength=15 value=\"$xfer_agent2agent_wait_extension\">".helptag("osdial_users-xfer_agent2agent_wait_extension")."</td>\n";
						echo "</tr>\n";
					    echo "<tr bgcolor=$oddrows>\n";
					    echo "  <td align=right>";
					} else {
					    echo "<tr bgcolor=$oddrows>\n";
					    echo "  <td align=right>";
						echo "<input type=hidden name=xfer_agent2agent_wait_extension value=\"$xfer_agent2agent_wait_extension\">";
					}
                    # tr and td for a2a multicall starts in above condition.
					echo "Agent2Agent Allow Multicall: </td>\n";
					echo "  <td align=left>\n";
					echo "    <select size=1 name=xfer_agent2agent_allow_multicall>\n";
					$wsel=''; if ($xfer_agent2agent_allow_multicall=='N') $wsel='selected';
					echo "      <option $wsel value=N>N</option>\n";
					$wsel=''; if ($xfer_agent2agent_allow_multicall=='Y') $wsel='selected';
					echo "      <option $wsel value=Y>Y</option>\n";
					echo "    </select>\n";
					echo "  ".helptag("osdial_users-xfer_agent2agent_allow_multicall")."</td>\n";
					echo "</tr>\n";
				}

				echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr></table>\n";
			}
			if ($LOG['user_level'] > 8 && $user_level > 7) {			
				echo "<br/><TABLE class=shadedtable cellspacing=3 width=550>\n";
				echo "<tr class=\"tabheader font3\"><td colspan=2 class=top_header2 align=center>ADMIN INTERFACE OPTIONS</td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Modify Agents: </td><td align=left><select size=1 name=modify_users><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_users) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete Agents: </td><td align=left><select size=1 name=delete_users><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_users) . "</select>".helptag("osdial_users-delete_users")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Alter Agent Interface Options: </td><td align=left><select size=1 name=alter_agent_interface_options><option value=0>N</option><option value=1>Y</option>" . optnum2let($alter_agent_interface_options) . "</select>".helptag("osdial_users-alter_agent_interface_options")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Change Agent Campaign: </td><td align=left><select size=1 name=change_agent_campaign><option value=0>N</option><option value=1>Y</option>" . optnum2let($change_agent_campaign) . "</select>".helptag("osdial_users-change_agent_campaign")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Modify Campaigns: </td><td align=left><select size=1 name=modify_campaigns><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_campaigns) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete Campaigns: </td><td align=left><select size=1 name=delete_campaigns><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_campaigns) . "</select>".helptag("osdial_users-delete_campaigns")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Campaign Detail: </td><td align=left><select size=1 name=campaign_detail><option value=0>N</option><option value=1>Y</option>" . optnum2let($campaign_detail) . "</select>".helptag("osdial_users-campaign_detail")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Modify Lists: </td><td align=left><select size=1 name=modify_lists><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_lists) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete Lists: </td><td align=left><select size=1 name=delete_lists><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_lists) . "</select>".helptag("osdial_users-delete_lists")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Load Leads: </td><td align=left><select size=1 name=load_leads><option value=0>N</option><option value=1>Y</option>" . optnum2let($load_leads) . "</select>".helptag("osdial_users-load_leads")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Modify Leads: </td><td align=left><select size=1 name=modify_leads><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_leads) . "</select>".helptag("osdial_users-modify_leads")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Export Leads: </td><td align=left><select size=1 name=export_leads><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_leads) . "</select>".helptag("osdial_users-export_leads")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Load DNC Records: </td><td align=left><select size=1 name=load_dnc><option value=0>N</option><option value=1>Y</option>" . optnum2let($load_dnc) . "</select>".helptag("osdial_users-load_dnc")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete DNC Records: </td><td align=left><select size=1 name=delete_dnc><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_dnc) . "</select>".helptag("osdial_users-delete_dnc")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Export DNC Records: </td><td align=left><select size=1 name=export_dnc><option value=0>N</option><option value=1>Y</option>" . optnum2let($export_dnc) . "</select>".helptag("osdial_users-export_dnc")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Modify Scripts: </td><td align=left><select size=1 name=modify_scripts><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_scripts) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete Scripts: </td><td align=left><select size=1 name=delete_scripts><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_scripts) . "</select>".helptag("osdial_users-delete_scripts")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Modify Filters: </td><td align=left><select size=1 name=modify_filters><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_filters) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete Filters: </td><td align=left><select size=1 name=delete_filters><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_filters) . "</select>".helptag("osdial_users-delete_filters")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Modify In-Groups: </td><td align=left><select size=1 name=modify_ingroups><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_ingroups) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete In-Groups: </td><td align=left><select size=1 name=delete_ingroups><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_ingroups) . "</select>".helptag("osdial_users-delete_ingroups")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Modify User Groups: </td><td align=left><select size=1 name=modify_usergroups><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_usergroups) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete User Groups: </td><td align=left><select size=1 name=delete_user_groups><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_user_groups) . "</select>".helptag("osdial_users-delete_user_groups")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>Modify External Agents: </td><td align=left><select size=1 name=modify_remoteagents><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_remoteagents) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$oddrows><td align=right>Delete External Agents: </td><td align=left><select size=1 name=delete_remote_agents><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_remote_agents) . "</select>".helptag("osdial_users-delete_remote_agents")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$oddrows><td align=right>View Reports: </td><td align=left><select size=1 name=view_reports><option value=0>N</option><option value=1>Y</option>" . optnum2let($view_reports) . "</select>".helptag("osdial_users-view_reports")."</td></tr>\n";

				echo "<tr bgcolor=grey><td colspan=2></td></tr>\n";

				echo "<tr bgcolor=$unusualrows><td align=right>Setup Menu: </td><td align=left><select size=1 name=ast_admin_access><option value=0>N</option><option value=1>Y</option>" . optnum2let($ast_admin_access) . "</select>".helptag("osdial_users-ast_admin_access")."</td></tr>\n";
				echo "<tr bgcolor=$unusualrows><td align=right>Modify Servers: </td><td align=left><select size=1 name=modify_servers><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_servers) . "</select>".helptag("osdial_users-modify_sections")."</td></tr>\n";
				echo "<tr bgcolor=$unusualrows><td align=right>Delete Phones: </td><td align=left><select size=1 name=ast_delete_phones><option value=0>N</option><option value=1>Y</option>" . optnum2let($ast_delete_phones) . "</select>".helptag("osdial_users-ast_delete_phones")."</td></tr>\n";
				echo "<tr bgcolor=$unusualrows><td align=right>Modify Call Times: </td><td align=left><select size=1 name=modify_call_times><option value=0>N</option><option value=1>Y</option>" . optnum2let($modify_call_times) . "</select>".helptag("osdial_users-modify_call_times")."</td></tr>\n";
				echo "<tr bgcolor=$unusualrows><td align=right>Delete Call Times: </td><td align=left><select size=1 name=delete_call_times><option value=0>N</option><option value=1>Y</option>" . optnum2let($delete_call_times) . "</select>".helptag("osdial_users-delete_call_times")."</td></tr>\n";
				echo "<tr bgcolor=$unusualrows><td align=right>Agent API Access: </td><td align=left><select size=1 name=agent_api_access><option value=0>N</option><option value=1>Y</option>" . optnum2let($agent_api_access) . "</select>".helptag("osdial_users-agent_api_access")."</td></tr>\n";
				echo "<tr bgcolor=$unusualrows><td align=right>Admin API Access: </td><td align=left><select size=1 name=admin_api_access><option value=0>N</option><option value=1>Y</option>" . optnum2let($admin_api_access) . "</select>".helptag("osdial_users-admin_api_access")."</td></tr>\n";

				echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr></table>\n";
			} else {
				echo "<br/>\n";
				echo "<input type=hidden name=view_reports value=$view_reports>\n";
				echo "<input type=hidden name=export_leads value=$export_leads>\n";
				echo "<input type=hidden name=alter_agent_interface_options value=$alter_agent_interface_options>\n";
				echo "<input type=hidden name=modify_users value=$modify_users>\n";
				echo "<input type=hidden name=change_agent_campaign value=$change_agent_campaign>\n";
				echo "<input type=hidden name=delete_users value=$delete_users>\n";
				echo "<input type=hidden name=modify_usergroups value=$modify_usergroups>\n";
				echo "<input type=hidden name=delete_user_groups value=$delete_user_groups>\n";
				echo "<input type=hidden name=modify_lists value=$modify_lists>\n";
				echo "<input type=hidden name=delete_lists value=$delete_lists>\n";
				echo "<input type=hidden name=load_leads value=$load_leads>\n";
				echo "<input type=hidden name=modify_leads value=$modify_leads>\n";
				echo "<input type=hidden name=modify_campaigns value=$modify_campaigns>\n";
				echo "<input type=hidden name=campaign_detail value=$campaign_detail>\n";
				echo "<input type=hidden name=delete_campaigns value=$delete_campaigns>\n";

				echo "<input type=hidden name=modify_ingroups value=$modify_ingroups>\n";
				echo "<input type=hidden name=delete_ingroups value=$delete_ingroups>\n";

				echo "<input type=hidden name=modify_remoteagents value=$modify_remoteagents>\n";
				echo "<input type=hidden name=delete_remote_agents value=$delete_remote_agents>\n";

				echo "<input type=hidden name=modify_scripts value=$modify_scripts>\n";
				echo "<input type=hidden name=delete_scripts value=$delete_scripts>\n";

				echo "<input type=hidden name=modify_filters value=$modify_filters>\n";
				echo "<input type=hidden name=delete_filters value=$delete_filters>\n";

				echo "<input type=hidden name=ast_admin_access value=$ast_admin_access>\n";
				echo "<input type=hidden name=ast_delete_phones value=$ast_delete_phones>\n";
				echo "<input type=hidden name=modify_call_times value=$modify_call_times>\n";
				echo "<input type=hidden name=delete_call_times value=$delete_call_times>\n";
				echo "<input type=hidden name=modify_servers value=$modify_servers>\n";
				echo "<input type=hidden name=agent_api_access value=$agent_api_access>\n";
				echo "<input type=hidden name=admin_api_access value=$admin_api_access>\n";
				echo "<input type=hidden name=load_dnc value=$load_dnc>\n";
				echo "<input type=hidden name=export_dnc value=$export_dnc>\n";
				echo "<input type=hidden name=delete_dnc value=$delete_dnc>\n";
			}
			echo "<input type=hidden name=phone_pass value=$phone_pass>\n";
			echo "</center>\n";

			echo "<br><br>";
			echo "<table align=center border=0 cellspacing=0 cellpadding=0 width=$section_width>\n";
			echo "  <tr>\n";
			echo "    <td align=center>Campaign Ranks: ".helptag("osdial_users-campaign_ranks")."</td>\n";
			echo "    <td width=5%>&nbsp;</td>\n";
			echo "    <td align=center>Inbound Groups: ".helptag("osdial_users-closer_campaigns")."</td>\n";
			echo "  </tr>\n";
			echo "  <tr>\n";
			echo "    <td align=center valign=top>\n";
			echo "      <table bgcolor=grey border=0 cellspacing=1 class=shadedtable>\n";
			echo "        $RANKcampaigns_list";
			echo "        <tr class=tabfooter><td align=center class=tabbutton colspan=3><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
			echo "      </table>\n";
			echo "    </td>\n";
			echo "    <td>&nbsp;</td>\n";
			echo "    <td align=center valign=top>\n";
			echo "      <table bgcolor=grey border=0 cellspacing=1 class=shadedtable>\n";
			echo "        $RANKgroups_list";
			echo "        <tr class=tabfooter><td align=center class=tabbutton colspan=4><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
			echo "      </table>\n";
			echo "    </td>\n";
			echo "  </tr>\n";
			echo "</table></div>\n";

			#echo "<center><br><br><br><br><br><a href=\"$PHP_SELF?ADD=999999&SUB=20&agent=$row[1]\">Click here for user time sheet</a>\n";
			#echo "<br><br><a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$row[1]\">Click here for user status</a>\n";
			#echo "<br><br><a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$row[1]\">Click here for user stats</a>\n";
			#echo "<br><br><a href=\"$PHP_SELF?ADD=8&user=$row[1]\">Click here for user CallBack Holds</a></center>\n";
	// 		if ($LOG['delete_users'] > 100) {
	// 			echo "<br><br><a href=\"$PHP_SELF?ADD=5&user=$row[1]\">DELETE THIS AGENT</a>\n";
	// 		}
		}
	} else {
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}

######################
# ADD=550 search form
######################

if ($ADD==550) {
    echo "<center><br><font class=top_header color=$default_text size=+1>SEARCH FOR AN AGENT</font>".helptag("search_agents-summary")."<form action=$PHP_SELF method=POST><br><br>\n";
    echo "<input type=hidden name=ADD value=660>\n";
    echo "<div style=\"width:660px;padding:5px;\">";
    echo "<TABLE class=shadedtable cellspacing=3 cellpadding=2 width=650>\n";
    #echo "<tr bgcolor=$oddrows><td align=right>Agent ID: </td><td align=left><input type=text name=user size=20 maxlength=20></td></tr>\n";
    echo "<tr bgcolor=$oddrows><td align=right width=30%>ID:</td><td align=left>\n";
    if ($LOG['multicomp_admin'] > 0) {
        $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
        echo "<select name=company_id>\n";
        echo '<option value="" selected> -- ALL COMPANIES -- </option>' . "\n";
        foreach ($comps as $comp) {
            echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
        }
        echo "</select>\n";
    } elseif ($LOG['multicomp']>0) {
        echo "<input type=hidden name=company_id value=$LOG[company_id]><font color=$default_text>" . $LOG['company_prefix'] . "</font>&nbsp;";
    }
    echo "<input type=text name=user size=20 maxlength=10>".helptag("search_agents-id")."</td></tr>\n";
    echo "<tr bgcolor=$oddrows><td align=right>Full Name:</td><td align=left><input type=text name=full_name size=30 maxlength=30>".helptag("search_agents-full_name")."</td></tr>\n";
    echo "<tr bgcolor=$oddrows><td align=right>User Level:</td><td align=left>";
	$h=$levelMAX;
	if ($LOG['user_level']==9) {
        $h=$LOG['user_level'];
    } else {
        $h=($LOG['user_level']-1);
    }
    $tuserlevel_list=array(''=>' -- ALL USER LEVELS -- ');
	while ($h>=0) {
        $ullabel='';
		if ($h==0) {
			$ullabel="Disabled";
		} elseif ($h>=1 and $h <=3) {
			$ullabel="Outbound $h";
		} elseif ($h>=4 and $h <=7) {
			$ullabel="Outbound/Inbound/Closer $h";
		} elseif ($h==8) {
			$ullabel="Manager";
		} elseif ($h==9) {
			$ullabel="Administrator";
		} else {
			$ullabel="($h)";
		}
        $tuserlevel_list[$h]=$ullabel;
		$h--;
	}
    echo editableSelectBox($tuserlevel_list, "user_level", '', 50, 50, 'selectBoxForce="1" selectBoxLabel=" -- ALL USER LEVELS -- "');
    echo helptag("search_agents-user_level")."</td></tr>\n";
    #echo "<tr bgcolor=$oddrows><td align=right>User Level: </td><td align=left><select size=1 name=user_level><option selected>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option></select></td></tr>\n";
    echo "<tr bgcolor=$oddrows><td align=right>User Group:</td><td align=left>\n";

	$krh = get_krh($link, 'osdial_user_groups', 'user_group,group_name', 'user_group', sprintf("user_group IN %s",$LOG['allowed_usergroupsSQL']), '');
    $tusergroup_list=array(''=>' -- ALL USER GROUPS -- ');
    foreach ($krh as $ugs) {
        $tusergroup_list[$ugs['user_group']]=$ugs['group_name'];
    }
    echo editableSelectBox($tusergroup_list, "user_group", '', 50, 50, 'selectBoxForce="1" selectBoxLabel=" -- ALL USER GROUPS -- "');

    echo helptag("search_agents-user_group")."</td></tr>\n";

    echo "<tr class=tabfooter><td align=center  class=tabbutton colspan=2><input type=submit name=search value=SEARCH></td></tr>\n";
    echo "</TABLE></div></center>\n";
}

######################
# ADD=660 user search results
######################

if ($ADD==660) {
	if ($LOG['user_level']==9) {
        $levelMAX=$LOG['user_level'];
    } else {
        $levelMAX=($LOG['user_level']-1);
    }

    $preuser = $user;
    if ($LOG['multicomp'] > 0) {
        if ($company_id > 0) {
            $preuser = (($company_id * 1) + 100) . $user;
        } else {
            $preuser = '%' . $user;
        }
    }

    $userSQL='';
    $nameSQL='';
    $levelSQL=sprintf("AND user_level<='%s' ",mres($levelMAX));
    $groupSQL=sprintf("AND user_group IN %s ",$LOG['allowed_usergroupsSQL']);

	if ($preuser)    $userSQL .= sprintf("AND user LIKE '%s%%' ",mres($preuser));
	if ($full_name)  $nameSQL .= sprintf("AND full_name LIKE '%%%s%%' ",mres($full_name));
	if ($user_level) $levelSQL .= sprintf("AND user_level LIKE '%%%s%%' ",mres($user_level));
	if ($user_group) $groupSQL .= sprintf("AND user_group='%s' ",mres($user_group));

    $srt = get_variable('srt');
    if ($srt == '') $srt='user';
    $srtdir = get_variable('srtdir');
    if ($srtdir == '') $srtdir='ASC';

	$stmt=sprintf("SELECT * FROM osdial_users WHERE user NOT IN ('PBX-IN','PBX-OUT') %s %s %s %s ORDER BY %s %s;",$userSQL,$nameSQL,$levelSQL,$groupSQL,mres($srt),mres($srtdir));
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

    if ($srtdir=='ASC') {
        $srtdir='DESC';
    } else {
        $srtdir='ASC';
    }

    echo "<center><br><font size=+1 color=$default_text>SEARCH RESULTS</font><br><br>\n";
    echo "<table align=center class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "  <tr class=\"tabheader\">\n";
    echo "    <td><a href=\"$PHP_SELF?ADD=$ADD&srt=user&srtdir=$srtdir&user=$user&full_name=$full_name&user_level=$user_level&user_group=$user_group&company_id=$company_id\">USER ID</a></td>\n";
    echo "    <td><a href=\"$PHP_SELF?ADD=$ADD&srt=full_name&srtdir=$srtdir&user=$user&full_name=$full_name&user_level=$user_level&user_group=$user_group&company_id=$company_id\">FULL NAME</a></td>\n";
    echo "    <td><a href=\"$PHP_SELF?ADD=$ADD&srt=user_level&srtdir=$srtdir&user=$user&full_name=$full_name&user_level=$user_level&user_group=$user_group&company_id=$company_id\">LEVEL</a></td>\n";
    echo "    <td><a href=\"$PHP_SELF?ADD=$ADD&srt=user_group&srtdir=$srtdir&user=$user&full_name=$full_name&user_level=$user_level&user_group=$user_group&company_id=$company_id\">GROUP</a></td>\n";
    echo "    <td>Links</td>\n";
    echo "  </tr>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "<tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=3&user=$row[1]');\">\n";
        echo "  <td>$row[1]</td>\n";
        echo "  <td>$row[3]</td>\n";
        echo "  <td>$row[4]</td>\n";
        echo "  <td>" . mclabel($row[5]) . "</td>\n";
		echo "  <td align=center><a href=\"$PHP_SELF?ADD=3&user=$row[1]\">MODIFY</a>";
        if ($LOG['view_agent_stats']) echo " | <a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$row[1]\">STATS</a>";
        if ($LOG['view_agent_status']) echo " | <a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$row[1]\">STATUS</a>";
        if ($LOG['view_agent_timesheet']) echo " | <a href=\"$PHP_SELF?ADD=999999&SUB=20&agent=$row[1]\">TIME</a>";
        echo "</td>\n";
        echo "</tr>\n";
		$o++;
	}

    echo "  <tr class=\"tabfooter\">\n";
    echo "    <td colspan=5></td>\n";
    echo "  </tr>\n";
    echo "</table></center>\n";

}

######################
# ADD=8 find all callbacks on hold by an Agent
######################
if ($ADD==8) {
	if ($LOG['modify_users']==1) {
		if ($SUB==89) {
		    $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE user='%s' AND status='LIVE' AND callback_time<'%s';",mres($user),mres($past_month_date));
		    $rslt=mysql_query($stmt, $link);
		   echo "<br>Agent ($user) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899) {
		    $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE user='%s' AND status='LIVE' AND callback_time<'%s';",mres($user),mres($past_week_date));
		    $rslt=mysql_query($stmt, $link);
		    echo "<br>Agent ($user) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
    $CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=8&SUB=89&user=$user\"><font color=$default_text>Remove LIVE Callbacks older than one month for this user</font></a><BR><a href=\"$PHP_SELF?ADD=8&SUB=899&user=$user\"><font color=$default_text>Remove LIVE Callbacks older than one week for this user</font></a><BR>";
	$CBquerySQLwhere = "and user='$user'";

echo "<br><br><center><font color=$default_text size=4>AGENT CALLBACK HOLD LISTINGS: $user</font></center>\n";
$oldADD = "ADD=8&user=$user";
$ADD='82';
include($WeBServeRRooT . '/admin/include/content/lists/lists.php');
}


######################
# ADD=0 display all active users
######################
if ($ADD==0 or $ADD==9)
{
$let = get_variable('let');
$letSQL = '';
if ($let != '') $letSQL = sprintf("AND (user LIKE '%s%s%%' OR full_name LIKE '%s%%' OR full_name LIKE '%% %s%%')",$LOG['company_prefix'],mres($let),mres($let),mres($let));

$num = get_variable('num');
$numSQL = '';
if ($num != '') $numSQL = sprintf("AND (user LIKE '%s%s%%')",$LOG['company_prefix'],mres($num));

	if ($LOG['user_level']==9) {
        $levelMAX=$LOG['user_level'];
    } else {
        $levelMAX=($LOG['user_level']-1);
    }

$level = get_variable('level');
$levelSQL = sprintf("AND user_level <= '%s'",mres($levelMAX));
if ($level != '') $levelSQL .= sprintf(" AND user_level='%s'",mres($level));

$group = get_variable('group');
$groupSQL = '';
if ($group != '') $groupSQL = sprintf("AND user_group='%s'",mres($group));

$viewdisabled = get_variable('viewdisabled');
$viewdisabledSQL = '';
$viewdisabledSQL = "AND user_level>'0'";
if ($viewdisabled != '') $viewdisabledSQL = "";

$mdn_user = get_variable('mdn_user');
if ($SUB==1 and $mdn_user != "" and $LOG['modify_users'] > 0 and $LOG['user_level'] > 7) {
    $mdn_limit = get_variable('mdn_limit');
    if ($mdn_limit == "" or $mdn_limit < 0)
        $mdn_limit = -1;
    $stmt=sprintf("UPDATE osdial_users SET manual_dial_new_limit='%s' WHERE user='%s';",mres($mdn_limit),mres($mdn_user));
    $rslt=mysql_query($stmt, $link);
}

$USERlink='stage=USERIDDOWN';
$NAMElink='stage=NAMEDOWN';
$LEVELlink='stage=LEVELDOWN';
$GROUPlink='stage=GROUPDOWN';
$SQLorder='order by full_name';
if (OSDpreg_match("/USERIDUP/",$stage)) {$SQLorder='order by user asc';   $USERlink='stage=USERIDDOWN';}
if (OSDpreg_match("/USERIDDOWN/",$stage)) {$SQLorder='order by user desc';   $USERlink='stage=USERIDUP';}
if (OSDpreg_match("/NAMEUP/",$stage)) {$SQLorder='order by full_name asc';   $NAMElink='stage=NAMEDOWN';}
if (OSDpreg_match("/NAMEDOWN/",$stage)) {$SQLorder='order by full_name desc';   $NAMElink='stage=NAMEUP';}
if (OSDpreg_match("/LEVELUP/",$stage)) {$SQLorder='order by user_level asc';   $LEVELlink='stage=LEVELDOWN';}
if (OSDpreg_match("/LEVELDOWN/",$stage)) {$SQLorder='order by user_level desc';   $LEVELlink='stage=LEVELUP';}
if (OSDpreg_match("/GROUPUP/",$stage)) {$SQLorder='order by user_group asc';   $GROUPlink='stage=GROUPDOWN';}
if (OSDpreg_match("/GROUPDOWN/",$stage)) {$SQLorder='order by user_group desc';   $GROUPlink='stage=GROUPUP';}
    if ($LOG['multicomp_admin'] > 0) {
	    $stmt = sprintf("SELECT * from osdial_users WHERE user NOT IN ('PBX-IN','PBX-OUT') %s %s %s %s %s %s",$letSQL,$numSQL,$levelSQL,$groupSQL,$viewdisabledSQL,$SQLorder);
    } elseif ($LOG['multicomp'] > 0) {
	    $stmt = sprintf("SELECT * from osdial_users WHERE user LIKE '%s__%%' AND user_group IN %s AND user NOT IN ('PBX-IN','PBX-OUT') %s %s %s %s %s %s",mres($LOG['company_prefix']),$LOG['allowed_usergroupsSQL'],$letSQL,$numSQL,$levelSQL,$groupSQL,$viewdisabledSQL,$SQLorder);
    } else {
	    $stmt = sprintf("SELECT * from osdial_users WHERE user_group IN %s AND user NOT IN ('PBX-IN','PBX-OUT') %s %s %s %s %s %s",$LOG['allowed_usergroupsSQL'],$letSQL,$numSQL,$levelSQL,$groupSQL,$viewdisabledSQL,$SQLorder);
    }
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

    echo "<center><br><font size=+1 class=top_header color=$default_text>AGENTS</font><br>\n";
    echo "<font color=$default_text size=-1>";
    if ($viewdisabled == '1') {
        echo "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&viewdisabled=0&let=$let&num=$num\">(Hide Disabled Users)</a>";
    } else {
        echo "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&viewdisabled=1&let=$let&num=$num\">(Show Disabled Users)</a>";
    }
    echo "</font><br>\n";

echo "<br>\n";
echo "<center><font size=-1 color=$default_text>&nbsp;|&nbsp;";
echo "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&viewdisabled=$viewdisabled&let=&num=\">-ALL-</a>&nbsp;|&nbsp;";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;";
foreach (range('A','Z') as $slet) {
    echo (($let == "$slet") ? $slet : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&viewdisabled=$viewdisabled&num=$num&let=$slet\">$slet</a>") . "&nbsp;|&nbsp;";
}
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;";
foreach (range('0','9') as $snum) {
    echo (($num == "$snum") ? $snum : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&viewdisabled=$viewdisabled&let=$let&num=$snum\">$snum</a>") . "&nbsp;|&nbsp;";
}
echo "</font><br>\n";

echo "<table width=$section_width class=shadedtable cellspacing=0 cellpadding=1 align=center>\n";
echo "  <tr class=tabheader>\n";
if ($LOG['multicomp_admin'] > 0) {
    echo "    <td width=150><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&viewdisabled=$viewdisabled&$USERlink\">Company<span style=\"color:#900;font-weight:bold;\">:</span>Agent</a></td>\n";
} else {
    echo "    <td width=150><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&viewdisabled=$viewdisabled&$USERlink\">ID</a></td>\n";
}
echo "    <td><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&viewdisabled=$viewdisabled&$NAMElink\">FULL NAME</a></td>\n";
if ($ADD==9) {
    echo "    <td align=center>NEW ATTEMPTS</td>\n";
    echo "    <td align=center>NEW ATTEMPT LIMIT</td>\n";
    echo "    <td align=right>CONTACTS</td>\n";
    echo "    <td align=right>SALES</td>\n";
    echo "    <td align=right>CLOSING%</td>\n";
    echo "    <td align=center>LINKS</td>\n";
} else {
    echo "    <td align=center><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&viewdisabled=$viewdisabled&$LEVELlink\">LEVEL</a></td>\n";
    echo "    <td><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&viewdisabled=$viewdisabled&$GROUPlink\">GROUP</a></td>\n";
    echo "    <td align=center>LINKS</td>";
}
echo "  </tr>\n";

    $new_count = 0;
    $sales_count = 0;
    $contact_count = 0;

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
            echo "  <form action=$PHP_SELF method=POST>\n";
            echo "  <input type=hidden name=ADD value=$ADD>\n";
            echo "  <input type=hidden name=SUB value=1>\n";
        if ($row[5] != "VIRTUAL") {
		    echo "  <tr class=\"row font1\" " . bgcolor($o) . " ondblclick=\"window.location='$PHP_SELF?ADD=3&user=$row[1]';\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=3&user=$row[1]\">";
            if (($LOG['multicomp'] > 0 and OSDpreg_match($LOG['companiesRE'],$row[1])) or $LOG['multicomp_user'] > 0) {
                echo OSDsubstr($row[1],0,3) . "<span style=\"color:#900;font-weight:bold;\">:</span>" . OSDsubstr($row[1],3,OSDstrlen($row[1]));;
            } else {
                echo $row[1];
            }
            echo "</a></td>\n";
            echo "    <td>$row[3]</td>\n";
            if ($ADD==9) {
	            $stmt2=sprintf("SELECT SUM(manual_dial_new_today),status_category_1,SUM(status_category_count_1),status_category_2,SUM(status_category_count_2),status_category_3,SUM(status_category_count_3),status_category_4,SUM(status_category_count_4) FROM osdial_campaign_agent_stats WHERE user='%s' GROUP BY user;",mres($row[1]));
	            $rslt2=mysql_query($stmt2, $link);
		        $row2=mysql_fetch_row($rslt2);
                $stat['SALE'] = 0;
                $stat['CONTACT'] = 0;
                $stat[$row2[1]] = $row2[2];
                $stat[$row2[3]] = $row2[4];
                $stat[$row2[5]] = $row2[6];
                $stat[$row2[7]] = $row2[8];
                $new_count += $row2[0];
                $sales_count += $stat['SALE'];
                $contact_count += $stat['CONTACT'];
                $close_pct = 0;
                if ($row[46] < 0) $row[46] = "";
                if ($stat['CONTACT'] > 0) $close_pct = (($stat['SALE'] / ($stat['CONTACT'] + $stat['SALE'])) * 100);
                echo "    <td align=center>$row2[0]</td>\n";
                echo "    <td align=center class=tabinput>\n";
                if ($LOG['modify_users'] > 0 and $LOG['user_level'] > 7) {
                    echo "      <input type=hidden name=mdn_user value=$row[1]><input type=text name=mdn_limit size=5 value=$row[46]>\n";
                } else {
                    echo "      $row[46]\n";
                }
                echo "    </td>\n";
                echo "    <td align=right>" . ($stat['CONTACT'] + $stat['SALE']) . "</td>\n";
                echo "    <td align=right>" . $stat['SALE'] . "</td>\n";
                echo "    <td align=right>" . sprintf('%5.2f',$close_pct) . " %</td>\n";
            } else {
                echo "    <td align=center><a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$row[4]&group=$group&viewdisabled=$viewdisabled&let=$let\">$row[4]</a></td>\n";
                echo "    <td><a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$row[5]&viewdisabled=$viewdisabled&let=$let\">" . mclabel($row[5]) . "</a></td>\n";
            }
		    echo "    <td align=center class=font1 nowrap><a href=\"$PHP_SELF?ADD=3&user=$row[1]\">MODIFY</a>";
            if ($LOG['view_agent_stats']) echo " | <a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$row[1]\">STATS</a>";
            if ($LOG['view_agent_status']) echo " | <a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$row[1]\">STATUS</a>";
            if ($LOG['view_agent_timesheet']) echo " | <a href=\"$PHP_SELF?ADD=999999&SUB=20&agent=$row[1]\">TIME</a>";
            echo "</td>\n";
            echo "  </tr>\n";
        }
        echo "</form>\n";
		$o++;
	}
    if ($ADD==9) {
        $close_pct = 0;
        if ($contact_count > 0) $close_pct = (($sales_count / ($contact_count + $sales_count)) * 100);
        echo "  <tr class=tabfooter>\n";
        echo "    <td>&nbsp;</td>\n";
        echo "    <td>&nbsp;</td>\n";
        echo "    <td align=center>" . $new_count . "</td>\n";
        echo "    <td align=center>&nbsp;</td>\n";
        echo "    <td align=right>" . ($contact_count + $sales_count) . "</td>\n";
        echo "    <td align=right>" . $sales_count . "</td>\n";
        echo "    <td align=right>" . sprintf('%5.2f',$close_pct) . " %</td>\n";
        echo "    <td>&nbsp;</td>\n";
        echo "  </tr>\n";
    } else {
        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=5></td>\n";
        echo "  </tr>\n";
    }

echo "</table>\n";
echo "</center>\n";


}

?>
