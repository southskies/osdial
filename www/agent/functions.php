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
    while ($krhrow = mysql_fetch_assoc($krhrslt)) {
        $krhrows[] = $krhrow;
    }
    return $krhrows;
}

function get_first_record($link, $tbl, $flds="*", $whr="") {
    if ($whr != '') {
        $whr = ' WHERE ' . $whr;
    }
    $gfrstmt="SELECT SQL_NO_CACHE " . $flds . " FROM " . $tbl . $whr . ' LIMIT 1';
    $gfrrslt=mysql_query($gfrstmt, $link);
    $gfrrow = mysql_fetch_assoc($gfrrslt);
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

function hide_element($yesno) {
    $elstyle = 'inherit';
    if ($yesno=='Y') $elstyle = 'hidden';
    return $elstyle;
}


function parseTimezones() {
    # The following creates arrays with TZ mappings.
    require_once 'Date.php';
    $tzret = array();
    $tzret['tzrefid'] = array();
    $tzret['tzrefidDST'] = array();
    $tzret['tzalt'] = array();
    $tzret['tzaltDST'] = array();
    $tzret['tznames'] = array();
    $tzret['tznamesDST'] = array();
    $tzret['tznames2'] = array();
    $tzret['tznamesDST2'] = array();
    $tzret['tzoffsets'] = array();
    $tzret['tzoffsetsDST'] = array();
    $tzids = Date_TimeZone::getAvailableIDs();
    arsort($tzids);
    $tzorder = array('^SystemV.*','^US.*','^America.*','^Europe.*','^Asia.*','^Africa.*','^Atlantic.*','^Pacific.*');
    foreach ($tzorder as $tzmatch) {
        foreach ($tzids as $tzid) {
            if (preg_match('/'.$tzmatch.'/',$tzid)) {
                $tmptz = new Date_TimeZone($tzid);
                $tzsn = $tmptz->getShortName();
                if (!empty($tzsn)) { 
                    $tzln = $tmptz->getLongName();
                    $tzdsn = $tmptz->getDSTShortName();
                    if (empty($tzdsn)) $tzdsn = $tzsn;
                    $tzoff = $tmptz->getRawOffset() / 3600000;
                    $tzoffDST = $tmptz->getOffset() / 3600000;

                    if (!isset($tzret['tzalt'][$tzdsn])) $tzret['tzalt'][$tzdsn] = $tzsn;
                    if (!isset($tzret['tzaltDST'][$tzsn])) $tzret['tzaltDST'][$tzsn] = $tzdsn;

                    $tzsep = '';
                    if (!isset($tzret['tzoffsets'][$tzsep . $tzoff])) $tzret['tzoffsets'][$tzsep . $tzoff] = $tzsn;
                    if (!isset($tzret['tznames'][$tzsn])) $tzret['tznames'][$tzsn] = $tzoff;
                    $tzsep = '';
                    if ($tzoff >= 0) $tzsep = '+';
                    if (!isset($tzret['tznames2'][$tzsn . $tzsep . $tzoff])) $tzret['tznames2'][$tzsn . $tzsep . $tzoff] = $tzoff;
                    if (!isset($tzret['tzrefid'][$tzsn])) $tzret['tzrefid'][$tzsn] = $tzid;

                    $tzsep = '';
                    if (!isset($tzret['tzoffsetsDST'][$tzsep . $tzoffDST])) $tzret['tzoffsetsDST'][$tzsep . $tzoffDST] = $tzdsn;
                    if (!isset($tzret['tznamesDST'][$tzdsn])) $tzret['tznamesDST'][$tzdsn] = $tzoffDST;
                    $tzsep = '';
                    if ($tzoffDST >= 0) $tzsep = '+';
                    if (!isset($tzret['tznamesDST2'][$tzdsn . $tzsep . $tzoffDST])) $tzret['tznamesDST2'][$tzdsn . $tzsep . $tzoffDST] = $tzoffDST;
                    if (!isset($tzret['tzrefidDST'][$tzdsn])) $tzret['tzrefidDST'][$tzdsn] = $tzid;
                }
            }
        }
    }
    $tzt = $tzret['tzalt'];
    foreach ($tzret['tzaltDST'] as $k => $v) {
        if (!isset($tzret['tzalt'][$k])) $tzret['tzalt'][$k] = $k;
    }
    foreach ($tzt as $k => $v) {
        if (!isset($tzret['tzaltDST'][$k])) $tzret['tzaltDST'][$k] = $k;
    }
    ksort($tzret['tzrefid']);
    ksort($tzret['tzrefidDST']);
    ksort($tzret['tzalt']);
    ksort($tzret['tzaltDST']);
    ksort($tzret['tznames']);
    ksort($tzret['tznamesDST']);
    ksort($tzret['tznames2']);
    ksort($tzret['tznamesDST2']);
    ksort($tzret['tzoffsets']);
    ksort($tzret['tzoffsetsDST']);
    return $tzret;
}

function dateCalcServerLocalGMTOffset($svrGMT, $locGMT, $locisDST, $tzsecs) {
    $tzs = parseTimezones();
    $tzoffsets = $tzs['tzoffsets'];
    $tzoffsetsDST = $tzs['tzoffsetsDST'];
    $tzalt = $tzs['tzalt'];
    $tzaltDST = $tzs['tzaltDST'];
    $tzrefid = $tzs['tzrefid'];
    $tzrefidDST = $tzs['tzrefidDST'];

    $dcsret = array();
    $tzdate = new Date();
    $tzdate->setFromTime($tzsecs);

    $svrGMT = $svrGMT * 1;
    $dcsret['svrsname'] = $tzoffsets[$svrGMT];
    $svrGMTname = $tzrefid[$dcsret['svrsname']];
    $svrtz = new Date_TimeZone($svrGMTname);
    $dcsret['svroffset'] = $svrtz->getOffset($tzdate) / 3600000;
    $dcsret['svrdst'] = $svrtz->inDaylightTime($tzdate);

    $locGMT = ($locGMT * 1);
    $dcsret['locsname'] = $tzoffsets[$locGMT];
    $locGMTname = $tzrefid[$dcsret['locsname']];
    if ($locisDST!=0) {
        $dcsret['locsname'] = $tzoffsetsDST[$locGMT];
        $locGMTname = $tzrefidDST[$dcsret['locsname']];
    }
    $loctz = new Date_TimeZone($locGMTname);
    $dcsret['locoffset'] = $loctz->getOffset($tzdate) / 3600000;
    $dcsret['locdst'] = $loctz->inDaylightTime($tzdate);

    return $dcsret;
}

function dateToLocal($link, $svrip, $cnvdate, $locGMT, $fmt="", $locisDST, $addlocTZlabel) {
    $tzs = parseTimezones();
    $tzalt = $tzs['tzalt'];
    $tzaltDST = $tzs['tzaltDST'];
    $system_settings = get_first_record($link, 'system_settings', '*','');

    if (empty($cnvdate)) return '';
    $dsecs = strtotime($cnvdate);
    if (empty($fmt)) $fmt=$system_settings['default_date_format'];
    if ($system_settings['use_browser_timezone_offset']=='N') return date($fmt, $dsecs);

    if (is_numeric($svrip) and $svrip>-27 and $svrip<27 and $svrip!='first') {
        $svrGMT = $svrip * 1;
    } else {
        $svrSQL = "active='Y' AND server_profile IN ('AIO','DIALER')";
        if ($svrip!='first') $svrSQL=sprintf("server_ip='%s'", mres($svrip));
        $server = get_first_record($link, 'servers', '*', $svrSQL);
        if (!is_array($server)) return date($fmt, $dsecs);
        $svrGMT = $server['local_gmt'] * 1;
    }

    $dcsoff = dateCalcServerLocalGMTOffset($svrGMT, $locGMT, $locisDST, $dsecs);

    $dsecs -= $dcsoff['svroffset'] * 60 * 60;
    $dsecs += $dcsoff['locoffset'] * 60 * 60;

    $locTZlabel = '';
    $retdate = date($fmt, $dsecs);
    if (preg_match('/'.$tzalt[$dcsoff['locsname']].'|'.$tzaltDST[$dcsoff['locsname']].'/',$retdate)) {
        return $retdate;
    } else {
        if ($addlocTZlabel>0) {
            if ($dcsoff['locdst']) {
                $locTZlabel=' '.$tzaltDST[$dcsoff['locsname']];
            } else {
                $locTZlabel=' '.$tzalt[$dcsoff['locsname']];
            }
        }
        return $retdate . $locTZlabel;
    }
}


function dateToServer($link, $svrip, $cnvdate, $locGMT, $fmt="", $locisDST, $addsvrTZlabel) {
    $tzs = parseTimezones();
    $tzalt = $tzs['tzalt'];
    $tzaltDST = $tzs['tzaltDST'];
    $system_settings = get_first_record($link, 'system_settings', '*','');

    if (empty($cnvdate)) return '';
    $dsecs = strtotime($cnvdate);
    if (empty($fmt)) $fmt='Y-m-d H:i:s';
    if ($system_settings['use_browser_timezone_offset']=='N') return date($fmt, $dsecs);

    if (is_numeric($svrip) and $svrip>-27 and $svrip<27 and $svrip!='first') {
        $svrGMT = $svrip * 1;
    } else {
        $svrSQL = "active='Y' AND server_profile IN ('AIO','DIALER')";
        if ($svrip!='first') $svrSQL=sprintf("server_ip='%s'", mres($svrip));
        $server = get_first_record($link, 'servers', '*', $svrSQL);
        if (!is_array($server)) return date($fmt, $dsecs);
        $svrGMT = $server['local_gmt'] * 1;
    }
    $dcsoff = dateCalcServerLocalGMTOffset($svrGMT, $locGMT, $locisDST, $dsecs);

    $dsecs -= ($dcsoff['locoffset'] - $dcsoff['svroffset']) * 60 * 60;

    $svrTZlabel = '';
    if ($addsvrTZlabel>0) {
        if ($dcsoff['svrdst']) {
            $svrTZlabel=' '.$tzaltDST[$dcsoff['svrsname']];
        } else {
            $svrTZlabel=' '.$tzalt[$dcsoff['svrsname']];
        }
    }
    return date($fmt, $dsecs) . $svrTZlabel;
}

function debugLog($filename, $output) {
    global $WeBRooTWritablE;
    if ($WeBRooTWritablE > 0) {
        if (preg_match("/^[^\/]/",$filename)) $filename = './' . $filename;
        $fp = fopen($filename.".txt", "a");
        fwrite ($fp, $output . "\n");
        fclose($fp);
    }
}

function OSDstrlen($instr) {
    if (is_array($instr)) {
        return 0;
    }
    return mb_strlen($instr);
}
function OSDstrtolower($instr) {
    return mb_strtolower($instr);
}
function OSDstrtoupper($instr) {
    return mb_strtoupper($instr);
}
function OSDpreg_replace($inre,$insub,$instr) {
    return preg_replace($inre.'u',$insub,$instr);
}
function OSDpreg_split($inre,$instr) {
    return preg_split($inre.'u',$instr);
}
function OSDpreg_splitX($inre,$instr,$incnt) {
    return preg_split($inre.'u',$instr,$incnt);
}
function OSDpreg_match($inre,$instr) {
    return preg_match($inre.'u',$instr);
}
function OSDsubstr($instr,$instart,$inlen) {
    return mb_substr($instr,$instart,$inlen);
}

function hangup_cause_description($isup) {
    $hangup_cause_dictionary = array(
        0 => "Unspecified. No other cause codes applicable.",
        1 => "Unallocated (unassigned) number.",
        2 => "No route to specified transit network (national use).",
        3 => "No route to destination.",
        6 => "Channel unacceptable.",
        7 => "Call awarded, being delivered in an established channel.",
        16 => "Normal call clearing.",
        17 => "User busy.",
        18 => "No user responding.",
        19 => "No answer from user (user alerted).",
        20 => "Subscriber absent.",
        21 => "Call rejected.",
        22 => "Number changed.",
        23 => "Redirection to new destination.",
        25 => "Exchange routing error.",
        27 => "Destination out of order.",
        28 => "Invalid number format (address incomplete).",
        29 => "Facilities rejected.",
        30 => "Response to STATUS INQUIRY.",
        31 => "Normal, unspecified.",
        34 => "No circuit/channel available.",
        38 => "Network out of order.",
        41 => "Temporary failure.",
        42 => "Switching equipment congestion.",
        43 => "Access information discarded.",
        44 => "Requested circuit/channel not available.",
        50 => "Requested facility not subscribed.",
        52 => "Outgoing calls barred.",
        54 => "Incoming calls barred.",
        57 => "Bearer capability not authorized.",
        58 => "Bearer capability not presently available.",
        63 => "Service or option not available, unspecified.",
        65 => "Bearer capability not implemented.",
        66 => "Channel type not implemented.",
        69 => "Requested facility not implemented.",
        79 => "Service or option not implemented, unspecified.",
        81 => "Invalid call reference value.",
        88 => "Incompatible destination.",
        95 => "Invalid message, unspecified.",
        96 => "Mandatory information element is missing.",
        97 => "Message type non-existent or not implemented.",
        98 => "Message not compatible with call state or message type non-existent or not implemented.",
        99 => "Information element / parameter non-existent or not implemented.",
        100 => "Invalid information element contents.",
        101 => "Message not compatible with call state.",
        102 => "Recovery on timer expiry.",
        103 => "Parameter non-existent or not implemented - passed on (national use).",
        111 => "Protocol error, unspecified.",
        127 => "Interworking, unspecified.",
        487 => "Originator cancel.",
        500 => "Crash.",
        501 => "System shutdown.",
        502 => "Lose race.",
        503 => "Manager request.",
        600 => "Blind transfer.",
        601 => "Attended transfer.",
        602 => "Allotted timeout.",
        603 => "User challenge.",
        604 => "Media timeout.",
        605 => "Picked off.",
        606 => "User not registered.",
        607 => "Progress timeout."
    );
    if (isset($hangup_cause_dictionary[$isup])) return $hangup_cause_dictionary[$isup];
    return "Unknown ISUP Result Code.";
}


function is_assoc($array) {
    foreach (array_keys($array) as $k => $v) {
        if ($k !== $v) return true;
    }
    return false;
}

function osdevent($link, $opts) {
    if (is_assoc($opts)) {
        $oelsql = '';
        foreach ($opts as $k => $v) {
                $oelsql .= sprintf("%s='%s',",$k,mres($v));
        }
        $oelsql = rtrim($oelsql,',');
        if (!empty($oelsql)) {
            $stmt = sprintf("INSERT INTO osdial_events SET %s;",$oelsql);
            $rslt = mysql_query($stmt, $link);
            return mysql_insert_id($link);
        }
    }
    return 0;
}

?>
