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


####  System Statuses




######################
# ADD=221111111111111 adds the new system status to the system
######################

if ($ADD==221111111111111) {
    if ($stage=='extended') {
        $ose = get_first_record($link,'osdial_statuses_extended','count(*) AS cnt',sprintf("parents='%s' AND status='%s'",mres($parents),mres($status)));
        if ($ose['cnt'] > 0) {
            echo "<br><font color=red>SYSTEM EXTENDED STATUS NOT ADDED - there is already a global-extended-status in the system with this name</font>\n";
        } else {
            if (OSDstrlen($status)<1 or OSDstrlen($status_name)<2) {
                echo "<br><font color=$default_text>SYSTEM EXTENDED STATUS NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>status must be between 1 and 8 characters in length\n";
                echo "<br>status name must be between 2 and 30 characters in length</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>SYSTEM STATUS ADDED: $status_name - $parents:$status</font></b>\n";
                $stmt=sprintf("INSERT INTO osdial_statuses_extended (parents,status,status_name,selectable) VALUES('%s','%s','%s','%s');",mres($parents),mres($status),mres($status_name),mres($selectable));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW SYSTEM EXTENDED STATUS   |$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }

    } else {
        $os = get_first_record($link,'osdial_statuses','count(*) AS cnt',sprintf("status='%s'",mres($status)));
        if ($os['cnt'] > 0) {
            echo "<br><font color=red>SYSTEM STATUS NOT ADDED - there is already a global-status in the system with this name</font>\n";
        } else {
            if (OSDstrlen($status)<1 or OSDstrlen($status_name)<2) {
                echo "<br><font color=$default_text>SYSTEM STATUS NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>status must be between 1 and 8 characters in length\n";
                echo "<br>status name must be between 2 and 30 characters in length</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>SYSTEM STATUS ADDED: $status_name - $status</font></b>\n";
                $stmt=sprintf("INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('%s','%s','%s','%s','%s');",mres($status),mres($status_name),mres($selectable),mres($human_answered),mres($category));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW SYSTEM STATUS   |$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }
    }
    $ADD=321111111111111;
}


######################
# ADD=421111111111111 modify/delete system status in the system
######################

if ($ADD==421111111111111) {
    if ($LOG['modify_servers']==1) {
        if (OSDpreg_match('/extended/',$stage)) {
            if (OSDpreg_match('/delete/',$stage)) {
                $ose = get_first_record($link,'osdial_statuses_extended','count(*) AS cnt',sprintf("parents='%s'",mres($parents.':'.$status)));
                if ($ose['cnt']>0) {
                    echo "<br><center><b><font color=red>SYSTEM EXTENDED STATUS NOT DELETED - This status has $ose[cnt] sub-statuses, please remove them first.</font></b></center><br>\n";
                } else {
                    echo "<br><b><font color=$default_text>SYSTEM EXTENDED STATUS DELETED: $parents:$status</font></b>\n";
                    $stmt=sprintf("DELETE FROM osdial_statuses_extended WHERE parents='%s' AND status='%s';",mres($parents),mres($status));
                    $rslt=mysql_query($stmt, $link);

                    ### LOG CHANGES TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|DELETE SYSTEM EXTENDED STATUS  |$PHP_AUTH_USER|$ip|$stmt|$stmtA|\n");
                        fclose($fp);
                    }
                }
            }
            if (OSDpreg_match('/modify/',$stage)) {
                if (OSDstrlen($status)<1 or OSDstrlen($status_name)<2) {
                    echo "<br><font color=red>SYSTEM EXTENDED STATUS NOT MODIFIED - Please go back and look at the data you entered\n";
                    echo "<br>the system status needs to be at least 1 characters in length\n";
                    echo "<br>the system status name needs to be at least 1 characters in length</font>\n";
                } else {
                    echo "<br><b><font color=$default_text>SYSTEM EXTENDED STATUS MODIFIED: $parents:$status</font></b>\n";
                    $stmt=sprintf("UPDATE osdial_statuses SET status_name='%s',selectable='%s' WHERE parents='%s' AND status='%s';",mres($status_name),mres($selectable),mres($parents),mres($status));
                    $rslt=mysql_query($stmt, $link);

                    ### LOG CHANGES TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|MODIFY SYSTEM EXTENDED STATUS  |$PHP_AUTH_USER|$ip|$stmt|\n");
                        fclose($fp);
                    }
                }
            }
            $stage='extended';


        } else {
            if (OSDpreg_match('/delete/',$stage)) {
                if (OSDstrlen($status)<1 or OSDpreg_match("/^B$|^DNC$|^NA$|^DROP$|^INCALL$|^QUEUE$|^NEW$/i",$status)) {
                    echo "<br><font color=red>SYSTEM STATUS NOT DELETED - Please go back and look at the data you entered\n";
                    echo "<br>the system status cannot be a reserved status: B,NA,DNC,NA,DROP,INCALL,QUEUE,NEW\n";
                    echo "<br>the system status needs to be at least 1 characters in length</font><br>\n";
                } else {
                    $ose = get_first_record($link,'osdial_statuses_extended','count(*) AS cnt',sprintf("parents='%s'",mres($status)));
                    if ($ose['cnt']>0) {
                        echo "<br><center><b><font color=red>SYSTEM EXTENDED STATUS NOT DELETED - This status has $ose[cnt] sub-statuses, please remove them first.</font></b></center><br>\n";
                    } else {
                        echo "<br><b><font color=$default_text>SYSTEM STATUS DELETED: $status</font></b>\n";
                        $stmt=sprintf("DELETE FROM osdial_statuses WHERE status='%s';",mres($status));
                        $rslt=mysql_query($stmt, $link);
                        $stmtA=sprintf("DELETE FROM osdial_campaign_hotkeys WHERE status='%s';",mres($status));
                        $rslt=mysql_query($stmtA, $link);

                        ### LOG CHANGES TO LOG FILE ###
                        if ($WeBRooTWritablE > 0) {
                            $fp = fopen ("./admin_changes_log.txt", "a");
                            fwrite ($fp, "$date|DELETE SYSTEM STATUS  |$PHP_AUTH_USER|$ip|$stmt|$stmtA|\n");
                            fclose($fp);
                        }
                    }
                }
            }
            if (OSDpreg_match('/modify/',$stage)) {
                if (OSDstrlen($status)<1 or OSDstrlen($status_name)<2) {
                    echo "<br><font color=red>SYSTEM STATUS NOT MODIFIED - Please go back and look at the data you entered\n";
                    echo "<br>the system status needs to be at least 1 characters in length\n";
                    echo "<br>the system status name needs to be at least 1 characters in length</font>\n";
                } else {
                    echo "<br><b><font color=$default_text>SYSTEM STATUS MODIFIED: $status</font></b>\n";
                    $stmt=sprintf("UPDATE osdial_statuses SET status_name='%s',selectable='%s',human_answered='%s',category='%s' WHERE status='%s';",mres($status_name),mres($selectable),mres($human_answered),mres($category),mres($status));
                    $rslt=mysql_query($stmt, $link);

                    ### LOG CHANGES TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|MODIFY SYSTEM STATUS  |$PHP_AUTH_USER|$ip|$stmt|\n");
                        fclose($fp);
                    }
                }
            }
        }

        $ADD=321111111111111;	# go to system settings modification form below

    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=321111111111111 modify osdial system statuses
######################

if ($ADD==321111111111111) {
	if ($LOG['modify_servers']==1) {
        if ($stage=='extended') {
            $tparents = OSDpreg_replace('/^(.*):\S+$/','\\1',$parents);
            $oparents = OSDpreg_replace('/^.*:(\S+)$/','\\1',$parents);

	        echo "<br><center>\n";
	        echo "<font class=top_header color=$default_text size=4>SYSTEM-WIDE EXTENDED STATUSES</font> ".helptag("osdial_statuses-osdial_statuses")."<br>\n";
echo "<font class=top_header_sect color=$default_text size=+1>\n";
            if (OSDpreg_match('/\:/',$parents)) {
                echo "<a title=\"Go Up One Level\" href=\"$PHP_SELF?ADD=321111111111111&parents=$tparents&stage=extended\">$tparents</a>\n";
            } else {
                echo "<a title=\"Go Up One Level\" href=\"$PHP_SELF?ADD=321111111111111\">[Back]</a>\n";
            }
            echo ":<a title=\"Refresh View\" href=\"$PHP_SELF?ADD=321111111111111&parents=$parents&stage=extended\">$oparents</a>\n";
            echo "</font><br><br>";

	        echo "<table class=shadedtable width=700 cellspacing=1 bgcolor=grey>\n";
	        echo "  <tr class=tabheader>\n";
            echo "    <td align=center>STATUS ID</td>\n";
	        echo "    <td align=center>DESCRIPTION</td>\n";
	        echo "    <td>SELECTABLE</td>\n";
	        echo "    <td align=center colspan=2>ACTIONS</td>\n";
            echo "  </tr>\n";

	        $o=0;
            $sekrh = get_krh($link,'osdial_statuses_extended','*','status ASC',sprintf("parents='%s'",mres($parents)),'');
	        foreach ($sekrh as $st) {
		        $o++;
                echo "  <form action=$PHP_SELF method=POST>\n";
		        echo "  <input type=hidden name=ADD value=421111111111111>\n";
		        echo "  <input type=hidden name=stage value=extended_modify>\n";
		        echo "  <input type=hidden name=parents value=\"$st[parents]\">\n";
		        echo "  <input type=hidden name=status value=\"$st[status]\">\n";
		        echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                echo "    <td>\n";
                echo "      <b>$st[status]\n";
			    echo "      &nbsp;<a title=\"Create an Extended Sub-Status\" href=\"$PHP_SELF?ADD=421111111111111&parents=$parents:$st[status]&stage=extended\">+</a></b>\n";
                echo "    </td>\n";
		        echo "    <td align=center class=tabinput><input type=text name=status_name size=30 maxlength=30 value=\"$st[status_name]\"></td>\n";
		        echo "    <td align=center class=tabinput><select size=1 name=selectable><option>Y</option><option>N</option><option selected>$st[selectable]</option></select></td>\n";
		        echo "    <td align=center nowrap class=tabinput colspan=2>\n";
			    echo "      <a href=\"$PHP_SELF?ADD=421111111111111&parents=$parents&status=$st[status]&stage=extended_delete\">DELETE</a>\n";
		        echo "      <input type=submit name=submit value=MODIFY>\n";
                echo "    </td>\n";
                echo "  </tr>\n";
                echo "  </form>\n";
	        }

            echo "  <form action=$PHP_SELF method=POST>\n";
	        echo "  <input type=hidden name=ADD value=221111111111111>\n";
	        echo "  <input type=hidden name=stage value=extended>\n";
	        echo "  <input type=hidden name=parents value=$parents>\n";
	        echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabinput><input size=11 maxlength=10 name=status></td>\n";
            echo "    <td align=center class=tabinput><input size=30 maxlength=30 name=status_name></td>\n";
            echo "    <td align=center class=tabinput><select size=1 name=selectable><option>Y</option><option>N</option></select></td>\n";
            echo "    <td align=center colspan=2 class=tabbutton1><input type=submit name=submit value=ADD></td>\n";
            echo "  </tr>\n";
            echo "  </form>\n";
	        echo "</table>\n";
        } else {
	        echo "<br><center>\n";
	        echo "<font class=top_header color=$default_text size=4>SYSTEM-WIDE STATUSES &nbsp;</font> ".helptag("osdial_statuses-osdial_statuses")."<br><br>\n";
	        echo "<table class=shadedtable width=850 cellspacing=1 bgcolor=grey>\n";
	        echo "  <tr class=tabheader>\n";
            echo "    <td align=center width=10%>STATUS ID</td>\n";
	        echo "    <td align=center width=25%>DESCRIPTION</td>\n";
	        echo "    <td>SELECTABLE</td>\n";
	        echo "    <td>HUMAN&nbsp;ANSWER</td>\n";
	        echo "    <td align=center width=20%>CATEGORY</td>\n";
	        echo "    <td align=center colspan=2 width=15%>ACTIONS</td>\n";
            echo "  </tr>\n";

	        ##### get status category listings for dynamic pulldown
            $cats_list='';
            $catsname_list=array();
            $sckrh = get_krh($link,'osdial_status_categories','*','vsc_id DESC','','');
	        foreach ($sckrh as $sc) {
		        $cats_list .= "<option value=\"$sc[vsc_id]\">$sc[vsc_id] - " . OSDsubstr($sc['vsc_name'],0,20) . "</option>\n";
		        $catsname_list[$sc['vsc_id']] = OSDsubstr($sc['vsc_name'],0,20);
	        }

	        $o=0;
            $skrh = get_krh($link,'osdial_statuses','*','status ASC','','');
	        foreach ($skrh as $st) {
		        $o++;
                echo "  <form action=$PHP_SELF method=POST>\n";
		        echo "  <input type=hidden name=ADD value=421111111111111>\n";
		        echo "  <input type=hidden name=stage value=modify>\n";
		        echo "  <input type=hidden name=status value=\"$st[status]\">\n";
		        echo "  <tr " . bgcolor($o) . " class=\"row font1\">\n";
                echo "    <td>\n";
                echo "      <b>$st[status]\n";
			    echo "      &nbsp;<a title=\"Create an Extended Sub-Status\" href=\"$PHP_SELF?ADD=421111111111111&parents=$st[status]&stage=extended\">+</a></b>\n";
                echo "    </td>\n";
		        echo "    <td align=center class=tabinput><input type=text name=status_name size=30 maxlength=30 value=\"$st[status_name]\"></td>\n";
		        echo "    <td align=center class=tabinput><select size=1 name=selectable><option>Y</option><option>N</option><option selected>$st[selectable]</option></select></td>\n";
		        echo "    <td align=center class=tabinput><select size=1 name=human_answered><option>Y</option><option>N</option><option selected>$st[human_answered]</option></select></td>\n";
		        echo "    <td align=center class=tabinput><select size=1 name=category>$cats_list<option selected value=\"$st[category]\">$st[category] - ".$catsname_list[$st['category']]."</option></select></td>\n";
		        echo "    <td align=center nowrap class=tabinput colspan=2>\n";
		        if (OSDpreg_match("/^AA$|^AL$|^AM$|^B$|^CALLBK$|^CBHOLD$|^CRC$|^CRF$|^CRO$|^CRR$|^DC$|^DNCE$|^DNCL$|^DNC$|^NA$|^DROP$|^INCALL$|^QUEUE$|^NEW$|^XFER$|^XDROP$/i",$rowx[0])) {
			        echo "      <del>DELETE</del>\n";
		        } else {
			        echo "      <a href=\"$PHP_SELF?ADD=421111111111111&status=$st[status]&stage=delete\">DELETE</a>\n";
		        }
		        echo "      <input type=submit name=submit value=MODIFY>\n";
                echo "    </td>\n";
                echo "  </tr>\n";
                echo "  </form>\n";
	        }

            echo "  <form action=$PHP_SELF method=POST>\n";
	        echo "  <input type=hidden name=ADD value=221111111111111>\n";
	        echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabinput><input size=7 maxlength=6 name=status></td>\n";
            echo "    <td align=center class=tabinput><input size=30 maxlength=30 name=status_name></td>\n";
            echo "    <td align=center class=tabinput><select size=1 name=selectable><option>Y</option><option>N</option></select></td>\n";
            echo "    <td align=center class=tabinput><select size=1 name=human_answered><option>Y</option><option>N</option></select></td>\n";
	        echo "    <td align=center class=tabinput><select size=1 name=category>$cats_list</select></td>\n";
            echo "    <td align=center colspan=2 class=tabbutton1><input type=submit name=submit value=ADD></td>\n";
            echo "  </tr>\n";
            echo "  </form>\n";
	        echo "</table>\n";

        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


#### Status Categories


######################
# ADD=231111111111111 adds the new status category to the system
######################

if ($ADD==231111111111111) {
    $osc = get_first_record($link,'osdial_status_categories','count(*) AS cnt',sprintf("vsc_id='%s'",mres($vsc_id)));
    if ($osc['cnt'] > 0) {
        echo "<br><font color=red>STATUS CATEGORY NOT ADDED - there is already a status category in the system with this ID: $osc[cnt]</font>\n";
    } else {
        if (OSDstrlen($vsc_id)<2 or OSDstrlen($vsc_id)>20 or OSDstrlen($vsc_name)<2) {
            echo "<br><font color=red>STATUS CATEGORY NOT ADDED - Please go back and look at the data you entered\n";
            echo "<br>ID must be between 2 and 20 characters in length\n";
            echo "<br>name name must be between 2 and 50 characters in length</font><br>\n";
        } else {
            echo "<br><b><font color=$default_text>STATUS CATEGORY ADDED: $vsc_id - $vsc_name</font></b>\n";

            $osc = get_first_record($link,'osdial_status_categories','count(*) AS cnt',sprintf("tovdad_display='Y' AND vsc_id NOT IN ('%s')",mres($vsc_id)));
            if ($osc['cnt']>3 and OSDpreg_match('/Y/',$tovdad_display)) {
                $tovdad_display = 'N';
                echo "<br><b><font color=red>ERROR: There are already 4 Status Categories set to display on the Real-Time report.</font></b>\n";
            }
            $stmt=sprintf("INSERT INTO osdial_status_categories (vsc_id,vsc_name,vsc_description,tovdad_display) VALUES('%s','%s','%s','%s');",mres($vsc_id),mres($vsc_name),mres($vsc_description),mres($tovdad_display));
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|ADD A NEW STATUS CATEGORY |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
    }
    $ADD=331111111111111;
}


######################
# ADD=431111111111111 modify/delete status category in the system
######################

if ($ADD==431111111111111) {
    if ($LOG['modify_servers']==1) {
        if (OSDstrlen($vsc_id)<2 or OSDpreg_match("/^UNDEFINED$/i",$vsc_id)) {
            echo "<br><font color=red>STATUS CATEGORY NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>the status category cannot be a reserved category: UNDEFINED\n";
            echo "<br>the status category needs to be at least 2 characters in length</font><br>\n";
        } else {
            if (OSDpreg_match('/delete/',$stage)) {
                echo "<br><b><font color=$default_text>STATUS CATEGORY DELETED: $vsc_id</font></b>\n";
                $stmt=sprintf("DELETE FROM osdial_status_categories WHERE vsc_id='%s';",mres($vsc_id));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|DELETE STATUS CATEGORY|$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
            if (OSDpreg_match('/modify/',$stage)) {
                echo "<br><b><font color=$default_text>STATUS CATEGORY MODIFIED: $vsc_id</font></b>\n";
                $osc = get_first_record($link,'osdial_status_categories','count(*) AS cnt',sprintf("tovdad_display='Y' AND vsc_id NOT IN ('%s')",mres($vsc_id)));
                if ($osc['cnt'] > 3 and OSDpreg_match('/Y/',$tovdad_display)) {
                    $tovdad_display = 'N';
                    echo "<br><b><font color=red>ERROR: There are already 4 Status Categories set to display on the Real-Time report.</font></b>\n";
                }
                $stmt=sprintf("UPDATE osdial_status_categories SET vsc_name='%s',vsc_description='%s',tovdad_display='%s' WHERE vsc_id='%s';",mres($vsc_name),mres($vsc_description),mres($tovdad_display),mres($vsc_id));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|MODIFY STATUS CATEGORY|$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }
        $ADD=331111111111111;	# go to system settings modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=331111111111111 modify osdial status categories
######################

if ($ADD==331111111111111) {
    if ($LOG['modify_servers']==1) {
        echo "<br>\n";
        echo "<center><font size=4 color=$default_text>STATUS CATEGORIES &nbsp; ".helptag("osdial_status_categories-osdial_status_categories")."</font></center><br>\n";
        echo "<center>\n";
        echo "<table width=800 cellspacing=1 bgcolor=grey>\n";
        echo "  <tr class=tabheader>\n";
        echo "    <td align=center>CATEGORY ID</td>\n";
        echo "    <td align=center>NAME</td>\n";
        echo "    <td align=center>DESCRIPTION</td>\n";
        echo "    <td align=center>ON&nbsp;REALTIME</td>\n";
        echo "    <td align=center colspan=2>ACTIONS</td>\n";
        echo "  </tr>\n";

        $o=0;
        $sckrh = get_krh($link,'osdial_status_categories','*','vsc_id ASC','','');
	    foreach ($sckrh as $osc) {
            $o++;

            $CATstatuses='';
            $oskrh = get_krh($link,'osdial_statuses','status','status ASC',sprintf("category='%s'",mres($osc['vsc_id'])),'');
	        foreach ($oskrh as $os) {
                $CATstatuses.=" ".$os['status'];
            }
            $ocskrh = get_krh($link,'osdial_campaign_statuses','status','status ASC',sprintf("category='%s'",mres($osc['vsc_id'])),'');
	        foreach ($ocskrh as $ocs) {
                $CATstatuses.=" ".$ocs['status'];
            }

            echo "  <form action=$PHP_SELF method=POST>\n";
            echo "  <input type=hidden name=ADD value=431111111111111>\n";
            echo "  <input type=hidden name=stage value=modify>\n";
            echo "  <input type=hidden name=vsc_id value=\"$osc[vsc_id]\">\n";
            echo "  <tr " . bgcolor($o) . " class=\"row font1\" title=\"Statuses in Category: $CATstatuses\">\n";
            echo "    <td><b>$osc[vsc_id]</b></td>\n";
            echo "    <td align=center class=tabinput><input type=text name=vsc_name size=30 maxlength=50 value=\"$osc[vsc_name]\"></td>\n";
            echo "    <td align=center class=tabinput><input type=text name=vsc_description size=40 maxlength=255 value=\"$osc[vsc_description]\"></td>\n";
            echo "    <td align=center class=tabinput><select size=1 name=tovdad_display><option>Y</option><option>N</option><option selected>$osc[tovdad_display]</option></select></td>\n";
            echo "    <td align=center><font size=1><a href=\"$PHP_SELF?ADD=431111111111111&vsc_id=$osc[vsc_id]&stage=delete\">DELETE</a></font></td>\n";
            echo "    <td align=center class=tabbutton1><input type=submit name=submit value=MODIFY></td>\n";
            echo "  </tr>\n";
            echo "  </form>\n";

        }

        echo "  <form action=$PHP_SELF method=POST>\n";
        echo "  <input type=hidden name=ADD value=231111111111111>\n";
        echo "  <tr class=tabfooter>\n";
        echo "    <td align=center class=tabinput><input type=text name=vsc_id size=15 maxlength=20></td>\n";
        echo "    <td align=center class=tabinput><input type=text name=vsc_name size=30 maxlength=50></td>\n";
        echo "    <td align=center class=tabinput><input type=text name=vsc_description size=40 maxlength=255></td>\n";
        echo "    <td align=center class=tabinput><select size=1 name=tovdad_display><option>N</option><option>Y</option></select></td>\n";
        echo "    <td align=center colspan=2 class=tabbutton1><input type=submit name=submit value=ADD></td>\n";
        echo "  </tr>\n";
        echo "  </form>\n";
        echo "</table>\n";
        echo "</center>\n";

    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



?>
