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
	$vendor_lead_code = 'VENDOR:LEAD;CODE';
	$list_id = 'LISTID';
	$gmt_offset_now = 'GMTOFFSET';
	$phone_code = '1';
	$phone_number = '7275551212';
	$title = 'Mr.';
	$first_name = 'JOHN';
	$middle_initial = 'Q';
	$last_name = 'PUBLIC';
	$address1 = '1234 Main St.';
	$address2 = 'Apt. 3';
	$address3 = 'ADDRESS3';
	$city = 'CHICAGO';
	$state = 'IL';
	$province = 'PROVINCE';
	$postal_code = '33760';
	$country_code = 'USA';
	$gender = 'M';
	$date_of_birth = '1970-01-01';
	$alt_phone = '3125551212';
	$email = 'test@test.com';
	$custom1 = 'custom1';
	$custom2 = 'custom2';
	$comments = 'COMMENTS FIELD';
	$RGfullname = 'JOE AGENT';
	$RGuser = '6666';
	$RGlead_id = '1234';
	$RGcampaign = 'TESTCAMP';
	$RGphone_login = 'gs102';
	$RGgroup = 'TESTCAMP';
	$RGchannel_group = 'TESTCAMP';
	$RGSQLdate = date("Y-m-d H:i:s");
	$RGepoch = date("U");
	$RGuniqueid = '1163095830.4136';
	$RGcustomer_zap_channel = 'Zap/1-1';
	$RGserver_ip = '10.10.10.15';
	$RGSIPexten = 'SIP/gs102';
	$RGsession_id = '8600051';

$stmt="SELECT * from osdial_scripts where script_id='$script_id';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$script_name =		$row[1];
$script_text =		$row[3];

$script_text = "<span style=\"display:block;\" id=\"SCRIPT_MAIN\">" . $script_text . "</span>";
$buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='" . $script_id . "'");
$hidebuttons = "document.getElementById('SCRIPT_MAIN').style.display='none';";
foreach ($buttons as $button) {
    $hidebuttons .= "document.getElementById('SCRIPT_" . $button['script_button_id'] . "').style.display='none';";
}


foreach ($buttons as $button) {
    $script_text .= "<span style=\"display:none;\" id=\"SCRIPT_" . $button['script_button_id'] . "\">";
    $script_text .= "<center><input type=\"button\" value=\"TOP\" onclick=\"$hidebuttons document.getElementById('SCRIPT_MAIN').style.display='block';\"></center><br>";
    $script_text .= $button['script_button_text'];
    $script_text .= "</span>";
}

if (eregi("iframe src",$script_text))
	{
	$vendor_lead_code = eregi_replace(' ','+',$vendor_lead_code);
	$list_id = eregi_replace(' ','+',$list_id);
	$gmt_offset_now = eregi_replace(' ','+',$gmt_offset_now);
	$phone_code = eregi_replace(' ','+',$phone_code);
	$phone_number = eregi_replace(' ','+',$phone_number);
	$title = eregi_replace(' ','+',$title);
	$first_name = eregi_replace(' ','+',$first_name);
	$middle_initial = eregi_replace(' ','+',$middle_initial);
	$last_name = eregi_replace(' ','+',$last_name);
	$address1 = eregi_replace(' ','+',$address1);
	$address2 = eregi_replace(' ','+',$address2);
	$address3 = eregi_replace(' ','+',$address2);
	$city = eregi_replace(' ','+',$city);
	$state = eregi_replace(' ','+',$state);
	$province = eregi_replace(' ','+',$province);
	$postal_code = eregi_replace(' ','+',$postal_code);
	$country_code = eregi_replace(' ','+',$country_code);
	$gender = eregi_replace(' ','+',$gender);
	$date_of_birth = eregi_replace(' ','+',$date_of_birth);
	$alt_phone = eregi_replace(' ','+',$alt_phone);
	$email = eregi_replace(' ','+',$email);
	$custom1 = eregi_replace(' ','+',$custom1);
	$custom2 = eregi_replace(' ','+',$custom2);
	$comments = eregi_replace(' ','+',$comments);
	$RGfullname = eregi_replace(' ','+',$RGfullname);
	$RGuser = eregi_replace(' ','+',$RGuser);
	$RGlead_id = eregi_replace(' ','+',$RGlead_id);
	$RGcampaign = eregi_replace(' ','+',$RGcampaign);
	$RGphone_login = eregi_replace(' ','+',$RGphone_login);
	$RGgroup = eregi_replace(' ','+',$RGgroup);
	$RGchannel_group = eregi_replace(' ','+',$RGchannel_group);
	$RGSQLdate = eregi_replace(' ','+',$RGSQLdate);
	$RGepoch = eregi_replace(' ','+',$RGepoch);
	$RGuniqueid = eregi_replace(' ','+',$RGuniqueid);
	$RGcustomer_zap_channel = eregi_replace(' ','+',$RGcustomer_zap_channel);
	$RGserver_ip = eregi_replace(' ','+',$RGserver_ip);
	$RGSIPexten = eregi_replace(' ','+',$RGSIPexten);
	$RGsession_id = eregi_replace(' ','+',$RGsession_id);
	}

# old variable substitution
$script_text = eregi_replace('--A--vendor_lead_code--B--',"$vendor_lead_code",$script_text);
$script_text = eregi_replace('--A--list_id--B--',"$list_id",$script_text);
$script_text = eregi_replace('--A--gmt_offset_now--B--',"$gmt_offset_now",$script_text);
$script_text = eregi_replace('--A--phone_code--B--',"$phone_code",$script_text);
$script_text = eregi_replace('--A--phone_number--B--',"$phone_number",$script_text);
$script_text = eregi_replace('--A--title--B--',"$title",$script_text);
$script_text = eregi_replace('--A--first_name--B--',"$first_name",$script_text);
$script_text = eregi_replace('--A--middle_initial--B--',"$middle_initial",$script_text);
$script_text = eregi_replace('--A--last_name--B--',"$last_name",$script_text);
$script_text = eregi_replace('--A--address1--B--',"$address1",$script_text);
$script_text = eregi_replace('--A--address2--B--',"$address2",$script_text);
$script_text = eregi_replace('--A--address3--B--',"$address3",$script_text);
$script_text = eregi_replace('--A--city--B--',"$city",$script_text);
$script_text = eregi_replace('--A--state--B--',"$state",$script_text);
$script_text = eregi_replace('--A--province--B--',"$province",$script_text);
$script_text = eregi_replace('--A--postal_code--B--',"$postal_code",$script_text);
$script_text = eregi_replace('--A--country_code--B--',"$country_code",$script_text);
$script_text = eregi_replace('--A--gender--B--',"$gender",$script_text);
$script_text = eregi_replace('--A--date_of_birth--B--',"$date_of_birth",$script_text);
$script_text = eregi_replace('--A--alt_phone--B--',"$alt_phone",$script_text);
$script_text = eregi_replace('--A--email--B--',"$email",$script_text);
$script_text = eregi_replace('--A--custom1--B--',"$custom1",$script_text);
$script_text = eregi_replace('--A--custom2--B--',"$custom2",$script_text);
$script_text = eregi_replace('--A--comments--B--',"$comments",$script_text);
$script_text = eregi_replace('--A--fullname--B--',"$RGfullname",$script_text);
$script_text = eregi_replace('--A--fronter--B--',"$RGuser",$script_text);
$script_text = eregi_replace('--A--user--B--',"$RGuser",$script_text);
$script_text = eregi_replace('--A--lead_id--B--',"$RGlead_id",$script_text);
$script_text = eregi_replace('--A--campaign--B--',"$RGcampaign",$script_text);
$script_text = eregi_replace('--A--phone_login--B--',"$RGphone_login",$script_text);
$script_text = eregi_replace('--A--group--B--',"$RGgroup",$script_text);
$script_text = eregi_replace('--A--channel_group--B--',"$RGchannel_group",$script_text);
$script_text = eregi_replace('--A--SQLdate--B--',"$RGSQLdate",$script_text);
$script_text = eregi_replace('--A--epoch--B--',"$RGepoch",$script_text);
$script_text = eregi_replace('--A--uniqueid--B--',"$RGuniqueid",$script_text);
$script_text = eregi_replace('--A--customer_zap_channel--B--',"$RGcustomer_zap_channel",$script_text);
$script_text = eregi_replace('--A--server_ip--B--',"$RGserver_ip",$script_text);
$script_text = eregi_replace('--A--SIPexten--B--',"$RGSIPexten",$script_text);
$script_text = eregi_replace('--A--session_id--B--',"$RGsession_id",$script_text);

#new variable substitution
$script_text = eregi_replace('\[\[vendor_lead_code\]\]',"$vendor_lead_code",$script_text);
$script_text = eregi_replace('\[\[list_id\]\]',"$list_id",$script_text);
$script_text = eregi_replace('\[\[gmt_offset_now\]\]',"$gmt_offset_now",$script_text);
$script_text = eregi_replace('\[\[phone_code\]\]',"$phone_code",$script_text);
$script_text = eregi_replace('\[\[phone_number\]\]',"$phone_number",$script_text);
$script_text = eregi_replace('\[\[title\]\]',"$title",$script_text);
$script_text = eregi_replace('\[\[first_name\]\]',"$first_name",$script_text);
$script_text = eregi_replace('\[\[middle_initial\]\]',"$middle_initial",$script_text);
$script_text = eregi_replace('\[\[last_name\]\]',"$last_name",$script_text);
$script_text = eregi_replace('\[\[address1\]\]',"$address1",$script_text);
$script_text = eregi_replace('\[\[address2\]\]',"$address2",$script_text);
$script_text = eregi_replace('\[\[address3\]\]',"$address3",$script_text);
$script_text = eregi_replace('\[\[city\]\]',"$city",$script_text);
$script_text = eregi_replace('\[\[state\]\]',"$state",$script_text);
$script_text = eregi_replace('\[\[province\]\]',"$province",$script_text);
$script_text = eregi_replace('\[\[postal_code\]\]',"$postal_code",$script_text);
$script_text = eregi_replace('\[\[country_code\]\]',"$country_code",$script_text);
$script_text = eregi_replace('\[\[gender\]\]',"$gender",$script_text);
$script_text = eregi_replace('\[\[date_of_birth\]\]',"$date_of_birth",$script_text);
$script_text = eregi_replace('\[\[alt_phone\]\]',"$alt_phone",$script_text);
$script_text = eregi_replace('\[\[email\]\]',"$email",$script_text);
$script_text = eregi_replace('\[\[custom1\]\]',"$custom1",$script_text);
$script_text = eregi_replace('\[\[custom2\]\]',"$custom2",$script_text);
$script_text = eregi_replace('\[\[comments\]\]',"$comments",$script_text);
$script_text = eregi_replace('\[\[fullname\]\]',"$RGfullname",$script_text);
$script_text = eregi_replace('\[\[fronter\]\]',"$RGuser",$script_text);
$script_text = eregi_replace('\[\[user\]\]',"$RGuser",$script_text);
$script_text = eregi_replace('\[\[lead_id\]\]',"$RGlead_id",$script_text);
$script_text = eregi_replace('\[\[campaign\]\]',"$RGcampaign",$script_text);
$script_text = eregi_replace('\[\[phone_login\]\]',"$RGphone_login",$script_text);
$script_text = eregi_replace('\[\[group\]\]',"$RGgroup",$script_text);
$script_text = eregi_replace('\[\[channel_group\]\]',"$RGchannel_group",$script_text);
$script_text = eregi_replace('\[\[SQLdate\]\]',"$RGSQLdate",$script_text);
$script_text = eregi_replace('\[\[epoch\]\]',"$RGepoch",$script_text);
$script_text = eregi_replace('\[\[uniqueid\]\]',"$RGuniqueid",$script_text);
$script_text = eregi_replace('\[\[customer_zap_channel\]\]',"$RGcustomer_zap_channel",$script_text);
$script_text = eregi_replace('\[\[server_ip\]\]',"$RGserver_ip",$script_text);
$script_text = eregi_replace('\[\[SIPexten\]\]',"$RGSIPexten",$script_text);
$script_text = eregi_replace('\[\[session_id\]\]',"$RGsession_id",$script_text);

$buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='" . $script_id . "'");
foreach ($buttons as $button) {
    $hbutton = "<input type=\"button\" value=\"" . $button['script_button_label'] . "\" onclick=\"$hidebuttons document.getElementById('SCRIPT_" . $button['script_button_id'] . "').style.display='block';\">";
    $script_text = eregi_replace('\{\{' . $button['script_button_id'] . '\}\}',$hbutton,$script_text);
}

$script_text = eregi_replace("\n","",$script_text);

	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

echo "Preview Script: $script_id<BR>\n";
echo "<center>";
echo "<table width=600 cellpadding=10>";
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
	if ($LOGmodify_scripts==1)
	{
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
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
<?
    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
    foreach ($forms as $form) {
	    $fcamps = split(',',$form['campaigns']);
	    foreach ($fcamps as $fcamp) {
            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
            foreach ($fields as $field) {
                echo "      mlbaf.add('" . $form['name'] . '_' . $field['name'] . "','" . $form['name'] . '_' . $field['name'] . "');\n";
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
    theme_advanced_buttons1 : "separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,hr,sub,sup,separator,cut,copy,paste,separator,undo,redo,separator",
    theme_advanced_buttons2 : "separator,fontselect,fontsizeselect,forecolor,backcolor,separator,myfields,myaddtlfields,separator,helpb,separator",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom"
});
</script>

<?

	echo "<center><br><font color=$default_text size=+1>ADD NEW SCRIPT</font><form name=scriptForm action=$PHP_SELF method=POST><br><br>\n";
	echo "<input type=hidden name=ADD value=2111111>\n";
	echo "<TABLE width=$section_width cellspacing=3>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Script ID: </td><td align=left><input type=text name=script_id size=12 maxlength=10> (no spaces or punctuation)$NWB#osdial_scripts-script_id$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Script Name: </td><td align=left><input type=text name=script_name size=40 maxlength=50> (title of the script)$NWB#osdial_scripts-script_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Script Comments: </td><td align=left><input type=text name=script_comments size=50 maxlength=255> $NWB#osdial_scripts-script_comments$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option></select>$NWB#osdial_scripts-active$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=center colspan=2>";

	echo "<TEXTAREA NAME=script_text ROWS=20 COLS=120 value=\"\"></TEXTAREA></td></tr>\n";
	echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></center>\n";
	}
	else
	{
	echo "<font color=red>You do not have permission to view this page</font>\n";
	exit;
	}
}


######################
# ADD=2111111 adds new script to the system
######################

if ($ADD==2111111)
{
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
	$stmt="SELECT count(*) from osdial_scripts where script_id='$script_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br><font color=red>SCRIPT NOT ADDED - there is already a script entry with this name</font>\n";}
	else
		{
		 if ( (strlen($script_id) < 2) or (strlen($script_name) < 2) or (strlen($script_text) < 2) )
			{
			 echo "<br><font color=red>SCRIPT NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Script name, description and text must be at least 2 characters in length</font><br>\n";
			 }
		 else
			{
            $stmt="INSERT INTO osdial_scripts values('" . mysql_real_escape_string($script_id) . "','" . mysql_real_escape_string($script_name) . "','" . mysql_real_escape_string($script_comments) . "','" . mysql_real_escape_string($script_text) . "','" . mysql_real_escape_string($active) . "');";
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=$default_text>SCRIPT ADDED: $script_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0)
				{
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|ADD A NEW SCRIPT ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
				}
			}
		}
$ADD=1000000;
}


######################
# ADD=4111111 modify script in the system
######################

if ($ADD==4111111) {
	if ($LOGmodify_scripts==1) {
	    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

        if ( (strlen($script_id) < 2) or (strlen($script_name) < 2) or (strlen($script_text) < 2) ) {
            echo "<br><font color=red>SCRIPT NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>Script name, description and text must be at least 2 characters in length</font><br>\n";
        } else {
            if ($script_button_id == "") {
                $stype = "SCRIPT";
                $sid = $script_id;
                $stmt="UPDATE osdial_scripts set script_name='" . mysql_real_escape_string($script_name) . "', script_comments='" . mysql_real_escape_string($script_comments) . "', script_text='" . mysql_real_escape_string($script_text) . "', active='" . mysql_real_escape_string($active) . "' where script_id='$script_id';";
            } else {
                $stype = "BUTTON/OBJECTION";
                $sid = $script_id . ":" . $script_button_id;
                $stmt="UPDATE osdial_script_buttons set script_button_label='" . mysql_real_escape_string($script_name) . "', script_button_description='" . mysql_real_escape_string($script_comments) . "', script_button_text='" . mysql_real_escape_string($script_text) . "' where script_id='$script_id' and script_button_id='$script_button_id';";
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
    } else {
	    echo "<font color=red>You do not have permission to view this page</font>\n";
	    exit;
    }
    $ADD=3111111;	# go to script modification form below
}



######################
# ADD=5111111 confirmation before deletion of script record
######################

if ($ADD==5111111) {
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	 if ( (strlen($script_id) < 2) or ($LOGdelete_scripts < 1) ) {
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
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

    $ADD='1000000';		# go to script list
	 if ( (strlen($script_id) < 2) or ($CoNfIrM != 'YES') or ($LOGdelete_scripts < 1) ) {
		 echo "<br><font color=red>SCRIPT NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Script_id be at least 2 characters in length</font><br>\n";
	} else {
        if ($script_button_id == "") {
		    $stmt="DELETE from osdial_scripts where script_id='$script_id' limit 1;";
            $stype = "SCRIPT";
            $sid = $script_id;
        } else {
		    $stmt="DELETE from osdial_script_buttons where script_id='$script_id' and script_button_id='$script_button_id' limit 1;";
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
	if ($LOGmodify_scripts==1)
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
                    title: 'Preview',
                     //image : '/tinymce/plugins/media/img/shockwave.gif',
                     onclick : function() {
                        window.open('/admin/admin.php?ADD=7111111&script_id=<?= $script_id?>','','width=1000,height=700,scrollbars=yes,menubar=yes,address=yes');
                     }
                });
                return previewb;

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
<?
    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'");
    foreach ($forms as $form) {
	    $fcamps = split(',',$form['campaigns']);
	    foreach ($fcamps as $fcamp) {
            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'");
            foreach ($fields as $field) {
                echo "      mlbaf.add('" . $form['name'] . '_' . $field['name'] . "','" . $form['name'] . '_' . $field['name'] . "');\n";
			}
		}
	}
?>

                // Return the new listbox instance
                return mlbaf;

            case 'mybuttons':
                var mlb = cm.createListBox('mybuttons', {
                     title : 'Buttons',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('<b>{{' + v + '}}</b>');
                        tinyMCE.activeEditor.controlManager.get('mybuttons').set(-1);
                         //tinyMCE.activeEditor.windowManager.alert('Value selected:' + v);
                     }
                });

                // Add some values to the list box
<?
    $buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='$script_id'");
    foreach ($buttons as $button) {
        echo "      mlb.add('" . $button['script_button_id'] . ': ' . $button['script_button_label'] . "','" . $button['script_button_id'] . "');\n";
	}
?>

                // Return the new listbox instance
                return mlb;

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
    theme_advanced_buttons1 : "separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,hr,sub,sup,separator,cut,copy,paste,separator,undo,redo,separator",
    theme_advanced_buttons2 : "separator,fontselect,fontsizeselect,forecolor,backcolor,separator,myfields,myaddtlfields,mybuttons,separator,helpb,separator,previewb,separator",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom"
});
</script>

<?
	echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

    if ($SUB == "--NEW--") {
        $stmt="SELECT count(*) from osdial_script_buttons where script_id='$script_id' and script_button_id='$script_button_id';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>BUTTON / OBJECTION NOT ADDED - there is already a button entry with this name</font>\n";
        } else {
            if ( (strlen($script_button_id) < 2) or (strlen($script_button_label) < 2) or (strlen($script_button_description) < 2) or ($script_button_id == 'MAIN')) {
                echo "<br><font color=red>BUTTON / OBJECTION NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>Button/Objection name, description and text must be at least 2 characters in length</font><br>\n";
            } else {
                $stmt="INSERT INTO osdial_script_buttons values('" . mysql_real_escape_string($script_id) . "','" . mysql_real_escape_string($script_button_id) . "','" . mysql_real_escape_string($script_button_description) . "','" . mysql_real_escape_string($script_button_label) . "','');";
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

	$stmt="SELECT * from osdial_scripts where script_id='$script_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$script_name =		$row[1];
	$script_comments =	$row[2];
	$script_text =		$row[3];
	$active =			$row[4];

    if ($SUB != "") {
        $stmt="SELECT * from osdial_script_buttons where script_id='$script_id' and script_button_id='$SUB';";
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

    $script_text = eregi_replace("\n","",$script_text);

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	echo "<center><br><font color=$default_text size=+1>MODIFY A $stypec</font><form name=scriptForm action=$PHP_SELF method=POST><br>\n";
	echo "<input type=hidden name=ADD value=4111111>\n";
	echo "<input type=hidden name=script_id value=\"$script_id\">\n";
    if ($SUB != "") {
	    echo "<input type=hidden name=script_button_id value=\"$script_button_id\">\n";
		echo "<center><a href=\"$PHP_SELF?ADD=$ADD&script_id=$script_id\">BACK TO SCRIPT: $script_id</a></center><br>\n";
    }
	echo "<TABLE width=$section_width>";
	echo "<tr bgcolor=$oddrows><td align=right>$id_label: </td><td align=left><B>$sid</B>$NWB#osdial_scripts-script_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>$name_label: </td><td align=left><input type=text name=script_name size=40 maxlength=50 value=\"$script_name\">$NWB#osdial_scripts-script_name$NWE</td></tr>\n";
	echo "<tr bgcolor=$oddrows><td align=right>$comment_label: </td><td align=left><input type=text name=script_comments size=50 maxlength=255 value=\"$script_comments\"> $NWB#osdial_scripts-script_comments$NWE</td></tr>\n";
    if ($SUB == "") {
	    echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option><option selected>$active</option></select>$NWB#osdial_scripts-active$NWE</td></tr>\n";
    }
	echo "<tr bgcolor=$oddrows><td align=center colspan=2>";

	echo "<TEXTAREA NAME=script_text ROWS=20 COLS=120>$script_text</TEXTAREA></td></tr>\n";
	echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
	echo "</TABLE></form></center>\n";

	if ($LOGdelete_scripts > 0) {
		echo "<center><a href=\"$PHP_SELF?ADD=5111111&script_id=$script_id";
        if ($SUB != "") {
		    echo "&script_button_id=$script_button_id";
        }
		echo "\">DELETE THIS $stypec</a></center>\n";
	}


    # Sub-scripts / Buttons.
    if ($SUB == "") {
        echo "<br /><br /><hr width=50%>\n";
        echo "<center><font color=$default_text size=+1>BUTTONS / OBJECTIONS & REBUTTALS</font><br><br>\n";
        echo "<table bgcolor=grey width=$section_width cellspacing=1 cellpadding=1>\n";
        echo "  <tr class=tabheader>\n";
        echo "      <td align=center>ID</td>\n";
        echo "      <td align=center>BUTTON LABEL</td>\n";
        echo "      <td align=center>DESCRIPTION</td>\n";
        echo "      <td align=center>ACTION</td>\n";
        echo "  </tr>\n";
        $buttons = get_krh($link, 'osdial_script_buttons', 'script_button_id,script_id,script_button_description,script_button_label,script_button_text', 'script_button_id', "script_id='" . $script_id . "'");
        $cnt = 0;
        foreach ($buttons as $button) {
            if (eregi("1$|3$|5$|7$|9$",$cnt))
                {$bgcolor='bgcolor='.$oddrows;}
            else
                {$bgcolor='bgcolor='.$evenrows;}
    
            echo '  <form action="' . $PHP_SELF . '" method="POST">';
            echo '  <input type="hidden" name="ADD" value="' . $ADD . '">';
            echo '  <input type="hidden" name="SUB" value="' . $button['script_button_id'] . '">';
	        echo "  <input type=hidden name=script_id value=\"$script_id\">\n";
            echo "  <tr $bgcolor class=\"row font1\">\n";
            echo "      <td align=center>" . $button['script_button_id'] . "</td>\n";
            echo "      <td align=center>" . $button['script_button_label'] . "</td>\n";
            echo "      <td align=center>" . $button['script_button_description'] . "</td>\n";
            echo "      <td align=center class=tabbutton1><input type=submit value=\"Edit\"></td>\n";
            echo "  </tr>\n";
            echo "  </form>\n";
            $cnt++;
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
	exit;
	}
}


######################
# ADD=1000000 display all scripts
######################
if ($ADD==1000000)
{
echo "<TABLE align=center><TR><TD>\n";
	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

	$stmt="SELECT * from osdial_scripts order by script_id";
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font color=$default_text size=+1>SCRIPTS</font><br><br>\n";
echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>";
echo "    <td>NAME</td>\n";
echo "    <td>DESCRIPTION</td>\n";
echo "    <td align=center>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $o))
			{$bgcolor='bgcolor='.$oddrows;} 
		else
			{$bgcolor='bgcolor='.$evenrows;}
		echo "  <tr $bgcolor class=\"row font1\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=3111111&script_id=$row[0]\">$row[0]</a></td>\n";
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
