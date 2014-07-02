#
# _osdial.py
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#
"""
osdial - Main object class
"""

import sys, os, re, pprint
from osdial.sql import OSDialSQL
from asterisk.agi import AGI as OSDAGI

class OSDial(object):
    vars = {}
    _agi = None
    _sql = None

    def __init__(self, option={}):
        for opt in option:
            self.vars[opt] = option[opt]
        if self.vars.has_key('DB') is False:
            self.vars['DB'] = 0
        if self.vars.has_key('PATHconf') is False:
            self.vars['PATHconf'] = '/etc/osdial.conf'
        self._loadConfig()
        for opt in option:
            self.vars[opt] = option[opt]
        self._sql = OSDialSQL(self, option)
        self._loadSQLConfig()

    def _loadConfig(self):
        file=open(self.vars['PATHconf'],'r')
        row = file.readlines()
        for line in row:
            test = re.sub(r'(\s|>|"|\'|\n|\r|\t|\#.*|;.*)', '', line)
            if test:
                key, data = re.split('=|:', test, 1)
                self.vars[key] = data

    def _loadSQLConfig(self):
        self.sql().execute("SELECT * FROM system_settings LIMIT 1;")
        for row in self.sql().fetchall():
            self.vars['settings'] = {}
            for col in row:
                self.vars['settings'][col] = row[col]

        self.sql().execute("SELECT * FROM servers WHERE server_ip=%s LIMIT 1;", (self.vars['VARserver_ip']))
        for row in self.sql().fetchall():
            self.vars['server'] = {}
            for col in row:
                self.vars['server'][col] = row[col]

        self.sql().execute("SELECT name,data FROM configuration WHERE fk_id='';")
        self.vars['configuration'] = {}
        for row in self.sql().fetchall():
            self.vars['configuration'][row['name']] = row['data']

    def close(self):
        if not self._sql is None:
            self._sql.close()
            self._sql = None
        if not self._agi is None:
            self._agi = None
        vars = {}

    def sql(self):
        if self._sql is None:
            self._sql = OSDSQL()
        return self._sql()

    def SQL(self):
        return self.sql()

    def agi(self):
        if self._agi is None:
            self._agi = OSDAGI()
        return self._agi

    def AGI(self):
        return self.agi()


    def __getattr__(self, name):
        return self.vars[name]

    def __setattr__(self, name, value):
        self.__dict__[name] = value


    def is_process_running(self,process_id):
        try:
            os.kill(process_id, 0)
            return True
        except OSError:
            return False

    def server_process_tracker(self, prog, server_ip, pid, allow_multiple):
        pcount = 0
        ret = True
        procs = {}
        self.sql().execute("SELECT id,name,server_ip,pid,IF(UNIX_TIMESTAMP(last_checkin)>UNIX_TIMESTAMP()-180 AND pid>0,1,0) AS is_alive FROM server_keepalive_processes WHERE name=%s ORDER BY last_checkin DESC;", (prog))
        for row in self.sql().fetchall():
            if procs.has_key(prog) is False:
                procs[prog] = {'id':row['id'], 'server_ip':row['server_ip'], 'pid':row['pid'], 'is_alive':row['is_alive']}
            pcount += 1
        if pcount == 0:
            self.sql().execute("SELECT id FROM server_keepalive_processes WHERE name=%s AND server_ip=%s ORDER BY last_checkin DESC LIMIT 1;", (prog, server_ip))
            if self.sql().rowcount > 0:
                for row in self.sql().fetchall():
                    self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, row['id']))
            else:
                self.sql().execute("INSERT INTO server_keepalive_processes SET server_ip=%s,name=%s,pid=%s;", (server_ip, prog, pid))
            ret = False
        else:
            for name in procs:
                if procs[name]['is_alive'] > 0:
                    if procs[name]['server_ip'] == server_ip:
                        if procs[name]['pid'] == pid:
                            if procs[name]['pid'] > 0 and self.is_process_running(procs[name]['pid']):
                                self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, procs[name]['id']))
                            	ret = False
                            else:
                                self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, '0', procs[name]['id']))
                        else:
                            if procs[name]['pid'] > 0 and self.is_process_running(procs[name]['pid']):
                                self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, procs[name]['pid'], procs[name]['id']))
                            else:
                                self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, procs[name]['id']))
                            	ret = False
                    else:
                        if allow_multiple is True:
                            self.sql().execute("SELECT id FROM server_keepalive_processes WHERE name=%s AND server_ip=%s ORDER BY last_checkin DESC LIMIT 1;", (prog, server_ip))
                            if self.sql().rowcount > 0:
                                for row in self.sql().fetchall:
                                    self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, row['id']))
                            else:
                                self.sql().execute("INSERT INTO server_keepalive_processes SET server_ip=%s,name=%s,pid=%s;", (server_ip, prog, pid))
                            ret = False
                else:
                    if procs[name]['server_ip'] == server_ip:
                        self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, procs[name]['id']))
                        ret = False
                    else:
                        if allow_multiple is True:
                            self.sql().execute("SELECT id FROM server_keepalive_processes WHERE name=%s AND server_ip=%s ORDER BY last_checkin DESC LIMIT 1;", (prog, server_ip))
                            if self.sql().rowcount > 0:
                                for row in self.sql().fetchall:
                                    self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, row['id']))
                            else:
                                self.sql().execute("INSERT INTO server_keepalive_processes SET server_ip=%s,name=%s,pid=%s;", (server_ip, prog, pid))
                            ret = False
                        else:
                            self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, procs[name]['id']))
                            ret = False
        return ret
