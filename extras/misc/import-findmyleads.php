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

# This script expects 5 variables to be supplied via HTTP POST/GET.
#	leadid
#	firstname
#	lastname
#	phonenumber
#	url

require("include/dbconnect.php");
require("include/functions.php");

$leadid       = get_variable('leadid');
$firstname    = get_variable('firstname');
$lastname     = get_variable('lastname');
$phonenumber  = get_variable('phonenumber');
$url          = get_variable('url');

# Make some dummy data if nothing was passed to us.
if ($leadid=='' or $phonenumber=='') {
	$leadid      = '9876543210';
	$phonenumber = '3215551212';
	$firstname   = 'John';
	$lastname    = 'Doe';
	$url         = 'http://www.google.com/';
}

# Build the xml based on posted results
$xmlReq = new SimpleXMLElement('<api><params></params></api>');
# Auth 
$xmlReq['user']     = '8553463695';
$xmlReq['pass']     = 'l38ds4m3';
$xmlReq['function'] = 'add_lead';
$xmlReq['mode']     = 'admin';
$xmlReq['vdcompat'] = '0';
$xmlReq['test']     = '0';
$xmlReq['debug']    = '0';
# Parameters / LeadData
$xmlReq->params->list_id          = '8553463695';
$xmlReq->params->vendor_lead_code = 'FML';
$xmlReq->params->source_id        = 'WEB';
$xmlReq->params->phone_code       = '1';
$xmlReq->params->status           = 'NEW';
$xmlReq->params->external_key     = $leadid;
$xmlReq->params->first_name       = $firstname;
$xmlReq->params->last_name        = $lastname;
$xmlReq->params->phone_number     = $phonenumber;
$xmlReq->params->custom1          = $url;
# Parameters / DataControl
$xmlReq->params->dnc_check         = 'Y';
$xmlReq->params->duplicate_check   = 'LIST';
$xmlReq->params->gmt_lookup_method = 'AREACODE';
$xmlReq->params->add_to_hopper     = 'Y';
$xmlReq->params->hopper_priority   = '50';
$xmlReq->params->hopper_local_call_time_check    = 'N';
$xmlReq->params->hopper_campaign_call_time_check = 'N';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1/admin/api.php');
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('xml' => $xmlReq->asXML()));
$result = curl_exec($ch);
curl_close($ch);

$xmlRes = new SimpleXMLElement($result);
echo $xmlRes->status;

exit(0);
?>
