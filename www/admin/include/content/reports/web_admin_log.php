<?php
### web_admin_log.php
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
###

function report_web_admin_log() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $query_date = get_variable('query_date');
    $submit = get_variable('submit');
    $SUBMIT = get_variable('SUBMIT');
    $query_date = preg_replace("/[^ \.\,-\_0-9a-zA-Z]/","",$query_date);

    $STARTtime = date("U");
    $DAY_DATE = date("j");
    if ($DAY_DATE < 10) {
	    $GREP_DATE = date("D, 0j M Y");
    } else {
	    $GREP_DATE = date("D, j M Y");
    }

    $NOW_DATE = date("Y-m-d");
    $NOW_TIME = date("Y-m-d H:i:s");
    if (!$query_date) {$query_date = $GREP_DATE;}

    $Gquery_date = preg_replace("/ /",'\ ',$query_date);
    $html .= "<!-- |$query_date|$Gquery_date| -->\n";

    $html .= "<form action=\"$PHP_SELF\" method=get>\n";
    $html .= "<input type=text name=query_date size=20 maxlength=20 value=\"$query_date\">\n";
    $html .= "<input type=submit name=submit value=SUBMIT>\n";
    $html .= "</form>\n";

    $html .= "<pre><font size=2>\n\n";
    $html .= "OSDIAL: Admin Change Log                    " . dateToLocal($link,'first',date('Y-m-d H:i:s'),$webClientAdjGMT,'',$webClientDST,1) . "\n\n";

    $html .= "<div style=\"align:center;width:950px;height:300px;overflow:scroll;\">";
    $retGrep = array();
    exec("grep $Gquery_date $WeBServeRRooT/admin/admin_changes_log.txt", $retGrep);
    $html .= implode("\n",$retGrep)."\n";

    $html .= "</div></font></pre>\n";

    return $html;
}
?>