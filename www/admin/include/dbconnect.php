<?
# 
# Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
if ( file_exists("/etc/astguiclient.conf") )
{
$DBCagc = file("/etc/astguiclient.conf");
foreach ($DBCagc as $DBCline) 
	{
	$DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$DBCline);
	if (ereg("^PATHlogs", $DBCline))
		{$PATHlogs = $DBCline;   $PATHlogs = preg_replace("/.*=/","",$PATHlogs);}
	if (ereg("^PATHweb", $DBCline))
		{$WeBServeRRooT = $DBCline;   $WeBServeRRooT = preg_replace("/.*=/","",$WeBServeRRooT);}
	if (ereg("^VARDB_server", $DBCline))
		{$VARDB_server = $DBCline;   $VARDB_server = preg_replace("/.*=/","",$VARDB_server);}
	if (ereg("^VARDB_database", $DBCline))
		{$VARDB_database = $DBCline;   $VARDB_database = preg_replace("/.*=/","",$VARDB_database);}
	if (ereg("^VARDB_user", $DBCline))
		{$VARDB_user = $DBCline;   $VARDB_user = preg_replace("/.*=/","",$VARDB_user);}
	if (ereg("^VARDB_pass", $DBCline))
		{$VARDB_pass = $DBCline;   $VARDB_pass = preg_replace("/.*=/","",$VARDB_pass);}
	if (ereg("^VARDB_port", $DBCline))
		{$VARDB_port = $DBCline;   $VARDB_port = preg_replace("/.*=/","",$VARDB_port);}
	}

}
else
{
#defaults for DB connection
$VARDB_server = 'localhost';
$VARDB_user = 'cron';
$VARDB_pass = '1234';
$VARDB_database = '1234';
$WeBServeRRooT = '/usr/local/apache2/htdocs';
}

$link=mysql_connect("$VARDB_server", "$VARDB_user", "$VARDB_pass");
mysql_select_db("$VARDB_database");

$local_DEF = 'Local/';
$conf_silent_prefix = '7';
$local_AMP = '@';
$ext_context = 'demo';
$recording_exten = '8309';
$WeBRooTWritablE = '1';
$non_latin = '0';	# set to 1 for UTF rules
$AM_shift_BEGIN = '03:45:00';
$AM_shift_END = '17:45:00';
$PM_shift_BEGIN = '17:45:01';
$PM_shift_END = '23:59:59';
?>
