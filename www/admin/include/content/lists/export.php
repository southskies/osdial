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
# 090410-1137 - Added custom2 field
# 090410-1137 - Added external_key field


######################
# ADD=131 Lead Export
######################

if ($ADD==131 && $SUB==2 && $LOGuser_level > 8 && $LOGexport_leads > 0) {

    $swhere = '(';
    foreach ($statuses as $stat) {
        if ($stat == '-CALLED-')
            $scall = 1;
        if ($stat == '-ALL-')
            $sall = 1;
        $swhere .= "status='" . $stat . "' OR ";
    }
    if ($scall) {
        $swhere = "list_id='" . $list_id . "' AND status!='NEW'";
    } elseif ($sall) {
        $swhere = "list_id='" . $list_id . "'";
    } else {
        $swhere = "list_id='" . $list_id . "' AND " . chop($swhere, ' OR') . ")";
    }

    foreach ($fields as $field) {
        if ($field == '-ALL-')
            $fall = 1;
        $sfield .= $field . ",";
    }
    $sfield = chop($sfield,',');
    if ($fall) {
        $sfield = 'lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,';
        $sfield .= 'phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,';
        $sfield .= 'postal_code,country_code,gender,date_of_birth,alt_phone,email,custom1,comments,called_count,custom2,external_key';
    }

    echo $sfield . "\r\n";
    $ffield = explode(',',$sfield);

    $leads = get_krh($link,'osdial_list',$sfield,'',$swhere);
    if (is_array($leads)) {
        foreach ($leads as $lead) {
            $output = '';
            foreach ($ffield as $field) {
                $output .= '"' . eregi_replace("\n","",strtr($lead[$field],'"','')) . '",';
            }
            echo chop($output,',') . "\r\n";
        }
    }
        

} elseif ($ADD==131 && $LOGuser_level > 8 && $LOGexport_leads > 0) {

	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	echo "<center><br><font color=$default_text size=+1>LEAD EXPORT</font><br><br>\n";

	if ($LOGmodify_lists==1) {
        if ($statuses[0] != '') $target=' target="_blank"';
        echo "<form name=export action=$PHP_SELF method=post$target>\n";
        echo "<input type=hidden name='ADD' value=\"$ADD\">\n";
        echo "<table align=center width=700 border=0 cellpadding=5 cellspacing=0 bgcolor=$oddrows>\n";
        echo " <tr><td>\n";
        echo "  <center>\n";
        echo "  <br />";

        if ($list_id == '') {
            $lists = get_krh($link,'osdial_lists','*','','');
            echo "  <b>Select List ID</b>\n";
            echo "  <br />";
            echo "  <br />";
            echo "  <select name=list_id>";
            sort($lists);
            foreach ($lists as $list) {
                echo '      <option value="' . $list['list_id'] . '">' . $list['list_id'] . ' - ' . $list['list_name'] . '</option>';
            }
            echo "  </select>\n";
            echo "  <br />\n";
            echo "  <br />\n";
            echo " </td></tr>\n";
            echo " <tr class=tabfooter><td class=tabbutton>\n";
            echo "  <input type=submit value=\"Next ->\">\n";
        } elseif ($statuses[0] == '') {
            $list = get_first_record($link,'osdial_lists','*',"list_id='" . $list_id . "'");
            echo "  <input type=hidden name='list_id' value=\"$list_id\">\n";
            echo "  <b>List ID:</b> " . $list_id . ' - ' . $list['list_name'] .  ' - ' . $list['campaign_id'] . "\n";
            echo "  <br />\n";
            echo "  <hr width='75%'>\n";
            echo "  <br />\n";
            echo "  <b>Select Statuses to Include in File:</b>\n";
            echo "  <br />\n";
            echo "  <br />\n";

            $sstats = get_krh($link,'osdial_statuses','*','','');
            $cstats = get_krh($link,'osdial_campaign_statuses','*','',"campaign_id='" . $list['campaign_id'] . "'");
            foreach ($sstats as $stat) {
                $stats[$stat['status']] = $stat;
            }
            foreach ($cstats as $stat) {
                $stats[$stat['status']] = $stat;
            }
            sort($stats);

            $unchkjs = '';
            foreach ($stats as $stat) {
                $unchkjs .= "document.getElementById('" . $stat['status'] . "').checked=false;";
            }
            echo "  <table align=center border=0 cellpadding=1 cellspacing=0 bgcolor=$oddrows>\n";
            echo "  <tr class=tabheader>\n";
            echo "      <td>&nbsp;</td>\n";
            echo "      <td>STATUS</td>\n";
            echo "      <td>DESCRIPTION</td>\n";
            echo "   </tr>\n";
            echo "   <tr class=font2>\n";
            echo '      <td align=right><input onclick="' . "document.getElementById('called').checked=false;" . $unchkjs .'" type=checkbox name=statuses[] id=all value="-ALL-" checked></td>' . "\n";
            echo "      <td><label for=all><b>ALL</b></label></td>\n";
            echo "      <td><label for=all><b>- Export ALL statuses</b></label></td>\n";
            echo "   </tr>\n";
            echo "  <tr><td colspan=3><hr></td></tr>\n";
            echo "   <tr class=font2>\n";
            echo '      <td align=right><input onclick="' . "document.getElementById('all').checked=false;" . $unchkjs .'" type=checkbox name=statuses[] id=called value="-CALLED-"></td>' . "\n";
            echo "      <td><label for=called><b>CALLED</b></label></td>\n";
            echo "      <td><label for=called><b>- Export CALLED statuses</b> <font size=1>(ALL excluding NEW leads)</font></label></td>\n";
            echo "   </tr>\n";
            echo "  <tr><td colspan=3><hr></td></tr>\n";
            foreach ($stats as $stat) {
                echo "   <tr>\n";
                echo '      <td align=right><input onclick="' . "document.getElementById('called').checked=false;document.getElementById('all').checked=false;" . '" type=checkbox name=statuses[] id="' . $stat['status'] . '" value="' . $stat['status']. "\"></td>\n";
                echo '      <td><label for=' . $stat['status'] . '><font size=2>' . $stat['status'] . "</font></label></td>\n";
                echo '      <td><label for=' . $stat['status'] . '><font size=2>- ' . $stat['status_name'] . "</font></label></td>\n";
                echo "   </tr>\n";
            }
            echo "  </table>\n";
            echo "  <br />\n";
            echo "  <br />\n";
            echo " </td></tr>\n";
            echo " <tr class=tabfooter><td class=tabbutton>\n";
            echo "  <input type=submit value=\"Next ->\">\n";
        } elseif ($fields[0] == '') {
            $list = get_first_record($link,'osdial_lists','*',"list_id='" . $list_id . "'");
            echo "  <input type=hidden name='SUB' value=\"2\">\n";
            echo "  <input type=hidden name='list_id' value=\"$list_id\">\n";
            foreach ($statuses as $stat) {
                echo "  <input type=hidden name='statuses[]' value=\"$stat\">\n";
            }
            echo "  <b>List ID:</b> " . $list_id . ' - ' . $list['list_name'] .  ' - ' . $list['campaign_id'] . "\n";
            echo "  <br />\n";
            echo "  <br>Statuses:</b> \n";
            foreach ($statuses as $stat) {
                echo "  " . $stat;
            }
            echo "  <hr width='75%'>\n";
            echo "  <br />\n";
            echo "  <b>Select Fields to Include in File:</b>\n";
            echo "  <br />\n";
            echo "  <br />\n";

            echo "  <table align=center border=0 cellpadding=1 cellspacing=0 bgcolor=$oddrows>\n";
            echo "  <tr class=tabheader>\n";
            echo "      <td>&nbsp;</td>\n";
            echo "      <td>FIELD</td>\n";
            echo "      <td>DESCRIPTION</td>\n";
            echo "   </tr>\n";

            $flds['lead_id'] = "Unique lead number";
            $flds['entry_date'] = "The date/time loaded";
            $flds['modify_date'] = "The date/time last modified";
            $flds['status'] = "The last status/disposition on lead";
            $flds['user'] = "The last agent to call";
            $flds['vendor_lead_code'] = "Vendor Code";
            $flds['list_id'] = "List ID";
            $flds['gmt_offset_now'] = "Timezone (GMT offset)";
            $flds['called_since_last_reset'] = "Flag indicating call-count since last reset";
            $flds['phone_code'] = "Phone Code";
            $flds['phone_number'] = "Phone Number";
            $flds['alt_phone'] = "Alternate Phone Number / Phone2";
            $flds['title'] = "Title";
            $flds['first_name'] = "First Name";
            $flds['middle_initial'] = "Middle Initial";
            $flds['last_name'] = "Last Name";
            $flds['address1'] = "Address 1";
            $flds['address2'] = "Address 2";
            $flds['address3'] = "Address 3 / Phone3";
            $flds['city'] = "City";
            $flds['state'] = "State";
            $flds['province'] = "Province";
            $flds['postal_code'] = "Postal Code / ZIP";
            $flds['country_code'] = "Country Code";
            $flds['Gender'] = "Gender";
            $flds['date_of_birth'] = "Birth Date";
            $flds['email'] = "Email Address";
            $flds['custom1'] = "Custom Field 1";
            $flds['custom2'] = "Custom Field 2";
            $flds['external_key'] = "External Key";
            $flds['comments'] = "Comments";
            $flds['called_count'] = "# of Times Called";
            $flds['last_local_call_time'] = "Local Date/Time of Last Call";
            $flds['cost'] = "Cost of Lead";
            $flds['post_date'] = "Post Date";

            foreach ($flds as $k => $v) {
                $unchkjs .= "document.getElementById('" . $k . "').checked=false;";
            }

            echo "   <tr class=font2>\n";
            echo '      <td align=right><input onclick="' . $unchkjs . '" type=checkbox name=fields[] id=ALL value="-ALL-" checked></td>' . "\n";
            echo "      <td><label for=ALL><b>ALL</b></label></td>\n";
            echo "      <td><label for=ALL><b>- Export ALL fields</b></label></td>\n";
            echo "   </tr>\n";
            echo "  <tr><td colspan=3><hr></td></tr>\n";

            foreach ($flds as $k => $v) {
                echo "   <tr class=font2>\n";
                echo '      <td align=right><input onclick="' . "document.getElementById('ALL').checked=false;" . '" type=checkbox name=fields[] id="' . $k . '" value="' . $k . '"></td>' . "\n";
                echo "      <td><label for=$k>$k</label></td>\n";
                echo "      <td><label for=$k>- $v</label></td>\n";
                echo "   </tr>\n";
            }





            echo "  </table>\n";
            echo "  <br />\n";
            echo "  <br />\n";
            echo " </td></tr>\n";
            echo " <tr class=tabfooter><td class=tabbutton>\n";
            echo "  <input type=submit value=\"Get File\" onclick=\"window.location='" . $PHP_SELF . "?ADD=100'\">\n";
        } else {
	        echo "<font color=red>Unexpected Error!</font>\n";
        }


        echo " </td></tr>\n";
        echo "</table>\n";
        echo "</form>\n";
        echo "</center>\n";
    } else {
	    echo "<font color=red>You do not have permission to view this page</font>\n";
    }

}


?>
