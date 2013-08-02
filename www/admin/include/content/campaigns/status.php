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
# ADD=22 adds the new campaign status to the system
######################

if ($ADD==22) {
    if ($stage=='extended') {
        $ocse = get_first_record($link,'osdial_campaign_statuses_extended','count(*) AS cnt',sprintf("campaign_id='%s' AND parents='%s' AND status='%s'",mres($campaign_id),mres($parents),mres($status)));
        if ($ocse['cnt'] > 0) {
            echo "<br><font color=red> CAMPAIGN EXTENDED STATUS NOT ADDED - there is already a campaign-extended-status in the system with this name</font>\n";
        } else {
            if (OSDstrlen($campaign_id)<2 or OSDstrlen($status)<1 or OSDstrlen($status_name)<2) {
                echo "<br><font color=red> CAMPAIGN EXTENDED STATUS NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>status must be between 1 and 8 characters in length\n";
                echo "<br>status name must be between 2 and 30 characters in length</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text> CAMPAIGN EXTENDED STATUS ADDED: $campaign_id - $parents:$status</font></b>\n";
                $stmt=sprintf("INSERT INTO osdial_campaign_statuses_extended (parents,status,status_name,selectable,campaign_id) VALUES('%s','%s','%s','%s','%s');",mres($parents),mres($status),mres($status_name),mres($selectable),mres($campaign_id));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW CAMPAIGN EXTENDED STATUS |$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }

    } else {
        $ocs = get_first_record($link,'osdial_campaign_statuses','count(*) AS cnt',sprintf("campaign_id='%s' AND status='%s'",mres($campaign_id),mres($status)));
        if ($ocs['cnt'] > 0) {
            echo "<br><font color=red> CAMPAIGN STATUS NOT ADDED - there is already a campaign-status in the system with this name</font>\n";
        } else {
            if (OSDstrlen($campaign_id)<2 or OSDstrlen($status)<1 or OSDstrlen($status_name)<2) {
                echo "<br><font color=red> CAMPAIGN STATUS NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>status must be between 1 and 8 characters in length\n";
                echo "<br>status name must be between 2 and 30 characters in length</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text> CAMPAIGN STATUS ADDED: $campaign_id - $status</font></b>\n";
                $stmt=sprintf("INSERT INTO osdial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category) VALUES('%s','%s','%s','%s','%s','%s');",mres($status),mres($status_name),mres($selectable),mres($campaign_id),mres($human_answered),mres($category));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW CAMPAIGN STATUS |$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }
    }
    $SUB=22;
    $ADD=31;
}


######################
# ADD=42 modify/delete campaign status in the system
######################

if ($ADD==42) {
    if ($LOG['modify_campaigns']==1) {
        if (OSDpreg_match('/extended/',$stage)) {
            if (OSDstrlen($campaign_id)<2 or OSDstrlen($parents)<1 or OSDstrlen($status)<1) {
                echo "<br><font color=red>CAMPAIGN EXTENDED STATUS NOT MODIFIED - Please go back and look at the data you entered\n";
                echo "<br>the campaign id needs to be at least 2 characters in length\n";
                echo "<br>the status parents needs to be at least 1 characters in length\n";
                echo "<br>the campaign status needs to be at least 1 characters in length</font><br>\n";
            } else {
                if (OSDpreg_match('/delete/',$stage)) {
                    $ocse = get_first_record($link,'osdial_campaign_statuses_extended','count(*) AS cnt',sprintf("campaign_id='%s' AND parents='%s'",mres($campaign_id),mres($parents.':'.$status)));
                    if ($ocse['cnt']>0) {
                        echo "<br><center><b><font color=red>CAMPAIGN EXTENDED STATUS NOT DELETED - This status has $ocse[cnt] sub-statuses, please remove them first.</font></b></center><br>\n";
                    } else {
                        echo "<br><b><font color=$default_text>CUSTOM CAMPAIGN EXTENDED STATUS DELETED: $campaign_id - $parents:$status</font></b>\n";
                        $stmt=sprintf("DELETE FROM osdial_campaign_statuses_extended WHERE campaign_id='%s' AND parents='%s' AND status='%s';",mres($campaign_id),mres($parents),mres($status));
                        $rslt=mysql_query($stmt, $link);

                        ### LOG CHANGES TO LOG FILE ###
                        if ($WeBRooTWritablE > 0) {
                            $fp = fopen ("./admin_changes_log.txt", "a");
                            fwrite ($fp, "$date|DELETE CAMPAIGN EXTENDED STATUS|$PHP_AUTH_USER|$ip|$stmt|$stmtA|\n");
                            fclose($fp);
                        }
                    }
                }
                if (OSDpreg_match('/modify/',$stage)) {
                    echo "<br><b><font color=$default_text>CUSTOM CAMPAIGN EXTENDED STATUS MODIFIED: $campaign_id - $parents:$status</font></b>\n";
                    $stmt=sprintf("UPDATE osdial_campaign_statuses_extended SET status_name='%s',selectable='%s' WHERE campaign_id='%s' AND parents='%s' AND status='%s';",mres($status_name),mres($selectable),mres($campaign_id),mres($parents),mres($status));
                    $rslt=mysql_query($stmt, $link);

                    ### LOG CHANGES TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|MODIFY CAMPAIGN EXTENDED STATUS|$PHP_AUTH_USER|$ip|$stmt|\n");
                        fclose($fp);
                    }
                }
            }
            $stage='extended';

        } else {
            if (OSDstrlen($campaign_id)<2 or OSDstrlen($status)<1) {
                echo "<br><font color=red>CAMPAIGN STATUS NOT MODIFIED - Please go back and look at the data you entered\n";
                echo "<br>the campaign id needs to be at least 2 characters in length\n";
                echo "<br>the campaign status needs to be at least 1 characters in length</font><br>\n";
            } else {
                if (OSDpreg_match('/delete/',$stage)) {
                    $ocse = get_first_record($link,'osdial_campaign_statuses_extended','count(*) AS cnt',sprintf("campaign_id='%s' AND parents='%s'",mres($campaign_id),mres($status)));
                    if ($ocse['cnt']>0) {
                        echo "<br><center><b><font color=red>CAMPAIGN STATUS NOT DELETED - This status has $ocse[cnt] sub-statuses, please remove them first.</font></b></center><br>\n";
                    } else {
                        echo "<br><b><font color=$default_text>CUSTOM CAMPAIGN STATUS DELETED: $campaign_id - $status</font></b>\n";
                        $stmt=sprintf("DELETE FROM osdial_campaign_statuses WHERE campaign_id='%s' AND status='%s';",mres($campaign_id),mres($status));
                        $rslt=mysql_query($stmt, $link);
                        $stmtA=sprintf("DELETE FROM osdial_campaign_hotkeys WHERE campaign_id='%s' AND status='%s';",mres($campaign_id),mres($status));
                        $rslt=mysql_query($stmtA, $link);

                        ### LOG CHANGES TO LOG FILE ###
                        if ($WeBRooTWritablE > 0) {
                            $fp = fopen ("./admin_changes_log.txt", "a");
                            fwrite ($fp, "$date|DELETE CAMPAIGN STATUS|$PHP_AUTH_USER|$ip|$stmt|$stmtA|\n");
                            fclose($fp);
                        }
                    }
                }
                if (OSDpreg_match('/modify/',$stage)) {
                    echo "<br><b><font color=$default_text>CUSTOM CAMPAIGN STATUS MODIFIED: $campaign_id - $status</font></b>\n";
                    $stmt=sprintf("UPDATE osdial_campaign_statuses SET status_name='%s',selectable='%s',human_answered='%s',category='%s' WHERE campaign_id='%s' AND status='%s';",mres($status_name),mres($selectable),mres($human_answered),mres($category),mres($campaign_id),mres($status));
                    $rslt=mysql_query($stmt, $link);

                    ### LOG CHANGES TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|MODIFY CAMPAIGN STATUS|$PHP_AUTH_USER|$ip|$stmt|\n");
                        fclose($fp);
                    }
                }
            }
        }
        $SUB=22;
        $ADD=31;	# go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=32 display all campaign statuses
######################
if ($ADD==32) {
    echo "<center><br><font color=$default_text size=+1>CUSTOM CAMPAIGN STATUSES</font><br><br>\n";
    echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "<tr class=tabheader>\n";
    echo "<td>CAMPAIGN</td>\n";
    echo "<td>NAME</td>\n";
    echo "<td align=center>STATUSES</td>\n";
    echo "<td align=center>LINKS</td>\n";
    echo "</tr>\n";

    $o=0;
    $ockrh = get_krh($link,'osdial_campaigns','campaign_id,campaign_name','campaign_id DESC',sprintf("campaign_id IN %s",$LOG['allowed_campaignsSQL']),'');
    foreach ($ockrh as $oc) {
        echo "  <tr class=\"row font1\" " . bgcolor($o) . " ondblclick=\"window.location='$PHP_SELF?ADD=31&SUB=22&campaign_id=$oc[campaign_id]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$oc[campaign_id]\">" . mclabel($oc['campaign_id']) . "</a></td>";
        echo "    <td>$oc[campaign_name]</td>";
        echo "    <td align=center>";

        $p=0;
        $ocskrh = get_krh($link,'osdial_campaign_statuses','status','status DESC',sprintf("campaign_id='%s'",mres($os['campaign_id'])),'');
        foreach ($ocskrh as $ocs) {
            echo $ocs['status']." ";
            $p++;
        }
        if ($p<1) {
            echo "      <font color=grey><del>NONE</del></font>\n";
        }
        echo "    </td>";
        echo "    <td align=center><a href=\"$PHP_SELF?ADD=31&SUB=22&campaign_id=$oc[campaign_id]\">MODIFY STATUSES</a></td>\n";
        echo "  </tr>\n";
        $o++;
    }

    echo "<tr class=tabfooter>";
    echo "  <td colspan=4></td>";
    echo "</tr>";
    echo "</table></center>\n";
}


require("campaigns.php");
?>
