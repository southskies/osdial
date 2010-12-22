<?php
#
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



######################
# ADD=1carrier SUB=4  adds did form
# ADD=1carrier SUB=2  adds carrier form
######################
if ($ADD=="1carrier") {
    if ($LOG['ast_admin_access'] == 1) {
        ### SUB=4  New DID
        if ($SUB==4) {
            $gfr = get_first_record($link, 'osdial_carriers', '*', sprintf("id='%s'",mres($carrier_id)));
            $carrier_name=$gfr['name'];
            echo "<center>\n";
            echo "<br><font color=$default_text size=+1>ADD A NEW DID</font><br>\n";
            echo "<a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\">[ BACK TO CARRIER ]</a><br><br>\n";
            echo "<form name=osdial_form action=$PHP_SELF method=POST>\n";
            echo "<input type=hidden name=ADD value=2carrier>\n";
            echo "<input type=hidden name=SUB value=$SUB>\n";
            echo "<input type=hidden name=carrier_id value=$carrier_id>\n";
            echo "<table width=$section_width cellspacing=3>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right width=30%>Carrier Name:</td>\n";
            echo "    <td align=left><span style=\"color:$default_text;\">$carrier_name</span></td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>DID:</td>\n";
            echo "    <td align=left><input type=text name=did size=13 maxlength=100 value=\"\" onkeyup=\"updateingroup(this);return false;\">$NWB#carrier_dids-did$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>DID Action:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_action onchange=\"selaction(this);return false;\">\n";
            echo "        <option selected>INGROUP</option>\n";
            echo "        <option>PHONE</option>\n";
            echo "        <option>EXTENSION</option>\n";
            echo "        <option>VOICEMAIL</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-did_action$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";

            echo "  <tr bgcolor=$oddrows name=PHONE style=\"visibility:collapse;\">\n";
            echo "    <td align=right>Phone:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_phone>\n";
            $krh = get_krh($link, 'phones', '*','',"active='Y'",'');
            echo format_select_options($krh, 'extension', 'fullname', '', "-- Select Phone --",'');
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-phone$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";

            echo "  <tr bgcolor=$oddrows name=EXTENSION style=\"visibility:collapse;\">\n";
            echo "    <td align=right>Context:</td>\n";
            echo "    <td align=left>\n";
            echo "      <input type=text name=did_extension_context size=20 maxlength=50 value=\"\">$NWB#carrier_dids-extension_context$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=EXTENSION style=\"visibility:collapse;\">\n";
            echo "    <td align=right>Extension:</td>\n";
            echo "    <td align=left>\n";
            echo "      <input type=text name=did_extension size=20 maxlength=50 value=\"\">$NWB#carrier_dids-extension$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";

            echo "  <tr bgcolor=$oddrows name=VOICEMAIL style=\"visibility:collapse;\">\n";
            echo "    <td align=right>Voicemail:</td>\n";
            echo "    <td align=left><input type=text name=did_voicemail size=20 maxlength=20 value=\"\">$NWB#carrier_dids-voicemail$NWE</td>\n";
            echo "  </tr>\n";

            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"visibility:visible;\">\n";
            echo "    <td align=right>InGroup:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_ingroup>\n";
            $krh = get_krh($link, 'osdial_inbound_groups', '*','',sprintf('group_id IN %s',$LOG['allowed_ingroupsSQL']),'');
            echo format_select_options($krh, 'group_id', 'group_name', '', " [ CREATE NEW INGROUP ] ",'');
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-ingroup$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"visibility:visible;\">\n";
            echo "    <td align=right>Server Allocation:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_server_allocation>\n";
            echo "        <option selected>LO</option>\n";
            echo "        <option>LB</option>\n";
            echo "        <option>SO</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-server_allocation$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"visibility:visible;\">\n";
            echo "    <td align=right>Park File:</td>\n";
            echo "    <td align=left><input type=text name=did_park_file size=20 maxlength=100 value=\"park\">$NWB#carrier_dids-park_file$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"visibility:visible;\">\n";
            echo "    <td align=right>Lookup Method:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_lookup_method>\n";
            echo "        <option selected>CID</option>\n";
            echo "        <option>CIDLOOKUP</option>\n";
            echo "        <option>CIDLOOKUPRL</option>\n";
            echo "        <option>CIDLOOKUPRC</option>\n";
            echo "        <option>CLOSER</option>\n";
            echo "        <option>ANI</option>\n";
            echo "        <option>ANILOOKUP</option>\n";
            echo "        <option>ANILOOKUPRL</option>\n";
            echo "        <option>ANILOOKUPRC</option>\n";
            echo "        <option>3DIGITID</option>\n";
            echo "        <option>4DIGITID</option>\n";
            echo "        <option>5DIGITID</option>\n";
            echo "        <option>10DIGITID</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-lookup_method$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"visibility:visible;\">\n";
            echo "    <td align=right>Default List ID:</td>\n";
            echo "    <td align=left><input type=text name=did_default_list_id size=15 maxlength=15 value=\"998\">$NWB#carrier_dids-default_list_id$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"visibility:visible;\">\n";
            echo "    <td align=right>Default Phone (country) Code:</td>\n";
            echo "    <td align=left><input type=text name=did_default_phone_code size=5 maxlength=5 value=\"1\">$NWB#carrier_dids-default_phone_code$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"visibility:visible;\">\n";
            echo "    <td align=right>Search Campaign:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_search_campaign>\n";
            $krh = get_krh($link, 'osdial_campaigns', '*','',sprintf('campaign_id IN %s',$LOG['allowed_campaignsSQL']),'');
            echo format_select_options($krh, 'campaign_id', 'campaign_name', '', "-- NONE --",'');
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-ingroup$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";

            echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
            echo "</table>\n";
            echo "</center>\n";
            echo "</form>\n";

            echo "<script type=\"text/javascript\">\n";
            include "carriers.js";
            echo "</script>\n";


        ### SUB=2  New Carrier
        } elseif ($SUB==2) {
            echo "<center>\n";
            echo "<br><font color=$default_text size=+1>ADD A NEW CARRIER</font><br><br>\n";
            echo "<form name=osdial_form action=$PHP_SELF method=POST>\n";
            echo "<input type=hidden name=ADD value=2carrier>\n";
            echo "<input type=hidden name=SUB value=$SUB>\n";
            echo "<table width=$section_width cellspacing=3>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right width=30%>Name:</td>\n";
            echo "    <td align=left><input type=text name=carrier_name size=20 maxlength=20 value=\"\">$NWB#carriers-name$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Description:</td>\n";
            echo "    <td align=left><input type=text name=carrier_description size=40 maxlength=255 value=\"\">$NWB#carriers-description$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Protocol:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=carrier_protocol>\n";
            echo "        <option>SIP</option>\n";
            echo "        <option>IAX2</option>\n";
            $svrp = get_first_record($link, 'servers', 'count(*) AS count', "asterisk_version LIKE '1.2.%%'");
            if ($svrp['count'] > 0) echo "        <option>Zap</option>\n";
            $svrp = get_first_record($link, 'servers', 'count(*) AS count', "asterisk_version LIKE '1.6.%%'");
            if ($svrp['count'] > 0) echo "        <option>DAHDI</option>\n";
            echo "        <option>EXTERNAL</option>\n";
            echo "       </select>\n";
            echo "       $NWB#carriers-protocol$NWE\n";
            echo "     </td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
            echo "</table>\n";
            echo "</center>\n";
            echo "</font>\n";


        } else {
            echo "<font color=red>Error, carrier function not specified.</font>\n";
        }
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}

######################
# ADD=2carrier SUB=4  adds the new did to the system
# ADD=2carrier SUB=2  adds the new carrier to the system
######################

$ANC=0;
if ($ADD=="2carrier") {
    if ($LOG['ast_admin_access'] == 1) {
        $stmt='';
        $aclog='';

        ### SUB=4  Added DID
        if ($SUB==4) {
            $SUB=2;
            if (strlen($did) < 1 or $carrier_id < 1) {
                echo "<br><font color=red>DID NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>carrier_id not set.\n";
                echo "<br>did must be at least 1 characters.</font><br>\n";
            } else {
                $stmt=sprintf("SELECT count(*) FROM osdial_carrier_dids WHERE did='%s' AND carrier_id='%s';",mres($did),mres($carrier_id));
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                if ($row[0] > 0) {
                    echo "<br><font color=red>DID NOT ADDED - there is already an entry for this DID for this Carrier.</font><br>\n";
                } elseif ($did_action=='PHONE' and $did_phone=='') {
                    echo "<br><font color=red>DID NOT ADDED - you must select a phone or select a different action.</font><br>\n";
                } elseif ($did_action=='EXTENSION' and $did_extension=='') {
                    echo "<br><font color=red>DID NOT ADDED - you must enter an extension or select a different action.</font><br>\n";
                } elseif ($did_action=='VOICEMAIL' and $did_voicemail=='') {
                    echo "<br><font color=red>DID NOT ADDED - you must enter a voicemail box or select a different action.</font><br>\n";
                } else {
                    if ($did_action=='INGROUP' and $did_ingroup=='') {
                        $did_ingroup='IN_'.$did;
                        $stmt=sprintf("SELECT count(*) FROM osdial_inbound_groups WHERE group_id='%s';",mres($did_ingroup));
                        $rslt=mysql_query($stmt, $link);
                        $row=mysql_fetch_row($rslt);
                        if ($row[0] == 0) {
                            $stmt=sprintf("INSERT INTO osdial_inbound_groups SET group_id='%s',group_name='%s',group_color='%s',active='%s',next_agent_call='%s',fronter_display='%s',".
                                "web_form_address='%s',web_form_address2='%s';",mres($did_ingroup),'Inbound From DID '.$did,'TEAL','Y','oldest_call_finish','Y','','');
                            $rslt=mysql_query($stmt, $link);
                            echo "<br><b><font color=$default_text>INGROUP ADDED: $did_ingroup</font></b>\n";
                        }
                    }
                    if ($did_initial_status=='') $did_initial_status='INBND';
                    if ($did_extension=='') $did_extension='9999';
                    if ($did_extension_context=='') $did_extension_context='default';
                    $stmt=sprintf("INSERT INTO osdial_carrier_dids SET carrier_id='%s',did='%s',did_action='%s',phone='%s',extension='%s',extension_context='%s',voicemail='%s',ingroup='%s'," .
                        "server_allocation='%s',park_file='%s',lookup_method='%s',initial_status='%s',default_list_id='%s',default_phone_code='%s',search_campaign='%s';",
                        mres($carrier_id),mres($did),mres($did_action),mres($did_phone),mres($did_extension),mres($did_extension_context),mres($did_voicemail),mres($did_ingroup),mres($did_server_allocation),
                        mres($did_park_file),mres($did_lookup_method),mres($did_initial_status),mres($did_default_list_id),mres($did_default_phone_code),mres($did_search_campaign));
                    $rslt=mysql_query($stmt, $link);
                    $did_id=mysql_insert_id($link);
                    echo "<br><b><font color=$default_text>DID ADDED: $carrier_id - $did_id - $did</font></b>\n";
                    $aclog='DID';
                    $SUB=4;
                }
            }


        ### SUB=2  Added Carrier
        } elseif ($SUB==2) {
            $SUB=1;
            if (strlen($carrier_name) < 3 or strlen($carrier_protocol) < 1) {
                echo "<br><font color=red>CARRIER NOT ADDED - Please go back and look at the data you entered\n";
                echo "<br>carrier_name must be at least 2 characters.\n";
                echo "<br>carrier_protocol_config must be at least 10 characters.</font><br>\n";
            } else {
                $stmt=sprintf("SELECT count(*) FROM osdial_carriers WHERE name='%s';",mres($carrier_name));
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                if ($row[0] > 0) {
                    echo "<br><font color=red>CARRIER NOT ADDED - there is already a carrier with this name</font><br>\n";
                } else {
                    $stmt=sprintf("INSERT INTO osdial_carriers SET name='%s',description='%s',protocol='%s';",mres($carrier_name),mres($carrier_description),mres($carrier_protocol));
                    $rslt=mysql_query($stmt, $link);
                    $carrier_id=mysql_insert_id($link);
                    echo "<br><b><font color=$default_text>CARRIER ADDED: $carrier_id - $carrier_name</font></b><br>\n";
                    $aclog='CARRIER';
                    $ANC=1;
                    $SUB=2;
                }
            }


        } else {
            echo "<font color=red>Error, carrier function not specified.</font>\n";
        }


        ### LOG CHANGES TO LOG FILE ###
        if (strlen($aclog) > 0 and $WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|ADD A NEW $aclog|$PHP_AUTH_USER|$ip|$stmt|\n");
            fclose($fp);
        }
        $ADD="3carrier";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=4carrier SUB=4  modify DID for a carrier in the system
# ADD=4carrier SUB=3  modify server specific carrier options in the system
# ADD=4carrier SUB=2  modify carrier in the system
######################

if ($ADD=="4carrier") {
    if ($LOG['ast_admin_access'] == 1) {
        $stmt='';
        $aclog='';

        $carrier_protocol_config = preg_replace("/^context=.*\n/m",'',$carrier_protocol_config);

        ### SUB=4  Modify DID 
        if ($SUB==4) {
            $SUB=1;
            if ($did_id < 1 or strlen($did) < 1 or $carrier_id < 1) {
                echo "<br><font color=red>DID NOT MODIFIED - Please go back and look at the data you entered\n";
                echo "<br>did_id not set..\n";
                echo "<br>carrier_id not set.\n";
                echo "<br>did must be at least 1 characters.</font><br>\n";
            } else {
                $SUB=4;
                $stmt=sprintf("SELECT count(*) FROM osdial_carrier_dids WHERE did='%s' AND carrier_id='%s' AND id!='%s';",mres($did),mres($carrier_id),mres($did_id));
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                if ($row[0] > 0) {
                    echo "<br><font color=red>DID NOT RENAMED - there is already an entry for this DID</font><br>\n";
                } elseif ($did_action=='PHONE' and $did_phone=='') {
                    echo "<br><font color=red>DID NOT MODIFIED - you must select a phone or select a different action.</font><br>\n";
                } elseif ($did_action=='EXTENSION' and $did_extension=='') {
                    echo "<br><font color=red>DID NOT MODIFIED - you must enter an extension or select a different action.</font><br>\n";
                } elseif ($did_action=='VOICEMAIL' and $did_voicemail=='') {
                    echo "<br><font color=red>DID NOT MODIFIED - you must enter a voicemail box or select a different action.</font><br>\n";
                } elseif ($did_action=='INGROUP' and $did_ingroup=='') {
                    echo "<br><font color=red>DID NOT MODIFIED - you must select an ingroup or a different action.</font><br>\n";
                } else {
                    if ($did_initial_status=='') $did_initial_status='INBND';
                    if ($did_extension=='') $did_extension='9999';
                    if ($did_extension_context=='') $did_extension_context='default';
                    echo "<br><b><font color=$default_text>DID MODIFIED: $carrier_id - $did_id - $did</font></b>\n";
                    $stmt=sprintf("UPDATE osdial_carrier_dids SET did='%s',did_action='%s',phone='%s',extension='%s',extension_context='%s',voicemail='%s',ingroup='%s'," .
                        "server_allocation='%s',park_file='%s',lookup_method='%s',initial_status='%s',default_list_id='%s'," .
                        "default_phone_code='%s',search_campaign='%s' WHERE id='%s' AND carrier_id='%s';",
                        mres($did),mres($did_action),mres($did_phone),mres($did_extension),mres($did_extension_context),mres($did_voicemail),mres($did_ingroup),mres($did_server_allocation),
                        mres($did_park_file),mres($did_lookup_method),mres($did_initial_status),mres($did_default_list_id),
                        mres($did_default_phone_code),mres($did_search_campaign),mres($did_id),mres($carrier_id));
                    $rslt=mysql_query($stmt, $link);
                    $aclog='DID';
                }
            }


        ### SUB=3  Modify Server Specifc Carrier Option
        } elseif ($SUB==3) {
            $SUB=1;
            if (strlen($carrier_server_ip) < 3 or $carrier_id < 1) {
                echo "<br><font color=red>SERVER SPECIFIC CARRIER OPTION NOT MODIFIED - Please go back and look at the data you entered\n";
                echo "<br>carrier_server_ip not set.\n";
                echo "<br>carrier_id not set.</font><br>\n";
            } else {
                $SUB=3;
                $gfr = get_first_record($link, 'osdial_carriers', '*', sprintf("id='%s'",mres($carrier_id)));
                if ($carrier_protocol_config == $gfr['protocol_config']) $carrier_protocol_config='';
                if ($carrier_registrations == $gfr['registrations']) $carrier_registrations='';
                if ($carrier_dialplan == $gfr['dialplan']) $carrier_dialplan='';
                $stmt=sprintf("SELECT count(*) FROM osdial_carrier_servers WHERE carrier_id='%s' AND server_ip='%s';",mres($carrier_id),mres($carrier_server_ip));
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                $csaction='MODIFIED';
                if ($row[0]==0) {
                    $stmt=sprintf("INSERT INTO osdial_carrier_servers SET protocol_config='%s',registrations='%s',dialplan='%s',carrier_id='%s',server_ip='%s';",
                        mres($carrier_protocol_config),mres($carrier_registrations),mres($carrier_dialplan),mres($carrier_id),mres($carrier_server_ip));
                    $csaction='ADDED';
                } else {
                    $stmt=sprintf("UPDATE osdial_carrier_servers SET protocol_config='%s',registrations='%s',dialplan='%s' WHERE carrier_id='%s' AND server_ip='%s';",
                        mres($carrier_protocol_config),mres($carrier_registrations),mres($carrier_dialplan),mres($carrier_id),mres($carrier_server_ip));
                    $SUB=2;
                }
                echo "<br><b><font color=$default_text>SERVER SPECIFIC CARRIER OPTIONS $csaction: $carrier_id - $carrier_server_ip</font></b><br>\n";
                $rslt=mysql_query($stmt, $link);
                $aclog='CARRIER SERVER';
            }


        ### SUB=2  Modify Carrier
        } elseif ($SUB==2) {
            $SUB=1;
            if ($carrier_id < 1 or strlen($carrier_name) < 3) {
                echo "<br><font color=red>CARRIER NOT MODIFIED - Please go back and look at the data you entered\n";
                echo "<br>carrier_id not set: $carrier_id\n";
                echo "<br>carrier_name must be at least 2 characters: $carrier_name</font><br>\n";
            } else {
                $stmt=sprintf("SELECT count(*) FROM osdial_carriers WHERE name='%s' AND id!='%s';",mres($carrier_name),mres($carrier_id));
                $rslt=mysql_query($stmt, $link);
                $row=mysql_fetch_row($rslt);
                if ($row[0] > 0) {
                    echo "<br><font color=red>CARRIER NOT RENAMED - there is already a carrier with this name.</font><br>\n";
                } else {
                    if ($carrier_default_callerid == '') $carrier_default_callerid = '0000000000';
                    if ($carrier_default_areacode == '') $carrier_default_areacode = '321';
                    echo "<br><b><font color=$default_text>CARRIER MODIFIED: $carrier_id - $carrier_name</font></b><br>\n";
                    $stmt=sprintf("UPDATE osdial_carriers SET name='%s',description='%s',active='%s',selectable='%s',protocol='%s'," .
                        "protocol_config='%s',registrations='%s',dialplan='%s',failover_id='%s',failover_condition='%s'," .
                        "strip_msd='%s',allow_international='%s',default_callerid='%s',default_areacode='%s',default_prefix='%s' WHERE id='%s';",
                        mres($carrier_name),mres($carrier_description),mres($carrier_active),mres($carrier_selectable),
                        mres($carrier_protocol),mres($carrier_protocol_config),mres($carrier_registrations),mres($carrier_dialplan),
                        mres($carrier_failover_id),mres($carrier_failover_condition),mres($carrier_strip_msd),mres($carrier_allow_international),
                        mres($carrier_default_callerid),mres($carrier_default_areacode),mres($carrier_default_prefix),mres($carrier_id));
                    $rslt=mysql_query($stmt, $link);
                    $aclog='CARRIER';
                }
            }
            $SUB=2;


        } else {
            echo "<font color=red>Error, carrier function not specified.</font>\n";
        }


        ### LOG CHANGES TO LOG FILE ###
        if (strlen($aclog) > 0 and $WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|MODIFY $aclog|$PHP_AUTH_USER|$ip|$stmt|\n");
            fclose($fp);
        }
        $ADD="3carrier";    # go to campaign modification form below
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


######################
# ADD=5carrier SUB=4  confirmation before deletion of DID
# ADD=5carrier SUB=3  confirmation before deletion of server specific carrier options
# ADD=5carrier SUB=2  confirmation before deletion of carrier and its DIDs
######################

if ($ADD == "5carrier") {
    if ($LOG['ast_admin_access'] == 1) {
        ### SUB=4  Confirm DID Deletion
        if ($SUB==4) {
            if ($carrier_id < 1 or $did_id < 1 or strlen($did) < 1) {
                echo "<br><font color=red>DID NOT DELETED - Please go back and look at the data you entered\n";
                echo "<br>carrier_id not set.\n";
                echo "<br>did_id not set.\n";
                echo "<br>did must be at least 1 characters.</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>DID DELETION CONFIRMATION: $did_id - $did</b>\n";
                echo "<br><a href=\"$PHP_SELF?ADD=6carrier&SUB=$SUB&carrier_id=$carrier_id&did_id=$did_id&did=$did&CoNfIrM=YES\">Click here to delete this DID</a></font><br>\n";
            }


        ### SUB=3  Confirm Server Specific Carrier Option Deletion
        } elseif ($SUB==3) {
            if (strlen($carrier_server_ip) < 3 or $carrier_id < 1) {
                echo "<br><font color=red>SERVER SPECIFIC OPTIONS NOT DELETED - Please go back and look at the data you entered\n";
                echo "<br>carrier_server_ip not set.</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>SERVER SPECIFIC OPTION DELETION CONFIRMATION: $carrier_id - $carrier_server_ip</b>\n";
                echo "<br><a href=\"$PHP_SELF?ADD=6carrier&SUB=$SUB&carrier_server_ip=$carrier_server_ip&carrier_id=$carrier_id&CoNfIrM=YES\">Click here to delete the server specific options for this carrier</a></font><br>\n";
            }


        ### SUB=2  Confirm Carrier Deletion
        } elseif ($SUB==2) {
            if ($carrier_id < 1) {
                echo "<br><font color=red>CARRIER NOT DELETED - Please go back and look at the data you entered\n";
                echo "<br>carrier_id not set.</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>CARRIER DELETION CONFIRMATION: $carrier_id - $carrier_name</b>\n";
                echo "<br><a href=\"$PHP_SELF?ADD=6carrier&SUB=$SUB&carrier_id=$carrier_id&CoNfIrM=YES\">Click here to delete this carrier and all its DIDs</a></font><br>\n";
            }


        } else {
            echo "<font color=red>Error, carrier function not specified.</font>\n";
        }
        $ADD="3carrier";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=6carrier SUB=4  delete a carrier's DID
# ADD=6carrier SUB=3  delete server specifc options for a carrier
# ADD=6carrier SUB=2  delete carrier and its DIDs
######################

if ($ADD=="6carrier") {
    if ($LOG['ast_admin_access'] == 1) {
        $stmt='';
        $aclog='';

        ### SUB=4  DID Deletion
        if ($SUB==4) {
            if ($carrier_id < 1 or $did_id < 1 or strlen($did) < 1) {
                echo "<br><font color=red>DID NOT DELETED - Please go back and look at the data you entered\n";
                echo "<br>carrier_id not set.\n";
                echo "<br>did_id not set.\n";
                echo "<br>did must be at least 1 characters.</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>DID DELETED: $did_id - $did</font></b><br>\n";
                $stmt=sprintf("DELETE FROM osdial_carrier_dids WHERE carrier_id='%s' AND id='%s';",mres($carrier_id),mres($did_id));
                $rslt=mysql_query($stmt, $link);
                $aclog='DID';
                $SUB=2;
            }


        ### SUB=3  Server Specific Carrier Option Deletion
        } elseif ($SUB==3) {
            if (strlen($carrier_server_ip) < 3 or $carrier_id < 1) {
                echo "<br><font color=red>SERVER SPECIFC CARRIER OPTIONS NOT DELETED - Please go back and look at the data you entered\n";
                echo "<br>carrier_server_ip not set.\n";
                echo "<br>carrier_id not set.</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>SERVER SPECIFIC CARRIER OPTIONS DELETED: $carrier_id - $carrier_server_ip</font></b><br>\n";
                $stmt=sprintf("DELETE FROM osdial_carrier_servers WHERE carrier_id='%s' AND server_ip='%s';",mres($carrier_id),mres($carrier_server_ip));
                $rslt=mysql_query($stmt, $link);
                $aclog='CARRIER SERVER';
                $SUB=2;
            }


        ### SUB=2  Carrier Deletion
        } elseif ($SUB==2) {
            if ($carrier_id < 1) {
                echo "<br><font color=red>CARRIER NOT DELETED - Please go back and look at the data you entered\n";
                echo "<br>carrier_id not set.</font><br>\n";
            } else {
                echo "<br><b><font color=$default_text>CARRIER DELETED: $carrier_id</font></b><br>\n";
                $stmt=sprintf("DELETE FROM osdial_carrier_dids WHERE carrier_id='%s';",mres($carrier_id));
                $rslt=mysql_query($stmt, $link);
                $stmt=sprintf("DELETE FROM osdial_carrier_servers WHERE carrier_id='%s';",mres($carrier_id));
                $rslt=mysql_query($stmt, $link);
                $stmt=sprintf("DELETE FROM osdial_carriers WHERE id='%s';",mres($carrier_id));
                $rslt=mysql_query($stmt, $link);
                $aclog='CARRIER';
                $SUB=1;
            }


        } else {
            echo "<font color=red>Error, carrier function not specified.</font>\n";
        }


        ### LOG CHANGES TO LOG FILE ###
        if (strlen($aclog)>0 and $WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|DELETE $aclog|$PHP_AUTH_USER|$ip|$stmt|\n");
            fclose($fp);
        }

        echo "</font>\n";
        $ADD="3carrier";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}



######################
# ADD=3carrier SUB=4 display DID
# ADD=3carrier SUB=3 display server specific carrier options
# ADD=3carrier SUB=2 display carrier
# ADD=3carrier SUB=1 display all carriers
######################
if ($ADD == "3carrier") {
    if ($LOG['ast_admin_access'] == 1) {
        ### SUB=4  DID Form
        if ($SUB==4) {
            $gfr = get_first_record($link, 'osdial_carriers', '*', sprintf("id='%s'",mres($carrier_id)));
            $carrier_name=$gfr['name'];
            $gfr = get_first_record($link, 'osdial_carrier_dids', '*', sprintf("id='%s'",mres($did_id)));
            $isel='';
            $psel='';
            $esel='';
            $vsel='';
            $istyle='visibility:collapse;';
            $pstyle='visibility:collapse;';
            $estyle='visibility:collapse;';
            $vstyle='visibility:collapse;';
            if ($gfr['did_action'] == 'INGROUP') {
                $isel = 'selected';
                $istyle = 'visibility:visible;';
            }
            if ($gfr['did_action'] == 'PHONE') {
                $psel = 'selected';
                $pstyle = 'visibility:visible;';
            }
            if ($gfr['did_action'] == 'EXTENSION') {
                $esel = 'selected';
                $estyle = 'visibility:visible;';
            }
            if ($gfr['did_action'] == 'VOICEMAIL') {
                $vsel = 'selected';
                $vstyle = 'visibility:visible;';
            }
            echo "<center>\n";
            echo "<br><font color=$default_text size=+1>MODIFY DID</font><br>\n";
            echo "<a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\">[ BACK TO CARRIER ]</a><br><br>\n";
            echo "<form name=osdial_form action=$PHP_SELF method=POST>\n";
            echo "<input type=hidden name=ADD value=4carrier>\n";
            echo "<input type=hidden name=SUB value=$SUB>\n";
            echo "<input type=hidden name=carrier_id value=$gfr[carrier_id]>\n";
            echo "<input type=hidden name=did_id value=$did_id>\n";

            echo "<table width=$section_width cellspacing=3>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right width=30%>Carrier Name:</td>\n";
            echo "    <td align=left><span style=\"color:$default_text;\">$carrier_name</span></td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>DID:</td>\n";
            echo "    <td align=left><input type=text name=did size=13 maxlength=100 value=\"$gfr[did]\">$NWB#carrier_dids-did$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>DID Action:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_action onchange=\"selaction(this);return false;\">\n";
            echo "        <option $isel>INGROUP</option>\n";
            echo "        <option $psel>PHONE</option>\n";
            echo "        <option $esel>EXTENSION</option>\n";
            echo "        <option $vsel>VOICEMAIL</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-did_action$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";

            echo "  <tr bgcolor=$oddrows name=PHONE style=\"$pstyle\">\n";
            echo "    <td align=right>Phone:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_phone>\n";
            $krh = get_krh($link, 'phones', '*','',"active='Y'",'');
            echo format_select_options($krh, 'extension', 'fullname', $gfr['phone'], "-- Select Phone --",'');
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-phone$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";

            echo "  <tr bgcolor=$oddrows name=EXTENSION style=\"$estyle\">\n";
            echo "    <td align=right>Context:</td>\n";
            echo "    <td align=left>\n";
            echo "      <input type=text name=did_extension_context size=20 maxlength=50 value=\"$gfr[extension_context]\">$NWB#carrier_dids-extension_context$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=EXTENSION style=\"$estyle\">\n";
            echo "    <td align=right>Extension:</td>\n";
            echo "    <td align=left>\n";
            echo "      <input type=text name=did_extension size=20 maxlength=50 value=\"$gfr[extension]\">$NWB#carrier_dids-extension$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";

            echo "  <tr bgcolor=$oddrows name=VOICEMAIL style=\"$vstyle\">\n";
            echo "    <td align=right>Voicemail:</td>\n";
            echo "    <td align=left><input type=text name=did_voicemail size=20 maxlength=20 value=\"$gfr[voicemail]\">$NWB#carrier_dids-voicemail$NWE</td>\n";
            echo "  </tr>\n";

            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"$istyle\">\n";
            echo "    <td align=right>InGroup:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_ingroup>\n";
            $krh = get_krh($link, 'osdial_inbound_groups', '*','',sprintf("(group_id='%s' OR group_id IN %s)",$gfr['ingroup'],$LOG['allowed_ingroupsSQL']),'');
            echo format_select_options($krh, 'group_id', 'group_name', $gfr['ingroup'], "-- NONE --",'');
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-ingroup$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"$istyle\">\n";
            echo "    <td align=right>Server Allocation:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_server_allocation>\n";
            echo "        <option>LO</option>\n";
            echo "        <option>LB</option>\n";
            echo "        <option>SO</option>\n";
            echo "        <option selected>$gfr[server_allocation]</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-server_allocation$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"$istyle\">\n";
            echo "    <td align=right>Park File:</td>\n";
            echo "    <td align=left><input type=text name=did_park_file size=20 maxlength=100 value=\"$gfr[park_file]\">$NWB#carrier_dids-park_file$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"$istyle\">\n";
            echo "    <td align=right>Initial Status:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_initial_status>\n";
            $krh = get_krh($link, 'osdial_statuses', '*','','','');
            echo format_select_options($krh, 'status', 'status_name', $gfr['initial_status'], "-- NONE --",'');
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-ingroup$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"$istyle\">\n";
            echo "    <td align=right>Lookup Method:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_lookup_method>\n";
            echo "        <option>CID</option>\n";
            echo "        <option>CIDLOOKUP</option>\n";
            echo "        <option>CIDLOOKUPRL</option>\n";
            echo "        <option>CIDLOOKUPRC</option>\n";
            echo "        <option>CLOSER</option>\n";
            echo "        <option>ANI</option>\n";
            echo "        <option>ANILOOKUP</option>\n";
            echo "        <option>ANILOOKUPRL</option>\n";
            echo "        <option>ANILOOKUPRC</option>\n";
            echo "        <option>3DIGITID</option>\n";
            echo "        <option>4DIGITID</option>\n";
            echo "        <option>5DIGITID</option>\n";
            echo "        <option>10DIGITID</option>\n";
            echo "        <option selected>$gfr[lookup_method]</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-lookup_method$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"$istyle\">\n";
            echo "    <td align=right>Default List ID:</td>\n";
            echo "    <td align=left><input type=text name=did_default_list_id size=15 maxlength=15 value=\"$gfr[default_list_id]\">$NWB#carrier_dids-default_list_id$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"$istyle\">\n";
            echo "    <td align=right>Default Phone (country) Code:</td>\n";
            echo "    <td align=left><input type=text name=did_default_phone_code size=5 maxlength=5 value=\"$gfr[default_phone_code]\">$NWB#carrier_dids-default_phone_code$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows name=INGROUP style=\"$istyle\">\n";
            echo "    <td align=right>Search Campaign:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=did_search_campaign>\n";
            $krh = get_krh($link, 'osdial_campaigns', '*','',sprintf('campaign_id IN %s',$LOG['allowed_campaignsSQL']),'');
            echo format_select_options($krh, 'campaign_id', 'campaign_name', $gfr['search_campaign'], "-- NONE --",'');
            echo "      </select>\n";
            echo "      $NWB#carrier_dids-ingroup$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";

            echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
            echo "</table>\n";
            echo "</center>\n";
            echo "</form>\n";

            echo "<br><br><a href=\"$PHP_SELF?ADD=5carrier&SUB=$SUB&did_id=$did_id&did=$did&carrier_id=$carrier_id\">DELETE THIS DID</a>\n";


        ### SUB=3  Server Specific Carrier Options Form
        } elseif ($SUB==3) {
            $gfr = get_first_record($link, 'osdial_carrier_servers', '*', sprintf("server_ip='%s' AND carrier_id='%s'",mres($carrier_server_ip),mres($carrier_id)));
            $carrier_protocol_config=$gfr['protocol_config'];
            $carrier_registrations=$gfr['registrations'];
            $carrier_dialplan=$gfr['dialplan'];

            $gfr = get_first_record($link, 'osdial_carriers', '*', sprintf("id='%s'",mres($carrier_id)));
            $gfr = get_first_record($link, 'osdial_carriers', '*', sprintf("id='%s'",mres($carrier_id)));
            if ($carrier_protocol_config == '') $carrier_protocol_config=$gfr['protocol_config'];
            if ($carrier_registrations == '') $carrier_registrations=$gfr['registrations'];
            if ($carrier_dialplan == '') $carrier_dialplan=$gfr['dialplan'];

            echo "<center>\n";
            echo "<br><font color=$default_text size=+1>MODIFY SERVER SPECIFIC CARRIER OPTIONS</font><br>\n";
            echo "<a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier_id\">[ BACK TO CARRIER ]</a><br><br>\n";
            echo "<form name=osdial_form action=$PHP_SELF method=POST>\n";
            echo "<input type=hidden name=ADD value=4carrier>\n";
            echo "<input type=hidden name=SUB value=$SUB>\n";
            echo "<input type=hidden name=carrier_id value=$carrier_id>\n";
            echo "<input type=hidden name=carrier_server_ip value=$carrier_server_ip>\n";
            echo "<table width=$section_width cellspacing=3>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right width=30%>Carrier Name:</td>\n";
            echo "    <td align=left><span style=\"color:$default_text;\">$gfr[name]</span></td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right>Server IP:</td>\n";
            echo "    <td align=left><span style=\"color:$default_text;\">$carrier_server_ip</span></td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right>Protocol Configuration:</td>\n";
            echo "    <td align=left>\n";
            echo "      <textarea name=carrier_protocol_config id=carrier_protocol_config rows=10 cols=100 wrap=off style=\"font-size:9px;\">" . $carrier_protocol_config . "</textarea>\n";
            echo "      $NWB#carrier_servers-protocol_config$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right>Registration(s):</td>\n";
            echo "    <td align=left>\n";
            $regcnt = count(explode("\n",$carrier_registrations));
            echo "      <textarea name=carrier_registrations id=carrier_registrations rows=\"$regcnt\" cols=100 wrap=off style=\"font-size:9px;\">" . $carrier_registrations . "</textarea>\n";
            echo "      $NWB#carrier_servers-registrations$NWE<br>\n";
            echo "      <span style=\"color:$default_text;font-size:10pt;font-style:italic;\">user[:secret[:authuser]]@host[:port][/extension]</span>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right>Dialplan:</td>\n";
            echo "    <td align=left>\n";
            echo "      <textarea name=carrier_dialplan id=carrier_dialplan rows=10 cols=100 wrap=off style=\"font-size:9px;\">" . $carrier_dialplan . "</textarea>\n";
            echo "      $NWB#carrier_servers-dialplan$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
            echo "</table>\n";
            echo "</center>\n";
            echo "</form>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=5carrier&SUB=$SUB&carrier_server_ip=$carrier_server_ip&carrier_id=$carrier_id\">DELETE THESE SERVER OPTIONS</a>\n";


        ### SUB=2  Carrier Form
        } elseif ($SUB==2) {
            $gfr = get_first_record($link, 'osdial_carriers', '*', sprintf("id='%s'",mres($carrier_id)));
            echo "<center>\n";
            echo "<br><font color=$default_text size=+1>MODIFY CARRIER</font><br><br>\n";
            echo "<form name=osdial_form action=$PHP_SELF method=POST>\n";
            echo "<input type=hidden name=ADD value=4carrier>\n";
            echo "<input type=hidden name=SUB value=$SUB>\n";
            echo "<input type=hidden name=carrier_id value=$carrier_id>\n";
            echo "<table width=$section_width cellspacing=3>\n";
            echo "  <tr>\n";
            echo "    <td align=center colspan=2>\n";
            echo "      <select name=cpt style=\"font-size:10px;\" onchange=\"selcarrier(this);return false;\">\n";
            echo "        <option selcted> -- Sample Carrier Configuration Templates -- </option>\n";
            echo "      </select>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabheader>\n";
            echo "    <td colspan=2></td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right width=30%>Name:</td>\n";
            echo "    <td align=left><input type=text name=carrier_name id=carrier_name size=20 maxlength=20 value=\"$gfr[name]\">$NWB#carriers-name$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Description:</td>\n";
            echo "    <td align=left><input type=text name=carrier_description id=carrier_description size=40 maxlength=255 value=\"$gfr[description]\">$NWB#carriers-description$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Active:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=carrier_active id=carrier_active>\n";
            echo "        <option>Y</option>\n";
            echo "        <option>N</option>\n";
            echo "        <option selected>$gfr[active]</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carriers-active$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Selectable:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=carrier_selectable id=carrier_selectable>\n";
            echo "        <option>Y</option>\n";
            echo "        <option>N</option>\n";
            echo "        <option selected>$gfr[selectable]</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carriers-selectable$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Protocol:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=carrier_protocol id=carrier_protocol>\n";
            echo "        <option>SIP</option>\n";
            echo "        <option>IAX2</option>\n";
            $svrp = get_first_record($link, 'servers', 'count(*) AS count', "asterisk_version LIKE '1.2.%%'");
            if ($svrp['count'] > 0) echo "        <option>Zap</option>\n";
            $svrp = get_first_record($link, 'servers', 'count(*) AS count', "asterisk_version LIKE '1.6.%%'");
            if ($svrp['count'] > 0) echo "        <option>DAHDI</option>\n";
            echo "        <option>EXTERNAL</option>\n";
            echo "        <option selected>$gfr[protocol]</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carriers-protocol$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right>Protocol Configuration:</td>\n";
            echo "    <td align=left>\n";
            echo "      <textarea name=carrier_protocol_config id=carrier_protocol_config rows=10 cols=100 wrap=off style=\"font-size:9px;\">" . $gfr['protocol_config'] . "</textarea>\n";
            echo "      $NWB#carriers-protocol_config$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right>Registration(s):</td>\n";
            echo "    <td align=left>\n";
            $regcnt = count(explode("\n",$gfr['registrations']));
            echo "      <textarea name=carrier_registrations id=carrier_registrations rows=\"$regcnt\" cols=100 wrap=off style=\"font-size:9px;\">" . $gfr['registrations'] . "</textarea>\n";
            echo "      $NWB#carriers-registrations$NWE<br>\n";
            echo "      <span style=\"color:$default_text;font-size:10pt;font-style:italic;\">user[:secret[:authuser]]@host[:port][/extension]</span>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows valign=top>\n";
            echo "    <td align=right>Dialplan:</td>\n";
            echo "    <td align=left>\n";
            echo "      <textarea name=carrier_dialplan id=carrier_dialplan rows=10 cols=100 wrap=off style=\"font-size:9px;\">" . $gfr['dialplan'] . "</textarea>\n";
            echo "      $NWB#carriers-dialplan$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Strip MSD:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=carrier_strip_msd id=carrier_strip_msd>\n";
            echo "        <option>Y</option>\n";
            echo "        <option>N</option>\n";
            echo "        <option selected>$gfr[strip_msd]</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carriers-strip_msd$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Allow International:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=carrier_allow_international id=carrier_allow_international>\n";
            echo "        <option>Y</option>\n";
            echo "        <option>N</option>\n";
            echo "        <option selected>$gfr[allow_international]</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carriers-allow_international$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Default CallerID:</td>\n";
            echo "    <td align=left><input type=text name=carrier_default_callerid id=carrier_default_callerid size=20 maxlength=20 value=\"$gfr[default_callerid]\">$NWB#carriers-default_callerid$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Default Areacode:</td>\n";
            echo "    <td align=left><input type=text name=carrier_default_areacode id=carrier_default_areacode size=3 maxlength=3 value=\"$gfr[default_areacode]\">$NWB#carriers-default_areacode$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Default Prefix:</td>\n";
            echo "    <td align=left><input type=text name=carrier_default_prefix id=carrier_default_prefix size=1 maxlength=1 value=\"$gfr[default_prefix]\">$NWB#carriers-default_prefix$NWE</td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Failover Carrier:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=carrier_failover_id>\n";
            $krh = get_krh($link, 'osdial_carriers', '*','',sprintf("id!='%s'",$carrier_id),'');
            echo format_select_options($krh, 'id', 'name', $gfr['failover_id'], "-- NONE --",'');
            echo "      </select>\n";
            echo "      $NWB#carriers-failover$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr bgcolor=$oddrows>\n";
            echo "    <td align=right>Failover Condition:</td>\n";
            echo "    <td align=left>\n";
            echo "      <select name=carrier_failover_condition>\n";
            echo "        <option>CHANUNAVAIL</option>\n";
            echo "        <option>CONGESTION</option>\n";
            echo "        <option>BOTH</option>\n";
            echo "        <option selected>$gfr[failover_condition]</option>\n";
            echo "      </select>\n";
            echo "      $NWB#carriers-failover_condition$NWE\n";
            echo "    </td>\n";
            echo "  </tr>\n";
            echo "  <tr class=tabfooter><td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td></tr>\n";
            echo "</table>\n";
            echo "</center>\n";
            echo "</form>\n";

            #### List Server Carrier Options
            echo "<br><br>\n";
            echo "<center>\n";
            echo "  <br><font color=$default_text size=+1>SERVER SPECIFIC SETTINGS</font><br><br>\n";
            echo "  <table width=500 cellspacing=0 cellpadding=1>\n";
            echo "    <tr class=tabheader>\n";
            echo "      <td>ServerIP</td>\n";
            echo "      <td>Name</td>\n";
            echo "      <td title=\"Protocol Configuration\">P</td>\n";
            echo "      <td title=\"Registrations\">R</td>\n";
            echo "      <td title=\"Dialplan\">D</td>\n";
            echo "      <td align=center>LINKS</td>\n";
            echo "    </tr>\n";
            $servers = get_krh($link, 'servers', '*','',"active='Y'",'');
            if (is_array($servers)) {
                $c=0;
                foreach ($servers as $server) {
                    $ocs = get_first_record($link, 'osdial_carrier_servers','*',sprintf("server_ip='%s' AND carrier_id='%s'",mres($server[server_ip]),mres($carrier_id)));
                    $pact = 'N';
                    $ract = 'N';
                    $dact = 'N';
                    if (is_array($ocs)) {
                        if ($ocs['protocol_config']!='') $pact='Y';
                        if ($ocs['registrations']!='')   $ract='Y';
                        if ($ocs['dialplan']!='') $dact='Y';
                    }
                    echo "    <tr " . bgcolor($c) ." class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3carrier&SUB=3&carrier_server_ip=$server[server_ip]&carrier_id=$carrier_id';\">\n";
                    echo "      <td align=center><a href=\"$PHP_SELF?ADD=3carrier&SUB=3&carrier_server_ip=$server[server_ip]&carrier_id=$carrier_id\">$server[server_ip]</a></td>\n";
                    echo "      <td align=center>$server[server_id]</td>\n";
                    echo "      <td align=center title=\"Protocol Configuration\">$pact</td>\n";
                    echo "      <td align=center title=\"Registrations\">$ract</td>\n";
                    echo "      <td align=center title=\"Dialplan\">$dact</td>\n";
                    echo "      <td align=center><a href=\"$PHP_SELF?ADD=3carrier&SUB=3&carrier_server_ip=$server[server_ip]&carrier_id=$carrier_id\">MODIFY</a></td>\n";
                    echo "    </tr>\n";
                    $c++;
                }
            }
            echo "    <tr class=tabfooter>\n";
            echo "      <td colspan=6></td>\n";
            echo "    </tr>\n";
            echo "  </table>\n";
            echo "</center>\n";

            #### List DIDs
            echo "<br><br>\n";
            echo "<center>\n";
            echo "  <br><font color=$default_text size=+1>DIDs</font><br><br>\n";
            echo "  <table width=500 cellspacing=0 cellpadding=1>\n";
            echo "    <tr class=tabheader>\n";
            echo "      <td>DID</td>\n";
            echo "      <td>Action</td>\n";
            echo "      <td>Destination</td>\n";
            echo "      <td align=center>LINKS</td>\n";
            echo "    </tr>\n";
            $dids = get_krh($link, 'osdial_carrier_dids', '*','did ASC',sprintf("carrier_id='%s'",mres($carrier_id)),'');
            if (is_array($dids)) {
                $c=0;
                foreach ($dids as $did) {
                    $dest = '';
                    if ($did['did_action']=='INGROUP') $dest=$did['ingroup'];
                    if ($did['did_action']=='PHONE') $dest=$did['phone'];
                    if ($did['did_action']=='EXTENSION') $dest=$did['extension'];
                    if ($did['did_action']=='VOICEMAIL') $dest=$did['voicemail'];
                    echo "    <tr " . bgcolor($c) ." class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3carrier&SUB=4&did_id=$did[id]&did[did]&carrier_id=$carrier_id';\">\n";
                    echo "      <td align=center><a href=\"$PHP_SELF?ADD=3carrier&SUB=4&did_id=$did[id]&did=$did[did]&carrier_id=$carrier_id\">$did[did]</a></td>\n";
                    echo "      <td align=center>$did[did_action]</td>\n";
                    echo "      <td align=center>$dest</td>\n";
                    echo "      <td align=center><a href=\"$PHP_SELF?ADD=3carrier&SUB=4&did_id=$did[id]&did=$did[did]&carrier_id=$carrier_id\">MODIFY</a></td>\n";
                    echo "    </tr>\n";
                    $c++;
                }
            }
            echo "    <tr class=tabfooter>\n";
            echo "      <td colspan=4></td>\n";
            echo "    </tr>\n";
            echo "  </table>\n";
            echo "  <br><a href=\"$PHP_SELF?ADD=1carrier&SUB=4&carrier_id=$carrier_id\">ADD NEW DID</a>\n";
            echo "</center>\n";
            echo "<br><br><a href=\"$PHP_SELF?ADD=5carrier&SUB=$SUB&carrier_id=$carrier_id\">DELETE THIS CARRIER</a>\n";


        ### SUB=1  Carrier List
        } elseif ($SUB<=1) {
            echo "<center>\n";
            echo "  <br><font color=$default_text size=+1>CARRIERS</font><br><br>\n";
            echo "  <table width=$section_width cellspacing=0 cellpadding=1>\n";
            echo "    <tr class=tabheader>\n";
            echo "      <td width=15%>Name</td>\n";
            echo "      <td width=20%>Description</td>\n";
            echo "      <td width=15%>Protocol</td>\n";
            echo "      <td>Active</td>\n";
            echo "      <td>Selectable</td>\n";
            echo "      <td width=20%>LINKS</td>\n";
            echo "    </tr>\n";
            $c=0;
            $carriers = get_krh($link, 'osdial_carriers', '*','','','');
            if (is_array($carriers)) {
                foreach ($carriers as $carrier) {
                    echo "    <tr " . bgcolor($c) ." class=\"row font1\" ondblclick=\"window.location='$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier[id]';\">\n";
                    echo "      <td><a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier[id]\">$carrier[name]</a></td>\n";
                    echo "      <td>$carrier[description]</td>\n";
                    echo "      <td align=center>$carrier[protocol]</td>\n";
                    echo "      <td align=center>$carrier[active]</td>\n";
                    echo "      <td align=center>$carrier[selectable]</td>\n";
                    echo "      <td align=center><a href=\"$PHP_SELF?ADD=3carrier&SUB=2&carrier_id=$carrier[id]\">MODIFY</a></td>\n";
                    echo "    </tr>\n";
                    $c++;
                }
            }
            echo "    <tr class=tabfooter>\n";
            echo "      <td colspan=6></td>\n";
            echo "    </tr>\n";
            echo "  </table>\n";
            echo "  <br><a href=\"$PHP_SELF?ADD=1carrier&SUB=2&carrier_id=$carrier_id\">ADD NEW CARRIER</a>\n";
            echo "</center>\n";


        } else {
            echo "<font color=red>Error, carrier function not specified.</font>\n";
        }

        echo "<script type=\"text/javascript\">\n";
        include "carriers.js";
        if ($ANC==1) {
            if ($carrier_protocol == 'SIP') {
                echo "document.osdial_form.carrier_protocol_config.value=carriers[2].replace(new RegExp('genericSIP','g'),'$carrier_name');\n";
            } elseif ($carrier_protocol == 'IAX2') {
                echo "document.osdial_form.carrier_protocol_config.value=carriers[7].replace(new RegExp('genericIAX','g'),'$carrier_name');\n";
            }
            echo "document.osdial_form.carrier_dialplan.value=dialplan;\n";
        }
        echo "</script>\n";
    } else {
        echo "<font color=red>You do not have permission to view this page</font>\n";
    }
}


?>
