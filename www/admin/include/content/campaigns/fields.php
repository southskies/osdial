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



######################
# ADD=1form create form
######################
if ($ADD == "1form") {
    if ($LOGmodify_campaigns == 1) {
        if (($form_id != 'NEW') or (strlen($form_name) < 1) or (strlen($form_description) < 1) or ($form_priority < 1) or (preg_match('/[^a-zA-Z0-9]/',$form_name))) {
            echo "<br><font color=red>FORM NOT CREATED - Please go back and look at the data you entered\n";
            echo "<br>name must be between 1 and 15 characters in length, A-Z, no spaces.\n";
            echo "<br>description must be between 1 and 50 characters\n";
            echo "<br>priority must be greater than 1</font><br>\n";
            $ADD = "2form"; # go to campaign modification form below
        } else {
            $form_id = 0;
            $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', '','');
            foreach ($forms as $form) {
                if ($form['id'] > $form_id) {
                    $form_id = $form['id'];
                }
            }
            $form_id++;
            echo "<br><B><font color=$default_text>FORM CREATED: $form_id - $form_name - $form_priority</font></B>\n";
            $fcamps = join(',',$campaigns);
            if ($LOG['allowed_campaignsALL'] > 0) {
                if (preg_match('/-ALL-/',$fcamps));
            } else {
                $fcamps = $LOG['user_group']; 
            }
            $form_name = strtoupper($form_name);
            $stmt = "INSERT INTO osdial_campaign_forms (id,name,description,description2,priority,campaigns) VALUES ('$form_id','$form_name','$form_description','$form_description2','$form_priority','$fcamps');";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|CREATE FORM |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
            $id = $form_id;
            $SUB = "2fields";
            $ADD = "3fields"; # go to campaign modification form below
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=2form add a new form
######################
if ($ADD == "2form") {
    if ($LOGmodify_campaigns == 1) {
        echo "<center><br><font color=$default_text size=+1>ADDITIONAL FORM</font><br><br>\n";

        $pri = 0;
        $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'",'');
        foreach ($forms as $form) {
            if ($form['priority'] > $pri) {
                $pri = $form['priority'];
            }
        }
        $pri++;

        echo '<form action="' . $PHP_SELF . '" method="POST">';
        echo '<input type="hidden" name="ADD" value="1form">';
        echo '<input type="hidden" name="form_id" value="NEW">';

        echo "<table cellspacing=1 cellpadding=1>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$evenrows align=right>Name</td>\n";
        echo "      <td bgcolor=$evenrows><input type=\"text\" size=\"15\" maxlength=\"15\" name=\"form_name\" value=\"\"></td>";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$evenrows align=right>Description</td>\n";
        echo "      <td bgcolor=$evenrows><input type=\"text\" size=\"50\" maxlength=\"50\" name=\"form_description\" value=\"\"></td>";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$evenrows align=right>Description (Line 2)</td>\n";
        echo "      <td bgcolor=$evenrows><input type=\"text\" size=\"50\" maxlength=\"50\" name=\"form_description2\" value=\"\"></td>";
        echo "  </tr>\n";
        echo "  <tr>\n";
        echo "      <td bgcolor=$evenrows align=right>Priority:</td>\n";
        echo "      <td bgcolor=$evenrows><input type=\"text\" size=\"2\" maxlength=\"2\" name=\"form_priority\" value=\"" . $pri . "\"></td>";
        echo "  </tr>\n";
        if ($LOG['allowed_campaignsALL'] > 0) {
            echo "  <tr>\n";
            echo "      <td bgcolor=$oddrows align=right>Campaigns:</td>\n";
            echo "      <td bgcolor=$oddrows>\n";
            if ($LOG['multicomp'] == 0) {
                echo "        <input type=\"checkbox\" name=\"campaigns[]\" value=\"-ALL-\"> <b>ALL - FORM IN ALL CAMPAIGNS</b>\n";
            }
            echo "      </td>";
            echo "  </tr>\n";
            $campaigns = get_krh($link, 'osdial_campaigns', 'campaign_id,campaign_name','',sprintf('campaign_id IN %s',$LOG['allowed_campaignsSQL']),'');
            foreach ($campaigns as $camp) {
                echo "  <tr>\n";
                echo "      <td bgcolor=$oddrows align=right>&nbsp;</td>\n";
                echo "      <td bgcolor=$oddrows><input type=\"checkbox\" name=\"campaigns[]\" value=\"" . $camp['campaign_id'] . "\"> " . mclabel($camp['campaign_id']) . ' - ' . $camp['campaign_name'] . '</td>';
                echo "  </tr>\n";
            }
        }
        echo "  <tr><td colspan=2 bgcolor=$evenrows> &nbsp;</td></tr>\n";
        echo "  <tr class=tabfooter>\n";
        echo "      <td colspan=2 class=tabbutton align=center><input type=submit value=\"Create Form\"></td>\n";
        echo "  </tr>\n";
        echo "</table>\n";

        echo "</form>";
        echo "</center>";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=2fields add a new field
######################
if ($ADD == "2fields") {
    if ($LOGmodify_campaigns == 1) {
        if ((strlen($field_name) < 1) or (strlen($field_description) < 1) or ($field_length > 22) or ($field_priority < 1) or (preg_match('/[^a-zA-Z0-9]/',$field_name))) {
            echo "<br><font color=red>FIELD NOT ADDED - Please go back and look at the data you entered\n";
            echo "<br>name must be between 1 and 15 characters in length, A-Z, no spaces.\n";
            echo "<br>description must be between 1 and 50 characters in length\n";
            echo "<br>length must be between 1 and 22\n";
            echo "<br>priority must be greater than 1</font><br>\n";
        } else {
            $field_name = strtoupper($field_name);
            echo "<br><B><font color=$default_text>FIELD ADDED: $field_name</font></B>\n";
            $stmt = "INSERT INTO osdial_campaign_fields (form_id,name,description,options,length,priority) values('$form_id','$field_name','$field_description','$field_options','$field_length','$field_priority');";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|ADD A NEW FIELD|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $id = $form_id;
        $SUB = "2fields";
        $ADD = "3fields";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=4form modify fields
######################
if ($ADD == "4form") {
    if ($LOGmodify_campaigns == 1) {
        $frm = get_first_record($link, 'osdial_campaign_forms', '*', sprintf('id=%s', mres($form_id)));
        if ($LOG['allowed_campaignsALL'] < 1 and $frm['campaigns'] != $LOG['user_group']) {
            echo "<br><font color=red>FORM NOT MODIFIED - These Forms / Fields belong to ALL campaigns.\n";
            echo "<br>In order to modify Forms and Fields, the Form must be assigned to a specific Campaign in your User Group.</font><br>\n";
        } else if (($form_id < 1) or (strlen($form_name) < 1) or (strlen($form_description) < 1) or ($form_priority < 1) or (preg_match('/[^a-zA-Z0-9]/',$form_name))) {
            echo "<br><font color=red>FORM NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>name must be between 1 and 15 characters in length, A-Z, no spaces.\n";
            echo "<br>description must be between 1 and 50 characters in length\n";
            echo "<br>priority must be greater than 1</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>FORM MODIFIED: $form_name</font></B>\n";
            $fcamps = join(',',$campaigns);
            if (preg_match('/-ALL-/',$fcamps)) {
                $fcamps='ALL';
            }
            $field_name = strtoupper($field_name);
            $stmt = "UPDATE osdial_campaign_forms SET name='$form_name',description='$form_description',description2='$form_description2',priority='$form_priority',campaigns='$fcamps' where id='$form_id';";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|MODIFY FORM|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $id = $form_id;
        $SUB = "2fields";
        $ADD = "3fields"; # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=4fields modify fields
######################
if ($ADD == "4fields") {
    if ($LOGmodify_campaigns == 1) {
        $fld = get_first_record($link, 'osdial_campaign_fields', '*', sprintf('id=%s', mres($field_id)));
        $frm = get_first_record($link, 'osdial_campaign_forms', '*', sprintf('id=%s', mres($fld['form_id'])));
        if ($LOG['allowed_campaignsALL'] < 1 and $frm['campaigns'] != $LOG['user_group']) {
            echo "<br><font color=red>FIELD NOT MODIFIED - These Forms / Fields belong to ALL campaigns.\n";
            echo "<br>In order to modify Forms and Fields, the Form must be assigned to a specific Campaign in your User Group.</font><br>\n";
        } elseif (($field_id < 1) or (strlen($field_name) < 1) or (strlen($field_description) < 1) or ($field_length > 22) or ($field_priority < 1) or (preg_match('/[^a-zA-Z0-9]/',$field_name))) {
            echo "<br><font color=red>FIELD NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>name must be between 1 and 15 characters in length, A-Z, no spaces.\n";
            echo "<br>description must be between 1 and 50 characters in length\n";
            echo "<br>length must be between 1 and 22\n";
            echo "<br>priority must be greater than 1</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>FIELD MODIFIED: $field_name</font></B>\n";
            $field_name = strtoupper($field_name);
            $stmt = "UPDATE osdial_campaign_fields SET name='$field_name',description='$field_description',options='$field_options',length='$field_length',priority='$field_priority' where id='$field_id';";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|MODIFY FIELD|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $id = $frm['id'];
        $SUB = "2fields";
        $ADD = "3fields"; # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=6form delete form
######################
if ($ADD == "6form") {
    if ($LOGmodify_campaigns == 1) {
        $SUB = "";
        $frm = get_first_record($link, 'osdial_campaign_forms', '*', sprintf('id=%s', mres($form_id)));
        if ($LOG['allowed_campaignsALL'] < 1 and $frm['campaigns'] != $LOG['user_group']) {
            echo "<br><font color=red>FORM NOT DELETE - These Forms / Fields belong to ALL campaigns.\n";
            echo "<br>In order to delete Forms and Fields, the Form must be assigned to a specific Campaign in your User Group.</font><br>\n";
            $SUB = "2fields";
        } elseif ($form_id < 1) {
            echo "<br><font color=red>FORM NOT DELETED - Could not find form id!\n";
        } else {
            echo "<br><B><font color=$default_text>FORM DELETED: $form_id - $form_name</font></B>\n";
            $stmt = "UPDATE osdial_campaign_forms SET deleted='1' WHERE id='$form_id';";
            $rslt = mysql_query($stmt, $link);
            $stmt = "UPDATE osdial_campaign_fields SET deleted='1' WHERE form_id='$form_id';";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|DELETE FORM|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $id = $form_id;
        $ADD = "3fields"; # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=6fields delete field
######################
if ($ADD == "6fields") {
    if ($LOGmodify_campaigns == 1) {
        $fld = get_first_record($link, 'osdial_campaign_fields', '*', sprintf('id=%s', mres($field_id)));
        $frm = get_first_record($link, 'osdial_campaign_forms', '*', sprintf('id=%s', mres($fld['form_id'])));
        if ($LOG['allowed_campaignsALL'] < 1 and $frm['campaigns'] != $LOG['user_group']) {
            echo "<br><font color=red>FIELD NOT DELETED - These Forms / Fields belong to ALL campaigns.\n";
            echo "<br>In order to delete Forms and Fields, the Form must be assigned to a specific Campaign in your User Group.</font><br>\n";
        } elseif ($field_id < 1) {
            echo "<br><font color=red>FIELD NOT DELETED - Could not find field id!\n";
        } else {
            echo "<br><B><font color=$default_text>FIELD DELETED: $field_id - $field_name</font></B>\n";
            $stmt = "UPDATE osdial_campaign_fields SET deleted='1' WHERE id='$field_id';";
            $rslt = mysql_query($stmt, $link);
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen("./admin_changes_log.txt", "a");
                fwrite($fp, "$date|DELETE FIELD|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $id = $frm['id'];
        $SUB = "2fields";
        $ADD = "3fields"; # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=35 display all campaign forms
######################
if ($ADD == "3fields" and $SUB != '2fields') {
    echo "<center><br><font color=$default_text size=+1>ADDITIONAL FORMS & FIELDS</font><br><br>\n";

    echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "  <tr class=tabheader>\n";
    echo "    <td>NAME</td>\n";
    echo "    <td>DESCRIPTION</td>\n";
    echo "    <td align=center>LINKS</td>\n";
    echo "  </tr>\n";

    $forms = get_krh($link, 'osdial_campaign_forms', '*', 'priority', "deleted='0'",'');

    $cnt = 0;
    foreach ($forms as $form) {
        $matched=0;
        foreach ($LOG['allowed_campaigns'] as $acamp) {
            if (preg_match('/^ALL$|^' . $acamp . '$|^' . $acamp . ',|,' . $acamp . '$|,' . $acamp . ',/',$form['campaigns'])) {
                $matched++;
            }
        }
        if ($matched) {
            echo "  <tr " . bgcolor($cnt) . " class=\"row font1\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=3fields&SUB=2fields&id=" . $form['id'] . "\">" . $form['name'] . "</a></td>\n";
            echo "    <td>" . $form['description'] . "</td>\n";
            echo "    <td align=center><a href=\"$PHP_SELF?ADD=3fields&SUB=2fields&id=" . $form['id'] . "\">MODIFY FORM</a></td></tr>\n";
            $cnt++;
        }
    }
    echo "  <tr class=tabfooter>\n";
    echo "    <td colspan=3></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    echo "</center>\n";

    echo "<br /><br /><center><a href=$PHP_SELF?ADD=2form>ADD NEW FORM</a></center>";
}

######################
# ADD=35 display all campaign form & fields
######################
if ($ADD == "3fields" and $SUB == '2fields') {
    echo "<center><br><font color=$default_text size=+1>ADDITIONAL FORM</font><br><br>\n";

    $form = get_first_record($link, 'osdial_campaign_forms', '*', 'id=' . $id);

    echo '<form action="' . $PHP_SELF . '" method="POST">';
    echo '<input type="hidden" name="ADD" value="4form">';
    echo '<input type="hidden" name="form_id" value="' . $form['id'] . '">';

    echo "<table cellspacing=1 cellpadding=1>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$evenrows align=right>Name</td>\n";
    echo '      <td bgcolor=' . $evenrows . '><input type="text" size="15" maxlength="15" name="form_name" value="' . $form['name'] . '"></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$evenrows align=right>Description</td>\n";
    echo '      <td bgcolor=' . $evenrows . '><input type="text" size="50" maxlength="50" name="form_description" value="' . $form['description'] . '"></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$evenrows align=right>Description (Line 2)</td>\n";
    echo '      <td bgcolor=' . $evenrows . '><input type="text" size="50" maxlength="50" name="form_description2" value="' . $form['description2'] . '"></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$evenrows align=right>Priority:</td>\n";
    echo '      <td bgcolor=' . $evenrows . '><input type="text" size="2" maxlength="2" name="form_priority" value="' . $form['priority'] . '"></td>';
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "      <td bgcolor=$oddrows align=right>Campaigns:</td>\n";
    echo '      <td bgcolor=' . $oddrows . '>' . "\n";
    if ($LOG['multicomp'] == 0 OR $LOG['multicomp_admin'] == 1) {
        if ($form['campaigns'] == 'ALL') {
            $ac = 'checked';
        }
        if ($LOG['allowed_campaignsALL']>0) {
            echo '<input type="checkbox" name="campaigns[]" id=campaigns value="-ALL-" ' . $ac . " onclick=\"var ctmp=this.checked; for(var i=0;i<document.getElementsByName('campaigns[]').length;i++) { document.getElementsByName('campaigns[]')[i].checked=false; }; this.checked=ctmp;\"" . '><label for=campaigns> <b>ALL - FORM IN ALL CAMPAIGNS</b></label>';
        }
    }
    echo '</td>';
    echo "  </tr>\n";
    if (($LOG['allowed_campaignsALL']<1 and $form['campaigns'] != 'ALL') or $LOG['allowed_campaignsALL']>0) {
        $campaigns = get_krh($link, 'osdial_campaigns', 'campaign_id,campaign_name','',sprintf('campaign_id IN %s',$LOG['allowed_campaignsSQL']),'');
        foreach ($campaigns as $camp) {
            echo "  <tr>\n";
            echo "      <td bgcolor=$oddrows align=right>&nbsp;</td>\n";
            $cc = '';
            $fcamps = split(',',$form['campaigns']);
            foreach ($fcamps as $fcamp) {
                if ($camp['campaign_id'] == $fcamp) {
                    $cc = 'checked';
                }
            }
            echo '      <td bgcolor=' . $oddrows . '><input type="checkbox" name="campaigns[]" id=campaigns' . $camp['campaign_id'] . ' value="' . $camp['campaign_id'] . '" ' . $cc . ' onclick="document.getElementById(\'campaigns\').checked=false;"><label for=campaigns' . $camp['campaign_id'] . '> ' . mclabel($camp['campaign_id']) . ' - ' . $camp['campaign_name'] . '</label></td>';
            echo "  </tr>\n";
        }
    }
    echo "  <tr><td colspan=2 bgcolor=$evenrows>&nbsp;</td></tr>\n";
    echo "  <tr class=tabfooter>\n";
    echo "      <td align=center><a href=$PHP_SELF?ADD=6form&form_id=$id>DELETE</a></td>\n";
    echo "      <td class=tabbutton align=center><input type=submit value=\"Save Form\"></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";

    echo "</form>";

    echo "<br /><br /><hr width=50%>\n";
    echo "<center><font color=$default_text size=+1>ADDITIONAL FORM FIELDS</font><br><br>\n";
    echo "<table width=$section_width cellspacing=1 cellpadding=0 bgcolor=grey>\n";
    echo "  <tr class=tabheader>\n";
    echo "      <td align=center>NAME</td>\n";
    echo "      <td align=center>DESCRIPTION</td>\n";
    echo "      <td align=center>OPTIONS</td>\n";
    echo "      <td align=center>LENGTH</td>\n";
    echo "      <td align=center>PRIORITY</td>\n";
    echo "      <td align=center colspan=2>ACTIONS</td>\n";
    echo "  </tr>\n";
    $fields = get_krh($link, 'osdial_campaign_fields', '*', 'priority', "form_id='" . $form['id'] . "' AND deleted='0'",'');
    $cnt = 0;
    $pri = 0;
    foreach ($fields as $field) {
        echo '  <form action="' . $PHP_SELF . '" method="POST">';
        echo '  <input type="hidden" name="ADD" value="4fields">';
        echo '  <input type="hidden" name="form_id" value="' . $form['id'] . '">';
        echo '  <input type="hidden" name="field_id" value="' . $field['id'] . '">';
        echo "  <tr " . bgcolor($cnt) . " class=\"row font1\">\n";
        echo "      <td align=center class=tabinput><input type=text name=field_name size=15 maxlength=15 value=\"" . $field['name'] . "\"></td>\n";
        echo "      <td align=center class=tabinput><input type=text name=field_description size=20 maxlength=50 value=\"" . $field['description'] . "\"></td>\n";
        echo "      <td align=center class=tabinput><input type=text name=field_options size=20 maxlength=255 value=\"" . $field['options'] . "\"></td>\n";
        echo "      <td align=center class=tabinput><input type=text name=field_length size=2 maxlength=2 value=\"" . $field['length'] . "\"></td>\n";
        echo "      <td align=center class=tabinput><input type=text name=field_priority size=2 maxlength=2 value=\"" . $field['priority'] . "\"></td>\n";
        echo "      <td align=center><a href=$PHP_SELF?ADD=6fields&form_id=" . $form['id'] . "&field_id=" . $field['id'] . ">DELETE</a></td>\n";
        echo "      <td align=center class=tabbutton1><input type=submit value=\"Save\"></td>\n";
        echo "  </tr>\n";
        echo "  </form>\n";
        if ($field['priority'] > $pri) {
           $pri = $field['priority']; 
        }
        $cnt++;
    }
    $pri++;
    echo '  <form action="' . $PHP_SELF . '" method="POST">';
    echo '  <input type="hidden" name="ADD" value="2fields">';
    echo '  <input type="hidden" name="form_id" value="' . $form['id'] . '">';
    echo "  <tr class=tabfooter>\n";
    echo "      <td align=center class=tabinput><input type=text name=field_name size=15 maxlength=15 value=\"\"></td>\n";
    echo "      <td align=center class=tabinput><input type=text name=field_description size=20 maxlength=50 value=\"\"></td>\n";
    echo "      <td align=center class=tabinput><input type=text name=field_options size=20 maxlength=255 value=\"\"></td>\n";
    echo "      <td align=center class=tabinput><input type=text name=field_length size=2 maxlength=2 value=\"22\"></td>\n";
    echo "      <td align=center class=tabinput><input type=text name=field_priority size=2 maxlength=2 value=\"$pri\"></td>\n";
    echo "      <td align=center class=tabbutton1 colspan=2><input type=submit value=\"New\"></td>\n";
    echo "  </tr>\n";
    echo "  <tr><td colspan=7 class=tabfooter align=center>(options are a comma separated list that will appear as a drop-down)</td></tr>\n";
    echo "  </form>\n";
    echo "</table>\n";

}
?>
