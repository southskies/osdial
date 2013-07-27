<?php
#
# Copyright (C) 2013  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
# ADD=10exten display the ADD NEW MEDIA SCREEN
######################

if ($ADD=="11exten") {
    if ($LOG['modify_servers']>0) {
        echo "<center><br><font class=top_header color=$default_text size=+1>ADD EXTENSION</font><br><br>\n";
        echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
        echo "<input type=hidden name=ADD value=21exten>\n";

        echo "<table class=shadedtable width=$section_width cellspacing=3>\n";
        echo "  <tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=30%>Extension Type: </td>\n";
        echo "    <td align=left>\n";
        echo "    <select size=1 name=ext_type>\n";
        echo "      <option>CUSTOM</option>\n";
        echo "      <option selected>DIALPLAN</option>\n";
        echo "      <option>IVR</option>\n";
        echo "    </select>\n";
        echo helptag("extensions-ext_type")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Ext Context: </td>\n";
        echo "    <td align=left>\n";
        $contexts = array();
        $contexts['osdial']='Allow direct calling to outbound and extensions';
        $contexts['default']='Same as osdial context';
        echo editableSelectBox($contexts, 'ext_context', 'osdial', 100, 100, '');
        echo helptag("extensions-ext_context")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Extension: </td>\n";
        echo "    <td align=left><input type=text name=exten size=30 maxlength=100 value=\"\" onkeypress=\"keyextonly();\">".helptag("extensions-exten")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Name: </td>\n";
        echo "    <td align=left><input type=text name=name size=30 maxlength=50 value=\"\">".helptag("extensions-name")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right>Description: </td>\n";
        echo "    <td align=left><input type=text name=description size=50 maxlength=255 value=\"\">".helptag("extensions-description")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</table></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=21exten adds new exten to the system
######################

if ($ADD=="21exten") {
    if ($LOG['modify_servers']>0) {
        $ADD="10exten";
        $rce = get_first_record($link, 'osdial_extensions', 'count(*) AS cnt', sprintf("ext_context='%s' AND exten='%s'",mres($ext_context),mres($exten)));
        if ($rce['cnt']>0) {
            echo "<br><font color=red>EXTENSION ALREADY EXISTS - $ext_context : $exten</font>\n";
        } elseif (OSDstrlen($exten) < 1) {
            echo "<br><font color=red>EXTENSION NOT ADDED - Please go back and look at the data you entered</font>\n";
        } else {
            echo "<br><font color=$default_text>EXTENSION ADDED</font>\n";

            $stmt=sprintf("INSERT INTO osdial_extensions SET ext_type='%s',name='%s',description='%s',ext_context='%s',exten='%s';",mres($ext_type),mres($name),mres($description),mres($ext_context),mres($exten));
            $rslt=mysql_query($stmt, $link);
            $exten_id=mysql_insert_id();
            echo "<br>";
            $ADD="31exten";
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=41exten modify exten record in the system
######################
if ($ADD=="41exten") {
    if ($LOG['modify_servers']>0) {
        if ($SUB>0) {
            if ($SUB==2) {
                $stmt=sprintf("INSERT INTO osdial_extensions_data SET exten_id='%s',ext_context='%s',exten='%s',ext_priority='%s',ext_app='%s',ext_appdata='%s';",mres($exten_id),mres($ext_context),mres($exten),mres($ext_priority),mres($ext_app),mres($ext_appdata));
                $rslt=mysql_query($stmt, $link);

            } elseif($SUB==4) {
                $stmt=sprintf("UPDATE osdial_extensions_data SET ext_context='%s',exten='%s',ext_priority='%s',ext_app='%s',ext_appdata='%s' WHERE id='%s';",mres($ext_context),mres($exten),mres($ext_priority),mres($ext_app),mres($ext_appdata),mres($edata_id));
                $rslt=mysql_query($stmt, $link);

            } elseif($SUB==6) {
                $stmt=sprintf("DELETE FROM osdial_extensions_data WHERE id='%s';",mres($edata_id));
                $rslt=mysql_query($stmt, $link);

            }

        } else {
            $rce = get_first_record($link, 'osdial_extensions', 'count(*) AS cnt', sprintf("id!='%s' AND ext_context='%s' AND exten='%s'",mres($exten_id),mres($ext_context),mres($exten)));
            if ($rce['cnt']>0) {
                echo "<br><font color=red>EXTENSION NOT RENAMED - ALREADY EXISTS - $ext_context : $exten</font>\n";
            } elseif (OSDstrlen($exten) < 1) {
                echo "<br><font color=$default_text>EXTENSION NOT MODIFIED - Please go back and look at the data you entered</font>\n";
            } else {
                echo "<br><font color=$default_text>EXTENSION MODIFIED: $exten_id : $ext_type : $exten</font>\n";

                $stmt=sprintf("UPDATE osdial_extensions SET ext_type='%s',name='%s',description='%s',ext_context='%s',exten='%s',readonly='%s',selectable='%d' WHERE id='%s';",mres($ext_type),mres($name),mres($description),mres($ext_context),mres($exten),mres($ext_readonly),mres($ext_selectable),mres($exten_id));
                $rslt=mysql_query($stmt, $link);

            }
        }
        $ADD="31exten";	# go to exten modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=51exten confirmation before deletion of exten record
######################
if ($ADD=="51exten") {
    if ($LOG['modify_servers']>0) {
        echo "<br><b><font color=$default_text>EXTENSION DELETION CONFIRMATION: $exten_id - $ext_type - $exten</b>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=61exten&exten_id=$exten_id&CoNfIrM=YES\">Click here to delete extension $exten</a></font><br><br><br>\n";
        $ADD='31exten';		# go to exten modification below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=61exten delete exten record
######################
if ($ADD=="61exten") {
    if ($LOG['modify_servers']>0) {
        $stmt=sprintf("DELETE FROM osdial_extensions WHERE id='%s' LIMIT 1;",mres($exten_id));
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!!!DELETING EXTENSION!!!|$PHP_AUTH_USER|$ip|id='$exten_id'||\n");
            fclose($fp);
        }
        echo "<br><b><font color=$default_text>EXTENSION DELETION COMPLETED: $exten_id</font></b>\n";
        echo "<br><br>\n";
        $ADD='10exten';		# go to exten list
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=31exten modify exten record in the system
######################
if ($ADD=="31exten") {
    if ($LOG['modify_servers']>0) {
        $rexten = get_first_record($link, 'osdial_extensions', '*', sprintf("id='%s'",mres($exten_id)) );

        echo "<center><br>\n";
        echo "<font class=top_header color=$default_text size=+1>MODIFY EXTENSION</font>\n";
        echo "<br><br>\n";
        $onsubjs='';
        $roele='';
        $ronly=0;
        if ($DB>0) {
        } else {
            if ($rexten['readonly']=='1') {
                $roele="disabled";
                $onsubjs=" onsubmit='return false;'";
                $ronly=1;
            }
        }
        echo "<form action=$PHP_SELF method=POST $onsubjs>\n";
        echo "<input type=hidden name=ADD value=\"41exten\">\n";
        echo "<input type=hidden name=DB value=\"$DB\">\n";
        echo "<input type=hidden name=exten_id value=\"$rexten[id]\">\n";
        echo "<input type=hidden name=ext_type value=\"$rexten[ext_type]\">\n";
        echo "<input type=hidden name=ext_context value=\"$rexten[ext_context]\">\n";
        echo "<table class=shadedtable cellspacing=3 width=600>\n";
        echo "<tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right width=125>ID: </td><td align=left><font color=$default_text>" . $rexten['id'] . "</font></td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Ext Type: </td><td align=left>$rexten[ext_type]".helptag("extensions-ext_type")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Ext Context: </td><td align=left>$rexten[ext_context]".helptag("extensions-ext_context")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Extension: </td><td align=left><input type=text name=exten $roele size=50 maxlength=255 value=\"".htmlentities($rexten['exten'])."\" onkeypress=\"keyextonly();\">".helptag("extensions-exten")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Name: </td><td align=left><input type=text name=name $roele size=50 maxlength=255 value=\"".htmlentities($rexten['name'])."\">".helptag("extensions-name")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=description $roele size=50 maxlength=255 value=\"".htmlentities($rexten['description'])."\">".helptag("extensions-description")."</td></tr>\n";
        $ysel='';$nsel='';if ($rexten['selectable']=='0') { $nsel=' selected'; } else { $ysel=' selected'; }
        echo "<tr bgcolor=$oddrows><td align=right>Selectable: </td><td align=left><select size=1 $roele name=\"ext_selectable\"><option value=\"1\" $ysel>Y</option><option value=\"0\" $nsel>N</option></select>".helptag("extensions-selectable")."</td></tr>\n";
        $ysel='';$nsel='';if ($rexten['readonly']=='1') { $ysel=' selected'; } else { $nsel=' selected'; }
        echo "<tr bgcolor=$oddrows><td align=right>Readonly: </td><td align=left><select $roele name=\"ext_readonly\"><option value=\"1\" $ysel>Y</option><option value=\"0\" $nsel>N</option></select>".helptag("extensions-readonly")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit $roele VALUE=SUBMIT></td></tr>\n";
        echo "</table></form>\n";
        echo "<br><br>\n";
        if ($rexten['ext_type']=='DIALPLAN') {
            echo "<font class=top_header color=$default_text size=+1>DIALPLAN</font>\n";
            echo "<table class=shadedtable width=800 cellspacing=0 cellpadding=1>\n";
            echo "  <tr class=tabheader>";
            echo "    <td>CONTEXT</td>\n";
            echo "    <td>EXTENSION</td>\n";
            echo "    <td>PRIO</td>\n";
            echo "    <td>APP</td>\n";
            echo "    <td>DATA</td>\n";
            echo "    <td colspan=2 align=center>LINKS</td>\n";
            echo "  </tr>\n";
            $extdata = get_krh($link, 'osdial_extensions_data', '*','ext_context ASC,exten ASC,ext_priority ASC',sprintf("exten_id='%s'",$exten_id),'');
            $o=0;
            $lastprio=0;
            foreach ($extdata as $edata) {
                $o++;
                echo "<form action=$PHP_SELF method=POST $onsubjs>\n";
                echo "<input type=hidden name=ADD value=\"41exten\">\n";
                echo "<input type=hidden name=SUB value=\"4\">\n";
                echo "<input type=hidden name=DB value=\"$DB\">\n";
                echo "<input type=hidden name=edata_id value=\"$edata[id]\">\n";
                echo "<input type=hidden name=exten_id value=\"$edata[exten_id]\">\n";
		        echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
		        echo "    <td align=center class=tabinput><input type=text readonly name=ext_context $roele size=10 maxlength=50 value=\"$edata[ext_context]\"></td>\n";
		        echo "    <td align=center class=tabinput><input type=text readonly name=exten $roele size=10 maxlength=50 value=\"".htmlentities($edata['exten'])."\"></td>\n";
		        echo "    <td align=center class=tabinput><input type=text name=ext_priority $roele size=5 maxlength=5 value=\"$edata[ext_priority]\"></td>\n";
		        echo "    <td align=center class=tabinput><input type=text name=ext_app $roele size=20 maxlength=255 value=\"".htmlentities($edata['ext_app'])."\"></td>\n";
		        echo "    <td align=center class=tabinput><input type=text name=ext_appdata $roele size=50 maxlength=1000 value=\"".htmlentities($edata['ext_appdata'])."\"></td>\n";
		        echo "    <td align=center nowrap>\n";
                if ($ronly==0) {
			        echo "      <a href=\"$PHP_SELF?ADD=41exten&SUB=6&edata_id=$edata[id]&exten_id=$rexten[id]&DB=$DB\">DELETE</a>\n";
                }
                echo "    </td>\n";
		        echo "    <td align=center nowrap class=tabbutton1><input type=submit name=submit $roele value=MODIFY></td>\n";
                echo "  </tr>\n";
                echo "  </form>\n";
                $lastprio=$edata['ext_priority'];
            }
            $lastprio++;
            echo "<form action=$PHP_SELF method=POST $onsubjs>\n";
            echo "<input type=hidden name=ADD value=\"41exten\">\n";
            echo "<input type=hidden name=SUB value=\"2\">\n";
            echo "<input type=hidden name=DB value=\"$DB\">\n";
            echo "<input type=hidden name=exten_id value=\"$rexten[id]\">\n";
            echo "  <tr class=tabfooter>";
            if ($ronly==1) {
                echo "    <td colspan=7>&nbsp;</td>\n";
            } else {
		        echo "    <td align=center class=tabinput><input type=text readonly name=ext_context $roele size=10 maxlength=50 value=\"$rexten[ext_context]\"></td>\n";
		        echo "    <td align=center class=tabinput><input type=text readonly name=exten $roele size=10 maxlength=50 value=\"".htmlentities($rexten['exten'])."\"></td>\n";
		        echo "    <td align=center class=tabinput><input type=text name=ext_priority $roele size=5 maxlength=5 value=\"$lastprio\"></td>\n";
		        echo "    <td align=center class=tabinput><input type=text name=ext_app $roele size=20 maxlength=255 value=\"\"></td>\n";
		        echo "    <td align=center class=tabinput><input type=text name=ext_appdata $roele size=50 maxlength=1000 value=\"\"></td>\n";
		        echo "    <td align=center colspan=2 nowrap class=tabbutton1><input type=submit name=submit $roele value=ADD></td>\n";
            }
            echo "  </tr>\n";
            echo "  </form>\n";
            echo "</table>\n";


        } elseif ($rexten['ext_type']=='IVR') {
            $exten_id=$rexten['id'];
            $exten=$rexten['exten'];
            if (empty($IVR)) {
                $IVR="3menu";
                $SUB="2keys";
            }
            echo "<hr width=70%>\n";
            require('extensions_ivr.php');
        }

        echo "</center>\n";

        echo "<br><br>\n";

//         echo "<br><br><a href=\"$PHP_SELF?ADD=51exten&exten_id=$rexten[id]\">DELETE EXTENSION</a>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=10exten display all exten
######################
if ($ADD=="10exten") {
    if ($LOG['modify_servers']>0) {
        echo "<center><br><font class=top_header color=$default_text size=+1>EXTENSIONS</font><br>\n";
        $showall=get_variable('show_all');
        $shq="selectable!='0'";
        if ($showall) {
            echo "<a href=\"$PHP_SELF?ADD=10exten&show_all=0\">(Hide Non-Selectable)</a>";
            $shq="";
        } else {
            $rcnt = get_first_record($link, 'osdial_extensions', 'count(*) AS cnt', sprintf("selectable='0'") );
            echo "<a href=\"$PHP_SELF?ADD=10exten&show_all=1\">(Show $rcnt[cnt] Non-Selectable)</a>";
        }
        echo "<br><br>\n";
        echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
        echo "  <tr class=tabheader>";
        echo "    <td width=5%>ID</td>\n";
        echo "    <td width=10%>CONTEXT</td>\n";
        echo "    <td width=10%>EXTENSION</td>\n";
        echo "    <td width=15%>TYPE</td>\n";
        echo "    <td width=40%>NAME</td>\n";
        echo "    <td width=10%>SELECTABLE</td>\n";
        echo "    <td width=10% align=center>LINKS</td>\n";
        echo "  </tr>\n";

        $c=0;
        $rexten = get_krh($link, 'osdial_extensions', '*',"ext_context ASC,(LPAD(exten,20,' ')) ASC",$shq,'');
        foreach ($rexten as $ext) {
            echo "  <tr " . bgcolor($c++) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31exten&exten_id=$ext[id]';\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=31exten&exten_id=$ext[id]\">" . $ext['id'] . "</a></td>\n";
            echo "    <td align=center>$ext[ext_context]</td>\n";
            echo "    <td align=right>$ext[exten]</td>\n";
            echo "    <td align=center>$ext[ext_type]</td>\n";
            echo "    <td>$ext[name]</td>\n";
            $sel='N'; if ($ext['selectable']) $sel='Y';
            echo "    <td align=center>$sel</td>\n";
            echo "    <td align=center>\n";
            echo "      <a href=\"$PHP_SELF?ADD=31exten&exten_id=$ext[id]\">MODIFY</a> |\n";
            echo "      <a href=\"$PHP_SELF?ADD=51exten&exten_id=$ext[id]&ext_type=$ext[ext_type]&exten=$ext[exten]\">DELETE</a>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
        }

        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=11></td>\n";
        echo "  </tr>\n";
        echo "</table></center>\n";

        echo "<br />\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


?>
