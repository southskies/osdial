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

function ShowCompanySettings() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }
    $html='';

    $html .= "        <tr class=tabheader><td colspan=2>Multi-Company</td></tr>\n";

    $html .= "        <tr bgcolor=$oddrows>\n";
    $html .= "          <td align=right>Enable Multi-Company Support:</td>\n";
    $html .= "          <td align=left>\n";
    $html .= "            <select size=1 name=enable_multicompany>\n";
    $html .= "              <option>1</option>\n";
    $html .= "              <option>0</option>\n";
    $html .= "              <option selected>$system_settings[enable_multicompany]</option>\n";
    $html .= "            </select>\n";
    $html .= "            ".helptag("system_settings-enable_multicompany")."\n";
    $html .= "          </td>\n";
    $html .= "        </tr>\n";
    $html .= "        <tr bgcolor=$oddrows>\n";
    $html .= "          <td align=right>Multi-Company Administator:</td>\n";
    $html .= "          <td align=left>\n";
    $seladm = OSDpreg_split('/\|/',$system_settings['multicompany_admin']);
    $admusers = get_krh($link, 'osdial_users', '*','',"user_level>8 AND (LENGTH(user)<7 OR NOT SUBSTRING(user,1,3) BETWEEN '100' AND '999')",'');
    $html .= "            <select name=multicompany_admin[] size=5 multiple=\"multiple\">\n";
    foreach ($admusers as $admu) {
        $asel='';
        foreach ($seladm as $sadm) {
            if ($sadm == $admu['user']) $asel='selected';
        }
        $html .= "              <option value=\"".$admu['user']."\" $asel>".$admu['user']." - ".$admu['full_name']."</option>\n";
    }
    $html .= "            </select>\n";
    $html .= helptag("system_settings-multicompany_admin");
    $html .= "          </td>\n";
    $html .= "        </tr>\n";

    return $html;
}

?>
