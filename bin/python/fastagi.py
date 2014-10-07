#!/usr/bin/python
#
# Copyright (C) 2014  Lott Caskey  <lottcaskey@gmail.com>
#

import sys, os, pwd, re, time, pprint, gc
import pystrix, threading, argparse

import MySQLdb, logging

from osdial import OSDial
from osdial.sql import OSDialSQL as OSDialSQL2

PROGNAME = 'osdial_fastagi'
VERSION = '0.1'
opt = {'loglevel':False,'daemon':False,'debug':False}

class FastAGIServer(threading.Thread):
    _fagi_server = None
    logger = None

    def __init__(self):
        threading.Thread.__init__(self)
        self.daemon = True

        self._fagi_server = pystrix.agi.FastAGIServer(port=4577, daemon_threads=True)

        self._fagi_server.register_script_handler(re.compile('call_log'), self._call_log_handler)
        self._fagi_server.register_script_handler(None, self._noop_handler)

        self.sa = self._fagi_server.socket.getsockname()

    def _call_log_handler(self, agi, args, kwargs, match, path):
        threadname = threading.current_thread().name
        threadid = re.sub('\D','',threading.current_thread().name)
        self.logger = logging.getLogger('fastagi.calllog')
        osdial = None
        self.logger.debug("%s - Started.", threadname)
        try:
          osdial = OSDial()
          ZorD = 'Zap'
          if re.search('1\.6|1\.8|1\.10|1\.11',osdial.server['asterisk_version']):
              ZorD = 'DAHDI'
          stage = 'START'
          DShasvalue = 1
          vars = agi.get_environment()
          vars['origchannel'] = vars['agi_channel']
          vars['agi_dnid'] = re.sub('unknown', '', vars['agi_dnid'])
          lnktmp = agi.execute(pystrix.agi.core.GetVariable("CDR(linkedid)"))
          if lnktmp:
              vars['linkedid'] = lnktmp
          vars['carrierid'] = 0
          crdtmp = agi.execute(pystrix.agi.core.GetVariable("carrierid"))
          if crdtmp:
              vars['carrierid'] = int(crdtmp or 0)
          vars['PRI'] = None
          vars['DEBUG'] = None
          vars['hangup_cause'] = None
          vars['dialstatus'] = None
          vars['dial_time'] = 0
          vars['answered_time'] = 0
          vars['ring_time'] = 0
          
  
          if re.search('--HVcauses--',vars['agi_request']):
              hargs = re.split('-----',vars['agi_request'])
              vars['PRI'] = re.sub('.*--HVcauses--','',hargs[0])
              vars['DEBUG'] = hargs[1]
              vars['hangup_cause'] = hargs[2]
              vars['dialstatus'] = hargs[3]
              if hargs[4]:
                  vars['dial_time'] = int(hargs[4] or 0)
              if hargs[5]:
                  vars['answered_time'] = int(hargs[5] or 0)
              if vars['dial_time'] > vars['answered_time']:
                  vars['ring_time'] = vars['dial_time'] - vars['answered_time']
              if vars['dialstatus'] == "":
                  DShasvalue = 0
  
          if re.search('--fullCID--',vars['agi_request']):
              cargs = re.split('-----',vars['agi_request'])
              vars['agi_callerid'] = cargs[2]
              vars['agi_calleridname'] = cargs[3]
          else:
              if re.search('"',vars['agi_calleridname']):
                  vars['agi_calleridname'] = re.sub('"','',vars['agi_calleridname'])
              if (len(vars['agi_callerid']) > 5 and (vars['agi_callerid'] != None or re.search('unknown|private|00000000|5551212',vars['agi_callerid']))) or (len(vars['agi_calleridname'])>17 and re.search('\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d',vars['agi_calleridname'])):
                  vars['agi_callerid'] = vars['agi_calleridname']
              if re.search('^\*\d\d\d\d\d\d\d\d\d\d\*',vars['agi_extension']):
                  vars['agi_callerid'] = vars['agi_extension']
                  vars['agi_callerid'] = re.sub('\*\d\d\d\d\*$','',vars['agi_callerid'])
                  vars['agi_callerid'] = re.sub('^\*','',vars['agi_callerid'])
                  vars['agi_extension'] = re.sub('^\*\d\d\d\d\d\d\d\d\d\d\*','',vars['agi_extension'])
                  vars['agi_extension'] = re.sub('\*$','',vars['agi_extension'])
              vars['agi_calleridname'] = vars['agi_callerid']
  
          if re.search('^h$',vars['agi_extension']):
              stage = 'END'
  
  
          # Start call_log process.
          if re.search('^call_log',vars['agi_network_script']):
              if stage == "START":
                  channel_group = ""
                  number_dialed = ""
                  is_client_phone = None
                  if vars['agi_uniqueid'] != vars['linkedid'] and vars['linkedid'] != "":
                      vars['agi_uniqueid'] = vars['linkedid']
                  vars['orig_extension'] = vars['agi_extension']
                  vars['agi_extension'] = re.sub('^dial.','',vars['agi_extension'])
                  for var in vars:
                      self.logger.info("%s|%s|vars:%s = %s", threadid, stage, var, vars[var])
                  channel_group = vars['agi_type'] + " Channel Line"
  
                  if re.search('^SIP',vars['agi_channel']):
                      channel_line = re.sub('^SIP\/|\-.*','',vars['agi_channel'])
                      osdial.sql().execute("SELECT count(*) AS cnt FROM phones WHERE server_ip=%s AND extension=%s AND protocol='SIP';",(osdial.VARserver_ip,channel_line))
                      for row in osdial.sql().fetchall():
                          is_client_phone = int(row['cnt'] or 0)
                      if is_client_phone < 1:
                          channel_group = "SIP Trunk Line"
                      else:
                          channel_group = "SIP Client Phone"
                          number_dialed = vars['agi_extension']
                          vars['agi_extension'] = channel_line
                  elif re.search('^IAX2',vars['agi_channel']):
                      channel_line = re.sub('^IAX2\/|\-.*','',vars['agi_channel'])
                      osdial.sql().execute("SELECT count(*) AS cnt FROM phones WHERE server_ip=%s AND extension=%s AND protocol='IAX2';",(osdial.VARserver_ip,channel_line))
                      for row in osdial.sql().fetchall():
                          is_client_phone = int(row['cnt'] or 0)
                      if is_client_phone < 1:
                          channel_group = "IAX2 Trunk Line"
                      else:
                          channel_group = "IAX2 Client Phone"
                          number_dialed = vars['agi_extension']
                          vars['agi_extension'] = channel_line
                  elif re.search('^'+ZorD+'\/',vars['agi_channel']):
                      channel_line = re.sub('^'+ZorD+'\/','',vars['agi_channel'])
                      osdial.sql().execute("SELECT count(*) AS cnt FROM phones WHERE server_ip=%s AND extension=%s AND protocol=%s;",(osdial.VARserver_ip,channel_line,ZorD))
                      for row in osdial.sql().fetchall():
                          is_client_phone = int(row['cnt'] or 0)
                      if is_client_phone < 1:
                          channel_group = ZorD + " Trunk Line"
                      else:
                          channel_group = ZorD + " Client Phone"
                          number_dialed = vars['agi_extension']
                          vars['agi_extension'] = channel_line
                  elif re.search('^Local\/',vars['agi_channel']):
                      channel_line = re.sub('^Local\/|\@.*','',vars['agi_channel'])
                      osdial.sql().execute("SELECT count(*) AS cnt,extension FROM phones WHERE server_ip=%s AND dialplan_number=%s AND protocol='EXTERNAL' LIMIT 1;",(osdial.VARserver_ip,channel_line))
                      for row in osdial.sql().fetchall():
                          is_client_phone = int(row['cnt'] or 0)
                          phone_ext = row['extension']
                      if is_client_phone < 1:
                          channel_group = "Local Channel Line"
                      else:
                          channel_group = "EXTERNAL Client Phone"
                          number_dialed = channel_line
                          vars['agi_extension'] = phone_ext
  
                  if re.search('^V|^M|^DC',vars['agi_accountcode']) and re.search('\d\d\d\d\d\d\d\d\d',vars['agi_accountcode']) and len(number_dialed) < 1:
                      osdial.sql().execute("SELECT cmd_line_b,cmd_line_d FROM osdial_manager WHERE callerid=%s LIMIT 1;",(vars['agi_accountcode']))
                      for row in osdial.sql().fetchall():
                          extension = ''
                          if not row['cmd_line_b']:
                              row['cmd_line_b']
                          if not row['cmd_line_d']:
                              row['cmd_line_d']
                          if re.search('^DC',vars['agi_accountcode']):
                              extension = re.sub('Exten\:\s+','',row['cmd_line_d'])
                              number_dialed = re.sub('Channel\:\s+Local\/|\@.*','',row['cmd_line_b'])
                          elif re.search('^V',vars['agi_accountcode']):
                              extension = re.sub('Exten\:\s+','',row['cmd_line_b'])
                              number_dialed = re.sub('Channel\:\s+Local\/|\@.*','',row['cmd_line_d'])
                          elif re.search('^M',vars['agi_accountcode']):
                              extension = re.sub('Channel\:\s+Local\/|\@.*','',row['cmd_line_d'])
                              number_dialed = re.sub('Exten\:\s+','',row['cmd_line_b'])
                          vars['agi_extension'] = extension
                      vars['agi_extension'] = re.sub('\D','',vars['agi_extension'])
                      number_dialed = re.sub('^.'+osdial.settings['default_phone_code'],osdial.settings['default_phone_code'],number_dialed)
                      number_dialed = re.sub('\D','',number_dialed)
                      if len(number_dialed) < 1:
                          number_dialed = vars['agi_extension']
  
                  if re.search('^Y',vars['agi_accountcode']) and re.search('\d\d\d\d\d\d\d\d\d',vars['agi_accountcode']) and len(number_dialed) < 1:
                      number_dialed = vars['agi_extension']
                      if len(number_dialed) < 1:
                          number_dialed = ''
  
                  if re.search('^SIP|^IAX2',vars['agi_channel']) or (is_client_phone > 0 and len(channel_group) < 1):
                      if is_client_phone > 0:
                          channel_group = 'Client Phone'
                      else:
                          if re.search('^Y',vars['agi_accountcode']):
                              channel_group = 'Inbound'
                              if re.search('^800|^888|^877|^866|^855',vars['agi_extension']) and len(number_dialed) == 10:
                                  channel_group = 'Inbound 800'
                          else:
                              ccprefix = ""
                              rawnumber = number_dialed
                              tempnum = re.sub('(^[789]0011|^[789]011|^[789]001|^[789]00)','',rawnumber)
                              if tempnum == rawnumber:
                                  tempnum = re.sub('(^0011|^011|^001|^00)','',tempnum)
                              else:
                                  if re.search('^1[2-9][0-9][0-9][2-9][0-9][0-9][0-9][0-9][0-9][0-9]$',tempnum):
                                      ccprefix = '1'
                                  else:
                                      tempnum = re.sub('^1','',tempnum)
                              rawnumber = tempnum
  
  
                              # UK
                              if re.search('^440',rawnumber) and len(rawnumber) >= 12 and len(rawnumber) <= 13:
                                  number_dialed = re.sub('^.','',number_dialed)
                                  channel_group = 'Outbound GBR'
                                  ccprefix = '44'
                              elif re.search('^44',rawnumber) and len(rawnumber) >= 11 and len(rawnumber) <= 12:
                                  channel_group = 'Outbound GBR'
                                  ccprefix = '44'
                              # Sweden
                              elif re.search('^460',rawnumber) and len(rawnumber) >= 10 and len(rawnumber) <= 12:
                                  channel_group = 'Outbound SWE'
                                  ccprefix = '46'
                              elif re.search('^46',rawnumber) and len(rawnumber) >= 9 and len(rawnumber) <= 11:
                                  channel_group = 'Outbound SWE'
                                  ccprefix = '46'
                              # Poland
                              elif re.search('^48',rawnumber) and len(rawnumber) == 11:
                                  channel_group = 'Outbound POL'
                                  ccprefix = '48'
                              # Australia
                              elif re.search('^610',rawnumber) and len(rawnumber) == 12:
                                  channel_group = 'Outbound AUS'
                                  ccprefix = '61'
                              elif re.search('^61',rawnumber) and len(rawnumber) == 11:
                                  channel_group = 'Outbound AUS'
                                  ccprefix = '61'
                              # New Zealand
                              elif re.search('^640',rawnumber) and len(rawnumber) >= 11 and len(rawnumber) <= 12:
                                  channel_group = 'Outbound NZL'
                                  ccprefix = '64'
                              elif re.search('^64',rawnumber) and len(rawnumber) >= 10 and len(rawnumber) <= 11:
                                  channel_group = 'Outbound NZL'
                                  ccprefix = '64'
                              # Honk Kong
                              elif re.search('^852',rawnumber) and len(rawnumber) == 11:
                                  channel_group = 'Outbound HKG'
                                  ccprefix = '852'
                              # Macau
                              elif re.search('^853',rawnumber) and len(rawnumber) == 11:
                                  channel_group = 'Outbound MAC'
                                  ccprefix = '853'
                              # China
                              elif re.search('^860',rawnumber) and len(rawnumber) >= 13 and len(rawnumber) <= 14:
                                  channel_group = 'Outbound CHN'
                                  ccprefix = '86'
                              elif re.search('^86',rawnumber) and len(rawnumber) >= 12 and len(rawnumber) <= 13:
                                  channel_group = 'Outbound CHN'
                                  ccprefix = '86'
                              # North America 800#s
                              elif re.search('^(1800|1888|1877|1866|1855)[2-9][0-9][0-9][0-9][0-9][0-9][0-9]$',rawnumber) and len(rawnumber) == 11:
                                  channel_group = 'Outbound 800'
                                  ccprefix = '1'
                              # North America
                              elif re.search('^1[2-9][0-9][0-9][2-9][0-9][0-9][0-9][0-9][0-9][0-9]$',rawnumber) and len(rawnumber) == 11:
                                  channel_group = 'Outbound'
                                  ccprefix = '1'
                              elif re.search('^1[2-9][0-9][0-9][2-9][0-9][0-9][0-9][0-9][0-9][0-9]$',rawnumber) and len(rawnumber) == 10:
                                  channel_group = 'Outbound'
                                  ccprefix = '1'
                              else:
                                  channel_group = 'Outbound Unknown'
                                  ccprefix = ''
                              number_dialed = rawnumber
  
                  osdial.sql().execute("SELECT COUNT(*) AS cnt FROM call_log WHERE uniqueid=%s AND server_ip=%s;",(vars['agi_uniqueid'],osdial.VARserver_ip))
                  self.logger.info("%s|%s|call_log select|%s", threadid, stage, osdial.sql()._executed)
                  for row in osdial.sql().fetchall():
                      if int(row['cnt'] or 0) == 0:
                          osdial.sql().execute("INSERT INTO call_log SET uniqueid=%s,channel=%s,channel_group=%s,type=%s,server_ip=%s,extension=%s,number_dialed=%s,start_time=NOW(),start_epoch=UNIX_TIMESTAMP(),end_time='0000-00-00 00:00:00',end_epoch=0,length_in_sec=0,length_in_min=0,caller_code=%s,carrier_id=%s,cid_name=%s,cid_number=%s,dnid=%s,language=%s;", (vars['agi_uniqueid'], vars['agi_channel'], channel_group, vars['agi_type'], osdial.VARserver_ip, vars['agi_extension'], number_dialed, vars['agi_accountcode'], vars['carrierid'], vars['agi_calleridname'], vars['agi_callerid'], vars['agi_dnid'], vars['agi_language']))
                          self.logger.info("%s|%s|call_log insert|%s", threadid, stage, osdial.sql()._executed)
  
              # Call End Stage
              else:
                  if re.search('--HVcauses--',vars['agi_request']):
                      hargs = re.split('-----',vars['agi_request'])
                      vars['PRI'] = re.sub('.*--HVcauses--','',hargs[0])
                      vars['DEBUG'] = hargs[1]
                      vars['hangup_cause'] = hargs[2]
                      if vars['hangup_cause'] == None or vars['hangup_cause'] == '' or vars['hangup_cause'] == '0':
                          vars['hangup_cause'] = '16'
                      vars['dialstatus'] = hargs[3]
                      vars['dial_time'] = 0
                      if hargs[4]:
                          vars['dial_time'] = int(hargs[4] or 0)
                      vars['answered_time'] = 0
                      if hargs[5]:
                          vars['answered_time'] = int(hargs[5] or 0)
                      vars['ring_time'] = 0
                      if vars['dial_time'] > vars['answered_time']:
                          vars['ring_time'] = vars['dial_time'] - vars['answered_time']
  
                  if vars['agi_uniqueid'] != vars['linkedid'] and vars['linkedid'] != "":
                      vars['agi_uniqueid'] = vars['linkedid']
  
                  cnt = 0
                  if re.search('^M',vars['agi_accountcode']):
                      osdial.sql().execute("SELECT uniqueid,start_epoch,channel,end_epoch,channel_group FROM call_log WHERE (end_epoch IS NULL OR end_epoch=0) AND caller_code=%s AND channel=%s AND server_ip=%s;", (vars['agi_accountcode'],vars['agi_channel'],osdial.VARserver_ip))
                  else:
                      osdial.sql().execute("SELECT uniqueid,start_epoch,channel,end_epoch,channel_group FROM call_log WHERE uniqueid=%s AND server_ip=%s;", (vars['agi_uniqueid'],osdial.VARserver_ip))
                  CLstart_epoch = 0
                  CLend_epoch = 0
                  for row in osdial.sql().fetchall():
                      vars['agi_uniqueid'] = row['uniqueid']
                      CLstart_epoch = int(row['start_epoch'] or 0)
                      if re.search('^M',vars['agi_accountcode']):
                          vars['agi_channel'] = row['channel']
                      CLend_epoch = int(row['end_epoch'] or 0)
                      if CLend_epoch < 10000:
                          CLend_epoch = time.time()
                      channel_group = row['channel_group']
                      cnt += 1
  
                  for var in vars:
                      self.logger.info("%s|%s|vars:%s = %s", threadid, stage, var, vars[var])

                  if cnt > 0:
                      osdial.sql().execute("UPDATE call_log SET end_time=%s,end_epoch=%s,length_in_sec=%s,length_in_min=%s,channel=%s,isup_result=%s WHERE uniqueid=%s AND server_ip=%s;", (time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(CLend_epoch)), CLend_epoch, (CLend_epoch-CLstart_epoch), ((CLend_epoch-CLstart_epoch)/60), vars['agi_channel'], vars['hangup_cause'], vars['agi_uniqueid'], osdial.VARserver_ip ))
                      self.logger.info("%s|%s|call_log update|%s", threadid, stage, osdial.sql()._executed)
  
                      osdial.sql().execute("UPDATE call_log SET answer_time=IF(answer_time='0000-00-00 00-00-00',end_time,answer_time),answer_epoch=IF(answer_epoch IS NULL,end_epoch,answer_epoch) WHERE uniqueid=%s AND server_ip=%s;", (vars['agi_uniqueid'], osdial.VARserver_ip))
                      self.logger.info("%s|%s|call_log update|%s", threadid, stage, osdial.sql()._executed)
                  osdial.sql().execute("DELETE FROM live_inbound WHERE uniqueid=%s AND server_ip=%s;", (vars['agi_uniqueid'], osdial.VARserver_ip))
                  self.logger.info("%s|%s|live_inbound delete|%s", threadid, stage, osdial.sql()._executed)
  
                  # Park Log entries
                  osdial.sql().execute("SELECT UNIX_TIMESTAMP(parked_time) AS upt,UNIX_TIMESTAMP(grab_time) AS ugt FROM park_log WHERE uniqueid=%s AND server_ip=%s LIMIT 1;", (vars['agi_uniqueid'],osdial.VARserver_ip))
                  for row in osdial.sql().fetchall():
                      talked_sec = 0
                      parked_sec = 0
                      parked_time = int(row['upt'] or 0)
                      grab_time = int(row['ugt'] or 0)
                      if parked_time > grab_time:
                          parked_sec = (time.time() - parked_time)
                          talked_sec = 0
                      else:
                          talked_sec = (time.time() - parked_time)
                          parked_sec = (grab_time - parked_time)
                      osdial.sql().execute("UPDATE park_log SET status='HUNGUP',hangup_time=%s,parked_sec=%s,talked_sec=%s WHERE uniqueid=%s AND server_ip=%s;", (time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()), parked_sec, talked_sec, vars['agi_uniqueid'], osdial.VARserver_ip))
                  # End Park
  
                  lead_status = ''
                  lead_comments = ''
  
                  CIDlead_id = 0
                  if vars['agi_accountcode']:
                      CIDlead_id = int(re.sub('.','',vars['agi_accountcode'],11) or 0)
                  
                  if re.search('^Local',vars['origchannel']) and not re.search('^Local[\/\*\#]87......\@',vars['origchannel']):
                      cpa_found = 0
                      if re.search('^V\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d',vars['agi_callerid']):
                          osdial.sql().execute("SELECT cpa_result,cpa_detailed_result FROM osdial_cpa_log WHERE callerid=%s AND cpa_result NOT IN('Voice','Unknown','???','') ORDER BY id DESC LIMIT 1;", (vars['agi_accountcode']))
                          for row in osdial.sql().fetchall():
                              cpa_result = row['cpa_result']
                              cpa_detailed_result = row['cpa_detailed_result']
                              if re.search('license',cpa_detailed_result):
                                  cpa_result = "License-Reject"
                              if re.search('Busy',cpa_result):
                                  lead_status = "CPRB"
                                  VDAC_status = "BUSY"
                                  cpa_found += 1
                              elif re.search('All-Trunks-Busy',cpa_result):
                                  lead_status = "CPRATB"
                                  VDAC_status = "CONGESTION"
                                  cpa_found += 1
                              elif re.search('Reject',cpa_result):
                                  lead_status = "CPRCR"
                                  VDAC_status = "CONGESTION"
                                  cpa_found += 1
                              elif re.search('License-Reject',cpa_result):
                                  lead_status = "CPRLR"
                                  VDAC_status = "CONGESTION"
                                  cpa_found += 1
                              elif re.search('Unknown',cpa_result):
                                  lead_status = "CPRUNK"
                                  VDAC_status = "CPA"
                                  cpa_found += 1
                              elif re.search('Sit-No-Circuit',cpa_result):
                                  lead_status = "CPRSNC"
                                  VDAC_status = "CONGESTION"
                                  cpa_found += 1
                              elif re.search('Sit-Reorder',cpa_result):
                                  lead_status = "CPRSRO"
                                  VDAC_status = "CONGESTION"
                                  cpa_found += 1
                              elif re.search('Sit-Intercept',cpa_result):
                                  lead_status = "CPRSIC"
                                  VDAC_status = "DISCONNECT"
                                  cpa_found += 1
                              elif re.search('Sit-Unknown',cpa_result):
                                  lead_status = "CPRSIO"
                                  VDAC_status = "DISCONNECT"
                                  cpa_found += 1
                              elif re.search('Sit-Vacant',cpa_result):
                                  lead_status = "CPRSVC"
                                  VDAC_status = "DISCONNECT"
                                  cpa_found += 1
                              elif re.search('No-Answer',cpa_result):
                                  lead_status = "CPRNA"
                                  VDAC_status = "CPA"
                                  cpa_found += 1
                              elif re.search('Fax|Modem',cpa_result):
                                  lead_status = "CPSFAX"
                                  VDAC_status = "CPA"
                                  cpa_found += 1
                              elif re.search('Answering-Machine',cpa_result):
                                  lead_status = "CPSAA"
                                  VDAC_status = "CPA"
                                  cpa_found += 1
                              elif re.search('\?\?\?',cpa_result):
                                  lead_status = "CPSUNK"
                                  VDAC_status = "CPA"
                                  cpa_found += 1
  
                      if re.search('^PRI$',vars['PRI']) and re.search('\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d\d',vars['agi_accountcode']) and ((re.search('BUSY|CONGESTION',vars['dialstatus']) or re.search('^18$|^19$|^21$|^22$|^27$|^29$|^34$|^38$|^102$',vars['hangup_cause']) or (re.search('CHANUNAVAIL',vars['dialstatus']) and re.search('^1$|^2$|^28$|^111$',vars['hangup_cause']) ) ) or cpa_found > 0):
                          if cpa_found < 1:
                              if re.search('CONGESTION',vars['dialstatus']):
                                  lead_status = 'CRC'
                                  VDAC_status = 'CONGESTION'
                              elif re.search('BUSY',vars['dialstatus']):
                                  lead_status = 'B'
                                  VDAC_status = 'BUSY'
                              elif re.search('CHANUNAVAIL',vars['dialstatus']):
                                  lead_status = 'DC'
                                  VDAC_status = 'DISCONNECT'
  
                              if re.search('^18$|^38$|^102$',vars['hangup_cause']):
                                  lead_status = 'CRF'
                                  VDAC_status = 'CONGESTION'
                              elif re.search('^21$|^29$|^111$',vars['hangup_cause']):
                                  lead_status = 'CRR'
                                  VDAC_status = 'CONGESTION'
                              elif re.search('^27$',vars['hangup_cause']):
                                  lead_status = 'CRO'
                                  VDAC_status = 'CONGESTION'
                              elif re.search('^19$|^34$',vars['hangup_cause']):
                                  lead_status = 'CRC'
                                  VDAC_status = 'CONGESTION'
                              elif re.search('^22$',vars['hangup_cause']):
                                  lead_status = 'DC'
                                  VDAC_status = 'DISCONNECT'
  
                          osdial.sql().execute("UPDATE osdial_list SET status=%s WHERE lead_id=%s;", (lead_status, CIDlead_id))
                          self.logger.info("%s|%s|osdial_list update|%s", threadid, stage, osdial.sql()._executed)
                          osdial.sql().execute("UPDATE osdial_auto_calls SET status=%s WHERE callerid=%s;", (VDAC_status, vars['agi_accountcode']))
                          self.logger.info("%s|%s|osdial_auto_calls update|%s", threadid, stage, osdial.sql()._executed)
                          osdial.sql().execute("UPDATE osdial_log FORCE INDEX(lead_id) SET status=%s WHERE lead_id=%s AND uniqueid LIKE %s AND server_ip=%s;", (lead_status, CIDlead_id, re.sub('\.\d+$','',vars['agi_uniqueid']) + '%', osdial.VARserver_ip))
                          self.logger.info("%s|%s|osdial_log update|%s", threadid, stage, osdial.sql()._executed)
  
  
  
  
  
                  else:
                      # Find and delete osdial_auto_calls
                      osdial.sql().execute("SELECT lead_id,callerid,campaign_id,alt_dial,stage,UNIX_TIMESTAMP(call_time) AS start_epoch,uniqueid,status FROM osdial_auto_calls WHERE channel=%s AND (uniqueid=%s OR callerid=%s) LIMIT 1;", (vars['agi_channel'],vars['agi_uniqueid'],vars['agi_accountcode']))
                      accnt = 0
                      autocalls = {'lead_id':0,'callerid':'','campaign_id':'','alt_dial':'','stage':'','start_epoch':0,'uniqueid':'','status':'','user':'','comments':'','term_reason':'','closecallid':0}
                      olog = {'start_epoch':0,'end_epoch':0}
                      liveagents = {'live_agent_id':0,'user':'','extension':'','uniqueid':'','last_call_time':'','server_ip':'','conf_exten':''}
                      company_id = ''
                      rows = osdial.sql().fetchall()
                      if rows is not None:
                          accnt = len(rows)  
                      if accnt > 0:
                          for row in rows:
                              for col in row:
                                  autocalls[col] = row[col]
  
                          if int(osdial.settings['enable_multicompany'] or 0) > 0:
                              if re.search('^\d\d\d...', autocalls['campaign_id']):
                                  company_id = (int(re.sub('^(\d\d\d).*','\g<1>',autocalls['campaign_id']) or 0) - 100)
  
                          osdial.sql().execute("SELECT start_epoch,end_epoch FROM osdial_log WHERE uniqueid=%s AND server_ip=%s LIMIT 1;", (vars['agi_uniqueid'],osdial.VARserver_ip))
                          for row in osdial.sql().fetchall():
                              for col in row:
                                  olog[col] = row[col]
  
                          osdial.sql().execute("SELECT live_agent_id,user,extension,uniqueid,last_call_time,server_ip,conf_exten FROM osdial_live_agents WHERE uniqueid=%s AND (extension LIKE 'R/va%%' OR extension LIKE 'R/tmp%%') LIMIT 1;",(vars['agi_uniqueid']))
                          lacnt = 0
                          rows = osdial.sql().fetchall()
                          if rows is not None:
                              lacnt = len(rows)  
                          if lacnt > 0:
                              for row in rows:
                                  for col in row:
                                      liveagents[col] = row[col]
  
  
                              pauseepoch = int(autocalls['start_epoch'] or 0)
                              waitepoch = int(autocalls['start_epoch'] or 0)
                              talkepoch = int(autocalls['start_epoch'] or 0)
                              if int(olog['start_epoch']or 0) > 0:
                                  talkepoch = int(olog['start_epoch'] or 0)
                              dispoepoch = time.time()
                              if int(olog['end_epoch'] or 0) > 0:
                                  dispoepoch = int(olog['end_epoch'] or 0)
                              waitsec = (talkepoch - waitepoch)
                              talksec = (dispoepoch - talkepoch)
  
                              if re.search('^R\/tmp',liveagents['extension']) and re.search('^tmp',liveagents['user']):
                                  osdial.sql().execute("DELETE FROM osdial_users WHERE user=%s LIMIT 1;", (liveagents['user']))
                                  self.logger.info("%s|%s|osdial_users delete|%s", threadid, stage, osdial.sql()._executed)
                                  osdial.sql().execute("DELETE FROM osdial_live_agents WHERE uniqueid=%s LIMIT 1;", (vars['agi_uniqueid']))
                                  self.logger.info("%s|%s|osdial_live_agents delete|%s", threadid, stage, osdial.sql()._executed)
                              else:
                                  CIDdate = re.sub('[ \-:]','',time.strftime('%y-%m-%d %H:%M:%S', time.localtime()))
                                  KqueryCID = 'ULGH3956' + CIDdate
                                  osdial.sql().execute("INSERT INTO osdial_manager SET entry_date=%s,status='NEW',response='N',server_ip=%s,action='Command',callerid=%s,cmd_line_b=%s;", (time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()), liveagents['server_ip'],KqueryCID,'Command: meetme kick '+liveagents['conf_exten']+' all'))
                                  self.logger.info("%s|%s|osdial_manager insert|%s", threadid, stage, osdial.sql()._executed)
  
                                  alog_calls = 0
                                  osdial.sql().execute("SELECT COUNT(*) as cnt FROM osdial_agent_log WHERE user=%s AND event_time>=%s AND event_time<=%s;", (liveagents['user'],time.strftime('%Y-%m-%d 00:00:00', time.localtime()),time.strftime('%Y-%m-%d 23:59:59', time.localtime())))
                                  for row in osdial.sql().fetchall():
                                      alog_calls = int(row['cnt'] or 0)
                                  osdial.sql().execute("UPDATE osdial_live_agents SET status='READY',lead_id=0,uniqueid='',callerid='',channel='',calls_today=%s,last_call_finish=NOW() WHERE live_agent_id=%s;", (alog_calls,liveagents['live_agent_id']))
                                  self.logger.info("%s|%s|osdial_live_agents update|%s", threadid, stage, osdial.sql()._executed)
  
                              acct_method = 'NONE'
                              osdial.sql().execute("SELECT acct_method FROM osdial_companies WHERE id=%s LIMIT 1;",(company_id))
                              for row in osdial.sql().fetchall():
                                  acct_method = row['acct_method']
  
                              osdial.sql().execute("SELECT status,comments FROM osdial_list WHERE lead_id=%s LIMIT 1;",(autocalls['lead_id']))
                              lcnt = 0
                              rows = osdial.sql().fetchall()
                              if rows is not None:
                                  lcnt = len(rows)
                              if lcnt > 0:
                                  for row in rows:
                                      lead_status = row['status']
                                      lead_comments = row['comments']
  
                                  alog_id = 0
                                  osdial.sql().execute("SELECT agent_log_id FROM osdial_agent_log WHERE server_ip=%s AND uniqueid=%s AND lead_id=%s;",(osdial.VARserver_ip,liveagents['uniqueid'],autocalls['lead_id']))
                                  alcnt = 0
                                  rows = osdial.sql().fetchall()
                                  if rows is not None:
                                      alcnt = len(rows)
                                  if alcnt > 0:
                                      for row in rows:
                                          alog_id = int(row['agent_log_id'] or 0)
                                      osdial.sql().execute("UPDATE osdial_agent_log SET status=%s WHERE server_ip=%s AND uniqueid=%s AND lead_id=%s;",(lead_status,osdial.VARserver_ip,liveagents['uniqueid'],autocalls['lead_id']))
                                      self.logger.info("%s|%s|osdial_agent_log update|%s", threadid, stage, osdial.sql()._executed)
                                  else:
                                      osdial.sql().execute("INSERT INTO osdial_agent_log SET user=%s,server_ip=%s,event_time=%s,uniqueid=%s,lead_id=%s,campaign_id=%s,pause_epoch=%s,wait_epoch=%s,wait_sec=%s,talk_epoch=%s,talk_sec=%s,dispo_epoch=%s,status=%s,user_group='VIRTUAL',comments=%s;",(liveagents['user'],osdial.VARserver_ip,liveagents['last_call_time'],liveagents['uniqueid'],autocalls['lead_id'],autocalls['campaign_id'],pauseepoch,waitepoch,waitsec,talkepoch,talksec,dispoepoch,lead_status,lead_comments))
                                      self.logger.info("%s|%s|osdial_agent_log insert|%s", threadid, stage, osdial.sql()._executed)
                                      alog_id = osdial.sql().lastrowid
  
                                  if not re.search('^$|^NONE$|^RANGE$', acct_method):
                                      if re.search('^TALK_ROUNDUP$', acct_method):
                                          if (talksec % 60) > 0:
                                              talksec -= (talksec % 60)
                                              talksec += 60
                                      secs = talksec
                                      if re.search('^AVAILABLE$|^TOTAL$', acct_method):
                                          secs += waitsec
                                      if secs > 0:
                                          secs = secs * -1
                                          osdial.sql().execute("INSERT INTO osdial_acct_trans SET company_id=%s,agent_log_id=%s,trans_type='DEBIT',trans_sec=%s,created=NOW();", (company_id, alog_id, secs))
                                      if re.search('^AVAILABLE$|^TOTAL$', acct_method):
                                          if waitsec > 0:
                                              wsec = waitsec * -1
                                              osdial.sql().execute("INSERT INTO osdial_acct_trans_daily SET company_id=%s,agent_log_id=%s,trans_type='WAIT',trans_sec=%s,created=NOW();",(company_id,alog_id,wsec))
                                      if talksec > 0:
                                          tsec = talksec * -1
                                          osdial.sql().execute("INSERT INTO osdial_acct_trans_daily SET company_id=%s,agent_log_id=%s,trans_type='TALK',trans_sec=%s,created=NOW();",(company_id,alog_id,tsec))
  
  
                          osdial.sql().execute("DELETE FROM osdial_auto_calls WHERE uniqueid=%s ORDER BY call_time DESC;",(vars['agi_uniqueid']))
                          self.logger.info("%s|%s|osdial_auto_calls delete|%s", threadid, stage, osdial.sql()._executed)
  
  
                          if int(osdial.settings['enable_queuemetrics_logging'] or 0) > 0:
                              qsql = None
                              try:
                                  qopts = {'host':'','user':'','user':'','passwd':'','db':''}
                                  qopts['host'] = osdial.settings['queuemetrics_server_ip']
                                  qopts['user'] = osdial.settings['queuemetrics_login']
                                  qopts['passwd'] = osdial.settings['queuemetrics_pass']
                                  qopts['db'] = osdial.settings['queuemetrics_dbname']
                                  qsql = OSDialSQL2(None, qopts)
                                  qm_agent = 'NONE'
                                  qm_stage = re.sub('.*-','',autocalls['stage'])
                                  if qm_stage < 0.25:
                                      qm_stage = 0
                                  qsql().execute("SELECT agent FROM queue_log WHERE call_id=%s AND verb='CONNECT';", (autocalls['callerid']))
                                  qcnt = 0
                                  qrecs = qsql().fetchall()
                                  if qrecs is not None:
                                      qcnt = len(qrecs)
                                  if qcnt > 0:
                                      for qrec in qrecs:
                                          qm_agent = qrec['agent']
                                  if qcnt < 1:
                                      qsql().execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id=%s,queue=%s,agent=%s,verb='ABANDON',data1='1',data2='1',data3=%s,serverid=%s;", (time.time(), autocalls['callerid'], autocalls['campaign_id'],qm_agent,qm_stage,osdial.settings['queuemetrics_log_id']))
                                  else:
                                      qsql().execute("INSERT INTO queue_log SET partition='P001',time_id=%s,call_id=%s,queue=%s,agent=%s,verb='COMPLETECALLER',data1='1',data2=%s,data3='1',serverid=%s;", (time.time(), autocalls['callerid'], autocalls['campaign_id'],qm_agent,(time.time()-autocalls['start_epoch']),osdial.settings['queuemetrics_log_id']))
                              except:
                                  pass
                              if not qsql is None:
                                  qsql.close()
                                  qsql = None
  
  
  
                          # update osdial_log
                          odlcnt = 0
                          odclcnt = 0
                          if not re.search('^Y\d\d\d\d',vars['agi_accountcode']):
                              osdial.sql().execute("SELECT start_epoch,status,user,term_reason,comments FROM osdial_log FORCE INDEX(lead_id) WHERE lead_id=%s AND uniqueid LIKE %s AND server_ip=%s LIMIT 1;", (autocalls['lead_id'],re.sub('\.\d+$','',vars['agi_uniqueid']) + '%',osdial.VARserver_ip))
                              odlcnt = 0
                              rows = osdial.sql().fetchall()
                              if rows is not None:
                                  odlcnt = len(rows)
                              if odlcnt > 0:
                                  for row in rows:
                                      for col in row:
                                          autocalls[col] = row[col]
  
                          if odlcnt < 1 or re.search('^Y\d\d\d\d',vars['agi_accountcode']):
                              osdial.sql().execute("SELECT start_epoch,status,closecallid,user,term_reason,length_in_sec,queue_seconds,comments,call_date,uniqueid,lead_id,campaign_id FROM osdial_closer_log WHERE lead_id=%s AND call_date>%s AND end_epoch IS NULL ORDER BY call_date ASC LIMIT 1;", (autocalls['lead_id'],time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time() - 21600))))
                              odclcnt = 0
                              rows = osdial.sql().fetchall()
                              if rows is not None:
                                  odclcnt = len(rows)
                              if odclcnt > 0:
                                  for row in rows:
                                      for col in row:
                                          autocalls[col] = row[col]
  
                          if autocalls['comments'] is None:
                              autocalls['comments'] = ''
                          if autocalls['user'] is None:
                              autocalls['user'] = ''
                          if autocalls['status'] is None:
                              autocalls['status'] = ''
                          if autocalls['term_reason'] is None:
                              autocalls['term_reason'] = ''
  
                          if odlcnt + odclcnt > 0:
                              SQL_status = ''
                              SQL_term_reason = ''
                              if re.search('^NA$|^NEW$|^QUEUE$|^XFER$',autocalls['status']) and not re.search('REMOTE',autocalls['comments']) and not re.search('IVR',autocalls['user']):
                                  if not re.search('AGENT|CALLER|QUEUETIMEOUT',autocalls['term_reason']) and (re.search('VDAD|VDCL',autocalls['user']) or len(autocalls['user']) < 1):
                                      SQL_term_reason = "term_reason='ABANDON',"
                                  elif not re.search('AGENT|CALLER|QUEUETIMEOUT',autocalls['term_reason']):
                                      SQL_term_reason = "term_reason='CALLER',"
                                  else:
                                      SQL_term_reason = ""
                                  SQL_status = "status='DROP'," + SQL_term_reason
                                  osdial.sql().execute("UPDATE osdial_list set status='DROP' WHERE lead_id=%s;", (autocalls['lead_id']))
                                  self.logger.info("%s|%s|osdial_list update|%s", threadid, stage, osdial.sql()._executed)
                              else:
                                  SQL_status = "term_reason='CALLER',"
                              ODL_update = 0
                              if not re.search('^Y\d\d\d\d',vars['agi_accountcode']):
                                  osdial.sql().execute("UPDATE osdial_log FORCE INDEX(lead_id) SET " + SQL_status + " end_epoch=%s,length_in_sec=%s WHERE lead_id=%s AND uniqueid LIKE %s AND server_ip=%s;", (time.time(),(time.time()-autocalls['start_epoch']),autocalls['lead_id'],re.sub('\.\d+$','',vars['agi_uniqueid']) + '%',osdial.VARserver_ip))
                                  self.logger.info("%s|%s|osdial_log update|%s", threadid, stage, osdial.sql()._executed)
                                  ODL_update = 1
  
  
                              # update closer log
                              if int(autocalls['closecallid']) > 0 or ODL_update < 1:
                                  SQL_update = ''
                                  SQL_status = ''
                                  SQL_term_reason = ''
                                  SQL_queue_seconds = ''
                                  if re.search('^DONE$|^INCALL$|^XFER$',autocalls['status']):
                                      SQL_update = "term_reason='CALLER',"
                                  else:
                                      if not re.search('AGENT|CALLER|QUEUETIMEOUT|AFTERHOURS|HOLDRECALLXFER|HOLDTIME',autocalls['term_reason']) and (re.search('VDAD|VDCL',autocalls['user']) or len(autocalls['user']) < 1):
                                          SQL_term_reason = "term_reason='ABANDON',"
                                      elif not re.search('AGENT|CALLER|QUEUETIMEOUT|AFTERHOURS|HOLDRECALLXFER|HOLDTIME',autocalls['term_reason']):
                                          SQL_term_reason = "term_reason='CALLER',"
                                      else:
                                          SQL_term_reason = ""
                                      if re.search('QUEUE',autocalls['status']):
                                          SQL_status = "status='DROP',"
                                          SQL_queue_seconds = "queue_seconds='" + (time.time()-autocalls['start_epoch']) + "',"
                                      else:
                                          SQL_status = "status='" + autocalls['status'] + "',"
                                          SQL_queue_seconds = ""
                                      SQL_update = SQL_status + SQL_term_reason + SQL_queue_seconds
                              
                                              
                                  osdial.sql().execute("UPDATE osdial_closer_log SET " + SQL_update + " end_epoch=%s,length_in_sec=%s WHERE closecallid=%s;", (time.time(), (time.time()-autocalls['start_epoch']), autocalls['closecallid']))
                                  self.logger.info("%s|%s|osdial_closer_log update|%s", threadid, stage, osdial.sql()._executed)
  
                                  if re.search('VDCL', autocalls['user']):
                                      alog_id = 0
                                      pauseepoch = int(autocalls['start_epoch'] or 0)
                                      waitepoch = int(autocalls['start_epoch'] or 0)
                                      talkepoch = int(autocalls['start_epoch'] or 0)
                                      dispoepoch = time.time()
                                      waitsec = (talkepoch - waitepoch)
                                      talksec = (dispoepoch - talkepoch)
                                      osdial.sql().execute("SELECT agent_log_id FROM osdial_agent_log WHERE server_ip=%s AND uniqueid=%s AND lead_id=%s;", (osdial.VARserver_ip,autocalls['uniqueid'],autocalls['lead_id']))
                                      alcnt = 0
                                      rows = osdial.sql().fetchall()
                                      if rows is not None:
                                          alcnt = len(rows)
                                      if alcnt > 0:
                                          for row in rows:
                                              alog_id = int(row['agent_log_id'] or 0)
                                          osdial.sql().execute("UPDATE osdial_agent_log SET status=%s WHERE server_ip=%s AND uniqueid=%s AND lead_id=%s;", (autocalls['status'],osdial.VARserver_ip,autocalls['uniqueid'],autocalls['lead_id']))
                                      else:
                                          osdial.sql().execute("INSERT INTO osdial_agent_log SET user='VDCL',user_group='VDCL',server_ip=%s,event_time=%s,uniqueid=%s,lead_id=%s,campaign_id=%s,pause_epoch=%s,wait_epoch=%s,wait_sec=%s,talk_epoch=%s,talk_sec=%s,dispo_epoch=%s," + SQL_status + "comments=%s;", (osdial.VARserver_ip,autocalls['calldate'],autocalls['uniqueid'],autocalls['lead_id'],autocalls['campaign_id'],pauseepoch,waitepoch,waitsec,talkepoch,talksec,dispoepoch,autocalls['comments']))
                                          self.logger.info("%s|%s|osdial_agent_log insert|%s", threadid, stage, osdial.sql()._executed)
                                          alog_id = osdial.sql().lastrowid
  
                                      acct_method = 'NONE'
                                      osdial.sql().execute("SELECT acct_method FROM osdial_companies WHERE id=%s LIMIT 1;", (company_id))
                                      ccnt = 0
                                      rows = osdial.sql().fetchall()
                                      if rows is not None:
                                          ccnt = len(rows)
                                      if ccnt > 0:
                                          for row in rows:
                                              acct_method = row['acct_method']
  
                                      if not re.search('^$|^NONE$|^RANGE$', acct_method):
                                          if re.search('^TALK_ROUNDUP$', acct_method):
                                              if (talksec % 60) > 0:
                                                  talksec -= (talksec % 60)
                                                  talksec += 60
                                          secs = talksec
                                          if re.search('^AVAILABLE$|^TOTAL$', acct_method):
                                              secs += waitsec
                                          if secs > 0:
                                              secs = secs * -1
                                              osdial.sql().execute("INSERT INTO osdial_acct_trans SET company_id=%s,agent_log_id=%s,trans_type='DEBIT',trans_sec=%s,created=NOW();", (company_id, alog_id, secs))
                                          if re.search('^AVAILABLE$|^TOTAL$', acct_method):
                                              if waitsec > 0:
                                                  wsec = waitsec * -1
                                                  osdial.sql().execute("INSERT INTO osdial_acct_trans_daily SET company_id=%s,agent_log_id=%s,trans_type='WAIT',trans_sec=%s,created=NOW();",(company_id,alog_id,wsec))
                                          if talksec > 0:
                                              tsec = talksec * -1
                                              osdial.sql().execute("INSERT INTO osdial_acct_trans_daily SET company_id=%s,agent_log_id=%s,trans_type='TALK',trans_sec=%s,created=NOW();",(company_id,alog_id,tsec))
  
  
  
  
                          # AUTO ALT DIALING
                          altdial = {'auto_alt_dial':'','auto_alt_dial_statuses':'','use_internal_dnc':'','alt_phone':'','address3':'','gmt_offset_now':'','state':'','list_id':0,'dnc_method':''}
                          altdial['auto_alt_dial'] = 'NONE'
                          altdial['auto_alt_dial_statuses'] = ''
                          osdial.sql().execute("SELECT auto_alt_dial,auto_alt_dial_statuses,use_internal_dnc FROM osdial_campaigns WHERE campaign_id=%s LIMIT 1;", (autocalls['campaign_id']))
                          cacnt = 0
                          rows = osdial.sql().fetchall()
                          if rows is not None:
                              cacnt = len(rows)
                          if cacnt > 0:
                              for row in rows:
                                  for col in row:
                                      altdial[col] = row[col]
  
                          if re.search(' '+autocalls['status']+' | '+lead_status+' ', altdial['auto_alt_dial_statuses']):
                              osdial.sql().execute("SELECT alt_phone,address3,gmt_offset_now,state,list_id FROM osdial_list WHERE lead_id=%s LIMIT 1;", (autocalls['lead_id']))
                              licnt = 0
                              rows = osdial.sql().fetchall()
                              if rows is not None:
                                  licnt = len(rows)
                              if licnt > 0:
                                  for row in rows:
                                      for col in row:
                                          altdial[col] = row[col]
                                  altdial['alt_phone'] = re.sub('\D','',altdial['alt_phone'])
                                  altdial['address3'] = re.sub('\D','',altdial['address3'])
                                  
                              if int(osdial.settings['enable_multicompany'] or 0) > 0:
                                  osdial.sql().execute("SELECT dnc_method FROM osdial_companies WHERE id=%s;", (company_id))
                                  cocnt = 0
                                  rows = osdial.sql().fetchall()
                                  if rows is not None:
                                      cocnt = len(rows)
                                  if cocnt > 0:
                                      for row in rows:
                                          altdial['dnc_method'] = row['dnc_method']
  
                              # alt_phone
                              if re.search('ALT_ONLY|ALT_AND_ADDR3|ALT_ADDR3_AND_AFFAP', altdial['auto_alt_dial']) and re.search('NONE|MAIN',autocalls['alt_dial']):
                                  alt_dnc_count = 0
                                  if len(altdial['alt_phone']) > 5:
                                      if altdial['use_internal_dnc'] == 'Y':
                                          dncsskip = 0
                                          if int(osdial.settings['enable_multicompany'] or 0) > 0:
                                              if re.search('COMPANY|BOTH',altdial['dnc_method']):
                                                  osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_dnc_company WHERE company_id=%s AND (phone_number=%s OR phone_number=%s);", (company_id, altdial['alt_phone'],re.sub('^(\d\d\d).*','\g<1>XXXXXXX',altdial['alt_phone'])))
                                                  odccnt = 0
                                                  rows = osdial.sql().fetchall()
                                                  if rows is not None:
                                                      odccnt = len(rows)
                                                  if odccnt > 0:
                                                      for row in rows:
                                                          alt_dnc_count += int(row['cnt'] or 0)
                                              if re.search('COMPANY', altdial['dnc_method']):
                                                  dncsskip += 1
                                          if dncsskip == 0:
                                              osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_dnc WHERE (phone_number=%s OR phone_number=%s);", (altdial['alt_phone'],re.sub('^(\d\d\d).*','\g<1>XXXXXXX',altdial['alt_phone'])))
                                              odcnt = 0
                                              rows = osdial.sql().fetchall()
                                              if rows is not None:
                                                  odcnt = len(rows)
                                              if odcnt > 0:
                                                  for row in rows:
                                                      alt_dnc_count += int(row['cnt'] or 0)
                                      if alt_dnc_count < 1:
                                          osdial.sql().execute("INSERT INTO osdial_hopper SET lead_id=%s,campaign_id=%s,status='HOLD',list_id=%s,gmt_offset_now=%s,state=%s,alt_dial='ALT',user='',priority='25';", (autocalls['lead_id'],autocalls['campaign_id'],altdial['list_id'],altdial['gmt_offset_now'],altdial['state']))
                                  if alt_dnc_count > 0 or len(altdial['alt_phone']) <= 5:
                                      autocalls['alt_dial'] = 'ALT'
  
                              # address3
                              if (re.search('ADDR3_ONLY', altdial['auto_alt_dial']) and re.search('NONE|MAIN',autocalls['alt_dial'])) or (re.search('ALT_AND_ADDR3|ALT_ADDR3_AND_AFFAP', altdial['auto_alt_dial']) and re.search('ALT',autocalls['alt_dial'])):
                                  addr3_dnc_count = 0
                                  if len(altdial['address3']) > 5:
                                      if altdial['use_internal_dnc'] == 'Y':
                                          dncsskip = 0
                                          if int(osdial.settings['enable_multicompany'] or 0) > 0:
                                              if re.search('COMPANY|BOTH',altdial['dnc_method']):
                                                  osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_dnc_company WHERE company_id=%s AND (phone_number=%s OR phone_number=%s);", (company_id, altdial['address3'],re.sub('^(\d\d\d).*','\g<1>XXXXXXX',altdial['address3'])))
                                                  odccnt = 0
                                                  rows = osdial.sql().fetchall()
                                                  if rows is not None:
                                                      odccnt = len(rows)
                                                  if odccnt > 0:
                                                      for row in rows:
                                                          addr3_dnc_count += int(row['cnt'] or 0)
                                              if re.search('COMPANY', altdial['dnc_method']):
                                                  dncsskip += 1
                                          if dncsskip == 0:
                                              osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_dnc WHERE (phone_number=%s OR phone_number=%s);", (altdial['address3'],re.sub('^(\d\d\d).*','\g<1>XXXXXXX',altdial['address3'])))
                                              odcnt = 0
                                              rows = osdial.sql().fetchall()
                                              if rows is not None:
                                                  odcnt = len(rows)
                                              if odcnt > 0:
                                                  for row in rows:
                                                      addr3_dnc_count += int(row['cnt'] or 0)
                                      if addr3_dnc_count < 1:
                                          osdial.sql().execute("INSERT INTO osdial_hopper SET lead_id=%s,campaign_id=%s,status='HOLD',list_id=%s,gmt_offset_now=%s,state=%s,alt_dial='ADDR3',user='',priority='25';", (autocalls['lead_id'],autocalls['campaign_id'],altdial['list_id'],altdial['gmt_offset_now'],altdial['state']))
                                  if addr3_dnc_count > 0 or len(altdial['address3']) <= 5:
                                      autocalls['alt_dial'] = 'ADDR3'
  
                              # AFFAP
                              if re.search('ALT_ADDR3_AND_AFFAP', altdial['auto_alt_dial']) and re.search('ADDR3|AFFAP',autocalls['alt_dial']):
                                  aff_number = ''
                                  cur_aff = 1
                                  if not autocalls['alt_dial'] == "ADDR3":
                                      cur_aff = (int(re.sub('\D','',autocalls['alt_dial']) or 0) * 1)
                                  while cur_aff < 10:
                                      osdial.sql().execute("SELECT value FROM osdial_list_fields WHERE field_id=(SELECT id FROM osdial_campaign_fields WHERE name=%s LIMIT 1) AND lead_id=%s;", ("AFFAP" + cur_aff, autocalls['lead_id']))
                                      olfcnt = 0
                                      rows = osdial.sql().fetchall()
                                      if rows is not None:
                                          olfcnt = len(rows)
                                      if olfcnt > 0:
                                          for row in rows:
                                              aff_number = re.sub('\D','',row['value'])
                                      aff_dnc_count = 0
                                      if len(aff_number) > 5:
                                          if altdial['use_internal_dnc'] == 'Y':
                                              dncsskip = 0
                                              if int(osdial.settings['enable_multicompany'] or 0) > 0:
                                                  if re.search('COMPANY|BOTH',altdial['dnc_method']):
                                                      osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_dnc_company WHERE company_id=%s AND (phone_number=%s OR phone_number=%s);", (company_id, aff_number,re.sub('^(\d\d\d).*','\g<1>XXXXXXX',aff_number)))
                                                      odccnt = 0
                                                      rows = osdial.sql().fetchall()
                                                      if rows is not None:
                                                          odccnt = len(rows)
                                                      if odccnt > 0:
                                                          for row in rows:
                                                              aff_dnc_count += int(row['cnt'] or 0)
                                                  if re.search('COMPANY', altdial['dnc_method']):
                                                      dncsskip += 1
                                              if dncsskip == 0:
                                                  osdial.sql().execute("SELECT count(*) AS cnt FROM osdial_dnc WHERE (phone_number=%s OR phone_number=%s);", (aff_number,re.sub('^(\d\d\d).*','\g<1>XXXXXXX',aff_number)))
                                                  odcnt = 0
                                                  rows = osdial.sql().fetchall()
                                                  if rows is not None:
                                                      odcnt = len(rows)
                                                  if odcnt > 0:
                                                      for row in rows:
                                                          aff_dnc_count += int(row['cnt'] or 0)
                                          if aff_dnc_count < 1:
                                              osdial.sql().execute("INSERT INTO osdial_hopper SET lead_id=%s,campaign_id=%s,status='HOLD',list_id=%s,gmt_offset_now=%s,state=%s,alt_dial=%s,user='',priority='25';", (autocalls['lead_id'],autocalls['campaign_id'],altdial['list_id'],altdial['gmt_offset_now'],altdial['state'], "AFFAP" + cur_aff))
                                      if aff_dnc_count > 0 or len(aff_number) <= 5:
                                          autocalls['alt_dial'] = 'AFFAP' + cur_aff
                                          cur_aff += 1
        except Exception, e:
            self.logger.error('%s', e)
            pass
        if not osdial is None:
            osdial.close()
            osdial = None
        gc.collect()
        self.logger.debug("%s - Stopped.", threadname)
                                    
                                    


    def _noop_handler(self, agi, args, kwargs, match, path):
        """
        Does nothing, causing control to return to Asterisk's dialplan immediately; provided just
        to demonstrate the fallback handler.
        """

    def kill(self):
        self._fagi_server.shutdown()

    def run(self):
        self.logger.info("osdial_fastagi server started on %s %s ...", self.sa[0], self.sa[1])
        if opt['daemon']:
            self.daemonize()
        try:
            self._fagi_server.serve_forever(0.2)
        except KeyboardInterrupt, e:
            raise e

    def daemonize(self):
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

def main(argv):
    logger = None
    parser = argparse.ArgumentParser(description='osdial_fastagi - records call_log details at start and end of each call.')
    parser.add_argument('-v', '--verbose', action='count', default=0, help='Increases verbosity.', dest='verbose')
    parser.add_argument('--version', action='version', version='%(prog)s %(ver)s' % {'prog':PROGNAME,'ver':VERSION})
    parser.add_argument('--debug', action='store_true', help='Turns on debug mode.',dest='daemon')
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

    osdspt = None
    try:
        osdspt = OSDial()
        FORMAT = '%(asctime)s|%(filename)s:%(lineno)d|%(levelname)s|%(message)s'
        logger = logging.getLogger('fastagi')
        logstr2err={'CRITICAL':logging.CRITICAL,'ERROR':logging.ERROR,'WARNING':logging.WARNING,'INFO':logging.INFO,'DEBUG':logging.DEBUG}
        logdeflvl = logging.ERROR
        if opt['verbose']:
            logdeflvl = logging.INFO
        elif opt['debug']:
            logdeflvl = logging.DEBUG
        elif opt['loglevel']:
            if logstr2err.has_key(opt['loglevel']):
                logdeflvl = logstr2err[opt['loglevel']]
        formatter = logging.Formatter(FORMAT)

        handler = logging.FileHandler('%s/FASTagiout.%s' % (osdspt.PATHlogs, time.strftime('%Y-%m-%d', time.localtime())) )
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
        if sptres is True:
            logger.error("Error process already running!")
            sys.exit(1)
    except MySQLdb.OperationalError, e:
        logger.error("Could not connect to MySQL! %s", e)
        sys.exit(1)
    gc.collect()

    fastagi_core = FastAGIServer()
    fastagi_core.logger = logger
    fastagi_core.start()

    sleepcnt = 0
    sleepamt = .2
    while fastagi_core.is_alive():
        if (sleepcnt * sleepamt) >= 120:
            osdspt = None
            try:
                osdspt = OSDial()
                sptres = osdspt.server_process_tracker(PROGNAME, osdspt.VARserver_ip, os.getpid(), True)
                osdspt.close()
                osdspt = None
                if sptres is True:
                    logger.error("Error process already running!")
                    sys.exit(1)
            except MySQLdb.OperationalError, e:
                logger.error("Could not connect to MySQL! %s", e)
                pass
            gc.collect()
        time.sleep(sleepamt)
        sleepcnt += 1
    fastagi_core.kill()

if __name__ == '__main__':
    sys.path.insert(0, '%s/python' % os.path.dirname(os.path.realpath(__file__)))
    try:
        libname = re.sub('(^osdial_|\..*$)','',os.path.basename(__file__))
        thislib = __import__(libname)
        thislib.user_main(sys.argv[1:])
    except KeyboardInterrupt, e:
        print >> sys.stderr, "\n\nExiting on user cancel."
        sys.exit(1)
