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


if (file_exists($WeBServeRRooT . '/admin/include/content/admin/company_settings.php')) {
    include_once($WeBServeRRooT . '/admin/include/content/admin/company_settings.php');
}

if (file_exists($WeBServeRRooT . '/admin/include/content/admin/acct_settings.php')) {
    include_once($WeBServeRRooT . '/admin/include/content/admin/acct_settings.php');
}

# Modify Section
if ($ADD==411111111111111) {
    if ($LOG['modify_servers']==1) {
        # Header
        echo "  <font color=$default_text>SYSTEM SETTINGS MODIFIED</font>\n";

        if (file_exists($WeBServeRRooT . '/admin/include/content/admin/acct_settings.php')) {
            AddUpdateAcctPackages();
        }

        if ($SUB=='') {
            if (empty($admin_session_expiration)) $admin_session_expiration='0';
            if (empty($admin_session_lockout)) $admin_session_lockout='0';
            if ($admin_session_expiration=='0') $admin_session_lockout='0';
            $multicompany_admin = join('|',$multicompany_admin);
            # Prepare with sprintf and filer ALL values wih mres as seen.
            $stmt = sprintf("UPDATE system_settings SET use_non_latin='%s',webroot_writable='%s',enable_queuemetrics_logging='%s',queuemetrics_server_ip='%s',queuemetrics_dbname='%s'," .
                "queuemetrics_login='%s',queuemetrics_pass='%s',queuemetrics_url='%s',queuemetrics_log_id='%s',queuemetrics_eq_prepend='%s',osdial_agent_disable='%s',allow_sipsak_messages='%s'," .
                "admin_home_url='%s',enable_agc_xfer_log='%s',company_name='%s',admin_template='%s',agent_template='%s',enable_lead_allocation='%s',enable_external_agents='%s',enable_filters='%s'," .
                "enable_multicompany='%s',multicompany_admin='%s',default_carrier_id='%s',intra_server_protocol='%s',default_date_format='%s',use_browser_timezone_offset='%s',last_recording_extension='%s',last_general_extension='%s',default_phone_code='%s',default_acct_method='%s',default_acct_cutoff='%s',default_acct_expire_days='%s',acct_email_warning_time='%s',acct_email_warning_expire='%s',use_old_admin_auth='%s',default_ext_context='%s',admin_session_expiration='%s',admin_session_lockout='%s',mc_default_enable_system_phones='%s',system_email='%s';",
                mres($use_non_latin),mres($webroot_writable),mres($enable_queuemetrics_logging),mres($queuemetrics_server_ip),mres($queuemetrics_dbname),
                mres($queuemetrics_login),mres($queuemetrics_pass),mres($queuemetrics_url),mres($queuemetrics_log_id),mres($queuemetrics_eq_prepend),mres($osdial_agent_disable),mres($allow_sipsak_messages),
                mres($admin_home_url),mres($enable_agc_xfer_log),mres($company_name),mres($admin_template),mres($agent_template),mres($enable_lead_allocation),mres($enable_external_agents),mres($enable_filters),
                mres($enable_multicompany),mres($multicompany_admin),mres($carrier_id),mres($intra_server_protocol),mres($default_date_format),mres($use_browser_timezone_offset),mres($last_recording_extension),
                mres($last_general_extension),mres($default_phone_code),mres($default_acct_method),mres($default_acct_cutoff),mres($default_acct_expire_days),mres($acct_email_warning_time),mres($acct_email_warning_expire),mres($use_old_admin_auth),mres($default_ext_context),mres($admin_session_expiration),mres($admin_session_lockout),mres($mc_default_enable_system_phones),mres($system_email));
            $rslt=mysql_query($stmt, $link);

            ### LOG CHANGES TO LOG FILE ###
            if ($WeBRooTWritablE > 0) {
                $fp = fopen ("./admin_changes_log.txt", "a");
                fwrite ($fp, "$date|MODIFY SYSTEM SETTINGS|$PHP_AUTH_USER|$ip|$stmt|\n");
                fclose($fp);
            }
        }
        # go to osdial system settings form below
        $ADD=311111111111111;
        $SUB='';

    } else {
        echo "<center><font color=red>You do not have permission to view this page</font></center>\n";
    }
    # Flush out the buffers
    ob_flush();
    flush();
}


# The System modification form
if ($ADD==311111111111111) {
    if ($LOG['modify_servers']==1) {
        $system_settings = get_first_record($link, 'system_settings', '*', '');
        # The Main System Settings Form.
        echo "      <center><br>\n";
        echo "      <font class=top_header color=$default_text size=+1>MODIFY SYSTEM SETTINGS</font>\n";
        echo "      <form action=$PHP_SELF method=POST><br>\n";
        echo "      <input type=hidden name=ADD value=411111111111111>\n";
        if (!file_exists($WeBServeRRooT . '/admin/include/content/admin/company.php')) {
            echo "      <input type=hidden name=multicompany_admin value=admin>\n";
            echo "      <input type=hidden name=enable_multicompany value=0>\n";
        }
        echo "      <table class=shadedtable cellspacing=3 width=800>\n";
        echo "        <tr class=tabheader><td colspan=2></td></tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Version:</td>\n";
        echo "          <td align=left><font color=$default_text>$system_settings[version]</font></td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Build:</td>\n";
        echo "          <td align=left><font color=$default_text>".$config['settings']['build']."</font></td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Installation Date:</td>\n";
        $system_settings['install_date'] = date('m-d-Y',strtotime($system_settings['install_date']));
        echo "          <td align=left><font color=$default_text>$system_settings[install_date]</font></td>\n";
        echo "        </tr>\n";

        echo "        <tr class=tabheader><td colspan=2>General</td></tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Company Name:</td>\n";
        echo "          <td align=left><input type=text name=company_name size=30 maxlength=100 value=\"$system_settings[company_name]\">".helptag("system_settings-company_name")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Admin Home URL:</td>\n";
        echo "          <td align=left><input type=text name=admin_home_url size=40 maxlength=255 value=\"$system_settings[admin_home_url]\">".helptag("system_settings-admin_home_url")."</td>\n";
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
        echo "            ".helptag("system_settings-intra_server_protocol")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Default Carrier:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select name=carrier_id>\n";
        $krh = get_krh($link, 'osdial_carriers', '*','',"active='Y' AND selectable='Y'",'');
        echo format_select_options($krh, 'id', 'name', $system_settings['default_carrier_id'], "** USE MANUAL CONFIGURATION **",'');
        echo "            </select>\n";
        echo "            ".helptag("system_settings-default_carrier")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Current Character-Set:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=use_non_latin ";
        if ($system_settings['use_non_latin']==0) {
            echo "onmousedown=\"alert('To change to the UTF8 character-set, log into the console and run: /opt/osdial/bin/sql/upgrade_sql.pl --convert --use-utf8');\">\n";
            echo "              <option value=0>Latin1</option>\n";
        } else {
            echo "onmousedown=\"alert('To change to the Latin1 character-set, log into the console and run: /opt/osdial/bin/sql/upgrade_sql.pl --convert --use-utf8');\">\n";
            echo "              <option value=1>UTF8</option>\n";
        }
        echo "            </select>\n";
        echo "            ".helptag("system_settings-use_non_latin")."\n";
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
        echo "            ".helptag("system_settings-webroot_writable")."\n";
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
        echo "            ".helptag("system_settings-allow_sipsak_messages")."\n";
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
        echo "            ".helptag("system_settings-enable_agc_xfer_log")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Display Agent Notifications:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=osdial_agent_disable>\n";
        echo "              <option>NOT_ACTIVE</option>\n";
        echo "              <option>LIVE_AGENT</option>\n";
        echo "              <option>EXTERNAL</option>\n";
        echo "              <option>ALL</option>\n";
        echo "              <option selected>$system_settings[osdial_agent_disable]</option>\n";
        echo "            </select>\n";
        echo "            ".helptag("system_settings-osdial_agent_disable")."\n";
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
        echo "            ".helptag("system_settings-agent_template")."\n";
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
        echo "            ".helptag("system_settings-admin_template")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Use Old Admin Auth:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=use_old_admin_auth>\n";
        echo "              <option>1</option>\n";
        echo "              <option>0</option>\n";
        echo "              <option selected>$system_settings[use_old_admin_auth]</option>\n";
        echo "            </select>\n";
        echo "            ".helptag("system_settings-use_old_admin_auth")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Admin Session Expiration:</td>\n";
        if (empty($system_settings['admin_session_expiration'])) $system_settings['admin_session_expiration']='0';
        echo "          <td align=left><input type=text name=admin_session_expiration size=15 maxlength=15 value=\"$system_settings[admin_session_expiration]\">".helptag("system_settings-admin_session_expiration")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Admin Session Lockout:</td>\n";
        if (empty($system_settings['admin_session_lockout'])) $system_settings['admin_session_lockout']='0';
        echo "          <td align=left><input type=text name=admin_session_lockout size=15 maxlength=15 value=\"$system_settings[admin_session_lockout]\">".helptag("system_settings-admin_session_lockout")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Default Date Format:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=default_date_format>\n";
        echo "              <option value=\"Y-m-d H:i:s\">" . date('Y-m-d H:i:s') . "</option>\n";
        echo "              <option value=\"Y-m-d H:i\">" . date('Y-m-d H:i') . "</option>\n";
        echo "              <option value=\"m-d-Y H:i:s\">" . date('m-d-Y H:i:s') . "</option>\n";
        echo "              <option value=\"m-d-Y H:i\">" . date('m-d-Y H:i') . "</option>\n";
        echo "              <option value=\"F j, Y, g:i a\">" . date('F j, Y, g:i a') . "</option>\n";
        echo "              <option value=\"D M j G:i:s T Y\">" . date('D M j G:i:s T Y') . "</option>\n";
        echo "              <option value=\"YmdHis\">" . date('YmdHis') . "</option>\n";
        echo "              <option value=\"d-m-Y H:i\">" . date('d-m-Y H:i') . "</option>\n";
        echo "              <option value=\"r\">" . date('r') . "</option>\n";
        echo "              <option value=\"h:m:s A d/m/Y\">" . date('h:m:s A d/m/Y') . "</option>\n";
        echo "              <option value=\"d/m/Y h:m:s A\">" . date('d/m/Y h:m:s A') . "</option>\n";
        echo "              <option value=\"c\">" . date('c') . "</option>\n";
        echo "              <option value=\"U\">" . date('U') . "</option>\n";
        echo "              <option value=\"l jS \\of F Y h:i:s A\">" . date('l jS \of F Y h:i:s A') . "</option>\n";
        echo "              <option selected value=\"$system_settings[default_date_format]\">" . date($system_settings['default_date_format']) . "</option>\n";
        echo "            </select>\n";
        echo "            ".helptag("system_settings-default_date_format")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Use Browser Timezone Offset:</td>\n";
        echo "          <td align=left>\n";
        echo "            <select size=1 name=use_browser_timezone_offset>\n";
        echo "              <option>Y</option>\n";
        echo "              <option>N</option>\n";
        echo "              <option selected>$system_settings[use_browser_timezone_offset]</option>\n";
        echo "            </select>\n";
        echo "            ".helptag("system_settings-use_broweser_timezone_offset")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Last Recording Extension:</td>\n";
        echo "          <td align=left><input type=text name=last_recording_extension size=20 maxlength=20 value=\"$system_settings[last_recording_extension]\">".helptag("system_settings-last_recording_extension")."</td>\n";
        echo "        </tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Last General Extension:</td>\n";
        echo "          <td align=left><input type=text name=last_general_extension size=20 maxlength=20 value=\"$system_settings[last_general_extension]\">".helptag("system_settings-last_general_extension")."</td>\n";
        echo "        </tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>Default Country Phone Code:</td>\n";
        echo "          <td align=left><input type=text name=default_phone_code size=10 maxlength=10 value=\"$system_settings[default_phone_code]\">".helptag("system_settings-default_phone_code")."</td>\n";
        echo "        </tr>\n";
        echo "<tr bgcolor=$oddrows><td align=right>Default Ext Context: </td><td align=left>";
        $contexts = array();
        $contexts['osdialBLOCK']='Block direct calling to outbound and extensions';
        $contexts['osdialEXT']='Block direct outbound, Allow direct extensions';
        $contexts['osdial']='Allow direct calling to outbound and extensions';
        $contexts['default']='Same as osdial context';
        echo editableSelectBox($contexts, 'default_ext_context', $system_settings['default_ext_context'], 100, 100, '');
        echo helptag("system_settings-default_ext_context")."</td></tr>\n";

        echo "        <tr bgcolor=$oddrows>\n";
        echo "          <td align=right>System Email:</td>\n";
        echo "          <td align=left><input type=text name=system_email size=30 maxlength=255 value=\"".htmlentities($system_settings[system_email])."\">".helptag("system_settings-system_email")."</td>\n";
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
        echo "            ".helptag("system_settings-enable_lead_allocation")."\n";
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
        echo "            ".helptag("system_settings-enable_filters")."\n";
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
        echo "            ".helptag("system_settings-enable_external_agents")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";

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
        echo "            ".helptag("system_settings-enable_queuemetrics_logging")."\n";
        echo "          </td>\n";
        echo "        </tr>\n";
        
        
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics Server IP:</td>\n";
		echo "          <td align=left><input type=text name=queuemetrics_server_ip size=18 maxlength=15 value=\"$system_settings[queuemetrics_server_ip]\">".helptag("system_settings-queuemetrics_server_ip")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics DB Name:</td>\n";
		echo "          <td align=left><input type=text name=queuemetrics_dbname size=18 maxlength=50 value=\"$system_settings[queuemetrics_dbname]\">".helptag("system_settings-queuemetrics_dbname")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics DB Login:</td>\n";
		echo "          <td align=left><input type=text name=queuemetrics_login size=18 maxlength=50 value=\"$system_settings[queuemetrics_login]\">".helptag("system_settings-queuemetrics_login")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics DB Password:</td>\n";
		echo "          <td align=left><input type=text name=queuemetrics_pass size=18 maxlength=50 value=\"$system_settings[queuemetrics_pass]\">".helptag("system_settings-queuemetrics_pass")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics URL:</td>\n";
		echo "          <td align=left><input type=text name=queuemetrics_url size=40 maxlength=255 value=\"$system_settings[queuemetrics_url]\">".helptag("system_settings-queuemetrics_url")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics Log ID:</td>\n";
		echo "          <td align=left><input type=text name=queuemetrics_log_id size=12 maxlength=10 value=\"$system_settings[queuemetrics_log_id]\">".helptag("system_settings-queuemetrics_log_id")."</td>\n";
        echo "        </tr>\n";
        echo "        <tr bgcolor=$oddrows style=\"$qstyle\">\n";
        echo "          <td align=right>QueueMetrics EnterQueue Prepend:</td>\n";
        echo "          <td align=left>\n";
        echo "            <div>\n";
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
		echo "            ".helptag("system_settings-queuemetrics_eq_prepend")."\n";
        echo "            </div>\n";
        echo "          </td>\n";
        echo "        </tr>\n";

        if (file_exists($WeBServeRRooT . '/admin/include/content/admin/company_settings.php')) {
            echo ShowCompanySettings();
            include_once($WeBServeRRooT . '/admin/include/content/admin/company_settings.php');
        }

        if (file_exists($WeBServeRRooT . '/admin/include/content/admin/acct_settings.php')) {
            echo ShowAcctSettings();
            include_once($WeBServeRRooT . '/admin/include/content/admin/acct_settings.php');
        }

        echo "        <tr class=tabfooter>\n";
        echo "          <td align=center colspan=2 class=tabbutton><input type=submit name=submit VALUE=SUBMIT></td>\n";
        echo "        </tr>\n";
        echo "      </table>\n";
        echo "      </form>\n";

        if (file_exists($WeBServeRRooT . '/admin/include/content/admin/acct_settings.php')) {
            echo ShowAcctPackages();
        }

        echo "      </center>\n";

    } else {
        echo "<center><font color=red>You do not have permission to view this page</font></center>\n";
    }
    # Flush out the buffers
    ob_flush();
    flush();
}


?>
