<?php
# admin.php - OSDial
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
#
#
session_start();

# Includes
require_once("include/includes.php");


if (empty($OAC)) {
    # Main Panel Header
    require_once("include/header.php");

    # Main Panel Content
    echo "<div class=content id=content>";
}
echo "<table width=100% class=maintable bgcolor=$maintable_color cellpadding=0 cellspacing=0 align=center>\n";
echo "  <tr>\n";
echo "    <td align=left colspan=10>\n";
require_once($content);
echo "      <br /><br /><br /><br /><br />\n";
echo "    </td>\n";
echo "  </tr>\n";
echo "</table>";

echo "<script language=\"JavaScript\">\nfixChromeTableCollapse();\n</script>\n";
if (empty($OAC)) {
    echo "</div>";

    # Main Panel Footers
    require_once("include/footer.php");
}

exit;
