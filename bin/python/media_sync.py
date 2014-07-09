#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging

from osdial import OSDial

PROGNAME = 'osdial_media_sync'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False,'file':False,'dir':False,'quiet':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_media_sync - sync media directories with database.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('-q', '--quiet', action='store_true', help='Silence output.',dest='quiet')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
    parser.add_argument('-l', '--logLevel', action='store', default='INFO', choices=['CRITICAL','ERROR','WARNING','INFO','DEBUG'], help='Sets the level of output verbosity.', dest='loglevel')
    parser.add_argument('--file', action='store', help='Load File into Media Manager.', dest='file')
    parser.add_argument('--dir', action='store', help='Load Directory into Media Manager.', dest='dir')
    opts = parser.parse_args(args=argv)
    newargs = vars(opts)
    for arg in newargs:
        opt[arg] = newargs[arg]

    if opt['verbose'] or opt['debug']:
        opt['quiet'] = False
    elif opt['quiet']:
        opt['verbose'] = False
        opt['debug'] = False
    else:
        opt['verbose'] = True

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
        logger = logging.getLogger('mediasync')
        logdeflvl = logging.INFO
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

    logger.info("Starting mediasync_process()")
    mediasync_process()


def mediasync_process():
    """
    Sync media directories with database.
    """
    osdial = OSDial()
    logger = logging.getLogger('mediasync')

    if opt['file']:
        opt['file'] = os.path.abspath(opt['file'])
        if os.path.exists(opt['file']) and not os.path.isdir(opt['file']):
            logger.info("  Add File: %s", osdial.media_add_file(opt['file'], False, False, False, False))
        else:
            logger.info("  Add File: File Not Found.")
    elif opt['dir']:
        opt['dir'] = os.path.abspath(opt['dir'])
        if os.path.exists(opt['dir']) and os.path.isdir(opt['dir']):
            logger.info("  Add Directory: %s", opt['dir'])
            files = osdial.media_add_files(opt['dir'],False,False)
            for mfile in files:
                logger.info("    Add File: %s", mfile)
        else:
            logger.info("  Add Directory Failed: File Not Found")
    else:
        files = []
        files.append(osdial.media_add_files('/var/lib/asterisk/sounds','generic-hold.*',False))
        files.append(osdial.media_add_files('/var/lib/asterisk/sounds/en','vm-goodbye.*',False))
        files.append(osdial.media_add_files('/var/lib/asterisk/sounds','8510.*',False))
        files.append(osdial.media_add_files('/var/lib/asterisk/sounds.ramfs','8510.*',False))
        files.append(osdial.media_add_files('/var/lib/asterisk/OSDprompts',False,False))
        files.append(osdial.media_add_files('/mnt/ramdisk/sounds','8510.*',False))
        files.append(osdial.media_add_files('/opt/osdial/html/ivr',False,False))
        for mfile in files:
            if opt['debug']:
                logger.debug("  Adding File: %s", mfile)

    savefiles = []
    savefiles.append(osdial.media_save_files('/opt/osdial/media',False,False))
    if os.path.isdir("/var/lib/asterisk/sounds"):
        savefiles.append(osdial.media_save_files('/var/lib/asterisk/sounds/osdial',False,False))
    if os.path.isdir("/mnt/ramdisk/sounds"):
        savefiles.append(osdial.media_save_files('/mnt/ramdisk/sounds/osdial',False,False))
    for mfile in savefiles:
        if opt['debug']:
            logger.debug("  Saving File: %s", mfile)

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
