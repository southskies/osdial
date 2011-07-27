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
# ADD=111111111111 display the ADD NEW SERVER SCREEN
######################
if ($ADD==111111111111) {
    if ($LOG['modify_servers']==1) {
        echo "<center><br><font color=$default_text size=+1>ADD A NEW SERVER</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=211111111111>\n";
        echo "<TABLE width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server ID: </td><td align=left><input type=text name=server_id size=10 maxlength=10>$NWB#servers-server_id$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server Description: </td><td align=left><input type=text name=server_description size=30 maxlength=255>$NWB#servers-server_description$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server IP Address: </td><td align=left><input type=text name=server_ip size=20 maxlength=15>$NWB#servers-server_ip$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server Profile: </td><td align=left><select size=1 name=server_profile><option>AIO</option><option>CONTROL</option><option>SQL</option><option>WEB</option><option selected>DIALER</option><option>ARCHIVE</option><option>OTHER</option></select>$NWB#servers-server_profile$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option></select>$NWB#servers-active$NWE</td></tr>\n";
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=211111111111 adds new server to the system
######################
if ($ADD==211111111111) {
    if ($LOG['modify_servers']==1) {
        $stmt = sprintf("SELECT count(*) FROM servers WHERE server_id='%s';",mres($server_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0) {
            echo "<br><font color=red>SERVER NOT ADDED - there is already a server in the system with this ID</font>\n";
        } else {
            if (strlen($server_id) < 1 or strlen($server_ip) < 7) {
                echo "<br><font color=red>SERVER NOT ADDED - Please go back and look at the data you entered</font>\n";
            } else {
                $asterisk_version='1.6.18';
                $max_osdial_trunks='200';
                $osdial_balance_active='Y';
                if (preg_match('/CONTROL|SQL|WEB|ARCHIVE|OTHER/',$server_profile)) {
                    $asterisk_version='';
                    $max_osdial_trunks='0';
                    $osdial_balance_active='N';
                }
                
                echo "<br><font color=$default_text>SERVER ADDED</font>\n";
                $stmt=sprintf("INSERT INTO servers (server_id,server_description,server_ip,server_profile,active,asterisk_version,max_osdial_trunks,osdial_balance_active) ".
                    "VALUES ('%s','%s','%s','%s','%s','%s','%s','%s');",
                    mres($server_id),mres($server_description),mres($server_ip),mres($server_profile),mres($active),mres($asterisk_version),mres($max_osdial_trunks),mres($osdial_balance_active));
                $rslt=mysql_query($stmt, $link);
            }
        }
        $ADD=311111111111;
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=221111111111 adds the new osdial server trunk record to the system
######################
if ($ADD==221111111111) {
    if ($LOG['modify_servers']==1) {
        $stmt = sprintf("SELECT max_osdial_trunks FROM servers WHERE server_ip='%s';",mres($server_ip));
        $rslt=mysql_query($stmt, $link);
        $rowx=mysql_fetch_row($rslt);
        $MAXosdial_trunks = $rowx[0];

        $stmt = sprintf("SELECT sum(dedicated_trunks) FROM osdial_server_trunks WHERE server_ip='%s' AND campaign_id!='%s';",mres($server_ip),mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $rowx=mysql_fetch_row($rslt);
        $SUMosdial_trunks = ($rowx[0] + $dedicated_trunks);

        if ($SUMosdial_trunks > $MAXosdial_trunks) {
            echo "<br><font color=red>SERVER TRUNK RECORD NOT ADDED - the number of osdial trunks is too high: $SUMosdial_trunks / $MAXosdial_trunks</font>\n";
        } else {
            $stmt = sprintf("SELECT count(*) FROM osdial_server_trunks WHERE campaign_id='%s' AND server_ip='%s';",mres($campaign_id),mres($server_ip));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0) {
                echo "<br><font color=red>SERVER TRUNK RECORD NOT ADDED - there is already a server-trunk record for this campaign</font>\n";
            } else {
                if (strlen($campaign_id) < 2 or strlen($server_ip) < 7 or strlen($dedicated_trunks) < 1 or strlen($trunk_restriction) < 1) {
                    echo "<br>SERVER TRUNK RECORD NOT ADDED - Please go back and look at the data you entered\n";
                    echo "<br>campaign must be between 3 and 8 characters in length\n";
                    echo "<br>server_ip delay must be at least 7 characters\n";
                    echo "<br>trunks must be a digit from 0 to 9999<br>\n";
                } else {
                    echo "<br><B><font color=$default_text>SERVER TRUNK RECORD ADDED: $campaign_id - $server_ip - $dedicated_trunks - $trunk_restriction</font></B>\n";

                    $stmt=sprintf("INSERT INTO osdial_server_trunks (server_ip,campaign_id,dedicated_trunks,trunk_restriction) VALUES ('%s','%s','%s','%s');",
                        mres($server_ip),mres($campaign_id),mres($dedicated_trunks),mres($trunk_restriction));
                    $rslt=mysql_query($stmt, $link);

                    ### LOG CHANGES TO LOG FILE ###
                    if ($WeBRooTWritablE > 0) {
                        $fp = fopen ("./admin_changes_log.txt", "a");
                        fwrite ($fp, "$date|ADD A NEW TRUNK  |$PHP_AUTH_USER|$ip|$stmt|\n");
                        fclose($fp);
                    }
                }
            }
        }
        $ADD=311111111111;
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=411111111111 modify server record in the system
######################
if ($ADD==411111111111) {
    if ($LOG['modify_servers']==1) {
        $stmt = sprintf("SELECT count(*) FROM servers WHERE server_id='%s';",mres($server_id));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        if ($row[0] > 0 && $server_id != $old_server_id) {
            echo "<br><font color=red>SERVER NOT MODIFIED - there is already a server in the system with this server_id</font>\n";
        } else {
            $stmt = sprintf("SELECT count(*) FROM servers WHERE server_ip='%s';",mres($server_ip));
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            if ($row[0] > 0 && $server_ip != $old_server_ip) {
                echo "<br><font color=red>SERVER NOT MODIFIED - there is already a server in the system with this server_ip</font>\n";
            } else {
                if (strlen($server_id) < 1 or strlen($server_ip) < 7) {
                    echo "<br><font color=red>SERVER NOT MODIFIED - Please go back and look at the data you entered</font>\n";
                } else {
                    if (preg_match('/CONTROL|SQL|WEB|ARCHIVE|OTHER/',$server_profile)) {
                        $asterisk_version='';
                        $max_osdial_trunks='0';
                        $osdial_balance_active='N';
                    }
                    echo "<br><font color=$default_text>SERVER MODIFIED: $server_ip</font>\n";
                    $stmt = sprintf("UPDATE servers SET server_id='%s',server_description='%s',server_ip='%s',active='%s',asterisk_version='%s',max_osdial_trunks='%s',telnet_host='%s',".
                        "telnet_port='%s',ASTmgrUSERNAME='%s',ASTmgrSECRET='%s',ASTmgrUSERNAMEupdate='%s',ASTmgrUSERNAMElisten='%s',ASTmgrUSERNAMEsend='%s',local_gmt='%s',".
                        "voicemail_dump_exten='%s',answer_transfer_agent='%s',ext_context='%s',sys_perf_log='%s',vd_server_logs='%s',agi_output='%s',osdial_balance_active='%s',".
                        "balance_trunks_offlimits='%s',server_profile='%s' WHERE server_id='%s';",
                        mres($server_id),mres($server_description),mres($server_ip),mres($active),mres($asterisk_version),mres($max_osdial_trunks),mres($telnet_host),
                        mres($telnet_port),mres($ASTmgrUSERNAME),mres($ASTmgrSECRET),mres($ASTmgrUSERNAMEupdate),mres($ASTmgrUSERNAMElisten),mres($ASTmgrUSERNAMEsend),mres($local_gmt),
                        mres($voicemail_dump_exten),mres($answer_transfer_agent),mres($ext_context),mres($sys_perf_log),mres($vd_server_logs),mres($agi_output),mres($osdial_balance_active),
                        mres($balance_trunks_offlimits),mres($server_profile),mres($old_server_id));

                    $rslt=mysql_query($stmt, $link);

                    if ($server_ip != $old_server_ip) {
                        function update_dep_server_ip($tbl) {
                            global $link;
                            global $old_server_ip;
                            global $server_ip;
                            $stmt = sprintf("SELECT count(*) FROM %s WHERE server_ip='%s';",$tbl,mres($old_server_ip));
                            $rslt=mysql_query($stmt, $link);
                            $row=mysql_fetch_row($rslt);
                            if ($row[0] > 0) {
                                echo "<br><font color=$default_text>UPDATE TABLE " . strtoupper($tbl) . ": $old_server_ip -&gt; $server_ip</font>\n";
                                $stmt = sprintf("UPDATE %s SET server_ip='%s' WHERE server_ip='%s';",$tbl,mres($server_ip),mres($old_server_ip));
                                $rslt=mysql_query($stmt, $link);
                            }
                        }

                        update_dep_server_ip('phones');
                        update_dep_server_ip('inbound_numbers');
                        update_dep_server_ip('server_updater');
                        update_dep_server_ip('conferences');
                        update_dep_server_ip('osdial_conferences');
                        update_dep_server_ip('osdial_remote_agents');
                        update_dep_server_ip('osdial_server_trunks');
                        update_dep_server_ip('osdial_auto_calls');
                        update_dep_server_ip('live_channels');
                        update_dep_server_ip('live_inbound');
                        update_dep_server_ip('live_sip_channels');
                        update_dep_server_ip('osdial_campaign_server_stats');
                        update_dep_server_ip('osdial_carriers');
                        update_dep_server_ip('osdial_manager');
                        update_dep_server_ip('parked_channels');
                        update_dep_server_ip('web_client_sessions');
                    }
                }
            }
        }
        $ADD=311111111111;# go to server modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=421111111111 modify osdial server trunks record in the system
######################
if ($ADD==421111111111) {
    if ($LOG['modify_servers']==1) {
        $stmt = sprintf("SELECT max_osdial_trunks FROM servers WHERE server_ip='%s';",mres($server_ip));
        $rslt=mysql_query($stmt, $link);
        $rowx=mysql_fetch_row($rslt);
        $MAXosdial_trunks = $rowx[0];

        $stmt = sprintf("SELECT sum(dedicated_trunks) FROM osdial_server_trunks WHERE server_ip='%s' AND campaign_id!='%s';",mres($server_ip),mres($campaign_id));
        $rslt=mysql_query($stmt, $link);
        $rowx=mysql_fetch_row($rslt);
        $SUMosdial_trunks = ($rowx[0] + $dedicated_trunks);

        if ($SUMosdial_trunks > $MAXosdial_trunks) {
            echo "<br><font color=red>SERVER TRUNK RECORD NOT ADDED - the number of trunks is too high: $SUMosdial_trunks / $MAXosdial_trunks</font>\n";
        } else {
            if (strlen($campaign_id) < 2 or strlen($server_ip) < 7 or strlen($dedicated_trunks) < 1 or strlen($trunk_restriction) < 1) {
                echo "<br><font color=red>SERVER TRUNK RECORD NOT MODIFIED - Please go back and look at the data you entered\n";
                echo "<br>campaign must be between 3 and 8 characters in length\n";
                echo "<br>server_ip delay must be at least 7 characters\n";
                echo "<br>trunks must be a digit from 0 to 9999</font><br>\n";
            } else {
                echo "<br><B><font color=$default_text>SERVER TRUNK RECORD MODIFIED: $campaign_id - $server_ip - $dedicated_trunks - $trunk_restriction</font></B>\n";
                $stmt = sprintf("UPDATE osdial_server_trunks SET dedicated_trunks='%s',trunk_restriction='%s' WHERE campaign_id='%s' AND server_ip='%s';",
                    mres($dedicated_trunks),mres($trunk_restriction),mres($campaign_id),mres($server_ip));
                $rslt=mysql_query($stmt, $link);

                ### LOG CHANGES TO LOG FILE ###
                if ($WeBRooTWritablE > 0) {
                    $fp = fopen ("./admin_changes_log.txt", "a");
                    fwrite ($fp, "$date|MODIFY SERVER TRUNK   |$PHP_AUTH_USER|$ip|$stmt|\n");
                    fclose($fp);
                }
            }
        }
        $ADD=311111111111;# go to server modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=511111111111 confirmation before deletion of server record
######################
if ($ADD==511111111111) {
    if ($LOG['modify_servers']==1) {
        if (strlen($server_id) < 2 or strlen($server_ip) < 7 or $LOG['ast_delete_phones'] < 1) {
            echo "<br><font color=red>SERVER NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>Server ID be at least 2 characters in length\n";
            echo "<br>Server IP be at least 7 characters in length</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>SERVER DELETION CONFIRMATION: $server_id - $server_ip</B>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=611111111111&server_id=$server_id&server_ip=$server_ip&CoNfIrM=YES\">Click here to delete phone $server_id - $server_ip</a></font><br><br><br>\n";
        }
        $ADD='311111111111';# go to server modification below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=611111111111 delete server record
######################
if ($ADD==611111111111) {
    if ($LOG['modify_servers']==1) {
        if (strlen($server_id) < 2 or strlen($server_ip) < 7 or $CoNfIrM != 'YES' or $LOG['ast_delete_phones'] < 1) {
            echo "<br><font color=red>SERVER NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>Server ID be at least 2 characters in length\n";
            echo "<br>Server IP be at least 7 characters in length</font><br>\n";
        } else {
            $stmt="DELETE FROM servers WHERE server_id='$server_id' AND server_ip='$server_ip' LIMIT 1;";
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|!!!DELETING SERVER!!|$PHP_AUTH_USER|$ip|server_id='$server_id'|server_ip='$server_ip'|\n");
                fclose($fp);
            }
            echo "<br><B><font color=$default_text>SERVER DELETION COMPLETED: $server_id - $server_ip</font></B>\n";
            echo "<br><br>\n";
        }
        $ADD='100000000000';# go to server list
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=621111111111 delete osdial server trunk record in the system
######################
if ($ADD==621111111111) {
    if ($LOG['modify_servers']==1) {
        if (strlen($campaign_id) < 2 or strlen($server_ip) < 7) {
            echo "<br><font color=red>SERVER TRUNK RECORD NOT DELETED - Please go back and look at the data you entered\n";
            echo "<br>campaign must be between 3 and 8 characters in length\n";
            echo "<br>server_ip delay must be at least 7 characters</font><br>\n";
        } else {
            echo "<br><B><font color=$default_text>SERVER TRUNK RECORD DELETED: $campaign_id - $server_ip</font></B>\n";

            $stmt="DELETE FROM osdial_server_trunks WHERE campaign_id='$campaign_id' AND server_ip='$server_ip';";
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|DELETE SERVER TRUNK   |$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        $ADD=311111111111;# go to server modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=311111111111 modify server record in the system
######################
if ($ADD==311111111111) {
    if ($LOG['modify_servers']==1) {
        $stmt = sprintf("SELECT * FROM servers WHERE server_id='%s' OR server_ip='%s';",mres($server_id),mres($server_ip));
        $rslt=mysql_query($stmt, $link);
        $row=mysql_fetch_row($rslt);
        $server_id = $row[0];
        $server_ip = $row[2];

        echo "<center><br><font color=$default_text size=+1>MODIFY A SERVER</font><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=411111111111>\n";
        echo "<input type=hidden name=old_server_id value=\"$server_id\">\n";
        echo "<input type=hidden name=old_server_ip value=\"$row[2]\">\n";
        echo "<TABLE width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server ID: </td><td align=left><input type=text name=server_id size=10 maxlength=10 value=\"$row[0]\">$NWB#servers-server_id$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server Description: </td><td align=left><input type=text name=server_description size=30 maxlength=255 value=\"$row[1]\">$NWB#servers-server_description$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server IP Address: </td><td align=left><input type=text name=server_ip size=20 maxlength=15 value=\"$row[2]\">$NWB#servers-server_ip$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server Profile: </td><td align=left><select size=1 name=server_profile><option>AIO</option><option>CONTROL</option><option>SQL</option><option>WEB</option><option>DIALER</option><option>ARCHIVE</option><option>OTHER</option><option selected>$row[22]</option></select>$NWB#servers-server_profile$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=active><option>Y</option><option>N</option><option selected>$row[3]</option></select>$NWB#servers-active$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$row[13]</option></select> (Do NOT Adjust for DST)$NWB#servers-local_gmt$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>System Performance: </td><td align=left><select size=1 name=sys_perf_log><option>Y</option><option>N</option><option selected>$row[17]</option></select>$NWB#servers-sys_perf_log$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Server Logs: </td><td align=left><select size=1 name=vd_server_logs><option>Y</option><option>N</option><option selected>$row[18]</option></select>$NWB#servers-vd_server_logs$NWE</td></tr>\n";
        if (preg_match('/CONTROL|SQL|WEB|ARCHIVE|OTHER/',$row[22])) {
            echo "<input type=hidden name=asterisk_version value=\"$row[4]\">\n";
            echo "<input type=hidden name=max_osdial_trunks value=\"$row[5]\">\n";
            echo "<input type=hidden name=osdial_balance_active value=\"$row[20]\">\n";
            echo "<input type=hidden name=balance_trunks_offlimits value=\"$row[21]\">\n";
            echo "<input type=hidden name=agi_output value=\"$row[19]\">\n";
            echo "<input type=hidden name=ext_context value=\"$row[16]\">\n";
            echo "<input type=hidden name=telnet_host value=\"$row[6]\">\n";
            echo "<input type=hidden name=telnet_port value=\"$row[7]\">\n";
            echo "<input type=hidden name=ASTmgrUSERNAME value=\"$row[8]\">\n";
            echo "<input type=hidden name=ASTmgrSECRET value=\"$row[9]\">\n";
            echo "<input type=hidden name=ASTmgrUSERNAMEupdate value=\"$row[10]\">\n";
            echo "<input type=hidden name=ASTmgrUSERNAMElisten value=\"$row[11]\">\n";
            echo "<input type=hidden name=ASTmgrUSERNAMEsend value=\"$row[12]\">\n";
            echo "<input type=hidden name=voicemail_dump_exten value=\"$row[14]\">\n";
            echo "<input type=hidden name=answer_transfer_agent value=\"$row[15]\">\n";
        } else {
            echo "<tr bgcolor=$oddrows><td align=right>Asterisk Version: </td><td align=left><input type=text name=asterisk_version size=20 maxlength=20 value=\"$row[4]\">$NWB#servers-asterisk_version$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Max Trunks: </td><td align=left><input type=text name=max_osdial_trunks size=5 maxlength=4 value=\"$row[5]\">$NWB#servers-max_osdial_trunks$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Balance Dialing: </td><td align=left><select size=1 name=osdial_balance_active><option>Y</option><option>N</option><option selected>$row[20]</option></select>$NWB#servers-osdial_balance_active$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Balance Offlimits: </td><td align=left><input type=text name=balance_trunks_offlimits size=5 maxlength=4 value=\"$row[21]\">$NWB#servers-balance_trunks_offlimits$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>AGI Log Output: </td><td align=left><select size=1 name=agi_output><option>NONE</option><option>STDERR</option><option>FILE</option><option>BOTH</option><option selected>$row[19]</option></select>$NWB#servers-agi_output$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Default Context: </td><td align=left><input type=text name=ext_context size=20 maxlength=20 value=\"$row[16]\">$NWB#servers-ext_context$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Telnet Host: </td><td align=left><input type=text name=telnet_host size=20 maxlength=20 value=\"$row[6]\">$NWB#servers-telnet_host$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Telnet Port: </td><td align=left><input type=text name=telnet_port size=6 maxlength=5 value=\"$row[7]\">$NWB#servers-telnet_port$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Manager User: </td><td align=left><input type=text name=ASTmgrUSERNAME size=20 maxlength=20 value=\"$row[8]\">$NWB#servers-ASTmgrUSERNAME$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Manager Secret: </td><td align=left><input type=text name=ASTmgrSECRET size=20 maxlength=20 value=\"$row[9]\">$NWB#servers-ASTmgrSECRET$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Manager Update User: </td><td align=left><input type=text name=ASTmgrUSERNAMEupdate size=20 maxlength=20 value=\"$row[10]\">$NWB#servers-ASTmgrUSERNAMEupdate$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Manager Listen User: </td><td align=left><input type=text name=ASTmgrUSERNAMElisten size=20 maxlength=20 value=\"$row[11]\">$NWB#servers-ASTmgrUSERNAMElisten$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Manager Send User: </td><td align=left><input type=text name=ASTmgrUSERNAMEsend size=20 maxlength=20 value=\"$row[12]\">$NWB#servers-ASTmgrUSERNAMEsend$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>VMail Dump Exten: </td><td align=left><input type=text name=voicemail_dump_exten size=20 maxlength=20 value=\"$row[14]\">$NWB#servers-voicemail_dump_exten$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>AD extension: </td><td align=left><input type=text name=answer_transfer_agent size=20 maxlength=20 value=\"$row[15]\">$NWB#servers-answer_transfer_agent$NWE</td></tr>\n";
        }
        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center></form>\n";


        if ($row[22] == 'AIO' or $row[22] == 'DIALER') {
            ### osdial server trunk records for this server
            echo "<br><br><center><font color=$default_text size=+1>TRUNKS FOR THIS SERVER &nbsp;</font> $NWB#osdial_server_trunks$NWE<br>\n";
            echo "<table width=600 cellspacing=1 bgcolor=grey>\n";
            echo "  <tr class=tabheader>\n";
            echo "    <td align=center>TRUNKS</td>\n";
            echo "    <td align=center>CAMPAIGN</td>\n";
            echo "    <td align=center>RESTRICTION</td>\n";
            echo "    <td align=center colspan=2>ACTIONS</td>\n";
            echo "  </tr>\n";

            $stmt = sprintf("SELECT * FROM osdial_server_trunks WHERE server_ip='%s' ORDER BY campaign_id;",mres($server_ip));
            $rslt=mysql_query($stmt, $link);
            $recycle_to_print = mysql_num_rows($rslt);
            $o=0;
            while ($recycle_to_print > $o) {
                $rowx=mysql_fetch_row($rslt);
                $o++;

                echo "  <form action=$PHP_SELF method=POST>\n";
                echo "  <input type=hidden name=server_ip value=\"$server_ip\">\n";
                echo "  <input type=hidden name=campaign_id value=\"$rowx[1]\">\n";
                echo "  <input type=hidden name=ADD value=421111111111>\n";
                echo "  <tr " . bgcolor($o) ." class=\"row font1\">\n";
                echo "    <td align=center class=tabinput><input size=6 maxlength=4 name=dedicated_trunks value=\"$rowx[2]\"></td>\n";
                echo "    <td align=center>$rowx[1]</td>";
                echo "    <td align=center class=tabinput><select size=1 name=trunk_restriction><option>MAXIMUM_LIMIT</option><option>OVERFLOW_ALLOWED</option><option SELECTED>$rowx[3]</option></select></td>\n";
                echo "    <td align=center><a href=\"$PHP_SELF?ADD=621111111111&campaign_id=$rowx[1]&server_ip=$server_ip\">DELETE</a></td>\n";
                echo "    <td align=center class=tabbutton1><input type=submit name=submit value=MODIFY></td>\n";
                echo "  </tr>\n";
                echo "  </form>\n";
            }

            echo "  <form action=$PHP_SELF method=POST>\n";
            echo "  <input type=hidden name=ADD value=221111111111>\n";
            echo "  <input type=hidden name=server_ip value=\"$server_ip\">\n";
            echo "  <tr class=tabfooter>\n";
            echo "    <td align=center class=tabinput><input size=6 maxlength=4 name=dedicated_trunks></td>\n";
            echo "    <td align=center class=tabinput><select size=1 name=campaign_id>$campaigns_list</select></td>\n";
            echo "    <td align=center class=tabinput><select size=1 name=trunk_restriction><option>MAXIMUM_LIMIT</option><option>OVERFLOW_ALLOWED</option></select></td>\n";
            echo "    <td align=center colspan=2 class=tabcutton1><input type=submit name=submit value=ADD></td>\n";
            echo "  </tr>\n";
            echo "  </form>\n";
            echo "</table>\n";
            echo "</font></center><br><br>\n";


            ### list of phones on this server
            echo "<center>\n";
            echo "<br><font color=$default_text>PHONES WITHIN THIS SERVER</font><br>\n";
            echo "<table width=400 cellspacing=1 bgcolor=grey>\n";
            echo "  <tr class=tabheader>\n";
            echo "    <td align=center>EXTENSION</td>\n";
            echo "    <td align=center>NAME</td>\n";
            echo "    <td align=center>ACTIVE</td>\n";
            echo "  </tr>\n";

            $active_phones = 0;
            $inactive_phones = 0;
            $stmt = sprintf("SELECT extension,active,fullname FROM phones WHERE server_ip='%s';",mres($row[2]));
            $rsltx=mysql_query($stmt, $link);
            $lists_to_print = mysql_num_rows($rsltx);
            $camp_lists='';

            $o=0;
            while ($lists_to_print > $o) {
                $rowx=mysql_fetch_row($rsltx);
                $o++;
                if (preg_match("/Y/", $rowx[1])) {
                    $active_phones++;
                    $camp_lists .= "'$rowx[0]',";
                }
                if (preg_match("/N/", $rowx[1])) {
                    $inactive_phones++;
                }

                echo "  <tr " . bgcolor($o) ." class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=31111111111&extension=$rowx[0]&server_ip=$row[2]');\">\n";
                echo "    <td align=center><a href=\"$PHP_SELF?ADD=31111111111&extension=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td>\n";
                echo "    <td align=center>$rowx[2]</td>\n";
                echo "    <td align=center>$rowx[1]</td>\n";
                echo "  </tr>\n";
            }

            echo "  <tr class=tabfooter>\n";
            echo "    <td colspan=3></td>\n";
            echo "  </tr>\n";
            echo "</table></font></center><br>\n";


            ### list of conferences on this server
            echo "<center>\n";
            echo "<br><br><font color=$default_text>CONFERENCES WITHIN THIS SERVER</font><br>\n";
            echo "<table width=400 cellspacing=1 bgcolor=grey>\n";
            echo "  <tr class=tabheader>\n";
            echo "    <td align=center>CONFERENCE</td>\n";
            echo "    <td align=center>EXTENSION</td>\n";
            echo "  </tr>\n";

            $active_confs = 0;
            $stmt = sprintf("SELECT conf_exten,extension FROM conferences WHERE server_ip='%s';",mres($row[2]));
            $rsltx=mysql_query($stmt, $link);
            $lists_to_print = mysql_num_rows($rsltx);
            $camp_lists='';

            $o=0;
            while ($lists_to_print > $o) {
                $rowx=mysql_fetch_row($rsltx);
                $o++;
                $active_confs++;

                echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=3111111111111&conf_exten=$rowx[0]&server_ip=$row[2]');\">\n";
                echo "    <td align=center><a href=\"$PHP_SELF?ADD=3111111111111&conf_exten=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td>\n";
                echo "    <td align=center>$rowx[2]</td>\n";
                echo "  </tr>\n";
            }

            echo "  <tr class=tabfooter>\n";
            echo "    <td colspan=2></td>\n";
            echo "  </tr>\n";
            echo "</table></font></center><br>\n";


            ### list of osdial conferences on this server
            echo "<center>\n";
            echo "<br><br><font color=$default_text>$t1 CONFERENCES WITHIN THIS SERVER<br>\n";
            echo "<table width=400 cellspacing=1 bgcolor=grey>\n";
            echo "  <tr class=tabheader>\n";
            echo "    <td align=center>$t1 CONFERENCE</td>\n";
            echo "    <td align=center>EXTENSION</td>\n";
            echo "  </tr>\n";

            $active_vdconfs = 0;
            $stmt = sprintf("SELECT conf_exten,extension FROM osdial_conferences WHERE server_ip='%s';",mres($row[2]));
            $rsltx=mysql_query($stmt, $link);
            $lists_to_print = mysql_num_rows($rsltx);
            $camp_lists='';

            $o=0;
            while ($lists_to_print > $o) {
                $rowx=mysql_fetch_row($rsltx);
                $o++;
                $active_vdconfs++;

                echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"openNewWindow('$PHP_SELF?ADD=31111111111111&conf_exten=$rowx[0]&server_ip=$row[2]');\">\n";
                echo "    <td align=center><a href=\"$PHP_SELF?ADD=31111111111111&conf_exten=$rowx[0]&server_ip=$row[2]\">$rowx[0]</a></td>\n";
                echo "    <td align=center>$rowx[2]</td>\n";
                echo "  </tr>\n";
            }

            echo "  <tr class=tabfooter>\n";
            echo "    <td colspan=2></td>\n";
            echo "  </tr>\n";
            echo "</table></font></center><br>\n";


            echo "<center><b>\n";

            $camp_lists = preg_replace("/.$/","",$camp_lists);
            echo "<font size=2 color=$default_text>This server has $active_phones active phones and $inactive_phones inactive phones<br><br>\n";
            echo "This server has $active_confs active conferences<br><br>\n";
            echo "This server has $active_vdconfs active $t1 conferences</font><br><br>\n";
            echo "</b></center>\n";
        }

        if ($LOG['ast_delete_phones'] > 0) {
            echo "<br><br><a href=\"$PHP_SELF?ADD=511111111111&server_id=$server_id&server_ip=$server_ip\">DELETE THIS SERVER</a>\n";
        }

    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=100000000000 display all servers
######################
if ($ADD==100000000000) {
    $stmt = "SELECT * FROM servers ORDER BY server_id;";
    $rslt=mysql_query($stmt, $link);
    $phones_to_print = mysql_num_rows($rslt);

    echo "<center><br><font color=$default_text size=+1>SERVERS</font><br><br>\n";
    echo "<table width=$section_width cellspacing=0 cellpadding=1>\n";
    echo "  <tr class=tabheader>\n";
    echo "    <td>ID</td>\n";
    echo "    <td>DESCRIPTION</td>\n";
    echo "    <td>SERVER</td>\n";
    echo "    <td>ASTERISK</td>\n";
    echo "    <td align=center>ACTIVE</td>\n";
    echo "    <td align=center colspan=2>LINKS</td>\n";
    echo "  </tr>\n";

    $o=0;
    while ($phones_to_print > $o) {
        $row=mysql_fetch_row($rslt);
        echo "  <tr " . bgcolor($o) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=311111111111&server_id=$row[0]';\">\n";
        echo "    <td><a href=\"$PHP_SELF?ADD=311111111111&server_id=$row[0]\">$row[0]</a></td>\n";
        echo "    <td>$row[1]</td>\n";
        echo "    <td>$row[2]</td>\n";
        echo "    <td>$row[4]</td>\n";
        echo "    <td align=center>$row[3]</td>\n";
        echo "    <td colspan=2 align=center>\n";
        echo "      <a href=\"/sysinfo/$row[2]/psi\" target=\"_new\">SYSINFO</a>&nbsp;|&nbsp;\n";
        echo "      <a href=\"$PHP_SELF?ADD=311111111111&server_id=$row[0]\">MODIFY</a>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        $o++;
    }

    echo "  <tr class=tabfooter>\n";
    echo "    <td colspan=7></td>\n";
    echo "  </tr>\n";
    echo "</TABLE></center>\n";
}



#### Archive Server

######################
# ADD=499111111111111 modify archive serversettings
######################
if ($ADD==499111111111111) {
    if ($LOG['modify_servers']==1) {
        echo "<br>ARCHIVE SERVER MODIFIED\n";

        if ($archive_transfer_method == "FTP" and $archive_port == "") {
            $archive_port = "21";
        } elseif ($archive_transfer_method == "FTPA" and $archive_port == "") {
            $archive_port = "21";
        } elseif ($archive_transfer_method == "SFTP" and $archive_port == "") {
            $archive_port = "22";
        } elseif ($archive_transfer_method == "SCP" and $archive_port == "") {
            $archive_port = "22";
        }

        $stmt1 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchiveHostname';",mres($archive_hostname));
        $stmt2 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchiveTransferMethod';",mres($archive_transfer_method));
        $stmt3 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchivePort';",mres($archive_port));
        $stmt4 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchiveUsername';",mres($archive_username));
        $stmt5 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchivePassword';",mres($archive_password));
        $stmt6 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchivePath';",mres($archive_path));
        $stmt7 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchiveReportPath';",mres($archive_report_path));
        $stmt8 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchiveWebPath';",mres($archive_web_path));
        $stmt9 = sprintf("UPDATE configuration SET data='%s' WHERE name='ArchiveMixFormat';",mres($archive_mix_format));

        $rslt = mysql_query($stmt1, $link);
        $rslt = mysql_query($stmt2, $link);
        $rslt = mysql_query($stmt3, $link);
        $rslt = mysql_query($stmt4, $link);
        $rslt = mysql_query($stmt5, $link);
        $rslt = mysql_query($stmt6, $link);
        $rslt = mysql_query($stmt7, $link);
        $rslt = mysql_query($stmt8, $link);
        $rslt = mysql_query($stmt9, $link);

    ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt1|\n");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt2|\n");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt3|\n");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt4|\n");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt5|\n");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt6|\n");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt7|\n");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt8|\n");
            fwrite ($fp, "$date|MODIFY ARCHIVE SERVER|$PHP_AUTH_USER|$ip|$stmt9|\n");
            fclose($fp);
        }
        $ADD=399111111111111;# go to osdial system settings form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=399111111111111 modify archive server settings
######################
if ($ADD=="399111111111111") {
    if ($LOG['modify_servers']==1) {
        $stmt = "SELECT name,data FROM configuration WHERE name LIKE 'Archive%';";
        $rslt = mysql_query($stmt, $link);
        $rows = mysql_num_rows($rslt);

        $c = 0;
        while ($rows > $c) {
            $row = mysql_fetch_row($rslt);
            if ($row[0] == "ArchiveHostname") {
                $archive_hostname = $row[1];
            } elseif ($row[0] == "ArchiveTransferMethod") {
                $archive_transfer_method = $row[1];
            } elseif ($row[0] == "ArchivePort") {
                $archive_port = $row[1];
            } elseif ($row[0] == "ArchiveUsername") {
                $archive_username = $row[1];
            } elseif ($row[0] == "ArchivePassword") {
                $archive_password = $row[1];
            } elseif ($row[0] == "ArchivePath") {
                $archive_path = $row[1];
            } elseif ($row[0] == "ArchiveReportPath") {
                $archive_report_path = $row[1];
            } elseif ($row[0] == "ArchiveWebPath") {
                $archive_web_path = $row[1];
            } elseif ($row[0] == "ArchiveMixFormat") {
                $archive_mix_format = $row[1];
            }
            $c++;
        }

        echo "<center><br><br><font color=$default_text size=+1>MODIFY ARCHIVE SERVER SETTINGS</font><br><form action=$PHP_SELF method=POST><br><br>\n";
        echo "<input type=hidden name=ADD value=499111111111111>\n";
        echo "<center><TABLE width=$section_width cellspacing=3>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Archive Server Address: </td><td align=left><input type=text name=archive_hostname size=30 maxlength=30 value=\"$archive_hostname\">$NWB#settings-archive_hostname$NWE</td></tr>\n";

        $atmsel = "<option selected>" . $archive_transfer_method . "</option>";
        if ($archive_transfer_method == "FTP") {
            $atmsel = "<option selected value=\"FTP\">FTP (passive)</option>";
        } elseif ($archive_transfer_method == "FTPA") {
            $atmsel = "<option selected value=\"FTPA\">FTP (active)</option>";
        }
        echo "<tr bgcolor=$oddrows><td align=right>Transfer Method: </td><td align=left><select size=1 name=archive_transfer_method><option value=\"FTP\">FTP (passive)</option><option value=\"FTPA\">FTP (active)</option><option>SFTP</option><option>SCP</option>$atmsel</select>$NWB#settings-archive_transfer_method$NWE</td></tr>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Port: </td><td align=left><input type=text name=archive_port size=6 maxlength=5 value=\"$archive_port\">$NWB#settings-archive_port$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Username: </td><td align=left><input type=text name=archive_username size=20 maxlength=20 value=\"$archive_username\">$NWB#settings-archive_username$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=archive_password size=20 maxlength=200 value=\"$archive_password\">$NWB#settings-archive_password$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Recording Path: </td><td align=left><input type=text name=archive_path size=40 maxlength=255 value=\"$archive_path\">$NWB#settings-archive_path$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Report Path: </td><td align=left><input type=text name=archive_report_path size=40 maxlength=255 value=\"$archive_report_path\">$NWB#settings-archive_report_path$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Web Path: </td><td align=left><input type=text name=archive_web_path size=40 maxlength=255 value=\"$archive_web_path\">$NWB#settings-archive_web_path$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Mix Format: </td><td align=left><select size=1 name=archive_mix_format><option value=\"\"> -[ DEFAULT ]- </option><option>MP3</option><option>WAV</option><option>GSM</option><option>OGG</option><option selected>$archive_mix_format</option></select>$NWB#settings-archive_mix_format$NWE</td></tr>\n";

        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";
        echo "</form>\n";
    } else {
        echo "You do not have permission to view this page\n";
    }
}



######################
# ADD=499911111111111 modify external dnc database settings
######################
if ($ADD==499911111111111) {
    if ($LOG['modify_servers']==1) {
        echo "<br>EXTERNAL DNC DATABASE MODIFIED\n";

        $stmt1 = sprintf("UPDATE configuration SET data='%s' WHERE name='External_DNC_Active';",mres($external_dnc_active));
        $stmt2 = sprintf("UPDATE configuration SET data='%s' WHERE name='External_DNC_Address';",mres($external_dnc_address));
        $stmt3 = sprintf("UPDATE configuration SET data='%s' WHERE name='External_DNC_Database';",mres($external_dnc_database));
        $stmt4 = sprintf("UPDATE configuration SET data='%s' WHERE name='External_DNC_Username';",mres($external_dnc_username));
        $stmt5 = sprintf("UPDATE configuration SET data='%s' WHERE name='External_DNC_Password';",mres($external_dnc_password));
        $stmt6 = sprintf("UPDATE configuration SET data='%s' WHERE name='External_DNC_SQL';",mres($external_dnc_sql));

        $rslt = mysql_query($stmt1, $link);
        $rslt = mysql_query($stmt2, $link);
        $rslt = mysql_query($stmt3, $link);
        $rslt = mysql_query($stmt4, $link);
        $rslt = mysql_query($stmt5, $link);
        $rslt = mysql_query($stmt6, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|MODIFY EXTERNAL DNC|$PHP_AUTH_USER|$ip|$stmt1|\n");
            fwrite ($fp, "$date|MODIFY EXTERNAL DNC|$PHP_AUTH_USER|$ip|$stmt2|\n");
            fwrite ($fp, "$date|MODIFY EXTERNAL DNC|$PHP_AUTH_USER|$ip|$stmt3|\n");
            fwrite ($fp, "$date|MODIFY EXTERNAL DNC|$PHP_AUTH_USER|$ip|$stmt4|\n");
            fwrite ($fp, "$date|MODIFY EXTERNAL DNC|$PHP_AUTH_USER|$ip|$stmt5|\n");
            fwrite ($fp, "$date|MODIFY EXTERNAL DNC|$PHP_AUTH_USER|$ip|$stmt6|\n");
            fclose($fp);
        }
        $ADD=399911111111111;# go to osdial dnc database settings form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=399911111111111 modify external dnc database settings
######################
if ($ADD=="399911111111111") {
    if ($LOG['modify_servers']==1) {
        $stmt = "SELECT name,data FROM configuration WHERE name LIKE 'External_DNC%';";
        $rslt = mysql_query($stmt, $link);
        $rows = mysql_num_rows($rslt);

        $c = 0;
        while ($rows > $c) {
            $row = mysql_fetch_row($rslt);
            if ($row[0] == "External_DNC_Active") {
                $external_dnc_active = $row[1];
            } elseif ($row[0] == "External_DNC_Address") {
                $external_dnc_address = $row[1];
            } elseif ($row[0] == "External_DNC_Database") {
                $external_dnc_database = $row[1];
            } elseif ($row[0] == "External_DNC_Username") {
                $external_dnc_username = $row[1];
            } elseif ($row[0] == "External_DNC_Password") {
                $external_dnc_password = $row[1];
            } elseif ($row[0] == "External_DNC_SQL") {
                $external_dnc_sql = $row[1];
            }
            $c++;
        }

        echo "<center><br><font color=$default_text size=+1>MODIFY DNC DATABASE SETTINGS</font><br><form action=$PHP_SELF method=POST>\n";
        echo "<input type=hidden name=ADD value=499911111111111>\n";
        echo "<center><TABLE width=$section_width cellspacing=3>\n";

        echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select name=external_dnc_active><option>Y</option><option>N</option><option selected>$external_dnc_active</option></select>$NWB#settings-external_dnc_active$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>DNC MySQL Address: </td><td align=left><input type=text name=external_dnc_address size=30 maxlength=30 value=\"$external_dnc_address\">$NWB#settings-external_dnc_address$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Database Name: </td><td align=left><input type=text name=external_dnc_database size=20 maxlength=20 value=\"$external_dnc_database\">$NWB#settings-external_dnc_database$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Username: </td><td align=left><input type=text name=external_dnc_username size=20 maxlength=20 value=\"$external_dnc_username\">$NWB#settings-external_dnc_username$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=external_dnc_password size=20 maxlength=200 value=\"$external_dnc_password\">$NWB#settings-external_dnc_password$NWE</td></tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>SQL: </td><td align=left><input type=text name=external_dnc_sql size=40 maxlength=255 value=\"$external_dnc_sql\">$NWB#settings-external_dnc_sql$NWE</td></tr>\n";


        echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
        echo "</TABLE></center>\n";
        echo "</form>\n";
    } else {
        echo "You do not have permission to view this page\n";
    }
}



#### QC Servers

######################
# ADD=499211111111111 modify qc serversettings
######################
if ($ADD==499211111111111) {
    if ($LOG['modify_servers']==1) {

        if (($qc_server_transfer_type == "BATCH" or $qc_server_transfer_type == "ARCHIVE") and $qc_server_batch_time == "0") $qc_server_batch_time="23";
        if ($qc_server_transfer_type == "ARCHIVE" and $qc_server_archive == "NONE") $qc_server_archive="ZIP";
        if ($qc_server_transfer_type == "IMMEDIATE" or $qc_server_transfer_type == "BATCH") $qc_server_archive="NONE";
        if ($qc_server_transfer_type == "IMMEDIATE") $qc_server_batch_time="0";

        if ($SUB==1) {
            $qcact = "ADD";
            echo "<br>QC SERVER ADDED\n";
            $stmt = sprintf("INSERT INTO qc_servers (name,description,transfer_method,host,transfer_type,batch_time,".
                "username,password,home_path,location_template,archive,active) ".
                "VALUES ('%s','%s','%s','%s','%s','%s',".
                "'%s','%s','%s','%s','%s','%s');",
                mres($qc_server_name),mres($qc_server_description),mres($qc_server_transfer_method),mres($qc_server_host),mres($qc_server_transfer_type),mres($qc_server_batch_time),
                mres($qc_server_username),mres($qc_server_password),mres($qc_server_home_path),mres($qc_server_location_template),mres($qc_server_archive),mres($qc_server_active));

        } elseif ($SUB==2) {
            $qcact = "MODIFIED";
            echo "<br>QC SERVER MODIFIED\n";
            $stmt = sprintf("UPDATE qc_servers SET name='%s',description='%s',transfer_method='%s',host='%s',transfer_type='%s',batch_time='%s',".
                "username='%s',password='%s',home_path='%s',location_template='%s',archive='%s',active='%s' ".
                "WHERE id='%s';",
                mres($qc_server_name),mres($qc_server_description),mres($qc_server_transfer_method),mres($qc_server_host),mres($qc_server_transfer_type),mres($qc_server_batch_time),
                mres($qc_server_username),mres($qc_server_password),mres($qc_server_home_path),mres($qc_server_location_template),mres($qc_server_archive),mres($qc_server_active),
                mres($qc_server_id));

        } elseif ($SUB==3) {
            $qcact = "ADD RULE";
            echo "<br>QC SERVER RULE MODIFIED\n";
            $stmt = sprintf("INSERT INTO qc_server_rules (qc_server_id,query) VALUES ('%s','%s');",mres($qc_server_id),mres($qc_server_rule_query));

        } elseif ($SUB==4) {
            $qcact = "MODIFIED RULE";
            echo "<br>QC SERVER RULE MODIFIED\n";
            $stmt = sprintf("UPDATE qc_server_rules SET query='%s' WHERE id='%s';",mres($qc_server_rule_query),mres($qc_server_rule_id));
            $qc_server_rule_query = "";
            $SUB=2;
        }

        $rslt = mysql_query($stmt, $link);

        if ($SUB==1) {
            $stmt = "SELECT id FROM qc_servers ORDER BY id DESC LIMIT 1;";
            $rslt=mysql_query($stmt, $link);
            $row=mysql_fetch_row($rslt);
            $qc_server_id=$row[0];
            $SUB++;
        }

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|$qcact QC SERVER|$PHP_AUTH_USER|$ip|$stmt|\n");
            fclose($fp);
        }

        $ADD=399211111111111;# go to osdial system settings form below

    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=699211111111111 delete qc server and sql records.
######################
if ($ADD==699211111111111){
    if ($LOG['modify_servers']==1) {
        if ($SUB==2) {
            # Delete rule entries
            $stmt="DELETE FROM qc_server_rules WHERE qc_server_id='$qc_server_id';";
            $rslt=mysql_query($stmt, $link);
        
            # Delete server entry
            $stmt="DELETE FROM qc_servers WHERE id='$qc_server_id';";
            $rslt=mysql_query($stmt, $link);
            $nQSI='';
            $nSUB='';
        } elseif ($SUB==4) {
            # Delete rule entry
            $stmt="DELETE FROM qc_server_rules WHERE id='$qc_server_rule_id';";
            $rslt=mysql_query($stmt, $link);
            $nQSI=$qc_server_id;
            $nSUB=2;
        }

        ### LOG CHANGES TO LOG FILE ###
        if ($SUB > 0 and $WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|!!!DELETING QC!!!!|$PHP_AUTH_USER|$ip|SUB=$SUB|qc_server_id='$qc_server_id'|qc_server_rule_id='$qc_server_rule_id'|\n");
            fclose($fp);
        }
        echo "<br><B>QC DELETION COMPLETED: $qc_server_id - $qc_server_rule_id</B>\n";
        echo "<br><br>\n";

        $SUB=$nSUB;
        $qc_server_id=$nQSI;
        $qc_server_rule_id='';
        $ADD='399211111111111';# go to osdial conference list
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=399211111111111 modify QC server settings
######################
if ($ADD=="399211111111111") {
    if ($LOG['modify_servers']==1) {
        $stmt = "SELECT id,name,description,host,transfer_method,transfer_type FROM qc_servers;";
        $rslt = mysql_query($stmt, $link);
        $rows = mysql_num_rows($rslt);

        echo "<center><br><font color=$default_text size=+1>QC SERVER LIST</font><br><br>\n";
        echo "<form action=$PHP_SELF method=POST>\n";
        echo "<input type=hidden name=ADD value=399211111111111>\n";
        echo "<input type=hidden name=SUB value=1>\n";
        echo "<center><table bgcolor=grey width=$section_width cellspacing=1>\n";
        echo "  <tr class=tabheader>\n";
        echo "    <td align=center>#</td>\n";
        echo "    <td align=center>NAME</td>\n";
        echo "    <td align=center>DESCRIPTION</td>\n";
        echo "    <td align=center>HOST</td>\n";
        echo "    <td align=center>METHOD</td>\n";
        echo "    <td align=center>TYPE</td>\n";
        echo "    <td colspan=2>&nbsp;</td>\n";
        echo "  </tr>\n";
                $c = 0;
        while ($rows > $c) {
            $row = mysql_fetch_row($rslt);

            echo "  <tr " . bgcolor($c) . " class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=399211111111111&SUB=2&qc_server_id=$row[0]';\">\n";
            echo "    <td>$c</td>\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=399211111111111&SUB=2&qc_server_id=$row[0]\">$row[1]</a></td>\n";
            echo "    <td>$row[2]</td>\n";
            echo "    <td>$row[3]</td>\n";
            echo "    <td>$row[4]</td>\n";
            echo "    <td>$row[5]</td>\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=399211111111111&SUB=2&qc_server_id=$row[0]\">MODIFY</a></td>\n";
            echo "    <td><a href=\"$PHP_SELF?ADD=699211111111111&SUB=2&qc_server_id=$row[0]\">REMOVE</a></td>\n";
            echo "  </tr>\n";

            $c++;
        }
        echo "  <tr class=tabfooter><td align=center colspan=8 class=tabbutton><input type=submit name=submit VALUE=NEW></td></tr>\n";
        echo "</TABLE></center>\n";
        echo "</form>\n";

        if ($SUB==1) {
            echo "<br><font color=$default_text>NEW QC SERVER</font>\n";
            echo "<form action=$PHP_SELF method=POST>\n";
            $qc_server_transfer_method   = "FTP";
            $qc_server_home_path         = "/home/USERNAME";
            $qc_server_location_template = "[campaign_id]/[date]";
            $qc_server_transfer_type     = "IMMEDIATE";
            $qc_server_archive           = "NONE";
            $qc_server_active            = "N";
            $qc_server_batch_time        = "0";
        } elseif ($SUB>1) {
            # Modify server
            echo "<br><font color=$default_text>MODIFY QC SERVER</font>\n";
            echo "<form action=$PHP_SELF method=POST>\n";
            echo "<input type=hidden name=qc_server_id value=$qc_server_id>\n";

            $stmt = sprintf("SELECT * FROM qc_servers WHERE id='%s';",mres($qc_server_id));
            $rslt = mysql_query($stmt, $link);
            $row = mysql_fetch_row($rslt);

            $qc_server_name              = $row[1];
            $qc_server_description       = $row[2];
            $qc_server_transfer_method   = $row[3];
            $qc_server_host              = $row[4];
            $qc_server_username          = $row[5];
            $qc_server_password          = $row[6];
            $qc_server_home_path         = $row[7];
            $qc_server_location_template = $row[8];
            $qc_server_transfer_type     = $row[9];
            $qc_server_archive           = $row[10];
            $qc_server_active            = $row[11];
            $qc_server_batch_time        = $row[12];
        }

        if ($SUB>0) {
            # New Server
            echo "<input type=hidden name=ADD value=499211111111111>\n";
            echo "<input type=hidden name=SUB value=$SUB>\n";
            echo "<center><TABLE width=$section_width cellspacing=1>\n";
    
            echo "<tr bgcolor=$oddrows><td align=right>Name: </td><td align=left><input type=text name=qc_server_name size=20 maxlength=20 value=\"$qc_server_name\">$NWB#qc-server_name$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Description: </td><td align=left><input type=text name=qc_server_description size=40 maxlength=100 value=\"$qc_server_description\">$NWB#qc-server_description$NWE</td></tr>\n";

            $qctmsel = "<option selected>" . $qc_server_transfer_method . "</option>";
            if ($qc_server_transfer_method == "FTP") {
                $qctmsel = "<option selected value=\"FTP\">FTP (passive)</option>";
            } elseif ($qc_server_transfer_method == "FTPA") {
                $qctmsel = "<option selected value=\"FTPA\">FTP (active)</option>";
            }
            echo "<tr bgcolor=$oddrows><td align=right>Transfer Method: </td><td align=left><select size=1 name=qc_server_transfer_method><option value=\"FTP\">FTP (passive)</option><option value=\"FTPA\">FTP (active)</option><option>SFTP</option><option>SCP</option>$qctmsel</select>$NWB#qc-server_transfer_method$NWE</td></tr>\n";

            echo "<tr bgcolor=$oddrows><td align=right>Hostname/IP: </td><td align=left><input type=text name=qc_server_host size=30 maxlength=50 value=\"$qc_server_host\">$NWB#qc-server_host$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Username: </td><td align=left><input type=text name=qc_server_username size=30 maxlength=30 value=\"$qc_server_username\">$NWB#qc-server_username$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Password: </td><td align=left><input type=text name=qc_server_password size=30 maxlength=30 value=\"$qc_server_password\">$NWB#qc-server_password$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Home Path: </td><td align=left><input type=text name=qc_server_home_path size=40 maxlength=100 value=\"$qc_server_home_path\">$NWB#qc-server_home_path$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Location Template: </td><td align=left><input type=text name=qc_server_location_template size=40 maxlength=255 value=\"$qc_server_location_template\">$NWB#qc-server_location_template$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Transfer Type: </td><td align=left><select size=1 name=qc_server_transfer_type><option>IMMEDIATE</option><option>BATCH</option><option>ARCHIVE</option><option selected>$qc_server_transfer_type</option></select>$NWB#qc-server_transfer_type$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Archive/Compression: </td><td align=left><select size=1 name=qc_server_archive><option>NONE</option><option>ZIP</option><option>TAR</option><option>TGZ</option><option>TBZ2</option><option selected>$qc_server_archive</option></select>$NWB#qc-server_archive$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Batch Time (hour): </td><td align=left><select size=1 name=qc_server_batch_time><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option><option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option><option>19</option><option>20</option><option>21</option><option>22</option><option>23</option><option selected>$qc_server_batch_time</option></select>$NWB#qc-server_batch_time$NWE</td></tr>\n";
            echo "<tr bgcolor=$oddrows><td align=right>Active: </td><td align=left><select size=1 name=qc_server_active><option>Y</option><option>N</option><option selected>$qc_server_active</option></select>$NWB#qc-server_active$NWE</td></tr>\n";
    
            echo "<tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
            echo "</TABLE></center>\n";
            echo "</form>\n";
        }

        if ($SUB>1) {
            # List QC rules
            echo "<br><font color=$default_text>QC SERVER RULES</font>\n";
            echo "<center><table cellspacing=1 width=$section_width bgcolor=grey>\n";
            echo "  <tr class=tabheader>\n";
            echo "    <td align=center>#</td>\n";
            echo "    <td align=center>QUERY</td>\n";
            echo "    <td colspan=2 align=center>ACTIONS</td>\n";
            echo "  </tr>\n";

            $stmt = sprintf("SELECT * FROM qc_server_rules WHERE qc_server_id='%s';",mres($qc_server_id));
            $rslt = mysql_query($stmt, $link);
            $rows = mysql_num_rows($rslt);
            $c = 0;
            while ($rows > $c) {
                $row = mysql_fetch_row($rslt);
                echo "  <tr " . bgcolor($c) . " class=\"row font1\">\n";
                echo "    <td>$c</td>\n";
                echo "    <td>$row[2]</td>\n";
                echo "    <td align=center><a href=\"$PHP_SELF?ADD=399211111111111&SUB=4&qc_server_id=$qc_server_id&qc_server_rule_id=$row[0]\">MODIFY</a></td>\n";
                echo "    <td align=center><a href=\"$PHP_SELF?ADD=699211111111111&SUB=4&qc_server_id=$qc_server_id&qc_server_rule_id=$row[0]\">REMOVE</a></td>\n";
                echo "  </tr>\n";
                $c++;
            }

            $qcfld = "<form action=$PHP_SELF method=POST>\n";
            $qcfld .= "<input type=hidden name=ADD value=499211111111111>\n";
            $qcfld .= "<input type=hidden name=qc_server_id value=$qc_server_id>\n";
            if ($SUB==4) {
                # Modify QC rule
                $qcfld .= "<input type=hidden name=SUB value=4>\n";
                $qcfld .= "<input type=hidden name=qc_server_rule_id value=$qc_server_rule_id>\n";
                $stmt = sprintf("SELECT * FROM qc_server_rules WHERE qc_server_id='%s';",mres($qc_server_id));
                $rslt = mysql_query($stmt, $link);
                $row = mysql_fetch_row($rslt);
                $qcract = "MODIFY";
                $qc_server_rule_query = $row[2];
            } else {
                # New QC rule
                $qcfld .= "<input type=hidden name=SUB value=3>\n";
                $qcract = "NEW";
            }
            $qcfld .= "  <tr class=tabfooter>\n";
            $qcfld .= "    <td>&nbsp;</td>\n";
            $qcfld .= "    <td align=center class=tabinput><input type=text name=qc_server_rule_query size=60 maxlength=255 value=\"$qc_server_rule_query\">$NWB#qc-server_rule_query$NWE</td>\n";
            $qcfld .= "    <td align=center colspan=2 class=tabbutton1><input type=submit name=submit VALUE=$qcract></td>\n";
            $qcfld .= "  </tr></form>\n";
            echo $qcfld;
            echo "</table>\n";
        }

    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



?>
