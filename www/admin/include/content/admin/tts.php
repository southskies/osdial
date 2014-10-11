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


$fest_voices = array();
if (file_exists("/usr/share/festival/lib/voices/us/cmu_us_rms_cg")) $fest_voices[] = "voice_cmu_us_rms_cg";
if (file_exists("/usr/share/festival/lib/voices/us/cmu_us_slt_arctic_hts")) $fest_voices[] = "voice_cmu_us_slt_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/cmu_us_awb_cg")) $fest_voices[] = "voice_cmu_us_awb_cg";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_rms_arctic_hts")) $fest_voices[] = "voice_nitech_us_rms_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_slt_arctic_hts")) $fest_voices[] = "voice_nitech_us_slt_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_awb_arctic_hts")) $fest_voices[] = "voice_nitech_us_awb_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_bdl_arctic_hts")) $fest_voices[] = "voice_nitech_us_bdl_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_clb_arctic_hts")) $fest_voices[] = "voice_nitech_us_clb_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_jmk_arctic_hts")) $fest_voices[] = "voice_nitech_us_jmk_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/english/kal_diphone")) $fest_voices[] = "voice_kal_diphone";
if (file_exists("/usr/share/festival/lib/voices/english/ked_diphone")) $fest_voices[] = "voice_ked_diphone";
if (file_exists("/usr/share/festival/lib/voices/english/rab_diphone")) $fest_voices[] = "voice_rab_diphone";
if (file_exists("/usr/share/festival/lib/voices/spanish/cstr_upc_upm_spanish_hts")) $fest_voices[] = "voice_cstr_upc_upm_spanish_hts";
if (file_exists("/usr/share/festival/lib/voices/es/JuntaDeAndalucia_es_pa_diphone")) $fest_voices[] = "voice_JuntaDeAndalucia_es_pa_diphone";
if (file_exists("/usr/share/festival/lib/voices/es/JuntaDeAndalucia_es_sf_diphone")) $fest_voices[] = "voice_JuntaDeAndalucia_es_sf_diphone";
if (file_exists("/usr/share/festival/lib/voices/spanish/el_diphone")) $fest_voices[] = "voice_el_diphone";
$fest_types = array();
$fest_types['voice_cmu_us_rms_cg'] = 'US English male speaker "RMS" (clustergen)';
$fest_types['voice_cmu_us_slt_arctic_hts'] = 'US English female speaker "SLT" (clustergen)';
$fest_types['voice_cmu_us_awb_cg'] = 'Scottish-accent US English male speaker "AWB" (clustergen)';
$fest_types['voice_nitech_us_rms_arctic_hts'] = 'US English male speaker "RMS" (nitech)';
$fest_types['voice_nitech_us_slt_arctic_hts'] = 'US English female speaker "SLT" (nitech)';
$fest_types['voice_nitech_us_awb_arctic_hts'] = 'Scottish-accent US English male speaker "AWB" (nitech)';
$fest_types['voice_nitech_us_bdl_arctic_hts'] = 'US English male speaker "BDL" (nitech)';
$fest_types['voice_nitech_us_clb_arctic_hts'] = 'US English female speaker "CLB" (nitech)';
$fest_types['voice_nitech_us_jmk_arctic_hts'] = 'Canadian-accent US English male speaker "JMK" (nitech)';
$fest_types['voice_kal_diphone'] = 'American English male speaker "Kevin" (diphone)';
$fest_types['voice_ked_diphone'] = 'American English male speaker "Kurt" (diphone)';
$fest_types['voice_rab_diphone'] = 'British English male speaker (diphone)';
$fest_types['voice_cstr_upc_upm_spanish_hts'] = 'Female Spanish voice (hts)';
$fest_types['voice_JuntaDeAndalucia_es_pa_diphone'] = 'Male Spanish voice "PAL"';
$fest_types['voice_JuntaDeAndalucia_es_sf_diphone'] = 'Female Spanish voice "SFL"';
$fest_types['voice_el_diphone'] = 'Male Spanish voice (diphone)';




######################
# ADD=10tts display the ADD NEW TTS SCREEN
######################

if ($ADD=="11tts") {
    if ($LOG['modify_servers']>0) {
        echo "<center><br/><font class=top_header color=$default_text size=+1>ADD NEW TEXT-TO-SPEECH TEMPLATE</font><br/><br/>\n";
        echo '<form action="' . $PHP_SELF . '" method="POST" name=osdial_form enctype="multipart/form-data">';
        echo "<input type=hidden name=ADD value=21tts>\n";
        echo "<input type=hidden name=last_general_extension value=\"".($config['settings']['last_general_extension']+1)."\">\n";

        echo "<table class=shadedtable width=$section_width cellspacing=3>\n";
        echo "  <tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=30%>Description: </td>\n";
        echo "    <td align=left><input type=text name=tts_description size=50 maxlength=100 value=\"\">".helptag("tts-description")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Extension: </td>\n";
        echo "    <td align=left><input type=text name=tts_extension size=10 maxlength=20 value=\"\" onclick=\"if (this.value=='') { this.value='".($config['settings']['last_general_extension']+1)."';}\">".helptag("tts-extension")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Voice: </td>\n";
        echo "    <td align=left>\n";
        echo "      <select name=tts_voice>\n";
        foreach ($fest_voices as $voice) {
            echo "        <option value=\"$voice\">$fest_types[$voice]</option>\n";
        }
        echo "      </select>".helptag("tts-voice")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</table></form></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=21tts adds new tts to the system
######################

if ($ADD=="21tts") {
    if ($LOG['modify_servers']>0) {
        if (OSDstrlen($tts_description) < 3) {
            echo "<br/><font color=red>TEXT-TO-SPEECH NOT ADDED - Please go back and look at the data you entered</font>\n";
        } else {
            $ttsins=1;
            if (!empty($tts_extension)) {
                $etts = get_first_record($link, 'osdial_tts', 'count(*) as count', sprintf("extension='%s'",mres($tts_extension)) );
                $phn  = get_first_record($link, 'phones', 'count(*) as count', sprintf("extension='%s'",mres($tts_extension)) );
                $med  = get_first_record($link, 'osdial_media', 'count(*) as count', sprintf("extension='%s'",mres($tts_extension)) );
                $cnf  = get_first_record($link, 'conferences', 'count(*) as count', sprintf("conf_exten='%s'",mres($tts_extension)) );
                $ocnf = get_first_record($link, 'osdial_conferences', 'count(*) as count', sprintf("conf_exten='%s'",mres($tts_extension)) );
                if ($etts['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT ADDED - The extension entered is currently in use by another TEXT-TO-SPEECH.</font>\n";
                    $ttsins=0;
                } elseif ($phn['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT ADDED - The extension entered is currently in use by a phone.</font>\n";
                    $ttsins=0;
                } elseif ($med['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT ADDED - The extension entered is currently in use by a media component.</font>\n";
                    $ttsins=0;
                } elseif ($cnf['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT ADDED - The extension entered is currently in use by a conference.</font>\n";
                    $ttsins=0;
                } elseif ($ocnf['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT ADDED - The extension entered is currently in use by a conference.</font>\n";
                    $ttsins=0;
                }
            }
            if ($ttsins) {
                echo "<br/><font color=$default_text>TEXT-TO-SPEECH ADDED</font>\n";

                $stmt=sprintf("INSERT INTO osdial_tts SET description='%s',extension='%s',voice='%s';",mres($tts_description),mres($tts_extension),mres($tts_voice));
                $rslt=mysql_query($stmt, $link);
                $tts_id = mysql_insert_id($link);
            }
            echo "<br/>";

            if ($last_general_extension==$tts_extension) {
                $stmt=sprintf("UPDATE system_settings SET last_general_extension='%s';",mres($last_general_extension));
                $rslt=mysql_query($stmt, $link);
            }
        }
        $ADD="31tts";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=41tts modify tts record in the system
######################
if ($ADD=="41tts") {
    if ($LOG['modify_servers']>0) {
        if (OSDstrlen($tts_description) < 3) {
            echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT MODIFIED - Please go back and look at the data you entered</font>\n";
        } else {
            $ttsupd=1;
            if (!empty($tts_extension)) {
                $etts = get_first_record($link, 'osdial_tts', 'count(*) as count', sprintf("extension='%s' AND id!='%s'",mres($tts_extension),mres($tts_id)) );
                $phn  = get_first_record($link, 'phones', 'count(*) as count', sprintf("extension='%s'",mres($tts_extension)) );
                $med  = get_first_record($link, 'osdial_media', 'count(*) as count', sprintf("extension='%s'",mres($tts_extension)) );
                $cnf  = get_first_record($link, 'conferences', 'count(*) as count', sprintf("conf_exten='%s'",mres($tts_extension)) );
                $ocnf = get_first_record($link, 'osdial_conferences', 'count(*) as count', sprintf("conf_exten='%s'",mres($tts_extension)) );
                if ($etts['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT MODIFIED - The extension entered is currently in use by another TEXT-TO-SPEECH.</font>\n";
                    $ttsupd=0;
                } elseif ($phn['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT MODIFIED - The extension entered is currently in use by a phone.</font>\n";
                    $ttsupd=0;
                } elseif ($med['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT MODIFIED - The extension entered is currently in use by a media component.</font>\n";
                    $ttsupd=0;
                } elseif ($cnf['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT MODIFIED - The extension entered is currently in use by a conference.</font>\n";
                    $ttsupd=0;
                } elseif ($ocnf['count']>0) {
                    echo "<br><font color=$default_text>TEXT-TO-SPEECH NOT MODIFIED - The extension entered is currently in use by a conference.</font>\n";
                    $ttsupd=0;
                }
            }

            if ($ttsupd) {
                echo "<br><font color=$default_text>TEXT-TO-SPEECH MODIFIED: $tts_id : $tts_description</font>\n";

                $tts_phrase = OSDpreg_replace('/&nbsp;/',' ',htmlspecialchars_decode(strip_tags(OSDpreg_replace('/(\<br \/\>|\<br\/\>|\<br\>)/',"\n",$tts_phrase))));
                $stmt=sprintf("UPDATE osdial_tts SET description='%s',extension='%s',phrase='%s',voice='%s' WHERE id='%s';",mres($tts_description),mres($tts_extension),mres($tts_phrase),mres($tts_voice),mres($tts_id));
                $rslt=mysql_query($stmt, $link);
            }

            if ($last_general_extension==$tts_extension) {
                $stmt=sprintf("UPDATE system_settings SET last_general_extension='%s';",mres($last_general_extension));
                $rslt=mysql_query($stmt, $link);
            }
        }
        $ADD="31tts";	# go to tts modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=51tts confirmation before deletion of tts record
######################
if ($ADD=="51tts") {
    if ($LOG['modify_servers']>0) {
        echo "<br/><b><font color=$default_text>TEXT-TO-SPEECH DELETION CONFIRMATION: $tts_id - $tts_extension</b>\n";
        echo "<br/><br/><a href=\"$PHP_SELF?ADD=61tts&tts_id=$tts_id&CoNfIrM=YES\">Click here to delete tts $tts_id</a></font><br/><br/><br/>\n";
        $ADD='31tts';		# go to tts modification below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=61tts delete tts record
######################
if ($ADD=="61tts") {
    if ($LOG['modify_servers']>0) {
        $stmt=sprintf("DELETE FROM osdial_tts WHERE id='%s' LIMIT 1;",mres($tts_id));
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!!!DELETING TTS!!!|$PHP_AUTH_USER|$ip|id='$tts_id'||\n");
            fclose($fp);
        }
        echo "<br/><b><font color=$default_text>TEXT-TO-SPEECH DELETION COMPLETED: $tts_id</font></b>\n";
        echo "<br/><br/>\n";
        $ADD='10tts';		# go to tts list
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=31tts modify tts record in the system
######################
if ($ADD=="31tts") {
    if ($LOG['modify_servers']>0) {
?>

<script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
// Creates a new plugin class and a custom listbox
tinymce.PluginManager.add('example', function(editor, url) {
    menuonclick = function(e) {editor.selection.setContent('<span class="osdial_template_field" title="'+this.value()+'">[['+this.value()+']]</span> ');editor.focus();};
    mediaonclick = function(e) {editor.selection.setContent('<span class="osdial_template_media" title="'+this.value()+'">{{'+this.value()+'}}</span> ');editor.focus();};
    asteriskonclick = function(e) {editor.selection.setContent('<span class="osdial_template_media" title="'+this.value()+'">{{'+this.value()+'}}</span> ');editor.focus();};


    editor.addButton('helpb', {
        tooltip: 'Help',
        image: '/admin/help.gif',
        onclick: function() {
            editor.windowManager.open({
                title: 'Help',
                url: '/admin/admin.php?ADD=99999#tts-phrase',
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


    var myaddtlfieldsmenu = [
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
                            echo "{text: '" . $form['name'] . '_' . $field['name'] . "', value: '" . $form['name'] . '_' . $field['name'] . "', onclick: menuonclick},\n";
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


   var mediafilesmenu = [
<?php
    $mflist = get_krh($link, 'osdial_media', 'filename,description','','','');
    if (is_array($mflist)) {
        foreach ($mflist as $mfile) {
            $mfile['filename'] = OSDpreg_replace('/.*\/|\..*/','',$mfile['filename']);
            echo "{text: '" . $mfile['filename'] . ': ' . $mfile['description'] . "', value: '" . $mfile['filename'] . "', onclick: mediaonclick},\n";
        }
    }
?>
    ];
    editor.addButton('mediafiles', {
        type: 'menubutton',
        text: 'Media Files',
        menu: mediafilesmenu
    });
    editor.addMenuItem('mediafiles', {
        type: 'menuitem',
        text: 'Media Files',
        menu: mediafilesmenu
    });


    var asteriskfilesmenu = [
<?php
    $assep = array();
    $aslist=file($WeBServeRRooT . '/admin/include/content/admin/tts-sounds-list.php');
    foreach ($aslist as $asfile) {
        $asfile = rtrim($asfile);
        $astmp = array();
        $astmp = OSDpreg_split('/\//',$asfile);
        if (count($astmp)==1) {
            $assep['misc'][] = $astmp[0];
        } elseif (count($astmp)==2) {
            $assep[$astmp[0]][] = $astmp[1];
        } elseif (count($astmp)==3) {
            $assep[$astmp[0]][$astmp[1]][] = $astmp[2];
        } elseif (count($astmp)==4) {
            $assep[$astmp[0]][$astmp[1]][$astmp[2]][] = $astmp[3];
        } elseif (count($astmp)==5) {
            $assep[$astmp[0]][$astmp[1]][$astmp[2]][$astmp[3]][] = $astmp[4];
        } elseif (count($astmp)==6) {
            $assep[$astmp[0]][$astmp[1]][$astmp[2]][$astmp[3]][$astmp[4]][] = $astmp[5];
        } elseif (count($astmp)==7) {
            $assep[$astmp[0]][$astmp[1]][$astmp[2]][$astmp[3]][$astmp[4]][$astmp[5]][] = $astmp[6];
        }
    }
    foreach ($assep as $ask1 => $asv1) {
        if (is_array($asv1)) {
            echo "  {text: '".$ask1."', menu: [\n";
            foreach ($asv1 as $ask2 => $asv2) {
                if (is_array($asv2)) {
                    echo "    {text: '".$ask2."', menu: [\n";
                    foreach ($asv2 as $ask3 => $asv3) {
                        if (is_array($asv3)) {
                            echo "      {text: '".$ask3."', menu: [\n";
                            foreach ($asv3 as $ask4 => $asv4) {
                                if (is_array($asv4)) {
                                    echo "        {text: '".$ask4."', menu: [\n";
                                    foreach ($asv4 as $ask5 => $asv5) {
                                        if (is_array($asv5)) {
                                            echo "          {text: '".$ask5."', menu: [\n";
                                            foreach ($asv5 as $ask6 => $asv6) {
                                                echo "              {text: '".$asv6."', value: '".$ask1.'/'.$ask2.'/'.$ask3.'/'.$ask4.'/'.$ask5.'/'.$asv6."', onclick: asteriskonclick},\n";
                                            }
                                            echo "          ]},\n";
                                        } else
                                            echo "            {text: '".$asv5."', value: '".$ask1.'/'.$ask2.'/'.$ask3.'/'.$ask4.'/'.$asv5."', onclick: asteriskonclick},\n";
                                    }
                                    echo "        ]},\n";
                                } else
                                    echo "        {text: '".$asv4."', value: '".$ask1.'/'.$ask2.'/'.$ask3.'/'.$asv4."', onclick: asteriskonclick},\n";
                            }
                            echo "      ]},\n";
                        } else
                            echo "      {text: '".$asv3."', value: '".$ask1.'/'.$ask2.'/'.$asv3."', onclick: asteriskonclick},\n";
                    }
                    echo "    ]},\n";
                } else
                    echo "    {text: '".$asv2."', value: '".$ask1.'/'.$asv2."', onclick: asteriskonclick},\n";
            }
            echo "  ]},\n";
        } else
            echo "  {text: '".$asv1."', value: '".$ask1.'/'.$asv1."', onclick: asteriskonclick},\n";
    }
?>
    ];
    editor.addButton('asteriskfiles', {
        type: 'menubutton',
        text: 'Asterisk Files',
        menu: asteriskfilesmenu
    });
    editor.addMenuItem('asteriskfiles', {
        type: 'menuitem',
        text: 'Asterisk Files',
        menu: asteriskfilesmenu
    });

});


// Initialize TinyMCE with the new plugin and listbox
tinyMCE.init({
    plugins: '-example lists contextmenu hr code charmap advlist paste visualblocks visualchars',
    selector: "textarea",
    theme: "modern",
    menubar: false,
    skin: 'osdial',
    contextmenu: "cut copy paste | myfields myaddtlfields mediafiles asteriskfiles",
    forced_root_block: '',
    toolbar: [
        "cut copy paste | undo redo | charmap code | visualblocks visualchars",
        "myfields | myaddtlfields | mediafiles | asteriskfiles | helpb"
    ],
    setup: function(e) {
        e.on('init', function(ed) {
            tbs = tinymce.DOM.select('[role="toolbar"]');
            for (var tb in tbs) {
                tbs[tb].firstChild.style.textAlign='center';
            }
            var lastsel;
            e.getBody().onclick = function (t) {
                if (t.target.nodeName == 'SPAN') {
                    if (t.target.className && t.target.className.match('osdial_template_field|osdial_template_media')) {
                        e.stopPropagation();
                        e.selection.select(t.target,true);
                        lastsel = e.selection.getNode();
                    }
                }
            };
            e.getBody().onkeyup = function (t) {
                if (t.keyCode == 39 || t.keyCode == 37) {
                    if (e.selection.getNode().className && e.selection.getNode().className.match('osdial_template_field|osdial_template_media')) {
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
                return '<span class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
            });
            var RGmatch2 = new RegExp("\\{\\{([\\w\\:\\-\\\\/\\+ ]+)\\}\\}","g");
            ed.content = ed.content.replace(RGmatch2,function(match,p1,offset,str) {
                return '<span class="osdial_template_media" title="' + p1 + '">{{' + p1 + '}}</span> ';
            });
        });
        e.on('LoadContent', function(ed) {
            var RGmatch = new RegExp("\\[\\[(\\S+)\\]\\]","g");
            ed.content = ed.content.replace(RGmatch,function(match,p1,offset,str) {
                return '<span class="osdial_template_field" title="' + p1 + '">[[' + p1 + ']]</span> ';
            });
            var RGmatch2 = new RegExp("\\{\\{([\\w\\:\\-\\\\/\\+ ]+)\\}\\}","g");
            ed.content = ed.content.replace(RGmatch2,function(match,p1,offset,str) {
                return '<span class="osdial_template_media" title="' + p1 + '">{{' + p1 + '}}</span> ';
            });
        });
        e.on('BeforeGetContent', function(ed) {
            flds = e.dom.select('.osdial_template_field');
            for (var fld in flds) {
                thisfld = flds[fld];
                e.dom.setHTML(thisfld,'[['+thisfld.title+']] ');
            }
            flds = e.dom.select('.osdial_template_media');
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
            flds2 = e.dom.select('.osdial_template_media');
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
        $tts = get_first_record($link, 'osdial_tts', '*', sprintf("id='%s'",mres($tts_id)) );

        echo "<center><br/><font class=top_header color=$default_text size=+1>MODIFY TEXT-TO-SPEECH TEMPLATE</font><form action=$PHP_SELF method=POST name=osdial_form><br/><br/>\n";
        echo "<input type=hidden name=ADD value=41tts>\n";
        echo "<input type=hidden name=tts_id value=$tts[id]>\n";
        echo "<input type=hidden name=last_general_extension value=\"".($config['settings']['last_general_extension']+1)."\">\n";
        echo "<table class=shadedtable width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right width='30%'>ID: </td><td align=left><font color=$default_text>" . $tts['id'] . "</font></td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=tts_description size=50 maxlength=255 value=\"$tts[description]\">".helptag("tts-description")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Extension: </td><td align=left><input type=text name=tts_extension size=10 maxlength=20 value=\"$tts[extension]\" onclick=\"if (this.value=='') { this.value='".($config['settings']['last_general_extension']+1)."';}\">".helptag("tts-extension")."</td></tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Voice: </td>\n";
        echo "    <td align=left>\n";
        echo "      <select name=tts_voice>\n";
        foreach ($fest_voices as $voice) {
            $vsel=''; if ($tts['voice']==$voice) $vsel='selected'; echo "        <option value=\"$voice\" $vsel>$fest_types[$voice]</option>\n";
        }
        echo "      </select>".helptag("tts-voice")."</td>\n";
        echo "  </tr>\n";
        $tts['phrase'] = OSDpreg_replace("/\n/",'<br/>',$tts['phrase']);
        echo "<tr bgcolor=$oddrows><td align=center colspan=2><textarea name=tts_phrase rows=20 cols=120>" . $tts['phrase'] . "</textarea></td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td></tr>\n";
        echo "</table></form></center>\n";

        echo "<br/><br/>\n";

//         echo "<br/><br/><a href=\"$PHP_SELF?ADD=51tts&tts_id=$tts[id]\">DELETE TEXT-TO-SPEECH TEMPLATE</a>\n";


    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=10tts display all tts
######################
if ($ADD=="10tts") {
    if ($LOG['modify_servers']>0) {
        $srv = get_first_record($link, 'servers', '*', sprintf("server_profile IN ('AIO','DIALER') AND active='Y'") );
        echo "<center><br/><font class=top_header color=$default_text size=+1>TEXT-TO-SPEECH TEMPLATES</font><br/><br/>\n";
        echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
        echo "  <tr class=tabheader>";
        echo "    <td>ID</td>\n";
        echo "    <td>DESCRIPTION</td>\n";
        echo "    <td>EXTENSION</td>\n";
        echo "    <td>VOICE</td>\n";
        echo "    <td>CREATED</td>\n";
        echo "    <td align=center>LINKS</td>\n";
        echo "  </tr>\n";

        $c=0;
        $tts = get_krh($link, 'osdial_tts', '*','','','');
        foreach ($tts as $med) {
            echo "  <tr " . bgcolor($c++) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31tts&tts_id=$med[id]';\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=31tts&tts_id=$med[id]\">" . $med['id'] . "</a></td>\n";
            echo "    <td>$med[description]</td>\n";
            echo "    <td>$med[extension]</td>\n";
            echo "    <td title=\"" . $fest_types[$med['voice']] . "\">$med[voice]</td>\n";
            echo "    <td>$med[created]</td>\n";
            echo "    <td align=center>\n";
            echo "      <a href=\"$PHP_SELF?ADD=31tts&tts_id=$med[id]\">MODIFY</a> |\n";
            echo "      <a href=\"$PHP_SELF?ADD=51tts&tts_id=$med[id]\">DELETE</a> |\n";
            echo "      <a href=\"/voicemail/".$srv['server_ip']."/ari/gettts.php?tts_id=$med[id]\" target=\"_new\">PLAY</a> |\n";
            echo "      <a href=\"/voicemail/".$srv['server_ip']."/ari/gettts.php?tts_id=$med[id]&download=attachment\">DOWNLOAD</a>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
        }

        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=8></td>\n";
        echo "  </tr>\n";
        echo "</table></center>\n";

    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

?>
