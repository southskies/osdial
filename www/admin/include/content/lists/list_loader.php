<?php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

if ($ADD==122) {
  if ($LOGload_leads==1 and $LOGuser_level > 7) {
    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
	
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	
	echo "<center><br><font color=$default_text size=+1>LOAD NEW LEADS</font><br><hr><br>\n";
	
	$leadfile=$_FILES["leadfile"];
	$LF_orig = $_FILES['leadfile']['name'];
	$LF_path = $_FILES['leadfile']['tmp_name'];
	if (isset($_FILES["leadfile"])) $leadfile_name=$_FILES["leadfile"]['name'];

    $single_insert = 1;
    $dot_count=0;

	$list_id_override = (preg_replace("/\D/","",$list_id_override));
	$phone_code_override = (preg_replace("/\D/","",$phone_code_override));
    $file_layout = get_variable('file_layout');
	
	$aff_fields = get_variable('aff_fields');
	$aff_field = Array();
	$affcnt = 0;

    if (strlen($aff_fields) > 0) $single_insert = 1;

	### Grab Server GMT value from the database
	$isdst = date("I");
	$Shour = date("H");
	$Smin = date("i");
	$Ssec = date("s");
	$Smon = date("m");
	$Smday = date("d");
	$Syear = date("Y");
    $server = get_first_record($link, 'servers', '*', "");
    if ($server['local_gmt'] != "") {
        $DBgmt = $server['local_gmt'];
        if (strlen($DBgmt)>0) $server_gmt = $DBgmt;
        if ($isdst) $server_gmt++;
	} else {
        $server_gmt = date("O");
        $server_gmt = eregi_replace("\+","",$server_gmt);
        $server_gmt = (($server_gmt + 0) / 100);
	}
	$local_gmt = $server_gmt;
	
    echo "  <form action=$PHP_SELF method=post onSubmit=\"ParseFileName()\" enctype=\"multipart/form-data\">\n";
    echo "      <input type=hidden name='ADD' value='$ADD'>\n";
    echo "      <input type=hidden name='leadfile_name' value=\"$leadfile_name\">\n";
	if (!$OK_to_process and ($file_layout != "custom" or $leadfile_name == "")) {
        if ($phone_code_override == "") $phone_code_override = "1";
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
        echo "                          <input type=button onClick=\"javascript:document.location='admin.php?ADD=$ADD'\" value=\"START OVER\" name='reload_page'>\n";
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
            ob_flush();
            flush();

			$dupcheckCLI=''; $postalgmtCLI='';
			if (eregi("DUPLIST",$dupcheck)) {$dupcheckCLI='--duplicate-check';}
			if (eregi("DUPCAMP",$dupcheck)) {$dupcheckCLI='--duplicate-campaign-check';}
			if (eregi("DUPSYS",$dupcheck)) {$dupcheckCLI='--duplicate-system-check';}
			if (eregi("POSTAL",$postalgmt)) {$postalgmtCLI='--postal-code-gmt';}
			passthru("$WeBServeRRooT/admin/listloader_super.pl $vendor_lead_code_field,$source_id_field,$list_id_field,$phone_code_field,$phone_number_field,$title_field,$first_name_field,$middle_initial_field,$last_name_field,$address1_field,$address2_field,$address3_field,$city_field,$state_field,$province_field,$postal_code_field,$country_code_field,$gender_field,$date_of_birth_field,$alt_phone_field,$email_field,$custom1_field,$comments_field, --forcelistid=$list_id_override --forcephonecode=$phone_code_override --lead-file=$lead_file $postalgmtCLI $dupcheckCLI");

        # Process a CSV/PSV/TSV
        # This is where the Customized (Field Mapped) Lead Loading begins.
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
            ob_flush();
            flush();

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
					$gmtl = lookup_gmt($phone_code,$USarea,$state,$local_gmt,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
					$gmt_offset = $gmtl[0];
                    $postal = $gmtl[1];
                    if ($postal > 0) $post++;
		
					if ($single_insert > 0) {
						$stmtZ = sprintf("INSERT INTO osdial_list values ('','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',0,'%s','%s','2008-01-01 00:00:00','%s','0000-00-00 00:00:00');",mres($entry_date),mres($modify_date),mres($status),mres($user),mres($vendor_lead_code),mres($source_id),mres($list_id),mres($gmt_offset),mres($called_since_last_reset),mres($phone_code),mres($phone_number),mres($title),mres($first_name),mres($middle_initial),mres($last_name),mres($address1),mres($address2),mres($address3),mres($city),mres($state),mres($province),mres($postal_code),mres($country_code),mres($gender),mres($date_of_birth),mres($alt_phone),mres($email),mres($custom1),mres($comments),mres($custom2),mres($external_key),mres($cost));
						$rslt=mysql_query($stmtZ, $link);
                        $lead_id = mysql_insert_id($link);

                        # Process any AFF Fields
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
			if ($single_insert < 1 and $multi_insert_counter != 0) {
				$stmtZ = "INSERT INTO osdial_list values".substr($multistmt, 0, -1).";";
				mysql_query($stmtZ, $link);
				if ($WeBRooTWritablE > 0) fwrite($stmt_file, $stmtZ."\n");
			}

			echo "<script language='javascript'>\nShowProgress($good, $bad, $total, $dup, $post, $affcnt);\n</script>\n";
            $dwin = 'load_status';
            if (($dup + $bad) == 0) $dwin = 'load_win';
            $lmenu = '';
            if ($list_id_override > 0) $lmenu = "<br><br><span style=text-align:center;font-size:14px;><a href=$PHP_SELF?ADD=311&list_id=$list_id_override>[ Back to List ]</a></span>";
            echo "<script language='javascript'>\ndocument.getElementById('$dwin').innerHTML = '<span style=text-align:center;font-size:48px;><b>DONE<b></span>$lmenu';\n</script>\n";
		}
		echo "<script language='javascript'>\ndocument.forms[0].leadfile.disabled=false;\ndocument.forms[0].submit_file.disabled=false;\ndocument.forms[0].reload_page.disabled=false;\n</script>\n";
        exit;
        # This is where the Customized (Field Mapped) Lead Loading ends.

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
					    $gmtl = lookup_gmt($phone_code,$USarea,$state,$local_gmt,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
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
                $lmenu = '';
                if ($list_id_override > 0) $lmenu = "<br><br><span style=text-align:center;font-size:14px;><a href=$PHP_SELF?ADD=311&list_id=$list_id_override>[ Back to List ]</a></span>";
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
			    echo "  <input type=hidden name=ADD value=$ADD>\n";
			    echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton><input type=button onClick=\"javascript:document.location='admin.php?ADD=$ADD'\" value=\"START OVER\" name='reload_page'></td>\n";
			    echo "    <td align=center class=tabbutton><input type=submit name='OK_to_process' value='OK TO PROCESS'></td>\n";
			    echo "  </tr>\n";
			    echo "</table>\n";
            } else {
			    echo "  <tr class=tabfooter>\n";
                echo "    <td align=center class=tabbutton colspan=2><input type=button onClick=\"javascript:document.location='admin.php?ADD=$ADD'\" value=\"START OVER\" name='reload_page'></td>\n";
			    echo "  </tr>\n";
			    echo "</table>\n";
                echo "<br><br><center><b>The uploaded file format is not supported, CSV format is often the best choice when preparing and loading lists.</b></center>\n";
            }
		}
		echo "<script language='javascript'>\ndocument.forms[0].leadfile.disabled=false;\ndocument.forms[0].submit_file.disabled=false;\ndocument.forms[0].reload_page.disabled=false;\n</script>\n";
	}
				
	echo "</form>\n";
  } else {
    echo "<center><font color=red>You do not have permissions to load leads.</font></center>\n";
  }
}


?>
