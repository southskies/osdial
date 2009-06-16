# 06/15/2009
ALTER TABLE osdial_ivr ENGINE=InnoDB;
ALTER TABLE osdial_ivr_options ENGINE=InnoDB;


UPDATE system_settings SET version='2.1.2.017';
