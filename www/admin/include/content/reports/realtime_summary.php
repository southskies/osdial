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



function report_realtime_summary() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }
 
    $html = '';
    $Ahtml = '';

	$pref="";

	$NOW_TIME = date("Y-m-d H:i:s");
	$STARTtime = date("U");
	
	$stmt="select campaign_id from osdial_campaigns where active='Y';";
	$rslt=mysql_query($stmt, $link);
	if (!isset($DB)) { $DB=0; }
	if ($DB) { $html .= "$stmt\n"; }
	
	$groups_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $groups_to_print)	{
		$row=mysql_fetch_row($rslt);
		$groups[$i] =$row[0];
		$i++;
	}
	
	if (!isset($RR))   {$RR=4;}
	if ($RR==0)  {$RR=4;}
	
	
	$html .= "<font size=1>";
	$html .= "<div class=no-ul>";
	
	$html .= "<form action=$PHP_SELF method=POST>\n";
	$html .= "<input type=hidden name=ADD value=$ADD>\n";
	$html .= "<input type=hidden name=SUB value=$SUB>\n";
	$html .= "<input type=hidden name=adastats value=$adastats\n";
	$html .= "<input type=hidden name=group value=$group>\n";
	$html .= "<input type=hidden name=campaign_id value=$campaign_id>\n";
	$html .= "<input type=hidden name=RR value=$RR>\n";
	
	$html .= "<p class=centered><font color=navy size=+1>ALL CAMPAIGNS SUMMARY</font<br><br>";
	$html .= "<font color=navy size=-1>Update:&nbsp;";
	if ($RR==38400) { $html .= "<font size=+1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=38400&DB=$DB&adastats=$adastats\">Daily</a>&nbsp;&nbsp;";
	if ($RR==3600) { $html .= "<font size=+1>"; } else { $html .= "<font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=3600&DB=$DB&adastats=$adastats\">Hourly</a>&nbsp;&nbsp;";
	if ($RR==600) { $html .= "<font size=+1>"; } else { $html .= "<font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=600&DB=$DB&adastats=$adastats\">10min</a>&nbsp;&nbsp;";
	if ($RR==30) { $html .= "<font size=+1>"; } else { $html .= "<font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=30&DB=$DB&adastats=$adastats\">30sec</a>&nbsp;&nbsp;";
	if ($RR==4) { $html .= "<font size=+1>"; } else { $html .= "<font size=-1>"; }
	$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=4&DB=$DB&adastats=$adastats\">4sec</a>&nbsp;&nbsp;";
	$html .= "</font>";
	$html .= "&nbsp;-&nbsp;&nbsp;";
	if ($adastats<2) {
		$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=2\"><font size=1>VIEW MORE SETTINGS</font></a>";
	} else {
		$html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=1\"><font size=1>VIEW LESS SETTINGS</font></a>";
	}
	$html .= "</p>\n\n";
	
	$k=0;
	while($k<$groups_to_print) {
		$NFB = '<b><font size=3 face="courier">';
		$NFE = '</font></b>';
		$F=''; $FG=''; $B=''; $BG='';
		
		
		$group = $groups[$k];
		$html .= "<font class=realtimeindents size=-1><b><a href=\"./admin.php?ADD=$ADD&SUB=" . ($SUB + 1) . "&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=$adastats\">$group</a></b> &nbsp; - &nbsp; ";
		$html .= "<a href=\"./admin.php?ADD=31&campaign_id=$group\">Modify</a> </font>\n";
		
		
		$stmt = "select count(*) from osdial_campaigns where campaign_id='$group' and campaign_allow_inbound='Y';";
		$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$campaign_allow_inbound = $row[0];
		
		$stmt="select auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout,dial_statuses from osdial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$DIALlev =	$row[0];
		$DIALstatusA =	$row[1];
		$DIALstatusB =	$row[2];
		$DIALstatusC =	$row[3];
		$DIALstatusD =	$row[4];
		$DIALstatusE =	$row[5];
		$DIALorder =	$row[6];
		$DIALfilter =	$row[7];
		$HOPlev =	$row[8];
		$DIALmethod =	$row[9];
		$maxDIALlev =	$row[10];
		$DROPmax =	$row[11];
		$targetDIFF =	$row[12];
		$ADAintense =	$row[13];
		$ADAavailonly =	$row[14];
		$TAPERtime =	$row[15];
		$CALLtime =	$row[16];
		$DIALtimeout =	$row[17];
		$DIALstatuses =	$row[18];
		$DIALstatuses = (preg_replace("/ -$|^ /","",$DIALstatuses));
		$DIALstatuses = (ereg_replace(' ',', ',$DIALstatuses));
		
		$stmt="select count(*) from osdial_hopper where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$VDhop = $row[0];
		
		$stmt="select dialable_leads,calls_today,drops_today,drops_answers_today_pct,differential_onemin,agents_average_onemin,balance_trunk_fill,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4,status_category_hour_count_1,status_category_hour_count_2,status_category_hour_count_3,status_category_hour_count_4 from osdial_campaign_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$DAleads =	$row[0];
		$callsTODAY =	$row[1];
		$dropsTODAY =	$row[2];
		$drpctTODAY =	$row[3];
		$diffONEMIN =	$row[4];
		$agentsONEMIN = $row[5];
		$balanceFILL =	$row[6];
		$answersTODAY = $row[7];
		$VSCcat1 =	$row[8];
		$VSCcat1tally = $row[9];
		$VSCcat2 =	$row[10];
		$VSCcat2tally = $row[11];
		$VSCcat3 =	$row[12];
		$VSCcat3tally = $row[13];
		$VSCcat4 =	$row[14];
		$VSCcat4tally = $row[15];
		$VSCcat1hourtally = $row[16];
		$VSCcat2hourtally = $row[17];
		$VSCcat3hourtally = $row[18];
		$VSCcat4hourtally = $row[19];
		
		if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) )	{
			$diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
			$diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
		} else {
			$diffpctONEMIN = '0.00';
		}
		
		$stmt="select sum(local_trunk_shortage) from osdial_campaign_server_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$balanceSHORT = $row[0];
		
		$html .= "<table align=center cellpadding=0 cellspacing=0 border=0><TR>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALlev&nbsp; &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>TRUNK SHORT/FILL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $balanceSHORT / $balanceFILL &nbsp; &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>FILTER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALfilter &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>TIME:</B></TD><TD ALIGN=LEFT><font size=2 color=navy>&nbsp; $NOW_TIME </TD>";
		$html .= "";
		$html .= "</TR>";
		
		if ($adastats>1) {
			$html .= "<TR BGCOLOR=\"#CCCCCC\">";
			$html .= "<TD ALIGN=RIGHT><font size=2>&nbsp; <B>MAX LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $maxDIALlev &nbsp; </TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>DROPPED MAX:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DROPmax% &nbsp; &nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>TARGET DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $targetDIFF &nbsp; &nbsp; </TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>INTENSITY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAintense &nbsp; &nbsp; </TD>";
			$html .= "</TR>";
		
			$html .= "<TR BGCOLOR=\"#CCCCCC\">";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>DIAL TIMEOUT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALtimeout &nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>TAPER TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $TAPERtime &nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>LOCAL TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $CALLtime &nbsp;</TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>AVAIL ONLY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAavailonly &nbsp;</TD>";
			$html .= "</TR>";
			
			$html .= "<TR BGCOLOR=\"#CCCCCC\">";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>DL DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffONEMIN &nbsp; &nbsp; </TD>";
			$html .= "<TD ALIGN=RIGHT><font size=2><B>DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffpctONEMIN% &nbsp; &nbsp; </TD>";
			$html .= "</TR>";
		}
		
		$html .= "<TR>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIALABLE LEADS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DAleads &nbsp; &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>AVG AGENTS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $agentsONEMIN &nbsp; &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL METHOD:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALmethod &nbsp; &nbsp; </TD>";
		$html .= "</TR>";
		
		$html .= "<TR>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>HOPPER LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $HOPlev &nbsp; &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED / ANSWERED:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY / $answersTODAY &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>STATUSES:</B></TD><TD ALIGN=LEFT colspan=3><font size=2>&nbsp; $DIALstatuses &nbsp; &nbsp; </TD>";
		$html .= "</TR>";
		
		$html .= "<TR>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>LEADS IN HOPPER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $VDhop &nbsp; &nbsp; </TD>";
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; ";
		if ($drpctTODAY >= $DROPmax) {
			$html .= "<font color=red><B>$drpctTODAY%</B></font>";
		} else {
			$html .= "$drpctTODAY%";
		}
		$html .= " &nbsp; &nbsp;</TD>";
		
		$html .= "<TD ALIGN=RIGHT><font size=2 color=navy><B>ORDER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALorder &nbsp; &nbsp; </TD>";
		$html .= "</tr><tr>";
		if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
			$html .= "<td align=right><font size=2 color=navy><B>$VSCcat1:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
			$html .= "<td align=right><font size=2 color=navy><B>$VSCcat2:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) { 
			$html .= "<td align=right><font size=2 color=navy><B>$VSCcat3:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
			$html .= "<td align=right><font size=2 color=navy><B>$VSCcat4:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4tally&nbsp;&nbsp;&nbsp; \n";
		}
		$html .= "</tr><tr>";
		if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
			$html .= "<td align=right><font size=2 color=navy><B>$VSCcat1/hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1hourtally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
			$html .= "<td align=right><font size=2 color=navy><B>$VSCcat2/hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2hourtally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) { 
			$html .= "<td align=right><font size=2 color=navy><B>$VSCcat3/hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3hourtally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
			$html .= "<td align=right><font size=2 color=navy><B>$VSCcat4/hr:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4hourtally&nbsp;&nbsp;&nbsp; \n";
		}
		
		$html .= "</TR>";
		
		$html .= "<TR>";
		$html .= "<TD ALIGN=center COLSPAN=8>";
		
		### Header finish
		
		
		
		
		
		################################################################################
		### START calculating calls/agents
		################################################################################
		
		################################################################################
		###### OUTBOUND CALLS
		################################################################################
		if ($campaign_allow_inbound > 0) {
			$stmt="select closer_campaigns from osdial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			$closer_campaigns = preg_replace("/^ | -$/","",$row[0]);
			$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
			$closer_campaigns = "'$closer_campaigns'";
		
			$stmt="select status from osdial_auto_calls where status NOT IN('XFER') and ( (call_type='IN' and campaign_id IN($closer_campaigns)) or (campaign_id='" . mysql_real_escape_string($group) . "' and call_type='OUT') );";
		} else {
			if ($group=='XXXX-ALL-ACTIVE-XXXX') { 
				$groupSQL = '';
			} else {
				$groupSQL = " and campaign_id='" . mysql_real_escape_string($group) . "'";
			}
		
			$stmt="select status from osdial_auto_calls where status NOT IN('XFER') $groupSQL;";
		}
		$rslt=mysql_query($stmt, $link);
		
		if ($DB) {
			$html .= "$stmt\n";
		}
		
		$parked_to_print = mysql_num_rows($rslt);
		if ($parked_to_print > 0) {
			$i=0;
			$out_total=0;
			$out_ring=0;
			$out_live=0;
			while ($i < $parked_to_print) {
				$row=mysql_fetch_row($rslt);
		
				if (eregi("LIVE",$row[0])) {
					$out_live++;
				} else {
					if (eregi("CLOSER",$row[0])) {
						$nothing=1;
					} else {
						$out_ring++;
					}
				}
				$out_total++;
				$i++;
			}
	
			if ($out_live > 0)  {$F='<FONT class="r1">'; $FG='</FONT>';}
			if ($out_live > 4)  {$F='<FONT class="r2">'; $FG='</FONT>';}
			if ($out_live > 9)  {$F='<FONT class="r3">'; $FG='</FONT>';}
			if ($out_live > 14) {$F='<FONT class="r4">'; $FG='</FONT>';}
	
			if ($campaign_allow_inbound > 0) {
				$html .= "$NFB$out_total$NFE <font color=navy>current active calls</font>&nbsp; &nbsp; &nbsp; \n";
			} else {
				$html .= "$NFB$out_total$NFE <font color=navy>calls being placed</font> &nbsp; &nbsp; &nbsp; \n";
			}
			
			$html .= "$NFB$out_ring$NFE <font color=navy>calls ringing</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
			$html .= "$NFB$F &nbsp;$out_live $FG$NFE <font color=navy>calls waiting for agents</font> &nbsp; &nbsp; &nbsp; \n";
		} else {
			$html .= "<font color=red>&nbsp;NO LIVE CALLS WAITING</font>&nbsp;\n";
		}
		
		
		###################################################################################
		###### TIME ON SYSTEM
		###################################################################################
		
		$agent_incall=0;
		$agent_ready=0;
		$agent_paused=0;
		$agent_total=0;
		
		$stmt="select extension,user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id from osdial_live_agents where campaign_id='" . mysql_real_escape_string($group) . "';";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {
			$html .= "$stmt\n";
		}
		$talking_to_print = mysql_num_rows($rslt);
		if ($talking_to_print > 0) {
			$i=0;
			$agentcount=0;
			while ($i < $talking_to_print) {
				$row=mysql_fetch_row($rslt);
				if (eregi("READY|PAUSED",$row[3]))	{
					$row[5]=$row[6];
				}
				$Lstatus =			$row[3];
				$status =			sprintf("%-6s", $row[3]);
				if (!eregi("INCALL|QUEUE",$row[3])) {
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
				if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
				$call_time_MS = "$call_time_M_int:$call_time_SEC";
				$call_time_MS =		sprintf("%7s", $call_time_MS);
				$G = '';		$EG = '';
				if (eregi("PAUSED",$row[3])) {
					if ($call_time_M_int >= 30) {
						$i++; continue;
					} else {
						$agent_paused++;  $agent_total++;
					}
				}
		
				if ( (eregi("INCALL",$status)) or (eregi("QUEUE",$status)) ) {$agent_incall++;  $agent_total++;}
				if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {$agent_ready++;  $agent_total++;}
				$agentcount++;
		
		
				$i++;
			}
		
			if ($agent_ready > 0) {$B='<FONT class="b1">'; $BG='</FONT>';}
			if ($agent_ready > 4) {$B='<FONT class="b2">'; $BG='</FONT>';}
			if ($agent_ready > 9) {$B='<FONT class="b3">'; $BG='</FONT>';}
			if ($agent_ready > 14) {$B='<FONT class="b4">'; $BG='</FONT>';}
	
			$html .= "\n<BR>\n";
	
			$html .= "$NFB$agent_total$NFE <font color=navy>agents logged in</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
			$html .= "$NFB$agent_incall$NFE <font color=navy>agents in calls</font> &nbsp; &nbsp; &nbsp; \n";
			$html .= "$NFB$B &nbsp;$agent_ready $BG$NFE <font color=navy>agents waiting</font> &nbsp; &nbsp; &nbsp; \n";
			$html .= "$NFB$agent_paused$NFE <font color=navy>paused agents</font> &nbsp; &nbsp; &nbsp; \n";
			
			$Ahtml .= "<pre><FONT face=Fixed,monospace SIZE=1>";
			$html .= "$Aecho";
		} else {
			$html .= "<font color=red>&bull;&nbsp;&nbsp;NO AGENTS ON CALLS</font><BR>\n";
			$Ahtml .= "<PRE><FONT face=Fixed,monospace SIZE=1>";
			$html .= "$Aecho"; 
		}
		
		################################################################################
		### END calculating calls/agents
		################################################################################
			
		$html .= "</TD>";
		$html .= "</TR>";
		$html .= "</TABLE>";
		
		$html .= "</FORM>\n\n";
		$k++;
	}
	
	$html .= "</div>";
	$html .= "&nbsp;";
 
	$load_ave = getloadavg();
	
	// Get server loads, txt file from other servers
	//$load_ave = get_server_load($load_ave);
		
	
	$Aecho="<pre><font face=Fixed,monospace SIZE=-2>";
	if (file_exists($pref . 'S1_load.txt')) {
		$s1_load = file($pref . 'S1_load.txt');
		list( $line_num, $line ) = each( $s1_load );
		$load_ave_s1=$line;
		$Ahtml .= "  <font color=navy>Apache   Load Average:</font> $load_ave<br>";
		$Ahtml .= "  <font color=navy>MySQL    Load Average:</font> $load_ave_s1";
	} elseif (!file_exists($pref . 'D1_load.txt')&& !file_exists($pref . 'D2_load.txt') && !file_exists($pref . 'D3_load.txt') && !file_exists($pref . 'D4_load.txt') && !file_exists($pref . 'D5_load.txt') && !file_exists($pref . 'D6_load.txt')) {
		$Ahtml .= "  <font color=navy>Dialer Load Average:</font> $load_ave<br>";
	} else {
		$Ahtml .= "  <font color=navy>SQL/Web  Load Average:</font> $load_ave";
	}
	if (file_exists($pref . 'D1_load.txt')) {
		$d1_load = file($pref . 'D1_load.txt');
		list( $line_num, $line ) = each( $d1_load ) ;
		$load_ave_d1=$line;
		$Ahtml .= "  <font color=navy>Dialer 1 Load Average:</font> $load_ave_d1";
	}
	if (file_exists($pref . 'D2_load.txt')) {
		$d2_load = file($pref . 'D2_load.txt');
		list( $line_num, $line ) = each( $d2_load );
		$load_ave_d2=$line;
		$Ahtml .= "  <font color=navy>Dialer 2 Load Average:</font> $load_ave_d2";
	}
	if (file_exists($pref . 'D3_load.txt')) {
		$d3_load = file($pref . 'D3_load.txt');
		list( $line_num, $line ) = each( $d3_load );
		$load_ave_d3=$line;
		$Ahtml .= "  <font color=navy>Dialer 3 Load Average:</font> $load_ave_d3";
	}
	if (file_exists($pref . 'D4_load.txt')) {
		$d4_load = file($pref . 'D4_load.txt');
		list( $line_num, $line ) = each( $d4_load );
		$load_ave_d4=$line;
		$Ahtml .= "  <font color=navy>Dialer 4 Load Average:</font> $load_ave_d4";
	}
	if (file_exists($pref . 'D5_load.txt')) {
		$d5_load = file($pref . 'D5_load.txt');
		list( $line_num, $line ) = each( $d5_load );
		$load_ave_d5=$line;
		$Ahtml .= "  <font color=navy>Dialer 5 Load Average:</font> $load_ave_d5";
	}
	if (file_exists($pref . 'D6_load.txt')) {
		$d6_load = file($pref . 'D6_load.txt');
		list( $line_num, $line ) = each( $d6_load );
		$load_ave_d6=$line;
		$Ahtml .= "  <font color=navy>Dialer 6 Load Average:</font> $load_ave_d6";
	}
	$html .= "$Aecho";
	$html .= "</pre>";
	$html .= "<TABLE WIDTH='<?=$page_width ?>' BGCOLOR=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>";

    return $html;
}



?>
