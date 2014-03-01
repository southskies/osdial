# 02/28/2014

UPDATE osdial_campaign_forms SET campaigns='-ALL-' WHERE campaigns IN ('','ALL');##|##
 ##Fix non-displayed additional forms.;

UPDATE system_settings SET version='3.0.1.122',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.122 and clearing last_update_check flag.;
