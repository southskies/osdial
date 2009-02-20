#!/bin/bash

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
