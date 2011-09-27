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
# ADD=28 adds new status to the campaign dial statuses
######################

if ($ADD==28)
{
	$status = OSDpreg_replace("/-----.*/",'',$status);
	$stmt=sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_id='%s' AND dial_statuses LIKE '%% %s %%';",mres($campaign_id),mres($status));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT ADDED - there is already an entry for this campaign with this status</font>\n";}
	else
		{
		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($status) < 1) )
			{
			 echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length</font>\n";
			}
		 else
			{
			#echo "<br><B><font color=$default_text>CAMPAIGN DIAL STATUS ADDED: $campaign_id - $status</font></B>\n";

			$stmt=sprintf("SELECT dial_statuses FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign_id));
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			if (OSDstrlen($row[0])<2) {$row[0] = ' -';}
			$dial_statuses = " $status$row[0]";
			$stmt=sprintf("UPDATE osdial_campaigns SET dial_statuses='%s' WHERE campaign_id='%s';",mres($dial_statuses),mres($campaign_id));
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD CAMPAIGN DIAL STATUS  |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
#$SUB=28;
$ADD=31;
}



######################
# ADD=68 remove campaign dial status
######################

if ($ADD==68)
{
	if ($LOG['modify_campaigns']==1)
	{
	$stmt=sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_id='%s' AND dial_statuses LIKE '%% %s %%';",mres($campaign_id),mres($status));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] < 1)
		{echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT REMOVED - this dial status is not selected for this campaign</font>\n";}
	else
		{
		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($status) < 1) )
			{
			 echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT REMOVED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length><br>\n";
			}
		 else
			{
			#echo "<br><B><font color=$default_text>CAMPAIGN DIAL STATUS REMOVED: $campaign_id - $status</font></B>\n";

			$stmt=sprintf("SELECT dial_statuses FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign_id));
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			$dial_statuses = OSDpreg_replace("/ $status /"," ",$row[0]);
			$stmt=sprintf("UPDATE osdial_campaigns SET dial_statuses='%s' WHERE campaign_id='%s';",mres($dial_statuses),mres($campaign_id));
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DIAL STATUS REMOVED   |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
    #$SUB=28;
    $ADD=31;	# go to campaign modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



require('campaigns.php');
?>
