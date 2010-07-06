<?
# modify_lead.php
#
# Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
# Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
# This is near complete rewrite of the upstream admin_modify_lead..
#

if ($ADD==1121) {
    if ($LOGmodify_leads==1 and $LOGuser_level > 7) {

        if (strlen($phone_number)<6) $phone_number=$old_phone;

        echo "<table width=$section_width cellpadding='0' cellspacing='0' align='center'>\n";
        echo "  <tr>\n";
        echo "    <td align='center'>\n";
        echo "      <font color=$default_text face=\"ARIAL,HELVETICA\" size='2'><center><br><font color=$default_text size=+1>LEAD MODIFICATION</font><br>\n";
        if ($LOG['view_lead_search']) echo "      <a target=\"_parent\" href=\"./admin.php?ADD=999999&SUB=27\">[ Basic Search ]</a>\n";
        echo "      &nbsp;&nbsp;|&nbsp;&nbsp;\n";
        if ($LOG['view_lead_search_advanced']) echo "      <a target=\"_parent\" href=\"./admin.php?ADD=999999&SUB=26\">[ Advanced Search ]</a>\n";
        echo "<br><br>\n";
			
        if ($end_call > 0) {
	        ### update the lead record in the osdial_list table 
	        $stmt = sprintf("UPDATE osdial_list SET status='%s',source_id='%s',title='%s',first_name='%s',middle_initial='%s',last_name='%s',address1='%s',address2='%s',address3='%s',city='%s',state='%s',province='%s',postal_code='%s',country_code='%s',alt_phone='%s',phone_code='%s',phone_number='%s',email='%s',custom1='%s',custom2='%s',external_key='%s',comments='%s',date_of_birth='%s',post_date='%s',cost='%s' WHERE lead_id='%s';", mres($status), mres($source_id), mres($title), mres($first_name), mres($middle_initial), mres($last_name), mres($address1), mres($address2), mres($address3), mres($city), mres($state), mres($province), mres($postal_code), mres($country_code), mres($alt_phone), mres($phone_code), mres($phone_number), mres($email), mres($custom1), mres($custom2), mres($external_key), mres($comments), mres($date_of_birth), mres($post_date), mres($cost), mres($lead_id));
	        if ($DB) echo "|$stmt|\n";
	        $rslt=mysql_query($stmt, $link);
			
            echo "<a href=\"$PHP_SELF?ADD=1121&lead_id=$lead_id\">[ Return Back to Lead #$lead_id ]</a><br><br>\n";
	        echo "<b>Lead Information Modified.</b><br><br>\n";
				
	        ### inactivate osdial_callbacks record for this lead 
	        if ($dispo != $status and $dispo == 'CBHOLD') {
		        $stmt = sprintf("UPDATE osdial_callbacks set status='INACTIVE' where lead_id='%s' and status='ACTIVE';", mres($lead_id));
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
		        echo "<br>osdial_callback record inactivated: $lead_id<br>\n";
	        }

	        ### inactivate osdial_callbacks record for this lead 
	        if ($dispo != $status and $dispo == 'CALLBK') {
		        $stmt="UPDATE osdial_callbacks set status='INACTIVE' where lead_id='" . mres($lead_id) . "' and status IN('ACTIVE','LIVE');";
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
		        echo "<br>osdial_callback record inactivated: $lead_id<br>\n";
	        }
			
	        ### add lead to the internal DNC list 
	        if ($dispo != $status and $status == 'DNC') {
                if ($LOG['multicomp_user'] > 0 and preg_match('/COMPANY|BOTH/',$LOG['company']['dnc_method'])) {
                    $stmt = sprintf("INSERT INTO osdial_dnc_company (company_id,phone_number) values('%s','%s');", mres($LOG['company_id']),mres($phone_number));
                } else {
                    $stmt = sprintf("INSERT INTO osdial_dnc (phone_number) values('%s');", mres($phone_number));
                }
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
		        echo "<br>Lead added to DNC List: $lead_id - $phone_number<br>\n";
	        }

	        ### update last record in osdial_log table
	        if ($dispo != $status and $modify_logs > 0) {
		        $stmt="UPDATE osdial_log set status='" . mres($status) . "' where lead_id='" . mres($lead_id) . "' order by call_date desc limit 1";
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
	        }
			
	        ### update last record in osdial_closer_log table
	        if (($dispo != $status) and ($modify_closer_logs > 0)) {
		        $stmt="UPDATE osdial_closer_log set status='" . mres($status) . "' where lead_id='" . mres($lead_id) . "' order by call_date desc limit 1";
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
	        }
			
	        ### update last record in osdial_agent_log table
	        if (($dispo != $status) and ($modify_agent_logs > 0)) {
		        $stmt="UPDATE osdial_agent_log set status='" . mres($status) . "' where lead_id='" . mres($lead_id) . "' order by agent_log_id desc limit 1";
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
	        }
			
	        ### insert a NEW record to the osdial_closer_log table 
	        if ($add_closer_record > 0) {
		        $stmt="INSERT INTO osdial_closer_log (lead_id,list_id,campaign_id,call_date,start_epoch,end_epoch,length_in_sec,status,phone_code,phone_number,user,comments,processed) values('" . mres($lead_id) . "','" . mres($list_id) . "','" . mres($campaign_id) . "','" . mres($parked_time) . "','$SQLdate','$STARTtime','1','" . mres($status) . "','" . mres($phone_code) . "','" . mres($phone_number) . "','" . mres($PHP_AUTH_USER) . "','" . mres($comments) . "','Y')";
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
	        }
			
        } else {

	        ### inactivate osdial_callbacks record for this lead 
	        if ($CBchangeUSERtoANY == 'YES') {
		        $stmt="UPDATE osdial_callbacks set recipient='ANYONE' where callback_id='" . mres($callback_id) . "';";
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
		        echo "<br>osdial_callback record changed to ANYONE<br>\n";
	        }

	        ### inactivate osdial_callbacks record for this lead 
	        if ($CBchangeUSERtoUSER == 'YES') {
		        $stmt="UPDATE osdial_callbacks set user='" . mres($CBuser) . "' where callback_id='" . mres($callback_id) . "';";
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
		        echo "<br>osdial_callback record user changed to $CBuser<br>\n";
	        }	

	        ### inactivate osdial_callbacks record for this lead 
	        if ($CBchangeANYtoUSER == 'YES') {
		        $stmt="UPDATE osdial_callbacks set user='" . mres($CBuser) . "',recipient='USERONLY' where callback_id='" . mres($callback_id) . "';";
		        if ($DB) echo "|$stmt|\n";
		        $rslt=mysql_query($stmt, $link);
		        echo "<br>osdial_callback record changed to USERONLY, user: $CBuser<br>\n";
	        }	

            if ($VARclient == 'OSDR') {
                if ($confirm_sale > 0) {
                    if (strlen($confirm_id) > 0) {
                        $stmt = sprintf("UPDATE osdial_list SET status='%s' WHERE lead_id='%s';", mres($confirm_status), mres($confirm_id));
                        $rslt = mysql_query($stmt, $link);
                    }
                }
            }
			
            $ld = get_first_record($link, 'osdial_list', '*', sprintf("lead_id='%s'",mres($lead_id)));
			
	        if ($ld[lead_id] > 0) {
			
		        ##### grab osdial_log records #####
		        $stmt="select * from osdial_log where lead_id='" . mres($lead_id) . "' order by uniqueid desc limit 500;";
		        $rslt=mysql_query($stmt, $link);
		        $logs_to_print = mysql_num_rows($rslt);
			
		        $u=0;
		        $call_log = '';
		        $log_campaign = '';
		        while ($logs_to_print > $u) {
			        $row=mysql_fetch_row($rslt);
			        if (strlen($log_campaign)<1) $log_campaign = $row[3];
			        $u++;
			        $call_log .= "  <tr " . bgcolor($u) . " class=\"row font1\">\n";
			        $call_log .= "    <td>$u</td>\n";
			        $call_log .= "    <td>$row[4]</td>\n";
			        $call_log .= "    <td align=left>$row[7]</td>\n";
			        $call_log .= "    <td align=left>$row[8]</td>\n";
                    if ($LOG['view_agent_stats']) {
			            $call_log .= "    <td align=left><a href=\"admin.php?ADD=999999&SUB=21&agent=$row[11]\" target=\"_blank\">$row[11]</a></td>\n";
                    } else {
			            $call_log .= "    <td align=left>$row[11]</td>\n";
                    }
			        $call_log .= "    <td align=right>$row[3]</td>\n";
			        $call_log .= "    <td align=right>$row[2]</td>\n";
			        $call_log .= "    <td align=right>$row[1]</td>\n";
			        $call_log .= "    <td align=right>$row[15]</td>\n";
                    $call_log .= "  </tr>\n";
			
			        $campaign_id = $row[3];
		        }
			
		        ##### grab osdial_agent_log records #####
		        $stmt="select * from osdial_agent_log where lead_id='" . mres($lead_id) . "' order by agent_log_id desc limit 500;";
		        $rslt=mysql_query($stmt, $link);
		        $Alogs_to_print = mysql_num_rows($rslt);
			
		        $y=0;
		        $agent_log = '';
		        $Alog_campaign = '';
		        while ($Alogs_to_print > $y) {
			        $row=mysql_fetch_row($rslt);
			        if (strlen($Alog_campaign)<1) $Alog_campaign = $row[5];
			        $y++;
			        $agent_log .= "  <tr " . bgcolor($y) . " class=\"row font1\">\n";
			        $agent_log .= "    <td>$y</td>\n";
			        $agent_log .= "    <td>$row[3]</td>\n";
			        $agent_log .= "    <td align=left>$row[5]</td>\n";
                    if ($LOG['view_agent_stats']) {
			            $agent_log .= "    <td align=left><a href=\"admin.php?ADD=999999&SUB=21&agent=$row[11]\" target=\"_blank\">$row[1]</a></td>\n";
                    } else {
			            $agent_log .= "    <td align=left>$row[1]</td>\n";
                    }
			        $agent_log .= "    <td align=right>$row[7]</td>\n";
			        $agent_log .= "    <td align=right>$row[9]</td>\n";
			        $agent_log .= "    <td align=right>$row[11]</td>\n";
			        $agent_log .= "    <td align=right>$row[13]</td>\n";
			        $agent_log .= "    <td align=right>$row[14]</td>\n";
			        $agent_log .= "    <td align=right>$row[15]</td>\n";
			        $agent_log .= "    <td align=right>$row[17]</td>\n";
                    $agent_log .= "  </tr>\n";
		
			        $campaign_id = $row[5];
		        }
			
	            ##### grab osdial_closer_log records #####
		        $stmt="select * from osdial_closer_log where lead_id='" . mres($lead_id) . "' order by closecallid desc limit 500;";
		        $rslt=mysql_query($stmt, $link);
		        $Clogs_to_print = mysql_num_rows($rslt);
			
		        $y=0;
		        $closer_log = '';
		        $Clog_campaign = '';
		        while ($Clogs_to_print > $y) {
			        $row=mysql_fetch_row($rslt);
			        if (strlen($Clog_campaign)<1) $Clog_campaign = $row[3];
			        $y++;
			        $closer_log .= "  <tr " . bgcolor($y) . " class=\"row font1\">\n";
			        $closer_log .= "    <td>$y</td>\n";
			        $closer_log .= "    <td>$row[4]</td>\n";
			        $closer_log .= "    <td align=left>$row[7]</td>\n";
			        $closer_log .= "    <td align=left>$row[8]</td>\n";
                    if ($LOG['view_agent_stats']) {
			            $closer_log .= "    <td align=left><a href=\"admin.php?&ADD=999999&SUB=21&agent=$row[11]\" target=\"_blank\">$row[11]</a></td>\n";
                    } else {
			            $closer_log .= "    <td align=left>$row[11]</td>\n";
                    }
			        $closer_log .= "    <td align=right>$row[3]</td>\n";
			        $closer_log .= "    <td align=right>$row[2]</td>\n";
			        $closer_log .= "    <td align=right>$row[1]</td>\n";
			        $closer_log .= "    <td align=right>$row[14]</td>\n";
			        $closer_log .= "  </tr>\n";
			
			        $campaign_id = $row[3];
		        }

                if ($save_aff > 0) {
                    if (strlen($alf_id) > 0) {
                        $stmt = sprintf("UPDATE osdial_list_fields SET value='%s' WHERE id='%s';",mres($alf_val), mres($alf_id));
                    } else {
                        $stmt = sprintf("INSERT INTO osdial_list_fields SET lead_id='%s',field_id='%s',value='%s';", mres($ld['lead_id']), mres($alf_fld_id), mres($alf_val));
                    }
                    $rslt = mysql_query($stmt, $link);
                }
			
		        echo "  <br>\n";
                echo "  <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
		        echo "    <input type=hidden name=end_call value=1>\n";
		        echo "    <input type=hidden name=DB value=\"$DB\">\n";
		        echo "    <input type=hidden name=ADD value=\"$ADD\">\n";
		        echo "    <input type=hidden name=SUB value=\"$SUB\">\n";
		        echo "    <input type=hidden name=lead_id value=\"$ld[lead_id]\">\n";
		        echo "    <input type=hidden name=dispo value=\"$ld[status]\">\n";
		        echo "    <input type=hidden name=list_id value=\"$ld[list_id]\">\n";
		        echo "    <input type=hidden name=campaign_id value=\"$campaign_id\">\n";
		        echo "    <input type=hidden name=old_phone value=\"$ld[phone_number]\">\n";
		        echo "    <input type=hidden name=server_ip value=\"$server_ip\">\n";
		        echo "    <input type=hidden name=extension value=\"$extension\">\n";
		        echo "    <input type=hidden name=channel value=\"$channel\">\n";
		        echo "    <input type=hidden name=call_began value=\"$call_began\">\n";
		        echo "    <input type=hidden name=parked_time value=\"$parked_time\">\n";

                echo "    <font color=$default_text size=2>Call information: $ld[first_name] $ld[last_name] - $ld[phone_number]<br></font>\n";
                echo "    <font color=$default_text size=2>Last Call Time: $ld[last_local_call_time] - GMT: $ld[gmt_offset_now]<br></font>\n";
                echo "    <font size=1>\n";
		        echo "      <table cellspacing=0 cellpadding=1 width=600>\n";
		        echo "        <tr class=tabheader>\n";
                echo "          <td width='50%'><font size=+1>Lead ID: $ld[lead_id]</font></td>\n";
                echo "          <td width='50%'><font size=+1>List ID: $ld[list_id]</font></td>\n";
                echo "        </tr>\n";
		        echo "        <tr class=tabheader>\n";
                if ($LOG['view_agent_stats']) {
                    echo "          <td width='50%'>Fronter:&nbsp;<a href=\"admin.php?&ADD=999999&SUB=21&agent=$ld[user]\">$ld[user]</a></td>\n";
                } else {
                    echo "          <td width='50%'>Fronter:&nbsp;$ld[user]</td>\n";
                }
                echo "          <td width='50%'>Vendor ID: $ld[vendor_lead_code]</td>\n";
                echo "        </tr>\n";
		        echo "      </table>\n";
					
		        echo "      <table cellspacing=3 width=600>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=center colspan=2>Title:&nbsp;<input type=text name=title size=4 maxlength=4 value=\"$ld[title]\"> &nbsp; First:&nbsp;<input type=text name=first_name size=15 maxlength=30 value=\"$ld[first_name]\"> &nbsp; M.I.&nbsp;<input type=text name=middle_initial size=2 maxlength=1 value=\"$ld[middle_initial]\"> &nbsp; Last:&nbsp;<input type=text name=last_name size=15 maxlength=30 value=\"$ld[last_name]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr class=tabheader>\n";
                echo "          <td colspan=2></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
	            echo "          <td width=30% align=center>Gender:&nbsp;<select name=gender><option>M</option><option>F</option><option selected>$ld[gender]</gender></select></td>\n";
	            echo "          <td align=center>Birth&nbsp;Date:&nbsp;<input type=text name=date_of_birth size=10 maxlength=10 value=\"$ld[date_of_birth]\"><font size=1>&nbsp;(YYYY-MM-DD)</font></td>\n";
                echo "        </tr>\n";
		        echo "        <tr class=tabheader>\n";
                echo "          <td colspan=2></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
	            echo "          <td align=right>Address 1:&nbsp;</td>\n";
		        echo "          <td align=left><input type=text name=address1 size=30 maxlength=30 value=\"$ld[address1]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Address 2:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=address2 size=30 maxlength=30 value=\"$ld[address2]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Address 3:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=address3 size=30 maxlength=30 value=\"$ld[address3]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>City:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=city size=30 maxlength=30 value=\"$ld[city]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>State:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=state size=2 maxlength=2 value=\"$ld[state]\"> &nbsp; Postal Code:&nbsp;<input type=text name=postal_code size=10 maxlength=10 value=\"$ld[postal_code]\"> </td>\n";
                echo "        </tr>\n";
			
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Province:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=province size=30 maxlength=30 value=\"$ld[province]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Country:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=country_code size=3 maxlength=3 value=\"$ld[country_code]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr class=tabheader>\n";
                echo "          <td colspan=2></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Phone Code:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=phone_code size=10 maxlength=10 value=\"$ld[phone_code]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Main Phone:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=phone_number size=20 maxlength=20 value=\"$ld[phone_number]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Alt Phone:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=alt_phone size=20 maxlength=20 value=\"$ld[alt_phone]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr class=tabheader>\n";
                echo "          <td colspan=2></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Email:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=email size=30 maxlength=50 value=\"$ld[email]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Source ID:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=source_id size=6 maxlength=6 value=\"$ld[source_id]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>External Key:&nbsp;</td>\n";
                echo "          <td align=left><input type=text name=external_key size=30 maxlength=100 value=\"$ld[external_key]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
	            echo "          <td align=right>Post Date:&nbsp;</td>\n";
		        echo "          <td align=left><input type=text name=post_date size=19 maxlength=19 value=\"$ld[post_date]\"><font size=1> (YYYY-MM-DD HH:MM:SS)</font></td>\n";
                echo "        </tr>\n";
                if ($ld[cost] == 0) $ld[cost] = '0.000';
		        echo "        <tr bgcolor=$oddrows>\n";
	            echo "          <td align=right>Lead Cost:&nbsp;</td>\n";
		        echo "          <td align=left><input type=text name=cost size=6 maxlength=6 value=\"$ld[cost]\"></td>\n";
                echo "        </tr>\n";
		        echo "        <tr class=tabheader>\n";
                echo "          <td colspan=2></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Comments:&nbsp;</td>\n";
                echo "          <td align=left><textarea name=comments cols=50 rows=3>$ld[comments]</textarea></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Custom1:&nbsp;</td>\n";
                echo "          <td align=left><textarea name=custom1 cols=50 rows=3>$ld[custom1]</textarea></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Custom2:&nbsp;</td>\n";
                echo "          <td align=left><textarea name=custom2 cols=50 rows=5>$ld[custom2]</textarea></td>\n";
                echo "        </tr>\n";
		        echo "        <tr class=tabheader>\n";
                echo "          <td colspan=2></td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td align=right>Disposition:&nbsp;</td>\n";
                echo "          <td align=left>\n";
                echo "            <select size=1 name=status>\n";
			
		        $stmt="SELECT * FROM osdial_statuses WHERE selectable='Y' ORDER BY status;";
		        $rslt=mysql_query($stmt, $link);
		        $statuses_to_print = mysql_num_rows($rslt);
		        $statuses_list='';
			
		        $o=0;
		        $DS=0;
		        while ($statuses_to_print > $o) {
			        $rowx=mysql_fetch_row($rslt);
			        if (strlen($ld[status]) == strlen($rowx[0]) and eregi($ld[status],$rowx[0])) {
                        $statuses_list .= "          <option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                        $DS++;
                    } else {
                        $statuses_list .= "          <option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                    }
			        $o++;
		        }
			
                if (strlen($log_campaign)>0) {
		            $stmt = sprintf("SELECT * FROM osdial_campaign_statuses WHERE selectable='Y' AND campaign_id='%s' ORDER BY status;", mres($log_campaign));
		            $rslt=mysql_query($stmt, $link);
		            $CAMPstatuses_to_print = mysql_num_rows($rslt);
			
		            $o=0;
		            while ($CAMPstatuses_to_print > $o) {
			            $rowx=mysql_fetch_row($rslt);
			            if (strlen($ld[status]) ==  strlen($rowx[0]) and eregi($ld[status],$rowx[0]) ) {
                            $statuses_list .= "          <option SELECTED value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                            $DS++;
                        } else {
                            $statuses_list .= "          <option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                        }
			            $o++;
		            }
                }
			
			
		        if ($DS < 1) $statuses_list .= "          <option SELECTED value=\"$ld[status]\">$ld[status]</option>\n";
	            echo "$statuses_list";
	            echo "            </select>\n";
                if (strlen($log_campaign)>0) echo "             <font size=1><i>(with $log_campaign statuses)</i></font>\n";
                echo "          </td>\n";
                echo "        </tr>\n";
			
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td></td>\n";
                echo "          <td align=left><input type=checkbox name=modify_logs value=\"1\" CHECKED>&nbsp;-&nbsp;Modify&nbsp;OSDial&nbsp;Log</td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td></td>\n";
                echo "          <td align=left><input type=checkbox name=modify_agent_logs value=\"1\" CHECKED>&nbsp;-&nbsp;Modify&nbsp;Agent&nbsp;Log</td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td></td>\n";
                echo "          <td align=left><input type=checkbox name=modify_closer_logs value=\"1\">&nbsp;-&nbsp;Modify&nbsp;Closer&nbsp;Log</td>\n";
                echo "        </tr>\n";
		        echo "        <tr bgcolor=$oddrows>\n";
                echo "          <td></td>\n";
                echo "          <td align=left><input type=checkbox name=add_closer_record value=\"1\">&nbsp;-&nbsp;Add&nbsp;Closer&nbsp;Log&nbsp;Record</td>\n";
                echo "        </tr>\n";
			
		        echo "        <tr class=tabfooter>\n";
                echo "          <td colspan=2 align=center class=tabbutton><input type=submit name=submit value=\"SUBMIT\"></td>\n";
                echo "        </tr>\n";
		        echo "      </table>\n";
                echo "    </font>\n";
                echo "  </form>\n";
                echo "  <font size=1>\n";
		        echo "  <br>\n";


                $wfv['lead_id'] = $ld['lead_id'];
                $wfv['vendor_id'] = $ld['vendor_lead_code'];
                $wfv['list_id'] = $ld['list_id'];
                $wfv['gmt_offset_now'] = $ld['gmt_offset_now'];
                $wfv['phone_code'] = $ld['phone_code'];
                $wfv['phone_number'] = $ld['phone_number'];
                $wfv['title'] = $ld['title'];
                $wfv['first_name'] = $ld['first_name'];
                $wfv['middle_initial'] = $ld['middle_initial'];
                $wfv['last_name'] = $ld['last_name'];
                $wfv['address1'] = $ld['address1'];
                $wfv['address2'] = $ld['address2'];
                $wfv['address3'] = $ld['address3'];
                $wfv['city'] = $ld['city'];
                $wfv['state'] = $ld['state'];
                $wfv['province'] = $ld['province'];
                $wfv['post_code'] = $ld['postal_code'];
                $wfv['country_code'] = $ld['country_code'];
                $wfv['gender'] = $ld['gender'];
                $wfv['date_of_birth'] = $ld['date_of_birth'];
                $wfv['alt_phone'] = $ld['alt_phone'];
                $wfv['email'] = $ld['email'];
                $wfv['custom1'] = $ld['custom1'];
                $wfv['custom2'] = $ld['custom2'];
                $wfv['comments'] = $ld['comments'];
                $wfv['user'] = $user;
                $wfv['campaign'] = $campaign_id;
                $wfv['fronter'] = $ld['user'];
                $wfv['group'] = $campaign_id;
                $wfv['phone'] = $ld['phone_number'];
                $wfv['dispo'] = $ld['status'];
                $wfv['dialed_number'] = $ld['phone_number'];
                $wfv['source_id'] = $ld['source_id'];
                $wfv['external_key'] = $ld['external_key'];
                $wfv['post_date'] = $ld['post_date'];

                $list = get_first_record($link, 'osdial_lists', '*', sprintf("list_id='%s'",$ld['list_id']));
                $camp = $list['campaign_id'];
                $affrms = get_krh($link, 'osdial_campaign_forms', '*', 'priority ASC', sprintf("campaigns='ALL' OR campaigns='%s' OR campaigns LIKE '%s,%%' OR campaigns LIKE '%%,%s'",mres($camp),mres($camp),mres($camp)), '');
                if (count($affrms) > 0) {
	                echo "    <table width=600 cellspacing=0 cellpadding=1>\n";
	                echo "      <tr class=tabheader>\n";
	                echo "        <td colspan=4><font size='+1'>ADDITIONAL FORM FIELDS</font></td>\n";
	                echo "      </tr>\n";
	                echo "      <tr class=tabheader>\n";
                    echo "        <td>Form</td>\n";
                    echo "        <td>Field</td>\n";
                    echo "        <td>Value</td>\n";
                    echo "        <td>Action</td>\n";
	                echo "      </tr>\n";

                    $u=0;
                    $lastfrm='';
	                foreach ($affrms as $affrm) {
                        if ($lastfrm!='' and $lastfrm != $affrm['id']) {
		                    echo "        <tr class=tabheader>\n";
                            echo "          <td colspan=4></td>\n";
                            echo "        </tr>\n";
                        }
                        $lastfrm = $affrm['id'];
        
                        $afflds = get_krh($link, 'osdial_campaign_fields', '*', 'priority ASC', sprintf("form_id='%s'",mres($affrm['id'])), '');
                        if (count($afflds) > 0) {
	                        foreach ($afflds as $affld) {
                                $afldel='';
                                $skip_fld=0;
                                $bgcolor = bgcolor($u);
                                $alf = get_first_record($link, 'osdial_list_fields', '*', sprintf("lead_id='%s' AND field_id='%s'",$ld['lead_id'],$affld['id']));
                                if ($affrm['deleted'] > 0 or $affld['deleted'] > 0) {
                                    if (strlen($alf['value']) > 0) {
                                        $afldel = "color=red";
                                        $bgcolor = 'bgcolor=#FFA07A title="This field has been deleted from the system, however, this lead still has data for it."';
                                    } else {
                                        $skip_fld=1;
                                    }
                                }
                                if ($skip_fld < 1) {
                                    echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
		                            echo '<input type="hidden" name="DB" value="' . $DB . '">';
		                            echo '<input type="hidden" name="ADD" value="' . $ADD . '">';
		                            echo '<input type="hidden" name="SUB" value="' . $SUB . '">';
		                            echo '<input type="hidden" name="save_aff" value=1>';
		                            echo '<input type="hidden" name="lead_id" value="' . $ld[lead_id] . '">';
		                            echo '<input type="hidden" name="alf_id" value="' . $alf[id] . '">';
		                            echo '<input type="hidden" name="alf_fld_id" value="' . $affld[id] . '">';
		                            echo "    <tr $bgcolor class=\"row font1\">\n";
                                    echo "      <td align=center><font $afldel><b>$affrm[name]</b></font></td>\n";
                                    echo "      <td align=center><font $afldel><b>$affld[name]</b></font></td>\n";
                                    echo "      <td align=center class=tabinput><input type=\"text\" name=\"alf_val\" size=\"30\" maxlength=\"255\" value=\"$alf[value]\"></td>\n";
                                    echo "      <td align=center class=tabbutton1><input type=\"submit\" value=\"Save\"></td>\n";
                                    echo "    </tr>\n";
                                    echo "    </form>\n";
                                    $wfv[$affrm['name'] . '_' . $affld['name']] = $alf['value'];
                                    $u++;
                                }
                            }
                        }
                    }
                    echo "      <tr class=tabfooter>\n";
                    echo "        <td colspan=4></td>\n";
                    echo "      </tr>\n";
	                echo "    </table>\n";
                }
	            echo "  <br><br>\n";
                echo "<hr>\n";
	            echo "  <br><br>\n";


			
		        if ($ld[status] == 'CALLBK' or $ld[status] == 'CBHOLD') {
			        ### find any osdial_callback records for this lead 
			        $stmt="select * from osdial_callbacks where lead_id='" . mres($ld[lead_id]) . "' and status IN('ACTIVE','LIVE') order by callback_id desc LIMIT 1;";
			        if ($DB) echo "|$stmt|\n";
			        $rslt=mysql_query($stmt, $link);
			        $CB_to_print = mysql_num_rows($rslt);
			        $rowx=mysql_fetch_row($rslt);
			
			        if ($CB_to_print>0) {
				        if ($rowx[9] == 'USERONLY') {
					        echo "    <br>\n";
                            echo "    <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
					        echo "      <input type=hidden name=CBchangeUSERtoANY value=\"YES\">\n";
					        echo "      <input type=hidden name=DB value=\"$DB\">\n";
		                    echo '      <input type="hidden" name="ADD" value="' . $ADD . '">' . "\n";
		                    echo '      <input type="hidden" name="SUB" value="' . $SUB . '">' . "\n";
					        echo "      <input type=hidden name=lead_id value=\"$ld[lead_id]\">\n";
					        echo "      <input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					        echo "      <input type=submit name=submit value=\"CHANGE TO ANYONE CALLBACK\">\n";
                            echo "    </form><br>\n";
			
					        echo "    <br>\n";
                            echo "    <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
					        echo "      <input type=hidden name=CBchangeUSERtoUSER value=\"YES\">\n";
					        echo "      <input type=hidden name=DB value=\"$DB\">\n";
		                    echo '      <input type="hidden" name="ADD" value="' . $ADD . '">' . "\n";
		                    echo '      <input type="hidden" name="SUB" value="' . $SUB . '">' . "\n";
					        echo "      <input type=hidden name=lead_id value=\"$ld[lead_id]\">\n";
					        echo "      <input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					        echo "      New Callback Owner UserID: <input type=text name=CBuser size=8 maxlength=10 value=\"$rowx[8]\"> \n";
					        echo "      <input type=submit name=submit value=\"CHANGE USERONLY CALLBACK USER\">\n";
                            echo "    </form><br>\n";
				        } else {
					        echo "    <br>\n";
					        echo "    <form action=$PHP_SELF method=POST enctype=\"multipart/form-data\">\n";
					        echo "      <input type=hidden name=CBchangeANYtoUSER value=\"YES\">\n";
					        echo "      <input type=hidden name=DB value=\"$DB\">\n";
		                    echo '      <input type="hidden" name="ADD" value="' . $ADD . '">' . "\n";
		                    echo '      <input type="hidden" name="SUB" value="' . $SUB . '">' . "\n";
					        echo "      <input type=hidden name=lead_id value=\"$ld[lead_id]\">\n";
					        echo "      <input type=hidden name=callback_id value=\"$rowx[0]\">\n";
					        echo "      New Callback Owner UserID: <input type=text name=CBuser size=8 maxlength=10 value=\"$rowx[8]\"> \n";
					        echo "      <input type=submit name=submit value=\"CHANGE TO USERONLY CALLBACK\">\n";
                            echo "    </form><br>\n";
				        }
			        } else {
				        echo "    <br>No Callback records found<br>\n";
			        }
		        }

	            echo "    <table width=750 cellspacing=0 cellpadding=1>\n";
	            echo "      <tr class=tabheader>\n";
	            echo "        <td colspan=9><font size='+1'>CALLS TO THIS LEAD</font></td>\n";
	            echo "      </tr>\n";
	            echo "      <tr class=tabheader>\n";
                echo "        <td># </td>\n";
                echo "        <td>DATE/TIME</td>\n";
                echo "        <td>LENGTH</td>\n";
                echo "        <td>STATUS</td>\n";
	            echo "        <td>TSR</td>\n";
	            echo "        <td>CAMPAIGN</td>\n";
	            echo "        <td>LIST</td>\n";
	            echo "        <td>LEAD</td>\n";
                echo "        <td>TERM REASON</td>\n";
	            echo "      </tr>\n";
	            echo "$call_log\n";
                echo "      <tr class=tabfooter>\n";
                echo "        <td colspan=9></td>\n";
                echo "      </tr>\n";
	            echo "    </table>\n";
	            echo "    <br><br>\n";
	            echo "    <br><br>\n";
			
	            echo "    <table width=750 cellspacing=0 cellpadding=1>\n";
	            echo "      <tr class=tabheader>\n";
	            echo "        <td colspan=9><font size='+1'>CLOSER RECORDS FOR THIS LEAD</font></td>\n";
	            echo "      </tr>\n";
	            echo "      <tr class=tabheader>\n";
                echo "        <td># </td>\n";
                echo "        <td>DATE/TIME</td>\n";
                echo "        <td>LENGTH</td>\n";
                echo "        <td>STATUS</td>\n";
                echo "        <td>TSR</td>\n";
                echo "        <td>CAMPAIGN</td>\n";
                echo "        <td>LIST</td>\n";
                echo "        <td>LEAD</td>\n";
                echo "        <td>WAIT</td>\n";
                echo "      </tr>\n";
	            echo "$closer_log\n";
                echo "      <tr class=tabfooter>\n";
                echo "        <td colspan=9></td>\n";
                echo "      </tr>\n";
	            echo "    </table>\n";
	            echo "    <br><br>\n";
	            echo "    <br><br>\n";
			
			
	            echo "    <table width=750 cellspacing=0 cellpadding=1>\n";
	            echo "      <tr class=tabheader>\n";
	            echo "        <td colspan=11><font size='+1'>AGENT LOG RECORDS FOR THIS LEAD</font></td>\n";
	            echo "      </tr>\n";
	            echo "      <tr class=tabheader>\n";
                echo "        <td># </td>\n";
                echo "        <td>DATE/TIME</td>\n";
                echo "        <td>CAMPAIGN</td>\n";
                echo "        <td>TSR</td>\n";
                echo "        <td>PAUSE</td>\n";
                echo "        <td>WAIT</td>\n";
                echo "        <td>TALK</td>\n";
                echo "        <td>DISPO</td>\n";
                echo "        <td>STATUS</td>\n";
                echo "        <td>GROUP</td>\n";
                echo "        <td>SUB</td>\n";
                echo "      </tr>\n";
	            echo "$agent_log\n";
                echo "      <tr class=tabfooter>\n";
                echo "        <td colspan=11></td>\n";
                echo "      </tr>\n";
	            echo "    </table>\n";
	            echo "    <br><br>\n";
	            echo "    <br><br>\n";
			
	            echo "    <table width=750 cellspacing=0 cellpadding=1>\n";
	            echo "      <tr class=tabheader>\n";
	            echo "        <td colspan=8><font size='+1'>RECORDINGS FOR THIS LEAD</font></td>\n";
	            echo "      </tr>\n";
	            echo "      <tr class=tabheader>\n";
                echo "        <td># </td>\n";
                echo "        <td>LEAD</td>\n";
                echo "        <td>DATE/TIME</td>\n";
                echo "        <td>SECONDS</td>\n";
                echo "        <td>RECID</td>\n";
                echo "        <td>FILENAME</td>\n";
                echo "        <td>LOCATION</td>\n";
                echo "        <td>TSR</td>\n";
                echo "      </tr>\n";
			
                $rlogs = get_krh($link, 'recording_log', '*', 'recording_id DESC', sprintf("lead_id='%s'",mres($ld[lead_id])), '500');
	            $u=0;
	            foreach ($rlogs as $rl) {
		            $location = $rl['location'];
		            $locat = ellipse($location,27,true);
		            if (eregi("http",$location) or eregi("^//", $location)) {
			            $location = eregi_replace("^//","/",$location);
			            $location = "<a href=\"$location\">$locat</a>";
		            } else {
			            $location = $locat;
		            }
                    if ($u==0) $wfv['recording_id'] = $rl['recording_id'];
		            $u++;
		            echo "      <tr " . bgcolor($u) . " class=\"row font1\">\n";
		            echo "        <td>$u</td>\n";
		            echo "        <td align=left>" . $rl['lead_id'] . "</td>\n";
		            echo "        <td align=left>" . $rl['starttime'] . "</td>\n";
		            echo "        <td align=left>" . $rl['length_in_sec'] . "</td>\n";
		            echo "        <td align=left>" . $rl['recording_id'] . "</td>\n";
		            echo "        <td align=center>" . $rl['filename'] . "</td>\n";
		            echo "        <td align=left>$location</td>\n";
                    if ($LOG['view_agent_stats']) {
		                echo "        <td align=left><a href=\"admin.php?ADD=999999&SUB=21&agent=" . $rl['user'] . "\" target=\"_blank\">" . $rl['user'] . "</a></td>\n";
                    } else {
		                echo "        <td align=left>" . $rl['user'] . "</td>\n";
                    }
		            echo "      </tr>\n";
	            }
		
                echo "      <tr class=tabfooter>\n";
                echo "        <td colspan=8></td>\n";
                echo "      </tr>\n";
	            echo "    </table>\n";
                if ($VARclient == 'OSDR') {
                    if ($ld['status'] == "SALE") {
                        echo '<form action="' . $PHP_SELF . '" method="POST" enctype="multipart/form-data">';
		                echo '<input type="hidden" name="DB" value="' . $DB . '">';
		                echo '<input type="hidden" name="ADD" value="' . $ADD . '">';
		                echo '<input type="hidden" name="SUB" value="' . $SUB . '">';
		                echo '<input type="hidden" name="lead_id" value="' . $ld['lead_id'] . '">';
		                echo '<input type="hidden" name="confirm_sale" value=1>';
		                echo '<input type="hidden" name="confirm_id" value="' . $ld['lead_id'] . '">';
		                echo '<input type="hidden" name="confirm_status" value="VERLIS">';
		                echo "    <center>\n";
                        echo "      <input type=\"submit\" value=\"CONFIRM SALE\">\n";
                        echo "    </center>\n";
                        echo "</form>\n";
                    }
                }
                if (strlen($campaign_id)>0) {
                    $camp = get_first_record($link, 'osdial_campaigns', '*', sprintf("campaign_id='%s'",mres($campaign_id)));
                    $wvars = '';
                    foreach ($wfv as $k => $v) {
                        $wvars .= '&' . $k . '=' . $v;
                    }
                    function pwfa($wfv, $k) { return $wfv[$k]; };
                    $wfa1 = $camp['web_form_address'] . '?1=1' . $wvars;
                    if (preg_match('/\?/',$camp['web_form_address'])) {
                        $wfa1 = preg_replace('/\[\[(.*)\]\]/ime', "pwfa(\$wfv,'\\1')", $camp['web_form_address']);
                    }
                    $wfa2 = $camp['web_form_address2'] . '?1=1' . $wvars;
                    if (preg_match('/\?/',$camp['web_form_address2'])) {
                        $wfa2 = preg_replace('/\[\[(.*)\]\]/ime', "pwfa(\$wfv,'\\1')", $camp['web_form_address2']);
                    }
                    echo "    <br><br><br><center><font size=3><a target=\"_new\" href=\"" . $wfa1 . "\">WEBFORM1</a>&nbsp;&nbsp;&nbsp;<a target=\"_new\" href=\"" . $wfa2 . "\">WEBFORM2</a></font></center>";
                }
                echo "    <br><br><br>\n";
            } else {
		        echo "lead lookup FAILED for lead_id $ld[lead_id] &nbsp; &nbsp; &nbsp; $SQLdate\n<br><br>\n";
	        }
	        echo "    </font>\n";
        }

        $ENDtime = date("U");
        $RUNtime = ($ENDtime - $STARTtime);
        echo "      <br>\n<br>\n<br>\n";
        echo "      <font size=0>\n<br>\n<br>\n<br>\nscript runtime: $RUNtime seconds</font>";
        echo "    </font>\n";
        echo "   </td>\n";
        echo " </tr>\n";
        echo "</table>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}
?>
