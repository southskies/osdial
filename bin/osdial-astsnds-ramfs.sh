#!/bin/bash

[ -n "$1" ] && DB=1 || DB=0

RAMDIR=/mnt/ramdisk
ASTDIR=/var/lib/asterisk

RAMMNT=`mount | grep "${RAMDIR}"`

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
	fi

# RAMDIR is mounted...lets move the sounds to RAMFS.
elif [ -n "$RAMMNT" ]; then
	if [ ! -e "${ASTDIR}/sounds.ramfs" -a ! -e "${ASTDIR}/sounds" -a ! -e "${RAMDIR}/sounds" ]; then
		[ $DB -gt 0 ] && echo -e "\nERROR: Fatal error, all sounds directories missing.!\n" || :
		exit 1
	elif [ ! -d "${ASTDIR}/sounds.ramfs" -a -d "${ASTDIR}/sounds" ]; then
		# Initial creation.
		[ $DB -gt 0 ] && echo -e "\nInitial move of sound files to RAMFS." || :
		mv ${ASTDIR}/sounds ${ASTDIR}/sounds.ramfs
		mkdir -p ${RAMDIR}/sounds
		cp -rf ${ASTDIR}/sounds.ramfs/* ${RAMDIR}/sounds
	elif [ -d "${ASTDIR}/sounds.ramfs" -a -d "${RAMDIR}/sounds" ]; then
		# Update our sound files on the harddisk.
		[ $DB -gt 0 ] && echo -e "\nUpdate sound files on HD." || :
		cp -rf ${RAMDIR}/sounds/* ${ASTDIR}/sounds.ramfs
	elif [ -d "${ASTDIR}/sounds.ramfs" -a ! -d "${RAMDIR}/sounds" ]; then
		# Recover RAMFS sound files.
		[ $DB -gt 0 ] && echo -e "\nUpdate sound files on RAMFS." || :
		mkdir -p ${RAMDIR}/sounds
		cp -rf ${ASTDIR}/sounds.ramfs/* ${RAMDIR}/sounds
	fi
fi

# Clean-up
[ ! -L "${ASTDIR}/sounds" -a -d "${ASTDIR}/sounds.ramfs" -a -d "${RAMDIR}/sounds" ] && ln -s ${RAMDIR}/sounds ${ASTDIR}/sounds || :
[ -d "${ASTDIR}/sounds.ramfs" ] && chown -R asterisk:asterisk ${ASTDIR}/sounds.ramfs || :
[ -d "${RAMDIR}/sounds" ]       && chown -R asterisk:asterisk ${RAMDIR}/sounds || :
[ -d "${ASTDIR}/sounds" ]       && chown -R asterisk:asterisk ${ASTDIR}/sounds || :

[ $DB -gt 0 ] && echo -e "Done.\n" || :
