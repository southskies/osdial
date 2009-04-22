#!/bin/bash

TMPIVR=/tmp/osdial_ivr_sync

WEB=$1

if [ -z "$WEB" ]; then
	WEB=127.0.0.1
fi

if [ ! -d "$TMPIVR" ]; then
	mkdir $TMPIVR
fi
cd $TMPIVR
wget --mirror -nH http://$WEB/osdial/ivr > /dev/null 2>&1
cd osdial/ivr
for i in `ls *.wav *.WAV *.gsm *.GSM *.mp3 *.MP3`; do
	if [ ! -f "/var/lib/asterisk/sounds/ivr/$i" ]; then
		cp -f "$i" /var/lib/asterisk/sounds/ivr
	fi
done
