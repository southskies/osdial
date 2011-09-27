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
# ADD=11111111 display the ADD NEW FILTER SCREEN
######################

if ($ADD==11111111)
{
	if ($LOG['modify_filters']==1)
	{
	echo "<center><br><font color=$default_text size=+1>ADD NEW FILTER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Filter ID: </td><td align=left>";
    if ($LOG['multicomp_admin'] > 0) {
        $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
        echo "<select name=company_id>\n";
        foreach ($comps as $comp) {
            echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
        }
        echo "</select>\n";
    } elseif ($LOG['multicomp']>0) {
        echo "<input type=hidden name=company_id value=$LOG[company_id]>";
        #echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
    }
    echo "<input type=text name=lead_filter_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_lead_filters-lead_filter_id$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Filter Name: </td><td align=left><input type=text name=lead_filter_name size=30 maxlength=30> (short description of the filter)$NWB#osdial_lead_filters-lead_filter_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Filter Comments: </td><td align=left><input type=text name=lead_filter_comments size=50 maxlength=255> $NWB#osdial_lead_filters-lead_filter_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Filter SQL: </td><td align=left><TEXTAREA NAME=lead_filter_sql ROWS=20 COLS=50 value=\"\"></TEXTAREA> $NWB#osdial_lead_filters-lead_filter_sql$NWE</td></tr>\n";
	echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=21111111 adds new filter to the system
######################

if ($ADD==21111111)
{
    $prelead_filter_id = $lead_filter_id;
    if ($LOG['multicomp'] > 0) $prelead_filter_id = (($company_id * 1) + 100) . $lead_filter_id;
	$stmt=sprintf("SELECT count(*) FROM osdial_lead_filters WHERE lead_filter_id='%s';",mres($prelead_filter_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>FILTER NOT ADDED - there is already a filter entry with this ID</font>\n";}
	else
		{
		 if ( (OSDstrlen($lead_filter_id) < 2) or (OSDstrlen($lead_filter_name) < 2) or (OSDstrlen($lead_filter_sql) < 2) )
			{
			 echo "<br><font color=red>FILTER NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Filter ID, name and SQL must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
            if ($LOG['multicomp'] > 0) $lead_filter_id = (($company_id * 1) + 100) . $lead_filter_id;
			$stmt=sprintf("INSERT INTO osdial_lead_filters SET lead_filter_id='%s',lead_filter_name='%s',lead_filter_comments='%s',lead_filter_sql='%s';",mres($lead_filter_id),mres($lead_filter_name),mres($lead_filter_comments),mres($lead_filter_sql));
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=$default_text>FILTER ADDED: $lead_filter_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW FILTER ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=10000000;
}


######################
# ADD=41111111 modify filter in the system
######################

if ($ADD==41111111)
{
	if ($LOG['modify_filters']==1)
	{
	 if ( (OSDstrlen($lead_filter_id) < 2) or (OSDstrlen($lead_filter_name) < 2) or (OSDstrlen($lead_filter_sql) < 2) )
		{
		 echo "<br><font color=red>FILTER NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID, name and SQL must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$lead_filter_sql = mysql_real_escape_string($lead_filter_sql);
		$stmt=sprintf("UPDATE osdial_lead_filters SET lead_filter_name='%s',lead_filter_comments='%s',lead_filter_sql='%s' WHERE lead_filter_id='%s';",mres($lead_filter_name),mres($lead_filter_comments),mres($lead_filter_sql),mres($lead_filter_id));
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=$default_text>FILTER MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY FILTER ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
$ADD=31111111;	# go to filter modification form below
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}

######################
# ADD=51111111 confirmation before deletion of filter record
######################

if ($ADD==51111111)
{
	 if ( (OSDstrlen($lead_filter_id) < 2) or ($LOG['delete_filters'] < 1) )
		{
		 echo "<br><font color=red>FILTER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=$default_text>FILTER DELETION CONFIRMATION: $lead_filter_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111111&lead_filter_id=$lead_filter_id&CoNfIrM=YES\">Click here to delete filter $lead_filter_id</a></font><br><br><br>\n";
		}

$ADD='31111111';		# go to filter modification below
}


######################
# ADD=61111111 delete filter record
######################

if ($ADD==61111111)
{
	 if ( (OSDstrlen($lead_filter_id) < 2) or ($CoNfIrM != 'YES') or ($LOG['delete_filters'] < 1) )
		{
		 echo "<br><font color=red>FILTER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt=sprintf("DELETE FROM osdial_lead_filters WHERE lead_filter_id='%s' LIMIT 1;",mres($lead_filter_id));
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING FILTER!!!!|$PHP_AUTH_USER|$ip|lead_filter_id='$lead_filter_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=$default_text>FILTER DELETION COMPLETED: $lead_filter_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='10000000';		# go to filter list
}



######################
# ADD=31111111 modify filter info in the system
######################

if ($ADD==31111111)
{
	if ($LOG['modify_filters']==1)
	{
	$stmt=sprintf("SELECT * FROM osdial_lead_filters WHERE lead_filter_id='%s';",mres($lead_filter_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$lead_filter_name =		$row[1];
	$lead_filter_comments =	$row[2];
	$lead_filter_sql =		$row[3];
	echo "<center><br><font color=$default_text size=+1>MODIFY A FILTER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111111>\n";
	echo "<input type=hidden name=lead_filter_id value=\"$lead_filter_id\">\n";
	echo "<TABLE>";
	echo "<tr bgcolor=$oddrows><td align=right>Filter ID: </td><td align=left><B>" . mclabel($lead_filter_id) . "</B>$NWB#osdial_lead_filters-lead_filter_id$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Filter Name: </td><td align=left><input type=text name=lead_filter_name size=40 maxlength=50 value=\"$lead_filter_name\"> (short description of the filter)$NWB#osdial_lead_filters-lead_filter_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Filter Comments: </td><td align=left><input type=text name=lead_filter_comments size=50 maxlength=255 value=\"$lead_filter_comments\"> $NWB#osdial_lead_filters-lead_filter_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Filter SQL:</td><td align=left><TEXTAREA NAME=lead_filter_sql ROWS=20 COLS=50>$lead_filter_sql</TEXTAREA> $NWB#osdial_lead_filters-lead_filter_sql$NWE</td></tr>\n";
	echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</table></form>\n";

		##### get campaigns listing for dynamic pulldown
		$stmt="SELECT campaign_id,campaign_name FROM osdial_campaigns ORDER BY campaign_id;";
		$rslt=mysql_query($stmt, $link);
		$campaigns_to_print = mysql_num_rows($rslt);
		$campaigns_list='';

		$o=0;
		while ($campaigns_to_print > $o)
			{
			$rowx=mysql_fetch_row($rslt);
			$campaigns_list .= "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
			$o++;
			}

	echo "<BR><BR>";
	echo "<center><br><font color=$default_text>TEST ON CAMPAIGN<form action=$PHP_SELF method=POST target=\"_blank\"><br>\n";
	echo "<input type=hidden name=lead_filter_id value=\"$lead_filter_id\">\n";
	echo "<input type=hidden name=ADD value=\"73\">\n";
	echo "<select size=1 name=campaign_id>\n";
	echo "$campaigns_list";
	echo "</select>\n";
	echo "<input type=submit name=SUBMIT value=SUBMIT></center>\n";
	echo "<br><br></center>";


	if ($LOG['delete_filters'] > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=51111111&lead_filter_id=$lead_filter_id\">DELETE THIS FILTER</a>\n";
		}
		echo "";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
	
}



######################
# ADD=10000000 display all filters
######################
if ($ADD==10000000)
{
	$stmt="SELECT * FROM osdial_lead_filters ORDER BY lead_filter_id;";
	$rslt=mysql_query($stmt, $link);
	$filters_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=$default_text size=+1>LEAD FILTERS</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>NAME</td>\n";
echo "    <td>DESCRIPTION</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($filters_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31111111&lead_filter_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$row[0]\">" . mclabel($row[0]) . "</a></td>\n";
		echo "    <td>$row[1]</td>\n";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=3></td>\n";
echo "  </tr>\n";
echo "</table></center>\n";
}


?>
