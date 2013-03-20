<?php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
# Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>  LICENSE: AGPLv3
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



### Conferences



######################
# ADD=1111111111111 display the ADD NEW CONFERENCE SCREEN
######################

if ($ADD==1111111111111)
{
	if ($LOG['ast_admin_access']==1)
	{
    $servers_list = get_servers($link, $server_ip, 'AIO|DIALER');
	echo "<center><br><font color=$default_text size=+1>ADD A NEW CONFERENCE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2111111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Conference Number: </td><td align=left><input type=text name=conf_exten size=8 maxlength=7> (digits only)".helptag("conferences-conf_exten")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	#echo "<option SELECTED>$server_ip</option>\n";
	echo "</select>".helptag("conferences-server_ip")."</td></tr>\n";
	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



######################
# ADD=2111111111111 adds new conference to the system
######################

if ($ADD==2111111111111)
{
	$stmt=sprintf("SELECT count(*) FROM conferences WHERE conf_exten='%s' AND server_ip='%s';",mres($conf_exten),mres($server_ip));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>CONFERENCE NOT ADDED - there is already a conference in the system with this ID and server</font>\n";}
	else
		{
		 if ( (OSDstrlen($conf_exten) < 1) or (OSDstrlen($server_ip) < 7) )
			{echo "<br><font color=red>CONFERENCE NOT ADDED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=$default_text>CONFERENCE ADDED</font>\n";

			$stmt=sprintf("INSERT INTO conferences (conf_exten,server_ip) VALUES('%s','%s');",mres($conf_exten),mres($server_ip));
			$rslt=mysql_query($stmt, $link);
			}
		}
$ADD=3111111111111;
}




######################
# ADD=4111111111111 modify conference record in the system
######################

if ($ADD==4111111111111)
{
	if ($LOG['ast_admin_access']==1)
	{
	$stmt=sprintf("SELECT count(*) FROM conferences WHERE conf_exten='%s' AND server_ip='%s';",mres($conf_exten),mres($server_ip));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ( ($row[0] > 0) && ( ($conf_exten != $old_conf_exten) or ($server_ip != $old_server_ip) ) )
		{echo "<br><font color=red>CONFERENCE NOT MODIFIED - there is already a Conference in the system with this extension-server</font>\n";}
	else
		{
		 if ( (OSDstrlen($conf_exten) < 1) or (OSDstrlen($server_ip) < 7) )
			{echo "<br><font color=red>CONFERENCE NOT MODIFIED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=$default_text>CONFERENCE MODIFIED: $conf_exten</font>\n";

			$stmt=sprintf("UPDATE conferences SET conf_exten='%s',server_ip='%s',extension='%s' WHERE conf_exten='%s' AND server_ip='%s';",mres($conf_exten),mres($server_ip),mres($extension),mres($old_conf_exten),mres($old_server_ip));
			$rslt=mysql_query($stmt, $link);
			}
		}
    $ADD=3111111111111;	# go to conference modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}




######################
# ADD=5111111111111 confirmation before deletion of conference record
######################

if ($ADD==5111111111111)
{
	 if ( (OSDstrlen($conf_exten) < 2) or (OSDstrlen($server_ip) < 7) or ($LOG['ast_delete_phones'] < 1) )
		{
		 echo "<br><font color=red>CONFERENCE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Conference must be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>CONFERENCE DELETION CONFIRMATION: $conf_exten - $server_ip</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6111111111111&conf_exten=$conf_exten&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete conference $conf_exten - $server_ip</a></font><br><br><br>\n";
		}
$ADD='3111111111111';		# go to conference modification below
}



######################
# ADD=6111111111111 delete conference record
######################

if ($ADD==6111111111111)
{
	 if ( (OSDstrlen($conf_exten) < 2) or (OSDstrlen($server_ip) < 7) or ($CoNfIrM != 'YES') or ($LOG['ast_delete_phones'] < 1) )
		{
		 echo "<br><font color=red>CONFERENCE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Conference be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		$stmt=sprintf("DELETE FROM conferences WHERE conf_exten='%s' AND server_ip='%s' LIMIT 1;",mres($conf_exten),mres($server_ip));
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING CONF!!!!|$PHP_AUTH_USER|$ip|conf_exten='$conf_exten'|server_ip='$server_ip'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=$default_text>CONFERENCE DELETION COMPLETED: $conf_exten - $server_ip</font></B>\n";
		echo "<br><br>\n";
		}
$ADD='1000000000000';		# go to conference list
}




######################
# ADD=3111111111111 modify conference record in the system
######################

if ($ADD==3111111111111)
{
	if ($LOG['ast_admin_access']==1)
	{
	$stmt=sprintf("SELECT * FROM conferences WHERE conf_exten='%s' AND server_ip='%s';",mres($conf_exten),mres($server_ip));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$conf_exten = $row[0];
	$server_ip = $row[1];

    $servers_list = get_servers($link, $row[1], 'AIO|DIALER');

	echo "<center><br><font class=top_header color=$default_text size=+1>MODIFY A CONFERENCE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=4111111111111>\n";
	echo "<input type=hidden name=old_conf_exten value=\"$row[0]\">\n";
	echo "<input type=hidden name=old_server_ip value=\"$row[1]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Conference: </td><td align=left><input type=text name=conf_exten size=10 maxlength=7 value=\"$row[0]\">".helptag("conferences-conf_exten")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=311111111111&server_ip=$row[1]\">Server IP</a>: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	#echo "<option SELECTED>$row[1]</option>\n";
	echo "</select>".helptag("conferences-server_ip")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Current Extension: </td><td align=left><input type=text name=extension size=20 maxlength=20 value=\"$row[2]\"></td></tr>\n";
	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";

	if ($LOG['ast_delete_phones'] > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=5111111111111&conf_exten=$conf_exten&server_ip=$server_ip\">DELETE THIS CONFERENCE</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



######################
# ADD=1000000000000 display all conferences
######################
if ($ADD==1000000000000)
{
	$stmt="SELECT * from conferences order by conf_exten";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

echo "<center><br><font class=top_header color=$default_text size=+1>CONFERENCES</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>ID</td>\n";
echo "    <td>SERVER</td>\n";
echo "    <td>EXTENSION</td>\n";
echo "    <td align=center colspan=3>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3111111111111&conf_exten=$row[0]&server_ip=$row[1]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$row[0]&server_ip=$row[1]\">$row[0]</a></td>\n";
		echo "    <td>$row[1]</td>\n";
		echo "    <td>$row[2]</td>\n";
		echo "    <td colspan=3 align=center><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$row[0]&server_ip=$row[1]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=6></td>\n";
echo "  </tr>\n";
echo "</table></center>\n";
}



####  OSD Conferences


######################
# ADD=11111111111111 display the ADD NEW OSDial CONFERENCE SCREEN
######################

if ($ADD==11111111111111)
{
	if ($LOG['ast_admin_access']==1)
	{
    $servers_list = get_servers($link, $server_ip, 'AIO|DIALER');
	echo "<center><br><font color=$default_text size=+1>ADD A NEW $t1 CONFERENCE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Conference Number: </td><td align=left><input type=text name=conf_exten size=8 maxlength=7> (digits only)".helptag("conferences-conf_exten")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	#echo "<option SELECTED>$server_ip</option>\n";
	echo "</select>".helptag("conferences-server_ip")."</td></tr>\n";
	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=21111111111111 adds new osdial conference to the system
######################

if ($ADD==21111111111111)
{
	$stmt=sprintf("SELECT count(*) FROM osdial_conferences WHERE conf_exten='%s' AND server_ip='%s';",mres($conf_exten),mres($server_ip));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>$t1 CONFERENCE NOT ADDED - there is already an $t1 conference in the system with this ID and server</font>\n";}
	else
		{
		 if ( (OSDstrlen($conf_exten) < 1) or (OSDstrlen($server_ip) < 7) )
			{echo "<br><font color=red>$t1 CONFERENCE NOT ADDED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=$default_text>$t1 CONFERENCE ADDED</font>\n";

			$stmt=sprintf("INSERT INTO osdial_conferences (conf_exten,server_ip) VALUES('%s','%s');",mres($conf_exten),mres($server_ip));
			$rslt=mysql_query($stmt, $link);
			}
		}
$ADD=31111111111111;
}


######################
# ADD=41111111111111 modify osdial conference record in the system
######################

if ($ADD==41111111111111)
{
	if ($LOG['ast_admin_access']==1)
	{
	$stmt=sprintf("SELECT count(*) FROM osdial_conferences WHERE conf_exten='%s' AND server_ip='%s';",mres($conf_exten),mres($server_ip));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ( ($row[0] > 0) && ( ($conf_exten != $old_conf_exten) or ($server_ip != $old_server_ip) ) )
		{echo "<br><font color=red>$t1 CONFERENCE NOT MODIFIED - there is already a Conference in the system with this extension-server</font>\n";}
	else
		{
		 if ( (OSDstrlen($conf_exten) < 1) or (OSDstrlen($server_ip) < 7) )
			{echo "<br><font color=red>$t1 CONFERENCE NOT MODIFIED - Please go back and look at the data you entered</font>\n";}
		 else
			{
			echo "<br><font color=$default_text>$t1 CONFERENCE MODIFIED: $conf_exten</font>\n";

			$stmt=sprintf("UPDATE osdial_conferences SET conf_exten='$conf_exten',server_ip='%s',extension='%s' WHERE conf_exten='%s' AND server_ip='%s';",mres($conf_exten),mres($server_ip),mres($extension),mres($old_conf_exten),mres($old_server_ip));
			$rslt=mysql_query($stmt, $link);

			}
		}
    $ADD=31111111111111;	# go to osdial conference modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



######################
# ADD=51111111111111 confirmation before deletion of osdial conference record
######################

if ($ADD==51111111111111)
{
	 if ( (OSDstrlen($conf_exten) < 2) or (OSDstrlen($server_ip) < 7) or ($LOG['ast_delete_phones'] < 1) )
		{
		 echo "<br><font color=red>$t1 CONFERENCE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Conference must be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>$t1 CONFERENCE DELETION CONFIRMATION: $conf_exten - $server_ip</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111111111111&conf_exten=$conf_exten&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete phone $conf_exten - $server_ip</a></font><br><br><br>\n";
		}
$ADD='31111111111111';		# go to osdial conference modification below
}



######################
# ADD=61111111111111 delete osdial conference record
######################

if ($ADD==61111111111111)
{
	 if ( (OSDstrlen($conf_exten) < 2) or (OSDstrlen($server_ip) < 7) or ($CoNfIrM != 'YES') or ($LOG['ast_delete_phones'] < 1) )
		{
		 echo "<br><font color=red>$t1 CONFERENCE NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Conference be at least 2 characters in length\n";
		 echo "<br>Server IP be at least 7 characters in length</font><br>\n";
		}
	 else
		{
		$stmt=sprintf("DELETE FROM osdial_conferences WHERE conf_exten='%s' AND server_ip='%s' LIMIT 1;",mres($conf_exten),mres($server_ip));
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!!!DELETING CONF!!!!|$PHP_AUTH_USER|$ip|conf_exten='$conf_exten'|server_ip='$server_ip'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=$default_text>$t1 CONFERENCE DELETION COMPLETED: $conf_exten - $server_ip</font></B>\n";
		echo "<br><br>\n";
		}
$ADD='10000000000000';		# go to osdial conference list
}


######################
# ADD=31111111111111 modify osdial conference record in the system
######################

if ($ADD==31111111111111)
{
	if ($LOG['ast_admin_access']==1)
	{
	$stmt=sprintf("SELECT * FROM osdial_conferences WHERE conf_exten='%s' AND server_ip='%s';",mres($conf_exten),mres($server_ip));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$conf_exten = $row[0];
	$server_ip = $row[1];

    $servers_list = get_servers($link, $row[1], 'AIO|DIALER');

	echo "<center><br><font color=$default_text size=+1>MODIFY AN $t1 CONFERENCE</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111111111111>\n";
	echo "<input type=hidden name=old_conf_exten value=\"$row[0]\">\n";
	echo "<input type=hidden name=old_server_ip value=\"$row[1]\">\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Conference: </td><td align=left><input type=text name=conf_exten size=10 maxlength=7 value=\"$row[0]\">".helptag("conferences-conf_exten")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=311111111111&server_ip=$row[1]\">Server IP</a>: </td><td align=left><select size=1 name=server_ip>\n";

	echo "$servers_list";
	#echo "<option SELECTED>$row[1]</option>\n";
	echo "</select>".helptag("conferences-server_ip")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Current Extension: </td><td align=left><input type=text name=extension size=20 maxlength=20 value=\"$row[2]\"></td></tr>\n";
	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";

	if ($LOG['ast_delete_phones'] > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=51111111111111&conf_exten=$conf_exten&server_ip=$server_ip\">DELETE THIS $t1 CONFERENCE</a>\n";
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=10000000000000 display all osdial conferences
######################
if ($ADD==10000000000000)
{
	$stmt="SELECT * from osdial_conferences order by conf_exten";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=$default_text size=+1>$t1 CONFERENCES</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>ID</td>\n";
echo "    <td>SERVER</td>\n";
echo "    <td>EXTENSION</td>\n";
echo "    <td align=center colspan=3>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($phones_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) ." class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31111111111111&conf_exten=$row[0]&server_ip=$row[1]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31111111111111&conf_exten=$row[0]&server_ip=$row[1]\">$row[0]</a></td>\n";
		echo "    <td>$row[1]</td>\n";
		echo "    <td>$row[2]</td>\n";
		echo "    <td colspan=3 align=center><a href=\"$PHP_SELF?ADD=31111111111111&conf_exten=$row[0]&server_ip=$row[1]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=6></td>\n";
echo "  </tr>\n";
echo "</table></center>\n";
}




?>
