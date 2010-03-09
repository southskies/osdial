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
    if ($srt != '') {
        $srt = " ORDER BY " . $srt;
    } elseif ($flds == '*') {
        $srt = "";
    } else {
        $sary = split(',', $flds);
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
    while ($krhrow = mysql_fetch_array($krhrslt, MYSQL_BOTH)) {
        $krhrows[$krhrow[0]] = $krhrow;
    }
    return $krhrows;
}

function get_first_record($link, $tbl, $flds="*", $whr="") {
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
    foreach ($krh as $ele) {
        $selopt = '';
        if (is_array($ksel)) {
            foreach ($ksel as $aksel) {
                if ($aksel == $ele[$kkey]) $selopt = " selected";
            }
        } else {
            if ($ksel == $ele[$kkey]) $selopt = " selected";
        }
        $option .= '<option value="' . $ele[$kkey] . '"' . $selopt . '>';
        if ($kcomp) {
            $option .= mclabel($ele[$kkey]);
        } else {
            $option .= $ele[$kkey];
        }
        if ($kkey != $kval) $option .= ' - ' . $ele[$kval];
        $option .= '</option>';
    }
    return $option;
}

##### get scripts listing for dynamic pulldown
function get_scripts($link, $selected="") {
    global $LOG;
    $krh = get_krh($link, 'osdial_scripts', 'script_id,script_name','',sprintf("script_id LIKE '%s___%%'",$LOG['company_prefix']));
    return format_select_options($krh, 'script_id', 'script_name', $selected, "NONE", true);
}


##### get filters listing for dynamic pulldown
function get_filters($link, $selected="") {
    global $LOG;
    $krh = get_krh($link, 'osdial_lead_filters', 'lead_filter_id,lead_filter_name','',sprintf("lead_filter_id LIKE '%s___%%'",$LOG['company_prefix']));
    return format_select_options($krh, 'lead_filter_id', 'lead_filter_name', $selected, "NONE", true);
}


##### get call_times listing for dynamic pulldown
function get_calltimes($link, $selected="") {
    $krh = get_krh($link, 'osdial_call_times', 'call_time_id,call_time_name');
    return format_select_options($krh, 'call_time_id', 'call_time_name', $selected, "NONE", false);
}

##### get server listing for dynamic pulldown
function get_servers($link, $selected="") {
    $krh = get_krh($link, 'servers', 'server_ip,server_description');
    return format_select_options($krh, 'server_ip', 'server_description', $selected, "NONE", false);
}


# Function to truncate a line and add ...
function ellipse($string,$len,$dots=true) {
    if(!$len || $len>strlen($string))
        return $string;
    if (!$dots) {
        return substr($string,0,$len);
    }
    return substr($string,0, ($len-3)) . '...';
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
                $stmt = "SELECT * FROM osdial_call_times where call_time_id='$local_call_time';";
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
                        $stmt = "SELECT * from osdial_state_call_times where state_call_time_id='$state_rules[$b]';";
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
                $stmt = "SELECT count(*) FROM osdial_list where called_since_last_reset='N' and status IN($Dsql) and list_id IN($camp_lists) and ($all_gmtSQL) $fSQL";
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

function lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code)
{
require("dbconnect.php");

$postalgmt_found=0;
if ( (eregi("POSTAL",$postalgmt)) && (strlen($postal_code)>4) )
	{
	if (preg_match('/^1$/', $phone_code))
		{
		$stmt="select * from osdial_postal_codes where country_code='$phone_code' and postal_code LIKE \"$postal_code%\";";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[2];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[3];
			$dst_range =	$row[4];
			$PC_processed++;
			$postalgmt_found++;
			}
		}
	}
if ($postalgmt_found < 1)
	{
	$PC_processed=0;
	### UNITED STATES ###
	if ($phone_code =='1')
		{
		$stmt="select * from osdial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### MEXICO ###
	if ($phone_code =='52')
		{
		$stmt="select * from osdial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### AUSTRALIA ###
	if ($phone_code =='61')
		{
		$stmt="select * from osdial_phone_codes where country_code='$phone_code' and state='$state';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	### ALL OTHER COUNTRY CODES ###
	if (!$PC_processed)
		{
		$PC_processed++;
		$stmt="select * from osdial_phone_codes where country_code='$phone_code';";
		$rslt=mysql_query($stmt, $link);
		$pc_recs = mysql_num_rows($rslt);
		if ($pc_recs > 0)
			{
			$row=mysql_fetch_row($rslt);
			$gmt_offset =	$row[4];	 $gmt_offset = eregi_replace("\+","",$gmt_offset);
			$dst =			$row[5];
			$dst_range =	$row[6];
			$PC_processed++;
			}
		}
	}

### Find out if DST to raise the gmt offset ###
$AC_GMT_diff = ($gmt_offset - $LOCAL_GMT_OFF_STD);
$AC_localtime = mktime(($Shour + $AC_GMT_diff), $Smin, $Ssec, $Smon, $Smday, $Syear);
	$hour = date("H",$AC_localtime);
	$min = date("i",$AC_localtime);
	$sec = date("s",$AC_localtime);
	$mon = date("m",$AC_localtime);
	$mday = date("d",$AC_localtime);
	$wday = date("w",$AC_localtime);
	$year = date("Y",$AC_localtime);
$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

$AC_processed=0;
if ( (!$AC_processed) and ($dst_range == 'SSM-FSN') )
	{
	if ($DBX) {print "     Second Sunday March to First Sunday November\n";}
	#**********************************************************************
	# SSM-FSN
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on Second Sunday March to First Sunday November at 2 am.
	#     INPUTS:
	#       mm              INTEGER       Month.
	#       dd              INTEGER       Day of the month.
	#       ns              INTEGER       Seconds into the day.
	#       dow             INTEGER       Day of week (0=Sunday, to 6=Saturday)
	#     OPTIONAL INPUT:
	#       timezone        INTEGER       hour difference UTC - local standard time
	#                                      (DEFAULT is blank)
	#                                     make calculations based on UTC time, 
	#                                     which means shift at 10:00 UTC in April
	#                                     and 9:00 UTC in October
	#     OUTPUT: 
	#                       INTEGER       1 = DST, 0 = not DST
	#
	# S  M  T  W  T  F  S
	# 1  2  3  4  5  6  7
	# 8  9 10 11 12 13 14
	#15 16 17 18 19 20 21
	#22 23 24 25 26 27 28
	#29 30 31
	# 
	# S  M  T  W  T  F  S
	#    1  2  3  4  5  6
	# 7  8  9 10 11 12 13
	#14 15 16 17 18 19 20
	#21 22 23 24 25 26 27
	#28 29 30 31
	# 
	#**********************************************************************

		$USACAN_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 11) {
		$USACAN_DST=0;   
		} elseif ($mm >= 4 and $mm <= 10) {
		$USACAN_DST=1;   
		} elseif ($mm == 3) {
		if ($dd > 13) {
			$USACAN_DST=1;   
		} elseif ($dd >= ($dow+8)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$USACAN_DST=0;   
			} else {
				$USACAN_DST=1;   
			}
			} else {
			if ($dow == 0 and $ns < 7200) {
				$USACAN_DST=0;   
			} else {
				$USACAN_DST=1;   
			}
			}
		} else {
			$USACAN_DST=0;   
		}
		} elseif ($mm == 11) {
		if ($dd > 7) {
			$USACAN_DST=0;   
		} elseif ($dd < ($dow+1)) {
			$USACAN_DST=1;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (7200+($timezone-1)*3600)) {
				$USACAN_DST=1;   
			} else {
				$USACAN_DST=0;   
			}
			} else { # local time calculations
			if ($ns < 7200) {
				$USACAN_DST=1;   
			} else {
				$USACAN_DST=0;   
			}
			}
		} else {
			$USACAN_DST=0;   
		}
		} # end of month checks
	if ($DBX) {print "     DST: $USACAN_DST\n";}
	if ($USACAN_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'FSA-LSO') )
	{
	if ($DBX) {print "     First Sunday April to Last Sunday October\n";}
	#**********************************************************************
	# FSA-LSO
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Sunday in April and last Sunday in October at 2 am.
	#**********************************************************************
		
		$USA_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 4 || $mm > 10) {
		$USA_DST=0;
		} elseif ($mm >= 5 and $mm <= 9) {
		$USA_DST=1;
		} elseif ($mm == 4) {
		if ($dd > 7) {
			$USA_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$USA_DST=0;
			} else {
				$USA_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 7200) {
				$USA_DST=0;
			} else {
				$USA_DST=1;
			}
			}
		} else {
			$USA_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$USA_DST=1;
		} elseif ($dd < ($dow+25)) {
			$USA_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (7200+($timezone-1)*3600)) {
				$USA_DST=1;
			} else {
				$USA_DST=0;
			}
			} else { # local time calculations
			if ($ns < 7200) {
				$USA_DST=1;
			} else {
				$USA_DST=0;
			}
			}
		} else {
			$USA_DST=0;
		}
		} # end of month checks

	if ($DBX) {print "     DST: $USA_DST\n";}
	if ($USA_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'LSM-LSO') )
	{
	if ($DBX) {print "     Last Sunday March to Last Sunday October\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on last Sunday in March and last Sunday in October at 1 am.
	#**********************************************************************
		
		$GBR_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$GBR_DST=0;
		} elseif ($mm >= 4 and $mm <= 9) {
		$GBR_DST=1;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$GBR_DST=0;
		} elseif ($dd < ($dow+25)) {
			$GBR_DST=0;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$GBR_DST=0;
			} else {
				$GBR_DST=1;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$GBR_DST=0;
			} else {
				$GBR_DST=1;
			}
			}
		} else {
			$GBR_DST=1;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$GBR_DST=1;
		} elseif ($dd < ($dow+25)) {
			$GBR_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$GBR_DST=1;
			} else {
				$GBR_DST=0;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$GBR_DST=1;
			} else {
				$GBR_DST=0;
			}
			}
		} else {
			$GBR_DST=0;
		}
		} # end of month checks
		if ($DBX) {print "     DST: $GBR_DST\n";}
	if ($GBR_DST) {$gmt_offset++;}
	$AC_processed++;
	}
if ( (!$AC_processed) and ($dst_range == 'LSO-LSM') )
	{
	if ($DBX) {print "     Last Sunday October to Last Sunday March\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on last Sunday in October and last Sunday in March at 1 am.
	#**********************************************************************
		
		$AUS_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$AUS_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$AUS_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$AUS_DST=1;
		} elseif ($dd < ($dow+25)) {
			$AUS_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUS_DST=1;
			} else {
				$AUS_DST=0;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$AUS_DST=1;
			} else {
				$AUS_DST=0;
			}
			}
		} else {
			$AUS_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd < 25) {
			$AUS_DST=0;
		} elseif ($dd < ($dow+25)) {
			$AUS_DST=0;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUS_DST=0;
			} else {
				$AUS_DST=1;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$AUS_DST=0;
			} else {
				$AUS_DST=1;
			}
			}
		} else {
			$AUS_DST=1;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $AUS_DST\n";}
	if ($AUS_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'FSO-LSM') )
	{
	if ($DBX) {print "     First Sunday October to Last Sunday March\n";}
	#**********************************************************************
	#   TASMANIA ONLY
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Sunday in October and last Sunday in March at 1 am.
	#**********************************************************************
		
		$AUST_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$AUST_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$AUST_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 25) {
			$AUST_DST=1;
		} elseif ($dd < ($dow+25)) {
			$AUST_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$AUST_DST=1;
			} else {
				$AUST_DST=0;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$AUST_DST=1;
			} else {
				$AUST_DST=0;
			}
			}
		} else {
			$AUST_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd > 7) {
			$AUST_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$AUST_DST=0;
			} else {
				$AUST_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 3600) {
				$AUST_DST=0;
			} else {
				$AUST_DST=1;
			}
			}
		} else {
			$AUST_DST=0;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $AUST_DST\n";}
	if ($AUST_DST) {$gmt_offset++;}
	$AC_processed++;
	}
if ( (!$AC_processed) and ($dst_range == 'FSO-TSM') )
	{
	if ($DBX) {print "     First Sunday October to Third Sunday March\n";}
	#**********************************************************************
	#     This is s 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect.
	#     Based on first Sunday in October and third Sunday in March at 1 am.
	#**********************************************************************
		
		$NZL_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 3 || $mm > 10) {
		$NZL_DST=1;
		} elseif ($mm >= 4 and $mm <= 9) {
		$NZL_DST=0;
		} elseif ($mm == 3) {
		if ($dd < 14) {
			$NZL_DST=1;
		} elseif ($dd < ($dow+14)) {
			$NZL_DST=1;
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$NZL_DST=1;
			} else {
				$NZL_DST=0;
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$NZL_DST=1;
			} else {
				$NZL_DST=0;
			}
			}
		} else {
			$NZL_DST=0;
		}
		} elseif ($mm == 10) {
		if ($dd > 7) {
			$NZL_DST=1;
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$NZL_DST=0;
			} else {
				$NZL_DST=1;
			}
			} else {
			if ($dow == 0 and $ns < 3600) {
				$NZL_DST=0;
			} else {
				$NZL_DST=1;
			}
			}
		} else {
			$NZL_DST=0;
		}
		} # end of month checks						
	if ($DBX) {print "     DST: $NZL_DST\n";}
	if ($NZL_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if ( (!$AC_processed) and ($dst_range == 'TSO-LSF') )
	{
	if ($DBX) {print "     Third Sunday October to Last Sunday February\n";}
	#**********************************************************************
	# TSO-LSF
	#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
	#       Standard time is in effect. Brazil
	#     Based on Third Sunday October to Last Sunday February at 1 am.
	#**********************************************************************
		
		$BZL_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 2 || $mm > 10) {
		$BZL_DST=1;   
		} elseif ($mm >= 3 and $mm <= 9) {
		$BZL_DST=0;   
		} elseif ($mm == 2) {
		if ($dd < 22) {
			$BZL_DST=1;   
		} elseif ($dd < ($dow+22)) {
			$BZL_DST=1;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$BZL_DST=1;   
			} else {
				$BZL_DST=0;   
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$BZL_DST=1;   
			} else {
				$BZL_DST=0;   
			}
			}
		} else {
			$BZL_DST=0;   
		}
		} elseif ($mm == 10) {
		if ($dd < 22) {
			$BZL_DST=0;   
		} elseif ($dd < ($dow+22)) {
			$BZL_DST=0;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$BZL_DST=0;   
			} else {
				$BZL_DST=1;   
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$BZL_DST=0;   
			} else {
				$BZL_DST=1;   
			}
			}
		} else {
			$BZL_DST=1;   
		}
		} # end of month checks
	if ($DBX) {print "     DST: $BZL_DST\n";}
	if ($BZL_DST) {$gmt_offset++;}
	$AC_processed++;
	}

if (!$AC_processed)
	{
	if ($DBX) {print "     No DST Method Found\n";}
	if ($DBX) {print "     DST: 0\n";}
	$AC_processed++;
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
    if ($state != "" and strlen($ct['ct_state_call_times'])>2) {
        foreach (explode('|',$ct['ct_state_call_times']) as $sr) {
            if (strlen($sr) > 1) {
                $sct = get_first_record($link, 'osdial_state_call_times', '*', sprintf("state_call_time_id='%s' AND state_call_time_state='%s'", mres($sr), mres(strtoupper($state)) ));
                if (strtoupper($sct['state_call_time_state']) == strtoupper($state)) {
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
    $xa = explode("\n", preg_replace("/>\s*</",">\n<",$pretty) );
    $pretty = array_shift($xa) . "\n";
    if ($indent < 1) $indent = 1;
    $cindent = 0;
    foreach($xa as $e) {
        if ( preg_match('/^<\/.+>$/',$e)) $cindent -= $indent;
        $pretty .=  str_repeat(' ', $cindent) . $e . "\n";
        if (preg_match('/^<([\w])+[^>\/]*>$/U',$e)) $cindent += $indent;
    }
    return $pretty;
}


# Quick function for bg row color.
function bgcolor($cnt) {
    global $oddrows;
    global $evenrows;
    $bgc = 'bgcolor="';
    if (eregi("1$|3$|5$|7$|9$", $cnt)) {
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
    if ($LOG['multicomp_user'] > 0) $strdat = substr($strdat,3);
    if ($LOG['multicomp_admin'] > 0 and preg_match($LOG['companiesRE'],$strdat)) $strdat = substr($strdat,0,3) . '&nbsp;' . substr($strdat,3);
    return $strdat;
}

?>
