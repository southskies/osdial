<?php
#
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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
# ADD=10comp display the ADD NEW COMPANY SCREEN
######################

if ($ADD=="11comp") {
    if ($LOG['multicomp_admin'] > 0) {
        echo "<center><br><font color=$default_text size=+1>ADD A NEW COMPANY</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=21comp>\n";

        echo "<table width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right width=50%>Company Name: </td><td align=left><input type=text name=company_name size=30 maxlength=100 value=\"\">$NWB#companies-company_name$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows>\n";
        echo "  <td align=right>Default Server IP: </td>\n";
        echo "  <td align=left>\n";
        echo "    <select name=server_ip>\n";
        $servers = get_krh($link, 'servers', '*,INET_ATON(server_ip) AS aton','aton ASC',"active='Y' AND server_profile IN ('AIO','DIALER')",'');
        foreach ($servers as $server) {
            echo "      <option value=\"$server[server_ip]\">$server[server_ip] - $server[server_id]</option>\n";
        }
        echo "    </select>\n";
        echo "    $NWB#companies-default_server_ip$NWE\n";
        echo "  </td>\n";
        echo "</tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Default Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option selected>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option></select> (Do NOT Adjust for DST)$NWB#companies-default_local_gmt$NWE</td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</table></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=21comp adds new company to the system
######################

if ($ADD=="21comp") {
    if ($LOG['multicomp_admin'] > 0) {
        if (OSDstrlen($company_name) < 3) {
            echo "<br><font color=red>COMPANY NOT ADDED - Please go back and look at the data you entered</font>\n";
        } else {
            echo "<br><font color=$default_text>COMAPNY ADDED</font>\n";

            $stmt=sprintf("INSERT INTO osdial_companies SET name='%s';",mres($company_name));
            $rslt=mysql_query($stmt, $link);
            $company_id =  mysql_insert_id($link);

            $cmp = (($company_id * 1) + 100);

            echo "<br><br>\n";
            # Add inital sample configuration.
            $pins = "INSERT INTO phones VALUES ";
            $pins .= sprintf("('%s1001','%s1001','%s1001','','','%s','%s1001','1001','ACTIVE','Y','SIP','Ext %s 1001','%s','NA',0,0,'SIP','%s','cron','1234','','','','8301','8302','8301','park','8612','8309','8501','85026666666666','osdial','local/8500998@osdial','Zap/g2/','/usr/bin/mozilla','/usr/local/perl_TK','http://localhost/test_callerid_output.php','http://localhost/test_osdial_output.php','1','1','1','0','0','1','1','1','1','1','1','1','0',1000,'0','1','1','','asterisk','cron','1234',3306,'','asterisk','cron','1234',3306,'%s1001','0','','1234',''),",$cmp,$cmp,$cmp,$server_ip,$cmp,$cmp,$cmp,$local_gmt,$cmp);
            $pins .= sprintf("('%s1002','%s1002','%s1002','','','%s','%s1002','1002','ACTIVE','Y','SIP','Ext %s 1002','%s','NA',0,0,'SIP','%s','cron','1234','','','','8301','8302','8301','park','8612','8309','8501','85026666666666','osdial','local/8500998@osdial','Zap/g2/','/usr/bin/mozilla','/usr/local/perl_TK','http://localhost/test_callerid_output.php','http://localhost/test_osdial_output.php','1','1','1','0','0','1','1','1','1','1','1','1','0',1000,'0','1','1','','asterisk','cron','1234',3306,'','asterisk','cron','1234',3306,'%s1002','0','','1234',''),",$cmp,$cmp,$cmp,$server_ip,$cmp,$cmp,$cmp,$local_gmt,$cmp);
            $pins .= sprintf("('%s9999','9999','%s9999','','','%s','%s9999','9999','ACTIVE','Y','Test','Test Phone','%s','NA',0,0,'EXTERNAL','%s','cron','1234','','','','8301','8302','8301','park','8612','8309','8501','85026666666666','osdial','local/8500998@osdial','Zap/g2/','/usr/bin/mozilla','/usr/local/perl_TK','http://localhost/test_callerid_output.php','http://localhost/test_osdial_output.php','1','1','1','0','0','1','1','1','1','1','1','1','0',1000,'0','1','1','','asterisk','cron','1234',3306,'','asterisk','cron','1234',3306,'%s9999','0','','1234','');",$cmp,$cmp,$server_ip,$cmp,$cmp,$local_gmt,$cmp);
            $rslt=mysql_query($pins, $link);
            echo "<font size=1 color=$default_text>SAMPLE PHONE CONFIGURATION ADDED - ".mysql_affected_rows()."</font><br>\n";

            $uins = "INSERT INTO osdial_users VALUES ";
            $uins .= sprintf("('','%sadmin','admin','Admin %s',9,'%sADMIN','','','1','1','1','1','1','1','1','1','1','1','1','1','0','1','1','','1','0','1','1','1','1','1','0','1','1','1','1','1','1','1','1','1','1','1','1','DISABLED','NOT_ACTIVE',-1,'1','1','1','1','0','','1','1','1'),",$cmp,$cmp,$cmp);
            $uins .= sprintf("('','%s1001','1001','Agent %s 1001',4,'%sAGENTS','','','0','0','0','0','0','0','0','0','0','0','0','0','1','0','0','','1','1','1','1','1','0','0','1','0','0','0','0','0','0','0','0','0','0','0','0','DISABLED','NOT_ACTIVE',-1,'1','0','0','0','0','','1','0','0'),",$cmp,$cmp,$cmp);
            $uins .= sprintf("('','%s1002','1002','Agent %s 1002',4,'%sAGENTS','','','0','0','0','0','0','0','0','0','0','0','0','0','1','0','0','','1','1','1','1','1','0','0','1','0','0','0','0','0','0','0','0','0','0','0','0','DISABLED','NOT_ACTIVE',-1,'1','0','0','0','0','','1','0','0');",$cmp,$cmp,$cmp);
            $rslt=mysql_query($uins, $link);
            echo "<font size=1 color=$default_text>SAMPLE USER CONFIGURATION ADDED - ".mysql_affected_rows()."</font><br>\n";

            $ugins = "INSERT INTO osdial_user_groups (user_group,group_name,allowed_campaigns,allowed_scripts,allowed_email_templates,allowed_ingroups) VALUES ";
            $ugins .= sprintf("('%sADMIN','OSDIAL ADMINISTRATORS',' -ALL-CAMPAIGNS- - -',' -ALL-SCRIPTS- -',' -ALL-EMAIL-TEMPLATES- -',' -ALL-INGROUPS- -'),('%sAGENTS','Agent User Group',' -ALL-CAMPAIGNS- - -',' -ALL-SCRIPTS- -',' -ALL-EMAIL-TEMPLATES- -',' -ALL-INGROUPS- -');",$cmp,$cmp);
            $rslt=mysql_query($ugins, $link);
            echo "<font size=1 color=$default_text>SAMPLE USERGROUP CONFIGURATION ADDED - ".mysql_affected_rows()."</font><br>\n";

            $sins = "INSERT INTO osdial_scripts VALUES ";
            $sins .= sprintf("('%sTEST','Test Script','Just a quick test','Hello Mr/Mrs [[last_name]],<br><br>We are calling you at [[phone_number]].<br><br>Your address is:<br>[[address1]]<br>[[city]], [[state]] [[postal_code]]<br><br>Thank-you','Y');",$cmp);
            $rslt=mysql_query($sins, $link);
            echo "<font size=1 color=$default_text>SAMPLE SCRIPT ADDED - ".mysql_affected_rows()."</font><br>\n";

            $olins = "INSERT INTO osdial_lists VALUES ";
            $olins .= sprintf("(%s998,'Default inbound list','%sTEST','N',NULL,NULL,NULL,'N',NULL,'',0,'','',''),",$cmp,$cmp);
            $olins .= sprintf("(%s999,'Default manual list','%sTEST','N',NULL,NULL,NULL,'N',NULL,'',0,'','','');",$cmp,$cmp);
            $rslt=mysql_query($olins, $link);
            echo "<font size=1 color=$default_text>SAMPLE LISTS ADDED - ".mysql_affected_rows()."</font><br>\n";


            $ocins = "INSERT INTO osdial_campaigns VALUES ";
            $ocins .= sprintf("('%sTEST','Test Campaign %s','Y','','','','','','DOWN','8301','park','/osdial/agent/webform_redirect.php','Y',200,'0','oldest_call_finish','24hours','',28,'9','0000000000','8368','8309','ONDEMAND','CAMPAIGN_AGENT_FULLDATE_CUSTPHONE','','NONE','8320','Y','','','','','N','Y','NONE',8,'Y','8307','Y',0,'Wrapup Call','','Y',0,'N','MANUAL','N',3,'3.0','2100','0',0,'AUTO','NONE',' A AA B N NA DC -','N','Test Campaign','2010-03-08 00:19:25','N',NULL,' A AA AL AM B CALLBK DROP NEW N NA -','N','Y','DISABLED','Y',%s999,'---NONE---','','/osdial/agent/webform_redirect.php','Y',0,'',10,'Y','Y','Y','NORMAL','N','2008-01-01 00:00:00','','CAMPAIGN','N','%s','','N','N','N','N','N','N','N','N','N','N','N');",$cmp,$cmp,$cmp,$config['settings']['default_carrier_id']);
            $rslt=mysql_query($ocins, $link);
            echo "<font size=1 color=$default_text>SAMPLE CAMPAIGN ADDED - ".mysql_affected_rows()."</font><br>\n";

            $ochkins = "INSERT INTO osdial_campaign_hotkeys VALUES ";
            $ochkins .= sprintf("('N','1','No Answer','Y','%sTEST',''),",$cmp);
            $ochkins .= sprintf("('A','2','Answering Machine','Y','%sTEST',''),",$cmp);
            $ochkins .= sprintf("('NI','3','Not Interested','Y','%sTEST',''),",$cmp);
            $ochkins .= sprintf("('CALLBK','4','Call Back','Y','%sTEST',''),",$cmp);
            $ochkins .= sprintf("('SALE','5','Sale Made','Y','%sTEST','');",$cmp);
            $rslt=mysql_query($ochkins, $link);
            echo "<font size=1 color=$default_text>SAMPLE CAMPAIGN-HOTKEYS ADDED - ".mysql_affected_rows()."</font><br>\n";

            echo "<br>";
        }
        $ADD="31comp";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=41comp modify company record in the system
######################
if ($ADD=="41comp") {
    if ($LOG['multicomp_admin']>0) {
        if (OSDstrlen($company_name) < 3) {
            echo "<br><font color=$default_text>COMPANY NOT MODIFIED - Please go back and look at the data you entered</font>\n";
        } else {
            echo "<br><font color=$default_text>COMPANY MODIFIED: $company_id : $company_name</font>\n";

            $stmt=sprintf("UPDATE osdial_companies SET name='%s',status='%s',enable_campaign_ivr='%s',enable_campaign_listmix='%s',export_leads='%s',enable_scripts='%s',enable_filters='%s',enable_ingroups='%s',enable_external_agents='%s',enable_system_calltimes='%s',enable_system_phones='%s',enable_system_conferences='%s',enable_system_servers='%s',enable_system_statuses='%s',api_access='%s',dnc_method='%s',default_server_ip='%s',default_local_gmt='%s',default_ext_context='%s',enable_system_carriers='%s' WHERE id='%s';",mres($company_name),mres($company_status),mres($company_enable_campaign_ivr),mres($company_enable_campaign_listmix),mres($company_export_leads),mres($company_enable_scripts),mres($company_enable_filters),mres($company_enable_ingroups),mres($company_enable_external_agents),mres($company_enable_system_calltimes),mres($company_enable_system_phones),mres($company_enable_system_conferences),mres($company_enable_system_servers),mres($company_enable_system_statuses),mres($company_api_access),mres($company_dnc_method),mres($server_ip),mres($local_gmt),mres($ext_context),mres($company_enable_system_carriers),mres($company_id));
            $rslt=mysql_query($stmt, $link);
        }
        $ADD="31comp";	# go to company modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}




######################
# ADD=51comp confirmation before deletion of company record
######################
if ($ADD=="51comp") {
    if ($LOG['multicomp_admin']>0) {
        echo "<br><B><font color=$default_text>COMPANY DELETION CONFIRMATION: $extension - $server_ip</B>\n";
        echo "<br><br><a href=\"$PHP_SELF?ADD=61comp&company_id=$company_id&CoNfIrM=YES\">Click here to delete company $company_id</a></font><br><br><br>\n";
        $ADD='31comp';		# go to company modification below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=61comp delete company record
######################
if ($ADD=="61comp") {
    if ($LOG['multicomp_admin']>0) {
        $stmt=sprintf("DELETE FROM osdial_companies WHERE id='%s' LIMIT 1;",mres($company_id));
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!!!DELETING COMPANY!!!|$PHP_AUTH_USER|$ip|id='$company_id'||\n");
            fclose($fp);
        }
        echo "<br><B><font color=$default_text>COMPANY DELETION COMPLETED: $company_id</font></B>\n";
        echo "<br><br>\n";
        $ADD='10comp';		# go to company list
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=31comp modify company record in the system
######################
if ($ADD=="31comp") {
    if ($LOG['multicomp_admin']>0) {
        $comp = get_first_record($link, 'osdial_companies', '*', sprintf("id='%s'",mres($company_id)) );

        echo "<center><br><font color=$default_text size=+1>MODIFY A COMPANY</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=41comp>\n";
        echo "<input type=hidden name=company_id value=$comp[id]>\n";
        echo "<table width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Company Prefix: </td><td align=left><font color=$default_text>" . (($comp['id'] * 1) + 100) . "</font></td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Company Name: </td><td align=left><input type=text name=company_name size=30 maxlength=100 value=\"$comp[name]\">$NWB#companies-company_name$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Status: </td><td align=left><select name=company_status><option>INACTIVE</option><option>ACTIVE</option><option>SUSPENDED</option><option>TERMINATED</option><option selected>$comp[status]</option></select>$NWB#companies-status$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable Campaign IVR: </td><td align=left><select name=company_enable_campaign_ivr><option>0</option><option>1</option><option selected>$comp[enable_campaign_ivr]</option></select>$NWB#companies-enable_campaign_ivr$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable Campaign ListMix: </td><td align=left><select name=company_enable_campaign_listmix><option>0</option><option>1</option><option selected>$comp[enable_campaign_listmix]</option></select>$NWB#companies-enable_campaign_listmix$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Export Leads: </td><td align=left><select name=company_export_leads><option>0</option><option>1</option><option selected>$comp[export_leads]</option></select>$NWB#companies-export_leads$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable Scripts: </td><td align=left><select name=company_enable_scripts><option>0</option><option>1</option><option selected>$comp[enable_scripts]</option></select>$NWB#companies-enable_scripts$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable Filters: </td><td align=left><select name=company_enable_filters><option>0</option><option>1</option><option selected>$comp[enable_filters]</option></select>$NWB#companies-enable_filters$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable In-Groups: </td><td align=left><select name=company_enable_ingroups><option>0</option><option>1</option><option selected>$comp[enable_ingroups]</option></select>$NWB#companies-enable_ingroups$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable External Agents: </td><td align=left><select name=company_enable_external_agents><option>0</option><option>1</option><option selected>$comp[enable_external_agents]</option></select>$NWB#companies-enable_external_agents$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable System Call-Times: </td><td align=left><select name=company_enable_system_calltimes><option>0</option><option>1</option><option selected>$comp[enable_system_calltimes]</option></select>$NWB#companies-enable_system_calltimes$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable System Phones: </td><td align=left><select name=company_enable_system_phones><option>0</option><option>1</option><option selected>$comp[enable_system_phones]</option></select>$NWB#companies-enable_system_phones$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable System Conferences: </td><td align=left><select name=company_enable_system_conferences><option>0</option><option>1</option><option selected>$comp[enable_system_conferences]</option></select>$NWB#companies-enable_system_conferences$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable System Servers: </td><td align=left><select name=company_enable_system_servers><option>0</option><option>1</option><option selected>$comp[enable_system_servers]</option></select>$NWB#companies-enable_system_servers$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable System Statuses: </td><td align=left><select name=company_enable_system_statuses><option>0</option><option>1</option><option selected>$comp[enable_system_statuses]</option></select>$NWB#companies-enable_system_statuses$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Enable System Carriers: </td><td align=left><select name=company_enable_system_carriers><option>0</option><option>1</option><option selected>$comp[enable_system_carriers]</option></select>$NWB#companies-enable_system_carriers$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>API Access: </td><td align=left><select name=company_api_access><option>0</option><option>1</option><option selected>$comp[api_access]</option></select>$NWB#companies-api_access$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>DNC Method: </td><td align=left><select name=company_dnc_method><option>SYSTEM</option><option>COMPANY</option><option>BOTH</option><option selected>$comp[dnc_method]</option></select>$NWB#companies-dnc_method$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows>\n";
        echo "  <td align=right>Default Server IP: </td>\n";
        echo "  <td align=left>\n";
        echo "    <select name=server_ip>\n";
        $servers = get_krh($link, 'servers', '*','','','');
        foreach ($servers as $server) {
            $csel = '';
            if ($comp['default_server_ip'] == $server['server_ip']) $csel = 'selected';
            echo "      <option $csel value=\"$server[server_ip]\">$server[server_ip] - $server[server_id]</option>\n";
        }
        echo "    </select>\n";
        echo "    $NWB#companies-default_server_ip$NWE\n";
        echo "  </td>\n";
        echo "</tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Default Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$comp[default_local_gmt]</option></select> (Do NOT Adjust for DST)$NWB#companies-default_local_gmt$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Default Ext Context: </td><td align=left><input type=text name=ext_context size=15 maxlength=20 value=\"$comp[default_ext_context]\">$NWB#companies-default_ext_context$NWE (osdial/osdialEXT/osdialBLOCK)</td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";

        echo "<br><br>\n";

        echo "<br><br><a href=\"$PHP_SELF?ADD=51comp&company_id=$comp[id]\">DELETE THIS COMPANY</a>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=10comp display all companies
######################
if ($ADD=="10comp") {
    if ($LOG["multicomp_admin"] > 0) {
        echo "<center><br><font color=$default_text size=+1>COMPANIES<br><br>\n";
        echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
        echo "  <tr class=tabheader>";
        echo "    <td width=10%>ID</td>\n";
        echo "    <td width=20%>STATUS</td>\n";
        echo "    <td width=50%>NAME</td>\n";
        echo "    <td width=20% align=center>LINKS</td>\n";
        echo "  </tr>\n";

        $c=0;
        $comps = get_krh($link, 'osdial_companies', '*','','','');
        foreach ($comps as $comp) {
            echo "  <tr " . bgcolor($c++) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=31comp&company_id=$comp[id]';\">\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=31comp&company_id=$comp[id]\">" . (($comp['id'] * 1) + 100) . "</a></td>\n";
            echo "    <td>$comp[status]</td>\n";
            echo "    <td>$comp[name]</td>\n";
            echo "    <td align=center><a href=\"$PHP_SELF?ADD=31comp&company_id=$comp[id]\">MODIFY</a></td>\n";
            echo "  </tr>\n";
        }

        echo "  <tr class=tabfooter>\n";
        echo "    <td colspan=10></td>\n";
        echo "  </tr>\n";
        echo "</table></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

?>
