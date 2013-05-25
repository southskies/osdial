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
%define krelver %(echo %{kversion2} | tr -s '-' '_')

%define perl_vendorlib %(eval "`%{__perl} -V:installvendorlib`"; echo $installvendorlib)
%define perl_vendorarch %(eval "`%{__perl} -V:installvendorarch`"; echo $installvendorarch)

%define _opt /opt

%define version %(if [ -f osdial.version ]; then %{__cat} osdial.version; else %{__cat} /builddir/build/SOURCES/osdial.version; fi)
%define release %(if [ -f osdial.release ]; then %{__cat} osdial.release; else %{__cat} /builddir/build/SOURCES/osdial.release; fi)
%define buildver %(if [ -f osdial.build ]; then %{__cat} osdial.build; else %{__cat} /builddir/build/SOURCES/osdial.build; fi)

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


Summary:        The OSDial predictive dialing suite.
Name:           osdial
Version:        %{version}
Release:        %{release}%{?dist}
License:        GPL
Group:          Applications/Telephony
Source0:        osdial-%{version}.tgz
Source1:        osdial-template-highcontrast.tgz
Source2:        osdial-template-slingdial.tgz
Source3:        osdial-template-largedialpresets.tgz
Source4:        osdial.version
Source5:        osdial.release
Source6:        osdial.build
URL:            http://www.callcentersg.com
Packager:       lottc@fugitol.com
Vendor:         Call Center Service Group
Requires(pre):  openssh coreutils e2fsprogs grep
Requires:       openvpn
Requires:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
BuildRequires:  /bin/cat
BuildArch:      noarch
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}

%description
OSDial is a predictive dialing system, an off-shoot of VICIdial,
currently being developed by Lott Caskey and Steve Szmidt.


%package conflict
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Conflicts:      osdial
Conflicts:      osdial-profile
Conflicts:      osdial-install
Conflicts:      osdial-common
Conflicts:      osdial-dialer
Conflicts:      osdial-web
Conflicts:      osdial-sql
BuildArch:      noarch

%description conflict
Meta package which will conflict with OSDial base packages.




%package profile
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Requires(post): coreutils grep gawk procps
Requires:       osdial-profile-all = %{version}-%{release}
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-dialer = %{version}-%{release}
Requires:       osdial-sql = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
Obsoletes:      osdial-profile-live
Obsoletes:      osdial-installcd
BuildArch:      noarch

%description profile
OSDial - Single / All-in-One Server Profile.
    osdial-common
    osdial-dialer
    osdial-sql
    osdial-web


%package profile-all
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Requires(post): coreutils grep gawk procps
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-dialer = %{version}-%{release}
Requires:       osdial-sql = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
Obsoletes:      osdial-profile-install-all
Obsoletes:      osdial-installcd
BuildArch:      noarch

%description profile-all
OSDial - Single / All-in-One Server Profile.
    osdial-common
    osdial-dialer
    osdial-sql
    osdial-web



%package profile-live
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Obsoletes:      osdial-livecd
Requires(post): coreutils grep gawk procps
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-dialer = %{version}-%{release}
Requires:       osdial-sql = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
BuildArch:      noarch

%description profile-live
Package for creating a live disk.





%if 0%{?blah}

%package profile-install-all
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Obsoletes:      osdial-livecd
Requires(post): coreutils grep
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-dialer = %{version}-%{release}
Requires:       osdial-sql = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
BuildArch:      noarch

%description profile-install-all
Package for creating an install disk.

%package profile-install-control
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Requires(post): coreutils grep
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-sql = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
BuildArch:      noarch

%description profile-install-control
Package for creating an install disk.

%package profile-install-dialer
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Requires(post): coreutils grep
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-dialer = %{version}-%{release}
BuildArch:      noarch

%description profile-install-dialer
Package for creating an install disk.

%package profile-install-sql
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Requires(post): coreutils grep
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-sql = %{version}-%{release}
BuildArch:      noarch

%description profile-install-sql
Package for creating an install disk.

%package profile-install-web
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Requires(post): coreutils grep
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
BuildArch:      noarch

%description profile-install-web
Package for creating an install disk.

%package profile-install-archive
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Requires(post): coreutils grep
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
BuildArch:      noarch

%description profile-install-archive
Package for creating an install disk.

%endif






%package profile-control
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Conflicts:      osdial-dialer
Requires(post): coreutils grep gawk procps
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
Requires:       osdial-sql = %{version}-%{release}
Obsoletes:      osdial-profile-install-control
BuildArch:      noarch

%description profile-control
OSDial - Provides packages needed for multi-server
    configuration.  Only installs web and SQL
    components.
        osdial-common
        osdial-sql
        osdial-web

%package profile-dialer
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Conflicts:      osdial-web
Conflicts:      osdial-sql
Requires(post): coreutils grep gawk procps
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-dialer = %{version}-%{release}
Obsoletes:      osdial-profile-install-dialer
BuildArch:      noarch

%description profile-dialer
OSDial - Provides packages needed for multi-server
    configuration.  Only installs dialer
    components.
        osdial-common
        osdial-dialer

%package profile-dialer-web
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Conflicts:      osdial-sql
Requires(post): coreutils grep gawk procps
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-dialer = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
Obsoletes:      osdial-profile-install-dialer-web
BuildArch:      noarch

%description profile-dialer-web
OSDial - Provides packages needed for multi-server
    configuration.  Only installs dialer
    components.
        osdial-common
        osdial-dialer
        osdial-web

%package profile-sql
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Conflicts:      osdial-web
Conflicts:      osdial-dialer
Requires(post): coreutils grep gawk procps
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-sql = %{version}-%{release}
Obsoletes:      osdial-profile-install-sql
BuildArch:      noarch

%description profile-sql
OSDial - Provides packages needed for multi-server
    configuration.  Only installs SQL
    components.
        osdial-common
        osdial-sql

%package profile-web
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Conflicts:      osdial-dialer
Conflicts:      osdial-sql
Requires(post): coreutils grep gawk procps
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       osdial-web = %{version}-%{release}
Obsoletes:      osdial-profile-install-web
BuildArch:      noarch

%description profile-web
OSDial - Provides packages needed for multi-server
    configuration.  Only installs Web
    components.
        osdial-common
        osdial-web

%package profile-archive
Summary:        The OSDial predictive dialing suite.
Group:          Applications/Telephony
Provides:       osdial-profile = %{version}-%{release}
Conflicts:      astguiclient
Conflicts:      vicidial
BuildRequires:  dialog
Conflicts:      osdial-dialer
Conflicts:      osdial-sql
Conflicts:      osdial-web
Requires(post): coreutils grep gawk procps vsftpd
Requires:       vsftpd
Requires:       httpd
Requires:       osdial = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Obsoletes:      osdial-profile-install-archive
BuildArch:      noarch

%description profile-archive
OSDial - Provides packages needed for multi-server
    configuration.  Only installs archive
    components.
        osdial-common




%package common
Summary:        OSDial backend scripts
Group:          Applications/Telephony
Obsoletes:      osdial-bin
Requires(post): coreutils grep gawk lsof ip_relay perl
Requires:       osdial = %{version}-%{release}
Requires:       osdial-profile = %{version}-%{release}
Requires:       perl-OSDial = %{version}-%{release}
Requires:       tuned
Requires:       openvpn
Requires:       sysstat
Requires:       httpd
Requires:       php-mbstring
Requires:       php-xml
Requires:       perl-MD5
Requires:       perl-Digest-SHA1
Requires:       perl-DBI
Requires:       perl-DBD-MySQL
Requires:       perl-Time-modules
Requires:       perl-Time-HiRes
Requires:       perl-Unicode-Map
Requires:       perl-Jcode
Requires:       perl-OLE-Storage_Lite
Requires:       perl-Proc-ProcessTable
Requires:       perl-IO-Interface
Requires:       perl-IO-stringy
Requires:       perl-IO-Socket-Multicast
Requires:       perl-Spreadsheet-ParseExcel
Requires:       perl-Spreadsheet-WriteExcel
Requires:       perl-Net-Telnet
Requires:       perl-Net-Server
Requires:       perl-Net-IP
Requires:       perl-Net-Address-IP-Local
Requires:       perl-Net-Netmask
Requires:       perl-Data-Validate-IP
Requires:       perl-Number-Format
Requires:       perl-version
Requires:       perl-Parse-RecDescent
Requires:       perl-Proc-Exists
Requires:       perl-TermReadKey
Requires:       readline
Requires:       sox
Requires:       lame
Requires:       toolame
Requires:       screen
Requires:       ntp
Requires:       iftop
Requires:       ploticus
Requires:       balance
%if 0%{?rhel} < 6
Requires:       subversion
%endif
Requires:       mtop
Requires:       perl-Curses
Requires:       perl-Asterisk
Requires:       htop
Requires:       sipsak
Requires:       ttyload
Requires:       sqlite2
Requires:       dialog
Requires:       ip_relay
Requires:       system-switch-asterisk
Requires:       festival
Requires:       festival-lib
Requires:       festival-speechtools-libs
Requires:       festvox-awb-arctic-hts
Requires:       festvox-bdl-arctic-hts
Requires:       festvox-clb-arctic-hts
Requires:       festvox-jmk-arctic-hts
Requires:       festvox-kal-diphone
Requires:       festvox-ked-diphone
Requires:       festvox-rms-arctic-hts
Requires:       festvox-slt-arctic-hts
Requires:       hispavoces-pal-diphone
Requires:       hispavoces-sfl-diphone
Requires:       libcgroup
Requires:       numad
Requires:       plymouth-theme-osdial
BuildArch:      noarch

%description common
OSDial backend scripts, needed by web, sql, etc.

%package sql
Summary:        OSDial SQL files and update scripts.
Group:          Applications/Telephony
Requires(post): coreutils grep sed mysql-server perl gawk procps
Requires:       osdial = %{version}-%{release}
Requires:       osdial-profile = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       perl-DBI
Requires:       perl-DBD-MySQL
Requires:       mysql-server >= %{mysql_version}
BuildArch:      noarch

%description sql
OSDial SQL file and update scripts.  Provides a method of
automatically updating the OSDial database, both through the
install package and RPM.

%package web
Summary:        OSDial user interface files
Group:          Applications/Telephony
Requires(post): coreutils grep httpd perl gawk procps php php-common
Requires:       osdial = %{version}-%{release}
Requires:       osdial-profile = %{version}-%{release}
Requires:       osdial-common = %{version}-%{release}
Requires:       php-pear
Requires:       php-pear-Date
Requires:       php-pear-Mail
Requires:       php-pear-Mail-Mime
Requires:       php-mysql
Requires:       ploticus
Requires:       httpd
Requires:       tinymce
BuildArch:      noarch

%description web
OSDial user interface files.  Mainly the php scripts, directory
structure and other supporting files.



%package dialer
Summary:        OSDial Asterisk System and Configuration
Group:          Applications/Telephony
Requires(post): coreutils grep sed gawk perl
Requires(pre):  osdial = %{version}-%{release}
Requires(pre):  osdial-profile = %{version}-%{release}
Requires(pre):  osdial-common = %{version}-%{release}
Requires:       osdial-asterisk-version
Requires:       osdial-sounds
Requires:       php-pear-db
Requires:       gawk
Obsoletes:      osdial-config
BuildArch:      noarch

%description dialer
The is a generic Asterisk configuration that should work out of box for most clients.



%package asterisk-version12
Summary:        OSDial Asterisk 1.2 System
Group:          Applications/Telephony
Requires(post): coreutils grep sed gawk perl
Requires(pre):  osdial = %{version}-%{release}
Requires(pre):  osdial-profile = %{version}-%{release}
Requires(pre):  osdial-common = %{version}-%{release}
Requires(pre):  osdial-dialer = %{version}-%{release}
Requires:       asterisk12-system
Requires:       libpri12 >= %{libpri12_version}
Requires:       zaptel12 >= %{zaptel12_version}
Requires:       wanpipe12 >= %{wanpipe_version}
Requires:       asterisk12 >= %{asterisk12_version}
Requires:       asterisk12-addons >= %{asterisk12_version}
Requires:       asterisk12-sounds >= %{asterisk12_version}
Requires:       gawk
Provides:       osdial-asterisk-version
Provides:       osdial-asterisk12 = %{version}-%{release}
Obsoletes:      osdial-asterisk
Conflicts:      osdial-asterisk-version16
BuildArch:      noarch

%description asterisk-version12
This package contains dependency and setup instructions for Asterisk 1.2.


%package asterisk-version16
Summary:        OSDial Asterisk 1.6 System
Group:          Applications/Telephony
Requires(post): coreutils grep sed gawk perl
Requires(pre):  osdial = %{version}-%{release}
Requires(pre):  osdial-profile = %{version}-%{release}
Requires(pre):  osdial-common = %{version}-%{release}
Requires(pre):  osdial-dialer = %{version}-%{release}
Requires:       asterisk16-system
Requires:       libpri14 >= %{libpri14_version}
Requires:       dahdi >= %{dahdi_version}
Requires:       dahdi-tools >= %{dahdi_tools_version}
Requires:       wanpipe16 >= %{wanpipe_version}
Requires:       asterisk16 >= %{asterisk16_version}
Requires:       asterisk16-addons >= %{asterisk16_version}
Requires:       asterisk16-sounds-en-gsm >= %{asterisk16_version}
Requires:       mysql-server >= %{mysql_version}
Requires:       gawk
Provides:       osdial-asterisk-version
Provides:       osdial-asterisk16 = %{version}-%{release}
Conflicts:      osdial-asterisk-version12
BuildArch:      noarch

%description asterisk-version16
This package contains dependency and setup instructions for Asterisk 1.6.




#%package debuginfo
#Summary:        OSDial debuginfo
#Group:          Applications/Telephony
#BuildArch:      i386
#
#%description debuginfo
#OSDial debuginfo

%package web-template-largedialpresets
Summary:        OSDial user interface files
Group:          Applications/Telephony
Requires:       osdial-web = %{version}-%{release}
Provides:       osdial-template-largedialpresets = %{version}-%{release}
BuildArch:      noarch

%description web-template-largedialpresets
Large Dial Presets Template


%package web-template-highcontrast
Summary:        OSDial user interface files
Group:          Applications/Telephony
Requires:       osdial-web = %{version}-%{release}
Provides:       osdial-template-highcontrast = %{version}-%{release}
BuildArch:      noarch

%description web-template-highcontrast
High Contrast Template

%package nonfree
Summary:        OSDial Non-Free
Group:          Applications/Telephony
Requires:       osdial-web = %{version}-%{release}
Provides:       osdial-nonfree-companies = %{version}-%{release}
BuildArch:      noarch

%description nonfree
OSDial Non-Free

%package nonfree-emailtemplates
Summary:        OSDial Non-Free
Group:          Applications/Telephony
Requires:       osdial-web = %{version}-%{release}
BuildArch:      noarch

%description nonfree-emailtemplates
OSDial Non-Free

%package -n perl-OSDial
Summary:        OSDial user interface files
Group:          Applications/Telephony
BuildRequires:  perl(ExtUtils::MakeMaker), perl
Requires:       perl
BuildArch:      noarch


%description -n perl-OSDial
This module is inteded to provided quick and easy access to common functions
in OSDial.  The module will read existing configuration files, connect to
the OSDial database, and interface with Asterisk as needed.

%package -n slingdial
Summary:        OSDial user interface files
Group:          Applications/Telephony
Requires:       osdial-web = %{version}-%{release}
Provides:       osdial-web-template-slingdial = %{version}-%{release}
Provides:       osdial-template-slingdial = %{version}-%{release}
BuildArch:      noarch

%description -n slingdial
template



%prep
%setup -q -a 0 -n osdial-%{version}
%setup -q -a 1 -D
%setup -q -a 2 -D
%setup -q -a 3 -D

%build

%install
%{__mkdir_p} %{buildroot}%{_usr}/lib/debug
%{__mkdir_p} %{buildroot}%{_usrsrc}/debug
%{__make} DESTDIR=%{buildroot} HTTPDUSER=asterisk install
%{__rm} -f %{buildroot}%{_sharedstatedir}/asterisk/sounds/*.ulaw
%{__rm} -f %{buildroot}%{_sharedstatedir}/asterisk/sounds/*.gsm
%{__rm} -f %{buildroot}%{_sharedstatedir}/asterisk/sounds/*.g729
cd perl
%{__perl} Makefile.PL PREFIX="%{buildroot}%{_prefix}" INSTALLDIRS="vendor"
%{__make} %{?_smp_mflags}
%{__make} install
find %{buildroot} -name .packlist -exec %{__rm} {} \;
find %{buildroot} -name perllocal.pod -exec %{__rm} {} \;
cd ..
%{__mkdir_p} %{buildroot}%{_sysconfdir}/httpd/conf.d
%{__mkdir_p} %{buildroot}%{_sysconfdir}/init.d
%{__mkdir_p} %{buildroot}%{_sysconfdir}/profile.d
%{__mkdir_p} %{buildroot}%{_sysconfdir}/pki/osdial-support
%{__mkdir_p} %{buildroot}%{_opt}/osdial/html/ivr
%{__mkdir_p} %{buildroot}%{_opt}/osdial/backups/recordings
%{__mkdir_p} %{buildroot}%{_opt}/osdial/recordings/processing/unmixed
%{__mkdir_p} %{buildroot}%{_opt}/osdial/recordings/processing/mixed
%{__mkdir_p} %{buildroot}%{_opt}/osdial/recordings/completed
%{__mkdir_p} %{buildroot}%{_opt}/osdial/recordings
%{__mkdir_p} %{buildroot}%{_opt}/osdial/reports
%{__mkdir_p} %{buildroot}%{_opt}/osdial/tts
%{__mkdir_p} %{buildroot}%{_opt}/osdial/backups
%{__mkdir_p} %{buildroot}%{_opt}/osdial/backups/recordings
%{__mkdir_p} %{buildroot}%{_opt}/osdial/media
%{__mkdir_p} %{buildroot}%{_localstatedir}/log/osdial
%{__mkdir_p} %{buildroot}%{_sharedstatedir}/asterisk/sounds/tts
%{__mkdir_p} %{buildroot}%{_sharedstatedir}/asterisk/sounds/ivr
%{__mkdir_p} %{buildroot}%{_sharedstatedir}/asterisk/sounds/osdial
%{__cp} extras/bash.profile %{buildroot}%{_sysconfdir}/profile.d/osdial.sh
%{__cp} extras/httpd-osdial.conf %{buildroot}%{_sysconfdir}/httpd/conf.d/osdial.conf
%{__cp} extras/httpd-osdial-archive.conf %{buildroot}%{_sysconfdir}/httpd/conf.d/osdial-archive.conf
%{__cp} extras/httpd-osdial-ari.conf %{buildroot}%{_sysconfdir}/httpd/conf.d/osdial-ari.conf
%{__cp} extras/httpd-osdial-psi.conf %{buildroot}%{_sysconfdir}/httpd/conf.d/osdial-psi.conf
%{__cp} extras/osdial.init %{buildroot}%{_sysconfdir}/init.d/osdial
%{__cp} extras/osdial_resource_send.init %{buildroot}%{_sysconfdir}/init.d/osdial_resource_send
%{__cp} extras/osdial_resource_listen.init %{buildroot}%{_sysconfdir}/init.d/osdial_resource_listen
%{__cp} extras/osdial-support.pub %{buildroot}%{_sysconfdir}/pki/osdial-support
%{__mkdir_p} %{buildroot}/root/.mc
%{__cp} extras/mc.ini %{buildroot}/root/.mc/ini
%{__cp} extras/mc.panels.ini %{buildroot}/root/.mc/panels.ini
%{__mkdir_p} %{buildroot}%{_sysconfdir}/cron.daily
%{__ln_s} %{_opt}/osdial/bin/AST_ntp_update.sh %{buildroot}%{_sysconfdir}/cron.daily
touch %{buildroot}%{_opt}/osdial/html/admin/VMnow.txt

%{__mv} %{buildroot}%{_opt}/osdial/bin/osdial_resource_send.pl %{buildroot}%{_opt}/osdial/bin/osdial_resource_send
%{__mv} %{buildroot}%{_opt}/osdial/bin/osdial_resource_listen.pl %{buildroot}%{_opt}/osdial/bin/osdial_resource_listen

# copy in asterisk configs
%{__mkdir_p} %{buildroot}%{_sysconfdir}/asterisk/startup.d
%{__mkdir_p} %{buildroot}%{_sysconfdir}/dahdi
echo -e "#!/bin/bash\nexport TTY=screen" > %{buildroot}%{_sysconfdir}/asterisk/startup.d/tty_screen.sh
%{__cp} docs/conf_examples/*.conf %{buildroot}%{_sysconfdir}/asterisk
%{__cp} docs/conf_examples/README.osdial %{buildroot}%{_sysconfdir}/asterisk

%{__perl} -pi -e 's|^VARserver_ip.*|VARserver_ip => 127.0.0.1|' %{buildroot}%{_sysconfdir}/osdial.conf
%{__perl} -pi -e 's|^VARHTTP_path.*|VARHTTP_path => http://127.0.0.1|' %{buildroot}%{_sysconfdir}/osdial.conf
%{__perl} -pi -e 's|^VARFTP_host.*|VARFTP_host => 127.0.0.1|' %{buildroot}%{_sysconfdir}/osdial.conf
%{__perl} -pi -e 's|^VARREPORT_host.*|VARREPORT_host => 127.0.0.1|' %{buildroot}%{_sysconfdir}/osdial.conf
%{__rm} -f %{buildroot}%{_sysconfdir}/asterisk/dahdi_system.conf

echo > %{buildroot}%{_opt}/osdial/.osdial-all
%if 0%{?blah}
echo > %{buildroot}%{_opt}/osdial/.osdial-install-all
echo > %{buildroot}%{_opt}/osdial/.osdial-install-control
echo > %{buildroot}%{_opt}/osdial/.osdial-install-dialer
echo > %{buildroot}%{_opt}/osdial/.osdial-install-sql
echo > %{buildroot}%{_opt}/osdial/.osdial-install-web
echo > %{buildroot}%{_opt}/osdial/.osdial-install-archive
%endif
echo > %{buildroot}%{_opt}/osdial/.osdial-live
echo > %{buildroot}%{_opt}/osdial/.osdial-control
echo > %{buildroot}%{_opt}/osdial/.osdial-dialer
echo > %{buildroot}%{_opt}/osdial/.osdial-dialer-web
echo > %{buildroot}%{_opt}/osdial/.osdial-sql
echo > %{buildroot}%{_opt}/osdial/.osdial-web
echo > %{buildroot}%{_opt}/osdial/.osdial-archive

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
echo "*                  -       core               unlimited" > %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       data               unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       fsize              unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       sigpending         unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       memlock            unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       as                 unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       nofile             999999" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       msgqueue           unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       cpu                unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       nproc              unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
echo "*                  -       locks              unlimited" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
if [ "`uname -m`" = "x86_64" ]; then
    echo "*                  -       stack              8192" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
else
    echo "*                  -       stack              2048" >> %{buildroot}%{_sysconfdir}/security/limits.d/99-osdial.conf
fi


%clean
%{__rm} -rf %{buildroot}

%pre
ASTUSER=`%{__id} asterisk 2>/dev/null`
RETVAL=$?
if [ "$RETVAL" -eq 1 ]; then
    %{_sbindir}/useradd -c "Asterisk PBX" -G tty -s /bin/bash -r -d "%{_sharedstatedir}/asterisk" asterisk > /dev/null 2>&1
fi
if [ ! -d "%{_sharedstatedir}/asterisk" ]; then
    %{__mkdir_p} %{_sharedstatedir}/asterisk
    %{__chown} asterisk:asterisk %{_sharedstatedir}/asterisk
fi
UUIDPW=`%{_bindir}/uuidgen`
if [ -f "%{_sharedstatedir}/asterisk/.asterisk_pw" ]; then
    UUIDPW=`%{__cat} %{_sharedstatedir}/asterisk/.asterisk_pw`
fi
if [ ! -f "%{_opt}/osdial/.osdial-archive" ]; then
    echo "$UUIDPW" | %{_bindir}/passwd --stdin asterisk > /dev/null 2>&1
fi
echo "$UUIDPW" > %{_sharedstatedir}/asterisk/.asterisk_pw
%{__chown} root:root %{_sharedstatedir}/asterisk/.asterisk_pw
%{__chmod} 600 %{_sharedstatedir}/asterisk/.asterisk_pw
if [ ! -f "/root/.ssh/id_rsa" ]; then
    %{_bindir}/ssh-keygen -t rsa -f /root/.ssh/id_rsa -q -N '' 2>/dev/null
fi
RKEY=`%{__cat} /root/.ssh/id_rsa.pub 2>/dev/null`
if [ ! -d "%{_sharedstatedir}/asterisk/.ssh" ]; then
    %{__mkdir_p} %{_sharedstatedir}/asterisk/.ssh
fi
if [ ! -f "%{_sharedstatedir}/asterisk/.ssh/authorized_keys" ]; then
    echo "$RKEY" > %{_sharedstatedir}/asterisk/.ssh/authorized_keys
fi
KCHK=`%{__grep} "$RKEY" %{_sharedstatedir}/asterisk/.ssh/authorized_keys`
if [ -z "$KCHK" ]; then
    echo "$RKEY" > %{_sharedstatedir}/asterisk/.ssh/authorized_keys
fi
%{__chown_Rhf} -R asterisk:asterisk %{_sharedstatedir}/asterisk/.ssh
%{__chmod} 700 %{_sharedstatedir}/asterisk/.ssh
%{__chmod} 600 %{_sharedstatedir}/asterisk/.ssh/*
exit 0

%post profile-live
INTY=$1
if [ "$INTY" -eq 1 ]; then
    echo > %{_opt}/osdial/.osdial-live
    echo "RUN_FIRSTBOOT=\"NO\"" > %{_sysconfdir}/sysconfig/firstboot
    echo "NETWORKING=yes" > %{_sysconfdir}/sysconfig/network
    echo "NETWORKING_IPV6=no" >> %{_sysconfdir}/sysconfig/network
    echo "HOSTNAME=osdial-live.callcentersg.com" >> %{_sysconfdir}/sysconfig/network
    echo "127.0.0.1 osdial-live.callcentersg.com osdial-live" > %{_sysconfdir}/hosts
    echo "127.0.0.1 localhost.localdomain localhost" >> %{_sysconfdir}/hosts
    echo "::1       localhost6.localdomain6 localhost6" >> %{_sysconfdir}/hosts
    if [ ! -d "%{_usr}/lib/syslinux" ]; then
        if [ -d "%{_usr}/share/syslinux" ]; then
            echo "    osdial-live: Fixing broken syslinux"
            %{__ln_s} %{_usr}/share/syslinux %{_usr}/lib/syslinux > /dev/null 2>&1 || :
        fi
    fi
    /sbin/service syslog stop > /dev/null 2>&1 || :
fi
echo -n


%if 0%{?blah}
%post profile-install-all
INTY=$1
if [ "$INTY" -eq 1 ]; then
    echo "NETWORKING=yes" > %{_sysconfdir}/sysconfig/network
    echo "NETWORKING_IPV6=no" >> %{_sysconfdir}/sysconfig/network
    echo "HOSTNAME=osdial.callcentersg.com" >> %{_sysconfdir}/sysconfig/network
    echo "127.0.0.1 osdial.callcentersg.com osdial" > %{_sysconfdir}/hosts
    echo "127.0.0.1 localhost.localdomain localhost" >> %{_sysconfdir}/hosts
    echo "::1       localhost6.localdomain6 localhost6" >> %{_sysconfdir}/hosts
fi
echo -n

%post profile-install-control
INTY=$1
if [ "$INTY" -eq 1 ]; then
    echo "NETWORKING=yes" > %{_sysconfdir}/sysconfig/network
    echo "NETWORKING_IPV6=no" >> %{_sysconfdir}/sysconfig/network
    echo "HOSTNAME=osdial-c1.callcentersg.com" >> %{_sysconfdir}/sysconfig/network
    echo "127.0.0.1 osdial-c1.callcentersg.com osdial-c1 c1" > %{_sysconfdir}/hosts
    echo "127.0.0.1 localhost.localdomain localhost" >> %{_sysconfdir}/hosts
    echo "::1       localhost6.localdomain6 localhost6" >> %{_sysconfdir}/hosts
fi
echo -n

%post profile-install-dialer
INTY=$1
if [ "$INTY" -eq 1 ]; then
    echo "NETWORKING=yes" > %{_sysconfdir}/sysconfig/network
    echo "NETWORKING_IPV6=no" >> %{_sysconfdir}/sysconfig/network
    echo "HOSTNAME=osdial-dN.callcentersg.com" >> %{_sysconfdir}/sysconfig/network
    echo "127.0.0.1 osdial-dN.callcentersg.com osdial-dN dN" > %{_sysconfdir}/hosts
    echo "127.0.0.1 localhost.localdomain localhost" >> %{_sysconfdir}/hosts
    echo "::1       localhost6.localdomain6 localhost6" >> %{_sysconfdir}/hosts
fi
echo -n

%post profile-install-sql
INTY=$1
if [ "$INTY" -eq 1 ]; then
    echo "NETWORKING=yes" > %{_sysconfdir}/sysconfig/network
    echo "NETWORKING_IPV6=no" >> %{_sysconfdir}/sysconfig/network
    echo "HOSTNAME=osdial-s1.callcentersg.com" >> %{_sysconfdir}/sysconfig/network
    echo "127.0.0.1 osdial-s1.callcentersg.com osdial-s1 s1" > %{_sysconfdir}/hosts
    echo "127.0.0.1 localhost.localdomain localhost" >> %{_sysconfdir}/hosts
    echo "::1       localhost6.localdomain6 localhost6" >> %{_sysconfdir}/hosts
fi
echo -n

%post profile-install-web
INTY=$1
if [ "$INTY" -eq 1 ]; then
    echo "NETWORKING=yes" > %{_sysconfdir}/sysconfig/network
    echo "NETWORKING_IPV6=no" >> %{_sysconfdir}/sysconfig/network
    echo "HOSTNAME=osdial-w1.callcentersg.com" >> %{_sysconfdir}/sysconfig/network
    echo "127.0.0.1 osdial-w1.callcentersg.com osdial-w1 w1" > %{_sysconfdir}/hosts
    echo "127.0.0.1 localhost.localdomain localhost" >> %{_sysconfdir}/hosts
    echo "::1       localhost6.localdomain6 localhost6" >> %{_sysconfdir}/hosts
fi
echo -n

%post profile-install-archive
INTY=$1
if [ "$INTY" -eq 1 ]; then
    echo "NETWORKING=yes" > %{_sysconfdir}/sysconfig/network
    echo "NETWORKING_IPV6=no" >> %{_sysconfdir}/sysconfig/network
    echo "HOSTNAME=osdial-a1.callcentersg.com" >> %{_sysconfdir}/sysconfig/network
    echo "127.0.0.1 osdial-a1.callcentersg.com osdial-a1 a1" > %{_sysconfdir}/hosts
    echo "127.0.0.1 localhost.localdomain localhost" >> %{_sysconfdir}/hosts
    echo "::1       localhost6.localdomain6 localhost6" >> %{_sysconfdir}/hosts
fi
echo -n

%endif





%post common
/sbin/chkconfig --add osdial > /dev/null 2>&1 || :
INTY=$1
if [ "${INTY}" -eq 1 ]; then
    /sbin/chkconfig --del osdial_resource_send > /dev/null 2>&1 || :
    /sbin/chkconfig --add osdial_resource_send > /dev/null 2>&1 || :
    /sbin/service osdial_resource_send restart > /dev/null 2>&1 || :
    /sbin/chkconfig osdial on > /dev/null 2>&1 || :
    # Make sure SELINUX didn't get turned on...
    if [ -f "%{_sysconfdir}/selinux/config" ]; then
        SELINUX="`%{__grep} '^SELINUX=' %{_sysconfdir}/selinux/config | %{__awk} -F= '{ print $2 }'`"
        if [ "${SELINUX}" == "enforcing" ]; then
            echo "    osdial-config: SELINUX is set to ENFORCING!"
            echo "                         Setting to SELINUX=disabled in %{_sysconfdir}/selinux/config."
            echo "                         You must reboot after install!!!"
            %{__perl} -pi -e 's|^SELINUX=.*|SELINUX=disabled|' %{_sysconfdir}/selinux/config
        elif [ "${SELINUX}" == "permissive" ]; then
            echo "    osdial-config: SELINUX is set to PERMISSIVE!"
            echo "                         This should be fine, but if you have problems"
            echo "                         logging in, you should modify %{_sysconfdir}/selinux/config,"
            echo "                         change the line SELINUX=permissive to SELINUX=disabled"
            echo "                         and reboot the server."
        elif [ -z "${SELINUX}" ]; then
            echo "    osdial-config: SELINUX directive not found!"
            echo "                         Adding SELINUX=disabled to %{_sysconfdir}/selinux/config."
            echo "SELINUX=disabled" >> %{_sysconfdir}/selinux/config
        elif [ "${SELINUX}" != "disabled" ]; then
            echo "    osdial-config: SELINUX is set to an UNKNOWN MODE! (${SELINUX})"
            echo "                         Setting to SELINUX=disabled in %{_sysconfdir}/selinux/config."
            echo "                         You must reboot after install!!!"
            %{__perl} -pi -e 's|^SELINUX=.*|SELINUX=disabled|' %{_sysconfdir}/selinux/config
        fi
    fi
    if [ -f "%{_opt}/osdial/.osdial-control" ]; then
        %{__perl} -pi -e 's|^VARactive_keepalives =\> 1234569$|VARactive_keepalives => 579|' %{_sysconfdir}/osdial.conf
    elif [ -f "%{_opt}/osdial/.osdial-sql" ]; then
        %{__perl} -pi -e 's|^VARactive_keepalives =\> 1234569$|VARactive_keepalives => 579|' %{_sysconfdir}/osdial.conf
    elif [ -f "%{_opt}/osdial/.osdial-dialer" ]; then
        %{__perl} -pi -e 's|^VARactive_keepalives =\> 1234569$|VARactive_keepalives => 12346|' %{_sysconfdir}/osdial.conf
    elif [ -f "%{_opt}/osdial/.osdial-dialer-web" ]; then
        %{__perl} -pi -e 's|^VARactive_keepalives =\> 1234569$|VARactive_keepalives => 12346|' %{_sysconfdir}/osdial.conf
    elif [ -f "%{_opt}/osdial/.osdial-web" ]; then
        %{__perl} -pi -e 's|^VARactive_keepalives =\> 1234569$|VARactive_keepalives => X|' %{_sysconfdir}/osdial.conf
    elif [ -f "%{_opt}/osdial/.osdial-archive" ]; then
        %{__perl} -pi -e 's|^VARactive_keepalives =\> 1234569$|VARactive_keepalives => X|' %{_sysconfdir}/osdial.conf
    fi
fi
if [ "${INTY}" -eq 2 ]; then
    %{_opt}/osdial/bin/osdial_killall.sh
    %{__mkdir_p} %{_opt}/osdial/backups/%{version}-%{release} > /dev/null 2>&1 || :
    %{__cp} -a %{_opt}/osdial/bin %{_opt}/osdial/backups/%{version}-%{release} > /dev/null 2>&1 || :

    %{__mkdir} %{_opt}/osdial/backups/%{version}-%{release}%{_sysconfdir} > /dev/null 2>&1 || :
    %{__cp} -a %{_sysconfdir}/osdial.conf %{_opt}/osdial/backups/%{version}-%{release}%{_sysconfdir} > /dev/null 2>&1 || :
    if [ -d "%{_sysconfdir}/asterisk" ]; then
        [ -f "%{_sysconfdir}/dahdi/system.conf" ] && %{__cp} -a %{_sysconfdir}/dahdi/system.conf %{_opt}/osdial/backups/%{version}-%{release}%{_sysconfdir} > /dev/null 2>&1 || :
        [ -f "%{_sysconfdir}/zaptel.conf" ] && %{__cp} -a %{_sysconfdir}/zaptel.conf %{_opt}/osdial/backups/%{version}-%{release}%{_sysconfdir} > /dev/null 2>&1 || :
        [ -d "%{_sysconfdir}/asterisk" ] && %{__cp} -a %{_sysconfdir}/asterisk %{_opt}/osdial/backups/%{version}-%{release}%{_sysconfdir} > /dev/null 2>&1 || :
    fi
    if [ -d "%{_sharedstatedir}/asterisk/agi-bin" ]; then
        %{__mkdir} %{_opt}/osdial/backups/%{version}-%{release}/agi > /dev/null 2>&1 || :
        %{__cp} -a %{_sharedstatedir}/asterisk/agi-bin %{_opt}/osdial/backups/%{version}-%{release}/agi > /dev/null 2>&1 || :
    fi
    if [ -d "%{_opt}/osdial/html" ]; then
        %{__mkdir} %{_opt}/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1 || :
        %{__mkdir} %{_opt}/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1 || :
        %{__mkdir} %{_opt}/osdial/backups/%{version}-%{release}/html/agent > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/*.php %{_opt}/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/*.txt %{_opt}/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/*.ico %{_opt}/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/ivr %{_opt}/osdial/backups/%{version}-%{release}/html > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/admin/*.php %{_opt}/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/admin/*.pl %{_opt}/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/admin/*.css %{_opt}/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/admin/*.gif %{_opt}/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/admin/*.png %{_opt}/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/admin/include %{_opt}/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/admin/templates %{_opt}/osdial/backups/%{version}-%{release}/html/admin > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/agent/*.php %{_opt}/osdial/backups/%{version}-%{release}/html/agent > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/agent/include %{_opt}/osdial/backups/%{version}-%{release}/html/agent > /dev/null 2>&1 || :
        %{__cp} -a %{_opt}/osdial/html/agent/templates %{_opt}/osdial/backups/%{version}-%{release}/html/agent > /dev/null 2>&1 || :
    fi
    if [ -f "%{_sysconfdir}/openvpn/osdial.up" ]; then
        HST=`/bin/hostname -s`        
        if [ "$HST" = "osdial" -o "$HST" = "osdial-live" -o "$HST" = "osdial-ccsg" ]; then
            echo "osdial" > %{_sysconfdir}/openvpn/osdial.up                                    
            echo "osdial1234" >> %{_sysconfdir}/openvpn/osdial.up                               
        else
            echo "${HST}" > %{_sysconfdir}/openvpn/osdial.up                                    
            echo "0o1s2d3i4a5l6${HST}6l5a4i3d2s1o0" >> %{_sysconfdir}/openvpn/osdial.up         
        fi                                                                                
    fi    
    # Run update script.
    %{_opt}/osdial/bin/sql/upgrade_sql.pl --info 2>&1 || :

    %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1 || :

    /sbin/chkconfig --del osdial_resource_send > /dev/null 2>&1 || :
    /sbin/chkconfig --add osdial_resource_send > /dev/null 2>&1 || :
    /sbin/service osdial_resource_send restart > /dev/null 2>&1 || :
fi
%{__mkdir_p} %{_opt}/osdial/tts > /dev/null 2>&1 || :
%{__mkdir_p} %{_opt}/osdial/reports > /dev/null 2>&1 || :
%{__mkdir_p} %{_opt}/osdial/recordings > /dev/null 2>&1 || :
%{__mkdir_p} %{_opt}/osdial/recordings/processing > /dev/null 2>&1 || :
%{__mkdir_p} %{_opt}/osdial/recordings/processing/mixed > /dev/null 2>&1 || :
%{__mkdir_p} %{_opt}/osdial/recordings/processing/unmixed > /dev/null 2>&1 || :
%{__mkdir_p} %{_opt}/osdial/recordings/completed > /dev/null 2>&1 || :
%{__mkdir_p} %{_opt}/osdial/backups/recordings > /dev/null 2>&1 || :
%{__chown} -R asterisk:asterisk %{_opt}/osdial/backups/recordings > /dev/null 2>&1 || :
%{__chown} -R asterisk:asterisk %{_opt}/osdial/tts > /dev/null 2>&1 || :
[ -z "`%{__grep} %{_opt}/osdial/reports /proc/mounts`" ] && %{__chown} -R asterisk:asterisk %{_opt}/osdial/reports > /dev/null 2>&1 || :
[ -z "`%{__grep} %{_opt}/osdial/recordings /proc/mounts`" ] && %{__chown} -R asterisk:asterisk %{_opt}/osdial/recordings > /dev/null 2>&1 || :
%{__chmod} 7755 %{_sbindir}/lsof > /dev/null 2>&1 || :
%{__ln_s} -f %{_bindir}/ip_relay %{_opt}/osdial/bin/ip_relay > /dev/null 2>&1 || :

if [ -n "`%{__grep} OSDbuild %{_sysconfdir}/osdial.conf`" ]; then
    %{__perl} -pi -e 's|^OSDversion =>.*|OSDversion => %{version}|' %{_sysconfdir}/osdial.conf
    %{__perl} -pi -e 's|^OSDbuild =>.*|OSDbuild => %{buildver}|' %{_sysconfdir}/osdial.conf
else
    %{__perl} -pi -e 's|^OSDversion =>.*|OSDversion => %{version}\nOSDbuild => %{buildver}|' %{_sysconfdir}/osdial.conf
fi
%{__perl} -pi -e 's|^PATHdocs =>.*|PATHdocs => %{_docdir}/osdial-%{version}|' %{_sysconfdir}/osdial.conf
[ -z "`%{__grep} PATHarchive_backup %{_sysconfdir}/osdial.conf`" ] && %{__perl} -pi -e 's|^PATHarchive_home =>.*|PATHarchive_home => %{_opt}/osdial/recordings\nPATHarchive_backup => %{_opt}/osdial/backups/recordings|' %{_sysconfdir}/osdial.conf || :

%{__perl} -pi -e 's|stacks|stack|' %{_sysconfdir}/security/limits.conf

if [ -n "`%{__grep} OSDial %{_sysconfdir}/security/limits.conf`" ]; then
    %{__sed} -ie '/# OSDial modifications/,//d' %{_sysconfdir}/security/limits.conf
fi

/sbin/chkconfig cgconfig on > /dev/null 2>&1 || :
/sbin/service cgconfig restart > /dev/null 2>&1 || :
/sbin/chkconfig cgred on > /dev/null 2>&1 || :
/sbin/service cgred restart > /dev/null 2>&1 || :
/sbin/chkconfig numad on > /dev/null 2>&1 || :
/sbin/service numad restart > /dev/null 2>&1 || :
/sbin/chkconfig httpd on > /dev/null 2>&1 || :
/sbin/service httpd restart > /dev/null 2>&1 || :
/sbin/chkconfig tuned on > /dev/null 2>&1 || :
TUNEDTST="`%{_sbindir}/tuned-adm active | %{__grep} '^Current active profile: default'`"
RES=$?
if [ $RES -eq 0 ]; then
    %{_sbindir}/tuned-adm profile throughput-performance > /dev/null 2>&1 ||:
    /sbin/service tuned stop > /dev/null 2>&1 || :
    /sbin/service tuned start > /dev/null 2>&1 || :
fi


DRIVES=`ls /dev/sd[a-z] /dev/hd[a-z] /dev/cciss/c[0-9]d[0-9] 2>/dev/null | tr "\n" "," | sed 's|,$||'`
%{__perl} -pi -e "s|/dev/sda, /dev/sdb|${DRIVES}|" %{_opt}/osdial/html/phpsysinfo/plugins/SMART/SMART.config.php || :
%{_sbindir}/usermod -G asterisk,disk apache || :
%{_sbindir}/usermod -G tty,apache,disk asterisk || :

OSDKEY=`%{__cat} %{_sysconfdir}/pki/osdial-support/osdial-support.pub 2>/dev/null`
if [ ! -d "/root/.ssh" ]; then
    %{__mkdir_p} /root/.ssh
fi
if [ ! -f "/root/.ssh/authorized_keys" ]; then
    echo "$OSDKEY" > /root/.ssh/authorized_keys
fi
KCHK=`%{__grep} "$OSDKEY" /root/.ssh/authorized_keys`
if [ -z "$KCHK" ]; then
    echo "$OSDKEY" >> /root/.ssh/authorized_keys
fi
%{__chown_Rhf} root:root /root/.ssh
%{__chmod} 700 /root/.ssh
%{__chmod} 600 /root/.ssh/*
echo -n



%post sql
INTY=$1
if [ "$INTY" -eq 1 ]; then
    /sbin/chkconfig mysqld on > /dev/null 2>&1
    # Apply OSDial SQL changes to %{_sysconfdir}/my.cnf
    if [ ! "`%{__grep} innodb_data_home_dir %{_sysconfdir}/my.cnf`" ]; then
        MEM=`head -1 /proc/meminfo | %{__sed} 's/MemTotal:\s*\(.*\) kB.*/\1/'`
        let MEM=MEM/1024/2
        # Stop mysql
        /sbin/service mysqld stop > /dev/null 2>&1 || :
        sleep 3
        if [ -f "%{_sysconfdir}/my.cnf.d/server.cnf" ]; then
            %{__perl} -pi -e "s|^innodb_buffer_pool_size = 512M$|innodb_buffer_pool_size = ${MEM}M|" %{_sysconfdir}/my.cnf.d/server.cnf > /dev/null 2>&1 || :
            if [ ! -f "%{_sharedstatedir}/mysql/mysql.lock" ]; then
                if [ -f "%{_sharedstatedir}/mysql/ib_logfile0" ]; then
                    %{__rm} -f %{_sharedstatedir}/mysql/ib_logfile0 > /dev/null 2>&1 || :
                    if [ -f "%{_sharedstatedir}/mysql/ib_logfile1" ]; then
                        %{__rm} -f %{_sharedstatedir}/mysql/ib_logfile1 > /dev/null 2>&1 || :
                    fi
                fi
            fi
        else
            MCNF="old_passwords=1\n\n"
            MCNF="${MCNF}#===== BEGIN OSDIAL my.cnf Additions =====\n"
            [ -z "`%{__grep} skip_name_resolve %{_sysconfdir}/my.cnf`" ] &&               MCNF="${MCNF}skip_name_resolve\n" || :
            [ -z "`%{__grep} max_connections %{_sysconfdir}/my.cnf`" ] &&                 MCNF="${MCNF}max_connections=250\n" || :
            [ -z "`%{__grep} open_files_limit %{_sysconfdir}/my.cnf`" ] &&                MCNF="${MCNF}open_files_limit=32768\n" || :
            [ -z "`%{__grep} query_cache_type %{_sysconfdir}/my.cnf`" ] &&                MCNF="${MCNF}query_cache_type = 1\nquery_cache_size = 100000000\nquery_cache_min_res_unit = 4096\nquery_cache_limit = 1048576\nquery_prealloc_size = 8192\nquery_cache_wlock_invalidate = 0\n" || :
            [ -z "`%{__grep} innodb_strict_mode %{_sysconfdir}/my.cnf`" ] &&              MCNF="${MCNF}loose_innodb_strict_mode = 1\n" || :
            [ -z "`%{__grep} innodb_file_format %{_sysconfdir}/my.cnf`" ] &&              MCNF="${MCNF}loose_innodb_file_format = barracuda\n" || :
            [ -z "`%{__grep} innodb_data_home_dir %{_sysconfdir}/my.cnf`" ] &&            MCNF="${MCNF}loose_innodb_data_home_dir = %{_sharedstatedir}/mysql/\n" || :
            [ -z "`%{__grep} innodb_log_group_home_dir %{_sysconfdir}/my.cnf`" ] &&       MCNF="${MCNF}loose_innodb_log_group_home_dir = %{_sharedstatedir}/mysql/\n" || :
            [ -z "`%{__grep} innodb_data_file_path %{_sysconfdir}/my.cnf`" ] &&           MCNF="${MCNF}loose_innodb_data_file_path = ibdata1:10M:autoextend\n" || :
            [ -z "`%{__grep} innodb_additional_mem_pool_size %{_sysconfdir}/my.cnf`" ] && MCNF="${MCNF}loose_innodb_additional_mem_pool_size = 8M\n" || :
            [ -z "`%{__grep} innodb_log_file_size %{_sysconfdir}/my.cnf`" ] &&            MCNF="${MCNF}loose_innodb_log_file_size = 5M\n" || :
            [ -z "`%{__grep} innodb_log_buffer_size %{_sysconfdir}/my.cnf`" ] &&          MCNF="${MCNF}loose_innodb_log_buffer_size = 8M\n" || :
            [ -z "`%{__grep} innodb_file_per_table %{_sysconfdir}/my.cnf`" ] &&           MCNF="${MCNF}loose_innodb_file_per_table = 1\n" || :
            [ -z "`%{__grep} innodb_flush_log_at_trx_commit %{_sysconfdir}/my.cnf`" ] &&  MCNF="${MCNF}loose_innodb_flush_log_at_trx_commit = 2\n" || :
            [ -z "`%{__grep} innodb_lock_wait_timeout %{_sysconfdir}/my.cnf`" ] &&        MCNF="${MCNF}loose_innodb_lock_wait_timeout = 50\n" || :
            [ -z "`%{__grep} innodb_adaptive_hash_index %{_sysconfdir}/my.cnf`" ] &&      MCNF="${MCNF}loose_innodb_adaptive_hash_index = 1\n" || :
            [ -z "`%{__grep} innodb_checksums %{_sysconfdir}/my.cnf`" ] &&                MCNF="${MCNF}loose_innodb_checksums = 1\n" || :
            [ -z "`%{__grep} innodb_doublewrite %{_sysconfdir}/my.cnf`" ] &&              MCNF="${MCNF}loose_innodb_doublewrite = 1\n" || :
            [ -z "`%{__grep} innodb_flush_method %{_sysconfdir}/my.cnf`" ] &&             MCNF="${MCNF}loose_innodb_flush_method = O_DIRECT\n" || :
            [ -z "`%{__grep} innodb_locks_unsafe_for_binlog %{_sysconfdir}/my.cnf`" ] &&  MCNF="${MCNF}loose_innodb_locks_unsafe_for_binlog = 0\n" || :
            [ -z "`%{__grep} innodb_max_dirty_pages_pct %{_sysconfdir}/my.cnf`" ] &&      MCNF="${MCNF}loose_innodb_max_dirty_pages_pct = 90\n" || :
            [ -z "`%{__grep} innodb_table_locks %{_sysconfdir}/my.cnf`" ] &&              MCNF="${MCNF}loose_innodb_table_locks = 1\n" || :
            [ -z "`%{__grep} innodb_thread_concurrency %{_sysconfdir}/my.cnf`" ] &&       MCNF="${MCNF}loose_innodb_thread_concurrency = 0\n" || :
            [ -z "`%{__grep} innodb_use_sys_malloc %{_sysconfdir}/my.cnf`" ] &&           MCNF="${MCNF}loose_innodb_use_sys_malloc = 0\n" || :
            [ -z "`%{__grep} innodb_fast_shutdown %{_sysconfdir}/my.cnf`" ] &&            MCNF="${MCNF}loose_innodb_fast_shutdown = 0\n" || :
            [ -z "`%{__grep} innodb_open_files %{_sysconfdir}/my.cnf`" ] &&               MCNF="${MCNF}loose_innodb_open_files = 2048\n" || :
            [ -z "`%{__grep} innodb_buffer_pool_size %{_sysconfdir}/my.cnf`" ] &&         MCNF="${MCNF}# Should be set to 50% system memory\n" || :
            [ -z "`%{__grep} innodb_buffer_pool_size %{_sysconfdir}/my.cnf`" ] &&         MCNF="${MCNF}loose_innodb_buffer_pool_size = ${MEM}M\n" || :
            MCNF="${MCNF}#===== END OSDIAL my.cnf Additions =====\n\n"
            %{__perl} -pi -e "s|old_passwords=1|$MCNF|" %{_sysconfdir}/my.cnf > /dev/null 2>&1 || :
        fi
        # Start mysql
        /sbin/service mysqld start > /dev/null 2>&1 || :
        sleep 3
    fi
    # Run update script.
    [ -f "%{_opt}/osdial/.osdial-live" ] && /sbin/service mysqld start > /dev/null 2>&1 || :
    %{_opt}/osdial/bin/sql/upgrade_sql.pl --install 2> /dev/null
    [ -f "%{_opt}/osdial/.osdial-live" ] && /sbin/service mysqld stop > /dev/null 2>&1 || :
    # If it didn't get created, assume it is an installcd
    [ ! -d "%{_sharedstatedir}/mysql/osdial" ] && echo "OSDIAL_MYSQL_INSTALL=YES" >> %{_sysconfdir}/sysconfig/osdial
    # cpuspeed can do bad things to ISDN/T1 cards
    if [ -f "%{_sysconfdir}/rc3.d/S06cpuspeed" ]; then
        echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
        %{_sysconfdir}/init.d/cpuspeed stop > /dev/null 2>&1
        /sbin/chkconfig cpuspeed off > /dev/null 2>&1
    fi
    # We don't need cups
    if [ -f "%{_sysconfdir}/rc3.d/S56cups" ]; then
        echo "    osdial-config: CUPS detected, disabling!"
        %{_sysconfdir}/init.d/cups stop > /dev/null 2>&1
        /sbin/chkconfig cups off > /dev/null 2>&1
    fi
fi
if [ "$INTY" -eq 2 ]; then
    # Reset running procs.
    if [ -f "%{_opt}/osdial/bin/osdial_killall.sh" ]; then
        %{_opt}/osdial/bin/osdial_killall.sh > /dev/null 2>&1 || :
    else
        [ -n "`ps -ef | %{__grep} FastAGI`" ] && kill -9 `ps -ef | %{__grep} FastAGI | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
        [ -n "`ps -ef | %{__grep} AST`" ] && kill -9 `ps -ef | %{__grep} AST | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
    fi
fi
echo -n

%post web
INTY=$1
if [ "$INTY" -eq 1 ]; then
    /sbin/chkconfig --del osdial_resource_listen > /dev/null 2>&1
    /sbin/chkconfig --add osdial_resource_listen > /dev/null 2>&1
    /sbin/service osdial_resource_listen restart > /dev/null 2>&1
    /sbin/chkconfig httpd on > /dev/null 2>&1
    if [ -f "%{_localstatedir}/www/html/index.html" ]; then
        [ -n "`%{__grep} osdial %{_localstatedir}/www/html/index.html`" ] && %{__mv} %{_localstatedir}/www/html/index.html %{_localstatedir}/www/html/index.html.bak || :
    fi
    [ ! -f "%{_localstatedir}/www/html/index.php" ] && %{__ln_s} %{_opt}/osdial/html/index.php %{_localstatedir}/www/html/index.php || :
    # modify php.ini for our defaults.
    if [ ! "`%{__grep} OSDIAL %{_sysconfdir}/php.ini`" ]; then
        %{__perl} -pi -e "s|^max_execution_time = 30     ; Maximum execution|max_execution_time = 300000 ; Maximum execution|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^max_execution_time = 30     $|max_execution_time = 300000|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^max_execution_time = 30$|max_execution_time = 300000|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^max_input_time = 60    ; Maximum amount of time|max_input_time = 600000 ; Maximum amount of time|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^max_input_time = 60$|max_input_time = 600000|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^memory_limit = 16M      ; Maximum amount of mem|memory_limit = 512M      ; Maximum amount of mem|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^memory_limit = 128M$|memory_limit = 512M|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^post_max_size = 8M|post_max_size = 100M|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^upload_max_filesize = 2M|upload_max_filesize = 100M|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^short_open_tag = Off$|short_open_tag = On|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^;error_reporting = E_ALL \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_NOTICE|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting  =  E_ALL|;error_reporting = E_ALL|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_NOTICE \& ~E_DEPRECATED$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        echo "; OSDIAL: modified" >> %{_sysconfdir}/php.ini
    fi
    [ -f "%{_opt}/osdial/.osdial-live" ] && /sbin/service httpd stop > /dev/null 2>&1 || :
    # cpuspeed can do bad things to ISDN/T1 cards
    if [ -f "%{_sysconfdir}/rc3.d/S06cpuspeed" ]; then
        echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
        %{_sysconfdir}/init.d/cpuspeed stop > /dev/null 2>&1 || :
        /sbin/chkconfig cpuspeed off > /dev/null 2>&1 || :
    fi
    # We don't need cups
    if [ -f "%{_sysconfdir}/rc3.d/S56cups" ]; then
        echo "    osdial-config: CUPS detected, disabling!"
        %{_sysconfdir}/init.d/cups stop > /dev/null 2>&1 || :
        /sbin/chkconfig cups off > /dev/null 2>&1 || :
    fi
fi
if [ "$INTY" -eq 2 ]; then
    /sbin/chkconfig --del osdial_resource_listen > /dev/null 2>&1 || :
    /sbin/chkconfig --add osdial_resource_listen > /dev/null 2>&1 || :
    /sbin/service osdial_resource_listen restart > /dev/null 2>&1 || :
    if [ -f "%{_localstatedir}/www/html/index.html" ]; then
        [ -n "`%{__grep} osdial %{_localstatedir}/www/html/index.html`" ] && %{__mv} %{_localstatedir}/www/html/index.html %{_localstatedir}/www/html/index.html.bak || :
    fi
    if [ ! "`%{__grep} OSDIAL %{_sysconfdir}/php.ini`" ]; then
        %{__perl} -pi -e "s|^memory_limit = 128M$|memory_limit = 512M|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^max_execution_time = 30     $|max_execution_time = 300000|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^max_execution_time = 30$|max_execution_time = 300000|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^max_input_time = 60$|max_input_time = 600000|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_NOTICE \& ~E_DEPRECATED$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
        %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
    fi
    [ ! -f "%{_localstatedir}/www/html/index.php" ] && %{__ln_s} %{_opt}/osdial/html/index.php %{_localstatedir}/www/html/index.php || :
    # Reset running procs.
    [ -n "`ps -ef | %{__grep} FastAGI`" ] && kill -9 `ps -ef | %{__grep} FastAGI | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
    [ -n "`ps -ef | %{__grep} AST`" ] && kill -9 `ps -ef | %{__grep} AST | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
if [ -f "%{_sysconfdir}/php.ini" ]; then
    %{__perl} -pi -e "s|^max_execution_time = 30     ; Maximum execution|max_execution_time = 300000 ; Maximum execution|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^max_execution_time = 30     $|max_execution_time = 300000|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^max_execution_time = 30$|max_execution_time = 300000|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^max_input_time = 60    ; Maximum amount of time|max_input_time = 600000 ; Maximum amount of time|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^max_input_time = 60$|max_input_time = 600000|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^memory_limit = 16M      ; Maximum amount of mem|memory_limit = 512M      ; Maximum amount of mem|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^memory_limit = 128M$|memory_limit = 512M|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^post_max_size = 8M|post_max_size = 100M|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^upload_max_filesize = 2M|upload_max_filesize = 100M|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^short_open_tag = Off$|short_open_tag = On|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^error_reporting = E_ALL$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_NOTICE \& ~E_DEPRECATED$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
    %{__perl} -pi -e "s|^error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING$|error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_NOTICE \& ~E_WARNING \& ~E_STRICT|" %{_sysconfdir}/php.ini
    /sbin/service httpd restart > /dev/null 2>&1 || :
fi
echo -n

%post dialer
INTY=$1
if [ "$INTY" -eq 1 ]; then
    # Put in some ramdisk on the dialer.
    if [ "`%{__grep} OSDIAL %{_sysconfdir}/rc.local`" ]; then
        OSDTMP=/tmp/osdtmp.$$
        %{__sed} -e '/BEGIN OSDIAL/,/END OSDIAL/d' %{_sysconfdir}/rc.local > $OSDTMP
        %{__sed} -e '/ramdisk/d' $OSDTMP > %{_sysconfdir}/rc.local
    fi
    # Lets turn on the cron!
    if [ -f "%{_localstatedir}/spool/cron/asterisk" ]; then
        CRGRP="`%{__grep} ADMIN_keepalive_ALL %{_localstatedir}/spool/cron/asterisk`"
        if [ -n "$CRGRP" ]; then
            # It already exists, so lets overwrite our existing.
            echo "    osdial-config: Cron for user 'asterisk' already in place."
            %{__cat} %{_localstatedir}/spool/cron/asterisk > %{_opt}/osdial/bin/osdial.cron
            %{__rm} -f %{_localstatedir}/spool/cron/asterisk > /dev/null 2>&1 || :
        fi
    fi
    echo "    osdial-config: Installing cron for user 'asterisk'."
    %{_bindir}/crontab -u asterisk %{_opt}/osdial/bin/osdial.cron > /dev/null 2>&1
    # If it didn't succeed, assume installcd
    [ ! -f "%{_localstatedir}/spool/cron/asterisk" ] && %{__cp} %{_opt}/osdial/bin/osdial.cron %{_localstatedir}/spool/cron/asterisk > /dev/null 2>&1 || :
    # Verify config was copied, if not, we are new.
    if [ ! -f "%{_sysconfdir}/osdial.conf" ]; then
        echo "    osdial-config: Setting up keepalive services."
        %{__perl} -pi -e 's|^VARactive_keepalives => XX$|VARactive_keepalives => 1234569|' %{_sysconfdir}/osdial.conf > /dev/null 2>&1 || :
    fi
    # We don't need cups
    if [ -f "%{_sysconfdir}/rc3.d/S56cups" ]; then
        echo "    osdial-config: CUPS detected, disabling!"
        %{_sysconfdir}/init.d/cups stop > /dev/null 2>&1 || :
        /sbin/chkconfig cups off > /dev/null 2>&1 || :
    fi
fi
[ "$INTY" -eq 2 ] && echo -n || :
# Reset running procs.
[ -n "`ps -ef | %{__grep} FastAGI`" ] && kill -9 `ps -ef | %{__grep} FastAGI | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
[ -n "`ps -ef | %{__grep} AST`" ] && kill -9 `ps -ef | %{__grep} AST | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
# Make sure we have backups of the prompts and sync them to sounds and sounds.ramfs
%{__mkdir_p} %{_sharedstatedir}/asterisk/OSDprompts > /dev/null 2>&1 || :
[ -d "%{_sharedstatedir}/asterisk/sounds.ramfs" ] && yes | %{__cp} -au %{_sharedstatedir}/asterisk/sounds.ramfs/851* %{_sharedstatedir}/asterisk/OSDprompts > /dev/null 2>&1 || :
[ -d "/mnt/ramdisk/sounds" ] && yes | %{__cp} -au /mnt/ramdisk/sounds/851* %{_sharedstatedir}/asterisk/OSDprompts > /dev/null 2>&1 || :
yes | %{__cp} -au %{_sharedstatedir}/asterisk/sounds/851* %{_sharedstatedir}/asterisk/OSDprompts > /dev/null 2>&1
yes | %{__cp} -au %{_sharedstatedir}/asterisk/OSDprompts/851* %{_sharedstatedir}/asterisk/sounds > /dev/null 2>&1
[ -d "%{_sharedstatedir}/asterisk/sounds.ramfs" ] && yes | %{__cp} -au %{_sharedstatedir}/asterisk/OSDprompts/851* %{_sharedstatedir}/asterisk/sounds.ramfs > /dev/null 2>&1 || :
[ -d "/mnt/ramdisk/sounds" ] && yes | %{__cp} -au %{_sharedstatedir}/asterisk/OSDprompts/851* /mnt/ramdisk/sounds > /dev/null 2>&1 || :
%{__chown} -R asterisk:asterisk %{_sharedstatedir}/asterisk/sounds > /dev/null 2>&1 || :
%{__chown} -R asterisk:asterisk %{_sharedstatedir}/asterisk/OSDprompts > /dev/null 2>&1 || :
[ -d "%{_sharedstatedir}/asterisk/sounds.ramfs" ] && %{__chown} -R asterisk:asterisk %{_sharedstatedir}/asterisk/sounds.ramfs > /dev/null 2>&1 || :
[ -d "/mnt/ramdisk/sounds" ] && %{__chown} -R asterisk:asterisk /mnt/ramdisk/sounds > /dev/null 2>&1 || :
if [ -d "%{_sharedstatedir}/asterisk/sounds/osdial" ]; then
    %{__chmod} 0777 %{_sharedstatedir}/asterisk/sounds/osdial > /dev/null 2>&1 || :
    %{__chmod} 0666 %{_sharedstatedir}/asterisk/sounds/osdial/* > /dev/null 2>&1 || :
fi
if [ -d "/mnt/ramdisk/sounds/osdial" ]; then
    %{__chmod} 0777 /mnt/ramdisk/sounds/osdial > /dev/null 2>&1 || :
    %{__chmod} 0666 /mnt/ramdisk/sounds/osdial/* > /dev/null 2>&1 || :
fi
if [ -d "%{_opt}/osdial/media" ]; then
    %{__chmod} 0777 %{_opt}/osdial/media > /dev/null 2>&1 || :
    %{__chmod} 0666 %{_opt}/osdial/media/* > /dev/null 2>&1 || :
fi
%{_opt}/osdial/bin/osdial_media_sync.pl --file=%{_sharedstatedir}/asterisk/sounds/en/vm-goodbye.ulaw > /dev/null 2>&1 || :
%{_opt}/osdial/bin/osdial_media_sync.pl --file=%{_sharedstatedir}/asterisk/sounds/generic_hold.ulaw > /dev/null 2>&1 || :


# Reload dsp.conf to ensure we have a silence_threshold
%{_sbindir}/asterisk -rx "reload dsp" > /dev/null 2>&1 || :
cd %{_opt}/osdial/html/ari/bin || :
tar xzf aribins.tgz . > /dev/null 2>&1 || :
echo -n

%post asterisk-version16
INTY=$1
/sbin/chkconfig asterisk on > /dev/null 2>&1 || :
# cpuspeed can do bad things to ISDN/T1 cards
if [ -f "%{_sysconfdir}/rc3.d/S06cpuspeed" ]; then
    echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
    %{_sysconfdir}/init.d/cpuspeed stop > /dev/null 2>&1 || :
    /sbin/chkconfig cpuspeed off > /dev/null 2>&1 || :
fi
# Set some performance options in asterisk...
if [ -f "%{_sysconfdir}/asterisk/asterisk.conf" ]; then
    %{__perl} -pi -e 's|^;timestamp = yes|timestamp = yes|' %{_sysconfdir}/asterisk/asterisk.conf
    %{__perl} -pi -e 's|^timestamp = no|timestamp = yes|' %{_sysconfdir}/asterisk/asterisk.conf

    %{__perl} -pi -e 's|^;highpriority = yes|highpriority = yes|' %{_sysconfdir}/asterisk/asterisk.conf
    %{__perl} -pi -e 's|^highpriority = no|highpriority = yes|' %{_sysconfdir}/asterisk/asterisk.conf

    %{__perl} -pi -e 's|^;internal_timing = yes|internal_timing = yes|' %{_sysconfdir}/asterisk/asterisk.conf
    %{__perl} -pi -e 's|^internal_timing = no|internal_timing = yes|' %{_sysconfdir}/asterisk/asterisk.conf

    %{__perl} -pi -e 's|^;transmit_silence = yes|transmit_silence = no|' %{_sysconfdir}/asterisk/asterisk.conf
    %{__perl} -pi -e 's|^transmit_silence = yes|transmit_silence = no|' %{_sysconfdir}/asterisk/asterisk.conf

    %{__perl} -pi -e 's|^;transmit_silence_during_record = yes|transmit_silence_during_record = yes|' %{_sysconfdir}/asterisk/asterisk.conf
    %{__perl} -pi -e 's|^transmit_silence_during_record = no|transmit_silence_during_record = yes|' %{_sysconfdir}/asterisk/asterisk.conf

    %{__perl} -pi -e 's|^;transcode_via_sln = yes|transcode_via_sln = no|' %{_sysconfdir}/asterisk/asterisk.conf
    %{__perl} -pi -e 's|^transcode_via_sln = yes|transcode_via_sln = no|' %{_sysconfdir}/asterisk/asterisk.conf

    %{__perl} -pi -e 's|^;cache_record_files = yes|cache_record_files = yes|' %{_sysconfdir}/asterisk/asterisk.conf
    %{__perl} -pi -e 's|^cache_record_files = no|cache_record_files = yes|' %{_sysconfdir}/asterisk/asterisk.conf

    %{__perl} -pi -e 's|^;record_cache_dir = /tmp|record_cache_dir = %{_localstatedir}/spool/asterisk/record_cache|' %{_sysconfdir}/asterisk/asterisk.conf
    %{__perl} -pi -e 's|^record_cache_dir = .*$|record_cache_dir = %{_localstatedir}/spool/asterisk/record_cache|' %{_sysconfdir}/asterisk/asterisk.conf
fi
# Remove bad chan-dahdi.conf, bad filename.
[ -f "%{_sysconfdir}/asterisk/chan-dahdi.conf" ] && %{__rm} -f %{_sysconfdir}/asterisk/chan-dahdi.conf > /dev/null 2>&1 || :
# Find and move zapata.conf
%{__mkdir_p} %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
MOVED=0
if [ -f "%{_sysconfdir}/asterisk/zapata.conf" ]; then
    if [ "$MOVED" = "0" ]; then
        MOVED=1
        %{__cp} -f %{_sysconfdir}/asterisk/zapata.conf %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
        %{__mv} -f %{_sysconfdir}/asterisk/zapata.conf %{_sysconfdir}/asterisk/chan_dahdi.conf > /dev/null 2>&1 || :
    else
        %{__mv} -f %{_sysconfdir}/asterisk/zapata.conf %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
    fi
fi
if [ -f "%{_sysconfdir}/asterisk/zapata.conf.rpmsave" ]; then
    if [ "$MOVED" = "0" ]; then
        MOVED=1
        %{__cp} -f %{_sysconfdir}/asterisk/zapata.conf.rpmsave %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
        %{__mv} -f %{_sysconfdir}/asterisk/zapata.conf.rpmsave %{_sysconfdir}/asterisk/chan_dahdi.conf > /dev/null 2>&1 || :
    else
        %{__mv} -f %{_sysconfdir}/asterisk/zapata.conf.rpmsave %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
    fi
fi
if [ -f "%{_sysconfdir}/asterisk/zapata.conf.rpmorig" ]; then
    if [ "$MOVED" = "0" ]; then
        MOVED=1
        %{__cp} -f %{_sysconfdir}/asterisk/zapata.conf.rpmorig %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
        %{__mv} -f %{_sysconfdir}/asterisk/zapata.conf.rpmorig %{_sysconfdir}/asterisk/chan_dahdi.conf > /dev/null 2>&1 || :
    else
        %{__mv} -f %{_sysconfdir}/asterisk/zapata.conf.rpmorig %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
    fi
fi
if [ -f "%{_sysconfdir}/asterisk/zapata.conf.rpmnew" ]; then
    if [ "$MOVED" = "0" ]; then
        MOVED=1
        %{__cp} -f %{_sysconfdir}/asterisk/zapata.conf.rpmnew %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
        %{__mv} -f %{_sysconfdir}/asterisk/zapata.conf.rpmnew %{_sysconfdir}/asterisk/chan_dahdi.conf > /dev/null 2>&1 || :
    else
        %{__mv} -f %{_sysconfdir}/asterisk/zapata.conf.rpmnew %{_sysconfdir}/zaptel.bak > /dev/null 2>&1 || :
    fi
fi
echo -n

%post asterisk-version12
INTY=$1
/sbin/chkconfig asterisk on > /dev/null 2>&1 || :
# cpuspeed can do bad things to ISDN/T1 cards
if [ -f "%{_sysconfdir}/rc3.d/S06cpuspeed" ]; then
    echo "    osdial-config: cpuspeed (scaling) detected, disabling!"
    %{_sysconfdir}/init.d/cpuspeed stop > /dev/null 2>&1 || :
    /sbin/chkconfig cpuspeed off > /dev/null 2>&1 || :
fi
# Find and move chan_dahdi.conf
%{__mkdir_p} %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
MOVED=0
if [ -f "%{_sysconfdir}/asterisk/chan_dahdi.conf" ]; then
    if [ "$MOVED" = "0" ]; then
        MOVED=1
        %{__cp} -f %{_sysconfdir}/asterisk/chan_dahdi.conf %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
        %{__mv} -f %{_sysconfdir}/asterisk/chan_dahdi.conf %{_sysconfdir}/asterisk/zapata.conf > /dev/null 2>&1 || :
    else
        %{__mv} -f %{_sysconfdir}/asterisk/chan_dahdi.conf %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
    fi
fi
if [ -f "%{_sysconfdir}/asterisk/chan_dahdi.conf.rpmsave" ]; then
    if [ "$MOVED" = "0" ]; then
        MOVED=1
        %{__cp} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmsave %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
        %{__mv} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmsave %{_sysconfdir}/asterisk/zapata.conf > /dev/null 2>&1 || :
    else
        %{__mv} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmsave %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
    fi
fi
if [ -f "%{_sysconfdir}/asterisk/chan_dahdi.conf.rpmorig" ]; then
    if [ "$MOVED" = "0" ]; then
        MOVED=1
        %{__cp} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmorig %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
        %{__mv} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmorig %{_sysconfdir}/asterisk/zapata.conf > /dev/null 2>&1 || :
    else
        %{__mv} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmorig %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
    fi
fi
if [ -f "%{_sysconfdir}/asterisk/chan_dahdi.conf.rpmnew" ]; then
    if [ "$MOVED" = "0" ]; then
        MOVED=1
        %{__cp} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmnew %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
        %{__mv} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmnew %{_sysconfdir}/asterisk/zapata.conf > /dev/null 2>&1 || :
    else
        %{__mv} -f %{_sysconfdir}/asterisk/chan_dahdi.conf.rpmnew %{_sysconfdir}/dahdi.bak > /dev/null 2>&1 || :
    fi
fi
echo -n


%post profile-all
INTY=$1
if [ "$INTY" -ge 1 ]; then
    CTB="%{_localstatedir}/spool/cron/asterisk"
    if [ -f "$CTB" ]; then
        if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
            echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        fi


        if [ -z "`%{__grep} 'remove old csv exports more than 2 days old' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
            echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_external_dnc $CTB`" ]; then
            echo -e "" >> $CTB
            echo "### (sql) Actual Scrub against external DNC" >> $CTB
            echo "* * * * * %{_opt}/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
            echo -e "" >> $CTB
            echo "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
            echo "0 1 * * * %{_opt}/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_CLEAR_auto_calls $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_flush_DBqueue $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
            echo -e "11,41 * * * * %{_opt}/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_cleanup_agent_log $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
            echo -e "33 * * * * %{_opt}/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_VDhopper $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) updater for OSDial hopper" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_adjust_GMTnow_on_leads $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
            echo -e "1 1,7 * * * %{_opt}/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_DB_optimize $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
            echo -e "3 1 * * * %{_opt}/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_agent_week $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
            echo -e "#2 0 * * 0 %{_opt}/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
            echo -e "#22 0 * * * %{_opt}/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_VDsales_export $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
            echo -e "#32 0 * * * %{_opt}/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
            echo -e "#42 0 * * * %{_opt}/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
        fi

        if [ -z "`%{__grep} osdial_astgen $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_ivr_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} VMnow $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
            echo -e "* * * * * %{_sbindir}/asterisk -rx \"show voicemail users\" > %{_opt}/osdial/html/admin/VMnow.txt" >> $CTB
        fi
        if [ -z "`%{__grep} AST_manager_kill_hung_congested $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_vm_update $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) updater for voicemail" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_conf_update $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) updater for conference validator" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_reset_mysql_vars $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
            echo -e "2 1 * * * %{_opt}/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old recordings more than' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
            echo -e "24 0 * * * %{_bindir}/find %{_localstatedir}/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old recording backups' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old recording backups" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_opt}/osdial/backups/recordings -maxdepth 1 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old tts cache' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old tts cache" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_opt}/osdial/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old asterisk tts cache' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old asterisk tts cache" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_sharedstatedir}/asterisk/sounds/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_audio_archive $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Send Recordings to archive server" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial-astsnds-ramfs.sh $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
        fi

        if [ -z "`%{__grep} AST_audio_compress $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (archive) Compress wav files to mp3" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_compress.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_sort_recordings $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (archive) Sort MP3s into campaign_id/date directory structure" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_sort_recordings.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_qc_transfer $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (archive) Send select MP3s to a third-party quality-control or offste archive server." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/AST_qc_transfer.pl > /dev/null 2>&1" >> $CTB
        fi

    else
        touch $CTB > /dev/null 2>&1 || :
        chown asterisk:asterisk $CTB > /dev/null 2>&1 || :
        echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
        echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Actual Scrub against external DNC" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
        echo -e "0 1 * * * %{_opt}/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
        echo -e "11,41 * * * * %{_opt}/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
        echo -e "33 * * * * %{_opt}/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) updater for OSDial hopper" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
        echo -e "1 1,7 * * * %{_opt}/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
        echo -e "3 1 * * * %{_opt}/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
        echo -e "#32 0 * * * %{_opt}/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
        echo -e "#42 0 * * * %{_opt}/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
        echo -e "#2 0 * * 0 %{_opt}/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
        echo -e "#22 0 * * * %{_opt}/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
        echo -e "* * * * * %{_sbindir}/asterisk -rx \"show voicemail users\" > %{_opt}/osdial/html/admin/VMnow.txt" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) updater for voicemail" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) updater for conference validator" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
        echo -e "2 1 * * * %{_opt}/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_localstatedir}/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) remove old recording backups" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/backups/recordings -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Send Recordings to archive server" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (archive) Compress wav files to mp3" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_compress.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (archive) Sort MP3s into campaign_id/date directory structure" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_sort_recordings.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (archive) Send select MP3s to a third-party quality-control or offste archive server." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/AST_qc_transfer.pl > /dev/null 2>&1" >> $CTB
    fi
    kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n

%post profile-control
INTY=$1
if [ "$INTY" -ge 1 ]; then
    CTB="%{_localstatedir}/spool/cron/asterisk"
    if [ -f "$CTB" ]; then
        if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
            echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        fi

        if [ -z "`%{__grep} 'remove old csv exports more than 2 days old' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
            echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_external_dnc $CTB`" ]; then
            echo -e "" >> $CTB
            echo "### (sql) Actual Scrub against external DNC" >> $CTB
            echo "* * * * * %{_opt}/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
            echo -e "" >> $CTB
            echo "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
            echo "0 1 * * * %{_opt}/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_CLEAR_auto_calls $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_flush_DBqueue $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
            echo -e "11,41 * * * * %{_opt}/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_cleanup_agent_log $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
            echo -e "33 * * * * %{_opt}/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_VDhopper $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) updater for OSDial hopper" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_adjust_GMTnow_on_leads $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
            echo -e "1 1,7 * * * %{_opt}/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_DB_optimize $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
            echo -e "3 1 * * * %{_opt}/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_agent_week $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
            echo -e "#2 0 * * 0 %{_opt}/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
            echo -e "#22 0 * * * %{_opt}/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_VDsales_export $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
            echo -e "#32 0 * * * %{_opt}/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
            echo -e "#42 0 * * * %{_opt}/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
        fi
    else
        touch $CTB > /dev/null 2>&1 || :
        chown asterisk:asterisk $CTB > /dev/null 2>&1 || :
        echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
        echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Actual Scrub against external DNC" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
        echo -e "0 1 * * * %{_opt}/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
        echo -e "11,41 * * * * %{_opt}/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
        echo -e "33 * * * * %{_opt}/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) updater for OSDial hopper" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
        echo -e "1 1,7 * * * %{_opt}/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
        echo -e "3 1 * * * %{_opt}/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
        echo -e "#32 0 * * * %{_opt}/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
        echo -e "#42 0 * * * %{_opt}/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
        echo -e "#2 0 * * 0 %{_opt}/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
        echo -e "#22 0 * * * %{_opt}/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
    fi
    kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n

%post profile-sql
INTY=$1
if [ "$INTY" -ge 1 ]; then
    CTB="%{_localstatedir}/spool/cron/asterisk"
    if [ -f "$CTB" ]; then
        if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
            echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        fi

        if [ -z "`%{__grep} 'remove old csv exports more than 2 days old' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
            echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_external_dnc $CTB`" ]; then
            echo -e "" >> $CTB
            echo "### (sql) Actual Scrub against external DNC" >> $CTB
            echo "* * * * * %{_opt}/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
            echo -e "" >> $CTB
            echo "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
            echo "0 1 * * * %{_opt}/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_CLEAR_auto_calls $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_flush_DBqueue $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
            echo -e "11,41 * * * * %{_opt}/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_cleanup_agent_log $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
            echo -e "33 * * * * %{_opt}/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_VDhopper $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) updater for OSDial hopper" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_adjust_GMTnow_on_leads $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
            echo -e "1 1,7 * * * %{_opt}/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_DB_optimize $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
            echo -e "3 1 * * * %{_opt}/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_agent_week $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
            echo -e "#2 0 * * 0 %{_opt}/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
            echo -e "#22 0 * * * %{_opt}/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_VDsales_export $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
            echo -e "#32 0 * * * %{_opt}/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
            echo -e "#42 0 * * * %{_opt}/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
        fi
    else
        touch $CTB > /dev/null 2>&1 || :
        chown asterisk:asterisk $CTB > /dev/null 2>&1 || :
        echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
        echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) remove old csv exports more than 2 days old" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/html/admin -name 'advsearch*' -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Actual Scrub against external DNC" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_external_dnc.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Schedule ALL to scrub against external DNC" >> $CTB
        echo -e "0 1 * * * %{_opt}/osdial/bin/osdial_external_dnc.pl --sched=ALL > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) Clean out auto-calls regularly" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_CLEAR_auto_calls.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) flush queue DB table every hour for entries older than 1 hour" >> $CTB
        echo -e "11,41 * * * * %{_opt}/osdial/bin/AST_flush_DBqueue.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) fix the osdial_agent_log once every hour" >> $CTB
        echo -e "33 * * * * %{_opt}/osdial/bin/AST_cleanup_agent_log.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) updater for OSDial hopper" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_VDhopper.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) adjust the GMT offset for the leads in the osdial_list table" >> $CTB
        echo -e "1 1,7 * * * %{_opt}/osdial/bin/ADMIN_adjust_GMTnow_on_leads.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) optimize the database tables within the asterisk database" >> $CTB
        echo -e "3 1 * * * %{_opt}/osdial/bin/AST_DB_optimize.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) OSDial campaign export scripts (OPTIONAL)" >> $CTB
        echo -e "#32 0 * * * %{_opt}/osdial/bin/AST_VDsales_export.pl > /dev/null 2>&1" >> $CTB
        echo -e "#42 0 * * * %{_opt}/osdial/bin/AST_sourceID_summary_export.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (sql) OSDial agent time log weekly and daily summary report generation" >> $CTB
        echo -e "#2 0 * * 0 %{_opt}/osdial/bin/AST_agent_week.pl > /dev/null 2>&1" >> $CTB
        echo -e "#22 0 * * * %{_opt}/osdial/bin/AST_agent_day.pl > /dev/null 2>&1" >> $CTB
    fi
    kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n

%post profile-web
INTY=$1
if [ "$INTY" -ge 1 ]; then
    CTB="%{_localstatedir}/spool/cron/asterisk"
    if [ -f "$CTB" ]; then
        if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
            echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        fi
    else
        touch $CTB > /dev/null 2>&1 || :
        chown asterisk:asterisk $CTB > /dev/null 2>&1 || :
        echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
        echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
    fi
    kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n

%post profile-dialer
INTY=$1
if [ "$INTY" -ge 1 ]; then
    CTB="%{_localstatedir}/spool/cron/asterisk"
    if [ -f "$CTB" ]; then
        if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
            echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        fi

        if [ -z "`%{__grep} osdial_astgen $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_ivr_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} VMnow $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
            echo -e "* * * * * %{_sbindir}/asterisk -rx \"show voicemail users\" > %{_opt}/osdial/html/admin/VMnow.txt" >> $CTB
        fi
        if [ -z "`%{__grep} AST_manager_kill_hung_congested $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_vm_update $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) updater for voicemail" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_conf_update $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) updater for conference validator" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_reset_mysql_vars $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
            echo -e "2 1 * * * %{_opt}/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old recordings more than' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
            echo -e "24 0 * * * %{_bindir}/find %{_localstatedir}/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old recording backups' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old recording backups" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_opt}/osdial/backups/recordings -maxdepth 1 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old tts cache' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old tts cache" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_opt}/osdial/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old asterisk tts cache' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old asterisk tts cache" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_sharedstatedir}/asterisk/sounds/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_audio_archive $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Send Recordings to archive server" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial-astsnds-ramfs.sh $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
        fi
    else
        touch $CTB > /dev/null 2>&1 || :
        chown asterisk:asterisk $CTB > /dev/null 2>&1 || :
        echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
        echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
        echo -e "* * * * * %{_sbindir}/asterisk -rx \"show voicemail users\" > %{_opt}/osdial/html/admin/VMnow.txt" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) updater for voicemail" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) updater for conference validator" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
        echo -e "2 1 * * * %{_opt}/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_localstatedir}/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) remove old recording backups" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/backups/recordings -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Send Recordings to archive server" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
    fi
    kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n

%post profile-dialer-web
INTY=$1
if [ "$INTY" -ge 1 ]; then
    CTB="%{_localstatedir}/spool/cron/asterisk"
    if [ -f "$CTB" ]; then
        if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
            echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        fi

        if [ -z "`%{__grep} osdial_astgen $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_ivr_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} VMnow $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
            echo -e "* * * * * %{_sbindir}/asterisk -rx \"show voicemail users\" > %{_opt}/osdial/html/admin/VMnow.txt" >> $CTB
        fi
        if [ -z "`%{__grep} AST_manager_kill_hung_congested $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_vm_update $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) updater for voicemail" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_conf_update $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) updater for conference validator" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_reset_mysql_vars $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
            echo -e "2 1 * * * %{_opt}/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old recordings more than' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
            echo -e "24 0 * * * %{_bindir}/find %{_localstatedir}/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old recording backups' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old recording backups" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_opt}/osdial/backups/recordings -maxdepth 1 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old tts cache' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old tts cache" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_opt}/osdial/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} 'remove old asterisk tts cache' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) remove old asterisk tts cache" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_sharedstatedir}/asterisk/sounds/tts -maxdepth 1 -type f -mtime +14 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_audio_archive $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Send Recordings to archive server" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial-astsnds-ramfs.sh $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
        fi
    else
        touch $CTB > /dev/null 2>&1 || :
        chown asterisk:asterisk $CTB > /dev/null 2>&1 || :
        echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
        echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        echo -e "### (dialer) Generate asterisk config files and reload modules" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_astgen.pl -q > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Syncronize IVR recordings, arg is web server" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/osdial_ivr_sync.sh 127.0.0.1 > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Write a file listing current voicemail" >> $CTB
        echo -e "* * * * * %{_sbindir}/asterisk -rx \"show voicemail users\" > %{_opt}/osdial/html/admin/VMnow.txt" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) kill Hangup script for Asterisk updaters" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_manager_kill_hung_congested.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) updater for voicemail" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_vm_update.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) updater for conference validator" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_conf_update.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) reset several temporary-info tables in the database" >> $CTB
        echo -e "2 1 * * * %{_opt}/osdial/bin/AST_reset_mysql_vars.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) remove old recordings more than 7 days old" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_localstatedir}/spool/asterisk/monitor -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) remove old recording backups" >> $CTB
        echo -e "24 0 * * * %{_bindir}/find %{_opt}/osdial/backups/recordings -maxdepth 2 -type f -mtime +7 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Send Recordings to archive server" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_archive.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (dialer) Process to increase Asterisk performance by placing sounds on RAMFS." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial-astsnds-ramfs.sh > /dev/null 2>&1" >> $CTB
    fi
    kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n

%post profile-archive
INTY=$1
if [ "$INTY" -eq 1 ]; then
    /sbin/chkconfig vsftpd on > /dev/null 2>&1
    %{__ln_s} -f %{_opt}/osdial/recordings %{_sharedstatedir}/asterisk/recordings > /dev/null 2>&1
    %{__ln_s} -f %{_opt}/osdial/reports %{_sharedstatedir}/asterisk/reports > /dev/null 2>&1
fi
if [ "$INTY" -ge 1 ]; then
    CTB="%{_localstatedir}/spool/cron/asterisk"
    if [ -f "$CTB" ]; then
        if [ -z "`%{__grep} 'remove old osdial logs' $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
            echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
            echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} ADMIN_keepalive_ALL $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} osdial_media_sync $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        fi

        if [ -z "`%{__grep} AST_audio_compress $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (archive) Compress wav files to mp3" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_compress.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_sort_recordings $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (archive) Sort MP3s into campaign_id/date directory structure" >> $CTB
            echo -e "* * * * * %{_opt}/osdial/bin/AST_sort_recordings.pl > /dev/null 2>&1" >> $CTB
        fi
        if [ -z "`%{__grep} AST_qc_transfer $CTB`" ]; then
            echo -e "" >> $CTB
            echo -e "### (archive) Send select MP3s to a third-party quality-control or offste archive server." >> $CTB
            echo -e "*/15 * * * * %{_opt}/osdial/bin/AST_qc_transfer.pl > /dev/null 2>&1" >> $CTB
        fi
    else
        touch $CTB > /dev/null 2>&1 || :
        chown asterisk:asterisk $CTB > /dev/null 2>&1 || :
        echo -e "### (ALL) keepalive script for osdial processes" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/ADMIN_keepalive_ALL.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) remove old osdial logs and asterisk logs more than 2 days old" >> $CTB
        echo -e "28 0 * * * %{_bindir}/find %{_localstatedir}/log/osdial -maxdepth 1 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "29 0 * * * %{_bindir}/find %{_localstatedir}/log/asterisk -maxdepth 3 -type f -mtime +2 -print | xargs %{__rm} -f > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (ALL) Syncronize media files with osdial_media_sync." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/osdial_media_sync.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (archive) Compress wav files to mp3" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_audio_compress.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (archive) Sort MP3s into campaign_id/date directory structure" >> $CTB
        echo -e "* * * * * %{_opt}/osdial/bin/AST_sort_recordings.pl > /dev/null 2>&1" >> $CTB
        echo -e "" >> $CTB
        echo -e "### (archive) Send select MP3s to a third-party quality-control or offste archive server." >> $CTB
        echo -e "*/15 * * * * %{_opt}/osdial/bin/AST_qc_transfer.pl > /dev/null 2>&1" >> $CTB
    fi
    kill -1 `ps -ef | %{__grep} crond | head -1 | %{__awk} '{ print $2 }'` > /dev/null 2>&1 || :
fi
echo -n


%post -n slingdial
[ -d "%{_sharedstatedir}/mysql/osdial" ] && echo "UPDATE system_settings SET admin_template='SlingDial',agent_template='SlingDial';" | %{_bindir}/mysql osdial || :
echo -n

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
%defattr(644,asterisk,asterisk,755)
%dir %attr(0777,asterisk,asterisk) %{_opt}/osdial/tts
%dir %attr(0755,asterisk,asterisk) %{_opt}/osdial/backups
%dir %attr(0755,asterisk,asterisk) %{_opt}/osdial/backups/recordings
%dir %attr(0777,asterisk,asterisk) %{_opt}/osdial/media

%files sql
%defattr(644,asterisk,asterisk,755)
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
%attr(0644,asterisk,asterisk) %config(noreplace) %{_sysconfdir}/asterisk/http.conf
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
%attr(0644,asterisk,asterisk) %{_sysconfdir}/asterisk/README.osdial
%dir %attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/sounds/ivr
%dir %attr(0777,asterisk,asterisk) %{_sharedstatedir}/asterisk/sounds/osdial
%dir %attr(0777,asterisk,asterisk) %{_sharedstatedir}/asterisk/sounds/tts
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDagent_conf.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDamd.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDamd_post.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/VD_auto_post_VERIFY.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDAD_ALL_inbound.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDAD_LB_transfer.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDAD_LO_transfer.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDAD_pin_IVR.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADautoREMINDER.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADautoREMINDERxfer.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDfixCXFER.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADinbound_NI_DNC_CIDlookup.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADselective_CID.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADselective_CID_hangup.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADtransfer.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADtransferBROADCAST.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADtransferSURVEY.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-VDADtransferTEST.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDoutboundIVR.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDoutbound.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDivr.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDivr-old.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDvmail_finder.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDtts.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDdtmf.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-record_prompts.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDstation_spy.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDstation_spy_prompted.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/call_inbound.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/debug_speak.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/invalid_speak.agi
%attr(0755,asterisk,asterisk) %{_sharedstatedir}/asterisk/agi-bin/agi-OSDpark.agi
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
%dir %attr(0755,asterisk,asterisk) %{_localstatedir}/log/osdial
%attr(0644,root,root) %config(noreplace) %{_sysconfdir}/security/limits.d/99-osdial.conf
%attr(0644,root,root) %config(noreplace) /root/.mc/ini
%attr(0644,root,root) %config(noreplace) /root/.mc/panels.ini
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
%attr(0775,apache,asterisk) %dir %{_opt}/osdial/html/phpsysinfo/plugins
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
%attr(0644,root,root) %{_sysconfdir}/pki/osdial-support/osdial-support.pub
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
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/AUTHORS
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/ChangeLog
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/COPYING
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/NEWS
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/README
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/TODO
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/ISUP_codes.txt
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
%doc %attr(0644,root,root) %{_docdir}/osdial-%{version}/conf_examples/http.conf
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_parkstats.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonVDAD.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/AST_timeonpark.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/admin.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/dbconnect.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/getmedia.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/help.png
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/help.gif
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader.pl
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/listloaderMAIN.php
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader_rowdisplay.pl
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/listloader_super.pl
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/remote_dispo.php
%attr(0775,apache,asterisk) %{_opt}/osdial/html/admin/spreadsheet_sales_viewer.pl
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/vdremote.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/osdial_sales_viewer.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/voice_lab.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/vtiger_search.php
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/recycle.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/campaigns/status.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/filters/filters.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/filters/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/ingroups/iframe.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/ingroups/ingroups.php
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/server_performance.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/usergroup_hourly.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/reports/web_admin_log.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/content/admin/remoteagent.php
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
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/help.xml
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/help.xsd
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/includes.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/init.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/menu.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/validation.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/include/variables.php
%attr(0664,apache,asterisk) %{_opt}/osdial/html/admin/index.php
%attr(0664,apache,asterisk) %config(noreplace) %{_opt}/osdial/html/admin/admin_changes_log.txt
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

#%files debuginfo
#%defattr(644,root,root,755)
#%{_usr}/lib/debug%{_opt}/osdial/bin/ip_relay.debug

%changelog
* Fri Apr 19 2013 Lott Caskey <lottc@fugitol.com> 3.0.0.096
- Release 3.0.0

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
