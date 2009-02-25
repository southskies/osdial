<?php


######################
# ADD=999999/SUB=13 Show RealTimeSummary screen
######################
if ($ADD==999999 && $SUB==13) {
 
	$pref="";
	function getloadavg() {
		if (file_exists($pref . 'Loadavg.txt')) {
			$loadavg = file_get_contents($pref . "Loadavg.txt");
		}
		return $loadavg;
	}

	#$stmt="SELECT count(*) from osdial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1';";
	#if ($DB) { echo "|$stmt|\n"; }
	#
	#$rslt=mysql_query($stmt, $link);
	#$row=mysql_fetch_row($rslt);
	#$auth=$row[0];
	#
	#if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth)) {
	#	Header("WWW-Authenticate: Basic realm=\"OSDial-Administrator\"");
	#	Header("HTTP/1.0 401 Unauthorized");
	#	echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
	#	exit;
	#}
	
	$NOW_TIME = date("Y-m-d H:i:s");
	$STARTtime = date("U");
	
	$stmt="select campaign_id from osdial_campaigns where active='Y';";
	$rslt=mysql_query($stmt, $link);
	if (!isset($DB)) { $DB=0; }
	if ($DB) { echo "$stmt\n"; }
	
	$groups_to_print = mysql_num_rows($rslt);
	$i=0;
	while ($i < $groups_to_print)	{
		$row=mysql_fetch_row($rslt);
		$groups[$i] =$row[0];
		$i++;
	}
	
	if (!isset($RR))   {$RR=4;}
	if ($RR==0)  {$RR=4;}
	
	
	echo "<font size=1>";
	echo "<div class=no-ul>";
	
	echo "<form action=$PHP_SELF method=POST>\n";
	echo "<input type=hidden name=ADD value=13>\n";
	echo "<input type=hidden name=adastats value=$adastats\n";
	echo "<input type=hidden name=group value=$group>\n";
	echo "<input type=hidden name=RR value=$RR>\n";
	
	echo "<p class=centered><font color=navy size=+1>ALL CAMPAIGNS SUMMARY</font<br><br>";
	echo "<font color=navy size=-1>Update:&nbsp;";
	if ($RR==38400) { echo "<font size=+1>"; }
	echo "<a href=\"$PHP_SELF?ADD=999999&SUB=13&group=$group&RR=38400&DB=$DB&adastats=$adastats\">Daily</a>&nbsp;&nbsp;";
	if ($RR==3600) { echo "<font size=+1>"; } else { echo "<font size=-1>"; }
	echo "<a href=\"$PHP_SELF?ADD=999999&SUB=13&group=$group&RR=3600&DB=$DB&adastats=$adastats\">Hourly</a>&nbsp;&nbsp;";
	if ($RR==600) { echo "<font size=+1>"; } else { echo "<font size=-1>"; }
	echo "<a href=\"$PHP_SELF?ADD=999999&SUB=13&group=$group&RR=600&DB=$DB&adastats=$adastats\">10min</a>&nbsp;&nbsp;";
	if ($RR==30) { echo "<font size=+1>"; } else { echo "<font size=-1>"; }
	echo "<a href=\"$PHP_SELF?ADD=999999&SUB=13&group=$group&RR=30&DB=$DB&adastats=$adastats\">30sec</a>&nbsp;&nbsp;";
	if ($RR==4) { echo "<font size=+1>"; } else { echo "<font size=-1>"; }
	echo "<a href=\"$PHP_SELF?ADD=999999&SUB=13&group=$group&RR=4&DB=$DB&adastats=$adastats\">4sec</a>&nbsp;&nbsp;";
	echo "</font>";
	echo "&nbsp;-&nbsp;&nbsp;";
	if ($adastats<2) {
		echo "<a href=\"$PHP_SELF?ADD=999999&SUB=13&group=$group&RR=$RR&DB=$DB&adastats=2\"><font size=1>VIEW MORE SETTINGS</font></a>";
	} else {
		echo "<a href=\"$PHP_SELF?ADD=999999&SUB=13&group=$group&RR=$RR&DB=$DB&adastats=1\"><font size=1>VIEW LESS SETTINGS</font></a>";
	}
	//echo "&nbsp;&nbsp;&nbsp;<a href=\"./admin.php?ADD=10\">Campaigns</a>&nbsp;&nbsp;<a href=\"./admin.php?ADD=999999\">Reports</a>";
	echo "</p>\n\n";
	
	$k=0;
	while($k<$groups_to_print) {
		$NFB = '<b><font size=3 face="courier">';
		$NFE = '</font></b>';
		$F=''; $FG=''; $B=''; $BG='';
		
		
		$group = $groups[$k];
		echo "<font class=realtimeindents size=-1><b><a href=\"./admin.php?ADD=999999&SUB=14&group=$group&RR=$RR&DB=$DB&adastats=$adastats\">$group</a></b> &nbsp; - &nbsp; ";
		echo "<a href=\"./admin.php?ADD=31&campaign_id=$group\">Modify</a> </font>\n";
		
		
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
		
		$stmt="select dialable_leads,calls_today,drops_today,drops_answers_today_pct,differential_onemin,agents_average_onemin,balance_trunk_fill,answers_today,status_category_1,status_category_count_1,status_category_2,status_category_count_2,status_category_3,status_category_count_3,status_category_4,status_category_count_4 from osdial_campaign_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
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
		
		echo "<table align=center cellpadding=0 cellspacing=0 border=0><TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALlev&nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>TRUNK SHORT/FILL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $balanceSHORT / $balanceFILL &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>FILTER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALfilter &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>TIME:</B></TD><TD ALIGN=LEFT><font size=2 color=navy>&nbsp; $NOW_TIME </TD>";
		echo "";
		echo "</TR>";
		
		if ($adastats>1) {
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2>&nbsp; <B>MAX LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $maxDIALlev &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED MAX:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DROPmax% &nbsp; &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>TARGET DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $targetDIFF &nbsp; &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>INTENSITY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAintense &nbsp; &nbsp; </TD>";
			echo "</TR>";
		
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2><B>DIAL TIMEOUT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALtimeout &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>TAPER TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $TAPERtime &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>LOCAL TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $CALLtime &nbsp;</TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>AVAIL ONLY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAavailonly &nbsp;</TD>";
			echo "</TR>";
			
			echo "<TR BGCOLOR=\"#CCCCCC\">";
			echo "<TD ALIGN=RIGHT><font size=2><B>DL DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffONEMIN &nbsp; &nbsp; </TD>";
			echo "<TD ALIGN=RIGHT><font size=2><B>DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffpctONEMIN% &nbsp; &nbsp; </TD>";
			echo "</TR>";
		}
		
		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIALABLE LEADS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DAleads &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>AVG AGENTS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $agentsONEMIN &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DIAL METHOD:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALmethod &nbsp; &nbsp; </TD>";
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>HOPPER LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $HOPlev &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED / ANSWERED:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY / $answersTODAY &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>STATUSES:</B></TD><TD ALIGN=LEFT colspan=3><font size=2>&nbsp; $DIALstatuses &nbsp; &nbsp; </TD>";
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>LEADS IN HOPPER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $VDhop &nbsp; &nbsp; </TD>";
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>DROPPED PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; ";
		if ($drpctTODAY >= $DROPmax) {
			echo "<font color=red><B>$drpctTODAY%</B></font>";
		} else {
			echo "$drpctTODAY%";
		}
		echo " &nbsp; &nbsp;</TD>";
		
		echo "<TD ALIGN=RIGHT><font size=2 color=navy><B>ORDER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALorder &nbsp; &nbsp; </TD>";
		echo "</tr><tr>";
		if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
			echo "<td align=right><font size=2 color=navy><B>$VSCcat1:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
			echo "<td align=right><font size=2 color=navy><B>$VSCcat2:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) { 
			echo "<td align=right><font size=2 color=navy><B>$VSCcat3:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3tally&nbsp;&nbsp;&nbsp; \n";
		}
		if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
			echo "<td align=right><font size=2 color=navy><B>$VSCcat4:</B></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4tally&nbsp;&nbsp;&nbsp; \n";
		}
		
		echo "</TR>";
		
		echo "<TR>";
		echo "<TD ALIGN=center COLSPAN=8>";
		
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
			echo "$stmt\n";
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
				echo "$NFB$out_total$NFE <font color=navy>current active calls</font>&nbsp; &nbsp; &nbsp; \n";
			} else {
				echo "$NFB$out_total$NFE <font color=navy>calls being placed</font> &nbsp; &nbsp; &nbsp; \n";
			}
			
			echo "$NFB$out_ring$NFE <font color=navy>calls ringing</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
			echo "$NFB$F &nbsp;$out_live $FG$NFE <font color=navy>calls waiting for agents</font> &nbsp; &nbsp; &nbsp; \n";
		} else {
			echo "<font color=red>&nbsp;NO LIVE CALLS WAITING</font>&nbsp;\n";
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
			echo "$stmt\n";
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
	
			echo "\n<BR>\n";
	
			echo "$NFB$agent_total$NFE <font color=navy>agents logged in</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
			echo "$NFB$agent_incall$NFE <font color=navy>agents in calls</font> &nbsp; &nbsp; &nbsp; \n";
			echo "$NFB$B &nbsp;$agent_ready $BG$NFE <font color=navy>agents waiting</font> &nbsp; &nbsp; &nbsp; \n";
			echo "$NFB$agent_paused$NFE <font color=navy>paused agents</font> &nbsp; &nbsp; &nbsp; \n";
			
			$Aecho .= "<pre><FONT face=Fixed,monospace SIZE=1>";
			echo "$Aecho";
		} else {
			echo "<font color=red>&bull;&nbsp;&nbsp;NO AGENTS ON CALLS</font><BR>\n";
			$Aecho .= "<PRE><FONT face=Fixed,monospace SIZE=1>";
			echo "$Aecho"; 
		}
		
		################################################################################
		### END calculating calls/agents
		################################################################################
			
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
		
		echo "</FORM>\n\n";
		$k++;
	}
	
	echo "</div>";
	echo "&nbsp;";
 
	$load_ave = getloadavg();
	
	// Get server loads, txt file from other servers
	//$load_ave = get_server_load($load_ave);
		
	
	$Aecho="<pre><font face=Fixed,monospace SIZE=1>";
	if (file_exists($pref . 'S1_load.txt')) {
		$s1_load = file($pref . 'S1_load.txt');
		list( $line_num, $line ) = each( $s1_load );
		$load_ave_s1=$line;
		$Aecho .= "  <font color=navy>Apache   Load Average:</font> $load_ave<br>";
		$Aecho .= "  <font color=navy>MySQL    Load Average:</font> $load_ave_s1<br>";
	} elseif (!file_exists($pref . 'D1_load.txt')&& !file_exists($pref . 'D2_load.txt') && !file_exists($pref . 'D3_load.txt') && !file_exists($pref . 'D4_load.txt') && !file_exists($pref . 'D5_load.txt') && !file_exists($pref . 'D6_load.txt')) {
		$Aecho .= "  <font color=navy>Dialer Load Average:</font> $load_ave<br>";
	} else {
		$Aecho .= "  <font color=navy>SQL/Web  Load Average:</font> $load_ave<br>";
	}
	if (file_exists($pref . 'D1_load.txt')) {
		$d1_load = file($pref . 'D1_load.txt');
		list( $line_num, $line ) = each( $d1_load ) ;
		$load_ave_d1=$line;
		$Aecho .= "  <font color=navy>Dialer 1 Load Average:</font> $load_ave_d1";
	}
	if (file_exists($pref . 'D2_load.txt')) {
		$d2_load = file($pref . 'D2_load.txt');
		list( $line_num, $line ) = each( $d2_load );
		$load_ave_d2=$line;
		$Aecho .= "  <font color=navy>Dialer 2 Load Average:</font> $load_ave_d2";
	}
	if (file_exists($pref . 'D3_load.txt')) {
		$d3_load = file($pref . 'D3_load.txt');
		list( $line_num, $line ) = each( $d3_load );
		$load_ave_d3=$line;
		$Aecho .= "  <font color=navy>Dialer 3 Load Average:</font> $load_ave_d3";
	}
	if (file_exists($pref . 'D4_load.txt')) {
		$d4_load = file($pref . 'D4_load.txt');
		list( $line_num, $line ) = each( $d4_load );
		$load_ave_d4=$line;
		$Aecho .= "  <font color=navy>Dialer 4 Load Average:</font> $load_ave_d4";
	}
	if (file_exists($pref . 'D5_load.txt')) {
		$d5_load = file($pref . 'D5_load.txt');
		list( $line_num, $line ) = each( $d5_load );
		$load_ave_d5=$line;
		$Aecho .= "  <font color=navy>Dialer 5 Load Average:</font> $load_ave_d5";
	}
	if (file_exists($pref . 'D6_load.txt')) {
		$d6_load = file($pref . 'D6_load.txt');
		list( $line_num, $line ) = each( $d6_load );
		$load_ave_d6=$line;
		$Aecho .= "  <font color=navy>Dialer 6 Load Average:</font> $load_ave_d6";
	}
	//echo "<tr><td colspan=10>";
	echo "$Aecho";
	echo "</pre>";
	//echo "</td></tr><tr><td colspan=10>&nbsp;";
	echo "<TABLE WIDTH='<?=$page_width ?>' BGCOLOR=#E9E8D9 cellpadding=0 cellspacing=0 align=center class=across>";
}



?>
