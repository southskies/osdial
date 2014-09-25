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
# 090511-2123 - Added status_category_hour_counts
# 090609-0230 - Added INBOUND and OUTBOUND campaign selections



function report_realtime_summary() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $html = '';
    $Ahtml = '';

    $pref="";

    $NOW_TIME = date("Y-m-d H:i:s");
    $STARTtime = date("U");

    $activeSQL = '1=1';
    if ($active_only=='Y') $activeSQL = "active='Y'";

    $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE %s AND campaign_id IN %s;",$activeSQL,$LOG['allowed_campaignsSQL']);
    $rslt=mysql_query($stmt, $link);
    if (!isset($DB)) $DB=0;
    if ($DB) $html .= "$stmt\n";

    $groups_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $groups_to_print)    {
        $row=mysql_fetch_row($rslt);
        $groups[$i] = $row[0];
        $group_names[$i] = $row[1];
        $i++;
    }
    
    if (!empty($useOAC) and empty($RR)) $RR=2;
    if (empty($RR)) $RR=4;
    if ($RR==0) $RR=4;
    
    
    $html .= "<font size=1>";
    $html .= "<div class=no-ul>";
    
    $html .= "<form action=$PHP_SELF method=POST>\n";
    $html .= "<input type=hidden name=useOAC value=$useOAC>\n";
    $html .= "<input type=hidden name=ADD value=$ADD>\n";
    $html .= "<input type=hidden name=SUB value=$SUB>\n";
    $html .= "<input type=hidden name=adastats value=$adastats\n";
    $html .= "<input type=hidden name=group value=$group>\n";
    $html .= "<input type=hidden name=campaign_id value=$campaign_id>\n";
    $html .= "<input type=hidden name=RR value=$RR>\n";
    $html .= "<input type=hidden name=cpuinfo value=$cpuinfo>\n";
    $html .= "<input type=hidden name=active_only value=$active_only>\n";
    
    $html .= "<br><p class=centered><font class=top_header color=$default_text size=+1>ALL CAMPAIGNS SUMMARY</font><br><br>";
    $html .= "<font color=$default_text size=-1>Update:&nbsp;";
    if ($RR==38400) $html .= "<font size=+1>";
    $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=38400&DB=$DB&adastats=$adastats&cpuinfo=$cpuinfo&active_only=$active_only\">Daily</a>&nbsp;&nbsp;";
    if ($RR==3600) {
        $html .= "<font size=+1>";
    } else {
        $html .= "<font size=-1>";
    }
    $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=3600&DB=$DB&adastats=$adastats&cpuinfo=$cpuinfo&active_only=$active_only\">Hourly</a>&nbsp;&nbsp;";
    if ($RR==600) {
        $html .= "<font size=+1>";
    } else {
        $html .= "<font size=-1>";
    }
    $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=600&DB=$DB&adastats=$adastats&cpuinfo=$cpuinfo&active_only=$active_only\">10min</a>&nbsp;&nbsp;";
    if ($RR==30) {
        $html .= "<font size=+1>";
    } else {
        $html .= "<font size=-1>";
    }
    $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=30&DB=$DB&adastats=$adastats&cpuinfo=$cpuinfo&active_only=$active_only\">30sec</a>&nbsp;&nbsp;";
    if ($RR==4) {
        $html .= "<font size=+1>";
    } else {
        $html .= "<font size=-1>";
    }
    $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=4&DB=$DB&adastats=$adastats&cpuinfo=$cpuinfo&active_only=$active_only\">4sec</a>&nbsp;&nbsp;";
    if (!empty($useOAC)) {
        if ($RR==2) {
            $html .= "<font size=+1>";
        } else {
            $html .= "<font size=-1>";
        }
        $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=2&DB=$DB&adastats=$adastats&cpuinfo=$cpuinfo&active_only=$active_only\">2sec</a>&nbsp;&nbsp;";
    }
    $html .= "</font>";
    $html .= "&nbsp;-&nbsp;&nbsp;";
    if ($adastats<2) {
        $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=2&cpuinfo=$cpuinfo&active_only=$active_only\"><font size=1>VIEW MORE SETTINGS</font></a>";
    } else {
        $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=1&cpuinfo=$cpuinfo&active_only=$active_only\"><font size=1>VIEW LESS SETTINGS</font></a>";
    }
    // Hide inactive campaigns by default 
    if ($active_only=='N') {
        $html .= "<font size=2>&nbsp;-&nbsp;<a href='$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=1&cpuinfo=$cpuinfo&active_only=Y'>Hide Inactive Campaigns</a></font>";
    } else {
        $html .= "<font size=2>&nbsp;-&nbsp;<a href='$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=1&cpuinfo=$cpuinfo&active_only=N'>Show Inactive Campaigns</a></font>";
    }
    $html .= "</p>\n\n";
    
    $k=0;
    while($k<$groups_to_print) {
//         $NFB = '<b><font size=3 face="courier">';
//         $NFE = '</font></b>';
        $F=''; $FG=''; $B=''; $BG='';
        
        $html .= "<table align=center border=0 cellpadding=0 cellspacing=0 class=shadedtable style='background:#EEE;margin-top:2px;' width=800><tr><td colspan=8>";
        $group = $groups[$k];
        $group_name = $group_names[$k];
        $rdlink = mclabel($group) . " - $group_name";
        if ($LOG['view_agent_realtime']) $rdlink = "<a href=\"./admin.php?useOAC=$useOAC&ADD=$ADD&SUB=" . ($SUB + 1) . "&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=$adastats&cpuinfo=$cpuinfo&active_only=$active_only\">" . mclabel($group) . " - $group_name</a>";
        $html .= "<div style='margin:5px 0 10px 5px;'><font size=-1><b>$rdlink</b> &nbsp; - &nbsp; ";
        $html .= "<a href=\"./admin.php?ADD=31&campaign_id=$group\">Modify Campaign</a></div> </font>\n";
        
        if ($LOG['multicomp']>0) {
            $tcnt=0;
            $comp = get_first_record($link, 'osdial_companies', "*,IF(acct_enddate!='0000-00-00 00:00:00',DATEDIFF(acct_enddate,NOW()),0) AS daystillend", sprintf("id='%s'",mres((OSDsubstr($group,0,3)*1)-100)) );
            $thtml = "<table frame=border><tr><td>";
            if ($comp['acct_method'] != 'NONE' and $comp['acct_method'] != '' and $comp['acct_method'] != 'RANGE') {
                if (($comp['acct_cutoff']*60)>=$comp['acct_remaining_time'] || ($config['settings']['acct_email_warning_time']*60)>=$comp['acct_remaining_time']) {
                    $thtml .= "<font size=1>Credit Left:</font><font size=1 color=#600> <b>".($comp['acct_remaining_time']/60)." min</b>&nbsp;&nbsp;&nbsp;&nbsp;</font>";
                } else {
                    $thtml .= "<font size=1>Credit Left:</font><font size=1 color=#060> ".($comp['acct_remaining_time']/60)." min&nbsp;&nbsp;&nbsp;&nbsp;</font>";
                }
                $tcnt++;
            }
            if ($comp['acct_enddate'] != '0000-00-00 00:00:00' and $comp['acct_enddate'] != '') {
                if ($config['settings']['acct_email_warning_expire']>=$comp['daystillend']) {
                    $thtml .= "<font size=1>Days Left:</font><font size=1 color=#600> <b>".$comp['daystillend']."</b></font>";
                } else {
                    $thtml .= "<font size=1>Days Left:</font><font size=1 color=#060> ".$comp['daystillend']."</font>";
                }
                $tcnt++;
            }
            $thtml .= "</td></tr></table>";
            if ($tcnt>0) $html.=$thtml;
        }
        
        $stmt = sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_id IN %s AND campaign_id='%s' and campaign_allow_inbound='Y';",$LOG['allowed_campaignsSQL'],mres($group));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $campaign_allow_inbound = $row[0];
        
        $stmt=sprintf("SELECT auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout,dial_statuses FROM osdial_campaigns WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $DIALlev =      $row[0];
        $DIALstatusA =  $row[1];
        $DIALstatusB =  $row[2];
        $DIALstatusC =  $row[3];
        $DIALstatusD =  $row[4];
        $DIALstatusE =  $row[5];
        $DIALorder =    $row[6];
        $DIALfilter =   $row[7];
        $HOPlev =       $row[8];
        $DIALmethod =   $row[9];
        $maxDIALlev =   $row[10];
        $DROPmax =      $row[11];
        $targetDIFF =   $row[12];
        $ADAintense =   $row[13];
        $ADAavailonly = $row[14];
        $TAPERtime =    $row[15];
        $CALLtime =     $row[16];
        $DIALtimeout =  $row[17];
        $DIALstatuses = $row[18];
        $DIALstatuses = OSDpreg_replace("/ -$|^ /","",$DIALstatuses);
        $DIALstatuses = OSDpreg_replace('/ /',', ',$DIALstatuses);
        
        $stmt=sprintf("SELECT count(*) FROM osdial_hopper WHERE campaign_id IN %s AND campaign_id='%s' AND status='READY';",$LOG['allowed_campaignsSQL'],mres($group));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $VDhop = $row[0];
        
        $stmt=sprintf("SELECT dialable_leads,calls_today,drops_today,drops_answers_today_pct,differential_onemin,agents_average_onemin,balance_trunk_fill,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4,status_category_hour_count_1,status_category_hour_count_2,status_category_hour_count_3,status_category_hour_count_4,recycle_total,recycle_sched FROM osdial_campaign_stats WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $DAleads =          $row[0];
        $callsTODAY =       $row[1];
        $dropsTODAY =       $row[2];
        $drpctTODAY =       $row[3];
        $diffONEMIN =       $row[4];
        $agentsONEMIN =     $row[5];
        $balanceFILL =      $row[6];
        $answersTODAY =     $row[7];
        $VSCcat1 =          $row[8];
        $VSCcat1tally =     $row[9];
        $VSCcat2 =          $row[10];
        $VSCcat2tally =     $row[11];
        $VSCcat3 =          $row[12];
        $VSCcat3tally =     $row[13];
        $VSCcat4 =          $row[14];
        $VSCcat4tally =     $row[15];
        $VSCcat1hourtally = $row[16];
        $VSCcat2hourtally = $row[17];
        $VSCcat3hourtally = $row[18];
        $VSCcat4hourtally = $row[19];
        $recycle_total =    $row[20];
        $recycle_sched =    $row[21];
        
        if ($diffONEMIN != 0 and $agentsONEMIN > 0) {
            $diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
            $diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
        } else {
            $diffpctONEMIN = '0.00';
        }
        
        $stmt=sprintf("SELECT sum(local_trunk_shortage) FROM osdial_campaign_server_stats WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $balanceSHORT = $row[0];
        
        
		$html .= "</td></tr>";
        $html .= "<tr>";
        $html .= "<td align=right colspan=1><font size=2 color=$default_text><b>Statuses:</b></font></td><td align=left colspan=7><font size=2>&nbsp; <span title=\"$DIALstatuses\">" . ellipse($DIALstatuses,110,true) . "</span>&nbsp;&nbsp;</font></td>";
        $html .= "</tr>";

        if ($balanceSHORT=='') {
			$balanceSHORT=0;
		}
        $html .= "<td align=right><font size=2 color=$default_text><b>Dial Level:</b></font></td><td align=left><font size=2>&nbsp; $DIALlev&nbsp; &nbsp; </font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Trunk Short/Fill:</b></font></td><td align=left><font size=2>&nbsp; $balanceSHORT / $balanceFILL &nbsp; &nbsp; </font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Filter:</b></font></td><td align=left><font size=2>&nbsp; $DIALfilter &nbsp; </font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Time:</b></font></td><td align=left><font size=2 color=$default_text>&nbsp; " . dateToLocal($link,'first',$NOW_TIME,$webClientAdjGMT,'',$webClientDST,1) . " </font></td>";
        $html .= "";
        $html .= "</tr>";

        if ($adastats>1) {
            $html .= "<tr bgcolor=\"#CCCCCC\">";
            $html .= "<td align=right><font size=2>&nbsp; <b>Max Level:</b></font></td><td align=left><font size=2>&nbsp; $maxDIALlev &nbsp; </font></td>";
            $html .= "<td align=right><font size=2><b>Dropped Max:</b></td></font><td align=left><font size=2>&nbsp; $DROPmax% &nbsp; &nbsp;</font></td>";
            $html .= "<td align=right><font size=2><b>Target Diff:</b></td></font><td align=left><font size=2>&nbsp; $targetDIFF &nbsp; &nbsp; </font></td>";
            $html .= "<td align=right><font size=2><b>Intensity:</b></font></td><td align=left><font size=2>&nbsp; $ADAintense &nbsp; &nbsp; </font></td>";
            $html .= "</tr>";

            $html .= "<tr bgcolor=\"#CCCCCC\">";
            $html .= "<td align=right><font size=2><b>Dial Timeout:</b></font></td><td align=left><font size=2>&nbsp; $DIALtimeout &nbsp;</font></td>";
            $html .= "<td align=right><font size=2><b>Taper Time:</b></font></td><td align=left><font size=2>&nbsp; $TAPERtime &nbsp;</font></td>";
            $html .= "<td align=right><font size=2><b>Local Ttime</b></font></td><td align=left><font size=2>&nbsp; $CALLtime &nbsp;</font></td>";
            $html .= "<td align=right colspan=2><font size=2>&nbsp;</font></td>";
            $html .= "</tr>";

            $html .= "<tr bgcolor=\"#CCCCCC\">";
            $html .= "<td align=right><font size=2><b>DL Diff:</b></font></td><td align=left><font size=2>&nbsp; $diffONEMIN &nbsp; &nbsp; </font></td>";
            $html .= "<td align=right><font size=2><b>Diff:</b></td></font><td align=left><font size=2>&nbsp; $diffpctONEMIN% &nbsp; &nbsp; </font></td>";
            $html .= "<td align=right><font size=2><b>Avg Agents:</b></font></td><td align=left><font size=2>&nbsp; $agentsONEMIN &nbsp; &nbsp; </font></td>";
            $html .= "</tr>";
        }
		
        $html .= "<tr>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Dialable Leads:</b></font></td><td align=left><font size=2>&nbsp; $DAleads &nbsp; &nbsp; </font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Recycles/Sched:</b></font></td><td align=left><font size=2>&nbsp; $recycle_total / $recycle_sched &nbsp; &nbsp; </font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Calls Today:</b></font></td><td align=left><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Dial Method:</b></font></td><td align=left><font size=2>&nbsp; $DIALmethod </font> &nbsp; &nbsp; </td>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Hopper Level:</b></font></td><td align=left><font size=2>&nbsp; $HOPlev&nbsp;&nbsp;</font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Leads In Hopper:</b></font></td><td align=left><font size=2>&nbsp; $VDhop&nbsp;&nbsp;</font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Drop/Answer:</b></font></td><td align=left><font size=2>&nbsp; $dropsTODAY / $answersTODAY&nbsp;&nbsp;</font></td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Drop %:</b></font></td><td align=left><font size=2>&nbsp; ";
        if ($drpctTODAY >= $DROPmax) {
            $html .= "<font color=red><b>$drpctTODAY%</b></font>";
        } else {
            $html .= "$drpctTODAY%";
        }
        $html .= "&nbsp;&nbsp;</td>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Order:</b></td><td align=left><font size=2>&nbsp; $DIALorder&nbsp;&nbsp;</td><td colspan=2><font size=2>&nbsp;</font></td><td align=right><font size=2 color=$default_text><b>Avail Only:</b></font></td><td align=left colspan=3><font size=2>&nbsp; $ADAavailonly&nbsp;&nbsp;</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        if (!OSDpreg_match('/NULL/',$VSCcat1) and OSDstrlen($VSCcat1)>0) {
            $html .= "<td align=right><font size=2 color=$default_text><B>".get_status_category_ucwords($VSCcat1).":</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1tally&nbsp;&nbsp;&nbsp;</td>\n";
        }
        if (!OSDpreg_match('/NULL/',$VSCcat2) and OSDstrlen($VSCcat2)>0) {
            $html .= "<td align=right><font size=2 color=$default_text><B>".get_status_category_ucwords($VSCcat2).":</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2tally&nbsp;&nbsp;&nbsp;</td>\n";
        }
        if (!OSDpreg_match('/NULL/',$VSCcat3) and OSDstrlen($VSCcat3)>0) { 
            $html .= "<td align=right><font size=2 color=$default_text><B>".get_status_category_ucwords($VSCcat3).":</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3tally&nbsp;&nbsp;&nbsp;</td>\n";
        }
        if (!OSDpreg_match('/NULL/',$VSCcat4) and OSDstrlen($VSCcat4)>0) {
            $html .= "<td align=right><font size=2 color=$default_text><B>".get_status_category_ucwords($VSCcat4).":</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4tally&nbsp;&nbsp;&nbsp;</td>\n";
        }
        $html .= "</tr><tr>";
        if (!OSDpreg_match('/NULL/',$VSCcat1) and OSDstrlen($VSCcat1)>0) {
            $html .= "<td align=right><font size=2 color=$default_text><B>".get_status_category_ucwords($VSCcat1)."/Hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1hourtally&nbsp;&nbsp;&nbsp;</td>\n";
        }
        if (!OSDpreg_match('/NULL/',$VSCcat2) and OSDstrlen($VSCcat2)>0) {
            $html .= "<td align=right><font size=2 color=$default_text><B>".get_status_category_ucwords($VSCcat2)."/Hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2hourtally&nbsp;&nbsp;&nbsp;</td>\n";
        }
        if (!OSDpreg_match('/NULL/',$VSCcat3) and OSDstrlen($VSCcat3)>0) { 
            $html .= "<td align=right><font size=2 color=$default_text><B>".get_status_category_ucwords($VSCcat3)."/Hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3hourtally&nbsp;&nbsp;&nbsp;</td>\n";
        }
        if (!OSDpreg_match('/NULL/',$VSCcat4) and OSDstrlen($VSCcat4)>0) {
            $html .= "<td align=right><font size=2 color=$default_text><B>".get_status_category_ucwords($VSCcat4)."/Hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4hourtally&nbsp;&nbsp;&nbsp;</td>\n";
        }
        
        $html .= "</tr>";
        
        $html .= "<tr>";
        $html .= "<td align=center colspan=8>";
        
        ### Header finish
        
        
        
        
        
        ################################################################################
        ### START calculating calls/agents
        ################################################################################
        
        ################################################################################
        ###### OUTBOUND CALLS
        ################################################################################
        if ($campaign_allow_inbound > 0) {
            $stmt=sprintf("SELECT closer_campaigns FROM osdial_campaigns WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $closer_campaigns = OSDpreg_replace("/^ | -$/","",$row[0]);
            $closer_campaigns = OSDpreg_replace("/ /","','",$closer_campaigns);
            $closer_campaigns = "'$closer_campaigns'";
        
            $stmt=sprintf("SELECT ac.status FROM osdial_auto_calls AS ac LEFT JOIN osdial_live_agents AS la ON (ac.channel=la.channel) WHERE ac.campaign_id IN %s AND (ac.status NOT IN('XFER') OR la.conf_exten LIKE '870_____') AND ( (ac.call_type='IN' AND ac.campaign_id IN($closer_campaigns)) OR (ac.campaign_id='%s' AND ac.call_type='OUT') );",$LOG['allowed_campaignsSQL'],mres($group));
        } else {
            if ($group=='XXXX-ALL-ACTIVE-XXXX') { 
                $groupSQL = '';
            } elseif ($group=='XXXX-OUTBOUND-XXXX') { 
                $groupSQL = ' and length(ac.closer_campaigns)<6';
            } elseif ($group=='XXXX-INBOUND-XXXX') { 
                $groupSQL = ' and length(ac.closer_campaigns)>5';
            } else {
                $groupSQL = sprintf(" AND ac.campaign_id='%s'",mres($group));
            }
        
            $stmt=sprintf("SELECT ac.status FROM osdial_auto_calls AS ac LEFT JOIN osdial_live_agents AS la ON (ac.channel=la.channel) WHERE ac.campaign_id IN %s AND (ac.status NOT IN('XFER') OR la.conf_exten LIKE '870_____') %s;",$LOG['allowed_campaignsSQL'],$groupSQL);
        }
        $rslt=mysql_query($stmt, $link);
        
        if ($DB) $html .= "$stmt\n";
        
        $parked_to_print = mysql_num_rows($rslt);
        if ($parked_to_print > 0) {
            $i=0;
            $out_total=0;
            $out_ring=0;
            $out_live=0;
            while ($i < $parked_to_print) {
                $row=mysql_fetch_row($rslt);
        
                if (OSDpreg_match("/LIVE/",$row[0])) {
                    $out_live++;
                } else {
                    if (OSDpreg_match("/CLOSER|XFER/",$row[0])) {
                        $nothing=1;
                    } else {
                        $out_ring++;
                    }
                }
                $out_total++;
                $i++;
            }
    
            if ($out_live > 0) {
                $F='<font class="r1">';
                $FG='</font>';
            }
            if ($out_live > 4) {
                $F='<font class="r2">';
                $FG='</font>';
            }
            if ($out_live > 9) {
                $F='<font class="r3">';
                $FG='</font>';
            }
            if ($out_live > 14) {
                $F='<font class="r4">';
                $FG='</font>';
            }
    		$html .= "$NFB$out_total$NFE&nbsp; <font color=blue>Active calls</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";

            $html .= "$NFB$out_ring$NFE&nbsp; <font color=blue>Calls ringing</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
            if ($out_live == 0) {
                $html .= "<span style=margin-left:250>&nbsp;</span> \n";
            } elseif ($out_live == 1) {
                $html .= "$NFB<font color=red> &nbsp;$out_live </font>$NFE <font color=blue>Call is waiting for an agent</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div> \n";
            } else {
                $html .= "$NFB<font color=red> &nbsp;$out_live </font>$NFE <font color=blue>Calls are waiting for agents</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div> \n";
            }
            
        } else {
            $html .= "<font color=red>&nbsp;";
            if ($DIALmethod!='MANUAL') {
                $html .= "NO LIVE CALLS WAITING";
            }
            $html .= "</font>&nbsp;\n";
        }
        
        
        ###################################################################################
        ###### TIME ON SYSTEM
        ###################################################################################
        
        $agent_incall=0;
        $agent_ready=0;
        $agent_paused=0;
        $agent_total=0;
        
        $stmt=sprintf("SELECT extension,user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id FROM osdial_live_agents WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
        $rslt=mysql_query($stmt, $link);
        if ($DB) $html .= "$stmt\n";

        $talking_to_print = mysql_num_rows($rslt);
        if ($talking_to_print > 0) {
            $i=0;
            $agentcount=0;
            while ($i < $talking_to_print) {
                $row=mysql_fetch_row($rslt);
                if (OSDpreg_match("/READY|PAUSED/",$row[3])) $row[5]=$row[6];
                $Lstatus = $row[3];
                $status = sprintf("%-6s", $row[3]);
                if (!OSDpreg_match("/INCALL|QUEUE/",$row[3])) {
                    $call_time_S = ($STARTtime - $row[6]);
                } else {
                    $call_time_S = ($STARTtime - $row[5]);
                }
        
                $call_time_M = ($call_time_S / 60);
                $call_time_M = round($call_time_M, 2);
                $call_time_M_int = intval("$call_time_M");
                $call_time_SEC = ($call_time_M - $call_time_M_int);
                $call_time_SEC = ($call_time_SEC * 60);
                $call_time_SEC = round($call_time_SEC, 0);
                if ($call_time_SEC < 10) $call_time_SEC = "0$call_time_SEC";
                $call_time_MS = "$call_time_M_int:$call_time_SEC";
                $call_time_MS = sprintf("%7s", $call_time_MS);
                $G = '';
                $EG = '';
                if (OSDpreg_match("/PAUSED/",$row[3])) {
                    if ($call_time_M_int >= 30) {
                        $i++;
                        continue;
                    } else {
                        $agent_paused++;
                        $agent_total++;
                    }
                }
        
                if (OSDpreg_match("/INCALL/",$status) or OSDpreg_match("/QUEUE/",$status)) {
                    $agent_incall++;
                    $agent_total++;
                }
                if (OSDpreg_match("/READY/",$status) or OSDpreg_match("/CLOSER/",$status)) {
                    $agent_ready++;
                    $agent_total++;
                }
                $agentcount++;
        
        
                $i++;
            }
        
            if ($agent_ready > 0) {
                $B='<font class="b1">';
                $BG='</font>';
            }
            if ($agent_ready > 4) {
                $B='<font class="b2">';
                $BG='</font>';
            }
            if ($agent_ready > 9) {
                $B='<font class="b3">';
                $BG='</font>';
            }
            if ($agent_ready > 14) {
                $B='<font class="b4">';
                $BG='</font>';
            }
    
            $html .= "\n<br/>\n";
          
            $html .= "<div style='position:relative;left:0px;top:0px;text-shadow: rgba(0,0,0,0.3) 1px -1px 4px;'>$NFB$agent_total$NFE <font color=blue>&nbsp;&nbsp;Agents logged in</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";

            if ($agent_incall == 0) {
            	$html .= " $NFB$agent_incall$NFE&nbsp; <font color=blue>&nbsp;No Agents in a call</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
        	} elseif ($agent_incall == 1) {
                $html .= " $NFB$agent_incall$NFE&nbsp; <font color=blue>Agent in a call</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
        	} else {
                $html .= " $NFB$agent_incall$NFE&nbsp; <font color=blue>Agents in calls</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
        	}
        	if ($agent_ready == 0) {
                $html .= " &nbsp;$NFB$B$agent_ready$BG$NFE&nbsp; <font color=blue>Agents waiting</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
        	} elseif ($agent_ready == 1) {
          		$html .= " &nbsp;$NFB$B$agent_ready$BG$NFE&nbsp; <font color=blue>Agent waiting</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
        	} else {
                $html .= " &nbsp;$NFB$B$agent_ready$BG$NFE&nbsp; <font color=blue>Agents waiting</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
        	}
        	if ($agent_paused == 0) {
                $html .= " $NFB$agent_paused$NFE&nbsp; <font color=blue>Paused agents</font></div>\n";
        	} elseif ($agent_paused == 1) {
                $html .= " $NFB$agent_paused$NFE&nbsp; <font color=blue>Paused agent</font></div>\n";
        	} else {
                $html .= " $NFB$agent_paused$NFE&nbsp; <font color=blue>Paused agents</font></div>\n";
        	}
          
            $Ahtml .= "<pre><FONT face=Fixed,monospace SIZE=1>";
            $html .= $Ahtml;
        } else {
            $html .= "<font color=red>&nbsp;&nbsp;NO AGENTS ON CALLS</font><br/>\n";
            $Ahtml .= "<pre><font face=\"Fixed,monospace\" size=1>";
            $html .= $Ahtml; 
        }
        
        ################################################################################
        ### END calculating calls/agents
        ################################################################################
            
        $html .= "</td>";
        $html .= "</tr>";
        $html .= "</table>";
        
        $html .= "</form>\n\n";
        $k++;
    }
    
    $html .= "</div>";
    $html .= "&nbsp;";
 
    if (file_exists($pref . 'resources.txt')) {
        $html .= "<br><br><br>";
        $html .= "<center>";
        if ($cpuinfo == 0 ) {
            $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=38400&DB=$DB&adastats=$adastats&cpuinfo=0&active_only=$active_only\"><font size=1><b>STANDARD INFO</b></font></a>";
            $html .= " - ";
            $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=38400&DB=$DB&adastats=$adastats&cpuinfo=1&active_only=$active_only\"><font size=1>EXTENDED INFO</font></a>";
            eval("\$html .= \"" . file_get_contents($pref . 'resources.txt') . "\";");
        } else {
            $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=38400&DB=$DB&adastats=$adastats&cpuinfo=0&active_only=$active_only\"><font size=1>STANDARD INFO</font></a>";
            $html .= " - ";
            $html .= "<a href=\"$PHP_SELF?useOAC=$useOAC&ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=38400&DB=$DB&adastats=$adastats&cpuinfo=1&active_only=$active_only\"><font size=1><b>EXTENDED INFO</b></font></a>";
            eval("\$html .= \"" . file_get_contents($pref . 'resources-xtd.txt') . "\";");
        }
        $html .= "</center>";
    } else {
        $load_ave = getloadavg();
    
        // Get server loads, txt file from other servers
        //$load_ave = get_server_load($load_ave);
        
    
        $Ahtml="<pre><font face=\"Fixed,monospace\" size=-2>";
        if (file_exists($pref . 'S1_load.txt')) {
            $s1_load = file($pref . 'S1_load.txt');
            list( $line_num, $line ) = each( $s1_load );
            $load_ave_s1=$line;
            $Ahtml .= "  <font color=$default_text>Apache   Load Average:</font> $load_ave<br>";
            $Ahtml .= "  <font color=$default_text>MySQL    Load Average:</font> $load_ave_s1";
        } elseif (!file_exists($pref . 'D1_load.txt')&& !file_exists($pref . 'D2_load.txt') && !file_exists($pref . 'D3_load.txt') && !file_exists($pref . 'D4_load.txt') && !file_exists($pref . 'D5_load.txt') && !file_exists($pref . 'D6_load.txt')) {
            $Ahtml .= "  <font color=$default_text>Dialer Load Average:</font> $load_ave<br>";
        } else {
            $Ahtml .= "  <font color=$default_text>SQL/Web  Load Average:</font> $load_ave";
        }
        if (file_exists($pref . 'D1_load.txt')) {
            $d1_load = file($pref . 'D1_load.txt');
            list( $line_num, $line ) = each( $d1_load ) ;
            $load_ave_d1=$line;
            $Ahtml .= "  <font color=$default_text>Dialer 1 Load Average:</font> $load_ave_d1";
        }
        if (file_exists($pref . 'D2_load.txt')) {
            $d2_load = file($pref . 'D2_load.txt');
            list( $line_num, $line ) = each( $d2_load );
            $load_ave_d2=$line;
            $Ahtml .= "  <font color=$default_text>Dialer 2 Load Average:</font> $load_ave_d2";
        }
        if (file_exists($pref . 'D3_load.txt')) {
            $d3_load = file($pref . 'D3_load.txt');
            list( $line_num, $line ) = each( $d3_load );
            $load_ave_d3=$line;
            $Ahtml .= "  <font color=$default_text>Dialer 3 Load Average:</font> $load_ave_d3";
        }
        if (file_exists($pref . 'D4_load.txt')) {
            $d4_load = file($pref . 'D4_load.txt');
            list( $line_num, $line ) = each( $d4_load );
            $load_ave_d4=$line;
            $Ahtml .= "  <font color=$default_text>Dialer 4 Load Average:</font> $load_ave_d4";
        }
        if (file_exists($pref . 'D5_load.txt')) {
            $d5_load = file($pref . 'D5_load.txt');
            list( $line_num, $line ) = each( $d5_load );
            $load_ave_d5=$line;
            $Ahtml .= "  <font color=$default_text>Dialer 5 Load Average:</font> $load_ave_d5";
        }
        if (file_exists($pref . 'D6_load.txt')) {
            $d6_load = file($pref . 'D6_load.txt');
            list( $line_num, $line ) = each( $d6_load );
            $load_ave_d6=$line;
            $Ahtml .= "  <font color=$default_text>Dialer 6 Load Average:</font> $load_ave_d6";
        }
        $Ahtml .= "</pre>";
        $html .= $Ahtml;
    }


    #$html .= "<table width='$page_width' bgcolor=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>";
    return $html;
}



?>
