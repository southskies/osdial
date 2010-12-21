#!/bin/bash
#
#  osdial-astsnds-ramfs.sh: Script used move Asterisk sounds files
#                           to RAMFS, maintain, and synchronize them.
#
#  Copyright (C) 2010  Lott Caskey  <lottcaskey@gmail.com>    LICENSE: AGPLv3
#
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
#


[ -n "$1" ] && DB=1 || DB=0

RAMDIR=/mnt/ramdisk
ASTDIR=/var/lib/asterisk

RAMMNT=`mount | grep "${RAMDIR}"`

if [ -d "${ASTDIR}/sounds.ramfs" ]; then
	# Update our sound files on the harddisk and remove sounds directory..
	[ $DB -gt 0 ] && echo -e "\nMerge duplicate sounds directories." || :
	[ -L "${ASTDIR}/sounds/sounds" ] && rm -f ${ASTDIR}/sounds/sounds || :
	[ -L "${ASTDIR}/sounds" ] && rm -f ${ASTDIR}/sounds || :
	if [ -d "${RAMDIR}/sounds" ]; then
		yes | cp -auf ${RAMDIR}/sounds/* ${ASTDIR}/sounds.ramfs
		rm -rf ${RAMDIR}/sounds
	fi
	yes | cp -auf ${ASTDIR}/sounds/* ${ASTDIR}/sounds.ramfs
	rm -rf ${ASTDIR}/sounds
	mv ${ASTDIR}/sounds.ramfs ${ASTDIR}/sounds
	[ -d "${ASTDIR}/OSDprompts" ] && yes | cp -auf ${ASTDIR}/OSDprompts/* ${ASTDIR}/sounds || :
fi

# RAMDIR is mounted...lets move the sounds to RAMFS.
if [ -n "$RAMMNT" ]; then
	if [ ! -e "${ASTDIR}/sounds" -a ! -e "${RAMDIR}/sounds" ]; then
		[ $DB -gt 0 ] && echo -e "\nERROR: Fatal error, all sounds directories missing.!\n" || :
		exit 1
	elif [ -d "${ASTDIR}/sounds" -a ! -d "${RAMDIR}/sounds" ]; then
		# Check for available space.
		[ $DB -gt 0 ] && echo -e "\nChecking for available/required memory." || :
		RAMAVAIL=`df -k | grep "${RAMDIR}" | awk '{ print $4 }'`
		let RAMAVAIL+=0
		RAMREQ=`du -sk ${ASTDIR}/sounds/| awk '{ print $1 }'`
		let RAMREQ+=1024*256
		[ $DB -gt 0 ] && echo -e "\nAvailable RAMFS Space: ${RAMAVAIL}k   Required RAMFS Space: ${RAMREQ}k" || :
		if [ $RAMREQ -gt $RAMAVAIL ]; then
			[ $DB -gt 0 ] && echo -e "\nERROR: There is not enough available memory to use this function.!\n" || :
			exit 1
		fi
		# Initial creation.
		[ $DB -gt 0 ] && echo -e "\nMoving sound files to RAMFS." || :
		if [ -d "${ASTDIR}/OSDprompts" ]; then
			yes | cp -auf ${ASTDIR}/OSDprompts/* ${ASTDIR}/sounds
			yes | cp -auf ${ASTDIR}/sounds/851* ${ASTDIR}/OSDprompts
		fi
		mkdir -p ${RAMDIR}/sounds
		yes | cp -auf ${ASTDIR}/sounds/* ${RAMDIR}/sounds
	elif [ -d "${ASTDIR}/sounds" -a -d "${RAMDIR}/sounds" ]; then
		# Update our sound files on the harddisk.
		[ $DB -gt 0 ] && echo -e "\nUpdate sound files on HD." || :
		if [ -d "${ASTDIR}/OSDprompts" ]; then
			yes | cp -auf ${ASTDIR}/OSDprompts/* ${ASTDIR}/sounds
			yes | cp -auf ${ASTDIR}/sounds/851* ${ASTDIR}/OSDprompts
		fi
		yes | cp -auf ${ASTDIR}/sounds/* ${RAMDIR}/sounds
		yes | cp -auf ${RAMDIR}/sounds/* ${ASTDIR}/sounds
	fi
fi

# Clean-up
[ -d "${ASTDIR}/sounds" ] &&     chown -R asterisk:asterisk ${ASTDIR}/sounds || :
[ -d "${ASTDIR}/OSDprompts" ] && chown -R asterisk:asterisk ${ASTDIR}/OSDprompts || :
[ -d "${RAMDIR}/sounds" ] &&     chown -R asterisk:asterisk ${RAMDIR}/sounds || :

[ $DB -gt 0 ] && echo -e "Done.\n" || :
