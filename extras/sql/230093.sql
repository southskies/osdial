# 08/29/2012

ALTER TABLE osdial_user_groups MODIFY allowed_campaigns varchar(2048) default ' -ALL-CAMPAIGNS- - -';##|##
  ## Allowed Campaigns permission should default to all.;

ALTER TABLE osdial_user_groups MODIFY allowed_scripts varchar(2048) default ' -ALL-SCRIPTS- -';##|##
  ## Allowed Scripts permission should default to all.;

ALTER TABLE osdial_user_groups MODIFY allowed_email_templates varchar(2048) default ' -ALL-EMAIL-TEMPLATES- -';##|##
  ## Allowed Email Templates permission should default to all.;

ALTER TABLE osdial_user_groups MODIFY allowed_ingroups varchar(2048) default ' -ALL-INGROUPS- -';##|##
  ## Allowed Ingroups permission should default to all.;

UPDATE osdial_user_groups SET allowed_campaigns=' -ALL-CAMPAIGNS- - -' WHERE allowed_campaigns IS NULL;##|##
  ## Update all NULL Allowed Campaigns permissions to have all.;

UPDATE osdial_user_groups SET allowed_scripts=' -ALL-SCRIPTS- -' WHERE allowed_scripts IS NULL;##|##
  ## Update all NULL Allowed Scripts permissions to have all.;

UPDATE osdial_user_groups SET allowed_email_templates=' -ALL-EMAIL-TEMPLATES- -' WHERE allowed_email_templates IS NULL;##|##
  ## Update all NULL Allowed Email Templates permissions to have all.;

UPDATE osdial_user_groups SET allowed_ingroups=' -ALL-INGROUPS- -' WHERE allowed_ingroups IS NULL;##|##
  ## Update all NULL Allowed Ingroups permissions to have all.;


UPDATE system_settings SET version='2.3.0.093',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 2.3.0.093 and clearing last_update_check flag.;
