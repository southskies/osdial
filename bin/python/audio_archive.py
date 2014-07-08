#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse, psutil, shutil

import MySQLdb, logging

from ftplib import FTP,all_errors

from osdial import OSDial

PROGNAME = 'osdial_audio_archive'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False,'nodatedir':False,'recloc':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_audio_archive - send recordings to archive.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Run in debug mode.',dest='debug')
    parser.add_argument('--nodatedir', action='store_true', default=True, help='Do not create subdirectory on FTP size.',dest='nodatedir')
    parser.add_argument('-t', '--test', action='store_true', help='Run in test mode.',dest='test')
    #parser.add_argument('-d', '--daemon', action='store_true', help='Puts process in daemon mode.',dest='daemon')
    parser.add_argument('--recording_location', action='store', help='Directory to find recordings in (defaults to PATHmonitor).', dest='recloc')
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
        logger = logging.getLogger('archiverec')
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

        handler = logging.FileHandler('%s/audio_archive.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
        handler.setLevel(logdeflvl)
        handler.setFormatter(formatter)
        logger.addHandler(handler)

        if opt['verbose'] or opt['debug']:
            handler = logging.StreamHandler()
            handler.setLevel(logdeflvl)
            handler.setFormatter(formatter)
            logger.addHandler(handler)

        logger.setLevel(logdeflvl)

        sptres = osdspt.server_process_tracker(PROGNAME, osdspt.VARserver_ip, os.getpid(), True)
        osdspt.close()
        osdspt = None
        if sptres:
            proclogger.error("Error process already running!")
            sys.exit(1)
    except MySQLdb.OperationalError, e:
        logger.error("Could not connect to MySQL! %s", e)
        sys.exit(1)
    gc.collect()

    logger.info("Starting archiverecordings_process()")
    archiverecordings_process()


def archiverecordings_process():
    """
    Sends recordings to archive.
    """
    osdial = OSDial()
    logger = logging.getLogger('archiverec')

    ahost = osdial.VARFTP_host
    aport = osdial.VARFTP_port
    auser = osdial.VARFTP_user
    apass = osdial.VARFTP_pass
    apath = osdial.VARFTP_dir
    awebpath = osdial.VARHTTP_path

    if osdial.configuration['ArchiveHostname']:
        ahost = osdial.configuration['ArchiveHostname']
        aport = osdial.configuration['ArchivePort']
        auser = osdial.configuration['ArchiveUsername']
        apass = osdial.configuration['ArchivePassword']
        apath = osdial.configuration['ArchivePath']
        awebpath = osdial.configuration['ArchiveWebPath']


    locked_files = {}
    for p in psutil.process_iter():
        try:
            if len(p.cmdline) and re.search('asterisk', p.cmdline[0]):
                for file in p.get_open_files():
                    locked_files[file.path] = True
        except psutil.AccessDenied, e:
            pass

    srcdir = "%s" % osdial.PATHmonitor
    if opt['recloc']:
        if os.path.isdir(opt['recloc']):
            src = "%s" % opt['recloc']

    for afile in os.listdir(srcdir):
        archive_file = False
        if len(afile) > 4 and not re.search('-out\.wav|archive\.lock|lost\+found',afile,re.IGNORECASE) and not os.path.isdir("%s/%s" % (srcdir, afile)):
            if not locked_files.has_key("%s/%s" % (srcdir,afile)):
                archive_file = True
        if archive_file:
            SQLfile = re.sub('-in\.wav|-all\.wav','',afile,re.IGNORECASE)
            ALLfile = "%s-all.wav" % SQLfile
            INfile = "%s-in.wav" % SQLfile
            OUTfile = "%s-out.wav" % SQLfile
            location = None
            recording_id = None

            if os.path.exists("%s/%s" % (srcdir, INfile)):
                os.rename("%s/%s" % (srcdir, INfile), "%s/%s" % (srcdir, ALLfile))
                if os.path.exists("%s/%s" % (srcdir, OUTfile)):
                    os.unlink("%s/%s" % (srcdir, OUTfile))

            if re.search('^PBX-IN|^PBX-OUT',SQLfile):
                (pcmp,pdat,puid,pext,pcnl) = SQLfile.split('_')

                osdial.sql().execute("SELECT * FROM call_log WHERE server_ip=%s AND uniqueid=%s LIMIT 1;", (osdial.VARserver_ip,puid))
                for row in osdial.sql().fetchall():
                    clstart = row['start_time']
                    clstart_epoch = row['start_epoch']
                    clend = row['end_time']
                    clend_epoch = row['end_epoch']
                    cllensec = row['length_in_sec']
                    cllenmin = row['length_in_min']
                    psid = None
                    plist = None
                    plead = None
                    pcom = None
                    if re.search('^PBX-IN',SQLfile):
                        psid = 'PBXIN'
                        plist = '10'
                        pcom = "PBX/External Inbound Call, from %s to %s." % (pcnl, pext)
                    else:
                        psid = 'PBXOUT'
                        plist = '11'
                        pcom = "PBX/External Outbound Call, from %s to %s." % (pext, pcnl)
                    if not pext:
                        pext = "0000000000"
                    if not pcnl:
                        pcnl = "0000000000"
                    osdial.sql().execute("INSERT INTO osdial_list SET entry_date=%s,modify_date=%s,status=%s,user=%s,vendor_lead_code=%s,custom1=%s,custom2=%s,external_key=%s,source_id=%s,phone_code='1',phone_number=%s,list_id=%s,comments=%s;", (clstart,clend,pcmp,pcmp,pext,pext,pext,"%s:%s" % (osdial.VARserver_ip,puid),psid,pcnl,plist,pcom))

                    osdial.sql().execute("SELECT lead_id FROM osdial_list WHERE external_key=%s LIMIT 1;", ("%s:%s" % (osdial.VARserver_ip,puid)))
                    for row2 in osdial.sql().fetchall():
                        plead = row2['lead_id']

                    osdial.sql().execute("INSERT INTO recording_log SET channel=%s,server_ip=%s,extension=%s,start_time=%s,start_epoch=%s,end_time=%s,end_epoch=%s,length_in_sec=%s,length_in_min=%s,filename=%s,lead_id=%s,user=%s,uniqueid=%s;", (pcnl,osdial.VARserver_ip,pext,clstart,clstart_epoch,clend,clend_epoch,cllensec,cllenmin,SQLfile,plead,pcmp,puid))
    

            start_date = None
            dnt = True
            osdial.sql().execute("SELECT recording_id,start_time FROM recording_log WHERE filename=%s ORDER BY recording_id DESC LIMIT 1;", (SQLfile))
            scnt = osdial.sql().rowcount
            if scnt:
                for row in osdial.sql().fetchall():
                    recording_id = row['recording_id']
                    start_date = re.sub('\s.*','',str(row['start_time']))
                    dnt = True
            else:
                dnt = False
                start_date = "NOTFOUND"

            start_date_path = ""
            via = "unknown"
            sts = False
            if not os.path.exists("%s/%s" % (osdial.PATHarchive_backup,ALLfile)):
                shutil.copyfile("%s/%s" % (srcdir,ALLfile), "%s/%s" % (osdial.PATHarchive_backup,ALLfile))
            if re.search('^127.0.0.1$|^localhost$',ahost,re.IGNORECASE):
                via = "move"
                os.rename("%s/%s" % (srcdir,ALLfile), "%s/%s/%s" % (osdial.PATHarchive_home, osdial.PATHarchive_unmixed, ALLfile))
                sts = True
            else:
                via = "FTP"
                try:
                    ftp = FTP()
                    if opt['verbose']:
                        ftp.set_debuglevel(1)
                    if opt['debug']:
                        ftp.set_debuglevel(2)
                    ftp.connect(ahost,aport)
                    ftp.login(auser,apass)
                    ftp.cwd(apath)
                    if not opt['nodatedir']:
                        ftp.mkdir(start_date)
                        ftp.cwd(start_date)
                        start_date_path = "%s/" % start_date
                    ftp.storbinary("STOR %s" % ALLfile, open("%s/%s" % (srcdir,ALLfile), "rb"))
                    ftp.quit()
                    if os.path.exists("%s/%s" % (srcdir,ALLfile)):
                        os.unlink("%s/%s" % (srcdir,ALLfile))
                    sts=True
                except all_errors as e:
                    sts=False
                    logger.debug("FTP failed, error %s." % type(e))

            if sts:
                if dnt:
                    osdial.sql().execute("UPDATE recording_log SET location=%s WHERE recording_id=%s;", ("%s/%s/%s%s" % (awebpath,apath,start_date_path,ALLfile),recording_id))

                logger.info("Recording: %s  File: %s  SUCCESS Sent via %s to: %s@%s:%s/%s/%s" % (recording_id,ALLfile,via,auser,ahost,awebpath,apath,start_date_path))
            else:
                logger.info("Recording: %s  File: %s  FAILURE Sent via %s to: %s@%s:%s/%s/%s" % (recording_id,ALLfile,via,auser,ahost,awebpath,apath,start_date_path))
            


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
