# 09/18/2009

ALTER TABLE qc_servers MODIFY transfer_method enum('FTP','SCP','SFTP','FTPA') default 'FTP';##|##
 ##Adds ability to select passive or active ftp connections;


UPDATE system_settings SET version='2.1.5.033',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.1.5.033 and clearing last_update_check flag.;
