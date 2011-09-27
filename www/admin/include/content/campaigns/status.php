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
# ADD=22 adds the new campaign status to the system
######################

if ($ADD==22)
{
	$stmt=sprintf("SELECT count(*) FROM osdial_campaign_statuses WHERE campaign_id='%s' AND status='%s';",mres($campaign_id),mres($status));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> CAMPAIGN STATUS NOT ADDED - there is already a campaign-status in the system with this name</font>\n";}
	else
		{
		#$stmt="SELECT count(*) from osdial_statuses where status='$status';";
		#$rslt=mysql_query($stmt, $link);
		#$row=mysql_fetch_row($rslt);
		#if ($row[0] > 0)
		#	{echo "<br><font color=$default_text> CAMPAIGN STATUS NOT ADDED - there is already a global-status in the system with this name</font>\n";}
		#else
		#	{
			 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($status) < 1) or (OSDstrlen($status_name) < 2) )
				{
				 echo "<br><font color=red> CAMPAIGN STATUS NOT ADDED - Please go back and look at the data you entered\n";
				 echo "<br>status must be between 1 and 8 characters in length\n";
				 echo "<br>status name must be between 2 and 30 characters in length</font><br>\n";
				}
			 else
				{
				echo "<br><B><font color=$default_text> CAMPAIGN STATUS ADDED: $campaign_id - $status</font></B>\n";

				$stmt="INSERT INTO osdial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category) values('$status','$status_name','$selectable','$campaign_id','$human_answered','$category');";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW CAMPAIGN STATUS |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}
				}
		#	}
		}
$SUB=22;
$ADD=31;
}


######################
# ADD=42 modify/delete campaign status in the system
######################

if ($ADD==42)
{
	if ($LOG['modify_campaigns']==1)
	{
	 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($status) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN STATUS NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>the campaign id needs to be at least 2 characters in length\n";
		 echo "<br>the campaign status needs to be at least 1 characters in length</font><br>\n";
		}
	 else
		{
		if (OSDpreg_match('/delete/',$stage))
			{
			echo "<br><B><font color=$default_text>CUSTOM CAMPAIGN STATUS DELETED: $campaign_id - $status</font></B>\n";

			$stmt="DELETE FROM osdial_campaign_statuses where campaign_id='$campaign_id' and status='$status';";
			$rslt=mysql_query($stmt, $link);

			$stmtA="DELETE FROM osdial_campaign_hotkeys where campaign_id='$campaign_id' and status='$status';";
			$rslt=mysql_query($stmtA, $link);


			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DELETE CAMPAIGN STATUS|$PHP_AUTH_USER|$ip|$stmt|$stmtA|\n");
				fclose($fp);
				}
			}
		if (OSDpreg_match('/modify/',$stage))
			{
			echo "<br><B><font color=$default_text>CUSTOM CAMPAIGN STATUS MODIFIED: $campaign_id - $status</font></B>\n";

			$stmt="UPDATE osdial_campaign_statuses SET status_name='$status_name',selectable='$selectable',human_answered='$human_answered',category='$category' where campaign_id='$campaign_id' and status='$status';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY CAMPAIGN STATUS|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
    $SUB=22;
    $ADD=31;	# go to campaign modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}

######################
# ADD=32 display all campaign statuses
######################
if ($ADD==32)
{
echo "<center><br><font color=$default_text size=+1>CUSTOM CAMPAIGN STATUSES</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr class=tabheader>\n";
echo "<td>CAMPAIGN</td>\n";
echo "<td>NAME</td>\n";
echo "<td align=center>STATUSES</td>\n";
echo "<td align=center>LINKS</td>\n";
echo "</tr>\n";

	$stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE campaign_id IN %s ORDER BY campaign_id;", $LOG['allowed_campaignsSQL']);
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
		echo "  <tr class=\"row font1\" " . bgcolor($o) . " ondblclick=\"window.location='$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaigns_id_list[$o]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaigns_id_list[$o]\">" . mclabel($campaigns_id_list[$o]) . "</a></td>";
		echo "    <td>$campaigns_name_list[$o]</td>";
		echo "    <td align=center>";

		$stmt=sprintf("SELECT status FROM osdial_campaign_statuses WHERE campaign_id='%s' ORDER BY status;",mres($campaigns_id_list[$o]));
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
		echo "    </td>";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$campaigns_id_list[$o]\">MODIFY STATUSES</a></td>\n";
        echo "  </tr>\n";
		$o++;
		}

echo "<tr class=tabfooter>";
echo "  <td colspan=4></td>";
echo "</tr>";
echo "</table></center>\n";
}


require("campaigns.php");
?>
