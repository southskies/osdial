# 01/29/2014

ALTER TABLE osdial_manager MODIFY action VARCHAR(50), MODIFY cmd_line_b VARCHAR(1024), MODIFY cmd_line_c VARCHAR(1024), MODIFY cmd_line_d VARCHAR(1024), MODIFY cmd_line_e VARCHAR(1024), MODIFY cmd_line_f VARCHAR(1024), MODIFY cmd_line_g VARCHAR(1024), MODIFY cmd_line_h VARCHAR(1024), MODIFY cmd_line_i VARCHAR(1024), MODIFY cmd_line_j VARCHAR(1024), MODIFY cmd_line_k VARCHAR(1024);##|##
 ## Increase size of cmd_line fields in order to hold all the data;

UPDATE osdial_extensions_data SET ext_appdata=REPLACE(ext_appdata,'${CALLERID(name)}','${FILENAME}') WHERE exten IN ('8309','8310','8311') AND ext_app='MixMonitor';##|##
 ## Do not use the CallerID to pass the filename.;

UPDATE system_settings SET version='3.0.1.119',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.119 and clearing last_update_check flag.;
