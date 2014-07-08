#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse, psutil

import MySQLdb, logging

from osdial import OSDial

PROGNAME = 'osdial_sort_recordings'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_sort_recordings - sorts recordings into campaign/date level directories.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
    #parser.add_argument('-d', '--daemon', action='store_true', help='Puts process in daemon mode.',dest='daemon')
    parser.add_argument('-l', '--logLevel', action='store', default='ERROR', choices=['CRITICAL','ERROR','WARNING','INFO','DEBUG'], help='Sets the level of output verbosity.', dest='loglevel')
    opts = parser.parse_args(args=argv)
    newargs = vars(opts)
    for arg in newargs:
        opt[arg] = newargs[arg]

    try:
        if os.getuid() == 0:
            astpwd = pwd.getpwnam('asterisk');
            os.setgid(astpwd.pw_gid)
            os.setuid(astpwd.pw_uid)
    except KeyError, e:
        pass

    osdspt = None
    try:
        osdspt = OSDial()
        FORMAT = '%(asctime)s|%(filename)s:%(lineno)d|%(levelname)s|%(message)s'
        logger = logging.getLogger('sortrec')
        logdeflvl = logging.ERROR
        logstr2err={'CRITICAL':logging.CRITICAL,'ERROR':logging.ERROR,'WARNING':logging.WARNING,'INFO':logging.INFO,'DEBUG':logging.DEBUG}
        if opt['verbose']:
            logdeflvl = logging.INFO
        elif opt['debug']:
            logdeflvl = logging.DEBUG
        elif opt['loglevel']:
            if logstr2err.has_key(opt['loglevel']):
                logdeflvl = logstr2err[opt['loglevel']]
        formatter = logging.Formatter(FORMAT)

        handler = logging.FileHandler('%s/sort_recordings.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
        handler.setLevel(logdeflvl)
        handler.setFormatter(formatter)
        logger.addHandler(handler)

        if opt['verbose'] or opt['debug']:
            handler = logging.StreamHandler()
            handler.setLevel(logdeflvl)
            handler.setFormatter(formatter)
            logger.addHandler(handler)

        logger.setLevel(logdeflvl)

        sptres = osdspt.server_process_tracker(PROGNAME, osdspt.VARserver_ip, os.getpid(), True)
        osdspt.close()
        osdspt = None
        if sptres:
            proclogger.error("Error process already running!")
            sys.exit(1)
    except MySQLdb.OperationalError, e:
        logger.error("Could not connect to MySQL! %s", e)
        sys.exit(1)
    gc.collect()

    logger.info("Starting sortrecordings_process()")
    sortrecordings_process()


def sortrecordings_process():
    """
    Sorts recordings into campaign/date style directories.
    """
    osdial = OSDial()
    logger = logging.getLogger('sortrec')

    webpath = osdial.VARHTTP_path
    if osdial.configuration['ArchiveWebPath']:
        webpath = osdial.configuration['ArchiveWebPath']
    if re.search('^http://127\.0\.0\.1',webpath):
        webpath = re.sub('http://127\.0\.0\.1','http://%s' % osdial.VARserver_ip,webpath)

    locked_files = {}
    for p in psutil.process_iter():
        try:
            if len(p.cmdline) and re.search('asterisk', p.cmdline[0]):
                for file in p.get_open_files():
                    locked_files[file.path] = True
        except psutil.AccessDenied, e:
            pass

    srcdir = "%s/%s" % (osdial.PATHarchive_home, osdial.PATHarchive_mixed)
    dstdir = "%s/%s" % (osdial.PATHarchive_home, osdial.PATHarchive_sorted)
    for afile in os.listdir(srcdir):
        sort_file = False
        if re.search('\.wav$|\.gsm$|\.ogg$|\.mp3$',afile,re.IGNORECASE):
            if not locked_files.has_key("%s/%s" % (srcdir,afile)):
                sort_file = True
        if sort_file:
            SQLfile = re.sub('-all\.wav|-all\.gsm|-all\.ogg|-all\.mp3','',afile,re.IGNORECASE)
            osdial.sql().execute("SELECT SQL_NO_CACHE recording_log.recording_id AS rcid,osdial_lists.campaign_id AS camp,DATE(recording_log.start_time) AS date,recording_log.lead_id AS lead,recording_log.extension AS rlext FROM recording_log LEFT JOIN osdial_list ON (recording_log.lead_id=osdial_list.lead_id) LEFT JOIN osdial_lists ON (osdial_list.list_id=osdial_lists.list_id) WHERE filename=%s ORDER BY recording_id DESC LIMIT 1;", (SQLfile))
            for row in osdial.sql().fetchall():
                extdir = ''
                if re.search('^PBX-IN|^PBX-OUT',SQLfile):
                    extdir = "/%s" % row['rlext']
                if not row['camp']:
                    row['camp'] = 'UNKNOWN'
                if not extdir and re.search('AIG',afile):
                    extdir = "/AIG"
                fulldstdir = "%s/%s/%s%s" % (dstdir,row['camp'],row['date'],extdir)
                if not opt['test']:
                    if not os.path.isdir(fulldstdir):
                        os.makedirs(fulldstdir)
                    os.rename("%s/%s" % (srcdir, afile), "%s/%s" % (fulldstdir, afile))
                    os.chmod("%s/%s" % (fulldstdir, afile), 0777)

                event = "%s sorted" % afile
                if row['rcid']:
                    if not opt['test']:
                        rlsts = osdial.sql().execute("UPDATE recording_log SET location=%s WHERE recording_id=%s;", ("%s/%s/%s/%s%s/%s" % (webpath,osdial.PATHarchive_sorted,row['camp'],row['date'],extdir,afile),row['rcid']))
                        if rlsts:
                            event = "%s, added to recording_log (%s)" % (event, rlsts)
                        qlsts = osdial.sql().execute("INSERT IGNORE INTO qc_recordings SET recording_id=%s,lead_id=%s,filename=%s,location=%s;", (row['rcid'],row['lead'],afile,fulldstdir))
                        if qlsts:
                            event = "%s, added to qc_recordings (%s)" % (event, qlsts)
                    else:
                        event = "%s, was found in recording_log, in test mode" % event
                else:
                    event = "%s, was not found in recording_log" % event
                logger.info(event)

    osdial.close()
    osdial = None
    sys.exit(0)


def user_main(args):
    errcode = main(args)
    return errcode

if __name__ == '__main__':
    sys.path.insert(0, '%s/python' % os.path.dirname(os.path.realpath(__file__)))
    try:
        libname = re.sub('(^osdial_|\..*$)','',os.path.basename(__file__))
        thislib = __import__(libname)
        thislib.user_main(sys.argv[1:])
    except KeyboardInterrupt, e:
        print >> sys.stderr, "\n\nExiting on user cancel."
        sys.exit(1)
