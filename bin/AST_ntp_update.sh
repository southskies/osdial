#!/bin/bash

#
# Copyright (C) 2009  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
#
#     This file is part of OSDial.
#
#     OSDial is free software: you can redistribute it and/or modify
#     it under the terms of the GNU Affero General Public License as
#     published by the Free Software Foundation, either version 3 of
#     the License, or (at your option) any later version.
#
#     OSDial is distributed in the hope that it will be useful,
#     but WITHOUT ANY WARRANTY; without even the implied warranty of
#     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#     GNU Affero General Public License for more details.
#
#     You should have received a copy of the GNU Affero General Public
#     License along with OSDial.  If not, see <http://www.gnu.org/licenses/>.
#

SERVER1="ntp.myfloridacity.us"
SERVER2="ntp-s1.cise.ufl.edu"
SERVER3="navobs1.gatech.edu"
SERVER4="tick.usno.navy.mil"
SERVER5="time-nw.nist.gov"
OPTS="-s -u"

/usr/sbin/ntpdate $OPTS $SERVER1
RETVAL=$?
if [ $RETVAL -ne 0 ]; then
	/usr/sbin/ntpdate $OPTS $SERVER2
	RETVAL=$?
	if [ $RETVAL -ne 0 ]; then
		/usr/sbin/ntpdate $OPTS $SERVER3
		RETVAL=$?
		if [ $RETVAL -ne 0 ]; then
			/usr/sbin/ntpdate $OPTS $SERVER4
			RETVAL=$?
			if [ $RETVAL -ne 0 ]; then
				/usr/sbin/ntpdate $OPTS $SERVER5
			fi
		fi
	fi
fi
