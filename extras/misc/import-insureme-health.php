<?php
# import-insureme-health.php - OSDial
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
# This program provides a method of importing live data from the InsureMe service.
# Currently this script only handles Health-based applicants.


require("include/dbconnect.php");
require("include/functions.php");

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
    if (strlen($DBgmt)>0)    {
        $server_gmt = $DBgmt;
    }
    if ($isdst) $server_gmt++;
} else {
    $server_gmt = date("O");
    $server_gmt = eregi_replace("\+","",$server_gmt);
    $server_gmt = (($server_gmt + 0) / 100);
}
$local_gmt = $server_gmt;


$file = get_variable("file");
$xmldata = get_variable("xmldata");
$test = get_variable("test");
$debug = get_variable("debug");

$lead['list_id'] = "2001";
$lead['source_id'] = "INSME";
$lead['phone_code'] = "1";
$lead['status'] = "NEW";

if ($file == "" and $xmldata == "") {
    $test=1;
    #$debug=1;
    $file=$argv[1];
}

if ($test == 1) {
    $lead['list_id'] = "2000";
    $test = 0;
}



$data = "";
if ($file != "") {
    $data = file_get_contents($file);
} elseif ($xmldata != "") {
    $data = $xmldata;
} else {
    reject(0,"failed to get XML");
}

$xml = new SimpleXMLElement($data);

$invalid = '([:alnum:]|[:space:]|\.)';

$badrep = "(aaa|bbb|ccc|ddd|eee|fff|ggg|hhh|iii|jjj|kkk|lll|mmm|nnn|ooo|ppp|qqq|rrr|sss|ttt|uuu|vvv|www|xxx|yyy|zzz)";
$badname = "(fuck|shit|cock|asshole|dickweed|cunt|pussy|bastard|bitch|twat|prick|titty|boob|jiz|mouse|goofy|duck|sponge)";
$badnumrep = "(0000000|1111111|2222222|3333333|4444444|5555555|6666666|7777777|8888888|9999999)";

$lid = $xml['id'];
$lead['vendor_lead_code'] = $xml->agent->agentid . ':' . $xml['id'];
$lead['external_key'] = $xml['id'];
$lead['user'] = "";
$lead['custom1'] = "";
$lead['custom2'] = $xml->agent->profile;

# Not part of XML spec.
## Reject if leaddistributioncap <= 0
#if ($xml->distributiondirectives->distributiondirective['leaddistributioncap'] <= 0) {
#    reject($lid,"leaddistributioncap is 0 or less");
#}

# Check for health type applicant.
if ($xml['health'] != "True") {
    reject($lid,"not health application");
}


$lead['title'] = "";
$lead['last_name'] = $xml->contactinfo->name->lastname; 
$lead['first_name'] = $xml->contactinfo->name->firstname; 
$lead['middle_initial'] = $xml->contactinfo->name->middlename; 

#$homestatus = $xml->contactinfo->contactaddress["status"];
$lead['address1'] = $xml->contactinfo->contactaddress->contactline[0];
$lead['address2'] = $xml->contactinfo->contactaddress->contactline[1];
$lead['city'] = $xml->contactinfo->contactaddress->contactcity;
$lead['state'] = $xml->contactinfo->contactaddress->contactstate;
$lead['postal_code'] = $xml->contactinfo->contactaddress->contactzipcode;
$lead['province'] = $xml->contactinfo->contactaddress->contactcounty;
$lead['country_code'] = "";

# Check for blank first/last name
if ($lead['last_name'] == "") reject($lid,"no lastname");
if ($lead['first_name'] == "") reject($lid,"no firstname");

# Check for asdf in any name.
if (eregi("asdf",$lead['last_name'])) reject($lid,"lastname has asdf");
if (eregi("asdf",$lead['first_name'])) reject($lid,"firstname has asdf");
if (eregi("asdf",$lead['middle_initial'])) reject($lid,"middlename has asdf");

## Check for invlid characters
#if (!eregi($invalid,$lead['last_name'])) reject($lid,"lastname has invalid characters");
#if (!eregi($invalid,$lead['first_name'])) reject($lid,"firstname has invalid characters");
#if (!eregi($invalid,$lead['address1'])) reject($lid,"contactline1 has invalid characters");
##if (!eregi($invalid,$lead['city'])) reject($lid,"contactcity has invalid characters");
##if (!eregi($invalid,$lead['state'])) reject($lid,"contactstate has invalid characters: " . $lead['state']);
## Address2/Middle name can be blank.
#if (!$lead['address2'] == "" and eregi($invalid,$lead['address2'])) reject($lid,"contactline2 has invalid characters");
#if (!$lead['middle_initial'] == "" and !eregi($invalid,$lead['middle_initial'])) reject($lid,"middlename has invalid characters");

# Check for 3 or more repeating characters.
if (eregi($badrep,$lead['last_name'])) reject($lid,"lastname has 3 or more repeating characters");
if (eregi($badrep,$lead['first_name'])) reject($lid,"firstname has 3 or more repeating characters");
if (eregi($badrep,$lead['middle_initial'])) reject($lid,"middlename has 3 or more repeating characters");

# Check for profanity.
if (eregi($badname,$lead['last_name'])) reject($lid,"lastname is bogus, fictitious or contains profanity");
if (eregi($badname,$lead['first_name'])) reject($lid,"firstname is bogus, fictitious or contains profanity");
if (eregi($badname,$lead['middle_initial'])) reject($lid,"middlename is bogus, fictitious or contains profanity");

$lead['phone_number'] = "";
$lead['alt_phone'] = "";
$lead['address3'] = "";
foreach ($xml->contactinfo->telephone->number as $number) {
    $fnumber = ereg_replace("[^0-9]","",$number);
    if (eregi($badnumrep,$fnumber)) reject($lid,"telephone-number has 7 repeating digits");
    if ($number["type"] == "Home") {
        if (strlen($fnumber) != 10) reject($lid,"telephone-number-home is not 10 digits");
        $lead['phone_number'] = $fnumber;
    } elseif ($number["type"] == "Mobile") {
        if (strlen($fnumber) != 10) reject($lid,"telephone-number-mobile is not 10 digits");
        $lead['alt_phone'] = $fnumber;
    } elseif ($number["type"] == "Work") {
        if (strlen($fnumber) < 10 or strlen($fnumber) > 16) reject($lid,"telephone-number-work is not 10 digits or greater than 16 digits");
        $lead['address3'] = substr($fnumber,0,10);
        $lead['custom1'] = "Extension: " . substr($fnumber,10);
    }
}

if ($lead['phone_number'] == "") {
    if ($lead['alt_phone'] != "") {
        $lead['phone_number'] = $lead['alt_phone'];
        $lead['alt_phone'] = $lead['address3'];
        $lead['address3'] = '';
    } elseif ($lead['address3'] != "") {
        $lead['phone_number'] = $lead['address3'];
        $lead['alt_phone'] = '';
        $lead['address3'] = '';
    }
}

$lead['email'] = $xml->contactinfo->emails->email[0];
$lead['comments'] = $xml->contactinfo->info['comment'];


# Get primary
$selfcount = 0;
$spousecount = 0;
$childcount = 0;
$othercount = 0;
$lext['PRIMARY_MEDS'] = '';
$lext['PRIMARY_CONDS'] = '';
foreach ($xml->persons->person as $person) {
    if ($person['relationshiptoprimary'] == "Self" and $person['health'] == "True") {
        $lead['gender'] = substr($person->gender,0,1);
        $lead['date_of_birth'] = ereg_replace("T"," ",$person->birthdate);
        if ($person->height['feet'] > 0) {
            $lext['PRIMARY_HEIGHT'] = $person->height['feet'] . "'";
            if ($person->height['inches'] > 0) {
                $lext['PRIMARY_HEIGHT'] .= $person->height['inches'] . "\"";
            }
        }
        $lext['PRIMARY_WEIGHT'] = $person->weight['lbs'];
        $lext['PRIMART_MARITAL'] = $person->maritalstatus;
        $lext['PRIMARY_OCCUPATION'] = $person->occupation;
        $lext['PRIMARY_SELFEMP'] = $person->selfemployed['response'];
        $lext['PRIMARY_RESIDENT'] = $person->usresident['response'];

        $lext['PRIMARYMED_INSCOMP'] = $person->insurances->insurance->currentcompany;

        $lext['PRIMARYMED_SMOKER'] = $person->smoker['response'];
        $lext['PRIMARYMED_PHYSTREAT']= $person->healthhistory->physiciantreatment['response'];
        $lext['PRIMARYMED_HOSPITAL'] = $person->healthhistory->hospitalized['response'];
        $lext['PRIMARYMED_COVDENIED'] = $person->healthhistory->deniedcoverage['response'];
        $lext['PRIMARYMED_EXSTDCTR'] = $person->healthhistory->existingdoctor['response'];
        $lext['PRIMARYMED_PREGNANT'] = $person->healthhistory->pregnant['response'];
        $lext['PRIMARYMED_DEPRESSION'] = $person->healthhistory->depression['response'];

        foreach ($person->healthhistory->medications->medication as $meds) {
            $lext['PRIMARYMED_MEDS'] .= $meds . ", ";
        }
        $lext['PRIMARYMED_MEDS'] = rtrim($lext['PRIMARYMED_MEDS'], ', ');
        foreach ($person->healthhistory->conditions->condition as $conds) {
            $lext['PRIMARYMED_CONDS'] .= $conds['type'];
            if ($conds != "")
                $lext['PRIMARYMED_CONDS'] .= ":" . $conds;
            $lext['PRIMARYMED_CONDS'] .= ", ";
        }
        $lext['PRIMARYMED_CONDS'] = rtrim($lext['PRIMARYMED_CONDS'], ', ');
        $selfcount++;

    } elseif ($person['relationshiptoprimary'] == "Spouse" and $person['health'] == "True") {
        # Get spouse
        $lext['SPOUSE_NAME'] = $person->firstname;
        if ($person->lastname != "")
            $lext['SPOUSE_NAME'] .= " " . $person->lastname;
        $lext['SPOUSE_GENDER'] = substr($person->gender,0,1);
        $lext['SPOUSE_BIRTHDATE'] = ereg_replace("T.*","",$person->birthdate);
        $lext['SPOUSE_OCCUPATION'] = $person->occupation;
        if ($person->height['feet'] > 0) {
            $lext['SPOUSE_HEIGHT'] = $person->height['feet'] . "'";
            if ($person->height['inches'] > 0) {
                $lext['SPOUSE_HEIGHT'] .= $person->height['inches'] . "\"";
            }
        }
        $lext['SPOUSE_WEIGHT'] = $person->weight['lbs'];

        $lext['SPOUSEMED_SMOKER'] = $person->smoker['response'];
        $lext['SPOUSEMED_PHYSTREAT']= $person->healthhistory->physiciantreatment['response'];
        $lext['SPOUSEMED_HOSPITAL'] = $person->healthhistory->hospitalized['response'];
        $lext['SPOUSEMED_COVDENIED'] = $person->healthhistory->deniedcoverage['response'];
        $lext['SPOUSEMED_EXSTDCTR'] = $person->healthhistory->existingdoctor['response'];
        $lext['SPOUSEMED_PREGNANT'] = $person->healthhistory->pregnant['response'];
        $lext['SPOUSEMED_DEPRESSION'] = $person->healthhistory->depression['response'];

        foreach ($person->healthhistory->medications->medication as $meds) {
            $lext['SPOUSEMED_MEDS'] .= $meds . ", ";
        }
        $lext['SPOUSEMED_MEDS'] = rtrim($lext['SPOUSEMED_MEDS'], ', ');
        foreach ($person->healthhistory->conditions->condition as $conds) {
            $lext['SPOUSEMED_CONDS'] .= $conds['type'];
            if ($conds != "")
                $lext['SPOUSEMED_CONDS'] .= ":" . $conds;
            $lext['SPOUSEMED_CONDS'] .= ", ";
        }
        $lext['SPOUSEMED_CONDS'] = rtrim($lext['SPOUSEMED_CONDS'], ', ');
        $spousecount++;

    } elseif ($person['relationshiptoprimary'] == "Child" and $person['health'] == "True") {
        # Get Children.
        $childcount++;
        $lext['CHILD' . $childcount . '_NAME'] = $person->firstname;
        if ($person->lastname != "")
            $lext['CHILD' . $childcount . '_NAME'] = $person->lastname;
        $lext['CHILD' . $childcount . '_GENDER'] = substr($person->gender,0,1);
        $lext['CHILD' . $childcount . '_BIRTHDATE'] = ereg_replace("T.*","",$person->birthdate);
        if ($person->height['feet'] > 0) {
            $lext['CHILD' . $childcount . '_HEIGHT'] = $person->height['feet'] . "'";
            if ($person->height['inches'] > 0) {
                $lext['CHILD' . $childcount . '_HEIGHT'] .= $person->height['inches'] . "\"";
            }
        }
        $lext['CHILD' . $childcount . '_WEIGHT'] = $person->weight['lbs'];
        $lext['CHILD' . $childcount . '_SMOKER'] = $person->smoker['response'];
        $lext['CHILD' . $childcount . '_PHYSTREAT']= $person->healthhistory->physiciantreatment['response'];
        $lext['CHILD' . $childcount . '_HOSPITAL'] = $person->healthhistory->hospitalized['response'];
        $lext['CHILD' . $childcount . '_COVDENIED'] = $person->healthhistory->deniedcoverage['response'];
        $lext['CHILD' . $childcount . '_EXSTDCTR'] = $person->healthhistory->existingdoctor['response'];
        $lext['CHILD' . $childcount . '_PREGNANT'] = $person->healthhistory->pregnant['response'];
        $lext['CHILD' . $childcount . '_DEPRESSION'] = $person->healthhistory->depression['response'];

        foreach ($person->healthhistory->medications->medication as $meds) {
            $lext['CHILD' . $childcount . '_MEDS'] .= $meds . ", ";
        }
        $lext['CHILD' . $childcount . '_MEDS'] = rtrim($lext['CHILD' . $childcount . '_MEDS'], ', ');
        foreach ($person->healthhistory->conditions->condition as $conds) {
            $lext['CHILD' . $childcount . '_CONDS'] .= $conds['type'];
            if ($conds != "")
                $lext['CHILD' . $childcount . '_CONDS'] .= ":" . $conds;
            $lext['CHILD' . $childcount . '_CONDS'] .= ", ";
        }
        $lext['CHILD' . $childcount . '_CONDS'] = rtrim($lext['CHILD' . $childcount . '_CONDS'], ', ');
    }
}
if ($selfcount == 0) reject($lid, "no persons-person-relationshiptoprimary=Self");
if ($selfcount > 1) reject($lid, "more than 1 persons-person-relationshiptoprimary=Self");
if ($spousecount > 1) reject($lid, "more than 1 persons-person-relationshiptoprimary=Spouse");
if ($lext['PRIMARY_MARITAL'] == "Single" and $spousecount == 1) reject($lid, "more than 1 persons-person-relationshiptoprimary=Spouse and not married");


# Check for lead.
$check_lead = get_first_record($link, 'osdial_list', '*', "list_id='" . mysql_real_escape_string($lead['list_id']) . "' AND phone_number='" . mysql_real_escape_string($lead['phone_number']) . "'");
if ($test != 1 and $check_lead['lead_id'] > 0) reject($lid,"lead already exists in system: " . $check_lead['lead_id']);

# Get cost
if ($xml->agent->cost > 0) {
    $lead['cost'] = $xml->agent->cost;
} else {
    $lcost = get_first_record($link, 'osdial_lists', '*', "list_id='" . mysql_real_escape_string($lead['list_id']) . "'");
    $lead['cost'] = $lcost['cost'];
}

# Get gmt
$postalgmt = "";
if ($lead['postal_code'] != "") $postalgmt = "POSTAL";
$lead['gmt_offset_now'] = lookup_gmt($lead['phone_code'],substr($lead['phone_number'],0,3),$lead['state'],$local_gmt,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$lead['postal_code']);

# Insert lead.
$stmt = "INSERT INTO osdial_list SET entry_date=NOW(),";
foreach ($lead as $k=>$v) {
    $stmt .= $k . "='" . mysql_real_escape_string($v) . "',";
}
$stmt = rtrim($stmt, ',');
$stmt .= ';';
if ($test != 1) $rslt=mysql_query($stmt, $link);

# Get lead id
$new_lead = get_first_record($link, 'osdial_list', '*', "list_id='" . mysql_real_escape_string($lead['list_id']) . "' AND phone_number='" . mysql_real_escape_string($lead['phone_number']) . "'");
if ($test != 1 and $new_lead['lead_id'] < 1) reject($lid,"lead could not be created: " . $lead['list_id'] . " " . $lead['phone_number']);

# Insert lext data.
$fstmt = "SELECT field.id,form.name,field.name FROM osdial_campaign_forms AS form JOIN osdial_campaign_fields AS field ON (form.id=field.form_id)";
$frslt=mysql_query($fstmt, $link);
while ($frow = mysql_fetch_array($frslt, MYSQL_BOTH)) {
    $fmap[$frow[1] . '_' . $frow[2]] = $frow[0];
}
foreach ($lext as $k=>$v) {
    if ($fmap[$k] != "") {
        $fstmt = "INSERT INTO osdial_list_fields SET lead_id='" . $new_lead['lead_id'] ."',field_id='" . $fmap[$k] . "',value='" . mysql_real_escape_string($v) . "';";
        if ($test != 1) $rslt=mysql_query($fstmt, $link);
    }
}


# Export and finish
echo "<?xml version='1.0' standalone='yes'?>\n";

if ($debug > 0) {
    print "<debug>\n";
    print "    <stmt>$stmt</stmt>\n";
    foreach ($lead as $k=>$v) {
        echo "    <" . $k . ">" . $v . "</" . $k . ">\n";
    }
    foreach ($lext as $k=>$v) {
        echo "    <" . $k . ">" . $v . "</" . $k . ">\n";
    }
    print "</debug>\n";
}

echo "<result leadid=\"$lid\" status=\"accepted\"/>\n";
exit;


function reject($leadid, $result) {
    echo "<?xml version='1.0' standalone='yes'?>\n";
    echo "<result leadid=\"$leadid\" reason=\"$result\" status=\"rejected\"/>\n";
    exit;
}

?>
