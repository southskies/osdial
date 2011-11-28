# 11/26/2011

CREATE TABLE osdial_cpa_log (
  id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
  callerid VARCHAR(20) NOT NULL DEFAULT '',
  uniqueid VARCHAR(20) NOT NULL DEFAULT '',
  lead_id INT(9) UNSIGNED NOT NULL DEFAULT '0',
  server_ip VARCHAR(15) NOT NULL,
  channel VARCHAR(100) NOT NULL,
  status ENUM('NEW','PROCESSED') DEFAULT 'NEW',
  cpa_result VARCHAR(50) NOT NULL DEFAULT 'Voice',
  cpa_detailed_result VARCHAR(255) NOT NULL DEFAULT '',
  cpa_call_id VARCHAR(100) NOT NULL DEFAULT '',
  cpa_reference_id VARCHAR(100) NOT NULL DEFAULT '',
  cpa_campaign_name VARCHAR(255) NOT NULL DEFAULT '',
  seconds DECIMAL(7,2) DEFAULT '0',
  event_date DATETIME,
  INDEX(callerid),
  INDEX(uniqueid),
  INDEX(lead_id)
) Engine=InnoDB;##|##
  ## Table for Sangoma Netborder CPA.;

ALTER TABLE osdial_auto_calls MODIFY status enum('SENT','RINGING','LIVE','XFER','PAUSED','CLOSER','BUSY','DISCONNECT','CONGESTION','CPA') default 'PAUSED';##|##
  ## Add CONGESTION AND CPA as statuses.;

INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('Fax',  'Fax Machine',               'Y','Y','CONTACT');##|##
  ## Add Status for Fax Machines.;

INSERT INTO osdial_status_categories (vsc_id,vsc_name,tovdad_display) VALUES('CPA','Netborder - Call Progress Analysis','N');##|##
  ## Add Status Category for Sangoma Netborder CPA.;

INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRB',  'CPA-Pre: Busy',               'N','N','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRB  - CPA-Pre: Busy".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRUNK','CPA-Pre: Unknown/Cancelled',  'N','N','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRUNK- CPA-Pre: Unknown/Cancelled".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRNA', 'CPA-Pre: No-Answer',          'N','N','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRNA - CPA-Pre: No-Answer".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRATB','CPA-Pre: All-Trunks-Busy',    'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRATB- CPA-Pre: All-Trunks-Busy".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRCR', 'CPA-Pre: Carrier Reject',     'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRCR - CPA-Pre: Carrier Reject".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRLR', 'CPA-Pre: License Reject',     'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRLR - CPA-Pre: Licence Reject".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRSNC','CPA-Pre: SIT-NC Temp Busy',   'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRSNC- CPA-Pre: SIT-NC Temp Busy".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRSRO','CPA-Pre: SIT-RO Temp Failure','N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRSRO- CPA-Pre: SIT-RO Temp Failure".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRSIC','CPA-Pre: SIT-IC Changed',     'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRSIC- CPA-Pre: SIT-IC Changed".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRSIO','CPA-Pre: SIT-IO Invalid',     'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRSIO- CPA-Pre: SIT-IO Invalid".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPRSVC','CPA-Pre: SIT-VC Unassigned',  'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPRSVC- CPA-Pre: SIT-VC Unassigned".;

INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPSHU', 'CPA-Post: Hangup',            'N','N','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPSHU - CPA-Post: Hangup".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPSAA', 'CPA-Post: Answering Machine', 'N','N','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPSAA - CPA-Post: Answering Machine".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPSFAX','CPA-Post: Fax Machine',       'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPSFAX- CPA-Post: Fax Machine".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPSUNK','CPA-Post: Unknown/Error',     'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPSUNK- CPA-Post: Unknown/Error".;
INSERT INTO osdial_statuses (status,status_name,selectable,human_answered,category) VALUES('CPSHMN','CPA-Post: Human Voice',       'N','Y','CPA');##|##
  ## Add Status for Sangoma Netborder CPA: "CPSHMN- CPA-Post: Human Voice".;

ALTER TABLE osdial_closer_log ADD INDEX uniqueid (uniqueid);##|##
  ## Add uniqueid index to osdial_closer_log for faster reporting.;

ALTER TABLE recording_log ADD INDEX time_user (start_time,user);##|##
  ## Add time_user index to recording_log for faster reporting.;

UPDATE system_settings SET version='2.3.0.092',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.3.0.092 and clearing last_update_check flag.;
