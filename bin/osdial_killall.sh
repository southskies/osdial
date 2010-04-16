#!/bin/bash

[ -n "`ps -ef | grep FastAGI`" ] && kill -9 `ps -ef | grep FastAGI | awk '{ print $2 }'` > /dev/null 2>&1
[ -n "`ps -ef | grep AST`" ] && kill -9 `ps -ef | grep AST | awk '{ print $2 }'` > /dev/null 2>&1
[ -n "`ps -ef | grep OSD`" ] && kill -9 `ps -ef | grep AST | awk '{ print $2 }'` > /dev/null 2>&1
