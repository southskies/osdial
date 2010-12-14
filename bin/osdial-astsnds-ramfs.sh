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

if [ -d "${ASTDIR}/sounds.ramfs" -a -d "${ASTDIR}/sounds" -a ! -L "${ASTDIR}/sounds"]; then
	# Update our sound files on the harddisk and remove sounds directory..
	[ $DB -gt 0 ] && echo -e "\nMerge duplicate sounds directories." || :
	if [ -L "${ASTDIR}/sounds/sounds"]; then
		rm -f ${ASTDIR}/sounds/sounds
	fi
	if [ -d "${RAMDIR}/sounds" ]; then
		cp -rf ${RAMDIR}/sounds/* ${ASTDIR}/sounds.ramfs
		rm -rf ${RAMDIR}/sounds
	fi
	cp -rf ${ASTDIR}/sounds/* ${ASTDIR}/sounds.ramfs
	rm -rf ${ASTDIR}/sounds
	mv ${ASTDIR}/sounds.ramfs ${ASTDIR}/sounds
	if [ -d "${ASTDIR}/OSDprompts" ]; then
		cp ${ASTDIR}/OSDprompts/* ${ASTDIR}/sounds
	fi
fi

# RAMDIR is NOT mounted...we better put everything back.
if [ -z "$RAMMNT" ]; then
	if [ -d "${ASTDIR}/sounds.ramfs" ]; then
		# If sounds is a symlink, remove.
		[ -L "${ASTDIR}/sounds" ] && rm -f ${ASTDIR}/sounds || :

		# If sounds is a directory copy over it and remove sounds.ramfs
		if [ -d "${ASTDIR}/sounds" ]; then
			cp -rf ${ASTDIR}/sounds.ramfs/* ${ASTDIR}/sounds
			rm -rf ${ASTDIR}/sounds.ramfs

		# If sounds does not exist, move sounds.ramfs over it.
		elif [ ! -e "${ASTDIR}/sounds" ]; then
			mv ${ASTDIR}/sounds.ramfs ${ASTDIR}/sounds
		fi
		if [ -d "${ASTDIR}/OSDprompts" ]; then
			cp ${ASTDIR}/OSDprompts/* ${ASTDIR}/sounds
		fi
	fi

# RAMDIR is mounted...lets move the sounds to RAMFS.
elif [ -n "$RAMMNT" ]; then
	if [ ! -e "${ASTDIR}/sounds.ramfs" -a ! -e "${ASTDIR}/sounds" -a ! -e "${RAMDIR}/sounds" ]; then
		[ $DB -gt 0 ] && echo -e "\nERROR: Fatal error, all sounds directories missing.!\n" || :
		exit 1
	elif [ ! -d "${ASTDIR}/sounds.ramfs" -a -d "${ASTDIR}/sounds" ]; then
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
		mv ${ASTDIR}/sounds ${ASTDIR}/sounds.ramfs
		mkdir -p ${RAMDIR}/sounds
		if [ -d "${ASTDIR}/OSDprompts" ]; then
			cp ${ASTDIR}/OSDprompts/* ${ASTDIR}/sounds.ramdir
		fi
		cp -rf ${ASTDIR}/sounds.ramfs/* ${RAMDIR}/sounds
	elif [ -d "${ASTDIR}/sounds.ramfs" -a -d "${RAMDIR}/sounds" ]; then
		# Update our sound files on the harddisk.
		[ $DB -gt 0 ] && echo -e "\nUpdate sound files on HD." || :
		if [ -d "${ASTDIR}/OSDprompts" ]; then
			cp ${ASTDIR}/OSDprompts/* ${RAMDIR}/sounds
		fi
		cp -rf ${RAMDIR}/sounds/* ${ASTDIR}/sounds.ramfs
	elif [ -d "${ASTDIR}/sounds.ramfs" -a ! -d "${RAMDIR}/sounds" ]; then
		# Recover RAMFS sound files.
		[ $DB -gt 0 ] && echo -e "\nUpdate sound files on RAMFS." || :
		mkdir -p ${RAMDIR}/sounds
		if [ -d "${ASTDIR}/OSDprompts" ]; then
			cp ${ASTDIR}/OSDprompts/* ${ASTDIR}/sounds.ramfs
		fi
		cp -rf ${ASTDIR}/sounds.ramfs/* ${RAMDIR}/sounds
	fi
fi

# Clean-up
[ ! -L "${ASTDIR}/sounds" -a -d "${ASTDIR}/sounds.ramfs" -a -d "${RAMDIR}/sounds" ] && ln -s ${RAMDIR}/sounds ${ASTDIR}/sounds || :
[ -d "${ASTDIR}/sounds.ramfs" ] && chown -R asterisk:asterisk ${ASTDIR}/sounds.ramfs || :
[ -d "${RAMDIR}/sounds" ]       && chown -R asterisk:asterisk ${RAMDIR}/sounds || :
[ -d "${ASTDIR}/sounds" ]       && chown -R asterisk:asterisk ${ASTDIR}/sounds || :

[ $DB -gt 0 ] && echo -e "Done.\n" || :
