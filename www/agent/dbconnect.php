<?php
# 
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
#
require_once('functions.php');

$config = array();

$conffile = "/etc/osdial.conf";
$cp2 = explode('/', getcwd());
$conffile2 = '/' . $cp2[1] . '/' . $cp2[2] . '/db.conf';
if (file_exists($conffile2)) $conffile = $conffile2;

if (file_exists($conffile)) {
    $DBCagc = file($conffile);
    foreach ($DBCagc as $DBCline) {
        $DBCline = preg_replace("/ |>|\"|'|\n|\r|\t|\#.*|;.*/","",$DBCline);
        if (preg_match("/=|:/", $DBCline)) {
            $DBClinesplit = preg_split("/=|:/", $DBCline, 2);
            $config[$DBClinesplit[0]] = $DBClinesplit[1];
        }
        if (preg_match("/^PATHlogs/", $DBCline)) {
            $PATHlogs = $DBCline;
            $PATHlogs = preg_replace("/.*=/","",$PATHlogs);
        } elseif (preg_match("/^PATHweb/", $DBCline)) {
            $WeBServeRRooT = $DBCline;
            $WeBServeRRooT = preg_replace("/.*=/","",$WeBServeRRooT);
        } elseif (preg_match("/^VARDB_server/", $DBCline)) {
            $VARDB_server = $DBCline;
            $VARDB_server = preg_replace("/.*=/","",$VARDB_server);
        } elseif (preg_match("/^VARDB_database/", $DBCline)) {
            $VARDB_database = $DBCline;
            $VARDB_database = preg_replace("/.*=/","",$VARDB_database);
        } elseif (preg_match("/^VARDB_user/", $DBCline)) {
            $VARDB_user = $DBCline;
            $VARDB_user = preg_replace("/.*=/","",$VARDB_user);
        } elseif (preg_match("/^VARDB_pass/", $DBCline)) {
            $VARDB_pass = $DBCline;
            $VARDB_pass = preg_replace("/.*=/","",$VARDB_pass);
        } elseif (preg_match("/^VARDB_port/", $DBCline)) {
            $VARDB_port = $DBCline;
            $VARDB_port = preg_replace("/.*=/","",$VARDB_port);
        } elseif (preg_match("/^VARserver_ip/", $DBCline)) {
            $VARserver_ip = $DBCline;
            $VARserver_ip = preg_replace("/.*=/","",$VARserver_ip);
        }
    }
} else {
    #configuration defaults
    $PATHlogs = '/var/log/osdial';
    $config['PATHlogs'] = $PATHlogs;

    $PATHweb = '/opt/osdial/html';
    $config['PATHweb'] = $PATHweb;
    $WeBServeRRooT = $PATHweb;

    $VARDB_server = 'localhost';
    $config['VARDB_server'] = $VARDB_server;

    $VARDB_database = 'osdial';
    $config['VARDB_database'] = $VARDB_database;

    $VARDB_user = 'osdial';
    $config['VARDB_user'] = $VARDB_user;

    $VARDB_pass = 'osdial1234';
    $config['VARDB_pass'] = $VARDB_pass;

    $VARDB_port = '3306';
    $config['VARDB_port'] = $VARDB_port;

    $VARserver_ip = '127.0.0.1';
    $config['VARserver_ip'] = $VARserver_ip;
}

$link=mysql_connect($config['VARDB_server'].':'.$config['VARDB_port'], $config['VARDB_user'], $config['VARDB_pass']);
mysql_select_db($config['VARDB_database'],$link);

$stmt = "SELECT * FROM system_settings LIMIT 1;";
$rslt=mysql_query($stmt, $link);
$confnum = mysql_num_rows($rslt);
$i=0;
while ($i < $confnum) {
    $row=mysql_fetch_assoc($rslt);
    foreach ($row as $k => $v) {
        $config['settings'][$k] = $v;
    }
    $i++;
}
$config['settings']['intra_server_sep']='*';
if ($config['settings']['intra_server_protocol']='IAX2') $config['settings']['intra_server_sep']='#';

mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
if ($config['settings']['use_non_latin'] > 0) {
    $rslt=mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';",$link);
    mysql_set_charset('utf8', $link);
} else {
    $rslt=mysql_query("SET NAMES 'latin1' COLLATE 'latin1_swedish_ci';",$link);
    mysql_set_charset('latin1', $link);
}

$stmt = "SELECT * FROM system_settings LIMIT 1;";
$rslt=mysql_query($stmt, $link);
$confnum = mysql_num_rows($rslt);
$i=0;
while ($i < $confnum) {
    $row=mysql_fetch_assoc($rslt);
    foreach ($row as $k => $v) {
        $config['settings'][$k] = $v;
    }
    $i++;
}

$stmt = sprintf("SELECT * FROM servers WHERE server_ip='%s' LIMIT 1;",mres($config['VARserver_ip']));
$rslt=mysql_query($stmt, $link);
$confnum = mysql_num_rows($rslt);
if ($confnum==0) {
    $stmt = "SELECT * FROM servers WHERE active='Y' LIMIT 1;";
    $rslt=mysql_query($stmt, $link);
    $confnum = mysql_num_rows($rslt);
}
$i=0;
while ($i < $confnum) {
    $row=mysql_fetch_assoc($rslt);
    foreach ($row as $k => $v) {
        $config['server'][$k] = $v;
    }
    $i++;
}


$local_DEF = 'Local/';
$conf_silent_prefix = '7';
$local_AMP = '@';
$ext_context = 'osdial';
$recording_exten = '8309';
$WeBRooTWritablE = '1';
$flag_channels=0;
$flag_string = 'OSDIALast20';

?>
