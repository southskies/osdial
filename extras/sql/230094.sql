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

UPDATE system_settings SET version='2.3.0.094',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.3.0.094 and clearing last_update_check flag.;
