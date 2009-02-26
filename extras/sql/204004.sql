#Create a globalized config table.
CREATE TABLE configuration (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        fk_id VARCHAR(20) NOT NULL default '',
        name VARCHAR(50),
        data VARCHAR(100),
        index (name),
        index (fk_id,name)
) ENGINE=INNODB;

INSERT INTO configuration (name,data) values('ArchiveHostname','');
INSERT INTO configuration (name,data) values('ArchiveTransferMethod','');
INSERT INTO configuration (name,data) values('ArchivePort','');
INSERT INTO configuration (name,data) values('ArchiveUsername','');
INSERT INTO configuration (name,data) values('ArchivePassword','');
INSERT INTO configuration (name,data) values('ArchivePath','');
INSERT INTO configuration (name,data) values('ArchiveWebPath','');
INSERT INTO configuration (name,data) values('ArchiveMixFormat','');

UPDATE system_settings SET version='2.0.4-004';
