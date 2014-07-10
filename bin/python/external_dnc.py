#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse, psutil

import MySQLdb, logging

from osdial import OSDial
from osdial.sql import OSDialSQL as OSDialSQL2

PROGNAME = 'osdial_external_dnc'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'sched':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_external_dnc - Check leads against an external DNC database.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
    parser.add_argument('-l', '--logLevel', action='store', default='ERROR', choices=['CRITICAL','ERROR','WARNING','INFO','DEBUG'], help='Sets the level of output verbosity.', dest='loglevel')
    parser.add_argument('--sched', action='store', help='Schedule lists to be scrubbed. (ALL|NONE|list_id)', dest='sched')
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
        logger = logging.getLogger('ednc')
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

    logger.info("Starting externaldnc_process()")
    externaldnc_process()


def externaldnc_process():
    """
    Check leads against an external DNC database.
    """
    osdial = OSDial()
    logger = logging.getLogger('ednc')

    if re.search('Y',osdial.configuration['External_DNC_Active']):

        if opt['sched']:
            if re.search('ALL',opt['sched']):
                logger.debug('  Setting sched to ALL')
                osdial.sql().execute("UPDATE osdial_lists SET scrub_dnc='Y';")
            elif re.search('NONE',opt['sched']):
                logger.debug('  Setting sched to NONE')
                osdial.sql().execute("UPDATE osdial_lists SET scrub_dnc='N';")
            else:
                logger.debug('  Setting sched to %s', opt['sched'])
                osdial.sql().execute("UPDATE osdial_lists SET scrub_dnc='Y' WHERE list_id=%s;", (opt['sched']))
        else:

            proccount = 0
            for proc in psutil.process_iter():
                try:
                    if re.search('(perl|python).*external_dnc', " ".join(proc.cmdline)):
                        proccount += 1
                except psutil.AccessDenied, e:
                    pass
                except psutil.NoSuchProcess, e:
                    pass
            if proccount > 3:
                logger.warning("External DNC already has %s running, exiting.", proccount)

            else:
                lists = {}
                osdial.sql().execute("SELECT list_id,campaign_id FROM osdial_lists WHERE scrub_dnc='Y';")
                for row in osdial.sql().fetchall():
                    lists[str(row['list_id'])] = str(row['campaign_id'])
                if len(lists.keys()):
                    externaldnc_scan(osdial, lists)
    else:
        logger.warning("External DNC Check is disabled, exiting.")


    osdial.close()
    osdial = None
    sys.exit(0)


def externaldnc_scan(osdial, lists):
    logger = logging.getLogger('ednc.scan')

    esql = None
    try:
        eopts = {'host':'','user':'','user':'','passwd':'','db':'','port':'3306'}
        eopts['host'] = osdial.configuration['External_DNC_Address']
        eopts['user'] = osdial.configuration['External_DNC_Username']
        eopts['passwd'] = osdial.configuration['External_DNC_Password']
        eopts['db'] = osdial.configuration['External_DNC_Database']
        esql = OSDialSQL2(None, eopts)

        for list_id in lists.keys():
            logger.debug("  Scrubbing : %s", list_id)
            osdial.sql().execute("UPDATE osdial_lists SET scrub_last=NOW(),scrub_dnc='N' WHERE list_id=%s;", (list_id))

            # Get statuses to scan through.
            stats = ""
            osdial.sql().execute("SELECT dial_statuses FROM osdial_campaigns WHERE campaign_id=%s;", (lists[list_id]))
            for row in osdial.sql().fetchall():
                stats += row['dial_statuses']
            stats = re.sub(' ',"','",stats)
            stats = re.sub("^',","",stats)
            stats = re.sub(",'-$","",stats)

            if stats:
                # Scan main phone number
                osdial.sql().execute("SELECT lead_id,phone_number,alt_phone,address3 FROM osdial_list WHERE list_id=%s and status IN("+stats+");", (list_id))
                cur_row = 0
                rows = osdial.sql().fetchall()
                tot_row = len(rows)
                logger.debug("  Main Rows: %s", tot_row)
                for row in rows:
                    cur_row += 1
                    for phonefield in ['phone_number','alt_phone','address3']:
                        row[phonefield] = re.sub('\D','',row[phonefield])
                        if len(row[phonefield]) > 5:
                            fullphone = "%s" % row[phonefield]
                            areacode = row[phonefield][0:3]
                            phone = row[phonefield][3:]

                            estmt = osdial.configuration['External_DNC_SQL']
                            estmt = re.sub('%AREACODE%',areacode,estmt)
                            estmt = re.sub('%FULLPHONE%',fullphone,estmt)
                            estmt = re.sub('%NUMBER%',phone,estmt)

                            found = False
                            esql().execute(estmt)
                            for erow in esql().fetchall():
                                found = True
                            if found:
                                logger.debug("  Found %s %s : %s", phonefield, row['lead_id'], fullphone)
                                osdial.sql().execute("UPDATE osdial_lists SET scrub_last=NOW(),scrub_info=%s WHERE list_id=%s;", ("%s/%s" % (cur_row,tot_row),list_id))
                                if not opt['test']:
                                    osdial.sql().execute("UPDATE osdial_list SET status='DNCE' WHERE lead_id=%s;", (row['lead_id']))
                                    osdial.sql().execute("INSERT IGNORE INTO osdial_dnc SET phone_number=%s;", (fullphone))
                osdial.sql().execute("UPDATE osdial_lists SET scrub_info=%s WHERE list_id=%s;", ("%s/%s" % (cur_row,tot_row),list_id))

    except MySQLdb.OperationalError, e:
        logger.error("Got MySQL error on external DB. %s", type(e))
        pass

    if not esql is None:
        esql.close()
        esql = None


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
