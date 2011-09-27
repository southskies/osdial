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
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_clb_arctic_hts")) $fest_voices[] = "voice_nitech_us_clb_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_awb_arctic_hts")) $fest_voices[] = "voice_nitech_us_awb_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_rms_arctic_hts")) $fest_voices[] = "voice_nitech_us_rms_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_jmk_arctic_hts")) $fest_voices[] = "voice_nitech_us_jmk_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_bdl_arctic_hts")) $fest_voices[] = "voice_nitech_us_bdl_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/us/nitech_us_slt_arctic_hts")) $fest_voices[] = "voice_nitech_us_slt_arctic_hts";
if (file_exists("/usr/share/festival/lib/voices/english/kal_diphone")) $fest_voices[] = "voice_kal_diphone";
if (file_exists("/usr/share/festival/lib/voices/english/ked_diphone")) $fest_voices[] = "voice_ked_diphone";
if (file_exists("/usr/share/festival/lib/voices/es/JuntaDeAndalucia_es_pa_diphone")) $fest_voices[] = "voice_JuntaDeAndalucia_es_pa_diphone";
if (file_exists("/usr/share/festival/lib/voices/es/JuntaDeAndalucia_es_sf_diphone")) $fest_voices[] = "voice_JuntaDeAndalucia_es_sf_diphone";
$fest_types = array();
$fest_types['voice_nitech_us_awb_arctic_hts'] = 'Scottish-accent US English male speaker "AWB"';
$fest_types['voice_nitech_us_bdl_arctic_hts'] = 'US English male speaker "BDL"';
$fest_types['voice_nitech_us_clb_arctic_hts'] = 'US English female speaker "CLB"';
$fest_types['voice_nitech_us_jmk_arctic_hts'] = 'Canadian-accent US English male speaker "JMK"';
$fest_types['voice_nitech_us_rms_arctic_hts'] = 'US English male speaker "RMS"';
$fest_types['voice_nitech_us_slt_arctic_hts'] = 'US English female speaker "SLT"';
$fest_types['voice_kal_diphone'] = 'American English male speaker "Kevin"';
$fest_types['voice_ked_diphone'] = 'American English male speaker "Kurt"';
$fest_types['voice_JuntaDeAndalucia_es_pa_diphone'] = 'Male Spanish voice "PAL"';
$fest_types['voice_JuntaDeAndalucia_es_sf_diphone'] = 'Female Spanish voice "SFL"';




######################
# ADD=10tts display the ADD NEW TTS SCREEN
######################

if ($ADD=="11tts") {
    if ($LOG['modify_servers']>0) {
        echo "<center><br/><font color=$default_text size=+1>ADD NEW TEXT-TO-SPEECH TEMPLATE</font><br/><br/>\n";
        echo '<form action="' . $PHP_SELF . '" method="POST" name=osdial_form enctype="multipart/form-data">';
        echo "<input type=hidden name=ADD value=21tts>\n";

        echo "<table width=$section_width cellspacing=3>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Description: </td>\n";
        echo "    <td align=left><input type=text name=tts_description size=50 maxlength=100 value=\"\">$NWB#tts-description$NWE</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Extension: </td>\n";
        echo "    <td align=left><input type=text name=tts_extension size=10 maxlength=20 value=\"\">$NWB#tts-extension$NWE</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Voice: </td>\n";
        echo "    <td align=left>\n";
        echo "      <select name=tts_voice>\n";
        foreach ($fest_voices as $voice) {
            echo "        <option value=\"$voice\">$fest_types[$voice]</option>\n";
        }
        echo "      </select>$NWB#tts-voice$NWE</td>\n";
        echo "  </tr>\n";
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

                $tts_phrase = OSDpreg_replace('/&nbsp;/',' ',htmlspecialchars_decode(strip_tags(OSDpreg_replace('/(\<br\/\>|\<br\>)/',"\n",$tts_phrase))));
                $stmt=sprintf("UPDATE osdial_tts SET description='%s',extension='%s',phrase='%s',voice='%s' WHERE id='%s';",mres($tts_description),mres($tts_extension),mres($tts_phrase),mres($tts_voice),mres($tts_id));
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
        $tts = get_first_record($link, 'osdial_tts', '*', sprintf("id='%s'",mres($tts_id)) );

        echo "<center><br/><font color=$default_text size=+1>MODIFY TEXT-TO-SPEECH TEMPLATE</font><form action=$PHP_SELF method=POST name=osdial_form id=osdial_form enctype=\"multipart/form-data\"><br/><br/>\n";
        echo "<input type=hidden name=ADD value=41tts>\n";
        echo "<input type=hidden name=tts_id value=$tts[id]>\n";
        echo "<table width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>ID: </td><td align=left><font color=$default_text>" . $tts['id'] . "</font></td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=tts_description size=50 maxlength=255 value=\"$tts[description]\">$NWB#tts-description$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Extension: </td><td align=left><input type=text name=tts_extension size=10 maxlength=20 value=\"$tts[extension]\">$NWB#tts-description$NWE</td></tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Voice: </td>\n";
        echo "    <td align=left>\n";
        echo "      <select name=tts_voice>\n";
        foreach ($fest_voices as $voice) {
            $vsel=''; if ($tts['voice']==$voice) $vsel='selected'; echo "        <option value=\"$voice\" $vsel>$fest_types[$voice]</option>\n";
        }
        echo "      </select>$NWB#tts-voice$NWE</td>\n";
        echo "  </tr>\n";
        $tts['phrase'] = OSDpreg_replace("/\n/",'<br/>',$tts['phrase']);
        echo "<tr bgcolor=$oddrows><td align=center colspan=2><div id=ttscon name=ttscon class=ttscon>Phrase<br/><textarea name=tts_phrase rows=20 cols=120>" . $tts['phrase'] . "</textarea></div></td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit value=SUBMIT></td></tr>\n";
        echo "</table></form></center>\n";

        echo "<br/><br/>\n";

        echo "<br/><br/><a href=\"$PHP_SELF?ADD=51tts&tts_id=$tts[id]\">DELETE TEXT-TO-SPEECH TEMPLATE</a>\n";
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
                        window.open('/admin/admin.php?ADD=99999#tts-phrase','','width=800,height=500,scrollbars=yes,menubar=yes,address=yes');
                     }
                });
                return helpb;
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
                        tinyMCE.activeEditor.selection.setContent('[[' + v + ']]');
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
                     }
                });
<?php
    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'",'');
    foreach ($forms as $form) {
        $fcamps = OSDpreg_split('/,/',$form['campaigns']);
        foreach ($fcamps as $fcamp) {
            $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "deleted='0' AND form_id='" . $form['id'] . "'",'');
            foreach ($fields as $field) {
                echo "      mlbaf.add('" . $form['name'] . '_' . $field['name'] . "','" . $form['name'] . '_' . $field['name'] . "');\n";
            }
        }
    }
?>
                return mlbaf;
            case 'mediafiles':
                var mflist = cm.createListBox('mediafiles', {
                     title : 'Media Files',
                     onselect : function(v) {
                        tinyMCE.activeEditor.focus();
                        tinyMCE.activeEditor.selection.setContent('{{' + v + '}}');
                        tinyMCE.activeEditor.controlManager.get('mediafiles').set(-1);
                     }
                });
<?php
    $mflist = get_krh($link, 'osdial_media', 'filename,description','','','');
    if (is_array($mflist)) {
        foreach ($mflist as $mfile) {
            $mfile['filename'] = OSDpreg_replace('/.*\/|\..*/','',$mfile['filename']);
            echo "      mflist.add('" . $mfile['filename'] . ": " . $mfile['description'] . "','" . $mfile['filename'] . "');\n";
        }
    }
?>
                return mflist;
            case 'asmenumb':
                var c = cm.createListBox('asmenumb', {
                     title : 'Asterisk Files',
                     max_height : 200
                });
                var dm = cm.createDropMenu('asmenudm',{title : 'Asterisk Files' });
                c.add("Select Sound File...","select_file");
                c.onRenderMenu.add(function(c, m) {
                    m.settings['max_height'] = 300;
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
        }
    }
    $tcnt=0;
    foreach ($assep as $ask1 => $asv1) {
        if (is_array($asv1)) {
            echo "    var as1sub$tcnt = m.addMenu({id : 'as1sub$tcnt', title : '$ask1' });\n";
            echo "    as1sub$tcnt.settings['max_height'] = 300;\n";
            $tcnt2=0;
            foreach ($asv1 as $ask2 => $asv2) {
                if (is_array($asv2)) {
                    echo "    var as2sub$tcnt2 = as1sub$tcnt.addMenu({id : 'as2sub$tcnt2', title : '$ask2'});\n";
                    echo "    as2sub$tcnt2.settings['max_height'] = 300;\n";
                    $tcnt3=0;
                    foreach ($asv2 as $ask3 => $asv3) {
                        if (is_array($asv3)) {
                            echo "    var as3sub$tcnt3 = as2sub$tcnt2.addMenu({id : 'as3sub$tcnt3', title : '$ask3'});\n";
                            echo "    as3sub$tcnt3.settings['max_height'] = 300;\n";
                            $tcnt4=0;
                            foreach ($asv3 as $ask4 => $asv4) {
                                if (is_array($asv4)) {
                                    echo "    var as4sub$tcnt4 = as3sub$tcnt3.addMenu({id : 'as4sub$tcnt4', title : '$ask4'});\n";
                                    echo "    as4sub$tcnt4.settings['max_height'] = 300;\n";
                                    $tcnt5=0;
                                    foreach ($asv4 as $ask5 => $asv5) {
                                        if (is_array($asv5)) {
                                            echo "    var as5sub$tcnt5 = as4sub$tcnt4.addMenu({id : 'as5sub$tcnt5', title : '$ask5'});\n";
                                            echo "    as5sub$tcnt5.settings['max_height'] = 300;\n";
                                        } else {
                                            echo "    as4sub$tcnt4.add({title : '$asv5', onclick : function() { asmenusel('$ask1/$ask2/$ask3/$asv5'); } });\n";
                                        }
                                        $tcnt5++;
                                    }
                                } else {
                                    echo "    as3sub$tcnt3.add({title : '$asv4', onclick : function() { asmenusel('$ask1/$ask2/$ask3/$asv4'); } });\n";
                                }
                                $tcnt4++;
                            }
                        } else {
                            echo "    as2sub$tcnt2.add({title : '$asv3', onclick : function() { asmenusel('$ask1/$ask2/$asv3'); } });\n";
                        }
                        $tcnt3++;
                    }
                } else {
                    echo "    as1sub$tcnt.add({title : '$asv2', onclick : function() { asmenusel('$ask1/$asv2'); } });\n";
                }
                $tcnt2++;
            }
        } else {
            echo "    m.add({title : '$asv1', onclick : function() { asmenusel('$ask1/$asv1'); } });\n";
        }
        $tcnt++;
    }
?>
                });
                c.renderMenu();
                return c;
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
    editor_deselector : "NoEditor",
    theme : "advanced",
    theme_advanced_buttons1 : "separator,cut,copy,paste,separator,undo,redo,separator,s0,separator,myfields,separator,myaddtlfields,separator,separator,mediafiles,separator,asmenumb,separator,s1,separator,separator,helpb,separator",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "center",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    constrain_menus : true,
    height : '250px',
    width : '700px',
    oninit : ttsformat
});


function asmenusel(asms) {
    tinyMCE.activeEditor.focus();
    tinyMCE.activeEditor.selection.setContent('{{'+asms+'}}');
}

function ttsformat() {
    tinyMCE.activeEditor.focus();
    tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.dom.select('p')[0]);
    tinymce.activeEditor.formatter.register('ttsformat', {
        inline : 'span',
        styles : {fontSize : '16px'}
    });

    tinymce.activeEditor.formatter.apply('ttsformat');
    tinyMCE.activeEditor.selection.setCursorLocation(tinyMCE.activeEditor.dom.select('p')[0],0);
}

</script>

<?php


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
        echo "<center><br/><font color=$default_text size=+1>TEXT-TO-SPEECH TEMPLATES</font><br/><br/>\n";
        echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
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
