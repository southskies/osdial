#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging
import asterisk.manager

from osdial import OSDial

PROGNAME = 'osdial_conf_update'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_conf_update - updates conferences and times out inactive conferences.')
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
        logger = logging.getLogger('confupdate')
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

    logger.info("Starting confupdate_process()")
    confupdate_process()

def confupdate_process():
    global gotConf
    global mmlist
    """
    This routine verifies the current use of a conference and frees the channel, if needed.
    """
    osdial = OSDial()
    logger = logging.getLogger('confupdate')

    CIDdate = time.strftime('%y%m%d%H%M%S', time.localtime(time.time()))
    now_date = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))
    two_hours_ago = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time() - (60*60*2)))

    logger.info(" - Looking for expired (2hrs) conference entries from 3way calls")
    osdial.sql().execute("SELECT conf_exten FROM osdial_conferences WHERE server_ip=%s AND leave_3way='1' AND leave_3way_datetime<%s;", (osdial.VARserver_ip, two_hours_ago))
    occnt = osdial.sql().rowcount
    if occnt > 0:
        exp_confs = []
        for row in osdial.sql().fetchall():
            exp_confs.append(row['conf_exten'])
        for conf in exp_confs:
            queryCID = "ULGC32%s" % CIDdate
            logger.info(" - Kicking expired 3way conference %s" % conf)
            osdial.sql().execute("INSERT INTO osdial_manager SET entry_date=%s,status='NEW',response='N',server_ip=%s,action='Command',callerid=%s,cmd_line_b=%s;", (now_date,osdial.VARserver_ip,queryCID,"Command: meetme kick %s all" % conf))
            osdial.sql().execute("UPDATE osdial_conferences SET extension='',leave_3way='0' WHERE server_ip=%s AND conf_exten=%s;", (osdial.VARserver_ip, conf))


    if len(osdial.server['ASTmgrUSERNAMEsend']) > 3:
        osdial.server['ASTmgrUSERNAME'] = osdial.server['ASTmgrUSERNAMEsend']

    ami = asterisk.manager.Manager()
    try:
        try:
            ami.connect(osdial.server['telnet_host'], port=osdial.server['telnet_port'])
            ami.login(osdial.server['ASTmgrUSERNAME'], osdial.server['ASTmgrSECRET'])
    
            logger.info(" - Scanning conference channels marked as leave_3way")
            osdial.sql().execute("SELECT extension,conf_exten FROM osdial_conferences WHERE server_ip=%s AND leave_3way='1';", (osdial.VARserver_ip))
            occnt = osdial.sql().rowcount
            confs = []

            if occnt > 0:
                for row in osdial.sql().fetchall():
                    confs.append({"extension":row['extension'],"conf_exten":row['conf_exten']})

                for conf in confs:
                    conf_empty = 0
                    mmlist = []
                    logger.info(" - Sending AMI request for conference list %s" % conf['conf_exten'])
                    action_id = 'C'+str(conf['conf_exten'])+'~U'+str(time.time())+"~MeetmeList"
                    action = {'Action':'Command','ActionID':action_id,'Command':'meetme list %s concise' % conf['conf_exten']}
                    response = ami.send_action(action)
                    if re.search('Follows',response['Response']):
                        for line in response.response:
                            if re.search('^\d+\!',line):
                                meetmeuser = re.split('\!', re.sub('\r|\n','',line))
                                mmlist.append({'Conference':conf['conf_exten'],
                                    'UserNumber':meetmeuser[0],
                                    'CallerIDNum':meetmeuser[1],
                                    'CallerIDName':meetmeuser[2],
                                    'Channel':meetmeuser[3],
                                    'Admin':meetmeuser[4],
                                    'ListenOnly':meetmeuser[5],
                                    'Muted':meetmeuser[6],
                                    'TalkRequest':meetmeuser[7],
                                    'Talking':meetmeuser[8],
                                    'Time':meetmeuser[9]})

                    if len(mmlist) <= 1:
                        conf_empty += 1

                    if len(mmlist) <= 2:
                        for meetme in mmlist:
                            if re.search("Local/3%s@" % conf,meetme['Channel']) and meetme['ListenOnly']:
                                conf_empty += 1
                                queryCID = "ULGC38%s" % CIDdate
                                osdial.sql().execute("INSERT INTO osdial_manager SET entry_date=%s,status='NEW',response='N',server_ip=%s,action='Command',callerid=%s,cmd_line_b=%s;", (now_date,osdial.VARserver_ip,queryCID,"Command: meetme kick %s %s" % (conf,meetme['UserNumber'])))

                    if conf_empty == 0:
                        if re.search("Xtimeout\d$", conf['extension'], re.IGNORECASE):
                            conf['extension'] = re.sub("Xtimeout\d$","",conf['extension'],re.IGNORECASE)
                            osdial.sql().execute("UPDATE osdial_conferences SET extension=%s WHERE server_ip=%s AND conf_exten=%s;", (conf['extension'],osdial.VARserver_ip,conf['conf_exten']))
                    else:
                        NEWexten = conf['extension']
                        leave_3way='1'
                        if re.search("Xtimeout3$", conf['extension'], re.IGNORECASE):
                            NEWexten = re.sub("Xtimeout3$","Xtimeout2",NEWexten,re.IGNORECASE)
                        if re.search("Xtimeout2$", conf['extension'], re.IGNORECASE):
                            NEWexten = re.sub("Xtimeout2$","Xtimeout1",NEWexten,re.IGNORECASE)
                        if re.search("Xtimeout1$", conf['extension'], re.IGNORECASE):
                            NEWexten = ""
                            leave_3way='1'
                        if not re.search("Xtimeout\d$", conf['extension'], re.IGNORECASE) and len(conf['extension']) > 0:
                            NEWexten = "%sXtimeout3" % NEWexten
                        if re.search("Xtimeout1$", NEWexten, re.IGNORECASE):
                            queryCID = "ULGC36%s" % CIDdate
                            osdial.sql().execute("INSERT INTO osdial_manager SET entry_date=%s,status='NEW',response='N',server_ip=%s,action='Command',callerid=%s,cmd_line_b=%s;", (now_date,osdial.VARserver_ip,queryCID,"Command: meetme kick %s all" % conf['conf_exten']))
                        osdial.sql().execute("UPDATE osdial_conferences SET extension=%s,leave_3way=%s WHERE server_ip=%s AND conf_exten=%s;", (NEWexten,leave_3way,osdial.VARserver_ip,conf['conf_exten']))
                    

            logger.info(" - Scanning conference channels")
            osdial.sql().execute("SELECT extension,conf_exten FROM osdial_conferences WHERE server_ip=%s AND extension IS NOT NULL AND extension!='';", (osdial.VARserver_ip))
            occnt = osdial.sql().rowcount
            confs = []

            if occnt > 0:
                for row in osdial.sql().fetchall():
                    confs.append({"extension":row['extension'],"conf_exten":row['conf_exten']})

                for conf in confs:
                    conf_empty = 0
                    mmlist = []
                    logger.info(" - Sending AMI request for conference list %s" % conf['conf_exten'])
                    action_id = 'C'+str(conf['conf_exten'])+'~U'+str(time.time())+"~MeetmeList"
                    action = {'Action':'Command','ActionID':action_id,'Command':'meetme list %s concise' % conf['conf_exten']}
                    response = ami.send_action(action)
                    if re.search('Follows',response['Response']):
                        for line in response.response:
                            if re.search('^\d+\!',line):
                                meetmeuser = re.split('\!', re.sub('\r|\n','',line))
                                mmlist.append({'Conference':conf['conf_exten'],
                                    'UserNumber':meetmeuser[0],
                                    'CallerIDNum':meetmeuser[1],
                                    'CallerIDName':meetmeuser[2],
                                    'Channel':meetmeuser[3],
                                    'Admin':meetmeuser[4],
                                    'ListenOnly':meetmeuser[5],
                                    'Muted':meetmeuser[6],
                                    'TalkRequest':meetmeuser[7],
                                    'Talking':meetmeuser[8],
                                    'Time':meetmeuser[9]})

                    if len(mmlist) < 1:
                        conf_empty += 1

                    if conf_empty == 0:
                        if re.search("Xtimeout\d$", conf['extension'], re.IGNORECASE):
                            conf['extension'] = re.sub("Xtimeout\d$","",conf['extension'],re.IGNORECASE)
                            osdial.sql().execute("UPDATE osdial_conferences SET extension=%s WHERE server_ip=%s AND conf_exten=%s;", (conf['extension'],osdial.VARserver_ip,conf['conf_exten']))
                    else:
                        NEWexten = conf['extension']
                        if re.search("Xtimeout3$", conf['extension'], re.IGNORECASE):
                            NEWexten = re.sub("Xtimeout3$","Xtimeout2",NEWexten,re.IGNORECASE)
                        if re.search("Xtimeout2$", conf['extension'], re.IGNORECASE):
                            NEWexten = re.sub("Xtimeout2$","Xtimeout1",NEWexten,re.IGNORECASE)
                        if re.search("Xtimeout1$", conf['extension'], re.IGNORECASE):
                            NEWexten = ""
                        if not re.search("Xtimeout\d$", conf['extension'], re.IGNORECASE) and len(conf['extension']):
                            NEWexten = "%sXtimeout3" % NEWexten
                        osdial.sql().execute("UPDATE osdial_conferences SET extension=%s WHERE server_ip=%s AND conf_exten=%s;", (NEWexten,osdial.VARserver_ip,conf['conf_exten']))
                
            ami.logoff()
        except asterisk.manager.ManagerSocketException as err:
            errno, reason = err
            logger.error("Error connecting to the manager: %s", reason)
        except asterisk.manager.ManagerAuthException as reason:
            logger.error("Error logging in to the manager: %s", reason)
        except asterisk.manager.ManagerException as reason:
            logger.error("Error: %s", reason)

    finally:
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
