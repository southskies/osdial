<?php
# advanced_search.php
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

if ($ADD==1122) {
    if ($LOGmodify_lists==1 and $LOGuser_level > 7) {
        echo "<table align=center><tr><td>\n";
        echo "<font face=\"arial,helvetica\" color=$default_text size=2>";
        echo "<center><br><font color=$default_text size=+1>ADVANCED LEAD SEARCH</font>\n";

        $last_name = get_variable("last_name");
        $first_name = get_variable("first_name");
        $phone_number = get_variable("phone_number");
        $phone_search_alt = get_variable("phone_search_alt");
        $lead_id = get_variable("lead_id");
        $city = get_variable("city");
        $postal_code = get_variable("postal_code");
        $email = get_variable("email");
        $custom1 = get_variable("custom1");
        $custom2 = get_variable("custom2");
        $external_key = get_variable("external_key");
        $entry_date_start = get_variable("entry_date_start");
        $entry_date_end = get_variable("entry_date_end");
        $modify_date_start = get_variable("modify_date_start");
        $modify_date_end = get_variable("modify_date_end");
        $lastcall_date_start = get_variable("lastcall_date_start");
        $lastcall_date_end = get_variable("lastcall_date_end");
        $use_osdial_log = get_variable("use_osdial_log");

        $orig_entry_date_start = "";
        $orig_entry_date_end = "";
        if ($entry_date_start != "" and $entry_date_end == "") $entry_date_end = $entry_date_start;
        if ($entry_date_start != "") {
            $entry_date_start .= " 00:00:00";
            $entry_date_end .= " 23:59:59";
            $tmp1 = explode(" ",$entry_date_start);
            $orig_entry_date_start = $tmp1[0];
            $tmp2 = explode(" ",$entry_date_end);
            $orig_entry_date_end = $tmp2[0];
        }

        $orig_modify_date_start = "";
        $orig_modify_date_end = "";
        if ($modify_date_start != "" and $modify_date_end == "") $modify_date_end = $modify_date_start;
        if ($modify_date_start != "") {
            $modify_date_start .= " 00:00:00";
            $modify_date_end .= " 23:59:59";
            $tmp1 = explode(" ",$modify_date_start);
            $orig_modify_date_start = $tmp1[0];
            $tmp2 = explode(" ",$modify_date_end);
            $orig_modify_date_end = $tmp2[0];
        }

        $orig_lastcall_date_start = "";
        $orig_lastcall_date_end = "";
        if ($lastcall_date_start != "" and $lastcall_date_end == "") $lastcall_date_end = $lastcall_date_start;
        if ($lastcall_date_start != "") {
            $lastcall_date_start .= " 00:00:00";
            $lastcall_date_end .= " 23:59:59";
            $tmp1 = explode(" ",$lastcall_date_start);
            $orig_lastcall_date_start = $tmp1[0];
            $tmp2 = explode(" ",$lastcall_date_end);
            $orig_lastcall_date_end = $tmp2[0];
        }

        $phone_number = ereg_replace("[^0-9]","",$phone_number);

        # groups
        $campaigns = get_variable("campaigns");
        $lists = get_variable("lists");
        $statuses = get_variable("statuses");
        $agents = get_variable("agents");
        $states = get_variable("states");
        $timezones = get_variable("timezones");
        $sources = get_variable("sources");
        $vendor_codes = get_variable("vendor_codes");
        $fields = get_variable("fields");

        $numresults = get_variable("numresults");
        if ($numresults == "" or $numresults == 0)
            $numresults = 100;
        $page = get_variable("page");
        if ($page < 1)
            $page = 1;
        $count = get_variable("count");
        if ($page == 1)
            $count = 0;
        $sort = get_variable("sort");
        if ($sort == "")
            $sort = "called_count";
        $direction = get_variable("direction");
        if ($direction == "")
            $direction = "ASC";

        $searchWHR = " WHERE 1=1";

        $pageURL ="?ADD=$ADD&last_name=$last_name&first_name=$first_name&phone_number=$phone_number&phone_search_alt=$phone_search_alt&lead_id=$lead_id&city=$city&postal_code=$postal_code&email=$email";
        $pageURL.="&entry_date_start=$orig_entry_date_start&entry_date_end=$orig_entry_date_end&modify_date_start=$orig_modify_date_start&modify_date_end=$orig_modify_date_end";
        $pageURL.="&lastcall_date_start=$orig_lastcall_date_start&lastcall_date_end=$orig_lastcall_date_end&use_osdial_log=$use_osdial_log";
        $pageURL.="&custom1=$custom1&custom2=$custom2&external_key=$external_key&numresults=$numresults";


        if ($phone_number) {
            $notac = "%";
            if (strlen($phone_number) == 3) $notac="";
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.phone_number LIKE '" . $notac . mres($phone_number) . "%'";
            } elseif ($phone_search_alt) {
                $searchWHR .= " AND (osdial_list.phone_number LIKE '" . $notac . mres($phone_number) . "%'";
                $searchWHR .= " OR osdial_list.alt_phone LIKE '" . $notac . mres($phone_number) . "%'";
                $searchWHR .= " OR osdial_list.address3 LIKE '" . $notac . mres($phone_number) . "%')";
            } else {
                $searchWHR .= " AND osdial_list.phone_number LIKE '" . $notac . mres($phone_number) . "%'";
            }
        }
        if ($lead_id) {
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.lead_id='" . mres($lead_id) . "'";
            } else {
                $searchWHR .= " AND osdial_list.lead_id='" . mres($lead_id) . "'";
            }
        }
        if ($lastcall_date_start) {
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.call_date BETWEEN '" . mres($lastcall_date_start) . "' AND '" . mres($lastcall_date_end) . "'";
            } else {
                $searchWHR .= " AND osdial_list.last_local_call_time BETWEEN '" . mres($lastcall_date_start) . "' AND '" . mres($lastcall_date_end) . "'";
            }
        }

        ### process campaigns group
        $campaignIN = "";
        foreach ($campaigns as $campaign) {
            if ($campaign != "") {
                $pageURL .= "&campaigns[]=$campaign";
                $campaignIN .= "'" . mres($campaign) . "',";
            }
        }
        if ($campaignIN != "") {
            $campaignIN = rtrim($campaignIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.campaign_id IN ($campaignIN)";
            } else {
                $searchWHR .= " AND osdial_lists.campaign_id IN ($campaignIN)";
            }
        }


        ### process lists group
        $listIN = "";
        foreach ($lists as $list) {
            if ($list != "") {
                $pageURL .= "&lists[]=$list";
                $listIN .= "'" . mres($list) . "',";
            }
        }
        if ($listIN != "") {
            $listIN = rtrim($listIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.list_id IN ($listIN)";
            } else {
                $searchWHR .= " AND osdial_list.list_id IN ($listIN)";
            }
        }


        ### process statuses group
        $statusIN = "";
        foreach ($statuses as $status) {
            if ($status != "") {
                $pageURL .= "&statuses[]=$status";
                $statusIN .= "'" . mres($status) . "',";
            }
        }
        if ($statusIN != "") {
            $statusIN = rtrim($statusIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.status IN ($statusIN)";
            } else {
                $searchWHR .= " AND osdial_list.status IN ($statusIN)";
            }
        }


        ### process agents group
        $agentIN = "";
        foreach ($agents as $agent) {
            if ($agent != "") {
                $pageURL .= "&agents[]=$agent";
                $agentIN .= "'" . mres($agent) . "',";
            }
        }
        if ($agentIN != "") {
            $agentIN = rtrim($agentIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.user IN ($agentIN)";
            } else {
                $searchWHR .= " AND osdial_list.user IN ($agentIN)";
            }
        }


        ### process states group
        $stateIN = "";
        foreach ($states as $state) {
            if ($state != "") {
                $pageURL .= "&states[]=$state";
                $stateIN .= "'" . mres($state) . "',";
            }
        }
        if ($stateIN != "") {
            $stateIN = rtrim($stateIN,",");
            $searchWHR .= " AND osdial_list.state IN ($stateIN)";
        }


        ### process sources group
        $sourceIN = "";
        foreach ($sources as $source) {
            if ($source != "") {
                $pageURL .= "&sources[]=$source";
                $sourceIN .= "'" . mres($source) . "',";
            }
        }
        if ($sourceIN != "") {
            $sourceIN = rtrim($sourceIN,",");
            $searchWHR .= " AND osdial_list.source_id IN ($sourceIN)";
        }

        
        ### process vendor_codes group
        $vendor_codeIN = "";
        foreach ($vendor_codes as $vendor_code) {
            if ($vendor_code != "") {
                $pageURL .= "&vendor_codes[]=$vendor_code";
                $vendor_codeIN .= "'" . mres($vendor_code) . "',";
            }
        }
        if ($vendor_codeIN != "") {
            $vendor_codeIN = rtrim($vendor_codeIN,",");
            $searchWHR .= " AND osdial_list.vendor_lead_code IN ($vendor_codeIN)";
        }

        if ($last_name)    $searchWHR .= " AND osdial_list.last_name LIKE '%" . mres($last_name) . "%'";
        if ($first_name)   $searchWHR .= " AND osdial_list.first_name LIKE '%" . mres($first_name) . "%'";
        if ($city)         $searchWHR .= " AND osdial_list.city LIKE '%" . mres($city) . "%'";
        if ($postal_code)  $searchWHR .= " AND osdial_list.postal_code LIKE '" . mres($postal_code) . "%'";
        if ($email)        $searchWHR .= " AND osdial_list.email LIKE '%" . mres($email) . "%'";
        if ($external_key) $searchWHR .= " AND osdial_list.external_key LIKE '%" . mres($external_key) . "%'";
        if ($custom1)      $searchWHR .= " AND osdial_list.custom1 LIKE '%" . mres($custom1) . "%'";
        if ($custom2)      $searchWHR .= " AND osdial_list.custom2 LIKE '%" . mres($custom2) . "%'";

        if ($entry_date_start)  $searchWHR .= " AND osdial_list.entry_date BETWEEN '" . mres($entry_date_start) . "' AND '" . mres($entry_date_end) . "'";
        if ($modify_date_start) $searchWHR .= " AND osdial_list.modify_date BETWEEN '" . mres($modify_date_start) . "' AND '" . mres($modify_date_end) . "'";


        ### process timezones group
        $timezoneIN = "";
        $timezoneCNTIN = "";
        foreach ($timezones as $timezone) {
            if ($timezone != "") {
                $pageURL .= "&timezones[]=$timezone";
                $timezoneIN .= mres($timezone) . ",";
	            $isdst = date("I");
                $timezoneDST = $timezone;
                if ($isdst) $timezoneDST += 1;
                $timezoneCNTIN .= mres($timezoneDST) . ",";
            }
        }
        if ($timezoneIN != "") {
            $timezoneIN = rtrim($timezoneIN,",");
            $timezoneCNTIN = rtrim($timezoneCNTIN,",");
            $searchTZWHR .= " AND coalesce(osdial_postal_code_groups.GMT_offset,osdial_phone_code_groups.GMT_offset) IN ($timezoneIN)";
            $countWHR .= " AND osdial_list.gmt_offset_now IN ($timezoneCNTIN)";
        }

        $searchFLD = '';
        $field_cnt = 0;
        $field_all = 0;
        $status_found = 0;
        $fieldJOIN = '';

        foreach ($fields as $field) {
            if ($field == "*") {
                $field_all = 1;
                $status_found++;
            } elseif ($field == "status") {
                $status_found++;
            } elseif ($field == "campaign_id" or $field == "lead_id" or $field == "list_id" or $field == "user" or $field == "phone_number") {
                if ($use_osdial_log) {
                    $searchFLD .= "osdial_log." . $field . ",";
                } else {
                    $searchFLD .= "osdial_lists." . $field . ",";
                }
            } else {
                $searchFLD .= "osdial_list." . $field . ",";
            }
            $field_cnt++;
        }

        if ($field_cnt == 0 or $field_all == 1) {
            $searchFLD = "osdial_list.*,";
            if ($use_osdial_log) {
                $searchFLD .= "osdial_log.*,";
            } else {
                $searchFLD .= "osdial_lists.campaign_id,";
            }
        } elseif ($use_osdial_log and $field_cnt > 0) {
            $searchFLD .= "osdial_log.call_date,osdial_log.length_in_sec,";
        }

        if ($status_found) {
            if ($use_osdial_log) {
                    $fieldJOIN = " LEFT JOIN osdial_statuses ON (osdial_log.status=osdial_statuses.status) ";
                    $searchFLD .= "osdial_log.status,";
            } else {
                    $fieldJOIN = " LEFT JOIN osdial_statuses ON (osdial_list.status=osdial_statuses.status) ";
                    $searchFLD .= "osdial_list.status,";
            }
            $searchFLD .= "osdial_statuses.status_name,";
        }

        $searchFLD = rtrim($searchFLD,",");


        $numresults_label['25']   = "25/page";
        $numresults_label['50']   = "50/page";
        $numresults_label['100']  = "100/page";
        $numresults_label['200']  = "200/page";
        $numresults_label['500']  = "500/page";
        $numresults_label['1000'] = "1000/page";

        $sort_label['list_id']     = "List#";
        $sort_label['lead_id']     = "Lead#";
        $sort_label['status']      = "Status";
        $sort_label['phone_number']= "Phone Number";
        $sort_label['last_name']   = "Last Name";
        $sort_label['city']        = "City";
        $sort_label['state']       = "State";
        $sort_label['postal_code'] = "ZIP/PostalCode";
        $sort_label['user']        = "Agent";
        $sort_label['vendor_lead_code'] = "Vendor ID";
        $sort_label['custom1']     = "Custom1";
        $sort_label['gmt_offset_now'] = "Timezone";
        $sort_label['entry_date']  = "Entry Date";
        $sort_label['modify_date'] = "Modify Date";
        $sort_label['called_count'] = "Attempts";
        $sort_label['last_local_call_time']      = "Last Local Call Time";
        $sort_label['post_date']      = "Post Dates";

        $direction_label['ASC']= "Ascending";
        $direction_label['DESC']= "Descending";

        $timezone_label['AKST'] = "-9";
        $timezone_label['AST'] = "-4";
        $timezone_label['CST'] = "-6";
        $timezone_label['EST'] = "-5";
        $timezone_label['HAST'] = "-10";
        $timezone_label['MST'] = "-7";
        $timezone_label['NST'] = "-3.5";
        $timezone_label['PST'] = "-8";
        $timezone_label['PHT'] = "8";

        $tz_revlabel['8'] = "PHT";
        $tz_revlabel['0'] = "0";
        $tz_revlabel['-1'] = "-1";
        $tz_revlabel['-2'] = "-2";
        $tz_revlabel['-3'] = "-3";
        $tz_revlabel['-3.5'] = "NST";
        $tz_revlabel['-4'] = "AST";
        $tz_revlabel['-5'] = "EST";
        $tz_revlabel['-6'] = "CST";
        $tz_revlabel['-7'] = "MST";
        $tz_revlabel['-8'] = "PST";
        $tz_revlabel['-9'] = "AKST";
        $tz_revlabel['-10'] = "HAST";

        $field_label['*'] = '-- ALL --';
        $field_label['lead_id'] = '';
        $field_label['entry_date'] = '';
        $field_label['modify_date'] = '';
        $field_label['status'] = '';
        $field_label['user'] = '';
        $field_label['vendor_lead_code'] = '';
        $field_label['source_id'] = '';
        $field_label['list_id'] = '';
        $field_label['campaign_id'] = '';
        $field_label['gmt_offset_now'] = '';
        $field_label['called_since_last_reset'] = '';
        $field_label['phone_code'] = '';
        $field_label['phone_number'] = '';
        $field_label['title'] = '';
        $field_label['first_name'] = '';
        $field_label['middle_initial'] = '';
        $field_label['last_name'] = '';
        $field_label['address1'] = '';
        $field_label['address2'] = '';
        $field_label['address3'] = '';
        $field_label['city'] = '';
        $field_label['state'] = '';
        $field_label['province'] = '';
        $field_label['postal_code'] = '';
        $field_label['country_code'] = '';
        $field_label['gender'] = '';
        $field_label['date_of_birth'] = '';
        $field_label['alt_phone'] = '';
        $field_label['email'] = '';
        $field_label['custom1'] = '';
        $field_label['comments'] = '';
        $field_label['called_count'] = '';
        $field_label['custom2'] = '';
        $field_label['external_key'] = '';
        $field_label['last_local_call_time'] = '';
        $field_label['cost'] = '';
        $field_label['post_date'] = '';



        echo "<style type=text/css> content {vertical-align:center}</style>\n";
        echo "<br><br><center>\n";
        echo "<form method=post name=search action=\"$PHP_SELF\">\n";
        echo "<input type=hidden name=ADD value=1122>\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";

        echo "<table width=$section_width cellspacing=0 bgcolor=$oddrows>\n";
        echo "  <tr>\n";
        echo "    <td colspan=4>\n";
        echo "      <br><center><font color=$default_text>Enter any combination of the following</font></center><br>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td width=25% align=right><font size=2>Last Name</font></td>\n";
        echo "    <td width=25% align=left><input type=text name=last_name value=\"$last_name\" size=20 maxlength=30></td>\n";
        echo "    <td width=25% align=right><font size=2>Lead ID</font></td>\n";
        echo "    <td width=25% align=left><input type=text name=lead_id value=\"$lead_id\" size=10 maxlength=10></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>First Name</font></td>\n";
        echo "    <td align=left><input type=text name=first_name value=\"$first_name\" size=20 maxlength=30></td>\n";
        echo "    <td align=right><font size=2>AreaCode or PhoneNumber</font></td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=phone_number value=\"$phone_number\" size=10 maxlength=20>\n";
        if ($phone_search_alt == 1) $check = " checked";
        echo "      <input type=checkbox name=phone_search_alt id=phone_seach_alt value=1$check> <font size=1><label for=phone_search_alt>Alternates</label></font>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>City</font></td>\n";
        echo "    <td align=left><input type=text name=city value=\"$city\" size=20 maxlength=50></td>\n";
        echo "    <td align=right><font size=2>ZIP / Postal Code</font></td>\n";
        echo "    <td align=left><input type=text name=postal_code value=\"$postal_code\" size=10 maxlength=10></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Email</font></td>\n";
        echo "    <td align=left><input type=text name=email value=\"$email\" size=20 maxlength=70></td>\n";
        echo "    <td align=right><font size=2>External Key</font></td>\n";
        echo "    <td align=left><input type=text name=external_key value=\"$external_key\" size=10 maxlength=100></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Custom1</font></td>\n";
        echo "    <td align=left><input type=text name=custom1 value=\"$custom1\" size=10 maxlength=255></td>\n";
        echo "    <td align=right><font size=2>Custom2</font></td>\n";
        echo "    <td align=left><input type=text name=custom2 value=\"$custom2\" size=10 maxlength=255></td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <td colspan=4><br></td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Entry Date</font></td>\n";
        echo "    <td align=left colspan=2><font size=2><input type=text name=entry_date_start value=\"$orig_entry_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"> to <input type=text name=entry_date_end value=\"$orig_entry_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"></font></td>\n";
        $fieldOPTS="";
        foreach ($field_label as $k => $v) {
            $sel="";
            foreach ($fields as $field) {
                if ($k != "" and $k == $field) {
                    $sel=" selected";
                }
            }
            if ($v == "") $v = $k;
            if ($k != "") $fieldOPTS .= "        <option value=\"" . $k . "\"$sel>" . $v . "</option>\n";
        }
        if ($LOGuser_level > 8 && $LOGexport_leads > 0) {
            echo "    <td align=center valign=top rowspan=4>\n";
            echo "      <font size=2>CSV Export Fields</font><br>\n";
            echo "      <select name=fields[] size=5 multiple>\n";
            echo $fieldOPTS;
            echo "      </select>\n";
            echo "    </td>\n";
        }
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Modified Date</font></td>\n";
        echo "    <td align=left colspan=3><font size=2><input type=text name=modify_date_start value=\"$orig_modify_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"> to <input type=text name=modify_date_end value=\"$orig_modify_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"></font></td>\n";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "    <td align=right><font size=2>Last Call Date</font></td>\n";
        echo "    <td align=left colspan=3><font size=2>\n";
        echo "      <input type=text name=lastcall_date_start value=\"$orig_lastcall_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"> to <input type=text name=lastcall_date_end value=\"$orig_lastcall_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\"></font>\n";
        if ($use_osdial_log == 1) $check = " checked";
        echo "  </td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <td>\n";
        echo "    </td>\n";
        echo "    <td colspan=3 align=left>\n";
        echo "      <input type=checkbox name=use_osdial_log id=use_osdial_log value=1$check> <font size=1><label for=use_osdial_log>Output Lead History (Must Enter Call Date)</label></font>\n";
        echo "      <br><br>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <td align=center>\n";
        echo "      <font size=2>Campaigns</font><br>\n";
        echo "      <select name=campaigns[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_campaigns', 'campaign_id,campaign_name');
        echo format_select_options($krh, 'campaign_id', 'campaign_id', $campaigns, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>Lists</font><br>\n";
        echo "      <select name=lists[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_lists', 'list_id,list_name,campaign_id');
        echo format_select_options($krh, 'list_id', 'list_name', $lists, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>Statuses</font><br>\n";
        echo "      <select name=statuses[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_statuses', 'status,status_name');
        $krh2 = get_krh($link, 'osdial_campaign_statuses', 'status,status_name');
        foreach ($krh2 as $k => $v) {
            $krh[$k] = $v;
        }
        echo format_select_options($krh, 'status', 'status_name', $statuses, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>Agents</font><br>\n";
        echo "      <select name=agents[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_users', 'user,full_name');
        echo format_select_options($krh, 'user', 'full_name', $agents, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        $agents_label = Array();
        foreach ($krh as $k => $v) {
            $agents_label[$k] = $v['full_name'];
        }

        echo "  <tr>\n";
        echo "    <td colspan=4><br></td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";

        echo "    <td align=center>\n";
        echo "      <font size=2>States</font><br>\n";
        echo "      <select name=states[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='states'");
        echo format_select_options($krh, 'group_value', 'group_value', $states, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";

        $timezoneOPTS="";
        $s=0;
        foreach ($timezone_label as $k => $v) {
            $sel="";
            foreach ($timezones as $timezone) {
                if ($k != "" and $v == $timezone) {
                    $sel=" selected";
                    $s++;
                }
            }
            if ($k != "") $timezoneOPTS .= "        <option value=\"" . $v . "\"$sel>" . $k . " (" . $v . ")</option>\n";
        }
        $sel="";
        if ($s==0) $sel=" selected";
        echo "    <td align=center>\n";
        echo "      <font size=2>TimeZones</font><br>\n";
        echo "      <select name=timezones[] size=5 multiple>\n";
        echo "        <option value=\"\"$sel>-- ALL --</option>\n";
        echo $timezoneOPTS;
        echo "      </select>\n";
        echo "    </td>\n";
        ob_flush();
        flush();

        echo "    <td align=center>\n";
        echo "      <font size=2>Sources</font><br>\n";
        echo "      <select name=sources[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='lead_source_id'", "1000");
        echo format_select_options($krh, 'group_value', 'group_value', $sources, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";
        ob_flush();
        flush();

        echo "    <td align=center>\n";
        echo "      <font size=2>Vendor Codes</font><br>\n";
        echo "      <select name=vendor_codes[] size=5 multiple>\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='lead_vendor_lead_code'", "1000");
        echo format_select_options($krh, 'group_value', 'group_value', $vendor_codes, "-- ALL --");
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        ob_flush();
        flush();


        echo "  <tr>\n";
        echo "    <td align=center colspan=4><br><font size=2>Results</font>";
        echo "      <select name=numresults size=1>\n";
        foreach ($numresults_label as $k => $v) {
            $sel="";
            if ($numresults==$k) $sel=" selected";
            echo "        <option value=\"$k\"$sel>$v</option>\n";
            ob_flush();
            flush();
        }
        echo "      </select>\n";
        echo "      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2>Sort By</font>";
        echo "      <select name=sort size=1>\n";
        foreach ($sort_label as $k => $v) {
            $sel="";
            if ($sort==$k) $sel=" selected";
            echo "        <option value=\"$k\"$sel>$v</option>\n";
            ob_flush();
            flush();
        }
        echo "      </select>\n";
        echo "      <select name=direction size=1>\n";
        foreach ($direction_label as $k => $v) {
            $sel="";
            if ($direction==$k) $sel=" selected";
            echo "        <option value=\"$k\"$sel>$v</option>\n";
            ob_flush();
            flush();
        }
        echo "      </select>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr>\n";
        echo "    <th colspan=4><center><input type=submit name=submit value=SUBMIT></b></center><br></th>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</form>\n";
        ob_flush();
        flush();



        if ($use_osdial_log) {
            $mainTBL = "osdial_log JOIN osdial_list ON (osdial_log.lead_id=osdial_list.lead_id)";
        } else {
            $mainTBL = "osdial_list";
        }
        $countTBL = " FROM " . $mainTBL ." JOIN osdial_lists ON (osdial_lists.list_id=osdial_list.list_id)";
        $searchTBL = " FROM " . $mainTBL ." JOIN osdial_lists ON (osdial_lists.list_id=osdial_list.list_id) LEFT JOIN osdial_postal_code_groups ON (osdial_postal_code_groups.country_code=osdial_list.phone_code AND osdial_postal_code_groups.postal_code=osdial_list.postal_code) LEFT JOIN osdial_phone_code_groups ON (osdial_phone_code_groups.country_code=osdial_list.country_code AND osdial_phone_code_groups.areacode=left(osdial_list.phone_number,3))" . $fieldJOIN;

        #$countSQL  = "SELECT count(*) " . $searchSQL . ";";
        $countSQL = "SELECT count(*)" . $countTBL . $searchWHR . $countWHR . ";";

        if ($count==0) {
            #echo "<br>$countSQL";
            if ($DB) echo "\n\n$countSQL\n\n<br>";
            $rslt=mysql_query($countSQL, $link);
            $row=mysql_fetch_row($rslt);
            $searchCount = $row[0];
        } else {
            $searchCount = $count;
        }

        # Get the number of pages needed to paginate the results.
        $pages = $searchCount / $numresults;
        if (($pages - round($pages)) != 0) $pages = (round($pages) + 1);
        if ($page > $pages) $page = $pages;


        $searchDone=1;
        while ($searchDone) {
            $searchSQL = "SELECT STRAIGHT_JOIN " . $searchFLD . $searchTBL . $searchWHR . $searchTZWHR;
            if ($field_cnt == 0) $searchSQL .= " ORDER BY " . $sort  . " " . $direction . " LIMIT " . (($page - 1) * $numresults) . ", " . $numresults;
            $searchSQL .= ";";

            #echo "<br>$searchSQL<br>";
            if ($DB) echo "\n\n$searchSQL\n\n";
            $rslt=mysql_query($searchSQL, $link);
            $results_to_print = mysql_num_rows($rslt);
            if ($page > 1 and $results_to_print == 0) {
                $page -= 1;
                $pages -= 1;
            } else {
                $searchDone=0;
                if ($results_to_print < $numresults) {
                    $pages = $page;
                    $searchCount = ((($page - 1) * $numresults) + $results_to_print);
                    if ($searchCount < 1) $searchCount=0;
                }
                echo "<br><br><br><div id=\"advsearch\"><font color=$default_text size=3><b>Records Found:&nbsp;" . $searchCount . "</b></font></div>";
            }
            ob_flush();
            flush();
        }

        $paginate = "";
        if ($results_to_print > 0 and $field_cnt == 0) {
            echo "<div id=\"advsearch\"><font color=$default_text size=3><b>Displaying:&nbsp;" . ((($page - 1) * $numresults) + 1) . " through " . ((($page - 1) * $numresults) + $results_to_print) . "</b></font></div>";
            $paginate .= "<font color=$default_text size=2>\n";
            if ($page == 1) {
                $paginate .= "<font color=darkgrey>";
                $paginate .= "&lt;&lt;&lt; First";
                $paginate .= "&nbsp;&nbsp;&nbsp;";
                $paginate .= "&lt;&lt; Previous";
                $paginate .= "</font>";
            } else {
                $paginate .= "<a href=\"" . $pageURL . "&sort=$sort&direction=$direction&page=1&count=" . $searchCount . "#advsearch\">&lt;&lt;&lt; First</a>";
                $paginate .= "&nbsp;&nbsp;&nbsp;";
                $paginate .= "<a href=\"" . $pageURL . "&sort=$sort&direction=$direction&page=" . ($page - 1) . "&count=" . $searchCount . "#advsearch\">&lt;&lt; Previous</a>";
            }
            $paginate .= "&nbsp;&nbsp;&nbsp;";
            $paginate .= "[<b>$page</b> of $pages]";
            #$paginate .= "[<b>$page</b>]";
            $paginate .= "&nbsp;&nbsp;&nbsp;";
            if ($page == $pages ) {
                $paginate .= "<font color=darkgrey>";
                $paginate .= "Next &gt;&gt;";
                $paginate .= "&nbsp;&nbsp;&nbsp;";
                $paginate .= "Last &gt;&gt;&gt;";
                $paginate .= "</font>";
            } else {
                $paginate .= "<a href=\"" . $pageURL . "&sort=$sort&direction=$direction&page=" . ($page + 1) . "&count=" . $searchCount . "#advsearch\">Next &gt;&gt;</a>";
                $paginate .= "&nbsp;&nbsp;&nbsp;";
                $paginate .= "<a href=\"" . $pageURL . "&sort=$sort&direction=$direction&page=" . ($pages) . "&count=" . $searchCount . "#advsearch\">Last &gt;&gt;&gt;</a>";
            }
            $paginate .= "</font>\n";
        }

        echo $paginate;

        echo "<table bgcolor=grey cellspacing=1>\n";
        echo "  <tr class=tabheader>\n";
        if ($field_cnt > 0 or $results_to_print < 1) {
            echo "    <td colspan=17><font size=1>&nbsp;</font></td>\n";
        } else {
            echo "    <td align=left title=\"Record #\"><font color=white size=2><b>#</b></font></td>\n";

            echo "    <td align=center title=\"Lead ID\"><font class=awhite color=white size=2><b>";
            if ($sort == "lead_id" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=lead_id&direction=ASC#advsearch\">Lead#&nbsp;^";
            } elseif ($sort=="lead_id") {
                echo "<a href=\"" . $pageURL . "&sort=lead_id&direction=DESC#advsearch\">Lead#&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=lead_id&direction=DESC#advsearch\">Lead#";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"List ID\"><font class=awhite color=white size=2><b>";
            if ($sort == "list_id" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=list_id&direction=ASC#advsearch\">List#&nbsp;^";
            } elseif ($sort == "list_id") {
                echo "<a href=\"" . $pageURL . "&sort=list_id&direction=DESC#advsearch\">List#&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=list_id&direction=DESC#advsearch\">List#";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Last Status / Disposition\"><font class=awhite color=white size=2><b>";
            if ($sort == "status" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=status&direction=ASC#advsearch\">Status&nbsp;^";
            } elseif ($sort == "status") {
                echo "<a href=\"" . $pageURL . "&sort=status&direction=DESC#advsearch\">Status&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=status&direction=DESC#advsearch\">Status";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Primary Phone Number\"><font class=awhite color=white size=2><b>";
            if ($sort == "phone_number" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=phone_number&direction=ASC#advsearch\">Phone#&nbsp;^";
            } elseif ($sort=="phone_number") {
                echo "<a href=\"" . $pageURL . "&sort=phone_number&direction=DESC#advsearch\">Phone#&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=phone_number&direction=DESC#advsearch\">Phone#";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Last Name, First Name\"><font class=awhite color=white size=2><b>";
            if ($sort == "last_name" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=last_name&direction=ASC#advsearch\">Name&nbsp;^";
            } elseif ($state == "last_name") {
                echo "<a href=\"" . $pageURL . "&sort=last_name&direction=DESC#advsearch\">Name&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=last_name&direction=DESC#advsearch\">Name";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"City\"><font class=awhite color=white size=2><b>";
            if ($sort == "city" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=city&direction=ASC#advsearch\">City&nbsp;^";
            } elseif($sort == "city") {
                echo "<a href=\"" . $pageURL . "&sort=city&direction=DESC#advsearch\">City&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=city&direction=DESC#advsearch\">City";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"State\"><font class=awhite color=white size=2><b>";
            if ($sort == "state" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=state&direction=ASC#advsearch\">State&nbsp;^";
            } elseif($sort == "state") {
                echo "<a href=\"" . $pageURL . "&sort=state&direction=DESC#advsearch\">State&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=state&direction=DESC#advsearch\">State";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"ZIP / Postal Code\"><font class=awhite color=white size=2><b>";
            if ($sort == "postal_code" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=postal_code&direction=ASC#advsearch\">ZIP&nbsp;^";
            } elseif($sort == "postal_code") {
                echo "<a href=\"" . $pageURL . "&sort=postal_code&direction=DESC#advsearch\">ZIP&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=postal_code&direction=DESC#advsearch\">ZIP";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Agent/User ID\"><font class=awhite color=white size=2><b>";
            if ($sort == "user" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=user&direction=ASC#advsearch\">Agent&nbsp;^";
            } elseif ($sort == "vendor_lead_code") {
                echo "<a href=\"" . $pageURL . "&sort=user&direction=DESC#advsearch\">Agent&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=user&direction=DESC#advsearch\">Agent";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Vendor Lead Code\"><font class=awhite color=white size=2><b>";
            if ($sort == "vendor_lead_code" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=ASC#advsearch\">VendorID&nbsp;^";
            } elseif ($sort == "vendor_lead_code") {
                echo "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=DESC#advsearch\">VendorID&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=DESC#advsearch\">VendorID";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Custom Field #1\"><font class=awhite color=white size=2><b>";
            if ($sort == "custom1" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=custom1&direction=ASC#advsearch\">Custom1&nbsp;^";
            } elseif ($sort == "custom1") {
                echo "<a href=\"" . $pageURL . "&sort=custom1&direction=DESC#advsearch\">Custom1&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=custom1&direction=DESC#advsearch\">Custom1";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Timezone\"><font class=awhite color=white size=2><b>";
            if ($sort == "gmt_offset_now" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=ASC#advsearch\">TZ&nbsp;^";
            } elseif ($sort == "gmt_offset_now") {
                echo "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=DESC#advsearch\">TZ&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=DESC#advsearch\">TZ";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"# of Call Attempts\"><font class=awhite color=white size=2><b>";
            if ($sort == "called_count" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=called_count&direction=ASC#advsearch\">Calls&nbsp;^";
            } elseif ($sort == "called_count") {
                echo "<a href=\"" . $pageURL . "&sort=called_count&direction=DESC#advsearch\">Calls&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=called_count&direction=DESC#advsearch\">Calls";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Entry Date\"><font class=awhite color=white size=2><b>";
            if ($sort == "entry_date" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=entry_date&direction=ASC#advsearch\">Entry&nbsp;^";
            } elseif ($sort == "entry_date") {
                echo "<a href=\"" . $pageURL . "&sort=entry_date&direction=DESC#advsearch\">Entry&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=entry_date&direction=DESC#advsearch\">Entry";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Modified Date\"><font class=awhite color=white size=2><b>";
            if ($sort == "modify_date" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=modify_date&direction=ASC#advsearch\">Modified&nbsp;^";
            } elseif ($sort == "modify_date") {
                echo "<a href=\"" . $pageURL . "&sort=modify_date&direction=DESC#advsearch\">Modified&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=modify_date&direction=DESC#advsearch\">Modified";
            }
            echo "</a></b></font></td>\n";

            echo "    <td align=center title=\"Post Date\"><font class=awhite color=white size=2><b>";
            if ($sort == "post_date" && $direction == "DESC") {
                echo "<a href=\"" . $pageURL . "&sort=post_date&direction=ASC#advsearch\">Post&nbsp;^";
            } elseif ($sort == "post_date") {
                echo "<a href=\"" . $pageURL . "&sort=post_date&direction=DESC#advsearch\">Post&nbsp;v";
            } else {
                echo "<a href=\"" . $pageURL . "&sort=post_date&direction=DESC#advsearch\">Post";
            }
            echo "</a></b></font></td>\n";
        }
        echo "  </tr>\n";
        ob_flush();
        flush();

        if ($field_cnt > 0 && $LOGexport_leads > 0) {
            $csvfile = "advsearch_" . date("Ymd-His") . ".csv";
            $fcsv = fopen ("./" . $csvfile, "a");
            $fld_cnt = mysql_num_fields($rslt);
            $fld_names = Array();
            $o=0;
            while ($fld_cnt > $o) {
                $fld_names[] = mysql_field_table($rslt, $o) . "." . mysql_field_name($rslt, $o);
                $o++;
            }
            fputcsv($fcsv, $fld_names);
            ob_flush();
            flush();
            
            $o=0;
            while ($results_to_print > $o) {
                $row=mysql_fetch_row($rslt);
                fputcsv($fcsv, $row);
                ob_flush();
                flush();
                $o++;
            }
            fclose($fcsv);
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td colspan=17 align=center><font size=3 color=$default_text><a target=_new href=\"$csvfile\">Click here to transfer CSV file.</a></font></td>\n";
            echo "  </tr>\n";
        } elseif ($results_to_print < 1) {
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td colspan=17 align=center><font size=3 color=$default_text>The item(s) you searched for were not found.</font></td>\n";
            echo "  </tr>\n";
        } else {
            $o=0;
            while ($results_to_print > $o) {
                $row=mysql_fetch_row($rslt);
                $o++;
                if (eregi("1$|3$|5$|7$|9$", $o)) 
                    {$bgcolor='bgcolor='.$oddrows;} 
                else
                    {$bgcolor='bgcolor='.$evenrows;}
                if ($row[1] == '0000-00-00 00:00:00') $row[1] = "";
                if ($row[2] == '0000-00-00 00:00:00') $row[2] = "";
                if ($row[35] == '0000-00-00 00:00:00') $row[35] = "";
                if (strlen($row[11]) == 10) $row[11] = substr($row[11],0,3) . "-" . substr($row[11],3,3) . "-" . substr($row[11],6,4);
                ob_flush();
                flush();
                echo "  <tr $bgcolor class=row>\n";
                echo "    <td nowrap align=left><font face=\"arial,helvetica\" size=1>" . ($o + (($page - 1) * $numresults)) . "</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[0]\"><font face=\"arial,helvetica\" size=1><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[0]\" target=\"_blank\">$row[0]</a></font></td>\n";
                echo "    <td nowrap align=center title=\"$row[7]\"><font face=\"arial,helvetica\" size=1><a href=\"" . $pageURL . "&lists[]=$row[7]&sort=$sort&direction=$direction#advsearch\">$row[7]</a></font></td>\n";
                echo "    <td nowrap align=center title=\"$row[3]\"><font face=\"arial,helvetica\" size=1>$row[3]</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[11]\"><font face=\"arial,helvetica\" size=1>$row[11]</font></td>\n";
                echo "    <td nowrap align=left title=\"$row[15], $row[13]\"><font face=\"arial,helvetica\" size=1>" . ellipse($row[15] . ", " . $row[13], 10, true) . "</font></td>\n";
                echo "    <td nowrap align=left title=\"$row[19]\"><font face=\"arial,helvetica\" size=1>" . ellipse($row[19],10,true) . "</font></td>\n";
                echo "    <td nowrap align=center title=\"" . strtoupper($row[20]) ."\"><font face=\"arial,helvetica\" size=1>" . strtoupper($row[20]) . "</font></td>\n";
                ob_flush();
                flush();
                echo "    <td nowrap align=center title=\"$row[22]\"><font face=\"arial,helvetica\" size=1>$row[22]</font></td>\n";
                echo "    <td nowrap align=center title=\"" . $row[4] . " (" . $agents_label[$row[4]] . ")\"><font face=\"arial,helvetica\" size=1>$row[4]</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[4]\"><font face=\"arial,helvetica\" size=1>$row[5]</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[28]\"><font face=\"arial,helvetica\" size=1>$row[28]</font></td>\n";
                echo "    <td nowrap align=center title=\"" . $tz_revlabel[($row[8] - date("I"))] . " (" . $row[8]. ")\"><font face=\"arial,helvetica\" size=1>" . $tz_revlabel[($row[8] - date("I"))] . "</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[30]\"><font face=\"arial,helvetica\" size=1>$row[30]</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[1]\"><font face=\"arial,helvetica\" size=1>&nbsp;" . ellipse($row[1],10,false) . "&nbsp;</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[2]\"><font face=\"arial,helvetica\" size=1>&nbsp;$row[2]&nbsp;</font></td>\n";
                echo "    <td nowrap align=center title=\"$row[35]\"><font face=\"arial,helvetica\" size=1>" . ellipse($row[35],10,false) . "</font></td>\n";
                echo "  </tr>\n";
                ob_flush();
                flush();
            }
        }
        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=17></td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        ob_flush();
        flush();

        echo $paginate;
        ob_flush();
        flush();
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


?>