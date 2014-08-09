#
# _osdial.py
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#
"""
osdial - Main object class
"""

import sys, os, pwd, re, pprint
from osdial.sql import OSDialSQL
from asterisk.agi import AGI as OSDAGI
import time, datetime
import mimetypes

mimetypes.init()
mimetypes.add_type('audio/G722','.g722',True)
mimetypes.add_type('audio/G729','.g729',True)
mimetypes.add_type('audio/GSM','.gsm',True)
mimetypes.add_type('audio/ogg','.ogg',True)
mimetypes.add_type('audio/PCMU','.ulaw',True)
mimetypes.add_type('audio/PCMA','.alaw',True)
mimetypes.add_type('audio/siren7','.siren7',True)
mimetypes.add_type('audio/siren14','.siren14',True)
mimetypes.add_type('audio/sln','.sln',True)
mimetypes.add_type('audio/sln-16','.sln16',True)
mimetypes.add_type('audio/mpeg','.mp3',True)
mimetypes.add_type('audio/x-wav','.wav',True)

class OSDial(object):
    vars = {}
    _agi = None
    _sql = None
    _sql_max_packet = 0

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
        self.sql_max_packet()

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


    def reload(self):
        self._loadConfig()
        self._loadSQLConfig()
        

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

    def sql_max_packet(self):
        self.sql().execute("SHOW variables LIKE 'max_allowed_packet';")
        self._sql_max_packet = 0
        for row in self.sql().fetchall():
            self._sql_max_packet = row['Value']

    def server_process_tracker(self, prog, server_ip, pid, allow_multiple):
        pcount = 0
        ret = True
        procs = {}
        self.sql().execute("SELECT id,name,server_ip,pid,IF(UNIX_TIMESTAMP(last_checkin)>UNIX_TIMESTAMP()-180 AND pid>0,1,0) AS is_alive FROM server_keepalive_processes WHERE name=%s ORDER BY last_checkin DESC;", (prog))
        rows = self.sql().fetchall()
        for row in rows:
            if procs.has_key(prog) is False:
                procs[prog] = {'id':row['id'], 'server_ip':row['server_ip'], 'pid':row['pid'], 'is_alive':row['is_alive']}
            pcount += 1
        if pcount == 0:
            self.sql().execute("SELECT id FROM server_keepalive_processes WHERE name=%s AND server_ip=%s ORDER BY last_checkin DESC LIMIT 1;", (prog, server_ip))
            rows = self.sql().fetchall()
            if rows is not None:
                for row in rows:
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
                            rows = self.sql().fetchall()
                            if rows is not None:
                                for row in rows:
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
                            rows = self.sql().fetchall()
                            if rows is not None:
                                for row in rows:
                                    self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, row['id']))
                            else:
                                self.sql().execute("INSERT INTO server_keepalive_processes SET server_ip=%s,name=%s,pid=%s;", (server_ip, prog, pid))
                            ret = False
                        else:
                            self.sql().execute("UPDATE server_keepalive_processes SET server_ip=%s,name=%s,pid=%s,last_checkin=NOW() WHERE id=%s;", (server_ip, prog, pid, procs[name]['id']))
                            ret = False
        return ret

    def nth_weekday_of_month_year(self, year, month, week_day, nth_week):
        temp = datetime.date(year, month, 1)
        adj = (week_day - temp.weekday()) % 7
        temp += datetime.timedelta(days=adj)
        temp += datetime.timedelta(weeks=nth_week-1)
        if temp.month != month:
            return False
        return temp

    def dstcalc(self, method, intime):
        nth_map = {'F':1,'S':2,'T':3,'L':5}
        day_map = {'M':0,'S':6}
        month_map = {'F':2,'M':3,'A':4,'S':9,'O':10,'N':11}

        (start,end) = method.split('-')
        (start_nth,start_day,start_month) = (start[0:1],start[1:2],start[2:3])
        (end_nth,end_day,end_month) = (end[0:1],end[1:2],end[2:3])

        start_year = time.localtime(intime).tm_year
        end_year = time.localtime(intime).tm_year
        if month_map[end_month] < month_map[start_month]:
            end_year += 1

        dst_start_date = self.nth_weekday_of_month_year(start_year, month_map[start_month], day_map[start_day], nth_map[start_nth])
        if not dst_start_date:
            dst_start_date = self.nth_weekday_of_month_year(start_year, month_map[start_month], day_map[start_day], nth_map[start_nth]-1)
        dst_start_time = time.mktime(time.strptime("%s 02:00:00" % dst_start_date, '%Y-%m-%d %H:%M:%S'))

        dst_end_date = self.nth_weekday_of_month_year(end_year, month_map[end_month], day_map[end_day], nth_map[end_nth])
        if not dst_end_date:
            dst_end_date = self.nth_weekday_of_month_year(end_year, month_map[end_month], day_map[end_day], nth_map[end_nth]-1)
        dst_end_time = time.mktime(time.strptime("%s 02:00:00" % dst_end_date, '%Y-%m-%d %H:%M:%S'))

        dstval = 0
        if intime >= dst_start_time and intime <= dst_end_time:
            dstval = 1
        return dstval


    def media_get_filedata(self, filename):
        self.sql().execute("SELECT filedata FROM osdial_media_data WHERE filename=%s;", (filename))
        rows = self.sql().fetchall()
        filedata = "".join([row['filedata'] for row in rows])
        return filedata

    def media_delete_filedata(self, filename):
        self.sql().execute("DELETE FROM osdial_media_data WHERE filename=%s;", (filename))


    def media_add_files(self, mdir, pattern, updatedata):
        if not mdir:
            mdir = '.'
        if not pattern:
            pattern = '.*'
        files = []
        if not os.path.isdir(mdir):
            return files
        for mfile in os.listdir(mdir):
            if not re.search('^.$|^..$',mfile) and re.search(pattern,mfile) and not os.path.isdir("%s/%s" % (mdir, mfile)):
                fullfile = "%s/%s" % (mdir, mfile)
                mime = re.sub('.*\.','.',"%s" % mfile)
                extension = re.sub('.*/|\..*$','',"%s" % mfile)
                if not re.search('^\d+$',extension):
                    extension = ''
                files.append(self.media_add_file(fullfile, mimetypes.types_map[mime], mfile, extension, updatedata))
        return files
                
    def media_add_file(self, fullfile, mimetype, description, extension, updatedata):
        mfile = re.sub('.*/','',"%s" % fullfile)
        if not mimetype:
            mimetype = mimetypes.types_map[re.sub('.*\.','.',mfile)]
        if not extension:
            extension = re.sub('.*/|\..*$','',"%s" % mfile)
            if not re.search('^\d+$',extension):
                extension = ''
        if not os.path.exists(fullfile):
            return '!%s' % mfile
        fncnt = 0
        self.sql().execute("SELECT count(*) fncnt FROM osdial_media WHERE filename=%s;", (mfile))
        for row in self.sql().fetchall():
            fncnt = row['fncnt']
        if fncnt == 0:
            self.sql().execute("INSERT INTO osdial_media SET filename=%s,mimetype=%s,description=%s,extension=%s;", (mfile, mimetype, description, extension))
        else:
            fncnt = 0
            self.sql().execute("SELECT count(*) fncnt FROM osdial_media_data WHERE filename=%s;", (mfile))
            for row in self.sql().fetchall():
                fncnt = row['fncnt']
            if fncnt:
                if updatedata:
                    self.media_delete_filedata(mfile)
                else:
                    return '*%s' % mfile
        
        datafile = open(fullfile, 'rb')
        readsize = int(self._sql_max_packet) - 120000
        data = datafile.read(readsize)
        while data is not None:
            self.sql().execute("INSERT INTO osdial_media_data SET filename=%s,filedata=%s;", (mfile,data))
            data = datafile.read(readsize)
        datafile.close()
        if updatedata:
            return '=%s' % mfile
        return '+%s' % mfile


    def media_save_file(self, mdir, mfile, overwrite):
        if not mdir:
            mdir = '.'
        astpwd = pwd.getpwnam('asterisk');
        if not os.path.isdir(mdir):
            os.makedirs(mdir, 0777)
            os.chown(mdir, astpwd.pw_uid, astpwd.pw_gid)
        os.chmod(mdir, 0777)
        fullfile = "%s/%s" % (mdir, mfile)
        if os.path.exists(fullfile) and overwrite is False:
            return "*%s" % mfile
        filedata = self.media_get_filedata(mfile)
        if not filedata:
            return "!%s" % mfile
            
        outfile = open(fullfile, "wb")
        outfile.write(filedata)
        outfile.close()
        os.chown(fullfile, astpwd.pw_uid, astpwd.pw_gid)
        os.chmod(fullfile, 0666)
        if overwrite:
            return "=%s" % mfile
        return "+%s" % mfile


    def media_save_files(self, mdir, pattern, overwrite):
        if not mdir:
            mdir = '.'
        if not pattern:
            pattern = '.*'
        astpwd = pwd.getpwnam('asterisk');
        if not os.path.isdir(mdir):
            os.makedirs(mdir, 0777)
            os.chown(mdir, astpwd.pw_uid, astpwd.pw_gid)
        os.chmod(mdir, 0777)
        files = []
        self.sql().execute("SELECT * FROM osdial_media;")
        for row in self.sql().fetchall():
            if re.search(pattern,row['filename']):
                files.append(self.media_save_file(mdir, row['filename'], overwrite))
                os.chmod("%s/%s" % (mdir,row['filename']), 0666)
        return files

    def osdevent(self, opts):
        self.event(opts)

    def event(self, opts):
        flds = []
        vals = []
        for opt in opts.keys():
            flds.append(opt)
            vals.append(opts[opt])
        insstr = "INSERT INTO osdial_events (%s) VALUES (%s);" % (",".join(flds),("%s,"[:3]*len(flds)).rstrip(','))
        self.sql().execute(insstr, (vals))
        

