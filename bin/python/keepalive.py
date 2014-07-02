#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, re, time, pprint, gc, psutil, subprocess
import pystrix, threading, argparse

import MySQLdb, logging, pystrix

from osdial import OSDial

PROGNAME = 'osdial_keepalive'
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

    logger.info("Starting keepalive_process()")
    keepalive_process(logger)


def keepalive_process(logger):
    """
    The routine responsible for keeping required osdial subsystems running.
    """
    osdial = OSDial()

    perl_running = {}
    perl_keepalives = {'1':'AST_update.pl',
                  '2':'AST_manager_send.pl',
                  '3':'AST_manager_listen.pl',
                  '4':'AST_VDauto_dial.pl',
                  '5':'AST_VDremote_agents.pl',
                  '6':'AST_VDadapt.pl',
                  '7':'FastAGI_log.pl',
                  '8':'AST_VDauto_dial_FILL.pl',
                  '9':'OSDcampaign_stats.pl' }

    """
    D - Means Dynamic
    """
    if re.search('D',osdial.VARactive_keepalives):
        if osdial.server['server_profile'] in ['AIO']:
            osdial.VARactive_keepalives = '12345679'
        if osdial.server['server_profile'] in ['CONTROL']:
            osdial.VARactive_keepalives = '579'
        if osdial.server['server_profile'] in ['SQL']:
            osdial.VARactive_keepalives = '579'
        if osdial.server['server_profile'] in ['WEB']:
            osdial.VARactive_keepalives = 'X'
        if osdial.server['server_profile'] in ['DIALER']:
            osdial.VARactive_keepalives = '12346'
        if osdial.server['server_profile'] in ['ARCHIVE']:
            osdial.VARactive_keepalives = 'X'
        if osdial.server['server_profile'] in ['OTHER']:
            osdial.VARactive_keepalives = 'X'

    """
    No soup for you
    """
    if re.search('X',osdial.VARactive_keepalives):
        logger.info("X in active_keepalives, exiting...")
        sys.exit(1)

    """
    Scan and prune running processes
    """
    for proc in psutil.process_iter():
        if proc.ppid != 1 and (re.search('\/usr\/bin\/perl|\/usr\/bin\/python',proc.exe) or (len(proc.cmdline) > 0 and re.search('\/usr\/bin\/perl|\/usr\/bin\/python',proc.cmdline[0]))):
            for kaprog in perl_keepalives.values():
                if not perl_running.has_key(kaprog):
                    perl_running[kaprog] = 0
                if re.search("%s" % kaprog,"%s" % proc.cmdline):
                    perl_running[kaprog] += 1
                    logger.info("IS RUNNING:%6d" % (proc.pid))
                    if perl_running[kaprog] > 0:
                        if re.search('AST_update.pl|AST_manager_listen.pl|AST_manager_send.pl',kaprog):
                            logger.info("Detected second instance of %s, killing pid %6d" % (kaprog, proc.pid))
                            if proc.is_running() and opt['test'] is False:
                                proc.kill()


    """
    Start keepalives
    """
    for kaval in perl_keepalives.keys():
        if re.search("%s" % kaval, "%s" % osdial.VARactive_keepalives):
            if perl_running[perl_keepalives[kaval]] < 1:
                logger.info("Starting %s." % (perl_keepalives[kaval]))
                screenid = "%s" % perl_keepalives[kaval]
                screenid = re.sub('AST|VD|OSD|_|\.pl$','',screenid)
                if opt['test'] is False:
                    retval = subprocess.call('/usr/bin/screen -d -m -S OSD%s %s/%s' % (screenid, osdial.PATHhome, perl_keepalives[kaval]), shell=True)

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
