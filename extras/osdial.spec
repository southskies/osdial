%{!?kernel: %{expand: %%define kernel %(uname -r)}}
%if %(echo %{kernel} | %{__grep} -c smp)
        %{expand:%%define ksmp -smp}
%endif
%if %(echo %{kernel} | %{__grep} -c PAE)
        %{expand:%%define kpae -PAE}
%endif
%if %(echo %{kernel} | %{__grep} -c xen)
        %{expand:%%define kxen -xen}
%endif

%define kversion2 %(echo %{kernel} | %{__sed} -e s/smp// -)
%define kversion3 %(echo %{kversion2} | %{__sed} -e s/xen// -)
%define kversion %(echo %{kversion3} | %{__sed} -e s/PAE// -)
%define krelver  %(echo %{kversion2} | tr -s '-' '_')

%define perl_vendorlib %(eval "`%{__perl} -V:installvendorlib`"; echo $installvendorlib)
%define perl_vendorarch %(eval "`%{__perl} -V:installvendorarch`"; echo $installvendorarch)

%define version %(if [ -f osdial.version ]; then cat osdial.version; else cat /builddir/build/SOURCES/osdial.version; fi)
%define release %(if [ -f osdial.release ]; then cat osdial.release; else cat /builddir/build/SOURCES/osdial.release; fi)
%define buildver %(if [ -f osdial.build ]; then cat osdial.build; else cat /builddir/build/SOURCES/osdial.build; fi)

# Current versions
%define mysql_version 5.1.39-5
%define asterisk12_version 1.2.32-13
%define asterisk16_version 1.6.2.16-63
%define libpri12_version 1.2.8-4
%define libpri14_version 1.4.10.2-5
%define zaptel12_version 1.2.27-11
%define dahdi_version 2.2.0.2-999917
%define dahdi_tools_version 2.2.0-999917
%define wanpipe_version 3.5.17-20


Summary:	The OSDial predictive dialing suite.
Name:		osdial
Version:	%{version}
Release:	%{release}%{?dist}
License:	GPL
Group:		Applications/Telephony
Source0:	osdial-%{version}.tgz
Source1:	osdial-template-highcontrast.tgz
Source2:	osdial-template-slingdial.tgz
Source3:	osdial-template-largedialpresets.tgz
Source4:	osdial.version
Source5:	osdial.release
Source6:	osdial.build
URL:		http://www.callcentersg.com
Packager:	lottc@fugitol.com
Vendor:         Call Center Service Group
Requires(pre):	openssh coreutils e2fsprogs grep
Requires:	openvpn
Requires:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
BuildRequires:  /bin/cat
BuildArch:	noarch
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}

%description
OSDial is a predictive dialing system, an off-shoot of VICIdial,
currently being developed by Lott Caskey and Steve Szmidt.


%package conflict
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Conflicts:	osdial
Conflicts:	osdial-profile
Conflicts:	osdial-install
Conflicts:	osdial-common
Conflicts:	osdial-dialer
Conflicts:	osdial-web
Conflicts:	osdial-sql
BuildArch:	noarch

%description conflict
Meta package which will conflict with OSDial base packages.




%package profile
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires(post):	coreutils grep gawk procps
Requires:	osdial-profile-all = %{version}-%{release}
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Obsoletes:	osdial-profile-live
Obsoletes:	osdial-installcd
BuildArch:	noarch

%description profile
OSDial - Single / All-in-One Server Profile.
          osdial-common
          osdial-dialer
          osdial-sql
          osdial-web


%package profile-all
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile-single = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Requires(post):	coreutils grep gawk procps
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Obsoletes:	osdial-profile-install-all
Obsoletes:	osdial-installcd
BuildArch:	noarch

%description profile-all
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
Requires(post):	coreutils grep gawk procps
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:	osdial-sql = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
BuildArch:	noarch

%description profile-live
Package for creating a live disk.





%if 0%{?blah}

%package profile-install-all
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Obsoletes:	osdial-livecd
Requires(post):	coreutils grep
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
Requires(post):	coreutils grep
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
Requires(post):	coreutils grep
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
Requires(post):	coreutils grep
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
Requires(post):	coreutils grep
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
Requires(post):	coreutils grep
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
BuildArch:	noarch

%description profile-install-archive
Package for creating an install disk.

%endif






%package profile-control
Summary:	The OSDial predictive dialing suite.
Group:		Applications/Telephony
Provides:	osdial-profile = %{version}-%{release}
Conflicts:	astguiclient
Conflicts:	vicidial
BuildRequires:  dialog
Conflicts:	osdial-dialer
Requires(post):	coreutils grep gawk procps
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
Requires(post):	coreutils grep gawk procps
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
Requires(post):	coreutils grep gawk procps
Requires:	osdial = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	osdial-dialer = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Obsoletes:	osdial-profile-install-dialer-web
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
Requires(post):	coreutils grep gawk procps
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
Requires(post):	coreutils grep gawk procps
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
Requires(post):	coreutils grep gawk procps vsftpd
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
Requires(post):	coreutils grep gawk lsof ip_relay perl
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	perl-OSDial = %{version}-%{release}
Requires:	openvpn
Requires:	sysstat
Requires:	httpd
Requires:	php-mbstring
Requires:	php-xml
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
Requires:	perl-IO-Socket-Multicast
Requires:	perl-Spreadsheet-ParseExcel
Requires:	perl-Spreadsheet-WriteExcel
Requires:	perl-Net-Telnet
Requires:	perl-Net-Server
Requires:	perl-Net-IP
Requires:	perl-Net-Address-IP-Local
Requires:	perl-Net-Netmask
Requires:	perl-Data-Validate-IP
Requires:	perl-Number-Format
Requires:	perl-version
Requires:	perl-Parse-RecDescent
Requires:	perl-Proc-Exists
Requires:	readline
Requires:	sox
Requires:	lame
Requires:	toolame
Requires:	screen
Requires:	ntp
Requires:	iftop
Requires:	ploticus
Requires:	balance
%if 0%{?rhel} < 6
Requires:	subversion
%endif
Requires:	mtop
Requires:	perl-Curses
Requires:	perl-Asterisk
Requires:	htop
Requires:	sipsak
Requires:	ttyload
Requires:	sqlite2
Requires:	dialog
Requires:	ip_relay
Requires:	system-switch-asterisk
Requires:	festival
Requires:	festival-lib
Requires:	festival-speechtools-libs
Requires:	festvox-awb-arctic-hts
Requires:	festvox-bdl-arctic-hts
Requires:	festvox-clb-arctic-hts
Requires:	festvox-jmk-arctic-hts
Requires:	festvox-kal-diphone
Requires:	festvox-ked-diphone
Requires:	festvox-rms-arctic-hts
Requires:	festvox-slt-arctic-hts
Requires:	hispavoces-pal-diphone
Requires:	hispavoces-sfl-diphone
Requires:       libcgroup
Requires:       numad
BuildArch:	noarch

%description common
OSDial backend scripts, needed by web, sql, etc.

%package sql
Summary: 	OSDial SQL files and update scripts.
Group:		Applications/Telephony
Requires(post):	coreutils grep sed mysql-server perl gawk procps
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	perl-DBI
Requires:	perl-DBD-MySQL
Requires:	mysql-server >= %{mysql_version}
BuildArch:	noarch

%description sql
OSDial SQL file and update scripts.  Provides a method of
automatically updating the OSDial database, both through the
install package and RPM.

%package web
Summary:	OSDial user interface files
Group:		Applications/Telephony
Requires(post):	coreutils grep httpd perl gawk procps php
Requires:	osdial = %{version}-%{release}
Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-common = %{version}-%{release}
Requires:	php-pear
Requires:	php-pear-Date
Requires:	php-pear-Mail
Requires:	php-pear-Mail-Mime
Requires:	php-mysql
Requires:	ploticus
Requires:	httpd
Requires:	tinymce
BuildArch:      noarch

%description web
OSDial user interface files.  Mainly the php scripts, directory
structure and other supporting files.



%package dialer
Summary:        OSDial Asterisk System and Configuration
Group:          Applications/Telephony
Requires(post):	coreutils grep sed gawk perl
Requires(pre):	osdial = %{version}-%{release}
Requires(pre):	osdial-profile = %{version}-%{release}
Requires(pre): osdial-common = %{version}-%{release}
Requires:       osdial-asterisk-version
Requires:       osdial-sounds
Requires:       php-pear-db
Requires:       gawk
Obsoletes:	osdial-config
BuildArch:      noarch

%description dialer
The is a generic Asterisk configuration that should work out of box for most clients.



%package asterisk-version12
Summary:        OSDial Asterisk 1.2 System
Group:          Applications/Telephony
Requires(post):	coreutils grep sed gawk perl
Requires(pre):	osdial = %{version}-%{release}
Requires(pre):	osdial-profile = %{version}-%{release}
Requires(pre):       osdial-common = %{version}-%{release}
Requires(pre):       osdial-dialer = %{version}-%{release}
Requires:	asterisk12-system
Requires:       libpri12 >= %{libpri12_version}
Requires:       zaptel12 >= %{zaptel12_version}
Requires:       wanpipe12 >= %{wanpipe_version}
Requires:       asterisk12 >= %{asterisk12_version}
Requires:       asterisk12-addons >= %{asterisk12_version}
Requires:       asterisk12-sounds >= %{asterisk12_version}
Requires:       gawk
Provides:	osdial-asterisk-version
Provides:	osdial-asterisk12 = %{version}-%{release}
#Requires:       kernel%{?kpae}%{?kxen}-module-wanpipe12-%{krelver} >= 3.4.4-8
#Requires:	kernel%{?kpae}%{?kxen}-module-zaptel12-%{krelver} >= 1.2.27-9
#Requires:	kernel%{?kpae}%{?kxen}-module-voicetime12-%{krelver} >= 1.0.9-23
#Obsoletes:	osdial-asterisk-version < %{version}-%{release}
#Obsoletes:	osdial-asterisk12 < %{version}-%{release}
#Obsoletes:       wanpipe-util
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-wanpipe
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-zaptel
#Obsoletes:       libpri14
#Obsoletes:       dahdi
#Obsoletes:	dahdi-tools
#Obsoletes:       dahdi16-module
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-dahdi
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-dahdi-%{krelver}
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-wanpipe16
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-wanpipe16-%{krelver}
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-voicetime16
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-voicetime16-%{krelver}
#Obsoletes:       wanpipe-util16
#Obsoletes:      asterisk16
#Obsoletes:      asterisk16-addons
#Obsoletes:      asterisk16-sounds
#Obsoletes:      asterisk16-sounds-en-ulaw
#Obsoletes:      asterisk16-sounds-en-gsm
#Obsoletes:      asterisk16-sounds-en-sln16
#Obsoletes:      asterisk16-sounds-en-wav
#Obsoletes:      asterisk16-sounds-en-g729
#Obsoletes:      asterisk16-g729
Obsoletes:      osdial-asterisk
Conflicts:	osdial-asterisk-version16
BuildArch:      noarch

%description asterisk-version12
This package contains dependency and setup instructions for Asterisk 1.2.


%package asterisk-version16
Summary:        OSDial Asterisk 1.6 System
Group:          Applications/Telephony
Requires(post):	coreutils grep sed gawk perl
Requires(pre):	osdial = %{version}-%{release}
Requires(pre):	osdial-profile = %{version}-%{release}
Requires(pre):       osdial-common = %{version}-%{release}
Requires(pre):       osdial-dialer = %{version}-%{release}
Requires:	asterisk16-system
Requires:       libpri14 >= %{libpri14_version}
Requires:       dahdi >= %{dahdi_version}
Requires:	dahdi-tools >= %{dahdi_tools_version}
Requires:       wanpipe16 >= %{wanpipe_version}
Requires:       asterisk16 >= %{asterisk16_version}
Requires:       asterisk16-addons >= %{asterisk16_version}
Requires:       asterisk16-sounds-en-gsm >= %{asterisk16_version}
#Requires:       asterisk16-sounds-en-g729 >= %{asterisk16_version}
#Requires:       asterisk16-sounds-en-ulaw >= %{asterisk16_version}
Requires:	mysql-server >= %{mysql_version}
Requires:       gawk
Provides:	osdial-asterisk-version
Provides:	osdial-asterisk16 = %{version}-%{release}
#Requires:       kernel%{?kpae}%{?kxen}-module-dahdi-%{krelver} >= 2.2.0.2-999913
#Requires:       kernel%{?kpae}%{?kxen}-module-wanpipe16-%{krelver} >= 3.4.4-8
#Requires:       kernel%{?kpae}%{?kxen}-module-voicetime16-%{krelver} >= 1.0.9-23
#Requires:       wanpipe-util16 >= 3.4.4-8
##Obsoletes:	osdial-asterisk-version < %{version}-%{release}
##Obsoletes:	osdial-asterisk16 < %{version}-%{release}
#Obsoletes:       libpri12
#Obsoletes:       zaptel12
#Obsoletes:       zaptel12-module
#Obsoletes:       wanpipe-util12
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-wanpipe12
#Obsoletes:       kernel%{?kpae}%{?kxen}-module-wanpipe12-%{krelver}
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-zaptel12
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-zaptel12-%{krelver}
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-voicetime
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-voicetime-%{krelver}
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-voicetime12
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-voicetime12-%{krelver}
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-wanpipe-voicetime
#Obsoletes:	kernel%{?kpae}%{?kxen}-module-wanpipe-voicetime-%{krelver}
#Obsoletes:       asterisk12
#Obsoletes:       asterisk12-sounds
#Obsoletes:       asterisk12-addons
#Obsoletes:      asterisk12-g729
Conflicts:	osdial-asterisk-version12
BuildArch:      noarch

%description asterisk-version16
This package contains dependency and setup instructions for Asterisk 1.6.




#%package debuginfo
#Summary:	OSDial debuginfo
#Group:		Applications/Telephony
#BuildArch:      i386
#
#%description debuginfo
#OSDial debuginfo

%package web-template-largedialpresets
Summary:	OSDial user interface files
Group:		Applications/Telephony
Requires:	osdial-web = %{version}-%{release}
Provides:	osdial-template-largedialpresets = %{version}-%{release}
BuildArch:      noarch

%description web-template-largedialpresets
Large Dial Presets Template


%package web-template-highcontrast
Summary:	OSDial user interface files
Group:		Applications/Telephony
#Requires:	osdial = %{version}-%{release}
#Requires:	osdial-common = %{version}-%{release}
#Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Provides:	osdial-template-highcontrast = %{version}-%{release}
BuildArch:      noarch

%description web-template-highcontrast
High Contrast Template

%package nonfree
Summary:	OSDial Non-Free
Group:		Applications/Telephony
Requires:	osdial-web = %{version}-%{release}
Provides:	osdial-nonfree-companies = %{version}-%{release}
BuildArch:      noarch

%description nonfree
OSDial Non-Free

%package nonfree-emailtemplates
Summary:	OSDial Non-Free
Group:		Applications/Telephony
Requires:	osdial-web = %{version}-%{release}
BuildArch:      noarch

%description nonfree-emailtemplates
OSDial Non-Free

%package -n perl-OSDial
Summary:	OSDial user interface files
Group:		Applications/Telephony
BuildRequires:	perl(ExtUtils::MakeMaker), perl
#Requires:	osdial-common = %{version}-%{release}
Requires:	perl
BuildArch:      noarch


%description -n perl-OSDial
This module is inteded to provided quick and easy access to common functions
in OSDial.  The module will read existing configuration files, connect to
the OSDial database, and interface with Asterisk as needed.

%package -n slingdial
Summary:	OSDial user interface files
Group:		Applications/Telephony
#Requires:	osdial = %{version}-%{release}
#Requires:	osdial-common = %{version}-%{release}
#Requires:	osdial-profile = %{version}-%{release}
Requires:	osdial-web = %{version}-%{release}
Provides:	osdial-web-template-slingdial = %{version}-%{release}
Provides:	osdial-template-slingdial = %{version}-%{release}
BuildArch:      noarch

%description -n slingdial
template



%prep
%{__rm} -rf %{buildroot}
%setup -a 0 -n osdial-%{version}
%setup -a 1 -D
%setup -a 2 -D
%setup -a 3 -D

%build
%{__install} -dp %{buildroot}

%install
%{__mkdir_p} %{buildroot}/usr/lib/debug
%{__mkdir_p} %{buildroot}/usr/src/debug
%{__make} DESTDIR=%{buildroot} HTTPDUSER=asterisk install
%{__rm} -f %{buildroot}%{_var}/lib/asterisk/sounds/*.ulaw
%{__rm} -f %{buildroot}%{_var}/lib/asterisk/sounds/*.gsm
%{__rm} -f %{buildroot}%{_var}/lib/asterisk/sounds/*.g729
cd perl
%{__perl} Makefile.PL PREFIX="%{buildroot}%{_prefix}" INSTALLDIRS="vendor"
%{__make} %{?_smp_mflags}
%{__make} install
find %{buildroot} -name .packlist -exec %{__rm} {} \;
find %{buildroot} -name perllocal.pod -exec %{__rm} {} \;
cd ..
%{__mkdir_p} %{buildroot}/etc/httpd/conf.d
%{__mkdir_p} %{buildroot}/etc/init.d
%{__mkdir_p} %{buildroot}/etc/profile.d
%{__mkdir_p} %{buildroot}/opt/osdial/html/ivr
%{__mkdir_p} %{buildroot}/opt/osdial/backups/recordings
%{__mkdir_p} %{buildroot}/opt/osdial/recordings/processing/unmixed
%{__mkdir_p} %{buildroot}/opt/osdial/recordings/processing/mixed
%{__mkdir_p} %{buildroot}/opt/osdial/recordings/completed
%{__mkdir_p} %{buildroot}/opt/osdial/recordings
%{__mkdir_p} %{buildroot}/opt/osdial/reports
%{__mkdir_p} %{buildroot}/opt/osdial/tts
%{__mkdir_p} %{buildroot}/opt/osdial/backups
%{__mkdir_p} %{buildroot}/opt/osdial/backups/recordings
%{__mkdir_p} %{buildroot}/opt/osdial/media
%{__mkdir_p} %{buildroot}/var/log/osdial
%{__mkdir_p} %{buildroot}/var/lib/asterisk/sounds/tts
%{__mkdir_p} %{buildroot}/var/lib/asterisk/sounds/ivr
%{__mkdir_p} %{buildroot}/var/lib/asterisk/sounds/osdial
%{__cp} extras/bash.profile %{buildroot}/etc/profile.d/osdial.sh
%{__cp} extras/httpd-osdial.conf %{buildroot}/etc/httpd/conf.d/osdial.conf
%{__cp} extras/httpd-osdial-archive.conf %{buildroot}/etc/httpd/conf.d/osdial-archive.conf
%{__cp} extras/httpd-osdial-ari.conf %{buildroot}/etc/httpd/conf.d/osdial-ari.conf
%{__cp} extras/httpd-osdial-psi.conf %{buildroot}/etc/httpd/conf.d/osdial-psi.conf
%{__cp} extras/osdial.init %{buildroot}/etc/init.d/osdial
%{__cp} extras/osdial_resource_send.init %{buildroot}/etc/init.d/osdial_resource_send
%{__cp} extras/osdial_resource_listen.init %{buildroot}/etc/init.d/osdial_resource_listen
%{__mkdir_p} %{buildroot}/etc/cron.daily
%{__ln_s} /opt/osdial/bin/AST_ntp_update.sh %{buildroot}/etc/cron.daily
touch %{buildroot}/opt/osdial/html/admin/VMnow.txt

%{__mv} %{buildroot}/opt/osdial/bin/osdial_resource_send.pl %{buildroot}/opt/osdial/bin/osdial_resource_send
%{__mv} %{buildroot}/opt/osdial/bin/osdial_resource_listen.pl %{buildroot}/opt/osdial/bin/osdial_resource_listen

# copy in asterisk configs
%{__mkdir_p} %{buildroot}/etc/asterisk/startup.d
%{__mkdir_p} %{buildroot}/etc/dahdi
echo -e "#!/bin/bash\nexport TTY=screen" > %{buildroot}/etc/asterisk/startup.d/tty_screen.sh
%{__cp} docs/conf_examples/*.conf %{buildroot}/etc/asterisk
%{__cp} docs/conf_examples/README.osdial %{buildroot}/etc/asterisk

%{__perl} -pi -e 's|^VARserver_ip.*|VARserver_ip => 127.0.0.1|' %{buildroot}/etc/osdial.conf
%{__perl} -pi -e 's|^VARHTTP_path.*|VARHTTP_path => http://127.0.0.1|' %{buildroot}/etc/osdial.conf
%{__perl} -pi -e 's|^VARFTP_host.*|VARFTP_host => 127.0.0.1|' %{buildroot}/etc/osdial.conf
%{__perl} -pi -e 's|^VARREPORT_host.*|VARREPORT_host => 127.0.0.1|' %{buildroot}/etc/osdial.conf
#%{__mv} %{buildroot}/etc/asterisk/dahdi_system.conf %{buildroot}/etc/dahdi/system.conf
%{__rm} -f %{buildroot}/etc/asterisk/dahdi_system.conf

echo > %{buildroot}/opt/osdial/.osdial-all
%if 0%{?blah}
echo > %{buildroot}/opt/osdial/.osdial-install-all
echo > %{buildroot}/opt/osdial/.osdial-install-control
echo > %{buildroot}/opt/osdial/.osdial-install-dialer
echo > %{buildroot}/opt/osdial/.osdial-install-sql
echo > %{buildroot}/opt/osdial/.osdial-install-web
echo > %{buildroot}/opt/osdial/.osdial-install-archive
%endif
echo > %{buildroot}/opt/osdial/.osdial-live
echo > %{buildroot}/opt/osdial/.osdial-control
echo > %{buildroot}/opt/osdial/.osdial-dialer
echo > %{buildroot}/opt/osdial/.osdial-dialer-web
echo > %{buildroot}/opt/osdial/.osdial-sql
echo > %{buildroot}/opt/osdial/.osdial-web
echo > %{buildroot}/opt/osdial/.osdial-archive

cd osdial-template-largedialpresets
%{__make} DESTDIR=%{buildroot} install
cd ..

cd osdial-template-highcontrast
%{__make} DESTDIR=%{buildroot} install
cd ..

cd osdial-template-slingdial
%{__make} DESTDIR=%{buildroot} install
cd ..

%{__mkdir_p} %{buildroot}%{_sysconfdir}/security/limits.d
cat > %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf <<EOF
*                  -       core               unlimited
*                  -       data               unlimited
*                  -       fsize              unlimited
*                  -       sigpending         unlimited
*                  -       memlock            unlimited
*                  -       as                 unlimited
*                  -       nofile             999999
*                  -       msgqueue           unlimited
*                  -       cpu                unlimited
*                  -       nproc              unlimited
*                  -       locks              unlimited
EOF
%ifarch x86_64
cat >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf <<EOF
*                  -       stack              8192
EOF
%else
cat >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf <<EOF
*                  -       stack              240
EOF
%endif


%clean
%{__rm} -rf %{buildroot}

%pre
ASTUSER=`id asterisk 2>/dev/null`
RETVAL=$?
if [ $RETVAL -eq 1 ]; then
	/usr/sbin/useradd -c "Asterisk PBX" -G tty -s /bin/bash -r -d "/var/lib/asterisk" asterisk > /dev/null 2>&1
fi
if [ ! -d "/var/lib/asterisk" ]; then
	/bin/mkdir -p /var/lib/asterisk
	/bin/chown asterisk:asterisk /var/lib/asterisk
fi
UUIDPW=`/usr/bin/uuidgen`
if [ -f "/var/lib/asterisk/.asterisk_pw" ]; then
	UUIDPW=`/bin/cat /var/lib/asterisk/.asterisk_pw`
fi
if [ ! -f "/opt/osdial/.osdial-archive" ]; then
	echo "$UUIDPW" | /usr/bin/passwd --stdin asterisk > /dev/null 2>&1
fi
echo "$UUIDPW" > /var/lib/asterisk/.asterisk_pw
/bin/chown root:root /var/lib/asterisk/.asterisk_pw
/bin/chmod 600 /var/lib/asterisk/.asterisk_pw
if [ ! -f "/root/.ssh/id_rsa" ]; then
	/usr/bin/ssh-keygen -t rsa -f /root/.ssh/id_rsa -q -N '' 2>/dev/null
fi
RKEY=`/bin/cat /root/.ssh/id_rsa.pub 2>/dev/null`
if [ ! -d "/var/lib/asterisk/.ssh" ]; then
	/bin/mkdir -p /var/lib/asterisk/.ssh
fi
if [ ! -f "/var/lib/asterisk/.ssh/authorized_keys" ]; then
	/bin/echo "$RKEY" > /var/lib/asterisk/.ssh/authorized_keys
fi
KCHK=`/bin/grep "$RKEY" /var/lib/asterisk/.ssh/authorized_keys`
if [ -z "$KCHK" ]; then
	/bin/echo "$RKEY" > /var/lib/asterisk/.ssh/authorized_keys
fi
/bin/chown -R asterisk:asterisk /var/lib/asterisk/.ssh
/bin/chmod 700 /var/lib/asterisk/.ssh
/bin/chmod 600 /var/lib/asterisk/.ssh/*
exit 0

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
			%{__ln_s} /usr/share/syslinux /usr/lib/syslinux
		fi
	fi
	/sbin/service syslog stop > /dev/null 2>&1
fi
echo -n


%if 0%{?blah}
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

%endif





%post common
/sbin/chkconfig --add osdial > /dev/null 2>&1
INTY=$1
if [ "$INTY" -eq 1 ]; then
        /sbin/chkconfig --del osdial_resource_send > /dev/null 2>&1
        /sbin/chkconfig --add osdial_resource_send > /dev/null 2>&1
	/sbin/service osdial_resource_send restart > /dev/null 2>&1
        /sbin/chkconfig osdial on > /dev/null 2>&1
	# Make sure SELINUX didn't get turned on...
	if [ -f /etc/selinux/config ]; then
        	SELINUX="`%{__grep} '^SELINUX=' /etc/selinux/config | %{__awk} -F= '{ print $2 }'`"
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
	/opt/osdial/bin/osdial_killall.sh
	%{__mkdir_p} /opt/osdial/backups/%{version}-%{release} > /dev/null 2>&1
	%{__cp} -a /opt/osdial/bin /opt/osdial/backups/%{version}-%{release} > /dev/null 2>&1

	%{__mkdir} /opt/osdial/backups/%{version}-%{release}/etc > /dev/null 2>&1
	%{__cp} -a /etc/osdial.conf /opt/osdial/backups/%{version}-%{release}/etc > /dev/null 2>&1
	if [ -d /etc/asterisk ]; then
		[ -f /etc/dahdi/system.conf ] && %{__cp} -a /etc/dahdi/system.conf /opt/osdial/backups/%{version}-%{release}/etc > /dev/null 2>&1
		[ -f /etc/zaptel.conf ] && %{__cp} -a /etc/zaptel.conf /opt/osdial/backups/%{version}-%{release}/etc > /dev/null 2>&1
		[ -d /etc/asterisk ] && %{__cp} -a /etc/asterisk /opt/osdial/backups/%{version}-%{release}/etc > /dev/null 2>&1
	fi
	if [ -d /var/lib/asterisk/agi-bin ]; then
		%{__mkdir} /opt/osdial/backups/%{version}-%{release}/agi > /dev/null 2>&1
		%{__cp} -a /var/lib/asterisk/agi-bin /opt/osdial/backups/%{version}-%{release}/agi > /dev/null 2>&1
	fi
	if [ -d /opt/osdial/html ]; then
		%{__mkdir} /opt/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1
		%{__mkdir} /opt/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1
		%{__mkdir} /opt/osdial/backups/%{version}-%{release}/html/agent > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/*.php /opt/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/*.txt /opt/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/*.ico /opt/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/ivr /opt/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/admin/*.php /opt/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/admin/*.pl /opt/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/admin/*.css /opt/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/admin/*.gif /opt/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/admin/include /opt/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/admin/templates /opt/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/agent/*.php /opt/osdial/backups/%{version}-%{release}/html/agent > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/agent/include /opt/osdial/backups/%{version}-%{release}/html/agent > /dev/null 2>&1
		%{__cp} -a /opt/osdial/html/agent/templates /opt/osdial/backups/%{version}-%{release}/html/agent > /dev/null 2>&1
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
	/opt/osdial/bin/sql/upgrade_sql.pl --info

	/opt/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1

        /sbin/chkconfig --del osdial_resource_send > /dev/null 2>&1
        /sbin/chkconfig --add osdial_resource_send > /dev/null 2>&1
	/sbin/service osdial_resource_send restart > /dev/null 2>&1
fi
%{__mkdir_p} /opt/osdial/tts > /dev/null 2>&1
%{__mkdir_p} /opt/osdial/reports > /dev/null 2>&1
%{__mkdir_p} /opt/osdial/recordings > /dev/null 2>&1
%{__mkdir_p} /opt/osdial/recordings/processing > /dev/null 2>&1
%{__mkdir_p} /opt/osdial/recordings/processing/mixed > /dev/null 2>&1
%{__mkdir_p} /opt/osdial/recordings/processing/unmixed > /dev/null 2>&1
%{__mkdir_p} /opt/osdial/recordings/completed > /dev/null 2>&1
%{__mkdir_p} /opt/osdial/backups/recordings > /dev/null 2>&1
%{__chown} -R asterisk:asterisk /opt/osdial/backups/recordings > /dev/null 2>&1
%{__chown} -R asterisk:asterisk /opt/osdial/tts > /dev/null 2>&1
[ -z "`%{__grep} /opt/osdial/reports /proc/mounts`" ] && %{__chown} -R asterisk:asterisk /opt/osdial/reports > /dev/null 2>&1 || :
[ -z "`%{__grep} /opt/osdial/recordings /proc/mounts`" ] && %{__chown} -R asterisk:asterisk /opt/osdial/recordings > /dev/null 2>&1 || :
%{__chmod} 7755 /usr/sbin/lsof > /dev/null 2>&1
%{__ln_s} -f /usr/bin/ip_relay /opt/osdial/bin/ip_relay > /dev/null 2>&1

if [ -n "`%{__grep} OSDbuild /etc/osdial.conf`" ]; then
	%{__perl} -pi -e 's|^OSDversion =>.*|OSDversion => %{version}|' /etc/osdial.conf
	%{__perl} -pi -e 's|^OSDbuild =>.*|OSDbuild => %{buildver}|' /etc/osdial.conf
else
	%{__perl} -pi -e 's|^OSDversion =>.*|OSDversion => %{version}\nOSDbuild => %{buildver}|' /etc/osdial.conf
fi
%{__perl} -pi -e 's|^PATHdocs =>.*|PATHdocs => /usr/share/doc/osdial-%{version}|' /etc/osdial.conf
[ -z "`%{__grep} PATHarchive_backup /etc/osdial.conf`" ] && %{__perl} -pi -e 's|^PATHarchive_home =>.*|PATHarchive_home => /opt/osdial/recordings\nPATHarchive_backup => /opt/osdial/backups/recordings|' /etc/osdial.conf || :

%{__perl} -pi -e 's|stacks|stack|' /etc/security/limits.conf
#if [ -z "`%{__grep} OSDial /etc/security/limits.conf`" ]; then
#	echo "" >> /etc/security/limits.conf
#	echo "# OSDial modifications" >> /etc/security/limits.conf
#	echo "root            soft    core             unlimited" >> /etc/security/limits.conf
#	echo "root            hard    core             unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    core             unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    core             unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    core             unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    core             unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    core             unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    core             unlimited" >> /etc/security/limits.conf
#	echo "root            soft    data             unlimited" >> /etc/security/limits.conf
#	echo "root            hard    data             unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    data             unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    data             unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    data             unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    data             unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    data             unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    data             unlimited" >> /etc/security/limits.conf
#	echo "root            soft    fsize            unlimited" >> /etc/security/limits.conf
#	echo "root            hard    fsize            unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    fsize            unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    fsize            unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    fsize            unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    fsize            unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    fsize            unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    fsize            unlimited" >> /etc/security/limits.conf
#	echo "root            soft    memlock          unlimited" >> /etc/security/limits.conf
#	echo "root            hard    memlock          unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    memlock          unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    memlock          unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    memlock          unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    memlock          unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    memlock          unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    memlock          unlimited" >> /etc/security/limits.conf
#	echo "root            soft    nofile           8192" >> /etc/security/limits.conf
#	echo "root            hard    nofile           65535" >> /etc/security/limits.conf
#	echo "asterisk        soft    nofile           16384" >> /etc/security/limits.conf
#	echo "asterisk        hard    nofile           16384" >> /etc/security/limits.conf
#	echo "apache          soft    nofile           8192" >> /etc/security/limits.conf
#	echo "apache          hard    nofile           8192" >> /etc/security/limits.conf
#	echo "mysql           soft    nofile           32768" >> /etc/security/limits.conf
#	echo "mysql           hard    nofile           32768" >> /etc/security/limits.conf
#	echo "root            soft    msgqueue         unlimited" >> /etc/security/limits.conf
#	echo "root            hard    msgqueue         unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    msgqueue         unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    msgqueue         unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    msgqueue         unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    msgqueue         unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    msgqueue         unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    msgqueue         unlimited" >> /etc/security/limits.conf
#	echo "root            soft    cpu              unlimited" >> /etc/security/limits.conf
#	echo "root            hard    cpu              unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    cpu              unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    cpu              unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    cpu              unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    cpu              unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    cpu              unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    cpu              unlimited" >> /etc/security/limits.conf
#	echo "root            soft    nproc            unlimited" >> /etc/security/limits.conf
#	echo "root            hard    nproc            unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    nproc            unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    nproc            unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    nproc            unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    nproc            unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    nproc            unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    nproc            unlimited" >> /etc/security/limits.conf
#	echo "root            soft    sigpending       unlimited" >> /etc/security/limits.conf
#	echo "root            hard    sigpending       unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    sigpending       unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    sigpending       unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    sigpending       unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    sigpending       unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    sigpending       unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    sigpending       unlimited" >> /etc/security/limits.conf
#	echo "root            soft    stack            unlimited" >> /etc/security/limits.conf
#	echo "root            hard    stack            unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    stack            unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    stack            unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    stack            8192" >> /etc/security/limits.conf
#	echo "apache          hard    stack            20480" >> /etc/security/limits.conf
#	echo "mysql           soft    stack            8192" >> /etc/security/limits.conf
#	echo "mysql           hard    stack            20480" >> /etc/security/limits.conf
#	echo "root            soft    locks            unlimited" >> /etc/security/limits.conf
#	echo "root            hard    locks            unlimited" >> /etc/security/limits.conf
#	echo "asterisk        soft    locks            unlimited" >> /etc/security/limits.conf
#	echo "asterisk        hard    locks            unlimited" >> /etc/security/limits.conf
#	echo "apache          soft    locks            unlimited" >> /etc/security/limits.conf
#	echo "apache          hard    locks            unlimited" >> /etc/security/limits.conf
#	echo "mysql           soft    locks            unlimited" >> /etc/security/limits.conf
#	echo "mysql           hard    locks            unlimited" >> /etc/security/limits.conf
#fi
if [ -n "`%{__grep} OSDial /etc/security/limits.conf`" ]; then
	%{__sed} -ie '/# OSDial modifications/,//d' /etc/security/limits.conf
fi
if [ -z "`%{__grep} scan_sleep_millisecs /etc/rc.local`" ]; then
	echo >> /etc/rc.local.conf
	echo "# Set scan_sleep_millisecs at the recomendation of numad." >> /etc/rc.local
	echo "if [ -f /sys/kernel/mm/redhat_transparent_hugepage/khugepaged/scan_sleep_millisecs ]; then" >> /etc/rc.local
	echo "    echo 100 > /sys/kernel/mm/redhat_transparent_hugepage/khugepaged/scan_sleep_millisecs" >> /etc/rc.local
	echo "fi" >> /etc/rc.local
fi
if [ -f /sys/kernel/mm/redhat_transparent_hugepage/khugepaged/scan_sleep_millisecs ]; then
	echo 100 > /sys/kernel/mm/redhat_transparent_hugepage/khugepaged/scan_sleep_millisecs
fi
/sbin/chkconfig cgconfig on > /dev/null 2>&1
/sbin/service cgconfig restart > /dev/null 2>&1
/sbin/chkconfig cgred on > /dev/null 2>&1
/sbin/service cgred restart > /dev/null 2>&1
/sbin/chkconfig numad on > /dev/null 2>&1
/sbin/service numad restart > /dev/null 2>&1
/sbin/chkconfig httpd on > /dev/null 2>&1
/sbin/service httpd restart > /dev/null 2>&1
DRIVES=`ls /dev/sd[a-z] /dev/hd[a-z] /dev/cciss/c[0-9]d[0-9] 2>/dev/null | tr "\n" "," | sed 's|,$||'`
%{__perl} -pi -e "s|/dev/sda, /dev/sdb|${DRIVES}|" /opt/osdial/html/phpsysinfo/plugins/SMART/SMART.config.php
/usr/sbin/usermod -G asterisk,disk apache
/usr/sbin/usermod -G tty,apache,disk asterisk
echo -n



%post sql
INTY=$1
if [ "$INTY" -eq 1 ]; then
	/sbin/chkconfig mysqld on > /dev/null 2>&1
	# Apply OSDial SQL changes to /etc/my.cnf
	if [ ! "`%{__grep} innodb_data_home_dir /etc/my.cnf`" ]; then
		MEM=`head -1 /proc/meminfo | %{__sed} 's/MemTotal:\s*\(.*\) kB.*/\1/'`
		let MEM=MEM/1024/2
		MCNF="old_passwords=1\n\n"
		MCNF="${MCNF}#===== BEGIN OSDIAL my.cnf Additions =====\n"
		[ -z "`%{__grep} skip_name_resolve /etc/my.cnf`" ] &&               MCNF="${MCNF}skip_name_resolve\n"
		[ -z "`%{__grep} max_connections /etc/my.cnf`" ] &&                 MCNF="${MCNF}max_connections=250\n"
		[ -z "`%{__grep} open_files_limit /etc/my.cnf`" ] &&                MCNF="${MCNF}open_files_limit=32768\n"
		[ -z "`%{__grep} query_cache_type /etc/my.cnf`" ] &&                MCNF="${MCNF}query_cache_type = 1\nquery_cache_size = 100000000\nquery_cache_min_res_unit = 4096\nquery_cache_limit = 1048576\nquery_prealloc_size = 8192\nquery_cache_wlock_invalidate = 0\n"
		[ -z "`%{__grep} innodb_strict_mode /etc/my.cnf`" ] &&              MCNF="${MCNF}loose_innodb_strict_mode = 1\n"
		[ -z "`%{__grep} innodb_file_format /etc/my.cnf`" ] &&              MCNF="${MCNF}loose_innodb_file_format = barracuda\n"
		[ -z "`%{__grep} innodb_data_home_dir /etc/my.cnf`" ] &&            MCNF="${MCNF}loose_innodb_data_home_dir = /var/lib/mysql/\n"
		[ -z "`%{__grep} innodb_log_group_home_dir /etc/my.cnf`" ] &&       MCNF="${MCNF}loose_innodb_log_group_home_dir = /var/lib/mysql/\n"
		[ -z "`%{__grep} innodb_data_file_path /etc/my.cnf`" ] &&           MCNF="${MCNF}loose_innodb_data_file_path = ibdata1:10M:autoextend\n"
		[ -z "`%{__grep} innodb_additional_mem_pool_size /etc/my.cnf`" ] && MCNF="${MCNF}loose_innodb_additional_mem_pool_size = 8M\n"
		[ -z "`%{__grep} innodb_log_file_size /etc/my.cnf`" ] &&            MCNF="${MCNF}loose_innodb_log_file_size = 5M\n"
		[ -z "`%{__grep} innodb_log_buffer_size /etc/my.cnf`" ] &&          MCNF="${MCNF}loose_innodb_log_buffer_size = 8M\n"
		[ -z "`%{__grep} innodb_file_per_table /etc/my.cnf`" ] &&           MCNF="${MCNF}loose_innodb_file_per_table = 1\n"
		[ -z "`%{__grep} innodb_flush_log_at_trx_commit /etc/my.cnf`" ] &&  MCNF="${MCNF}loose_innodb_flush_log_at_trx_commit = 2\n"
		[ -z "`%{__grep} innodb_lock_wait_timeout /etc/my.cnf`" ] &&        MCNF="${MCNF}loose_innodb_lock_wait_timeout = 50\n"
		[ -z "`%{__grep} innodb_adaptive_hash_index /etc/my.cnf`" ] &&      MCNF="${MCNF}loose_innodb_adaptive_hash_index = 1\n"
		[ -z "`%{__grep} innodb_checksums /etc/my.cnf`" ] &&                MCNF="${MCNF}loose_innodb_checksums = 1\n"
		[ -z "`%{__grep} innodb_doublewrite /etc/my.cnf`" ] &&              MCNF="${MCNF}loose_innodb_doublewrite = 1\n"
		[ -z "`%{__grep} innodb_flush_method /etc/my.cnf`" ] &&             MCNF="${MCNF}loose_innodb_flush_method = O_DIRECT\n"
		[ -z "`%{__grep} innodb_locks_unsafe_for_binlog /etc/my.cnf`" ] &&  MCNF="${MCNF}loose_innodb_locks_unsafe_for_binlog = 0\n"
		[ -z "`%{__grep} innodb_max_dirty_pages_pct /etc/my.cnf`" ] &&      MCNF="${MCNF}loose_innodb_max_dirty_pages_pct = 90\n"
		[ -z "`%{__grep} innodb_table_locks /etc/my.cnf`" ] &&              MCNF="${MCNF}loose_innodb_table_locks = 1\n"
		[ -z "`%{__grep} innodb_thread_concurrency /etc/my.cnf`" ] &&       MCNF="${MCNF}loose_innodb_thread_concurrency = 0\n"
		[ -z "`%{__grep} innodb_use_sys_malloc /etc/my.cnf`" ] &&           MCNF="${MCNF}loose_innodb_use_sys_malloc = 0\n"
		[ -z "`%{__grep} innodb_fast_shutdown /etc/my.cnf`" ] &&            MCNF="${MCNF}loose_innodb_fast_shutdown = 0\n"
		[ -z "`%{__grep} innodb_open_files /etc/my.cnf`" ] &&               MCNF="${MCNF}loose_innodb_open_files = 2048\n"
		[ -z "`%{__grep} innodb_buffer_pool_size /etc/my.cnf`" ] &&         MCNF="${MCNF}# Should be set to 50% system memory\n"
		[ -z "`%{__grep} innodb_buffer_pool_size /etc/my.cnf`" ] &&         MCNF="${MCNF}loose_innodb_buffer_pool_size = ${MEM}M\n"
		MCNF="${MCNF}#===== END OSDIAL my.cnf Additions =====\n\n"
		%{__perl} -pi -e "s|old_passwords=1|$MCNF|" /etc/my.cnf
		# Restart mysql
		/sbin/service mysqld restart > /dev/null 2>&1
	fi
	# Run update script.
	[ -f "/opt/osdial/.osdial-live" ] && /sbin/service mysqld start > /dev/null 2>&1 || :
	/opt/osdial/bin/sql/upgrade_sql.pl --install 2> /dev/null
	[ -f "/opt/osdial/.osdial-live" ] && /sbin/service mysqld stop > /dev/null 2>&1 || :
	# If it didn't get created, assume it is an installcd
	[ ! -d "/var/lib/mysql/osdial" ] && echo "OSDIAL_MYSQL_INSTALL=YES" >> /etc/sysconfig/osdial
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
	# Reset running procs.
	[ -n "`ps -ef | %{__grep} FastAGI`" ] && kill -9 `ps -ef | %{__grep} FastAGI | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
	[ -n "`ps -ef | %{__grep} AST`" ] && kill -9 `ps -ef | %{__grep} AST | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n

%post web
INTY=$1
if [ "$INTY" -eq 1 ]; then
        /sbin/chkconfig --del osdial_resource_listen > /dev/null 2>&1
        /sbin/chkconfig --add osdial_resource_listen > /dev/null 2>&1
	/sbin/service osdial_resource_listen restart > /dev/null 2>&1
	/sbin/chkconfig httpd on > /dev/null 2>&1
	if [ -f "/var/www/html/index.html" ]; then
		[ -n "`%{__grep} osdial /var/www/html/index.html`" ] && %{__mv} /var/www/html/index.html /var/www/html/index.html.bak || :
	fi
	[ ! -f /var/www/html/index.php ] && %{__ln_s} /opt/osdial/html/index.php /var/www/html/index.php || :
	# modify php.ini for our defaults.
	if [ ! "`%{__grep} OSDIAL /etc/php.ini`" ]; then
		%{__perl} -pi -e "s|^max_execution_time = 30     ; Maximum execution|max_execution_time = 300000 ; Maximum execution|" /etc/php.ini
		%{__perl} -pi -e "s|^max_input_time = 60    ; Maximum amount of time|max_input_time = 600000 ; Maximum amount of time|" /etc/php.ini
		%{__perl} -pi -e "s|^memory_limit = 16M      ; Maximum amount of mem|memory_limit = 512M      ; Maximum amount of mem|" /etc/php.ini
		%{__perl} -pi -e "s|^memory_limit = 128M$|memory_limit = 512M|" /etc/php.ini
		%{__perl} -pi -e "s|^;error_reporting = E_ALL \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_NOTICE|" /etc/php.ini
		%{__perl} -pi -e "s|^error_reporting  =  E_ALL|;error_reporting  =  E_ALL|" /etc/php.ini
		%{__perl} -pi -e "s|^post_max_size = 8M|post_max_size = 100M|" /etc/php.ini
		%{__perl} -pi -e "s|^upload_max_filesize = 2M|upload_max_filesize = 100M|" /etc/php.ini
		%{__perl} -pi -e "s|^short_open_tag = Off$|short_open_tag = On|" /etc/php.ini
		echo "; OSDIAL: modified" >> /etc/php.ini
		/sbin/service httpd restart > /dev/null 2>&1 t
	fi
	[ -f "/opt/osdial/.osdial-live" ] && /sbin/service httpd stop > /dev/null 2>&1 || :
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
        /sbin/chkconfig --del osdial_resource_listen > /dev/null 2>&1
        /sbin/chkconfig --add osdial_resource_listen > /dev/null 2>&1
	/sbin/service osdial_resource_listen restart > /dev/null 2>&1
	if [ -f "/var/www/html/index.html" ]; then
		[ -n "`%{__grep} osdial /var/www/html/index.html`" ] && %{__mv} /var/www/html/index.html /var/www/html/index.html.bak || :
	fi
	[ ! -f /var/www/html/index.php ] && %{__ln_s} /opt/osdial/html/index.php /var/www/html/index.php || :
	# Reset running procs.
	[ -n "`ps -ef | %{__grep} FastAGI`" ] && kill -9 `ps -ef | %{__grep} FastAGI | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
	[ -n "`ps -ef | %{__grep} AST`" ] && kill -9 `ps -ef | %{__grep} AST | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n

%post dialer
INTY=$1
if [ "$INTY" -eq 1 ]; then
	# Put in some ramdisk on the dialer.
	if [ "`%{__grep} OSDIAL /etc/rc.local`" ]; then
		OSDTMP=/tmp/osdtmp.$$
		%{__sed} -e '/BEGIN OSDIAL/,/END OSDIAL/d' /etc/rc.local > $OSDTMP
		%{__sed} -e '/ramdisk/d' $OSDTMP > /etc/rc.local
	fi
	# Lets turn on the cron!
	if [ -f /var/spool/cron/asterisk ]; then
        	CRGRP="`%{__grep} ADMIN_keepalive_ALL /var/spool/cron/asterisk`"
        	if [ -n "$CRGRP" ]; then
                	# It already exists, so lets overwrite our existing.
                	echo "    osdial-config: Cron for user 'asterisk' already in place."
                	%{__cat} /var/spool/cron/asterisk > /opt/osdial/bin/osdial.cron
                	%{__rm} -f /var/spool/cron/asterisk
        	fi
	fi
	echo "    osdial-config: Installing cron for user 'asterisk'."
	/usr/bin/crontab -u asterisk /opt/osdial/bin/osdial.cron > /dev/null 2>&1
	# If it didn't succeed, assume installcd
	[ ! -f "/var/spool/cron/asterisk" ] && %{__cp} /opt/osdial/bin/osdial.cron /var/spool/cron/asterisk || :
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
[ "$INTY" -eq 2 ] && echo -n || :
# Reset running procs.
[ -n "`ps -ef | %{__grep} FastAGI`" ] && kill -9 `ps -ef | %{__grep} FastAGI | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
[ -n "`ps -ef | %{__grep} AST`" ] && kill -9 `ps -ef | %{__grep} AST | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
# Make sure we have backups of the prompts and sync them to sounds and sounds.ramfs
%{__mkdir_p} /var/lib/asterisk/OSDprompts
[ -d "/var/lib/asterisk/sounds.ramfs" ] && yes | %{__cp} -au /var/lib/asterisk/sounds.ramfs/851* /var/lib/asterisk/OSDprompts > /dev/null 2>&1 || :
[ -d "/mnt/ramdisk/sounds" ] && yes | %{__cp} -au /mnt/ramdisk/sounds/851* /var/lib/asterisk/OSDprompts > /dev/null 2>&1 || :
yes | %{__cp} -au /var/lib/asterisk/sounds/851* /var/lib/asterisk/OSDprompts > /dev/null 2>&1
yes | %{__cp} -au /var/lib/asterisk/OSDprompts/851* /var/lib/asterisk/sounds > /dev/null 2>&1
[ -d "/var/lib/asterisk/sounds.ramfs" ] && yes | %{__cp} -au /var/lib/asterisk/OSDprompts/851* /var/lib/asterisk/sounds.ramfs > /dev/null 2>&1 || :
[ -d "/mnt/ramdisk/sounds" ] && yes | %{__cp} -au /var/lib/asterisk/OSDprompts/851* /mnt/ramdisk/sounds > /dev/null 2>&1 || :
%{__chown} -R asterisk:asterisk /var/lib/asterisk/sounds
%{__chown} -R asterisk:asterisk /var/lib/asterisk/OSDprompts
[ -d "/var/lib/asterisk/sounds.ramfs" ] && %{__chown} -R asterisk:asterisk /var/lib/asterisk/sounds.ramfs || :
[ -d "/mnt/ramdisk/sounds" ] && %{__chown} -R asterisk:asterisk /mnt/ramdisk/sounds || :
if [ -d "/var/lib/asterisk/sounds/osdial" ]; then
	%{__chmod} 0777 /var/lib/asterisk/sounds/osdial > /dev/null 2>&1
	%{__chmod} 0666 /var/lib/asterisk/sounds/osdial/* > /dev/null 2>&1
fi
if [ -d "/mnt/ramdisk/sounds/osdial" ]; then
	%{__chmod} 0777 /mnt/ramdisk/sounds/osdial > /dev/null 2>&1
	%{__chmod} 0666 /mnt/ramdisk/sounds/osdial/* > /dev/null 2>&1
fi
if [ -d "/opt/osdial/media" ]; then
	%{__chmod} 0777 /opt/osdial/media > /dev/null 2>&1
	%{__chmod} 0666 /opt/osdial/media/* > /dev/null 2>&1
fi
/opt/osdial/bin/osdial_media_sync.pl --file=/var/lib/asterisk/sounds/en/vm-goodbye.ulaw > /dev/null 2>&1
/opt/osdial/bin/osdial_media_sync.pl --file=/var/lib/asterisk/sounds/generic_hold.ulaw > /dev/null 2>&1


# Reload dsp.conf to ensure we have a silence_threshold
/usr/sbin/asterisk -rx "reload dsp" > /dev/null 2>&1
cd /opt/osdial/html/ari/bin
tar xzf aribins.tgz .
echo -n

%post asterisk-version16
INTY=$1
/sbin/chkconfig asterisk on > /dev/null 2>&1
# cpuspeed can do bad things to ISDN/T1 cards
if [ -f /etc/rc3.d/S06cpuspeed ]; then
               	echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
               	/etc/init.d/cpuspeed stop > /dev/null 2>&1
               	/sbin/chkconfig cpuspeed off > /dev/null 2>&1
fi
# Set some performance options in asterisk...
if [ -f "/etc/asterisk/asterisk.conf" ]; then
        	%{__perl} -pi -e 's|^;timestamp = yes|timestamp = yes|' /etc/asterisk/asterisk.conf
        	%{__perl} -pi -e 's|^timestamp = no|timestamp = yes|' /etc/asterisk/asterisk.conf

        	%{__perl} -pi -e 's|^;highpriority = yes|highpriority = yes|' /etc/asterisk/asterisk.conf
        	%{__perl} -pi -e 's|^highpriority = no|highpriority = yes|' /etc/asterisk/asterisk.conf

        	%{__perl} -pi -e 's|^;internal_timing = yes|internal_timing = yes|' /etc/asterisk/asterisk.conf
        	%{__perl} -pi -e 's|^internal_timing = no|internal_timing = yes|' /etc/asterisk/asterisk.conf

        	%{__perl} -pi -e 's|^;transmit_silence = yes|transmit_silence = no|' /etc/asterisk/asterisk.conf
        	%{__perl} -pi -e 's|^transmit_silence = yes|transmit_silence = no|' /etc/asterisk/asterisk.conf

        	%{__perl} -pi -e 's|^;transmit_silence_during_record = yes|transmit_silence_during_record = yes|' /etc/asterisk/asterisk.conf
        	%{__perl} -pi -e 's|^transmit_silence_during_record = no|transmit_silence_during_record = yes|' /etc/asterisk/asterisk.conf

        	%{__perl} -pi -e 's|^;transcode_via_sln = yes|transcode_via_sln = no|' /etc/asterisk/asterisk.conf
        	%{__perl} -pi -e 's|^transcode_via_sln = yes|transcode_via_sln = no|' /etc/asterisk/asterisk.conf

        	%{__perl} -pi -e 's|^;cache_record_files = yes|cache_record_files = yes|' /etc/asterisk/asterisk.conf
        	%{__perl} -pi -e 's|^cache_record_files = no|cache_record_files = yes|' /etc/asterisk/asterisk.conf

        	%{__perl} -pi -e 's|^;record_cache_dir = /tmp|record_cache_dir = /var/spool/asterisk/record_cache|' /etc/asterisk/asterisk.conf
        	%{__perl} -pi -e 's|^record_cache_dir = .*$|record_cache_dir = /var/spool/asterisk/record_cache|' /etc/asterisk/asterisk.conf
fi
# Remove bad chan-dahdi.conf, bad filename.
[ -f "/etc/asterisk/chan-dahdi.conf" ] && %{__rm} -f /etc/asterisk/chan-dahdi.conf > /dev/null 2>&1 || :
# Find and move zapata.conf
%{__mkdir_p} /etc/zaptel.bak
MOVED=0
if [ -f "/etc/asterisk/zapata.conf" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		%{__cp} -f /etc/asterisk/zapata.conf /etc/zaptel.bak > /dev/null 2>&1
		%{__mv} -f /etc/asterisk/zapata.conf /etc/asterisk/chan_dahdi.conf > /dev/null 2>&1
	else
		%{__mv} -f /etc/asterisk/zapata.conf /etc/zaptel.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/zapata.conf.rpmsave" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		%{__cp} -f /etc/asterisk/zapata.conf.rpmsave /etc/zaptel.bak > /dev/null 2>&1
		%{__mv} -f /etc/asterisk/zapata.conf.rpmsave /etc/asterisk/chan_dahdi.conf > /dev/null 2>&1
	else
		%{__mv} -f /etc/asterisk/zapata.conf.rpmsave /etc/zaptel.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/zapata.conf.rpmorig" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		%{__cp} -f /etc/asterisk/zapata.conf.rpmorig /etc/zaptel.bak > /dev/null 2>&1
		%{__mv} -f /etc/asterisk/zapata.conf.rpmorig /etc/asterisk/chan_dahdi.conf > /dev/null 2>&1
	else
		%{__mv} -f /etc/asterisk/zapata.conf.rpmorig /etc/zaptel.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/zapata.conf.rpmnew" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		%{__cp} -f /etc/asterisk/zapata.conf.rpmnew /etc/zaptel.bak > /dev/null 2>&1
		%{__mv} -f /etc/asterisk/zapata.conf.rpmnew /etc/asterisk/chan_dahdi.conf > /dev/null 2>&1
	else
		%{__mv} -f /etc/asterisk/zapata.conf.rpmnew /etc/zaptel.bak > /dev/null 2>&1
	fi
fi
echo -n

%post asterisk-version12
INTY=$1
/sbin/chkconfig asterisk on > /dev/null 2>&1
# cpuspeed can do bad things to ISDN/T1 cards
if [ -f /etc/rc3.d/S06cpuspeed ]; then
               	echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
               	/etc/init.d/cpuspeed stop > /dev/null 2>&1
               	/sbin/chkconfig cpuspeed off > /dev/null 2>&1
fi
# Find and move chan_dahdi.conf
%{__mkdir_p} /etc/dahdi.bak
MOVED=0
if [ -f "/etc/asterisk/chan_dahdi.conf" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		%{__cp} -f /etc/asterisk/chan_dahdi.conf /etc/dahdi.bak > /dev/null 2>&1
		%{__mv} -f /etc/asterisk/chan_dahdi.conf /etc/asterisk/zapata.conf > /dev/null 2>&1
	else
		%{__mv} -f /etc/asterisk/chan_dahdi.conf /etc/dahdi.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/chan_dahdi.conf.rpmsave" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		%{__cp} -f /etc/asterisk/chan_dahdi.conf.rpmsave /etc/dahdi.bak > /dev/null 2>&1
		%{__mv} -f /etc/asterisk/chan_dahdi.conf.rpmsave /etc/asterisk/zapata.conf > /dev/null 2>&1
	else
		%{__mv} -f /etc/asterisk/chan_dahdi.conf.rpmsave /etc/dahdi.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/chan_dahdi.conf.rpmorig" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		%{__cp} -f /etc/asterisk/chan_dahdi.conf.rpmorig /etc/dahdi.bak > /dev/null 2>&1
		%{__mv} -f /etc/asterisk/chan_dahdi.conf.rpmorig /etc/asterisk/zapata.conf > /dev/null 2>&1
	else
		%{__mv} -f /etc/asterisk/chan_dahdi.conf.rpmorig /etc/dahdi.bak > /dev/null 2>&1
	fi
fi
if [ -f "/etc/asterisk/chan_dahdi.conf.rpmnew" ]; then
	if [ "$MOVED" = "0" ]; then
		MOVED=1
		%{__cp} -f /etc/asterisk/chan_dahdi.conf.rpmnew /etc/dahdi.bak > /dev/null 2>&1
		%{__mv} -f /etc/asterisk/chan_dahdi.conf.rpmnew /etc/asterisk/zapata.conf > /dev/null 2>&1
	else
		%{__mv} -f /etc/asterisk/chan_dahdi.conf.rpmnew /etc/dahdi.bak > /dev/null 2>&1
	fi
fi
echo -n


%post profile-all
INTY=$1
if [ "$INTY" -eq 2 ]; then
	CTB="/var/spool/cron/asterisk"
	if [ -f "$CTB" ]; then
		if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
			echo -e "29 0 * * * /usr/bin/find /var/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
		fi


		if [ -z "`%{__grep} 'remove old csv exports more than 2 days old' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
			echo -e "24 0 * * * /usr/bin/find /opt/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_external_dnc $CTB`" ]; then
			echo -e "" >> $CTB
			echo "### (sql) Actual Scrub against external DNC" >> $CTB
			echo "* * * * * /opt/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
			echo -e "" >> $CTB
			echo "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
			echo "0 1 * * * /opt/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_CLEAR_auto_calls $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_flush_DBqueue $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
			echo -e "11,41 * * * * /opt/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_cleanup_agent_log $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
			echo -e "33 * * * * /opt/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_VDhopper $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) updater for OSDial hopper" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_adjust_GMTnow_on_leads $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
			echo -e "1 1,7 * * * /opt/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_DB_optimize $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
			echo -e "3 1 * * * /opt/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_agent_week $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
			echo -e "#2 0 * * 0 /opt/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
			echo -e "#22 0 * * * /opt/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_VDsales_export $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
			echo -e "#32 0 * * * /opt/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
			echo -e "#42 0 * * * /opt/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`%{__grep} osdial_astgen $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_ivr_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} VMnow $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
			echo -e "* * * * * /usr/sbin/asterisk -rx \"show voicemail users\" > /opt/osdial/html/admin/VMnow.txt" >> $CTB
		fi
		if [ -z "`%{__grep} AST_manager_kill_hung_congested $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_vm_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) updater for voicemail" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_conf_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) updater for conference validator" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_reset_mysql_vars $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
			echo -e "2 1 * * * /opt/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old recordings more than' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
			echo -e "24 0 * * * /usr/bin/find /var/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old recording backups' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old recording backups" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /opt/osdial/backups/recordings -maxdepth 1 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old tts cache' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old tts cache" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /opt/osdial/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old asterisk tts cache' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old asterisk tts cache" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/lib/asterisk/sounds/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_audio_archive $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Send Recordings to archive server" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial-astsnds-ramfs.sh $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`%{__grep} AST_audio_compress $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Compress wav files to mp3" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_audio_compress.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_sort_recordings $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Sort MP3s into campaign_id/date directory structure" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_sort_recordings.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_qc_transfer $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Send select MP3s to a third-party quality-control or offste archive server." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/AST_qc_transfer.pl > /dev/null 2>&1" >> $CTB
		fi

		kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post profile-control
INTY=$1
if [ "$INTY" -eq 2 ]; then
	CTB="/var/spool/cron/asterisk"
	if [ -f "$CTB" ]; then
		if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
			echo -e "29 0 * * * /usr/bin/find /var/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`%{__grep} 'remove old csv exports more than 2 days old' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
			echo -e "24 0 * * * /usr/bin/find /opt/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_external_dnc $CTB`" ]; then
			echo -e "" >> $CTB
			echo "### (sql) Actual Scrub against external DNC" >> $CTB
			echo "* * * * * /opt/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
			echo -e "" >> $CTB
			echo "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
			echo "0 1 * * * /opt/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_CLEAR_auto_calls $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_flush_DBqueue $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
			echo -e "11,41 * * * * /opt/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_cleanup_agent_log $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
			echo -e "33 * * * * /opt/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_VDhopper $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) updater for OSDial hopper" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_adjust_GMTnow_on_leads $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
			echo -e "1 1,7 * * * /opt/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_DB_optimize $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
			echo -e "3 1 * * * /opt/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_agent_week $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
			echo -e "#2 0 * * 0 /opt/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
			echo -e "#22 0 * * * /opt/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_VDsales_export $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
			echo -e "#32 0 * * * /opt/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
			echo -e "#42 0 * * * /opt/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
		fi

		kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post profile-sql
INTY=$1
if [ "$INTY" -eq 2 ]; then
	CTB="/var/spool/cron/asterisk"
	if [ -f "$CTB" ]; then
		if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
			echo -e "29 0 * * * /usr/bin/find /var/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`%{__grep} 'remove old csv exports more than 2 days old' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
			echo -e "24 0 * * * /usr/bin/find /opt/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_external_dnc $CTB`" ]; then
			echo -e "" >> $CTB
			echo "### (sql) Actual Scrub against external DNC" >> $CTB
			echo "* * * * * /opt/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
			echo -e "" >> $CTB
			echo "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
			echo "0 1 * * * /opt/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_CLEAR_auto_calls $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_flush_DBqueue $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
			echo -e "11,41 * * * * /opt/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_cleanup_agent_log $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
			echo -e "33 * * * * /opt/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_VDhopper $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) updater for OSDial hopper" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_adjust_GMTnow_on_leads $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
			echo -e "1 1,7 * * * /opt/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_DB_optimize $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
			echo -e "3 1 * * * /opt/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_agent_week $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
			echo -e "#2 0 * * 0 /opt/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
			echo -e "#22 0 * * * /opt/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_VDsales_export $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
			echo -e "#32 0 * * * /opt/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
			echo -e "#42 0 * * * /opt/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
		fi
		kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post profile-web
INTY=$1
if [ "$INTY" -eq 2 ]; then
	CTB="/var/spool/cron/asterisk"
	if [ -f "$CTB" ]; then
		if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
			echo -e "29 0 * * * /usr/bin/find /var/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
		fi
		kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post profile-dialer
INTY=$1
if [ "$INTY" -eq 2 ]; then
	CTB="/var/spool/cron/asterisk"
	if [ -f "$CTB" ]; then
		if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
			echo -e "29 0 * * * /usr/bin/find /var/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`%{__grep} osdial_astgen $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_ivr_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} VMnow $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
			echo -e "* * * * * /usr/sbin/asterisk -rx \"show voicemail users\" > /opt/osdial/html/admin/VMnow.txt" >> $CTB
		fi
		if [ -z "`%{__grep} AST_manager_kill_hung_congested $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_vm_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) updater for voicemail" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_conf_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) updater for conference validator" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_reset_mysql_vars $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
			echo -e "2 1 * * * /opt/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old recordings more than' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
			echo -e "24 0 * * * /usr/bin/find /var/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old recording backups' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old recording backups" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /opt/osdial/backups/recordings -maxdepth 1 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old tts cache' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old tts cache" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /opt/osdial/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old asterisk tts cache' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old asterisk tts cache" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/lib/asterisk/sounds/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_audio_archive $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Send Recordings to archive server" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial-astsnds-ramfs.sh $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
		fi
		kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post profile-dialer-web
INTY=$1
if [ "$INTY" -eq 2 ]; then
	CTB="/var/spool/cron/asterisk"
	if [ -f "$CTB" ]; then
		if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
			echo -e "29 0 * * * /usr/bin/find /var/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`%{__grep} osdial_astgen $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_ivr_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} VMnow $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
			echo -e "* * * * * /usr/sbin/asterisk -rx \"show voicemail users\" > /opt/osdial/html/admin/VMnow.txt" >> $CTB
		fi
		if [ -z "`%{__grep} AST_manager_kill_hung_congested $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_vm_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) updater for voicemail" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_conf_update $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) updater for conference validator" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_reset_mysql_vars $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
			echo -e "2 1 * * * /opt/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old recordings more than' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
			echo -e "24 0 * * * /usr/bin/find /var/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old recording backups' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old recording backups" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /opt/osdial/backups/recordings -maxdepth 1 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old tts cache' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old tts cache" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /opt/osdial/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} 'remove old asterisk tts cache' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) remove old asterisk tts cache" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/lib/asterisk/sounds/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_audio_archive $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Send Recordings to archive server" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial-astsnds-ramfs.sh $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
		fi
		kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n

%post profile-archive
INTY=$1
if [ "$INTY" -eq 1 ]; then
	/sbin/chkconfig vsftpd on > /dev/null 2>&1
	%{__ln_s} -f /opt/osdial/recordings /var/lib/asterisk/recordings > /dev/null 2>&1
	%{__ln_s} -f /opt/osdial/reports /var/lib/asterisk/reports > /dev/null 2>&1
elif [ "$INTY" -eq 2 ]; then
	CTB="/var/spool/cron/asterisk"
	if [ -f "$CTB" ]; then
		if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
			echo -e "28 0 * * * /usr/bin/find /var/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
			echo -e "29 0 * * * /usr/bin/find /var/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
		fi

		if [ -z "`%{__grep} AST_audio_compress $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Compress wav files to mp3" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_audio_compress.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_sort_recordings $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Sort MP3s into campaign_id/date directory structure" >> $CTB
			echo -e "* * * * * /opt/osdial/bin/AST_sort_recordings.pl > /dev/null 2>&1" >> $CTB
		fi
		if [ -z "`%{__grep} AST_qc_transfer $CTB`" ]; then
			echo -e "" >> $CTB
			echo -e "### (archive) Send select MP3s to a third-party quality-control or offste archive server." >> $CTB
			echo -e "*/15 * * * * /opt/osdial/bin/AST_qc_transfer.pl > /dev/null 2>&1" >> $CTB
		fi
		kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1
	fi
fi
echo -n


%post -n slingdial
[ -d "/var/lib/mysql/osdial" ] && echo "UPDATE system_settings SET admin_template='SlingDial',agent_template='SlingDial';" | /usr/bin/mysql osdial || :
echo -n

%define _opt /opt

%files conflict

%files profile

%files profile-all
%attr(0644,root,root) %{_opt}/osdial/.osdial-all

%if 0%{?blah}
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
%endif

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

%files asterisk-version16

%files asterisk-version12


%files
%dir %attr(0777,asterisk,asterisk) %{_opt}/osdial/tts
%dir %attr(0755,asterisk,asterisk) %{_opt}/osdial/backups
%dir %attr(0755,asterisk,asterisk) %{_opt}/osdial/backups/recordings
%dir %attr(0777,asterisk,asterisk) %{_opt}/osdial/media

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
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/dsp.conf
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
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_extensions_carriers.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_extensions_custom.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_extensions_inbound.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_extensions_outbound.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_extensions_phones.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_extensions_servers.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_extensions_testing.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_iax.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_iax_carriers.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_iax_custom.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_iax_phones.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_iax_registrations.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_iax_servers.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_iax_trunks.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_manager.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_meetme.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_sip.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_carriers.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_servers.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_custom.conf
%attr(0644,asterisk,asterisk) %config %{_sysconfdir}/asterisk/osdial_sip_phones.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_registrations.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/osdial_sip_trunks.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/oss.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/phone.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/sip.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/rtp.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/voicemail.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/chan_dahdi.conf
#%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/dahdi/system.conf
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/README.osdial
%dir %attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/sounds/ivr
%dir %attr(0777,asterisk,asterisk) %{_var}/lib/asterisk/sounds/osdial
%dir %attr(0777,asterisk,asterisk) %{_var}/lib/asterisk/sounds/tts
#%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/*.gsm
#%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/*.g729
#%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/*.sln
#%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/*.sln16
#%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/*.ulaw
#%attr(0644,asterisk,asterisk) %{_var}/lib/asterisk/sounds/*.wav
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDamd.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDamd_post.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/VD_auto_post_VERIFY.agi
#%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-IVR_recording_verification.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDAD_ALL_inbound.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDAD_LB_transfer.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDAD_LO_transfer.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDAD_pin_IVR.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADautoREMINDER.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADautoREMINDERxfer.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDfixCXFER.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADinbound_NI_DNC_CIDlookup.agi
#%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADlisten_DTMF.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADselective_CID.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADselective_CID_hangup.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADtransfer.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADtransferBROADCAST.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADtransferSURVEY.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-VDADtransferTEST.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDoutboundIVR.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDoutbound.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDivr.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDvmail_finder.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDtts.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDdtmf.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-record_prompts.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDstation_spy.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDstation_spy_prompted.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/call_inbound.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/debug_speak.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/invalid_speak.agi
%attr(0755,asterisk,asterisk) %{_var}/lib/asterisk/agi-bin/agi-OSDpark.agi
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/ari
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/ari/bin
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/ari/includes
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/ari/misc
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/ari/modules
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/ari/theme
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/ari/theme/images
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/*.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/*.log
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/*.txt
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/bin/aribins.tgz
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/includes/*.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/misc/*.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/modules/*.module
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/theme/*.css
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/theme/*.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/theme/*.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/theme/images/*.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/ari/theme/images/*.png

%files common
%defattr(644,asterisk,asterisk,755)
%dir %attr(0755,asterisk,asterisk) %{_var}/log/osdial
%attr(0644,root,root) %config(noreplace) %{_sysconfdir}/security/limits.d/99-osdial.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/httpd/conf.d/osdial-archive.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/httpd/conf.d/osdial-ari.conf
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/httpd/conf.d/osdial-psi.conf
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo
%attr(0664,apache,asterisk) %config %{_opt}/osdial/html/phpsysinfo/config.php
%attr(0664,apache,asterisk) %config %{_opt}/osdial/html/phpsysinfo/config.php.new
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/js
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/js/jQuery
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/js/phpSysInfo
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/data
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/gfx
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/gfx/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/gfx/treeTable
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/templates
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/templates/nextgen
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/templates/plugin
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/templates/cream
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/templates/aqua
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/templates/two
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/templates/html
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/Quotas
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/Quotas/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/Quotas/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/Quotas/css
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/Quotas/gfx
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/MDStatus
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/MDStatus/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/MDStatus/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/MDStatus/css
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/MDStatus/gfx
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/ipmi
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/ipmi/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/ipmi/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SNMPPInfo
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SNMPPInfo/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SNMPPInfo/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SNMPPInfo/css
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SNMPPInfo/gfx
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PS
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PS/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PS/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PS/css
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PS/gfx
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/UpdateNotifier
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/UpdateNotifier/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/UpdateNotifier/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/UpdateNotifier/css
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/UpdateNotifier/gfx
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/BAT
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/BAT/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/BAT/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/BAT/css
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/BAT/gfx
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PSStatus
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PSStatus/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PSStatus/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PSStatus/css
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/PSStatus/gfx
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SMART
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SMART/lang
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SMART/js
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SMART/css
#%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins/SMART/gfx
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/plugin
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/ups
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/to
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/to/device
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/interface
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/os
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/js
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/output
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/xml
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/error
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/includes/mb
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/language
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/ChangeLog
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/COPYING
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/index.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/js.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/*.xsd
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/*.xslt
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/README
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/README_PLUGIN
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/xml.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/data/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/gfx/*.gif
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/gfx/*.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/gfx/images/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/gfx/treeTable/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/*.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/error/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/interface/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/js/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/mb/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/os/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/output/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/plugin/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/to/*.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/to/device/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/ups/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/includes/xml/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/js/jQuery/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/js/phpSysInfo/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/language/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/plugins/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/phpsysinfo/templates/*
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/osdial.conf
%attr(0755,root,root) %{_sysconfdir}/profile.d/osdial.sh
%attr(0755,root,root) %{_sysconfdir}/init.d/osdial
%attr(0755,root,root) %{_sysconfdir}/init.d/osdial_resource_send
%attr(0644,asterisk,asterisk) %{_opt}/osdial/bin/sql/*.sql
%attr(0644,asterisk,asterisk) %{_opt}/osdial/bin/sql/upgrade_sql.map
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/sql/upgrade_sql.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_resource_send
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_resource_listen
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_CLEAR_auto_calls.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_audio_archive.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_audio_compress.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_qc_transfer.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_sort_recordings.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/AST_VDcampaign_stats.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/OSDcampaign_stats.pl
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
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_ast_hangup_all.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_ivr_sync.sh
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_killall.sh
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial-astsnds-ramfs.sh
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_tts_generate.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/osdial_media_sync.pl
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/OSDconfig
%attr(0755,asterisk,asterisk) %{_bindir}/OSDconfig
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
#%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/ip_relay
%attr(0755,asterisk,asterisk) %{_opt}/osdial/bin/safe_ip_relay
%attr(0755,asterisk,asterisk) %{_sysconfdir}/cron.daily/AST_ntp_update.sh
%attr(0640,root,root) %{_sysconfdir}/openvpn/osdial-ca.crt
%attr(0640,root,root) %config(noreplace) %{_sysconfdir}/openvpn/osdial.up
%attr(0640,root,root) %{_sysconfdir}/openvpn/osdial-ta.key
%attr(0640,root,root) %config(noreplace) %{_sysconfdir}/openvpn/osdial.conf
%attr(0640,root,root) %config(noreplace) %{_sysconfdir}/openvpn/osdial2.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/ALTERNATE_NUMBER_DIALING.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/BALANCE_FILL_PROCESS.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/CALLBACKS_PROCESS.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/IE_INCOMPATIBILITIES.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/INBOUND-CLOSER_PROCESS.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/LOAD_BALANCING.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/OUTBOUND_IVR.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/trixbox.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/zhone_10b_24port.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/copyright
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/firewall.sh
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/osdial-template-example.tgz
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/httpd-osdial.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/httpd-osdial-archive.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/httpd-osdial-ari.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/httpd-osdial-psi.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/my.cnf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/php.ini
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/osdial.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/osdial.cron
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/AUTHORS
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/ChangeLog
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/COPYING
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/NEWS
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/README
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/TODO
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/api/api-admin-add_lead.xml
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/api/api.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/api/api-version.xml
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/amd.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/cdr.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/dnsmgr.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/dsp.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/extensions.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/extconfig.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/res_mysql.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/iax.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/iaxprov.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/indications.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/logger.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/manager.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/meetme.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/modules.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/musiconhold.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions_carriers.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions_conferences.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions_custom.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions_inbound.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions_outbound.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions_phones.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions_servers.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_extensions_testing.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_iax.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_iax_carriers.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_iax_custom.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_iax_phones.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_iax_registrations.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_iax_servers.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_iax_trunks.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_manager.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_meetme.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_sip.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_sip_carriers.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_sip_custom.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_sip_phones.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_sip_registrations.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_sip_servers.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/osdial_sip_trunks.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/oss.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/phone.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/sip.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/rtp.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/voicemail.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/chan_dahdi.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/dahdi_system.conf
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/README.osdial
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/LICENSE.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/vd205conv.txt
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/extras

%files nonfree
%defattr(644,asterisk,asterisk,755)
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/company.php

%files nonfree-emailtemplates
%defattr(644,asterisk,asterisk,755)
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/scripts/email_templates.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/email_blacklist.php

%files web-template-largedialpresets
%defattr(644,asterisk,asterisk,755)
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/LargeDialPresets
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/LargeDialPresets/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/LargeDialPresets
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/LargeDialPresets/images
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/LargeDialPresets/*.ttf
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/LargeDialPresets/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/LargeDialPresets/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/LargeDialPresets/images/*
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/LargeDialPresets/*.ttf
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/LargeDialPresets/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/LargeDialPresets/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/LargeDialPresets/images/*

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

%files -n perl-OSDial
%defattr(644,root,root,755)
%{perl_vendorlib}/OSDial*
%doc %{_mandir}/man3/OSDial.3pm.gz

%files -n slingdial
%defattr(644,asterisk,asterisk,755)
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/SlingDial
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/agent/templates/SlingDial/images
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/SlingDial
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/admin/templates/SlingDial/images
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/SlingDial/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/SlingDial/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/SlingDial/*.ttf
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/SlingDial/images/*
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/SlingDial/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/SlingDial/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/SlingDial/*.ttf
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/SlingDial/images/*

%files web
%defattr(644,asterisk,asterisk,755)
%attr(0644,asterisk,asterisk) %{_sysconfdir}/httpd/conf.d/osdial.conf
%attr(0755,root,root) %{_sysconfdir}/init.d/osdial_resource_listen
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/robots.txt
%attr(0664,apache,asterisk) %{_opt}/osdial/html/favicon.ico
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/active_list_refresh.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/astguiclient.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/call_log_display.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/conf_exten_check.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/dbconnect.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/inbound_popup.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/live_exten_check.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/manager_send.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/park_calls_display.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/vdc_db_query.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/osdial.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/print_email_template.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/voicemail_check.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/webform_redirect.php
%attr(0644,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/webform_test.php
%attr(0644,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/webform-event_members.php
%attr(0644,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/agent/webform-print_form.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/default/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/default/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/default/*.ttf
%attr(0644,apache,asterisk) %{_opt}/osdial/html/agent/templates/default/images/*
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/default/*.php
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/default/*.css
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/default/*.ttf
%attr(0644,apache,asterisk) %{_opt}/osdial/html/admin/templates/default/images/*
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/api.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/copyright.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/tocsv.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_CLOSERstats.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_VDADstats.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_OSDIAL_hopperlist.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_admin_log_display.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_disposition.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_performance.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_performance_detail.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_time_sheet.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_agent_time_sheet_archive.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_inboundEXTstats.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_inboundEXTstats_department.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_parkstats.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_server_performance.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDAD.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADall.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADallREC.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDADallSUMMARY.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonpark.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/admin.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/admin_modify_lead.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/admin_search_lead.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer-fronter_popup.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer-fronter_popup2.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer_dispo.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/closer_popup.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/dbconnect.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/group_hourly_stats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/getmedia.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/help.gif
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader.pl
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/listloaderMAIN.php
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader_rowdisplay.pl
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader_super.pl
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/log_test.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/new_listloader_superL.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/phone_stats.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/record_conf_1_hour.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/remote_dispo.php
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/spreadsheet_sales_viewer.pl
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/user_stats.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/user_status.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/vdremote.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/osdial_sales_viewer.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/voice_lab.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/vtiger_search.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/welcome.php
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/admin/VMnow.txt
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/admin.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/EditableSelect.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/CalendarPopup.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/auth.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/carriers.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/carriers.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/conference.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/media.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/phones.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/server.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/settings.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/status.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/times.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/tts.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/tts-sounds-list.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/autoalt.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/campaigns.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/cid_areacode.php
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
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/advanced_search.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/basic_search.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/export.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/list_loader.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/lists.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/dnc.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/lists/modify_lead.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/agent_pause_summary.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/agent_performance_detail.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/agent_stats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/agent_status.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/agent_timesheet.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/call_stats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/closer_stats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/custom.php-example
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/hopperlist.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/lead_performance_campaign.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/lead_performance_list.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/lead_search_advanced.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/lead_search_basic.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/list_cost.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/phone_stats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/realtime_detail.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/realtime_summary.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/reports.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/list_performance.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/server_performance.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/usergroup_hourly.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/web_admin_log.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/remoteagent/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/remoteagent/remoteagent.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/scripts/email_templates.php
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/include/osdial-dynamic.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/include/osdial-global.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/include/osdial-global-dynamic.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/include/osdial-login.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/include/osdial-static.js
%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/index.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/test_OSDIAL_output.php
#%attr(0664,apache,asterisk) %{_opt}/osdial/html/agent/test_callerid_output.php

#%files debuginfo
#%defattr(644,root,root,755)
#/usr/lib/debug/opt/osdial/bin/ip_relay.debug

%changelog
* Tue Dec 22 2009 Lott Caskey <lottc@fugitol.com> 2.2.0.038-182
- Brand 2.2.0

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
