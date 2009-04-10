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
# 090410-1140 - Added custom2 field
# 090410-1145 - Escaped lead loading variables.


//if ($ADD=='') { $ADD=122 }

######################
# ADD=111 display the ADD NEW LIST FORM SCREEN
######################

if ($ADD==111) {
	if ($LOGmodify_lists==1)	{
		echo "<TABLE align=center><TR><TD>\n";
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
		
		echo "<center><br><font color=navy size=+1>ADD A NEW LIST</font><form action=$PHP_SELF method=POST><br><br>\n";
		echo "<input type=hidden name=ADD value=211>\n";
		echo "<TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>List ID: </td><td align=left><input type=text name=list_id size=8 maxlength=8> (digits only)$NWB#osdial_lists-list_id$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>List Description: </td><td align=left><input type=text name=list_description size=30 maxlength=255>$NWB#osdial_lists-list_description$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
		
			$stmt="SELECT campaign_id,campaign_name from osdial_campaigns order by campaign_id";
			$rslt=mysql_query($stmt, $link);
			$campaigns_to_print = mysql_num_rows($rslt);
			$campaigns_list='';
		
			$o=0;
			while ($campaigns_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				$o++;
			}
		echo "$campaigns_list";
		echo "<option SELECTED>$campaign_id</option>\n";
		echo "</select>$NWB#osdial_lists-campaign_id$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_lists-active$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
		echo "</TABLE></center>\n";
	} else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
		exit;
	}
}


######################
# ADD=112 admin_search_lead.php
######################

if ($ADD==112) {

	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<center><br><font color=navy size=+1>SEARCH FOR A LEAD</font>\n";
	
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
	
	$STARTtime = date("U");
	$TODAY = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");
	
	
	$stmt="SELECT count(*) from osdial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_leads='1';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];
	
	if ($WeBRooTWritablE > 0) {
		$fp = fopen ("./project_auth_entries.txt", "a");
	}
	
	$date = date("r");
	$ip = getenv("REMOTE_ADDR");
	$browser = getenv("HTTP_USER_AGENT");
	
	if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
		Header("WWW-Authenticate: Basic realm=\"OSDial-Administrator\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
	} else {
	
		if($auth>0) {
			$office_no=strtoupper($PHP_AUTH_USER);
			$password=strtoupper($PHP_AUTH_PW);
				$stmt="SELECT full_name,modify_leads from osdial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$LOGfullname				=$row[0];
				$LOGmodify_leads			=$row[1];
	
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "OSDial|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
				fclose($fp);
			}
		} else {
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "OSDial|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
				fclose($fp);
			}
		}
	}
	
	if (isset($_GET["last_name"]))				{$last_name=$_GET["last_name"];}
		elseif (isset($_POST["last_name"]))		{$last_name=$_POST["last_name"];}
	if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
		elseif (isset($_POST["first_name"]))		{$first_name=$_POST["first_name"];}
	
	
	if ( (!$vendor_id) and (!$phone)  and (!$lead_id) and (!$last_name) and (!$first_name) ) {
		echo "<style type=text/css> content {vertical-align:center}</style>";
		echo "\n<br><br><center>\n";
		echo "<TABLE width=$section_width cellspacing=0 bgcolor=#C1D6DF>\n";
		echo "<tr><td colspan=2>\n";
		echo "<form method=post name=search action=\"$PHP_SELF\">\n";
		echo "<input type=hidden name=ADD value=112>\n";
		echo "<input type=hidden name=DB value=\"$DB\">\n";
		echo "<br><center><font color=navy>Enter one of the following</font></center></td>";
		echo "</tr>";
		echo "<tr>\n";
		echo "	<td align=right width=50%>Custom 2:&nbsp;</td>";
		echo "	<td width=50%><input type=text name=vendor_id size=10 maxlength=10> (Aka Vendor Lead Code)</td>";
		echo "</tr>";
		echo "<tr> \n";
		echo "	<td align=right>Home Phone:&nbsp;</td>";
		echo "	<td align=left><input type=text name=phone size=10 maxlength=10></td>";
		echo "</tr>";
		echo "<tr> \n";
		echo "	<td align=right>Last, First Name:&nbsp;</td>";
		echo "	<td align=left><input type=text name=last_name size=10 maxlength=20><input type=text name=first_name size=10 maxlength=20></td>";
		echo "</tr>\n";
		echo "<tr> \n";
		echo "	<td align=right>Lead ID:&nbsp;</td>";
		echo "	<td align=left><input type=text name=lead_id size=10 maxlength=10></td>";
		echo "</tr>\n";
		echo "<tr>";
		echo "<th colspan=2><center><br><input type=submit name=submit value=SUBMIT></b></center><br></th>\n";
		echo "</form>\n";
		echo "</tr>";
		echo "</table>\n";

	/*
	echo "<tr bgcolor=#C1D6DF><td align=right>List ID: </td><td align=left><input type=text name=list_id size=8 maxlength=8> (digits only)$NWB#osdial_lists-list_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
	*/

	
	} else {
		
		if ($last_name and $first_name) {
			$stmt="SELECT * from osdial_list where last_name LIKE '" . mysql_real_escape_string($last_name) . "%' AND first_name LIKE '" . mysql_real_escape_string($first_name) . "%' order by modify_date desc limit 1000";
			//$stmt="SELECT * from osdial_list where last_name='" . mysql_real_escape_string($last_name) . "' and first_name='" . mysql_real_escape_string($first_name) . "' order by modify_date desc limit 1000";
		} else {
			if ($last_name) {
				$stmt="SELECT * from osdial_list where last_name='" . mysql_real_escape_string($last_name) . "' order by modify_date desc limit 1000";
			} else {
				if ($vendor_id) {
					$stmt="SELECT * from osdial_list where vendor_lead_code='" . mysql_real_escape_string($vendor_id) . "' order by modify_date desc limit 1000";
				} else {
					if ($phone) {
						$stmt="SELECT * from osdial_list where phone_number='" . mysql_real_escape_string($phone) . "' order by modify_date desc limit 1000";
					} else {
						if ($lead_id) {
							$stmt="SELECT * from osdial_list where lead_id='" . mysql_real_escape_string($lead_id) . "' order by modify_date desc limit 1000";
						} else {
							print "ERROR: You must search for something!";
							exit;
						}
					}
				}
			}
		} 
		
		
		if ($DB) {
			echo "\n\n$stmt\n\n";
		}
		
		$rslt=mysql_query($stmt, $link);
		$results_to_print = mysql_num_rows($rslt);
		if ($results_to_print < 1) {
			//echo date("l F j, Y G:i:s A");
			echo "<br><br><br><center>\n";
			echo "<font size=3 color=navy>The item(s) you search for were not found.<br><br>\n";
			//echo "You can click on \"Browser Back\" and double check the information you entered.</font>\n";
			echo "<a href='admin.php?ADD=112'>Search Again</a>";
			echo "</center>\n";
		} else {
			echo "<p<font color=navy size=+1>Found:&nbsp;$results_to_print</font></b></p>";
			echo "<font size=1>";
			echo "<TABLE BGCOLOR=WHITE CELLPADDING=1 CELLSPACING=0>\n";
			echo "<TR BGCOLOR=#716A5B>\n";
			echo "<TD ALIGN=LEFT><FONT COLOR=WHITE size=2>#</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Lead ID</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Status</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Vendor ID</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Last Agent</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>List ID</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Phone</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Name</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>City</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Custom 1</FONT>&nbsp;&nbsp;&nbsp;</TD>\n";
			echo "<TD ALIGN=CENTER><FONT COLOR=WHITE size=2>Last Call</FONT></TD>\n";
			echo "</TR>\n";
			$o=0;
			while ($results_to_print > $o) {
				$row=mysql_fetch_row($rslt);
				$o++;
				if (eregi("1$|3$|5$|7$|9$", $o)) {
					$bgcolor='bgcolor="#CBDCE0"';
				} else {
					$bgcolor='bgcolor="#C1D6DB"';
				}
				echo "<TR $bgcolor>\n";
				echo "<TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$o</FONT></TD>\n";
                echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1><a href=\"admin.php?ADD=999999&SUB=3&iframe=admin_modify_lead.php?lead_id=$row[0]\">$row[0]</a></FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[3]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[5]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[4]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[7]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[11]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[13] $row[15]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[19]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[28]</FONT></TD>\n";
				echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$row[2]</FONT></TD>\n";
				echo "</TR>\n";
			}
			echo "</TABLE>\n";
		}
	}
}



######################
# ADD=121 display the ADD NUMBER TO DNC FORM SCREEN and add a new number
######################

if ($ADD==121)
{
echo "<TABLE align=center><TR><TD align=center>\n";
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

if (strlen($phone_number) > 2) {
	$stmt="SELECT count(*) from osdial_dnc where phone_number='$phone_number';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0) {
		echo "<br>DNC NOT ADDED - This phone number is already in the Do Not Call List: $phone_number<BR><BR>\n";
	} else {
		$stmt="INSERT INTO osdial_dnc (phone_number) values('$phone_number');";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B>DNC ADDED: $phone_number</B><BR><BR>\n";

		### LOG INSERTION TO LOG FILE ###
		if ($WeBRooTWritablE > 0) {
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|ADD A NEW DNC NUMBER|$PHP_AUTH_USER|$ip|'$phone_number'|\n");
			fclose($fp);
		}
	}
}

echo "<br><font color=navy size=+1>ADD A NUMBER TO THE DNC LIST</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=121>\n";
//echo "<center>";
echo "<TABLE width=$section_width cellspacing=3>\n";
echo "<tr bgcolor=#C1D6DF><td align=right>Phone Number: </td><td align=left><input type=text name=phone_number size=14 maxlength=12> (digits only)$NWB#osdial_list-dnc$NWE</td></tr>\n";
echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
echo "</TABLE></center>\n";

}



######################
# ADD=122 new_listloader_superL.php
######################

if ($ADD==122) {
	
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	
	echo "<center><br><font color=navy size=+1>LOAD NEW LEADS</font><br><br>\n";
	
	
	$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
	$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
	$PHP_SELF=$_SERVER['PHP_SELF'];
	$leadfile=$_FILES["leadfile"];
	$LF_orig = $_FILES['leadfile']['name'];
	$LF_path = $_FILES['leadfile']['tmp_name'];
	if (isset($_GET["ADD"]))   {$ADD=$_GET["ADD"];}
		elseif (isset($_POST["ADD"])) {$ADD=$_POST["ADD"];}
	if (isset($_GET["submit_file"]))				{$submit_file=$_GET["submit_file"];}
		elseif (isset($_POST["submit_file"]))		{$submit_file=$_POST["submit_file"];}
	if (isset($_GET["leadfile_name"]))				{$leadfile_name=$_GET["leadfile_name"];}
		elseif (isset($_POST["leadfile_name"]))		{$leadfile_name=$_POST["leadfile_name"];}
	if (isset($_FILES["leadfile"]))				{$leadfile_name=$_FILES["leadfile"]['name'];}
	if (isset($_GET["file_layout"]))				{$file_layout=$_GET["file_layout"];}
		elseif (isset($_POST["file_layout"]))		{$file_layout=$_POST["file_layout"];}
	if (isset($_GET["OK_to_process"]))				{$OK_to_process=$_GET["OK_to_process"];}
		elseif (isset($_POST["OK_to_process"]))		{$OK_to_process=$_POST["OK_to_process"];}
	if (isset($_GET["vendor_lead_code_field"]))				{$vendor_lead_code_field=$_GET["vendor_lead_code_field"];}
		elseif (isset($_POST["vendor_lead_code_field"]))		{$vendor_lead_code_field=$_POST["vendor_lead_code_field"];}
	if (isset($_GET["source_id_field"]))				{$source_id_field=$_GET["source_id_field"];}
		elseif (isset($_POST["source_id_field"]))		{$source_id_field=$_POST["source_id_field"];}
	if (isset($_GET["list_id_field"]))				{$list_id_field=$_GET["list_id_field"];}
		elseif (isset($_POST["list_id_field"]))		{$list_id_field=$_POST["list_id_field"];}
	if (isset($_GET["phone_code_field"]))				{$phone_code_field=$_GET["phone_code_field"];}
		elseif (isset($_POST["phone_code_field"]))		{$phone_code_field=$_POST["phone_code_field"];}
	if (isset($_GET["phone_number_field"]))				{$phone_number_field=$_GET["phone_number_field"];}
		elseif (isset($_POST["phone_number_field"]))		{$phone_number_field=$_POST["phone_number_field"];}
	if (isset($_GET["title_field"]))				{$title_field=$_GET["title_field"];}
		elseif (isset($_POST["title_field"]))		{$title_field=$_POST["title_field"];}
	if (isset($_GET["first_name_field"]))				{$first_name_field=$_GET["first_name_field"];}
		elseif (isset($_POST["first_name_field"]))		{$first_name_field=$_POST["first_name_field"];}
	if (isset($_GET["middle_initial_field"]))				{$middle_initial_field=$_GET["middle_initial_field"];}
		elseif (isset($_POST["middle_initial_field"]))		{$middle_initial_field=$_POST["middle_initial_field"];}
	if (isset($_GET["last_name_field"]))				{$last_name_field=$_GET["last_name_field"];}
		elseif (isset($_POST["last_name_field"]))		{$last_name_field=$_POST["last_name_field"];}
	if (isset($_GET["address1_field"]))				{$address1_field=$_GET["address1_field"];}
		elseif (isset($_POST["address1_field"]))		{$address1_field=$_POST["address1_field"];}
	if (isset($_GET["address2_field"]))				{$address2_field=$_GET["address2_field"];}
		elseif (isset($_POST["address2_field"]))		{$address2_field=$_POST["address2_field"];}
	if (isset($_GET["address3_field"]))				{$address3_field=$_GET["address3_field"];}
		elseif (isset($_POST["address3_field"]))		{$address3_field=$_POST["address3_field"];}
	if (isset($_GET["city_field"]))				{$city_field=$_GET["city_field"];}
		elseif (isset($_POST["city_field"]))		{$city_field=$_POST["city_field"];}
	if (isset($_GET["state_field"]))				{$state_field=$_GET["state_field"];}
		elseif (isset($_POST["state_field"]))		{$state_field=$_POST["state_field"];}
	if (isset($_GET["province_field"]))				{$province_field=$_GET["province_field"];}
		elseif (isset($_POST["province_field"]))		{$province_field=$_POST["province_field"];}
	if (isset($_GET["postal_code_field"]))				{$postal_code_field=$_GET["postal_code_field"];}
		elseif (isset($_POST["postal_code_field"]))		{$postal_code_field=$_POST["postal_code_field"];}
	if (isset($_GET["country_code_field"]))				{$country_code_field=$_GET["country_code_field"];}
		elseif (isset($_POST["country_code_field"]))		{$country_code_field=$_POST["country_code_field"];}
	if (isset($_GET["gender_field"]))				{$gender_field=$_GET["gender_field"];}
		elseif (isset($_POST["gender_field"]))		{$gender_field=$_POST["gender_field"];}
	if (isset($_GET["date_of_birth_field"]))				{$date_of_birth_field=$_GET["date_of_birth_field"];}
		elseif (isset($_POST["date_of_birth_field"]))		{$date_of_birth_field=$_POST["date_of_birth_field"];}
	if (isset($_GET["alt_phone_field"]))				{$alt_phone_field=$_GET["alt_phone_field"];}
		elseif (isset($_POST["alt_phone_field"]))		{$alt_phone_field=$_POST["alt_phone_field"];}
	if (isset($_GET["email_field"]))				{$email_field=$_GET["email_field"];}
		elseif (isset($_POST["email_field"]))		{$email_field=$_POST["email_field"];}
	if (isset($_GET["custom1_field"]))				{$custom1_field=$_GET["custom1_field"];}
		elseif (isset($_POST["custom1_field"]))		{$custom1_field=$_POST["custom1_field"];}
	if (isset($_GET["custom2_field"]))				{$custom2_field=$_GET["custom2_field"];}
		elseif (isset($_POST["custom2_field"]))		{$custom2_field=$_POST["custom2_field"];}
	if (isset($_GET["comments_field"]))				{$comments_field=$_GET["comments_field"];}
		elseif (isset($_POST["comments_field"]))		{$comments_field=$_POST["comments_field"];}
	if (isset($_GET["list_id_override"]))				{$list_id_override=$_GET["list_id_override"];}
		elseif (isset($_POST["list_id_override"]))		{$list_id_override=$_POST["list_id_override"];}
		$list_id_override = (preg_replace("/\D/","",$list_id_override));
	if (isset($_GET["lead_file"]))					{$lead_file=$_GET["lead_file"];}
		elseif (isset($_POST["lead_file"]))			{$lead_file=$_POST["lead_file"];}
	if (isset($_GET["dupcheck"]))				{$dupcheck=$_GET["dupcheck"];}
		elseif (isset($_POST["dupcheck"]))		{$dupcheck=$_POST["dupcheck"];}
	if (isset($_GET["postalgmt"]))				{$postalgmt=$_GET["postalgmt"];}
		elseif (isset($_POST["postalgmt"]))		{$postalgmt=$_POST["postalgmt"];}
	if (isset($_GET["phone_code_override"]))			{$phone_code_override=$_GET["phone_code_override"];}
		elseif (isset($_POST["phone_code_override"]))	{$phone_code_override=$_POST["phone_code_override"];}
	
	$phone_code_override = (preg_replace("/\D/","",$phone_code_override));
	$Imported=get_variable('Imported');
	
	# $country_field=$_GET["country_field"];					if (!$country_field) {$country_field=$_POST["country_field"];}
	
	
	#############################################
	##### START SYSTEM_SETTINGS LOOKUP #####
	$stmt = "SELECT use_non_latin FROM system_settings;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$qm_conf_ct = mysql_num_rows($rslt);
	$i=0;
	while ($i < $qm_conf_ct)
		{
		$row=mysql_fetch_row($rslt);
		$non_latin =					$row[0];
		$i++;
		}
	##### END SETTINGS LOOKUP #####
	###########################################
	
	if ($non_latin < 1) {
		$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
		$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
		$list_id_override = ereg_replace("[^0-9]","",$list_id_override);
	}
	
	$STARTtime = date("U");
	$TODAY = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");
	$FILE_datetime = $STARTtime;

	$stmt="SELECT count(*) from osdial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7;";
	if ($DB) {
		echo "|$stmt|\n";
	}
	
	if ($non_latin > 0) {
		$rslt=mysql_query("SET NAMES 'UTF8'");
	}
		
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

	if ($WeBRooTWritablE > 0) {
		$fp = fopen ("./project_auth_entries.txt", "a");
	}
	$date = date("r");
	$ip = getenv("REMOTE_ADDR");
	$browser = getenv("HTTP_USER_AGENT");

	if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
		Header("WWW-Authenticate: Basic realm=\"OSDial-LEAD-LOADER\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
	} else {

		if($auth>0) {
			$office_no=strtoupper($PHP_AUTH_USER);
			$password=strtoupper($PHP_AUTH_PW);
			$stmt="SELECT load_leads from osdial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$LOGload_leads				=$row[0];
	
			if ($LOGload_leads < 1)
				{
				echo "You do not have permissions to load leads\n";
				exit;
				}
			if ($WeBRooTWritablE > 0) 
				{
				fwrite ($fp, "LIST_LOAD|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
				fclose($fp);
				}
		} else {
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "LIST_LOAD|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
				fclose($fp);
			}
		}
	}

	
	$script_name = getenv("SCRIPT_NAME");
	$server_name = getenv("SERVER_NAME");
	$server_port = getenv("SERVER_PORT");
	if (eregi("443",$server_port)) {
		$HTTPprotocol = 'https://';
	} else {
		$HTTPprotocol = 'http://';
	}
	$admDIR = "$HTTPprotocol$server_name$script_name";
	//$admDIR = eregi_replace('new_listloader_superL.php','',$admDIR);  // debug - original line
	//$admDIR = eregi_replace('admin.php?ADD=122','',$admDIR); // debug
	$admSCR = 'admin.php?ADD=122'; //    debug -       admin.php is already in admDIR 
	$NWB = " &nbsp; <a href=\"javascript:openNewWindow('$admDIR$admSCR?ADD=99999";
	$NWE = "')\"><IMG SRC=\"help.gif\" WIDTH=20 HEIGHT=20 BORDER=0 ALT=\"HELP\" ALIGN=TOP></A>";
	$secX = date("U");
	$hour = date("H");
	$min = date("i");
	$sec = date("s");
	$mon = date("m");
	$mday = date("d");
	$year = date("Y");
	$isdst = date("I");
	$Shour = date("H");
	$Smin = date("i");
	$Ssec = date("s");
	$Smon = date("m");
	$Smday = date("d");
	$Syear = date("Y");
	$pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
	$inSD = $pulldate0;
	$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

	### Grab Server GMT value from the database
	$stmt="SELECT local_gmt FROM servers where server_ip = '$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$gmt_recs = mysql_num_rows($rslt);
	if ($gmt_recs > 0) {
		$row=mysql_fetch_row($rslt);
		$DBSERVER_GMT		=		"$row[0]";
		if (strlen($DBSERVER_GMT)>0)	{
			$SERVER_GMT = $DBSERVER_GMT;
		}
		
		if ($isdst) {
			$SERVER_GMT++;
		} 
		
	} else {
		$SERVER_GMT = date("O");
		$SERVER_GMT = eregi_replace("\+","",$SERVER_GMT);
		$SERVER_GMT = ($SERVER_GMT + 0);
		$SERVER_GMT = ($SERVER_GMT / 100);
	}

	$LOCAL_GMT_OFF = $SERVER_GMT;
	$LOCAL_GMT_OFF_STD = $SERVER_GMT;
	
	#if ($DB) {print "SEED TIME  $secX      :   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF\n";}
	
	
	echo "<!-- VERSION: $version     BUILD: $build -->\n";
	echo "<!-- SEED TIME  $secX:   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF  DST: $isdst -->\n";

	function macfontfix($fontsize) {
		$browser = getenv("HTTP_USER_AGENT");
		$pctype = explode("(", $browser);
		if (ereg("Mac",$pctype[1])) {
			/* Browser is a Mac.  If not Netscape 6, raise fonts */
			$blownbrowser = explode('/', $browser);
			$ver = explode(' ', $blownbrowser[1]);
			$ver = $ver[0];
			if ($ver >= 5.0) {
				return $fontsize; 
			} else { 
				return ($fontsize+2); 
			}
		} else {
			return $fontsize;	/* Browser is not a Mac - don't touch fonts */ 
		}
	}
	
	echo "<style type=\"text/css\">\n
	<!--\n
	.title {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(18)."pt}\n
	.standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt}\n
	.small_standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(8)."pt}\n
	.tiny_standard {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(6)."pt}\n
	.standard_bold {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold}\n
	.standard_header {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(14)."pt; font-weight: bold}\n
	.standard_bold_highlight {  font-family: Arial, Helvetica, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold; color: white; BACKGROUND-COLOR: black}\n
	.standard_bold_blue_highlight {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; BACKGROUND-COLOR: blue}\n
	A.employee_standard {  font-family: garamond, sans-serif; font-size: ".macfontfix(10)."pt; font-style: normal; font-variant: normal; font-weight: bold; text-decoration: none}\n
	.employee_standard {  font-family: garamond, sans-serif; font-size: ".macfontfix(10)."pt; font-weight: bold}\n
	.employee_title {  font-family: Garamond, sans-serif; font-size: ".macfontfix(14)."pt; font-weight: bold}\n
	\\\\-->\n
	</style>\n";

?>


	<form action=<?=$PHP_SELF ?> method=post onSubmit="ParseFileName()" enctype="multipart/form-data">
		<input type=hidden name='ADD' value='122'>
		<input type=hidden name='leadfile_name' value="<?=$leadfile_name ?>">
		<input type=hidden name='Imported' value=''>
<? 
		if ($file_layout!="custom" or $leadfile_name == "") {
            if ($phone_code_override == "") { $phone_code_override = "1";}
			$Imported++;
?>
			<table align=center width="700" border=0 cellpadding=5 cellspacing=0 bgcolor=#C1D6DF>
				<tr>
					<td align=right width="35%"><B><font face="arial, helvetica" size=2>Load leads from this file:</font></B></td>
					<td align=left width="65%"><input type=file name="leadfile" value="<?=$leadfile ?>"> <? echo "$NWB#osdial_list_loader$NWE"; ?></td>
				</tr>
				<tr>
                    <td align=right width="25%"><font face="arial, helvetica" size=2>List ID: </font></td>
                    <td align=left width="75%"><font face="arial, helvetica" size=1>
                        <select size=1 name=list_id_override>
                            <?
                            $stmt="SELECT list_id,list_name FROM osdial_lists;";
                            $rslt=mysql_query($stmt, $link);
                            $lrows = mysql_num_rows($rslt);
                            if ($lrows > 0) {
                                $count = 0;
                                if ($list_id_override < 1) {
                                    echo "            <option value=\"1\">[ PLEASE SELECT A LIST ]</option>\n";
                                }
                                while ($count < $lrows) {
                                    $row=mysql_fetch_row($rslt);
                                    $lsel = '';
                                    if ($row[0] == $list_id_override) { $lsel = ' selected'; }
                                    echo '            <option value="' . $row[0] . '"' . $lsel . '>' . $row[0] . ' - ' . $row[1] . "</option>\n";
                                    $count++;
                                }
                            } else {
                                echo "            <option value=\"1\">[ ERROR, YOU MUST FIRST ADD A LIST]</option>\n";
                            }
                        ?>
                        </select>
                        <!-- <input type=text value="<?=$list_id_override ?>" name='list_id_override' size=10 maxlength=8>-->
                    </td>
				</tr>
				<tr>
					<td align=right width="25%"><font face="arial, helvetica" size=2>Phone Code: </font></td>
					<td align=left width="75%"><font face="arial, helvetica" size=1><input type=text value="<?=$phone_code_override ?>" name='phone_code_override' size=8 maxlength=6> (numbers only or leave blank for values in the file)</td>
				</tr>
				<tr>
					<td align=right><B><font face="arial, helvetica" size=2>File layout to use:</font></B></td>
					<td align=left><font face="arial, helvetica" size=2><input type=radio name="file_layout" value="custom" checked>Custom Layout&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name="file_layout" value="standard">Predefined Layout</td>
				</tr>
				<tr>
					<td align=right width="25%"><font face="arial, helvetica" size=2>Lead Duplicate Check: </font></td>
					<td align=left width="75%"><font face="arial, helvetica" size=1><select size=1 name=dupcheck>
					<option value="NONE">NO DUPLICATE CHECK</option>
					<option selected value="DUPLIST">CHECK FOR DUPLICATES BY PHONE IN LIST ID</option>
					<option value="DUPCAMP">CHECK FOR DUPLICATES BY PHONE IN ALL CAMPAIGN LISTS</option>
					<option value="DUPSYS">CHECK FOR DUPLICATES BY PHONE IN ENTIRE SYSTEM</option>
					</select></td>
				</tr>
				<tr>
					<td align=right width="25%"><font face="arial, helvetica" size=2>Lead Time Zone Lookup: </font></td>
					<td align=left width="75%"><font face="arial, helvetica" size=1><select size=1 name=postalgmt><option selected value="AREA">COUNTRY CODE AND AREA CODE ONLY</option><option value="POSTAL">POSTAL CODE FIRST</option></select></td>
				</tr>
				<tr>
					<!-- td align=center colspan=2><input type=submit value="SUBMIT" name='submit_file'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onClick="javascript:document.location='new_listloader_superL.php'" value="START OVER" name='reload_page'></td -->
					<td align=center colspan=2><input type=submit value="SUBMIT" name='submit_file'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onClick="javascript:document.location='admin.php?ADD=122'" value="START OVER" name='reload_page'>
					</td>
				</tr>
			</table>
<? 
		}
		
		if ($OK_to_process) {
			print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true;document.forms[0].list_id_override.disabled=true;document.forms[0].phone_code_override.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
			flush();
			$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
	
			if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) {
				# copy($leadfile, "./osdial_temp_file.txt");
				$file=fopen("$lead_file", "r");
				if ($WeBRooTWritablE > 0) {
					$stmt_file=fopen("listloader_stmts.txt", "w");
				}
				$buffer=fgets($file, 4096);
				$tab_count=substr_count($buffer, "\t");
				$pipe_count=substr_count($buffer, "|");
	
				if ($tab_count>$pipe_count) {
					$delimiter="\t";  $delim_name="tab";
				} else {
					$delimiter="|";  $delim_name="pipe";
				}
				$field_check=explode($delimiter, $buffer);
	
				if (count($field_check)>=5) {
					flush();
					$file=fopen("$lead_file", "r");
					print "<center><font size=3 color='navy'><B>Processing $delim_name-delimited file...\n";
	
					if (strlen($list_id_override)>0) {
						print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
					}
		
					if (strlen($phone_code_override)>0) 
						{
						print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
						}
		
						while (!feof($file)) {
							$record++;
							$buffer=rtrim(fgets($file, 4096));
							$buffer=stripslashes($buffer);
		
							if (strlen($buffer)>0) {
								$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));
		
								$pulldate=date("Y-m-d H:i:s");
								$entry_date =			"$pulldate";
								$modify_date =			"";
								$status =				"NEW";
								$user =                 "";
								$vendor_lead_code =		mysql_real_escape_string($row[$vendor_lead_code_field]);
								$source_code =			mysql_real_escape_string($row[$source_id_field]);
								$source_id=             $source_code;
								$list_id =				mysql_real_escape_string($row[$list_id_field]);
								$gmt_offset =			'0';
								$called_since_last_reset='N';
								$phone_code =			eregi_replace("[^0-9]", "", $row[$phone_code_field]);
								$phone_number =			eregi_replace("[^0-9]", "", $row[$phone_number_field]);
								$USarea = 			    substr($phone_number, 0, 3);
								$title =				mysql_real_escape_string($row[$title_field]);
								$first_name =			mysql_real_escape_string($row[$first_name_field]);
								$middle_initial =		mysql_real_escape_string($row[$middle_initial_field]);
								$last_name =			mysql_real_escape_string($row[$last_name_field]);
								$address1 =				mysql_real_escape_string($row[$address1_field]);
								$address2 =				mysql_real_escape_string($row[$address2_field]);
								$address3 =				mysql_real_escape_string($row[$address3_field]);
								$city =                 mysql_real_escape_string($row[$city_field]);
								$state =				mysql_real_escape_string($row[$state_field]);
								$province =				mysql_real_escape_string($row[$province_field]);
								$postal_code =			mysql_real_escape_string($row[$postal_code_field]);
								$country_code =			mysql_real_escape_string($row[$country_code_field]);
								$gender =				mysql_real_escape_string($row[$gender_field]);
								$date_of_birth =		mysql_real_escape_string($row[$date_of_birth_field]);
								$alt_phone =			eregi_replace("[^0-9]", "", $row[$alt_phone_field]);
								$email =				mysql_real_escape_string($row[$email_field]);
								$custom1 =		        mysql_real_escape_string($row[$custom1_field]);
								$comments =				mysql_real_escape_string(trim($row[$comments_field]));
								$custom2 =		        mysql_real_escape_string($row[$custom2_field]);
		
								if (strlen($list_id_override)>0) 
									{
								#	print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
									$list_id = $list_id_override;
									}
								if (strlen($phone_code_override)>0) 
									{
									$phone_code = $phone_code_override;
									}
		
								##### Check for duplicate phone numbers in osdial_list table for all lists in a campaign #####
								if (eregi("DUPCAMP",$dupcheck))
									{
										$dup_lead=0;
										$dup_lists='';
									$stmt="select campaign_id from osdial_lists where list_id='$list_id';";
									$rslt=mysql_query($stmt, $link);
									$ci_recs = mysql_num_rows($rslt);
									if ($ci_recs > 0)
										{
										$row=mysql_fetch_row($rslt);
										$dup_camp =			$row[0];
		
										$stmt="select list_id from osdial_lists where campaign_id='$dup_camp';";
										$rslt=mysql_query($stmt, $link);
										$li_recs = mysql_num_rows($rslt);
										if ($li_recs > 0)
											{
											$L=0;
											while ($li_recs > $L)
												{
												$row=mysql_fetch_row($rslt);
												$dup_lists .=	"'$row[0]',";
												$L++;
												}
											$dup_lists = eregi_replace(",$",'',$dup_lists);
		
											$stmt="select list_id from osdial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
											$rslt=mysql_query($stmt, $link);
											$pc_recs = mysql_num_rows($rslt);
											if ($pc_recs > 0)
												{
												$dup_lead=1;
												$row=mysql_fetch_row($rslt);
												$dup_lead_list =	$row[0];
												}
											if ($dup_lead < 1)
												{
												if (eregi("$phone_number$US$list_id",$phone_list))
													{$dup_lead++; $dup++;}
												}
											}
										}
									}
		
								##### Check for duplicate phone numbers in osdial_list table entire database #####
								if (eregi("DUPSYS",$dupcheck))
									{
									$dup_lead=0;
									$stmt="select list_id from osdial_list where phone_number='$phone_number';";
									$rslt=mysql_query($stmt, $link);
									$pc_recs = mysql_num_rows($rslt);
									if ($pc_recs > 0)
										{
										$dup_lead=1;
										$row=mysql_fetch_row($rslt);
										$dup_lead_list =	$row[0];
										}
									if ($dup_lead < 1)
										{
										if (eregi("$phone_number$US$list_id",$phone_list))
											{$dup_lead++; $dup++;}
										}
									}
		
								##### Check for duplicate phone numbers in osdial_list table for one list_id #####
								if (eregi("DUPLIST",$dupcheck))
									{
									$dup_lead=0;
									$stmt="select count(*) from osdial_list where phone_number='$phone_number' and list_id='$list_id';";
									$rslt=mysql_query($stmt, $link);
									$pc_recs = mysql_num_rows($rslt);
									if ($pc_recs > 0)
										{
										$row=mysql_fetch_row($rslt);
										$dup_lead =			$row[0];
										$dup_lead_list =	$list_id;
										}
									if ($dup_lead < 1)
										{
										if (eregi("$phone_number$US$list_id",$phone_list))
											{$dup_lead++; $dup++;}
										}
									}
		
								if ( (strlen($phone_number)>6) and ($dup_lead<1) )
									{
									if (strlen($phone_code)<1) {$phone_code = '1';}
		
									$US='_';
									$phone_list .= "$phone_number$US$list_id|";
		
									$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
		
									if ($multi_insert_counter > 8) {
										### insert good deal into pending_transactions table ###
										$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2');";
										$rslt=mysql_query($stmtZ, $link);
										if ($WeBRooTWritablE > 0) 
											{fwrite($stmt_file, $stmtZ."\r\n");}
										$multistmt='';
										$multi_insert_counter=0;
		
									} else {
										$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2'),";
										$multi_insert_counter++;
									}
		
									$good++;
								} else {
									if ($bad < 1000000) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead  $dup_lead_list</font><b>\n";}
									$bad++;
								}
								$total++;
								if ($total%100==0) {
									print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
									usleep(1000);
									flush();
								}
							}
						}
						if ($multi_insert_counter!=0) {
							$stmtZ = "INSERT INTO osdial_list values".substr($multistmt, 0, -1).";";
							mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0) 
								{fwrite($stmt_file, $stmtZ."\r\n");}
						}
						if ($bad >'0') {
							$FC="<font color='red'>";
						} else {
							$FC="<font color='black'>";
						}
						print "<BR><BR>Done</B><br><br> GOOD: <font color='black'>$good</font> &nbsp; &nbsp; &nbsp; BAD: $FC $bad </font> &nbsp; &nbsp; &nbsp; TOTAL: $FC $total</font></center>";
						$Imported++;
						
					} else {
						print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: The file does not have the required number of fields to process it.</B></font></center>";
					}
				} else if (!eregi(".csv", $leadfile_name)) {
					# copy($leadfile, "./osdial_temp_file.xls");
					$file=fopen("$lead_file", "r");
		
					print "<center><font size=3 color='navy'><B>Processing Excel file... \n";
					if (strlen($list_id_override)>0) 
					{
					print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>\n";
					}
					if (strlen($phone_code_override)>0) 
					{
					print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>\n";
					}
				# print "|$WeBServeRRooT/admin/listloader_super.pl $vendor_lead_code_field,$source_id_field,$list_id_field,$phone_code_field,$phone_number_field,$title_field,$first_name_field,$middle_initial_field,$last_name_field,$address1_field,$address2_field,$address3_field,$city_field,$state_field,$province_field,$postal_code_field,$country_code_field,$gender_field,$date_of_birth_field,$alt_phone_field,$email_field,$custom1_field,$comments_field, --forcelistid=$list_id_override --lead_file=$lead_file|";
					$dupcheckCLI=''; $postalgmtCLI='';
					if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
					if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
					if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
					if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
					passthru("$WeBServeRRooT/admin/listloader_super.pl $vendor_lead_code_field,$source_id_field,$list_id_field,$phone_code_field,$phone_number_field,$title_field,$first_name_field,$middle_initial_field,$last_name_field,$address1_field,$address2_field,$address3_field,$city_field,$state_field,$province_field,$postal_code_field,$country_code_field,$gender_field,$date_of_birth_field,$alt_phone_field,$email_field,$custom1_field,$comments_field, --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");
				} else {
					# copy($leadfile, "./osdial_temp_file.csv");
					$file=fopen("$lead_file", "r");
		
					if ($WeBRooTWritablE > 0)
						{$stmt_file=fopen("$WeBServeRRooT/admin/listloader_stmts.txt", "w");}
					
					print "<center><font size=3 color='navy'><B>Processing CSV file... \n";
					if (strlen($list_id_override)>0) 
						{
						print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
						}
		
					if (strlen($phone_code_override)>0) 
						{
						print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
						}
		
					while($row=fgetcsv($file, 1000, ",")) {
		
						$pulldate=date("Y-m-d H:i:s");
						$entry_date =			"$pulldate";
						$modify_date =			"";
						$status =				"NEW";
						$user =                 "";
						$vendor_lead_code =		mysql_real_escape_string($row[$vendor_lead_code_field]);
						$source_code =			mysql_real_escape_string($row[$source_id_field]);
						$source_id=             $source_code;
						$list_id =				mysql_real_escape_string($row[$list_id_field]);
						$gmt_offset =			'0';
						$called_since_last_reset='N';
						$phone_code =			eregi_replace("[^0-9]", "", $row[$phone_code_field]);
						$phone_number =			eregi_replace("[^0-9]", "", $row[$phone_number_field]);
						$USarea = 			    substr($phone_number, 0, 3);
						$title =				mysql_real_escape_string($row[$title_field]);
						$first_name =			mysql_real_escape_string($row[$first_name_field]);
						$middle_initial =		mysql_real_escape_string($row[$middle_initial_field]);
						$last_name =			mysql_real_escape_string($row[$last_name_field]);
						$address1 =				mysql_real_escape_string($row[$address1_field]);
						$address2 =				mysql_real_escape_string($row[$address2_field]);
						$address3 =				mysql_real_escape_string($row[$address3_field]);
						$city =                 mysql_real_escape_string($row[$city_field]);
						$state =				mysql_real_escape_string($row[$state_field]);
						$province =				mysql_real_escape_string($row[$province_field]);
						$postal_code =			mysql_real_escape_string($row[$postal_code_field]);
						$country_code =			mysql_real_escape_string($row[$country_code_field]);
						$gender =				mysql_real_escape_string($row[$gender_field]);
						$date_of_birth =		mysql_real_escape_string($row[$date_of_birth_field]);
						$alt_phone =			eregi_replace("[^0-9]", "", $row[$alt_phone_field]);
						$email =				mysql_real_escape_string($row[$email_field]);
						$custom1 =		        mysql_real_escape_string($row[$custom1_field]);
						$comments =				mysql_real_escape_string(trim($row[$comments_field]));
						$custom2 =		        mysql_real_escape_string($row[$custom2_field]);
		
							if (strlen($list_id_override)>0) 
								{
								$list_id = $list_id_override;
								}
							if (strlen($phone_code_override)>0) 
								{
								$phone_code = $phone_code_override;
								}
		
							##### Check for duplicate phone numbers in osdial_list table for all lists in a campaign #####
							if (eregi("DUPCAMP",$dupcheck))
								{
									$dup_lead=0;
									$dup_lists='';
								$stmt="select campaign_id from osdial_lists where list_id='$list_id';";
								$rslt=mysql_query($stmt, $link);
								$ci_recs = mysql_num_rows($rslt);
								if ($ci_recs > 0)
									{
									$row=mysql_fetch_row($rslt);
									$dup_camp =			$row[0];
		
									$stmt="select list_id from osdial_lists where campaign_id='$dup_camp';";
									$rslt=mysql_query($stmt, $link);
									$li_recs = mysql_num_rows($rslt);
									if ($li_recs > 0)
										{
										$L=0;
										while ($li_recs > $L)
											{
											$row=mysql_fetch_row($rslt);
											$dup_lists .=	"'$row[0]',";
											$L++;
											}
										$dup_lists = eregi_replace(",$",'',$dup_lists);
		
										$stmt="select list_id from osdial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
										$rslt=mysql_query($stmt, $link);
										$pc_recs = mysql_num_rows($rslt);
										if ($pc_recs > 0)
											{
											$dup_lead=1;
											$row=mysql_fetch_row($rslt);
											$dup_lead_list =	$row[0];
											}
										if ($dup_lead < 1)
											{
											if (eregi("$phone_number$US$list_id",$phone_list))
												{$dup_lead++; $dup++;}
											}
										}
									}
								}
					
							##### Check for duplicate phone numbers in osdial_list table entire database #####
							if (eregi("DUPSYS",$dupcheck))
								{
								$dup_lead=0;
								$stmt="select list_id from osdial_list where phone_number='$phone_number';";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
		
							##### Check for duplicate phone numbers in osdial_list table for one list_id #####
							if (eregi("DUPLIST",$dupcheck))
								{
								$dup_lead=0;
								$stmt="select count(*) from osdial_list where phone_number='$phone_number' and list_id='$list_id';";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0)
									{
									$row=mysql_fetch_row($rslt);
									$dup_lead =			$row[0];
									}
								if ($dup_lead < 1)
									{
									if (eregi("$phone_number$US$list_id",$phone_list))
										{$dup_lead++; $dup++;}
									}
								}
		
							if ( (strlen($phone_number)>6) and ($dup_lead<1) )
								{
								if (strlen($phone_code)<1) {$phone_code = '1';}
		
								$US='_';
								$phone_list .= "$phone_number$US$list_id|";
		
								$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
		
		
							if ($multi_insert_counter > 8) {
								### insert good deal into pending_transactions table ###
								$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2');";
								$rslt=mysql_query($stmtZ, $link);
								if ($WeBRooTWritablE > 0) 
									{fwrite($stmt_file, $stmtZ."\r\n");}
								$multistmt='';
								$multi_insert_counter=0;
		
							} else {
								$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2'),";
								$multi_insert_counter++;
							}
		
							$good++;
						} else {
							if ($bad < 1000000) {print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead</font><b>\n";}
							$bad++;
						}
						$total++;
						if ($total%100==0) {
							print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
							usleep(1000);
							flush();
						}
					}
					if ($multi_insert_counter!=0) {
						$stmtZ = "INSERT INTO osdial_list values".substr($multistmt, 0, -1).";";
						mysql_query($stmtZ, $link);
						if ($WeBRooTWritablE > 0) 
							{fwrite($stmt_file, $stmtZ."\r\n");}
					}
					if ($bad >'0') {
						$FC="<font color='red'>";
					} else {
						$FC="<font color='black'>";
					}
					print "<BR><BR>Done</B><br><br> GOOD: <font color='black'>$good</font> &nbsp; &nbsp; &nbsp; BAD: $FC $bad</font> &nbsp; &nbsp; &nbsp; TOTAL: $FC $total</font></center>";
					$Imported++;
					
				}
				print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
			} 
		
		if ($leadfile_name) {
			# Look for list id before importing leads
			if ($list_id_override) {
				$stmt="select list_id from osdial_lists where list_id='$list_id_override';";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$ListID=$row[0];
				echo "- ListID=$ListID -";
				if ($ListID == "") {
					echo "<br><br>You are trying to load leads into a non existent list $list_id_override<br>";
				}
			}
			
			
			$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
			if ($file_layout=="standard") {
		
				print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script>";
				flush();
		
				if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) {
		
				if ($WeBRooTWritablE > 0) {
					copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.txt");
					$lead_file = "./osdial_temp_file.txt";
				} else {
					copy($LF_path, "/tmp/osdial_temp_file.txt");
					$lead_file = "/tmp/osdial_temp_file.txt";
				}
				$file=fopen("$lead_file", "r");
				if ($WeBRooTWritablE > 0)
					{$stmt_file=fopen("$WeBServeRRooT/admin/listloader_stmts.txt", "w");}
		
				$buffer=fgets($file, 4096);
				$tab_count=substr_count($buffer, "\t");
				$pipe_count=substr_count($buffer, "|");
		
				if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
				$field_check=explode($delimiter, $buffer);
		
				if (count($field_check)>=5) {
					flush();
					$file=fopen("$lead_file", "r");
					$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
					print "<center><font size=3 color='navy'><B>Processing $delim_name-delimited file... ($tab_count|$pipe_count)\n";
					if (strlen($list_id_override)>0) {
						print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
					}
					if (strlen($phone_code_override)>0) {
						print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>\n";
					}
					while (!feof($file)) {
						$record++;
						$buffer=rtrim(fgets($file, 4096));
						$buffer=stripslashes($buffer);
		
						if (strlen($buffer)>0) {
							$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));
		
							$pulldate=date("Y-m-d H:i:s");
							$entry_date =			"$pulldate";
							$modify_date =			"";
							$status =				"NEW";
							$user ="";
							$vendor_lead_code =		$row[0];
							$source_code =			$row[1];
							$source_id=$source_code;
							$list_id =				$row[2];
							$gmt_offset =			'0';
							$called_since_last_reset='N';
							$phone_code =			eregi_replace("[^0-9]", "", $row[3]);
							$phone_number =			eregi_replace("[^0-9]", "", $row[4]);
							$USarea = 			substr($phone_number, 0, 3);
							$title =				$row[5];
							$first_name =			$row[6];
							$middle_initial =		$row[7];
							$last_name =			$row[8];
							$address1 =				$row[9];
							$address2 =				$row[10];
							$address3 =				$row[11];
							$city =$row[12];
							$state =				$row[13];
							$province =				$row[14];
							$postal_code =			$row[15];
							$country_code =			$row[16];
							$gender =				$row[17];
							$date_of_birth =		$row[18];
							$alt_phone =			eregi_replace("[^0-9]", "", $row[19]);
							$email =				$row[20];
							$custom1 =		$row[21];
							$comments =				trim($row[22]);
		
							if (strlen($list_id_override)>0) {
								$list_id = $list_id_override;
							}
							if (strlen($phone_code_override)>0) {
								$phone_code = $phone_code_override;
							}
		
							##### Check for duplicate phone numbers in osdial_list table for all lists in a campaign #####
							if (eregi("DUPCAMP",$dupcheck)) {
								$dup_lead=0;
								$dup_lists='';
								$stmt="select campaign_id from osdial_lists where list_id='$list_id';";
								$rslt=mysql_query($stmt, $link);
								$ci_recs = mysql_num_rows($rslt);
								if ($ci_recs > 0) {
									$row=mysql_fetch_row($rslt);
									$dup_camp =			$row[0];
		
									$stmt="select list_id from osdial_lists where campaign_id='$dup_camp';";
									$rslt=mysql_query($stmt, $link);
									$li_recs = mysql_num_rows($rslt);
									if ($li_recs > 0) {
										$L=0;
										while ($li_recs > $L) {
											$row=mysql_fetch_row($rslt);
											$dup_lists .=	"'$row[0]',";
											$L++;
										}
										$dup_lists = eregi_replace(",$",'',$dup_lists);
		
										$stmt="select list_id from osdial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
										$rslt=mysql_query($stmt, $link);
										$pc_recs = mysql_num_rows($rslt);
										if ($pc_recs > 0) {
											$dup_lead=1;
											$row=mysql_fetch_row($rslt);
											$dup_lead_list =	$row[0];
										}
										if ($dup_lead < 1) {
											if (eregi("$phone_number$US$list_id",$phone_list)) {
												$dup_lead++; $dup++;
											}
										}
									}
								}
							}
		
							##### Check for duplicate phone numbers in osdial_list table entire database #####
							if (eregi("DUPSYS",$dupcheck)) {
								$dup_lead=0;
								$stmt="select list_id from osdial_list where phone_number='$phone_number';";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0) {
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
								}
								if ($dup_lead < 1) {
									if (eregi("$phone_number$US$list_id",$phone_list)) {
										$dup_lead++; $dup++;
									}
								}
							}
		
							##### Check for duplicate phone numbers in osdial_list table for one list_id #####
							if (eregi("DUPLIST",$dupcheck)) {
								$dup_lead=0;
								$stmt="select count(*) from osdial_list where phone_number='$phone_number' and list_id='$list_id';";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0) {
									$row=mysql_fetch_row($rslt);
									$dup_lead =			$row[0];
								}
								if ($dup_lead < 1) {
									if (eregi("$phone_number$US$list_id",$phone_list)) {
										$dup_lead++; $dup++;
									}
								}
							}
		
							if ( (strlen($phone_number)>6) and ($dup_lead<1) ) {
								if (strlen($phone_code)<1) {
									$phone_code = '1';
								}
		
								$US='_';
								$phone_list .= "$phone_number$US$list_id|";
	
								$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
		
		
								if ($multi_insert_counter > 8) {
									### insert good deal into pending_transactions table ###
									$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2');";
									$rslt=mysql_query($stmtZ, $link);
									if ($WeBRooTWritablE > 0) 
										{fwrite($stmt_file, $stmtZ."\r\n");}
									$multistmt='';
									$multi_insert_counter=0;
		
								} else {
									$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2'),";
									$multi_insert_counter++;
								}
		
								$good++;
							} else {
								if ($bad < 1000000) {
									print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead</font><b>\n";
								}
								$bad++;
							}
							$total++;
							if ($total%100==0) {
								print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
								usleep(1000);
								flush();
							}
						}
					}
					if ($multi_insert_counter!=0) {
						$stmtZ = "INSERT INTO osdial_list values".substr($multistmt, 0, -1).";";
						mysql_query($stmtZ, $link);
						if ($WeBRooTWritablE > 0) {
							fwrite($stmt_file, $stmtZ."\r\n");
						}
					}
					if ($bad >'0') {
						$FC="<font color='red'>";
					} else {
						$FC="<font color='black'>";
					}
					print "<BR><BR>Done</B><br><br> GOOD: <font color='black'>$good</font> &nbsp; &nbsp; &nbsp; BAD: $FC $bad</font> &nbsp; &nbsp; &nbsp; TOTAL: $FC $total</font></center>";
					$Imported++;
		
				} else {
					print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: The file does not have the required number of fields to process it.</B></font></center>";
				}
			} else if (!eregi(".csv", $leadfile_name)) {
				if ($WeBRooTWritablE > 0) {
					copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.xls");
					$lead_file = "$WeBServeRRooT/admin/osdial_temp_file.xls";
				} else {
					copy($LF_path, "/tmp/osdial_temp_file.xls");
					$lead_file = "/tmp/osdial_temp_file.xls";
				}
				$file=fopen("$lead_file", "r");
		
			#	echo "|$WeBServeRRooT/admin/listloader.pl --forcelistid=$list_id_override --lead-file=$lead_file|";
				$dupcheckCLI=''; $postalgmtCLI='';
				if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
				if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
				if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
				if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
				passthru("$WeBServeRRooT/admin/listloader.pl --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file  $postalgmtCLI $dupcheckCLI");
			
			} else {
				if ($WeBRooTWritablE > 0) {
					copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.csv");
					$lead_file = "$WeBServeRRooT/admin/osdial_temp_file.csv";
				} else {
					copy($LF_path, "/tmp/osdial_temp_file.csv");
					$lead_file = "/tmp/osdial_temp_file.csv";
				}
				$file=fopen("$lead_file", "r");
				if ($WeBRooTWritablE > 0) {
					$stmt_file=fopen("$WeBServeRRooT/admin/listloader_stmts.txt", "w");
				}
				
				print "<center><font size=3 color='navy'><B>Processing CSV file... \n";
		
				if (strlen($list_id_override)>0) {
					print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
				}
				if (strlen($phone_code_override)>0) {
					print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
				}
		
				while ($row=fgetcsv($file, 1000, ",")) {
					$pulldate=date("Y-m-d H:i:s");
					$entry_date =			"$pulldate";
					$modify_date =			"";
					$status =				"NEW";
					$user ="";
					$vendor_lead_code =		$row[0];
					$source_code =			$row[1];
					$source_id=$source_code;
					$list_id =				$row[2];
					$gmt_offset =			'0';
					$called_since_last_reset='N';
					$phone_code =			eregi_replace("[^0-9]", "", $row[3]);
					$phone_number =			eregi_replace("[^0-9]", "", $row[4]);
					$USarea = 			substr($phone_number, 0, 3);
					$title =				$row[5];
					$first_name =			$row[6];
					$middle_initial =		$row[7];
					$last_name =			$row[8];
					$address1 =				$row[9];
					$address2 =				$row[10];
					$address3 =				$row[11];
					$city =$row[12];
					$state =				$row[13];
					$province =				$row[14];
					$postal_code =			$row[15];
					$country_code =			$row[16];
					$gender =				$row[17];
					$date_of_birth =		$row[18];
					$alt_phone =			eregi_replace("[^0-9]", "", $row[19]);
					$email =				$row[20];
					$custom1 =		$row[21];
					$comments =				trim($row[22]);
	
					if (strlen($list_id_override)>0) {
						$list_id = $list_id_override;
					}
					if (strlen($phone_code_override)>0) {
						$phone_code = $phone_code_override;
					}
	
					##### Check for duplicate phone numbers in osdial_list table for all lists in a campaign #####
					if (eregi("DUPCAMP",$dupcheck)) {
						$dup_lead=0;
						$dup_lists='';
						$stmt="select campaign_id from osdial_lists where list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$ci_recs = mysql_num_rows($rslt);
						if ($ci_recs > 0) {
							$row=mysql_fetch_row($rslt);
							$dup_camp =			$row[0];
	
							$stmt="select list_id from osdial_lists where campaign_id='$dup_camp';";
							$rslt=mysql_query($stmt, $link);
							$li_recs = mysql_num_rows($rslt);
							if ($li_recs > 0) {
								$L=0;
								while ($li_recs > $L) {
									$row=mysql_fetch_row($rslt);
									$dup_lists .=	"'$row[0]',";
									$L++;
								}
								
								$dup_lists = eregi_replace(",$",'',$dup_lists);
	
								$stmt="select list_id from osdial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
								$rslt=mysql_query($stmt, $link);
								$pc_recs = mysql_num_rows($rslt);
								if ($pc_recs > 0) {
									$dup_lead=1;
									$row=mysql_fetch_row($rslt);
									$dup_lead_list =	$row[0];
								}
								if ($dup_lead < 1) {
									if (eregi("$phone_number$US$list_id",$phone_list)) {
										$dup_lead++; $dup++;
									}
								}
							}
						}
					}
		
					##### Check for duplicate phone numbers in osdial_list table entire database #####
					if (eregi("DUPSYS",$dupcheck)) {
						$dup_lead=0;
						$stmt="select list_id from osdial_list where phone_number='$phone_number';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0) {
							$dup_lead=1;
							$row=mysql_fetch_row($rslt);
							$dup_lead_list =	$row[0];
						}
						if ($dup_lead < 1) {
							if (eregi("$phone_number$US$list_id",$phone_list)) {
								$dup_lead++; $dup++;
							}
						}
					}
	
					##### Check for duplicate phone numbers in osdial_list table for one list_id #####
					if (eregi("DUPLIST",$dupcheck)) {
						$dup_lead=0;
						$stmt="select count(*) from osdial_list where phone_number='$phone_number' and list_id='$list_id';";
						$rslt=mysql_query($stmt, $link);
						$pc_recs = mysql_num_rows($rslt);
						if ($pc_recs > 0) {
							$row=mysql_fetch_row($rslt);
							$dup_lead =			$row[0];
						}
						if ($dup_lead < 1) {
							if (eregi("$phone_number$US$list_id",$phone_list)) {
								$dup_lead++; $dup++;
							}
						}
					}
	
					if ( (strlen($phone_number)>6) and ($dup_lead<1) ) {
						if (strlen($phone_code)<1) {
							$phone_code = '1';
						}
	
						$US='_';
						$phone_list .= "$phone_number$US$list_id|";
	
						$gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
	
	
						if ($multi_insert_counter > 8) {
							### insert good deal into pending_transactions table ###
							$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2');";
							$rslt=mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0) 
								{fwrite($stmt_file, $stmtZ."\r\n");}
							$multistmt='';
							$multi_insert_counter=0;
		
						} else {
							$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2'),";
							$multi_insert_counter++;
						}
		
						$good++;
						
					} else {
					
						if ($bad < 1000000) {
							print "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$row[0]| DUP: $dup_lead</font><b>\n";
						}
						$bad++;
					}
					$total++;
					if ($total%100==0) {
						print "<script language='JavaScript1.2'>ShowProgress($good, $bad, $total, $dup, $post)</script>";
						usleep(1000);
						flush();
					}
				}
				if ($multi_insert_counter!=0) {
					$stmtZ = "INSERT INTO osdial_list values".substr($multistmt, 0, -1).";";
					mysql_query($stmtZ, $link);
					if ($WeBRooTWritablE > 0) {
						fwrite($stmt_file, $stmtZ."\r\n");
					}
				}
				if ($bad >'0') {
					$FC="<font color='red'>";
				} else {
					$FC="<font color='black'>";
				}
				print "<BR><BR>Done</B><br><br> GOOD: <font color='black'>$good</font> &nbsp; &nbsp; &nbsp; BAD: $FC $bad</font> &nbsp; &nbsp; &nbsp; TOTAL: $FC $total</font></center>";
				
			}
			print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
	
		} else {
			print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script><HR>";
			flush();
			print "<table border=0 cellpadding=3 cellspacing=0 width=700 align=center>\r\n";
			print "  <tr bgcolor='#330099'>\r\n";
			print "    <th align=right><font class='standard' color='white'>OSDial Column</font></th>\r\n";
			print "    <th><font class='standard' color='white'>File data</font></th>\r\n";
			print "  </tr>\r\n";
				
			$rslt=mysql_query("select vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, custom1, comments, custom2 from osdial_list limit 1", $link);
			
	
			if (!eregi(".csv", $leadfile_name) && !eregi(".xls", $leadfile_name)) 
				{
				if ($WeBRooTWritablE > 0)
					{
					copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.txt");
					$lead_file = "$WeBServeRRooT/admin/osdial_temp_file.txt";
					}
				else
					{
					copy($LF_path, "/tmp/osdial_temp_file.txt");
					$lead_file = "/tmp/osdial_temp_file.txt";
					}
				$file=fopen("$lead_file", "r");
				if ($WeBRooTWritablE > 0)
					{$stmt_file=fopen("$WeBServeRRooT/admin/listloader_stmts.txt", "w");}
	
				$buffer=fgets($file, 4096);
				$tab_count=substr_count($buffer, "\t");
				$pipe_count=substr_count($buffer, "|");
	
				if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
				$field_check=explode($delimiter, $buffer);
				flush();
				$file=fopen("$lead_file", "r");
				print "<center><font size=3 color='navy'><B>Processing $delim_name-delimited file...\n";
	
				if (strlen($list_id_override)>0) 
					{
					print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
					}
				if (strlen($phone_code_override)>0) 
					{
					print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
					}
				$buffer=rtrim(fgets($file, 4096));
				$buffer=stripslashes($buffer);
				$row=explode($delimiter, eregi_replace("[\'\"]", "", $buffer));
				
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {
	
					if ( (mysql_field_name($rslt, $i)=="list_id" and $list_id_override!="") or (mysql_field_name($rslt, $i)=="phone_code" and $phone_code_override!="") ) {
						print "<!-- skipping " . mysql_field_name($rslt, $i) . " -->\n";
					} else {
						print "  <tr bgcolor=#D9E6FE>\r\n";
						print "    <td align=right><font class=standard>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </font></td>\r\n";
						print "    <td align=center><select name='".mysql_field_name($rslt, $i)."_field'>\r\n";
						print "     <option value='-1'>(none)</option>\r\n";
	
						for ($j=0; $j<count($row); $j++) {
							eregi_replace("\"", "", $row[$j]);
							print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
						}
	
						print "    </select></td>\r\n";
						print "  </tr>\r\n";
					}
	
				}
			} else if (!eregi(".csv", $leadfile_name)) {
				if ($WeBRooTWritablE > 0) {
					copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.xls");
					$lead_file = "$WeBServeRRooT/admin/osdial_temp_file.xls";
				} else {
					copy($LF_path, "/tmp/osidial_temp_file.xls");
					$lead_file = "/tmp/osdial_temp_file.xls";
				}
	
				#	echo "|$WeBServeRRooT/admin/listloader_rowdisplay.pl --lead-file=$lead_file|";
				$dupcheckCLI=''; $postalgmtCLI='';
				if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
				if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
				if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
				if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
				passthru("$WeBServeRRooT/admin/listloader_rowdisplay.pl --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");
			} else {
				if ($WeBRooTWritablE > 0) {
					copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.csv");
					$lead_file = "$WeBServeRRooT/admin/osdial_temp_file.csv";
				} else {
					copy($LF_path, "/tmp/osdial_temp_file.csv");
					$lead_file = "/tmp/osdial_temp_file.csv";
				}
				$file=fopen("$lead_file", "r");
	
				if ($WeBRooTWritablE > 0) {
					$stmt_file=fopen("$WeBServeRRooT/admin/listloader_stmts.txt", "w");
				}
				
				print "<center><font size=3 color='navy'><B>Processing CSV file... \n";
				
				if (strlen($list_id_override)>0) {
					print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
				}
				if (strlen($phone_code_override)>0) {
					print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
				}
	
				$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
				$row=fgetcsv($file, 1000, ",");
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {
					if ( (mysql_field_name($rslt, $i)=="list_id" and $list_id_override!="") or (mysql_field_name($rslt, $i)=="phone_code" and $phone_code_override!="") ) {
						print "<!-- skipping " . mysql_field_name($rslt, $i) . " -->\n";
					} else {
						print "  <tr bgcolor=#D9E6FE>\r\n";
						print "    <td align=right><font class=standard>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </font></td>\r\n";
						print "    <td align=center><select name='".mysql_field_name($rslt, $i)."_field'>\r\n";
						print "     <option value='-1'>(none)</option>\r\n";
	
						for ($j=0; $j<count($row); $j++) {
							eregi_replace("\"", "", $row[$j]);
							print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
						}
	
						print "    </select></td>\r\n";
						print "  </tr>\r\n";
					}
				}
			}
			print "  <tr bgcolor='#330099'>\r\n";
			print "  <input type=hidden name=dupcheck value=\"$dupcheck\">\r\n";
			print "  <input type=hidden name=postalgmt value=\"$postalgmt\">\r\n";
			print "  <input type=hidden name=lead_file value=\"$lead_file\">\r\n";
			print "  <input type=hidden name=list_id_override value=\"$list_id_override\">\r\n";
			print "  <input type=hidden name=phone_code_override value=\"$phone_code_override\">\r\n";
			print "<input type=hidden name=ADD value=122>\n"; // debug -added
			print "    <th colspan=2><input type=submit name='OK_to_process' value='OK TO PROCESS'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button onClick=\"javascript:document.location='admin.php?ADD=122'\" value=\"START OVER\" name='reload_page'></th>\r\n";
			print "  </tr>\r\n";
			print "</table>\r\n";
		}
		print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
	}
				
	echo "</form>\n";
}



######################
# ADD=125 generates test leads to test campaign
######################
if ($ADD==125) {
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
	
	$STARTtime = date("U");
	$TODAY = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");
	
	
	$stmt="SELECT count(*) from osdial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_leads='1';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];
	
	if ($WeBRooTWritablE > 0) {
		$fp = fopen ("./project_auth_entries.txt", "a");
	}
	
	$date = date("r");
	$ip = getenv("REMOTE_ADDR");
	$browser = getenv("HTTP_USER_AGENT");
	
	if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
		Header("WWW-Authenticate: Basic realm=\"OSDial-Administrator\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
	} else {
	
		if($auth>0) {
			$office_no=strtoupper($PHP_AUTH_USER);
			$password=strtoupper($PHP_AUTH_PW);
				$stmt="SELECT full_name,modify_leads from osdial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$LOGfullname				=$row[0];
				$LOGmodify_leads			=$row[1];
	
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "OSDial|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
				fclose($fp);
			}
		} else {
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "OSDial|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
				fclose($fp);
			}
		}
	}
	if ($LOGmodify_lists==1)	{
		echo "<TABLE align=center><TR><TD>\n";
		echo "<center><br><font color=navy size='2'>GENERATE TEST LEADS</font><br>(ONLY works with TEST list 998.)<form action=$PHP_SELF method=POST><br><br>\n";
		echo "<input type=hidden name=ADD value=126>\n";
		echo "<TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Phone Number: </td><td align=left><input type=text name=testphone size=8 maxlength=8> (digits only)$NWB#$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=right>Number of leads: </td><td align=left><input type=text name=testnbr size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
		echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
			echo "</TABLE></center>\n";
	} else {
		echo "<font color=red>You do not have permission to view this page.</font>\n";
		exit;
	}
}


######################
# ADD=126 generates test leads to test campaign
######################
if ($ADD==126) {
	echo "<TABLE align=center>";
	echo "	<tr>";
	echo "		<td>";
	
	$stmt="insert into osdial_list where list_id='998'";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
	
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
}




######################
# ADD=211 adds the new list to the system
######################

if ($ADD==211)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from osdial_lists where list_id='$list_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>LIST NOT ADDED - there is already a list in the system with this ID</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($list_name) < 2)  or ($list_id < 100) or (strlen($list_id) > 8) )
			{
			 echo "<br><font color=red>LIST NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>List ID must be between 2 and 8 characters in length\n";
			 echo "<br>List name must be at least 2 characters in length\n";
			 echo "<br>List ID must be greater than 100</font><br>\n";
			 }
		 else
			{
			echo "<br><B><font color=navy>LIST ADDED: $list_id</font></B>\n";

			$stmt="INSERT INTO osdial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate) values('$list_id','$list_name','$campaign_id','$active','$list_description','$SQLdate');";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW LIST      |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=311;
}


######################
# ADD=411 submit list modifications to the system
######################

if ($ADD==411)
{
	if ($LOGmodify_lists==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($list_name) < 2) or (strlen($campaign_id) < 2) )
		{
		 echo "<br><font color=red>LIST NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>list name must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>LIST MODIFIED: $list_id</font></B>\n";

		$stmt="UPDATE osdial_lists set list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description',list_changedate='$SQLdate' where list_id='$list_id';";
		$rslt=mysql_query($stmt, $link);

		if ($reset_list == 'Y')
			{
			echo "<br><font color=navy>RESETTING LIST-CALLED-STATUS</font>\n";
			$stmt="UPDATE osdial_list set called_since_last_reset='N' where list_id='$list_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG RESET TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|RESET LIST CALLED   |$PHP_AUTH_USER|$ip|list_name='$list_name'|\n");
				fclose($fp);
				}
			}
		if ($campaign_id != "$old_campaign_id")
			{
			echo "<br><font color=navy>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($old_campaign_id)</font>\n";
			$stmt="DELETE from osdial_hopper where list_id='$list_id' and campaign_id='$old_campaign_id';";
			$rslt=mysql_query($stmt, $link);
			}

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY LIST INFO    |$PHP_AUTH_USER|$ip|list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description' where list_id='$list_id'|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=311;	# go to list modification form below
}


######################
# ADD=511 confirmation before deletion of list
######################

if ($ADD==511)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($list_id) < 2) or ($LOGdelete_lists < 1) )
		{
		 echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>List_id be at least 2 characters in length</font>\n";
		}
	 else
		{
      if ($SUB==1) {
        echo "<br><B><font color=navy>LIST AND LEAD DELETION CONFIRMATION: $list_id</B>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=611&SUB=1&list_id=$list_id&CoNfIrM=YES\">Click here to delete list and all of its leads $list_id</a></font><br><br><br>\n";
      } else {
		    echo "<br><B><font color=navy>LIST DELETION CONFIRMATION: $list_id</B>\n";
		    echo "<br><br><a href=\"$PHP_SELF?ADD=611&list_id=$list_id&CoNfIrM=YES\">Click here to delete list $list_id</a></font><br><br><br>\n";
      }
		}

$ADD='311';		# go to campaign modification below
}

######################
# ADD=611 delete list record and all leads within it
######################

if ($ADD==611)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( ( strlen($list_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_lists < 1) )
		{
		 echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>List_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from osdial_lists where list_id='$list_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		echo "<br><font color=navy>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($list_id)</font>\n";
		$stmt="DELETE from osdial_hopper where list_id='$list_id';";
		$rslt=mysql_query($stmt, $link);

    if ($SUB==1) {
		  echo "<br><font color=navy>REMOVING LIST LEADS FROM OSDial_LIST TABLE</font>\n";
		  $stmt="DELETE from osdial_list where list_id='$list_id';";
		  $rslt=mysql_query($stmt, $link);
    }

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING LIST!!!!|$PHP_AUTH_USER|$ip|list_id='$list_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>LIST DELETION COMPLETED: $list_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='100';		# go to lists list
}

######################
# ADD=311 modify list info in the system
######################

if ($ADD==311)
{
	if ($LOGmodify_lists==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from osdial_lists where list_id='$list_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$campaign_id = $row[2];
	$active = $row[3];
	$list_description = $row[4];
	$list_changedate = $row[5];
	$list_lastcalldate = $row[6];

	# grab names of global statuses and statuses in the selected campaign
	$stmt="SELECT * from osdial_statuses order by status";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($statuses_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$statuses_list["$rowx[0]"] = "$rowx[1]";
		$o++;
	}

	$stmt="SELECT * from osdial_campaign_statuses where campaign_id='$campaign_id' order by status";
	$rslt=mysql_query($stmt, $link);
	$Cstatuses_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($Cstatuses_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$statuses_list["$rowx[0]"] = "$rowx[1]";
		$o++;
	}
	# end grab status names


	echo "<center><br><font color=navy size=+1>MODIFY A LIST</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=411>\n";
	echo "<input type=hidden name=list_id value=\"$row[0]\">\n";
	echo "<input type=hidden name=old_campaign_id value=\"$row[2]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List ID: </td><td align=left><b>$row[0]</b>$NWB#osdial_lists-list_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20 value=\"$row[1]\">$NWB#osdial_lists-list_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Description: </td><td align=left><input type=text name=list_description size=30 maxlength=255 value=\"$list_description\">$NWB#osdial_lists-list_description$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right><a href=\"$PHP_SELF?ADD=34&campaign_id=$campaign_id\">Campaign</a>: </td><td align=left><select size=1 name=campaign_id>\n";

	$stmt="SELECT campaign_id,campaign_name from osdial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);
	$campaigns_list='';

	$o=0;
	while ($campaigns_to_print > $o) {
		$rowx=mysql_fetch_row($rslt);
		$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$o++;
	}
	echo "$campaigns_list";
	echo "<option SELECTED>$campaign_id</option>\n";
	echo "</select>$NWB#osdial_lists-campaign_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$active</option></select>$NWB#osdial_lists-active$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Reset Lead-Called-Status for this list: </td><td align=left><select size=1 name=reset_list><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_lists-reset_list$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Change Date: </td><td align=left>$list_changedate &nbsp; $NWB#osdial_lists-list_changedate$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>List Last Call Date: </td><td align=left>$list_lastcalldate &nbsp; $NWB#osdial_lists-list_lastcalldate$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2>";
	echo "<input type=button name=addleads value=\"ADD LEADS\" onclick=\"window.location='admin.php?ADD=122&list_id_override=$row[0]'\">&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<input type=submit name=SUBMIT value=SUBMIT>";
	echo "</td></tr>\n";
	echo "</TABLE></center>\n";

	echo "<center>\n";
	echo "<br><font color=navy size=+1>STATUSES WITHIN THIS LIST</font></b><br><br>\n";
	echo "<TABLE width=500 cellspacing=3>\n";
	echo "<tr><td><font color=navy>STATUS</font></td><td><font color=navy>STATUS NAME</font></td><td><font color=navy>CALLED</font></td><td><font color=navy>NOT CALLED</font></td></tr>\n";

	$leads_in_list = 0;
	$leads_in_list_N = 0;
	$leads_in_list_Y = 0;
	$stmt="SELECT status,called_since_last_reset,count(*) from osdial_list where list_id='$list_id' group by status,called_since_last_reset order by status,called_since_last_reset";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);

	$o=0;
	$lead_list['count'] = 0;
	$lead_list['Y_count'] = 0;
	$lead_list['N_count'] = 0;
	while ($statuses_to_print > $o) 
	{
	    $rowx=mysql_fetch_row($rslt);
	    
	    $lead_list['count'] = ($lead_list['count'] + $rowx[2]);
	    if ($rowx[1] == 'N') 
	    {
		$since_reset = 'N';
		$since_resetX = 'Y';
	    }
	    else 
	    {
		$since_reset = 'Y';
		$since_resetX = 'N';
	    } 
	    $lead_list[$since_reset][$rowx[0]] = ($lead_list[$since_reset][$rowx[0]] + $rowx[2]);
	    $lead_list[$since_reset.'_count'] = ($lead_list[$since_reset.'_count'] + $rowx[2]);
	    #If opposite side is not set, it may not in the future so give it a value of zero
	    if (!isset($lead_list[$since_resetX][$rowx[0]])) 
	    {
		$lead_list[$since_resetX][$rowx[0]]=0;
	    }
	    $o++;
	}
 
	$o=0;
	if ($lead_list['count'] > 0)
	{
		while (list($dispo,) = each($lead_list[$since_reset]))
		{

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		if ($dispo == 'CBHOLD')
			{
			$CLB="<a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">";
			$CLE="</a>";
			}
		else
			{
			$CLB='';
			$CLE='';
			}

		echo "<tr $bgcolor><td><font size=1>$CLB$dispo$CLE</td><td><font size=1>$statuses_list[$dispo]</td><td><font size=1>".$lead_list['Y'][$dispo]."</td><td><font size=1>".$lead_list['N'][$dispo]." </td></tr>\n";
		$o++;
		}
	}

	echo "<tr><td colspan=2><font size=1><font color=navy>SUBTOTALS</font></td><td><font size=1>$lead_list[Y_count]</td><td><font size=1>$lead_list[N_count]</td></tr>\n";
	echo "<tr bgcolor=\"#C1D6DB\"><td><font size=1>TOTAL</td><td colspan=3 align=center><font size=1>$lead_list[count]</td></tr>\n";

	echo "</table></center><br>\n";
	unset($lead_list);


	echo "<center>\n";
	echo "<br><font color=navy size=+1>TIME ZONES WITHIN THIS LIST</font></b><br><br>\n";
	echo "<TABLE width=500 cellspacing=3>\n";
	echo "<tr><td><font color=navy>GMT OFFSET NOW (local time)</font></td><td><font color=navy>CALLED</font></td><td><font color=navy>NOT CALLED</font></td></tr>\n";

	$stmt="SELECT gmt_offset_now,called_since_last_reset,count(*) from osdial_list where list_id='$list_id' group by gmt_offset_now,called_since_last_reset order by gmt_offset_now,called_since_last_reset";
	$rslt=mysql_query($stmt, $link);
	$statuses_to_print = mysql_num_rows($rslt);

	$o=0;
	$plus='+';
	$lead_list['count'] = 0;
	$lead_list['Y_count'] = 0;
	$lead_list['N_count'] = 0;
	while ($statuses_to_print > $o) 
	{
	    $rowx=mysql_fetch_row($rslt);
	    
	    $lead_list['count'] = ($lead_list['count'] + $rowx[2]);
	    if ($rowx[1] == 'N') 
	    {
		$since_reset = 'N';
		$since_resetX = 'Y';
	    }
	    else 
	    {
		$since_reset = 'Y';
		$since_resetX = 'N';
	    } 
	    $lead_list[$since_reset][$rowx[0]] = ($lead_list[$since_reset][$rowx[0]] + $rowx[2]);
	    $lead_list[$since_reset.'_count'] = ($lead_list[$since_reset.'_count'] + $rowx[2]);
	    #If opposite side is not set, it may not in the future so give it a value of zero
	    if (!isset($lead_list[$since_resetX][$rowx[0]])) 
	    {
		$lead_list[$since_resetX][$rowx[0]]=0;
	    }
	    $o++;
	}

	if ($lead_list['count'] > 0)
	{
		while (list($tzone,) = each($lead_list[$since_reset]))
		{
		$LOCALzone=3600 * $tzone;
		$LOCALdate=gmdate("D M Y H:i", time() + $LOCALzone);

		if ($tzone >= 0) {$DISPtzone = "$plus$tzone";}
		else {$DISPtzone = "$tzone";}
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

			echo "<tr $bgcolor><td><font size=1>".$DISPtzone." &nbsp; &nbsp; ($LOCALdate)</td><td><font size=1>".$lead_list['Y'][$tzone]."</td><td><font size=1>".$lead_list['N'][$tzone]."</td></tr>\n";
		}
	}

	echo "<tr><td><font size=1><font color=navy>SUBTOTALS</font></td><td><font size=1>$lead_list[Y_count]</td><td><font size=1>$lead_list[N_count]</td></tr>\n";
	echo "<tr bgcolor=\"#C1D6DB\"><td><font size=1>TOTAL</td><td colspan=2 align=center><font size=1>$lead_list[count]</td></tr>\n";

	echo "</table></center><br>\n";
	unset($lead_list);



	$leads_in_list = 0;
	$leads_in_list_N = 0;
	$leads_in_list_Y = 0;
	$stmt="SELECT status,called_count,count(*) from osdial_list where list_id='$list_id' group by status,called_count order by status,called_count";
	$rslt=mysql_query($stmt, $link);
	$status_called_to_print = mysql_num_rows($rslt);

	$o=0;
	$sts=0;
	$first_row=1;
	$all_called_first=1000;
	$all_called_last=0;
	while ($status_called_to_print > $o) 
	{
	$rowx=mysql_fetch_row($rslt);
	$leads_in_list = ($leads_in_list + $rowx[2]);
	$count_statuses[$o]			= "$rowx[0]";
	$count_called[$o]			= "$rowx[1]";
	$count_count[$o]			= "$rowx[2]";
	$all_called_count[$rowx[1]] = ($all_called_count[$rowx[1]] + $rowx[2]);

	if ( (strlen($status[$sts]) < 1) or ($status[$sts] != "$rowx[0]") )
		{
		if ($first_row) {$first_row=0;}
		else {$sts++;}
		$status[$sts] = "$rowx[0]";
		$status_called_first[$sts] = "$rowx[1]";
		if ($status_called_first[$sts] < $all_called_first) {$all_called_first = $status_called_first[$sts];}
		}
	$leads_in_sts[$sts] = ($leads_in_sts[$sts] + $rowx[2]);
	$status_called_last[$sts] = "$rowx[1]";
	if ($status_called_last[$sts] > $all_called_last) {$all_called_last = $status_called_last[$sts];}

	$o++;
	}




	echo "<center>\n";
	echo "<br><font color=navy size=+1>CALLED COUNTS WITHIN THIS LIST</font></b><br><br>\n";
	echo "<TABLE width=500 cellspacing=1>\n";
	echo "<tr><td align=left><font size=1 color=navy>STATUS</td><td align=center><font size=1 color=navy>STATUS NAME</td>";
	$first = $all_called_first;
	while ($first <= $all_called_last)
		{
		if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='bgcolor="#AFEEEE"';} 
		else{$AB='bgcolor="#E0FFFF"';}
		echo "<td align=center $AB><font size=1>$first</td>";
		$first++;
		}
	echo "<td align=center><font size=1 color=navy>SUBTOTAL</td></tr>\n";

		$sts=0;
		$statuses_called_to_print = count($status);
		while ($statuses_called_to_print > $sts) 
		{
		$Pstatus = $status[$sts];
		if (eregi("1$|3$|5$|7$|9$", $sts))
			{$bgcolor='bgcolor="#CBDCE0"';   $AB='bgcolor="#C1D6DB"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';   $AB='bgcolor="#CBDCE0"';}
	#	echo "$status[$sts]|$status_called_first[$sts]|$status_called_last[$sts]|$leads_in_sts[$sts]|\n";
	#	echo "$status[$sts]|";
		echo "<tr $bgcolor><td><font size=1>$Pstatus</td><td><font size=1>$statuses_list[$Pstatus]</td>";

		$first = $all_called_first;
		while ($first <= $all_called_last)
			{
			if (eregi("1$|3$|5$|7$|9$", $sts))
				{
				if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='bgcolor="#C1D6DB"';} 
				else{$AB='bgcolor="#CBDCE0"';}
				}
			else
				{
				if (eregi("0$|2$|4$|6$|8$", $first)) {$AB='bgcolor="#C1D6DB"';} 
				else{$AB='bgcolor="#CBDCE0"';}
				}

			$called_printed=0;
			$o=0;
			while ($status_called_to_print > $o) 
				{
				if ( ($count_statuses[$o] == "$Pstatus") and ($count_called[$o] == "$first") )
					{
					$called_printed++;
					echo "<td $AB><font size=1> $count_count[$o]</td>";
					}


				$o++;
				}
			if (!$called_printed) 
				{echo "<td $AB><font size=1> &nbsp;</td>";}
			$first++;
			}
		echo "<td><font size=1>$leads_in_sts[$sts]</td></tr>\n\n";

		$sts++;
		}

	echo "<tr><td align=center colspan=2><b><font size=1><font color=navy>TOTAL</font></td>";
	$first = $all_called_first;
	while ($first <= $all_called_last)
		{
		if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='bgcolor="#AFEEEE"';} 
		else{$AB='bgcolor="#E0FFFF"';}
		echo "<td align=center $AB><b><font size=1>$all_called_count[$first]</td>";
		$first++;
		}
	echo "<td align=center><b><font size=1>$leads_in_list</td></tr>\n";

	echo "</table></center><br>\n";





	echo "<center>\n";
	echo "<br><br><a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">Click here to see all CallBack Holds in this list</a><BR><BR>\n";
	echo "</center>\n";
	
	if ($LOGdelete_lists > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=511&list_id=$list_id\">DELETE THIS LIST</a>\n";
    echo "<br><br><a href=\"$PHP_SELF?ADD=511&SUB=1&list_id=$list_id\">DELETE THIS LIST AND ITS LEADS</a> (WARNING: Will damage call-backs made in this list!)\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=811 find all callbacks on hold within a List
######################
if ($ADD==811)
{
	if ($LOGmodify_lists==1)
	{
		if ($SUB==89)
		{
		$stmt="UPDATE osdial_callbacks SET status='INACTIVE' where list_id='$list_id' and status='LIVE' and callback_time < '$past_month_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>list($list_id) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899)
		{
		$stmt="UPDATE osdial_callbacks SET status='INACTIVE' where list_id='$list_id' and status='LIVE' and callback_time < '$past_week_date';";
		$rslt=mysql_query($stmt, $link);
		echo "<br>list($list_id) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
$CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=811&SUB=89&list_id=$list_id\"><font color=navy>Remove LIVE Callbacks older than one month for this list</font></a><BR><a href=\"$PHP_SELF?ADD=811&SUB=899&list_id=$list_id\"><font color=navy>Remove LIVE Callbacks older than one week for this list</font></a><BR>";

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$CBquerySQLwhere = "and list_id='$list_id'";

echo "<br><font color=navy> LIST CALLBACK HOLD LISTINGS: $list_id</font>\n";
$oldADD = "ADD=811&list_id=$list_id";
$ADD='82';
}


######################
# ADD=82 display all callbacks on hold
######################
if ($ADD==82)
{

$USERlink='stage=USERIDDOWN';
$GROUPlink='stage=GROUPDOWN';
$ENDATElink='stage=ENDATEDOWN';
$SQLorder='order by ';
if (eregi("USERIDDOWN",$stage)) {$SQLorder='order by user desc,';   $USERlink='stage=USERIDUP';}
if (eregi("GROUPDOWN",$stage)) {$SQLorder='order by user_group desc,';   $NAMElink='stage=NAMEUP';}
if (eregi("ENDATEDOWN",$stage)) {$SQLorder='order by entry_time desc,';   $LEVELlink='stage=LEVELUP';}

	$stmt="SELECT * from osdial_callbacks where status IN('ACTIVE','LIVE') $CBquerySQLwhere $SQLorder recipient,status desc,callback_time";
	$rslt=mysql_query($stmt, $link);
	$cb_to_print = mysql_num_rows($rslt);

echo "<TABLE><TR><TD>\n";
echo "<center><TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=navy>\n";
echo "<td><font size=1 color=white>LEAD</td><td><font size=1 color=white>LIST</td>\n";
echo "<td><font size=1 color=white> CAMPAIGN</td>\n";
echo "<td><a href=\"$PHP_SELF?$oldADD&$ENDATElink\"><font size=1 color=white><B>ENTRY DATE</B></a></td>\n";
echo "<td><font size=1 color=white>CALLBACK DATE</td>\n";
echo "<td><a href=\"$PHP_SELF?$oldADD&$USERlink\"><font size=1 color=white><B>USER</B></a></td>\n";
echo "<td><font size=1 color=white>RECIPIENT</td>\n";
echo "<td><font size=1 color=white>STATUS</td>\n";
echo "<td><a href=\"$PHP_SELF?$oldADD&$GROUPlink\"><font size=1 color=white><B>GROUP</B></a></td></tr>\n";

	$o=0;
	while ($cb_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor>";
		echo "<td><font size=1><A HREF=\"admin_modify_lead.php?lead_id=$row[1]\" target=\"_blank\">$row[1]</A></td>";
		echo "<td><font size=1><A HREF=\"$PHP_SELF?ADD=311&list_id=$row[2]\">$row[2]</A></td>";
		echo "<td><font size=1><A HREF=\"$PHP_SELF?ADD=31&campaign_id=$row[3]\">$row[3]</A></td>";
		echo "<td><font size=1>$row[5]</td>";
		echo "<td><font size=1>$row[6]</td>";
		echo "<td><font size=1><A HREF=\"$PHP_SELF?ADD=3&user=$row[8]\">$row[8]</A></td>";
		echo "<td><font size=1>$row[9]</td>";
		echo "<td><font size=1>$row[4]</td>";
		echo "<td><font size=1><A HREF=\"$PHP_SELF?ADD=311111&user_group=$row[11]\">$row[11]</A></td>";
		echo "</tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";

echo "$CBinactiveLINK";
}



######################
# ADD=100 display all lists
######################
if ($ADD==100)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from osdial_lists order by list_id";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>LISTS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>ID</B></td>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td><font size=1 color=white><B>CAMPAIGN</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td align=center><font size=1 color=white><B>MODIFIED</B></td>";
echo "<td align=center><font size=1 color=white><B>ACTIVE</B></td>";
echo "<td align=center colspan=3><font size=1 color=white><B>LINKS</B></td>";
	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td><font size=1> $row[2]</td>";
		echo "<td><font size=1>$row[4]</td>";
		echo "<td align=center><font size=1>$row[5]</td>";
		echo "<td align=center><font size=1> $row[3]</td>";
		echo "<td><font size=1>$row[7]</td>";
		#echo "<td><font size=1> &nbsp;</td>";
		echo "<td colspan=2 align=center><font size=1><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">MODIFY</a>\n";
		echo " | <font size=1><a href=\"$PHP_SELF?ADD=131&list_id=$row[0]\">EXPORT</a>\n";
		echo " | <font size=1><a href=\"$PHP_SELF?ADD=122&list_id_override=$row[0]\">ADD LEADS</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}






?>
