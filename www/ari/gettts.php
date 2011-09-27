<?php
# tocsv.php - OSDial
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
#
#
function get_variable($varid) {
    $myvar = '';
    if (isset($_GET[$varid])) {
        $myvar=$_GET[$varid];
    } elseif (isset($_POST[$varid])) {
        $myvar=$_POST[$varid];
    }
    return $myvar;
}
function mimemap($file) {
    $mimetype = 'application/octet-stream';
    if (isset($file) and $file!='') {
        $ext = strtolower(preg_replace('/.*\/|.*\./','',$file));
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

$tts_id = get_variable("tts_id");
$download = get_variable('download');
if ($download=='') {
    $download='inline';
} else {
    $download='attachment';
}

exec("/opt/osdial/bin/osdial_tts_generate.pl --merge --quiet --id=$tts_id",$output);
$filename = $output[0];
$filename = preg_replace('/\/tmp\//','',$filename);
$mimetype=mimemap($filename);

if ($filename!='' and $mimetype!='') {
    header("Content-type: $mimetype; charset=utf-8");
    header("Content-Disposition: $download; filename=\"$filename\"");

    $handle = fopen("/tmp/".$filename,'rb');
    fpassthru($handle);
    fclose($handle);
    unlink("/tmp/".$filename);
}
exit;

?>
