# 07/13/2009

ALTER TABLE osdial_list MODIFY called_since_last_reset VARCHAR(4) default 'N';##|##
 ##Modification to allow lead-recycling to work up to 999 times.;


CREATE TABLE osdial_carriers (
        id INT(11) NOT NULL auto_increment,
        active ENUM('Y','N') default 'Y',
        description VARCHAR(255),
        server_ip VARCHAR(15) NOT NULL default '--ALL--',
        protocol ENUM('SIP','Zap','IAX2','EXTERNAL','DAHDI') default 'SIP',
        outbound_context VARCHAR(50),
        outbound_config TEXT,
        inbound_context VARCHAR(50),
        inbound_config TEXT,
        registration_string VARCHAR(255),
        dial_rules TEXT,
        failover_active ENUM('Y','N') default 'Y',
        failover_on ENUM('CONGESTION','CHANUNAVAIL','BOTH') default 'CHANUNAVAIL',
        failover_to TEXT,
        primary key (id),
        unique (server_ip,outbound_context),
        unique (server_ip,inbound_context)
) ENGINE=InnoDB;##|##
 ##Addition of osdial_carriers table.
 ##
 ##This will allow administrators to add additional carriers directly through
 ##the management interface.;


CREATE TABLE osdial_carrier_dids (
        id INT(11) NOT NULL auto_increment,
        carrier_id INT(11),
        did CHAR(50) NOT NULL default '',
        did_action ENUM('INGROUP','EXTENSION','VOICEMAIL') default 'EXTENSION',
        extension VARCHAR(50) NOT NULL default 'default,9999',
        voicemail VARCHAR(20) NOT NULL default 'osdial',
        ingroup VARCHAR(20) NOT NULL default '',
	server_allocation ENUM('LO','LB','SO') default 'LO',
        park_file VARCHAR(100) NOT NULL default 'park',
	lookup_method ENUM('CID','CIDLOOKUP','CIDLOOKUPRL','CIDLOOKUPRC','CLOSER','ANI','ANILOOKUP','ANILOOKUPRL','3DIGITID','4DIGITID','5DIGITID','10DIGITID') default 'CID',
        initial_status VARCHAR(6) NOT NULL default 'INBND',
        default_list_id VARCHAR(15) NOT NULL default '998',
        default_phone_code VARCHAR(5) NOT NULL default '1',
        search_campaign VARCHAR(20) NOT NULL default '',
        primary key (id),
        unique (carrier_id,did)
) ENGINE=InnoDB;##|##
 ##Addition of osdial_carrier_dids table.
 ##
 ##This will allow administrators to add additional DIDs through the management
 ##interface.  It will also allow for a destination to be chosen.
 ##  Ie. To send the call to an Inbound-Group, to a Voicemail, or to an extension.;

INSERT INTO osdial_statuses values('INBND','Inbound Call','N','Y','SYSTEM');##|##
 ##Addition of INBND (Inbound Call) system status.;



UPDATE system_settings SET version='2.1.4.030';##|##
 ##Updating database to version 2.1.4.030;
UPDATE system_settings SET last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##Clearing last_update_check flag.;
