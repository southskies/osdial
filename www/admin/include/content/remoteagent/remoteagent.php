<?php


######################
# ADD=11111 display the ADD NEW REMOTE AGENTS SCREEN
######################

if ($ADD==11111)
{
	if ($LOGmodify_remoteagents==1)
	{
    $servers_list = get_servers($link, $server_ip);
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD NEW OFF-HOOK AGENTS</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Agent ID Start: </td><td align=left><input type=text name=user_start size=6 maxlength=6> (numbers only, incremented)$NWB#osdial_remote_agents-user_start$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Number of Lines: </td><td align=left><input type=text name=number_of_lines size=3 maxlength=3> (numbers only)$NWB#osdial_remote_agents-number_of_lines$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";
	echo "$servers_list";
	echo "</select>$NWB#osdial_remote_agents-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>External Extension: </td><td align=left><input type=text name=conf_exten size=20 maxlength=20> (dial plan number dialed to reach agents)$NWB#osdial_remote_agents-conf_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Status: </td><td align=left><select size=1 name=status><option>ACTIVE</option><option SELECTED>INACTIVE</option></select>$NWB#osdial_remote_agents-status$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
	echo "$campaigns_list";
	echo "</select>$NWB#osdial_remote_agents-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Inbound Groups: </td><td align=left>\n";
	echo "$groups_list";
	echo "$NWB#osdial_remote_agents-closer_campaigns$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	echo "NOTE: It can take up to 30 seconds for changes submitted on this screen to go live\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=21111 adds new remote agents to the system
######################

if ($ADD==21111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from osdial_remote_agents where server_ip='$server_ip' and user_start='$user_start';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>REMOTE AGENTS NOT ADDED - there is already a remote agents entry starting with this userID</font>\n";}
	else
		{
		 if ( (strlen($server_ip) < 2) or (strlen($user_start) < 2)  or (strlen($campaign_id) < 2) or (strlen($conf_exten) < 2) )
			{
			 echo "<br><font color=red>REMOTE AGENTS NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Agents ID start and external extension must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt="INSERT INTO osdial_remote_agents values('','$user_start','$number_of_lines','$server_ip','$conf_exten','$status','$campaign_id','$groups_value');";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>REMOTE AGENTS ADDED: $user_start</font></B>\n";

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
	if ($LOGmodify_remoteagents==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($server_ip) < 2) or (strlen($user_start) < 2)  or (strlen($campaign_id) < 2) or (strlen($conf_exten) < 2) )
		{
		 echo "<br><font color=red>REMOTE AGENTS NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Agent ID Start and External Extension must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="UPDATE osdial_remote_agents set user_start='$user_start', number_of_lines='$number_of_lines', server_ip='$server_ip', conf_exten='$conf_exten', status='$status', campaign_id='$campaign_id', closer_campaigns='$groups_value' where remote_agent_id='$remote_agent_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=navy>REMOTE AGENTS MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY REMOTE AGENTS ENTRY     |$PHP_AUTH_USER|$ip|set user_start='$user_start', number_of_lines='$number_of_lines', server_ip='$server_ip', conf_exten='$conf_exten', status='$status', campaign_id='$campaign_id', closer_campaigns='$groups_value' where remote_agent_id='$remote_agent_id'|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=31111;	# go to remote agents modification form below
}


######################
# ADD=51111 confirmation before deletion of remote agent record
######################

if ($ADD==51111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($remote_agent_id) < 1) or ($LOGdelete_remote_agents < 1) )
		{
		 echo "<br><font color=red>REMOTE AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Remote_agent_id be at least 2 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>REMOTE AGENT DELETION CONFIRMATION: $remote_agent_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111&remote_agent_id=$remote_agent_id&CoNfIrM=YES\">Click here to delete remote agent $remote_agent_id</a></font><br><br><br>\n";
		}

$ADD='31111';		# go to remote agent modification below
}


######################
# ADD=61111 delete remote agent record
######################

if ($ADD==61111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($remote_agent_id) < 1) or ($CoNfIrM != 'YES') or ($LOGdelete_remote_agents < 1) )
		{
		 echo "<br><font color=red>REMOTE AGENT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Remote_agent_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from osdial_remote_agents where remote_agent_id='$remote_agent_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING RMTAGENT!!|$PHP_AUTH_USER|$ip|remote_agent_id='$remote_agent_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>REMOTE AGENT DELETION COMPLETED: $remote_agent_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='10000';		# go to remote agents list
}


######################
# ADD=31111 modify remote agents info in the system
######################

if ($ADD==31111)
{
	if ($LOGmodify_remoteagents==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from osdial_remote_agents where remote_agent_id='$remote_agent_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$remote_agent_id =	$row[0];
	$user_start =		$row[1];
	$number_of_lines =	$row[2];
	$server_ip =		$row[3];
	$conf_exten =		$row[4];
	$status =			$row[5];
	$campaign_id =		$row[6];

    $servers_list = get_servers($link, $row[3]);

	echo "<center><br><font color=navy size=+1>MODIFY A REMOTE AGENT</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111>\n";
	echo "<input type=hidden name=remote_agent_id value=\"$row[0]\">\n";
	
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Agent ID Start: </td><td align=left><input type=text name=user_start size=6 maxlength=6 value=\"$user_start\"> (numbers only, incremented)$NWB#osdial_remote_agents-user_start$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Number of Lines: </td><td align=left><input type=text name=number_of_lines size=3 maxlength=3 value=\"$number_of_lines\"> (numbers only)$NWB#osdial_remote_agents-number_of_lines$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";
	echo "$servers_list";
	#echo "<option SELECTED>$row[3]</option>\n";
	echo "</select>$NWB#osdial_remote_agents-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>External Extension: </td><td align=left><input type=text name=conf_exten size=20 maxlength=20 value=\"$conf_exten\"> (dial plan number dialed to reach agents)$NWB#osdial_remote_agents-conf_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Status: </td><td align=left><select size=1 name=status><option SELECTED>ACTIVE</option><option>INACTIVE</option><option SELECTED>$status</option></select>$NWB#osdial_remote_agents-status$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
	echo "$campaigns_list";
	echo "<option SELECTED>$campaign_id</option>\n";
	echo "</select>$NWB#osdial_remote_agents-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Inbound Groups: </td><td align=left>\n";
	echo "$groups_list";
	echo "$NWB#osdial_remote_agents-closer_campaigns$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	
	echo "NOTE: It can take up to 30 seconds for changes submitted on this screen to go live\n";


	if ($LOGdelete_remote_agents > 0)
		{
		echo "<br><br><br><a href=\"$PHP_SELF?ADD=51111&remote_agent_id=$remote_agent_id\">DELETE THIS REMOTE AGENT</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=10000 display all remote agents
######################
if ($ADD==10000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from osdial_remote_agents order by server_ip,campaign_id,user_start";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>OFF-HOOK AGENTS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td align=center><font size=1 color=white><B>LINES</B></td>";
echo "<td align=center><font size=1 color=white><B>SERVER</B></td>";
echo "<td><font size=1 color=white><B>EXTENSION</B></td>";
echo "<td align=center><font size=1 color=white><B>ACTIVE</B></td>";
echo "<td><font size=1 color=white><B>CAMPAIGN</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111&remote_agent_id=$row[0]\">$row[1]</a></td>";
		echo "<td align=center><font size=1> $row[2]</td>";
		echo "<td align=center><font size=1> $row[3]</td>";
		echo "<td><font size=1> $row[4]</td>";
		echo "<td align=center><font size=1> $row[5]</td>";
		echo "<td><font size=1> $row[6]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31111&remote_agent_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}


?>
