<?php



###################################### Footer ########################################### 


echo "</TD></TR></TABLE></center>\n";

$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);

echo "<br><br><br><br><br>";
echo "<table width=100% cellspacing=0 cellpadding=0>";
echo "	<TR><TD ALIGN=LEFT COLSPAN=3 HEIGHT=1 BGCOLOR=#999999></TD></TR>";
echo "	<tr bgcolor=#A3C1C9>";
echo "		<td height=15 align=left width=33%><font size=0 color=navy>&nbsp;&nbsp;Script Runtime: $RUNtime sec</td>";
echo "    	<td align=center width=33%><font size=0 color=navy>Version: $admin_version</td>";
echo "    	<td align=right width=33%><font size=0 color=navy>Build: $build&nbsp;&nbsp;</td>";
echo "	</tr>";
echo "<TR><TD ALIGN=LEFT COLSPAN=3 HEIGHT=1 BGCOLOR=#666666></TD></TR>";
echo "</table>";


echo "</TD></TR>";
echo "</TABLE>";
echo "<br>";
echo "</div>";
echo "</body>";
echo "</html>";

?>
