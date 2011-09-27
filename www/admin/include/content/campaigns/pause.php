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
# ADD=27 adds the new campaign agent pause code entry to the system
######################

if ($ADD==27) {
    if ($LOG['modify_campaigns'] == 1) {
        $stmt=sprintf("SELECT count(*) FROM osdial_pause_codes WHERE campaign_id='%s' AND pause_code='%s';",mres($campaign_id),mres($pause_code));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>AGENT PAUSE CODE NOT ADDED - there is already an entry for this campaign with this pause code</font>\n";
        } else {
            if (OSDstrlen($campaign_id) < 2 or OSDstrlen($pause_code) < 1 or OSDstrlen($pause_code) > 6 or OSDstrlen($pause_code_name) < 2) {
                echo "<br><font color=red>AGENT PAUSE CODE NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>pause code must be between 1 and 6 characters in length\n";
                echo "<br>pause code name must be between 2 and 30 characters in length</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>AGENT PAUSE CODE ADDED: $campaign_id - $pause_code - $pause_code_name</font></b>\n";
    
                $stmt=sprintf("INSERT INTO osdial_pause_codes (campaign_id,pause_code,pause_code_name,billable) VALUES ('%s','%s','%s','%s');",mres($campaign_id),mres($pause_code),mres($pause_code_name),mres($billable));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW AGENT PAUSE CODE|$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }
        $SUB=27;
        $ADD=31;
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=47 modify agent pause code in the system
######################

if ($ADD==47) {
    if ($LOG['modify_campaigns'] == 1) {
        if (OSDstrlen($campaign_id) < 2 or OSDstrlen($pause_code) < 1 or OSDstrlen($pause_code) > 6 or OSDstrlen($pause_code_name) < 2) {
            echo "<br><font color=red>AGENT PAUSE CODE NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>pause_code must be between 1 and 6 characters in length\n";
            echo "<br>pause_code name must be between 2 and 30 characters in length</font><br>\n";
        } else {
            echo "<br><b><font color=$default_text>AGENT PAUSE CODE MODIFIED: $campaign_id - $pause_code - $pause_code_name</font></b>\n";

            $stmt=sprintf("UPDATE osdial_pause_codes SET pause_code_name='%s',billable='%s' WHERE campaign_id='%s' AND pause_code='%s';",mres($pause_code_name),mres($billable),mres($campaign_id),mres($pause_code));
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY AGENT PAUSECODE|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $SUB=27;
        $ADD=31;    # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=67 delete agent pause code in the system
######################

if ($ADD==67) {
    if ($LOG['modify_campaigns'] == 1) {
        if (OSDstrlen($campaign_id) < 2 or OSDstrlen($pause_code) < 1) {
            echo "<br><font color=red>CAMPAIGN PAUSE CODE NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>pause code must be between 1 and 6 characters in length</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>CAMPAIGN PAUSE CODE DELETED: $campaign_id - $pause_code</font></B>\n";

            $stmt=sprintf("DELETE FROM osdial_pause_codes WHERE campaign_id='%s' AND pause_code='%s';",mres($campaign_id),mres($pause_code));
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|DELETE AGENT PAUSECODE|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $SUB=27;
        $ADD=31;    # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=37 display all campaign agent pause codes
######################
if ($ADD==37) {
    echo "<center>\n";
    echo "  <br><font color=$default_text size=+1>CAMPAIGN AGENT PAUSE CODES</font><br><br>\n";
    echo "  <table width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "    <tr class=tabheader>\n";
    echo "      <td>CAMPAIGN</td>\n";
    echo "      <td>NAME</td>\n";
    echo "      <td>PAUSE CODES</td>\n";
    echo "      <td align=center>LINKS</td>\n";
    echo "    </tr>\n";

    $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE campaign_id IN %s ORDER BY campaign_id;", $LOG['allowed_campaignsSQL']);
    $rslt=mysql_query($stmt, $link);
    $campaigns_to_print = mysql_num_rows($rslt);

    $o=0;
    while ($campaigns_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        $campaigns_id_list[$o] = $row[0];
        $campaigns_name_list[$o] = $row[1];
        $o++;
    }

    $o=0;
    while ($campaigns_to_print > $o) {
        echo "    <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31&SUB=27&campaign_id=$campaigns_id_list[$o]';\">\n";
        echo "      <td><a href=\"$PHP_SELF?ADD=31&SUB=27&campaign_id=$campaigns_id_list[$o]\">" . mclabel($campaigns_id_list[$o]) . "</a></td>\n";
        echo "      <td>$campaigns_name_list[$o]</td>\n";
        echo "      <td>";

        $stmt=sprintf("SELECT pause_code FROM osdial_pause_codes WHERE campaign_id='%s' ORDER BY pause_code;",mres($campaigns_id_list[$o]));
        $rslt=mysql_query($stmt, $link);
        $campstatus_to_print = mysql_num_rows($rslt);
        $p=0;
        while ($campstatus_to_print > $p and $p < 10) {
            $row=mysql_fetch_row($rslt);
            echo "$row[0] ";
            $p++;
        }
        if ($p<1) echo "        <font color=grey><DEL>NONE</DEL></font>";
        echo "      </td>\n";
        echo "      <td align=center><a href=\"$PHP_SELF?ADD=31&SUB=27&campaign_id=$campaigns_id_list[$o]\">MODIFY PAUSE CODES</a></td>\n";
        echo "    </tr>\n";
        $o++;
    }

    echo "    <tr class=tabfooter>\n";
    echo "      <td colspan=4></td>\n";
    echo "    </tr>\n";
    echo "  </table>\n";
    echo "</center>\n";
}

require("campaigns.php");
?>
