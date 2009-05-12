# 05/11/2009
ALTER TABLE osdial_campaign_stats ADD status_category_hour_count_1 INT(9) UNSIGNED default '0';
ALTER TABLE osdial_campaign_stats ADD status_category_hour_count_2 INT(9) UNSIGNED default '0';
ALTER TABLE osdial_campaign_stats ADD status_category_hour_count_3 INT(9) UNSIGNED default '0';
ALTER TABLE osdial_campaign_stats ADD status_category_hour_count_4 INT(9) UNSIGNED default '0';


UPDATE system_settings SET version='2.1.1.008';
