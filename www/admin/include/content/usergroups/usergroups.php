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

if ($ADD==111111)
{
	if ($LOGmodify_usergroups==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<center><br><font color=$default_text size=+1>ADD NEW AGENTS GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=211111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Group: </td><td align=left><input type=text name=user_group size=15 maxlength=20> (no spaces or punctuation)$NWB#osdial_user_groups-user_group$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=group_name size=40 maxlength=40> (description of group)$NWB#osdial_user_groups-group_name$NWE</td></tr>\n";
	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=211111 adds new user group to the system
######################

if ($ADD==211111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	$stmt="SELECT count(*) from osdial_user_groups where user_group='$user_group';";
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

if ($ADD==411111)
{
	if ($LOGmodify_usergroups==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($user_group) < 2) or (strlen($group_name) < 2) )
		{
		 echo "<br><font color=red>USER GROUP NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Group name and description must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="UPDATE osdial_user_groups set user_group='$user_group', group_name='$group_name',allowed_campaigns='$campaigns_value' where user_group='$OLDuser_group';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=$default_text>USER GROUP MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY USER GROUP ENTRY     |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111;	# go to user group modification form below
}

######################
# ADD=511111 confirmation before deletion of user group record
######################

if ($ADD==511111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($user_group) < 2) or ($LOGdelete_user_groups < 1) )
		{
		 echo "<br><font color=red>USER GROUP NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>User_group be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>USER GROUP DELETION CONFIRMATION: $user_group</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=611111&user_group=$user_group&CoNfIrM=YES\">Click here to delete user group $user_group</a></font><br><br><br>\n";
		}

$ADD='311111';		# go to user group modification below
}

######################
# ADD=611111 delete user group record
######################

if ($ADD==611111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($user_group) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_user_groups < 1) )
		{
		 echo "<br><font color=red>USER GROUP NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>User_group be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from osdial_user_groups where user_group='$user_group' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
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
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<center><br><font color=$default_text size=+1>MODIFY A USER GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411111>\n";
	echo "<input type=hidden name=OLDuser_group value=\"$user_group\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Group: </td><td align=left><input type=text name=user_group size=15 maxlength=20 value=\"$user_group\"> (no spaces or punctuation)$NWB#osdial_user_groups-user_group$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=group_name size=40 maxlength=40 value=\"$group_name\"> (description of group)$NWB#osdial_user_groups-group_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Allowed Campaigns: </td><td align=left>\n";
	echo "$campaigns_list";
	echo "$NWB#osdial_user_groups-allowed_campaigns$NWE</td></tr>\n";
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

		echo "  <tr $bgcolor class=\"row font1\">\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=3&user=$rowx[0]\">$rowx[0]</a></td>\n";
		echo "    <td>$rowx[1]</td>\n";
		echo "    <td>$rowx[2]</td>\n";
		echo "  </tr>\n";
		}

    echo "  <tr class=tabfooter>\n";
    echo "    <td colspan=3></td>\n";
    echo "  </tr>\n";
	echo "</table></center><br>\n";



	echo "<br><br><a href=\"$PHP_SELF?ADD=8111&user_group=$user_group\">Click here to see all CallBack Holds in this user group</a><BR><BR>\n";

	if ($LOGdelete_user_groups > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=511111&user_group=$user_group\">DELETE THIS USER GROUP</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
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

	$stmt="SELECT * from osdial_user_groups order by user_group";
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
		echo "  <tr $bgcolor class=\"row font1\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=311111&user_group=$row[0]\">$row[0]</a></td>\n";
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
