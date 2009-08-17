<? 
### report_list_cost.php
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
# 090812-1200 - First build

function report_list_cost() {
    #############################################
    ##### START REPORT #####
    $report_start = date("U");

    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $html = "";


    $query_date=get_variable("query_date");
    $end_date=get_variable("end_date");
    $group=get_variable("group");
    $submit=get_variable("submit");
    $DB=get_variable("DB");

    if ($query_date=="") $query_date = date("Y-m-d");
    if ($end_date=="") $end_date = date("Y-m-d");

    $stmt="select list_id,list_name from osdial_lists;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) $html .= "$stmt\n";
    $lists_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $lists_to_print) {
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
        $group_SQLand = "";
    } else {
        $group_SQL = eregi_replace(",$",'',$group_SQL);
        $group_SQLand = "and osdial_list.list_id IN($group_SQL)";
        $group_SQL = "where osdial_list.list_id IN($group_SQL)";
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
    $html .= "<br><font color=navy size=+1>LIST COST BY ENTRY DATE</font><br><br>";
    $html .= "<div class=\"noprint\">\n";
    $html .= "<form action=\"$PHP_SELF\" method=get>\n";
    $html .= "<input type=hidden name=ADD value=$ADD>\n";
    $html .= "<input type=hidden name=SUB value=$SUB>\n";
    $html .= "<input type=hidden name=DB value=$DB>\n";
    $html .= "<table border=0>\n";
    $html .= "  <tr>\n";
    $html .= "    <td>\n";
    $html .= "      <input type=text name=query_date size=10 maxlength=10 value=\"$query_date\"> to <input type=text name=end_date size=10 maxlength=10 value=\"$end_date\">\n";
    $html .= "    </td>\n";
    $html .= "    <td>\n";
    $html .= "      <select size=5 name=group[] multiple>\n";
    if  (eregi("--ALL--",$group_string)) {
        $html .= "        <option value=\"--ALL--\" selected>-- ALL LISTS --</option>\n";
    } else {
        $html .= "        <option value=\"--ALL--\">-- ALL LISTS --</option>\n";
    }
    $o=0;
    while ($lists_to_print > $o) {
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
    $html .= "      <input type=submit name=submit value=submit>\n";
    $html .= "    </td>\n";
    $html .= "  </tr>\n";
    $html .= "</table>\n";
    $html .= "</form>\n\n";
    $html .= "</div>\n\n";

    $html .= "<pre><font size=2>";


    if (!$group) {
        $html .= "Please Select A List And Date\n";
    } else {
        if (strlen($time_BEGIN) < 6) $time_BEGIN = "00:00:00";
        if (strlen($time_END) < 6) $time_END = "23:59:59";
        $query_date_BEGIN = "$query_date $time_BEGIN";   
        $query_date_END = "$end_date $time_END";

        $html .= "OSDIAL: List Cost by Entry Date                         " . date("Y-m-d H:i:s") . "\n";

        $html .= "Time range: $query_date_BEGIN to $query_date_END\n\n";

        $html .= "</font></pre>\n";

        $CSVrow=0;
        $html .= "<form target=\"_new\" action=\"/osdial/admin/tocsv.php\">\n";
        $html .= "<input type=hidden name=\"name\" value=\"lcr\">\n";

        $html .= "<table width=100% cellspacing=1 cellpadding=1 bgcolor=grey>\n";
        $html .= "  <tr bgcolor=$menubarcolor>\n";
        $html .= "    <td><font color=white size=1><b>DATE</b></font></td>\n";
        $html .= "    <td><font color=white size=1><b>LIST</b></font></td>\n";
        $html .= "    <td align=center><font color=white size=1><b>LEADS</b></font></td>\n";
        $html .= "    <td align=center><font color=white size=1><b>AVERAGE COST</b></font></td>\n";
        $html .= "    <td align=center><font color=white size=1><b>TOTAL COST</b></font></td>\n";
        $html .= "  </tr>\n";
        $head = "DATE|LIST|LEADS|AVERAGE COST|TOTAL COST";
        $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $head . "\">\n";

        $stmt="SELECT date(osdial_list.entry_date),osdial_lists.list_id,osdial_lists.list_name,count(*),avg(osdial_list.cost),sum(osdial_list.cost) FROM osdial_list,osdial_lists WHERE entry_date <= '$query_date_END' AND entry_date >= '$query_date_BEGIN' AND osdial_lists.list_id=osdial_list.list_id $group_SQLand GROUP BY list_id;";
        $rslt=mysql_query($stmt, $link);
        if ($DB) $html .= "$stmt\n";
        $rows_to_print = mysql_num_rows($rslt);

        $last_date = "";
        $TOTleads=0;
        $TOTavg_cost=0;
        $TOTtotal_cost=0;
        $i=0;
        while ($i < $rows_to_print) {
            $row=mysql_fetch_row($rslt);
            if (eregi("1$|3$|5$|7$|9$", $i)) {
                $bgcolor='bgcolor='.$oddrows;
            } else {
                $bgcolor='bgcolor='.$evenrows;
            }
    
            if ($i > 0 and $last_date != $row[0]) {
                $html .= "  <tr bgcolor=$menubarcolor>\n";
                $html .= "    <td colspan=5><font color=white style=\"font-size:1px;\">&nbsp;</font></td>\n";
                $html .= "  </tr>\n";
            }

            $date           = $row[0];
            $list_id        = $row[1];
            $list_name      = $row[2];
            $leads          = $row[3];
            $avg_cost       = $row[4];
            $total_cost     = $row[5];

            $TOTleads      += $row[3];
            $TOTavg_cost   += $row[4];
            $TOTtotal_cost += $row[5];

            $last_date      = $row[0];

            $name = $list_id . ": " . $list_name;

            $date       = sprintf("%10s",   $date);
            $name       = sprintf("%-26s",  $name); 
            $leads      = sprintf("%7s",    $leads); 
            $avg_cost   = sprintf("%8.2f",  $avg_cost); 
            $total_cost = sprintf("%10.2f", $total_cost); 

            $html .= "  <tr $bgcolor>\n";
            $html .= "    <td align=left><font size=1>$date</font></td>\n";
            $html .= "    <td align=left><font size=1>$name</font></td>\n";
            $html .= "    <td align=right><font size=1>$leads</font></td>\n";
            $html .= "    <td align=right><font size=1>$avg_cost</font></td>\n";
            $html .= "    <td align=right><font size=1>$total_cost</font></td>\n";
            $html .= "  </tr>\n";
            $line = "$date|$name|$leads|$avg_cost|$total_cost";
            $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $line . "\">\n";

            $i++;
        }

        $TOTleads      = sprintf("%7s",    $TOTleads); 
        $TOTavg_cost   = sprintf("%8.2f",  $TOTavg_cost); 
        $TOTtotal_cost = sprintf("%10.2f", $TOTtotal_cost); 
    
        $html .= "  <tr bgcolor=$menubarcolor>\n";
        $html .= "    <td align=left colspan=2><font color=white size=1>TOTAL</font></td>\n";
        $html .= "    <td align=right><font color=white size=1><b>$TOTleads</b></font></td>\n";
        $html .= "    <td align=right><font color=white size=1><b>$TOTavg_cost</b></font></td>\n";
        $html .= "    <td align=right><font color=white size=1><b>$TOTtotal_cost</b></font></td>\n";
        $html .= "  </tr>\n";
        $html .= "</table>\n";
        $line = "||||";
        $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $line . "\">\n";
        $line = "TOTAL||$leads|$avg_cost|$total_cost";
        $html .= "<input type=hidden name=\"row" . $CSVrow++ . "\" value=\"" . $line . "\">\n";
        $html .= "<input type=hidden name=\"rows\" value=\"" . $CSVrow . "\">\n";
        $html .= "<input type=submit class=\"noprint\" name=\"export\" value=\"Export to CSV\">\n";
        $html .= "</form>";

    }

    $report_end = date("U");
    $report_time = ($report_end - $report_start);

    $html .= "<pre><font size=2>\n";
    $html .= "\nRun Time: $report_time seconds\n";
    $html .= "</font></pre>\n";

    $html .= "</td>";
    $html .= "<table width=$page_width bgcolor=#e9e8d9 cellpadding=0 cellspacing=0 align=center class=across>";
    return $html;
}

?>
