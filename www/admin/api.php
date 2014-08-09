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
    if ($xml->params->organization == "" ) $xml->params->organization = get_variable("organization");
    if ($xml->params->organization_title == "" ) $xml->params->organization_title = get_variable("organization_title");

    # following added for update_lead
    if ($xml->params->called_since_last_reset == "" ) $xml->params->called_since_last_reset = get_variable("called_since_last_reset");
    if ($xml->params->status == "" ) $xml->params->status = get_variable("status");
    if ($xml->params->status_extended == "" ) $xml->params->status_extended = get_variable("status_extended");
    if ($xml->params->lead_id == "" ) $xml->params->lead_id = get_variable("lead_id");

    if ($xml->params->campaign_id == "" ) $xml->params->campaign_id = get_variable("campaign_id");
    if ($xml->params->delete_leads == "" ) $xml->params->delete_leads = get_variable("delete_leads");
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


##### Function to get vmail message counts.
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


##### Function to delete a campaign.
} elseif ($xml['function'] == "delete_campaign" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['delete_campaigns'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "delete_campaign USER DOES NOT HAVE PERMISSION TO DELETE CAMPAIGNS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, modify_leads=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['delete_campaigns'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "delete_campaign USER DOES NOT HAVE PERMISSION TO DELETE CAMPAIGNS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # campaign ID check.
    if (OSDstrlen($xml->params->campaign_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Campaign ID must be defined...";
        $vdreason = "delete_campaign CAMPAIGN_ID MISSING";
    }

    # check if campaign, list_id are allowed for user.
    if ($status != "ERROR") {
        if (!OSDpreg_match('/:'.$xml->params->campaign_id.':/',$LOG['allowed_campaignsSTR'])) {
            $status = "ERROR";
            $reason = "Campaign ID not found.";
            $vdreason = "delete_campaign INVALID CAMPAIGN ID";
        }
    }

    if ($status != "ERROR") {
        if ($xml['test'] < 1) {
            $del =  sprintf("DELETE FROM osdial_campaigns WHERE campaign_id='%s';",$xml->params->campaign_id);

            if ($xml['debug'] > 0) $debug .= "CAMPAIGN DELETE: " . $del . "\n";
            $rslt=mysql_query($del, $link);
            $dres = mysql_affected_rows($link);
            if ($dres < 1) {
                $status = "ERROR";
                $reason = "Campaign delete failure.";
                $vdreason = "delete_campaign CAMPAIGN HAS NOT BEEN DELETED";
            }
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->campaign_id = $xml->params->campaign_id;
        $status = "SUCCESS";
        $reason = "Campaign deleted.";
        $vdreason = sprintf("delete_campaign CAMPAIGN HAS BEEN DELETED - %s|%s",$xml->params->campaign_id,$xml['user']);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to delete a lead.
} elseif ($xml['function'] == "delete_lead" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['delete_lists'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "delete_lead USER DOES NOT HAVE PERMISSION TO DELETE LISTS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, modify_leads=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['delete_lists'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "delete_lead USER DOES NOT HAVE PERMISSION TO DELETE LISTS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # lead ID check.
    if (OSDstrlen($xml->params->lead_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Lead ID must be defined...";
        $vdreason = "delete_lead LEAD_ID MISSING";
    }

    # check if campaign, list_id are allowed for user.
    if ($status != "ERROR") {
        $lead = get_first_record($link, 'osdial_list', 'list_id', sprintf("lead_id='%s'",mres($xml->params->lead_id)) );
        if ($lead['list_id'] != "") {
            $list = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($lead['list_id'])) );
            if ($list['campaign_id'] != "") {
                if (!OSDpreg_match('/:'.$list['campaign_id'].':/',$LOG['allowed_campaignsSTR'])) {
                    $status = "ERROR";
                    $reason = "Lead ID not found.";
                    $vdreason = "delete_lead INVALID LIST ID";
                }
            }
        } else {
            $status = "ERROR";
            $reason = "Lead ID not found.";
            $vdreason = "delete_lead INVALID LEAD ID";
        }
    }

    if ($status != "ERROR") {
        if ($xml['test'] < 1) {
            $del =  sprintf("DELETE FROM osdial_list WHERE lead_id='%s';",$xml->params->lead_id);

            if ($xml['debug'] > 0) $debug .= "LEAD DELETE: " . $del . "\n";
            $rslt=mysql_query($del, $link);
            $dres = mysql_affected_rows($link);
            if ($dres < 1) {
                $status = "ERROR";
                $reason = "Lead delete failure.";
                $vdreason = "delete_lead LEAD HAS NOT BEEN DELETED";
            }
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->lead_id = $xml->params->lead_id;
        $status = "SUCCESS";
        $reason = "Lead deleted.";
        $vdreason = sprintf("delete_lead LEAD HAS BEEN DELETED - %s|%s",$xml->params->lead_id,$xml['user']);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to delete a list.
} elseif ($xml['function'] == "delete_list" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['delete_lists'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "delete_list USER DOES NOT HAVE PERMISSION TO DELETE LISTS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, modify_leads=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['delete_lists'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "delete_list USER DOES NOT HAVE PERMISSION TO DELETE LISTS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # lead ID check.
    if (OSDstrlen($xml->params->list_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "List ID must be defined...";
        $vdreason = "delete_list LIST_ID MISSING";
    }

    # check if campaign, list_id are allowed for user.
    if ($status != "ERROR") {
        $list = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($xml->params->list_id)) );
        if ($list['campaign_id'] != "") {
            if (!OSDpreg_match('/:'.$list['campaign_id'].':/',$LOG['allowed_campaignsSTR'])) {
                $status = "ERROR";
                $reason = "List ID not found.";
                $vdreason = "delete_list INVALID LIST ID";
            }
        } else {
            $status = "ERROR";
            $reason = "List ID not found.";
            $vdreason = "delete_list INVALID LIST ID";
        }
    }

    if ($status != "ERROR") {
        if ($xml['test'] < 1) {
            $del =  sprintf("DELETE FROM osdial_lists WHERE list_id='%s';",$xml->params->list_id);

            if ($xml['debug'] > 0) $debug .= "LIST DELETE: " . $del . "\n";
            $rslt=mysql_query($del, $link);
            $dres = mysql_affected_rows($link);
            if ($dres < 1) {
                $status = "ERROR";
                $reason = "List delete failure.";
                $vdreason = "delete_list LIST HAS NOT BEEN DELETED";
            } else {
                if (preg_match('/^[TtYy1]|1/',$xml->params->delete_leads)) {
                    $del =  sprintf("DELETE FROM osdial_list WHERE list_id='%s';",$xml->params->list_id);

                    if ($xml['debug'] > 0) $debug .= "LEADS DELETE: " . $del . "\n";
                    $rslt=mysql_query($del, $link);
                    $dres = mysql_affected_rows($link);
                }
            }
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->list_id = $xml->params->list_id;
        $status = "SUCCESS";
        if ($xml->params->delete_leads) {
            $reason = "List and its leads deleted.";
            $vdreason = sprintf("delete_list LIST AND ITS LEADS HAVE BEEN DELETED - %s|%s",$xml->params->list_id,$xml['user']);
        } else {
            $reason = "List deleted.";
            $vdreason = sprintf("delete_list LIST HAS BEEN DELETED - %s|%s",$xml->params->list_id,$xml['user']);
        }
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to get a campaign.
} elseif ($xml['function'] == "get_campaign" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "get_campaign USER DOES NOT HAVE PERMISSION TO GET CAMPAIGNS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "get_campaign USER DOES NOT HAVE PERMISSION TO GET CAMPAIGNS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # campaign ID check.
    if (OSDstrlen($xml->params->campaign_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Campaign ID must be defined...";
        $vdreason = "get_campaign CAMPAIGN_ID MISSING";
    }

    # check if campaign are allowed for user.
    if ($status != "ERROR") {
        if (!OSDpreg_match('/:'.$xml->params->campaign_id.':/',$LOG['allowed_campaignsSTR'])) {
            $status = "ERROR";
            $reason = "Campaign ID not found.";
            $vdreason = "get_campaign INVALID CAMPAIGN ID";
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $campaign = get_first_record($link, 'osdial_campaigns', '*', sprintf("campaign_id='%s'",mres($xml->params->campaign_id)) );
        $vdout = '';
        foreach ($campaign as $k => $v) {
            if (preg_match('/^(email_templates|xfer_groups|closer_campaigns|auto_alt_dial_statuses|dial_statuses)$/',$k)) {
                $v = preg_replace('/^\s+/','',$v);
                $v = preg_replace('/\s+\-$/','',$v);
                $vitems = preg_split('/\s+/',$v);
                $result->result->record[0]->{$k}->addChild('item');
                $icnt = 0;
                foreach ($vitems as $vi) {
                    if ($vi != '') {
                        $result->result->record[0]->{$k}->item[$icnt] = $vi;
                        $icnt++;
                    }
                }
            } else {
                $result->result->record[0]->{$k} = $v;
            }
            $vdout .= "\n".$k.":".$v;
        }
        $status = "SUCCESS";
        $reason = "Got campaign.";
        $vdreason = sprintf("get_campaign GOT CAMPAIGN - %s|%s%s",$xml->params->campaign_id,$xml['user'],$vdout);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to get a lead.
} elseif ($xml['function'] == "get_lead" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "get_lead USER DOES NOT HAVE PERMISSION TO GET LEADS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "get_lead USER DOES NOT HAVE PERMISSION TO GET LEADS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # lead ID check.
    if (OSDstrlen($xml->params->lead_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Lead ID must be defined...";
        $vdreason = "get_lead LEAD_ID MISSING";
    }

    # check if campaign, list_id are allowed for user.
    if ($status != "ERROR") {
        $lead = get_first_record($link, 'osdial_list', 'list_id', sprintf("lead_id='%s'",mres($xml->params->lead_id)) );
        if ($lead['list_id'] != "") {
            $list = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($lead['list_id'])) );
            if ($list['campaign_id'] != "") {
                if (!OSDpreg_match('/:'.$list['campaign_id'].':/',$LOG['allowed_campaignsSTR'])) {
                    $status = "ERROR";
                    $reason = "Lead ID not found.";
                    $vdreason = "get_lead INVALID LIST ID";
                }
            }
        } else {
            $status = "ERROR";
            $reason = "Lead ID not found.";
            $vdreason = "get_lead INVALID LEAD ID";
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $lead = get_first_record($link, 'osdial_list', '*', sprintf("lead_id='%s'",mres($xml->params->lead_id)) );
        $vdout = '';
        foreach ($lead as $k => $v) {
            $result->result->record[0]->{$k} = $v;
            $vdout .= "\n".$k.":".$v;
        }
        $status = "SUCCESS";
        $reason = "Got lead.";
        $vdreason = sprintf("get_lead GOT LEAD - %s|%s%s",$xml->params->lead_id,$xml['user'],$vdout);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to get a list.
} elseif ($xml['function'] == "get_list" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "get_list USER DOES NOT HAVE PERMISSION TO GET LISTS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "get_list USER DOES NOT HAVE PERMISSION TO GET LISTS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # list ID check.
    if (OSDstrlen($xml->params->list_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "List ID must be defined...";
        $vdreason = "get_list LIST_ID MISSING";
    }

    # check if campaign, list_id are allowed for user.
    if ($status != "ERROR") {
        $list = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($xml->params->list_id)) );
        if ($list['campaign_id'] != "") {
            if (!OSDpreg_match('/:'.$list['campaign_id'].':/',$LOG['allowed_campaignsSTR'])) {
                $status = "ERROR";
                $reason = "List ID not found.";
                $vdreason = "get_list INVALID LIST ID";
            }
        } else {
            $status = "ERROR";
            $reason = "List ID not found.";
            $vdreason = "get_list INVALID LIST ID";
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $list = get_first_record($link, 'osdial_lists', '*', sprintf("list_id='%s'",mres($xml->params->list_id)) );
        $vdout = '';
        foreach ($list as $k => $v) {
            $result->result->record[0]->{$k} = $v;
            $vdout .= "\n".$k.":".$v;
        }
        $status = "SUCCESS";
        $reason = "Got list.";
        $vdreason = sprintf("get_list GOT LIST - %s|%s%s",$xml->params->list_id,$xml['user'],$vdout);
    }
    $result->status['code'] = $status;
    $result->status = $reason;




##### Function to update a campaign.
} elseif ($xml['function'] == "update_campaign" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['modify_campaigns'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "update_campaign USER DOES NOT HAVE PERMISSION TO MODIFY CAMPAIGNS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, modify_campaigns=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['modify_campaigns'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "update_campaign USER DOES NOT HAVE PERMISSION TO MODIFY CAMPAIGNS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # Validate fields.

    # campaign ID check.
    if (OSDstrlen($xml->params->campaign_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Campaign ID must be defined...";
        $vdreason = "update_campaign CAMPAIGN_ID MISSING";
    }

    # check if campaign, list_id, and/or new list_id are allowed for user.
    if ($status != "ERROR") {
        if (!OSDpreg_match('/:'.$xml->params->campaign_id.':/',$LOG['allowed_campaignsSTR'])) {
            $status = "ERROR";
            $reason = "Campaign ID not found.";
            $vdreason = "update_campaign INVALID CAMPAIGN ID";
        }
    }

    if ($status != "ERROR") {
        $flds = ['email_templates','xfer_groups','closer_campaigns','auto_alt_dial_statuses','dial_statuses'];
        foreach ($flds as $fld) {
            if ($xml->params->{$fld}) {
                $istr = ' ';
                foreach ($xml->params->{$fld}->item as $item) {
                    $istr .= $item . ' ';
                }
                $istr .= '-';
                if ($istr == ' -') $istr = '';
                $xml->params->{$fld} = $istr;
            }
        }
        if ($xml['test'] < 1) {
            $upd =  sprintf("UPDATE osdial_campaigns SET campaign_changedate=NOW(),");
            $flds = ['campaign_name', 'active', 'lead_order', 'allow_closers', 'hopper_level', 'auto_dial_level',
                'web_form_address', 'web_form_address2', 'next_agent_call', 'local_call_time', 'voicemail_ext',
                'dial_timeout', 'campaign_cid', 'campaign_cid_name', 'campaign_vdad_exten', 'campaign_recording', 'campaign_rec_filename',
                'campaign_script', 'get_call_launch', 'am_message_exten', 'amd_send_to_vmx', 'alt_number_dialing',
                'scheduled_callbacks', 'lead_filter_id', 'drop_call_seconds', 'use_internal_dnc', 'allcalls_delay',
                'dial_method', 'available_only_ratio_tally', 'adaptive_dropped_percentage', 'adaptive_maximum_level', 'adaptive_latest_server_time',
                'auto_alt_dial', 'agent_pause_codes_active', 'campaign_description', 'campaign_stats_refresh','disable_alter_custdata',
                'no_hopper_leads_logins', 'list_order_mix', 'campaign_allow_inbound', 'manual_dial_list_id', 'default_xfer_group',
                'allow_tab_switch', 'campaign_call_time', 'preview_force_dial_time', 'manual_preview_default', 'web_form_extwindow',
                'web_form2_extwindow', 'submit_method', 'use_custom2_callerid', 'xfer_cid_mode', 'use_cid_areacode_map', 'carrier_id',
                'disable_manual_dial', 'hide_xfer_local_closer', 'hide_xfer_dial_override', 'hide_xfer_hangup_xfer', 'hide_xfer_leave_3way',
                'hide_xfer_dial_with', 'hide_xfer_hangup_both', 'hide_xfer_blind_xfer', 'hide_xfer_park_dial', 'hide_xfer_blind_vmail',
                'allow_md_hopperlist', 'ivr_id', 'safe_harbor_message', 'safe_harbor_exten', 'park_ext', 'concurrent_transfers', 'wrapup_message',
                'wrapup_seconds', 'answers_per_hour_limit', 'adaptive_dl_diff_target', 'adaptive_intensity', 'display_dialable_count', 
                'omit_phone_code', 
                'email_templates','xfer_groups','closer_campaigns','auto_alt_dial_statuses','dial_statuses'];

            foreach ($flds as $fld) {
                if ($xml->params->{$fld}) {
                    $upd .= sprintf("%s='%s',",$fld,mres($xml->params->{$fld}));
                }
            }
            $upd = OSDpreg_replace('/,$/','',$upd);

            $upd .= sprintf(" WHERE campaign_id='%s';",$xml->params->campaign_id);

            if ($xml['debug'] > 0) $debug .= "CAMPAIGN UPDATE: " . $upd . "\n";
            $rslt=mysql_query($upd, $link);
            $updres = mysql_affected_rows($link);
            if ($updres < 1) {
                $status = "ERROR";
                $reason = "Campaign update failure.";
                $vdreason = "update_campaign CAMPAIGN HAS NOT BEEN MODIFIED";
            }
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->campaign_id = $xml->params->campaign_id;
        $status = "SUCCESS";
        $reason = "Campaign updated.";
        $vdreason = sprintf("update_campaign CAMPAIGN HAS BEEN MODIFIED - %s|%s",$xml->params->campaign_id,$xml['user']);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to update a lead.
} elseif ($xml['function'] == "update_lead" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['modify_leads'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "update_lead USER DOES NOT HAVE PERMISSION TO MODIFY LEADS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, modify_leads=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['modify_leads'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "update_lead USER DOES NOT HAVE PERMISSION TO MODIFY LEADS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # Validate fields.
    if ($xml->params->phone_code) {
        $xml->params->phone_code = OSDpreg_replace('/[^0-9]/',"",$xml->params->phone_code);
        if ($xml->params->phone_code == "") $xml->params->phone_code = 1;
    }
    if ($xml->params->phone_number) $xml->params->phone_number = OSDpreg_replace('/[^0-9]/',"",$xml->params->phone_number);
    if ($xml->params->alt_phone) $xml->params->alt_phone = OSDpreg_replace('/[^0-9]/',"",$xml->params->alt_phone);
    if ($xml->params->cost) $xml->params->cost = OSDpreg_replace('/[^0-9\.]/',"",$xml->params->cost);

    # DOB validation
    if (OSDstrlen($xml->params->date_of_birth) > 1 and $status != "ERROR") {
        $dm = Array();
        if (preg_match('/(19[0-9][0-9]|20[0-9][0-9])-(0[1-9]|[1-9]|1[012])-(0[1-9]|[1-9]|[12][0-9]|3[01])([ |T].*|$)/ix', $xml->params->date_of_birth, $dm)) {
            $xml->params->date_of_birth = date('Y-m-d',strtotime($dm[1] . '-' . $dm[2] . '-' . $dm[3]));
        } else {
            $status = "ERROR";
            $reason = "Invalid Date of Birth format, should be YYYY-MM-DD or ISO-8601 (2010-01-01T00:00:00).";
            $vdreason = "update_lead DATE OF BIRTH FORMAT SHOULD BE YYYY-MM-DD";
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
            $vdreason = "update_lead DATE OF BIRTH FORMAT SHOULD BE YYYY-MM-DD";
        }
    }

    # lead ID check.
    if (OSDstrlen($xml->params->lead_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Lead ID must be defined...";
        $vdreason = "update_lead LEAD_ID MISSING";
    }

    # check if campaign, list_id, and/or new list_id are allowed for user.
    if ($status != "ERROR") {
        $lead = get_first_record($link, 'osdial_list', 'list_id', sprintf("lead_id='%s'",mres($xml->params->lead_id)) );
        if ($lead['list_id'] != "") {
            $list = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($lead['list_id'])) );
            if ($list['campaign_id'] != "") {
                if (!OSDpreg_match('/:'.$list['campaign_id'].':/',$LOG['allowed_campaignsSTR'])) {
                    $status = "ERROR";
                    $reason = "Lead ID not found.";
                    $vdreason = "update_lead INVALID LIST ID";
                }
            }
            if (OSDstrlen($xml->params->list_id) > 1 and $lead['list_id'] != $xml->params->list_id and $status != 'ERROR') {
                $list2 = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($xml->params->list_id)) );
                if ($list2['campaign_id'] != "") {
                    if (!OSDpreg_match('/:'.$list2['campaign_id'].':/',$LOG['allowed_campaignsSTR'])) {
                        $status = "ERROR";
                        $reason = "Lead ID not found.";
                        $vdreason = "update_lead INVALID LIST ID";
                    }
                }
            }
        } else {
            $status = "ERROR";
            $reason = "Lead ID not found.";
            $vdreason = "update_lead INVALID LEAD ID";
        }
    }

    if ($status != "ERROR") {
        if ($xml['test'] < 1) {
            $upd =  sprintf("UPDATE osdial_list SET modify_date=NOW(),",mres($xml->params->lead_id));
            $flds = ['status', 'called_since_last_reset', 'user', 'vendor_lead_code', 'source_id', 'list_id',
                'gmt_offset_now', 'phone_code', 'phone_number', 'title', 'first_name', 'middle_initial', 'last_name', 'address1', 'address2', 'address3',
                'city', 'state', 'province', 'postal_code', 'country_code', 'gender', 'date_of_birth', 'alt_phone', 'email', 'custom1', 'comments', 'custom2',
                'external_key', 'cost', 'post_date','organization','organization_title','status_extended'];

            foreach ($flds as $fld) {
                if ($xml->params->{$fld}) {
                    if ($fld == 'user') {
                        $upd .= sprintf("%s='%s',",$fld,mres($xml->params->agent));
                    } else {
                        $upd .= sprintf("%s='%s',",$fld,mres($xml->params->{$fld}));
                    }
                }
            }
            $upd = OSDpreg_replace('/,$/','',$upd);

            $upd .= sprintf(" WHERE lead_id='%s';",$xml->params->lead_id);

            if ($xml['debug'] > 0) $debug .= "LEAD UPDATE: " . $upd . "\n";
            $rslt=mysql_query($upd, $link);
            $updres = mysql_affected_rows($link);
            if ($updres < 1) {
                $status = "ERROR";
                $reason = "Lead update failure.";
                $vdreason = "update_lead LEAD HAS NOT BEEN MODIFIED";
            } else {
                foreach ($xml->params->additional_fields->additional_field as $af) {
                    $form = get_first_record($link, 'osdial_campaign_forms', '*', sprintf("name='%s'", mres($af['form'])) );
                    $field = get_first_record($link, 'osdial_campaign_fields', '*', sprintf("form_id='%s' AND name='%s'", mres($form['id']), mres($af['field'])) );
                    $afins = sprintf("INSERT INTO osdial_list_fields (lead_id,field_id,value) VALUES ('%s','%s','%s') ON DUPLICATE KEY UPDATE value=VALUES(value);", mres($xml->params->lead_id), mres($field['id']), mres($af) );
                    if ($xml['debug'] > 0) $debug .= "AF INSERT/UPDATE: " . $afins . "\n";
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
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->phone_number = (string)$xml->params->phone_number;
        $result->result->record[0]->lead_id = $xml->params->lead_id;
        $result->result->record[0]->addition_fields['total'] = $af_count;
        $result->result->record[0]->addition_fields['success'] = $af_success;
        $result->result->record[0]->addition_fields['failed'] = $af_failed;
        $afc=0;
        foreach ($af_ids as $af_id) {
            $result->result->record[0]->addition_fields->addChild("field");
            $result->result->record[0]->addition_fields->field[$afc]['id'] = $af_id;
            $result->result->record[0]->addition_fields->field[$afc]['success'] = $af_id_stats[$afc];
            $afc++;
        }
        $status = "SUCCESS";
        $reason = "Lead updated.";
        $vdreason = sprintf("update_lead LEAD HAS BEEN MODIFIED - %s|%s|%s",$xml->params->phone_number,$xml->params->lead_id,$xml['user']);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to update a list.
} elseif ($xml['function'] == "update_list" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['modify_lists'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "update_list USER DOES NOT HAVE PERMISSION TO MODIFY LISTS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, modify_lists=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['modify_lists'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "update_list USER DOES NOT HAVE PERMISSION TO MODIFY LISTS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # Validate fields.
    if ($xml->params->cost) $xml->params->cost = OSDpreg_replace('/[^0-9\.]/',"",$xml->params->cost);

    # list ID check.
    if (OSDstrlen($xml->params->list_id) < 1 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "List ID must be defined...";
        $vdreason = "update_list LIST_ID MISSING";
    }

    # check if campaign, list_id, and/or new list_id are allowed for user.
    if ($status != "ERROR") {
        $list = get_first_record($link, 'osdial_lists', 'campaign_id', sprintf("list_id='%s'",mres($xml->params->list_id)) );
        if ($list['campaign_id'] != "") {
            if (!OSDpreg_match('/:'.$list['campaign_id'].':/',$LOG['allowed_campaignsSTR'])) {
                $status = "ERROR";
                $reason = "Campaign ID not found.";
                $vdreason = "update_list INVALID CAMPAIGN ID";
            }
            if (OSDstrlen($xml->params->campaign_id) > 1 and $list['campaign_id'] != $xml->params->campaign_id and $status != 'ERROR') {
                if (!OSDpreg_match('/:'.$xml->params->campaign_id.':/',$LOG['allowed_campaignsSTR'])) {
                    $status = "ERROR";
                    $reason = "Campaign ID not found.";
                    $vdreason = "update_list INVALID CAMPAIGN ID";
                }
            }
        } else {
            $status = "ERROR";
            $reason = "List ID not found.";
            $vdreason = "update_list INVALID LIST ID";
        }
    }

    if ($status != "ERROR") {
        if ($xml['test'] < 1) {
            $upd =  sprintf("UPDATE osdial_lists SET list_changedate=NOW(),");
            $flds = ['list_name', 'campaign_id', 'active', 'list_description', 'scrub_dnc', 'cost',
                'web_form_address', 'web_form_address2', 'list_script'];

            foreach ($flds as $fld) {
                if ($xml->params->{$fld}) {
                    $upd .= sprintf("%s='%s',",$fld,mres($xml->params->{$fld}));
                }
            }
            $upd = OSDpreg_replace('/,$/','',$upd);

            $upd .= sprintf(" WHERE list_id='%s';",$xml->params->list_id);

            if ($xml['debug'] > 0) $debug .= "LIST UPDATE: " . $upd . "\n";
            $rslt=mysql_query($upd, $link);
            $updres = mysql_affected_rows($link);
            if ($updres < 1) {
                $status = "ERROR";
                $reason = "List update failure.";
                $vdreason = "update_list LIST HAS NOT BEEN MODIFIED";
            }
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->list_id = $xml->params->list_id;
        $status = "SUCCESS";
        $reason = "List updated.";
        $vdreason = sprintf("update_list LIST HAS BEEN MODIFIED - %s|%s",$xml->params->list_id,$xml['user']);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to add a campaign.
} elseif ($xml['function'] == "add_campaign" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['modify_campaigns'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "add_campaign USER DOES NOT HAVE PERMISSION TO MODIFY CAMPAIGNS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, modify_campaigns=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['modify_campaigns'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "add_campaign USER DOES NOT HAVE PERMISSION TO MODIFY CAMPAIGNS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # Validate fields.

    # campaign ID check.
    if (OSDstrlen($xml->params->campaign_id) < 5 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Campaign ID must be defined...";
        $vdreason = "add_campaign CAMPAIGN_ID MISSING";
    }

    # campaign Name check.
    if (OSDstrlen($xml->params->campaign_name) < 5 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Campaign Name must be defined...";
        $vdreason = "add_campaign CAMPAIGN_NAME MISSING";
    }

    # check if campaign already exists.
    if ($status != "ERROR") {
        $campaign = get_first_record($link, 'osdial_campaigns', 'count(*) as cnt', sprintf("campaign_id='%s'",mres($xml->params->campaign_id)) );
        if ($campaign['cnt']>0) {
            $status = "ERROR";
            $reason = "Campaign ID already exists.";
            $vdreason = "add_campaign CAMPAIGN ID EXISTS";
        }
    }

    if ($status != "ERROR") {
        $flds = ['email_templates','xfer_groups','closer_campaigns','auto_alt_dial_statuses','dial_statuses'];
        foreach ($flds as $fld) {
            if ($xml->params->{$fld}) {
                $istr = ' ';
                foreach ($xml->params->{$fld}->item as $item) {
                    $istr .= $item . ' ';
                }
                $istr .= '-';
                if ($istr == ' -') $istr = '';
                $xml->params->{$fld} = $istr;
            }
        }

        $flddefs = [];
        $flddefs['campaign_description'] = $xml->params->campaign_name;
        $flddefs['dial_statuses'] = ' A AA AL AM B CALLBK DROP NEW N NA -';
        $flddefs['auto_alt_dial_statuses'] = ' A AA B N NA DC -';
        $flddefs['campaign_cid'] = '0000000000';
        $flddefs['park_ext'] = '8301';
        $flddefs['campaign_vdad_exten'] = '8365';
        $flddefs['campaign_rec_exten'] = '8309';
        $flddefs['am_message_exten'] = '8320';
        $flddefs['safe_harbor_exten'] = '8307';
        $flddefs['campaign_rec_filename'] = 'CAMPAIGN_AGENT_FULLDATE_CUSTPHONE';
        $flddefs['campaign_recording'] = 'ONDEMAND';
        $flddefs['campaign_call_time'] = '24hours';
        $flddefs['local_call_time'] = '9am-9pm';
        $flddefs['manual_dial_list_id'] = '999';
        if ($config['settings']['enable_multicompany'] > 0)
            $flddefs['manual_dial_list_id'] = sprintf('%s999',(OSDsubstr($xml['user'],0,3) * 1));
        $flddefs['dial_method'] = 'MANUAL';
        $flddefs['hopper_level'] = '200';
        $flddefs['auto_dial_level'] = '1.0';
        $flddefs['adaptive_dropped_percentage'] = '3';
        $flddefs['adaptive_maximum_level'] = '3.0';
        $flddefs['adaptive_latest_server_time'] = '2100';
        $flddefs['next_agent_call'] = 'oldest_call_finish';
        $flddefs['lead_order'] = 'DOWN';
        $flddefs['list_order_mix'] = 'DISABLED';
        $flddefs['concurrent_transfers'] = 'AUTO';
        $flddefs['dial_timeout'] = '28';
        $flddefs['drop_call_seconds'] = '8';
        $flddefs['get_call_launch'] = 'NONE';
        $flddefs['web_form_address'] = '/osdial/agent/webform_redirect.php';
        $flddefs['web_form_address2'] = '/osdial/agent/webform_redirect.php';
        $flddefs['default_xfer_group'] = '---NONE---';
        $flddefs['xfer_cid_mode'] = 'CAMPAIGN';
        $flddefs['submit_method'] = 'NORMAL';
        $flddefs['auto_alt_dial'] = 'NONE';
        $flddefs['wrapup_message'] = 'Wrapup Call';
        foreach ($flddefs as $k => $v) {
            if (!$xml->params->{$k}) $xml->params->{$k} = $v;
        }

        $flds0 = ['carrier_id','preview_force_dial_time','answers_per_hour_limit','adaptive_intensity','adaptive_dl_diff_target','allcalls_delay','wrapup_seconds'];
        foreach ($flds0 as $fld) {
            if (!$xml->params->{$fld}) {
                $xml->params->{$fld} = '0';
            }
        }

        $fldsYN = [];
        $fldsY = ['no_hopper_leads_logins','campaign_allow_inbound','allow_closers','amd_send_to_vmx','safe_harbor_message','scheduled_callbacks',
                    'display_dialable_count','use_internal_dnc','allow_tab_switch','use_cid_areacode_map','manual_preview_default'];
        foreach ($fldsY as $fld) {
            if (!$xml->params->{$fld}) {
                $xml->params->{$fld} = 'Y';
            }
            $fldYN[] = $fld;
        }

        $fldsN = ['active','disable_alter_custdata','alt_number_dialing','omit_phone_code','available_only_ratio_tally','web_form_extwindow',
                    'web_form2_extwindow','agent_pause_codes_active','campaign_stats_refresh','use_custom2_callerid','disable_manual_dial','hide_xfer_local_closer',
                    'hide_xfer_dial_override','hide_xfer_hangup_xfer','hide_xfer_leave_3way','hide_xfer_dial_with','hide_xfer_hangup_both','hide_xfer_blind_xfer',
                    'hide_xfer_park_dial','hide_xfer_blind_vmail','allow_md_hopperlist'];
        foreach ($fldsN as $fld) {
            if (!$xml->params->{$fld}) {
                $xml->params->{$fld} = 'N';
            }
            $fldYN[] = $fld;
        }

        foreach ($fldsYN as $fld) {
            if (preg_match('/^[YyTt1]/',$xml->params->{$fld})) {
                $xml->params->{$fld} = 'Y';
            } elseif (preg_match('/^[NnFf0]/',$xml->params->{$fld})) {
                $xml->params->{$fld} = 'N';
            }
        }

        if ($xml['test'] < 1) {
            $ins =  sprintf("INSERT INTO osdial_campaigns SET campaign_changedate=NOW(),");
            $flds = ['campaign_id', 'campaign_name', 'active', 'lead_order', 'allow_closers', 'hopper_level', 'auto_dial_level',
                'web_form_address', 'web_form_address2', 'next_agent_call', 'local_call_time', 'voicemail_ext',
                'dial_timeout', 'campaign_cid', 'campaign_cid_name', 'campaign_vdad_exten', 'campaign_recording', 'campaign_rec_filename',
                'campaign_script', 'get_call_launch', 'am_message_exten', 'amd_send_to_vmx', 'alt_number_dialing',
                'scheduled_callbacks', 'lead_filter_id', 'drop_call_seconds', 'use_internal_dnc', 'allcalls_delay',
                'dial_method', 'available_only_ratio_tally', 'adaptive_dropped_percentage', 'adaptive_maximum_level', 'adaptive_latest_server_time',
                'auto_alt_dial', 'agent_pause_codes_active', 'campaign_description', 'campaign_stats_refresh','disable_alter_custdata',
                'no_hopper_leads_logins', 'list_order_mix', 'campaign_allow_inbound', 'manual_dial_list_id', 'default_xfer_group',
                'allow_tab_switch', 'campaign_call_time', 'preview_force_dial_time', 'manual_preview_default', 'web_form_extwindow',
                'web_form2_extwindow', 'submit_method', 'use_custom2_callerid', 'xfer_cid_mode', 'use_cid_areacode_map', 'carrier_id',
                'disable_manual_dial', 'hide_xfer_local_closer', 'hide_xfer_dial_override', 'hide_xfer_hangup_xfer', 'hide_xfer_leave_3way',
                'hide_xfer_dial_with', 'hide_xfer_hangup_both', 'hide_xfer_blind_xfer', 'hide_xfer_park_dial', 'hide_xfer_blind_vmail',
                'allow_md_hopperlist', 'ivr_id', 'safe_harbor_message', 'safe_harbor_exten', 'park_ext', 'concurrent_transfers', 'wrapup_message',
                'wrapup_seconds', 'answers_per_hour_limit', 'adaptive_dl_diff_target', 'adaptive_intensity', 'display_dialable_count', 
                'omit_phone_code', 
                'email_templates','xfer_groups','closer_campaigns','auto_alt_dial_statuses','dial_statuses'];

            foreach ($flds as $fld) {
                if ($xml->params->{$fld}) {
                    $ins .= sprintf("%s='%s',",$fld,mres($xml->params->{$fld}));
                }
            }
            $ins = OSDpreg_replace('/,$/',';',$ins);

            if ($xml['debug'] > 0) $debug .= "CAMPAIGN ADD: " . $ins . "\n";
            $rslt=mysql_query($ins, $link);
            $insres = mysql_affected_rows($link);
            if ($insres < 1) {
                $status = "ERROR";
                $reason = "Campaign insert failure.";
                $vdreason = "add_campaign CAMPAIGN HAS NOT BEEN ADDED";
            }
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->campaign_id = $xml->params->campaign_id;
        $status = "SUCCESS";
        $reason = "Campaign added.";
        $vdreason = sprintf("add_campaign CAMPAIGN HAS BEEN ADDED - %s|%s",$xml->params->campaign_id,$xml['user']);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



##### Function to add a list.
} elseif ($xml['function'] == "add_list" and $xml['mode'] == "admin") {
    $status = '';
    $reason = '';

    # Authenticate connection first.
    $LOG = osdial_authenticate($xml['user'],$xml['pass']);
    if ($LOG['admin_api_access'] < 1 or $LOG['modify_lists'] < 1 or $LOG['user_level'] < 8) {
        $status = "ERROR";
        $reason = "Access Denied.";
        $vdreason = "add_list USER DOES NOT HAVE PERMISSION TO MODIFY LISTS IN THE SYSTEM";
    } else {
        if ($xml['debug'] > 0)
            $debug .= sprintf("AUTH: Success. user=%s, pass=%s, admin_api_access=%s, modify_lists=%s, user_level=%s\n", $xml['user'], $xml['pass'], $LOG['admin_api_access'], $LOG['modify_lists'], $LOG['user_level']);
    }
    # Validate Company Access.
    if ($status != "ERROR") {
        if ($config['settings']['enable_multicompany'] > 0) {
            $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",((OSDsubstr($xml['user'],0,3) * 1) - 100) ));
            if ($comp['api_access'] < 1) {
                $status = "ERROR";
                $reason = "Access Denied.";
                $vdreason = "add_list USER DOES NOT HAVE PERMISSION TO MODIFY LISTS IN THE SYSTEM";
            } else {
                if ($xml['debug'] > 0)
                    $debug .= sprintf("COMPANY AUTH: Success. company_id=%s, api_access=%s\n", $comp['id'], $comp['api_access']);
            }
        }
    }

    # Validate fields.
    if ($xml->params->cost) $xml->params->cost = OSDpreg_replace('/[^0-9\.]/',"",$xml->params->cost);

    # Check List is length
    if ($status != "ERROR") {
        if (OSDstrlen($xml->params->list_id) < 4) {
            $xml->params->list_id = date('YmdHi');
        }
    }

    # Check List does not exist
    if ($status != "ERROR") {
        $list = get_first_record($link, 'osdial_lists', 'count(*) as cnt', sprintf("list_id='%s'",mres($xml->params->list_id)) );
        if ($list['cnt']>0) {
            $status = "ERROR";
            $reason = "List ID already exists.";
            $vdreason = "add_list LIST ID EXISTS";
        }
    }

    # Check List name length
    if ($status != "ERROR") {
        if (OSDstrlen($xml->params->list_name) < 5) {
            $status = "ERROR";
            $reason = "List name too short.";
            $vdreason = "add_list INVALID LIST NAME";
        }
    }

    # check if campaign_id is allowed for user.
    if ($status != "ERROR") {
        if ($xml->params->campaign_id == '' or !OSDpreg_match('/:'.$xml->params->campaign_id.':/',$LOG['allowed_campaignsSTR'])) {
            $status = "ERROR";
            $reason = "Campaign ID not found.";
            $vdreason = "add_list INVALID CAMPAIGN ID";
        }
    }

    if ($status != "ERROR") {
        if (preg_match('/^[YyTt1]/',$xml->params->active)) {
            $xml->params->active = 'Y';
        } elseif (preg_match('/^[NnFf0]/',$xml->params->active)) {
            $xml->params->active = 'N';
        } else {
            $xml->params->active = 'N';
        }
        if (preg_match('/^[YyTt1]/',$xml->params->scrub_dnc)) {
            $xml->params->scrub_dnc = 'Y';
        } elseif (preg_match('/^[NnFf0]/',$xml->params->scrub_dnc)) {
            $xml->params->scrub_dnc = 'N';
        } else {
            $xml->params->scrub_dnc = 'N';
        }
        if ($xml['test'] < 1) {
            $ins =  sprintf("INSERT INTO osdial_lists SET list_changedate=NOW(),");
            $flds = ['list_id', 'list_name', 'campaign_id', 'active', 'list_description', 'scrub_dnc', 'cost',
                'web_form_address', 'web_form_address2', 'list_script'];

            foreach ($flds as $fld) {
                if ($xml->params->{$fld}) {
                    $ins .= sprintf("%s='%s',",$fld,mres($xml->params->{$fld}));
                }
            }
            $ins = OSDpreg_replace('/,$/',';',$ins);

            if ($xml['debug'] > 0) $debug .= "LIST ADD: " . $ins . "\n";
            $rslt=mysql_query($ins, $link);
            $insres = mysql_affected_rows($link);
            if ($insres < 1) {
                $status = "ERROR";
                $reason = "List insert failure.";
                $vdreason = "add_list LIST HAS NOT BEEN ADDED";
            }
        }
    }

    # Finally, setup the result set.
    if ($status != "ERROR") {
        $result->result['records'] = '1';
        $result->result->addChild("record");
        $result->result->record[0]['id'] = '0';
        $result->result->record[0]->list_id = $xml->params->list_id;
        $status = "SUCCESS";
        $reason = "List added.";
        $vdreason = sprintf("add_list LIST HAS BEEN ADDED - %s|%s",$xml->params->list_id,$xml['user']);
    }
    $result->status['code'] = $status;
    $result->status = $reason;



###### Function to add a new lead.
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

    # Last name length check.
    if (OSDstrlen($xml->params->last_name) < 2 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "Last Name must be 2 or more characters..";
        $vdreason = "add_lead INVALID LAST NAME";
    }

    # First name length check.
    if (OSDstrlen($xml->params->first_name) < 2 and $status != "ERROR") {
        $status = "ERROR";
        $reason = "First Name must be 2 or more characters..";
        $vdreason = "add_lead INVALID FIRST NAME";
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
            if (!OSDpreg_match('/:'.$list_campaign_id.':/',$LOG['allowed_campaignsSTR'])) {
                $status = "ERROR";
                $reason = "List ID not found.";
                $vdreason = "add_lead INVALID LIST ID";
            }
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
    if ($xml->params->duplicate_check == "" and $status != "ERROR") $xml->params->duplicate_check = "DUPLIST";
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
            $ins .= sprintf("post_date='%s',organization='%s',organization_title='%s';", mres($xml->params->post_date),mres($xml->params->organization),mres($xml->params->organization_title) );
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



##### Function to list the campaigns that the authenticated user has access too.
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
            $vdout = '';
            foreach ($campaigns as $campaign) {
                $result->result->addChild("record");
                $result->result->record[$camp_cnt]['id'] = $camp_cnt;
                $result->result->record[$camp_cnt]->campaign_id = $campaign['campaign_id'];
                $result->result->record[$camp_cnt]->campaign_name = $campaign['campaign_name'];
                $result->result->record[$camp_cnt]->active = $campaign['active'];
                $camp_outonly = '1';
                if (OSDstrlen($campaign['closer_campaigns']) > 5) $camp_outonly = '0';
                $result->result->record[$camp_cnt]->outbound_only = $camp_outonly;
                $vdout .= sprintf("\n%s | %s | %s | %s | %s",$camp_cnt,$campaign['campaign_id'],$campaign['campaign_name'],$campaign['active'],$camp_outonly);
                $camp_cnt++;
            }
            $result->result['records'] = $camp_cnt;
            $status = "SUCCESS";
            $result->status['code'] = $status;
            $result->status = "Listing " . $result->result['records'] . " campaigns.";
            $vdreason = "Listing " . $result->result['records'] . " campaigns.".$vdout;
        } else {
            $status = "ERROR";
            $reason = "Could not find any allowed campaigns.";
            $vdstatus = "ERROR";
            $vdreason = $reason;
        }
    }



##### Function to list the user_groups that the authenticated user has access too.
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
            $vdout = '';
            foreach ($usergroups as $usergroup) {
                $result->result->addChild("record");
                $result->result->record[$ug_cnt]['id'] = $ug_cnt;
                $result->result->record[$ug_cnt]->user_group = $usergroup['user_group'];
                $result->result->record[$ug_cnt]->group_name = $usergroup['group_name'];
                $vdout .= sprintf("\n%s | %s | %s",$ug_cnt,$usergroup['user_group'],$usergroup['group_name']);
                $ug_cnt++;
            }
            $result->result['records'] = $ug_cnt;
            $status = "SUCCESS";
            $result->status['code'] = $status;
            $result->status = "Listing " . $result->result['records'] . " usergroups.";
            $vdreason = "Listing " . $result->result['records'] . " usergroups.".$vdout;
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
