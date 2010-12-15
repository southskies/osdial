# 12/14/2010

ALTER TABLE servers ADD server_profile ENUM('AIO','CONTROL','SQL','WEB','DIALER','ARCHIVE','OTHER') DEFAULT 'DIALER';##|##
 ##    Adds server profile to servers table.;

UPDATE servers SET server_profile='DIALER';##|##
 ##    Sets default server profile.;

INSERT INTO configuration (name,data) values('ArchiveReportPath','');##|##
 ##    Add ArchiveReportPath to configuration.;

UPDATE configuration SET data='' WHERE name LIKE 'Archive%';##|##
 ##    Clear Archive configuration.;

CREATE TABLE server_stats (
  server_ip VARCHAR(15) NOT NULL,
  server_timestamp DATETIME NOT NULL,
  host VARCHAR(255) NOT NULL,
  domain VARCHAR(255) NOT NULL,
  label VARCHAR(255) NOT NULL,
  load_one VARCHAR(6) NOT NULL,
  load_five VARCHAR(6) NOT NULL,
  load_ten VARCHAR(6) NOT NULL,
  load_procs VARCHAR(10) NOT NULL,
  cpu_count VARCHAR(2) NOT NULL,
  cpu_pct VARCHAR(10) NOT NULL,
  mem_total VARCHAR(20) NOT NULL,
  mem_free VARCHAR(20) NOT NULL,
  mem_pct VARCHAR(10) NOT NULL,
  swap_used VARCHAR(20) NOT NULL,
  update_time TIMESTAMP NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (server_ip)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;##|##
  ## Table for server stats.;

UPDATE system_settings SET version='2.2.9.081',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.9.081 and clearing last_update_check flag.;
