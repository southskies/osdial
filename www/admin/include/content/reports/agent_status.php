<?php
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
    $close_after_emergency_logout = get_variable("close_after_emergency_logout");
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
    $form='';
    $table='';

    $company_prefix = "";
    if ($LOG['multicomp_user'] > 0) {
        $company_prefix = $LOG['company_prefix'];
        if (OSDsubstr($agent,0,3) == $LOG['company_prefix']) {
            $agent = OSDsubstr($agent,3,OSDstrlen($agent));
        }
    }

    $head .= "<br>\n";
    $head .= "<center><font size=4 class=top_header color=$default_text>AGENT STATUS</font></center><br>\n";
    if ($agent) {
        $stmt=sprintf("SELECT full_name,user_group FROM osdial_users WHERE user_group IN %s AND user='%s';",$LOG['allowed_usergroupsSQL'],mres($company_prefix.$agent));
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $row=mysql_fetch_row($rslt);
        $full_name = $row[0];
        $user_group = $row[1];

        $head .= "<center><font color=$default_text size=3><b>$agent - $full_name</b></font></center>\n";
        $head .= "<center>\n";
        $head .= "<span class=font2>\n";
        if ($LOG['view_agent_timesheet']) $head .= "<a href=\"$PHP_SELF?ADD=999999&SUB=20&agent=$agent\">Agent Timesheet</a>\n";
        if ($LOG['view_agent_stats']) $head .= " - <a href=\"$PHP_SELF?ADD=999999&SUB=21&agent=$agent\">Agent Stats</a>\n";
        $head .= " - <a href=\"$PHP_SELF?ADD=3&user=$agent\">Modify Agent</a></span>\n";
        $head .= "</center>\n";
    }

    $form .= "<br>\n";
    $form .= "<form name=lookup action=\"$PHP_SELF\" method=POST>\n";
    $form .= "  <input type=hidden name=ADD value=\"$ADD\">\n";
    $form .= "  <input type=hidden name=SUB value=\"$SUB\">\n";
    $form .= "  <input type=hidden name=DB value=\"$DB\">\n";
    $form .= "  <table class=shadedtable align=center cellspacing=1 bgcolor=grey width=350>\n";
    $form .= "    <tr class=tabheader>\n";
    $form .= "      <td>Agent #</td>\n";
    $form .= "      <td>&nbsp;<td>\n";
    $form .= "    </tr>\n";
    $form .= "    <tr class=tabheader>\n";
    $form .= "      <td><input type=textbox name=agent value=\"$agent\"></td>\n";
    $form .= "      <td class=tabbutton><input type=submit name=submit value=LOOKUP></td>\n";
    $form .= "    </tr>\n";
    $form .= "  </table>\n";
    $form .= "</form>\n";

    if ($agent) {
        $stmt=sprintf("SELECT osdial_live_agents.* FROM osdial_live_agents JOIN osdial_users ON (osdial_live_agents.user=osdial_users.user) WHERE user_group IN %s AND osdial_live_agents.user='%s';",$LOG['allowed_usergroupsSQL'],mres($company_prefix.$agent));
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

        $stmt=sprintf("SELECT * FROM osdial_campaigns WHERE campaign_id IN %s;",$LOG['allowed_campaignsSQL']);
        $rslt=mysql_query($stmt, $link);
        if ($DB) {$html .= "$stmt\n";}
        $groups_to_print = mysql_num_rows($rslt);
        $i=0;
        while ($i < $groups_to_print) {
            $row=mysql_fetch_row($rslt);
            $groups[$i] = $row[0];
            $UPgroups[$i] = OSDstrtoupper($row[0]);
            $i++;
        }

        $table .= "<br>\n";
        $table .= "<center>\n";
        $table .= "  <table class=shadedtable width=620 cellpadding=2 cellspacing=0>\n";
        $table .= "    <tr class=tabheader>\n";
        $table .= "      <td>&nbsp;</td>\n";
        $table .= "      <td></td>\n";
        $table .= "    </tr>\n";
        $table .= "    <tr bgcolor=\"$evenrows\">\n";
        $table .= "      <td align=left colspan=2>\n";
        $table .= "        <font color=$default_text size=3><b> &nbsp; \n";

        if (!$LOG['view_reports']) {
            $table .= "<center><font color=red>You do not have permission to view this page</font></center>\n";
        } else {
            $table .= "<table align=center width=100%>\n";
            $table .= "<tr class=font2><td align=center colspan=2 width=50%>Run Date: " . date($config['settings']['default_date_format']) . "</td></tr>\n";
            $table .= "<tr class=font2><td align=center colspan=2>Agent ID: $agent</td></tr>\n";
            $table .= "<tr class=font2><td align=center colspan=2>Agent Full Name: $full_name</td></tr>\n";
            $table .= "<tr class=font2><td align=center colspan=2>Agent Group: " . mclabel($user_group) . "</td></tr>\n";
            if ($agents_to_print > 0) {
                $table .= "<tr class=font2><td align=center colspan=2>Agent Server IP: $Aserver_ip</td></tr>\n";
                $table .= "<tr class=font2><td align=center colspan=2>Session ID: $Asession_id</td></tr>\n";
                $table .= "<tr class=font2><td align=center colspan=2>From Phone: $Aextension</td></tr>\n";
                $table .= "<tr class=font2><td align=center colspan=2>Agent Campaign: " . mclabel($Acampaign) . "</td></tr>\n";
                $table .= "<tr class=font2><td align=center colspan=2>Status: $Astatus</td></tr>\n";
                $table .= "<tr class=font2><td align=center colspan=2>Last Call Finished: $Alast_call</td></tr>\n";
                $table .= "<tr class=font2><td align=center colspan=2>Closer Groups: $Acl_campaigns</td></tr>\n";
            }
            $table .= "<tr class=font2><td colspan=2>&nbsp;</td></tr>\n";
            if ($agents_to_print > 0) {
                if ($LOG['change_agent_campaign'] > 0 and $stage != "live_campaign_change" and $stage != "log_agent_out") {
                    $table .= "<tr><td align=center colspan=2 style=\"width: 100%;\">";
                    $table .= "  <form name=campchange action=\"$PHP_SELF\" method=POST style=\"margin: 0px; padding: 0px;\">\n";
                    $table .= "    <input type=hidden name=ADD value=\"$ADD\">\n";
                    $table .= "    <input type=hidden name=SUB value=\"$SUB\">\n";
                    $table .= "    <input type=hidden name=DB value=\"$DB\">\n";
                    $table .= "    <input type=hidden name=agent value=\"$agent\">\n";
                    $table .= "    <input type=hidden name=stage value=\"live_campaign_change\">\n";
                    $table .= "    <table style=\"margin: 0px; padding: 0px;\" align=center width=50%>\n";
                    $table .= "      <tr class=font2><td align=center colspan=2 style=\"font-weight: bold; color: $default_text;\">Live Agent Maintenance</td></tr>\n";
                    $table .= "      <tr><td colspan=2 style=\"height:3px;\"></td></tr>\n";
                    $table .= "      <tr bgcolor=yellow><td align=right>\n";
                    $table .= "        <select name=group size=1 style=\"width: 100%;\">\n";
                    $o=0;
                    while ($groups_to_print > $o) {
                        $sel = ''; if ($UPgroups[$o] == $Acampaign) $sel = 'selected';
                        $table .= "          <option $sel value=\"$UPgroups[$o]\">" . mclabel($groups[$o]) . "</option>\n";
                        $o++;
                    }
                    $table .= "        </select>\n";
                    $table .= "      </td>\n";
                    $table .= "      <td align=left><input style=\"width: 100%; font-size: 9pt;\" type=submit name=submit value=\"CHANGE CAMPAIGN\"></td>\n";
                    $table .= "    </tr>\n";
                    $table .= "   </table>\n";
                    $table .= "  </form>\n";
                    $table .= "</td></tr>\n";
    
                    $table .= "<tr><td colspan=2 align=center style=\"width: 50%;\">";
		            $table .= "  <form name=\"emergency\" action=\"$PHP_SELF\" method=POST style=\"margin: 0px; padding: 0px;\">\n";
                    $table .= "  <input type=hidden name=ADD value=\"$ADD\">\n";
                    $table .= "  <input type=hidden name=SUB value=\"$SUB\">\n";
                    $table .= "  <input type=hidden name=DB value=\"$DB\">\n";
		            $table .= "  <input type=hidden name=agent value=\"$agent\">\n";
		            $table .= "  <input type=hidden name=extension value=\"$Aextension\">\n";
		            $table .= "  <input type=hidden name=conf_exten value=\"$Asession_id\">\n";
		            $table .= "  <input type=hidden name=server_ip value=\"$Aserver_ip\">\n";
		            $table .= "  <input type=hidden name=close_after_emergency_logout value=\"$close_after_emergency_logout\">\n";
		            $table .= "  <input type=hidden name=stage value=\"log_agent_out\">\n";
                    $table .= "  <table style=\"margin: 0px; padding: 0px;\" align=center width=50%>\n";
                    $table .= "    <tr class=font2 bgcolor=red>\n";
                    $table .= "      <td colspan=2 align=center><input style=\"width: 100%;\" type=submit name=submit value=\"EMERGENCY LOG AGENT OUT\"></td>\n";
                    $table .= "    </tr>\n";
                    $table .= "  </table>\n";
                    $table .= "  </form>\n";
                    $table .= "</td></tr>\n";
                }
            }
	        $table .= "</table>\n";

            if ($stage == "live_campaign_change" and $LOG['change_agent_campaign'] > 0) {
                $stmt=sprintf("UPDATE osdial_live_agents SET campaign_id='%' WHERE user='%s';",mres($group),mres($company_prefix.$agent));
                $rslt=mysql_query($stmt, $link);
    
                $table .= "<center>Agent was changed to $group campaign.</center><br>\n";
            } elseif ($stage == "log_agent_out" and $LOG['change_agent_campaign'] > 0) {
                $stmt=sprintf("SELECT channel FROM osdial_live_agents WHERE user='%s' LIMIT 1;",mres($company_prefix.$agent));
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $custchannel = $row[0];

                $stmt=sprintf("DELETE FROM osdial_live_agents WHERE user='%s';",mres($company_prefix.$agent));
                $rslt=mysql_query($stmt, $link);

                $stmt=sprintf("UPDATE osdial_conferences SET extension='' WHERE extension='%s' AND server_ip='%s';",mres($extension),mres($server_ip));
                $rslt=mysql_query($stmt, $link);

                $queryCID = "ULGH3459$STARTtime";
                $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Command','%s','Command: %s','','','','','','','','','');",mres(date('Y-m-d H:i:s')),mres($server_ip),mres($queryCID),mres('meetme kick '.$conf_exten.' all'));
                $rslt=mysql_query($stmt, $link);

                if (!empty($custchannel)) {
                    $queryCID = "ULGH3460$STARTtime";
                    $stmt=sprintf("INSERT INTO osdial_manager VALUES('','','%s','NEW','N','%s','','Hangup','%s','Channel: %s','','','','','','','','','');",mres(date('Y-m-d H:i:s')),mres($server_ip),mres($queryCID),mres($custchannel));
                    $rslt=mysql_query($stmt, $link);
                }

                $table .= "<center>Agent has been emergency logged out.<br>Please, make sure they close their web browser</center><br>\n";
                if ($close_after_emergency_logout == "Y") {
                    $table .= "\n\n<script language=\"javascript\">\n";
                    $table .= "window.close();\n";
                    $table .= "</script>\n\n";
                    flush();
                }
            } elseif ($agents_to_print < 1) {
                $table .= "<center>Agent is not logged in.</center>\n";
            }
        }

        $table .= "          </b></font>\n";
        $table .= "        </td>\n";
        $table .= "      </tr>\n";
        $table .= "      <tr bgcolor=$evenrows>\n";
        $table .= "        <td align=center colspan=2>\n";

        $ENDtime = date("U");
        $RUNtime = ($ENDtime - $STARTtime);

        $table .= "          <font size=0>\n";
        $table .= "            Script Runtime: $RUNtime seconds\n";
        $table .= "            <font color=$evenrows>|$stage|$group|</font>\n";
        $table .= "          </font>\n";
        $table .= "        </td>\n";
        $table .= "      </tr>\n";
        $table .= "    </table>\n";
        $table .= "  </center>\n";
    }

    $html .= "<div class=noprint>$head</div>\n";
    $html .= "<div class=noprint>$form</div>\n";
    $html .= "<div class=noprint>$table</div>\n";

    return $html;
}

?>
