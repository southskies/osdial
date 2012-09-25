<?php
# api.php - OSDial
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
#
# This program provides a method of importing live data from the InsureMe service.
# Currently this script only handles Health-based applicants.

require_once("include/dbconnect.php");
require_once("include/functions.php");
require_once("include/variables.php");

# This flag allows us to import the authentication and permissions function, allowing us to authenticate within this module.
$osdial_skip_auth=1;
require_once("include/auth.php");

$file_debug=0;


### Grab Server GMT value from the database
$isdst = date("I");
$Shour = date("H");
$Smin = date("i");
$Ssec = date("s");
$Smon = date("m");
$Smday = date("d");
$Syear = date("Y");
$server = get_first_record($link, 'servers', '*', "");
if ($server['local_gmt'] != "") {
    $DBgmt = $server['local_gmt'];
    if (OSDstrlen($DBgmt)>0) {
        $server_gmt = $DBgmt;
    }
    if ($isdst) $server_gmt++;
} else {
    $server_gmt = date("O");
    $server_gmt = OSDpreg_replace("/\+/","",$server_gmt);
    $server_gmt = (($server_gmt + 0) / 100);
}
$local_gmt = $server_gmt;

$start = date("Y-m-d H:i:s");
$start_epoch = date("U");


# GET/POST vars for various actions/command methods.
$file = get_variable("file");
$xmldata = get_variable("xml");
$function = get_variable("function");


# If file, xml, or function POST/GET are not defined, grab xml input from CLI defined file.
if ($file == "" and $xmldata == "" and $function == "") $file=$argv[1];


# Setup initial response XML
$result = new SimpleXMLElement("<response><request/></response>");
$result->status['code'] = "SUCCESS";
$vdstatus="";
$vdreason="";
$debug = '';


# Build xml input data from file POST location, xml POST, compatibility mode, else fail.
$data = "";
if ($file != "") {
    # Get xml data from file.
    $data = file_get_contents($file);
} elseif ($xmldata != "") {
    # Get defined xml data.
    $data = $xmldata;
} elseif ($function != "") {
    # Compatibility mode from upstream.
    $data = "<api vdcompat=\"1\" function=\"$function\"></api>";
} else {
    # fail.
    $data = "<api></api>";
    $result->status['code'] = "ERROR";
    $result->status = "Failed to parse XML";
}
$xml = new SimpleXMLElement($data);


# Put initial request back into the result.
$result = new SimpleXMLElement(OSDpreg_replace('/(^<\?.*$|\n)/m',"",OSDpreg_replace('/<request\/>/m',"<request>\n" . $xml->asXML() . "\n</request>",$result->asXML())));


# Put start date/time and epoch into XML result.
$result->status['start'] = $start;
$result->status['start_epoch'] = $start_epoch;

$mime_type='text/xml';
if (preg_match('/^Y$|^YES$|^T$|^TRUE$|^1$/i',$xml['vdcompat'])) {
    $xml['vdcompat'] = 1;
    $mimetype = "text/html";
} else {
    $xml['vdcompat'] = 0;
    $mimetype = "text/xml";
}

header("Content-type: $mime_type; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");


# If we are in compatibility mode, make sure we grab some vars if not defined in XML.
if ($xml['vdcompat'] > 0) {
    if ($xml['test'] == "" ) $xml['test'] = get_variable("test");
    if ($xml['debug'] == "" ) $xml['debug'] = get_variable("DB");
    if ($xml['debug'] == "" ) $xml['debug'] = get_variable("debug");
    if ($xml['user'] == "" ) $xml['user'] = get_variable("user");
    if ($xml['pass'] == "" ) $xml['pass'] = get_variable("pass");

    # Added for vmail_check
    if ($xml->params->server_ip == "" ) $xml->params->server_ip = get_variable("server_ip");
    if ($xml->params->vmail_box == "" ) $xml->params->vmail_box = get_variable("vmail_box");

    if ($xml->params->dnc_check == "" ) $xml->params->dnc_check = get_variable("dnc_check");
    if ($xml->params->duplicate_check == "" ) $xml->params->duplicate_check = get_variable("dnc_check");
    if ($xml->params->gmt_lookup_method == "" ) $xml->params->gmt_lookup_method = get_variable("gmt_lookup_method");
    if ($xml->params->add_to_hopper == "" ) $xml->params->add_to_hopper = get_variable("add_to_hopper");
    if ($xml->params->hopper_local_call_time_check == "" ) $xml->params->hopper_local_call_time_check = get_variable("hopper_local_call_time_check");
    if ($xml->params->hopper_campaign_call_time_check == "" ) $xml->params->hopper_campaign_call_time_check = get_variable("hopper_campaign_call_time_check");
    if ($xml->params->hopper_priority == "" ) $xml->params->hopper_priority = get_variable("hopper_priority");
    if ($xml->params->hopper_priority == "" ) $xml->params->hopper_priority = get_variable("priority");

    # The following provide compatibility for add_lead
    if ($xml->params->vendor_lead_code == "" ) $xml->params->vendor_lead_code = get_variable("vendor_lead_code");
    if ($xml->params->source_id == "" ) $xml->params->source_id = get_variable("source_id");
    if ($xml->params->list_id == "" ) $xml->params->list_id = get_variable("list_id");
    if ($xml->params->phone_code == "" ) $xml->params->phone_code = get_variable("phone_code");
    if ($xml->params->phone_number == "" ) $xml->params->phone_number = get_variable("phone_number");
    if ($xml->params->title == "" ) $xml->params->title = OSDpreg_replace('/\+/'," ",get_variable("title"));
    if ($xml->params->first_name == "" ) $xml->params->first_name = OSDpreg_replace('/\+/'," ",get_variable("first_name"));
    if ($xml->params->middle_initial == "" ) $xml->params->middle_initial = get_variable("middle_initial");
    if ($xml->params->last_name == "" ) $xml->params->last_name = OSDpreg_replace('/\+/'," ",get_variable("last_name"));
    if ($xml->params->address1 == "" ) $xml->params->address1 = OSDpreg_replace('/\+/'," ",get_variable("address1"));
    if ($xml->params->address2 == "" ) $xml->params->address2 = OSDpreg_replace('/\+/'," ",get_variable("address2"));
    if ($xml->params->address3 == "" ) $xml->params->address3 = OSDpreg_replace('/\+/'," ",get_variable("address3"));
    if ($xml->params->city == "" ) $xml->params->city = OSDpreg_replace('/\+/'," ",get_variable("city"));
    if ($xml->params->state == "" ) $xml->params->state = OSDpreg_replace('/\+/'," ",get_variable("state"));
    if ($xml->params->province == "" ) $xml->params->province = OSDpreg_replace('/\+/'," ",get_variable("province"));
    if ($xml->params->postal_code == "" ) $xml->params->postal_code = OSDpreg_replace('/\+/'," ",get_variable("postal_code"));
    if ($xml->params->country_code == "" ) $xml->params->country_code = OSDpreg_replace('/\+/'," ",get_variable("country_code"));
    if ($xml->params->gender == "" ) $xml->params->gender = get_variable("gender");
    if ($xml->params->date_of_birth == "" ) $xml->params->date_of_birth = get_variable("date_of_birth");
    if ($xml->params->alt_phone == "" ) $xml->params->alt_phone = get_variable("alt_phone");
    if ($xml->params->email == "" ) $xml->params->email = get_variable("email");
    # security_phrase was renamed to custom1, put in custom1 if defined, but custom1 should override.
    if ($xml->params->custom1 == "" ) $xml->params->custom1 = OSDpreg_replace('/\+/'," ",get_variable("security_phrase"));
    if ($xml->params->custom1 == "" ) $xml->params->custom1 = OSDpreg_replace('/\+/'," ",get_variable("custom1"));
    if ($xml->params->comments == "" ) $xml->params->comments = OSDpreg_replace('/\+/'," ",get_variable("comments"));
    if ($xml->params->custom2 == "" ) $xml->params->custom2 = OSDpreg_replace('/\+/'," ",get_variable("custom2"));
    if ($xml->params->external_key == "" ) $xml->params->external_key = get_variable("external_key");
    if ($xml->params->cost == "" ) $xml->params->cost = get_variable("cost");
    if ($xml->params->post_date == "" ) $xml->params->post_date = get_variable("post_date");
    if ($xml->params->agent == "" ) $xml->params->agent = get_variable("agent");
}


# General Validations
$xml['function'] = OSDstrtolower($xml['function']);

if ($xml['mode'] == "") $xml['mode'] = "admin";
if ($xml['mode'] == "non_agent") $xml['mode'] = "admin";
$xml['mode'] = OSDstrtolower($xml['mode']);

if (preg_match('/^Y$|^YES$|^T$|^TRUE$|^1$/i',$xml['test'])) {
    $xml['test'] = 1;
} else {
    $xml['test'] = 0;
}

if (preg_match('/^Y$|^YES$|^T$|^TRUE$|^1$/i',$xml['debug'])) {
    $xml['debug'] = 1;
} elseif (!preg_match('/^[0-9]$/i',$xml['debug'])) {
    $xml['debug'] = 0;
} else {
    $xml['debug'] += 0;
}

if ($file_debug > 0) $xml['debug'] = 1;

if ($xml['user'] == "" and $xml['username'] != "") {
    $xml['user'] = $xml["username"];
} elseif ($xml['user'] == "" and $xml['login'] != "") {
    $xml['user'] = $xml["login"];
}
if ($xml['pass'] == "" and $xml['password'] != "") $xml['pass'] = $xml["password"];

if ($xml['debug'] > 0) $debug .= sprintf("\nFunction: %s, Mode: %s\n", $xml['function'], $xml['mode']);

# Function to output version.
if ($xml['function'] == "version") {
    $result->result['records'] = '1';
    $result->result->addChild("record");
    $result->result->record[0]['id'] = '0';
    $result->result->record[0]->version = $admin_version;
    $result->result->record[0]->build = $build;
    $vdstatus = "VERSION";
    $vdreason = $admin_version . "|BUILD: " . $build . "|DATE: " . $start . "|EPOCH: " . $start_epoch;


# Function to get vmail message counts.
} else if ($xml['function'] == "vmail_check") {
    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['user_level'] < 1) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "vmail_check USER DOES NOT HAVE PERMISSION TO CHECK VMAIL.";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['user_level']);
    }

    if ($status != "ERROR") {
        $phones = get_first_record($link, 'phones', '*', sprintf("server_ip='%s' AND voicemail_id='%s'",mres($xml->params->server_ip),mres($xml->params->vmail_box)) );
        if ($phones['extension']!='') {
            $result->result['records'] = '1';
            $result->result->addChild("record");
            $result->result->record[0]['id'] = '0';
            $result->result->record[0]->vmail_box = (string)$xml->params->vmail_box;
            $result->result->record[0]->messages = $phones['messages'];
            $result->result->record[0]->old_messages = $phones['old_messages'];
            $vdstatus = "VMAIL_BOX";
            $vdreason = $xml->params->vmail_box . "|NEW: " . $phones['messages'] . "|OLD: " . $phones['old_messages'] . "|EPOCH: " . $start_epoch;
        } else {
            $status = "ERROR";
            $reason = "Could not locate given vmail_box/server_ip pair..";
            $vdstatus = "ERROR";
            $vdreason = "vmail_check VMAIL_BOX DOES NOT EXIST ON SERVER..";
        }
    }


# Function to add a new lead.
} elseif ($xml['function'] == "add_lead" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    $list_campaign_id = '';
    $dup_list = '';
    $dup_list_active = '';
    $dup_syslist = '';
    $dup_syslist_active = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['load_leads'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "add_lead USER DOES NOT HAVE PERMISSION TO ADD LEADS TO THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, load_leads=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['load_leads'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "add_lead USER DOES NOT HAVE PERMISSION TO ADD LEADS TO THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # Validate fields.
    $xml->params->phone_code = OSDpreg_replace('/[^0-9]/',"",$xml->params->phone_code);
    if ($xml->params->phone_code == "") $xml->params->phone_code = 1;
    $xml->params->phone_number = OSDpreg_replace('/[^0-9]/',"",$xml->params->phone_number);
    $xml->params->alt_phone = OSDpreg_replace('/[^0-9]/',"",$xml->params->alt_phone);
    $xml->params->cost = OSDpreg_replace('/[^0-9\.]/',"",$xml->params->cost);

    # Check phone number length.
    if ((OSDstrlen($xml->params->phone_number) < 6 or OSDstrlen($xml->params->phone_number) > 16) and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Phone Number not between 7 and 15 digits.";
        $vdreason = "add_lead INVALID PHONE NUMBER";
    }

    # DOB validation
    if (OSDstrlen($xml->params->date_of_birth) > 1 and $status != "ERROR") {
        $dm = Array();
        if (preg_match('/(19[0-9][0-9]|20[0-9][0-9])-(0[1-9]|[1-9]|1[012])-(0[1-9]|[1-9]|[12][0-9]|3[01])([ |T].*|$)/ix', $xml->params->date_of_birth, $dm)) {
            $xml->params->date_of_birth = date('Y-m-d',strtotime($dm[1] . '-' . $dm[2] . '-' . $dm[3]));
        } else {
            $status = "ERROR";
            $reason = "Invalid Date of Birth format, should be YYYY-MM-DD or ISO-8601 (2010-01-01T00:00:00).";
            $vdreason = "add_lead DATE OF BIRTH FORMAT SHOULD BE YYYY-MM-DD";
        }
    }

    # post_date validation
    if (OSDstrlen($xml->params->post_date) > 1 and $status != "ERROR") {
        $dm = Array();
        if (preg_match('/(19[0-9][0-9]|20[0-9][0-9])-(0[1-9]|[1-9]|1[012])-(0[1-9]|[1-9]|[12][0-9]|3[01])$/ix', $xml->params->post_date, $dm)) {
            $xml->params->post_date = date('Y-m-d H:i:s',strtotime($dm[1] . '-' . $dm[2] . '-' . $dm[3]));
        } elseif (preg_match('/(19[0-9][0-9]|20[0-9][0-9])-(0[1-9]|[1-9]|1[012])-(0[1-9]|[1-9]|[12][0-9]|3[01])[ |T](.*)/ix', $xml->params->post_date, $dm)) {
            $xml->params->post_date = date('Y-m-d H:i:s',strtotime($dm[1] . '-' . $dm[2] . '-' . $dm[3] . ' ' . $dm[4]));
        } else {
            $status = "ERROR";
            $reason = "Invalid Date of Birth format, should be YYYY-MM-DD or ISO-8601 (2010-01-01T00:00:00).";
            $vdreason = "add_lead DATE OF BIRTH FORMAT SHOULD BE YYYY-MM-DD";
        }
    }

    # Check list_id and that it exists.
    if ($xml->params->list_id == "" and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Must specify List ID.";
        $vdreason = "add_lead INVALID LIST ID";
    } elseif ($status != "ERROR") {
        $list = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($xml->params->list_id)) );
        if ($list['campaign_id'] != "") {
            $list_campaign_id = $list['campaign_id'];
            if ($xml['debug'] > 0) $debug .= sprintf("List: %s  Campaign: %s\n", $list_campaign_id, $xml->params->list_id);
        } else {
            $status = "ERROR";
            $reason = "List ID not found.";
            $vdreason = "add_lead INVALID LIST ID";
        }
    }

    # Get a lists within the above campaign.
    if ($status != "ERROR") {
        $lists = get_krh($link, 'osdial_lists', 'list_id,active', '', sprintf("campaign_id='%s'",mres($list_campaign_id)), '');
        if (!is_array($lists)) {
            $status = "ERROR";
            $reason = "Could not match List ID to campaigns.";
            $vdreason = "add_lead INVALID LIST ID";
        } else {
            foreach ($lists as $list) {
                if ($list['list_id'] != "") {
                    $dup_list .= "'" . mres($list['list_id']) . "',";
                    if ($list['active'] == "Y")
                        $dup_list_active .= "'" . mres($list['list_id']) . "',";
                }
            }
            $dup_list = rtrim($dup_list,',');
            $dup_list_active = rtrim($dup_list_active,',');
            if ($xml['debug'] > 0) $debug .= sprintf("DUP_LIST: %s\nDUP_LIST_ACTIVE: %s\n", $dup_list, $dup_list_active);
        }
    }

    # Get a lists within the system.
    if ($status != "ERROR") {
        $lists = get_krh($link, 'osdial_lists', 'list_id,active', '', '', '');
        if (!is_array($lists)) {
            $status = "ERROR";
            $reason = "Could not find any lists in system.";
            $vdreason = "add_lead INVALID LIST ID";
        } else {
            foreach ($lists as $list) {
                if ($list['list_id'] != "") {
                    $dup_syslist .= "'" . mres($list['list_id']) . "',";
                    if ($list['active'] == "Y")
                        $dup_syslist_active .= "'" . mres($list['list_id']) . "',";
                }
            }
            $dup_syslist = rtrim($dup_syslist,',');
            $dup_syslist_active = rtrim($dup_syslist_active,',');
            if ($xml['debug'] > 0) $debug .= sprintf("DUP_SYSLIST: %s\nDUP_SYSLIST_ACTIVE: %s\n", $dup_syslist, $dup_syslist_active);
        }
    }

    # Check if in DNC unless explicitly N.
    if (preg_match('/^Y$|^YES$|^T$|^TRUE$|^1$|^AREACODE$/i',$xml->params->dnc_check) and $status != "ERROR") {
        if (preg_match('/^AREACODE$/i',$xml->params->dnc_check))
            $xml->params->phone_number = OSDsubstr($xml->params->phone_number, 0, 3) . 'XXXXXXX';
        $dncc=0;
        $dncs=0;
        $dncsskip=0;

        if ($config['settings']['enable_multicompany'] > 0) {
            if (preg_match('/COMPANY|BOTH/',$comp['dnc_method'])) {
                $dnc_where = sprintf("company_id='%s' AND phone_number='%s'",$comp['id'],mres($xml->params->phone_number));
                if ($xml['debug'] > 0) $debug .= "DNC_COMP_WHERE: " . $dnc_where . "\n";
                $dnc = get_first_record($link, 'osdial_dnc_company', 'count(*) AS count', $dnc_where);
                $dncs = $dnc['count'];
                $dncc=$dnc['count'];
            }
            if (preg_match('/COMPANY/',$comp['dnc_method'])) $dncsskip++;
        }

        if ($dncsskip==0) {
            $dnc_where = sprintf("phone_number='%s'",mres($xml->params->phone_number));
            if ($xml['debug'] > 0) $debug .= "DNC_WHERE: " . $dnc_where . "\n";
            $dnc = get_first_record($link, 'osdial_dnc', 'count(*) AS count', $dnc_where);
            $dncs = $dnc['count'];
        }

        if ($dncs > 0 or $dncc > 0) {
            $status = "ERROR";
            $reason = "Phone Number is in ";
            if ($dncs > 0 and $dncc > 0) {
                $reason .= "System and Company";
            } elseif ($dncs > 0) {
                $reason .= "System";
            } elseif ($dncc > 0) {
                $reason .= "Company";
            }
            $reason .= " DNC list.";
            $vdreason = "add_lead PHONE NUMBER IN DNC";
        }
    }

    # Duplicate Checks
    if ($xml->params->duplicate_check == "") $xml->params->duplicate_check = "DUPLIST";
    if (!preg_match('/(^NO$|^N$|^F$|^FALSE$|^0$)/',$xml->params->duplicate_check) and $status != "ERROR") {
        $dup_error = '';
        $dup_error2 = '';
        $dup_where = '';
        if (preg_match('/^CAMPAIGN$|^C$|^DUPCAMP$/i',$xml->params->duplicate_check)) {
            # Check for duplicates in all lists within campaign.
            $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($xml->params->phone_number),$dup_list);
            $dup_error = sprintf("Found duplicate lead while checking ALL lists in Campaign:%s for Phone#:%s.\n", $list_campaign_id, $xml->params->phone_number);
            $dup_error2 = "ALL LISTS WITHIN CAMPAIGN";

        } elseif (preg_match('/^CAMPAIGNACTIVE$|^CA$|^DUPCAMPACT$/i',$xml->params->duplicate_check)) {
            # Check for duplicates in all active lists within campaign.
            $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($xml->params->phone_number),$dup_list_active);
            $dup_error = sprintf("Found duplicate lead while checking ACTIVE lists in Campaign:%s for Phone#:%s.\n", $list_campaign_id, $xml->params->phone_number);
            $dup_error2 = "ACTIVE LISTS WITHIN CAMPAIGN";

        } elseif (preg_match('/^SYSTEM$|^S$|^DUPSYS$/i',$xml->params->duplicate_check)) {
            # Check for duplicates in all lists within system.
            $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($xml->params->phone_number),$dup_syslist);
            $dup_error = sprintf("Found duplicate lead while checking ALL lists in System for Phone#:%s.\n", $xml->params->phone_number);
            $dup_error2 = "ALL LISTS WITHIN SYSTEM";

        } elseif (preg_match('/^SYSTEMACTIVE$|^SA$|^DUPSYSACT$/i',$xml->params->duplicate_check)) {
            $dup_where = sprintf("phone_number='%s' AND list_id IN (%s)",mres($xml->params->phone_number),$dup_syslist_active);
            $dup_error = sprintf("Found duplicate lead while checking ACTIVE lists in System for Phone#:%s.\n", $xml->params->phone_number);
            $dup_error2 = "ACTIVE LISTS WITHIN SYSTEM";

        # (default) Check for duplicates in this list (LIST || L || DUPLIST).
        } else {
            $dup_where = sprintf("phone_number='%s' AND list_id='%s'",mres($xml->params->phone_number),mres($xml->params->list_id));
            $dup_error = sprintf("Found duplicate lead while checking List:%s for Phone#:%s.\n",$xml->params->list_id, $xml->params->phone_number);
            $dup_error2 = "LIST";
        }

        # Check for the duplicate.
        if ($xml['debug'] > 0) $debug .= "DUP_WHERE: " . $dup_where . "\n";
        $dup = get_first_record($link, 'osdial_list', 'lead_id,list_id', $dup_where);
        if ($dup['lead_id'] > 0) {
            $status = "ERROR";
            $reason = $dup_error;
            $vdreason = "add_lead DUPLICATE PHONE NUMBER IN " . $dup_error2;
        }
    }

    # Add lead now.
    $lead_id=0;
    $hopper_id=0;
    $gmt_offset_now=0;
    $af_count=0;
    $af_success=0;
    $af_failed=0;
    $af_ids = Array();
    $af_id_stats = Array();
    if ($status != "ERROR") {
        # Set GMT offset, using the POSTAL lookup method as the default
        if ($xml->params->gmt_lookup_method == "")
            $xml->params->gmt_lookup_method = "POSTAL";
        $gmtl = lookup_gmt(
            mres($xml->params->phone_code),
            mres(OSDsubstr($xml->params->phone_number,0,3)),
            mres(OSDstrtoupper($xml->params->state)),
            $local_gmt,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,
            mres(OSDstrtoupper($xml->params->gmt_lookup_method)),
            mres(OSDstrtoupper($xml->params->postal_code)) );
        $gmt_offset_now = $gmtl[0];
        $post = $gmtl[1];
        if ($xml['debug'] > 0) $debug .= sprintf("GMT_OFFSET_NOW: %s\n", $gmt_offset_now);

        if ($xml['test'] < 1) {
            $ins =  "INSERT INTO osdial_list SET status='NEW',called_since_last_reset='N',entry_date=NOW(),last_local_call_time=NOW(),";
            $ins .= sprintf("user='%s',vendor_lead_code='%s',source_id='%s',list_id='%s',", mres($xml->params->agent), mres($xml->params->vendor_lead_code), mres($xml->params->source_id), mres($xml->params->list_id) );
            $ins .= sprintf("gmt_offset_now='%s',phone_code='%s',phone_number='%s',title='%s',", mres($gmt_offset_now), mres($xml->params->phone_code), mres($xml->params->phone_number), mres($xml->params->title) );
            $ins .= sprintf("first_name='%s',middle_initial='%s',last_name='%s',address1='%s',", mres($xml->params->first_name), mres($xml->params->middle_initial), mres($xml->params->last_name), mres($xml->params->address1) );
            $ins .= sprintf("address2='%s',address3='%s',city='%s',state='%s',", mres($xml->params->address2), mres($xml->params->address3), mres($xml->params->city), mres(OSDstrtoupper($xml->params->state)) );
            $ins .= sprintf("province='%s',postal_code='%s',", mres($xml->params->province), OSDstrtoupper(mres($xml->params->postal_code)) );
            $ins .= sprintf("country_code='%s',gender='%s',", mres(OSDstrtoupper($xml->params->country_code)), mres(OSDstrtoupper($xml->params->gender)) );
            $ins .= sprintf("date_of_birth='%s',alt_phone='%s',email='%s',custom1='%s',", mres($xml->params->date_of_birth), mres($xml->params->alt_phone), mres($xml->params->email), mres($xml->params->custom1) );
            $ins .= sprintf("comments='%s',custom2='%s',external_key='%s',cost='%s',", mres($xml->params->comments), mres($xml->params->custom2), mres($xml->params->external_key), mres($xml->params->cost) );
            $ins .= sprintf("post_date='%s';", mres($xml->params->post_date) );
            if ($xml['debug'] > 0) $debug .= "LEAD INSERT: " . $ins . "\n";
            $rslt=mysql_query($ins, $link);
            $insres = mysql_affected_rows($link);
            if ($insres < 1) {
                $status = "ERROR";
                $reason = "Lead insertion failure.";
                $vdreason = "add_lead LEAD HAS NOT BEEN ADDED";
            } else {
                $lead_id = mysql_insert_id($link);
                foreach ($xml->params->additional_fields->additional_field as $af) {
                    $form = get_first_record($link, 'osdial_campaign_forms', '*', sprintf("name='%s'", mres($af['form'])) );
                    $field = get_first_record($link, 'osdial_campaign_fields', '*', sprintf("form_id='%s' AND name='%s'", mres($form['id']), mres($af['field'])) );
                    $afins = sprintf("INSERT INTO osdial_list_fields SET lead_id='%s',field_id='%s',value='%s';", mres($lead_id), mres($field['id']), mres($af) );
                    if ($xml['debug'] > 0) $debug .= "AF INSERT: " . $afins . "\n";
                    $rslt=mysql_query($afins, $link);
                    $afinsres = mysql_affected_rows($link);
                    if ($afinsres > 0) {
                        $af_ids[] = mysql_insert_id($link);
                        $af_id_stats[$af_count] = 1;
                        $af_success++;
                    } else {
                        $af_id_stats[$af_count] = 0;
                        $af_failed++;
                    }
                    $af_count++;
                 }
            }

            if (preg_match('/^Y$|^YES$|^T$|^TRUE$|^1$/i',$xml->params->add_to_hopper) and $status != "ERROR") {
                $camp = get_first_record($link, 'osdial_campaigns', '*', sprintf("campaign_id='%s'",mres($list_campaign_id)) );
                $ldialable = 1;
                $cdialable = 1;
                $state = OSDstrtoupper($xml->params->state);
                if (preg_match('/^$|^DEFAULT$/i',$xml->params->hopper_local_call_time_check) and $camp['local_call_time'] != "") $xml->params->hopper_local_call_time_check = "Y";
                if (preg_match('/^Y$|^YES$|^T$|^TRUE$|^1$|^NOSTATE$/i',$xml->params->hopper_local_call_time_check)) {
                    if (preg_match('/^NOSTATE$/',$xml->params->hopper_local_call_time_check)) $state = "";
                    $ldialable = dialable_gmt($xml['debug'],$link,$camp['local_call_time'],$gmt_offset_now,$state);
                }
                if (preg_match('/^Y$|^YES$|^T$|^TRUE$|^1$/i',$xml->params->hopper_campaign_call_time_check) and $camp['campaign_call_time'] == "") $xml->params->hopper_campaign_call_time_check = "N";
                if (preg_match('/^$|^DEFAULT$/i',$xml->params->hopper_campaign_call_time_check) and $camp['campaign_call_time'] != "") $xml->params->hopper_campaign_call_time_check = "Y";
                if (preg_match('/^Y$|^YES$|^T$|^TRUE$|^1$/i',$xml->params->hopper_campaign_call_time_check))
                    $cdialable = dialable_gmt($xml['debug'],$link,$camp['campaign_call_time'],$local_gmt,'');
                if ($xml['debug'] > 0) $debug .= sprintf("Local Call-Time Dialable: %s, %s, %s\nCampaign Call-Time Dialable: %s, %s\n", $ldialable, $gmt_offset_now, $state, $cdialable, $local_gmt);
                if ($ldialable < 1) {
                    $status = "HOPPERNOTICE";
                    $reason = "Lead not added to hopper, current time is outside the allowed calling time of the lead for its timezone.";
                    $vdreason = "add_lead NOT ADDED TO HOPPER, OUTSIDE OF LOCAL TIME";
                } elseif ($cdialable < 1) {
                    $status = "HOPPERNOTICE";
                    $reason = "Lead not added to hopper, current time is outside the allowed calling time of the campaign.";
                    $vdreason = "add_lead NOT ADDED TO HOPPER, OUTSIDE OF CAMPAIGN CALL TIME";
                } else {
                    $hopins = "INSERT INTO osdial_hopper SET status='API',";
                    $hopins .= sprintf("lead_id='%s',campaign_id='%s',list_id='%s',user='%s',", mres($lead_id), mres($list_campaign_id), mres($xml->params->list_id), mres($xml->params->agent) );
                    $hopins .= sprintf("gmt_offset_now='%s',state='%s',priority='%s';", mres($gmt_offset_now), mres(OSDstrtoupper($xml->params->state)), mres($xml->params->hopper_priority));
                    if ($xml['debug'] > 0) $debug .= "HOPPER INSERT: " . $hopins . "\n";
                    $rslt=mysql_query($hopins, $link);
                    $hopres = mysql_affected_rows($link);
                    $status = "HOPPERNOTICE";
                    if ($hopres > 0) {
                        $hopper_id = mysql_insert_id($link);
                        $reason = "Lead added to hopper.";
                        $vdreason = sprintf("add_lead ADDED TO HOPPER - %s|%s|%s|%s",$xml->params->phone_number,$lead_id,$hopper_id,$xml['user']);
                    } else {
                        $reason = "Lead not added to hopper.";
                        $vdreason = "add_lead NOT ADDED TO HOPPER";
                    }
                }
            }
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->phone_number = (string)$xml->params->phone_number;
        $result->result->record[0]->list_id = (string)$xml->params->list_id;
        $result->result->record[0]->lead_id = $lead_id;
        $result->result->record[0]->gmt_offset = $gmt_offset_now;
        $result->result->record[0]->addition_fields['total'] = $af_count;
        $result->result->record[0]->addition_fields['success'] = $af_success;
        $result->result->record[0]->addition_fields['failed'] = $af_failed;
        $vdhopper='';
        if ($status == "HOPPERNOTICE") {
            $vdhopper = "\nNOTICE: " . $vdreason;
            $result->result->record[0]->hopper_notice = $reason;
            if ($hopper_id > 0) $result->result->record[0]->hopper_id = $hopper_id;
        }
        $afc=0;
        foreach ($af_ids as $af_id) {
            $result->result->record[0]->addition_fields->addChild("field");
            $result->result->record[0]->addition_fields->field[$afc]['id'] = $af_id;
            $result->result->record[0]->addition_fields->field[$afc]['success'] = $af_id_stats[$afc];
            $afc++;
        }
        $status = "SUCCESS";
        $reason = "Lead added.";
        $vdreason = sprintf("add_lead LEAD HAS BEEN ADDED - %s|%s|%s|%s|%s",$xml->params->phone_number,$xml->params->list_id,$lead_id,$gmt_offset_now,$xml['user']) . $vdhopper;
    }
    $result->status['code'] = $status;
    $result->status = $reason;



# Function to list the campaigns that the authenticated user has access too.
} else if ($xml['function'] == "list_campaigns" and $xml['mode'] == 'admin') {
    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['user_level'] < 1) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "list_campaigns USER DOES NOT HAVE PERMISSION TO ACCESS CAMPAIGNS.";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['user_level']);
    }

    if ($status != "ERROR") {
        $campaigns = get_krh($link, 'osdial_campaigns', '*', sprintf("campaign_id IN %s",$LOG['allowed_campaignsSQL']) );

        if (is_array($campaigns)) {
            $camp_cnt=0;
            $result->result['records'] = '0';
            foreach ($campaigns as $campaign) {
                $result->result->addChild("record");
                $result->result->record[$camp_cnt]['id'] = $camp_cnt;
                $result->result->record[$camp_cnt]->campaign_id = $campaign['campaign_id'];
                $result->result->record[$camp_cnt]->campaign_name = $campaign['campaign_name'];
                $result->result->record[$camp_cnt]->active = $campaign['active'];
                $camp_outonly = '1';
                if (OSDstrlen($campaign['closer_campaigns']) > 5) $camp_outonly = '0';
                $result->result->record[$camp_cnt]->outbound_only = $camp_outonly;
                $camp_cnt++;
            }
            $result->result['records'] = $camp_cnt;
            $status = "SUCCESS";
            $result->status['code'] = $status;
            $result->status = "Listing " . $result->result['records'] . " campaigns.";
        } else {
            $status = "ERROR";
            $reason = "Could not find any allowed campaigns.";
            $vdstatus = "ERROR";
            $vdreason = $reason;
        }
    }



# Function to list the user_groups that the authenticated user has access too.
} else if ($xml['function'] == "list_usergroups" and $xml['mode'] == 'admin') {
    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['user_level'] < 1) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "list_usergroups USER DOES NOT HAVE PERMISSION TO ACCESS CAMPAIGNS.";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['user_level']);
    }

    if ($status != "ERROR") {
        $usergroups = get_krh($link, 'osdial_user_groups', '*', sprintf("user_group IN %s",$LOG['allowed_usergroupsSQL']) );

        if (is_array($usergroups)) {
            $ug_cnt=0;
            $result->result['records'] = '0';
            foreach ($usergroups as $usergroup) {
                $result->result->addChild("record");
                $result->result->record[$ug_cnt]['id'] = $ug_cnt;
                $result->result->record[$ug_cnt]->user_group = $usergroup['user_group'];
                $result->result->record[$ug_cnt]->group_name = $usergroup['group_name'];
                $ug_cnt++;
            }
            $result->result['records'] = $ug_cnt;
            $status = "SUCCESS";
            $result->status['code'] = $status;
            $result->status = "Listing " . $result->result['records'] . " usergroups.";
        } else {
            $status = "ERROR";
            $reason = "Could not find any allowed usergroups.";
            $vdstatus = "ERROR";
            $vdreason = $reason;
        }
    }


# Function not defined.
} else {
    $result->status['code'] = "ERROR";
    $result->status = "Invalid Function.";
}





# Set end date/time and epoch, calculate runtime.
$end = date("Y-m-d H:i:s");
$end_epoch = date("U");
$result->status['end'] = $end;
$result->status['end_epoch'] = $end_epoch;
$result->status['runtime'] = ($end_epoch - $start_epoch);
if ($xml['debug'] > 0 and $file_debug==0)
    $result->debug = $debug;


# Export and finish
if ($xml['vdcompat'] > 0) {
    if ($vdstatus == "") $vdstatus = $result->status['code'];
    if ($vdreason == "") $vdreason = $result->status;
    echo $vdstatus . ": " . $vdreason . "\n";
    if ($xml['debug'] > 0 and $file_debug==0)
        echo "\n\nDEBUG\n-------------------------------------------\n" . $result->debug . "\n\n";
} elseif ($xml['json'] > 0) {
    if ($xml['pretty'] > 0) {
        #echo json_encode($result,JSON_PRETTY_PRINT);
        echo json_encode($result);
    } else {
        echo json_encode($result);
    }
} else {
    if ($xml['pretty'] > 0) {
        echo prettyXML($result->asXML());
    } else {
        echo $result->asXML();
    }
}

if ($file_debug>0) {
    $result->debug = $debug;
    $fps = "\n###### START ######\n# $start\n" . prettyXML($result->asXML()) . "\n#######  END  ######\n# " . date("Y-m-d H:i:s") . "\n\n";
    if ($WeBRooTWritablE > 0) {
        $fp = fopen($WeBServeRRooT . "/admin/api_debug_output.txt", "a");
        fwrite ($fp, $fps);
        fclose($fp);
    }
}


exit;



?>
