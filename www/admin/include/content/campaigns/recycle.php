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
# ADD=25 adds the new campaign lead recycle entry to the system
######################

if ($ADD==25)
{
	if ($LOG['modify_campaigns']==1)
	{
	$status = OSDpreg_replace("/-----.*/",'',$status);
	$stmt=sprintf("SELECT count(*) FROM osdial_lead_recycle WHERE campaign_id='%s' AND status='%s';",mres($campaign_id),mres($status));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> CAMPAIGN LEAD RECYCLE NOT ADDED - there is already a lead-recycle for this campaign with this status</font>\n";}
	else
		{
		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($status) < 1) or ($attempt_delay < 120) or ($attempt_maximum < 1) or ($attempt_maximum > 999) )
			{
			 echo "<br><font color=red>CAMPAIGN LEAD RECYCLE NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length\n";
			 echo "<br>attempt delay must be at least 120 seconds\n";
			 echo "<br>maximum attempts must be from 1 to 50</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=$default_text>CAMPAIGN LEAD RECYCLE ADDED: $campaign_id - $status - $attempt_delay</font></B>\n";

			$stmt=sprintf("INSERT INTO osdial_lead_recycle(campaign_id,status,attempt_delay,attempt_maximum,active) VALUES('%s','%s','%s','%s','%s');",mres($campaign_id),mres($status),mres($attempt_delay),mres($attempt_maximum),mres($active));
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW LEAD RECYCLE    |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
    $SUB=25;
    $ADD=31;
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



######################
# ADD=45 modify campaign lead recycle in the system
######################

if ($ADD==45)
{
	if ($LOG['modify_campaigns']==1)
	{
	 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($status) < 1) or ($attempt_delay < 120)  or ($attempt_maximum < 1) or ($attempt_maximum > 999) )
		{
		 echo "<br><font color=red>CAMPAIGN LEAD RECYCLE NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>status must be between 1 and 6 characters in length\n";
		 echo "<br>attempt delay must be at least 120 seconds\n";
		 echo "<br>maximum attempts must be from 1 to 10</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>CAMPAIGN LEAD MODIFIED: $campaign_id - $status - $attempt_delay</font></B>\n";

		$stmt=sprintf("UPDATE osdial_lead_recycle SET attempt_delay='%s',attempt_maximum='%s',active='%s' WHERE campaign_id='%s' AND status='%s';",mres($attempt_delay),mres($attempt_maximum),mre($active),mres($campaign_id),mres($status));
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY LEAD RECYCLE   |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
    $SUB=25;
    $ADD=31;	# go to campaign modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=65 delete campaign lead recycle in the system
######################

if ($ADD==65)
{
	if ($LOG['modify_campaigns']==1)
	{
	 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($status) < 1) )
		{
		 echo "<br><font color=red>CAMPAIGN LEAD RECYCLE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>status must be between 1 and 6 characters in length\n";
		 echo "<br>attempt delay must be at least 120 seconds\n";
		 echo "<br>maximum attempts must be from 1 to 10</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>CAMPAIGN LEAD RECYCLE DELETED: $campaign_id - $status - $attempt_delay</font></B>\n";

		$stmt=sprintf("DELETE FROM osdial_lead_recycle WHERE campaign_id='%s' AND status='%s';",mres($campaign_id),mres($status));
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|DELETE LEAD RECYCLE   |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
    $SUB=25;
    $ADD=31;	# go to campaign modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=35 display all campaign lead recycle entries
######################
if ($ADD==35)
{
echo "<center><br><font color=$default_text size=+1>CAMPAIGN LEAD RECYCLES</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>CAMPAIGN</td>\n";
echo "    <td>NAME</td>\n";
echo "    <td>LEAD RECYCLES</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

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
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31&SUB=25&campaign_id=$campaigns_id_list[$o]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31&SUB=25&campaign_id=$campaigns_id_list[$o]\">" . mclabel($campaigns_id_list[$o]) . "</a></td>\n";
		echo "    <td><font size=1> $campaigns_name_list[$o]</td>\n";
		echo "    <td>";

		$stmt=sprintf("SELECT status FROM osdial_lead_recycle WHERE campaign_id='%s' ORDER BY status;",mres($campaigns_id_list[$o]));
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
		echo "</td>\n";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=31&SUB=25&campaign_id=$campaigns_id_list[$o]\">MODIFY LEAD RECYCLES</a></td>\n";
        echo "  </tr>\n";
		$o++;
		}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=4></td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "</center>\n";
}

require("campaigns.php");
?>
