<?
# 
#
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
#
# 
# Overview:
#  Form is to allow selection of existing customers based on phone number
#  and/or last name.  Upon selection and submission of the form, update
#  each of the selected leads with status indicating they are are member
#  of that event.
#  

require('dbconnect.php');
require("functions.php");
require("../admin/include/variables.php");


# Set this for Vendor Lead Code label.
$vlc_label = 'Vendor Lead Code';
# Field to assign reference lead.
$lead_ref = 'external_key';
# Set this for the status to assign to selected leads.
$ref_stat = 'ECM';
# Search method, 0 = current list, 1 = all lists in campaign, 2 = entire system
$search_method = 0;
# Update method


# Setup SQL for desired search method.
$smsql = "list_id='$list_id' AND ";
if ($search_method == 1) {
    $smsql = "campaign_id='$campaign_id' AND ";
} elseif ($search_method == 2) {
    $smsql = "1=1 AND ";
}



header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
echo "<html>\n";
echo "<head>\n";
echo "  <title>Event Reconfirmation</title>\n";
echo "</head>\n";
echo "<body bgcolor=#759ba3 style=\"font-family: 'dejavu sans',sans;\"marginheight=0 marginwidth=0 name=wfer>\n";



# Authenticate
$stmt="SELECT count(*) from osdial_users where user='$user' and pass='$pass' and user_level > 0;";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];
if ($auth == 0) {
    echo "Bad User/Pass";
    exit;
}

# If not submitted, display form.
if (!$submit) {
    echo " <form name=wferform id=wferform action=\"$PHP_SELF\" method=post>\n";
    foreach ($GLOBALS as $key => $val) { if ($val != '') { echo " <input type=hidden name=\"$key\" value=\"$val\">\n"; } }
    echo "  <table width=100% border=0>\n";
    echo "    <tr>\n";
    echo "      <td colspan=2 align=center valign=middle height=80 style=\"background-color:#8fb2bb;\"><font color=red><b>$vlc_label: $vendor_lead_code<br />Phone Number: $phone_number</b></td>\n";
    echo "    </tr><tr>\n";
    echo "      <td align=center colspan=2><font size=-1><b>Additional Confirmations</b></font></td>\n";
    echo "    </tr><tr>\n";
    echo "      <td align=center><font size=-1><b><i>By Number</i></b></font></td>\n";
    echo "      <td align=center><font size=-1><b><i>By Last Name</i></b></font></td>\n";
    echo "    </tr><tr>\n";

    # Get list of leads matched by phone_number.
    $pleads = get_krh($link, 'osdial_list', '*', 'last_name, first_name', $smsql . "phone_number='$phone_number'");
    echo "      <td width=50%>\n";
    echo "       <div style=\"background-color:#b0cfd7;height:150px;overflow:scroll;\">\n";
    
    foreach ($pleads as $lead) {
        if ($lead_id != $lead['lead_id'] and ($lead_id == $lead[$lead_ref] or $lead[$lead_ref] == '')) {
            $chk = '';
            if ($lead_id == $lead[$lead_ref]) {
                $chk = 'checked readonly';
            } 
            echo "        <font size=-1>";
            echo "        <input type=hidden name=confnumsvlc" . $lead['lead_id'] . "[] value=" . $lead['vendor_lead_code'] . ">\n";
            echo "        <input type=checkbox name=confnums[] $chk value=" . $lead['lead_id'] . "> " . $lead['phone_number'] . ' - ' . $lead['last_name'] . ', ' . $lead['first_name'] . ' - ' . $lead['vendor_lead_code'] . "<br />\n";
            echo "        </font>";
        }
    }
    echo "       </div>\n";
    echo "      </td>\n";

    # Get list of leads matched by names.
    $nleads = get_krh($link, 'osdial_list', '*', 'last_name, first_name', $smsql . "last_name='$last_name' AND last_name IS NOT NULL");
    echo "      <td width=50%>\n";
    echo "       <div style=\"background-color:#b0cfd7;height:150px;overflow:scroll;\">\n";
    foreach ($nleads as $lead) {
        if ($lead_id != $lead['lead_id'] and $pleads[$lead['lead_id']]['phone_number'] == '' and ($lead_id == $lead[$lead_ref] or $lead[$lead_ref] == '')) {
            $chk = '';
            if ($lead_id == $lead[$lead_ref]) {
                $chk = 'checked readonly';
            } 
            echo "        <font size=-1>";
            echo "        <input type=hidden name=confnamesvlc" . $lead['lead_id'] . "[] value=" . $lead['vendor_lead_code'] . ">\n";
            echo "        <input type=checkbox name=confnames[] $chk value=" . $lead['lead_id'] . "> " . $lead['phone_number'] . ' - ' . $lead['last_name'] . ', ' . $lead['first_name'] . ' - ' . $lead['vendor_lead_code'] . "<br />\n";
            echo "        </font>";
        }
    }
    echo "       </div>\n";
    echo "      </td>\n";

    echo "    </tr><tr>\n";
    echo "      <td align=center valign=bottom height=65 colspan=2>\n";
    echo "        <font size=-1><b>I attest that the above information is true and correct.</b></font><br />\n";
    echo "        <input type=submit name=submit value=\"Submit Confirm Data\">";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "  </table>\n";
    echo " </form>\n";

# If submitted, save data.
} else {
    # Get confirms by number and update leads.
    $cnums = get_variable("confnums");
    foreach ($cnums as $lead) {
        echo "Updated lead #$lead<br />";
        $stmt="UPDATE osdial_list SET modify_date=NOW(),user='$user',status='$ref_stat',$lead_ref='$lead_id' WHERE lead_id='$lead';";
        $rslt=mysql_query($stmt, $link);
        $stmt="DELETE FROM osdial_hopper WHERE lead_id='$lead';";
        $rslt=mysql_query($stmt, $link);
    }

    $cnames = get_variable("confnames");
    foreach ($cnames as $lead) {
        echo "Updated lead #$lead<br />";
        $stmt="UPDATE osdial_list SET modify_date=NOW(),user='$user',status='$ref_stat',$lead_ref='$lead_id' WHERE lead_id='$lead';";
        $rslt=mysql_query($stmt, $link);
        $stmt="DELETE FROM osdial_hopper WHERE lead_id='$lead';";
        $rslt=mysql_query($stmt, $link);
    }

    # 
    echo "<script language=\"javascript\">\n";
    #echo "parent.document.getElementById('WebFormPanel2').style.visibility = 'hidden';\n";
    echo "parent.CloseWebFormPanels();\n";
    echo "</script>\n";
    
}

echo "</body>\n";
echo "</html>\n";
