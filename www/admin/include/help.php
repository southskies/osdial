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

$NWB = " &nbsp; <span onmouseover=\"this.style.cursor='help';\" onmouseout=\"this.style.cursor='auto';\" onclick=\"openNewWindow('$PHP_SELF?ADD=99999'+this.getAttribute('helpitem'));\" helpitem=\"";
$NWE = "\"><img src=\"help.gif\" width=20 height=20 border=0 alt=\"Help\" align=top></span>";

function helptag($helpphrase) {
	global $helpdata;
	$helpwidth = '600px';
	$helpphrase = OSDpreg_replace('/^#/','',$helpphrase);
	list($helpsection,$helpitem) = OSDpreg_split('/\-/',$helpphrase);
	$helpid = 'H'.$helpphrase.rand(0,99999);
	$ret = "&nbsp;&nbsp;<span class=\"help\" ";
	$ret .= "onclick=\"var hp = document.getElementById('$helpid');if (helppopon==0) {helppopon=1;hp.style.width='$helpwidth';hp.classList.remove('helptranshidden');hp.classList.add('helptransvisible');var mwid=(document.body.offsetWidth/2)-(hp.offsetWidth/2);hp.parentNode.style.left=hp.parentNode.parentNode.offsetLeft*-1+mwid;} else {helppopon=0;var hp2=document.getElementsByClassName('helppopup');for (var i=0;i<hp2.length;i++){if (hp2[i].classList.contains('helptransvisible')) {hp2[i].classList.remove('helptransvisible');hp2[i].classList.add('helptranshidden');}}}\" ";
	$ret .= "onmouseover=\"if (helppopon==0) {var hp = document.getElementById('$helpid');hp.style.width='$helpwidth';hp.classList.remove('helptranshidden');hp.classList.add('helptransvisible');var mwid=(document.body.offsetWidth/2)-(hp.offsetWidth/2);hp.parentNode.style.left=hp.parentNode.parentNode.offsetLeft*-1+mwid;}\" ";
	$ret .= "onmouseout=\"if (helppopon==0) {var hp = document.getElementById('$helpid'); hp.classList.remove('helptransvisible');hp.classList.add('helptranshidden');}\" ";
	$ret .= "ondblclick=\"openNewWindow('$PHP_SELF?ADD=99999'+this.getAttribute('helpitem'));\" helpitem=\"#$helpphrase\">";
	$ret .= "<img src=\"help.png\" width=20 height=20 border=0 alt=\"Help\" align=top>";
	$ret .= "<span class=\"helpcontainer\"><span id=\"$helpid\" class=\"helppopup helptranshidden\">";
	$helptag = $helpdata['admin_overview']['children']['admin_modules']['children'][$helpsection]['children'][$helpitem];
	$ret .= "<b>".$helptag['title'] . "</b><br/>". $helptag['text'];
	$ret .= "</span></span></span>";
	return $ret;
}

######################
# ADD=99999 display the HELP SCREENS
######################

if ($ADD==99999) {
	header ("Content-type: text/html; charset=utf-8");
	#$data = file_get_contents("include/help.xml");
	#$xml = new SimpleXMLElement($data);
	#$helpdata = genhelpdata($xml);

	echo "<html>\n";
	echo "  <head>\n";
	echo "   <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
	echo "   <link rel=\"stylesheet\" type=\"text/css\" href=\"templates/".$systems_settings['admin_template']."/styles.css\" media=\"screen\">\n";
	echo "   <title>OSDial Administrator: REPORTS</title>\n";
	echo "  </head>\n";
	echo "  <body bgcolor=\"white\" marginheight=\"0\" marginwidth=\"0\" leftmargin=\"0\" topmargin=\"0\">\n";
	echo "    <center><br/>\n";
	echo "    <table frame='box' width='98%' bgcolor='#afcfd7' cellpadding='2' cellspacing='0'>\n";
	echo "      <tr><td align=center><font face=\"dejavu sans,verdana,sans-serif\" color=\"1c4754\" size=\"4\"><br/><b>OSDial Help</b></font></td></tr>\n";
	echo "      <tr><td><font face=\"dejavu sans,verdana,sans-serif\" color=\"1c4754\" size=\"2\"><br/><br/>\n";

	foreach ($helpdata['admin_overview']['children']['admin_modules']['children'] as $section) {
		echo "<br/><br/><b><font size=3>".$section['title']."</font></b><br/><br/>\n";
		foreach ($section['children'] as $item) {
			echo "<a name=\"".$section['pathId']."-".$item['pathId']."\"><b>".$item['title']." -</b></a>".$item['text']."<br/><br/>\n";
		}
	}

	echo "      <br/><br/></td></tr>\n";
	echo "      <tr><td style=\"text-align:center;color:#1C4754;size=4;\"> - End - <br/><br/></td></tr>\n";
	echo "    </table>\n";
	echo "    </center>\n";
	echo "  </body>\n";
	echo "</html>\n";

	exit;
}
?>
