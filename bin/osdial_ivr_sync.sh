#!/bin/bash

TMPIVR=/tmp/osdial_ivr_sync
IVRDIR=/var/lib/asterisk/sounds/ivr

WEB=$1

if [ -z "$WEB" ]; then
	WEB=127.0.0.1
fi

if [ ! -d "$IVRDIR" ]; then
	mkdir -p $IVRDIR
fi
if [ ! -d "$TMPIVR" ]; then
	mkdir $TMPIVR
fi
cd $TMPIVR
wget --mirror -nH http://$WEB/osdial/ivr > /dev/null 2>&1
if [ -d "$TMPIVR/osdial" ]; then
	cd osdial/ivr
else
	wget --mirror -nH http://$WEB/ivr > /dev/null 2>&1
	cd ivr
fi
for i in `ls *.wav *.WAV *.gsm *.GSM *.mp3 *.MP3`; do
	if [ ! -f "$IVRDIR/$i" ]; then
		cp -f "$i" $IVRDIR
	fi
done
