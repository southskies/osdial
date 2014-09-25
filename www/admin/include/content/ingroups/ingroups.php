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
# 090410-1730 - Added allow_tab_switch




######################
# ADD=1111 display the ADD NEW INBOUND GROUP SCREEN
######################

if ($ADD==1111) {
	if ($LOG['modify_ingroups']==1) {
        echo "<center><br><font class=top_header color=$default_text size=+1>ADD A NEW INBOUND GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=2111>\n";
        echo "<TABLE border=0 class=shadedtable width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right width=35%>ID: </td><td align=left>";
        if ($LOG['multicomp_admin'] > 0) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "<select name=company_id>\n";
            foreach ($comps as $comp) {
                echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "</select>\n";
        } elseif ($LOG['multicomp']>0) {
            echo "<input type=hidden name=company_id value=$LOG[company_id]>";
            #echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
        }
        echo "<input type=text name=group_id size=20 maxlength=20> (no spaces)".helptag("osdial_inbound_groups-group_id")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Name: </td><td align=left><input type=text name=group_name size=30 maxlength=30>".helptag("osdial_inbound_groups-group_name")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Color: </td><td align=left id=\"group_color_td\"><input type=text name=group_color size=7 maxlength=7 mode=\"colorpicker\" hideInput=\"1\" forceWidth=\"50\">".helptag("osdial_inbound_groups-group_color")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option></select>".helptag("osdial_inbound_groups-active")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 1: </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"$web_form_address\">".helptag("osdial_inbound_groups-web_form_address")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 2: </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"$web_form_address2\">".helptag("osdial_inbound_groups-web_form_address")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Voicemail: </td><td align=left>\n";
        #echo "<input type=text name=voicemail_ext size=10 maxlength=10 value=\"$voicemail_ext\">\n";
        echo phone_voicemail_text_options($link, 'voicemail_ext', $voicemail_ext, 10, 10);
        echo "".helptag("osdial_inbound_groups-voicemail_ext")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Next Agent Call: </td><td align=left><select size=1 name=next_agent_call><option >random</option><option>oldest_call_start</option><option>oldest_call_finish</option><option>overall_user_level</option><option>inbound_group_rank</option><option>campaign_rank</option><option>fewest_calls</option><option>fewest_calls_campaign</option></select>".helptag("osdial_inbound_groups-next_agent_call")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Fronter Display: </td><td align=left><select size=1 name=fronter_display><option SELECTED>Y</option><option>N</option></select>".helptag("osdial_inbound_groups-fronter_display")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Script: </td><td align=left><select size=1 name=script_id>\n";
        echo get_scripts($link, '');
        #echo "$scripts_list";
        echo "</select>".helptag("osdial_inbound_groups-ingroup_script")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Get Call Launch: </td><td align=left><select size=1 name=get_call_launch><option selected>NONE</option><option>SCRIPT</option><option>WEBFORM</option></select>".helptag("osdial_inbound_groups-get_call_launch")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Allow Tab Switch: </td><td align=left><select size=1 name=allow_tab_switch><option selected>Y</option><option>N</option></select>".helptag("osdial_inbound_groups-allow_tab_switch")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Allow Multicall: </td><td align=left><select size=1 name=allow_multicall><option selected>Y</option><option>N</option></select>".helptag("osdial_inbound_groups-allow_multicall")."</td></tr>\n";
        echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";
	} else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=1211 display the COPY INBOUND GROUP SCREEN
######################

if ($ADD==1211) {
	if ($LOG['modify_ingroups']==1)	{
        echo "<center><br><font class=top_header color=$default_text size=+1>COPY INBOUND GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=2011>\n";
        echo "<TABLE class=shadedtable width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right width=35%>ID: </td><td align=left>";
        if ($LOG['multicomp_admin'] > 0) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "<select name=company_id>\n";
            foreach ($comps as $comp) {
                echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "</select>\n";
        } elseif ($LOG['multicomp']>0) {
            echo "<input type=hidden name=company_id value=$LOG[company_id]>";
            #echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
        }
        echo "<input type=text name=group_id size=20 maxlength=20> (no spaces)".helptag("osdial_inbound_groups-group_id")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Name: </td><td align=left><input type=text name=group_name size=30 maxlength=30>".helptag("osdial_inbound_groups-group_name")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Source ID: </td><td align=left><select size=1 name=source_group_id>\n";

            $stmt=sprintf("SELECT group_id,group_name FROM osdial_inbound_groups WHERE group_id IN %s AND group_id NOT LIKE 'A2A_%%' ORDER BY group_id",$LOG['allowed_ingroupsSQL']);
            $rslt=mysql_query($stmt, $link);
            $groups_to_print = mysql_num_rows($rslt);
            $groups_list='';

            $o=0;
            while ($groups_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $groups_list .= "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
                $o++;
            }
        echo "$groups_list";
        echo "</select>".helptag("osdial_inbound_groups-group_id")."</td></tr>\n";

        echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";
	} else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



######################
# ADD=2111 adds the new inbound group to the system
######################

if ($ADD==2111) {
    $pregroup_id = $group_id;
    if ($LOG['multicomp'] > 0) $pregroup_id = (($company_id * 1) + 100) . $group_id;
	$stmt=sprintf("SELECT count(*) FROM osdial_inbound_groups WHERE group_id='%s';",mres($pregroup_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0) {
        echo "<br><font color=red>GROUP NOT ADDED - there is already a group in the system with this ID</font>\n";
    } else {
		$stmt=sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_id='%s';",mres($pregroup_id));
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0) {
            echo "<br><font color=red>GROUP NOT ADDED - there is already a campaign in the system with this ID</font>\n";
        } else {
			 if ((OSDstrlen($group_id) < 2) or (OSDstrlen($group_name) < 2)  or (OSDstrlen($group_color) < 2) or (OSDstrlen($group_id) > 20) or (OSDpreg_match('/ /',$group_id)) or (OSDpreg_match("/\-/",$group_id)) or (OSDpreg_match("/\+/",$group_id))) {
				 echo "<br><font color=$default_text>GROUP NOT ADDED - Please go back and look at the data you entered\n";
				 echo "<br>Group ID must be between 2 and 20 characters in length and contain no ' -+'.\n";
				 echo "<br>Group name and group color must be at least 2 characters in length</font><br>\n";
            } else {
                if ($LOG['multicomp'] > 0) $group_id = (($company_id * 1) + 100) . $group_id;
				$stmt=sprintf("INSERT INTO osdial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,web_form_address2,allow_tab_switch,allow_multicall) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');",mres($group_id),mres($group_name),mres($group_color),mres($active),mres($web_form_address),mres($voicemail_ext),mres($next_agent_call),mres($fronter_display),mres($script_id),mres($get_call_launch),mres($web_form_address2),mres($allow_tab_switch),mres($allow_multicall));
				$rslt=mysql_query($stmt, $link);

				echo "<br><B><font color=$default_text>GROUP ADDED: $group_id</font></B>\n";

				### LOG CHANGES TO LOG FILE ###
				if ($WeBRooTWritablE > 0) {
					$fp = fopen ("./admin_changes_log.txt", "a");
					fwrite ($fp, "$date|ADD A NEW GROUP     |$PHP_AUTH_USER|$ip|$stmt|\n");
					fclose($fp);
                }

                if ($LOG['allowed_ingroupsALL']==0) {
                    $LOG['allowed_ingroups'][] = $group_id;
                    $ingroups_value = ' ' . implode(' ', $LOG['allowed_ingroups']) . ' -';
                    $stmt = sprintf("UPDATE osdial_user_groups SET allowed_ingroups='%s' WHERE user_group='%s';",mres($ingroups_value),mres($LOG['user_group']));
				    $rslt=mysql_query($stmt, $link);
                }
            }
        }
    }
    $ADD=3111;
}


######################
# ADD=2011 adds copied inbound group to the system
######################

if ($ADD==2011) {
    $pregroup_id = $group_id;
    if ($LOG['multicomp'] > 0) $pregroup_id = (($company_id * 1) + 100) . $group_id;
	$stmt=sprintf("SELECT count(*) FROM osdial_inbound_groups WHERE group_id='%s';",mres($pregroup_id));
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0) {
        echo "<br><font color=red>GROUP NOT ADDED - there is already a group in the system with this ID</font>\n";
    } else {
		 if ((OSDstrlen($group_id) < 2) or (OSDstrlen($group_name) < 2) or (OSDstrlen($group_id) > 20) or (OSDpreg_match('/ /',$group_id)) or (OSDpreg_match("/\-/",$group_id)) or (OSDpreg_match("/\+/",$group_id))) {
			 echo "<br><font color=red>GROUP NOT ADDED - Please go back and look at the data you entered\n";
			 echo "<br>Group ID must be between 2 and 20 characters in length and contain no ' -+'.\n";
			 echo "<br>Group name and group color must be at least 2 characters in length</font><br>\n";
        } else {
            if ($LOG['multicomp'] > 0) $group_id = (($company_id * 1) + 100) . $group_id;
			$stmt=sprintf("INSERT INTO osdial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,drop_call_seconds,drop_action,drop_exten,call_time_id,after_hours_action,after_hours_message_filename,after_hours_exten,after_hours_voicemail,welcome_message_filename,moh_context,onhold_prompt_filename,prompt_interval,agent_alert_exten,agent_alert_delay,default_xfer_group,web_form_address2,allow_tab_switch,web_form_extwindow,web_form2_extwindow,drop_trigger,allow_multicall,placement_interval,placement_max_repeat,queuetime_interval,queuetime_max_repeat,background_music_filename,drop_message_filename,callback_interval,callback_interrupt_key,onhold_startdelay,callback_startdelay,placement_startdelay,queuetime_startdelay,prompt_language,welcome_message_min_playtime) SELECT '%s','%s',group_color,'N',web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,drop_call_seconds,drop_action,drop_exten,call_time_id,after_hours_action,after_hours_message_filename,after_hours_exten,after_hours_voicemail,welcome_message_filename,moh_context,onhold_prompt_filename,prompt_interval,agent_alert_exten,agent_alert_delay,default_xfer_group,web_form_address2,allow_tab_switch,web_form_extwindow,web_form2_extwindow,drop_trigger,allow_multicall,placement_interval,placement_max_repeat,queuetime_interval,queuetime_max_repeat,background_music_filename,drop_message_filename,callback_interval,callback_interrupt_key,onhold_startdelay,callback_startdelay,placement_startdelay,queuetime_startdelay,prompt_language,welcome_message_min_playtime FROM osdial_inbound_groups WHERE group_id='%s';",mres($group_id),mres($group_name),mres($source_group_id));
			$rslt=mysql_query($stmt, $link);

			echo "<br><B><font color=$default_text>GROUP ADDED: $group_id</font></B>\n";

			### LOG CHANGES TO LOG FILE ###
			if ($WeBRooTWritablE > 0) {
				$fp = fopen ("./admin_changes_log.txt", "a");
				fwrite ($fp, "$date|COPIED TO NEW GROUP |$PHP_AUTH_USER|$ip|$stmt|\n");
				fclose($fp);
            }

            if ($LOG['allowed_ingroupsALL']==0) {
                $LOG['allowed_ingroups'][] = $group_id;
                $ingroups_value = ' ' . implode(' ', $LOG['allowed_ingroups']) . ' -';
                $stmt = sprintf("UPDATE osdial_user_groups SET allowed_ingroups='%s' WHERE user_group='%s';",mres($ingroups_value),mres($LOG['user_group']));
                $rslt=mysql_query($stmt, $link);
            }

        }
    }
    $ADD=3111;
}

######################
# ADD=4111 modify in-group info in the system
######################

if ($ADD==4111) {
	if ($LOG['modify_ingroups']==1) {
        if ((OSDstrlen($group_name) < 2) or (OSDstrlen($group_color) < 2)) {
            echo "<br><font color=red>GROUP NOT MODIFIED - Please go back and look at the data you entered\n";
            echo "<br>group name and group color must be at least 2 characters in length</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>GROUP MODIFIED: $group_id</font></B>\n";

            $stmt=sprintf("UPDATE osdial_inbound_groups SET group_name='%s',group_color='%s',active='%s',web_form_address='%s',voicemail_ext='%s',next_agent_call='%s',fronter_display='%s',ingroup_script='%s',get_call_launch='%s',xferconf_a_dtmf='%s',xferconf_a_number='%s',xferconf_b_dtmf='%s',xferconf_b_number='%s',drop_action='%s',drop_call_seconds='%s',drop_exten='%s',call_time_id='%s',after_hours_action='%s',after_hours_message_filename='%s',after_hours_exten='%s',after_hours_voicemail='%s',welcome_message_filename='%s',moh_context='%s',onhold_prompt_filename='%s',prompt_interval='%s',agent_alert_exten='%s',agent_alert_delay='%s',default_xfer_group='%s',web_form_address2='%s',allow_tab_switch='%s',web_form_extwindow='%s',web_form2_extwindow='%s',drop_trigger='%s',allow_multicall='%s',placement_interval='%s',placement_max_repeat='%s',queuetime_interval='%s',queuetime_max_repeat='%s',background_music_filename='%s',drop_message_filename='%s',callback_interval='%s',callback_interrupt_key='%s',onhold_startdelay='%s',callback_startdelay='%s',placement_startdelay='%s',queuetime_startdelay='%s',prompt_language='%s',welcome_message_min_playtime='%s' WHERE group_id='%s';",mres($group_name),mres($group_color),mres($active),mres($web_form_address),mres($voicemail_ext),mres($next_agent_call),mres($fronter_display),mres($script_id),mres($get_call_launch),mres($xferconf_a_dtmf),mres($xferconf_a_number),mres($xferconf_b_dtmf),mres($xferconf_b_number),mres($drop_action),mres($drop_call_seconds),mres($drop_exten),mres($call_time_id),mres($after_hours_action),mres($after_hours_message_filename),mres($after_hours_exten),mres($after_hours_voicemail),mres($welcome_message_filename),mres($moh_context),mres($onhold_prompt_filename),mres($prompt_interval),mres($agent_alert_exten),mres($agent_alert_delay),mres($default_xfer_group),mres($web_form_address2),mres($allow_tab_switch),mres($web_form_extwindow),mres($web_form2_extwindow),mres($drop_trigger),mres($allow_multicall),mres($placement_interval),mres($placement_max_repeat),mres($queuetime_interval),mres($queuetime_max_repeat),mres($background_music_filename),mres($drop_message_filename),mres($callback_interval),mres($callback_interrupt_key),mres($onhold_startdelay),mres($callback_startdelay),mres($placement_startdelay),mres($queuetime_startdelay),mres($prompt_language),mres($welcome_message_min_playtime),mres($group_id));
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY GROUP INFO   |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
    $ADD=3111;	# go to in-group modification form below
	} else {
	echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}


######################
# ADD=5111 confirmation before deletion of in-group
######################

if ($ADD==5111) {
	 if ((OSDstrlen($group_id) < 2) or ($LOG['delete_ingroups'] < 1)) {
		 echo "<br><font color=red>IN-GROUP NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Group_id be at least 2 characters in length</font><br>\n";
    } else {
		echo "<br><B><font color=$default_text>IN-GROUP DELETION CONFIRMATION: $group_id</B>\n";
		echo "<br><br><a href=\"$PHP_SELF?ADD=6111&group_id=$group_id&CoNfIrM=YES\">Click here to delete in-group $group_id</a></font><br><br><br>\n";
    }
    $ADD='3111';		# go to in-group modification below
}


######################
# ADD=6111 delete in-group record
######################

if ($ADD==6111) {
	 if ((OSDstrlen($group_id) < 2) or ($CoNfIrM != 'YES') or ($LOG['delete_ingroups'] < 1)) {
		 echo "<br><font color=red>IN-GROUP NOT DELETED - Please go back and look at the data you entered\n";
		 echo "<br>Group_id be at least 2 characters in length</font><br>\n";
    } else {
		$stmt=sprintf("DELETE FROM osdial_inbound_groups WHERE group_id='%s' LIMIT 1;",mres($group_id));
		$rslt=mysql_query($stmt, $link);

		$stmt=sprintf("DELETE FROM osdial_inbound_group_agents WHERE group_id='%s';",mres($group_id));
		$rslt=mysql_query($stmt, $link);

		$stmt=sprintf("DELETE FROM osdial_live_inbound_agents WHERE group_id='%s';",mres($group_id));
		$rslt=mysql_query($stmt, $link);

		### LOG CHANGES TO LOG FILE ###
		if ($WeBRooTWritablE > 0) {
			$fp = fopen ("./admin_changes_log.txt", "a");
			fwrite ($fp, "$date|!DELETING IN-GROUP!!|$PHP_AUTH_USER|$ip|group_id='$group_id'|\n");
			fclose($fp);
        }
		echo "<br><B><font color=$default_text>IN-GROUP DELETION COMPLETED: $group_id</font></B>\n";
		echo "<br><br>\n";
    }
    $ADD='1000';		# go to in-group list
}


######################
# ADD=3111 modify in-group info in the system
######################

if ($ADD==3111) {
	if ($LOG['modify_ingroups']==1) {
        $stmt=sprintf("SELECT * FROM osdial_inbound_groups WHERE group_id='%s';",mres($group_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $group_name =				$row[1];
        $group_color =				$row[2];
        $active =					$row[3];
        $web_form_address =			$row[4];
        $voicemail_ext =			$row[5];
        $next_agent_call =			$row[6];
        $fronter_display =			$row[7];
        $script_id =				$row[8];
        $get_call_launch =			$row[9];
        $xferconf_a_dtmf =			$row[10];
        $xferconf_a_number =		$row[11];
        $xferconf_b_dtmf =			$row[12];
        $xferconf_b_number =		$row[13];
        $drop_call_seconds =		$row[14];
        $drop_action =				$row[15];
        $drop_exten =				$row[16];
        $call_time_id =				$row[17];
        $after_hours_action =		$row[18];
        $after_hours_message_filename =	$row[19];
        $after_hours_exten =		$row[20];
        $after_hours_voicemail =	$row[21];
        $welcome_message_filename =	$row[22];
        $moh_context =				$row[23];
        $onhold_prompt_filename =	$row[24];
        $prompt_interval =			$row[25];
        $agent_alert_exten =		$row[26];
        $agent_alert_delay =		$row[27];
        $default_xfer_group =		$row[28];
        $web_form_address2 =		$row[29];
        $allow_tab_switch =		    $row[30];
        $web_form_extwindow =		$row[31];
        $web_form2_extwindow =		$row[32];
        $drop_trigger =		        $row[33];
        $allow_multicall =	        $row[34];
        $placement_interval =	    $row[35];
        $placement_max_repeat =	    $row[36];
        $queuetime_interval =	    $row[37];
        $queuetime_max_repeat =	    $row[38];
        $background_music_filename =$row[39];
        $drop_message_filename =	$row[40];
        $callback_interval =	    $row[41];
        $callback_interrupt_key =	$row[42];
        $onhold_startdelay =	    $row[43];
        $callback_startdelay =	    $row[44];
        $placement_startdelay =	    $row[45];
        $queuetime_startdelay =	    $row[46];
        $prompt_language =	        $row[47];
        $welcome_message_min_playtime =	$row[48];

        ##### get in-groups listings for dynamic pulldown
        $stmt="SELECT group_id,group_name FROM osdial_inbound_groups ORDER BY group_id;";
        $rslt=mysql_query($stmt, $link);
        $Xgroups_to_print = mysql_num_rows($rslt);
        $Xgroups_menu='';
        $Xgroups_selected=0;
        $o=0;
        while ($Xgroups_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $Xgroups_menu .= "<option ";
            if ($default_xfer_group == "$rowx[0]") {
                $Xgroups_menu .= "SELECTED ";
                $Xgroups_selected++;
            }
            $Xgroups_menu .= "value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>\n";
            $o++;
        }
        if ($Xgroups_selected < 1) {
            $Xgroups_menu .= "<option SELECTED value=\"---NONE---\">---NONE---</option>\n";
        } else {
            $Xgroups_menu .= "<option value=\"---NONE---\">---NONE---</option>\n";
        }

        $astlangs = array();
        $stmt = sprintf("SELECT * FROM servers WHERE active='Y' AND server_profile IN ('AIO','DIALER');");
        $rslt=mysql_query($stmt, $link);
        $astnum = mysql_num_rows($rslt);
        $astlangcnt=0;
        while ($astlangcnt < $astnum) {
            $rowS=mysql_fetch_assoc($rslt);
            $langs = OSDpreg_split('/\|/', $rowS['asterisk_languages']);
            foreach ($langs as $lang) {
                if (array_key_exists($lang,$astlangs)) {
                    $astlangs[$lang]++;
                } else {
                    $astlangs[$lang] = 1;
                }
            }
            $astlangcnt++;
        }

        echo "<center><br><font class=top_header color=$default_text size=+1>MODIFY AN IN-GROUP</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=4111>\n";
        echo "<input type=hidden name=group_id value=\"$row[0]\">\n";
        echo "<TABLE class=shadedtable width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>ID: </td><td align=left><b>" . mclabel($row[0]) . "</b>".helptag("osdial_inbound_groups-group_id")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Name: </td><td align=left><input type=text name=group_name size=30 maxlength=30 value=\"$row[1]\">".helptag("osdial_inbound_groups-group_name")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Color: </td><td align=left id=\"group_color_td\"><input type=text name=group_color size=7 maxlength=7 value=\"$row[2]\" mode=\"colorpicker\" hideInput=\"1\" forceWidth=\"50\">".helptag("osdial_inbound_groups-group_color")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$active</option></select>".helptag("osdial_inbound_groups-active")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 1: </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"$web_form_address\">".helptag("osdial_inbound_groups-web_form_address")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form in External Window: </td><td align=left><select size=1 name=web_form_extwindow><option>Y</option><option>N</option><option SELECTED>$web_form_extwindow</option></select>".helptag("osdial_inbound_groups-web_form_extwindow")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 2: </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"$web_form_address2\">".helptag("osdial_inbound_groups-web_form_address")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 2 in External Window: </td><td align=left><select size=1 name=web_form2_extwindow><option>Y</option><option>N</option><option SELECTED>$web_form2_extwindow</option></select>".helptag("osdial_inbound_groups-web_form2_extwindow")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Next Agent Call: </td><td align=left><select size=1 name=next_agent_call><option >random</option><option>oldest_call_start</option><option>oldest_call_finish</option><option>overall_user_level</option><option>inbound_group_rank</option><option>campaign_rank</option><option>fewest_calls</option><option>fewest_calls_campaign</option><option SELECTED>$next_agent_call</option></select>".helptag("osdial_inbound_groups-next_agent_call")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Fronter Display: </td><td align=left><select size=1 name=fronter_display><option>Y</option><option>N</option><option SELECTED>$fronter_display</option></select>".helptag("osdial_inbound_groups-fronter_display")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=3111111&script_id=$script_id\">Script</a>: </td><td align=left><select size=1 name=script_id>\n";
        echo get_scripts($link, $script_id);
        #echo "$scripts_list";
        #echo "<option selected value=\"$script_id\">$script_id - $scriptname_list[$script_id]</option>\n";
        echo "</select>".helptag("osdial_inbound_groups-ingroup_script")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Get Call Launch: </td><td align=left><select size=1 name=get_call_launch><option selected>NONE</option><option>SCRIPT</option><option>WEBFORM</option><option selected>$get_call_launch</option></select>".helptag("osdial_inbound_groups-get_call_launch")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Allow Tab Switch: </td><td align=left><select size=1 name=allow_tab_switch><option>Y</option><option>N</option><option selected>$allow_tab_switch</option></select>".helptag("osdial_inbound_groups-allow_tab_switch")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Transfer-Conf DTMF 1: </td><td align=left><input type=text name=xferconf_a_dtmf size=20 maxlength=50 value=\"$xferconf_a_dtmf\">".helptag("osdial_inbound_groups-xferconf_a_dtmf")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Transfer-Conf Number 1: </td><td align=left><input type=text name=xferconf_a_number size=20 maxlength=50 value=\"$xferconf_a_number\">".helptag("osdial_inbound_groups-xferconf_a_number")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Transfer-Conf DTMF 2: </td><td align=left><input type=text name=xferconf_b_dtmf size=20 maxlength=50 value=\"$xferconf_b_dtmf\">".helptag("osdial_inbound_groups-xferconf_a_dtmf")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Transfer-Conf Number 2: </td><td align=left><input type=text name=xferconf_b_number size=20 maxlength=50 value=\"$xferconf_b_number\">".helptag("osdial_inbound_groups-xferconf_a_number")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Default Transfer Group: </td><td align=left><select size=1 name=default_xfer_group>";
        echo "$Xgroups_menu";
        echo "</select>".helptag("osdial_inbound_groups-default_xfer_group")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Allow Multicall: </td><td align=left><select size=1 name=allow_multicall><option>Y</option><option>N</option><option selected>$allow_multicall</option></select>".helptag("osdial_inbound_groups-allow_multicall")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Agent Alert Delay: </td><td align=left><input type=text name=agent_alert_delay size=6 maxlength=6 value=\"$agent_alert_delay\">".helptag("osdial_inbound_groups-agent_alert_delay")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Agent Alert Extension: </td><td align=left>\n";
        #echo "<input type=text name=agent_alert_exten size=10 maxlength=20 value=\"$agent_alert_exten\">\n";
        echo extension_text_options($link, 'agent_alert_exten', $agent_alert_exten, 15, 50);
        echo "".helptag("osdial_inbound_groups-agent_alert_exten")."</td></tr>\n";

        echo "<tr class=tabheader><td colspan=2></td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=311111111&call_time_id=$call_time_id\">Call Time: </a></td><td align=left><select size=1 name=call_time_id>\n";
        echo get_calltimes($link, $call_time_id);
        #echo "$call_times_list";
        #echo "<option selected value=\"$call_time_id\">$call_time_id - $call_timename_list[$call_time_id]</option>\n";
        echo "</select>".helptag("osdial_inbound_groups-call_time_id")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Welcome Message Filename: </td>\n";
        echo "  <td align=left>\n";
        #echo "    <select name=welcome_message_filename>\n";
        #echo media_file_select_options($link, $welcome_message_filename);
        #echo "    </select>\n";
        echo media_file_text_options($link, 'welcome_message_filename', $welcome_message_filename, 15, 50);
        echo "".helptag("osdial_inbound_groups-welcome_message_filename")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Welcome Msg Min Play: </td>\n";
        echo "  <td align=left>\n";
        echo "<input type=text name=welcome_message_min_playtime size=5 maxlength=5 value=\"$welcome_message_min_playtime\">\n";
        echo "".helptag("osdial_inbound_groups-welcome_message_min_playtime")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Music On Hold Context: </td><td align=left><input type=text name=moh_context size=10 maxlength=20 value=\"$moh_context\">".helptag("osdial_inbound_groups-moh_context")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Background Music Filename: </td>\n";
        echo "  <td align=left>\n";
        echo media_file_text_options($link, 'background_music_filename', $background_music_filename, 30, 100);
        echo "".helptag("osdial_inbound_groups-background_music_filename")."</td></tr>\n";

        echo "<tr class=tabheader><td colspan=2></td></tr>\n";

        echo "<tr bgcolor=$oddrows>\n";
        echo "  <td align=right>Prompt Language: </td>\n";
        echo "  <td align=left>\n";
        echo "    <select size=1 name=prompt_language>\n";
        $asel = ''; if ($prompt_language == '') $asel=' selected';
        echo "      <option value=\"\" $asel>DEFAULT</option>\n";
        foreach ($astlangs as $k => $v) {
            if ($v == $astlangcnt) {
                $asel = ''; if ($k == $prompt_language) $asel=' selected';
                echo "      <option value=\"$k\"$asel>$k</option>\n";
            }
        }
        echo "    </select>".helptag("osdial_inbound_groups-prompt_language");
        echo "  </td>\n";
        echo "</tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>On Hold Prompt Filename: </td>\n";
        echo "  <td align=left>\n";
        #echo "    <select name=onhold_prompt_filename>\n";
        #echo media_file_select_options($link, $onhold_prompt_filename);
        #echo "    </select>\n";
        echo media_file_text_options($link, 'onhold_prompt_filename', $onhold_prompt_filename, 30, 50);
        echo "".helptag("osdial_inbound_groups-onhold_prompt_filename")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>On Hold Prompt Interval: </td><td align=left><input type=text name=prompt_interval size=5 maxlength=10 value=\"$prompt_interval\">".helptag("osdial_inbound_groups-prompt_interval")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>On Hold Start Delay: </td><td align=left><input type=text name=onhold_startdelay size=5 maxlength=10 value=\"$onhold_startdelay\">".helptag("osdial_inbound_groups-onhold_startdelay")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Callback Prompt Interval: </td><td align=left><input type=text name=callback_interval size=5 maxlength=10 value=\"$callback_interval\">".helptag("osdial_inbound_groups-callback_interval")."</td></tr>\n";
        if ($callback_interrupt_key == '') $callback_interrupt_key='*';
        echo "<tr bgcolor=$oddrows><td align=right>Callback Interrupt Key: </td><td align=left><select size=1 name=callback_interrupt_key><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>*</option><option>#</option><option SELECTED>$callback_interrupt_key</option></select>".helptag("osdial_inbound_groups-callback_interrupt_key")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Callback Start Delay: </td><td align=left><input type=text name=callback_startdelay size=5 maxlength=10 value=\"$callback_startdelay\">".helptag("osdial_inbound_groups-callback_startdelay")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Placement Prompt Interval: </td><td align=left><input type=text name=placement_interval size=5 maxlength=10 value=\"$placement_interval\">".helptag("osdial_inbound_groups-placement_interval")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Placement Prompt Repeat: </td><td align=left><input type=text name=placement_max_repeat size=5 maxlength=5 value=\"$placement_max_repeat\">".helptag("osdial_inbound_groups-placement_max_repeat")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Placement Start Delay: </td><td align=left><input type=text name=placement_startdelay size=5 maxlength=10 value=\"$placement_startdelay\">".helptag("osdial_inbound_groups-placement_startdelay")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Est Queuetime Prompt Interval: </td><td align=left><input type=text name=queuetime_interval size=5 maxlength=10 value=\"$queuetime_interval\">".helptag("osdial_inbound_groups-queuetime_interval")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Est Queuetime Prompt Repeat: </td><td align=left><input type=text name=queuetime_max_repeat size=5 maxlength=5 value=\"$queuetime_max_repeat\">".helptag("osdial_inbound_groups-queuetime_max_repeat")."</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Est Queuetime Start Delay: </td><td align=left><input type=text name=queuetime_startdelay size=5 maxlength=10 value=\"$queuetime_startdelay\">".helptag("osdial_inbound_groups-queuetime_startdelay")."</td></tr>\n";

        echo "<tr class=tabheader><td colspan=2></td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Drop Trigger: </td>\n";
        echo "  <td align=left>\n";
        echo "    <select size=1 name=drop_trigger>\n";
        $dtscs=''; if ($drop_trigger=='CALL_SECONDS_TIMEOUT') $dtscs = "selected";
        echo "      <option $dtscs>CALL_SECONDS_TIMEOUT</option>\n";
        $dtsac=''; if ($drop_trigger=='NO_AGENTS_CONNECTED') $dtsac = "selected";
        echo "      <option $dtsac>NO_AGENTS_CONNECTED</option>\n";
        $dtsaa=''; if ($drop_trigger=='NO_AGENTS_AVAILABLE') $dtsaa = "selected";
        echo "      <option $dtsaa>NO_AGENTS_AVAILABLE</option>\n";
        echo "    </select>\n";
        echo "    ".helptag("osdial_inbound_groups-drop_trigger")."\n";
        echo "  </td>\n";
        echo "</tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Drop Call Seconds: </td><td align=left><input type=text name=drop_call_seconds size=5 maxlength=4 value=\"$drop_call_seconds\">".helptag("osdial_inbound_groups-drop_call_seconds")."</td></tr>\n";

        if ($drop_action == '') $drop_action='HANGUP';
        echo "<tr bgcolor=$oddrows><td align=right>Drop Action: </td><td align=left><select size=1 name=drop_action><option>HANGUP</option><option>MESSAGE</option><option>EXTENSION</option><option>VOICEMAIL</option><option>CALLBACK</option><option SELECTED>$drop_action</option></select>".helptag("osdial_inbound_groups-drop_action")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Drop Message Filename: </td>\n";
        echo "  <td align=left>\n";
        echo media_file_text_options($link, 'drop_message_filename', $drop_message_filename, 15, 50);
        echo "".helptag("osdial_inbound_groups-drop_message_filename")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Drop Extension: </td><td align=left>\n";
        #echo "<input type=text name=drop_exten size=10 maxlength=20 value=\"$drop_exten\">\n";
        echo extension_text_options($link, 'drop_exten', $drop_exten, 15, 50);
        echo "".helptag("osdial_inbound_groups-drop_exten")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Drop Voicemail: </td><td align=left>\n";
        #echo "<input type=text name=voicemail_ext size=10 maxlength=10 value=\"$voicemail_ext\">\n";
        echo phone_voicemail_text_options($link, 'voicemail_ext', $voicemail_ext, 10, 10);
        echo "".helptag("osdial_inbound_groups-voicemail_ext")."</td></tr>\n";

        echo "<tr class=tabheader><td colspan=2></td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>After Hours Action: </td><td align=left><select size=1 name=after_hours_action><option>HANGUP</option><option>MESSAGE</option><option>EXTENSION</option><option>VOICEMAIL</option><option>CALLBACK</option><option SELECTED>$after_hours_action</option></select>".helptag("osdial_inbound_groups-after_hours_action")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>After Hours Message Filename: </td>\n";
        echo "  <td align=left>\n";
        echo media_file_text_options($link, 'after_hours_message_filename', $after_hours_message_filename, 15, 50);
        echo "".helptag("osdial_inbound_groups-after_hours_message_filename")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>After Hours Extension: </td><td align=left>\n";
        #echo "<input type=text name=after_hours_exten size=10 maxlength=20 value=\"$after_hours_exten\">\n";
        echo extension_text_options($link, 'after_hours_exten', $after_hours_exten, 15, 50);
        echo "".helptag("osdial_inbound_groups-after_hours_exten")."</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>After Hours Voicemail: </td><td align=left>\n";
        #echo "<input type=text name=after_hours_voicemail size=10 maxlength=20 value=\"$after_hours_voicemail\">\n";
        echo phone_voicemail_text_options($link, 'after_hours_voicemail', $after_hours_voicemail, 10, 20);
        echo "".helptag("osdial_inbound_groups-after_hours_voicemail")."</td></tr>\n";


        echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";

        ### list of agent rank or skill-level for this inbound group
        echo "<center>\n";
        echo "<br><font class=top_header2 color=$default_text size=+1>AGENT RANKS FOR THIS INBOUND GROUP</font<br><br>\n";
        echo "<table class=shadedtable bgcolor=grey width=400 cellspacing=1>\n";
        echo "  <tr class=tabheader>\n";
        echo "    <td align=center>USER</td>\n";
        echo "    <td align=center>FULL NAME</td>\n";
        echo "    <td align=center>RANK</td>\n";
        echo "    <td align=center>CALLS TODAY</td>\n";
        echo "  </tr>\n";

        if ($LOG['multicomp_admin'] > 0) {
            $stmt=sprintf("SELECT osdial_users.user,full_name,group_rank,calls_today FROM osdial_inbound_group_agents JOIN osdial_users ON (osdial_inbound_group_agents.user=osdial_users.user) WHERE group_id='%s' AND closer_campaigns LIKE '%% %s %%' AND SUBSTRING(osdial_users.user,1,3)=SUBSTRING(group_id,1,3) ORDER BY osdial_users.user;",mres($group_id),mres($group_id));
        } elseif ($LOG['multicomp'] > 0) {
            $stmt=sprintf("SELECT osdial_users.user,full_name,group_rank,calls_today FROM osdial_inbound_group_agents JOIN osdial_users ON (osdial_inbound_group_agents.user=osdial_users.user) WHERE group_id='%s' AND closer_campaigns LIKE '%% %s %%' AND osdial_users.user LIKE '%s__%%' ORDER BY osdial_users.user;",mres($group_id),mres($group_id),mres($LOG['company_prefix']));
        } else {
            $stmt=sprintf("SELECT osdial_users.user,full_name,group_rank,calls_today FROM osdial_inbound_group_agents JOIN osdial_users ON (osdial_inbound_group_agents.user=osdial_users.user) WHERE group_id='%s' AND closer_campaigns LIKE '%% %s %%' ORDER BY osdial_users.user;",mres($group_id),mres($group_id));
        }
        $rsltx=mysql_query($stmt, $link);
        $users_to_print = mysql_num_rows($rsltx);

        $o=0;
        while ($users_to_print > $o) {
            $rowx=mysql_fetch_row($rsltx);
            $o++;

            echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=3&user=$rowx[0]');\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=3&user=$rowx[0]\">$rowx[0]</a></td>\n";
            echo "    <td align=left>$rowx[1]</td>\n";
            echo "    <td align=right>$rowx[2]</td>\n";
            echo "    <td align=right>$rowx[3]</td>\n";
            echo "  </tr>\n";
        }

        //echo "</table><br>\n";

        echo "  <tr class=tabfooter>";
        echo "    <td colspan=4></td>";
        echo "  </tr>";
        echo "</table><br><br>\n";

        echo "<br><font class=top_header2 color=$default_text size=+1>CAMPAIGNS ASSIGNED TO THIS INBOUND GROUP</font<br><br>\n";
        echo "<table class=shadedtable bgcolor=grey width=400 cellspacing=1>\n";
        echo "  <tr class=tabheader>\n";
        echo "    <td align=center>CAMPAIGN_ID</td>\n";
        echo "    <td align=center>NAME</td>\n";
        echo "    <td align=center>ACTIVE</td>\n";
        echo "  </tr>\n";

        $stmt=sprintf("SELECT campaign_id,campaign_name,active FROM osdial_campaigns WHERE closer_campaigns LIKE '%% %s %%';",mres($group_id));
        $rsltx=mysql_query($stmt, $link);
        $camps_to_print = mysql_num_rows($rsltx);

        $o=0;
        while ($camps_to_print > $o) {
            $rowx=mysql_fetch_row($rsltx);
            $o++;

            echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=34&campaign_id=$rowx[0]');\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=34&campaign_id=$rowx[0]\">$rowx[0]</a></td>\n";
            echo "    <td>$rowx[1]</td>\n";
            echo "    <td align=center>$rowx[2]</td>\n";
            echo "  </tr>\n";
        }

        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=3></td>\n";
        echo "  </tr>\n";
        echo "</table><br>\n";

        #echo "<a href=\"./AST_CLOSERstats.php?group=$group_id\">Click here to see a report for this inbound group</a><BR><BR>\n";

        echo "</center>\n";

    // 	if ($LOG['delete_ingroups'] > 0) {
    // 		echo "<br><br><a href=\"$PHP_SELF?ADD=53&campaign_id=$group_id&stage=IN\">EMERGENCY CLEAR AUTO CALLS FOR THIS IN-GROUP</a><BR><BR>\n";
    // 		echo "<br><br><a href=\"$PHP_SELF?ADD=5111&group_id=$group_id\">DELETE THIS IN-GROUP</a>\n";
    // 	}
    } else {
		echo "<font color=red>You do not have permission to view this page</font>\n";
	}
}



######################
# ADD=1000 display all inbound groups
######################
if ($ADD==1000)
{
    $a2aSQL = "group_id NOT LIKE 'A2A_%'";
    if (get_variable('let')=='A2A') $a2aSQL = "group_id LIKE 'A2A_%'";
	$stmt=sprintf("SELECT * FROM osdial_inbound_groups WHERE group_id IN %s AND %s ORDER BY group_id",$LOG['allowed_ingroupsSQL'],$a2aSQL);
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font class=top_header color=$default_text size=+1>INBOUND GROUPS</font><br><br>\n";
echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>GROUP ID</td>\n";
echo "    <td>NAME</td>\n";
echo "    <td align=center>ACTIVE</td>\n";
echo "    <td align=center>VOICEMAIL</td>\n";
echo "    <td align=center>COLOR</td>\n";
echo "    <td align=center colspan=1>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3111&group_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=3111&group_id=$row[0]\">" . mclabel($row[0]) . "</a></td>\n";
		echo "    <td>$row[1]</td>\n";
		echo "    <td align=center>$row[3]</td>\n";
		echo "    <td align=center>$row[5]</td>\n";
		echo "    <td width=6 bgcolor=\"$row[2]\">&nbsp;</td>\n";
		echo "    <td align=center>&nbsp;<a href=\"$PHP_SELF?ADD=3111&group_id=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>";
echo "    <td colspan=6></td>";
echo "  </tr>";
echo "</TABLE></center>\n";
}


######################
# ADD=1001 display all A2A inbound groups
######################
if ($ADD==1001)
{
    $a2aSQL = "group_id LIKE 'A2A_%'";
	$stmt=sprintf("SELECT * FROM osdial_inbound_groups WHERE group_id IN %s AND %s ORDER BY group_id",$LOG['allowed_ingroupsSQL'],$a2aSQL);
	$rslt=mysql_query($stmt, $link);
	$people_to_print = mysql_num_rows($rslt);

echo "<center><br><font class=top_header color=$default_text size=+1>INBOUND AGENT TO AGENT GROUPS</font><br><br>\n";
echo "<table class=shadedtable width=$section_width cellspacing=0 cellpadding=1>\n";
echo "  <tr class=tabheader>\n";
echo "    <td>GROUP ID</td>\n";
echo "    <td>NAME</td>\n";
echo "    <td align=center>ACTIVE</td>\n";
echo "    <td align=center>VOICEMAIL</td>\n";
echo "    <td align=center>COLOR</td>\n";
echo "    <td align=center colspan=1>LINKS</td>\n";
echo "  </tr>\n";

	$o=0;
	while ($people_to_print > $o) {
		$row=mysql_fetch_row($rslt);
		echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3111&group_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=3111&group_id=$row[0]\">" . mclabel($row[0]) . "</a></td>\n";
		echo "    <td>$row[1]</td>\n";
		echo "    <td align=center>$row[3]</td>\n";
		echo "    <td align=center>$row[5]</td>\n";
		echo "    <td width=6 bgcolor=\"$row[2]\">&nbsp;</td>\n";
		echo "    <td align=center>&nbsp;<a href=\"$PHP_SELF?ADD=3111&group_id=$row[0]\">MODIFY</a></td>\n";
        echo "  </tr>\n";
		$o++;
	}

echo "  <tr class=tabfooter>";
echo "    <td colspan=6></td>";
echo "  </tr>";
echo "</TABLE></center>\n";
}

?>
