<?php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
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
# 090504-0237 - Moved in getloadavg.



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
function get_krh($link, $tbl, $flds="*", $srt="", $whr="", $lmt="") {
    global $DB;
    if ($srt != '') {
        $srt = " ORDER BY " . $srt;
    } elseif ($flds == '*') {
        $srt = "";
    } else {
        $sary = explode(',', $flds);
        $srt = " ORDER BY " . $sary[0];
    }
    if ($whr != '') {
        $whr = ' WHERE ' . $whr;
    }
    if ($lmt != '') {
        $lmt = ' LIMIT ' . $lmt;
    }
    $krhstmt="SELECT " . $flds . " FROM " . $tbl . $whr . $srt . $lmt;
    $krhrslt=mysql_query($krhstmt, $link);
    if ($DB) echo "\n<!--$krhstmt-->\n";
    while ($krhrow = mysql_fetch_assoc($krhrslt)) {
        $krhrows[] = $krhrow;
    }
    if (isset($krhrows)) return $krhrows;
}

function get_first_record($link, $tbl, $flds="*", $whr="") {
    if ($whr != '') {
        $whr = ' WHERE ' . $whr;
    }
    $gfrstmt="SELECT " . $flds . " FROM " . $tbl . $whr . ' LIMIT 1';
    $gfrrslt=mysql_query($gfrstmt, $link);
    $gfrrow = mysql_fetch_assoc($gfrrslt);
    return $gfrrow;
}

##### format_select - formats return from get_krh into HTML options.
#####   krh  = The return from the get_krh
#####   kkey = The value for the option
#####   kval = The description for the option
#####   ksel = The selected option (optional)
#####   kdef= Prefix a default label ie, "NONE" or "ALL" option. (optional, boolean)
function format_select_options($krh, $kkey, $kval, $ksel="!", $kdef="", $kcomp=false) {
    global $LOG;
    $option = '';
    $selopt = '';
    if (is_array($ksel)) {
        foreach ($ksel as $aksel) {
            if ($aksel == "") $selopt = " selected";
        }
    } else {
        if ($ksel == "") $selopt = " selected";
    }
    if ($kdef != "") {
        $option = "  <option value=\"\"" . $selopt . ">";
        $option .= $kdef;
        $option .= "</option>\n";
    }
    if (!is_array($krh)) return $option;
    $klen='';
    if ($kkey != $kval) {
        foreach ($krh as $ele) {
            $klent = OSDstrlen($ele[$kkey]);
            if ($kcomp) $klent = OSDstrlen(mclabel($ele[$kkey]));
            if ($klent > $klen) $klen=$klent;
        }
    }
    foreach ($krh as $ele) {
        $selopt = '';
        if (is_array($ksel)) {
            foreach ($ksel as $aksel) {
                if ($aksel == $ele[$kkey]) $selopt = " selected";
            }
        } else {
            if ($ksel == $ele[$kkey]) $selopt = " selected";
        }
        $optstyle = '';
        $optlabel = sprintf('%-'.$klen.'s',$ele[$kkey]);
        if ($kcomp) $optlabel = sprintf('%-'.$klen.'s',mclabel($ele[$kkey]));
        if ($kkey != $kval) {
                $optlabel .= '- ' . $ele[$kval];
                $optstyle = ' style="font-family:monospace;';
                if (isset($ele['active']) && $ele['active']=='N') $optstyle .= 'color:#800000;';
                $optstyle .= '"';
        }
        $optlabel = OSDpreg_replace('/ /','&nbsp;',$optlabel);
        $option .= '<option value="' . $ele[$kkey] . '"' . $optstyle . $selopt . '>' . $optlabel . '</option>' . "\n";
    }
    return $option;
}

##### get scripts listing for dynamic pulldown
function get_scripts($link, $selected="") {
    global $LOG;
    $krh = get_krh($link, 'osdial_scripts', 'script_id,script_name,active','',sprintf("script_id LIKE '%s__%%' AND script_id IN %s",$LOG['company_prefix'],$LOG['allowed_scriptsSQL']),'');
    return format_select_options($krh, 'script_id', 'script_name', $selected, "NONE", true);
}

##### get email_templates listing for dynamic pulldown
function get_email_templates($link, $selected="") {
    global $LOG;
    $krh = get_krh($link, 'osdial_email_templates', 'et_id,et_name,active','',sprintf("et_id LIKE '%s__%%' AND et_id IN %s",$LOG['company_prefix'],$LOG['allowed_email_templatesSQL']),'');
    return format_select_options($krh, 'et_id', 'et_name', explode(',',$selected), "", true);
}

##### get filters listing for dynamic pulldown
function get_filters($link, $selected="") {
    global $LOG;
    $krh = get_krh($link, 'osdial_lead_filters', 'lead_filter_id,lead_filter_name','',sprintf("lead_filter_id LIKE '%s__%%'",$LOG['company_prefix']),'');
    return format_select_options($krh, 'lead_filter_id', 'lead_filter_name', $selected, "NONE", true);
}


##### get call_times listing for dynamic pulldown
function get_calltimes($link, $selected="") {
    $krh = get_krh($link, 'osdial_call_times', 'call_time_id,call_time_name','','','');
    return format_select_options($krh, 'call_time_id', 'call_time_name', $selected, "NONE", false);
}

##### get server listing for dynamic pulldown
function get_servers($link, $selected="",$type="") {
    $tsql='';
    if (isset($type) and $type != '') {
        foreach (explode('|',$type) as $t) {
            $tsql.="'$t',";
        }
        $tsql="server_profile IN (".rtrim($tsql,',').")";
    }
    $krh = get_krh($link, 'servers', 'server_ip,server_description,active','',$tsql,'');
    return format_select_options($krh, 'server_ip', 'server_description', $selected, "", false);
}


# Function to truncate a line and add ...
function ellipse($string,$len,$dots=true) {
    if(!$len || $len>OSDstrlen($string))
        return $string;
    if (!$dots) {
        return OSDsubstr($string,0,$len);
    }
    return OSDsubstr($string,0, ($len-3)) . '...';
}




##### CALCULATE DIALABLE LEADS #####
function dialable_leads($DB, $link, $local_call_time, $dial_statuses, $camp_lists, $fSQL) {
    ##### BEGIN calculate what gmt_offset_now values are within the allowed local_call_time setting ###
    if (isset($camp_lists)) {
        if (OSDstrlen($camp_lists) > 1) {
            if (OSDstrlen($dial_statuses) > 2) {
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
                $stmt = sprintf("SELECT * FROM osdial_call_times where call_time_id='%s';",mres($local_call_time));
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
                if (OSDstrlen($Gct_state_call_times) > 2) {
                    $state_rules = explode('|', $Gct_state_call_times);
                    $ct_srs = ((count($state_rules)) - 2);
                }
                while ($ct_srs >= $b) {
                    if (OSDstrlen($state_rules[$b]) > 1) {
                        $stmt = sprintf("SELECT * from osdial_state_call_times where state_call_time_id='%s';",mres($state_rules[$b]));
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
                if (OSDstrlen($ct_states) > 2) {
                    $ct_states = OSDpreg_replace("/,$/", '', $ct_states);
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
                $dial_statuses = OSDpreg_replace("/ -$/", "", $dial_statuses);
                $Dstatuses = explode(" ", $dial_statuses);
                $Ds_to_print = (count($Dstatuses) - 0);
                $Dsql = '';
                $o = 0;
                while ($Ds_to_print > $o) {
                    $o++;
                    $Dsql.= "'$Dstatuses[$o]',";
                }
                $Dsql = OSDpreg_replace("/,$/", "", $Dsql);
                $stmt = "SELECT count(*) FROM osdial_list FORCE INDEX (list_status) where called_since_last_reset='N' and status IN($Dsql) and list_id IN($camp_lists) and ($all_gmtSQL) $fSQL";
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
                //echo "|$DB|\n";
                echo " $DB &nbsp;\n";
                echo "<font color=$default_text> This campaign has $active_leads leads to be dialed in those lists</font>\n";
            } else {
                echo "<font color=$default_text> No dial statuses selected for this campaign</font>\n";
            }
        } else {
            echo "<font color=$default_text> No active lists selected for this campaign</font>\n";
        }
    } else {
        echo "<font color=$default_text> No active lists selected for this campaign</font>\n";
    }
}

function dstcalc($method,$time) {

        $nlet['F'] = 1;
        $nlet['S'] = 2;
        $nlet['T'] = 3;
        $nlet['L'] = 5;

        $dlet['M'] = 1;
        $dlet['S'] = 0;

        $mlet['F'] = 2;
        $mlet['M'] = 3;
        $mlet['A'] = 4;
        $mlet['S'] = 9;
        $mlet['O'] = 10;
        $mlet['N'] = 11;

        list($start,$end) = OSDpreg_split('/\-/',$method);
        $sn = OSDsubstr($start,0,1);
        $sd = OSDsubstr($start,1,1);
        $sm = OSDsubstr($start,2,1);
        $en = OSDsubstr($end,0,1);
        $ed = OSDsubstr($end,1,1);
        $em = OSDsubstr($end,2,1);

        $sy = date('Y',$time);
        $ey = date('Y',$time);
        if ($mlet[$em]<$mlet[$sm]) $ey++;

        $dststart_date = Date_Calc::NWeekdayOfMonth($nlet[$sn],$dlet[$sd],$mlet[$sm],$sy,'%Y-%m-%d');
        if (!isset($dststart_date) or empty($dststart_date)) $dststart_date = Date_Calc::NWeekdayOfMonth($nlet[$sn]-1,$dlet[$sd],$mlet[$sm],$sy,'%Y-%m-%d');
        list ($dsy,$dsm,$dsd) = OSDpreg_split('/\-/',$dststart_date);
        $dststart = mktime(2,0,0,$dsm,$dsd,$dsy);

        $dstend_date = Date_Calc::NWeekdayOfMonth($nlet[$en],$dlet[$ed],$mlet[$em],$ey,'%Y-%m-%d');
        if (!isset($dstend_date) or empty($dstend_date)) $dstend_date = Date_Calc::NWeekdayOfMonth($nlet[$en]-1,$dlet[$ed],$mlet[$em],$ey,'%Y-%m-%d');
        list ($dey,$dem,$ded) = OSDpreg_split('/\-/',$dstend_date);
        $dstend = mktime(2,0,0,$dem,$ded,$dey);

        $dstval=0;
        if ($time >= $dststart && $time <= $dstend) $dstval++;

        return $dstval;
}


function lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code) {
    require('dbconnect.php');

    $postalgmt_found=0;
    if (OSDpreg_match("/POSTAL/",$postalgmt) && OSDstrlen($postal_code)>4) {
        if (OSDpreg_match('/^1$/', $phone_code)) {
            $stmt="SELECT * FROM osdial_postal_codes WHERE country_code='$phone_code' AND postal_code LIKE '$postal_code%';";
            $rslt=mysql_query($stmt, $link);
            $pc_recs = mysql_num_rows($rslt);
            if ($pc_recs>0) {
                $row=mysql_fetch_row($rslt);
                $gmt_offset = $row[2];
                $gmt_offset = OSDpreg_replace("/\+/","",$gmt_offset);
                $dst = $row[3];
                $dst_range = $row[4];
                $PC_processed++;
                $postalgmt_found++;
            }
        }
    }
    if ($postalgmt_found<1) {
        $PC_processed=0;
        $stmt='';
        if ($phone_code=='1' or $phone_code=='52') {
            ### US, Canada, and Mexico
            $stmt="SELECT * FROM osdial_phone_codes WHERE country_code='$phone_code' AND areacode='$USarea';";
        } elseif ($phone_code=='61') {
	        ### Australia
            $stmt="SELECT * FROM osdial_phone_codes WHERE country_code='$phone_code' AND state='$state';";
        } else {
            ### All others
            $stmt="SELECT * FROM osdial_phone_codes WHERE country_code='$phone_code';";
        }
        $rslt=mysql_query($stmt, $link);
        $pc_recs = mysql_num_rows($rslt);
        if ($pc_recs>0) {
            $row=mysql_fetch_row($rslt);
            $gmt_offset = $row[4];
            $gmt_offset = OSDpreg_replace("/\+/","",$gmt_offset);
            $dst = $row[5];
            $dst_range = $row[6];
            $PC_processed++;
        }
    }

    ### Find out if DST to raise the gmt offset ###
    $AC_GMT_diff = ($gmt_offset - $LOCAL_GMT_OFF_STD);
    $AC_localtime = mktime(($Shour + $AC_GMT_diff), $Smin, $Ssec, $Smon, $Smday, $Syear);

    if (OSDpreg_match('/[FSTL][MS][FMASON]\-[FSTL][MS][FMASON]/',$dst_range)) {
        $dstval = dstcalc($dst_range,$AC_localtime);
        if ($dstval) $gmt_offset++;
    }

    $ret = Array();
    $ret[] = $gmt_offset;
    $ret[] = $postalgmt_found;
    return $ret;
}

function getloadavg() {
	if (file_exists($pref . 'Loadavg.txt')) {
		$loadavg = file_get_contents($pref . "Loadavg.txt");
	}
	return $loadavg;
}

function optnum2let($val=0) {
    $ret = "<option value=$val selected>";
    if ($val == 0) {
        $ret .= "N";
    } else {
        $ret .= "Y";
    }
    $ret .= "</option>";
    return $ret;
}

# Returns a Yes/No select element.
function select_yesno($selname, $selval) {
    $Ysel='';
    $Yval='';
    $Nsel='';
    $Nval='';
    if ($selval=="Y") {
        $Ysel=" selected";
        $Yval='Y';
        $Nval='N';
    } elseif ($selval=="N") {
        $Nsel=" selected";
        $Yval='Y';
        $Nval='N';
    } elseif ($selval==1) {
        $Ysel=" selected";
        $Yval='1';
        $Nval='0';
    } else {
        $Nsel=" selected";
        $Yval='1';
        $Nval='0';
    }
    $ret  = "    <select size=1 name=\"" . $selname . "\">\n";
    $ret .= "      <option value=\"" . $Yval . "\"" . $Ysel . ">Y</option>\n";
    $ret .= "      <option value=\"" . $Nval . "\"" . $Nsel . ">N</option>\n";
    $ret .= "    </select>\n";
    return $ret;
}

# Simple shorthand for mysql_real_escape_string.
function mres($val) {
    return mysql_real_escape_string($val);
}


# Determine if current local time of the lead is in our local call time.
function dialable_gmt($DB,$link,$local_call_time,$gmt_offset,$state) {

    $dialable = 0;
    $pzone = (3600 * $gmt_offset) + time();
    $pmin = gmdate("i", $pzone);
    $phour = gmdate("G", $pzone) * 100;
    $gday = gmdate("w", $pzone);
    $ghr = ($phour + $pmin);

    $ct = get_first_record($link, 'osdial_call_times', '*', sprintf("call_time_id='%s'",mres($local_call_time)));

    # gday: 0 = Sunday, 1 = Monday, 2 = Tuesday, 3 = Wednesday, 4 = Thursday, 5 = Friday, 6 = Saturday
    if ($gday == 0) {
        if (($ghr >= $ct['ct_sunday_start'] and $ghr < $ct['ct_sunday_stop'])
          or ($ct['ct_sunday_start'] < 1 and $ct['ct_sunday_stop'] < 1 and $ghr >= $ct['ct_default_start'] and $ghr < $ct['ct_default_stop']))
            $dialable=1;
    } elseif ($gday == 1) {
        if (($ghr >= $ct['ct_monday_start'] and $ghr < $ct['ct_monday_stop'])
          or ($ct['ct_monday_start'] < 1 and $ct['ct_monday_stop'] < 1 and $ghr >= $ct['ct_default_start'] and $ghr < $ct['ct_default_stop']))
            $dialable=1;
    } elseif ($gday == 2) {
        if (($ghr >= $ct['ct_tuesday_start'] and $ghr < $ct['ct_tuesday_stop'])
          or ($ct['ct_tuesday_start'] < 1 and $ct['ct_tuesday_stop'] < 1 and $ghr >= $ct['ct_default_start'] and $ghr < $ct['ct_default_stop']))
            $dialable=1;
    } elseif ($gday == 3) {
        if (($ghr >= $ct['ct_wednesday_start'] and $ghr < $ct['ct_wednesday_stop'])
          or ($ct['ct_wednesday_start'] < 1 and $ct['ct_wednesday_stop'] < 1 and $ghr >= $ct['ct_default_start'] and $ghr < $ct['ct_default_stop']))
            $dialable=1;
    } elseif ($gday == 4) {
        if (($ghr >= $ct['ct_thursday_start'] and $ghr < $ct['ct_thursday_stop'])
          or ($ct['ct_thursday_start'] < 1 and $ct['ct_thursday_stop'] < 1 and $ghr >= $ct['ct_default_start'] and $ghr < $ct['ct_default_stop']))
            $dialable=1;
    } elseif ($gday == 5) {
        if (($ghr >= $ct['ct_friday_start'] and $ghr < $ct['ct_friday_stop'])
          or ($ct['ct_friday_start'] < 1 and $ct['ct_friday_stop'] < 1 and $ghr >= $ct['ct_default_start'] and $ghr < $ct['ct_default_stop']))
            $dialable=1;
    } elseif ($gday == 6) {
        if (($ghr >= $ct['ct_saturday_start'] and $ghr < $ct['ct_saturday_stop'])
          or ($ct['ct_saturday_start'] < 1 and $ct['ct_saturday_stop'] < 1 and $ghr >= $ct['ct_default_start'] and $ghr < $ct['ct_default_stop']))
            $dialable=1;
    }

    # If state is blank, assume that we don't want to check by state...
    $sdialable = 1;
    if ($state != "" and OSDstrlen($ct['ct_state_call_times'])>2) {
        foreach (explode('|',$ct['ct_state_call_times']) as $sr) {
            if (OSDstrlen($sr) > 1) {
                $sct = get_first_record($link, 'osdial_state_call_times', '*', sprintf("state_call_time_id='%s' AND state_call_time_state='%s'", mres($sr), mres(OSDstrtoupper($state)) ));
                if (OSDstrtoupper($sct['state_call_time_state']) == OSDstrtoupper($state)) {
                    $sdialable = 0;
                    # gday: 0 = Sunday, 1 = Monday, 2 = Tuesday, 3 = Wednesday, 4 = Thursday, 5 = Friday, 6 = Saturday
                    if ($gday == 0) {
                        if (($ghr >= $sct['sct_sunday_start'] and $ghr < $sct['sct_sunday_stop'])
                          or ($sct['sct_sunday_start'] < 1 and $sct['sct_sunday_stop'] < 1 and $ghr >= $sct['sct_default_start'] and $ghr < $sct['sct_default_stop']))
                            $sdialable=1;
                    } elseif ($gday == 1) {
                        if (($ghr >= $sct['sct_monday_start'] and $ghr < $sct['sct_monday_stop'])
                          or ($sct['sct_monday_start'] < 1 and $sct['sct_monday_stop'] < 1 and $ghr >= $sct['sct_default_start'] and $ghr < $sct['sct_default_stop']))
                            $sdialable=1;
                    } elseif ($gday == 2) {
                        if (($ghr >= $sct['sct_tuesday_start'] and $ghr < $sct['sct_tuesday_stop'])
                          or ($sct['sct_tuesday_start'] < 1 and $sct['sct_tuesday_stop'] < 1 and $ghr >= $sct['sct_default_start'] and $ghr < $sct['sct_default_stop']))
                            $sdialable=1;
                    } elseif ($gday == 3) {
                        if (($ghr >= $sct['sct_wednesday_start'] and $ghr < $sct['sct_wednesday_stop'])
                          or ($sct['sct_wednesday_start'] < 1 and $sct['sct_wednesday_stop'] < 1 and $ghr >= $sct['sct_default_start'] and $ghr < $sct['sct_default_stop']))
                            $sdialable=1;
                    } elseif ($gday == 4) {
                        if (($ghr >= $sct['sct_thursday_start'] and $ghr < $sct['sct_thursday_stop'])
                          or ($sct['sct_thursday_start'] < 1 and $sct['sct_thursday_stop'] < 1 and $ghr >= $sct['sct_default_start'] and $ghr < $sct['sct_default_stop']))
                            $sdialable=1;
                    } elseif ($gday == 5) {
                        if (($ghr >= $sct['sct_friday_start'] and $ghr < $sct['sct_friday_stop'])
                          or ($sct['sct_friday_start'] < 1 and $sct['sct_friday_stop'] < 1 and $ghr >= $sct['sct_default_start'] and $ghr < $sct['sct_default_stop']))
                            $sdialable=1;
                    } elseif ($gday == 6) {
                        if (($ghr >= $sct['sct_saturday_start'] and $ghr < $sct['sct_saturday_stop'])
                          or ($sct['sct_saturday_start'] < 1 and $sct['sct_saturday_stop'] < 1 and $ghr >= $sct['sct_default_start'] and $ghr < $sct['sct_default_stop']))
                            $sdialable=1;
                    }
                }
            }
        }
    }
    if ($sdialable < 1) $dialable = 0;


    return $dialable;
}



# Function to format a return pretty-fied XML.
function prettyXML($pretty,$indent=1) {
    $xa = explode("\n", OSDpreg_replace("/>\s*</",">\n<",$pretty) );
    $pretty = array_shift($xa) . "\n";
    if ($indent < 1) $indent = 1;
    $cindent = 0;
    foreach($xa as $e) {
        if ( OSDpreg_match('/^<\/.+>$/',$e)) $cindent -= $indent;
        if ($e) $pretty .=  str_repeat(' ', $cindent) . $e . "\n";
        if (OSDpreg_match('/^<([\w])+[^>\/]*>$/U',$e)) $cindent += $indent;
    }
    return $pretty;
}


# Quick function for bg row color.
function bgcolor($cnt) {
    global $oddrows;
    global $evenrows;
    $bgc = 'bgcolor="';
    if (OSDpreg_match("/1$|3$|5$|7$|9$/", $cnt)) {
        $bgc .= $oddrows;
    } else {
        $bgc .= $evenrows;
    }
    $bgc .= '"';
    return $bgc;
}


# Returns string with first three chars stripped if multicomp_user
function mclabel($strdat) {
    global $LOG;
    if ($LOG['multicomp_user'] > 0) {
        $strdat = OSDsubstr($strdat,3,OSDstrlen($strdat));
    }
    if ($LOG['multicomp_admin'] > 0 and OSDpreg_match($LOG['companiesRE'],$strdat)) { 
        $strdat = OSDsubstr($strdat,0,3) . ':' . OSDsubstr($strdat,3,OSDstrlen($strdat));
    }
    return $strdat;
}


# Function to parse given seconds into hhh:mm:ss format.
function fmt_hms($seconds) {
    $hrs = intval(($seconds / 3600));
    $mins = intval(($seconds - ($hrs * 3600)) / 60);
    $secs = intval(($seconds - (($hrs * 3600) + ($mins * 60))));
    return sprintf('%d:%02d:%02d',$hrs,$mins,$secs);
}


# Function to parse given seconds into mmm:ss format.
function fmt_ms($seconds) {
    $mins = intval($seconds / 60);
    $secs = intval(($seconds - ($mins * 60)));
    return sprintf('%d:%02d',$mins,$secs);
}


# Function to send emails
function send_email($host, $port, $user, $pass, $to, $from, $subject, $html, $text) {
    include('Mail.php');
    include('Mail/mime.php');
    global $config;

    if ($port=='') $port='25';
    $params["host"] = $host . ':' . $port;
    if ($user) {
        $params["auth"] = true;
        $params["username"] = $user;
        $params["password"] = $pass;
    }

    $headers["To"] = $to;
    $headers["From"] = $from;
    $headers["Subject"] = $subject;

    $mparams = array();
    $mparams["eol"] = "\n";
    if ($config['settings']['use_non_latin']==1) {
        $mparams["text_charset"] = "utf-8";
        $mparams["html_charset"] = "utf-8";
    }
    $mime = new Mail_mime($mparams);

    if ($html) $mime->setHTMLBody($html);
    if ($text) $mime->setTXTBody($text);
    if ($config['settings']['use_non_latin']==1) {
        foreach ($headers as $name => $value){
            $headers[$name] = $mime->encodeHeader($name, $value, "utf-8", "quoted-printable");
        }
    }

    $message = $mime->get();
    $headers = $mime->headers($headers);

    $mail =& Mail::factory('smtp', $params);
    $mail->send($to, $headers, $message);
}


# Quick and dirty file extension to mimetype conversion.
function mimemap($file) {
    $mimetype = 'application/octet-stream';
    if (isset($file) and $file!='') {
        $ext = OSDstrtolower(OSDpreg_replace('/.*\/|.*\./','',$file));
        if ($ext=='g722')    $mimetype = 'audio/G722';
        if ($ext=='g729')    $mimetype = 'audio/G729';
        if ($ext=='gsm')     $mimetype = 'audio/GSM';
        if ($ext=='ogg')     $mimetype = 'audio/ogg';
        if ($ext=='ulaw')    $mimetype = 'audio/PCMU';
        if ($ext=='alaw')    $mimetype = 'audio/PCMA';
        if ($ext=='siren7')  $mimetype = 'audio/siren7';
        if ($ext=='siren14') $mimetype = 'audio/siren14';
        if ($ext=='sln')     $mimetype = 'audio/sln';
        if ($ext=='sln16')   $mimetype = 'audio/sln-16';
        if ($ext=='mp3')     $mimetype = 'audio/mpeg';
        if ($ext=='wav')     $mimetype = 'audio/x-wav';
    }
    return $mimetype;
}



function media_add_files($link, $directory, $pattern, $update_data) {
    if (!isset($directory) or $directory=='') $directory = '.';
    if (!isset($pattern) or $pattern=='') $pattern = '.*';
    if (!isset($update_data) or $update_data=='') $update_data = 0;

    $files = array();
    if (file_exists($directory)) {
        $handle = opendir($directory);
        while (false !== ($filename = readdir($handle))) {
            if ($filename!='.' and $filename!='..' and OSDpreg_match('/'.$pattern.'/',$filename) and ! is_dir($filename)) {
                $filepath=$directory.'/'.$filename;
                $mimetype = mimemap($filename);
                $extension = OSDpreg_replace('/.*\/|\..*/','',$filename);
                if (!OSDpreg_match('/^\d+$/',$extension)) $extension = "";
                $files[] = media_add_file($link, $filepath, $mimetype, $filename, $extension, $updata_data);
            }
        }
        closedir($handle);
        return $files;
    }
}



function media_add_file($link, $filepath, $mimetype, $description, $extension, $update_data) {
    $filename=OSDpreg_replace('/.*\//','',$filepath);
    if (!isset($mimetype) or $mimetype=='') $mimetype = mimemap($filename);
    if (!isset($description)) $description = $filename;
    if (isset($extension)) {
        if (empty($extension)) $extension = OSDpreg_replace('/.*\/|\..*/g','',$filename);
        if (!OSDpreg_match('/^\d+$/',$extension)) $extension = "";
    }
    if (!isset($update_data) or $update_data=='') $update_data = 0;

    if (!file_exists($filepath)) return '!'.$filename;

    $mcnt = get_first_record($link, 'osdial_media', 'count(*) AS cnt', sprintf("filename='%s'", mres($filename)));
    if ($mcnt['cnt']==0) {
        $stmt = sprintf("INSERT INTO osdial_media SET filename='%s',mimetype='%s',description='%s',extension='%s';",mres($filename),mres($mimetype),mres($description),mres($extension));
        $rslt = mysql_query($stmt, $link);
    } else {
        $mdcnt = get_first_record($link, 'osdial_media_data', 'count(*) AS cnt', sprintf("filename='%s'", mres($filename)));
        if ($mdcnt['cnt']>0) {
            if ($update_data>0) {
                media_delete_filedata($link, $filename);
            } else {
                return '*'.$filename;
            }
        }
    }

    $rslt = mysql_query("SHOW variables LIKE 'max_allowed_packet';",$link);
    $row = mysql_fetch_row($rslt);
    $max_packet = $row[1];

    $handle = fopen($filepath,'r');
    while (!feof($handle)) {
        $filedata=fread($handle,$max_packet);
        $stmt = sprintf("INSERT INTO osdial_media_data SET filename='%s',filedata='%s';",mres($filename),mres($filedata));
        $rslt = mysql_query($stmt, $link);
    }
    fclose($handle);

    if ($update_data) return '='.$filename;
    return '+'.$filename;
}



function media_delete_filedata($link, $filename) {
    if (isset($filename) and $filename!='') {
        $stmt = sprintf("DELETE from osdial_media_data WHERE filename='%s';",mres($filename));
        $rslt = mysql_query($stmt, $link);
    }
}



function media_get_filedata($link, $filename) {
    if (isset($filename) and $filename!='') {
        $rslt = mysql_query(sprintf("SELECT filedata FROM osdial_media_data WHERE filename='%s';",mres($filename)));
        $filedata="";
        while ($row = mysql_fetch_row($rslt)) {
            $filedata.=$row[0];
        }
        if ($filedata!='') return $filedata;
    }
}



function media_save_file($link, $directory, $filename, $overwrite) {
    if (!isset($directory) or $directory=='') $directory = '.';
    if (!isset($overwrite) or $overwrite=='') $overwrite = 0;
    if (!file_exists($directory)) mkdir($directory, 0777);
    chmod($directory, 0777);

    $filepath = $directory.'/'.$filename;
    if (file_exists($filepath) and $overwrite==0) return '*'.$filename;

    $filedata = media_get_filedata($link, $filename);
    if (!isset($filedata) or $filedata=='') return '!'.$filename;

    $handle = fopen($filepath, "w");
    fwrite($handle, $filedata);
    fclose($handle);
    chmod($filepath, 0666);

    if ($overwrite) return '='.$filename;
    return '+'.$filename;
}


        
function media_save_files($link, $directory, $pattern, $overwrite) {
    if (!isset($directory) or $directory=='') $directory = '.';
    if (!isset($pattern) or $pattern=='') $pattern = '.*';
    if (!isset($overwrite) or $overwrite=='') $overwrite = 0;
    if (!file_exists($directory)) mkdir($directory, 0777);
    chmod($directory, 0777);

    $files = array();
    $mkrh = get_krh($link, 'osdial_media', '*','','','');
    if (is_array($mkrh)) {
        foreach ($mkrh as $om) {
            if (OSDpreg_match('/'.$pattern.'/',$om['filename'])) {
                $files[] = media_save_file($link, $directory, $om['filename'], $overwrite);
                chmod($directory.'/'.$om['filename'], 0666);
            }
        }
    }
    return $files;
}



function media_file_label_list($link) {
    $mlist = array();
    $mkrh = get_krh($link, 'osdial_media', '*','filename ASC','','');
    if (is_array($mkrh)) {
        $mkeys = array();
        foreach ($mkrh as $om) {
            $tdesc = $om['description'];
            if (OSDpreg_match("/$om[filename]/",$tdesc)) $tdesc='';
            $mkeys[OSDpreg_replace('/.*\/|\..*/','',$om['filename'])] = array($tdesc,'MEDIA');
        }
        if (is_array($mkeys)) {
            foreach ($mkeys as $mk => $mv) {
                $mlist[$mk] = $mv;
            }
        }
    }
    return $mlist;
}

function media_file_select_options($link,$msel) {
    $msel = OSDpreg_replace('/.*\/|\..*|---NONE---/','',$msel);
    $mlist = media_file_label_list($link);
    $mopts = "<option value=\"\"> - NONE - </option>\n";
    if (is_array($mlist)) {
        foreach ($mlist as $ml) {
            $optsel = '';
            if ($ml==$msel) $optsel = 'selected';
            $mopts .= "<option value=\"$ml\" $optsel>$ml</option>\n";
        }
    }
    return $mopts;
}

function media_file_text_options($link, $name, $val, $size, $maxsize) {
    if (!isset($maxsize) or $maxsize=='') $maxsize=$size;
    $val = OSDpreg_replace('/.*\/|\..*|---NONE---/','',$val);
    return editableSelectBox(media_file_label_list($link), $name, $val, $size, $maxsize, '');
}

function media_extension_text_options($link, $name, $val, $size, $maxsize) {
    if (!isset($maxsize) or $maxsize=='') $maxsize=$size;
    $val = OSDpreg_replace('/.*\/|\..*|---NONE---/','',$val);
    return editableSelectBox(media_extension_label_list($link), $name, $val, $size, $maxsize, '');
}



function media_extension_label_list($link) {
    $mlist = array();
    $mkrh = get_krh($link, 'osdial_media', '*','extension ASC',"extension!=''",'');
    if (is_array($mkrh)) {
        $mkeys = array();
        foreach ($mkrh as $om) {
            $tdesc = $om['description'];
            if (OSDpreg_match("/$om[extension]/",$tdesc)) $tdesc='';
            $mkeys[$om['extension']] = array($tdesc,'MEDIA');
        }
        if (is_array($mkeys)) {
            foreach ($mkeys as $mk => $mv) {
                $mlist[$mk] = $mv;
            }
        }
    }
    return $mlist;
}



function phone_voicemail_list($link) {
    $plist = array();
    $pkrh = get_krh($link, 'phones', '*','voicemail_id ASC',"voicemail_id!=''",'');
    if (is_array($pkrh)) {
        $pkeys = array();
        foreach ($pkrh as $op) {
            $tdesc = $op['extension'];
            if (OSDpreg_match("/^$op[voicemail_id]$/",$tdesc)) $tdesc='';
            $pkeys[$op['voicemail_id']] = array($tdesc,'PHONE');
        }
        if (is_array($pkeys)) {
            foreach ($pkeys as $pk => $pv) {
                $plist[$pk] = $pv;
            }
        }
    }
    return $plist;
}



function phone_extension_list($link) {
    $plist = array();
    $pkrh = get_krh($link, 'phones', '*','dialplan_number ASC',"dialplan_number!=''",'');
    if (is_array($pkrh)) {
        $pkeys = array();
        foreach ($pkrh as $op) {
            $tdesc = $op['extension'];
            if (OSDpreg_match("/^$op[dialplan_number]$/",$tdesc)) $tdesc='';
            $pkeys[$op['dialplan_number']] = array($tdesc,'PHONE');
        }
        if (is_array($pkeys)) {
            foreach ($pkeys as $pk => $pv) {
                $plist[$pk] = $pv;
            }
        }
    }
    return $plist;
}



function phone_voicemail_text_options($link, $name, $val, $size, $maxsize) {
    if (!isset($maxsize) or $maxsize=='') $maxsize=$size;
    return editableSelectBox(phone_voicemail_list($link), $name, $val, $size, $maxsize, '');
}

function phone_extension_text_options($link, $name, $val, $size, $maxsize) {
    if (!isset($maxsize) or $maxsize=='') $maxsize=$size;
    return editableSelectBox(phone_extension_list($link), $name, $val, $size, $maxsize, '');
}


function tts_extension_list($link) {
    $tlist = array();
    $tkrh = get_krh($link, 'osdial_tts', '*','extension ASC',"extension!=''",'');
    if (is_array($tkrh)) {
        $tkeys = array();
        foreach ($tkrh as $ot) {
            $tdesc = $ot['description'];
            $tkeys[$ot['extension']] = array($tdesc,'TTS');
        }
        if (is_array($tkeys)) {
            foreach ($tkeys as $tk => $tv) {
                $tlist[$tk] = $tv;
            }
        }
    }
    return $tlist;
}
function tts_file_list($link) {
    $tlist = array();
    $tkrh = get_krh($link, 'osdial_tts', '*','',"",'');
    if (is_array($tkrh)) {
        $tkeys = array();
        foreach ($tkrh as $ot) {
            $tdesc = $ot['description'];
            $tkeys['TTS:'.$ot['extension']] = array($tdesc,'TTS');
        }
        if (is_array($tkeys)) {
            foreach ($tkeys as $tk => $tv) {
                $tlist[$tk] = $tv;
            }
        }
    }
    return $tlist;
}

function ivr_file_text_options($link, $name, $val, $size, $maxsize) {
    if (!isset($maxsize) or $maxsize=='') $maxsize=$size;
    $val = OSDpreg_replace('/.*\/|\..*|---NONE---/','',$val);
    $ext = media_file_label_list($link);
    foreach (tts_file_list($link) as $k => $v) {
        $ext[$k] = $v;
    }
    return editableSelectBox($ext, $name, $val, $size, $maxsize, '');
}


function list_id_list($link) {
    $llist = array();
    $lkrh = get_krh($link, 'osdial_lists', '*','list_id ASC',"list_id>='20' AND active='Y'",'');
    if (is_array($lkrh)) {
        $lkeys = array();
        foreach ($lkrh as $ol) {
            $lkeys[$ol['list_id']] = array($ol['list_name'],'LIST');
        }
        if (is_array($lkeys)) {
            foreach ($lkeys as $lk => $lv) {
                $llist[$lk] = $lv;
            }
        }
    }
    return $llist;
}



function list_id_text_options($link, $name, $val, $size, $maxsize) {
    if (!isset($maxsize) or $maxsize=='') $maxsize=$size;
    return editableSelectBox(list_id_list($link), $name, $val, $size, $maxsize, '');
}



function extension_text_options($link, $name, $val, $size, $maxsize) {
    if (!isset($maxsize) or $maxsize=='') $maxsize=$size;
    $ext = media_extension_label_list($link);
    foreach (tts_extension_list($link) as $k => $v) {
        $ext[$k] = $v;
    }
    foreach (phone_extension_list($link) as $k => $v) {
        $ext[$k] = $v;
    }
    ksort($ext);
    return editableSelectBox($ext, $name, $val, $size, $maxsize, '');
}



function editableSelectBox($options, $name, $val, $size, $maxsize, $attribs) {
    if (!isset($maxsize) or $maxsize=='') $maxsize=$size;
    if (!isset($attribs)) $attribs='';
    if (is_assoc($options)) {
        $esboptsO =' selectBoxOptions="';
        $esboptsL =' selectBoxLabels="';
        $esboptsT =' selectBoxTypes="';
        foreach ($options as $k => $v) {
            $esboptsO .= OSDpreg_replace('/;/','',$k).';';
            if (is_array($v)) {
                $esboptsL .= OSDpreg_replace('/;/','',$v[0]).';';
                if (isset($v[1])) $esboptsT .= OSDpreg_replace('/;/','',$v[1]).';';
            } else {
                $esboptsL .= OSDpreg_replace('/;/','',$v).';';
            }
        }
        $esboptsO = rtrim($esboptsO,';') . "\"";
        $esboptsL = rtrim($esboptsL,';') . "\"";
        $esboptsT = rtrim($esboptsT,';') . "\"";
    } elseif (is_array($options)) {
        $esboptsO =' selectBoxOptions="';
        foreach ($options as $opt) {
            $esboptsO .= $opt.';';
        }
        $esboptsO = rtrim($esboptsO,';') . "\"";
    }
    $esbopts = "<input type=\"text\" name=\"$name\" size=\"$size\" maxlength=\"$maxsize\" value=\"$val\" $attribs ";
    $esbopts .= $esboptsO . $esboptsL . $esboptsT;
    $esbopts .= ">\n";
    $esbopts .= "<script type=\"text/javascript\">\n";
    $esbopts .= "createEditableSelect(document.forms[0].$name);\n";
    $esbopts .= "</script>\n";
    return $esbopts;
}


function is_assoc($array) {
    foreach (array_keys($array) as $k => $v) {
        if ($k !== $v) return true;
    }
    return false;
}


function dateCalcServerLocalGMTOffset($svrGMT, $locGMT, $locisDST, $tzsecs) {
    global $tzoffsets;
    global $tzoffsetsDST;
    global $tzalt;
    global $tzaltDST;
    global $tzrefid;
    global $tzrefidDST;

    $dcsret = array();

    $svrGMT = $svrGMT * 1;
    $dcsret['svrsname'] = $tzoffsets[$svrGMT];
    $svrGMTname = $tzrefid[$dcsret['svrsname']];
    $svrtz = new Date_TimeZone($svrGMTname);
    $dcsret['svroffset'] = $svrtz->getOffset(new Date($tzsecs)) / 3600000;
    $dcsret['svrdst'] = $svrtz->inDaylightTime(new Date($tzsecs));
        
    $locGMT = $locGMT * 1;
    $dcsret['locsname'] = $tzoffsets[$locGMT];
    $locGMTname = $tzrefid[$dcsret['locsname']];
    if ($locisDST>0) {
        $dcsret['locsname'] = $tzoffsets[$locGMT];
        $locGMTname = $tzrefidDST[$tzalt[$dcsret['locsname']]];
    }
    $loctz = new Date_TimeZone($locGMTname);
    $dcsret['locoffset'] = $loctz->getOffset(new Date($tzsecs)) / 3600000;
    $dcsret['locdst'] = $loctz->inDaylightTime(new Date($tzsecs));

    return $dcsret;
}

function dateToLocal($link, $svrip, $cnvdate, $locGMT, $fmt="", $locisDST, $addlocTZlabel) {
    global $tzalt;
    global $tzaltDST;
    global $config;

    if (empty($cnvdate)) return '';
    $dsecs = strtotime($cnvdate);
    if (empty($fmt)) $fmt=$config['settings']['default_date_format'];
    if ($config['settings']['use_browser_timezone_offset']=='N') return date($fmt, $dsecs);

    if (is_numeric($svrip) and $svrip>-27 and $svrip<27 and $svrip!='first') {
        $svrGMT = $svrip * 1;
    } else {
        $server = get_first_record($link, 'servers', '*', sprintf("server_ip='%s'", mres($svrip)));
        if (!is_array($server)) {
            $server = get_first_record($link, 'servers', '*', "server_profile IN ('AIO','DIALER')");
        }
        if (!is_array($server)) return date($fmt, $dsecs);
        $svrGMT = $server['local_gmt'] * 1;
    }

    $dcsoff = dateCalcServerLocalGMTOffset($svrGMT, $locGMT, $locisDST, $dsecs);

    $dsecs -= $dcsoff['svroffset'] * 60 * 60;
    $dsecs += $dcsoff['locoffset'] * 60 * 60;

    $locTZlabel = '';
    $retdate = date($fmt, $dsecs);
    if (OSDpreg_match('/'.$dcsoff['locsname'].'|'.$tzalt[$dcsoff['locsname']].'/',$retdate)) {
        return $retdate;
    } else {
        if ($addlocTZlabel>0) {
            if ($dcsoff['locdst']) {
                $locTZlabel=' '.$tzalt[$dcsoff['locsname']];
            } else {
                $locTZlabel=' '.$dcsoff['locsname'];
            }
        }
        return $retdate . $locTZlabel;
    }
}


function dateToServer($link, $svrip, $cnvdate, $locGMT, $fmt="", $locisDST, $addsvrTZlabel) {
    global $tzalt;
    global $tzaltDST;
    global $config;

    if (empty($cnvdate)) return '';
    $dsecs = strtotime($cnvdate);
    if (empty($fmt)) $fmt='Y-m-d H:i:s';
    if ($config['settings']['use_browser_timezone_offset']=='N') return date($fmt, $dsecs);

    if (is_numeric($svrip) and $svrip>-27 and $svrip<27 and $svrip!='first') {
        $svrGMT = $svrip * 1;
    } else {
        $server = get_first_record($link, 'servers', '*', sprintf("server_ip='%s'", mres($svrip)));
        if (!is_array($server)) {
            $server = get_first_record($link, 'servers', '*', "server_profile IN ('AIO','DIALER')");
        }
        if (!is_array($server)) return date($fmt, $dsecs);
        $svrGMT = $server['local_gmt'] * 1;
    }
    $dcsoff = dateCalcServerLocalGMTOffset($svrGMT, $locGMT, $locisDST, $dsecs);

    $dsecs -= ($dcsoff['locoffset'] - $dcsoff['svroffset']) * 60 * 60;

    $svrTZlabel = '';
    if ($addsvrTZlabel>0) {
        if ($dcsoff['svrdst']) {
            $svrTZlabel=' '.$tzalt[$dcsoff['svrsname']];
        } else {
            $svrTZlabel=' '.$dcsoff['svrsname'];
        }
    }
    return date($fmt, $dsecs) . $svrTZlabel;
}


function OSDstrwidth($instr) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return mb_strwidth($instr,'UTF-8');
    return strlen($instr);
}
function OSDstrlen($instr) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return mb_strlen($instr,'UTF-8');
    return strlen($instr);
}
function OSDstrtolower($instr) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return mb_strtolower($instr,'UTF-8');
    return strtolower($instr);
}
function OSDstrtoupper($instr) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return mb_strtoupper($instr,'UTF-8');
    return strtoupper($instr);
}
function OSDpreg_replace($inre,$insub,$instr) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return preg_replace($inre.'u',$insub,$instr);
    return preg_replace($inre,$insub,$instr);
}
function OSDpreg_split($inre,$instr) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return preg_split($inre.'u',$instr);
    return preg_split($inre,$instr);
}
function OSDpreg_splitX($inre,$instr,$incnt) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return preg_split($inre.'u',$instr,$incnt);
    return preg_split($inre,$instr,$incnt);
}
function OSDpreg_split4($inre,$instr,$incnt,$inflag) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return preg_split($inre.'u',$instr,$incnt,$inflag);
    return preg_split($inre,$instr,$incnt,$inflag);
}
function OSDpreg_match($inre,$instr) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return preg_match($inre.'u',$instr);
    return preg_match($inre,$instr);
}
function OSDsubstr($instr,$instart,$inlen) {
    global $config;
    if ($config['settings']['use_non_latin']==1) return mb_substr($instr,$instart,$inlen,'UTF-8');
    return substr($instr,$instart,$inlen);
}

function OSDsprintf() {
    $args = func_get_args();
    global $config;
    if ($config['settings']['use_non_latin']==1) return call_user_func_array('mb_sprintf', $args);;
    return call_user_func_array('sprintf', $args);;
}


## Taken from http://php.net/manual/en/function.sprintf.php
function mb_sprintf($format) {
    $argv = func_get_args();
    array_shift($argv) ;
    return mb_vsprintf($format, $argv) ;
}

## Taken from http://php.net/manual/en/function.sprintf.php
/**
 * Works with all encodings in format and arguments.
 * Supported: Sign, padding, alignment, width and precision.
 * Not supported: Argument swapping.
 */
function mb_vsprintf($format, $argv, $encoding=null) {
    if (is_null($encoding)) $encoding = mb_internal_encoding();
    // Use UTF-8 in the format so we can use the u flag in preg_split
    $format = mb_convert_encoding($format, 'UTF-8', $encoding);
    $newformat = ""; // build a new format in UTF-8
    $newargv = array(); // unhandled args in unchanged encoding
    while ($format !== "") {
        // Split the format in two parts: $pre and $post by the first %-directive
        // We get also the matched groups
        list ($pre, $sign, $filler, $align, $size, $precision, $type, $post)
          = preg_split("!\%(\+?)('.|[0 ]|)(-?)([1-9][0-9]*|)(\.[1-9][0-9]*|)([%a-zA-Z])!u", $format, 2, PREG_SPLIT_DELIM_CAPTURE);
        $newformat .= mb_convert_encoding($pre, $encoding, 'UTF-8');
        if ($type == '') {
            // didn't match. do nothing. this is the last iteration.
        } elseif ($type == '%') {
            // an escaped %
            $newformat .= '%%';
        } elseif ($type == 's') {
            $arg = array_shift($argv);
            $arg = mb_convert_encoding($arg, 'UTF-8', $encoding);
            $padding_pre = '';
            $padding_post = '';
            // truncate $arg
            if ($precision !== '') {
                $precision = intval(substr($precision,1));
                if ($precision > 0 && mb_strlen($arg,$encoding) > $precision) $arg = mb_substr($precision,0,$precision,$encoding);
            }
            // define padding
            if ($size > 0) {
                $arglen = mb_strlen($arg, $encoding);
                if ($arglen < $size) {
                    if ($filler==='') $filler = ' ';
                    if ($align == '-') $padding_post = str_repeat($filler, $size - $arglen);
                } else {
                    $padding_pre = str_repeat($filler, $size - $arglen);
                }
            }
            // escape % and pass it forward
            $newformat .= $padding_pre . str_replace('%', '%%', $arg) . $padding_post;
        } else {
            // another type, pass forward
            $newformat .= "%$sign$filler$align$size$precision$type";
            $newargv[] = array_shift($argv);
        }
        $format = strval($post);
    }
    // Convert new format back from UTF-8 to the original encoding
    $newformat = mb_convert_encoding($newformat, $encoding, 'UTF-8');
    return vsprintf($newformat, $newargv);
}
function get_status_category_ucwords($catid) {
    global $config;
    $newcatid='';

    # if IVR or CPA do not adjust.
    if (OSDpreg_match('/^(IVR|CPA)$/i',$catid)) {
        $newcatid=$catid;

    # If NOCONTACT, add special casing.
    } elseif ($catid == "NOCONTACT") {
        $newcatid="NoContact";

    # Everything else can be handled by the mb_convert_case or ucwords functions.
    } else {
        $newcatid=OSDstrtolower($catid);
        if ($config['settings']['use_non_latin']==1) {
            $newcatid=mb_convert_case($newcatid, MB_CASE_TITLE, 'UTF-8');
        } else {
            $newcatid=ucwords($newcatid);
        }
    }

    # Add 's' to end of CONTACT, NOCONTACT, SALE to make plural.
    if (OSDpreg_match('/^(CONTACT|NOCONTACT|SALE)$/i',$newcatid)) $newcatid.='s';

    return $newcatid;
}

function jump_section ($section_level) {
	echo "<span class=jump>Links: </span>";
	echo "<a href=#basic class=jump>Control</a>&nbsp;"; 
	echo "<a href=#status class=jump>Status</a>&nbsp;";  
	echo "<a href=#method class=jump>Method</a>&nbsp;";  
	echo "<a href=#options class=jump>Options</a>&nbsp;";  
	echo "<a href=#list class=jump>Lists</a>&nbsp;";  
	echo "<a href=#carrier class=jump>Carrier</a>&nbsp;";  
	echo "<a href=#record class=jump>Record</a>&nbsp;";  
	echo "<a href=#am class=jump>A-M</a>&nbsp;";  
	echo "<a href=#drop class=jump>Drop</a>&nbsp;";  
	echo "<a href=#transfer class=jump>Transfer</a>&nbsp;";  
	echo "<a href=#webform class=jump>WebForm</a>&nbsp;";  
	echo "<a href=#script class=jump>Script</a>&nbsp;";  
	echo "<a href=#eoc class=jump>End-Of-Call</a>&nbsp;";  
	echo "<a href=#dnc class=jump>DNC</a>&nbsp;";  
	echo "<a href=#groups class=jump>Groups</a>&nbsp;";  
	if ($section_level == '') {
		echo "<a href=#alists class=jumpend>ActiveLists</a>&nbsp;"; 
	} else {
		echo "<a href=#alists class=jumpend2>ActiveLists</a>&nbsp;"; 
	}
}    

/**************************************************************/
// This function parses the help documentation from an XML ref. 
function genhelpdata($indata) {
    $hdata = array();
    foreach ($indata as $docitem) {
        $inattr = array();
        # Get attributes for this section
        foreach ($docitem->attributes() as $key => $val) {
            $inattr[(string)$key] = (string)$val;
        }
        $hdata[$inattr['pathId']] = $inattr;
        $hdata[$inattr['pathId']]['type'] = (string)$docitem->getName();
        # Test if this node had children, if not, try to extract the embedded text.
        if ($docitem->count()<1) {
            if (isset($docitem[0]) && !empty($docitem[0])) $hdata[$inattr['pathId']]['text'] = (string)$docitem[0];
        } else {
            # Dig deep, iterating through all submenus and adding them as children.
            $hdata[$inattr['pathId']]['children'] = genhelpdata($docitem);
        }
    }
    # Return a fully nested document.
    return $hdata;
}



?>
