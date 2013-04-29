# 04/29/2013

ALTER TABLE osdial_carriers MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;##|##
 ## Add auto increment to osdial_carriers.;

UPDATE system_settings SET version='3.0.0.097',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.0.097 and clearing last_update_check flag.;
