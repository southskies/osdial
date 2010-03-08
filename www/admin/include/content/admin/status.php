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


####  System Statuses




######################
# ADD=221111111111111 adds the new system status to the system
######################

if ($ADD==221111111111111)
{

    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
    $stmt="SELECT count(*) from osdial_campaign_statuses where status='$status';";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    if ($row[0] > 0)
        {echo "<br><font color=red>SYSTEM STATUS NOT ADDED - there is already a campaign-status in the system with this name: $row[0]</font>\n";}
    else
        {
        $stmt="SELECT count(*) from osdial_statuses where status='$status';";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    if ($row[0] > 0)
    {echo "<br><font color=red>SYSTEM STATUS NOT ADDED - there is already a global-status in the system with this name</font>\n";}
    else
			{
    if ( (strlen($status) < 1) or (strlen($status_name) < 2) )
    {
    echo "<br><font color=$default_text>SYSTEM STATUS NOT ADDED - Please go back and look at the data you entered\n";
    echo "<br>status must be between 1 and 8 characters in length\n";
    echo "<br>status name must be between 2 and 30 characters in length</font><br>\n";
				}
    else
    {
    echo "<br><B><font color=$default_text>SYSTEM STATUS ADDED: $status_name - $status</font></B>\n";

				$stmt="INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) values('$status','$status_name','$selectable','$human_answered','$category');";
				$rslt=mysql_query($stmt, $link);

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0)
					{
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW SYSTEM STATUS   |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
					}
				}
			}
		}
$ADD=321111111111111;
}


######################
# ADD=421111111111111 modify/delete system status in the system
######################

if ($ADD==421111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	if (ereg('delete',$stage))
		{
		if ( (strlen($status) < 1) or (preg_match("/^B$|^NA$|^DNC$|^NA$|^DROP$|^INCALL$|^QUEUE$|^NEW$/i",$status)) )
			{
			 echo "<br><font color=red>SYSTEM STATUS NOT DELETED - Please go back and look at the data you entered\n";
			 echo "<br>the system status cannot be a reserved status: B,NA,DNC,NA,DROP,INCALL,QUEUE,NEW\n";
			 echo "<br>the system status needs to be at least 1 characters in length</font><br>\n";
			}
		else
			{
			echo "<br><B><font color=$default_text>SYSTEM STATUS DELETED: $status</font></B>\n";

			$stmt="DELETE FROM osdial_statuses where status='$status';";
			$rslt=mysql_query($stmt, $link);

			$stmtA="DELETE FROM osdial_campaign_hotkeys where status='$status';";
			$rslt=mysql_query($stmtA, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DELETE SYSTEM STATUS  |$PHP_AUTH_USER|$ip|$stmt|$stmtA|\n");
				fclose($fp);
				}
			}
		}
	if (ereg('modify',$stage))
		{
		if ( (strlen($status) < 1) or (strlen($status_name) < 2) )
			{
			 echo "<br><font color=red>SYSTEM STATUS NOT MODIFIED - Please go back and look at the data you entered\n";
			 echo "<br>the system status needs to be at least 1 characters in length\n";
			 echo "<br>the system status name needs to be at least 1 characters in length</font>\n";
			}
		else
			{
			echo "<br><B><font color=$default_text>SYSTEM STATUS MODIFIED: $status</font></B>\n";

			$stmt="UPDATE osdial_statuses SET status_name='$status_name',selectable='$selectable',human_answered='$human_answered',category='$category' where status='$status';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY SYSTEM STATUS  |$PHP_AUTH_USER|$ip|$stmt|\n");
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
$ADD=321111111111111;	# go to system settings modification form below
}


######################
# ADD=321111111111111 modify osdial system statuses
######################

if ($ADD==321111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<br><center>\n";
	echo "<font color=$default_text size=4>$t1 SYSTEM-WIDE STATUSES &nbsp; $NWB#osdial_statuses$NWE</font><br><br>\n";
	echo "<table width=800 cellspacing=1 bgcolor=grey>\n";
	echo "  <tr class=tabheader>\n";
    echo "    <td align=center>STATUS</td>\n";
	echo "    <td align=center>DESCRIPTION</td>\n";
	echo "    <td>SELECTABLE</td>\n";
	echo "    <td>HUMAN&nbsp;ANSWER</td>\n";
	echo "    <td align=center>CATEGORY</td>\n";
	echo "    <td align=center colspan=2>ACTIONS</td>\n";
    echo "  </tr>\n";

	##### get status category listings for dynamic pulldown
	$stmt="SELECT vsc_id,vsc_name from osdial_status_categories order by vsc_id desc";
	$rslt=mysql_query($stmt, $link);
	$cats_to_print = mysql_num_rows($rslt);
	$cats_list="";

	$o=0;
	while ($cats_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$cats_list .= "<option value=\"$rowx[0]\">$rowx[0] - " . substr($rowx[1],0,20) . "</option>\n";
		$catsname_list["$rowx[0]"] = substr($rowx[1],0,20);
		$o++;
		}


	$stmt="SELECT * from osdial_statuses order by status;";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($statuses_to_print > $o) 
		{
		$rowx=mysql_fetch_row($rslt);
		$AScategory = $rowx[4];
		$o++;

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}

        echo "  <form action=$PHP_SELF method=POST>\n";
		echo "  <input type=hidden name=ADD value=421111111111111>\n";
		echo "  <input type=hidden name=stage value=modify>\n";
		echo "  <input type=hidden name=status value=\"$rowx[0]\">\n";
		echo "  <tr $bgcolor class=\"row font1\">\n";
        echo "    <td><b>$rowx[0]</b></td>\n";
		echo "    <td align=center class=tabinput><input type=text name=status_name size=20 maxlength=30 value=\"$rowx[1]\"></td>\n";
		echo "    <td align=center class=tabinput><select size=1 name=selectable><option>Y</option><option>N</option><option selected>$rowx[2]</option></select></td>\n";
		echo "    <td align=center class=tabinput><select size=1 name=human_answered><option>Y</option><option>N</option><option selected>$rowx[3]</option></select></td>\n";
		echo "    <td align=center class=tabinput><select size=1 name=category>$cats_list<option selected value=\"$AScategory\">$AScategory - $catsname_list[$AScategory]</option></select></td>\n";
		echo "    <td align=center nowrap>\n";
		
		if (preg_match("/^B$|^NA$|^DNC$|^NA$|^DROP$|^INCALL$|^QUEUE$|^NEW$/i",$rowx[0])) {
			echo "      <del>DELETE</del>\n";
		} else {
			echo "      <a href=\"$PHP_SELF?ADD=421111111111111&status=$rowx[0]&stage=delete\">DELETE</a>\n";
		}
		echo "      </font>\n";
        echo "    </td>\n";
		echo "    <td align=center nowrap class=tabbutton1><input type=submit name=submit value=MODIFY></td>\n";
        echo "  </tr>\n";
        echo "  </form>\n";
	}

    echo "  <form action=$PHP_SELF method=POST>\n";
	echo "  <input type=hidden name=ADD value=221111111111111>\n";
	echo "  <tr class=tabfooter>\n";
    echo "    <td align=center class=tabinput><input size=7 maxlength=6 name=status></td>\n";
    echo "    <td align=center class=tabinput><input size=30 maxlength=30 name=status_name></td>\n";
    echo "    <td align=center class=tabinput><select size=1 name=selectable><option>Y</option><option>N</option></select></td>\n";
    echo "    <td align=center class=tabinput><select size=1 name=human_answered><option>Y</option><option>N</option></select></td>\n";
	echo "    <td align=center class=tabinput><select size=1 name=category>$cats_list</select></td>\n";
    echo "    <td align=center colspan=2 class=tabbutton1><input type=submit name=submit value=ADD></td>\n";
    echo "  </tr>\n";
    echo "  </form>\n";
	echo "</table>\n";

	#echo "<option selected value=\"$AScategory\">$AScategory - $catsname_list[$AScategory]</option>\n";
	#echo "<input type=submit name=submit value=ADD><BR></font>\n";


	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


#### Status Categories


######################
# ADD=231111111111111 adds the new status category to the system
######################

if ($ADD==231111111111111)
{

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	$stmt="SELECT count(*) from osdial_status_categories where vsc_id='$vsc_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>STATUS CATEGORY NOT ADDED - there is already a status category in the system with this ID: $row[0]</font>\n";}
	else
		{
		 if ( (strlen($vsc_id) < 2) or (strlen($vsc_id) > 20) or (strlen($vsc_name) < 2) )
			{
			 echo "<br><font color=red>STATUS CATEGORY NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>ID must be between 2 and 20 characters in length\n";
			 echo "<br>name name must be between 2 and 50 characters in length</font><br>\n";
			}
		 else
			{
			echo "<br><B><font color=$default_text>STATUS CATEGORY ADDED: $vsc_id - $vsc_name</font></B>\n";

			$stmt="SELECT count(*) from osdial_status_categories where tovdad_display='Y' and vsc_id NOT IN('$vsc_id');";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ( ($row[0] > 3) and (ereg('Y',$tovdad_display)) )
				{
				$tovdad_display = 'N';
				echo "<br><B><font color=red>ERROR: There are already 4 Status Categories set to Time On $t1 Display</font></B>\n";
				}

			$stmt="INSERT INTO osdial_status_categories (vsc_id,vsc_name,vsc_description,tovdad_display) values('$vsc_id','$vsc_name','$vsc_description','$tovdad_display');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW STATUS CATEGORY |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=331111111111111;
}


######################
# ADD=431111111111111 modify/delete status category in the system
######################

if ($ADD==431111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($vsc_id) < 2)  or (preg_match("/^UNDEFINED$/i",$vsc_id)) )
		{
		 echo "<br><font color=red>STATUS CATEGORY NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>the status category cannot be a reserved category: UNDEFINED\n";
		 echo "<br>the status category needs to be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		if (ereg('delete',$stage))
			{
			echo "<br><B><font color=$default_text>STATUS CATEGORY DELETED: $vsc_id</font></B>\n";

			$stmt="DELETE FROM osdial_status_categories where vsc_id='$vsc_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DELETE STATUS CATEGORY|$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		if (ereg('modify',$stage))
			{
			echo "<br><B><font color=$default_text>STATUS CATEGORY MODIFIED: $vsc_id</font></B>\n";

			$stmt="SELECT count(*) from osdial_status_categories where tovdad_display='Y' and vsc_id NOT IN('$vsc_id');";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ( ($row[0] > 3) and (ereg('Y',$tovdad_display)) )
				{
				$tovdad_display = 'N';
				echo "<br><B><font color=red>ERROR: There are already 4 Status Categories set to Time On $t1 Display</font></B>\n";
				}

			$stmt="UPDATE osdial_status_categories SET vsc_name='$vsc_name',vsc_description='$vsc_description',tovdad_display='$tovdad_display' where vsc_id='$vsc_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|MODIFY STATUS CATEGORY|$PHP_AUTH_USER|$ip|$stmt|\n");
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
$ADD=331111111111111;	# go to system settings modification form below
}


######################
# ADD=331111111111111 modify osdial status categories
######################

if ($ADD==331111111111111)
{
	if ($LOGmodify_servers==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<br>\n";
	echo "<center><font size=4 color=$default_text>$t1 STATUS CATEGORIES &nbsp; $NWB#osdial_status_categories$NWE</font></center><br>\n";
	echo "<table width=800 cellspacing=1 bgcolor=grey>\n";
	echo "  <tr class=tabheader>\n";
    echo "    <td align=center>CATEGORY</td>\n";
    echo "    <td align=center>NAME</td>\n";
    echo "    <td align=center>DESCRIPTION</td>\n";
    echo "    <td align=center>ON&nbsp;REALTIME</td>\n";
    echo "    <td align=center colspan=2>ACTIONS</td>\n";
    echo "  </tr>\n";

		$stmt="SELECT * from osdial_status_categories order by vsc_id;";
		$rslt=mysql_query($stmt, $link);
		$statuses_to_print = mysql_num_rows($rslt);
		$o=0;
		while ($statuses_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);

			$Avsc_id[$o] = $rowx[0];
			$Avsc_name[$o] = $rowx[1];
			$Avsc_description[$o] = $rowx[2];
			$Atovdad_display[$o] = $rowx[3];

			$o++;
			}
		$p=0;
		while ($o > $p)
			{
			if (eregi("1$|3$|5$|7$|9$", $p))
				{$bgcolor='bgcolor='.$oddrows;} 
			else
				{$bgcolor='bgcolor='.$evenrows;}

			$CATstatuses='';
			$stmt="SELECT status from osdial_statuses where category='$Avsc_id[$p]' order by status;";
			$rslt=mysql_query($stmt, $link);
			$statuses_to_print = mysql_num_rows($rslt);
			$q=0;
			while ($statuses_to_print > $q) 
				{
				$rowx=mysql_fetch_row($rslt);
				$CATstatuses.=" $rowx[0]";
				$q++;
				}
			$stmt="SELECT status from osdial_campaign_statuses where category='$Avsc_id[$p]' order by status;";
			$rslt=mysql_query($stmt, $link);
			$statuses_to_print = mysql_num_rows($rslt);
			$q=0;
			while ($statuses_to_print > $q) 
				{
				$rowx=mysql_fetch_row($rslt);
				$CATstatuses.=" $rowx[0]";
				$q++;
				}

            echo "  <form action=$PHP_SELF method=POST>\n";
			echo "  <input type=hidden name=ADD value=431111111111111>\n";
			echo "  <input type=hidden name=stage value=modify>\n";
			echo "  <input type=hidden name=vsc_id value=\"$Avsc_id[$p]\">\n";
			echo "  <tr $bgcolor class=\"row font1\" title=\"Statuses in Category: $CATstatuses\">\n";
            echo "    <td><b>$Avsc_id[$p]</b></td>\n";
			echo "    <td align=center class=tabinput><input type=text name=vsc_name size=20 maxlength=50 value=\"$Avsc_name[$p]\"></td>\n";
			echo "    <td align=center class=tabinput><input type=text name=vsc_description size=40 maxlength=255 value=\"$Avsc_description[$p]\"></td>\n";
			echo "    <td align=center class=tabinput><select size=1 name=tovdad_display><option>Y</option><option>N</option><option selected>$Atovdad_display[$p]</option></select></td>\n";
			echo "    <td align=center><font size=1><a href=\"$PHP_SELF?ADD=431111111111111&vsc_id=$Avsc_id[$p]&stage=delete\">DELETE</a></font></td>\n";
			echo "    <td align=center class=tabbutton1><input type=submit name=submit value=MODIFY></td>\n";
			echo "  </tr>\n";
            echo "  </form>\n";

			$p++;
			}

    echo "  <form action=$PHP_SELF method=POST>\n";
	echo "  <input type=hidden name=ADD value=231111111111111>\n";
    echo "  <tr class=tabfooter>\n";
    echo "    <td align=center class=tabinput><input type=text name=vsc_id size=15 maxlength=20></td>\n";
    echo "    <td align=center class=tabinput><input type=text name=vsc_name size=20 maxlength=50></td>\n";
    echo "    <td align=center class=tabinput><input type=text name=vsc_description size=40 maxlength=255></td>\n";
    echo "    <td align=center class=tabinput><select size=1 name=tovdad_display><option>N</option><option>Y</option></select></td>\n";
    echo "    <td align=center colspan=2 class=tabbutton1><input type=submit name=submit value=ADD></td>\n";
    echo "  </tr>\n";
    echo "  </form>\n";
	echo "</table>\n";

	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}



?>
