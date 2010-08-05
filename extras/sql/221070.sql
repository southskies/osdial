# 08/04/2010


ALTER TABLE system_settings ADD intra_server_protocol ENUM('IAX2','SIP') default 'SIP';##|##
 ##    Selection for choosing the desired intra-server protocol.;

UPDATE system_settings SET version='2.2.1.070',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.1.070 and clearing last_update_check flag.;
