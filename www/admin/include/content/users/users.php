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

if ($ADD=="1")
{
	if ($LOGmodify_users==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<center><br><font color=$default_text size=+1>ADD A NEW AGENT<form action=$PHP_SELF method=POST></font><br><br>\n";
	echo "<input type=hidden name=ADD value=2>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Agent Number: </td><td align=left><input type=text name=user size=20 maxlength=10>$NWB#osdial_users-user$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10>$NWB#osdial_users-pass$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=20 maxlength=100>$NWB#osdial_users-full_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>User Level: </td><td align=left><select size=1 name=user_level>";
	$h=1;
	while ($h<=$LOGuser_level)
		{
		echo "<option>$h</option>";
		$h++;
		}
	echo "</select>$NWB#osdial_users-user_level$NWE</td></tr>\n";
	
	echo "<tr bgcolor=$oddrows><td align=right>User Group: </td><td align=left><select size=1 name=user_group>\n";

		$stmt="SELECT user_group,group_name from osdial_user_groups order by user_group";
		$rslt=mysql_query($stmt, $link);
		$Ugroups_to_print = mysql_num_rows($rslt);
		$Ugroups_list='';

		$o=0;
		while ($Ugroups_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$Ugroups_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
		}
	echo "$Ugroups_list";
	echo "<option SELECTED>$user_group</option>\n";
	echo "</select>$NWB#osdial_users-user_group$NWE</td></tr>\n";
	
	echo "<tr bgcolor=$oddrows><td align=right>Phone Login: </td><td align=left><input type=text name=phone_login size=20 maxlength=20>$NWB#osdial_users-phone_login$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Phone Pass: </td><td align=left><input type=text name=phone_pass size=20 maxlength=20>$NWB#osdial_users-phone_pass$NWE</td></tr>\n";
	
	echo "<tr><td align=center colspan=2><input type=submit name=SUBMIT value=ADD style=\"width: 100%;\"></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=1A display the COPY USER FORM SCREEN
######################

if ($ADD=="1A")
{
	if ($LOGmodify_users==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<center><br><font color=$default_text size=+1>COPY AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2A>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Agent Number: </td><td align=left><input type=text name=user size=20 maxlength=10>$NWB#osdial_users-user$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10>$NWB#osdial_users-pass$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=20 maxlength=100>$NWB#osdial_users-full_name$NWE</td></tr>\n";

	if ($LOGuser_level==9) {$levelMAX=10;}
	else {$levelMAX=$LOGuser_level;}

	echo "<tr bgcolor=$oddrows><td align=right>Source Agent: </td><td align=left><select size=1 name=source_user_id>\n";

		$stmt="SELECT user,full_name from osdial_users where user_level < $levelMAX order by full_name;";
		$rslt=mysql_query($stmt, $link);
		$Uusers_to_print = mysql_num_rows($rslt);
		$Uusers_list='';

		$o=0;
		while ($Uusers_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$Uusers_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
		}
	echo "$Uusers_list";
	echo "</select>$NWB#osdial_users-user$NWE</td></tr>\n";
	echo "<tr><td align=center colspan=2><input type=submit name=SUBMIT value=COPY style=\"width: 100%;\"></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}

######################
# ADD=2 adds the new user to the system
######################

if ($ADD=="2")
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	$stmt="SELECT count(*) from osdial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> AGENT NOT ADDED - there is already a user in the system with this user number</font>\n";}
	else
		{
		 if ( (strlen($user) < 2) or (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user) > 8) )
			{
			 echo "<br><font color=red> AGENT NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>user id must be between 2 and 8 characters long\n";
			 echo "<br>full name and password must be at least 2 characters long</font><br>\n";
			}
		 else
			{
			echo "<br><B>AGENT ADDED: $user</B>\n";

			$stmt="INSERT INTO osdial_users (user,pass,full_name,user_level,user_group,phone_login,phone_pass) values('$user','$pass','$full_name','$user_level','$user_group','$phone_login','$phone_pass');";
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
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	$stmt="SELECT count(*) from osdial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> AGENT NOT ADDED - there is already a user in the system with this user number</font>\n";}
	else
		{
		 if ( (strlen($user) < 2) or (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user) > 8) )
			{
			 echo "<br><font color=red> AGENT NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>user id must be between 2 and 8 characters long\n";
			 echo "<br>full name and password must be at least 2 characters long</font><br>\n";
			}
		 else
			{
			$stmt="INSERT INTO osdial_users (user,pass,full_name,user_level,user_group,phone_login,phone_pass,delete_users,delete_user_groups,delete_lists,delete_campaigns,delete_ingroups,delete_remote_agents,load_leads,campaign_detail,ast_admin_access,ast_delete_phones,delete_scripts,modify_leads,hotkeys_active,change_agent_campaign,agent_choose_ingroups,closer_campaigns,scheduled_callbacks,agentonly_callbacks,agentcall_manual,osdial_recording,osdial_transfers,delete_filters,alter_agent_interface_options,closer_default_blended,delete_call_times,modify_call_times,modify_users,modify_campaigns,modify_lists,modify_scripts,modify_filters,modify_ingroups,modify_usergroups,modify_remoteagents,modify_servers,view_reports,osdial_recording_override,alter_custdata_override,manual_dial_allow_skip) SELECT \"$user\",\"$pass\",\"$full_name\",user_level,user_group,phone_login,phone_pass,delete_users,delete_user_groups,delete_lists,delete_campaigns,delete_ingroups,delete_remote_agents,load_leads,campaign_detail,ast_admin_access,ast_delete_phones,delete_scripts,modify_leads,hotkeys_active,change_agent_campaign,agent_choose_ingroups,closer_campaigns,scheduled_callbacks,agentonly_callbacks,agentcall_manual,osdial_recording,osdial_transfers,delete_filters,alter_agent_interface_options,closer_default_blended,delete_call_times,modify_call_times,modify_users,modify_campaigns,modify_lists,modify_scripts,modify_filters,modify_ingroups,modify_usergroups,modify_remoteagents,modify_servers,view_reports,osdial_recording_override,alter_custdata_override,manual_dial_allow_skip from osdial_users where user=\"$source_user_id\";";
			$rslt=mysql_query($stmt, $link);

			$stmtA="INSERT INTO osdial_inbound_group_agents (user,group_id,group_rank,group_weight,calls_today) SELECT \"$user\",group_id,group_rank,group_weight,\"0\" from osdial_inbound_group_agents where user=\"$source_user_id\";";
			$rslt=mysql_query($stmtA, $link);

			$stmtA="INSERT INTO osdial_campaign_agents (user,campaign_id,campaign_rank,campaign_weight,calls_today) SELECT \"$user\",campaign_id,campaign_rank,campaign_weight,\"0\" from osdial_campaign_agents where user=\"$source_user_id\";";
			$rslt=mysql_query($stmtA, $link);

			echo "<br><B><font color=$default_text> AGENT COPIED: $user copied from $source_user_id</font></B>\n";
			echo "<br><br>\n";
			echo "<a href=\"$PHP_SELF?ADD=3&user=$user\">Click here to go to the user record</a>\n";
			echo "<br><br>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A COPIED AGENT   |$PHP_AUTH_USER|$ip|$user|$source_user_id|$stmt|\n");
				fclose($fp);
				}
			}
		}
exit;
}

######################
# ADD=4A submit user modifications to the system - ADMIN
######################

if ($ADD=="4A")
{
	if ($LOGmodify_users==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user_level) < 1) )
		{
		 echo "<br><font color=red>AGENT NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>AGENT MODIFIED - ADMIN: $user</font></B>\n";

		$stmt="UPDATE osdial_users set pass='$pass',full_name='$full_name',user_level='$user_level',user_group='$user_group',phone_login='$phone_login',phone_pass='$phone_pass',delete_users='$delete_users',delete_user_groups='$delete_user_groups',delete_lists='$delete_lists',delete_campaigns='$delete_campaigns',delete_ingroups='$delete_ingroups',delete_remote_agents='$delete_remote_agents',load_leads='$load_leads',campaign_detail='$campaign_detail',ast_admin_access='$ast_admin_access',ast_delete_phones='$ast_delete_phones',delete_scripts='$delete_scripts',modify_leads='$modify_leads',hotkeys_active='$hotkeys_active',change_agent_campaign='$change_agent_campaign',agent_choose_ingroups='$agent_choose_ingroups',closer_campaigns='$groups_value',scheduled_callbacks='$scheduled_callbacks',agentonly_callbacks='$agentonly_callbacks',agentcall_manual='$agentcall_manual',osdial_recording='$osdial_recording',osdial_transfers='$osdial_transfers',delete_filters='$delete_filters',alter_agent_interface_options='$alter_agent_interface_options',closer_default_blended='$closer_default_blended',delete_call_times='$delete_call_times',modify_call_times='$modify_call_times',modify_users='$modify_users',modify_campaigns='$modify_campaigns',modify_lists='$modify_lists',modify_scripts='$modify_scripts',modify_filters='$modify_filters',modify_ingroups='$modify_ingroups',modify_usergroups='$modify_usergroups',modify_remoteagents='$modify_remoteagents',modify_servers='$modify_servers',view_reports='$view_reports',osdial_recording_override='$osdial_recording_override',alter_custdata_override='$alter_custdata_override',manual_dial_allow_skip='$manual_dial_allow_skip' where user='$user';";
		$rslt=mysql_query($stmt, $link);



		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY AGENT INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo " <font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3;		# go to user modification below
}


######################
# ADD=4B submit user modifications to the system - ADMIN
######################

if ($ADD=="4B")
{
	if ($LOGmodify_users==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user_level) < 1) )
		{
		 echo "<br><font color=red>AGENT NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>AGENT MODIFIED - ADMIN: $user</font></B>\n";

		$stmt="UPDATE osdial_users set pass='$pass',full_name='$full_name',user_level='$user_level',user_group='$user_group',phone_login='$phone_login',phone_pass='$phone_pass',hotkeys_active='$hotkeys_active',agent_choose_ingroups='$agent_choose_ingroups',closer_campaigns='$groups_value',scheduled_callbacks='$scheduled_callbacks',agentonly_callbacks='$agentonly_callbacks',agentcall_manual='$agentcall_manual',osdial_recording='$osdial_recording',osdial_transfers='$osdial_transfers',closer_default_blended='$closer_default_blended',osdial_recording_override='$osdial_recording_override',alter_custdata_override='$alter_custdata_override',manual_dial_allow_skip='$manual_dial_allow_skip' where user='$user';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY AGENT INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3;		# go to user modification below
}



######################
# ADD=4 submit user modifications to the system
######################

if ($ADD==4)
{
	if ($LOGmodify_users==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($pass) < 2) or (strlen($full_name) < 2) or (strlen($user_level) < 1) )
		{
		 echo "<br><font color=red>AGENT NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Password and Full Name each need ot be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>AGENT MODIFIED: $user</font></B>\n";

		$stmt="UPDATE osdial_users set pass='$pass',full_name='$full_name',user_level='$user_level',user_group='$user_group',phone_login='$phone_login',phone_pass='$phone_pass' where user='$user';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY AGENT INFO    |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=3;		# go to user modification below
}


######################
# ADD=5 confirmation before deletion of user
######################

if ($ADD==5)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($user) < 2) or ($LOGdelete_users < 1) )
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
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( ( strlen($user) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_users < 1) )
		{
		 echo "<br><font color=red>AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Agent be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmtA="DELETE from osdial_users where user='$user' limit 1;";
		$rslt=mysql_query($stmtA, $link);

		$stmt="DELETE from osdial_campaign_agents where user='$user';";
		$rslt=mysql_query($stmt, $link);

		$stmt="DELETE from osdial_inbound_group_agents where user='$user';";
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

if ($ADD==3)
{
	if ($LOGmodify_users==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$stmt="SELECT * from osdial_users where user='$user';";
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

	if ( ($user_level >= $LOGuser_level) and ($LOGuser_level < 9) )
		{
		echo "<br><font color=red>You do not have permissions to modify this user: $row[1]</font>\n";
		}
	else
		{
		echo "<center><br><font color=$default_text size=+1>MODIFY AN AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
		if ($LOGuser_level > 8)
			{echo "<input type=hidden name=ADD value=4A>\n";}
		else
			{
			if ($LOGalter_agent_interface == "1")
				{echo "<input type=hidden name=ADD value=4B>\n";}
			else
				{echo "<input type=hidden name=ADD value=4>\n";}
			}
		echo "<input type=hidden name=user value=\"$row[1]\">\n";
		echo "<TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Agent Number: </td><td align=left><b>$row[1]</b>$NWB#osdial_users-user$NWE</td></tr>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=pass size=20 maxlength=10 value=\"$row[2]\">$NWB#osdial_users-pass$NWE</td></tr>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=30 maxlength=30 value=\"$row[3]\">$NWB#osdial_users-full_name$NWE</td></tr>\n";
		echo "<tr bgcolor=$oddrows><td align=right>User Level: </td><td align=left><select size=1 name=user_level>";
		$h=1;
		while ($h<=$LOGuser_level)
			{
			echo "<option>$h</option>";
			$h++;
			}
		echo "<option SELECTED>$row[4]</option></select>$NWB#osdial_users-user_level$NWE</td></tr>\n";
		echo "<tr bgcolor=$oddrows><td align=right><A HREF=\"$PHP_SELF?ADD=311111&user_group=$user_group\">User Group</A>: </td><td align=left><select size=1 name=user_group>\n";

			$stmt="SELECT user_group,group_name from osdial_user_groups order by user_group";
			$rslt=mysql_query($stmt, $link);
			$Ugroups_to_print = mysql_num_rows($rslt);
			$Ugroups_list='';

			$o=0;
			while ($Ugroups_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$Ugroups_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				$o++;
			}
		echo "$Ugroups_list";
		echo "<option SELECTED>$user_group</option>\n";
		echo "</select>$NWB#osdial_users-user_group$NWE</td></tr>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Phone Login: </td><td align=left><input type=text name=phone_login size=20 maxlength=20 value=\"$phone_login\">$NWB#osdial_users-phone_login$NWE</td></tr>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Phone Pass: </td><td align=left><input type=text name=phone_pass size=20 maxlength=20 value=\"$phone_pass\">$NWB#osdial_users-phone_pass$NWE</td></tr>\n";
		echo "<tr><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT style=\"width: 100%;\"></td></tr>\n";

		if ( ($LOGuser_level > 8) or ($LOGalter_agent_interface == "1") )
			{
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr bgcolor=$menubarcolor><td colspan=2 align=center><font color=white><B>AGENT INTERFACE OPTIONS:</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Agent Choose Ingroups: </td><td align=left><select size=1 name=agent_choose_ingroups><option>0</option><option>1</option><option SELECTED>$agent_choose_ingroups</option></select>$NWB#osdial_users-agent_choose_ingroups$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Hot Keys Active: </td><td align=left><select size=1 name=hotkeys_active><option>0</option><option>1</option><option SELECTED>$hotkeys_active</option></select>$NWB#osdial_users-hotkeys_active$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Scheduled Callbacks: </td><td align=left><select size=1 name=scheduled_callbacks><option>0</option><option>1</option><option SELECTED>$scheduled_callbacks</option></select>$NWB#osdial_users-scheduled_callbacks$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Agent-Only Callbacks: </td><td align=left><select size=1 name=agentonly_callbacks><option>0</option><option>1</option><option SELECTED>$agentonly_callbacks</option></select>$NWB#osdial_users-agentonly_callbacks$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Agent Call Manual: </td><td align=left><select size=1 name=agentcall_manual><option>0</option><option>1</option><option SELECTED>$agentcall_manual</option></select>$NWB#osdial_users-agentcall_manual$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>$t1 Recording: </td><td align=left><select size=1 name=osdial_recording><option>0</option><option>1</option><option SELECTED>$osdial_recording</option></select>$NWB#osdial_users-osdial_recording$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>$t1 Transfers: </td><td align=left><select size=1 name=osdial_transfers><option>0</option><option>1</option><option SELECTED>$osdial_transfers</option></select>$NWB#osdial_users-osdial_transfers$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Closer Default Blended: </td><td align=left><select size=1 name=closer_default_blended><option>0</option><option>1</option><option SELECTED>$closer_default_blended</option></select>$NWB#osdial_users-closer_default_blended$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>$t1 Recording Override: </td><td align=left><select size=1 name=osdial_recording_override><option>DISABLED</option><option>NEVER</option><option>ONDEMAND</option><option>ALLCALLS</option><option>ALLFORCE</option><option SELECTED>$osdial_recording_override</option></select>$NWB#osdial_users-osdial_recording_override$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Agent Alter Customer Data Override: </td><td align=left><select size=1 name=alter_custdata_override><option>NOT_ACTIVE</option><option>ALLOW_ALTER</option><option SELECTED>$alter_custdata_override</option></select>$NWB#osdial_users-alter_custdata_override$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Manual-Dial Allow Skip-Lead: </td><td align=left><select size=1 name=manual_dial_allow_skip><option>0</option><option>1</option><option SELECTED>$manual_dial_allow_skip</option></select>$NWB#osdial_users-manual_dial_allow_skip$NWE</td></tr>\n";
			echo "<tr><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT style=\"width: 100%;\"></td></tr>\n";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr bgcolor=$oddrows><td align=center colspan=2>Campaign Ranks: $NWB#osdial_users-campaign_ranks$NWE<BR>\n";
			echo "<table border=0>\n";
			echo "$RANKcampaigns_list";
			echo "</table>\n";
			echo "<tr><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT style=\"width: 100%;\"></td></tr>\n";
			echo "</td></tr>\n";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr bgcolor=$oddrows><td align=center colspan=2>Inbound Groups: $NWB#osdial_users-closer_campaigns$NWE<BR>\n";
			echo "<table border=0>\n";
			echo "$RANKgroups_list";
			echo "</table>\n";
			echo "</td></tr>\n";
			echo "<tr><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT style=\"width: 100%;\"></td></tr>\n";
			}
		if ($LOGuser_level > 8 && $user_level > 7)
			{
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr bgcolor=$menubarcolor><td colspan=2 align=center><font color=white><B>ADMIN INTERFACE OPTIONS:</td></tr>\n";


			echo "<tr bgcolor=$oddrows><td align=right>View Reports: </td><td align=left><select size=1 name=view_reports><option>0</option><option>1</option><option SELECTED>$view_reports</option></select>$NWB#osdial_users-view_reports$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>Alter Agent Interface Options: </td><td align=left><select size=1 name=alter_agent_interface_options><option>0</option><option>1</option><option SELECTED>$alter_agent_interface_options</option></select>$NWB#osdial_users-alter_agent_interface_options$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Modify Agents: </td><td align=left><select size=1 name=modify_users><option>0</option><option>1</option><option SELECTED>$modify_users</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Change Agent Campaign: </td><td align=left><select size=1 name=change_agent_campaign><option>0</option><option>1</option><option SELECTED>$change_agent_campaign</option></select>$NWB#osdial_users-change_agent_campaign$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete Agents: </td><td align=left><select size=1 name=delete_users><option>0</option><option>1</option><option SELECTED>$delete_users</option></select>$NWB#osdial_users-delete_users$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>Modify User Groups: </td><td align=left><select size=1 name=modify_usergroups><option>0</option><option>1</option><option SELECTED>$modify_usergroups</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete User Groups: </td><td align=left><select size=1 name=delete_user_groups><option>0</option><option>1</option><option SELECTED>$delete_user_groups</option></select>$NWB#osdial_users-delete_user_groups$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>Modify Lists: </td><td align=left><select size=1 name=modify_lists><option>0</option><option>1</option><option SELECTED>$modify_lists</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete Lists: </td><td align=left><select size=1 name=delete_lists><option>0</option><option>1</option><option SELECTED>$delete_lists</option></select>$NWB#osdial_users-delete_lists$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Load Leads: </td><td align=left><select size=1 name=load_leads><option>0</option><option>1</option><option SELECTED>$load_leads</option></select>$NWB#osdial_users-load_leads$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Modify Leads: </td><td align=left><select size=1 name=modify_leads><option>0</option><option>1</option><option SELECTED>$modify_leads</option></select>$NWB#osdial_users-modify_leads$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>Modify Campaigns: </td><td align=left><select size=1 name=modify_campaigns><option>0</option><option>1</option><option SELECTED>$modify_campaigns</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Campaign Detail: </td><td align=left><select size=1 name=campaign_detail><option>0</option><option>1</option><option SELECTED>$campaign_detail</option></select>$NWB#osdial_users-campaign_detail$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete Campaigns: </td><td align=left><select size=1 name=delete_campaigns><option>0</option><option>1</option><option SELECTED>$delete_campaigns</option></select>$NWB#osdial_users-delete_campaigns$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>Modify In-Groups: </td><td align=left><select size=1 name=modify_ingroups><option>0</option><option>1</option><option SELECTED>$modify_ingroups</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete In-Groups: </td><td align=left><select size=1 name=delete_ingroups><option>0</option><option>1</option><option SELECTED>$delete_ingroups</option></select>$NWB#osdial_users-delete_ingroups$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>Modify Remote Agents: </td><td align=left><select size=1 name=modify_remoteagents><option>0</option><option>1</option><option SELECTED>$modify_remoteagents</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete Remote Agents: </td><td align=left><select size=1 name=delete_remote_agents><option>0</option><option>1</option><option SELECTED>$delete_remote_agents</option></select>$NWB#osdial_users-delete_remote_agents$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>Modify Scripts: </td><td align=left><select size=1 name=modify_scripts><option>0</option><option>1</option><option SELECTED>$modify_scripts</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete Scripts: </td><td align=left><select size=1 name=delete_scripts><option>0</option><option>1</option><option SELECTED>$delete_scripts</option></select>$NWB#osdial_users-delete_scripts$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>Modify Filters: </td><td align=left><select size=1 name=modify_filters><option>0</option><option>1</option><option SELECTED>$modify_filters</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete Filters: </td><td align=left><select size=1 name=delete_filters><option>0</option><option>1</option><option SELECTED>$delete_filters</option></select>$NWB#osdial_users-delete_filters$NWE</td></tr>\n";

			echo "<tr bgcolor=$oddrows><td align=right>AGC Admin Access: </td><td align=left><select size=1 name=ast_admin_access><option>0</option><option>1</option><option SELECTED>$ast_admin_access</option></select>$NWB#osdial_users-ast_admin_access$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>AGC Delete Phones: </td><td align=left><select size=1 name=ast_delete_phones><option>0</option><option>1</option><option SELECTED>$ast_delete_phones</option></select>$NWB#osdial_users-ast_delete_phones$NWE</td></tr>\n";
			echo "<tr bgcolor=$unusualrows><td align=right>Modify Call Times: </td><td align=left><select size=1 name=modify_call_times><option>0</option><option>1</option><option SELECTED>$modify_call_times</option></select>$NWB#osdial_users-modify_call_times$NWE</td></tr>\n";
			echo "<tr bgcolor=$oddrows><td align=right>Delete Call Times: </td><td align=left><select size=1 name=delete_call_times><option>0</option><option>1</option><option SELECTED>$delete_call_times</option></select>$NWB#osdial_users-delete_call_times$NWE</td></tr>\n";
			echo "<tr bgcolor=$unusualrows><td align=right>Modify Servers: </td><td align=left><select size=1 name=modify_servers><option>0</option><option>1</option><option SELECTED>$modify_servers</option></select>$NWB#osdial_users-modify_sections$NWE</td></tr>\n";
			echo "<tr><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT style=\"width: 100%;\"></td></tr>\n";
		} else {
			echo "<input type=hidden name=view_reports value=$view_reports>\n";
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
		}
		echo "</TABLE></center>\n";

		echo "<center><br><br><a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=AST_agent_time_sheet.php?agent=$row[1]\">Click here for user time sheet</a>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=user_status.php?user=$row[1]\">Click here for user status</a>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=user_stats.php?user=$row[1]\">Click here for user stats</a>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=8&user=$row[1]\">Click here for user CallBack Holds</a></center>\n";
		if ($LOGdelete_users > 0)
			{
			echo "<br><br><a href=\"$PHP_SELF?ADD=5&user=$row[1]\">DELETE THIS AGENT</a>\n";
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}

######################
# ADD=550 search form
######################

if ($ADD==550)
{
echo "<TABLE align=center><TR><TD>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

echo "<center><br><font color=$default_text size=+1>SEARCH FOR AN AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=660>\n";
echo "<TABLE width=$section_width cellspacing=3>\n";
echo "<tr bgcolor=$oddrows><td align=right>Agent Number: </td><td align=left><input type=text name=user size=20 maxlength=20></td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=full_name size=30 maxlength=30></td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>User Level: </td><td align=left><select size=1 name=user_level><option selected>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option></select></td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>User Group: </td><td align=left><select size=1 name=user_group>\n";

	$stmt="SELECT * from osdial_user_groups order by user_group";
	$rslt=mysql_query($stmt, $link);
	$groups_to_print = mysql_num_rows($rslt);
	$o=0;
	$groups_list='';
	while ($groups_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$groups_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
	}
echo "$groups_list</select></td></tr>\n";

echo "<tr><td align=center colspan=2><input type=submit name=search value=SEARCH style=\"width: 100%;\"></td></tr>\n";
echo "</TABLE></center>\n";

}

######################
# ADD=660 user search results
######################

if ($ADD==660)
{
echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$SQL = '';
	if ($user) {$SQL .= " user LIKE \"%$user%\" and";}
	if ($full_name) {$SQL .= " full_name LIKE \"%$full_name%\" and";}
	if ($user_level > 0) {$SQL .= " user_level LIKE \"%$user_level%\" and";}
	if ($user_group) {$SQL .= " user_group = '$user_group' and";}
	$SQL = eregi_replace(" and$", "", $SQL);
	if (strlen($SQL)>5) {$SQL = "where $SQL";}

	$stmt="SELECT * from osdial_users $SQL order by full_name desc;";
#	echo "\n|$stmt|\n";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<br><font color=$default_text> SEARCH RESULTS:</font>\n";
echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}
		echo "<tr $bgcolor><td><font size=1>$row[1]</td><td><font size=1>$row[3]</td><td><font size=1>$row[4]</td><td><font size=1>$row[5]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=3&user=$row[1]\">MODIFY</a> | <a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=user_stats.php?user=$row[1]\">STATS</a> | <a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=user_status.php?user=$row[1]\">STATUS</a> | <a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=AST_agent_time_sheet.php?agent=$row[1]\">TIME</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";

}

######################
# ADD=8 find all callbacks on hold by an Agent
######################
if ($ADD==8)
{
	if ($LOGmodify_users==1)
	{
		if ($SUB==89)
		{
		$stmt="UPDATE osdial_callbacks SET status='INACTIVE' where user='$user' and status='LIVE' and callback_time < '$past_month_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>Agent ($user) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899)
		{
		$stmt="UPDATE osdial_callbacks SET status='INACTIVE' where user='$user' and status='LIVE' and callback_time < '$past_week_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>Agent ($user) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
$CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=8&SUB=89&user=$user\"><font color=$default_text>Remove LIVE Callbacks older than one month for this user</font></a><BR><a href=\"$PHP_SELF?ADD=8&SUB=899&user=$user\"><font color=$default_text>Remove LIVE Callbacks older than one week for this user</font></a><BR>";

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$CBquerySQLwhere = "and user='$user'";

echo "<br><font color=$default_text> AGENT CALLBACK HOLD LISTINGS: $user</font>\n";
$oldADD = "ADD=8&user=$user";
$ADD='82';
include($WeBServeRRooT . '/admin/include/content/lists/lists.php');
}


######################
# ADD=0 display all active users
######################
if ($ADD==0 or $ADD==9)
{
echo "<TABLE align=center><TR><TD>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

$let = get_variable('let');
$letSQL = '';
if ($let != '') $letSQL = "AND (full_name LIKE '$let%' OR full_name LIKE '% $let%')";

$level = get_variable('level');
$levelSQL = '';
if ($level != '') $levelSQL = "AND user_level='$level'";

$group = get_variable('group');
$groupSQL = '';
if ($group != '') $groupSQL = "AND user_group='$group'";

$mdn_user = get_variable('mdn_user');
if ($SUB==1 and $mdn_user != "") {
    $mdn_limit = get_variable('mdn_limit');
    if ($mdn_limit == "" or $mdn_limit < 0)
        $mdn_limit = -1;
    $stmt="UPDATE osdial_users SET manual_dial_new_limit='$mdn_limit' WHERE user='$mdn_user';";
    $rslt=mysql_query($stmt, $link);
}

$USERlink='stage=USERIDDOWN';
$NAMElink='stage=NAMEDOWN';
$LEVELlink='stage=LEVELDOWN';
$GROUPlink='stage=GROUPDOWN';
$SQLorder='order by full_name';
if (eregi("USERIDUP",$stage)) {$SQLorder='order by user asc';   $USERlink='stage=USERIDDOWN';}
if (eregi("USERIDDOWN",$stage)) {$SQLorder='order by user desc';   $USERlink='stage=USERIDUP';}
if (eregi("NAMEUP",$stage)) {$SQLorder='order by full_name asc';   $NAMElink='stage=NAMEDOWN';}
if (eregi("NAMEDOWN",$stage)) {$SQLorder='order by full_name desc';   $NAMElink='stage=NAMEUP';}
if (eregi("LEVELUP",$stage)) {$SQLorder='order by user_level asc';   $LEVELlink='stage=LEVELDOWN';}
if (eregi("LEVELDOWN",$stage)) {$SQLorder='order by user_level desc';   $LEVELlink='stage=LEVELUP';}
if (eregi("GROUPUP",$stage)) {$SQLorder='order by user_group asc';   $GROUPlink='stage=GROUPDOWN';}
if (eregi("GROUPDOWN",$stage)) {$SQLorder='order by user_group desc';   $GROUPlink='stage=GROUPUP';}
	$stmt="SELECT * from osdial_users WHERE 1=1 $letSQL $levelSQL $groupSQL $SQLorder";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font size=+1 color=$default_text>AGENTS</font><br><br>\n";
echo "<center><font size=-1 color=$default_text>&nbsp;|&nbsp;";
echo (($let == "A") ? "A" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=A\">A</a>") . "&nbsp;|&nbsp;";
echo (($let == "B") ? "B" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=B\">B</a>") . "&nbsp;|&nbsp;";
echo (($let == "C") ? "C" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=C\">C</a>") . "&nbsp;|&nbsp;";
echo (($let == "D") ? "D" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=D\">D</a>") . "&nbsp;|&nbsp;";
echo (($let == "E") ? "E" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=E\">E</a>") . "&nbsp;|&nbsp;";
echo (($let == "F") ? "F" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=F\">F</a>") . "&nbsp;|&nbsp;";
echo (($let == "G") ? "G" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=G\">G</a>") . "&nbsp;|&nbsp;";
echo (($let == "H") ? "H" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=H\">H</a>") . "&nbsp;|&nbsp;";
echo (($let == "I") ? "I" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=I\">I</a>") . "&nbsp;|&nbsp;";
echo (($let == "J") ? "J" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=J\">J</a>") . "&nbsp;|&nbsp;";
echo (($let == "K") ? "K" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=K\">K</a>") . "&nbsp;|&nbsp;";
echo (($let == "L") ? "L" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=L\">L</a>") . "&nbsp;|&nbsp;";
echo (($let == "M") ? "M" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=M\">M</a>") . "&nbsp;|&nbsp;";
echo (($let == "N") ? "N" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=N\">N</a>") . "&nbsp;|&nbsp;";
echo (($let == "O") ? "O" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=O\">O</a>") . "&nbsp;|&nbsp;";
echo (($let == "P") ? "P" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=P\">P</a>") . "&nbsp;|&nbsp;";
echo (($let == "Q") ? "Q" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=Q\">Q</a>") . "&nbsp;|&nbsp;";
echo (($let == "R") ? "R" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=R\">R</a>") . "&nbsp;|&nbsp;";
echo (($let == "S") ? "S" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=S\">S</a>") . "&nbsp;|&nbsp;";
echo (($let == "T") ? "T" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=T\">T</a>") . "&nbsp;|&nbsp;";
echo (($let == "U") ? "U" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=U\">U</a>") . "&nbsp;|&nbsp;";
echo (($let == "V") ? "V" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=V\">V</a>") . "&nbsp;|&nbsp;";
echo (($let == "W") ? "W" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=W\">W</a>") . "&nbsp;|&nbsp;";
echo (($let == "X") ? "X" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=X\">X</a>") . "&nbsp;|&nbsp;";
echo (($let == "Y") ? "Y" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=Y\">Y</a>") . "&nbsp;|&nbsp;";
echo (($let == "Z") ? "Z" : "<a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$group&let=Z\">Z</a>") . "&nbsp;|&nbsp;";
echo "</font><br>\n";

echo "<TABLE width=$section_width cellspacing=0 cellpadding=1 align=center>\n";
echo "<tr bgcolor=$menubarcolor>";
if ($ADD==9) {
    echo "  <td><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&$USERlink\"><font size=1 color=white><B>USER ID</B></a></td>";
    echo "  <td><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&$NAMElink\"><font size=1 color=white><B>FULL NAME</B></a></td>";
    echo "  <td><font size=1 color=white><B>NEW ATTEMPTS</B></td>";
    echo "  <td><font size=1 color=white><B>NEW ATTEMPT LIMIT</B></td>";
    echo "  <td><font size=1 color=white><B>CONTACTS</B></td>";
    echo "  <td><font size=1 color=white><B>SALES</B></td>";
    echo "  <td><font size=1 color=white><B>CLOSING%</B></td>";
    echo "  <td align=center><font size=1 color=white><B>LINKS</B></td>";
} else {
    echo "  <td><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&$USERlink\"><font size=1 color=white><B>USER ID</B></a></td>";
    echo "  <td><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&$NAMElink\"><font size=1 color=white><B>FULL NAME</B></a></td>";
    echo "  <td><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&$LEVELlink\"><font size=1 color=white><B>LEVEL</B></a></td>";
    echo "  <td><a href=\"$PHP_SELF?ADD=$ADD&let=$let&level=$level&group=$group&$GROUPlink\"><font size=1 color=white><B>GROUP</B></a></td>";
    echo "  <td align=center><font size=1 color=white><B>LINKS</B></td>";
}
echo "</tr>";

    $new_count = 0;
    $sales_count = 0;
    $contact_count = 0;

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}
        echo "<form action=$PHP_SELF method=POST>";
        echo "<input type=hidden name=ADD value=$ADD>";
        echo "<input type=hidden name=SUB value=1>";
		echo "<tr class=row $bgcolor>";
        echo "  <td><a href=\"$PHP_SELF?ADD=3&user=$row[1]\"><font size=1 color=$default_text>$row[1]</a></td>";
        echo "  <td><font size=1>$row[3]</td>";
        if ($ADD==9) {
	        $stmt2="SELECT SUM(manual_dial_new_today),status_category_1,SUM(status_category_count_1),status_category_2,SUM(status_category_count_2),status_category_3,SUM(status_category_count_3),status_category_4,SUM(status_category_count_4) FROM osdial_campaign_agent_stats WHERE user='$row[1]' GROUP BY user";
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
            if ($row[46] < 0)
                $row[46] = "";
            if ($stat['CONTACT'] > 0) $close_pct = (($stat['SALE'] / ($stat['CONTACT'] + $stat['SALE'])) * 100);
            echo "  <td align=center><font size=1>$row2[0]</td>";
            echo "  <td><font size=1><input type=hidden name=mdn_user value=$row[1]><input style=\"font-size: 7px;\" type=text name=mdn_limit size=5 value=$row[46]></td>";
            echo "  <td align=center><font size=1>" . ($stat['CONTACT'] + $stat['SALE']) . "</td>";
            echo "  <td align=center><font size=1>" . $stat['SALE'] . "</td>";
            echo "  <td align=center><font size=1>" . sprintf('%5.2f',$close_pct) . " %</td>";
        } else {
            echo "  <td><font size=1><a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$row[4]&group=$group&let=$let\">$row[4]</a></td>";
            echo "  <td><font size=1><a href=\"$PHP_SELF?ADD=$ADD&stage=$stage&level=$level&group=$row[5]&let=$let\">$row[5]</a></td>";
        }
		echo "  <td align=center><font size=1><a href=\"$PHP_SELF?ADD=3&user=$row[1]\">MODIFY</a> | <a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=user_stats.php?user=$row[1]\">STATS</a> | <a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=user_status.php?user=$row[1]\">STATUS</a> | <a href=\"$PHP_SELF?ADD=999999&SUB=1&iframe=AST_agent_time_sheet.php?agent=$row[1]\">TIME</a></td>";
        echo "</tr>";
        echo "</form>";
		$o++;
	}
    if ($ADD==9) {
        $close_pct = 0;
        if ($contact_count > 0) $close_pct = (($sales_count / ($contact_count + $sales_count)) * 100);
        echo "<tr bgcolor=$menubarcolor>";
        echo "  <td><font size=1 color=white>&nbsp;</font></td>";
        echo "  <td><font size=1 color=white>&nbsp;</font></td>";
        echo "  <td align=center><font size=1 color=white>" . $new_count . "</font></td>";
        echo "  <td><font size=1 color=white>&nbsp;</td>";
        echo "  <td align=center><font size=1 color=white>" . ($contact_count + $sales_count) . "</font></td>";
        echo "  <td align=center><font size=1 color=white>" . $sales_count . "</font></td>";
        #echo "Total Contact: " . $contact_count . "<br />";
        echo "  <td align=center><font size=1 color=white>" . sprintf('%5.2f',$close_pct) . " %</font></td>";
        echo "  <td><font size=1 color=white>&nbsp;</font></td>";
        echo "</tr>";
    } else {
        echo "<tr bgcolor=$menubarcolor>";
        echo "  <td colspan=5 height=8px><font size=1 color=white></font></td>";
        echo "</tr>";
    }

echo "</TABLE></center>\n";


}

?>
