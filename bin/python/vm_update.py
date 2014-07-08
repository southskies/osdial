#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging
import asterisk.manager

from osdial import OSDial

PROGNAME = 'osdial_vm_update'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_vm_update - updates voicemail counts.')
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
        logger = logging.getLogger('vmupdate')
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

    logger.info("Starting vmupdate_process()")
    vmupdate_process()


def vmupdate_process():
    """
    This pools the Asterisk AMI for all voicemail box counts.
    """
    osdial = OSDial()
    logger = logging.getLogger('vmupdate')

    logger.info(" - Scanning phones table")
    osdial.sql().execute("SELECT extension,voicemail_id,messages,old_messages FROM phones WHERE server_ip=%s AND voicemail_id!='';", (osdial.VARserver_ip))
    phones = []
    phcnt = osdial.sql().rowcount
    if phcnt > 0:
        for row in osdial.sql().fetchall():
            phones.append({"extension":row['extension'],"voicemail_id":"%s@osdial" % row['voicemail_id'],
                           "messages":int(row['messages']),"old_messages":int(row['old_messages'])})


    if len(osdial.server['ASTmgrUSERNAMEsend']) > 3:
        osdial.server['ASTmgrUSERNAME'] = osdial.server['ASTmgrUSERNAMEsend']

    ami = asterisk.manager.Manager()
    try:
        try:
            ami.connect(osdial.server['telnet_host'], port=osdial.server['telnet_port'])
            ami.login(osdial.server['ASTmgrUSERNAME'], osdial.server['ASTmgrSECRET'])

            for phone in phones:
                mailhdr = {}
                logger.info(" - Sending AMI request for voicemail box "+phone['voicemail_id'])
                response = ami.mailbox_count(phone['voicemail_id'])
                if re.search('Success|Follows',response['Response']):
                    for mhdr in response.response:
                        if re.search(': ', mhdr):
                            [k,v] = re.split(': ', re.sub('\r|\n','',mhdr), maxsplit=1)
                            mailhdr[re.sub('\W','',k)] = v

                    origmsgcnt = phone['messages']
                    origomsgcnt = phone['old_messages']
                    if mailhdr.has_key('NewMessages'):
                        phone['messages'] = int(mailhdr['NewMessages'])
                    if mailhdr.has_key('UrgMessages'):
                        phone['messages'] += int(mailhdr['UrgMessages'])
                    if mailhdr.has_key('OldMessages'):
                        phone['old_messages'] = int(mailhdr['OldMessages'])
                    if origmsgcnt == phone['messages'] and origomsgcnt == phone['old_messages']:
                        logger.info(" - Got response, no change, not updating record. %d/%d" % (phone['messages'], phone['old_messages']))
                    else:
                        logger.info(" - Got response, updating phone record. %d/%d" % (phone['messages'], phone['old_messages']))
                        osdial.sql().execute("UPDATE phones SET messages=%s,old_messages=%s WHERE server_ip=%s AND extension=%s;", (phone['messages'],phone['old_messages'],osdial.VARserver_ip,phone['extension']))

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
