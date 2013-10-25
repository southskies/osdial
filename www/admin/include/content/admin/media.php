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



######################
# ADD=10media display the ADD NEW MEDIA SCREEN
######################

if ($ADD=="11media") {
    if ($LOG['modify_servers']>0) {
        echo "<center><br><font class=top_header color=$default_text size=+1>ADD MEDIA FILE</font><br><br>\n";
        echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
        echo "<input type=hidden name=ADD value=21media>\n";
        echo "<input type=hidden name=last_general_extension value=\"".($config['settings']['last_general_extension']+1)."\">\n";

        echo "<table class=shadedtable width=$section_width cellspacing=3>\n";
        echo "  <tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Description: </td>\n";
        echo "    <td align=left><input type=text name=media_description size=50 maxlength=100 value=\"\">".helptag("media-description")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Extension: </td>\n";
        echo "    <td align=left><input type=text name=media_extension size=10 maxlength=20 value=\"\" onclick=\"if (this.value=='') { this.value='".($config['settings']['last_general_extension']+1)."';}\">".helptag("media-extension")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Media File: </td>\n";
        echo "    <td align=left><input type=file name=recfile>".helptag("media-filename")."</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</table></form></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=21media adds new media to the system
######################

if ($ADD=="21media") {
    if ($LOG['modify_servers']>0) {
        if (OSDstrlen($media_description) < 3) {
            echo "<br><font color=red>MEDIA NOT ADDED - Please go back and look at the data you entered</font>\n";
        } else {
            echo "<br><font color=$default_text>MEDIA ADDED</font>\n";

            $recfile = $_FILES['recfile'];
            $recfiletmp = $_FILES['recfile']['tmp_name'];
            $recfilename = $_FILES['recfile']['name'];
            $recfilename = OSDpreg_replace('/ /','_',$recfilename);
            $recfilename = OSDpreg_replace('/[^-\_\.0-9a-zA-Z]/',"",$recfilename);
            $recfilename = OSDpreg_replace('/\.wav$/i','.wav',$recfilename);
            $recfilename = OSDpreg_replace('/\.gsm$/i','.gsm',$recfilename);
            $recfilename = OSDpreg_replace('/\.mp3$/i','.mp3',$recfilename);
            move_uploaded_file($recfiletmp, '/tmp/'.$recfilename);

            if (OSDpreg_match('/\.wav$/i',$recfilename)) {
                $convfile = '/tmp/CONV_'.$recfilename;
                $destfile = '/tmp/'.$recfilename;
                rename($destfile, $convfile);
                $soxtype = exec('/usr/bin/soxi -t \''.$convfile.'\'');
                if (OSDpreg_match('/wav/i',$soxtype)) {
                    $soxbit = (exec('/usr/bin/soxi -b \''.$convfile.'\'')*1);
                    $soxrate = (exec('/usr/bin/soxi -r \''.$convfile.'\'')*1);
                    $soxchan = (exec('/usr/bin/soxi -c \''.$convfile.'\'')*1);
                    $sbopt=($soxbit!=16?'-b 16':'');
                    $sropt=($soxrate!=8000?'rate 8k':'');
                    $scopt=($soxchan!=1?'remix -':'');
                    if (!empty($sbopt) or !empty($sropt) or !empty($scopt)) {
                        exec('/usr/bin/sox \''.$convfile.'\' '.$sbopt.' \''.$destfile.'\' '.$sropt.' '.$scopt);
                        if (file_exists($destfile)) {
                            unlink($convfile);
                        }
                    }
                }
                if (file_exists($convfile)) {
                        rename($convfile, $destfile);
                }
            }

            if ($recfilename != '') media_add_file($link, '/tmp/'.$recfilename, mimemap($recfilename), $media_description, $media_extension,1);
            unlink('/tmp/'.$recfilename);
            echo "<br>";

            if ($last_general_extension==$media_extension) {
                $stmt=sprintf("UPDATE system_settings SET last_general_extension='%s';",mres($last_general_extension));
                $rslt=mysql_query($stmt, $link);
            }
        }
        $ADD="10media";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=41media modify media record in the system
######################
if ($ADD=="41media") {
    if ($LOG['modify_servers']>0) {
        if (OSDstrlen($media_description) < 3) {
            echo "<br><font color=$default_text>MEDIA NOT MODIFIED - Please go back and look at the data you entered</font>\n";
        } else {
            echo "<br><font color=$default_text>MEDIA MODIFIED: $media_id : $media_filename</font>\n";

            $stmt=sprintf("UPDATE osdial_media SET description='%s',extension='%s' WHERE id='%s';",mres($media_description),mres($media_extension),mres($media_id));
            $rslt=mysql_query($stmt, $link);

            if ($last_general_extension==$media_extension) {
                $stmt=sprintf("UPDATE system_settings SET last_general_extension='%s';",mres($last_general_extension));
                $rslt=mysql_query($stmt, $link);
            }
        }
        $ADD="31media";	# go to media modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=51media confirmation before deletion of media record
######################
if ($ADD=="51media") {
    if ($LOG['modify_servers']>0) {
        echo "<br><b><font color=$default_text>MEDIA DELETION CONFIRMATION: $media_id - $media_filename - $media_extension</b>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=61media&media_id=$media_id&CoNfIrM=YES\">Click here to delete media $media_id</a></font><br><br><br>\n";
        $ADD='31media';		# go to media modification below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=61media delete media record
######################
if ($ADD=="61media") {
    if ($LOG['modify_servers']>0) {
        $stmt=sprintf("DELETE FROM osdial_media WHERE id='%s' LIMIT 1;",mres($media_id));
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!!!DELETING MEDIA!!!|$PHP_AUTH_USER|$ip|id='$media_id'||\n");
            fclose($fp);
        }
        echo "<br><b><font color=$default_text>MEDIA DELETION COMPLETED: $media_id</font></b>\n";
        echo "<br><br>\n";
        $ADD='10media';		# go to media list
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=31media modify media record in the system
######################
if ($ADD=="31media") {
    if ($LOG['modify_servers']>0) {
        $media = get_first_record($link, 'osdial_media', '*', sprintf("id='%s'",mres($media_id)) );

        echo "<center><br><font class=top_header color=$default_text size=+1>MODIFY MEDIA</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=41media>\n";
        echo "<input type=hidden name=media_id value=$media[id]>\n";
        echo "<input type=hidden name=last_general_extension value=\"".($config['settings']['last_general_extension']+1)."\">\n";
        echo "<table class=shadedtable cellspacing=3 width=600>\n";
        echo "<tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right width=125>ID: </td><td align=left><font color=$default_text>" . $media['id'] . "</font></td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>FileName: </td><td align=left>$media[filename]".helptag("media-filename")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>MimeType: </td><td align=left>$media[mimetype]".helptag("media-mimetype")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=media_description size=50 maxlength=255 value=\"$media[description]\">".helptag("media-description")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Extension: </td><td align=left><input type=text name=media_extension size=10 maxlength=20 value=\"$media[extension]\" onclick=\"if (this.value=='') { this.value='".($config['settings']['last_general_extension']+1)."';}\">".helptag("media-extension")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td colspan=2>&nbsp;</td></tr>";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";

        echo "<br><br>\n";

//         echo "<br><br><a href=\"$PHP_SELF?ADD=51media&media_id=$media[id]\">DELETE MEDIA</a>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=10media display all media
######################
if ($ADD=="10media") {
    if ($LOG['modify_servers']>0) {
        echo "<center><br><font class=top_header color=$default_text size=+1>MEDIA</font><br><br>\n";
        echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
        echo "  <tr class=tabheader>";
        echo "    <td>ID</td>\n";
        echo "    <td>FILE</td>\n";
        echo "    <td>MIME</td>\n";
        echo "    <td>DESCRIPTION</td>\n";
        echo "    <td>EXTENSION</td>\n";
        echo "    <td>CREATED</td>\n";
        echo "    <td align=center>LINKS</td>\n";
        echo "  </tr>\n";

        $c=0;
        $media = get_krh($link, 'osdial_media', '*','','','');
        foreach ($media as $med) {
            echo "  <tr " . bgcolor($c++) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31media&media_id=$med[id]';\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=31media&media_id=$med[id]\">" . $med[id] . "</a></td>\n";
            echo "    <td>$med[filename]</td>\n";
            echo "    <td>$med[mimetype]</td>\n";
            echo "    <td>$med[description]</td>\n";
            echo "    <td>$med[extension]</td>\n";
            echo "    <td>$med[created]</td>\n";
            echo "    <td align=center>\n";
            echo "      <a href=\"$PHP_SELF?ADD=31media&media_id=$med[id]\">MODIFY</a> |\n";
            echo "      <a href=\"$PHP_SELF?ADD=51media&media_id=$med[id]&media_filename=$med[filename]&media_extension=$med[extension]\">DELETE</a> |\n";
            echo "      <a href=\"/admin/getmedia.php?filename=$med[filename]&mimetype=$med[mimetype]\" target=\"_new\">PLAY</a> |\n";
            echo "      <a href=\"/admin/getmedia.php?filename=$med[filename]&mimetype=$med[mimetype]&download=1\">DOWNLOAD</a>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
        }

        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=10></td>\n";
        echo "  </tr>\n";
        echo "</table></center>\n";

        echo "<br /><div style='text-align:center;color:black;'>Use Add Media File or dial extension 8167 (pin 4321)</div>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


require_once('tts.php');

?>
