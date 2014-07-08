#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging

from osdial import OSDial

PROGNAME = 'osdial_areacode_populate'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_areacode_populate - loads areacode entries into database.')
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
        logger = logging.getLogger('areacode')
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

    logger.info("Starting areacodepopulate_process()")
    areacodepopulate_process()


def areacodepopulate_process():
    """
    loads areacode entries into database.
    """
    osdial = OSDial()
    logger = logging.getLogger('areacode')

    logger.info(" - Clearing osdial_phone_codes.")
    osdial.sql().execute("DELETE FROM osdial_phone_codes;")

    if os.path.exists("%s/phone_codes_GMT.txt" % osdial.PATHhome):
        logger.info(" - Loading phone_codes_GMT.txt into osdial_phone_codes.")
        pcfile = open("%s/phone_codes_GMT.txt" % osdial.PATHhome,"r")
        lcnt = 0
        head = []
        data = []
        for line in pcfile:
            if not lcnt:
                line = re.sub(' ', '_', line.lower(), re.IGNORECASE)
                row = line.rstrip('\r\n').split('\t') 
                for col in row:
                    col = re.sub('^code$','country_code',col)
                    col = re.sub('^dst$','DST',col)
                    col = re.sub('^dst_range$','DST_range',col)
                    col = re.sub('^gmt$','GMT_offset',col)
                    head.append(col)
            else:
                ldata = line.rstrip('\r\n').split('\t')[:8]
                if ldata[7]:
                    ldata[7] = ldata[7][:30]
                data.append(ldata)
            lcnt += 1
        osdial.sql().executemany("INSERT INTO osdial_phone_codes ("+",".join(head[:8])+") VALUES ("+(str("%s,")[:3]*len(head[:8])).rstrip(',')+")", data)
        pcfile.close()

    logger.info(" - Clearing osdial_postal_codes.")
    osdial.sql().execute("DELETE FROM osdial_postal_codes;")
    if os.path.exists("%s/GMT_USA_zip.txt" % osdial.PATHhome):
        logger.info(" - Loading GMT_USA_zip.txt into osdial_postal_codes.")
        pcfile = open("%s/GMT_USA_zip.txt" % osdial.PATHhome,"r")
        head = ['postal_code','state','GMT_offset','DST','DST_range','country','country_code']
        data = []
        for line in pcfile:
            ldata = line.rstrip('\r\n').split('\t')[:4]
            dst_range = ""
            if re.search('Y',ldata[3]):
                dst_range = "SSM-FSN"
            ldata.append(dst_range)
            ldata.append('USA')
            ldata.append('1')
            data.append(ldata)
        osdial.sql().executemany("INSERT INTO osdial_postal_codes ("+",".join(head[:7])+") VALUES ("+(str("%s,")[:3]*len(head[:7])).rstrip(',')+")", data)
        pcfile.close()

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
