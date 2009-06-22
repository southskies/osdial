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


#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,admin_template FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
$i=0;
while ($i < $qm_conf_ct)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =					$row[0];
	$admin_template =				$row[1];
	$i++;
	}

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

?>
<!-- html>
<head>
<title>OSDIAL recent sales lookup</title>
</head -->
<?
include("dbconnect.php");
#include("/home/www/phpsubs/stylesheet.inc");
?>
<link rel="stylesheet" type="text/css" href="templates/<?= $admin_template ?>/styles.css" media="screen">
<script language="JavaScript1.2">
function GatherListIDs() {
		var ListIDstr="";
		var ListIDstr2="";
	    for (var i=0; i<document.forms[0].list_id.options.length; i++) {
			ListIDstr2+=document.forms[0].list_id.options[i].value;
			ListIDstr2+=",";
			if (document.forms[0].list_id.options[i].selected) {
				ListIDstr+=document.forms[0].list_id.options[i].value;
				ListIDstr+=",";
			}
		}
		if (ListIDstr.length>0) {
			document.forms[0].list_ids.value=ListIDstr;
		} else {
			document.forms[0].list_ids.value=ListIDstr2;
		}
		return true;
}
</script>
<!-- body -->
<table align=center cellpadding=0 cellspacing=0>
<tr><td align=center>
<br><font color=navy size=+1>RECENT OUTBOUND SALES REPORT</font><br>
<form action="<?=$PHP_SELF ?>" method=post onSubmit="return GatherListIDs()">
<input type="hidden" name="list_ids">
<table border=0 cellpadding=5 cellspacing=0 align=center width=600>
<tr>
	<th colspan=3>
	<BR></th>
</tr>
<tr bgcolor='#CCCCCC'>
	<td colspan=3>
		<table width=100%>
			<td align=right width=200 nowrap><font class='standard_bold'>Campaign:</td>
			<td align=left><select name="dcampaign" onChange="this.form.submit();">
			<? 
			if ($dcampaign) {
				$stmt="select campaign_id, campaign_name from osdial_campaigns where campaign_id='$dcampaign'";
				$rslt=mysql_query($stmt, $link);
				while ($row=mysql_fetch_array($rslt)) {
					print "\t\t<option value='$row[campaign_id]' selected>$row[campaign_id] - $row[campaign_name]</option>\n";
				}
			} 
			?>
			<option value=''>----------- Select -----------</option>
			<?
				$stmt="select distinct vc.campaign_id, vc.campaign_name from osdial_campaigns vc, osdial_lists vl where vc.campaign_id=vl.campaign_id order by vc.campaign_name asc";
				$rslt=mysql_query($stmt, $link);
				while ($row=mysql_fetch_array($rslt)) {
					print "\t\t<option value='$row[campaign_id]'>$row[campaign_id] - $row[campaign_name]</option>\n";
				}
			?>
			</select></td>
			</tr>
			<?
			if ($dcampaign) {
			?>
			<tr bgcolor='#CCCCCC'>
				<td align=right width=200 nowrap><font class='standard_bold'>Select list ID(s) # (optional):</td>
				<td align=left><select name="list_id" multiple size="4">
				<?
					$stmt="select list_id, list_name from osdial_lists where campaign_id='$dcampaign' order by list_id asc";
					$rslt=mysql_query($stmt, $link);
					while ($row=mysql_fetch_array($rslt)) {
						print "\t\t<option value='$row[list_id]'>$row[list_id] - $row[list_name]</option>\n";
					}
				?>
				</select></td>
			</tr>
			<?
				}
			?>
		</table>
	</td>
</tr>
<tr bgcolor='#EEEEEE'>
	<th align=left width=350><font class='standard_bold'>View sales made within the last <select name="sales_time_frame">
	<option value=''>----------</option>
	<option value='15'>15 minutes</option>
	<option value='30'>30 minutes</option>
	<option value='45'>45 minutes</option>
	<option value='60'>1 hour</option>
	<option value='120'>2 hours</option>
	<option value='180'>3 hours</option>
	<option value='240'>4 hours</option>
	<option value='360'>6 hours</option>
	<option value='480'>8 hours</option>
	</select></th>
	<th width=50>OR...</th>
	<th align=right width=200><font class='standard_bold'>View the last <input type=text size=5 maxlength=5 name="sales_number"> sales**</font></th>
</tr>
<tr bgcolor='#EEEEEE'><th colspan=3><font class="small_standard">(If you enter values in both fields, the results will be limited by the first criteria met)</font></th></tr>
<tr bgcolor='#CCCCCC'>
	<td align=right><font class="standard_bold">Campaign is:&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name="forc" value="F">Transfer</font></td>
	<td colspan=2 align=left><font class="standard_bold">&nbsp;&nbsp;<input type=radio name="forc" value="C" checked>Non-transfer</font></td>
</tr>
<tr><th colspan=3><input type=submit name="submit_report" value="SUBMIT"></th></tr>
<!-- <tr><th colspan=3><input type=checkbox name="weekly_report" value="WEEKLY_REPORT"><font class="small_standard">Generate weekly report</font></th></tr> //-->
<tr><td colspan=3 align=center><font class="small_standard">** - sorted by call date</font></td></tr>
</table>
</form>
<?
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
	print "<HR>";
	print "<table border=0 cellpadding=5 cellspacing=0 align=center>";
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
	print "<tr><th colspan=8><font class='standard_bold' color='navy'>Last ".mysql_num_rows($rslt)." sales made</font></th></tr>\n";
	print "<tr>\n";
	print "\t<th><font color='navy' size='2'>Sales Rep(s)</font></th>\n";
	print "\t<th><font color='navy' size='2'>Customer Name</font></th>\n";
	print "\t<th><font color='navy' size='2'>Phone</font></th>\n";
	print "\t<th><font color='navy' size='2'>Recording ID</font></th>\n";
	print "\t<th><font color='navy' size='2'>Timestamp</font></th>\n";
	print "</tr>\n";
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

		if ($i%2==0) {$bgcolor="#999999";} else {$bgcolor="#CCCCCC";}
		print "<tr bgcolor='$bgcolor'>\n";
		print "\t<th><font size='2'>$rep_name</font></th>\n";
		print "\t<th><font size='2'>$row[0] $row[1]</font></th>\n";
		print "\t<th><font size='2'>$row[2]</font></th>\n";
		print "\t<th><font size='2'>$rec_row[0]</font></th>\n";
		print "\t<th><font size='2'>$row[3]</font></th>\n";
		print "</tr>\n";
		flush();
	}
	print "</table>";
	passthru("$WeBServeRRooT/admin/spreadsheet_sales_viewer.pl $list_ids $sales_number $timestamp $forc $now $dcampaign");
#	print "\n\n<BR>$WeBServeRRooT/osdial/spreadsheet_sales_viewer.pl $list_ids $sales_number $timestamp $forc $now $dcampaign<BR>\n";
	flush();
	print "<table align=center border=0 cellpadding=3 cellspacing=5 width=700><tr bgcolor='#CCCCCC'>";
	if ($forc=="F") {
		print "<th width='50%'><font size='2'><a href='osdial_fronter_report_$now.xls'>View complete Excel fronter report for this shift</a></font></th>";
	}
	print "<th width='50%'><font size='2'<a href='osdial_closer_report_$now.xls'>View complete Excel sales report for this shift</a></font></th>";
	print "</tr></table>";
}
?>
<!-- /body>
</html -->

