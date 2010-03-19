# 03/14/2010

ALTER TABLE osdial_companies ADD COLUMN default_server_ip VARCHAR(15) DEFAULT '127.0.0.1';##|##
 ##    Add default server_ip.;

ALTER TABLE osdial_companies ADD COLUMN default_local_gmt VARCHAR(6) DEFAULT '-5.00';##|##
 ##    Add default local_gmt.;

ALTER TABLE osdial_users ADD xfer_agent2agent enum('0','1') DEFAULT '0';##|##
 ##    Add permission option Agent2Agent XFER.;

UPDATE system_settings SET version='2.2.1.051',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.051 and clearing last_update_check flag.;
