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



######################
# ADD=111111111 display the ADD NEW CALL TIME SCREEN
######################

if ($ADD==111111111)
{
	if ($LOG['modify_call_times']==1)
	{
	echo "<center><br><font color=$default_text size=+1>ADD NEW CALL TIME</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=211111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
// 	echo "<tr bgcolor=$oddrows><td align=right width=30%>Call Time ID: </td><td align=left><input type=text name=call_time_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_call_times-call_time_id$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Call Time Name: </td><td align=left><input type=text name=call_time_name size=30 maxlength=30> (short description of the call time)$NWB#osdial_call_times-call_time_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Call Time Comments: </td><td align=left><input type=text name=call_time_comments size=50 maxlength=255> $NWB#osdial_call_times-call_time_comments$NWE</td></tr>\n";

	echo "<tr bgcolor=$oddrows><td align=center colspan=2>Day and time options will appear once you have created the Call Time Definition</td></tr>\n";
	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=211111111 adds new call time definition to the system
######################

if ($ADD==211111111)
{
	$stmt=sprintf("SELECT count(*) FROM osdial_call_times WHERE call_time_id='%s';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>CALL TIME DEFINITION NOT ADDED - there is already a call time entry with this ID</font>\n";}
	else
		{
		 if ( (OSDstrlen($call_time_id) < 2) or (OSDstrlen($call_time_name) < 2) )
			{
			 echo "<br><font color=red>CALL TIME DEFINITION NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Call Time ID and name must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt=sprintf("INSERT INTO osdial_call_times SET call_time_id='%s',call_time_name='%s',call_time_comments='%s';",mres($call_time_id),mres($call_time_name),mres($call_time_comments));
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=$default_text>CALL TIME ADDED: $call_time_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW CALL TIME ENTRY      |$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=311111111;
}



######################
# ADD=411111111 modify call time in the system
######################

if ($ADD==411111111)
{
	if ($LOG['modify_call_times']==1)
	{
	 if ( (OSDstrlen($call_time_id) < 2) or (OSDstrlen($call_time_name) < 2) )
		{
		 echo "<br><font color=red>CALL TIME NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID and name must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$ct_default_start = OSDpreg_replace('/\D/', '', $ct_default_start);
		$ct_default_stop = OSDpreg_replace('/\D/', '', $ct_default_stop);
		$ct_sunday_start = OSDpreg_replace('/\D/', '', $ct_sunday_start);
		$ct_sunday_stop = OSDpreg_replace('/\D/', '', $ct_sunday_stop);
		$ct_monday_start = OSDpreg_replace('/\D/', '', $ct_monday_start);
		$ct_monday_stop = OSDpreg_replace('/\D/', '', $ct_monday_stop);
		$ct_tuesday_start = OSDpreg_replace('/\D/', '', $ct_tuesday_start);
		$ct_tuesday_stop = OSDpreg_replace('/\D/', '', $ct_tuesday_stop);
		$ct_wednesday_start = OSDpreg_replace('/\D/', '', $ct_wednesday_start);
		$ct_wednesday_stop = OSDpreg_replace('/\D/', '', $ct_wednesday_stop);
		$ct_thursday_start = OSDpreg_replace('/\D/', '', $ct_thursday_start);
		$ct_thursday_stop = OSDpreg_replace('/\D/', '', $ct_thursday_stop);
		$ct_friday_start = OSDpreg_replace('/\D/', '', $ct_friday_start);
		$ct_friday_stop = OSDpreg_replace('/\D/', '', $ct_friday_stop);
		$ct_saturday_start = OSDpreg_replace('/\D/', '', $ct_saturday_start);
		$ct_saturday_stop = OSDpreg_replace('/\D/', '', $ct_saturday_stop);
		$stmt="UPDATE osdial_call_times set call_time_name='$call_time_name', call_time_comments='$call_time_comments', ct_default_start='$ct_default_start', ct_default_stop='$ct_default_stop', ct_sunday_start='$ct_sunday_start', ct_sunday_stop='$ct_sunday_stop', ct_monday_start='$ct_monday_start', ct_monday_stop='$ct_monday_stop', ct_tuesday_start='$ct_tuesday_start', ct_tuesday_stop='$ct_tuesday_stop', ct_wednesday_start='$ct_wednesday_start', ct_wednesday_stop='$ct_wednesday_stop', ct_thursday_start='$ct_thursday_start', ct_thursday_stop='$ct_thursday_stop', ct_friday_start='$ct_friday_start', ct_friday_stop='$ct_friday_stop', ct_saturday_start='$ct_saturday_start', ct_saturday_stop='$ct_saturday_stop',use_recycle_gap='$use_recycle_gap' where call_time_id='$call_time_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=$default_text>CALL TIME MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY CALL TIME ENTRY      |$stmt|\n");
			fclose($fp);
			}
		}
    $ADD=311111111;	# go to call time modification form below
	}
	else
	{
	echo "<font color=$default_text>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=511111111 confirmation before deletion of call time record
######################

if ($ADD==511111111)
{
	 if ( (OSDstrlen($call_time_id) < 2) or ($LOG['delete_call_times'] < 1) )
		{
		 echo "<br><font color=red>CALL TIME NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID must be at least 2 characters in length</font>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>CALL TIME DELETION CONFIRMATION: $call_time_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=611111111&call_time_id=$call_time_id&CoNfIrM=YES\">Click here to delete call time $call_time_id</a></font><br><br><br>\n";
		}

$ADD='311111111';		# go to call time modification below
}


######################
# ADD=611111111 delete call times record
######################

if ($ADD==611111111)
{
	 if ( (OSDstrlen($call_time_id) < 2) or ($LOG['delete_call_times'] < 1) )
		{
		 echo "<br><font color=red>CALL TIME NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt=sprintf("DELETE FROM osdial_call_times WHERE call_time_id='%s' LIMIT 1;",mres($call_time_id));
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING CALL TIME!|$PHP_AUTH_USER|$ip|call_time_id='$call_time_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=$default_text>CALL TIME DELETION COMPLETED: $call_time_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='100000000';		# go to call times list
}



######################
# ADD=321111111 modify call time definition info in the system
######################

if ($ADD==321111111)
{
if ($LOG['modify_call_times']==1)
{

if ( ($stage=="ADD") and (OSDstrlen($state_rule)>0) )
	{
	$stmt=sprintf("SELECT ct_state_call_times FROM osdial_call_times WHERE call_time_id='%s';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$ct_state_call_times = $row[0];

	if (OSDpreg_match("/\|$/",$ct_state_call_times))
		{$ct_state_call_times = "$ct_state_call_times$state_rule\|";}
	else
		{$ct_state_call_times = "$ct_state_call_times\|$state_rule\|";}
	$stmt=sprintf("UPDATE osdial_call_times SET ct_state_call_times='%s' WHERE call_time_id='%s';",mres($ct_state_call_times),mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	echo "State Rule Added: $state_rule<BR>\n";
	}
if ( ($stage=="REMOVE") and (OSDstrlen($state_rule)>0) )
	{
	$stmt=sprintf("SELECT ct_state_call_times FROM osdial_call_times WHERE call_time_id='$call_time_id';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$ct_state_call_times = $row[0];

	$ct_state_call_times = OSDpreg_replace("/\|$state_rule\|/",'|',$ct_state_call_times);
	$stmt=sprintf("UPDATE osdial_call_times SET ct_state_call_times='%s' WHERE call_time_id='%s';",mres($ct_state_call_times),mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	echo "State Rule Removed: $state_rule<BR>\n";
	}

$ADD=311111111;
}
else
{
echo "<font color=red>You are not authorized to view this page. Please go back.</font>";
}

}


######################
# ADD=311111111 modify call time definition info in the system
######################

if ($ADD==311111111)
{

if ($LOG['modify_call_times']==1)
{
	$stmt=sprintf("SELECT * FROM osdial_call_times WHERE call_time_id='$call_time_id';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$call_time_name =		$row[1];
	$call_time_comments =	$row[2];
	$ct_default_start =		$row[3];
	$ct_default_stop =		$row[4];
	$ct_sunday_start =		$row[5];
	$ct_sunday_stop =		$row[6];
	$ct_monday_start =		$row[7];
	$ct_monday_stop =		$row[8];
	$ct_tuesday_start =		$row[9];
	$ct_tuesday_stop =		$row[10];
	$ct_wednesday_start =	$row[11];
	$ct_wednesday_stop =	$row[12];
	$ct_thursday_start =	$row[13];
	$ct_thursday_stop =		$row[14];
	$ct_friday_start =		$row[15];
	$ct_friday_stop =		$row[16];
	$ct_saturday_start =	$row[17];
	$ct_saturday_stop =		$row[18];
	$ct_state_call_times =	$row[19];
	$use_recycle_gap =	    $row[20];

echo "<center><br><font class=top_header color=$default_text size=+1>MODIFY A CALL TIME</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=411111111>\n";
echo "<input type=hidden name=call_time_id value=\"$call_time_id\">\n";
echo "<table cellspacing=1>";
echo "<tr bgcolor=$oddrows><td align=right>Call Time ID: </td><td align=left colspan=3><B>$call_time_id</B>$NWB#osdial_call_times-call_time_id$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Call Time Name: </td><td align=left colspan=3><input type=text name=call_time_name size=40 maxlength=50 value=\"$call_time_name\"> (short description of the call time)$NWB#osdial_call_times-call_time_name$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Call Time Comments: </td><td align=left colspan=3><input type=text name=call_time_comments size=50 maxlength=255 value=\"$call_time_comments\"> $NWB#osdial_call_times-call_time_comments$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Default Start:</td><td align=left><input type=text name=ct_default_start size=5 maxlength=4 value=\"$ct_default_start\"> </td><td align=right>Default Stop:</td><td align=left><input type=text name=ct_default_stop size=5 maxlength=4 value=\"$ct_default_stop\"> $NWB#osdial_call_times-ct_default_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Sunday Start:</td><td align=left><input type=text name=ct_sunday_start size=5 maxlength=4 value=\"$ct_sunday_start\"> </td><td align=right>Sunday Stop:</td><td align=left><input type=text name=ct_sunday_stop size=5 maxlength=4 value=\"$ct_sunday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Monday Start:</td><td align=left><input type=text name=ct_monday_start size=5 maxlength=4 value=\"$ct_monday_start\"> </td><td align=right>Monday Stop:</td><td align=left><input type=text name=ct_monday_stop size=5 maxlength=4 value=\"$ct_monday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Tuesday Start:</td><td align=left><input type=text name=ct_tuesday_start size=5 maxlength=4 value=\"$ct_tuesday_start\"> </td><td align=right>Tuesday Stop:</td><td align=left><input type=text name=ct_tuesday_stop size=5 maxlength=4 value=\"$ct_tuesday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Wednesday Start:</td><td align=left><input type=text name=ct_wednesday_start size=5 maxlength=4 value=\"$ct_wednesday_start\"> </td><td align=right>Wednesday Stop:</td><td align=left><input type=text name=ct_wednesday_stop size=5 maxlength=4 value=\"$ct_wednesday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Thursday Start:</td><td align=left><input type=text name=ct_thursday_start size=5 maxlength=4 value=\"$ct_thursday_start\"> </td><td align=right>Thursday Stop:</td><td align=left><input type=text name=ct_thursday_stop size=5 maxlength=4 value=\"$ct_thursday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Friday Start:</td><td align=left><input type=text name=ct_friday_start size=5 maxlength=4 value=\"$ct_friday_start\"> </td><td align=right>Friday Stop:</td><td align=left><input type=text name=ct_friday_stop size=5 maxlength=4 value=\"$ct_friday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Saturday Start:</td><td align=left><input type=text name=ct_saturday_start size=5 maxlength=4 value=\"$ct_saturday_start\"> </td><td align=right>Saturday Stop:</td><td align=left><input type=text name=ct_saturday_stop size=5 maxlength=4 value=\"$ct_saturday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Add No-Call Periods to Recylce Delay: </td><td align=left colspan=3><select name=use_recycle_gap><option>N</option><option>Y</option><option>$use_recycle_gap</option></select>$NWB#osdial_call_times-use_recycle_gap$NWE</td></tr>\n";
echo "<tr class=tabfooter><td align=center class=tabbutton colspan=4><input type=submit name=SUBMIT value=SUBMIT></FORM></td></tr>\n";
echo "</table><br><br>\n";

$ct_srs=1;
$b=0;
$srs_SQL ='';
if (OSDstrlen($ct_state_call_times)>2)
	{
	$state_rules = explode('|',$ct_state_call_times);
	$ct_srs = ((count($state_rules)) - 1);
	}
echo "<table bgcolor=grey width=500 cellspacing=1 bgcolor=grey>\n";
echo "  <tr class=tabheader>\n";
echo "    <td align=center colspan=3>ACTIVE STATE CALL TIME DEFINITIONS</td>\n";
echo "  </tr>\n";
echo "  <tr class=tabheader>\n";
echo "    <td align=center>STATE CALL-TIME ID</td>\n";
echo "    <td align=center>DESCRIPTION</td>\n";
echo "    <td align=center>ACTION</td>\n";
echo "  </tr>\n";
$o=0;
while($ct_srs >= $b) {
	if (OSDstrlen($state_rules[$b])>0) {
		$stmt=sprintf("SELECT state_call_time_state,state_call_time_name FROM osdial_state_call_times WHERE state_call_time_id='%s';",mres($state_rules[$b]));
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		echo "  <tr class=\"row font1\" " . bgcolor($o) . " ondblclick=\"openNewWindow('$PHP_SELF?ADD=3111111111&call_time_id=$state_rules[$b]');\">\n";
        echo "    <td align=left nowrap><a href=\"$PHP_SELF?ADD=3111111111&call_time_id=$state_rules[$b]\">$state_rules[$b]</a></td>\n";
        echo "    <td align=left nowrap>$row[0] - $row[1]</td>\n";
        echo "    <td align=center nowrap><a href=\"$PHP_SELF?ADD=321111111&call_time_id=$call_time_id&state_rule=$state_rules[$b]&stage=REMOVE\">REMOVE</a></td>\n";
        echo "  </tr>\n";
		$srs_SQL .= "'$state_rules[$b]',";
		$srs_state_SQL .= "'$row[0]',";
        $o++;
	}
	$b++;
}
if (OSDstrlen($srs_SQL)>2)
	{
	$srs_SQL = "$srs_SQL''";
	$srs_state_SQL = "$srs_state_SQL''";
	$srs_SQL = "where state_call_time_id NOT IN($srs_SQL) and state_call_time_state NOT IN($srs_state_SQL)";
	}
else
	{$srs_SQL='';}
$stmt="SELECT state_call_time_id,state_call_time_name FROM osdial_state_call_times $srs_SQL ORDER BY state_call_time_id;";
$rslt=mysql_query($stmt, $link);
$sct_to_print = mysql_num_rows($rslt);
$sct_list='';

$o=0;
while ($sct_to_print > $o) {
	$rowx=mysql_fetch_row($rslt);
	$sct_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
	$o++;
}
echo "<form action=$PHP_SELF method=POST>\n";
echo "<input type=hidden name=ADD value=321111111>\n";
echo "<input type=hidden name=stage value=\"ADD\">\n";
echo "<input type=hidden name=call_time_id value=\"$call_time_id\">\n";
echo "  <tr class=tabfooter>\n";
echo "    <td align=center colspan=2 class=tabinput><select size=1 name=state_rule>$sct_list</select></td>\n";
echo "    <td align=center class=tabbutton1><input type=submit name=SUBMIT value=ADD></td>\n";
echo "  </tr>\n";
echo "  </form>\n";

echo "</table><br><br><br>\n";
echo "<font class=top_header2 color=$default_text size=+1>CAMPAIGNS USING THIS CALL TIME</font><br>\n";
echo "<table width=500 cellspacing=1 bgcolor=grey>\n";
echo "  <tr class=tabheader>\n";
echo "    <td align=center>Campaign ID</td>\n";
echo "    <td align=center>Campaign Description</td>\n";
echo "  </tr>\n";

	$stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE local_call_time='%s';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$camps_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($camps_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=31&campaign_id=$row[0]');\">\n";
        echo "    <td nowrap><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[0]\">$row[0]</a></td>\n";
        echo "    <td nowrap>$row[1]</td>\n";
        echo "  </tr>\n";
		$o++;
	}
echo "  <tr class=tabfooter>\n";
echo "    <td colspan=2></td>\n";
echo "  </tr>\n";
echo "</table>\n";



echo "<br><br><font class=top_header color=$default_text size=+1>INBOUND GROUPS USING THIS CALL TIME</font><br>\n";
echo "<table width=500 cellspacing=1 bgcolor=grey>\n";
echo "  <tr class=tabheader>\n";
echo "    <td align=center>InGroup ID</td>\n";
echo "    <td align=center>InGroup Description</td>\n";
echo "  </tr>\n";

	$stmt=sprintf("SELECT group_id,group_name FROM osdial_inbound_groups WHERE call_time_id='%s';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$camps_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($camps_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=3111&group_id=$row[0]');\">\n";
        echo "    <td nowrap><a href=\"$PHP_SELF?ADD=3111&group_id=$row[0]\">$row[0]</a></td>\n";
        echo "    <td nowrap>$row[1]</td>\n";
        echo "  </tr>\n";
		$o++;
	}
echo "  <tr class=tabfooter>\n";
echo "    <td colspan=2></td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "</center><br><br>\n";

if ($LOG['delete_call_times'] > 0)
	{
	echo "<br><br><a href=\"$PHP_SELF?ADD=511111111&call_time_id=$call_time_id\">DELETE THIS CALL TIME DEFINITION</a>\n";
	}
}
else
{
echo "<font color=red>You are not authorized to view this page. Please go back.</font>";
}

}


######################
# ADD=100000000 display all call times
######################
if ($ADD==100000000)
{
	$stmt="SELECT * FROM osdial_call_times ORDER BY call_time_id";
	$rslt=mysql_query($stmt, $link);
	$filters_to_print = mysql_num_rows($rslt);

echo "<center><br><font class=top_header color=$default_text size=+1>CALL TIMES</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>ID</td>\n";
echo "    <td>NAME</td>\n";
echo "    <td align=center>START</td>\n";
echo "    <td align=center>STOP</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($filters_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=311111111&call_time_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$row[0]\">$row[0]</a></td>\n";
		echo "    <td>$row[1]</td>\n";
		echo "    <td align=center>$row[3] </td>";
		echo "    <td align=center>$row[4] </td>";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=5></td>\n";
echo "  </tr>\n";
echo "</table></center>\n";
}



#### State Call Times.


######################
# ADD=1111111111 display the ADD NEW STATE CALL TIME SCREEN
######################

if ($ADD==1111111111)
{
	if ($LOG['modify_call_times']==1)
	{
	echo "<center><br><font color=$default_text size=+1>ADD NEW STATE CALL TIME</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2111111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right width=30%>State Call Time ID: </td><td align=left><input type=text name=call_time_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_call_times-call_time_id$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>State Call Time State: </td><td align=left><input type=text name=state_call_time_state size=4 maxlength=2> (no spaces or punctuation)$NWB#osdial_call_times-state_call_time_state$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>State Call Time Name: </td><td align=left><input type=text name=call_time_name size=30 maxlength=30> (short description of the call time)$NWB#osdial_call_times-call_time_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>State Call Time Comments: </td><td align=left><input type=text name=call_time_comments size=50 maxlength=255> $NWB#osdial_call_times-call_time_comments$NWE</td></tr>\n";

	echo "<tr bgcolor=$oddrows><td align=center colspan=2>Day and time options will appear once you have created the Call Time Definition</td></tr>\n";
	echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



######################
# ADD=2111111111 adds new state call time definition to the system
######################

if ($ADD==2111111111)
{
	$stmt=sprintf("SELECT count(*) FROM osdial_state_call_times WHERE state_call_time_id='%s';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>STATE CALL TIME DEFINITION NOT ADDED - there is already a call time entry with this ID</font>\n";}
	else
		{
		 if ( (OSDstrlen($call_time_id) < 2) or (OSDstrlen($call_time_name) < 2) or (OSDstrlen($state_call_time_state) < 2) )
			{
			 echo "<br><font color=red>STATE CALL TIME DEFINITION NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>State Call Time ID, name and state must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt=sprintf("INSERT INTO osdial_state_call_times SET state_call_time_id='%s',state_call_time_name='%s',state_call_time_comments='%s',state_call_time_state='%s';",mres($call_time_id),mres($call_time_name),mres($call_time_comments),mres($state_call_time_state));
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=$default_text>STATE CALL TIME ADDED: $call_time_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW STATE CALL TIME ENTRY|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=3111111111;
}



######################
# ADD=4111111111 modify state call time in the system
######################

if ($ADD==4111111111)
{
	if ($LOG['modify_call_times']==1)
	{
	 if ( (OSDstrlen($call_time_id) < 2) or (OSDstrlen($call_time_name) < 2) or (OSDstrlen($state_call_time_state) < 2) )
		{
		 echo "<br><font color=red>STATE CALL TIME NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>State Call Time ID, name and state must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$ct_default_start = OSDpreg_replace('/\D/', '', $ct_default_start);
		$ct_default_stop = OSDpreg_replace('/\D/', '', $ct_default_stop);
		$ct_sunday_start = OSDpreg_replace('/\D/', '', $ct_sunday_start);
		$ct_sunday_stop = OSDpreg_replace('/\D/', '', $ct_sunday_stop);
		$ct_monday_start = OSDpreg_replace('/\D/', '', $ct_monday_start);
		$ct_monday_stop = OSDpreg_replace('/\D/', '', $ct_monday_stop);
		$ct_tuesday_start = OSDpreg_replace('/\D/', '', $ct_tuesday_start);
		$ct_tuesday_stop = OSDpreg_replace('/\D/', '', $ct_tuesday_stop);
		$ct_wednesday_start = OSDpreg_replace('/\D/', '', $ct_wednesday_start);
		$ct_wednesday_stop = OSDpreg_replace('/\D/', '', $ct_wednesday_stop);
		$ct_thursday_start = OSDpreg_replace('/\D/', '', $ct_thursday_start);
		$ct_thursday_stop = OSDpreg_replace('/\D/', '', $ct_thursday_stop);
		$ct_friday_start = OSDpreg_replace('/\D/', '', $ct_friday_start);
		$ct_friday_stop = OSDpreg_replace('/\D/', '', $ct_friday_stop);
		$ct_saturday_start = OSDpreg_replace('/\D/', '', $ct_saturday_start);
		$ct_saturday_stop = OSDpreg_replace('/\D/', '', $ct_saturday_stop);
		$stmt=sprintf("UPDATE osdial_state_call_times SET state_call_time_name='%s',state_call_time_comments='%s',sct_default_start='%s',sct_default_stop='%s',sct_sunday_start='%s',sct_sunday_stop='%s',sct_monday_start='%s',sct_monday_stop='%s',sct_tuesday_start='%s',sct_tuesday_stop='%s',sct_wednesday_start='%s',sct_wednesday_stop='%s',sct_thursday_start='%s',sct_thursday_stop='%s',sct_friday_start='%s',sct_friday_stop='%s',sct_saturday_start='%s',sct_saturday_stop='%s',state_call_time_state='%s'  WHERE state_call_time_id='%s';",mres($call_time_name),mres($call_time_comments),mres($ct_default_start),mres($ct_default_stop),mres($ct_sunday_start),mres($ct_sunday_stop),mres($ct_monday_start),mres($ct_monday_stop),mres($ct_tuesday_start),mres($ct_tuesday_stop),mres($ct_wednesday_start),mres($ct_wednesday_stop),mres($ct_thursday_start),mres($ct_thursday_stop),mres($ct_friday_start),mres($ct_friday_stop),mres($ct_saturday_start),mres($ct_saturday_stop),mres($state_call_time_state),mres($call_time_id));
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=$default_text>STATE CALL TIME MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY STATE CALL TIME ENTRY|$stmt|\n");
			fclose($fp);
			}
		}
    $ADD=3111111111;	# go to state call time modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=5111111111 confirmation before deletion of state call time record
######################

if ($ADD==5111111111)
{
	 if ( (OSDstrlen($call_time_id) < 2) or ($LOG['delete_call_times'] < 1) )
		{
		 echo "<br><font color=red>STATE CALL TIME NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>STATE CALL TIME DELETION CONFIRMATION: $call_time_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6111111111&call_time_id=$call_time_id&CoNfIrM=YES\">Click here to delete state call time $call_time_id</a></font><br><br><br>\n";
		}

$ADD='3111111111';		# go to state call time modification below
}



######################
# ADD=6111111111 delete state call times record
######################

if ($ADD==6111111111)
{
	 if ( (OSDstrlen($call_time_id) < 2) or ($LOG['delete_call_times'] < 1) )
		{
		 echo "<br><font color=red>STATE CALL TIME NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Call Time ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt=sprintf("DELETE FROM osdial_state_call_times WHERE state_call_time_id='%s' LIMIT 1;",mres($call_time_id));
		$rslt=mysql_query($stmt, $link);

		$stmt=sprintf("SELECT call_time_id,ct_state_call_times FROM osdial_call_times WHERE ct_state_call_times LIKE '%%|%s|%%' ORDER BY call_time_id;",mres($call_time_id));
		$rslt=mysql_query($stmt, $link);
		$sct_to_print = mysql_num_rows($rslt);
		$sct_list='';

		$o=0;
		while ($sct_to_print > $o) {
			$rowx=mysql_fetch_row($rslt);
			$sct_ids[$o] = "$rowx[0]";
			$sct_states[$o] = "$rowx[1]";
			$o++;
		}
		$o=0;

		while ($sct_to_print > $o) {
			$sct_states[$o] = OSDpreg_replace("/\|$call_time_id\|/",'|',$sct_states[$o]);
			$stmt="UPDATE osdial_call_times set ct_state_call_times='$sct_states[$o]' where call_time_id='$sct_ids[$o]';";
			$rslt=mysql_query($stmt, $link);
			echo "$stmt\n";
			echo "State Rule Removed: $sct_ids[$o]<BR>\n";
			$o++;
		}

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING CALL TIME!|$PHP_AUTH_USER|$ip|state_call_time_id='$call_time_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=$default_text>STATE CALL TIME DELETION COMPLETED: $call_time_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='1000000000';		# go to call times list
}



######################
# ADD=3111111111 modify state call time definition info in the system
######################

if ($ADD==3111111111)
{

if ($LOG['modify_call_times']==1)
{
	$stmt=sprintf("SELECT * FROM osdial_state_call_times WHERE state_call_time_id='%s';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$state_call_time_state =$row[1];
	$call_time_name =		$row[2];
	$call_time_comments =	$row[3];
	$ct_default_start =		$row[4];
	$ct_default_stop =		$row[5];
	$ct_sunday_start =		$row[6];
	$ct_sunday_stop =		$row[7];
	$ct_monday_start =		$row[8];
	$ct_monday_stop =		$row[9];
	$ct_tuesday_start =		$row[10];
	$ct_tuesday_stop =		$row[11];
	$ct_wednesday_start =	$row[12];
	$ct_wednesday_stop =	$row[13];
	$ct_thursday_start =	$row[14];
	$ct_thursday_stop =		$row[15];
	$ct_friday_start =		$row[16];
	$ct_friday_stop =		$row[17];
	$ct_saturday_start =	$row[18];
	$ct_saturday_stop =		$row[19];

echo "<center><br><font color=$default_text size=+1>MODIFY A STATE CALL TIME</font><form action=$PHP_SELF method=POST><br><br>\n";
echo "<input type=hidden name=ADD value=4111111111>\n";
echo "<input type=hidden name=call_time_id value=\"$call_time_id\">\n";
echo "<TABLE>";
echo "<tr bgcolor=$oddrows><td align=right>Call Time ID: </td><td align=left colspan=3><B>$call_time_id</B>$NWB#osdial_call_times-call_time_id$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>State Call Time State: </td><td align=left colspan=3><input type=text name=state_call_time_state size=4 maxlength=2 value=\"$state_call_time_state\"> $NWB#osdial_call_times-state_call_time_state$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>State Call Time Name: </td><td align=left colspan=3><input type=text name=call_time_name size=40 maxlength=50 value=\"$call_time_name\"> (short description of the call time)$NWB#osdial_call_times-call_time_name$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>State Call Time Comments: </td><td align=left colspan=3><input type=text name=call_time_comments size=50 maxlength=255 value=\"$call_time_comments\"> $NWB#osdial_call_times-call_time_comments$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Default Start:</td><td align=left><input type=text name=ct_default_start size=5 maxlength=4 value=\"$ct_default_start\"> </td><td align=right>Default Stop:</td><td align=left><input type=text name=ct_default_stop size=5 maxlength=4 value=\"$ct_default_stop\"> $NWB#osdial_call_times-ct_default_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Sunday Start:</td><td align=left><input type=text name=ct_sunday_start size=5 maxlength=4 value=\"$ct_sunday_start\"> </td><td align=right>Sunday Stop:</td><td align=left><input type=text name=ct_sunday_stop size=5 maxlength=4 value=\"$ct_sunday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Monday Start:</td><td align=left><input type=text name=ct_monday_start size=5 maxlength=4 value=\"$ct_monday_start\"> </td><td align=right>Monday Stop:</td><td align=left><input type=text name=ct_monday_stop size=5 maxlength=4 value=\"$ct_monday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Tuesday Start:</td><td align=left><input type=text name=ct_tuesday_start size=5 maxlength=4 value=\"$ct_tuesday_start\"> </td><td align=right>Tuesday Stop:</td><td align=left><input type=text name=ct_tuesday_stop size=5 maxlength=4 value=\"$ct_tuesday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Wednesday Start:</td><td align=left><input type=text name=ct_wednesday_start size=5 maxlength=4 value=\"$ct_wednesday_start\"> </td><td align=right>Wednesday Stop:</td><td align=left><input type=text name=ct_wednesday_stop size=5 maxlength=4 value=\"$ct_wednesday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Thursday Start:</td><td align=left><input type=text name=ct_thursday_start size=5 maxlength=4 value=\"$ct_thursday_start\"> </td><td align=right>Thursday Stop:</td><td align=left><input type=text name=ct_thursday_stop size=5 maxlength=4 value=\"$ct_thursday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Friday Start:</td><td align=left><input type=text name=ct_friday_start size=5 maxlength=4 value=\"$ct_friday_start\"> </td><td align=right>Friday Stop:</td><td align=left><input type=text name=ct_friday_stop size=5 maxlength=4 value=\"$ct_friday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";
echo "<tr bgcolor=$oddrows><td align=right>Saturday Start:</td><td align=left><input type=text name=ct_saturday_start size=5 maxlength=4 value=\"$ct_saturday_start\"> </td><td align=right>Saturday Stop:</td><td align=left><input type=text name=ct_saturday_stop size=5 maxlength=4 value=\"$ct_saturday_stop\"> $NWB#osdial_call_times-ct_sunday_start$NWE</td></tr>\n";

echo "<tr class=tabfooter><td align=center colspan=4 class=tabbutton><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
echo "</TABLE><BR><BR>\n";
echo "<BR><font color=$default_text size=+1>CALL TIMES USING THIS STATE CALL TIME</font><BR>\n";
echo "<table width=500 cellspacing=1 bgcolor=grey>\n";
echo "  <tr class=tabheader>\n";
echo "    <td align=center>CALL-TIME ID</td>\n";
echo "    <td align=center>DESCRIPTION</td>\n";
echo "  </tr>\n";

	$stmt=sprintf("SELECT call_time_id,call_time_name FROM osdial_call_times WHERE ct_state_call_times LIKE '%%|%s|%%';",mres($call_time_id));
	$rslt=mysql_query($stmt, $link);
	$camps_to_print = mysql_num_rows($rslt);
	$o=0;
	while ($camps_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=311111111&call_time_id=$row[0]');\">\n";
        echo "    <td align=center><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$row[0]\">$row[0]</a></font></td>\n";
        echo "    <td nowrap>$row[1]</font></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=2></td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "</center><BR><BR><br>\n";

if ($LOG['delete_call_times'] > 0)
	{
	echo "<br><br><a href=\"$PHP_SELF?ADD=5111111111&call_time_id=$call_time_id\">DELETE THIS STATE CALL TIME DEFINITION</a>\n";
	}

}
else
{
echo "<font color=red>You are not authorized to view this page. Please go back.</font>";
}

}



######################
# ADD=1000000000 display all state call times
######################
if ($ADD==1000000000)
{
	$stmt="SELECT * FROM osdial_state_call_times ORDER BY state_call_time_id;";
	$rslt=mysql_query($stmt, $link);
	$filters_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=$default_text size=+1>STATE CALL TIMES</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>";
echo "    <td>ID</td>\n";
echo "    <td align=center>STATE</td>\n";
echo "    <td>NAME</td>\n";
echo "    <td>COMMENT</td>\n";
echo "    <td align=center>START</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($filters_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3111111111&call_time_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=3111111111&call_time_id=$row[0]\">$row[0]</a></td>\n";
		echo "    <td align=center>$row[1]</td>\n";
		echo "    <td>$row[2]</td>\n";
		echo "    <td>$row[3]</td>\n";
		echo "    <td align=center>$row[4]</td>\n";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=3111111111&call_time_id=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=6></td>\n";
echo "  </tr>\n";
echo "</table></center>\n";
}






?>
