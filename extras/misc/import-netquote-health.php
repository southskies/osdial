<?php
# import-findmyleads.php - OSDial
#
# Copyright (C) 2011  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

require_once("include/dbconnect.php");
require_once("include/functions.php");

$product         = get_variable('Product');
$productcategory = get_variable('ProductCategory');
$userid          = get_variable('UserID');
$password        = get_variable('Password');
$leaddata        = get_variable('LeadData');
$leadid          = get_variable('LeadID');
$test            = get_variable('test');

$xml = new SimpleXMLElement($leaddata);

# Build the xml based on posted results
$xmlReq = new SimpleXMLElement('<api><params></params></api>');
$afs = $xmlReq->params->addChild('additional_fields');
# Auth 
$xmlReq['user']     = 'netquote';
$xmlReq['pass']     = 'n3tqu0t3';
$xmlReq['function'] = 'add_lead';
$xmlReq['mode']     = 'admin';
$xmlReq['vdcompat'] = '0';
$xmlReq['test'] = '0';
if (!empty($test)) $xmlReq['test'] = '1';
$xmlReq['debug']    = '0';
# Parameters / LeadData
$xmlReq->params->list_id          = '991001';
$xmlReq->params->vendor_lead_code = 'NETQUOTE';
$xmlReq->params->source_id        = 'WEB';
$xmlReq->params->phone_code       = '1';
$xmlReq->params->status           = 'NEW';
if ($leadid)                                              $xmlReq->params->external_key = $leadid;
if ($xml->QuoteRequest->ApplicationContact->FirstName)    $xmlReq->params->first_name   = $xml->QuoteRequest->ApplicationContact->FirstName;
if ($xml->QuoteRequest->ApplicationContact->LastName)     $xmlReq->params->last_name    = $xml->QuoteRequest->ApplicationContact->LastName;
if ($xml->QuoteRequest->ApplicationContact->EmailAddress) $xmlReq->params->email        = $xml->QuoteRequest->ApplicationContact->EmailAddress;
if ($xml->QuoteRequest->ApplicationContact->Address1)     $xmlReq->params->address1     = $xml->QuoteRequest->ApplicationContact->Address1;
if ($xml->QuoteRequest->ApplicationContact->Address2)     $xmlReq->params->address2     = $xml->QuoteRequest->ApplicationContact->Address2;
if ($xml->QuoteRequest->ApplicationContact->County)       $xmlReq->params->province     = $xml->QuoteRequest->ApplicationContact->County;
if ($xml->QuoteRequest->ApplicationContact->City)         $xmlReq->params->city         = $xml->QuoteRequest->ApplicationContact->City;
if ($xml->QuoteRequest->ApplicationContact->State)        $xmlReq->params->state        = $xml->QuoteRequest->ApplicationContact->State;
if ($xml->QuoteRequest->ApplicationContact->ZIPCode)      $xmlReq->params->postal_code  = $xml->QuoteRequest->ApplicationContact->ZIPCode;
foreach ($xml->QuoteRequest->ApplicationContact->PhoneNumbers->PhoneNumber as $number) {
    if ($number["PhoneNumberType"] == "Home") {
        $xmlReq->params->phone_number = $number->PhoneNumberValue;
    }
    if ($number["PhoneNumberType"] == "Work") {
        $xmlReq->params->alt_phone = $number->PhoneNumberValue;
    }
}
foreach ($xml->QuoteRequest->Persons->Person as $person) {
    if ($person["PersonID"] == "1") {
        if ($person->BirthDate) $xmlReq->params->date_of_birth = $person->BirthDate;
        if ($person['Gender'])  $xmlReq->params->gender        = OSDsubstr($person['Gender'],0,1);
    }
    if (OSDpreg_match('/^1$|^2$|^3$|^4$|^5$/',$person["PersonID"])) {
        if ($person->LastName or $person->FirstName) {
            $pname='';
            if ($person->LastName) {
                $pname .= $person->LastName;
            }
            if ($person->FirstName) {
                if ($person->LastName) {
                    $pname .= ', ';
                }
                $pname .= $person->FirstName;
            }
            $af = $afs->addChild('additional_field',$pname);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','NAME');
        }
        if ($person["RelationshipToApplicant"]) {
            $af = $afs->addChild('additional_field',$person["RelationshipToApplicant"]);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','RELATION');
        }
        if ($person["Gender"]) {
            $af = $afs->addChild('additional_field',$person["Gender"]);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','GENDER');
        }
        if ($person["MilitaryExperience"]) {
            $af = $afs->addChild('additional_field',$person["MilitaryExperience"]);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','MILITARY');
        }
        if ($person["USResident12Months"]) {
            $af = $afs->addChild('additional_field',$person["USResident12Months"]);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','USRESIDENT');
        }
        if ($person["MaritalStatus"]) {
            $af = $afs->addChild('additional_field',$person["MaritalStatus"]);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','MARITAL');
        }
        if ($person->BirthDate) {
            $af = $afs->addChild('additional_field',$person->BirthDate);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','BIRTHDATE');
        }
        if ($person->SocialSecurityNumber) {
            $af = $afs->addChild('additional_field',$person->SocialSecurityNumber);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','SSN');
        }
        if ($person->Occupation) {
            $af = $afs->addChild('additional_field',$person->Occupation['OccupationName'].', '.$person->Occupation->YearsInField.' year(s)');
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','OCCUPATION');
        }
        if ($person->EducationProfile->Education) {
            $af = $afs->addChild('additional_field',$person->EducationProfile->Education['HighestDegree']);
            $af->addAttribute('form','PERSON'.$person['PersonID']);
            $af->addAttribute('field','EDUCATION');

            #if ($person->EducationProfile->Education['GoodStudentDiscount']) {
            #    $af = $afs->addChild('additional_field',$person->EducationProfile->Education['GoodStudentDiscount']);
            #    $af->addAttribute('form','PERSON'.$person['PersonID']);
            #    $af->addAttribute('field','STUDISCOUNT');
            #}
        }
    }
}

foreach ($xml->QuoteRequest->HealthInsuranceQuoteRequest->HealthPolicyMembers->HealthPolicyMember as $member) {
    if (OSDpreg_match('/^1$|^2$|^3$|^4$|^5$/',$member["PersonID"])) {
        if ($member->MedicalProfile->HeightFeet or $member->MedicalProfile->HeightInches) {
            $pheight='';
            if ($member->MedicalProfile->HeightFeet) {
                $pheight .= $member->MedicalProfile->HeightFeet . "'";
            }
            if ($member->MedicalProfile->HeightInches) {
                if ($member->MedicalProfile->HeightFeet) {
                    $pheight .= ' ';
                }
                $pheight .= $member->MedicalProfile->HeightInches . '"';
            }
            $af = $afs->addChild('additional_field',$pheight);
            $af->addAttribute('form','PERSONMED'.$member['PersonID']);
            $af->addAttribute('field','HEIGHT');
        }

        if ($member->MedicalProfile->WeightPounds) {
            $af = $afs->addChild('additional_field',$member->MedicalProfile->WeightPounds);
            $af->addAttribute('form','PERSONMED'.$member['PersonID']);
            $af->addAttribute('field','WEIGHT');
        }

        if ($member->MedicalProfile['ExpectantMotherOrFather']) {
            $af = $afs->addChild('additional_field',$member->MedicalProfile['ExpectantMotherOrFather']);
            $af->addAttribute('form','PERSONMED'.$member['PersonID']);
            $af->addAttribute('field','PREGNANT');
        }

        if ($member->MedicalProfile['TobaccoUse12Months']) {
            $af = $afs->addChild('additional_field',$member->MedicalProfile['TobaccoUse12Months']);
            $af->addAttribute('form','PERSONMED'.$member['PersonID']);
            $af->addAttribute('field','TOBACCOUSER');
        }

        $history='';
        if ($member->MedicalProfile->Disorders) {
            if ($member->MedicalProfile->Disorders['Medication'] and $member->MedicalProfile->Disorders['Medication'] == 'Yes') {
                $history .= 'Medication,';
            }
            if ($member->MedicalProfile->Disorders['HighBloodPressure'] and $member->MedicalProfile->Disorders['HighBloodPressure'] == 'Yes') {
                $history .= 'HighBloodPressure,';
            }
            if ($member->MedicalProfile->Disorders['AlcoholDrugAddiction'] and $member->MedicalProfile->Disorders['AlcoholDrugAddiction'] == 'Yes') {
                $history .= 'AlcoholDrugAddiction,';
            }
            if ($member->MedicalProfile->Disorders['Alzheimers'] and $member->MedicalProfile->Disorders['Alzheimers'] == 'Yes') {
                $history .= 'Alzheimers,';
            }
            if ($member->MedicalProfile->Disorders['Asthma'] and $member->MedicalProfile->Disorders['Asthma'] == 'Yes') {
                $history .= 'Asthma,';
            }
            if ($member->MedicalProfile->Disorders['Cancer'] and $member->MedicalProfile->Disorders['Cancer'] == 'Yes') {
                $history .= 'Cancer,';
            }
            if ($member->MedicalProfile->Disorders['HighCholesterol'] and $member->MedicalProfile->Disorders['HighCholesterol'] == 'Yes') {
                $history .= 'HighCholesterol,';
            }
            if ($member->MedicalProfile->Disorders['Depression'] == 'Yes') {
                $history .= 'Depression,';
            }
            if ($member->MedicalProfile->Disorders['Diabetes'] and $member->MedicalProfile->Disorders['Diabetes'] == 'Yes') {
                $history .= 'Diabetes,';
            }
            if ($member->MedicalProfile->Disorders['HeartDisease'] and $member->MedicalProfile->Disorders['HeartDisease'] == 'Yes') {
                $history .= 'HeartDisease,';
            }
            if ($member->MedicalProfile->Disorders['KidneyDisease'] and $member->MedicalProfile->Disorders['KidneyDisease'] == 'Yes') {
                $history .= 'KidneyDisease,';
            }
            if ($member->MedicalProfile->Disorders['LiverDisease'] and $member->MedicalProfile->Disorders['LiverDisease'] == 'Yes') {
                $history .= 'LiverDisease,';
            }
            if ($member->MedicalProfile->Disorders['MentalIllness'] and $member->MedicalProfile->Disorders['MentalIllness'] == 'Yes') {
                $history .= 'MentalIllness,';
            }
            if ($member->MedicalProfile->Disorders['PulmonaryDisease'] and $member->MedicalProfile->Disorders['PulmonaryDisease'] == 'Yes') {
                $history .= 'PulmonaryDisease,';
            }
            if ($member->MedicalProfile->Disorders['Stroke'] and $member->MedicalProfile->Disorders['Stroke'] == 'Yes') {
                $history .= 'Stroke,';
            }
            if ($member->MedicalProfile->Disorders['Ulcers'] and $member->MedicalProfile->Disorders['Ulcers'] == 'Yes') {
                $history .= 'Ulcers,';
            }
            if ($member->MedicalProfile->Disorders['VascularDisease'] and $member->MedicalProfile->Disorders['VascularDisease'] == 'Yes') {
                $history .= 'VascularDisease,';
            }
            if ($member->MedicalProfile->Disorders['OtherDisorder'] and $member->MedicalProfile->Disorders['OtherDisorder'] == 'Yes') {
                $history .= 'OtherDisorder,';
            }
            if ($member->MedicalProfile->Disorders['FamilyHistoryHeartDisease'] and $member->MedicalProfile->Disorders['FamilyHistoryHeartDisease'] == 'Yes') {
                $history .= 'FamilyHistoryHeartDisease,';
            }
            if ($member->MedicalProfile->Disorders['FamilyHistoryCancer'] and $member->MedicalProfile->Disorders['FamilyHistoryCancer'] == 'Yes') {
                $history .= 'FamilyHistoryCancer,';
            }
            if ($member->MedicalProfile->Disorders['AIDSHIV'] and $member->MedicalProfile->Disorders['AIDSHIV'] == 'Yes') {
                $history .= 'AIDSHIV,';
            }
        }
        $history = rtrim($history,',');
        $af = $afs->addChild('additional_field',$history);
        $af->addAttribute('form','PERSONMED'.$member['PersonID']);
        $af->addAttribute('field','HISTORY');
    }
}

if ($xml->QuoteRequest->ApplicationContact["ReceiveNewsletter"]) {
    $af = $afs->addChild('additional_field',$xml->QuoteRequest->ApplicationContact["ReceiveNewsletter"]);
    $af->addAttribute('form','GENERAL');
    $af->addAttribute('field','NEWSLETTER');
}

if ($xml->QuoteRequest->ApplicationContact->ResidencyProfile->CurrentResidence) {
    $af = $afs->addChild('additional_field',$xml->QuoteRequest->ApplicationContact->ResidencyProfile->CurrentResidence["ResidenceStatus"]);
    $af->addAttribute('form','GENERAL');
    $af->addAttribute('field','RESSTATUS');

    $af = $afs->addChild('additional_field',$xml->QuoteRequest->ApplicationContact->ResidencyProfile->CurrentResidence->OccupancyDate);
    $af->addAttribute('form','GENERAL');
    $af->addAttribute('field','RESDATE');
}

if ($xml->QuoteRequest->HealthInsuranceQuoteRequest->InsuranceProfile->RequestedPolicy->StartDate) {
    $af = $afs->addChild('additional_field',$xml->QuoteRequest->HealthInsuranceQuoteRequest->InsuranceProfile->RequestedPolicy->StartDate);
    $af->addAttribute('form','GENERAL');
    $af->addAttribute('field','STARTDATE');
}

if ($xml->QuoteRequest->HealthInsuranceQuoteRequest->InsuranceProfile->RequestedPolicy->RequestedCoverages) {
    $pcomment='';
    foreach ($xml->QuoteRequest->HealthInsuranceQuoteRequest->InsuranceProfile->RequestedPolicy->RequestedCoverages->CoverageComment as $comment) {
        $pcomment .= $comment.',';
    }
    $pcomment = rtrim($pcomment,',');
    $af = $afs->addChild('additional_field',$pcomment);
    $af->addAttribute('form','GENERAL');
    $af->addAttribute('field','COMMENT');

    $pcoverage='';
    foreach ($xml->QuoteRequest->HealthInsuranceQuoteRequest->InsuranceProfile->RequestedPolicy->RequestedCoverages->RequestedCoverage as $coverage) {
        $pcoverage .= $coverage['CoverageType'].',';
    }
    $pcoverage = rtrim($pcoverage,',');
    $af = $afs->addChild('additional_field',$pcoverage);
    $af->addAttribute('form','GENERAL');
    $af->addAttribute('field','COVERAGE');
}

# Parameters / DataControl
$xmlReq->params->dnc_check         = 'N';
$xmlReq->params->duplicate_check   = 'LIST';
$xmlReq->params->gmt_lookup_method = 'AREACODE';
$xmlReq->params->add_to_hopper     = 'Y';
$xmlReq->params->hopper_priority   = '50';
$xmlReq->params->hopper_local_call_time_check    = 'N';
$xmlReq->params->hopper_campaign_call_time_check = 'N';


#print_r($xmlReq);
#print_r($xml);
#echo $leaddata;
#echo prettyXML($xmlReq->asXML());


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1/admin/api.php');
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('xml' => $xmlReq->asXML()));
$result = curl_exec($ch);
curl_close($ch);

$xmlRes = new SimpleXMLElement($result);
#echo prettyXML($xmlRes->asXML());

$xmlAck = new SimpleXMLElement('<NetQuoteResponse></NetQuoteResponse>');
$xmlAck->addAttribute('Date',date('Y-m-d'));

if ($xmlRes->status['code'] == 'SUCCESS') {
    $rescode = 'Accepted';
    $xmlAck->addChild('ResponseCode','Accepted');
    $xmlAck->addAttribute('Identifier',$xmlRes->result->record->lead_id);
} else {
    $ers = $xmlAck->addChild('Errors');
    $er = $ers->addChild('Error');
    if (OSDpreg_match('/dnc|hopper|duplicate/i',$xmlRes->status)) {
        $xmlAck->addChild('ResponseCode','Rejected');
        if (OSDpreg_match('/duplicate/i',$xmlRes->status)) $er->addAttribute('Code','-32');
        if (OSDpreg_match('/dnc/i',$xmlRes->status)) $er->addAttribute('Code','-1024');
        if (OSDpreg_match('/hopper/i',$xmlRes->status)) $er->addAttribute('Code','-2048');
    } else {
        $xmlAck->addChild('ResponseCode','Failed');
        if (OSDpreg_match('/access denied|list id|lists in system|insertion failure/i',$xmlRes->status)) {
            $er->addAttribute('Code','-1');
        } else {
            $er->addAttribute('Code','-64');
        }
    }
    $er->addChild('Description',OSDpreg_replace('/\n/','',$xmlRes->status));
} 

echo $xmlAck->asXML();

exit(0);
?>
