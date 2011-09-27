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
	if ($LOG['modify_campaigns']==1)
	{
	##### MODIFY a list mix container entry #####
		if ($stage=='MODIFY')
		{
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
		$list_mix_container = OSDpreg_replace("/:$/","",$list_mix_container);

		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($vcl_id) < 1) or (OSDstrlen($list_mix_container) < 6) or (OSDstrlen($vcl_name) < 2) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>vcl_name name must be between 2 and 30 characters in length</font><br>\n";
			}
		 else
			{
			$stmt=sprintf("UPDATE osdial_campaigns_list_mix SET vcl_name='%s',mix_method='%s',list_mix_container='%s' WHERE campaign_id='%s' AND vcl_id='%s';",mres($vcl_name),mres($mix_method),mres($list_mix_container),mres($campaign_id),mres($vcl_id));
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
		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($vcl_id) < 1) or (OSDstrlen($list_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>list_id must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt=sprintf("SELECT list_mix_container FROM osdial_campaigns_list_mix WHERE campaign_id='%s' AND vcl_id='%s';",mres($campaign_id),mres($vcl_id));
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$OLDlist_mix_container =	$row[0];
			$NEWlist_mix_container = "$OLDlist_mix_container:$list_id|10|0| -|";

			$stmt=sprintf("UPDATE osdial_campaigns_list_mix SET list_mix_container='%s' WHERE campaign_id='%s' AND vcl_id='%s';",mres($NEWlist_mix_container),mres($campaign_id),mres($vcl_id));
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
		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($vcl_id) < 1) or (OSDstrlen($list_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>list_id must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt=sprintf("SELECT list_mix_container FROM osdial_campaigns_list_mix WHERE campaign_id='%s' AND vcl_id='%s';",mres($campaign_id),mres($vcl_id));
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
				$NEWlist_mix_container = OSDpreg_replace("/.$/",'',$NEWlist_mix_container);

                $stmt=sprintf("UPDATE osdial_campaigns_list_mix SET list_mix_container='%s' WHERE campaign_id='%s' AND vcl_id='%s';",mres($NEWlist_mix_container),mres($campaign_id),mres($vcl_id));
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
		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($vcl_id) < 1) or (OSDstrlen($vcl_name) < 2) )
			{
			 echo "<br><font color=red>LIST MIX NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length\n";
			 echo "<br>vcl_name must be at least 2 characters in length</font>\n";
			}
		 else
			{
			$stmt=sprintf("SELECT count(*) FROM osdial_campaigns_list_mix WHERE vcl_id='%s';",mres($vcl_id));
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ($row[0] > 0)
				{
				 echo "<br><font color=red>LIST MIX NOT ADDED - There is already a list mix with this ID in the system</font>\n";
				}
			else
				{
				$stmt=sprintf("INSERT INTO osdial_campaigns_list_mix SET list_mix_container='%s|1|100| %s -|',campaign_id='%s',vcl_id='%s',vcl_name='%s',mix_method='%s',status='INACTIVE';",mres($list_id),mres($status),mres($campaign_id),mres($vcl_id),mres($vcl_name),mres($mix_method));
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
		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($vcl_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT DELETED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length</font>\n";
			}
		 else
			{
			$stmt=sprintf("DELETE FROM osdial_campaigns_list_mix WHERE vcl_id='%s' AND campaign_id='%s';",mres($vcl_id),mres($campaign_id));
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
		 if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($vcl_id) < 1) )
			{
			 echo "<br><font color=red>LIST MIX NOT ACTIVATED - Please go back and look at the data you entered\n";
			 echo "<br>vcl_id must be between 1 and 20 characters in length</font>\n";
			}
		 else
			{
			$stmt=sprintf("UPDATE osdial_campaigns_list_mix SET status='INACTIVE' WHERE campaign_id='%s';",mres($campaign_id));
			$rslt=mysql_query($stmt, $link);

			$stmt=sprintf("UPDATE osdial_campaigns_list_mix SET status='ACTIVE' WHERE vcl_id='%s' AND campaign_id='%s';",mres($vcl_id),mres($campaign_id));
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
    $SUB=29;
    $ADD=31;	# go to campaign modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=39 display all campaign list mixes
######################
if ($ADD==39)
{
echo "<center><br><font color=$default_text size=+1>CAMPAIGN LIST MIXES</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>CAMPAIGN</td>\n";
echo "    <td>NAME</td>\n";
echo "    <td align=center>LIST MIX</td>\n";
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
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaigns_id_list[$o]'\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaigns_id_list[$o]\">" . mclabel($campaigns_id_list[$o]) . "</a></td>\n";
		echo "    <td>$campaigns_name_list[$o]</td>\n";
		echo "    <td align=center>";

		$stmt=sprintf("SELECT vcl_id FROM osdial_campaigns_list_mix WHERE campaign_id='%s' ORDER BY status,vcl_id;",mres($campaigns_id_list[$o]));
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
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaigns_id_list[$o]\">MODIFY LIST MIX</a></td>\n";
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
