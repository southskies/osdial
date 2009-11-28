<?
### report_agent_status.php
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
# CHANGES
#
# 60619-1738 - Added variable filtering to eliminate SQL injection attack threat
#

function report_agent_status() {
    # Bring all globals into this scope.
    foreach ($GLOBALS as $key => $val) { global $$key; }

    $agent = get_variable("agent");
    $begin_date = get_variable("begin_date");
    $end_date = get_variable("end_date");
    $user = get_variable("user");
    $extension = get_variable("extension");
    $conf_exten = get_variable("conf_exten");
    $server_ip = get_variable("server_ip");
    $group = get_variable("group");
    $stage = get_variable("stage");
    $submit = get_variable("submit");
    $SUBMIT = get_variable("SUBMIT");

    $STARTtime = date("U");
    $TODAY = date("Y-m-d");

    if ($begin_date=="") {$begin_date = $TODAY;}
    if ($end_date=="") {$end_date = $TODAY;}
    if ($user=="") {$user = $agent;}
    if ($agent=="") {$agent = $user;}

    $html='';

    $stmt="SELECT full_name,user_group from osdial_users where user='" . mysql_real_escape_string($agent) . "';";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $row=mysql_fetch_row($rslt);
    $full_name = $row[0];
    $user_group = $row[1];

    $stmt="SELECT * from osdial_live_agents where user='" . mysql_real_escape_string($agent) . "';";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $agents_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $agents_to_print) {
        $row=mysql_fetch_row($rslt);
        $Aserver_ip =		$row[2];
        $Asession_id =		$row[3];
        $Aextension =		$row[4];
        $Astatus =			$row[5];
        $Acampaign =		$row[7];
        $Alast_call =		$row[14];
        $Acl_campaigns =	$row[15];
        $i++;
    }

    $stmt="select * from osdial_campaigns;";
    $rslt=mysql_query($stmt, $link);
    if ($DB) {$html .= "$stmt\n";}
    $groups_to_print = mysql_num_rows($rslt);
    $i=0;
    while ($i < $groups_to_print) {
        $row=mysql_fetch_row($rslt);
        $groups[$i] = $row[0];
        $UPgroups[$i] = strtoupper($row[0]);
        $i++;
    }


    $html .= "<br><center><font size=4 color=$default_text>AGENT STATUS</font></center><br>\n";
    $html .= "<form name=lookup action=\"$PHP_SELF\" method=POST>\n";
    $html .= "  <input type=hidden name=ADD value=\"$ADD\">\n";
    $html .= "  <input type=hidden name=SUB value=\"$SUB\">\n";
    $html .= "  <input type=hidden name=DB value=\"$DB\">\n";
    $html .= "  <table align=center cellspacing=1 bgcolor=grey>\n";
    $html .= "    <tr class=tabheader>\n";
    $html .= "      <td>Agent #</td>\n";
    $html .= "      <td>&nbsp;<td>\n";
    $html .= "    </tr>\n";
    $html .= "    <tr class=tabfooter>\n";
    $html .= "      <td><input type=textbox name=agent value=\"$agent\"></td>\n";
    $html .= "      <td class=tabbutton><input type=submit name=submit value=LOOKUP></td>\n";
    $html .= "    </tr>\n";
    $html .= "  </table>\n";
    $html .= "</form>\n";
    $html .= "<br><br>\n";
    $html .= "<center>\n";
    $html .= "  <table width=620 cellpadding=2 cellspacing=0>\n";
    $html .= "    <tr class=tabheader>\n";
    $html .= "      <td align=left>\n";
    $html .= "        Agent Status for $agent\n";
    $html .= "      </td>\n";
    $html .= "      <td align=right>" . date("l F j, Y G:i:s A") . "</td>\n";
    $html .= "    </tr>\n";
    $html .= "    <tr bgcolor=\"$evenrows\">\n";
    $html .= "      <td align=left colspan=2>\n";
    $html .= "        <font color=$default_text size=3><b> &nbsp; \n";

    if (!$LOGview_reports) {
        $html .= "          <font color=red>You do not have permission to view this page</font>\n";
    } elseif ($stage == "live_campaign_change" and $LOGchange_agent_campaign > 0) {
        $stmt="UPDATE osdial_live_agents set campaign_id='" . mysql_real_escape_string($group) . "' where user='" . mysql_real_escape_string($agent) . "';";
        $rslt=mysql_query($stmt, $link);

        $html .= "          Agent $agent - $full_name changed to $group campaign<br>\n";
    } elseif ($stage == "log_agent_out" and $LOGchange_agent_campaign > 0) {
        $stmt="DELETE from osdial_live_agents where user='" . mysql_real_escape_string($agent) . "';";
        $rslt=mysql_query($stmt, $link);

        $stmt="UPDATE osdial_conferences SET extension='' WHERE extension='" . mysql_real_escape_string($ELOext) . "' AND server_ip='" . mysql_real_escape_string($ELOserver) . "';";
        $rslt=mysql_query($stmt, $link);

        $html .= "          Agent $agent - $full_name has been emergency logged out, make sure they close their web browser<br>\n";
    } else {
        $html .= "<table align=center>\n";
        $html .= "<tr><td colspan=2 style=\"height: 5px;\"></td></tr>\n";
        $html .= "<tr class=font3 style=\"color: $default_text;\"><td align=right>Agent ID:</td><td align=left>$agent - $full_name</td></tr>\n";
        $html .= "<tr class=font2 style=\"color: $default_text;\"><td align=right>Group:</td><td align=left>$user_group</td></tr>\n";
        if ($agents_to_print > 0) {
            $html .= "<tr class=font2><td align=right>Agent Server IP:</td><td align=left>$Aserver_ip</td></tr>\n";
            $html .= "<tr class=font2><td align=right>Session ID:</td><td align=left>$Asession_id</td></tr>\n";
            $html .= "<tr class=font2><td align=right>From Phone:</td><td align=left>$Aextension</td></tr>\n";
            $html .= "<tr class=font2><td align=right>Agent Campaign:</td><td align=left>$Acampaign</td></tr>\n";
            $html .= "<tr class=font2><td align=right>Status:</td><td align=left>$Astatus</td></tr>\n";
            $html .= "<tr class=font2><td align=right>Last Call Finished:</td><td align=left>$Alast_call</td></tr>\n";
            $html .= "<tr class=font2><td align=right>Closer Groups:</td><td align=left>$Acl_campaigns</td></tr>\n";
        }
        $html .= "<tr class=font2><td style=\"height: 5px;\" colspan=2></td></tr>\n";
        $html .= "<tr class=font2><td colspan=2>";
        $html .= "<a href=\"$PHP_SELF?ADD=999999&SUB=20&agent=$agent\">Agent Timesheet</a>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;";
        $html .= "<a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$agent\">Agent Stats</a>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;";
        $html .= "<a href=\"./admin.php?ADD=3&user=$agent\">Modify Agent</a>";
        $html .= "</td></tr>\n";
        $html .= "<tr class=font2><td style=\"height: 5px;\" colspan=2></td></tr>\n";
        $html .= "<tr class=font2><td align=right>&nbsp;</td><td align=left>&nbsp;</td></tr>\n";
        if ($agents_to_print > 0) {
            if ($LOGchange_agent_campaign > 0) {
                $html .= "<tr><td colspan=2 style=\"width: 100%;\">";
                $html .= "  <form name=campchange action=\"$PHP_SELF\" method=POST style=\"margin: 0px; padding: 0px;\">\n";
                $html .= "    <input type=hidden name=ADD value=\"$ADD\">\n";
                $html .= "    <input type=hidden name=SUB value=\"$SUB\">\n";
                $html .= "    <input type=hidden name=DB value=\"$DB\">\n";
                $html .= "    <input type=hidden name=agent value=\"$agent\">\n";
                $html .= "    <input type=hidden name=stage value=\"live_campaign_change\">\n";
                $html .= "    <table style=\"margin: 0px; padding: 0px;\" align=center width=100%>\n";
                $html .= "      <tr class=font2><td align=center colspan=2 style=\"font-weight: bold; color: $default_text;\">Live Agent Maintenance</td></tr>\n";
                $html .= "      <tr><td colspan=2 style=\"height:3px;\"></td></tr>\n";
                $html .= "      <tr bgcolor=yellow><td align=right>\n";
                $html .= "        <select name=group size=1 style=\"width: 100%;\">\n";
                $o=0;
                while ($groups_to_print > $o) {
                    $sel = ''; if ($UPgroups[$o] == $Acampaign) $sel = 'selected';
                    $html .= "          <option $sel value=\"$UPgroups[$o]\">$groups[$o]</option>\n";
                    $o++;
                }
                $html .= "        </select>\n";
                $html .= "      </td>\n";
                $html .= "      <td align=left><input style=\"width: 100%; font-size: 9pt;\" type=submit name=submit value=\"CHANGE CAMPAIGN\"></td>\n";
                $html .= "    </tr>\n";
                $html .= "   </table>\n";
                $html .= "  </form>\n";
                $html .= "</td></tr>\n";

                $html .= "<tr><td colspan=2 style=\"width: 100%;\">";
		        $html .= "  <form name=\"emergency\" action=\"$PHP_SELF\" method=POST style=\"margin: 0px; padding: 0px;\">\n";
                $html .= "  <input type=hidden name=ADD value=\"$ADD\">\n";
                $html .= "  <input type=hidden name=SUB value=\"$SUB\">\n";
                $html .= "  <input type=hidden name=DB value=\"$DB\">\n";
		        $html .= "  <input type=hidden name=agent value=\"$agent\">\n";
		        $html .= "  <input type=hidden name=extension value=\"$Aextension\">\n";
		        $html .= "  <input type=hidden name=conf_exten value=\"$Asession_id\">\n";
		        $html .= "  <input type=hidden name=server_ip value=\"$Aserver_ip\">\n";
		        $html .= "  <input type=hidden name=stage value=\"log_agent_out\">\n";
                $html .= "  <table style=\"margin: 0px; padding: 0px;\" align=center width=100%>\n";
                $html .= "    <tr class=font2 bgcolor=red>\n";
                $html .= "      <td colspan=2 align=center><input style=\"width: 100%;\" type=submit name=submit value=\"EMERGENCY LOG AGENT OUT\"></td>\n";
                $html .= "    </tr>\n";
                $html .= "  </table>\n";
                $html .= "  </form>\n";
                $html .= "</td></tr>\n";
            }
        } else {
            $html .= "<tr class=font3><td colspan=2 style=\"color: $default_text; font-weight: bold;\">Agent is not logged in</td></tr>\n";
        }

        $html .= "<tr class=font2><td align=right>&nbsp;</td><td align=left>&nbsp;</td></tr>\n";
	    $html .= "</table>\n";
    }

    $html .= "          </b></font>\n";
    $html .= "        </td>\n";
    $html .= "      </tr>\n";
    $html .= "      <tr bgcolor=$evenrows>\n";
    $html .= "        <td align=center colspan=2>\n";

    $ENDtime = date("U");
    $RUNtime = ($ENDtime - $STARTtime);

    $html .= "          <font size=0>\n";
    $html .= "            Script Runtime: $RUNtime seconds\n";
    $html .= "            <font color=$evenrows>|$stage|$group|</font>\n";
    $html .= "          </font>\n";
    $html .= "        </td>\n";
    $html .= "      </tr>\n";
    $html .= "    </table>\n";
    $html .= "  </center>\n";

    return $html;
}

?>
