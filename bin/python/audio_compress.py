#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import argparse, psutil

import MySQLdb, logging

from osdial import OSDial

PROGNAME = 'osdial_audio_compress'
VERSION = '0.1'

opt = {'verbose':False,'loglevel':False,'debug':False,'test':False,'daemon':False,'gsm':False,'mp3':False,'oldmp3':False,'ogg':False,'wav':False}
logger = None

def main(argv):
    parser = argparse.ArgumentParser(description='osdial_audio_compress - compress recordings.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--GSM', '--gsm', action='store_true', help='Compress into the GSM format.',dest='gsm')
    parser.add_argument('--MP3', '--mp3', action='store_true', help='Compress into the MP3 format.',dest='mp3')
    parser.add_argument('--OLDMP3', '--oldmp3', action='store_true', help='Compress into the old MP3 format.',dest='oldmp3')
    parser.add_argument('--OGG', '--ogg', action='store_true', help='Compress into the OGG format.',dest='ogg')
    parser.add_argument('--WAV', '--wav', action='store_true', help='Compress into the WAV format.',dest='wav')
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
        logger = logging.getLogger('comprec')
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

        handler = logging.FileHandler('%s/audio_compress.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
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

    logger.info("Starting comprecordings_process()")
    comprecordings_process()


def comprecordings_process():
    """
    Compresses recordings to a specified format.
    """
    osdial = OSDial()
    logger = logging.getLogger('comprec')

    if not opt['mp3'] and not opt['oldmp3'] and not opt['gsm'] and not opt['wav'] and not opt['ogg']:
        if osdial.configuration['ArchiveMixFormat']:
            opt[osdial.configuration['ArchiveMixFormat'].lower()] = osdial.configuration['ArchiveMixFormat']

    soxbin = ''
    if os.path.isfile('/usr/bin/sox'):
        soxbin = '/usr/bin/sox'
    elif os.path.isfile('/usr/local/bin/sox'):
        soxbin = '/usr/local/bin/sox'

    lamebin = ''
    lameopts = ''
    if os.path.isfile('/usr/bin/toolame'):
        lamebin = '/usr/bin/toolame'
        lameopts = '-s 16 -b 16 -m j'
    elif os.path.isfile('/usr/local/bin/toolame'):
        lamebin = '/usr/local/bin/toolame'
        lameopts = '-s 16 -b 16 -m j'
    elif os.path.isfile('/usr/bin/lame'):
        lamebin = '/usr/bin/lame'
        lameopts = '-b 16 -m m --silent'
    elif os.path.isfile('/usr/local/bin/lame'):
        lamebin = '/usr/local/bin/lame'
        lameopts = '-b 16 -m m --silent'

    if opt['oldmp3']:
        if not lamebin:
            logger.error("Can't find lame binary! Exiting.")
            sys.exit(2)
    else:
        if not soxbin:
            logger.error("Can't find sox binary! Exiting.")
            sys.exit(2)
    
    locked_files = {}
    for p in psutil.process_iter():
        try:
            if len(p.cmdline) and re.search('asterisk', p.cmdline[0]):
                for file in p.get_open_files():
                    locked_files[file.path] = True
        except psutil.AccessDenied, e:
            pass

    srcdir = "%s/%s" % (osdial.PATHarchive_home, osdial.PATHarchive_unmixed)
    dstdir = "%s/%s/.." % (osdial.PATHarchive_home, osdial.PATHarchive_mixed)
    for afile in os.listdir(srcdir):
        comp_file = False
        if len(afile) > 4 and not re.search('out\.|in\.|lost\+found',afile,re.IGNORECASE) and not os.path.isdir("%s/%s" % (srcdir, afile)):
            if not locked_files.has_key("%s/%s" % (srcdir,afile)):
                comp_file = True
        if comp_file:
            SQLfile = re.sub('-all\.wav|-all\.gsm','',afile,re.IGNORECASE)
            location = None
            recording_id = None
            CNVfile = "%s" % afile

            osdial.sql().execute("SELECT SQL_NO_CACHE recording_id FROM recording_log WHERE filename=%s ORDER BY recording_id DESC LIMIT 1;", (SQLfile))
            for row in osdial.sql().fetchall():
                recording_id = row['recording_id']

            if opt['test']:
                logger.info("|%s|%s|%s|     |%s|", recording_id, afile, CNVfile, SQLfile)

            elif os.stat("%s/%s" % (srcdir, afile)).st_size == 0:
                if os.path.exists("%s/%s" % (srcdir, afile)):
                    os.unlink("%s/%s" % (srcdir, afile))
                location = ""
                logger.info("Removed empty file: %s/%s   |%s|%s|%s|",srcdir,afile,recording_id,SQLfile,location)

            elif os.system("/usr/bin/soxi '%s/%s' >/dev/null 2>&1" % (srcdir, afile)):
                if os.path.exists("%s/%s" % (srcdir, afile)):
                    os.unlink("%s/%s" % (srcdir, afile))
                location = ""
                logger.info("Removed corrupted file: %s/%s   |%s|%s|%s|",srcdir,afile,recording_id,SQLfile,location)

            elif opt['mp3'] or opt['gsm'] or opt['wav'] or opt['ogg']:
                if opt['gsm']:
                    CNVfile = re.sub('-all\.wav','-all.gsm', CNVfile, re.IGNORECASE)
                elif opt['ogg']:
                    CNVfile = re.sub('-all\.wav','-all.ogg', CNVfile, re.IGNORECASE)
                elif opt['wav']:
                    CNVfile = re.sub('-all\.wav','-all.wav', CNVfile, re.IGNORECASE)
                elif opt['mp3']:
                    CNVfile = re.sub('-all\.wav','-all.mp3', CNVfile, re.IGNORECASE)

                os.system("%s '%s/%s' '%s/mixed/%s' >/dev/null 2>&1" % (soxbin, srcdir, afile, dstdir, CNVfile))
                if os.path.exists("%s/mixed/%s" % (dstdir, CNVfile)):
                    if os.path.exists("%s/%s" % (srcdir, afile)):
                        os.unlink("%s/%s" % (srcdir, afile))

                location = "http://%s/%s/../mixed/%s" % (osdial.VARserver_ip,osdial.PATHarchive_mixed,CNVfile)
                logger.info("Compressed: %s   to   %s   |%s|%s|%s|",afile,CNVfile,recording_id,SQLfile,location)

            elif opt['oldmp3']:
                CNVfile = re.sub('-all\.wav','-all.mp3', CNVfile, re.IGNORECASE)

                if re.search('toolame',lamebin):
                    os.rename("%s/%s" % (srcdir, afile),"/tmp")
                    os.system("%s '/tmp/%s' -r 16000 -c 1 '%s/%s' resample -ql >/dev/null 2>&1" % (soxbin, afile, srcdir, afile))
                    if os.path.exists("/tmp/%s" % afile):
                        os.unlink("/tmp/%s" % afile)

                os.system("%s %s '%s/%s' '%s/mixed/%s' >/dev/null 2>&1" % (lamebin, lameopts, srcdir, afile, dstdir, CNVfile))
                if os.path.exists("%s/mixed/%s" % (dstdir, CNVfile)):
                    if os.path.exists("%s/%s" % (srcdir, afile)):
                        os.unlink("%s/%s" % (srcdir, afile))

                location = "http://%s/%s/../mixed/%s" % (osdial.VARserver_ip,osdial.PATHarchive_mixed,CNVfile)
                logger.info("Compressed: %s   to   %s   |%s|%s|%s|",afile,CNVfile,recording_id,SQLfile,location)

            if not opt['test']:
                osdial.sql().execute("UPDATE recording_log SET location=%s WHERE recording_id=%s;", (location,recording_id))

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
