#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, re, time, pprint, gc
import pystrix, threading, argparse

import MySQLdb, logging, pystrix

from osdial import OSDial
from osdial.sql import OSDialSQL as OSDialSQL2

from multiprocessing import Process
import multiprocessing

from pystrix.ami.ami import _Request

PROGNAME = 'osdial_manager_send'
VERSION = '0.1'
opt = {'verbose':False,'loglevel':False,'sendonlyone':False,'debug':False,'test':False,'daemon':False}
logger = None

class AMICore(object):
    _manager = None
    _kill_flag = False
    _data = {}
    logger = None
    def __init__(self):
        self._manager = pystrix.ami.Manager(aggregate_timeout=10)
        self._register_callbacks()

    def send_child(self, mylogger, mydata):
        self.logger = mylogger
        self._data = mydata

        try:
            self._manager.connect(self._data['telnet_host'], self._data['telnet_port'])
            action_id = 'M'+str(self._data['man_id'])+'~U'+str(time.time())+"~Challenge"
            challenge_response = self._manager.send_action(pystrix.ami.core.Challenge(), action_id=action_id, ActionID=action_id)

            if challenge_response and challenge_response.success:
                action = pystrix.ami.core.Login(self._data['ASTmgrUSERNAME'], self._data['ASTmgrSECRET'], challenge=challenge_response.result['Challenge'])
                action_id = 'M'+str(self._data['man_id'])+'~U'+str(time.time())+"~Login"
                self._manager.send_action(action, action_id=action_id, ActionID=action_id)
            else:
                self._kill_flag = True
                raise ConnectionError("Asterisk did not provide an MD5 challenge token" + (challenge_response is None and ': timed out' or ''))
        except pystrix.ami.ManagerSocketError as e:
            self._kill_flag = True
            raise ConnectionError("Unable to connect to Asterisk server: %(error)s" % {'error': str(e), })
        except pystrix.ami.core.ManagerAuthError as reason:
            self._kill_flag = True
            raise ConnectionError("Unable to authenticate to Asterisk server: %(reason)s" % {'reason': reason, })
        except pystrix.ami.ManagerError as reason:
            self._kill_flag = True
            raise ConnectionError("An unexpected Asterisk error occurred: %(reason)s" % {'reason': reason, })

        self._manager.monitor_connection()

        if re.search('XXYYXXYYXXYYXX',self._data['cmd_line_b']):
            action = pystrix.ami.app_meetme.MeetmeList(conference=self._data['cmd_line_b'])
            action_id = 'M'+str(self._data['man_id'])+'~U'+str(time.time())+"~MeetmeList"
            self._manager.send_action(action, action_id=action_id, ActionID=action_id)

        custcmd = {}
        if self._data.has_key('cmd_line_b') and re.search('^\S+: \S+', self._data['cmd_line_b']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_b'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_b'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_c') and re.search('^\S+: \S+', self._data['cmd_line_c']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_c'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_c'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_d') and re.search('^\S+: \S+', self._data['cmd_line_d']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_d'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_d'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_e') and re.search('^\S+: \S+', self._data['cmd_line_e']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_e'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_e'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_f') and re.search('^\S+: \S+', self._data['cmd_line_f']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_f'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_f'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_g') and re.search('^\S+: \S+', self._data['cmd_line_g']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_g'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_g'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_h') and re.search('^\S+: \S+', self._data['cmd_line_h']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_h'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_h'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_i') and re.search('^\S+: \S+', self._data['cmd_line_i']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_i'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_i'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_j') and re.search('^\S+: \S+', self._data['cmd_line_j']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_j'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_j'])
            custcmd[p1] = p2
        if self._data.has_key('cmd_line_k') and re.search('^\S+: \S+', self._data['cmd_line_k']):
            p1 = re.sub('^(\S+): (.*)','\g<1>',self._data['cmd_line_k'])
            p2 = re.sub('^\S+: (.*)','\g<1>',self._data['cmd_line_k'])
            custcmd[p1] = p2

        action = CustomRequest(self._data['action'], custcmd)
        action_id = 'M'+str(self._data['man_id'])+'~U'+str(time.time())+"~"+self._data['action']
        res = self._manager.send_action(action, action_id=action_id, ActionID=action_id)
        tmpsql = None
        response = None
        message = None
        if not response is None:
            for i in res:
                if re.search('message', str(type(i)), re.IGNORECASE):
                    message = i.copy()
                if re.search('request', str(type(i)), re.IGNORECASE):
                    response = i.copy()
                if re.search('Follows',response.result['Response'],re.IGNORECASE):
                    if re.search('Command', response.result['ActionID'], flags=re.IGNORECASE) and re.search('END COMMAND', response.result['Message'], flags=re.IGNORECASE):
                        tmpsql = ["UPDATE osdial_manager SET status='DEAD',uniqueid=%s WHERE man_id=%s;", time.time(), self._data['man_id']]
                elif re.search('Error',response.result['Response'],flags=re.IGNORECASE):
                    if re.search('Hangup', response.result['ActionID'], flags=re.IGNORECASE) and re.search('No such channel', response.result['Message'], flags=re.IGNORECASE):
                        tmpsql = ["UPDATE osdial_manager SET status='DEAD' WHERE man_id=%s;", self._data['man_id']]
        if tmpsql is None:
            tmpsql = ["UPDATE osdial_manager SET status=status WHERE man_id=%s;", self._data['man_id']]

        action = pystrix.ami.core.Logoff()
        action_id = 'M'+str(self._data['man_id'])+'~U'+str(time.time())+"~Logoff"
        response = self._manager.send_action(action, action_id=action_id, ActionID=action_id)

        return tmpsql

    def _register_callbacks(self):
        self._manager.register_callback('', self._handle_event)
        self._manager.register_callback(None, self._handle_event)
        self._manager.register_callback('Shutdown', self._handle_shutdown)

    def _handle_shutdown(self, event, manager):
        self._kill_flag = True

    def _handle_event(self, event, manager):
        """
        print "Recieved event: %s" % event.name
        """

    def is_alive(self):
        return not self._kill_flag

    def kill(self):
        self._manager.close()
        self._kill_flag = True

class CustomRequest(_Request):
    def __init__(self, action, kwargs, timeout=None, async=None):
        _Request.__init__(self, action)
        self['Async'] = async and 'true' or 'false'
        if timeout and timeout > 0:
            self['Timeout'] = str(timeout)
            self.timeout = timeout + 2000
        else:
            self.timeout = 10 * 60 * 1000
        for (key, value) in kwargs.items():
            self[key] = value



class Error(Exception):
    """
    The base class from which all exceptions native to this module inherit.
    """

class ConnectionError(Error):
    """
    Indicates that a problem occurred while connecting to the Asterisk server
    or that the connection was severed unexpectedly.
    """

def worker(wlogger, wAMIinput, wAMIoutput):
    wdata = wAMIinput.get()
    ami_core = AMICore()
    scret = ami_core.send_child(wlogger, wdata)
    ami_core.kill()
    ami_code = None
    wAMIoutput.put(scret)


def manager_process(logger):
    """
    The routine responsible for monitoring osdial_manager for new entries and
    relaying those to Asterisk via AMI.
    """
    procs = []
    pcnt = 0
    AMIinput = multiprocessing.Queue()
    AMIoutput = multiprocessing.Queue()
    osdial = None
    try:
        osdial = OSDial()
        osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_manager WHERE server_ip=%s AND status='NEW';", (osdial.VARserver_ip))
        new_actions = 0
        queue_actions = 0
        for row in osdial.sql().fetchall():
            new_actions = row['cnt']
        if new_actions > 0:
            logger.info("%s NEW actions to send on %s.", new_actions, osdial.VARserver_ip)
            osdial.sql().execute("UPDATE osdial_manager SET status='QUEUE' WHERE server_ip=%s AND status='NEW' ORDER BY man_id ASC;", (osdial.VARserver_ip))
            queue_actions = osdial.sql().rowcount
        if queue_actions > 0:
            logger.info("%s rows changed to QUEUE on %s.", queue_actions, osdial.VARserver_ip)
            osdial.sql().execute("SELECT * FROM osdial_manager WHERE server_ip=%s AND status='QUEUE' ORDER BY man_id ASC;", (osdial.VARserver_ip)) 
            if osdial.sql().rowcount > 0:
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
                        if dead_count > 0:
                            logger.info("Not sending as command is already marked as DEAD|%s|%s", row['uniqueid'], row['callerid'])
                            send_command = False
                        else:
                            send_command = True
                    if not send_command is True:
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
                        if row.has_key('cmd_line_b') and not row['cmd_line_b'] is None and not row['cmd_line_b'] == "":
                            data['cmd_line_b'] = row['cmd_line_b']
                        if row.has_key('cmd_line_c') and not row['cmd_line_c'] is None and not row['cmd_line_c'] == "":
                            data['cmd_line_c'] = row['cmd_line_c']
                        if row.has_key('cmd_line_d') and not row['cmd_line_d'] is None and not row['cmd_line_d'] == "":
                            data['cmd_line_d'] = row['cmd_line_d']
                        if row.has_key('cmd_line_e') and not row['cmd_line_e'] is None and not row['cmd_line_e'] == "":
                            data['cmd_line_e'] = row['cmd_line_e']
                        if row.has_key('cmd_line_f') and not row['cmd_line_f'] is None and not row['cmd_line_f'] == "":
                            data['cmd_line_f'] = row['cmd_line_f']
                        if row.has_key('cmd_line_j') and not row['cmd_line_g'] is None and not row['cmd_line_g'] == "":
                            data['cmd_line_g'] = row['cmd_line_g']
                        if row.has_key('cmd_line_h') and not row['cmd_line_h'] is None and not row['cmd_line_h'] == "":
                            data['cmd_line_h'] = row['cmd_line_h']
                        if row.has_key('cmd_line_i') and not row['cmd_line_i'] is None and not row['cmd_line_i'] == "":
                            data['cmd_line_i'] = row['cmd_line_i']
                        if row.has_key('cmd_line_j') and not row['cmd_line_j'] is None and not row['cmd_line_j'] == "":
                            data['cmd_line_j'] = row['cmd_line_j']
                        if row.has_key('cmd_line_k') and not row['cmd_line_k'] is None and not row['cmd_line_k'] == "":
                            data['cmd_line_k'] = row['cmd_line_k']
                        AMIinput.put(data)
    except Exception, e:
        logger.error('%s', e)
        pass
    if pcnt > 0:
        logger.info("Starting procs.")
        for w in xrange(pcnt):
            p = Process(target=worker, args=(logger, AMIinput, AMIoutput))
            p.daemon = True
            p.start()
            procs.append(p)
        for p in procs:
            p.join()
        data = None
        while not AMIoutput.empty():
            data = AMIoutput.get()
            if not data is None:
                osdial.sql().execute(data[0], (data[1:]))
    if not osdial is None:
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

        handler = logging.FileHandler('%s/action_process.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.gmtime())) )
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

    logger.info("Starting manager_process()")
    while True:
        manager_process(logger)
        time.sleep(1)

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
