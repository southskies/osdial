<?php
#
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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



#####################################################################################################
####  ADD=121 - This file controls DNC Maintenance.  Searching, Adding, Removing, Loading, Exporting
#####################################################################################################

if ($ADD==121) {
	if ($LOG['modify_lists']==1) {

        echo "<center><br><font class=top_header class=top_header color=$default_text size=4>DO-NOT-CALL MAINTENANCE</font></center><br><br>\n";


        # Declare some globals.
        $dncs=0; $dncc=0;
        $searchres=''; $addres=''; $loadres=''; $deleteres='';
        $searcherr=''; $adderr=''; $loaderr=''; $deleteerr='';
        if ($SUB=='') $SUB=0;


        ###############################################
        ####  SUB=1 - Performs Search for DNC Records
        ###############################################
        if ($SUB==1) {
            if (OSDstrlen($dnc_search_phone) < 3) {
                $searcherr = "<tr bgcolor=$oddrows><td align=center><font color=red>DNC SEARCH FAILED - The phone number should be at least 3 digits.</font></td></tr>\n";
            } else {
                $mcsearch='';
                if ($LOG['multicomp'] > 0) {
                    if (OSDpreg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
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
                    if (OSDpreg_match('/SYSTEM|BOTH/',$LOG['company']['dnc_method'])) {
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
                if ($searchres!='') $searchres = "<tr bgcolor=$oddrows><td>" . $searchres . "</td></tr>\n";
            }



        ###############################################
        ####  SUB=2 - Performs Addition of DNC Record
        ###############################################
        } elseif ($SUB==2) {
            if (OSDstrlen($dnc_add_phone) < 3) {
                $adderr = "<tr bgcolor=$oddrows><td align=center><font color=red>DNC RECORD NOT ADDED - The phone number should be at least 3 digits.</font></td></tr>\n";
            } else {
                $dncsskip=0;
                if (OSDstrlen($dnc_add_phone)==3) $dnc_add_phone .= 'XXXXXXX';
                $dnc_add_phone = OSDpreg_replace('/x/','X',$dnc_add_phone);

                if ($LOG['multicomp_user'] > 0) {
                    if (OSDpreg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                        $stmt = sprintf("SELECT count(*) FROM osdial_dnc_company WHERE company_id='%s' AND phone_number='%s';", mres($LOG['company_id']),mres($dnc_add_phone));
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        $dncc=$row[0];
                    }
                    if (OSDpreg_match('/COMPANY/',$LOG['company']['dnc_method'])) $dncsskip++;
                }

                if ($dncsskip==0) {
                    $stmt = sprintf("SELECT count(*) FROM osdial_dnc WHERE phone_number='%s';", mres($dnc_add_phone));
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $dncs=$row[0];
                }

                if ($dncs > 0 or $dncc > 0) {
                    $adderr = "<tr bgcolor=$oddrows class=font2><td align=center><font color=red>DNC NOT ADDED - This phone number is already in the ";
                    if ($dncs > 0 and $dncc > 0) {
                        $adderr .= "System and Company";
                    } elseif ($dncs > 0) {
                        $adderr .= "System";
                    } elseif ($dncc > 0) {
                        $adderr .= "Company";
                    }
                    $adderr .= " Do Not Call List: $dnc_add_phone</font></td></tr>\n";
                } else {
                    if ($LOG['multicomp_user'] > 0 and OSDpreg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                        $stmt = sprintf("INSERT INTO osdial_dnc_company (company_id,phone_number) values('%s','%s');", mres($LOG['company_id']),mres($dnc_add_phone));
                    } else {
                        $stmt = sprintf("INSERT INTO osdial_dnc (phone_number) values('%s');", mres($dnc_add_phone));
                    }
                    $rslt=mysql_query($stmt, $link);
                    $addres = "<tr bgcolor=$oddrows class=font2><td align=center>DNC ADDED SUCCESSFULLY: $dnc_add_phone</td></tr>\n";

                    ### LOG INSERTION TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|ADD A NEW DNC NUMBER|$PHP_AUTH_USER|$ip|'$dnc_add_phone'|\n");
                        fclose($fp);
                    }
                }
            }



        #####################################################################################
        ####  SUB=3,4,5,6 - Deletion of DNC record.  Prompts user 3 times for confirmation.
        #####################################################################################
        } elseif ($LOG['user_level']==9 and $LOG['delete_dnc']==1 and $SUB>=3 and $SUB<=6) {
	        if (OSDstrlen($dnc_delete_phone) < 6) {
                $deleteerr = "<tr bgcolor=$oddrows><td align=center><font color=red>DNC DELETION FAILED - The phone number should be at least 6 digits.</font></td></tr>\n";
                $SUB=0;

            ### Deletion Prompt 1 ###
            } elseif ($SUB==3) {
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

            ### Deletion Prompt 2 ###
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
                echo "      <input type=submit name=SUBMIT value=\"NO\">\n";
                echo "    </td>\n";
                echo "    <td align=center class=tabbutton bgcolor=red width=50%>\n";
                echo "      <input type=button name=cancel1 value=\"YES\" onclick=\"document.getElementById('SUB').value='';document.getElementById('DDP').value='';submit();\">\n";
                echo "    </td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";

            ### Deletion Prompt 3 ###
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
                echo "      <input type=button name=cancel1 value=\"CANCEL\" onclick=\"document.getElementById('SUB').value='';document.getElementById('DDP').value='';submit();\">\n";
                echo "    </td>\n";
                echo "    <td align=center class=tabbutton bgcolor=red width=50%>\n";
                echo "      <input type=submit name=SUBMIT value=\"DELETE\">\n";
                echo "    </td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";

            ### Actual Deletion ###
	        } elseif ($SUB==6) {
                $dnc_delete_phone = OSDpreg_replace('/x/','X',$dnc_delete_phone);
                if ($LOG['multicomp_user'] > 0) {
                    if (OSDpreg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                        $stmt = sprintf("SELECT * FROM osdial_dnc_company WHERE company_id='%s' AND phone_number='%s';", mres($LOG['company_id']),mres($dnc_delete_phone));
                        $rslt=mysql_query($stmt, $link);
                        $dncc = mysql_num_rows($rslt);
                    }
                    if ($dncc==0) {
                        $stmt = sprintf("SELECT count(*) FROM osdial_dnc WHERE phone_number='%s';", mres($dnc_delete_phone));
                        $rslt=mysql_query($stmt, $link);
                        $dncs=mysql_fetch_row($rslt);
                        if ($dncs>0 and OSDpreg_match('/SYSTEM|BOTH/',$LOG['company']['dnc_method'])) {
                            $deleteres="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was NOT FOUND in the Do-Not-Call list for company ".$LOG['company_id'].", however, it is in the System-wide Do-Not-Call list.  If you need this number removed, you should contact the system administrator.</td></tr>\n";
                        } else {
                            $deleteres="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was NOT FOUND in the Do-Not-Call list for company ".$LOG['company_id'].".</td></tr>\n";
                        }
                    } else {
                        $stmt = sprintf("DELETE FROM osdial_dnc_company WHERE company_id='%s' AND phone_number='%s';", mres($LOG['company_id']),mres($dnc_delete_phone));
                        $rslt=mysql_query($stmt, $link);
                        $deleteres="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was SUCCESSFULLY deleted from the Do-Not-Call list for company ".$LOG['company_id'].".</td></tr>\n";

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
                        $deleteres="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was NOT FOUND in the Do-Not-Call list.</td></tr>\n";
                    } else {
                        $stmt = sprintf("DELETE FROM osdial_dnc WHERE phone_number='%s';",mres($dnc_delete_phone));
                        $rslt=mysql_query($stmt, $link);
                        $deleteres="<tr bgcolor=$oddrows class=font2><td align=center>\"$dnc_delete_phone\" was SUCCESSFULLY deleted from the Do-Not-Call list.</td></tr>\n";

                        ### LOG INSERTION TO LOG FILE ###
                        if ($WeBRooTWritablE > 0) {
                            $fp = fopen ("./admin_changes_log.txt", "a");
                            fwrite ($fp, "$date|DELETE A DNC NUMBER|$PHP_AUTH_USER|$ip|'$dnc_delete_phone'|\n");
                            fclose($fp);
                        }
                    }
                }
                $dnc_delete_phone='';
                $SUB=0;
            }



        ########################################################################
        ##  SUB=7 - Routine to recieve uploaded file and add it to DNC tables.
        ########################################################################
	    } elseif ($LOG['user_level']==9 and $LOG['load_dnc']==1 and $SUB==7) {
            $dncfile_name='';
            if (isset($_FILES["dncfile"])) {
                $dncfile_name=$_FILES["dncfile"]['name'];
                $dncfile_path=$_FILES['dncfile']['tmp_name'];
            }
            if (OSDpreg_match('/\.txt$|\.csv$|\.psv$|\.tsv$|\.tab$/i', $dncfile_name)) {
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

                ob_flush(); flush();
                $file=fopen($dncfile, "r");

                echo "<hr width=80%><br><br><center><font size=3 color='$default_text'><b>Processing $delim_name file...</b></font></center><br>\n";
                ob_flush(); flush();

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
                    $dnc_number = OSDpreg_replace('/x/','X',OSDpreg_replace('/[^0-9Xx\*]/','',$csvrow[0]));
                    $dnc_compid='';
                    if ($LOG['multicomp_user']>0 and OSDpreg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) $dnc_compid = $LOG['company_id'];
                    if ($LOG['multicomp_admin']>0) $dnc_compid = (OSDpreg_replace('/[^0-9]/','',$csvrow[1]) - 100);
                    if ($dnc_compid < 1) $dnc_compid = '';

                    $status='FAILED';
                    if (OSDstrlen($dnc_number) >= 6) {
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
                    if ($o % 50 == 0) {
                        echo $restab; $restab='';
                        ob_flush(); flush();
                    }
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



        ################################################################
        ##  SUB=8 - Routine to export DNC list.
        ################################################################
	    } elseif ($LOG['user_level']==9 and $LOG['export_dnc']==1 and $SUB==8) {
            $dncdata='';
            if ($LOG['multicomp_user'] > 0) {
                $dncdata .= "Phone|Company|Created\n";
                if (OSDpreg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                    $stmt = sprintf("SELECT phone_number,(company_id+100) AS cid,creation_date FROM osdial_dnc_company WHERE company_id='%s';",mres($LOG['company_id']));
                    $rslt=mysql_query($stmt, $link);
                    $dncc = mysql_num_rows($rslt);
                    $o=0;
                    while ($dncc > $o) {
                        $rowx=mysql_fetch_row($rslt);
                        $dncdata .= $rowx['0'].'|'.$rowx['1'].'|'.$rowx['2']."\n";
                        $o++;
                    }
                }
                if (OSDpreg_match('/SYSTEM|BOTH/',$LOG['company']['dnc_method'])) {
                    $stmt = "SELECT phone_number,'',creation_date FROM osdial_dnc;";
                    $rslt=mysql_query($stmt, $link);
                    $dncs = mysql_num_rows($rslt);
                    $o=0;
                    while ($dncs > $o) {
                        $rowx=mysql_fetch_row($rslt);
                        $dncdata .= $rowx['0'].'|'.$rowx['1'].'|'.$rowx['2']."\n";
                        $o++;
                    }
                }
            } elseif ($LOG['multicomp_admin'] > 0) {
                $dncdata .= "Phone|Company|Created\n";
                #echo "g.value+=\"Phone|Company|Created\\n\";\n";
                $stmt = "SELECT phone_number,(company_id+100) AS cid,creation_date FROM osdial_dnc_company ORDER BY cid;";
                $rslt=mysql_query($stmt, $link);
                $dncc = mysql_num_rows($rslt);
                $o=0;
                while ($dncc > $o) {
                    $rowx=mysql_fetch_row($rslt);
                    $dncdata .= $rowx['0'].'|'.$rowx['1'].'|'.$rowx['2']."\n";
                    $o++;
                }

                $stmt = "SELECT phone_number,'',creation_date FROM osdial_dnc;";
                $rslt=mysql_query($stmt, $link);
                $dncs = mysql_num_rows($rslt);
                $o=0;
                while ($dncs > $o) {
                    $rowx=mysql_fetch_row($rslt);
                    $dncdata .= $rowx['0'].'|'.$rowx['1'].'|'.$rowx['2']."\n";
                    $o++;
                }
            } else {
                $dncdata .= "Phone|Created\n";
                $stmt = "SELECT phone_number,creation_date FROM osdial_dnc;";
                $rslt=mysql_query($stmt, $link);
                $dncs = mysql_num_rows($rslt);
                $o=0;
                while ($dncs > $o) {
                    $rowx=mysql_fetch_row($rslt);
                    $dncdata .= $rowx['0'].'|'.$rowx['1']."\n";
                    $o++;
                }
            }
            echo "<hr width=80%><br>";
            echo "<form action=\"tocsv.php\" method=POST id=\"tocsvform\">\n";
            echo "<center><font color=$default_text size=3>Preparing DNC Download</font></center>\n";
            echo "<input type=hidden name=ADD value=121>\n";
		    echo "<table width=500 bgcolor=grey align=center cellspacing=1>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=center class=font2>Please be patient, your download will begin shortly.</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=center class=font2>\n";
            echo "      <input type=hidden name=name value=\"OSDial_DNC_Export-".$LOG['user']."\">\n";
            echo "      <textarea name=\"glob\" id=\"glob\" style=\"visibility:hidden;white-space:nowrap;font-size:5pt;\" wrap=off cols=100 rows=2>$dncdata</textarea>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabbutton>\n";
            echo "      <input type=submit name=SUBMIT id=\"dlbutton\" value=\"CLICK\">\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "</table>\n";
            echo "</form>\n";
            echo "<center><iframe name=\"downloadframe\" id=\"downloadframe\" height=\"100px\" width=\"500px\" scrolling=\"no\" frameborder=\"0\" src=\"tocsv.php\" style=\"visibility:hidden;\"></iframe></center>\n";
            echo "<script type=\"text/javascript\">\n";
            echo "var iframe = document.getElementById('downloadframe');\n";
            echo "var form = document.getElementById('tocsvform');\n";
            echo "var g = document.getElementById('glob');\n";
            echo "form.target = iframe.id;\n";
            echo "document.getElementById('dlbutton').disabled=true;\n";
            echo "document.getElementById('dlbutton').value='PROCESSING';\n";
            echo "</script>\n";
            ob_flush(); flush();
            usleep(300000);
            echo "<script type=\"text/javascript\">\n";
            echo "form.submit();\n";
            echo "</script>\n";
            ob_flush(); flush();
            usleep(300000);
            echo "<script type=\"text/javascript\">\n";
            echo "document.getElementById('dlbutton').value='DOWNLOAD WILL START SOON';\n";
            echo "</script>\n";
            ob_flush(); flush();
        }



        ###############################################
        ####  Main Menu for DNC Maintenance  
        ###############################################
        if ($SUB<3) {


            #### DNC Search ####
            echo "<hr width=80%><br>";
            echo "<form action=$PHP_SELF method=POST>\n";
            echo "<center><font class=top_header2 color=$default_text size=3>Search DNC List</font></center>\n";
            echo "<input type=hidden name=ADD value=121>\n";
            echo "<input type=hidden name=SUB value=1>\n";
		    echo "<table class=shadedtable width=500 bgcolor=grey align=center cellspacing=1>\n";
            echo $searcherr;
            echo $searchres;
            echo "  <tr bgcolor=$oddrows class=font2>\n";
            echo "    <td align=center>Phone Number:&nbsp;<input type=text name=dnc_search_phone size=16 maxlength=15 value=\"$dnc_search_phone\">".helptag("osdial_list-dnc")."</td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabbutton><input type=submit name=SUBMIT value=\"SEARCH\"></td>\n";
            echo "  </tr>\n";
            echo "</table>\n";
            echo "</form>\n";


            #### DNC Add ####
            echo "<br><hr width=80%><br>";
            echo "<center><font class=top_header2 color=$default_text size=3>Add Number to DNC List</font></center>\n";
            echo "<form action=$PHP_SELF method=POST>\n";
            echo "<input type=hidden name=ADD value=121>\n";
            echo "<input type=hidden name=SUB value=2>\n";
		    echo "<table class=shadedtable width=500 bgcolor=grey align=center cellspacing=1>\n";
            echo $adderr;
            echo $addres;
            echo "  <tr bgcolor=$oddrows class=font2>\n";
            echo "    <td align=center>Phone Number:&nbsp;<input type=text name=dnc_add_phone size=16 maxlength=15 value=\"$dnc_add_phone\">".helptag("osdial_list-dnc")."</td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabbutton bgcolor=purple><input type=submit name=SUBMIT value=\"ADD\"></td>\n";
            echo "  </tr>\n";
            echo "</table>\n";
            echo "</form>\n";


            #### DNC Delete - Must be user_level 9 and have permission to remove DNC records. ####
	        if ($LOG['user_level']==9 and $LOG['delete_dnc']==1){
                echo "<br><hr width=80%><br>";
                echo "<center><font class=top_header2 color=$default_text size=3>Delete Number from DNC List</font></center>\n";
                echo "<form action=$PHP_SELF method=POST>\n";
                echo "<input type=hidden name=ADD value=121>\n";
                echo "<input type=hidden name=SUB value=3>\n";
		        echo "<table class=shadedtable width=500 bgcolor=grey align=center cellspacing=1>\n";
                echo $deleteerr;
                echo $deleteres;
                echo "  <tr bgcolor=$oddrows class=font2>\n";
                echo "    <td align=center>Phone Number:&nbsp;<input type=text name=dnc_delete_phone size=16 maxlength=15 value=\"$dnc_delete_phone\">".helptag("osdial_list-dnc")."</td>\n";
                echo "  </tr>\n";
                echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton bgcolor=red><input type=submit name=SUBMIT value=\"DELETE\"></td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";
            }


            #### DNC Upload - Must be user_level 9 and have permission to load DNC records. ####
	        if ($LOG['user_level']==9 and $LOG['load_dnc']==1) {
                echo "<br><hr width=80%><br>";
                echo "<center><font class=top_header2 color=$default_text size=3>Upload DNC List</font></center>\n";
                echo "<form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
                echo "<input type=hidden name=ADD value=121>\n";
                echo "<input type=hidden name=SUB value=7>\n";
		        echo "<table class=shadedtable width=500 bgcolor=grey align=center cellspacing=1>\n";
                echo $loaderr;
                echo $loadres;
                echo "  <tr bgcolor=$oddrows class=font2>\n";
                echo "    <td align=center>CSV/PSV/TSV File:&nbsp;<input type=file name=\"dncfile\" value=\"\">".helptag("osdial_list-dnc")."</td>\n";
                echo "  </tr>\n";
                echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton><input type=submit name=SUBMIT value=\"UPLOAD\"></td>\n";
                echo "  </tr>\n";
                echo "</table>\n";
                echo "</form>\n";
            }


            #### DNC Export - Must be user_level 9 and have permission to export DNC records. ####
	        if ($LOG['user_level']==9 and $LOG['export_dnc']==1) {
                echo "<br><hr width=80%><br>";
                echo "<center><font class=top_header2 color=$default_text size=3>Export DNC List</font></center>\n";
                echo "<form action=$PHP_SELF method=POST>\n";
                echo "<input type=hidden name=ADD value=121>\n";
                echo "<input type=hidden name=SUB value=8>\n";
		        echo "<table class=shadedtable width=500 bgcolor=grey align=center cellspacing=1>\n";
                echo "  <tr bgcolor=$oddrows class=font2>\n";
                echo "    <td align=center>Click EXPORT to begin downloading DNC. ".helptag("osdial_list-dnc")."</td>\n";
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


?>
