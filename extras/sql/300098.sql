# 06/07/2013

ALTER TABLE osdial_media MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_media.;

ALTER TABLE osdial_media_data MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_media_data.;

ALTER TABLE configuration MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to configuration.;

ALTER TABLE osdial_companies MODIFY `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_companies.;

ALTER TABLE osdial_auto_calls MODIFY `auto_call_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_auto_calls.;

ALTER TABLE osdial_agent_log MODIFY `agent_log_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_agent_log.;

ALTER TABLE osdial_callbacks MODIFY `callback_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_callbacks.;

ALTER TABLE osdial_campaign_fields MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_campaign_fields.;

ALTER TABLE osdial_campaign_forms MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_campaign_forms.;

ALTER TABLE osdial_carrier_dids MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_carrier_dids.;

ALTER TABLE osdial_ivr MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_ivr.;

ALTER TABLE osdial_ivr_options MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_ivr_options.;

ALTER TABLE osdial_closer_log MODIFY `closecallid` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_closer_log.;

ALTER TABLE osdial_cpa_log MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_cpa_log.;

ALTER TABLE osdial_list_fields MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_list_fields.;

ALTER TABLE osdial_list_pins MODIFY `pins_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_list_pins.;

ALTER TABLE osdial_live_agents MODIFY `live_agent_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_live_agents.;

ALTER TABLE osdial_hopper MODIFY `hopper_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_hopper.;

ALTER TABLE osdial_lead_recycle MODIFY `recycle_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_lead_recycle.;

ALTER TABLE osdial_list MODIFY `lead_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_list.;

ALTER TABLE osdial_remote_agents MODIFY `remote_agent_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_remote_agents.;

ALTER TABLE osdial_script_button_log MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_script_button_log.;

ALTER TABLE osdial_tts MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_tts.;

ALTER TABLE osdial_user_log MODIFY `user_log_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_user_log.;

ALTER TABLE osdial_users MODIFY `user_id` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_users.;

ALTER TABLE osdial_xfer_log MODIFY `xfercallid` int(9) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_xfer_log.;

ALTER TABLE qc_recordings MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to qc_recordings.;

ALTER TABLE qc_server_rules MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to qc_server_rules.;

ALTER TABLE qc_servers MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to qc_servers.;

ALTER TABLE qc_transfers MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to qc_transfers.;

ALTER TABLE recording_log MODIFY `recording_id` int(10) unsigned NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to recording_log.;

ALTER TABLE osdial_callbacks MODIFY `modify_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to osdial_callbacks.;

ALTER TABLE osdial_campaign_server_stats MODIFY `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to osdial_campaign_server_stats.;

ALTER TABLE osdial_campaign_stats MODIFY `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to osdial_campaign_stats.;

ALTER TABLE osdial_auto_calls MODIFY `last_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to osdial_auto_calls.;

ALTER TABLE osdial_campaign_agent_stats MODIFY `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to osdial_campaign_agent_stats.;

ALTER TABLE osdial_list MODIFY `modify_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to osdial_list.;

ALTER TABLE osdial_live_agents MODIFY `last_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to osdial_live_agents.;

ALTER TABLE server_stats MODIFY `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to server_stats.;

ALTER TABLE server_updater MODIFY `sql_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;##|##
 ## Add current timestamp on update to server_updater.;

UPDATE system_settings SET version='3.0.0.098',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.0.098 and clearing last_update_check flag.;
