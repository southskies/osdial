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



######################
# ADD=11111111111 display the ADD NEW PHONE SCREEN
######################

if ($ADD==11111111111) {
    if ($LOGast_admin_access==1) {
        $servers_list = get_servers($link, '');
        echo "<TABLE align=center><TR><TD>\n";
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

        echo "<center><br><font color=$default_text size=+1>ADD A NEW PHONE</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=21111111111>\n";
        echo "<TABLE width=$section_width cellspacing=3>\n";

        echo "<center><TABLE width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Phone extension: </td><td align=left><input type=text name=extension size=20 maxlength=100 value=\"\">$NWB#phones-extension$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Dial Plan Number: </td><td align=left><input type=text name=dialplan_number size=15 maxlength=20 value=\"$row[1]\"> (digits only)$NWB#phones-dialplan_number$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Voicemail Box: </td><td align=left><input type=text name=voicemail_id size=10 maxlength=10 value=\"$row[2]\"> (digits only)$NWB#phones-voicemail_id$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Outbound CallerID Name: </td><td align=left><input type=text name=outbound_cid_name size=20 maxlength=40 value=\"$row[67]\">$NWB#phones-outbound_cid_name$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Outbound CallerID: </td><td align=left><input type=text name=outbound_cid size=10 maxlength=20 value=\"$row[65]\"> (digits only)$NWB#phones-outbound_cid$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Phone IP address: </td><td align=left><input type=text name=phone_ip size=20 maxlength=15 value=\"$row[3]\"> (optional)$NWB#phones-phone_ip$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Computer IP address: </td><td align=left><input type=text name=computer_ip size=20 maxlength=15 value=\"$row[4]\"> (optional)$NWB#phones-computer_ip$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server IP: </td><td align=left><select size=1 name=server_ip>\n";

        echo "$servers_list";
        echo "<option SELECTED>$row[5]</option>\n";
        echo "</select>$NWB#phones-server_ip$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Login: </td><td align=left><input type=text name=login size=10 maxlength=10 value=\"$row[6]\">$NWB#phones-login$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=pass size=10 maxlength=10 value=\"$row[7]\">$NWB#phones-pass$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Status: </td><td align=left><select size=1 name=status><option>ACTIVE</option><option>SUSPENDED</option><option>CLOSED</option><option>PENDING</option><option>ADMIN</option><option selected>$row[8]</option></select>$NWB#phones-status$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Active Account: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option selected>$row[9]</option></select>$NWB#phones-active$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Phone Type: </td><td align=left><input type=text name=phone_type size=20 maxlength=50 value=\"$row[10]\">$NWB#phones-phone_type$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=fullname size=20 maxlength=50 value=\"$row[11]\">$NWB#phones-fullname$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Company: </td><td align=left>";
        if ($LOG['multicomp_admin'] > 0) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "<select name=company>\n";
            foreach ($comps as $comp) {
                echo "<option value=" . (($comp['id'] * 1) + 100) . ">" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "</select>\n";
            echo "$NWB#phones-company$NWE";
        } elseif ($LOG['multicomp']>0) {
            echo "<input type=hidden name=company value=$LOG[company_prefix]>";
            echo "<font color=$default_text>" . $LOG[company_prefix] . "</font>";
        } else {
            echo "<input type=text name=company size=10 maxlength=10 value=\"$row[12]\">";
            echo "$NWB#phones-company$NWE";
        }
        echo "</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Picture: </td><td align=left><input type=text name=picture size=20 maxlength=19 value=\"$row[13]\">$NWB#phones-picture$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Client Protocol: </td><td align=left><select size=1 name=protocol><option>SIP</option><option>DAHDI</option><option>Zap</option><option>IAX2</option><option>EXTERNAL</option><option selected>$row[16]</option></select>$NWB#phones-protocol$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$row[17]</option></select> (Do NOT Adjust for DST)$NWB#phones-local_gmt$NWE</td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=21111111111 adds new phone to the system
######################

if ($ADD==21111111111) {
    if ($LOGast_admin_access==1) {
        echo "<TABLE><TR><TD>\n";
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
        $preextension = $extension;
        if ($LOG['multicomp'] > 0) $preextension = (($company * 1) + 0) . $extension;
        $stmt="SELECT count(*) from phones where extension='$preextension' and server_ip='$server_ip';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>PHONE NOT ADDED - there is already a Phone in the system with this extension/server</font>\n";
        } else {
            if ( (strlen($extension) < 1) or (strlen($server_ip) < 7) or (strlen($dialplan_number) < 1) or (strlen($voicemail_id) < 1) or (strlen($login) < 1)  or (strlen($pass) < 1)) {
                echo "<br><font color=red>PHONE NOT ADDED - Please go back and look at the data you entered</font>\n";
            } else {
                echo "<br><font color=$default_text>PHONE ADDED</font>\n";
    
                if ($LOG['multicomp'] > 0) {
                    if (!preg_match('/\/|@/',$extension)) $extension = (($company * 1) + 0) . $extension;
                    if ((preg_match('/SIP|IAX/',$protocol) and substr($dialplan_number,0,3) != $company)) $dialplan_number = (($company * 1) + 0) . $dialplan_number;
                    $voicemail_id = (($company * 1) + 0) . $voicemail_id;
                    $login = (($company * 1) + 0) . $login;
                }
                $stmt="INSERT INTO phones (extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,protocol,local_gmt,outbound_cid,outbound_cid_name) values('$extension','$dialplan_number','$voicemail_id','$phone_ip','$computer_ip','$server_ip','$login','$pass','$status','$active','$phone_type','$fullname','$company','$picture','$protocol','$local_gmt','$outbound_cid','$outbound_cid_name');";
                $rslt=mysql_query($stmt, $link);
            }
        }
        $ADD=31111111111;
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=41111111111 modify phone record in the system
######################

if ($ADD==41111111111) {
    if ($LOGast_admin_access==1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

        $preextension = $extension;
        if ($LOG['multicomp'] > 0 and !preg_match('/\/|@/',$extension)) $preextension = (($company * 1) + 0) . $extension;
        $stmt="SELECT count(*) from phones where extension='$preextension' and server_ip='$server_ip';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ( ($row[0] > 0) && ( ($preextension != $old_extension) or ($server_ip != $old_server_ip) ) ) {
            echo "<br><font color=red>PHONE NOT MODIFIED - there is already a Phone in the system with this extension/server</font>\n";
        } else {
            if ( (strlen($extension) < 1) or (strlen($server_ip) < 7) or (strlen($dialplan_number) < 1) or (strlen($voicemail_id) < 1) or (strlen($login) < 1)  or (strlen($pass) < 1)) {
                echo "<br><font color=$default_text>PHONE NOT MODIFIED - Please go back and look at the data you entered</font>\n";
            } else {
                echo "<br><font color=$default_text>PHONE MODIFIED: $extension</font>\n";

                if ($LOG['multicomp'] > 0) {
                    if (!preg_match('/\/|@/',$extension)) $extension = (($company * 1) + 0) . $extension;
                    if ((preg_match('/SIP|IAX/',$protocol) and substr($dialplan_number,0,3) != $company)) $dialplan_number = (($company * 1) + 0) . $dialplan_number;
                    $voicemail_id = (($company * 1) + 0) . $voicemail_id;
                    $login = (($company * 1) + 0) . $login;
                }
                $stmt="UPDATE phones set extension='$extension', dialplan_number='$dialplan_number', voicemail_id='$voicemail_id', phone_ip='$phone_ip', computer_ip='$computer_ip', server_ip='$server_ip', login='$login', pass='$pass', status='$status', active='$active', phone_type='$phone_type', fullname='$fullname', company='$company', picture='$picture', protocol='$protocol', local_gmt='$local_gmt', ASTmgrUSERNAME='$ASTmgrUSERNAME', ASTmgrSECRET='$ASTmgrSECRET', login_user='$login_user', login_pass='$login_pass', login_campaign='$login_campaign', park_on_extension='$park_on_extension', conf_on_extension='$conf_on_extension', OSDIAL_park_on_extension='$OSDIAL_park_on_extension', OSDIAL_park_on_filename='$OSDIAL_park_on_filename', monitor_prefix='$monitor_prefix', recording_exten='$recording_exten', voicemail_exten='$voicemail_exten', voicemail_dump_exten='$voicemail_dump_exten', ext_context='$ext_context', dtmf_send_extension='$dtmf_send_extension', call_out_number_group='$call_out_number_group', client_browser='$client_browser', install_directory='$install_directory', local_web_callerID_URL='" . mysql_real_escape_string($local_web_callerID_URL) . "', OSDIAL_web_URL='" . mysql_real_escape_string($OSDIAL_web_URL) . "', AGI_call_logging_enabled='$AGI_call_logging_enabled', user_switching_enabled='$user_switching_enabled', conferencing_enabled='$conferencing_enabled', admin_hangup_enabled='$admin_hangup_enabled', admin_hijack_enabled='$admin_hijack_enabled', admin_monitor_enabled='$admin_monitor_enabled', call_parking_enabled='$call_parking_enabled', updater_check_enabled='$updater_check_enabled', AFLogging_enabled='$AFLogging_enabled', QUEUE_ACTION_enabled='$QUEUE_ACTION_enabled', CallerID_popup_enabled='$CallerID_popup_enabled', voicemail_button_enabled='$voicemail_button_enabled', enable_fast_refresh='$enable_fast_refresh', fast_refresh_rate='$fast_refresh_rate', enable_persistant_mysql='$enable_persistant_mysql', auto_dial_next_number='$auto_dial_next_number', VDstop_rec_after_each_call='$VDstop_rec_after_each_call', DBX_server='$DBX_server', DBX_database='$DBX_database', DBX_user='$DBX_user', DBX_pass='$DBX_pass', DBX_port='$DBX_port', DBY_server='$DBY_server', DBY_database='$DBY_database', DBY_user='$DBY_user', DBY_pass='$DBY_pass', DBY_port='$DBY_port', outbound_cid='$outbound_cid', outbound_cid_name='$outbound_cid_name', enable_sipsak_messages='$enable_sipsak_messages' where extension='$old_extension' and server_ip='$old_server_ip';";
                $rslt=mysql_query($stmt, $link);
            }
        }
        $ADD=31111111111;	# go to phone modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=51111111111 confirmation before deletion of phone record
######################

if ($ADD==51111111111) {
    if ($LOGast_admin_access==1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";
        if ( (strlen($extension) < 2) or (strlen($server_ip) < 7) or ($LOGast_delete_phones < 1) ) {
            echo "<br><font color=red>PHONE NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>Extension be at least 2 characters in length\n";
            echo "<br>Server IP be at least 7 characters in length</font>\n";
        } else {
            echo "<br><B><font color=$default_text>PHONE DELETION CONFIRMATION: $extension - $server_ip</B>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=61111111111&extension=$extension&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete phone $extension - $server_ip</a></font><br><br><br>\n";
        }
        $ADD='31111111111';		# go to phone modification below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=61111111111 delete phone record
######################

if ($ADD==61111111111) {
    if ($LOGast_admin_access==1) {
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

        if ( (strlen($extension) < 2) or (strlen($server_ip) < 7) or ($CoNfIrM != 'YES') or ($LOGast_delete_phones < 1) ) {
            echo "<br><font color=red>PHONE NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>Extension be at least 2 characters in length\n";
            echo "<br>Server IP be at least 7 characters in length</font><br>\n";
        } else {
            $stmt="DELETE from phones where extension='$extension' and server_ip='$server_ip' limit 1;";
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|!!!DELETING PHONE!!!|$PHP_AUTH_USER|$ip|extension='$extension'|server_ip='$server_ip'|\n");
                fclose($fp);
            }
            echo "<br><B><font color=$default_text>PHONE DELETION COMPLETED: $extension - $server_ip</font></B>\n";
            echo "<br><br>\n";
        }
        $ADD='10000000000';		# go to phone list
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=31111111111 modify phone record in the system
######################

if ($ADD==31111111111) {
    if ($LOGast_admin_access==1) {
        echo "<TABLE align=center><TR><TD>\n";
        echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

        $stmt="SELECT * from phones where extension='$extension' and server_ip='$server_ip';";
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $servers_list = get_servers($link, $row[5]);

        echo "<center><br><font color=$default_text size=+1>MODIFY A PHONE</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=41111111111>\n";
        echo "<input type=hidden name=old_extension value=\"$row[0]\">\n";
        echo "<input type=hidden name=old_server_ip value=\"$row[5]\">\n";
        echo "<TABLE width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Phone extension: </td><td align=left>";
        $ext = $row[0];
        if ($LOG['multicomp'] > 0 and !preg_match('/\/|@/',$row[0]) and preg_match($LOG['companiesRE'],$row[0])) {
            echo "<font color=$default_text>" . $row[12] . "</font>";
            if ($row[12] == substr($row[0],0,3)) $ext = substr($row[0],3);
        }
        echo "<input type=text name=extension size=20 maxlength=100 value=\"" . $ext . "\">";
        echo "$NWB#phones-extension$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Exten Context: </td><td align=left><input type=text name=ext_context size=20 maxlength=20 value=\"$row[31]\">$NWB#phones-ext_context$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Dial Plan Number: </td><td align=left>";
        $dpn = $row[1];
        if ($LOG['multicomp'] > 0) {
            if (preg_match('/SIP|IAX/',$row[16]) and preg_match($LOG['companiesRE'],$row[1])) {
                echo "<font color=$default_text>" . $row[12] . "</font>";
                if ($row[12] == substr($row[1],0,3)) $dpn = substr($row[1],3);
            }
        }
        echo "<input type=text name=dialplan_number size=15 maxlength=20 value=\"$dpn\">";
        echo " (digits only)$NWB#phones-dialplan_number$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Voicemail Box: </td><td align=left>";
        $vmb = $row[2];
        if ($LOG['multicomp'] > 0 and preg_match($LOG['companiesRE'],$row[2])) {
            echo "<font color=$default_text>" . $row[12] . "</font>";
            if ($row[12] == substr($row[2],0,3)) $vmb = substr($row[2],3);
        }
        echo "<input type=text name=voicemail_id size=10 maxlength=10 value=\"$vmb\">";
        echo " (digits only)$NWB#phones-voicemail_id$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Outbound CallerID Name: </td><td align=left><input type=text name=outbound_cid_name size=20 maxlength=40 value=\"$row[67]\">$NWB#phones-outbound_cid_name$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Outbound CallerID: </td><td align=left><input type=text name=outbound_cid size=10 maxlength=20 value=\"$row[65]\"> (digits only)$NWB#phones-outbound_cid$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Phone IP address: </td><td align=left><input type=text name=phone_ip size=20 maxlength=15 value=\"$row[3]\"> (optional)$NWB#phones-phone_ip$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Computer IP address: </td><td align=left><input type=text name=computer_ip size=20 maxlength=15 value=\"$row[4]\"> (optional)$NWB#phones-computer_ip$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right><a href=\"$PHP_SELF?ADD=311111111111&server_ip=$row[5]\">Server IP</a>: </td><td align=left><select size=1 name=server_ip>\n";

        echo "$servers_list";
        #echo "<option SELECTED>$row[5]</option>\n";
        echo "</select>$NWB#phones-server_ip$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Login: </td><td align=left>";
        $plog = $row[6];
        if ($LOG['multicomp'] > 0 and preg_match($LOG['companiesRE'],$row[6])) {
            echo "<font color=$default_text>" . $row[12] . "</font>";
            if ($row[12] == substr($row[6],0,3)) $plog = substr($row[6],3);
        }
        echo "<input type=text name=login size=10 maxlength=10 value=\"" . $plog . "\">";
        echo "$NWB#phones-login$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=pass size=10 maxlength=10 value=\"$row[7]\">$NWB#phones-pass$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Status: </td><td align=left><select size=1 name=status><option>ACTIVE</option><option>SUSPENDED</option><option>CLOSED</option><option>PENDING</option><option>ADMIN</option><option selected>$row[8]</option></select>$NWB#phones-status$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Active Account: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option selected>$row[9]</option></select>$NWB#phones-active$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Phone Type: </td><td align=left><input type=text name=phone_type size=20 maxlength=50 value=\"$row[10]\">$NWB#phones-phone_type$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Full Name: </td><td align=left><input type=text name=fullname size=20 maxlength=50 value=\"$row[11]\">$NWB#phones-fullname$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Company: </td><td align=left>";
        if ($LOG['multicomp_admin'] > 0 and preg_match($LOG['companiesRE'],$row[12])) {
            $comps = get_krh($link, 'osdial_companies', '*','',"status IN ('ACTIVE','INACTIVE','SUSPENDED')",'');
            echo "<select name=company>\n";
            foreach ($comps as $comp) {
                $csel = '';
                if ((($comp['id'] * 1) + 100) == ($row[12] * 1)) $csel='selected';
                echo "<option value=" . (($comp['id'] * 1) + 100) . " $csel>" . (($comp['id'] * 1) + 100) . ": " . $comp['name'] . "</option>\n";
            }
            echo "</select>\n";
            echo "$NWB#phones-company$NWE";
        } elseif ($LOG['multicomp']>0 and preg_match($LOG['companiesRE'],$row[0])) {
            echo "<input type=hidden name=company value=$row[12]>";
            echo "<font color=$default_text>" . $row[12] . "</font>";
        } else {
            echo "<input type=text name=company size=10 maxlength=10 value=\"$row[12]\">";
            echo "$NWB#phones-company$NWE";
        }

        echo "</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Picture: </td><td align=left><input type=text name=picture size=20 maxlength=19 value=\"$row[13]\">$NWB#phones-picture$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>New Messages: </td><td align=left><b>$row[14]</b>$NWB#phones-messages$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Old Messages: </td><td align=left><b>$row[15]</b>$NWB#phones-old_messages$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Client Protocol: </td><td align=left><select size=1 name=protocol><option>SIP</option><option>DAHDI</option><option>Zap</option><option>IAX2</option><option>EXTERNAL</option><option selected>$row[16]</option></select>$NWB#phones-protocol$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$row[17]</option></select> (Do NOT Adjust for DST)$NWB#phones-local_gmt$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>" . $t1 . " Default Agent: </td><td align=left><input type=text name=login_user size=20 maxlength=20 value=\"$row[20]\">$NWB#phones-login_user$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>" . $t1 . " Default Pass: </td><td align=left><input type=text name=login_pass size=20 maxlength=20 value=\"$row[21]\">$NWB#phones-login_pass$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>" . $t1 . " Default Campaign: </td><td align=left><input type=text name=login_campaign size=10 maxlength=10 value=\"$row[22]\">$NWB#phones-login_campaign$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Recording Exten: </td><td align=left><input type=text name=recording_exten size=10 maxlength=10 value=\"$row[28]\">$NWB#phones-recording_exten$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>DTMFSend Channel: </td><td align=left><input type=text name=dtmf_send_extension size=40 maxlength=100 value=\"$row[32]\">$NWB#phones-dtmf_send_extension$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Fast Refresh: </td><td align=left><select size=1 name=enable_fast_refresh><option>1</option><option>0</option><option selected>$row[50]</option></select>$NWB#phones-enable_fast_refresh$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Fast Refresh Rate: </td><td align=left><input type=text size=5 name=fast_refresh_rate value=\"$row[51]\">(in ms)$NWB#phones-fast_refresh_rate$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Stop Rec after each call: </td><td align=left><select size=1 name=VDstop_rec_after_each_call><option>1</option><option>0</option><option selected>$row[54]</option></select>$NWB#phones-VDstop_rec_after_each_call$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable SIPSAK Messages: </td><td align=left><select size=1 name=enable_sipsak_messages><option>1</option><option>0</option><option selected>$row[66]</option></select>$NWB#phones-enable_sipsak_messages$NWE</td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";

        echo "<input type=hidden name=ASTmgrUSERNAME value=\"$row[18]\">\n";
        echo "<input type=hidden name=ASTmgrSECRET value=\"$row[19]\">\n";
        echo "<input type=hidden name=park_on_extension value=\"$row[23]\">\n";
        echo "<input type=hidden name=conf_on_extension value=\"$row[24]\">\n";
        echo "<input type=hidden name=OSDIAL_park_on_extension value=\"$row[25]\">\n";
        echo "<input type=hidden name=OSDIAL_park_on_filename value=\"$row[26]\">\n";
        echo "<input type=hidden name=monitor_prefix value=\"$row[27]\">\n";
        echo "<input type=hidden name=voicemail_exten value=\"$row[29]\">\n";
        echo "<input type=hidden name=voicemail_dump_exten value=\"$row[30]\">\n";
        echo "<input type=hidden name=call_out_number_group value=\"$row[33]\">\n";
        echo "<input type=hidden name=client_browser value=\"$row[34]\">\n";
        echo "<input type=hidden name=install_directory value=\"$row[35]\">\n";
        echo "<input type=hidden name=local_web_callerID_URL value=\"$row[36]\">\n";
        echo "<input type=hidden name=OSDIAL_web_URL value=\"$row[37]\">\n";
        echo "<input type=hidden name=AGI_call_logging_enabled value=\"$row[38]\">\n";
        echo "<input type=hidden name=user_switching_enabled value=\"$row[39]\">\n";
        echo "<input type=hidden name=conferencing_enabled value=\"$row[40]\">\n";
        echo "<input type=hidden name=admin_hangup_enabled value=\"$row[41]\">\n";
        echo "<input type=hidden name=admin_hijack_enabled value=\"$row[42]\">\n";
        echo "<input type=hidden name=admin_monitor_enabled value=\"$row[43]\">\n";
        echo "<input type=hidden name=call_parking_enabled value=\"$row[44]\">\n";
        echo "<input type=hidden name=updater_check_enabled value=\"$row[45]\">\n";
        echo "<input type=hidden name=AFLogging_enabled value=\"$row[46]\">\n";
        echo "<input type=hidden name=QUEUE_ACTION_enabled value=\"$row[47]\">\n";
        echo "<input type=hidden name=CallerID_popup_enabled value=\"$row[48]\">\n";
        echo "<input type=hidden name=voicemail_button_enabled value=\"$row[49]\">\n";
        echo "<input type=hidden name=enable_persistant_mysql value=\"$row[52]\">\n";
        echo "<input type=hidden name=auto_dial_next_number value=\"$row[53]\">\n";
        echo "<input type=hidden name=DBX_server value=\"$row[55]\">\n";
        echo "<input type=hidden name=DBX_database value=\"$row[56]\">\n";
        echo "<input type=hidden name=DBX_user size=15 maxlength=15 value=\"$row[57]\">\n";
        echo "<input type=hidden name=DBX_pass size=15 maxlength=15 value=\"$row[58]\">\n";
        echo "<input type=hidden name=DBX_port size=6 maxlength=6 value=\"$row[59]\">\n";
        echo "<input type=hidden name=DBY_server size=15 maxlength=15 value=\"$row[60]\">\n";
        echo "<input type=hidden name=DBY_database size=15 maxlength=15 value=\"$row[61]\">\n";
        echo "<input type=hidden name=DBY_user size=15 maxlength=15 value=\"$row[62]\">\n";
        echo "<input type=hidden name=DBY_pass size=15 maxlength=15 value=\"$row[63]\">\n";
        echo "<input type=hidden name=DBY_port size=6 maxlength=6 value=\"$row[64]\">\n";

        echo "<br><br><a href=\"$PHP_SELF?ADD=999999&SUB=10&iframe=phone_stats.php?extension=$row[0]%26server_ip=$row[5]'\">Click here for phone stats</a><br><br>\n";

        if ($LOGast_delete_phones > 0) {
            echo "<br><br><a href=\"$PHP_SELF?ADD=51111111111&extension=$extension&server_ip=$server_ip\">DELETE THIS PHONE</a>\n";
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=10000000000 display all phones
######################
if ($ADD==10000000000) {
    echo "<TABLE align=center><TR><TD>\n";
    echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=$default_text SIZE=2>";

    $EXTENlink='stage=EXTENDOWN';
    $PROTOlink='stage=PROTODOWN';
    $SERVERlink='stage=SERVERDOWN';
    $STATUSlink='stage=STATUSDOWN';
    $SQLorder='order by extension,server_ip';
    if (eregi("EXTENUP",$stage)) {$SQLorder='order by extension asc';   $EXTENlink='stage=EXTENDOWN';}
    if (eregi("EXTENDOWN",$stage)) {$SQLorder='order by extension desc';   $EXTENlink='stage=EXTENUP';}
    if (eregi("PROTOUP",$stage)) {$SQLorder='order by protocol asc';   $PROTOlink='stage=PROTODOWN';}
    if (eregi("PROTODOWN",$stage)) {$SQLorder='order by protocol desc';   $PROTOlink='stage=PROTOUP';}
    if (eregi("SERVERUP",$stage)) {$SQLorder='order by server_ip asc';   $SERVERlink='stage=SERVERDOWN';}
    if (eregi("SERVERDOWN",$stage)) {$SQLorder='order by server_ip desc';   $SERVERlink='stage=SERVERUP';}
    if (eregi("STATUSUP",$stage)) {$SQLorder='order by status asc';   $STATUSlink='stage=STATUSDOWN';}
    if (eregi("STATUSDOWN",$stage)) {$SQLorder='order by status desc';   $STATUSlink='stage=STATUSUP';}

    if ($LOG['multicomp_user'] > 0) {
        $stmt=sprintf("SELECT * FROM phones WHERE company='%s' %s;",$LOG['company_prefix'],$SQLorder);
    } else {
        $stmt=sprintf("SELECT * FROM phones %s;",$SQLorder);
    }
    $rslt=mysql_query($stmt, $link);
    $phones_to_print = mysql_num_rows($rslt);

    echo "<center><br><font color=$default_text size=+1>PHONES<br><br><font size=-2>(<a href=#VMList>VoiceMail List</a>)</font><br><br>\n";
    echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "  <tr class=tabheader>";
    echo "    <td><a href=\"$PHP_SELF?ADD=10000000000&$EXTENlink\">EXTEN</a></td>\n";
    echo "    <td><a href=\"$PHP_SELF?ADD=10000000000&$PROTOlink\">PROTO</a></td>\n";
    echo "    <td><a href=\"$PHP_SELF?ADD=10000000000&$SERVERlink\">SERVER</a></td>\n";
    echo "    <td>DIALPLAN</B></td>\n";
    echo "    <td><a href=\"$PHP_SELF?ADD=10000000000&$STATUSlink\">STATUS</a></td>\n";
    echo "    <td>NAME</td>\n";
    echo "    <td colspan=3>VOICEMAIL</td>\n";
    echo "    <td align=center>LINKS</td>\n";
    echo "  </tr>\n";

    $o=0;
    while ($phones_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        if (eregi("1$|3$|5$|7$|9$", $o)) {
            $bgcolor='bgcolor='.$oddrows;
        } else {
            $bgcolor='bgcolor='.$evenrows;
        }
        echo "  <tr $bgcolor class=\"row font1\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=31111111111&extension=$row[0]&server_ip=$row[5]\">";
        if ($LOG['multicomp'] and !preg_match('/\/|@/',$row[0]) and preg_match($LOG['campaignsRE'],$row[0])) {
            echo $row[12] . "&nbsp;" . substr($row[0],3);
        } else {
            echo $row[0];
        }
        echo "</a></td>\n";
        echo "    <td>$row[16]</td>\n";
        echo "    <td>$row[5]</td>\n";
        echo "    <td>$row[1]</td>\n";
        echo "    <td>$row[8]</td>\n";
        echo "    <td>$row[11]</td>\n";
        echo "    <td>$row[2]</td>\n";
        echo "    <td>$row[14]</td>\n";
        echo "    <td>$row[15]</td>\n";
        echo "    <td align=center><a href=\"$PHP_SELF?ADD=31111111111&extension=$row[0]&server_ip=$row[5]\">MODIFY</a> | <a href=\"$PHP_SELF?ADD=999999&SUB=10&iframe=phone_stats.php?extension=$row[0]%26server_ip=$row[5]\">STATS</a></td>\n";
        echo "  </tr>\n";
        $o++;
    }

    echo "  <tr class=tabfooter>\n";
    echo "    <td colspan=10></td>\n";
    echo "  </tr>\n";
    echo "</table></center>\n";

    if ($LOG['multicomp'] == 0) {
        // List all voicemail on dialer 1
        echo "<a name=VMList></a>";
        echo '<br><br><br><br>';
        echo '<center>';
        echo "<b><font color=$default_text size=-1>VOICE MAIL</b><br>";
        if (file_exists ('VMnow.txt') ) {
            echo "<font color=$default_text><p> As of " . date("l dS o F h:i:s A",filectime('VMnow.txt') )  . "</p></font>";
            echo "<table bgcolor=grey cellspacing=1 align=center width=560>\n";
            echo "  <tr class=tabheader>\n";
            echo "    <td width=10 align=center>Context</td>\n";
            echo "    <td width=30 align=center>Mbox</td>\n";
            echo "    <td width=110 align=center>Agent</td>\n";
            echo "    <td width=35 align=right>NewMsgs</td>\n";
            echo "  </tr>";
            #echo "</table>";
            // get a web page into an array and print it out ("l dS of F Y h:i:s A")
            $fcontents = file( 'VMnow.txt' );
            #echo "<table>";
            $o=0;
            while ( list( $line_num, $line ) = each( $fcontents ) ) {
                // Exit if the Verbosity line shows up - Obscured by only listing vm context 'default'
                //if ( substr($line,0,9) == "Verbosity") {
                //        break;
                //}
                // Ensuring only vm entries show up
                if ( substr($line,0,7) == "default" ) {
                    if (eregi("1$|3$|5$|7$|9$", $o)) {
                        $bgcolor='bgcolor='.$oddrows;
                    } else {
                        $bgcolor='bgcolor='.$evenrows;
                    }
                    $line = rtrim($line);
                    $lary = preg_split("/\\s+/",$line);
                    echo "  <tr $bgcolor class=\"row font1\">\n";
                    echo "    <td>" . $lary[0] . "</td>\n";
                    echo "    <td>" . $lary[1] . "</td>\n";
                    $llast = count($lary) - 1;
                    $lagent='';
                    if ($llast - 1 > 2) {
                        foreach (range(2, $llast - 1) as $lnum) {
                            $lagent .= $lary[$lnum] . " ";
                        }
                        $lagent = rtrim($lagent);
                    }
                    echo "    <td>" . $lagent . "</td>\n";
                    echo "    <td align=right>" . $lary[$llast] . "</td>\n";
                    echo "</tr>";
                    #echo "<tr><td><pre>" . $line . "</td></tr>";
                    $o++;
                }
            }
            echo "  <tr class=tabfooter>\n";
            echo "    <td colspan=4></td>\n";
            echo "  </tr>\n";
            echo "</table>";
        } else {
            echo "Error! VMnow.txt is missing!";
        }
    }
}


?>
