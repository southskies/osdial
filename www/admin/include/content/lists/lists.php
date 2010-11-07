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
		echo "  <tr bgcolor=$oddrows><td align=right width=50%>List ID: </td><td align=left width=50%><input type=text name=list_id size=12 maxlength=12 value=\"" . date("YmdHi") . "\"> (digits only)$NWB#osdial_lists-list_id$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>List Name: </td><td align=left><input type=text name=list_name size=20 maxlength=20>$NWB#osdial_lists-list_name$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>List Description: </td><td align=left><input type=text name=list_description size=30 maxlength=255>$NWB#osdial_lists-list_description$NWE</td></tr>\n";
		echo "  <tr bgcolor=$oddrows><td align=right>Campaign: </td><td align=left><select size=1 name=campaign_id>\n";
		
			$stmt=sprintf("SELECT campaign_id,campaign_name from osdial_campaigns where campaign_id IN %s order by campaign_id",$LOG['allowed_campaignsSQL']);
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
# ADD=121 display the ADD NUMBER TO DNC FORM SCREEN and add a new number
######################

if ($ADD==121) {
	if ($LOGmodify_lists==1)	{
        $dncs=0;
        $dncc=0;
        $searchres='';
        if ($SUB==1) {
            if (strlen($dnc_search_phone) < 3) {
                echo "<br><font color=red>DNC SEARCH FAILED - The phone number should be at least 3 digits.</font>\n";
            } else {
                $mcsearch='';
                if ($LOG['multicomp'] > 0) {
                    if (preg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                        $stmt = sprintf("SELECT company_id,phone_number,creation_date FROM osdial_dnc_company WHERE company_id='%s' AND phone_number LIKE '%%%s%%';", mres($LOG['company_id']),mres($dnc_search_phone));
                        if ($LOG['multicomp_admin'] > 0) {
                            $stmt = sprintf("SELECT company_id,phone_number,creation_date FROM osdial_dnc_company WHERE phone_number LIKE '%%%s%%';", mres($dnc_search_phone));
                        }
                        $rslt=mysql_query($stmt, $link);
                        $dncc = mysql_num_rows($rslt);
                        if ($dncc>0) {
                            $mcsearch.="<table bgcolor=$oddrows align=center cellspacing=1 width=400>\n";
                            $mcsearch.="  <tr class=tabheader>\n";
                            $mcsearch.="    <td>Company</td><td>Phone</td><td>Created</td>\n";
                            $mcsearch.="  </tr>\n";
                            $o=0;
                            while ($dncc > $o) {
                                $rowx=mysql_fetch_row($rslt);
                                $mcsearch.="  <tr class=font2><td align=center>$rowx[0]</td><td align=center>$rowx[1]</td><td align=center>$rowx[2]</td></tr>\n";
                                $o++;
                            }
                        }
                    }
                }

                $syssearch='';
                $stmt = "SELECT phone_number,creation_date FROM osdial_dnc WHERE 1=2;";
                if ($LOG['multicomp_user'] > 0) {
                    if (preg_match('/SYSTEM|BOTH/',$LOG['company']['dnc_method'])) {
                        $stmt = sprintf("SELECT phone_number,creation_date FROM osdial_dnc WHERE phone_number LIKE '%%%s%%';", mres($dnc_search_phone));
                    }
                } else {
                    $stmt = sprintf("SELECT phone_number,creation_date FROM osdial_dnc WHERE phone_number LIKE '%%%s%%';", mres($dnc_search_phone));
                }
                $rslt=mysql_query($stmt, $link);
                $dncs = mysql_num_rows($rslt);
                if ($mcsearch=="") {
                    if ($dncs>0) {
                        $syssearch.="<table bgcolor=$oddrows align=center cellspacing=1 width=400>\n";
                        $syssearch.="  <tr class=tabheader>\n";
                        $syssearch.="    <td>Phone</td><td>Created</td>\n";
                        $syssearch.="  </tr>\n";
                        $o=0;
                        while ($dncs > $o) {
                            $rowx=mysql_fetch_row($rslt);
                            $syssearch.="  <tr class=font2><td align=center>$rowx[0]</td><td align=center>$rowx[1]</td></tr>\n";
                            $o++;
                        }
                        $syssearch.="  <tr class=tabfooter><td colspan=2></td></tr>\n";
                        $syssearch.="</table>\n";
                        $searchres=$syssearch;
                    }
                } else {
                    if ($dncs>0) {
                        $o=0;
                        while ($dncs > $o) {
                            $rowx=mysql_fetch_row($rslt);
                            $mcsearch.="  <tr class=font2><td>&nbsp;</td><td align=center>$rowx[0]</td><td align=center>$rowx[1]</td></tr>\n";
                            $o++;
                        }
                    }
                    $mcsearch.="  <tr class=tabfooter><td colspan=3></td></tr>\n";
                    $mcsearch.="</table>\n";
                    $searchres=$mcsearch;
                }
            }
        } elseif ($SUB==2) {
            if (strlen($dnc_add_phone) < 3) {
                echo "<br><font color=red>DNC RECORD NOT ADDED - The phone number should be at least 3 digits.</font>\n";
            } else {
                $dncsskip=0;
                if (strlen($dnc_add_phone)==3) $dnc_add_phone .= 'XXXXXXX';
                $dnc_add_phone = preg_replace('/x/','X',$dnc_add_phone);

                if ($LOG['multicomp_user'] > 0) {
                    if (preg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                        $stmt = sprintf("SELECT count(*) FROM osdial_dnc_company WHERE company_id='%s' AND phone_number='%s';", mres($LOG['company_id']),mres($dnc_add_phone));
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $dncc=$row[0];
                    }
                    if (preg_match('/COMPANY/',$LOG['company']['dnc_method'])) $dncsskip++;
                }

                if ($dncsskip==0) {
                    $stmt = sprintf("SELECT count(*) from osdial_dnc where phone_number='%s';", mres($dnc_add_phone));
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $dncs=$row[0];
                }

                if ($dncs > 0 or $dncc > 0) {
                    echo "<br>DNC NOT ADDED - This phone number is already in the ";
                    if ($dncs > 0 and $dncc > 0) {
                        echo "System and Company";
                    } elseif ($dncs > 0) {
                        echo "System";
                    } elseif ($dncc > 0) {
                        echo "Company";
                    }
                    echo " Do Not Call List: $dnc_add_phone<BR><BR>\n";
                } else {
                    if ($LOG['multicomp_user'] > 0 and preg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                        $stmt = sprintf("INSERT INTO osdial_dnc_company (company_id,phone_number) values('%s','%s');", mres($LOG['company_id']),mres($dnc_add_phone));
                    } else {
                        $stmt = sprintf("INSERT INTO osdial_dnc (phone_number) values('%s');", mres($dnc_add_phone));
                    }
                    $rslt=mysql_query($stmt, $link);

                    echo "<br><B>DNC ADDED SUCCESSFULLY: $dnc_add_phone</B><BR><BR>\n";

                    ### LOG INSERTION TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|ADD A NEW DNC NUMBER|$PHP_AUTH_USER|$ip|'$dnc_add_phone'|\n");
                        fclose($fp);
                    }
                }
            }
        }

        echo "<center><br><font color=$default_text size=4>DO-NOT-CALL MAINTENANCE</font></center><br><br>\n";

	    if ($LOG['user_level']==9 and $LOG['delete_dnc']==1 and $SUB>=3 and $SUB<=5) {
            if (strlen($dnc_delete_phone) < 6) {
                echo "<br><font color=red>DNC DELETION FAILED - The phone number should be at least 6 digits.</font>\n";
                $SUB='';
            }
        }

        $dnc_prepare_export=get_variable('dnc_prepare_export');

	    if ($LOG['user_level']==9 and $LOG['delete_dnc']==1 and $SUB>=3 and $SUB<=5) {
            if ($SUB==3) {
                echo "<hr width=80%><br>";
                echo "<form action=$PHP_SELF method=POST>\n";
                echo "<input type=hidden name=ADD value=121>\n";
                echo "<input type=hidden name=SUB id=SUB value=4>\n";
                echo "<input type=hidden name=dnc_delete_phone id=DDP value=\"$dnc_delete_phone\">\n";
		        echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
                echo "  <tr bgcolor=$oddrows>\n";
                echo "    <td align=center colspan=2 class=font2>Do you want to delete \"$dnc_delete_phone\"?</td>\n";
                echo "  </tr>\n";
                echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton bgcolor=red width=50%>\n";
                echo "      <input type=button name=cancel1 value=\"NO\" onclick=\"document.getElementById('SUB').value='';document.getElementById('DDP').value='';submit();\">\n";
                echo "    </td>\n";
                echo "    <td align=center class=tabbutton bgcolor=red width=50%>\n";
                echo "      <input type=submit name=SUBMIT value=\"YES\">\n";
                echo "    </td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";
            } elseif ($SUB==4) {
                echo "<hr width=80%><br>";
                echo "<form action=$PHP_SELF method=POST>\n";
                echo "<input type=hidden name=ADD value=121>\n";
                echo "<input type=hidden name=SUB id=SUB value=5>\n";
                echo "<input type=hidden name=dnc_delete_phone id=DDP value=\"$dnc_delete_phone\">\n";
		        echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
                echo "  <tr bgcolor=$oddrows>\n";
                echo "    <td align=center colspan=2 class=font2>Do you want to cancel and keep \"$dnc_delete_phone\" on your Do-Not-Call list?</td>\n";
                echo "  </tr>\n";
                echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton bgcolor=red width=50%>\n";
                echo "      <input type=button name=cancel1 value=\"YES\" onclick=\"document.getElementById('SUB').value='';document.getElementById('DDP').value='';submit();\">\n";
                echo "    </td>\n";
                echo "    <td align=center class=tabbutton bgcolor=red width=50%>\n";
                echo "      <input type=submit name=SUBMIT value=\"NO\">\n";
                echo "    </td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";
            } elseif ($SUB==5) {
                echo "<hr width=80%><br>";
                echo "<form action=$PHP_SELF method=POST>\n";
                echo "<input type=hidden name=ADD value=121>\n";
                echo "<input type=hidden name=SUB id=SUB value=6>\n";
                echo "<input type=hidden name=dnc_delete_phone id=DDP value=\"$dnc_delete_phone\">\n";
		        echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
                echo "  <tr bgcolor=$oddrows>\n";
                echo "    <td align=center colspan=2 class=font2>If you click DELETE, \"$dnc_delete_phone\" will be removed from your Do-Not-Call list.  There is no recovery process for DNC deletion.  You may be subject to fines from both State and Federal governments if you make a mistake.  If you are absolutely certain, click DELETE and you will be able to once again call this number.</td>\n";
                echo "  </tr>\n";
                echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton bgcolor=red width=50%>\n";
                echo "      <input type=submit name=SUBMIT value=\"DELETE\">\n";
                echo "    </td>\n";
                echo "    <td align=center class=tabbutton bgcolor=red width=50%>\n";
                echo "      <input type=button name=cancel1 value=\"CANCEL\" onclick=\"document.getElementById('SUB').value='';document.getElementById('DDP').value='';submit();\">\n";
                echo "    </td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";
            }

	    } elseif ($LOG['user_level']==9 and $LOG['export_dnc']==1 and $dnc_prepare_export=='1') {
            echo "<hr width=80%><br>";
            echo "<form action=\"tocsv.php\" method=POST id=\"tocsvform\" enctype=\"multipart/form-data\">\n";
            echo "<center><font color=$default_text size=3>Preparing DNC Download</font></center>\n";
            echo "<input type=hidden name=ADD value=121>\n";
		    echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=center class=font2>Please be patient, your download will begin shortly.</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=center class=font2>\n";
            echo "      <input type=hidden name=name value=\"OSDial_DNC_Export-".$LOG['user']."\">\n";
            echo "      <textarea name=\"glob\" id=\"glob\" style=\"visibility:hidden;white-space:nowrap;font-size:5pt;\" wrap=off cols=100 rows=2></textarea>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabbutton>\n";
            echo "      <input type=submit name=SUBMIT id=\"dlbutton\" value=\"DOWNLOAD\">\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "</table>\n";
            echo "</form>\n";
            echo "<center><iframe name=\"downloadframe\" id=\"downloadframe\" height=\"100px\" width=\"500px\" scrolling=\"no\" frameborder=\"0\"></iframe></center>\n";
            echo "<script type=\"text/javascript\">\n";
            echo "var iframe = document.getElementById('downloadframe');\n";
            echo "var form = document.getElementById('tocsvform');\n";
            echo "var g = document.getElementById('glob');\n";
            echo "form.target = iframe.id;\n";
            echo "document.getElementById('dlbutton').disabled=true;\n";
            echo "document.getElementById('dlbutton').value='GATHERING DNC DATA';\n";
            echo "</script>\n";
            flush();
            ob_flush();
            echo "<script type=\"text/javascript\">\n";

            $rows=0;
            $odcrows=0;
            $odrows=0;

            if ($LOG['multicomp_user'] > 0) {
                echo "g.value+=\"Phone|Company|Created\\n\";\n";
                $rows++;
                if (preg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                    $stmt = sprintf("SELECT phone_number,(company_id+100) AS cid,creation_date FROM osdial_dnc_company WHERE company_id='%s';",mres($LOG['company_id']));
                    $rslt=mysql_query($stmt, $link);
                    $dncc = mysql_num_rows($rslt);
                    $o=0;
                    while ($dncc > $o) {
                        $rowx=mysql_fetch_row($rslt);
                        echo "g.value+=\"".$rowx['0'].'|'.$rowx['1'].'|'.$rowx['2']."\\n\";\n";
                        $rows++;
                        $odcrows++;
                        if ($o % 500 == 0) {
                            echo "document.getElementById('dlbutton').value+='.';\n";
                            flush();
                            ob_flush();
                        }
                        $o++;
                    }
                }
                if (preg_match('/SYSTEM|BOTH/',$LOG['company']['dnc_method'])) {
                    $stmt = "SELECT phone_number,'',creation_date FROM osdial_dnc;";
                    $rslt=mysql_query($stmt, $link);
                    $dncs = mysql_num_rows($rslt);
                    $o=0;
                    while ($dncs > $o) {
                        $rowx=mysql_fetch_row($rslt);
                        echo "g.value+=\"".$rowx['0'].'|'.$rowx['1'].'|'.$rowx['2']."\\n\";\n";
                        $rows++;
                        $odrows++;
                        if ($o % 500 == 0) {
                            echo "document.getElementById('dlbutton').value+='.';\n";
                            flush();
                            ob_flush();
                        }
                        $o++;
                    }
                }
            } elseif ($LOG['multicomp_admin'] > 0) {
                echo "g.value+=\"Phone|Company|Created\\n\";\n";
                $rows++;
                $stmt = "SELECT phone_number,(company_id+100) AS cid,creation_date FROM osdial_dnc_company ORDER BY cid;";
                $rslt=mysql_query($stmt, $link);
                $dncc = mysql_num_rows($rslt);
                $o=0;
                while ($dncc > $o) {
                    $rowx=mysql_fetch_row($rslt);
                    echo "g.value+=\"".$rowx['0'].'|'.$rowx['1'].'|'.$rowx['2']."\\n\";\n";
                    $rows++;
                    $odcrows++;
                    if ($o % 500 == 0) {
                        echo "document.getElementById('dlbutton').value+='.';\n";
                        flush();
                        ob_flush();
                    }
                    $o++;
                }

                $stmt = "SELECT phone_number,'',creation_date FROM osdial_dnc;";
                $rslt=mysql_query($stmt, $link);
                $dncs = mysql_num_rows($rslt);
                $o=0;
                while ($dncs > $o) {
                    $rowx=mysql_fetch_row($rslt);
                    echo "g.value+=\"".$rowx['0'].'|'.$rowx['1'].'|'.$rowx['2']."\\n\";\n";
                    $rows++;
                    $odrows++;
                    if ($o % 500 == 0) {
                        echo "document.getElementById('dlbutton').value+='.';\n";
                        flush();
                        ob_flush();
                    }
                    $o++;
                }
            } else {
                echo "g.value+=\"Phone|Created\\n\";\n";
                $rows++;
                $stmt = "SELECT phone_number,creation_date FROM osdial_dnc;";
                $rslt=mysql_query($stmt, $link);
                $dncs = mysql_num_rows($rslt);
                $o=0;
                while ($dncs > $o) {
                    $rowx=mysql_fetch_row($rslt);
                    echo "g.value+=\"".$rowx['0'].'|'.$rowx['1']."\\n\";\n";
                    $rows++;
                    $odrows++;
                    if ($o % 500 == 0) {
                        echo "document.getElementById('dlbutton').value+='.';\n";
                        flush();
                        ob_flush();
                    }
                    $o++;
                }
            }
            echo "</script>\n";
            flush();
            ob_flush();
            echo "<script type=\"text/javascript\">\n";
            echo "document.getElementById('dlbutton').value='FORMATTING DNC FILE';\n";
            echo "form.submit();\n";
            echo "</script>\n";
            flush();
            ob_flush();
            echo "<script type=\"text/javascript\">\n";
            echo "document.getElementById('dlbutton').value='DOWNLOAD WILL START SOON';\n";
            echo "</script>\n";
            flush();
            ob_flush();
        } else {

            echo "<hr width=80%><br>";
            echo "<form action=$PHP_SELF method=POST>\n";
            echo "<center><font color=$default_text size=3>Search DNC List</font></center>\n";
            echo "<input type=hidden name=ADD value=121>\n";
            echo "<input type=hidden name=SUB value=1>\n";
		    echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
            if ($dncc>0 or $dncs>0) {
                echo "  <tr bgcolor=$oddrows>\n";
                echo "    <td>" . $searchres . "</td>\n";
                echo "  </tr>\n";
            }
            echo "  <tr bgcolor=$oddrows class=font2>\n";
            echo "    <td align=center>Phone Number:&nbsp;<input type=text name=dnc_search_phone size=16 maxlength=15 value=\"$dnc_search_phone\">$NWB#osdial_list-dnc$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabbutton><input type=submit name=SUBMIT value=\"SEARCH\"></td>\n";
            echo "  </tr>\n";
            echo "</table>\n";
            echo "</form>\n";

            echo "<br><hr width=80%><br>";
            echo "<center><font color=$default_text size=3>Add Number to DNC List</font></center>\n";
            echo "<form action=$PHP_SELF method=POST>\n";
            echo "<input type=hidden name=ADD value=121>\n";
            echo "<input type=hidden name=SUB value=2>\n";
		    echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
            echo "  <tr bgcolor=$oddrows class=font2>\n";
            echo "    <td align=center>Phone Number:&nbsp;<input type=text name=dnc_add_phone size=16 maxlength=15 value=\"$dnc_add_phone\">$NWB#osdial_list-dnc$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabbutton bgcolor=purple><input type=submit name=SUBMIT value=\"ADD\"></td>\n";
            echo "  </tr>\n";
            echo "</table>\n";
            echo "</form>\n";

	        if ($LOG['user_level']==9 and $LOG['delete_dnc']==1){
                $delstatus='';
                $dncs=0;
                $dncc=0;
	            if ($SUB==6) {
                    if (strlen($dnc_delete_phone) < 6) {
                        echo "<br><font color=red>DNC DELETION FAILED - The phone number should be at least 6 digits.</font>\n";
                    } else {
                        $dnc_delete_phone = preg_replace('/x/','X',$dnc_delete_phone);
                        if ($LOG['multicomp_user'] > 0) {
                            if (preg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                                $stmt = sprintf("SELECT * FROM osdial_dnc_company WHERE company_id='%s' AND phone_number='%s';", mres($LOG['company_id']),mres($dnc_delete_phone));
                                $rslt=mysql_query($stmt, $link);
                                $dncc = mysql_num_rows($rslt);
                            }
                            if ($dncc==0) {
                                $stmt = sprintf("SELECT count(*) FROM osdial_dnc WHERE phone_number='%s';", mres($dnc_delete_phone));
                                $rslt=mysql_query($stmt, $link);
                                $dncs=mysql_fetch_row($rslt);
                                if ($dncs>0 and preg_match('/SYSTEM|BOTH/',$LOG['company']['dnc_method'])) {
                                    $delstatus="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was NOT FOUND in the Do-Not-Call list for company ".$LOG['company_id'].", however, it is in the System-wide Do-Not-Call list.  If you need this number removed, you should contact the system administrator.</td></tr>\n";
                                } else {
                                    $delstatus="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was NOT FOUND in the Do-Not-Call list for company ".$LOG['company_id'].".</td></tr>\n";
                                }
                            } else {
                                $stmt = sprintf("DELETE FROM osdial_dnc_company WHERE company_id='%s' AND phone_number='%s';", mres($LOG['company_id']),mres($dnc_delete_phone));
                                $rslt=mysql_query($stmt, $link);

                                $delstatus="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was SUCCESSFULLY deleted from the Do-Not-Call list for company ".$LOG['company_id'].".</td></tr>\n";

                                ### LOG INSERTION TO LOG FILE ###
                                if ($WeBRooTWritablE > 0) {
                                    $fp = fopen ("./admin_changes_log.txt", "a");
                                    fwrite ($fp, "$date|DELETE A DNC NUMBER|$PHP_AUTH_USER|$ip|'$dnc_delete_phone'|\n");
                                    fclose($fp);
                                }
                            }
                        } else {
                            $stmt = sprintf("SELECT * FROM osdial_dnc WHERE phone_number='%s';",mres($dnc_delete_phone));
                            $rslt=mysql_query($stmt, $link);
                            $dncs = mysql_num_rows($rslt);
                            if ($dncs==0) {
                                $delstatus="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was NOT FOUND in the Do-Not-Call list.</td></tr>\n";
                            } else {
                                $stmt = sprintf("DELETE FROM osdial_dnc WHERE phone_number='%s';",mres($dnc_delete_phone));
                                $rslt=mysql_query($stmt, $link);

                                $delstatus="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was SUCCESSFULLY deleted from the Do-Not-Call list.</td></tr>\n";

                                ### LOG INSERTION TO LOG FILE ###
                                if ($WeBRooTWritablE > 0) {
                                    $fp = fopen ("./admin_changes_log.txt", "a");
                                    fwrite ($fp, "$date|DELETE A DNC NUMBER|$PHP_AUTH_USER|$ip|'$dnc_delete_phone'|\n");
                                    fclose($fp);
                                }
                            }
                        }
                        $dnc_delete_phone='';
                    }
                }

                echo "<br><hr width=80%><br>";
                echo "<center><font color=$default_text size=3>Delete Number from DNC List</font></center>\n";
                echo "<form action=$PHP_SELF method=POST>\n";
                echo "<input type=hidden name=ADD value=121>\n";
                echo "<input type=hidden name=SUB value=3>\n";
		        echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
                echo $delstatus;
                echo "  <tr bgcolor=$oddrows class=font2>\n";
                echo "    <td align=center>Phone Number:&nbsp;<input type=text name=dnc_delete_phone size=16 maxlength=15 value=\"$dnc_delete_phone\">$NWB#osdial_list-dnc$NWE</td>\n";
                echo "  </tr>\n";
                echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton bgcolor=red><input type=submit name=SUBMIT value=\"DELETE\"></td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";
            }

	        if ($LOG['user_level']==9 and $LOG['load_dnc']==1) {
                $loaderr='';
                $dncfile_name='';
                if (isset($_FILES["dncfile"])) {
                    $dncfile_name=$_FILES["dncfile"]['name'];
                    $dncfile_path=$_FILES['dncfile']['tmp_name'];
                }

                # Process file if sent.
                if ($dncfile_name!='') {
                    if (preg_match('/\.txt$|\.csv$|\.psv$|\.tsv$|\.tab$/i', $dncfile_name)) {
                        $dncfile='';
                        if ($WeBRooTWritablE > 0) {
                            copy($dncfile_path, "$WeBServeRRooT/admin/osdial_temp_file.csv");
                            $dncfile = "$WeBServeRRooT/admin/osdial_temp_file.csv";
                        } else {
                            copy($dncfile_path, "/tmp/osdial_temp_file.csv");
                            $dncfile = "/tmp/osdial_temp_file.csv";
                        }

                        $file=fopen($dncfile, "r");

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
                        $file=fopen($dncfile, "r");

                        echo "<hr width=80%><br><br><center><font size=3 color='$default_text'><b>Processing $delim_name file...</b></font></center><br>\n";
                        ob_flush();
                        flush();

                        $restab =  "<center><font color=$default_text size=3>FILE UPLOAD RESULTS</font></center>\n";
                        $restab .= "<table border=0 cellspacing=1 width=400 align=center bgcolor=grey>\n";
                        $restab .= "  <tr class=tabheader>\n";
                        $restab .= "    <td>#</td>\n";
                        $restab .= "    <td>NUMBER</td>\n";
                        $restab .= "    <td>";
                        if ($LOG['multicomp']) $restab .= "COMPANY";
                        $restab .= "</td>\n";
                        $restab .= "    <td>STATUS</td>\n";
                        $restab .= "  </tr>\n";

                        echo $restab; $restab='';

                        $dnc_fail=0;
                        $dnc_skip=0;
                        $dnc_add=0;
                        $o=0;
                        while($csvrow=fgetcsv($file, 1000, $delimiter)) {
                            $o++;
                            $dnc_number = preg_replace('/x/','X',preg_replace('/[^0-9Xx\*]/','',$csvrow[0]));
                            $dnc_compid='';
                            if ($LOG['multicomp_user']>0 and preg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) $dnc_compid = $LOG['company_id'];
                            if ($LOG['multicomp_admin']>0) $dnc_compid = (preg_replace('/[^0-9]/','',$csvrow[1]) - 100);
                            if ($dnc_compid < 1) $dnc_compid = '';

                            $status='FAILED';
                            if (strlen($dnc_number) >= 6) {
                                if ($dnc_compid=='') {
                                    $stmt=sprintf("SELECT count(*) FROM osdial_dnc WHERE phone_number='%s';",mres($dnc_number));
                                } else {
                                    $stmt=sprintf("SELECT count(*) FROM osdial_dnc_company WHERE phone_number='%s' AND company_id='%s';",mres($dnc_number),mres($dnc_compid));
                                }
                                $rslt=mysql_query($stmt, $link);
                                $row=mysql_fetch_row($rslt);

                                if ($row[0] > 0) {
                                    # Existing record found, skipping.
                                    $status='SKIPPED';

                                } else {
                                    # No existing record found, adding.
                                    if ($dnc_compid=='') {
                                        $stmt=sprintf("INSERT INTO osdial_dnc (phone_number) VALUES ('%s');",mres($dnc_number));
                                    } else {
                                        $stmt=sprintf("INSERT INTO osdial_dnc_company (phone_number,company_id) VALUES ('%s','%s');",mres($dnc_number),mres($dnc_compid));
                                    }
                                    $rslt=mysql_query($stmt, $link);
                                    $ar = mysql_affected_rows($link);
                                    if ($ar>0) $status='ADDED';
                                }
                            }
                            if ($status == 'FAILED') $resclr = "red";
                            if ($status == 'SKIPPED')  $resclr = "orange";
                            if ($status == 'ADDED')  $resclr = "black";

                            $restab .= "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                            $restab .= "    <td align=right><font color=\"$resclr\">$o</font></td>\n";
                            $restab .= "    <td align=center><font color=\"$resclr\">$dnc_number</font></td>\n";
                            $restab .= "    <td align=center><font color=\"$resclr\">";
                            if ($dnc_compid>0) $restab .= ($dnc_compid+100);
                            $restab .= "</font></td>\n";
                            $restab .= "    <td align=center><font color=\"$resclr\">$status</font></td>\n";
                            $restab .= "  </tr>\n";
                            if ($status == 'FAILED') $dnc_fail++;
                            if ($status == 'SKIPPED') $dnc_skip++;
                            if ($status == 'ADDED') $dnc_add++;
                            echo $restab; $restab='';
                        }
                        $restab .= "  <tr class=tabheader>\n";
                        $restab .= "    <td>FAILED</td>\n";
                        $restab .= "    <td>SKIPPED</td>\n";
                        $restab .= "    <td>ADDED</td>\n";
                        $restab .= "    <td>TOTAL</td>\n";
                        $restab .= "  </tr>\n";
                        $restab .= "  <tr bgcolor=$oddrows class=\"row font2\">\n";
                        $restab .= "    <td align=right>$dnc_fail</td>\n";
                        $restab .= "    <td align=right>$dnc_skip</td>\n";
                        $restab .= "    <td align=right>$dnc_add</td>\n";
                        $restab .= "    <td align=right><b>$o</b></td>\n";
                        $restab .= "  </tr>\n";
                        $restab .= "  <tr class=tabfooter>\n";
                        $restab .= "    <td colspan=4></td>\n";
                        $restab .= "  </tr>\n";
                        $restab .= "</table>\n";
                        echo $restab; $restab='';


                    } else {
                        $loaderr = "<tr bgcolor=$oddrows><td align=center><font color=red>DNC UPLOAD ERROR - File must be in CSV, PSV or TAB format.</font></td></tr>\n";
                        $dncfile_name='';
                    }
                }

                # Display form.
                if ($dncfile_name=='') {
                    echo "<br><hr width=80%><br>";
                    echo "<center><font color=$default_text size=3>Upload DNC List</font></center>\n";
                    echo "<form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
                    echo "<input type=hidden name=ADD value=121>\n";
		            echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
                    echo $loaderr;
                    echo "  <tr bgcolor=$oddrows class=font2>\n";
                    echo "    <td align=center>CSV/PSV/TSV File:&nbsp;<input type=file name=\"dncfile\" value=\"\">$NWB#osdial_list-dnc$NWE</td>\n";
                    echo "  </tr>\n";
                    echo "  <tr class=tabfooter>\n";
                    echo "    <td align=center class=tabbutton><input type=submit name=SUBMIT value=\"UPLOAD\"></td>\n";
                    echo "  </tr>\n";
                    echo "</table>\n";
                    echo "</form>\n";
                }
            }

	        if ($LOG['user_level']==9 and $LOG['export_dnc']==1) {
                echo "<br><hr width=80%><br>";
                echo "<center><font color=$default_text size=3>Export DNC List</font></center>\n";
                echo "<form action=$PHP_SELF method=POST>\n";
                echo "<input type=hidden name=ADD value=121>\n";
                echo "<input type=hidden name=dnc_prepare_export value=1>\n";
		        echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
                echo "  <tr bgcolor=$oddrows class=font2>\n";
                echo "    <td align=center>Click EXPORT to begin downloading DNC. $NWB#osdial_list-dnc$NWE</td>\n";
                echo "  </tr>\n";
                echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton><input type=submit name=SUBMIT value=\"EXPORT\"></td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";
            }
        }

    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=125 generates test leads to test campaign
######################
if ($ADD==125) {
	if ($LOGmodify_lists==1)	{
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
	if ($LOGmodify_lists==1)	{
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
	if ($LOGmodify_lists==1)	{
        $stmt="SELECT count(*) from osdial_lists where list_id='$list_id';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>LIST NOT ADDED - there is already a list in the system with this ID</font>\n";
            $ADD=100;
        } else {
            if ( (strlen($campaign_id) < 2) or (strlen($list_name) < 2)  or ($list_id < 100) or (strlen($list_id) > 12) ) {
                echo "<br><font color=red>LIST NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>List ID must be between 2 and 12 characters in length\n";
                echo "<br>List name must be at least 2 characters in length\n";
                echo "<br>List ID must be greater than 100</font><br>\n";
                $ADD=100;
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
    if ($LOGmodify_lists==1) {
        if ( (strlen($list_name) < 2) or (strlen($campaign_id) < 2) ) {
            echo "<br><font color=red>LIST NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>list name must be at least 2 characters in length</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>LIST MODIFIED: $list_id</font></B>\n";

            $stmt="UPDATE osdial_lists set list_name='$list_name',campaign_id='$campaign_id',active='$active',list_description='$list_description',list_changedate='$SQLdate',scrub_dnc='$scrub_dnc',cost='$cost',web_form_address='" . mres($web_form_address) . "',web_form_address2='" . mres($web_form_address2) . "',list_script='" . mres($script_id) . "' where list_id='$list_id';";
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
        $ADD=311;	# go to list modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=511 confirmation before deletion of list
######################

if ($ADD==511) {
    if ($LOGmodify_lists==1) {
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
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=611 delete list record and all leads within it
######################

if ($ADD==611) {
    if ($LOGmodify_lists==1) {
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
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=311 modify list info in the system
######################

if ($ADD==311) {
    if ($LOGmodify_lists==1) {
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
        $script_id = $row[13];

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

        $stmt=sprintf("SELECT campaign_id,campaign_name from osdial_campaigns where campaign_id IN %s order by campaign_id", $LOG['allowed_campaignsSQL']);
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
        echo "<tr bgcolor=$oddrows><td align=right>List Change Date: </td><td align=left>$list_changedate &nbsp; $NWB#osdial_lists-list_changedate$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>List Last Call Date: </td><td align=left>$list_lastcalldate &nbsp; $NWB#osdial_lists-list_lastcalldate$NWE</td></tr>\n";
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
                echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
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
        echo "  <tr style=\"cursor:crosshair;\" class=tabheader>\n";
        echo "    <td style=\"cursor:crosshair;\" align=left>STATUS</td>\n";
        echo "    <td style=\"cursor:crosshair;\" align=left>STATUS&nbsp;NAME</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            $flabel = $first;
            if ($first == 30) $flabel = "30+";
            if (strlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $flabel;
            if (strlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $flabel;
            if (strlen($flabel) == 3) $flabel = '&nbsp;' . $flabel;
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

        echo "  <tr style=\"cursor:crosshair;\" class=tabfooter>";
        echo "    <td style=\"cursor:crosshair;\" align=left colspan=2>TOTAL</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            if ($all_called_count[$first] == 0 or $all_called_count[$first] == '') $all_called_count[$first] = '0';
            $flabel = $all_called_count[$first];
            if (strlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $all_called_count[$first];
            if (strlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $all_called_count[$first];
            if (strlen($flabel) == 3) $flabel = '&nbsp;' . $all_called_count[$first];
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
        echo "  <tr style=\"cursor:crosshair;\" class=tabheader>\n";
        echo "    <td style=\"cursor:crosshair;\" align=left>STATUS</td>\n";
        echo "    <td style=\"cursor:crosshair;\" align=left>STATUS&nbsp;NAME</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            $flabel = $first;
            if ($first == 30) $flabel = "30+";
            if (strlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $flabel;
            if (strlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $flabel;
            if (strlen($flabel) == 3) $flabel = '&nbsp;' . $flabel;
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

        echo "  <tr style=\"cursor:crosshair;\" class=tabfooter>";
        echo "    <td style=\"cursor:crosshair;\" align=left colspan=2>TOTAL</td>";
        $first = $all_called_first;
        while ($first <= $all_called_last) {
            if ($all_called_count[$first] == 0 or $all_called_count[$first] == '') $all_called_count[$first] = '0';
            $flabel = $all_called_count[$first];
            if (strlen($flabel) == 1) $flabel = '&nbsp;&nbsp;&nbsp;' . $all_called_count[$first];
            if (strlen($flabel) == 2) $flabel = '&nbsp;&nbsp;' . $all_called_count[$first];
            if (strlen($flabel) == 3) $flabel = '&nbsp;' . $all_called_count[$first];
            echo "    <td style=\"cursor:crosshair;\" align=right class=right title=\"$first Attempt Total: $all_called_count[$first] Calls\">$flabel</td>";
            $first++;
        }
        echo "    <td style=\"cursor:crosshair;\" align=right title=\"Total: $leads_in_list Calls\">$leads_in_list</td>\n";
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
		echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
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
        echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=311&list_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=311&list_id=$row[0]\">$row[0]</a></td>\n";
        echo "    <td>$row[1]</td>\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=100&camp=$row[2]&dispact=$dispact\">" . mclabel($row[2]) . "</a></td>\n";
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
