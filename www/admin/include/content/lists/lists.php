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
# ADD=1122 advanced lead search
######################

if ($ADD==1122) {

    if ($LOGmodify_lists==1) {
        echo "<table align=center><tr><td>\n";
        echo "<font face=\"arial,helvetica\" color=$default_text size=2>";
        echo "<center><br><font color=$default_text size=+1>ADVANCED LEAD SEARCH</font>\n";

        $last_name = get_variable("last_name");
        $first_name = get_variable("first_name");
        $phone_number = get_variable("phone_number");
        $phone_search_alt = get_variable("phone_search_alt");
        $lead_id = get_variable("lead_id");
        $city = get_variable("city");
        $postal_code = get_variable("postal_code");
        $email = get_variable("email");
        $custom1 = get_variable("custom1");
        $custom2 = get_variable("custom2");
        $external_key = get_variable("external_key");
        $entry_date_start = get_variable("entry_date_start");
        $entry_date_end = get_variable("entry_date_end");
        $modify_date_start = get_variable("modify_date_start");
        $modify_date_end = get_variable("modify_date_end");
        $lastcall_date_start = get_variable("lastcall_date_start");
        $lastcall_date_end = get_variable("lastcall_date_end");
        $use_osdial_log = get_variable("use_osdial_log");

        $orig_entry_date_start = "";
        $orig_entry_date_end = "";
        if ($entry_date_start != "" and $entry_date_end == "") $entry_date_end = $entry_date_start;
        if ($entry_date_start != "") {
            $entry_date_start .= " 00:00:00";
            $entry_date_end .= " 23:59:59";
            $tmp1 = explode(" ",$entry_date_start);
            $orig_entry_date_start = $tmp1[0];
            $tmp2 = explode(" ",$entry_date_end);
            $orig_entry_date_end = $tmp2[0];
        }

        $orig_modify_date_start = "";
        $orig_modify_date_end = "";
        if ($modify_date_start != "" and $modify_date_end == "") $modify_date_end = $modify_date_start;
        if ($modify_date_start != "") {
            $modify_date_start .= " 00:00:00";
            $modify_date_end .= " 23:59:59";
            $tmp1 = explode(" ",$modify_date_start);
            $orig_modify_date_start = $tmp1[0];
            $tmp2 = explode(" ",$modify_date_end);
            $orig_modify_date_end = $tmp2[0];
        }

        $orig_lastcall_date_start = "";
        $orig_lastcall_date_end = "";
        if ($lastcall_date_start != "" and $lastcall_date_end == "") $lastcall_date_end = $lastcall_date_start;
        if ($lastcall_date_start != "") {
            $lastcall_date_start .= " 00:00:00";
            $lastcall_date_end .= " 23:59:59";
            $tmp1 = explode(" ",$lastcall_date_start);
            $orig_lastcall_date_start = $tmp1[0];
            $tmp2 = explode(" ",$lastcall_date_end);
            $orig_lastcall_date_end = $tmp2[0];
        }

        $phone_number = ereg_replace("[^0-9]","",$phone_number);

        # groups
        $campaigns = get_variable("campaigns");
        $lists = get_variable("lists");
        $statuses = get_variable("statuses");
        $agents = get_variable("agents");
        $states = get_variable("states");
        $timezones = get_variable("timezones");
        $sources = get_variable("sources");
        $vendor_codes = get_variable("vendor_codes");
        $fields = get_variable("fields");

        $numresults = get_variable("numresults");
        if ($numresults == "" or $numresults == 0)
            $numresults = 100;
        $page = get_variable("page");
        if ($page < 1)
            $page = 1;
        $count = get_variable("count");
        if ($page == 1)
            $count = 0;
        $sort = get_variable("sort");
        if ($sort == "")
            $sort = "called_count";
        $direction = get_variable("direction");
        if ($direction == "")
            $direction = "ASC";

        $searchWHR = " WHERE 1=1";

        $pageURL ="?ADD=$ADD&last_name=$last_name&first_name=$first_name&phone_number=$phone_number&phone_search_alt=$phone_search_alt&lead_id=$lead_id&city=$city&postal_code=$postal_code&email=$email";
        $pageURL.="&entry_date_start=$orig_entry_date_start&entry_date_end=$orig_entry_date_end&modify_date_start=$orig_modify_date_start&modify_date_end=$orig_modify_date_end";
        $pageURL.="&lastcall_date_start=$orig_lastcall_date_start&lastcall_date_end=$orig_lastcall_date_end&use_osdial_log=$use_osdial_log";
        $pageURL.="&custom1=$custom1&custom2=$custom2&external_key=$external_key&numresults=$numresults";


        if ($phone_number) {
            $notac = "%";
            if (strlen($phone_number) == 3) $notac="";
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.phone_number LIKE '" . $notac . mysql_real_escape_string($phone_number) . "%'";
            } elseif ($phone_search_alt) {
                $searchWHR .= " AND (osdial_list.phone_number LIKE '" . $notac . mysql_real_escape_string($phone_number) . "%'";
                $searchWHR .= " OR osdial_list.alt_phone LIKE '" . $notac . mysql_real_escape_string($phone_number) . "%'";
                $searchWHR .= " OR osdial_list.address3 LIKE '" . $notac . mysql_real_escape_string($phone_number) . "%')";
            } else {
                $searchWHR .= " AND osdial_list.phone_number LIKE '" . $notac . mysql_real_escape_string($phone_number) . "%'";
            }
        }
        if ($lead_id) {
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.lead_id='" . mysql_real_escape_string($lead_id) . "'";
            } else {
                $searchWHR .= " AND osdial_list.lead_id='" . mysql_real_escape_string($lead_id) . "'";
            }
        }
        if ($lastcall_date_start) {
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.call_date BETWEEN '" . mysql_real_escape_string($lastcall_date_start) . "' AND '" . mysql_real_escape_string($lastcall_date_end) . "'";
            } else {
                $searchWHR .= " AND osdial_list.last_local_call_time BETWEEN '" . mysql_real_escape_string($lastcall_date_start) . "' AND '" . mysql_real_escape_string($lastcall_date_end) . "'";
            }
        }

        ### process campaigns group
        $campaignIN = "";
        foreach ($campaigns as $campaign) {
            if ($campaign != "") {
                $pageURL .= "&campaigns[]=$campaign";
                $campaignIN .= "'" . mysql_real_escape_string($campaign) . "',";
            }
        }
        if ($campaignIN != "") {
            $campaignIN = rtrim($campaignIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.campaign_id IN ($campaignIN)";
            } else {
                $searchWHR .= " AND osdial_lists.campaign_id IN ($campaignIN)";
            }
        }


        ### process lists group
        $listIN = "";
        foreach ($lists as $list) {
            if ($list != "") {
                $pageURL .= "&lists[]=$list";
                $listIN .= "'" . mysql_real_escape_string($list) . "',";
            }
        }
        if ($listIN != "") {
            $listIN = rtrim($listIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.list_id IN ($listIN)";
            } else {
                $searchWHR .= " AND osdial_list.list_id IN ($listIN)";
            }
        }


        ### process statuses group
        $statusIN = "";
        foreach ($statuses as $status) {
            if ($status != "") {
                $pageURL .= "&statuses[]=$status";
                $statusIN .= "'" . mysql_real_escape_string($status) . "',";
            }
        }
        if ($statusIN != "") {
            $statusIN = rtrim($statusIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.status IN ($statusIN)";
            } else {
                $searchWHR .= " AND osdial_list.status IN ($statusIN)";
            }
        }


        ### process agents group
        $agentIN = "";
        foreach ($agents as $agent) {
            if ($agent != "") {
                $pageURL .= "&agents[]=$agent";
                $agentIN .= "'" . mysql_real_escape_string($agent) . "',";
            }
        }
        if ($agentIN != "") {
            $agentIN = rtrim($agentIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.user IN ($agentIN)";
            } else {
                $searchWHR .= " AND osdial_list.user IN ($agentIN)";
            }
        }


        ### process states group
        $stateIN = "";
        foreach ($states as $state) {
            if ($state != "") {
                $pageURL .= "&states[]=$state";
                $stateIN .= "'" . mysql_real_escape_string($state) . "',";
            }
        }
        if ($stateIN != "") {
            $stateIN = rtrim($stateIN,",");
            $searchWHR .= " AND osdial_list.state IN ($stateIN)";
        }


        ### process sources group
        $sourceIN = "";
        foreach ($sources as $source) {
            if ($source != "") {
                $pageURL .= "&sources[]=$source";
                $sourceIN .= "'" . mysql_real_escape_string($source) . "',";
            }
        }
        if ($sourceIN != "") {
            $sourceIN = rtrim($sourceIN,",");
            $searchWHR .= " AND osdial_list.source_id IN ($sourceIN)";
        }

        
        ### process vendor_codes group
        $vendor_codeIN = "";
        foreach ($vendor_codes as $vendor_code) {
            if ($vendor_code != "") {
                $pageURL .= "&vendor_codes[]=$vendor_code";
                $vendor_codeIN .= "'" . mysql_real_escape_string($vendor_code) . "',";
            }
        }
        if ($vendor_codeIN != "") {
            $vendor_codeIN = rtrim($vendor_codeIN,",");
            $searchWHR .= " AND osdial_list.vendor_lead_code IN ($vendor_codeIN)";
        }

        if ($last_name)    $searchWHR .= " AND osdial_list.last_name LIKE '%" . mysql_real_escape_string($last_name) . "%'";
        if ($first_name)   $searchWHR .= " AND osdial_list.first_name LIKE '%" . mysql_real_escape_string($first_name) . "%'";
        if ($city)         $searchWHR .= " AND osdial_list.city LIKE '%" . mysql_real_escape_string($city) . "%'";
        if ($postal_code)  $searchWHR .= " AND osdial_list.postal_code LIKE '" . mysql_real_escape_string($postal_code) . "%'";
        if ($email)        $searchWHR .= " AND osdial_list.email LIKE '%" . mysql_real_escape_string($email) . "%'";
        if ($external_key) $searchWHR .= " AND osdial_list.external_key LIKE '%" . mysql_real_escape_string($external_key) . "%'";
        if ($custom1)      $searchWHR .= " AND osdial_list.custom1 LIKE '%" . mysql_real_escape_string($custom1) . "%'";
        if ($custom2)      $searchWHR .= " AND osdial_list.custom2 LIKE '%" . mysql_real_escape_string($custom2) . "%'";

        if ($entry_date_start)  $searchWHR .= " AND osdial_list.entry_date BETWEEN '" . mysql_real_escape_string($entry_date_start) . "' AND '" . mysql_real_escape_string($entry_date_end) . "'";
        if ($modify_date_start) $searchWHR .= " AND osdial_list.modify_date BETWEEN '" . mysql_real_escape_string($modify_date_start) . "' AND '" . mysql_real_escape_string($modify_date_end) . "'";


        ### process timezones group
        $timezoneIN = "";
        $timezoneCNTIN = "";
        foreach ($timezones as $timezone) {
            if ($timezone != "") {
                $pageURL .= "&timezones[]=$timezone";
                $timezoneIN .= mysql_real_escape_string($timezone) . ",";
	            $isdst = date("I");
                $timezoneDST = $timezone;
                if ($isdst) $timezoneDST += 1;
                $timezoneCNTIN .= mysql_real_escape_string($timezoneDST) . ",";
            }
        }
        if ($timezoneIN != "") {
            $timezoneIN = rtrim($timezoneIN,",");
            $timezoneCNTIN = rtrim($timezoneCNTIN,",");
            $searchTZWHR .= " AND coalesce(osdial_postal_code_groups.GMT_offset,osdial_phone_code_groups.GMT_offset) IN ($timezoneIN)";
            $countWHR .= " AND osdial_list.gmt_offset_now IN ($timezoneCNTIN)";
        }

        $searchFLD = '';
        $field_cnt = 0;
        $field_all = 0;
        $status_found = 0;
        $fieldJOIN = '';

        foreach ($fields as $field) {
            if ($field == "*") {
                $field_all = 1;
                $status_found++;
            } elseif ($field == "status") {
                $status_found++;
            } elseif ($field == "campaign_id" or $field == "lead_id" or $field == "list_id" or $field == "user" or $field == "phone_number") {
                if ($use_osdial_log) {
                    $searchFLD .= "osdial_log." . $field . ",";
                } else {
                    $searchFLD .= "osdial_lists." . $field . ",";
                }
            } else {
                $searchFLD .= "osdial_list." . $field . ",";
            }
            $field_cnt++;
        }

        if ($field_cnt == 0 or $field_all == 1) {
            $searchFLD = "osdial_list.*,";
            if ($use_osdial_log) {
                $searchFLD .= "osdial_log.*,";
            } else {
                $searchFLD .= "osdial_lists.campaign_id,";
            }
        } elseif ($use_osdial_log and $field_cnt > 0) {
            $searchFLD .= "osdial_log.call_date,osdial_log.length_in_sec,";
        }

        if ($status_found) {
            if ($use_osdial_log) {
                    $fieldJOIN = " LEFT JOIN osdial_statuses ON (osdial_log.status=osdial_statuses.status) ";
                    $searchFLD .= "osdial_log.status,";
            } else {
                    $fieldJOIN = " LEFT JOIN osdial_statuses ON (osdial_list.status=osdial_statuses.status) ";
                    $searchFLD .= "osdial_list.status,";
            }
            $searchFLD .= "osdial_statuses.status_name,";
        }

        $searchFLD = rtrim($searchFLD,",");


        $numresults_label['25']   = "25/page";
        $numresults_label['50']   = "50/page";
        $numresults_label['100']  = "100/page";
        $numresults_label['200']  = "200/page";
        $numresults_label['500']  = "500/page";
        $numresults_label['1000'] = "1000/page";

        $sort_label['list_id']     = "List#";
        $sort_label['lead_id']     = "Lead#";
        $sort_label['status']      = "Status";
        $sort_label['phone_number']= "Phone Number";
        $sort_label['last_name']   = "Last Name";
        $sort_label['city']        = "City";
        $sort_label['state']       = "State";
        $sort_label['postal_code'] = "ZIP/PostalCode";
        $sort_label['user']        = "Agent";
        $sort_label['vendor_lead_code'] = "Vendor ID";
        $sort_label['custom1']     = "Custom1";
        $sort_label['gmt_offset_now'] = "Timezone";
        $sort_label['entry_date']  = "Entry Date";
        $sort_label['modify_date'] = "Modify Date";
        $sort_label['called_count'] = "Attempts";
        $sort_label['last_local_call_time']      = "Last Local Call Time";
        $sort_label['post_date']      = "Post Dates";

        $direction_label['ASC']= "Ascending";
        $direction_label['DESC']= "Descending";

        $timezone_label['AKST'] = "-9";
        $timezone_label['AST'] = "-4";
        $timezone_label['CST'] = "-6";
        $timezone_label['EST'] = "-5";
        $timezone_label['HAST'] = "-10";
        $timezone_label['MST'] = "-7";
        $timezone_label['NST'] = "-3.5";
        $timezone_label['PST'] = "-8";
        $timezone_label['PHT'] = "8";

        $tz_revlabel['8'] = "PHT";
        $tz_revlabel['0'] = "0";
        $tz_revlabel['-1'] = "-1";
        $tz_revlabel['-2'] = "-2";
        $tz_revlabel['-3'] = "-3";
        $tz_revlabel['-3.5'] = "NST";
        $tz_revlabel['-4'] = "AST";
        $tz_revlabel['-5'] = "EST";
        $tz_revlabel['-6'] = "CST";
        $tz_revlabel['-7'] = "MST";
        $tz_revlabel['-8'] = "PST";
        $tz_revlabel['-9'] = "AKST";
        $tz_revlabel['-10'] = "HAST";

        $field_label['*'] = '-- ALL --';
        $field_label['lead_id'] = '';
        $field_label['entry_date'] = '';
        $field_label['modify_date'] = '';
        $field_label['status'] = '';
        $field_label['user'] = '';
        $field_label['vendor_lead_code'] = '';
        $field_label['source_id'] = '';
        $field_label['list_id'] = '';
        $field_label['campaign_id'] = '';
        $field_label['gmt_offset_now'] = '';
        $field_label['called_since_last_reset'] = '';
        $field_label['phone_code'] = '';
        $field_label['phone_number'] = '';
        $field_label['title'] = '';
        $field_label['first_name'] = '';
        $field_label['middle_initial'] = '';
        $field_label['last_name'] = '';
        $field_label['address1'] = '';
        $field_label['address2'] = '';
        $field_label['address3'] = '';
        $field_label['city'] = '';
        $field_label['state'] = '';
        $field_label['province'] = '';
        $field_label['postal_code'] = '';
        $field_label['country_code'] = '';
        $field_label['gender'] = '';
        $field_label['date_of_birth'] = '';
        $field_label['alt_phone'] = '';
        $field_label['email'] = '';
        $field_label['custom1'] = '';
        $field_label['comments'] = '';
        $field_label['called_count'] = '';
        $field_label['custom2'] = '';
        $field_label['external_key'] = '';
        $field_label['last_local_call_time'] = '';
        $field_label['cost'] = '';
        $field_label['post_date'] = '';



        echo "<style type=text/css> content {vertical-align:center}</style>\n";
        echo "<br><br><center>\n";
        echo "<form method=post name=search action=\"$PHP_SELF\">\n";
        echo "<input type=hidden name=ADD value=1122>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";

        echo "<table width=$section_width cellspacing=0 bgcolor=$oddrows>\n";
        echo "  <tr>\n";
        echo "    <td colspan=4>\n";
        echo "      <br><center><font color=$default_text>Enter any combination of the following</font></center><br>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td width=25% align=right><font size=2>Last Name</font></td>\n";
        echo "    <td width=25% align=left><input type=text name=last_name value=\"$last_name\" size=20 maxlength=30></td>\n";
        echo "    <td width=25% align=right><font size=2>Lead ID</font></td>\n";
        echo "    <td width=25% align=left><input type=text name=lead_id value=\"$lead_id\" size=10 maxlength=10></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>First Name</font></td>\n";
        echo "    <td align=left><input type=text name=first_name value=\"$first_name\" size=20 maxlength=30></td>\n";
        echo "    <td align=right><font size=2>AreaCode or PhoneNumber</font></td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=phone_number value=\"$phone_number\" size=10 maxlength=20>\n";
        if ($phone_search_alt == 1) $check = " checked";
        echo "      <input type=checkbox name=phone_search_alt id=phone_seach_alt value=1$check> <font size=1><label for=phone_search_alt>Alternates</label></font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>City</font></td>\n";
        echo "    <td align=left><input type=text name=city value=\"$city\" size=20 maxlength=50></td>\n";
        echo "    <td align=right><font size=2>ZIP / Postal Code</font></td>\n";
        echo "    <td align=left><input type=text name=postal_code value=\"$postal_code\" size=10 maxlength=10></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Email</font></td>\n";
        echo "    <td align=left><input type=text name=email value=\"$email\" size=20 maxlength=70></td>\n";
        echo "    <td align=right><font size=2>External Key</font></td>\n";
        echo "    <td align=left><input type=text name=external_key value=\"$external_key\" size=10 maxlength=100></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Custom1</font></td>\n";
        echo "    <td align=left><input type=text name=custom1 value=\"$custom1\" size=10 maxlength=255></td>\n";
        echo "    <td align=right><font size=2>Custom2</font></td>\n";
        echo "    <td align=left><input type=text name=custom2 value=\"$custom2\" size=10 maxlength=255></td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <td colspan=4><br></td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Entry Date</font></td>\n";
        echo "    <td align=left colspan=2><font size=2><input type=text name=entry_date_start value=\"$orig_entry_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"> to <input type=text name=entry_date_end value=\"$orig_entry_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"></font></td>\n";
        $fieldOPTS="";
        foreach ($field_label as $k => $v) {
            $sel="";
            foreach ($fields as $field) {
                if ($k != "" and $k == $field) {
                    $sel=" selected";
                }
            }
            if ($v == "") $v = $k;
            if ($k != "") $fieldOPTS .= "        <option value=\"" . $k . "\"$sel>" . $v . "</option>\n";
        }
        if ($LOGuser_level > 8) {
            echo "    <td align=center valign=top rowspan=4>\n";
            echo "      <font size=2>CSV Export Fields</font><br>\n";
            echo "      <select name=fields[] size=5 multiple>\n";
            echo $fieldOPTS;
            echo "      </select>\n";
            echo "    </td>\n";
        }
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Modified Date</font></td>\n";
        echo "    <td align=left colspan=3><font size=2><input type=text name=modify_date_start value=\"$orig_modify_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"> to <input type=text name=modify_date_end value=\"$orig_modify_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"></font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Last Call Date</font></td>\n";
        echo "    <td align=left colspan=3><font size=2>\n";
        echo "      <input type=text name=lastcall_date_start value=\"$orig_lastcall_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"> to <input type=text name=lastcall_date_end value=\"$orig_lastcall_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"></font>\n";
        if ($use_osdial_log == 1) $check = " checked";
        echo "  </td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <td>\n";
        echo "    </td>\n";
        echo "    <td colspan=3 align=left>\n";
        echo "      <input type=checkbox name=use_osdial_log id=use_osdial_log value=1$check> <font size=1><label for=use_osdial_log>Output Lead History (Must Enter Call Date)</label></font>\n";
        echo "      <br><br>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <td align=center>\n";
        echo "      <font size=2>Campaigns</font><br>\n";
        echo "      <select name=campaigns[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_campaigns', 'campaign_id,campaign_name');
        echo format_select_options($krh, 'campaign_id', 'campaign_id', $campaigns, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>Lists</font><br>\n";
        echo "      <select name=lists[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_lists', 'list_id,list_name,campaign_id');
        echo format_select_options($krh, 'list_id', 'list_name', $lists, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>Statuses</font><br>\n";
        echo "      <select name=statuses[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_statuses', 'status,status_name');
        $krh2 = get_krh($link, 'osdial_campaign_statuses', 'status,status_name');
        foreach ($krh2 as $k => $v) {
            $krh[$k] = $v;
        }
        echo format_select_options($krh, 'status', 'status_name', $statuses, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>Agents</font><br>\n";
        echo "      <select name=agents[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_users', 'user,full_name');
        echo format_select_options($krh, 'user', 'full_name', $agents, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        $agents_label = Array();
        foreach ($krh as $k => $v) {
            $agents_label[$k] = $v['full_name'];
        }

        echo "  <tr>\n";
        echo "    <td colspan=4><br></td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>States</font><br>\n";
        echo "      <select name=states[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='states'");
        echo format_select_options($krh, 'group_value', 'group_value', $states, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        $timezoneOPTS="";
        $s=0;
        foreach ($timezone_label as $k => $v) {
            $sel="";
            foreach ($timezones as $timezone) {
                if ($k != "" and $v == $timezone) {
                    $sel=" selected";
                    $s++;
                }
            }
            if ($k != "") $timezoneOPTS .= "        <option value=\"" . $v . "\"$sel>" . $k . " (" . $v . ")</option>\n";
        }
        $sel="";
        if ($s==0) $sel=" selected";
        echo "    <td align=center>\n";
        echo "      <font size=2>TimeZones</font><br>\n";
        echo "      <select name=timezones[] size=5 multiple>\n";
        echo "        <option value=\"\"$sel>-- ALL --</option>\n";
        echo $timezoneOPTS;
        echo "      </select>\n";
        echo "    </td>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>Sources</font><br>\n";
        echo "      <select name=sources[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='lead_source_id'", "1000");
        echo format_select_options($krh, 'group_value', 'group_value', $sources, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>Vendor Codes</font><br>\n";
        echo "      <select name=vendor_codes[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='lead_vendor_lead_code'", "1000");
        echo format_select_options($krh, 'group_value', 'group_value', $vendor_codes, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";


        echo "  <tr>\n";
        echo "    <td align=center colspan=4><br><font size=2>Results</font>";
        echo "      <select name=numresults size=1>\n";
        foreach ($numresults_label as $k => $v) {
            $sel="";
            if ($numresults==$k) $sel=" selected";
            echo "        <option value=\"$k\"$sel>$v</option>\n";
        }
        echo "      </select>\n";
        echo "      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2>Sort By</font>";
        echo "      <select name=sort size=1>\n";
        foreach ($sort_label as $k => $v) {
            $sel="";
            if ($sort==$k) $sel=" selected";
            echo "        <option value=\"$k\"$sel>$v</option>\n";
        }
        echo "      </select>\n";
        echo "      <select name=direction size=1>\n";
        foreach ($direction_label as $k => $v) {
            $sel="";
            if ($direction==$k) $sel=" selected";
            echo "        <option value=\"$k\"$sel>$v</option>\n";
        }
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <th colspan=4><center><input type=submit name=submit value=SUBMIT></b></center><br></th>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</form>\n";



        if ($use_osdial_log) {
            $mainTBL = "osdial_log JOIN osdial_list ON (osdial_log.lead_id=osdial_list.lead_id)";
        } else {
            $mainTBL = "osdial_list";
        }
        $countTBL = " FROM " . $mainTBL ." JOIN osdial_lists ON (osdial_lists.list_id=osdial_list.list_id)";
        $searchTBL = " FROM " . $mainTBL ." JOIN osdial_lists ON (osdial_lists.list_id=osdial_list.list_id) LEFT JOIN osdial_postal_code_groups ON (osdial_postal_code_groups.country_code=osdial_list.phone_code AND osdial_postal_code_groups.postal_code=osdial_list.postal_code) LEFT JOIN osdial_phone_code_groups ON (osdial_phone_code_groups.country_code=osdial_list.country_code AND osdial_phone_code_groups.areacode=left(osdial_list.phone_number,3))" . $fieldJOIN;

        #$countSQL  = "SELECT count(*) " . $searchSQL . ";";
        $countSQL = "SELECT count(*)" . $countTBL . $searchWHR . $countWHR . ";";

        if ($count==0) {
            #echo "<br>$countSQL";
            if ($DB) echo "\n\n$countSQL\n\n<br>";
            $rslt=mysql_query($countSQL, $link);
            $row=mysql_fetch_row($rslt);
            $searchCount = $row[0];
        } else {
            $searchCount = $count;
        }

        # Get the number of pages needed to paginate the results.
        $pages = $searchCount / $numresults;
        if (($pages - round($pages)) != 0) $pages = (round($pages) + 1);
        if ($page > $pages) $page = $pages;


        $searchDone=1;
        while ($searchDone) {
            $searchSQL = "SELECT STRAIGHT_JOIN " . $searchFLD . $searchTBL . $searchWHR . $searchTZWHR;
            if ($field_cnt == 0) $searchSQL .= " ORDER BY " . $sort  . " " . $direction . " LIMIT " . (($page - 1) * $numresults) . ", " . $numresults;
            $searchSQL .= ";";

            #echo "<br>$searchSQL<br>";
            if ($DB) echo "\n\n$searchSQL\n\n";
            $rslt=mysql_query($searchSQL, $link);
            $results_to_print = mysql_num_rows($rslt);
            if ($page > 1 and $results_to_print == 0) {
                $page -= 1;
                $pages -= 1;
            } else {
                $searchDone=0;
                if ($results_to_print < $numresults) {
                    $pages = $page;
                    $searchCount = ((($page - 1) * $numresults) + $results_to_print);
                    if ($searchCount < 1) $searchCount=0;
                }
                echo "<br><br><br><div id=\"advsearch\"><font color=$default_text size=3><b>Records Found:&nbsp;" . $searchCount . "</b></font></div>";
            }
        }

        $paginate = "";
        if ($results_to_print > 0 and $field_cnt == 0) {
            echo "<div id=\"advsearch\"><font color=$default_text size=3><b>Displaying:&nbsp;" . ((($page - 1) * $numresults) + 1) . " through " . ((($page - 1) * $numresults) + $results_to_print) . "</b></font></div>";
            $paginate .= "<font color=$default_text size=2>\n";
            if ($page == 1) {
                $paginate .= "<font color=darkgrey>";
                $paginate .= "&lt;&lt;&lt; First";
                $paginate .= "&nbsp;&nbsp;&nbsp;";
                $paginate .= "&lt;&lt; Previous";
                $paginate .= "</font>";
            } else {
                $paginate .= "<a href=\"" . $pageURL . "&sort=$sort&direction=$direction&page=1&count=" . $searchCount . "#advsearch\">&lt;&lt;&lt; First</a>";
                $paginate .= "&nbsp;&nbsp;&nbsp;";
                $paginate .= "<a href=\"" . $pageURL . "&sort=$sort&direction=$direction&page=" . ($page - 1) . "&count=" . $searchCount . "#advsearch\">&lt;&lt; Previous</a>";
            }
            $paginate .= "&nbsp;&nbsp;&nbsp;";
            $paginate .= "[<b>$page</b> of $pages]";
            #$paginate .= "[<b>$page</b>]";
            $paginate .= "&nbsp;&nbsp;&nbsp;";
            if ($page == $pages ) {
                $paginate .= "<font color=darkgrey>";
                $paginate .= "Next &gt;&gt;";
                $paginate .= "&nbsp;&nbsp;&nbsp;";
                $paginate .= "Last &gt;&gt;&gt;";
                $paginate .= "</font>";
            } else {
                $paginate .= "<a href=\"" . $pageURL . "&sort=$sort&direction=$direction&page=" . ($page + 1) . "&count=" . $searchCount . "#advsearch\">Next &gt;&gt;</a>";
                $paginate .= "&nbsp;&nbsp;&nbsp;";
                $paginate .= "<a href=\"" . $pageURL . "&sort=$sort&direction=$direction&page=" . ($pages) . "&count=" . $searchCount . "#advsearch\">Last &gt;&gt;&gt;</a>";
            }
            $paginate .= "</font>\n";
        }

        echo $paginate;

        echo "<table bgcolor=grey cellspacing=1>\n";
        echo "  <tr class=tabheader>\n";
        if ($field_cnt > 0 or $results_to_print < 1) {
            echo "    <td colspan=17><font size=1>&nbsp;</font></td>\n";
        } else {
            echo "    <td align=left title=\"Record #\"><font color=white size=2><b>#</b></font></td>\n";

            echo "    <td align=center title=\"Lead ID\"><font class=awhite color=white size=2><b>";
            if ($sort == "lead_id" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=lead_id&direction=ASC#advsearch\">Lead#&nbsp;^";
            } elseif ($sort=="lead_id") {
                echo "<a href=\"" . $pageURL . "&sort=lead_id&direction=DESC#advsearch\">Lead#&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=lead_id&direction=DESC#advsearch\">Lead#";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"List ID\"><font class=awhite color=white size=2><b>";
            if ($sort == "list_id" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=list_id&direction=ASC#advsearch\">List#&nbsp;^";
            } elseif ($sort == "list_id") {
                echo "<a href=\"" . $pageURL . "&sort=list_id&direction=DESC#advsearch\">List#&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=list_id&direction=DESC#advsearch\">List#";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Last Status / Disposition\"><font class=awhite color=white size=2><b>";
            if ($sort == "status" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=status&direction=ASC#advsearch\">Status&nbsp;^";
            } elseif ($sort == "status") {
                echo "<a href=\"" . $pageURL . "&sort=status&direction=DESC#advsearch\">Status&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=status&direction=DESC#advsearch\">Status";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Primary Phone Number\"><font class=awhite color=white size=2><b>";
            if ($sort == "phone_number" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=phone_number&direction=ASC#advsearch\">Phone#&nbsp;^";
            } elseif ($sort=="phone_number") {
                echo "<a href=\"" . $pageURL . "&sort=phone_number&direction=DESC#advsearch\">Phone#&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=phone_number&direction=DESC#advsearch\">Phone#";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Last Name, First Name\"><font class=awhite color=white size=2><b>";
            if ($sort == "last_name" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=last_name&direction=ASC#advsearch\">Name&nbsp;^";
            } elseif ($state == "last_name") {
                echo "<a href=\"" . $pageURL . "&sort=last_name&direction=DESC#advsearch\">Name&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=last_name&direction=DESC#advsearch\">Name";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"City\"><font class=awhite color=white size=2><b>";
            if ($sort == "city" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=city&direction=ASC#advsearch\">City&nbsp;^";
            } elseif($sort == "city") {
                echo "<a href=\"" . $pageURL . "&sort=city&direction=DESC#advsearch\">City&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=city&direction=DESC#advsearch\">City";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"State\"><font class=awhite color=white size=2><b>";
            if ($sort == "state" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=state&direction=ASC#advsearch\">State&nbsp;^";
            } elseif($sort == "state") {
                echo "<a href=\"" . $pageURL . "&sort=state&direction=DESC#advsearch\">State&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=state&direction=DESC#advsearch\">State";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"ZIP / Postal Code\"><font class=awhite color=white size=2><b>";
            if ($sort == "postal_code" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=postal_code&direction=ASC#advsearch\">ZIP&nbsp;^";
            } elseif($sort == "postal_code") {
                echo "<a href=\"" . $pageURL . "&sort=postal_code&direction=DESC#advsearch\">ZIP&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=postal_code&direction=DESC#advsearch\">ZIP";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Agent/User ID\"><font class=awhite color=white size=2><b>";
            if ($sort == "user" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=user&direction=ASC#advsearch\">Agent&nbsp;^";
            } elseif ($sort == "vendor_lead_code") {
                echo "<a href=\"" . $pageURL . "&sort=user&direction=DESC#advsearch\">Agent&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=user&direction=DESC#advsearch\">Agent";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Vendor Lead Code\"><font class=awhite color=white size=2><b>";
            if ($sort == "vendor_lead_code" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=ASC#advsearch\">VendorID&nbsp;^";
            } elseif ($sort == "vendor_lead_code") {
                echo "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=DESC#advsearch\">VendorID&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=DESC#advsearch\">VendorID";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Custom Field #1\"><font class=awhite color=white size=2><b>";
            if ($sort == "custom1" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=custom1&direction=ASC#advsearch\">Custom1&nbsp;^";
            } elseif ($sort == "custom1") {
                echo "<a href=\"" . $pageURL . "&sort=custom1&direction=DESC#advsearch\">Custom1&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=custom1&direction=DESC#advsearch\">Custom1";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Timezone\"><font class=awhite color=white size=2><b>";
            if ($sort == "gmt_offset_now" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=ASC#advsearch\">TZ&nbsp;^";
            } elseif ($sort == "gmt_offset_now") {
                echo "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=DESC#advsearch\">TZ&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=DESC#advsearch\">TZ";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"# of Call Attempts\"><font class=awhite color=white size=2><b>";
            if ($sort == "called_count" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=called_count&direction=ASC#advsearch\">Calls&nbsp;^";
            } elseif ($sort == "called_count") {
                echo "<a href=\"" . $pageURL . "&sort=called_count&direction=DESC#advsearch\">Calls&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=called_count&direction=DESC#advsearch\">Calls";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Entry Date\"><font class=awhite color=white size=2><b>";
            if ($sort == "entry_date" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=entry_date&direction=ASC#advsearch\">Entry&nbsp;^";
            } elseif ($sort == "entry_date") {
                echo "<a href=\"" . $pageURL . "&sort=entry_date&direction=DESC#advsearch\">Entry&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=entry_date&direction=DESC#advsearch\">Entry";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Modified Date\"><font class=awhite color=white size=2><b>";
            if ($sort == "modify_date" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=modify_date&direction=ASC#advsearch\">Modified&nbsp;^";
            } elseif ($sort == "modify_date") {
                echo "<a href=\"" . $pageURL . "&sort=modify_date&direction=DESC#advsearch\">Modified&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=modify_date&direction=DESC#advsearch\">Modified";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Post Date\"><font class=awhite color=white size=2><b>";
            if ($sort == "post_date" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=post_date&direction=ASC#advsearch\">Post&nbsp;^";
            } elseif ($sort == "post_date") {
                echo "<a href=\"" . $pageURL . "&sort=post_date&direction=DESC#advsearch\">Post&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=post_date&direction=DESC#advsearch\">Post";
            }
            echo "</a></b></font></td>\n";
        }
        echo "  </tr>\n";

        if ($field_cnt > 0) {
            $csvfile = "advsearch_" . date("Ymd-His") . ".csv";
            $fcsv = fopen ("./" . $csvfile, "a");
            $fld_cnt = mysql_num_fields($rslt);
            $fld_names = Array();
            $o=0;
            while ($fld_cnt > $o) {
                $fld_names[] = mysql_field_table($rslt, $o) . "." . mysql_field_name($rslt, $o);
                $o++;
            }
            fputcsv($fcsv, $fld_names);
            
            $o=0;
            while ($results_to_print > $o) {
                $row=mysql_fetch_row($rslt);
                fputcsv($fcsv, $row);
                $o++;
            }
            fclose($fcsv);
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td colspan=17 align=center><font size=3 color=$default_text><a target=_new href=\"$csvfile\">Click here to transfer CSV file.</a></font></td>\n";
            echo "  </tr>\n";
        } elseif ($results_to_print < 1) {
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td colspan=17 align=center><font size=3 color=$default_text>The item(s) you searched for were not found.</font></td>\n";
            echo "  </tr>\n";
        } else {
            $o=0;
            while ($results_to_print > $o) {
                $row=mysql_fetch_row($rslt);
                $o++;
                if (eregi("1$|3$|5$|7$|9$", $o)) 
                    {$bgcolor='bgcolor='.$oddrows;} 
                else
                    {$bgcolor='bgcolor='.$evenrows;}
                if ($row[1] == '0000-00-00 00:00:00') $row[1] = "";
                if ($row[2] == '0000-00-00 00:00:00') $row[2] = "";
                if ($row[35] == '0000-00-00 00:00:00') $row[35] = "";
                if (strlen($row[11]) == 10) $row[11] = substr($row[11],0,3) . "-" . substr($row[11],3,3) . "-" . substr($row[11],6,4);
                echo "  <tr $bgcolor class=row>\n";
                echo "    <td nowrap align=left><font face=\"arial,helvetica\" size=1>" . ($o + (($page - 1) * $numresults)) . "</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[0]\"><font face=\"arial,helvetica\" size=1><a href=\"admin.php?ADD=999999&SUB=3&iframe=admin_modify_lead.php?lead_id=$row[0]\">$row[0]</a></font></td>\n";
                echo "    <td nowrap align=center title=\"$row[7]\"><font face=\"arial,helvetica\" size=1><a href=\"" . $pageURL . "&lists[]=$row[7]&sort=$sort&direction=$direction#advsearch\">$row[7]</a></font></td>\n";
                echo "    <td nowrap align=center title=\"$row[3]\"><font face=\"arial,helvetica\" size=1>$row[3]</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[11]\"><font face=\"arial,helvetica\" size=1>$row[11]</font></td>\n";
                echo "    <td nowrap align=left title=\"$row[15], $row[13]\"><font face=\"arial,helvetica\" size=1>" . ellipse($row[15] . ", " . $row[13], 10, true) . "</font></td>\n";
                echo "    <td nowrap align=left title=\"$row[19]\"><font face=\"arial,helvetica\" size=1>" . ellipse($row[19],10,true) . "</font></td>\n";
                echo "    <td nowrap align=center title=\"" . strtoupper($row[20]) ."\"><font face=\"arial,helvetica\" size=1>" . strtoupper($row[20]) . "</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[22]\"><font face=\"arial,helvetica\" size=1>$row[22]</font></td>\n";
                echo "    <td nowrap align=center title=\"" . $row[4] . " (" . $agents_label[$row[4]] . ")\"><font face=\"arial,helvetica\" size=1>$row[4]</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[4]\"><font face=\"arial,helvetica\" size=1>$row[5]</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[28]\"><font face=\"arial,helvetica\" size=1>$row[28]</font></td>\n";
                echo "    <td nowrap align=center title=\"" . $tz_revlabel[($row[8] - date("I"))] . " (" . $row[8]. ")\"><font face=\"arial,helvetica\" size=1>" . $tz_revlabel[($row[8] - date("I"))] . "</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[30]\"><font face=\"arial,helvetica\" size=1>$row[30]</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[1]\"><font face=\"arial,helvetica\" size=1>&nbsp;" . ellipse($row[1],10,false) . "&nbsp;</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[2]\"><font face=\"arial,helvetica\" size=1>&nbsp;$row[2]&nbsp;</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[35]\"><font face=\"arial,helvetica\" size=1>" . ellipse($row[35],10,false) . "</font></td>\n";
                echo "  </tr>\n";
            }
        }
        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=17></td>\n";
        echo "  </tr>\n";
        echo "</table>\n";

        echo $paginate;
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
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
	
	echo "<center><br><font color=$default_text size=+1>LOAD NEW LEADS</font><br><br>\n";
	
	
	$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
	$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
	$PHP_SELF=$_SERVER['PHP_SELF'];
	$leadfile=$_FILES["leadfile"];
	$LF_orig = $_FILES['leadfile']['name'];
	$LF_path = $_FILES['leadfile']['tmp_name'];
	if (isset($_FILES["leadfile"]))				{$leadfile_name=$_FILES["leadfile"]['name'];}

	$list_id_override = (preg_replace("/\D/","",$list_id_override));
	$phone_code_override = (preg_replace("/\D/","",$phone_code_override));
	$Imported=get_variable('Imported');
    $file_layout = get_variable('file_layout');
	
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
		Header("WWW-Authenticate: Basic realm=\"$t1-LEAD-LOADER\"");
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
		if (!$OK_to_process and ($file_layout!="custom" or $leadfile_name == "")) {
            if ($phone_code_override == "") { $phone_code_override = "1";}
			$Imported++;
            echo "	<table align=center width=\"700\" border=0 cellpadding=5 cellspacing=0 bgcolor=$oddrows>";
        
?>				
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
				<tr class=tabfooter>
					<td align=center class=tabbutton>
                      <input type=button onClick="javascript:document.location='admin.php?ADD=122'" value="START OVER" name='reload_page'>
					</td>
					<td align=center class=tabbutton>
                      <input type=submit value="SUBMIT" name='submit_file'>
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
	
				if (count($field_check)>=3) {
					flush();
					$file=fopen("$lead_file", "r");
					print "<center><font size=3 color='$default_text'><B>Processing $delim_name-delimited file...\n";
	
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
								$external_key =		    mysql_real_escape_string($row[$external_key_field]);
								$cost =		            mysql_real_escape_string($row[$cost_field]);
		
								if (strlen($list_id_override)>0) 
									{
								#	print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
									$list_id = $list_id_override;
									}
								if (strlen($phone_code_override)>0) 
									{
									$phone_code = $phone_code_override;
									}

                                # Try to grab cost if zero.
                                if ($cost == 0) {
                                    $lcost = get_first_record($link, 'osdial_lists', '*', "list_id='" . mysql_real_escape_string($list_id) . "'");
                                    $cost = $lcost['cost'];
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
										$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00');";
										$rslt=mysql_query($stmtZ, $link);
										if ($WeBRooTWritablE > 0) 
											{fwrite($stmt_file, $stmtZ."\r\n");}
										$multistmt='';
										$multi_insert_counter=0;
		
									} else {
										$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00'),";
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
							$FC="<font color='$default_text'>";
						}
						print "<BR><BR>Done</B><br><br> GOOD: <font color='$default_text'>$good</font> &nbsp; &nbsp; &nbsp; BAD: $FC $bad </font> &nbsp; &nbsp; &nbsp; TOTAL: $FC $total</font></center>";
						$Imported++;
						
					} else {
						print "<center><font face='arial, helvetica' size=3 color='#990000'><B>ERROR: The file does not have the required number of fields to process it.</B></font></center>";
					}
				} else if (!eregi(".csv", $leadfile_name)) {
					# copy($leadfile, "./osdial_temp_file.xls");
					$file=fopen("$lead_file", "r");
		
					print "<center><font size=3 color='$default_text'><B>Processing Excel file... \n";
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
					
					print "<center><font size=3 color='$default_text'><B>Processing CSV file... \n";
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
						$external_key =		    mysql_real_escape_string($row[$external_key_field]);
						$cost =		            mysql_real_escape_string($row[$cost_field]);
		
							if (strlen($list_id_override)>0) 
								{
								$list_id = $list_id_override;
								}
							if (strlen($phone_code_override)>0) 
								{
								$phone_code = $phone_code_override;
								}

                            # Try to grab cost if its zero 
                            if ($cost == 0) {
                                $lcost = get_first_record($link, 'osdial_lists', '*', "list_id='" . mysql_real_escape_string($list_id) . "'");
                                $cost = $lcost['cost'];
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
								$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00');";
								$rslt=mysql_query($stmtZ, $link);
								if ($WeBRooTWritablE > 0) 
									{fwrite($stmt_file, $stmtZ."\r\n");}
								$multistmt='';
								$multi_insert_counter=0;
		
							} else {
								$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00'),";
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
						$FC="<font color='$default_text'>";
					}
					print "<BR><BR>Done</B><br><br> GOOD: <font color='$default_text'>$good</font> &nbsp; &nbsp; &nbsp; BAD: $FC $bad</font> &nbsp; &nbsp; &nbsp; TOTAL: $FC $total</font></center>";
					$Imported++;
                    $leadfile_name = '';
				}
				print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
		} elseif ($leadfile_name) {
			# Look for list id before importing leads
			if ($list_id_override) {
				$stmt="select list_id from osdial_lists where list_id='$list_id_override';";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$ListID=$row[0];
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
		
				if (count($field_check)>=3) {
					flush();
					$file=fopen("$lead_file", "r");
					$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
					print "<center><font size=3 color='$default_text'><B>Processing $delim_name-delimited file... ($tab_count|$pipe_count)\n";
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

                            # Grab cost as its zero.
                            $lcost = get_first_record($link, 'osdial_lists', '*', "list_id='" . mysql_real_escape_string($list_id) . "'");
                            $cost = $lcost['cost'];
		
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
									$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00');";
									$rslt=mysql_query($stmtZ, $link);
									if ($WeBRooTWritablE > 0) 
										{fwrite($stmt_file, $stmtZ."\r\n");}
									$multistmt='';
									$multi_insert_counter=0;
		
								} else {
									$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00'),";
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
						$FC="<font color='$default_text'>";
					}
					print "<BR><BR>Done</B><br><br> GOOD: <font color='$default_text'>$good</font> &nbsp; &nbsp; &nbsp; BAD: $FC $bad</font> &nbsp; &nbsp; &nbsp; TOTAL: $FC $total</font></center>";
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
				
				print "<center><font size=3 color='$default_text'><B>Processing CSV file... \n";
		
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

                    # Grab cost as its zero.
                    $lcost = get_first_record($link, 'osdial_lists', '*', "list_id='" . mysql_real_escape_string($list_id) . "'");
                    $cost = $lcost['cost'];
	
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
							$stmtZ = "INSERT INTO osdial_list values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00');";
							$rslt=mysql_query($stmtZ, $link);
							if ($WeBRooTWritablE > 0) 
								{fwrite($stmt_file, $stmtZ."\r\n");}
							$multistmt='';
							$multi_insert_counter=0;
		
						} else {
							$multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$custom1','$comments',0,'$custom2','$external_key','2008-01-01 00:00:00','$cost','0000-00-00 00:00:00'),";
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
					$FC="<font color='$default_text'>";
				}
				print "<BR><BR>Done</B><br><br> GOOD: <font color='$default_text'>$good</font> &nbsp; &nbsp; &nbsp; BAD: $FC $bad</font> &nbsp; &nbsp; &nbsp; TOTAL: $FC $total</font></center>";
				
			}
			print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=false; document.forms[0].submit_file.disabled=false; document.forms[0].reload_page.disabled=false;</script>";
	
		} else {
			print "<script language='JavaScript1.2'>document.forms[0].leadfile.disabled=true; document.forms[0].submit_file.disabled=true; document.forms[0].reload_page.disabled=true;</script><HR>";
			flush();
			print "<table border=0 cellspacing=1 width=400 align=center bgcolor=grey>\n";
			print "  <tr class=tabheader>\n";
			print "    <td align=center colspan=2>FIELD MAPPINGS</td>\n";
			print "  </tr>\n";
			print "  <tr class=tabheader>\n";
			print "    <td align=center>" . strtoupper($t1) . " LEAD FIELD</td>\n";
			print "    <td align=center>FILE HEADER ROW</td>\n";
			print "  </tr>\n";
				
			$rslt=mysql_query("select vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, custom1, comments, custom2, external_key, cost from osdial_list limit 1", $link);
			
	
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
				print "<center><font size=3 color='$default_text'><B>Processing $delim_name-delimited file...\n";
	
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
				
                $o=0;
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {
	
					if ( (mysql_field_name($rslt, $i)=="list_id" and $list_id_override!="") or (mysql_field_name($rslt, $i)=="phone_code" and $phone_code_override!="") ) {
						print "<!-- skipping " . mysql_field_name($rslt, $i) . " -->\n";
					} else {
		                if (eregi("1$|3$|5$|7$|9$", $o)) {
                            $bgcolor='bgcolor='.$oddrows;
                        } else {
                            $bgcolor='bgcolor='.$evenrows;
                        }
						print "  <tr class=\"row font1\" $bgcolor>\r\n";
						print "    <td align=center>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </font></td>\r\n";
						print "    <td align=center class=tabinput><select name='".mysql_field_name($rslt, $i)."_field'>\r\n";
						print "     <option value='-1'>(none)</option>\r\n";
	
						for ($j=0; $j<count($row); $j++) {
							eregi_replace("\"", "", $row[$j]);
							print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
						}
	
						print "    </select></td>\r\n";
						print "  </tr>\r\n";
                        $o++;
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
				
				print "<center><font size=3 color='$default_text'><B>Processing CSV file... \n";
				
				if (strlen($list_id_override)>0) {
					print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
				}
				if (strlen($phone_code_override)>0) {
					print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
				}
	
				$total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';
				$row=fgetcsv($file, 1000, ",");
                $o=0;
				for ($i=0; $i<mysql_num_fields($rslt); $i++) {
					if ( (mysql_field_name($rslt, $i)=="list_id" and $list_id_override!="") or (mysql_field_name($rslt, $i)=="phone_code" and $phone_code_override!="") ) {
						print "<!-- skipping " . mysql_field_name($rslt, $i) . " -->\n";
					} else {
		                if (eregi("1$|3$|5$|7$|9$", $o)) {
                            $bgcolor='bgcolor='.$oddrows;
                        } else {
                            $bgcolor='bgcolor='.$evenrows;
                        }
						print "  <tr class=\"row font1\" $bgcolor>\r\n";
						print "    <td align=center>".strtoupper(eregi_replace("_", " ", mysql_field_name($rslt, $i))).": </td>\r\n";
						print "    <td align=center class=tabinput><select name='".mysql_field_name($rslt, $i)."_field'>\r\n";
						print "     <option value='-1'>(none)</option>\r\n";
	
						for ($j=0; $j<count($row); $j++) {
							eregi_replace("\"", "", $row[$j]);
							print "     <option value='$j'>\"$row[$j]\"</option>\r\n";
						}
	
						print "    </select></td>\r\n";
						print "  </tr>\r\n";
                        $o++;
					}
				}
			}
			print "  <input type=hidden name=dupcheck value=\"$dupcheck\">\n";
			print "  <input type=hidden name=postalgmt value=\"$postalgmt\">\n";
			print "  <input type=hidden name=lead_file value=\"$lead_file\">\n";
			print "  <input type=hidden name=list_id_override value=\"$list_id_override\">\n";
			print "  <input type=hidden name=phone_code_override value=\"$phone_code_override\">\n";
			print "  <input type=hidden name=ADD value=122>\n"; // debug -added
			print "  <tr class=tabfooter>\n";
            print "    <td align=center class=tabbutton><input type=button onClick=\"javascript:document.location='admin.php?ADD=122'\" value=\"START OVER\" name='reload_page'></td>\n";
			print "    <td align=center class=tabbutton><input type=submit name='OK_to_process' value='OK TO PROCESS'></td>\n";
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

if ($ADD==211)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
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
			echo "<br><B><font color=$default_text>LIST ADDED: $list_id</font></B>\n";

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
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($list_name) < 2) or (strlen($campaign_id) < 2) )
		{
		 echo "<br><font color=red>LIST NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>list name must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>LIST MODIFIED: $list_id</font></B>\n";

		$stmt="UPDATE osdial_lists set list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description',list_changedate='$SQLdate',scrub_dnc='$scrub_dnc',cost='$cost' where list_id='$list_id';";
		$rslt=mysql_query($stmt, $link);

		if ($reset_list == 'Y')
			{
			echo "<br><font color=$default_text>RESETTING LIST-CALLED-STATUS</font>\n";
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
			echo "<br><font color=$default_text>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($old_campaign_id)</font>\n";
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
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($list_id) < 2) or ($LOGdelete_lists < 1) )
		{
		 echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>List_id be at least 2 characters in length</font>\n";
		}
	 else
		{
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

if ($ADD==611)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( ( strlen($list_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_lists < 1) )
		{
		 echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>List_id be at least 2 characters in length</font><br>\n";
		}
	 else
		{
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
		if ($WeBRooTWritablE > 0)
			{
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

if ($ADD==311)
{
	if ($LOGmodify_lists==1)
	{
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
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}

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
		echo "    <td><a href=\"admin_modify_lead.php?lead_id=$row[1]\" target=\"_blank\">$row[1]</a></td>\n";
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
if ($ADD==100)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

$camp = get_variable('camp');
$campSQL = '';
if ($camp != '') $campSQL = "AND campaign_id='$camp'";

$dispact = get_variable('dispact');
$dispactSQL = '';
if ($dispact == 1) $dispactSQL = "AND active='Y'";

	$stmt="SELECT * from osdial_lists WHERE 1=1 $campSQL $dispactSQL order by list_id";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=$default_text size=+1>LISTS</font><br>";
if ($people_to_print > 20) {
    echo "<center><font color=$default_text size=-1>";
    if ($dispact == '1') {
        echo "<a href=\"$PHP_SELF?ADD=100&camp=$camp&dispact=\">(Show Inactive)</a>";
    } else {
        echo "<a href=\"$PHP_SELF?ADD=100&camp=$camp&dispact=1\">(Hide Inactive)</a>";
    }
    echo "</font><br>\n";
}
echo "<br>\n";
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
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}
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
