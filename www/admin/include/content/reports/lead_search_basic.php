<?php
# lead_search_basic.php
#
# Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

function report_lead_search_basic() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $html = '';

    if ($LOG['modify_leads']==1 and $LOG['user_level'] > 7) {

        $html .= "<center><br><font class=top_header color=$default_text size=4>BASIC LEAD SEARCH</font></center>\n";
        $html .= "<center><font color=$default_text size=2>[ Basic Search ]\n";
        if ($LOG['view_lead_search_advanced']) $html .= "&nbsp;&nbsp;|&nbsp;&nbsp;<a target=\"_parent\" href=\"./admin.php?ADD=999999&SUB=26\">[ Advanced Search ]</a>\n";
        $html .= "<br><br></font></center>\n";
        if (!$vendor_id and !$custom1 and !$custom2 and !$phone and !$lead_id and !$last_name and !$first_name and !$organization and !$organization_title) {
            $html .= "<form method=POST name=search action=\"$PHP_SELF\">\n";
            $html .= "  <input type=hidden name=ADD value=\"$ADD\">\n";
            $html .= "  <input type=hidden name=SUB value=\"$SUB\">\n";
            $html .= "  <input type=hidden name=DB value=\"$DB\">\n";
            $html .= "  <table class=shadedtable width=500 cellspacing=3 align=center>\n";
            $html .= "    <tr class=tabheader>\n";
            $html .= "      <td colspan=2 align=center>Enter Any One Search Term</td>\n";
            $html .= "    </tr>\n";
            $html .= "    <tr bgcolor=$oddrows>\n";
            $html .= "	    <td align=right width=50%>Home Phone:&nbsp;</td>\n";
            $html .= "	    <td align=left width=50%><input type=text name=phone value=\"$phone\"size=10 maxlength=12></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr class=tabheader>\n";
            $html .= "      <td colspan=2></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr bgcolor=$oddrows>\n";
            $html .= "	    <td align=right>Last, First Name:&nbsp;</td>\n";
            $html .= "	    <td align=left><input type=text name=last_name value=\"$last_name\" size=10 maxlength=30><input type=text name=first_name size=10 maxlength=30 value=\"$first_name\"></td>\n";
            $html .= "    </tr>\n";
            $html .= "    <tr class=tabheader>\n";
            $html .= "      <td colspan=2></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr bgcolor=$oddrows>\n";
            $html .= "	    <td align=right>Organization, Title:&nbsp;</td>\n";
            $html .= "	    <td align=left style=\"white-space:nowrap;\"><input type=text name=organization value=\"$organization\" size=20 maxlength=255><input type=text name=organization_title size=10 maxlength=255 value=\"$organization_title\"></td>\n";
            $html .= "    </tr>\n";
            $html .= "    <tr class=tabheader>\n";
            $html .= "      <td colspan=2></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr bgcolor=$oddrows>\n";
            $html .= "      <td align=right>Lead ID:&nbsp;</td>\n";
            $html .= "      <td align=left><input type=text name=lead_id value=\"$lead_id\" size=10 maxlength=10></td>\n";
            $html .= "    </tr>\n";
            $html .= "    <tr class=tabheader>\n";
            $html .= "      <td colspan=2></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr bgcolor=$oddrows>\n";
            $html .= "	    <td align=right>Vendor Lead Code:&nbsp;</td>\n";
            $html .= "	    <td align=left><input type=text name=vendor_id value=\"$vendor_id\" size=20 maxlength=20></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr class=tabheader>\n";
            $html .= "      <td colspan=2></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr bgcolor=$oddrows>\n";
            $html .= "	    <td align=right>Custom1:&nbsp;</td>\n";
            $html .= "	    <td align=left><input type=text name=custom1 value=\"$custom1\" size=20 maxlength=40></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr class=tabheader>\n";
            $html .= "      <td colspan=2></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr bgcolor=$oddrows>\n";
            $html .= "	    <td align=right>Custom2:&nbsp;</td>\n";
            $html .= "	    <td align=left><input type=text name=custom2 value=\"$custom2\" size=20 maxlength=40></td>\n";
            $html .= "    </tr>";
            $html .= "    <tr class=tabfooter>\n";
            $html .= "      <td colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
            $html .= "    </tr>";
            $html .= "  </table>\n";
            $html .= "</form>\n";

        } else {
            $good_query=1;
            if ($last_name and $first_name) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND last_name LIKE '%s' AND first_name LIKE '%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($last_name) . '%', mres($first_name) . '%');
            } elseif ($last_name) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND last_name LIKE '%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($last_name) . '%');
            } elseif ($organization and $organization_title) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND organization LIKE '%s' AND organization_title LIKE '%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($organization) . '%', mres($organization_title) . '%');
            } elseif ($organization) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND organization LIKE '%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($organization) . '%');
            } elseif ($custom1) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND custom1='%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($custom1));
            } elseif ($custom2) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND custom2='%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($custom2));
            } elseif ($vendor_id) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND vendor_lead_code='%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($vendor_id));
            } elseif ($phone) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND phone_number='%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($phone));
            } elseif ($lead_id) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND lead_id='%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($lead_id));
            } else {
                $html .= "ERROR: You must search for something!";
                $good_query=0;
            }

            if ($good_query > 0) {
                $rslt=mysql_query($stmt, $link);
                $results_to_print = mysql_num_rows($rslt);
                if ($results_to_print < 1) {
                    $html .= "<br><br><br>\n";
                    $html .= "<center>\n";
                    $html .= "  <font size=3 color=$default_text>The item(s) you searched for were not found.<br><br>\n";
                    $html .= "  <span class=top_header2><a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB\">Search Again</a></span></font>\n";
                    $html .= "</center>\n";
                } else {
                    $html .= "<center>\n";
                    $html .= "<font color=$default_text size=+1>Found:&nbsp;$results_to_print</font>\n";
                    $html .= "<table class=shadedtable width=$section_width cellpadding=1 cellspacing=0>\n";
                    $html .= "  <tr class=tabheader>\n";
                    $html .= "    <td>#</td>\n";
                    $html .= "    <td>ID</td>\n";
                    $html .= "    <td>Status</td>\n";
                    $html .= "    <td>Vendor</td>\n";
                    $html .= "    <td>Agent</td>\n";
                    $html .= "    <td>List</td>\n";
                    $html .= "    <td>Phone</td>\n";
                    $html .= "    <td>Name</td>\n";
                    $html .= "    <td>Org</td>\n";
                    $html .= "    <td>City</td>\n";
                    $html .= "    <td>Cust1</td>\n";
                    $html .= "    <td>Cust2</td>\n";
                    $html .= "    <td>Last&nbsp;Call</td>\n";
                    $html .= "  </tr>\n";
                    $o=0;
                    while ($results_to_print > $o) {
                        $row=mysql_fetch_row($rslt);
                        $name = $row[15];
                        if (empty($row[15]) and !empty($row[13])) {
                            $name = $row[13];
                        } elseif (!empty($row[15]) and !empty($row[13])) {
                            $name .= ',&nbsp;' . $row[13];
                        }
                        $org = $row[36];
                        if (empty($row[36]) and !empty($row[37])) {
                            $org = $row[37];
                        } elseif (!empty($row[36]) and !empty($row[37])) {
                            $org .= ', ' . $row[37];
                        }
                        $html .= "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=1121&lead_id=$row[0]');\" style=\"white-space:nowrap;\">\n";
                        $html .= "    <td>" . ($o+1) . "</td>\n";
                        $html .= "    <td align=right><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[0]\" target=\"_blank\">$row[0]</a></td>\n";
                        $html .= "    <td align=center>$row[3]</td>\n";
                        $html .= "    <td align=center>$row[5]</td>\n";
                        $html .= "    <td align=center>$row[4]</td>\n";
                        $html .= "    <td align=center>$row[7]</td>\n";
                        $html .= "    <td align=center>$row[11]</td>\n";
                        $html .= "    <td align=center>".ellipse($name, 25, true)."</td>\n";
                        $html .= "    <td align=center>".ellipse($org, 20, true)."</td>\n";
                        $html .= "    <td align=center>".ellipse($row[19], 20, true)."</td>\n";
                        $html .= "    <td align=center>".ellipse($row[28], 20, true)."</td>\n";
                        $html .= "    <td align=center>".ellipse($row[31], 20, true)."</td>\n";
                        $html .= "    <td align=right>". dateToLocal($link,'first',$row[33],$webClientAdjGMT,'',$webClientDST,1) . "</td>\n";
                        $html .= "  </tr>\n";
                        $o++;
                    }
                    $html .= "  <tr class=tabfooter>\n";
                    $html .= "    <td colspan=13></td>\n";
                    $html .= "  </tr>\n";
                    $html .= "</table>\n";
                    $html .= "<br><br><font size=3 color=$default_text><span class=top_header2><a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB\">Search Again</a></span></font>\n";
                    $html .= "</center>\n";
                }
            }
        }
    } else {
        $html .= "<center><font color=red>You do not have permission to view this page</font></center>\n";
    }

    return $html;
}

?>
