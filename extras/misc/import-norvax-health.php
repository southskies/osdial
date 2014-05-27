<?php
# import-norvax-health.php - OSDial
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

$fielddef = array();
$fielddef[] = array('name'=>'lead_id','type'=>'int','mappings'=>array('external_key'));
$fielddef[] = array('name'=>'timestamp','type'=>'datetime','mappings'=>array('post_date'));
$fielddef[] = array('name'=>'name','type'=>'name','mappings'=>array());
#$fielddef[] = array('name'=>'first_name','type'=>'string','mappings'=>array('first_name','PERSON1_NAME'));
#$fielddef[] = array('name'=>'last_name','type'=>'string','mappings'=>array('last_name','PERSON1_NAME'));
$fielddef[] = array('name'=>'email','type'=>'string','mappings'=>array('email'));
$fielddef[] = array('name'=>'day_phone','type'=>'phone','mappings'=>array('phone_number'));
$fielddef[] = array('name'=>'evening_phone','type'=>'phone','mappings'=>array('alt_phone'));
$fielddef[] = array('name'=>'fax','type'=>'phone','mappings'=>array('address3'));
$fielddef[] = array('name'=>'street1','type'=>'string','mappings'=>array('address1'));
$fielddef[] = array('name'=>'street2','type'=>'string','mappings'=>array('address2'));
$fielddef[] = array('name'=>'city','type'=>'string','mappings'=>array('city'));
$fielddef[] = array('name'=>'state','type'=>'string','mappings'=>array('state'));
$fielddef[] = array('name'=>'zipcode','type'=>'string','mappings'=>array('postal_code'));
$fielddef[] = array('name'=>'contact_time','type'=>'string','mappings'=>array());
$fielddef[] = array('name'=>'conditions','type'=>'string','mappings'=>array());
$fielddef[] = array('name'=>'medications','type'=>'string','mappings'=>array());
$fielddef[] = array('name'=>'currently_insured','type'=>'boolean','mappings'=>array('GENERAL_COVERAGE'));
$fielddef[] = array('name'=>'ip_address','type'=>'string','mappings'=>array());

$personid=1;
foreach (array('applicant','spouse') as $adult_type) {
	if ($personid==1) {
		$fielddef[] = array('name'=>$adult_type,'type'=>'dob','personid'=>$personid,'mappings'=>array('date_of_birth','PERSON'.$personid.'_BIRTHDATE'));
		$fielddef[] = array('name'=>$adult_type.'_gender','type'=>'gender','personid'=>$personid,'mappings'=>array('gender','PERSON'.$personid.'_GENDER'));
	} else {
		$fielddef[] = array('name'=>$adult_type,'type'=>'dob','personid'=>$personid,'mappings'=>array('PERSON'.$personid.'_BIRTHDATE'));
		$fielddef[] = array('name'=>$adult_type.'_gender','type'=>'gender','personid'=>$personid,'mappings'=>array('PERSON'.$personid.'_GENDER'));
	}
	$fielddef[] = array('name'=>$adult_type.'_height','type'=>'height','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_HEIGHT'));
	#$fielddef[] = array('name'=>$adult_type.'_heightFT','type'=>'int','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_HEIGHT'));
	#$fielddef[] = array('name'=>$adult_type.'_heightIN','type'=>'int','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_HEIGHT'));
	$fielddef[] = array('name'=>$adult_type.'_smoker','type'=>'boolean','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_TOBACCOUSER'));
	$fielddef[] = array('name'=>$adult_type.'_weight','type'=>'int','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_WEIGHT'));
	$personid++;
}

foreach (array('child_1','child_2','child_3','child_4','child_5','child_6') as $child_type) {
	$fielddef[] = array('name'=>$child_type,'type'=>'dob','personid'=>$personid,'mappings'=>array('PERSON'.$personid.'_BIRTHDATE'));
	#$fielddef[] = array('name'=>$child_type.'_age','type'=>'int','personid'=>$personid,'mappings'=>array('PERSON'.$personid.'_AGE'));
	$fielddef[] = array('name'=>$child_type.'_gender','type'=>'gender','personid'=>$personid,'mappings'=>array('PERSON'.$personid.'_GENDER'));
	$fielddef[] = array('name'=>$child_type.'_height','type'=>'height','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_HEIGHT'));
	#$fielddef[] = array('name'=>$child_type.'_heightFT','type'=>'int','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_HEIGHT'));
	#$fielddef[] = array('name'=>$child_type.'_heightIN','type'=>'int','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_HEIGHT'));
	$fielddef[] = array('name'=>$child_type.'_weight','type'=>'int','personid'=>$personid,'mappings'=>array('PERSONMED'.$personid.'_WEIGHT'));
	$personid++;
}

$test            = get_variable('test');

# Build the xml based on posted results
$xmlReq = new SimpleXMLElement('<api><params></params></api>');
$afs = $xmlReq->params->addChild('additional_fields');
# Auth 
$xmlReq['user']     = 'norvax';
$xmlReq['pass']     = 'n0rv8x';
$xmlReq['function'] = 'add_lead';
$xmlReq['mode']     = 'admin';
$xmlReq['vdcompat'] = '0';
$xmlReq['test'] = '0';
if (!empty($test)) $xmlReq['test'] = '1';
$xmlReq['debug']    = '0';
# Parameters / LeadData
$xmlReq->params->list_id          = '991002';
$xmlReq->params->vendor_lead_code = 'NORVAX';
$xmlReq->params->source_id        = 'WEB';
$xmlReq->params->phone_code       = '1';
$xmlReq->params->status           = 'NEW';


$history='';
$comments='';
foreach ($fielddef as $def) {
	$tmpdata='';
	$tmpdata2='';
	$tmpdata3='';
	#echo $def['name'].':'.$def['type']."\n";
	if ($def['type'] == 'int') {
		$tmpdata = OSDpreg_replace('/\D/g','',get_variable($def['name']));
	} elseif ($def['type'] == 'boolean') {
		$bool = get_variable($def['name']);
		if (OSDpreg_match('/^no$|^n$|^0$/i',$bool)) {
			$tmpdata='No';
		} elseif (OSDpreg_match('/^yes$|^y$|^1$/i',$bool)) {
			$tmpdata='Yes';
		} else {
			$tmpdata='';
		}
	} elseif ($def['type'] == 'datetime') {
		$datetime = get_variable($def['name']);
		if (!empty($datetime)) $tmpdata = date('Y-m-d h:i:s',strtotime($datetime));
	} elseif ($def['type'] == 'name') {
		$tmpdata2 = get_variable('first_name');
		$tmpdata3 = get_variable('last_name');
		$tmpdata = $tmpdata3;
		if (!empty($tmpdata2)) {
			if (!empty($tmpdata)) $tmpdata.=', ';
			$tmpdata .= $tmpdata2;
		}
	} elseif ($def['type'] == 'dob') {
		$dob = get_variable($def['name'].'_dob');
		$age = OSDpreg_replace('/\D/g','',get_variable($def['name'].'_age'));
		if (empty($dob)) {
			if (!empty($age)) {
				$yr = date('Y') - $age;
				$tmpdata = $yr.'-01-01';
			}
		} else {
			$tmpdata = date('Y-m-d',strtotime($dob));
		}
	} elseif ($def['type'] == 'gender') {
		$gen = get_variable($def['name']);
		if ($def['personid']==1) {
			$tmpdata2='Self';
		} elseif ($def['personid']==2) {
			$tmpdata2='Spouse';
		} elseif ($def['personid']>=3) {
			$tmpdata2='Child';
		}
		if (OSDpreg_match('/^m$|^male$|^man$|^men$|^b$|^boy$|^s$|^son$/i',$gen)) {
			$tmpdata='Male';
		} elseif (OSDpreg_match('/^f$|^female$|^w$|^woman$|^women$|^g$|^girl$|^d$|^daughter$/i',$gen)) {
			$tmpdata='Female';
		} else {
			$tmpdata='';
		}
	} elseif ($def['type'] == 'height') {
		$ft = OSDpreg_replace('/\D/g','',get_variable($def['name'].'FT'));
		if (!empty($ft)) $ft.="'";
		$in = OSDpreg_replace('/\D/g','',get_variable($def['name'].'IN'));
		if (!empty($ft) and empty($in)) $in.="0";
		if (!empty($in)) $in.="\"";
		$tmpdata=$ft.$in;
	} elseif ($def['type'] == 'phone') {
		$phone=get_variable($def['name']);
		$phone = OSDpreg_replace('/\D/','',$phone);
		$tmpdata = OSDpreg_replace('/^1/','',$phone);
	} else {
		# String
		$tmpdata=get_variable($def['name']);
	}

	if (count($def['mappings'])==0) {
		if ($def['name']=='name') {
			if (!empty($tmpdata2)) $xmlReq->params->addChild('first_name',$tmpdata2);
			if (!empty($tmpdata3)) $xmlReq->params->addChild('last_name',$tmpdata3);
			if (!empty($tmpdata)) {
				$afname = $afs->addChild('additional_field',$tmpdata);
				$afname->addAttribute('form','PERSON1');
				$afname->addAttribute('field','NAME');
			}
		} elseif ($def['name']=='conditions' and !empty($tmpdata)) {
			$history.='Conditions: '.$tmpdata.'~ ';
		} elseif ($def['name']=='medications' and !empty($tmpdata)) {
			$history.='Medications: '.$tmpdata.'~ ';
		} elseif ($def['name']=='contact_time' and !empty($tmpdata)) {
			$comments.='Contact Time: '.$tmpdata.'~ ';
		} elseif ($def['name']=='ip_address' and !empty($tmpdata)) {
			$comments.='IP Address: '.$tmpdata.'~ ';
		}
	} else {
		foreach ($def['mappings'] as $mapname) {
			if (!empty($tmpdata)) {
				if (OSDpreg_match('/[a-z]/',$mapname)) {
					$xmlReq->params->addChild($mapname,$tmpdata);
				} else {
					$af = $afs->addChild('additional_field',$tmpdata);
					$mapnamearray = OSDpreg_splitX('/_/',$mapname,2);
					$af->addAttribute('form',$mapnamearray[0]);
					$af->addAttribute('field',$mapnamearray[1]);
				}
			}
		}
	}
}
if (!empty($history)) {
	$afhistory = $afs->addChild('additional_field',$history);
	$afhistory->addAttribute('form','PERSON1MED');
	$afhistory->addAttribute('field','HISTORY');
}
if (!empty($comments)) {
	$afcomments = $afs->addChild('additional_field',$comments);
	$afcomments->addAttribute('form','GENERAL');
	$afcomments->addAttribute('field','COMMENT');
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

$ack = date('Y-m-d h:i:s').' '.$xmlRes->status['code'];

if ($xmlRes->status['code'] != 'SUCCESS') {
    $ack .= ' '.$xmlRes->status;
    header("HTTP/1.0 500 Internal Server Error: $ack");
} else {
    $ack .= ' Added Lead: '.$xmlRes->result->record->lead_id;
}
echo $ack."\n";

#echo prettyXML($xmlRes->asXML());

exit(0);
?>
