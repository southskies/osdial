<?php
#
# Copyright (C) 2010 Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
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


# Modify Section
if ($ADD==411111111111111) {
    if ($LOGmodify_servers==1) {
        # Header
        echo "  <font color=$default_text>SYSTEM SETTINGS MODIFIED</font>\n";

        # Prepare with sprintf and filer ALL values wih mres as seen.
        $stmt = sprintf("UPDATE system_settings SET use_non_latin='%s',webroot_writable='%s',enable_queuemetrics_logging='%s',queuemetrics_server_ip='%s',queuemetrics_dbname='%s'," .
            "queuemetrics_login='%s',queuemetrics_pass='%s',queuemetrics_url='%s',queuemetrics_log_id='%s',queuemetrics_eq_prepend='%s',osdial_agent_disable='%s',allow_sipsak_messages='%s'," .
            "admin_home_url='%s',enable_agc_xfer_log='%s',company_name='%s',admin_template='%s',agent_template='%s',enable_lead_allocation='%s',enable_external_agents='%s',enable_filters='%s'," .
            "enable_multicompany='%s',multicompany_admin='%s',default_carrier_id='%s',intra_server_protocol='%s';",
            mres($use_non_latin),mres($webroot_writable),mres($enable_queuemetrics_logging),mres($queuemetrics_server_ip),mres($queuemetrics_dbname),
            mres($queuemetrics_login),mres($queuemetrics_pass),mres($queuemetrics_url),mres($queuemetrics_log_id),mres($queuemetrics_eq_prepend),mres($osdial_agent_disable),mres($allow_sipsak_messages),
            mres($admin_home_url),mres($enable_agc_xfer_log),mres($company_name),mres($admin_template),mres($agent_template),mres($enable_lead_allocation),mres($enable_external_agents),mres($enable_filters),
            mres($enable_multicompany),mres($multicompany_admin),mres($carrier_id),mres($intra_server_protocol));
        $rslt=mysql_query($stmt, $link);

        ### LOG CHANGES TO LOG FILE ###
        if ($WeBRooTWritablE > 0) {
            $fp = fopen ("./admin_changes_log.txt", "a");
            fwrite ($fp, "$date|MODIFY SYSTEM SETTINGS|$PHP_AUTH_USER|$ip|$stmt|\n");
            fclose($fp);
        }
        # go to osdial system settings form below
        $ADD=311111111111111;

    } else {
        echo "<center><font color=red>You do not have permission to view this page</font></center>\n";
    }
    # Flush out the buffers
    ob_flush();
    flush();
}


# The System modification form
if ($ADD==311111111111111) {
    if ($LOGmodify_servers==1) {
        $system_settings = get_first_record($link, 'system_settings', '*', '');
        # The Main System Settings Form.
        echo "      <center><br>\n";
        echo "      <font color=$default_text size=+1>MODIFY SYSTEM SETTINGS</font>\n";
        echo "      <form action=$PHP_SELF method=POST><br>\n";
        echo "      <input type=hidden name=ADD value=411111111111111>\n";
        if (!file_exists($WeBServeRRooT . '/admin/include/content/admin/company.php')) {
            echo "      <input type=hidden name=multicompany_admin value=admin>\n";
            echo "      <input type=hidden name=enable_multicompany value=0>\n";
        }
        echo "      <table cellspacing=3>\n";
        echo "        <tr class=tabheader><td colspan=2></td></tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Version:</td>\n";
        echo "          <td align=left><font color=$default_text>$system_settings[version]</font></td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Build:</td>\n";
        echo "          <td align=left><font color=$default_text>$build</font></td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Installation Date:</td>\n";
        $system_settings['install_date'] = date('m-d-Y',strtotime($system_settings['install_date']));
        echo "          <td align=left><font color=$default_text>$system_settings[install_date]</font></td>\n";
        echo "        </tr>\n";

        echo "        <tr class=tabheader><td colspan=2>General</td></tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Company Name:</td>\n";
        echo "          <td align=left><input type=text name=company_name size=30 maxlength=100 value=\"$system_settings[company_name]\"></td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Admin Home URL:</td>\n";
        echo "          <td align=left><input type=text name=admin_home_url size=40 maxlength=255 value=\"$system_settings[admin_home_url]\">$NWB#settings-admin_home_url$NWE</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Intra-Server Protocol:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select name=intra_server_protocol>\n";
        $ispi = ''; if ($system_settings['intra_server_protocol']=='IAX2') $ispi = 'selected';
        echo "              <option $ispi>IAX2</option>\n";
        $isps = ''; if ($system_settings['intra_server_protocol']=='SIP' or $ispi=='') $isps = 'selected';
        echo "              <option $isps>SIP</option>\n";
        echo "            </select>\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Default Carrier:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select name=carrier_id>\n";
        $krh = get_krh($link, 'osdial_carriers', '*','',"active='Y' AND selectable='Y'",'');
        echo format_select_options($krh, 'id', 'name', $system_settings['default_carrier_id'], "** USE MANUAL CONFIGURATION **",'');
        echo "            </select>\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Use Non-Latin:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=use_non_latin>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[use_non_latin]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-use_non_latin$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Webroot Writable:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=webroot_writable>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[webroot_writable]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-webroot_writable$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Allow SIPSAK Messages:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=allow_sipsak_messages>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[allow_sipsak_messages]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-allow_sipsak_messages$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Enable Agent Transfer Logfile:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=enable_agc_xfer_log>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[enable_agc_xfer_log]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-enable_agc_xfer_log$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Agent Disable Display:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=osdial_agent_disable>\n";
        echo "              <option>NOT_ACTIVE</option>\n";
        echo "              <option>LIVE_AGENT</option>\n";
        echo "              <option>EXTERNAL</option>\n";
        echo "              <option>ALL</option>\n";
        echo "              <option selected>$system_settings[osdial_agent_disable]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-osdial_agent_disable$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Agent Template:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=agent_template>\n";
        $dir_handle = opendir($WeBServeRRooT . "/agent/templates");
        while ($file = readdir($dir_handle)) {
            if($file!="." && $file!="..") echo "              <option>$file</option>\n";
        }
        echo "              <option selected>$system_settings[agent_template]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-agent_template$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Admin Template:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=admin_template>\n";
        $dir_handle = opendir($WeBServeRRooT . "/admin/templates");
        while ($file = readdir($dir_handle)) {
            if($file!="." && $file!="..") echo "              <option>$file</option>\n";
        }
        echo "              <option selected>$system_settings[admin_template]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-admin_template$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";

        echo "        <tr class=tabheader><td colspan=2>Admin GUI</td></tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Enable Lead Allocation Menu:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=enable_lead_allocation>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[enable_lead_allocation]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-enable_lead_allocation$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Enable Filters Menu:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=enable_filters>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[enable_filters]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-enable_filters$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Enable External Agents Menu:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=enable_external_agents>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[enable_external_agents]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-enable_external_agents$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";

        if (file_exists($WeBServeRRooT . '/admin/include/content/admin/company.php')) {
            echo "        <tr class=tabheader><td colspan=2>Multi-Company</td></tr>\n";

            echo "        <tr bgcolor=$oddrows>\n";
            echo "          <td align=right>Enable Multi-Company Support:</td>\n";
            echo "          <td align=left>\n";
            echo "            <select size=1 name=enable_multicompany>\n";
            echo "              <option>1</option>\n";
            echo "              <option>0</option>\n";
            echo "              <option selected>$system_settings[enable_multicompany]</option>\n";
            echo "            </select>\n";
            echo "            $NWB#settings-enable_multicompany$NWE\n";
            echo "          </td>\n";
            echo "        </tr>\n";
            echo "        <tr bgcolor=$oddrows>\n";
            echo "          <td align=right>Multi-Company Administator:</td>\n";
            echo "          <td align=left><input type=text name=multicompany_admin size=10 maxlength=15 value=\"$system_settings[multicompany_admin]\"></td>\n";
            echo "        </tr>\n";
        }

        echo "        <tr class=tabheader><td colspan=2>Queuemetrics</td></tr>\n";

        $qstyle = 'visibility:collapse;';
        if ($system_settings['enable_queuemetrics_logging']>0) $qstyle = 'visibility:visible;';
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Enable QueueMetrics Logging:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=enable_queuemetrics_logging>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[enable_queuemetrics_logging]</option>\n";
        echo "            </select>\n";
        echo "            $NWB#settings-enable_queuemetrics_logging$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics Server IP:</td>\n";
        echo "          <td align=left><input type=text name=queuemetrics_server_ip size=18 maxlength=15 value=\"$system_settings[queuemetrics_server_ip]\">$NWB#settings-queuemetrics_server_ip$NWE</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics DB Name:</td>\n";
        echo "          <td align=left><input type=text name=queuemetrics_dbname size=18 maxlength=50 value=\"$system_settings[queuemetrics_dbname]\">$NWB#settings-queuemetrics_dbname$NWE</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics DB Login:</td>\n";
        echo "          <td align=left><input type=text name=queuemetrics_login size=18 maxlength=50 value=\"$queuemetrics_login\">$NWB#settings-queuemetrics_login$NWE</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics DB Password:</td>\n";
        echo "          <td align=left><input type=text name=queuemetrics_pass size=18 maxlength=50 value=\"$queuemetrics_pass\">$NWB#settings-queuemetrics_pass$NWE</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics URL:</td>\n";
        echo "          <td align=left><input type=text name=queuemetrics_url size=40 maxlength=255 value=\"$queuemetrics_url\">$NWB#settings-queuemetrics_url$NWE</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics Log ID:</td>\n";
        echo "          <td align=left><input type=text name=queuemetrics_log_id size=12 maxlength=10 value=\"$queuemetrics_log_id\">$NWB#settings-queuemetrics_log_id$NWE</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics EnterQueue Prepend:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=queuemetrics_eq_prepend>\n";
        echo "              <option>NONE</option>\n";
        echo "              <option>lead_id</option>\n";
        echo "              <option>list_id</option>\n";
        echo "              <option>source_id</option>\n";
        echo "              <option>vendor_lead_code</option>\n";
        echo "              <option>address3</option>\n";
        echo "              <option>custom1</option>\n";
        echo "              <option>custom2</option>\n";
        echo "              <option>external_key</option>\n";
        echo "              <option selected>$system_settings[queuemetrics_eq_prepend]</option>\n";
        echo "            </select>";
        echo "            $NWB#settings-queuemetrics_eq_prepend$NWE\n";
        echo "          </td>\n";
        echo "        </tr>\n";

        echo "        <tr class=tabfooter>\n";
        echo "          <td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td>\n";
        echo "        </tr>\n";
        echo "      </table>\n";
        echo "      </form>\n";
        echo "      </center>\n";

    } else {
        echo "<center><font color=red>You do not have permission to view this page</font></center>\n";
    }
    # Flush out the buffers
    ob_flush();
    flush();
}


?>
