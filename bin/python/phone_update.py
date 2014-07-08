#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging
import asterisk.manager

from osdial import OSDial

PROGNAME = 'osdial_phone_update'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_phone_update - updates phone status variables.')
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
        logger = logging.getLogger('phoneupdate')
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

    logger.info("Starting phoneupdate_process()")
    phoneupdate_process()

gotEvent = False
eventdata = []
def phoneupdate_process():
    global gotEvent
    global eventdata
    """
    This routine verifies the registered IP of the phone against the database.
    """
    osdial = OSDial()
    logger = logging.getLogger('phoneupdate')

    CIDdate = time.strftime('%y%m%d%H%M%S', time.localtime(time.time()))
    now_date = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))

    logger.info(" - Scanning SIP/IAX2 phones")
    osdial.sql().execute("SELECT extension,phone_ip,protocol FROM phones WHERE server_ip=%s AND protocol IN ('SIP','IAX2');", (osdial.VARserver_ip))
    phcnt = osdial.sql().rowcount
    phones = []
    if phcnt > 0:
        for row in osdial.sql().fetchall():
            phones.append({"extension":row['extension'],"phone_ip":row['phone_ip'],"protocol":row['protocol']})

        ami = asterisk.manager.Manager()
        try:
            try:
                ami.connect(osdial.server['telnet_host'], port=osdial.server['telnet_port'])
                ami.login(osdial.server['ASTmgrUSERNAME'], osdial.server['ASTmgrSECRET'])
    
                for phone in phones:
                    response = None
                    phonehdr = {}
                    logger.info(" - Sending AMI request for SIP/IAX2 peer info")
                    if phone['protocol'] == "SIP":
                        response = ami.sipshowpeer(phone['extension'])
                    elif phone['protocol'] == "IAX2":
                        response = ami.command("iax2 show peer %s" % (phone['extension']))

                    if re.search('Success|Follows',response['Response']):
                        for phdr in response.response:
                            if re.search(': ', phdr):
                                [k,v] = re.split(': ', re.sub('\r|\n','',phdr), maxsplit=1)
                                phonehdr[re.sub('\W','',k)] = v

                        osdial.sql().execute("UPDATE phones SET picture=%s WHERE server_ip=%s AND extension=%s;", (phonehdr['Status'],osdial.VARserver_ip,phone['extension']))
                        if phone['protocol'] == "SIP":
                            logger.debug("SIP/IAX2 peer headers differ, put SIP specific updates here.")
                        elif phone['protocol'] == "IAX2":
                            logger.debug("SIP/IAX2 peer headers differ, put IAX2 specific updates here.")


                ami.logoff()
            except asterisk.manager.ManagerSocketException as err:
                errno, reason = err
                logger.info("Error connecting to the manager: %s", reason)
            except asterisk.manager.ManagerAuthException as reason:
                logger.info("Error logging in to the manager: %s", reason)
            except asterisk.manager.ManagerException as reason:
                logger.info("Error: %s", reason)

        finally:
            # remember to clean up
            ami.close()

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
