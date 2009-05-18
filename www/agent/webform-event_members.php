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
# webform-event_members.php
# 
# Overview:
#  Form is to allow selection of existing customers based on phone number
#  and/or last name.  Upon selection and submission of the form, update
#  each of the selected leads with status indicating they are are member
#  of that event.  Find duplicates of current lead based on vendor_lead_code
#  and of the selected leads based on their vendor_lead_code and update
#  the status of those to evtdup.
#  
# POST vars:
#   DB:        Debug, no write to database.
#   vlc_label: The header label.
#   status:    Status to assign to event members.
#   evtdup:    Status to assign to duplicate members based on vendor_lead_code.
#   method:    Search/Update method, list = current list, campaign = all lists in campaign, system = entire system

require('dbconnect.php');
require("functions.php");
require("../admin/include/variables.php");

$DB = get_variable('DB');

# Set this for Vendor Lead Code label.
$vlc_label = get_variable('vlc_label');
if ($vlc_label == '') {$vlc_label = 'Vendor Lead Code';}

# Field to assign reference lead.
$lead_ref = 'external_key';

# Set this for the status to assign to selected leads.
$status = get_variable('status');
if ($status == '') {$status = 'EVM';}

# Flag duplicate vlc records using update method.
$evtdup = get_variable('evtdup');
#if ($evtdup == '') {$evtdup = 'EVD';}

# Search/Update method, 0 = current list, 1 = all lists in campaign, 2 = entire system
$method = get_variable('method');
if ($method == "system" or $method == 2) {
    $method = 2;
    $smsql = "ols.active='Y' AND ";
} elseif ($method == "campaign" or $method == 1) {
    $method = 1;
    $smsql = "ols.campaign_id='$campaign_id' AND ols.active='Y' AND ";
} else {
    $method = 0;
    $smsql = "ols.list_id='$list_id' AND ols.active='Y' AND ";
}




header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
echo "<html>\n";
echo "<head>\n";
echo "  <title>Event Members</title>\n";
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
    echo "      <td colspan=2 align=center valign=middle height=80 style=\"background-color:#8fb2bb;\"><font color=#1c4754><b>$vlc_label: $vendor_id<br />Phone Number: $phone_number</b></td>\n";
    echo "    </tr><tr>\n";
    echo "      <td align=center colspan=2><font color=#1c4754 size=-1><b>Additional Event Members</b></font></td>\n";
    echo "    </tr><tr>\n";
    echo "      <td align=center><font color=#1c4754 size=-1><b><i>By Number</i></b></font></td>\n";
    echo "      <td align=center><font color=#1c4754 size=-1><b><i>By Last Name</i></b></font></td>\n";
    echo "    </tr><tr>\n";

    # Get list of leads matched by phone_number.
    $pleads = get_krh($link, 'osdial_list AS ol,osdial_lists AS ols', 'ol.*', 'last_name, first_name', $smsql . "phone_number='$phone_number' AND (vendor_lead_code IS NULL OR vendor_lead_code!='$vendor_id') AND ol.list_id=ols.list_id");
    echo "      <td width=50%>\n";
    echo "       <div style=\"background-color:#b0cfd7;height:150px;overflow:scroll;\">\n";
    
    foreach ($pleads as $lead) {
        if ($lead_id != $lead['lead_id'] and ($lead_id == $lead[$lead_ref] or $lead[$lead_ref] == '')) {
            $chk = '';
            if ($lead_id == $lead[$lead_ref]) {
                $chk = 'checked readonly';
            }
            echo "        <font size=-1>";
            echo "        <input type=checkbox name=evtnums[] $chk value=" . $lead['lead_id'] . "> " . $lead['phone_number'] . ' - ' . $lead['last_name'] . ', ' . $lead['first_name'] . ' - ' . $lead['vendor_lead_code'] . "<br />\n";
            echo "        </font>";
        }
    }
    echo "       </div>\n";
    echo "      </td>\n";

    # Get list of leads matched by names.
    $nleads = get_krh($link, 'osdial_list AS ol,osdial_lists AS ols', 'ol.*', 'last_name, first_name', $smsql . "last_name='$last_name' AND last_name IS NOT NULL AND last_name!='' AND (vendor_lead_code IS NULL OR vendor_lead_code!='$vendor_id') AND ol.list_id=ols.list_id");
    echo "      <td width=50%>\n";
    echo "       <div style=\"background-color:#b0cfd7;height:150px;overflow:scroll;\">\n";
    foreach ($nleads as $lead) {
        if ($lead_id != $lead['lead_id'] and $pleads[$lead['lead_id']]['phone_number'] == '' and ($lead_id == $lead[$lead_ref] or $lead[$lead_ref] == '')) {
            $chk = '';
            if ($lead_id == $lead[$lead_ref]) {
                $chk = 'checked readonly';
            } 
            echo "        <font size=-1>";
            echo "        <input type=checkbox name=evtnames[] $chk value=" . $lead['lead_id'] . "> " . $lead['phone_number'] . ' - ' . $lead['last_name'] . ', ' . $lead['first_name'] . ' - ' . $lead['vendor_lead_code'] . "<br />\n";
            echo "        </font>";
        }
    }
    echo "       </div>\n";
    echo "      </td>\n";

    echo "    </tr><tr>\n";
    echo "      <td align=center valign=bottom height=65 colspan=2>\n";
    echo "        <font color=#1c4754 size=-1><b>I attest that the above information is true and correct.</b></font><br />\n";
    echo "        <input type=submit name=submit value=\"Submit Member Data\">";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "  </table>\n";
    echo " </form>\n";

# If submitted, save data.
} else {

    $cnames = get_variable("evtnames");
    $cnums = get_variable("evtnums");
    $cleads = "'" . implode("','",$cnums) . "','" . implode("','",$cnames) . "'";

    # Get members by number and update leads.
    foreach ($cnums as $lead) {
        echo "Updated lead #$lead<br />";
        $stmt="UPDATE osdial_list SET modify_date=NOW(),user='$user',status='$status',$lead_ref='$lead_id' WHERE lead_id='$lead';";
        if ($DB>0) { echo $stmt . "<br />"; } else { $rslt=mysql_query($stmt, $link); }
        $stmt="DELETE FROM osdial_hopper WHERE lead_id='$lead';";
        if ($DB>0) { echo $stmt . "<br />"; } else { $rslt=mysql_query($stmt, $link); }
        # If evtdup is set, look for duplicate party members in lead search method and flag them.
        if ($evtdup != '') {
            $stmt="SELECT vendor_lead_code FROM osdial_list WHERE lead_id='$lead' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0) {
                $stmt="UPDATE osdial_list AS ol, osdial_lists AS ols SET modify_date=NOW(),user='$user',status='$evtdup',$lead_ref='$lead' WHERE " . $smsql . " lead_id NOT IN ($cleads) AND vendor_lead_code='" . $row[0] . "' AND ol.list_id=ols.list_id;";
                if ($DB>0) { echo $stmt . "<br />"; } else { $rslt=mysql_query($stmt, $link); }
            }
        }
    }

    # Get members by name and update leads.
    foreach ($cnames as $lead) {
        echo "Updated lead #$lead<br />";
        $stmt="UPDATE osdial_list SET modify_date=NOW(),user='$user',status='$status',$lead_ref='$lead_id' WHERE lead_id='$lead';";
        if ($DB>0) { echo $stmt . "<br />"; } else { $rslt=mysql_query($stmt, $link); }
        $stmt="DELETE FROM osdial_hopper WHERE lead_id='$lead';";
        if ($DB>0) { echo $stmt . "<br />"; } else { $rslt=mysql_query($stmt, $link); }
        # If evtdup is set, look for duplicate party members in lead search method and flag them.
        if ($evtdup != '') {
            $stmt="SELECT vendor_lead_code FROM osdial_list WHERE lead_id='$lead';";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0) {
                $stmt="UPDATE osdial_list AS ol, osdial_lists AS ols SET modify_date=NOW(),user='$user',status='$evtdup',$lead_ref='$lead' WHERE " . $smsql . " lead_id NOT IN ($cleads) AND vendor_lead_code='" . $row[0] . "' AND ol.list_id=ols.list_id;";
                if ($DB>0) { echo $stmt . "<br />"; } else { $rslt=mysql_query($stmt, $link); }
            }
        }
    }

    # If evtdup is set, look for duplicate party members in lead search method and flag them.
    if ($evtdup != '' and $vendor_id > 0) {
        $stmt="UPDATE osdial_list AS ol, osdial_lists AS ols SET modify_date=NOW(),user='$user',status='$evtdup',$lead_ref='$lead_id' WHERE " . $smsql . " lead_id!='$lead_id' AND vendor_lead_code='$vendor_id' AND ol.list_id=ols.list_id;";
        if ($DB>0) { echo $stmt . "<br />"; } else { $rslt=mysql_query($stmt, $link); }
    }

    # Close the web form panel...
    if ($DB=='') {
        echo "<script language=\"javascript\">\n";
        echo "parent.CloseWebFormPanels();\n";
        echo "</script>\n";
    }
}

echo "</body>\n";
echo "</html>\n";
