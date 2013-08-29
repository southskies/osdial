<?php
#
# Copyright (C) 2013 Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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

function ShowAcctSettings() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }
    $html='';

    $html .= "        <tr class=tabheader><td colspan=2>Accounting and Billing</td></tr>\n";

    $html .= "        <tr bgcolor=$oddrows>\n";
    $html .= "          <td align=right>Default Accouting Method:</td>\n";
    $html .= "          <td align=left>\n";
    $html .= "            <select size=1 name=default_acct_method>\n";
    $nsel='selected';
    $drsel='';
    $mtsel='';
    $masel='';
    $mtasel='';
    $mtrsel='';
    if ($system_settings['default_acct_method']=='NONE') $nsel='selected';
    if ($system_settings['default_acct_method']=='RANGE') $drsel='selected';
    if ($system_settings['default_acct_method']=='TOTAL') $mtsel='selected';
    if ($system_settings['default_acct_method']=='AVAILABLE') $masel='selected';
    if ($system_settings['default_acct_method']=='TALK') $mtasel='selected';
    if ($system_settings['default_acct_method']=='TALK_ROUNDUP') $mtrsel='selected';
    $html .= "              <option value=\"NONE\" $nsel>NONE</option>\n";
    $html .= "              <option value=\"RANGE\" $drsel>RANGE</option>\n";
    $html .= "              <option value=\"TOTAL\" $mtsel>TOTAL</option>\n";
    $html .= "              <option value=\"AVAILABLE\" $masel>AVAILABLE</option>\n";
    $html .= "              <option value=\"TALK\" $mtasel>TALK</option>\n";
    $html .= "              <option value=\"TALK_ROUNDUP\" $mtrsel>TALK_ROUNDUP</option>\n";
    $html .= "            </select>\n";
    $html .= "            ".helptag("system_settings-default_acct_method")."\n";
    $html .= "          </td>\n";
    $html .= "        </tr>\n";
    $html .= "        <tr bgcolor=$oddrows>\n";
    $html .= "          <td align=right>Default Cutoff Time:</td>\n";
    $html .= "          <td align=left><input type=text name=default_acct_cutoff size=10 maxlength=15 value=\"$system_settings[default_acct_cutoff]\">".helptag("system_settings-default_acct_cutoff")."</td>\n";
    $html .= "        </tr>\n";
    $html .= "        <tr bgcolor=$oddrows>\n";
    $html .= "          <td align=right>Default Expire Days:</td>\n";
    $html .= "          <td align=left><input type=text name=default_acct_expire_days size=10 maxlength=15 value=\"$system_settings[default_acct_expire_days]\">".helptag("system_settings-default_acct_expire_days")."</td>\n";
    $html .= "        </tr>\n";
    $html .= "        <tr bgcolor=$oddrows>\n";
    $html .= "          <td align=right>Email Warning Time:</td>\n";
    $html .= "          <td align=left><input type=text name=acct_email_warning_time size=10 maxlength=15 value=\"$system_settings[acct_email_warning_time]\">".helptag("system_settings-acct_email_warning_time")."</td>\n";
    $html .= "        </tr>\n";
    $html .= "        <tr bgcolor=$oddrows>\n";
    $html .= "          <td align=right>Email Warning Expire Days:</td>\n";
    $html .= "          <td align=left><input type=text name=acct_email_warning_expire size=10 maxlength=15 value=\"$system_settings[acct_email_warning_expire]\">".helptag("system_settings-acct_email_warning_expire")."</td>\n";
    $html .= "        </tr>\n";
    $html .= "        <tr bgcolor=$oddrows valign=top>\n";
    $html .= "          <td align=right>Email Templates:</td>\n";
    $html .= "          <td align=left>\n";
    $html .= "            <input type=button onclick=\"openNewWindow('$PHP_SELF?ADD=3email&et_id=MCABNEWCOMP');\" name=\"MCABNEWCOMP\" value=\"New Company\" style=\"width:130px;\"><br/>";
    $html .= "            <input type=button onclick=\"openNewWindow('$PHP_SELF?ADD=3email&et_id=MCABCREDWARN');\" name=\"MCABCREDWARN\" value=\"Credit Warning\" style=\"width:130px;\">&nbsp;&nbsp;";
    $html .= "            <input type=button onclick=\"openNewWindow('$PHP_SELF?ADD=3email&et_id=MCABACCTWARN');\" name=\"MCABACCTWARN\" value=\"Expire Warning\" style=\"width:130px;\"><br/>";
    $html .= "            <input type=button onclick=\"openNewWindow('$PHP_SELF?ADD=3email&et_id=MCABCREDEXP');\" name=\"MCABCREDEXP\" value=\"Credit Expired\" style=\"width:130px;\">&nbsp;&nbsp;";
    $html .= "            <input type=button onclick=\"openNewWindow('$PHP_SELF?ADD=3email&et_id=MCABACCTEXP');\" name=\"MCABACCTEXP\" value=\"Account Expired\" style=\"width:130px;\">";
    $html .= helptag("system_settings-acct_email_template");
    $html .= "          </td>\n";
    $html .= "        </tr>\n";

    return $html;
}

function ShowAcctPackages() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $edit=0;
    if ($SUB=='packageedit') $edit=1;
    $pck = get_first_record($link, 'osdial_acct_packages', '*', sprintf("id='%s'",mres($id)) );
    $html='';

    $html.= "<br><br>\n";

    $html.= "<script type=\"text/javascript\">\n";
    $html.= "function selaction(sb) {\n";
    $html.= "   for (var i=0; i<sb.options.length; i++) {\n";
    $html.= "       var actforms = document.getElementsByName(sb.options[i].text);\n";
    $html.= "       for (var i2=0; i2<actforms.length; i2++) {\n";
    $html.= "           actforms[i2].style.visibility = 'collapse';\n";
    $html.= "       }\n";
    $html.= "   }\n";
    $html.= "   var actname = sb.options[sb.selectedIndex].text;\n";
    $html.= "   var actforms = document.getElementsByName(actname);\n";
    $html.= "   for (var i=0; i<actforms.length; i++) {\n";
    $html.= "       actforms[i].style.visibility = 'visible';\n";
    $html.= "   }\n";
    $html.= "}\n";
    $html.= "</script>\n";

    $html.= "<font class=top_header color=$default_text size=+1>ACCOUNTING PACKAGE ENTRY</font>";
    $html.= "".helptag("system_settings-acct_package")."<br>\n";
    $html.= "<form action=$PHP_SELF method=POST>";
    $html.= "<input type=hidden name=ADD value=411111111111111>";
    if ($edit) {
        $html.= "<input type=hidden name=id value=$pck[id]>";
        $html.= "<input type=hidden name=SUB value=packageupdate>";
    } else {
        $html.= "<input type=hidden name=SUB value=packageadd>";
    }
    $html.= "<table class=shadedtable width=$section_width cellspacing=3>\n";
    $html.= "        <tr bgcolor=$oddrows>\n";
    $html.= "          <td align=right width=30%>Type:</td>\n";
    $html.= "          <td align=left nowrap>\n";
    $html.= "            <div style=\"display:inline;float:left;\"><select size=1 name=ptype onchange=\"fixChromeTableExpand2('DAYS'); fixChromeTableExpand2('MINUTES'); fixChromeTableExpand2('OTHER'); selaction(this); fixChromeTableCollapse();\">\n";
    $msel='selected';
    $dsel='';
    $osel='';
    if ($edit) {
        if ($pck['ptype']=='MINUTES') $msel='selected';
        if ($pck['ptype']=='DAYS') $dsel='selected';
        if ($pck['ptype']=='OTHER') $osel='selected';
    }
    $html.= "              <option value=\"MINUTES\" $msel>MINUTES</option>\n";
    $html.= "              <option value=\"DAYS\" $dsel>DAYS</option>\n";
    $html.= "              <option value=\"OTHER\" $osel>OTHER</option>\n";
    $html.= "            </select>";
    $html.= "".helptag("system_settings-acct_package_type")."</div>";
    $html.= "</td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows>\n";
    $html.= "          <td align=right>Code:</td>\n";
    $code='';
    if ($edit) $code=$pck['code'];
    $html.= "          <td align=left><input type=text name=code size=15 maxlength=255 value=\"$code\">\n";
    $html.= "".helptag("system_settings-acct_package_code")."";
    $html.= "          </td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows>\n";
    $html.= "          <td align=right>Name:</td>\n";
    $name='';
    if ($edit) $name=$pck['name'];
    $html.= "          <td align=left><input type=text name=name size=30 maxlength=255 value=\"$name\">\n";
    $html.= "".helptag("system_settings-acct_package_name")."";
    $html.= "          </td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows>\n";
    $html.= "          <td align=right>Units:</td>\n";
    $units='1';
    if ($edit) $units=$pck['units'];
    $html.= "          <td align=left><input type=text name=units size=10 maxlength=255 value=\"$units\">\n";
    $html.= "".helptag("system_settings-acct_package_units")."";
    $html.= "          </td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows name=MINUTES style=\"visibility:collapse;\">\n";
    $html.= "          <td align=right>Use Default Expire Days:</td>\n";
    $html.= "          <td align=left>\n";
    $html.= "            <div style=\"display:inline;float:left;\"><select size=1 name=use_default_expire>\n";
    $ysel='selected';
    $nsel='';
    if ($edit) {
        if ($pck['use_default_expire']=='Y') $ysel='selected';
        if ($pck['use_default_expire']=='N') $nsel='selected';
    }

    $html.= "              <option value=\"Y\" $ysel>Y</option>\n";
    $html.= "              <option value=\"N\" $nsel>N</option>\n";
    $html.= "            </select>";
    $html.= "".helptag("system_settings-acct_package_use_default_expire")."</div>";
    $html.= "</td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows name=OTHER style=\"visibility:collapse;\">\n";
    $html.= "          <td align=right>Other Action:</td>\n";
    $html.= "          <td align=left nowrap>\n";
    $html.= "            <div style=\"display:inline;float:left;\"><select size=1 name=other_action>\n";
    $bsel='selected';
    $csel='';
    $dsel='';
    if ($edit) {
        if ($pck['other_action']=='') $bsel='selected';
        if ($pck['other_action']=='CREATE_NEW_COMPANY') $csel='selected';
        if ($pck['other_action']=='DISABLE_COMPANY') $dsel='selected';
    }
    $html.= "              <option value=\"\" $bsel>[NOT SELECTED]</option>\n";
    $html.= "              <option value=\"CREATE_NEW_COMPANY\" $csel>Create New Company</option>\n";
    $html.= "              <option value=\"DISABLE_COMPANY\" $dsel>Disable Company</option>\n";
    $html.= "            </select>";
    $html.= "".helptag("system_settings-acct_package_other_action")."</div>";
    $html.= "</td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows name=OTHER style=\"visibility:collapse;\">\n";
    $html.= "          <td align=right>NewComp Acct Method:</td>\n";
    $html.= "          <td align=left nowrap>\n";
    $html.= "            <div style=\"display:inline;float:left;\"><select size=1 name=other_newcomp_acct_method>\n";
    $dsel='selected';
    $nsel='';
    $drsel='';
    $mtsel='';
    $masel='';
    $mtasel='';
    $mtrsel='';
    if ($edit) {
        if ($pck['other_newcomp_acct_method']=='') $dsel='selected';
        if ($pck['other_newcomp_acct_method']=='NONE') $nsel='selected';
        if ($pck['other_newcomp_acct_method']=='RANGE') $drsel='selected';
        if ($pck['other_newcomp_acct_method']=='TOTAL') $mtsel='selected';
        if ($pck['other_newcomp_acct_method']=='AVAILABLE') $masel='selected';
        if ($pck['other_newcomp_acct_method']=='TALK') $mtasel='selected';
        if ($pck['other_newcomp_acct_method']=='TALK_ROUNDUP') $mtrsel='selected';
    }
    $html.= "              <option value=\"\" $dsel>DEFAULT: " . $system_settings['default_acct_method'] . "</option>\n";
    $html.= "              <option value=\"NONE\" $nsel>NONE</option>\n";
    $html.= "              <option value=\"RANGE\" $drsel>RANGE</option>\n";
    $html.= "              <option value=\"TOTAL\" $mtsel>TOTAL</option>\n";
    $html.= "              <option value=\"AVAILABLE\" $masel>AVAILABLE</option>\n";
    $html.= "              <option value=\"TALK\" $mtasel>TALK</option>\n";
    $html.= "              <option value=\"TALK_ROUNDUP\" $mtrsel>TALK_ROUNDUP</option>\n";
    $html.= "            </select>";
    $html.= "".helptag("system_settings-acct_package_newcomp_method")."</div>";
    $html.= "</td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows name=OTHER style=\"visibility:collapse;\">\n";
    $html.= "          <td align=right>NewComp Initial Units:</td>\n";
    $other_newcomp_initial_units='0';
    if ($edit) $other_newcomp_initial_units=$pck['other_newcomp_initial_units'];
    $html.= "          <td align=left><input type=text name=other_newcomp_initial_units size=10 maxlength=255 value=\"$other_newcomp_initial_units\">\n";
    $html.= "".helptag("system_settings-acct_package_newcomp_initial_units")."";
    $html.= "          </td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows>\n";
    $html.= "          <td align=right>Base Cost:</td>\n";
    $base_cost='0.00';
    if ($edit) $base_cost=$pck['base_cost'];
    $html.= "          <td align=left><input type=text name=base_cost size=20 maxlength=255 value=\"$base_cost\">\n";
    $html.= "".helptag("system_settings-acct_package_base_cost")."";
    $html.= "          </td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows name=DAYS style=\"visibility:collapse;\">\n";
    $html.= "          <td align=right>Recurring:</td>\n";
    $html.= "          <td align=left nowrap>\n";
    $html.= "            <div style=\"display:inline;float:left;\"><select size=1 name=recurring>";
    $nsel='selected';
    $ysel='';
    if ($edit) {
        if ($pck['recurring']=='Y') $ysel='selected';
        if ($pck['recurring']=='N') $nsel='selected';
    }
    $html.= "              <option value=\"Y\" $ysel disabled>Y</option>";
    $html.= "              <option value=\"N\" $nsel>N</option>";
    $html.= "            </select>";
    $html.= "".helptag("system_settings-acct_package_recurring")."</div>";
    $html.= "</td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows name=DAYS style=\"visibility:collapse;\">\n";
    $html.= "          <td align=right>Recurring Days:</td>\n";
    $recurring_days='0';
    if ($edit) $recurring_days=$pck['recurring_days'];
    $html.= "          <td align=left><input type=text name=recurring_days size=10 maxlength=255 value=\"$recurring_days\" disabled>\n";
    $html.= "".helptag("system_settings-acct_package_recurring_days")."";
    $html.= "          </td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows>\n";
    $html.= "          <td align=right>Active:</td>\n";
    $html.= "          <td align=left nowrap>\n";
    $html.= "            <div style=\"display:inline;float:left;\"><select size=1 name=active>\n";
    $ysel='selected';
    $nsel='';
    if ($edit) {
        if ($pck['active']=='Y') $ysel='selected';
        if ($pck['active']=='N') $nsel='selected';
    }
    $html.= "              <option value=\"Y\" $ysel>Y</option>\n";
    $html.= "              <option value=\"N\" $nsel>N</option>\n";
    $html.= "            </select></div>";
    $html.= "</td>\n";
    $html.= "        </tr>\n";

    $html.= "        <tr bgcolor=$oddrows>\n";
    $html.= "          <td align=right>Last Updated:</td>\n";
    $html.= "          <td align=left><input type=text name=updated size=20 maxlength=255 value=\"$pck[updated]\" disabled></td>\n";
    $html.= "        </tr>\n";
    $html.= "        <tr bgcolor=$oddrows>\n";
    $html.= "          <td align=right>Created:</td>\n";
    $html.= "          <td align=left><input type=text name=created size=20 maxlength=255 value=\"$pck[created]\" disabled></td>\n";
    $html.= "        </tr>\n";

    $html.= "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
    $html.= "</table></form>\n";

    $html.= "<br><br>\n";

    $html.= "<font class=top_header color=$default_text size=+1>ACCOUNTING PACKAGES</font>\n";
    $html.= "".helptag("system_settings-acct_package")."<br>\n";
    $html.= "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
    $html.= "  <tr class=tabheader>";
    $html.= "    <td width=3%>ID</td>\n";
    $html.= "    <td width=11%>CODE</td>\n";
    $html.= "    <td width=9%>TYPE</td>\n";
    $html.= "    <td width=18%>NAME</td>\n";
    $html.= "    <td width=5%>UNITS</td>\n";
    $html.= "    <td width=20%>OTHER</td>\n";
    $html.= "    <td width=7%>EXPIRE</td>\n";
    $html.= "    <td width=8%>COST</td>\n";
    $html.= "    <td width=5%>ACTIVE</td>\n";
    $html.= "    <td width=7%>UPDATED</td>\n";
    $html.= "    <td width=7%>CREATED</td>\n";
    $html.= "  </tr>\n";
    $c=0;
    $packs = get_krh($link, 'osdial_acct_packages', '*','','','');
    foreach ($packs as $pck) {
        $html.= "  <tr " . bgcolor($c++) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=$ADD&SUB=packageedit&id=$pck[id]';\">\n";
        $html.= "    <td><a href=$PHP_SELF?ADD=$ADD&SUB=packageedit&id=$pck[id]>$pck[id]</a></td>\n";
        $html.= "    <td>$pck[code]</td>\n";
        $html.= "    <td>$pck[ptype]</td>\n";
        $html.= "    <td>$pck[name]</td>\n";
        $html.= "    <td align=right>$pck[units]</td>\n";
        $html.= "    <td align=center>";
        if ($pck['ptype']=='OTHER') {
            $html .= $pck['other_action'];
            if ($pck['other_action']=='CREATE_NEW_COMPANY') {
                if ($pck['other_newcomp_acct_method']=='') $pck['other_newcomp_acct_method']='DEFAULT';
                $html .= ':'.$pck['other_newcomp_acct_method'];
                $html .= ':'.$pck['other_newcomp_initial_units'];
            }
        }
        $html.= "    </td>\n";
        $html.= "    <td align=center>$pck[use_default_expire]</td>\n";
        $html.= "    <td align=right>$pck[base_cost]</td>\n";
        $html.= "    <td align=center>$pck[active]</td>\n";
        $html.= "    <td align=right>".OSDsubstr($pck['updated'],0,10)."</td>\n";
        $html.= "    <td align=right>".OSDsubstr($pck['created'],0,10)."</td>\n";
        $html.= "  </tr>\n";
    }
    $html.= "</table>\n";
    $html.= "<script type=\"text/javascript\">\n";
    $html.= "fixChromeTableCollapse();\n";
    $html.= "</script>\n";

    return $html;
}

function AddUpdateAcctPackages() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $stmt='';
    if (!empty($code) and !empty($name)) {
        if ($SUB=='packageadd' and !empty($code) and !empty($name)) {
            $stmt = sprintf("INSERT INTO osdial_acct_packages SET code='%s',ptype='%s',name='%s',units='%s',use_default_expire='%s',other_action='%s',other_newcomp_acct_method='%s',other_newcomp_initial_units='%s',base_cost='%s',recurring='%s',recurring_days='%s',active='%s',created=NOW();",mres($code),mres($ptype),mres($name),mres($units),mres($use_default_expire),mres($other_action),mres($other_newcomp_acct_method),mres($other_newcomp_initial_units),mres($base_cost),mres($recurrring),mres($recurring_days),mres($active));
            $rslt=mysql_query($stmt, $link);
        } elseif ($SUB=='packageupdate') {
            $stmt = sprintf("UPDATE osdial_acct_packages SET code='%s',ptype='%s',name='%s',units='%s',use_default_expire='%s',other_action='%s',other_newcomp_acct_method='%s',other_newcomp_initial_units='%s',base_cost='%s',recurring='%s',recurring_days='%s',active='%s' WHERE id='%s';",mres($code),mres($ptype),mres($name),mres($units),mres($use_default_expire),mres($other_action),mres($other_newcomp_acct_method),mres($other_newcomp_initial_units),mres($base_cost),mres($recurrring),mres($recurring_days),mres($active),mres($id));
            $rslt=mysql_query($stmt, $link);
        }
    }
}

?>
