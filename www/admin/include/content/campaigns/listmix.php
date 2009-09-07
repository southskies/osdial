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
# ADD=49 modify campaign list mix in the system
######################

if ($ADD==49)
{
	if ($LOGmodify_campaigns==1)
	{
	##### MODIFY a list mix container entry #####
		if ($stage=='MODIFY')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

		$Flist_mix_container = "list_mix_container_$vcl_id";
		$Fmix_method = "mix_method_$vcl_id";
		$Fstatus = "status_$vcl_id";
		$Fvcl_name = "vcl_name_$vcl_id";

		if (isset($_GET[$Flist_mix_container]))				{$list_mix_container=$_GET[$Flist_mix_container];}
			elseif (isset($_POST[$Flist_mix_container]))	{$list_mix_container=$_POST[$Flist_mix_container];}
		if (isset($_GET[$Fmix_method]))						{$mix_method=$_GET[$Fmix_method];}
			elseif (isset($_POST[$Fmix_method]))			{$mix_method=$_POST[$Fmix_method];}
		if (isset($_GET[$Fstatus]))							{$status=$_GET[$Fstatus];}
			elseif (isset($_POST[$Fstatus]))				{$status=$_POST[$Fstatus];}
		if (isset($_GET[$Fvcl_name]))						{$vcl_name=$_GET[$Fvcl_name];}
			elseif (isset($_POST[$Fvcl_name]))				{$vcl_name=$_POST[$Fvcl_name];}
		$list_mix_container = preg_replace("/:$/","",$list_mix_container);

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) or (strlen($list_mix_container) < 6) or (strlen($vcl_name) < 2) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>vcl_name name must be between 2 and 30 characters in length</font><br>\n";
			}
		 else
			{
			$stmt="UPDATE osdial_campaigns_list_mix SET vcl_name='$vcl_name',mix_method='$mix_method',list_mix_container='$list_mix_container' where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

			echo "<br><B><font color=$default_text>LIST MIX MODIFIED: $campaign_id - $vcl_id - $vcl_name</font></B>\n";
			}
		}

	##### ADD a list mix container entry #####
		if ($stage=='ADD')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) or (strlen($list_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>list_id must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt="SELECT list_mix_container from osdial_campaigns_list_mix where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$OLDlist_mix_container =	$row[0];
			$NEWlist_mix_container = "$OLDlist_mix_container:$list_id|10|0| -|";

			$stmt="UPDATE osdial_campaigns_list_mix SET list_mix_container='$NEWlist_mix_container' where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

			echo "<br><B><font color=$default_text>LIST MIX MODIFIED: $campaign_id - $vcl_id - $list_id</font></B>\n";
			}
		}

	##### REMOVE a list mix container entry #####
		if ($stage=='REMOVE')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) or (strlen($list_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>list_id must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt="SELECT list_mix_container from osdial_campaigns_list_mix where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$MIXentries = $MT;
			$MIXentries = explode(":", $row[0]);
			$Ms_to_print = (count($MIXentries) - 0);

			if ($Ms_to_print < 2)
				{
				echo "<br><B><font color=red>LIST MIX NOT MODIFIED: You cannot delete the last list_id entry from a list mix</font></B>\n";
				}
			else
				{
				$MIXdetailsPCT = explode('|', $MIXentries[$mix_container_item]);
				$MIXpercentPCT = $MIXdetailsPCT[2];

				$q=0;
				while ($Ms_to_print > $q) 
					{
					if ( ($mix_container_item > $q) or ($mix_container_item < $q) )
						{
						if ( ($q==0) and ($mix_container_item > 0) )
							{
							$MIXdetailsONE = explode('|', $MIXentries[$q]);
							$MIXpercentONE = ($MIXdetailsONE[2] + $MIXpercentPCT);
							$NEWlist_mix_container .= "$MIXdetailsONE[0]|$MIXdetailsONE[1]|$MIXpercentONE|$MIXdetailsONE[3]|:";
							}
						else
							{
							if ( ($q==1) and ($mix_container_item < 1) )
								{
								$MIXdetailsONE = explode('|', $MIXentries[$q]);
								$MIXpercentONE = ($MIXdetailsONE[2] + $MIXpercentPCT);
								$NEWlist_mix_container .= "$MIXdetailsONE[0]|$MIXdetailsONE[1]|$MIXpercentONE|$MIXdetailsONE[3]|:";
								}
							else
								{
								$NEWlist_mix_container .= "$MIXentries[$q]:";
								}
							}
						}
					$q++;
					}
				$NEWlist_mix_container = preg_replace("/.$/",'',$NEWlist_mix_container);

				$stmt="UPDATE osdial_campaigns_list_mix SET list_mix_container='$NEWlist_mix_container' where campaign_id='$campaign_id' and vcl_id='$vcl_id';";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}

				echo "<br><B><font color=$default_text>LIST MIX MODIFIED: $campaign_id - $vcl_id - $list_id - $mix_container_item</font></B>\n";
				}
			}
		}

	##### ADD a NEW list mix #####
		if ($stage=='NEWMIX')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) or (strlen($vcl_name) < 2) )
			{
			 echo "<br><font color=red>LIST MIX NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>vcl_name must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt="SELECT count(*) from osdial_campaigns_list_mix where vcl_id='$vcl_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ($row[0] > 0)
				{
				 echo "<br><font color=red>LIST MIX NOT ADDED - There is already a list mix with this ID in the system</font>\n";
				}
			else
				{
				$stmt="INSERT INTO osdial_campaigns_list_mix SET list_mix_container='$list_id|1|100| $status -|',campaign_id='$campaign_id',vcl_id='$vcl_id',vcl_name='$vcl_name',mix_method='$mix_method',status='INACTIVE';";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}

				echo "<br><B><font color=$default_text>LIST MIX ADDED: $campaign_id - $vcl_id - $vcl_name</font></B>\n";
				}
			}
		}

	##### DELETE an existing list mix #####
		if ($stage=='DELMIX')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT DELETED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length</font>\n";
			}
		 else
			{
			$stmt="DELETE from osdial_campaigns_list_mix where vcl_id='$vcl_id' and campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

			echo "<br><B><font color=$default_text>LIST MIX DELETED: $campaign_id - $vcl_id - $vcl_name</font></B>\n";
			}
		}

	##### Set list mix entry to active #####
		if ($stage=='SETACTIVE')
		{
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

		 if ( (strlen($campaign_id) < 2) or (strlen($vcl_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT ACTIVATED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length</font>\n";
			}
		 else
			{
			$stmt="UPDATE osdial_campaigns_list_mix SET status='INACTIVE' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			$stmt="UPDATE osdial_campaigns_list_mix SET status='ACTIVE' where vcl_id='$vcl_id' and campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY LIST MIX       |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

			echo "<br><B><font color=$default_text>LIST MIX ACTIVATED: $campaign_id - $vcl_id - $vcl_name</font></B>\n";
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$SUB=29;
$ADD=31;	# go to campaign modification form below
}


######################
# ADD=39 display all campaign list mixes
######################
if ($ADD==39)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

echo "<center><br><font color=$default_text size=+1>CAMPAIGN LIST MIXES</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>\n";
echo "<td><font color=white size=1>CAMPAIGN</font></td>\n";
echo "<td><font color=white size=1>NAME</font></td>\n";
echo "<td><font color=white size=1>LIST MIX</font></td>\n";
echo "<td><font color=white size=1>LINKS</font></td>\n";
echo "</tr>\n";

	$stmt="SELECT campaign_id,campaign_name from osdial_campaigns order by campaign_id";
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
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaigns_id_list[$o]\">$campaigns_id_list[$o]</a></td>";
		echo "<td><font size=1> $campaigns_name_list[$o] </td>";
		echo "<td><font size=1> ";

		$stmt="SELECT vcl_id from osdial_campaigns_list_mix where campaign_id='$campaigns_id_list[$o]' order by status,vcl_id;";
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
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaigns_id_list[$o]\">MODIFY LIST MIX</a></td></tr>\n";
		$o++;
		}

echo "</TABLE></center>\n";
}

require("campaigns.php");
?>
