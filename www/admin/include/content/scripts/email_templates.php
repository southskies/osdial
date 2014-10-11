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
    $organization = 'ACME, Inc.';
    $organization_title = 'Office Manager';
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

    if (OSDpreg_match("/iframe src/i",$et_text)) {
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
        $organziation = OSDpreg_replace('/ /','+',$organziation);
        $organziation_title = OSDpreg_replace('/ /','+',$organziation_title);
    }

    $et_text = OSDpreg_replace('/\[\[list_id\]\]/',             $list_id,             $et_text);
    $et_text = OSDpreg_replace('/\[\[gmt_offset_now\]\]/',      $gmt_offset_now,      $et_text);
    $et_text = OSDpreg_replace('/\[\[fullname\]\]/',            $fullname,            $et_text);
    $et_text = OSDpreg_replace('/\[\[fronter\]\]/',             $fronter,             $et_text);
    $et_text = OSDpreg_replace('/\[\[user\]\]/',                $user,                $et_text);
    $et_text = OSDpreg_replace('/\[\[pass\]\]/',                $pass,                $et_text);
    $et_text = OSDpreg_replace('/\[\[lead_id\]\]/',             $lead_id,             $et_text);
    $et_text = OSDpreg_replace('/\[\[campaign\]\]/',            $campaign,            $et_text);
    $et_text = OSDpreg_replace('/\[\[phone_login\]\]/',         $phone_login,         $et_text);
    $et_text = OSDpreg_replace('/\[\[group\]\]/',               $group,               $et_text);
    $et_text = OSDpreg_replace('/\[\[channel_group\]\]/',       $channel_group,       $et_text);
    $et_text = OSDpreg_replace('/\[\[SQLdate\]\]/',             $SQLdate,             $et_text);
    $et_text = OSDpreg_replace('/\[\[epoch\]\]/',               $epoch,               $et_text);
    $et_text = OSDpreg_replace('/\[\[uniqueid\]\]/',            $uniqueid,            $et_text);
    $et_text = OSDpreg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$et_text);
    $et_text = OSDpreg_replace('/\[\[server_ip\]\]/',           $server_ip,           $et_text);
    $et_text = OSDpreg_replace('/\[\[SIPexten\]\]/',            $SIPexten,            $et_text);
    $et_text = OSDpreg_replace('/\[\[session_id\]\]/',          $session_id,          $et_text);

    $et_text = OSDpreg_replace('/\[\[title\]\]/',               $title,               $et_text);
    $et_text = OSDpreg_replace('/\[\[first_name\]\]/',          $first_name,          $et_text);
    $et_text = OSDpreg_replace('/\[\[middle_initial\]\]/',      $middle_initial,      $et_text);
    $et_text = OSDpreg_replace('/\[\[last_name\]\]/',           $last_name,           $et_text);
    $et_text = OSDpreg_replace('/\[\[address1\]\]/',            $address1,            $et_text);
    $et_text = OSDpreg_replace('/\[\[address2\]\]/',            $address2,            $et_text);
    $et_text = OSDpreg_replace('/\[\[address3\]\]/',            $address3,            $et_text);
    $et_text = OSDpreg_replace('/\[\[city\]\]/',                $city,                $et_text);
    $et_text = OSDpreg_replace('/\[\[state\]\]/',               $state,               $et_text);
    $et_text = OSDpreg_replace('/\[\[province\]\]/',            $province,            $et_text);
    $et_text = OSDpreg_replace('/\[\[postal_code\]\]/',         $postal_code,         $et_text);
    $et_text = OSDpreg_replace('/\[\[country_code\]\]/',        $country_code,        $et_text);
    $et_text = OSDpreg_replace('/\[\[phone_code\]\]/',          $phone_code,          $et_text);
    $et_text = OSDpreg_replace('/\[\[phone_number\]\]/',        $phone_number,        $et_text);
    $et_text = OSDpreg_replace('/\[\[alt_phone\]\]/',           $alt_phone,           $et_text);
    $et_text = OSDpreg_replace('/\[\[email\]\]/',               $email,               $et_text);
    $et_text = OSDpreg_replace('/\[\[gender\]\]/',              $gender,              $et_text);
    $et_text = OSDpreg_replace('/\[\[date_of_birth\]\]/',       $date_of_birth,       $et_text);
    $et_text = OSDpreg_replace('/\[\[post_date\]\]/',           $post_date,           $et_text);
    $et_text = OSDpreg_replace('/\[\[vendor_lead_code\]\]/',    $vendor_lead_code,    $et_text);
    $et_text = OSDpreg_replace('/\[\[comments\]\]/',            $comments,            $et_text);
    $et_text = OSDpreg_replace('/\[\[custom1\]\]/',             $custom1,             $et_text);
    $et_text = OSDpreg_replace('/\[\[custom2\]\]/',             $custom2,             $et_text);
    $et_text = OSDpreg_replace('/\[\[organization\]\]/',        $organization,        $et_text);
    $et_text = OSDpreg_replace('/\[\[organization_title\]\]/',  $organization_title,  $et_text);

    $et_text = OSDpreg_replace("/\n/","",$et_text);

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

<script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
// Creates a new plugin class and a custom listbox
tinymce.PluginManager.add('example', function(editor, url) {
    menuonclick = function(e) {editor.selection.setContent('<span class="osdial_template_field" title="'+this.value()+'">[['+this.value()+']]</span> ');editor.focus();};


    editor.addButton('helpb', {
        tooltip: 'Help',
        image: '/admin/help.gif',
        onclick: function() {
            editor.windowManager.open({
                title: 'Help',
                url: '/admin/admin.php?ADD=99999#osdial_email_templates-et_body_html',
                width: 800,
                height: 500
            });
        }
    });


    var myfieldsmenu = [
        {text: 'vendor_lead_code', value: 'vendor_lead_code', onclick: menuonclick},
        {text: 'source_id', value: 'source_id', onclick: menuonclick},
        {text: 'list_id', value: 'list_id', onclick: menuonclick},
        {text: 'gmt_offset_now', value: 'gmt_offset_now', onclick: menuonclick},
        {text: 'called_since_last_reset', value: 'called_since_last_reset', onclick: menuonclick},
        {text: 'phone_code', value: 'phone_code', onclick: menuonclick},
        {text: 'phone_number', value: 'phone_number', onclick: menuonclick},
        {text: 'title', value: 'title', onclick: menuonclick},
        {text: 'first_name', value: 'first_name', onclick: menuonclick},
        {text: 'middle_initial', value: 'middle_initial', onclick: menuonclick},
        {text: 'last_name', value: 'last_name', onclick: menuonclick},
        {text: 'address1', value: 'address1', onclick: menuonclick},
        {text: 'address2', value: 'address2', onclick: menuonclick},
        {text: 'address3', value: 'address3', onclick: menuonclick},
        {text: 'city', value: 'city', onclick: menuonclick},
        {text: 'state', value: 'state', onclick: menuonclick},
        {text: 'province', value: 'province', onclick: menuonclick},
        {text: 'postal_code', value: 'postal_code', onclick: menuonclick},
        {text: 'country_code',value: ' country_code', onclick: menuonclick},
        {text: 'gender', value: 'gender', onclick: menuonclick},
        {text: 'date_of_birth', value: 'date_of_birth', onclick: menuonclick},
        {text: 'alt_phone', value: 'alt_phone', onclick: menuonclick},
        {text: 'email', value: 'email', onclick: menuonclick},
        {text: 'custom1', value: 'custom1', onclick: menuonclick},
        {text: 'custom2', value: 'custom2', onclick: menuonclick},
        {text: 'comments', value: 'comments', onclick: menuonclick},
        {text: 'lead_id', value: 'lead_id', onclick: menuonclick},
        {text: 'campaign', value: 'campaign', onclick: menuonclick},
        {text: 'phone_login', value: 'phone_login', onclick: menuonclick},
        {text: 'group', value: 'group', onclick: menuonclick},
        {text: 'channel_group', value: 'channel_group', onclick: menuonclick},
        {text: 'SQLdate', value: 'SQLdate', onclick: menuonclick},
        {text: 'epoch', value: 'epoch', onclick: menuonclick},
        {text: 'uniqueid', value: 'uniqueid', onclick: menuonclick},
        {text: 'customer_zap_channel', value: 'customer_zap_channel', onclick: menuonclick},
        {text: 'server_ip', value: 'server_ip', onclick: menuonclick},
        {text: 'SIPexten', value: 'SIPexten', onclick: menuonclick},
        {text: 'session_id', value: 'session_id', onclick: menuonclick},
        {text: 'organization', value: 'organization', onclick: menuonclick},
        {text: 'organization_title', value: 'organization_title', onclick: menuonclick}
    ];
    editor.addButton('myfields', {
        type: 'menubutton',
        text: 'Form Fields',
        menu: myfieldsmenu
    });
    editor.addMenuItem('myfields', {
        type: 'menuitem',
        text: 'Form Fields',
        menu: myfieldsmenu
    });


    myaddtlfieldsmenu = [
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
                            echo "{text: '" . $form['name'] . '_' . $field['name'] . "',value: '" . $form['name'] . '_' . $field['name'] . "', onclick: menuonclick},\n";
                        }
                    }
                }
            }
        }
    }
?>
    ];
    editor.addButton('myaddtlfields', {
        type: 'menubutton',
        text: 'Addtl Fields',
        title: 'Additional Form Fields',
        menu: myaddtlfieldsmenu
    });
    editor.addMenuItem('myaddtlfields', {
        type: 'menuitem',
        text: 'Addtl Fields',
        title: 'Additional Form Fields',
        menu: myaddtlfieldsmenu
    });

});


// Initialize TinyMCE with the new plugin and listbox
tinyMCE.init({
    plugins: '-example textcolor lists colorpicker contextmenu hr image table code charmap link advlist autolink paste visualblocks visualchars',
    selector: "textarea",
    theme: "modern",
    menubar: false,
    skin: 'osdial',
    contextmenu: "cut copy paste | myfields myaddtlfields | link image inserttable | cell row column deletetable",
    forced_root_block: '',
    toolbar: [
        "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist advlist outdent indent blockquote | subscript superscript | cut copy paste | undo redo",
        "removeformat | styleselect | fontselect | fontsizeselect | forecolor backcolor | table | hr link image charmap code | visualblocks visualchars",
        "myfields | myaddtlfields | helpb",
    ],
    setup: function(e) {
        e.on('init', function(ed) {
            tbs = tinymce.DOM.select('[role="toolbar"]');
            for (var tb in tbs) {
                tbs[tb].firstChild.style.textAlign='center';
            }
            var lastsel;
            tinymce.activeEditor.getBody().onclick = function (t) {
                if (t.target.nodeName == 'SPAN') {
                    if (t.target.className && t.target.className.match('osdial_template_field|osdial_template_button')) {
                        e.stopPropagation();
                        tinymce.activeEditor.selection.select(t.target,true);
                        lastsel = e.selection.getNode();
                    }
                }
            };
            tinymce.activeEditor.getBody().onkeyup = function (t) {
                if (t.keyCode == 39 || t.keyCode == 37) {
                    if (e.selection.getNode().className && e.selection.getNode().className.match('osdial_template_field|osdial_template_button')) {
                        if (t.keyCode==37) {
                            e.selection.setCursorLocation(e.selection.getNode(),0);
                            e.selection.select(e.selection.getNode(),true);
                            lastsel=e.selection.getNode();
                        } else {
                            if (lastsel !== e.selection.getNode() || e.selection.getRng().startOffset==1) {
                                e.selection.select(e.selection.getNode(),true);
                                lastsel=e.selection.getNode();
                            } else {
                                lastsel=null;
                            }
                        }
                    }
                }
            };
        });
        e.on('BeforeSetContent', function(ed) {
            var RGmatch = new RegExp("\\[\\[(\\S+)\\]\\]","g");
            ed.content = ed.content.replace(RGmatch,function(match,p1,offset,str) {
                var RGEFmatch = new RegExp("^EF","g");
                if (p1.match(RGEFmatch)) {
                    return '<span style="border: 1px solid #000; padding: 3px 20px 2px 4px; margin: 0px 2px 0px 2px;" class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
                } else {
                    return '<span class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
                }
            });
            var RGmatch2 = new RegExp("\\{\\{([\\w\\:\\-\\\\/\\+ ]+)\\}\\}","g");
            ed.content = ed.content.replace(RGmatch2,function(match,p1,offset,str) {
                return '<span style="border: 2px outset buttonface; color: buttontext; background-color: buttonface;" class="osdial_template_button" title="' + p1 + '">{{' + p1 + '}}</span> ';
            });
        });
        e.on('LoadContent', function(ed) {
            var RGmatch = new RegExp("\\[\\[(\\S+)\\]\\]","g");
            ed.content = ed.content.replace(RGmatch,function(match,p1,offset,str) {
                var RGEFmatch = new RegExp("^EF","g");
                if (p1.match(RGEFmatch)) {
                    return '<span style="border: 1px solid #000; padding: 3px 20px 2px 4px; margin: 0px 2px 0px 2px;" class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
                } else {
                    return '<span class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
                }
            });
            var RGmatch2 = new RegExp("\\{\\{([\\w\\:\\-\\\\/\\+ ]+)\\}\\}","g");
            ed.content = ed.content.replace(RGmatch2,function(match,p1,offset,str) {
                return '<span style="border: 2px outset buttonface; color: buttontext; background-color: buttonface;" class="osdial_template_button" title="' + p1 + '">{{' + p1 + '}}</span> ';
            });
        });
        e.on('BeforeGetContent', function(ed) {
            flds = e.dom.select('.osdial_template_field');
            for (var fld in flds) {
                thisfld = flds[fld];
                e.dom.setHTML(thisfld,'[['+thisfld.title+']] ');
            }
            flds = e.dom.select('.osdial_template_button');
            for (var fld in flds) {
                thisfld = flds[fld];
                e.dom.setHTML(thisfld,'{{'+thisfld.title+'}} ');
            }
        });
        e.on('SaveContent', function(ed) {
            oldhtml = e.getBody().innerHTML;
            flds = e.dom.select('.osdial_template_field');
            for (var fld in flds) {
                e.dom.setOuterHTML(flds[fld],'[['+flds[fld].title+']] ');
            }
            flds2 = e.dom.select('.osdial_template_button');
            for (var fld in flds2) {
                e.dom.setOuterHTML(flds2[fld],'{{'+flds2[fld].title+'}} ');
            }
            ed.content = tinymce.trim(e.serializer.serialize(e.getBody()));
            e.dom.setHTML(e.getBody(), oldhtml);
        });
    }
});

</script>

<?php

        echo "<center><br>\n";
        echo "<font class=top_header color=$default_text size=+1>ADD NEW EMAIL TEMPLATE</font><br><br>\n";
        echo "<form name=etform action=$PHP_SELF method=post enctype=\"multipart/form-data\">\n";
        echo "<input type=hidden name=ADD value=2email>\n";
        echo "<table class=shadedtable width=$section_width cellspacing=3>\n";
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
        echo "      ".helptag("osdial_email_templates-et_id")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Name:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_name size=40 maxlength=50> (title of the template)\n";
        echo "      ".helptag("osdial_email_templates-et_name")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Comments:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_comments size=50 maxlength=255>\n";
        echo "      ".helptag("osdial_email_templates-et_comments")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Active:</td>\n";
        echo "    <td align=left>\n";
        echo "      <select size=1 name=active>\n";
        echo "        <option selected>Y</option>\n";
        echo "        <option>N</option>\n";
        echo "      </select>\n";
        echo "      ".helptag("osdial_email_templates-active")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>SMTP Host / Port:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_host size=40 maxlength=255 value=\"localhost\">";
        echo "      ".helptag("osdial_email_templates-et_host")."\n";
	echo " / <input type=text name=et_port size=5 maxlength=5 value=\"25\">\n";
        echo "      ".helptag("osdial_email_templates-et_port")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>SMTP User / Pass:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_user size=20 maxlength=255> ";
        echo "      ".helptag("osdial_email_templates-et_user")."\n";
	echo " / <input type=text name=et_pass size=20 maxlength=255>\n";
        echo "      ".helptag("osdial_email_templates-et_pass")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>From:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_from size=50 maxlength=255 value='\"John Doe\" <johndoe@gmail.com>'>\n";
        echo "      ".helptag("osdial_email_templates-et_from")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Subject:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_subject size=50 maxlength=255>\n";
        echo "      ".helptag("osdial_email_templates-et_subject")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        #echo "  <tr bgcolor=$oddrows>\n";
        #echo "    <td align=center colspan=2>\n";
        #echo "      <br>HTML Body:<br>";
        #echo "      <textarea name=et_body_html rows=20 cols=120></textarea>\n";
        #echo "    </td>\n";
        #echo "  </tr>\n";

        #echo "  <tr bgcolor=$oddrows>\n";
        #echo "    <td align=center colspan=2>\n";
        #echo "      <br>Text Body:<br>";
        #echo "      <textarea name=et_body_text class=NoEditor rows=20 cols=70></textarea>\n";
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
        $stmt=sprintf("SELECT count(*) FROM osdial_email_templates WHERE et_id='%s';",mres($preet_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>TEMPLATE NOT ADDED - there is already an email template with this name</font>\n";
        } else {
            if (OSDstrlen($et_id) < 2 or OSDstrlen($et_name) < 2) {
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

        if (OSDstrlen($et_id) < 2 or OSDstrlen($et_name) < 2 or OSDstrlen($et_body_html) < 2) {
            echo "<br><font color=red>TEMPLATE NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>Template name (".OSDstrlen($et_name).") and HTML body (".OSDstrlen($et_body_html).") must be at least 2 characters in length</font><br>\n";
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
                $organization = 'ACME, Inc.';
                $organization_title = 'Office Manager';
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

                $et_subject   = OSDpreg_replace('/\[\[list_id\]\]/',             $list_id,             $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[gmt_offset_now\]\]/',      $gmt_offset_now,      $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[fullname\]\]/',            $fullname,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[fronter\]\]/',             $fronter,             $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[user\]\]/',                $user,                $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[pass\]\]/',                $pass,                $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[lead_id\]\]/',             $lead_id,             $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[campaign\]\]/',            $campaign,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[phone_login\]\]/',         $phone_login,         $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[group\]\]/',               $group,               $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[channel_group\]\]/',       $channel_group,       $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[SQLdate\]\]/',             $SQLdate,             $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[epoch\]\]/',               $epoch,               $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[uniqueid\]\]/',            $uniqueid,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$et_subject);
                $et_subject   = OSDpreg_replace('/\[\[server_ip\]\]/',           $server_ip,           $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[SIPexten\]\]/',            $SIPexten,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[session_id\]\]/',          $session_id,          $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[title\]\]/',               $title,               $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[first_name\]\]/',          $first_name,          $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[middle_initial\]\]/',      $middle_initial,      $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[last_name\]\]/',           $last_name,           $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[address1\]\]/',            $address1,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[address2\]\]/',            $address2,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[address3\]\]/',            $address3,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[city\]\]/',                $city,                $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[state\]\]/',               $state,               $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[province\]\]/',            $province,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[postal_code\]\]/',         $postal_code,         $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[country_code\]\]/',        $country_code,        $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[phone_code\]\]/',          $phone_code,          $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[phone_number\]\]/',        $phone_number,        $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[alt_phone\]\]/',           $alt_phone,           $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[email\]\]/',               $email,               $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[gender\]\]/',              $gender,              $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[date_of_birth\]\]/',       $date_of_birth,       $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[post_date\]\]/',           $post_date,           $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[vendor_lead_code\]\]/',    $vendor_lead_code,    $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[comments\]\]/',            $comments,            $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[custom1\]\]/',             $custom1,             $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[custom2\]\]/',             $custom2,             $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[organization\]\]/',        $organization,        $et_subject);
                $et_subject   = OSDpreg_replace('/\[\[organization_title\]\]/',  $organization_title,  $et_subject);

                $et_body_html = OSDpreg_replace('/\[\[list_id\]\]/',             $list_id,             $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[gmt_offset_now\]\]/',      $gmt_offset_now,      $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[fullname\]\]/',            $fullname,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[fronter\]\]/',             $fronter,             $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[user\]\]/',                $user,                $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[pass\]\]/',                $pass,                $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[lead_id\]\]/',             $lead_id,             $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[campaign\]\]/',            $campaign,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[phone_login\]\]/',         $phone_login,         $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[group\]\]/',               $group,               $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[channel_group\]\]/',       $channel_group,       $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[SQLdate\]\]/',             $SQLdate,             $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[epoch\]\]/',               $epoch,               $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[uniqueid\]\]/',            $uniqueid,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[server_ip\]\]/',           $server_ip,           $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[SIPexten\]\]/',            $SIPexten,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[session_id\]\]/',          $session_id,          $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[title\]\]/',               $title,               $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[first_name\]\]/',          $first_name,          $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[middle_initial\]\]/',      $middle_initial,      $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[last_name\]\]/',           $last_name,           $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[address1\]\]/',            $address1,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[address2\]\]/',            $address2,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[address3\]\]/',            $address3,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[city\]\]/',                $city,                $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[state\]\]/',               $state,               $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[province\]\]/',            $province,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[postal_code\]\]/',         $postal_code,         $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[country_code\]\]/',        $country_code,        $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[phone_code\]\]/',          $phone_code,          $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[phone_number\]\]/',        $phone_number,        $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[alt_phone\]\]/',           $alt_phone,           $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[email\]\]/',               $email,               $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[gender\]\]/',              $gender,              $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[date_of_birth\]\]/',       $date_of_birth,       $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[post_date\]\]/',           $post_date,           $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[vendor_lead_code\]\]/',    $vendor_lead_code,    $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[comments\]\]/',            $comments,            $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[custom1\]\]/',             $custom1,             $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[custom2\]\]/',             $custom2,             $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[organization\]\]/',        $organization,        $et_body_html);
                $et_body_html = OSDpreg_replace('/\[\[organization_title\]\]/',  $organization_title,  $et_body_html);

                $et_body_text = OSDpreg_replace('/\[\[list_id\]\]/',             $list_id,             $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[gmt_offset_now\]\]/',      $gmt_offset_now,      $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[fullname\]\]/',            $fullname,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[fronter\]\]/',             $fronter,             $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[user\]\]/',                $user,                $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[pass\]\]/',                $pass,                $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[lead_id\]\]/',             $lead_id,             $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[campaign\]\]/',            $campaign,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[phone_login\]\]/',         $phone_login,         $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[group\]\]/',               $group,               $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[channel_group\]\]/',       $channel_group,       $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[SQLdate\]\]/',             $SQLdate,             $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[epoch\]\]/',               $epoch,               $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[uniqueid\]\]/',            $uniqueid,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[customer_zap_channel\]\]/',$customer_zap_channel,$et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[server_ip\]\]/',           $server_ip,           $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[SIPexten\]\]/',            $SIPexten,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[session_id\]\]/',          $session_id,          $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[title\]\]/',               $title,               $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[first_name\]\]/',          $first_name,          $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[middle_initial\]\]/',      $middle_initial,      $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[last_name\]\]/',           $last_name,           $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[address1\]\]/',            $address1,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[address2\]\]/',            $address2,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[address3\]\]/',            $address3,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[city\]\]/',                $city,                $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[state\]\]/',               $state,               $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[province\]\]/',            $province,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[postal_code\]\]/',         $postal_code,         $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[country_code\]\]/',        $country_code,        $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[phone_code\]\]/',          $phone_code,          $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[phone_number\]\]/',        $phone_number,        $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[alt_phone\]\]/',           $alt_phone,           $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[email\]\]/',               $email,               $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[gender\]\]/',              $gender,              $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[date_of_birth\]\]/',       $date_of_birth,       $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[post_date\]\]/',           $post_date,           $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[vendor_lead_code\]\]/',    $vendor_lead_code,    $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[comments\]\]/',            $comments,            $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[custom1\]\]/',             $custom1,             $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[custom2\]\]/',             $custom2,             $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[organization\]\]/',        $organization,        $et_body_text);
                $et_body_text = OSDpreg_replace('/\[\[organization_title\]\]/',  $organization_title,  $et_body_text);

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

        if (OSDstrlen($et_id) < 2) {
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

        if (OSDstrlen($et_id) < 2 or $CoNfIrM != 'YES') {
            echo "<br><font color=red>TEMPLATE NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>et_id be at least 2 characters in length</font><br>\n";
        } else {
            $stmt=sprintf("DELETE FROM osdial_email_templates WHERE et_id='%s' LIMIT 1;",mres($et_id));
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

<script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
// Creates a new plugin class and a custom listbox
tinymce.PluginManager.add('example', function(editor, url) {
    menuonclick = function(e) {editor.selection.setContent('<span class="osdial_template_field" title="'+this.value()+'">[['+this.value()+']]</span> ');editor.focus();};


    editor.addButton('helpb', {
        tooltip: 'Help',
        image: '/admin/help.gif',
        onclick: function() {
            editor.windowManager.open({
                title: 'Help',
                url: '/admin/admin.php?ADD=99999#osdial_email_templates-et_body_html',
                width: 800,
                height: 500
            });
        }
    });


    editor.addButton('previewb', {
        text: 'Preview',
        onclick: function() {
            editor.windowManager.open({
                title: 'Preview',
                url: '/admin/admin.php?ADD=7email&et_id=<?php echo $et_id; ?>',
                width: 800,
                height: 500
            });
        }
    });


    var myfieldsmenu = [
        {text: 'vendor_lead_code', value: 'vendor_lead_code', onclick: menuonclick},
        {text: 'source_id', value: 'source_id', onclick: menuonclick},
        {text: 'list_id', value: 'list_id', onclick: menuonclick},
        {text: 'gmt_offset_now', value: 'gmt_offset_now', onclick: menuonclick},
        {text: 'called_since_last_reset', value: 'called_since_last_reset', onclick: menuonclick},
        {text: 'phone_code', value: 'phone_code', onclick: menuonclick},
        {text: 'phone_number', value: 'phone_number', onclick: menuonclick},
        {text: 'title', value: 'title', onclick: menuonclick},
        {text: 'first_name', value: 'first_name', onclick: menuonclick},
        {text: 'middle_initial', value: 'middle_initial', onclick: menuonclick},
        {text: 'last_name', value: 'last_name', onclick: menuonclick},
        {text: 'address1', value: 'address1', onclick: menuonclick},
        {text: 'address2', value: 'address2', onclick: menuonclick},
        {text: 'address3', value: 'address3', onclick: menuonclick},
        {text: 'city', value: 'city', onclick: menuonclick},
        {text: 'state', value: 'state', onclick: menuonclick},
        {text: 'province', value: 'province', onclick: menuonclick},
        {text: 'postal_code', value: 'postal_code', onclick: menuonclick},
        {text: 'country_code',value: ' country_code', onclick: menuonclick},
        {text: 'gender', value: 'gender', onclick: menuonclick},
        {text: 'date_of_birth', value: 'date_of_birth', onclick: menuonclick},
        {text: 'alt_phone', value: 'alt_phone', onclick: menuonclick},
        {text: 'email', value: 'email', onclick: menuonclick},
        {text: 'custom1', value: 'custom1', onclick: menuonclick},
        {text: 'custom2', value: 'custom2', onclick: menuonclick},
        {text: 'comments', value: 'comments', onclick: menuonclick},
        {text: 'lead_id', value: 'lead_id', onclick: menuonclick},
        {text: 'campaign', value: 'campaign', onclick: menuonclick},
        {text: 'phone_login', value: 'phone_login', onclick: menuonclick},
        {text: 'group', value: 'group', onclick: menuonclick},
        {text: 'channel_group', value: 'channel_group', onclick: menuonclick},
        {text: 'SQLdate', value: 'SQLdate', onclick: menuonclick},
        {text: 'epoch', value: 'epoch', onclick: menuonclick},
        {text: 'uniqueid', value: 'uniqueid', onclick: menuonclick},
        {text: 'customer_zap_channel', value: 'customer_zap_channel', onclick: menuonclick},
        {text: 'server_ip', value: 'server_ip', onclick: menuonclick},
        {text: 'SIPexten', value: 'SIPexten', onclick: menuonclick},
        {text: 'session_id', value: 'session_id', onclick: menuonclick},
        {text: 'organization', value: 'organization', onclick: menuonclick},
        {text: 'organization_title', value: 'organization_title', onclick: menuonclick}
    ];
    editor.addButton('myfields', {
        type: 'menubutton',
        text: 'Form Fields',
        menu: myfieldsmenu
    });
    editor.addMenuItem('myfields', {
        type: 'menuitem',
        text: 'Form Fields',
        menu: myfieldsmenu
    });


    myaddtlfieldsmenu = [
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
                            echo "{text: '" . $form['name'] . '_' . $field['name'] . "',value: '" . $form['name'] . '_' . $field['name'] . "', onclick: menuonclick},\n";
                        }
                    }
                }
            }
        }
    }
?>
    ];
    editor.addButton('myaddtlfields', {
        type: 'menubutton',
        text: 'Addtl Fields',
        title: 'Additional Form Fields',
        menu: myaddtlfieldsmenu
    });
    editor.addMenuItem('myaddtlfields', {
        type: 'menuitem',
        text: 'Addtl Fields',
        title: 'Additional Form Fields',
        menu: myaddtlfieldsmenu
    });

});


// Initialize TinyMCE with the new plugin and listbox
tinyMCE.init({
    plugins: '-example textcolor lists colorpicker contextmenu hr image table code charmap link advlist autolink paste visualblocks visualchars',
    selector: "textarea",
    theme: "modern",
    menubar: false,
    skin: 'osdial',
    contextmenu: "cut copy paste | myfields myaddtlfields | link image inserttable | cell row column deletetable",
    forced_root_block: '',
    toolbar: [
        "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist advlist outdent indent blockquote | subscript superscript | cut copy paste | undo redo",
        "removeformat | styleselect | fontselect | fontsizeselect | forecolor backcolor | table | hr link image charmap code | visualblocks visualchars",
        "myfields | myaddtlfields | previewb | helpb",
    ],
    setup: function(e) {
        e.on('init', function(ed) {
            tbs = tinymce.DOM.select('[role="toolbar"]');
            for (var tb in tbs) {
                tbs[tb].firstChild.style.textAlign='center';
            }
            var lastsel;
            tinymce.activeEditor.getBody().onclick = function (t) {
                if (t.target.nodeName == 'SPAN') {
                    if (t.target.className && t.target.className.match('osdial_template_field|osdial_template_button')) {
                        e.stopPropagation();
                        tinymce.activeEditor.selection.select(t.target,true);
                        lastsel = e.selection.getNode();
                    }
                }
            };
            tinymce.activeEditor.getBody().onkeyup = function (t) {
                if (t.keyCode == 39 || t.keyCode == 37) {
                    if (e.selection.getNode().className && e.selection.getNode().className.match('osdial_template_field|osdial_template_button')) {
                        if (t.keyCode==37) {
                            e.selection.setCursorLocation(e.selection.getNode(),0);
                            e.selection.select(e.selection.getNode(),true);
                            lastsel=e.selection.getNode();
                        } else {
                            if (lastsel !== e.selection.getNode() || e.selection.getRng().startOffset==1) {
                                e.selection.select(e.selection.getNode(),true);
                                lastsel=e.selection.getNode();
                            } else {
                                lastsel=null;
                            }
                        }
                    }
                }
            };
        });
        e.on('BeforeSetContent', function(ed) {
            var RGmatch = new RegExp("\\[\\[(\\S+)\\]\\]","g");
            ed.content = ed.content.replace(RGmatch,function(match,p1,offset,str) {
                var RGEFmatch = new RegExp("^EF","g");
                if (p1.match(RGEFmatch)) {
                    return '<span style="border: 1px solid #000; padding: 3px 20px 2px 4px; margin: 0px 2px 0px 2px;" class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
                } else {
                    return '<span class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
                }
            });
            var RGmatch2 = new RegExp("\\{\\{([\\w\\:\\-\\\\/\\+ ]+)\\}\\}","g");
            ed.content = ed.content.replace(RGmatch2,function(match,p1,offset,str) {
                return '<span style="border: 2px outset buttonface; color: buttontext; background-color: buttonface;" class="osdial_template_button" title="' + p1 + '">{{' + p1 + '}}</span> ';
            });
        });
        e.on('LoadContent', function(ed) {
            var RGmatch = new RegExp("\\[\\[(\\S+)\\]\\]","g");
            ed.content = ed.content.replace(RGmatch,function(match,p1,offset,str) {
                var RGEFmatch = new RegExp("^EF","g");
                if (p1.match(RGEFmatch)) {
                    return '<span style="border: 1px solid #000; padding: 3px 20px 2px 4px; margin: 0px 2px 0px 2px;" class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
                } else {
                    return '<span class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
                }
            });
            var RGmatch2 = new RegExp("\\{\\{([\\w\\:\\-\\\\/\\+ ]+)\\}\\}","g");
            ed.content = ed.content.replace(RGmatch2,function(match,p1,offset,str) {
                return '<span style="border: 2px outset buttonface; color: buttontext; background-color: buttonface;" class="osdial_template_button" title="' + p1 + '">{{' + p1 + '}}</span> ';
            });
        });
        e.on('BeforeGetContent', function(ed) {
            flds = e.dom.select('.osdial_template_field');
            for (var fld in flds) {
                thisfld = flds[fld];
                e.dom.setHTML(thisfld,'[['+thisfld.title+']] ');
            }
            flds = e.dom.select('.osdial_template_button');
            for (var fld in flds) {
                thisfld = flds[fld];
                e.dom.setHTML(thisfld,'{{'+thisfld.title+'}} ');
            }
        });
        e.on('SaveContent', function(ed) {
            oldhtml = e.getBody().innerHTML;
            flds = e.dom.select('.osdial_template_field');
            for (var fld in flds) {
                e.dom.setOuterHTML(flds[fld],'[['+flds[fld].title+']] ');
            }
            flds2 = e.dom.select('.osdial_template_button');
            for (var fld in flds2) {
                e.dom.setOuterHTML(flds2[fld],'{{'+flds2[fld].title+'}} ');
            }
            ed.content = tinymce.trim(e.serializer.serialize(e.getBody()));
            e.dom.setHTML(e.getBody(), oldhtml);
        });
    }
});

</script>

<?php
        $et = get_first_record($link, 'osdial_email_templates', '*', sprintf("et_id='%s'",mres($et_id)) );
        $rslt=mysql_query($stmt, $link);

        $et['et_body_html'] = OSDpreg_replace("/\n/","",$et['et_body_html']);


        echo "<center>\n";
        echo "<br><font class=top_header color=$default_text size=+1>MODIFY AN EMAIL TEMPLATE</font><br>\n";
        echo "<form name=etform action=$PHP_SELF method=post enctype=\"multipart/form-data\">\n";
        echo "<input type=hidden name=ADD value=4email>\n";
        echo "<input type=hidden name=et_id value=\"$et_id\">\n";
        echo "<table width=$section_width onkeypress=\"tinyMCE.triggerSave();\">\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Template ID:</td>\n";
        echo "    <td align=left><b>" . mclabel($et_id) . "</b>".helptag("osdial_email_templates-et_id")."</td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Name:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_name size=40 maxlength=50 value=\"$et[et_name]\"> (title of the template)\n";
        echo "      ".helptag("osdial_email_templates-et_name")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Comments:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_comments size=50 maxlength=255 value=\"$et[et_comments]\">\n";
        echo "      ".helptag("osdial_email_templates-et_comments")."\n";
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
        echo "      ".helptag("osdial_email_templates-active")."\n";
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
        echo "      ".helptag("osdial_email_templates-et_send_action")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>SMTP Host / Port:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_host size=40 maxlength=255 value=\"$et[et_host]\"> ";
        echo "      ".helptag("osdial_email_templates-et_host")."\n";
	echo " / <input type=text name=et_port size=5 maxlength=5 value=\"$et[et_port]\">\n";
        echo "      ".helptag("osdial_email_templates-et_port")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>SMTP User / Pass:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_user size=20 maxlength=255 value=\"$et[et_user]\"> ";
        echo "      ".helptag("osdial_email_templates-et_user")."\n";
	echo " / <input type=text name=et_pass size=20 maxlength=255 value=\"$et[et_pass]\">\n";
        echo "      ".helptag("osdial_email_templates-et_pass")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>From:</td>\n";
        echo "    <td align=left>\n";
        $et['et_from'] = OSDpreg_replace("/'/",'&#39;',$et['et_from']);
        echo "      <input type=text name=et_from size=50 maxlength=255 value='$et[et_from]'>\n";
        echo "      ".helptag("osdial_email_templates-et_from")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Subject:</td>\n";
        echo "    <td align=left>\n";
        echo "      <input type=text name=et_subject size=50 maxlength=255 value=\"$et[et_subject]\">\n";
        echo "      ".helptag("osdial_email_templates-et_subject")."\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=center colspan=2>\n";
        echo "      <p>HTML Body:</p>\n";
        echo "      <textarea name=et_body_html rows=20 cols=120>$et[et_body_html]</textarea>\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=center colspan=2>\n";
        echo "      <p>Text Body:</p>\n";
        echo "      <textarea name=et_body_text class=NoEditor rows=20 cols=70>$et[et_body_text]</textarea>\n";
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
    $stmt=sprintf("SELECT * FROM osdial_email_templates WHERE et_id LIKE '%s__%%' AND (et_id IN %s OR et_id='%s') ORDER BY et_id;",mres($LOG['company_prefix']),$LOG['allowed_email_templatesSQL'],mres($et_id));
    $rslt=mysql_query($stmt, $link);
    $people_to_print = mysql_num_rows($rslt);

    echo "<center><br><font class=top_header color=$default_text size=+1>EMAIL TEMPLATES</font><br><br>\n";
    echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
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
