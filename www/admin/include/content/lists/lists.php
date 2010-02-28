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
# 090410-1544 - Added external_key field


//if ($ADD=='') { $ADD=122 }

######################
# ADD=111 display the ADD NEW LIST FORM SCREEN
######################

if ($ADD==111) {
	if ($LOGmodify_lists==1)	{
		echo "<center><br><font color=$default_text size=4>ADD A NEW LIST</font><form action=$PHP_SELF method=POST><br></center>\n";
		echo "<input type=hidden name=ADD value=211>\n";
		echo "<table width=$section_width bgcolor=$oddrows align=center cellspacing=3>\n";
		echo "  <tr bgcolor=$oddrows><td align=right width=50%>List ID: </td><td align=left width=50%><input type=text name=list_id size=8 maxlength=8> (digits only)$NWB#osdial_lists-list_id$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>List Description: </td><td align=left><input type=text name=list_description size=30 maxlength=255>$NWB#osdial_lists-list_description$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
		
			$stmt="SELECT campaign_id,campaign_name from osdial_campaigns order by campaign_id";
			$rslt=mysql_query($stmt, $link);
			$campaigns_to_print = mysql_num_rows($rslt);
			$campaigns_list='';
		
			$o=0;
			while ($campaigns_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$campaigns_list .= "      <option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				$o++;
			}
		echo "      $campaigns_list";
		echo "      <option SELECTED>$campaign_id</option>\n";
		echo "    </select>$NWB#osdial_lists-campaign_id$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_lists-active$NWE</td></tr>\n";
		echo "  <tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
		echo "</table>\n";
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
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	echo "<center><br><font color=$default_text size=+1>SEARCH FOR A LEAD</font>\n";
	
	$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);
	
	$STARTtime = date("U");
	$TODAY = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");
	
	
	$stmt = sprintf("SELECT count(*) from osdial_users where user='%s' and pass='%s' and user_level > 7 and modify_leads='1';", mres($PHP_AUTH_USER), mres($PHP_AUTH_PW));
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
		Header("WWW-Authenticate: Basic realm=\"$t1-Administrator\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
	} else {
	
		if($auth>0) {
			$office_no=strtoupper($PHP_AUTH_USER);
			$password=strtoupper($PHP_AUTH_PW);
				$stmt = sprintf("SELECT full_name,modify_leads from osdial_users where user='%s' and pass='%s'", mres($PHP_AUTH_USER), mres($PHP_AUTH_PW));
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$LOGfullname				=$row[0];
				$LOGmodify_leads			=$row[1];
	
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "$t1|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
				fclose($fp);
			}
		} else {
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "$t1|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
				fclose($fp);
			}
		}
	}
	
	
	if ( (!$vendor_id) and (!$phone)  and (!$lead_id) and (!$last_name) and (!$first_name) ) {
		echo "<style type=text/css> content {vertical-align:center}</style>";
		echo "\n<br><br><center>\n";
		echo "<TABLE width=$section_width cellspacing=0 bgcolor=$oddrows>\n";
		echo "<tr><td colspan=2>\n";
		echo "<form method=post name=search action=\"$PHP_SELF\">\n";
		echo "<input type=hidden name=ADD value=112>\n";
		echo "<input type=hidden name=DB value=\"$DB\">\n";
		echo "<br><center><font color=$default_text>Enter one of the following</font></center></td>";
		echo "</tr>";
		echo "<tr>\n";
		echo "	<td align=right width=50%>Custom 2:&nbsp;</td>";
		echo "	<td width=50%><input type=text name=vendor_id value=\"$vendor_id\" size=10 maxlength=10> (Aka Vendor Lead Code)</td>";
		echo "</tr>";
		echo "<tr> \n";
		echo "	<td align=right>Home Phone:&nbsp;</td>";
		echo "	<td align=left><input type=text name=phone value=\"$phone\"size=10 maxlength=10></td>";
		echo "</tr>";
		echo "<tr> \n";
		echo "	<td align=right>Last, First Name:&nbsp;</td>";
		echo "	<td align=left><input type=text name=last_name value=\"$last_name\" size=10 maxlength=20><input type=text name=first_name size=10 maxlength=20></td>";
		echo "</tr>\n";
		echo "<tr> \n";
		echo "	<td align=right>Lead ID:&nbsp;</td>";
		echo "	<td align=left><input type=text name=lead_id value=\"$lead_id\" size=10 maxlength=10></td>";
		echo "</tr>\n";
		echo "<tr class=tabfooter>";
		echo "<td colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
		echo "</form>\n";
		echo "</tr>";
		echo "</table>\n";

	/*
	echo "<tr bgcolor=$oddrows><td align=right>List ID: </td><td align=left><input type=text name=list_id size=8 maxlength=8> (digits only)$NWB#osdial_lists-list_id$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
	*/

	
	} else {
		
		if ($last_name and $first_name) {
			$stmt = sprintf("SELECT * from osdial_list where last_name LIKE '%s' AND first_name LIKE '%s' order by modify_date desc limit 1000", mres($last_name) . '%', mres($first_name) . '%');
		} elseif ($last_name) {
			$stmt = sprintf("SELECT * from osdial_list where last_name='%s' order by modify_date desc limit 1000", mres($last_name));
		} elseif ($vendor_id) {
			$stmt = sprintf("SELECT * from osdial_list where vendor_lead_code='%s' order by modify_date desc limit 1000", mres($vendor_id));
		} elseif ($phone) {
			$stmt = sprintf("SELECT * from osdial_list where phone_number='%s' order by modify_date desc limit 1000", mres($phone));
		} elseif ($lead_id) {
			$stmt = sprintf("SELECT * from osdial_list where lead_id='%s' order by modify_date desc limit 1000", mres($lead_id));
		} else {
			echo "ERROR: You must search for something!";
		    exit;
		}
		
		
		if ($DB) {
			echo "\n\n$stmt\n\n";
		}
		
		$rslt=mysql_query($stmt, $link);
		$results_to_print = mysql_num_rows($rslt);
		if ($results_to_print < 1) {
			//echo date("l F j, Y G:i:s A");
			echo "<br><br><br><center>\n";
			echo "<font size=3 color=$default_text>The item(s) you search for were not found.<br><br>\n";
			//echo "You can click on \"Browser Back\" and double check the information you entered.</font>\n";
			echo "<a href='admin.php?ADD=112'>Search Again</a>";
			echo "</center>\n";
		} else {
			echo "<p<font color=$default_text size=+1>Found:&nbsp;$results_to_print</font></b></p>";
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
				if (eregi("1$|3$|5$|7$|9$", $o)) 
					{$bgcolor='bgcolor='.$oddrows;} 
				else
					{$bgcolor='bgcolor='.$evenrows;}
				echo "<TR $bgcolor>\n";
				echo "<TD ALIGN=LEFT><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1>$o</FONT></TD>\n";
                echo "<TD ALIGN=CENTER><FONT FACE=\"ARIAL,HELVETICA\" SIZE=1><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[0]\" target=\"_blank\">$row[0]</a></FONT></TD>\n";
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
echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

if (strlen($phone_number) > 2) {
	$stmt = sprintf("SELECT count(*) from osdial_dnc where phone_number='%s';", mres($phone_number));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0) {
		echo "<br>DNC NOT ADDED - This phone number is already in the Do Not Call List: $phone_number<BR><BR>\n";
	} else {
		$stmt = sprintf("INSERT INTO osdial_dnc (phone_number) values('%s');", mres($phone_number));
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

echo "<br><font color=$default_text size=+1>ADD A NUMBER TO THE DNC LIST</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=121>\n";
//echo "<center>";
echo "<TABLE width=$section_width bgcolor=$oddrows cellspacing=3>\n";
echo "<tr bgcolor=$oddrows><td align=right width=50%>Phone Number: </td><td align=left width=50%><input type=text name=phone_number size=14 maxlength=12> (digits only)$NWB#osdial_list-dnc$NWE</td></tr>\n";
echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
echo "</TABLE></center>\n";

}



######################
# ADD=122 new_listloader_superL.php
######################

if ($ADD==122) {
	
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	
	echo "<center><br><font color=$default_text size=+1>LOAD NEW LEADS</font><br><hr><br>\n";
	
	
	$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
	$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
	$PHP_SELF=$_SERVER['PHP_SELF'];
	$leadfile=$_FILES["leadfile"];
	$LF_orig = $_FILES['leadfile']['name'];
	$LF_path = $_FILES['leadfile']['tmp_name'];
	if (isset($_FILES["leadfile"])) $leadfile_name=$_FILES["leadfile"]['name'];

    $single_insert = 1;
    $dot_count=0;
    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);

	$list_id_override = (preg_replace("/\D/","",$list_id_override));
	$phone_code_override = (preg_replace("/\D/","",$phone_code_override));
	$Imported = get_variable('Imported');
    $file_layout = get_variable('file_layout');
	
	$aff_fields = get_variable('aff_fields');
	$aff_field = Array();
	$affcnt = 0;

    if (strlen($aff_fields) > 0) $single_insert = 1;

	#############################################
	##### START SYSTEM_SETTINGS LOOKUP #####
	$stmt = "SELECT use_non_latin FROM system_settings;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) echo "$stmt\n";
	$qm_conf_ct = mysql_num_rows($rslt);
	$i=0;
	while ($i < $qm_conf_ct) {
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

	$stmt = sprintf("SELECT count(*) from osdial_users where user='%s' and pass='%s' and user_level > 7;", mres($PHP_AUTH_USER), mres($PHP_AUTH_PW));
	if ($DB) echo "|$stmt|\n";
	if ($non_latin > 0) $rslt=mysql_query("SET NAMES 'UTF8'");
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

	if ($WeBRooTWritablE > 0) $fp = fopen ("./project_auth_entries.txt", "a");
	$date = date("r");
	$ip = getenv("REMOTE_ADDR");
	$browser = getenv("HTTP_USER_AGENT");

	if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
		Header("WWW-Authenticate: Basic realm=\"$t1-LEAD-LOADER\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
		exit;
	} elseif ($auth > 0) {
		$office_no=strtoupper($PHP_AUTH_USER);
		$password=strtoupper($PHP_AUTH_PW);
		$stmt = sprintf("SELECT load_leads from osdial_users where user='%s' and pass='%s'", mres($PHP_AUTH_USER), mres($PHP_AUTH_PW));
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGload_leads				=$row[0];
	
		if ($LOGload_leads < 1) {
			echo "You do not have permissions to load leads\n";
			exit;
		}
		if ($WeBRooTWritablE > 0) {
			fwrite ($fp, "LIST_LOAD|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
		}
	} elseif ($WeBRooTWritablE > 0) {
			fwrite ($fp, "LIST_LOAD|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
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
	$stmt = sprintf("SELECT local_gmt FROM servers where server_ip = '%s';",mres($server_ip));
	$rslt=mysql_query($stmt, $link);
	$gmt_recs = mysql_num_rows($rslt);
	if ($gmt_recs > 0) {
		$row=mysql_fetch_row($rslt);
		$DBSERVER_GMT = "$row[0]";
		if (strlen($DBSERVER_GMT)>0) $SERVER_GMT = $DBSERVER_GMT;
		if ($isdst) $SERVER_GMT++;
	} else {
		$SERVER_GMT = date("O");
		$SERVER_GMT = eregi_replace("\+","",$SERVER_GMT);
		$SERVER_GMT = ($SERVER_GMT + 0);
		$SERVER_GMT = ($SERVER_GMT / 100);
	}

	$LOCAL_GMT_OFF = $SERVER_GMT;
	$LOCAL_GMT_OFF_STD = $SERVER_GMT;
	
	echo "<!-- VERSION: $version     BUILD: $build -->\n";
	echo "<!-- SEED TIME  $secX:   $year-$mon-$mday $hour:$min:$sec  LOCAL GMT OFFSET NOW: $LOCAL_GMT_OFF  DST: $isdst -->\n";

    echo "  <form action=$PHP_SELF method=post onSubmit=\"ParseFileName()\" enctype=\"multipart/form-data\">\n";
    echo "      <input type=hidden name='ADD' value='122'>\n";
    echo "      <input type=hidden name='leadfile_name' value=\"$leadfile_name\">\n";
    echo "      <input type=hidden name='Imported' value=''>\n";
	if (!$OK_to_process and ($file_layout != "custom" or $leadfile_name == "")) {
        if ($phone_code_override == "") $phone_code_override = "1";
		$Imported++;
        echo "	        <table align=center width=\"700\" border=0 cellpadding=5 cellspacing=0 bgcolor=$oddrows>\n";
        echo "              <tr>\n";
        echo "                  <td align=right width=\"35%\"><B><font face=\"arial, helvetica\" size=2>Load leads from this file:</font></B></td>\n";
        echo "                  <td align=left width=\"65%\"><input type=file name=\"leadfile\" value=\"$leadfile\">$NWB#osdial_list_loader$NWE</td>\n";
        echo "              </tr>\n";
        echo "              <tr>\n";
        echo "                  <td align=right width=\"25%\"><font face=\"arial, helvetica\" size=2>List ID: </font></td>\n";
        echo "                  <td align=left width=\"75%\"><font face=\"arial, helvetica\" size=1>\n";
        echo "                      <select size=1 name=list_id_override>\n";
        
        $stmt="SELECT list_id,list_name FROM osdial_lists;";
        $rslt=mysql_query($stmt, $link);
        $lrows = mysql_num_rows($rslt);
        if ($lrows > 0) {
            $count = 0;
            if ($list_id_override < 1) echo "            <option value=\"1\">[ PLEASE SELECT A LIST ]</option>\n";
            while ($count < $lrows) {
                $row=mysql_fetch_row($rslt);
                $lsel = '';
                if ($row[0] == $list_id_override) $lsel = ' selected';
                echo '            <option value="' . $row[0] . '"' . $lsel . '>' . $row[0] . ' - ' . $row[1] . "</option>\n";
                $count++;
            }
        } else {
            echo "            <option value=\"1\">[ ERROR, YOU MUST FIRST ADD A LIST]</option>\n";
        }

        echo "                      </select>\n";
        echo "                      <!-- <input type=text value=\"$list_id_override\" name='list_id_override' size=10 maxlength=8>-->\n";
        echo "                    </td>\n";
        echo "                  </tr>\n";
        echo "                  <tr>\n";
        echo "                      <td align=right width=\"25%\"><font face=\"arial, helvetica\" size=2>Phone Code: </font></td>\n";
        echo "                      <td align=left width=\"75%\"><font face=\"arial, helvetica\" size=1>\n";
        echo "                          <input type=text value=\"$phone_code_override\" name='phone_code_override' size=8 maxlength=6> (numbers only or leave blank for values in the file)\n";
        echo "                      </td>\n";
        echo "                  </tr>\n";
        echo "                  <tr>\n";
        echo "                      <td align=right><B><font face=\"arial, helvetica\" size=2>File layout to use:</font></B></td>\n";
        echo "                      <td align=left><font face=\"arial, helvetica\" size=2>\n";
        echo "                          <input type=radio name=\"file_layout\" value=\"custom\" checked>Custom Layout&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=\"file_layout\" value=\"standard\">Predefined Layout\n";
        echo "                      </td>\n";
        echo "                  </tr>\n";
        echo "                  <tr>\n";
        echo "                      <td align=right width=\"25%\"><font face=\"arial, helvetica\" size=2>Lead Duplicate Check: </font></td>\n";
        echo "                      <td align=left width=\"75%\"><font face=\"arial, helvetica\" size=1>\n";
        echo "                          <select size=1 name=dupcheck>\n";
        echo "                              <option value=\"NONE\">NO DUPLICATE CHECK</option>\n";
        echo "                              <option selected value=\"DUPLIST\">CHECK FOR DUPLICATES BY PHONE IN LIST ID</option>\n";
        echo "                              <option value=\"DUPCAMP\">CHECK FOR DUPLICATES BY PHONE IN ALL CAMPAIGN LISTS</option>\n";
        echo "                              <option value=\"DUPCAMPACT\">CHECK FOR DUPLICATES BY PHONE IN ONLY ACTIVE CAMPAIGN LISTS</option>\n";
        echo "                              <option value=\"DUPSYS\">CHECK FOR DUPLICATES BY PHONE IN ALL LISTS ON SYSTEM</option>\n";
        echo "                              <option value=\"DUPSYSACT\">CHECK FOR DUPLICATES BY PHONE IN ONLY ACTIVE LISTS ON SYSTEM</option>\n";
        echo "                          </select>\n";
        echo "                      </td>\n";
        echo "                  </tr>\n";
        echo "                  <tr>\n";
        echo "                      <td align=right width=\"25%\"><font face=\"arial, helvetica\" size=2>Lead Time Zone Lookup: </font></td>\n";
        echo "                      <td align=left width=\"75%\"><font face=\"arial, helvetica\" size=1>\n";
        echo "                          <select size=1 name=postalgmt>\n";
        echo "                              <option selected value=\"AREA\">COUNTRY CODE AND AREA CODE ONLY</option>\n";
        echo "                              <option value=\"POSTAL\">POSTAL CODE FIRST</option>\n";
        echo "                          </select>\n";
        echo "                      </td>\n";
        echo "                  </tr>\n";
        echo "                  <tr class=tabfooter>\n";
        echo "                      <td align=center class=tabbutton>\n";
        echo "                          <input type=button onClick=\"javascript:document.location='admin.php?ADD=122'\" value=\"START OVER\" name='reload_page'>\n";
        echo "                      </td>\n";
        echo "                      <td align=center class=tabbutton>\n";
        echo "                          <input type=submit value=\"SUBMIT\" name='submit_file'>\n";
        echo "                      </td>\n";
        echo "                  </tr>\n";
        echo "               </table>\n";
	}
		
	if ($OK_to_process) {
		echo "<script language='javascript'>\ndocument.forms[0].leadfile.disabled=true;\ndocument.forms[0].list_id_override.disabled=true;\ndocument.forms[0].phone_code_override.disabled=true;\ndocument.forms[0].submit_file.disabled=true;\ndocument.forms[0].reload_page.disabled=true;\n</script>\n";
		ob_flush();
		flush();
		$total=0; $good=0; $bad=0; $dup=0; $post=0;

        # Process an Excel 
		if (eregi(".xls$", $leadfile_name)) {
			# copy($leadfile, "./osdial_temp_file.xls");
			$file=fopen("$lead_file", "r");
		
			echo "<center><font size=3 color='$default_text'><B>Processing Excel file... \n<br>";

            echo "<iframe name='lead_count' width='600' height='250' align='middle' frameborder='0' scrolling='no'></iframe>\n<br>\n";
            echo "<div name='load_win' id='load_win' style='width:850px;align:center;'>\n";
            echo "  <div name='load_status' id='load_status' style='float:left;text-align:center;width:50%;height:200px;overflow:auto;'></div>\n";
            echo "  <div name='load_error' id='load_error' style='float:left;text-align:left;width:50%;height:200px;overflow:auto;'></div>\n";
            echo "</div>";
            require($WeBServeRRooT . "/admin/include/footer.php");

			$dupcheckCLI=''; $postalgmtCLI='';
			if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
			if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
			if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
			if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
			passthru("$WeBServeRRooT/admin/listloader_super.pl $vendor_lead_code_field,$source_id_field,$list_id_field,$phone_code_field,$phone_number_field,$title_field,$first_name_field,$middle_initial_field,$last_name_field,$address1_field,$address2_field,$address3_field,$city_field,$state_field,$province_field,$postal_code_field,$country_code_field,$gender_field,$date_of_birth_field,$alt_phone_field,$email_field,$custom1_field,$comments_field, --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");

        # Process a CSV/PSV/TSV
		} else {
			$file=fopen("$lead_file", "r");

			$buffer=fgets($file, 4096);
			$comma_count=substr_count($buffer, ",");
			$tab_count=substr_count($buffer, "\t");
			$pipe_count=substr_count($buffer, "|");
	
			if ($tab_count > $comma_count and $tab_count > $pipe_count) {
				$delimiter="\t";  $delim_name="TSV (tab-separated values)";
			} elseif ($pipe_count > $tab_count and $pipe_count > $comma_count) {
				$delimiter="|";  $delim_name="PSV (pipe-separated values)";
			} else {
				$delimiter=",";  $delim_name="CSV (comma-separated values)";
			}

            flush();
			$file=fopen("$lead_file", "r");
		
			if ($WeBRooTWritablE > 0 and $single_insert < 1) $stmt_file=fopen("$WeBServeRRooT/admin/listloader_stmts.txt", "w");
					
			echo "<center><font size=3 color='$default_text'><B>Processing $delim_name file... \n<br>";

            echo "<iframe name='lead_count' width='600' height='250' align='middle' frameborder='0' scrolling='no'></iframe>\n<br>\n";
            echo "<div name='load_win' id='load_win' style='width:850px;align:center;'>\n";
            echo "  <div name='load_status' id='load_status' style='float:left;text-align:center;width:50%;height:200px;overflow:auto;'></div>\n";
            echo "  <div name='load_error' id='load_error' style='float:left;text-align:left;width:50%;height:200px;overflow:auto;'></div>\n";
            echo "</div>";
            require($WeBServeRRooT . "/admin/include/footer.php");

			while($row=fgetcsv($file, 1000, $delimiter)) {
				$pulldate=date("Y-m-d H:i:s");
				$entry_date =			"$pulldate";
				$modify_date =			"";
				$status =				"NEW";
				$user =                 "";
				$vendor_lead_code =		$row[$vendor_lead_code_field];
				$source_code =			$row[$source_id_field];
				$source_id=             $source_code;
				$list_id =				$row[$list_id_field];
				$gmt_offset =			'0';
				$called_since_last_reset='N';
				$phone_code =			eregi_replace("[^0-9]", "", $row[$phone_code_field]);
				$phone_number =			eregi_replace("[^0-9]", "", $row[$phone_number_field]);
				$USarea = 			    substr($phone_number, 0, 3);
				$title =				$row[$title_field];
				$first_name =			$row[$first_name_field];
				$middle_initial =		$row[$middle_initial_field];
				$last_name =			$row[$last_name_field];
				$address1 =				$row[$address1_field];
				$address2 =				$row[$address2_field];
				$address3 =				$row[$address3_field];
				$city =                 $row[$city_field];
				$state =				$row[$state_field];
				$province =				$row[$province_field];
				$postal_code =			$row[$postal_code_field];
				$country_code =			$row[$country_code_field];
				$gender =				$row[$gender_field];
				$date_of_birth =		$row[$date_of_birth_field];
				$alt_phone =			eregi_replace("[^0-9]", "", $row[$alt_phone_field]);
				$email =				$row[$email_field];
				$custom1 =		        $row[$custom1_field];
				$comments =				trim($row[$comments_field]);
				$custom2 =		        $row[$custom2_field];
				$external_key =		    $row[$external_key_field];
				$cost =		            $row[$cost_field];

                $aflds = explode(',',$aff_fields);
                foreach ($aflds as $afld) {
                    $aff_field[$afld] = $row[get_variable($afld)];
                }
		
				if (strlen($list_id_override)>0) $list_id = $list_id_override;
				if (strlen($phone_code_override)>0) $phone_code = $phone_code_override;
				if (strlen($phone_code)<1) $phone_code = '1';

                $list_camp = get_first_record($link, 'osdial_lists', '*', sprintf("list_id='%s'", mres($list_id)));

                # Try to grab cost if its zero 
                if ($cost == 0) $cost = $list_camp['cost'];


                if ($dupcheck != "" and $dupcheck != "NONE") {
				    $dup_lead=0;
                    $dup_list = '';
                    $dup_list_active = '';
                    $dup_syslist = '';
                    $dup_syslist_active = '';

                    if (preg_match('/^DUPCAMP/i',$dupcheck)) {
                        $camp_lists = get_krh($link, 'osdial_lists', 'list_id,active', '', sprintf("campaign_id='%s'",mres($list_camp['campaign_id'])), '');
                        foreach ($camp_lists as $clist) {
                            if ($clist['list_id'] != "") {
                                $dup_list .= "'" . mres($clist['list_id']) . "',";
                                if ($clist['active'] == "Y")
                                    $dup_list_active .= "'" . mres($clist['list_id']) . "',";
                            }
                        }
                        $dup_list = rtrim($dup_list,',');
                        $dup_list_active = rtrim($dup_list_active,',');
                    }

                    if (preg_match('/^DUPSYS/i',$dupcheck)) {
                        $sys_lists = get_krh($link, 'osdial_lists', 'list_id,active', '', '', '');
                        foreach ($sys_lists as $slist) {
                            if ($slist['list_id'] != "") {
                                $dup_syslist .= "'" . mres($slist['list_id']) . "',";
                                if ($slist['active'] == "Y")
                                    $dup_syslist_active .= "'" . mres($slist['list_id']) . "',";
                            }
                        }
                        $dup_syslist = rtrim($dup_syslist,',');
                        $dup_syslist_active = rtrim($dup_syslist_active,',');
                    }


                    if (preg_match('/^DUPCAMP$/i',$dupcheck)) {
                        $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($phone_number),$dup_list);

                    } elseif (preg_match('/^DUPCAMPACT$/i',$dupcheck)) {
                        $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($phone_number),$dup_list_active);

                    } elseif (preg_match('/^DUPSYS$/i',$dupcheck)) {
                        $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($phone_number),$dup_syslist);

                    } elseif (preg_match('/^DUPSYSACT$/i',$dupcheck)) {
                        $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($phone_number),$dup_syslist_active);

                    } elseif (preg_match('/^DUPLIST$/i',$dupcheck)) {
                        $dup_where = sprintf("phone_number='%s' AND list_id='%s'",mres($phone_number),mres($list_id));
                    }

                    # Check for the duplicate.
                    $gfr_dup = get_first_record($link, 'osdial_list', 'lead_id,list_id', $dup_where);
                    if ($gfr_dup['lead_id'] > 0) {
                        $dup_lead++;
                        $dup++;
                    }
                }


				if (strlen($phone_number) > 6 and strlen($phone_number) < 17 and $dup_lead < 1) {
					$gmtl = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
					$gmt_offset = $gmtl[0];
                    $postal = $gmtl[1];
                    if ($postal > 0) $post++;
		
					if ($single_insert > 0) {
						$stmtZ = sprintf("INSERT INTO osdial_list values ('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0,'%s','%s','2008-01-01 00:00:00','%s','0000-00-00 00:00:00');",mres($entry_date),mres($modify_date),mres($status),mres($user),mres($vendor_lead_code),mres($source_id),mres($list_id),mres($gmt_offset),mres($called_since_last_reset),mres($phone_code),mres($phone_number),mres($title),mres($first_name),mres($middle_initial),mres($last_name),mres($address1),mres($address2),mres($address3),mres($city),mres($state),mres($province),mres($postal_code),mres($country_code),mres($gender),mres($date_of_birth),mres($alt_phone),mres($email),mres($custom1),mres($comments),mres($custom2),mres($external_key),mres($cost));
						$rslt=mysql_query($stmtZ, $link);
                        $lead_id = mysql_insert_id($link);
                        foreach ($aff_field as $k => $v) {
                            if (strlen($v)>0) {
                                $afs = explode('_',$k);
                                $stmt = sprintf("INSERT INTO osdial_list_fields SET lead_id='%s',field_id='%s',value='%s';", mres($lead_id), mres($afs[2]),mres($v));
						        $rslt=mysql_query($stmt, $link);
                                $affcnt++;
                            }
                        }

					} elseif ($multi_insert_counter > 8) {
						### insert good deal into pending_transactions table ###
						$stmtZ = sprintf("INSERT INTO osdial_list values%s('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0,'%s','%s','2008-01-01 00:00:00','%s','0000-00-00 00:00:00');",$multistmt,mres($entry_date),mres($modify_date),mres($status),mres($user),mres($vendor_lead_code),mres($source_id),mres($list_id),mres($gmt_offset),mres($called_since_last_reset),mres($phone_code),mres($phone_number),mres($title),mres($first_name),mres($middle_initial),mres($last_name),mres($address1),mres($address2),mres($address3),mres($city),mres($state),mres($province),mres($postal_code),mres($country_code),mres($gender),mres($date_of_birth),mres($alt_phone),mres($email),mres($custom1),mres($comments),mres($custom2),mres($external_key),mres($cost));
						$rslt=mysql_query($stmtZ, $link);
						if ($WeBRooTWritablE > 0) fwrite($stmt_file, $stmtZ."\n");
						$multistmt='';
						$multi_insert_counter=0;
		
				    } else {
					    $multistmt .= sprintf("('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0,'%s','%s','2008-01-01 00:00:00','%s','0000-00-00 00:00:00'),",mres($entry_date),mres($modify_date),mres($status),mres($user),mres($vendor_lead_code),mres($source_id),mres($list_id),mres($gmt_offset),mres($called_since_last_reset),mres($phone_code),mres($phone_number),mres($title),mres($first_name),mres($middle_initial),mres($last_name),mres($address1),mres($address2),mres($address3),mres($city),mres($state),mres($province),mres($postal_code),mres($country_code),mres($gender),mres($date_of_birth),mres($alt_phone),mres($email),mres($custom1),mres($comments),mres($custom2),mres($external_key),mres($cost));
					    $multi_insert_counter++;
				    }
				    $good++;

				} elseif ($dup_lead > 0) {
                    echo "<script language='javascript'>\ndocument.getElementById('load_error').innerHTML += '<b><font size=1 color=#ff6600>record " . ($total + 1) . " DUP-$dup: L$dup_lead / P$phone_number</font></b><br>';\n</script>\n";
                } else {
                    $bad++;
                    echo "<script language='javascript'>\ndocument.getElementById('load_error').innerHTML += '<b><font size=1 color=red>record " . ($total + 1) . " BAD-$bad: P$phone_number</font></b><br>';\n</script>\n";
                }
				ob_flush();
				flush();
				$total++;

				if ($total%200==0) {
				    echo "<script language='javascript'>\nShowProgress($good, $bad, $total, $dup, $post, $affcnt);\n</script>\n";
                    echo "<script language='javascript'>\ndocument.getElementById('load_status').innerHTML += '.';\n</script>\n";
                    if ($dot_count >= 80) {
                        echo "<script language='javascript'>\ndocument.getElementById('load_status').innerHTML += '<br>';\n</script>\n";
                        $dot_count=0;
                    }
				    ob_flush();
				    flush();
                    $dot_count++;
				}
			}
			if ($single_insert < 1 and $multi_insert_counter!=0) {
				$stmtZ = "INSERT INTO osdial_list values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) fwrite($stmt_file, $stmtZ."\n");
			}

			echo "<script language='javascript'>\nShowProgress($good, $bad, $total, $dup, $post, $affcnt);\n</script>\n";
            $dwin = 'load_status';
            if (($dup + $bad) == 0) $dwin = 'load_win';
            $lmenu = ''; if ($list_id_override > 0) $lmenu = "<br><br><span style=text-align:center;font-size:14px;><a href=/admin/admin.php?ADD=311&list_id=$list_id_override>[ Back to List ]</a></span>";
            echo "<script language='javascript'>\ndocument.getElementById('$dwin').innerHTML = '<span style=text-align:center;font-size:48px;><b>DONE<b></span>$lmenu';\n</script>\n";
			$Imported++;
            $leadfile_name = '';
		}
		echo "<script language='javascript'>\ndocument.forms[0].leadfile.disabled=false;\ndocument.forms[0].submit_file.disabled=false;\ndocument.forms[0].reload_page.disabled=false;\n</script>\n";
        exit;

	} elseif ($leadfile) {
		# Look for list id before importing leads
		if ($list_id_override > 0) {
            $list_camp = get_first_record($link, 'osdial_lists', '*', sprintf("list_id='%s'", mres($list_id_override)));
			if ($list_camp['list_id'] == "") echo "<br><br>You are trying to load leads into a non existent list $list_id_override<br>";
		}
			
		$total=0; $good=0; $bad=0; $dup=0; $post=0;
		if ($file_layout=="standard") {
			echo "<script language='javascript'>\ndocument.forms[0].leadfile.disabled=true;\ndocument.forms[0].submit_file.disabled=true;\ndocument.forms[0].reload_page.disabled=true;\n</script>\n";
			ob_flush();
			flush();
		
            # Process the "standard" style Excel file.
		    if (eregi(".xls$", $leadfile_name)) {
			    if ($WeBRooTWritablE > 0) {
				    copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.xls");
				    $lead_file = "$WeBServeRRooT/admin/osdial_temp_file.xls";
			    } else {
				    copy($LF_path, "/tmp/osdial_temp_file.xls");
				    $lead_file = "/tmp/osdial_temp_file.xls";
			    }
			    $file=fopen("$lead_file", "r");
		
			    $dupcheckCLI='';
                $postalgmtCLI='';
			    if (eregi("DUPLIST",$dupcheck)) $dupcheckCLI='--duplicate-check';
			    if (eregi("DUPCAMP",$dupcheck)) $dupcheckCLI='--duplicate-campaign-check';
			    if (eregi("DUPSYS",$dupcheck)) $dupcheckCLI='--duplicate-system-check';
			    if (eregi("POSTAL",$postalgmt)) $postalgmtCLI='--postal-code-gmt';
			    passthru("$WeBServeRRooT/admin/listloader.pl --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file  $postalgmtCLI $dupcheckCLI");
			
            # Process "standard" CSV/TSV/PSV
		    } else {
		    	if ($WeBRooTWritablE > 0) {
		    		copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.csv");
		    		$lead_file = "$WeBServeRRooT/admin/osdial_temp_file.csv";
		    	} else {
		    		copy($LF_path, "/tmp/osdial_temp_file.csv");
		    		$lead_file = "/tmp/osdial_temp_file.csv";
		    	}
		    	$file=fopen("$lead_file", "r");
		    	$buffer=fgets($file, 4096);
		    	$comma_count=substr_count($buffer, ",");
		    	$tab_count=substr_count($buffer, "\t");
		    	$pipe_count=substr_count($buffer, "|");
	
		    	if ($tab_count > $comma_count and $tab_count > $pipe_count) {
		    		$delimiter="\t";  $delim_name="TSV (tab-separated values)";
		    	} elseif ($pipe_count > $tab_count and $pipe_count > $comma_count) {
		    		$delimiter="|";  $delim_name="PSV (pipe-separated values)";
		    	} else {
		    		$delimiter=",";  $delim_name="CSV (comma-separated values)";
		    	}

                flush();
		    	$file=fopen("$lead_file", "r");
		    	if ($WeBRooTWritablE > 0 and $single_insert < 1) $stmt_file=fopen("$WeBServeRRooT/admin/listloader_stmts.txt", "w");
				
		    	echo "<center><font size=3 color='$default_text'><B>Processing $delim_name file... \n<br>";
		
                echo "<iframe name='lead_count' width='600' height='250' align='middle' frameborder='0' scrolling='no'></iframe>\n<br>\n";
                echo "<div name='load_win' id='load_win' style='width:850px;align:center;'>\n";
                echo "  <div name='load_status' id='load_status' style='float:left;text-align:center;width:50%;height:200px;overflow:auto;'></div>\n";
                echo "  <div name='load_error' id='load_error' style='float:left;text-align:left;width:50%;height:200px;overflow:auto;'></div>\n";
                echo "</div>";
                require($WeBServeRRooT . "/admin/include/footer.php");

		    	while ($row=fgetcsv($file, 1000, $delimiter)) {
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
	
		    		if (strlen($list_id_override)>0) $list_id = $list_id_override;
		    		if (strlen($phone_code_override)>0) $phone_code = $phone_code_override;
		    		if (strlen($phone_code)<1) $phone_code = '1';

                    $list_camp = get_first_record($link, 'osdial_lists', '*', sprintf("list_id='%s'", mres($list_id)));

                    # Try to grab cost if its zero 
                    if ($cost == 0) $cost = $list_camp['cost'];


                    if ($dupcheck != "" and $dupcheck != "NONE") {
				        $dup_lead=0;
                        $dup_list = '';
                        $dup_list_active = '';
                        $dup_syslist = '';
                        $dup_syslist_active = '';

                        if (preg_match('/^DUPCAMP/i',$dupcheck)) {
                            $camp_lists = get_krh($link, 'osdial_lists', 'list_id,active', '', sprintf("campaign_id='%s'",mres($list_camp['campaign_id'])), '');
                            foreach ($camp_lists as $clist) {
                                if ($clist['list_id'] != "") {
                                    $dup_list .= "'" . mres($clist['list_id']) . "',";
                                    if ($clist['active'] == "Y")
                                        $dup_list_active .= "'" . mres($clist['list_id']) . "',";
                                }
                            }
                            $dup_list = rtrim($dup_list,',');
                            $dup_list_active = rtrim($dup_list_active,',');
                        }

                        if (preg_match('/^DUPSYS/i',$dupcheck)) {
                            $sys_lists = get_krh($link, 'osdial_lists', 'list_id,active', '', '', '');
                            foreach ($sys_lists as $slist) {
                                if ($slist['list_id'] != "") {
                                    $dup_syslist .= "'" . mres($slist['list_id']) . "',";
                                    if ($slist['active'] == "Y")
                                        $dup_syslist_active .= "'" . mres($slist['list_id']) . "',";
                                }
                            }
                            $dup_syslist = rtrim($dup_syslist,',');
                            $dup_syslist_active = rtrim($dup_syslist_active,',');
                        }


                        if (preg_match('/^DUPCAMP$/i',$dupcheck)) {
                            $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($phone_number),$dup_list);

                        } elseif (preg_match('/^DUPCAMPACT$/i',$dupcheck)) {
                            $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($phone_number),$dup_list_active);

                        } elseif (preg_match('/^DUPSYS$/i',$dupcheck)) {
                            $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($phone_number),$dup_syslist);

                        } elseif (preg_match('/^DUPSYSACT$/i',$dupcheck)) {
                            $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($phone_number),$dup_syslist_active);

                        } elseif (preg_match('/^DUPLIST$/i',$dupcheck)) {
                            $dup_where = sprintf("phone_number='%s' AND list_id='%s'",mres($phone_number),mres($list_id));
                        }

                        # Check for the duplicate.
                        $gfr_dup = get_first_record($link, 'osdial_list', 'lead_id,list_id', $dup_where);
                        if ($gfr_dup['lead_id'] > 0) {
                            $dup_lead++;
                            $dup++;
                        }
                    }


	                if (strlen($phone_number) > 6 and strlen($phone_number) < 17 and $dup_lead < 1) {
					    $gmtl = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
					    $gmt_offset = $gmtl[0];
                        $postal = $gmtl[1];
                        if ($postal > 0) $post++;
	
						if ($single_insert > 0) {
							$stmtZ = sprintf("INSERT INTO osdial_list values ('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0,'%s','%s','2008-01-01 00:00:00','%s','0000-00-00 00:00:00');",mres($entry_date),mres($modify_date),mres($status),mres($user),mres($vendor_lead_code),mres($source_id),mres($list_id),mres($gmt_offset),mres($called_since_last_reset),mres($phone_code),mres($phone_number),mres($title),mres($first_name),mres($middle_initial),mres($last_name),mres($address1),mres($address2),mres($address3),mres($city),mres($state),mres($province),mres($postal_code),mres($country_code),mres($gender),mres($date_of_birth),mres($alt_phone),mres($email),mres($custom1),mres($comments),mres($custom2),mres($external_key),mres($cost));
							$rslt=mysql_query($stmtZ, $link);

						} elseif ($multi_insert_counter > 8) {
							### insert good deal into pending_transactions table ###
							$stmtZ = sprintf("INSERT INTO osdial_list values%s('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0,'%s','%s','2008-01-01 00:00:00','%s','0000-00-00 00:00:00');",$multistmt,mres($entry_date),mres($modify_date),mres($status),mres($user),mres($vendor_lead_code),mres($source_id),mres($list_id),mres($gmt_offset),mres($called_since_last_reset),mres($phone_code),mres($phone_number),mres($title),mres($first_name),mres($middle_initial),mres($last_name),mres($address1),mres($address2),mres($address3),mres($city),mres($state),mres($province),mres($postal_code),mres($country_code),mres($gender),mres($date_of_birth),mres($alt_phone),mres($email),mres($custom1),mres($comments),mres($custom2),mres($external_key),mres($cost));
							$rslt=mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0) fwrite($stmt_file, $stmtZ."\n");
							$multistmt='';
							$multi_insert_counter=0;
		
						} else {
							$multistmt .= sprintf("('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0,'%s','%s','2008-01-01 00:00:00','%s','0000-00-00 00:00:00'),",mres($entry_date),mres($modify_date),mres($status),mres($user),mres($vendor_lead_code),mres($source_id),mres($list_id),mres($gmt_offset),mres($called_since_last_reset),mres($phone_code),mres($phone_number),mres($title),mres($first_name),mres($middle_initial),mres($last_name),mres($address1),mres($address2),mres($address3),mres($city),mres($state),mres($province),mres($postal_code),mres($country_code),mres($gender),mres($date_of_birth),mres($alt_phone),mres($email),mres($custom1),mres($comments),mres($custom2),mres($external_key),mres($cost));
							$multi_insert_counter++;
						}
						$good++;
						
					} elseif ($dup_lead > 0) {
                        echo "<script language='javascript'>\ndocument.getElementById('load_error').innerHTML += '<b><font size=1 color=#ff6600>record " . ($total + 1) . " DUP-$dup: L$dup_lead / P$phone_number</font></b><br>';\n</script>\n";
                    } else {
                        $bad++;
                        echo "<script language='javascript'>\ndocument.getElementById('load_error').innerHTML += '<b><font size=1 color=red>record " . ($total + 1) . " BAD-$bad: P$phone_number</font></b><br>';\n</script>\n";
                    }
					ob_flush();
					flush();
					$total++;

					if ($total%200==0) {
						echo "<script language='javascript'>\nShowProgress($good, $bad, $total, $dup, $post, $affcnt);\n</script>\n";
                        echo "<script language='javascript'>\ndocument.getElementById('load_status').innerHTML += '.';\n</script>\n";
                        if ($dot_count >= 80) {
                            echo "<script language='javascript'>\ndocument.getElementById('load_status').innerHTML += '<br>';\n</script>\n";
                            $dot_count=0;
                        }
						ob_flush();
						flush();
                        $dot_count++;
					}
				}
				if ($single_insert < 1 and $multi_insert_counter != 0) {
					$stmtZ = "INSERT INTO osdial_list values".substr($multistmt, 0, -1).";";
					mysql_query($stmtZ, $link);
					if ($WeBRooTWritablE > 0) fwrite($stmt_file, $stmtZ."\n");
				}
			    echo "<script language='javascript'>\nShowProgress($good, $bad, $total, $dup, $post, $affcnt);\n</script>\n";
                $dwin = 'load_status';
                if (($dup + $bad) == 0) $dwin = 'load_win';
                $lmenu = ''; if ($list_id_override > 0) $lmenu = "<br><br><span style=text-align:center;font-size:14px;><a href=/admin/admin.php?ADD=311&list_id=$list_id_override>[ Back to List ]</a></span>";
                echo "<script language='javascript'>\ndocument.getElementById('dwin').innerHTML = '<span style=text-align:center;font-size:48px;><b>DONE<b></span>$lmenu';\n</script>\n";
			}
			echo "<script language='javascript'>\ndocument.forms[0].leadfile.disabled=false;\ndocument.forms[0].submit_file.disabled=false;\ndocument.forms[0].reload_page.disabled=false;\n</script>\n";
            exit;
	

        # Field Mapping screen.
		} else {
            $badfile=0;
			echo "<script language='javascript'>\ndocument.forms[0].leadfile.disabled=true;\ndocument.forms[0].submit_file.disabled=true;\ndocument.forms[0].reload_page.disabled=true;\n</script>\n<br>\n";
			ob_flush();
			flush();
			echo "<table border=0 cellspacing=1 width=400 align=center bgcolor=grey>\n";
			echo "  <tr class=tabheader>\n";
			echo "    <td align=center colspan=2>FIELD MAPPINGS</td>\n";
			echo "  </tr>\n";
			echo "  <tr class=tabheader>\n";
			echo "    <td align=center>" . strtoupper($t1) . " LEAD FIELD</td>\n";
			echo "    <td align=center width=50%>FILE HEADER ROW</td>\n";
			echo "  </tr>\n";

            $afmaps = Array();
            $afjoin = "";
			if (strlen($list_id_override)>0) {
                $gfr_list = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($list_id_override)) );
			    if (strlen($gfr_list['campaign_id'])>0) {
                    $camp = $gfr_list['campaign_id'];
                    $af_forms = get_krh($link, 'osdial_campaign_forms', '*', 'name ASC', sprintf("deleted='0' AND (campaigns='ALL' OR campaigns='%s' OR campaigns LIKE '%s,%%' OR campaigns LIKE '%%,%s')",mres($camp),mres($camp),mres($camp)), '');
                    foreach ($af_forms as $afform) {
                        $af_fields = get_krh($link, 'osdial_campaign_fields', '*', 'name ASC', sprintf("deleted='0' AND form_id='%s'",$afform['id']), '');
                        foreach ($af_fields as $affield) {
                            $afmaps['AF_' . $afform['id'] . '_' . $affield['id']] = $afform['name'] . '_' . $affield['name'];
                            $afjoin .= 'AF_' . $afform['id'] . '_' . $affield['id'] . ',';
                        }
                    }
                }
			}
            rtrim($afjoin,',');
				
			$rslt=mysql_query("select phone_code, list_id, vendor_lead_code, source_id, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, custom1, comments, custom2, external_key, cost from osdial_list limit 1", $link);
			
	
            # Process Excel file for field selection.
			if (eregi(".xls$", $leadfile_name)) {
				if ($WeBRooTWritablE > 0) {
					copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.xls");
					$lead_file = "$WeBServeRRooT/admin/osdial_temp_file.xls";
				} else {
					copy($LF_path, "/tmp/osidial_temp_file.xls");
					$lead_file = "/tmp/osdial_temp_file.xls";
				}
	
				$dupcheckCLI=''; $postalgmtCLI='';
				if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
				if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
				if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
				if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
				passthru("$WeBServeRRooT/admin/listloader_rowdisplay.pl --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");

            # Process CSV/PSV/TSV file for field selection.
			} elseif (preg_match('/\.txt$|\.csv$|\.psv$|\.tsv$|\.tab$/i', $leadfile_name)) {
				if ($WeBRooTWritablE > 0) {
					copy($LF_path, "$WeBServeRRooT/admin/osdial_temp_file.csv");
					$lead_file = "$WeBServeRRooT/admin/osdial_temp_file.csv";
				} else {
					copy($LF_path, "/tmp/osdial_temp_file.csv");
					$lead_file = "/tmp/osdial_temp_file.csv";
				}

				$file=fopen("$lead_file", "r");
			    $buffer=fgets($file, 4096);
			    $comma_count=substr_count($buffer, ",");
			    $tab_count=substr_count($buffer, "\t");
			    $pipe_count=substr_count($buffer, "|");
	
			    if ($tab_count > $comma_count and $tab_count > $pipe_count) {
			    	$delimiter="\t";  $delim_name="TSV (tab-separated values)";
			    } elseif ($pipe_count > $tab_count and $pipe_count > $comma_count) {
			    	$delimiter="|";  $delim_name="PSV (pipe-separated values)";
			    } else {
			    	$delimiter=",";  $delim_name="CSV (comma-separated values)";
			    }

                flush();
			    $file=fopen("$lead_file", "r");
	
				if ($WeBRooTWritablE > 0 and $single_insert < 1) $stmt_file=fopen("$WeBServeRRooT/admin/listloader_stmts.txt", "w");
				
				echo "<center><font size=3 color='$default_text'><B>Processing $delim_name file... \n<br>";
				
				$row=fgetcsv($file, 1000, $delimiter);
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {
		            if (eregi("1$|3$|5$|7$|9$", $i)) {
                        $bgcolor='bgcolor='.$oddrows;
                    } else {
                        $bgcolor='bgcolor='.$evenrows;
                    }
					echo "  <tr class=\"row font1\" $bgcolor>\n";
					echo "    <td align=center>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </td>\n";
					echo "    <td align=center class=tabinput>\n";
					if (mysql_field_name($rslt, $i) == "list_id" and $list_id_override != "") {
						echo "      <a title='The List ID was set from the Load New Leads options menu, it will not be pulled from the file.'><center><font color=red><b>$list_id_override</b></font><center></a>\n";
                    } elseif (mysql_field_name($rslt, $i) == "phone_code" and $phone_code_override != "") {
						echo "      <a title='The Phone Code was set from the Load New Leads options menu, it will not be pulled from the file.'><center><font color=red><b>$phone_code_override</b></font><center></a>\n";
                    } else {
                        echo "      <select name='".mysql_field_name($rslt, $i)."_field'>\n";
					    echo "        <option value='-1'>(none)</option>\n";
					    for ($j=0; $j<count($row); $j++) {
						    eregi_replace("\"", "", $row[$j]);
						    echo "        <option value='$j'>\"$row[$j]\"</option>\n";
					    }
					    echo "      </select>\n";
                    }
                    echo "    </td>\n";
					echo "  </tr>\n";
				}
			} else {
                # Oops, we didn't recognize the file extension.
                $badfile=1;
            }

            if ($badfile == 0) {
                # Display Additional Form Fields if a "custom" lead load.
                if (count($afmaps) > 0 and $file_layout == "custom") {
			        echo "  <input type=hidden name=aff_fields value=\"$afjoin\">\n";
			        echo "  <tr class=tabheader>\n";
			        echo "    <td align=center colspan=2>ADDITIONAL FORM FIELDS</td>\n";
			        echo "  </tr>\n";
                    $o=0;
                    foreach ($afmaps as $k => $v) {
		                if (eregi("1$|3$|5$|7$|9$", $o)) {
                            $bgcolor='bgcolor='.$oddrows;
                        } else {
                            $bgcolor='bgcolor='.$evenrows;
                        }
					    echo "  <tr class=\"row font1\" $bgcolor>\n";
					    echo "    <td align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".strtoupper(eregi_replace("_", " ", $v)).": </td>\n";
					    echo "    <td align=center class=tabinput>\n";
                        echo "      <select name='$k'>\n";
					    echo "        <option value='-1'>(none)</option>\n";
					    for ($j=0; $j<count($row); $j++) {
					        eregi_replace("\"", "", $row[$j]);
					        echo "        <option value='$j'>\"$row[$j]\"</option>\n";
					    }
					    echo "      </select>\n";
                        echo "    </td>\n";
					    echo "  </tr>\n";
                        $o++;
                    }
                }

			    echo "  <input type=hidden name=dupcheck value=\"$dupcheck\">\n";
			    echo "  <input type=hidden name=postalgmt value=\"$postalgmt\">\n";
			    echo "  <input type=hidden name=lead_file value=\"$lead_file\">\n";
			    echo "  <input type=hidden name=list_id_override value=\"$list_id_override\">\n";
			    echo "  <input type=hidden name=phone_code_override value=\"$phone_code_override\">\n";
			    echo "  <input type=hidden name=ADD value=122>\n";
			    echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton><input type=button onClick=\"javascript:document.location='admin.php?ADD=122'\" value=\"START OVER\" name='reload_page'></td>\n";
			    echo "    <td align=center class=tabbutton><input type=submit name='OK_to_process' value='OK TO PROCESS'></td>\n";
			    echo "  </tr>\n";
			    echo "</table>\n";
            } else {
			    echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton colspan=2><input type=button onClick=\"javascript:document.location='admin.php?ADD=122'\" value=\"START OVER\" name='reload_page'></td>\n";
			    echo "  </tr>\n";
			    echo "</table>\n";
                echo "<br><br><center><b>The uploaded file format is not supported, CSV format is often the best choice when preparing and loading lists.</b></center>\n";
            }
		}
		echo "<script language='javascript'>\ndocument.forms[0].leadfile.disabled=false;\ndocument.forms[0].submit_file.disabled=false;\ndocument.forms[0].reload_page.disabled=false;\n</script>\n";
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
		Header("WWW-Authenticate: Basic realm=\"$t1-Administrator\"");
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
				fwrite ($fp, "$t1|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
				fclose($fp);
			}
		} else {
			if ($WeBRooTWritablE > 0) {
				fwrite ($fp, "$t1|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
				fclose($fp);
			}
		}
	}
	if ($LOGmodify_lists==1)	{
		echo "<TABLE align=center><TR><TD>\n";
		echo "<center><br><font color=$default_text size='2'>GENERATE TEST LEADS</font><br>(ONLY works with TEST list 998.)<form action=$PHP_SELF method=POST><br><br>\n";
		echo "<input type=hidden name=ADD value=126>\n";
		echo "<TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Phone Number: </td><td align=left><input type=text name=testphone size=8 maxlength=8> (digits only)$NWB#$NWE</td></tr>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Number of leads: </td><td align=left><input type=text name=testnbr size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
		echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
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

if ($ADD==211) {
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
    $stmt="SELECT count(*) from osdial_lists where list_id='$list_id';";
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    if ($row[0] > 0) {
        echo "<br><font color=red>LIST NOT ADDED - there is already a list in the system with this ID</font>\n";
    } else {
        if ( (strlen($campaign_id) < 2) or (strlen($list_name) < 2)  or ($list_id < 100) or (strlen($list_id) > 8) ) {
            echo "<br><font color=red>LIST NOT ADDED - Please go back and look at the data you entered\n";
            echo "<br>List ID must be between 2 and 8 characters in length\n";
            echo "<br>List name must be at least 2 characters in length\n";
            echo "<br>List ID must be greater than 100</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>LIST ADDED: $list_id</font></B>\n";

            $stmt="INSERT INTO osdial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate) values('$list_id','$list_name','$campaign_id','$active','$list_description','$SQLdate');";
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
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

if ($ADD==411) {
    if ($LOGmodify_lists==1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

        if ( (strlen($list_name) < 2) or (strlen($campaign_id) < 2) ) {
            echo "<br><font color=red>LIST NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>list name must be at least 2 characters in length</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>LIST MODIFIED: $list_id</font></B>\n";

            $stmt="UPDATE osdial_lists set list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description',list_changedate='$SQLdate',scrub_dnc='$scrub_dnc',cost='$cost',web_form_address='" . mres($web_form_address) . "',web_form_address2='" . mres($web_form_address2) . "' where list_id='$list_id';";
            $rslt=mysql_query($stmt, $link);

            if ($reset_list == 'Y') {
                echo "<br><font color=$default_text>RESETTING LIST-CALLED-STATUS</font>\n";
                $stmt="UPDATE osdial_list set called_since_last_reset='N' where list_id='$list_id';";
                $rslt=mysql_query($stmt, $link);

                ### LOG RESET TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|RESET LIST CALLED   |$PHP_AUTH_USER|$ip|list_name='$list_name'|\n");
                    fclose($fp);
                }
            }
            if ($campaign_id != "$old_campaign_id") {
                echo "<br><font color=$default_text>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($old_campaign_id)</font>\n";
                $stmt="DELETE from osdial_hopper where list_id='$list_id' and campaign_id='$old_campaign_id';";
                $rslt=mysql_query($stmt, $link);
            }

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY LIST INFO    |$PHP_AUTH_USER|$ip|list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description' where list_id='$list_id'|\n");
                fclose($fp);
            }
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
        exit;
    }
    $ADD=311;	# go to list modification form below
}


######################
# ADD=511 confirmation before deletion of list
######################

if ($ADD==511) {
    echo "<font face=\"Arial,Helvetica\" color=$default_text size=2>";

    if ( (strlen($list_id) < 2) or ($LOGdelete_lists < 1) ) {
        echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
        echo "<br>List_id be at least 2 characters in length</font>\n";
    } else {
        if ($SUB==1) {
            echo "<br><B><font color=$default_text>LIST AND LEAD DELETION CONFIRMATION: $list_id</B>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=611&SUB=1&list_id=$list_id&CoNfIrM=YES\">Click here to delete list and all of its leads $list_id</a></font><br><br><br>\n";
        } else {
            echo "<br><B><font color=$default_text>LIST DELETION CONFIRMATION: $list_id</B>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=611&list_id=$list_id&CoNfIrM=YES\">Click here to delete list $list_id</a></font><br><br><br>\n";
        }
    }
    $ADD='311';		# go to campaign modification below
}

######################
# ADD=611 delete list record and all leads within it
######################

if ($ADD==611) {
    echo "<font face=\"Arial,Helvetica\" color=$default_text SIZE=2>";

    if ( ( strlen($list_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_lists < 1) ) {
        echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
        echo "<br>List_id be at least 2 characters in length</font><br>\n";
    } else {
        $stmt="DELETE from osdial_lists where list_id='$list_id' limit 1;";
        $rslt=mysql_query($stmt, $link);

        echo "<br><font color=$default_text>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($list_id)</font>\n";
        $stmt="DELETE from osdial_hopper where list_id='$list_id';";
        $rslt=mysql_query($stmt, $link);

        if ($SUB==1) {
            echo "<br><font color=$default_text>REMOVING LIST LEADS FROM $t1 TABLE</font>\n";
            $stmt="DELETE from osdial_list where list_id='$list_id';";
            $rslt=mysql_query($stmt, $link);
        }

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!!!DELETING LIST!!!!|$PHP_AUTH_USER|$ip|list_id='$list_id'|\n");
            fclose($fp);
        }
        echo "<br><B><font color=$default_text>LIST DELETION COMPLETED: $list_id</font></B>\n";
        echo "<br><br>\n";
    }
    $ADD='100';		# go to lists list
}

######################
# ADD=311 modify list info in the system
######################

if ($ADD==311) {
    if ($LOGmodify_lists==1) {
        echo "<TABLE align=center><TR><TD>\n";
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

        $stmt="SELECT * from osdial_lists where list_id='$list_id';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $campaign_id = $row[2];
        $active = $row[3];
        $list_description = $row[4];
        $list_changedate = $row[5];
        $list_lastcalldate = $row[6];
        $list_scrub_dnc = $row[7];
        $list_scrub_last = $row[8];
        $list_scrub_info = $row[9];
        $cost = $row[10];
        $web_form_address = $row[11];
        $web_form_address2 = $row[12];

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

        $stmt="SELECT data FROM configuration WHERE name='External_DNC_Active';";
        $rslt=mysql_query($stmt, $link);
        $rowd=mysql_fetch_row($rslt);
        $can_scrub_dnc = $rowd[0];


        echo "<center><br><font color=$default_text size=+1>MODIFY A LIST</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=411>\n";
        echo "<input type=hidden name=list_id value=\"$row[0]\">\n";
        echo "<input type=hidden name=old_campaign_id value=\"$row[2]\">\n";
        echo "<TABLE width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>List ID: </td><td align=left><b>$row[0]</b>$NWB#osdial_lists-list_id$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20 value=\"$row[1]\">$NWB#osdial_lists-list_name$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>List Description: </td><td align=left><input type=text name=list_description size=30 maxlength=255 value=\"$list_description\">$NWB#osdial_lists-list_description$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Per-Lead Cost: </td><td align=left><input type=text name=cost size=10 maxlength=10 value=\"" . sprintf('%3.4f',$cost) . "\">$NWB#osdial_lists-cost$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=34&campaign_id=$campaign_id\">Campaign</a>: </td><td align=left><select size=1 name=campaign_id>\n";

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
        echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$active</option></select>$NWB#osdial_lists-active$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Reset Lead-Called-Status for this list: </td><td align=left><select size=1 name=reset_list><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_lists-reset_list$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>List Change Date: </td><td align=left>$list_changedate &nbsp; $NWB#osdial_lists-list_changedate$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>List Last Call Date: </td><td align=left>$list_lastcalldate &nbsp; $NWB#osdial_lists-list_lastcalldate$NWE</td></tr>\n";
        if ($can_scrub_dnc == 'Y') {
            echo "<tr bgcolor=$oddrows><td align=right>External DNC Scrub Now: </td><td align=left><select size=1 name=scrub_dnc><option>Y</option><option selected>N</option></select>$NWB#osdial_lists-srub_dnc$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Last External Scrub: </td><td align=left>$list_scrub_last : $list_scrub_info</td></tr>\n";
        }
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 1 (campaign override): </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"$web_form_address\">$NWB#osdial_lists-web_form_address$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 2 (campaign override): </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"$web_form_address2\">$NWB#osdial_lists-web_form_address$NWE</td></tr>\n";
        echo "<tr class=tabfooter>";
        echo "<td align=center class=tabbutton>";
        echo "<input type=button name=addleads value=\"ADD LEADS\" onclick=\"window.location='admin.php?ADD=122&list_id_override=$row[0]'\">";
        echo "</td>";
        echo "<td align=center class=tabbutton>";
        echo "<input type=submit name=SUBMIT value=SUBMIT>";
        echo "</td>";
        echo "</tr>\n";
        echo "</TABLE></center>\n";

        echo "<center>\n";
        echo "<br><font color=$default_text size=+1>STATUSES WITHIN THIS LIST</font></b><br>\n";
        echo "<table bgcolor=grey width=500 cellspacing=1>\n";
        echo "  <tr class=tabheader>\n";
        echo "    <td align=center>STATUS</td>\n";
        echo "    <td align=center>STATUS NAME</td>\n";
        echo "    <td align=center>CALLED</td>\n";
        echo "    <td align=center>NOT CALLED</td>\n";
        echo "  </tr>\n";

        $leads_in_list = 0;
        $leads_in_list_N = 0;
        $leads_in_list_Y = 0;
        $stmt="SELECT status,called_since_last_reset,count(*) from osdial_list where list_id='$list_id' group by status,called_since_last_reset order by status,called_since_last_reset";
        if ($DB) echo "$stmt\n";
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);

        $o=0;
        $lead_list['count'] = 0;
        $lead_list['Y_count'] = 0;
        $lead_list['N_count'] = 0;
        while ($statuses_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
	    
            $lead_list['count'] = ($lead_list['count'] + $rowx[2]);
            if ($rowx[1] == 'N') {
                $since_reset = 'N';
                $since_resetX = 'Y';
            } else {
                $since_reset = 'Y';
                $since_resetX = 'N';
            } 
            $lead_list[$since_reset][$rowx[0]] = ($lead_list[$since_reset][$rowx[0]] + $rowx[2]);
            $lead_list[$since_reset.'_count'] = ($lead_list[$since_reset.'_count'] + $rowx[2]);
            #If opposite side is not set, it may not in the future so give it a value of zero
            if (!isset($lead_list[$since_resetX][$rowx[0]])) {
                $lead_list[$since_resetX][$rowx[0]]=0;
            }
            $o++;
        }
 
        $o=0;
        if ($lead_list['count'] > 0) {
            while (list($dispo,) = each($lead_list[$since_reset])) {

                if (eregi("1$|3$|5$|7$|9$", $o)) {
                    $bgcolor='bgcolor='.$oddrows;
                } else {
                    $bgcolor='bgcolor='.$evenrows;
                }

                if ($dispo == 'CBHOLD') {
                    $CLB="<a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">";
                    $CLE="</a>";
                } else {
                    $CLB='';
                    $CLE='';
                }

                echo "  <tr $bgcolor class=\"row font1\">\n";
                echo "    <td>$CLB$dispo$CLE</td>\n";
                echo "    <td>$statuses_list[$dispo]</td>\n";
                echo "    <td align=right>".$lead_list['Y'][$dispo]."</td>\n";
                echo "    <td align=right>".$lead_list['N'][$dispo]."</td>\n";
                echo "  </tr>\n";
                $o++;
            }
        }

        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=2>SUBTOTALS</td>\n";
        echo "    <td align=right>" . $lead_list[Y_count] . "</td>\n";
        echo "    <td align=right>" . $lead_list[N_count] . "</td>\n";
        echo "  </tr>\n";
        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=2>TOTAL</td>\n";
        echo "    <td colspan=2 align=center><b>" . $lead_list[count] . "</td>\n";
        echo "  </tr>\n";

        echo "</table></center><br>\n";
        unset($lead_list);


        echo "<center>\n";
        echo "<br><font color=$default_text size=+1>TIME ZONES WITHIN THIS LIST</font></b><br>\n";
        echo "<table bgcolor=grey width=500 cellspacing=1>\n";
        echo "  <tr class=tabheader>\n";
        echo "    <td align=center>GMT OFFSET NOW (local time)</td>\n";
        echo "    <td align=center>CALLED</td>\n";
        echo "    <td align=center>NOT CALLED</td>\n";
        echo "  </tr>\n";

        $stmt="SELECT gmt_offset_now,called_since_last_reset,count(*) from osdial_list where list_id='$list_id' group by gmt_offset_now,called_since_last_reset order by gmt_offset_now,called_since_last_reset";
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);

        $o=0;
        $plus='+';
        $lead_list['count'] = 0;
        $lead_list['Y_count'] = 0;
        $lead_list['N_count'] = 0;
        while ($statuses_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);

            $lead_list['count'] = ($lead_list['count'] + $rowx[2]);
            if ($rowx[1] == 'N') {
                $since_reset = 'N';
                $since_resetX = 'Y';
            } else {
                $since_reset = 'Y';
                $since_resetX = 'N';
            } 
            $lead_list[$since_reset][$rowx[0]] = ($lead_list[$since_reset][$rowx[0]] + $rowx[2]);
            $lead_list[$since_reset.'_count'] = ($lead_list[$since_reset.'_count'] + $rowx[2]);
            #If opposite side is not set, it may not in the future so give it a value of zero
            if (!isset($lead_list[$since_resetX][$rowx[0]])) {
                $lead_list[$since_resetX][$rowx[0]]=0;
            }
            $o++;
        }

        if ($lead_list['count'] > 0) {
            $o=0;
            while (list($tzone,) = each($lead_list[$since_reset])) {
                $LOCALzone=3600 * $tzone;
                $LOCALdate=gmdate("D M Y H:i", time() + $LOCALzone);

                if ($tzone >= 0) {
                    $DISPtzone = "$plus$tzone";
                } else {
                    $DISPtzone = "$tzone";
                }
                if (eregi("1$|3$|5$|7$|9$", $o)) {
                    $bgcolor='bgcolor='.$oddrows;
                } else {
                    $bgcolor='bgcolor='.$evenrows;
                }

                echo "  <tr $bgcolor class=\"row font1\">\n";
                echo "    <td>".$DISPtzone." &nbsp; &nbsp; ($LOCALdate)</td>\n";
                echo "    <td align=right>".$lead_list['Y'][$tzone]."</td>\n";
                echo "    <td align=right>".$lead_list['N'][$tzone]."</td>\n";
                echo "  </tr>\n";
                $o++;
            }
        }

        echo "  <tr class=tabfooter>\n";
        echo "    <td>SUBTOTALS</td>\n";
        echo "    <td align=right>" . $lead_list[Y_count] . "</td>\n";
        echo "    <td align=right>" . $lead_list[N_count] . "</td>\n";
        echo "  </tr>\n";
        echo "  <tr class=tabfooter>\n";
        echo "    <td>TOTAL</td>\n";
        echo "    <td colspan=2 align=center>" . $lead_list[count] . "</td>\n";
        echo "  </tr>\n";

        echo "</table></center><br>\n";
        unset($lead_list);



        $count_cols=30;
        $leads_in_list = 0;
        $leads_in_list_N = 0;
        $leads_in_list_Y = 0;
        $max_col_grouping = "if(called_count>" . ($count_cols - 1) . "," . $count_cols . ",called_count)";
        $stmt="SELECT status,$max_col_grouping,count(*) from osdial_list where list_id='$list_id' group by status,$max_col_grouping order by status,called_count";
        $rslt=mysql_query($stmt, $link);
        $status_called_to_print = mysql_num_rows($rslt);

        $o=0;
        $sts=0;
        $first_row=1;
        $all_called_first=1000;
        $all_called_last=0;
        while ($status_called_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $leads_in_list = ($leads_in_list + $rowx[2]);
            $count_statuses[$o]			= "$rowx[0]";
            $count_called[$o]			= "$rowx[1]";
            $count_count[$o]			= "$rowx[2]";
            $all_called_count[$rowx[1]] = ($all_called_count[$rowx[1]] + $rowx[2]);

            if ( (strlen($status[$sts]) < 1) or ($status[$sts] != "$rowx[0]") ) {
                if ($first_row) {
                    $first_row=0;
                } else {
                    $sts++;
                }
                $status[$sts] = "$rowx[0]";
                $status_called_first[$sts] = "$rowx[1]";
                if ($status_called_first[$sts] < $all_called_first) {
                    $all_called_first = $status_called_first[$sts];
                }
            }
            $leads_in_sts[$sts] = ($leads_in_sts[$sts] + $rowx[2]);
            $status_called_last[$sts] = "$rowx[1]";
            if ($status_called_last[$sts] > $all_called_last) {
                $all_called_last = $status_called_last[$sts];
            }
            $o++;
        }


        echo "<center>\n";
        echo "<br><font color=$default_text size=4>CALLED COUNTS WITHIN THIS LIST</font></b><br>\n";
        echo "<table style=\"cursor:crosshair;\" bgcolor=grey width=500 cellspacing=1>\n";
        echo "  <tr style=\"cusrsor:crosshair;\" class=tabheader>\n";
        echo "    <td style=\"cusrsor:crosshair;\" align=left>STATUS</td>\n";
        echo "    <td style=\"cusrsor:crosshair;\" align=left>STATUS&nbsp;NAME</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            $flabel = $first;
            if ($first == 30) $flabel = "30+";
            if (strlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $flabel;
            if (strlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $flabel;
            if (strlen($flabel) == 3) $flabel = '&nbsp;' . $flabel;
            echo "    <td style=\"cusrsor:crosshair;\" align=right>$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cusrsor:crosshair;\" align=right>&nbsp;&nbsp;SUB</td>\n";
        echo "  </tr>\n";
        $sts=0;
        $statuses_called_to_print = count($status);
        while ($statuses_called_to_print > $sts) {
            $Pstatus = $status[$sts];
            if (eregi("1$|3$|5$|7$|9$", $sts)) {
                $bgcolor="bgcolor=$evenrows";   $AB="bgcolor=$oddrows";
            } else {
                $bgcolor="bgcolor=$oddrows";   $AB="bgcolor=$evenrows";
            }
            #	echo "$status[$sts]|$status_called_first[$sts]|$status_called_last[$sts]|$leads_in_sts[$sts]|\n";
            #	echo "$status[$sts]|";
            echo "  <tr $bgcolor style=\"cursor:crosshair;\" class=\"row font1\">\n";
            echo "     <td style=\"cusrsor:crosshair;\" nowrap>$Pstatus</td>\n";
            echo "     <td style=\"cusrsor:crosshair;\" nowrap>$statuses_list[$Pstatus]</td>";

            $first = $all_called_first;
            while ($first <= $all_called_last) {
                $called_printed=0;
                $o=0;
                while ($status_called_to_print > $o) {
                    if ( ($count_statuses[$o] == "$Pstatus") and ($count_called[$o] == "$first") ) {
                        $lplural = '';
                        $fplural = '';
                        if ($count_count[$o] != 1) $lplural = 's';
                        if ($first != 1) $fplural = 's';
                        $clabel = $count_count[$o];
                        if (strlen($clabel) == 1) $clabel = '&nbsp;&nbsp;&nbsp;' . $count_count[$o];
                        if (strlen($clabel) == 2) $clabel = '&nbsp;&nbsp;' . $count_count[$o];
                        if (strlen($clabel) == 3) $clabel = '&nbsp;' . $count_count[$o];
                        echo "    <td style=\"cursor:crosshair;\" class=hover align=right title=\"$count_count[$o] Lead$lplural, Called $first Time$fplural, Last Dispositioned as '$Pstatus'\">$clabel</td>\n";
                        $called_printed++;
                    }
                    $o++;
                }
                if (!$called_printed) echo "    <td style=\"cursor:crosshair;\" class=hover align=right>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
                $first++;
            }
            echo "    <td style=\"cursor:crosshair;\" align=right title=\"Subtotal for '$Pstatus': $leads_in_sts[$sts]\"><b>$leads_in_sts[$sts]</b></td>\n";
            echo "  </tr>\n";

            $sts++;
        }

        echo "  <tr style=\"cusrsor:crosshair;\" class=tabfooter>";
        echo "    <td style=\"cusrsor:crosshair;\" align=left colspan=2>TOTAL</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            if ($all_called_count[$first] == 0 or $all_called_count[$first] == '') $all_called_count[$first] = '0';
            $flabel = $all_called_count[$first];
            if (strlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $all_called_count[$first];
            if (strlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $all_called_count[$first];
            if (strlen($flabel) == 3) $flabel = '&nbsp;' . $all_called_count[$first];
            echo "    <td style=\"cusrsor:crosshair;\" align=right class=right title=\"$first Lead Count Total: $all_called_count[$first] Leads\">$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cusrsor:crosshair;\" align=right title=\"Total: $leads_in_list Leads\">$leads_in_list</td>\n";
        echo "  </tr>\n";
        echo "</table></center><br>\n";


        $count_cols=30;
        $leads_in_list = 0;
        $leads_in_list_N = 0;
        $leads_in_list_Y = 0;
        $max_col_grouping = "if(cnt>" . ($count_cols - 1) . "," . $count_cols . ",cnt)";
        $stmt="SELECT stat,$max_col_grouping,count(*) FROM (select osdial_log.status AS stat,count(*) AS cnt from osdial_list JOIN osdial_log ON (osdial_list.lead_id=osdial_log.lead_id) where osdial_list.list_id='$list_id' group by osdial_log.lead_id,osdial_log.status,osdial_log.lead_id order by osdial_log.status,count(*)) AS t1 group by $max_col_grouping,stat order by stat,$max_col_grouping;";
        $rslt=mysql_query($stmt, $link);
        $status_called_to_print = mysql_num_rows($rslt);

        $o=0;
        $sts=0;
        $first_row=1;
        $all_called_first=1000;
        $all_called_last=0;
        while ($status_called_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $leads_in_list = ($leads_in_list + $rowx[2]);
            $count_statuses[$o]			= "$rowx[0]";
            $count_called[$o]			= "$rowx[1]";
            $count_count[$o]			= "$rowx[2]";
            $all_called_count[$rowx[1]] = ($all_called_count[$rowx[1]] + $rowx[2]);

            if ( (strlen($status[$sts]) < 1) or ($status[$sts] != "$rowx[0]") ) {
                if ($first_row) {
                    $first_row=0;
                } else {
                    $sts++;
                }
                $status[$sts] = "$rowx[0]";
                $status_called_first[$sts] = "$rowx[1]";
                if ($status_called_first[$sts] < $all_called_first) {
                    $all_called_first = $status_called_first[$sts];
                }
            }
            $leads_in_sts[$sts] = ($leads_in_sts[$sts] + $rowx[2]);
            $status_called_last[$sts] = "$rowx[1]";
            if ($status_called_last[$sts] > $all_called_last) {
                $all_called_last = $status_called_last[$sts];
            }
            $o++;
        }




        echo "<center>\n";
        echo "<br><font color=$default_text size=4>PER-LEAD DISPOSITION COUNTS FROM LOG</font></b><br>\n";
        echo "<table style=\"cursor:crosshair;\" bgcolor=grey width=500 cellspacing=1>\n";
        echo "  <tr style=\"cusrsor:crosshair;\" class=tabheader>\n";
        echo "    <td style=\"cusrsor:crosshair;\" align=left>STATUS</td>\n";
        echo "    <td style=\"cusrsor:crosshair;\" align=left>STATUS&nbsp;NAME</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            $flabel = $first;
            if ($first == 30) $flabel = "30+";
            if (strlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $flabel;
            if (strlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $flabel;
            if (strlen($flabel) == 3) $flabel = '&nbsp;' . $flabel;
            echo "    <td style=\"cusrsor:crosshair;\" align=right>$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cusrsor:crosshair;\" align=right>&nbsp;&nbsp;SUB</td>\n";
        echo "  </tr>\n";

        $sts=0;
        $statuses_called_to_print = count($status);
        while ($statuses_called_to_print > $sts) {
            $Pstatus = $status[$sts];
            if (eregi("1$|3$|5$|7$|9$", $sts)) {
                $bgcolor="bgcolor=$evenrows";   $AB="bgcolor=$oddrows";
            } else {
                $bgcolor="bgcolor=$oddrows";   $AB="bgcolor=$evenrows";
            }
            #	echo "$status[$sts]|$status_called_first[$sts]|$status_called_last[$sts]|$leads_in_sts[$sts]|\n";
            #	echo "$status[$sts]|";
            echo "  <tr $bgcolor style=\"cursor:crosshair;\" class=\"row font1\">\n";
            echo "     <td style=\"cusrsor:crosshair;\" nowrap>$Pstatus</td>\n";
            echo "     <td style=\"cusrsor:crosshair;\" nowrap>$statuses_list[$Pstatus]</td>";

            $first = $all_called_first;
            while ($first <= $all_called_last) {
                $called_printed=0;
                $o=0;
                while ($status_called_to_print > $o) {
                    if ( ($count_statuses[$o] == "$Pstatus") and ($count_called[$o] == "$first") ) {
                        $lplural = ',';
                        $fplural = '';
                        if ($count_count[$o] != 1) $lplural = 's, Each';
                        if ($first != 1) $fplural = 's';
                        $clabel = $count_count[$o];
                        if (strlen($clabel) == 1) $clabel = '&nbsp;&nbsp;&nbsp;' . $count_count[$o];
                        if (strlen($clabel) == 2) $clabel = '&nbsp;&nbsp;' . $count_count[$o];
                        if (strlen($clabel) == 3) $clabel = '&nbsp;' . $count_count[$o];
                        echo "    <td style=\"cursor:crosshair;\" class=hover align=right title=\"$count_count[$o] Lead$lplural Dispositioned as '$Pstatus' $first Time$fplural\">$clabel</td>\n";
                        $called_printed++;
                    }
                    $o++;
                }
                if (!$called_printed) echo "    <td style=\"cursor:crosshair;\" class=hover align=right>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
                $first++;
            }
            echo "    <td style=\"cursor:crosshair;\" align=right title=\"Subtotal for '$Pstatus': $leads_in_sts[$sts]\"><b>$leads_in_sts[$sts]</b></td>\n";
            echo "  </tr>\n";
            $sts++;
        }

        echo "  <tr style=\"cusrsor:crosshair;\" class=tabfooter>";
        echo "    <td style=\"cusrsor:crosshair;\" align=left colspan=2>TOTAL</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            if ($all_called_count[$first] == 0 or $all_called_count[$first] == '') $all_called_count[$first] = '0';
            $flabel = $all_called_count[$first];
            if (strlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $all_called_count[$first];
            if (strlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $all_called_count[$first];
            if (strlen($flabel) == 3) $flabel = '&nbsp;' . $all_called_count[$first];
            echo "    <td style=\"cusrsor:crosshair;\" align=right class=right title=\"$first Attempt Total: $all_called_count[$first] Calls\">$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cusrsor:crosshair;\" align=right title=\"Total: $leads_in_list Calls\">$leads_in_list</td>\n";
        echo "  </tr>\n";

        echo "</table></center><br>\n";





        echo "<center>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">Click here to see all CallBack Holds in this list</a><BR><BR>\n";
        echo "</center>\n";
	
        if ($LOGdelete_lists > 0) {
            echo "<br><br><a href=\"$PHP_SELF?ADD=511&list_id=$list_id\">DELETE THIS LIST</a>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=511&SUB=1&list_id=$list_id\">DELETE THIS LIST AND ITS LEADS</a> (WARNING: Will damage call-backs made in this list!)\n";
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
        exit;
    }
}


######################
# ADD=811 find all callbacks on hold within a List
######################
if ($ADD==811) {
	if ($LOGmodify_lists==1) {
		if ($SUB==89) {
		    $stmt="UPDATE osdial_callbacks SET status='INACTIVE' where list_id='$list_id' and status='LIVE' and callback_time < '$past_month_date';";
		    $rslt=mysql_query($stmt, $link);
		    echo "<br>list($list_id) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899) {
		    $stmt="UPDATE osdial_callbacks SET status='INACTIVE' where list_id='$list_id' and status='LIVE' and callback_time < '$past_week_date';";
		    $rslt=mysql_query($stmt, $link);
		    echo "<br>list($list_id) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
    $CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=811&SUB=89&list_id=$list_id\"><font color=$default_text>Remove LIVE Callbacks older than one month for this list</font></a><BR><a href=\"$PHP_SELF?ADD=811&SUB=899&list_id=$list_id\"><font color=$default_text>Remove LIVE Callbacks older than one week for this list</font></a><BR>";

    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$CBquerySQLwhere = "and list_id='$list_id'";

    echo "<center><br><br><font size=4 color=$default_text>LIST CALLBACK HOLD LISTINGS: $list_id<br><br></font></center>\n";
    $oldADD = "ADD=811&list_id=$list_id";
    $ADD='82';
}


######################
# ADD=82 display all callbacks on hold
######################
if ($ADD==82) {

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

    echo "<TABLE width=100%><TR><TD>\n";
    echo "<center><table width=$section_width cellspacing=1 cellpadding=0 bgcolor=grey>\n";
    echo "  <tr class=tabheader>\n";
    echo "    <td>LEAD</td>\n";
    echo "    <td>LIST</td>\n";
    echo "    <td>CAMPAIGN</td>\n";
    echo "    <td><a href=\"$PHP_SELF?$oldADD&$ENDATElink\">ENTRY DATE</a></td>\n";
    echo "    <td>CALLBACK DATE</td>\n";
    echo "    <td><a href=\"$PHP_SELF?$oldADD&$USERlink\">USER</a></td>\n";
    echo "    <td>RECIPIENT</td>\n";
    echo "    <td>STATUS</td>\n";
    echo "    <td><a href=\"$PHP_SELF?$oldADD&$GROUPlink\">GROUP</a></td>\n";
    echo "  </tr>\n";

	$o=0;
	while ($cb_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}
		echo "  <tr $bgcolor class=\"row font1\">\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[1]\" target=\"_blank\">$row[1]</a></td>\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=311&list_id=$row[2]\">$row[2]</a></td>\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[3]\">$row[3]</a></td>\n";
		echo "    <td>$row[5]</td>\n";
		echo "    <td>$row[6]</td>\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=3&user=$row[8]\">$row[8]</a></td>\n";
		echo "    <td>$row[9]</td>\n";
		echo "    <td>$row[4]</td>\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=311111&user_group=$row[11]\">$row[11]</a></td>\n";
		echo "  </tr>\n";
		$o++;
	}

    echo "  <tr class=tabfooter>";
    echo "    <td colspan=9></td>";
    echo "  </tr>";
    echo "</TABLE></center>\n";

    echo "<center>$CBinactiveLINK</center>";
}



######################
# ADD=100 display all lists
######################
if ($ADD==100) {
    echo "<TABLE align=center><TR><TD>\n";
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

    $camp = get_variable('camp');
    $campSQL = '';
    if ($camp != '') $campSQL = "AND campaign_id='$camp'";

    $let = get_variable('let');
    $letSQL = '';
    if ($let != '') $letSQL = "AND (campaign_id LIKE '$let%' OR list_id LIKE '$let%')";

    $dispact = get_variable('dispact');
    $dispactSQL = '';
    if ($dispact == 1) $dispactSQL = "AND active='Y'";

    $stmt="SELECT * from osdial_lists WHERE 1=1 $campSQL $letSQL $dispactSQL order by list_id";
    $rslt=mysql_query($stmt, $link);
    $people_to_print = mysql_num_rows($rslt);

    echo "<center><br><font color=$default_text size=+1>LISTS</font><br>";
    if ($people_to_print > 20) {
        echo "<font color=$default_text size=-1>";
        if ($dispact == '1') {
            echo "<a href=\"$PHP_SELF?ADD=100&camp=$camp&let=$let&dispact=\">(Show Inactive)</a>";
        } else {
            echo "<a href=\"$PHP_SELF?ADD=100&camp=$camp&let=$let&dispact=1\">(Hide Inactive)</a>";
        }
        echo "</font><br>\n";
    }
    echo "<br>\n";
    echo "<font size=-1 color=$default_text>&nbsp;|&nbsp;";
    echo "<a href=\"$PHP_SELF?ADD=$ADD&dispact=$dispact&let=\">-ALL-</a>&nbsp;|&nbsp;";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;";
    foreach (range('A','Z') as $slet) {
        echo (($let == "$slet") ? $slet : "<a href=\"$PHP_SELF?ADD=$ADD&dispact=$dispact&let=$slet\">$slet</a>") . "&nbsp;|&nbsp;";
    }
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;";
    foreach (range('0','9') as $snum) {
        echo (($let == "$snum") ? $snum : "<a href=\"$PHP_SELF?ADD=$ADD&dispact=$dispact&let=$snum\">$snum</a>") . "&nbsp;|&nbsp;";
    }
    echo "</font><br>\n";
    echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "<tr class=tabheader>";
    echo "<td>ID</td>";
    echo "<td>NAME</td>";
    echo "<td>CAMPAIGN</td>";
    echo "<td>DESCRIPTION</td>";
    echo "<td align=center>MODIFIED</td>";
    echo "<td align=center>ACTIVE</td>";
    echo "<td align=center colspan=3>LINKS</td>";
    $o=0;
    while ($people_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        if (eregi("1$|3$|5$|7$|9$", $o)) {
            $bgcolor='bgcolor='.$oddrows;
        } else {
            $bgcolor='bgcolor='.$evenrows;
        }
        echo "  <tr $bgcolor class=\"row font1\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">$row[0]</a></td>\n";
        echo "    <td>$row[1]</td>\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=100&camp=$row[2]&dispact=$dispact\">$row[2]</a></td>\n";
        echo "    <td>$row[4]</td>\n";
        echo "    <td align=center>$row[5]</td>\n";
        echo "    <td align=center>$row[3]</td>\n";
        #echo "    <td>$row[7]</td>\n";
        echo "    <td colspan=3 align=center><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">MODIFY</a>";
        if ($LOGuser_leve > 8) {
            echo " | <a href=\"$PHP_SELF?ADD=131&list_id=$row[0]\">EXPORT</a>";
        }
        echo " | <a href=\"$PHP_SELF?ADD=122&list_id_override=$row[0]\">ADD LEADS</a></td>\n";
        echo "  </tr>\n";
        $o++;
    }

    echo "  <tr class=tabfooter>\n";
    echo "    <td colspan=9></td>\n";
    echo "  </tr>\n";
    echo "</TABLE></center>\n";
}






?>
