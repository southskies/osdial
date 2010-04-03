<?php
# basic_search.php
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

if ($ADD==112) {
    if ($LOGmodify_leads==1 and $LOGuser_level > 7) {

        echo "<center><br><font color=$default_text size=4>SEARCH FOR A LEAD</font><br><br></center>\n";

        if (!$vendor_id and !$custom1 and !$custom2 and !$phone and !$lead_id and !$last_name and !$first_name) {
            echo "<form method=POST name=search action=\"$PHP_SELF\">\n";
            echo "  <input type=hidden name=ADD value=112>\n";
            echo "  <input type=hidden name=DB value=\"$DB\">\n";
            echo "  <table width=500 cellspacing=3 align=center>\n";
            echo "    <tr class=tabheader>\n";
            echo "      <td colspan=2 align=center>Enter Any One Search Term</td>\n";
            echo "    </tr>\n";
            echo "    <tr bgcolor=$oddrows>\n";
            echo "	    <td align=right width=50%>Home Phone:&nbsp;</td>\n";
            echo "	    <td align=left width=50%><input type=text name=phone value=\"$phone\"size=10 maxlength=12></td>\n";
            echo "    </tr>";
            echo "    <tr class=tabheader>\n";
            echo "      <td colspan=2></td>\n";
            echo "    </tr>";
            echo "    <tr bgcolor=$oddrows>\n";
            echo "	    <td align=right>Last, First Name:&nbsp;</td>\n";
            echo "	    <td align=left><input type=text name=last_name value=\"$last_name\" size=10 maxlength=30><input type=text name=first_name size=10 maxlength=30></td>\n";
            echo "    </tr>\n";
            echo "    <tr class=tabheader>\n";
            echo "      <td colspan=2></td>\n";
            echo "    </tr>";
            echo "    <tr bgcolor=$oddrows>\n";
            echo "      <td align=right>Lead ID:&nbsp;</td>\n";
            echo "      <td align=left><input type=text name=lead_id value=\"$lead_id\" size=10 maxlength=10></td>\n";
            echo "    </tr>\n";
            echo "    <tr class=tabheader>\n";
            echo "      <td colspan=2></td>\n";
            echo "    </tr>";
            echo "    <tr bgcolor=$oddrows>\n";
            echo "	    <td align=right>Vendor Lead Code:&nbsp;</td>\n";
            echo "	    <td align=left><input type=text name=vendor_id value=\"$vendor_id\" size=20 maxlength=20></td>\n";
            echo "    </tr>";
            echo "    <tr class=tabheader>\n";
            echo "      <td colspan=2></td>\n";
            echo "    </tr>";
            echo "    <tr bgcolor=$oddrows>\n";
            echo "	    <td align=right>Custom1:&nbsp;</td>\n";
            echo "	    <td align=left><input type=text name=custom1 value=\"$custom1\" size=20 maxlength=40></td>\n";
            echo "    </tr>";
            echo "    <tr class=tabheader>\n";
            echo "      <td colspan=2></td>\n";
            echo "    </tr>";
            echo "    <tr bgcolor=$oddrows>\n";
            echo "	    <td align=right>Custom2:&nbsp;</td>\n";
            echo "	    <td align=left><input type=text name=custom2 value=\"$custom2\" size=20 maxlength=40></td>\n";
            echo "    </tr>";
            echo "    <tr class=tabfooter>\n";
            echo "      <td colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td>\n";
            echo "    </tr>";
            echo "  </table>\n";
            echo "</form>\n";

        } else {
            $good_query=1;
            if ($last_name and $first_name) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND last_name LIKE '%s' AND first_name LIKE '%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($last_name) . '%', mres($first_name) . '%');
            } elseif ($last_name) {
                $stmt = sprintf("SELECT * FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND campaign_id IN %s AND last_name LIKE '%s' ORDER BY modify_date DESC LIMIT 1000;", $LOG['allowed_campaignsSQL'], mres($last_name) . '%');
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
                echo "ERROR: You must search for something!";
                $good_query=0;
            }

            if ($good_query > 0) {
                $rslt=mysql_query($stmt, $link);
                $results_to_print = mysql_num_rows($rslt);
                if ($results_to_print < 1) {
                    echo "<br><br><br>\n";
                    echo "<center>\n";
                    echo "  <font size=3 color=$default_text>The item(s) you searched for were not found.<br><br>\n";
                    echo "  <a href=\"$PHP_SELF?ADD=112\">Search Again</a></font>\n";
                    echo "</center>\n";
                } else {
                    echo "<center>\n";
                    echo "<font color=$default_text size=+1>Found:&nbsp;$results_to_print</font>\n";
                    echo "<table width=$section_width cellpadding=1 cellspacing=0>\n";
                    echo "  <tr class=tabheader>\n";
                    echo "    <td>#</td>\n";
                    echo "    <td>Lead&nbsp;ID</td>\n";
                    echo "    <td>Status</td>\n";
                    echo "    <td>Vendor&nbsp;ID</td>\n";
                    echo "    <td>Last Agent</td>\n";
                    echo "    <td>List&nbsp;ID</td>\n";
                    echo "    <td>Phone</td>\n";
                    echo "    <td>Name</td>\n";
                    echo "    <td>City</td>\n";
                    echo "    <td>Custom1</td>\n";
                    echo "    <td>Custom2</td>\n";
                    echo "    <td>Last&nbsp;Call</td>\n";
                    echo "  </tr>\n";
                    $o=0;
                    while ($results_to_print > $o) {
                        $row=mysql_fetch_row($rslt);
                        if (eregi("1$|3$|5$|7$|9$", $o)) {
                            $bgcolor='bgcolor='.$oddrows;
                        } else {
                            $bgcolor='bgcolor='.$evenrows;
                        }
                        echo "  <tr $bgcolor class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=1121&lead_id=$row[0]');\">\n";
                        echo "    <td>" . ($o+1) . "</td>\n";
                        echo "    <td><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[0]\" target=\"_blank\">$row[0]</a></td>\n";
                        echo "    <td>$row[3]</td>\n";
                        echo "    <td>$row[5]</td>\n";
                        echo "    <td>$row[4]</td>\n";
                        echo "    <td>$row[7]</td>\n";
                        echo "    <td>$row[11]</td>\n";
                        echo "    <td>$row[13]&nbsp;$row[15]</td>\n";
                        echo "    <td>$row[19]</td>\n";
                        echo "    <td>$row[28]</td>\n";
                        echo "    <td>$row[31]</td>\n";
                        echo "    <td>$row[33]</td>\n";
                        echo "  </tr>\n";
                        $o++;
                    }
                    echo "  <tr class=tabfooter>\n";
                    echo "    <td colspan=12></td>\n";
                    echo "  </tr>\n";
                    echo "</table>\n";
                    echo "</center>\n";
                }
            }
        }
    } else {
        echo "<center><font color=red>You do not have permission to view this page</font></center>\n";
    }
}

?>
