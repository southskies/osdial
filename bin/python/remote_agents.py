#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse, psutil, random

import MySQLdb, logging

from osdial import OSDial
from osdial.sql import OSDialSQL as OSDialSQL2

PROGNAME = 'osdial_remote_agents'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False,'delay':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_remote_agents - Simulates presence of both remote and virtual agents.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
    parser.add_argument('-d', '--daemon', action='store_true', help='Puts process in daemon mode.',dest='daemon')
    parser.add_argument('-l', '--logLevel', action='store', default='INFO', choices=['CRITICAL','ERROR','WARNING','INFO','DEBUG'], help='Sets the level of output verbosity.', dest='loglevel')
    parser.add_argument('--delay', action='store', default=2, help='delay per loop, in seconds (default 2)', dest='delay')
    opts = parser.parse_args(args=argv)
    newargs = vars(opts)
    for arg in newargs:
        opt[arg] = newargs[arg]

    if not opt['delay']:
        opt['delay'] = 2
    opt['delay'] = int(opt['delay']) * 1000

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
        logger = logging.getLogger('remote')
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

        handler = logging.FileHandler('%s/remoteagent.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
        handler.setLevel(logdeflvl)
        handler.setFormatter(formatter)
        logger.addHandler(handler)

        if opt['verbose'] or opt['debug']:
            streamhandler = logging.StreamHandler()
            streamhandler.setLevel(logdeflvl)
            streamhandler.setFormatter(formatter)
            logger.addHandler(streamhandler)

        logger.setLevel(logdeflvl)

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

    logger.info("Starting remoteagent_process()")
    remoteagent_process()

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


def update_times(times):
    times['nowdate_epoch'] = time.time()
    times['FDdate_epoch'] = times['nowdate_epoch']+60
    times['BDdate_epoch'] = times['nowdate_epoch']-10
    times['PDdate_epoch'] = times['nowdate_epoch']-30
    times['XDdate_epoch'] = times['nowdate_epoch']-120
    times['TDdate_epoch'] = times['nowdate_epoch']-600
    times['CIDdate'] = time.strftime('%y%m%d%H%M%S', time.localtime(times['nowdate_epoch']))
    times['nowdate'] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(times['nowdate_epoch']))
    times['FDdate'] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(times['FDdate_epoch']))
    times['BDdate'] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(times['BDdate_epoch']))
    times['PDdate'] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(times['PDdate_epoch']))
    times['XDdate'] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(times['XDdate_epoch']))
    times['TDdate'] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(times['TDdate_epoch']))

def remoteagent_process():
    """
    Simulates presence of both remote and virtual agents.
    """
    osdial = OSDial()
    logger = logging.getLogger('remote')

    times = {}
    update_times(times)

    logger.info("PROGRAM STARTED!")
    endless_loop = (7*24*60*60)/(int(opt['delay'])/1000)
    while endless_loop:
        time.sleep(0.2)
        update_times(times)

        if re.search('0$|5$',str(endless_loop)):
            osdial.reload()

            ### delete call records that are LIVE for over 10 minutes and last_update_time < '$PDtsSQLdate'
            affected = osdial.sql().execute("DELETE FROM osdial_live_agents WHERE server_ip=%s AND status IN('PAUSED') AND extension LIKE 'R/%%';", (osdial.VARserver_ip))
            if affected:
                logger.info("|     lagged call vla agent DELETED %s", affected)

            ##### grab number of calls today in this campaign and increment
            calls_today = 0
            osdial.sql().execute("SELECT calls_today FROM osdial_live_agents WHERE extension LIKE 'R/%%';")
            for row in osdial.sql().fetchall():
                calls_today = row['calls_today']
            calls_today += 1

            ##### grab number of calls today in this campaign and increment
            osdial.sql().execute("SELECT vla.live_agent_id,vla.lead_id,vla.uniqueid,vla.user,vac.call_type FROM osdial_live_agents vla,osdial_auto_calls vac WHERE vla.server_ip=%s AND vla.status IN('QUEUE') AND vla.extension LIKE 'R/%%' AND vla.uniqueid=vac.uniqueid AND vla.channel=vac.channel;", (osdial.VARserver_ip))
            rows = osdial.sql().fetchall()
            for qh in rows:
                affected1 = 0
                affected2 = 0
                affected3 = 0
                affected1 = osdial.sql().execute("UPDATE osdial_live_agents SET status='INCALL',last_call_time=%s,comments='REMOTE',calls_today=%s WHERE live_agent_id=%s;", (times['nowdate'], calls_today, qh['live_agent_id']))
                if affected:
                    logger.info("|     set agent to INCALL %s", affected)

                if re.search('IN',qh['call_type']):
                    affected2 = osdial.sql().execute("UPDATE osdial_log SET status='XFER',user=%s,comments='REMOTE' WHERE uniqueid=%s AND server_ip=%s AND status NOT LIKE 'V%%';", (qh['user'],qh['uniqueid'],osdial.VARserver_ip))
                else:
                    affected2 = osdial.sql().execute("UPDATE osdial_log SET status='XFER',user=%s,comments='REMOTE' WHERE uniqueid=%s AND server_ip=%s AND status NOT LIKE 'V%%';", (qh['user'], qh['uniqueid'], osdial.VARserver_ip))

                if affected2:
                    affected3 = osdial.sql().execute("UPDATE osdial_list SET status='XFER',user=%s WHERE lead_id=%s;", (qh['user'],qh['lead_id']))

                logger.info("|     QUEUEd listing UPDATEd |%s|%s|%s|     |%s|%s|%s|%s|%s|",affected1,affected2,affected3,qh['live_agent_id'],qh['lead_id'],qh['uniqueid'],qh['user'],qh['call_type'])

            
            running_listen = False
            for proc in psutil.process_iter():
                try:
                    if re.search('(perl|python).*manager_listen', " ".join(proc.cmdline)):
                        logger.debug("LISTEN RUNNING: |%s|", proc.pid)
                        running_listen = True
                except psutil.AccessDenied, e:
                    running_listen = True
                    pass
                except psutil.NoSuchProcess, e:
                    running_listen = True
                    pass

            if not running_listen:
                logger.error("PROCESS KILLED NO LISTENER RUNNING... EXITING")
                endless_loop = 0

        ### Variables
        remote_users = []
        deleted_users = {}
        live_users = []
            
        #### Update closer campaigns if ivr is set to allow inbound.
        osdial.sql().execute("UPDATE osdial_remote_agents AS ra,osdial_campaigns AS c,osdial_ivr AS i SET ra.closer_campaigns=c.closer_campaigns WHERE ra.campaign_id=c.campaign_id AND c.ivr_id=i.id AND i.allow_inbound='Y' AND ra.user_start LIKE 'va%%';")
        osdial.sql().execute("UPDATE osdial_remote_agents AS ra,osdial_campaigns AS c,osdial_ivr AS i SET ra.closer_campaigns='' WHERE ra.campaign_id=c.campaign_id AND c.ivr_id=i.id AND i.allow_inbound='N' AND ra.user_start LIKE 'va%%';")
        osdial.sql().execute("UPDATE osdial_users AS u,osdial_remote_agents AS ra SET u.closer_campaigns=ra.closer_campaigns WHERE u.user=ra.user_start AND ra.user_start LIKE 'va%%';")
        osdial.sql().execute("UPDATE osdial_users AS u,osdial_live_agents AS la SET la.closer_campaigns=u.closer_campaigns WHERE u.user=la.user AND la.user LIKE 'va%%';")


        """
        ###############################################################################
        ###### first grab all of the ACTIVE remote agents information from the database
        ###############################################################################
        """
        osdial.sql().execute("SELECT user_start,number_of_lines,conf_exten,campaign_id,closer_campaigns FROM osdial_remote_agents WHERE status IN('ACTIVE') AND server_ip=%s ORDER BY user_start;", (osdial.VARserver_ip))
        for row in osdial.sql().fetchall():
            va = ""
            if re.search('va%s' % row['campaign_id'],row['user_start']):
                row['user_start'] = re.sub('va%s' % row['campaign_id'],'',row['user_start'])
                upad = (len(row['user_start']) + len(row['campaign_id'])) - (len(str(int(row['user_start']))) + len(row['campaign_id']))
                vasub = "va%%s%%0%dd" % upad
                va = vasub % (row['campaign_id'], 0)
            for line in range(int(row['number_of_lines'])):
                randnum = random.randint(0, 9999998) + 10000000
                user_id = (int(row['user_start']) + line)
                remote_users.append({'user_start':"%s%s" % (va,row['user_start']),
                                    'user':"%s%s" % (va,user_id),
                                    'campaign_id':row['campaign_id'],
                                    'conf_exten':row['conf_exten'],
                                    'closer_campaigns':row['closer_campaigns'],
                                    'random':randnum})
        logger.debug("%s live remote agents ACTIVE.", len(remote_users))


        """
        ###############################################################################
        ###### second grab all of the INACTIVE remote agents information from the database
        ###############################################################################
        """
        osdial.sql().execute("SELECT user_start,number_of_lines,campaign_id FROM osdial_remote_agents WHERE status IN('INACTIVE') AND server_ip=%s ORDER BY user_start;", (osdial.VARserver_ip))
        for row in osdial.sql().fetchall():
            va = ""
            if re.search('va%s' % row['campaign_id'],row['user_start']):
                row['user_start'] = re.sub('va%s' % row['campaign_id'],'',row['user_start'])
                upad = (len(row['user_start']) + len(row['campaign_id'])) - (len(str(int(row['user_start']))) + len(row['campaign_id']))
                vasub = "va%%s%%0%dd" % upad
                va = vasub % (row['campaign_id'], 0)
            for line in range(int(row['number_of_lines'])):
                user_id = (int(row['user_start']) + line)
                deleted_users["R/%s%s" % (va,user_id)] = True
        if len(deleted_users.keys()):
            logger.info("INACTIVE remote agents: |%s|%s|",len(deleted_users.keys()),"|".join(deleted_users.keys()))


        """
        ###############################################################################
        ###### third traverse array of remote agents to be active and insert or update 
        ###### in osdial_live_agents table 
        ###############################################################################
        """
        for user in remote_users:

            ### check to see if the record exists and only needs random number update
            update_random = 0
            osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_live_agents WHERE user=%s AND server_ip=%s AND campaign_id=%s AND conf_exten=%s;", (user['user'],osdial.VARserver_ip,user['campaign_id'],user['conf_exten']))
            for row in osdial.sql().fetchall():
                update_random = row['cnt']
            update_times(times)
            if update_random:
                affected = osdial.sql().execute("UPDATE osdial_live_agents SET random_id=%s,last_update_time=%s WHERE user=%s AND server_ip=%s AND campaign_id=%s AND conf_exten=%s;", (user['random'],times['FDdate'],user['user'],osdial.VARserver_ip,user['campaign_id'],user['conf_exten']))
                logger.debug("|    %s %s ONLY RANDOM ID UPDATE: %s", user['user'],user['campaign_id'],affected)
                continue

            ### check if record for user on server exists at all in osdial_live_agents
            update_exists = 0
            osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_live_agents WHERE user=%s AND server_ip=%s;", (user['user'],osdial.VARserver_ip))
            for row in osdial.sql().fetchall():
                update_exists = row['cnt']
            if update_exists:
                affected = osdial.sql().execute("UPDATE osdial_live_agents SET random_id=%s,last_update_time=%s,campaign_id=%s,conf_exten=%s,closer_campaigns=%s,status='READY' WHERE user=%s AND server_ip=%s;", (user['random'],times['FDdate'],user['campaign_id'],user['conf_exten'],user['closer_campaigns'],user['user'],osdial.VARserver_ip))
                logger.debug("|    %s ALL UPDATE: %s", user['user'],affected)
                continue

            ### no records exist so insert a new one
            user_level = '7'
            osdial.sql().execute("SELECT user_level FROM osdial_users WHERE user=%s;",user['user_start'])
            for row in osdial.sql().fetchall():
                user_level = row['user_level']

            affected = osdial.sql().execute("INSERT INTO osdial_live_agents SET user=%s,server_ip=%s,conf_exten=%s,extension=%s,status='READY',campaign_id=%s,random_id=%s,last_call_time=%s,last_update_time=%s,last_call_finish=%s,closer_campaigns=%s,channel='',uniqueid='',callerid='',user_level=%s,comments='REMOTE';", (user['user'],osdial.VARserver_ip,user['conf_exten'],"R/%s" % user['user'],user['campaign_id'],user['random'],times['nowdate'],times['FDdate'],times['nowdate'],user['closer_campaigns'],user_level))
            if affected:
                logger.debug("|    %s NEW INSERT: %s", user['user'], affected)

                ### Event and Queuemetrics posting
                osdial.event({'event':'AGENT_LOGIN','server_ip':osdial.VARserver_ip,'campaign_id':user['campaign_id'],'user':user['user'],'data1':"Local/%s@%s" % (user['conf_exten'],osdial.server['ext_context']),'data2':"Local/%s@%s" % (user['conf_exten'],osdial.server['ext_context']),'data3':user['conf_exten']});
                osdial.event({'event':'AGENT_PAUSE','server_ip':osdial.VARserver_ip,'campaign_id':user['campaign_id'],'user':user['user']});
                osdial.event({'event':'AGENT_UNPAUSE','server_ip':osdial.VARserver_ip,'campaign_id':user['campaign_id'],'user':user['user']});

                if osdial.settings['enable_queuemetrics_logging']:
                    qsql = None
                    try:
                        qopts = {'host':'','user':'','user':'','passwd':'','db':''}
                        qopts['host'] = osdial.settings['queuemetrics_server_ip']
                        qopts['user'] = osdial.settings['queuemetrics_login']
                        qopts['passwd'] = osdial.settings['queuemetrics_pass']
                        qopts['db'] = osdial.settings['queuemetrics_dbname']
                        qsql = OSDialSQL2(None, qopts)
                        qsql().execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id='NONE',queue=%s',agent=%s,verb='AGENTLOGIN',data1=%s,serverid=%s;", (times['nowdate_epoch'],user['campaign_id'],"Agent/%s" % user['user'],user['user'],osdial.settings['queuemetrics_log_id']))
                        qsql.execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id='NONE',queue=%s,agent=%s,verb='PAUSEALL',serverid=%s;", (times['nowdate_epoch'],user['campaign_id'],"Agent/%s" % user['user'],osdial.settings['queuemetrics_log_id']))
                        qsql.execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id='NONE',queue=%s,agent=%s,verb='UNPAUSEALL',serverid=%s;", (times['nowdate_epoch'],user['campaign_id'],"Agent/%s" % user['user'],osdial.settings['queuemetrics_log_id']))
                    except:
                        pass
                    if not qsql is None:
                        qsql.close()
                        qsql = None


        """
        ###############################################################################
        ###### fourth validate that the calls that the osdial_live_agents are on are not dead
        ###### and if they are wipe out the values and set the agent record back to READY
        ###############################################################################
        """
        affected = osdial.sql().execute("UPDATE osdial_live_agents SET status='PAUSED' WHERE extension LIKE 'R/%%' AND server_ip=%s AND lead_id='0' AND uniqueid='' AND callerid='' AND status='INCALL';", (osdial.VARserver_ip))
        if affected:
            logger.debug("|    Found INCALL without lead_id/uniqueid/callerid, set to PAUSED |%S|", affected)

        osdial.sql().execute("SELECT user,extension,status,uniqueid,callerid,lead_id,campaign_id,conf_exten FROM osdial_live_agents WHERE extension LIKE 'R/%%' AND server_ip=%s AND uniqueid>10;", (osdial.VARserver_ip))
        for row in osdial.sql().fetchall():
            randnum = random.randint(0, 9999998) + 10000000
            live_users.append({'user':row['user'],
                        'extension':row['extension'],
                        'status':row['status'],
                        'uniqueid':row['uniqueid'],
                        'callerid':row['callerid'],
                        'lead_id':row['lead_id'],
                        'campaign_id':row['campaign_id'],
                        'conf_exten':row['conf_exten'],
                        'random':randnum})
        logger.debug("%s remote agents on calls", len(live_users))

        for user in live_users:

            ### Check if autocall entry already exists
            autocall_exists = 0
            osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_auto_calls WHERE uniqueid=%s AND server_ip=%s;", (user['uniqueid'],osdial.VARserver_ip))
            for row in osdial.sql().fetchall():
                autocall_exists = row['cnt']
            if autocall_exists:
                logger.debug("|    %s FOUND AUTOCALL ENTRY |%s|", user['user'],user['uniqueid'])
                continue

            ### Clear any live agents records if the user is marked inactive.
            if deleted_users.has_key("R/%s" % user['user']):
                affected = osdial.sql().execute("UPDATE osdial_live_agents SET random_id=%s,status='PAUSED',last_call_finish=%s,lead_id='',uniqueid='',callerid='',channel='' WHERE user=%s AND server_ip=%s;", (user['random'],times['nowdate'],user['user'],osdial.VARserver_ip))
                if affected:
                    logger.debug("|    %s CALL WIPE UPDATE: %s|PAUSED|%s|",user['user'],affected,user['uniqueid'])

                    ### Event and Queuemetrics posting
                    logintime = 0
                    osdial.sql().execute("SELECT UNIX_TIMESTAMP(event_time) AS logintime FROM osdial_events WHERE user=%s AND event='AGENT_LOGIN' ORDER BY event_time DESC LIMIT 1;", (user['user']))
                    for row in osdial.sql().fetchall():
                        logintime = row['logintime']
                    time_logged_in = (times['nowdate_epoch'] - logintime)
                    if not time_logged_in:
                        time_logged_in = 0
                    elif time_logged_in > 1000000:
                        time_logged_in = 1
                    osdial.event({'event':'AGENT_PAUSE','server_ip':osdial.VARserver_ip,'campaign_id':user['campaign_id'],'user':user['user']});
                    osdial.event({'event':'AGENT_LOGOUT','server_ip':osdial.VARserver_ip,'campaign_id':user['campaign_id'],'user':user['user'],'data1':time_logged_in});

                    if osdial.settings['enable_queuemetrics_logging']:
                        qsql = None
                        try:
                            qopts = {'host':'','user':'','user':'','passwd':'','db':''}
                            qopts['host'] = osdial.settings['queuemetrics_server_ip']
                            qopts['user'] = osdial.settings['queuemetrics_login']
                            qopts['passwd'] = osdial.settings['queuemetrics_pass']
                            qopts['db'] = osdial.settings['queuemetrics_dbname']
                            qsql = OSDialSQL2(None, qopts)
                            qsql.execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id='NONE',queue='NONE',agent=%s,verb='PAUSEALL',serverid=%s;", (times['nowdate_epoch'],"Agent/%s" % user['user'],osdial.settings['queuemetrics_log_id']))
                            logintime = 0
                            qsql.execute("SELECT time_id FROM queue_log WHERE agent=%s AND verb='AGENTLOGIN' ORDER BY time_id DESC LIMIT 1;", ("Agent/%s" % user['user']))
                            for row in qsql.fetchall():
                                logintime = row['time_id']
                            time_logged_in = (times['nowdate_epoch'] - logintime)
                            if not time_logged_in:
                                time_logged_in = 0
                            elif time_logged_in > 1000000:
                                time_logged_in = 1
                            qsql.execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id='NONE',queue=%s,agent=%s,verb='AGENTLOGOFF',data1=%s,data2=%s,serverid=%s;", (times['nowdate_epoch'],user['campaign_id'],"Agent/%s" % user['user'],user['user'],time_logged_in,osdial.settings['queuemetrics_log_id']))
                        except:
                            pass
                        if not qsql is None:
                            qsql.close()
                            qsql = None
                continue

            ### Check call and closer logs for evidence that the call has ended.
            calllog_finished = 0
            osdial.sql().execute("SELECT count(*) AS cnt FROM call_log WHERE caller_code=%s AND channel NOT LIKE 'Local%%' AND end_epoch>10;", (user['callerid']))
            for row in osdial.sql().fetchall():
                calllog_finished = row['cnt']

            calllog_local_finished = 0
            osdial.sql().execute("SELECT count(*) AS cnt FROM call_log WHERE caller_code=%s AND channel LIKE 'Local%%' AND end_epoch>10;", (user['callerid']))
            for row in osdial.sql().fetchall():
                calllog_local_finished = row['cnt']

            closerlog_finished = 0
            if not calllog_finished:
                osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_closer_log AS cl,osdial_live_agents AS la WHERE cl.callerid=%s AND cl.callerid=la.callerid AND server_ip=%s AND end_epoch>10;", (user['callerid'],osdial.VARserver_ip))
                for row in osdial.sql().fetchall():
                    closerlog_finished = row['cnt']

            ### Send hangup commands to channels if ending call_log or closer_log entries have been recorded.
            if calllog_finished or calllog_local_finished > 1 or closerlog_finished:
                lachannel = None
                osdial.sql().execute("SELECT channel FROM osdial_live_agents WHERE callerid=%s;", (user['callerid']))
                for row in osdial.sql().fetchall():
                    lachannel = row['channel']
                if lachannel:
                    update_times(times)
                    queryCID = "ULGH3957%s" % times['CIDdate']
                    affected = osdial.sql().execute("INSERT INTO osdial_manager SET entry_date=%s,status='NEW',response='N',server_ip=%s,action='Hangup',callerid=%s,cmd_line_b=%s;", (times['nowdate'],osdial.VARserver_ip,queryCID,"Channel: %s" % lachannel))
                    if affected:
                        logger.debug("|    %s CALL HANGUP SENT: %s|READY|%s|",user['user'],affected,user['uniqueid'])
                    if re.search('^R/va', user['extension']):
                        update_times(times)
                        queryCID = "ULGH3956%s" % times['CIDdate']
                        affected = osdial.sql().execute("INSERT INTO osdial_manager SET entry_date=%s,status='NEW',response='N',server_ip=%s,action='Command',callerid=%s,cmd_line_b=%s;", (times['nowdate'],osdial.VARserver_ip,queryCID,"Command: meetme kick %s all" % user['conf_exten']))

                affected1 = osdial.sql().execute("UPDATE osdial_live_agents SET random_id=%s,last_call_finish=%s,lead_id='0',uniqueid='',callerid='',channel='' WHERE user=%s AND server_ip=%S;", (user['random'],times['nowdate'],user['user'],osdial.VARserver_ip))
                affected2 = osdial.sql().execute("UPDATE osdial_live_agents SET status='READY' WHERE user=%s AND server_ip=%s;", (user['user'],osdial.VARserver_ip))
                if affected1 and affected2:
                    logger.debug("|    %s CALL WIPE UPDATE: %s/%s|READY|%s|",user['user'],affected1,affected2,user['uniqueid'])

                    ### Event and Queuemetrics posting
                    osdial.event({'event':'AGENT_PAUSE','server_ip':osdial.VARserver_ip,'campaign_id':user['campaign_id'],'user':user['user']});
                    osdial.event({'event':'AGENT_LOGOUT','server_ip':osdial.VARserver_ip,'campaign_id':user['campaign_id'],'user':user['user']});

                    if osdial.settings['enable_queuemetrics_logging']:
                        qsql = None
                        try:
                            qopts = {'host':'','user':'','user':'','passwd':'','db':''}
                            qopts['host'] = osdial.settings['queuemetrics_server_ip']
                            qopts['user'] = osdial.settings['queuemetrics_login']
                            qopts['passwd'] = osdial.settings['queuemetrics_pass']
                            qopts['db'] = osdial.settings['queuemetrics_dbname']
                            qsql = OSDialSQL2(None, qopts)
                            qsql.execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id='NONE',queue='NONE',agent=%s,verb='PAUSEALL',serverid=%s;", (times['nowdate_epoch'],"Agent/%s" % user['user'],osdial.settings['queuemetrics_log_id']))
                            qsql.execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id='NONE',queue=%s,agent=%s,verb='AGENTLOGOFF',data1=%s,serverid=%s;", (times['nowdate_epoch'],user['campaign_id'],"Agent/%s" % user['user'],user['user'],osdial.settings['queuemetrics_log_id']))
                        except:
                            pass
                        if not qsql is None:
                            qsql.close()
                            qsql = None

        time.sleep(int(opt['delay'])/1000)
        if osdial.server_process_tracker(PROGNAME, osdial.VARserver_ip, os.getpid(), True):
            logger.error("ERROR: Process already running!")
            endless_loop = 0

        endless_loop -= 1

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
