#!/bin/bash

[ -n "`ps -ef | grep FastAGI`" ] && kill -9 `ps -ef | grep FastAGI | awk '{ print $2 }'` > /dev/null 2>&1
[ -n "`ps -ef | grep AST`" ] && kill -9 `ps -ef | grep AST | awk '{ print $2 }'` > /dev/null 2>&1
[ -n "`ps -ef | grep OSD`" ] && kill -9 `ps -ef | grep OSD | awk '{ print $2 }'` > /dev/null 2>&1
[ -n "`ps -ef | grep '/usr/bin/python /opt/osdial/bin/osdial_'`" ] && kill -9 `ps -ef | grep '/usr/bin/python /opt/osdial/bin/osdial_' | awk '{ print $2 }'` > /dev/null 2>&1
