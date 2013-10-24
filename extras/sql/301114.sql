# 10/24/2013

UPDATE osdial_extensions_data SET ext_appdata='silence&ding' WHERE ext_context='osdial' AND exten='8304' and ext_app='Playback' AND ext_appdata='ding';##|##
 ## Play silence before short ding sound.;

UPDATE system_settings SET version='3.0.1.114',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.114 and clearing last_update_check flag.;
