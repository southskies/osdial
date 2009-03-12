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
echo "</TD></TR></TABLE></center>\n";

$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);

echo "<br><br><br><br><br>";
echo "<table width=100% cellspacing=0 cellpadding=0>";
echo "	<TR><TD ALIGN=LEFT COLSPAN=4 HEIGHT=1 BGCOLOR=#999999></TD></TR>";
echo "	<tr bgcolor=#A3C1C9>";
echo "		<td height=15 align=left width=33%><font size=0 color='#1A4349'>&nbsp;&nbsp;Script Runtime: $RUNtime sec</td>";
echo "    	<td align=center width='33%'><a style='color:#A3C1C9;' href='http://callcentersg.com' target='_blank'><img src='images/dlfoot.png' height='9' width='120'></a></td>";
echo "    	<td align=right width='16%'><font size=0 color='#1A4349'>Version: $admin_version&nbsp;</td>";
echo "    	<td align=right width='16%'><font size=0 color='#1A4349'>Build: $build&nbsp;&nbsp;</td>";
echo "	</tr>";
echo "	<TR><TD ALIGN=LEFT COLSPAN=4 HEIGHT=1 BGCOLOR=#666666></TD></TR>";
echo "</table>";


echo "</TD></TR>";
echo "</TABLE>";
echo "<br>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
