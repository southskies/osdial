#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, re, time, pprint, gc
import pystrix, threading, argparse

import MySQLdb, logging, pystrix

from osdial import OSDial

PROGNAME = 'osdial_reset_mysql_vars'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_keepalive - keeps required subsystems running.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
    parser.add_argument('-d', '--daemon', action='store_true', help='Puts process in daemon mode.',dest='daemon')
    parser.add_argument('-l', '--logLevel', action='store', default='ERROR', choices=['CRITICAL','ERROR','WARNING','INFO','DEBUG'], help='Sets the level of output verbosity.', dest='loglevel')
    opts = parser.parse_args(args=argv)
    newargs = vars(opts)
    for arg in newargs:
        opt[arg] = newargs[arg]

    osdspt = None
    try:
        osdspt = OSDial()
        FORMAT = '%(asctime)s|%(filename)s:%(lineno)d|%(levelname)s|%(message)s'
        logger = logging.getLogger()
        logdeflvl = logging.ERROR
        if opt.has_key('loglevel') and not opt['loglevel'] is None:
            logstr2err={'CRITICAL':logging.CRITICAL,'ERROR':logging.ERROR,'WARNING':logging.WARNING,'INFO':logging.INFO,'DEBUG':logging.DEBUG}
            logdeflvl = logstr2err[opt['loglevel']]
        logger.setLevel(logdeflvl)

        handler = logging.FileHandler('%s/maintenance.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.gmtime())) )
        handler.setLevel(logdeflvl)
        formatter = logging.Formatter(FORMAT)
        handler.setFormatter(formatter)
        logger.addHandler(handler)

        if opt['verbose']:
            handler = logging.StreamHandler()
            handler.setLevel(logging.INFO)
            formatter = logging.Formatter(FORMAT)
            handler.setFormatter(formatter)
            logger.addHandler(handler)
        

        sptres = osdspt.server_process_tracker(PROGNAME, osdspt.VARserver_ip, os.getpid(), True)
        osdspt.close()
        osdspt = None
        if sptres is True:
            logger.error("Error process already running!")
            sys.exit(1)
    except MySQLdb.OperationalError, e:
        logger.error("Could not connect to MySQL! %s", e)
        sys.exit(1)
    gc.collect()

    logger.info("Starting resetmysqlvar_process()")
    resetmysqlvar_process(logger)


def resetmysqlvar_process(logger):
    """
    The routine responsible for clearing the tables entries associated with this particular server_ip.
    """
    osdial = OSDial()

    updates = ['conferences','osdial_conferences']
    deletes = ['osdial_manager','osdial_auto_calls','osdial_live_agents','osdial_campaign_server_stats',
               'live_channels','live_inbound','live_sip_channels','parked_channels','web_client_sessions']
    for updtab in updates:
        logger.info(" - Updating Table "+updtab+" for server "+osdial.VARserver_ip)
        osdial.sql().execute("UPDATE "+updtab+" SET extension='' WHERE server_ip=%s;", (osdial.VARserver_ip))
    for deltab in deletes:
        logger.info(" - Clear Table "+deltab+" for server "+osdial.VARserver_ip)
        osdial.sql().execute("DELETE FROM "+deltab+" WHERE server_ip=%s;", (osdial.VARserver_ip))

    logger.info(" - Clear Table osdial_hopper for username like xxxx_"+osdial.VARserver_ip)
    osdial.sql().execute("DELETE from osdial_hopper where user LIKE %s;", ("%%_%s" % osdial.VARserver_ip))

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
