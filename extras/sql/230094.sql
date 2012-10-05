# 10/04/2012

ALTER TABLE osdial_list DROP INDEX phone_number, DROP INDEX list_id, DROP INDEX called_since_last_reset, DROP INDEX status, DROP INDEX gmt_offset_now, DROP INDEX postal_code, DROP INDEX phone_list, DROP INDEX list_phone, DROP INDEX list_status, DROP INDEX last_local_call_time, DROP INDEX entry_date, DROP INDEX modify_date, DROP INDEX area_code, DROP INDEX last_name, DROP INDEX external_key;##|##
  ## Removing ALL indexes on the osdial_list table.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list MODIFY phone_number varchar(12) NOT NULL DEFAULT '', MODIFY alt_phone varchar(12) NOT NULL DEFAULT '', MODIFY list_id bigint(14) unsigned NOT NULL DEFAULT '0', MODIFY called_since_last_reset varchar(4) NOT NULL DEFAULT 'N', MODIFY phone_code varchar(10) NOT NULL DEFAULT '', MODIFY status varchar(6) NOT NULL DEFAULT '', MODIFY gmt_offset_now decimal(4,2) NOT NULL DEFAULT '0.00', MODIFY postal_code varchar(10) NOT NULL DEFAULT '', MODIFY last_local_call_time datetime NOT NULL DEFAULT '2008-01-01 00:00:00', MODIFY entry_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00', MODIFY external_key varchar(100) NOT NULL DEFAULT '', MODIFY user varchar(20) NOT NULL DEFAULT '', MODIFY vendor_lead_code varchar(20) NOT NULL DEFAULT '', MODIFY source_id varchar(6) NOT NULL DEFAULT '', MODIFY state varchar(2) NOT NULL DEFAULT '', MODIFY country_code varchar(3) NOT NULL DEFAULT '', MODIFY gender enum('M','F','') NOT NULL DEFAULT '', MODIFY email varchar(70) NOT NULL DEFAULT '', MODIFY called_count smallint(5) unsigned NOT NULL DEFAULT '0', MODIFY cost float NOT NULL DEFAULT '0', MODIFY date_of_birth date NOT NULL DEFAULT '0000-00-00';##|##
  ## Optimizing indexed fields in osdial_list by removing NULLs.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX phone_number (phone_number);##|##
  ## Creating index in osdial_list 1/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX list_id (list_id);##|##
  ## Creating index in osdial_list 2/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX called_since_last_reset (called_since_last_reset(1));##|##
  ## Creating index in osdial_list 3/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX status (status);##|##
  ## Creating index in osdial_list 4/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX gmt_offset_now (gmt_offset_now);##|##
  ## Creating index in osdial_list 5/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX postal_code (postal_code);##|##
  ## Creating index in osdial_list 6/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX list_phone (list_id,phone_number);##|##
  ## Creating index in osdial_list 6/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX list_status (list_id,status);##|##
  ## Creating index in osdial_list 7/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX last_local_call_time (last_local_call_time);##|##
  ## Creating index in osdial_list 8/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX entry_date (entry_date);##|##
  ## Creating index in osdial_list 9/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX modify_date (modify_date);##|##
  ## Creating index in osdial_list 10/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX area_code (phone_number(3));##|##
  ## Creating index in osdial_list 11/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX last_name (last_name(3));##|##
  ## Creating index in osdial_list 11/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list ADD INDEX external_key (external_key);##|##
  ## Creating index in osdial_list 12/12.  This may take a awhile, do not interrupt!!!;

ALTER TABLE qc_transfers DROP INDEX qc_server_id, DROP INDEX qc_recording_id, DROP INDEX qc_server_id_2, DROP INDEX qc_recording_id_2, DROP INDEX qc_server_id_4;##|##
  ## Removing unusued indexes on qc_transfers.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_lists ADD INDEX campaign (campaign_id);##|##
  ## Creating index in osdial_lists.  This may take a awhile, do not interrupt!!!;

ALTER TABLE recording_log MODIFY filename varchar(100) NOT NULL DEFAULT '', MODIFY lead_id int(9) unsigned NOT NULL DEFAULT '0', MODIFY user varchar(20) NOT NULL DEFAULT '', MODIFY start_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00';##|##
 ## Optimizing recording_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE qc_recordings MODIFY filename varchar(255) NOT NULL DEFAULT '', MODIFY location varchar(255) NOT NULL DEFAULT '';##|##
 ## Optimizing qc_recordings by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE qc_transfers MODIFY status enum('NOTFOUND','PENDING','SUCCESS','FAILURE') NOT NULL DEFAULT 'PENDING', MODIFY last_attempt datetime NOT NULL DEFAULT '0000-00-00 00:00:00', MODIFY archive_filename varchar(255) NOT NULL DEFAULT '', MODIFY remote_location varchar(255) NOT NULL DEFAULT '';##|##
 ## Optimizing qc_transfers by removing NULL indexes.  This may take a awhile, do not interrupt!!!;




ALTER TABLE osdial_users MODIFY user varchar(20) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_users by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE qc_servers MODIFY active enum('Y','N') NOT NULL DEFAULT 'Y';##|##
 ## Optimizing qc_servers by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE park_log MODIFY parked_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00';##|##
 ## Optimizing park_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE call_log MODIFY channel varchar(100) NOT NULL DEFAULT '', MODIFY server_ip varchar(15) NOT NULL DEFAULT '', MODIFY caller_code varchar(20) NOT NULL DEFAULT '', MODIFY start_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00', MODIFY end_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00';##|##
 ## Optimizing call_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;
ALTER TABLE configuration MODIFY name varchar(50) NOT NULL DEFAULT '';##|##
 ## Optimizing configuration by removing NULL indexes.  This may take a awhile, do not interrupt!!!;
ALTER TABLE live_inbound_log MODIFY phone_ext varchar(40) DEFAULT '', MODIFY start_time datetime DEFAULT '0000-00-00 00:00:00';##|##
 ## Optimizing live_inbound_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;
ALTER TABLE osdial_agent_log MODIFY event_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00', MODIFY lead_id int(9) unsigned NOT NULL DEFAULT '0', MODIFY campaign_id varchar(20) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_agent_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;
ALTER TABLE osdial_log MODIFY call_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00';##|##
 ## Optimizing call_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;
ALTER TABLE osdial_auto_calls MODIFY uniqueid varchar(20) NOT NULL DEFAULT '', MODIFY callerid varchar(20) NOT NULL DEFAULT '', MODIFY call_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00';##|##
 ## Optimizing osdial_auto_calls by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_callbacks MODIFY lead_id int(9) unsigned NOT NULL DEFAULT '0', MODIFY status varchar(10) NOT NULL DEFAULT '', MODIFY callback_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00';##|##
 ## Optimizing osdial_callbasks by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_carrier_dids MODIFY carrier_id int(11) NOT NULL DEFAULT '0';##|##
 ## Optimizing osdial_carrier_dids by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_closer_log MODIFY call_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00', MODIFY phone_number varchar(12) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_closer_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_inbound_group_agents MODIFY user varchar(20) NOT NULL DEFAULT '', MODIFY group_id varchar(20) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_inbound_group_agents by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_ivr MODIFY campaign_id varchar(20) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_ivr by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_ivr_options MODIFY ivr_id int(11) NOT NULL DEFAULT '0';##|##
 ## Optimizing osdial_ivr_options by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_list_pins MODIFY entry_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00', MODIFY phone_number varchar(12) NOT NULL DEFAULT '', MODIFY lead_id int(9) unsigned NOT NULL DEFAULT '0';##|##
 ## Optimizing osdial_list_pins by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_live_agents MODIFY user varchar(20) NOT NULL DEFAULT '', MODIFY uniqueid varchar(20) NOT NULL DEFAULT '', MODIFY callerid varchar(20) NOT NULL DEFAULT '', MODIFY random_id int(8) unsigned NOT NULL DEFAULT '0', MODIFY last_call_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00', MODIFY last_call_finish datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE osdial_live_inbound_agents MODIFY group_id varchar(20)  NOT NULL DEFAULT '', MODIFY group_weight tinyint(1) unsigned NOT NULL DEFAULT '0';##|##
 ## Optimizing osdial_live_agents by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_xfer_log MODIFY call_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00', MODIFY phone_number varchar(20) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_xfer_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_user_log MODIFY user varchar(20) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_user_log by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_script_button_log MODIFY script_id varchar(40) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_script_button by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_postal_codes MODIFY country_code smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Optimizing osdial_postal_codes by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_phone_codes MODIFY country_code smallint(5) unsigned NOT NULL DEFAULT '0';##|##
 ## Optimizing osdial_phone_codes by removing NULL indexes.  This may take a awhile, do not interrupt!!!;

ALTER TABLE osdial_manager MODIFY uniqueid varchar(20) NOT NULL DEFAULT '', MODIFY callerid varchar(20) NOT NULL DEFAULT '';##|##
 ## Optimizing osdial_manager by removing NULL indexes.  This may take a awhile, do not interrupt!!!;


UPDATE system_settings SET version='2.3.0.094',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.3.0.094 and clearing last_update_check flag.;
