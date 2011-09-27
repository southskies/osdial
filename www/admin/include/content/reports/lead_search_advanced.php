<?php
# lead_search_advanced.php
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

# $lsa_seg can be "form" or "data".

function report_lead_search_advanced($lsa_seg='form') {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $html = '';

    if ($LOG['modify_lists']==1 and $LOG['user_level'] > 7) {
        $form = '';
        $form .= "<table align=center><tr><td>\n";
        $form .= "<font face=\"dejavu sans,verdana,sans-serif\" color=$default_text size=2>";
        $form .= "<center><br><font color=$default_text size=+1>ADVANCED LEAD SEARCH</font><br>\n";
        $form .= "<center><font color=$default_text size=2>\n";
        if ($LOG['view_lead_search']) $form .= "<a target=\"_parent\" href=\"./admin.php?ADD=999999&SUB=27\">[ Basic Search ]</a>&nbsp;&nbsp;|&nbsp;&nbsp;\n";
        $form .= "[ Advanced Search ]<br></font></center>\n";

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
        $use_osdial_agent_log = get_variable("use_osdial_agent_log");
        $use_osdial_closer_log = get_variable("use_osdial_closer_log");

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

        $phone_number = OSDpreg_replace("/[^0-9]/","",$phone_number);

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
        $affields = get_variable("affields");
        $scbuttons = get_variable("scbuttons");

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

        $pageURL ="?ADD=$ADD&SUB=$SUB&last_name=$last_name&first_name=$first_name&phone_number=$phone_number&phone_search_alt=$phone_search_alt&lead_id=$lead_id&city=$city&postal_code=$postal_code&email=$email";
        $pageURL.="&entry_date_start=$orig_entry_date_start&entry_date_end=$orig_entry_date_end&modify_date_start=$orig_modify_date_start&modify_date_end=$orig_modify_date_end";
        $pageURL.="&lastcall_date_start=$orig_lastcall_date_start&lastcall_date_end=$orig_lastcall_date_end&use_osdial_log=$use_osdial_log&use_osdial_closer_log=$use_osdial_closer_log&use_osdial_agent_log=$use_osdial_agent_log";
        $pageURL.="&custom1=$custom1&custom2=$custom2&external_key=$external_key&numresults=$numresults";


        if ($phone_number) {
            $notac = "%";
            if (OSDstrlen($phone_number) == 3) $notac="";
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.phone_number LIKE '" . $notac . mres($phone_number) . "%'";
            } elseif ($use_osdial_closer_log) {
                $searchWHR .= " AND osdial_closer_log.phone_number LIKE '" . $notac . mres($phone_number) . "%'";
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
            } elseif ($use_osdial_closer_log) {
                $searchWHR .= " AND osdial_closer_log.lead_id='" . mres($lead_id) . "'";
            } elseif ($use_osdial_agent_log) {
                $searchWHR .= " AND osdial_agent_log.lead_id='" . mres($lead_id) . "'";
            } else {
                $searchWHR .= " AND osdial_list.lead_id='" . mres($lead_id) . "'";
            }
        }
        if ($lastcall_date_start) {
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.call_date BETWEEN '" . mres($lastcall_date_start) . "' AND '" . mres($lastcall_date_end) . "'";
            } elseif ($use_osdial_closer_log) {
                $searchWHR .= " AND osdial_closer_log.call_date BETWEEN '" . mres($lastcall_date_start) . "' AND '" . mres($lastcall_date_end) . "'";
            } elseif ($use_osdial_agent_log) {
                $searchWHR .= " AND osdial_agent_log.event_time BETWEEN '" . mres($lastcall_date_start) . "' AND '" . mres($lastcall_date_end) . "'";
            } else {
                $searchWHR .= " AND osdial_list.last_local_call_time BETWEEN '" . mres($lastcall_date_start) . "' AND '" . mres($lastcall_date_end) . "'";
            }
        }

        ### process campaigns group
        if ($use_osdial_log) {
            $searchWHR .= sprintf(" AND osdial_log.campaign_id IN %s",$LOG['allowed_campaignsSQL']);
        } elseif ($use_osdial_agent_log) {
            $searchWHR .= sprintf(" AND osdial_agent_log.campaign_id IN %s",$LOG['allowed_campaignsSQL']);
        } else {
            $searchWHR .= sprintf(" AND osdial_lists.campaign_id IN %s",$LOG['allowed_campaignsSQL']);
        }

        $campaignIN = "";
        if (is_array($campaigns)) {
            foreach ($campaigns as $campaign) {
                if ($campaign != "") {
                    $pageURL .= "&campaigns[]=$campaign";
                    $campaignIN .= "'" . mres($campaign) . "',";
                }
            }
        }
        if ($campaignIN != "") {
            $campaignIN = rtrim($campaignIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.campaign_id IN ($campaignIN)";
            } elseif ($use_osdial_closer_log) {
                $searchWHR .= " AND osdial_closer_log.campaign_id IN ($campaignIN)";
            } elseif ($use_osdial_agent_log) {
                $searchWHR .= " AND osdial_agent_log.campaign_id IN ($campaignIN)";
            } else {
                $searchWHR .= " AND osdial_lists.campaign_id IN ($campaignIN)";
            }
        }


        ### process lists group
        $listIN = "";
        if (is_array($lists)) {
            foreach ($lists as $list) {
                if ($list != "") {
                    $pageURL .= "&lists[]=$list";
                    $listIN .= "'" . mres($list) . "',";
                }
            }
        }
        if ($listIN != "") {
            $listIN = rtrim($listIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.list_id IN ($listIN)";
            } elseif ($use_osdial_closer_log) {
                $searchWHR .= " AND osdial_closer_log.list_id IN ($listIN)";
            } else {
                $searchWHR .= " AND osdial_list.list_id IN ($listIN)";
            }
        }


        ### process statuses group
        $statusIN = "";
        if (is_array($statuses)) {
            foreach ($statuses as $status) {
                if ($status != "") {
                    $pageURL .= "&statuses[]=$status";
                    $statusIN .= "'" . mres($status) . "',";
                }
            }
        }
        if ($statusIN != "") {
            $statusIN = rtrim($statusIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.status IN ($statusIN)";
            } elseif ($use_osdial_closer_log) {
                $searchWHR .= " AND osdial_closer_log.status IN ($statusIN)";
            } elseif ($use_osdial_agent_log) {
                $searchWHR .= " AND osdial_agent_log.status IN ($statusIN)";
            } else {
                $searchWHR .= " AND osdial_list.status IN ($statusIN)";
            }
        }


        ### process agents group
        $agentIN = "";
        if (is_array($agents)) {
            foreach ($agents as $agent) {
                if ($agent != "") {
                    $pageURL .= "&agents[]=$agent";
                    $agentIN .= "'" . mres($agent) . "',";
                }
            }
        }
        if ($agentIN != "") {
            $agentIN = rtrim($agentIN,",");
            if ($use_osdial_log) {
                $searchWHR .= " AND osdial_log.user IN ($agentIN)";
            } elseif ($use_osdial_closer_log) {
                $searchWHR .= " AND osdial_closer_log.user IN ($agentIN)";
            } elseif ($use_osdial_agent_log) {
                $searchWHR .= " AND osdial_agent_log.user IN ($agentIN)";
            } else {
                $searchWHR .= " AND osdial_list.user IN ($agentIN)";
            }
        }


        ### process states group
        $stateIN = "";
        if (is_array($states)) {
            foreach ($states as $state) {
                if ($state != "") {
                    $pageURL .= "&states[]=$state";
                    $stateIN .= "'" . mres($state) . "',";
                }
            }
        }
        if ($stateIN != "") {
            $stateIN = rtrim($stateIN,",");
            $searchWHR .= " AND osdial_list.state IN ($stateIN)";
        }


        ### process sources group
        $sourceIN = "";
        if (is_array($sources)) {
            foreach ($sources as $source) {
                if ($source != "") {
                    $pageURL .= "&sources[]=$source";
                    $sourceIN .= "'" . mres($source) . "',";
                }
            }
        }
        if ($sourceIN != "") {
            $sourceIN = rtrim($sourceIN,",");
            $searchWHR .= " AND osdial_list.source_id IN ($sourceIN)";
        }

        
        ### process vendor_codes group
        $vendor_codeIN = "";
        if (is_array($vendor_codes)) {
            foreach ($vendor_codes as $vendor_code) {
                if ($vendor_code != "") {
                    $pageURL .= "&vendor_codes[]=$vendor_code";
                    $vendor_codeIN .= "'" . mres($vendor_code) . "',";
                }
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
        if (is_array($timezones)) {
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

        if (is_array($fields)) {
            foreach ($fields as $field) {
                if ($field == "*") {
                    $field_all = 1;
                    $status_found++;
                } elseif ($field == "status") {
                    $status_found++;
                } elseif ($field == "campaign_id" or $field == "lead_id" or $field == "list_id" or $field == "user" or $field == "phone_number") {
                    if ($use_osdial_log) {
                        $searchFLD .= "osdial_log." . $field . ",";
                    } elseif ($use_osdial_agent_log) {
                        if ($field == "campaign_id" or $field == "lead_id" or $field == "user") {
                            $searchFLD .= "osdial_agent_log." . $field . ",";
                        }
                    } else {
                        $searchFLD .= "osdial_lists." . $field . ",";
                    }
                } else {
                    $searchFLD .= "osdial_list." . $field . ",";
                }
                $field_cnt++;
            }
        }

        if ($field_cnt == 0 or $field_all == 1) {
            $searchFLD = "osdial_list.*,";
            if ($use_osdial_log) {
                $searchFLD .= "osdial_log.*,osdial_log.status AS status,";
            } elseif ($use_osdial_closer_log) {
                $searchFLD .= "osdial_closer_log.*,osdial_closer_log.status AS status,";
            } elseif ($use_osdial_agent_log) {
                $searchFLD .= "osdial_agent_log.*,osdial_agent_log.status AS status,";
            } else {
                $searchFLD .= "osdial_lists.campaign_id,osdial_list.status AS status,";
            }
        } elseif ($use_osdial_log and $field_cnt > 0) {
            $searchFLD .= "osdial_log.call_date,osdial_closer_log.length_in_sec,";
        } elseif ($use_osdial_closer_log and $field_cnt > 0) {
            $searchFLD .= "osdial_closer_log.call_date,osdial_closer_log.length_in_sec,";
        }

        if ($status_found) {
            if ($use_osdial_log) {
                    $fieldJOIN = " LEFT JOIN osdial_statuses ON (osdial_log.status=osdial_statuses.status) ";
                    $searchFLD .= "osdial_log.status,";
            } elseif ($use_osdial_closer_log) {
                    $fieldJOIN = " LEFT JOIN osdial_statuses ON (osdial_closer_log.status=osdial_statuses.status) ";
                    $searchFLD .= "osdial_closer_log.status,";
            } elseif ($use_osdial_agent_log) {
                    $fieldJOIN = " LEFT JOIN osdial_statuses ON (osdial_agent_log.status=osdial_statuses.status) ";
                    $searchFLD .= "osdial_agent_log.status,";
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



        $form .= "<style type=text/css>\n";
        $form .= "content {vertical-align:middle;}\n";
        $form .= "</style>\n";
        $form .= "<br><br><center>\n";

        $form .= "<div id=\"caldiv1\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";
        $form .= "<div id=\"caldiv2\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";
        $form .= "<div id=\"caldiv3\" style=\"position:absolute;visibility:hidden;background-color:white;\"></div>\n";

        $form .= "<form method=post name=advsearch_form action=\"$PHP_SELF\">\n";
        $form .= "<input type=hidden name=ADD value=\"$ADD\">\n";
        $form .= "<input type=hidden name=SUB value=\"$SUB\">\n";
        $form .= "<input type=hidden name=DB value=\"$DB\">\n";
        $form .= "<table width=$section_width cellspacing=0 bgcolor=$oddrows class=tabinput style=\"white-space:nowrap;\">\n";
        $form .= "  <tr>\n";
        $form .= "    <td colspan=4 class=tabheader>Enter any combination of the following</td>\n";
        $form .= "  </tr>\n";
        $form .= "  <tr>\n";
        $form .= "    <td width=25% align=right><font size=2>Last Name</font></td>\n";
        $form .= "    <td width=25% align=left><input type=text name=last_name value=\"$last_name\" size=20 maxlength=30></td>\n";
        $form .= "    <td width=25% align=right><font size=2>Lead ID</font></td>\n";
        $form .= "    <td width=25% align=left><input type=text name=lead_id value=\"$lead_id\" size=10 maxlength=10></td>\n";
        $form .= "  </tr>\n";
        $form .= "  <tr>\n";
        $form .= "    <td align=right><font size=2>First Name</font></td>\n";
        $form .= "    <td align=left><input type=text name=first_name value=\"$first_name\" size=20 maxlength=30></td>\n";
        $form .= "    <td align=right><font size=2>AreaCode or PhoneNumber</font></td>\n";
        $form .= "    <td align=left>\n";
        $form .= "      <input type=text name=phone_number value=\"$phone_number\" size=10 maxlength=20>\n";
        if ($phone_search_alt == 1) $check = " checked";
        $form .= "      <input type=checkbox name=phone_search_alt id=phone_seach_alt value=1$check> <font size=1><label for=phone_search_alt>Alternates</label></font>\n";
        $form .= "    </td>\n";
        $form .= "  </tr>\n";
        $form .= "  <tr>\n";
        $form .= "    <td align=right><font size=2>City</font></td>\n";
        $form .= "    <td align=left><input type=text name=city value=\"$city\" size=20 maxlength=50></td>\n";
        $form .= "    <td align=right><font size=2>ZIP / Postal Code</font></td>\n";
        $form .= "    <td align=left><input type=text name=postal_code value=\"$postal_code\" size=10 maxlength=10></td>\n";
        $form .= "  </tr>\n";
        $form .= "  <tr>\n";
        $form .= "    <td align=right><font size=2>Email</font></td>\n";
        $form .= "    <td align=left><input type=text name=email value=\"$email\" size=20 maxlength=70></td>\n";
        $form .= "    <td align=right><font size=2>External Key</font></td>\n";
        $form .= "    <td align=left><input type=text name=external_key value=\"$external_key\" size=10 maxlength=100></td>\n";
        $form .= "  </tr>\n";
        $form .= "  <tr>\n";
        $form .= "    <td align=right><font size=2>Custom1</font></td>\n";
        $form .= "    <td align=left><input type=text name=custom1 value=\"$custom1\" size=10 maxlength=255></td>\n";
        $form .= "    <td align=right><font size=2>Custom2</font></td>\n";
        $form .= "    <td align=left><input type=text name=custom2 value=\"$custom2\" size=10 maxlength=255></td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td colspan=4><font size=1>&nbsp;</font></td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td align=right><font size=2>Entry Date</font></td>\n";
        $form .= "    <td align=left>\n";
        $form .= "      <script>\n";
        $form .= "        var cal1 = new CalendarPopup('caldiv1');\n";
        $form .= "        cal1.showNavigationDropdowns();\n";
        $form .= "      </script>\n";
        $form .= "      <font size=2><input type=text name=entry_date_start value=\"$orig_entry_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\">\n";
        $form .= "      <a href=# onclick=\"";
        $form .=          "cal1.addDisabledDates('clear','clear');";
        $form .=          "if (document.forms[0].entry_date_end.value.length==10) cal1.addDisabledDates(formatDate(parseDate(document.forms[0].entry_date_end.value).addDays(1),'yyyy-MM-dd'),null);";
        $form .=          "cal1.select(document.forms[0].entry_date_start,'EDacal1','yyyy-MM-dd');";
        $form .=          "return false;";
        $form .=          "\" name=EDacal1 id=EDacal1>\n";
        $form .= "      <img width=12 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
        $form .= "      to \n";
        $form .= "      <input type=text name=entry_date_end value=\"$orig_entry_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\">\n";
        $form .= "      <a href=# onclick=\"";
        $form .=          "cal1.addDisabledDates('clear','clear');";
        $form .=          "if (document.forms[0].entry_date_start.value.length==10) cal1.addDisabledDates(null,formatDate(parseDate(document.forms[0].entry_date_start.value).addDays(-1),'yyyy-MM-dd'));";
        $form .=          "cal1.select(document.forms[0].entry_date_end,'EDacal2','yyyy-MM-dd');";
        $form .=          "return false;";
        $form .=          "\" name=EDacal2 id=EDacal2>\n";
        $form .= "      <img width=12 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
        $form .= "      </font></td>\n";
        $fieldOPTS="";
        if (is_array($field_label)) {
            foreach ($field_label as $k => $v) {
                $sel="";
                if (is_array($fields)) {
                    foreach ($fields as $field) {
                        if ($k != "" and $k == $field) {
                            $sel=" selected";
                        }
                    }
                }
                if ($v == "") $v = $k;
                if ($k != "") $fieldOPTS .= "        <option value=\"" . $k . "\"$sel>" . $v . "</option>\n";
            }
        }
        $form .= "    <td align=center valign=top colspan=2 rowspan=5>\n";
        if ($LOG['export_lead_search_advanced'] and $LOG['user_level'] > 8 and $LOG['export_leads'] > 0 and ($LOG['multicomp_user'] == 0 or $LOG['company']['export_leads'] > 0)) {
            $form .= "     <table cellpadding=0 cellspacing=1 width=95%>\n";
            $form .= "       <tr><td class=tabheader colspan=3>CSV Export</td></tr>\n";
            $form .= "       <tr>\n";

            $form .= "         <td align=center valign=top width=33%>\n";
            $form .= "           <table cellpadding=0 cellspacing=0 width=95%>\n";
            $form .= "             <tr><td class=tabheader>Fields</td></tr>\n";
            $sjs = "fld=document.getElementById('fields');afflds=document.getElementById('affields'); scb=document.getElementById('scbuttons'); if (fld.selectedIndex==-1) { afflds.disabled=true; afflds.style.background='#CCCCCC'; for (var i=0;i<afflds.options.length;i++){afflds.options[i].selected=false;}; scb.disabled=true; scb.style.background='#CCCCCC'; for (var i=0;i<scb.options.length;i++){scb.options[i].selected=false;}; } else { afflds.disabled=false; afflds.style.background='#FFFFFF'; scb.disabled=false; scb.style.background='#FFFFFF'; }";
            $form .= "             <tr><td><select name=fields[] id=fields size=6 multiple onchange=\"$sjs\" style=\"width:100%;\">\n";
            $form .= $fieldOPTS;
            $form .= "             </select></td></tr>\n";
            $form .= "           </table>\n";
            $form .= "         </td>\n";

            $affield_label = Array();
            $form .= "         <td align=center valign=top width=33%>\n";
            $form .= "           <table cellpadding=0 cellspacing=0 width=95%>\n";
            $form .= "             <tr><td class=tabheader>Additional Fields</td></tr>\n";
            $affdisable = "style=\"width:100%;border:1px solid #AAAAAA;background:#CCCCCC;\" disabled";
            if (is_array($fields)) {
                if (count($fields) > 0) { 
                    $affdisable = "style=\"width:100%;border:1px solid #AAAAAA;background:#FFFFFF;\"";
                }
            }
            if (is_array($affields)) {
                if (count($affields) > 0) { 
                    $affdisable = "style=\"width:100%;border:1px solid #AAAAAA;background:#FFFFFF;\"";
                }
            }
            $form .= "             <tr><td><select name=affields[] id=affields size=6 multiple $affdisable>\n";
            $krh = get_krh($link, 'osdial_campaign_fields AS fld,osdial_campaign_forms AS frm', "fld.id AS fldid,concat(frm.name,'_',fld.name) AS ffname,fld.deleted AS flddel,frm.deleted AS frmdel",'ffname','fld.form_id=frm.id','');
            if (is_array($krh)) {
                foreach ($krh as $k) {
                    $affstyle = '';
                    if ($k['frmdel']>0) $affstyle = 'style="color:#800000;"';
                    if ($k['flddel']>0) $affstyle = 'style="color:#800000;"';
                    if (is_array($affields)) {
                        foreach ($affields as $affield) {
                            if ($k['fldid'] != "" and $k['fldid'] == $affield) {
                                $sel=" selected";
                            }
                        }
                    }
                    $form .= "        <option $affstyle value=\"" . $k['fldid'] . "\"$sel>" . $k['ffname'] . "</option>\n";
                    $affield_label[$k['fldid']] = $k['ffname'];
                }
            }
            $form .= "             </select></td></tr>\n";
            $form .= "           </table>\n";
            $form .= "         </td>\n";

            $scbutton_label = Array();
            $form .= "         <td align=center valign=top width=33%>\n";
            $form .= "           <table cellpadding=0 cellspacing=0 width=95%>\n";
            $form .= "             <tr><td class=tabheader>Script Clicks</td></tr>\n";
            $scbdisable = "style=\"width:100%;border:1px solid #AAAAAA;background:#CCCCCC;\" disabled";
            if (is_array($fields)) {
                if (count($fields) > 0) { 
                    $scbdisable = "style=\"width:100%;border:1px solid #AAAAAA;background:#FFFFFF;\"";
                }
            }
            if (is_array($scbuttons)) {
                if (count($scbuttons) > 0) { 
                    $scbdisable = "style=\"width:100%;border:1px solid #AAAAAA;background:#FFFFFF;\"";
                }
            }
            $form .= "             <tr><td><select name=scbuttons[] id=scbuttons size=6 multiple $scbdisable>\n";
            $sresults = get_krh($link, 'osdial_scripts', "script_id,script_name",'script_id','1=1','');
            if (is_array($sresults)) {
                foreach ($sresults as $sres) {
                    $scbutton_label[$sres['script_id']] = $sres['script_id'];

                    $sbresults = get_krh($link, 'osdial_script_buttons', "script_button_id,script_id",'script_button_id',sprintf("script_id='%s'",$sres['script_id']),'');
                    if (is_array($sbresults)) {
                        foreach ($sbresults as $sbres) {
                            $scbutton_label[$sres['script_id'] . '_' . $sbres['script_button_id']] = $sres['script_id'] . '_' . $sbres['script_button_id'];
                        }
                    }
                }
            }

            foreach ($scbutton_label as $k) {
                if (is_array($scbuttons)) {
                    foreach ($scbuttons as $scbutton) {
                        if ($k != "" and $k == $scbutton) {
                            $sel=" selected";
                        }
                    }
                }
                $form .= "        <option value=\"" . $k . "\"$sel>" . $k . "</option>\n";
            }
            $form .= "             </select></td></tr>\n";
            $form .= "           </table>\n";
            $form .= "         </td>\n";

            $form .= "       </tr>\n";
            $form .= "     </table>\n";
        }
        $form .= "    </td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td align=right><font size=2>Modified Date</font></td>\n";
        $form .= "    <td align=left>\n";
        $form .= "      <script>\n";
        $form .= "        var cal2 = new CalendarPopup('caldiv2');\n";
        $form .= "        cal2.showNavigationDropdowns();\n";
        $form .= "      </script>\n";
        $form .= "      <font size=2><input type=text name=modify_date_start value=\"$orig_modify_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\">\n";
        $form .= "      <a href=# onclick=\"";
        $form .=          "cal2.addDisabledDates('clear','clear');";
        $form .=          "if (document.forms[0].modify_date_end.value.length==10) cal2.addDisabledDates(formatDate(parseDate(document.forms[0].modify_date_end.value).addDays(1),'yyyy-MM-dd'),null);";
        $form .=          "cal2.select(document.forms[0].modify_date_start,'MDacal1','yyyy-MM-dd');";
        $form .=          "return false;";
        $form .=          "\" name=MDacal1 id=MDacal1>\n";
        $form .= "      <img width=12 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
        $form .= "      to\n";
        $form .= "      <input type=text name=modify_date_end value=\"$orig_modify_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\">\n";
        $form .= "      <a href=# onclick=\"";
        $form .=          "cal2.addDisabledDates('clear','clear');";
        $form .=          "if (document.forms[0].modify_date_start.value.length==10) cal2.addDisabledDates(null,formatDate(parseDate(document.forms[0].modify_date_start.value).addDays(-1),'yyyy-MM-dd'));";
        $form .=          "cal2.select(document.forms[0].modify_date_end,'MDacal2','yyyy-MM-dd');";
        $form .=          "return false;";
        $form .=          "\" name=MDacal2 id=MDacal2>\n";
        $form .= "      <img width=12 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
        $form .= "      </font></td>\n";
        $form .= "  </tr>\n";
        $form .= "  <tr>\n";
        $form .= "    <td align=right><font size=2>LastCall/Log Date</font></td>\n";
        $form .= "    <td align=left><font size=2>\n";
        $form .= "      <script>\n";
        $form .= "        var cal3 = new CalendarPopup('caldiv3');\n";
        $form .= "        cal3.showNavigationDropdowns();\n";
        $form .= "      </script>\n";
        $form .= "      <input type=text name=lastcall_date_start value=\"$orig_lastcall_date_start\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\">\n";
        $form .= "      <a href=# onclick=\"";
        $form .=          "cal3.addDisabledDates('clear','clear');";
        $form .=          "if (document.forms[0].lastcall_date_end.value.length==10) cal3.addDisabledDates(formatDate(parseDate(document.forms[0].lastcall_date_end.value).addDays(1),'yyyy-MM-dd'),null);";
        $form .=          "cal3.select(document.forms[0].lastcall_date_start,'LCDacal1','yyyy-MM-dd');";
        $form .=          "return false;";
        $form .=          "\" name=LCDacal1 id=LCDacal1>\n";
        $form .= "      <img width=12 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
        $form .= "      to \n";
        $form .= "      <input type=text name=lastcall_date_end value=\"$orig_lastcall_date_end\" size=10 maxlength=10 title=\"Format: YYYY-MM-DD\">\n";
        $form .= "      <a href=# onclick=\"";
        $form .=          "cal3.addDisabledDates('clear','clear');";
        $form .=          "if (document.forms[0].lastcall_date_start.value.length==10) cal3.addDisabledDates(null,formatDate(parseDate(document.forms[0].lastcall_date_start.value).addDays(-1),'yyyy-MM-dd'));";
        $form .=          "cal3.select(document.forms[0].lastcall_date_end,'LCDacal2','yyyy-MM-dd');";
        $form .=          "return false;";
        $form .=          "\" name=LCDacal2 id=LCDacal2>\n";
        $form .= "      <img width=12 src=\"templates/default/images/calendar.png\" style=border:0px;></a>\n";
        $form .= "    </font>\n";
        $check='';if ($use_osdial_log == 1) $check = " checked";
        $check2='';if ($use_osdial_closer_log == 1) $check2 = " checked";
        $check3='';if ($use_osdial_agent_log == 1) $check3 = " checked";
        $form .= "  </td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td align=right><font size=2>Use Log Entries</font></td>\n";
        $form .= "    </td>\n";
        $form .= "    <td align=left>\n";
        $form .= "      <input type=checkbox name=use_osdial_agent_log id=use_osdial_agent_log onchange=\"if (this.checked==true) use_osdial_closer_log.checked=false; use_osdial_log.checked=false;\" value=1$check3><font size=1><label for=use_osdial_agent_log>Agent</label></font>&nbsp;&nbsp;&nbsp;";
        $form .= "<input type=checkbox name=use_osdial_log id=use_osdial_log onchange=\"if (this.checked==true) use_osdial_agent_log.checked=false; use_osdial_closer_log.checked=false;\" value=1$check><font size=1><label for=use_osdial_log>Outbound</label></font>&nbsp;&nbsp;&nbsp;";
        $form .= "<input type=checkbox name=use_osdial_closer_log id=use_osdial_closer_log onchange=\"if (this.checked==true) use_osdial_agent_log.checked=false; use_osdial_log.checked=false;\" value=1$check2><font size=1><label for=use_osdial_closer_log>Closer/Inbound</label></font>\n";
        $form .= "    </td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td colspan=2><font size=1>&nbsp;</font></td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td colspan=4><font size=1>&nbsp;</font></td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td align=center>\n";
        $form .= "     <table cellpadding=0 cellspacing=0 width=95%>\n";
        $form .= "      <tr><td class=tabheader>Campaigns</td></tr>\n";
        $form .= "      <tr><td><select name=campaigns[] size=8 multiple style=\"width:100%;\">\n";
        $krh = get_krh($link, 'osdial_campaigns', 'campaign_id,campaign_name','',sprintf("campaign_id IN %s",$LOG['allowed_campaignsSQL']),'');
        $form .= format_select_options($krh, 'campaign_id', 'campaign_name', $campaigns, "-- ALL --",$LOG['multicomp_user']);
        $form .= "      </select></td></tr>\n";
        $form .= "     </table>\n";
        $form .= "    </td>\n";

        $form .= "    <td align=center>\n";
        $form .= "     <table cellpadding=0 cellspacing=0 width=95%>\n";
        $form .= "      <tr><td class=tabheader>Lists</td></tr>\n";
        $form .= "      <tr><td><select name=lists[] size=8 multiple style=\"width:100%;\">\n";
        $krh = get_krh($link, 'osdial_lists', 'list_id,list_name,campaign_id','',sprintf("campaign_id IN %s",$LOG['allowed_campaignsSQL']),'');
        $form .= format_select_options($krh, 'list_id', 'list_name', $lists, "-- ALL --");
        $form .= "      </select></td></tr>\n";
        $form .= "     </table>\n";
        $form .= "    </td>\n";

        $form .= "    <td align=center>\n";
        $form .= "     <table cellpadding=0 cellspacing=0 width=95%>\n";
        $form .= "      <tr><td class=tabheader>Statuses</td></tr>\n";
        $form .= "      <tr><td><select name=statuses[] size=8 multiple style=\"width:100%;\">\n";
        $krh = get_krh($link, 'osdial_statuses', 'status,status_name','','','');
        $krh2 = get_krh($link, 'osdial_campaign_statuses', 'status,status_name','','','');
        if (is_array($krh2)) {
            foreach ($krh2 as $k => $v) {
                $krh[$k] = $v;
            }
        }
        ksort($krh);
        $form .= format_select_options($krh, 'status', 'status_name', $statuses, "-- ALL --");
        $form .= "      </select></td></tr>\n";
        $form .= "     </table>\n";
        $form .= "    </td>\n";

        $form .= "    <td align=center>\n";
        $form .= "     <table cellpadding=0 cellspacing=0 width=95%>\n";
        $form .= "      <tr><td class=tabheader>Agents</td></tr>\n";
        $form .= "      <tr><td><select name=agents[] size=8 multiple style=\"width:100%;\">\n";
        $krh = get_krh($link, 'osdial_users', 'user,full_name','',sprintf("user_group IN %s",$LOG['allowed_usergroupsSQL']),'');
        $form .= format_select_options($krh, 'user', 'full_name', $agents, "-- ALL --",$LOG['multicomp_user']);
        $form .= "      </select></td></tr>\n";
        $form .= "     </table>\n";
        $form .= "    </td>\n";
        $form .= "  </tr>\n";
        $agents_label = Array();
        foreach ($krh as $k => $v) {
            $agents_label[$k] = $v['full_name'];
        }

        $form .= "  <tr>\n";
        $form .= "    <td colspan=4><font size=1>&nbsp;</font></td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";

        $form .= "    <td align=center>\n";
        $form .= "     <table cellpadding=0 cellspacing=0 width=95%>\n";
        $form .= "      <tr><td class=tabheader>States</td></tr>\n";
        $form .= "      <tr><td><select name=states[] size=8 multiple style=\"width:100%;\">\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='states'",'');
        $form .= format_select_options($krh, 'group_value', 'group_value', $states, "-- ALL --");
        $form .= "      </select></td></tr>\n";
        $form .= "     </table>\n";
        $form .= "    </td>\n";

        $timezoneOPTS="";
        $s=0;
        $tznameslabel = $tznamesDST;
        if (date('I')) $tznameslabel = $tznamesDST;
        if (is_array($tznameslabel)) {
            foreach ($tznameslabel as $k => $v) {
                $sel="";
                if (is_array($timezones)) {
                    foreach ($timezones as $timezone) {
                        if ($timezone != "" and $k != "" and $v == $timezone) {
                            $sel=" selected";
                            $s++;
                        }
                    }
                }
                if ($k != "") $timezoneOPTS .= "        <option value=\"" . $v . "\"$sel>" . $k . " (" . $v . ")</option>\n";
            }
        }
        $sel="";
        if ($s==0) $sel=" selected";
        $form .= "    <td align=center>\n";
        $form .= "     <table cellpadding=0 cellspacing=0 width=95%>\n";
        $form .= "      <tr><td class=tabheader>TimeZones</td></tr>\n";
        $form .= "      <tr><td><select name=timezones[] size=8 multiple style=\"width:100%;\">\n";
        $form .= "        <option value=\"\"$sel>-- ALL --</option>\n";
        $form .= $timezoneOPTS;
        $form .= "      </select></td></tr>\n";
        $form .= "     </table>\n";
        $form .= "    </td>\n";

        $form .= "    <td align=center>\n";
        $form .= "     <table cellpadding=0 cellspacing=0 width=95%>\n";
        $form .= "      <tr><td class=tabheader>Sources</td></tr>\n";
        $form .= "      <tr><td><select name=sources[] size=8 multiple style=\"width:100%;\">\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='lead_source_id'", "1000");
        $form .= format_select_options($krh, 'group_value', 'group_value', $sources, "-- ALL --");
        $form .= "      </select></td></tr>\n";
        $form .= "     </table>\n";
        $form .= "    </td>\n";

        $form .= "    <td align=center>\n";
        $form .= "     <table cellpadding=0 cellspacing=0 width=95%>\n";
        $form .= "      <tr><td class=tabheader>Vendor Codes</td></tr>\n";
        $form .= "      <tr><td><select name=vendor_codes[] size=8 multiple style=\"width:100%;\">\n";
        $krh = get_krh($link, 'osdial_report_groups', 'group_value,group_label', "", "group_type='lead_vendor_lead_code'", "1000");
        $form .= format_select_options($krh, 'group_value', 'group_value', $vendor_codes, "-- ALL --");
        $form .= "      </select></td></tr>\n";
        $form .= "     </table>\n";
        $form .= "    </td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td colspan=4><font size=1>&nbsp;</font></td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr>\n";
        $form .= "    <td align=center colspan=4><font size=2>Results</font>";
        $form .= "      <select name=numresults size=1>\n";
        foreach ($numresults_label as $k => $v) {
            $sel="";
            if ($numresults==$k) $sel=" selected";
            $form .= "        <option value=\"$k\"$sel>$v</option>\n";
        }
        $form .= "      </select>\n";
        $form .= "      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=2>Sort By</font>";
        $form .= "      <select name=sort size=1>\n";
        foreach ($sort_label as $k => $v) {
            $sel="";
            if ($sort==$k) $sel=" selected";
            $form .= "        <option value=\"$k\"$sel>$v</option>\n";
        }
        $form .= "      </select>\n";
        $form .= "      <select name=direction size=1>\n";
        foreach ($direction_label as $k => $v) {
            $sel="";
            if ($direction==$k) $sel=" selected";
            $form .= "        <option value=\"$k\"$sel>$v</option>\n";
        }
        $form .= "      </select>\n";
        $form .= "    </td>\n";
        $form .= "  </tr>\n";

        $form .= "  <tr class=tabfooter>\n";
        $form .= "    <th colspan=4 class=tabbutton><input type=submit name=submit value=SUBMIT></th>\n";
        $form .= "  </tr>\n";
        $form .= "</table>\n";
        $form .= "</form>\n";

        if (!isset($lsa_seg) or $lsa_seg == 'form') {
            return $form;

        } elseif ($lsa_seg=='data') {

            $data='';

            if ($use_osdial_log) {
                $mainTBL = "osdial_log JOIN osdial_list ON (osdial_log.lead_id=osdial_list.lead_id)";
            } elseif ($use_osdial_closer_log) {
                $mainTBL = "osdial_closer_log JOIN osdial_list ON (osdial_closer_log.lead_id=osdial_list.lead_id)";
            } elseif ($use_osdial_agent_log) {
                $mainTBL = "osdial_agent_log JOIN osdial_list ON (osdial_agent_log.lead_id=osdial_list.lead_id)";
            } else {
                $mainTBL = "osdial_list";
            }
            $countTBL = " FROM " . $mainTBL ." JOIN osdial_lists ON (osdial_lists.list_id=osdial_list.list_id)";
            $searchTBL = " FROM " . $mainTBL ." JOIN osdial_lists ON (osdial_lists.list_id=osdial_list.list_id) LEFT JOIN osdial_postal_code_groups ON (osdial_postal_code_groups.country_code=osdial_list.phone_code AND osdial_postal_code_groups.postal_code=osdial_list.postal_code) LEFT JOIN osdial_phone_code_groups ON (osdial_phone_code_groups.country_code=osdial_list.country_code AND osdial_phone_code_groups.areacode=left(osdial_list.phone_number,3))" . $fieldJOIN;
    
            #$countSQL  = "SELECT count(*) " . $searchSQL . ";";
            $countSQL = "SELECT count(*)" . $countTBL . $searchWHR . $countWHR . ";";
    
            if ($count==0) {
                #$data .= "<br>$countSQL";
                if ($DB) $data .= "\n\n$countSQL\n\n<br>";
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
    
                #$data .= "<br>$searchSQL<br>";
                if ($DB) $data .= "\n\n$searchSQL\n\n";
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
                    $data .= "<br><br><br><div id=\"advsearch\"><font color=$default_text size=3><b>Records Found:&nbsp;" . $searchCount . "</b></font></div>";
                }
            }
    
            $paginate = "";
            if ($results_to_print > 0 and $field_cnt == 0) {
                $data .= "<div id=\"advsearch\"><font color=$default_text size=3><b>Displaying:&nbsp;" . ((($page - 1) * $numresults) + 1) . " through " . ((($page - 1) * $numresults) + $results_to_print) . "</b></font></div>";
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
    
            $data .= $paginate;
    
            $data .= "<table bgcolor=grey cellspacing=1>\n";
            $data .= "  <tr class=tabheader>\n";
            if ($field_cnt > 0 or $results_to_print < 1) {
                $data .= "    <td colspan=17><font size=1>&nbsp;</font></td>\n";
            } else {
                $data .= "    <td align=left title=\"Record #\"><font color=white size=2><b>#</b></font></td>\n";
    
                $data .= "    <td align=center title=\"Lead ID\"><font class=awhite color=white size=2><b>";
                if ($sort == "lead_id" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=lead_id&direction=ASC#advsearch\">Lead#&nbsp;^";
                } elseif ($sort=="lead_id") {
                    $data .= "<a href=\"" . $pageURL . "&sort=lead_id&direction=DESC#advsearch\">Lead#&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=lead_id&direction=DESC#advsearch\">Lead#";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"List ID\"><font class=awhite color=white size=2><b>";
                if ($sort == "list_id" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=list_id&direction=ASC#advsearch\">List#&nbsp;^";
                } elseif ($sort == "list_id") {
                    $data .= "<a href=\"" . $pageURL . "&sort=list_id&direction=DESC#advsearch\">List#&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=list_id&direction=DESC#advsearch\">List#";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Last Status / Disposition\"><font class=awhite color=white size=2><b>";
                if ($sort == "status" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=status&direction=ASC#advsearch\">Status&nbsp;^";
                } elseif ($sort == "status") {
                    $data .= "<a href=\"" . $pageURL . "&sort=status&direction=DESC#advsearch\">Status&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=status&direction=DESC#advsearch\">Status";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Primary Phone Number\"><font class=awhite color=white size=2><b>";
                if ($sort == "phone_number" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=phone_number&direction=ASC#advsearch\">Phone#&nbsp;^";
                } elseif ($sort=="phone_number") {
                    $data .= "<a href=\"" . $pageURL . "&sort=phone_number&direction=DESC#advsearch\">Phone#&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=phone_number&direction=DESC#advsearch\">Phone#";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Last Name, First Name\"><font class=awhite color=white size=2><b>";
                if ($sort == "last_name" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=last_name&direction=ASC#advsearch\">Name&nbsp;^";
                } elseif ($state == "last_name") {
                    $data .= "<a href=\"" . $pageURL . "&sort=last_name&direction=DESC#advsearch\">Name&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=last_name&direction=DESC#advsearch\">Name";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"City\"><font class=awhite color=white size=2><b>";
                if ($sort == "city" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=city&direction=ASC#advsearch\">City&nbsp;^";
                } elseif($sort == "city") {
                    $data .= "<a href=\"" . $pageURL . "&sort=city&direction=DESC#advsearch\">City&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=city&direction=DESC#advsearch\">City";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"State\"><font class=awhite color=white size=2><b>";
                if ($sort == "state" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=state&direction=ASC#advsearch\">State&nbsp;^";
                } elseif($sort == "state") {
                    $data .= "<a href=\"" . $pageURL . "&sort=state&direction=DESC#advsearch\">State&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=state&direction=DESC#advsearch\">State";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"ZIP / Postal Code\"><font class=awhite color=white size=2><b>";
                if ($sort == "postal_code" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=postal_code&direction=ASC#advsearch\">ZIP&nbsp;^";
                } elseif($sort == "postal_code") {
                    $data .= "<a href=\"" . $pageURL . "&sort=postal_code&direction=DESC#advsearch\">ZIP&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=postal_code&direction=DESC#advsearch\">ZIP";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Agent/User ID\"><font class=awhite color=white size=2><b>";
                if ($sort == "user" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=user&direction=ASC#advsearch\">Agent&nbsp;^";
                } elseif ($sort == "vendor_lead_code") {
                    $data .= "<a href=\"" . $pageURL . "&sort=user&direction=DESC#advsearch\">Agent&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=user&direction=DESC#advsearch\">Agent";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Vendor Lead Code\"><font class=awhite color=white size=2><b>";
                if ($sort == "vendor_lead_code" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=ASC#advsearch\">VendorID&nbsp;^";
                } elseif ($sort == "vendor_lead_code") {
                    $data .= "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=DESC#advsearch\">VendorID&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=vendor_lead_code&direction=DESC#advsearch\">VendorID";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Custom Field #1\"><font class=awhite color=white size=2><b>";
                if ($sort == "custom1" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=custom1&direction=ASC#advsearch\">Custom1&nbsp;^";
                } elseif ($sort == "custom1") {
                    $data .= "<a href=\"" . $pageURL . "&sort=custom1&direction=DESC#advsearch\">Custom1&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=custom1&direction=DESC#advsearch\">Custom1";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Timezone\"><font class=awhite color=white size=2><b>";
                if ($sort == "gmt_offset_now" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=ASC#advsearch\">TZ&nbsp;^";
                } elseif ($sort == "gmt_offset_now") {
                    $data .= "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=DESC#advsearch\">TZ&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=gmt_offset_now&direction=DESC#advsearch\">TZ";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"# of Call Attempts\"><font class=awhite color=white size=2><b>";
                if ($sort == "called_count" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=called_count&direction=ASC#advsearch\">Calls&nbsp;^";
                } elseif ($sort == "called_count") {
                    $data .= "<a href=\"" . $pageURL . "&sort=called_count&direction=DESC#advsearch\">Calls&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=called_count&direction=DESC#advsearch\">Calls";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Entry Date\"><font class=awhite color=white size=2><b>";
                if ($sort == "entry_date" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=entry_date&direction=ASC#advsearch\">Entry&nbsp;^";
                } elseif ($sort == "entry_date") {
                    $data .= "<a href=\"" . $pageURL . "&sort=entry_date&direction=DESC#advsearch\">Entry&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=entry_date&direction=DESC#advsearch\">Entry";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Modified Date\"><font class=awhite color=white size=2><b>";
                if ($sort == "modify_date" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=modify_date&direction=ASC#advsearch\">Modified&nbsp;^";
                } elseif ($sort == "modify_date") {
                    $data .= "<a href=\"" . $pageURL . "&sort=modify_date&direction=DESC#advsearch\">Modified&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=modify_date&direction=DESC#advsearch\">Modified";
                }
                $data .= "</a></b></font></td>\n";
    
                $data .= "    <td align=center title=\"Post Date\"><font class=awhite color=white size=2><b>";
                if ($sort == "post_date" && $direction == "DESC") {
                    $data .= "<a href=\"" . $pageURL . "&sort=post_date&direction=ASC#advsearch\">Post&nbsp;^";
                } elseif ($sort == "post_date") {
                    $data .= "<a href=\"" . $pageURL . "&sort=post_date&direction=DESC#advsearch\">Post&nbsp;v";
                } else {
                    $data .= "<a href=\"" . $pageURL . "&sort=post_date&direction=DESC#advsearch\">Post";
                }
                $data .= "</a></b></font></td>\n";
                $data .= "    <td align=center title=\"Recordings\"><font class=awhite color=white size=2><b>REC</b></font></td>\n";
            }
            $data .= "  </tr>\n";
    
    
            if ($field_cnt > 0 && $LOG['export_leads'] > 0) {
                $csvfile = "advsearch_" . date("Ymd-His") . ".csv";
                $fcsv = fopen ("./" . $csvfile, "a");
                $fld_cnt = mysql_num_fields($rslt);
                $fld_names = Array();
                $o=0;
                while ($fld_cnt > $o) {
                    $fld_names[] = mysql_field_table($rslt, $o) . "." . mysql_field_name($rslt, $o);
                    $o++;
                }
                $fld_names[] = "REC";
                if (is_array($affields)) {
                    foreach ($affields as $affield) {
                        $fld_names[] = $affield_label[$affield];
                    }
                }
                if (is_array($scbuttons)) {
                    foreach ($scbuttons as $scbutton) {
                        $fld_names[] = $scbutton_label[$scbutton];
                    }
                }
                fputcsv($fcsv, $fld_names);
                
                $o=0;
                while ($results_to_print > $o) {
                    $row=mysql_fetch_row($rslt);
                    if ($row[0] > 0) {
                        $rslt2=mysql_query(sprintf("SELECT location FROM recording_log WHERE lead_id='%s' ORDER BY recording_id DESC LIMIT 1;",$row[0]), $link);
                        $row2=mysql_fetch_row($rslt2);
                        $row[] = 'http://' . $_SERVER['SERVER_ADDR'] . $row2[0];
                        if (is_array($affields)) {
                            foreach ($affields as $affield) {
                                $rslt2=mysql_query(sprintf("SELECT value FROM osdial_list_fields WHERE lead_id='%s' AND field_id='%s' LIMIT 1;",$row[0],$affield), $link);
                                $row2=mysql_fetch_row($rslt2);
                                if (is_numeric(OSDsubstr($row2[0],0,1)) and (!OSDpreg_match('/[a-z]/i',$row2[0]))) $row2[0] = "'" . $row2[0];
                                $row[] = $row2[0];
                            }
                        }
                        if (is_array($scbuttons)) {
                            foreach ($scbuttons as $scbutton) {
                                $rslt2=mysql_query(sprintf("SELECT script_button_id FROM osdial_script_button_log WHERE lead_id='%s' AND script_id='%s' ORDER BY event_time DESC LIMIT 1;",$row[0],$scbutton), $link);
                                $row2=mysql_fetch_row($rslt2);
                                $row[] = $row2[0];
                            }
                        }
                    }
                    fputcsv($fcsv, $row);
                    $o++;
                }
                fclose($fcsv);
                $data .= "  <tr bgcolor=$oddrows>\n";
                $data .= "    <td colspan=17 align=center><font size=3 color=$default_text><a target=_new href=\"$csvfile\">Click here to transfer CSV file.</a></font></td>\n";
                $data .= "  </tr>\n";
            } elseif ($results_to_print < 1) {
                $data .= "  <tr bgcolor=$oddrows>\n";
                $data .= "    <td colspan=17 align=center><font size=3 color=$default_text>The item(s) you searched for were not found.</font></td>\n";
                $data .= "  </tr>\n";
            } else {
                $o=0;
                while ($results_to_print > $o) {
                    $row=mysql_fetch_array($rslt, MYSQL_BOTH);
                    $recloc = '';
                    if ($row[0] > 0) {
                        $rslt2=mysql_query(sprintf("SELECT location FROM recording_log WHERE lead_id='%s' ORDER BY recording_id DESC LIMIT 1;",$row[0]), $link);
                        $row2=mysql_fetch_row($rslt2);
                        $recloc = $row2[0];
                    }
                    $o++;
                    if ($row[1] == '0000-00-00 00:00:00') $row[1] = "";
                    if ($row[2] == '0000-00-00 00:00:00') $row[2] = "";
                    if ($row[35] == '0000-00-00 00:00:00') $row[35] = "";
                    if (OSDstrlen($row[11]) == 10) $row[11] = OSDsubstr($row[11],0,3) . "-" . OSDsubstr($row[11],3,3) . "-" . OSDsubstr($row[11],6,4);
                    $row[8] = $row[8] * 1;
                    $tzlabel = $tzoffsets[$row[8]];
                    if (date('I') == 1) $tzlabel = $tzoffsetsDST[$row[8]];
                    $data .= "  <tr " . bgcolor($o) . " class=row ondblclick=\"openNewWindow('$PHP_SELF?ADD=1121&lead_id=$row[0]');\">\n";
                    $data .= "    <td nowrap align=left><font face=\"dejavu sans,verdana,sans-serif\" size=1>" . ($o + (($page - 1) * $numresults)) . "</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[0]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1><a href=\"$PHP_SELF?ADD=1121&lead_id=$row[0]\" target=\"_blank\">$row[0]</a></font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[7]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1><a href=\"" . $pageURL . "&lists[]=$row[7]&sort=$sort&direction=$direction#advsearch\">$row[7]</a></font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[status]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>$row[status]</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[11]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>$row[11]</font></td>\n";
                    $data .= "    <td nowrap align=left title=\"$row[15], $row[13]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>" . ellipse($row[15] . ", " . $row[13], 10, true) . "</font></td>\n";
                    $data .= "    <td nowrap align=left title=\"$row[19]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>" . ellipse($row[19],10,true) . "</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"" . OSDstrtoupper($row[20]) ."\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>" . OSDstrtoupper($row[20]) . "</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[22]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>$row[22]</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"" . $row[4] . " (" . $agents_label[$row[4]] . ")\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>$row[4]</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[4]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>$row[5]</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[28]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>" . ellipse($row[28],10,true) . "</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"" . $tzlabel . " (" . $row[8]. ")\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>" . $tzlabel . "</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[30]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>$row[30]</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[1]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>&nbsp;" . ellipse($row[1],10,false) . "&nbsp;</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[2]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>&nbsp;" . dateToLocal($link,'first',$row[2],$webClientAdjGMT,'',$webClientDST,1) . "&nbsp;</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$row[35]\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>" . ellipse($row[35],10,false) . "</font></td>\n";
                    $data .= "    <td nowrap align=center title=\"$recloc\"><font face=\"dejavu sans,verdana,sans-serif\" size=1>";
                    if ($recloc != "") {
                        $data .= "<a target=\"_new\" href=\"http://" . $_SERVER['SERVER_ADDR'] . "$recloc\">[rec]</a>";
                    }
                    $data .= "</font></td>\n";
                    $data .= "  </tr>\n";
                }
            }
            $data .= "  <tr class=tabfooter>\n";
            $data .= "    <td colspan=18></td>\n";
            $data .= "  </tr>\n";
            $data .= "</table>\n";
    
            $data .= $paginate;
            $data .= "</center>\n";

            return $data;
        }
    } else {
        $html .= "<font color=red>You do not have permission to view this page</font>\n";
    }

    return $html;
}


?>
