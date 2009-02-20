<?php


function get_variable($varid) {
    $myvar = '';
    if (isset($_GET[$varid])) {
        $myvar=$_GET[$varid];
    } elseif (isset($_POST[$varid])) {
        $myvar=$_POST[$varid];
    }
    return $myvar;
}

##### get key row hashes
function get_krh($link, $tbl, $flds, $srt, $whr) {
    if ($srt != '') {
        $srt = " ORDER BY " . $srt;
    } elseif ($flds == '*') {
        $srt = '';
    } else {
        $sary = split(',', $flds);
        $srt = " ORDER BY " . $sary[0];
    }
    if ($whr != '') {
        $whr = ' WHERE ' . $whr;
    }
    $krhstmt="SELECT " . $flds . " FROM " . $tbl . $whr . $srt;
    $krhrslt=mysql_query($krhstmt, $link);
    while ($krhrow = mysql_fetch_array($krhrslt, MYSQL_BOTH)) {
        $krhrows[$krhrow[0]] = $krhrow;
    }
    return $krhrows;
}

function get_first_record($link, $tbl, $flds, $whr) {
    if ($whr != '') {
        $whr = ' WHERE ' . $whr;
    }
    $gfrstmt="SELECT " . $flds . " FROM " . $tbl . $whr . ' LIMIT 1';
    $gfrrslt=mysql_query($gfrstmt, $link);
    $gfrrow = mysql_fetch_array($gfrrslt, MYSQL_BOTH);
    return $gfrrow;
}

##### format_select - formats return from get_krh into HTML options.
#####   krh  = The return from the get_krh
#####   kkey = The value for the option
#####   kval = The description for the option
#####   ksel = The selected option (optional)
#####   knone= Prefix a "NONE" option. (optional, boolean)
function format_select_options($krh, $kkey, $kval, $ksel, $knone) {
    $option = '';
    if ($knone == true) $option = "  <option value=\"\">NONE</option>\n";
    foreach ($krh as $ele) {
        $selopt = '';
        if ($ele[$kkey] == $ksel) $selopt = " selected";
        $option .= '<option value="' . $ele[$kkey] . '"' . $selopt . '>' . $ele[$kkey] . ' - ' . $ele[$kval] . '</option>';
    }
    return $option;
}

##### get scripts listing for dynamic pulldown
function get_scripts($link, $selected) {
    $krh = get_krh($link, 'vicidial_scripts', 'script_id,script_name');
    return format_select_options($krh, 'script_id', 'script_name', $selected, true);
}


##### get filters listing for dynamic pulldown
function get_filters($link, $selected) {
    $krh = get_krh($link, 'vicidial_lead_filters', 'lead_filter_id,lead_filter_name');
    return format_select_options($krh, 'lead_filter_id', 'lead_filter_name', $selected, true);
}


##### get call_times listing for dynamic pulldown
function get_calltimes($link, $selected) {
    $krh = get_krh($link, 'vicidial_call_times', 'call_time_id,call_time_name');
    return format_select_options($krh, 'call_time_id', 'call_time_name', $selected, true);
}

##### get server listing for dynamic pulldown
function get_servers($link, $selected) {
    $krh = get_krh($link, 'servers', 'server_ip,server_description');
    return format_select_options($krh, 'server_ip', 'server_description', $selected, false);
}




##### CALCULATE DIALABLE LEADS #####
function dialable_leads($DB, $link, $local_call_time, $dial_statuses, $camp_lists, $fSQL) {
    ##### BEGIN calculate what gmt_offset_now values are within the allowed local_call_time setting ###
    if (isset($camp_lists)) {
        if (strlen($camp_lists) > 1) {
            if (strlen($dial_statuses) > 2) {
                $g = 0;
                $p = '13';
                $GMT_gmt[0] = '';
                $GMT_hour[0] = '';
                $GMT_day[0] = '';
                while ($p > - 13) {
                    $pzone = 3600 * $p;
                    $pmin = (gmdate("i", time() + $pzone));
                    $phour = ((gmdate("G", time() + $pzone)) * 100);
                    $pday = gmdate("w", time() + $pzone);
                    $tz = sprintf("%.2f", $p);
                    $GMT_gmt[$g] = "$tz";
                    $GMT_day[$g] = "$pday";
                    $GMT_hour[$g] = ($phour + $pmin);
                    $p = ($p - 0.25);
                    $g++;
                }
                $stmt = "SELECT * FROM vicidial_call_times where call_time_id='$local_call_time';";
                if ($DB) {
                    echo "$stmt\n";
                }
                $rslt = mysql_query($stmt, $link);
                $rowx = mysql_fetch_row($rslt);
                $Gct_default_start = "$rowx[3]";
                $Gct_default_stop = "$rowx[4]";
                $Gct_sunday_start = "$rowx[5]";
                $Gct_sunday_stop = "$rowx[6]";
                $Gct_monday_start = "$rowx[7]";
                $Gct_monday_stop = "$rowx[8]";
                $Gct_tuesday_start = "$rowx[9]";
                $Gct_tuesday_stop = "$rowx[10]";
                $Gct_wednesday_start = "$rowx[11]";
                $Gct_wednesday_stop = "$rowx[12]";
                $Gct_thursday_start = "$rowx[13]";
                $Gct_thursday_stop = "$rowx[14]";
                $Gct_friday_start = "$rowx[15]";
                $Gct_friday_stop = "$rowx[16]";
                $Gct_saturday_start = "$rowx[17]";
                $Gct_saturday_stop = "$rowx[18]";
                $Gct_state_call_times = "$rowx[19]";
                $ct_states = '';
                $ct_state_gmt_SQL = '';
                $ct_srs = 0;
                $b = 0;
                if (strlen($Gct_state_call_times) > 2) {
                    $state_rules = explode('|', $Gct_state_call_times);
                    $ct_srs = ((count($state_rules)) - 2);
                }
                while ($ct_srs >= $b) {
                    if (strlen($state_rules[$b]) > 1) {
                        $stmt = "SELECT * from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
                        $rslt = mysql_query($stmt, $link);
                        $row = mysql_fetch_row($rslt);
                        $Gstate_call_time_id = "$row[0]";
                        $Gstate_call_time_state = "$row[1]";
                        $Gsct_default_start = "$row[4]";
                        $Gsct_default_stop = "$row[5]";
                        $Gsct_sunday_start = "$row[6]";
                        $Gsct_sunday_stop = "$row[7]";
                        $Gsct_monday_start = "$row[8]";
                        $Gsct_monday_stop = "$row[9]";
                        $Gsct_tuesday_start = "$row[10]";
                        $Gsct_tuesday_stop = "$row[11]";
                        $Gsct_wednesday_start = "$row[12]";
                        $Gsct_wednesday_stop = "$row[13]";
                        $Gsct_thursday_start = "$row[14]";
                        $Gsct_thursday_stop = "$row[15]";
                        $Gsct_friday_start = "$row[16]";
                        $Gsct_friday_stop = "$row[17]";
                        $Gsct_saturday_start = "$row[18]";
                        $Gsct_saturday_stop = "$row[19]";
                        $ct_states.= "'$Gstate_call_time_state',";
                        $r = 0;
                        $state_gmt = '';
                        while ($r < $g) {
                            #### Sunday local time
                            if ($GMT_day[$r] == 0) {
                                if (($Gsct_sunday_start == 0) and ($Gsct_sunday_stop == 0)) {
                                    if (($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                } else {
                                    if (($GMT_hour[$r] >= $Gsct_sunday_start) and ($GMT_hour[$r] < $Gsct_sunday_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                }
                            }
                            #### Monday local time
                            if ($GMT_day[$r] == 1) {
                                if (($Gsct_monday_start == 0) and ($Gsct_monday_stop == 0)) {
                                    if (($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                } else {
                                    if (($GMT_hour[$r] >= $Gsct_monday_start) and ($GMT_hour[$r] < $Gsct_monday_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                }
                            }
                            #### Tuesday local time
                            if ($GMT_day[$r] == 2) {
                                if (($Gsct_tuesday_start == 0) and ($Gsct_tuesday_stop == 0)) {
                                    if (($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                } else {
                                    if (($GMT_hour[$r] >= $Gsct_tuesday_start) and ($GMT_hour[$r] < $Gsct_tuesday_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                }
                            }
                            #### Wednesday local time
                            if ($GMT_day[$r] == 3) {
                                if (($Gsct_wednesday_start == 0) and ($Gsct_wednesday_stop == 0)) {
                                    if (($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                } else {
                                    if (($GMT_hour[$r] >= $Gsct_wednesday_start) and ($GMT_hour[$r] < $Gsct_wednesday_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                }
                            }
                            #### Thursday local time
                            if ($GMT_day[$r] == 4) {
                                if (($Gsct_thursday_start == 0) and ($Gsct_thursday_stop == 0)) {
                                    if (($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                } else {
                                    if (($GMT_hour[$r] >= $Gsct_thursday_start) and ($GMT_hour[$r] < $Gsct_thursday_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                }
                            }
                            #### Friday local time
                            if ($GMT_day[$r] == 5) {
                                if (($Gsct_friday_start == 0) and ($Gsct_friday_stop == 0)) {
                                    if (($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                } else {
                                    if (($GMT_hour[$r] >= $Gsct_friday_start) and ($GMT_hour[$r] < $Gsct_friday_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                }
                            }
                            #### Saturday local time
                            if ($GMT_day[$r] == 6) {
                                if (($Gsct_saturday_start == 0) and ($Gsct_saturday_stop == 0)) {
                                    if (($GMT_hour[$r] >= $Gsct_default_start) and ($GMT_hour[$r] < $Gsct_default_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                } else {
                                    if (($GMT_hour[$r] >= $Gsct_saturday_start) and ($GMT_hour[$r] < $Gsct_saturday_stop)) {
                                        $state_gmt.= "'$GMT_gmt[$r]',";
                                    }
                                }
                            }
                            $r++;
                        }
                        $state_gmt = "$state_gmt'99'";
                        $ct_state_gmt_SQL.= "or (state='$Gstate_call_time_state' and gmt_offset_now IN($state_gmt)) ";
                    }
                    $b++;
                }
                if (strlen($ct_states) > 2) {
                    $ct_states = eregi_replace(",$", '', $ct_states);
                    $ct_statesSQL = "and state NOT IN($ct_states)";
                } else {
                    $ct_statesSQL = "";
                }
                $r = 0;
                $default_gmt = '';
                while ($r < $g) {
                    #### Sunday local time
                    if ($GMT_day[$r] == 0) {
                        if (($Gct_sunday_start == 0) and ($Gct_sunday_stop == 0)) {
                            if (($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        } else {
                            if (($GMT_hour[$r] >= $Gct_sunday_start) and ($GMT_hour[$r] < $Gct_sunday_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        }
                    }
                    #### Monday local time
                    if ($GMT_day[$r] == 1) {
                        if (($Gct_monday_start == 0) and ($Gct_monday_stop == 0)) {
                            if (($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        } else {
                            if (($GMT_hour[$r] >= $Gct_monday_start) and ($GMT_hour[$r] < $Gct_monday_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        }
                    }
                    #### Tuesday local time
                    if ($GMT_day[$r] == 2) {
                        if (($Gct_tuesday_start == 0) and ($Gct_tuesday_stop == 0)) {
                            if (($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        } else {
                            if (($GMT_hour[$r] >= $Gct_tuesday_start) and ($GMT_hour[$r] < $Gct_tuesday_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        }
                    }
                    #### Wednesday local time
                    if ($GMT_day[$r] == 3) {
                        if (($Gct_wednesday_start == 0) and ($Gct_wednesday_stop == 0)) {
                            if (($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        } else {
                            if (($GMT_hour[$r] >= $Gct_wednesday_start) and ($GMT_hour[$r] < $Gct_wednesday_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        }
                    }
                    #### Thursday local time
                    if ($GMT_day[$r] == 4) {
                        if (($Gct_thursday_start == 0) and ($Gct_thursday_stop == 0)) {
                            if (($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        } else {
                            if (($GMT_hour[$r] >= $Gct_thursday_start) and ($GMT_hour[$r] < $Gct_thursday_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        }
                    }
                    #### Friday local time
                    if ($GMT_day[$r] == 5) {
                        if (($Gct_friday_start == 0) and ($Gct_friday_stop == 0)) {
                            if (($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        } else {
                            if (($GMT_hour[$r] >= $Gct_friday_start) and ($GMT_hour[$r] < $Gct_friday_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        }
                    }
                    #### Saturday local time
                    if ($GMT_day[$r] == 6) {
                        if (($Gct_saturday_start == 0) and ($Gct_saturday_stop == 0)) {
                            if (($GMT_hour[$r] >= $Gct_default_start) and ($GMT_hour[$r] < $Gct_default_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        } else {
                            if (($GMT_hour[$r] >= $Gct_saturday_start) and ($GMT_hour[$r] < $Gct_saturday_stop)) {
                                $default_gmt.= "'$GMT_gmt[$r]',";
                            }
                        }
                    }
                    $r++;
                }
                $default_gmt = "$default_gmt'99'";
                $all_gmtSQL = "(gmt_offset_now IN($default_gmt) $ct_statesSQL) $ct_state_gmt_SQL";
                $dial_statuses = preg_replace("/ -$/", "", $dial_statuses);
                $Dstatuses = explode(" ", $dial_statuses);
                $Ds_to_print = (count($Dstatuses) - 0);
                $Dsql = '';
                $o = 0;
                while ($Ds_to_print > $o) {
                    $o++;
                    $Dsql.= "'$Dstatuses[$o]',";
                }
                $Dsql = preg_replace("/,$/", "", $Dsql);
                $stmt = "SELECT count(*) FROM vicidial_list where called_since_last_reset='N' and status IN($Dsql) and list_id IN($camp_lists) and ($all_gmtSQL) $fSQL";
                if ($DB) {
                    echo "$stmt\n";
                }
                $rslt = mysql_query($stmt, $link);
                $rslt_rows = mysql_num_rows($rslt);
                if ($rslt_rows) {
                    $rowx = mysql_fetch_row($rslt);
                    $active_leads = "$rowx[0]";
                } else {
                    $active_leads = '0';
                }
                echo "|$DB|\n";
                echo "<font color=navy> This campaign has $active_leads leads to be dialed in those lists</font>\n";
            } else {
                echo "<font color=navy> No dial statuses selected for this campaign</font>\n";
            }
        } else {
            echo "<font color=navy> No active lists selected for this campaign</font>\n";
        }
    } else {
        echo "<font color=navy> No active lists selected for this campaign</font>\n";
    }
}
?>
