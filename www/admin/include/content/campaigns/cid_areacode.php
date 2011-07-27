<?php
#
# Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
# ADD=2ca adds the new cid areacode entry to the system
######################

if ($ADD=="2ca") {
    if ($LOG['modify_campaigns'] == 1) {
        $cidfile_name='';
        if (isset($_FILES["cidfile"])) {
            $cidfile_name=$_FILES["cidfile"]['name'];
	        $cidfile_path=$_FILES['cidfile']['tmp_name'];
        }

        # Process file if sent.
        if ($cidfile_name!='') {
			if (preg_match('/\.txt$|\.csv$|\.psv$|\.tsv$|\.tab$/i', $cidfile_name)) {
                $cidfile='';
				if ($WeBRooTWritablE > 0) {
					copy($cidfile_path, "$WeBServeRRooT/admin/osdial_temp_file.csv");
					$cidfile = "$WeBServeRRooT/admin/osdial_temp_file.csv";
				} else {
					copy($cidfile_path, "/tmp/osdial_temp_file.csv");
					$cidfile = "/tmp/osdial_temp_file.csv";
				}

			    $file=fopen($cidfile, "r");

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
			    $file=fopen($cidfile, "r");

			    echo "<center><font size=3 color='$default_text'><b>Processing $delim_name file...</b></font></center><br>\n";
                ob_flush();
                flush();

                $restab =  "<font color=$default_text size=3>FILE UPLOAD RESULTS</font>\n";
                $restab .= "<table border=0 cellspacing=1 width=400 align=center bgcolor=grey>\n";
                $restab .= "  <tr class=tabheader>\n";
                $restab .= "    <td>#</td>\n";
                $restab .= "    <td>CAMPAIGN</td>\n";
                $restab .= "    <td>AREACODE</td>\n";
                $restab .= "    <td>CALLERID</td>\n";
                $restab .= "    <td>CALLERID NAME</td>\n";
                $restab .= "    <td>STATUS</td>\n";
                $restab .= "  </tr>\n";

                $cnt_fail=0;
                $cnt_skip=0;
                $cnt_update=0;
                $cnt_add=0;
                $o=0;
			    while($csvrow=fgetcsv($file, 1000, $delimiter)) {
                    $o++;
                    $areacode = preg_replace('/[^0-9]/','',$csvrow[0]);
                    $cid_number = preg_replace('/[^0-9]/','',$csvrow[1]);
                    $cid_name = $csvrow[2];

                    $status='FAILED';
                    if (strlen($areacode) == 3 and strlen($cid_number) >= 8 and strlen($cid_number) <= 20) {
                        $stmt=sprintf("SELECT count(*) FROM osdial_campaign_cid_areacodes WHERE campaign_id='%s' AND areacode='%s';",mres($campaign_id),mres($areacode));
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);

                        if ($row[0] > 0) {
                            # Existing record found, updating.
                            $stmt=sprintf("UPDATE osdial_campaign_cid_areacodes SET cid_number='%s',cid_name='%s' WHERE campaign_id='%s' AND areacode='%s';",mres($cid_number),mres($cid_name),mres($campaign_id),mres($areacode));
                            $rslt=mysql_query($stmt, $link);
                            $ar = mysql_affected_rows($link);
                            $status='SKIPPED';
                            if ($ar>0) $status='UPDATED';

                        } else {
                            # No existing record found, adding.
                            $stmt=sprintf("INSERT INTO osdial_campaign_cid_areacodes (campaign_id,areacode,cid_number,cid_name) VALUES ('%s','%s','%s','%s');",mres($campaign_id),mres($areacode),mres($cid_number),mres($cid_name));
                            $rslt=mysql_query($stmt, $link);
                            $ar = mysql_affected_rows($link);
                            if ($ar>0) $status='ADDED';
                        }
                    }
                    if ($status == 'FAILED') $resclr = "red";
                    if ($status == 'SKIPPED')  $resclr = "orange";
                    if ($status == 'UPDATED')  $resclr = "blue";
                    if ($status == 'ADDED')  $resclr = "black";

                    $restab .= "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                    $restab .= "    <td align=right><font color=\"$resclr\">$o</font></td>\n";
                    $restab .= "    <td align=center><font color=\"$resclr\">$campaign_id</font></td>\n";
                    $restab .= "    <td align=center><font color=\"$resclr\">$areacode</font></td>\n";
                    $restab .= "    <td align=center><font color=\"$resclr\">$cid_number</font></td>\n";
                    $restab .= "    <td align=left><font color=\"$resclr\">$cid_name</font></td>\n";
                    $restab .= "    <td align=center><font color=\"$resclr\">$status</font></td>\n";
                    $restab .= "  </tr>\n";
                    if ($status == 'FAILED') $cnt_fail++;
                    if ($status == 'SKIPPED') $cnt_skip++;
                    if ($status == 'UPDATED') $cnt_update++;
                    if ($status == 'ADDED') $cnt_add++;
                }
                $restab .= "  <tr class=tabheader>\n";
                $restab .= "    <td></td>\n";
                $restab .= "    <td>FAILED</td>\n";
                $restab .= "    <td>SKIPPED</td>\n";
                $restab .= "    <td>UPDATED</td>\n";
                $restab .= "    <td>ADDED</td>\n";
                $restab .= "    <td>TOTAL</td>\n";
                $restab .= "  </tr>\n";
                $restab .= "  <tr bgcolor=$oddrows class=\"row font2\">\n";
                $restab .= "    <td></td>\n";
                $restab .= "    <td align=right>$cnt_fail</td>\n";
                $restab .= "    <td align=right>$cnt_skip</td>\n";
                $restab .= "    <td align=right>$cnt_update</td>\n";
                $restab .= "    <td align=right>$cnt_add</td>\n";
                $restab .= "    <td align=right><b>$o</b></td>\n";
                $restab .= "  </tr>\n";
                $restab .= "  <tr class=tabfooter>\n";
                $restab .= "    <td colspan=6></td>\n";
                $restab .= "  </tr>\n";
                $restab .= "</table>\n";

            } else {
                echo "<br><font color=red>CALLERID/AREACODE UPLOAD ERROR - File must be in CSV, PSV or TAB format.</font>\n";
            }

            echo "</font>\n";
            $SUB=2;
            $ADD="3ca";

        # Manual addition.
        } else {
            $stmt=sprintf("SELECT count(*) FROM osdial_campaign_cid_areacodes WHERE campaign_id='%s' AND areacode='%s';",mres($campaign_id),mres($areacode));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0) {
                echo "<br><font color=red>CALLERID/AREACODE NOT ADDED - there is already an entry for this campaign with this areacode</font>\n";
            } else {
                if (strlen($campaign_id) < 2 or strlen($areacode) != 3 or strlen($cid_number) < 8 or strlen($cid_number) > 20) {
                    echo "<br><font color=red>CALLERID/AREACODE NOT ADDED - Please go back and look at the data you entered\n";
                    echo "<br>areacode must be 3 characters in length: $areacode\n";
                    echo "<br>cid_number must be between 8 and 20 characters in length: $cid_number</font><br>\n";
                } else {
                    echo "<br><b><font color=$default_text>CALLERID AREACODE ADDED: $campaign_id - $areacode - $cid_number</font></b>\n";

                    $stmt=sprintf("INSERT INTO osdial_campaign_cid_areacodes (campaign_id,areacode,cid_number,cid_name) VALUES ('%s','%s','%s','%s');",mres($campaign_id),mres($areacode),mres($cid_number),mres($cid_name));
                    $rslt=mysql_query($stmt, $link);

                    ### LOG CHANGES TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|ADD A NEW CID AREACODE|$PHP_AUTH_USER|$ip|$stmt|\n");
                        fclose($fp);
                    }
                }
            }
            $SUB=2;
            $ADD="3ca";
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=4ca modify cid areacode in the system
######################

if ($ADD=="4ca") {
    if ($LOG['modify_campaigns'] == 1) {
        if (strlen($campaign_id) < 2 or strlen($areacode) != 3 or strlen($cid_number) < 8 or strlen($cid_number) > 20) {
            echo "<br><font color=red>CALLERID/AREACODE NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>areacode must be 3 characters in length: $areacode\n";
            echo "<br>cid_number must be between 8 and 20 characters in length: $cid_number</font><br>\n";
        } else {
            $oldareacode = get_variable("oldareacode");
            $stmt=sprintf("SELECT count(*) FROM osdial_campaign_cid_areacodes WHERE campaign_id='%s' AND areacode='%s';",mres($campaign_id),mres($areacode));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0 and $oldareacode != $areacode) {
                echo "<br><b><font color=red>CALLERID/AREACODE MODIFY FAILED (DUPLICATE): Areacode $areacode already exists.</font></b>\n";
            } else {
                echo "<br><b><font color=$default_text>CALLERID/AREACODE MODIFIED: $campaign_id - $areacode - $cid_number</font></b>\n";

                $stmt=sprintf("UPDATE osdial_campaign_cid_areacodes SET areacode='%s',cid_number='%s',cid_name='%s' WHERE campaign_id='%s' AND areacode='%s';",mres($areacode),mres($cid_number),mres($cid_name),mres($campaign_id),mres($oldareacode));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|MODIFY CID AREACODE|$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }
        $SUB=2;
        $ADD="3ca";    # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=51 confirmation before deletion of cid areacodes
######################

if ($ADD == "5ca") {
    if ($LOG['modify_campaigns'] == 1) {
        if (strlen($campaign_id) < 2) {
            echo "<br><font color=red>CALLERID/AREACODES NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>Campaign_id be at least 2 characters in length</font><br<\n";
        } else {
            echo "<br><b><font color=$default_text>CALLERID/AREACODE DELETION CONFIRMATION: $campaign_id - $areacode</b>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=6ca&campaign_id=$campaign_id&areacode=000&CoNfIrM=YES\">Click here to delete ALL CallerID/Areacode Mappings for $campaign_id</a></font><br><br><br>\n";
        }
        $SUB=2;
        $ADD="3ca";        # go to campaign modification below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=6ca delete cid areacode in the system
######################

if ($ADD=="6ca") {
    if ($LOG['modify_campaigns'] == 1) {
        if (strlen($campaign_id) < 2 or strlen($areacode) < 1) {
            echo "<br><font color=red>CALLERID/AREACODE NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>areacode must be between 1 and 4 characters in length</font><br>\n";
        } else {
            echo "<br><b><font color=$default_text>CALLERID/AREACODES DELETED: $campaign_id - $areacode</font></b>\n";

            $stmt='';
            if ($areacode!='000') $stmt=sprintf("AND areacode='%s'",mres($areacode));
            $stmt=sprintf("DELETE FROM osdial_campaign_cid_areacodes WHERE campaign_id='%s' %s;",mres($campaign_id),$stmt);
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|DELETE CID AREACODE|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        echo "</font>\n";
        $SUB=2;
        $ADD="3ca";    # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=3ca display all campaign cid areacodes
######################
if ($ADD == "3ca" and $SUB != 2) {
    if ($LOG['modify_campaigns'] == 1) {
        echo "<center>\n";
        echo "  <br><font color=$default_text size=+1>CALLERID/AREACODES CAMPAIGNS</font><br><br>\n";
        echo "  <table width=$section_width cellspacing=0 cellpadding=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td>CAMPAIGN</td>\n";
        echo "      <td>NAME</td>\n";
        echo "      <td align=center>LINKS</td>\n";
        echo "    </tr>\n";

        $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE campaign_id IN %s ORDER BY campaign_id;", $LOG['allowed_campaignsSQL']);
        $rslt=mysql_query($stmt, $link);
        $campaigns_to_print = mysql_num_rows($rslt);

        $o=0;
        while ($campaigns_to_print > $o) {
            $row=mysql_fetch_row($rslt);
            $campaigns_id_list[$o] = $row[0];
            $campaigns_name_list[$o] = $row[1];
            $o++;
        }

        $o=0;
        while ($campaigns_to_print > $o) {
            echo "    <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3ca&SUB=2&campaign_id=$campaigns_id_list[$o]';\">\n";
            echo "      <td><a href=\"$PHP_SELF?ADD=3ca&SUB=2&campaign_id=$campaigns_id_list[$o]\">" . mclabel($campaigns_id_list[$o]) . "</a></td>\n";
            echo "      <td>$campaigns_name_list[$o]</td>\n";
            echo "      <td align=center><a href=\"$PHP_SELF?ADD=3ca&SUB=2&campaign_id=$campaigns_id_list[$o]\">CALLERID/AREACODES</a></td>\n";
            echo "    </tr>\n";
            $o++;
        }

        echo "    <tr class=tabfooter>\n";
        echo "      <td colspan=4></td>\n";
        echo "    </tr>\n";
        echo "  </table>\n";
        echo "</center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

##### CAMPAIGN CALLERID/AREACODES #####
if ($ADD == "3ca" and $SUB == 2) {
    if ($LOG['modify_campaigns'] == 1) {
        echo "<center>\n";
        echo "  <br><font color=$default_text size=+1>CALLERID/AREACODES FOR THIS CAMPAIGN</font><br>\n";
        #echo " &nbsp; $NWB#osdial_campaign_cid_areacodes$NWE</font><br>\n";
        echo "  <table bgcolor=grey width=500 cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center>AREACODE</td>\n";
        echo "      <td align=center>CALLERID</td>\n";
        echo "      <td align=center>CALLERID NAME</td>\n";
        echo "      <td align=center colspan=2>ACTIONS</td>\n";
        echo "    </tr>\n";

        $stmt=sprintf("SELECT * FROM osdial_campaign_cid_areacodes WHERE campaign_id='%s' ORDER BY areacode;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $areacodes_to_print = mysql_num_rows($rslt);
        $o=0;
        while ($areacodes_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $o++;

            echo "    <form action=$PHP_SELF method=POST>\n";
            echo "    <input type=hidden name=ADD value=4ca>\n";
            echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
            echo "    <input type=hidden name=oldareacode value=\"$rowx[1]\">\n";
            echo "    <tr " . bgcolor($o) . " class=\"row font1\">\n";
            echo "      <td align=center><input type=text size=3 maxlength=4 name=areacode value=\"$rowx[1]\"></td>\n";
            echo "      <td align=center><input type=text size=10 maxlength=20 name=cid_number value=\"$rowx[2]\"></td>\n";
            echo "      <td align=center><input type=text size=20 maxlength=40 name=cid_name value=\"$rowx[3]\"></td>\n";
            echo "      <td align=center><a href=\"$PHP_SELF?ADD=6ca&campaign_id=$campaign_id&areacode=$rowx[1]\">DELETE</a></td>\n";
            echo "      <td align=center class=tabbutton1><input type=submit name=submit value=MODIFY></td>\n";
            echo "    </tr>\n";
            echo "    </form>\n";
        }

        echo "    <tr " . bgcolor($o) . " class=\"row font1\">\n";
        echo "      <td colspan=5></td>\n";
        echo "    </tr>\n";
        echo "    <form action=$PHP_SELF method=POST>\n";
        echo "    <input type=hidden name=ADD value=2ca>\n";
        echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
        echo "    <tr class=tabfooter>\n";
        echo "      <td align=center><input type=text size=3 maxlength=4 name=areacode></td>\n";
        echo "      <td align=center><input type=text size=10 maxlength=20 name=cid_number></td>\n";
        echo "      <td align=center><input type=text size=20 maxlength=40 name=cid_name></td>\n";
        echo "      <td align=center class=tabbutton1 colspan=2><input type=submit name=submit value=ADD></td>\n";
        echo "    </tr>\n";
        echo "    </form>\n";
        echo "  </table>\n";

        if (!isset($restab)) {
            echo "  <br>\n";
            echo "  <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\"><br>\n";
            echo "  <input type=hidden name=ADD value=2ca>\n";
            echo "  <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
            echo "  <table bgcolor=grey width=300 cellspacing=1>\n";
            echo "    <tr class=tabfooter>\n";
            echo "      <td align=center colspan=2>UPLOAD A LIST OF CALLERID/AREACODES</td>\n";
            echo "    </tr>\n";
            echo "    <tr class=tabfooter>\n";
            echo "      <td align=center>\n";
            echo "        <input type=file name=\"cidfile\" value=\"\">\n";
            echo "      </td>\n";
            echo "      <td align=center class=tabbutton1><input type=submit name=submit value=UPLOAD></td>\n";
            echo "    </tr>\n";
            echo "  </table>\n";
            echo "  </form>\n";

        } else {
            echo "<br>\n";
            echo "<br>\n";
            echo "<br>\n";
            echo $restab;
        }

        echo "</center>\n";

        echo "<br><br><a href=\"$PHP_SELF?ADD=5ca&campaign_id=$campaign_id\">DELETE ALL CALLERID/AREACODES FOR THIS CAMPAIGN</a>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


?>
