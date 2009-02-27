Summary:	The OSDial predictive dialing suite.
Name:		osdial
Version:	2.1.0.000
Release:	1
License:	GPL
Group:		Applications/Telephony
Source0:	osdial-2.1.0.tgz
URL:		http://www.osdial.com
BuildRequires:  dialog
Requires:	php-pear
Requires:	php-mysql
Requires:	perl-MD5
Requires:	perl-Digest-SHA1
Requires:	perl-DBI
Requires:	perl-DBD-MySQL
Requires:	perl-Time-modules
Requires:	perl-Unicode-Map
Requires:	perl-Jcode
Requires:	perl-OLE-Storage_Lite
Requires:	perl-Proc-ProcessTable
Requires:	perl-IO-stringy
Requires:	perl-Spreadsheet-ParseExcel
Requires:	perl-Spreadsheet-WriteExcel
Requires:	perl-Net-Telnet
Requires:	perl-Net-Server
Requires:	readline
Requires:	sox
Requires:	lame
Requires:	screen
Requires:	ntp
Requires:	iftop
Requires:	ploticus
Requires:	balance
Requires:	subversion
Requires:	mtop
Requires:	perl-Curses
Requires:	perl-Asterisk
Requires:	htop
Requires:	sipsak
Requires:	ttyload
Requires:	php-eaccelerator
Requires:	sqlite2
Requires:	dialog
BuildArch:	noarch
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}

%description
OSDial is a predictive dialing system, an off-shoot of VICIDial/astGUIclient,
originally developed by Matt Florrel, to be used with the Asterisk PBX,
an open source PBX developed by Mark Spencer.

%prep
%setup -n osdial-%{version}

%build
install -dp %{buildroot}
#cp %{SOURCE1} %{buildroot}/../osdial-%{version}/.osdial.config

%install
%{__make} DESTDIR=%{buildroot} HTTPDUSER=asterisk install
#cp %{SOURCE2} %{buildroot}/opt/osdial/html
#cp %{SOURCE3} %{buildroot}/opt/osdial/html
mkdir -p %{buildroot}/etc/httpd/conf.d
mkdir -p %{buildroot}/opt/osdial/recordings/processing/wav
mkdir -p %{buildroot}/opt/osdial/recordings/processing/mp3
mkdir -p %{buildroot}/opt/osdial/recordings/completed
mkdir -p %{buildroot}/opt/osdial/reports
mkdir -p %{buildroot}/var/log/osdial
#cp %{SOURCE4} %{buildroot}/etc/httpd/conf.d/osdial.conf
#cp %{SOURCE5} %{buildroot}/var/lib/asterisk/sounds/conf.gsm
#cp %{SOURCE5} %{buildroot}/var/lib/asterisk/sounds/park.gsm
mkdir -p %{buildroot}/etc/cron.hourly
#TODO: Link AST_ntp_update...
#cp %{SOURCE6} %{buildroot}/etc/cron.hourly
cp ccsg/bin/AST_CLEAR_auto_calls.pl %{buildroot}/opt/osdial/bin
cp ccsg/bin/AST_qc_transfer.pl %{buildroot}/opt/osdial/bin
cp ccsg/bin/AST_sort_recordings.pl %{buildroot}/opt/osdial/bin
cp ccsg/bin/AST_audio_archive.pl %{buildroot}/opt/osdial/bin
cp ccsg/bin/AST_audio_compress.pl %{buildroot}/opt/osdial/bin
mv %{buildroot}/etc/osdial.conf %{buildroot}/etc/osdial.conf.orig
touch %{buildroot}/opt/osdial/html/admin/VMnow.txt
#rm %{buildroot}/opt/osdial/asterisk.cron

%{__mkdir_p} %{buildroot}/etc/asterisk/startup.d
echo "#!/bin/bash\nexport TTY=screen" > %{buildroot}/etc/asterisk/startup.d/tty_screen.sh


%clean
%{__rm} -rf %{buildroot}

%post
if [ ! "`grep OSDial /etc/rc.local`" ]; then
	echo >> /etc/rc.local
	echo "#===== BEGIN OSDial RAMdisk Additions ====" >> /etc/rc.local
	echo "/sbin/mkfs.ext3 /dev/ramdisk > /dev/null 2>&1" >> /etc/rc.local
	echo "/bin/mkdir /mnt/ramdisk > /dev/null 2>&1" >> /etc/rc.local
	echo "/bin/mount /dev/ramdisk /mnt/ramdisk > /dev/null 2>&1" >> /etc/rc.local
	echo "/bin/mkdir /mnt/ramdisk/VDmonitor > /dev/null 2>&1" >> /etc/rc.local
	echo "/bin/chown asterisk:asterisk /mnt/ramdisk/VDmonitor > /dev/null 2>&1" >> /etc/rc.local
	echo "/bin/ln -sf /mnt/ramdisk/VDmonitor /var/spool/asterisk/VDmonitor > /dev/null 2>&1" >> /etc/rc.local
	echo "#===== END OSDial RAMdisk Additions ====" >> /etc/rc.local
fi
if [ ! -f /var/www/html/index.html ]; then
	ln -s /opt/osdial/html/index.html /var/www/html/index.html
fi


%define _opt /opt

%files
%defattr(644,asterisk,asterisk,755)
#%doc CHANGES README
#%{perl_vendorlib}/Asterisk*
#%doc examples/*.agi
#%doc %{_mandir}/man3/*
%dir %{_opt}/osdial/reports
%dir %{_opt}/osdial/recordings
%dir %{_opt}/osdial/recordings/processing
%dir %{_opt}/osdial/recordings/processing/mp3
%dir %{_opt}/osdial/recordings/processing/wav
%dir %{_opt}/osdial/recordings/completed
%dir %{_var}/log/osdial
%attr(0755,asterisk,asterisk) %config(noreplace) /etc/cron.hourly/ntpdate
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/osdial.conf.orig
%attr(0755,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/startup.d/tty_screen.sh
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/httpd/conf.d/osdial.conf
%attr(0755,asterisk,asterisk) %{_usr}/bin/VDconfig
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CLEAR_auto_calls.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_audio_archive.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_audio_compress.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_qc_transfer.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_sort_recordings.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDcampaign_stats.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDpredictive.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ADMIN_area_code_populate.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ADMIN_backup.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ADMIN_restart_roll_logs.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ADMIN_update_server_ip.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_GSM_SALE_recordings.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_audio_1_move_VDonly.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_audio_1_move_mix.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_audio_2_compress.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_audio_3_ftp.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_mix_recordings.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_mix_recordings_BASIC.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_mix_recordings_GSM.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_mix_recordings_MP3.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_mix_recordings_MP3_DATE.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_mix_recordings_VDonly_DATE.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CRON_purge_recordings.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_DB_optimize.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_DB_tz_divide.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDadapt.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDauto_dial.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDauto_dial_FILL.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDhopper.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDhopper_MIXtest.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDremote_agents.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDsales_export.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_agent_day.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_agent_week.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_agent_week_tally.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_cleanup_agent_log.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_conf_update.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_flush_DBqueue.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_manager_kill_hung_congested.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_manager_listen.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_manager_send.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_phone_update.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_reset_mysql_vars.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_send_action_child.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_sourceID_summary_export.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_update.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_vm_update.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/FastAGI_log.pl
%attr(0644,asterisk,asterisk) %{_opt}/osdial/bin/GMT_USA_zip.txt
%attr(0644,asterisk,asterisk) %{_opt}/osdial/bin/MySQL_AST_CREATE_tables.sql
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/VDconfig
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/OSDIAL_DEDUPE_leads.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/OSDIAL_IN_new_leads_file.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/build_translation_www_files.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/AGI.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/Manager.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/Outgoing.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/QCall.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/Voicemail.pm
%attr(0644,asterisk,asterisk) %{_opt}/osdial/bin/phone_codes_GMT.txt
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/start_asterisk_boot.pl
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/conf.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/park.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/,.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/0.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/0.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/1.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/1.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/2.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/2.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/3.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/3.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/4.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/4.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/5.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/5.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/6.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/6.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/7.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/7.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/8.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/8.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/9.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/9.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/US_pol_survey_hello.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/US_pol_survey_transfer.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/US_reminder_callback.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/US_reminder_goodbye.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/US_reminder_message.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/US_reminder_options.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/US_thanks_no_contact.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/auth-thankyou.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/buzz.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/ding.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/enter.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/four_digit_id.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/generic_hold.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/hash.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/hash.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/hold_tone.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/ld_invalid_pin_number.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/ld_welcome_pin_number.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/leave.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/silence.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/sip-silence.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/star.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/star.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/vm-goodbye.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/vm-msgsaved.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/vm-rec-generic.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/vm-review.gsm
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/VD_amd.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/VD_amd_post.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/VD_auto_post_VERIFY.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-IVR_recording_verification.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDAD_ALL_inbound.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDAD_LB_transfer.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDAD_LO_transfer.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDAD_pin_IVR.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADautoREMINDER.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADautoREMINDERxfer.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADfixCXFER.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADinbound_NI_DNC_CIDlookup.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADlisten_DTMF.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADselective_CID.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADselective_CID_hangup.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADtransfer.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADtransferBROADCAST.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADtransferSURVEY.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADtransferTEST.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-dtmf.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-record_prompts.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/call_inbound.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/debug_speak.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/invalid_speak.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/park_CID.agi
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/ploticus
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/agent_reports
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/server_reports
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/active_list_refresh.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/astguiclient.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/call_log_display.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/conf_exten_check.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/dbconnect.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/inbound_popup.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/live_exten_check.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/manager_send.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/park_calls_display.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/vdc_db_query.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/osdial.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/voicemail_check.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/Thumbs.db
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_BLINK.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_BLINK_e.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_BLINK_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_BLINK_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_ON.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_ON_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_ON_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_ON.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_ON_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_ON_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_ON_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_active_lines.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_active_lines_el.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_active_lines_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_active_lines_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_astguiclient.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_astguiclient_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_conferences.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_conferences_el.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_conferences_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_conferences_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_main.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_main_el.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_main_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_main_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/blank.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/br.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/de.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/down.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/el.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/en.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/fr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/it.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/pt.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/remove.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/up.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_spacer.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF_p.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_RPLD_on.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_ammessage.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_ammessage_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_channel.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_channel_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_channel_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_code.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_header.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_header_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_header_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF_es-orig.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_pl.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysinactive_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_number.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_number_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_number_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_OFF.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_OFF_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_OFF_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_seconds.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_seconds_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_seconds_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_script.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_script_es.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_script_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_osdial.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_osdial_ptbr.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_buttons1.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_buttons2.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_MUTE.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_UNMUTE.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_down.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_down_off.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_up.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_up_off.gif
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_CLOSERstats.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_VDADstats.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_OSDIAL_hopperlist.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_admin_log_display.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_disposition.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_performance.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_performance_detail.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_time_sheet.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_time_sheet_archive.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_inboundEXTstats.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_inboundEXTstats_department.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_parkstats.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_server_performance.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDAD.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADall.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADallREC.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADallSUMMARY.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonpark.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/admin.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/admin_modify_lead.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/admin_search_lead.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/closer-fronter_popup.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/closer-fronter_popup2.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/closer.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/closer_dispo.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/closer_popup.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/dbconnect.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/group_hourly_stats.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/help.gif
%attr(0755,apache,asterisk) %{_opt}/osdial/html/admin/listloader.pl
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/listloaderMAIN.php
%attr(0755,apache,asterisk) %{_opt}/osdial/html/admin/listloader_rowdisplay.pl
%attr(0755,apache,asterisk) %{_opt}/osdial/html/admin/listloader_super.pl
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/log_test.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/new_listloader_superL.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/phone_stats.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/record_conf_1_hour.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/remote_dispo.php
%attr(0755,apache,asterisk) %{_opt}/osdial/html/admin/spreadsheet_sales_viewer.pl
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/user_stats.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/user_status.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/vdremote.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/osdial_sales_viewer.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/voice_lab.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/vtiger_search.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/welcome.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/VMnow.txt

%changelog
* Mon Feb 16 2009 Lott Caskey <lottc@fugitol.com> 2.1.0.000-1
- Initial package.
