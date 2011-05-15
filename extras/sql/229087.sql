# 05/15/2011

ALTER TABLE system_settings ADD default_date_format VARCHAR(50) DEFAULT 'Y-m-d H:i:s';##|##
 ##    Add default date format for the dateToLocale functions.;

UPDATE system_settings SET default_date_format='Y-m-d H:i:s';##|##
 ##      Add default values.;

ALTER TABLE system_settings ADD use_browser_timezone_offset ENUM('Y','N') DEFAULT 'Y';##|##
 ##    Adds the ability to turn the browser based date/time offsets off and use the server gmt.;

UPDATE system_settings SET use_browser_timezone_offset='Y';##|##
 ##      Add default values.;

UPDATE system_settings SET version='2.2.9.087',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.087 and clearing last_update_check flag.;
