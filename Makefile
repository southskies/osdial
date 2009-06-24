#
# Makefile
#
#
## Copyright (C) 2008  Matt Florell <vicidial@gmail.com>      LICENSE: AGPLv2
## Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
##
##     This file is part of OSDial.
##
##     OSDial is free software: you can redistribute it and/or modify
##     it under the terms of the GNU Affero General Public License as
##     published by the Free Software Foundation, either version 3 of
##     the License, or (at your option) any later version.
##
##     OSDial is distributed in the hope that it will be useful,
##     but WITHOUT ANY WARRANTY; without even the implied warranty of
##     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
##     GNU Affero General Public License for more details.
##
##     You should have received a copy of the GNU Affero General Public
##     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
##
#
#
# CHANGES
# 71121-1430 - Initial release.
# 71130-2137 - Remove install-VDconfig section and replace with install-common.
# 71201-0358 - Use sh to run VDconfig.
# 71207-2138 - Update VDconfig location.
# 71208-0016 - Update to check for httpd user for settings perms of web stuff.
#            - This will default to "nobody" the apache default if not found.
# 71216-0026 - Create HTTPDUSER account if it doesn't exist. (always nobody?)
# 90310-0000 - Update for osdial files.
# 90406-2138 - Update for auto-asterisk-configs.
# 90406-2211 - Added ip-relay.
#
#
# methods:
#   make menuconfig
#      Runs graphical configuration.
#
#   make defaultconfig
#      Creates a default install config file.
#
#   make clean
#      Removes previous install config.
#
#   make install
#      Install Base, User Interface (web) and documents/samples.
#
#   make install-base
#      Only install executables and other base components.
#
#   make install-web
#      Only install User Interface (web) components.
#
#   make install-docs
#      Installs documents to /usr/share/doc/osdial-(version)
#
#   make install-asterisk-sample-config
#      Installs sample configs in /etc/asterisk, backing-up existing files.


.PHONY : defaultconfig menuconfig .install .install-base install-web install install-base install-web install-docs install-asterisk-sample-config clean .install-complete .install-common install-asterisk-sample-configs .install-docs
OSDconfig := bin/OSDconfig
version  := $(shell cat version)

HTTPDUSER := $(shell if [ "`id -u apache`" ]; then echo apache; elif [ "`id -u www-data`" ]; then echo www-data; else echo nobody; fi)

menuconfig:
	@sh $(OSDconfig)

clean:
	rm -f .osdial.config

.osdial.config:
	@echo "########################################################"
	@echo "###"
	@echo "### \".osdial.config\" file not found."
	@echo "### Running configuration and just using defaults."
	@echo "###"
	@echo "########################################################"
	@sh $(OSDconfig) --no-menu

defaultconfig: .osdial.config
	@sh $(OSDconfig) --no-menu

# The following, install, install-base and install-web shell-out to
# run OSDconfig which set the path variables and runs make with .install*
install: .osdial.config
	@sh $(OSDconfig) --env-make .$@

install-base: .osdial.config
	@sh $(OSDconfig) --env-make .$@

install-web: .osdial.config
	@sh $(OSDconfig) --env-make .$@

install-docs: .osdial.config
	@sh $(OSDconfig) --env-make .$@


# The following, .install, .install-base and .install-web are the actual
# methods used to install
.install: .install-base .install-web install-docs .install-common .install-complete

.install-common:
	@echo "Installing OSDconfig script..."
	@install -d -m 755 $(DESTDIR)$(PATHhome)
	@install -p -m 755 $(OSDconfig) $(DESTDIR)$(PATHhome)/OSDconfig
	@install -d -m 755 $(DESTDIR)/usr/bin
	@ln -fs $(PATHhome)/OSDconfig $(DESTDIR)/usr/bin/OSDconfig
	@echo "Installing OSDial configuration to $(DESTDIR)/etc/osdial.conf..."
	@install -d -m 755 $(DESTDIR)/etc
	@install -p -m 644 ./.osdial.config $(DESTDIR)/etc/osdial.conf
	@echo "Creating log directory $(DESTDIR)$(PATHlogs)..."
	@install -d -m 777 $(DESTDIR)$(PATHlogs)

.install-base: .install-common
	@echo "Installing OSDial base components..."
	@install -d -m 755 $(DESTDIR)$(PATHhome)
	@install -d -m 755 $(DESTDIR)$(PATHhome)/sql
	@install -d -m 755 $(DESTDIR)$(PATHhome)/libs
	@install -d -m 755 $(DESTDIR)$(PATHhome)/libs/Asterisk
	@install -d -m 755 $(DESTDIR)$(PATHhome)/LEADS_IN
	@install -d -m 755 $(DESTDIR)$(PATHhome)/LEADS_IN/DONE
	@install -d -m 755 $(DESTDIR)$(PATHmonitor)
	@install -d -m 755 $(DESTDIR)$(PATHDONEmonitor)
	@install -d -m 755 $(DESTDIR)$(PATHDONEmonitor)/ORIG
	@install -d -m 755 $(DESTDIR)$(PATHagi)
	@install -d -m 755 $(DESTDIR)/etc/openvpn
	@install -d -m 755 $(DESTDIR)$(PATHsounds)
	@install -p -m 755 ./bin/* $(DESTDIR)$(PATHhome)
	@install -p -m 644 ./extras/osdial.cron $(DESTDIR)$(PATHhome)
	@install -p -m 755 ./extras/ip_relay/ip_relay $(DESTDIR)$(PATHhome)
	@install -p -m 755 ./extras/perl/Asterisk.pm $(DESTDIR)$(PATHhome)/libs
	@install -p -m 755 ./extras/perl/Asterisk/* $(DESTDIR)$(PATHhome)/libs/Asterisk
	@install -p -m 644 ./extras/gmt/GMT_USA_zip.txt $(DESTDIR)$(PATHhome)
	@install -p -m 644 ./extras/gmt/phone_codes_GMT.txt $(DESTDIR)$(PATHhome)
	@install -p -m 644 ./extras/sql/* $(DESTDIR)$(PATHhome)/sql
	@install -p -m 755 ./agi/* $(DESTDIR)$(PATHagi)
	@install -p -m 644 ./sounds/* $(DESTDIR)$(PATHsounds)
	@install -p -m 644 ./extras/openvpn/* $(DESTDIR)/etc/openvpn
	@[ -d $(DESTDIR)/var/lib/mysql/osdial ] && /usr/bin/perl $(DESTDIR)$(PATHhome)/sql/upgrade_sql.pl || :

.install-web: .install-common
	@echo "Installing User-Interface (web) in $(DESTDIR)$(PATHweb)..."
	@install -d -m 777 $(DESTDIR)$(PATHweb)/agent
	@install -d -m 777 $(DESTDIR)$(PATHweb)/agent/include
	@install -d -m 755 $(DESTDIR)$(PATHweb)/agent/templates
	@install -d -m 755 $(DESTDIR)$(PATHweb)/agent/templates/default
	@install -d -m 755 $(DESTDIR)$(PATHweb)/agent/templates/default/images
	@install -d -m 777 $(DESTDIR)$(PATHweb)/admin
	@install -d -m 777 $(DESTDIR)$(PATHweb)/admin/ploticus
	@install -d -m 777 $(DESTDIR)$(PATHweb)/admin/agent_reports
	@install -d -m 777 $(DESTDIR)$(PATHweb)/admin/server_reports
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/admin
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/campaigns
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/filters
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/ingroups
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/lists
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/remoteagent
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/reports
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/remoteagent
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/scripts
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/usergroups
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/include/content/users
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/templates
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/templates/default
	@install -d -m 755 $(DESTDIR)$(PATHweb)/admin/templates/default/images
	@install -p -m 644 ./www/*.php $(DESTDIR)$(PATHweb)
	@install -p -m 644 ./www/agent/*.php $(DESTDIR)$(PATHweb)/agent
	@install -p -m 666 ./www/agent/*.txt $(DESTDIR)$(PATHweb)/agent
	@install -p -m 644 ./www/agent/include/* $(DESTDIR)$(PATHweb)/agent/include
	@install -p -m 644 ./www/agent/templates/default/*.css $(DESTDIR)$(PATHweb)/agent/templates/default
	@install -p -m 644 ./www/agent/templates/default/*.php $(DESTDIR)$(PATHweb)/agent/templates/default
	@install -p -m 644 ./www/agent/templates/default/images/* $(DESTDIR)$(PATHweb)/agent/templates/default/images
	@install -p -m 666 ./www/admin/*.txt $(DESTDIR)$(PATHweb)/admin
	@install -p -m 644 ./www/admin/*.php $(DESTDIR)$(PATHweb)/admin
	@install -p -m 755 ./www/admin/*.pl $(DESTDIR)$(PATHweb)/admin
	@install -p -m 644 ./www/admin/*.gif $(DESTDIR)$(PATHweb)/admin
	@install -p -m 644 ./www/admin/*.css $(DESTDIR)$(PATHweb)/admin
	@install -p -m 644 ./www/admin/templates/default/*.css $(DESTDIR)$(PATHweb)/admin/templates/default
	@install -p -m 644 ./www/admin/templates/default/*.php $(DESTDIR)$(PATHweb)/admin/templates/default
	@install -p -m 644 ./www/admin/templates/default/images/* $(DESTDIR)$(PATHweb)/admin/templates/default/images
	@install -p -m 644 ./www/admin/include/*.js $(DESTDIR)$(PATHweb)/admin/include
	@install -p -m 644 ./www/admin/include/*.php $(DESTDIR)$(PATHweb)/admin/include
	@install -p -m 644 ./www/admin/include/content/admin/* $(DESTDIR)$(PATHweb)/admin/include/content/admin
	@install -p -m 644 ./www/admin/include/content/campaigns/* $(DESTDIR)$(PATHweb)/admin/include/content/campaigns
	@install -p -m 644 ./www/admin/include/content/filters/* $(DESTDIR)$(PATHweb)/admin/include/content/filters
	@install -p -m 644 ./www/admin/include/content/ingroups/* $(DESTDIR)$(PATHweb)/admin/include/content/ingroups
	@install -p -m 644 ./www/admin/include/content/lists/* $(DESTDIR)$(PATHweb)/admin/include/content/lists
	@install -p -m 644 ./www/admin/include/content/remoteagent/* $(DESTDIR)$(PATHweb)/admin/include/content/remoteagent
	@install -p -m 644 ./www/admin/include/content/reports/* $(DESTDIR)$(PATHweb)/admin/include/content/reports
	@install -p -m 644 ./www/admin/include/content/scripts/* $(DESTDIR)$(PATHweb)/admin/include/content/scripts
	@install -p -m 644 ./www/admin/include/content/usergroups/* $(DESTDIR)$(PATHweb)/admin/include/content/usergroups
	@install -p -m 644 ./www/admin/include/content/users/* $(DESTDIR)$(PATHweb)/admin/include/content/users

install-asterisk-sample-configs: install-asterisk-sample-config

install-asterisk-sample-config:
	@echo "Installing sample configs in $(DESTDIR)/etc/asterisk..."
	@install -d -m 777 $(DESTDIR)/etc/asterisk
	@install -b -p -m 644 ./docs/conf_examples/*.conf $(DESTDIR)/etc/asterisk
	
.install-docs:
	@echo "Installing documents in $(DESTDIR)$(PATHdocs)..."
	@install -d -m 755 $(DESTDIR)$(PATHdocs)/conf_examples
	@install -p -m 644 ./docs/*.txt $(DESTDIR)$(PATHdocs)
	@install -p -m 644 ./docs/conf_examples/* $(DESTDIR)$(PATHdocs)/conf_examples

.install-complete:
	@echo
	@echo
	@echo
	@echo "################################################################################"
	@echo "##                                                                           ###"
	@echo "##                                                                           ###"
	@echo "##                                  OSDial                                   ###"
	@echo "##                           INSTALLATION COMPLETE                           ###"
	@echo "##                                                                           ###"
	@echo "##      Run 'make install-asterisk-sample-config' to install the sample      ###"
	@echo "##     configuration in /etc/asterisk.  This will will first backup any      ###"
	@echo "##     existing Asterisk configuration files.  These confiruration files     ###"
	@echo "##       are also located in '/usr/share/doc/osdial-(version)'.              ###"
	@echo "##                                                                           ###"
	@echo "##                                                                           ###"
	@echo "##                                                                           ###"
	@#echo "## You can run '/usr/bin/OSDconfig' to modify the configuration at any time. ###"
	@echo "################################################################################"
