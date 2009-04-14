Summary:	The OSDial predictive dialing suite.
Name:		osdial
Version:	2.1.0.002
Release:	7
License:	GPL
Group:		Applications/Telephony
Source0:	osdial-%{version}.tgz
URL:		http://www.osdial.com
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires:	osdial-asterisk
Requires:	osdial-bin
Requires:	osdial-sql
Requires:	osdial-web
Obsoletes:	osdial-installcd
Obsoletes:	osdial-livecd
BuildArch:	noarch
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}

%description
OSDial is a predictive dialing system, an off-shoot of VICIdial,
originally developed by Matt Florrel, to be used with the Asterisk
PBX, an open source PBX developed by Mark Spencer.
This packages provides:
              osdial-asterisk
              osdial-bin
              osdial-web
              osdial-sql 

%package bin
Summary:	OSDial backend scripts
Group:		Applications/Telephony
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
Requires:	sqlite2
Requires:	dialog
BuildArch:	noarch

%description bin
OSDial backend scripts, needed by web, sql, etc.

%package sql
Summary: 	OSDial SQL files and update scripts.
Group:		Applications/Telephony
Requires:	osdial-bin
Requires:	perl-DBI
Requires:	perl-DBD-MySQL
Requires:	mysql-server
BuildArch:	noarch

%description sql
OSDial SQL file and update scripts.  Provides a method of
automatically updating the OSDial database, both through the
install package and RPM.

%package web
Summary:	OSDial user interface files
Group:		Applications/Telephony
Requires:	osdial-bin
Requires:	php-pear
Requires:	php-mysql
Requires:	ploticus
Requires:	php-eaccelerator
BuildArch:      noarch

%description web
OSDial user interface files.  Mainly the php scripts, directory
structure and other supporting files.

%package asterisk
Summary:        OSDial generic asterisk configuration.
Group:          Applications/Telephony
Requires:       asterisk12
Requires:       zaptel12
Requires:       wanpipe-util
Requires:       kernel-PAE-module-wanpipe
Requires:       kernel-PAE-module-zaptel
Requires:       osdial-bin
Requires:       gawk
Obsoletes:	osdial-config
BuildArch:      noarch

%description asterisk
The is a generic configuration that should work out of box for most clients.
SQL and web configuration has been moved to osdial-sql and osdial-web.

%package debuginfo
Summary:	OSDial debuginfo
Group:		Applications/Telephony
BuildArch:      i386

%description debuginfo
OSDial debuginfo

%prep
%{__rm} -rf %{buildroot}
%setup -n osdial-%{version}

%build
install -dp %{buildroot}
#cp %{SOURCE1} %{buildroot}/../osdial-%{version}/.osdial.config

%install
%{__make} DESTDIR=%{buildroot} HTTPDUSER=asterisk install
mkdir -p %{buildroot}/etc/httpd/conf.d
mkdir -p %{buildroot}/etc/init.d
mkdir -p %{buildroot}/opt/osdial/recordings/processing/wav
mkdir -p %{buildroot}/opt/osdial/recordings/processing/mp3
mkdir -p %{buildroot}/opt/osdial/recordings/completed
mkdir -p %{buildroot}/opt/osdial/reports
mkdir -p %{buildroot}/var/log/osdial
cp extras/httpd-osdial.conf %{buildroot}/etc/httpd/conf.d/osdial.conf
cp extras/osdial.init %{buildroot}/etc/init.d/osdial
mkdir -p %{buildroot}/etc/cron.hourly
ln -s /opt/osdial/bin/AST_ntp_update.sh %{buildroot}/etc/cron.hourly
#mv %{buildroot}/etc/osdial.conf %{buildroot}/etc/osdial.conf.orig
touch %{buildroot}/opt/osdial/html/admin/VMnow.txt

# copy in asterisk configs
%{__mkdir_p} %{buildroot}/etc/asterisk/startup.d
echo "#!/bin/bash\nexport TTY=screen" > %{buildroot}/etc/asterisk/startup.d/tty_screen.sh
cp docs/conf_examples/*.conf %{buildroot}/etc/asterisk
cp docs/conf_examples/README.osdial %{buildroot}/etc/asterisk

%clean
%{__rm} -rf %{buildroot}

%post
	# Network check, needed for LiveCD/MySQL
	if [ ! -f /etc/sysconfig/network ]; then
        	echo "    osdial-config: Must be a LiveCD, making /etc/sysconfig/network"
        	echo "NETWORKING=yes" > /etc/sysconfig/network
        	echo "NETWORKING_IPV6=no" >> /etc/sysconfig/network
        	echo "HOSTNAME=osdial-livecd.callcentersg.com" >> /etc/sysconfig/network
        	echo 1 > /etc/osdial.livecd
	fi
	# /etc/hosts check, needed for LiveCD/MySQL/Apache
	if [ ! -f /etc/hosts ]; then
        	echo "    osdial-config: Must be a LiveCD, making /etc/hosts"
        	echo "127.0.0.1 osdial-livecd.callcentersg.com osdial-livecd osdial" > /etc/hosts
        	echo "127.0.0.1 localhost.localdomain localhost" >> /etc/hosts
        	echo "::1       localhost6.localdomain6 localhost6" >> /etc/hosts
        	echo 1 > /etc/osdial.livecd
	fi
	# Its not a LiveCD, so lets put a different hostname on it.
	if [ ! -f /etc/osdial.livecd ]; then
        	if [ -f /etc/sysconfig/network ]; then
        	        %{__perl} -pi -e 's|^HOSTNAME=localhost.*|HOSTNAME=osdial.callcentersg.com|' /etc/sysconfig/network
        	fi
        	if [ -f /etc/hosts ]; then
                	HOSTS="`cat /etc/hosts`"
                	echo "127.0.0.1         osdial.callcentersg.com osdial" > /etc/hosts
                	echo "$HOSTS" >> /etc/hosts
        	fi
	fi

%post bin
        /sbin/chkconfig osdial on > /dev/null 2>&1
	# Make sure SELINUX didn't get turned on...
	if [ -f /etc/selinux/config ]; then
        	SELINUX="`grep '^SELINUX=' /etc/selinux/config | awk -F= '{ print $2 }'`"
        	if [ "$SELINUX" == "enforcing" ]; then
                	echo "    osdial-config: SELINUX is set to ENFORCING!"
                	echo "                         Setting to SELINUX=disabled in /etc/selinux/config."
                	echo "                         You must reboot after install!!!"
                	%{__perl} -pi -e 's|^SELINUX=.*|SELINUX=disabled|' /etc/selinux/config
        	elif [ "$SELINUX" == "permissive" ]; then
                	echo "    osdial-config: SELINUX is set to PERMISSIVE!"
                	echo "                         This should be fine, but if you have problems"
                	echo "                         logging in, you should modify /etc/selinux/config,"
                	echo "                         change the line SELINUX=permissive to SELINUX=disabled"
                	echo "                         and reboot the server."
        	elif [ -z "$SELINUX" ]; then
                	echo "    osdial-config: SELINUX directive not found!"
                	echo "                         Adding SELINUX=disabled to /etc/selinux/config."
                	echo "SELINUX=disabled" >> /etc/selinux/config
        	elif [ "$SELINUX" != "disabled" ]; then
                	echo "    osdial-config: SELINUX is set to an UNKNOWN MODE! ($SELINUX)"
                	echo "                         Setting to SELINUX=disabled in /etc/selinux/config."
                	echo "                         You must reboot after install!!!"
                	%{__perl} -pi -e 's|^SELINUX=.*|SELINUX=disabled|' /etc/selinux/config
        	fi
	fi


%post sql
	/sbin/chkconfig mysqld on > /dev/null 2>&1
	# Apply OSDial SQL changes to /etc/my.cnf
	if [ ! "`grep OSDIAL /etc/my.cnf`" ]; then
		MCNF="old_passwords=1\n\n"
		MCNF="${MCNF}#===== BEGIN OSDIAL my.cnf Additions =====\n"
		MCNF="${MCNF}skip-name-resolve\n"
		MCNF="${MCNF}innodb_file_per_table\n"
		MCNF="${MCNF}innodb_data_home_dir = /var/lib/mysql/\n"
		MCNF="${MCNF}innodb_data_file_path = ibdata1:10M:autoextend\n"
		MCNF="${MCNF}innodb_log_group_home_dir = /var/lib/mysql/\n"
		MCNF="${MCNF}innodb_log_arch_dir = /var/lib/mysql/\n"
		MCNF="${MCNF}innodb_buffer_pool_size = 16M\n"
		MCNF="${MCNF}innodb_additional_mem_pool_size = 2M\n"
		MCNF="${MCNF}innodb_log_file_size = 5M\n"
		MCNF="${MCNF}innodb_log_buffer_size = 8M\n"
		MCNF="${MCNF}innodb_flush_log_at_trx_commit = 1\n"
		MCNF="${MCNF}innodb_lock_wait_timeout = 50\n"
		MCNF="${MCNF}#===== END OSDIAL my.cnf Additions =====\n\n"
		/usr/bin/perl -pi -e "s|old_passwords=1|$MCNF|" /etc/my.cnf
		# Restart mysql
		/sbin/service mysqld restart
	fi
	# Run update script.
	/opt/osdial/bin/sql/upgrade_sql.pl
	# If it didn't get created, assume it is an installcd
	if [ ! -d "/var/lib/mysql/osdial" ]; then
		echo "OSDIAL_MYSQL_INSTALL=YES" >> /etc/sysconfig/osdial
	fi
	# cpuspeed can do bad things to ISDN/T1 cards
	if [ -f /etc/rc3.d/S06cpuspeed ]; then
                	echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
                	/etc/init.d/cpuspeed stop > /dev/null 2>&1
                	/sbin/chkconfig cpuspeed off > /dev/null 2>&1
	fi
	# We don't need cups
	if [ -f /etc/rc3.d/S56cups ]; then
                	echo "    osdial-config: CUPS detected, disabling!"
                	/etc/init.d/cups stop > /dev/null 2>&1
                	/sbin/chkconfig cups off > /dev/null 2>&1
	fi

%post web
	/sbin/chkconfig httpd on > /dev/null 2>&1
	if [ -f "/var/www/html/index.html" -a -n "`grep osdial /var/www/html/index.html`" ]; then
		mv /var/www/html/index.html /var/www/html/index.html.bak
	fi
	if [ ! -f /var/www/html/index.php ]; then
		ln -s /opt/osdial/html/index.php /var/www/html/index.php
	fi
	# modify php.ini for our defaults.
	if [ ! "`grep OSDIAL /etc/php.ini`" ]; then
		perl -pi -e "s|^max_execution_time = 30     ; Maximum execution|max_execution_time = 300000 ; Maximum execution|" /etc/php.ini
		perl -pi -e "s|^max_input_time = 60    ; Maximum amount of time|max_input_time = 600000 ; Maximum amount of time|" /etc/php.ini
		perl -pi -e "s|^memory_limit = 16M      ; Maximum amount of mem|memory_limit = 64M      ; Maximum amount of mem|" /etc/php.ini
		perl -pi -e "s|^;error_reporting = E_ALL \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_NOTICE|" /etc/php.ini
		perl -pi -e "s|^error_reporting  =  E_ALL|;error_reporting  =  E_ALL|" /etc/php.ini
		perl -pi -e "s|^post_max_size = 8M|post_max_size = 100M|" /etc/php.ini
		perl -pi -e "s|^upload_max_filesize = 2M|upload_max_filesize = 100M|" /etc/php.ini
		echo "; OSDIAL: modified" >> /etc/php.ini
		/sbin/service httpd restart
	fi
	# cpuspeed can do bad things to ISDN/T1 cards
	if [ -f /etc/rc3.d/S06cpuspeed ]; then
                	echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
                	/etc/init.d/cpuspeed stop > /dev/null 2>&1
                	/sbin/chkconfig cpuspeed off > /dev/null 2>&1
	fi
	# We don't need cups
	if [ -f /etc/rc3.d/S56cups ]; then
                	echo "    osdial-config: CUPS detected, disabling!"
                	/etc/init.d/cups stop > /dev/null 2>&1
                	/sbin/chkconfig cups off > /dev/null 2>&1
	fi

%post asterisk
	/sbin/chkconfig asterisk on > /dev/null 2>&1
	# Put in some ramdisk on the dialer.
	if [ ! "`grep OSDIAL /etc/rc.local`" ]; then
		echo >> /etc/rc.local
		echo "#===== BEGIN OSDIAL RAMdisk Additions ====" >> /etc/rc.local
		echo "/sbin/mkfs.ext3 /dev/ramdisk > /dev/null 2>&1" >> /etc/rc.local
		echo "/bin/mkdir /mnt/ramdisk > /dev/null 2>&1" >> /etc/rc.local
		echo "/bin/mount /dev/ramdisk /mnt/ramdisk > /dev/null 2>&1" >> /etc/rc.local
		echo "/bin/mkdir /mnt/ramdisk/VDmonitor > /dev/null 2>&1" >> /etc/rc.local
		echo "/bin/chown asterisk:asterisk /mnt/ramdisk/VDmonitor > /dev/null 2>&1" >> /etc/rc.local
		echo "/bin/ln -sf /mnt/ramdisk/VDmonitor /var/spool/asterisk/VDmonitor > /dev/null 2>&1" >> /etc/rc.local
		echo "#===== END OSDIAL RAMdisk Additions ====" >> /etc/rc.local
	fi
	# Lets turn on the cron!
	if [ -f /var/spool/cron/asterisk ]; then
        	CRGRP="`grep ADMIN_keepalive_ALL /var/spool/cron/asterisk`"
        	if [ -n "$CRGRP" ]; then
                	# It already exists, so lets overwrite our existing.
                	echo "    osdial-config: Cron for user 'asterisk' already in place."
                	cat /var/spool/cron/asterisk > /opt/osdial/bin/osdial.cron
                	rm -f /var/spool/cron/asterisk
        	fi
	fi
	echo "    osdial-config: Installing cron for user 'asterisk'."
	/usr/bin/crontab -u asterisk /opt/osdial/bin/osdial.cron > /dev/null 2>&1
	# If it didn't succeed, assume installcd
	if [ ! -f "/var/spool/cron/asterisk" ]; then
		cp /opt/osdial/bin/osdial.cron /var/spool/cron/asterisk
	fi
	# Verify config was copied, if not, we are new.
	if [ ! -f /etc/osdial.conf ]; then
        	echo "    osdial-config: Setting up keepalive services."
        	%{__perl} -pi -e 's|^VARactive_keepalives => X$|VARactive_keepalives => 1234569|' /etc/osdial.conf
	fi
	# cpuspeed can do bad things to ISDN/T1 cards
	if [ -f /etc/rc3.d/S06cpuspeed ]; then
                	echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
                	/etc/init.d/cpuspeed stop > /dev/null 2>&1
                	/sbin/chkconfig cpuspeed off > /dev/null 2>&1
	fi
	# We don't need cups
	if [ -f /etc/rc3.d/S56cups ]; then
                	echo "    osdial-config: CUPS detected, disabling!"
                	/etc/init.d/cups stop > /dev/null 2>&1
                	/sbin/chkconfig cups off > /dev/null 2>&1
	fi



%define _opt /opt

%files sql
%defattr(644,asterisk,asterisk,755)
%attr(0644,root,root) %{_opt}/osdial/bin/sql/000000.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/204000.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/204001.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/204002.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/204003.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/204004.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/204005.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/204006.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/204007.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/210000.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/210001.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/210002.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/999999.sql
%attr(0644,root,root) %{_opt}/osdial/bin/sql/upgrade_sql.map
%attr(0755,root,root) %{_opt}/osdial/bin/sql/upgrade_sql.pl
%attr(0755,root,root) %{_opt}/osdial/bin/sql/upstream_converison.sh


%files asterisk
%defattr(644,asterisk,asterisk,755)
%attr(0755,asterisk,asterisk) %{_sysconfdir}/asterisk/startup.d/tty_screen.sh
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/amd.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/dnsmgr.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/extensions.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/iax.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/indications.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/logger.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/manager.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/meetme.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/modules.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/musiconhold.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_extensions.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_extensions_conferences.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_extensions_custom.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_extensions_inbound.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_extensions_outbound.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_extensions_phones.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_extensions_servers.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_extensions_testing.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_iax.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_iax_custom.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_iax_phones.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_iax_registrations.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_iax_servers.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_iax_trunks.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_manager.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_meetme.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_sip.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_custom.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_sip_phones.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_sip_registrations.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/osdial_sip_trunks.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/oss.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/phone.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/sip.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/voicemail.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/zapata.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/zaptel.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/README.osdial
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
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-station_monitor.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-station_barge.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/call_inbound.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/debug_speak.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/invalid_speak.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/park_CID.agi

%files bin
%defattr(644,asterisk,asterisk,755)
%dir %attr(0755,asterisk,asterisk) %{_var}/log/osdial
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/osdial.conf
%attr(0755,root,root) %{_sysconfdir}/init.d/osdial
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
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/OSDconfig
%attr(0755,asterisk,asterisk) /usr/bin/OSDconfig
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/OSDIAL_DEDUPE_leads.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/OSDIAL_IN_new_leads_file.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/AGI.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/Manager.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/Outgoing.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/QCall.pm
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/libs/Asterisk/Voicemail.pm
%attr(0644,asterisk,asterisk) %{_opt}/osdial/bin/phone_codes_GMT.txt
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/start_asterisk_boot.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_ntp_update.sh
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_astgen.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial.cron
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ip_relay
%attr(0755,asterisk,asterisk) %{_sysconfdir}/cron.hourly/AST_ntp_update.sh

%files
%defattr(644,asterisk,asterisk,755)
%dir %{_opt}/osdial/reports
%dir %{_opt}/osdial/recordings
%dir %{_opt}/osdial/recordings/processing
%dir %{_opt}/osdial/recordings/processing/mp3
%dir %{_opt}/osdial/recordings/processing/wav
%dir %{_opt}/osdial/recordings/completed
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/ALTERNATE_NUMBER_DIALING.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/BALANCE_FILL_PROCESS.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/CALLBACKS_PROCESS.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/IE_INCOMPATIBILITIES.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/INBOUND-CLOSER_PROCESS.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/LICENSE.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/LOAD_BALANCING.txt
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/amd.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/dnsmgr.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/extensions.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/iax.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/indications.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/logger.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/manager.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/meetme.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/modules.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/musiconhold.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_conferences.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_custom.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_inbound.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_outbound.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_phones.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_servers.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_testing.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_custom.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_phones.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_registrations.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_servers.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_trunks.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_manager.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_meetme.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_custom.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_phones.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_registrations.conf
#%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_servers.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_trunks.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/oss.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/phone.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/sip.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/voicemail.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/zapata.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/zaptel.conf
%attr(0644,asterisk,asterisk) /usr/share/doc/osdial-2.1.0/conf_examples/README.osdial


%files web
%defattr(644,asterisk,asterisk,755)
%attr(0644,asterisk,asterisk) %{_sysconfdir}/httpd/conf.d/osdial.conf
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/ploticus
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/agent_reports
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/server_reports
%attr(0664,apache,asterisk) %{_opt}/osdial/html/index.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/osdial-bg.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/AgentLoginDn.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/AgentLoginUp.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/ControlLoginDn.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/ControlLoginUp.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/clientCompany.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/defaultCompany.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/a.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/b.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/c.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/d.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/e.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/f.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/g.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/h.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/i.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/j.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/k.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/l.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/m.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/n.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/o.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/p.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/q.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/r.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/s.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/t.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/u.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/v.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/w.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/x.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/y.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/z.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/A.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/B.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/C.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/D.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/E.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/F.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/G.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/H.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/I.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/J.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/K.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/L.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/M.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/N.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/O.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/P.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/Q.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/R.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/S.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/T.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/U.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/V.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/W.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/X.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/Y.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/Z.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/0.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/1.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/2.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/3.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/4.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/5.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/6.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/7.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/8.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/9.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/space.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/exclamation.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/at.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/ampersand.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/hyphen.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/period.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/comma.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/colon.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/test.html
%attr(0664,apache,asterisk) %{_opt}/osdial/html/images/rawtest.html
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/active_list_refresh.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/astguiclient.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/call_log_display.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/conf_exten_check.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/dbconnect.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/inbound_popup.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/live_exten_check.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/manager_send.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/park_calls_display.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/vdc_db_query.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/osdial.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/voicemail_check.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/across140.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/across146.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/across160.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/across2x146.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/top15.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/top16.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/topleft.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/topright.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/images/dlfoot.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_BLINK.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_BLINK_e.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_BLINK_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_BLINK_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_ON.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_ON_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_check_voicemail_ON_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_ON.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_ON_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_ON_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_live_call_ON_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_active_lines.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_active_lines_el.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_active_lines_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_active_lines_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_astguiclient.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_astguiclient_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_conferences.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_conferences_el.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_conferences_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_conferences_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_main.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_main_el.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_main_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agc_tab_main_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/blank.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/br.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/de.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/down.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/el.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/en.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/fr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/it.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/pt.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/remove.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/up.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_grabparkedcall_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_pause_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_resume_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_spacer.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF_p.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_RPLD_on.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_ammessage.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_ammessage_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_blindtransfer_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_channel.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_channel_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_channel_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_code.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_dialwithcustomer_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupbothlines_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hangupxferline_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_header.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_header_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_header_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF_es-orig.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_pl.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysactive_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysinactive_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_internalcloser_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_leave3waycall_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_localcloser_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_number.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_number_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_number_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_OFF_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_OFF_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_parkcustomerdial_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_seconds.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_seconds_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_seconds_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_script.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_script_es.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_script_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_osdial.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_osdial_ptbr.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_buttons1.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_buttons2.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_MUTE.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_UNMUTE.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_down.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_down_off.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_up.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_volume_up_off.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_CLOSERstats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_VDADstats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_OSDIAL_hopperlist.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_admin_log_display.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_disposition.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_performance.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_performance_detail.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_time_sheet.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_time_sheet_archive.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_inboundEXTstats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_inboundEXTstats_department.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_parkstats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_server_performance.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDAD.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADall.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADallREC.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADallSUMMARY.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonpark.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/admin.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/admin_modify_lead.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/admin_search_lead.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer-fronter_popup.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer-fronter_popup2.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer_dispo.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer_popup.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/dbconnect.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/group_hourly_stats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/help.gif
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader.pl
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/listloaderMAIN.php
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader_rowdisplay.pl
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader_super.pl
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/log_test.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/new_listloader_superL.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/phone_stats.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/record_conf_1_hour.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/remote_dispo.php
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/spreadsheet_sales_viewer.pl
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/user_stats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/user_status.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/vdremote.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/osdial_sales_viewer.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/voice_lab.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/vtiger_search.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/welcome.php
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/admin/VMnow.txt
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/admin.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/auth.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/conference.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/phones.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/server.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/settings.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/status.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/times.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/autoalt.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/campaigns.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/dialstat.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/fields.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/hotkey.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/listmix.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/pause.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/realtime.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/realtime_detail.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/realtime_summary.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/recycle.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/status.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/filters/filters.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/filters/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/ingroups/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/ingroups/ingroups.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/export.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/lists.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/reports.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/remoteagent/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/remoteagent/remoteagent.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/scripts/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/scripts/scripts.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/usergroups/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/usergroups/usergroups.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/users/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/users/users.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/dbconnect.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/display.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/footer.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/functions.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/header.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/help.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/includes.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/init.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/menu.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/validation.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/variables.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/index.php
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/admin/admin_changes_log.txt
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/admin_config.inc
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/admin/discover_stmts.txt
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/admin/listloader_stmts.txt
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/admin/project_auth_entries.txt
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/styles.css
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/osdial_auth_entries.txt
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/osdial_debug.txt
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/styles.css
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/functions.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/AgentTopLeft.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/AgentTopRight.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/AgentTopRightS.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/ControlLoginDn.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/ControlLoginUp.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/LoginAgainDn.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/LoginAgainUp.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/ShowCallbackInfo_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/ShowCallbackInfo_ON.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agentsidetab_cancel.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agentsidetab_extra.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agentsidetab_line.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agentsidetab_press.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agentsidetab_select.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agentsidetab_tab.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/agentsidetab_top.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/down150.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/down200.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/down240.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/down350.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/down433.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/down435.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/down550.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/formtab.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/loginagain-bg.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/muted.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/mutedoff.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/sidebar.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/sidebar2.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/sidebar2S.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/topleft.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/topright.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_dialnextnumber_dn.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_hangupcustomer_dn.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_parkcall_dn.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_senddtmf_dn.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_startrecording_dn.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_stoprecording_dn.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transerconf.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_transferconf_dn.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform1.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform1_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform2.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform2_OFF.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_LB_webform_dn.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_RPLD_off.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_XB_hotkeysinactive.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_osdial-i.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/images/vdc_tab_script-a.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/include/osdial.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/index.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/test_OSDIAL_output.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/test_callerid_output.php

%files debuginfo
%defattr(644,root,root,755)
/usr/lib/debug/opt/osdial/bin/ip_relay.debug

%changelog
* Thu Apr 9 2009 Lott Caskey <lottc@fugitol.com> 2.1.0.002-4
- Added auto-asterisk config generation
- Added ip_relay

* Sun Mar 29 2009 Lott Caskey <lottc@fugitol.com> 2.1.0.002-3
- Fixed osdial init script installation.

* Sat Mar 28 2009 Lott Caskey <lottc@fugitol.com> 2.1.0.002-2
- Added configuration methods into this package.

* Fri Mar 27 2009 Lott Caskey <lottc@fugitol.com> 2.1.0.002-1
- Restructure for osdial-web / osdial-sql.

* Mon Feb 16 2009 Lott Caskey <lottc@fugitol.com> 2.1.0.000-1
- Initial package.
