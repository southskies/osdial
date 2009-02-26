#Create some custom indexes.
create index country_postal ON osdial_postal_codes (country_code,postal_code);
create index country_area ON osdial_phone_codes (country_code,areacode);
create index country_state ON osdial_phone_codes (country_code,state);
create index country_code ON osdial_phone_codes (country_code);
create index phone_list ON osdial_list (phone_number,list_id);

UPDATE system_settings SET version='2.0.4-003';
