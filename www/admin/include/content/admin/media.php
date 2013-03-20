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
        echo "<center><br><font color=$default_text size=+1>ADD NEW MEDIA</font><br><br>\n";
        echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
        echo "<input type=hidden name=ADD value=21media>\n";

        echo "<table width=$section_width cellspacing=3>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Description: </td>\n";
        echo "    <td align=left><input type=text name=media_description size=50 maxlength=100 value=\"\">$NWB#media-description$NWE</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Extension: </td>\n";
        echo "    <td align=left><input type=text name=media_extension size=10 maxlength=20 value=\"\">$NWB#media-extension$NWE</td>\n";
        echo "  </tr>\n";
        echo "  <tr bgcolor=$oddrows>\n";
        echo "    <td align=right width=50%>Media File: </td>\n";
        echo "    <td align=left><input type=file name=recfile>$NWB#media-filename$NWE</td>\n";
        echo "  </tr>\n";
        echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</table></center>\n";
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
            rename($recfiletmp, '/tmp/'.$recfilename);

            if ($recfilename != '') media_add_file($link, '/tmp/'.$recfilename, mimemap($recfilename), $media_description, $media_extension,1);
            unlink('/tmp/'.$recfilename);
            echo "<br>";
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
        echo "<table cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>ID: </td><td align=left><font color=$default_text>" . $media['id'] . "</font></td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>FileName: </td><td align=left>$media[filename]$NWB#media-filename$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>MimeType: </td><td align=left>$media[mimetype]$NWB#media-mimetype$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=media_description size=50 maxlength=255 value=\"$media[description]\">$NWB#media-description$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Extension: </td><td align=left><input type=text name=media_extension size=10 maxlength=20 value=\"$media[extension]\">$NWB#media-description$NWE</td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";

        echo "<br><br>\n";

        echo "<br><br><a href=\"$PHP_SELF?ADD=51media&media_id=$media[id]\">DELETE MEDIA</a>\n";
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
        echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
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

        echo "<center>You may upload or dial extension 8167 (pin 4321).</center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


require_once('tts.php');

?>
