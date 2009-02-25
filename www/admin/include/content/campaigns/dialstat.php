<?php


######################
# ADD=28 adds new status to the campaign dial statuses
######################

if ($ADD==28)
{
	$status = eregi_replace("-----.*",'',$status);
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from osdial_campaigns where campaign_id='$campaign_id' and dial_statuses LIKE \"% $status %\";";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT ADDED - there is already an entry for this campaign with this status</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
			{
			 echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length</font>\n";
			}
		 else
			{
			#echo "<br><B><font color=navy>CAMPAIGN DIAL STATUS ADDED: $campaign_id - $status</font></B>\n";

			$stmt="SELECT dial_statuses from osdial_campaigns where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			if (strlen($row[0])<2) {$row[0] = ' -';}
			$dial_statuses = " $status$row[0]";
			$stmt="UPDATE osdial_campaigns set dial_statuses='$dial_statuses' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD CAMPAIGN DIAL STATUS  |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
#$SUB=28;
$ADD=31;
}



######################
# ADD=68 remove campaign dial status
######################

if ($ADD==68)
{
	if ($LOGmodify_campaigns==1)
	{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from osdial_campaigns where campaign_id='$campaign_id' and dial_statuses LIKE \"% $status %\";";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] < 1)
		{echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT REMOVED - this dial status is not selected for this campaign</font>\n";}
	else
		{
		 if ( (strlen($campaign_id) < 2) or (strlen($status) < 1) )
			{
			 echo "<br><font color=red>CAMPAIGN DIAL STATUS NOT REMOVED - Please go back and look at the data you entered\n";
			 echo "<br>status must be between 1 and 6 characters in length><br>\n";
			}
		 else
			{
			#echo "<br><B><font color=navy>CAMPAIGN DIAL STATUS REMOVED: $campaign_id - $status</font></B>\n";

			$stmt="SELECT dial_statuses from osdial_campaigns where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);

			$dial_statuses = eregi_replace(" $status "," ",$row[0]);
			$stmt="UPDATE osdial_campaigns set dial_statuses='$dial_statuses' where campaign_id='$campaign_id';";
			$rslt=mysql_query($stmt, $link);

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|DIAL STATUS REMOVED   |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
#$SUB=28;
$ADD=31;	# go to campaign modification form below
}



require('campaigns.php');


?>
