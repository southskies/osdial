#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, MySQLdb, MySQLdb.cursors

class OSDialSQL(object):
    osdial = None
    db = None
    cur = None
    def __init__(self,osd,opts={}):
        self.osdial = osd
        self.open(opts)
    def open(self, opts={}):
        if opts.has_key('host') is False:
            opts['host'] = self.osdial.VARDB_server
        if opts.has_key('port') is False:
            opts['port'] = self.osdial.VARDB_port
        if opts.has_key('user') is False:
            opts['user'] = self.osdial.VARDB_user
        if opts.has_key('passwd') is False:
            opts['passwd'] = self.osdial.VARDB_pass
        if opts.has_key('db') is False:
            opts['db'] = self.osdial.VARDB_database
        self.db = MySQLdb.connect(host=opts['host'], port=int(opts['port']), user=opts['user'], passwd=opts['passwd'], db=opts['db'], cursorclass=MySQLdb.cursors.DictCursor)
        self.db.autocommit(True)
        self.db.ping(True)
        self.cur = self.db.cursor()
    def close(self):
        if not self.cur is None:
            self.cur.close()
            self.cur = None
        if not self.db is None:
            self.db.close()
            self.db = None
    def __call__(self, *args, **kwargs):
        return self.cur

