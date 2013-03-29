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



######################
# ADD=11111 display the ADD NEW REMOTE AGENTS SCREEN
######################

if ($ADD==11111) {
	if ($LOG['modify_remoteagents']==1)	{
    $servers_list = get_servers($link, $server_ip, 'AIO|DIALER');
	echo "<center><br><font class=top_header color=$default_text size=+1>ADD NEW EXTERNAL AGENTS</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right width=35%>Agent ID Start: </td><td align=left><input type=text name=user_start size=6 maxlength=20> (numbers only, incremented)".helptag("osdial_remote_agents-user_start")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Number of Lines: </td><td align=left><input type=text name=number_of_lines size=3 maxlength=3> (numbers only)".helptag("osdial_remote_agents-number_of_lines")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";
	echo "$servers_list";
	echo "</select>".helptag("osdial_remote_agents-server_ip")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>External Extension: </td><td align=left><input type=text name=conf_exten size=20 maxlength=20> (dial plan number dialed to reach agents)".helptag("osdial_remote_agents-conf_exten")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Status: </td><td align=left><select size=1 name=status><option>ACTIVE</option><option SELECTED>INACTIVE</option></select>".helptag("osdial_remote_agents-status")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
        $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE campaign_id IN %s ORDER BY campaign_id;",$LOG['allowed_campaignsSQL']);
        $rslt=mysql_query($stmt, $link);
        $campaigns_to_print = mysql_num_rows($rslt);
        $campaigns_list='';

        $o=0;
        while ($campaigns_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $campaigns_list .= "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
            $o++;
        }
	echo "$campaigns_list";
	echo "</select>".helptag("osdial_remote_agents-campaign_id")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Inbound Groups: </td><td align=left>\n";
	echo "$groups_list";
	echo "".helptag("osdial_remote_agents-closer_campaigns")."</td></tr>\n";
	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "NOTE: It can take up to 30 seconds for changes submitted on this screen to go live\n";
	} else {
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=21111 adds new remote agents to the system
######################

if ($ADD==21111)
{
	$stmt=sprintf("SELECT count(*) FROM osdial_remote_agents WHERE server_ip='%s' AND user_start='%s';",mres($server_ip),mres($user_start));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>REMOTE AGENTS NOT ADDED - there is already a remote agents entry starting with this userID</font>\n";}
	else
		{
		 if ( (OSDstrlen($server_ip) < 2) or (OSDstrlen($user_start) < 2)  or (OSDstrlen($campaign_id) < 2) or (OSDstrlen($conf_exten) < 2) )
			{
			 echo "<br><font color=red>REMOTE AGENTS NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Agents ID start and external extension must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt=sprintf("INSERT INTO osdial_remote_agents values('','%s','%s','%s','%s','%s','%s','%s');",mres($user_start),mres($number_of_lines),mres($server_ip),mres($conf_exten),mres($status),mres($campaign_id),mres($groups_value));
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=$default_text>REMOTE AGENTS ADDED: $user_start</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW REMOTE AGENTS ENTRY     |$PHP_AUTH_USER|$ip|'$user_start','$number_of_lines','$server_ip','$conf_exten','$status','$campaign_id','$groups_value'|\n");
				fclose($fp);
				}
			}
		}
$ADD=10000;
}


######################
# ADD=41111 modify remote agents info in the system
######################

if ($ADD==41111)
{
	if ($LOG['modify_remoteagents']==1)
	{
	 if ( (OSDstrlen($server_ip) < 2) or (OSDstrlen($user_start) < 2)  or (OSDstrlen($campaign_id) < 2) or (OSDstrlen($conf_exten) < 2) )
		{
		 echo "<br><font color=red>REMOTE AGENTS NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Agent ID Start and External Extension must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt=sprintf("UPDATE osdial_remote_agents SET user_start='%s',number_of_lines='%s',server_ip='%s',conf_exten='%s',status='%s',campaign_id='%s',closer_campaigns='%s' WHERE remote_agent_id='%s';",mres($user_start),mres($number_of_lines),mres($server_ip),mres($conf_exten),mres($status),mres($campaign_id),mres($groups_value),mres($remote_agent_id));
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=$default_text>REMOTE AGENTS MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY REMOTE AGENTS ENTRY     |$PHP_AUTH_USER|$ip|set user_start='$user_start', number_of_lines='$number_of_lines', server_ip='$server_ip', conf_exten='$conf_exten', status='$status', campaign_id='$campaign_id', closer_campaigns='$groups_value' where remote_agent_id='$remote_agent_id'|\n");
			fclose($fp);
			}
		}
    $ADD=31111;	# go to remote agents modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=51111 confirmation before deletion of remote agent record
######################

if ($ADD==51111)
{
	 if ( (OSDstrlen($remote_agent_id) < 1) or ($LOG['delete_remote_agents'] < 1) )
		{
		 echo "<br><font color=red>REMOTE AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>agent_id be at least 2 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>REMOTE AGENT DELETION CONFIRMATION: $emote_agent_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111&remote_agent_id=$remote_agent_id&CoNfIrM=YES\">Click here to delete remote agent $remote_agent_id</a></font><br><br><br>\n";
		}

$ADD='31111';		# go to remote agent modification below
}


######################
# ADD=61111 delete remote agent record
######################

if ($ADD==61111)
{
	 if ( (OSDstrlen($remote_agent_id) < 1) or ($CoNfIrM != 'YES') or ($LOG['delete_remote_agents'] < 1) )
		{
		 echo "<br><font color=red>REMOTE AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>agent_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt=sprintf("DELETE FROM osdial_remote_agents WHERE remote_agent_id='%s' LIMIT 1;",mres($remote_agent_id));
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING RMTAGENT!!|$PHP_AUTH_USER|$ip|remote_agent_id='$remote_agent_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=$default_text>REMOTE AGENT DELETION COMPLETED: $remote_agent_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='10000';		# go to remote agents list
}


######################
# ADD=31111 modify remote agents info in the system
######################

if ($ADD==31111)
{
	if ($LOG['modify_remoteagents']==1)	{
	$stmt=sprintf("SELECT * FROM osdial_remote_agents WHERE remote_agent_id='%s';",mres($remote_agent_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$remote_agent_id =	$row[0];
	$user_start =		$row[1];
	$number_of_lines =	$row[2];
	$server_ip =		$row[3];
	$conf_exten =		$row[4];
	$status =			$row[5];
	$campaign_id =		$row[6];

    $servers_list = get_servers($link, $row[3], 'AIO|DIALER');

	echo "<center><br><font class=top_header color=$default_text size=+1>MODIFY AN EXTERNAL AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111>\n";
	echo "<input type=hidden name=remote_agent_id value=\"$row[0]\">\n";
	
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right width=35%>Agent ID Start: </td><td align=left><input type=text name=user_start size=7 maxlength=6 value=\"$user_start\"> (numbers only, incremented)".helptag("osdial_remote_agents-user_start")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Number of Lines: </td><td align=left><input type=text name=number_of_lines size=3 maxlength=3 value=\"$number_of_lines\"> (numbers only)".helptag("osdial_remote_agents-number_of_lines")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";
	echo "$servers_list";
	#echo "<option SELECTED>$row[3]</option>\n";
	echo "</select>".helptag("osdial_remote_agents-server_ip")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>External Extension: </td><td align=left><input type=text name=conf_exten size=20 maxlength=20 value=\"$conf_exten\"> (dial plan number dialed to reach agents)".helptag("osdial_remote_agents-conf_exten")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Status: </td><td align=left><select size=1 name=status><option SELECTED>ACTIVE</option><option>INACTIVE</option><option SELECTED>$status</option></select>".helptag("osdial_remote_agents-status")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
        $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE campaign_id IN %s ORDER BY campaign_id;",$LOG['allowed_campaignsSQL']);
        $rslt=mysql_query($stmt, $link);
        $campaigns_to_print = mysql_num_rows($rslt);
        $campaigns_list='';

        $o=0;
        while ($campaigns_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $campaigns_list .= "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
            $o++;
        }
	echo "$campaigns_list";
	echo "<option SELECTED>$campaign_id</option>\n";
	echo "</select>".helptag("osdial_remote_agents-campaign_id")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Inbound Groups: </td><td align=left>\n";
	echo "$groups_list";
	echo "".helptag("osdial_remote_agents-closer_campaigns")."</td></tr>\n";
	echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	
	echo "NOTE: It can take up to 30 seconds for changes submitted on this screen to go live\n";


		if ($LOG['delete_remote_agents'] > 0) {
		echo "<br><br><br><a href=\"$PHP_SELF?ADD=51111&remote_agent_id=$remote_agent_id\">DELETE THIS REMOTE AGENT</a>\n";
		}
	} else {
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=10000 display all remote agents
######################
if ($ADD==10000) {
	$stmt=sprintf("SELECT * FROM osdial_remote_agents WHERE campaign_id IN %s ORDER BY server_ip,campaign_id,user_start;",$LOG['allowed_campaignsSQL']);
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font class=top_header color=$default_text size=+1>EXTERNAL AGENTS</font><br><br>\n";
	echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>ID</td>\n";
echo "    <td align=center>LINES</td>\n";
echo "    <td align=center>SERVER</td>\n";
echo "    <td>EXTENSION</td>\n";
echo "    <td align=center>ACTIVE</td>\n";
echo "    <td>CAMPAIGN</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31111&remote_agent_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31111&remote_agent_id=$row[0]\">$row[1]</a></td>\n";
		echo "    <td align=center>$row[2]</td>\n";
		echo "    <td align=center>$row[3]</td>\n";
		echo "    <td>$row[4]</td>\n";
		echo "    <td align=center>$row[5]</td>\n";
		echo "    <td>$row[6]</td>\n";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=31111&remote_agent_id=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=7></td>\n";
echo "  </tr>\n";
echo "</TABLE></center>\n";
}


?>
