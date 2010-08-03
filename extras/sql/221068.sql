# 07/30/2010


ALTER TABLE osdial_campaigns MODIFY auto_alt_dial ENUM('NONE','ALT_ONLY','ADDR3_ONLY','ALT_AND_ADDR3','ALT_ADDR3_AND_AFFAP') default 'NONE';##|##
 ##    Updates to osdial_campaigns for AFF alt_phone numbers.;

ALTER TABLE osdial_hopper ADD alt_dial ENUM('NONE','ALT','ADDR3','AFFAP1','AFFAP2','AFFAP3','AFFAP4','AFFAP5','AFFAP6','AFFAP7','AFFAP8','AFFAP9') default 'NONE';##|##
 ##    Updates to osdial_hopper for AFF alt_phone numbers.;

ALTER TABLE osdial_auto_calls ADD alt_dial ENUM('NONE','MAIN','ALT','ADDR3','AFFAP1','AFFAP2','AFFAP3','AFFAP4','AFFAP5','AFFAP6','AFFAP7','AFFAP8','AFFAP9') default 'NONE';##|##
 ##    Updates to osdial_auto_calls for AFF alt_phone numbers.;

INSERT INTO osdial_campaign_forms SET campaigns='ALL',name='AFFAP',description='Alternate Phone Numbers',description2='',priority='999',deleted='0';##|##
 ##    Add AFF Alternate Phone form.;

INSERT INTO osdial_campaign_fields SET form_id=(SELECT id FROM osdial_campaign_forms WHERE name='AFFAP' LIMIT 1),name='AFFAP1',description='Alt Phone 4',options='',length='15',priority='1',deleted='0';##|##
 ##    Add AFF Alternate Phone field 1.;

INSERT INTO osdial_campaign_fields SET form_id=(SELECT id FROM osdial_campaign_forms WHERE name='AFFAP' LIMIT 1),name='AFFAP2',description='Alt Phone 5',options='',length='15',priority='2',deleted='0';##|##
 ##    Add AFF Alternate Phone field 2.;

INSERT INTO osdial_campaign_fields SET form_id=(SELECT id FROM osdial_campaign_forms WHERE name='AFFAP' LIMIT 1),name='AFFAP3',description='Alt Phone 6',options='',length='15',priority='3',deleted='0';##|##
 ##    Add AFF Alternate Phone field 3.;

INSERT INTO osdial_campaign_fields SET form_id=(SELECT id FROM osdial_campaign_forms WHERE name='AFFAP' LIMIT 1),name='AFFAP4',description='Alt Phone 7',options='',length='15',priority='4',deleted='0';##|##
 ##    Add AFF Alternate Phone field 4.;

INSERT INTO osdial_campaign_fields SET form_id=(SELECT id FROM osdial_campaign_forms WHERE name='AFFAP' LIMIT 1),name='AFFAP5',description='Alt Phone 8',options='',length='15',priority='5',deleted='0';##|##
 ##    Add AFF Alternate Phone field 5.;

INSERT INTO osdial_campaign_fields SET form_id=(SELECT id FROM osdial_campaign_forms WHERE name='AFFAP' LIMIT 1),name='AFFAP6',description='Alt Phone 9',options='',length='15',priority='6',deleted='0';##|##
 ##    Add AFF Alternate Phone field 6.;

INSERT INTO osdial_campaign_fields SET form_id=(SELECT id FROM osdial_campaign_forms WHERE name='AFFAP' LIMIT 1),name='AFFAP7',description='Alt Phone 10',options='',length='15',priority='7',deleted='0';##|##
 ##    Add AFF Alternate Phone field 7.;


UPDATE system_settings SET version='2.2.1.068',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.068 and clearing last_update_check flag.;
