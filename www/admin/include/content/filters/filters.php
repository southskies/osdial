<?php

######################
# ADD=11111111 display the ADD NEW FILTER SCREEN
######################

if ($ADD==11111111)
{
	if ($LOGmodify_filters==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>ADD NEW FILTER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=21111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter ID: </td><td align=left><input type=text name=lead_filter_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_lead_filters-lead_filter_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter Name: </td><td align=left><input type=text name=lead_filter_name size=30 maxlength=30> (short description of the filter)$NWB#osdial_lead_filters-lead_filter_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter Comments: </td><td align=left><input type=text name=lead_filter_comments size=50 maxlength=255> $NWB#osdial_lead_filters-lead_filter_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter SQL: </td><td align=left><TEXTAREA NAME=lead_filter_sql ROWS=20 COLS=50 value=\"\"></TEXTAREA> $NWB#osdial_lead_filters-lead_filter_sql$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=21111111 adds new filter to the system
######################

if ($ADD==21111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from vicidial_lead_filters where lead_filter_id='$lead_filter_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>FILTER NOT ADDED - there is already a filter entry with this ID</font>\n";}
	else
		{
		 if ( (strlen($lead_filter_id) < 2) or (strlen($lead_filter_name) < 2) or (strlen($lead_filter_sql) < 2) )
			{
			 echo "<br><font color=red>FILTER NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Filter ID, name and SQL must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
			$stmt="INSERT INTO vicidial_lead_filters SET lead_filter_id='$lead_filter_id',lead_filter_name='$lead_filter_name',lead_filter_comments='$lead_filter_comments',lead_filter_sql='$lead_filter_sql';";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=navy>FILTER ADDED: $lead_filter_id</font></B>\n";

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
	if ($LOGmodify_filters==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($lead_filter_id) < 2) or (strlen($lead_filter_name) < 2) or (strlen($lead_filter_sql) < 2) )
		{
		 echo "<br><font color=red>FILTER NOT MODIFIED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID, name and SQL must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="UPDATE vicidial_lead_filters set lead_filter_name='$lead_filter_name', lead_filter_comments='$lead_filter_comments', lead_filter_sql='$lead_filter_sql' where lead_filter_id='$lead_filter_id';";
		$rslt=mysql_query($stmt, $link);

		echo "<br><B><font color=navy>FILTER MODIFIED</font></B>\n";

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|MODIFY FILTER ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
			fclose($fp);
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
$ADD=31111111;	# go to filter modification form below
}

######################
# ADD=51111111 confirmation before deletion of filter record
######################

if ($ADD==51111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($lead_filter_id) < 2) or ($LOGdelete_filters < 1) )
		{
		 echo "<br><font color=red>FILTER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		echo "<br><B><font color=navy>FILTER DELETION CONFIRMATION: $lead_filter_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=61111111&lead_filter_id=$lead_filter_id&CoNfIrM=YES\">Click here to delete filter $lead_filter_id</a></font><br><br><br>\n";
		}

$ADD='31111111';		# go to filter modification below
}


######################
# ADD=61111111 delete filter record
######################

if ($ADD==61111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	 if ( (strlen($lead_filter_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_filters < 1) )
		{
		 echo "<br><font color=red>FILTER NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Filter ID must be at least 2 characters in length</font><br>\n";
		}
	 else
		{
		$stmt="DELETE from vicidial_lead_filters where lead_filter_id='$lead_filter_id' limit 1;";
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0)
			{
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING FILTER!!!!|$PHP_AUTH_USER|$ip|lead_filter_id='$lead_filter_id'|\n");
			fclose($fp);
			}
		echo "<br><B><font color=navy>FILTER DELETION COMPLETED: $lead_filter_id</font></B>\n";
		echo "<br><br>\n";
		}

$ADD='10000000';		# go to filter list
}



######################
# ADD=31111111 modify filter info in the system
######################

if ($ADD==31111111)
{
	if ($LOGmodify_filters==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_lead_filters where lead_filter_id='$lead_filter_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$lead_filter_name =		$row[1];
	$lead_filter_comments =	$row[2];
	$lead_filter_sql =		$row[3];
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	echo "<center><br><font color=navy size=+1>MODIFY A FILTER</font><form action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=41111111>\n";
	echo "<input type=hidden name=lead_filter_id value=\"$lead_filter_id\">\n";
	echo "<TABLE>";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter ID: </td><td align=left><B>$lead_filter_id</B>$NWB#osdial_lead_filters-lead_filter_id$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter Name: </td><td align=left><input type=text name=lead_filter_name size=40 maxlength=50 value=\"$lead_filter_name\"> (short description of the filter)$NWB#osdial_lead_filters-lead_filter_name$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter Comments: </td><td align=left><input type=text name=lead_filter_comments size=50 maxlength=255 value=\"$lead_filter_comments\"> $NWB#osdial_lead_filters-lead_filter_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=right>Filter SQL:</td><td align=left><TEXTAREA NAME=lead_filter_sql ROWS=20 COLS=50>$lead_filter_sql</TEXTAREA> $NWB#osdial_lead_filters-lead_filter_sql$NWE</td></tr>\n";
	echo "<tr bgcolor=#C1D6DF><td align=center colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</table></form>\n";

		##### get campaigns listing for dynamic pulldown
		$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
		$rslt=mysql_query($stmt, $link);
		$campaigns_to_print = mysql_num_rows($rslt);
		$campaigns_list='';

		$o=0;
		while ($campaigns_to_print > $o)
			{
			$rowx=mysql_fetch_row($rslt);
			$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
			}

	echo "<BR><BR>";
	echo "<center><br><font color=navy>TEST ON CAMPAIGN<form action=$PHP_SELF method=POST target=\"_blank\"><br>\n";
	echo "<input type=hidden name=lead_filter_id value=\"$lead_filter_id\">\n";
	echo "<input type=hidden name=ADD value=\"73\">\n";
	echo "<select size=1 name=campaign_id>\n";
	echo "$campaigns_list";
	echo "</select>\n";
	echo "<input type=submit name=SUBMIT value=SUBMIT></center>\n";
	echo "<br><br></center>";


	if ($LOGdelete_filters > 0)
		{
		echo "<br><br><a href=\"$PHP_SELF?ADD=51111111&lead_filter_id=$lead_filter_id\">DELETE THIS FILTER</a>\n";
		}
		echo "";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
	
}



######################
# ADD=10000000 display all filters
######################
if ($ADD==10000000)
{
echo "<TABLE align=center><TR><TD>\n";

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

	$stmt="SELECT * from vicidial_lead_filters order by lead_filter_id";
	$rslt=mysql_query($stmt, $link);
	$filters_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=navy size=+1>LEAD FILTERS</font><br><br>\n";
echo "<TABLE width=$section_width cellspacing=0 cellpadding=1>\n";
echo "<tr bgcolor=$menubarcolor>";
echo "<td><font size=1 color=white><B>NAME</B></td>";
echo "<td><font size=1 color=white><B>DESCRIPTION</B></td>";
echo "<td align=center><font size=1 color=white><B>LINKS</B></td>";

	$o=0;
	while ($filters_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}
		echo "<tr $bgcolor><td><font size=1><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$row[0]\">$row[0]</a></td>";
		echo "<td><font size=1> $row[1]</td>";
		echo "<td><font size=1><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$row[0]\">MODIFY</a></td></tr>\n";
		$o++;
	}

echo "</TABLE></center>\n";
}


?>