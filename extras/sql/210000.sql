
# 02/11/2009
CREATE TABLE osdial_campaign_forms (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        campaigns VARCHAR(255) NOT NULL,
        name VARCHAR(15) NOT NULL,
	description VARCHAR(50) NOT NULL,
	description2 VARCHAR(50) NOT NULL,
        priority INT(10) UNSIGNED NOT NULL,
        deleted INT(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=INNODB;

CREATE TABLE osdial_campaign_fields (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        form_id INT(10) UNSIGNED NOT NULL,
        name VARCHAR(15) NOT NULL,
	description VARCHAR(50) NOT NULL,
	options VARCHAR(255) NOT NULL,
        length INT(2) NOT NULL DEFAULT '22',
        priority INT(10) UNSIGNED NOT NULL,
        deleted INT(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=INNODB;


CREATE TABLE osdial_list_fields (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        lead_id INT(10) UNSIGNED NOT NULL,
        field_id INT(10) UNSIGNED NOT NULL,
        value VARCHAR(255),
	index (lead_id),
	unique (lead_id, field_id)
) ENGINE=INNODB;

ALTER TABLE osdial_campaigns ADD web_form_address2 VARCHAR(255);
ALTER TABLE osdial_inbound_groups ADD web_form_address2 VARCHAR(255);

INSERT INTO osdial_campaign_forms values('1','ALL','CREDITCARD','Details as They Appear on CC','(if different)','1','0');
INSERT INTO osdial_campaign_fields values('1','1','NAME','Name on Card','','22','1','0');
INSERT INTO osdial_campaign_fields values('2','1','ADDRESS','Billing Address','','22','2','0');
INSERT INTO osdial_campaign_fields values('3','1','CITY','City','','22','3','0');
INSERT INTO osdial_campaign_fields values('4','1','STATE','State','','3','4','0');
INSERT INTO osdial_campaign_fields values('5','1','ZIP','ZIP','','10','5','0');
INSERT INTO osdial_campaign_fields values('6','1','TYPE','CC Type','VISA,Master Card,American Express','22','6','0');
INSERT INTO osdial_campaign_fields values('7','1','NUMBER','CC Number','','22','7','0');
INSERT INTO osdial_campaign_fields values('8','1','CVV','CVV Code','','5','8','0');

UPDATE system_settings SET version='2.1.0-000';
