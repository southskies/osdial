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
# ADD=26 adds the new auto alt dial status to the campaign
######################

if ($ADD==26)
{
	$status = eregi_replace("-----.*",'',$status);
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	$stmt="SELECT count(*) from osdial_campaigns where campaign_id='$campaign_id' and auto_alt_dial_statuses LIKE \"% $status %\";";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red> AUTO ALT DIAL STATUS NOT ADDED - there is already an entry for this campaign with this status</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
			{
			 echo "<br><font color=red>AUTO ALT DIAL STATUS NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length</font>\n";
			}
		 else
			{
			echo "<br><B><font color=$default_text>AUTO ALT DIAL STATUS ADDED: $campaign_id - $status</font></B>\n";

			$stmt="SELECT auto_alt_dial_statuses from osdial_campaigns where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			if (strlen($row[0])<2) {$row[0] = ' -';}
			$auto_alt_dial_statuses = " $status$row[0]";
			$stmt="UPDATE osdial_campaigns set auto_alt_dial_statuses='$auto_alt_dial_statuses' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A AUTO-ALT-DIAL STATUS|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$SUB=26;
$ADD=31;
}

######################
# ADD=66 delete auto alt dial status from the campaign
######################

if ($ADD==66)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	$stmt="SELECT count(*) from osdial_campaigns where campaign_id='$campaign_id' and auto_alt_dial_statuses LIKE \"% $status %\";";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] < 1)
		{echo "<br><font color=red>AUTO ALT DIAL STATUS NOT DELETED - this auto alt dial status is not in this campaign</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
			{
			 echo "<br><font color=red>AUTO ALT DIAL STATUS NOT DELETED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=$default_text>AUTO ALT DIAL STATUS DELETED: $campaign_id - $status</font></B>\n";

			$stmt="SELECT auto_alt_dial_statuses from osdial_campaigns where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			$auto_alt_dial_statuses = eregi_replace(" $status "," ",$row[0]);
			$stmt="UPDATE osdial_campaigns set auto_alt_dial_statuses='$auto_alt_dial_statuses' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DELETE AUTALTDIALSTTUS|$PHP_AUTH_USER|$ip|$stmt|\n");
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
$SUB=26;
$ADD=31;	# go to campaign modification form below
}


######################
# ADD=36 display all campaign auto-alt dial entries
######################
if ($ADD==36)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

echo "<center><br><font color=$default_text size=+1>CAMPAIGN LEAD AUTO-ALT DIALS</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>CAMPAIGN</td>\n";
echo "    <td>NAME</td>\n";
echo "    <td>AUTO-ALT DIAL</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

	$stmt=sprintf("SELECT campaign_id,campaign_name from osdial_campaigns where campaign_id IN %s order by campaign_id",$LOG['allowed_campaignsSQL']);
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
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}
		echo "  <tr $bgcolor class=\"row font1\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31&SUB=26&campaign_id=$campaigns_id_list[$o]\">" . mclabel($campaigns_id_list[$o]) . "</a></td>\n";
		echo "    <td>$campaigns_name_list[$o]</td>\n";
		echo "    <td>";

		$stmt="SELECT auto_alt_dial_statuses from osdial_campaigns where campaign_id='$campaigns_id_list[$o]';";
		$rslt=mysql_query($stmt, $link);
		$campstatus_to_print = mysql_num_rows($rslt);
		$p=0;
		while ( ($campstatus_to_print > $p) and ($p < 10) )
			{
			$row=mysql_fetch_row($rslt);
			echo "$row[0] ";
			$p++;
			}
		if (strlen($row[0])<3) 
			{echo "<font color=grey><DEL>NONE</DEL></font>";}
		echo "</td>\n";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=31&SUB=26&campaign_id=$campaigns_id_list[$o]\">MODIFY AUTO-ALT DIAL</a></td>\n";
        echo "  </tr>\n";
		$o++;
		}

echo "  <tr class=tabfooter>";
echo "    <td colspan=4></td>";
echo "  </tr>";
echo "</table>\n";
echo "</center>\n";
}


require("campaigns.php");
?>
