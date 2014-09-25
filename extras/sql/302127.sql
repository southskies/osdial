# 09/25/2014

INSERT INTO osdial_campaign_fields SET form_id=(SELECT id FROM osdial_campaign_forms WHERE name='CREDITCARD' LIMIT 1),name='EXP',description='Exp Date',options='',length='7',priority='9',deleted='0';##|##
 ##    Add EXP - Credit Card Expiration Date.;

UPDATE system_settings SET version='3.0.2.127',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.2.127 and clearing last_update_check flag.;
