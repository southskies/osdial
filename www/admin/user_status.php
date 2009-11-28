<?
### user_status.php
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

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["begin_date"]))				{$begin_date=$_GET["begin_date"];}
	elseif (isset($_POST["begin_date"]))		{$begin_date=$_POST["begin_date"];}
if (isset($_GET["end_date"]))				{$end_date=$_GET["end_date"];}
	elseif (isset($_POST["end_date"]))		{$end_date=$_POST["end_date"];}
if (isset($_GET["user"]))				{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))		{$user=$_POST["user"];}
if (isset($_GET["extension"]))				{$ELOext=$_GET["extension"];}
	elseif (isset($_POST["extension"]))		{$ELOext=$_POST["extension"];}
if (isset($_GET["conf_exten"]))				{$ELOconf=$_GET["conf_exten"];}
	elseif (isset($_POST["conf_exten"]))		{$ELOconf=$_POST["conf_exten"];}
if (isset($_GET["server_ip"]))				{$ELOserver=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))		{$ELOserver=$_POST["server_ip"];}
if (isset($_GET["group"]))				{$group=$_GET["group"];}
	elseif (isset($_POST["group"]))		{$group=$_POST["group"];}
if (isset($_GET["stage"]))				{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))		{$stage=$_POST["stage"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))		{$submit=$_POST["submit"];}
if (isset($_GET["SUBMIT"]))				{$SUBMIT=$_GET["SUBMIT"];}
	elseif (isset($_POST["SUBMIT"]))		{$SUBMIT=$_POST["SUBMIT"];}

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");

if (!isset($begin_date)) {$begin_date = $TODAY;}
if (!isset($end_date)) {$end_date = $TODAY;}
if ($begin_date=="") {$begin_date = $TODAY;}
if ($end_date=="") {$end_date = $TODAY;}

$html='';

$stmt="SELECT full_name,view_reports,change_agent_campaign,count(*) FROM osdial_users WHERE user='$PHP_AUTH_USER' AND pass='$PHP_AUTH_PW' AND user_level > 7;";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$row=mysql_fetch_row($rslt);
$LOGfullname=$row[0];
$view_reports=$row[1];
$change_agent_campaign=$row[2];
$auth=$row[3];

$fp = fopen ("./project_auth_entries.txt", "a");
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

if((strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth) or (!$view_reports)) {
    Header("WWW-Authenticate: Basic realm=\"OSIDAL-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    fwrite ($fp, "OSDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
    fclose($fp);
    exit;
}

fwrite ($fp, "OSDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
fclose($fp);

$stmt="SELECT full_name,user_group from osdial_users where user='" . mysql_real_escape_string($user) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {$html .= "$stmt\n";}
$row=mysql_fetch_row($rslt);
$full_name = $row[0];
$user_group = $row[1];

$stmt="SELECT * from osdial_live_agents where user='" . mysql_real_escape_string($user) . "';";
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
    $groups[$i] =$row[0];
    $i++;
}


$html .= "<html>\n";
$html .= "<head>\n";
$html .= "  <title>OSDIAL ADMIN: User Status</title>\n";
$html .= "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
$html .= "</head>\n";
$html .= "<body bgcolor=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
$html .= "<center>\n";
$html .= "  <table width=620 bgcolor=#D9E6FE cellpadding=2 cellspacing=0>\n";
$html .= "    <tr bgcolor=#015B91>\n";
$html .= "      <td align=left>\n";
$html .= "        <b> &nbsp; <a href=\"./admin.php\"><font face=\"Arial,Helvetica\" color=white size=2>OSDIAL ADMIN</font></a><font face=\"Arial,Helvetica\" color=white size=2>: User Status for $user</font></b>\n";
$html .= "      </td>\n";
$html .= "      <td align=right><font face=\"Arial,Helvetica\" color=white size=2><b>" . date("l F j, Y G:i:s A") . " &nbsp; </b></font></td>\n";
$html .= "    </tr>\n";
$html .= "    <tr bgcolor=\"#F0F5FE\">\n";
$html .= "      <td align=left colspan=2>\n";
$html .= "        <font face=\"Arial,Helvetica\" color=$default_text size=3><b> &nbsp; \n";

if ($stage == "live_campaign_change" and $change_agent_campaign > 0) {
    $stmt="UPDATE osdial_live_agents set campaign_id='" . mysql_real_escape_string($group) . "' where user='" . mysql_real_escape_string($user) . "';";
    $rslt=mysql_query($stmt, $link);

    $html .= "          Agent $user - $full_name changed to $group campaign<br>\n";
} elseif ($stage == "log_agent_out" and $change_agent_campaign > 0) {
    $stmt="DELETE from osdial_live_agents where user='" . mysql_real_escape_string($user) . "';";
    $rslt=mysql_query($stmt, $link);

    $stmt="UPDATE osdial_conferences SET extension='' WHERE extension='" . mysql_real_escape_string($ELOext) . "' AND server_ip='" . mysql_real_escape_string($ELOserver) . "';";
    $rslt=mysql_query($stmt, $link);

    $html .= "          Agent $user - $full_name has been emergency logged out, make sure they close their web browser<br>\n";
} else {
    if ($agents_to_print > 0) {
        $html .= "<pre>";
        $html .= "Agent Logged in at server:  $Aserver_ip\n";
        $html .= "               in session:  $Asession_id\n";
        $html .= "               from phone:  $Aextension\n";
        $html .= "Agent is in campaign:       $Acampaign\n";
        $html .= "              status:       $Astatus\n";
        $html .= " hungup last call at:       $Alast_call\n";
        $html .= "       Closer groups:       $Acl_campaigns\n\n";
	    $html .= "</pre>\n";

        if ($change_agent_campaign > 0) {
            $html .= "          <form action=$PHP_SELF method=POST>\n";
            $html .= "            <input type=hidden name=user value=\"$user\">\n";
            $html .= "            <input type=hidden name=stage value=\"live_campaign_change\">\n";
            $html .= "            Current Campaign:\n";
            $html .= "            <select size=1 name=group>\n";
            $o=0;
            while ($groups_to_print > $o) {
                if ($groups[$o] == "$Acampaign") {
                    $html .= "              <option selected value=\"$groups[$o]\">$groups[$o]</option>\n";
                } else {
                    $html .= "              <option value=\"$groups[$o]\">$groups[$o]</option>\n";
                }
                $o++;
            }
            $html .= "            </select>\n";
            $html .= "            <input type=submit name=submit value=CHANGE>\n";
            $html .= "            <br>\n";
            $html .= "          </form>\n";

		    $html .= "          <form action=$PHP_SELF method=POST>\n";
		    $html .= "            <input type=hidden name=user value=\"$user\">\n";
		    $html .= "            <input type=hidden name=extension value=\"$Aextension\">\n";
		    $html .= "            <input type=hidden name=conf_exten value=\"$Asession_id\">\n";
		    $html .= "            <input type=hidden name=server_ip value=\"$Aserver_ip\">\n";
		    $html .= "            <input type=hidden name=stage value=\"log_agent_out\">\n";
		    $html .= "            <input type=submit name=submit value=\"EMERGENCY LOG AGENT OUT\">\n";
            $html .= "            <br>\n";
            $html .= "          </form>\n";
        }
    } else {
        $html .= "          Agent is not logged in<br>\n";
    }

    $html .= "           &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $user - $full_name \n";
    $html .= "           &nbsp; &nbsp; &nbsp; GROUP: $user_group <br><br>\n";

    $html .= "          <a href=\"./admin.php?ADD=999999&SUB=20&agent=$user\">OSDIAL Time Sheet</a>\n";
    $html .= "           - <a href=\"./user_stats.php?user=$user\">User Stats</a>\n";
    $html .= "           - <a href=\"./admin.php?ADD=3&user=$user\">Modify User</a>\n";
}

$html .= "          </b></font>\n";
$html .= "        </td>\n";
$html .= "      </tr>\n";
$html .= "      <tr>\n";
$html .= "        <td align=left colspan=2>\n";

$ENDtime = date("U");
$RUNtime = ($ENDtime - $STARTtime);

$html .= "          <br><br><br>\n";
$html .= "          <font size=0>\n";
$html .= "            <br><br><br>\n";
$html .= "            script runtime: $RUNtime seconds\n";
$html .= "          </font>\n";
$html .= "          |$stage|$group|\n";
$html .= "        </td>\n";
$html .= "      </tr>\n";
$html .= "    </table>\n";
$html .= "  </center>\n";

$html .= "</body>\n";
$html .= "</html>\n";

echo $html;

?>
