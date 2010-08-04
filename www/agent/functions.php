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


# Get a variable from a form post/get
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

# Returns string with first three chars stripped if multicomp_user
function mclabel($strdat) {
    global $multicomp;
    if ($multicomp > 0) $strdat = substr($strdat,3);
    return $strdat;
}

# Simple shorthand for mysql_real_escape_string.
function mres($val) {
    return mysql_real_escape_string($val);
}

function load_status($smes) {
    echo "<script type=\"text/javascript\">\n";
    echo "document.getElementById('WelcomeBoxA').style.visibility = 'visible';\n";
    echo "document.getElementById('WelcomeBoxStatus').innerHTML = '$smes';\n";
    echo "</script>\n";
    flush();
}

function generate_calendar($prefix,$months) {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    define ('ADAY', (60*60*24));
    $CdayARY = getdate();
    $Cmon = $CdayARY['mon'];
    $Cyear = $CdayARY['year'];
    $CTODAY = date("Y-m");
    $CTODAYmday = date("j");
    $CINC = 0;
    if (!isset($cal_bg5)) $cal_bg5=$cal_bg3;
    
    $Cmonths = Array('January','February','March','April','May','June', 'July','August','September','October','November','December');
    $Cdays = Array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
    
    if ($months==0) $months=60;
    $Cmax_months = 0;
    while (($months + $Cmax_months) % 12 != 0) {
        $Cmax_months++;
    }
    
    $CCAL_OUT = "\n";
    while ($CINC < ($months + $Cmax_months)) {
        if ($CINC % 12 == 0) {
            $CCALstyle = "display:block;";
            if ($CINC > 0) $CCALstyle = "display:none;";
            $CCAL_OUT .= sprintf('<span id="%sCAL%s" style="%s">',$prefix,$CINC,$CCALstyle) . "\n";
            $CCAL_OUT .= "<table border=0 cellpadding=2 cellspacing=2>\n";
        }
        if ($CINC % 4 == 0) $CCAL_OUT .= "<tr>\n";
        $CCAL_OUT .= "<td valign=top>\n";
    
        $CYyear = $Cyear;
        $Cmonth = ($Cmon + $CINC);
        if ($Cmonth > 12) {
            $Cmonth = ($Cmonth - 12);
            $CYyear++;
        }
        $Cstart = mktime(11,0,0,$Cmonth,1,$CYyear);
        $CfirstdayARY = getdate($Cstart);
        $CPRNTDAY = date("Y-m", $Cstart);
    
        $CDC='';
    
        $CCAL_OUT .= "<table class=cal border=1 cellpadding=1 cellspacing=0>\n";
        $CCAL_OUT .= "<tr>\n";
        $CCAL_OUT .= sprintf('<td colspan=7 class=cal_head1>%s %s</td>',$CfirstdayARY['month'],$CfirstdayARY['year']) . "\n";
        $CCAL_OUT .= "</tr>\n\n";
    
        $CCAL_OUT .= "<tr>\n";
        foreach($Cdays as $Cday) {
            $CCAL_OUT .= sprintf('<td class=cal_head2>%s</td>',$Cday) . "\n";
        }
    
        $Crow = 0;
        for ($Ccount = 0; $Ccount < (6*7); $Ccount++) {
            $Cdayarray = getdate($Cstart);
            if($Ccount % 7 == 0) {
                if($Crow++ > 5 and $Cdayarray['mon'] != $CfirstdayARY['mon']) break;
                $CCAL_OUT .= "</tr>\n\n";
                $CCAL_OUT .= "<tr>\n";
            }
            $CDdayclass = 'calday';
            $CBLclick = '';
            $CBLdblclick = '';
            if ($Cmonth > 12) $Cmonth = ($Cmonth - 12);
            if ($CINC > $months and ($Ccount < $CfirstdayARY['wday'] or $Cdayarray['mon'] != $Cmonth)) {
                $CDdayclass = 'caldayold';
                $CBL = '&nbsp;';
            } elseif ($Ccount < $CfirstdayARY['wday'] or $Cdayarray['mon'] != $Cmonth) {
                $CBL = '&nbsp;';
                #$CBL = sprintf('<!-- %s %s %s %s -->',$Ccount,$CfirstdayARY['wday'],$Cdayarray['mon'],$Cmonth);
            } else {
                $CPRNTmday = sprintf('%02d',$Cdayarray['mday']);
                $CBLclick = sprintf('onclick="%s_date_pick(\'%s-%s\',this);"',$prefix,$CPRNTDAY,$CPRNTmday);
                $CBLdblclick = sprintf('ondblclick="%ssel();"',$prefix);
                $CBL = $Cdayarray['mday'];
                if($Cdayarray['mday'] == $CTODAYmday and $CPRNTDAY == $CTODAY) {
                    $CDdayclass = 'caldaysel';
                } elseif ($Cdayarray['mday'] < $CTODAYmday and $CPRNTDAY == $CTODAY) {
                    $CDdayclass = 'caldayold';
                    $CBLclick = '';
                    $CBLdblclick = '';
                }
                $Cstart += ADAY;
            }
            $CCAL_OUT .= sprintf('<td %s %s class=%s>%s</td>',$CBLclick,$CBLdblclick,$CDdayclass,$CBL) . "\n";
        }
        $CCAL_OUT .= "</tr>\n";
        $CCAL_OUT .= "</table>\n\n";
        $CCAL_OUT .= "</td>\n";
    
        $CTINC = ($CINC+1);
        $CCALnxtbtn = '';
        $CCALbckbtn = '';
        if ($CINC>0 and $CTINC % 12 == 0) {
            if ($CTINC <= 12 and $Cmax_months <= 12) {
                $CCALnxtbtn = '';
                $CCALbckbtn = '';
            } elseif ($CTINC > 12 and $CTINC % 12 == 0 and $CTINC != $Cmax_months) {
                $CCALbckbtn  = sprintf('<a href="#" onclick="document.getElementById(\'%sCAL%s\').style.display=\'block\';document.getElementById(\'%sCAL%s\').style.display=\'none\';">[&lt;- BACK]</a>',$prefix,($CTINC-24),$prefix,($CTINC-12));
                $CCALnxtbtn .= sprintf('<a href="#" onclick="document.getElementById(\'%sCAL%s\').style.display=\'none\';document.getElementById(\'%sCAL%s\').style.display=\'block\';">[NEXT -&gt;]</a>',$prefix,($CTINC-12),$prefix,$CTINC);
            } elseif ($CTINC % 12 == 0 and $CTINC != $Cmax_months) {
                $CCALnxtbtn .= sprintf('<a href="#" onclick="document.getElementById(\'%sCAL%s\').style.display=\'none\';document.getElementById(\'%sCAL%s\').style.display=\'block\';">[NEXT -&gt;]</a>',$prefix,($CTINC-12),$prefix,$CTINC);
            } elseif ($CTINC > 12 or $CTINC == $Cmax_months) {
                $CCALbckbtn  = sprintf('<a href="#" onclick="document.getElementById(\'%sCAL%s\').style.display=\'block\';document.getElementById(\'%sCAL%s\').style.display=\'none\';">[&lt;- BACK]</a>',$prefix,($CTINC-24),$prefix,($CTINC-12));
            }
    
        }
        if ($CTINC % 4 == 0) {
            $CCAL_OUT .= "</tr>\n\n";
            if ($CTINC % 12 == 0) {
                $CCAL_OUT .= "<tr>\n";
                $CCAL_OUT .= "<td></td>\n";
                $CCAL_OUT .= sprintf('<td align=center style="font-size:9pt;color:%s;">%s</td>',$cal_fc,$CCALbckbtn) . "\n";
                $CCAL_OUT .= sprintf('<td align=center style="font-size:9pt;color:%s;">%s</td>',$cal_fc,$CCALnxtbtn) . "\n";
                $CCAL_OUT .= "<td></td>";
                $CCAL_OUT .= "</tr>\n\n";
            }
        }
        if ($CTINC % 12 == 0 or $CTINC == $Cmax_months) {
            $CCAL_OUT .= "</table>\n";
            $CCAL_OUT .= "</span>\n\n";
        }
        $CINC++;
    }

    return $CCAL_OUT;
}


# Function to send emails
function send_email($host, $port, $user, $pass, $to, $from, $subject, $html, $text) {
    include('Mail.php');
    include('Mail/mime.php');

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

    $mime = new Mail_mime("\n");
    if ($html) $mime->setHTMLBody($html);
    if ($text) $mime->setTXTBody($text);
    $message = $mime->get();
    $headers = $mime->headers($headers);

    $mail =& Mail::factory('smtp', $params);
    $mail->send($to, $headers, $message);
}

?>
