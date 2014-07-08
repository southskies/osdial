#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging

from osdial import OSDial

PROGNAME = 'osdial_cleanup_agent_log'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_cleanup_agent_log - corrects malformed or incomplete agent log entries.')
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
        if os.geteuid() == 0:
            astpwd = pwd.getpwnam('asterisk');
            os.setegid(astpwd.pw_gid)
            os.seteuid(astpwd.pw_uid)
    except KeyError, e:
        pass

    osdspt = None
    try:
        osdspt = OSDial()
        FORMAT = '%(asctime)s|%(filename)s:%(lineno)d|%(levelname)s|%(message)s'
        logger = logging.getLogger('cleanup')
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

    logger.info("Starting cleanupagentlog_process()")
    cleanupagentlog_process()


def cleanupagentlog_process():
    """
    The routine responsible for cleaning up malformed entries in the agent log.
    """
    osdial = OSDial()
    logger = logging.getLogger('cleanup')

    lastdate = time.strftime('%Y-%m-%d 00:00:00', time.localtime(time.time() - (60*60*24*2)))

    time_recalc(osdial, "pause", "wait", lastdate)
    time_recalc(osdial, "wait", "talk", lastdate)
    time_recalc(osdial, "talk", "dispo", lastdate)

    logger.info(" - cleaning up dispo time")
    osdial.sql().execute("SELECT count(*) as dcnt FROM osdial_agent_log WHERE event_time>=%s AND dispo_sec>43999;", (lastdate))
    baddispocount = 0
    for row in osdial.sql().fetchall():
        baddispocount = row['dcnt']
    osdial.sql().execute("UPDATE osdial_agent_log SET dispo_sec='0' WHERE event_time>=%s AND dispo_sec>43999;", (lastdate))
    if baddispocount > 0:
        logger.info("     Bad Dispo records fixed: %s" % (baddispocount))

    osdial.close()
    osdial = None
    sys.exit(0)


def time_recalc(osdial, type1, type2, lastdate):
    logger = logging.getLogger('cleanup.recalc')
    logger.info(" - cleaning up "+type1+" time")
    osdial.sql().execute("SELECT agent_log_id,"+type1+"_epoch,"+type2+"_epoch FROM osdial_agent_log WHERE event_time>=%s AND "+type1+"_sec>43999;", (lastdate))
    alcnt = osdial.sql().rowcount
    if alcnt > 0:
        agent_log_id = []
        type1_epoch = []
        type2_epoch = []
        for row in osdial.sql().fetchall():
            agent_log_id.append(row['agent_log_id'])
            type1_epoch.append(row[type1+'_epoch'])
            type2_epoch.append(row[type2+'_epoch'])
        for idx in range(len(agent_log_id)):
            type1_sec = type2_epoch[idx] - type1_epoch[idx]
            if type1_sec < 0 or type1_sec > 43999:
                type1_sec = 0
            osdial.sql().execute("UPDATE osdial_agent_log SET "+type1+"_sec=%s WHERE event_time>=%s AND agent_log_id=%s;", (type1_sec, lastdate, agent_log_id[idx]))
        logger.info("     "+type1+" records fixed: %s" % (alcnt))

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
