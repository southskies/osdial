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


######################
# ADD=111 display the ADD NEW LIST FORM SCREEN
######################

if ($ADD==111) {
	if ($LOG['modify_lists']==1)	{
		echo "<center><br><font color=$default_text size=4>ADD A NEW LIST</font><form action=$PHP_SELF method=POST><br></center>\n";
		echo "<input type=hidden name=ADD value=211>\n";
		echo "<table width=$section_width bgcolor=$oddrows align=center cellspacing=3>\n";
		echo "  <tr bgcolor=$oddrows><td align=right width=50%>List ID: </td><td align=left width=50%><input type=text name=list_id size=12 maxlength=12 value=\"" . date("YmdHi") . "\"> (digits only)$NWB#osdial_lists-list_id$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>List Description: </td><td align=left><input type=text name=list_description size=30 maxlength=255>$NWB#osdial_lists-list_description$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
		
			$stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE campaign_id IN %s ORDER BY campaign_id;",$LOG['allowed_campaignsSQL']);
			$rslt=mysql_query($stmt, $link);
			$campaigns_to_print = mysql_num_rows($rslt);
			$campaigns_list='';
		
			$o=0;
			while ($campaigns_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$campaigns_list .= "      <option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
				$o++;
			}
		echo "      $campaigns_list";
		echo "      <option value=\"\" SELECTED>- SELECT CAMPAIGN -</option>\n";
		echo "    </select>$NWB#osdial_lists-campaign_id$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_lists-active$NWE</td></tr>\n";
		echo "  <tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
		echo "</table>\n";
	} else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}




######################
# ADD=125 generates test leads to test campaign
######################
if ($ADD==125) {
	if ($LOG['modify_lists']==1)	{
		echo "<center><br><font color=$default_text size='2'>GENERATE TEST LEADS</font><br>(ONLY works with TEST list 998.)<form action=$PHP_SELF method=POST><br><br>\n";
		echo "<input type=hidden name=ADD value=126>\n";
		echo "<TABLE width=$section_width cellspacing=3>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Phone Number: </td><td align=left><input type=text name=testphone size=8 maxlength=8> (digits only)$NWB#$NWE</td></tr>\n";
		echo "<tr bgcolor=$oddrows><td align=right>Number of leads: </td><td align=left><input type=text name=testnbr size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
		echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
			echo "</TABLE></center>\n";
	} else {
		echo "<font color=red>You do not have permission to view this page.</font>\n";
	}
}


######################
# ADD=126 generates test leads to test campaign
######################
if ($ADD==126) {
	if ($LOG['modify_lists']==1)	{
        echo "<TABLE align=center>";
        echo "	<tr>";
        echo "		<td>";
	
        $stmt="insert into osdial_list where list_id='998'";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);

        echo "		</td>";
        echo "	</tr>";
        echo "</table>";
	} else {
		echo "<font color=red>You do not have permission to view this page.</font>\n";
	}
}




######################
# ADD=211 adds the new list to the system
######################

if ($ADD==211) {
	if ($LOG['modify_lists']==1)	{
        $stmt=sprintf("SELECT count(*) FROM osdial_lists WHERE list_id='%s';",mres($list_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>LIST NOT ADDED - there is already a list in the system with this ID</font>\n";
            $ADD=100;
        } else {
            if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($list_name) < 2)  or ($list_id < 100) or (OSDstrlen($list_id) > 12) ) {
                echo "<br><font color=red>LIST NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>List ID must be between 2 and 12 characters in length\n";
                echo "<br>List name must be at least 2 characters in length\n";
                echo "<br>List ID must be greater than 100</font><br>\n";
                $ADD=100;
            } else {
                echo "<br><B><font color=$default_text>LIST ADDED: $list_id</font></B>\n";

                $stmt=sprintf("INSERT INTO osdial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate) VALUES('%s','%s','%s','%s','%s','%s');",mres($list_id),mres($list_name),mres($campaign_id),mres($active),mres($list_description),mres($SQLdate));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW LIST      |$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
                $ADD=311;
            }
        }
	} else {
		echo "<font color=red>You do not have permission to view this page.</font>\n";
	}
}


######################
# ADD=411 submit list modifications to the system
######################

if ($ADD==411) {
    if ($LOG['modify_lists']==1) {
        if ( (OSDstrlen($list_name) < 2) or (OSDstrlen($campaign_id) < 2) ) {
            echo "<br><font color=red>LIST NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>list name must be at least 2 characters in length</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>LIST MODIFIED: $list_id</font></B>\n";

            $stmt=sprintf("UPDATE osdial_lists SET list_name='%s',campaign_id='%s',active='%s',list_description='%s',list_changedate='%s',scrub_dnc='%s',cost='%s',web_form_address='%s',web_form_address2='%s',list_script='%s' WHERE list_id='%s';",mres($list_name),mres($campaign_id),mres($active),mres($list_description),mres($SQLdate),mres($scrub_dnc),mres($cost),mres($web_form_address),mres($web_form_address2),mres($script_id),mres($list_id));
            $rslt=mysql_query($stmt, $link);

            if ($reset_list == 'Y') {
                echo "<br><font color=$default_text>RESETTING LIST-CALLED-STATUS</font>\n";
                $stmt=sprintf("UPDATE osdial_list SET called_since_last_reset='N' WHERE list_id='%s';",mres($list_id));
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
                $stmt=sprintf("DELETE FROM osdial_hopper WHERE list_id='%s' AND campaign_id='%s';",mres($list_id),mres($old_campaign_id));
                $rslt=mysql_query($stmt, $link);
            }

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY LIST INFO    |$PHP_AUTH_USER|$ip|list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description' where list_id='$list_id'|\n");
                fclose($fp);
            }
        }
        $ADD=311;	# go to list modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=511 confirmation before deletion of list
######################

if ($ADD==511) {
    if ($LOG['modify_lists']==1) {
        if ( (OSDstrlen($list_id) < 2) or ($LOG['delete_lists'] < 1) ) {
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
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=611 delete list record and all leads within it
######################

if ($ADD==611) {
    if ($LOG['modify_lists']==1) {
        if ( ( OSDstrlen($list_id) < 2) or ($CoNfIrM != 'YES') or ($LOG['delete_lists'] < 1) ) {
            echo "<br><font color=red>LIST NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>List_id be at least 2 characters in length</font><br>\n";
        } else {
            $stmt=sprintf("DELETE FROM osdial_lists WHERE list_id='%s' LIMIT 1;",mres($list_id));
            $rslt=mysql_query($stmt, $link);

            echo "<br><font color=$default_text>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($list_id)</font>\n";
            $stmt=sprintf("DELETE FROM osdial_hopper WHERE list_id='%s';",mres($list_id));
            $rslt=mysql_query($stmt, $link);

            if ($SUB==1) {
                echo "<br><font color=$default_text>REMOVING LIST LEADS FROM $t1 TABLE</font>\n";
                $stmt=sprintf("DELETE FROM osdial_list WHERE list_id='%s';",mres($list_id));
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
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=311 modify list info in the system
######################

if ($ADD==311) {
    if ($LOG['modify_lists']==1) {
        $stmt=sprintf("SELECT * FROM osdial_lists WHERE list_id='%s';",mres($list_id));
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
        $script_id = $row[13];

        # grab names of global statuses and statuses in the selected campaign
        $stmt="SELECT * FROM osdial_statuses ORDER BY status;";
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);

        $o=0;
        while ($statuses_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $statuses_list["$rowx[0]"] = "$rowx[1]";
            $o++;
        }

        $stmt=sprintf("SELECT * FROM osdial_campaign_statuses WHERE campaign_id='%s' ORDER BY status;",mres($campaign_id));
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

        $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE campaign_id IN %s ORDER BY campaign_id;", $LOG['allowed_campaignsSQL']);
        $rslt=mysql_query($stmt, $link);
        $campaigns_to_print = mysql_num_rows($rslt);
        $campaigns_list='';

        $o=0;
        while ($campaigns_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $campaigns_list .= "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
            $o++;
        }
        echo "$campaigns_list";
        echo "<option value=\"$campaign_id\" SELECTED>" . mclabel($campaign_id) . "</option>\n";
        echo "</select>$NWB#osdial_lists-campaign_id$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$active</option></select>$NWB#osdial_lists-active$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Reset Lead-Called-Status for this list: </td><td align=left><select size=1 name=reset_list><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_lists-reset_list$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>List Change Date: </td><td align=left title=\"$list_changedate\">" . dateToLocal($link,'first',$list_changedate,$webClientAdjGMT,'',$webClientDST,1) . " &nbsp; $NWB#osdial_lists-list_changedate$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>List Last Call Date: </td><td align=left title=\"$list_lastcalldate\">" . dateToLocal($link,'first',$list_lastcalldate,$webClientAdjGMT,'',$webClientDST,1) . " &nbsp; $NWB#osdial_lists-list_lastcalldate$NWE</td></tr>\n";
        if ($can_scrub_dnc == 'Y') {
            echo "<tr bgcolor=$oddrows><td align=right>External DNC Scrub Now: </td><td align=left><select size=1 name=scrub_dnc><option>Y</option><option selected>N</option></select>$NWB#osdial_lists-srub_dnc$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Last External Scrub: </td><td align=left>$list_scrub_last : $list_scrub_info</td></tr>\n";
        }
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 1 (campaign override): </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"$web_form_address\">$NWB#osdial_lists-web_form_address$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 2 (campaign override): </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"$web_form_address2\">$NWB#osdial_lists-web_form_address$NWE</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Script: </td><td align=left><select size=1 name=script_id>\n";
        echo get_scripts($link, $script_id);
        echo "</select>$NWB#osdial_lists-list_script$NWE</td></tr>\n";

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
        $stmt=sprintf("SELECT status,called_since_last_reset,count(*) FROM osdial_list WHERE list_id='%s' GROUP BY status,called_since_last_reset ORDER BY status,called_since_last_reset;",mres($list_id));
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

                if ($dispo == 'CBHOLD') {
                    $CLB="<a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">";
                    $CLE="</a>";
                } else {
                    $CLB='';
                    $CLE='';
                }

                echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
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

        $stmt=sprintf("SELECT gmt_offset_now,called_since_last_reset,count(*) FROM osdial_list WHERE list_id='%s' GROUP BY gmt_offset_now,called_since_last_reset ORDER BY gmt_offset_now,called_since_last_reset;",mres($list_id));
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
                $LOCALdate2=gmdate("Y-m-d H:i:s", time() + $LOCALzone);
                $ttzone = $tzone * 1;
                $tzlabel = $tzoffsets[$ttzone];
                if (date('I')=='1') $tzlabel = $tzoffsetsDST[$ttzone];

                if ($tzone >= 0) {
                    $DISPtzone = "$plus$tzone";
                } else {
                    $DISPtzone = "$tzone";
                }
                echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                echo "    <td>".$DISPtzone." ".$tzlabel." &nbsp; &nbsp; ($LOCALdate)</td>\n";
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
        $stmt=sprintf("SELECT status,%s,count(*) FROM osdial_list WHERE list_id='%s' GROUP BY status,%s ORDER BY status,called_count;",$max_col_grouping,mres($list_id),$max_col_grouping);
        $rslt=mysql_query($stmt, $link);
        $status_called_to_print = mysql_num_rows($rslt);

        $o=0;
        $sts=0;
        $first_row=1;
        $all_called_first=1000;
        $all_called_last=0;
        $count_statuses = array();
        $count_called = array();
        $count_count = array();
        $all_called_count = array();
        $status_called_first = array();
        $status_called_last = array();
        $leads_in_sts = array();
        $status = array();
        while ($status_called_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $leads_in_list = ($leads_in_list + $rowx[2]);
            $count_statuses[$o]			= "$rowx[0]";
            $count_called[$o]			= "$rowx[1]";
            $count_count[$o]			= "$rowx[2]";
            $all_called_count[$rowx[1]] = ($all_called_count[$rowx[1]] + $rowx[2]);

            if ( (OSDstrlen($status[$sts]) < 1) or ($status[$sts] != "$rowx[0]") ) {
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
        echo "  <tr style=\"cursor:crosshair;\" class=tabheader>\n";
        echo "    <td style=\"cursor:crosshair;\" align=left>STATUS</td>\n";
        echo "    <td style=\"cursor:crosshair;\" align=left>STATUS&nbsp;NAME</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            $flabel = $first;
            if ($first == 30) $flabel = "30+";
            if (OSDstrlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $flabel;
            if (OSDstrlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $flabel;
            if (OSDstrlen($flabel) == 3) $flabel = '&nbsp;' . $flabel;
            echo "    <td style=\"cursor:crosshair;\" align=right>$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cursor:crosshair;\" align=right>&nbsp;&nbsp;SUB</td>\n";
        echo "  </tr>\n";
        $sts=0;
        $statuses_called_to_print = count($status);
        while ($statuses_called_to_print > $sts) {
            $Pstatus = $status[$sts];
            #	echo "$status[$sts]|$status_called_first[$sts]|$status_called_last[$sts]|$leads_in_sts[$sts]|\n";
            #	echo "$status[$sts]|";
            echo "  <tr " . bgcolor($sts) . " style=\"cursor:crosshair;\" class=\"row font1\">\n";
            echo "     <td style=\"cursor:crosshair;\" nowrap>$Pstatus</td>\n";
            echo "     <td style=\"cursor:crosshair;\" nowrap>$statuses_list[$Pstatus]</td>";

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
                        if (OSDstrlen($clabel) == 1) $clabel = '&nbsp;&nbsp;&nbsp;' . $count_count[$o];
                        if (OSDstrlen($clabel) == 2) $clabel = '&nbsp;&nbsp;' . $count_count[$o];
                        if (OSDstrlen($clabel) == 3) $clabel = '&nbsp;' . $count_count[$o];
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

        echo "  <tr style=\"cursor:crosshair;\" class=tabfooter>";
        echo "    <td style=\"cursor:crosshair;\" align=left colspan=2>TOTAL</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            if ($all_called_count[$first] == 0 or $all_called_count[$first] == '') $all_called_count[$first] = '0';
            $flabel = $all_called_count[$first];
            if (OSDstrlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $all_called_count[$first];
            if (OSDstrlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $all_called_count[$first];
            if (OSDstrlen($flabel) == 3) $flabel = '&nbsp;' . $all_called_count[$first];
            echo "    <td style=\"cursor:crosshair;\" align=right class=right title=\"$first Lead Count Total: $all_called_count[$first] Leads\">$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cursor:crosshair;\" align=right title=\"Total: $leads_in_list Leads\">$leads_in_list</td>\n";
        echo "  </tr>\n";
        echo "</table></center><br>\n";


        $count_cols=30;
        $leads_in_list = 0;
        $leads_in_list_N = 0;
        $leads_in_list_Y = 0;
        $max_col_grouping = "if(cnt>" . ($count_cols - 1) . "," . $count_cols . ",cnt)";
        $stmt=sprintf("SELECT stat,%s,count(*) FROM (select osdial_log.status AS stat,count(*) AS cnt FROM osdial_list JOIN osdial_log ON (osdial_list.lead_id=osdial_log.lead_id) WHERE osdial_list.list_id='%s' group by osdial_log.lead_id,osdial_log.status,osdial_log.lead_id ORDER BY osdial_log.status,count(*)) AS t1 GROUP BY %s,stat ORDER BY stat,%s;",$max_col_grouping,mres($list_id),$max_col_grouping,$max_col_grouping);
        $rslt=mysql_query($stmt, $link);
        $status_called_to_print = mysql_num_rows($rslt);

        $o=0;
        $sts=0;
        $first_row=1;
        $all_called_first=1000;
        $all_called_last=0;
        $count_statuses = array();
        $count_called = array();
        $count_count = array();
        $all_called_count = array();
        $status_called_first = array();
        $status_called_last = array();
        $leads_in_sts = array();
        $status = array();
        while ($status_called_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $leads_in_list = ($leads_in_list + $rowx[2]);
            $count_statuses[$o]			= "$rowx[0]";
            $count_called[$o]			= "$rowx[1]";
            $count_count[$o]			= "$rowx[2]";
            $all_called_count[$rowx[1]] = ($all_called_count[$rowx[1]] + $rowx[2]);

            if ( (OSDstrlen($status[$sts]) < 1) or ($status[$sts] != "$rowx[0]") ) {
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
        echo "  <tr style=\"cursor:crosshair;\" class=tabheader>\n";
        echo "    <td style=\"cursor:crosshair;\" align=left>STATUS</td>\n";
        echo "    <td style=\"cursor:crosshair;\" align=left>STATUS&nbsp;NAME</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            $flabel = $first;
            if ($first == 30) $flabel = "30+";
            if (OSDstrlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $flabel;
            if (OSDstrlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $flabel;
            if (OSDstrlen($flabel) == 3) $flabel = '&nbsp;' . $flabel;
            echo "    <td style=\"cursor:crosshair;\" align=right>$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cursor:crosshair;\" align=right>&nbsp;&nbsp;SUB</td>\n";
        echo "  </tr>\n";

        $sts=0;
        $statuses_called_to_print = count($status);
        while ($statuses_called_to_print > $sts) {
            $Pstatus = $status[$sts];
            #	echo "$status[$sts]|$status_called_first[$sts]|$status_called_last[$sts]|$leads_in_sts[$sts]|\n";
            #	echo "$status[$sts]|";
            echo "  <tr " . bgcolor($sts) . " style=\"cursor:crosshair;\" class=\"row font1\">\n";
            echo "     <td style=\"cursor:crosshair;\" nowrap>$Pstatus</td>\n";
            echo "     <td style=\"cursor:crosshair;\" nowrap>$statuses_list[$Pstatus]</td>";

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
                        if (OSDstrlen($clabel) == 1) $clabel = '&nbsp;&nbsp;&nbsp;' . $count_count[$o];
                        if (OSDstrlen($clabel) == 2) $clabel = '&nbsp;&nbsp;' . $count_count[$o];
                        if (OSDstrlen($clabel) == 3) $clabel = '&nbsp;' . $count_count[$o];
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

        echo "  <tr style=\"cursor:crosshair;\" class=tabfooter>";
        echo "    <td style=\"cursor:crosshair;\" align=left colspan=2>TOTAL</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            if ($all_called_count[$first] == 0 or $all_called_count[$first] == '') $all_called_count[$first] = '0';
            $flabel = $all_called_count[$first];
            if (OSDstrlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $all_called_count[$first];
            if (OSDstrlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $all_called_count[$first];
            if (OSDstrlen($flabel) == 3) $flabel = '&nbsp;' . $all_called_count[$first];
            echo "    <td style=\"cursor:crosshair;\" align=right class=right title=\"$first Attempt Total: $all_called_count[$first] Calls\">$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cursor:crosshair;\" align=right title=\"Total: $leads_in_list Calls\">$leads_in_list</td>\n";
        echo "  </tr>\n";

        echo "</table></center><br>\n";





        echo "<center>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=811&list_id=$list_id\">Click here to see all CallBack Holds in this list</a><BR><BR>\n";
        echo "</center>\n";
	
        if ($LOG['delete_lists'] > 0) {
            echo "<br><br><a href=\"$PHP_SELF?ADD=511&list_id=$list_id\">DELETE THIS LIST</a>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=511&SUB=1&list_id=$list_id\">DELETE THIS LIST AND ITS LEADS</a> (WARNING: Will damage call-backs made in this list!)\n";
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=811 find all callbacks on hold within a List
######################
if ($ADD==811) {
	if ($LOG['modify_lists']==1) {
		if ($SUB==89) {
		    $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE list_id='%s' AND status='LIVE' AND callback_time<'%s';",mres($list_id),mres($past_month_date));
		    $rslt=mysql_query($stmt, $link);
		    echo "<br>list($list_id) callback listings LIVE for more than one month have been made INACTIVE\n";
		}
		if ($SUB==899) {
		    $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE list_id='%s' AND status='LIVE' AND callback_time<'%s';",mres($list_id),mres($past_week_date));
		    $rslt=mysql_query($stmt, $link);
		    echo "<br>list($list_id) callback listings LIVE for more than one week have been made INACTIVE\n";
		}
	}
    $CBinactiveLINK = "<BR><a href=\"$PHP_SELF?ADD=811&SUB=89&list_id=$list_id\"><font color=$default_text>Remove LIVE Callbacks older than one month for this list</font></a><BR><a href=\"$PHP_SELF?ADD=811&SUB=899&list_id=$list_id\"><font color=$default_text>Remove LIVE Callbacks older than one week for this list</font></a><BR>";

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
    if (OSDpreg_match("/USERIDDOWN/",$stage)) {$SQLorder='order by user desc,';   $USERlink='stage=USERIDUP';}
    if (OSDpreg_match("/GROUPDOWN/",$stage)) {$SQLorder='order by user_group desc,';   $NAMElink='stage=NAMEUP';}
    if (OSDpreg_match("/ENDATEDOWN/",$stage)) {$SQLorder='order by entry_time desc,';   $LEVELlink='stage=LEVELUP';}

    $stmt="SELECT * FROM osdial_callbacks WHERE status IN('ACTIVE','LIVE') $CBquerySQLwhere $SQLorder recipient,status DESC,callback_time;";
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
		echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[1]\" target=\"_blank\">$row[1]</a></td>\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=311&list_id=$row[2]\">$row[2]</a></td>\n";
		echo "    <td><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[3]\">$row[3]</a></td>\n";
		echo "    <td title=\"ENTRY DATE: $row[5]\">" . dateToLocal($link,'first',$row[5],$webClientAdjGMT,'',$webClientDST,1) . "</td>\n";
		echo "    <td title=\"CALLBACK DATE: $row[6]\">" . dateToLocal($link,'first',$row[6],$webClientAdjGMT,'',$webClientDST,1) . "</td>\n";
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
    $camp = get_variable('camp');
    $campSQL = '';
    if ($camp != '') $campSQL = "AND campaign_id='$camp'";

    $let = get_variable('let');
    $letSQL = '';
    if ($let != '') $letSQL = "AND (campaign_id LIKE '$let%' OR list_id LIKE '$let%')";

    $dispact = get_variable('dispact');
    $dispactSQL = '';
    if ($dispact == 1) $dispactSQL = "AND active='Y'";

    $stmt = sprintf("SELECT * from osdial_lists WHERE campaign_id IN %s %s %s %s order by list_id",$LOG['allowed_campaignsSQL'],$campSQL, $letSQL, $dispactSQL);
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
    echo "<TABLE width=$section_width cellspacing=0 cellpadding=1 style=\"white-space:nowrap;\">\n";
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
        if ($row[0] > 19 or $let=='1') {
            echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=311&list_id=$row[0]';\" title=\"MODIFIED: $row[5]\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">$row[0]</a></td>\n";
            echo "    <td>$row[1]</td>\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=100&camp=$row[2]&dispact=$dispact\">" . mclabel($row[2]) . "</a></td>\n";
            echo "    <td>$row[4]</td>\n";
            echo "    <td align=center>" . dateToLocal($link,'first',$row[5],$webClientAdjGMT,'',$webClientDST,1) . "</td>\n";
            echo "    <td align=center>$row[3]</td>\n";
            #echo "    <td>$row[7]</td>\n";
            echo "    <td colspan=3 align=center><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">MODIFY</a>";
            if ($LOG['user_level'] > 8) {
                echo " | <a href=\"$PHP_SELF?ADD=131&list_id=$row[0]\">EXPORT</a>";
            }
            echo " | <a href=\"$PHP_SELF?ADD=122&list_id_override=$row[0]\">ADD LEADS</a></td>\n";
            echo "  </tr>\n";
        }
        $o++;
    }

    echo "  <tr class=tabfooter>\n";
    echo "    <td colspan=9></td>\n";
    echo "  </tr>\n";
    echo "</TABLE></center>\n";
}






?>
