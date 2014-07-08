#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging

from osdial import OSDial

PROGNAME = 'osdial_adjust_gmtnow_on_leads'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False,'search_postal':False,'singlelistid':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_vm_update - updates voicemail counts.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('--postal-code-gmt', action='store_true', help='Attempt postal codes lookup for timezones.',dest='search_postal')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
    #parser.add_argument('-d', '--daemon', action='store_true', help='Puts process in daemon mode.',dest='daemon')
    parser.add_argument('--singlelistid', action='store', default=False, help='Only lookup and alter leads in one list_id.', dest='singlelistid')
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
        logger = logging.getLogger('adjustgmt')
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

    logger.info("Starting adjustgmt_process()")
    adjustgmt_process()


def adjustgmt_process():
    """
    goes throught the osdial_list table and adjusts the gmt_offset_now
    field to change it to today's offset if needed because of Daylight Saving Time
    """
    osdial = OSDial()
    logger = logging.getLogger('adjustgmt')

    local_gmt_off = float(osdial.server['local_gmt'])
    local_gmt_off_std = float(osdial.server['local_gmt'])
    if time.localtime().tm_isdst:
        local_gmt_off += 1

    listSQL = ''
    XlistSQL = ''
    if opt['singlelistid']:
        listSQL = " WHERE list_id='%s' " % opt['singlelistid']
        XlistSQL = " AND list_id='%s' " % opt['singlelistid']

    phone_codes_list = {}
    sqlcnt = osdial.sql().execute("SELECT DISTINCT phone_code FROM osdial_list " + listSQL)
    for row in osdial.sql().fetchall():
        phone_code = row['phone_code']
        phone_code = re.sub('\s|\t|\r|\n','',phone_code)
        phone_code = re.sub('^011|^0011|^001|^010|^000|^00|^0|^9011','',phone_code)
        if phone_code and len(phone_code) < 5:
            phone_codes_list[str(row['phone_code'])] = str(phone_code)
    logger.info(" - Unique Country dial codes found: %s", (sqlcnt))

    phone_codes = {}
    sqlcnt = osdial.sql().execute("SELECT * FROM osdial_phone_codes;")
    for row in osdial.sql().fetchall():
        if not phone_codes.has_key(str(row['country_code'])):
            phone_codes[str(row['country_code'])] = {}
        phone_codes[str(row['country_code'])][str(row['areacode'])] = row
    logger.info(" - GMT phone codes records: %s", (sqlcnt))

    postal_codes = {}
    sqlcnt = 0
    osdial.sql().execute("SELECT * FROM osdial_postal_codes;")
    if opt['search_postal']:
        for row in osdial.sql().fetchall():
            if not postal_codes.has_key(str(row['country_code'])):
                postal_codes[str(row['country_code'])] = {}
            postal_codes[str(row['country_code'])][str(row['postal_code'])] = row
            sqlcnt += 1
    logger.info(" - GMT postal codes records: %s", (sqlcnt))

    area_updated_count = 0
    postal_updated_count = 0
    for phone_code in sorted(phone_codes_list.keys()):
        country_code = phone_codes_list[phone_code]
        if not phone_code:
            country_code = '1'

        logger.info('RUNNING LOOP FOR COUNTRY CODE: %s (%s)', country_code, phone_code)

        for areacode in sorted(phone_codes[country_code].keys()):
            area_gmt = phone_codes[country_code][areacode]['GMT_offset']
            area_gmt = float(re.sub('\+','',area_gmt))
            area_gmt_method = phone_codes[country_code][areacode]['DST_range']
            ac_match = ''
            if re.search('S',areacode):
                areacode = re.sub('\D','',areacode)
                ac_match = " AND phone_number LIKE '%s%%%%' AND state='%s' " % (areacode, phone_codes[country_code][areacode]['state'])
            else:
                ac_match = " AND phone_number LIKE '%s%%%%' " % (areacode)
            osdial.sql().execute("SELECT count(*) AS reccount FROM osdial_list WHERE phone_code=%s "+ac_match+" "+XlistSQL+";", (phone_code))
            reccount = 0
            for row in osdial.sql().fetchall():
                reccount = row['reccount']
            if reccount:
                logger.info("PROCESSING THIS LINE: %s  %s  %s", phone_code, country_code, areacode)
                ac_gmt_diff = (area_gmt - local_gmt_off_std)
                ac_localtime = (time.time() + (3600 * ac_gmt_diff))
                if re.search('[FSTL][MS][FMASON]-[FSTL][MS][FMASON]',area_gmt_method):
                    dstval = osdial.dstcalc(area_gmt_method,ac_localtime);
                    if dstval:
                        area_gmt += 1
                else:
                    logger.info("     No DST Method Found.   DST: 0")

                osdial.sql().execute("SELECT count(*) AS reccount2 FROM osdial_list WHERE phone_code=%s "+ac_match+" AND gmt_offset_now!=%s "+XlistSQL+";", (phone_code, area_gmt))
                reccount2 = 0
                for row in osdial.sql().fetchall():
                    reccount2 = row['reccount2']
            
                if not reccount2:
                    logger.info("   ALL GMT ALREADY CORRECT FOR : %s %s %s %s", phone_code, country_code, areacode, area_gmt)
                else:
                    osdial.sql().execute("UPDATE osdial_list SET gmt_offset_now=%s,modify_date=modify_date WHERE phone_code=%s "+ac_match+" AND gmt_offset_now!=%s "+XlistSQL+";", (area_gmt, phone_code, area_gmt))
                    area_updated_count += 1
                    logger.info(" %s records in %s %s %s updated to %s", reccount2, phone_code, country_code, areacode, area_gmt)

        if opt['search_postal']:
            logger.info("POSTAL CODE RUN START...")
            for postal_code in sorted(postal_codes[country_code].keys()):
                area_gmt = postal_codes[country_code][postal_code]['GMT_offset']
                area_gmt = float(re.sub('\+','',area_gmt))
                area_gmt_method = postal_codes[country_code][postal_code]['DST_range']
                ac_match = " AND postal_code LIKE '%s%%%%' " % (postal_code)
                osdial.sql().execute("SELECT count(*) AS reccount FROM osdial_list WHERE postal_code=%s "+ac_match+" "+XlistSQL+";", (postal_code))
                reccount = 0
                for row in osdial.sql().fetchall():
                    reccount = row['reccount']
                if reccount:
                    logger.info("PROCESSING THIS LINE: %s  %s  %s", phone_code, country_code, postal_code)
                    ac_gmt_diff = (area_gmt - local_gmt_off_std)
                    ac_localtime = (time.time() + (3600 * ac_gmt_diff))
                    if re.search('[FSTL][MS][FMASON]-[FSTL][MS][FMASON]',area_gmt_method):
                        dstval = osdial.dstcalc(area_gmt_method,ac_localtime);
                        if dstval:
                            area_gmt += 1
                    else:
                        logger.info("     No DST Method Found.   DST: 0")

                    osdial.sql().execute("SELECT count(*) AS reccount2 FROM osdial_list WHERE postal_code=%s "+ac_match+" AND gmt_offset_now!=%s "+XlistSQL+";", (postal_code, area_gmt))
                    reccount2 = 0
                    for row in osdial.sql().fetchall():
                        reccount2 = row['reccount2']
            
                    if not reccount2:
                        logger.info("   ALL GMT ALREADY CORRECT FOR : %s %s %s %s", phone_code, country_code, postal_code, area_gmt)
                    else:
                        osdial.sql().execute("UPDATE osdial_list SET gmt_offset_now=%s,modify_date=modify_date WHERE postal_code=%s "+ac_match+" AND gmt_offset_now!=%s "+XlistSQL+";", (area_gmt, postal_code, area_gmt))
                        postal_updated_count += 1
                        logger.info(" %s records in %s %s %s updated to %s", reccount2, phone_code, country_code, postal_code, area_gmt)

    logger.info("Areacode Updates:  %s", area_updated_count)
    logger.info("Postal Updates:    %s", postal_updated_count)

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
