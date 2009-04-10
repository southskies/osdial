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


######################
# ADD=131 Lead Export
######################

if ($ADD==131 && $SUB==2) {

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
        $sfield .= 'postal_code,country_code,gender,date_of_birth,alt_phone,email,custom1,comments,called_count,custom2';
    }

    echo $sfield . "\r\n";
    $ffield = explode(',',$sfield);

    $leads = get_krh($link,'osdial_list',$sfield,'',$swhere);
    if (is_array($leads)) {
        foreach ($leads as $lead) {
            $output = '';
            foreach ($ffield as $field) {
                $output .= '"' . strtr($lead[$field],'"','') . '",';
            }
            echo chop($output,',') . "\r\n";
        }
    }
        

} elseif ($ADD==131) {

	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	echo "<center><br><font color=navy size=+1>LEAD EXPORT</font><br><br>\n";

	if ($LOGmodify_lists==1) {
        if ($statuses[0] != '') $target=' target="_blank"';
        echo "<form action=$PHP_SELF method=post$target>\n";
        echo "<input type=hidden name='ADD' value=\"$ADD\">\n";
        echo "<table align=center width=700 border=0 cellpadding=5 cellspacing=0 bgcolor=#C1D6DF>\n";
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

            echo "  <table align=center border=0 cellpadding=2 cellspacing=0 bgcolor=#C1D6DF>\n";
            echo "  <tr bgcolor=$menubarcolor>\n";
            echo "      <td><font size=1 color=white>&nbsp;</font></td>\n";
            echo "      <td><font size=1 color=white><b>STATUS</b></font></td>\n";
            echo "      <td><font size=1 color=white><b>DESCRIPTION</b></font></td>\n";
            echo "   </tr>\n";
            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=statuses[] value="-CALLED-" checked></td>' . "\n";
            echo "      <td><b>CALLED</b></td>\n";
            echo "      <td><b> - Export CALLED statuses</b> <font size=1>(ALL excluding NEW leads)</font></td>\n";
            echo "   </tr>\n";
            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=statuses[] value="-ALL-"></td>' . "\n";
            echo "      <td><b>ALL</b></td>\n";
            echo "      <td><b> - Export ALL statuses</b></td>\n";
            echo "   </tr>\n";
            echo "  <tr><td colspan=3><hr></td></tr>\n";
            foreach ($stats as $stat) {
                echo "   <tr>\n";
                echo '      <td align=right><input type=checkbox name=statuses[] value="' . $stat['status']. "\"></td>\n";
                echo '      <td>' . $stat['status'] . "</td>\n";
                echo '      <td> - ' . $stat['status_name'] . "</td>\n";
                echo "   </tr>\n";
            }
            echo "  </table>\n";
            echo "  <br />\n";
            echo "  <br />\n";
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

            echo "  <table align=center border=0 cellpadding=2 cellspacing=0 bgcolor=#C1D6DF>\n";
            echo "  <tr bgcolor=$menubarcolor>\n";
            echo "      <td><font size=1 color=white>&nbsp;</font></td>\n";
            echo "      <td><font size=1 color=white><b>FIELD</b></font></td>\n";
            echo "      <td><font size=1 color=white><b>DESCRIPTION</b></font></td>\n";
            echo "   </tr>\n";
            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="-ALL-" checked></td>' . "\n";
            echo "      <td><b>ALL</b></td>\n";
            echo "      <td><b> - Export ALL fields</b></td>\n";
            echo "   </tr>\n";
            echo "  <tr><td colspan=3><hr></td></tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="lead_id"></td>' . "\n";
            echo "      <td>lead_id</td>\n";
            echo "      <td> - Unique lead numbertd>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="entry_date"></td>' . "\n";
            echo "      <td>entry_date</td>\n";
            echo "      <td> - The date/time loaded</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="modify_date"></td>' . "\n";
            echo "      <td>modify_date</td>\n";
            echo "      <td> - Last call/modify date/time</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="status"></td>' . "\n";
            echo "      <td>status</td>\n";
            echo "      <td> - The status/disposition of lead</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="user"></td>' . "\n";
            echo "      <td>user</td>\n";
            echo "      <td> - Last agent</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="vendor_lead_code"></td>' . "\n";
            echo "      <td>vendor_lead_code</td>\n";
            echo "      <td> - Vendor assigned lead number</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="source_id"></td>' . "\n";
            echo "      <td>source_id</td>\n";
            echo "      <td> - Source ID</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="list_id"></td>' . "\n";
            echo "      <td>list_id</td>\n";
            echo "      <td> - List ID</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="gmt_offset_now"></td>' . "\n";
            echo "      <td>gmt_offset_now</td>\n";
            echo "      <td> - Timezone (GMT offset)</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="called_since_last_reset"></td>' . "\n";
            echo "      <td>called_since_last_reset</td>\n";
            echo "      <td> - Flag for calls since last reset</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="phone_code"></td>' . "\n";
            echo "      <td>phone_code</td>\n";
            echo "      <td> - Phone code</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="phone_number"></td>' . "\n";
            echo "      <td>phone_number</td>\n";
            echo "      <td> - Phone number</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="title"></td>' . "\n";
            echo "      <td>title</td>\n";
            echo "      <td> - Title</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="first_name"></td>' . "\n";
            echo "      <td>first_name</td>\n";
            echo "      <td> - First name</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="middle_initial"></td>' . "\n";
            echo "      <td>middle_initial</td>\n";
            echo "      <td> - Middle Initial</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="last_name"></td>' . "\n";
            echo "      <td>last_name</td>\n";
            echo "      <td> - Last name</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="address1"></td>' . "\n";
            echo "      <td>address1</td>\n";
            echo "      <td> - Address line 1</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="address2"></td>' . "\n";
            echo "      <td>address2</td>\n";
            echo "      <td> - Address line 2</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="address3"></td>' . "\n";
            echo "      <td>address3</td>\n";
            echo "      <td> - Address line 3 / Alt Phone 3</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="city"></td>' . "\n";
            echo "      <td>city</td>\n";
            echo "      <td> - City</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="state"></td>' . "\n";
            echo "      <td>state</td>\n";
            echo "      <td> - State</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="province"></td>' . "\n";
            echo "      <td>province</td>\n";
            echo "      <td> - Province</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="postal_code"></td>' . "\n";
            echo "      <td>postal_code</td>\n";
            echo "      <td> - ZIP / Postal code</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="country_code"></td>' . "\n";
            echo "      <td>country_code</td>\n";
            echo "      <td> - Country code</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="gender"></td>' . "\n";
            echo "      <td>gender</td>\n";
            echo "      <td> - Gender</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="date_of_birth"></td>' . "\n";
            echo "      <td>date_of_birth</td>\n";
            echo "      <td> - Date of birth</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="alt_phone"></td>' . "\n";
            echo "      <td>alt_phone</td>\n";
            echo "      <td> - Alternate phone number</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="email"></td>' . "\n";
            echo "      <td>email</td>\n";
            echo "      <td> - Email address</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="custom1"></td>' . "\n";
            echo "      <td>custom1</td>\n";
            echo "      <td> - Custom1</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="custom2"></td>' . "\n";
            echo "      <td>custom2</td>\n";
            echo "      <td> - Custom2</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="comments"></td>' . "\n";
            echo "      <td>comments</td>\n";
            echo "      <td> - Comments</td>\n";
            echo "   </tr>\n";

            echo "   <tr>\n";
            echo '      <td align=right><input type=checkbox name=fields[] value="called_count"></td>' . "\n";
            echo "      <td>called_count</td>\n";
            echo "      <td> - Times call attempted</td>\n";
            echo "   </tr>\n";

            echo "  </table>\n";
            echo "  <br />\n";
            echo "  <br />\n";
            echo "  <input type=submit value=\"Get File\" onclick=\"window.location='" . $PHP_SELF . "?ADD=100'\">\n";
        } else {
	        echo "<font color=red>Unexpected Error!</font>\n";
        }


        echo "  <br />";
        echo "  </center>\n";
        echo " <tr><td>\n";
        echo "</table>\n";
        echo "</form>\n";
        echo "</center>\n";
    } else {
	    echo "<font color=red>You do not have permission to view this page</font>\n";
    }

}


?>
