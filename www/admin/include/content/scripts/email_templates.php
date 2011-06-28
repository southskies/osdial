<?php

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



######################
# ADD=7email view sample email template with test variables
######################

if ($ADD=="7email") {
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
    $phone_code = '1';
    $phone_number = '7275551212';
    $alt_phone = '3125551212';
    $email = 'test@test.com';
    $date_of_birth = '1970-01-01';
    $gender = 'M';
    $post_date = date("Y-m-d");
    $vendor_lead_code = 'GOLDLEADS';
    $comments = 'COMMENTS FIELD';
    $custom1 = 'custom1';
    $custom2 = 'custom2';

    $et = get_first_record($link, 'osdial_email_templates', '*', sprintf("et_id='%s'",mres($et_id)) );
    $et_text = $et['et_body_html'] . "<br /><br /><hr /><br /><br />" . $et['et_body_html'];

    if (preg_match("/iframe src/i",$et_text)) {
        $vendor_lead_code = preg_replace('/ /','+',$vendor_lead_code);
        $list_id = preg_replace('/ /','+',$list_id);
        $gmt_offset_now = preg_replace('/ /','+',$gmt_offset_now);
        $phone_code = preg_replace('/ /','+',$phone_code);
        $phone_number = preg_replace('/ /','+',$phone_number);
        $title = preg_replace('/ /','+',$title);
        $first_name = preg_replace('/ /','+',$first_name);
        $middle_initial = preg_replace('/ /','+',$middle_initial);
        $last_name = preg_replace('/ /','+',$last_name);
        $address1 = preg_replace('/ /','+',$address1);
        $address2 = preg_replace('/ /','+',$address2);
        $address3 = preg_replace('/ /','+',$address2);
        $city = preg_replace('/ /','+',$city);
        $state = preg_replace('/ /','+',$state);
        $province = preg_replace('/ /','+',$province);
        $postal_code = preg_replace('/ /','+',$postal_code);
        $country_code = preg_replace('/ /','+',$country_code);
        $gender = preg_replace('/ /','+',$gender);
        $date_of_birth = preg_replace('/ /','+',$date_of_birth);
        $alt_phone = preg_replace('/ /','+',$alt_phone);
        $email = preg_replace('/ /','+',$email);
        $custom1 = preg_replace('/ /','+',$custom1);
        $custom2 = preg_replace('/ /','+',$custom2);
        $comments = preg_replace('/ /','+',$comments);
        $fullname = preg_replace('/ /','+',$fullname);
        $user = preg_replace('/ /','+',$user);
        $pass = preg_replace('/ /','+',$pass);
        $lead_id = preg_replace('/ /','+',$lead_id);
        $campaign = preg_replace('/ /','+',$campaign);
        $phone_login = preg_replace('/ /','+',$phone_login);
        $group = preg_replace('/ /','+',$group);
        $channel_group = preg_replace('/ /','+',$channel_group);
        $SQLdate = preg_replace('/ /','+',$SQLdate);
        $epoch = preg_replace('/ /','+',$epoch);
        $uniqueid = preg_replace('/ /','+',$uniqueid);
        $customer_zap_channel = preg_replace('/ /','+',$customer_zap_channel);
        $server_ip = preg_replace('/ /','+',$server_ip);
        $SIPexten = preg_replace('/ /','+',$SIPexten);
        $session_id = preg_replace('/ /','+',$session_id);
    }

    $et_text = preg_replace('/\[\[list_id\]\]/',             $list_id,             $et_text);
    $et_text = preg_replace('/\[\[gmt_offset_now\]\]/',      $gmt_offset_now,      $et_text);
    $et_text = preg_replace('/\[\[fullname\]\]/',            $fullname,            $et_text);
    $et_text = preg_replace('/\[\[fronter\]\]/',             $fronter,             $et_text);
    $et_text = preg_replace('/\[\[user\]\]/',                $user,                $et_text);
    $et_text = preg_replace('/\[\[pass\]\]/',                $pass,                $et_text);
    $et_text = preg_replace('/\[\[lead_id\]\]/',             $lead_id,             $et_text);
    $et_text = preg_replace('/\[\[campaign\]\]/',            $campaign,            $et_text);
    $et_text = preg_replace('/\[\[phone_login\]\]/',         $phone_login,         $et_text);
    $et_text = preg_replace('/\[\[group\]\]/',               $group,               $et_text);
    $et_text = preg_replace('/\[\[channel_group\]\]/',       $channel_group,       $et_text);
    $et_text = preg_replace('/\[\[SQLdate\]\]/',             $SQLdate,             $et_text);
    $et_text = preg_replace('/\[\[epoch\]\]/',               $epoch,               $et_text);
    $et_text = preg_replace('/\[\[uniqueid\]\]/',            $uniqueid,            $et_text);
    $et_text = preg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$et_text);
    $et_text = preg_replace('/\[\[server_ip\]\]/',           $server_ip,           $et_text);
    $et_text = preg_replace('/\[\[SIPexten\]\]/',            $SIPexten,            $et_text);
    $et_text = preg_replace('/\[\[session_id\]\]/',          $session_id,          $et_text);

    $et_text = preg_replace('/\[\[title\]\]/',               $title,               $et_text);
    $et_text = preg_replace('/\[\[first_name\]\]/',          $first_name,          $et_text);
    $et_text = preg_replace('/\[\[middle_initial\]\]/',      $middle_initial,      $et_text);
    $et_text = preg_replace('/\[\[last_name\]\]/',           $last_name,           $et_text);
    $et_text = preg_replace('/\[\[address1\]\]/',            $address1,            $et_text);
    $et_text = preg_replace('/\[\[address2\]\]/',            $address2,            $et_text);
    $et_text = preg_replace('/\[\[address3\]\]/',            $address3,            $et_text);
    $et_text = preg_replace('/\[\[city\]\]/',                $city,                $et_text);
    $et_text = preg_replace('/\[\[state\]\]/',               $state,               $et_text);
    $et_text = preg_replace('/\[\[province\]\]/',            $province,            $et_text);
    $et_text = preg_replace('/\[\[postal_code\]\]/',         $postal_code,         $et_text);
    $et_text = preg_replace('/\[\[country_code\]\]/',        $country_code,        $et_text);
    $et_text = preg_replace('/\[\[phone_code\]\]/',          $phone_code,          $et_text);
    $et_text = preg_replace('/\[\[phone_number\]\]/',        $phone_number,        $et_text);
    $et_text = preg_replace('/\[\[alt_phone\]\]/',           $alt_phone,           $et_text);
    $et_text = preg_replace('/\[\[email\]\]/',               $email,               $et_text);
    $et_text = preg_replace('/\[\[gender\]\]/',              $gender,              $et_text);
    $et_text = preg_replace('/\[\[date_of_birth\]\]/',       $date_of_birth,       $et_text);
    $et_text = preg_replace('/\[\[post_date\]\]/',           $post_date,           $et_text);
    $et_text = preg_replace('/\[\[vendor_lead_code\]\]/',    $vendor_lead_code,    $et_text);
    $et_text = preg_replace('/\[\[comments\]\]/',            $comments,            $et_text);
    $et_text = preg_replace('/\[\[custom1\]\]/',             $custom1,             $et_text);
    $et_text = preg_replace('/\[\[custom2\]\]/',             $custom2,             $et_text);

    $et_text = preg_replace("/\n/","",$et_text);

    echo "<table align=center><tr><td>\n";

    echo "Preview Email: $et[et_id]<br>\n";
    echo "<center>\n";
    echo "<table width=600 cellpadding=10>\n";
    echo "  <tr bgcolor=$oddrows>\n";
    echo "    <td>\n";
    echo "      <center><b>$et[et_name]</b></center>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "  <tr bgcolor=$evenrows>\n";
    echo "    <td>\n";
    echo "$et_text\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "</center>\n";

    echo "</td></tr></table>\n";

}



######################
# ADD=1email display the ADD NEW EMAIL TEMPLATE SCREEN
######################

if ($ADD=="1email") {
    if ($LOG['modify_scripts']==1) {
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
                        window.open('/admin/admin.php?ADD=99999#osdial_email_templates-et_body','','width=800,height=500,scrollbars=yes,menubar=yes,address=yes');
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
    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'",'');
    foreach ($forms as $form) {
        $fcamps = preg_split('/,/',$form['campaigns']);
        foreach ($fcamps as $fcamp) {
            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'",'');
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
    mode : "specific_textareas",
    editor_selector : "mceEditor",
    theme : "advanced",
    theme_advanced_buttons1 : "separator,fontselect,fontsizeselect,forecolor,backcolor,separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,hr,sub,sup,separator,cut,copy,paste,separator,undo,redo,separator",
    theme_advanced_buttons2 : "separator,myfields,separator,myaddtlfields,separator,helpb,separator",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "center",
    theme_advanced_statusbar_location : "bottom"
});
</script>

<?

        echo "<center><br>\n";
        echo "<font color=$default_text size=+1>ADD NEW EMAIL TEMPLATE</font><br><br>\n";
        echo "<form name=etform action=$PHP_SELF method=post>\n";
        echo "<input type=hidden name=ADD value=2email>\n";
        echo "<table width=$section_width cellspacing=3>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Template ID:</td>\n";
        echo "    <td align=left>\n";
        if ($LOG['multicomp_admin'] > 0) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "      <select name=company_id>\n";
            foreach ($comps as $comp) {
                echo "        <option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "      </select>\n";
        } elseif ($LOG['multicomp']>0) {
            echo "      <input type=hidden name=company_id value=$LOG[company_id]>\n";
        }
        echo "      <input type=text name=et_id size=12 maxlength=10> (no spaces or punctuation)\n";
        echo "      $NWB#osdial_email_templates-et_id$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Name:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_name size=40 maxlength=50> (title of the template)\n";
        echo "      $NWB#osdial_email_templates-et_name$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Comments:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_comments size=50 maxlength=255>\n";
        echo "      $NWB#osdial_email_templates-et_comments$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Active:</td>\n";
        echo "    <td align=left>\n";
        echo "      <select size=1 name=active>\n";
        echo "        <option selected>Y</option>\n";
        echo "        <option>N</option>\n";
        echo "      </select>\n";
        echo "      $NWB#osdial_email_templates-active$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>SMTP Host / Port:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_host size=40 maxlength=255 value=\"localhost\"> / <input type=text name=et_port size=5 maxlength=5 value=\"25\">\n";
        echo "      $NWB#osdial_email_templates-et_host$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>SMTP User / Pass:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_user size=20 maxlength=255> / <input type=text name=et_pass size=20 maxlength=255>\n";
        echo "      $NWB#osdial_email_templates-et_user$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>From:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_from size=50 maxlength=255 value='\"John Doe\" <johndoe@gmail.com>'>\n";
        echo "      $NWB#osdial_email_templates-et_from$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Subject:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_subject size=50 maxlength=255>\n";
        echo "      $NWB#osdial_email_templates-et_subject$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        #echo "  <tr bgcolor=$oddrows>\n";
        #echo "    <td align=center colspan=2>\n";
        #echo "      <br>HTML Body:<br>";
        #echo "      <textarea name=et_body_html class=mceEditor rows=20 cols=120></textarea>\n";
        #echo "    </td>\n";
        #echo "  </tr>\n";

        #echo "  <tr bgcolor=$oddrows>\n";
        #echo "    <td align=center colspan=2>\n";
        #echo "      <br>Text Body:<br>";
        #echo "      <textarea name=et_body_text rows=20 cols=70></textarea>\n";
        #echo "    </td>\n";
        #echo "  </tr>\n";

        echo "  <tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "</table>\n";
        echo "</form>\n";
        echo "</center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=2email adds new email template to the system
######################

if ($ADD=="2email") {
    if ($LOG['modify_scripts']==1) {
        $preet_id = $et_id;
        if ($LOG['multicomp'] > 0) $preet_id = (($company_id * 1) + 100) . $et_id;
        $stmt="SELECT count(*) from osdial_email_templates where et_id='$preet_id';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>TEMPLATE NOT ADDED - there is already an email template with this name</font>\n";
        } else {
            if (strlen($et_id) < 2 or strlen($et_name) < 2) {
                echo "<br><font color=red>TEMPLATE NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>Template name and HTML body must be at least 2 characters in length</font><br>\n";
            } else {
                if ($LOG['multicomp'] > 0) $et_id = (($company_id * 1) + 100) . $et_id;
                $stmt=sprintf("INSERT INTO osdial_email_templates SET et_id='%s',et_name='%s',et_comments='%s',et_host='%s',et_port='%s',et_user='%s',et_pass='%s',et_from='%s',et_subject='%s',et_body_html='%s',et_body_text='%s',active='%s',et_send_action='ONDEMAND';",mres($et_id),mres($et_name),mres($et_comments),mres($et_host),mres($et_port),mres($et_user),mres($et_pass),mres($et_from),mres($et_subject),mres($et_body_html),mres($et_body_text),mres($active));
                $rslt=mysql_query($stmt, $link);

                echo "<br><b><font color=$default_text>TEMPLATE ADDED: $et_id</font></b>\n";

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW TEMPLATE ENTRY         |$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }

                if ($LOG['allowed_email_templatesALL']==0) {
                    $LOG['allowed_email_templates'][] = $et_id;
                    $email_templates_value = ' ' . implode(' ', $LOG['allowed_email_templates']) . ' -';
                    $stmt = sprintf("UPDATE osdial_user_groups SET allowed_email_templates='%s' WHERE user_group='%s';",mres($email_templates_value),$LOG['user_group']);
                    $rslt=mysql_query($stmt, $link);
                }

            }
        }
        $ADD="0email";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=4email modify email_template in the system
######################

if ($ADD=="4email") {
    if ($LOG['modify_scripts']==1) {

        if (strlen($et_id) < 2 or strlen($et_name) < 2 or strlen($et_body_html) < 2) {
            echo "<br><font color=red>TEMPLATE NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>Template name and HTML body must be at least 2 characters in length</font><br>\n";
        } else {
            if ($et_port=='') $et_port='25';
            $stmt=sprintf("UPDATE osdial_email_templates SET et_name='%s',et_comments='%s',et_host='%s',et_port='%s',et_user='%s',et_pass='%s',et_from='%s',et_subject='%s',et_body_html='%s',et_body_text='%s',active='%s',et_send_action='%s' WHERE et_id='%s';",mres($et_name),mres($et_comments),mres($et_host),mres($et_port),mres($et_user),mres($et_pass),mres($et_from),mres($et_subject),mres($et_body_html),mres($et_body_text),mres($active),mres($et_send_action),mres($et_id));
            $rslt=mysql_query($stmt, $link);

            echo "<br><b><font color=$default_text>TEMPLATE MODIFIED</font></b>\n";

            if ($email) {
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
                $phone_code = '1';
                $phone_number = '7275551212';
                $alt_phone = '3125551212';
                $email_to = 'test@test.com';
                $date_of_birth = '1970-01-01';
                $gender = 'M';
                $post_date = date("Y-m-d");
                $vendor_lead_code = 'GOLDLEADS';
                $comments = 'COMMENTS FIELD';
                $custom1 = 'custom1';
                $custom2 = 'custom2';

                $et_subject   = preg_replace('/\[\[list_id\]\]/',             $list_id,             $et_subject);
                $et_subject   = preg_replace('/\[\[gmt_offset_now\]\]/',      $gmt_offset_now,      $et_subject);
                $et_subject   = preg_replace('/\[\[fullname\]\]/',            $fullname,            $et_subject);
                $et_subject   = preg_replace('/\[\[fronter\]\]/',             $fronter,             $et_subject);
                $et_subject   = preg_replace('/\[\[user\]\]/',                $user,                $et_subject);
                $et_subject   = preg_replace('/\[\[pass\]\]/',                $pass,                $et_subject);
                $et_subject   = preg_replace('/\[\[lead_id\]\]/',             $lead_id,             $et_subject);
                $et_subject   = preg_replace('/\[\[campaign\]\]/',            $campaign,            $et_subject);
                $et_subject   = preg_replace('/\[\[phone_login\]\]/',         $phone_login,         $et_subject);
                $et_subject   = preg_replace('/\[\[group\]\]/',               $group,               $et_subject);
                $et_subject   = preg_replace('/\[\[channel_group\]\]/',       $channel_group,       $et_subject);
                $et_subject   = preg_replace('/\[\[SQLdate\]\]/',             $SQLdate,             $et_subject);
                $et_subject   = preg_replace('/\[\[epoch\]\]/',               $epoch,               $et_subject);
                $et_subject   = preg_replace('/\[\[uniqueid\]\]/',            $uniqueid,            $et_subject);
                $et_subject   = preg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$et_subject);
                $et_subject   = preg_replace('/\[\[server_ip\]\]/',           $server_ip,           $et_subject);
                $et_subject   = preg_replace('/\[\[SIPexten\]\]/',            $SIPexten,            $et_subject);
                $et_subject   = preg_replace('/\[\[session_id\]\]/',          $session_id,          $et_subject);
                $et_subject   = preg_replace('/\[\[title\]\]/',               $title,               $et_subject);
                $et_subject   = preg_replace('/\[\[first_name\]\]/',          $first_name,          $et_subject);
                $et_subject   = preg_replace('/\[\[middle_initial\]\]/',      $middle_initial,      $et_subject);
                $et_subject   = preg_replace('/\[\[last_name\]\]/',           $last_name,           $et_subject);
                $et_subject   = preg_replace('/\[\[address1\]\]/',            $address1,            $et_subject);
                $et_subject   = preg_replace('/\[\[address2\]\]/',            $address2,            $et_subject);
                $et_subject   = preg_replace('/\[\[address3\]\]/',            $address3,            $et_subject);
                $et_subject   = preg_replace('/\[\[city\]\]/',                $city,                $et_subject);
                $et_subject   = preg_replace('/\[\[state\]\]/',               $state,               $et_subject);
                $et_subject   = preg_replace('/\[\[province\]\]/',            $province,            $et_subject);
                $et_subject   = preg_replace('/\[\[postal_code\]\]/',         $postal_code,         $et_subject);
                $et_subject   = preg_replace('/\[\[country_code\]\]/',        $country_code,        $et_subject);
                $et_subject   = preg_replace('/\[\[phone_code\]\]/',          $phone_code,          $et_subject);
                $et_subject   = preg_replace('/\[\[phone_number\]\]/',        $phone_number,        $et_subject);
                $et_subject   = preg_replace('/\[\[alt_phone\]\]/',           $alt_phone,           $et_subject);
                $et_subject   = preg_replace('/\[\[email\]\]/',               $email,               $et_subject);
                $et_subject   = preg_replace('/\[\[gender\]\]/',              $gender,              $et_subject);
                $et_subject   = preg_replace('/\[\[date_of_birth\]\]/',       $date_of_birth,       $et_subject);
                $et_subject   = preg_replace('/\[\[post_date\]\]/',           $post_date,           $et_subject);
                $et_subject   = preg_replace('/\[\[vendor_lead_code\]\]/',    $vendor_lead_code,    $et_subject);
                $et_subject   = preg_replace('/\[\[comments\]\]/',            $comments,            $et_subject);
                $et_subject   = preg_replace('/\[\[custom1\]\]/',             $custom1,             $et_subject);
                $et_subject   = preg_replace('/\[\[custom2\]\]/',             $custom2,             $et_subject);

                $et_body_html = preg_replace('/\[\[list_id\]\]/',             $list_id,             $et_body_html);
                $et_body_html = preg_replace('/\[\[gmt_offset_now\]\]/',      $gmt_offset_now,      $et_body_html);
                $et_body_html = preg_replace('/\[\[fullname\]\]/',            $fullname,            $et_body_html);
                $et_body_html = preg_replace('/\[\[fronter\]\]/',             $fronter,             $et_body_html);
                $et_body_html = preg_replace('/\[\[user\]\]/',                $user,                $et_body_html);
                $et_body_html = preg_replace('/\[\[pass\]\]/',                $pass,                $et_body_html);
                $et_body_html = preg_replace('/\[\[lead_id\]\]/',             $lead_id,             $et_body_html);
                $et_body_html = preg_replace('/\[\[campaign\]\]/',            $campaign,            $et_body_html);
                $et_body_html = preg_replace('/\[\[phone_login\]\]/',         $phone_login,         $et_body_html);
                $et_body_html = preg_replace('/\[\[group\]\]/',               $group,               $et_body_html);
                $et_body_html = preg_replace('/\[\[channel_group\]\]/',       $channel_group,       $et_body_html);
                $et_body_html = preg_replace('/\[\[SQLdate\]\]/',             $SQLdate,             $et_body_html);
                $et_body_html = preg_replace('/\[\[epoch\]\]/',               $epoch,               $et_body_html);
                $et_body_html = preg_replace('/\[\[uniqueid\]\]/',            $uniqueid,            $et_body_html);
                $et_body_html = preg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$et_body_html);
                $et_body_html = preg_replace('/\[\[server_ip\]\]/',           $server_ip,           $et_body_html);
                $et_body_html = preg_replace('/\[\[SIPexten\]\]/',            $SIPexten,            $et_body_html);
                $et_body_html = preg_replace('/\[\[session_id\]\]/',          $session_id,          $et_body_html);
                $et_body_html = preg_replace('/\[\[title\]\]/',               $title,               $et_body_html);
                $et_body_html = preg_replace('/\[\[first_name\]\]/',          $first_name,          $et_body_html);
                $et_body_html = preg_replace('/\[\[middle_initial\]\]/',      $middle_initial,      $et_body_html);
                $et_body_html = preg_replace('/\[\[last_name\]\]/',           $last_name,           $et_body_html);
                $et_body_html = preg_replace('/\[\[address1\]\]/',            $address1,            $et_body_html);
                $et_body_html = preg_replace('/\[\[address2\]\]/',            $address2,            $et_body_html);
                $et_body_html = preg_replace('/\[\[address3\]\]/',            $address3,            $et_body_html);
                $et_body_html = preg_replace('/\[\[city\]\]/',                $city,                $et_body_html);
                $et_body_html = preg_replace('/\[\[state\]\]/',               $state,               $et_body_html);
                $et_body_html = preg_replace('/\[\[province\]\]/',            $province,            $et_body_html);
                $et_body_html = preg_replace('/\[\[postal_code\]\]/',         $postal_code,         $et_body_html);
                $et_body_html = preg_replace('/\[\[country_code\]\]/',        $country_code,        $et_body_html);
                $et_body_html = preg_replace('/\[\[phone_code\]\]/',          $phone_code,          $et_body_html);
                $et_body_html = preg_replace('/\[\[phone_number\]\]/',        $phone_number,        $et_body_html);
                $et_body_html = preg_replace('/\[\[alt_phone\]\]/',           $alt_phone,           $et_body_html);
                $et_body_html = preg_replace('/\[\[email\]\]/',               $email,               $et_body_html);
                $et_body_html = preg_replace('/\[\[gender\]\]/',              $gender,              $et_body_html);
                $et_body_html = preg_replace('/\[\[date_of_birth\]\]/',       $date_of_birth,       $et_body_html);
                $et_body_html = preg_replace('/\[\[post_date\]\]/',           $post_date,           $et_body_html);
                $et_body_html = preg_replace('/\[\[vendor_lead_code\]\]/',    $vendor_lead_code,    $et_body_html);
                $et_body_html = preg_replace('/\[\[comments\]\]/',            $comments,            $et_body_html);
                $et_body_html = preg_replace('/\[\[custom1\]\]/',             $custom1,             $et_body_html);
                $et_body_html = preg_replace('/\[\[custom2\]\]/',             $custom2,             $et_body_html);

                $et_body_text = preg_replace('/\[\[list_id\]\]/',             $list_id,             $et_body_text);
                $et_body_text = preg_replace('/\[\[gmt_offset_now\]\]/',      $gmt_offset_now,      $et_body_text);
                $et_body_text = preg_replace('/\[\[fullname\]\]/',            $fullname,            $et_body_text);
                $et_body_text = preg_replace('/\[\[fronter\]\]/',             $fronter,             $et_body_text);
                $et_body_text = preg_replace('/\[\[user\]\]/',                $user,                $et_body_text);
                $et_body_text = preg_replace('/\[\[pass\]\]/',                $pass,                $et_body_text);
                $et_body_text = preg_replace('/\[\[lead_id\]\]/',             $lead_id,             $et_body_text);
                $et_body_text = preg_replace('/\[\[campaign\]\]/',            $campaign,            $et_body_text);
                $et_body_text = preg_replace('/\[\[phone_login\]\]/',         $phone_login,         $et_body_text);
                $et_body_text = preg_replace('/\[\[group\]\]/',               $group,               $et_body_text);
                $et_body_text = preg_replace('/\[\[channel_group\]\]/',       $channel_group,       $et_body_text);
                $et_body_text = preg_replace('/\[\[SQLdate\]\]/',             $SQLdate,             $et_body_text);
                $et_body_text = preg_replace('/\[\[epoch\]\]/',               $epoch,               $et_body_text);
                $et_body_text = preg_replace('/\[\[uniqueid\]\]/',            $uniqueid,            $et_body_text);
                $et_body_text = preg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$et_body_text);
                $et_body_text = preg_replace('/\[\[server_ip\]\]/',           $server_ip,           $et_body_text);
                $et_body_text = preg_replace('/\[\[SIPexten\]\]/',            $SIPexten,            $et_body_text);
                $et_body_text = preg_replace('/\[\[session_id\]\]/',          $session_id,          $et_body_text);
                $et_body_text = preg_replace('/\[\[title\]\]/',               $title,               $et_body_text);
                $et_body_text = preg_replace('/\[\[first_name\]\]/',          $first_name,          $et_body_text);
                $et_body_text = preg_replace('/\[\[middle_initial\]\]/',      $middle_initial,      $et_body_text);
                $et_body_text = preg_replace('/\[\[last_name\]\]/',           $last_name,           $et_body_text);
                $et_body_text = preg_replace('/\[\[address1\]\]/',            $address1,            $et_body_text);
                $et_body_text = preg_replace('/\[\[address2\]\]/',            $address2,            $et_body_text);
                $et_body_text = preg_replace('/\[\[address3\]\]/',            $address3,            $et_body_text);
                $et_body_text = preg_replace('/\[\[city\]\]/',                $city,                $et_body_text);
                $et_body_text = preg_replace('/\[\[state\]\]/',               $state,               $et_body_text);
                $et_body_text = preg_replace('/\[\[province\]\]/',            $province,            $et_body_text);
                $et_body_text = preg_replace('/\[\[postal_code\]\]/',         $postal_code,         $et_body_text);
                $et_body_text = preg_replace('/\[\[country_code\]\]/',        $country_code,        $et_body_text);
                $et_body_text = preg_replace('/\[\[phone_code\]\]/',          $phone_code,          $et_body_text);
                $et_body_text = preg_replace('/\[\[phone_number\]\]/',        $phone_number,        $et_body_text);
                $et_body_text = preg_replace('/\[\[alt_phone\]\]/',           $alt_phone,           $et_body_text);
                $et_body_text = preg_replace('/\[\[email\]\]/',               $email,               $et_body_text);
                $et_body_text = preg_replace('/\[\[gender\]\]/',              $gender,              $et_body_text);
                $et_body_text = preg_replace('/\[\[date_of_birth\]\]/',       $date_of_birth,       $et_body_text);
                $et_body_text = preg_replace('/\[\[post_date\]\]/',           $post_date,           $et_body_text);
                $et_body_text = preg_replace('/\[\[vendor_lead_code\]\]/',    $vendor_lead_code,    $et_body_text);
                $et_body_text = preg_replace('/\[\[comments\]\]/',            $comments,            $et_body_text);
                $et_body_text = preg_replace('/\[\[custom1\]\]/',             $custom1,             $et_body_text);
                $et_body_text = preg_replace('/\[\[custom2\]\]/',             $custom2,             $et_body_text);

                send_email($et_host, $et_port, $et_user, $et_pass, $email, $et_from, $et_subject, $et_body_html, $et_body_text);
                echo "<br><b><font color=$default_text>TEST EMAIL TEMPLATE SENT</font></b>\n";
            }

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY TEMPLATE ENTRY         |$PHP_AUTH_USER|$ip|$et_id|\n");
                fclose($fp);
            }
        }
        $ADD="3email";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=5email confirmation before deletion of email template record
######################

if ($ADD=="5email") {
    if ($LOG['delete_scripts']==1) {

        if (strlen($et_id) < 2) {
            echo "<br><font color=red>TEMPLATE NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>et_id must be at least 2 characters in length</font>\n";
        } else {
            echo "<br><b><font color=$default_text>TEMPLATE DELETION CONFIRMATION: $et_id</b>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=6email&et_id=$et_id&CoNfIrM=YES\">Click here to delete template $et_id</a></font><br><br><br>\n";
        }
        $ADD="3email";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=6email delete email template record
######################

if ($ADD=="6email") {
    if ($LOG['delete_scripts']==1) {

        if (strlen($et_id) < 2 or $CoNfIrM != 'YES') {
            echo "<br><font color=red>TEMPLATE NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>et_id be at least 2 characters in length</font><br>\n";
        } else {
            $stmt="DELETE FROM osdial_email_templates WHERE et_id='$et_id' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|!DELETING TEMPLATE!!!!|$PHP_AUTH_USER|$ip|$et_id|\n");
                fclose($fp);
            }
            echo "<br><b><font color=$default_text>TEMPLATE DELETION COMPLETED: $et_id</font></b><br><br>\n";
        }
        $ADD="0email";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=3email modify email template
######################

if ($ADD=="3email") {
    if ($LOG['modify_scripts']==1) {

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
                        window.open('/admin/admin.php?ADD=99999#osdial_email_templates-email_body','','width=800,height=500,scrollbars=yes,menubar=yes,address=yes');
                     }
                });
                return helpb;

            case 'previewb':
                var previewb = cm.createButton('previewb', {
                    label: 'Preview',
                    onclick : function() {
                        window.open('/admin/admin.php?ADD=7email&et_id=<?= $et_id?>','','width=1000,height=700,scrollbars=yes,menubar=yes,address=yes');
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
<?
    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'",'');
    foreach ($forms as $form) {
        $fcamps = preg_split('/,/',$form['campaigns']);
        foreach ($fcamps as $fcamp) {
            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'",'');
            foreach ($fields as $field) {
                echo "      mlbaf.add('" . $form['name'] . '_' . $field['name'] . "','" . $form['name'] . '_' . $field['name'] . "');\n";
            }
        }
    }
?>
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
    mode : "specific_textareas",
    editor_selector : "mceEditor",
    theme : "advanced",
    theme_advanced_buttons1 : "separator,fontselect,fontsizeselect,forecolor,backcolor,separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,hr,sub,sup,separator,cut,copy,paste,separator,undo,redo,separator,code,separator",
    theme_advanced_buttons2 : "separator,myfields,separator,separator,separator,myaddtlfields,separator,s0,separator,separator,s1,separator,previewb,separator,separator,helpb,separator",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "center",
    theme_advanced_statusbar_location : "bottom"
});
</script>

<?
        $et = get_first_record($link, 'osdial_email_templates', '*', sprintf("et_id='%s'",mres($et_id)) );
        $rslt=mysql_query($stmt, $link);

        $et['et_body_html'] = preg_replace("/\n/","",$et['et_body_html']);


        echo "<center>\n";
        echo "<br><font color=$default_text size=+1>MODIFY AN EMAIL TEMPLATE</font><br>\n";
        echo "<form name=etform action=$PHP_SELF method=post>\n";
        echo "<input type=hidden name=ADD value=4email>\n";
        echo "<input type=hidden name=et_id value=\"$et_id\">\n";
        echo "<table width=$section_width>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Template ID:</td>\n";
        echo "    <td align=left><b>" . mclabel($et_id) . "</b>$NWB#osdial_email_templates-et_id$NWE</td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Name:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_name size=40 maxlength=50 value=\"$et[et_name]\"> (title of the template)\n";
        echo "      $NWB#osdial_email_templates-et_name$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Comments:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_comments size=50 maxlength=255 value=\"$et[et_comments]\">\n";
        echo "      $NWB#osdial_email_templates-et_comments$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Active:</td>\n";
        echo "    <td align=left>\n";
        echo "      <select size=1 name=active>\n";
        echo "        <option>Y</option>\n";
        echo "        <option>N</option>\n";
        echo "        <option selected>$et[active]</option>\n";
        echo "      </select>\n";
        echo "      $NWB#osdial_email_templates-active$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Send Action:</td>\n";
        echo "    <td align=left>\n";
        echo "      <select size=1 name=et_send_action>\n";
        echo "        <option>ONDEMAND</option>\n";
        echo "        <option>ALL</option>\n";
        echo "        <option>ALLFORCE</option>\n";
        echo "        <option selected>$et[et_send_action]</option>\n";
        echo "      </select>\n";
        echo "      $NWB#osdial_email_templates-et_send_action$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>SMTP Host / Port:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_host size=40 maxlength=255 value=\"$et[et_host]\"> / <input type=text name=et_port size=5 maxlength=5 value=\"$et[et_port]\">\n";
        echo "      $NWB#osdial_email_templates-et_host$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>SMTP User / Pass:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_user size=20 maxlength=255 value=\"$et[et_user]\"> / <input type=text name=et_pass size=20 maxlength=255 value=\"$et[et_pass]\">\n";
        echo "      $NWB#osdial_email_templates-et_user$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>From:</td>\n";
        echo "    <td align=left>\n";
        $et['et_from'] = preg_replace("/'/",'&#39;',$et['et_from']);
        echo "      <input type=text name=et_from size=50 maxlength=255 value='$et[et_from]'>\n";
        echo "      $NWB#osdial_email_templates-et_from$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Subject:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_subject size=50 maxlength=255 value=\"$et[et_subject]\">\n";
        echo "      $NWB#osdial_email_templates-et_subject$NWE\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=center colspan=2>\n";
        echo "      <br>HTML Body:<br>\n";
        echo "      <textarea name=et_body_html class=mceEditor rows=20 cols=120>$et[et_body_html]</textarea>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=center colspan=2>\n";
        echo "      <br>Text Body:<br>\n";
        echo "      <textarea name=et_body_text rows=20 cols=70>$et[et_body_text]</textarea>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";

        echo "<tr><td align=center colspan=2>&nbsp;</td></tr>\n";
        echo "  <tr class=tabfooter align=center>\n";
        echo "    <td colspan=2>\n";
        echo "      <input type=text name=email size=50 maxlength=255>\n";
        echo "      <input type=submit name=SUBMIT value=\"Send Test Email\">\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        echo "</form>\n";
        echo "</center>\n";

        if ($LOG['delete_scripts'] > 0) {
            echo "<center><a href=\"$PHP_SELF?ADD=5email&et_id=$et_id";
            echo "\">DELETE THIS TEMPLATE</a></center>\n";
        }

    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=0email display all email templates
######################
if ($ADD=="0email") {
    $stmt=sprintf("SELECT * FROM osdial_email_templates WHERE et_id LIKE '%s__%%' AND (et_id IN %s OR et_id='%s') ORDER BY et_id;",$LOG['company_prefix'],$LOG['allowed_email_templatesSQL'],mres($et_id));
    $rslt=mysql_query($stmt, $link);
    $people_to_print = mysql_num_rows($rslt);

    echo "<center><br><font color=$default_text size=+1>EMAIL TEMPLATES</font><br><br>\n";
    echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "  <tr class=tabheader>";
    echo "    <td>ID</td>\n";
    echo "    <td>NAME</td>\n";
    echo "    <td align=center>LINKS</td>\n";
    echo "  </tr>\n";

    $o=0;
    while ($people_to_print > $o) {
    $row=mysql_fetch_row($rslt);
        echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3email&et_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=3email&et_id=$row[0]\">" . mclabel($row[0]) . "</a></td>\n";
        echo "    <td>$row[1]</td>\n";
        echo "    <td align=center><a href=\"$PHP_SELF?ADD=3email&et_id=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
        $o++;
    }

    echo "  <tr class=tabfooter>\n";
    echo "    <td colspan=3></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "</center>\n";
}

?>
