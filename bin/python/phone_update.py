#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, re, time, pprint, gc
import pystrix, threading, argparse

import MySQLdb, logging, pystrix

from osdial import OSDial

PROGNAME = 'osdial_phone_update'
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

    logger.info("Starting phoneupdate_process()")
    phoneupdate_process(logger)

gotEvent = False
eventdata = []
def phoneupdate_process(logger):
    global gotEvent
    global eventdata
    """
    This routine verifies the registered IP of the phone against the database.
    """
    osdial = OSDial()

    CIDdate = time.strftime('%y%m%d%H%M%S', time.gmtime(time.time()))
    now_date = time.strftime('%Y-%m-%d %H:%M:%S', time.gmtime(time.time()))

    logger.info(" - Scanning SIP phones")
    osdial.sql().execute("SELECT SQL_NO_CACHE extension,phone_ip FROM phones WHERE server_ip=%s AND protocol='SIP';", (osdial.VARserver_ip))
    phcnt = osdial.sql().rowcount
    phones = []
    if phcnt > 0:
        for row in osdial.sql().fetchall():
            phones.append({"extension":row['extension'],"phone_ip":row['phone_ip']})

        try:
            ami = pystrix.ami.Manager(logger=logger,aggregate_timeout=0)
            ami.connect(osdial.server['telnet_host'], osdial.server['telnet_port'])
            action_id = '~U'+str(time.time())+"~Challenge"
            challenge_response = ami.send_action(pystrix.ami.core.Challenge(), action_id=action_id, ActionID=action_id)
            if challenge_response and challenge_response.success:
                action = pystrix.ami.core.Login(osdial.server['ASTmgrUSERNAME'], osdial.server['ASTmgrSECRET'], challenge=challenge_response.result['Challenge'])
                action_id = '~U'+str(time.time())+"~Login"
                ami.send_action(action, action_id=action_id, ActionID=action_id)
            else:
                raise ConnectionError("Asterisk did not provide an MD5 challenge token" + (challenge_response is None and ': timed out' or ''))
        except pystrix.ami.ManagerSocketError as e:
            raise ConnectionError("Unable to connect to Asterisk server: %(error)s" % {'error': str(e), })
        except pystrix.ami.core.ManagerAuthError as reason:
            raise ConnectionError("Unable to authenticate to Asterisk server: %(reason)s" % {'reason': reason, })
        except pystrix.ami.ManagerError as reason:
            raise ConnectionError("An unexpected Asterisk error occurred: %(reason)s" % {'reason': reason, })
    
        gotEvent = False
        eventdata = []
        logger.info(" - Sending AMI request for SIP peer info")
        ami.register_callback('PeerEntry', handle_data)
        ami.register_callback('PeerlistComplete', handle_data)
        action = pystrix.ami.core.SIPpeers()
        action_id = '~U'+str(time.time())+"~SIPshowpeer"
        res =  ami.send_action(action, action_id=action_id, ActionID=action_id)

        while gotEvent is False:
            time.sleep(0.1)

        for phone in phones:
            for event in eventdata:
                if event['ObjectName'] == phone['extension']:
                    osdial.sql().execute("UPDATE phones SET picture=%s WHERE server_ip=%s AND extension=%s;", (event['Status'],osdial.VARserver_ip,phone['extension']))
                    if event['Channeltype'] == 'SIP':
                        if event['IPaddress'] != "-none-":
                            """
                            osdial.sql().execute("UPDATE phones SET phone_ip=%s WHERE server_ip=%s AND extension=%s;", (event['IPaddress'],osdial.VARserver_ip,phone['extension']))
                            """


        action = pystrix.ami.core.Logoff()
        action_id = '~U'+str(time.time())+"~Logoff"
        response = ami.send_action(action, action_id=action_id, ActionID=action_id)

        ami.close()
        ami = None

    osdial.close()
    osdial = None
    sys.exit(0)

def handle_data(event, manager):
    global gotEvent
    global evendata
    if event['Event'] == 'PeerEntry':
        eventdata.append(event)
    elif event['Event'] == 'PeerlistComplete':
        gotEvent = True

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
