#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging
import asterisk.manager

from osdial import OSDial

PROGNAME = 'osdial_manager_listen'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_manager_listen - monitor AMI for responses.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
    parser.add_argument('-d', '--daemon', action='store_true', help='Puts process in daemon mode.',dest='daemon')
    parser.add_argument('-l', '--logLevel', action='store', default='INFO', choices=['CRITICAL','ERROR','WARNING','INFO','DEBUG'], help='Sets the level of output verbosity.', dest='loglevel')
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

    if opt['daemon']:
        daemonize()

    osdspt = None
    try:
        osdspt = OSDial()
        FORMAT = '%(asctime)s|%(name)s:%(lineno)d|%(levelname)s|%(message)s'
        logger = logging.getLogger('listen')
        proclogger = logging.getLogger('process')
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

        handler = logging.FileHandler('%s/listen.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
        handler.setLevel(logdeflvl)
        handler.setFormatter(formatter)
        logger.addHandler(handler)

        prochandler = logging.FileHandler('%s/listen_process.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
        prochandler.setLevel(logdeflvl)
        prochandler.setFormatter(formatter)
        proclogger.addHandler(prochandler)

        if opt['verbose'] or opt['debug']:
            streamhandler = logging.StreamHandler()
            streamhandler.setLevel(logdeflvl)
            streamhandler.setFormatter(formatter)
            logger.addHandler(streamhandler)
            proclogger.addHandler(streamhandler)

        logger.setLevel(logdeflvl)
        proclogger.setLevel(logdeflvl)

        sptres = osdspt.server_process_tracker(PROGNAME, osdspt.VARserver_ip, os.getpid(), True)
        osdspt.close()
        osdspt = None
        if sptres:
            proclogger.error("Error process already running!")
            sys.exit(1)
    except MySQLdb.OperationalError, e:
        proclogger.error("Could not connect to MySQL! %s", e)
        sys.exit(1)
    gc.collect()

    proclogger.info("Starting listen_process()")
    listen_process()

def daemonize():
    if os.fork() != 0:
        os._exit(0)

    os.setsid()

    if os.fork() != 0:
        os._exit(0)

    os.chdir("/")
    os.umask(022)
    [os.close(i) for i in xrange(3)]
    os.open(os.devnull, os.O_RDWR)
    os.dup2(0, 1)
    os.dup2(0, 2)


def listen_process():
    """
    This monitors the Asterisk AMI for events.
    """
    osdial = OSDial()

    if len(osdial.server['ASTmgrUSERNAMElisten']) > 3:
        osdial.server['ASTmgrUSERNAME'] = osdial.server['ASTmgrUSERNAMElisten']

    proclogger = logging.getLogger('process')
    logger = logging.getLogger('listen')
    ami = asterisk.manager.Manager()
    ami.exitnow = False
    while not ami.exitnow:
        ami.osdial = osdial
        ami.islive = True
        try:
            try:
                ami.connect(osdial.server['telnet_host'], port=osdial.server['telnet_port'])
                ami.login(osdial.server['ASTmgrUSERNAME'], osdial.server['ASTmgrSECRET'])

                ami.register_event('VMChangePassword', handle_vmchangepassword)
                ami.register_event('MeetmeEnd', handle_meetmeend)
                ami.register_event('Shutdown', handle_shutdown)
                ami.register_event('Hangup', handle_hangup)
                ami.register_event('Rename', handle_rename)
                ami.register_event('NewAccountCode', handle_newaccountcode)
                ami.register_event('NewCallerid', handle_newaccountcode)
                ami.register_event('RedirectStatus', handle_redirectstatus)
                ami.register_event('Newstate', handle_newstate)
                ami.register_event('Dial', handle_dial)
                ami.register_event('CPAResult', handle_cparesult)
                ami.register_event('*', handle_event)

                while ami.islive:
                    response = ami.ping()
                    if not re.search('Success',response['Response']):
                        ami.islive = False
                    else:
                        time.sleep(30)

                if ami.islive or ami.connected():
                    ami.logoff()
            except asterisk.manager.ManagerSocketException as err:
                errno, reason = err
                proclogger.error("Error connecting to the manager: %s", reason)
                time.sleep(15)
            except asterisk.manager.ManagerAuthException as reason:
                proclogger.error("Error logging in to the manager: %s", reason)
                ami.exitnow = True
            except asterisk.manager.ManagerException as reason:
                proclogger.error("Error: %s", reason)
                time.sleep(15)
            except KeyboardInterrupt, e:
                proclogger.info("Exiting")
                ami.exitnow = True

        finally:
            ami.osdial = None
            ami.close()

    osdial.close()
    osdial = None
    sys.exit(0)


def fix_headers(event):
    event.headers['processed'] = True
    for hkey in event.headers.keys():
        event.headers[hkey.lower()] = event.headers[hkey]
        if not re.search(hkey.lower(),hkey):
            del event.headers[hkey]
    if (event.headers.has_key('destination')):
        event.headers['destination'] = re.sub('\s*$','',event.headers['destination'])
    for hkey in event.headers.keys():
        if re.search('^callerid',hkey,re.IGNORECASE):
            event.headers[hkey] = re.sub('\s*$','',event.headers[hkey])
            event.headers[hkey] = re.sub('^"','',event.headers[hkey])
            event.headers[hkey] = re.sub('".*$','',event.headers[hkey])
    if (event.headers.has_key('calleridnum')):
        event.headers['callerid'] = event.headers['calleridnum']
    if (event.headers.has_key('channelstatedesc')):
        event.headers['state'] = event.headers['channelstatedesc']
    if (event.headers.has_key('uniqueid')):
        event.headers['srcuniqueid'] = event.headers['uniqueid']
    if (event.headers.has_key('account')):
        event.headers['accountcode'] = event.headers['account']

    for line in event.message.response:
        if (re.search('<ZOMBIE>',line)):
            event.headers['zombie'] = True
        if (re.search('<MASQ>',line)):
            event.headers['masq'] = True
        if (re.search('AsyncGoto/',line)):
            event.headers['asyncgoto'] = True
        if event.headers.has_key('channel'):
            event.headers['channel'] = re.sub('<ZOMBIE>','',event.headers['channel'])
            event.headers['channel'] = re.sub('<MASQ>','',event.headers['channel'])
            event.headers['channel'] = re.sub('AsyncGoto/','',event.headers['channel'])


def handle_vmchangepassword(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    if re.search('@osdial$',event.headers['mailbox']):
        updated = ami.osdial.sql().execute("UPDATE phones SET voicemail_password=%s WHERE voicemail_id=%s AND server_ip=%s;", (event.headers['newpassword'],re.sub('@osdial$','',event.headers['mailbox']),ami.osdial.VARserver_ip))
        logger.info("Got voicemail password change request, updating database. (%s)\n\t%s\n\t%s", updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
    else:
        logger.info("Got voicemail password change request, but not an 'osdial' box.\n\t%s\n\t%s", event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_meetmeend(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    ami.osdial.sql().execute("SELECT * FROM osdial_conferences WHERE server_ip=%s AND conf_exten=%s LIMIT 1;", (ami.osdial.VARserver_ip,event.headers['meetme']))
    for row in ami.osdial.sql().fetchall():
        if re.search('3WAY',row['extension']):
            updated = ami.osdial.sql().execute("UPDATE osdial_conferences SET extension='',leave_3way='0',leave_3way_datetime=NOW() WHERE server_ip=%s AND conf_exten=%s;", (ami.osdial.VARserver_ip,event.headers['meetme']))
            logger.info("End of Meetme 3WAY osdial_conference %s for %s. (%s)\n\t%s\n\t%s", event.headers['meetme'], row['extension'], updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_shutdown(event, ami):
    logger = logging.getLogger('listen.handle')
    proclogger = logging.getLogger('process.handle')
    fix_headers(event)
    ami.exitnow = True
    ami.islive = False
    logger.info("Asterisk server shutdown.\n\t%s\n\t%s", event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
    proclogger.info("Asterisk server shutdown.")


def handle_hangup(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    updated1 = ami.osdial.sql().execute("UPDATE osdial_manager SET status='DEAD',channel=%s,response='Y' WHERE server_ip=%s AND uniqueid=%s AND callerid NOT LIKE 'DCagcW%%';", (event.headers['channel'],ami.osdial.VARserver_ip,event.headers['uniqueid']))
    if re.search('^OSDial#',event.headers['calleridname']) and re.search('^Local/.*@.*-....;.*$',event.headers['channel']):
        event.headers['channel'] = re.sub('-....;.*$','',event.headers['channel'])
    updated2 = ami.osdial.sql().execute("UPDATE osdial_manager SET status='DEAD',uniqueid=%s WHERE server_ip=%s AND channel=%s AND action='Hangup' AND callerid NOT LIKE 'DCagcW%%';", (event.headers['uniqueid'],ami.osdial.VARserver_ip,event.headers['channel']))
    logger.info("Hangup on channel, update manager as DEAD. (%s) (%s)\n\t%s\n\t%s", updated1, updated2, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_rename(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    logger.info("%s on channel, not updating.\n\t%s\n\t%s", event.name, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_newaccountcode(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    if re.search('Local',event.headers['channel']):
        updated = ami.osdial.sql().execute("UPDATE osdial_manager SET channel=%s,uniqueid=%s WHERE server_ip=%s AND callerid=%s LIMIT 1;", (event.headers['channel'],event.headers['uniqueid'],ami.osdial.VARserver_ip,event.headers['accountcode']))
        logger.info("%s on channel, update manager. (%s)\n\t%s\n\t%s", event.name, updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
    else:
        logger.info("%s on channel, not local. no update.\n\t%s\n\t%s", event.name, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_redirectstatus(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    if event.headers.has_key('accountcode') and event.headers['accountcode'] and event.headers.has_key('status'):
        if event.headers.has_key('extrastatus'):
            if re.search('Failure',event.headers['status']) and re.search('Failure',event.headers['extrastatus']):
                updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='DEAD',channel=%s,uniqueid=%s WHERE action='Redirect' AND server_ip=%s AND callerid=%s;", (event.headers['channel'],event.headers['uniqueid'],ami.osdial.VARserver_ip,event.headers['accountcode']))
                logger.info("%s dual failure on channel, update manager. (%s)\n\t%s\n\t%s", event.name, updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
            elif re.search('Success',event.headers['status']) and re.search('Failure',event.headers['extrastatus']):
                updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='UPDATED',channel=%s,uniqueid=%s WHERE action='Redirect' AND server_ip=%s AND callerid=%s;", (event.headers['channel'],event.headers['uniqueid'],ami.osdial.VARserver_ip,event.headers['accountcode']))
                logger.info("%s dual with secondary failure on channel, update manager. (%s)\n\t%s\n\t%s", event.name, updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
            elif re.search('Success',event.headers['status']) and re.search('Success',event.headers['extrastatus']):
                updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='UPDATED',channel=%s,uniqueid=%s WHERE action='Redirect' AND server_ip=%s AND callerid=%s;", (event.headers['channel'],event.headers['uniqueid'],ami.osdial.VARserver_ip,event.headers['accountcode']))
                logger.info("%s dual success on channel, update manager. (%s)\n\t%s\n\t%s", event.name, updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
        else:
            if re.search('Failure',event.headers['status']):
                updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='DEAD',channel=%s,uniqueid=%s WHERE action='Redirect' AND server_ip=%s AND callerid=%s;", (event.headers['channel'],event.headers['uniqueid'],ami.osdial.VARserver_ip,event.headers['accountcode']))
                logger.info("%s failure on channel, update manager. (%s)\n\t%s\n\t%s", event.name, updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
            elif re.search('Success',event.headers['status']):
                updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='UPDATED',channel=%s,uniqueid=%s WHERE action='Redirect' AND server_ip=%s AND callerid=%s;", (event.headers['channel'],event.headers['uniqueid'],ami.osdial.VARserver_ip,event.headers['accountcode']))
                logger.info("%s success on channel, update manager. (%s)\n\t%s\n\t%s", event.name, updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_newstate(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    if event.headers.has_key('accountcode'):
        # look for special osdial conference call event.
        if re.search('^[AD]CagcW',event.headers['accountcode']):
            if re.search('^Up',event.headers['state']):
                updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='UPDATED',channel=%s,uniqueid=%s WHERE server_ip=%s AND callerid=%s;", (event.headers['channel'],event.headers['srcuniqueid'],ami.osdial.VARserver_ip,event.headersp['accountcode']))
                logger.info("Newstate (%s) on DCagcW channel, update manager. (%s)\n\t%s\n\t%s", event.headers['state'], updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
            else:
                logger.info("Unhandled newstate (%s).\n\t%s\n\t%s", event.headers['state'], event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
        else:
            if re.search('Dialing|Ringing',event.headers['state']):
                updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='SENT',channel=%s WHERE server_ip=%s AND callerid=%s;", (event.headers['channel'],ami.osdial.VARserver_ip,event.headers['accountcode']))
                logger.info("Newstate (%s) on channel, update manager. (%s)\n\t%s\n\t%s", event.headers['state'], updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
            elif re.search('^Up',event.headers['state']):
                if re.search('Local',event.headers['channel']) and not re.search('^S',event.headers['accountcode']):
                    logger.info("Newstate (%s) on channel, Local channel, not updating manager.\n\t%s\n\t%s", event.headers['state'], event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
                else:
                    updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='UPDATED',channel=%s WHERE server_ip=%s AND callerid=%s;", (event.headers['channel'],ami.osdial.VARserver_ip,event.headers['accountcode']))
                    logger.info("Newstate (%s) on channel, update manager. (%s)\n\t%s\n\t%s", event.headers['state'], updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
            else:
                logger.info("Unhandled newstate (%s).\n\t%s\n\t%s", event.headers['state'], event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
    else:
        logger.info("Unhandled newstate (%s), no accountcode.\n\t%s\n\t%s", event.headers['state'], event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_dial(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    if event.headers.has_key('accountcode') and re.search('^[AD]CagcW',event.headers['accountcode']):
        if event.headers.has_key('destination') and not event.headers['destination']:
            logger.info("Unhandled dial on DCagcW channel, not initiation.\n\t%s\n\t%s", event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
        elif event.headers.has_key('destination') and re.search('^Local',event.headers['destination']):
            logger.info("Unhandled dial on DCagcW channel, Local channel.\n\t%s\n\t%s", event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
        else:
            updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='UPDATED',channel=%s,uniqueid=%s WHERE server_ip=%s AND callerid=%s;", (event.headers['destination'],event.headers['srcuniqueid'],ami.osdial.VARserver_ip,event.headers['accountcode']))
            logger.info("Dial on DCagcW non-local channel. (%s)\n\t%s\n\t%s", updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
        
    elif event.headers.has_key('subevent') and re.search('Begin',event.headers['subevent']):
        if event.headers.has_key('destination'):
            if not re.search('^Local',event.headers['destination']):
                updated = ami.osdial.sql().execute("UPDATE osdial_manager SET status='SENT',channel=%s,uniqueid=%s WHERE server_ip=%s AND callerid=%s;", (event.headers['destination'],event.headers['uniqueid'],ami.osdial.VARserver_ip,event.headers['accountcode']))
                logger.info("Dial on non-Local channel destination. (%s)\n\t%s\n\t%s", updated, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
            else:
                logger.info("Dial on Local channel destination.\n\t%s\n\t%s", event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_cparesult(event, ami):
    logger = logging.getLogger('listen.handle')
    fix_headers(event)
    nomatch = True
    if event.headers.has_key('accountcode') and event.headers.has_key('channel') and event.headers.has_key('uniqueid') and event.headers.has_key('cparesult'):
        if event.headers['accountcode'] and event.headers['channel'] and event.headers['uniqueid'] and event.headers['cparesult']:
            if not re.search('UNKNOWN',event.headers['accountcode'], re.IGNORECASE) and not re.search('UNKNOWN',event.headers['channel'], re.IGNORECASE) and not re.search('UNKNOWN',event.headers['uniqueid'], re.IGNORECASE):
                nomatch = False
                inscnt = ami.osdial.sql().execute("INSERT INTO osdial_cpa_log SET callerid=%s,uniqueid=%s,lead_id=%s,server_ip=%s,channel=%s,cpa_result=%s,cpa_detailed_result=%s,cpa_call_id=%s,cpa_reference_id=%s,cpa_campaign_name=%s,event_date=NOW();", (event.headers['accountcode'],event.headers['uniqueid'],int(event.headers['accountcode'][11:]),ami.osdial.VARserver_ip,event.headers['channel'],event.headers['cparesult'],event.headers['cpadetailedresult'],event.headers['cpacallid'],event.headers['cpareferenceid'],event.headers['cpacampaignname']))
                logger.info("%s: Adding log entry. channel:%s uniqueid:%s lead_id:%s cpa_result:%s. (%s)\n\t%s\n\t%s", event.name, event.headers['channel'], event.headers['uniqueid'], int(event.headers['accountcode'][11:]), event.headers['cparesult'], inscnt, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))
    if nomatch:
        logger.info("%s: Missing required header. channel:%s uniqueid:%s cpa_result:%s.\n\t%s\n\t%s", event.name, event.headers['channel'], event.headers['uniqueid'], event.headers['cparesult'], event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


def handle_event(event, ami):
    logger = logging.getLogger('listen.handle')
    if not event.headers.has_key('processed'):
        logger.info("Unhandled event (%s).\n\t%s\n\t%s", event.name, event.headers, re.sub('\r\n','\n\t',''.join(event.message.response)))


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
