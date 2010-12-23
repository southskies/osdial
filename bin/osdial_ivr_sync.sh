#!/bin/bash

TMPIVR=/tmp/osdial_ivr_sync
IVRDIR=/var/lib/asterisk/sounds/ivr

WEB=$1

if [ -z "$WEB" ]; then
	WEB=127.0.0.1
fi

if [ ! -d "$IVRDIR" ]; then
	mkdir -p $IVRDIR > /dev/null 2>&1
fi
if [ ! -d "$TMPIVR" ]; then
	mkdir $TMPIVR > /dev/null 2>&1
fi
cd $TMPIVR
wget --mirror -nH http://$WEB/osdial/ivr > /dev/null 2>&1
if [ -d "$TMPIVR/osdial" ]; then
	cd osdial/ivr
else
	wget --mirror -nH http://$WEB/ivr > /dev/null 2>&1
	cd ivr
fi
for i in `ls *.wav 2>/dev/null`; do
	fileNE=`echo $i | awk '{ sub(/.wav$/, ""); print }'`
	if [ ! -f "$IVRDIR/$i" ]; then
		cp -f "$i" $IVRDIR > /dev/null 2>&1
	fi
done
for i in `ls *.gsm 2>/dev/null`; do
	fileNE=`echo $i | awk '{ sub(/.gsm$/, ""); print }'`
	if [ ! -f "$IVRDIR/$i" ]; then
		cp -f "$i" $IVRDIR > /dev/null 2>&1
	fi
done
for i in `ls *.mp3 2>/dev/null`; do
	fileNE=`echo $i | awk '{ sub(/.mp3$/, ""); print }'`
	if [ ! -f "$IVRDIR/$i" ]; then
		cp -f "$i" $IVRDIR > /dev/null 2>&1
		/usr/sbin/asterisk -rx "file convert $IVRDIR/$i $IVRDIR/$fileNE.sln16" > /dev/null 2>&1
		/usr/sbin/asterisk -rx "file convert $IVRDIR/$i $IVRDIR/$fileNE.ulaw" > /dev/null 2>&1
		/usr/sbin/asterisk -rx "file convert $IVRDIR/$i $IVRDIR/$fileNE.gsm" > /dev/null 2>&1
		/usr/sbin/asterisk -rx "file convert $IVRDIR/$i $IVRDIR/$fileNE.wav" > /dev/null 2>&1
	fi
done
