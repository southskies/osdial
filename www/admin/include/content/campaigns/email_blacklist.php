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
# ADD=2eb adds the new email blacklist entry to the system
######################

if ($ADD=="2eb") {
    if ($LOG['modify_campaigns'] == 1) {
        $ebfile_name='';
        if (isset($_FILES["ebfile"])) {
            $ebfile_name=$_FILES["ebfile"]['name'];
	        $ebfile_path=$_FILES['ebfile']['tmp_name'];
        }

        # Process file if sent.
        if ($ebfile_name!='') {
			if (OSDpreg_match('/\.txt$|\.csv$|\.psv$|\.tsv$|\.tab$/i', $ebfile_name)) {
                $ebfile='';
				if ($WeBRooTWritablE > 0) {
					copy($ebfile_path, "$WeBServeRRooT/admin/osdial_temp_file.csv");
					$ebfile = "$WeBServeRRooT/admin/osdial_temp_file.csv";
				} else {
					copy($ebfile_path, "/tmp/osdial_temp_file.csv");
					$ebfile = "/tmp/osdial_temp_file.csv";
				}

			    $file=fopen($ebfile, "r");

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
			    $file=fopen($ebfile, "r");

			    echo "<center><font size=3 color='$default_text'><b>Processing $delim_name file...</b></font></center><br>\n";
                ob_flush();
                flush();

                $restab =  "<font color=$default_text size=3>FILE UPLOAD RESULTS</font>\n";
                $restab .= "<table border=0 cellspacing=1 width=400 align=center bgcolor=grey>\n";
                $restab .= "  <tr class=tabheader>\n";
                $restab .= "    <td>#</td>\n";
                $restab .= "    <td>CAMPAIGN</td>\n";
                $restab .= "    <td>EMAIL</td>\n";
                $restab .= "    <td></td>\n";
                $restab .= "    <td></td>\n";
                $restab .= "    <td>STATUS</td>\n";
                $restab .= "  </tr>\n";

                $cnt_fail=0;
                $cnt_skip=0;
                $cnt_update=0;
                $cnt_add=0;
                $o=0;
			    while($csvrow=fgetcsv($file, 1000, $delimiter)) {
                    $o++;
                    $email = OSDstrtolower($csvrow[0]);

                    $status='FAILED';
                    if (OSDstrlen($email) >= 10 and OSDpreg_match('/@/',$email)) {
                        $stmt=sprintf("SELECT count(*) FROM osdial_campaign_email_blacklist WHERE campaign_id='%s' AND email='%s';",mres($campaign_id),mres($email));
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);

                        if ($row[0] > 0) {
                            # Existing record found, updating.
                            $stmt=sprintf("UPDATE osdial_campaign_email_blacklist SET email='%s' WHERE campaign_id='%s' AND email='%s';",mres($email),mres($campaign_id),mres($email));
                            $rslt=mysql_query($stmt, $link);
                            $ar = mysql_affected_rows($link);
                            $status='SKIPPED';
                            if ($ar>0) $status='UPDATED';

                        } else {
                            # No existing record found, adding.
                            $stmt=sprintf("INSERT INTO osdial_campaign_email_blacklist (campaign_id,email) VALUES ('%s','%s');",mres($campaign_id),mres($email));
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
                    $restab .= "    <td align=center><font color=\"$resclr\">$email</font></td>\n";
                    $restab .= "    <td></td>\n";
                    $restab .= "    <td></td>\n";
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
                echo "<br><font color=red>EMAIL BLACKLIST UPLOAD ERROR - File must be in CSV, PSV or TAB format.</font>\n";
            }

            echo "</font>\n";
            $SUB=2;
            $ADD="3eb";

        # Manual addition.
        } else {
            $stmt=sprintf("SELECT count(*) FROM osdial_campaign_email_blacklist WHERE campaign_id='%s' AND email='%s';",mres($campaign_id),mres($email));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0) {
                echo "<br><font color=red>EMAIL NOT ADDED - there is already an entry for this campaign with this email</font>\n";
            } else {
                if (OSDstrlen($campaign_id) < 2 or OSDstrlen($email) <= 9 or !OSDpreg_match('/@/',$email)) {
                    echo "<br><font color=red>EMAIL NOT ADDED - Please go back and look at the data you entered\n";
                    echo "<br>email must be at lease 10 characters in length: $email</font><br>\n";
                } else {
                    echo "<br><b><font color=$default_text>EMAIL BLACKLIST ADDED: $campaign_id - $email</font></b>\n";

                    $stmt=sprintf("INSERT INTO osdial_campaign_email_blacklist (campaign_id,email) VALUES ('%s','%s');",mres($campaign_id),mres(OSDstrtolower($email)));
                    $rslt=mysql_query($stmt, $link);

                    ### LOG CHANGES TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|ADD A NEW EMAIL BLACKLIST|$PHP_AUTH_USER|$ip|$stmt|\n");
                        fclose($fp);
                    }
                }
            }
            $SUB=2;
            $ADD="3eb";
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=4eb modify email blacklist in the system
######################

if ($ADD=="4eb") {
    if ($LOG['modify_campaigns'] == 1) {
        if (OSDstrlen($campaign_id) < 2 or OSDstrlen($email) <= 9 or !OSDpreg_match('/@/',$email)) {
            echo "<br><font color=red>EMAIL BLACKLIST NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>email must be at least 10 characters in length: $email</font><br>\n";
        } else {
            $oldemail = get_variable("oldemail");
            $stmt=sprintf("SELECT count(*) FROM osdial_campaign_email_blacklist WHERE campaign_id='%s' AND email='%s';",mres($campaign_id),mres($email));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0 and $oldemail != $email) {
                echo "<br><b><font color=red>EMAIL BLACKLIST MODIFY FAILED (DUPLICATE): Email $email already exists.</font></b>\n";
            } else {
                echo "<br><b><font color=$default_text>EMAIL MODIFIED: $campaign_id - $email</font></b>\n";

                $stmt=sprintf("UPDATE osdial_campaign_email_blacklist SET email='%s' WHERE campaign_id='%s' AND email='%s';",mres($email),mres($campaign_id),mres($oldemail));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|MODIFY EMAIL BLACKLIST|$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }
        $SUB=2;
        $ADD="3eb";    # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=5eb confirmation before deletion of email blacklist
######################

if ($ADD == "5eb") {
    if ($LOG['modify_campaigns'] == 1) {
        if (OSDstrlen($campaign_id) < 2) {
            echo "<br><font color=red>EMAIL NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>Campaign_id be at least 2 characters in length</font><br<\n";
        } else {
            echo "<br><b><font color=$default_text>EMAIL DELETION CONFIRMATION: $campaign_id - $email</b>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=6eb&campaign_id=$campaign_id&email=ALL&CoNfIrM=YES\">Click here to delete ALL Email Blacklist Entries for $campaign_id</a></font><br><br><br>\n";
        }
        $SUB=2;
        $ADD="3eb";        # go to campaign modification below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=6eb delete email blacklist in the system
######################

if ($ADD=="6eb") {
    if ($LOG['modify_campaigns'] == 1) {
        if (OSDstrlen($campaign_id) < 2 or OSDstrlen($email) < 1) {
            echo "<br><font color=red>CALLERID/AREACODE NOT DELETED - Please go back and look at the data you entered\n";
        } else {
            echo "<br><b><font color=$default_text>EMAIL DELETED: $campaign_id - $email</font></b>\n";

            $stmt='';
            if ($email!='ALL') $stmt=sprintf("AND email='%s'",mres($email));
            $stmt=sprintf("DELETE FROM osdial_campaign_email_clacklist WHERE campaign_id='%s' %s;",mres($campaign_id),$stmt);
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|DELETE EMAIL BLACKLIST|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        echo "</font>\n";
        $SUB=2;
        $ADD="3eb";    # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=3eb display all campaign email blacklist
######################
if ($ADD == "3eb" and $SUB != 2) {
    if ($LOG['modify_campaigns'] == 1) {
        echo "<center>\n";
        echo "  <br><font color=$default_text size=+1>EMAIL BLACKLIST CAMPAIGNS</font><br><br>\n";
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
            echo "    <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3eb&SUB=2&campaign_id=$campaigns_id_list[$o]';\">\n";
            echo "      <td><a href=\"$PHP_SELF?ADD=3eb&SUB=2&campaign_id=$campaigns_id_list[$o]\">" . mclabel($campaigns_id_list[$o]) . "</a></td>\n";
            echo "      <td>$campaigns_name_list[$o]</td>\n";
            echo "      <td align=center><a href=\"$PHP_SELF?ADD=3eb&SUB=2&campaign_id=$campaigns_id_list[$o]\">EMAIL BLACKLIST</a></td>\n";
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

##### CAMPAIGN EMAIL BLACKLIST #####
if ($ADD == "3eb" and $SUB == 2) {
    if ($LOG['modify_campaigns'] == 1) {
        echo "<center>\n";
        echo "  <br><font color=$default_text size=+1>EMAIL BLACKLIST FOR THIS CAMPAIGN</font><br>\n";
        echo "  <table bgcolor=grey width=500 cellspacing=1>\n";
        echo "    <tr class=tabheader>\n";
        echo "      <td align=center>EMAIL</td>\n";
        echo "      <td align=center colspan=2>ACTIONS</td>\n";
        echo "    </tr>\n";

        $stmt=sprintf("SELECT * FROM osdial_campaign_email_blacklist WHERE campaign_id='%s' ORDER BY email;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $emails_to_print = mysql_num_rows($rslt);
        $o=0;
        while ($emails_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $o++;

            echo "    <form action=$PHP_SELF method=POST>\n";
            echo "    <input type=hidden name=ADD value=4eb>\n";
            echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
            echo "    <input type=hidden name=oldemail value=\"$rowx[1]\">\n";
            echo "    <tr " . bgcolor($o) . " class=\"row font1\">\n";
            echo "      <td align=center><input type=text size=40 maxlength=255 name=email value=\"$rowx[1]\"></td>\n";
            echo "      <td align=center><a href=\"$PHP_SELF?ADD=6eb&campaign_id=$campaign_id&email=$rowx[1]\">DELETE</a></td>\n";
            echo "      <td align=center class=tabbutton1><input type=submit name=submit value=MODIFY></td>\n";
            echo "    </tr>\n";
            echo "    </form>\n";
        }

        echo "    <tr " . bgcolor($o) . " class=\"row font1\">\n";
        echo "      <td colspan=3></td>\n";
        echo "    </tr>\n";
        echo "    <form action=$PHP_SELF method=POST>\n";
        echo "    <input type=hidden name=ADD value=2eb>\n";
        echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
        echo "    <tr class=tabfooter>\n";
        echo "      <td align=center><input type=text size=40 maxlength=255 name=email></td>\n";
        echo "      <td align=center class=tabbutton1 colspan=2><input type=submit name=submit value=ADD></td>\n";
        echo "    </tr>\n";
        echo "    </form>\n";
        echo "  </table>\n";

        if (!isset($restab)) {
            echo "  <br>\n";
            echo "  <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\"><br>\n";
            echo "  <input type=hidden name=ADD value=2eb>\n";
            echo "  <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
            echo "  <table bgcolor=grey width=300 cellspacing=1>\n";
            echo "    <tr class=tabfooter>\n";
            echo "      <td align=center colspan=2>UPLOAD A LIST OF EMAILS TO BLACKLIST</td>\n";
            echo "    </tr>\n";
            echo "    <tr class=tabfooter>\n";
            echo "      <td align=center>\n";
            echo "        <input type=file name=\"ebfile\" value=\"\">\n";
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

        echo "<br><br><a href=\"$PHP_SELF?ADD=5eb&campaign_id=$campaign_id\">DELETE ALL EMAILS FOR THIS CAMPAIGN</a>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


?>
