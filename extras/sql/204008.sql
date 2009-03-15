#index osdial closer/xfer logs
create index date_user ON osdial_xfer_log (call_date,user);
create index date_closer ON osdial_xfer_log (call_date,closer);
create index phone_number ON osdial_xfer_log (phone_number);
create index phone_number ON osdial_closer_log (phone_number);
create index date_user ON osdial_closer_log (call_date,user);

UPDATE system_settings SET version='2.0.4.008';
