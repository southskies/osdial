<?php

/**
 * @file
 * site-specific configuration file.
 */

#
# Host for Asterisk Manager Interface
#
$ASTERISKMGR_DBHOST = "127.0.0.1";

#
# Standalone, for use without AMP
#   set use = true;
#   set asterisk_mgruser to Asterisk Call Manager username
#   set asterisk_mgrpass to Asterisk Call Manager password
#
$STANDALONE['use'] = true;
$STANDALONE['asterisk_mgruser'] = "cron";
$STANDALONE['asterisk_mgrpass'] = "1234";
#$STANDALONE['asteriskcdr_dbuser'] = "";
#$STANDALONE['asteriskcdr_dbpass'] = "";

###############################
# authentication settings
###############################
#
# For using the Call Monitor only
#   option: 0 - use Authentication, Voicemail, and Call Monitor
#           1 - use only the Call Monitor
#
$ARI_NO_LOGIN = 0;

#
# Admin only account - change defaults to prevent unauthorized access to call recordings
#
$ARI_ADMIN_USERNAME = "Rdssdfsdfffffsder32434rfds";
$ARI_ADMIN_PASSWORD = "adfsdfwhsahkjakjrkjkhjiord";
#
# Admin extensions
#   option: Comma delimited list of extensions
#
$ARI_ADMIN_EXTENSIONS = "";

#
# Authentication password to unlock cookie password
#   This must be all continuous and only letters and numbers
#   Change this password to prevent unauthorized access to cookie contents
#
$ARI_CRYPT_PASSWORD = "z1Mc6KRxA7Nw90dGjY5qLXhtrPgJOfeCaUmHvQT3yW8nDsI2VkEpiS4blFoBuZ";

###############################
# modules settings
###############################
#
# modules with admin only status (they will not be displayed for regular users)
#   option: Comma delimited list of module names (ie voicemail,callmonitor,help,settings)
#
$ARI_ADMIN_MODULES = "";

#
# disable modules (you can also just delete them from /recordings/modules without problems)
#   option: Comma delimited list of module names (ie voicemail,callmonitor,help,settings)
#
$ARI_DISABLED_MODULES = "";

#
# sets the default admin page
#   option: Comma delimited list of module names (ie voicemail,callmonitor,help,settings)
#
$ARI_DEFAULT_ADMIN_PAGE = "voicemail";

#
# sets the default user page
#   option: Comma delimited list of module names (ie voicemail,callmonitor,help,settings)
#
$ARI_DEFAULT_USER_PAGE = "voicemail";

#
# enables ajax page refresh
#   option: 0 - disable ajax page refresh
#           1 - enable ajax page refresh
#
$AJAX_PAGE_REFRESH_ENABLE = 1;

#
# sets the default user page
#   option: refresh time in 'minutes:seconds' (0 to inifinity) : (0 to 59)
#
$AJAX_PAGE_REFRESH_TIME ="01:00";
###############################
# voicemail settings
###############################
#
# voicemail config.
#
$ASTERISK_VOICEMAIL_CONF = "/etc/asterisk/voicemail.conf";

#
# To set to a specific context.  
#   If using default or more than one context then leave blank
#
$ASTERISK_VOICEMAIL_CONTEXT = "osdial";

#
# Location of asterisk voicemail recordings on server
#    Use semi-colon for multiple paths
#

$ASTERISK_VOICEMAIL_PATH = "/var/spool/asterisk/voicemail";

#
# valid mailbox folders
#
$ASTERISK_VOICEMAIL_FOLDERS = array();
$ASTERISK_VOICEMAIL_FOLDERS[0]['folder'] = "INBOX";
$ASTERISK_VOICEMAIL_FOLDERS[0]['name'] = _("INBOX");
$ASTERISK_VOICEMAIL_FOLDERS[1]['folder'] = "Family";
$ASTERISK_VOICEMAIL_FOLDERS[1]['name'] = _("Family");
$ASTERISK_VOICEMAIL_FOLDERS[2]['folder'] = "Friends";
$ASTERISK_VOICEMAIL_FOLDERS[2]['name'] = _("Friends");
$ASTERISK_VOICEMAIL_FOLDERS[3]['folder'] = "Old";
$ASTERISK_VOICEMAIL_FOLDERS[3]['name'] = _("Old");
$ASTERISK_VOICEMAIL_FOLDERS[4]['folder'] = "Work";
$ASTERISK_VOICEMAIL_FOLDERS[4]['name'] = _("Work");

###############################
# settings page settings
###############################
#
# protocol config.
#   config_file options: semi-colon delimited list of extensions
#
$ASTERISK_PROTOCOLS = array();
$ASTERISK_PROTOCOLS['iax']['table'] = "iax";
$ASTERISK_PROTOCOLS['iax']['config_files'] = "/etc/asterisk/iax.conf;/etc/asterisk/osdial_iax.conf;/etc/asterisk/osdial_iax_registrations.conf;/etc/asterisk/osdial_iax_carriers.conf;/etc/asterisk/osdial_iax_trunks.conf;/etc/asterisk/osdial_iax_servers.conf;/etc/asterisk/osdial_iax_phones.conf;/etc/asterisk/osdial_iax_custom.conf";
$ASTERISK_PROTOCOLS['sip']['table'] = "sip";
$ASTERISK_PROTOCOLS['sip']['config_files'] = "/etc/asterisk/sip.conf;/etc/asterisk/osdial_sip.conf;/etc/asterisk/osdial_sip_registrations.conf;/etc/asterisk/osdial_sip_carriers.conf;/etc/asterisk/osdial_sip_trunks.conf;/etc/asterisk/osdial_sip_servers.conf;/etc/asterisk/osdial_sip_phones.conf;/etc/asterisk/osdial_sip_custom.conf";
$ASTERISK_PROTOCOLS['zap']['table'] = "zap";
$ASTERISK_PROTOCOLS['zap']['config_files'] = "/etc/asterisk/zapata.conf;/etc/asterisk/chan_dahdi.conf";
# Settings for Follow-Me Select Boxes in seconds
#

$SETTINGS_PRERING_LOW = 4;
$SETTINGS_PRERING_HIGH = 30;
$SETTINGS_LISTRING_LOW = 6;
$SETTINGS_LISTRING_HIGH = 60;

$SETTINGS_FOLLOW_ME_LIST_MAX = 0;
$SETTINGS_ALLOW_VMX_SETTINGS = false;
#
# For setting 
#   option: 0 - do not show controls
#           1 - show controls
#
$SETTINGS_ALLOW_CALLFORWARD_SETTINGS = 0;
$SETTINGS_ALLOW_VOICEMAIL_SETTINGS = 1;
$SETTINGS_ALLOW_VOICEMAIL_PASSWORD_SET = 0;

#
# password length 
#   setting: number of characters required for changing voicemail password
#
$SETTINGS_VOICEMAIL_PASSWORD_LENGTH = 3;

#
# password exact length
#   option: 0 - do not require exact length when setting the password
#           1 - require exact length when setting the password
#
$SETTINGS_VOICEMAIL_PASSWORD_EXACT = 0;

#
# Default
#   option: 
#           ".WAV" - wav49 format
#	    ".wav" - wav format
#           ".gsm" - gsm format
#
$ARI_VOICEMAIL_AUDIO_FORMAT_DEFAULT = ".WAV";

#
# For setting 
#   option: 0 - do not show controls
#           1 - show controls
#
$SETTINGS_ALLOW_CALL_RECORDING_SET = 0;


$SETTINGS_ALLOW_PHONE_SETTINGS = 0;

#
# Maximum number of sound files that will be read before an error is generated indicating issues since
# too many files can be create problems but on some systems this may need to be increased.
#
$SETTINGS_MAX_FILES=3000;

?>
