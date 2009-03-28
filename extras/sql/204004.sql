#Quality Control Server / Rules / Transfer Log
CREATE TABLE qc_servers (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        name VARCHAR(50),
        description VARCHAR(255),
        transfer_method enum('FTP','SCP','SFTP') default 'FTP',
        host VARCHAR(100),
        username VARCHAR(50),
        password VARCHAR(50),
        home_path VARCHAR(255),
        location_template VARCHAR(255) DEFAULT '[campaign_id]/[date]',
        transfer_type enum('IMMEDIATE','BATCH','ARCHIVE') DEFAULT 'IMMEDIATE',
        archive enum('NONE','ZIP','TAR','TGZ','TBZ2') DEFAULT 'NONE',
        active enum('Y','N') DEFAULT 'Y',
        batch_time INT(2) UNSIGNED DEFAULT 0,
        batch_lastrun DATETIME,
        index (active)
) ENGINE=INNODB;
CREATE TABLE qc_server_rules (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        qc_server_id INT(10) UNSIGNED NOT NULL,
        query VARCHAR(255),
        index (qc_server_id)
) ENGINE=INNODB;

CREATE TABLE qc_recordings (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        recording_id INT(10) UNSIGNED NOT NULL,
        lead_id INT(10) UNSIGNED NOT NULL,
        filename VARCHAR(255),
        location VARCHAR(255),
        index (recording_id),
        index (lead_id),
        index (recording_id,lead_id),
        index (filename),
        index (location),
        unique (location,filename)
) ENGINE=INNODB;

CREATE TABLE qc_transfers (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        qc_server_id INT(10) UNSIGNED NOT NULL,
        qc_recording_id INT(10) UNSIGNED NOT NULL,
        status enum('NOTFOUND','PENDING','SUCCESS','FAILURE') DEFAULT 'PENDING',
        last_attempt DATETIME,
        archive_filename VARCHAR(255),
        remote_location VARCHAR(255),
        index (qc_server_id),
        index (qc_recording_id),
        index (status),
        index (qc_server_id,status),
        index (qc_recording_id,status),
        unique (qc_server_id,qc_recording_id),
        index (qc_server_id,qc_recording_id,status)
) ENGINE=INNODB;

UPDATE system_settings SET version='2.0.4.004';
