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

function report_realtime_detail() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $html = '';
    $pref = '';

    $RR = get_variable('RR');
    $group = get_variable('group');
    $usergroup = get_variable('usergroup');
    $UGdisplay = get_variable('UGdisplay');
    $UidORname = get_variable('UidORname');
    $orderby = get_variable('orderby');
    $orddir = get_variable('orddir');
    $SERVdisplay = get_variable('SERVdisplay');
    $CALLSdisplay = get_variable('CALLSdisplay');
    $VAdisplay = get_variable('VAdisplay');
    $cpuinfo = get_variable('cpuinfo');
    $DB = get_variable('DB');
    $adastats = get_variable('adastats');
    $SIPmonitorLINK = get_variable('SIPmonitorLINK');
    $IAXmonitorLINK = get_variable('IAXmonitorLINK');
    if ($RR=='') {$RR=4;}
    if ($group=='') {$group='XXXX-ALL-ACTIVE-XXXX';}
    if ($UGdisplay=='') {$UGdisplay=0;}
    if ($UidORname=='') {$UidORname=0;}
    if ($orderby=='') {$orderby='exten';}
    if ($orddir=='') {$orddir='down';}
    if ($SERVdisplay=='') {$SERVdisplay=0;}
    if ($CALLSdisplay=='') {$CALLSdisplay=0;}
    if ($VAdisplay=='') {$VAdisplay=0;}
    if ($cpuinfo=='') {$cpuinfo=0;}


    $NOW_TIME = date("Y-m-d H:i:s");
    $NOW_DAY = date("Y-m-d");
    $NOW_HOUR = date("H:i:s");
    $STARTtime = date("U");
    $epochSIXhoursAGO = ($STARTtime - 21600);
    $timeSIXhoursAGO = date("Y-m-d H:i:s",$epochSIXhoursAGO);


    function HorizLine($Width) {
        $HDLine='';
        for ($i = 1; $i <= $Width; $i++) {
            $HDLine.="&#x2550;";
        }
        return $HDLine;
    }
    function CenterLine($Width) {
        $HDLine='';
        for ($i = 1; $i <= $Width; $i++) {
            $HDLine.="&#x2500;";
        }
        return $HDLine;
    }


    $stmt=sprintf("SELECT campaign_id,campaign_name FROM osdial_campaigns WHERE active='Y' AND campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
    $rslt=mysql_query($stmt, $link);
    if (!isset($DB))   {$DB=0;}
    if ($DB) {$html .= "$stmt\n";}

    $groups_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $groups_to_print) {
        $row=mysql_fetch_row($rslt);
        $groups[$i] =$row[0];
        $group_names[$i] =$row[1];
        $i++;
    }

    $stmt="SELECT * FROM osdial_user_groups;";
    $rslt=mysql_query($stmt, $link);
    if (!isset($DB))   {$DB=0;}
    if ($DB) {$html .= "$stmt\n";}
    $usergroups_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $usergroups_to_print) {
        $row=mysql_fetch_row($rslt);
        $usergroups[$i] =$row[0];
        $i++;
    }


    $NFB = '<b><font size=6 face="courier">'; 
    $NFE = '</font></b>';
    $F=''; $FG=''; $B=''; $BG='';


    $html .= "<style type=\"text/css\">\n";
    $html .= "<!--\n";
    $html .= "   .green {color: white; background-color: green}\n";
    $html .= "   .red {color: white; background-color: red}\n";
    $html .= "   .lightblue {color: black; background-color: #ADD8E6}\n";
    $html .= "   .blue {color: white; background-color: blue}\n";
    $html .= "   .midnightblue {color: white; background-color: #191970}\n";
    $html .= "   .purple {color: white; background-color: purple}\n";
    $html .= "   .violet {color: black; background-color: #EE82EE} \n";
    $html .= "   .thistle {color: black; background-color: #D8BFD8} \n";
    $html .= "   .olive {color: white; background-color: #808000}\n";
    $html .= "   .yellow {color: black; background-color: yellow}\n";
    $html .= "   .khaki {color: black; background-color: #F0E68C}\n";
    $html .= "   .orange {color: black; background-color: orange}\n";
    $html .= "   .black {color: #FF0000; background-color: black}\n";
    #$html .= "   .pause0 {color: white; background-color: #FFEEBB}\n";
    #$html .= "   .pause1 {color: white; background-color: #FFCC99}\n";
    #$html .= "   .pause2 {color: white; background-color: #FF9966}\n";
    #$html .= "   .pause3 {color: white; background-color: #CC6633}\n";
    $html .= "   .pause0 {color: black; background-color: #FFDDDD}\n";
    $html .= "   .pause1 {color: black; background-color: #DDBBBB}\n";
    $html .= "   .pause2 {color: black; background-color: #DD7777}\n";
    $html .= "   .pause3 {color: white; background-color: #AA0000}\n";
    $html .= "   .dispo0 {color: black; background-color: #FFEECC}\n";
    $html .= "   .dispo1 {color: black; background-color: #EEFF99}\n";
    $html .= "   .dispo2 {color: black; background-color: #DDEE66}\n";
    $html .= "   .dispo3 {color: black; background-color: #BBBB66}\n";
    $html .= "   .wait0 {color: black; background-color: #DDDDFF}\n";
    $html .= "   .wait1 {color: black; background-color: #CCCCEE}\n";
    $html .= "   .wait2 {color: black; background-color: #9999DD}\n";
    $html .= "   .wait3 {color: white; background-color: #333366}\n";
    #$html .= "   .call0 {color: black; background-color: #DDFFDD} \n";
    #$html .= "   .call1 {color: black; background-color: #9CC375} \n";
    #$html .= "   .call2 {color: black; background-color: #77DD77} \n";
    #$html .= "   .call3 {color: white; background-color: #00AA00}\n";
    $html .= "   .call0 {color: black; background-color: #BDFF99} \n";
    $html .= "   .call1 {color: black; background-color: #9CDD75} \n";
    $html .= "   .call2 {color: white; background-color: #76AA59} \n";
    $html .= "   .call3 {color: white; background-color: #578F41}\n";
    #$html .= "   .pausecode {color: black; background-color: #99CC66}\n";
    $html .= "   .pausecode {color: black; background-color: #FF9966}\n";
    $html .= "   .outcamp {color: black; background-color: #CCEEFF}\n";
    $html .= "   .agtphn0 {color: #700000}\n";
    $html .= "   .agtphn1 {color: white; background-color: #700000}\n";
    $html .= "   .dead0 {color: black}\n";
    $html .= "   .dead1 {color: #FF0000; background-color: black}\n";

    $html .= "   .r1 {color: black; background-color: #FFCCCC}\n";
    $html .= "   .r2 {color: black; background-color: #FF9999}\n";
    $html .= "   .r3 {color: black; background-color: #FF6666}\n";
    $html .= "   .r4 {color: white; background-color: #FF0000}\n";
    $html .= "   .b1 {color: black; background-color: #CCCCFF}\n";
    $html .= "   .b2 {color: black; background-color: #9999FF}\n";
    $html .= "   .b3 {color: black; background-color: #6666FF}\n";
    $html .= "   .b4 {color: white; background-color: #0000FF}\n";


    $stmt=sprintf("SELECT group_id,group_color FROM osdial_inbound_groups WHERE group_id IN %s OR group_id LIKE 'A2A_%s%%';",$LOG['allowed_ingroupsSQL'],$company_prefix);
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $INgroups_to_print = mysql_num_rows($rslt);
    if (is_array($INgroups_to_print)) {
        if ($INgroups_to_print > 0) {
            $g=0;
            while ($g < $INgroups_to_print) {
                $row=mysql_fetch_row($rslt);
                $group_id[$g] = $row[0];
                $group_color[$g] = $row[1];
                $html .= "   .$group_id[$g] {color: black; background-color: $group_color[$g]}\n";
                $g++;
            }
        }
    }

    $html .= "\n-->\n";
    $html .= "</style>\n";

    $stmt = sprintf("SELECT count(*) FROM osdial_campaigns WHERE campaign_allow_inbound='Y' AND campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
    $rslt=mysql_query($stmt, $link);
    $row=mysql_fetch_row($rslt);
    $campaign_allow_inbound = $row[0];


    $html .= "<table align=center><tr><td>\n";
    $html .= "<font size=1>";

    $html .= "<div class=no-ul>";
    $html .= "<form action=\"$PHP_SELF\" method=get>\n";
    $html .= "<input type=hidden name=ADD value=$ADD>\n";
    $html .= "<input type=hidden name=SUB value=$SUB>\n";
    $html .= "<input type=hidden name=campaign_id value=$campaign_id>\n";
    $html .= "<input type=hidden name=group value=$group>\n";
    $html .= "<input type=hidden name=RR value=$RR>\n";

    $html .= "<p class=centered>";
    $html .= "<font color=$default_text size=+1>CAMPAIGN DETAILS</font><br><br>";

    $html .= "<font color=$default_text SIZE=2>";
    $html .= "Update:&nbsp;";

    if ($RR==38400) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
    $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=38400&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">Daily</a>&nbsp;&nbsp;"; 

    if ($RR==3600) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
    $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=3600&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">Hourly</a>&nbsp;&nbsp;";

    if ($RR==60) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
    $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=60&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">1min</a>&nbsp;&nbsp;";

    if ($RR==10) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
    $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=10&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">10sec</a>&nbsp;&nbsp;";

    if ($RR==4) { $html .= "</font><font size=+1>"; } else { $html .= "</font><font size=-1>"; }
    $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&campaign_id=$campaign_id&group=$group&RR=4&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">4sec</a>&nbsp;&nbsp;";

    $html .= "<font size=2>";

    if (!preg_match('/^XXXX/',$group)) $html .= "&nbsp;-&nbsp;<a href=\"./admin.php?ADD=31&campaign_id=$group\">Modify</a>";
    $html .= "&nbsp;-&nbsp;<a href=\"$PHP_SELF?ADD=$ADD&SUB=" . ($SUB - 1) . "&campaign_id=$campaign_id&group=$group&RR=$RR&DB=$DB&adastats=$adastats\">Summary</a>&nbsp;-&nbsp;\n";
    $html .= "<br></font>\n";
    $html .= "</font>";

    $html .= "<font color=$default_text size=-1>Campaign:</font>\n";
    $html .= "<input type=hidden name=ADD value=\"$ADD\">\n";
    $html .= "<input type=hidden name=SUB value=\"$SUB\">\n";
    $html .= "<input type=hidden name=RR value=\"$RR\">\n";
    $html .= "<input type=hidden name=DB value=\"$DB\">\n";
    $html .= "<input type=hidden name=adastats value=\"$adastats\">\n";
    $html .= "<input type=hidden name=SIPmonitorLINK value=\"$SIPmonitorLINK\">\n";
    $html .= "<input type=hidden name=IAXmonitorLINK value=\"$IAXmonitorLINK\">\n";
    $html .= "<input type=hidden name=usergroup value=\"$usergroup\">\n";
    $html .= "<input type=hidden name=UGdisplay value=\"$UGdisplay\">\n";
    $html .= "<input type=hidden name=UidORname value=\"$UidORname\">\n";
    $html .= "<input type=hidden name=orderby value=\"$orderby\">\n";
    $html .= "<input type=hidden name=orddir value=\"$orddir\">\n";
    $html .= "<input type=hidden name=SERVdisplay value=\"$SERVdisplay\">\n";
    $html .= "<input type=hidden name=CALLSdisplay value=\"$CALLSdisplay\">\n";
    $html .= "<input type=hidden name=VAdisplay value=\"$VAdisplay\">\n";
    $html .= "<input type=hidden name=cpuinfo value=\"$cpuinfo\">\n";

    $html .= "<select size=1 name=group>\n";
    $aasel=''; if ($group == "XXXX-ALL-ACTIVE-XXXX") $aasel = "selected";
    $html .= "<option value=\"XXXX-ALL-ACTIVE-XXXX\" $aasel>XXXX-ALL-ACTIVE-XXXX</option>\n";
    $outsel=''; if ($group == "XXXX-OUTBOUND-XXXX") $outsel = "selected";
    $html .= "<option value=\"XXXX-OUTBOUND-XXXX\" $outsel>XXXX-OUTBOUND-XXXX</option>\n";
    $insel=''; if ($group == "XXXX-INBOUND-XXXX") $insel = "selected";
    $html .= "<option value=\"XXXX-INBOUND-XXXX\" $insel>XXXX-INBOUND-XXXX</option>\n";
    $o=0;

    $group_name = '';
    while ($groups_to_print > $o) {
        $gsel='';
        if ($groups[$o] == $group) {
            $gsel = 'selected';
            $group_name = $group_names[$o];
        }
        $html .= "<option $gsel value=\"$groups[$o]\">" . mclabel($groups[$o]) . " - $group_names[$o]</option>\n";
        $o++;
    }
    $html .= "</select>\n";
    if ($UGdisplay > 0) {
        $html .= "<select size=1 name=usergroup>\n";
        $html .= "<option value=\"\">ALL USER GROUPS</option>\n";
        $o=0;
        while ($usergroups_to_print > $o) {
            if ($usergroups[$o] == $usergroup) {
                $html .= "<option selected value=\"$usergroups[$o]\">$usergroups[$o]</option>\n";
            } else {
                $html .= "<option value=\"$usergroups[$o]\">$usergroups[$o]</option>\n";
            }
            $o++;
        }
        $html .= "</select>\n";
    }
    $html .= "<input type=submit name=submit value=submit>";
    $html .= "<br>";


    if ($group) {
        $stmt="SELECT avg(auto_dial_level),min(dial_status_a),min(dial_status_b),min(dial_status_c),min(dial_status_d),min(dial_status_e),min(lead_order),min(lead_filter_id),sum(hopper_level),min(dial_method),avg(adaptive_maximum_level),avg(adaptive_dropped_percentage),avg(adaptive_dl_diff_target),avg(adaptive_intensity),min(available_only_ratio_tally),min(adaptive_latest_server_time),min(local_call_time),avg(dial_timeout),min(dial_statuses),active FROM osdial_campaigns";
        if ($group=='XXXX-ALL-ACTIVE-XXXX') {
            $stmt=sprintf("%s WHERE campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } elseif ($group=='XXXX-OUTBOUND-XXXX') {
            $stmt=sprintf("%s WHERE length(closer_campaigns)<6 AND campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } elseif ($group=='XXXX-INBOUND-XXXX') {
            $stmt=sprintf("%s WHERE length(closer_campaigns)>5 AND campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } else {
            $stmt=sprintf("%s WHERE campaign_id IN %s AND campaign_id='%s';",$stmt,$LOG['allowed_campaignsSQL'],mres($group));
        }
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $DIALlev =      sprintf('%3.2f',$row[0]);
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
        $active =       $row[19];

        $DIALstatuses = (preg_replace("/ -$|^ /","",$DIALstatuses));
        $DIALstatuses = (preg_replace('/ /',', ',$DIALstatuses));

        if ($group=='XXXX-ALL-ACTIVE-XXXX') {
            $stmt=sprintf("SELECT count(*) FROM osdial_hopper WHERE campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
        } elseif ($group=='XXXX-OUTBOUND-XXXX') {
            $stmt=sprintf("SELECT count(*) FROM osdial_hopper LEFT JOIN osdial_campaigns ON (osdial_hopper.campaign_id=osdial_campaigns.campaign_id) WHERE length(closer_campaigns)<6 AND osdial_hopper.campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
        } elseif ($group=='XXXX-INBOUND-XXXX') {
            $stmt=sprintf("SELECT count(*) FROM osdial_hopper LEFT JOIN osdial_campaigns ON (osdial_hopper.campaign_id=osdial_campaigns.campaign_id) WHERE length(closer_campaigns)>5 AND osdial_hopper.campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
        } else {
            $stmt=sprintf("SELECT count(*) FROM osdial_hopper WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
        }

        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $VDhop = $row[0];

        $stmt="SELECT sum(dialable_leads),sum(calls_today),sum(drops_today),avg(drops_answers_today_pct),avg(differential_onemin),avg(agents_average_onemin),sum(balance_trunk_fill),sum(answers_today),min(status_category_1),sum(status_category_count_1),min(status_category_2),sum(status_category_count_2),min(status_category_3),sum(status_category_count_3),min(status_category_4),sum(status_category_count_4),sum(status_category_hour_count_1),sum(status_category_hour_count_2),sum(status_category_hour_count_3),sum(status_category_hour_count_4),SUM(recycle_total),SUM(recycle_sched)";
        if ($group=='XXXX-ALL-ACTIVE-XXXX') {
            $stmt=sprintf("%s FROM osdial_campaign_stats WHERE campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } elseif ($group=='XXXX-OUTBOUND-XXXX') {
            $stmt=sprintf("%s FROM osdial_campaign_stats,osdial_campaigns WHERE osdial_campaign_stats.campaign_id=osdial_campaigns.campaign_id AND length(closer_campaigns)<6 AND osdial_campaign_stats.campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } elseif ($group=='XXXX-INBOUND-XXXX') {
            $stmt=sprintf("%s FROM osdial_campaign_stats,osdial_campaigns WHERE osdial_campaign_stats.campaign_id=osdial_campaigns.campaign_id AND length(closer_campaigns)>5 AND osdial_campaign_stats.campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } else {
            $stmt=sprintf("%s FROM osdial_campaign_stats WHERE campaign_id IN %s AND campaign_id='%s';",$stmt,$LOG['allowed_campaignsSQL'],mres($group));
        }

        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $DAleads =          $row[0];
        $callsTODAY =       $row[1];
        $dropsTODAY =       $row[2];
        $drpctTODAY =       sprintf('%3.2f',$row[3]);
        $diffONEMIN =       $row[4];
        $agentsONEMIN =     sprintf('%3.2f',$row[5]);
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

        if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) ) {
            $diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
            $diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
        } else {
            $diffpctONEMIN = '0.00';
        }

        $stmt="SELECT sum(local_trunk_shortage),sum(if(osdial_campaigns.active='Y',1,0)),sum(if(agent_pause_codes_active='Y' AND osdial_campaigns.active='Y',1,0)) FROM osdial_campaign_server_stats,osdial_campaigns WHERE osdial_campaign_server_stats.campaign_id=osdial_campaigns.campaign_id";
        if ($group=='XXXX-ALL-ACTIVE-XXXX') {
            $stmt=sprintf("%s AND osdial_campaign_server_stats.campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } elseif ($group=='XXXX-OUTBOUND-XXXX') {
            $stmt=sprintf("%s AND length(closer_campaigns)<6 AND osdial_campaign_server_stats.campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } elseif ($group=='XXXX-INBOUND-XXXX') {
            $stmt=sprintf("%s AND length(closer_campaigns)>5 AND osdial_campaign_server_stats.campaign_id IN %s;",$stmt,$LOG['allowed_campaignsSQL']);
        } else {
            $stmt=sprintf("%s AND osdial_campaign_server_stats.campaign_id IN %s AND osdial_campaign_server_stats.campaign_id='%s';",$stmt,$LOG['allowed_campaignsSQL'],mres($group));
        }
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $balanceSHORT = $row[0];
        $campaign_active=$row[1];
        $agent_pause_codes_active=$row[2];


        $html .= "</td></tr><tr><td align=left>";
        $html .= "<font class=indented color=#1C4754 size=2><b>$group - $group_name</b></font>";
        if (ereg("^XXXX",$group)) {
            $html .= '';
        } elseif ($active=='Y') {
            $html .="<font color='green' size='-1'>&nbsp;&nbsp;(Active)</font>";
        } else {
            $html .="<font color='red'>&nbsp;&nbsp;(In-Active)</font>";
        }
        $html .= "</td></tr><tr><td align=center>";
        $html .= "<table class=indents cellpadding=0 cellspacing=0><tr>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Dial Level:</b></td><td align=left><font size=2>&nbsp; $DIALlev&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Trunk Short/Fill:</b></td><td align=left><font size=2>&nbsp; $balanceSHORT / $balanceFILL&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Filter:</b></td><td align=left><font size=2>&nbsp; $DIALfilter&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Time:</b></td><td align=left><font size=2 color=$default_text>&nbsp; $NOW_TIME&nbsp;&nbsp;</td>";
        $html .= "</tr>";

        if ($adastats > 1) {
            $html .= "<tr bgcolor=\"#cccccc\">";
            $html .= "<td align=right><font size=2><b>Max Level:</b></td><td align=left><font size=2>&nbsp; $maxDIALlev&nbsp;&nbsp;</td>";
            $html .= "<td align=right><font size=2><b>Dropped Max:</b></td><td align=left><font size=2>&nbsp; $DROPmax%&nbsp;&nbsp;</td>";
            $html .= "<td align=right><font size=2><b>Target Diff:</b></td><td align=left><font size=2>&nbsp; $targetDIFF&nbsp;&nbsp;</td>";
            $html .= "<td align=right><font size=2><b>Intensity:</b></td><td align=left><font size=2>&nbsp; $ADAintense&nbsp;&nbsp;</td>";
            $html .= "</tr>";

            $html .= "<tr bgcolor=\"#cccccc\">";
            $html .= "<td align=right><font size=2><b>Dial Timeout:</b></td><td align=left><font size=2>&nbsp; $DIALtimeout&nbsp;&nbsp;</td>";
            $html .= "<td align=right><font size=2><b>Taper Time:</b></td><td align=left><font size=2>&nbsp; $TAPERtime&nbsp;&nbsp;</td>";
            $html .= "<td align=right><font size=2><b>Local Time:</b></td><td align=left><font size=2>&nbsp; $CALLtime&nbsp;&nbsp;</td>";
            $html .= "<td align=right><font size=2><b>Avail Only:</b></td><td align=left><font size=2>&nbsp; $ADAavailonly&nbsp;&nbsp;</td>";
            $html .= "</tr>";

            $html .= "<tr bgcolor=\"#cccccc\">";
            $html .= "<td align=right><font size=2><b>DL Diff:</b></td><td align=left><font size=2>&nbsp; $diffONEMIN&nbsp;&nbsp;</td>";
            $html .= "<td align=right><font size=2><b>Diff:</b></td><td align=left><font size=2>&nbsp; $diffpctONEMIN%&nbsp;&nbsp;</td>";
            $html .= "<td align=right><font size=2 color=$default_text><b>Avg Agents:</b></td><td align=left><font size=2>&nbsp; $agentsONEMIN&nbsp;&nbsp;</td>";
            $html .= "</tr>";
        }

        $html .= "<tr>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Dialable Leads:</b></td><td align=left><font size=2>&nbsp; $DAleads&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Recycles/Sched:</b></td><td align=left><font size=2>&nbsp; $recycle_total / $recycle_sched&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Calls Today:</b></td><td align=left><font size=2>&nbsp; $callsTODAY&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Dial Method:</b></td><td align=left><font size=2>&nbsp; $DIALmethod&nbsp;&nbsp;</td>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Hopper Level:</b></td><td align=left><font size=2>&nbsp; $HOPlev&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Drop/Answer:</b></td><td align=left><font size=2>&nbsp; $dropsTODAY / $answersTODAY&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Statuses:</b></td><TD ALIGN=LEFT colspan=3><font size=2>&nbsp; <span title=\"$DIALstatuses\">" . ellipse($DIALstatuses,40,true) . "</span>&nbsp;&nbsp;</td>";
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Leads In Hopper:</b></td><td align=left><font size=2>&nbsp; $VDhop&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Drop %:</b></td><td align=left><font size=2>&nbsp; ";
        if ($drpctTODAY >= $DROPmax) {
            $html .= "<font color=red><b>$drpctTODAY%</b></font>";
        } else {
            $html .= "$drpctTODAY%";
        }
        $html .= "&nbsp;&nbsp;</td>";
        $html .= "<td align=right><font size=2 color=$default_text><b>Order:</b></td><td align=left><font size=2>&nbsp; $DIALorder&nbsp;&nbsp;</td>";
        $html .= "</tr><tr>";
        if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
            $html .= "<td align=right><font size=2 color=$default_text><b>$VSCcat1:</b></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1tally&nbsp;&nbsp;</td>\n";
        }
        if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
            $html .= "<td align=right><font size=2 color=$default_text><b>$VSCcat2:</b></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2tally&nbsp;&nbsp;</td>\n";
        }
        if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) {
            $html .= "<td align=right><font size=2 color=$default_text><b>$VSCcat3:</b></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3tally&nbsp;&nbsp;</td>\n";
        }
        if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
            $html .= "<td align=right><font size=2 color=$default_text><b>$VSCcat4:</b></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4tally&nbsp;&nbsp;</td>\n";
        }
        $html .= "</tr><tr>";
        if ( (!eregi('NULL',$VSCcat1)) and (strlen($VSCcat1)>0) ) {
            $html .= "<td align=right><font size=2 color=$default_text><b>$VSCcat1/hr:</b></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat1hourtally&nbsp;&nbsp;</td>\n";
        }
        if ( (!eregi('NULL',$VSCcat2)) and (strlen($VSCcat2)>0) ) {
            $html .= "<td align=right><font size=2 color=$default_text><b>$VSCcat2/hr:</b></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat2hourtally&nbsp;&nbsp;</td>\n";
        }
        if ( (!eregi('NULL',$VSCcat3)) and (strlen($VSCcat3)>0) ) {
            $html .= "<td align=right><font size=2 color=$default_text><b>$VSCcat3/hr:</b></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat3hourtally&nbsp;&nbsp;</td>\n";
        }
        if ( (!eregi('NULL',$VSCcat4)) and (strlen($VSCcat4)>0) ) {
            $html .= "<td align=right><font size=2 color=$default_text><b>$VSCcat4/hr:</b></td><td align=left><font size=2>&nbsp;&nbsp;$VSCcat4hourtally&nbsp;&nbsp;</td>\n";
        }
        $html .= "</tr>";

        $html .= "<tr>";
        $html .= "<td align=left colspan=8>";

        if ($adastats<2) {
            $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=2&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>VIEW MORE SETTINGS</font></a>";
        } else {
            $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=1&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>VIEW LESS SETTINGS</font></a>";
        }
        if ($UGdisplay>0) {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=0&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>HIDE USER GROUP</font></a>";
        } else {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=1&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>VIEW USER GROUP</font></a>";
        }
        if ($UidORname>0) {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=0&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>SHOW AGENT ID</font></a>";
        } else {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=1&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>SHOW AGENT NAME</font></a>";
        }
        if ($SERVdisplay>0) {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=0&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>HIDE SERVER INFO</font></a>";
        } else {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=1&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>SHOW SERVER INFO</font></a>";
        }
        if ($CALLSdisplay>0) {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=0&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>HIDE WAITING CALLS DETAIL</font></a>";
        } else {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=1&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\"><font size=1>SHOW WAITING CALLS DETAIL</font></a>";
        }
        if ($VAdisplay==0) {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=1&cpuinfo=$cpuinfo\"><font size=1>SHOW LIVE VIRTUAL AGENTS</font></a>";
        } elseif ($VAdisplay==1) {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=2&cpuinfo=$cpuinfo\"><font size=1>SHOW ALL VIRTUAL AGENTS</font></a>";
        } else {
            $html .= " &nbsp; &nbsp; &nbsp; <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=0&cpuinfo=$cpuinfo\"><font size=1>HIDE VIRTUAL AGENTS</font></a>";
        }
        $html .= "</td>";
        $html .= "</tr>";
        $html .= "</table>";

        $html .= "</form>\n\n";
        $html .= "<br>";
    }

    ###################################################################################
    ###### INBOUND/OUTBOUND CALLS
    ###################################################################################
    if ($campaign_allow_inbound > 0) {
        if (strlen($group) > 1) {
            $stmt=sprintf("SELECT closer_campaigns FROM osdial_campaigns WHERE campaign_id IN %s AND campaign_id='%s';",$LOG['allowed_campaignsSQL'],mres($group));
        } else {
            $stmt=sprintf("SELECT closer_campaigns FROM osdial_campaigns WHERE campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
        }
        $rslt=mysql_query($stmt, $link);
        $ccamps_to_print = mysql_num_rows($rslt);
        $c=0;
        $closer_campaignsSQL='';
        while ($ccamps_to_print > $c) {
            $row=mysql_fetch_row($rslt);
            $closer_campaigns = preg_replace("/^ | -$/","",$row[0]);
            $closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
            $closer_campaignsSQL .= "'$closer_campaigns',";
            $c++;
        }
        $closer_campaignsSQL = preg_replace('/,$/','',$closer_campaignsSQL);

        #$stmtB=sprintf("FROM osdial_auto_calls WHERE campaign_id IN %s AND status NOT IN('XFER') AND ( (call_type='IN' AND campaign_id IN(%s)) OR (campaign_id='%s' AND call_type IN('OUT','OUTBALANCE')) ) ORDER BY campaign_id,call_time;",$LOG['allowed_campaignsSQL'],$closer_campaignsSQL,mres($group));
        $stmtB=sprintf("FROM osdial_auto_calls WHERE status NOT IN('XFER') AND ( (call_type='IN' AND campaign_id IN(%s)) OR (campaign_id='%s' AND call_type IN('OUT','OUTBALANCE')) ) ORDER BY campaign_id,call_time;",$closer_campaignsSQL,mres($group));
    } else {
        $groupSQL = '';
        if (!preg_match('/^XXXX/',$group)) {
            $groupSQL = sprintf(" AND campaign_id='%s'",mres($group));
        }

        $stmtB=sprintf("FROM osdial_auto_calls WHERE campaign_id IN %s AND status NOT IN('XFER') %s ORDER BY campaign_id,call_time;", $LOG['allowed_campaignsSQL'],$groupSQL);
    }

    $stmtA = "SELECT status";
    if ($CALLSdisplay > 0) $stmtA .= ",campaign_id,phone_number,server_ip,UNIX_TIMESTAMP(call_time),call_type";


    $k=0;
    $stmt = $stmtA . ' ' . $stmtB;
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $parked_to_print = mysql_num_rows($rslt);
    if ($parked_to_print > 0) {
        $i=0;
        $out_total=0;
        $out_ring=0;
        $out_live=0;
        while ($i < $parked_to_print) {
            $row=mysql_fetch_row($rslt);

            if ($row[0]=='LIVE') {
                $out_live++;

                if ($CALLSdisplay > 0) {
                    $CDstatus[$k] =            $row[0];
                    $CDcampaign_id[$k] =    $row[1];
                    $CDphone_number[$k] =    $row[2];
                    $CDserver_ip[$k] =        $row[3];
                    $CDcall_time[$k] =        $row[4];
                    $CDcall_type[$k] =        $row[5];
                    $k++;
                }
            } elseif ($row[0]=='CLOSER') {
                $nothing=1;
            } else {
                $out_ring++;
            }

            $out_total++;
            $i++;
        }

        if ($out_live > 0) {$F='<font class="r1">'; $FG='</font>';}
        if ($out_live > 4) {$F='<font class="r2">'; $FG='</font>';}
        if ($out_live > 9) {$F='<font class="r3">'; $FG='</font>';}
        if ($out_live > 14) {$F='<font class="r4">'; $FG='</font>';}

        if ($campaign_allow_inbound > 0) {
            $html .= "$NFB$out_total$NFE <font color=blue>current active calls</font> &nbsp; &nbsp; &nbsp; \n";
        } else {
            $html .= "$NFB$out_total$NFE <font color=blue>calls being placed</font> &nbsp; &nbsp; &nbsp; \n";
        }

        $html .= "$NFB$out_ring$NFE <font color=blue>calls ringing</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
        $html .= "$NFB$F &nbsp;$out_live $FG$NFE <font color=blue>calls waiting for agents</font> &nbsp; &nbsp; &nbsp; \n";
    } else {
        $html .= "&nbsp;<font color=red>NO LIVE CALLS WAITING</font>&nbsp;";
    }



    // Changed to draw solid lines
    $LNtopleft    ="&#x2554;";
    $LNleft       ="&#x2551;";
    $LNright      ="&#x2551;";
    $LNcenterleft ="&#x255F;";
    $LNcenterbar  ="&#x2502;";
    $LNtopdown    ="&#x2564;";
    $LNtopright   ="&#x2557;";
    $LNbottomleft ="&#x255A;";
    $LNbottomright="&#x255D;";
    $LNcentcross  ="&#x253C;";
    $LNcentright  ="&#x2562;";
    $LNbottomup   ="&#x2567;";



    ###################################################################################
    ###### CALLS WAITING
    ###################################################################################
    $Chtml = '';
    $Chtml .= "<font color=$default_text>&nbsp;&nbsp;Calls Waiting                      $NOW_TIME\n";
    $Chtml .=$LNtopleft.HorizLine(8).$LNtopdown.HorizLine(17).$LNtopdown.HorizLine(14).$LNtopdown.HorizLine(17).$LNtopdown.HorizLine(10).$LNtopdown.HorizLine(12).$LNtopright."\n";
    $Chtml .="$LNleft STATUS $LNcenterbar    CAMPAIGN     $LNcenterbar PHONE NUMBER $LNcenterbar    SERVER_IP    $LNcenterbar DIALTIME $LNcenterbar CALL TYPE  $LNright\n";
    $Chtml .=$LNcenterleft.CenterLine(8).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(14).$LNcentcross.CenterLine(17).$LNcentcross.CenterLine(10).$LNcentcross.CenterLine(12).$LNcentright."\n";

    $p=0;
    while($p<$k) {
        $Cstatus =        sprintf("%-6s", $CDstatus[$p]);
        $Ccampaign_id =   sprintf("%-15s", preg_replace('/&nbsp;/',' ',mclabel($CDcampaign_id[$p])));
        $Cphone_number =  sprintf("%-12s", $CDphone_number[$p]);
        $Cserver_ip =     sprintf("%-15s", $CDserver_ip[$p]);
        $Ccall_type =     sprintf("%-10s", $CDcall_type[$p]);

        $Ccall_time_S =     ($STARTtime - $CDcall_time[$p]);
        $Ccall_time_M =     ($Ccall_time_S / 60);
        $Ccall_time_M =     round($Ccall_time_M, 2);
        $Ccall_time_M_int = intval($Ccall_time_M);
        $Ccall_time_SEC =   ($Ccall_time_M - $Ccall_time_M_int);
        $Ccall_time_SEC =   ($Ccall_time_SEC * 60);
        $Ccall_time_SEC =   round($Ccall_time_SEC, 0);
        if ($Ccall_time_SEC < 10) {$Ccall_time_SEC = "0$Ccall_time_SEC";}
        $Ccall_time_MS =    "$Ccall_time_M_int:$Ccall_time_SEC";
        $Ccall_time_MS =    sprintf("%7s", $Ccall_time_MS) . ' ';

        $G ='<span class="outcamp">';
        $EG='</span>';
        if ($CDcall_type[$p] == 'IN')    {
            $G="<span class=\"$CDcampaign_id[$p]\">";
            $EG='</span>';
        }

        $Chtml .= "$LNleft$G $Cstatus $LNcenterbar $Ccampaign_id $LNcenterbar $Cphone_number $LNcenterbar $Cserver_ip $LNcenterbar $Ccall_time_MS $LNcenterbar $Ccall_type $EG$LNright\n";

        $p++;
    }

    $Chtml .=$LNbottomleft.HorizLine(8).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(14).$LNbottomup.HorizLine(17).$LNbottomup.HorizLine(10).$LNbottomup.HorizLine(12).$LNbottomright."\n</font>";

    if ($p<1) {$Chtml='';}

    ###################################################################################
    ###### TIME ON SYSTEM
    ###################################################################################

    $orderby .= $orddir;
    if ($orddir == 'up') { $orddir = 'down'; } else { $orddir = 'up'; }

    if ($orderby=='extenup')      {$orderSQL='osdial_live_agents.extension';}
    if ($orderby=='extendown')    {$orderSQL='osdial_live_agents.extension desc';}
    if ($orderby=='callsup')      {$orderSQL='osdial_live_agents.calls_today';}
    if ($orderby=='callsdown')    {$orderSQL='osdial_live_agents.calls_today desc';}
    if ($orderby=='confup')       {$orderSQL='osdial_live_agents.conf_exten,osdial_live_agents.server_ip desc';}
    if ($orderby=='confdown')     {$orderSQL='osdial_live_agents.conf_exten desc,osdial_live_agents.server_ip desc';}
    if ($orderby=='serverup')     {$orderSQL='osdial_live_agents.server_ip,osdial_live_agents.conf_exten desc';}
    if ($orderby=='serverdown')   {$orderSQL='osdial_live_agents.server_ip desc,osdial_live_agents.conf_exten desc';}
    if ($orderby=='statusup')     {$orderSQL='osdial_live_agents.status,osdial_live_agents.last_call_finish desc';}
    if ($orderby=='statusdown')   {$orderSQL='osdial_live_agents.status desc,osdial_live_agents.last_call_finish desc';}
    if ($orderby=='timeup')       {$orderSQL='osdial_live_agents.last_call_time';}
    if ($orderby=='timedown')     {$orderSQL='osdial_live_agents.last_call_time desc';}
    if ($UidORname > 0) {
        if ($orderby=='userup')       {$orderSQL='osdial_users.full_name';}
        if ($orderby=='userdown')     {$orderSQL='osdial_users.full_name desc';}
        if ($orderby=='groupup')      {$orderSQL='osdial_users.user_group,osdial_live_agents.full_name desc';}
        if ($orderby=='groupdown')    {$orderSQL='osdial_users.user_group desc,osdial_live_agents.full_name desc';}
        if ($orderby=='campaignup')   {$orderSQL='osdial_live_agents.campaign_id,osdial_live_agents.full_name desc';}
        if ($orderby=='campaigndown') {$orderSQL='osdial_live_agents.campaign_id desc,osdial_live_agents.full_name desc';}
    } else {
        if ($orderby=='userup')       {$orderSQL='osdial_live_agents.user';}
        if ($orderby=='userdown')     {$orderSQL='osdial_live_agents.user desc';}
        if ($orderby=='groupup')      {$orderSQL='osdial_users.user_group,osdial_live_agents.user desc';}
        if ($orderby=='groupdown')    {$orderSQL='osdial_users.user_group desc,osdial_live_agents.user desc';}
        if ($orderby=='campaignup')   {$orderSQL='osdial_live_agents.campaign_id,osdial_live_agents.user desc';}
        if ($orderby=='campaigndown') {$orderSQL='osdial_live_agents.campaign_id desc,osdial_live_agents.user desc';}
    }

    $agent_incall=0;
    $agent_ready=0;
    $agent_paused=0;
    $agent_dead=0;
    $agent_total=0;
    $Ahtml = '';
    $Ahtml .= "<font color=$default_text size=1 face=fixed,monospace>&nbsp;&nbsp;Agents Time On Calls Campaign: $group                    $NOW_TIME\n";


    $HDstation = HorizLine(15).$LNtopdown; //12
    $HTstation = "   <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=exten&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">STATION</a>     ".$LNcenterbar;
    $HXstation = CenterLine(15).$LNcentcross;
    $HBstation = HorizLine(15).$LNbottomup;
    $HDuser = HorizLine(20).$LNtopdown; //20
    $HTuser = "      <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=user&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">USER</a>          ".$LNcenterbar;
    $HXuser = CenterLine(20).$LNcentcross;
    $HBuser = HorizLine(20).$LNbottomup;
    $HDusergroup ='';
    $HTusergroup ='';
    $HXusergroup ='';
    $HBusergroup ='';
    if ($UGdisplay > 0) {
        $HDusergroup = HorizLine(14).$LNtopdown; //14
        $HTusergroup = " <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=group&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">USER GROUP</a>   ".$LNcenterbar;
        $HXusergroup = CenterLine(14).$LNcentcross;
        $HBusergroup = HorizLine(14).$LNbottomup;
    }
    if ( ($SIPmonitorLINK<1) and ($IAXmonitorLINK<1) ) {
        $HDsessionid = HorizLine(11).$LNtopdown; //11
        $HTsessionid = " <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=conf&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">SESSIONID</a> ".$LNcenterbar;
        $HXsessionid = CenterLine(11).$LNcentcross;
        $HBsessionid = HorizLine(11).$LNbottomup;
        $HDmonitor = '';
        $HTmonitor = '';
        $HXmonitor = '';
        $HBmonitor = '';
    } else {
        $HDsessionid = HorizLine(11); //11
        $HTsessionid = " <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=conf&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">SESSIONID</a> ";
        $HXsessionid = CenterLine(11);
        $HBsessionid = HorizLine(11);
        $HDmonitor = HorizLine(7).$LNtopdown;
        $HTmonitor = "       ".$LNcenterbar;
        $HXmonitor = CenterLine(7).$LNcentcross;
        $HBmonitor = HorizLine(7).$LNbottomup;
    }
    $HDstatus = HorizLine(10).$LNtopdown; //10
    $HTstatus = "  STATUS  ".$LNcenterbar;
    $HTstatus = "  <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=status&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">STATUS</a>  ".$LNcenterbar;
    $HXstatus = CenterLine(10).$LNcentcross;
    $HBstatus = HorizLine(10).$LNbottomup;
    $HDpause = '';
    $HTpause = '';
    $HXpause = '';
    $HBpause = '';
    if ($agent_pause_codes_active>0) {
        $HDpause = HorizLine(8).$LNtopdown; //8
        $HTpause = " PAUSE  ".$LNcenterbar;
        $HXpause = CenterLine(8).$LNcentcross;
        $HBpause = HorizLine(8).$LNbottomup;
    }
    $HDserver_ip = '';
    $HTserver_ip = '';
    $HXserver_ip = '';
    $HBserver_ip = '';
    $HDcall_server_ip = '';
    $HTcall_server_ip = '';
    $HXcall_server_ip = '';
    $HBcall_server_ip = '';
    if ($SERVdisplay > 0) {
        $HDserver_ip = HorizLine(17).$LNtopdown; //17
        $HTserver_ip = "    <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=server&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">SERVER IP</a>    ".$LNcenterbar;
        $HXserver_ip = CenterLine(17).$LNcentcross;
        $HBserver_ip = HorizLine(17).$LNbottomup;
        $HDcall_server_ip = HorizLine(17).$LNtopdown; //17
        $HTcall_server_ip = " CALL SERVER IP  ".$LNcenterbar;
        $HXcall_server_ip = CenterLine(17).$LNcentcross;
        $HBcall_server_ip = HorizLine(17).$LNbottomup;
    }
    $HDtime = HorizLine(9).$LNtopdown; //9
    $HTtime = "  <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=time&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">MM:SS</a>  ".$LNcenterbar;
    $HXtime = CenterLine(9).$LNcentcross;
    $HBtime = HorizLine(9).$LNbottomup;
    $HDcampaign = HorizLine(15).$LNtopdown; //15
    $HTcampaign = "   <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=campaign&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&cpuinfo=$cpuinfo\">CAMPAIGN</a>    ".$LNcenterbar;
    $HXcampaign = CenterLine(15).$LNcentcross;
    $HBcampaign = HorizLine(15).$LNbottomup;
    $HDcalls = HorizLine(7).$LNtopdown; //7
    $HTcalls = " <a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=calls&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=$cpuinfo\">CALLS</a> ".$LNcenterbar;
    $HXcalls = CenterLine(7).$LNcentcross;
    $HBcalls = HorizLine(7).$LNbottomup;
    $HDingrp = HorizLine(21); //21
    $HTingrp = "      INGROUP        ";
    $HXingrp = CenterLine(21);
    $HBingrp = HorizLine(21);


    $Ahtml .= $LNtopleft.   $HDstation.$HDuser.$HDusergroup.$HDsessionid.$HDmonitor.$HDstatus.$HDpause.$HDserver_ip.$HDcall_server_ip.$HDtime.$HDcampaign.$HDcalls.$HDingrp.$LNtopright."\n";
    $Ahtml .= $LNleft.      $HTstation.$HTuser.$HTusergroup.$HTsessionid.$HTmonitor.$HTstatus.$HTpause.$HTserver_ip.$HTcall_server_ip.$HTtime.$HTcampaign.$HTcalls.$HTingrp.$LNright."\n";
    $Ahtml .= $LNcenterleft.$HXstation.$HXuser.$HXusergroup.$HXsessionid.$HXmonitor.$HXstatus.$HXpause.$HXserver_ip.$HXcall_server_ip.$HXtime.$HXcampaign.$HXcalls.$HXingrp.$LNcentright."\n";

    $Dline  = $LNbottomleft.$HBstation.$HBuser.$HBusergroup.$HBsessionid.$HBmonitor.$HBstatus.$HBpause.$HBserver_ip.$HBcall_server_ip.$HBtime.$HBcampaign.$HBcalls.$HBingrp.$LNbottomright."</font>\n";


    if ($group=='XXXX-ALL-ACTIVE-XXXX') {
        $groupSQL = '';
    } elseif ($group=='XXXX-OUTBOUND-XXXX') {
        $groupSQL = ' and length(osdial_live_agents.closer_campaigns)<6';
    } elseif ($group=='XXXX-INBOUND-XXXX') {
        $groupSQL = ' and length(osdial_live_agents.closer_campaigns)>5';
    } else {
        $groupSQL = sprintf(" and campaign_id='%s'",mres($group));
    }
    $usergroupSQL = '';
    if (strlen($usergroup)>0) $usergroupSQL = sprintf(" and user_group='%s'",mres($usergroup));

      $vadispSQL = "";
    if ($VAdisplay == 0) {
      $vadispSQL = "AND osdial_live_agents.extension NOT LIKE 'R/va%'";
    } elseif ($VAdisplay == 1) {
      $vadispSQL = "AND (osdial_live_agents.extension NOT LIKE 'R/va%' OR status!='READY')";
    }

    $stmt=sprintf("SELECT extension,osdial_live_agents.user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),UNIX_TIMESTAMP(last_update_time),call_server_ip,osdial_live_agents.campaign_id,osdial_users.user_group,osdial_users.full_name,osdial_live_agents.comments,osdial_live_agents.calls_today,osdial_live_agents.callerid,lead_id,osdial_live_agents.uniqueid,osdial_live_agents.channel FROM osdial_live_agents,osdial_users WHERE campaign_id IN %s AND osdial_live_agents.user=osdial_users.user %s %s %s ORDER BY %s;",$LOG['allowed_campaignsSQL'],$groupSQL,$usergroupSQL,$vadispSQL,$orderSQL);
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $talking_to_print = mysql_num_rows($rslt);
    if ($talking_to_print > 0) {
        $i=0;
        while ($i < $talking_to_print) {
            $row=mysql_fetch_row($rslt);

            $Aextension[$i] =       $row[0];
            $Auser[$i] =            $row[1];
            $Asessionid[$i] =       $row[2];
            $Astatus[$i] =          $row[3];
            $Aserver_ip[$i] =       $row[4];
            $Acall_time[$i] =       $row[5];
            $Acall_finish[$i] =     $row[6];
            $Acall_lastupdate[$i] = $row[7];
            $Acall_server_ip[$i] =  $row[8];
            $Acampaign_id[$i] =     $row[9];
            $Auser_group[$i] =      $row[10];
            $Afull_name[$i] =       $row[11];
            $Acomments[$i] =        $row[12];
            $Acalls_today[$i] =     $row[13];
            $Acallerid[$i] =        $row[14];
            $Alead_id[$i] =         $row[15];
            $Auniqueid[$i] =        $row[16];
            $Achannel[$i] =         $row[17];

            $i++;
        }


        $i=0;
        while ($i < $talking_to_print) {
            if (eregi('SIP/',$Aextension[$i])) {
                $protocol = 'SIP';
                $dialplan = preg_replace('/^SIP\/|-.*$/i',"",$Aextension[$i]);
                $exten = sprintf("extension='%s'",mres($dialplan));
            } elseif (eregi('IAX2/',$Aextension[$i])) {
                $protocol = 'IAX2';
                $dialplan = preg_replace('/^IAX2\/|-.*$/i',"",$Aextension[$i]);
                $exten = sprintf("extension='%s'",mres($dialplan));
            } elseif (eregi("Local/",$Aextension[$i])) {
                $protocol = 'EXTERNAL';
                $dialplan = preg_replace('/^Local\/|\@.*$/i',"",$Aextension[$i]);
                $exten = sprintf("dialplan_number='%s'",mres($dialplan));
            } elseif (eregi('DAHDI/',$Aextension[$i])) {
                $protocol = 'DAHDI';
                $dialplan = preg_replace('/^DAHDI\//i',"",$Aextension[$i]);
                $exten = sprintf("extension='%s'",mres($dialplan));
            } elseif (eregi('Zap/',$Aextension[$i])) {
                $protocol = 'Zap';
                $dialplan = preg_replace('/^Zap\//i',"",$Aextension[$i]);
                $exten = sprintf("extension='%s'",mres($dialplan));
            } elseif (eregi("R/",$Aextension[$i])) {
                $protocol = 'EXTERNAL';
                $dialplan = preg_replace('/^R\/|\@.*$/i',"",$Aextension[$i]);
                $exten = sprintf("dialplan_number='%s'",mres($dialplan));
            }

            $stmt=sprintf("SELECT login FROM phones WHERE server_ip='%s' AND %s AND protocol='%s';",mres($Aserver_ip[$i]),$exten,mres($protocol));
            $rslt=mysql_query($stmt, $link);
            if ($DB) {echo "$stmt\n";}
            $phones_to_print = mysql_num_rows($rslt);
            if ($phones_to_print > 0) {
                $row=mysql_fetch_row($rslt);
                $Alogin[$i] = "$row[0]-----$i";
            } else {
                $Alogin[$i] = "$Aextension[$i]-----$i";
            }

            if ($Acomments[$i] == "INBOUND" or preg_match('/^R\//',$Aextension[$i])) {
                $Auser_connected[$i] = 1;
            } else {
                $stmt=sprintf("SELECT count(*) FROM live_sip_channels WHERE channel LIKE '%s%%' AND server_ip='%s' AND extension='%s';",mres($Achannel[$i]),mres($Aserver_ip[$i]),mres($Asessionid[$i]));
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $Auser_connected[$i] = $row[0];
                if ($Auser_connected[$i] == 0) {
                    $stmt=sprintf("SELECT count(*) FROM live_sip_channels WHERE channel LIKE '%s%%' AND server_ip='%s' AND extension='%s';",mres($Aextension[$i]),mres($Aserver_ip[$i]),mres($Asessionid[$i]));
                    $rslt=mysql_query($stmt, $link);
                    $row=mysql_fetch_row($rslt);
                    $Auser_connected[$i] = $row[0];
                }
            }

            $i++;
        }




        $j=0;
        $agentcount=0;
        while ($j < $talking_to_print) {
            $phone_split = explode("-----",$Alogin[$j]);
            $i = $phone_split[1];

            $extension =        sprintf("%-13s",preg_replace('/&nbsp;/',' ',mclabel($phone_split[0])));
            while(strlen($extension)>13) {
                $extension = substr($extension, 0, -1);
            }

            $phone =            sprintf("%-10s", $phone_split[0]);
            $Luser =            $Auser[$i];
            $user =             sprintf("%-18s", preg_replace('/&nbsp;/',' ',mclabel($Auser[$i])));
            $Lstatus =          $Astatus[$i];
            $status =           sprintf("%-6s", $Astatus[$i]);
            $Lsessionid =       $Asessionid[$i];
            #if ($Auser_connected[$i] == 0) { $Lsessionid='-------'; $Lstatus='AGTPHN'; }
            if ($Auser_connected[$i] == 0) { $Lstatus='AGTPHN'; }
            $sessionid =        sprintf("%-9s", $Lsessionid);
            $server_ip =        sprintf("%-15s", $Aserver_ip[$i]);
            $call_server_ip =   sprintf("%-15s", $Acall_server_ip[$i]);
            $campaign_id =      sprintf("%-13s", preg_replace('/&nbsp;/',' ',mclabel($Acampaign_id[$i])));
            $comments=          $Acomments[$i];
            if ($Acalls_today[$i]=='') $Acalls_today[$i] = '0';
            $calls_today =      sprintf("%5s", $Acalls_today[$i]);
            $lead_id =          $Alead_id[$i];
            $Lcallerid =        $Acallerid[$i];
            $Luser_group =      $Auser_group[$i];
            $Lfull_name =       $Afull_name[$i];
            $Lserver_ip =       $Aserver_ip[$i];
            $Lcall_server_ip =  $Acall_server_ip[$i];
            $Luniqueid =        $Auniqueid[$i];
            $Lchannel =         $Achannel[$i];


            if ($Lstatus=='PAUSED' and $comments=='DISPO') $Lstatus = 'DISPO';


            if ($Lstatus=='INCALL' and ($Lcallerid != '' or $Luniqueid > 0)) {
                #echo "$Lstatus|$lead_id|$Lcallerid|$Luniqueid|$Lchannel|$Lcomments<br>";
                $pollserver = $Lcall_server_ip;
                if ($pollserver == '') $pollserver = $Lserver_ip;
                if ($Luniqueid > 0) {
                    $stmtB = sprintf("SELECT uniqueid,caller_code,end_time,end_epoch FROM call_log WHERE uniqueid='%s' AND server_ip='%s' AND end_epoch>0 limit 1;",mres($Luniqueid),mres($pollserver));
                } elseif ($Lcallerid != '') {
                    $stmtB = sprintf("SELECT uniqueid,caller_code,end_time,end_epoch FROM call_log WHERE caller_code='%s' AND server_ip='%s' AND end_epoch>0 limit 1;",mres($Lcallerid),mres($pollserver));
                }
                #echo $stmtB . '<br>';
                $rsltB=mysql_query($stmtB, $link);
                $dead_cnt = mysql_num_rows($rslt);
                if ($dead_cnt > 0) {
                    $rowB=mysql_fetch_row($rsltB);
                    $DEDuniqueid  = $rowB[0];
                    $DEDcallerid  = $rowB[1];
                    $DEDend_time  = $rowB[2];
                    $DEDend_epoch = $rowB[3];
                    if ($DEDend_epoch > 0) $Lstatus = 'DEAD';
                }
            }

            $CM=' ';
            $lstatus='';
            if ($Lstatus=='INCALL') {
                if ($comments=='AUTO') {
                    $CM='A';
                } elseif ($comments=="INBOUND") {
                    $CM='I';
                } else {
                    $CM='M';
                }
                if ($lead_id > 0) {
                    $stmtB = sprintf("SELECT status FROM osdial_list WHERE lead_id='%s' AND status LIKE 'V%%';",mres($lead_id));
                    $rsltB=mysql_query($stmtB, $link);
                    $rowB=mysql_fetch_row($rsltB);
                    if ($rowB[0] != '') $lstatus = sprintf("%-6s",$rowB[0]);
                }
            }

            if ($UGdisplay > 0) {
                $user_group = sprintf("%-12s", preg_replace('/&nbsp;/',' ',mclabel($Luser_group)));
                while(strlen($user_group)>12) {
                    $user_group = substr($user_group, 0, -1);
                }
            }

            if ($UidORname > 0) {
                $user = sprintf("%-18s", $Lfull_name);
                while(strlen($user)>18) {
                    $user = substr($user, 0, -1);
                }
            }

            $LENDtime = $Acall_finish[$i];
            if (preg_match('/INCALL|QUEUE/i',$Astatus[$i])) $LENDtime = $Acall_time[$i];
            if ($Lstatus=='DEAD') $LENDtime = $DEDend_epoch;

            $call_time_S = ($STARTtime - $LENDtime);

            $call_time_M = ($call_time_S / 60);
            $call_time_M = round($call_time_M, 2);
            $call_time_M_int = intval($call_time_M);
            $call_time_SEC = ($call_time_M - $call_time_M_int);
            $call_time_SEC = ($call_time_SEC * 60);
            $call_time_SEC = round($call_time_SEC, 0);
            if ($call_time_SEC < 10) {
                $call_time_SEC = "0$call_time_SEC";
            }
            $call_time_MS = "$call_time_M_int:$call_time_SEC";
            $call_time_MS = sprintf("%7s", $call_time_MS);

            $G = '';
            $EG = '';
            $pausecode='';
            if ($Lstatus=='DISPO') {
                if ($call_time_M_int >= 360) {
                    $j++;
                    continue;
                } else {
                    $agent_paused++;
                    $agent_total++;
                    $Lstatus = 'DISPO';
                    $status = sprintf("%-6s",'DISPO');
                    $G='<span class="dispo0">'; $EG='</span>';
                    if ($call_time_S >= 15) {$G='<span class="dispo1">'; $EG='</span>';}
                    if ($call_time_S >= 30) {$G='<span class="dispo2">'; $EG='</span>';}
                    if ($call_time_M_int >= 1) {$G='<span class="dispo3">'; $EG='</span>';}
                }
            } elseif ($Lstatus=='AGTPHN') {
                if ($call_time_M_int >= 360) {
                    $j++;
                    continue;
                } else {
                    $agent_dead++;
                    $agent_total++;
                    $Lstatus = 'AGTPHN';
                    $status = sprintf("%-6s",'AGTPHN');
                    $G='<span class="agtphn0">'; $EG='</span>';
                    if ($call_time_S >= 10) {$G='<span class="agtphn1">'; $EG='</span>';}
                }
            } elseif ($Lstatus=='DEAD') {
                if ($call_time_M_int >= 360) {
                    $j++;
                    continue;
                } else {
                    $agent_dead++;
                    $agent_total++;
                    $Lstatus = 'DEAD';
                    $status = sprintf("%-6s",'HUNGUP');
                    if ($call_time_S < 30) {$G='<span class="dead0">'; $EG='</span>'; $status=sprintf('%-6s','HUNGUP');}
                    if ($call_time_M >= 5) {$G='<span class="dead1">'; $EG='</span>';}
                }
            } elseif (preg_match('/INCALL|QUEUE/i',$Lstatus)) {
                $agent_incall++;
                $agent_total++;
                if ($Lstatus=='INCALL') {
                    $G='<span class="call0">'; $EG='</span>';
                    if ($call_time_S >= 30) {$G='<span class="call1">'; $EG='</span>';}
                    if ($call_time_M_int >= 2) {$G='<span class="call2">'; $EG='</span>';}
                    if ($call_time_M_int >= 10) {$G='<span class="call3">'; $EG='</span>';}
                    if ($call_time_S < 5 and $CM!='M') {$status = sprintf('%-6s','ANSWER');}
                    if ($lstatus != "" and $lstatus != "      ") $status = $lstatus;
                }
            } elseif ($Lstatus=='PAUSED') {
                if ($call_time_M_int >= 360) {
                    $j++;
                    continue;
                } else {
                    if ($agent_pause_codes_active>0) {
                        $stmtC=sprintf("SELECT sub_status FROM osdial_agent_log WHERE user='%s' ORDER BY agent_log_id DESC LIMIT 1;",mres($Luser));
                        $rsltC=mysql_query($stmtC,$link);
                        $rowC=mysql_fetch_row($rsltC);
                        $pausecode = $rowC[0];
                    }
                    $agent_paused++;
                    $agent_total++;
                    $G='<span class="pause0">'; $EG='</span>';
                    if ($call_time_S >= 10) {$G='<span class="pause1">'; $EG='</span>';}
                    if ($call_time_M_int >= 1) {$G='<span class="pause2">'; $EG='</span>';}
                    if ($call_time_M_int >= 5) {$G='<span class="pause3">'; $EG='</span>';}
                    if (strlen($pausecode) > 0 and $pausecode != 'LOGIN') {$G='<span class="pausecode">'; $EG='</span>';}
                }
            } elseif (preg_match('/READY|CLOSER/i',$Lstatus)) {
                $agent_ready++;
                $agent_total++;
                #$G='<span class="lightblue">'; $EG='</span>';
                #if ($call_time_M_int >= 1) {$G='<span class="blue">'; $EG='</span>';}
                #if ($call_time_M_int >= 5) {$G='<span class="midnightblue">'; $EG='</span>';}
                $G='<span class="wait0">'; $EG='</span>';
                if ($call_time_S >= 30) {$G='<span class="wait1">'; $EG='</span>';}
                if ($call_time_M_int >= 1) {$G='<span class="wait2">'; $EG='</span>';}
                if ($call_time_M_int >= 2) {$G='<span class="wait3">'; $EG='</span>';}
            }

            if ($agent_pause_codes_active>0) $pausecode = sprintf('%-6s', $pausecode) . ' ' . $LNcenterbar . ' ';

            $L='';
            $R='';
            $PAsrv_ip = explode('.',$Lserver_ip);
            $Pserver_ip = sprintf('%03d%03d%03d%03d',$PAsrv_ip[0],$PAsrv_ip[1],$PAsrv_ip[2],$PAsrv_ip[3]);

            #if ($SIPmonitorLINK==1) {$L="<a href=\"sip:6$Lsessionid@$Lserver_ip\">LISTEN</a> ";   $R='';}
            #if ($IAXmonitorLINK==1) {$L="<a href=\"iax:6$Lsessionid@$Lserver_ip\">LISTEN</a> ";   $R='';}
            #if ($SIPmonitorLINK==2) {$R=" <a href=\"sip:$Lsessionid@$Lserver_ip\">BARGE</a> ";}
            #if ($IAXmonitorLINK==2) {$R=" <a href=\"iax:$Lsessionid@$Lserver_ip\">BARGE</a> ";}
            if ($SIPmonitorLINK==1) {$L=sprintf('<a href="sip:%s6%s@%s">LISTEN</a> ',$Pserver_ip,$Lsessionid,$Lserver_ip);   $R='';}
            if ($IAXmonitorLINK==1) {$L=sprintf('<a href="iax:%s6%s@%s">LISTEN</a> ',$Pserver_ip,$Lsessionid,$Lserver_ip);   $R='';}
            if ($SIPmonitorLINK==2) {$R=sprintf(' <a href="sip:%s%s@%s">BARGE</a> ', $Pserver_ip,$Lsessionid,$Lserver_ip);}
            if ($IAXmonitorLINK==2) {$R=sprintf(' <a href="iax:%s%s@%s">BARGE</a> ', $Pserver_ip,$Lsessionid,$Lserver_ip);}

            $UGD = '';
            if ($UGdisplay > 0)    $UGD = $user_group . ' ' . $LNcenterbar . ' ';

            $SVD = '';
            if ($SERVdisplay > 0) $SVD = $server_ip . ' ' . $LNcenterbar . ' ' . $call_server_ip . ' ' . $LNcenterbar . ' ';

            $vac_campaign='';
            $INGRP='';
            if ($CM == 'I') {
                $stmt=sprintf("SELECT campaign_id FROM osdial_auto_calls WHERE callerid='%s' LIMIT 1;",mres($Lcallerid));
                $rslt=mysql_query($stmt, $link);
                $ingrp_to_print = mysql_num_rows($rslt);
                if ($ingrp_to_print > 0) {
                    $row=mysql_fetch_row($rslt);
                    $vac_campaign = $row[0];
                }
            }
            $INGRP = sprintf('%-20s',$vac_campaign);

            $agentcount++;


            $disp_agent = 1;
            if ($group == "XXXX-ALL-ACTIVE-XXXX" and $campaign_active<1) $disp_agent = 0;

            if ($disp_agent) $Ahtml .= "$LNleft$G $extension $LNcenterbar <a href=\"$PHP_SELF?ADD=999999&SUB=22&agent=$Luser\" target=\"_blank\">$G$user$EG</a> $LNcenterbar $UGD$sessionid$L$R $LNcenterbar $status $CM $LNcenterbar $pausecode$SVD$call_time_MS $LNcenterbar $campaign_id $LNcenterbar $calls_today $LNcenterbar $INGRP$EG$LNright\n";

            $j++;
        }

        $Ahtml .= $Dline;

        $Ahtml .= "  $agentcount <font color=$default_text>agents logged in on all servers</font>\n";


        if ($agent_ready > 0) {$B='<font class="b1">'; $BG='</font>';}
        if ($agent_ready > 4) {$B='<font class="b2">'; $BG='</font>';}
        if ($agent_ready > 9) {$B='<font class="b3">'; $BG='</font>';}
        if ($agent_ready > 14) {$B='<font class="b4">'; $BG='</font>';}


        $html .= "\n<br>\n";

        $html .= "$NFB$agent_total$NFE <font color=blue>agents logged in</font> &nbsp; &nbsp; &nbsp; &nbsp; \n";
        $html .= "$NFB$agent_incall$NFE <font color=blue>agents in calls</font> &nbsp; &nbsp; &nbsp; \n";
        $html .= "&nbsp;$NFB$B$agent_ready$BG$NFE <font color=blue>agents waiting</font> &nbsp; &nbsp; &nbsp; \n";
        $html .= "$NFB$agent_paused$NFE <font color=blue>paused agents</font> &nbsp; &nbsp; &nbsp; \n";

        $html .= '<pre><font size=2>';

        $html .= $Chtml;
        $html .= $Ahtml;

        $html .= "<br><br><center>";
        $html .= "<table width=700><tr><td width=5px>&nbsp;</td><td width=265>";
        $html .= "  <font color=$default_text>";
        $html .= "  <span class=wait0>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Waiting for call</b><br>";
        $html .= "  <span class=wait1>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Waiting for call > 30sec</b><br>";
        $html .= "  <span class=wait2>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Waiting for call >&nbsp;&nbsp;1min</b><br>";
        $html .= "  <span class=wait3>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Waiting for call >&nbsp;&nbsp;2min</b>";
        $html .= "  </font>";
        $html .= "</td><td width=230px>";
        $html .= "  <font color=$default_text>";
        $html .= "  <span class=call0>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - On call</b><br>";
        $html .= "  <span class=call1>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - On call > 30sec</b><br>";
        $html .= "  <span class=call2>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - On call >&nbsp;&nbsp;2min</b><br>";
        $html .= "  <span class=call3>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - On call >&nbsp;10min</b>";
        $html .= "  </font>";
        $html .= "</td><td width=200px>";
        $html .= "  <font color=$default_text>";
        $html .= "  <span class=pause0>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Paused</b><br>";
        $html .= "  <span class=pause1>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Paused > 10sec</b><br>";
        $html .= "  <span class=pause2><b>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Paused >&nbsp;&nbsp;1min</b><br>";
        $html .= "  <span class=pause3><b>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Paused >&nbsp;&nbsp;5min</b>";
        $html .= "  </font>";
        $html .= "</td></tr><tr><td colspan=4 height=1px></td>";
        $html .= "</tr><tr><td>&nbsp;</td><td valign=top>";
        $html .= "  <font color=$default_text>&nbsp;</font>";
        $html .= "</td><td valign=top>";
        $html .= "  <font color=$default_text>";
        $html .= "  <span class=dispo0>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Disposition</b><br>";
        $html .= "  <span class=dispo1>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Disposition > 15sec</b><br>";
        $html .= "  <span class=dispo2>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Disposition > 30sec</b><br>";
        $html .= "  <span class=dispo3>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Disposition >&nbsp;&nbsp;1min</b>";
        $html .= "  </font>";
        $html .= "</td><td valign=top>";
        $html .= "  <font color=$default_text>";
        $html .= "  <span class=pausecode>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Pause Code</b><br><br>";
        $html .= "  <span class=agtphn1>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Agent Phone Issue</b><br>";
        $html .= "  <span class=dead1>&nbsp;&nbsp;&nbsp;&nbsp;</span><b> - Dead Call / Hungup</b>";
        $html .= "  </font>";
        $html .= "</td></tr>";
        $html .= "</table></center>";
    } else {
        $html .= "&nbsp;&nbsp;<font color=red>&bull;&nbsp;&nbsp;NO AGENTS ON CALLS</font> \n";
        $html .= '<br>';
        $html .= '<br>';
    }

    $html .= '</pre>';

    if (file_exists($pref . 'resources.txt')) {
        $html .= '<br>';
        $html .= '<center>';
        if ($cpuinfo == 0 ) {
            $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=0\"><font size=1><b>STANDARD INFO</b></font></a>";
            $html .= ' - ';
            $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=1\"><font size=1>EXTENDED INFO</font></a>";
            eval("\$html .= \"" . file_get_contents($pref . 'resources.txt') . "\";");
        } else {
            $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=0\"><font size=1>STANDARD INFO</font></a>";
            $html .= ' - ';
            $html .= "<a href=\"$PHP_SELF?ADD=$ADD&SUB=$SUB&group=$group&campaign_id=$campaign_id&RR=$RR&DB=$DB&adastats=$adastats&SIPmonitorLINK=$SIPmonitorLINK&IAXmonitorLINK=$IAXmonitorLINK&usergroup=$usergroup&UGdisplay=$UGdisplay&UidORname=$UidORname&orderby=$orderby&orddir=$orddir&SERVdisplay=$SERVdisplay&CALLSdisplay=$CALLSdisplay&VAdisplay=$VAdisplay&cpuinfo=1\"><font size=1><b>EXTENDED INFO</b></font></a>";
            eval("\$html .= \"" . file_get_contents($pref . 'resources-xtd.txt') . "\";");
        }
        $html .= '</center>';
    }

    return $html;
}

?>
