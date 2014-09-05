#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging

from osdial import OSDial

PROGNAME = 'osdial_lead_transfers'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_lead_transfers - process to transfer leads between lists based on rules defined in GUI.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
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
        logger = logging.getLogger('leadtransfers')
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

    logger.info("Starting leadtransfers_process()")
    leadtransfers_process()


def leadtransfers_process():
    """
    The routine responsible for finding called leads in osdial_log and transferring those leads to others lists based on GUI policy rules.
    """
    osdial = OSDial()
    logger = logging.getLogger('leadtransfers')

    lxs = {} 
    osdial.sql().execute("SELECT * FROM osdial_lead_transfers WHERE active>=%s;", ('Y'))
    for row in osdial.sql().fetchall():
        lxs[row['id']] = []
        tarys = row['container'].split('|:')
        for tary in tarys:
            cary = tary.split('|')
            qarys = cary[4].split(',')
            parts = []
            for qary in qarys:
                qpart = qary.split(' ')
                parts.append({'condition':qpart[0],'operator':qpart[1],'value':qpart[2]})
            lxs[row['id']].append({'action':cary[0],'orig_new_status':cary[1],'dest_list':cary[2],'dest_status':cary[3],'conds':parts})

    lists = {} 
    osdial.sql().execute("SELECT * FROM osdial_lists;")
    for row in osdial.sql().fetchall():
        lists[str(row['list_id'])] = row


    nowdate = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()-900))
    logs = []
    osdial.sql().execute("SELECT * FROM osdial_log WHERE call_date>=%s AND end_epoch>%s AND server_ip=%s AND processed=%s;", (nowdate,0,osdial.VARserver_ip,'N'))
    for row in osdial.sql().fetchall():
      logs.append(row)


    for log in logs:
        values = {}
        values['log_id'] = log['id']
        values['log_status'] = log['status']
        values['log_user'] = log['user']
        values['log_call_length'] = log['length_in_sec']

        osdial.sql().execute("SELECT * FROM osdial_list WHERE lead_id=%s;", (log['lead_id']))
        for row in osdial.sql().fetchall():
            values['lead_called_count'] = int('0%s' % row['called_count'])
            if row['called_since_last_reset']=='Y':
                row['called_since_last_reset']=1
            values['lead_called_since_last_reset'] = int(re.sub('\D','','0%s' % row['called_since_last_reset']))
            values['lead_list_id'] = str(row['list_id'])
            values['lead'] = row

        hopper_count = 0
        osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_hopper WHERE lead_id=%s;", (log['lead_id']))
        for row in osdial.sql().fetchall():
            hopper_count = row['cnt']

        if lists.has_key(values['lead_list_id']) and hopper_count==0:
            if lists[values['lead_list_id']]['lead_transfer_id']:
                values['list_campaign_id'] = lists[values['lead_list_id']]['campaign_id']
                values['list_lead_transfer_id'] = lists[values['lead_list_id']]['lead_transfer_id']
                lxc = lxs[values['list_lead_transfer_id']]
                for lx in lxc:
                    if lists.has_key(lx['dest_list']):
                        matches_status=True
                        matches_called_count=True
                        for cond in lx['conds']:
                            if cond['condition']=='status':
                                statuses = re.split('&',cond['value'])
                                if not values['log_status'] in statuses:
                                    matches_status=False
                            elif cond['condition']=='called_count':
                                if not int(values['lead_called_count']) >= int(cond['value']):
                                    matches_called_count=False
                            else:
                                matches_status=False
                                matches_called_count=False
                        if matches_status and matches_called_count:
                            if lx['action'] == 'COPY':
                                newlead = values['lead']
                                if str(lx['dest_list']) != str(newlead['list_id']):
                                    if not newlead['external_key']:
                                        newlead['external_key'] = str(newlead['lead_id'])
                                    if lx['orig_new_status']:
                                        sts = osdial.sql().execute("UPDATE osdial_list SET status=%s WHERE lead_id=%s;", (lx['orig_new_status'], newlead['lead_id']))
                                    del newlead['lead_id']
                                    if lx['dest_list']:
                                        newlead['list_id'] = lx['dest_list']
                                    if lx['dest_status']:
                                        newlead['status'] = lx['dest_status']
                                    if not newlead['post_date']:
                                        newlead['post_date'] = '0000-00-00 00:00:00'
                                    if not newlead['date_of_birth']:
                                        newlead['date_of_birth'] = '0000-00-00 00:00:00'
                                    newlead['called_since_last_reset'] = 'N'
                                    newlead['called_count'] = '0'

                                    flds = []
                                    stmt = "INSERT INTO osdial_list SET "
                                    for fld in newlead:
                                        flds.append(newlead[fld])
                                        stmt += fld + "=%s,"
                                    stmt = re.sub(',$',';',stmt)
                                    sts = osdial.sql().execute(stmt, flds)

                            elif lx['action'] == 'MOVE':
                                lead = values['lead']
                                if str(lx['dest_list']) != str(lead['list_id']):
                                    if lx['dest_list']:
                                        lead['list_id'] = lx['dest_list']
                                    if lx['dest_status']:
                                        lead['status'] = lx['dest_status']
                                    lead['called_since_last_reset'] = 'N'
                                    sts = osdial.sql().execute("UPDATE osdial_list SET list_id=%s,status=%s,called_since_last_reset=%s WHERE lead_id=%s;", (lead['list_id'], lead['status'],lead['called_since_last_reset'],lead['lead_id']))

        sts = osdial.sql().execute("UPDATE osdial_log SET processed=%s WHERE id=%s;", ('Y', values['log_id']))

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
