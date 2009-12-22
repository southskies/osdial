# 12/22/2009

ALTER TABLE phones MODIFY ext_context VARCHAR(20) default 'osdial';##|##
 ##Security fix.  Change default context to be osdial and not "default".;

UPDATE phones SET ext_context='osdial' WHERE ext_context='default';##|##
 ##Security fix.  If any phones are still under the default context, move to osdial.

UPDATE system_settings SET version='2.2.0.038',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.0.038 and clearing last_update_check flag.;
