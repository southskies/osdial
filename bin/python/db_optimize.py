#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging

from osdial import OSDial
from warnings import filterwarnings
import MySQLdb as Database
filterwarnings('ignore', category = Database.Warning)

PROGNAME = 'osdial_db_optimize'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_db_optimize - Optimizes database tables.')
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
        logger = logging.getLogger('dbopt')
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

    logger.info("Starting dboptimize_process()")
    dboptimize_process()


def dboptimize_process():
    """
    The routine responsible for nightly database optimization.
    """
    osdial = OSDial()
    logger = logging.getLogger('dbopt')
    optimizes = ['osdial_manager','osdial_auto_calls','osdial_live_agents','osdial_campaign_stats','osdial_campaign_server_stats',
                'osdial_dnc','osdial_callbacks','osdial_conferences','osdial_hopper']
    deletes = ['osdial_campaign_stats','osdial_campaign_server_stats','osdial_campaign_agent_stats','osdial_live_inbound_agents']
    updates = ['osdial_inbound_group_agents','osdial_campaign_agents']
    for opttab in optimizes:
        logger.info(" - Optimizing Table "+opttab)
        osdial.sql().execute("OPTIMIZE TABLE "+opttab+";")
    for deltab in deletes:
        logger.info(" - Clear Table "+deltab)
        osdial.sql().execute("DELETE FROM "+deltab+";")
    for updtab in updates:
        logger.info(" - Updating Table "+updtab)
        osdial.sql().execute("UPDATE "+updtab+" SET calls_today=0;")
    logger.info(" - Caching osdial_list groups")
    osdial.sql().execute("INSERT IGNORE INTO osdial_report_groups (SELECT 'states' AS group_type,state AS group_value,state AS group_label FROM osdial_postal_codes WHERE country_code='1' GROUP BY state) UNION (SELECT 'lead_source_id' AS group_type,source_id AS group_value,source_id AS group_label FROM osdial_list WHERE source_id!='' GROUP BY source_id) UNION (SELECT 'lead_vendor_lead_code' AS group_type,vendor_lead_code AS group_value,vendor_lead_code AS group_label FROM osdial_list WHERE vendor_lead_code!='' GROUP BY vendor_lead_code);")

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
