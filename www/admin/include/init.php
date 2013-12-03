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



if ( ( (OSDstrlen($ADD)>4) && ($ADD < 99998) ) or ($ADD==3) or (($ADD>20) and ($ADD<70)) or ($ADD=="4A")  or ($ADD=="4B") or (OSDstrlen($ADD)==12) ) {

    #TODO: functionalize
    ##### BEGIN get campaigns listing for rankings #####
    $stmt = sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns ORDER BY campaign_id;");
    $rslt=mysql_query($stmt, $link);
    $campaigns_to_print = mysql_num_rows($rslt);
    $campaigns_value='';
    $RANKcampaigns_list="<tr class=tabheader><td align=left>CAMPAIGN</td><td align=center>RANK</td><td align=right>CALLS</td></tr>\n";

    $o=0;
    while ($campaigns_to_print > $o) {
        $rowx=mysql_fetch_row($rslt);
        $campaign_id_values[$o] = $rowx[0];
        $campaign_name_values[$o] = $rowx[1];
        $o++;
    }

    $o=0;
    while ($campaigns_to_print > $o) {
        $stmt = sprintf("SELECT campaign_rank,calls_today FROM osdial_campaign_agents WHERE user='%s' AND campaign_id='%s';",mres($user),mres($campaign_id_values[$o]));
        $rslt=mysql_query($stmt, $link);
        $ranks_to_print = mysql_num_rows($rslt);
        if ($ranks_to_print > 0) {
            $row=mysql_fetch_row($rslt);
            $SELECT_campaign_rank = $row[0];
            $calls_today = $row[1];
        } else {
            $calls_today=0;
            $SELECT_campaign_rank=0;
        }
        if ( ($ADD=="4A") or ($ADD=="4B") ) {
            if (isset($_GET["RANK_$campaign_id_values[$o]"])) {
                $campaign_rank = $_GET["RANK_$campaign_id_values[$o]"];
            } elseif (isset($_POST["RANK_$campaign_id_values[$o]"])) {
                $campaign_rank=$_POST["RANK_$campaign_id_values[$o]"];
            }

            if ($LOG['multicomp_admin'] > 0) {
                # Do nothing.
            } elseif ($LOG['multicomp'] > 0) {
                if ($ranks_to_print > 0) {
                    $stmt=sprintf("UPDATE osdial_campaign_agents SET campaign_rank='%s',campaign_weight='%s' WHERE campaign_id='%s' AND user='%s' AND user LIKE '%s__%%';",mres($campaign_rank),mres($campaign_rank),mres($campaign_id_values[$o]),mres($user),mres($LOG['company_prefix']));
                    $rslt=mysql_query($stmt, $link);
                } else {
                    $stmt=sprintf("INSERT INTO osdial_campaign_agents SET campaign_rank='%s',campaign_weight='%s',campaign_id='%s',user='%s';",mres($campaign_rank),mres($campaign_rank),mres($campaign_id_values[$o]),mres($user));
                    $rslt=mysql_query($stmt, $link);
                }
                $stmt=sprintf("UPDATE osdial_live_agents SET campaign_weight='%s' WHERE campaign_id='%s' AND user='%s' AND user LIKE '%s__%%';",mres($campaign_rank),mres($campaign_id_values[$o]),mres($user),mres($LOG['company_prefix']));
                $rslt=mysql_query($stmt, $link);
            } else {
                if ($ranks_to_print > 0) {
                    $stmt=sprintf("UPDATE osdial_campaign_agents SET campaign_rank='%s',campaign_weight='%s' WHERE campaign_id='%s' AND user='%s';",mres($campaign_rank),mres($campaign_rank),mres($campaign_id_values[$o]),mres($user));
                    $rslt=mysql_query($stmt, $link);
                } else {
                    $stmt=sprintf("INSERT INTO osdial_campaign_agents SET campaign_rank='%s',campaign_weight='%s',campaign_id='%s',user='%s';",mres($campaign_rank),mres($campaign_rank),mres($campaign_id_values[$o]),mres($user));
                    $rslt=mysql_query($stmt, $link);
                }
                $stmt=sprintf("UPDATE osdial_live_agents SET campaign_weight='%s' WHERE campaign_id='%s' AND user='%s';",mres($campaign_rank),mres($campaign_id_values[$o]),mres($user));
                $rslt=mysql_query($stmt, $link);
            }
        } else {
            $campaign_rank = $SELECT_campaign_rank;
        }

        # disable non user-group allowable campaign ranks
        $stmt=sprintf("SELECT user_group FROM osdial_users WHERE user='%s';",mres($user));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $Ruser_group = $row[0];

        $stmt=sprintf("SELECT allowed_campaigns FROM osdial_user_groups WHERE user_group='%s';",mres($Ruser_group));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $allowed_campaigns = $row[0];
        $allowed_campaigns = OSDpreg_replace("/ -$/","",$allowed_campaigns);
        $UGcampaigns = explode(" ", $allowed_campaigns);

        $p=0;
        $RANK_camp_active=0;
        $CR_disabled = '';
        if (OSDpreg_match('/-ALL-CAMPAIGNS-/',$allowed_campaigns)) {
            $RANK_camp_active++;
        } else {
            $UGcampaign_ct = count($UGcampaigns);
            while ($p < $UGcampaign_ct) {
                if ($campaign_id_values[$o] == $UGcampaigns[$p]) {
                    $RANK_camp_active++;
                }
                $p++;
            }
        }
        if ($RANK_camp_active < 1) {
            $CR_disabled = 'DISABLED';
        }

        if (OSDpreg_match('/:'.$campaign_id_values[$o].':/',$LOG['allowed_campaignsSTR'])) {
            $RANKcampaigns_list .= "<tr class=row " . bgcolor($o) . "><td>";
            $RANKcampaigns_list .= "<font size=1><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id_values[$o]\">" . mclabel($campaign_id_values[$o]) . "</a> - $campaign_name_values[$o]</font></td>";
            $RANKcampaigns_list .= "<td align=center><select style=\"font-size: 8px;\" size=1 name=RANK_$campaign_id_values[$o] $CR_disabled>\n";
            $h="9";
            while ($h>=-9) {
                $RANKcampaigns_list .= "<option value=\"$h\"";
                if ($h==$campaign_rank) {
                    $RANKcampaigns_list .= " SELECTED";
                }
                $RANKcampaigns_list .= ">$h</option>";
                $h--;
            }
            $RANKcampaigns_list .= "</select></td>\n";
            $RANKcampaigns_list .= "<td align=right><font size=1>$calls_today</font></td></tr>\n";
        } else {
            $RANKcampaigns_list .= "<input type=\"hidden\" name=RANK_$campaign_id_values[$o] value=\"$campaign_rank\">";
        }
        $o++;
    }
    $RANKcampaigns_list .= "<tr bgcolor=$menubarcolor height=8px><td colspan=3><font color=white size=1></font></td></tr>\n";
    ##### END get campaigns listing for rankings #####


    ##### BEGIN get inbound groups listing for checkboxes #####
    $xfer_groupsSQL='';
    if ( (($ADD>20) and ($ADD<70)) and ($ADD!=41) ) {
        $stmt = sprintf("SELECT closer_campaigns,xfer_groups from osdial_campaigns where campaign_id='%s' AND campaign_id IN %s;",mres($campaign_id),$LOG['allowed_campaignsSQL']);
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $closer_campaigns = $row[0];
        $closer_campaigns = OSDpreg_replace("/ -$/","",$closer_campaigns);
        $groups = explode(" ", $closer_campaigns);
        $xfer_groups = $row[1];
        $xfer_groups = OSDpreg_replace("/ -$/","",$xfer_groups);
        $XFERgroups = explode(" ", $xfer_groups);
        $xfer_groupsSQL = OSDpreg_replace("/^ | -$/","",$xfer_groups);
        $xfer_groupsSQL = OSDpreg_replace("/ /","','",$xfer_groupsSQL);
        $xfer_groupsSQL = "WHERE group_id IN('$xfer_groupsSQL')";
    }
    if ($ADD==41) {
        $p=0;
        $XFERgroup_ct = count($XFERgroups);
        while ($p < $XFERgroup_ct) {
            $xfer_groups .= " $XFERgroups[$p]";
            $p++;
        }
        $xfer_groupsSQL = OSDpreg_replace("/^ | -$/","",$xfer_groups);
        $xfer_groupsSQL = OSDpreg_replace("/ /","','",$xfer_groupsSQL);
        $xfer_groupsSQL = "WHERE group_id IN('$xfer_groupsSQL')";
    }

    if ( (($ADD==31111) or ($ADD==31111)) and (count($groups)<1) ) {
        $stmt = sprintf("SELECT closer_campaigns from osdial_remote_agents where remote_agent_id='$remote_agent_id' and campaign_id IN %s;",mres($remote_agent_id),$LOG['allowed_campaignsSQL']);
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $closer_campaigns = $row[0];
        $closer_campaigns = OSDpreg_replace("/ -$/","",$closer_campaigns);
        $groups = explode(" ", $closer_campaigns);
    }

    if ($ADD==3) {
        $stmt=sprintf("SELECT closer_campaigns FROM osdial_users WHERE user='%s';",mres($user));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $closer_campaigns = $row[0];
        $closer_campaigns = OSDpreg_replace("/ -$/","",$closer_campaigns);
        $groups = explode(" ", $closer_campaigns);
    }

    $stmt=sprintf("SELECT group_id,group_name FROM osdial_inbound_groups WHERE group_id NOT LIKE 'A2A_%%' ORDER BY group_id;");
    $rslt=mysql_query($stmt, $link);
    $groups_to_print = mysql_num_rows($rslt);
    $groups_list='';
    $groups_value='';
    $XFERgroups_list='';
    $XFERgroups_value='';
    $RANKgroups_list="<tr class=tabheader><td>&nbsp;</td><td align=left>INBOUND GROUP</td><td align=center>RANK</td><td align=right>CALLS</td></tr>\n";
    $groups_listTAB="<tr class=tabheader><td>&nbsp;</td><td align=left>INBOUND GROUP</td></tr>\n";
    $XFERgroups_listTAB="<tr class=tabheader><td>&nbsp;</td><td align=left>TRANSFER GROUP</td></tr>\n";

    $o=0;
    while ($groups_to_print > $o) {
        $rowx=mysql_fetch_row($rslt);
        $group_id_values[$o] = $rowx[0];
        $group_name_values[$o] = $rowx[1];
        $o++;
    }

    $o=0;
    while ($groups_to_print > $o) {
        $stmt=sprintf("SELECT group_rank,calls_today FROM osdial_inbound_group_agents WHERE user='%s' AND group_id='%s';",mres($user),mres($group_id_values[$o]));
        $rslt=mysql_query($stmt, $link);
        $ranks_to_print = mysql_num_rows($rslt);
        if ($ranks_to_print > 0) {
            $row=mysql_fetch_row($rslt);
            $SELECT_group_rank = $row[0];
            $calls_today = $row[1];
        } else {
            $calls_today=0;
            $SELECT_group_rank=0;
        }
        if ( ($ADD=="4A") or ($ADD=="4B") ) {
            if (isset($_GET["RANK_$group_id_values[$o]"])) {
                $group_rank=$_GET["RANK_$group_id_values[$o]"];
            } elseif (isset($_POST["RANK_$group_id_values[$o]"])) {
                $group_rank=$_POST["RANK_$group_id_values[$o]"];
            }

            if ($LOG['multicomp_admin'] > 0) {
                # Do nothing.
            } elseif ($LOG['multicomp'] > 0) {
                if ($ranks_to_print > 0) {
                    $stmt=sprintf("UPDATE osdial_inbound_group_agents SET group_rank='%s',group_weight='%s' WHERE group_id='%s' AND user='%s' AND user LIKE '%s__%%';",mres($group_rank),mres($group_rank),mres($group_id_values[$o]),mres($user),mres($LOG['company_prefix']));
                    $rslt=mysql_query($stmt, $link);
                } else {
                    $stmt=sprintf("INSERT INTO osdial_inbound_group_agents SET group_rank='%s',group_weight='%s',group_id='%s',user='%s';",mres($group_rank),mres($group_rank),mres($group_id_values[$o]),mres($user));
                    $rslt=mysql_query($stmt, $link);
                }
                $stmt=sprintf("UPDATE osdial_live_inbound_agents SET group_weight='%s' WHERE group_id='%s' AND user='%s' AND user LIKE '%s__%%';",mres($group_rank),mres($group_id_values[$o]),mres($user),mres($LOG['company_prefix']));
                $rslt=mysql_query($stmt, $link);
            } else {
                if ($ranks_to_print > 0) {
                    $stmt=sprintf("UPDATE osdial_inbound_group_agents SET group_rank='%s',group_weight='%s' WHERE group_id='%s' AND user='%s';",mres($group_rank),mres($group_rank),mres($group_id_values[$o]),mres($user));
                    $rslt=mysql_query($stmt, $link);
                } else {
                    $stmt=sprintf("INSERT INTO osdial_inbound_group_agents SET group_rank='%s',group_weight='%s',group_id='%s',user='%s';",mres($group_rank),mres($group_rank),mres($group_id_values[$o]),mres($user));
                    $rslt=mysql_query($stmt, $link);
                }
                $stmt=sprintf("UPDATE osdial_live_inbound_agents SET group_weight='%s' WHERE group_id='%s' AND user='%s';",mres($group_rank),mres($group_id_values[$o]),mres($user));
                $rslt=mysql_query($stmt, $link);
            }
        } else {
            $group_rank = $SELECT_group_rank;
        }

        $hidden_groups_list='';
        $hidden_groups_listTAB='';
        $hidden_XFERgroups_list='';
        $hidden_XFERgroups_listTAB='';
        $hidden_RANKgroups_list='';
        if (OSDpreg_match('/:'.$group_id_values[$o].':/',$LOG['allowed_ingroupsSTR'])) {
            $groups_list        .= "<input type=\"checkbox\" id=\"GL$group_id_values[$o]\" name=\"groups[]\" value=\"$group_id_values[$o]\"";
            $groups_listTAB     .= "<tr " . bgcolor($o) . " title=\"$group_name_values[$o]\" class=row><td align=left><font size=1><input type=\"checkbox\" id=\"GL$group_id_values[$o]\" name=\"groups[]\" value=\"$group_id_values[$o]\"";
            $XFERgroups_list    .= "<input type=\"checkbox\" id=\"XGL$group_id_values[$o]\" name=\"XFERgroups[]\" value=\"$group_id_values[$o]\"";
            $XFERgroups_listTAB .= "<tr " . bgcolor($o) . " title=\"$group_name_values[$o]\" class=row><td align=left><font size=1><input type=\"checkbox\" id=\"XGL$group_id_values[$o]\" name=\"XFERgroups[]\" value=\"$group_id_values[$o]\"";
            $RANKgroups_list    .= "<tr " . bgcolor($o) . " title=\"$group_name_values[$o]\" class=row><td align=left><font size=1><input type=\"checkbox\" id=\"RGL$group_id_values[$o]\" name=\"groups[]\" value=\"$group_id_values[$o]\"";
        } else {
            $hidden_groups_list        = "<input type=\"hidden\" id=\"GL$group_id_values[$o]\" name=\"groups[]\" value=\"$group_id_values[$o]\">";
            $hidden_groups_listTAB     = "<input type=\"hidden\" id=\"GL$group_id_values[$o]\" name=\"groups[]\" value=\"$group_id_values[$o]\">";
            $hidden_XFERgroups_list    = "<input type=\"hidden\" id=\"XGL$group_id_values[$o]\" name=\"XFERgroups[]\" value=\"$group_id_values[$o]\">";
            $hidden_XFERgroups_listTAB = "<input type=\"hidden\" id=\"XGL$group_id_values[$o]\" name=\"XFERgroups[]\" value=\"$group_id_values[$o]\">";
            $hidden_RANKgroups_list    = "<input type=\"hidden\" id=\"RGL$group_id_values[$o]\" name=\"groups[]\" value=\"$group_id_values[$o]\">";
        }
        $p=0;
        $group_ct = count($groups);
        while ($p < $group_ct) {
            if ($group_id_values[$o] == $groups[$p]) {
                $groups_list .= " CHECKED";
                $groups_listTAB .= " CHECKED";
                $RANKgroups_list .= " CHECKED";
                $groups_list .= $hidden_groups_list;
                $groups_listTAB .= $hidden_groups_listTAB;
                $RANKgroups_list .= $hidden_RANKgroups_list;
                $groups_value .= " $group_id_values[$o]";
            }
            $p++;
        }
        $p=0;
        $XFERgroup_ct = count($XFERgroups);
        while ($p < $XFERgroup_ct) {
            if ($group_id_values[$o] == $XFERgroups[$p]) {
                $XFERgroups_list .= " CHECKED";
                $XFERgroups_listTAB .= " CHECKED";
                $XFERgroups_list .= $hidden_XFERgroups_list;
                $XFERgroups_listTAB .= $hidden_XFERgroups_listTAB;
                $XFERgroups_value .= " $group_id_values[$o]";
            }
            $p++;
        }
        if (OSDpreg_match('/:'.$group_id_values[$o].':/',$LOG['allowed_ingroupsSTR'])) {
            $groups_list         .= "><label for=\"GL$group_id_values[$o]\"> <a href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">" . mclabel($group_id_values[$o]) . "</a> - $group_name_values[$o] </label><BR>\n";
            $XFERgroups_list     .= "><label for=\"XGL$group_id_values[$o]\"> <a href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">" . mclabel($group_id_values[$o]) . "</a> - $group_name_values[$o] </label><BR>\n";
            $groups_listTAB      .= "></font></td><td onclick=\"document.getElementById('GL$group_id_values[$o]').click();\"><font size=1><a onclick=\"document.getElementById('GL$group_id_values[$o]').click();\" href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">" . mclabel($group_id_values[$o]) . "</a> - $group_name_values[$o]</font></td></tr>";
            $XFERgroups_listTAB  .= "></font></td><td onclick=\"document.getElementById('XGL$group_id_values[$o]').click();\"><font size=1><a onclick=\"document.getElementById('XGL$group_id_values[$o]').click();\" href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">" . mclabel($group_id_values[$o]) . "</a> - $group_name_values[$o]</font></td></tr>";
            $RANKgroups_list     .= "></font></td><td onclick=\"document.getElementById('RGL$group_id_values[$o]').click();\"><font size=1><a onclick=\"document.getElementById('RGL$group_id_values[$o]').click();\" href=\"$PHP_SELF?ADD=3111&group_id=$group_id_values[$o]\">" . mclabel($group_id_values[$o]) . "</a> - $group_name_values[$o]</font></td>";
            $RANKgroups_list     .= "<td align=center><select size=1 style=\"font-size: 8px;\" name=RANK_$group_id_values[$o]>\n";
            $h="9";
            while ($h>=-9) {
                $RANKgroups_list .= "<option value=\"$h\"";
                if ($h==$group_rank) {
                    $RANKgroups_list .= " SELECTED";
                }
                $RANKgroups_list .= ">$h</option>";
                $h--;
            }
            $RANKgroups_list .= "</select></td>\n";
            $RANKgroups_list .= "<td align=right onclick=\"document.getElementById('RGL$group_id_values[$o]').click();\"><font size=1>$calls_today</font></td></tr>\n";
        } else {
            $RANKgroups_list .= "<input type=\"hidden\" name=RANK_$group_id_values[$o] value=\"$group_rank\">";
        }
        $o++;
    }
    $RANKgroups_list    .= "<tr bgcolor=$menubarcolor height=8px><td colspan=4><font size=1 color=white></font></td></tr>\n";
    $groups_listTAB     .= "<tr bgcolor=$menubarcolor height=8px><td colspan=2><font size=1 color=white></font></td></tr>\n";
    $XFERgroups_listTAB .= "<tr bgcolor=$menubarcolor height=8px><td colspan=2><font size=1 color=white></font></td></tr>\n";
    if (OSDstrlen($groups_value)>2) {
        $groups_value .= " -";
    }
    if (OSDstrlen($XFERgroups_value)>2) {
        $XFERgroups_value .= " -";
    }
}
##### END get inbound groups listing for checkboxes #####


?>
