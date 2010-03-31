# 03/31/2010

INSERT INTO osdial_lists SET list_id='10',list_name='PBX-IN',list_description='PBX/External Inbound',campaign_id='PBX-IN';##|##
 ##    Add list for pbx-in traffic.;

INSERT INTO osdial_users SET user='PBX-IN',pass='do not delete',user_level='0',user_group='ADMIN';##|##
 ##    Add user for pbx-in traffic.;

INSERT INTO osdial_lists SET list_id='11',list_name='PBX-OUT',list_description='PBX/External Outbound',campaign_id='PBX-OUT';##|##
 ##    Add list for pbx-out traffic.;

INSERT INTO osdial_users SET user='PBX-OUT',pass='do not delete',user_level='0',user_group='ADMIN';##|##
 ##    Add user for pbx-out traffic.;

ALTER TABLE recording_log MODIFY filename VARCHAR(100) DEFAULT '';##|##
 ##    Increase filename size in recording_log.;

UPDATE system_settings SET version='2.2.1.052',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.052 and clearing last_update_check flag.;
