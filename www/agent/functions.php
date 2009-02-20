<?php

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

?>
