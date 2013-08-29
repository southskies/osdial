# 08/10/2013

ALTER TABLE phones MODIFY protocol enum('SIP','Zap','IAX2','EXTERNAL','DAHDI','WebSIP') DEFAULT 'WebSIP';##|##
 ## Added new WebSIP protocol;

UPDATE system_settings SET version='3.0.1.111',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.111 and clearing last_update_check flag.;
