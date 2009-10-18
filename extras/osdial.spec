%define version %(cat version)
%define release %(cat release)

Summary:	The OSDial predictive dialing suite.
Name:		osdial
Version:	%{version}
Release:	%{release}
License:	GPL
Group:		Applications/Telephony
Source0:	osdial-%{version}.tgz
Source1:	osdial-template-highcontrast.tgz
Source2:	osdial-template-slingdial.tgz
URL:		http://www.callcentersg.com
Packager:	lottc@fugitol.com
Vendor:         Call Center Service Group
Requires:	openvpn
Requires:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
BuildArch:	noarch
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}

%description
OSDial is a predictive dialing system, an off-shoot of VICIdial,
currently being developed by Lott Caskey and Steve Szmidt.


%package profile
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile-all
Provides:	osdial-profile-single
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Obsoletes:	osdial-profile-install-all
Obsoletes:	osdial-profile-live
Obsoletes:	osdial-installcd
BuildArch:	noarch

%description profile
OSDial - Single / All-in-One Server Profile.
          osdial-common
          osdial-dialer
          osdial-sql
          osdial-web





%package profile-live
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Obsoletes:	osdial-livecd
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
BuildArch:	noarch

%description profile-live
Package for creating a live disk.






%package profile-install-all
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Obsoletes:	osdial-livecd
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
BuildArch:	noarch

%description profile-install-all
Package for creating an install disk.

%package profile-install-control
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
BuildArch:	noarch

%description profile-install-control
Package for creating an install disk.

%package profile-install-dialer
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
BuildArch:	noarch

%description profile-install-dialer
Package for creating an install disk.

%package profile-install-sql
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
BuildArch:	noarch

%description profile-install-sql
Package for creating an install disk.

%package profile-install-web
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
BuildArch:	noarch

%description profile-install-web
Package for creating an install disk.

%package profile-install-archive
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
BuildArch:	noarch

%description profile-install-archive
Package for creating an install disk.






%package profile-control
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Conflicts:	osdial-dialer
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Obsoletes:	osdial-profile-install-control
BuildArch:	noarch

%description profile-control
OSDial - Provides packages needed for multi-server
         configuration.  Only installs web and SQL
         components.
          osdial-common
          osdial-sql
          osdial-web

%package profile-dialer
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Conflicts:	osdial-web
Conflicts:	osdial-sql
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Obsoletes:	osdial-profile-install-dialer
BuildArch:	noarch

%description profile-dialer
OSDial - Provides packages needed for multi-server
         configuration.  Only installs dialer
         components.
               osdial-common
               osdial-dialer

%package profile-dialer-web
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Conflicts:	osdial-sql
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Obsoletes:	osdial-profile-install-dialer
BuildArch:	noarch

%description profile-dialer-web
OSDial - Provides packages needed for multi-server
         configuration.  Only installs dialer
         components.
               osdial-common
               osdial-dialer
               osdial-web

%package profile-sql
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Conflicts:	osdial-web
Conflicts:	osdial-dialer
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Obsoletes:	osdial-profile-install-sql
BuildArch:	noarch

%description profile-sql
OSDial - Provides packages needed for multi-server
         configuration.  Only installs SQL
         components.
               osdial-common
               osdial-sql

%package profile-web
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Conflicts:	osdial-dialer
Conflicts:	osdial-sql
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Obsoletes:	osdial-profile-install-web
BuildArch:	noarch

%description profile-web
OSDial - Provides packages needed for multi-server
         configuration.  Only installs Web
         components.
               osdial-common
               osdial-web

%package profile-archive
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Conflicts:	osdial-dialer
Conflicts:	osdial-sql
Conflicts:	osdial-web
Requires:	vsftpd
Requires:	httpd
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Obsoletes:	osdial-profile-install-archive
BuildArch:	noarch

%description profile-archive
OSDial - Provides packages needed for multi-server
         configuration.  Only installs archive
         components.
               osdial-common




%package common
Summary:	OSDial backend scripts
Group:		Applications/Telephony
Obsoletes:	osdial-bin
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	openvpn
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

%description common
OSDial backend scripts, needed by web, sql, etc.

%package sql
Summary: 	OSDial SQL files and update scripts.
Group:		Applications/Telephony
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
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
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	php-pear
Requires:	php-mysql
Requires:	ploticus
Requires:	httpd
Requires:	tinymce
Requires:	php-eaccelerator
BuildArch:      noarch

%description web
OSDial user interface files.  Mainly the php scripts, directory
structure and other supporting files.

%package dialer
Summary:        OSDial generic Asterisk configuration.
Group:          Applications/Telephony
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-asterisk = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       gawk
Obsoletes:	osdial-config
BuildArch:      noarch

%description dialer
The is a generic Asterisk configuration that should work out of box for most clients.



%package asterisk
Summary:        OSDial Asterisk 1.6 System
Group:          Applications/Telephony
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       asterisk >= 1.6.2.0
Requires:       wanpipe-util
Requires:       kernel-PAE-module-wanpipe
Requires:       dahdi
Requires:	dahdi-tools
Requires:       gawk
Requires:	mysql-server
Provides:	osdial-asterisk16
BuildArch:      noarch

%description asterisk
This package contains dependency and setup instructions for Asterisk 1.6.



%package asterisk12
Summary:        OSDial Asterisk 1.2 System
Group:          Applications/Telephony
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       asterisk12 <= 1.3
Requires:       wanpipe-util
Requires:       kernel-PAE-module-wanpipe
Requires:	kernel-PAE-module-zaptel
Requires:       zaptel12
Requires:       gawk
Provides:	osdial-asterisk
BuildArch:      noarch

%description asterisk12
This package contains dependency and setup instructions for Asterisk 1.2.

%package debuginfo
Summary:	OSDial debuginfo
Group:		Applications/Telephony
BuildArch:      i386

%description debuginfo
OSDial debuginfo

%package web-template-highcontrast
Summary:	OSDial user interface files
Group:		Applications/Telephony
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Provides:	osdial-template-highcontrast
BuildArch:      noarch

%description web-template-highcontrast
High Contrast Template

%package -n slingdial
Summary:	OSDial user interface files
Group:		Applications/Telephony
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Provides:	osdial-web-template-slingdial
Provides:	osdial-template-slingdial
BuildArch:      noarch

%description -n slingdial
template



%prep
%{__rm} -rf %{buildroot}
%setup -a 0 -n osdial-%{version}
%setup -a 1 -D
%setup -a 2 -D

%build
install -dp %{buildroot}
#cp %{SOURCE1} %{buildroot}/../osdial-%{version}/.osdial.config

%install
%{__make} DESTDIR=%{buildroot} HTTPDUSER=asterisk install
mkdir -p %{buildroot}/etc/httpd/conf.d
mkdir -p %{buildroot}/etc/init.d
mkdir -p %{buildroot}/opt/osdial/html/ivr
mkdir -p %{buildroot}/opt/osdial/recordings/processing/unmixed
mkdir -p %{buildroot}/opt/osdial/recordings/processing/mixed
mkdir -p %{buildroot}/opt/osdial/recordings/completed
mkdir -p %{buildroot}/opt/osdial/reports
mkdir -p %{buildroot}/opt/osdial/backups
mkdir -p %{buildroot}/var/log/osdial
mkdir -p %{buildroot}/var/lib/asterisk/sounds/ivr
cp extras/httpd-osdial.conf %{buildroot}/etc/httpd/conf.d/osdial.conf
cp extras/httpd-osdial-archive.conf %{buildroot}/etc/httpd/conf.d/osdial-archive.conf
cp extras/osdial.init %{buildroot}/etc/init.d/osdial
mkdir -p %{buildroot}/etc/cron.hourly
ln -s /opt/osdial/bin/AST_ntp_update.sh %{buildroot}/etc/cron.hourly
#mv %{buildroot}/etc/osdial.conf %{buildroot}/etc/osdial.conf.orig
touch %{buildroot}/opt/osdial/html/admin/VMnow.txt

# copy in asterisk configs
%{__mkdir_p} %{buildroot}/etc/asterisk/startup.d
%{__mkdir_p} %{buildroot}/etc/dahdi
echo -e "#!/bin/bash\nexport TTY=screen" > %{buildroot}/etc/asterisk/startup.d/tty_screen.sh
cp docs/conf_examples/*.conf %{buildroot}/etc/asterisk
cp docs/conf_examples/README.osdial %{buildroot}/etc/asterisk

%{__perl} -pi -e 's|^VARserver_ip.*|VARserver_ip => 127.0.0.1|' %{buildroot}/etc/osdial.conf
%{__perl} -pi -e 's|^VARHTTP_path.*|VARHTTP_path => http://127.0.0.1|' %{buildroot}/etc/osdial.conf
%{__perl} -pi -e 's|^VARFTP_host.*|VARFTP_host => 127.0.0.1|' %{buildroot}/etc/osdial.conf
%{__perl} -pi -e 's|^VARREPORT_host.*|VARREPORT_host => 127.0.0.1|' %{buildroot}/etc/osdial.conf
#mv %{buildroot}/etc/asterisk/dahdi_system.conf %{buildroot}/etc/dahdi/system.conf
rm %{buildroot}/etc/asterisk/dahdi_system.conf

echo > %{buildroot}/opt/osdial/.osdial-all
echo > %{buildroot}/opt/osdial/.osdial-install-all
echo > %{buildroot}/opt/osdial/.osdial-install-control
echo > %{buildroot}/opt/osdial/.osdial-install-dialer
echo > %{buildroot}/opt/osdial/.osdial-install-sql
echo > %{buildroot}/opt/osdial/.osdial-install-web
echo > %{buildroot}/opt/osdial/.osdial-install-archive
echo > %{buildroot}/opt/osdial/.osdial-live
echo > %{buildroot}/opt/osdial/.osdial-control
echo > %{buildroot}/opt/osdial/.osdial-dialer
echo > %{buildroot}/opt/osdial/.osdial-dialer-web
echo > %{buildroot}/opt/osdial/.osdial-sql
echo > %{buildroot}/opt/osdial/.osdial-web
echo > %{buildroot}/opt/osdial/.osdial-archive
echo > %{buildroot}/opt/osdial/.osdial-asterisk
echo > %{buildroot}/opt/osdial/.osdial-asterisk12

cd osdial-template-highcontrast
%{__make} DESTDIR=%{buildroot} install
cd ..

cd osdial-template-slingdial
%{__make} DESTDIR=%{buildroot} install
cd ..


%clean
%{__rm} -rf %{buildroot}

%pre
ASTUSER=`id asterisk`
RETVAL=$?
if [ "$RETVAL" -eq 1 ]; then
	/usr/sbin/useradd -r -d /var/lib/asterisk asterisk
fi

%post profile-live
INTY=$1
if [ "$INTY" -eq 1 ]; then
	echo > /opt/osdial/.osdial-live
        echo "RUN_FIRSTBOOT=\"NO\"" > /etc/sysconfig/firstboot
        echo "NETWORKING=yes" > /etc/sysconfig/network
        echo "NETWORKING_IPV6=no" >> /etc/sysconfig/network
        echo "HOSTNAME=osdial-live.callcentersg.com" >> /etc/sysconfig/network
        echo "127.0.0.1 osdial-live.callcentersg.com osdial-live" > /etc/hosts
        echo "127.0.0.1 localhost.localdomain localhost" >> /etc/hosts
        echo "::1       localhost6.localdomain6 localhost6" >> /etc/hosts
	if [ ! -d "/usr/lib/syslinux" ]; then
		if [ -d "/usr/share/syslinux" ]; then
			echo "    osdial-live: Fixing broken syslinux"
			/bin/ln -s /usr/share/syslinux /usr/lib/syslinux
		fi
	fi
	/sbin/service syslog stop
fi
echo -n

%post profile-archive
        /sbin/chkconfig vsftpd on > /dev/null 2>&1
	ln -sf /opt/osdial/recordings /var/lib/asterisk/recordings > /dev/null 2>&1
	ln -sf /opt/osdial/reports /var/lib/asterisk/reports > /dev/null 2>&1
	echo -n

%post profile-install-all
INTY=$1
if [ "$INTY" -eq 1 ]; then
        echo "NETWORKING=yes" > /etc/sysconfig/network
        echo "NETWORKING_IPV6=no" >> /etc/sysconfig/network
        echo "HOSTNAME=osdial.callcentersg.com" >> /etc/sysconfig/network
        echo "127.0.0.1 osdial.callcentersg.com osdial" > /etc/hosts
        echo "127.0.0.1 localhost.localdomain localhost" >> /etc/hosts
        echo "::1       localhost6.localdomain6 localhost6" >> /etc/hosts
fi
echo -n

%post profile-install-control
INTY=$1
if [ "$INTY" -eq 1 ]; then
        echo "NETWORKING=yes" > /etc/sysconfig/network
        echo "NETWORKING_IPV6=no" >> /etc/sysconfig/network
        echo "HOSTNAME=osdial-c1.callcentersg.com" >> /etc/sysconfig/network
        echo "127.0.0.1 osdial-c1.callcentersg.com osdial-c1 c1" > /etc/hosts
        echo "127.0.0.1 localhost.localdomain localhost" >> /etc/hosts
        echo "::1       localhost6.localdomain6 localhost6" >> /etc/hosts
fi
echo -n

%post profile-install-dialer
INTY=$1
if [ "$INTY" -eq 1 ]; then
        echo "NETWORKING=yes" > /etc/sysconfig/network
        echo "NETWORKING_IPV6=no" >> /etc/sysconfig/network
        echo "HOSTNAME=osdial-dN.callcentersg.com" >> /etc/sysconfig/network
        echo "127.0.0.1 osdial-dN.callcentersg.com osdial-dN dN" > /etc/hosts
        echo "127.0.0.1 localhost.localdomain localhost" >> /etc/hosts
        echo "::1       localhost6.localdomain6 localhost6" >> /etc/hosts
fi
echo -n

%post profile-install-sql
INTY=$1
if [ "$INTY" -eq 1 ]; then
        echo "NETWORKING=yes" > /etc/sysconfig/network
        echo "NETWORKING_IPV6=no" >> /etc/sysconfig/network
        echo "HOSTNAME=osdial-s1.callcentersg.com" >> /etc/sysconfig/network
        echo "127.0.0.1 osdial-s1.callcentersg.com osdial-s1 s1" > /etc/hosts
        echo "127.0.0.1 localhost.localdomain localhost" >> /etc/hosts
        echo "::1       localhost6.localdomain6 localhost6" >> /etc/hosts
fi
echo -n

%post profile-install-web
INTY=$1
if [ "$INTY" -eq 1 ]; then
        echo "NETWORKING=yes" > /etc/sysconfig/network
        echo "NETWORKING_IPV6=no" >> /etc/sysconfig/network
        echo "HOSTNAME=osdial-w1.callcentersg.com" >> /etc/sysconfig/network
        echo "127.0.0.1 osdial-w1.callcentersg.com osdial-w1 w1" > /etc/hosts
        echo "127.0.0.1 localhost.localdomain localhost" >> /etc/hosts
        echo "::1       localhost6.localdomain6 localhost6" >> /etc/hosts
fi
echo -n

%post profile-install-archive
INTY=$1
if [ "$INTY" -eq 1 ]; then
        echo "NETWORKING=yes" > /etc/sysconfig/network
        echo "NETWORKING_IPV6=no" >> /etc/sysconfig/network
        echo "HOSTNAME=osdial-a1.callcentersg.com" >> /etc/sysconfig/network
        echo "127.0.0.1 osdial-a1.callcentersg.com osdial-a1 a1" > /etc/hosts
        echo "127.0.0.1 localhost.localdomain localhost" >> /etc/hosts
        echo "::1       localhost6.localdomain6 localhost6" >> /etc/hosts
fi
echo -n






%post common
INTY=$1
if [ "$INTY" -eq 1 ]; then
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
fi
if [ "$INTY" -eq 2 ]; then
	mkdir -p /opt/osdial/backups/%{version}-%{release}
	cp -r /opt/osdial/bin /opt/osdial/backups/%{version}-%{release}

	mkdir /opt/osdial/backups/%{version}-%{release}/etc
	cp /etc/osdial.conf /opt/osdial/backups/%{version}-%{release}/etc
	if [ -d /etc/asterisk ]; then
		[ -f /etc/dahdi/system.conf ] && cp /etc/dahdi/system.conf /opt/osdial/backups/%{version}-%{release}/etc
		[ -d /etc/asterisk ] && cp -r /etc/asterisk /opt/osdial/backups/%{version}-%{release}/etc
	fi
	if [ -d /var/lib/asterisk/agi-bin ]; then
		mkdir /opt/osdial/backups/%{version}-%{release}/agi
		cp -r /var/lib/asterisk/agi-bin /opt/osdial/backups/%{version}-%{release}/agi
	fi
	if [ -d /opt/osdial/html ]; then
		mkdir /opt/osdial/backups/%{version}-%{release}/html
		cp -r /opt/osdial/html/* /opt/osdial/backups/%{version}-%{release}/html
	fi
	if [ -f /etc/openvpn/osdial.up ]; then
		HST=`/bin/hostname -s`        
		if [ "$HST" = "osdial" -o "$HST" = "osdial-live" -o "$HST" = "osdial-ccsg" ]; then
			echo "osdial" > /etc/openvpn/osdial.up                                    
			echo "osdial1234" >> /etc/openvpn/osdial.up                               
			else                                                                              
			echo "${HST}" > /etc/openvpn/osdial.up                                    
			echo "0o1s2d3i4a5l6${HST}6l5a4i3d2s1o0" >> /etc/openvpn/osdial.up         
		fi                                                                                
	fi    
	# Run update script.
	/opt/osdial/bin/sql/upgrade_sql.pl --skip-auth-fix
fi
mkdir -p /opt/osdial/reports > /dev/null 2>&1
mkdir -p /opt/osdial/recordings > /dev/null 2>&1
mkdir -p /opt/osdial/recordings/processing > /dev/null 2>&1
mkdir -p /opt/osdial/recordings/processing/mixed > /dev/null 2>&1
mkdir -p /opt/osdial/recordings/processing/unmixed > /dev/null 2>&1
mkdir -p /opt/osdial/recordings/completed > /dev/null 2>&1
chown -R asterisk:asterisk /opt/osdial/reports > /dev/null 2>&1
chown -R asterisk:asterisk /opt/osdial/recordings > /dev/null 2>&1
echo -n



%post sql
INTY=$1
if [ "$INTY" -eq 1 ]; then
	/sbin/chkconfig mysqld on > /dev/null 2>&1
	# Apply OSDial SQL changes to /etc/my.cnf
	if [ ! "`grep innodb_log_arch_dir /etc/my.cnf`" ]; then
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
	if [ -f "/opt/osdial/.osdial-live" ]; then
		/sbin/service mysqld start
	fi
	/opt/osdial/bin/sql/upgrade_sql.pl
	if [ -f "/opt/osdial/.osdial-live" ]; then
		/sbin/service mysqld stop
	fi
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
fi
if [ "$INTY" -eq 2 ]; then
	# Run update script.
	/opt/osdial/bin/sql/upgrade_sql.pl
	# Reset running procs.
	if [ -n "`ps -ef | grep FastAGI`" ]; then
		kill -9 `ps -ef | grep FastAGI | awk '{ print $2 }'` > /dev/null 2>&1
	fi
	if [ -n "`ps -ef | grep AST`" ]; then
		kill -9 `ps -ef | grep AST | awk '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post web
INTY=$1
if [ "$INTY" -eq 1 ]; then
	/sbin/chkconfig httpd on > /dev/null 2>&1
	if [ -f "/var/www/html/index.html" ]; then
		if [ -n "`grep osdial /var/www/html/index.html`" ]; then
			mv /var/www/html/index.html /var/www/html/index.html.bak
		fi
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
	if [ -f "/opt/osdial/.osdial-live" ]; then
		/sbin/service httpd stop
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
fi
if [ "$INTY" -eq 2 ]; then
	if [ -f "/var/www/html/index.html" ]; then
		if [ -n "`grep osdial /var/www/html/index.html`" ]; then
			mv /var/www/html/index.html /var/www/html/index.html.bak
		fi
	fi
	if [ ! -f /var/www/html/index.php ]; then
		ln -s /opt/osdial/html/index.php /var/www/html/index.php
	fi
	# Reset running procs.
	if [ -n "`ps -ef | grep FastAGI`" ]; then
		kill -9 `ps -ef | grep FastAGI | awk '{ print $2 }'` > /dev/null 2>&1
	fi
	if [ -n "`ps -ef | grep AST`" ]; then
		kill -9 `ps -ef | grep AST | awk '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post dialer
INTY=$1
if [ "$INTY" -eq 1 ]; then
	# Put in some ramdisk on the dialer.
	if [ "`grep OSDIAL /etc/rc.local`" ]; then
		OSDTMP=/tmp/osdtmp.$$
		sed -e '/BEGIN OSDIAL/,/END OSDIAL/d' /etc/rc.local > $OSDTMP
		sed -e '/ramdisk/d' $OSDTMP > /etc/rc.local
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
        	%{__perl} -pi -e 's|^VARactive_keepalives => XX$|VARactive_keepalives => 1234569|' /etc/osdial.conf
	fi
	# We don't need cups
	if [ -f /etc/rc3.d/S56cups ]; then
                	echo "    osdial-config: CUPS detected, disabling!"
                	/etc/init.d/cups stop > /dev/null 2>&1
                	/sbin/chkconfig cups off > /dev/null 2>&1
	fi
fi
if [ "$INTY" -eq 2 ]; then
	echo -n
fi
# Reset running procs.
if [ -n "`ps -ef | grep FastAGI`" ]; then
	kill -9 `ps -ef | grep FastAGI | awk '{ print $2 }'` > /dev/null 2>&1
fi
if [ -n "`ps -ef | grep AST`" ]; then
	kill -9 `ps -ef | grep AST | awk '{ print $2 }'` > /dev/null 2>&1
fi
echo -n

%post asterisk
INTY=$1
/sbin/chkconfig asterisk on > /dev/null 2>&1
# cpuspeed can do bad things to ISDN/T1 cards
if [ -f /etc/rc3.d/S06cpuspeed ]; then
               	echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
               	/etc/init.d/cpuspeed stop > /dev/null 2>&1
               	/sbin/chkconfig cpuspeed off > /dev/null 2>&1
fi
# Find and move zapata.conf
mkdir -p /etc/zaptel.bak
MOVED=0
if [ -f "/etc/asterisk/zapata.conf" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		cp -f /etc/asterisk/zapata.conf /etc/zaptel.bak > /dev/null 2>&1
		mv -f /etc/asterisk/zapata.conf /etc/asterisk/chan_dahdi.conf > /dev/null 2>&1
	else
		mv -f /etc/asterisk/zapata.conf /etc/zaptel.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/zapata.conf.rpmsave" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		cp -f /etc/asterisk/zapata.conf.rpmsave /etc/zaptel.bak > /dev/null 2>&1
		mv -f /etc/asterisk/zapata.conf.rpmsave /etc/asterisk/chan_dahdi.conf > /dev/null 2>&1
	else
		mv -f /etc/asterisk/zapata.conf.rpmsave /etc/zaptel.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/zapata.conf.rpmorig" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		cp -f /etc/asterisk/zapata.conf.rpmorig /etc/zaptel.bak > /dev/null 2>&1
		mv -f /etc/asterisk/zapata.conf.rpmorig /etc/asterisk/chan_dahdi.conf > /dev/null 2>&1
	else
		mv -f /etc/asterisk/zapata.conf.rpmorig /etc/zaptel.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/zapata.conf.rpmnew" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		cp -f /etc/asterisk/zapata.conf.rpmnew /etc/zaptel.bak > /dev/null 2>&1
		mv -f /etc/asterisk/zapata.conf.rpmnew /etc/asterisk/chan_dahdi.conf > /dev/null 2>&1
	else
		mv -f /etc/asterisk/zapata.conf.rpmnew /etc/zaptel.bak > /dev/null 2>&1
	fi
fi
echo -n

%post asterisk12
INTY=$1
/sbin/chkconfig asterisk on > /dev/null 2>&1
# cpuspeed can do bad things to ISDN/T1 cards
if [ -f /etc/rc3.d/S06cpuspeed ]; then
               	echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
               	/etc/init.d/cpuspeed stop > /dev/null 2>&1
               	/sbin/chkconfig cpuspeed off > /dev/null 2>&1
fi
# Find and move chan_dahdi.conf
mkdir -p /etc/dahdi.bak
MOVED=0
if [ -f "/etc/asterisk/chan_dahdi.conf" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		cp -f /etc/asterisk/chan_dahdi.conf /etc/dahdi.bak > /dev/null 2>&1
		mv -f /etc/asterisk/chan_dahdi.conf /etc/asterisk/zapata.conf > /dev/null 2>&1
	else
		mv -f /etc/asterisk/chan_dahdi.conf /etc/dahdi.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/chan_dahdi.conf.rpmsave" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		cp -f /etc/asterisk/chan_dahdi.conf.rpmsave /etc/dahdi.bak > /dev/null 2>&1
		mv -f /etc/asterisk/chan_dahdi.conf.rpmsave /etc/asterisk/zapata.conf > /dev/null 2>&1
	else
		mv -f /etc/asterisk/chan_dahdi.conf.rpmsave /etc/dahdi.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/chan_dahdi.conf.rpmorig" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		cp -f /etc/asterisk/chan_dahdi.conf.rpmorig /etc/dahdi.bak > /dev/null 2>&1
		mv -f /etc/asterisk/chan_dahdi.conf.rpmorig /etc/asterisk/zapata.conf > /dev/null 2>&1
	else
		mv -f /etc/asterisk/chan_dahdi.conf.rpmorig /etc/dahdi.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/chan_dahdi.conf.rpmnew" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		cp -f /etc/asterisk/chan_dahdi.conf.rpmnew /etc/dahdi.bak > /dev/null 2>&1
		mv -f /etc/asterisk/chan_dahdi.conf.rpmnew /etc/asterisk/zapata.conf > /dev/null 2>&1
	else
		mv -f /etc/asterisk/chan_dahdi.conf.rpmnew /etc/dahdi.bak > /dev/null 2>&1
	fi
fi
echo -n


%post profile
INTY=$1
if [ "$INTY" -eq 2 ]; then
	CTB="/var/spool/cron/asterisk"
	if [ -f "$CTB" ]; then
		if [ -z "`grep AST_ntp_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Force update of time servers. Now run from /etc/cron.hourly" >> $CTB
			echo -e "#0 * * * * /opt/osdial/bin/AST_ntp_update.sh > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`grep Loadavg $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Get load average." >> $CTB
			echo -e "* * * * * cat /proc/loadavg | cut -d" " -f1 > /opt/osdial/html/admin/Loadavg.txt" >> $CTB
		fi

		if [ -z "`grep 'remove old osdial logs' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs rm -f" >> $CTB
			echo -e "29 0 * * * /usr/bin/find /var/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs rm -f" >> $CTB
		fi

		if [ -z "`grep ADMIN_keepalive_ALL $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/ADMIN_keepalive_ALL.pl" >> $CTB
		fi


		if [ -z "`grep osdial_external_dnc $CTB`" ]; then
			echo -e "" >> $CTB
			echo "### (sql) Actual Scrub against external DNC" >> $CTB
			echo "* * * * * /opt/osdial/bin/osdial_external_dnc.pl" >> $CTB
			echo -e "" >> $CTB
			echo "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
			echo "0 1 * * * /opt/osdial/bin/osdial_external_dnc.pl --sched=ALL" >> $CTB
		fi

		if [ -z "`grep AST_CLEAR_auto_calls $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_CLEAR_auto_calls.pl" >> $CTB
		fi

		if [ -z "`grep AST_flush_DBqueue $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
			echo -e "11,41 * * * * /opt/osdial/bin/AST_flush_DBqueue.pl -q" >> $CTB
		fi

		if [ -z "`grep AST_cleanup_agent_log $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
			echo -e "33 * * * * /opt/osdial/bin/AST_cleanup_agent_log.pl" >> $CTB
		fi

		if [ -z "`grep AST_VDhopper $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) updater for OSDial hopper" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_VDhopper.pl -q" >> $CTB
		fi

		if [ -z "`grep ADMIN_adjust_GMTnow_on_leads $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
			echo -e "1 1,7 * * * /opt/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl --debug --postal-code-gmt" >> $CTB
		fi

		if [ -z "`grep AST_DB_optimize $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
			echo -e "3 1 * * * /opt/osdial/bin/AST_DB_optimize.pl" >> $CTB
		fi

		if [ -z "`grep AST_agent_week $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
			echo -e "#2 0 * * 0 /opt/osdial/bin/AST_agent_week.pl" >> $CTB
			echo -e "#22 0 * * * /opt/osdial/bin/AST_agent_day.pl" >> $CTB
		fi

		if [ -z "`grep AST_VDsales_export $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
			echo -e "#32 0 * * * /opt/osdial/bin/AST_VDsales_export.pl" >> $CTB
			echo -e "#42 0 * * * /opt/osdial/bin/AST_sourceID_summary_export.pl" >> $CTB
		fi

		if [ -z "`grep osdial_astgen $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/osdial_astgen.pl -q" >> $CTB
		fi

		if [ -z "`grep osdial_ivr_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`grep VMnow $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
			echo -e "* * * * * /usr/sbin/asterisk -rx \"show voicemail users\" > /opt/osdial/html/admin/VMnow.txt" >> $CTB
		fi

		if [ -z "`grep AST_manager_kill_hung_congested $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_manager_kill_hung_congested.pl" >> $CTB
		fi

		if [ -z "`grep AST_vm_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) updater for voicemail" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_vm_update.pl" >> $CTB
		fi

		if [ -z "`grep AST_conf_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) updater for conference validator" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_conf_update.pl" >> $CTB
		fi

		if [ -z "`grep AST_reset_mysql_vars $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
			echo -e "2 1 * * * /opt/osdial/bin/AST_reset_mysql_vars.pl" >> $CTB
		fi

		if [ -z "`grep monitor $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
			echo -e "24 0 * * * /usr/bin/find /var/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs rm -f" >> $CTB
		fi

		if [ -z "`grep AST_audio_archive $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Send Recordings to archive server" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_audio_archive.pl" >> $CTB
		fi

		if [ -z "`grep AST_audio_compress $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Compress wav files to mp3" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_audio_compress.pl --MP3" >> $CTB
		fi

		if [ -z "`grep AST_sort_recordings $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Sort MP3s into campaign_id/date directory structure" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_sort_recordings.pl" >> $CTB
		fi

		if [ -z "`grep AST_qc_transfer $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Send select MP3s to a third-party quality-control or offste archive server." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/AST_qc_transfer.pl" >> $CTB
		fi

		kill -1 `ps -ef | grep crond | head -1 | awk '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post -n slingdial
if [ -d "/var/lib/mysql/osdial" ]; then
	echo "UPDATE system_settings SET admin_template='SlingDial',agent_template='SlingDial';" | /usr/bin/mysql osdial
fi
echo -n

%define _opt /opt

%files profile
%attr(0644,root,root) %{_opt}/osdial/.osdial-all

%files profile-install-all
%attr(0644,root,root) %{_opt}/osdial/.osdial-install-all

%files profile-install-control
%attr(0644,root,root) %{_opt}/osdial/.osdial-install-control

%files profile-install-dialer
%attr(0644,root,root) %{_opt}/osdial/.osdial-install-dialer

%files profile-install-sql
%attr(0644,root,root) %{_opt}/osdial/.osdial-install-sql

%files profile-install-web
%attr(0644,root,root) %{_opt}/osdial/.osdial-install-web

%files profile-install-archive
%attr(0644,root,root) %{_opt}/osdial/.osdial-install-archive

%files profile-live
%attr(0644,root,root) %{_opt}/osdial/.osdial-live

%files profile-control
%attr(0644,root,root) %{_opt}/osdial/.osdial-control

%files profile-dialer
%attr(0644,root,root) %{_opt}/osdial/.osdial-dialer

%files profile-dialer-web
%attr(0644,root,root) %{_opt}/osdial/.osdial-dialer-web

%files profile-sql
%attr(0644,root,root) %{_opt}/osdial/.osdial-sql

%files profile-web
%attr(0644,root,root) %{_opt}/osdial/.osdial-web

%files profile-archive
%attr(0644,root,root) %{_opt}/osdial/.osdial-archive

%files asterisk
%attr(0644,root,root) %{_opt}/osdial/.osdial-asterisk

%files asterisk12
%attr(0644,root,root) %{_opt}/osdial/.osdial-asterisk12



%files
%dir %attr(0755,asterisk,asterisk) %{_opt}/osdial/backups

%files sql
%defattr(644,asterisk,asterisk,755)
#%attr(0644,root,root) %{_opt}/osdial/bin/sql/*.sql
#%attr(0644,root,root) %{_opt}/osdial/bin/sql/upgrade_sql.map
#%attr(0755,root,root) %{_opt}/osdial/bin/sql/upgrade_sql.pl
%attr(0755,root,root) %{_opt}/osdial/bin/sql/upstream_conversion.sh


%files dialer
%defattr(644,asterisk,asterisk,755)
%attr(0755,asterisk,asterisk) %{_sysconfdir}/asterisk/startup.d/tty_screen.sh
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/amd.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/cdr.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/dnsmgr.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/extensions.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/extconfig.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/res_mysql.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/iax.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/iaxprov.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/indications.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/logger.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/manager.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/meetme.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/modules.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/musiconhold.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_extensions.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_extensions_conferences.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_extensions_custom.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_extensions_inbound.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_extensions_outbound.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_extensions_phones.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_extensions_servers.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_extensions_testing.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_iax.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_iax_custom.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_iax_phones.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_iax_registrations.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_iax_servers.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_iax_trunks.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_manager.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_meetme.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_sip.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_custom.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_sip_phones.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_registrations.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_trunks.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/oss.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/phone.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/sip.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/voicemail.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/chan_dahdi.conf
#%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/dahdi/system.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/README.osdial
%dir %attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/sounds/ivr
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/conf.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/park.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/,.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-0.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-0.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-1.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-1.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-2.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-2.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-3.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-3.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-4.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-4.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-5.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-5.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-6.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-6.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-7.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-7.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-8.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-8.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-9.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-9.wav
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
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-hash.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-hash.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/hold_tone.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/ld_invalid_pin_number.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/ld_welcome_pin_number.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/leave.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/silence.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/sip-silence.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-star.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/dtmf-star.wav
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/vm-goodbye.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/vm-msgsaved.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/vm-rec-generic.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/vm-review.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/check-number-dial-again.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/connected.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/extension.gsm
%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/not-yet-connected.gsm
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
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDoutboundIVR.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDivr.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-dtmf.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-record_prompts.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-station_monitor.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-station_barge.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/call_inbound.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/debug_speak.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/invalid_speak.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/park_CID.agi

%files common
%defattr(644,asterisk,asterisk,755)
%dir %attr(0755,asterisk,asterisk) %{_var}/log/osdial
%attr(0644,asterisk,asterisk) %{_sysconfdir}/httpd/conf.d/osdial-archive.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/osdial.conf
%attr(0755,asterisk,asterisk) %{_sysconfdir}/init.d/osdial
%attr(0644,asterisk,asterisk) %{_opt}/osdial/bin/sql/*.sql
%attr(0644,asterisk,asterisk) %{_opt}/osdial/bin/sql/upgrade_sql.map
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/sql/upgrade_sql.pl
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
#%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDhopper_MIXtest.pl
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
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_ivr_sync.sh
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_killall.sh
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
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_external_dnc.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial.cron
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ip_relay
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/safe_ip_relay
%attr(0755,asterisk,asterisk) %{_sysconfdir}/cron.hourly/AST_ntp_update.sh
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/ALTERNATE_NUMBER_DIALING.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/BALANCE_FILL_PROCESS.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/CALLBACKS_PROCESS.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/IE_INCOMPATIBILITIES.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/INBOUND-CLOSER_PROCESS.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/LOAD_BALANCING.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/OUTBOUND_IVR.txt
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/amd.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/cdr.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/dnsmgr.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/extensions.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/extconfig.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/res_mysql.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/iax.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/iaxprov.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/indications.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/logger.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/manager.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/meetme.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/modules.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/musiconhold.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_conferences.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_custom.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_inbound.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_outbound.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_phones.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_servers.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_extensions_testing.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_custom.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_phones.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_registrations.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_servers.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_iax_trunks.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_manager.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_meetme.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_custom.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_phones.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_registrations.conf
#%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_servers.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/osdial_sip_trunks.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/oss.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/phone.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/sip.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/voicemail.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/chan_dahdi.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/dahdi_system.conf
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/conf_examples/README.osdial
%attr(0644,root,root) /usr/share/doc/osdial-2.1.0/LICENSE.txt
%attr(0640,root,root) /etc/openvpn/osdial-ca.crt
%attr(0640,root,root) /etc/openvpn/osdial.up
%attr(0640,root,root) /etc/openvpn/osdial-ta.key
%attr(0640,root,root) /etc/openvpn/osdial.conf
%attr(0640,root,root) /etc/openvpn/osdial2.conf


%files web-template-highcontrast
%defattr(644,asterisk,asterisk,755)
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/HighContrast
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/HighContrast/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/HighContrast
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/HighContrast/images
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/HighContrast/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/HighContrast/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/HighContrast/images/*
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/HighContrast/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/HighContrast/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/HighContrast/images/*

%files -n slingdial
%defattr(644,asterisk,asterisk,755)
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/SlingDial
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/SlingDial/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/SlingDial
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/SlingDial/images
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/SlingDial/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/SlingDial/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/SlingDial/images/*
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/SlingDial/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/SlingDial/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/SlingDial/images/*

%files web
%defattr(644,asterisk,asterisk,755)
%attr(0644,asterisk,asterisk) %{_sysconfdir}/httpd/conf.d/osdial.conf
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/ivr
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/default
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/default/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/ploticus
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/agent_reports
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/server_reports
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/default
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/default/images
%attr(0664,apache,asterisk) %{_opt}/osdial/html/index.php
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
%attr(0644,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/webform_redirect.php
%attr(0644,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/webform_redirect.php
%attr(0644,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/webform-event_members.php
%attr(0644,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/webform-print_form.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/default/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/default/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/default/images/*
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/default/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/default/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/default/images/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/copyright.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/tocsv.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_CLOSERstats.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_VDADstats.php
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
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDAD.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADall.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADallREC.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADallSUMMARY.php
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/ivr.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/realtime.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/realtime_detail.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/realtime_summary.php
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/realtime_detail.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/realtime_summary.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/call_stats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/list_cost.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/list_performance.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/remoteagent/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/remoteagent/remoteagent.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/scripts/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/scripts/scripts.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/usergroups/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/usergroups/usergroups.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/users/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/users/users.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/dbconnect.php
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/styles-print.css
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/osdial_auth_entries.txt
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/osdial_debug.txt
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/blank.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/functions.php
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
