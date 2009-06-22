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




###################################### Footer ########################################### 

# Footer is required 'as is'.
#
echo "</td></tr></table></center>\n";
echo "<br /><br /><br /><br /><br />";
echo "</td></tr>";
echo "</table>";
echo "</div>\n";

$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);

echo "<div class=\"footer\">\n";
echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "	<tr><td align=\"left\" colspan=\"4\" height=\"1\" bgcolor=\"#999999\"></td></tr>\n";
echo "	<tr bgcolor=\"$footer_color\">\n";
echo "		<td height=\"15\" align=\"left\" width=\"33%\"><font size=\"0\" color=\"#1A4349\">&nbsp;&nbsp;Copyright &#169; 2009 Call Center Service Group, LC<!-- Script Runtime: $RUNtime sec --></td>\n";
#
# NOTICE:
# Removal or modification of the following line constitutes a breach of License and doing so may result in legal action.
echo "    	<td align=\"center\" width=\"33%\"><a style=\"color:$footer_color;\" href=\"http://callcentersg.com\" target=\"_blank\"><img src=\"templates/" . $system_settings['admin_template'] . "/images/dlfoot.png\" height=\"9\" width=\"120\"></a></td>";
#
#
echo "    	<td align=\"right\" width=\"16%\"><font size=\"0\" color=\"#1A4349\">Version: $admin_version&nbsp;</td>";
echo "    	<td align=\"right\" width=\"16%\"><font size=\"0\" color=\"#1A4349\">Build: $build&nbsp;&nbsp;</td>";
echo "	</tr>";
echo "	<tr><td align=\"left\" colspan=\"4\" height=\"1\" bgcolor=\"#666666\"></td></tr>";
echo "</table>";
echo "<br />";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";

?>
