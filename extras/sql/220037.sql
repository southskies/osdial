# 11/24/2009

ALTER TABLE osdial_campaigns ADD campaign_lastcall DATETIME default '2008-01-01 00:00:00';##|##
 ##Added campaign_lastcall to campaign records.  Mainly to provide support for regeneration
 ##of campaign_stats when there is activity within the campaign.;

UPDATE system_settings SET version='2.2.0.037',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.2.0.037 and clearing last_update_check flag.;
