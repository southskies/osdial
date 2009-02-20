<?php


if ( ( (strlen($ADD)>4) && ($ADD < 99998) ) or ($ADD==3) or (($ADD>20) and ($ADD<70)) or ($ADD=="4A")  or ($ADD=="4B") or (strlen($ADD)==12) )
	{

	#TODO: functionalize
	##### BEGIN get campaigns listing for rankings #####
	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);
	$campaigns_list='';
	$campaigns_value='';
	$RANKcampaigns_list="<tr><td>CAMPAIGN</td><td> &nbsp; &nbsp; RANK</td><td> &nbsp; &nbsp; CALLS</td></tr>\n";

	$o=0;
	while ($campaigns_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$campaigns_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
		$campaign_id_values[$o] = $rowx[0];
		$campaign_name_values[$o] = $rowx[1];
		$o++;
		}

	$o=0;
	while ($campaigns_to_print > $o)
		{
		$stmt="SELECT campaign_rank,calls_today from vicidial_campaign_agents where user='$user' and campaign_id='$campaign_id_values[$o]'";
		$rslt=mysql_query($stmt, $link);
		$ranks_to_print = mysql_num_rows($rslt);
		if ($ranks_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$SELECT_campaign_rank = $row[0];
			$calls_today = $row[1];
			}
		else
			{$calls_today=0;   $SELECT_campaign_rank=0;}
		if ( ($ADD=="4A") or ($ADD=="4B") )
			{
			if (isset($_GET["RANK_$campaign_id_values[$o]"]))			{$campaign_rank=$_GET["RANK_$campaign_id_values[$o]"];}
				elseif (isset($_POST["RANK_$campaign_id_values[$o]"]))	{$campaign_rank=$_POST["RANK_$campaign_id_values[$o]"];}

			if ($ranks_to_print > 0)
				{
				$stmt="UPDATE vicidial_campaign_agents set campaign_rank='$campaign_rank', campaign_weight='$campaign_rank' where campaign_id='$campaign_id_values[$o]' and user='$user';";
				$rslt=mysql_query($stmt, $link);
				}
			else
				{
				$stmt="INSERT INTO vicidial_campaign_agents set campaign_rank='$campaign_rank', campaign_weight='$campaign_rank', campaign_id='$campaign_id_values[$o]', user='$user';";
				$rslt=mysql_query($stmt, $link);
				}

			$stmt="UPDATE vicidial_live_agents set campaign_weight='$campaign_rank' where campaign_id='$campaign_id_values[$o]' and user='$user';";
			$rslt=mysql_query($stmt, $link);
			}
		else {$campaign_rank = $SELECT_campaign_rank;}

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		# disable non user-group allowable campaign ranks
		$stmt="SELECT user_group from vicidial_users where user='$user';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$Ruser_group =	$row[0];

		$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$Ruser_group';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$allowed_campaigns =	$row[0];
		$allowed_campaigns = preg_replace("/ -$/","",$allowed_campaigns);
		$UGcampaigns = explode(" ", $allowed_campaigns);

		$p=0;   $RANK_camp_active=0;   $CR_disabled = '';
		if (eregi('-ALL-CAMPAIGNS-',$allowed_campaigns))
			{$RANK_camp_active++;}
		else
			{
			$UGcampaign_ct = count($UGcampaigns);
			while ($p < $UGcampaign_ct)
				{
				if ($campaign_id_values[$o] == $UGcampaigns[$p]) 
					{$RANK_camp_active++;}
				$p++;
				}
			}
		if ($RANK_camp_active < 1) {$CR_disabled = 'DISABLED';}

		$RANKcampaigns_list .= "<tr $bgcolor><td>";
		$campaigns_list .= "<a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id_values[$o]\">$campaign_id_values[$o]</a> - $campaign_name_values[$o] <BR>\n";
		$RANKcampaigns_list .= "<a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id_values[$o]\">$campaign_id_values[$o]</a> - $campaign_name_values[$o] </td>";
		$RANKcampaigns_list .= "<td> &nbsp; &nbsp; <select size=1 name=RANK_$campaign_id_values[$o] $CR_disabled>\n";
		$h="9";
		while ($h>=-9)
			{
			$RANKcampaigns_list .= "<option value=\"$h\"";
			if ($h==$campaign_rank)
				{$RANKcampaigns_list .= " SELECTED";}
			$RANKcampaigns_list .= ">$h</option>";
			$h--;
			}
		$RANKcampaigns_list .= "</select></td>\n";
		$RANKcampaigns_list .= "<td align=right> &nbsp; &nbsp; $calls_today</td></tr>\n";
		$o++;
		}
	##### END get campaigns listing for rankings #####


	##### BEGIN get inbound groups listing for checkboxes #####
	$xfer_groupsSQL='';
	if ( (($ADD>20) and ($ADD<70)) and ($ADD!=41) )
	{
	$stmt="SELECT closer_campaigns,xfer_groups from vicidial_campaigns where campaign_id='$campaign_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$closer_campaigns =	$row[0];
		$closer_campaigns = preg_replace("/ -$/","",$closer_campaigns);
		$groups = explode(" ", $closer_campaigns);
	$xfer_groups =	$row[1];
		$xfer_groups = preg_replace("/ -$/","",$xfer_groups);
		$XFERgroups = explode(" ", $xfer_groups);
	$xfer_groupsSQL = preg_replace("/^ | -$/","",$xfer_groups);
	$xfer_groupsSQL = preg_replace("/ /","','",$xfer_groupsSQL);
	$xfer_groupsSQL = "WHERE group_id IN('$xfer_groupsSQL')";
	}
	if ($ADD==41)
	{
	$p=0;
	$XFERgroup_ct = count($XFERgroups);
	while ($p < $XFERgroup_ct)
		{
		$xfer_groups .= " $XFERgroups[$p]";
		$p++;
		}
	$xfer_groupsSQL = preg_replace("/^ | -$/","",$xfer_groups);
	$xfer_groupsSQL = preg_replace("/ /","','",$xfer_groupsSQL);
	$xfer_groupsSQL = "WHERE group_id IN('$xfer_groupsSQL')";
	}

	if ( (($ADD==31111) or ($ADD==31111)) and (count($groups)<1) )
	{
	$stmt="SELECT closer_campaigns from vicidial_remote_agents where remote_agent_id='$remote_agent_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$closer_campaigns =	$row[0];
	$closer_campaigns = preg_replace("/ -$/","",$closer_campaigns);
	$groups = explode(" ", $closer_campaigns);
	}

	if ($ADD==3)
	{
	$stmt="SELECT closer_campaigns from vicidial_users where user='$user';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$closer_campaigns =	$row[0];
	$closer_campaigns = preg_replace("/ -$/","",$closer_campaigns);
	$groups = explode(" ", $closer_campaigns);
	}

	$stmt="SELECT group_id,group_name from vicidial_inbound_groups order by group_id";
	$rslt=mysql_query($stmt, $link);
	$groups_to_print = mysql_num_rows($rslt);
	$groups_list='';
	$groups_value='';
	$XFERgroups_list='';
	$RANKgroups_list="<tr><td>INBOUND GROUP</td><td> &nbsp; &nbsp; RANK</td><td> &nbsp; &nbsp; CALLS</td></tr>\n";

	$o=0;
	while ($groups_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$group_id_values[$o] = $rowx[0];
		$group_name_values[$o] = $rowx[1];
		$o++;
		}

	$o=0;
	while ($groups_to_print > $o)
		{
		$stmt="SELECT group_rank,calls_today from vicidial_inbound_group_agents where user='$user' and group_id='$group_id_values[$o]'";
		$rslt=mysql_query($stmt, $link);
		$ranks_to_print = mysql_num_rows($rslt);
		if ($ranks_to_print > 0)
			{
			$row=mysql_fetch_row($rslt);
			$SELECT_group_rank = $row[0];
			$calls_today = $row[1];
			}
		else
			{$calls_today=0;   $SELECT_group_rank=0;}
		if ( ($ADD=="4A") or ($ADD=="4B") )
			{
			if (isset($_GET["RANK_$group_id_values[$o]"]))			{$group_rank=$_GET["RANK_$group_id_values[$o]"];}
				elseif (isset($_POST["RANK_$group_id_values[$o]"]))	{$group_rank=$_POST["RANK_$group_id_values[$o]"];}

			if ($ranks_to_print > 0)
				{
				$stmt="UPDATE vicidial_inbound_group_agents set group_rank='$group_rank', group_weight='$group_rank' where group_id='$group_id_values[$o]' and user='$user';";
				$rslt=mysql_query($stmt, $link);
				}
			else
				{
				$stmt="INSERT INTO vicidial_inbound_group_agents set group_rank='$group_rank', group_weight='$group_rank', group_id='$group_id_values[$o]', user='$user';";
				$rslt=mysql_query($stmt, $link);
				}

			$stmt="UPDATE vicidial_live_inbound_agents set group_weight='$group_rank' where group_id='$group_id_values[$o]' and user='$user';";
			$rslt=mysql_query($stmt, $link);
			}
		else {$group_rank = $SELECT_group_rank;}

		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor="#CBDCE0"';} 
		else
			{$bgcolor='bgcolor="#C1D6DB"';}

		$groups_list .= "<input type=\"checkbox\" name=\"groups[]\" value=\"$group_id_values[$o]\"";
		$XFERgroups_list .= "<input type=\"checkbox\" name=\"XFERgroups[]\" value=\"$group_id_values[$o]\"";
		$RANKgroups_list .= "<tr $bgcolor><td><input type=\"checkbox\" name=\"groups[]\" value=\"$group_id_values[$o]\"";
		$p=0;
		$group_ct = count($groups);
		while ($p < $group_ct)
			{
			if ($group_id_values[$o] == $groups[$p]) 
				{
				$groups_list .= " CHECKED";
				$RANKgroups_list .= " CHECKED";
				$groups_value .= " $group_id_values[$o]";
				}
			$p++;
			}
		$p=0;
		$XFERgroup_ct = count($XFERgroups);
		while ($p < $XFERgroup_ct)
			{
			if ($group_id_values[$o] == $XFERgroups[$p]) 
				{
				$XFERgroups_list .= " CHECKED";
				$XFERgroups_value .= " $group_id_values[$o]";
				}
			$p++;
			}
		$groups_list .= "> <a href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">$group_id_values[$o]</a> - $group_name_values[$o] <BR>\n";
		$XFERgroups_list .= "> <a href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">$group_id_values[$o]</a> - $group_name_values[$o] <BR>\n";
		$RANKgroups_list .= "> <a href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">$group_id_values[$o]</a> - $group_name_values[$o] </td>";
		$RANKgroups_list .= "<td> &nbsp; &nbsp; <select size=1 name=RANK_$group_id_values[$o]>\n";
		$h="9";
		while ($h>=-9)
			{
			$RANKgroups_list .= "<option value=\"$h\"";
			if ($h==$group_rank)
				{$RANKgroups_list .= " SELECTED";}
			$RANKgroups_list .= ">$h</option>";
			$h--;
			}
		$RANKgroups_list .= "</select></td>\n";
		$RANKgroups_list .= "<td align=right> &nbsp; &nbsp; $calls_today</td></tr>\n";
		$o++;
		}
	if (strlen($groups_value)>2) {$groups_value .= " -";}
	if (strlen($XFERgroups_value)>2) {$XFERgroups_value .= " -";}
	}
	##### END get inbound groups listing for checkboxes #####


	##### BEGIN get campaigns listing for checkboxes #####
	if ( ($ADD==211111) or ($ADD==311111) or ($ADD==411111) or ($ADD==511111) or ($ADD==611111) )
	{
		if ( ($ADD==211111) or ($ADD==311111) or ($ADD==511111) or ($ADD==611111) )
		{
		$stmt="SELECT allowed_campaigns from vicidial_user_groups where user_group='$user_group';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$allowed_campaigns =	$row[0];
		$allowed_campaigns = preg_replace("/ -$/","",$allowed_campaigns);
		$campaigns = explode(" ", $allowed_campaigns);
		}

	$campaigns_value='';
	$campaigns_list='<B><input type="checkbox" name="campaigns[]" value="-ALL-CAMPAIGNS-"';
		$p=0;
		while ($p<100)
			{
			if (eregi('ALL-CAMPAIGNS',$campaigns[$p])) 
				{
				$campaigns_list.=" CHECKED";
				$campaigns_value .= " -ALL-CAMPAIGNS- -";
				}
			$p++;
			}
	$campaigns_list.="> ALL-CAMPAIGNS - AGENTS CAN VIEW ANY CAMPAIGN</B><BR>\n";

	$stmt="SELECT campaign_id,campaign_name from vicidial_campaigns order by campaign_id";
	$rslt=mysql_query($stmt, $link);
	$campaigns_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($campaigns_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		$campaign_id_value = $rowx[0];
		$campaign_name_value = $rowx[1];
		$campaigns_list .= "<input type=\"checkbox\" name=\"campaigns[]\" value=\"$campaign_id_value\"";
		$p=0;
		while ($p<100)
			{
			if ($campaign_id_value == $campaigns[$p]) 
				{
			#	echo "<!--  X $p|$campaign_id_value|$campaigns[$p]| -->";
				$campaigns_list .= " CHECKED";
				$campaigns_value .= " $campaign_id_value";
				}
		#	echo "<!--  O $p|$campaign_id_value|$campaigns[$p]| -->";
			$p++;
			}
		$campaigns_list .= "> $campaign_id_value - $campaign_name_value<BR>\n";
		$o++;
		}
	if (strlen($campaigns_value)>2) {$campaigns_value .= " -";}
	}
	##### END get campaigns listing for checkboxes #####

?>
