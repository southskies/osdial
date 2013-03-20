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
# 090410-1727 - Added allow_tab_switch
# 090420-1846 - Added answers_per_hour_limit
# 090515-0129 - Added preview_force_dial_time, fixed some minor errors on save.
# 090515-0140 - Added manual_preview_default
# 090515-0538 - Added web_form_extwindow and web_form2_extwindow
# 090519-2234 - Added INBOUND_MAN
# 090520-1915 - Changed inbound in manual mode to work without the INBOUND_MAN dial status.



######################
# ADD=73 view dialable leads from a filter and a campaign
######################

if ($ADD==73) {
    if ($LOG['modify_campaigns']==1) {
        $stmt=sprintf("SELECT dial_statuses,local_call_time,lead_filter_id FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $dial_statuses = $row[0];
        $local_call_time = $row[1];
        if ($lead_filter_id=='') {
            $lead_filter_id = $row[2];
            if ($lead_filter_id=='') {
                $lead_filter_id='NONE';
            }
        }

        $stmt=sprintf("SELECT list_id,active,list_name FROM osdial_lists WHERE campaign_id='$campaign_id';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $lists_to_print = mysql_num_rows($rslt);
        $camp_lists='';
        $o=0;
        while ($lists_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $o++;
            if (OSDpreg_match("/Y/", $rowx[1])) $camp_lists .= "'$rowx[0]',";
        }
        $camp_lists = OSDpreg_replace('/,$/','',$camp_lists);

        $fSQL = '';
        $Tfilter = get_first_record($link, 'osdial_lead_filters', '*', sprintf("lead_filter_id='%s'",mres($lead_filter_id)) );
        if (OSDstrlen($Tfilter['lead_filter_sql'])>4) $fSQL = "and " . OSDpreg_replace('/^and|and$|^or|or$/i','',$Tfilter['lead_filter_sql']);

        echo "<br><br>";
        echo "<b>Show Dialable Leads Count</b> -<br><br>";
        echo "<b>CAMPAIGN:</B> $campaign_id<br>";
        echo "<b>LISTS:</B> $camp_lists<br>";
        echo "<b>STATUSES:</B> $dial_statuses<br>";
        echo "<b>FILTER:</B> $lead_filter_id<br>";
        echo "<b>CALL TIME:</B> $local_call_time<br><br>";

        ### call function to calculate and print dialable leads
        dialable_leads($DB,$link,$local_call_time,$dial_statuses,$camp_lists,$fSQL);

        echo "<br><br>";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>";
    }
}


######################
# ADD=11 display the ADD NEW CAMPAIGN FORM SCREEN
######################

if ($ADD==11) {
    if ($LOG['modify_campaigns']==1) {
    
		// LIST CAMPAIGNS
		$let = get_variable('let');
		$letSQL = '';
		if ($let != '') $letSQL = sprintf("AND campaign_id LIKE '%s%s%%'",$LOG['company_prefix'],$let);

		$dispact = get_variable('dispact');
		$dispactSQL = '';
		if ($dispact == 1) $dispactSQL = "AND active='Y'";

		$stmt=sprintf("SELECT * FROM osdial_campaigns WHERE campaign_id IN %s %s %s ORDER BY campaign_id;",$LOG['allowed_campaignsSQL'],$letSQL,$dispactSQL);

		$rslt=mysql_query($stmt, $link);
		$people_to_print = mysql_num_rows($rslt);
		
		echo "<div style='float:left;width:100px;height=550;margin:10 0 0 5'>";
			echo "<table bgcolor=#eee width=87 cellspacing=0 cellpadding=1 class=rounded-inset2>";
			echo "  <tr>";
			echo "    <td bgcolor=#ddd class=rounded-tab style=font-size:11px;text-align:center;color:#009;>Campaigns</td>";
			echo "  </tr>";
			$o=0;
			while ($people_to_print > $o) {
				$row=mysql_fetch_row($rslt);
				echo "  <tr class=\"row font1\"  ondblclick=\"window.location='$PHP_SELF?ADD=34&campaign_id=$row[0]';\">";
				echo "    <td><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[0]\">" . mclabel($row[0]) . "</a></td>";
				/*  
				echo "    <td>$row[1]</td>";
				echo "    <td align=center><font size=1>$row[2]</td>";
				echo "    <td colspan=7 align=center><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[0]\">MODIFY</a></td>";
				*/
				echo "  </tr>";
				$o++;
			}
			echo "  <tr Xclass=tabfooter>";
			echo "    <td></td>";
			echo "  </tr>";
			echo "</table></center>";
		echo "</div>";

		
		// ADD A NEW CAMPAIGN
		echo "<div style='overflow:auto;'>";
			echo "<br><div style='text-align:center;margin:0 100 0 0;'><font class=top_header color=$default_text size=+1>ADD A NEW CAMPAIGN</font><form action=$PHP_SELF method=POST></div><br><br>";
			echo "<input type=hidden name=DB value=$DB>";
			echo "<input type=hidden name=ADD value=21>";
			echo "<table width=760 oldwidth=$section_width cellspacing=3>";
			echo "<tr><td align=right width=40%>ID: </td><td align=left width=60%>";
			if ($LOG['multicomp_admin'] > 0) {
				$comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
				echo "<select name=company_id>";
				foreach ($comps as $comp) {
					echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>";
				}
				echo "</select>";
			} elseif ($LOG['multicomp']>0) {
				echo "<input type=hidden name=company_id value=$LOG[company_id]>";
				#echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
			}
			echo "<input type=text name=campaign_id size=10 maxlength=8><font size=-1>2-8 Characters, no spaces or symbols</font>&nbsp;&nbsp;$NWB#osdial_campaigns-campaign_id$NWE</td></tr>";
			echo "<tr><td align=right>Name: </td><td align=left><input type=text name=campaign_name size=30 maxlength=30>$NWB#osdial_campaigns-campaign_name$NWE</td></tr>";
			echo "<tr><td align=right>Description: </td><td align=left><input type=text name=campaign_description size=30 maxlength=255>$NWB#osdial_campaigns-campaign_description$NWE</td></tr>";
			echo "<tr><td align=right>Active: </td><td align=left><select size=1 name=active><option>N</option><option>Y</option></select>$NWB#osdial_campaigns-active$NWE</td></tr>";
			
			// Add ten default Statuses
			
			/*
			echo "<tr style=\"visibility:collapse;\" ><td align=right>Park Extension: </td><td align=left>";
			#echo "<input type=text name=park_ext size=10 maxlength=10 value=\"8301\">";
			echo media_extension_text_options($link, 'park_ext', '8301', 10, 10);
			echo "$NWB#osdial_campaigns-park_ext$NWE</td></tr>";
			echo "<tr><td align=right>Park Filename: </td><td align=left>";
			#echo "<input type=text name=park_file_name size=10 maxlength=10 value=\"park\">";
			echo media_file_text_options($link, 'park_file_name', 'park', 10, 10);
			echo "$NWB#osdial_campaigns-park_file_name$NWE</td></tr>";
			echo "<tr><td align=right>Web Form 1: </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"/osdial/agent/webform_redirect.php\">$NWB#osdial_campaigns-web_form_address$NWE</td></tr>";
			echo "<tr><td align=right>Web Form 2: </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"/osdial/agent/webform_redirect.php\">$NWB#osdial_campaigns-web_form_address$NWE</td></tr>";
			echo "<tr><td align=right>Allow Transfer and Closers: </td><td align=left><select size=1 name=allow_closers><option>Y</option><option>N</option></select>$NWB#osdial_campaigns-allow_closers$NWE</td></tr>";
			echo "<tr><td align=right>Hopper Level: </td><td align=left><select size=1 name=hopper_level><option>1</option><option>5</option><option>10</option><option>20</option><option>50</option><option>100</option><option>200</option><option>500</option><option>1000</option><option>2000</option></select>$NWB#osdial_campaigns-hopper_level$NWE</td></tr>";
			echo "<tr><td align=right>Auto Dial Level: </td><td align=left nowrap><input type=text name=auto_dial_level size=6 maxlength=6 value=\"0\" selectBoxOptions=\"0;1;1.1;1.2;1.3;1.4;1.5;1.6;1.7;1.8;1.9;2.0;2.2;2.5;3.0;4.0;4.5;5.0\"> (0 = off)$NWB#osdial_campaigns-auto_dial_level$NWE</td></tr>";
			echo "<tr><td align=right>Next Agent Call: </td><td align=left><select size=1 name=next_agent_call><option >random</option><option>oldest_call_start</option><option>oldest_call_finish</option><option>overall_user_level</option><option>campaign_rank</option><option>fewest_calls</option></select>$NWB#osdial_campaigns-next_agent_call$NWE</td></tr>";
			echo "<tr><td align=right>Operation Time: </td><td align=left><select size=1 name=campaign_call_time>";
			echo get_calltimes($link, '24hours');
			echo "</select>$NWB#osdial_campaigns-campaign_call_time$NWE</td></tr>";
			echo "<tr><td align=right>Local Timzone Call Time: </td><td align=left><select size=1 name=local_call_time>";
			echo get_calltimes($link, '9am-9pm');
			echo "</select>$NWB#osdial_campaigns-local_call_time$NWE</td></tr>";
			echo "<tr><td align=right>Voicemail: </td><td align=left>";
			#echo "<input type=text name=voicemail_ext size=10 maxlength=10 value=\"$voicemail_ext\">";
			echo phone_voicemail_text_options($link, 'voicemail_ext', $voicemail_ext, 10, 10);
			echo "$NWB#osdial_campaigns-voicemail_ext$NWE</td></tr>";
			if (file_exists($WeBServeRRooT . '/admin/include/content/scripts/email_templates.php')) {
				echo "<tr bgcolor=$oddrows><td align=right valign=top>Email Templates: </td><td align=left><select size=4 multiple name=\"email_templates[]\">";
				echo get_email_templates($link, '');
				echo "</select>$NWB#osdial_campaigns-email_templates$NWE</td></tr>";
			}
			echo "<tr><td align=right>Script: </td><td align=left><select size=1 name=script_id>";
			echo get_scripts($link, '');
			echo "</select>$NWB#osdial_campaigns-campaign_script$NWE</td></tr>";
			echo "<tr><td align=right>Allow Tab Switch: </td><td align=left><select size=1 name=allow_tab_switch><option selected>Y</option><option>N</option></select>$NWB#osdial_campaigns-allow_tab_switch$NWE</td></tr>";
			echo "<tr><td align=right>Get Call Launch: </td><td align=left><select size=1 name=get_call_launch><option selected>NONE</option><option>SCRIPT</option><option>WEBFORM</option><option>WEBFORM2</option></select>$NWB#osdial_campaigns-get_call_launch$NWE</td></tr>";
			*/
			echo "<tr><td colspan=2>&nbsp;</td></tr>";
			echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=ADD></td></tr>";
			echo "</table>";
			echo "</form>";

			echo "<script type=\"text/javascript\">";
			echo "createEditableSelect(document.forms[0].auto_dial_level);";
			echo "</script>";
		echo "</div>";
		//echo "</div>";
	} else {
		echo "<font color=red>You do not have permission to view this page</font>";
	}
    
}


######################
# ADD=12 display the COPY CAMPAIGN FORM SCREEN
######################

if ($ADD==12)
{
    if ($LOG['modify_campaigns']==1) {
    
		// LIST CAMPAIGNS
		$let = get_variable('let');
		$letSQL = '';
		if ($let != '') $letSQL = sprintf("AND campaign_id LIKE '%s%s%%'",$LOG['company_prefix'],$let);

		$dispact = get_variable('dispact');
		$dispactSQL = '';
		if ($dispact == 1) $dispactSQL = "AND active='Y'";

		$stmt=sprintf("SELECT * FROM osdial_campaigns WHERE campaign_id IN %s %s %s ORDER BY campaign_id;",$LOG['allowed_campaignsSQL'],$letSQL,$dispactSQL);

		$rslt=mysql_query($stmt, $link);
		$people_to_print = mysql_num_rows($rslt);
		
		echo "<div style='float:left;width:100px;height=260px;margin:10 0 0 5'>";
			echo "<table bgcolor=#eee width=87 cellspacing=0 cellpadding=1 class=rounded-inset>";
			echo "  <tr>";
			echo "    <td style=font-size:11px;text-align:center;color:#009;>Campaigns</td>";
			echo "  </tr>";
			$o=0;
			while ($people_to_print > $o) {
				$row=mysql_fetch_row($rslt);
				echo "  <tr class=\"row font1\"  ondblclick=\"window.location='$PHP_SELF?ADD=34&campaign_id=$row[0]';\">";
				echo "    <td><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[0]\">" . mclabel($row[0]) . "</a></td>";
				echo "  </tr>";
				$o++;
			}
			echo "  <tr>";
			echo "    <td></td>";
			echo "  </tr>";
			echo "</table></center>";
		echo "</div>";

		// COPY A CAMPAIGN
		echo "<div style='overflow:auto;'>";
		echo "<br><div style='text-align:center;margin:0 100 0 0;'><font class=top_header color=$default_text size=+1>COPY A CAMPAIGN</font><form action=$PHP_SELF method=POST></div><br><br>";
		echo "<input type=hidden name=DB value=$DB>";
		echo "<input type=hidden name=ADD value=20>";
		echo "<table width=760 oldwidth=$section_width cellspacing=3>";
		echo "<tr><td align=right width=40%>ID: </td><td align=left width=60%>";
		if ($LOG['multicomp_admin'] > 0) {
			$comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
			echo "<select name=company_id>";
			foreach ($comps as $comp) {
				echo "<option value=$comp[id]>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>";
			}
			echo "</select>";
		} elseif ($LOG['multicomp']>0) {
			echo "<input type=hidden name=company_id value=$LOG[company_id]>";
			#echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
		}
		echo "<input type=text name=campaign_id size=10 maxlength=8>$NWB#osdial_campaigns-campaign_id$NWE</td></tr>";
		echo "<tr><td align=right>Name: </td><td align=left><input type=text name=campaign_name size=30 maxlength=30>$NWB#osdial_campaigns-campaign_name$NWE</td></tr>";

		echo "<tr><td align=right>Source Campaign: </td><td align=left><select size=1 name=source_campaign_id>";

			$stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE campaign_id IN %s ORDER BY campaign_id;",$LOG['allowed_campaignsSQL']);
			$rslt=mysql_query($stmt, $link);
			$campaigns_to_print = mysql_num_rows($rslt);
			$campaigns_list='';

			$o=0;
			while ($campaigns_to_print > $o) {
				$rowx=mysql_fetch_row($rslt);
				$campaigns_list .= "<option value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>";
				$o++;
			}
		echo "$campaigns_list";
		echo "</select>$NWB#osdial_campaigns-campaign_id$NWE</td></tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr class=tabfooter><td align=center class=tabbutton colspan=2><input type=submit name=SUBMIT value=COPY></td></tr>";
		echo "</table></center>";
		echo "</div>";
	} else {
		echo "<font color=red>You do not have permission to view this page</font>";
    }
}



######################
# ADD=21 adds the new campaign to the system
######################

if ($ADD==21)
{
    $precampaign_id = $campaign_id;
    if ($LOG['multicomp'] > 0) $precampaign_id = (($company_id * 1) + 100) . $campaign_id;
    $stmt=sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_id='%s';",mres($precampaign_id));
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    if ($row[0] > 0)
        {echo "<br><font color=red> CAMPAIGN NOT ADDED - there is already a campaign in the system with this ID</font>";}
    else
        {
        $stmt=sprintf("SELECT count(*) FROM osdial_inbound_groups WHERE group_id='%s';",mres($precampaign_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0)
            {echo "<br><font color=red> CAMPAIGN NOT ADDED - there is already an inbound group in the system with this ID</font>";}
        else
            {
             if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($campaign_id) > 8) or (OSDstrlen($campaign_name) < 6)  or (OSDstrlen($campaign_name) > 40) )
                {
                 echo "<br><font color=red> CAMPAIGN NOT ADDED - Please go back and look at the data you entered";
                 echo "<br>campaign ID must be between 2 and 8 characters in length";
                 echo "<br>campaign name must be between 6 and 40 characters in length</font><br>";
                }
             else
                {
                if ($LOG['multicomp'] > 0) $campaign_id = (($company_id * 1) + 100) . $campaign_id;
                echo "<br><B><font color=$default_text> CAMPAIGN ADDED: $campaign_id</font></B>";

                $LOG['companiesRE'] = rtrim($LOG['companiesRE'], '/') . "|^" . (($company_id * 1) + 100) . "/";
                $LOG['allowed_campaignsSQL'] = rtrim($LOG['allowed_campaignsSQL'], ')');
                $LOG['allowed_campaignsSQL'] .= ",'$campaign_id')";
                $LOG['allowed_campaignsSTR'] .= "$campaign_id:";
                $LOG['allowed_campaigns'][] .= $campaign_id;
                if ($LOG['allowed_campaignsALL']<1) {
                    $stmt=sprintf("UPDATE osdial_user_groups SET allowed_campaigns=' %s -' WHERE user_group='%s';",mres(implode(" ",$LOG['allowed_campaigns'])),mres($LOG['user_group']));  
                    $rslt=mysql_query($stmt, $link);
                }
                $carrier_id = $config['settings']['default_carrier_id'];
                $ets = implode(',',$email_templates);
		$allow_tab_switch='Y';
		$allow_closers='Y';
		$next_agent_call='oldest_call_finish';
		$hopper_level='200';
		$web_form_address='/osdial/agent/webform_redirect.php';
		$web_form_address2='/osdial/agent/webform_redirect.php';
		$local_call_time='9am-9pm';
		$campaign_call_time='24hours';
		$get_call_launch='NONE';
                $stmt=sprintf("INSERT INTO osdial_campaigns (campaign_id,campaign_name,campaign_description,active,dial_status_a,lead_order,park_ext,park_file_name,web_form_address,allow_closers,hopper_level,auto_dial_level,next_agent_call,local_call_time,voicemail_ext,campaign_script,get_call_launch,campaign_changedate,campaign_stats_refresh,list_order_mix,web_form_address2,allow_tab_switch,campaign_call_time,carrier_id,email_templates,dial_statuses,no_hopper_leads_logins,campaign_allow_inbound,campaign_rec_filename,amd_send_to_vmx,safe_harbor_message,safe_harbor_exten,web_form_extwindow,web_form2_extwindow,scheduled_callbacks,agent_pause_codes_active,use_internal_dnc) values('%s','%s','%s','%s','NEW','DOWN','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','Y','DISABLED','%s','%s','%s','%s','%s',' A AA B CALLBK DROP NEW N NA -','Y','Y','CAMPAIGN_AGENT_FULLDATE_CUSTPHONE','Y','Y','8307','Y','Y','Y','Y','Y');",mres($campaign_id),mres($campaign_name),mres($campaign_description),mres($active),mres($park_ext),mres($park_file_name),mres($web_form_address),mres($allow_closers),mres($hopper_level),mres($auto_dial_level),mres($next_agent_call),mres($local_call_time),mres($voicemail_ext),mres($script_id),mres($get_call_launch),mres($SQLdate),mres($web_form_address2),mres($allow_tab_switch),mres($campaign_call_time),mres($carrier_id),mres($ets));
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("INSERT INTO osdial_campaign_stats (campaign_id) VALUES('%s');",mres($campaign_id));
                $rslt=mysql_query($stmt, $link);

                echo "<!-- $stmt -->";
                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0)
                    {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|ADD A NEW CAMPAIGN  |$PHP_AUTH_USER|$ip|$stmt|");
                    fclose($fp);
                    }

                if ($LOG['allowed_campaignsALL']==0) {
                    $LOG['allowed_campaigns'][] = $campaign_id;
                    $campaigns_value = ' ' . implode(' ', $LOG['allowed_campaigns']) . ' -';
                    $stmt = sprintf("UPDATE osdial_user_groups SET allowed_campaigns='%s' WHERE user_group='%s';",mres($campaigns_value),mres($LOG['user_group']));
				    $rslt=mysql_query($stmt, $link);
                }

                }
            }
        }
$ADD=31;
}

######################
# ADD=20 adds copied new campaign to the system
######################

if ($ADD==20)
{
    $precampaign_id = $campaign_id;
    if ($LOG['multicomp'] > 0) $precampaign_id = (($company_id * 1) + 100) . $campaign_id;
    $stmt=sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_id='%s';",mres($precampaign_id));
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    if ($row[0] > 0)
        {echo "<br><font color=red> CAMPAIGN NOT ADDED - there is already a campaign in the system with this ID</font>";}
    else
        {
         if ( (OSDstrlen($campaign_id) < 2) or (OSDstrlen($campaign_id) > 8) or  (OSDstrlen($campaign_name) < 2) or (OSDstrlen($source_campaign_id) < 2) or (OSDstrlen($source_campaign_id) > 8) )
            {
             echo "<br><font color=red> CAMPAIGN NOT ADDED - Please go back and look at the data you entered";
             echo "<br>campaign ID must be between 2 and 8 characters in length";
             echo "<br>source campaign ID must be between 2 and 8 characters in length</font><br>";
            }
         else
            {
            if ($LOG['multicomp'] > 0) $campaign_id = (($company_id * 1) + 100) . $campaign_id;
            echo "<br><B><font color=$default_text> CAMPAIGN COPIED: $campaign_id copied from $source_campaign_id</font></B>";

            $LOG['companiesRE'] = rtrim($LOG['companiesRE'], '/') . "|^" . (($company_id * 1) + 100) . "/";
            $LOG['allowed_campaignsSQL'] = rtrim($LOG['allowed_campaignsSQL'], ')');
            $LOG['allowed_campaignsSQL'] .= ",'$campaign_id')";
            $LOG['allowed_campaignsSTR'] .= "$campaign_id:";
            $stmt="INSERT INTO osdial_campaigns ";
            $stmt .= "(campaign_name,campaign_id,active,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,park_ext,park_file_name,web_form_address,allow_closers,hopper_level,";
            $stmt .= "auto_dial_level,next_agent_call,local_call_time,voicemail_ext,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,";
            $stmt .= "campaign_script,get_call_launch,am_message_exten,amd_send_to_vmx,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,lead_filter_id,";
            $stmt .= "drop_call_seconds,safe_harbor_message,safe_harbor_exten,display_dialable_count,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,dial_method,";
            $stmt .= "available_only_ratio_tally,adaptive_dropped_percentage,adaptive_maximum_level,adaptive_latest_server_time,adaptive_intensity,adaptive_dl_diff_target,concurrent_transfers,auto_alt_dial,";
            $stmt .= "auto_alt_dial_statuses,agent_pause_codes_active,campaign_description,campaign_changedate,campaign_stats_refresh,campaign_logindate,dial_statuses,disable_alter_custdata,no_hopper_leads_logins,";
            $stmt .= "list_order_mix,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,web_form_address2,allow_tab_switch,answers_per_hour_limit,campaign_call_time,preview_force_dial_time,";
            $stmt .= "manual_preview_default,web_form_extwindow,web_form2_extwindow,submit_method,use_custom2_callerid,campaign_cid_name,xfer_cid_mode,use_cid_areacode_map,carrier_id,email_templates,disable_manual_dial) ";

            $stmt .= sprintf("SELECT '%s','%s',",mres($campaign_name),mres($campaign_id));
            $stmt .= "'N',dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,park_ext,park_file_name,web_form_address,allow_closers,hopper_level,";
            $stmt .= "auto_dial_level,next_agent_call,local_call_time,voicemail_ext,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,";
            $stmt .= "campaign_script,get_call_launch,am_message_exten,amd_send_to_vmx,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,lead_filter_id,";
            $stmt .= "drop_call_seconds,safe_harbor_message,safe_harbor_exten,display_dialable_count,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,dial_method,";
            $stmt .= "available_only_ratio_tally,adaptive_dropped_percentage,adaptive_maximum_level,adaptive_latest_server_time,adaptive_intensity,adaptive_dl_diff_target,concurrent_transfers,auto_alt_dial,";
            $stmt .= "auto_alt_dial_statuses,agent_pause_codes_active,campaign_description,campaign_changedate,campaign_stats_refresh,campaign_logindate,dial_statuses,disable_alter_custdata,no_hopper_leads_logins,";
            $stmt .= "'DISABLED',campaign_allow_inbound,manual_dial_list_id,default_xfer_group,web_form_address2,allow_tab_switch,answers_per_hour_limit,campaign_call_time,preview_force_dial_time,";
            $stmt .= "manual_preview_default,web_form_extwindow,web_form2_extwindow,submit_method,use_custom2_callerid,campaign_cid_name,xfer_cid_mode,use_cid_areacode_map,carrier_id,email_templates,disable_manual_dial ";

            $stmt .= sprintf("FROM osdial_campaigns WHERE campaign_id='%s';",mres($source_campaign_id));
            $rslt=mysql_query($stmt, $link);

            $stmtA=sprintf("INSERT INTO osdial_campaign_stats (campaign_id) VALUES('%s');",mres($campaign_id));
            $rslt=mysql_query($stmtA, $link);

            $stmtA=sprintf("INSERT INTO osdial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category) SELECT status,status_name,selectable,'%s',human_answered,category FROM osdial_campaign_statuses WHERE campaign_id='%s';",mres($campaign_id),mres($source_campaign_id));
            $rslt=mysql_query($stmtA, $link);

            $stmtA=sprintf("INSERT INTO osdial_campaign_hotkeys (status,hotkey,status_name,selectable,campaign_id,xfer_exten) SELECT status,hotkey,status_name,selectable,'%s',xfer_exten FROM osdial_campaign_hotkeys WHERE campaign_id='%s';",mres($campaign_id),mres($source_campaign_id));
            $rslt=mysql_query($stmtA, $link);

            $stmtA=sprintf("INSERT INTO osdial_lead_recycle (status,attempt_delay,attempt_maximum,active,campaign_id) SELECT status,attempt_delay,attempt_maximum,active,'%s' FROM osdial_lead_recycle WHERE campaign_id='%s';",mres($campaign_id),mres($source_campaign_id));
            $rslt=mysql_query($stmtA, $link);

            $stmtA=sprintf("INSERT INTO osdial_pause_codes (pause_code,pause_code_name,billable,campaign_id) SELECT pause_code,pause_code_name,billable,'%s' FROM osdial_pause_codes WHERE campaign_id='%s';",mres($campaign_id),mres($source_campaign_id));
            $rslt=mysql_query($stmtA, $link);

            $ccivr = get_first_record($link, 'osdial_ivr', '*', sprintf("campaign_id LIKE '%s'",mres($source_campaign_id)));
            if (is_array($ccivr)) {
                $stmtA=sprintf("INSERT INTO osdial_ivr (campaign_id,name,announcement,repeat_loops,wait_loops,wait_timeout,answered_status,virtual_agents,status,timeout_action,reserve_agents,allow_inbound) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');",mres($campaign_id),mres($ccivr['name']),mres($ccivr['announcement']),mres($ccivr['repeat_loops']),mres($ccivr['wait_loops']),mres($ccivr['wait_timeout']),mres($ccivr['answered_status']),mres($ccivr['virtual_agents']),mres($ccivr['status']),mres($ccivr['timeout_action']),mres($ccivr['reserve_agents']),mres($ccivr['allow_inbound']));
                $rslt=mysql_query($stmtA, $link);
                $new_ivr_id =  mysql_insert_id($link);

                $ccivropts = get_krh($link, 'osdial_ivr_options', '*','parent_id ASC',sprintf("ivr_id='%s'",mres($ccivr['id'])),'');
                if (is_array($ccivropts)) {
                    $ccidmap = Array();
                    foreach ($ccivropts as $ccivropt) {
                        $ccidmap[$ccivropt['id']] = 0;
                        $ccivrad = explode('#:#',$ccivropt['action_data']);
                        if ($ccivropt['action'] == 'MENU') $ccivrad[0] = $new_ivr_id;
                        $new_cc_ad = implode('#:#',$ccivrad);
                        $stmtA=sprintf("INSERT INTO osdial_ivr_options (ivr_id,parent_id,keypress,action,action_data) VALUES ('%s','%s','%s','%s','%s');",mres($new_ivr_id),mres($ccidmap[$ccivropt['parent_id']]),mres($ccivropt['keypress']),mres($ccivropt['action']),mres($new_cc_ad));
                        $rslt=mysql_query($stmtA, $link);
                        $new_ivropt_id =  mysql_insert_id($link);
                        if ($ccivropt['action'] == 'MENU') $ccidmap[$ccivropt['id']] = $new_ivropt_id;
                    }
                }
            }

            echo "<!-- $stmt -->";
            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0)
                {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|COPY TO NEW CAMPAIGN|$PHP_AUTH_USER|$ip|$campaign_id|$source_campaign_id|$stmt|$stmtA|");
                fclose($fp);
                }

                if ($LOG['allowed_campaignsALL']==0) {
                    $LOG['allowed_campaigns'][] = $campaign_id;
                    $campaigns_value = ' ' . implode(' ', $LOG['allowed_campaigns']) . ' -';
                    $stmt = sprintf("UPDATE osdial_user_groups SET allowed_campaigns='%s' WHERE user_group='%s';",mres($campaigns_value),mres($LOG['user_group']));
				    $rslt=mysql_query($stmt, $link);
                }

            }
        }
$ADD=31;
}


######################
# ADD=41 submit campaign modifications to the system
######################

if ($ADD==41)
{
    if ($LOG['modify_campaigns']==1)
    {
     if ( (OSDstrlen($campaign_name) < 6) or (OSDstrlen($active) < 1) )
        {
         echo "<br><font color=red>CAMPAIGN NOT MODIFIED - Please go back and look at the data you entered";
         echo "<br>the campaign name needs to be at least 6 characters in length";
         echo "<br>|$campaign_name|$active|</font><br>";
        }
     else
        {
        echo "<br><B><font color=$default_text>CAMPAIGN MODIFIED: $campaign_id</font></B>";
        
	if ($dial_method == 'MANUAL' and $campaign_allow_inbound != 'Y')
		    {
		    $auto_dial_level='0';
		    $adlSQL = "auto_dial_level='0',";
		    }
		else
		    {
		    if ($dial_level_override > 0)
			{
			$auto_dial_level = get_variable("ADAPT_auto_dial_level");
			$adlSQL = sprintf("auto_dial_level='%s',",mres($auto_dial_level));
			$available_only_ratio_tally = get_variable('ADAPT_available_only_ratio_tally');
			}
		    else
			{
			if ($dial_method == 'RATIO')
			    {
			    if ($auto_dial_level < 1) {$auto_dial_level = "1.0";}
			    $adlSQL = sprintf("auto_dial_level='%s',",mres($auto_dial_level));
			    }
			else
			    {
			    $auto_dial_level = get_variable("ADAPT_auto_dial_level");
			    $adlSQL = "";
			    if ($auto_dial_level < 1)
				{
				$auto_dial_level = "1.0";
				$adlSQL = sprintf("auto_dial_level='%s',",mres($auto_dial_level));
				}
			    }
			}
		    }
            
            
        if ( (!OSDpreg_match("/DISABLED/",$list_order_mix)) and ($hopper_level < 100) )
            {$hopper_level='100';}

        if (OSDpreg_match('/^8510/',$am_message_exten)) $am_message_exten = '8320'.$am_message_exten;

        $lo_array = array();
        if (!empty($lead_order_direction)) $lo_array[] = $lead_order_direction;
        if (!empty($lead_order_field)) $lo_array[] = $lead_order_field;
        if (!empty($lead_order_nthnew)) $lo_array[] = $lead_order_nthnew;
        $lead_order = implode(' ', $lo_array);
        if (is_array($email_templates)) $ets = implode(',',$email_templates);
        $stmtA=sprintf("UPDATE osdial_campaigns SET %s campaign_name='%s',active='%s',dial_status_a='%s',dial_status_b='%s',dial_status_c='%s',dial_status_d='%s',"
            ."dial_status_e='%s',lead_order='%s',allow_closers='%s',hopper_level='%s',next_agent_call='%s',local_call_time='%s',voicemail_ext='%s',"
            ."dial_timeout='%s',dial_prefix='%s',campaign_cid='%s',campaign_vdad_exten='%s',web_form_address='%s',park_ext='%s',park_file_name='%s',"
            ."campaign_rec_exten='%s',campaign_recording='%s',campaign_rec_filename='%s',campaign_script='%s',get_call_launch='%s',am_message_exten='%s',"
            ."amd_send_to_vmx='%s',xferconf_a_dtmf='%s',xferconf_a_number='%s',xferconf_b_dtmf='%s',xferconf_b_number='%s',lead_filter_id='%s',"
            ."alt_number_dialing='%s',scheduled_callbacks='%s',safe_harbor_message='%s',drop_call_seconds='%s',safe_harbor_exten='%s',wrapup_seconds='%s',"
            ."wrapup_message='%s',closer_campaigns='%s',use_internal_dnc='%s',allcalls_delay='%s',omit_phone_code='%s',dial_method='%s',available_only_ratio_tally='%s',"
            ."adaptive_dropped_percentage='%s',adaptive_maximum_level='%s',adaptive_latest_server_time='%s',adaptive_intensity='%s',adaptive_dl_diff_target='%s',"
            ."concurrent_transfers='%s',auto_alt_dial='%s',agent_pause_codes_active='%s',campaign_description='%s',campaign_changedate='%s',campaign_stats_refresh='%s',"
            ."disable_alter_custdata='%s',no_hopper_leads_logins='%s',list_order_mix='%s',campaign_allow_inbound='%s',manual_dial_list_id='%s',"
            ."default_xfer_group='%s',xfer_groups='%s',web_form_address2='%s',allow_tab_switch='%s',answers_per_hour_limit='%s',campaign_call_time='%s',"
            ."preview_force_dial_time='%s',manual_preview_default='%s',web_form_extwindow='%s',web_form2_extwindow='%s',submit_method='%s',use_custom2_callerid='%s',"
            ."campaign_cid_name='%s',xfer_cid_mode='%s',use_cid_areacode_map='%s',carrier_id='%s',email_templates='%s',disable_manual_dial='%s',"
            ."hide_xfer_local_closer='%s',hide_xfer_dial_override='%s',hide_xfer_hangup_xfer='%s',hide_xfer_leave_3way='%s',hide_xfer_dial_with='%s',"
            ."hide_xfer_hangup_both='%s',hide_xfer_blind_xfer='%s',hide_xfer_park_dial='%s',hide_xfer_blind_vmail='%s',allow_md_hopperlist='%s' "
            ."WHERE campaign_id='%s';",
            $adlSQL,mres($campaign_name),mres($active),mres($dial_status_a),mres($dial_status_b),mres($dial_status_c),mres($dial_status_d),
            mres($dial_status_e),mres($lead_order),mres($allow_closers),mres($hopper_level),mres($next_agent_call),mres($local_call_time),mres($voicemail_ext),
            mres($dial_timeout),mres($dial_prefix),mres($campaign_cid),mres($campaign_vdad_exten),mres($web_form_address),mres($park_ext),mres($park_file_name),
            mres($campaign_rec_exten),mres($campaign_recording),mres($campaign_rec_filename),mres($script_id),mres($get_call_launch),mres($am_message_exten),
            mres($amd_send_to_vmx),mres($xferconf_a_dtmf),mres($xferconf_a_number),mres($xferconf_b_dtmf),mres($xferconf_b_number),mres($lead_filter_id),
            mres($alt_number_dialing),mres($scheduled_callbacks),mres($safe_harbor_message),mres($drop_call_seconds),mres($safe_harbor_exten),mres($wrapup_seconds),
            mres($wrapup_message),mres($groups_value),mres($use_internal_dnc),mres($allcalls_delay),mres($omit_phone_code),mres($dial_method),mres($available_only_ratio_tally),
            mres($adaptive_dropped_percentage),mres($adaptive_maximum_level),mres($adaptive_latest_server_time),mres($adaptive_intensity),mres($adaptive_dl_diff_target),
            mres($concurrent_transfers),mres($auto_alt_dial),mres($agent_pause_codes_active),mres($campaign_description),mres($SQLdate),mres($campaign_stats_refresh),
            mres($disable_alter_custdata),mres($no_hopper_leads_logins),mres($list_order_mix),mres($campaign_allow_inbound),mres($manual_dial_list_id),
            mres($default_xfer_group),mres($XFERgroups_value),mres($web_form_address2),mres($allow_tab_switch),mres($answers_per_hour_limit),mres($campaign_call_time),
            mres($preview_force_dial_time),mres($manual_preview_default),mres($web_form_extwindow),mres($web_form2_extwindow),mres($submit_method),mres($use_custom2_callerid),
            mres($campaign_cid_name),mres($xfer_cid_mode),mres($use_cid_areacode_map),mres($carrier_id),mres($ets),mres($disable_manual_dial),
            mres($hide_xfer_local_closer),mres($hide_xfer_dial_override),mres($hide_xfer_hangup_xfer),mres($hide_xfer_leave_3way),mres($hide_xfer_dial_with),
            mres($hide_xfer_hangup_both),mres($hide_xfer_blind_xfer),mres($hide_xfer_park_dial),mres($hide_xfer_blind_vmail),mres($allow_md_hopperlist),
            mres($campaign_id));
        if ($DB) echo $stmtA;
        $rslt=mysql_query($stmtA, $link);

        if ($reset_hopper == 'Y')
            {
            echo "<br><font color=$default_text>RESETTING CAMPAIGN LEAD HOPPER";
            echo "<br> - Wait 1 minute before dialing next number</font>";
            $stmt=sprintf("DELETE FROM osdial_hopper WHERE campaign_id='%s' AND status IN('READY','QUEUE','DONE');",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);

            ### LOG RESET TO LOG FILE ###
            if ($WeBRooTWritablE > 0)
                {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|CAMPAIGN HOPPERRESET|$PHP_AUTH_USER|$ip|$stmt|");
                fclose($fp);
                }
            }

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0)
            {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|MODIFY CAMPAIGN INFO|$PHP_AUTH_USER|$ip|$stmtA|$reset_hopper|");
            fclose($fp);
            }
        }
    $ADD=31;    # go to campaign modification form below
    }
    else
    {
    echo "<font color=red>You do not have permission to view this page</font>";
    }
}


######################
# ADD=44 submit campaign modifications to the system - Basic View
######################
/*
if ($ADD==44)
{
    if ($LOG['modify_campaigns']==1)
    {
     if ( (OSDstrlen($campaign_name) < 6) or (OSDstrlen($active) < 1) )
        {
         echo "<br><font color=red>CAMPAIGN NOT MODIFIED - Please go back and look at the data you entered";
         echo "<br>the campaign name needs to be at least 6 characters in length</font><br>";
        }
     else
        {
        echo "<br><B><font color=$default_text>CAMPAIGN MODIFIED: $campaign_id</font></B>";

        if ($dial_method == 'RATIO')
            {
            if ($auto_dial_level < 1) {$auto_dial_level = "1.0";}
            $adlSQL = "auto_dial_level='$auto_dial_level',";
            }
        else
            {
            if ($dial_method == 'MANUAL' and $campaign_allow_inbound != 'Y') 
                {
                $auto_dial_level='0';
                $adlSQL = "auto_dial_level='0',";
                }
            else
                {
                $adlSQL = "";
                if ($auto_dial_level < 1) 
                    {
                    $auto_dial_level = "1.0";
                    $adlSQL = "auto_dial_level='$auto_dial_level',";
                    }
                }
            }
        if ( (!OSDpreg_match("/DISABLED/",$list_order_mix)) and ($hopper_level < 100) )
            {$hopper_level='100';}

        $lo_array = array();
        if (!empty($lead_order_direction)) $lo_array[] = $lead_order_direction;
        if (!empty($lead_order_field)) $lo_array[] = $lead_order_field;
        if (!empty($lead_order_nthnew)) $lo_array[] = $lead_order_nthnew;
        $lead_order = implode(' ', $lo_array);
        $stmtA=sprintf("UPDATE osdial_campaigns SET %s campaign_name='%s',active='%s',dial_status_a='%s',dial_status_b='%s',dial_status_c='%s',dial_status_d='%s',"
            ."dial_status_e='%s',lead_order='%s',hopper_level='%s',lead_filter_id='%s',dial_method='%s',adaptive_intensity='%s',campaign_changedate='%s',"
            ."list_order_mix='%s',answers_per_hour_limit='%s' "
            ."WHERE campaign_id='%s';",
            $adlSQL,mres($campaign_name),mres($active),mres($dial_status_a),mres($dial_status_b),mres($dial_status_c),mres($dial_status_d),
            mres($dial_status_e),mres($lead_order),mres($hopper_level),mres($lead_filter_id),mres($dial_method),mres($adaptive_intensity),mres($SQLdate),
            mres($list_order_mix),mres($answers_per_hour_limit),
            mres($campaign_id));
        $rslt=mysql_query($stmtA, $link);

        if ($reset_hopper == 'Y')
            {
            echo "<br>RESETTING CAMPAIGN LEAD HOPPER";
            echo "<br> - Wait 1 minute before dialing next number";
            $stmt=sprintf("DELETE FROM osdial_hopper WHERE campaign_id='%s' AND status IN('READY','QUEUE','DONE');",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);

            ### LOG HOPPER RESET TO LOG FILE ###
            if ($WeBRooTWritablE > 0)
                {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|CAMPAIGN HOPPERRESET|$PHP_AUTH_USER|$ip|$stmt|");
                fclose($fp);
                }
            }

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0)
            {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|MODIFY CAMPAIGN INFO|$PHP_AUTH_USER|$ip|$stmtA|$reset_hopper|");
            fclose($fp);
            }
        }
    $ADD=34;    # go to campaign modification form below
    }
    else
    {
    echo "<font color=red>You do not have permission to view this page</font>";
    }
}
*/


######################
# ADD=51 confirmation before deletion of campaign
######################

if ($ADD==51)
{
     if ( (OSDstrlen($campaign_id) < 2) or ($LOG['delete_campaigns'] < 1) )
        {
         echo "<br><font color=red>CAMPAIGN NOT DELETED - Please go back and look at the data you entered";
         echo "<br>Campaign_id be at least 2 characters in length</font><br>";
        }
     else
        {
        echo "<br><B><font color=$default_text>CAMPAIGN DELETION CONFIRMATION: $campaign_id</B>";
        echo "<br><br><a href=\"$PHP_SELF?ADD=61&campaign_id=$campaign_id&CoNfIrM=YES\">Click here to delete campaign $campaign_id</a></font><br><br><br>";
        }

$ADD='31';        # go to campaign modification below
}

######################
# ADD=52 confirmation before logging all agents out of campaign of campaign
######################

if ($ADD==52)
{
     if (OSDstrlen($campaign_id) < 2)
        {
         echo "<br><font color=red>AGENTS NOT LOGGED OUT OF CAMPAIGN - Please go back and look at the data you entered";
         echo "<br>Campaign_id be at least 2 characters in length</font><br>";
        }
     else
        {
        echo "<br><B><font color=$default_text>AGENT LOGOUT CONFIRMATION: $campaign_id</B>";
        echo "<br><br><a href=\"$PHP_SELF?ADD=62&campaign_id=$campaign_id&CoNfIrM=YES\">Click here to log all agents out of $campaign_id</a></font><br><br><br>";
        }

$ADD='31';        # go to campaign modification below
}

######################
# ADD=53 confirmation before Emergency AUTO CALLS Jam Clear - deletes oldest LIVE osdial_auto_call record
######################

if ($ADD==53)
{
    if (OSDpreg_match('/IN/',$stage))
        {$group_id=$campaign_id;}

     if (OSDstrlen($campaign_id) < 2)
        {
         echo "<br><font color=red>AUTO CALLS NOT CLEARED FOR CAMPAIGN - Please go back and look at the data you entered";
         echo "<br>Campaign_id be at least 2 characters in length</font><br>";
        }
     else
        {
        echo "<br><B><font color=$default_text>AUTO CALLS CLEAR CONFIRMATION: $campaign_id</B>";
        echo "<br><br><a href=\"$PHP_SELF?ADD=63&campaign_id=$campaign_id&CoNfIrM=YES&&stage=$stage\">Click here to delete the oldest LIVE record in AUTO CALLS for $campaign_id</a></font><br><br><br>";
        }

# go to campaign modification below
if (OSDpreg_match('/IN/',$stage))
    {$ADD='3111';}
else
    {$ADD='31';}    
}


######################
# ADD=61 delete campaign record
######################

if ($ADD==61)
{
     if ( ( OSDstrlen($campaign_id) < 2) or ($CoNfIrM != 'YES') or ($LOG['delete_campaigns'] < 1) )
        {
         echo "<br><font color=red>CAMPAIGN NOT DELETED - Please go back and look at the data you entered";
         echo "<br>Campaign_id be at least 2 characters in length</font><br>";
        }
     else
        {
        $stmt=sprintf("DELETE FROM osdial_campaigns WHERE campaign_id='%s' LIMIT 1;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_campaign_agents WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_live_agents WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_campaign_statuses WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_campaign_hotkeys WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_callbacks WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_campaign_stats WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_lead_recycle WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_campaign_server_stats WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_server_trunks WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_pause_codes WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $stmt=sprintf("DELETE FROM osdial_campaigns_list_mix WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        $ccivr = get_first_record($link, 'osdial_ivr', '*', sprintf("campaign_id LIKE '%s'",mres($campaign_id)));
        if (is_array($ccivr)) {
            $stmt=sprintf("DELETE FROM osdial_ivr WHERE id='%s';",mres($ccivr['id']));
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("DELETE FROM osdial_ivr_options WHERE ivr_id='%s';",mres($ccivr['id']));
            $rslt=mysql_query($stmt, $link);

            $stmt=sprintf("DELETE FROM osdial_users WHERE user LIKE 'va%s___';",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
        }

        echo "<br><font color=$default_text>REMOVING LIST HOPPER LEADS FROM OLD CAMPAIGN HOPPER ($campaign_id)</font>";
        $stmt=sprintf("DELETE FROM osdial_hopper WHERE campaign_id='%s' AND status!='API';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0)
            {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!!DELETING CAMPAIGN!|$PHP_AUTH_USER|$ip|campaign_id='$campaign_id'|");
            fclose($fp);
            }
        echo "<br><B><font color=$default_text>CAMPAIGN DELETION COMPLETED: $campaign_id</font></B>";
        echo "<br><br>";
        }

$ADD='10';        # go to campaigns list
}

######################
# ADD=62 Logout all agents from a campaign
######################        

if ($ADD==62)
{
    if ($LOG['modify_campaigns']==1)
    {
     if (OSDstrlen($campaign_id) < 2)
        {
         echo "<br><font color=red>AGENTS NOT LOGGED OUT OF CAMPAIGN - Please go back and look at the data you entered";
         echo "<br>Campaign_id be at least 2 characters in length</font><br>";
        }
     else
        {
        $stmt=sprintf("DELETE FROM osdial_live_agents WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0)
            {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!!AGENT LOGOUT!!!!!!|$PHP_AUTH_USER|$ip|campaign_id='$campaign_id'|");
            fclose($fp);
            }
        echo "<br><B><font color=$default_text>AGENT LOGOUT COMPLETED: $campaign_id</font></B>";
        echo "<br><br>";
        }
    $ADD='31';        # go to campaign modification below
    }
    else
    {
    echo "<font color=red>You do not have permission to view this page</font>";
    }
}


######################
# ADD=63 Emergency AUTO CALLS Jam Clear
######################

if ($ADD==63)
{
    if ($LOG['modify_campaigns']==1)
    {
    if (OSDpreg_match('/IN/',$stage))
        {$group_id=$campaign_id;}

     if (OSDstrlen($campaign_id) < 2)
        {
         echo "<br><font color=red>AUTO CALLS NOT CLEARED FOR CAMPAIGN - Please go back and look at the data you entered";
         echo "<br>Campaign_id be at least 2 characters in length</font><br>";
        }
     else
        {
        $stmt=sprintf("DELETE FROM osdial_auto_calls WHERE status='LIVE' AND campaign_id='%s' ORDER BY call_time LIMIT 1;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0)
            {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|EMERGENCY AUTO CALLS CLEAR|$PHP_AUTH_USER|$ip|campaign_id='$campaign_id'|");
            fclose($fp);
            }
        echo "<br><B><font color=$default_text>LAST AUTO CALLS RECORD CLEARED FOR CAMPAIGN: $campaign_id</font></B>";
        echo "<br><br>";
        }
        # go to campaign modification below
        if (OSDpreg_match('/IN/',$stage)) {$ADD='3111';} else {$ADD='31';}    
    }
    else
    {
    echo "<font color=red>You do not have permission to view this page</font>";
    }
}



######################
# ADD=31 modify campaign info in the system - Detail view
######################

# send to Basic if not allowed

if ( ($LOG['campaign_detail'] < 1) and ($ADD==31) ) {
//    $ADD=34; Basic is gone
	$ADD=30;
}


# send to not allowed screen if not in osdial_user_groups allowed_campaigns list
if ( ($ADD==31) and (!OSDpreg_match('/:' . $campaign_id . ':/',$LOG['allowed_campaignsSTR'])) ) {
    $ADD=30;
}

if ($ADD==31) {
    if ($LOG['modify_campaigns']==1) {
        if ($stage=='show_dialable') {
            $stmt=sprintf("UPDATE osdial_campaigns SET display_dialable_count='Y' WHERE campaign_id='%s';",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
        }
        if ($stage=='hide_dialable') {
            $stmt=sprintf("UPDATE osdial_campaigns SET display_dialable_count='N' WHERE campaign_id='%s';",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
        }

        $stmt=sprintf("SELECT * FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        //$park_ext = $row[0];
        $campaign_name = $row[1];
        //$active = $row[2];
        $dial_status_a = $row[3];
        $dial_status_b = $row[4];
        $dial_status_c = $row[5];
        $dial_status_d = $row[6];
        $dial_status_e = $row[7];
        $lead_order = $row[8];
        //$park_ext = $row[9];
        //$park_file_name = $row[10];
        $web_form_address = $row[11];
        $allow_closers = $row[12];
        $hopper_level = $row[13];
        $auto_dial_level = $row[14];
        $next_agent_call = $row[15];
        $local_call_time = $row[16];
        $voicemail_ext = $row[17];
        $dial_timeout = $row[18];
        $dial_prefix = $row[19];
        $campaign_cid = $row[20];
        $campaign_vdad_exten = $row[21];
        $campaign_rec_exten = $row[22];
        $campaign_recording = $row[23];
        $campaign_rec_filename = $row[24];
        $script_id = $row[25];
        $get_call_launch = $row[26];
        $am_message_exten = OSDpreg_replace('/^8320/','',$row[27]);
        $amd_send_to_vmx = $row[28];
        $xferconf_a_dtmf = $row[29];
        $xferconf_a_number = $row[30];
        $xferconf_b_dtmf = $row[31];
        $xferconf_b_number = $row[32];
        $alt_number_dialing = $row[33];
        $scheduled_callbacks = $row[34];
        $lead_filter_id = $row[35];
        if ($lead_filter_id=='') {$lead_filter_id='NONE';}
        $drop_call_seconds = $row[36];
        $safe_harbor_message = $row[37];
        $safe_harbor_exten = $row[38];
        $display_dialable_count = $row[39];
        $wrapup_seconds = $row[40];
        $wrapup_message = $row[41];
    #    $closer_campaigns = $row[42];
        $use_internal_dnc = $row[43];
        $allcalls_delay = $row[44];
        $omit_phone_code = $row[45];
        $dial_method = $row[46];
        $available_only_ratio_tally = $row[47];
        $adaptive_dropped_percentage = $row[48];
        $adaptive_maximum_level = $row[49];
        $adaptive_latest_server_time = $row[50];
        $adaptive_intensity = $row[51];
        $adaptive_dl_diff_target = $row[52];
        $concurrent_transfers = $row[53];
        $auto_alt_dial = $row[54];
        $auto_alt_dial_statuses = $row[55];
        $agent_pause_codes_active = $row[56];
        $campaign_description = $row[57];
        $campaign_changedate = $row[58];
        $campaign_stats_refresh = $row[59];
        $campaign_logindate = $row[60];
        $dial_statuses = $row[61];
        $disable_alter_custdata = $row[62];
        $no_hopper_leads_logins = $row[63];
        $list_order_mix = $row[64];
        $campaign_allow_inbound = $row[65];
        $manual_dial_list_id = $row[66];
        $default_xfer_group = $row[67];
        //$xfer_groups = $row[68];
        $web_form_address2 = $row[69];
        $allow_tab_switch = $row[70];
        $answers_per_hour_limit = $row[71];
        $campaign_call_time = $row[72];
        $preview_force_dial_time = $row[73];
        $manual_preview_default = $row[74];
        $web_form_extwindow = $row[75];
        $web_form2_extwindow = $row[76];
        $submit_method = $row[77];
        $use_custom2_callerid = $row[78];
        $campaign_lastcall = $row[79];
        $campaign_cid_name = $row[80];
        $xfer_cid_mode = $row[81];
        $use_cid_areacode_map = $row[82];
        $carrier_id = $row[83];
        $email_templates = $row[84];
        $disable_manual_dial = $row[85];
        $hide_xfer_local_closer = $row[86];
        $hide_xfer_dial_override = $row[87];
        $hide_xfer_hangup_xfer = $row[88];
        $hide_xfer_leave_3way = $row[89];
        $hide_xfer_dial_with = $row[90];
        $hide_xfer_hangup_both = $row[91];
        $hide_xfer_blind_xfer = $row[92];
        $hide_xfer_park_dial = $row[93];
        $hide_xfer_blind_vmail = $row[94];
        $allow_md_hopperlist = $row[95];

        if (OSDpreg_match("/DISABLED/",$list_order_mix)) {
            $DEFlistDISABLE = '';
            $DEFstatusDISABLED=0;
        } else {
            $DEFlistDISABLE = 'disabled';
            $DEFstatusDISABLED=1;
        }

        $stmt=sprintf("SELECT count(*) FROM osdial_campaigns_list_mix WHERE campaign_id='%s' AND status='ACTIVE';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $rowx=mysql_fetch_row($rslt);
        if ($rowx[0] < 1) {
            $mixes_list="<option SELECTED value=\"DISABLED\">DISABLED</option>";
            $mixname_list["DISABLED"] = "DISABLED";
        } else {
            ##### get list_mix listings for dynamic pulldown
            $stmt=sprintf("SELECT vcl_id,vcl_name FROM osdial_campaigns_list_mix WHERE campaign_id='%s' AND status='ACTIVE' LIMIT 1;",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
            $mixes_to_print = mysql_num_rows($rslt);
            $mixes_list="<option value=\"DISABLED\">DISABLED</option>";

            $o=0;
            while ($mixes_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $mixes_list .= "<option value=\"ACTIVE\">ACTIVE ($rowx[0] - $rowx[1])</option>";
                $mixname_list["ACTIVE"] = "$rowx[0] - $rowx[1]";
                $o++;
            }
       }

        ##### get status listings for dynamic pulldown
        $stmt=sprintf("SELECT * FROM osdial_statuses ORDER BY status;");
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);
        $statuses_list='';
        $dial_statuses_list='';

        $o=0;
        while ($statuses_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>";
            if ($rowx[0] != 'CBHOLD') {
                $dial_statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>";
            }
            $statname_list["$rowx[0]"] = "$rowx[1]";
            $LRstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>";
            if (OSDpreg_match("/Y/",$rowx[2])) {
                $HKstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>";
            }
            $o++;
        }

        $stmt=sprintf("SELECT * FROM osdial_campaign_statuses WHERE campaign_id='%s' ORDER BY status;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $Cstatuses_to_print = mysql_num_rows($rslt);

        $o=0;
        while ($Cstatuses_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>";
            if ($rowx[0] != 'CBHOLD') {
                $dial_statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>";
            }
            $statname_list["$rowx[0]"] = "$rowx[1]";
            $LRstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>";
            if (OSDpreg_match("/Y/",$rowx[2])) {
                $HKstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>";
            }
            $o++;
        }

        $dial_statuses = OSDpreg_replace("/ -$/","",$dial_statuses);
        $Dstatuses = explode(" ", $dial_statuses);
        $Ds_to_print = (count($Dstatuses) -1);

        ##### get in-groups listings for dynamic pulldown list menu
        $stmt="SELECT group_id,group_name FROM osdial_inbound_groups $xfer_groupsSQL ORDER BY group_id;";
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
            $Xgroups_menu .= "value=\"$rowx[0]\">" . mclabel($rowx[0]) . " - $rowx[1]</option>";
            $o++;
        }
        if ($Xgroups_selected < 1) {
            $Xgroups_menu .= "<option SELECTED value=\"---NONE---\">---NONE---</option>";
        } else {
            $Xgroups_menu .= "<option value=\"---NONE---\">---NONE---</option>";
        }

        echo "<center>";

		if ($SUB < 1) {
        echo "<br /><font class=top_header color=$default_text size=+1>MODIFY CAMPAIGN</font></p><br />";
        echo "<form action=$PHP_SELF method=POST>";
        echo "<input type=hidden name=DB value=$DB>";
        echo "<input type=hidden name=ADD value=41>";
        echo "<input type=hidden name=campaign_id value=\"$campaign_id\">";
        echo "<input type=hidden name=park_ext value=\"$row[9]\">\n";
        echo "<input type=hidden name=park_file value=\"$row[10]\">\n";        
        
        
        
        
        
        
        // BASIC CONTROL
        echo "<a name=basic></a>";
        echo "<table width=$section_width cellspacing=2 frame=border class=rounded-corners>";
        echo "<tr><td class=top_header_sect colspan=4 align=left>Basic Control</td></tr>";
		echo "<tr>";
        echo "	<td colspan=2 align=right width=50%>ID: </td>";
        echo "	<td colspan=2 align=left width=50% style=color:#005><b>" . mclabel($row[0]) . "</b>$NWB#osdial_campaigns-campaign_id$NWE</td>";
        echo "</tr>";
        echo "<tr>";
        echo "	<td colspan=2 align=right width=50%>Active: </td>";
        echo "  <td colspan=2 align=left width=50%><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$row[2]</option></select>$NWB#osdial_campaigns-active$NWE</td>";
        echo "</tr>";
        echo "<tr><td colspan=4 style=font-size:1px>&nbsp;</td></tr>";
        echo "<tr>";
        echo "	<td colspan=2 align=left width=50%>&nbsp;Name:<input type=text name=campaign_name size=40 maxlength=40 value=\"$campaign_name\">$NWB#osdial_campaigns-campaign_name$NWE</td>";
        echo "	<td colspan=2 align=left width=50%>&nbsp;Description:<input type=text name=campaign_description size=36 maxlength=255 value=\"$campaign_description\">$NWB#osdial_campaigns-campaign_description$NWE</td>";
        echo "</tr>";
        echo "<tr>";
        echo "	<td colspan=2 align=left valign=bottom title=\"$campaign_changedate\" style=font-size:14px;>&nbsp;&nbsp;Change Date: ". dateToLocal($link,'first',$campaign_changedate,$webClientAdjGMT,'',$webClientDST,1) . "&nbsp;$NWB#osdial_campaigns-campaign_changedate$NWE</td>";
        echo "	<td colspan=2 align=left valign=bottom title=\"$campaign_logindate\" Xstyle=font-size:14px;>&nbsp;Login Date: ". dateToLocal($link,'first',$campaign_logindate,$webClientAdjGMT,'',$webClientDST,1)."&nbsp;$NWB#osdial_campaigns-campaign_logindate$NWE</td>";
        echo "</tr>";
        echo "<tr>";
        echo "	<td align=right width=25%>Allow No-Leads Logins: </td>";
        echo "	<td align=left width=25%><select size=1 name=no_hopper_leads_logins><option>Y</option><option>N</option><option SELECTED>$no_hopper_leads_logins</option></select>$NWB#osdial_campaigns-no_hopper_leads_logins$NWE</td>";
        echo "	<td colspan=2 align=left>&nbsp;Allow Inbound & Blended: <select size=1 name=campaign_allow_inbound><option>Y</option><option>N</option><option SELECTED>$campaign_allow_inbound</option></select>$NWB#osdial_campaigns-campaign_allow_inbound$NWE</td>";
        echo "</tr>";
        echo "<tr>";
        echo "	<td align=right width=25%>Disable Alter Data: </td>";
        echo "	<td colspan=1 align=left><select size=1 name=disable_alter_custdata><option>Y</option><option>N</option><option SELECTED>$disable_alter_custdata</option></select>$NWB#osdial_campaigns-disable_alter_custdata$NWE</td>";
        echo "<td colspan=2 align=left>&nbsp;Answers Per Hour Limit: <input type=text name=answers_per_hour_limit size=10 maxlength=10 value=\"$answers_per_hour_limit\">$NWB#osdial_campaigns-answers_per_hour_limit$NWE</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td align=right class=no-ul colspan=4><br />";
        jump_section(1);
        echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        
        
        // DIALING
        echo "<a name=status></a>";
        echo "<table width=96% $section_width cellspacing=3 frame=border class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top>Dialing</td></tr>";
        
        
        
		// Status Selection
        echo "<tr><td colspan=2 align=center><table width=97% $section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners2>";
        echo "<tr class=tabheader2><td align=left class=top_header_sect valign=top width=40% colspan=2>Status Selection</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        $o=0;
        while ($Ds_to_print > $o) {            
			$o++;
			$Dstatus = $Dstatuses[$o];

			echo "<tr><td align=right>Dial Status $o: </td><td align=left> ";

			if ($DEFstatusDISABLED > 0) {
				echo "<font color=grey><DEL><b>$Dstatus</b> - $statname_list[$Dstatus] &nbsp; &nbsp; &nbsp; &nbsp; <font size=2>";
				echo "REMOVE</DEL></td></tr>";
			} else {
				echo "<b>$Dstatus</b> - $statname_list[$Dstatus] &nbsp; &nbsp; &nbsp; &nbsp; <font size=2>";
				echo "<a href=\"$PHP_SELF?ADD=68&campaign_id=$campaign_id&status=$Dstatuses[$o]\">REMOVE</a></td></tr>";
			}
		}
        echo "<tr>";
        echo "<td align=right>Add A Dial Status: </td>";
        echo "<td align=left><select size=1 style=\"width:300px;\" name=dial_status $DEFlistDISABLE>";
        echo "<option value=\"\"> - NONE - </option>";
        echo "$dial_statuses_list";
        echo "</select> &nbsp; ";
        echo "<input type=submit name=submit value=ADD> &nbsp; &nbsp; $NWB#osdial_campaigns-dial_status$NWE</td>";
        echo "</tr>";
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr class=tabfooter2><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table></td></tr>";
        
        
        
        // DIAL METHOD
        echo "<tr><td colspan=2 align=center>";
        echo "<a name=method></a>";
			
	//         I'm moving the adapt options into the Adapt section, leaving Manual, Ratio and Adaptive as the primary choices. 
	
			if ((OSDpreg_match('/^ADAPT/',$dial_method))) {
				$dial_method_a_class=='active-mode';
				$dial_method_m_class=='';
				$dial_method_r_class=='';
				$test='A';
			}
			if ($dial_method=='MANUAL') {
				$dial_method_a_class=='';
				$dial_method_m_class=='active-mode';
				$dial_method_r_class=='';
				$test='M';
			}
			if ($dial_method=='RATIO') {
				$dial_method_a_class=='';
				$dial_method_m_class=='';
				$dial_method_r_class=='active-mode';
				$test='R';
        }
		
			echo "<table width=$section_width cellspacing=3 frame=border  cellpadding=0 cellspacing=0 class=rounded-corners2>";
			echo "<tr><td align=left class=top_header_sect valign=top colspan=2>Dial Method ($test)</td></tr>";
			
			echo "<tr><td colspan=2><br /><center><table frame=0 border=0 cellpadding=1 cellspacing=1 width=250><tr>";
			echo "<td align=center bgcolor=$dial_method_a_class><button class=$dial_method_a_class name=dial_method value=ADAPT>Adaptive</button></td>";
			echo "<td align=center bgcolor=$dial_method_m_class><button class=$dial_method_m_class name=dial_method value=MANUAL>Manual</button></td>";
			echo "<td align=center bgcolor=$dial_method_r_class><button class=$dial_method_r_class name=dial_method value=RATIO>Ratio</button></td>";
			
			echo "</tr></table></center>";
			echo "<table width=100%><tr><td align=right><input style='color:#1C4754' type=submit name=SUBMIT value='Change Mode'></td></tr></table>";
			echo "</td></tr>";
			
// 			echo "<tr><td align=right width=40%>Dial Method: </td><td align=left width=60%>
// 					<select size=1 name=dial_method>
// 					  <option>MANUAL</option>
// 					  <option>RATIO</option>
// 					  <option>ADAPTIVE</option>
// 					  <option selected>$dial_method</option>
// 					</select>$NWB#osdial_campaigns-dial_method$NWE</td></tr>";
$dial_method="RATIO";
// 			echo "<tr><td align=right class=no-ul colspan=2>";
			
			
        // table includes three dial method section
        
        
			$manual_visible='visibility:collapse;';
			$ratio_visible='visibility:collapse;';
			$adapt_visible='visibility:collapse;';
			if ($dial_method=="MANUAL") {
				$manual_visible='';
			} elseif ($dial_method=="RATIO") {
				$ratio_visible='';
			} elseif (OSDpreg_match('/^ADAPT/',$dial_method)) {
				$adapt_visible='';
			}

        $section_width10=($section_width*.99);
		

				// MANUAL DIAL
			echo "<tr style=\"$manual_visible\"><td colspan=2 align=center><table width=$section_width10 cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners3>";
			echo "<tr><td align=left class=top_header_sect valign=top width=40%>Manual Dial Options</td></tr>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td align=right>Preview Force Dial Time: </td><td align=left><input type=text name=preview_force_dial_time size=3 maxlength=3 value=\"$preview_force_dial_time\"> <i>in seconds, 0 disables</i>$NWB#osdial_campaigns-preview_force_dial_time$NWE</td></tr>";
			echo "<tr><td align=right>Manual Preview Default: </td><td align=left><select size=1 name=manual_preview_default><option>Y</option><option>N</option><option selected>$manual_preview_default</option></select>$NWB#osdial_campaigns-manual_preview_default$NWE</td></tr>";
			echo "<tr>";
			echo "  <td align=right>Allow ManualDial HopperList:</td>";
			echo "  <td align=left>";
			echo select_yesno('allow_md_hopperlist',$allow_md_hopperlist);
			echo "    </select>";
			echo "    $NWB#osdial_campaigns-allow_md_hopperlist$NWE";
			echo "  </td>";
			echo "</tr>";
			echo "<tr><td align=right>Disable Manual Dial: </td><td align=left><select size=1 name=disable_manual_dial><option>Y</option><option>N</option><option SELECTED>$disable_manual_dial</option></select>$NWB#osdial_campaigns-disable_manual_dial$NWE</td></tr>";
			//echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr><td align=right class=no-ul colspan=2><br />";
				jump_section(1);
				echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
			echo "</table></td></tr>";
        
        
        // RATIO DIAL
			echo "<tr style=\"$ratio_visible\"><td colspan=2 align=center><table width=$section_width10 cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners3>";
			echo "<tr><td align=left class=top_header_sect valign=top width=40%>Ratio Dial Options</td></tr>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td align=right>Auto Dial Level: </td><td align=left nowrap><input type=text name=auto_dial_level size=6 maxlength=6 value=\"$auto_dial_level\" selectBoxOptions=\"0;1;1.1;1.2;1.3;1.4;1.5;1.6;1.7;1.8;1.9;2.0;2.2;2.5;3.0;4.0;4.5;5.0\"> $NWB#osdial_campaigns-auto_dial_level$NWE</td></tr>";

			echo "<tr><td align=right>Available Only Tally: </td><td align=left><select size=1 name=available_only_ratio_tally><option >Y</option><option>N</option><option SELECTED>$available_only_ratio_tally</option></select>$NWB#osdial_campaigns-available_only_ratio_tally$NWE</td></tr>";

			//echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr><td align=right class=no-ul colspan=2><br />";
				jump_section(1);
				echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
			echo "</table></td></tr>";
        
        
        // ADAPTIVE DIAL
			echo "<tr style=\"$adapt_visible\"><td colspan=2 align=center><table width=$section_width10 cellspacing=3 frame=border cellpadding=1 cellspacing=0 class=rounded-corners3>";
			echo "<tr><td align=left class=top_header_sect valign=top width=40%>Adaptive Dial Options</td></tr>";
			echo "<tr><td>&nbsp;</td></tr>";
			$adapt_dial_method=$dial_method;
			echo "<tr><td align=right width=40%>Adapt Method: </td><td align=left width=60%>";
			echo "<select size=1 name=dial_method>
					<option>ADAPT_AVERAGE</option>
					<option>ADAPT_TAPERED</option>
					<option>ADAPT_HARD_LIMIT</option>
					<option SELECTED>$adapt_dial_method</option>
				</select>$NWB#osdial_campaigns-dial_method$NWE</td></tr>";
				if ($adapt_dial_method ==  "ADAPT_HARD_LIMIT" or $adapt_dial_method ==  "ADAPT_TAPERED") {
					echo "<tr><td colspan=2 align=center><font color=red>We recommend against ADAPT_HARD_LIMIT and ADAPT_TAPERED</font></td></tr>";
					echo "<tr><td colspan=2 align=center><font color=red>due to weak dialing logic in these two modes.</font></td></tr>";
				}
			
			if ($adapt_dial_method=='ADAPT_AVERAGE' or $adapt_dial_method=='ADAPT_HARD_LIMIT' or $adapt_dial_method=='ADAPT_TAPERED') {
				$dial_method=$adapt_dial_method;
		} else {
				$dial_method='ADAPT_AVERAGE';
			}
		
			
			echo "<tr><td align=right>Drop Percentage Limit: </td><td align=left><select size=1 name=adaptive_dropped_percentage>";
			$n=100;
			while ($n>=1) {
				$sel='';
				if ($n==$adaptive_dropped_percentage) $sel='selected';
				echo "<option value=\"$n\" $sel>$n %</option>";
				$n--;
			}
			echo "</select>$NWB#osdial_campaigns-adaptive_dropped_percentage$NWE</td></tr>";
			
			echo "<tr Xbgcolor=$unusualrows><td align=right>Maximum Adapt Dial Level: </td><td align=left nowrap><input type=text name=adaptive_maximum_level size=6 maxlength=6 value=\"$adaptive_maximum_level\" selectBoxOptions=\"0;1;1.1;1.2;1.3;1.4;1.5;1.6;1.7;1.8;1.9;2.0;2.2;2.5;3.0;4.0;4.5;5.0\"> $NWB#osdial_campaigns-adaptive_maximum_level$NWE</td></tr>";
			echo "<tr Xbgcolor=$unusualrows><td align=right>Auto Dial Level: </td><td align=left nowrap><input type=text name=ADAPT_auto_dial_level size=6 maxlength=6 value=\"$auto_dial_level\" selectBoxOptions=\"0;1;1.1;1.2;1.3;1.4;1.5;1.6;1.7;1.8;1.9;2.0;2.2;2.5;3.0;4.0;4.5;5.0\"> $NWB#osdial_campaigns-auto_dial_level$NWE &nbsp; &nbsp; &nbsp; <input type=checkbox name=dial_level_override value=\"1\">Activate Override</td></tr>";
			echo "<tr Xbgcolor=$unusualrows><td align=right>Available Only Tally: </td><td align=left><select size=1 name=ADAPT_available_only_ratio_tally><option >Y</option><option>N</option><option SELECTED>$available_only_ratio_tally</option></select>$NWB#osdial_campaigns-available_only_ratio_tally$NWE</td></tr>";
			echo "<tr Xbgcolor=$unusualrows><td align=right>Adapt Intensity Modifier: </td><td align=left><select size=1 name=adaptive_intensity>";
			$n=40;
			while ($n>=-40) {
				$sel='';
				$dtl = 'Balanced';
				if ($n<0) $dtl = 'Less Intense';
				if ($n>0) $dtl = 'More Intense';
				if ($n == $adaptive_intensity) $sel='selected';
				echo "<option value=\"$n\" $sel>$n - $dtl</option>";
				$n--;
			}
			echo "</select> $NWB#osdial_campaigns-adaptive_intensity$NWE</td></tr>";

			echo "<tr Xbgcolor=$unusualrows><td align=right>Dial Level Difference Target: </td><td align=left><select size=1 name=adaptive_dl_diff_target>";
			$n=40;
			while ($n>=-40) {
				$sel='';
				$nabs = abs($n);
				$dtl = 'Balanced';
				if ($n<0) $dtl = 'Agents Waiting for Calls';
				if ($n>0) $dtl = 'Calls Waiting for Agents';
				if ($n == $adaptive_dl_diff_target) $sel='selected';
				echo "<option value=\"$n\" $sel>$n --- $nabs $dtl</option>";
				$n--;
			}
			echo "</select> $NWB#osdial_campaigns-adaptive_dl_diff_target$NWE</td></tr>";
			echo "<tr Xbgcolor=$unusualrows><td align=right>Latest Server Time for Tapered Mode: </td><td align=left><input type=text name=adaptive_latest_server_time size=6 maxlength=4 value=\"$adaptive_latest_server_time\"><i>4 digits only</i> $NWB#osdial_campaigns-adaptive_latest_server_time$NWE</td></tr>";
			echo "<tr Xbgcolor=$unusualrows><td align=right>Auto Dial Answer Handling: </td>";
			echo "  <td align=left>";
			echo "    <select size=1 name=campaign_vdad_exten>";
			echo "      <option value=\"8365\" $sel1>8365 - Home Server Only</option>";
			echo "      <option value=\"8367\" $sel2>8367 - Load Sharing</option>";
			echo "      <option value=\"8368\" $sel3>8368 - Load Balancing</option>";
			echo "      <option value=\"8369\" $sel4>8369 - Answering Machine Detection, Load Balancing</option>";
			echo "    </select>";
			echo "    $NWB#osdial_campaigns-campaign_vdad_exten$NWE";
			echo "  </td></tr>";
				echo "<tr><td align=right class=no-ul colspan=2><br />";
				jump_section(1);
				echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
			echo "</table></td></tr>";
		

        	echo "</table></td></tr>";
        
        
		// DIALING OPTIONS
        $section_width10=($section_width*.99);
        echo "<tr><td colspan=2 align=center>";
        echo "<a name=options></a>";
        echo "<table width=$section_width10 cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners2>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>Dialing Options</td></tr>";
        $sel1='';
        $sel2='';
        $sel3='';
        $sel4='';
        if ($campaign_vdad_exten=='8365') {
            $sel1='selected';
        } elseif ($campaign_vdad_exten=='8367') {
            $sel2='selected';
        } elseif ($campaign_vdad_exten=='8369') {
            $sel4='selected';
        } else {
            $sel3='selected';
        }
        echo "<tr style=\"visibility:collapse;\" Xbgcolor=$unusualrows><td align=right>Concurrent Transfers: </td><td align=left><select size=1 name=concurrent_transfers><option >AUTO</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10<option SELECTED>$concurrent_transfers</option></select>$NWB#osdial_campaigns-concurrent_transfers$NWE</td></tr>";

        echo "<tr><td align=right>Alt Number Dialing: </td><td align=left><select size=1 name=alt_number_dialing><option>Y</option><option>N</option><option SELECTED>$alt_number_dialing</option></select>$NWB#osdial_campaigns-alt_number_dialing$NWE</td></tr>";

        echo "<tr><td align=right>Auto Alt-Number Dialing: </td><td align=left><select size=1 name=auto_alt_dial><option >NONE</option><option>ALT_ONLY</option><option>ADDR3_ONLY</option><option>ALT_AND_ADDR3</option><option>ALT_ADDR3_AND_AFFAP</option><option SELECTED>$auto_alt_dial</option></select>$NWB#osdial_campaigns-auto_alt_dial$NWE</td></tr>";

        echo "<tr><td align=right>Next Agent Call: </td><td align=left><select size=1 name=next_agent_call><option >random</option><option>oldest_call_start</option><option>oldest_call_finish</option><option>overall_user_level</option><option>campaign_rank</option><option>fewest_calls</option><option SELECTED>$next_agent_call</option></select>$NWB#osdial_campaigns-next_agent_call$NWE</td></tr>";

        echo "<tr><td align=right>";
        if ($LOG['multicomp_user'] > 0) {
            echo "<a href=\"$PHP_SELF?ADD=311111111&call_time_id=$campaign_call_time\">Operation Time: </a>";
        } else {
            echo "Operation Time: ";
        }
        echo "</td><td align=left><select size=1 name=campaign_call_time>";
        echo get_calltimes($link, $campaign_call_time);
        echo "</select>$NWB#osdial_campaigns-campaign_call_time$NWE</td></tr>";
        echo "<tr><td align=right>";
        if ($LOG['multicomp_user'] > 0) {
            echo "<a href=\"$PHP_SELF?ADD=311111111&call_time_id=$local_call_time\">Local Timezone Call Time: </a>";
        } else {
            echo "Local Timezone Call Time: ";
        }
        echo "</td><td align=left><select size=1 name=local_call_time>";
        echo get_calltimes($link, $local_call_time);
        echo "</select>$NWB#osdial_campaigns-local_call_time$NWE</td></tr>";

        echo "<tr><td align=right>Dial Timeout: </td><td align=left><input type=text name=dial_timeout size=3 maxlength=3 value=\"$dial_timeout\"> <i>in seconds</i>$NWB#osdial_campaigns-dial_timeout$NWE</td></tr>";

        echo "<tr><td align=right>Drop Call Seconds: </td><td align=left><input type=text name=drop_call_seconds size=5 maxlength=2 value=\"$drop_call_seconds\">$NWB#osdial_campaigns-drop_call_seconds$NWE</td></tr>";
			echo "<tr><td></td></tr>";
        echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // LIST HANDLING OPTIONS
        echo "<a name=list></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>List Handling Options</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr>";
        echo "  <td align=right>List Order: </td>";
        echo "  <td align=left>";
        $usel='';
        $dsel='';
        if (OSDpreg_match('/^UP/',$lead_order)) {
            $usel='selected';
        } else {
            $dsel='selected';
        }
        echo "    <select size=1 name=lead_order_direction><option $dsel>DOWN</option><option $usel>UP</option></select>";
        $tpsel='';
        $tlsel='';
        $tcsel='';
        $trsel='';
        $tisel='';
        $psel='';
        $lsel='';
        $csel='';
        $rsel='';
        $isel='';
        if (OSDpreg_match('/TZ PHONE/',$lead_order)) {
            $tpsel='selected';
        } else if (OSDpreg_match('/TZ LAST NAME/',$lead_order)) {
            $tlsel='selected';
        } else if (OSDpreg_match('/TZ COUNT/',$lead_order)) {
            $tcsel='selected';
        } else if (OSDpreg_match('/TZ RANDOM/',$lead_order)) {
            $trsel='selected';
        } else if (OSDpreg_match('/TZ/',$lead_order)) {
            $tisel='selected';
        } else if (OSDpreg_match('/PHONE/',$lead_order)) {
            $psel='selected';
        } else if (OSDpreg_match('/LAST NAME/',$lead_order)) {
            $lsel='selected';
        } else if (OSDpreg_match('/COUNT/',$lead_order)) {
            $csel='selected';
        } else if (OSDpreg_match('/RANDOM/',$lead_order)) {
            $rsel='selected';
        } else {
            $isel='selected';
        }
        echo "    <select size=1 name=lead_order_field><option value=\"\" $isel>ID</option><option $psel>PHONE</option><option $lsel>LAST NAME</option><option value=\"COUNT\" $csel>CALL COUNT</option><option $rsel>RANDOM</option><option value=\"TZ\" $tisel>TIMEZONE / ID</option><option value=\"TZ PHONE\" $tpsel>TIMEZONE / PHONE</option><option value=\"TZ LAST NAME\" $tlsel>TIMEZONE / LAST NAME</option><option value=\"TZ COUNT\" $tcsel>TIMEZONE / CALL COUNT</option><option value=\"TZ RANDOM\" $trsel>TIMEZONE / RANDOM</option></select>";
        $seln='';
        $sel2='';
        $sel3='';
        $sel4='';
        $sel5='';
        $sel6='';
        if (OSDpreg_match('/2nd NEW$/',$lead_order)) {
            $sel2='selected';
        } else if (OSDpreg_match('/3rd NEW$/',$lead_order)) {
            $sel3='selected';
        } else if (OSDpreg_match('/4th NEW$/',$lead_order)) {
            $sel4='selected';
        } else if (OSDpreg_match('/5th NEW$/',$lead_order)) {
            $sel5='selected';
        } else if (OSDpreg_match('/6th NEW$/',$lead_order)) {
            $sel6='selected';
        } else {
            $seln='selected';
        }
        echo "    <select size=1 name=lead_order_nthnew><option value=\"\" $seln>-------</option><option $sel2>2nd NEW</option><option $sel3>3rd NEW</option><option $sel4>4th NEW</option><option $sel5>5th NEW</option><option $sel6>6th NEW</option></select>";
        echo "    $NWB#osdial_campaigns-lead_order$NWE</td></tr>";
        #echo "<tr bgcolor=$oddrows><td align=right>List Order: </td><td align=left><select size=1 name=lead_order ><option>DOWN</option><option>UP</option><option>DOWN PHONE</option><option>UP PHONE</option><option>DOWN LAST NAME</option><option>UP LAST NAME</option><option>DOWN COUNT</option><option>UP COUNT</option><option>DOWN 2nd NEW</option><option>DOWN 3rd NEW</option><option>DOWN 4th NEW</option><option>DOWN 5th NEW</option><option>DOWN 6th NEW</option><option>UP 2nd NEW</option><option>UP 3rd NEW</option><option>UP 4th NEW</option><option>UP 5th NEW</option><option>UP 6th NEW</option><option>DOWN PHONE 2nd NEW</option><option>DOWN PHONE 3rd NEW</option><option>DOWN PHONE 4th NEW</option><option>DOWN PHONE 5th NEW</option><option>DOWN PHONE 6th NEW</option><option>UP PHONE 2nd NEW</option><option>UP PHONE 3rd NEW</option><option>UP PHONE 4th NEW</option><option>UP PHONE 5th NEW</option><option>UP PHONE 6th NEW</option><option>DOWN LAST NAME 2nd NEW</option><option>DOWN LAST NAME 3rd NEW</option><option>DOWN LAST NAME 4th NEW</option><option>DOWN LAST NAME 5th NEW</option><option>DOWN LAST NAME 6th NEW</option><option>UP LAST NAME 2nd NEW</option><option>UP LAST NAME 3rd NEW</option><option>UP LAST NAME 4th NEW</option><option>UP LAST NAME 5th NEW</option><option>UP LAST NAME 6th NEW</option><option>DOWN COUNT 2nd NEW</option><option>DOWN COUNT 3rd NEW</option><option>DOWN COUNT 4th NEW</option><option>DOWN COUNT 5th NEW</option><option>DOWN COUNT 6th NEW</option><option>UP COUNT 2nd NEW</option><option>UP COUNT 3rd NEW</option><option>UP COUNT 4th NEW</option><option>UP COUNT 5th NEW</option><option>UP COUNT 6th NEW</option><option>RANDOM</option><option SELECTED>$lead_order</option></select>$NWB#osdial_campaigns-lead_order$NWE</td></tr>";
        #echo "<tr bgcolor=$oddrows><td align=right>List Order: </td><td align=left><select size=1 name=lead_order ><option>DOWN</option><option>UP</option><option>UP PHONE</option><option>DOWN PHONE</option><option>UP LAST NAME</option><option>DOWN LAST NAME</option><option>UP COUNT</option><option>DOWN COUNT</option><option>DOWN COUNT 2nd NEW</option><option>DOWN COUNT 3rd NEW</option><option>DOWN COUNT 4th NEW</option><option>DOWN COUNT 5th NEW</option><option>DOWN COUNT 6th NEW</option><option SELECTED>$lead_order</option></select>$NWB#osdial_campaigns-lead_order$NWE</td></tr>";

        echo "<tr><td align=right><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaign_id&vcl_id=$list_order_mix\">List Mix</a>: </td><td align=left><select size=1 name=list_order_mix>";
        echo "$mixes_list";
        if (OSDpreg_match("/DISABLED/",$list_order_mix))
            {echo "<option selected value=\"$list_order_mix\">$list_order_mix - $mixname_list[$list_order_mix]</option>";}
        else
            {echo "<option selected value=\"ACTIVE\">ACTIVE ($mixname_list[ACTIVE])</option>";}
        echo "</select>$NWB#osdial_campaigns-list_order_mix$NWE</td></tr>";

        echo "<tr><td align=right><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$lead_filter_id\">Lead Filter</a>: </td><td align=left><select size=1 name=lead_filter_id>";
        echo get_filters($link, $lead_filter_id);
        #echo "$filters_list";
        #echo "<option selected value=\"$lead_filter_id\">$lead_filter_id - $filtername_list[$lead_filter_id]</option>";
        echo "</select>$NWB#osdial_campaigns-lead_filter_id$NWE</td></tr>";

        echo "<tr><td align=right>Hopper Level: </td><td align=left><select size=1 name=hopper_level><option>1</option><option>5</option><option>10</option><option>20</option><option>50</option><option>100</option><option>200</option><option>500</option><option>700</option><option>1000</option><option>2000</option><option SELECTED>$hopper_level</option></select>$NWB#osdial_campaigns-hopper_level$NWE</td></tr>";

        echo "<tr><td align=right>Force Reset of Hopper: </td><td align=left><select size=1 name=reset_hopper><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_campaigns-force_reset_hopper$NWE</td></tr>";
        echo "<tr><td align=right>Add Manual Dialed Calls to List: </td><td align=left>";
        echo list_id_text_options($link, 'manual_dial_list_id', $manual_dial_list_id, 20, 20);
        #echo "  <select name=manual_dial_list_id size=1>";
        #$sel = '';
        #$krh = get_krh($link, 'osdial_lists', 'list_id,list_name','',sprintf("campaign_id LIKE '%s__%%'",$LOG['company_prefix']),'');
        #echo format_select_options($krh, 'list_id', 'list_name', $manual_dial_list_id, '', false);
        #if (OSDpreg_match('/|^$|^0$/',$manual_dial_list_id)) $sel='';
        #echo "<option value='0'>- NO LIST SELECTED -</option>";
        #echo " $manual_dial_list_id\">";
        echo "$NWB#osdial_campaigns-manual_dial_list_id$NWE</td></tr>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
	


		// CARRIER OPTIONS
		echo "<a name=carrier></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>Carrier Options</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "        <tr>";
        echo "          <td align=right>Carrier:</td>";
        echo "          <td align=left>";
        echo "            <select name=carrier_id>";
        $krh = get_krh($link, 'osdial_carriers', '*','',"active='Y' AND selectable='Y'",'');
        $carrier_label = "** USE MANUAL CONFIGURATION **";
        if ($config['settings']['default_carrier_id'] > 0) {
            $carrier_label = "** USE SYSTEM DEFAULT **";
        }
        echo format_select_options($krh, 'id', 'name', $carrier_id, $carrier_label,'');
        echo "            </select>";
        echo "          </td>";
        echo "        </tr>";
        echo "<tr><td align=right>Dial Prefix: </td><td align=left><input type=text name=dial_prefix size=20 maxlength=20 value=\"$dial_prefix\"> <font size=1>use 9 for TRUNK1, use 8 for TRUNK2</font>$NWB#osdial_campaigns-dial_prefix$NWE</td></tr>";
        echo "<tr><td align=right>Omit Phone Code: </td><td align=left><select size=1 name=omit_phone_code><option>Y</option><option>N</option><option SELECTED>$omit_phone_code</option></select>$NWB#osdial_campaigns-omit_phone_code$NWE</td></tr>";
        echo "<tr><td align=right>CallerID Name: </td><td align=left><input type=text name=campaign_cid_name size=20 maxlength=40 value=\"$campaign_cid_name\">$NWB#osdial_campaigns-campaign_cid_name$NWE</td></tr>";
        echo "<tr><td align=right>CallerID: </td><td align=left><input type=text name=campaign_cid size=20 maxlength=20 value=\"$campaign_cid\">$NWB#osdial_campaigns-campaign_cid$NWE</td></tr>";
        echo "<tr><td align=right>Use Custom2 CallerID: </td><td align=left><select name=use_custom2_callerid><option>N</option><option>Y</option><option selected>$use_custom2_callerid</option></select>$NWB#osdial_campaigns-use_custom2_callerid$NWE</td></tr>";
        echo "<tr><td align=right><a href=\"$PHP_SELF?ADD=3ca&SUB=2&campaign_id=$campaign_id\">Use CallerID Areacode Map:</a> </td><td align=left><select name=use_cid_areacode_map><option>N</option><option>Y</option><option selected>$use_cid_areacode_map</option></select>$NWB#osdial_campaigns-use_cid_areacode_map$NWE</td></tr>";
        echo "<tr><td align=right>3rd-Party CID Mode: </td><td align=left><select name=xfer_cid_mode><option>CAMPAIGN</option><option>PHONE</option><option>LEAD</option><option>LEAD_CUSTOM1</option><option>LEAD_CUSTOM2</option><option selected>$xfer_cid_mode</option></select>$NWB#osdial_campaigns-xfer_cid_mode$NWE</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // RECORDING OPTIONS
        echo "<a name=record></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>Recording Options</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right>Recording: </td><td align=left><select size=1 name=campaign_recording><option>NEVER</option><option>ONDEMAND</option><option>ALLCALLS</option><option>ALLFORCE</option><option SELECTED>$campaign_recording</option></select>$NWB#osdial_campaigns-campaign_recording$NWE</td></tr>";
        echo "<tr style=\"visibility:collapse;\"><td align=right>Rec Exten: </td><td align=left><input type=text name=campaign_rec_exten size=10 maxlength=10 value=\"$campaign_rec_exten\">$NWB#osdial_campaigns-campaign_rec_exten$NWE</td></tr>";
        echo "<tr><td align=right>Rec Filename: </td><td align=left><input type=text name=campaign_rec_filename size=50 maxlength=50 value=\"$campaign_rec_filename\">$NWB#osdial_campaigns-campaign_rec_filename$NWE</td></tr>";
		echo "<tr><td align=right>Recording Delay: </td><td align=left><input type=text name=allcalls_delay size=3 maxlength=3 value=\"$allcalls_delay\"> <i>in seconds</i>$NWB#osdial_campaigns-allcalls_delay$NWE</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // ANSWERING MACHINE OPTIONS
        echo "<a name=am></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>Answering Machine Options</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right>Answering Message Extension: </td><td align=left>";
        #echo "<input type=text name=am_message_exten size=10 maxlength=20 value=\"$am_message_exten\">";
        echo extension_text_options($link, 'am_message_exten', $am_message_exten, 10, 20);
        echo "$NWB#osdial_campaigns-am_message_exten$NWE</td></tr>";
        echo "<tr><td align=right>Send AMD to AM Extension: </td><td align=left><select size=1 name=amd_send_to_vmx><option>Y</option><option>N</option><option>CUSTOM1</option><option>CUSTOM2</option><option SELECTED>$amd_send_to_vmx</option></select>$NWB#osdial_campaigns-amd_send_to_vmx$NWE</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // DROPPED CALL OPTIONS
        echo "<a name=drop></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=1 cellspacing=0 class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>Dropped Calls Options</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right>Drop Call Handling (Safe Harbor): </td><td align=left>";
        echo "<select size=1 name=safe_harbor_message>";
        $sel1='';
        $sel2='';
        if ($safe_harbor_message=='Y') {
            $sel1='selected';
        } else {
            $sel2='selected';
        }
        echo "  <option value=\"Y\" $sel1>Message/Extension</option>";
        echo "  <option value=\"N\" $sel2>Voicemail</option>";
        echo "</select>$NWB#osdial_campaigns-safe_harbor_message$NWE</td></tr>";
        echo "<tr><td align=right>Drop Message/Extension: </td><td align=left>";
        #echo "<input type=text name=safe_harbor_exten size=10 maxlength=20 value=\"$safe_harbor_exten\">";
        echo extension_text_options($link, 'safe_harbor_exten', $safe_harbor_exten, 10, 20);
        echo "$NWB#osdial_campaigns-safe_harbor_exten$NWE</td></tr>";
        echo "<tr><td align=right>Drop Voicemail: </td><td align=left>";
        #echo "<input type=text name=voicemail_ext size=10 maxlength=10 value=\"$voicemail_ext\">";
        echo phone_voicemail_text_options($link, 'voicemail_ext', $voicemail_ext, 10, 10);
        echo "$NWB#osdial_campaigns-voicemail_ext$NWE</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // CALL TRANSFER OPTIONS
        echo "<a name=transfer></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners>";
        echo "<tr><td class=top_header_sect colspan=4 align=left valign=top width=40%>Call Transfer Options</td></tr>";
        echo "<tr><td colspan=4>&nbsp;</td></tr>";
        echo "<tr>";
        echo "	<td align=right width=30%>Allow Transfer and Closers: ";
        echo "	</td>";
        echo "	<td align=left width=20%>";
        echo "	  <select size=1 name=allow_closers><option>Y</option><option>N</option><option SELECTED>$allow_closers</option></select>$NWB#osdial_campaigns-allow_closers$NWE";
        echo "</td>";
        echo "  <td align=right width=30%>Local Closer - XFER Hide:</td>";
        echo "  <td align=left width=20%>";
        echo select_yesno('hide_xfer_local_closer',$hide_xfer_local_closer);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_local_closer$NWE";
        echo "  </td>";
        echo "</tr>";
        echo "<tr>";
        echo "  <td align=right>Dial Override - XFER Hide:</td>";
        echo "  <td align=left>";
        echo select_yesno('hide_xfer_dial_override',$hide_xfer_dial_override);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_dial_override$NWE";
        echo "  </td>";
        echo "  <td align=right>Hangup Xfer Line - XFER Hide:</td>";
        echo "  <td align=left>";
        echo select_yesno('hide_xfer_hangup_xfer',$hide_xfer_hangup_xfer);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_hangup_xfer$NWE";
        echo "  </td>";
        echo "</tr>";
        echo "<tr>";
        echo "  <td align=right>Leave 3Way Call - XFER Hide:</td>";
        echo "  <td align=left>";
        echo select_yesno('hide_xfer_leave_3way',$hide_xfer_leave_3way);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_leave_3way$NWE";
        echo "  </td>";
        echo "  <td align=right>Dial With Customer - XFER Hide:</td>";
        echo "  <td align=left>";
        echo select_yesno('hide_xfer_dial_with',$hide_xfer_dial_with);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_dial_with$NWE";
        echo "  </td>";
        echo "</tr>";
        echo "<tr>";
        echo "  <td align=right>Hangup Both Lines - XFER Hide:</td>";
        echo "  <td align=left>";
        echo select_yesno('hide_xfer_hangup_both',$hide_xfer_hangup_both);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_hangup_both$NWE";
        echo "  </td>";
        echo "  <td align=right>Blind Transfer - XFER Hide:</td>";
        echo "  <td align=left>";
        echo select_yesno('hide_xfer_blind_xfer',$hide_xfer_blind_xfer);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_blind_xfer$NWE";
        echo "  </td>";
        echo "</tr>";
        echo "<tr>";
        echo "  <td align=right>Park Customer Dial - XFER Hide:</td>";
        echo "  <td align=left>";
        echo select_yesno('hide_xfer_park_dial',$hide_xfer_park_dial);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_park_dial$NWE";
        echo "  </td>";
        echo "  <td align=right>Blind VMail - XFER Hide:</td>";
        echo "  <td align=left>";
        echo select_yesno('hide_xfer_blind_vmail',$hide_xfer_blind_vmail);
        echo "    </select>";
        echo "    $NWB#osdial_campaigns-hide_xfer_blind_vmail$NWE";
        echo "  </td>";
        echo "</tr>";
        echo "<tr><td colspan=4><table align=center border=0 width=99% class=tablefont>";
        echo "<tr><td align=right>Transfer-Conf DTMF 1: </td><td align=left><input type=text name=xferconf_a_dtmf size=20 maxlength=50 value=\"$xferconf_a_dtmf\">$NWB#osdial_campaigns-xferconf_a_dtmf$NWE</td>";
        echo "    <td align=right>Transfer-Conf Number 1: </td><td align=left><input type=text name=xferconf_a_number size=20 maxlength=50 value=\"$xferconf_a_number\">$NWB#osdial_campaigns-xferconf_a_dtmf$NWE</td></tr>";
        echo "<tr><td align=right>Transfer-Conf DTMF 2: </td><td align=left><input type=text name=xferconf_b_dtmf size=20 maxlength=50 value=\"$xferconf_b_dtmf\">$NWB#osdial_campaigns-xferconf_a_dtmf$NWE</td>";
        echo "    <td align=right>Transfer-Conf Number 2: </td><td align=left><input type=text name=xferconf_b_number size=20 maxlength=50 value=\"$xferconf_b_number\">$NWB#osdial_campaigns-xferconf_a_dtmf$NWE</td></tr>";
        echo "<tr><td colspan=4>&nbsp;</td></tr>";
        echo "<tr><td align=right class=no-ul colspan=4>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // WEB FORM
        echo "<a name=webform></a>";
        echo "<table width=$section_width cellspacing=3 frame=border class=rounded-corners>";
        echo "<tr><td colspan=2 align=left class=top_header_sect valign=top width=40%>Web Form</td></tr>";
        echo "<tr><td colspan=2>&nbsp;</td></tr>";
        echo "<tr><td align=right>Web Form 1: </td><td align=left><input type=text name=web_form_address size=50 maxlength=255 value=\"$web_form_address\">$NWB#osdial_campaigns-web_form_address$NWE</td></tr>";
        echo "<tr><td align=right>Web Form 1 External: </td><td align=left><select size=1 name=web_form_extwindow><option>Y</option><option>N</option><option SELECTED>$web_form_extwindow</option></select><font size=1><i>'Y' to open in new window, 'N' to open in an $t1 frame.</i></font>$NWB#osdial_campaigns-web_form_extwindow$NWE</td></tr>";
        echo "<tr><td align=right>Web Form 2: </td><td align=left><input type=text name=web_form_address2 size=50 maxlength=255 value=\"$web_form_address2\">$NWB#osdial_campaigns-web_form_address$NWE</td></tr>";
        echo "<tr><td align=right>Web Form 2 External: </td><td align=left><select size=1 name=web_form2_extwindow><option>Y</option><option>N</option><option SELECTED>$web_form2_extwindow</option></select><font size=1><i>'Y' to open in new window, 'N' to open in an $t1 frame.</i></font>$NWB#osdial_campaigns-web_form_extwindow$NWE</td></tr>";
        echo "<tr><td align=right>Dispo Submit Method: </td><td align=left><select size=1 name=submit_method><option>NORMAL</option><option>WEBFORM1</option><option>WEBFORM2</option><option SELECTED>$submit_method</option></select>$NWB#osdial_campaigns-dispo_submit_method$NWE</td></tr>";
        echo "<tr><td align=right>Get Call Launch: </td><td align=left><select size=1 name=get_call_launch><option selected>NONE</option><option>SCRIPT</option><option>WEBFORM</option><option>WEBFORM2</option><option selected>$get_call_launch</option></select>$NWB#osdial_campaigns-get_call_launch$NWE</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // SCRIPT OPTIONS
        echo "<a name=script></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>Script Options</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right><a href=\"$PHP_SELF?ADD=3111111&script_id=$script_id\">Script</a>: </td><td align=left><select size=1 name=script_id>";
        echo get_scripts($link, $script_id);
        #echo "$scripts_list";
        #echo "<option selected value=\"$script_id\">$script_id - $scriptname_list[$script_id]</option>";
        echo "</select>$NWB#osdial_campaigns-campaign_script$NWE</td></tr>";
        echo "<tr><td align=right>Allow Tab Switch: </td><td align=left><select size=1 name=allow_tab_switch><option>Y</option><option>N</option><option selected>$allow_tab_switch</option></select>$NWB#osdial_campaigns-allow_tab_switch$NWE</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // END OF CALL OPTIONS
        echo "<a name=eoc></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>End of Call Options</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right>Scheduled Callbacks: </td><td align=left><select size=1 name=scheduled_callbacks><option>Y</option><option>N</option><option SELECTED>$scheduled_callbacks</option></select>$NWB#osdial_campaigns-scheduled_callbacks$NWE</td></tr>";
        
        echo "<tr><td align=right>Wrap Up Seconds: </td><td align=left><input type=text name=wrapup_seconds size=5 maxlength=3 value=\"$wrapup_seconds\">$NWB#osdial_campaigns-wrapup_seconds$NWE</td></tr>";
        echo "<tr><td align=right>Wrap Up Message: </td><td align=left><input type=text name=wrapup_message size=40 maxlength=255 value=\"$wrapup_message\">$NWB#osdial_campaigns-wrapup_message$NWE</td></tr>";
        if (file_exists($WeBServeRRooT . '/admin/include/content/scripts/email_templates.php')) {
            echo "<tr><td align=right valign=top>Email Templates: </td><td align=left><select size=4 multiple name=\"email_templates[]\">";
            echo get_email_templates($link, $email_templates);
            echo "</select>$NWB#osdial_campaigns-email_templates$NWE</td></tr>";
            echo "<tr><td align=right>Email Blacklist: </td><td align=left><input type=button name=email_blacklist value=\"EDIT BLACKLIST\" onclick=\"window.location='$PHP_SELF?ADD=3eb&SUB=2&campaign_id=$campaign_id';\"></td></tr>";
        }
        echo "<tr><td align=right>Agent Pause Codes Active: </td><td align=left><select size=1 name=agent_pause_codes_active><option>Y</option><option>N</option><option SELECTED>$agent_pause_codes_active</option></select>$NWB#osdial_campaigns-agent_pause_codes_active$NWE</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        
        
        
        // DNC OPTIONS
        echo "<a name=dnc></a>";
        echo "<table width=$section_width cellspacing=3 frame=border cellpadding=0 cellspacing=0 class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect valign=top width=40%>Do Not Call Options</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=right>Use Internal DNC List: </td><td align=left><select size=1 name=use_internal_dnc><option>Y</option><option>N</option><option SELECTED>$use_internal_dnc</option></select>$NWB#osdial_campaigns-use_internal_dnc$NWE</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td align=right class=no-ul colspan=2>";
		jump_section(1);
		echo "<input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table>&nbsp;";
        

		// Allowed Groups
        echo "<script type=\"text/javascript\">";
        echo "createEditableSelect(document.forms[0].auto_dial_level);";
        echo "createEditableSelect(document.forms[0].adaptive_maximum_level);";
        echo "createEditableSelect(document.forms[0].ADAPT_auto_dial_level);";
        echo "</script>";
        $disp_inbound_closer = "visibility:collapse;";
        $disp_allow_inbound = "visibility:collapse;";
        $disp_allow_closers = "visibility:collapse;";
        if ($campaign_allow_inbound == 'Y' or $allow_closers == 'Y') {
            $disp_inbound_closer = "visibility:visible;";
            if ($campaign_allow_inbound == 'Y') $disp_allow_inbound = "visibility:visible;";
            if ($allow_closers == 'Y') $disp_allow_closers = "visibility:visible;";
        }

        echo "<a name=groups></a>";
        if ($campaign_allow_inbound == 'Y' or $allow_closers == 'Y') {
        	echo "<table width=$section_width cellspacing=3 cellpadding=0 cellspacing=0 frame=border class=rounded-corners>";
        	echo "<tr style=\"$disp_inbound_closer\">";
        	echo " <td align=left class=top_header_sect>Allowed Groups</td></tr>";
        	echo "  <tr><td align=center><br>";
        
        	echo "  <table cellspacing=0 cellpadding=0 border=0 align=center>";
        	echo "    <tr>";
        	echo "      <td style=\"$disp_allow_closers\" align=center>Allowed Transfer Groups: $NWB#osdial_campaigns-xfer_groups$NWE</td>";
        	echo "      <td width=15%>&nbsp;</td>";
        	echo "      <td style=\"$disp_allow_inbound\" align=center>Allowed Inbound Groups: $NWB#osdial_campaigns-closer_campaigns$NWE</td>";
        	echo "    </tr>";
        	echo "    <tr>";
        	echo "      <td style=\"$disp_allow_closers\" align=center valign=top>";
        	echo "        <table bgcolor=grey cellspacing=1 border=0>";
        	echo "          $XFERgroups_listTAB";
        	echo "          <tr class=tabfooter>";
        	echo "            <td align=center colspan=2 class=tabbutton>";
        	echo "              Default: <select style=\"font-size: 10px;\" size=1 name=default_xfer_group>$Xgroups_menu</select><br><br>";
        	echo "              <input style='color:#1C4754' type=submit name=SUBMIT value=Submit>";
        	echo "            </td>";
        	echo "          </tr>";
        	echo "        </table>";
        	echo "      </td>";
        	echo "      <td>&nbsp;</td>";
        	echo "      <td style=\"$disp_allow_inbound\" align=center valign=top>";
        	echo "        <table bgcolor=grey cellspacing=1 border=0>";
        	echo "          $groups_listTAB";
        	echo "          <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        	echo "        </table>";
        	echo "      </td>";
        	echo "    </tr>";
			echo "  </table>";
        	echo " </td>";
        	echo "</tr>";
        	echo "<tr><td align=center class=no-ul colspan=3><br />";
        	jump_section(1);
        	echo "</td></tr>";
			echo "</table>";
		}
        echo "</FORM>";

        
        
        # List Statuses
		echo "<a name=alists></a>";
        echo "<table width=$section_width cellspacing=3 cellpadding=0 cellspacing=0 frame=border class=rounded-corners>";
        echo "<tr><td align=left class=top_header_sect>List Statuses</td></tr>";
        echo "<tr><td align=center><br>";

        $dispinact = get_variable('dispinact');
        $dispinactSQL = "AND active='Y'";
        if ($dispinact == 1) $dispinactSQL = "";
        
		echo "<font color=$default_text size=+1>LISTS WITHIN THIS CAMPAIGN &nbsp; $NWB#osdial_campaign_lists$NWE</font></b><br>";
		echo "<center><font color=$default_text size=-1>";
		if ($dispinact == '1') {
			echo "<a href=\"$PHP_SELF?ADD=$ADD&campaign_id=$campaign_id&dispinact=\">(Hide Inactive)</a>";
		} else {
			echo "<a href=\"$PHP_SELF?ADD=$ADD&campaign_id=$campaign_id&dispinact=1\">(Show Inactive)</a>";
		}
		echo "</font><br>";
		
		echo "<table bgcolor=grey width=400 cellspacing=1>";
		echo "<tr class=tabheader><td align=center>LIST ID</td><td align=center>LIST NAME</td><td align=center>ACTIVE</td></tr>";


        $active_lists = 0;
        $inactive_lists = 0;
        $stmt=sprintf("SELECT list_id,active,list_name FROM osdial_lists WHERE 1=1 $dispinactSQL and campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $lists_to_print = mysql_num_rows($rslt);
        $camp_lists='';

        $o=0;
        while ($lists_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $o++;
            if (OSDpreg_match("/Y/", $rowx[1])) {$active_lists++;   $camp_lists .= "'$rowx[0]',";}
            if (OSDpreg_match("/N/", $rowx[1])) {$inactive_lists++;}

            echo "<tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=311&list_id=$rowx[0]');\"><td><a href=\"$PHP_SELF?ADD=311&list_id=$rowx[0]\">$rowx[0]</a></td><td>$rowx[2]</td><td align=center>$rowx[1]</td></tr>";
        }
        echo "<tr class=tabfooter><td colspan=3></td></tr>";
        echo "</table></center><br>";
        echo "<center><b>";

        $fSQL = '';
        $Tfilter = get_first_record($link, 'osdial_lead_filters', '*', sprintf("lead_filter_id='%s'",mres($lead_filter_id)) );
        if (OSDstrlen($Tfilter['lead_filter_sql'])>4) $fSQL = "and " . OSDpreg_replace('/^and|and$|^or|or$/i','',$Tfilter['lead_filter_sql']);

        $camp_lists = OSDpreg_replace('/,$/','',$camp_lists);
        echo "<br><font>This campaign has $active_lists active lists and $inactive_lists inactive lists</font><br><br>";

        if ($display_dialable_count == 'Y') {
            ### call function to calculate and print dialable leads
            dialable_leads($DB,$link,$local_call_time,$dial_statuses,$camp_lists,$fSQL);
            echo " - <font size=1><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id&stage=hide_dialable\">HIDE</a></font><br><br>";
        } else {
            echo "<a href=\"$PHP_SELF?ADD=73&campaign_id=$campaign_id\" target=\"_blank\">Popup Dialable Leads Count</a>";
            echo " - <font size=1><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id&stage=show_dialable\">SHOW</a></font><br><br>";
        }


        $Thopper = get_first_record($link, 'osdial_hopper', 'count(*) AS count', sprintf("campaign_id='%s' AND status IN ('API')",mres($campaign_id)) );
        echo "<font>This campaign has " . $Thopper['count'] . " API leads in the dial hopper<br><br>";

        $Thopper = get_first_record($link, 'osdial_hopper', 'count(*) AS count', sprintf("campaign_id='%s' AND status IN ('READY')",mres($campaign_id)) );
        echo "<font>This campaign has " . $Thopper['count'] . " READY leads in the dial hopper<br><br>";

        echo "<a href=\"$PHP_SELF?ADD=999999&SUB=28&group=$campaign_id\">Click here to see what leads are in the hopper right now</a><br><br>";
        echo "<a href=\"$PHP_SELF?ADD=81&campaign_id=$campaign_id\">Click here to see all CallBack Holds in this campaign</a><br><br>";
        if ($LOG['view_agent_realtime']) echo "<a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12&group=$campaign_id\">Click here to see a Time On Dialer report for this campaign</a></font><br><br><br />";
			
			echo "</b></center>";
        }
 		echo "</td></tr></table>";
		
		
		
		echo "<table align=center bgcolor=#E9E8D9 width=100% cellspacing=3 cellpadding=0 cellspacing=0 class=tablefont>";
        echo "<tr><td align=center>";

        ##### CAMPAIGN CUSTOM STATUSES #####
        if ($SUB==22) {

            ##### get status category listings for dynamic pulldown
            $stmt="SELECT vsc_id,vsc_name FROM osdial_status_categories ORDER BY vsc_id DESC;";
            $rslt=mysql_query($stmt, $link);
            $cats_to_print = mysql_num_rows($rslt);
            $cats_list="";

            $o=0;
            while ($cats_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $cats_list .= "<option value=\"$rowx[0]\">$rowx[0] - " . OSDsubstr($rowx[1],0,20) . "</option>";
                $catsname_list["$rowx[0]"] = OSDsubstr($rowx[1],0,20);
                $o++;
            }


            echo "<center><br><font class=top_header_sect color=$default_text size=+1>CUSTOM STATUSES WITHIN THIS CAMPAIGN &nbsp; $NWB#osdial_campaign_statuses$NWE</font><br><br>";
            echo "  <table bgcolor=grey width=$section_width cellspacing=1 align=center>";
            echo "    <tr class=tabheader>";
            echo "      <td align=center>STATUS</td>";
            echo "      <td align=center>DESCRIPTION</td>";
            echo "      <td align=center>SELECTABLE</td>";
            echo "      <td align=center>HUMAN&nbsp;ANSWER</td>";
            echo "      <td align=center>CATEGORY</td>";
            echo "      <td colspan=2 align=center>ACTIONS</td>";
            echo "    </tr>";

            $stmt=sprintf("SELECT * FROM osdial_campaign_statuses WHERE campaign_id='%s';",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
            $statuses_to_print = mysql_num_rows($rslt);
            $AScategory='';
            $o=0;
            while ($statuses_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $AScategory = $rowx[5];
                $o++;

                echo "    <form action=$PHP_SELF method=POST>";
                echo "    <input type=hidden name=DB value=$DB>";
                echo "    <input type=hidden name=ADD value=42>";
                echo "    <input type=hidden name=stage value=modify>";
                echo "    <input type=hidden name=status value=\"$rowx[0]\">";
                echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">";
                echo "    <tr " . bgcolor($o) . " class=\"row font1\">";
                echo "      <td nowrap><font size=1>$rowx[0]</font></td>";
                echo "      <td align=center class=tabinput nowrap><input type=text name=status_name size=20 maxlength=30 value=\"$rowx[1]\"></td>";
                echo "      <td align=center class=tabinput nowrap><select size=1 name=selectable><option>Y</option><option>N</option><option selected>$rowx[2]</option></select></td>";
                echo "      <td align=center class=tabinput nowrap><select size=1 name=human_answered><option>Y</option><option>N</option><option selected>$rowx[4]</option></select></td>";
                echo "      <td align=center class=tabinput nowrap><select size=1 name=category>$cats_list<option selected value=\"$AScategory\">$AScategory - $catsname_list[$AScategory]</option></select></td>";
                echo "      <td align=center><a href=\"$PHP_SELF?ADD=42&campaign_id=$campaign_id&status=$rowx[0]&stage=delete\">DELETE</a></td>";
                echo "      <td align=center class=tabinput class=tabbutton1 nowrap><input type=submit name=submit value=MODIFY></td>";
                echo "    </tr>";
                echo "    </form>";
            }

            echo "    <form action=$PHP_SELF method=POST><br>";
            echo "    <input type=hidden name=DB value=$DB>";
            echo "    <input type=hidden name=ADD value=22>";
            echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">";
            echo "    <tr class=tabfooter>";
            echo "      <td class=tabinput align=center><input type=text name=status size=10 maxlength=8></td>";
            echo "      <td class=tabinput align=center><input type=text name=status_name size=20 maxlength=30></td>";
            echo "      <td class=tabinput align=center><select size=1 name=selectable><option>Y</option><option>N</option></select></td>";
            echo "      <td class=tabinput align=center><select size=1 name=human_answered><option>Y</option><option>N</option></select></td>";
            echo "      <td class=tabinput align=center><select size=1 name=category>$cats_list<option selected value=\"$AScategory\">$AScategory - $catsname_list[$AScategory]</option></select></td>";
            echo "      <td class=tabbutton1 colspan=2 align=center><input type=submit name=submit value=ADD></td>";
            echo "    </tr>";
            echo "    </form>";
            echo "  </table>";
            echo "</center>";
        }

        ##### CAMPAIGN HOTKEYS #####
        if ($SUB==23) {
            echo "<center><br><font class=top_header color=$default_text size=+1>CUSTOM HOT KEYS WITHIN THIS CAMPAIGN &nbsp; $NWB#osdial_campaign_hotkeys$NWE</font><br><br>";
            echo "  <table bgcolor=grey width=500 cellspacing=1 align=center>";
            echo "    <tr class=tabheader>";
            echo "      <td align=center>HOT&nbsp;KEY</td>";
            echo "      <td align=center>STATUS</td>";
            echo "      <td align=center>XFER&nbsp;EXTEN</td>";
            echo "      <td align=center>ACTIONS</td>";
            echo "    </tr>";

            $stmt=sprintf("SELECT * FROM osdial_campaign_hotkeys WHERE campaign_id='%s' ORDER BY hotkey;",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
            $statuses_to_print = mysql_num_rows($rslt);
            $o=0;
            while ($statuses_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $o++;

                echo "    <tr " . bgcolor($o) . " class=\"row font1\">";
                echo "      <td align=center>$rowx[1]</td>";
                echo "      <td>$rowx[0] - $rowx[2]</td>";
                echo "      <td align=center>$rowx[5]</td>";
                echo "      <td align=center><a href=\"$PHP_SELF?ADD=43&campaign_id=$campaign_id&status=$rowx[0]&hotkey=$rowx[1]&action=DELETE\">DELETE</a></td>";
                echo "    </tr>";
            }

            echo "    <form action=$PHP_SELF method=POST>";
            echo "    <input type=hidden name=DB value=$DB>";
            echo "    <input type=hidden name=ADD value=23>";
            echo "    <input type=hidden name=selectable value=Y>";
            echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">";
            echo "    <tr class=tabfooter>";
            echo "      <td class=tabinput align=center>";
            echo "        <select size=1 name=hotkey>";
            echo "          <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option>";
            echo "        </select>";
            echo "      </td>";
            echo "      <td class=tabinput align=center>";
            echo "        <select size=1 name=HKstatus>";
            echo "          $HKstatuses_list";
            echo "          <option value=\"ALTPH2-----Alternate Phone Hot Dial\">ALTPH2 - Alternate Phone Hot Dial</option>";
            echo "          <option value=\"ADDR3-----Address3 Hot Dial\">ADDR3 - Address3 Hot Dial</option>";
            echo "        </select>";
            echo "      </td>";
            echo "      <td class=tabinput align=center><input type=text name=xfer_exten size=10 maxlength=20 value=\"\"></td>";
            echo "      <td class=tabbutton1 align=center><input type=submit name=submit value=ADD></td>";
            echo "    </tr>";
            echo "    </form>";
            echo "  </table>";
            echo "</center>";
        }

        ##### CAMPAIGN LEAD RECYCLING #####
        if ($SUB==25) {
            echo "<center><br><font class=top_header color=$default_text size=+1>LEAD RECYCLING WITHIN THIS CAMPAIGN &nbsp; $NWB#osdial_lead_recycle$NWE</font><br><br>";
            echo "  <table bgcolor=grey width=600 cellspacing=1>";
            echo "    <tr class=tabheader>";
            echo "      <td>&nbsp;</td>";
            echo "      <td colspan=2 align=center>ATTEMPT</td>";
            echo "      <td colspan=3>&nbsp;</td>";
            echo "    </tr>";
            echo "    <tr class=tabheader>";
            echo "      <td align=center>STATUS</td>";
            echo "      <td align=center>DELAY</td>";
            echo "      <td align=center>MAXIMUM</td>";
            echo "      <td align=center>ACTIVE</td>";
            echo "      <td colspan=2 align=center>ACTIONS</td>";
            echo "    </tr>";

            $stmt=sprintf("SELECT * FROM osdial_lead_recycle WHERE campaign_id='%s' ORDER BY status;",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
            $recycle_to_print = mysql_num_rows($rslt);
            $o=0;
            while ($recycle_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $o++;

                echo "    <form action=$PHP_SELF method=POST>";
                echo "    <input type=hidden name=DB value=$DB>";
                echo "    <input type=hidden name=status value=\"$rowx[2]\">";
                echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">";
                echo "    <input type=hidden name=SUB value=25>";
                echo "    <input type=hidden name=ADD value=45>";
                echo "    <tr " . bgcolor($o) . " class=\"row font1\">";
                echo "      <td align=center>$rowx[2]</td>";
                echo "      <td class=tabinput align=center><input type=text size=10 maxlength=10 name=attempt_delay value=\"$rowx[3]\"></td>";
                echo "      <td class=tabinput align=center><input type=text size=5 maxlength=3 name=attempt_maximum value=\"$rowx[4]\"></td>";
                echo "      <td class=tabinput align=center><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$rowx[5]</option></select></td>";
                echo "      <td align=center><a href=\"$PHP_SELF?ADD=65&campaign_id=$campaign_id&status=$rowx[2]\">DELETE</a></td>";
                echo "      <td class=tabbutton1 align=center nowrap><input type=submit name=submit value=MODIFY></td>";
                echo "    </tr>";
                echo "    </form>";
            }

            echo "    <form action=$PHP_SELF method=POST>";
            echo "    <input type=hidden name=DB value=$DB>";
            echo "    <input type=hidden name=SUB value=25>";
            echo "    <input type=hidden name=ADD value=25>";
            echo "    <input type=hidden name=active value=\"N\">";
            echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">";
            echo "    <tr class=tabfooter>";
            echo "      <td class=tabinput align=center><select size=1 name=status>$LRstatuses_list</select></td>";
            echo "      <td class=tabinput align=center><input type=text size=10 maxlength=10 name=attempt_delay></td>";
            echo "      <td class=tabinput align=center><input type=text size=5 maxlength=3 name=attempt_maximum></td>";
            echo "      <td></td>";
            echo "      <td colspan=2 class=tabbutton1 align=center><input type=submit name=submit value=ADD></td>";
            echo "    </tr>";
            echo "    </form>";
            echo "  </table>";
            echo "</center>";
        }

        ##### CAMPAIGN AUTO-ALT-NUMBER DIALING #####
        if ($SUB==26) {
            echo "<center><br><font class=top_header color=$default_text size=+1>AUTO ALT NUMBER DIALING FOR THIS CAMPAIGN &nbsp; $NWB#osdial_auto_alt_dial_statuses$NWE</font><br><br>";
            echo "  <table bgcolor=grey width=300 cellspacing=1>";
            echo "    <tr class=tabheader>";
            echo "      <td align=center>STATUSES</td>";
            echo "      <td align=center>ACTIONS</td>";
            echo "    </tr>";

            $auto_alt_dial_statuses = OSDpreg_replace("/ -$/","",$auto_alt_dial_statuses);
            $AADstatuses = explode(" ", $auto_alt_dial_statuses);
            $AADs_to_print = (count($AADstatuses) -1);

            $o=0;
            while ($AADs_to_print > $o) {
                $o++;

                echo "    <tr " . bgcolor($o) . " class=\"row font1\">";
                echo "      <td align=center>$AADstatuses[$o]</td>";
                echo "      <td align=center><a href=\"$PHP_SELF?ADD=66&campaign_id=$campaign_id&status=$AADstatuses[$o]\">DELETE</a></td>";
                echo "    </tr>";
            }

            echo "    <form action=$PHP_SELF method=POST><br>";
            echo "    <input type=hidden name=DB value=$DB>";
            echo "    <input type=hidden name=ADD value=26>";
            echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">";
            echo "    <tr class=tabfooter>";
            echo "      <td align=center class=tabinput><select size=1 name=status>$LRstatuses_list</select></td>";
            echo "      <td align=center class=tabbutton1><input type=submit name=submit value=ADD></td>";
            echo "    </form>";
            echo "  </table>";
            echo "</center>";
        }

        ##### CAMPAIGN PAUSE CODES #####
        if ($SUB==27) {
            echo "<center><br><font class=top_header color=$default_text size=+1>AGENT PAUSE CODES FOR THIS CAMPAIGN &nbsp; $NWB#osdial_pause_codes$NWE</font><br><br>";
            echo "  <table bgcolor=grey width=600 cellspacing=1>";
            echo "    <tr class=tabheader>";
            echo "      <td align=center>PAUSE CODE</td>";
            echo "      <td align=center>DESCRIPTION</td>";
            echo "      <td align=center>BILLABLE</td>";
            echo "      <td align=center colspan=2>ACTIONS</td>";
            echo "    </tr>";

            $stmt=sprintf("SELECT * FROM osdial_pause_codes WHERE campaign_id='%s' ORDER BY pause_code;",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
            $pause_codes_to_print = mysql_num_rows($rslt);
            $o=0;
            while ($pause_codes_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $o++;

                echo "    <form action=$PHP_SELF method=POST>";
                echo "    <input type=hidden name=DB value=$DB>";
                echo "    <input type=hidden name=ADD value=47>";
                echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">";
                echo "    <input type=hidden name=pause_code value=\"$rowx[0]\"> &nbsp;";
                echo "    <tr " . bgcolor($o) . " class=\"row font1\">";
                echo "      <td align=center>$rowx[0]</td>";
                echo "      <td align=center class=tabinput><input type=text size=20 maxlength=30 name=pause_code_name value=\"$rowx[1]\"></td>";
                echo "      <td align=center class=tabinput><select size=1 name=billable><option>YES</option><option>NO</option><option>HALF</option><option SELECTED>$rowx[2]</option></select></td>";
                echo "      <td align=center><a href=\"$PHP_SELF?ADD=67&campaign_id=$campaign_id&pause_code=$rowx[0]\">DELETE</a></td>";
                echo "      <td align=center class=tabbutton1><input type=submit name=submit value=MODIFY></td>";
                echo "    </tr>";
                echo "    </form>";
            }

            echo "    <form action=$PHP_SELF method=POST><br>";
            echo "    <input type=hidden name=DB value=$DB>";
            echo "    <input type=hidden name=ADD value=27>";
            echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">";
            echo "    <tr class=tabfooter>";
            echo "      <td align=center class=tabinput><input type=text size=8 maxlength=6 name=pause_code></td>";
            echo "      <td align=center class=tabinput><input type=text size=20 maxlength=30 name=pause_code_name></td>";
            echo "      <td align=center class=tabinput><select size=1 name=billable><option>YES</option><option>NO</option><option>HALF</option></select></td>";
            echo "      <td align=center class=tabbutton1 colspan=2><input type=submit name=submit value=ADD></td>";
            echo "    </tr>";
            echo "    </form>";
            echo "  </table>";
            echo "</center>";
        }
		
            

		# Terminate
        if ($SUB < 1) {
			echo "<br /><table width=$section_width cellspacing=0 cellpadding=0 cellspacing=0 frame=border class=rounded-corners><tr><td align=left class=top_header_sect>&nbsp;Terminate</td></tr>";
            echo "<tr><td align=center><br><br>";
            echo "<a href=\"$PHP_SELF?ADD=52&campaign_id=$campaign_id\">LOG ALL AGENTS OUT OF THIS CAMPAIGN</a><br><br>";
            echo "<a href=\"$PHP_SELF?ADD=53&campaign_id=$campaign_id\">Emergency Clear Auto Calls For This Campaign</a><br><br>";

            if ($LOG['delete_campaigns'] > 0) {
                echo "<br><br><a href=\"$PHP_SELF?ADD=51&campaign_id=$campaign_id\">Delete This Campaign</a><br /><br /><br /><br />";
            }
            echo "<span class=no-ul>";
            jump_section(1);
            echo "</span>";
        }
        
        
        // Above td, tr and table are left open for other screens below.
        
    } else {
        echo "<font color=red>You do not have permission to view this page</font>";
    }
}



######################
# ADD=34 modify campaign info in the system - Basic View
######################
/*
# send to not allowed screen if not in osdial_user_groups allowed_campaigns list
if ( ($ADD==34) and (!OSDpreg_match('/:' . $campaign_id . ':/',$LOG['allowed_campaignsSTR'])) ) {
    $ADD=30;
}

if ($ADD==34)
{
    if ($LOG['modify_campaigns']==1)
    {
        if ($stage=='show_dialable')
        {
            $stmt=sprintf("UPDATE osdial_campaigns SET display_dialable_count='Y' WHERE campaign_id='%s';",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
        }
        if ($stage=='hide_dialable')
        {
            $stmt=sprintf("UPDATE osdial_campaigns SET display_dialable_count='N' WHERE campaign_id='%s';",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
        }

        $stmt=sprintf("SELECT * FROM osdial_campaigns WHERE campaign_id='%s';",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $dial_status_a = $row[3];
        $dial_status_b = $row[4];
        $dial_status_c = $row[5];
        $dial_status_d = $row[6];
        $dial_status_e = $row[7];
        $lead_order = $row[8];
        $hopper_level = $row[13];
        $auto_dial_level = $row[14];
        $next_agent_call = $row[15];
        $local_call_time = $row[16];
        $voicemail_ext = $row[17];
        $dial_timeout = $row[18];
        $dial_prefix = $row[19];
        $campaign_cid = $row[20];
        $campaign_vdad_exten = $row[21];
        $script_id = $row[25];
        $get_call_launch = $row[26];
        $lead_filter_id = $row[35];
            if ($lead_filter_id=='') {$lead_filter_id='NONE';}
        $display_dialable_count = $row[39];
        $dial_method = $row[46];
        $adaptive_intensity = $row[51];
        $campaign_description = $row[57];
        $campaign_changedate = $row[58];
        $campaign_stats_refresh = $row[59];
        $campaign_logindate = $row[60];
        $dial_statuses = $row[61];
        $list_order_mix = $row[64];
        $default_xfer_group = $row[67];
        $campaign_allow_inbound = $row[65];
        $default_xfer_group = $row[67];
        $allow_tab_switch = $row[70];
        $answers_per_hour_limit = $row[71];
        $campaign_call_time = $row[72];
        $preview_force_dial_time = $row[73];
        $manual_preview_default = $row[74];
        $use_custom2_callerid = $row[78];

    if (OSDpreg_match("/DISABLED/",$list_order_mix))
        {$DEFlistDISABLE = '';    $DEFstatusDISABLED=0;}
    else
        {$DEFlistDISABLE = 'disabled';    $DEFstatusDISABLED=1;}

        $stmt="SELECT * FROM osdial_statuses ORDER BY status;";
        $rslt=mysql_query($stmt, $link);
        $statuses_to_print = mysql_num_rows($rslt);
        $LRstatuses_list='';
        $HKstatuses_list='';
        $statuses_list='';
        $dial_statuses_list='';
        $o=0;
        while ($statuses_to_print > $o) 
            {
            $rowx=mysql_fetch_row($rslt);
            $statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>";
            if ($rowx[0] != 'CBHOLD') {$dial_statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>";}
            $statname_list["$rowx[0]"] = "$rowx[1]";
            $LRstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>";
            if (OSDpreg_match("/Y/",$rowx[2]))
                {$HKstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>";}
            $o++;
            }

        $stmt=sprintf("SELECT * FROM osdial_campaign_statuses WHERE campaign_id='%s' ORDER BY status;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $Cstatuses_to_print = mysql_num_rows($rslt);

        $o=0;
        while ($Cstatuses_to_print > $o) 
            {
            $rowx=mysql_fetch_row($rslt);
            $statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>";
            if ($rowx[0] != 'CBHOLD') {$dial_statuses_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>";}
            $statname_list["$rowx[0]"] = "$rowx[1]";
            $LRstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>";
            if (OSDpreg_match("/Y/",$rowx[2]))
                {$HKstatuses_list .= "<option value=\"$rowx[0]-----$rowx[1]\">$rowx[0] - $rowx[1]</option>";}
            $o++;
            }

        $dial_statuses = OSDpreg_replace("/ -$/","",$dial_statuses);
        $Dstatuses = explode(" ", $dial_statuses);
        $Ds_to_print = (count($Dstatuses) -1);

    $stmt=sprintf("SELECT count(*) FROM osdial_campaigns_list_mix WHERE campaign_id='%s' AND status='ACTIVE';",mres($campaign_id));
    $rslt=mysql_query($stmt, $link);
    $rowx=mysql_fetch_row($rslt);
    if ($rowx[0] < 1)
        {
        $mixes_list="<option SELECTED value=\"DISABLED\">DISABLED</option>";
        $mixname_list["DISABLED"] = "DISABLED";
        }
    else
        {
        ##### get list_mix listings for dynamic pulldown
        $stmt=sprintf("SELECT vcl_id,vcl_name FROM osdial_campaigns_list_mix WHERE campaign_id='%s' AND status='ACTIVE' LIMIT 1;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $mixes_to_print = mysql_num_rows($rslt);
        $mixes_list="<option value=\"DISABLED\">DISABLED</option>";

        $o=0;
        while ($mixes_to_print > $o)
            {
            $rowx=mysql_fetch_row($rslt);
            $mixes_list .= "<option value=\"ACTIVE\">ACTIVE ($rowx[0] - $rowx[1])</option>";
            $mixname_list["ACTIVE"] = "$rowx[0] - $rowx[1]";
            $o++;
            }
        }

    if ($SUB < 1)
        {
        echo "<br /><p align=center><font color=$default_text size=+1>MODIFY CAMPAIGN</font></p>";
        echo "<form action=$PHP_SELF method=POST>";
        echo "<input type=hidden name=DB value=$DB>";
        echo "<input type=hidden name=ADD value=44>";
        echo "<input type=hidden name=campaign_id value=\"$campaign_id\">";
        echo "<table width=$section_width cellspacing=3>";
        echo "<tr bgcolor=$oddrows><td align=right width=40%>ID: </td><td align=left width=60%>";
        echo "<b>" . mclabel($row[0]) . "</b>";
        echo "$NWB#osdial_campaigns-campaign_id$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Name: </td><td align=left><input type=text name=campaign_name size=40 maxlength=40 value=\"$row[1]\">$NWB#osdial_campaigns-campaign_name$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left>$row[57]$NWB#osdial_campaigns-campaign_description$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Change Date: </td><td align=left title=\"$campaign_changedate\">" . dateToLocal($link,'first',$campaign_changedate,$webClientAdjGMT,'',$webClientDST,1) ." &nbsp; $NWB#osdial_campaigns-campaign_changedate$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Login Date: </td><td align=left title=\"$campaign_logindate\">" . dateToLocal($link,'first',$campaign_logindate,$webClientAdjGMT,'',$webClientDST,1) . " &nbsp; $NWB#osdial_campaigns-campaign_logindate$NWE</td></tr>";

        echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option SELECTED>$row[2]</option></select>$NWB#osdial_campaigns-active$NWE</td></tr>";
        echo "<tr style=\"visibility:collapse;\" bgcolor=$oddrows><td align=right>Park Extension: </td><td align=left>$row[9] - $row[10]$NWB#osdial_campaigns-park_ext$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 1: </td><td align=left>" . ellipse($row[11],50,true) . "$NWB#osdial_campaigns-web_form_address$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Web Form 2: </td><td align=left>" . ellipse($row[69],50,true) . "$NWB#osdial_campaigns-web_form_address$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Allow Transfer and Closers: </td><td align=left>$row[12] $NWB#osdial_campaigns-allow_closers$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Default Transfer Group: </td><td align=left>$default_xfer_group $NWB#osdial_campaigns-default_xfer_group$NWE</td></tr>";
        echo "<tr bgcolor=$oddrows><td align=right>Allow Inbound and Blended: </td><td align=left>$campaign_allow_inbound $NWB#osdial_campaigns-campaign_allow_inbound$NWE</td></tr>";

        $o=0;
        while ($Ds_to_print > $o) 
            {
            $o++;
            $Dstatus = $Dstatuses[$o];

            echo "<tr bgcolor=$oddrows><td align=right>Dial Status $o: </td><td align=left> ";
            if ($DEFstatusDISABLED > 0)
                {
                echo "<font color=grey><DEL><b>$Dstatus</b> - $statname_list[$Dstatus] &nbsp; &nbsp; &nbsp; &nbsp; <font size=2>";
                echo "REMOVE</DEL></td></tr>";
                }
            else
                {
                echo "<b>$Dstatus</b> - $statname_list[$Dstatus] &nbsp; &nbsp; &nbsp; &nbsp; <font size=2>";
                echo "<a href=\"$PHP_SELF?ADD=68&campaign_id=$campaign_id&status=$Dstatuses[$o]\">REMOVE</a></td></tr>";
                }
            }

        echo "<tr bgcolor=$oddrows><td align=right>Add A Dial Status: </td><td align=left><select size=1 style=\"width:300px;\" name=dial_status $DEFlistDISABLE>";
        echo "<option value=\"\"> - NONE - </option>";

        echo "$dial_statuses_list";
        echo "</select> &nbsp; ";
        echo "<input type=submit name=submit value=ADD> &nbsp; &nbsp; $NWB#osdial_campaigns-dial_status$NWE</td></tr>";

        echo "<tr bgcolor=$oddrows>";
        echo "  <td align=right>List Order: </td>";
        echo "  <td align=left>";
        $usel='';
        $dsel='';
        if (OSDpreg_match('/^UP/',$lead_order)) {
            $usel='selected';
        } else {
            $dsel='selected';
        }
        echo "    <select size=1 name=lead_order_direction><option $dsel>DOWN</option><option $usel>UP</option></select>";
        $tpsel='';
        $tlsel='';
        $tcsel='';
        $trsel='';
        $tisel='';
        $psel='';
        $lsel='';
        $csel='';
        $rsel='';
        $isel='';
        if (OSDpreg_match('/TZ PHONE/',$lead_order)) {
            $tpsel='selected';
        } else if (OSDpreg_match('/TZ LAST NAME/',$lead_order)) {
            $tlsel='selected';
        } else if (OSDpreg_match('/TZ COUNT/',$lead_order)) {
            $tcsel='selected';
        } else if (OSDpreg_match('/TZ RANDOM/',$lead_order)) {
            $trsel='selected';
        } else if (OSDpreg_match('/TZ/',$lead_order)) {
            $tisel='selected';
        } else if (OSDpreg_match('/PHONE/',$lead_order)) {
            $psel='selected';
        } else if (OSDpreg_match('/LAST NAME/',$lead_order)) {
            $lsel='selected';
        } else if (OSDpreg_match('/COUNT/',$lead_order)) {
            $csel='selected';
        } else if (OSDpreg_match('/RANDOM/',$lead_order)) {
            $rsel='selected';
        } else {
            $isel='selected';
        }
        echo "    <select size=1 name=lead_order_field><option value=\"\" $isel>ID</option><option $psel>PHONE</option><option $lsel>LAST NAME</option><option value=\"COUNT\" $csel>CALL COUNT</option><option $rsel>RANDOM</option><option value=\"TZ\" $tisel>TIMEZONE / ID</option><option value=\"TZ PHONE\" $tpsel>TIMEZONE / PHONE</option><option value=\"TZ LAST NAME\" $tlsel>TIMEZONE / LAST NAME</option><option value=\"TZ COUNT\" $tcsel>TIMEZONE / CALL COUNT</option><option value=\"TZ RANDOM\" $trsel>TIMEZONE / RANDOM</option></select>";
        $seln='';
        $sel2='';
        $sel3='';
        $sel4='';
        $sel5='';
        $sel6='';
        if (OSDpreg_match('/2nd NEW$/',$lead_order)) {
            $sel2='selected';
        } else if (OSDpreg_match('/3rd NEW$/',$lead_order)) {
            $sel3='selected';
        } else if (OSDpreg_match('/4th NEW$/',$lead_order)) {
            $sel4='selected';
        } else if (OSDpreg_match('/5th NEW$/',$lead_order)) {
            $sel5='selected';
        } else if (OSDpreg_match('/6th NEW$/',$lead_order)) {
            $sel6='selected';
        } else {
            $seln='selected';
        }
        echo "    <select size=1 name=lead_order_nthnew><option value=\"\" $seln>-------</option><option $sel2>2nd NEW</option><option $sel3>3rd NEW</option><option $sel4>4th NEW</option><option $sel5>5th NEW</option><option $sel6>6th NEW</option></select>";
        echo "    $NWB#osdial_campaigns-lead_order$NWE</td></tr>";
        #echo "<tr bgcolor=$oddrows><td align=right>List Order: </td><td align=left><select size=1 name=lead_order ><option>DOWN</option><option>UP</option><option>DOWN PHONE</option><option>UP PHONE</option><option>DOWN LAST NAME</option><option>UP LAST NAME</option><option>DOWN COUNT</option><option>UP COUNT</option><option>DOWN 2nd NEW</option><option>DOWN 3rd NEW</option><option>DOWN 4th NEW</option><option>DOWN 5th NEW</option><option>DOWN 6th NEW</option><option>UP 2nd NEW</option><option>UP 3rd NEW</option><option>UP 4th NEW</option><option>UP 5th NEW</option><option>UP 6th NEW</option><option>DOWN PHONE 2nd NEW</option><option>DOWN PHONE 3rd NEW</option><option>DOWN PHONE 4th NEW</option><option>DOWN PHONE 5th NEW</option><option>DOWN PHONE 6th NEW</option><option>UP PHONE 2nd NEW</option><option>UP PHONE 3rd NEW</option><option>UP PHONE 4th NEW</option><option>UP PHONE 5th NEW</option><option>UP PHONE 6th NEW</option><option>DOWN LAST NAME 2nd NEW</option><option>DOWN LAST NAME 3rd NEW</option><option>DOWN LAST NAME 4th NEW</option><option>DOWN LAST NAME 5th NEW</option><option>DOWN LAST NAME 6th NEW</option><option>UP LAST NAME 2nd NEW</option><option>UP LAST NAME 3rd NEW</option><option>UP LAST NAME 4th NEW</option><option>UP LAST NAME 5th NEW</option><option>UP LAST NAME 6th NEW</option><option>DOWN COUNT 2nd NEW</option><option>DOWN COUNT 3rd NEW</option><option>DOWN COUNT 4th NEW</option><option>DOWN COUNT 5th NEW</option><option>DOWN COUNT 6th NEW</option><option>UP COUNT 2nd NEW</option><option>UP COUNT 3rd NEW</option><option>UP COUNT 4th NEW</option><option>UP COUNT 5th NEW</option><option>UP COUNT 6th NEW</option><option>RANDOM</option><option SELECTED>$lead_order</option></select>$NWB#osdial_campaigns-lead_order$NWE</td></tr>";
        #echo "<tr bgcolor=$oddrows><td align=right>List Order: </td><td align=left><select size=1 name=lead_order><option>DOWN</option><option>UP</option><option>UP PHONE</option><option>DOWN PHONE</option><option>UP LAST NAME</option><option>DOWN LAST NAME</option><option>UP COUNT</option><option>DOWN COUNT</option><option>DOWN COUNT 2nd NEW</option><option>DOWN COUNT 3rd NEW</option><option>DOWN COUNT 4th NEW</option><option>DOWN COUNT 5th NEW</option><option>DOWN COUNT 6th NEW</option><option SELECTED>$lead_order</option></select>$NWB#osdial_campaigns-lead_order$NWE</td></tr>";

        echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=31&SUB=29&campaign_id=$campaign_id&vcl_id=$list_order_mix\">List Mix</a>: </td><td align=left><select size=1 name=list_order_mix>";
        echo "$mixes_list";
        if (OSDpreg_match("/DISABLED/",$list_order_mix))
            {echo "<option selected value=\"$list_order_mix\">$list_order_mix - $mixname_list[$list_order_mix]</option>";}
        else
            {echo "<option selected value=\"ACTIVE\">ACTIVE ($mixname_list[ACTIVE])</option>";}
        echo "</select>$NWB#osdial_campaigns-list_order_mix$NWE</td></tr>";

        echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=31111111&lead_filter_id=$lead_filter_id\">Lead Filter</a>: </td><td align=left><select size=1 name=lead_filter_id>";
        echo get_filters($link, $lead_filter_id);
        #echo "$filters_list";
        #echo "<option selected value=\"$lead_filter_id\">$lead_filter_id - $filtername_list[$lead_filter_id]</option>";
        echo "</select>$NWB#osdial_campaigns-lead_filter_id$NWE</td></tr>";

        echo "<tr bgcolor=$oddrows><td align=right>Hopper Level: </td><td align=left><select size=1 name=hopper_level><option>1</option><option>5</option><option>10</option><option>20</option><option>50</option><option>100</option><option>200</option><option>500</option><option>700</option><option>1000</option><option>2000</option><option SELECTED>$hopper_level</option></select>$NWB#osdial_campaigns-hopper_level$NWE</td></tr>";

        echo "<tr bgcolor=$oddrows><td align=right>Force Reset of Hopper: </td><td align=left><select size=1 name=reset_hopper><option>Y</option><option SELECTED>N</option></select>$NWB#osdial_campaigns-force_reset_hopper$NWE</td></tr>";

        echo "<tr bgcolor=$unusualrows><td align=right>Dial Method: </td><td align=left><select size=1 name=dial_method><option >MANUAL</option><option>RATIO</option><option>ADAPT_HARD_LIMIT</option><option>ADAPT_TAPERED</option><option>ADAPT_AVERAGE</option><option SELECTED>$dial_method</option></select>$NWB#osdial_campaigns-dial_method$NWE</td></tr>";

        echo "<tr bgcolor=$unusualrows><td align=right>Auto Dial Level: </td><td align=left nowrap><input type=text name=auto_dial_level size=6 maxlength=6 value=\"$auto_dial_level\" selectBoxOptions=\"0;1;1.1;1.2;1.3;1.4;1.5;1.6;1.7;1.8;1.9;2.0;2.2;2.5;3.0;4.0;4.5;5.0\"> (0 = off)$NWB#osdial_campaigns-auto_dial_level$NWE</td></tr>";

        echo "<tr bgcolor=$unusualrows><td align=right>Adapt Intensity Modifier: </td><td align=left><select size=1 name=adaptive_intensity>";
        $n=40;
        while ($n>=-40)
            {
            $dtl = 'Balanced';
            if ($n<0) {$dtl = 'Less Intense';}
            if ($n>0) {$dtl = 'More Intense';}
            if ($n == $adaptive_intensity) 
                {echo "<option SELECTED value=\"$n\">$n - $dtl</option>";}
            else
                {echo "<option value=\"$n\">$n - $dtl</option>";}
            $n--;
            }
        echo "</select> $NWB#osdial_campaigns-adaptive_intensity$NWE</td></tr>";

        echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=3111111&script_id=$script_id\">Script</a>: </td><td align=left>";
        if ($LOG['multicomp_user'] > 0) {
            echo OSDsubstr($script_id,3,OSDstrlen($script_id));
        } else {
            echo $script_id;
        }
        echo "</td></tr>";

        echo "<tr bgcolor=$oddrows><td align=right>Get Call Launch: </td><td align=left>$get_call_launch</td></tr>";

        echo "<tr bgcolor=$oddrows><td align=right>Answers Per Hour Limit: </td><td align=left><input type=text name=answers_per_hour_limit size=10 maxlength=10 value=\"$answers_per_hour_limit\">$NWB#osdial_campaigns-answers_per_hour_limit$NWE</td></tr>";

        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input style='color:#1C4754' type=submit name=SUBMIT value=Submit></td></tr>";
        echo "</table></center></FORM>";

        echo "<script type=\"text/javascript\">";
        echo "createEditableSelect(document.forms[0].auto_dial_level);";
        echo "</script>";

            $dispinact = get_variable('dispinact');
            $dispinactSQL = "AND active='Y'";
            if ($dispinact == 1) $dispinactSQL = "";

        echo "<center>";
        echo "<br><br><b><font color=$default_text>LISTS WITHIN THIS CAMPAIGN: &nbsp; $NWB#osdial_campaign_lists$NWE</font></b><br>";
        echo "<center><font color=$default_text size=-1>";
        if ($dispinact == '1') {
            echo "<a href=\"$PHP_SELF?ADD=$ADD&campaign_id=$campaign_id&dispinact=\">(Hide Inactive)</a>";
        } else {
            echo "<a href=\"$PHP_SELF?ADD=$ADD&campaign_id=$campaign_id&dispinact=1\">(Show Inactive)</a>";
        }
        echo "</font><br>";
        echo "<table bgcolor=grey width=400 cellspacing=1>";
        echo "<tr class=tabheader><td align=center>LIST ID</td><td align=center>LIST NAME</td><td align=center>ACTIVE</td></tr>";

            $active_lists = 0;
            $inactive_lists = 0;
            $stmt=sprintf("SELECT list_id,active,list_name FROM osdial_lists WHERE 1=1 $dispinactSQL AND campaign_id='%s';",mres($campaign_id));
            $rslt=mysql_query($stmt, $link);
            $lists_to_print = mysql_num_rows($rslt);
            $camp_lists='';

            $o=0;
            while ($lists_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $o++;
            if (OSDpreg_match("/Y/", $rowx[1])) {$active_lists++;   $camp_lists .= "'$rowx[0]',";}
            if (OSDpreg_match("/N/", $rowx[1])) {$inactive_lists++;}

            echo "<tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=311&list_id=$rowx[0]');\"><td><a href=\"$PHP_SELF?ADD=311&list_id=$rowx[0]\">$rowx[0]</a></td><td>$rowx[2]</td><td align=center>$rowx[1]</td></tr>";

            }

        echo "<tr class=tabfooter><td colspan=3></td></tr>";
        echo "</table></center><br>";
        echo "<center><b>";

        $fSQL = '';
        $Tfilter = get_first_record($link, 'osdial_lead_filters', '*', sprintf("lead_filter_id='%s'",mres($lead_filter_id)) );
        if (OSDstrlen($Tfilter['lead_filter_sql'])>4) $fSQL = "and " . OSDpreg_replace('/^and|and$|^or|or$/i','',$Tfilter['lead_filter_sql']);

        $camp_lists = OSDpreg_replace('/,$/','',$camp_lists);
        echo "<font color=$default_text>This campaign has $active_lists active lists and $inactive_lists inactive lists</font><br><br>";


        if ($display_dialable_count == 'Y') {
            ### call function to calculate and print dialable leads
            dialable_leads($DB,$link,$local_call_time,$dial_statuses,$camp_lists,$fSQL);
            echo " - <font size=1><a href=\"$PHP_SELF?ADD=34&campaign_id=$campaign_id&stage=hide_dialable\">HIDE</a></font><br><br>";
        } else {
            echo "<a href=\"$PHP_SELF?ADD=73&campaign_id=$campaign_id\" target=\"_blank\">Popup Dialable Leads Count</a>";
            echo " - <font size=1><a href=\"$PHP_SELF?ADD=31&campaign_id=$campaign_id&stage=show_dialable\">SHOW</a></font><br><br>";
        }



        $Thopper = get_first_record($link, 'osdial_hopper', 'count(*) AS count', sprintf("campaign_id='%s' AND status IN ('API')",mres($campaign_id)) );
        echo "<font color=$default_text>This campaign has " . $Thopper['count'] . " API leads in the dial hopper<br><br>";

        $Thopper = get_first_record($link, 'osdial_hopper', 'count(*) AS count', sprintf("campaign_id='%s' AND status IN ('READY')",mres($campaign_id)) );
        echo "<font color=$default_text>This campaign has " . $Thopper['count'] . " READY leads in the dial hopper<br><br>";

        echo "<a href=\"$PHP_SELF?ADD=999999&SUB=28&group=$campaign_id\">Click here to see what leads are in the hopper right now</a><br><br>";
        echo "<a href=\"$PHP_SELF?ADD=81&campaign_id=$campaign_id\">Click here to see all CallBack Holds in this campaign</a><br><br>";
        if ($LOG['view_agent_realtime']) echo "<a href=\"$PHP_SELF?useOAC=1&ADD=999999&SUB=12&group=$campaign_id\">Click here to see a Time On Dialer report for this campaign</a></font><br><br>";
        echo "</b></center>";

        echo "<br>";

        ### list of agent rank or skill-level for this campaign
        echo "<center>";
        echo "<br><b><font color=$default_text>AGENT RANKS FOR THIS CAMPAIGN:</font></b><br>";
        echo "<table bgcolor=grey width=400 cellspacing=1>";
        echo "  <tr class=tabheader>";
        echo "    <td align=center>USER</td>";
        echo "    <td align=center>FULL NAME</td>";
        echo "    <td align=center>RANK</td>";
        echo "    <td align=center>CALLS</td>";
        echo "  </tr>";

        if ($LOG['multicomp_admin'] > 0) {
            $stmt=sprintf("SELECT osdial_users.user,full_name,campaign_rank,calls_today FROM osdial_campaign_agents JOIN osdial_users ON (osdial_campaign_agents.user=osdial_users.user) WHERE campaign_id='%s' AND SUBSTRING(osdial_users.user,1,3)=SUBSTRING(campaign_id,1,3) ORDER BY osdial_users.user;",mres($campaign_id));
        } elseif ($LOG['multicomp'] > 0) {
            $stmt=sprintf("SELECT osdial_users.user,full_name,campaign_rank,calls_today FROM osdial_campaign_agents JOIN osdial_users ON (osdial_campaign_agents.user=osdial_users.user) WHERE campaign_id='%s' AND osdial_users.user LIKE '%s__%%' ORDER BY osdial_users.user;",mres($campaign_id),mres($LOG['company_prefix']));
        } else {
            $stmt=sprintf("SELECT osdial_users.user,full_name,campaign_rank,calls_today FROM osdial_campaign_agents JOIN osdial_users ON (osdial_campaign_agents.user=osdial_users.user) WHERE campaign_id='%s' ORDER BY osdial_users.user;",mres($campaign_id));
        }
            $rsltx=mysql_query($stmt, $link);
            $users_to_print = mysql_num_rows($rsltx);

            $o=0;
            while ($users_to_print > $o) {
                $rowx=mysql_fetch_row($rsltx);
                $o++;

            echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=3&user=$rowx[0]');\">";
            echo "    <td><a href=\"$PHP_SELF?ADD=3&user=$rowx[0]\">" . mclabel($rowx[0]) . "</a></td>";
            echo "    <td align=left>$rowx[1]</td>";
            echo "    <td align=right>$rowx[2]</td>";
            echo "    <td align=right>$rowx[3]</td>";
            echo "  </tr>";
            }

        echo "  <tr class=tabfooter><td colspan=4></td></tr>";
        echo "</table>";
        echo "</center><br>";


        echo "<a href=\"$PHP_SELF?ADD=52&campaign_id=$campaign_id\">LOG ALL AGENTS OUT OF THIS CAMPAIGN</a><br><br>";


        if ($LOG['delete_campaigns'] > 0)
            {
            echo "<br><br><a href=\"$PHP_SELF?ADD=51&campaign_id=$campaign_id\">DELETE THIS CAMPAIGN</a>";
            }
        }
    }
    else
    {
    echo "<font color=red>You do not have permission to view this page</font>";
    }
}
*/

######################
# ADD=31 or 34 and SUB=29 for list mixes
######################
# send to not allowed screen if not in osdial_user_groups allowed_campaigns list
if ( ( ($ADD==34) or ($ADD==31) ) and (!OSDpreg_match('/:' . $campaign_id . ':/',$LOG['allowed_campaignsSTR'])) ) {
    $ADD=30;
}

if ( ($ADD==34) or ($ADD==31) ) {

    if ($LOG['modify_campaigns']==1) {
    ##### CAMPAIGN LIST MIX SETTINGS #####
    if ($SUB==29) {
        ##### get list_id listings for dynamic pulldown
        $stmt=sprintf("SELECT list_id,list_name FROM osdial_lists WHERE campaign_id='%s' ORDER BY list_id;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $mixlists_to_print = mysql_num_rows($rslt);
        $mixlists_list="";

        $o=0;
        while ($mixlists_to_print > $o) {
            $rowx=mysql_fetch_row($rslt);
            $mixlists_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
            $mixlistsname_list["$rowx[0]"] = "$rowx[1]";
            $o++;
        }


        echo "<br><font class=top_header color=$default_text size=+1>LIST MIXES FOR THIS CAMPAIGN &nbsp; $NWB#osdial_campaigns-list_order_mix$NWE</font><br><br>\n";
        echo "<table width=$section_width cellspacing=1 cellpadding=0 bgcolor=grey class=row>\n";

        $stmt=sprintf("SELECT * FROM osdial_campaigns_list_mix WHERE campaign_id='%s' ORDER BY status,vcl_id;",mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $listmixes = mysql_num_rows($rslt);
        $o=0;
        while ($listmixes > $o) {
            $rowx=mysql_fetch_row($rslt);
            $vcl_id=$rowx[0];
            $o++;

            echo " <tr>\n";
            echo "  <td align=center>\n";
            echo "<table width=$section_width cellspacing=1 " . bgcolor($o+1) . " class=row>\n";
            echo "  <form action=\"$PHP_SELF#$vcl_id\" method=POST name=$vcl_id id=$vcl_id>\n";
            echo "  <input type=hidden name=DB value=$DB>\n";
            echo "  <input type=hidden name=ADD value=49>\n";
            echo "  <input type=hidden name=SUB value=29>\n";
            echo "  <input type=hidden name=stage value=\"MODIFY\">\n";
            echo "  <input type=hidden name=vcl_id value=\"$vcl_id\">\n";
            echo "  <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
            echo "  <input type=hidden name=list_mix_container$US$vcl_id id=list_mix_container$US$vcl_id value=\"\">\n";
            echo "  <tr>\n";
            echo "    <td colspan=3>Status: <B>$rowx[5]</B>\n";
            if ($rowx[5]=='INACTIVE') {
                echo "<a href=\"$PHP_SELF?ADD=49&SUB=29&stage=SETACTIVE&campaign_id=$campaign_id&vcl_id=$vcl_id\"><font size=1>SET TO ACTIVE</font></a>\n";
            }
            echo "    </td>\n";
            echo "    <td colspan=3 align=right nowrap class=font3>\n";
            echo "      <a href=\"$PHP_SELF?ADD=49&SUB=29&stage=DELMIX&campaign_id=$campaign_id&vcl_id=$vcl_id\">DELETE LIST MIX</a>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr>\n";
            echo "    <td colspan=2 nowrap align=left class=font2>\n";
            echo "      Mix ID: <B>$vcl_id:</B>\n";
            echo "    </td>\n";
            echo "    <td colspan=2 nowrap align=center class=font2>\n";
            echo "      Mix Name: <input type=text size=40 maxlength=50 name=vcl_name$US$vcl_id id=vcl_name$US$vcl_id value=\"$rowx[1]\">\n";
            echo "    </td>\n";
            echo "    <td colspan=2 align=right class=font2>\n";
            echo "      Mix Method: ";
            echo "      <select size=1 name=mix_method$US$vcl_id id=method$US$vcl_id>\n";
            echo "        <option value=\"EVEN_MIX\">EVEN_MIX</option>\n";
            echo "        <option value=\"IN_ORDER\">IN_ORDER</option>\n";
            echo "        <option value=\"RANDOM\">RANDOM</option>\n";
            echo "        <option SELECTED value=\"$rowx[4]\">$rowx[4]</option>\n";
            echo "      </select>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabheader>\n";
            echo "    <td align=center>LIST ID</td>\n";
            echo "    <td align=center>PRIORITY</td>\n";
            echo "    <td align=center>%&nbsp;MIX</td>\n";
            echo "    <td align=center>STATUSES</td>\n";
            echo "    <td align=center>STATUS&nbsp;ACTIONS</td>\n";
            echo "  </tr>\n";

# list_id|order|percent|statuses|:list_id|order|percent|statuses|:...
# 101|1|40| A B NA -|:102|2|25| NEW -|:103|3|30| DROP CALLBK -|:101|4|5| DROP -|
# INSERT INTO osdial_campaigns_list_mix values('TESTMIX','TESTCAMP List Mix','TESTCAMP','101|1|40| A B NA -|:102|2|25| NEW -|:103|3|30| DROP CALLBK -|:101|4|5| DROP -|','IN_ORDER','ACTIVE');
# INSERT INTO osdial_campaigns_list_mix values('TESTMIX2','TESTCAMP List Mix2','TESTCAMP','101|1|20| A B -|:102|2|45| NEW -|:103|3|30| DROP CALLBK -|:101|4|5| DROP -|','IN_ORDER','ACTIVE');
# INSERT INTO osdial_campaigns_list_mix values('TESTMIX3','TESTCAMP List Mix3','TESTCAMP','101|1|30| A NA -|:102|2|35| NEW -|:103|3|30| DROP CALLBK -|:101|4|5| DROP -|','IN_ORDER','ACTIVE');

            $MIXentries = $MT;
            $MIXentries = explode(":", $rowx[3]);
            $Ms_to_print = (count($MIXentries) - 0);
            $q=0;
            while ($Ms_to_print > $q) {
                $MIXdetails = explode('|', $MIXentries[$q]);
                $MIXdetailsLIST = $MIXdetails[0];

                $dial_statuses = OSDpreg_replace("/ -$/","",$dial_statuses);
                $Dstatuses = explode(" ", $dial_statuses);
                $Ds_to_print = (count($Dstatuses) - 0);
                $Dsql = '';
                $r=0;
                while ($Ds_to_print > $r) {
                    $r++;
                    $Dsql .= "'$Dstatuses[$r]',";
                }
                $Dsql = OSDpreg_replace("/,$/","",$Dsql);

                #echo "  <tr " . bgcolor($o) . " class=font2>\n";
                echo "  <tr class=font2>\n";
                echo "    <td NOWRAP>\n";
                echo "      <input type=hidden name=list_id$US$q$US$vcl_id id=list_id$US$q$US$vcl_id value=$MIXdetailsLIST>\n";
                echo "      <a href=\"$PHP_SELF?ADD=311&list_id=$MIXdetailsLIST\">List: $MIXdetailsLIST</a> &nbsp; ";
                echo "      <a href=\"$PHP_SELF?ADD=49&SUB=29&stage=REMOVE&campaign_id=$campaign_id&vcl_id=$vcl_id&mix_container_item=$q&list_id=$MIXdetailsLIST#$vcl_id\">REMOVE</a>\n";
                echo "    </td>\n";

                echo "    <td align=center>\n";
                echo "      <select size=1 name=priority$US$q$US$vcl_id id=priority$US$q$US$vcl_id>\n";
                $n=10;
                while ($n>=1) {
                    echo "        <option value=\"$n\">$n</option>\n";
                    $n = ($n-1);
                }
                echo "        <option SELECTED value=\"$MIXdetails[1]\">$MIXdetails[1]</option>\n";
                echo "      </select>\n";
                echo "    </td>\n";

                echo "    <td align=center>\n";
                echo "      <select size=1 name=\"percentage$US$q$US$vcl_id\" id=\"percentage$US$q$US$vcl_id\" onChange=\"mod_mix_percent('$vcl_id','$Ms_to_print')\">\n";
                $n=100;
                while ($n>=0) {
                    echo "        <option value=\"$n\">$n</option>\n";
                    $n = ($n-5);
                }
                echo "        <option SELECTED value=\"$MIXdetails[2]\">$MIXdetails[2]</option>\n";
                echo "      </select>\n";
                echo "    </td>\n";

                
                echo "    <td align=center>\n";
                echo "      <input type=hidden name=status$US$q$US$vcl_id id=status$US$q$US$vcl_id value=\"$MIXdetails[3]\">\n";
                echo "      <input type=text size=30 maxlength=255 name=ROstatus$US$q$US$vcl_id id=ROstatus$US$q$US$vcl_id value=\"$MIXdetails[3]\" READONLY>\n";
                echo "    </td>\n";

                echo "    <td nowrap>\n";
                echo "      <select size=1 name=dial_status$US$q$US$vcl_id id=dial_status$US$q$US$vcl_id>\n";
                echo "        <option value=\"\"> - Select A Status - </option>\n";
                echo "        $dial_statuses_list";
                echo "      </select>\n";
                echo "      <b>\n";
                echo "        <a href=\"#\" onclick=\"mod_mix_status('ADD','$vcl_id','$q');return false;\">ADD</a> &nbsp; ";
                echo "        <a href=\"#\" onclick=\"mod_mix_status('REMOVE','$vcl_id','$q');return false;\">REMOVE</a>\n";
                echo "      </b>\n";
                echo "    </td>\n";

                echo "  </tr>\n";

                $q++;

            }

            echo "  <tr class=font2>\n";
            echo "    <td colspan=3 align=right>Difference %: <input type=text size=4 name=PCT_DIFF_$vcl_id id=PCT_DIFF_$vcl_id value=0 readonly></td>\n";
            echo "    <td colspan=2 class=tabbutton><input type=button name=submit_$vcl_id id=submit_$vcl_id value=\"SAVE LISTMIX RULES\" onClick=\"submit_mix('$vcl_id','$Ms_to_print')\"></td>\n";
            echo "  </tr>\n";
            echo "  </form>\n";
            echo "  <tr class=font2>\n";
            echo "    <td colspan=5>&nbsp;<span id=ERROR_$vcl_id></span></td>\n";
            echo "  </tr>\n";


            echo "  <form action=\"$PHP_SELF#$vcl_id\" method=POST name=$vcl_id id=$vcl_id>\n";
            echo "  <input type=hidden name=DB value=$DB>\n";
            echo "  <input type=hidden name=ADD value=49>\n";
            echo "  <input type=hidden name=SUB value=29>\n";
            echo "  <input type=hidden name=stage value=\"ADD\">\n";
            echo "  <input type=hidden name=vcl_id value=\"$vcl_id\">\n";
            echo "  <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
            echo "  <tr class=font2>\n";
            echo "    <td colspan=4 align=right>\n";
            echo "      List: ";
            echo "      <select size=1 name=list_id>\n";
            echo "        $mixlists_list";
            echo "        <option selected value=\"\">ADD ANOTHER LIST</option>\n";
            echo "      </select>\n";
            echo "    </td>\n";
            
            if ($q > 9) {
                $AE_disabled = 'DISABLED';
            } else {
                $AE_disabled = '';
            }
            echo "    <td class=tabbutton><input type=submit name=submit value=\"ADD LIST\" $AE_disabled></td>\n";
            echo "  </tr>\n";
            echo "  </form>\n";
            echo "</table>\n";

            echo "  </td>\n";
            echo " </tr>\n";
        }

        echo " <tr bgcolor=$evenrows>\n";
        echo "  <td align=center>\n";
        echo "<br><b><font color=$default_text>ADD NEW LIST MIX</font></b><br><br />\n";
        #echo "<form action=$PHP_SELF method=POST>\n";
        echo "<form action=\"$PHP_SELF#$vcl_id\" method=POST>\n";
        echo " <input type=hidden name=DB value=$DB>\n";
        echo " <input type=hidden name=ADD value=49>\n";
        echo " <input type=hidden name=SUB value=29>\n";
        echo " <input type=hidden name=stage value=\"NEWMIX\">\n";
        echo " <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
        echo " <table border=0 width=$section_width cellpadding=0 cellspacing=1>\n";
        echo "  <tr class=tabheader>\n";
        echo "    <td align=center>ID</td>\n";
        echo "    <td align=center>Name</td>\n";
        echo "    <td align=center>Method</td>\n";
        echo "    <td align=center>List</td>\n";
        echo "    <td align=center>Status</td>\n";
        echo "    <td align=center>Actions</td>\n";
        echo "  </tr>\n";
        echo "  <tr class=tabfooter>\n";
        echo "    <td align=center class=tabinput><input type=text size=20 maxlength=20 name=vcl_id value=\"\"></td>\n";
        echo "    <td align=center class=tabinput><input type=text size=30 maxlength=50 name=vcl_name value=\"\"></td>\n";
        echo "    <td align=center class=tabinput><select size=1 name=mix_method><option value=\"EVEN_MIX\">EVEN_MIX</option><option value=\"IN_ORDER\">IN_ORDER</option><option value=\"RANDOM\">RANDOM</option></select></td>\n";
        echo "    <td align=center class=tabinput><select size=1 name=list_id>$mixlists_list</select></td>\n";
        echo "    <td align=center class=tabinput><select size=1 name=status>$dial_statuses_list</select></td>\n";
        echo "    <td align=center class=tabbutton1><input type=submit name=submit value=\"ADD NEW MIX\"></td>\n";
        echo "  </tr>\n";
        echo " </table>\n";
        echo "</form>\n";

        echo "  </td>\n";
        echo " </tr>\n";
        echo "</table>\n";

        echo "<br>\n";

        }
    }

}



######################
# ADD=30 campaign not allowed
######################
if ($ADD==30) {
    echo "<font color=red>You do not have permission to view campaign $campaign_id</font>";
}



######################
# ADD=81 find all callbacks on hold within a Campaign
######################
if ($ADD==81) {
    if ($LOG['modify_campaigns']==1) {
        if ($SUB==89) {
            $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE campaign_id='%s' AND status='LIVE' AND callback_time<'%s';",mres($campaign_id),mres($past_month_date));
            $rslt=mysql_query($stmt, $link);
            echo "<br>campaign($campaign_id) callback listings LIVE for more than one month have been made INACTIVE";
        }
        if ($SUB==899) {
            $stmt=sprintf("UPDATE osdial_callbacks SET status='INACTIVE' WHERE campaign_id='%s' AND status='LIVE' AND callback_time<'%s';",mres($campaign_id),mres($past_month_date));
            $rslt=mysql_query($stmt, $link);
            echo "<br>campaign($campaign_id) callback listings LIVE for more than one week have been made INACTIVE";
        }
    }
    $CBinactiveLINK = "<br><a href=\"$PHP_SELF?ADD=81&SUB=89&campaign_id=$campaign_id\"><font color=$default_text>Remove LIVE Callbacks older than one month for this campaign</font></a><br><a href=\"$PHP_SELF?ADD=81&SUB=899&campaign_id=$campaign_id\"><font color=$default_text>Remove LIVE Callbacks older than one week for this campaign</font></a><br>";

    $CBquerySQLwhere = "and campaign_id='$campaign_id'";

    echo "<br><br><center><font color=$default_text size=4>CAMPAIGN CALLBACK HOLD LISTINGS: $campaign_id</font></center>";
    $oldADD = "ADD=81&campaign_id=$campaign_id";
    $ADD='82';
    include($WeBServeRRooT . "/admin/include/content/lists/lists.php");
}



######################
# ADD=10 display all campaigns
######################
if ($ADD==10) {

	$let = get_variable('let');
	$letSQL = '';
	if ($let != '') $letSQL = sprintf("AND campaign_id LIKE '%s%s%%'",$LOG['company_prefix'],$let);

	$dispact = get_variable('dispact');
	$dispactSQL = '';
	if ($dispact == 1) $dispactSQL = "AND active='Y'";

		$stmt=sprintf("SELECT * FROM osdial_campaigns WHERE campaign_id IN %s %s %s ORDER BY campaign_id;",$LOG['allowed_campaignsSQL'],$letSQL,$dispactSQL);

		$rslt=mysql_query($stmt, $link);
		$people_to_print = mysql_num_rows($rslt);

	echo "<center><br><font class=top_header color=$default_text size=+1>CAMPAIGNS</font><br>";
	if ($people_to_print > 20) {
		echo "<center><font color=$default_text size=-1>";
		if ($dispact == '1') {
			echo "<a href=\"$PHP_SELF?ADD=10&let=$let&dispact=\">(Show Inactive)</a>";
		} else {
			echo "<a href=\"$PHP_SELF?ADD=10&let=$let&dispact=1\">(Hide Inactive)</a>";
		}
		echo "</font><br><br>";
	}
	echo "<br>";
	echo "<center><font size=-1 color=$default_text>&nbsp;|&nbsp;";
	echo "<a href=\"$PHP_SELF?ADD=$ADD&dispact=$dispact&let=\">-ALL-</a>&nbsp;|&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;";
	foreach (range('A','Z') as $slet) {
		echo (($let == "$slet") ? $slet : "<a href=\"$PHP_SELF?ADD=$ADD&dispact=$dispact&let=$slet\">$slet</a>") . "&nbsp;|&nbsp;";
	}
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;";
	foreach (range('0','9') as $snum) {
		echo (($let == "$snum") ? $snum : "<a href=\"$PHP_SELF?ADD=$ADD&dispact=$dispact&let=$snum\">$snum</a>") . "&nbsp;|&nbsp;";
	}
	echo "</font><br>";

	echo "<table width=$section_width cellspacing=0 cellpadding=1>";
	echo "  <tr class=tabheader>";
	echo "    <td>ID</td>";
	echo "    <td>DESCRIPTION</td>";
	echo "    <td align=center>ACTIVE</td>";
	echo "    <td align=center colspan=7>LINKS</td>";
	echo "  </tr>";
		$o=0;
		while ($people_to_print > $o) {
			$row=mysql_fetch_row($rslt);
			echo "  <tr class=\"row font1\" " . bgcolor($o) ." ondblclick=\"window.location='$PHP_SELF?ADD=34&campaign_id=$row[0]';\">";
			echo "    <td><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[0]\">" . mclabel($row[0]) . "</a></td>";
			echo "    <td>$row[1]</td>";
			echo "    <td align=center><font size=1>$row[2]</td>";
			echo "    <td colspan=7 align=center><a href=\"$PHP_SELF?ADD=31&campaign_id=$row[0]\">MODIFY</a></td>";
			echo "  </tr>";
			$o++;
		}
	echo "  <tr class=tabfooter>";
	echo "    <td colspan=10></td>";
	echo "  </tr>";
	echo "</table></center>";
}



?>
