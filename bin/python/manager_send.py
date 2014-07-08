#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb
import logging

import asterisk.manager

from osdial import OSDial

from multiprocessing import Process,Manager
import multiprocessing

PROGNAME = 'osdial_manager_send'
VERSION = '0.1'
opt = {'verbose':False,'loglevel':False,'sendonlyone':False,'debug':False,'test':False,'daemon':False}

def send_action_child(SACinput, SACoutput):
    mydata = SACinput.get()
    returnsql = None
    logger = logging.getLogger('child')

    ami = asterisk.manager.Manager()
    try:
        try:
            ami.connect(mydata['telnet_host'], port=mydata['telnet_port'])
            ami.login(mydata['ASTmgrUSERNAME'], mydata['ASTmgrSECRET'])

            if re.search('XXYYXXYYXXYYXX',mydata['cmd_line_b']):
                action_id = 'M'+str(mydata['man_id'])+'~U'+str(time.time())+"~MeetmeList"
                action = {'Action':'Command','ActionID':action_id,'Command':'meetme list %s concise' % mydata['cmd_line_k']}
                response = ami.send_action(action)
                participant = '1'
                if re.search('Follows',response['Response']):
                    for line in response.response:
                        if re.search('^\d+\!',line):
                            meetmeuser = re.split('\!', re.sub('\r|\n','',line))
                            if re.search(mydata['cmd_line_j'],meetmeuser[3]):
                                participant = meetmeuser[0]
                mydata['cmd_line_b'] = re.sub('XXYYXXYYXXYYXX',participant,mydata['cmd_line_b'])
                mydata['cmd_line_j'] = '';
                mydata['cmd_line_k'] = '';
                logger.debug(action)

            action_id = 'M'+str(mydata['man_id'])+'~U'+str(time.time())+"~"+mydata['action']
            action = {'Action':mydata['action'],'ActionID':action_id}
            for cmd in ('b','c','d','e','f','g','h','i','j','k'):
                if mydata.has_key('cmd_line_%s' % cmd) and re.search('^\S+: \S+', mydata['cmd_line_%s' % cmd]):
                    p1 = re.sub('^(\S+): (.*)','\g<1>',mydata['cmd_line_%s' % cmd])
                    p2 = re.sub('^\S+: (.*)','\g<1>',mydata['cmd_line_%s' % cmd])
                    action[p1] = p2
            logger.debug(action)

            response = ami.send_action(action)
            if re.search('Follows',response['Response']):
                if re.search('Command', response.headers['ActionID']):
                    (manid,uniqueid,cmd) = re.split('~',response.headers['ActionID'])
                    manid = re.sub('^M','',manid)
                    uniqueid = re.sub('^U','',uniqueid)
                    logger.info("Marking completed command (%s) as DEAD.", manid)
                    SACoutput.put(["UPDATE osdial_manager SET status='DEAD',uniqueid=%s WHERE man_id=%s;", uniqueid, manid])
            elif re.search('Error',response['Response']):
                if re.search('Hangup', response.headers['ActionID']):
                    (manid,uniqueid,cmd) = re.split('~',response.headers['ActionID'])
                    manid = re.sub('^M','',manid)
                    uniqueid = re.sub('^U','',uniqueid)
                    for line in response.response:
                        if re.search('No such channel',line,re.IGNORECASE):
                            logger.info("Marking failed hangup (%s) as DEAD.", manid)
                            SACoutput.put(["UPDATE osdial_manager SET status='DEAD' WHERE man_id=%s;", manid])

            ami.logoff()
        except asterisk.manager.ManagerSocketException as err:
            errno, reason = err
            logger.error("Error connecting to the manager: %s" % reason)
            pass
        except asterisk.manager.ManagerAuthException as reason:
            logger.error("Error logging in to the manager: %s" % reason)
            pass
        except asterisk.manager.ManagerException as reason:
            logger.error("Error: %s" % reason)
            pass
    finally:
        try:
            ami.close()
        except Exception as e:
            logger.warning("AMI closure failed. %s" % type(e))


def manager_send():
    """
    The routine responsible for monitoring osdial_manager for new entries and
    relaying those to Asterisk via AMI.
    """
    osdial = None
    logger = logging.getLogger('manager')
    send_logger = logging.getLogger('child')
    try:
        osdial = OSDial()
        while True:
            procs = []
            pcnt = 0
            SACinput = multiprocessing.Queue()
            SACoutput = multiprocessing.Queue()
            osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_manager WHERE server_ip=%s AND status='NEW';", (osdial.VARserver_ip))
            new_actions = 0
            queue_actions = 0
            for row in osdial.sql().fetchall():
                new_actions = row['cnt']
            if new_actions:
                logger.info("%s NEW actions to send on %s.", new_actions, osdial.VARserver_ip)
                osdial.sql().execute("UPDATE osdial_manager SET status='QUEUE' WHERE server_ip=%s AND status='NEW' ORDER BY man_id ASC;", (osdial.VARserver_ip))
                queue_actions = osdial.sql().rowcount
            if queue_actions:
                logger.info("%s rows changed to QUEUE on %s.", queue_actions, osdial.VARserver_ip)
                osdial.sql().execute("SELECT * FROM osdial_manager WHERE server_ip=%s AND status='QUEUE' ORDER BY man_id ASC;", (osdial.VARserver_ip)) 
                if osdial.sql().rowcount:
                    rows = osdial.sql().fetchall()
                    for row in rows:
                        osdial.sql().execute("UPDATE osdial_manager SET status='SENT' WHERE man_id=%s AND status='QUEUE';", (row['man_id']))
                        logger.info("Processing QUEUEd entry %s, marking as SENT and sending to the AMI on %s.|%s|%s|%s|%s", row['man_id'], osdial.VARserver_ip, row['uniqueid'], row['channel'], row['action'], row['callerid'])
                        send_command = True
                        if re.search('^(Hangup|Redirect)$',row['action'],flags=re.IGNORECASE):
                            logger.info("Checking for DEAD call before sending|%s|%s", row['uniqueid'], row['callerid'])
                            osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_manager WHERE server_ip=%s AND callerid=%s AND status='DEAD';", (osdial.VARserver_ip, row['callerid']))
                            dead_count = 0
                            for row2 in osdial.sql().fetchall():
                                dead_count = row2['cnt']
                            if dead_count:
                                logger.info("Not sending as command is already marked as DEAD|%s|%s", row['uniqueid'], row['callerid'])
                                send_command = False
                            else:
                                send_command = True
                        if not send_command:
                            osdial.sql().execute("UPDATE osdial_manager SET status='DEAD' WHERE man_id=%s;", (row['man_id']))
                        else:
                            pcnt += 1
                            data = {}
                            data['telnet_host'] = osdial.server['telnet_host']
                            data['telnet_port'] = osdial.server['telnet_port']
                            data['ASTmgrUSERNAME'] = osdial.server['ASTmgrUSERNAME']
                            data['ASTmgrSECRET'] = osdial.server['ASTmgrSECRET']
                            data['asterisk_version'] = osdial.server['asterisk_version']
                            data['man_id'] = row['man_id']
                            data['action'] = row['action']
                            for cmd in ('b','c','d','e','f','g','h','i','j','k'):
                                if row.has_key('cmd_line_%s'% cmd):
                                    if row['cmd_line_%s' % cmd]:
                                        data['cmd_line_%s' % cmd] = row['cmd_line_%s' % cmd]
                            SACinput.put(data)
                            logger.debug("Starting send_action_child process.")
                            send_logger.debug("Starting send_action_child process.")
                            p = Process(target=send_action_child, args=(SACinput, SACoutput))
                            procs.append(p)
                            p.start()
                    for p in procs:
                        p.join()
                        if not SACoutput.empty():
                            data = SACoutput.get()
                            if data:
                                osdial.sql().execute(data[0], (data[1:]))
            time.sleep(0.05)
    except Exception as e:
        logger.error('%s: %s', e, type(e))
        pass
    if osdial:
        osdial.close()
        osdial = None
    gc.collect()
                    


def main(argv):
    parser = argparse.ArgumentParser(description='osdial_manager_send - sends queued manager requests to Asterisk.')
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
        if os.geteuid() == 0:
            astpwd = pwd.getpwnam('asterisk');
            os.setegid(astpwd.pw_gid)
            os.seteuid(astpwd.pw_uid)
    except KeyError, e:
        pass

    if opt['daemon']:
        daemonize()

    osdspt = None
    try:
        osdspt = OSDial()
        FORMAT = '%(asctime)s|%(name)s:%(process)d:%(lineno)d|%(levelname)s|%(message)s'
        logger = logging.getLogger('manager')
        send_logger = logging.getLogger('child')
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

        handler = logging.FileHandler('%s/action_process.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
        handler.setLevel(logdeflvl)
        handler.setFormatter(formatter)
        logger.addHandler(handler)

        handler = logging.FileHandler('%s/action_full.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
        handler.setLevel(logdeflvl)
        handler.setFormatter(formatter)
        send_logger.addHandler(handler)

        if opt['verbose'] or opt['debug']:
            handler = logging.StreamHandler()
            handler.setLevel(logdeflvl)
            handler.setFormatter(formatter)
            logger.addHandler(handler)
            send_logger.addHandler(handler)

        logger.setLevel(logdeflvl)
        send_logger.setLevel(logdeflvl)

        sptres = osdspt.server_process_tracker(PROGNAME, osdspt.VARserver_ip, os.getpid(), True)
        osdspt.close()
        osdspt = None
        if sptres:
            logger.error("Error process already running!")
            sys.exit(1)
    except MySQLdb.OperationalError, e:
        logger.error("Could not connect to MySQL! %s", e)
        sys.exit(1)
    gc.collect()

    logger.info("Starting manager_send()")
    while True:
        manager_send()
        time.sleep(0.05)

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
