#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse

import MySQLdb, logging

from osdial import OSDial

import netifaces

PROGNAME = 'osdial_update_server_ip'
VERSION = '0.1'

opt = {'verbose':False,'debug':False,'auto':False,'noprompt':False,'oldserverip':False,'serverip':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_update_server_ip - updates the server IP for this system.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('--auto', action='store_true', help='no prompts.',dest='auto')
    parser.add_argument('--noprompt', action='store_true', help='no prompts.',dest='auto')
    parser.add_argument('--old-server_ip', '--old_server_ip', '--old-server-ip', action='store', help='Define old server IP address at runtime.', dest='oldserverip')
    parser.add_argument('--server_ip', '--server-ip', action='store', help='Define server IP address at runtime.', dest='serverip')
    opts = parser.parse_args(args=argv)
    newargs = vars(opts)
    for arg in newargs:
        opt[arg] = newargs[arg]

    if opt['debug']:
        print("Starting updateserverip_process()")
    updateserverip_process()


def updateserverip_process():
    """
    Updates the server IP for this system.
    """
    osdial = OSDial()

    valid_ips = {}
    for iface in netifaces.interfaces():
        addrs = netifaces.ifaddresses(iface)
        for addr in addrs[netifaces.AF_INET]:
            valid_ips[addr['addr']] = True

    if not opt['oldserverip']:
        opt['oldserverip'] = osdial.VARserver_ip

    interactive = True
    if opt['auto']:
        interactive = False
        for iface in netifaces.interfaces():
            if not re.search('^lo',iface):
                addrs = netifaces.ifaddresses(iface)
                for addr in addrs[netifaces.AF_INET]:
                    opt['serverip'] = addr['addr']
        if not opt['serverip']:
            opt['serverip'] = "127.0.0.1"
    elif opt['noprompt']:
        interactive = False
    else:
        linein = raw_input("\nWould you like to use interactive mode (y/n): [y] ")
        if re.search('n',linein,re.IGNORECASE):
            interactive = False

    if interactive:
        config_repeat = True
        while config_repeat:
            print("\nSTARTING SERVER IP ADDRESS CHANGE FOR OSDIAL...\n")

            isvalid = False
            while not isvalid:
                linein = raw_input("\nOld server IP address or press enter for default: [%s] " % opt['oldserverip'])
                oldselectedip = re.sub('\s|\n|\r|\t|/$','',linein)
                if re.search('^$',oldselectedip):
                    oldselectedip = opt['oldserverip']
                if re.search('^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$',oldselectedip):
                    isvalid = True
                    opt['oldserverip'] = oldselectedip
                else:
                    print("\n\nInvalid IP Address! Please try again.")

            for iface in netifaces.interfaces():
                if not re.search('^lo',iface):
                    addrs = netifaces.ifaddresses(iface)
                    for addr in addrs[netifaces.AF_INET]:
                        opt['serverip'] = addr['addr']
            if not opt['serverip']:
                opt['serverip'] = "127.0.0.1"

            isvalid = False
            while not isvalid:
                linein = raw_input("\nServer IP address or press enter for default: [%s] " % opt['serverip'])
                selectedip = re.sub('\s|\n|\r|\t|/$','',linein)
                if re.search('^$',selectedip):
                    selectedip = opt['serverip']
                if valid_ips.has_key(selectedip):
                    isvalid = True
                    opt['serverip'] = selectedip
                else:
                    print("\n\nInvalid IP Address! Please try again.")

            print("\n  old server_ip:     %s" % opt['oldserverip'])
            print("  new server_ip:     %s\n" % opt['serverip'])
            linein = raw_input("Are these settings correct?(y/n): [y] ")
            if not re.search('n',linein,re.IGNORECASE):
                config_repeat = False

    os.system("/usr/bin/perl -pi -e 's|^VARserver_ip => %s|VARserver_ip => %s|' %s" % (opt['oldserverip'],opt['serverip'],osdial.PATHconf))

    print("\nSTARTING DATABASE TABLES UPDATES PHASE...\n")

    tables = {
        'call_log':['server_ip'],
        'conferences':['server_ip'],
        'inbound_numbers':['server_ip'],
        'live_channels':['server_ip'],
        'live_inbound':['server_ip'],
        'live_sip_channels':['server_ip'],
        'osdial_agent_log':['server_ip'],
        'osdial_auto_calls':['server_ip'],
        'osdial_campaign_server_stats':['server_ip'],
        'osdial_carrier_servers':['server_ip'],
        'osdial_closer_log':['server_ip'],
        'osdial_companies':['default_server_ip'],
        'osdial_conferences':['server_ip'],
        'osdial_cpa_log':['server_ip'],
        'osdial_events':['server_ip'],
        'osdial_live_agents':['server_ip','call_server_ip'],
        'osdial_log':['server_ip'],
        'osdial_manager':['server_ip'],
        'osdial_remote_agents':['server_ip'],
        'osdial_server_trunks':['server_ip'],
        'park_log':['server_ip'],
        'parked_channels':['server_ip'],
        'phones':['server_ip'],
        'recording_log':['server_ip'],
        'server_keepalive_processes':['server_ip'],
        'server_performance':['server_ip'],
        'server_stats':['server_ip'],
        'server_updater':['server_ip'],
        'servers':['server_ip'],
        'web_client_sessions':['server_ip']}

    for tab in tables.keys():
        for svrtype in tables[tab]:
            print("  Updating %s table using column %s..." % (tab, svrtype))
            osdial.sql().execute("UPDATE "+tab+" SET "+svrtype+"=%s WHERE "+svrtype+"=%s;", (opt['serverip'], opt['oldserverip']))

    os.system("%s/osdial_killall.sh" % osdial.PATHhome)

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
