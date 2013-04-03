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
# 090410-1146 - Added custom2 field



######################
# ADD=7111111 view sample script with test variables
######################

if ($ADD==7111111)
{
	##### TEST VARIABLES #####
	$fullname = 'JOE AGENT';
	$user = '1001';
	$pass = '1001';
	$fronter = '2001';
	$lead_id = '1234';
	$campaign = 'TESTCAMP';
	$phone_login = '9999';
	$group = 'TESTCAMP';
	$channel_group = 'TESTCAMP';
	$SQLdate = date("Y-m-d H:i:s");
	$epoch = date("U");
	$uniqueid = '1163095830.4136';
	$customer_zap_channel = 'Zap/1-1';
	$server_ip = '10.10.10.15';
	$SIPexten = 'SIP/gs102';
	$session_id = '8600051';
	$list_id = date("YmdHis");
	$gmt_offset_now = '-5.0';

	$title = 'Mr.';
    $EFtitle = "<input type=text size=4 name=title id=title maxlength=4 value=\"$title\">";
	$first_name = 'JOHN';
    $EFfirst_name = "<input type=text size=14 name=first_name id=first_name maxlength=30 value=\"$first_name\">";
	$middle_initial = 'Q';
    $EFmiddle_initial = "<input type=text size=1 name=middle_initial id=middle_initial maxlength=1 value=\"$middle_initial\">";
	$last_name = 'PUBLIC';
    $EFlast_name = "<input type=text size=15 name=last_name id=last_name maxlength=30 value=\"$last_name\">";
	$address1 = '1234 Main St.';
    $EFaddress1 = "<input type=text size=58 name=address1 id=address1 maxlength=100 value=\"$address1\">";
	$address2 = 'Apt. 3';
    $EFaddress2 = "<input type=text size=22 name=address2 id=address2 maxlength=100 value=\"$address2\">";
	$address3 = 'ADDRESS3';
    $EFaddress3 = "<input type=text size=22 name=address3 id=address3 maxlength=100 value=\"$address3\">";
	$city = 'CHICAGO';
    $EFcity = "<input type=text size=22 name=city id=city maxlength=50 value=\"$city\">";
	$state = 'IL';
    $EFstate = "<input type=text size=2 name=state id=state maxlength=2 value=\"$state\">";
	$province = 'PROVINCE';
    $EFprovince = "<input type=text size=22 name=province id=province maxlength=50 value=\"$province\">";
	$postal_code = '33760';
    $EFpostal_code = "<input type=text size=9 name=postal_code id=postal_code maxlength=10 value=\"$postal_code\">";
	$country_code = 'USA';
    $EFcountry_code = "<input type=text size=4 name=country_code id=country_code maxlength=5 value=\"$country_code\">";
	$phone_code = '1';
    $EFphone_code = "<input type=text size=4 name=phone_code id=phone_code maxlength=10 value=\"$phone_code\">";
	$phone_number = '7275551212';
    $EFphone_number = "<input type=text size=12 name=phone_number id=phone_number maxlength=12 value=\"$phone_number\">";
	$alt_phone = '3125551212';
    $EFalt_phone = "<input type=text size=12 name=alt_phone id=alt_phone maxlength=12 value=\"$alt_phone\">";
	$email = 'test@test.com';
    $EFemail = "<input type=text size=22 name=email id=email maxlength=70 value=\"$email\">";
	$date_of_birth = '1970-01-01';
    $EFdate_of_birth = "<input type=text size=22 name=date_of_birth id=date_of_birth maxlength=20 value=\"$date_of_birth\">";
	$gender = 'M';
    $EFgender = "<select name=gender id=gender class=cust_form><option></option><option selected>M</option><option>F</option></select>";
	$post_date = date("Y-m-d");
    $EFpost_date = "<input type=text size=22 name=post_date id=post_date maxlength=20 value=\"$post_date\">";
	$vendor_lead_code = 'GOLDLEADS';
    $EFvendor_lead_code = "<input type=text size=15 name=vendor_lead_code id=vendor_lead_code maxlength=20 value=\"$vendor_lead_code\">";
	$comments = 'COMMENTS FIELD';
    $EFcomments = "<textarea name=comments id=comments rows=2 cols=56>$comments</textarea>";
	$custom1 = 'custom1';
    $EFcustom1 = "<input type=text size=22 name=custom1 id=custom1 maxlength=100 value=\"$custom1\">";
	$custom2 = 'custom2';
    $EFcustom2 = "<input type=text size=22 name=custom2 id=custom2 maxlength=100 value=\"$custom2\">";

$stmt=sprintf("SELECT * FROM osdial_scripts WHERE script_id='%s';",mres($script_id));
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$script_name =		$row[1];
$script_text =		$row[3];

$script_text = "<span style=\"display:block;\" id=\"SCRIPT_MAIN\">" . $script_text . "</span>";
$buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='" . $script_id . "'",'');
$hidebuttons = "document.getElementById('SCRIPT_MAIN').style.display='none';";
if (is_array($buttons)) {
    foreach ($buttons as $button) {
        $hidebuttons .= "document.getElementById('SCRIPT_" . $button['script_button_id'] . "').style.display='none';";
    }
}


if (is_array($buttons)) {
    foreach ($buttons as $button) {
        $script_text .= "<span style=\"display:none;\" id=\"SCRIPT_" . $button['script_button_id'] . "\">";
        $script_text .= "<center><input type=\"button\" value=\"TOP\" onclick=\"$hidebuttons document.getElementById('SCRIPT_MAIN').style.display='block';\"></center><br>";
        $script_text .= $button['script_button_text'];
        $script_text .= "</span>";
    }
}

if (OSDpreg_match("/iframe src/i",$script_text)) {
	$vendor_lead_code = OSDpreg_replace('/ /','+',$vendor_lead_code);
	$list_id = OSDpreg_replace('/ /','+',$list_id);
	$gmt_offset_now = OSDpreg_replace('/ /','+',$gmt_offset_now);
	$phone_code = OSDpreg_replace('/ /','+',$phone_code);
	$phone_number = OSDpreg_replace('/ /','+',$phone_number);
	$title = OSDpreg_replace('/ /','+',$title);
	$first_name = OSDpreg_replace('/ /','+',$first_name);
	$middle_initial = OSDpreg_replace('/ /','+',$middle_initial);
	$last_name = OSDpreg_replace('/ /','+',$last_name);
	$address1 = OSDpreg_replace('/ /','+',$address1);
	$address2 = OSDpreg_replace('/ /','+',$address2);
	$address3 = OSDpreg_replace('/ /','+',$address2);
	$city = OSDpreg_replace('/ /','+',$city);
	$state = OSDpreg_replace('/ /','+',$state);
	$province = OSDpreg_replace('/ /','+',$province);
	$postal_code = OSDpreg_replace('/ /','+',$postal_code);
	$country_code = OSDpreg_replace('/ /','+',$country_code);
	$gender = OSDpreg_replace('/ /','+',$gender);
	$date_of_birth = OSDpreg_replace('/ /','+',$date_of_birth);
	$alt_phone = OSDpreg_replace('/ /','+',$alt_phone);
	$email = OSDpreg_replace('/ /','+',$email);
	$custom1 = OSDpreg_replace('/ /','+',$custom1);
	$custom2 = OSDpreg_replace('/ /','+',$custom2);
	$comments = OSDpreg_replace('/ /','+',$comments);
	$fullname = OSDpreg_replace('/ /','+',$fullname);
	$user = OSDpreg_replace('/ /','+',$user);
	$pass = OSDpreg_replace('/ /','+',$pass);
	$lead_id = OSDpreg_replace('/ /','+',$lead_id);
	$campaign = OSDpreg_replace('/ /','+',$campaign);
	$phone_login = OSDpreg_replace('/ /','+',$phone_login);
	$group = OSDpreg_replace('/ /','+',$group);
	$channel_group = OSDpreg_replace('/ /','+',$channel_group);
	$SQLdate = OSDpreg_replace('/ /','+',$SQLdate);
	$epoch = OSDpreg_replace('/ /','+',$epoch);
	$uniqueid = OSDpreg_replace('/ /','+',$uniqueid);
	$customer_zap_channel = OSDpreg_replace('/ /','+',$customer_zap_channel);
	$server_ip = OSDpreg_replace('/ /','+',$server_ip);
	$SIPexten = OSDpreg_replace('/ /','+',$SIPexten);
	$session_id = OSDpreg_replace('/ /','+',$session_id);
}

# old variable substitution
$script_text = OSDpreg_replace('/--A--vendor_lead_code--B--/',$vendor_lead_code,$script_text);
$script_text = OSDpreg_replace('/--A--list_id--B--/',$list_id,$script_text);
$script_text = OSDpreg_replace('/--A--gmt_offset_now--B--/',$gmt_offset_now,$script_text);
$script_text = OSDpreg_replace('/--A--phone_code--B--/',$phone_code,$script_text);
$script_text = OSDpreg_replace('/--A--phone_number--B--/',$phone_number,$script_text);
$script_text = OSDpreg_replace('/--A--title--B--/',$title,$script_text);
$script_text = OSDpreg_replace('/--A--first_name--B--/',$first_name,$script_text);
$script_text = OSDpreg_replace('/--A--middle_initial--B--/',$middle_initial,$script_text);
$script_text = OSDpreg_replace('/--A--last_name--B--/',$last_name,$script_text);
$script_text = OSDpreg_replace('/--A--address1--B--/',$address1,$script_text);
$script_text = OSDpreg_replace('/--A--address2--B--/',$address2,$script_text);
$script_text = OSDpreg_replace('/--A--address3--B--/',$address3,$script_text);
$script_text = OSDpreg_replace('/--A--city--B--/',$city,$script_text);
$script_text = OSDpreg_replace('/--A--state--B--/',$state,$script_text);
$script_text = OSDpreg_replace('/--A--province--B--/',$province,$script_text);
$script_text = OSDpreg_replace('/--A--postal_code--B--/',$postal_code,$script_text);
$script_text = OSDpreg_replace('/--A--country_code--B--/',$country_code,$script_text);
$script_text = OSDpreg_replace('/--A--gender--B--/',$gender,$script_text);
$script_text = OSDpreg_replace('/--A--date_of_birth--B--/',$date_of_birth,$script_text);
$script_text = OSDpreg_replace('/--A--alt_phone--B--/',$alt_phone,$script_text);
$script_text = OSDpreg_replace('/--A--email--B--/',$email,$script_text);
$script_text = OSDpreg_replace('/--A--custom1--B--/',$custom1,$script_text);
$script_text = OSDpreg_replace('/--A--custom2--B--/',$custom2,$script_text);
$script_text = OSDpreg_replace('/--A--comments--B--/',$comments,$script_text);
$script_text = OSDpreg_replace('/--A--fullname--B--/',$fullname,$script_text);
$script_text = OSDpreg_replace('/--A--fronter--B--/',$fronter,$script_text);
$script_text = OSDpreg_replace('/--A--user--B--/',$user,$script_text);
$script_text = OSDpreg_replace('/--A--pass--B--/',$pass,$script_text);
$script_text = OSDpreg_replace('/--A--lead_id--B--/',$lead_id,$script_text);
$script_text = OSDpreg_replace('/--A--campaign--B--/',$campaign,$script_text);
$script_text = OSDpreg_replace('/--A--phone_login--B--/',$phone_login,$script_text);
$script_text = OSDpreg_replace('/--A--group--B--/',$group,$script_text);
$script_text = OSDpreg_replace('/--A--channel_group--B--/',$channel_group,$script_text);
$script_text = OSDpreg_replace('/--A--SQLdate--B--/',$SQLdate,$script_text);
$script_text = OSDpreg_replace('/--A--epoch--B--/',$epoch,$script_text);
$script_text = OSDpreg_replace('/--A--uniqueid--B--/',$uniqueid,$script_text);
$script_text = OSDpreg_replace('/--A--customer_zap_channel--B--/',$customer_zap_channel,$script_text);
$script_text = OSDpreg_replace('/--A--server_ip--B--/',$server_ip,$script_text);
$script_text = OSDpreg_replace('/--A--SIPexten--B--/',$SIPexten,$script_text);
$script_text = OSDpreg_replace('/--A--session_id--B--/',$session_id,$script_text);

#new variable substitution
$script_text = OSDpreg_replace('/\[\[list_id\]\]/',$list_id,$script_text);
$script_text = OSDpreg_replace('/\[\[gmt_offset_now\]\]/',$gmt_offset_now,$script_text);
$script_text = OSDpreg_replace('/\[\[fullname\]\]/',$fullname,$script_text);
$script_text = OSDpreg_replace('/\[\[fronter\]\]/',$fronter,$script_text);
$script_text = OSDpreg_replace('/\[\[user\]\]/',$user,$script_text);
$script_text = OSDpreg_replace('/\[\[pass\]\]/',$pass,$script_text);
$script_text = OSDpreg_replace('/\[\[lead_id\]\]/',$lead_id,$script_text);
$script_text = OSDpreg_replace('/\[\[campaign\]\]/',$campaign,$script_text);
$script_text = OSDpreg_replace('/\[\[phone_login\]\]/',$phone_login,$script_text);
$script_text = OSDpreg_replace('/\[\[group\]\]/',$group,$script_text);
$script_text = OSDpreg_replace('/\[\[channel_group\]\]/',$channel_group,$script_text);
$script_text = OSDpreg_replace('/\[\[SQLdate\]\]/',$SQLdate,$script_text);
$script_text = OSDpreg_replace('/\[\[epoch\]\]/',$epoch,$script_text);
$script_text = OSDpreg_replace('/\[\[uniqueid\]\]/',$uniqueid,$script_text);
$script_text = OSDpreg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$script_text);
$script_text = OSDpreg_replace('/\[\[server_ip\]\]/',$server_ip,$script_text);
$script_text = OSDpreg_replace('/\[\[SIPexten\]\]/',$SIPexten,$script_text);
$script_text = OSDpreg_replace('/\[\[session_id\]\]/',$session_id,$script_text);

$script_text = OSDpreg_replace('/\[\[title\]\]/',             $title,$script_text);
$script_text = OSDpreg_replace('/\[\[EFtitle\]\]/',           $EFtitle,$script_text);
$script_text = OSDpreg_replace('/\[\[first_name\]\]/',        $first_name,$script_text);
$script_text = OSDpreg_replace('/\[\[EFfirst_name\]\]/',      $EFfirst_name,$script_text);
$script_text = OSDpreg_replace('/\[\[middle_initial\]\]/',    $middle_initial,$script_text);
$script_text = OSDpreg_replace('/\[\[EFmiddle_initial\]\]/',  $EFmiddle_initial,$script_text);
$script_text = OSDpreg_replace('/\[\[last_name\]\]/',         $last_name,$script_text);
$script_text = OSDpreg_replace('/\[\[EFlast_name\]\]/',       $EFlast_name,$script_text);
$script_text = OSDpreg_replace('/\[\[address1\]\]/',          $address1,$script_text);
$script_text = OSDpreg_replace('/\[\[EFaddress1\]\]/',        $EFaddress1,$script_text);
$script_text = OSDpreg_replace('/\[\[address2\]\]/',          $address2,$script_text);
$script_text = OSDpreg_replace('/\[\[EFaddress2\]\]/',        $EFaddress2,$script_text);
$script_text = OSDpreg_replace('/\[\[address3\]\]/',          $address3,$script_text);
$script_text = OSDpreg_replace('/\[\[EFaddress3\]\]/',        $EFaddress3,$script_text);
$script_text = OSDpreg_replace('/\[\[city\]\]/',              $city,$script_text);
$script_text = OSDpreg_replace('/\[\[EFcity\]\]/',            $EFcity,$script_text);
$script_text = OSDpreg_replace('/\[\[state\]\]/',             $state,$script_text);
$script_text = OSDpreg_replace('/\[\[EFstate\]\]/',           $EFstate,$script_text);
$script_text = OSDpreg_replace('/\[\[province\]\]/',          $province,$script_text);
$script_text = OSDpreg_replace('/\[\[EFprovince\]\]/',        $EFprovince,$script_text);
$script_text = OSDpreg_replace('/\[\[postal_code\]\]/',       $postal_code,$script_text);
$script_text = OSDpreg_replace('/\[\[EFpostal_code\]\]/',     $EFpostal_code,$script_text);
$script_text = OSDpreg_replace('/\[\[country_code\]\]/',      $country_code,$script_text);
$script_text = OSDpreg_replace('/\[\[EFcountry_code\]\]/',    $EFcountry_code,$script_text);
$script_text = OSDpreg_replace('/\[\[phone_code\]\]/',        $phone_code,$script_text);
$script_text = OSDpreg_replace('/\[\[EFphone_code\]\]/',      $EFphone_code,$script_text);
$script_text = OSDpreg_replace('/\[\[phone_number\]\]/',      $phone_number,$script_text);
$script_text = OSDpreg_replace('/\[\[EFphone_number\]\]/',    $EFphone_number,$script_text);
$script_text = OSDpreg_replace('/\[\[alt_phone\]\]/',         $alt_phone,$script_text);
$script_text = OSDpreg_replace('/\[\[EFalt_phone\]\]/',       $EFalt_phone,$script_text);
$script_text = OSDpreg_replace('/\[\[email\]\]/',             $email,$script_text);
$script_text = OSDpreg_replace('/\[\[EFemail\]\]/',           $EFemail,$script_text);
$script_text = OSDpreg_replace('/\[\[gender\]\]/',            $gender,$script_text);
$script_text = OSDpreg_replace('/\[\[EFgender\]\]/',          $EFgender,$script_text);
$script_text = OSDpreg_replace('/\[\[date_of_birth\]\]/',     $date_of_birth,$script_text);
$script_text = OSDpreg_replace('/\[\[EFdate_of_birth\]\]/',   $EFdate_of_birth,$script_text);
$script_text = OSDpreg_replace('/\[\[post_date\]\]/',         $post_date,$script_text);
$script_text = OSDpreg_replace('/\[\[EFpost_date\]\]/',       $EFpost_date,$script_text);
$script_text = OSDpreg_replace('/\[\[vendor_lead_code\]\]/',  $vendor_lead_code,$script_text);
$script_text = OSDpreg_replace('/\[\[EFvendor_lead_code\]\]/',$EFvendor_lead_code,$script_text);
$script_text = OSDpreg_replace('/\[\[comments\]\]/',          $comments,$script_text);
$script_text = OSDpreg_replace('/\[\[EFcomments\]\]/',        $EFcomments,$script_text);
$script_text = OSDpreg_replace('/\[\[custom1\]\]/',           $custom1,$script_text);
$script_text = OSDpreg_replace('/\[\[EFcustom1\]\]/',         $EFcustom1,$script_text);
$script_text = OSDpreg_replace('/\[\[custom2\]\]/',           $custom2,$script_text);
$script_text = OSDpreg_replace('/\[\[EFcustom2\]\]/',         $EFcustom2,$script_text);

$buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='" . $script_id . "'",'');
if (is_array($buttons)) {
    foreach ($buttons as $button) {
        $script_text = OSDpreg_replace('/\{\{' . $button['script_button_id'] . '\}\}/imU', '{{' . $button['script_button_id'] . ':' . $button['script_button_label'] . '}}',$script_text);
        $script_text = OSDpreg_replace('/\{\{' . $button['script_button_id'] . ':(.*)\}\}/imU', '<input type="button" value="$1" onclick="' . $hidebuttons . ' document.getElementById(\'SCRIPT_' . $button['script_button_id'] . '\').style.display=\'block\';">',$script_text);
    }
}

$script_text = OSDpreg_replace('/\{\{DISPO:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="alert(\'Disposition as $1 and Hangup\');">',$script_text);
$script_text = OSDpreg_replace('/\{\{DISPO_NORMAL:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="alert(\'Disposition as $1 and Hangup, No WebForms.\');">',$script_text);
$script_text = OSDpreg_replace('/\{\{DISPO_WEBFORM1:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="alert(\'Disposition as $1 and Hangup, Open WebForm1.\');">',$script_text);
$script_text = OSDpreg_replace('/\{\{DISPO_WEBFORM2:(.*):(.*)\}\}/imU','<input type="button" value="$2" onclick="alert(\'Disposition as $1 and Hangup, Open WebForm2.\');">',$script_text);
$script_text = OSDpreg_replace('/\[\[(\w+)\]\]/imU','<input type="text" value="$1" size="30">',$script_text);

$script_text = OSDpreg_replace("/\n/","",$script_text);


echo "Preview Script: $script_id<BR>\n";
echo "<center>";
echo "<table class=shadedtable width=600 cellpadding=10>";
echo "  <tr bgcolor=$oddrows>";
echo "    <td>";
echo "      <center><b>$script_name</b></center>\n";
echo "    </td>";
echo "  </tr>";
echo "  <tr bgcolor=$evenrows>";
echo "    <td>";
echo "$script_text\n";
echo "    </td>";
echo "  </tr>";
echo "</table>";
echo "</center>\n";

}



######################
# ADD=1111111 display the ADD NEW SCRIPT SCREEN
######################

if ($ADD==1111111)
{
	if ($LOG['modify_scripts']==1)
	{
?>

<script type="text/javascript" src="/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
// Creates a new plugin class and a custom listbox
tinymce.create('tinymce.plugins.ExamplePlugin', {
    createControl: function(n, cm) {
        switch (n) {
            case 'helpb':
                var helpb = cm.createButton('helpb', {
                    title: 'Help',
                     image : '/admin/help.gif',
                     onclick : function() {
                        window.open('/admin/admin.php?ADD=99999#osdial_scripts-script_text','','width=800,height=500,scrollbars=yes,menubar=yes,address=yes');
                     }
                });
                return helpb;

            case 'myfields':
                var mlbf = cm.createListBox('myfields', {
                     title : 'Form Fields',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('<b>[[' + v + ']]</b>');
                        tinyMCE.activeEditor.controlManager.get('myfields').set(-1);
                     }
                });

                // Add some values to the list box
                mlbf.add('vendor_lead_code', 'vendor_lead_code');
                mlbf.add('source_id', 'source_id');
                mlbf.add('list_id', 'list_id');
                mlbf.add('gmt_offset_now', 'gmt_offset_now');
                mlbf.add('called_since_last_reset', 'called_since_last_reset');
                mlbf.add('phone_code', 'phone_code');
                mlbf.add('phone_number', 'phone_number');
                mlbf.add('title', 'title');
                mlbf.add('first_name', 'first_name');
                mlbf.add('middle_initial', 'middle_initial');
                mlbf.add('last_name', 'last_name');
                mlbf.add('address1', 'address1');
                mlbf.add('address2', 'address2');
                mlbf.add('address3', 'address3');
                mlbf.add('city', 'city');
                mlbf.add('state', 'state');
                mlbf.add('province', 'province');
                mlbf.add('postal_code', 'postal_code');
                mlbf.add('country_code',' country_code');
                mlbf.add('gender', 'gender');
                mlbf.add('date_of_birth', 'date_of_birth');
                mlbf.add('alt_phone', 'alt_phone');
                mlbf.add('email', 'email');
                mlbf.add('custom1', 'custom1');
                mlbf.add('custom2', 'custom2');
                mlbf.add('comments', 'comments');
                mlbf.add('fullname', 'fullname');
                mlbf.add('user', 'user');
                mlbf.add('pass', 'pass');
                mlbf.add('lead_id', 'lead_id');
                mlbf.add('campaign', 'campaign');
                mlbf.add('phone_login', 'phone_login');
                mlbf.add('group', 'group');
                mlbf.add('channel_group', 'channel_group');
                mlbf.add('SQLdate', 'SQLdate');
                mlbf.add('epoch', 'epoch');
                mlbf.add('uniqueid', 'uniqueid');
                mlbf.add('customer_zap_channel', 'customer_zap_channel');
                mlbf.add('server_ip', 'server_ip');
                mlbf.add('SIPexten', 'SIPexten');
                mlbf.add('session_id', 'session_id');

                // Return the new listbox instance
                return mlbf;

            case 'myeditfields':
                var mlbef = cm.createListBox('myeditfields', {
                     title : 'Editable Fields',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('[[' + v + ']]');
                        tinyMCE.activeEditor.controlManager.get('myeditfields').set(-1);
                     }
                });

                // Add some values to the list box
                mlbef.add('title',            'EFtitle');
                mlbef.add('first_name',       'EFfirst_name');
                mlbef.add('middle_initial',   'EFmiddle_initial');
                mlbef.add('last_name',        'EFlast_name');
                mlbef.add('address1',         'EFaddress1');
                mlbef.add('address2',         'EFaddress2');
                mlbef.add('address3',         'EFaddress3');
                mlbef.add('city',             'EFcity');
                mlbef.add('state',            'EFstate');
                mlbef.add('province',         'EFprovince');
                mlbef.add('postal_code',      'EFpostal_code');
                mlbef.add('country_code',     'EFcountry_code');
                mlbef.add('email',            'EFemail');
                mlbef.add('phone_code',       'EFphone_code');
                mlbef.add('phone_number',     'EFphone_number');
                mlbef.add('alt_phone',        'EFalt_phone');
                mlbef.add('comments',         'EFcomments');
                mlbef.add('date_of_birth',    'EFdate_of_birth');
                mlbef.add('gender',           'EFgender');
                mlbef.add('post_date',        'EFpost_date');
                mlbef.add('vendor_lead_code', 'EFvendor_lead_code');
                mlbef.add('custom1',          'EFcustom1');
                mlbef.add('custom2',          'EFcustom2');

                // Return the new listbox instance
                return mlbef;

            case 'myaddtlfields':
                var mlbaf = cm.createListBox('myaddtlfields', {
                     title : 'Addtl Fields',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('<b>[[' + v + ']]</b>');
                        tinyMCE.activeEditor.controlManager.get('myaddtlfields').set(-1);
                         //tinyMCE.activeEditor.windowManager.alert('Value selected:' + v);
                     }
                });

                // Add some values to the list box
<?php
    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'",'');
    if (is_array($forms)) {
        foreach ($forms as $form) {
	        $fcamps = OSDpreg_split('/,/',$form['campaigns']);
            if (is_array($fcamps)) {
	            foreach ($fcamps as $fcamp) {
                    $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'",'');
                    if (is_array($fields)) {
                        foreach ($fields as $field) {
                            echo "      mlbaf.add('" . $form['name'] . '_' . $field['name'] . "','" . $form['name'] . '_' . $field['name'] . "');\n";
                        }
			        }
                }
		    }
	    }
    }
?>

                // Return the new listbox instance
                return mlbaf;

        }

        return null;
    }
});

// Register plugin with a short name
tinymce.PluginManager.add('example', tinymce.plugins.ExamplePlugin);

// Initialize TinyMCE with the new plugin and listbox
tinyMCE.init({
    plugins : '-example', // - tells TinyMCE to skip the loading of the plugin
    mode : "textareas",
    theme : "advanced",
    theme_advanced_buttons1 : "separator,fontselect,fontsizeselect,forecolor,backcolor,separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,hr,sub,sup,separator,cut,copy,paste,separator,undo,redo,separator",
    theme_advanced_buttons2 : "separator,myfields,separator,myeditfields,separator,myaddtlfields,separator,helpb,separator",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "center",
    theme_advanced_statusbar_location : "bottom"
});
</script>

<?php

	echo "<center><br><font class=top_header color=$default_text size=+1>ADD NEW SCRIPT</font><form name=scriptForm action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2111111>\n";
	echo "<TABLE class=shadedtable width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Script ID: </td><td align=left>";
    if ($LOG['multicomp_admin'] > 0) {
        $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
        echo "<select name=company_id>\n";
        if (is_array($comps)) {
            foreach ($comps as $comp) {
                echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
        }
        echo "</select>\n";
    } elseif ($LOG['multicomp']>0) {
        echo "<input type=hidden name=company_id value=$LOG[company_id]>";
        #echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
    }
    echo "<input type=text name=script_id size=12 maxlength=10> (no spaces or punctuation)".helptag("osdial_scripts-script_id")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Script Name: </td><td align=left><input type=text name=script_name size=40 maxlength=50> (title of the script)".helptag("osdial_scripts-script_name")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Script Comments: </td><td align=left><input type=text name=script_comments size=50 maxlength=255> ".helptag("osdial_scripts-script_comments")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option></select>".helptag("osdial_scripts-active")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=center colspan=2>";

	echo "<TEXTAREA NAME=script_text ROWS=20 COLS=110 value=\"\"></TEXTAREA></td></tr>\n";
	echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=2111111 adds new script to the system
######################

if ($ADD==2111111)
{
    $prescript_id = $script_id;
    if ($LOG['multicomp'] > 0) $prescript_id = (($company_id * 1) + 100) . $script_id;
	$stmt=sprintf("SELECT count(*) FROM osdial_scripts WHERE script_id='%s';",mres($prescript_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>SCRIPT NOT ADDED - there is already a script entry with this name</font>\n";}
	else
		{
		 if ( (OSDstrlen($script_id) < 2) or (OSDstrlen($script_name) < 2) or (OSDstrlen($script_text) < 2) )
			{
			 echo "<br><font color=red>SCRIPT NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Script name, description and text must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
            if ($LOG['multicomp'] > 0) $script_id = (($company_id * 1) + 100) . $script_id;
            $stmt=sprintf("INSERT INTO osdial_scripts VALUES('%s','%s','%s','%s','%s');",mres($script_id),mres($script_name),mres($script_comments),mres($script_text),mres($active));
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=$default_text>SCRIPT ADDED: $script_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW SCRIPT ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}

                if ($LOG['allowed_scriptsALL']==0) {
                    $LOG['allowed_scripts'][] = $script_id;
                    $scripts_value = ' ' . implode(' ', $LOG['allowed_scripts']) . ' -';
                    $stmt = sprintf("UPDATE osdial_user_groups SET allowed_scripts='%s' WHERE user_group='%s';",mres($scripts_value),mres($LOG['user_group']));
				    $rslt=mysql_query($stmt, $link);
                }

			}
		}
$ADD=1000000;
}


######################
# ADD=4111111 modify script in the system
######################

if ($ADD==4111111) {
	if ($LOG['modify_scripts']==1) {
        if ( (OSDstrlen($script_id) < 2) or (OSDstrlen($script_name) < 2) or (OSDstrlen($script_text) < 2) ) {
            echo "<br><font color=red>SCRIPT NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>Script name, description and text must be at least 2 characters in length</font><br>\n";
        } else {
            if ($script_button_id == "") {
                $stype = "SCRIPT";
                $sid = $script_id;
                $stmt=sprintf("UPDATE osdial_scripts SET script_name='%s',script_comments='%s',script_text='%s',active='%s' WHERE script_id='%s';",mres($script_name),mres($script_comments),mres($script_text),mres($active),mres($script_id));
            } else {
                $stype = "BUTTON/OBJECTION";
                $sid = $script_id . ":" . $script_button_id;
                $stmt=sprintf("UPDATE osdial_script_buttons SET script_button_label='%s',script_button_description='%s',script_button_text='%s' WHERE script_id='%s' AND script_button_id='%s';",mres($script_name),mres($script_comments),mres($script_text),mres($script_id),mres($script_button_id));
            }
		    $rslt=mysql_query($stmt, $link);

		    echo "<br><B><font color=$default_text>$stype MODIFIED</font></B>\n";

		    ### LOG CHANGES TO LOG FILE ###
		    if ($WeBRooTWritablE > 0) {
			    $fp = fopen ("./admin_changes_log.txt", "a");
			    fwrite ($fp, "$date|MODIFY $stype ENTRY         |$PHP_AUTH_USER|$ip|$sid|\n");
			    fclose($fp);
		    }
	    }
    $ADD=3111111;	# go to script modification form below
    } else {
	    echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=5111111 confirmation before deletion of script record
######################

if ($ADD==5111111) {
	 if ( (OSDstrlen($script_id) < 2) or ($LOG['delete_scripts'] < 1) ) {
		 echo "<br><font color=red>SCRIPT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Script_id must be at least 2 characters in length</font>\n";
	} else {
        if ($script_button_id == "") {
		    echo "<br><B><font color=$default_text>SCRIPT DELETION CONFIRMATION: $script_id</B>\n";
    		echo "<br><br><a href=\"$PHP_SELF?ADD=6111111&script_id=$script_id&CoNfIrM=YES\">Click here to delete script $script_id</a></font><br><br><br>\n";
		} else {
		    echo "<br><B><font color=$default_text>BUTTON/OBJECTION DELETION CONFIRMATION: $script_id</B>\n";
    		echo "<br><br><a href=\"$PHP_SELF?ADD=6111111&script_id=$script_id&script_button_id=$script_button_id&CoNfIrM=YES\">Click here to delete button/objection $script_id : $script_button_id</a></font><br><br><br>\n";
        }
    }
    $ADD='3111111';		# go to script modification below
}



######################
# ADD=6111111 delete script record
######################

if ($ADD==6111111) {
    $ADD='1000000';		# go to script list
	 if ( (OSDstrlen($script_id) < 2) or ($CoNfIrM != 'YES') or ($LOG['delete_scripts'] < 1) ) {
		 echo "<br><font color=red>SCRIPT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Script_id be at least 2 characters in length</font><br>\n";
	} else {
        if ($script_button_id == "") {
		    $stmt=sprintf("DELETE FROM osdial_scripts WHERE script_id='%s' LIMIT 1;",mres($script_id));
            $stype = "SCRIPT";
            $sid = $script_id;
        } else {
		    $stmt=sprintf("DELETE FROM osdial_script_buttons WHERE script_id='%s' AND script_button_id='%s' LIMIT 1;",mres($script_id),mres($script_button_id));
            $stype = "BUTTON/OBJECTION";
            $sid = $script_id . ":" . $script_button_id;
            $ADD='3111111';		# go to script modification below
        }
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0) {
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING $stype!!!!|$PHP_AUTH_USER|$ip|$sid|\n");
			fclose($fp);
		}
		echo "<br><B><font color=$default_text>$stype DELETION COMPLETED: $sid</font></B>\n";
		echo "<br><br>\n";
	}
}



######################
# ADD=3111111 modify script info in the system
######################

if ($ADD==3111111)
{
	if ($LOG['modify_scripts']==1)
	{
?>

<script type="text/javascript" src="/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
// Creates a new plugin class and a custom listbox
tinymce.create('tinymce.plugins.ExamplePlugin', {
    createControl: function(n, cm) {
        switch (n) {
            case 'helpb':
                var helpb = cm.createButton('helpb', {
                    title: 'Help',
                     image : '/admin/help.gif',
                     onclick : function() {
                        window.open('/admin/admin.php?ADD=99999#osdial_scripts-script_text','','width=800,height=500,scrollbars=yes,menubar=yes,address=yes');
                     }
                });
                return helpb;

            case 'previewb':
                var previewb = cm.createButton('previewb', {
                    label: 'Preview',
                    onclick : function() {
                        window.open('/admin/admin.php?ADD=7111111&script_id=<?php echo $script_id; ?>','','width=1000,height=700,scrollbars=yes,menubar=yes,address=yes');
                     }
                });
                return previewb;

            case 's0':
                var s = cm.createButton('s0',{label: ' '});
                return s;
            case 's1':
                var s = cm.createButton('s1',{label: ' '});
                return s;


            case 'myfields':
                var mlbf = cm.createListBox('myfields', {
                     title : 'Form Fields',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('<b>[[' + v + ']]</b>');
                        tinyMCE.activeEditor.controlManager.get('myfields').set(-1);
                     }
                });
                mlbf.add('vendor_lead_code', 'vendor_lead_code');
                mlbf.add('source_id', 'source_id');
                mlbf.add('list_id', 'list_id');
                mlbf.add('gmt_offset_now', 'gmt_offset_now');
                mlbf.add('called_since_last_reset', 'called_since_last_reset');
                mlbf.add('phone_code', 'phone_code');
                mlbf.add('phone_number', 'phone_number');
                mlbf.add('title', 'title');
                mlbf.add('first_name', 'first_name');
                mlbf.add('middle_initial', 'middle_initial');
                mlbf.add('last_name', 'last_name');
                mlbf.add('address1', 'address1');
                mlbf.add('address2', 'address2');
                mlbf.add('address3', 'address3');
                mlbf.add('city', 'city');
                mlbf.add('state', 'state');
                mlbf.add('province', 'province');
                mlbf.add('postal_code', 'postal_code');
                mlbf.add('country_code',' country_code');
                mlbf.add('gender', 'gender');
                mlbf.add('date_of_birth', 'date_of_birth');
                mlbf.add('alt_phone', 'alt_phone');
                mlbf.add('email', 'email');
                mlbf.add('custom1', 'custom1');
                mlbf.add('custom2', 'custom2');
                mlbf.add('comments', 'comments');
                mlbf.add('lead_id', 'lead_id');
                mlbf.add('campaign', 'campaign');
                mlbf.add('phone_login', 'phone_login');
                mlbf.add('group', 'group');
                mlbf.add('channel_group', 'channel_group');
                mlbf.add('SQLdate', 'SQLdate');
                mlbf.add('epoch', 'epoch');
                mlbf.add('uniqueid', 'uniqueid');
                mlbf.add('customer_zap_channel', 'customer_zap_channel');
                mlbf.add('server_ip', 'server_ip');
                mlbf.add('SIPexten', 'SIPexten');
                mlbf.add('session_id', 'session_id');
                return mlbf;

            case 'myeditfields':
                var mlbef = cm.createListBox('myeditfields', {
                     title : 'Editable Fields',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('[[' + v + ']]');
                        tinyMCE.activeEditor.controlManager.get('myeditfields').set(-1);
                     }
                });
                mlbef.add('title',            'EFtitle');
                mlbef.add('first_name',       'EFfirst_name');
                mlbef.add('middle_initial',   'EFmiddle_initial');
                mlbef.add('last_name',        'EFlast_name');
                mlbef.add('address1',         'EFaddress1');
                mlbef.add('address2',         'EFaddress2');
                mlbef.add('address3',         'EFaddress3');
                mlbef.add('city',             'EFcity');
                mlbef.add('state',            'EFstate');
                mlbef.add('province',         'EFprovince');
                mlbef.add('postal_code',      'EFpostal_code');
                mlbef.add('country_code',     'EFcountry_code');
                mlbef.add('email',            'EFemail');
                mlbef.add('phone_code',       'EFphone_code');
                mlbef.add('phone_number',     'EFphone_number');
                mlbef.add('alt_phone',        'EFalt_phone');
                mlbef.add('comments',         'EFcomments');
                mlbef.add('date_of_birth',    'EFdate_of_birth');
                mlbef.add('gender',           'EFgender');
                mlbef.add('post_date',        'EFpost_date');
                mlbef.add('vendor_lead_code', 'EFvendor_lead_code');
                mlbef.add('custom1',          'EFcustom1');
                mlbef.add('custom2',          'EFcustom2');
                return mlbef;

            case 'myaddtlfields':
                var mlbaf = cm.createListBox('myaddtlfields', {
                     title : 'Addtl Fields',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('[[' + v + ']]');
                        tinyMCE.activeEditor.controlManager.get('myaddtlfields').set(-1);
                         //tinyMCE.activeEditor.windowManager.alert('Value selected:' + v);
                     }
                });
<?php
    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'",'');
    if (is_array($forms)) {
        foreach ($forms as $form) {
	        $fcamps = OSDpreg_split('/,/',$form['campaigns']);
            if (is_array($fcamps)) {
	            foreach ($fcamps as $fcamp) {
                    $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'",'');
                    if (is_array($fields)) {
                        foreach ($fields as $field) {
                            echo "      mlbaf.add('" . $form['name'] . '_' . $field['name'] . "','" . $form['name'] . '_' . $field['name'] . "');\n";
			            }
		            }
	            }
            }
        }
    }
?>
                return mlbaf;

            case 'mybuttons':
                var mlb = cm.createListBox('mybuttons', {
                     title : "Script Buttons<br>Objections / Rebuttals",
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('{{' + v + '}}');
                        tinyMCE.activeEditor.controlManager.get('mybuttons').set(-1);
                         //tinyMCE.activeEditor.windowManager.alert('Value selected:' + v);
                     }
                });
<?php
    $buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='$script_id'",'');
    if (is_array($buttons)) {
        foreach ($buttons as $button) {
            echo "      mlb.add('" . $button['script_button_id'] . ': ' . $button['script_button_label'] . "','" . $button['script_button_id'] . "');\n";
	    }
    }
?>
                return mlb;

            case 'mydispo':
                var mldb = cm.createListBox('mydispo', {
                     title : 'Dispo Buttons<br>Hangup and Disposition',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('{{' + v + '}}');
                        tinyMCE.activeEditor.controlManager.get('mydispo').set(-1);
                         //tinyMCE.activeEditor.windowManager.alert('Value selected:' + v);
                     }
                });
<?php
    $DBdispo = Array();
    $stmt = "SELECT status,status_name FROM (SELECT status,status_name FROM osdial_statuses WHERE selectable='Y' UNION SELECT status,status_name FROM osdial_campaign_statuses WHERE selectable='Y') AS stat GROUP BY status;";
	$rslt=mysql_query($stmt, $link);
    while ($row=mysql_fetch_row($rslt)) {
        $DBdispo[$row[0]] = $row[1];
	}
    $DBdefault='';
    $DBnormal='';
    $DBwebform1='';
    $DBwebform2='';
    if (is_array($DBdispo)) {
        foreach ($DBdispo as $DBcode => $DBdesc) {
            $DBdefault .= "      mldb.add('" . $DBcode . " <!-- \"" . $DBdesc . "\" (Use Campaign/InGroup submit method) -->','DISPO:" . $DBcode . ":" . $DBdesc . "',{title : '" . $DBdesc . "'});\n";
            $DBnormal .= "      mldb.add('NORMAL:" . $DBcode . " <!-- \"" . $DBdesc . "\" (NORMAL submit method, no webforms) -->','DISPO_NORMAL:" . $DBcode . ":" . $DBdesc . "',{title : '" . $DBdesc . "'});\n";
            $DBwebform1 .= "      mldb.add('WEBFORM1:" . $DBcode . " <!-- \"" . $DBdesc . "\" (WEBFORM1 submit method) -->','DISPO_WEBFORM1:" . $DBcode . ":" . $DBdesc . "',{title : '" . $DBdesc . "'});\n";
            $DBwebform2 .= "      mldb.add('WEBFORM2:" . $DBcode . " <!-- \"" . $DBdesc . "\" (WEBFORM2 submit method) -->','DISPO_WEBFORM2:" . $DBcode . ":" . $DBdesc . "',{title : '" . $DBdesc . "'});\n";
	    }
    }
    echo $DBdefault . $DBwebform1 . $DBwebform2 . $DBnormal;
?>
                return mldb;

        }

        return null;
    }
});

// Register plugin with a short name
tinymce.PluginManager.add('example', tinymce.plugins.ExamplePlugin);

// Initialize TinyMCE with the new plugin and listbox
tinyMCE.init({
    plugins : '-example', // - tells TinyMCE to skip the loading of the plugin
    mode : "textareas",
    theme : "advanced",
    theme_advanced_buttons1 : "separator,fontselect,fontsizeselect,forecolor,backcolor,separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,hr,sub,sup,separator,cut,copy,paste,separator,undo,redo,separator,code,separator",
    theme_advanced_buttons2 : "separator,myfields,separator,separator,myeditfields,separator,separator,myaddtlfields,separator,s0,separator,mydispo,separator,separator,mybuttons,separator,s1,separator,previewb,separator,separator,helpb,separator",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "center",
    theme_advanced_statusbar_location : "bottom"
});
</script>

<?php

    if ($SUB == "--NEW--") {
        $stmt=sprintf("SELECT count(*) FROM osdial_script_buttons WHERE script_id='%s' AND script_button_id='%s';",mres($script_id),mres($script_button_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>BUTTON / OBJECTION NOT ADDED - there is already a button entry with this name</font>\n";
        } else {
            if ( (OSDstrlen($script_button_id) < 2) or (OSDstrlen($script_button_label) < 2) or (OSDstrlen($script_button_description) < 2) or ($script_button_id == 'MAIN')) {
                echo "<br><font color=red>BUTTON / OBJECTION NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>Button/Objection name, description and text must be at least 2 characters in length</font><br>\n";
            } else {
                $stmt=sprintf("INSERT INTO osdial_script_buttons VALUES('%s','%s','%s','%s','');",mres($script_id),mres($script_button_id),mres($script_button_description),mres($script_button_label));
                $rslt=mysql_query($stmt, $link);
                echo "<br><B><font color=$default_text>BUTTON / OBJECTION ADDED: $script_button_id</font></B>\n";
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW SCRIPT BUTTON ENTRY  |$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }
        $SUB = "";
    }

	$stmt=sprintf("SELECT * FROM osdial_scripts WHERE script_id='%s';",mres($script_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$script_name =		$row[1];
	$script_comments =	$row[2];
	$script_text =		$row[3];
	$active =			$row[4];

    if ($SUB != "") {
        $stmt=sprintf("SELECT * FROM osdial_script_buttons WHERE script_id='%s' AND script_button_id='%s';",mres($script_id),mres($SUB));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $script_button_id =	$row[1];
        $script_comments =	$row[2];
        $script_name =		$row[3];
        $script_text =		$row[4];
        $stype = "Button/Objection";
        $stypec = "BUTTON/OBJECTION";
        $id_label = $stype . " ID";
        $name_label = $stype . " Label";
        $comment_label = $stype . " Description";
        $sid = $script_id . ": " . $script_button_id;
    } else {
        $stype = "Script";
        $stypec = "SCRIPT";
        $id_label = $stype . " ID";
        $name_label = $stype . " Name";
        $comment_label = $stype . " Comments";
        $sid = $script_id;
    }

    $script_text = OSDpreg_replace("/\n/","",$script_text);

	echo "<center><br><font class=top_header color=$default_text size=+1>MODIFY A $stypec</font><form name=scriptForm action=$PHP_SELF method=POST><br>\n";
	echo "<input type=hidden name=ADD value=4111111>\n";
	echo "<input type=hidden name=script_id value=\"$script_id\">\n";
    if ($SUB != "") {
	    echo "<input type=hidden name=script_button_id value=\"$script_button_id\">\n";
		echo "<center><a href=\"$PHP_SELF?ADD=$ADD&script_id=$script_id\">BACK TO SCRIPT: " . mclabel($script_id) . "</a></center><br>\n";
    }
	echo "<TABLE class=shadedtable width=$section_width>";
	echo "<tr bgcolor=$oddrows><td align=right>$id_label: </td><td align=left><B>" . mclabel($sid) . "</B>".helptag('osdial_scripts-script_name')."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>$name_label: </td><td align=left><input type=text name=script_name size=40 maxlength=50 value=\"$script_name\">".helptag("osdial_scripts-script_name")."</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>$comment_label: </td><td align=left><input type=text name=script_comments size=50 maxlength=255 value=\"$script_comments\"> ".helptag("osdial_scripts-script_comments")."</td></tr>\n";
    if ($SUB == "") {
	    echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option><option selected>$active</option></select>".helptag("osdial_scripts-active")."</td></tr>\n";
    }
	echo "<tr bgcolor=$oddrows><td align=center colspan=2>";

	echo "<TEXTAREA NAME=script_text ROWS=20 COLS=110>$script_text</TEXTAREA></td></tr>\n";
	echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></form></center>\n";

// 	if ($LOG['delete_scripts'] > 0) {
// 		echo "<center><a href=\"$PHP_SELF?ADD=5111111&script_id=$script_id";
//         if ($SUB != "") {
// 		    echo "&script_button_id=$script_button_id";
//         }
// 		echo "\">DELETE THIS $stypec</a></center>\n";
// 	}


    # Sub-scripts / Buttons.
    if ($SUB == "") {
        echo "<br /><br /><hr width=50%>\n";
        echo "<center><font color=$default_text size=+1>BUTTONS / OBJECTIONS & REBUTTALS</font><br><br>\n";
        echo "<table class=shadedtable bgcolor=grey width=$section_width cellspacing=1 cellpadding=1>\n";
        echo "  <tr class=tabheader>\n";
        echo "      <td align=center>ID</td>\n";
        echo "      <td align=center>BUTTON LABEL</td>\n";
        echo "      <td align=center>DESCRIPTION</td>\n";
        echo "      <td align=center>ACTION</td>\n";
        echo "  </tr>\n";
        $buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='" . $script_id . "'",'');
        $cnt = 0;
        if (is_array($buttons)) {
            foreach ($buttons as $button) {
                echo '  <form action="' . $PHP_SELF . '" method="POST">';
                echo '  <input type="hidden" name="ADD" value="' . $ADD . '">';
                echo '  <input type="hidden" name="SUB" value="' . $button['script_button_id'] . '">';
	            echo "  <input type=hidden name=script_id value=\"$script_id\">\n";
                echo "  <tr " . bgcolor($cnt) . " class=\"row font1\">\n";
                echo "      <td align=center>" . $button['script_button_id'] . "</td>\n";
                echo "      <td align=center>" . $button['script_button_label'] . "</td>\n";
                echo "      <td align=center>" . $button['script_button_description'] . "</td>\n";
                echo "      <td align=center class=tabbutton1><input type=submit value=\"Edit\"></td>\n";
                echo "  </tr>\n";
                echo "  </form>\n";
                $cnt++;
            }
        }
        echo '  <form action="' . $PHP_SELF . '" method="POST">';
        echo '  <input type="hidden" name="ADD" value="' . $ADD . '">';
        echo '  <input type="hidden" name="SUB" value="--NEW--">';
	    echo "  <input type=hidden name=script_id value=\"$script_id\">\n";
        echo "  <tr class=tabfooter>\n";
        echo "      <td class=tabinput align=center><input type=text name=script_button_id size=10 maxlength=10 value=\"\"></td>\n";
        echo "      <td class=tabinput align=center><input type=text name=script_button_label size=20 maxlength=50 value=\"\"></td>\n";
        echo "      <td class=tabinput align=center><input type=text name=script_button_description size=30 maxlength=100 value=\"\"></td>\n";
        echo "      <td class=tabbutton1 align=center><input type=submit value=\"NEW\"></td>\n";
        echo "  </tr>\n";
        echo "  </form>\n";
        echo "</table>\n";
    }


	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=1000000 display all scripts
######################
if ($ADD==1000000)
{
	$stmt=sprintf("SELECT * FROM osdial_scripts WHERE script_id LIKE '%s__%%' AND script_id IN %s ORDER BY script_id;",$LOG['company_prefix'],$LOG['allowed_scriptsSQL']);
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font class=top_header color=$default_text size=+1>SCRIPTS</font><br><br>\n";
echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>";
echo "    <td>NAME</td>\n";
echo "    <td>DESCRIPTION</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3111111&script_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=3111111&script_id=$row[0]\">" . mclabel($row[0]) . "</a></td>\n";
		echo "    <td>$row[1]</td>\n";
		echo "    <td align=center><a href=\"$PHP_SELF?ADD=3111111&script_id=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>\n";
echo "    <td colspan=3></td>\n";
echo "  </tr>\n";
echo "</table></center>\n";
}

?>
