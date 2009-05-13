#!/bin/bash

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

# Conversion script to rename table from upstream provider to OSDial.

db=asterisk

echo
echo " WARNING: Depending on the size of your database, this could take"
echo "          several hours to complete!"
echo

echo -n "  Creating osdial database:"
echo "CREATE DATABASE osdial;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then
	echo "FAILURE, database already exists.!"
	exit 1
fi
echo "DONE"
echo

echo -n "  Renaming tables:"
echo "RENAME TABLE $db.vicidial_agent_log             TO osdial.osdial_agent_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then
	echo "FAILURE, error renaming first table, bailing..."
	exit 1
fi
echo -n "."
echo "RENAME TABLE $db.phones                         TO osdial.phones;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.servers                        TO osdial.servers;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.live_channels                  TO osdial.live_channels;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.live_sip_channels              TO osdial.live_sip_channels;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.parked_channels                TO osdial.parked_channels;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.conferences                    TO osdial.conferences;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.recording_log                  TO osdial.recording_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.live_inbound                   TO osdial.live_inbound;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.inbound_numbers                TO osdial.inbound_numbers;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.server_updater                 TO osdial.server_updater;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.call_log                       TO osdial.call_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.park_log                       TO osdial.park_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.live_inbound_log               TO osdial.live_inbound_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.web_client_sessions            TO osdial.web_client_sessions;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.server_performance             TO osdial.server_performance;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.phone_favorites                TO osdial.phone_favorites;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.system_settings                TO osdial.system_settings;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.configuration                  TO osdial.configuration;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.qc_servers                     TO osdial.qc_servers;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.qc_server_rules                TO osdial.qc_server_rules;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.qc_recordings                  TO osdial.qc_recordings;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.qc_transfers                   TO osdial.qc_transfers;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaign_forms        TO osdial.osdial_campaign_forms;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaign_fields       TO osdial.osdial_campaign_fields;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_list_fields           TO osdial.osdial_list_fields;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_auto_calls            TO osdial.osdial_auto_calls;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_call_times            TO osdial.osdial_call_times;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_callbacks             TO osdial.osdial_callbacks;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaign_agents       TO osdial.osdial_campaign_agents;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaign_hotkeys      TO osdial.osdial_campaign_hotkeys;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaign_server_stats TO osdial.osdial_campaign_server_stats;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaign_stats        TO osdial.osdial_campaign_stats;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaign_statuses     TO osdial.osdial_campaign_statuses;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaigns             TO osdial.osdial_campaigns;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_campaigns_list_mix    TO osdial.osdial_campaigns_list_mix;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_closer_log            TO osdial.osdial_closer_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_conferences           TO osdial.osdial_conferences;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_dnc                   TO osdial.osdial_dnc;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_hopper                TO osdial.osdial_hopper;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_inbound_group_agents  TO osdial.osdial_inbound_group_agents;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_inbound_groups        TO osdial.osdial_inbound_groups;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_ivr                   TO osdial.osdial_ivr;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_lead_filters          TO osdial.osdial_lead_filters;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_lead_recycle          TO osdial.osdial_lead_recycle;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_list                  TO osdial.osdial_list;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_list_pins             TO osdial.osdial_list_pins;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_lists                 TO osdial.osdial_lists;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_live_agents           TO osdial.osdial_live_agents;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_live_inbound_agents   TO osdial.osdial_live_inbound_agents;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_log                   TO osdial.osdial_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_manager               TO osdial.osdial_manager;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_pause_codes           TO osdial.osdial_pause_codes;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_phone_codes           TO osdial.osdial_phone_codes;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_postal_codes          TO osdial.osdial_postal_codes;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_remote_agents         TO osdial.osdial_remote_agents;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_scripts               TO osdial.osdial_scripts;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_server_trunks         TO osdial.osdial_server_trunks;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_state_call_times      TO osdial.osdial_state_call_times;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_stations              TO osdial.osdial_stations;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_status_categories     TO osdial.osdial_status_categories;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_statuses              TO osdial.osdial_statuses;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_user_groups           TO osdial.osdial_user_groups;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_user_log              TO osdial.osdial_user_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_users                 TO osdial.osdial_users;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "RENAME TABLE $db.vicidial_xfer_log              TO osdial.osdial_xfer_log;" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "DONE."
echo

echo -n "  Renaming fields:"
echo "ALTER TABLE osdial.phones CHANGE COLUMN VICIDIAL_park_on_extension OSDIAL_park_on_extension VARCHAR(10) default '8301';" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "ALTER TABLE osdial.phones CHANGE COLUMN VICIDIAL_park_on_filename OSDIAL_park_on_filename VARCHAR(10) default 'park';" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "ALTER TABLE osdial.phones CHANGE COLUMN VICIDIAL_web_URL OSDIAL_web_URL VARCHAR(255) default 'http://localhost/test_VICIDIAL_output.php';" | mysql -u root > /dev/null 2>&1
if [ "$?" -gt 0 ]; then echo -n "x"; else echo -n "."; fi
echo "DONE"
echo
echo " You can proceed with the SQL update process."
