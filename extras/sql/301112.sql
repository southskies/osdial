# 08/29/2013

ALTER TABLE system_settings MODIFY multicompany_admin VARCHAR(255) default 'admin';##|##
 ## Increase multicompany_admin size to 255.;

ALTER TABLE system_settings CHANGE acct_email_warning_from system_email VARCHAR(255) default '\"John Doe\" <johndoe@gmail.com>';##|##
 ## Change acct_email_warning_from to system_email.;

ALTER TABLE system_settings DROP COLUMN acct_email_warning_message;##|##
 ## Drop column acct_email_warning_message.;

ALTER TABLE system_settings DROP COLUMN acct_email_warning_subject;##|##
 ## Drop column acct_email_warning_subject.;

INSERT INTO `osdial_email_templates` VALUES ('MCABCREDWARN','Out of Credit Warning','Out of Credit Warning','localhost','25','','','\"OSDial System\" <system@osdial.org>','Out of Credit Warning','<p>You are nearly out of credit. You only have [:acct_minutes_remaining:] minutes available. If needed, you may wish to purchase more credit.</p>','You are nearly out of credit. You only have [:acct_minutes_remaining:] minutes available. If needed, you may wish to purchase more credit.','Y','ALL');##|##
 ## Template for Credit Warning;

INSERT INTO `osdial_email_templates` VALUES ('MCABACCTWARN','Near Expiration Warning','Near Expiration Warning','localhost','25','','','\"OSDial System\" <system@osdial.org>','New Expiration Warning','<p>You are within days of your account expiration. You only have [:acct_days_remaining:] days left. If needed, you may wish to purchase an additional block.</p>','You are within days of your account expiration. You only have [:acct_days_remaining:] days left. If needed, you may wish to purchase an additional block.','Y','ALL');##|##
 ## Template for Account Warning;

INSERT INTO `osdial_email_templates` VALUES ('MCABCREDEXP','Credit Expiration','Credit Expiration','localhost','25','','','system@osdial.org','Credit Expiration','<p>A previously purchase block of credit has expired.</p>\r\n<p>&nbsp;</p>\r\n<p>The total credit initial purchased was [:acct_credit_total:]. Of that, [:acct_credit_used:] had been used. The remaining [:acct_credit_expired:] is marked as expired, which was deducted from your total credit remaining on the account.</p>\r\n<p>&nbsp;</p>\r\n<p>You now have [:acct_minutes_remaining:] left on your account. If needed, you may wish to purchase more credit.</p>','A previously purchase block of credit has expired.\r\n\r\nThe total credit initial purchased was [:acct_credit_total:]. Of that, [:acct_credit_used:] has been used. The remaining [:acct_credit_expired:] is marked as expired, which was deducted from your total credit remaining on the account.\r\n\r\nYou now have [:acct_minutes_remaining:] left on your account. If needed, you may wish to purchase more credit.','Y','ALL');##|##
 ## Template for Credit Expiration;

INSERT INTO `osdial_email_templates` VALUES ('MCABCREDNO','No Credit Remaining','No Credit Remaining','localhost','25','','','\"OSDial System\" <system@osdial.org>','No Credit Remaining','<p>Your account does not have any credit remaining. If needed, you may wish to purchase more credit.</p>','Your account does not have any credit remaining. If needed, you may wish to purchase more credit.','Y','ALL');##|##
 ## Template for No Credit Remaining;

INSERT INTO `osdial_email_templates` VALUES ('MCABACCTEXP','Expired','Expired','localhost','25','','','\"OSDial System\" <system@osdial.org>','Expired','<p>Your account is now expired, as it was scheduled to expire on [:acct_end_date:]. If needed, you may wish to purchase an additional block.</p>','Your account is now expired, as it was scheduled to expire on [:acct_end_date:]. If needed, you may wish to purchase an additional block.','Y','ALL');##|##
 ## Template for Account Expiration;

INSERT INTO `osdial_email_templates` VALUES ('MCABNEWCOMP','New Company','New Company','localhost','25','','','system@osdial.org','New Company','<p>Your account as been configured.</p>\r\n<p>Login at [:system_web_url:].</p>\r\n<p>&nbsp;</p>\r\n<p>The administrator user account is \"[:company_prefix:]admin\", and the password is \"[:company_password_prefix:]admin\".</p>\r\n<p>&nbsp;</p>\r\n<p>Two initial agent and SIP phone accounts have also been setup:</p>\r\n<p>Username: [:company_prefix:]1001 Password: [:company_password_prefix:]1001</p>\r\n<p>Username: [:company_prefix:]1002 Password: [:company_password_prefix:]1002</p>','Your account as been configured.\r\n\r\nLogin at [:system_web_url:].\r\n\r\nThe administrator user account is \"[:company_prefix:]admin\", and the password is \"[:company_password_prefix:]admin\".\r\n\r\nTwo initial agent and SIP phone accounts have also been setup:\r\nUsername: [:company_prefix:]1001  Password: [:company_password_prefix:]1001\r\nUsername: [:company_prefix:]1002  Password: [:company_password_prefix:]1002','Y','ALL');##|##
 ## Template for New Company;

ALTER TABLE osdial_companies ADD COLUMN password_prefix VARCHAR(20) default '';##|##
 ## Field for generated password prefix;

ALTER TABLE servers ADD COLUMN server_domainname VARCHAR(255) default '';##|##
 ## The domain name of this server;

ALTER TABLE servers ADD COLUMN server_public_ip VARCHAR(15) default '';##|##
 ## The public IP for this server;

ALTER TABLE servers ADD COLUMN web_url VARCHAR(255) default '';##|##
 ## A modifiable URL that will override the computed 'http://server_id.server_domainname/';

ALTER TABLE system_settings ADD COLUMN mc_default_enable_system_phones ENUM('0','1') default '1';##|##
 ## Default enable_system_phones setting for new companies.;

UPDATE system_settings SET version='3.0.1.112',last_update_check=DATE_SUB(NOW(), INTERVAL 1 DAY);##|##
 ##    Updating database to version 3.0.1.112 and clearing last_update_check flag.;
