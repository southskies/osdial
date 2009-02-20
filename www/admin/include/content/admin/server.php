<?php


######################
# ADD=111111111111 display the ADD NEW SERVER SCREEN
######################

if ($ADD==111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD A NEW SERVER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=211111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server ID: </td><td align=left><input type=text name=server_id size=10 maxlength=10>$NWB#servers-server_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server Description: </td><td align=left><input type=text name=server_description size=30 maxlength=255>$NWB#servers-server_description$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP Address: </td><td align=left><input type=text name=server_ip size=20 maxlength=15>$NWB#servers-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option></select>$NWB#servers-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Asterisk Version: </td><td align=left><input type=text name=asterisk_version size=20 maxlength=20>$NWB#servers-asterisk_version$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}



######################
# ADD=211111111111 adds new server to the system
######################

if ($ADD==211111111111)
{
echo "<TABLE><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from servers where server_id='$server_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>SERVER NOT ADDED - there is already a server in the system with this ID</font>\n";}
	else
		{
		 if ( (strlen($server_id) < 1) or (strlen($server_ip) < 7) )
			{echo "<br><font color=red>SERVER NOT ADDED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=navy>SERVER ADDED</font>\n";

			$stmt="INSERT INTO servers (server_id,server_description,server_ip,active,asterisk_version) values('$server_id','$server_description','$server_ip','$active','$asterisk_version');";
			$rslt=mysql_query($stmt, $link);
			}
		}
$ADD=311111111111;
}



######################
# ADD=221111111111 adds the new vicidial server trunk record to the system
######################

if ($ADD==221111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT max_vicidial_trunks from servers where server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	$MAXvicidial_trunks = $rowx[0];
	
	$stmt="SELECT sum(dedicated_trunks) from vicidial_server_trunks where server_ip='$server_ip' and campaign_id !='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	$SUMvicidial_trunks = ($rowx[0] + $dedicated_trunks);
	
	if ($SUMvicidial_trunks > $MAXvicidial_trunks)
		{
		echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT ADDED - the number of vicidial trunks is too high: $SUMvicidial_trunks / $MAXvicidial_trunks</font>\n";
		}
	else
		{
		$stmt="SELECT count(*) from vicidial_server_trunks where campaign_id='$campaign_id' and server_ip='$server_ip';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT ADDED - there is already a server-trunk record for this campaign</font>\n";}
		else
			{
			 if ( (strlen($campaign_id) < 2) or (strlen($server_ip) < 7) or (strlen($dedicated_trunks) < 1) or (strlen($trunk_restriction) < 1) )
				{
				 echo "<br>OSDial SERVER TRUNK RECORD NOT ADDED - Please go back and look at the data you entered\n";
				 echo "<br>campaign must be between 3 and 8 characters in length\n";
				 echo "<br>server_ip delay must be at least 7 characters\n";
				 echo "<br>trunks must be a digit from 0 to 9999<br>\n";
				}
			 else
				{
				echo "<br><B><font color=navy>OSDial SERVER TRUNK RECORD ADDED: $campaign_id - $server_ip - $dedicated_trunks - $trunk_restriction</font></B>\n";

				$stmt="INSERT INTO vicidial_server_trunks(server_ip,campaign_id,dedicated_trunks,trunk_restriction) values('$server_ip','$campaign_id','$dedicated_trunks','$trunk_restriction');";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW OSDial TRUNK  |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}
				}
			}
		}
$ADD=311111111111;
}



######################
# ADD=411111111111 modify server record in the system
######################

if ($ADD==411111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT count(*) from servers where server_id='$server_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ( ($row[0] > 0) && ($server_id != $old_server_id) )
		{echo "<br><font color=red>SERVER NOT MODIFIED - there is already a server in the system with this server_id</font>\n";}
	else
		{
		$stmt="SELECT count(*) from servers where server_ip='$server_ip';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ( ($row[0] > 0) && ($server_ip != $old_server_ip) )
			{echo "<br><font color=red>SERVER NOT MODIFIED - there is already a server in the system with this server_ip</font>\n";}
		else
			{
			 if ( (strlen($server_id) < 1) or (strlen($server_ip) < 7) )
				{echo "<br><font color=red>SERVER NOT MODIFIED - Please go back and look at the data you entered</font>\n";}
			 else
				{
				echo "<br><font color=navy>SERVER MODIFIED: $server_ip</font>\n";

				$stmt="UPDATE servers set server_id='$server_id',server_description='$server_description',server_ip='$server_ip',active='$active',asterisk_version='$asterisk_version', max_vicidial_trunks='$max_vicidial_trunks', telnet_host='$telnet_host', telnet_port='$telnet_port', ASTmgrUSERNAME='$ASTmgrUSERNAME', ASTmgrSECRET='$ASTmgrSECRET', ASTmgrUSERNAMEupdate='$ASTmgrUSERNAMEupdate', ASTmgrUSERNAMElisten='$ASTmgrUSERNAMElisten', ASTmgrUSERNAMEsend='$ASTmgrUSERNAMEsend', local_gmt='$local_gmt', voicemail_dump_exten='$voicemail_dump_exten', answer_transfer_agent='$answer_transfer_agent', ext_context='$ext_context', sys_perf_log='$sys_perf_log', vd_server_logs='$vd_server_logs', agi_output='$agi_output', vicidial_balance_active='$vicidial_balance_active', balance_trunks_offlimits='$balance_trunks_offlimits' where server_id='$old_server_id';";
				$rslt=mysql_query($stmt, $link);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111111;	# go to server modification form below
}


######################
# ADD=421111111111 modify vicidial server trunks record in the system
######################

if ($ADD==421111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT max_vicidial_trunks from servers where server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	$MAXvicidial_trunks = $rowx[0];
	
	$stmt="SELECT sum(dedicated_trunks) from vicidial_server_trunks where server_ip='$server_ip' and campaign_id !='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$rowx=mysql_fetch_row($rslt);
	$SUMvicidial_trunks = ($rowx[0] + $dedicated_trunks);
	
	if ($SUMvicidial_trunks > $MAXvicidial_trunks)
		{
		echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT ADDED - the number of OSDial trunks is too high: $SUMvicidial_trunks / $MAXvicidial_trunks</font>\n";
		}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($server_ip) < 7) or (strlen($dedicated_trunks) < 1) or (strlen($trunk_restriction) < 1) )
			{
			 echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>campaign must be between 3 and 8 characters in length\n";
			 echo "<br>server_ip delay must be at least 7 characters\n";
			 echo "<br>trunks must be a digit from 0 to 9999</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy>OSDial SERVER TRUNK RECORD MODIFIED: $campaign_id - $server_ip - $dedicated_trunks - $trunk_restriction</font></B>\n";

			$stmt="UPDATE vicidial_server_trunks SET dedicated_trunks='$dedicated_trunks',trunk_restriction='$trunk_restriction' where campaign_id='$campaign_id' and server_ip='$server_ip';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY SERVER TRUNK   |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111111;	# go to server modification form below
}




######################
# ADD=511111111111 confirmation before deletion of server record
######################

if ($ADD==511111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($server_id) < 2) or (strlen($server_ip) < 7) or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>SERVER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Server ID be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>SERVER DELETION CONFIRMATION: $server_id - $server_ip</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=611111111111&server_id=$server_id&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete phone $server_id - $server_ip</a></font><br><br><br>\n";
		}
$ADD='311111111111';		# go to server modification below
}




######################
# ADD=611111111111 delete server record
######################

if ($ADD==611111111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($server_id) < 2) or (strlen($server_ip) < 7) or ($CoNfIrM != 'YES') or ($LOGast_delete_phones < 1) )
		{
		 echo "<br><font color=red>SERVER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Server ID be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from servers where server_id='$server_id' and server_ip='$server_ip' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING SERVER!!|$PHP_AUTH_USER|$ip|server_id='$server_id'|server_ip='$server_ip'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>SERVER DELETION COMPLETED: $server_id - $server_ip</font></B>\n";
		echo "<br><br>\n";
		}
$ADD='100000000000';		# go to server list
}


######################
# ADD=621111111111 delete vicidial server trunk record in the system
######################

if ($ADD==621111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($server_ip) < 7) )
		{
		 echo "<br><font color=red>OSDial SERVER TRUNK RECORD NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>campaign must be between 3 and 8 characters in length\n";
		 echo "<br>server_ip delay must be at least 7 characters</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>OSDial SERVER TRUNK RECORD DELETED: $campaign_id - $server_ip</font></B>\n";

		$stmt="DELETE FROM vicidial_server_trunks where campaign_id='$campaign_id' and server_ip='$server_ip';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|DELETE SERVER TRUNK   |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311111111111;	# go to server modification form below
}



######################
# ADD=311111111111 modify server record in the system
######################

if ($ADD==311111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from servers where server_id='$server_id' or server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$server_id = $row[0];
	$server_ip = $row[2];

	echo "<center><br><font color=navy size=+1>MODIFY A SERVER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411111111111>\n";
	echo "<input type=hidden name=old_server_id value=\"$server_id\">\n";
	echo "<input type=hidden name=old_server_ip value=\"$row[2]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server ID: </td><td align=left><input type=text name=server_id size=10 maxlength=10 value=\"$row[0]\">$NWB#servers-server_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server Description: </td><td align=left><input type=text name=server_description size=30 maxlength=255 value=\"$row[1]\">$NWB#servers-server_description$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server IP Address: </td><td align=left><input type=text name=server_ip size=20 maxlength=15 value=\"$row[2]\">$NWB#servers-server_ip$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option selected>$row[3]</option></select>$NWB#servers-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Asterisk Version: </td><td align=left><input type=text name=asterisk_version size=20 maxlength=20 value=\"$row[4]\">$NWB#servers-asterisk_version$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Max OSDial Trunks: </td><td align=left><input type=text name=max_vicidial_trunks size=5 maxlength=4 value=\"$row[5]\">$NWB#servers-max_osdial_trunks$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Balance Dialing: </td><td align=left><select size=1 name=vicidial_balance_active><option>Y</option><option>N</option><option selected>$row[20]</option></select>$NWB#servers-osdial_balance_active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial Balance Offlimits: </td><td align=left><input type=text name=balance_trunks_offlimits size=5 maxlength=4 value=\"$row[21]\">$NWB#servers-balance_trunks_offlimits$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Telnet Host: </td><td align=left><input type=text name=telnet_host size=20 maxlength=20 value=\"$row[6]\">$NWB#servers-telnet_host$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Telnet Port: </td><td align=left><input type=text name=telnet_port size=6 maxlength=5 value=\"$row[7]\">$NWB#servers-telnet_port$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager User: </td><td align=left><input type=text name=ASTmgrUSERNAME size=20 maxlength=20 value=\"$row[8]\">$NWB#servers-ASTmgrUSERNAME$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Secret: </td><td align=left><input type=text name=ASTmgrSECRET size=20 maxlength=20 value=\"$row[9]\">$NWB#servers-ASTmgrSECRET$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Update User: </td><td align=left><input type=text name=ASTmgrUSERNAMEupdate size=20 maxlength=20 value=\"$row[10]\">$NWB#servers-ASTmgrUSERNAMEupdate$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Listen User: </td><td align=left><input type=text name=ASTmgrUSERNAMElisten size=20 maxlength=20 value=\"$row[11]\">$NWB#servers-ASTmgrUSERNAMElisten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Manager Send User: </td><td align=left><input type=text name=ASTmgrUSERNAMEsend size=20 maxlength=20 value=\"$row[12]\">$NWB#servers-ASTmgrUSERNAMEsend$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$row[13]</option></select> (Do NOT Adjust for DST)$NWB#servers-local_gmt$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>VMail Dump Exten: </td><td align=left><input type=text name=voicemail_dump_exten size=20 maxlength=20 value=\"$row[14]\">$NWB#servers-voicemail_dump_exten$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>OSDial AD extension: </td><td align=left><input type=text name=answer_transfer_agent size=20 maxlength=20 value=\"$row[15]\">$NWB#servers-answer_transfer_agent$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Default Context: </td><td align=left><input type=text name=ext_context size=20 maxlength=20 value=\"$row[16]\">$NWB#servers-ext_context$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>System Performance: </td><td align=left><select size=1 name=sys_perf_log><option>Y</option><option>N</option><option selected>$row[17]</option></select>$NWB#servers-sys_perf_log$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Server Logs: </td><td align=left><select size=1 name=vd_server_logs><option>Y</option><option>N</option><option selected>$row[18]</option></select>$NWB#servers-vd_server_logs$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>AGI Output: </td><td align=left><select size=1 name=agi_output><option>NONE</option><option>STDERR</option><option>FILE</option><option>BOTH</option><option selected>$row[19]</option></select>$NWB#servers-agi_output$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center></form>\n";


	### vicidial server trunk records for this server
	echo "<br><br><center><font color=navy size=+1>OSDial TRUNKS FOR THIS SERVER &nbsp;</font> $NWB#osdial_server_trunks$NWE<br>\n";
	echo "<TABLE width=500 cellspacing=3>\n";
	echo "<tr><td><font color=navy>CAMPAIGN</font></td><td><font color=navy>TRUNKS</font> </td><td><font color=navy>RESTRICTION</font> </td><td> </td><td><font color=navy>DELETE</font> </td></tr>\n";

		$stmt="SELECT * from vicidial_server_trunks where server_ip='$server_ip' order by campaign_id";
		$rslt=mysql_query($stmt, $link);
		$recycle_to_print = mysql_num_rows($rslt);
		$o=0;
		while ($recycle_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$o++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1>$rowx[1]</font><form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
		echo "<input type=hidden name=campaign_id value=\"$rowx[1]\">\n";
		echo "<input type=hidden name=ADD value=421111111111></td>\n";
		echo "<td><font size=1><input size=6 maxlength=4 name=dedicated_trunks value=\"$rowx[2]\"></font></td>\n";
		echo "<td><select size=1 name=trunk_restriction><option>MAXIMUM_LIMIT</option><option>OVERFLOW_ALLOWED</option><option SELECTED>$rowx[3]</option></select></td>\n";
		echo "<td><font size=1><input type=submit name=submit value=MODIFY></form></font></td>\n";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=621111111111&campaign_id=$rowx[1]&server_ip=$server_ip\">DELETE</a></font></td></tr>\n";
		}

	echo "</table></font></center><br><br>\n";

	echo "<br><center><font color=navy>ADD NEW SERVER OSDial TRUNK<BR><br></font></font><form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=ADD value=221111111111>\n";
	echo "<input type=hidden name=server_ip value=\"$server_ip\">\n";
	echo "TRUNKS: <input size=6 maxlength=4 name=dedicated_trunks><BR><br>\n";
	echo "CAMPAIGN: <select size=1 name=campaign_id>\n";
	echo "$campaigns_list\n";
	echo "</select><BR><br>\n";
	echo "RESTRICTION: <select size=1 name=trunk_restriction><option>MAXIMUM_LIMIT</option><option>OVERFLOW_ALLOWED</option></select><BR><br>\n";
	echo "<input type=submit name=submit value=ADD><BR></font>\n";

	echo "</center></FORM><br>\n";


	### list of phones on this server
	echo "<center>\n";
	echo "<br><font color=navy>PHONES WITHIN THIS SERVER</font><br><br>\n";
	echo "<TABLE width=400 cellspacing=3>\n";
	echo "<tr><td><font color=navy>EXTENSION</font></td><td><font color=navy>NAME</font></td><td><font color=navy>ACTIVE</font></td></tr>\n";

		$active_phones = 0;
		$inactive_phones = 0;
		$stmt="SELECT extension,active,fullname from phones where server_ip='$row[2]'";
		$rsltx=mysql_query($stmt, $link);
		$lists_to_print = mysql_num_rows($rsltx);
		$camp_lists='';

		$o=0;
		while ($lists_to_print > $o) {
			$rowx=mysql_fetch_row($rsltx);
			$o++;
		if (ereg("Y", $rowx[1])) {$active_phones++;   $camp_lists .= "'$rowx[0]',";}
		if (ereg("N", $rowx[1])) {$inactive_phones++;}

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111111111&extension=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td><td><font size=1>$rowx[2]</td><td><font size=1>$rowx[1]</td></tr>\n";
		}

	echo "</table></font></center><br>\n";


	### list of conferences on this server
	echo "<center>\n";
	echo "<br><br><font color=navy>CONFERENCES WITHIN THIS SERVER</font><br><br>\n";
	echo "<TABLE width=400 cellspacing=3>\n";
	echo "<tr><td>CONFERENCE</td><td>EXTENSION</td></tr>\n";

		$active_confs = 0;
		$stmt="SELECT conf_exten,extension from conferences where server_ip='$row[2]'";
		$rsltx=mysql_query($stmt, $link);
		$lists_to_print = mysql_num_rows($rsltx);
		$camp_lists='';

		$o=0;
		while ($lists_to_print > $o) {
			$rowx=mysql_fetch_row($rsltx);
			$o++;
			$active_confs++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td><td><font size=1>$rowx[2]</td></tr>\n";
		}

	echo "</table></font></center><br>\n";


	### list of vicidial conferences on this server
	echo "<center>\n";
	echo "<br><br><font color=navy>OSDial CONFERENCES WITHIN THIS SERVER<br><br>\n";
	echo "<TABLE width=400 cellspacing=3>\n";
	echo "<tr><td><font color=navy>VD CONFERENCE</font></td><td><font color=navy>EXTENSION</font></td></tr>\n";

		$active_vdconfs = 0;
		$stmt="SELECT conf_exten,extension from vicidial_conferences where server_ip='$row[2]'";
		$rsltx=mysql_query($stmt, $link);
		$lists_to_print = mysql_num_rows($rsltx);
		$camp_lists='';

		$o=0;
		while ($lists_to_print > $o) {
			$rowx=mysql_fetch_row($rsltx);
			$o++;
			$active_vdconfs++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111111111111&conf_exten=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td><td><font size=1>$rowx[2]</td></tr>\n";
		}

	echo "</table></font></center><br>\n";


	echo "<center><b>\n";

		$camp_lists = eregi_replace(".$","",$camp_lists);
	echo "<font color=navy>This server has $active_phones active phones and $inactive_phones inactive phones<br><br>\n";
	echo "This server has $active_confs active conferences<br><br>\n";
	echo "This server has $active_vdconfs active OSDial conferences</font><br><br>\n";
	echo "</b></center>\n";
	if ($LOGast_delete_phones > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=511111111111&server_id=$server_id&server_ip=$server_ip\">DELETE THIS SERVER</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}



######################
# ADD=100000000000 display all servers
######################
if ($ADD==100000000000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from servers order by server_id";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>SERVERS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td><font size=1 color=white><B>SERVER</B></td>";
echo "<td><font size=1 color=white><B>ASTERISK</B></td>";
echo "<td align=center><font size=1 color=white><B>ACTIVE</B></td>";
echo "<td align=center colspan=2><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=311111111111&server_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1>$row[1]</td>";
		echo "<td><font size=1> $row[2]</td>";
		echo "<td><font size=1> $row[4]</td>";
		echo "<td align=center><font size=1> $row[3]</td>";
		echo "<td><font size=1> &nbsp;</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=311111111111&server_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}



#### Archive Server

######################
# ADD=499111111111111 modify archive serversettings
######################

if ($ADD==499111111111111) {
	if ($LOGmodify_servers==1) {

		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=navy SIZE=2>";
		echo "<br>OSDial ARCHIVE SERVER MODIFIED\n";

		if ($archive_transfer_method == "FTP" and $archive_port == "") {
			$archive_port = "21";
		} elseif ($archive_transfer_method == "SFTP" and $archive_port == "") {
			$archive_port = "22";
		} elseif ($archive_transfer_method == "SCP" and $archive_port == "") {
			$archive_port = "22";
		}

		$stmt1 = "UPDATE configuration SET data='$archive_hostname' WHERE name='ArchiveHostname';";
		$stmt2 = "UPDATE configuration SET data='$archive_transfer_method' WHERE name='ArchiveTransferMethod';";
		$stmt3 = "UPDATE configuration SET data='$archive_port' WHERE name='ArchivePort';";
		$stmt4 = "UPDATE configuration SET data='$archive_username' WHERE name='ArchiveUsername';";
		$stmt5 = "UPDATE configuration SET data='$archive_password' WHERE name='ArchivePassword';";
		$stmt6 = "UPDATE configuration SET data='$archive_path' WHERE name='ArchivePath';";
		$stmt7 = "UPDATE configuration SET data='$archive_web_path' WHERE name='ArchiveWebPath';";
		$stmt8 = "UPDATE configuration SET data='$archive_mix_format' WHERE name='ArchiveMixFormat';";

		$rslt = mysql_query($stmt1, $link);
		$rslt = mysql_query($stmt2, $link);
		$rslt = mysql_query($stmt3, $link);
		$rslt = mysql_query($stmt4, $link);
		$rslt = mysql_query($stmt5, $link);
		$rslt = mysql_query($stmt6, $link);
		$rslt = mysql_query($stmt7, $link);
		$rslt = mysql_query($stmt8, $link);

	### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0) {
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt1|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt2|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt3|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt4|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt5|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt6|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt7|\n");
			fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt8|\n");
			fclose($fp);
		}
	} else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
		exit;
	}
	$ADD=399111111111111;	# go to vicidial system settings form below
}

######################
# ADD=399111111111111 modify archive server settings
######################

if ($ADD=="399111111111111") {
	if ($LOGmodify_servers==1) {
		echo "<TABLE align=center><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$stmt="SELECT name,data FROM configuration WHERE name LIKE 'Archive%';";
		$rslt = mysql_query($stmt, $link);
		$rows = mysql_num_rows($rslt);

                $c = 0;
		while ($rows > $c) {
			$row = mysql_fetch_row($rslt);
			if ($row[0] == "ArchiveHostname") {
				$archive_hostname = $row[1];
			} elseif ($row[0] == "ArchiveTransferMethod") {
				$archive_transfer_method = $row[1];
			} elseif ($row[0] == "ArchivePort") {
				$archive_port = $row[1];
			} elseif ($row[0] == "ArchiveUsername") {
				$archive_username = $row[1];
			} elseif ($row[0] == "ArchivePassword") {
				$archive_password = $row[1];
			} elseif ($row[0] == "ArchivePath") {
				$archive_path = $row[1];
			} elseif ($row[0] == "ArchiveWebPath") {
				$archive_web_path = $row[1];
			} elseif ($row[0] == "ArchiveMixFormat") {
				$archive_mix_format = $row[1];
			}
			$c++;
		}

		echo "<center><br><font color=navy size=+1>MODIFY ARCHIVE SERVER SETTINGS</font><br><form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=ADD value=499111111111111>\n";
		echo "<center><TABLE width=$section_width cellspacing=3>\n";

		echo "<tr bgcolor=#C1D6DF><td align=right>Archive Server Address: </td><td align=left><input type=text name=archive_hostname size=30 maxlength=30 value=\"$archive_hostname\">$NWB#settings-archive_hostname$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Transfer Method: </td><td align=left><select size=1 name=archive_transfer_method><option>FTP</option><option>SFTP</option><option>SCP</option><option selected>$archive_transfer_method</option></select>$NWB#settings-archive_transfer_method$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Port: </td><td align=left><input type=text name=archive_port size=6 maxlength=5 value=\"$archive_port\">$NWB#settings-archive_port$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Username: </td><td align=left><input type=text name=archive_username size=20 maxlength=20 value=\"$archive_username\">$NWB#settings-archive_username$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=archive_password size=20 maxlength=20 value=\"$archive_password\">$NWB#settings-archive_password$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Path: </td><td align=left><input type=text name=archive_path size=40 maxlength=255 value=\"$archive_path\">$NWB#settings-archive_path$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Web Path: </td><td align=left><input type=text name=archive_web_path size=40 maxlength=255 value=\"$archive_web_path\">$NWB#settings-archive_web_path$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Mix Format: </td><td align=left><select size=1 name=archive_mix_format><option>MP3</option><option>WAV</option><option>GSM</option><option>OGG</option><option selected>$archive_mix_format</option></select>$NWB#settings-archive_mix_format$NWE</td></tr>\n";


		echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
		echo "</TABLE></center>\n";
		echo "</form>\n";
	} else {
		echo "You do not have permission to view this page\n";
		exit;
	}
}





#### QC Servers


######################
# ADD=499211111111111 modify qc serversettings
######################

if ($ADD==499211111111111) {
	if ($LOGmodify_servers==1) {

		if (($qc_server_transfer_type == "BATCH" or $qc_server_transfer_type == "ARCHIVE") and $qc_server_batch_time == "0") $qc_server_batch_time="23";
		if ($qc_server_transfer_type == "ARCHIVE" and $qc_server_archive == "NONE") $qc_server_archive="ZIP";
		if ($qc_server_transfer_type == "IMMEDIATE" or $qc_server_transfer_type == "BATCH") $qc_server_archive="NONE";
		if ($qc_server_transfer_type == "IMMEDIATE") $qc_server_batch_time="0";

		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=navy SIZE=2>";
		if ($SUB==1) {
			$qcact = "ADD";
			echo "<br>OSDial QC SERVER ADDED\n";

			$stmt = "INSERT INTO qc_servers (name,description,transfer_method,host,transfer_type,batch_time,username,password,home_path,location_template,archive,active) ";
			$stmt .= "VALUES ('$qc_server_name','$qc_server_description','$qc_server_transfer_method','$qc_server_host','$qc_server_transfer_type','$qc_server_batch_time',";
			$stmt .= "'$qc_server_username','$qc_server_password','$qc_server_home_path','$qc_server_location_template','$qc_server_archive','$qc_server_active');";

		} elseif ($SUB==2) {
			$qcact = "MODIFIED";
			echo "<br>OSDial QC SERVER MODIFIED\n";

			$stmt = "UPDATE qc_servers SET name='$qc_server_name',description='$qc_server_description',transfer_method='$qc_server_transfer_method',";
			$stmt .= "host='$qc_server_host',transfer_type='$qc_server_transfer_type',batch_time='$qc_server_batch_time',username='$qc_server_username',password='$qc_server_password',";
			$stmt .= "home_path='$qc_server_home_path',location_template='$qc_server_location_template',archive='$qc_server_archive',active='$qc_server_active' ";
			$stmt .= "WHERE id='$qc_server_id';";

		} elseif ($SUB==3) {
			$qcact = "ADD RULE";
			echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
			echo "<br>OSDial QC SERVER RULE MODIFIED\n";

			$stmt = "INSERT INTO qc_server_rules (qc_server_id,query) VALUES ('$qc_server_id','" . mysql_real_escape_string($qc_server_rule_query) . "');";
		} elseif ($SUB==4) {
			$qcact = "MODIFIED RULE";
			echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
			echo "<br>OSDial QC SERVER RULE MODIFIED\n";

			$stmt = "UPDATE qc_server_rules SET query='" . mysql_real_escape_string($qc_server_rule_query) ."' WHERE id='$qc_server_rule_id';";
			$qc_server_rule_query = "";
			$SUB=2;
		}
		$rslt = mysql_query($stmt, $link);
		if ($SUB==1) {
			$stmt = "SELECT id FROM qc_servers ORDER BY id DESC LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$qc_server_id=$row[0];
			$SUB++;
		}

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0) {
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|$qcact QC SERVER|$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
		}
	} else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
		exit;
	}
	$ADD=399211111111111;	# go to vicidial system settings form below
}


######################
# ADD=699211111111111 delete qc server and sql records.
######################

if ($ADD==699211111111111){
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	if ($SUB==2) {
		# Delete rule entries
		$stmt="DELETE from qc_server_rules where qc_server_id='$qc_server_id';";
		$rslt=mysql_query($stmt, $link);
		
		# Delete server entry
		$stmt="DELETE from qc_servers where id='$qc_server_id';";
		$rslt=mysql_query($stmt, $link);
		$nQSI='';
		$nSUB='';
	} elseif ($SUB==4) {
		# Delete rule entry
		$stmt="DELETE from qc_server_rules where id='$qc_server_rule_id';";
		$rslt=mysql_query($stmt, $link);
		$nQSI=$qc_server_id;
		$nSUB=2;
	}

	### LOG CHANGES TO LOG FILE ###
	if ($SUB > 0 and $WeBRooTWritablE > 0) {
		$fp = fopen ("./admin_changes_log.txt", "a");
		fwrite ($fp, "$date|!!!DELETING QC!!!!|$PHP_AUTH_USER|$ip|SUB=$SUB|qc_server_id='$qc_server_id'|qc_server_rule_id='$qc_server_rule_id'|\n");
		fclose($fp);
	}
	echo "<br><B>OSDial QC DELETION COMPLETED: $qc_server_id - $qc_server_rule_id</B>\n";
	echo "<br><br>\n";

	$SUB=$nSUB;
	$qc_server_id=$nQSI;
	$qc_server_rule_id='';
	$ADD='399211111111111';		# go to vicidial conference list
}



######################
# ADD=399211111111111 modify QC server settings
######################

if ($ADD=="399211111111111") {
	if ($LOGmodify_servers==1) {
		echo "<TABLE align=center><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$stmt="SELECT id,name,description,host,transfer_method,transfer_type FROM qc_servers;";
		$rslt = mysql_query($stmt, $link);
		$rows = mysql_num_rows($rslt);

		echo "<center><br><font color=navy size=+1>QC SERVER LIST</font><br>\n";
		echo "<form action=$PHP_SELF method=POST>\n";
		echo "<input type=hidden name=ADD value=399211111111111>\n";
		echo "<input type=hidden name=SUB value=1>\n";
		echo "<center><TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=navy>";
		echo "<td align=center><font size=1 color=white>#</font></td>";
		echo "<td align=center><font size=1 color=white>Name</font></td>";
		echo "<td align=center><font size=1 color=white>Description</font></td>";
		echo "<td align=center><font size=1 color=white>Host</font></td>";
		echo "<td align=center><font size=1 color=white>Method</font></td>";
		echo "<td align=center><font size=1 color=white>Type</font></td>";
		echo "<td colspan=2><font size=1 color=white>&nbsp;</font></td>";
		echo "</tr>\n";
                $c = 0;
		while ($rows > $c) {
			$row = mysql_fetch_row($rslt);

			if (eregi("1$|3$|5$|7$|9$", $c)) {
				$bgcolor='bgcolor="#CBDCE0"'; 
			} else {
				$bgcolor='bgcolor="#C1D6DB"';
			}

			echo "<tr $bgcolor>";
			echo "<td><font size=1>$c</td>";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=399211111111111&SUB=2&qc_server_id=$row[0]\">$row[1]</a></td>";
			echo "<td><font size=1>$row[2]</td>";
			echo "<td><font size=1>$row[3]</td>";
			echo "<td><font size=1>$row[4]</td>";
			echo "<td><font size=1>$row[5]</td>";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=399211111111111&SUB=2&qc_server_id=$row[0]\">MODIFY</a></td>";
			echo "<td><font size=1><a href=\"$PHP_SELF?ADD=699211111111111&SUB=2&qc_server_id=$row[0]\">REMOVE</a></td>";
			echo "</tr>\n";

			$c++;
		}
		echo "<tr bgcolor=#C1D6DF><td align=center colspan=8><input type=submit name=submit VALUE=NEW></td></tr>\n";
		echo "</TABLE></center>\n";
		echo "</form>\n";

		if ($SUB==1) {
			echo "<br><font color=navy>NEW QC SERVER</font>\n";
			echo "<form action=$PHP_SELF method=POST>\n";
			$qc_server_transfer_method   = "FTP";
			$qc_server_home_path         = "/home/USERNAME";
			$qc_server_location_template = "[campaign_id]/[date]";
			$qc_server_transfer_type     = "IMMEDIATE";
			$qc_server_archive           = "NONE";
			$qc_server_active            = "N";
			$qc_server_batch_time        = "0";
		} elseif ($SUB>1) {
			# Modify server
			echo "<br><font color=navy>MODIFY QC SERVER</font>\n";
			echo "<form action=$PHP_SELF method=POST>\n";
			echo "<input type=hidden name=qc_server_id value=$qc_server_id>\n";

			$stmt="SELECT * FROM qc_servers WHERE id='$qc_server_id';";
			$rslt = mysql_query($stmt, $link);
			$row = mysql_fetch_row($rslt);

			$qc_server_name              = $row[1];
			$qc_server_description       = $row[2];
			$qc_server_transfer_method   = $row[3];
			$qc_server_host              = $row[4];
			$qc_server_username          = $row[5];
			$qc_server_password          = $row[6];
			$qc_server_home_path         = $row[7];
			$qc_server_location_template = $row[8];
			$qc_server_transfer_type     = $row[9];
			$qc_server_archive           = $row[10];
			$qc_server_active            = $row[11];
			$qc_server_batch_time        = $row[12];
		}

		if ($SUB>0) {
			# New Server
			echo "<input type=hidden name=ADD value=499211111111111>\n";
			echo "<input type=hidden name=SUB value=$SUB>\n";
			echo "<center><TABLE width=$section_width cellspacing=1>\n";
	
			echo "<tr bgcolor=#C1D6DF><td align=right>Name: </td><td align=left><input type=text name=qc_server_name size=20 maxlength=20 value=\"$qc_server_name\">$NWB#qc-server_name$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Description: </td><td align=left><input type=text name=qc_server_description size=40 maxlength=100 value=\"$qc_server_description\">$NWB#qc-server_description$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Transfer Method: </td><td align=left><select size=1 name=qc_server_transfer_method><option>FTP</option><option>SFTP</option><option>SCP</option><option selected>$qc_server_transfer_method</option></select>$NWB#qc-server_transfer_method$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Hostname/IP: </td><td align=left><input type=text name=qc_server_host size=30 maxlength=50 value=\"$qc_server_host\">$NWB#qc-server_host$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Username: </td><td align=left><input type=text name=qc_server_username size=30 maxlength=30 value=\"$qc_server_username\">$NWB#qc-server_username$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Password: </td><td align=left><input type=text name=qc_server_password size=30 maxlength=30 value=\"$qc_server_password\">$NWB#qc-server_password$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Home Path: </td><td align=left><input type=text name=qc_server_home_path size=40 maxlength=100 value=\"$qc_server_home_path\">$NWB#qc-server_home_path$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Location Template: </td><td align=left><input type=text name=qc_server_location_template size=40 maxlength=255 value=\"$qc_server_location_template\">$NWB#qc-server_location_template$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Transfer Type: </td><td align=left><select size=1 name=qc_server_transfer_type><option>IMMEDIATE</option><option>BATCH</option><option>ARCHIVE</option><option selected>$qc_server_transfer_type</option></select>$NWB#qc-server_transfer_type$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Archive/Compression: </td><td align=left><select size=1 name=qc_server_archive><option>NONE</option><option>ZIP</option><option>TAR</option><option>TGZ</option><option>TBZ2</option><option selected>$qc_server_archive</option></select>$NWB#qc-server_archive$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Batch Time (hour): </td><td align=left><select size=1 name=qc_server_batch_time><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option><option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option><option>19</option><option>20</option><option>21</option><option>22</option><option>23</option><option selected>$qc_server_batch_time</option></select>$NWB#qc-server_batch_time$NWE</td></tr>\n";
			echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=qc_server_active><option>Y</option><option>N</option><option selected>$qc_server_active</option></select>$NWB#qc-server_active$NWE</td></tr>\n";
	
			echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
			echo "</TABLE></center>\n";
			echo "</form>\n";
		}

		if ($SUB>1) {
			# List QC rules
			echo "<br><font color=navy>QC SERVER RULES</font>\n";
			echo "<center><table width=$section_width cellspacing=1>\n";
			echo "<tr bgcolor=navy>";
			echo "<td align=center bgcolor=navy><font size=1 color=white>#</font></td>";
			echo "<td align=center bgcolor=navy><font size=1 color=white>Query</font></td>";
			echo "<td colspan=2 align=center bgcolor=navy><font size=1 color=white>&nbsp;</font></td>";
			echo "</tr>\n";

			$stmt="SELECT * FROM qc_server_rules WHERE qc_server_id='$qc_server_id';";
			$rslt = mysql_query($stmt, $link);
			$rows = mysql_num_rows($rslt);
                	$c = 0;
			while ($rows > $c) {
				$row = mysql_fetch_row($rslt);
	
				if (eregi("1$|3$|5$|7$|9$", $c)) {
					$bgcolor='bgcolor="#CBDCE0"'; 
				} else {
					$bgcolor='bgcolor="#C1D6DB"';
				}
	
				echo "<tr $bgcolor>";
				echo "<td><font size=1>$c</td>";
				echo "<td><font size=1>$row[2]</td>";
				echo "<td><font size=1><a href=\"$PHP_SELF?ADD=399211111111111&SUB=4&qc_server_id=$qc_server_id&qc_server_rule_id=$row[0]\">MODIFY</a></td>";
				echo "<td><font size=1><a href=\"$PHP_SELF?ADD=699211111111111&SUB=4&qc_server_id=$qc_server_id&qc_server_rule_id=$row[0]\">REMOVE</a></td>";
				echo "</tr>\n";
	
				$c++;
			}

			$qcfld = "<form action=$PHP_SELF method=POST>\n";
			$qcfld .= "<input type=hidden name=ADD value=499211111111111>\n";
			$qcfld .= "<input type=hidden name=qc_server_id value=$qc_server_id>\n";
			if ($SUB==4) {
				# Modify QC rule
				$qcfld .= "<input type=hidden name=SUB value=4>\n";
				$qcfld .= "<input type=hidden name=qc_server_rule_id value=$qc_server_rule_id>\n";
				$stmt="SELECT * FROM qc_server_rules WHERE id='$qc_server_rule_id';";
				$rslt = mysql_query($stmt, $link);
				$row = mysql_fetch_row($rslt);
				$qcract = "MODIFY";
				$qc_server_rule_query = $row[2];
			} else {
				# New QC rule
				$qcfld .= "<input type=hidden name=SUB value=3>\n";
				$qcract = "NEW";
			}
			$qcfld .= "<tr bgcolor=#C1D6DF>";
			$qcfld .= "<td>&nbsp;</td>";
			$qcfld .= "<td align=left><input type=text name=qc_server_rule_query size=60 maxlength=255 value=\"$qc_server_rule_query\">$NWB#qc-server_rule_query$NWE</td>";
			$qcfld .= "<td align=center colspan=2><input type=submit name=submit VALUE=$qcract></td>";
			$qcfld .= "</tr></form>\n";
			echo $qcfld;
			echo "</table>\n";
		}


	} else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
		exit;
	}
}



?>