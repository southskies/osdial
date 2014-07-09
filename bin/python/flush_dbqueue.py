#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging

from osdial import OSDial

PROGNAME = 'osdial_flush_dbqueue'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_flush_dbqueue - Clears expires manager entries and optimizes related tables.')
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
        logger = logging.getLogger('flushdb')
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

        handler = logging.FileHandler('%s/maintenance.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
        handler.setLevel(logdeflvl)
        handler.setFormatter(formatter)
        logger.addHandler(handler)

        if opt['verbose'] or opt['debug']:
            handler = logging.StreamHandler()
            handler.setLevel(logdeflvl)
            handler.setFormatter(formatter)
            logger.addHandler(handler)

        logger.setLevel(logdeflvl)

        osdspt.close()
        osdspt = None
    except MySQLdb.OperationalError, e:
        logger.error("Could not connect to MySQL! %s", e)
        sys.exit(1)
    gc.collect()

    logger.info("Starting flushdbqueue_process()")
    flushdbqueue_process()


def flushdbqueue_process():
    """
    The routine responsible for flushing the DB queues.
    """
    osdial = OSDial()
    logger = logging.getLogger('flushdb')
    six_hour = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time() - 21600))
    three_hour = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time() - 10800))
    quarter_hour = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time() - 900))
    eigth_hour = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time() - 450))

    svrlog = {}
    osdial.sql().execute("SELECT server_ip,vd_server_logs FROM servers WHERE active='Y';")
    for row in osdial.sql().fetchall():
        svrlog[row['server_ip']] = row['vd_server_logs']

    flush_time = eigth_hour
    if re.search('Y',svrlog[osdial.VARserver_ip]):
        flush_time = quarter_hour

    for svr in svrlog.keys():
        logger.info(" - Clearing expired manager entries")
        osdial.sql().execute("DELETE FROM osdial_manager WHERE (server_ip=%s AND ((status='DEAD' AND entry_date<%s) OR entry_date<%s)) OR entry_date<%s;", (svr, flush_time, three_hour, six_hour))

    optimizes = ['osdial_manager','osdial_live_calls','osdial_auto_calls','osdial_hopper']
    for opttab in optimizes:
        logger.info(" - Optimizing Table "+opttab)
        osdial.sql().execute("OPTIMIZE TABLE "+opttab+";")
        rows = osdial.sql().fetchall()

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
