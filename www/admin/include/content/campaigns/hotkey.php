<?php


######################
# ADD=23 adds the new campaign hotkey to the system
######################

if ($ADD==23)
{
	$HKstatus_data = explode('-----',$HKstatus);
	$status = $HKstatus_data[0];
	$status_name = $HKstatus_data[1];

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_campaign_hotkeys where campaign_id='$campaign_id' and hotkey='$hotkey' and hotkey='$hotkey';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> CAMPAIGN HOT KEY NOT ADDED - there is already a campaign-hotkey in the system with this hotkey</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) or (strlen($hotkey) < 1) )
			{
			 echo "<br><font color=red> CAMPAIGN HOT KEY NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>hotkey must be a single character between 1 and 9 \n";
			 echo "<br>status must be between 1 and 8 characters in length</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=navy> CAMPAIGN HOT KEY ADDED: $campaign_id - $status - $hotkey</font></B>\n";

			$stmt="INSERT INTO vicidial_campaign_hotkeys values('$status','$hotkey','$status_name','$selectable','$campaign_id');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW CAMPAIGN HOT KEY |$PHP_AUTH_USER|$ip|'$status','$hotkey','$status_name','$selectable','$campaign_id'|\n");
				fclose($fp);
				}
			}
		}
$SUB=23;
$ADD=31;
}

######################
# ADD=43 delete campaign hotkey in the system
######################

if ($ADD==43)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) or (strlen($hotkey) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN HOT KEY NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>the campaign id needs to be at least 2 characters in length\n";
		 echo "<br>the campaign status needs to be at least 1 characters in length\n";
		 echo "<br>the campaign hotkey needs to be at least 1 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>CUSTOM CAMPAIGN HOT KEY DELETED: $campaign_id - $status - $hotkey</font></B>\n";

		$stmt="DELETE FROM vicidial_campaign_hotkeys where campaign_id='$campaign_id' and status='$status' and hotkey='$hotkey';";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|DELETE CAMPAIGN STATUS|$PHP_AUTH_USER|$ip|DELETE FROM vicidial_campaign_hotkeys where campaign_id='$campaign_id' and status='$status' and hotkey='$hotkey'|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=23;
$ADD=31;	# go to campaign modification form below
}


######################
# ADD=33 display all campaign hotkeys
######################
if ($ADD==33)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

echo "<center><br><font color=navy size=+1>CAMPAIGN HOTKEYS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>\n";
echo "<td><font color=white size=1>CAMPAIGN</font></td>\n";
echo "<td><font color=white size=1>NAME</font></td>\n";
echo "<td><font color=white size=1>HOTKEYS</font></td>\n";
echo "<td><font color=white size=1>LINKS</font></td>\n";
echo "</tr>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		$row=mysql_fetch_row($rslt);
		$campaigns_id_list[$o] = $row[0];
		$campaigns_name_list[$o] = $row[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o) 
		{
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=23&campaign_id=$campaigns_id_list[$o]\">$campaigns_id_list[$o]</a></td>";
		echo "<td><font size=1> $campaigns_name_list[$o] </td>";
		echo "<td><font size=1> ";

		$stmt="SELECT status from vicidial_campaign_hotkeys where campaign_id='$campaigns_id_list[$o]' order by status";
		$rslt=mysql_query($stmt, $link);
		$campstatus_to_print = mysql_num_rows($rslt);
		$p=0;
		while ( ($campstatus_to_print > $p) and ($p < 10) )
			{
			$row=mysql_fetch_row($rslt);
			echo "$row[0] ";
			$p++;
			}
		if ($p<1) 
			{echo "<font color=grey><DEL>NONE</DEL></font>";}
		echo "</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=23&campaign_id=$campaigns_id_list[$o]\">MODIFY HOTKEYS</a></td></tr>\n";
		$o++;
		}

echo "</TABLE></center>\n";
}


# TODO Goto campaign detail

?>