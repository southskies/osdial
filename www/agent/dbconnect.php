<?
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
$conffile = "/etc/osdial.conf";
$cp2 = explode('/', getcwd());
$conffile2 = '/' . $cp2[1] . '/' . $cp2[2] . '/db.conf';
if ( file_exists($conffile2) ) {
    $conffile = $conffile2;
}
if ( file_exists($conffile) )
{
$DBCagc = file($conffile);
foreach ($DBCagc as $DBCline) 
	{
	$DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$DBCline);
	if (preg_match("/^PATHlogs/", $DBCline))
		{$PATHlogs = $DBCline;   $PATHlogs = preg_replace("/.*=/","",$PATHlogs);}
	if (preg_match("/^PATHweb/", $DBCline))
		{$WeBServeRRooT = $DBCline;   $WeBServeRRooT = preg_replace("/.*=/","",$WeBServeRRooT);}
	if (preg_match("/^VARDB_server/", $DBCline))
		{$VARDB_server = $DBCline;   $VARDB_server = preg_replace("/.*=/","",$VARDB_server);}
	if (preg_match("/^VARDB_database/", $DBCline))
		{$VARDB_database = $DBCline;   $VARDB_database = preg_replace("/.*=/","",$VARDB_database);}
	if (preg_match("/^VARDB_user/", $DBCline))
		{$VARDB_user = $DBCline;   $VARDB_user = preg_replace("/.*=/","",$VARDB_user);}
	if (preg_match("/^VARDB_pass/", $DBCline))
		{$VARDB_pass = $DBCline;   $VARDB_pass = preg_replace("/.*=/","",$VARDB_pass);}
	if (preg_match("/^VARDB_port/", $DBCline))
		{$VARDB_port = $DBCline;   $VARDB_port = preg_replace("/.*=/","",$VARDB_port);}
	}

}
else
{
#defaults for DB connection
$VARDB_server = 'localhost';
$VARDB_user = 'osdial';
$VARDB_pass = 'osdial1234';
$VARDB_database = 'osdial';
$WeBServeRRooT = '/opt/osdial/html';
}

$link=mysql_connect("$VARDB_server", "$VARDB_user", "$VARDB_pass");
mysql_select_db("$VARDB_database",$link);

$local_DEF = 'Local/';
$conf_silent_prefix = '7';
$local_AMP = '@';
$ext_context = 'osdial';
$recording_exten = '8309';
$WeBRooTWritablE = '1';
$non_latin = '0';	# set to 1 for UTF rules, overridden by system_settings
$flag_channels=0;
$flag_string = 'OSDIALast20';

?>
