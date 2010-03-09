<? header("Pragma: no-cache"); 

# osdial_sales_viewer.php - OSDIAL administration page
# 
# Copyright (C) 2008  Matt Florell,Joe Johnson <vicidial@gmail.com>  LICENSE: AGPLv2
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>            LICENSE: AGPLv3
# Copyright (C) 2009  Steve Szmidt <techs@callcentersg.com>          LICENSE: AGPLv3
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


if (isset($_GET["dcampaign"]))					{$dcampaign=$_GET["dcampaign"];}
	elseif (isset($_POST["dcampaign"]))			{$dcampaign=$_POST["dcampaign"];}
if (isset($_GET["submit_report"]))				{$submit_report=$_GET["submit_report"];}
	elseif (isset($_POST["submit_report"]))		{$submit_report=$_POST["submit_report"];}
if (isset($_GET["list_ids"]))					{$list_ids=$_GET["list_ids"];}
	elseif (isset($_POST["list_ids"]))			{$list_ids=$_POST["list_ids"];}
if (isset($_GET["sales_number"]))				{$sales_number=$_GET["sales_number"];}
	elseif (isset($_POST["sales_number"]))		{$sales_number=$_POST["sales_number"];}
if (isset($_GET["sales_time_frame"]))			{$sales_time_frame=$_GET["sales_time_frame"];}
	elseif (isset($_POST["sales_time_frame"]))	{$sales_time_frame=$_POST["sales_time_frame"];}
if (isset($_GET["forc"]))						{$forc=$_GET["forc"];}
	elseif (isset($_POST["forc"]))				{$forc=$_POST["forc"];}

include("include/dbconnect.php");
include($WeBServeRRooT . "/admin/include/functions.php");
include($WeBServeRRooT . "/admin/include/variables.php");
include($WeBServeRRooT . "/admin/include/auth.php");
include($WeBServeRRooT . "/admin/templates/default/display.php");
include($WeBServeRRooT . "/admin/templates/" . $system_settings['admin_template'] . "/display.php");

echo "<html>\n";
echo "<head>\n";
echo "  <title>OSDIAL recent sales lookup</title>\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/default/styles.css\" media=\"screen\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"templates/" . $system_settings['admin_template'] . "/styles.css\" media=\"screen\">\n";
echo "<script language=\"JavaScript1.2\">\n";
echo "  function GatherListIDs() {\n";
echo "    var ListIDstr=\"\";\n";
echo "    var ListIDstr2=\"\";\n";
echo "    for (var i=0; i<document.forms[0].list_id.options.length; i++) {\n";
echo "      ListIDstr2+=document.forms[0].list_id.options[i].value;\n";
echo "      ListIDstr2+=\",\";\n";
echo "      if (document.forms[0].list_id.options[i].selected) {\n";
echo "        ListIDstr+=document.forms[0].list_id.options[i].value;\n";
echo "        ListIDstr+=\",\";\n";
echo "      }\n";
echo "    }\n";
echo "    if (ListIDstr.length>0) {\n";
echo "      document.forms[0].list_ids.value=ListIDstr;\n";
echo "    } else {\n";
echo "      document.forms[0].list_ids.value=ListIDstr2;\n";
echo "    }\n";
echo "  return true;\n";
echo "}\n";
echo "</script>\n";
echo "</head>\n";
echo "<body>\n";
echo "<table align=center cellpadding=0 cellspacing=0>\n";
echo "  <tr>\n";
echo "    <td align=center>\n";
echo "      <br><font color=$default_text size=+1>RECENT OUTBOUND SALES REPORT</font><br>\n";
echo "      <form action=\"$PHP_SELF\" method=post onSubmit=\"return GatherListIDs()\">\n";
echo "      <input type=\"hidden\" name=\"list_ids\">\n";
echo "      <table border=0 cellpadding=5 cellspacing=0 align=center width=600>\n";
echo "        <tr>\n";
echo "          <th colspan=3><br></th>\n";
echo "        </tr>\n";
echo "        <tr bgcolor='$oddrows'>\n";
echo "          <td colspan=3>\n";
echo "            <table width=100%>\n";
echo "              <tr>\n";
echo "                <td align=right width=200 nowrap><font class=font2>Campaign:</td>\n";
echo "                <td align=left>\n";
echo "                  <select name=\"dcampaign\" onChange=\"this.form.submit();\">\n";
if ($dcampaign) {
    $stmt="SELECT campaign_id, campaign_name FROM osdial_campaigns WHERE campaign_id='$dcampaign';";
    $rslt=mysql_query($stmt, $link);
    while ($row=mysql_fetch_array($rslt)) {
        echo "                  <option value='$row[campaign_id]' selected>" . mclabel($row[campaign_id]) . " - $row[campaign_name]</option>\n";
    }
} 
echo "                  <option value=''>----------- Select -----------</option>\n";
$stmt=sprintf("SELECT distinct vc.campaign_id, vc.campaign_name FROM osdial_campaigns vc, osdial_lists vl WHERE vc.campaign_id=vl.campaign_id AND vc.campaign_id IN %s ORDER BY vc.campaign_name ASC;",$LOG['allowed_campaignsSQL']);
$rslt=mysql_query($stmt, $link);
while ($row=mysql_fetch_array($rslt)) {
    echo "                  <option value='$row[campaign_id]'>" . mclabel($row[campaign_id]) . " - $row[campaign_name]</option>\n";
}
echo "                  </select>\n";
echo "                </td>\n";
echo "              </tr>\n";
if ($dcampaign) {
    echo "              <tr bgcolor='$oddrows'>\n";
    echo "                <td align=right width=200 nowrap><font class=font2>List ID(s) #:<br><span class=font1>(optional)</span></td>\n";
    echo "                <td align=left>\n";
    echo "                  <select name=\"list_id\" multiple size=\"4\">\n";
    $stmt="select list_id, list_name from osdial_lists where campaign_id='$dcampaign' order by list_id asc";
    $rslt=mysql_query($stmt, $link);
    while ($row=mysql_fetch_array($rslt)) {
        $lsel='';
	    $lists=explode(",", $list_ids);
	    for ($i=0; $i<count($lists); $i++) {
		    if (strlen($lists[$i]>0) and $lists[$i] == $row['list_id']) $lsel = "selected";
	    }
        echo "                    <option $lsel value='$row[list_id]'>$row[list_id] - $row[list_name]</option>\n";
    }
    echo "                  </select>\n";
    echo "                </td>\n";
    echo "              </tr>\n";
}
echo "            </table>\n";
echo "          </td>\n";
echo "        </tr>\n";
echo "        <tr bgcolor=$oddrows>\n";
echo "          <td align=center width=350 colspan=3><br><font class=font2>View sales made within the last\n";
echo "            <select name=sales_time_frame>\n";
echo "              <option value=''>----------</option>\n";
echo "              <option value='15'>15 minutes</option>\n";
echo "              <option value='30'>30 minutes</option>\n";
echo "              <option value='45'>45 minutes</option>\n";
echo "              <option value='60'>1 hour</option>\n";
echo "              <option value='120'>2 hours</option>\n";
echo "              <option value='180'>3 hours</option>\n";
echo "              <option value='240'>4 hours</option>\n";
echo "              <option value='360'>6 hours</option>\n";
echo "              <option value='480'>8 hours</option>\n";
echo "            </select><br>\n";
echo "          <b>OR</b><br>\n";
echo "          <font class=font2>View the last <input type=text size=5 maxlength=5 name=sales_number> sales**</font>\n";
echo "        </tr>\n";
echo "        <tr bgcolor='$oddrows'>\n";
echo "          <td colspan=3 align=center><font class=font1>(If you enter values in both fields, the results will be limited by the first criteria met)</font><br><br></td>\n";
echo "        </tr>\n";
echo "        <tr bgcolor='$oddrows'>\n";
echo "          <td align=right><font class=font2>Campaign is:&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=forc value=F>Transfer</font></td>\n";
echo "          <td colspan=2 align=left><font class=font2>&nbsp;&nbsp;<input type=radio name=forc value=C checked>Non-transfer</font></td>\n";
echo "        </tr>\n";
echo "        <tr class=tabfooter>\n";
echo "          <td colspan=3 class=tabbutton><input type=submit name=submit_report value=SUBMIT></td>\n";
echo "        </tr>\n";
echo "        <!-- <tr><th colspan=3><input type=checkbox name=weekly_report value=WEEKLY_REPORT><font class=small_standard>Generate weekly report</font></th></tr> //-->\n";
echo "        <tr><td colspan=3 align=center><font class=font1>** - sorted by call date</font></td></tr>\n";
echo "      </table>\n";
echo "      </form>\n";
if ($submit_report && $list_ids) {

	$now=date("YmdHis");
	$list_id_clause="and v.list_id in (";
	$lists=explode(",", $list_ids);
	for ($i=0; $i<count($lists); $i++) {
		if (strlen($lists[$i]>0)) {	$list_id_clause.="$lists[$i], "; }
	}
	$list_id_clause=substr($list_id_clause, 0, -2);
	$list_id_clause.=")";

	if ($sales_number && $sales_number>0) {
		$sales_number=eregi_replace("[^0-9]", "", $sales_number);
		$limit_clause="limit $sales_number";
	} else {
		$sales_number=0;
		$limit_clause="";
	}
	if ($sales_time_frame && $sales_time_frame>0) {
		$hours=$sales_time_frame/60;
		$timestamp=date("YmdHis", mktime(date("H"),(date("i")-$sales_time_frame),date("s"),date("m"),date("d"),date("Y")));
	} else {
		$timestamp=date("YmdHis", mktime(date("H"),date("i"),date("s"),date("m"),(date("d")-1),date("Y")));
		$hours=24;
	}
	echo "      <hr>\n";
	echo "      <table border=0 cellpadding=5 cellspacing=1 bgcolor=grey align=center>\n";
	$i=0;

	$dfile=fopen("discover_stmts.txt", "w");
	if ($forc=="C") {
		$stmt="select v.first_name, v.last_name, v.phone_number, vl.call_date, v.lead_id, u.full_name from osdial_users u, osdial_list v, osdial_log vl where vl.call_date>='$timestamp' and vl.lead_id=v.lead_id and v.status='SALE' $list_id_clause and vl.user=u.user order by call_date desc $limit_clause";
	} else {
		$stmt="select v.first_name, v.last_name, v.phone_number, vl.call_date, v.lead_id, vl.user, vl.closer from osdial_list v, osdial_xfer_log vl where vl.call_date>='$timestamp' and vl.lead_id=v.lead_id and v.status='SALE' $list_id_clause order by call_date desc $limit_clause";
	}
	fwrite($dfile, "$stmt\n");
	$rslt=mysql_query($stmt, $link);
	$q=0;
	echo "        <tr>\n";
    echo "          <th colspan=8 bgcolor=$oddrows><font class='standard_bold' color='$default_text'>Last ".mysql_num_rows($rslt)." sales made</font></th>\n";
    echo "        </tr>\n";
	echo "        <tr class=tabheader>\n";
	echo "          <td>Sales Rep(s)</td>\n";
	echo "          <td>Customer Name</td>\n";
	echo "          <td>Phone</td>\n";
	echo "          <td>Recording ID</td>\n";
	echo "          <td>Timestamp</td>\n";
	echo "        </tr>\n";
    $i=0;
	while ($row=mysql_fetch_row($rslt)) {
		$rec_stmt="select max(recording_id) from recording_log where lead_id='$row[4]'";
		$rec_rslt=mysql_query($rec_stmt, $link);
		$rec_row=mysql_fetch_row($rec_rslt);

		if ($forc=="F") {
			$rep_stmt="select full_name from osdial_users where user='$row[5]'";
			$rep_rslt=mysql_query($rep_stmt, $link);
			$fr_row=mysql_fetch_array($rep_rslt);

			$rep_stmt="select full_name from osdial_users where user='$row[6]'";
			$rep_rslt=mysql_query($rep_stmt, $link);
			$cl_row=mysql_fetch_array($rep_rslt);

			$rep_name="$fr_row[full_name]/$cl_row[full_name]";
		} else {
			$rep_name=$row[5];
		}

		if ($i%2==0) {$bgcolor=$evenrows;} else {$bgcolor=$oddrows;}
		echo "        <tr class=\"row font1\" bgcolor='$bgcolor'>\n";
		echo "          <td>$rep_name</td>\n";
		echo "          <td>$row[0] $row[1]</td>\n";
		echo "          <td>$row[2]</td>\n";
		echo "          <td>$rec_row[0]</td>\n";
		echo "          <td>$row[3]</td>\n";
		echo "        </tr>\n";
		flush();
        $i++;
	}
	echo "        <tr class=tabfooter>\n";
	echo "          <td></td>\n";
	echo "          <td></td>\n";
	echo "          <td></td>\n";
	echo "          <td></td>\n";
	echo "          <td></td>\n";
	echo "        </tr>\n";
	echo "      </table>";
	echo "<!-- $WeBServeRRooT/admin/spreadsheet_sales_viewer.pl $list_ids $sales_number $timestamp $forc $now $dcampaign -->\n";
	passthru("$WeBServeRRooT/admin/spreadsheet_sales_viewer.pl $list_ids $sales_number $timestamp $forc $now $dcampaign");
	flush();
	echo "<table align=center border=0 cellpadding=3 cellspacing=5 width=700><tr bgcolor='$oddrows'>";
	if ($forc=="F") {
		echo "<th width='50%'><font size='2'><a href='osdial_fronter_report_$now.xls'>View complete Excel fronter report for this shift</a></font></th>";
	}
	echo "<th width='50%'><font size='2'<a href='osdial_closer_report_$now.xls'>View complete Excel sales report for this shift</a></font></th>";
	echo "</tr></table>";
}
echo "</body>\n";
echo "</html>\n";
?>

