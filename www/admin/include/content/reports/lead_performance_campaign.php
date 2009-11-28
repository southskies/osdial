<? 
### report_lead_performance_campaign.php
### 
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
#
# 090814-1200 - First build

function report_lead_performance_campaign() {
    #############################################
    ##### START REPORT #####
    $report_start = date("U");

    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $html = "";

    $type=get_variable("type");
    $start_date=get_variable("start_date");
    $start_time=get_variable("start_time");
    $end_date=get_variable("end_date");
    $end_time=get_variable("end_time");
    $group=get_variable("group");
    $submit=get_variable("submit");
    $DB=get_variable("DB");

    if ($type=="") $type = "date";
    if ($start_date=="") $start_date = date("Y-m-d");
    if ($end_date=="") $end_date = $start_date;
    if (strlen($start_time) < 5) $start_time = "00:00";
    if (strlen($end_time) < 5) $end_time = "23:59";
    if ($group=='') $group[] = '--ALL--';

    $stmt="select campaign_id,campaign_name from osdial_campaigns;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) $html .= "$stmt\n";
    $camps_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $camps_to_print) {
        $row=mysql_fetch_row($rslt);
        $groups[$i] =$row[0];
        $group_names[$i] =$row[1];
        $i++;
    }

    $i=0;
    $group_string='|';
    $group_ct = count($group);
    while($i < $group_ct) {
        $group_string .= "$group[$i]|";
        $group_SQL .= "'" . mysql_real_escape_string($group[$i]) . "',";
        $groupQS .= "&group[]=$group[$i]";
        $i++;
    }
    if ( (ereg("--ALL--",$group_string) ) or ($group_ct < 1) ) {
        $group_SQL = "";
        $group_olSQL = "";
        $group_ocSQL = "";
        $group_logSQL = "";
        $group_SQLand = "";
        $group_olSQLand = "";
        $group_ocSQLand = "";
        $group_logSQLand = "";
    } else {
        $group_SQL = eregi_replace(",$",'',$group_SQL);

        $group_logSQLand = "and osdial_log.campaign_id IN($group_SQL)";
        $group_olSQLand = "and osdial_lists.campaign_id IN($group_SQL)";
        $group_ocSQLand = "and osdial_campaigns.campaign_id IN($group_SQL)";
        $group_SQLand = "and campaign_id IN($group_SQL)";

        $group_logSQL = "where osdial_log.campaign_id IN($group_SQL)";
        $group_olSQL = "where osdial_lists.campaign_id IN($group_SQL)";
        $group_ocSQL = "where osdial_campaigns.campaign_id IN($group_SQL)";
        $group_SQL = "where campaign_id IN($group_SQL)";
    }


    $stmt="SELECT category,status FROM osdial_statuses;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) $html .= "$stmt\n";
    $sc_to_print = mysql_num_rows($rslt);
    $c=0;
    $s=0;
    $i=0;
    while ($i < $sc_to_print) {
        $row=mysql_fetch_row($rslt);
        if ($row[0] == "CONTACT") {
            $CSCcontacts[$c] =$row[1];
            $c++;
        } elseif ($row[0] == "SALE") {
            $CSCsales[$s] =$row[1];
            $s++;
        }
        $i++;
    }
    $stmt="SELECT category,status FROM osdial_campaign_statuses $group_SQL;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) $html .= "$stmt\n";
    $sc_to_print = mysql_num_rows($rslt);
    $j=0;
    while ($j < $sc_to_print) {
        $row=mysql_fetch_row($rslt);
        if ($row[0] == "CONTACT") {
            $CSCcontacts[$c] =$row[1];
            $c++;
        } elseif ($row[0] == "SALE") {
            $CSCsales[$s] =$row[1];
            $s++;
        }
        $j++;
    }

    $csc_ct = count($CSCcontacts);
    $i=0;
    while($i < $csc_ct) {
        $SCcontacts .= "'" . mysql_real_escape_string($CSCcontacts[$i]) . "',";
        $i++;
    }
    $SCcontacts = eregi_replace(",$",'',$SCcontacts);

    $csc_ct = count($CSCsales);
    $i=0;
    while($i < $csc_ct) {
        $SCsales .= "'" . mysql_real_escape_string($CSCsales[$i]) . "',";
        $i++;
    }
    $SCsales = eregi_replace(",$",'',$SCsales);
    if ($csc_ct < 2) {
        $SCsales = "'SALE','XFER'";
    }

    $html .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/$admin_template/styles.css\" media=\"screen\">\n";
    $html .= "<style type=\"text/css\">\n";
    $html .= "<!--\n";
    $html .= "    .green {color: white; background-color: green}\n";
    $html .= "    .red {color: white; background-color: red}\n";
    $html .= "    .blue {color: white; background-color: blue}\n";
    $html .= "    .purple {color: white; background-color: purple}\n";
    $html .= "-->\n";
    $html .= "</style>\n";

    $html .= "<table align=center cellpadding=0 cellspacing=0>";
    $html .= "<tr><td align=center>";
    $html .= "<br><font color=$default_text size=+1>LEAD PERFORMANCE BY CAMPAIGN</font><br><br>";
    $html .= "<div class=\"noprint\">\n";
    $html .= "<form action=\"$PHP_SELF\" method=get>\n";
    $html .= "<input type=hidden name=type value=$type>\n";
    $html .= "<input type=hidden name=ADD value=$ADD>\n";
    $html .= "<input type=hidden name=SUB value=$SUB>\n";
    $html .= "<input type=hidden name=DB value=$DB>\n";
    $html .= "<table border=0 bgcolor=grey cellspacing=1>\n";
    $html .= "  <tr class=tabheader>\n";
    $html .= "    <td>Campaign</td>\n";
    $html .= "    <td>Date Range</td>\n";
    $html .= "  </tr>\n";
    $html .= "  <tr class=tabfooter>\n";
    $html .= "    <td rowspan=3>\n";
    $html .= "      <select size=5 name=group[] multiple>\n";
    if  (eregi("--ALL--",$group_string)) {
        $html .= "        <option value=\"--ALL--\" selected>-- ALL CAMPAIGNS --</option>\n";
    } else {
        $html .= "        <option value=\"--ALL--\">-- ALL CAMPAIGNS --</option>\n";
    }
    $o=0;
    while ($camps_to_print > $o) {
        if (eregi("$groups[$o]\|",$group_string)) {
            $html .= "        <option selected value=\"$groups[$o]\">$groups[$o]: $group_names[$o]</option>\n";
        } else {
            $html .= "        <option value=\"$groups[$o]\">$groups[$o]: $group_names[$o]</option>\n";
        }
        $o++;
    }
    $html .= "      </select>\n";
    $html .= "    </td>\n";
    $html .= "    <td>\n";
    $html .= "      <input type=text name=start_date size=10 maxlength=10 value=\"$start_date\"> to <input type=text name=end_date size=10 maxlength=10 value=\"$end_date\">\n";
    $html .= "    </td>\n";
    $html .= "  </tr>\n";
    $html .= "  <tr class=tabfooter valign=bottom>\n";
    $html .= "    <td rowspan=2 class=tabbutton>\n";
    $html .= "      <input type=submit name=submit value=submit>\n";
    $html .= "    </td>\n";
    $html .= "  </tr>\n";
    $html .= "  <tr class=tabfooter></tr>\n";
    $html .= "</table>\n";
    $html .= "</form>\n";
    $html .= "</div>\n";

    $html .= "<font size=2><pre>";


    if ($group) {
        $query_date_BEGIN = "$start_date $start_time:00";
        $query_date_END = "$end_date $end_time:59";

        $html .= "OSDIAL: List Performance Campaign / Entry Date                         " . date("Y-m-d H:i:s") . "\n";

        $html .= "Time range: $query_date_BEGIN to $query_date_END\n\n";

        $html .= "</pre>\n";

        $CSVrow=0;
        $html .= "<form target=\"_new\" action=\"/osdial/admin/tocsv.php\">\n";
        $html .= "<input type=hidden name=\"name\" value=\"lpr\">\n";

        $html .= "<table cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $html .= "  <tr class=tabheader>\n";
        $html .= "    <td><font size=2>&nbsp;</font></td>\n";
        $html .= "    <td align=center><font style=\"font-size:1px;\"><b>&nbsp;</b></font></td>\n";
        if ($type == "hour") {
            $html .= "    <td align=center colspan=8><font color=white size=2><b>Lead Analysis by Hour Called</b></font></td>\n";
            $html .= "    <td align=center><font color=white style=\"font-size:1px;\"><b>&nbsp;</b></font></td>\n";
            $html .= "    <td align=center colspan=8><font color=white size=2><b>Lead Analysis by Entry Hour</b></font></td>\n";
            $head1 = "||Lead Analysis by Hour Called||||||||Lead Analysis by Entry Hour";
            $head1 = "||Lead|Analysis|by|Hour|Called||||Lead|Analysis|by|Entry|Hour";
        } else {
            $html .= "    <td align=center colspan=8><font color=white size=2><b>Lead Analysis by Date Called</b></font></td>\n";
            $html .= "    <td align=center><font color=white style=\"font-size:1px;\"><b>&nbsp;</b></font></td>\n";
            $html .= "    <td align=center colspan=8><font color=white size=2><b>Lead Analysis by Entry Date</b></font></td>\n";
            $head1 = "||Lead Analysis by Date Called||||||||Lead Analysis by Entry Date";
            $head1 = "||Lead|Analysis|by|Date|Called||||Lead|Analysis|by Entry|Date";
        }
        $html .= "  </tr>\n";
        $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $head1 . "\">\n";

        $html .= "  <tr class=tabheader>\n";
        if ($type == "hour") {
            $html .= "    <td align=center>Hour</td>\n";
            $head2 = "Hour|";
        } else {
            $html .= "    <td align=center>Date</td>\n";
            $head2 = "Date|";
        }
        $html .= "    <td align=center><font style=\"font-size:1px;\">&nbsp;</td>\n";
        $html .= "    <td align=center>Calls</td>\n";
        $html .= "    <td align=center>Contacts</td>\n";
        $html .= "    <td align=center>Sales</td>\n";
        $html .= "    <td align=center>Contact<br>Closing%</td>\n";
        $html .= "    <td align=center>Closing%</td>\n";
        $html .= "    <td align=center>Total Cost</td>\n";
        $html .= "    <td align=center>Average Cost</td>\n";
        $html .= "    <td align=center>Cost Per Sale</td>\n";
        $html .= "    <td align=center><font style=\"font-size:1px;\">&nbsp;</font></td>\n";
        $html .= "    <td align=center>Leads Entered</td>\n";
        $html .= "    <td align=center>Contacts</td>\n";
        $html .= "    <td align=center>Sales</td>\n";
        $html .= "    <td align=center>Contact<br>Closing%</td>\n";
        $html .= "    <td align=center>Closing%</td>\n";
        $html .= "    <td align=center>Total Cost</td>\n";
        $html .= "    <td align=center>Average Cost</td>\n";
        $html .= "    <td align=center>Cost Per Sale</td>\n";
        $html .= "  </tr>\n";
        $head2 .= "|Calls|Contacts|Sales|Contact Closing%|Closing%|Total Cost|Average Cost|Cost Per Sale||Leads Entered|Contacts|Sales|Contact Closing%|Closing%|Total Cost|Average Cost|Cost Per Sale";
        $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $head2 . "\">\n";

        $stmt="SELECT $type(osdial_list.entry_date),sum(osdial_list.cost),count(*),sum(if(osdial_list.status IN ($SCcontacts,$SCsales),1,0)),sum(if(osdial_list.status IN ($SCsales),1,0)) FROM osdial_list,osdial_lists WHERE osdial_list.list_id=osdial_lists.list_id AND osdial_list.entry_date <= '$query_date_END' AND osdial_list.entry_date >= '$query_date_BEGIN' $group_olSQLand GROUP BY $type(osdial_list.entry_date);";
        $rslt=mysql_query($stmt, $link);
        if ($DB) $html .= "$stmt\n";
        $rows_to_print = mysql_num_rows($rslt);

        $TOTnewcost    = 0;
        $TOTnewleads   = 0;
        $TOTnewcontacts= 0;
        $TOTnewsales   = 0;
        $i=0;
        while ($i < $rows_to_print) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $i)) {
                $bgcolor='bgcolor='.$oddrows;
            } else {
                $bgcolor='bgcolor='.$evenrows;
            }


            $period         = $row[0];
            $newcost        = $row[1];
            $newleads       = $row[2];
            $newcontacts    = $row[3];
            $newsales       = $row[4];


            $cost = 0;
            $calls = 0;
            $contacts = 0;
            $sales = 0;

            $stmtB = "SELECT $type(osdial_log.call_date),count(*),sum(if(osdial_log.status IN ($SCcontacts,$SCsales),1,0)),sum(if(osdial_log.status IN ($SCsales),1,0)),sum(osdial_list.cost) FROM osdial_log,osdial_list WHERE osdial_log.lead_id=osdial_list.lead_id AND osdial_log.call_date <= '$query_date_END' AND osdial_log.call_date >= '$query_date_BEGIN' AND $type(osdial_log.call_date)='$period' $group_logSQLand GROUP BY $type(osdial_log.call_date);";
            $rsltB=mysql_query($stmtB, $link);
            if ($DB) $html .= "$stmtB\n";
            $rowsB_to_print = mysql_num_rows($rsltB);
            $j=0;
            while ($j < $rowsB_to_print) {
                $rowB=mysql_fetch_row($rsltB);
                $calls = $rowB[1];
                $contacts = $rowB[2];
                $sales = $rowB[3];
                $cost = $rowB[4];
                $j++;
            }


            $cnt_closing_pct = "0%";
            if ($contacts > 0) $cnt_closing_pct = sprintf('%3.2f',(($sales / $contacts) * 100)) . "%";
            $closing_pct = "0%";
            if ($calls > 0) $closing_pct = sprintf('%3.2f',(($sales / $calls) * 100)) . "%";
            $avg_cost = '0.00';
            if ($calls > 0) $avg_cost = sprintf('%3.2f',$cost / $calls);
            $cost_sale = '0.00';
            if ($sales > 0) $cost_sale = sprintf('%3.2f',$cost / $sales);

            $newcnt_closing_pct = "0%";
            if ($newcontacts > 0) $newcnt_closing_pct = sprintf('%3.2f',(($newsales / $newcontacts) * 100)) . "%";
            $newclosing_pct = "0%";
            if ($newleads > 0) $newclosing_pct = sprintf('%3.2f',(($newsales / $newleads) * 100)) . "%";
            $newavg_cost = '0.00';
            if ($newleads > 0) $newavg_cost = sprintf('%3.2f',$newcost / $newleads);
            $newcost_sale = '0.00';
            if ($newsales > 0) $newcost_sale = sprintf('%3.2f',$newcost / $newsales);

            $html .= "  <tr $bgcolor class=\"row font1\">\n";
            if ($type == "hour") {
                $html .= "    <td align=right><a href=\"?ADD=$ADD&SUB=$SUB&type=date&start_date=$start_date$groupQS&submit=submit&DB=$DB\">$period</a></td>\n";
                $html .= "    <td align=center bgcolor=$menubarcolor><font style=\"font-size:1px;\">&nbsp;</font></td>\n";
                $html .= "    <td align=right><a href=\"?ADD=999999&SUB=15&query_date=$start_date&time_begin=$period:00$&end_date=$start_date&time_end=$period:59&use_agent_log=1$groupQS&SUBMIT=SUBMIT&DB=$DB\">$calls</a></td>\n";
            } else {
                $html .= "    <td align=right><a href=\"?ADD=$ADD&SUB=$SUB&type=hour&start_date=$period$groupQS&submit=submit&DB=$DB\">$period</a></td>\n";
                $html .= "    <td align=center bgcolor=$menubarcolor><font style=\"font-size:1px;\">&nbsp;</font></td>\n";
                $html .= "    <td align=right><a href=\"?ADD=999999&SUB=15&query_date=$period&time_begin=00:00$&end_date=$period&time_end=23:59&use_agent_log=1$groupQS&SUBMIT=SUBMIT&DB=$DB\">$calls</a></td>\n";
            }
            $html .= "    <td align=right>$contacts</td>\n";
            $html .= "    <td align=right>$sales</td>\n";
            $html .= "    <td align=right>$cnt_closing_pct</td>\n";
            $html .= "    <td align=right>$closing_pct</td>\n";
            $html .= "    <td align=right>$cost</td>\n";
            $html .= "    <td align=right>$avg_cost</td>\n";
            $html .= "    <td align=right>$cost_sale</td>\n";
            $html .= "    <td align=center bgcolor=$menubarcolor><font style=\"font-size:1px;\">&nbsp;</font></td>\n";
            $html .= "    <td align=right>$newleads</td>\n";
            $html .= "    <td align=right>$newcontacts</td>\n";
            $html .= "    <td align=right>$newsales</td>\n";
            $html .= "    <td align=right>$newcnt_closing_pct</td>\n";
            $html .= "    <td align=right>$newclosing_pct</td>\n";
            $html .= "    <td align=right>$newcost</td>\n";
            $html .= "    <td align=right>$newavg_cost</td>\n";
            $html .= "    <td align=right>$newcost_sale</td>\n";
            $html .= "  </tr>\n";
            $line = "$period||$calls|$contacts|$sales|$closing_pct|$cost|$avg_cost|$cost_sale||$newleads|$newcontacts|$newsales|$newclosing_pct|$newcost|$newavg_cost|$newcost_sale";
            $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $line . "\">\n";


            $TOTcost    += $cost;
            $TOTcalls   += $calls;
            $TOTcontacts+= $contacts;
            $TOTsales   += $sales;
            $TOTnewcost    += $newcost;
            $TOTnewleads   += $newleads;
            $TOTnewcontacts+= $newcontacts;
            $TOTnewsales   += $newsales;

            $i++;
        }


        $TOTcnt_closing_pct = "0%";
        if ($TOTcontacts > 0) $TOTcnt_closing_pct = sprintf('%3.2f',(($TOTsales / $TOTcontacts) * 100)) . "%";
        $TOTclosing_pct = "0%";
        if ($TOTcalls > 0) $TOTclosing_pct = sprintf('%3.2f',(($TOTsales / $TOTcalls) * 100)) . "%";
        $TOTavg_cost = '0.00';
        if ($TOTcalls > 0) $TOTavg_cost = sprintf('%3.2f',$TOTcost / $TOTcalls);
        $TOTcost_sale = '0.00';
        if ($TOTsales > 0) $TOTcost_sale = sprintf('%3.2f',$TOTcost / $TOTsales);

        $TOTnewcnt_closing_pct = "0%";
        if ($TOTnewcontacts > 0) $TOTnewcnt_closing_pct = sprintf('%3.2f',(($TOTnewsales / $TOTnewcontacts) * 100)) . "%";
        $TOTnewclosing_pct = "0%";
        if ($TOTnewcontacts > 0) $TOTnewclosing_pct = sprintf('%3.2f',(($TOTnewsales / $TOTnewleads) * 100)) . "%";
        $TOTnewavg_cost = '0.00';
        if ($TOTnewleads > 0) $TOTnewavg_cost = sprintf('%3.2f',$TOTnewcost / $TOTnewleads);
        $TOTnewcost_sale = '0.00';
        if ($TOTnewsales > 0) $TOTnewcost_sale = sprintf('%3.2f',$TOTnewcost / $TOTnewsales);

        $html .= "  <tr class=tabfooter>\n";
        $html .= "    <td>TOTAL</td>\n";
        $html .= "    <td><font style=\"font-size:1px;\">&nbsp;</font></td>\n";
        $html .= "    <td align=right>$TOTcalls</td>\n";
        $html .= "    <td align=right>$TOTcontacts</td>\n";
        $html .= "    <td align=right>$TOTsales</td>\n";
        $html .= "    <td align=right>$TOTcnt_closing_pct</td>\n";
        $html .= "    <td align=right>$TOTclosing_pct</td>\n";
        $html .= "    <td align=right>$TOTcost</td>\n";
        $html .= "    <td align=right>$TOTavg_cost</td>\n";
        $html .= "    <td align=right>$TOTcost_sale</td>\n";
        $html .= "    <td align=center><font style=\"font-size:1px;\">&nbsp;</font></td>\n";
        $html .= "    <td align=right>$TOTnewleads</td>\n";
        $html .= "    <td align=right>$TOTnewcontacts</td>\n";
        $html .= "    <td align=right>$TOTnewsales</td>\n";
        $html .= "    <td align=right>$TOTnewcnt_closing_pct</td>\n";
        $html .= "    <td align=right>$TOTnewclosing_pct</td>\n";
        $html .= "    <td align=right>$TOTnewcost</td>\n";
        $html .= "    <td align=right>$TOTnewavg_cost</td>\n";
        $html .= "    <td align=right>$TOTnewcost_sale</td>\n";
        $html .= "  </tr>\n";
        $html .= "</table>\n";
        $line = "||||||||||||||||";
        $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $line . "\">\n";
        $line = "TOTAL||$TOTcalls|$TOTcontacts|$TOTsales|$TOTclosing_pct|$TOTcost|$TOTavg_cost|$TOTcost_sale||$TOTnewleads|$TOTnewcontacts|$TOTnewsales|$TOTnewclosing_pct|$TOTnewcost|$TOTnewavg_cost|$TOTnewcost_sale";
        $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $line . "\">\n";
        $html .= "<input type=hidden name=\"rows\" value=\"" . $CSVrow . "\">\n";
        $html .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
        $html .= "</form>";

        $report_end = date("U");
        $report_time = ($report_end - $report_start);
        $html .= "<pre>\n";
        $html .= "\nRun Time: $report_time seconds\n";
        $html .= "</pre>\n";
    }

    #$html .= "</font>\n";

    #$html .= "</td>";
    #$html .= "<table width=$page_width bgcolor=#e9e8d9 cellpadding=0 cellspacing=0 align=center class=across>";
    return $html;
}

?>
