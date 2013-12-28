# 12/28/2013

INSERT INTO osdial_statuses SET status='TIMEOT',status_name='Queue Timeout',selectable='N',human_answered='N',category='CONTACT';##|##
 ## Insert TIMEOT status into osdial_statuses.;

UPDATE system_settings SET version='3.0.1.118',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.118 and clearing last_update_check flag.;
